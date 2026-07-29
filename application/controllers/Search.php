<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Search extends CI_Controller {

	

	public function __construct()

	{

		parent::__construct();
		$this->load->model('Lead_model');
		$userrole =$this->session->userdata['logged_in']['role'];
		/*if($userrole==1){
			
		}else{
		$ip = $_SERVER["REMOTE_ADDR"];
		 $query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
		 if($query->num_rows()=='0'){
		 $this->session->set_flashdata('message','<div class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</div><br/><br><br>');
		 redirect(page_url.'User');    

		 } 
		}*/

		$this->load->model('Salescrm_model','salescrm');
		$this->load->model('Dashboard_model','dashboard');
	}


	function searchProducts()

	{
		$keyword = $this->input->post('keyword');
		if($keyword){
			$result = $this->Lead_model->searchProducts($keyword);
			$html="";
			$html.="<ul id='country-list'style=''>";
			if (count($result) > 0) {
				foreach ($result as $rows) {

					// if($rows->seo_url=='')
					// {
					// 	$url='Product/productdetail/'.$rows->product_id; 
					// }else
					// {
					// 	$url=''.$rows->seo_url;   
					// }

					$html.="<a href='".page_url."Search/search_box/".base64_encode($rows->id)."'><li style='list-style:none;color:black'>".$rows->contact_no."</li></a>";
				}
			}else
			{
				$html.="<li style='list-style:none;color:black'> Not Found </li>";
			}

			$html.="</ul>";

			echo $html; 

			// $data['getMenuProducts'] = $this->Mainmodel->getMenuProducts();
			// $data['searchProducts'] = $this->Mainmodel->searchProducts($keyword);
			// $this->load->view('header',$data);
		}
	}

	public function search_box(){

	$this->load->view('dashboard/search');

	}


	function globalfilter()
	{
		$this->load->view('dashboard/filter_search');
	}

	function get_all_users() {

	   
	   if(isset($q)){
	   	$q = $_GET['q'];
	   $query = $this->db->select('user_id, first_name, last_name')
						 ->from('system_users')
						 ->where('user_status', 1)
						 ->like('first_name', $q, 'both')
						 ->get();
}else{
	$query = $this->db->select('user_id, first_name, last_name')
						 ->from('system_users')
						 ->where('user_status', 1)
						 ->order_by('first_name','ASC')
						 ->get();
}
		if($query->num_rows()>0) {
			foreach($query->result() as $row) {
				$json[] = array('id'=>$row->user_id, 'text'=>$row->first_name." ".$row->last_name);
			}
		} else {
				$json[] = array('id'=>"", 'text'=>"No Data Available");
		}
		
		echo json_encode($json);
	}


	function global_filter_data()
	{
		$from=date('Y-m-d',strtotime($this->input->post('from_date')));
		$to=date('Y-m-d',strtotime($this->input->post('to_date')));
		$users=$this->input->post('usersall');
		$stage=$this->input->post('stages');
		$products=$this->input->post('products');
		$source=$this->input->post('lead_source');
		$unqualified_reason=$this->input->post('unqualified_reason');
		$product_qty=$this->input->post('product_qty');
		$product_pack_size=$this->input->post('product_pack_size');
		$date_type=$this->input->post('date_type');
		$pack = array();
		$user_id=$_SESSION['logged_in']['user_id'];

		if(count($users)>0)
		{
			$users1 = "'" . implode ( "', '", $users) . "'";
			$userdata = base64_encode($users1); 
		} else {
			$userdata = "NA";
		}

		if(count($stage)>0)
		{
			$stage1 = "'" . implode ( "', '", $stage) . "'";
			$stagedata = base64_encode($stage1); 
		}else
		{
			$stagedata="NA";
		}


		if(count($products)>0)
		{
			$products1 = "'" . implode ( "', '", $products) . "'";
			$productdata = base64_encode($products1); 
		}else
		{
			$productdata="NA";
		}


		if(count($source)>0)
		{
			$source1 = "'" . implode ( "', '", $source) . "'";
			$sourcedata = base64_encode($source1); 
		}else
		{
			$sourcedata="NA";
		}


		if(count($unqualified_reason)>0)
		{
			$unqualified_reason1 = "'" . implode ( "', '", $unqualified_reason) . "'";
			$unqualified_reasondata = base64_encode($unqualified_reason1); 
		}else
		{
			$unqualified_reasondata="NA";
		}


	
		if($product_qty<>'')
		{
			$product_qty1 = "'" . implode ( "', '", $product_qty) . "'";
			// echo $product_qty1;exit;
			$product_qtydata = base64_encode($product_qty1); 
		}else
		{
			$product_qtydata="NA";
		}

		
			$product_pack_sizedata="NA";
	


		redirect(page_url.'Search/globalfilter/'.$from.'/'.$to.'/'.$userdata.'/'.$stagedata.'/'.$productdata.'/'.$sourcedata.'/'.$unqualified_reasondata.'/'.$product_qtydata.'/'.$product_pack_sizedata.'/'.$date_type);		
	}
	


		function filtered_leads()
		{
		
		$lead_data = array();
		
		$membercheck='';
		$stagecheck='';
		$productcheck='';
		$sourcecheck='';
		$reasoncheck='';
		$qtycheck='';
		$pack_sizecheck='';
		$users="NA";
		$stages="NA";
		$product="NA";
		$lead_source="NA";
		$reason="NA";
		$qty="NA";
		$pack_size="NA";
		$from = date('Y-m-d',strtotime($this->uri->segment(3)));
		$to = date('Y-m-d',strtotime($this->uri->segment(4)));

		// echo $from;exit;

		if($this->uri->segment(5)<>'NA' && $this->uri->segment(5)<>'')
		{
		$users = base64_decode($this->uri->segment(5));
		}

		if($this->uri->segment(6)<>'NA' && $this->uri->segment(6)<>'')
		{
		$stages = base64_decode($this->uri->segment(6));
		}

		if($this->uri->segment(7)<>'NA' && $this->uri->segment(7)<>'')
		{
		$product = base64_decode($this->uri->segment(7));
		}

		if($this->uri->segment(8)<>'NA' && $this->uri->segment(8)<>'')
		{
		$lead_source = base64_decode($this->uri->segment(8));
		}

		if($this->uri->segment(9)<>'NA' && $this->uri->segment(9)<>'')
		{
		$reason = base64_decode($this->uri->segment(9));
		}



		if($this->uri->segment(10)<>'NA' && $this->uri->segment(10)<>'')
		{
		$qty = base64_decode($this->uri->segment(10));
		}


		if($this->uri->segment(11)<>'NA' && $this->uri->segment(11)<>'')
		{
		$pack_size = base64_decode($this->uri->segment(11));
		}

		if($from<>'' && $from<>'1970-01-01' && $to <>'' && $to<>'1970-01-01')
		{

			$start_date = $from.' 00:00:00';
			$end_date = $to.' 23:59:59';

			


			if($this->uri->segment(12)==0)
			{
			if($stages=="'1'")
			{
				$start_date=date('Y-m-d',strtotime($start_date));
				$end_date=date('Y-m-d',strtotime($end_date));
				$date_selected="b.create_date>= '$start_date' AND b.create_date<='$end_date'";
			}else
			{
			$date_selected="a.added_on>= '$start_date' AND a.added_on<='$end_date'";
			}
			}else
			{
				$start_date=date('Y-m-d',strtotime($start_date));
				$end_date=date('Y-m-d',strtotime($end_date));
				$date_selected="b.create_date>= '$start_date' AND b.create_date<='$end_date'";
			}

			
			if($stages<>'' && $stages<>'NA')
			{

				$getConversionLeadStage=$this->dashboard->getConversionLeadStage();
				$stagecheck="AND a.lead_status IN ($stages)";

			}

			if($lead_source<>'' && $lead_source<>'NA')
			{
				$sourcecheck="AND b.lead_source_id IN ($lead_source)";
			}


			if($product<>'' && $product<>'NA')
			{
				$productcheck="AND c.product_id IN ($product)";
			}



			if($reason <> '' && $reason <> 'NA') {
				$reasoncheck = "AND a.nonqualifiedreason IN ($reason)";
			}

			


			if($qty <> '' && $qty <> 'NA') {
				$qtycheck = "AND d.pack_size IN ($qty)";
			}




			if($pack_size <> '' && $pack_size <> 'NA') {
				$pack_sizecheck = "AND c.packsize IN ($pack_size)";
			}else
			{
				$pack_sizecheck='';
			}


			if($users<>'' && $users<>'NA')
			{
				$membercheck="AND b.added_by IN ($users)";
			}


			$resty=$this->db->query("SELECT a.nonqualifiedreason,a.id,a.added_on,a.added_by,a.lead_status, b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact,b.alt_contact_no,b.patient_type_id,a.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.client_location, b.postal_address FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE $date_selected $stagecheck $sourcecheck $productcheck $reasoncheck $qtycheck $pack_sizecheck $membercheck GROUP BY b.id ORDER BY b.id DESC");
			} else {
				$resty=$this->db->query("SELECT a.nonqualifiedreason,a.id,a.added_on,a.added_by,a.lead_status, b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no, b.alt_contact,b.alt_contact_no,b.patient_type_id,a.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.client_location, b.postal_address FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  GROUP BY b.id ORDER BY b.id DESC");
			}

			$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
			// echo $row->leadid;exit;
			$a=1;
			$checkIfQuotationExists=$this->salescrm->checkIfQuotationExists($row->leadid);

			if($checkIfQuotationExists > 0) {
				$quotation_id = $this->salescrm->getCustomerQuotationID($row->leadid);
				// echo $quotation_id;exit;
				$products = $this->salescrm->getQuotationProducts($quotation_id);
			} else {
				$products = $this->salescrm->getProducts($row->leadid);
			}
			$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
			$check_approval = $this->checkforapproval($row->lead_status);
			$checkUnqualifiedReason = $this->checkUnqualifiedReason($row->nonqualifiedreason);
			$assignedsalesmember=$this->dashboard->checkforassignedmember($row->leadid);
			$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);

			// echo "<pre>";print_r($products);exit;
			// $checkforassignedmember_verification=$this->dashboard->checkforassignedmember_verification($row->leadid);

		
		// if($row->order_value<>'' && $row->order_value<>0)
		// {
		// 	$odvalue="₹".$row->order_value;
		// }else
		// {
		// 	$odvalue="";
		// }
		

		// 	$view = "<a href='".page_url."/Search/search_box/".$row->leadid."' style='font-size:10px;' target='_blank'>Timeline</a><br/>";
		// 	if($_SESSION['logged_in']['role']==7)
		// 	{

		// 	}else{
		// 	$view.="<a href='".page_url."Leads/view_detail/".$row->leadid."' style='font-size:10px;' target='_blank'>Edit</a> | ";
		// 	}


		   	// 		$instruments[] = $row1->instruments_name.'-'.$row1->qty.' '.$pack;

		   	// $products1 = implode('<br>', $instruments);
		   	$html = '';
		   	if($products != '') {

		   	$html .= '<table style="border: 1px solid black;width:400px"" class="table table-bordered" >
						<tr>
			                <th style="border: 1px solid black;text-align:center; width:200px;">Product Name</th>
			                <th style="border: 1px solid black;text-align:center; width:200px;">Qty</th>
			                <th style="border: 1px solid black;text-align:center; width:200px;">Pack Size</th>
			                
			            </tr>';
			foreach($products as $row1) {
				
				if($checkIfQuotationExists > 0) {
					$unit=$this->salescrm->getUnitName($row1->pack_size);
				} else {
					$unit=$this->salescrm->getUnitName($row1->packsize);
				}
			    
			    $html .= '<tr>
			                <td style="border: 1px solid black;text-align:center;color:black;">'.$row1->instruments_name.'</td>
			                <td style="border: 1px solid black;text-align:center;color:black;">'.$row1->qty.' '.$unit.'</td>
			                <td style="border: 1px solid black;text-align:center;color:black;">'.$row1->instrument_pack_size.'</td>
			                			            </tr>';

		   		}
					 $html .= '</table>';
			}

			$edit = "<a href='".page_url."Leads/edit_leads/".$row->leadid."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";

			$lastup = date('d-m-Y H:i:s',strtotime($row->added_on));
			$addedBy = $this->salescrm->getusername($row->added_by);

			$getLeadStatus = $this->salescrm->getLeadStatus($row->leadid);
			if(($getLeadStatus) > 0) {
			foreach ($getLeadStatus as $status);
				$currentleadstatus = $status->lead_name;
				$currentleadremarks = $status->remarks;

			} else {
				$currentleadstatus = '';
				$currentleadremarks = '';

			}

			$lead_data[] = array(
			'sr_no'=>$i,
			'lead_create'=>$row->unique_id.'<br>'.date('d-M-Y',strtotime($row->create_date)),
			'customer_type' => $clienttype, 
			'customer_name' => $row->customer_name, 
			'company_name' => $row->company_name, 
			'contact_no' => $row->contact_no."<br/>".$row->email_id,
			'product'=>$html,
			'address'=>$row->postal_address,
			'alt_contact'=>$row->alt_contact."<br/>".$row->alt_contact_no,
			'assignedto'=>$assignedsalesmember,
			'clientremarks'=>$row->clientremarks,
			// 'ex_assignedto'=>$checkforassignedmember_verification,
			// 'odvalue'=>$odvalue,
			'assigned_on' => date('d-m-Y H:i:s', strtotime($row->added_on)),
			'lead_source' => $leadsource,
			'edit' => $edit,
			'updated_on' => $lastup."<br/><br/>".$addedBy,
			'current_status' =>"<strong style='color:red;font-weight:bold;'>".ucwords(strtolower($currentleadstatus))."</strong>",
			'reason' => $checkUnqualifiedReason
			// 'lead_id' => $row->unique_id,
			// 'update_progress' => $view,
			// 'lead_transfer' =>$transfer
			);
			$i++;


			}

			}
			
	

		$results = array(
							"sEcho" => 1,
							"iTotalRecords" => count($lead_data),
							"iTotalDisplayRecords" => count($lead_data),
							"aaData"=>$lead_data);
			
		echo json_encode($results);
		}



		function getLeadProductswithprice($lead_id) {
		$ht="";
		$ht1=array();
		$odvalue=0;
		$odd=$this->db->select('order_value')->from('leads')->where('id',$lead_id)->get();
		if($odd->num_rows()>0)
		{
			foreach($odd->result() as $odd1);
			$odvalue=$odd1->order_value;

		}
			
			$sql=$this->db->select('b.id, b.product_name')
			 			    ->from('lead_products a')
						    ->join('products b', 'b.id=a.product_id')
						    ->where('a.lead_id',$lead_id)
						    ->get();

		$j = 1;
		if($sql->num_rows()>0) {
				
			foreach($sql->result() as $rows) {

				// $ht.="<tr>
				// <td style='padding:0px;'>".ucwords($rows->product_name)."</td>
				// </tr>";

				$ht1[]=ucwords($rows->product_name);
				$j++;
			}

			if(count($ht1)>0)
			{
			$products=implode(",",$ht1);
			}

			$ht.=$products;
			// if($odvalue>0)
			// {

			// $ht.="<br/><p>Value- ".$odvalue."</p>";
			
			// }

			

		}else
		{
			$ht.="NO PRODUCT SELECTED";
		}

		return $ht;
	}


	function checkforapproval($lead_stage)
{
		$rr=$this->db->select('lead_id')->from('lead_stage')->where('conversion_step',1)->where('lead_id',$lead_stage)->get();
		return $rr->num_rows();

}


