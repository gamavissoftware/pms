<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shift_report_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Fetches and calculates all KPIs, grouped by shift.
     */
    public function get_kpis_by_shift($startDate, $endDate) {
        $this->db->select("
            shift,
            SUM(actual_qty) AS total_produced,
            SUM(quantity_rejected) AS total_rejected,
            SUM(total_time_minutes) AS total_actual_time,
            SUM(cycle_time_minutes * actual_qty) AS total_standard_time,
            SUM(b_d + setting_s + rm_short + `no operator` + other) AS total_downtime
        ");
        $this->db->from('production_logs');
        $this->db->where('production_date >=', $startDate);
        $this->db->where('production_date <=', $endDate);
        $this->db->where_in('shift', ['A', 'B']); // Ensure we only get data for these shifts
        $this->db->group_by('shift');

        $query = $this->db->get();
        $results = $query->result_array();
        
        // Process the results to calculate derived KPIs
        $processed_data = [];
        foreach ($results as $row) {
            $shift_name = 'Shift ' . $row['shift'];
            $total_produced = (int)$row['total_produced'];
            $total_rejected = (int)$row['total_rejected'];
            $total_good = $total_produced - $total_rejected;

            $row['performance_efficiency'] = ($row['total_actual_time'] > 0)
                ? round(($row['total_standard_time'] / $row['total_actual_time']) * 100, 2)
                : 0;

            $row['quality_rate'] = ($total_produced > 0)
                ? round(($total_good / $total_produced) * 100, 2)
                : 0;
            
            $processed_data[$shift_name] = $row;
        }
        return $processed_data;
    }

    /**
     * Fetches production data for the top 5 parts, grouped by shift.
     */
    public function get_top_parts_by_shift($startDate, $endDate, $limit = 5) {
        // First, find the top 5 parts overall in the period
        $top_parts_query = $this->db
            ->select('part_name, SUM(actual_qty) as total')
            ->from('production_logs')
            ->where('production_date >=', $startDate)
            ->where('production_date <=', $endDate)
            ->group_by('part_name')
            ->order_by('total', 'DESC')
            ->limit($limit)
            ->get();
        
        $top_parts = array_column($top_parts_query->result_array(), 'part_name');

        if (empty($top_parts)) {
            return [];
        }

        // Then, get the production for those parts, broken down by shift
        $this->db->select('shift, part_name, SUM(actual_qty) as total_produced');
        $this->db->from('production_logs');
        $this->db->where('production_date >=', $startDate);
        $this->db->where('production_date <=', $endDate);
        $this->db->where_in('part_name', $top_parts);
        $this->db->where_in('shift', ['A', 'B']);
        $this->db->group_by(['shift', 'part_name']);
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
   
    public function get_downtime_by_shift($startDate, $endDate) {
        $this->db->select("
            shift,
            SUM(b_d) AS Breakdown,
            SUM(setting_s) AS Setup,
            SUM(rm_short) AS 'RM Shortage',
            SUM(`no operator`) AS 'No Operator',
            SUM(other) AS Other
        ");
        $this->db->from('production_logs');
        $this->db->where('production_date >=', $startDate);
        $this->db->where('production_date <=', $endDate);
        $this->db->where_in('shift', ['A', 'B']);
        $this->db->group_by('shift');
        
        $query = $this->db->get();
        return $query->result_array();
    }
}