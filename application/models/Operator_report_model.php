<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Operator_report_model
 *
 * This model is responsible for fetching all detailed data for a single
 * operator's performance report for a given date range.
 */
class Operator_report_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Checks if an operator with the given name exists in the logs.
     *
     * @param string $operatorName The operator's name.
     * @return bool True if the operator exists, false otherwise.
     */
    public function operator_exists($operatorName) {
        $this->db->select('1');
        $this->db->from('production_logs');
        $this->db->where('operator_name', $operatorName);
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    /**
     * Fetches the top-level KPIs for a single operator.
     *
     * @param string $operatorName The operator's name.
     * @param string $startDate The start date of the period.
     * @param string $endDate   The end date of the period.
     * @return array An array containing the KPIs for the specified operator.
     */
    public function get_operator_kpis($operatorName, $startDate, $endDate) {
        $this->db->select("
            SUM(actual_qty) AS total_produced,
            SUM(quantity_rejected) AS total_rejected,
            SUM(total_time_minutes) AS total_actual_time,
            SUM(cycle_time_minutes * actual_qty) AS total_standard_time
        ");
        $this->db->from('production_logs');
        $this->db->where('operator_name', $operatorName);
        $this->db->where('production_date >=', $startDate);
        $this->db->where('production_date <=', $endDate);

        $query = $this->db->get();
        $result = $query->row_array();

        if (!$result || $result['total_produced'] === null) {
            return null;
        }

        // --- NEW: Calculate Performance Efficiency ---
        $result['performance_efficiency'] = ($result['total_actual_time'] > 0)
            ? round(($result['total_standard_time'] / $result['total_actual_time']) * 100, 2)
            : 0;
        // --- END NEW ---

        $total_produced = (int) $result['total_produced'];
        $total_rejected = (int) $result['total_rejected'];
        $total_good = $total_produced - $total_rejected;

        $result['quality_rate_percentage'] = ($total_produced > 0)
            ? round(($total_good / $total_produced) * 100, 2)
            : 0;

        $result['primary_machine'] = $this->get_primary_machine_for_operator($operatorName, $startDate, $endDate);

        return $result;
    }

    /**
     * Finds the machine an operator used most frequently.
     */
    private function get_primary_machine_for_operator($operatorName, $startDate, $endDate) {
        $this->db->select('machine_no, COUNT(log_id) as log_count');
        $this->db->from('production_logs');
        $this->db->where('operator_name', $operatorName);
        $this->db->where('production_date >=', $startDate);
        $this->db->where('production_date <=', $endDate);
        $this->db->group_by('machine_no');
        $this->db->order_by('log_count', 'DESC');
        $this->db->limit(1);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row()->machine_no;
        }
        return 'N/A';
    }

    /**
     * Fetches production quantity by part for a single operator.
     *
     * @param string $operatorName The operator's name.
     * @param string $startDate The start date of the period.
     * @param string $endDate   The end date of the period.
     * @return array Data formatted for a chart.
     */
    public function get_production_by_part($operatorName, $startDate, $endDate) {
        $this->db->select('part_name, SUM(actual_qty) as total_quantity');
        $this->db->from('production_logs');
        $this->db->where('operator_name', $operatorName);
        $this->db->where('production_date >=', $startDate);
        $this->db->where('production_date <=', $endDate);
        $this->db->group_by('part_name');
        $this->db->order_by('total_quantity', 'DESC');

        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * Fetches quality data (good vs. rejected) by part for an operator.
     *
     * @param string $operatorName The operator's name.
     * @param string $startDate The start date of the period.
     * @param string $endDate   The end date of the period.
     * @return array Data formatted for a stacked bar chart.
     */
    public function get_quality_by_part($operatorName, $startDate, $endDate) {
        $this->db->select("
            part_name,
            SUM(actual_qty - quantity_rejected) as good_units,
            SUM(quantity_rejected) as rejected_units
        ");
        $this->db->from('production_logs');
        $this->db->where('operator_name', $operatorName);
        $this->db->where('production_date >=', $startDate);
        $this->db->where('production_date <=', $endDate);
        $this->db->group_by('part_name');
        $this->db->order_by('good_units', 'DESC');

        $query = $this->db->get();
        return $query->result_array();
    }


    /**
     * Fetches the detailed production log entries for a single operator.
     *
     * @param string $operatorName The operator's name.
     * @param string $startDate The start date of the period.
     * @param string $endDate   The end date of the period.
     * @return array An array of production log rows.
     */
    public function get_operator_production_logs($operatorName, $startDate, $endDate) {
        $this->db->select('production_date, shift, machine_no, part_name, planned_qty, actual_qty, quantity_rejected, remarks');
        $this->db->from('production_logs');
        $this->db->where('operator_name', $operatorName);
        $this->db->where('production_date >=', $startDate);
        $this->db->where('production_date <=', $endDate);
        $this->db->order_by('production_date', 'DESC');

        $query = $this->db->get();
        return $query->result_array();
    }
}