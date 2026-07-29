<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ServiceStages extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) { redirect(page_url); }
    }

    public function index() {
        $data['stages'] = $this->db->order_by('sort_order', 'ASC')->get('service_lead_stages')->result();
        $this->load->view('spares/service_stages_view', $data);
    }

    public function save_stage() {
        $id = $this->input->post('stage_id');
        $data = array(
            'stage_name' => $this->input->post('stage_name'),
            'sort_order' => $this->input->post('sort_order')
        );

        if ($id) {
            $this->db->where('stage_id', $id)->update('service_lead_stages', $data);
        } else {
            $this->db->insert('service_lead_stages', $data);
        }
        redirect(page_url.'ServiceStages');
    }
}