<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_cleanup_model extends CI_Model {

    public function get_distinct_part_names() {
        $this->db->distinct();
        $this->db->select('part_name');
        $this->db->from('production_logs');
        $query = $this->db->get();
        
        $result = $query->result_array();
        
        $part_names = [];
        foreach($result as $row){
            if (!empty(trim($row['part_name']))) {
                $part_names[] = $row['part_name'];
            }
        }
        return array_unique($part_names);
    }

    public function update_part_names_safely($data) {
        // Start a transaction
        $this->db->trans_start();

        foreach ($data as $update_item) {
            $this->db->where_in('part_name', $update_item['originals']);
            $this->db->update('production_logs', ['part_name' => $update_item['new_name']]);
        }
        
        // Complete the transaction
        $this->db->trans_complete();

        // Was the transaction successful?
        return $this->db->trans_status();
    }
}