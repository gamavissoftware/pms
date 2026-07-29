<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Sample_page extends CI_Controller {

	

	public function __construct()

	{

		parent::__construct();

		$session = $this->session->userdata('logged_in');

		if($session == FALSE)

		{

		

		redirect(page_url);

		

		}

		$this->load->model('User_model','user');

		$this->load->model('Dashboard_model','reportingdata');

		$this->load->model('Store_model','store');

		$this->load->model('Fms_model','Fms_model');

		$user_id =$this->session->userdata['logged_in']['user_id'];

	if(empty($user_id))

         {

         redirect(site_url(),'refresh');

         }

	

	}

    public function index(){
		$this->load->view('dashboard/sample_page'); 
	}

}