<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RegisterNewClient extends CI_Controller {

function __construct()
    {  
        parent::__construct();
		$this->load->model('Master_model','master');	
    }

	public function index() {
		$data['getModules'] = $this->master->getAllModules();
		$this->load->view('register_new_client/registernewclient', $data);
	}

	public function addNewClient() {

		$data = array(
				"company_name" => $this->input->post('company_name'),
				"client_name" => $this->input->post('client_name'),
				"contact_no" => $this->input->post('contact_no'),
				"address" => $this->input->post('address'),
				"gst" => $this->input->post('gst'),
				"subs_start" => $this->input->post('subscription_start'),
				"subs_type" => $this->input->post('subs_type'),
				"renewal_subs" => $this->input->post('renewal_subs')
				);

		$last_insert_id = $this->master->addClientDetails($data);
		//echo $last_insert_id;exit;
			
				$module = $this->input->post('module');

				for ($i=0; $i < count($module); $i++) { 
					$module_data = array(
									"client_id" => $last_insert_id,
									"module_id" => $module[$i]
									);
					//echo "<pre>";print_r($module_data);exit;

					$results = $this->master->addModule($module_data);
				}
				redirect(page_url.'RegisterNewClient');
			

	}

	public function client_records() {
		$this->load->view('register_new_client/client_records');
	}

	public function client_record_list()
	{
		$i=1;
		$client_data = array();
		$res = $this->master->getClientRecords();
		foreach($res as $row) {
			
			if ($row->subs_type == 1) {
				$subs_type = 'Yearly';
			} elseif ($row->subs_type == 2) {
				$subs_type = 'Permanently';
			} else {
				$subs_type = '';
			}
		 $client_data[] = array('sr_no'=>$i,
								'company_name' => $row->company_name,
								'client_name' => $row->client_name,
								'contact_no' => $row->contact_no,
								'address'=> $row->address,
								'gst_no' => $row->gst,
								'subs_start' => $row->subs_start,
								'subs_type' => $subs_type,
								'renewal_subs' => $row->renewal_subs,
								'product_master_name' => $row->product_master_name
								);
					$i++;
				}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($client_data),
			"iTotalDisplayRecords" => count($client_data),
			"aaData"=>$client_data);
			
		echo json_encode($results);
	}


}
?>