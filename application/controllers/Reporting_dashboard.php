<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reporting_dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('production_dashboard_model');
        $this->load->helper('url');
        $this->load->library('form_validation'); // Load form validation library
    }

    public function index() {
        // --- NEW: Date Handling Logic ---
        $this->form_validation->set_rules('start_date', 'Start Date', 'trim|required');
        $this->form_validation->set_rules('end_date', 'End Date', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            // If form is not submitted or validation fails, default to the current month
            $startDate = date('Y-m-01');
            $endDate   = date('Y-m-t'); // 't' gets the last day of the month
        } else {
            // If form is submitted, use the posted dates
            $startDate = $this->input->post('start_date');
            $endDate   = $this->input->post('end_date');
        }
        // --- END: Date Handling Logic ---

        $data = [];
        
        // Pass the dates to the view so the form can display the current selection
        $data['start_date'] = $startDate;
        $data['end_date'] = $endDate;

        // Update titles to be dynamic
        $data['page_title'] = 'Production Dashboard';
        $data['report_period'] = 'Report for period: ' . date('d M, Y', strtotime($startDate)) . ' to ' . date('d M, Y', strtotime($endDate));

        // Get all the data from the model using the dynamic dates
        $data['kpis'] = $this->production_dashboard_model->get_kpi_data($startDate, $endDate);
        $data['daily_trend_data'] = $this->production_dashboard_model->get_daily_production_trend($startDate, $endDate);
        $data['downtime_data'] = $this->production_dashboard_model->get_downtime_by_reason($startDate, $endDate);
        
        $this->load->view('dashboard_view', $data);
    }
}