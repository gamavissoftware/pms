<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sampling extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Salescrm_model','salescrm');	
	}


	public function pending_samples()
	{
		$this->load->view('sampling/pending_sample_request');
	} 


	function sample_request() {
		$lead_data = array();
		$query = $this->db->select('a.id,e.first_name,e.last_name,c.instruments_name,a.lead_id,a.lead_product_id,a.status,a.requestOn,a.request_remarks,a.request_by,d.customer_name,d.contact_no,d.city,d.company_name,d.postal_address,d.alt_contact,d.alt_contact_no,d.title')->from('sample_to_be_sent a')->join('lead_products b','a.lead_product_id=b.id')->join('presto_instruments c','b.product_id=c.id')->join('leads d','a.lead_id=d.id')->join('system_users e','e.user_id=a.request_by')->where('a.status',0)->get();
		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			$status="<a href='javascript:;' class='btn btn-warning btn-xs' onclick='add_remarks(".$row->id.")'>Mark as Sent</a>";
		
			$lead_data[] = array('sr_no'=>$i,
								 'company_name'=>$row->company_name,
								 'customer_name'=>"Primary Contact: ".$row->customer_name."<br/>Mob:".$row->contact_no."<br/><br/>Alternate Person: ".$row->alt_contact."<br/>Mob: ".$row->alt_contact_no,
								 'products'=>$row->instruments_name,
								 'remarks'=>"<strong style='color:red;font-weight:bold;'>".$row->request_remarks."</strong>",
								 'shipaddress' =>$row->postal_address."-".$row->city,
								 'raisedby' =>$row->first_name." ".$row->last_name."<br/>".date('d-M-Y H:i:s',strtotime($row->requestOn)),
								 'status' =>$status
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

	
	function addremarks()
	{
		$sample_request=$this->input->post('request_id');
		$remarks=$this->input->post('remarks');

		$data=array('status'=>1,'send_remarks'=>$remarks,'sendOn'=>date('Y-m-d H:i:s'),'sendBy'=>$_SESSION['logged_in']['user_id']);

		$this->db->where('id',$sample_request);
		$this->db->update('sample_to_be_sent',$data);
		$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully updated.</div>');
		redirect(page_url.'Sampling/pending_samples');
	}


	function  sample_request_history()
	{
		$this->load->view('sampling/sample_request_history');
	}
	function sample_request_history_list() {
		$lead_data = array();
		$query = $this->db->select('f.first_name as fname,f.last_name as lname,a.sendBy,a.sendOn,a.send_remarks,a.id,e.first_name,e.last_name,c.instruments_name,a.lead_id,a.lead_product_id,a.status,a.requestOn,a.request_remarks,a.request_by,d.customer_name,d.contact_no,d.city,d.company_name,d.postal_address,d.alt_contact,d.alt_contact_no,d.title')->from('sample_to_be_sent a')->join('lead_products b','a.lead_product_id=b.id')->join('presto_instruments c','b.product_id=c.id')->join('leads d','a.lead_id=d.id')->join('system_users e','e.user_id=a.request_by')->join('system_users f','f.user_id=a.sendBy')->where('a.status',1)->get();
		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			$status="Remarks: ".$row->send_remarks."<br/>Send By: ".$row->fname." ".$row->lname."<br/>Send On ".date('d-m-Y',strtotime($row->sendOn));
		
			$lead_data[] = array('sr_no'=>$i,
								 'company_name'=>$row->company_name,
								 'customer_name'=>"Primary Contact: ".$row->customer_name."<br/>Mob:".$row->contact_no."<br/><br/>Alternate Person: ".$row->alt_contact."<br/>Mob: ".$row->alt_contact_no,
								 'products'=>$row->instruments_name,
								 'remarks'=>"<strong style='color:red;font-weight:bold;'>".$row->request_remarks."</strong>",
								 'shipaddress' =>$row->postal_address."-".$row->city,
								 'raisedby' =>$row->first_name." ".$row->last_name."<br/>".date('d-M-Y H:i:s',strtotime($row->requestOn)),
								 'status' =>$status
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


	public function pending_trials()
	{
		$this->load->view('sampling/pending_trial_request');
	} 

	public function unapprovedtrail()
	{
		$this->load->view('sampling/unapproved_trail');
	} 


	function trial_request() {
		$lead_data = array();
		$this->db->select('a.id, e.first_name, e.last_name, c.instruments_name, a.lead_id, a.lead_product_id, a.status, a.requestOn, a.request_remarks, a.request_by, c.trial_reading, d.customer_name, d.contact_no, d.city, d.company_name, d.postal_address,d.alt_contact,d.alt_contact_no,d.title')->from('trial_to_be_sent a')->join('lead_products b','a.lead_product_id=b.id')->join('presto_instruments c','b.product_id=c.id')->join('leads d','a.lead_id=d.id')->join('system_users e','e.user_id=a.request_by')->where('a.status',0);
		
		if($this->uri->segment(3)<>''){
				$this->db->where('a.assignedperson',$this->uri->segment(3));
		}else{

		}
		$query =  $this->db->get();
		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			if($row->trial_reading == 1) {
				$reading = "<a href='".page_url."Sampling/trial_readings/".$row->id."/".$row->lead_id."' class='btn btn-success btn-xs'>Add Readings</a><br><br>";
			} else {
				$reading = '';
			}
			
			$status=$reading."<a href='javascript:;' class='btn btn-warning btn-xs' onclick='add_remarks(".$row->id.", ".$row->lead_id.")'>Trial Done</a><br><br><a href='javascript:;' class='btn btn-danger btn-xs' onclick='close_trial(".$row->id.", ".$row->lead_id.")'>Close Trial</a>";
		
			$lead_data[] = array('sr_no'=>$i,
								 'company_name'=>$row->company_name,
								 'customer_name'=>"Primary Contact: ".$row->customer_name."<br/>Mob:".$row->contact_no."<br/><br/>Alternate Person: ".$row->alt_contact."<br/>Mob: ".$row->alt_contact_no,
								 'products'=>$row->instruments_name,
								 'remarks'=>"<strong style='color:red;font-weight:bold;'>".$row->request_remarks."</strong>",
								 'shipaddress' =>$row->postal_address."-".$row->city,
								 'raisedby' =>$row->first_name." ".$row->last_name."<br/>".date('d-M-Y H:i:s',strtotime($row->requestOn)),
								 'status' =>$status
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

	function unapproved_trial_request() {
		$lead_data = array();
		$this->db->select('a.id, e.first_name, e.last_name, c.instruments_name, a.lead_id, a.lead_product_id, a.status, a.requestOn, a.request_remarks, a.request_by, c.trial_reading, d.customer_name, d.contact_no, d.city, d.company_name, d.postal_address,d.alt_contact,d.alt_contact_no,d.title, f.first_name as approvalpersonf, f.last_name as approvalpersonl')->from('trial_to_be_sent a')->join('lead_products b','a.lead_product_id=b.id')->join('presto_instruments c','b.product_id=c.id')->join('leads d','a.lead_id=d.id')->join('system_users e','e.user_id=a.request_by')->join('system_users f','f.user_id=a.approval_addedby','left')->where('a.status',0)->where('a.approved',2);
		
		if($this->uri->segment(3)<>''){
				$this->db->where('a.assignedperson',$this->uri->segment(3));
		}else{

		}
		$query =  $this->db->get();
		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			if($row->trial_reading == 1) {
				$reading = "<a href='".page_url."Sampling/trial_readings/".$row->id."/".$row->lead_id."' class='btn btn-success btn-xs'>Add Readings</a><br><br>";
			} else {
				$reading = '';
			}

			$unapprovedremarks = $row->approvalremarks;
			
			$status=$reading."<a href='javascript:;' class='btn btn-warning btn-xs' onclick='add_remarks(".$row->id.", ".$row->lead_id.")'>Trial Done</a><br><br><a href='javascript:;' class='btn btn-danger btn-xs' onclick='close_trial(".$row->id.", ".$row->lead_id.")'>Close Trial</a>";
		
			$lead_data[] = array('sr_no'=>$i,
								 'company_name'=>$row->company_name,
								 'customer_name'=>"Primary Contact: ".$row->customer_name."<br/>Mob:".$row->contact_no."<br/><br/>Alternate Person: ".$row->alt_contact."<br/>Mob: ".$row->alt_contact_no,
								 'products'=>$row->instruments_name,
								 'remarks'=>"<strong style='color:red;font-weight:bold;'>".$row->request_remarks."</strong>",
								 'shipaddress' =>$row->postal_address."-".$row->city,
								 'raisedby' =>$row->first_name." ".$row->last_name."<br/>".date('d-M-Y H:i:s',strtotime($row->requestOn)),
								 'status' =>$unapprovedremarks."<br><br>Added By: ".$row->approvalpersonf." ".$approvalpersonl
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


	function addremarks_trial()
	{
		$sample_request=$this->input->post('request_id');

		$lead_id=$this->input->post('lead_id');

		$remarks=$this->input->post('remarks');

		$data=array('status'=>1,'send_remarks'=>$remarks,'sendOn'=>date('Y-m-d H:i:s'),'sendBy'=>$_SESSION['logged_in']['user_id']);

		$this->db->where('id',$sample_request);
		$this->db->update('trial_to_be_sent',$data);


		$getLeadInfo = $this->salescrm->getLeadInfo($lead_id);
		$product_name = $this->salescrm->gettrialproductInfo($sample_request);
		
		$company_name = '';
		$sales_person = '';
		$salescontact = '';

		if($getLeadInfo != '') {
		    foreach ($getLeadInfo as $rows);
		    $company_name = $rows->company_name;
		    $product_name = $product_name;
		    $sales_person = $rows->first_name." ".$rows->last_name;
		    $salescontact = $rows->contact_number;
		}

		$msg = "Dear ".$sales_person." ,\n\n";
		$msg .= "Following trial has been completed\n\n";
		$msg .= "Product Name: *".$product_name."*\n";
		$msg .= "Company Name: *".$company_name."*\n";
		$msg .= "Remarks: *".$remarks."*\n\n";
		$msg .= "Please update the lead status from your panel\n\n";
		$msg .= "Team Sundar Industrial Oil 🚀";

		//ECHO $msg; exit;

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POST, 1);
				$post = array(
				'receiverMobileNo' => '91'.$salescontact,
				// 'receiverMobileNo' => '918447031736',
				// 'receiverMobileNo' => '919560814669',
				'username' => whatsappuser,
				'password' => whatsapppass,
				'filePathUrl' => $file,
				'message'=>strip_tags(ucwords(strtolower($msg))));
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
				$result = curl_exec($ch);
				//echo $result; exit;
				if (curl_errno($ch)) {
				echo 'Error:' . curl_error($ch);
				}
				curl_close($ch);

		$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully updated.</div>');
		redirect(page_url.'Sampling/pending_trials');
	}


	function  trial_request_history()
	{
		$this->load->view('sampling/trial_request_history');
	}
	function trial_request_history_list() {
		$lead_data = array();
		$this->db->select('f.first_name as fname,f.last_name as lname,a.sendBy,a.sendOn,a.send_remarks,a.id,e.first_name,e.last_name,c.instruments_name,a.lead_id,a.lead_product_id,a.status,a.requestOn,a.request_remarks,a.request_by,d.customer_name,d.contact_no,d.city,d.company_name,d.postal_address,d.alt_contact,d.alt_contact_no,d.title')->from('trial_to_be_sent a')->join('lead_products b','a.lead_product_id=b.id')->join('presto_instruments c','b.product_id=c.id')->join('leads d','a.lead_id=d.id')->join('system_users e','e.user_id=a.request_by')->join('system_users f','f.user_id=a.sendBy')->where('a.status',1);
		if($this->uri->segment(3)){
				$this->db->where('a.assignedperson',$this->uri->segment(3));
		}else{

		}
		$query = $this->db->get();
		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			$status="Remarks: ".$row->send_remarks."<br/>Send By: ".$row->fname." ".$row->lname."<br/>Send On ".date('d-m-Y',strtotime($row->sendOn));

			$view_readings = '<a href="'.page_url.'Sampling/trial_readings/'.$row->id.'/'.$row->lead_id.'/t4r4i4a4l" class="btn btn-warning btn-xs">View Readings</a>';
		
			$lead_data[] = array('sr_no'=>$i,
								 'company_name'=>$row->company_name,
								 'customer_name'=>"Primary Contact: ".$row->customer_name."<br/>Mob:".$row->contact_no."<br/><br/>Alternate Person: ".$row->alt_contact."<br/>Mob: ".$row->alt_contact_no,
								 'products'=>$row->instruments_name,
								 'remarks'=>"<strong style='color:red;font-weight:bold;'>".$row->request_remarks."</strong>",
								 'shipaddress' =>$row->postal_address."-".$row->city,
								 'raisedby' =>$row->first_name." ".$row->last_name."<br/>".date('d-M-Y H:i:s',strtotime($row->requestOn)),
								 'status' =>$status,
								 'view_readings' => $view_readings 
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

	public function trial_readings()
	{
		$this->load->view('sampling/trial_readings');
	} 

	function add_trial_readings() {
		$current_date = $this->input->post('current_date');
		$shift = $this->input->post('shift');
		$machine_name = $this->input->post('machine_name');
		$water_used = $this->input->post('water_used');
		$oil_top_up = $this->input->post('oil_top_up');
		$final_conc_1 = $this->input->post('final_conc_1');
		$final_conc_2 = $this->input->post('final_conc_2');
		$final_conc_3 = $this->input->post('final_conc_3');
		$final_conc_4 = $this->input->post('final_conc_4');
		$final_conc_5 = $this->input->post('final_conc_5');
		$final_conc_6 = $this->input->post('final_conc_6');
		$final_conc_7 = $this->input->post('final_conc_7');
		$final_conc_8 = $this->input->post('final_conc_8');
		$final_conc_9 = $this->input->post('final_conc_9');
		$final_conc_10 = $this->input->post('final_conc_10');
		$final_conc_11 = $this->input->post('final_conc_11');
		$final_conc_12 = $this->input->post('final_conc_12');
		$remarks = $this->input->post('remarks');

		$pic = $_FILES['evidence']['name'];
		if($pic <> '') {
		$files = explode('.', $pic);
		$ext = end($files);
		$newname = time().'.'.$ext;
		move_uploaded_file($_FILES['evidence']["tmp_name"], UPLOADPATH.'trailreadingdata/'.$newname);
		} else {
		$newname = '';
		}

		for($i=0; $i < count($current_date); $i++) {
			if($current_date[$i] != '') {
				$data = array(
							  'trial_id' => $this->uri->segment(3),
							  'lead_id' => $this->uri->segment(4),
							  'currentdate' => date('Y-m-d', strtotime($current_date[$i])),
							  'shift'=>$shift[$i],
							  'machine_name' => $machine_name[$i],
							  'water_used' => $water_used[$i],
							  'oil_top_up' => $oil_top_up[$i],
							  'evidencedata'=>$newname,
							  'final_conc_1' => $final_conc_1[$i],
							  'final_conc_2' => $final_conc_2[$i],
							  'remarks' => $remarks[$i]
							 );

					$this->db->insert('trial_readings', $data);
					
			}

					$q = $this->db->select('visitdays')->from('trial_to_be_sent')->where('id',$this->uri->segment(3))->get();
					foreach($q->result() as $rows);
					$numofdays  = $rows->visitdays;
					$Date = date('Y-m-d');
					$scheduledate = date('Y-m-d', strtotime($Date. ' + '.$numofdays.' days'));
					
					$data22 = array('status'=>1);
					$this->db->where('trail_id',$this->uri->segment(3));
					$this->db->update('old_customer_visit_schedule',$data22);

					$data1 = array('trail_id'=>$this->uri->segment(3),
					'visitschedule'=>$scheduledate,
					'added_by'=>$_SESSION['logged_in']['user_id'],
					'added_on'=>date('Y-m-d H:i:s'),
					'status'=>0);
					$this->db->insert('old_customer_visit_schedule',$data1);

			


		}

		$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, readings successfully added.</div>');
		redirect(page_url.'Sampling/pending_trials');
	}

	function trial_readings_list() {
		$lead_data = array();
		$trial_id = $this->uri->segment(3);
		$lead_id = $this->uri->segment(4);

		$start_date = $this->uri->segment(5);
		$end_date = $this->uri->segment(6);

		$this->db->select('id,shift, currentdate, machine_name, water_used, oil_top_up, final_conc_1, final_conc_2, remarks, evidencedata')
						  ->from('trial_readings')
						  ->where('trial_id', $trial_id)
						  ->where('lead_id', $lead_id);
						   if($start_date<>'' && $end_date<>'')
						  {
						  	$this->db->where('currentdate>=',$start_date);
						  	$this->db->where('currentdate<=',$end_date);
						  }
					
						  $query = $this->db->get();


		if($query->num_rows()>0) {
	
			$i=1;
			foreach($query->result() as $row) {
			
			$action="<a href='javascript:;' onclick='edit_reading(".$row->id.")'><i class='fa fa-pencil-square-o'></i></a>";
			if($row->evidencedata<>''){
			$media = "<a href='".page_url1."image_bank/trailreadingdata/".$row->evidencedata."' download>Download Media</a>";
		}else{
			$media="";
		}
			$lead_data[] = array('sr_no' => $i,
								 'date' => date('d-m-Y', strtotime($row->currentdate)),
								 'shift' => $row->shift,
								 'machine_name' => $row->machine_name,
								 'water_used' => $row->water_used,
								 'oil_topup' => $row->oil_top_up,
								 'final_conc_1' => $row->final_conc_1,
								 'final_conc_2' => $row->final_conc_2,
								 'remarks' => $row->remarks,
								 'action' =>$action,
								 'media' =>$media
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

	function get_readings() {
		$res = '';
		$id = $this->input->post('id');
		$query = $this->db->select('shift,currentdate, machine_name, water_used, oil_top_up, final_conc_1, final_conc_2, remarks')
						  ->from('trial_readings')
						  ->where('id', $id)
						  ->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row);

			$res = date('d-m-Y', strtotime($row->currentdate)).'|'.$row->machine_name.'|'.$row->water_used.'|'.$row->oil_top_up.'|'.$row->final_conc_1.'|'.$row->final_conc_2.'|'.$row->remarks."|".$row->shift;
		}

		echo $res;
	}

	function update_reading() {
		$reading_id = $this->input->post('reading_id');
		$current_date = $this->input->post('current_date');
		$shift = $this->input->post('shift');
		$machine_name = $this->input->post('machine_name');
		$water_used = $this->input->post('water_used');
		$oil_top_up = $this->input->post('oil_top_up');
		$final_conc_1 = $this->input->post('final_conc_1');
		$final_conc_2 = $this->input->post('final_conc_2');
		$remarks = $this->input->post('remarks');

				$data = array(
							  'trial_id' => $this->uri->segment(3),
							  'lead_id' => $this->uri->segment(4),
							  'currentdate' => date('Y-m-d', strtotime($current_date)),
							  'shift'=>$shift,
							  'machine_name' => $machine_name,
							  'water_used' => $water_used,
							  'oil_top_up' => $oil_top_up,
							  'final_conc_1' => $final_conc_1,
							  'final_conc_2' => $final_conc_2,
							  'remarks' => $remarks
							 );

				$this->db->where('id', $reading_id)
						 ->update('trial_readings', $data);

		$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, readings successfully updated.</div>');
		redirect(page_url.'Sampling/trial_readings/'.$this->uri->segment(3).'/'.$this->uri->segment(4));
	}

	function close_trial()
	{
		$sample_request=$this->input->post('request_id');
		$lead_id=$this->input->post('lead_id');
		$remarks=$this->input->post('remarks');

		$data=array(
					'status'=>1,
					'send_remarks'=>$remarks,
					'sendOn'=>date('Y-m-d H:i:s'),
					'sendBy'=>$_SESSION['logged_in']['user_id'],
					'manual_close'=>1
					);

		// echo "<pre>";print_r($data);exit;
		$this->db->where('id',$sample_request);
		$this->db->update('trial_to_be_sent',$data);

		$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully updated.</div>');
		redirect(page_url.'Sampling/pending_trials');
	}


	function trial_request_of_old_customers() {
		$lead_data = array();
		$this->db->select('a.id, e.first_name, e.last_name, c.instruments_name, a.lead_id, a.lead_product_id, a.status, a.requestOn, a.request_remarks, a.request_by, c.trial_reading, d.title, d.customer_name, d.company_name, d.ship_address, d.contact_no, d.alt_contact, d.city')->from('trial_to_be_sent a')->join('presto_instruments c','a.lead_product_id=c.id')->join('system_users e','e.user_id=a.request_by')->join('customer_detail d','a.customer_id=d.id','left')->where('a.status',0)->where('a.lead_id',0);
		if($this->uri->segment(3)){
			$this->db->where('a.assignedperson',$this->uri->segment(3));
		}
		$query = $this->db->get();
		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			if($row->trial_reading == 1) {
				$reading = "<a href='".page_url."Trail/trial_readings/".$row->id."/".$row->lead_id."' class='btn btn-success btn-xs'>Add Readings</a><br><br>";
			} else {
				$reading = '';
			}
			
			$status=$reading."<a href='javascript:;' class='btn btn-warning btn-xs' onclick='add_remarks(".$row->id.", ".$row->lead_id.")'>Trial Done</a><br><br><a href='javascript:;' class='btn btn-danger btn-xs' onclick='close_trial(".$row->id.", ".$row->lead_id.")'>Close Trial</a>";
		
			$lead_data[] = array('sr_no'=>$i,
				 				'company_name'=>$row->company_name,
								 'customer_name'=>"Primary Contact: ".$row->customer_name."<br/>Mob:".$row->contact_no."<br/><br/>Alternate Contact Number: ".$row->alt_contact,
								 'products'=>$row->instruments_name,
								 'remarks'=>"<strong style='color:red;font-weight:bold;'>".$row->request_remarks."</strong>",
								 'shipaddress' =>$row->ship_address."-".$row->city,
								 'raisedby' =>$row->first_name." ".$row->last_name."<br/>".date('d-M-Y H:i:s',strtotime($row->requestOn)),
								 'status' =>$status
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

public function samplependingforview()
	{
		$this->load->view('sampling/samplependingforview');
	} 

function sample_request_list_for_Review() {
	$userid = $_SESSION['logged_in']['user_id'];
		$lead_data = array();
		$query = $this->db->select('f.first_name as fname,f.last_name as lname,a.sendBy,a.sendOn,a.send_remarks,a.id,e.first_name,e.last_name,c.instruments_name,a.lead_id,a.lead_product_id,a.status,a.requestOn,a.request_remarks,a.request_by,d.customer_name,d.contact_no,d.city,d.company_name,d.postal_address,d.alt_contact,d.alt_contact_no,d.title')->from('sample_to_be_sent a')->join('lead_products b','a.lead_product_id=b.id')->join('presto_instruments c','b.product_id=c.id')->join('leads d','a.lead_id=d.id')->join('system_users e','e.user_id=a.request_by')->join('system_users f','f.user_id=a.sendBy')->where('a.request_by',$userid)->order_by('a.requestOn','DESC')->get();
		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			$status="Remarks: ".$row->send_remarks."<br/>Send By: ".$row->fname." ".$row->lname."<br/>Send On ".date('d-m-Y',strtotime($row->sendOn));
		
			$lead_data[] = array('sr_no'=>$i,
								 'company_name'=>$row->company_name,
								 'customer_name'=>"Primary Contact: ".$row->customer_name."<br/>Mob:".$row->contact_no."<br/><br/>Alternate Person: ".$row->alt_contact."<br/>Mob: ".$row->alt_contact_no,
								 'products'=>$row->instruments_name,
								 'remarks'=>"<strong style='color:red;font-weight:bold;'>".$row->request_remarks."</strong>",
								 'shipaddress' =>$row->postal_address."-".$row->city,
								 'raisedby' =>$row->first_name." ".$row->last_name."<br/>".date('d-M-Y H:i:s',strtotime($row->requestOn)),
								 'status' =>$status
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

	function  yourtrailrequestreport()
	{
		$this->load->view('sampling/yourtrailrequestreport');
	}
	function yourtrailrequestreport_list() {
		$lead_data = array();
		$this->db->select('f.first_name as fname,f.last_name as lname,a.sendBy,a.sendOn,a.send_remarks,a.id,e.first_name,e.last_name,c.instruments_name,a.lead_id,a.lead_product_id,a.status,a.requestOn,a.request_remarks,a.request_by,d.customer_name,d.contact_no,d.city,d.company_name,d.postal_address,d.alt_contact,d.alt_contact_no,d.title')->from('trial_to_be_sent a')->join('lead_products b','a.lead_product_id=b.id')->join('presto_instruments c','b.product_id=c.id')->join('leads d','a.lead_id=d.id')->join('system_users e','e.user_id=a.request_by')->join('system_users f','f.user_id=a.sendBy');
		if($this->uri->segment(3)){
				$this->db->where('a.request_by',$this->uri->segment(3));
		}else{

		}
		$query = $this->db->get();
		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			$status="Remarks: ".$row->send_remarks."<br/>Send By: ".$row->fname." ".$row->lname."<br/>Send On ".date('d-m-Y',strtotime($row->sendOn));

			$view_readings = '<a href="'.page_url.'Sampling/trial_readings/'.$row->id.'/'.$row->lead_id.'/t4r4i4a4l" class="btn btn-warning btn-xs">View Readings</a>';
		
			$lead_data[] = array('sr_no'=>$i,
								 'company_name'=>$row->company_name,
								 'customer_name'=>"Primary Contact: ".$row->customer_name."<br/>Mob:".$row->contact_no."<br/><br/>Alternate Person: ".$row->alt_contact."<br/>Mob: ".$row->alt_contact_no,
								 'products'=>$row->instruments_name,
								 'remarks'=>"<strong style='color:red;font-weight:bold;'>".$row->request_remarks."</strong>",
								 'shipaddress' =>$row->postal_address."-".$row->city,
								 'raisedby' =>$row->first_name." ".$row->last_name."<br/>".date('d-M-Y H:i:s',strtotime($row->requestOn)),
								 'status' =>$status,
								 'view_readings' => $view_readings 
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



	function trial_readings_list_for_old_customer() {
		$lead_data = array();
		$trial_id = $this->uri->segment(3);
		$lead_id = $this->uri->segment(4);
		$start_date = $this->uri->segment(5);
		$end_date = $this->uri->segment(6);

		$this->db->select('shift,evidencedata,id, currentdate, machine_name, water_used, oil_top_up, final_conc_1, final_conc_2, remarks')
						  ->from('trial_readings')
						  ->where('trial_id', $trial_id);
						  if($start_date<>'' && $end_date<>'')
						  {
						  	$this->db->where('currentdate>=',$start_date);
						  	$this->db->where('currentdate<=',$end_date);
						  }
					
						$query =   $this->db->get();


		if($query->num_rows()>0) {
	
			$i=1;
			foreach($query->result() as $row) {
			
			$action="<a href='javascript:;' onclick='edit_reading(".$row->id.")'><i class='fa fa-pencil-square-o'></i></a>";
		
			if($row->evidencedata<>''){
            $media = "<a href='".sfdocument."trailreadingdata/".$row->evidencedata."' download>Download Media</a>";
        }else{
            $media="";
        }

			$lead_data[] = array('sr_no' => $i,
								 'date' => date('d-m-Y', strtotime($row->currentdate)),
								 'shift'=>$row->shift,
								 'machine_name' => $row->machine_name,
								 'water_used' => $row->water_used,
								 'oil_topup' => $row->oil_top_up,
								 'final_conc_1' => $row->final_conc_1,
								 'final_conc_2' => $row->final_conc_2,
								 'remarks' => $row->remarks,
								 'action' =>$action,
								 'media' =>$media
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


	function filtertrial()
	{
		$flag1=$this->uri->segment(3);
		$flag2=$this->uri->segment(4);

		$start_date=date('Y-m-d',strtotime($this->input->post('start_date')));
		$end_date=date('Y-m-d',strtotime($this->input->post('end_date')));
		
			redirect(page_url.'Sampling/trial_readings/'.$flag1.'/'.$flag2.'/'.$start_date.'/'.$end_date);
	}

}