<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Assembly_line_progress_model extends CI_Model {
    private $table = 'trn_assembly_line_progress';

    public function ensure_schema() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `{$this->table}` (
                `progress_id` INT(11) NOT NULL AUTO_INCREMENT,
                `line_id` INT(11) NOT NULL,
                `progress_date` DATE NOT NULL,
                `progress_percent` INT(11) NOT NULL DEFAULT 0,
                `remarks` TEXT NOT NULL,
                `image_path` VARCHAR(255) NOT NULL,
                `image_original_name` VARCHAR(255) DEFAULT NULL,
                `created_by` INT(11) NOT NULL,
                `created_at` DATETIME NOT NULL,
                PRIMARY KEY (`progress_id`),
                KEY `idx_assembly_line_progress_line` (`line_id`),
                KEY `idx_assembly_line_progress_date` (`progress_date`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function get_latest_by_lines($line_ids) {
        $line_ids = array_values(array_unique(array_filter(array_map('intval', (array) $line_ids))));
        if (empty($line_ids)) {
            return array();
        }

        $this->db->select('
            p.*,
            CONCAT(COALESCE(u.first_name, ""), " ", COALESCE(u.last_name, "")) as updated_by_name
        ', false);
        $this->db->from($this->table . ' p');
        $this->db->join(
            '(SELECT line_id, MAX(progress_id) latest_progress_id FROM ' . $this->table . ' GROUP BY line_id) latest',
            'latest.latest_progress_id = p.progress_id',
            'inner',
            false
        );
        $this->db->join('system_users u', 'u.user_id = p.created_by', 'left');
        $this->db->where_in('p.line_id', $line_ids);

        $result = array();
        foreach ($this->db->get()->result() as $row) {
            $result[(int) $row->line_id] = $row;
        }
        return $result;
    }

    public function insert($data) {
        return $this->db->insert($this->table, $data);
    }
}
