<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Business_card_leads_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function get_company_info()
	{
		return $this->db
			->select('company_name, logo, colorcode')
			->from('company_information')
			->limit(1)
			->get()
			->row();
	}

	public function get_all_leads()
	{
		return $this->db
			->select("
				bcl.id,
				bcl.name,
				bcl.company,
				bcl.mobile,
				bcl.email,
				bcl.address,
				bcl.remarks,
				bcl.lead_type,
				bcl.scanned_at,
				bcl.created_at,
				bcl.status,
				ei.exhibition,
				CONCAT_WS(' ', u.first_name, u.last_name) AS created_by_name
			", FALSE)
			->from('business_card_leads bcl')
			->join('exhibition_info ei', 'ei.id = bcl.exhibition_id', 'left')
			->join('system_users u', 'u.user_id = bcl.created_by', 'left')
			->order_by('bcl.scanned_at', 'DESC')
			->order_by('bcl.id', 'DESC')
			->get()
			->result();
	}
}
