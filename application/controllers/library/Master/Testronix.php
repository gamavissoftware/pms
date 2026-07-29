<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Testronix extends CI_Controller {

	public function __construct()
	{
		
		parent::__construct();
		// $this->load->model('Master_model','master');
	}
	
	
	function index() {
		$this->load->view('Quotation/testronix_lead_source');
	}

	function save_lead_source() {

		$data = array(
				'source' => $this->input->post('source'),
				'status' => $this->input->post('status')
				);

			$this->db->insert('testronix_lead_source', $data);

			if($this->db->affected_rows() > 0) {
				$this->session->set_flashdata('message','<span style="color:black; float-left:20px;" class="alert alert-success">Lead Source Added Successfully.</span><br/>');
				redirect(page_url.'Master/Testronix');
			}
	}

	function designation_master() {
		$this->load->view('Quotation/designation_master');
	}


	function save_designation() {

		$data = array(
				'designation' => $this->input->post('designation'),
				'status' => $this->input->post('status')
				);

			$this->db->insert('designation_master', $data);

			if($this->db->affected_rows() > 0) {
				$this->session->set_flashdata('message','<span style="color:black; float-left:20px;" class="alert alert-success">Designation Added Successfully.</span><br/>');
				redirect(page_url.'Master/Testronix/designation_master');
			}
	}

}