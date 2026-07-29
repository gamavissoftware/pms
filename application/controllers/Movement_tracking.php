<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Movement_tracking extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$session = $this->session->userdata('logged_in');
		if($session == FALSE)
		{
		
		redirect(page_url);
		
		}
$user_id =$this->session->userdata['logged_in']['user_id'];
	if(empty($user_id))
         {
         redirect(site_url(),'refresh');
         }
		$this->load->model('User_model','user');
		$this->load->model('Master_model','master');
		$config = array();  
		$config['protocol'] = 'smtp';  
		$config['smtp_host'] = 'localhost';  
		$config['smtp_user'] = 'donotreply@packingtest.com';  
		$config['smtp_pass'] = 'Presto@#21';  
		$config['smtp_port'] = 25;  
		$this->email->initialize($config);  

		$this->email->set_newline("\r\n");  
		$this->load->library('email', $config);
		$user_id =$this->session->userdata['logged_in']['user_id'];
	if(empty($user_id))
         {
         redirect(site_url(),'refresh');
         }
		 if($user_id=='66' || $user_id=='67' || $user_id=='1'){
			
		}else{ 
		$ip = $_SERVER["REMOTE_ADDR"];
		 $query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
            if($query->num_rows()=='0'){
            $this->session->set_flashdata('message','<span class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</span><br/>');
            redirect(page_url.'Dashboard');    
            }
		}
	}
	public function movement_out(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('employee_name', 'employee_name', 'required|trim');
		$this->form_validation->set_rules('material_out_date', 'material_out_date', 'required|trim');
		$this->form_validation->set_rules('out_time', 'out_time', 'required|trim');
		$this->form_validation->set_rules('to', 'to', 'required|trim');
		$this->form_validation->set_rules('job_card_number', 'job_card_number', 'required|trim');
		$this->form_validation->set_rules('weight', 'weight', 'required|trim');
		$this->form_validation->set_rules('qty', 'qty', 'required|trim');
		$this->form_validation->set_rules('challan_no', 'challan_no', 'required|trim');
		$this->form_validation->set_rules('out_from', 'out_from', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		    
			$this->load->view('movement/material_out');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		 
		   $data=
			array('material_out_date'=>date('Y-m-d',strtotime($this->input->post('material_out_date'))),
			'employee_name'=>strtoupper($this->input->post('employee_name')),
			'out_time'=>strtoupper($this->input->post('out_time')),
			'item_name'=>$this->input->post('item_name'),
			'to_location'=>$this->input->post('to'),
			'job_card_number'=>$this->input->post('job_card_number'),
			'weight'=>$this->input->post('weight'),
			'qty'=>$this->input->post('qty'),
			'challan_no'=>$this->input->post('challan_no'),
			'out_from'=>$this->input->post('out_from'),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			
			$res = $this->db->insert('movement_out_data',$data);
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
			redirect(page_url.'Movement_tracking/movement_out');
		  
			}
	}
	
	public function movement_out_list()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$scheduler_data = array();
		$query = $this->db->select('id,employee_name, material_out_date, out_time, item_name, to_location, job_card_number, weight, qty, challan_no, out_from, added_on, in_weight, in_qty, in_from, in_time, remarks')->from('movement_out_data')->order_by('added_on','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			if($row->in_weight==''){
			$movement_in = "<a href='".page_url."Movement_tracking/movement_in/".$row->id."'><span class='btn btn-success btn-xs'>Movement In</span></a>";
			
			}else{
				$movement_in="Done";
				
			}
			$inweight = $row->in_weight;
			$weight = $row->weight;
			if($inweight!==''){
			$weightdiff = $weight-$inweight;
			if($inweight==$weight){
				$receive_weight = $weightdiff;
			}else{
				$receive_weight  = "<span style='font-size:14px; color:red; font-weight:bold;'>".$weightdiff."</span>";
			}
			}else{
			$receive_weight="";
			
			}
			$qty = $row->qty;
			$inqty  =$row->in_qty;
			if($inqty!==''){
			$qtydeff = $qty-$inqty;
			if($inqty==$qty){
				$receive_qty = $qtydeff;
			}else{
				$receive_qty  = "<span style='font-size:14px; color:red; font-weight:bold;'>".$qtydeff."</span>";
				
			}
			}else{
				$receive_qty="0";
			
			}
			
			if($row->remarks==''){
			$remarks = "<a href='".page_url."Movement_tracking/auditor_remarks/".$row->id."'><span class='btn btn-success btn-xs'>Auditor Remarks</span></a>";
			}else{
			$remarks=$row->remarks;	
			}
			

			date_default_timezone_set("Asia/Kolkata");
			$added_time = date('Y-m-d H:i a',strtotime($row->added_on));
			
			$scheduler_data[] = array('sr_no'=>$i,
			'employee_name'=>$row->employee_name,
			'material_out_date'=>$row->material_out_date,
			'out_time'=>date('H:i A',strtotime($row->out_time)),
			'item_name'=>$row->item_name,
			'to_location'=>strtoupper($row->to_location),
			'job_card_number'=>strtoupper($row->job_card_number),
			'weight'=>strtoupper($row->weight),
			'qty'=>strtoupper($row->qty),
			'challan_no'=>strtoupper($row->challan_no),
			'out_from'=>strtoupper($row->out_from),
			'timestamp'=>$added_time,
			'movement_in'=>$movement_in,
			'in_weight'=>$row->in_weight,
			'in_qty'=>$inqty,
			'in_from'=>$row->in_from,
			'weightdiff'=>$receive_weight,
			'qtydeff'=>$receive_qty,
			'remarks'=>$remarks,
			'in_time'=>date('H:i A',strtotime($row->in_time)));
			$i++;
		}
		//echo "<pre>"; print_r($scheduler_data); exit;
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}
public function movement_out_dashboard(){
	$this->load->view('movement/material_out_dashboard');
}

