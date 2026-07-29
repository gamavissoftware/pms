<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DispatchReport extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Dispatch_model');
    }

    public function index() {
        $data['kpi'] = $this->Dispatch_model->get_dispatch_kpis();
        
        $filters = [
            'from_date' => $this->input->get('from_date'),
            'to_date' => $this->input->get('to_date')
        ];
        
        $data['reports'] = $this->Dispatch_model->get_dispatch_report($filters);
        $data['active_stage_name'] = "Dispatch & Accounts Summary";
        
        // Pass to your format
        $this->load->view('reports/dispatch_list_view', $data);
    }
}