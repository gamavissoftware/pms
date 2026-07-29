<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Finance_controller extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Finance_model');
    }

    public function index()
    {
        $data['summary'] = $this->Finance_model->get_finance_summary();
        $data['po_list'] = $this->Finance_model->get_po_finance_data();

        $this->load->view('finance_dashboard', $data);
    }

}