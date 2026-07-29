<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Assembly_progress extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper(array('form', 'url'));
        $this->load->library('upload'); // Load upload library
        // Check login session here if needed
    }

    // 1. Supervisor Dashboard: View Active Jobs
    public function index() {
        // Join assignments with Machines and Lines to get readable names
        $this->db->select('trn_machine_assignments.*, mst_lines.line_name, mst_machines.machine_name');
        $this->db->from('trn_machine_assignments');
        $this->db->join('mst_lines', 'mst_lines.line_id = trn_machine_assignments.line_id');
        $this->db->join('mst_machines', 'mst_machines.machine_id = trn_machine_assignments.machine_id');
        
        // FILTER: Only show active jobs (is_active = 1)
        // Optional: Filter by specific supervisor ID if not Admin
        $this->db->where('trn_machine_assignments.is_active', 1);
        
        $data['active_jobs'] = $this->db->get()->result();
        
        $this->load->view('assembly_progress/dashboard', $data);
    }

    // 2. Handle Form Submission
    public function save_daily_log() {
        $assignment_id = $this->input->post('assignment_id');
        
        // 1. Configure Image Upload
        $config['upload_path']   = './uploads/daily_logs/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size']      = 4096; // 4MB max
        $config['file_name']     = 'log_' . $assignment_id . '_' . date('YmdHis');

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('site_photo')) {
            // Upload Failed
            $error = $this->upload->display_errors();
            $this->session->set_flashdata('error', $error);
            redirect('production/index');
        } else {
            // Upload Success
            $upload_data = $this->upload->data();
            $image_path = 'uploads/daily_logs/' . $upload_data['file_name'];

            // 2. Prepare Data for DB
            $data = array(
                'assignment_id'   => $assignment_id,
                'log_date'        => $this->input->post('log_date'),
                'progress_status' => $this->input->post('progress_status'),
                'remarks'         => $this->input->post('remarks'),
                'image_path'      => $image_path,
                'created_by'      => 1 // Replace with $this->session->userdata('user_id')
            );

            $this->db->insert('trn_daily_logs', $data);
            
            $this->session->set_flashdata('success', 'Daily report uploaded successfully!');
            redirect('production/index');
        }
    }
}