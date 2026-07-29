<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Service_deployment_type_model extends CI_Model
{
    private $table = 'mst_service_deployment_types';

    public function get_table_name()
    {
        return $this->table;
    }

    public function ensure_table()
    {
        if (!$this->db->table_exists($this->table)) {
            $this->db->query(
                "CREATE TABLE IF NOT EXISTS `{$this->table}` (
                    `deployment_type_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `deployment_type_value` VARCHAR(100) NOT NULL,
                    `deployment_type_label` VARCHAR(150) NOT NULL,
                    `sort_order` INT(11) NOT NULL DEFAULT 0,
                    `status` TINYINT(1) NOT NULL DEFAULT 1,
                    `created_by` INT(11) DEFAULT NULL,
                    `created_at` DATETIME DEFAULT NULL,
                    `updated_at` DATETIME DEFAULT NULL,
                    PRIMARY KEY (`deployment_type_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
            );
        }

        if ($this->db->table_exists($this->table) && (int) $this->db->count_all($this->table) === 0) {
            $this->seed_defaults();
            return;
        }

        if ($this->db->table_exists($this->table)) {
            $this->sync_default_types();
        }
    }

    public function get_all()
    {
        $this->ensure_table();

        return $this->db->order_by('sort_order', 'ASC')
            ->order_by('deployment_type_label', 'ASC')
            ->get($this->table)
            ->result();
    }

    public function get_active_for_scheduler()
    {
        $this->ensure_table();

        if (!$this->db->table_exists($this->table)) {
            return $this->build_default_type_objects();
        }

        if ((int) $this->db->count_all($this->table) === 0) {
            return $this->build_default_type_objects();
        }

        return $this->db->where('status', 1)
            ->order_by('sort_order', 'ASC')
            ->order_by('deployment_type_label', 'ASC')
            ->get($this->table)
            ->result();
    }

    public function get_by_id($deployment_type_id)
    {
        $this->ensure_table();

        return $this->db->get_where($this->table, [
            'deployment_type_id' => (int) $deployment_type_id,
        ])->row();
    }

    public function value_exists($value, $exclude_id = 0)
    {
        $sql = "SELECT deployment_type_id
                FROM {$this->table}
                WHERE LOWER(TRIM(deployment_type_value)) = ?";
        $params = [strtolower(trim($value))];

        if ((int) $exclude_id > 0) {
            $sql .= " AND deployment_type_id != ?";
            $params[] = (int) $exclude_id;
        }

        return $this->db->query($sql, $params)->num_rows() > 0;
    }

    public function label_exists($label, $exclude_id = 0)
    {
        $sql = "SELECT deployment_type_id
                FROM {$this->table}
                WHERE LOWER(TRIM(deployment_type_label)) = ?";
        $params = [strtolower(trim($label))];

        if ((int) $exclude_id > 0) {
            $sql .= " AND deployment_type_id != ?";
            $params[] = (int) $exclude_id;
        }

        return $this->db->query($sql, $params)->num_rows() > 0;
    }

    private function seed_defaults()
    {
        $now = date('Y-m-d H:i:s');
        $defaults = [];

        foreach ($this->get_default_types() as $index => $type) {
            $defaults[] = [
                'deployment_type_value' => $type['value'],
                'deployment_type_label' => $type['label'],
                'sort_order' => $index + 1,
                'status' => 1,
                'created_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($defaults)) {
            $this->db->insert_batch($this->table, $defaults);
        }
    }

    private function sync_default_types()
    {
        $existing_types = $this->db->select('deployment_type_value, deployment_type_label')
            ->from($this->table)
            ->get()
            ->result();

        $existing_values = [];
        $existing_labels = [];

        foreach ($existing_types as $existing_type) {
            $existing_values[] = strtolower(trim((string) $existing_type->deployment_type_value));
            $existing_labels[] = strtolower(trim((string) $existing_type->deployment_type_label));
        }

        $current_max_sort_order = (int) $this->db->select_max('sort_order')->get($this->table)->row()->sort_order;
        $next_sort_order = $current_max_sort_order + 1;
        $now = date('Y-m-d H:i:s');
        $rows_to_insert = [];

        foreach ($this->get_default_types() as $type) {
            $normalized_value = strtolower(trim($type['value']));
            $normalized_label = strtolower(trim($type['label']));

            if (in_array($normalized_value, $existing_values, true) || in_array($normalized_label, $existing_labels, true)) {
                continue;
            }

            $rows_to_insert[] = [
                'deployment_type_value' => $type['value'],
                'deployment_type_label' => $type['label'],
                'sort_order' => $next_sort_order++,
                'status' => 1,
                'created_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($rows_to_insert)) {
            $this->db->insert_batch($this->table, $rows_to_insert);
        }
    }

    private function build_default_type_objects()
    {
        $types = [];

        foreach ($this->get_default_types() as $index => $type) {
            $types[] = (object) [
                'deployment_type_id' => $index + 1,
                'deployment_type_value' => $type['value'],
                'deployment_type_label' => $type['label'],
                'sort_order' => $index + 1,
                'status' => 1,
            ];
        }

        return $types;
    }

    private function get_default_types()
    {
        return [
            ['value' => 'Installation', 'label' => 'New Installation'],
            ['value' => 'Breakdown', 'label' => 'Emergency Breakdown'],
            ['value' => 'AMC', 'label' => 'AMC Service Visit'],
            ['value' => 'Training', 'label' => 'Technical Training'],
            ['value' => 'Health Check- Up', 'label' => 'Health Check- Up'],
        ];
    }
}
