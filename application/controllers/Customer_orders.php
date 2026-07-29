<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_orders extends CI_Controller {

public function __construct()
{

parent::__construct();

}

function customer_order_comparision()
{
$this->load->view('product_comparision/customer_buying_comparision');
}


// public function order_comparision()
// 	{

// 		$this->db->
// 				$prestogroup_quotations = array();
// 				$prestogroup_quotations[] = array('sr_no'=>$i,
// 				'quotation_date'=>date('d-m-Y',strtotime($row->quotation_date)),
// 				'reference_number'=>$row->reference_number,
// 				'company_name'=>$row->company_name,
// 				'customer_name'=>$row->title." ".$row->customer_name,
// 				'contact_number'=>$row->contact_number,
// 				'email'=>$row->email,
// 				'followup'=>$followupinfo,
// 				'address'=>$row->address,
// 				'viewpi'=>$viewpi,
// 				'sendemail'=>$sendemail,
// 				'edit'=>$edit,
// 				'sendpi'=>$sendpi,
// 				'view_pi_history'=>$view_pi_history);

// 				$results = array(
// 				"sEcho" => 1,
// 				"iTotalRecords" => count($prestogroup_quotations),
// 				"iTotalDisplayRecords" => count($prestogroup_quotations),
// 				"aaData"=>$prestogroup_quotations);
// 				echo json_encode($results);

// 	}





}