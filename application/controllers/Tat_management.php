<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tat_management extends CI_Controller {
	
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
	$ip = $_SERVER["REMOTE_ADDR"];
		 	
	}

	
	
	public function daily_tat_list()
	{
		$daily_data = array();
		$this->db->select('*')->from('daily_tat');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Tat_management/update_daily_tat_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Tat_management/update_daily_tat_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
				$edit = "<a href='".page_url."Tat_management/edit_daily_tat/".$row->id."'><i class='fa fa-pencil'></i></a>";
			
			$daily_data[] = array('sr_no'=>$i,
			'timing'=>$row->timing,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
		//echo "<pre>"; print_r($business_data); exit;
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($daily_data),
			"iTotalDisplayRecords" => count($daily_data),
			"aaData"=>$daily_data);
			
		echo json_encode($results);
	}
	
	
	public function add_daily_tat()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('tat_time', 'TAT time', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/TAT/daily_tat');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "daily_tat";
		$query = $this->db->select('timing')->from('daily_tat')->where('timing',$this->input->post('tat_time'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','Sorry,This record already exist.');
			redirect(page_url.'Tat_management/add_daily_tat/'.$this->uri->segment(3));
		}else{
			$data = array('timing'=>$this->input->post('tat_time'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Tat_management/add_daily_tat/'.$this->uri->segment(3));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tat_management/add_daily_tat/'.$this->uri->segment(3));
		}
		}
		
	}
		
	}
	
	public function update_daily_tat_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "id";
		$table = "daily_tat";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', 'Status successfully updated.');
			redirect('Tat_management/add_daily_tat/');
		}
	
	public function edit_daily_tat()
	{
		$this->load->view('audit_report/TAT/edit_daily_tat');
		
	}
	
	public function update_daily_tat()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('timing', 'Timing', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/TAT/edit_daily_tat');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "daily_tat";	
		$identifier = $this->uri->segment(3);
		$field_name = "id";		
		$data = array('timing'=>$this->input->post('timing'),
			'status'=>$this->input->post('status'),
			'added_by'=>$user_id);
		$result  = $this->master->update_records($table,$data,$identifier,$field_name);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'Tat_management/add_daily_tat');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tat_management/add_daily_tat');
			
		}
			
	}
		
	}
	
	public function twice_in_a_week_list()
	{
		$tat_data = array();
		$this->db->select('*')->from('twice_in_a_week_tat');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Tat_management/update_twice_in_a_week_tat_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Tat_management/update_twice_in_a_week_tat_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
				$edit = "<a href='".page_url."Tat_management/edit_twice_in_a_week_tat/".$row->id."'><i class='fa fa-pencil'></i></a>";
			
			$tat_data[] = array('sr_no'=>$i,
			'first_day'=>$row->first_day,
			'second_day'=>$row->second_day,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
		//echo "<pre>"; print_r($business_data); exit;
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($tat_data),
			"iTotalDisplayRecords" => count($tat_data),
			"aaData"=>$tat_data);
			
		echo json_encode($results);
	}
	
	
	public function add_twice_in_a_week_tat()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('first_day', 'First Day', 'required|trim');
		$this->form_validation->set_rules('second_day', 'Second Day', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/TAT/add_twice_in_a_week_tat');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "twice_in_a_week_tat";
		$query = $this->db->select('first_day,second_day')->from('twice_in_a_week_tat')->where('first_day',$this->input->post('first_day'))->where('second_day',$this->input->post('second_day'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','Sorry,This record already exist.');
			redirect(page_url.'Tat_management/add_twice_in_a_week_tat/'.$this->uri->segment(3));
		}else{
			$data = array('first_day'=>$this->input->post('first_day'),
			'second_day'=>$this->input->post('second_day'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Tat_management/add_twice_in_a_week_tat/'.$this->uri->segment(3));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tat_management/add_twice_in_a_week_tat/'.$this->uri->segment(3));
		}
		}
		
	}
		
	}
	
	public function update_twice_in_a_week_tat_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "id";
		$table = "twice_in_a_week_tat";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', 'Status successfully updated.');
			redirect('Tat_management/add_twice_in_a_week_tat/');
		}
	
	public function edit_twice_in_a_week_tat()
	{
		$this->load->view('audit_report/TAT/edit_twice_in_a_week_tat');
		
	}
	
	public function update_twice_in_a_week_tat()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('first_day', 'First Day', 'required|trim');
		$this->form_validation->set_rules('second_day', 'Second Day', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/TAT/edit_twice_in_a_week_tat');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "twice_in_a_week_tat";	
		$identifier = $this->uri->segment(3);
		$field_name = "id";		
		$data = array('first_day'=>$this->input->post('first_day'),
			'second_day'=>$this->input->post('second_day'),
			'status'=>$this->input->post('status'),
			'added_by'=>$user_id);
		$result  = $this->master->update_records($table,$data,$identifier,$field_name);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'Tat_management/add_twice_in_a_week_tat');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tat_management/add_twice_in_a_week_tat');
			
		}
			
	}
		
	}
	
	public function weekly_tat_list()
	{
		$tat_data = array();
		$this->db->select('*')->from('weekly_tat');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Tat_management/update_weekly_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Tat_management/update_weekly_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
				$edit = "<a href='".page_url."Tat_management/edit_weekly_tat/".$row->id."'><i class='fa fa-pencil'></i></a>";
			
			$tat_data[] = array('sr_no'=>$i,
			'weekday'=>$row->weekday,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
		//echo "<pre>"; print_r($business_data); exit;
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($tat_data),
			"iTotalDisplayRecords" => count($tat_data),
			"aaData"=>$tat_data);
			
		echo json_encode($results);
	}
	
	
	public function add_weekly_tat()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('weekday', 'Week Day', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/TAT/add_weekly_tat');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "weekly_tat";
		$query = $this->db->select('weekday')->from('weekly_tat')->where('weekday',$this->input->post('weekday'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','Sorry,This record already exist.');
			redirect(page_url.'Tat_management/add_weekly_tat/'.$this->uri->segment(3));
		}else{
			$data = array('weekday'=>$this->input->post('weekday'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Tat_management/add_weekly_tat/'.$this->uri->segment(3));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tat_management/add_weekly_tat/'.$this->uri->segment(3));
		}
		}
		
	}
		
	}
	
	public function update_weekly_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "id";
		$table = "weekly_tat";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', 'Status successfully updated.');
			redirect('Tat_management/add_weekly_tat/');
		}
	
	public function edit_weekly_tat()
	{
		$this->load->view('audit_report/TAT/edit_weekly_tat');
		
	}
	
	public function update_weekly_tat()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('weekday', 'Day', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/TAT/edit_weekly_tat');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "weekly_tat";	
		$identifier = $this->uri->segment(3);
		$field_name = "id";		
		$data = array('weekday'=>$this->input->post('weekday'),
			'status'=>$this->input->post('status'),
			'added_by'=>$user_id);
		$result  = $this->master->update_records($table,$data,$identifier,$field_name);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'Tat_management/add_weekly_tat');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tat_management/add_weekly_tat');
			
		}
			
	}
		
	}
	
	public function twice_in_a_month_list()
	{
		$tat_data = array();
		$this->db->select('*')->from('twice_in_a_month_tat');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Tat_management/update_twice_in_a_month_tat_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Tat_management/update_twice_in_a_month_tat_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
				$edit = "<a href='".page_url."Tat_management/edit_twice_in_a_month_tat/".$row->id."'><i class='fa fa-pencil'></i></a>";
			
			$tat_data[] = array('sr_no'=>$i,
			'first_date'=>$row->first_date,
			'second_date'=>$row->second_date,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($tat_data),
			"iTotalDisplayRecords" => count($tat_data),
			"aaData"=>$tat_data);
			
		echo json_encode($results);
	}
	
	
	public function add_twice_in_a_month_tat()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('first_date', 'First Date', 'required|trim');
		$this->form_validation->set_rules('second_date', 'Second Date', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/TAT/add_twice_in_a_month_tat');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "twice_in_a_month_tat";
		$query = $this->db->select('first_date,second_date')->from('twice_in_a_month_tat')->where('first_date',$this->input->post('first_date'))->where('second_date',$this->input->post('second_date'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','Sorry,This record already exist.');
			redirect(page_url.'Tat_management/add_twice_in_a_month_tat/'.$this->uri->segment(3));
		}else{
			$data = array('first_date'=>$this->input->post('first_date'),
			'second_date'=>$this->input->post('second_date'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Tat_management/add_twice_in_a_month_tat/'.$this->uri->segment(3));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tat_management/add_twice_in_a_month_tat/'.$this->uri->segment(3));
		}
		}
		
	}
		
	}
	
	public function update_twice_in_a_month_tat_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "id";
		$table = "twice_in_a_month_tat";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', 'Status successfully updated.');
			redirect('Tat_management/add_twice_in_a_month_tat/');
		}
	
	public function edit_twice_in_a_month_tat()
	{
		$this->load->view('audit_report/TAT/edit_twice_in_a_month_tat');
		
	}
	
	public function update_twice_in_a_month_tat()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('first_date', 'First Date', 'required|trim');
		$this->form_validation->set_rules('second_date', 'Second Date', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/TAT/edit_twice_in_a_month_tat');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "twice_in_a_month_tat";	
		$identifier = $this->uri->segment(3);
		$field_name = "id";		
		$data = array('first_date'=>$this->input->post('first_date'),
			'second_date'=>$this->input->post('second_date'),
			'status'=>$this->input->post('status'),
			'added_by'=>$user_id);
		$result  = $this->master->update_records($table,$data,$identifier,$field_name);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'Tat_management/add_twice_in_a_month_tat');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tat_management/add_twice_in_a_month_tat');
			
		}
			
	}
		
	}
	
	public function monthly_tat_list()
	{
		$tat_data = array();
		$this->db->select('*')->from('monthly_tat');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Tat_management/update_monthly_tat_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Tat_management/update_monthly_tat_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
				$edit = "<a href='".page_url."Tat_management/edit_monthly_tat/".$row->id."'><i class='fa fa-pencil'></i></a>";
			
			$tat_data[] = array('sr_no'=>$i,
			'monthly'=>$row->monthly,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($tat_data),
			"iTotalDisplayRecords" => count($tat_data),
			"aaData"=>$tat_data);
			
		echo json_encode($results);
	}
	
	
	public function add_monthly_tat()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('monthly', 'Date', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/TAT/add_monthly_tat');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "monthly_tat";
		$query = $this->db->select('monthly')->from('monthly_tat')->where('monthly',$this->input->post('monthly'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','Sorry,This record already exist.');
			redirect(page_url.'Tat_management/add_monthly_tat/'.$this->uri->segment(3));
		}else{
			$data = array('monthly'=>$this->input->post('monthly'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Tat_management/add_monthly_tat/'.$this->uri->segment(3));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tat_management/add_monthly_tat/'.$this->uri->segment(3));
		}
		}
		
	}
		
	}
	
	public function update_monthly_tat_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "id";
		$table = "monthly_tat";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', 'Status successfully updated.');
			redirect('Tat_management/add_monthly_tat/');
		}
	
	public function edit_monthly_tat()
	{
		$this->load->view('audit_report/TAT/edit_monthly_tat');
		
	}
	
	public function update_monthly_tat()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('monthly', 'Date', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/TAT/edit_monthly_tat');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "monthly_tat";	
		$identifier = $this->uri->segment(3);
		$field_name = "id";		
		$data = array('monthly'=>$this->input->post('monthly'),
			'status'=>$this->input->post('status'),
			'added_by'=>$user_id);
		$result  = $this->master->update_records($table,$data,$identifier,$field_name);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'Tat_management/add_monthly_tat');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tat_management/add_monthly_tat');
			
		}
		}
	}
	public function yearly_tat_list()
	{
		$tat_data = array();
		$this->db->select('*')->from('yearly_tat');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Tat_management/update_yearly_tat_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Tat_management/update_yearly_tat_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
				$edit = "<a href='".page_url."Tat_management/edit_yearly_tat/".$row->id."'><i class='fa fa-pencil'></i></a>";
			
			$tat_data[] = array('sr_no'=>$i,
			'month'=>$row->yearly,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($tat_data),
			"iTotalDisplayRecords" => count($tat_data),
			"aaData"=>$tat_data);
			
		echo json_encode($results);
	}
	
	
	public function add_yearly_tat()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('month', 'Month', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/TAT/add_yearly_tat');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "yearly_tat";
		$query = $this->db->select('yearly')->from('yearly_tat')->where('yearly',$this->input->post('month'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','Sorry,This record already exist.');
			redirect(page_url.'Tat_management/add_yearly_tat/'.$this->uri->segment(3));
		}else{
			$data = array('yearly'=>$this->input->post('month'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Tat_management/add_yearly_tat/'.$this->uri->segment(3));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tat_management/add_yearly_tat/'.$this->uri->segment(3));
		}
		}
		
	}
		
	}
	
	public function update_yearly_tat_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "id";
		$table = "yearly_tat";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', 'Status successfully updated.');
			redirect('Tat_management/add_yearly_tat/');
		}
	
	public function edit_yearly_tat()
	{
		$this->load->view('audit_report/TAT/edit_yearly_tat');
		
	}
	
	public function update_yearly_tat()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('month', 'Month', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/TAT/edit_yearly_tat');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "yearly_tat";	
		$identifier = $this->uri->segment(3);
		$field_name = "id";		
		$data = array('yearly'=>$this->input->post('month'),
			'status'=>$this->input->post('status'),
			'added_by'=>$user_id);
		$result  = $this->master->update_records($table,$data,$identifier,$field_name);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'Tat_management/add_yearly_tat');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tat_management/add_yearly_tat');
			
		}
		
	}
		
	}
	
	public function quarterly_tat_list()
	{
		$tat_data = array();
		$this->db->select('*')->from('quarterly_tat');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Tat_management/update_quarterly_tat_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Tat_management/update_quarterly_tat_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
				$edit = "<a href='".page_url."Tat_management/edit_quarterly_tat/".$row->id."'><i class='fa fa-pencil'></i></a>";
			
			$tat_data[] = array('sr_no'=>$i,
			'month'=>$row->month,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($tat_data),
			"iTotalDisplayRecords" => count($tat_data),
			"aaData"=>$tat_data);
			
		echo json_encode($results);
	}
	
	
	public function add_quarterly_tat()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('month', 'Month', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/TAT/add_quarterly_tat');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "quarterly_tat";
		$query = $this->db->select('month')->from('quarterly_tat')->where('month',$this->input->post('month'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','Sorry,This record already exist.');
			redirect(page_url.'Tat_management/add_quarterly_tat/'.$this->uri->segment(3));
		}else{
			$data = array('month'=>$this->input->post('month'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Tat_management/add_quarterly_tat/'.$this->uri->segment(3));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tat_management/add_quarterly_tat/'.$this->uri->segment(3));
		}
		}
		
	}
		
	}
	
	public function update_quarterly_tat_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "id";
		$table = "quarterly_tat";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', 'Status successfully updated.');
			redirect('Tat_management/add_quarterly_tat/');
		}
	
	public function edit_quarterly_tat()
	{
		$this->load->view('audit_report/TAT/edit_quarterly_tat');
		
	}
	
	public function update_quarterly_tat()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('month', 'Month', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/TAT/edit_quarterly_tat');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "quarterly_tat";	
		$identifier = $this->uri->segment(3);
		$field_name = "id";		
		$data = array('month'=>$this->input->post('month'),
			'status'=>$this->input->post('status'),
			'added_by'=>$user_id);
		$result  = $this->master->update_records($table,$data,$identifier,$field_name);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'Tat_management/add_quarterly_tat');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tat_management/add_quarterly_tat');
			
		}
		
	}
		
	}

	public function twice_in_a_year_list()
	{
		$tat_data = array();
		$this->db->select('*')->from('twice_in_a_year_tat');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Tat_management/update_twice_in_a_year_tat_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Tat_management/update_twice_in_a_year_tat_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
				$edit = "<a href='".page_url."Tat_management/edit_twice_in_a_year_tat/".$row->id."'><i class='fa fa-pencil'></i></a>";
			
			$tat_data[] = array('sr_no'=>$i,
			'first_month'=>$row->first_month,
			'second_month'=>$row->second_month,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($tat_data),
			"iTotalDisplayRecords" => count($tat_data),
			"aaData"=>$tat_data);
			
		echo json_encode($results);
	}
	
	
	public function add_twice_in_a_year_tat()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('first_month', 'Month', 'required|trim');
		$this->form_validation->set_rules('second_month', 'Month', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/TAT/add_twice_in_a_year');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "twice_in_a_year_tat";
		$query = $this->db->select('first_month,second_month')->from('twice_in_a_year_tat')->where('first_month',$this->input->post('first_month'))->where('second_month',$this->input->post('second_month'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','Sorry,This record already exist.');
			redirect(page_url.'Tat_management/add_twice_in_a_year_tat/'.$this->uri->segment(3));
		}else{
			$data = array('first_month'=>$this->input->post('first_month'),
			'second_month'=>$this->input->post('second_month'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Tat_management/add_twice_in_a_year_tat/'.$this->uri->segment(3));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tat_management/add_twice_in_a_year_tat/'.$this->uri->segment(3));
		}
		}
		
	}
		
	}
	
	public function update_twice_in_a_year_tat_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "id";
		$table = "twice_in_a_year_tat";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', 'Status successfully updated.');
			redirect('Tat_management/add_twice_in_a_year_tat/');
		}
	
	public function edit_twice_in_a_year_tat()
	{
		$this->load->view('audit_report/TAT/edit_twice_in_a_year');
		
	}
	
	public function update_twice_in_a_year_tat()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('first_month', 'Month', 'required|trim');
		$this->form_validation->set_rules('second_month', 'Month', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/TAT/edit_twice_in_a_year');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "twice_in_a_year_tat";	
		$identifier = $this->uri->segment(3);
		$field_name = "id";		
		$data = array('first_month'=>$this->input->post('first_month'),
			'second_month'=>$this->input->post('second_month'),
			'status'=>$this->input->post('status'),
			'added_by'=>$user_id);
		$result  = $this->master->update_records($table,$data,$identifier,$field_name);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'Tat_management/add_twice_in_a_year_tat');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tat_management/add_twice_in_a_year_tat');
			
		}
		
	}
		
	}
	
	public function twice_in_a_day_list()
	{
		$tat_data = array();
		$this->db->select('*')->from('twice_in_a_day_tat');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Tat_management/update_twice_in_a_day_tat_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Tat_management/update_twice_in_a_day_tat_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
				$edit = "<a href='".page_url."Tat_management/edit_twice_in_a_day_tat/".$row->id."'><i class='fa fa-pencil'></i></a>";
			
			$tat_data[] = array('sr_no'=>$i,
			'first_time'=>$row->first_time,
			'second_time'=>$row->second_time,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($tat_data),
			"iTotalDisplayRecords" => count($tat_data),
			"aaData"=>$tat_data);
			
		echo json_encode($results);
	}
	
	
	public function add_twice_in_a_day_tat()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('first_time', 'Time', 'required|trim');
		$this->form_validation->set_rules('second_time', 'Time', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/TAT/add_twice_in_a_day');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "twice_in_a_day_tat";
		$query = $this->db->select('first_time,second_time')->from('twice_in_a_day_tat')->where('first_time',$this->input->post('first_time'))->where('second_time',$this->input->post('second_time'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','Sorry,This record already exist.');
			redirect(page_url.'Tat_management/add_twice_in_a_day_tat/'.$this->uri->segment(3));
		}else{
			$data = array('first_time'=>$this->input->post('first_time'),
			'second_time'=>$this->input->post('second_time'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Tat_management/add_twice_in_a_day_tat/'.$this->uri->segment(3));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tat_management/add_twice_in_a_day_tat/'.$this->uri->segment(3));
		}
		}
		
	}
		
	}
	
	public function update_twice_in_a_day_tat_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "id";
		$table = "twice_in_a_day_tat";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', 'Status successfully updated.');
			redirect('Tat_management/add_twice_in_a_day_tat/');
		}
	
	public function edit_twice_in_a_day_tat()
	{
		$this->load->view('audit_report/TAT/edit_twice_in_a_day');
		
	}
	
	public function update_twice_in_a_day_tat()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('first_time', 'Time', 'required|trim');
		$this->form_validation->set_rules('second_time', 'Time', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/TAT/edit_twice_in_a_day');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "twice_in_a_day_tat";	
		$identifier = $this->uri->segment(3);
		$field_name = "id";		
		$data = array('first_time'=>$this->input->post('first_time'),
			'second_time'=>$this->input->post('second_time'),
			'status'=>$this->input->post('status'),
			'added_by'=>$user_id);
		$result  = $this->master->update_records($table,$data,$identifier,$field_name);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'Tat_management/add_twice_in_a_day_tat');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tat_management/add_twice_in_a_day_tat');
			
		}
		
	}
		
	}
	
}
