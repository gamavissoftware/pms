<?php
defined('whatsappuser') OR define('whatsappuser','sundarindustrial');
defined('whatsapppass') OR define('whatsapppass','HPCLsundar@42I');
ini_set('serialize_precision','-1');
defined('BASEPATH') OR exit('No direct script access allowed');

class Trail extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('Salescrm_model','salescrm');
				
	}

	public function index(){
		$this->load->view('trail/trailform');
	}

	public function savedata(){
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('Customer', 'Customer', 'required|trim');
		$this->form_validation->set_rules('visitschedule', 'Visit Schedule', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		    $this->load->view('trail/trailform');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		  	$customer_id = $this->input->post('Customer');
		  	$assignedperson = $this->input->post('assignedperson');
		  	$visitschedule = $this->input->post('visitschedule');


		   	$table = "trial_to_be_sent";
			if(isset($_REQUEST['product'])){	
					$tags1=count($_REQUEST['product']);
					if($tags1>0)
					{
					$product=$_REQUEST['product'];
					$remarks = $_REQUEST['remarks'];
					
					$i=1;
					for($x=0;$x<$tags1;$x++){
					if($product[$x]!='')
						{
							
							$data=array('lead_id'=>0,
							'customer_id'=>$customer_id,
							'assignedperson'=>$assignedperson,
							'lead_product_id'=>$product[$x],
							'request_by'=>$user_id,
							'requestOn'=>$added_time,
							'visitdays'=>$visitschedule ,
							'request_remarks'=>$remarks[$x]);
							$this->db->insert($table,$data);
							$id = $this->db->insert_id();
							$Date = date('Y-m-d');
							$scheduledate = date('Y-m-d', strtotime($Date. ' + '.$visitschedule.' days'));


							$data1 = array('trail_id'=>$id,
								'visitschedule'=>$scheduledate,
								'added_by'=>$user_id,
								'added_on'=>$added_time,
								'status'=>0);

							$this->db->insert('old_customer_visit_schedule',$data1);
							
						}
					$i++;	
					}
					}
					}
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'Trail');
			
		}
		
		
				
	}

	public function trial_readings()
	{
		$this->load->view('sampling/old_customer_trial_readings');
	} 


	function trial_request_history_list() {
		$lead_data = array();
		$this->db->select('a.id, e.first_name, e.last_name, c.instruments_name, a.lead_id, a.lead_product_id, a.status, a.requestOn, a.request_remarks, a.request_by, c.trial_reading, d.title, d.customer_name, d.company_name, d.ship_address, d.contact_no, d.alt_contact, d.city, a.send_remarks, a.sendOn, f.first_name as fname, f.last_name as lname')->from('trial_to_be_sent a')->join('lead_products b','a.lead_product_id=b.id')->join('presto_instruments c','b.product_id=c.id')->join('system_users e','e.user_id=a.request_by')->join('customer_detail d','a.customer_id=d.id','left')->join('system_users f','f.user_id=a.sendBy')->where('a.status',1)->where('a.lead_id',0);
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
			
			$status="Remarks: ".$row->send_remarks."<br/>Send By: ".$row->fname." ".$row->lname."<br/>Send On ".date('d-m-Y',strtotime($row->sendOn));

			$view_readings = '<a href="'.page_url.'Trail/trial_readings/'.$row->id.'/'.$row->lead_id.'/t4r4i4a4l" class="btn btn-warning btn-xs">View Readings</a>';
		
			$lead_data[] = array('sr_no'=>$i,
				 				'company_name'=>$row->company_name,
								 'customer_name'=>"Primary Contact: ".$row->customer_name."<br/>Mob:".$row->contact_no."<br/><br/>Alternate Contact Number: ".$row->alt_contact,
								 'products'=>$row->instruments_name,
								 'remarks'=>"<strong style='color:red;font-weight:bold;'>".$row->request_remarks."</strong>",
								 'shipaddress' =>$row->ship_address."-".$row->city,
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

	public function getassignedpersondetail(){
		$customerid = $this->input->post('customername');
		$q = $this->db->select('b.first_name, b.last_name, b.user_id')->from('customer_detail a')->join('system_users b','a.assign_customer_for_trail=b.user_id','left')->where('id',$customerid)->get();
		foreach($q->result() as $row);
		echo "<option value='".$row->user_id."'>".$row->first_name." ".$row->last_name."</option>"; exit;

	}

	function trial_request_for_assign() {
		$lead_data = array();
		$this->db->select('a.id, e.first_name, e.last_name, c.instruments_name, a.lead_id, a.lead_product_id, a.status, a.requestOn, a.request_remarks, a.request_by, c.trial_reading, d.customer_name, d.contact_no, d.city, d.company_name, d.postal_address,d.alt_contact,d.alt_contact_no,d.title, a.assignedperson')->from('trial_to_be_sent a')->join('lead_products b','a.lead_product_id=b.id')->join('presto_instruments c','b.product_id=c.id')->join('leads d','a.lead_id=d.id')->join('system_users e','e.user_id=a.request_by')->where('a.assignedperson',0);
		
		if($this->uri->segment(3)){
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

			$q11 = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('department_id',7)->where('user_status',1)->get();

			$confirmation = '<button class="btn btn-success btn-xs waves-effect waves-light" onclick="trailapprovalbox('.$row->id.');">APPROVE</button>';

		

			$trailassign = "<select name='employeename".$i."' id='employeename".$i."' class='form-control' onChange='assign_to_trail(".$row->id.",".$i.")'>";
				$trailassign.="<option value=''>Select User to Assign</option>";

				foreach($q11->result() as $rowssss){
					if($row->assignedperson==$rowssss->user_id){
						$selected = "selected";
					}else{
						$selected = "";
					}
			$trailassign.="<option value='".$rowssss->user_id."' ".$selected.">".$rowssss->first_name." " .$rowssss->last_name."</option>";  
				}

			$trailassign.="</select><div style='color:red; font-weight:bold;' id='datasuccess".$row->id."'></div>";



		
			$lead_data[] = array('sr_no'=>$i,
								 'company_name'=>$row->company_name,
								 'customer_name'=>"Primary Contact: ".$row->customer_name."<br/>Mob:".$row->contact_no."<br/><br/>Alternate Person: ".$row->alt_contact."<br/>Mob: ".$row->alt_contact_no,
								 'products'=>$row->instruments_name,
								 'remarks'=>"<strong style='color:red;font-weight:bold;'>".$row->request_remarks."</strong>",
								 'shipaddress' =>$row->postal_address."-".$row->city,
								 'raisedby' =>$row->first_name." ".$row->last_name."<br/>".date('d-M-Y H:i:s',strtotime($row->requestOn)),
								 'status' =>$confirmation
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

	function yourtrailrequestreport_list() {
		$lead_data = array();
		$this->db->select('a.id, e.first_name, e.last_name, c.instruments_name, a.lead_id, a.lead_product_id, a.status, a.requestOn, a.request_remarks, a.request_by, c.trial_reading, d.title, d.customer_name, d.company_name, d.ship_address, d.contact_no, d.alt_contact, d.city, a.send_remarks, a.sendOn, f.first_name as fname, f.last_name as lname')->from('trial_to_be_sent a')->join('lead_products b','a.lead_product_id=b.id')->join('presto_instruments c','b.product_id=c.id')->join('system_users e','e.user_id=a.request_by')->join('customer_detail d','a.customer_id=d.id','left')->join('system_users f','f.user_id=a.sendBy')->where('a.lead_id',0);
		if($this->uri->segment(3)){
			$this->db->where('a.request_by',$this->uri->segment(3));
		}
		$query = $this->db->get();
		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			$status="Remarks: ".$row->send_remarks."<br/>Send By: ".$row->fname." ".$row->lname."<br/>Send On ".date('d-m-Y',strtotime($row->sendOn));

			$view_readings = '<a href="'.page_url.'Trail/trial_readings/'.$row->id.'/'.$row->lead_id.'/t4r4i4a4l" class="btn btn-warning btn-xs">View Readings</a>';
		
			$lead_data[] = array('sr_no'=>$i,
				 				'company_name'=>$row->company_name,
								 'customer_name'=>"Primary Contact: ".$row->customer_name."<br/>Mob:".$row->contact_no."<br/><br/>Alternate Contact Number: ".$row->alt_contact,
								 'products'=>$row->instruments_name,
								 'remarks'=>"<strong style='color:red;font-weight:bold;'>".$row->request_remarks."</strong>",
								 'shipaddress' =>$row->ship_address."-".$row->city,
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

	function filtertrial()
	{
		$flag1=$this->uri->segment(3);
		$flag2=$this->uri->segment(4);

		$start_date=date('Y-m-d',strtotime($this->input->post('start_date')));
		$end_date=date('Y-m-d',strtotime($this->input->post('end_date')));
		
			redirect(page_url.'Trail/trial_readings/'.$flag1.'/'.$flag2.'/'.$start_date.'/'.$end_date);
	}
}