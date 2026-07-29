<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Reports Controller
 *
 * This controller handles requests for detailed, specific reports,
 * such as the performance of an individual machine, part, or operator.
 */
class Reports extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Load all of our report models
        $this->load->model('machine_report_model');
        $this->load->model('part_report_model');
        $this->load->model('operator_report_model');
        $this->load->model('home_model'); // <-- Add this line
        $this->load->model('shift_report_model');
        $this->load->helper('url');
    }

    private function get_date_range() {
        $startDate = $this->input->get('start_date', TRUE);
        $endDate = $this->input->get('end_date', TRUE);

        if (empty($startDate) || empty($endDate)) {
            $startDate = date('Y-m-01');
            $endDate   = date('Y-m-t');
        }

        return ['startDate' => $startDate, 'endDate' => $endDate];
    }

    public function machine($machineNo = NULL) {
        if (empty($machineNo)) {
            show_404();
            return;
        }
        
        $machineNo = urldecode($machineNo);
        $dates = $this->get_date_range();
        $startDate = $dates['startDate'];
        $endDate = $dates['endDate'];

        if (!$this->machine_report_model->machine_exists($machineNo)) {
            show_error('No production data found for machine: ' . html_escape($machineNo), 404, 'Machine Data Not Found');
            return;
        }

        $data = [];
        // NEW: Get the list of all machines for the filter dropdown
        $data['all_machines'] = $this->home_model->get_unique_machines();

        // Pass the current dates to the view for the filter to use
        $data['start_date'] = $startDate;
        $data['end_date'] = $endDate;

        $data['page_title'] = 'Machine Performance Report: ' . html_escape($machineNo);
        $data['machine_no'] = html_escape($machineNo);
        $data['report_period'] = 'Report for period: ' . date('d M, Y', strtotime($startDate)) . ' to ' . date('d M, Y', strtotime($endDate));
        
        $data['kpis'] = $this->machine_report_model->get_machine_kpis($machineNo, $startDate, $endDate);
        $data['downtime_breakdown'] = $this->machine_report_model->get_machine_downtime_breakdown($machineNo, $startDate, $endDate);
        $data['parts_produced'] = $this->machine_report_model->get_parts_produced_by_machine($machineNo, $startDate, $endDate);
        $data['production_logs'] = $this->machine_report_model->get_machine_production_logs($machineNo, $startDate, $endDate);

        $this->load->view('machine_report_view', $data);
    }

    public function part($partName = NULL) {
        if (empty($partName)) {
            show_404();
            return;
        }

        $partName = urldecode($partName);
        $dates = $this->get_date_range();
        $startDate = $dates['startDate'];
        $endDate = $dates['endDate'];

        if (!$this->part_report_model->part_exists($partName)) {
            show_error('No production data found for Part: ' . html_escape($partName), 404, 'Part Data Not Found');
            return;
        }

        $data = [];
        $data['all_parts'] = $this->home_model->get_unique_parts();
        $data['start_date'] = $startDate;
        $data['end_date'] = $endDate;
        
        $kpis = $this->part_report_model->get_part_kpis($partName, $startDate, $endDate);
        
        if ($kpis) {
            $data['kpis'] = $kpis;
            $data['production_by_machine'] = $this->part_report_model->get_production_by_machine($partName, $startDate, $endDate);
            $data['rejections_by_machine'] = $this->part_report_model->get_rejections_by_machine($partName, $startDate, $endDate);
            $data['production_logs'] = $this->part_report_model->get_part_production_logs($partName, $startDate, $endDate);
            $data['drawing_no'] = $kpis['drawing_no'];
        } else {
            $data['kpis'] = null;
            $data['production_by_machine'] = [];
            $data['rejections_by_machine'] = [];
            $data['production_logs'] = [];
            $data['drawing_no'] = 'N/A'; // No drawing number if no data
        }

        $data['page_title'] = 'Part Analysis: ' . html_escape($partName);
        $data['part_name'] = html_escape($partName); // Pass the part name for the switcher
        $data['report_period'] = 'Report for period: ' . date('d M, Y', strtotime($startDate)) . ' to ' . date('d M, Y', strtotime($endDate));
        
        $this->load->view('part_report_view', $data);
    }

   public function operator($operatorName = NULL) {
        if (empty($operatorName)) {
            show_404();
            return;
        }
        
        $operatorName = urldecode($operatorName);
        $dates = $this->get_date_range();
        $startDate = $dates['startDate'];
        $endDate = $dates['endDate'];

        if (!$this->operator_report_model->operator_exists($operatorName)) {
            show_error('No production data found for operator: ' . html_escape($operatorName), 404, 'Operator Data Not Found');
            return;
        }

        $data = [];
        // NEW: Get the list of all operators for the filter dropdown
        $data['all_operators'] = $this->home_model->get_unique_operators();

        // Pass current dates to the view
        $data['start_date'] = $startDate;
        $data['end_date'] = $endDate;
        
        $data['page_title'] = 'Operator Efficiency Report: ' . html_escape($operatorName);
        $data['operator_name'] = html_escape($operatorName); // Pass operator name for the switcher
        $data['report_period'] = 'Report for period: ' . date('d M, Y', strtotime($startDate)) . ' to ' . date('d M, Y', strtotime($endDate));

        $kpis = $this->operator_report_model->get_operator_kpis($operatorName, $startDate, $endDate);
        
        if ($kpis) {
            $data['kpis'] = $kpis;
            $data['production_by_part'] = $this->operator_report_model->get_production_by_part($operatorName, $startDate, $endDate);
            $data['quality_by_part'] = $this->operator_report_model->get_quality_by_part($operatorName, $startDate, $endDate);
            $data['production_logs'] = $this->operator_report_model->get_operator_production_logs($operatorName, $startDate, $endDate);
        } else {
            $data['kpis'] = null;
            $data['production_by_part'] = [];
            $data['quality_by_part'] = [];
            $data['production_logs'] = [];
        }
        
        $this->load->view('operator_report_view', $data);
    }

    public function shifts() {
    // We don't need a URL parameter, just the dates
    $dates = $this->get_date_range();
    $startDate = $dates['startDate'];
    $endDate = $dates['endDate'];

    $data = [];
    $data['page_title'] = 'Shift Comparison Report';
    $data['report_period'] = 'Comparison for period: ' . date('d M, Y', strtotime($startDate)) . ' to ' . date('d M, Y', strtotime($endDate));
    $data['start_date'] = $startDate;
    $data['end_date'] = $endDate;

    // Get all the data from the new model
    $data['kpis'] = $this->shift_report_model->get_kpis_by_shift($startDate, $endDate);
    $data['top_parts_data'] = $this->shift_report_model->get_top_parts_by_shift($startDate, $endDate);
    $data['downtime_data'] = $this->shift_report_model->get_downtime_by_shift($startDate, $endDate);
    
    // Load the new view
    $this->load->view('shift_report_view', $data);
}
}