public function movement_in(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('in_time', 'in_time', 'required|trim');
		$this->form_validation->set_rules('weight', 'weight', 'required|trim');
		$this->form_validation->set_rules('qty', 'qty', 'required|trim');
		$this->form_validation->set_rules('in_from', 'in_from', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		    
			$this->load->view('movement/material_in');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		
		   $data=
			array('in_time'=>strtoupper($this->input->post('in_time')),
			'in_weight'=>$this->input->post('weight'),
			'in_qty'=>$this->input->post('qty'),
			'in_from'=>$this->input->post('in_from'),
			'in_by'=>$user_id,
			'in_added_time'=>$added_time);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update('movement_out_data',$data);
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
			redirect(page_url.'Movement_tracking/movement_out_dashboard');
		  
			}
	}
	
public function auditor_remarks(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('remarks', 'remarks', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		    
			$this->load->view('movement/auditor_remarks');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		
		   $data=
			array(
			'remarks'=>$this->input->post('remarks'),
			'remarks_time'=>$added_time);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update('movement_out_data',$data);
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
			redirect(page_url.'Movement_tracking/movement_out_dashboard');
		  
			}
	}
	
	public function staff_movement(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('movement_date', 'movement_date', 'required|trim');
		$this->form_validation->set_rules('employee_name', 'employee_name', 'required|trim');
		$this->form_validation->set_rules('from_location', 'from_location', 'required|trim');
		$this->form_validation->set_rules('going_to', 'going_to', 'required|trim');
		$this->form_validation->set_rules('reason', 'reason', 'required|trim');
		$this->form_validation->set_rules('out_time', 'out_time', 'required|trim');
		//$this->form_validation->set_rules('in_time', 'in_time', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		    
			$this->load->view('movement/staff_movement');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		
		$travelwith= $this->input->post('travelwith');
		if($travelwith=='Own Vehicle'){
		    $bikereading=$this->input->post('bike_reading');
		}else{
		    $bikereading="";
		}
		
		   $data=
			array('movement_date'=>date('Y-m-d',strtotime($this->input->post('movement_date'))),
			'employee_name'=>strtoupper($this->input->post('employee_name')),
			'from_location'=>strtoupper($this->input->post('from_location')),
			'going_to'=>strtoupper($this->input->post('going_to')),
			'reason'=>strtoupper($this->input->post('reason')),
			'out_time'=>$this->input->post('out_time'),
			'bike_reading'=>$bikereading,
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			$res = $this->db->insert('staff_movement_tracking',$data);
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
			redirect(page_url.'Movement_tracking/staff_movement/');
		  
			}
	}
	public function staff_movement_dashboard(){
		$this->load->view('movement/staff_movement_dashboard');
	}
