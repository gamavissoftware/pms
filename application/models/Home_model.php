<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Fetches a distinct list of all machine numbers.
     * @return array
     */
    public function get_unique_machines() {
        $this->db->distinct();
        $this->db->select('machine_no');
        $this->db->from('production_logs');
        $this->db->order_by('machine_no', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Fetches a distinct list of all parts (drawing_no and part_name).
     * @return array
     */
   public function get_unique_parts() {
        $this->db->distinct();
        $this->db->select('drawing_no, part_name');
        $this->db->from('production_logs');
        
        // --- ADD THESE TWO LINES ---
        $this->db->where('part_name IS NOT NULL');
        $this->db->where('part_name !=', '');
        // --- END OF ADDITION ---
        
        $this->db->order_by('part_name', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Fetches a distinct list of all operator names.
     * @return array
     */
    public function get_unique_operators() {
        $this->db->distinct();
        $this->db->select('operator_name');
        $this->db->from('production_logs');
        $this->db->where('operator_name IS NOT NULL');
        $this->db->where('operator_name !=', '');
        $this->db->order_by('operator_name', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }
}