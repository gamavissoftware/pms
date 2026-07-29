<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Machine_report_model
 *
 * This model is responsible for fetching all detailed data for a single
 * machine's performance report for a given date range.
 */
class Machine_report_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Checks if a machine with the given number exists in the logs.
     *
     * @param string $machineNo The machine number to check.
     * @return bool True if the machine exists, false otherwise.
     */
    public function machine_exists($machineNo) {
        $this->db->select('1');
        $this->db->from('production_logs');
        $this->db->where('machine_no', $machineNo);
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }


    /**
     * Fetches the top-level KPIs for a single machine.
     *
     * @param string $machineNo The machine number (e.g., 'MC-01').
     * @param string $startDate The start date of the period.
     * @param string $endDate   The end date of the period.
     * @return array An array containing the KPIs for the specified machine.
     */
   public function get_machine_kpis($machineNo, $startDate, $endDate) {
        $this->db->select("
            SUM(actual_qty) AS total_production,
            SUM(quantity_rejected) AS total_rejected,
            SUM(total_time_minutes) AS total_actual_time,
            SUM(cycle_time_minutes * actual_qty) AS total_standard_time,
            SUM(b_d + setting_s + rm_short + `no operator` + other) AS total_downtime_minutes
        ");
        $this->db->from('production_logs');
        $this->db->where('machine_no', $machineNo);
        $this->db->where('production_date >=', $startDate);
        $this->db->where('production_date <=', $endDate);

        $query = $this->db->get();
        $result = $query->row_array();

        // --- NEW: Calculate Performance Efficiency ---
        $result['performance_efficiency'] = ($result['total_actual_time'] > 0)
            ? round(($result['total_standard_time'] / $result['total_actual_time']) * 100, 2)
            : 0;
        // --- END NEW ---

        $result['quality_yield_percentage'] = ($result['total_production'] > 0)
            ? round((($result['total_production'] - $result['total_rejected']) / $result['total_production']) * 100, 2)
            : 0;

        $result['most_produced_part'] = $this->get_most_produced_part($machineNo, $startDate, $endDate);

        return $result;
    }

    /**
     * Finds the name of the part most produced by a specific machine.
     */
    private function get_most_produced_part($machineNo, $startDate, $endDate) {
        $this->db->select('part_name, SUM(actual_qty) as total_qty');
        $this->db->from('production_logs');
        $this->db->where('machine_no', $machineNo);
        $this->db->where('production_date >=', $startDate);
        $this->db->where('production_date <=', $endDate);
        $this->db->group_by('part_name');
        $this->db->order_by('total_qty', 'DESC');
        $this->db->limit(1);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row()->part_name;
        }
        return 'N/A';
    }


    /**
     * Fetches aggregated downtime data by reason for a single machine.
     *
     * @param string $machineNo The machine number.
     * @param string $startDate The start date of the period.
     * @param string $endDate   The end date of the period.
     * @return array Data formatted for a chart library.
     */
    public function get_machine_downtime_breakdown($machineNo, $startDate, $endDate) {
        $this->db->select("
            SUM(b_d) AS Breakdown,
            SUM(setting_s) AS Setup,
            SUM(rm_short) AS 'RM Shortage',
            SUM(`no operator`) AS 'No Operator',
            SUM(other) AS Other
        ");
        $this->db->from('production_logs');
        $this->db->where('machine_no', $machineNo);
        $this->db->where('production_date >=', $startDate);
        $this->db->where('production_date <=', $endDate);

        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Fetches the quantity of each part produced by a single machine.
     *
     * @param string $machineNo The machine number.
     * @param string $startDate The start date of the period.
     * @param string $endDate   The end date of the period.
     * @return array Data formatted for a chart library.
     */
    public function get_parts_produced_by_machine($machineNo, $startDate, $endDate) {
        $this->db->select('part_name, SUM(actual_qty) as total_quantity');
        $this->db->from('production_logs');
        $this->db->where('machine_no', $machineNo);
        $this->db->where('production_date >=', $startDate);
        $this->db->where('production_date <=', $endDate);
        $this->db->group_by('part_name');
        $this->db->order_by('total_quantity', 'DESC');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Fetches the detailed production log entries for a single machine.
     *
     * @param string $machineNo The machine number.
     * @param string $startDate The start date of the period.
     * @param string $endDate   The end date of the period.
     * @return array An array of production log rows.
     */
    public function get_machine_production_logs($machineNo, $startDate, $endDate) {
        $this->db->select('production_date, shift, part_name, planned_qty, actual_qty, quantity_rejected, total_time_minutes, remarks');
        $this->db->from('production_logs');
        $this->db->where('machine_no', $machineNo);
        $this->db->where('production_date >=', $startDate);
        $this->db->where('production_date <=', $endDate);
        $this->db->order_by('production_date', 'DESC');

        $query = $this->db->get();
        return $query->result_array();
    }
}