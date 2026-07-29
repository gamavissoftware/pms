<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Audit_report extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
			$session = $this->session->userdata('logged_in');
		if($session == FALSE)
		{
		
		redirect(page_url);
		
		}
		$this->load->model('User_model','user');
		$this->load->model('Master_model','master');
		$ip = $_SERVER["REMOTE_ADDR"];
		 /*$query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
            if($query->num_rows()=='0'){
            $this->session->set_flashdata('message','<span class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</span><br/>');
            redirect(page_url.'Dashboard');    
            }*/
	}
	
	public function index(){
		$this->load->view('audit_report/dashboard');
	}
	public function audit_type()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('audit_type', 'Audit Type', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		//$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/audit_type');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "audit_type";
		$query = $this->db->select('audit_type')->from('audit_type')->where('audit_type',$this->input->post('audit_type'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','Sorry! This record already exist.');
			redirect(page_url.'Audit_report/audit_type');
			
		}else{
		
		
			$data = array('audit_type'=>$this->input->post('audit_type'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>'1');
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Audit_report/audit_type');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Audit_report/audit_type');
		}
		}
		
	}
		
	}
	public function Audit_type_list()
	{
		$Audit_type_data = array();
		$i=1;
		$this->db->select('*')->from('audit_type');
		$this->db->order_by('audit_type','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Audit_report/update_audit_type_status/".$row->audit_type_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Audit_report/update_audit_type_status/".$row->audit_type_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Audit_report/edit_audit_type/".$row->audit_type_id."'><i class='fa fa-pencil'></i></a>";	
				
			$Audit_type_data[] = array('sr_no'=>$i,
			'audit_type'=>$row->audit_type,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($Audit_type_data),
			"iTotalDisplayRecords" => count($Audit_type_data),
			"aaData"=>$Audit_type_data);
			
		echo json_encode($results);
	}
	
	public function update_audit_type_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "audit_type_id";
		$table = "audit_type";
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
			redirect('Audit_report/audit_type');
		}

	public function edit_audit_type(){
		$this->load->view('audit_report/edit_audit_type');
		
	}	
	
	public function update_audit_type()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('audit_type', 'Audit Type', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		//$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/edit_audit_type');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "audit_type";
		$query = $this->db->select('audit_type')->from('audit_type')->where('audit_type',$this->input->post('audit_type'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','Sorry! This record already exist.');
			redirect(page_url.'Audit_report/audit_type');
			
		}else{
		
		
			$data = array('audit_type'=>$this->input->post('audit_type'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>'1');
			$this->db->where('audit_type_id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'Audit_report/audit_type');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Audit_report/audit_type');
		}
		}
		
	}
		
	}
	
	public function audit_section()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('audit_type', 'Audit Type', 'required|trim');
		$this->form_validation->set_rules('audit_section', 'Audit Section', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/audit_section');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "audit_section";
		$query = $this->db->select('audit_type, audit_section')->from('audit_section')->where('audit_type',$this->input->post('audit_type'))->where('audit_section',$this->input->post('audit_section'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','Sorry! This record already exist.');
			redirect(page_url.'Audit_report/audit_section');
			
		}else{
		
		
			$data = array('audit_type'=>$this->input->post('audit_type'),
			'audit_section'=>$this->input->post('audit_section'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Audit_report/audit_section');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Audit_report/audit_section');
		}
		}
		
	}
		
	}
	public function Audit_section_list()
	{
		$Audit_section_data = array();
		$i=1;
		$this->db->select('a.*,b.audit_type_id, b.audit_type as audit_type_name')->from('audit_section a')->join('audit_type b','a.audit_type=b.audit_type_id','left');
		$this->db->order_by('a.audit_section','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Audit_report/update_audit_section_status/".$row->audit_section_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Audit_report/update_audit_section_status/".$row->audit_section_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Audit_report/edit_audit_section/".$row->audit_section_id."'><i class='fa fa-pencil'></i></a>";	
				
			$Audit_section_data[] = array('sr_no'=>$i,
			'audit_type'=>$row->audit_type_name,
			'audit_section'=>$row->audit_section,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($Audit_section_data),
			"iTotalDisplayRecords" => count($Audit_section_data),
			"aaData"=>$Audit_section_data);
			
		echo json_encode($results);
	}
	
	public function update_audit_section_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "audit_section_id";
		$table = "audit_section";
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
			redirect('Audit_report/audit_section');
		}

	public function edit_audit_section(){
		$this->load->view('audit_report/edit_audit_section');
		
	}	
	
	public function update_audit_section()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('audit_type', 'Audit Type', 'required|trim');
		$this->form_validation->set_rules('audit_section', 'Audit Section', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		//$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/edit_audit_section');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "audit_section";
		$query = $this->db->select('audit_type, audit_section')->from('audit_section')->where('audit_type',$this->input->post('audit_type'))->where('audit_section',$this->input->post('audit_section'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','Sorry! This record already exist.');
			redirect(page_url.'Audit_report/audit_section');
			
		}else{
		
		
			$data = array('audit_type'=>$this->input->post('audit_type'),
			'audit_section'=>$this->input->post('audit_section'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>'1');
			$this->db->where('audit_section_id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'Audit_report/audit_section');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Audit_report/audit_section');
		}
		}
		
	}
		
	}
	
	public function add_task()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('audit_type', 'Audit Type', 'required|trim');
		$this->form_validation->set_rules('process_objectives', 'process objectives', 'required|trim');
		$this->form_validation->set_rules('process_name', 'Process Name', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/add-task');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "audit_tasks";
		
			$data = array('audit_type_id'=>$this->input->post('audit_type'),
			'audit_section_id'=>$this->input->post('audit_section'),
			'process_name'=>$this->input->post('process_name'),
			'process_objective'=>$this->input->post('process_objectives'),
			'process_steps'=>$this->input->post('process_step'),
			'tentative_time'=>$this->input->post('tentative_time'),
			'duration_id'=>$this->input->post('duration'),
			'status'=>$this->input->post('status'),
			'weightage'=>$this->input->post('weightage'),
			'category'=>$this->input->post('category'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Audit_report/add_task/'.$this->input->post('audit_type'));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Audit_report/add_task/'.$this->input->post('audit_type'));
		}

		
	}
		
	}
	
	public function add_task_under_section()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('audit_type', 'Audit Type', 'required|trim');
		$this->form_validation->set_rules('process_objectives', 'process objectives', 'required|trim');
		$this->form_validation->set_rules('process_name', 'Process Name', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/view_audit_task_by_devision');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "audit_tasks";
		
			$data = array('audit_type_id'=>$this->input->post('audit_type'),
			'audit_section_id'=>$this->input->post('audit_section'),
			'process_name'=>$this->input->post('process_name'),
			'process_objective'=>$this->input->post('process_objectives'),
			'process_steps'=>$this->input->post('process_step'),
			'tentative_time'=>$this->input->post('tentative_time'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Audit_report/view_audit_tasks/'.$this->uri->segment(3)."/".$this->uri->segment(4));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Audit_report/view_audit_tasks/'.$this->uri->segment(3)."/".$this->uri->segment(4));
		}

		
	}
		
	}
	
	public function not_active_task(){
	    $this->load->view('audit_report/not_active_tasks.php');
	}
	public function Audit_task_list()
	{
		$Audit_section_data = array();
		$i=1;
		$this->db->select('a.*,b.audit_type_id, b.audit_type as audit_type_name, c.audit_section_id, c.audit_section,d.timing_id,d.timing_period')->from('audit_tasks a')->join('audit_type b','a.audit_type_id=b.audit_type_id','left')->join('audit_section c','a.audit_section_id=c.audit_section_id','left')->join('timing_slot d','a.tentative_time=d.timing_id','left');
		$this->db->where('a.audit_type_id',$this->uri->segment(3));
		$this->db->where('a.status','1');
		$this->db->order_by('a.task_id','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
		    
		    $scheduledtime = "";
			$tat_id = $row->tentative_time;
			$duration_id = $row->duration_id;
			/* For Daily*/
			if($tat_id=='1'){
				$query = $this->db->select('id, timing')->from('daily_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $daily)
				$scheduledtime = date('h:i A',strtotime($daily->timing));
				
			}
			/* For Daily*/
			/* For Twice in a week*/
			if($tat_id=='2'){
				$query = $this->db->select('id, first_day, second_day')->from('twice_in_a_week_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $twice_in_a_week)
				$scheduledtime = $twice_in_a_week->first_day." and ".$twice_in_a_week->second_day;
				
			}
			/* For Twice in a week*/
			/* Weekly*/
			if($tat_id=='3'){
				$query = $this->db->select('id, weekday')->from('weekly_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $weekly)
				$scheduledtime = $weekly->weekday;
				
			}
			/* Weekly*/
			/* Twice in a Month*/
			if($tat_id=='4'){
				$query = $this->db->select('id, first_date, second_date')->from('twice_in_a_month_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $twice_in_a_month)
				$scheduledtime = $twice_in_a_month->first_date." and ".$twice_in_a_month->second_date." In every Month";
				
			}
			/* Twice in a Month*/
			/* Monthly*/
			if($tat_id=='5'){
				$query = $this->db->select('id, monthly')->from('monthly_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $monthly)
				$scheduledtime = $monthly->monthly;
				
			}
			/*Monthly*/
			/* quarterly_tat*/
			if($tat_id=='6'){
				$query = $this->db->select('id, month')->from('quarterly_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $quarterly_tat)
				$scheduledtime = $quarterly_tat->month;
				
			}
			/*quarterly_tat*/
			
			/* twice_in_a_year_tat*/
			if($tat_id=='7'){
				$query = $this->db->select('id, first_month, second_month')->from('twice_in_a_year_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $twice_in_a_year_tat)
				$scheduledtime = $twice_in_a_year_tat->first_month." and ".$twice_in_a_year_tat->second_month;
				
			}
			/*twice_in_a_year_tat*/
			/* yearly_tat*/
			if($tat_id=='8'){
				$query = $this->db->select('id, yearly')->from('yearly_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $yearly_tat)
				$scheduledtime = $yearly_tat->yearly;
				
			}
			/*twice_in_a_year_tat*/
			/* twice_in_a_day_tat*/
			if($tat_id=='9'){
				$query = $this->db->select('id, second_time, first_time')->from('twice_in_a_day_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $twice_in_a_day_tat)
				$scheduledtime = date('h:i A',strtotime($twice_in_a_day_tat->first_time))." and ".date('h:i A',strtotime($twice_in_a_day_tat->second_time));
				
			}
			/*twice_in_a_year_tat*/
			
			
		$view_report = "<a href='".page_url."Audit_report/view_reports/".$row->task_id."/".$row->audit_type_id."'><span class='btn btn-danger btn-xs'>View report</span></a>";											
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Audit_report/update_audit_task_status/".$row->task_id."/".$row->status."/".$row->audit_type_id."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Audit_report/update_audit_task_status/".$row->task_id."/".$row->status."/".$row->audit_type_id."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Audit_report/edit_audit_tasks/".$row->task_id."/".$row->audit_type_id."'><i class='fa fa-pencil'></i></a>";
	    	$query = $this->db->select('id, section_id, typeid, taskid, remarks, addedOn')->from('audit_tasks_remark')->where('taskid',$row->task_id)->limit(1)->order_by('id','desc')->get();
			$res = $query->result();
			if($res){
				foreach($res as $remarks)
				
				
				if($row->remark_flag=='0'){
				
		$update_report = "<input type='radio' name='pick_items".$row->task_id."' class='dny' id='pick_items".$i."' value='0' onChange='display_qtybox(".$i.",".$row->task_id.")'>&nbsp;Not Done";
			$update_report1 = "<input type='radio' name='pick_items".$row->task_id."' class='dny' id='pick_items1".$i."' value='1' onChange='display_qtybox(".$i.",".$row->task_id.")'>&nbsp;Done";
		    $reporting_box = "<textarea name='rtrm".$row->task_id."' class='form-control' style='display:none; width:100%' id='qty".$i."'></textarea>&nbsp;&nbsp;&nbsp;<input type='file' style='display:none; width:100%' class='form-control' name='report".$row->task_id."' id='report".$i."'>";
			$hidden="<div id='makenewhidden".$i."'></div>";
		$html = "<div class='row'><div class='col-md-12'><div class='col-md-6'>".$update_report."</div><div class='col-md-6'>".$update_report1."</div></div><div class='col-md-12'><div class='col-md-9'>".$reporting_box."</div></div></div>".$hidden;
				}else{
				    	$html = $remarks->remarks;
				}
			}else{
			  
			$update_report = "<input type='radio' name='pick_items".$row->task_id."' class='dny' id='pick_items".$i."' value='0' onChange='display_qtybox(".$i.",".$row->task_id.")'>&nbsp;Not Done";
			$update_report1 = "<input type='radio' name='pick_items".$row->task_id."' class='dny' id='pick_items1".$i."' value='1' onChange='display_qtybox(".$i.",".$row->task_id.")'>&nbsp;Done";
		    $reporting_box = "<textarea name='rtrm".$row->task_id."' class='form-control' style='display:none; width:100%' id='qty".$i."'></textarea>&nbsp;&nbsp;&nbsp;<input type='file' style='display:none; width:100%' class='form-control' name='report".$row->task_id."' id='report".$i."'>";
			$hidden="<div id='makenewhidden".$i."'></div>";
			$html = "<div class='row'><div class='col-md-12'><div class='col-md-6'>".$update_report."</div><div class='col-md-6'>".$update_report1."</div></div><div class='col-md-12'><div class='col-md-9'>".$reporting_box."</div></div></div>".$hidden;
			}
			
			$category = $row->category; 
			if($category=='1'){
			    $cat_name = "Normal";
			}elseif($category=='2'){
			    $cat_name = "Critical";
			}else{
			    $cat_name = "";
			}
			$Audit_section_data[] = array('sr_no'=>$i,
			'audit_section'=>$row->audit_section,
			'process_name'=>$row->process_name."<br>".$view_report,
			'process_objective'=>$row->process_objective,
			'process_steps'=>$row->process_steps,
			'tentative_time'=>$row->timing_period,
			'scheduledtime'=>$scheduledtime,
			'category'=>$cat_name,
			'weightage'=>$row->weightage,
			'status'=>$sta,
			'update_status'=>$html,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($Audit_section_data),
			"iTotalDisplayRecords" => count($Audit_section_data),
			"aaData"=>$Audit_section_data);
			
		echo json_encode($results);
	}
	
	
		public function Not_active_Audit_task_list()
	{
		$Audit_section_data = array();
		$i=1;
		$this->db->select('a.*,b.audit_type_id, b.audit_type as audit_type_name, c.audit_section_id, c.audit_section,d.timing_id,d.timing_period')->from('audit_tasks a')->join('audit_type b','a.audit_type_id=b.audit_type_id','left')->join('audit_section c','a.audit_section_id=c.audit_section_id','left')->join('timing_slot d','a.tentative_time=d.timing_id','left');
		//$this->db->where('a.audit_type_id',$this->uri->segment(3));
		$this->db->where('a.status','0');
		$this->db->order_by('a.task_id','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
		    
		    $scheduledtime = "";
			$tat_id = $row->tentative_time;
			$duration_id = $row->duration_id;
			/* For Daily*/
			if($tat_id=='1'){
				$query = $this->db->select('id, timing')->from('daily_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $daily)
				$scheduledtime = date('h:i A',strtotime($daily->timing));
				
			}
			/* For Daily*/
			/* For Twice in a week*/
			if($tat_id=='2'){
				$query = $this->db->select('id, first_day, second_day')->from('twice_in_a_week_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $twice_in_a_week)
				$scheduledtime = $twice_in_a_week->first_day." and ".$twice_in_a_week->second_day;
				
			}
			/* For Twice in a week*/
			/* Weekly*/
			if($tat_id=='3'){
				$query = $this->db->select('id, weekday')->from('weekly_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $weekly)
				$scheduledtime = $weekly->weekday;
				
			}
			/* Weekly*/
			/* Twice in a Month*/
			if($tat_id=='4'){
				$query = $this->db->select('id, first_date, second_date')->from('twice_in_a_month_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $twice_in_a_month)
				$scheduledtime = $twice_in_a_month->first_date." and ".$twice_in_a_month->second_date." In every Month";
				
			}
			/* Twice in a Month*/
			/* Monthly*/
			if($tat_id=='5'){
				$query = $this->db->select('id, monthly')->from('monthly_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $monthly)
				$scheduledtime = $monthly->monthly;
				
			}
			/*Monthly*/
			/* quarterly_tat*/
			if($tat_id=='6'){
				$query = $this->db->select('id, month')->from('quarterly_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $quarterly_tat)
				$scheduledtime = $quarterly_tat->month;
				
			}
			/*quarterly_tat*/
			
			/* twice_in_a_year_tat*/
			if($tat_id=='7'){
				$query = $this->db->select('id, first_month, second_month')->from('twice_in_a_year_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $twice_in_a_year_tat)
				$scheduledtime = $twice_in_a_year_tat->first_month." and ".$twice_in_a_year_tat->second_month;
				
			}
			/*twice_in_a_year_tat*/
			/* yearly_tat*/
			if($tat_id=='8'){
				$query = $this->db->select('id, yearly')->from('yearly_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $yearly_tat)
				$scheduledtime = $yearly_tat->yearly;
				
			}
			/*twice_in_a_year_tat*/
			/* twice_in_a_day_tat*/
			if($tat_id=='9'){
				$query = $this->db->select('id, second_time, first_time')->from('twice_in_a_day_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $twice_in_a_day_tat)
				$scheduledtime = date('h:i A',strtotime($twice_in_a_day_tat->first_time))." and ".date('h:i A',strtotime($twice_in_a_day_tat->second_time));
				
			}
			/*twice_in_a_year_tat*/
			
			
		$view_report = "<a href='".page_url."Audit_report/view_reports/".$row->task_id."/".$row->audit_type_id."'><span class='btn btn-danger btn-xs'>View report</span></a>";											
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Audit_report/update_not_active_audit_task_status/".$row->task_id."/".$row->status."/".$row->audit_type_id."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Audit_report/update_not_active_audit_task_status/".$row->task_id."/".$row->status."/".$row->audit_type_id."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Audit_report/edit_audit_tasks/".$row->task_id."/".$row->audit_type_id."'><i class='fa fa-pencil'></i></a>";
	    	$query = $this->db->select('id, section_id, typeid, taskid, remarks, addedOn')->from('audit_tasks_remark')->where('taskid',$row->task_id)->limit(1)->order_by('id','desc')->get();
			$res = $query->result();
			if($res){
				foreach($res as $remarks)
				
				
				if($row->remark_flag=='0'){
				
		$update_report = "<input type='radio' name='pick_items".$row->task_id."' class='dny' id='pick_items".$i."' value='0' onChange='display_qtybox(".$i.",".$row->task_id.")'>&nbsp;Not Done";
			$update_report1 = "<input type='radio' name='pick_items".$row->task_id."' class='dny' id='pick_items1".$i."' value='1' onChange='display_qtybox(".$i.",".$row->task_id.")'>&nbsp;Done";
		    $reporting_box = "<textarea name='rtrm".$row->task_id."' class='form-control' style='display:none; width:100%' id='qty".$i."'></textarea>&nbsp;&nbsp;&nbsp;<input type='file' style='display:none; width:100%' class='form-control' name='report".$row->task_id."' id='report".$i."'>";
			$hidden="<div id='makenewhidden".$i."'></div>";
		$html = "<div class='row'><div class='col-md-12'><div class='col-md-6'>".$update_report."</div><div class='col-md-6'>".$update_report1."</div></div><div class='col-md-12'><div class='col-md-9'>".$reporting_box."</div></div></div>".$hidden;
				}else{
				    	$html = $remarks->remarks;
				}
			}else{
			  
			$update_report = "<input type='radio' name='pick_items".$row->task_id."' class='dny' id='pick_items".$i."' value='0' onChange='display_qtybox(".$i.",".$row->task_id.")'>&nbsp;Not Done";
			$update_report1 = "<input type='radio' name='pick_items".$row->task_id."' class='dny' id='pick_items1".$i."' value='1' onChange='display_qtybox(".$i.",".$row->task_id.")'>&nbsp;Done";
		    $reporting_box = "<textarea name='rtrm".$row->task_id."' class='form-control' style='display:none; width:100%' id='qty".$i."'></textarea>&nbsp;&nbsp;&nbsp;<input type='file' style='display:none; width:100%' class='form-control' name='report".$row->task_id."' id='report".$i."'>";
			$hidden="<div id='makenewhidden".$i."'></div>";
			$html = "<div class='row'><div class='col-md-12'><div class='col-md-6'>".$update_report."</div><div class='col-md-6'>".$update_report1."</div></div><div class='col-md-12'><div class='col-md-9'>".$reporting_box."</div></div></div>".$hidden;
			}
			
			$Audit_section_data[] = array('sr_no'=>$i,
			'audit_section'=>$row->audit_section,
			'process_name'=>$row->process_name."<br>".$view_report,
			'process_objective'=>$row->process_objective,
			'process_steps'=>$row->process_steps,
			'tentative_time'=>$row->timing_period,
			'scheduledtime'=>$scheduledtime,
			'status'=>$sta,
			'update_status'=>$html,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($Audit_section_data),
			"iTotalDisplayRecords" => count($Audit_section_data),
			"aaData"=>$Audit_section_data);
			
		echo json_encode($results);
	}
	
	public function view_audit_tasks(){
		$this->load->view('audit_report/view_audit_task_by_devision');
	}
	public function filter_Audit_task_list()
	{
		$Audit_section_data = array();
		$i=1;
		$this->db->select('a.*,b.audit_type_id, b.audit_type as audit_type_name, c.audit_section_id, c.audit_section')->from('audit_tasks a')->join('audit_type b','a.audit_type_id=b.audit_type_id','left')->join('audit_section c','a.audit_section_id=c.audit_section_id','left');
		$this->db->where('a.audit_type_id',$this->uri->segment(3));
		$this->db->where('a.audit_section_id',$this->uri->segment(4));
		$this->db->order_by('a.task_id','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
		    
		    $view_report = "<a href='".page_url."Audit_report/view_reports/".$row->task_id."/".$row->audit_type_id."'><span class='btn btn-danger btn-xs'>View report</span></a>";	
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Audit_report/update_audit_task_status/".$row->task_id."/".$row->status."/".$row->audit_type_id."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Audit_report/update_audit_task_status/".$row->task_id."/".$row->status."/".$row->audit_type_id."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Audit_report/edit_audit_tasks/".$row->task_id."/".$row->audit_type_id."'><i class='fa fa-pencil'></i></a>";	
			$query = $this->db->select('id, section_id, typeid, taskid, remarks, addedOn')->from('audit_tasks_remark')->where('taskid',$row->task_id)->get();
			$res = $query->result();
			if($res){
				foreach($res as $remarks)
				$html = $remarks->remarks;				
			}else{
			$update_report = "<input type='checkbox' class='form-control' name='pick_items[]' id='pick_items".$i."' value='".$row->task_id."' onChange='display_qtybox(".$i.")'>";
		    $reporting_box = "<textarea name='rtrm[]' class='form-control' style='display:none; width:100%' id='qty".$i."'></textarea>&nbsp;&nbsp;&nbsp;<input type='file' style='display:none; width:100%' class='form-control' name='report[]' id='report".$i."'>";
			$html = "<div class='row'><div class='col-md-3'>".$update_report."</div><div class='col-md-9'>".$reporting_box."</div></div>";
			}
				
			$Audit_section_data[] = array('sr_no'=>$i,
			'audit_section'=>$row->audit_section,
			'process_name'=>$row->process_name."<br>".$view_report,
			'process_objective'=>$row->process_objective,
			'process_steps'=>$row->process_steps,
			'tentative_time'=>$row->tentative_time,
			'status'=>$sta,
			'update_status'=>$html,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($Audit_section_data),
			"iTotalDisplayRecords" => count($Audit_section_data),
			"aaData"=>$Audit_section_data);
			
		echo json_encode($results);
	}
	
	public function update_audit_task_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$department = $this->uri->segment(5);
		$field_name = "task_id";
		$table = "audit_tasks";
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
			redirect('Audit_report/add_task/'.$department);
		}
		
	public function update_not_active_audit_task_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$department = $this->uri->segment(5);
		$field_name = "task_id";
		$table = "audit_tasks";
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
			redirect('Audit_report/not_active_task/');
		}

	public function edit_audit_tasks(){
		$this->load->view('audit_report/edit_task');
		
	}	
	
	public function update_audit_task()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('audit_type', 'Audit Type', 'required|trim');
		$this->form_validation->set_rules('process_objectives', 'process objectives', 'required|trim');
		$this->form_validation->set_rules('process_name', 'Process Name', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/edit_task');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "audit_tasks";
		
			$data = array('audit_type_id'=>$this->input->post('audit_type'),
			'audit_section_id'=>$this->input->post('audit_section'),
			'process_name'=>$this->input->post('process_name'),
			'process_objective'=>$this->input->post('process_objectives'),
			'process_steps'=>$this->input->post('process_step'),
			'tentative_time'=>$this->input->post('tentative_time'),
			'duration_id'=>$this->input->post('duration'),
			'status'=>$this->input->post('status'),
			'weightage'=>$this->input->post('weightage'),
			'category'=>$this->input->post('category'),
			'updated_on'=>$date,
			'added_by'=>$user_id);
			$this->db->where('task_id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Audit_report/add_task/'.$this->input->post('audit_type'));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Audit_report/add_task/'.$this->input->post('audit_type'));
		}

		
	}
	}
	
	public function scheduler(){
	
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('scheduler', 'Scheduler', 'required|trim');
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/scheduler');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   $query = $this->db->select('timing_period')->from('timing_slot')->where('timing_period',$this->input->post('scheduler'))->get();
		   $res = $query->result();
		   if($res){
			   $this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Sorry,This record already exist.</span><br/>');
				redirect(page_url.'Audit_report/scheduler');
			   
		   }else{
		   
		   $data=
			array('timing_period'=>$this->input->post('scheduler'),
			'timing_status'=>$this->input->post('status'),
			'added_by'=>$user_id,
			'added_time'=>$added_time);
			
			$res = $this->db->insert('timing_slot',$data);
			if($res)
			{
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'Audit_report/scheduler');
				}
		   }
			
			}
}
public function update_scheduler_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "timing_id";
		$table = "timing_slot";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('timing_status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', 'Status successfully updated.');
			redirect('Audit_report/scheduler');
		}
	public function edit_scheduler()
	{
	$this->load->view('audit_report/edit_schedular');
		
	}
public function scheduler_list()
	{
		$scheduler_data = array();
		$query = $this->db->select('*')->from('timing_slot')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			$status = $row->timing_status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Audit_report/update_scheduler_status/".$row->timing_id."/".$row->timing_status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Audit_report/update_scheduler_status/".$row->timing_id."/".$row->timing_status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			
			if($row->timing_id=='1'){
				$add_view = "<a href='".page_url."Tat_management/add_daily_tat/".$row->timing_id."'><span class='btn btn-warning btn-xs'>Add/ View</span></a>";
			}elseif($row->timing_id=='2'){
				$add_view = "<a href='".page_url."Tat_management/add_twice_in_a_week_tat/".$row->timing_id."'><span class='btn btn-warning btn-xs'>Add/ View</span></a>";
			}elseif($row->timing_id=='3'){
				$add_view = "<a href='".page_url."Tat_management/add_weekly_tat/".$row->timing_id."'><span class='btn btn-warning btn-xs'>Add/ View</span></a>";
			}elseif($row->timing_id=='4'){
				$add_view = "<a href='".page_url."Tat_management/add_twice_in_a_month_tat/".$row->timing_id."'><span class='btn btn-warning btn-xs'>Add/ View</span></a>";
			}elseif($row->timing_id=='5'){
				$add_view = "<a href='".page_url."Tat_management/add_monthly_tat/".$row->timing_id."'><span class='btn btn-warning btn-xs'>Add/ View</span></a>";
			}elseif($row->timing_id=='6'){
				$add_view = "<a href='".page_url."Tat_management/add_quarterly_tat/".$row->timing_id."'><span class='btn btn-warning btn-xs'>Add/ View</span></a>";
			}elseif($row->timing_id=='7'){
				$add_view = "<a href='".page_url."Tat_management/add_twice_in_a_year_tat/".$row->timing_id."'><span class='btn btn-warning btn-xs'>Add/ View</span></a>";
			}elseif($row->timing_id=='8'){
				$add_view = "<a href='".page_url."Tat_management/add_yearly_tat/".$row->timing_id."'><span class='btn btn-warning btn-xs'>Add/ View</span></a>";
			}elseif($row->timing_id=='9'){
				$add_view = "<a href='".page_url."Tat_management/add_twice_in_a_day_tat/".$row->timing_id."'><span class='btn btn-warning btn-xs'>Add/ View</span></a>";
			}
			
			
			$edit = "<a href='".page_url."Audit_report/edit_scheduler/".$row->timing_id."'><i class='fa fa-pencil'></i></a>";
			$scheduler_data[] = array('sr_no'=>$i,
			'timing_period'=>$row->timing_period,
			'add_view'=>$add_view,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}



public function update_scheduler()
{
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('scheduler', 'Scheduler', 'required|trim');
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/edit_schedular');
		}else
		{
			
		   date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   if($this->input->post('start_date')){
		    $start_date = date('Y-m-d',strtotime($this->input->post('start_date')));
			$end_date = date('Y-m-d',strtotime($this->input->post('end_date')));
			$data=
			array('timing_period'=>$this->input->post('scheduler'),
			'timing_status'=>$this->input->post('status'),
			'added_by'=>$user_id,
			'added_time'=>$added_time);
			$this->db->where('timing_id',$this->uri->segment(3));
			$res = $this->db->update('timing_slot',$data);
			if($res)
			{
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'Audit_report/scheduler');
				}
		   }else{
			 $data=
			array('timing_period'=>$this->input->post('scheduler'),
			'timing_status'=>$this->input->post('status'),
			'added_by'=>$user_id,
			'added_time'=>$added_time);
			$this->db->where('timing_id',$this->uri->segment(3));
			$res = $this->db->update('timing_slot',$data);
			if($res)
			{
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'Audit_report/scheduler');
				}			
		   }
		 
			
		
				
			}
}

function updateremarks()
{
  
  $user_id =$this->session->userdata['logged_in']['user_id'];
  $sectionid=$this->uri->segment('3');
  date_default_timezone_set("Asia/Kolkata");
 

$checkeditem=$this->input->post('countall');
 //echo "<pre>"; print_r($checkeditem);exit;
 for($i=0;$i<count($checkeditem);$i++)
 {
	 $taskid=$checkeditem[$i];
	 $yno=$_REQUEST['pick_items'.$taskid];
	if($yno=='0')
	{
	 $taskstatus='0';
	}else
	{
		 $taskstatus='1';
	}
	
	 $report=$_FILES['report'.$taskid]['name'];
	 if($report<>'')
	 {
		$exty=explode('.',$report);
		$newname=$exty[0].'_'.date('d-y').'.'.$exty['1'];
		move_uploaded_file($_FILES["report".$taskid]["tmp_name"],UPLOADPATH.'auditreport/'.$newname);
		$data1=array('task_id'=>$taskid,'report_file'=>$newname,'added_on'=>date('Y-m-d h:i:s'));
		$this->db->insert('upload_reports',$data1);
	 }else
	 {
		 $newname='';
	 }
	
	//echo "<pre>"; print_r($a);exit;
	
	 $que=$this->db->select('audit_type_id')->from('audit_tasks')->where('task_id',$taskid)->get();
	 foreach($que->result() as $auditdata);
	 $typeid=$auditdata->audit_type_id;
	 $userremarks=$_REQUEST['rtrm'.$taskid];
	 
	 $data=array('section_id'=>$sectionid,'typeid'=>$typeid,'taskid'=>$taskid,'remarks'=>$userremarks,'addedby'=>$user_id,'addedOn'=>date('Y-m-d h:i:s'),'taskstatus'=>$taskstatus);
	$this->db->insert('audit_tasks_remark',$data);
	
	$taskdata = array('remark_flag'=>'1');
	$this->db->where('task_id',$taskid);
	$this->db->update('audit_tasks',$taskdata);
}
    redirect(page_url.'Audit_report/add_task/'.$typeid);
	
}

function updateremarks_by_devision()
{
  
  $user_id =$this->session->userdata['logged_in']['user_id'];
  $sectionid=$this->uri->segment('3');
  $checkeditem=$this->input->post('pick_items');
  $remarks=$this->input->post('rtrm');
  
 date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
 for($i=0;$i<count($checkeditem);$i++)
 {
	$taskid=$checkeditem[$i];
	 $report=$_FILES['report']['name'][$i];
	 if($report<>'')
	 {
		$exty=explode('.',$report);
		$newname=$exty[0].'_'.date('d-y').'.'.$exty['1'];
		move_uploaded_file($_FILES["report"]["tmp_name"][$i],UPLOADPATH.'auditreport/'.$newname);
		$data1=array('task_id'=>$taskid,'report_file'=>$newname,'added_on'=>date('Y-m-d h:i:s'));
		$this->db->insert('upload_reports',$data1);
	 }else
	 {
		 $newname='';
	 }
	$que=$this->db->select('audit_type_id')->from('audit_tasks')->where('task_id',$taskid)->get();
	foreach($que->result() as $auditdata);
	$typeid=$auditdata->audit_type_id;
	$userremarks=$remarks[$i];
	$data=array('section_id'=>$sectionid,'typeid'=>$typeid,'taskid'=>$taskid,'remarks'=>$remarks[$i],'addedby'=>$user_id,'addedOn'=>$added_time);
	$this->db->insert('audit_tasks_remark',$data);
}
$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span><br/>');
    redirect(page_url.'Audit_report/view_audit_tasks/'.$this->uri->segment(3)."/".$this->uri->segment(4));
	
}

public function add_hr_work()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('audit_type', 'Audit Type', 'required|trim');
		$this->form_validation->set_rules('responsible_person', 'Responsible Person', 'required|trim');
		$this->form_validation->set_rules('process_name', 'Process Name', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/hr_audit');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "hr_audit_works";
		
			$data = array('audit_type_id'=>$this->input->post('audit_type'),
			'responsible_person'=>$this->input->post('responsible_person'),
			'process_name'=>$this->input->post('process_name'),
			'checklist_points'=>$this->input->post('audit_checklist'),
			'work_timing'=>$this->input->post('work_timing'),
			'tentative_time'=>$this->input->post('tentative_time'),
			'duration_id'=>$this->input->post('duration'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Audit_report/add_hr_work/'.$this->input->post('audit_type'));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Audit_report/add_hr_work/'.$this->input->post('audit_type'));
		}

		
	}
		
	}
	public function hr_checklist()
	{
		$Audit_section_data = array();
		$i=1;
		$this->db->select('a.*,b.audit_type_id, b.audit_type as audit_type_name,c.user_id, c.first_name, c.last_name, f.timing_id,f.timing_period')->from('hr_audit_works a')->join('audit_type b','a.audit_type_id=b.audit_type_id','left')->join('system_users c','c.user_id=a.responsible_person','left')->join('timing_slot f','a.tentative_time=f.timing_id','left');
		$this->db->where('a.audit_type_id',$this->uri->segment(3));
		$this->db->order_by('a.task_id','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			
$scheduledtime = "";
			$tat_id = $row->tentative_time;
			$duration_id = $row->duration_id;
			/* For Daily*/
			if($tat_id=='1'){
				$query = $this->db->select('id, timing')->from('daily_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $daily)
				$scheduledtime = date('h:i A',strtotime($daily->timing));
				
			}
			/* For Daily*/
			/* For Twice in a week*/
			if($tat_id=='2'){
				$query = $this->db->select('id, first_day, second_day')->from('twice_in_a_week_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $twice_in_a_week)
				$scheduledtime = $twice_in_a_week->first_day." and ".$twice_in_a_week->second_day;
				
			}
			/* For Twice in a week*/
			/* Weekly*/
			if($tat_id=='3'){
				$query = $this->db->select('id, weekday')->from('weekly_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $weekly)
				$scheduledtime = $weekly->weekday;
				
			}
			/* Weekly*/
			/* Twice in a Month*/
			if($tat_id=='4'){
				$query = $this->db->select('id, first_date, second_date')->from('twice_in_a_month_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $twice_in_a_month)
				$scheduledtime = $twice_in_a_month->first_date." and ".$twice_in_a_month->second_date." In every Month";
				
			}
			/* Twice in a Month*/
			/* Monthly*/
			if($tat_id=='5'){
				$query = $this->db->select('id, monthly')->from('monthly_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $monthly)
				$scheduledtime = $monthly->monthly;
				
			}
			/*Monthly*/
			/* quarterly_tat*/
			if($tat_id=='6'){
				$query = $this->db->select('id, month')->from('quarterly_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $quarterly_tat)
				$scheduledtime = $quarterly_tat->month;
				
			}
			/*quarterly_tat*/
			
			/* twice_in_a_year_tat*/
			if($tat_id=='7'){
				$query = $this->db->select('id, first_month, second_month')->from('twice_in_a_year_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $twice_in_a_year_tat)
				$scheduledtime = $twice_in_a_year_tat->first_month." and ".$twice_in_a_year_tat->second_month;
				
			}
			/*twice_in_a_year_tat*/
			/* yearly_tat*/
			if($tat_id=='8'){
				$query = $this->db->select('id, yearly')->from('yearly_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $yearly_tat)
				$scheduledtime = $yearly_tat->yearly;
				
			}
			/*twice_in_a_year_tat*/
			/* twice_in_a_day_tat*/
			if($tat_id=='9'){
				$query = $this->db->select('id, second_time, first_time')->from('twice_in_a_day_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $twice_in_a_day_tat)
				$scheduledtime = date('h:i A',strtotime($twice_in_a_day_tat->first_time))." and ".date('h:i A',strtotime($twice_in_a_day_tat->second_time));
				
			}
			/*twice_in_a_year_tat*/
			
			
			
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Audit_report/update_audit_task_status/".$row->task_id."/".$row->status."/".$row->audit_type_id."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Audit_report/update_audit_task_status/".$row->task_id."/".$row->status."/".$row->audit_type_id."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Audit_report/edit_hr_task/".$row->task_id."/".$row->audit_type_id."'><i class='fa fa-pencil'></i></a>";
		  $query = $this->db->select('id, section_id, typeid, taskid, remarks, addedOn')->from('audit_hr_tasks_remark')->where('taskid',$row->task_id)->limit(1)->order_by('id','desc')->get();
			$res = $query->result();
			if($res){
				foreach($res as $remarks)
				
				
				if($row->remark_flag=='0'){
				
		$update_report = "<input type='radio' name='pick_items".$row->task_id."' class='dny' id='pick_items".$i."' value='0' onChange='display_qtybox(".$i.",".$row->task_id.")'>&nbsp;Not Done";
			$update_report1 = "<input type='radio' name='pick_items".$row->task_id."' class='dny' id='pick_items1".$i."' value='1' onChange='display_qtybox(".$i.",".$row->task_id.")'>&nbsp;Done";
		    $reporting_box = "<textarea name='rtrm".$row->task_id."' class='form-control' style='display:none; width:100%' id='qty".$i."'></textarea>&nbsp;&nbsp;&nbsp;<input type='file' style='display:none; width:100%' class='form-control' name='report".$row->task_id."' id='report".$i."'>";
			$hidden="<div id='makenewhidden".$i."'></div>";
		$html = "<div class='row'><div class='col-md-12'><div class='col-md-6'>".$update_report."</div><div class='col-md-6'>".$update_report1."</div></div><div class='col-md-12'><div class='col-md-9'>".$reporting_box."</div></div></div>".$hidden;
				}else{
				    	$html = $remarks->remarks;
				}
			}else{
			  
			$update_report = "<input type='radio' name='pick_items".$row->task_id."' class='dny' id='pick_items".$i."' value='0' onChange='display_qtybox(".$i.",".$row->task_id.")'>&nbsp;Not Done";
			$update_report1 = "<input type='radio' name='pick_items".$row->task_id."' class='dny' id='pick_items1".$i."' value='1' onChange='display_qtybox(".$i.",".$row->task_id.")'>&nbsp;Done";
		    $reporting_box = "<textarea name='rtrm".$row->task_id."' class='form-control' style='display:none; width:100%' id='qty".$i."'></textarea>&nbsp;&nbsp;&nbsp;<input type='file' style='display:none; width:100%' class='form-control' name='report".$row->task_id."' id='report".$i."'>";
			$hidden="<div id='makenewhidden".$i."'></div>";
			$html = "<div class='row'><div class='col-md-12'><div class='col-md-6'>".$update_report."</div><div class='col-md-6'>".$update_report1."</div></div><div class='col-md-12'><div class='col-md-9'>".$reporting_box."</div></div></div>".$hidden;
			}
			
			$category = $row->category; 
			if($category=='1'){
			    $cat_name = "Normal";
			}elseif($category=='2'){
			    $cat_name = "Critical";
			}else{
			    $cat_name = "";
			}
			
			$Audit_section_data[] = array('sr_no'=>$i,
			'process_name'=>$row->process_name,
			'work_timing'=>$row->work_timing,
			'responsible_person'=>$row->first_name." ".$row->last_name,
			'scheduledtime'=>$scheduledtime,
			'checklist_points'=>$row->checklist_points,
			'category'=>$cat_name,
			'weightage'=>$row->weightage,
			'update_status'=>$html,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($Audit_section_data),
			"iTotalDisplayRecords" => count($Audit_section_data),
			"aaData"=>$Audit_section_data);
			
		echo json_encode($results);
	}
	
	
	public function update_hr_remarks()
{
  
  $user_id =$this->session->userdata['logged_in']['user_id'];
  $sectionid=$this->uri->segment('3');
  $checkeditem=$this->input->post('pick_items');
  $remarks=$this->input->post('rtrm');
 date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
 for($i=0;$i<count($checkeditem);$i++)
 {
	$taskid=$checkeditem[$i];
	 
	$que=$this->db->select('audit_type_id')->from('hr_audit_works')->where('task_id',$taskid)->get();
	foreach($que->result() as $auditdata);
	$typeid=$auditdata->audit_type_id;
	$userremarks=$remarks[$i];
	$data=array('section_id'=>$sectionid,'typeid'=>$typeid,'taskid'=>$taskid,'remarks'=>$remarks[$i],'addedby'=>$user_id,'addedOn'=>$added_time);
	$this->db->insert('audit_hr_tasks_remark',$data);
}
$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span><br/>');
    redirect(page_url.'Audit_report/add_hr_work/'.$this->uri->segment(3));
	
}

public function edit_hr_task(){
	$this->load->view('audit_report/edit_hr_task');
	
}


public function update_hr_audit_task()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('audit_type', 'Audit Type', 'required|trim');
		$this->form_validation->set_rules('responsible_person', 'Responsible Person', 'required|trim');
		$this->form_validation->set_rules('process_name', 'Process Name', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('audit_report/edit_hr_task');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "hr_audit_works";
		
			$data = array('audit_type_id'=>$this->input->post('audit_type'),
			'responsible_person'=>$this->input->post('responsible_person'),
			'process_name'=>$this->input->post('process_name'),
			'checklist_points'=>$this->input->post('audit_checklist'),
			'work_timing'=>$this->input->post('work_timing'),
			'tentative_time'=>$this->input->post('tentative_time'),
			'duration_id'=>$this->input->post('duration'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			$this->db->where('task_id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'Audit_report/add_hr_work/'.$this->uri->segment(4));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Audit_report/add_hr_work/'.$this->uri->segment(4));
		}

		
	}
		
	}
	public function upload_audit_report(){
		$this->load->view('audit_report/upload_report');
	}
	
	public function upload(){
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "audit_report_attachment";
		$reportdata=$_FILES['reportdata']['name'];
		if($reportdata<>'')
		{
			$image1=explode('.',$reportdata);
			$cat_image=end($image1);
			$audit_report=time().'.'.$cat_image;
			move_uploaded_file($_FILES["reportdata"]["tmp_name"],UPLOADPATH.'audit_report/' . $audit_report);
		}else
		{
			$audit_report="";
			}
			
			$data = array('report'=>$audit_report,
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Audit_report/upload_audit_report/');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Audit_report/upload_audit_report/');
		}
	}
	public function attachment_list()
	{
		$audit_attachment_list = array();
		$i=1;
		$this->db->select('a.*,b.user_id, b.first_name, b.last_name')->from('audit_report_attachment a')->join('system_users b','a.added_by=b.user_id','left');
		$this->db->order_by('a.id','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$attachment = "<a href='".report_url.$row->report."'><i class='fa fa-file-excel-o' style='font-size:30px'></i></a>";
			$audit_attachment_list[] = array('sr_no'=>$i,
			'report'=>$attachment,
			'added_on'=>$row->added_on,
			'added_by'=>$row->first_name."".$row->last_name);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($audit_attachment_list),
			"iTotalDisplayRecords" => count($audit_attachment_list),
			"aaData"=>$audit_attachment_list);
			
		echo json_encode($results);
	}
	
	public function view_reports(){
		
		$this->load->view('audit_report/view_reports');
	}
	
	public function Audit_report_list()
	{
		$Audit_section_data = array();
		$i=1;
		$this->db->select('a.*,b.task_id,b.tentative_time,c.user_id, c.first_name, c.last_name,d.timing_id,d.timing_period')->from('audit_tasks_remark a')->join('audit_tasks b','a.taskid=b.task_id','left')->join('system_users c','a.addedby=c.user_id','left')->join('timing_slot d','b.tentative_time=d.timing_id','left');
		$this->db->where('a.taskid',$this->uri->segment(3));
		$this->db->order_by('a.id','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			
			$addeddate = date('Y-m-d', strtotime($row->addedOn));
			$time = date('H:i:s', strtotime($row->addedOn));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 	
			$Audit_section_data[] = array('sr_no'=>$i,
			'tentative_time'=>$row->timing_period,
			'added_on'=>$addeddate.$addedtime,
			'remarks'=>$row->remarks,
			'addedby'=>$row->first_name." ".$row->last_name
			);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($Audit_section_data),
			"iTotalDisplayRecords" => count($Audit_section_data),
			"aaData"=>$Audit_section_data);
			
		echo json_encode($results);
	}
	
	
	public function View_complete_report_by_category(){
		$this->load->view('audit_report/view_complete_report_by_category');
	}
	
	public function complete_audit_report_view()
	{
		$Audit_section_data = array();
		$i=1;
		$this->db->select('a.*,b.task_id,b.tentative_time,b.process_name, b.duration_id,c.user_id, c.first_name, c.last_name,d.audit_section_id, d.audit_section, t.timing_id, t.timing_period')->from('audit_tasks_remark a')->join('audit_tasks b','a.taskid=b.task_id','left')->join('system_users c','a.addedby=c.user_id','left')->join('audit_section d','a.typeid=d.audit_section_id','left')->join('timing_slot t','b.tentative_time=t.timing_id','left');
		$this->db->where('a.section_id',$this->uri->segment(3));
		$this->db->order_by('a.addedOn','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
		    
		 
		 $scheduledtime = "";
			$tat_id = $row->tentative_time;
			$duration_id = $row->duration_id;
			/* For Daily*/
			if($tat_id=='1'){
				$query = $this->db->select('id, timing')->from('daily_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $daily)
				$scheduledtime = date('h:i A',strtotime($daily->timing));
				
			}
			/* For Daily*/
			/* For Twice in a week*/
			if($tat_id=='2'){
				$query = $this->db->select('id, first_day, second_day')->from('twice_in_a_week_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $twice_in_a_week)
				$scheduledtime = $twice_in_a_week->first_day." and ".$twice_in_a_week->second_day;
				
			}
			/* For Twice in a week*/
			/* Weekly*/
			if($tat_id=='3'){
				$query = $this->db->select('id, weekday')->from('weekly_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $weekly)
				$scheduledtime = $weekly->weekday;
				
			}
			/* Weekly*/
			/* Twice in a Month*/
			if($tat_id=='4'){
				$query = $this->db->select('id, first_date, second_date')->from('twice_in_a_month_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $twice_in_a_month)
				$scheduledtime = $twice_in_a_month->first_date." and ".$twice_in_a_month->second_date." In every Month";
				
			}
			/* Twice in a Month*/
			/* Monthly*/
			if($tat_id=='5'){
				$query = $this->db->select('id, monthly')->from('monthly_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $monthly)
				$scheduledtime = $monthly->monthly;
				
			}
			/*Monthly*/
			/* quarterly_tat*/
			if($tat_id=='6'){
				$query = $this->db->select('id, month')->from('quarterly_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $quarterly_tat)
				$scheduledtime = $quarterly_tat->month;
				
			}
			/*quarterly_tat*/
			
			/* twice_in_a_year_tat*/
			if($tat_id=='7'){
				$query = $this->db->select('id, first_month, second_month')->from('twice_in_a_year_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $twice_in_a_year_tat)
				$scheduledtime = $twice_in_a_year_tat->first_month." and ".$twice_in_a_year_tat->second_month;
				
			}
			/*twice_in_a_year_tat*/
			/* yearly_tat*/
			if($tat_id=='8'){
				$query = $this->db->select('id, yearly')->from('yearly_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $yearly_tat)
				$scheduledtime = $yearly_tat->yearly;
				
			}
			/*twice_in_a_year_tat*/
			/* twice_in_a_day_tat*/
			if($tat_id=='9'){
				$query = $this->db->select('id, second_time, first_time')->from('twice_in_a_day_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $twice_in_a_day_tat)
				$scheduledtime = date('h:i A',strtotime($twice_in_a_day_tat->first_time))." and ".date('h:i A',strtotime($twice_in_a_day_tat->second_time));
				
			}
			/*twice_in_a_year_tat*/
			
			$addeddate = date('Y-m-d', strtotime($row->addedOn));
			$time = date('H:i:s', strtotime($row->addedOn));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 	
			$Audit_section_data[] = array('sr_no'=>$i,
			'audit_section'=>$row->audit_section,
			'process_name'=>$row->process_name,
			'tentative_time'=>$row->timing_period,
			'scheduledtime'=>$scheduledtime,
			'added_on'=>$addeddate.$addedtime,
			'remarks'=>$row->remarks,
			'addedby'=>$row->first_name." ".$row->last_name
			);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($Audit_section_data),
			"iTotalDisplayRecords" => count($Audit_section_data),
			"aaData"=>$Audit_section_data);
			
		echo json_encode($results);
	}
	
	public function view_data(){
		
	
	$fromdate = $this->input->post('from_date');
	if($fromdate==''){
		$from_date = "NA";
	}else{
		$from_date = $fromdate;
	}
	$todate = $this->input->post('to_date');
	if($todate==''){
		$to_date = "NA";
	}else{
		$to_date = $todate;
	}
	$data['start_date'] = $from_date;
	$data['end_date'] = $to_date;
	
		$this->load->view('audit_report/filtered_data',$data);
	}
	
	public function view_filtered_report_by_date()
	{
		$Audit_section_data = array();
		$i=1;
		$this->db->select('a.*,b.task_id,b.tentative_time,b.process_name, b.duration_id,c.user_id, c.first_name, c.last_name,d.audit_section_id, d.audit_section, t.timing_id, t.timing_period')->from('audit_tasks_remark a')->join('audit_tasks b','a.taskid=b.task_id','left')->join('system_users c','a.addedby=c.user_id','left')->join('audit_section d','a.typeid=d.audit_section_id','left')->join('timing_slot t','b.tentative_time=t.timing_id','left');
		$this->db->where('a.section_id',$this->uri->segment(3));
		$start_date = $this->uri->segment(4);
		$end_date = $this->uri->segment(5);
		if($start_date=='NA' && $end_date=='NA'){}else{
		$this->db->where("a.addedOn BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'");
		}
		$this->db->order_by('a.addedOn','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
		     $scheduledtime = "";
			$tat_id = $row->tentative_time;
			$duration_id = $row->duration_id;
			/* For Daily*/
			if($tat_id=='1'){
				$query = $this->db->select('id, timing')->from('daily_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $daily)
				$scheduledtime = date('h:i A',strtotime($daily->timing));
				
			}
			/* For Daily*/
			/* For Twice in a week*/
			if($tat_id=='2'){
				$query = $this->db->select('id, first_day, second_day')->from('twice_in_a_week_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $twice_in_a_week)
				$scheduledtime = $twice_in_a_week->first_day." and ".$twice_in_a_week->second_day;
				
			}
			/* For Twice in a week*/
			/* Weekly*/
			if($tat_id=='3'){
				$query = $this->db->select('id, weekday')->from('weekly_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $weekly)
				$scheduledtime = $weekly->weekday;
				
			}
			/* Weekly*/
			/* Twice in a Month*/
			if($tat_id=='4'){
				$query = $this->db->select('id, first_date, second_date')->from('twice_in_a_month_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $twice_in_a_month)
				$scheduledtime = $twice_in_a_month->first_date." and ".$twice_in_a_month->second_date." In every Month";
				
			}
			/* Twice in a Month*/
			/* Monthly*/
			if($tat_id=='5'){
				$query = $this->db->select('id, monthly')->from('monthly_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $monthly)
				$scheduledtime = $monthly->monthly;
				
			}
			/*Monthly*/
			/* quarterly_tat*/
			if($tat_id=='6'){
				$query = $this->db->select('id, month')->from('quarterly_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $quarterly_tat)
				$scheduledtime = $quarterly_tat->month;
				
			}
			/*quarterly_tat*/
			
			/* twice_in_a_year_tat*/
			if($tat_id=='7'){
				$query = $this->db->select('id, first_month, second_month')->from('twice_in_a_year_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $twice_in_a_year_tat)
				$scheduledtime = $twice_in_a_year_tat->first_month." and ".$twice_in_a_year_tat->second_month;
				
			}
			/*twice_in_a_year_tat*/
			/* yearly_tat*/
			if($tat_id=='8'){
				$query = $this->db->select('id, yearly')->from('yearly_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $yearly_tat)
				$scheduledtime = $yearly_tat->yearly;
				
			}
			/*twice_in_a_year_tat*/
			/* twice_in_a_day_tat*/
			if($tat_id=='9'){
				$query = $this->db->select('id, second_time, first_time')->from('twice_in_a_day_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $twice_in_a_day_tat)
				$scheduledtime = date('h:i A',strtotime($twice_in_a_day_tat->first_time))." and ".date('h:i A',strtotime($twice_in_a_day_tat->second_time));
				
			}
			/*twice_in_a_year_tat*/
			
			
			
			$addeddate = date('Y-m-d', strtotime($row->addedOn));
			$time = date('H:i:s', strtotime($row->addedOn));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 	
			$Audit_section_data[] = array('sr_no'=>$i,
			'audit_section'=>$row->audit_section,
			'process_name'=>$row->process_name,
			'tentative_time'=>$row->timing_period,
			'scheduledtime'=>$scheduledtime,
			'added_on'=>$addeddate.$addedtime,
			'remarks'=>$row->remarks,
			'addedby'=>$row->first_name." ".$row->last_name
			);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($Audit_section_data),
			"iTotalDisplayRecords" => count($Audit_section_data),
			"aaData"=>$Audit_section_data);
			
		echo json_encode($results);
	}
	
	public function select_tat_durations()
	{
		
	echo "<option value=''>--Select Duration--</option>";
	
	$tat_id = $this->input->post('tentative_time');
	/* Daily TAT*/
	if($tat_id=='1'){
	$query = $this->db->select('id,timing,status')->from('daily_tat')->where('status','1')->get();
	
			foreach($query->result() as $duration)
			{
				echo "<option value='".$duration->id."'>".$duration->timing."</option>";
				}
	}
	/* Daily TAT*/
	
	/* Twice in a Week TAT*/
	if($tat_id=='2'){
	$query = $this->db->select('id,first_day,status,second_day')->from('twice_in_a_week_tat')->where('status','1')->get();
	
			foreach($query->result() as $duration)
			{
				echo "<option value='".$duration->id."'>".$duration->first_day." -- And -- ".$duration->second_day."</option>";
				}
	}
	/* Twice in a Week TAT*/
	
	/* Weekly TAT*/
	if($tat_id=='3'){
	$query = $this->db->select('id,weekday,status')->from('weekly_tat')->where('status','1')->get();
	
			foreach($query->result() as $duration)
			{
				echo "<option value='".$duration->id."'>".$duration->weekday."</option>";
				}
	}
	/* Weekly TAT*/
	
	/* Twice in a month TAT*/
	if($tat_id=='4'){
	$query = $this->db->select('id,first_date,status,second_date')->from('twice_in_a_month_tat')->where('status','1')->get();
	
			foreach($query->result() as $duration)
			{
				echo "<option value='".$duration->id."'>".$duration->first_date." -- and -- ".$duration->second_date."</option>";
				}
	}
	/* Twice in a month TAT*/
	
	/* Monthly TAT*/
	if($tat_id=='5'){
	$query = $this->db->select('id,monthly,status')->from('monthly_tat')->where('status','1')->get();
	
			foreach($query->result() as $duration)
			{
				echo "<option value='".$duration->id."'>".$duration->monthly."</option>";
				}
	}
	/* Monthly TAT*/
	
	/* quarterly TAT*/
	if($tat_id=='6'){
	$query = $this->db->select('id,month,status')->from('quarterly_tat')->where('status','1')->get();
	
			foreach($query->result() as $duration)
			{
				echo "<option value='".$duration->id."'>".$duration->month."</option>";
				}
	}
	/* quarterly TAT*/
	
	/* Twice in a year TAT*/
	if($tat_id=='7'){
	$query = $this->db->select('id,first_month,second_month,status')->from('twice_in_a_year_tat')->where('status','1')->get();
	
			foreach($query->result() as $duration)
			{
				echo "<option value='".$duration->id."'>".$duration->first_month." -- and -- ".$duration->second_month."</option>";
				}
	}
	/* Twice in a year TAT*/
	
	/* Twice in a Yearly TAT*/
	if($tat_id=='8'){
	$query = $this->db->select('id,yearly,status')->from('yearly_tat')->where('status','1')->get();
	
			foreach($query->result() as $duration)
			{
				echo "<option value='".$duration->id."'>".$duration->yearly."</option>";
				}
	}
	/* Twice in a Yearly TAT*/
	
	/* Twice in a Twice in a day TAT*/
	if($tat_id=='9'){
	$query = $this->db->select('id,first_time,status,second_time')->from('twice_in_a_day_tat')->where('status','1')->get();
	
			foreach($query->result() as $duration)
			{
				echo "<option value='".$duration->id."'>".$duration->first_time." -- and -- ".$duration->second_time."</option>";
				}
	}
	/* Twice in a Twice in a day TAT*/
	
	
	
		}
		
		
		public function View_complete_report_by_status(){
			
		$this->load->view('audit_report/view_complete_report_by_status');
	}
	
	
	public function complete_audit_report_by_status()
	{
		$Audit_section_data = array();
		$i=1;
		$this->db->select('a.*,b.task_id,b.tentative_time,b.process_name, b.tentative_time, b.duration_id, c.user_id, c.first_name, c.last_name,d.audit_section_id, d.audit_section, t.timing_id, t.timing_period')->from('audit_tasks_remark a')->join('audit_tasks b','a.taskid=b.task_id','left')->join('system_users c','a.addedby=c.user_id','left')->join('audit_section d','a.typeid=d.audit_section_id','left')->join('timing_slot t','b.tentative_time=t.timing_id','left');
		$this->db->where('a.section_id',$this->uri->segment(3));
		$this->db->where('a.taskstatus',$this->uri->segment(4));
		$this->db->order_by('a.addedOn','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
		    
		     $scheduledtime = "";
			$tat_id = $row->tentative_time;
			$duration_id = $row->duration_id;
			/* For Daily*/
			if($tat_id=='1'){
				$query = $this->db->select('id, timing')->from('daily_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $daily)
				$scheduledtime = date('h:i A',strtotime($daily->timing));
				
			}
			/* For Daily*/
			/* For Twice in a week*/
			if($tat_id=='2'){
				$query = $this->db->select('id, first_day, second_day')->from('twice_in_a_week_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $twice_in_a_week)
				$scheduledtime = $twice_in_a_week->first_day." and ".$twice_in_a_week->second_day;
				
			}
			/* For Twice in a week*/
			/* Weekly*/
			if($tat_id=='3'){
				$query = $this->db->select('id, weekday')->from('weekly_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $weekly)
				$scheduledtime = $weekly->weekday;
				
			}
			/* Weekly*/
			/* Twice in a Month*/
			if($tat_id=='4'){
				$query = $this->db->select('id, first_date, second_date')->from('twice_in_a_month_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $twice_in_a_month)
				$scheduledtime = $twice_in_a_month->first_date." and ".$twice_in_a_month->second_date." In every Month";
				
			}
			/* Twice in a Month*/
			/* Monthly*/
			if($tat_id=='5'){
				$query = $this->db->select('id, monthly')->from('monthly_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $monthly)
				$scheduledtime = $monthly->monthly;
				
			}
			/*Monthly*/
			/* quarterly_tat*/
			if($tat_id=='6'){
				$query = $this->db->select('id, month')->from('quarterly_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $quarterly_tat)
				$scheduledtime = $quarterly_tat->month;
				
			}
			/*quarterly_tat*/
			
			/* twice_in_a_year_tat*/
			if($tat_id=='7'){
				$query = $this->db->select('id, first_month, second_month')->from('twice_in_a_year_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $twice_in_a_year_tat)
				$scheduledtime = $twice_in_a_year_tat->first_month." and ".$twice_in_a_year_tat->second_month;
				
			}
			/*twice_in_a_year_tat*/
			/* yearly_tat*/
			if($tat_id=='8'){
				$query = $this->db->select('id, yearly')->from('yearly_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $yearly_tat)
				$scheduledtime = $yearly_tat->yearly;
				
			}
			/*twice_in_a_year_tat*/
			/* twice_in_a_day_tat*/
			if($tat_id=='9'){
				$query = $this->db->select('id, second_time, first_time')->from('twice_in_a_day_tat')->where('id',$duration_id)->get();
				foreach($query->result() as $twice_in_a_day_tat)
				$scheduledtime = date('h:i A',strtotime($twice_in_a_day_tat->first_time))." and ".date('h:i A',strtotime($twice_in_a_day_tat->second_time));
				
			}
			/*twice_in_a_year_tat*/
			
			
			
			$addeddate = date('Y-m-d', strtotime($row->addedOn));
			$time = date('H:i:s', strtotime($row->addedOn));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 	
			$Audit_section_data[] = array('sr_no'=>$i,
			'audit_section'=>$row->audit_section,
			'process_name'=>$row->process_name,
			'tentative_time'=>$row->timing_period,
			'scheduledtime'=>$scheduledtime,
			'added_on'=>$addeddate.$addedtime,
			'remarks'=>$row->remarks,
			'addedby'=>$row->first_name." ".$row->last_name
			);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($Audit_section_data),
			"iTotalDisplayRecords" => count($Audit_section_data),
			"aaData"=>$Audit_section_data);
			
		echo json_encode($results);
	}
	
function update_hr_remarks1()
{
  
  $user_id =$this->session->userdata['logged_in']['user_id'];
  $sectionid=$this->uri->segment('3');
  date_default_timezone_set("Asia/Kolkata");
 

$checkeditem=$this->input->post('countall');
 //echo "<pre>"; print_r($checkeditem);exit;
 for($i=0;$i<count($checkeditem);$i++)
 {
	 $taskid=$checkeditem[$i];
	 $yno=$_REQUEST['pick_items'.$taskid];
	if($yno=='0')
	{
	 $taskstatus='0';
	}else
	{
		 $taskstatus='1';
	}
	
	 $report=$_FILES['report'.$taskid]['name'];
	 if($report<>'')
	 {
		$exty=explode('.',$report);
		$newname=$exty[0].'_'.date('d-y').'.'.$exty['1'];
		move_uploaded_file($_FILES["report".$taskid]["tmp_name"],UPLOADPATH.'auditreport/'.$newname);
		$data1=array('task_id'=>$taskid,'report_file'=>$newname,'added_on'=>date('Y-m-d h:i:s'),'added_by'=>$user_id);
		$this->db->insert('upload_hrreports',$data1);
	 }else
	 {
		 $newname='';
	 }
	
	
	 $que=$this->db->select('audit_type_id')->from('hr_audit_works')->where('task_id',$taskid)->get();
	 foreach($que->result() as $auditdata);
	 $typeid=$auditdata->audit_type_id;
	 $userremarks=$_REQUEST['rtrm'.$taskid];
	 
	 $data=array('section_id'=>$sectionid,'typeid'=>$typeid,'taskid'=>$taskid,'remarks'=>$userremarks,'addedby'=>$user_id,'addedOn'=>date('Y-m-d h:i:s'),'taskstatus'=>$taskstatus);
	$this->db->insert('audit_hr_tasks_remark',$data);
	
	$taskdata = array('remark_flag'=>'1');
	$this->db->where('task_id',$taskid);
	$this->db->update('hr_audit_works',$taskdata);
}
    redirect(page_url.'Audit_report/add_hr_work/'.$typeid);
	
}	


function pmsreport()
		{
			$this->load->view('audit_report/PMS_Dashboard');
		}
		
		function  departmentpms()
		{
			$this->load->view('audit_report/departmentpms');
		}
	
	
	function  hrdepartmentpms()
		{
			$this->load->view('audit_report/hrpms');
		}
		
		
	}
	
	
	