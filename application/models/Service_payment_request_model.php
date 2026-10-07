<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Service_payment_request_model extends CI_Model
{
    private $table = 'service_payment_requests';
    private $log_table = 'service_payment_request_logs';

    public function ensure_tables()
    {
        if (!$this->db->table_exists($this->table)) {
            $this->db->query(
                "CREATE TABLE IF NOT EXISTS `{$this->table}` (
                    `request_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `request_code` VARCHAR(60) NOT NULL,
                    `opportunity_id` INT(11) NOT NULL,
                    `visit_id` INT(11) DEFAULT NULL,
                    `parent_request_id` INT(11) DEFAULT NULL,
                    `request_basis` VARCHAR(30) NOT NULL DEFAULT 'ORDER',
                    `request_type` VARCHAR(80) NOT NULL DEFAULT 'FOC',
                    `request_title` VARCHAR(255) DEFAULT NULL,
                    `op_no` VARCHAR(60) DEFAULT NULL,
                    `customer_id` INT(11) DEFAULT NULL,
                    `customer_name` VARCHAR(255) DEFAULT NULL,
                    `po_number` VARCHAR(120) DEFAULT NULL,
                    `po_date` DATE DEFAULT NULL,
                    `po_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                    `engineer_id` INT(11) DEFAULT NULL,
                    `requested_for_user_id` INT(11) DEFAULT NULL,
                    `service_from_date` DATE DEFAULT NULL,
                    `service_to_date` DATE DEFAULT NULL,
                    `request_date` DATE NOT NULL,
                    `amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                    `approved_amount` DECIMAL(15,2) DEFAULT NULL,
                    `purpose` LONGTEXT NOT NULL,
                    `extension_reason` TEXT DEFAULT NULL,
                    `attachment` VARCHAR(255) DEFAULT NULL,
                    `status` VARCHAR(40) NOT NULL DEFAULT 'Pending HOD Approval',
                    `hod_id` INT(11) DEFAULT NULL,
                    `hod_remarks` LONGTEXT DEFAULT NULL,
                    `hod_action_on` DATETIME DEFAULT NULL,
                    `created_by` INT(11) NOT NULL,
                    `created_at` DATETIME NOT NULL,
                    `updated_by` INT(11) DEFAULT NULL,
                    `updated_at` DATETIME DEFAULT NULL,
                    PRIMARY KEY (`request_id`),
                    UNIQUE KEY `uniq_service_payment_request_code` (`request_code`),
                    KEY `idx_service_payment_request_opportunity` (`opportunity_id`),
                    KEY `idx_service_payment_request_visit` (`visit_id`),
                    KEY `idx_service_payment_request_status` (`status`),
                    KEY `idx_service_payment_request_created_by` (`created_by`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
            );
        } else {
            $this->ensure_field(
                'parent_request_id',
                "ALTER TABLE `{$this->table}` ADD `parent_request_id` INT(11) DEFAULT NULL AFTER `visit_id`"
            );
            $this->ensure_field(
                'request_basis',
                "ALTER TABLE `{$this->table}` ADD `request_basis` VARCHAR(30) NOT NULL DEFAULT 'ORDER' AFTER `parent_request_id`"
            );
            $this->ensure_field(
                'request_type',
                "ALTER TABLE `{$this->table}` ADD `request_type` VARCHAR(80) NOT NULL DEFAULT 'FOC' AFTER `request_basis`"
            );
            $this->ensure_field(
                'request_title',
                "ALTER TABLE `{$this->table}` ADD `request_title` VARCHAR(255) DEFAULT NULL AFTER `request_type`"
            );
            $this->ensure_field(
                'op_no',
                "ALTER TABLE `{$this->table}` ADD `op_no` VARCHAR(60) DEFAULT NULL AFTER `request_title`"
            );
            $this->ensure_field(
                'customer_id',
                "ALTER TABLE `{$this->table}` ADD `customer_id` INT(11) DEFAULT NULL AFTER `op_no`"
            );
            $this->ensure_field(
                'customer_name',
                "ALTER TABLE `{$this->table}` ADD `customer_name` VARCHAR(255) DEFAULT NULL AFTER `customer_id`"
            );
            $this->ensure_field(
                'po_number',
                "ALTER TABLE `{$this->table}` ADD `po_number` VARCHAR(120) DEFAULT NULL AFTER `customer_name`"
            );
            $this->ensure_field(
                'po_date',
                "ALTER TABLE `{$this->table}` ADD `po_date` DATE DEFAULT NULL AFTER `po_number`"
            );
            $this->ensure_field(
                'po_amount',
                "ALTER TABLE `{$this->table}` ADD `po_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER `po_date`"
            );
            $this->ensure_field(
                'engineer_id',
                "ALTER TABLE `{$this->table}` ADD `engineer_id` INT(11) DEFAULT NULL AFTER `po_amount`"
            );
            $this->ensure_field(
                'requested_for_user_id',
                "ALTER TABLE `{$this->table}` ADD `requested_for_user_id` INT(11) DEFAULT NULL AFTER `engineer_id`"
            );
            $this->ensure_field(
                'service_from_date',
                "ALTER TABLE `{$this->table}` ADD `service_from_date` DATE DEFAULT NULL AFTER `requested_for_user_id`"
            );
            $this->ensure_field(
                'service_to_date',
                "ALTER TABLE `{$this->table}` ADD `service_to_date` DATE DEFAULT NULL AFTER `service_from_date`"
            );
            $this->ensure_field(
                'request_date',
                "ALTER TABLE `{$this->table}` ADD `request_date` DATE NOT NULL DEFAULT CURRENT_DATE AFTER `service_to_date`"
            );
            // What the HOD actually sanctioned. NULL until the HOD acts, and NULL
            // means "not revised" rather than zero - `amount` always keeps the
            // figure the engineer asked for.
            $this->ensure_field(
                'approved_amount',
                "ALTER TABLE `{$this->table}` ADD `approved_amount` DECIMAL(15,2) DEFAULT NULL AFTER `amount`"
            );
            $this->ensure_field(
                'purpose',
                "ALTER TABLE `{$this->table}` ADD `purpose` LONGTEXT DEFAULT NULL AFTER `amount`"
            );
            $this->ensure_field(
                'extension_reason',
                "ALTER TABLE `{$this->table}` ADD `extension_reason` TEXT DEFAULT NULL AFTER `purpose`"
            );
            $this->ensure_field(
                'attachment',
                "ALTER TABLE `{$this->table}` ADD `attachment` VARCHAR(255) DEFAULT NULL AFTER `extension_reason`"
            );
            $this->ensure_field(
                'hod_id',
                "ALTER TABLE `{$this->table}` ADD `hod_id` INT(11) DEFAULT NULL AFTER `status`"
            );
            $this->ensure_field(
                'hod_remarks',
                "ALTER TABLE `{$this->table}` ADD `hod_remarks` LONGTEXT DEFAULT NULL AFTER `hod_id`"
            );
            $this->ensure_field(
                'hod_action_on',
                "ALTER TABLE `{$this->table}` ADD `hod_action_on` DATETIME DEFAULT NULL AFTER `hod_remarks`"
            );
            $this->ensure_field(
                'updated_by',
                "ALTER TABLE `{$this->table}` ADD `updated_by` INT(11) DEFAULT NULL AFTER `created_at`"
            );
            $this->ensure_field(
                'updated_at',
                "ALTER TABLE `{$this->table}` ADD `updated_at` DATETIME DEFAULT NULL AFTER `updated_by`"
            );
        }

        if (!$this->db->table_exists($this->log_table)) {
            $this->db->query(
                "CREATE TABLE IF NOT EXISTS `{$this->log_table}` (
                    `log_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `request_id` INT(11) NOT NULL,
                    `action_type` VARCHAR(60) NOT NULL,
                    `status_label` VARCHAR(60) DEFAULT NULL,
                    `remarks` LONGTEXT DEFAULT NULL,
                    `actor_id` INT(11) DEFAULT NULL,
                    `meta_json` LONGTEXT DEFAULT NULL,
                    `created_at` DATETIME NOT NULL,
                    PRIMARY KEY (`log_id`),
                    KEY `idx_service_payment_request_logs_request` (`request_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
            );
        }
    }

    public function get_next_request_code()
    {
        $this->ensure_tables();

        $row = $this->db->select_max('request_id', 'max_id')->get($this->table)->row();
        $next_id = (int) ($row->max_id ?? 0) + 1;

        $month = (int) date('m');
        $year = (int) date('y');
        $financial_year = $month >= 4
            ? sprintf('%02d-%02d', $year, $year + 1)
            : sprintf('%02d-%02d', $year - 1, $year);

        return 'SPR/' . str_pad($next_id, 4, '0', STR_PAD_LEFT) . '/' . $financial_year;
    }

    public function create_request($data)
    {
        $this->ensure_tables();

        $this->db->insert($this->table, $data);
        $request_id = (int) $this->db->insert_id();

        if ($request_id > 0) {
            $this->log_action(
                $request_id,
                'REQUEST_CREATED',
                'Payment request created.',
                !empty($data['created_by']) ? (int) $data['created_by'] : null,
                [
                    'amount' => isset($data['amount']) ? (float) $data['amount'] : 0,
                    'request_type' => $data['request_type'] ?? '',
                    'request_basis' => $data['request_basis'] ?? '',
                ],
                $data['status'] ?? 'Pending HOD Approval'
            );
        }

        return $request_id;
    }

    public function update_request($request_id, $data)
    {
        $this->ensure_tables();

        return $this->db->where('request_id', (int) $request_id)->update($this->table, $data);
    }

    public function log_action($request_id, $action_type, $remarks = '', $actor_id = null, $meta = [], $status_label = null)
    {
        $this->ensure_tables();

        return $this->db->insert($this->log_table, [
            'request_id' => (int) $request_id,
            'action_type' => (string) $action_type,
            'status_label' => $status_label,
            'remarks' => $remarks,
            'actor_id' => $actor_id !== null ? (int) $actor_id : null,
            'meta_json' => !empty($meta) ? json_encode($meta) : null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function get_request_by_id($request_id)
    {
        $rows = $this->get_request_rows([
            'request_id' => (int) $request_id,
            'limit' => 1,
        ]);

        return !empty($rows) ? $rows[0] : null;
    }

    public function get_request_rows($filters = [])
    {
        $this->ensure_tables();

        $this->db->select("
            r.*,
            parent.request_code as parent_request_code,
            parent.amount as parent_request_amount,
            TRIM(CONCAT(COALESCE(cb.first_name, ''), ' ', COALESCE(cb.last_name, ''))) as created_by_name,
            TRIM(CONCAT(COALESCE(eng.first_name, ''), ' ', COALESCE(eng.last_name, ''))) as engineer_name,
            TRIM(CONCAT(COALESCE(reqfor.first_name, ''), ' ', COALESCE(reqfor.last_name, ''))) as requested_for_name,
            TRIM(CONCAT(COALESCE(hod.first_name, ''), ' ', COALESCE(hod.last_name, ''))) as hod_name,
            COALESCE(
                NULLIF(sc.contact_person, ''),
                NULLIF(cd.customer_name, ''),
                NULLIF(r.customer_name, ''),
                NULLIF(sc.company_name, ''),
                NULLIF(cd.company_name, ''),
                ''
            ) as live_customer_name,
            COALESCE(
                NULLIF(sc.company_name, ''),
                NULLIF(cd.company_name, ''),
                ''
            ) as live_company_name
        ");
        $this->db->from("{$this->table} r");
        $this->db->join("{$this->table} parent", 'parent.request_id = r.parent_request_id', 'left');
        $this->db->join('system_users cb', 'cb.user_id = r.created_by', 'left');
        $this->db->join('system_users eng', 'eng.user_id = r.engineer_id', 'left');
        $this->db->join('system_users reqfor', 'reqfor.user_id = r.requested_for_user_id', 'left');
        $this->db->join('system_users hod', 'hod.user_id = r.hod_id', 'left');
        $this->db->join('service_opportunities so', 'so.opportunity_id = r.opportunity_id', 'left');
        $this->db->join('spares_customers sc', 'sc.customer_id = so.customer_id', 'left');
        $this->db->join('customer_detail cd', 'cd.id = so.customer_id', 'left');

        if (!empty($filters['request_id'])) {
            $this->db->where('r.request_id', (int) $filters['request_id']);
        }

        if (!empty($filters['opportunity_id'])) {
            $this->db->where('r.opportunity_id', (int) $filters['opportunity_id']);
        }

        if (isset($filters['visit_id'])) {
            if ((int) $filters['visit_id'] > 0) {
                $this->db->where('r.visit_id', (int) $filters['visit_id']);
            } elseif (!empty($filters['only_order_requests'])) {
                $this->db->where('r.visit_id IS NULL', null, false);
            }
        }

        if (!empty($filters['created_by'])) {
            $this->db->where('r.created_by', (int) $filters['created_by']);
        }

        if (!empty($filters['requested_for_user_id'])) {
            $this->db->where('r.requested_for_user_id', (int) $filters['requested_for_user_id']);
        }

        if (!empty($filters['engineer_id'])) {
            $this->db->where('r.engineer_id', (int) $filters['engineer_id']);
        }

        if (!empty($filters['status'])) {
            if (is_array($filters['status'])) {
                $this->db->where_in('r.status', $filters['status']);
            } else {
                $this->db->where('r.status', $filters['status']);
            }
        }

        if (!empty($filters['exclude_request_id'])) {
            $this->db->where('r.request_id !=', (int) $filters['exclude_request_id']);
        }

        if (!empty($filters['parent_request_id'])) {
            $this->db->where('r.parent_request_id', (int) $filters['parent_request_id']);
        }

        $this->db->order_by('r.request_date', 'DESC');
        $this->db->order_by('r.request_id', 'DESC');

        if (!empty($filters['limit'])) {
            $this->db->limit((int) $filters['limit']);
        }

        $rows = $this->db->get()->result();

        foreach ($rows as $row) {
            $row->amount = (float) ($row->amount ?? 0);
            // NULL approved_amount means the HOD has not revised anything, so it
            // must not collapse to 0.00 - payable_amount is the figure Accounts
            // pays and amount_revised drives the "HOD changed this" callouts.
            $raw_approved = property_exists($row, 'approved_amount') ? $row->approved_amount : null;
            $row->approved_amount = ($raw_approved === null || $raw_approved === '') ? null : (float) $raw_approved;
            $row->payable_amount = ($row->approved_amount !== null && $row->approved_amount > 0)
                ? $row->approved_amount
                : $row->amount;
            $row->amount_revised = $row->approved_amount !== null
                && abs($row->approved_amount - $row->amount) > 0.009;
            $row->po_amount = (float) ($row->po_amount ?? 0);
            $row->parent_request_amount = (float) ($row->parent_request_amount ?? 0);
            $row->customer_name = $this->normalize_person_name(
                $row->live_customer_name ?? '',
                trim((string) ($row->customer_name ?? ''))
            );
            $row->customer_company_name = trim((string) ($row->live_company_name ?? ''));
            if ($row->customer_company_name === '') {
                $row->customer_company_name = $row->customer_name;
            }
            $row->created_by_name = $this->normalize_person_name($row->created_by_name, 'Unknown Requester');
            $row->engineer_name = $this->normalize_person_name($row->engineer_name);
            $row->requested_for_name = $this->normalize_person_name($row->requested_for_name);
            $row->hod_name = $this->normalize_person_name($row->hod_name);
            $row->status_slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', (string) $row->status), '-'));
            $row->request_basis = strtoupper(trim((string) $row->request_basis));
        }

        return $rows;
    }

    public function get_related_requests($opportunity_id, $visit_id = null, $exclude_request_id = null)
    {
        $filters = [
            'opportunity_id' => (int) $opportunity_id,
            'exclude_request_id' => (int) $exclude_request_id,
        ];

        if (!empty($visit_id)) {
            $filters['visit_id'] = (int) $visit_id;
        }

        return $this->get_request_rows($filters);
    }

    public function get_request_logs($request_id)
    {
        $this->ensure_tables();

        return $this->db->select("
                l.*,
                TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) as actor_name
            ")
            ->from("{$this->log_table} l")
            ->join('system_users u', 'u.user_id = l.actor_id', 'left')
            ->where('l.request_id', (int) $request_id)
            ->order_by('l.log_id', 'DESC')
            ->get()
            ->result();
    }

    public function get_context_totals($opportunity_id, $visit_id = null)
    {
        $this->ensure_tables();

        $this->db->select("
            SUM(CASE WHEN status = 'Approved' THEN COALESCE(NULLIF(approved_amount, 0), amount) ELSE 0 END) as approved_total,
            SUM(CASE WHEN status = 'Pending HOD Approval' THEN amount ELSE 0 END) as pending_total,
            COUNT(*) as request_count
        ", false);
        $this->db->from($this->table);
        $this->db->where('opportunity_id', (int) $opportunity_id);

        if (!empty($visit_id)) {
            $this->db->where('visit_id', (int) $visit_id);
        }

        $row = $this->db->get()->row();

        return [
            'approved_total' => (float) ($row->approved_total ?? 0),
            'pending_total' => (float) ($row->pending_total ?? 0),
            'request_count' => (int) ($row->request_count ?? 0),
        ];
    }

    private function ensure_field($field_name, $query)
    {
        if (!$this->db->field_exists($field_name, $this->table)) {
            $this->db->query($query);
        }
    }

    private function normalize_person_name($name, $fallback = '')
    {
        $name = trim((string) $name);
        return $name !== '' ? $name : $fallback;
    }
}