public function staff_movement_list()
	{
		$scheduler_data = array();
		
		
		$type = $this->uri->segment('3');
		
		$this->db->select('*')->from('staff_movement_tracking');
		if($type=='1'){
			$this->db->where('from_location!=','OUTSIDE FACTORY');
		}else if($type=='2'){
			$this->db->where('from_location','OUTSIDE FACTORY');
		}
		$query = $this->db->order_by('added_on','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		    
			if($row->status=='0'){
			    $user_id =$this->session->userdata['logged_in']['user_id'];
			    if($user_id=='144'){
				$updatestatus = "<a href='".page_url."Movement_tracking/mark_as_approved/".$row->id."' class='btn btn-success btn-xs'>Mark as Approved</a>";
			    }else{
			        $updatestatus="PENDING FOR REVIEW";
			    }
			}else{
			    $updatestatus="APPROVED";
			}
			if($row->from_location=='OUTSIDE FACTORY' && $row->status=='1'){
				$uniqueid =  $row->uniqueid;
			}else{
				$uniqueid="";
			}
			
			
			date_default_timezone_set("Asia/Kolkata");
			$added_time = date('Y-m-d H:i A',strtotime($row->added_on));
			$scheduler_data[] = array('sr_no'=>$i,
			'employee_name'=>$row->employee_name,
			'timestamp'=>$added_time,
			'movement_date'=>$row->movement_date,
			'out_time'=>date('H:i A',strtotime($row->out_time)),
			'from_location'=>$row->from_location,
			'going_to'=>strtoupper($row->going_to),
			'reason'=>strtoupper($row->reason),
			'bike_reading'=>strtoupper($row->bike_reading),
			'unique_id'=>$uniqueid,
			'update_status'=>$updatestatus);
			$i++;
		}
	
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}

public function mark_as_approved()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$field_name = "id";
		$table = "staff_movement_tracking";
		$randomnumber = "SMT-".rand(1,1000);
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		date_default_timezone_set("Asia/Kolkata");
		$added_time = date('Y-m-d H:i A',strtotime($row->added_on));
		$data = array('status'=>'1',
		'updated_on'=>$added_time,
		'uniqueid'=>$randomnumber,
		'updated_by'=>$user_id);
		$res = $this->master->update_records($table,$data,$identifier,$field_name);
		$this->session->set_flashdata('message', '<div class="alert alert-success" style="color:#000;">Status successfully updated.</div>');
		redirect('Movement_tracking/staff_movement_dashboard');
		}
public function billing_to_booking(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('month_name', 'month_name', 'required|trim');
		$this->form_validation->set_rules('target', 'target', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		    
			$this->load->view('movement/booking_to_billing');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
            $added_time = date('Y-m-d H:i:s');
			$year = date('Y');
			$data=
			array('working_year'=>$year,
			'working_month'=>$this->input->post('month_name'),
			'target'=>$this->input->post('target'));
			$res = $this->db->insert('target_billing_month',$data);
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
			redirect(page_url.'Movement_tracking/billing_to_booking');
		  }
	}
	
	public function booking_to_billing_list()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$scheduler_data = array();
		$query = $this->db->select('id,working_year, working_month, target')->from('target_billing_month')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$add_view = "<a href='".page_url."Movement_tracking/monthly_report/".$row->id."'><span class='btn btn-success btn-xs'>Add/View daily Target</span></a>";
			$scheduler_data[] = array('sr_no'=>$i,
			'working_year'=>$row->working_year,
			'working_month'=>$row->working_month,
			'target'=>$row->target,
			'add_view'=>$add_view);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}

public function monthly_report(){
	$this->load->view('movement/monthly_target');
}

public function add_daily_billing_amount(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('date', 'date', 'required|trim');
		$this->form_validation->set_rules('amount', 'amount', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		    
			$this->load->view('movement/monthly_target');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
            $added_time = date('Y-m-d H:i:s');
			$year = date('Y');
			$month = $this->uri->segment(4);
			
			$q = $this->db->select('id')->from('daily_billing_entry')->where('billing_date',$this->input->post('date'))->where('billing_month',$month)->get();
			if($q->num_rows()>0){
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000; float-left:20px;">Sorry! This record already exist.</span></div><br/>');
			redirect(page_url.'Movement_tracking/monthly_report/'.$this->uri->segment(3));
			}else{
			
			
			$data=
			array('record_id'=>$this->uri->segment(3),
			'billing_date'=>$this->input->post('date'),
			'billing_month'=>$month,
			'amount'=>$this->input->post('amount'));
			$res = $this->db->insert('daily_billing_entry',$data);
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
			redirect(page_url.'Movement_tracking/monthly_report/'.$this->uri->segment(3));
			}
			
		  }
	}
	
	public function movement_filter(){
		$this->load->view('movement/filter_staff_movement_dashboard');
	}
}