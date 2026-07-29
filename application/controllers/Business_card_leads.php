<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Business_card_leads extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$session = $this->session->userdata('logged_in');
		if ($session == FALSE)
		{
			redirect(page_url);
		}

		$user_id = $this->session->userdata['logged_in']['user_id'];
		if (empty($user_id))
		{
			redirect(site_url(), 'refresh');
		}

		$this->load->model('Business_card_leads_model', 'businesscardleads');
	}

	public function index()
	{
		$data['company_info'] = $this->businesscardleads->get_company_info();
		$data['business_card_leads'] = $this->businesscardleads->get_all_leads();

		$this->load->view('business_card_leads/index', $data);
	}
}
