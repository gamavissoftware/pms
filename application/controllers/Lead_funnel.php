<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lead_funnel extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Leadfunnel_model','leadfunnel');

	}
	
	public function index(){
		$this->load->view('lead_funnel/lead_funnel_form');
	}
	
		public function add_lead_funnel() {
            // $form_name = $this->input->post('form_name');
			$field_name = $this->input->post('add_field');
			$required_field = $this->input->post('required_field');

		
				
				$data = array(
							'form_name' => $this->input->post('form_name')
							);
				

				$result = $this->leadfunnel->add_lead_funnel($data);
			
				
				for($j = 0; $j < count($field_name); $j++) {
				    $datas = array(
				            'form_id' => $result,
							'field_name' => $field_name[$j],
							'required' => $required_field[$j]
							);
				// 			echo "<pre>";print_r($datas);exit;

				    $results = $this->leadfunnel->add_lead_funnel_fields($datas);
				}
				
					
						$this->session->set_flashdata('message','<span style="color:black; float-left:20px;" class="alert alert-success">Fields Added Successfully.</span><br/>');
						redirect(page_url.'Lead_funnel');
				
			    

		}
}