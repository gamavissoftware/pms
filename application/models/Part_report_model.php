<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Part_report_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // UPDATED to use part_name
    public function part_exists($partName) {
        $this->db->select('1');
        $this->db->from('production_logs');
        $this->db->where('part_name', $partName);
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    // UPDATED to use part_name
    public function get_part_kpis($partName, $startDate, $endDate) {
        $this->db->select("
            drawing_no,
            part_name,
            SUM(actual_qty) AS total_produced,
            SUM(quantity_rejected) AS total_rejected,
            SUM(total_time_minutes) AS total_actual_time,
            SUM(cycle_time_minutes * actual_qty) AS total_standard_time 
        ");
        $this->db->from('production_logs');
        $this->db->where('part_name', $partName);
        $this->db->where('production_date >=', $startDate);
        $this->db->where('production_date <=', $endDate);
        $this->db->group_by(['drawing_no', 'part_name']);

        $query = $this->db->get();
        $result = $query->row_array();

        if (!$result) { return null; }

        $result['performance_efficiency'] = ($result['total_actual_time'] > 0)
            ? round(($result['total_standard_time'] / $result['total_actual_time']) * 100, 2) : 0;
        
        $result['rejection_rate_percentage'] = ($result['total_produced'] > 0)
            ? round(($result['total_rejected'] / $result['total_produced']) * 100, 2) : 0;

        $result['avg_cycle_time_per_unit'] = ($result['total_produced'] > 0)
            ? round($result['total_actual_time'] / $result['total_produced'], 2) : 0;

        $result['primary_machine'] = $this->get_primary_machine_for_part($partName, $startDate, $endDate);

        return $result;
    }

    // UPDATED to use part_name
    private function get_primary_machine_for_part($partName, $startDate, $endDate) {
        $this->db->select('machine_no, SUM(actual_qty) as total_qty');
        $this->db->from('production_logs');
        $this->db->where('part_name', $partName);
        $this->db->where('production_date >=', $startDate);
        $this->db->where('production_date <=', $endDate);
        $this->db->group_by('machine_no');
        $this->db->order_by('total_qty', 'DESC');
        $this->db->limit(1);

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->row()->machine_no : 'N/A';
    }

    // UPDATED to use part_name
    public function get_production_by_machine($partName, $startDate, $endDate) {
        $this->db->select('machine_no, SUM(actual_qty) as total_quantity');
        $this->db->from('production_logs');
        $this->db->where('part_name', $partName);
        $this->db->where('production_date >=', $startDate);
        $this->db->where('production_date <=', $endDate);
        $this->db->group_by('machine_no');
        $this->db->order_by('total_quantity', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    // UPDATED to use part_name
    public function get_rejections_by_machine($partName, $startDate, $endDate) {
        $this->db->select('machine_no, SUM(quantity_rejected) as total_rejected');
        $this->db->from('production_logs');
        $this->db->where('part_name', $partName);
        $this->db->where('production_date >=', $startDate);
        $this->db->where('production_date <=', $endDate);
        $this->db->where('quantity_rejected >', 0);
        $this->db->group_by('machine_no');
        $this->db->order_by('total_rejected', 'DESC');

        $query = $this->db->get();
        return $query->result_array();
    }

    // UPDATED to use part_name
    public function get_part_production_logs($partName, $startDate, $endDate) {
        $this->db->select('production_date, shift, machine_no, operator_name, planned_qty, actual_qty, quantity_rejected, rejection_reason, remarks');
        $this->db->from('production_logs');
        $this->db->where('part_name', $partName);
        $this->db->where('production_date >=', $startDate);
        $this->db->where('production_date <=', $endDate);
        $this->db->order_by('production_date', 'DESC');

        $query = $this->db->get();
        return $query->result_array();
    }
}