function get_all_tl_users() {

	  $user_id=$_SESSION['logged_in']['user_id'];
	  $emp=$this->Lead_model->getteamdetails($user_id);
	  if(count($emp)>0)
	  {
	  $team_member="'" . implode ( "', '", $emp ) . "'";
	  }else
	  {
	  	$team_member='';
	  }
	   $q = $_GET['q'];
			$this->db->select('b.user_id, b.first_name, b.last_name');
			$this->db->from('user_role a');
			$this->db->join('system_users b', 'b.user_role_id=a.user_role_id');
			$this->db->where('b.user_status', 1);
			if($team_member<>'')
			{
			$this->db->where_in('b.user_id',$team_member,false);
			}
			$this->db->where('a.status', 1);
			$this->db->like('b.first_name', $q, 'both');
			$query = $this->db->get();

		if($query->num_rows()>0) {
			foreach($query->result() as $row) {
				$json[] = array('id'=>$row->user_id, 'text'=>$row->first_name." ".$row->last_name);
			}
		} else {
				$json[] = array('id'=>"", 'text'=>"No Data Available");
		}
		
		echo json_encode($json);
	}

	function checkUnqualifiedReason($reason_id) {
		$res = '';
		$sql = $this->db->select('reason')
						->from('leads_unqualified_reason')
						->where('reason_id', $reason_id)
						->get();

		if($sql->num_rows() > 0) {
			foreach ($sql->result() as $row);
				$res = $row->reason;
		}

		return $res;
	}



	function leads_globalfilter()
	{
		$this->load->view('dashboard/filter_search');
	}

	function filtered_leads_search()
		{
		
		$lead_data = array();
		
		$membercheck='';
		$stagecheck='';
		$productcheck='';
		$sourcecheck='';
		$reasoncheck='';
		$qtycheck='';
		$pack_sizecheck='';
		$users="NA";
		$stages="NA";
		$product="NA";
		$lead_source="NA";
		$reason="NA";
		$qty="NA";
		$pack_size="NA";
		$from = date('Y-m-d',strtotime($this->uri->segment(3)));
		$to = date('Y-m-d',strtotime($this->uri->segment(4)));

		// echo $from;exit;

		if($this->uri->segment(5)<>'NA' && $this->uri->segment(5)<>'')
		{
		$users = base64_decode($this->uri->segment(5));
		}

		if($this->uri->segment(6)<>'NA' && $this->uri->segment(6)<>'')
		{
		$stages = base64_decode($this->uri->segment(6));
		}

		if($this->uri->segment(7)<>'NA' && $this->uri->segment(7)<>'')
		{
		$product = base64_decode($this->uri->segment(7));
		}

		if($this->uri->segment(8)<>'NA' && $this->uri->segment(8)<>'')
		{
		$lead_source = base64_decode($this->uri->segment(8));
		}

		if($this->uri->segment(9)<>'NA' && $this->uri->segment(9)<>'')
		{
		$reason = base64_decode($this->uri->segment(9));
		}



		if($this->uri->segment(10)<>'NA' && $this->uri->segment(10)<>'')
		{
		$qty = base64_decode($this->uri->segment(10));
		}


		if($this->uri->segment(11)<>'NA' && $this->uri->segment(11)<>'')
		{
		$pack_size = base64_decode($this->uri->segment(11));
		}

		if($from<>'' && $from<>'1970-01-01' && $to <>'' && $to<>'1970-01-01')
		{

			$start_date = $from.' 00:00:00';
			$end_date = $to.' 23:59:59';

			
			if($users<>'' && $users<>'NA')
			{
				$membercheck="AND b.added_by IN ($users)";
			}

			if($stages<>'' && $stages<>'NA')
			{
				$getConversionLeadStage=$this->dashboard->getConversionLeadStage();
				$stagecheck="AND a.lead_status IN ($stages)";

			}

			if($product<>'' && $product<>'NA')
			{
				$productcheck="AND c.product_id IN ($product)";
			}


			if($lead_source<>'' && $lead_source<>'NA')
			{
				$sourcecheck="AND b.lead_source_id IN ($lead_source)";
			}



			if($reason <> '' && $reason <> 'NA') {
				$reasoncheck = "AND a.nonqualifiedreason IN ($reason)";
			}




			if($qty <> '' && $qty <> 'NA') {
				$qtycheck = "AND d.pack_size IN ($qty)";
			}


			if($pack_size <> '' && $pack_size <> 'NA') {
				$pack_sizecheck = "AND c.packsize IN ($pack_size)";
			}else
			{
				$pack_sizecheck='';
			}


			// $qty='';

			$date_selected="AND a.added_on>= '$start_date' AND a.added_on<='$end_date'";

			// $resty=$this->db->query("SELECT b.create_date,b.order_value,a.id,b.lead_source_id, a.lead_status, b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_no,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.pincode, b.address, c.added_on, d.first_name as fname, d.last_name as lname, e.first_name, e.last_name FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN $c c ON c.lead_id=b.id JOIN system_users d ON d.user_id=c.member_id JOIN system_users e ON c.added_by=e.user_id JOIN lead_products f ON b.id=f.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks_view sub WHERE a.lead_id=sub.lead_id) $membercheck $productcheck $sourcecheck $stagecheck $dat GROUP BY a.lead_id ORDER BY b.id DESC");

			$resty=$this->db->query("SELECT a.nonqualifiedreason,a.id,a.added_on,a.added_by,a.lead_status, b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no, b.alt_contact,b.alt_contact_no,b.patient_type_id,a.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.client_location, b.postal_address FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_products c ON c.lead_id=b.id JOIN presto_instruments d ON d.id=c.product_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) $stagecheck $sourcecheck $productcheck $reasoncheck $date_selected $qtycheck $pack_sizecheck $membercheck GROUP BY b.id ORDER BY b.id DESC");
			} else {
				$resty=$this->db->query("SELECT a.nonqualifiedreason,a.id,a.added_on,a.added_by,a.lead_status, b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no, b.alt_contact,b.alt_contact_no,b.patient_type_id,a.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.client_location, b.postal_address FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_products c ON c.lead_id=b.id JOIN presto_instruments d ON d.id=c.product_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) GROUP BY b.id ORDER BY b.id DESC");
			}

			$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
			// echo $row->leadid;exit;
			$a=1;
			$checkIfQuotationExists=$this->salescrm->checkIfQuotationExists($row->leadid);

			if($checkIfQuotationExists > 0) {
				$quotation_id = $this->salescrm->getCustomerQuotationID($row->leadid);
				// echo $quotation_id;exit;
				$products = $this->salescrm->getQuotationProducts($quotation_id);
			} else {
				$products = $this->salescrm->getProducts($row->leadid);
			}
			$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
			$check_approval = $this->checkforapproval($row->lead_status);
			$checkUnqualifiedReason = $this->checkUnqualifiedReason($row->nonqualifiedreason);
			$assignedsalesmember=$this->dashboard->checkforassignedmember($row->leadid);
			$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);

			// echo "<pre>";print_r($products);exit;
			// $checkforassignedmember_verification=$this->dashboard->checkforassignedmember_verification($row->leadid);

		
		// if($row->order_value<>'' && $row->order_value<>0)
		// {
		// 	$odvalue="₹".$row->order_value;
		// }else
		// {
		// 	$odvalue="";
		// }
		

		// 	$view = "<a href='".page_url."/Search/search_box/".$row->leadid."' style='font-size:10px;' target='_blank'>Timeline</a><br/>";
		// 	if($_SESSION['logged_in']['role']==7)
		// 	{

		// 	}else{
		// 	$view.="<a href='".page_url."Leads/view_detail/".$row->leadid."' style='font-size:10px;' target='_blank'>Edit</a> | ";
		// 	}


		   	// 		$instruments[] = $row1->instruments_name.'-'.$row1->qty.' '.$pack;

		   	// $products1 = implode('<br>', $instruments);
		   	$html = '';
		   	if($products != '') {

		   	$html .= '<table style="border: 1px solid black;width:400px"" class="table table-bordered" >
						<tr>
			                <th style="border: 1px solid black;text-align:center; width:200px;">Product Name</th>
			                <th style="border: 1px solid black;text-align:center; width:200px;">Qty</th>
			                <th style="border: 1px solid black;text-align:center; width:200px;">Pack Size</th>
			                
			            </tr>';
			foreach($products as $row1) {
				
				if($checkIfQuotationExists > 0) {
					$unit=$this->salescrm->getUnitName($row1->pack_size);
				} else {
					$unit=$this->salescrm->getUnitName($row1->packsize);
				}
			    
			    $html .= '<tr>
			                <td style="border: 1px solid black;text-align:center;color:black;">'.$row1->instruments_name.'</td>
			                <td style="border: 1px solid black;text-align:center;color:black;">'.$row1->qty.' '.$unit.'</td>
			                <td style="border: 1px solid black;text-align:center;color:black;">'.$row1->instrument_pack_size.'</td>
			                			            </tr>';

		   		}
					 $html .= '</table>';
			}

			$edit = "<a href='".page_url."Leads/edit_leads/".$row->leadid."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";

			$lastup = date('d-m-Y H:i:s',strtotime($row->added_on));
			$addedBy = $this->salescrm->getusername($row->added_by);

			$getLeadStatus = $this->salescrm->getLeadStatus($row->leadid);
			if(($getLeadStatus) > 0) {
			foreach ($getLeadStatus as $status);
				$currentleadstatus = $status->lead_name;
				$currentleadremarks = $status->remarks;

			} else {
				$currentleadstatus = '';
				$currentleadremarks = '';

			}

			$lead_data[] = array(
			'sr_no'=>$i,
			'lead_create'=>$row->unique_id.'<br>'.date('d-M-Y',strtotime($row->create_date)),
			'customer_type' => $clienttype, 
			'customer_name' => $row->customer_name, 
			'company_name' => $row->company_name, 
			'contact_no' => $row->contact_no."<br/>".$row->email_id,
			'product'=>$html,
			'address'=>$row->postal_address,
			'alt_contact'=>$row->alt_contact."<br/>".$row->alt_contact_no,
			'assignedto'=>$assignedsalesmember,
			'clientremarks'=>$row->clientremarks,
			// 'ex_assignedto'=>$checkforassignedmember_verification,
			// 'odvalue'=>$odvalue,
			'assigned_on' => date('d-m-Y H:i:s', strtotime($row->added_on)),
			'lead_source' => $leadsource,
			'edit' => $edit,
			'updated_on' => $lastup."<br/><br/>".$addedBy,
			'current_status' =>"<strong style='color:red;font-weight:bold;'>".ucwords(strtolower($currentleadstatus))."</strong>",
			'reason' => $checkUnqualifiedReason
			// 'lead_id' => $row->unique_id,
			// 'update_progress' => $view,
			// 'lead_transfer' =>$transfer
			);
			$i++;


			}

			}
			
	

		$results = array(
							"sEcho" => 1,
							"iTotalRecords" => count($lead_data),
							"iTotalDisplayRecords" => count($lead_data),
							"aaData"=>$lead_data);
			
		echo json_encode($results);
		}

}