<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('home_model');
        $this->load->helper('url');
    }

    public function index() {
        $data = [];
        $data['page_title'] = 'Production Reports Dashboard';

        // Get the lists for our dropdown menus
        $data['machines'] = $this->home_model->get_unique_machines();
        $data['parts'] = $this->home_model->get_unique_parts();
        $data['operators'] = $this->home_model->get_unique_operators();

        // Load the new dashboard view
        $this->load->view('home_view', $data);
    }
}