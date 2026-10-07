<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Service_quotation_expiry_model extends CI_Model
{
    private $table = 'service_quotation_expiry';
    private $cancelled_stage_name = 'Cancelled Quotation';

    public function ensure_schema()
    {
        if (!$this->db->table_exists($this->table)) {
            $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `opportunity_id` INT NOT NULL,
                `quotation_id` INT NOT NULL,
                `cancelled_stage_id` INT NOT NULL,
                `previous_stage_id` INT NOT NULL,
                `quotation_date` DATE NOT NULL,
                `expired_on` DATETIME NOT NULL,
                `reopened_on` DATETIME DEFAULT NULL,
                `reopened_by` INT NOT NULL DEFAULT 0,
                `status` VARCHAR(20) NOT NULL DEFAULT 'Cancelled',
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_service_quote_expiry_opportunity` (`opportunity_id`),
                KEY `idx_service_quote_expiry_status` (`status`, `expired_on`),
                KEY `idx_service_quote_expiry_quotation` (`quotation_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

            if (!$this->db->query($sql)) {
                return false;
            }
        }

        return $this->get_cancelled_stage_id() > 0;
    }

    public function get_cancelled_stage_id()
    {
        $stage = $this->db->query(
            "SELECT stage_id FROM service_lead_stages
             WHERE LOWER(TRIM(stage_name)) = 'cancelled quotation'
             LIMIT 1"
        )->row();

        if (!empty($stage->stage_id)) {
            return (int) $stage->stage_id;
        }

        $max_sort = $this->db->select_max('sort_order', 'max_sort')
            ->get('service_lead_stages')
            ->row();

        $this->db->insert('service_lead_stages', array(
            'stage_name' => $this->cancelled_stage_name,
            'sort_order' => (int) ($max_sort->max_sort ?? 0) + 1,
            'status' => 1,
            'default_probability' => 0,
            'app_access' => 0
        ));

        return (int) $this->db->insert_id();
    }

    public function get_followup_excluded_stage_ids()
    {
        $query = $this->db->query(
            "SELECT stage_id FROM service_lead_stages
             WHERE LOWER(TRIM(stage_name)) IN (
                'cancelled quotation', 'po received', 'order won', 'create pi'
             )"
        );

        return array_map('intval', array_column($query->result_array(), 'stage_id'));
    }

    public static function expiry_cutoff_date($as_of_date)
    {
        $as_of = DateTime::createFromFormat('!Y-m-d', (string) $as_of_date);
        if (!$as_of || $as_of->format('Y-m-d') !== (string) $as_of_date) {
            return null;
        }

        $day = (int) $as_of->format('d');
        $previous_month = clone $as_of;
        $previous_month->modify('first day of this month');
        $previous_month->modify('-1 month');
        $previous_month->setDate(
            (int) $previous_month->format('Y'),
            (int) $previous_month->format('m'),
            min($day, (int) $previous_month->format('t'))
        );

        return $previous_month->format('Y-m-d');
    }

    public function expire_stale_quotations($as_of_date = null)
    {
        if (!$this->ensure_schema()) {
            return 0;
        }

        $as_of_date = $as_of_date ?: date('Y-m-d');
        $cutoff_date = self::expiry_cutoff_date($as_of_date);
        if ($cutoff_date === null) {
            return 0;
        }

        $cancelled_stage_id = $this->get_cancelled_stage_id();
        $excluded_stage_ids = $this->get_followup_excluded_stage_ids();
        if (!in_array($cancelled_stage_id, $excluded_stage_ids, true)) {
            $excluded_stage_ids[] = $cancelled_stage_id;
        }

        $excluded_sql = implode(',', array_map('intval', $excluded_stage_ids));
        if ($excluded_sql === '') {
            $excluded_sql = '0';
        }

        $candidates = $this->db->query(
            "SELECT so.opportunity_id, so.current_stage_id, sq.id AS quotation_id, sq.quotation_date
             FROM service_opportunities so
             INNER JOIN service_quotations sq
                ON sq.id = (
                    SELECT MAX(sq_latest.id)
                    FROM service_quotations sq_latest
                    WHERE sq_latest.opportunity_id = so.opportunity_id
                )
             WHERE sq.quotation_date IS NOT NULL
               AND sq.quotation_date != '0000-00-00'
               AND sq.quotation_date <= ?
               AND so.current_stage_id NOT IN ({$excluded_sql})
               AND NOT EXISTS (
                    SELECT 1
                    FROM {$this->table} reopened_expiry
                    WHERE reopened_expiry.opportunity_id = so.opportunity_id
                      AND reopened_expiry.quotation_id = sq.id
                      AND reopened_expiry.status = 'Reopened'
                      AND DATE(reopened_expiry.reopened_on) > ?
               )",
            array($cutoff_date, $cutoff_date)
        )->result();

        if (empty($candidates)) {
            return 0;
        }

        $expired_on = date('Y-m-d H:i:s');
        $expired_count = 0;
        $this->db->trans_begin();

        foreach ($candidates as $candidate) {
            $tracking_data = array(
                'opportunity_id' => (int) $candidate->opportunity_id,
                'quotation_id' => (int) $candidate->quotation_id,
                'cancelled_stage_id' => $cancelled_stage_id,
                'previous_stage_id' => (int) $candidate->current_stage_id,
                'quotation_date' => (string) $candidate->quotation_date,
                'expired_on' => $expired_on,
                'reopened_on' => null,
                'reopened_by' => 0,
                'status' => 'Cancelled'
            );

            $existing = $this->db->select('id')
                ->from($this->table)
                ->where('opportunity_id', (int) $candidate->opportunity_id)
                ->limit(1)
                ->get()
                ->row();

            if (!empty($existing->id)) {
                $this->db->where('id', (int) $existing->id)->update($this->table, $tracking_data);
            } else {
                $this->db->insert($this->table, $tracking_data);
            }

            $this->db->where('opportunity_id', (int) $candidate->opportunity_id)
                ->update('service_opportunities', array(
                    'current_stage_id' => $cancelled_stage_id,
                    'probability' => 0,
                    'updated_by' => 0,
                    'updated_date' => $expired_on
                ));

            $this->db->insert('service_progress_history', array(
                'opportunity_id' => (int) $candidate->opportunity_id,
                'stage_id' => $cancelled_stage_id,
                'remarks' => 'Automatically cancelled because the latest quotation is at least one month old.',
                'next_follow_date' => null,
                'added_by' => 0,
                'added_on' => $expired_on
            ));

            $expired_count++;
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return 0;
        }

        $this->db->trans_commit();
        return $expired_count;
    }

    public function get_active_cancellation($opportunity_id)
    {
        if (!$this->ensure_schema()) {
            return null;
        }

        return $this->db->select('expiry.*, previous_stage.stage_name AS previous_stage_name')
            ->from($this->table . ' expiry')
            ->join('service_lead_stages previous_stage', 'previous_stage.stage_id = expiry.previous_stage_id', 'left')
            ->where('expiry.opportunity_id', (int) $opportunity_id)
            ->where('expiry.status', 'Cancelled')
            ->limit(1)
            ->get()
            ->row();
    }

    public function reopen($opportunity_id, $user_id)
    {
        $cancellation = $this->get_active_cancellation($opportunity_id);
        if (empty($cancellation)) {
            return array('success' => false, 'message' => 'This quotation is not awaiting reopening.');
        }

        $opportunity = $this->db->select('current_stage_id')
            ->from('service_opportunities')
            ->where('opportunity_id', (int) $opportunity_id)
            ->limit(1)
            ->get()
            ->row();

        if (empty($opportunity) || (int) $opportunity->current_stage_id !== (int) $cancellation->cancelled_stage_id) {
            return array('success' => false, 'message' => 'This opportunity has already moved out of Cancelled Quotation.');
        }

        $previous_stage = $this->db->select('stage_id, stage_name, default_probability')
            ->from('service_lead_stages')
            ->where('stage_id', (int) $cancellation->previous_stage_id)
            ->where('status', 1)
            ->limit(1)
            ->get()
            ->row();

        if (empty($previous_stage)) {
            return array('success' => false, 'message' => 'The previous quotation stage is no longer available.');
        }

        $now = date('Y-m-d H:i:s');
        $this->db->trans_begin();

        $this->db->where('opportunity_id', (int) $opportunity_id)
            ->update('service_opportunities', array(
                'current_stage_id' => (int) $previous_stage->stage_id,
                'probability' => (int) $previous_stage->default_probability,
                'status' => 'Open',
                'updated_by' => (int) $user_id,
                'updated_date' => $now
            ));

        $this->db->where('id', (int) $cancellation->id)
            ->update($this->table, array(
                'status' => 'Reopened',
                'reopened_on' => $now,
                'reopened_by' => (int) $user_id
            ));

        $this->db->insert('service_progress_history', array(
            'opportunity_id' => (int) $opportunity_id,
            'stage_id' => (int) $previous_stage->stage_id,
            'remarks' => 'Quotation reopened after receiving a customer response. Restored to ' . $previous_stage->stage_name . '.',
            'next_follow_date' => date('Y-m-d'),
            'added_by' => (int) $user_id,
            'added_on' => $now
        ));

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return array('success' => false, 'message' => 'The quotation could not be reopened.');
        }

        $this->db->trans_commit();
        return array(
            'success' => true,
            'message' => 'Quotation reopened at ' . $previous_stage->stage_name . '.',
            'stage_id' => (int) $previous_stage->stage_id
        );
    }
}
