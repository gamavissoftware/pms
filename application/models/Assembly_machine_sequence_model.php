<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Assembly_machine_sequence_model extends CI_Model {
    private $table = 'trn_assembly_machine_sequence';

    public function ensure_schema() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `{$this->table}` (
                `assignment_id` INT(11) NOT NULL,
                `line_id` INT(11) NOT NULL,
                `sequence_no` INT(11) NOT NULL DEFAULT 0,
                `updated_by` INT(11) NOT NULL,
                `updated_at` DATETIME NOT NULL,
                PRIMARY KEY (`assignment_id`),
                KEY `idx_assembly_machine_sequence_line` (`line_id`, `sequence_no`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function save_line_sequence($line_id, $assignment_ids, $user_id) {
        $this->db->trans_start();
        foreach (array_values($assignment_ids) as $index => $assignment_id) {
            $data = array(
                'assignment_id' => (int) $assignment_id,
                'line_id' => (int) $line_id,
                'sequence_no' => $index + 1,
                'updated_by' => (int) $user_id,
                'updated_at' => date('Y-m-d H:i:s')
            );
            $this->db->replace($this->table, $data);
        }
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
}
