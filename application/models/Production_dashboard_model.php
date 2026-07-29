<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Production_dashboard_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Fetches the main KPI data for the dashboard.
     *
     * @param string $startDate The start date of the period (e.g., '2023-06-01')
     * @param string $endDate   The end date of the period (e.g., '2023-06-30')
     * @return array An array containing the main KPIs.
     */
    public function get_kpi_data($startDate, $endDate) {
        $this->db->select("
            SUM(planned_qty) AS total_planned,
            SUM(actual_qty) AS total_produced,
            SUM(quantity_rejected) AS total_rejected,
            SUM(total_time_minutes) AS total_actual_time,
            SUM(cycle_time_minutes * actual_qty) AS total_standard_time,
            SUM(b_d) AS downtime_breakdown,
            SUM(setting_s) AS downtime_setup,
            SUM(rm_short) AS downtime_rm_shortage,
            SUM(`no operator`) AS downtime_no_operator,
            SUM(other) AS downtime_other
        ");

        $this->db->from('production_logs');
        $this->db->where('production_date >=', $startDate);
        $this->db->where('production_date <=', $endDate);

        $query = $this->db->get();
        $result = $query->row_array();

        // Calculate derived KPIs
        $kpis = [];

        // --- NEW: Calculate Performance Efficiency ---
        $kpis['performance_efficiency'] = ($result['total_actual_time'] > 0)
            ? round(($result['total_standard_time'] / $result['total_actual_time']) * 100, 2)
            : 0;
        // --- END NEW ---
        
        $kpis['total_produced'] = (int) $result['total_produced'];
        $kpis['total_planned'] = (int) $result['total_planned'];
        $kpis['total_rejected'] = (int) $result['total_rejected'];
        $kpis['total_good_parts'] = $kpis['total_produced'] - $kpis['total_rejected'];

        $kpis['plan_achievement_percentage'] = ($kpis['total_planned'] > 0)
            ? round(($kpis['total_produced'] / $kpis['total_planned']) * 100, 2)
            : 0;

        $kpis['quality_yield_percentage'] = ($kpis['total_produced'] > 0)
            ? round(($kpis['total_good_parts'] / $kpis['total_produced']) * 100, 2)
            : 0;

        $kpis['total_downtime_minutes'] = $result['downtime_breakdown'] + $result['downtime_setup'] + $result['downtime_rm_shortage'] + $result['downtime_no_operator'] + $result['downtime_other'];

        return $kpis;
    }

    public function get_daily_production_trend($startDate, $endDate) {
        $this->db->select('production_date, SUM(actual_qty) as daily_total');
        $this->db->from('production_logs');
        $this->db->where('production_date >=', $startDate);
        $this->db->where('production_date <=', $endDate);
        $this->db->group_by('production_date');
        $this->db->order_by('production_date', 'ASC');

        $query = $this->db->get();
        $results = $query->result_array();

        $chart_data = [
            'labels' => [],
            'data'   => [],
        ];
        foreach ($results as $row) {
            $chart_data['labels'][] = date('d M', strtotime($row['production_date']));
            $chart_data['data'][] = (int) $row['daily_total'];
        }
        return $chart_data;
    }

    /**
     * Fetches aggregated data for the downtime reasons bar chart.
     *
     * @param string $startDate The start date of the period.
     * @param string $endDate   The end date of the period.
     * @return array Data formatted for a chart library.
     */
    public function get_downtime_by_reason($startDate, $endDate) {
        // --- FIX IS HERE ---
        // We apply the same fix to this function.
        $this->db->select("
            SUM(b_d) AS Breakdown,
            SUM(setting_s) AS Setup,
            SUM(rm_short) AS 'RM Shortage',
            SUM(`no operator`) AS 'No Operator', /* Corrected syntax */
            SUM(other) AS Other
        ");
        // --- END OF FIX ---

        $this->db->from('production_logs');
        $this->db->where('production_date >=', $startDate);
        $this->db->where('production_date <=', $endDate);

        $query = $this->db->get();
        return $query->row_array();
    }
}