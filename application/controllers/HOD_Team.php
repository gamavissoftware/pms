<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HOD_Team extends CI_Controller {
	
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
$config['smtp_host'] = 'smtpout.secureserver.net';  
$config['smtp_user'] = 'mitr@prestomitr.com';  
$config['smtp_pass'] = 'Presto@123!@#';   
$config['smtp_port'] = 587;  
$this->email->initialize($config);  
$this->email->set_newline("\r\n");  
$this->load->library('email', $config);
	
	}

public function index()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('team_leader', 'team_leader', 'required|trim');
		$this->form_validation->set_rules('team_name', 'team_name', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		$this->load->view('hod_team/hod_list');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "presto_hod";
		$query = $this->db->select('team_name, employee_id')->from('presto_hod')->where('team_name',$this->input->post('team_name'))->where('employee_id',$this->input->post('team_leader'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','<span class="alert alert-danger" style="color:#000">Sorry! This record already exist.</span>');
			redirect(page_url.'HOD_Team');
			
		}else{
		
		
			$data = array(
			'team_name'=>$this->input->post('team_name'),
			'employee_id'=>$this->input->post('team_leader'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->db->insert($table,$data);	
		if($result)
		{
		$this->session->set_flashdata('message','<span class="alert alert-success" style="color:#000">Thank you, record successfully added.</span>');
			redirect(page_url.'HOD_Team');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info" style="color:#000">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'HOD_Team');
		}
		}
		
	}
		
	}

public function hod_list()
	{
		$team_data = array();
		$i=1;
		$this->db->select('a.id, a.team_name, a.employee_id, b.first_name, b.last_name')->from('presto_hod a');
		$this->db->join('system_users b','a.employee_id=b.user_id','left');
		$this->db->order_by('a.team_name','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			
			$add = "<a href='".page_url."HOD_Team/add_employees/".$row->id."'><span class='btn btn-success btn-xs'>Add Employees in Team</span></a>";			
			$edit = "<a href='".page_url."HOD_Team/edit_hod/".$row->id."'><i class='fa fa-pencil'></i></a>";
			
			
			$html = "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center; width:300px'>EMPLOYEE NAME</th><th style='padding:2px 2px 2px 2px; text-align:center; width:200px'>EMPLOYEE CODE <a href='".page_url."HOD_Team/edit_team_members/".$row->id."'><i class='fa fa-pencil-square-o'></i></a></th></tr>";
			$instrumentsss = array();
			$query = $this->db->select('id,employee_name, employee_code')->from('hod_team_members')->where('team_id',$row->id)->get();
			foreach($query->result() as $record){
				$delete = "<a href='".page_url."/HOD_Team/delete_employee/".$record->id."'><i class='fa fa-trash'></i></a>";
				
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->employee_name)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->employee_code)." &nbsp; &nbsp; &nbsp;".$delete."</td>";
				
				$html.="</tr>";
				
			}
			$html.="</table>";
			
			
			$team_data[] = array('sr_no'=>$i,
			'team_leader'=>$row->first_name." ".$row->last_name,
			'team_name'=>$row->team_name,
			'add'=>$add,
			'edit'=>$edit,
			'employee_list'=>$html);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($team_data),
			"iTotalDisplayRecords" => count($team_data),
			"aaData"=>$team_data);
			
		echo json_encode($results);
	}
	
	
	
	public function edit_hod(){
		$this->load->view('hod_team/edit_hod');
	}
	public function update_hod_info()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('team_leader', 'team_leader', 'required|trim');
		$this->form_validation->set_rules('team_name', 'team_name', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		$this->load->view('hod_team/edit_hod');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "presto_hod";
		$query = $this->db->select('team_name, employee_id')->from('presto_hod')->where('team_name',$this->input->post('team_name'))->where('employee_id',$this->input->post('team_leader'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','<span class="alert alert-danger" style="color:#000">Sorry! This record already exist.</span>');
			redirect(page_url.'HOD_Team');
			
		}else{
		
		$data = array(
			'team_name'=>$this->input->post('team_name'),
			'employee_id'=>$this->input->post('team_leader'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			$this->db->where('id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
		$this->session->set_flashdata('message','<span class="alert alert-success" style="color:#000">Thank you, record successfully added.</span>');
			redirect(page_url.'HOD_Team');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info" style="color:#000">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'HOD_Team');
		}
		}
		
	}
		
	}
	
	public function add_employees(){
		$this->load->view('hod_team/add_employees');
	}
	
	public function add(){
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
           $table = "hod_team_members";
			if(isset($_REQUEST['employee_name'])){	
					$tags1=count($_REQUEST['employee_name']);
					if($tags1>0)
					{
					$employee_name=$_REQUEST['employee_name'];
					$employee_code=$_REQUEST['employee_code'];
					for($x=0;$x<$tags1;$x++){
					if($employee_name[$x]!='')
						{
						   
							$data=array('employee_name'=>$employee_name[$x],
							'employee_code'=>$employee_code[$x],
							'team_id'=>$this->uri->segment(3),
							'added_on'=>$added_time,
							'added_by'=>$user_id);
							$this->db->insert($table,$data);
						}
					}
					}
					}
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'HOD_Team');
				
	}
	
	public function HOD_panel(){
		$this->load->view('hod_team/hod_panel');
	}
	
	public function update_schedule(){
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
           $table = "hod_team_employee_schedule";
			if(isset($_REQUEST['employee_name'])){	
					$tags1=count($_REQUEST['employee_name']);
					if($tags1>0)
					{
					$recordid=$_REQUEST['recordid'];
					$employee_name=$_REQUEST['employee_name'];
					$employee_code=$_REQUEST['employee_code'];
					$scheduled_date= date('Y-m-d',strtotime($this->input->post('schedule_date')));
					for($x=0;$x<$tags1;$x++){
					if($employee_name[$x]!='')
						{
							$attendance=$_REQUEST['attendance'.$x];
							$data=array('employee_name'=>$employee_name[$x],
							'employee_code'=>$employee_code[$x],
							'team_id'=>$this->uri->segment(3),
							'scheduled_date'=>$scheduled_date,
							'record_id'=>$recordid[$x],
							'status'=>$attendance,
							'added_on'=>$added_time,
							'added_by'=>$user_id);
							$this->db->insert($table,$data);
						}
					}
					}
					}
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'HOD_Team/HOD_panel');
				
	}
	
	public function team_report(){
		$this->load->view('hod_team/tomorrow_report');
	}
	public function hod_team_report_list()
	{
		$team_data = array();
		$i=1;
		$nextdate = date('Y-m-d', strtotime(' +1 day'));
		$this->db->select('a.id, a.team_name, a.employee_id, b.first_name, b.last_name')->from('presto_hod a');
		$this->db->join('system_users b','a.employee_id=b.user_id','left');
		$this->db->join('hod_team_employee_schedule c','a.id=c.team_id','left');
		$this->db->where('c.scheduled_date',$nextdate);
		$this->db->group_by('c.team_id');
		$this->db->order_by('a.team_name','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			
			$html = "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='width:100px'>DATE</th><th style='padding:2px 2px 2px 2px; text-align:center; width:200px'>EMPLOYEE NAME</th><th style='padding:2px 2px 2px 2px; text-align:center; width:100px'>EMPLOYEE CODE</th><th style='padding:2px 2px 2px 2px; text-align:center; width:50px'>YES</th><th style='padding:2px 2px 2px 2px; text-align:center; width:50px'>NO</th></tr>";
			$instrumentsss = array();
			$query = $this->db->select('scheduled_date, employee_name, employee_code, status')->from('hod_team_employee_schedule')->where('team_id',$row->id)->where('scheduled_date',$nextdate)->get();
			foreach($query->result() as $record){
				
				if($record->status=='1'){
					$yes = "<i class='fa fa-check-square-o' style='font-size:23px;'></i>";
					$no="";
				}else{
					$yes="";
					$no = "<i class='fa fa-check-square-o' style='font-size:23px;'></i>";
				}
				
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".date('d-m-Y',strtotime($record->scheduled_date))."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->employee_name)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->employee_code)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$yes."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$no."</td>";
				$html.="</tr>";
				
			}
			$html.="</table>";
			
			
			$team_data[] = array('sr_no'=>$i,
			'team_leader'=>$row->first_name." ".$row->last_name,
			'team_name'=>$row->team_name,
			'employee_list'=>$html);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($team_data),
			"iTotalDisplayRecords" => count($team_data),
			"aaData"=>$team_data);
			
		echo json_encode($results);
	}
	
	function edit_team_members(){
	    $this->load->view('hod_team/edit_team_members');
	}
	
	public function update_Employee_detail(){
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
           $table = "hod_team_members";
			if(isset($_REQUEST['employee_name'])){	
					$tags1=count($_REQUEST['employee_name']);
					if($tags1>0)
					{
					$employee_name=$_REQUEST['employee_name'];
					$employee_code=$_REQUEST['employee_code'];
					$recordid = $_REQUEST['record_id'];
					for($x=0;$x<$tags1;$x++){
					if($employee_name[$x]!='')
						{
						   
							$data=array('employee_name'=>$employee_name[$x],
							'employee_code'=>$employee_code[$x],
							'added_on'=>$added_time,
							'added_by'=>$user_id);
							$this->db->where('id',$recordid[$x]);
							$this->db->update($table,$data);
						}
					}
					}
					}
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'HOD_Team');
				
	}
	
	public function team_plan_history(){
	    $this->load->view('hod_team/team_plan_history');
	}
	public function hod_team_report_list_history()
	{
		$team_data = array();
		$i=1;
		$nextdate = date('Y-m-d', strtotime(' +1 day'));
	$this->db->select('a.id,a.team_id, a.scheduled_date,b.team_name, b.employee_id,c.first_name, c.last_name')->from('hod_team_employee_schedule a')->join('presto_hod b','a.team_id=b.id','left')->join('system_users c','b.employee_id=c.user_id','left')->group_by('a.team_id')->group_by('a.scheduled_date')->order_by('a.scheduled_date','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			
			$html = "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='width:100px'>DATE</th><th style='padding:2px 2px 2px 2px; text-align:center; width:200px'>EMPLOYEE NAME</th><th style='padding:2px 2px 2px 2px; text-align:center; width:100px'>EMPLOYEE CODE</th><th style='padding:2px 2px 2px 2px; text-align:center; width:50px'>YES</th><th style='padding:2px 2px 2px 2px; text-align:center; width:50px'>NO</th></tr>";
			$instrumentsss = array();
			$query = $this->db->select('scheduled_date, employee_name, employee_code, status')->from('hod_team_employee_schedule')->where('team_id',$row->team_id)->where('scheduled_date',$row->scheduled_date)->get();
			foreach($query->result() as $record){
				
				if($record->status=='1'){
					$yes = "<i class='fa fa-check-square-o' style='font-size:23px;'></i>";
					$no="";
				}else{
					$yes="";
					$no = "<i class='fa fa-check-square-o' style='font-size:23px;'></i>";
				}
				
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".date('d-m-Y',strtotime($record->scheduled_date))."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->employee_name)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->employee_code)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$yes."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$no."</td>";
				$html.="</tr>";
				
			}
			$html.="</table>";
			
			
			$team_data[] = array('sr_no'=>$i,
			'team_leader'=>$row->first_name." ".$row->last_name,
			'team_name'=>$row->team_name,
			'employee_list'=>$html);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($team_data),
			"iTotalDisplayRecords" => count($team_data),
			"aaData"=>$team_data);
			
		echo json_encode($results);
	}
	
	function delete_employee(){
	    $id = $this->uri->segment(3);
	    $this->db->where('id',$id);
	    $this->db->delete('hod_team_members');
	    $this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully deleted.</span><br/>');
				redirect(page_url.'HOD_Team');
	    
	}
	
}