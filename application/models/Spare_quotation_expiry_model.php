<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Spare_quotation_expiry_model extends CI_Model
{
    private $table = 'spare_quotation_expiry';
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
                `previous_status` VARCHAR(30) NOT NULL DEFAULT 'Open',
                `previous_probability` INT NOT NULL DEFAULT 0,
                `quotation_date` DATE NOT NULL,
                `expired_on` DATETIME NOT NULL,
                `reopened_on` DATETIME DEFAULT NULL,
                `reopened_by` INT NOT NULL DEFAULT 0,
                `status` VARCHAR(20) NOT NULL DEFAULT 'Cancelled',
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_spare_quote_expiry_opportunity` (`opportunity_id`),
                KEY `idx_spare_quote_expiry_status` (`status`, `expired_on`),
                KEY `idx_spare_quote_expiry_quotation` (`quotation_id`)
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
            "SELECT lead_id FROM spare_lead_stage
             WHERE LOWER(TRIM(lead_name)) = 'cancelled quotation'
             LIMIT 1"
        )->row();

        if (!empty($stage->lead_id)) {
            return (int) $stage->lead_id;
        }

        $max_sort = $this->db->select_max('sort_order', 'max_sort')
            ->get('spare_lead_stage')
            ->row();

        $now = date('Y-m-d H:i:s');
        $this->db->insert('spare_lead_stage', array(
            'lead_name' => $this->cancelled_stage_name,
            'sort_order' => (int) ($max_sort->max_sort ?? 0) + 1,
            'user_role' => 1,
            'quotation_step' => 0,
            'quote_visible' => 0,
            'quotation_revised_step' => 0,
            'pi_step' => 0,
            'pi_revised_step' => 0,
            'conversion_step' => 0,
            'comparison_report' => 0,
            'reason' => 0,
            'followup_date' => 0,
            'dead_end' => 1,
            'sample' => 0,
            'trail' => 0,
            'trial_successful' => 0,
            'icon' => 'fa-ban',
            'status' => 1,
            'added_on' => $now,
            'added_by' => 0,
            'funnel' => 1,
            'mis' => 0,
            'tat_type' => 0,
            'day_time' => 0,
            'day_text' => 0,
            'time_text' => 0,
            'company_id' => 0,
            'show_in_funnel' => 1,
            'visit_step' => 0,
            'demo_scheduled' => 0,
            'step_type' => 0,
            'customization_related' => 0,
            'quotation_related_steps' => 0,
            'show_in_app' => 1,
            'app_access' => 1
        ));

        return (int) $this->db->insert_id();
    }

    public function get_followup_excluded_stage_ids()
    {
        $query = $this->db->query(
            "SELECT lead_id FROM spare_lead_stage
             WHERE LOWER(TRIM(lead_name)) IN (
                'cancelled quotation', 'order won', 'lead lost',
                'pending for po', 'po created', 'create pi'
             )"
        );

        return array_map('intval', array_column($query->result_array(), 'lead_id'));
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

        $advanced_business_sql = '';
        if ($this->db->table_exists('spare_proforma_invoices')) {
            $advanced_business_sql .= " AND NOT EXISTS (
                SELECT 1 FROM spare_proforma_invoices spi
                WHERE spi.opportunity_id = op.opportunity_id
            )";
        }
        if ($this->db->table_exists('purchase_orders')) {
            $advanced_business_sql .= " AND NOT EXISTS (
                SELECT 1 FROM purchase_orders po
                WHERE po.opportunity_id = op.opportunity_id
            )";
        }
        if ($this->db->table_exists('spares_orders')) {
            $advanced_business_sql .= " AND NOT EXISTS (
                SELECT 1 FROM spares_orders orders_won
                WHERE orders_won.opportunity_id = op.opportunity_id
            )";
        }

        $candidates = $this->db->query(
            "SELECT op.opportunity_id, op.status AS opportunity_status,
                    op.probability, current_progress.lead_stage AS current_stage_id,
                    quote.quotation_id, quote.quotation_date
             FROM opportunities op
             INNER JOIN quotations quote
                ON quote.quotation_id = (
                    SELECT MAX(latest_quote.quotation_id)
                    FROM quotations latest_quote
                    WHERE latest_quote.opportunity_id = op.opportunity_id
                )
             INNER JOIN spare_progress_remarks current_progress
                ON current_progress.id = (
                    SELECT MAX(latest_progress.id)
                    FROM spare_progress_remarks latest_progress
                    WHERE latest_progress.lead_id = op.opportunity_id
                )
             WHERE quote.quotation_date IS NOT NULL
               AND quote.quotation_date != '0000-00-00'
               AND quote.quotation_date <= ?
               AND current_progress.lead_stage NOT IN ({$excluded_sql})
               {$advanced_business_sql}
               AND NOT EXISTS (
                    SELECT 1
                    FROM {$this->table} reopened_expiry
                    WHERE reopened_expiry.opportunity_id = op.opportunity_id
                      AND reopened_expiry.quotation_id = quote.quotation_id
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
                'previous_status' => (string) $candidate->opportunity_status,
                'previous_probability' => (int) $candidate->probability,
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
                ->update('opportunities', array(
                    'status' => 'Cancelled',
                    'probability' => 0
                ));

            $this->db->insert('spare_progress_remarks', array(
                'lead_id' => (int) $candidate->opportunity_id,
                'lead_stage' => $cancelled_stage_id,
                'next_follow_date' => '0000-00-00',
                'remarks' => 'Automatically cancelled because the latest quotation is at least one month old.',
                'remark_title' => $this->cancelled_stage_name,
                'added_on' => $expired_on,
                'added_by' => 0
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

        return $this->db->select('expiry.*, previous_stage.lead_name AS previous_stage_name')
            ->from($this->table . ' expiry')
            ->join('spare_lead_stage previous_stage', 'previous_stage.lead_id = expiry.previous_stage_id', 'left')
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

        $current_progress = $this->db->select('lead_stage')
            ->from('spare_progress_remarks')
            ->where('lead_id', (int) $opportunity_id)
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get()
            ->row();

        if (empty($current_progress) || (int) $current_progress->lead_stage !== (int) $cancellation->cancelled_stage_id) {
            return array('success' => false, 'message' => 'This opportunity has already moved out of Cancelled Quotation.');
        }

        $previous_stage = $this->db->select('lead_id, lead_name')
            ->from('spare_lead_stage')
            ->where('lead_id', (int) $cancellation->previous_stage_id)
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
            ->update('opportunities', array(
                'status' => $cancellation->previous_status,
                'probability' => (int) $cancellation->previous_probability
            ));

        $this->db->where('id', (int) $cancellation->id)
            ->update($this->table, array(
                'status' => 'Reopened',
                'reopened_on' => $now,
                'reopened_by' => (int) $user_id
            ));

        $this->db->insert('spare_progress_remarks', array(
            'lead_id' => (int) $opportunity_id,
            'lead_stage' => (int) $previous_stage->lead_id,
            'next_follow_date' => date('Y-m-d'),
            'remarks' => 'Quotation reopened after receiving a customer response. Restored to ' . $previous_stage->lead_name . '.',
            'remark_title' => 'Quotation Reopened',
            'added_on' => $now,
            'added_by' => (int) $user_id
        ));

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return array('success' => false, 'message' => 'The quotation could not be reopened.');
        }

        $this->db->trans_commit();
        return array(
            'success' => true,
            'message' => 'Quotation reopened at ' . $previous_stage->lead_name . '.',
            'stage_id' => (int) $previous_stage->lead_id
        );
    }
}
