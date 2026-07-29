<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Service_visit_execution_model extends CI_Model
{
    private $visit_table = 'service_engineer_visits';
    private $update_table = 'service_visit_daily_updates';
    private $document_table = 'service_visit_completion_documents';
    private $version_table = 'service_visit_schedule_versions';

    public function ensure_tables()
    {
        if ($this->db->table_exists($this->visit_table)) {
            $this->ensure_visit_field(
                'updated_at',
                "ALTER TABLE `{$this->visit_table}` ADD `updated_at` DATETIME DEFAULT NULL AFTER `created_at`"
            );
            $this->ensure_visit_field(
                'completed_on',
                "ALTER TABLE `{$this->visit_table}` ADD `completed_on` DATETIME DEFAULT NULL AFTER `visit_status`"
            );
            $this->ensure_visit_field(
                'completion_notes',
                "ALTER TABLE `{$this->visit_table}` ADD `completion_notes` TEXT DEFAULT NULL AFTER `completed_on`"
            );
        }

        if (!$this->db->table_exists($this->update_table)) {
            $this->db->query(
                "CREATE TABLE IF NOT EXISTS `{$this->update_table}` (
                    `update_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `visit_id` INT(11) NOT NULL,
                    `work_date` DATE NOT NULL,
                    `mom_points` LONGTEXT NOT NULL,
                    `next_plan` TEXT DEFAULT NULL,
                    `added_by` INT(11) DEFAULT NULL,
                    `added_on` DATETIME DEFAULT NULL,
                    `updated_by` INT(11) DEFAULT NULL,
                    `updated_on` DATETIME DEFAULT NULL,
                    PRIMARY KEY (`update_id`),
                    UNIQUE KEY `uniq_visit_work_date` (`visit_id`, `work_date`),
                    KEY `idx_visit_daily_updates_visit` (`visit_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
            );
        }

        if (!$this->db->table_exists($this->document_table)) {
            $this->db->query(
                "CREATE TABLE IF NOT EXISTS `{$this->document_table}` (
                    `document_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `visit_id` INT(11) NOT NULL,
                    `document_type` VARCHAR(100) DEFAULT 'SIGNED_COMPLETION',
                    `file_name` VARCHAR(255) DEFAULT NULL,
                    `original_name` VARCHAR(255) DEFAULT NULL,
                    `file_path` VARCHAR(255) DEFAULT NULL,
                    `uploaded_by` INT(11) DEFAULT NULL,
                    `uploaded_at` DATETIME DEFAULT NULL,
                    PRIMARY KEY (`document_id`),
                    KEY `idx_visit_completion_documents_visit` (`visit_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
            );
        }

        if (!$this->db->table_exists($this->version_table)) {
            $this->db->query(
                "CREATE TABLE IF NOT EXISTS `{$this->version_table}` (
                    `version_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `visit_id` INT(11) NOT NULL,
                    `version_no` INT(11) NOT NULL DEFAULT 1,
                    `start_date` DATE NOT NULL,
                    `end_date` DATE NOT NULL,
                    `duration_days` INT(11) NOT NULL DEFAULT 1,
                    `change_type` VARCHAR(100) DEFAULT 'Initial Schedule',
                    `change_reason` TEXT DEFAULT NULL,
                    `created_by` INT(11) DEFAULT NULL,
                    `created_at` DATETIME DEFAULT NULL,
                    PRIMARY KEY (`version_id`),
                    UNIQUE KEY `uniq_service_visit_version` (`visit_id`, `version_no`),
                    KEY `idx_service_visit_versions_visit` (`visit_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
            );
        }
    }

    public function get_visit_rows($filters = [])
    {
        $this->ensure_tables();

        $visit_status_select = $this->db->field_exists('visit_status', $this->visit_table)
            ? "COALESCE(NULLIF(v.visit_status, ''), 'Scheduled')"
            : "'Scheduled'";

        $this->db->select("
            v.visit_id,
            v.opportunity_id,
            v.engineer_id,
            v.start_date,
            v.end_date,
            v.visit_type,
            v.remarks,
            v.completed_on,
            v.completion_notes,
            {$visit_status_select} as visit_status,
            so.op_no,
            so.op_date,
            TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) as engineer_full_name,
            IFNULL(cm_spares.company_name, cm_marketing.company_name) as customer_name,
            COALESCE(NULLIF(cm_spares.contact_person, ''), NULLIF(cm_marketing.customer_name, ''), '') as customer_contact_name,
            COALESCE(NULLIF(cm_spares.contact_person_no, ''), NULLIF(cm_marketing.contact_no, ''), '') as customer_contact_no,
            COALESCE(NULLIF(cm_spares.email, ''), NULLIF(cm_marketing.email, ''), '') as customer_email,
            COALESCE(NULLIF(cm_spares.address, ''), NULLIF(cm_marketing.address, ''), '') as customer_address,
            (
                SELECT MAX(du.work_date)
                FROM {$this->update_table} du
                WHERE du.visit_id = v.visit_id
            ) as latest_work_date,
            (
                SELECT COUNT(1)
                FROM {$this->update_table} du
                WHERE du.visit_id = v.visit_id
            ) as mom_count,
            (
                SELECT COUNT(1)
                FROM {$this->document_table} dd
                WHERE dd.visit_id = v.visit_id
            ) as document_count,
            (
                SELECT MAX(vv.version_no)
                FROM {$this->version_table} vv
                WHERE vv.visit_id = v.visit_id
            ) as schedule_version
        ");
        $this->db->from("{$this->visit_table} v");
        $this->db->join('system_users u', 'u.user_id = v.engineer_id', 'left');
        $this->db->join('service_opportunities so', 'so.opportunity_id = v.opportunity_id', 'left');
        $this->db->join('spares_customers cm_spares', 'cm_spares.customer_id = so.customer_id', 'left');
        $this->db->join('customer_detail cm_marketing', 'cm_marketing.id = so.customer_id', 'left');

        if (!empty($filters['visit_status'])) {
            $this->db->where($visit_status_select . ' = ' . $this->db->escape($filters['visit_status']), null, false);
        }

        if (!empty($filters['engineer_id'])) {
            $this->db->where('v.engineer_id', (int) $filters['engineer_id']);
        }

        if (!empty($filters['opportunity_id'])) {
            $this->db->where('v.opportunity_id', (int) $filters['opportunity_id']);
        }

        if (!empty($filters['visit_id'])) {
            $this->db->where('v.visit_id', (int) $filters['visit_id']);
        }

        $this->db->order_by('v.start_date', 'DESC');
        $this->db->order_by('v.visit_id', 'DESC');

        return $this->decorate_visits($this->db->get()->result());
    }

    public function get_visit_by_id($visit_id)
    {
        $rows = $this->get_visit_rows([
            'visit_id' => (int) $visit_id,
        ]);

        return !empty($rows) ? $rows[0] : null;
    }

    public function get_visit_updates($visit_id)
    {
        $this->ensure_tables();

        return $this->db->select("
                du.*,
                TRIM(CONCAT(COALESCE(au.first_name, ''), ' ', COALESCE(au.last_name, ''))) as added_by_name,
                TRIM(CONCAT(COALESCE(uu.first_name, ''), ' ', COALESCE(uu.last_name, ''))) as updated_by_name
            ")
            ->from("{$this->update_table} du")
            ->join('system_users au', 'au.user_id = du.added_by', 'left')
            ->join('system_users uu', 'uu.user_id = du.updated_by', 'left')
            ->where('du.visit_id', (int) $visit_id)
            ->order_by('du.work_date', 'DESC')
            ->order_by('du.update_id', 'DESC')
            ->get()
            ->result();
    }

    public function get_visit_documents($visit_id)
    {
        $this->ensure_tables();

        return $this->db->select("
                d.*,
                TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) as uploaded_by_name
            ")
            ->from("{$this->document_table} d")
            ->join('system_users u', 'u.user_id = d.uploaded_by', 'left')
            ->where('d.visit_id', (int) $visit_id)
            ->order_by('d.document_id', 'DESC')
            ->get()
            ->result();
    }

    public function get_visit_update_count($visit_id)
    {
        $this->ensure_tables();

        return (int) $this->db->where('visit_id', (int) $visit_id)->count_all_results($this->update_table);
    }

    public function get_visit_document_count($visit_id)
    {
        $this->ensure_tables();

        return (int) $this->db->where('visit_id', (int) $visit_id)->count_all_results($this->document_table);
    }

    public function save_daily_update($visit_id, $payload)
    {
        $this->ensure_tables();

        $visit_id = (int) $visit_id;
        $work_date = $payload['work_date'];
        $user_id = !empty($payload['user_id']) ? (int) $payload['user_id'] : null;
        $now = date('Y-m-d H:i:s');

        $existing = $this->db->get_where($this->update_table, [
            'visit_id' => $visit_id,
            'work_date' => $work_date,
        ])->row();

        $data = [
            'mom_points' => $payload['mom_points'],
            'next_plan' => $payload['next_plan'],
            'updated_by' => $user_id,
            'updated_on' => $now,
        ];

        if ($existing) {
            $this->db->where('update_id', (int) $existing->update_id)->update($this->update_table, $data);
        } else {
            $data['visit_id'] = $visit_id;
            $data['work_date'] = $work_date;
            $data['added_by'] = $user_id;
            $data['added_on'] = $now;
            $this->db->insert($this->update_table, $data);
        }

        $visit = $this->db->select('visit_status')
            ->get_where($this->visit_table, ['visit_id' => $visit_id])
            ->row();

        $visit_update = [];
        if ($this->db->field_exists('updated_at', $this->visit_table)) {
            $visit_update['updated_at'] = $now;
        }

        if ($this->db->field_exists('visit_status', $this->visit_table)) {
            $current_status = strtolower(trim((string) ($visit->visit_status ?? '')));
            if (!in_array($current_status, ['completed', 'cancelled'], true)) {
                $visit_update['visit_status'] = 'On-Site';
            }
        }

        if (!empty($visit_update)) {
            $this->db->where('visit_id', $visit_id)->update($this->visit_table, $visit_update);
        }

        return true;
    }

    public function save_completion_document($visit_id, $document, $user_id = null)
    {
        $this->ensure_tables();

        return $this->db->insert($this->document_table, [
            'visit_id' => (int) $visit_id,
            'document_type' => !empty($document['document_type']) ? $document['document_type'] : 'SIGNED_COMPLETION',
            'file_name' => $document['file_name'],
            'original_name' => $document['original_name'],
            'file_path' => $document['file_path'],
            'uploaded_by' => !empty($user_id) ? (int) $user_id : null,
            'uploaded_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function mark_visit_completed($visit_id, $completion_notes = '')
    {
        $this->ensure_tables();

        $update_data = [];
        if ($this->db->field_exists('visit_status', $this->visit_table)) {
            $update_data['visit_status'] = 'Completed';
        }
        if ($this->db->field_exists('completed_on', $this->visit_table)) {
            $update_data['completed_on'] = date('Y-m-d H:i:s');
        }
        if ($this->db->field_exists('completion_notes', $this->visit_table)) {
            $update_data['completion_notes'] = $completion_notes;
        }
        if ($this->db->field_exists('updated_at', $this->visit_table)) {
            $update_data['updated_at'] = date('Y-m-d H:i:s');
        }

        return $this->db->where('visit_id', (int) $visit_id)->update($this->visit_table, $update_data);
    }

    public function create_initial_schedule_version($visit_id, $user_id = null, $reason = 'Visit scheduled', $created_at = null)
    {
        $this->ensure_tables();

        $visit_id = (int) $visit_id;
        if ($visit_id <= 0) {
            return false;
        }

        $existing_count = (int) $this->db->where('visit_id', $visit_id)->count_all_results($this->version_table);
        if ($existing_count > 0) {
            return true;
        }

        $visit_row = $this->db->select('visit_id, start_date, end_date, created_at')
            ->from($this->visit_table)
            ->where('visit_id', $visit_id)
            ->get()
            ->row();

        if (empty($visit_row) || empty($visit_row->start_date) || empty($visit_row->end_date)) {
            return false;
        }

        return $this->db->insert($this->version_table, [
            'visit_id' => $visit_id,
            'version_no' => 1,
            'start_date' => $visit_row->start_date,
            'end_date' => $visit_row->end_date,
            'duration_days' => $this->calculate_duration_days($visit_row->start_date, $visit_row->end_date),
            'change_type' => 'Initial Schedule',
            'change_reason' => $reason,
            'created_by' => !empty($user_id) ? (int) $user_id : null,
            'created_at' => $this->normalize_version_timestamp($created_at, $visit_row->created_at, $visit_row->start_date),
        ]);
    }

    public function get_visit_versions($visit_id, $visit_snapshot = null)
    {
        $this->ensure_tables();

        $rows = $this->db->select("
                vv.*,
                TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) as created_by_name
            ")
            ->from("{$this->version_table} vv")
            ->join('system_users u', 'u.user_id = vv.created_by', 'left')
            ->where('vv.visit_id', (int) $visit_id)
            ->order_by('vv.version_no', 'ASC')
            ->get()
            ->result();

        if (empty($rows)) {
            if ($visit_snapshot === null) {
                $visit_snapshot = $this->get_visit_by_id((int) $visit_id);
            }

            if ($visit_snapshot === null) {
                return [];
            }

            return [$this->build_virtual_version_row($visit_snapshot)];
        }

        $previous_row = null;
        foreach ($rows as $row) {
            $row->duration_days = $this->calculate_duration_days($row->start_date, $row->end_date);
            $row->duration_label = $row->duration_days === 1 ? '1 day' : $row->duration_days . ' days';
            $row->created_by_name = trim((string) $row->created_by_name);
            $row->previous_start_date = $previous_row ? $previous_row->start_date : null;
            $row->previous_end_date = $previous_row ? $previous_row->end_date : null;
            $row->change_summary = $this->build_version_change_summary($row, $previous_row);
            $previous_row = $row;
        }

        return array_reverse($rows);
    }

    public function extend_visit_schedule($visit_id, $new_end_date, $reason, $user_id = null)
    {
        $this->ensure_tables();

        $visit_id = (int) $visit_id;
        $visit_row = $this->db->select('visit_id, start_date, end_date, created_at')
            ->from($this->visit_table)
            ->where('visit_id', $visit_id)
            ->get()
            ->row();

        if (empty($visit_row)) {
            return false;
        }

        $new_end_date = trim((string) $new_end_date);
        $reason = trim((string) $reason);
        $timestamp = date('Y-m-d H:i:s');

        if (!$this->create_initial_schedule_version($visit_id, $user_id, 'Visit scheduled', $visit_row->created_at)) {
            return false;
        }

        $latest_version = $this->db->select('MAX(version_no) as max_version')
            ->from($this->version_table)
            ->where('visit_id', $visit_id)
            ->get()
            ->row();

        $next_version = max(1, (int) ($latest_version->max_version ?? 0)) + 1;

        $this->db->trans_start();

        $update_data = [
            'end_date' => $new_end_date,
        ];
        if ($this->db->field_exists('updated_at', $this->visit_table)) {
            $update_data['updated_at'] = $timestamp;
        }

        $this->db->where('visit_id', $visit_id)->update($this->visit_table, $update_data);
        $this->db->insert($this->version_table, [
            'visit_id' => $visit_id,
            'version_no' => $next_version,
            'start_date' => $visit_row->start_date,
            'end_date' => $new_end_date,
            'duration_days' => $this->calculate_duration_days($visit_row->start_date, $new_end_date),
            'change_type' => 'Extension',
            'change_reason' => $reason,
            'created_by' => !empty($user_id) ? (int) $user_id : null,
            'created_at' => $timestamp,
        ]);

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    private function ensure_visit_field($field_name, $query)
    {
        if (!$this->db->field_exists($field_name, $this->visit_table)) {
            $this->db->query($query);
        }
    }

    private function decorate_visits($visits)
    {
        $today = date('Y-m-d');

        foreach ($visits as $visit) {
            $visit->engineer_full_name = trim((string) $visit->engineer_full_name);
            if ($visit->engineer_full_name === '') {
                $visit->engineer_full_name = 'Unknown Engineer';
            }

            $visit->customer_name = trim((string) $visit->customer_name);
            if ($visit->customer_name === '') {
                $visit->customer_name = 'Customer not available';
            }

            $duration_days = 1;
            if (!empty($visit->start_date) && !empty($visit->end_date)) {
                $duration_days = (int) floor((strtotime($visit->end_date) - strtotime($visit->start_date)) / 86400) + 1;
            }

            $visit->duration_days = max(1, $duration_days);
            $visit->duration_label = $visit->duration_days === 1 ? '1 day' : $visit->duration_days . ' days';
            $visit->status_slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', (string) $visit->visit_status), '-'));
            $visit->type_slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', (string) $visit->visit_type), '-'));
            $visit->is_active_window = !empty($visit->end_date) && $visit->end_date >= $today;
            $visit->is_today = !empty($visit->start_date) && !empty($visit->end_date) && $today >= $visit->start_date && $today <= $visit->end_date;
            $visit->mom_count = (int) ($visit->mom_count ?? 0);
            $visit->document_count = (int) ($visit->document_count ?? 0);
            $visit->schedule_version = max(1, (int) ($visit->schedule_version ?? 0));
            $visit->extension_count = max(0, $visit->schedule_version - 1);
        }

        return $visits;
    }

    private function calculate_duration_days($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 1;
        }

        return max(1, (int) floor((strtotime($end_date) - strtotime($start_date)) / 86400) + 1);
    }

    private function normalize_version_timestamp($preferred_timestamp = null, $fallback_timestamp = null, $fallback_date = null)
    {
        $preferred_timestamp = trim((string) $preferred_timestamp);
        if ($preferred_timestamp !== '') {
            return $preferred_timestamp;
        }

        $fallback_timestamp = trim((string) $fallback_timestamp);
        if ($fallback_timestamp !== '') {
            return $fallback_timestamp;
        }

        $fallback_date = trim((string) $fallback_date);
        if ($fallback_date !== '') {
            return $fallback_date . ' 00:00:00';
        }

        return date('Y-m-d H:i:s');
    }

    private function build_virtual_version_row($visit_snapshot)
    {
        $row = (object) [
            'version_id' => 0,
            'visit_id' => (int) $visit_snapshot->visit_id,
            'version_no' => 1,
            'start_date' => (string) $visit_snapshot->start_date,
            'end_date' => (string) $visit_snapshot->end_date,
            'duration_days' => $this->calculate_duration_days($visit_snapshot->start_date, $visit_snapshot->end_date),
            'change_type' => 'Initial Schedule',
            'change_reason' => 'Visit scheduled',
            'created_by' => null,
            'created_at' => $this->normalize_version_timestamp(null, null, $visit_snapshot->start_date),
            'created_by_name' => '',
            'previous_start_date' => null,
            'previous_end_date' => null,
            'change_summary' => 'Initial schedule created',
        ];
        $row->duration_label = $row->duration_days === 1 ? '1 day' : $row->duration_days . ' days';

        return $row;
    }

    private function build_version_change_summary($row, $previous_row = null)
    {
        $change_type = strtolower(trim((string) ($row->change_type ?? '')));
        if ($previous_row === null) {
            return 'Initial schedule created';
        }

        if ($change_type === 'extension') {
            $extended_days = $this->calculate_duration_days($previous_row->end_date, $row->end_date) - 1;
            if ($extended_days > 0) {
                return $extended_days === 1 ? 'Extended by 1 day' : 'Extended by ' . $extended_days . ' days';
            }
        }

        if ($row->start_date !== $previous_row->start_date || $row->end_date !== $previous_row->end_date) {
            return 'Schedule window revised';
        }

        return 'Schedule version updated';
    }
}
