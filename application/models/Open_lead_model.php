<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Open_lead_model extends CI_Model {

	public function __construct()
	{
		
		parent::__construct();

	}

	function getlead_based_customer($lead_id)
	{
		$data=array();
		$reste=$this->db->select('customer_name,company_name')->from('leads')->where('id',$lead_id)->get();
		if($reste->num_rows()>0)
		{
			foreach($reste->result() as $row);
			
			$data[]=$row->customer_name;
			$data[]=$row->company_name;
		}

		return $data;

	}

	function getquotation_based_customer($company_id)
	{
		$data=array();
		$restey=$this->db->select('customer_name,company_name')->form('customer_detail')->where('id',$company_id)->get();
		if($restey->num_rows()>0)
		{
		foreach($restey->result() as $restey1);
		$data[]=$row->customer_name;
		$data[]=$row->company_name;
		}
	return $data;

	}

	function getCustomerName($customer_id) {
		$res = '';
		$sql = $this->db->select('company_name')
						->from('customer_detail')
						->where('id', $customer_id)
						->get();

			if($sql->num_rows() > 0) {
				foreach ($sql->result() as $row) {
					$res = $row->company_name;
				}
			}

			return $res;
	}


	function getCustomerDetails($customer_id) {
		$res = array();
		$sql = $this->db->select('company_name,tds_appl,tds_per')
						->from('customer_detail')
						->where('id', $customer_id)
						->get();

			if($sql->num_rows() > 0) {
				foreach ($sql->result() as $row) {
					$res[] = $row->company_name;
					$res[] = $row->tds_appl;
					$res[] = $row->tds_per;
				}
			}

			return $res;
	}
	

}