<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User_management extends CI_Controller {
	
	public function __construct()
	{

		if(!empty($this->session->userdata['logged_in']['smtpemailid'])){
			$smtpemail  = $this->session->userdata['logged_in']['smtpemailid'];
		}else{
			$smtpemail = '';
		}
		if(!empty($this->session->userdata['logged_in']['smtppassword'])){
			$smtppassword  = $this->session->userdata['logged_in']['smtppassword'];
		}else{
			$smtppassword='';
		}
        
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
		$this->load->library('Master_profile_guard');
		$this->apply_master_profile_guard();
        if(!empty($this->session->userdata['logged_in']['smtpemailid']) && !empty($this->session->userdata['logged_in']['smtppassword'])){
   		$config = array(
		'protocol' => 'smtp', 
		'smtp_host' => 'ssl://smtp.googlemail.com',
		'smtp_port' => 465,   	
		'smtp_user' => $smtpemail, 
		'smtp_pass' => $smtppassword, 
		'mailtype' => 'html', 
		'smtp_timeout'=>100,
		'charset' => 'iso-8859-1',
		'newline'=>"\r\n",
		'starttls'=>TRUE

		);
		$this->email->initialize($config);
		$this->email->set_mailtype("html");
		$this->email->set_newline("\r\n");
        }
     


	}

	private function apply_master_profile_guard()
	{
		$blocked_methods = array(
			'fetch_users',
			'fetch_marketing_users',
			'fetch_marketing_usersforedit',
			'index',
			'not_active_employees',
			'Departments',
			'add_department',
			'department_list',
			'update_department_status',
			'edit_department',
			'update_department',
			'user_role',
			'select_department',
			'add_user_role',
			'user_role_list',
			'update_user_role_status',
			'edit_user_role',
			'update_user_role',
			'select_user_roles',
			'add_new_user',
			'all_system_users_list',
			'all_not_active_system_users_list',
			'send_login_detail',
			'update_user_status',
			'delete_user',
			'edit_user_profile',
			'update_user_profile',
			'create_team',
			'team_list',
			'update_team_status',
			'edit_team',
			'update_team',
			'selete_team_leader',
			'select_team_member',
			'add_member_in_team',
			'add_members',
			'view_team_list',
			'company_team_list',
			'all_created_team',
			'google_sheet_link',
			'add_google_sheet_urls',
			'google_sheet_list_by_department',
			'view_all_google_sheet_link',
			'google_sheet_lists',
			'edit_googlesheet_link',
			'update_googlesheet_URL',
			'remove_member',
			'assigncapabilities',
			'getfmswiseuser',
			'getsortorder',
			'gettat',
			'getfmsinformation',
			'userwise_permission_dashboard',
			'all_system_user_list',
			'user_permission',
			'edit_module_access_permission',
			'edit_capablities',
			'reference',
			'reference_list',
			'sub_reference',
			'sub_reference_list',
			'update_reference_status',
			'update_sub_reference_status',
			'edit_reference',
			'update_reference',
			'add_new_vendor',
			'add_new_vendors',
			'add_new_vendorsOlddd',
			'vendor_list',
			'vendor_listOlddd',
			'edit_vendor',
			'update_vendor',
			'update_vendorOLdd',
			'email_template_list',
			'triggeremail',
			'edit_email_template',
			'update_email_template',
			'email_template',
			'add_email_template',
			'view_email_template',
			'sub_subreference',
			'sub_subreference_list',
			'update_sub_subreference_status',
			'edit_sub_subreference',
			'update_sub_subreference',
			'set_ims_header_permission',
			'edit_ims_header_permission',
			'add_vendor_via_ajax',
			'start_train',
			'machine_master',
			'machine_master_list',
			'create_machine_bom',
			'view_spare_parts_bom',
			'spare_parts_bom_list',
			'delete_spare_parts_bom',
			'add_machine_bom',
			'machine_master_status',
			'add_machine_master',
			'edit_machine_master',
			'spare_parts',
			'spare_parts_list',
			'add_spare_parts',
			'edit_spare_parts',
			'spare_parts_status',
			'get_spare_parts',
			'shortage_spare_parts',
			'add_shortage_spare_parts'
		);

		$this->master_profile_guard->block_methods(
			$blocked_methods,
			'This EA profile can use the software but cannot open or change master setup screens.'
		);
	}

	private function system_users_supports_project_coordinator()
	{
		return $this->db->field_exists('project_coordinator_user_id', 'system_users');
	}

	private function user_role_supports_master_write_access()
	{
		return $this->db->field_exists('master_write_access', 'user_role');
	}

	private function get_requested_master_write_access()
	{
		if (!$this->user_role_supports_master_write_access()) {
			return 1;
		}

		return $this->input->post('master_write_access') === '0' ? 0 : 1;
	}

	private function resolve_project_coordinator_user_id($business_location = null)
	{
		if (!$this->system_users_supports_project_coordinator()) {
			return null;
		}

		if ((int) $this->input->post('marketing_person') !== 1) {
			return null;
		}

		$project_coordinator_user_id = (int) $this->input->post('project_coordinator_user_id');
		if ($project_coordinator_user_id <= 0) {
			return null;
		}

		$this->db->select('user_id')->from('system_users');
		$this->db->where('user_id', $project_coordinator_user_id);
		$this->db->where('hide_profile', '0');
		$this->db->where('user_status', '1');
		if ($business_location !== null && $business_location !== '') {
			$this->db->where('business_location', $business_location);
		}

		$query = $this->db->get();
		if ($query->num_rows() === 0) {
			return null;
		}

		return $project_coordinator_user_id;
	}
	public function fetch_users()
	{
	echo "<option value=''>--Select User--</option>";
	$business_location = $this->input->post('business_loc');
	$query = $this->db->select('business_location, user_id, title, first_name, last_name, user_status')->from('system_users')->where('business_location',$business_location)->where('user_status','1')->get();
	
		$ajax_department = $query->result();
			foreach($ajax_department as $department)
			{
				echo "<option value=".$department->user_id.">".$department->title." ".$department->first_name." ".$department->last_name."</option>";
				}
		
		}
		
	public function fetch_marketing_users()
	{
	echo "<option value=''>--Select User--</option>";
	$business_location = $this->input->post('business_loc');
	$query = $this->db->select('a.userid, b.user_id, b.first_name, b.last_name')->from('saleszoneusers a')->join('system_users b','a.userid=b.user_id','left')->where('a.zoneid',$business_location)->where('b.user_status','1')->where('b.hide_profile','0')->get();
	
		$ajax_department = $query->result();
			foreach($ajax_department as $department)
			{
				echo "<option value=".$department->user_id.">".$department->title." ".$department->first_name." ".$department->last_name."</option>";
				}
		
		}
		
		
	public function fetch_marketing_usersforedit()
	{
	    $yuse=$this->input->post('markuser');
	    echo $yuse;exit; 
	echo "<option value=''>--Select User--</option>";
	$business_location = $this->input->post('business_loc');
	$query = $this->db->select('a.userid, b.user_id, b.first_name, b.last_name')->from('saleszoneusers a')->join('system_users b','a.userid=b.user_id','left')->where('a.zoneid',$business_location)->where('b.user_status','1')->where('b.hide_profile','0')->get();
	
		$ajax_department = $query->result();
			foreach($ajax_department as $department)
			{
			    if($department->user_id==$yuse)
			    {
    			        $a="selected";
			    }else
			    {
			          $a="";
			    }
				echo "<option value='".$department->user_id."' ".$a.">".$department->first_name." ".$department->last_name."</option>";
				}
		
		}
	public function index()
	{
		
			$this->load->view('master/user_list');
	}
	
		public function not_active_employees()
	{
		$logged_in = $this->session->userdata('logged_in');
		$business_location = !empty($logged_in['business_location']) ? $logged_in['business_location'] : '';

		$this->db->select('a.user_id,a.first_name,a.last_name,a.email,a.password,a.contact_number,a.alternate_number,a.profile_image,a.business_location,a.department_id,a.user_role_id,a.user_status,a.hide_profile,b.business_loc_id,b.company_name,c.department_id,c.department,d.user_role_id,d.user_role')->from('system_users a');
		$this->db->join('business_location b','a.business_location=b.business_loc_id','left');
		$this->db->join('departments c','a.department_id=c.department_id','left');
		$this->db->join('user_role d','a.user_role_id=d.user_role_id','left');
		if($business_location !== '')
		{
			$this->db->where('a.business_location',$business_location);
		}
		$this->db->where('a.hide_profile','0');
		$this->db->where('a.user_status','0');
		$this->db->order_by('a.first_name','ASC');
		$data['inactive_users'] = $this->db->get()->result();

		$this->load->view('master/not_active_users',$data);
	}
	
	public function Departments(){
		
			$this->load->view('master/departments');
	}
	
	public function add_department()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('business_loc', 'Business Location', 'required|trim');
		$this->form_validation->set_rules('department_name', 'Department Name', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/departments');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "departments";
		$query = $this->db->select('business_loc_id, department')->from('departments')->where('business_loc_id',$this->input->post('business_loc'))->where('department',$this->input->post('department_name'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Sorry! This record already exist.</div>');
			redirect(page_url.'Master/User_management/Departments');
			
		}else{
		
		
			
			
			$data = array('business_loc_id'=>$this->input->post('business_loc'),
			'department'=>$this->input->post('department_name'),
			'status'=>$this->input->post('status'),
			'assign_delegation'=>$this->input->post('assign_delegation'),
			'show_in_master_index'=>$this->input->post('show_in_master_index'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Master/User_management/Departments');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/User_management/Departments');
		}
		}
		
	}
		
	}
	public function department_list()
	{
		$i=1;
		$business_location = $this->session->userdata['logged_in']['business_location'];
		$department_data= array();
		$this->db->select('a.*,b.business_loc_id,company_name, s.first_name, s.last_name')->from('departments a');
		$this->db->join('business_location b','a.business_loc_id=b.business_loc_id','left');
		$this->db->join('system_users s','a.departmenthead=s.user_id','left');
		$this->db->where('a.business_loc_id',$business_location);
		$this->db->order_by('b.company_name','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Master/User_management/update_department_status/".$row->department_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Master/User_management/update_department_status/".$row->department_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Master/User_management/edit_department/".$row->department_id."'><i class='fa fa-pencil'></i></a>";	
			$add = "<a href='".page_url."Master/User_management/google_sheet_link/".$row->department_id."'><span class='btn btn-warning'><i class='fa fa-google'></i> Add google sheet URL</span></a>";
			
			$show_in_master_index = $row->show_in_master_index;
			if($show_in_master_index=='1'){
			    $show = "Yes";
			}else{
			   $show = "No"; 
			}
			
			
			$matrialupload="<a href='".page_url."LMS/listlms/".$row->department_id."'><span class='btn btn-info btn-xs'>Add/Edit Material</span></a>";
			
			$department_data[] = array('sr_no'=>$i,
			'company_name'=>strtoupper($row->company_name),
			'department'=>$row->department,
            'google'=>$add,
            'departmenthead'=>$row->first_name." ".$row->last_name,
            'show_in_master_index'=>$show,
            'matrialupload'=>$matrialupload,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($department_data),
			"iTotalDisplayRecords" => count($department_data),
			"aaData"=>$department_data);
			
		echo json_encode($results);
	}
	
	public function update_department_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(4);
		$sval =  $this->uri->segment(5);
		$field_name = "department_id";
		$table = "departments";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Status successfully updated.</div>');
			redirect(page_url.'Master/User_management/Departments');
		}

	public function edit_department(){
		$this->load->view('master/edit_department');
		
	}	
	
	public function update_department()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('business_loc', 'Business Location', 'required|trim');
		$this->form_validation->set_rules('department_name', 'Department Name', 'required|trim');
		$this->form_validation->set_rules('departmenthead', 'Department Head', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/edit_department');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "departments";
		
		
		
			$data = array('business_loc_id'=>$this->input->post('business_loc'),
			'department'=>$this->input->post('department_name'),
			'status'=>$this->input->post('status'),
			'show_in_master_index'=>$this->input->post('show_in_master_index'),
			'added_on'=>$date,
			'departmenthead'=>$this->input->post('departmenthead'),
			'assign_delegation'=>$this->input->post('assign_delegation'),
			'added_by'=>$user_id,
			'updated_on'=>$date,
			'updated_by'=>$user_id);
			$this->db->where('department_id',$this->uri->segment(4));
		$result  = $this->db->update($table,$data);	


		$table1 ="multiple_department_add";
		$select_departments = $this->input->post('related_department_name');

		// echo "<pre>"; print_r($select_departments); exit;

		$deptt_id = $this->uri->segment(4);

		$this->db->where('dept_id', $deptt_id);
$this->db->delete($table1);

		if(count($select_departments)>0){



			foreach($select_departments as $depart_id){

				$data1 =array(

					'dept_id' => $deptt_id,
'related_department_name'=>$depart_id,
'added_on'=>$date,
'added_by'=>$user_id
				);

				$result1 = $this->db->insert($table1, $data1);

			}



		}



		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Master/User_management/Departments');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/User_management/Departments');
		}
		
		
	}
		
	}
	public function user_role(){
		
			$this->load->view('master/user_role');
		
	}
	
	public function select_department()
	{
	echo "<option value=''>--Select Department--</option>";
	$business_location = $this->input->post('business_loc');
		$ajax_department = $this->master->select_department($business_location);
			foreach($ajax_department as $department)
			{
				echo "<option value=".$department->department_id.">".$department->department."</option>";
				}
		
		}
	public function add_user_role()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('business_loc', 'Business Location', 'required|trim');
		$this->form_validation->set_rules('department', 'Department Name', 'required|trim');
		$this->form_validation->set_rules('user_role', 'User Role', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/user_role');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "user_role";
		$query = $this->db->select('business_loc_id, department_id, user_role')->from('user_role')->where('business_loc_id',$this->input->post('business_loc'))->where('department_id',$this->input->post('department'))->where('user_role',$this->input->post('user_role'))->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry! This record already exist.</div>');
			redirect(page_url.'Master/User_management/user_role');
			
		}else{
		
		
			$data = array('business_loc_id'=>$this->input->post('business_loc'),
			'department_id'=>$this->input->post('department'),
			'user_role'=>$this->input->post('user_role'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			if ($this->user_role_supports_master_write_access()) {
				$data['master_write_access'] = $this->get_requested_master_write_access();
			}
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Master/User_management/user_role');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div>');
			redirect(page_url.'Master/User_management/user_role');
		}
		}
		
	}
		
	}	

	public function user_role_list()
	{
		$i=1;
		$business_location = $this->session->userdata['logged_in']['business_location'];
		$department_data = array();
		$this->db->select('a.*,b.business_loc_id,b.company_name,c.department_id, c.department')->from('user_role a');
		$this->db->join('business_location b','a.business_loc_id=b.business_loc_id','left');
		$this->db->join('departments c','a.department_id=c.department_id','left');
		$this->db->where('a.business_loc_id',$business_location);
		$this->db->order_by('b.company_name','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Master/User_management/update_user_role_status/".$row->user_role_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Master/User_management/update_user_role_status/".$row->user_role_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Master/User_management/edit_user_role/".$row->user_role_id."'><i class='fa fa-pencil'></i></a>";	
			$idset=$this->db->select('*')->from('module_access')->where('role_id',$row->user_role_id)->get();
			$ifset=$idset->num_rows();
			if($ifset=='0')
			{
			$set_permission = "<a href='".page_url."User/user_permission/".$row->user_role_id."'><span class='btn btn-warning btn-xs'>Set Permission</span></a>";	
			}else
			{
		$set_permission = "<a href='".page_url."User/edit_permission/".$row->user_role_id."'><span class='btn btn-primary btn-xs'>Edit Permission</span></a>";	
			}	
			$master_access_mode = 'MASTER CHANGES ALLOWED';
			if ($this->user_role_supports_master_write_access() && isset($row->master_write_access) && (string)$row->master_write_access === '0') {
				$master_access_mode = 'VIEW ONLY (EA)';
			}
			$department_data[] = array('sr_no'=>$i,
			'company_name'=>strtoupper($row->company_name),
			'department'=>strtoupper($row->department),
			'user_role'=>strtoupper($row->user_role),
			'master_access_mode'=>$master_access_mode,
			'permission'=>'',
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($department_data),
			"iTotalDisplayRecords" => count($department_data),
			"aaData"=>$department_data);
			
		echo json_encode($results);
	}
	
	public function update_user_role_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(4);
		$sval =  $this->uri->segment(5);
		$field_name = "user_role_id";
		$table = "user_role";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Status successfully updated.</div>');
			redirect(page_url.'Master/User_management/user_role');
		}
	
	public function edit_user_role(){
		$this->load->view('master/edit_user_role');
		
	}	
	
	public function update_user_role()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('business_loc', 'Business Location', 'required|trim');
		$this->form_validation->set_rules('department', 'Department Name', 'required|trim');
		$this->form_validation->set_rules('user_role', 'User Role', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/edit_user_role');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "user_role";
		$query = $this->db->select('business_loc_id, department_id, user_role')
			->from('user_role')
			->where('business_loc_id',$this->input->post('business_loc'))
			->where('department_id',$this->input->post('department'))
			->where('user_role',$this->input->post('user_role'))
			->where('user_role_id !=', $this->uri->segment(4))
			->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry! This record already exist.</div>');
			redirect(page_url.'Master/User_management/user_role');
			
		}else{
		
		$data = array('business_loc_id'=>$this->input->post('business_loc'),
			'department_id'=>$this->input->post('department'),
			'user_role'=>$this->input->post('user_role'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id,
			'updated_on'=>$date,
			'updated_by'=>$user_id);
			if ($this->user_role_supports_master_write_access()) {
				$data['master_write_access'] = $this->get_requested_master_write_access();
			}
			$this->db->where('user_role_id',$this->uri->segment(4));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Master/User_management/user_role');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/User_management/user_role');
		}
		}
		
	}
		
	}	
	public function select_user_roles()
	{
	echo "<option value=''>--Select User Role--</option>";
	$department = $this->input->post('department');
		$ajax_user_role = $this->master->select_user_roles($department);
			foreach($ajax_user_role as $userrole)
			{
				echo "<option value=".$userrole->user_role_id.">".$userrole->user_role."</option>";
				}
		
		}
	
	
	
	public function add_new_user()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('first_name', 'First Name', 'required|trim');
		$this->form_validation->set_rules('last_name', 'Last Name', 'required|trim');
		$this->form_validation->set_rules('email', 'Email ID', 'required|trim');
		$this->form_validation->set_rules('contact_number', 'Contact Number', 'required|trim');
			//$this->form_validation->set_rules('adharcard ', 'Aadhar Card', 'required|trim');
		$this->form_validation->set_rules('business_loc', 'Business Location', 'required|trim');
		$this->form_validation->set_rules('department', 'Department', 'required|trim');
		$this->form_validation->set_rules('user_role', 'User Role', 'required|trim');
			$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/user_list');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "system_users";
		$adharcard=$_FILES['adharcard']['name'];
		if($adharcard<>'')
		{
			$image1=explode('.',$adharcard);
			$cat_image=end($image1);
			$aadhar_card=time().'.'.$cat_image;
			move_uploaded_file($_FILES["adharcard"]["tmp_name"],UPLOADPATH.'users/document/' . $aadhar_card);
		}else
		{
			$aadhar_card="";
			}
		
		$profilephoto=$_FILES['photo']['name'];
		if($profilephoto<>'')
		{
			$image2=explode('.',$profilephoto);
			$cat_image1=end($image2);
			$profile_photo=time().'.'.$cat_image1;
			move_uploaded_file($_FILES["photo"]["tmp_name"],UPLOADPATH.'users/' . $profile_photo);
		}else
		{
			$profile_photo="";
			}
			
		$relieving_letter=$_FILES['relieving_letter']['name'];
		if($relieving_letter<>'')
		{
			$image3=explode('.',$relieving_letter);
			$cat_image2=end($image3);
			$relieving_letterfile=time().'.'.$cat_image2;
			move_uploaded_file($_FILES["relieving_letter"]["tmp_name"],UPLOADPATH.'users/' . $relieving_letterfile);
		}else
		{
			$relieving_letterfile="";
			}
		
		$joining_letter=$_FILES['joining_letter']['name'];
		if($joining_letter<>'')
		{
			$image4=explode('.',$joining_letter);
			$cat_image3=end($image4);
			$joining_letterfile=time().'.'.$cat_image3;
			move_uploaded_file($_FILES["joining_letter"]["tmp_name"],UPLOADPATH.'users/' . $joining_letterfile);
		}else
		{
			$joining_letterfile="";
			}
			
			
			$km_type=0;
			$convence=$this->input->post('convence');
			if($convence==1)
			{
				$con_type=$this->input->post('con_type');
				if($con_type==1)
				{
					$km_type=$this->input->post('crate');
				}
		}else
		{
			$convence=0;
			$con_type=0;
			$km_type=0;
		}

		$project_coordinator_user_id = $this->resolve_project_coordinator_user_id($this->input->post('business_loc'));

				
			$rand_number  = mt_rand(15, 50);
		$password = "SPM-".$rand_number;
			$data = array('first_name'=>$this->input->post('first_name'),
			'last_name'=>$this->input->post('last_name'),
			'email'=>$this->input->post('email'),
			'password'=>$password,
			'contact_number'=>$this->input->post('contact_number'),
			'alternate_number'=>$this->input->post('alternate_number'),
			'father_name'=>$this->input->post('father_name'),
			'mother_name'=>$this->input->post('mother_name'),
			'date_of_birth'=>date('Y-m-d',strtotime($this->input->post('date_of_birth'))),
			'date_of_joining'=>date('Y-m-d',strtotime($this->input->post('date_of_joining'))),
			'qualification'=>$this->input->post('qualification'),
			'aadharcard'=>$aadhar_card,
			'pancard'=>$this->input->post('pancard'),
			'profile_image'=>$profile_photo,
			'business_location'=>$this->input->post('business_loc'),
			'department_id'=>$this->input->post('department'),
			'user_role_id'=>$this->input->post('user_role'),
			'address'=>$this->input->post('address'),
			'user_status'=>$this->input->post('status'),
			'added_on'=>$date,
			'last_updated_on'=>$date,
			'hide_profile'=>'0',
			'fms_process'=>'0',
			'assign_delegation'=>$this->input->post('assign_delegation'),
			'aadhar_number'=>$this->input->post('adhaar_number'),
			'blood_group'=>$this->input->post('blood_group'),
			'spouce_name'=>$this->input->post('spouce_name'),
			'spouce_company_name'=>$this->input->post('spouce_working_in'),
			'children_name'=>$this->input->post('children_names'),
			'added_by'=>$user_id,
			'employee_code'=>$this->input->post('employee_code'),
			'nominee_name'=>$this->input->post('nominee_name'),
			'date_of_leaving'=>date('Y-m-d',strtotime($this->input->post('date_of_leaving'))),
			'basic_salary'=>$this->input->post('basic_salary'),
			'gross_salary'=>$this->input->post('gross_salary'),
			'ctc'=>$this->input->post('ctc'),
			'uia_no'=>$this->input->post('uia_no'),
			'pf_no'=>$this->input->post('pf_no'),
			'bank_acc_detail'=>$this->input->post('bank_acc_detail'),
			'present_address'=>$this->input->post('present_address'),
			'joining_letter'=>$joining_letterfile,
			'relieving_letter'=>$relieving_letterfile,
			'marketing_person'=>$this->input->post('marketing_person'),
			'salestarget'=>$this->input->post('sellingtarget'),
			'assign_call_delegation'=>$this->input->post('assign_call_delegation'),
			'payment_grace_period'=>$this->input->post('payment_grace_period'),
			'convence'=>$convence,
			'convence_type'=>$con_type,
			'convence_rate'=>$km_type,
			'paymenttarget'=>$this->input->post('paymenttarget'));
		if ($this->system_users_supports_project_coordinator()) {
			$data['project_coordinator_user_id'] = $project_coordinator_user_id;
		}
		//echo "<pre>"; print_r($data); exit;
		$result  = $this->db->insert($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Master/User_management/');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div>');
			redirect(page_url.'Master/User_management/');
		}
	
		
	}
		
	}	
	
	
public function all_system_users_list()
	{
	$user_data = array();
	$business_location = $this->session->userdata['logged_in']['business_location'];	
		$this->db->select('a.start_training,a.fms_process,a.user_id,a.employeecode,a.first_name, a.last_name, a.email , a.password,a.contact_number, a.alternate_number, a.profile_image, a.business_location, a.department_id, a.user_role_id, a.user_status, a.hide_profile,b.business_loc_id,b.company_name,c.department_id,c.department,d.user_role_id,d.user_role')->from('system_users a');
		$this->db->join('business_location b','a.business_location=b.business_loc_id','left');
		$this->db->join('departments c','a.department_id=c.department_id','left');
		$this->db->join('user_role d','a.user_role_id=d.user_role_id','left');
		$this->db->where('a.business_location',$business_location);
		$this->db->where('a.hide_profile','0');
		$this->db->where('user_status','1');
		$query = $this->db->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;									
		$i=1;
		
		foreach($res as $row)
		{
			/** FMS Process **/
			
			if($row->employeecode==''){
			    $generatecode = "<a href='".page_url."Master/User_management/generateemployeecode/".$row->user_id."'><span class='btn btn-success btn-xs'>generate code</span></a>";
			}else{
			    $generatecode=$row->employeecode;
			}
			
			$fmsassined="NA";
				
			
			/** End **/
			$status = $row->user_status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Master/User_management/update_user_status/".$row->user_id."/".$row->user_status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Master/User_management/update_user_status/".$row->user_id."/".$row->user_status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
				$edit = "<a href='".page_url."Master/User_management/edit_user_profile/".$row->user_id."'><i class='fa fa-pencil' title='Edit Profile'></i></a>";
				$view = "<a href='".page_url."User/view_profile/".$row->user_id."'><i class='fa fa-eye' title='View Profile'></i></a>";
				
				$delete = "<a href='".page_url."Master/User_management/delete_user/".$row->user_id."'><i class='fa fa-trash' title='Remove User Profile' ></i></a>";
			$notify = "<a href='".page_url."Master/User_management/send_login_detail/".$row->user_id."'><span class='btn btn-warning btn-xs'>Send Login Detail</span></a>";
			if($row->profile_image){
			$img = "<img src='".user_profile.$row->profile_image."' width='50px' height='50px'  class='img-responsive'>";
			}else{
			$img = "<img src='".user_profile."userplaceholder.jpeg' width='50px' height='50px'  class='img-responsive'>";	
			}
			
			
			$loginasuser='<a href="'.page_url.'User/access_user_dashboard/'.$row->user_id.'" class="btn btn-success btn-xs" taget="_blank">Click to Login</a>';
			
			
			
			$changepassword=' <button class="btn btn-warning btn-xs waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$i.'">Change Password</button>';
$changepassword.= '<div id="con-close-modal'.$i.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
           <form id="loginForm" method="post" action="'.page_url.'Master/User_management/update_your_password/'.$row->user_id.'">
  
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Change Password</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                               <div class="col-md-6 col-lg-6">
													<div class="form-group">
														<label for="field-1" class="control-label">Password</label><br>
													<input type="text" class="form-control" name="password" id="password" value="">
													</div> 
												</div>
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>';
                            
                            $st_train=$row->start_training;
			//echo $st_train; exit;
			if($st_train==1)
			{
				$starttrain='<a href="'.page_url.'Master/User_management/start_train/'.$row->user_id.'/'.$row->start_training.'" class="btn btn-success btn-xs" taget="_blank">Goes Training</a>';
			}
			else{
				$starttrain='<a href="'.page_url.'Master/User_management/start_train/'.$row->user_id.'/'.$row->start_training.'" class="btn btn-danger btn-xs" taget="_blank">Start Training</a>';
				}

			$customize='<a href="'.page_url.'Dashboard/dashboard_access/'.$row->user_id.'" class="btn btn-danger btn-xs" target="_blank">Customize</a>';

			$open_lead_link = page_url.'Open_leads/open_lead/'.base64_encode($row->user_id);

			$open_lead = '<input type="text" class="form-control" value="'.$open_lead_link.'" id="myInput'.$row->user_id.'" readonly>
						 <button onclick="myFunction('.$row->user_id.')" >Copy</button>';
			// $open_lead = "<input type='text' class='form-control click' id='user_id".$row->user_id."' value='".$open_lead_link."'><button class='copy' onclick='copyToClipboard(".$row->user_id.")'>Copy text</button>";
			 $cheque_collection_link = page_url.'Open_leads/payment_form/'.base64_encode($row->user_id);
			 $cheque_collection = '<input type="text" class="form-control" value="'.$cheque_collection_link.'" id="myCollection'.$row->user_id.'" readonly>
			 <button onclick="myFunctions('.$row->user_id.')" >Copy</button>';
				
			$user_data[] = array('sr_no'=>$i,
			'profile_image'=>$img,
			'first_name'=>strtoupper($row->first_name." ".$row->last_name),
			'training'=>'',
			'email'=>strtoupper($row->email),
			'password'=>$changepassword,
			'viewpassword'=>$row->password,
			'contact_number'=>$row->contact_number."<br>".$row->alternate_number,
			'company_name'=>strtoupper($row->company_name),
			'department'=>strtoupper($row->department),
			'user_role'=>strtoupper($row->user_role),
			'generatecode'=>$generatecode,
			'notify'=>$notify,
			'open_lead'=>$open_lead,
			'cheque_collection'=>$cheque_collection,
			'customize'=>$customize,
			'status'=>$sta,
			'login'=>$loginasuser,
			'fmsp'=>$fmsassined,
			'edit'=>$edit." l ".$delete." l ".$view);
			$i++;
		}
		//echo "<pre>"; print_r($business_data); exit;
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($user_data),
			"iTotalDisplayRecords" => count($user_data),
			"aaData"=>$user_data);
			
		echo json_encode($results);
	}
	
	
	public function all_not_active_system_users_list()
	{
	$user_data = array();
	$logged_in = $this->session->userdata('logged_in');
	$business_location = !empty($logged_in['business_location']) ? $logged_in['business_location'] : '';
		$this->db->select('a.fms_process,a.user_id,a.first_name, a.last_name, a.email , a.password,a.contact_number, a.alternate_number, a.profile_image, a.business_location, a.department_id, a.user_role_id, a.user_status, a.hide_profile,b.business_loc_id,b.company_name,c.department_id,c.department,d.user_role_id,d.user_role')->from('system_users a');
		$this->db->join('business_location b','a.business_location=b.business_loc_id','left');
		$this->db->join('departments c','a.department_id=c.department_id','left');
		$this->db->join('user_role d','a.user_role_id=d.user_role_id','left');
		if($business_location !== '')
		{
			$this->db->where('a.business_location',$business_location);
		}
		$this->db->where('a.hide_profile','0');
		$this->db->where('a.user_status','0');
		$query = $this->db->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;									
		$i=1;
		
		foreach($res as $row)
		{
			$status = $row->user_status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Master/User_management/update_user_status/".$row->user_id."/".$row->user_status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Master/User_management/update_user_status/".$row->user_id."/".$row->user_status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
				$edit = "<a href='".page_url."Master/User_management/edit_user_profile/".$row->user_id."'><i class='fa fa-pencil' title='Edit Profile'></i></a>";
				$view = "<a href='".page_url."User/view_profile/".$row->user_id."'><i class='fa fa-eye' title='View Profile'></i></a>";
				
				$delete = "<a href='".page_url."Master/User_management/delete_user/".$row->user_id."'><i class='fa fa-trash' title='Remove User Profile' ></i></a>";
			$notify = "<a href='".page_url."Master/User_management/send_login_detail/".$row->user_id."'><span class='btn btn-warning btn-xs'>Send Login Detail</span></a>";

			if($row->profile_image){
			$img = "<img src='".user_profile.$row->profile_image."' width='50px' height='50px' class='img-responsive'>";
			}else{
			$img = "<img src='".user_profile."userplaceholder.jpeg' width='50px' height='50px' class='img-responsive'>";
			}

			$user_data[] = array('sr_no'=>$i,
			'profile_image'=>$img,
			'first_name'=>strtoupper(trim($row->first_name." ".$row->last_name)),
			'email'=>strtoupper((string)$row->email),
			'password'=>(string)$row->password,
			'contact_number'=>$row->contact_number."<br>".$row->alternate_number,
			'company_name'=>strtoupper((string)$row->company_name),
			'department'=>strtoupper((string)$row->department),
			'user_role'=>strtoupper((string)$row->user_role),
			'notify'=>$notify,
			'status'=>$sta,
			'fmsp'=>'NA',
			'edit'=>$edit);
			$i++;
		}
		//echo "<pre>"; print_r($business_data); exit;
		$results = array(
		"sEcho" => 1,
		"iTotalRecords" => count($user_data),
		"iTotalDisplayRecords" => count($user_data),
		"aaData"=>$user_data);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($results, JSON_INVALID_UTF8_SUBSTITUTE));
	}
	
	public function send_login_detail(){
	    $query = $this->db->select('user_id, first_name, last_name, email, password')->from('system_users')->where('user_id',$this->uri->segment(4))->get();
	    foreach($query->result() as $user_information);
	    
	    
	    	$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
				   <tr>
					<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="" width="200px;" alt="" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>MITR Login Credential </strong><br><br></td>
					  </tr> 
					 
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;">Dear ,<br>'.$user_information->first_name.' '.$user_information->last_name.' Please find your login detail.
						</td>
					  </tr>
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>User Name </strong> : '.$user_information->email.'</td>
					  </tr>
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Password </strong> : '.$user_information->password.'</td>
					  </tr>
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Login URL </strong> : https://www.prestomitr.com</td>
					  </tr>
					  <tr>
						<td>&nbsp;</td>
					  </tr>
					 
					  
					  <tr>
						
					  </tr>
				
					 <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td>
					   </td>
					  </tr>
					</table>
					</td>
				  </tr>
				</table>';
				//echo $Message; exit;
				$subjectname = "MITR Login Credential";
					$this->email->set_mailtype("html");
					$this->email->to($user_information->email);
					$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('mitr@prestomitr.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();
			
			
			$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, login detail successfully sent.</div>');
			redirect(page_url.'Master/User_management');
	    
	    
	    
	    
	}
	
	public function update_user_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(4);
		$sval =  $this->uri->segment(5);
		$field_name = "user_id";
		$table = "system_users";
		if($sval=='1')
			{
				$password = implode( '-', str_split( substr( strtoupper( md5( time() . rand( 1000, 9999 ) ) ), 0, 20 ), 4 ) );
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('password'=>$password,
			'user_status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', 'Status successfully updated.');
			redirect(page_url.'Master/User_management');
		}
		
	
	public function delete_user()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(4);
		$field_name = "user_id";
		$table = "system_users";
		
			$data = array('hide_profile'=>'1');
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', 'Status successfully deleted.');
			redirect(page_url.'Master/User_management');
		}
		
		
	public function edit_user_profile(){
		$this->load->view('master/edit_user');
		
	}
	
	public function update_user_profile()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('first_name', 'First Name', 'required|trim');
		$this->form_validation->set_rules('last_name', 'Last Name', 'required|trim');
		$this->form_validation->set_rules('email', 'Email ID', 'required|trim');
		$this->form_validation->set_rules('contact_number', 'Contact Number', 'required|trim');
		//$this->form_validation->set_rules('father_name', 'Father Name', 'required|trim');
		//$this->form_validation->set_rules('mother_name', 'Mother Name', 'required|trim');
	//	$this->form_validation->set_rules('qualification', 'Qualification', 'required|trim');
		//$this->form_validation->set_rules('date_of_birth', 'Date of Birth', 'required|trim');
		//$this->form_validation->set_rules('date_of_joining', 'Date of Joining', 'required|trim');
		//$this->form_validation->set_rules('adharcard ', 'Aadhar Card', 'required|trim');
		//$this->form_validation->set_rules('pancard', 'Pan Card', 'required|trim');
		$this->form_validation->set_rules('business_loc', 'Business Location', 'required|trim');
		$this->form_validation->set_rules('department', 'Department', 'required|trim');
		$this->form_validation->set_rules('user_role', 'User Role', 'required|trim');
		//$this->form_validation->set_rules('address', 'Address', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/edit_user');
		}
		else
		{
			$old_img = $this->input->post('old_img');
			$old_adharcard = $this->input->post('old_adharcard');
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "system_users";
		$adharcard=$_FILES['adharcard']['name'];
		if($adharcard<>'')
		{
			$image1=explode('.',$adharcard);
			$cat_image=end($image1);
			$aadhar_card=time().'.'.$cat_image;
			move_uploaded_file($_FILES["adharcard"]["tmp_name"],UPLOADPATH.'users/document/' . $aadhar_card);
		}else
		{
			$aadhar_card=$old_adharcard;
			}
		
		$profilephoto=$_FILES['photo']['name'];
		if($profilephoto<>'')
		{
			$image2=explode('.',$profilephoto);
			$cat_image1=end($image2);
			$profile_photo=time().'.'.$cat_image1;
			move_uploaded_file($_FILES["photo"]["tmp_name"],UPLOADPATH.'users/' . $profile_photo);
		}else
		{
			$profile_photo=$old_img;
			}
		
		
		$relieving_letter=$_FILES['relieving_letter']['name'];
		if($relieving_letter<>'')
		{
			$image3=explode('.',$relieving_letter);
			$cat_image2=end($image3);
			$relieving_letterfile=time().'.'.$cat_image2;
			move_uploaded_file($_FILES["relieving_letter"]["tmp_name"],UPLOADPATH.'users/' . $relieving_letterfile);
		}else
		{
			$relieving_letterfile=$this->input->post('old_relieving_letter');
			}
		
		$joining_letter=$_FILES['joining_letter']['name'];
		if($joining_letter<>'')
		{
			$image4=explode('.',$joining_letter);
			$cat_image3=end($image4);
			$joining_letterfile=time().'.'.$cat_image3;
			move_uploaded_file($_FILES["joining_letter"]["tmp_name"],UPLOADPATH.'users/' . $joining_letterfile);
		}else
		{
			$joining_letterfile=$this->input->post('old_joining_letter');
			}
			
			$km_type = 0;
			$convence = (int) $this->input->post('convence');
			if ($convence == 1)
			{
				$con_type = (int) $this->input->post('con_type');
				if ($con_type == 1)
				{
					$km_type = $this->input->post('crate');
				}
			}else
			{
				$convence = 0;
				$con_type = 0;
				$km_type = 0;
			}

			$approvals = (int) $this->input->post('approvals');
			$project_coordinator_user_id = $this->resolve_project_coordinator_user_id($this->input->post('business_loc'));

			// if($approvals!=0)
			// {
			// 	$approvals_item=$this->input->post('approvals_item');
			// 	for($u=0;$u<count($approvals_item);$u++)
			// 	{
			// 		$appdata=array('user_id'=>$this->uri->segment(4),'approval_id'=>$approvals_item[$u],'addedOn'=>date('Y-m-d H:i:s'));
			// 		$this->db->insert('approvals_permission',$appdata);

			// 	}

			// }else
			// {
			// 	$this->db->where('user_id',$this->uri->segment(4));
			// 	$this->db->delete('approvals_permission');

			// }

		//$rand_number  = mt_rand(15, 50);
		//$password = "Prestogroup-".$rand_number;
			$data = array('first_name'=>$this->input->post('first_name'),
			'last_name'=>$this->input->post('last_name'),
			'email'=>$this->input->post('email'),
			'contact_number'=>$this->input->post('contact_number'),
			'alternate_number'=>$this->input->post('alternate_number'),
			'father_name'=>$this->input->post('father_name'),
			'mother_name'=>$this->input->post('mother_name'),
			'date_of_birth'=>date('Y-m-d',strtotime($this->input->post('date_of_birth'))),
			'date_of_joining'=>date('Y-m-d',strtotime($this->input->post('date_of_joining'))),
			'qualification'=>$this->input->post('qualification'),
			'aadharcard'=>$aadhar_card,
			'pancard'=>$this->input->post('pancard'),
			'profile_image'=>$profile_photo,
			'business_location'=>$this->input->post('business_loc'),
			'department_id'=>$this->input->post('department'),
			'user_role_id'=>$this->input->post('user_role'),
			'address'=>$this->input->post('address'),
			'user_status'=>$this->input->post('status'),
			'added_on'=>$date,
			'last_updated_on'=>$date,
			'aadhar_number'=>$this->input->post('aadhar_number'),
			'blood_group'=>$this->input->post('blood_group'),
			'spouce_name'=>$this->input->post('spouce_name'),
			'spouce_company_name'=>$this->input->post('spouce_working_in'),
			'children_name'=>$this->input->post('children_names'),
			'fms_process'=>0,
			'added_by'=>$user_id,
			'employee_code'=>$this->input->post('employee_code'),
			'nominee_name'=>$this->input->post('nominee_name'),
			'date_of_leaving'=>date('Y-m-d',strtotime($this->input->post('date_of_leaving'))),
			'basic_salary'=>$this->input->post('basic_salary'),
			'gross_salary'=>$this->input->post('gross_salary'),
			'ctc'=>$this->input->post('ctc'),
			'uia_no'=>$this->input->post('uia_no'),
			'pf_no'=>$this->input->post('pf_no'),
			'assign_delegation'=>$this->input->post('assign_delegation'),
			'bank_acc_detail'=>$this->input->post('bank_acc_detail'),
			'present_address'=>$this->input->post('present_address'),
			'joining_letter'=>$joining_letterfile,
			'relieving_letter'=>$relieving_letterfile,
			'marketing_person'=>$this->input->post('marketing_person'),
			'salestarget'=>$this->input->post('sellingtarget'),
			'assign_call_delegation'=>$this->input->post('assign_call_delegation'),
			'payment_grace_period'=>$this->input->post('payment_grace_period'),
			'approvals'=>$approvals,
			'convence'=>$convence,
			'convence_type'=>$con_type,
			'convence_rate'=>$km_type,
			'paymenttarget'=>$this->input->post('paymenttarget'));
			if ($this->system_users_supports_project_coordinator()) {
				$data['project_coordinator_user_id'] = $project_coordinator_user_id;
			}
			//echo "<pre>"; print_r($data); exit;
			$this->db->where('user_id',$this->uri->segment(4));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
		    if(!empty($this->input->post('fmsp'))){
			/** FMS Process **/
				$allfmsp=$this->input->post('fmsp');
				if(count($allfmsp)>0)
				{
					$this->db->where('userid',$this->uri->segment(4));
					$this->db->delete('flowtousers');
				}
				for($i=0;$i<count($allfmsp);$i++)
				{
				$fmspdata=array('production_flowid'=>$allfmsp[$i],'userid'=>$this->uri->segment(4),'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
				$qy = $this->db->select('id')->from('flowtousers')->where('userid',$this->uri->segment(4))->where('production_flowid',$allfmsp[$i])->get();
				if($qy->num_rows()==0){
					$this->db->insert('flowtousers',$fmspdata);
				}
				
				}
		}	
			$this->session->set_flashdata('message','<div class="alert  alert-success">Thank you, record successfully added.</div>');
			redirect(page_url.'Master/User_management/');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/User_management/');
		}
	
		
	}
		
	}
	public function create_team()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('business_loc', 'Business Location', 'required|trim');
		$this->form_validation->set_rules('department', 'Department Name', 'required|trim');
		$this->form_validation->set_rules('team_leader', 'Team Leader', 'required|trim');
		$this->form_validation->set_rules('team_name', 'Team Name', 'required|trim');
		//$this->form_validation->set_rules('by_pass', 'By Pass', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/create_team');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "prestogroup_teams";
		$query = $this->db->select('team_id')
			->from('prestogroup_teams')
			->where('business_loc_id',$this->input->post('business_loc'))
			->where('department_id',$this->input->post('department'))
			->where('team_leader',$this->input->post('team_leader'))
			->limit(1)
			->get();
		if($query->num_rows() > 0){
			$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry! This team leader is already mapped for the selected department.</div>');
			redirect(page_url.'Master/User_management/company_team_list');
			
		}else{
		
		
			$data = array('business_loc_id'=>$this->input->post('business_loc'),
			'department_id'=>$this->input->post('department'),
			'team_leader'=>$this->input->post('team_leader'),
			'team_name'=>$this->input->post('team_name'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			if ($this->prestogroup_teams_supports_task_access_settings()) {
				$data['show_all_team_tasks'] = $this->input->post('show_all_team_tasks') ? 1 : 0;
				$data['allow_task_assignment'] = $this->input->post('allow_task_assignment') ? 1 : 0;
			}
			
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
		$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully added.</div>');
			redirect(page_url.'Master/User_management/company_team_list');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/User_management/company_team_list');
		}
		}
		
	}
		
	}
	public function team_list()
	{
		$team_data = array();
		$i=1;
		$this->db->select('a.*,a.department_id as presto_department_id, b.business_loc_id,b.company_name,c.department_id, c.department, d.user_id, d.first_name, d.last_name')->from('prestogroup_teams a');
		$this->db->join('business_location b','a.business_loc_id=b.business_loc_id','left');
		$this->db->join('departments c','a.department_id=c.department_id','left');
		$this->db->join('system_users d','a.team_leader=d.user_id','left');
		$this->db->where('a.department_id',$this->uri->segment(4));
		$this->db->order_by('a.team_name','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			//echo "<pre>"; print_r($row); exit;									
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Master/User_management/update_team_status/".$row->team_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
				
				$add_team = "<a href='".page_url."Master/User_management/add_member_in_team/".$row->team_id."/".$row->business_loc_id."/".$row->presto_department_id."'><span class='btn btn-success btn-xs'><i class='fa fa-group'></i>Add Member</span></a>";
				/*1st segment is team id, 2nd is business location and 3rd is department ID*/
			}else
			{
				$sta =  "<a href='".page_url."Master/User_management/update_team_status/".$row->team_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
				$add_team="";
			}
			$edit = "<a href='".page_url."Master/User_management/edit_team/".$row->team_id."'><i class='fa fa-pencil'></i></a>";	
			
			$team_data[] = array('sr_no'=>$i,
			'company_name'=>$row->company_name,
			'department'=>$row->department,
			'team_leader'=>$row->first_name." ".$row->last_name,
			'team'=>$row->team_name,
			'add_team'=>$add_team,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($team_data),
			"iTotalDisplayRecords" => count($team_data),
			"aaData"=>$team_data);
			
		echo json_encode($results);
	}
	
	public function update_team_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(4);
		$sval =  $this->uri->segment(5);
		$field_name = "team_id";
		$table = "prestogroup_teams";
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
			redirect(page_url.'Master/User_management/company_team_list');
		}

	public function edit_team(){
		$this->load->view('master/edit_team');
	}	
	
	public function update_team()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('business_loc', 'Business Location', 'required|trim');
		$this->form_validation->set_rules('department', 'Department Name', 'required|trim');
		$this->form_validation->set_rules('team_leader', 'Team Leader', 'required|trim');
		$this->form_validation->set_rules('team_name', 'Team Name', 'required|trim');
		$this->form_validation->set_rules('by_pass', 'By Pass', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/edit_team');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "prestogroup_teams";
		$query = $this->db->select('team_id')
			->from('prestogroup_teams')
			->where('business_loc_id',$this->input->post('business_loc'))
			->where('department_id',$this->input->post('department'))
			->where('team_leader',$this->input->post('team_leader'))
			->where('team_id !=',(int)$this->uri->segment(4))
			->limit(1)
			->get();
		if($query->num_rows() > 0){
			$this->session->set_flashdata('message','<div class="alert alert-danger" style="color:#000;">Sorry! This team leader is already mapped for the selected department.</div>');
			redirect(page_url.'Master/User_management/company_team_list');
			
		}else{
		
		
			$data = array('business_loc_id'=>$this->input->post('business_loc'),
			'department_id'=>$this->input->post('department'),
			'team_leader'=>$this->input->post('team_leader'),
			'team_name'=>$this->input->post('team_name'),
			'status'=>$this->input->post('status'),
			'by_pass'=>$this->input->post('by_pass'),
			'added_by'=>$user_id);
			if ($this->prestogroup_teams_supports_task_access_settings()) {
				$data['show_all_team_tasks'] = $this->input->post('show_all_team_tasks') ? 1 : 0;
				$data['allow_task_assignment'] = $this->input->post('allow_task_assignment') ? 1 : 0;
			}
			$this->db->where('team_id',$this->uri->segment(4));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger" style="color:#000;">Thank you, record successfully updated.</div>');
			redirect(page_url.'Master/User_management/company_team_list');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info" style="color:#000">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/User_management/company_team_list');
		}
		}
		
	}
		
	}

	public function selete_team_leader()
	{
	$department = $this->input->post('department');
	$business_loc = $this->input->post('business_loc');
		$query = $this->db->select('b.user_id, b.first_name, b.last_name, a.business_loc_id, a.department_id')->from('departments a')->join('system_users b','a.departmenthead=b.user_id','left')->where('a.business_loc_id',$business_loc)->where('a.department_id',$department)->where('b.user_status','1')->get();
			foreach($query->result() as $users)
			{
				echo "<option value=".$users->user_id.">".$users->first_name." ".$users->last_name."</option>";
				}
		
		}


		public function select_team_member()
		{
		echo "<option value=''>--Select Team Leader--</option>";
	$department = (int) $this->input->post('department');
	$business_loc = (int) $this->input->post('business_loc');
		if($department <= 0){
			echo "<option value=''>--No Active User Found--</option>";
			return;
		}

		$team_leaders = array();
		$add_user_option = function($users) use (&$team_leaders) {
			foreach($users as $user){
				if(empty($user->user_id)){
					continue;
				}
				$team_leaders[$user->user_id] = trim($user->first_name." ".$user->last_name);
			}
		};

		if($department > 0){
			$this->db->select('b.user_id, b.first_name, b.last_name')
				->from('departments a')
				->join('system_users b','a.departmenthead=b.user_id','left')
				->where('a.department_id',$department)
				->where('a.status','1')
				->where('b.user_status','1');
			if($business_loc > 0){
				$this->db->where('a.business_loc_id',$business_loc);
			}
			$add_user_option($this->db->get()->result());
		}

		if($department > 0){
			$query = $this->db->select('user_id, first_name, last_name')
				->from('system_users')
				->where('user_status','1')
				->where('department_id',$department)
				->order_by('first_name','asc')
				->get();
			$add_user_option($query->result());
		}

		// Fallback: still allow department users even if their status/location data is not perfectly maintained.
		if(empty($team_leaders) && $department > 0){
			$query = $this->db->select('user_id, first_name, last_name')
				->from('system_users')
				->where('department_id',$department)
				->order_by('first_name','asc')
				->get();
			$add_user_option($query->result());
		}

		if(!empty($team_leaders)){
			asort($team_leaders);
			foreach($team_leaders as $user_id => $user_name)
			{
				echo "<option value=".$user_id.">".$user_name."</option>";
			}
		}else{
			echo "<option value=''>--No Active User Found--</option>";
		}
			
			}

		private function get_team_details_for_member_management($team_id = 0, $business_loc_id = 0, $department_id = 0)
		{
			$team_id = (int) $team_id;
			$business_loc_id = (int) $business_loc_id;
			$department_id = (int) $department_id;

			if ($team_id <= 0) {
				return null;
			}

			$this->db->select('a.team_id, a.business_loc_id, a.department_id, a.team_leader, a.team_name, b.first_name as leader_first_name, b.last_name as leader_last_name')
				->from('prestogroup_teams a')
				->join('system_users b', 'b.user_id = a.team_leader', 'left')
				->where('a.team_id', $team_id);
			if ($business_loc_id > 0) {
				$this->db->where('a.business_loc_id', $business_loc_id);
			}
			if ($department_id > 0) {
				$this->db->where('a.department_id', $department_id);
			}

			return $this->db->limit(1)->get()->row();
		}

		private function get_available_members_for_team($team)
		{
			if (empty($team)) {
				return array();
			}

			$excluded_user_ids = array((int) $team->team_leader);
			$existing_members = $this->db->select('employee_id')
				->from('presto_team_members')
				->where('team_id', (int) $team->team_id)
				->get()
				->result();

			foreach ($existing_members as $existing_member) {
				$employee_id = (int) $existing_member->employee_id;
				if ($employee_id > 0) {
					$excluded_user_ids[] = $employee_id;
				}
			}

			$excluded_user_ids = array_values(array_unique($excluded_user_ids));

			$this->db->select('user_id, first_name, last_name, business_location, department_id, user_status')
				->from('system_users')
				->where('business_location', (int) $team->business_loc_id)
				->where('department_id', (int) $team->department_id)
				->where('user_status', '1')
				->where('hide_profile', '0');

			if (!empty($excluded_user_ids)) {
				$this->db->where_not_in('user_id', $excluded_user_ids);
			}

			$this->db->order_by('first_name', 'asc');
			$this->db->order_by('last_name', 'asc');

			return $this->db->get()->result();
		}
		
		public function add_member_in_team()
		{
		$team_id = (int) $this->uri->segment(4);
		$business_loc_id = (int) $this->uri->segment(5);
		$department_id = (int) $this->uri->segment(6);
		$team = $this->get_team_details_for_member_management($team_id, $business_loc_id, $department_id);

		if (empty($team)) {
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Sorry! Team not found.</div>');
			redirect(page_url.'Master/User_management/company_team_list');
		}

		$data = array(
			'team' => $team,
			'available_members' => $this->get_available_members_for_team($team)
		);

		$this->load->view('master/add_member_in_team', $data);	
		}
		
		public function add_members(){
			date_default_timezone_set("Asia/Kolkata");
			$date =  date('Y-m-d H:i:s'); 
			$table = "presto_team_members";
			$user_id =$this->session->userdata['logged_in']['user_id'];
			$team_id = (int) $this->uri->segment(4);
			$business_loc_id = (int) $this->uri->segment(5);
			$department_id = (int) $this->uri->segment(6);
			$redirect_url = page_url.'Master/User_management/add_member_in_team/'.$team_id.'/'.$business_loc_id.'/'.$department_id;
			$team = $this->get_team_details_for_member_management($team_id, $business_loc_id, $department_id);

			if (empty($team)) {
				$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry! Team not found.</div>');
				redirect(page_url.'Master/User_management/company_team_list');
			}

			$submitted_members = $this->input->post('my_multi_select1');
			if (!is_array($submitted_members) || empty($submitted_members)) {
				$this->session->set_flashdata('message','<div class="alert alert-danger">Please select at least one valid team member.</div>');
				redirect($redirect_url);
			}

			$selected_member_ids = array();
			foreach ($submitted_members as $submitted_member) {
				$submitted_member = (int) $submitted_member;
				if ($submitted_member > 0) {
					$selected_member_ids[] = $submitted_member;
				}
			}
			$selected_member_ids = array_values(array_unique($selected_member_ids));

			if (empty($selected_member_ids)) {
				$this->session->set_flashdata('message','<div class="alert alert-danger">Please select at least one valid team member.</div>');
				redirect($redirect_url);
			}

			$team_leader_selected = in_array((int) $team->team_leader, $selected_member_ids, true);
			$available_members = $this->get_available_members_for_team($team);
			$allowed_member_ids = array();
			foreach ($available_members as $available_member) {
				$allowed_member_ids[(int) $available_member->user_id] = true;
			}

			$eligible_member_ids = array();
			$skipped_member_ids = array();
			foreach ($selected_member_ids as $selected_member_id) {
				if (isset($allowed_member_ids[$selected_member_id])) {
					$eligible_member_ids[] = $selected_member_id;
				} else {
					$skipped_member_ids[] = $selected_member_id;
				}
			}

			if (empty($eligible_member_ids)) {
				$error_message = 'Please select only valid users from this department team.';
				if ($team_leader_selected) {
					$error_message = 'Team leader cannot be added as a member in the same team.';
				}
				$this->session->set_flashdata('message','<div class="alert alert-danger">'.$error_message.'</div>');
				redirect($redirect_url);
			}

			$added_members_count = 0;
			foreach ($eligible_member_ids as $employee_id) {
				$data=array(
					'business_loc_id'=>$business_loc_id,
					'department_id'=>$department_id,
					'team_id'=>$team_id,
					'employee_id'=>$employee_id,
					'added_on'=>$date,
					'added_by'=>$user_id
				);
				$query = $this->db->select('business_loc_id,department_id,team_id,employee_id')
					->from('presto_team_members')
					->where('business_loc_id',$business_loc_id)
					->where('department_id',$department_id)
					->where('team_id',$team_id)
					->where('employee_id',$employee_id)
					->get();
				$res = $query->result();
				if(!$res){
					$this->master->insert_record($table,$data);
					$added_members_count++;
				}
			}

			if ($added_members_count > 0 && ($team_leader_selected || !empty($skipped_member_ids))) {
				$this->session->set_flashdata('message','<div class="alert alert-warning">Valid members were added. Team leader or already-added/invalid users were skipped.</div>');
			} else if ($added_members_count > 0) {
				$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully added.</div>');
			} else {
				$this->session->set_flashdata('message','<div class="alert alert-info">Selected users are already in the team or not eligible to be added.</div>');
				redirect($redirect_url);
			}

			redirect(page_url.'Master/User_management/company_team_list');
			
		}
	
	public function view_team_list(){
		$this->load->view('master/view_team_members');
	}
	
	public function company_team_list(){
		
			$this->load->view('master/view_team_list');
		
	}
	
	public function all_created_team()
	{
		$team_data = array();
		$i=1;
		$business_location = $this->session->userdata['logged_in']['business_location'];
		$this->db->select('a.*,a.department_id as presto_department_id, b.business_loc_id,b.company_name,c.department_id, c.department, d.user_id, d.first_name, d.last_name')->from('prestogroup_teams a');
		$this->db->join('business_location b','a.business_loc_id=b.business_loc_id','left');
		$this->db->join('departments c','a.department_id=c.department_id','left');
		$this->db->join('system_users d','a.team_leader=d.user_id','left');
		$this->db->where('a.business_loc_id',$business_location);
		$this->db->order_by('a.team_name','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Master/User_management/update_team_status/".$row->team_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
				
				$add_team = "<a href='".page_url."Master/User_management/add_member_in_team/".$row->team_id."/".$row->business_loc_id."/".$row->presto_department_id."'><span class='btn btn-success btn-xs'><i class='fa fa-group'></i>&nbsp;Add Member</span></a>";
				
				$view_team_members = "<a href='".page_url."Master/User_management/view_team_list/".$row->team_id."/".$row->business_loc_id."/".$row->presto_department_id."'><span class='btn btn-warning btn-xs'><i class='fa fa-group'></i>&nbsp;View Members</span></a>";
				/*1st segment is team id, 2nd is business location and 3rd is department ID*/
			}else
			{
				$sta =  "<a href='".page_url."Master/User_management/update_team_status/".$row->team_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
				$add_team="";
				$view_team_members="";
			}
			$edit = "<a href='".page_url."Master/User_management/edit_team/".$row->team_id."'><i class='fa fa-pencil'></i></a>";	
			if($row->by_pass=='1'){
				$bypass = "Yes";
			}else{
				$bypass = "No";
			}
			$show_all_team_tasks = (!empty($row->show_all_team_tasks) && (int) $row->show_all_team_tasks === 1) ? 'Yes' : 'No';
			$allow_task_assignment = (!empty($row->allow_task_assignment) && (int) $row->allow_task_assignment === 1) ? 'Yes' : 'No';


			$teamname=array();
				$query = $this->db->select('a.team_id,a.team_members_id, a.employee_id, b.user_id, b.first_name, b.last_name, b.profile_image,b.user_role_id, c.user_role_id,c.user_role')
					->from('presto_team_members a')
					->join('system_users b','a.employee_id=b.user_id','left')
					->join('user_role c','b.user_role_id=c.user_role_id','left')
					->where('team_id',$row->team_id)
					->where('a.employee_id !=', $row->team_leader)
					->get();
			if($query->num_rows()>0)
			{
			foreach($query->result() as $team){
				$teamname[]=ucwords(strtolower($team->first_name." ".$team->last_name));
			}
			}

			
			$tname=implode("<br/>",$teamname);
			if($tname==''){
				$tname = '<span style="color:#999;">No team user added</span>';
			}
			


			$team_data[] = array('sr_no'=>$i,
			'company_name'=>ucwords(strtolower($row->company_name)),
			'department'=>ucwords(strtolower($row->department)),
			'team_leader'=>ucwords(strtolower($row->first_name." ".$row->last_name)),
			'team'=>ucwords(strtolower($row->team_name)),
			'bypass'=>$bypass,
			'show_all_team_tasks'=>$show_all_team_tasks,
			'allow_task_assignment'=>$allow_task_assignment,
			'add_team'=>$add_team." ".$view_team_members,
			'members'=>$tname,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($team_data),
			"iTotalDisplayRecords" => count($team_data),
			"aaData"=>$team_data);
			
		echo json_encode($results);
	}

	private function prestogroup_teams_supports_task_access_settings()
	{
		return $this->db->field_exists('show_all_team_tasks', 'prestogroup_teams')
			&& $this->db->field_exists('allow_task_assignment', 'prestogroup_teams');
	}
	
	public function google_sheet_link(){
		$this->load->view('Googlesheet/add_sheet');
		
	}
	
	
public function add_google_sheet_urls(){
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
           $table = "google_sheet_by_department";
			if(isset($_REQUEST['url'])){	
					$tags1=count($_REQUEST['url']);
					if($tags1>0)
					{
						$department_id = $this->uri->segment(4);
						if($department_id==''){
							$depart_id = "0";
						}else{
							$depart_id =$department_id;
						}
					$content_attruibute=$_REQUEST['url'];
					$content_attruibute1=$_REQUEST['sheeturl'];
					
					$title = $this->input->post('title');
					for($x=0;$x<$tags1;$x++){
					if($content_attruibute[$x]!='')
						{
							$data=array('google_sheet_url'=>$content_attruibute[$x],
							'google_sheet_link'=>$content_attruibute1[$x],
							'department_id'=>$depart_id,
							'title'=>$title[$x],
							'added_on'=>$added_time,
							'added_by'=>$user_id);
							$this->master->insert_record($table,$data);
						}
					}
					}
					}
				if($depart_id=='0'){
					
					$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'Master/User_management/view_all_google_sheet_link');
				}else{
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'Master/User_management/google_sheet_link/'.$department_id);
				}
	}
	
	public function google_sheet_list_by_department()
	{
		$google_sheet_data = array();
		$i=1;
		$this->db->select('a.*, d.user_id, d.first_name, d.last_name')->from('google_sheet_by_department a');
		$this->db->join('system_users d','a.added_by=d.user_id','left');
		$this->db->where('a.department_id',$this->uri->segment(4));
		$this->db->order_by('a.title','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			$addeddate = date('Y-m-d', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time));				$sheeturl = "<a href='".$row->google_sheet_link."' target='_blank'>".$row->google_sheet_link."</a>";					
			$edit = "<a href='".page_url."Master/User_management/edit_googlesheet_link/".$row->sheet_id."'><i class='fa fa-pencil'></i></a>";	
			$google_sheet_data[] = array('sr_no'=>$i,
			'title'=>"<a href='".$row->google_sheet_url."' target='_blank'>".$row->title."</a>",
			'sheeturl'=>$sheeturl,
			'added_by'=>$row->first_name." ".$row->last_name,
			'added_on'=>$addeddate.$addedtime,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($google_sheet_data),
			"iTotalDisplayRecords" => count($google_sheet_data),
			"aaData"=>$google_sheet_data);
			
		echo json_encode($results);
	}
	
	public function view_all_google_sheet_link(){
		$this->load->view('Googlesheet/view_all_records');
	}
	
public function google_sheet_lists()
	{
		$google_sheet_data = array();
		$i=1;
		$this->db->select('a.*, d.user_id, d.first_name, d.last_name, b.department_id, b.department, b.business_loc_id,c.business_loc_id,c.company_name, c.address')->from('google_sheet_by_department a')->join('departments b','a.department_id=b.department_id','left')->join('business_location c','c.business_loc_id=b.business_loc_id','left');
		$this->db->join('system_users d','a.added_by=d.user_id','left');
		$this->db->order_by('a.title','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			$link = "<span class='btn btn-warning btn-xs'><i class='fa fa-google'></i><a href='".$row->google_sheet_url."' target='_blank'><span style='color:white'>".$row->title."</span></a></span>";
			$addeddate = date('Y-m-d', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time));									
			$edit = "<a href='".page_url."Master/User_management/edit_googlesheet_link/".$row->sheet_id."'><i class='fa fa-pencil'></i></a>";	
		
		$sheet_link = $row->google_sheet_link;
			$google_sheet_data[] = array('sr_no'=>$i,
			'business_loc'=>$row->company_name."<br>".$row->address,
			'department'=>$row->department,
			'title'=>$link,
			'sheetlink'=>$sheet_link,
			'added_by'=>$row->first_name." ".$row->last_name,
			'added_on'=>$addeddate.$addedtime,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($google_sheet_data),
			"iTotalDisplayRecords" => count($google_sheet_data),
			"aaData"=>$google_sheet_data);
			
		echo json_encode($results);
	}
	
	public function edit_googlesheet_link(){
		$this->load->view('Googlesheet/edit_google_sheet');
	}
	
	
	public function update_googlesheet_URL()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('title', 'Title', 'required|trim');
		$this->form_validation->set_rules('google_url', 'Google URL', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('Googlesheet/edit_google_sheet');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "google_sheet_by_department";
		$department_id = $this->input->post('department_id');
			$data = array(
			'title'=>$this->input->post('title'),
			'google_sheet_url'=>$this->input->post('google_url'),
			'google_sheet_link'=>$this->input->post('sheeturl'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			$this->db->where('sheet_id',$this->uri->segment(4));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			if($department_id=='0'){
			    redirect(page_url.'Master/User_management/view_all_google_sheet_link/');
			}else{
			redirect(page_url.'Master/User_management/google_sheet_link/'.$department_id);
			}
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/User_management/google_sheet_link/'.$department_id);
		}
		
		
	}
		
	}
public function remove_member(){
    $id = $this->uri->segment(4);
    $this->db->where('team_members_id', $id);
$this->db->delete('presto_team_members');
	$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, Member successfully removed.</div><br/>');
			redirect(page_url.'Master/User_management/view_team_list/'.$this->uri->segment(5));

}	

public function change_password(){
    
		$this->load->view('master/change_password');
	}
	
public function update_your_password(){
	$id = $this->uri->segment(4);
	$data = array('password'=>$this->input->post('password'));
	$this->db->where('user_id',$id);
	$this->db->update('system_users',$data);
	 $this->session->set_flashdata('message','<div class="alert alert-success" style="color:#000">Thank You! Your Password succefully changed.</div>');
			redirect(page_url.'Master/User_management');
	
}
	public function change_quote(){
    
		$this->load->view('master/change_quote');
	}
	
		public function upadate_quote(){
		
	
		$user_id =$this->session->userdata['logged_in']['user_id'];
		$data = array('quote'=>$this->input->post('quote'));
		
		$this->db->where('id',$this->uri->segment(4));
		$this->db->update('quote_of_the_day',$data);
		if($this->uri->segment(4)==''){
		 $data = array('quote_date'=>date('Y-m-d'),
		 'quote'=>$this->input->post('quote'),
		 'added_by'=>$user_id);
		 $this->db->insert('quote_of_the_day',$data);
		}
	 $this->session->set_flashdata('message','<div class="alert alert-success" style="color:#000">Thank You! Your Quote succefully changed.</div>');
			redirect(page_url.'Dashboard');
	
		
	}
	public function upadate_password(){
		
		$oldpass= $this->input->post('old_pass');
		$conpass= $this->input->post('con_pass');
		if($oldpass==$conpass){
		$table="system_users";
	    $data = array(
        'password' => $this->input->post('old_pass')
       	);
		$user_id =$this->session->userdata['logged_in']['user_id'];
		$this->db->where('user_id',$user_id);
		$this->db->update($table,$data);
			$this->session->set_flashdata('message','Thank you, Your Password successfully updated. Please Logout your account and login with New Password. ');
			redirect(page_url.'Master/User_management/change_password');
		}else{
		    $this->session->set_flashdata('message','Password combination is not matched.');
			redirect(page_url.'Master/User_management/change_password');
		}
		
	}
	
	function assigncapabilities()
{
	$user_id=$this->uri->segment('4');
	$module=$this->input->post('module');
	$module = is_array($module) ? $module : array();
	for($i=0;$i<count($module); $i++)
	{
		$moduleid=$module[$i];
	   $moduleac=isset($_REQUEST['moduleaccess'.$moduleid]) ? $_REQUEST['moduleaccess'.$moduleid] : '0';
	  
	  $data=array('role_id'=>$user_id,'moduleid'=>$moduleid,'access'=>$moduleac,'addedOn'=>date('Y-m-d h:i:s'));
			
			$this->db->insert('module_access',$data);
			$lid=$this->db->insert_id();
			
			
			$submodule=isset($_REQUEST['submodule'.$moduleid]) && is_array($_REQUEST['submodule'.$moduleid]) ? $_REQUEST['submodule'.$moduleid] : array();
			//echo "<pre>"; print_r($submodule);exit;
			for($j=0;$j<count($submodule);$j++)
			{
				$submoduleid=$submodule[$j];
			
			if($moduleac=='1')
			{
				if(isset($_REQUEST['add'.$moduleid.$submoduleid]))
				{
					$add='1';
				}else
				{
					 $add='0';
				}
				
				if(isset($_REQUEST['edit'.$moduleid.$submoduleid]))
				{
					$edit='1';
				}else
				{
					 $edit='0';
				}
				
				
				
				if(isset($_REQUEST['remove'.$moduleid.$submoduleid]))
				{
					$remove='1';
				}else
				{
					 $remove='0';
				}
				//echo $add;exit;
				if($add=='1' || $edit=='1' || $remove=='1')
				{
					$submoduleaccess = "1";
				}else{
					$submoduleaccess = "0";
				}
				
				
				$data1=array('acessid'=>$lid,'role_id'=>$user_id,'moduleid'=>$moduleid,'submoduleid'=>$submoduleid,'submodule_access'=>$submoduleaccess,'madd'=>$add,'medit'=>$edit,'mremove'=>$remove,'addedOn'=>date('Y-m-d h:i:s'));
				
				$this->db->insert('module_capablity',$data1);
				}
				
				}
				
				}
				
				$this->session->set_flashdata('message','Thank you, Access permission successfully set.');
				redirect(page_url.'Master/User_management/userwise_permission_dashboard/');
				
				
				
}

	function getfmswiseuser()
{
		$fmsid=$this->uri->segment(4);

		$query = $this->db->select('a.user_id, a.title, a.first_name, a.last_name, a.user_status')->from('system_users a')->where('a.user_status','1')->order_by('a.first_name','asc')->get();
		echo '<option value="">Select Who</option>';
		if($query->num_rows()>0)
		{
		foreach($query->result() as $row){

		echo '<option value="'.$row->user_id.'">'.strtoupper($row->title).' '.strtoupper($row->first_name).' '.strtoupper($row->last_name).'</option>';
		}
		}
	
}

	function getsortorder()
{
		$fmsid=$this->uri->segment(4);

		$resttui=$this->db->select('setorder')->from('fms_flow')->where('production_flow_id',$fmsid)->get();
		$previousorder=array();
		if($resttui->num_rows()>0)
		{
		foreach($resttui->result() as $resttui1)
		{
		$previousorder[]=$resttui1->setorder;
		}
		}else
		{
		$previousorder=array();
		}
echo '<option value="">SET ORDER</option>';
		for($i=1;$i<16;$i++)
		{
		if (!in_array($i, $previousorder)) 
		{ 

		echo '<option value="'.$i.'">'.$i.'</option>';

		}
		}
												
		
	
}

	
function gettat()
{
		$fmsid=$this->uri->segment(4);
echo "<option value='0'>ORDER PLANNING</option>";
		$resttui=$this->db->select('flow_id,fms_flow')->from('fms_flow')->where('production_flow_id',$fmsid)->get();
		if($resttui->num_rows()>0)
		{
		
			foreach($resttui->result() as $resttui1)
			{
			echo "<option value='".$resttui1->flow_id."'>".$resttui1->fms_flow."</option>";
			
				
			}
			
		}
												
		
	
}
	
function getfmsinformation()
{
$fmsid=$this->uri->segment(4);
$existingval = base64_decode($this->uri->segment(5));
$this->db->select('flow_id,fms_flow')->from('fms_flow');  //->where('production_flow_id',$fmsid);
if($existingval<>'' && $existingval<>'0'){
		$movetodata = explode(',',$existingval);
		foreach($movetodata as $data1){
			$this->db->where_not_in('flow_id',$data1);
		}
		
	}
$resttui=$this->db->get();
if($resttui->num_rows()>0)
{
foreach($resttui->result() as $resttui1)
{
	
echo "<option value='".$resttui1->flow_id."'>".$resttui1->fms_flow."</option>";


}

}
}

	public function userwise_permission_dashboard(){
		$this->load->view('master/user_wise_permission');
		
	}
	
	public function all_system_user_list()
	{
		$i=1;
		$business_location = $this->session->userdata['logged_in']['business_location'];
		$department_data = array();
		$this->db->select('a.user_id, a.title, a.first_name, a.last_name, a.business_location, a.department_id,b.business_loc_id,b.company_name,c.department_id, c.department')->from('system_users a');
		$this->db->join('business_location b','a.business_location=b.business_loc_id','left');
		$this->db->join('departments c','a.department_id=c.department_id','left');
		$this->db->where('a.user_status','1');
		$this->db->where('a.hide_profile','0');
		$this->db->where('a.business_location',$business_location);
		$this->db->order_by('a.first_name','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			
			
			$idset=$this->db->select('id')->from('module_email_sms_whatsapp_notofication')->where('user_id',$row->user_id)->get();
			$ifset=$idset->num_rows();
			if($ifset=='0')
			{
			$notificationpermission = "<a href='".page_url."Delegation/set_notification_permission/".$row->user_id."'><span class='btn btn-warning btn-xs'>Set Permission</span></a>";	
			}else
			{
		   $notificationpermission = "<a href='".page_url."Delegation/edit_permission/".$row->user_id."'><span class='btn btn-success btn-xs'>Edit Permission</span></a>";	
			}	
			
			$qry=$this->db->select('role_id')->from('module_access')->where('role_id',$row->user_id)->get();
			$ifset=$qry->num_rows();
			if($ifset=='0')
			{
			$userpermission = "<a href='".page_url."Master/User_management/user_permission/".$row->user_id."'><span class='btn btn-warning btn-xs'>Set Permission</span></a>";	
			}else
			{
		   $userpermission = "<a href='".page_url."Master/User_management/edit_module_access_permission/".$row->user_id."'><span class='btn btn-success btn-xs'>Edit Permission</span></a>";	
			}	
			
			$qry=$this->db->select('id')->from('ims_header_permission')->where('user_id',$row->user_id)->get();
			$ifset=$qry->num_rows();
			if($ifset=='0')
			{
			$ims_permission = "<a href='".page_url."Master/User_management/set_ims_header_permission/".$row->user_id."'><span class='btn btn-warning btn-xs'>Set Permission</span></a>";	
			}else
			{
		   $ims_permission = "<a href='".page_url."Master/User_management/edit_ims_header_permission/".$row->user_id."'><span class='btn btn-success btn-xs'>Edit Permission</span></a>";	
			}
			
			
			$department_data[] = array('sr_no'=>$i,
			'company_name'=>strtoupper($row->company_name),
			'department'=>strtoupper($row->department),
			'user_name'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'notificationpermission'=>$notificationpermission,
			'userpermission'=>$userpermission,
			'ims_permission'=>$ims_permission);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($department_data),
			"iTotalDisplayRecords" => count($department_data),
			"aaData"=>$department_data);
			
		echo json_encode($results);
	}
public function user_permission()
		{
			$this->ensure_service_payment_permission_submodules();

			$user_id = (int)$this->uri->segment(4);
			$user = $this->get_permission_user($user_id);
			if (empty($user)) {
				$this->session->set_flashdata('message','<div class="alert alert-danger" style="color:#000">Selected user was not found.</div>');
				redirect(page_url.'Master/User_management/userwise_permission_dashboard/');
			}

			$business_location = !empty($user['business_location']) ? (int)$user['business_location'] : (int)$this->session->userdata['logged_in']['business_location'];
			$modules = $this->get_permission_modules($user_id, $business_location);

			$data = array(
				'permission_user' => $user,
				'permission_modules' => $modules,
				'permission_summary' => $this->build_permission_summary($modules),
				'permission_page_title' => 'Set Access Permission',
				'permission_form_action' => page_url.'Master/User_management/assigncapabilities/'.$user_id,
				'permission_submit_label' => 'Set Permission',
				'permission_back_url' => page_url.'Master/User_management/userwise_permission_dashboard/',
				'permission_save_heading' => 'Save access setup for ' . trim((!empty($user['title']) ? $user['title'] . ' ' : '') . (!empty($user['first_name']) ? $user['first_name'] . ' ' : '') . (!empty($user['last_name']) ? $user['last_name'] : '')),
				'permission_save_note' => 'Turn the module on, then allow only the submodules this user should get for the first time.'
			);

			$this->load->view('master/edit_permission', $data);
			
    }
	
function edit_module_access_permission()
		{
			$this->ensure_service_payment_permission_submodules();

			$user_id = (int)$this->uri->segment(4);
			$user = $this->get_permission_user($user_id);
			if (empty($user)) {
				$this->session->set_flashdata('message','<div class="alert alert-danger" style="color:#000">Selected user was not found.</div>');
				redirect(page_url.'Master/User_management/userwise_permission_dashboard/');
			}

			$business_location = !empty($user['business_location']) ? (int)$user['business_location'] : (int)$this->session->userdata['logged_in']['business_location'];
			$modules = $this->get_permission_modules($user_id, $business_location);

			$data = array(
				'permission_user' => $user,
				'permission_modules' => $modules,
				'permission_summary' => $this->build_permission_summary($modules),
				'permission_page_title' => 'Edit Access Permission',
				'permission_form_action' => page_url.'Master/User_management/edit_capablities/'.$user_id,
				'permission_submit_label' => 'Update Permission',
				'permission_back_url' => page_url.'Master/User_management/userwise_permission_dashboard/',
				'permission_save_heading' => 'Save changes for ' . trim((!empty($user['title']) ? $user['title'] . ' ' : '') . (!empty($user['first_name']) ? $user['first_name'] . ' ' : '') . (!empty($user['last_name']) ? $user['last_name'] : '')),
				'permission_save_note' => 'Turn the module on, then allow only the submodules this user should actually use.'
			);

			$this->load->view('master/edit_permission', $data);
		}

private function ensure_service_payment_permission_submodules()
{
	$this->ensure_permission_submodule(17, 'SERVICE PAYMENT REQUESTS');
	$this->ensure_permission_submodule(17, 'SERVICE PAYMENT APPROVALS');
}

private function ensure_permission_submodule($module_id, $submodule_name)
{
	$existing = $this->db->select('id')
		->from('submodule')
		->where('moduleid', (int) $module_id)
		->where('submodule', (string) $submodule_name)
		->limit(1)
		->get()
		->row();

	if (!empty($existing)) {
		return (int) $existing->id;
	}

	$insert_data = array(
		'moduleid' => (int) $module_id,
		'submodule' => (string) $submodule_name,
		'status' => 1,
		'addedOn' => date('Y-m-d H:i:s')
	);

	if ($this->db->field_exists('dynachem', 'submodule')) {
		$insert_data['dynachem'] = 0;
	}

	if ($this->db->field_exists('shubhampack', 'submodule')) {
		$insert_data['shubhampack'] = 2;
	}

	$this->db->insert('submodule', $insert_data);

	return (int) $this->db->insert_id();
}

function edit_capablities()
{

$user_id=$this->uri->segment('4');
	$module=$this->input->post('module');
	$module = is_array($module) ? $module : array();
	for($i=0;$i<count($module); $i++)
	{
	   $moduleid=$module[$i];
	   $moduleac=isset($_REQUEST['moduleaccess'.$moduleid]) ? $_REQUEST['moduleaccess'.$moduleid] : '0';
	   $modfg=$this->db->select('id')->from('module_access')->where('role_id',$user_id)->where('moduleid',$moduleid)->get();
	  if($modfg->num_rows()>0)
	  {
			$data=array('role_id'=>$user_id,'moduleid'=>$moduleid,'access'=>$moduleac,'addedOn'=>date('Y-m-d h:i:s'));         
			$this->db->where('moduleid',$moduleid);
			$this->db->where('role_id',$user_id);
			$this->db->update('module_access',$data);
			$modfg1=$this->db->select('id')->from('module_access')->where('role_id',$user_id)->where('moduleid',$moduleid)->get();
			foreach($modfg1->result() as $modfg12);
			$lid=$modfg12->id;
			
	    }else
	    {
		  $data=array('role_id'=>$user_id,'moduleid'=>$moduleid,'access'=>$moduleac,'addedOn'=>date('Y-m-d h:i:s'));
		 $this->db->insert('module_access',$data);
		 $lid=$this->db->insert_id(); 
		  
	    }
			
		$submodule=isset($_REQUEST['submodule'.$moduleid]) && is_array($_REQUEST['submodule'.$moduleid]) ? $_REQUEST['submodule'.$moduleid] : array();
			//echo "<pre>"; print_r($submodule);exit;
			for($j=0;$j<count($submodule);$j++)
			{
				$submoduleid=$submodule[$j];
			
			if($moduleac=='1')
			{
				if(isset($_REQUEST['add'.$moduleid.$submoduleid]))
				{
					$add='1';
				}else
				{
					 $add='0';
				}
				
				if(isset($_REQUEST['edit'.$moduleid.$submoduleid]))
				{
					$edit='1';
				}else
				{
					 $edit='0';
				}
				
				
				
				if(isset($_REQUEST['remove'.$moduleid.$submoduleid]))
				{
					$remove='1';
				}else
				{
					 $remove='0';
				}
				
				
				if($add=='1' || $edit=='1' || $remove=='1')
				{
					$submoduleaccess = "1";
				}else{
					$submoduleaccess = "0";
				}
				
				$isthere=$this->db->select('*')->from('module_capablity')->where('submoduleid',$submoduleid)->where('role_id',$user_id)->get();
				if($isthere->num_rows()=='0')
				{
					//echo "hi";exit;
				
				$data1=array('acessid'=>$lid,'role_id'=>$user_id,'moduleid'=>$moduleid,'submoduleid'=>$submoduleid,'submodule_access'=>$submoduleaccess,'madd'=>$add,'medit'=>$edit,'mremove'=>$remove,'addedOn'=>date('Y-m-d h:i:s'));
					//echo "<pre>"; print_r($data1);exit;
			
				$this->db->insert('module_capablity',$data1);
					
				}else
				{
				
					$data1=array('role_id'=>$user_id,'moduleid'=>$moduleid,'submoduleid'=>$submoduleid,'submodule_access'=>$submoduleaccess,'madd'=>$add,'medit'=>$edit,'mremove'=>$remove,'addedOn'=>date('Y-m-d h:i:s'));
					
				
				$this->db->where('submoduleid',$submoduleid);
				$this->db->where('role_id',$user_id);
				$this->db->update('module_capablity',$data1);
				}
				}
				}
			
			
	}
	
		$this->session->set_flashdata('message','<div class="alert alert-success" style="color:#000">Thank you, Access permission successfully updated.</div>');
	redirect(page_url.'Master/User_management/userwise_permission_dashboard/');

}

	private function get_permission_user($user_id)
	{
		return $this->db->select('a.user_id, a.title, a.first_name, a.last_name, a.email, a.business_location, a.department_id, b.company_name, c.department')
			->from('system_users a')
			->join('business_location b', 'a.business_location = b.business_loc_id', 'left')
			->join('departments c', 'a.department_id = c.department_id', 'left')
			->where('a.user_id', (int)$user_id)
			->limit(1)
			->get()
			->row_array();
	}

	private function ensure_service_visit_overview_submodule()
	{
		$submodule_name = 'SERVICE ENGINEER VISIT ASSIGNMENT OVERVIEW';
		$existing = $this->db->select('id')
			->from('submodule')
			->where('moduleid', 17)
			->where('submodule', $submodule_name)
			->limit(1)
			->get()
			->row_array();

		if (!empty($existing['id'])) {
			return (int)$existing['id'];
		}

		$insert_data = array(
			'moduleid' => 17,
			'submodule' => $submodule_name,
			'status' => 1,
			'addedOn' => date('Y-m-d H:i:s')
		);

		if ($this->db->field_exists('dynachem', 'submodule')) {
			$insert_data['dynachem'] = 0;
		}

		if ($this->db->field_exists('shubhampack', 'submodule')) {
			$insert_data['shubhampack'] = 2;
		}

		$this->db->insert('submodule', $insert_data);

		return (int)$this->db->insert_id();
	}

	private function get_permission_modules($user_id, $business_location)
	{
		$business_location = (int)$business_location;
		$service_visit_overview_submodule_id = $this->ensure_service_visit_overview_submodule();
		$df_change_control_map = array(
			41 => array(
				'label' => 'Raise ECN / IOM',
				'description' => 'Allow this user to create a new DF change-control request.',
				'badge' => 'DF Change Control'
			),
			42 => array(
				'label' => 'DF Change Control Dashboard',
				'description' => 'Allow this user to see HOD requests, assignments and the DF request dashboard.',
				'badge' => 'HOD Access'
			)
		);
		$custom_submodule_map = array();
		if ($service_visit_overview_submodule_id > 0) {
			$custom_submodule_map[$service_visit_overview_submodule_id] = array(
				'label' => 'Engineer Visit Assignment Overview',
				'description' => 'Allow this user to open the read-only engineer assignment calendar and see full visit details in the popup.',
				'badge' => 'Service Visits'
			);
		}

		$this->db->select('*')->from('system_modules')->where('status',1);
		if($business_location==1){
			$this->db->where('dynachem',1);
		}
		if($business_location==2){
			$this->db->where('shubhampack',2);
		}
		$module_rows = $this->db->order_by('id','ASC')->get()->result_array();

		$module_access_rows = $this->db->select('moduleid, access')
			->from('module_access')
			->where('role_id', (int)$user_id)
			->get()
			->result_array();
		$module_access_map = array();
		foreach ($module_access_rows as $access_row) {
			$module_access_map[(int)$access_row['moduleid']] = (string)$access_row['access'];
		}

		$capability_rows = $this->db->select('moduleid, submoduleid, submodule_access, madd, medit, mremove')
			->from('module_capablity')
			->where('role_id', (int)$user_id)
			->get()
			->result_array();
		$capability_map = array();
		foreach ($capability_rows as $capability_row) {
			$capability_map[(int)$capability_row['submoduleid']] = $capability_row;
		}

		$this->db->select('*')->from('submodule');
		if($business_location==1){
			$this->db->where('dynachem',1);
		}
		if($business_location==2){
			$this->db->where('shubhampack',2);
		}
		$this->db->group_start();
		$this->db->where('status','1');
		$this->db->or_group_start()->where('moduleid', 3)->where_in('id', array_keys($df_change_control_map))->group_end();
		$this->db->group_end();
		$submodule_rows = $this->db->order_by('moduleid','ASC')->order_by('id','ASC')->get()->result_array();

		$submodules_by_module = array();
		foreach ($submodule_rows as $submodule_row) {
			$moduleid = (int)$submodule_row['moduleid'];
			if (!isset($submodules_by_module[$moduleid])) {
				$submodules_by_module[$moduleid] = array();
			}

			$submodule_id = (int)$submodule_row['id'];
			$capability = isset($capability_map[$submodule_id]) ? $capability_map[$submodule_id] : array();
			$special_config = isset($custom_submodule_map[$submodule_id]) ? $custom_submodule_map[$submodule_id] : array();
			if (empty($special_config) && isset($df_change_control_map[$submodule_id])) {
				$special_config = $df_change_control_map[$submodule_id];
			}

			$submodules_by_module[$moduleid][] = array(
				'id' => $submodule_id,
				'label' => !empty($special_config['label']) ? $special_config['label'] : $submodule_row['submodule'],
				'description' => !empty($special_config['description']) ? $special_config['description'] : '',
				'badge' => !empty($special_config['badge']) ? $special_config['badge'] : '',
				'allow' => !empty($capability) && isset($capability['submodule_access']) && (string)$capability['submodule_access'] === '1',
				'edit' => !empty($capability) && isset($capability['medit']) && (string)$capability['medit'] === '1',
				'remove' => !empty($capability) && isset($capability['mremove']) && (string)$capability['mremove'] === '1',
				'is_df_change_control' => isset($df_change_control_map[$submodule_id])
			);
		}

		$modules = array();
		foreach ($module_rows as $module_row) {
			$module_id = (int)$module_row['id'];
			$modules[] = array(
				'id' => $module_id,
				'name' => $module_row['modulename'],
				'is_enabled' => isset($module_access_map[$module_id]) && $module_access_map[$module_id] === '1',
				'submodules' => isset($submodules_by_module[$module_id]) ? $submodules_by_module[$module_id] : array()
			);
		}

		return $modules;
	}

	private function build_permission_summary($modules)
	{
		$summary = array(
			'total_modules' => count($modules),
			'active_modules' => 0,
			'total_shortcuts' => 0,
			'allowed_shortcuts' => 0,
			'df_shortcuts' => 0
		);

		foreach ($modules as $module_row) {
			if (!empty($module_row['is_enabled'])) {
				$summary['active_modules']++;
			}

			$summary['total_shortcuts'] += count($module_row['submodules']);

			foreach ($module_row['submodules'] as $submodule_row) {
				if (!empty($submodule_row['allow'])) {
					$summary['allowed_shortcuts']++;
				}
				if (!empty($submodule_row['is_df_change_control'])) {
					$summary['df_shortcuts']++;
				}
			}
		}

		return $summary;
	}
	public function reference()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('business_loc', 'Business Location', 'required|trim');
		$this->form_validation->set_rules('department', 'department Name', 'required|trim');
		$this->form_validation->set_rules('reference_title', 'reference', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/reference');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "department_reference";
		$query = $this->db->select('department_id, reference_title')->from('department_reference')->where('business_loc_id',$this->input->post('business_loc'))->where('department_id',$this->input->post('department'))->where('reference_title',$this->input->post('reference_title'))->get();
		$res = $query->result();
		
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry! This record already exist.</div>');
			redirect(page_url.'Master/User_management/reference');
			
		}else{
		
				
			$data = array('department_id'=>$this->input->post('department'),
			'business_loc_id'=>strtoupper($this->input->post('business_loc')),
			'reference_title'=>strtoupper($this->input->post('reference_title')),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Master/User_management/reference');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/User_management/reference');
		}
		}
		
	}
		
	}
	
public function reference_list()
	{
		$i=1;
		$department_data= array();
		$this->db->select('a.*,b.business_loc_id, c.state_name, d.city_name, e.department')->from('department_reference a');
		$this->db->join('business_location b','a.business_loc_id=b.business_loc_id','left');
		$this->db->join('states c','b.state_id=c.state_id','left');
		$this->db->join('cities d','d.city_id=b.city_id','left');
		$this->db->join('departments e','a.department_id=e.department_id','left');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Master/User_management/update_reference_status/".$row->ref_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Master/User_management/update_reference_status/".$row->ref_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Master/User_management/edit_reference/".$row->ref_id."'><i class='fa fa-pencil'></i></a>";	
			
			$add = "<a href='".page_url."Master/User_management/sub_reference/".$row->ref_id."' class='btn btn-success btn-xs'>Add Sub Reference</a>";
			
			$department_data[] = array('sr_no'=>$i,
			'company_name'=>$row->state_name." ".$row->city_name,
			'department'=>$row->department,
			'reference'=>$row->reference_title,
			'add_subref'=>$add,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($department_data),
			"iTotalDisplayRecords" => count($department_data),
			"aaData"=>$department_data);
			
		echo json_encode($results);
	}
public function sub_reference()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('reference_title', 'reference', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/sub-reference');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "department_sub_reference";
			
		$photo=$_FILES['fileupload']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$finaldata=time().'.'.$cat_image;
				move_uploaded_file($_FILES['fileupload']["tmp_name"],UPLOADPATH.'reference/' . $finaldata);
			}else
			{
				$finaldata="";
				}
				
			$data = array(
			'ref_id'=>$this->uri->segment(4),
			'sub_ref_title'=>strtoupper($this->input->post('reference_title')),
			'attachment'=>$finaldata,
			'video_link'=>$this->input->post('video_link'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Master/User_management/sub_reference/'.$this->uri->segment(4));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/User_management/sub_reference/'.$this->uri->segment(4));
		}
		
		
	}
		
	}
	
public function sub_reference_list()
	{
		$i=1;
		$department_data= array();
		$this->db->select('*')->from('department_sub_reference')->where('ref_id',$this->uri->segment(4));
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Master/User_management/update_sub_reference_status/".$row->sub_ref_id."/".$row->status."/".$row->ref_id."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Master/User_management/update_sub_reference_status/".$row->sub_ref_id."/".$row->status."/".$row->ref_id."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Master/User_management/edit_sub_reference/".$row->sub_ref_id."'><i class='fa fa-pencil'></i></a>";	
			
			$add = "<a href='".page_url."Master/User_management/sub_subreference/".$row->ref_id."/".$row->sub_ref_id."'><span class='btn btn-danger btn-xs'>Add 3rd Level Sub Reference</span></a>";
			
			$attachment = "<a href='".referencefilepath.$row->attachment."' class='btn btn-success btn-xs' download>Click here to download</a>";
			
			$department_data[] = array('sr_no'=>$i,
			'reference'=>$row->sub_ref_title,
			'attachment'=>$attachment,
			'video_link'=>$row->video_link,
			'add'=>$add,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($department_data),
			"iTotalDisplayRecords" => count($department_data),
			"aaData"=>$department_data);
			
		echo json_encode($results);
	}	
	
	public function update_reference_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(4);
		$sval =  $this->uri->segment(5);
		$field_name = "ref_id";
		$table = "department_reference";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Status successfully updated.</div>');
			redirect(page_url.'Master/User_management/reference');
		}
	public function update_sub_reference_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(4);
		$sval =  $this->uri->segment(5);
		$field_name = "sub_ref_id";
		$table = "department_sub_reference";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Status successfully updated.</div>');
			redirect(page_url.'Master/User_management/reference/'.$this->uri->segment(6));
		}
	
public function edit_reference(){
		$this->load->view('master/edit_reference');
	}
	
	public function update_reference()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('business_loc', 'Business Location', 'required|trim');
		$this->form_validation->set_rules('department', 'department Name', 'required|trim');
		$this->form_validation->set_rules('reference_title', 'reference', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/edit_reference');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "department_reference";
		$query = $this->db->select('department_id, reference_title')->from('department_reference')->where('business_loc_id',$this->input->post('business_loc'))->where('department_id',$this->input->post('department'))->where('reference_title',$this->input->post('reference_title'))->get();
		$res = $query->result();
		
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry! This record already exist.</div>');
			redirect(page_url.'Master/User_management/reference');
			
		}else{
		
				
			$data = array('department_id'=>$this->input->post('department'),
			'business_loc_id'=>strtoupper($this->input->post('business_loc')),
			'reference_title'=>strtoupper($this->input->post('reference_title')),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
			$this->db->where('ref_id',$this->uri->segment(4));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Master/User_management/reference');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/User_management/reference');
		}
		}
		
	}
		
	}
	
	public function edit_sub_reference(){
		$this->load->view('master/edit_sub_reference');
	}
	
	public function update_sub_reference()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('reference_title', 'reference', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/sub-reference');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "department_sub_reference";
			$oldfile = $this->input->post('oldfile');
		$photo=$_FILES['fileupload']['name'];
			if($photo<>'')
			{
				unlink(UPLOADPATH.'reference/'.$oldfile);
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$finaldata=time().'.'.$cat_image;
				move_uploaded_file($_FILES['fileupload']["tmp_name"],UPLOADPATH.'reference/' . $finaldata);
			}else
			{
				$finaldata=$this->input->post('oldfile');
				}
				
			$data = array(
			'ref_id'=>$this->uri->segment(4),
			'sub_ref_title'=>strtoupper($this->input->post('reference_title')),
			'attachment'=>$finaldata,
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'video_link'=>$this->input->post('video_link'),
			'added_by'=>$user_id);
			$this->db->where('sub_ref_id',$this->uri->segment(5));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Master/User_management/sub_reference/'.$this->uri->segment(4));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/User_management/sub_reference/'.$this->uri->segment(4));
		}
		
		
	}
		
	}
public function add_new_vendor()
	{
		$this->load->view('master/vendor');
	}

	public function add_new_vendors()
	{
		
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "vendors";
		if($this->input->post('del_type')=='Other'){
		   $deliverytime = $this->input->post('otherdtype'); 
		}else{
		   $deliverytime= $this->input->post('del_type');
		}

		$state_code = substr($this->input->post('gst'), 0, 2);


		$state_id = $this->master->getStateID($state_code);
		// echo $state_id;exit;
					
		$data = array('name'=>$this->input->post('vendor_name'),
    'phone'=>$this->input->post('contact'),
    'company'=>$this->input->post('company'),
    'code'=>$this->input->post('vendor_code'),
		'email'=>$this->input->post('email'),
		'address'=>$this->input->post('address'),
		'gst'=>$this->input->post('gst'),
		'state'=>$state_id,
		// 'payment_terms'=>$this->input->post('payment'),
		// 'payment_mode'=>$this->input->post('pmode'),
		'status'=>'1',
		'addedOn'=>$date,
		// 'credit_days'=>$this->input->post('credit'),
		// 'packingpercentage'=>$this->input->post('packingpercentage'),
		// 'freightpercentage'=>$this->input->post('freightpercentage'),
		'addedBy'=>$_SESSION['logged_in']['user_id'],
		'contactperson'=>$this->input->post('contactperson'),
		'pan'=>$this->input->post('pan')
		// 'deliveryby'=>$this->input->post('dby'),
		// 'deliverytime'=>$deliverytime,
		// 'otherdeliverytime'=>$this->input->post('otherdtype')
	);
        
		$result  = $this->master->insert_record($table,$data);	
		// $lid=$this->db->insert_id();
		// if($result)
		// {
		 //    if($this->uri->segment(4)<>'')
			// {
			// 	$itemid=$this->input->post('itemid');
			// 	$originalprice=$this->input->post('originalprice');
			// 	$discounttype=$this->input->post('discounttype');
			// 	$discount_percent=$this->input->post('discount_percent');
			// 	$discount_price=$this->input->post('discount_price');
			// 	$finalvalue=$this->input->post('finalvalue');
			// 	$prno=$this->input->post('prno');
			// 	$prtyypee=$this->getpotype($prno);
			// 	if($discounttype=='1')
			// 	{
			// 	$per=$discount_percent;
			// 	}else{

			// 	/** Calculate  Percentage **/
			// 	$diff=$original-$finalvalue;

			// 	$pers=$diff*100;

			// 	$per=$pers/$originalprice;

			// 	}
			// 	if($prtyypee<>'NA')
			// 	{
				   
			// 		if($prtyypee=='0')
			// 		{
			// 			$tableee="vendors_price";
						
			// 			$data=array('masterid'=>$itemid,'itemid'=>$itemid,'vendorid'=>$lid,'listprice'=>$originalprice,'discount'=>$per,'price'=>$finalvalue,'upd'=>'1','green_supplier'=>'1','quoteid'=>$this->uri->segment(4));
			// 			$this->db->insert('vendors_price',$data);
						
			// 		}else
			// 		{
			// 			$tableee="vendorwise_house_keeping_item_price";
						
			// 			$data=array('item_id'=>$itemid,'vendor_id'=>$lid,'price'=>$finalvalue,'added_on'=>date('Y-m-d H:i:s'),'added_by'=>$_SESSION['logged_in']['user_id'],'green_supplier'=>'1','quoteid'=>$this->uri->segment(4));
			// 			$this->db->insert('vendorwise_house_keeping_item_price',$data);
			// 		}
			// 	}else{
					
			// 		echo "PR TYPE NOT FOUND"; exit;
			// 	}
				
				

					

			// }
			
		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
		redirect(page_url.'Master/User_management/add_new_vendor');

		// }

		
	}
public function add_new_vendorsOlddd()
	{
		
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "vendors";
		if($this->input->post('del_type')=='Other'){
		   $deliverytime = $this->input->post('otherdtype'); 
		}else{
		   $deliverytime= $this->input->post('del_type');
		}
					
		$data = array('name'=>$this->input->post('vendor_name'),
		'phone'=>$this->input->post('contact'),
		'email'=>$this->input->post('email'),
		'address'=>$this->input->post('address'),
		'gst'=>$this->input->post('gst'),
		// 'payment_terms'=>$this->input->post('payment'),
		// 'payment_mode'=>$this->input->post('pmode'),
		'status'=>'1',
		'addedOn'=>$date,
		// 'credit_days'=>$this->input->post('credit'),
		// 'packingpercentage'=>$this->input->post('packingpercentage'),
		// 'freightpercentage'=>$this->input->post('freightpercentage'),
		'addedBy'=>$_SESSION['logged_in']['user_id'],
		'contactperson'=>$this->input->post('contactperson'),
		'pan'=>$this->input->post('pan')
		// 'deliveryby'=>$this->input->post('dby'),
		// 'deliverytime'=>$deliverytime,
		// 'otherdeliverytime'=>$this->input->post('otherdtype')
	);
        
		$result  = $this->master->insert_record($table,$data);	
		// $lid=$this->db->insert_id();
		// if($result)
		// {
		 //    if($this->uri->segment(4)<>'')
			// {
			// 	$itemid=$this->input->post('itemid');
			// 	$originalprice=$this->input->post('originalprice');
			// 	$discounttype=$this->input->post('discounttype');
			// 	$discount_percent=$this->input->post('discount_percent');
			// 	$discount_price=$this->input->post('discount_price');
			// 	$finalvalue=$this->input->post('finalvalue');
			// 	$prno=$this->input->post('prno');
			// 	$prtyypee=$this->getpotype($prno);
			// 	if($discounttype=='1')
			// 	{
			// 	$per=$discount_percent;
			// 	}else{

			// 	/** Calculate  Percentage **/
			// 	$diff=$original-$finalvalue;

			// 	$pers=$diff*100;

			// 	$per=$pers/$originalprice;

			// 	}
			// 	if($prtyypee<>'NA')
			// 	{
				   
			// 		if($prtyypee=='0')
			// 		{
			// 			$tableee="vendors_price";
						
			// 			$data=array('masterid'=>$itemid,'itemid'=>$itemid,'vendorid'=>$lid,'listprice'=>$originalprice,'discount'=>$per,'price'=>$finalvalue,'upd'=>'1','green_supplier'=>'1','quoteid'=>$this->uri->segment(4));
			// 			$this->db->insert('vendors_price',$data);
						
			// 		}else
			// 		{
			// 			$tableee="vendorwise_house_keeping_item_price";
						
			// 			$data=array('item_id'=>$itemid,'vendor_id'=>$lid,'price'=>$finalvalue,'added_on'=>date('Y-m-d H:i:s'),'added_by'=>$_SESSION['logged_in']['user_id'],'green_supplier'=>'1','quoteid'=>$this->uri->segment(4));
			// 			$this->db->insert('vendorwise_house_keeping_item_price',$data);
			// 		}
			// 	}else{
					
			// 		echo "PR TYPE NOT FOUND"; exit;
			// 	}
				
				

					

			// }
			
		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
		redirect(page_url.'Master/User_management/add_new_vendor');

		// }

		
	}


	public function vendor_list()
	{
		$i=1;
		$vendor_data= array();
		$this->db->select('a.*,companyname')->from('vendors a')->join('store_rack_location b','a.company = b.id','left');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Master/User_management/update_department_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Master/User_management/update_department_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Master/User_management/edit_vendor/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			
			
				if($row->deliverytime<>'Other')
				{
					$delby=$row->deliverytime;
				}
				else{
					
					$delby=$row->otherdeliverytime;
				}

				if($row->hpcl==1)
				{
					$h="YES";
				}else
				{
					$h="NO";
				}
				if($row->hpcl==1)
				{
					if($_SESSION['logged_in']['role']==1)
					{
					$edit=$edit;
					}else
					{
						$edit='';
					}
				}else
				{
				
					$edit=$edit;
				}
			$vendor_data[] = array('sr_no'=>$i,
			'vendor_name'=>$row->name,
			'code'=>$row->code,
			'hpcl'=>$h,
      'person'=>$row->contactperson,
      'company'=>$row->companyname,
			'phone'=>$row->phone,
			'email'=>$row->email,
			'address'=>$row->address,
			'gst'=>$row->gst,
			'pan'=>$row->pan,
			'payment'=>$row->payment_terms,
			 'payment_mode'=>$row->payment_mode,
			'credit'=>$row->credit_days,
			'deliveryby'=>$row->deliveryby,
			'deliverytime'=>$delby. "Days",
			'packingpercentage'=>$row->packingpercentage,
			'freightpercentage'=>$row->freightpercentage,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($vendor_data),
			"iTotalDisplayRecords" => count($vendor_data),
			"aaData"=>$vendor_data);
			
		echo json_encode($results);
	}
	public function vendor_listOlddd()
	{
		$i=1;
		$vendor_data= array();
		$this->db->select('*')->from('vendors');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Master/User_management/update_department_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Master/User_management/update_department_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Master/User_management/edit_vendor/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			
			
				if($row->deliverytime<>'Other')
				{
					$delby=$row->deliverytime;
				}
				else{
					
					$delby=$row->otherdeliverytime;
				}
			$vendor_data[] = array('sr_no'=>$i,
			'vendor_name'=>$row->name,
			'person'=>$row->contactperson,
			'phone'=>$row->phone,
			'email'=>$row->email,
			'address'=>$row->address,
			'gst'=>$row->gst,
			'pan'=>$row->pan,
			'payment'=>$row->payment_terms,
			 'payment_mode'=>$row->payment_mode,
			'credit'=>$row->credit_days,
			'deliveryby'=>$row->deliveryby,
			'deliverytime'=>$delby. "Days",
			'packingpercentage'=>$row->packingpercentage,
			'freightpercentage'=>$row->freightpercentage,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($vendor_data),
			"iTotalDisplayRecords" => count($vendor_data),
			"aaData"=>$vendor_data);
			
		echo json_encode($results);
	}
	
	public function edit_vendor(){
		$this->load->view('master/edit_vendor');
	}
	

	public function update_vendor()
	{
		
		
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "vendors";
	    if($this->input->post('del_type')=='Other')
	    {
	        $deliverydays = $this->input->post('otherdtype');
	    }else{
	        $deliverydays=$this->input->post('del_type');
	    }

	    $state_code = substr($this->input->post('gst'), 0, 2);

		$state_id = $this->master->getStateID($state_code);
			
			if($this->input->post('hpcl_vendor')<>'')
			{
				$hpcl_v=1;
			}else
			{
				$hpcl_v=0;
			}

		$data = array('name'=>$this->input->post('vendor_name'),
		'company'=>$this->input->post('company'),
		'code'=>$this->input->post('vendor_code'),
		'hpcl'=>$hpcl_v,
		'phone'=>$this->input->post('contact'),
		'email'=>$this->input->post('email'),
		'address'=>$this->input->post('address'),
		'gst'=>$this->input->post('gst'),
		'state'=>$state_id,
		'hpcl_location'=>$this->input->post('location'),
		// 'payment_terms'=>$this->input->post('payment'),
		// 	'payment_mode'=>$this->input->post('pmode'),
		// 'credit_days'=>$this->input->post('credit'),
		'contactperson'=>$this->input->post('contactperson'),
		'pan'=>$this->input->post('pan')
		// 'deliveryby'=>$this->input->post('dby'),
		// 'freightpercentage'=>$this->input->post('freightpercentage'),
		// 'packingpercentage'=>$this->input->post('packingpercentage'),
		// 'deliverytime'=>$deliverydays,
		// 'otherdeliverytime'=>$this->input->post('otherdtype')
		);
		
			$this->db->where('id',$this->uri->segment(4));
			$result  = $this->db->update($table,$data);	
				if($result)
				{
				$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
				redirect(page_url.'Master/User_management/add_new_vendor');

				}else
				{
				$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
				redirect(page_url.'Master/User_management/add_new_vendor');
				}
	
		
	}
	public function update_vendorOLdd()
	{
		
		
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "vendors";
	    if($this->input->post('del_type')=='Other')
	    {
	        $deliverydays = $this->input->post('otherdtype');
	    }else{
	        $deliverydays=$this->input->post('del_type');
	    }
			
			$data = array('name'=>$this->input->post('vendor_name'),
		'phone'=>$this->input->post('contact'),
		'email'=>$this->input->post('email'),
		'address'=>$this->input->post('address'),
		'gst'=>$this->input->post('gst'),
		// 'payment_terms'=>$this->input->post('payment'),
		// 	'payment_mode'=>$this->input->post('pmode'),
		// 'credit_days'=>$this->input->post('credit'),
		'contactperson'=>$this->input->post('contactperson'),
		'pan'=>$this->input->post('pan')
		// 'deliveryby'=>$this->input->post('dby'),
		// 'freightpercentage'=>$this->input->post('freightpercentage'),
		// 'packingpercentage'=>$this->input->post('packingpercentage'),
		// 'deliverytime'=>$deliverydays,
		// 'otherdeliverytime'=>$this->input->post('otherdtype')
	);
		
			$this->db->where('id',$this->uri->segment(4));
			$result  = $this->db->update($table,$data);	
				if($result)
				{
				$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
				redirect(page_url.'Master/User_management/add_new_vendor');

				}else
				{
				$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
				redirect(page_url.'Master/User_management/add_new_vendor');
				}
	
		
	}
	
	public function email_template_list()
	{
		$i=1;
		$department_data= array();
		$this->db->select('*')
				 ->from('departmentwise_email_template');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			
			$edit = "<a href='".page_url."Master/User_management/edit_email_template/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			
			

			if($row->send_email == 1) {
				$send_email = "Email";
			} else {
				$send_email = "";
			}

			$picture = "";
			
			$triggeremail = '<a href="'.page_url.'Master/User_management/triggeremail/'.$row->id.'"><span class="btn btn-success btn-xs">Trigger Email <i class="fa fa-envelope"></i></span></a>';
			$department_data[] = array('sr_no'=>$i,
			'selection' => $send_email,
			'subject'=>$row->email_subject,
			'email_template'=>$row->email_template,
			'picture'=>$picture,
			'triggeremail'=>$triggeremail,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($department_data),
			"iTotalDisplayRecords" => count($department_data),
			"aaData"=>$department_data);
			
		echo json_encode($results);
	}

public function triggeremail(){
	$this->load->view('master/triggeremail');
}
public function edit_email_template(){
		$this->load->view('master/edit_email_template');
	}
	
public function update_email_template()
	{
		
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$table = "departmentwise_email_template";
		
		$photo=$_FILES['attchment']['name'];
		if($photo<>'')
		{
			$image1=explode('.',$photo);
			$cat_image=end($image1);
			$finaldata=time().'.'.$cat_image;
			move_uploaded_file($_FILES['attchment']["tmp_name"],UPLOADPATH.'reference/' . $finaldata);
		}else
		{
			$finaldata="";
			}

		$send_email = 1;
		$data = array(
			'exhibition_id'=>$this->input->post('Exhibition'),
			'email_subject'=>$this->input->post('subject'),
			'send_email'=>$send_email,
			'file_attachment'=>$finaldata,
			'email_template'=>$this->input->post('email_template'));

			$this->db->where('id',$this->uri->segment(4));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Master/User_management/email_template');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/User_management/email_template');
		}
		
	
		
	}	

	public function email_template()
	{
		$this->load->view('master/subject');
	}
	
	public function add_email_template()
	{
	
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		$table = "departmentwise_email_template";

		$send_email = 1;
		$photo=$_FILES['attchment']['name'];
		if($photo<>'')
		{
			$image1=explode('.',$photo);
			$cat_image=end($image1);
			$finaldata=time().'.'.$cat_image;
			move_uploaded_file($_FILES['attchment']["tmp_name"],UPLOADPATH.'reference/' . $finaldata);
		}else
		{
			$finaldata="";
			}

		$data = array(
			'email_subject'=>$this->input->post('subject'),
			'send_email'=>$send_email,
			'email_template'=>$this->input->post('email_template'),
			'added_on'=>$date,
			'exhibition_id'=>$this->input->post('Exhibition'),
			'file_attachment'=>$finaldata,
			'added_by'=>$user_id);
		
		$this->db->insert('departmentwise_email_template',$data);
		$lastid = $this->db->insert_id();
		if($lastid)
		{

		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Master/User_management/email_template');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error occurred.</div><br/>');
			redirect(page_url.'Master/User_management/email_template');
		}
	
		
	}
	
	public function view_email_template(){
		$this->load->view('master/view_email_template');
	}
	
	public function sub_subreference()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('reference_title', 'reference', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/3rd_level_subreference');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "department_sub_sub_reference";
			
		$photo=$_FILES['fileupload']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$finaldata=time().'.'.$cat_image;
				move_uploaded_file($_FILES['fileupload']["tmp_name"],UPLOADPATH.'reference/' . $finaldata);
			}else
			{
				$finaldata="";
				}
			
				
			$data = array(
			'ref_id'=>$this->uri->segment(4),
			'sub_ref_id'=>$this->uri->segment(5),
			'sub_ref_title'=>strtoupper($this->input->post('reference_title')),
			'attachment'=>$finaldata,
			'video_link'=>$this->input->post('video_link'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Master/User_management/sub_subreference/'.$this->uri->segment(4)."/".$this->uri->segment(5));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/User_management/sub_subreference/'.$this->uri->segment(4)."/".$this->uri->segment(5));
		}
		
		
	}
		
	}
	
public function sub_subreference_list()
	{
		$i=1;
		$department_data= array();
		$this->db->distinct();
		$this->db->select('*')->from('department_sub_sub_reference')->where('sub_ref_id',$this->uri->segment(4));
		$query = $this->db->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Master/User_management/update_sub_subreference_status/".$row->sub_subref_id."/".$row->status."/".$row->ref_id."/".$row->sub_ref_id."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Master/User_management/update_sub_subreference_status/".$row->sub_subref_id."/".$row->status."/".$row->ref_id."/".$row->sub_ref_id."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Master/User_management/edit_sub_subreference/".$row->sub_subref_id."/".$row->ref_id."/".$row->sub_ref_id."'><i class='fa fa-pencil'></i></a>";	
			
			$attachment = "<a href='".referencefilepath.$row->attachment."' class='btn btn-success btn-xs' download>Click here to download</a>";
			
			$department_data[] = array('sr_no'=>$i,
			'sub_ref_title'=>$row->sub_ref_title,
			'attachment'=>$attachment,
			'status'=>$sta,
			'video_link'=>$row->video_link,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($department_data),
			"iTotalDisplayRecords" => count($department_data),
			"aaData"=>$department_data);
			
		echo json_encode($results);
	}	
	
	public function update_sub_subreference_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(4);
		$sval =  $this->uri->segment(5);
		$field_name = "sub_subref_id";
		$table = "department_sub_sub_reference";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Status successfully updated.</div>');
			redirect(page_url.'Master/User_management/sub_subreference/'.$this->uri->segment(6)."/".$this->uri->segment(7));
		}

public function edit_sub_subreference(){
	$this->load->view('master/edit_sub_subreference');
}

public function update_sub_subreference()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('reference_title', 'reference', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/edit_sub_subreference');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "department_sub_sub_reference";
			
		$photo=$_FILES['fileupload']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$finaldata=time().'.'.$cat_image;
				move_uploaded_file($_FILES['fileupload']["tmp_name"],UPLOADPATH.'reference/' . $finaldata);
			}else
			{
				$finaldata=$this->input->post('oldfile');
				}
				
			$data = array(
			'ref_id'=>$this->uri->segment(5),
			'sub_ref_id'=>$this->uri->segment(6),
			'sub_ref_title'=>strtoupper($this->input->post('reference_title')),
			'attachment'=>$finaldata,
			'status'=>$this->input->post('status'),
			'video_link'=>$this->input->post('video_link'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			$this->db->where('sub_subref_id',$this->uri->segment(4));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Master/User_management/sub_subreference/'.$this->uri->segment(5)."/".$this->uri->segment(6));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/User_management/sub_subreference/'.$this->uri->segment(5)."/".$this->uri->segment(6));
		}
		
		
	}
		
	}
	
public function set_ims_header_permission()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('min_qty', 'min_qty', 'required|trim');
		$this->form_validation->set_rules('vendor', 'vendor', 'required|trim');
		$this->form_validation->set_rules('item_price', 'item_price', 'required|trim');
			$this->form_validation->set_rules('blocked', 'Blocked Stock', 'required|trim');
				$this->form_validation->set_rules('gst', 'GST', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/set_ims_header_permission');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "ims_header_permission";
		
		$data = array('user_id'=>$this->uri->segment(4),
			'qty'=>$this->input->post('min_qty'),
			'vendor'=>$this->input->post('vendor'),
			'price'=>$this->input->post('item_price'),
			'bstock'=>$this->input->post('blocked'),
			'gst'=>$this->input->post('gst'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			
			$data1 = array('user_id'=>$this->uri->segment(4),
			'per_unit_price'=>$this->input->post('per_unit_price'),
			'opening_stock'=>$this->input->post('opening_stock'),
			'min_qty'=>$this->input->post('min_qty'),
			'total_received'=>$this->input->post('total_received'),
			'total_issued'=>$this->input->post('total_issued'),
			'issue_item'=>$this->input->post('issue_item'),
			'receive_item'=>$this->input->post('receive_item'),
			'block_items'=>$this->input->post('block_items'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			$this->db->insert('imported_items_permission',$data1);
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Master/User_management/userwise_permission_dashboard');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/User_management/userwise_permission_dashboard');
		}
		
		
	}
		
	}
		public function edit_ims_header_permission()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('min_qty', 'min_qty', 'required|trim');
		$this->form_validation->set_rules('vendor', 'vendor', 'required|trim');
		$this->form_validation->set_rules('item_price', 'item_price', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/edit_ims_header_permission');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "ims_header_permission";
		
		$data = array(
			'qty'=>$this->input->post('min_qty'),
			'vendor'=>$this->input->post('vendor'),
			'price'=>$this->input->post('item_price'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			$this->db->where('user_id',$this->uri->segment(4));
			
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$data1 = array('user_id'=>$this->uri->segment(4),
			'per_unit_price'=>$this->input->post('per_unit_price'),
			'opening_stock'=>$this->input->post('opening_stock'),
			'min_qty'=>$this->input->post('min_qty'),
			'total_received'=>$this->input->post('total_received'),
			'total_issued'=>$this->input->post('total_issued'),
			'issue_item'=>$this->input->post('issue_item'),
			'receive_item'=>$this->input->post('receive_item'),
			'block_items'=>$this->input->post('block_items'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			$this->db->where('user_id',$this->uri->segment(4));
			$result  = $this->db->update('imported_items_permission',$data1);
			
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Master/User_management/userwise_permission_dashboard');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/User_management/userwise_permission_dashboard');
		}
		
		
	}
		
	}

public function mark_your_attendance()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('attendance', 'attendance', 'required|trim');
        $user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/mark_your_attendance');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$time = date('H:i:a');
		$table = "mark_your_attendance";
		$random = rand();
		$finaldata= "";
		
				
		
		$query = $this->db->select('id,morning_time')->from('mark_your_attendance')->where('employee_id',$user_id)->where('attendance_date',date('Y-m-d'))->get();
		if($query->num_rows()>0){
			
			foreach($query->result() as $attdata);
		/** Days Logic **/
		
		$halfdaymin=4*60;  /** 240 **/
		$earlyleave=6*60;  /** 360 **/
		$fulldaymin=8*60;  /** 480 **/
		$morningatttime=date('Y-m-d')." ".date('H:i',strtotime($attdata->morning_time));
		$currtime=date('Y-m-d')." ".date('H:i');
		$mindiff =(strtotime($currtime) - strtotime($morningatttime))/60;
		if($mindiff>=$fulldaymin)
		{
			
			$r="FULL";
			
		}else if($mindiff>=$earlyleave && $mindiff<$fulldaymin)
		{
		/** CHECK FOR EARLY DAY TWICE IN MONTH **/
			$ecou=$this->checkforearlydaycount($user_id);
			if($ecou<=2)
			{
			$r="SHORT";	
			}else{
					
				$r="HALF";	
			}
			
		}else if($mindiff<360 && $mindiff>=$halfdaymin)
		{
			$r="HALF";
			
		}else{
			
			$r="ABSENT";
		}	
			$data = array('employee_id'=>$user_id,
			'attendance_date'=>date('Y-m-d'),
			'evening_time'=>$time,
			'evening_selfie'=>$finaldata,
			'daystat'=>$r,
			'added_on'=>$date);
			//echo "<pre>"; print_r($data); exit;
			$this->db->where('employee_id',$user_id);
			$this->db->where('attendance_date',date('Y-m-d'));
			$this->db->update('mark_your_attendance',$data);
		}else{
		    
		  
		    $r="";
			$data = array('employee_id'=>$user_id,
			'attendance_date'=>date('Y-m-d'),
			'morning_time'=>$time,
			'morning_selfie'=>$finaldata,
			'daystat'=>$r,
			'added_on'=>$date);
			//echo "<pre>"; print_r($data); exit;
			$this->db->insert('mark_your_attendance',$data);
		}
			$this->session->set_flashdata('message','<span class="alert alert-success">Thank you, Your attendance successfully marked.</span>');
			redirect(page_url.'Master/User_management/mark_your_attendance');
			
		
	}
		
	}
	
	function checkforearlydaycount()
	{
		$startdate=date('Y-m').'-01';
		$enddate=date('Y-m-t');
		$qu=$this->db->select('id')->from('mark_your_attendance')->where('daystat','SHORT')->where('attendance_date BETWEEN "'.$startdate. '" and "'.$enddate.'"')->get();
		 return $qu->num_rows();
		
		
	}
	
	public function your_attendance_report()
	{
		$i=1;
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$attendance_date= array();
		$this->db->select('*')->from('mark_your_attendance')->where('employee_id',$user_id)->order_by('id','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
		     $empname = $row->employee_name;
		   
			if($row->morning_selfie!==''){
				$morningselfie = "<img src='".selfiepath.$row->morning_selfie."' width='100px'>";
			}else{
				$morningselfie="";
			}
			if($row->evening_selfie!==''){
				$eveningselfie = "<img src='".selfiepath.$row->evening_selfie."' width='100px'>";
			}else{
				$eveningselfie="";
			}
			
			if($row->absent_status=='1'){
			   $evntime="";
			   $morningtime = "";
			   
			}else{
			   $evntime="";
			   if($row->morning_time=='00:00:00'){
			      $morningtime=""; 
			   }else{
			      $morningtime = date('h:i a',strtotime($row->morning_time)); 
			   }
			   
			    if($row->evening_time!=='00:00:00'){
			        $evntime=date('h:i A',strtotime($row->evening_time));
			        
			    }
			    
			    
			    
			}
			
			$remarks="";
			    $markattendance="";
			   
			    
			    
			    if($row->remarks==''){
			    $markattendance  ="Pending at HR";
			    }else{
			        //$remarks= $row->remarks."<br><br> <strong>UPDATED BY </strong>".$row->hrfname." ".$row->hrlname;
					$remarks = "";
					$markattendance = "";
			    }
			  
		
			
			if($row->evening_time!=='00:00:00'){
			   $time1 = $row->morning_time;
$time2 =$row->evening_time;  
list($hours, $minutes) = explode(':', $time1);
$startTimestamp = mktime($hours, $minutes);

list($hours, $minutes) = explode(':', $time2);
$endTimestamp = mktime($hours, $minutes);

$seconds = $endTimestamp - $startTimestamp;
if($seconds < 0) {
    $seconds=60*60*24; 
}

$minutes = ($seconds / 60) % 60;
$hours = round($seconds / (60 * 60));

$difference= "<b>$hours </b> hours and <b>$minutes</b> minutes</b>";
			}else{
			    $difference="";
			}
			
		if($row->absent_status=='1'){
		    $attendace_status="Absent";
		}else if($row->onleave=='1'){
		    $attendace_status="Leave";
		}else{
		    $attendace_status="Present";
		}
		 $markabsent = "<span class='btn btn-danger'></span>";
			    $markleave = "<span class='btn btn-success'></span>";
			
			$attendance_date[] = array('sr_no'=>$i,
			'employee_name'=>$empname,
			'attendance_date'=>date('d-m-Y',strtotime($row->attendance_date)),
			'morning_time'=>$morningtime,
			'morning_selfie'=>$morningselfie,
			'evening_selfie'=>$eveningselfie,
			'evening_time'=>$evntime,
			'daystat'=>$difference,
			'attendace_status'=>$attendace_status,
			'markattendance'=>$markattendance." ".$remarks,
			'markpresntabsent'=>$markabsent." ".$markleave);
			$i++;
		}
		
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($attendance_date),
			"iTotalDisplayRecords" => count($attendance_date),
			"aaData"=>$attendance_date);
			
		echo json_encode($results);
	}

	public function attendance_dashboard(){
	

		$this->load->view('master/attendance_dashboard');
	}
	
	public function attendance_report()
	{
		$i=1;
		$uri = $this->uri->segment(4);
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		
		$q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
		if($q->num_rows()>0)
		{
		foreach($q->result() as $teamdetail);
		 $teammembers = array();
		$query = $this->db->select('team_id, employee_id')->from('presto_team_members')->where('team_id',$teamdetail->team_id)->get();
        $res = $query->result();
         foreach($res as $teaminfo){
            $teammembers[] = $teaminfo->employee_id;
            
        }
	
		//$team = implode(',',$teammembers);
		$team = "'" . implode ( "', '", $teammembers ) . "'";
		
		}else
		{
		    $team='NA';
		}
		
		
		
		$attendance_date= array();
		$this->db->select('a.*, c.first_name as hrfname, c.last_name as hrlname')->from('mark_your_attendance a')->join('system_users c','a.hr_id=c.user_id','left')->where('a.attendance_date',date('Y-m-d'));
		if($uri=='HOD'){
		    if($q->num_rows()>0){
		$this->db->where_in('a.employee_id',$team, false);
		}
		}else if(!empty($uri) && $uri!=='HOD'){
		  $this->db->where('a.employee_id',$uri);  
		}
		$this->db->order_by('a.id','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
	    if($row->employee_name==''){
	        
        $qy = $this->db->select('a.first_name, a.last_name, d.department')->from('system_users a')->join('departments d','a.department_id=d.department_id','left')->where('a.user_id',$row->employee_id)->get(); foreach($qy->result() as $row1);
        $empname = strtoupper($row1->first_name." ".$row1->last_name);
        $departmentname = $row1->department;
        if($row->absent_status=='1'){
	            $markabsent="HR HAS MARKED ABSENT";
	        }else if($row->morning_time!=='00:00:00'){
	            $markabsent="";
	        }else{
	           $markabsent = "<a href='".page_url."Master/User_management/mark_absent/".$row->id."'><span class='btn btn-danger btn-xs'>ABSENT</span></a>"; 
	        }
	          
	    }else{
	        if($row->absent_status=='1'){
	            $markabsent="HR HAS MARKED ABSENT";
	        }else{
	           $markabsent = "<a href='".page_url."Master/User_management/mark_absent/".$row->id."'><span class='btn btn-danger btn-xs'>ABSENT</span></a>"; 
	        }
	        	
	        $empname = $row->employee_name;
	        $departmentname="Factory";
	    }
		if($row->morning_selfie!==''){
			$morningselfie = "<img src='".selfiepath.$row->morning_selfie."' width='100px'>";
		}else{
			$morningselfie="";
		}
		if($row->evening_selfie!==''){
			$eveningselfie = "<img src='".selfiepath.$row->evening_selfie."' width='100px'>";
		}else{
			$eveningselfie="";
		}
			
		if($row->absent_status=='1'){
		   $evntime="";
		   $morningtime = "";
		   
		}else{
		   $evntime="";
		   if($row->morning_time=='00:00:00'){
		      $morningtime=""; 
		   }else{
		      $morningtime = date('h:i a',strtotime($row->morning_time)); 
		   }
		   
		    if($row->evening_time!=='00:00:00'){
		        $evntime=date('h:i A',strtotime($row->evening_time));
		        
		    }
			    
			}
			
			$remarks="";
			    $markattendance="";
			    
			    $markattendance  ="<a href='".page_url."Master/User_management/hr_mark_attendance/".$row->id."'><span class='btn btn-success'>Update Attendance </span></a><br><br>";
			   
			        if($row->remarks){
			        $remarks= $row->remarks."<br><br> <strong>UPDATED BY </strong>".$row->hrfname." ".$row->hrlname;
			        }
			    
			  
		
			
			if($row->evening_time!=='00:00:00'){
			  $time1 = $row->morning_time;
            $time2 =$row->evening_time;  
            
            $diff = abs(strtotime($time1) - strtotime($time2));

            $tmins = $diff/60;
            $hours = floor($tmins/60);
            $mins = $tmins%60;
            $difference= "<b>$hours</b> hours and <b>$mins</b> minutes</b>";
			}else{
			    $difference="";
			}
			
		if($row->absent_status=='1'){
		    $attendace_status="Absent";
		}else if($row->onleave=='1'){
		    $attendace_status="Leave";
		}else{
		    $attendace_status="Present";
		}
		
	
			    //$markleave = "<a href='".page_url."Master/User_management/hr_mark_attendance/".$row->id."'><span class='btn btn-success btn-xs'>SHORT LEAVE</span></a>";	
			$attendance_date[] = array('sr_no'=>$i,
			'employee_name'=>$empname,
			'attendance_date'=>date('d-m-Y',strtotime($row->attendance_date)),
			'morning_time'=>$morningtime,
			'department'=>$departmentname,
			'morning_selfie'=>$morningselfie,
			'evening_selfie'=>$eveningselfie,
			'evening_time'=>$evntime,
			'daystat'=>$difference,
			'attendace_status'=>$attendace_status,
			'markattendance'=>$markattendance." ".$remarks,
			'markpresntabsent'=>$markabsent);
			$i++;
			
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($attendance_date),
			"iTotalDisplayRecords" => count($attendance_date),
			"aaData"=>$attendance_date);
			
		echo json_encode($results);
	}


	public function attendance_report_month()
	{
		$i=1;
		$start_date = $this->uri->segment(4);
		$end_date = $this->uri->segment(5);
		$user_id = $this->uri->segment(6);
		//$user_id =$this->session->userdata['logged_in']['user_id'];			
		
		$attendance_date= array();
		$this->db->select('a.*, c.first_name as hrfname, c.last_name as hrlname')->from('mark_your_attendance a')->join('system_users c','a.hr_id=c.user_id','left')->where('a.attendance_date >=',$start_date)->where('a.attendance_date <=',$end_date);
		
		if($user_id<>'' && $user_id<>'ALL')
		{
			$this->db->where('a.employee_id',$user_id);
		}

		$this->db->order_by('a.id','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
	    if($row->employee_name==''){
	        
        $qy = $this->db->select('a.first_name, a.last_name, d.department')->from('system_users a')->join('departments d','a.department_id=d.department_id','left')->where('a.user_id',$row->employee_id)->get(); foreach($qy->result() as $row1);
        $empname = strtoupper($row1->first_name." ".$row1->last_name);
        $departmentname = $row1->department;
        if($row->absent_status=='1'){
	            $markabsent="HR HAS MARKED ABSENT";
	        }else if($row->morning_time!=='00:00:00'){
	            $markabsent="";
	        }else{
	           $markabsent = "<a href='".page_url."Master/User_management/mark_absent/".$row->id."'><span class='btn btn-danger btn-xs'>ABSENT</span></a>"; 
	        }
	          
	    }else{
	        if($row->absent_status=='1'){
	            $markabsent="HR HAS MARKED ABSENT";
	        }else{
	           $markabsent = "<a href='".page_url."Master/User_management/mark_absent/".$row->id."'><span class='btn btn-danger btn-xs'>ABSENT</span></a>"; 
	        }
	        	
	        $empname = $row->employee_name;
	        $departmentname="Factory";
	    }
		if($row->morning_selfie!==''){
			$morningselfie = "<img src='".selfiepath.$row->morning_selfie."' width='100px'>";
		}else{
			$morningselfie="";
		}
		if($row->evening_selfie!==''){
			$eveningselfie = "<img src='".selfiepath.$row->evening_selfie."' width='100px'>";
		}else{
			$eveningselfie="";
		}
			
		if($row->absent_status=='1'){
		   $evntime="";
		   $morningtime = "";
		   
		}else{
		   $evntime="";
		   if($row->morning_time=='00:00:00'){
		      $morningtime=""; 
		   }else{
		      $morningtime = date('h:i a',strtotime($row->morning_time)); 
		   }
		   
		    if($row->evening_time!=='00:00:00'){
		        $evntime=date('h:i A',strtotime($row->evening_time));
		        
		    }
			    
			}
			
			$remarks="";
			    $markattendance="";
			    
			    $markattendance  ="<a href='".page_url."Master/User_management/hr_mark_attendance/".$row->id."'><span class='btn btn-success'>Update Attendance </span></a><br><br>";
			   
			        if($row->remarks){
			        $remarks= $row->remarks."<br><br> <strong>UPDATED BY </strong>".$row->hrfname." ".$row->hrlname;
			        }
			    
			  
		
			
			if($row->evening_time!=='00:00:00'){
			  $time1 = $row->morning_time;
            $time2 =$row->evening_time;  
            
            $diff = abs(strtotime($time1) - strtotime($time2));

            $tmins = $diff/60;
            $hours = floor($tmins/60);
            $mins = $tmins%60;
            $difference= "<b>$hours</b> hours and <b>$mins</b> minutes</b>";
			}else{
			    $difference="";
			}
			
		if($row->absent_status=='1'){
		    $attendace_status="Absent";
		}else if($row->onleave=='1'){
		    $attendace_status="Leave";
		}else{
		    $attendace_status="Present";
		}
		
	
			    //$markleave = "<a href='".page_url."Master/User_management/hr_mark_attendance/".$row->id."'><span class='btn btn-success btn-xs'>SHORT LEAVE</span></a>";	
			$attendance_date[] = array('sr_no'=>$i,
			'employee_name'=>$empname,
			'attendance_date'=>date('d-m-Y',strtotime($row->attendance_date)),
			'morning_time'=>$morningtime,
			'department'=>$departmentname,
			'morning_selfie'=>$morningselfie,
			'evening_selfie'=>$eveningselfie,
			'evening_time'=>$evntime,
			'daystat'=>$difference,
			'attendace_status'=>$attendace_status,
			'markattendance'=>$markattendance." ".$remarks,
			'markpresntabsent'=>$markabsent);
			$i++;
			
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($attendance_date),
			"iTotalDisplayRecords" => count($attendance_date),
			"aaData"=>$attendance_date);
			
		echo json_encode($results);
	}
	
		public function attendance_dashboard_history(){
			
$url="http://crm.gamavis.com/Mitr_api/attendance_history_report/";
$ch = curl_init();
$post = array('flag'=>'1');
curl_setopt_array($ch, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $post
));
//Ignore SSL certificate verification
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
 $response = curl_exec($ch);
$data['attendacereport'] = json_decode($response,true);
$err = curl_error($ch);

curl_close($ch);
		$this->load->view('master/attendance_dashboard_history',$data);
	}
	
	public function attendance_history_report()
	{
		$i=1;
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$uri = $this->uri->segment(4);
        $q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader',$uri)->get();
		if($q->num_rows()>0)
		{
		foreach($q->result() as $teamdetail);
		 $teammembers = array();
		$query = $this->db->select('team_id, employee_id')->from('presto_team_members')->where('team_id',$teamdetail->team_id)->get();
        $res = $query->result();
         foreach($res as $teaminfo){
            $teammembers[] = $teaminfo->employee_id;
            
        }
	
		//$team = implode(',',$teammembers);
		$team = "'" . implode ( "', '", $teammembers ) . "'";
		
		}else
		{
		    $team='NA';
		}
		
		
		$attendance_date= array();
		$this->db->select('a.*, c.first_name as hrfname, c.last_name as hrlname')->from('mark_your_attendance a')->join('system_users c','a.hr_id=c.user_id','left');
		if($team<>'NA'){
		   
		    if($q->num_rows()>0){
		$this->db->where_in('a.employee_id',$team, false);
		}
		}else if($team=='NA' && $uri==''){
		  
		   
		}else{
		    $this->db->where('a.employee_id',$uri); 
		}
		$this->db->order_by('a.id','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
		    if($row->employee_name==''){
		        
		        $qy = $this->db->select('first_name, last_name')->from('system_users')->where('user_id',$row->employee_id)->get(); foreach($qy->result() as $row1);
		        $empname = strtoupper($row1->first_name." ".$row1->last_name);
		        
		    }else{
		        $empname = $row->employee_name;
		    }
			if($row->morning_selfie!==''){
				$morningselfie = "<img src='".selfiepath.$row->morning_selfie."' width='100px'>";
			}else{
				$morningselfie="";
			}
			if($row->evening_selfie!==''){
				$eveningselfie = "<img src='".selfiepath.$row->evening_selfie."' width='100px'>";
			}else{
				$eveningselfie="";
			}
			
			if($row->absent_status=='1'){
			   $evntime="";
			   $morningtime = "";
			   
			}else{
			   $evntime="";
			   if($row->morning_time=='00:00:00'){
			      $morningtime=""; 
			   }else{
			      $morningtime = date('h:i a',strtotime($row->morning_time)); 
			   }
			   
			    if($row->evening_time!=='00:00:00'){
			        $evntime=date('h:i A',strtotime($row->evening_time));
			        
			    }
			    
			    
			    
			}
			
			$remarks="";
			    $markattendance="";
			    if($row->remarks==''){
			    $markattendance  ="<a href='".page_url."Master/User_management/hr_mark_attendance/".$row->id."'><span class='btn btn-success'>Update Attendance </span></a>";
			    }else{
			        $remarks= $row->remarks."<br><br> <strong>UPDATED BY </strong>".$row->hrfname." ".$row->hrlname;
			    }
			  
		
			
			if($row->evening_time!=='00:00:00' && $row->morning_time!=='00:00:00'){
			  $time1 = $row->morning_time;
            $time2 =$row->evening_time;  
            
            $diff = abs(strtotime($time1) - strtotime($time2));

            $tmins = $diff/60;
            $hours = floor($tmins/60);
            $mins = $tmins%60;
            $difference= "<b>$hours</b> hours and <b>$mins</b> minutes</b>";
			}else{
			    $difference="";
			}
			
		if($row->absent_status=='1'){
		    $attendace_status="Absent";
		}else if($row->onleave=='1'){
		    $attendace_status="Leave";
		}else{
		    $attendace_status="Present";
		}
			
			$attendance_date[] = array('sr_no'=>$i,
			'employee_name'=>$empname,
			'attendance_date'=>date('d-m-Y',strtotime($row->attendance_date)),
			'morning_time'=>$morningtime,
			'morning_selfie'=>$morningselfie,
			'evening_selfie'=>$eveningselfie,
			'evening_time'=>$evntime,
			'daystat'=>$difference,
			'attendace_status'=>$attendace_status,
			'markattendance'=>$markattendance." ".$remarks);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($attendance_date),
			"iTotalDisplayRecords" => count($attendance_date),
			"aaData"=>$attendance_date);
			
		echo json_encode($results);
	}
	
	public function hr_mark_attendance(){
	    $this->load->view('master/hr_mark_attendance');
	}
	
	public function update_attendance(){
	    $user_id =$this->session->userdata['logged_in']['user_id'];	
	    date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$data = array('remarks'=>$this->input->post('remarks'),
		'hr_id'=>$user_id,
		'morning_time'=>$this->input->post('morning_time'),
		'evening_time'=>$this->input->post('endtime'),
		'absent_status'=>'0',
		'updated_time'=>$date);
		$this->db->where('id',$this->uri->segment(4));
		$this->db->update('mark_your_attendance',$data);
	    $this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully updated.</div>');
		redirect(page_url.'Master/User_management/attendance_dashboard');
	}
	
public function update_attendace_of_the_employee(){
    
    date_default_timezone_set("Asia/Calcutta"); 
     $query = $this->db->select('employee_name')->from('prestogroup_employees')->where('user_id','0')->where('system_user','0')->get();
           foreach($query->result() as $row){
        
               
           $q22 = $this->db->select('id')->from('mark_your_attendance')->where('employee_name',$row->employee_name)->where('attendance_date',date('Y-m-d'))->get();
          if($q22->num_rows()>0){
              
          }else{
            $data = array('employee_id'=>'0',
            'employee_name'=>$row->employee_name,
            'attendance_date'=>date('Y-m-d'),
            'added_on'=>date('Y-m-d h:i:s'));
            $this->db->insert('mark_your_attendance',$data);  
          }
          
           }   
    
    
    $q = $this->db->select('user_id')->from('system_users')->where('user_status','1')->where('hide_profile','0')->get();
    
    if($q->num_rows()>0){
       foreach($q->result() as $row){
        $query = $this->db->select('id')->from('mark_your_attendance')->where('employee_id',$row->user_id)->where('attendance_date',date('Y-m-d'))->get();
       if($query->num_rows()>0){
           
       }else{
           
           $qq =$this->db->select('employee_id')->from('leave_application')->where('employee_id',$row->user_id)->where('leave_date',date('Y-m-d'))->get();
           if($qq->num_rows()>0){
               foreach($qq->result() as $leavedata);
               $leave = "1";
               $absent_status="0";
           }else{
               $leave="0";
               $absent_status="1";
           }
           
           $data = array('employee_id'=>$row->user_id,
           'attendance_date'=>date('Y-m-d'),
          'absent_status'=>$absent_status,
          'onleave'=>$leave,
          'added_on'=>date('Y-m-d h:i:s'));
           $this->db->insert('mark_your_attendance',$data);
               
           }
           
           
       }
       
       }
       
    
   $this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully updated.</div>');
	redirect(page_url.'Master/User_management/attendance_dashboard'); 
    
}


function compressImage($source, $destination, $quality) {

            $info = getimagesize($source);

            if ($info['mime'] == 'image/jpeg') 
                $image = imagecreatefromjpeg($source);

            elseif ($info['mime'] == 'image/gif') 
                $image = imagecreatefromgif($source);

            elseif ($info['mime'] == 'image/png') 
                $image = imagecreatefrompng($source);

            imagejpeg($image, $destination, $quality);

        }

public function attendance_filter_report(){
    $data = array('user_id'=>$this->input->post('user_id'),
    'start_date'=>date('Y-m-d',strtotime($this->input->post('start_date'))),
    'end_date'=>date('Y-m-d',strtotime($this->input->post('end_date'))));
    
    $this->load->view('master/attendance_filter_report.php',$data);
}

public function hod_attendance_filter_report(){
    $user_id = $this->input->post('user_id');
    $start_date = date('Y-m-d',strtotime($this->input->post('start_date')));
    $end_date = date('Y-m-d',strtotime($this->input->post('end_date')));
    
    redirect(page_url.'Master/User_management/hod_attendance/'.$user_id.'/'.$start_date.'/'.$end_date);
    // $this->load->view('master/hod_attendance',$data);
}

	public function attendance_filter_report_list()
	{
		$i=1;
		
		$attendance_date= array();
		$userid = $this->uri->segment(4);
		$startdate = $this->uri->segment(5);
		$enddate = $this->uri->segment(6);
		
		$this->db->select('a.*, c.first_name as hrfname, c.last_name as hrlname')->from('mark_your_attendance a')->join('system_users c','a.hr_id=c.user_id','left')->where('a.employee_id',$userid);
		$this->db->where('attendance_date BETWEEN "'.$startdate. '" and "'.$enddate.'"');
		$this->db->order_by('a.id','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
		    if($row->employee_name==''){
		        
		        $qy = $this->db->select('first_name, last_name')->from('system_users')->where('user_id',$row->employee_id)->get(); foreach($qy->result() as $row1);
		        $empname = strtoupper($row1->first_name." ".$row1->last_name);
		        
		    }else{
		        $empname = $row->employee_name;
		    }
			if($row->morning_selfie!==''){
				$morningselfie = "<img src='".selfiepath.$row->morning_selfie."' width='100px'>";
			}else{
				$morningselfie="";
			}
			if($row->evening_selfie!==''){
				$eveningselfie = "<img src='".selfiepath.$row->evening_selfie."' width='100px'>";
			}else{
				$eveningselfie="";
			}
			
			if($row->absent_status=='1'){
			   $evntime="";
			   $morningtime = "";
			   
			}else{
			   $evntime="";
			   if($row->morning_time=='00:00:00'){
			      $morningtime=""; 
			   }else{
			      $morningtime = date('h:i a',strtotime($row->morning_time)); 
			   }
			   
			    if($row->evening_time!=='00:00:00'){
			        $evntime=date('h:i A',strtotime($row->evening_time));
			        
			    }
			    
			    
			    
			}
			
			$remarks="";
			    $markattendance="";
			    if($row->remarks==''){
			    $markattendance  ="<a href='".page_url."Master/User_management/hr_mark_attendance/".$row->id."'><span class='btn btn-success'>Update Attendance </span></a>";
			    }else{
			        $remarks= $row->remarks."<br><br> <strong>UPDATED BY </strong>".$row->hrfname." ".$row->hrlname;
			    }
			  
		
			
			if($row->evening_time!=='00:00:00' && $row->morning_time!=='00:00:00'){
			  $time1 = $row->morning_time;
            $time2 =$row->evening_time;  
            
            $diff = abs(strtotime($time1) - strtotime($time2));

            $tmins = $diff/60;
            $hours = floor($tmins/60);
            $mins = $tmins%60;
            $difference= "<b>$hours</b> hours and <b>$mins</b> minutes</b>";
			}else{
			    $difference="";
			}
			
		if($row->absent_status=='1'){
		    $attendace_status="Absent";
		}else if($row->onleave=='1'){
		    $attendace_status="Leave";
		}else{
		    $attendace_status="Present";
		}
			
			$attendance_date[] = array('sr_no'=>$i,
			'employee_name'=>$empname,
			'attendance_date'=>date('d-m-Y',strtotime($row->attendance_date)),
			'morning_time'=>$morningtime,
			'morning_selfie'=>$morningselfie,
			'evening_selfie'=>$eveningselfie,
			'evening_time'=>$evntime,
			'daystat'=>$difference,
			'attendace_status'=>$attendace_status,
			'markattendance'=>$markattendance." ".$remarks);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($attendance_date),
			"iTotalDisplayRecords" => count($attendance_date),
			"aaData"=>$attendance_date);
			
		echo json_encode($results);
	}
	
	public function mark_absent(){
	    
	    $userid = $this->uri->segment(4);
	    $data = array('absent_status'=>'1');
	    $this->db->where('id',$userid);
	    $this->db->update('mark_your_attendance',$data);
	    $this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully mark as Absent.</div>');
		redirect(page_url.'Master/User_management/attendance_dashboard');
	    
	}

	public function mark_absent_hod(){
	    
	    $userid = $this->uri->segment(4);
	    $data = array('absent_status'=>'1');
	    $this->db->where('id',$userid);
	    $this->db->update('mark_your_attendance',$data);
	    $this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully mark as Absent.</div>');
		redirect(page_url.'Master/User_management/hod_attendance');
	    
	}


	public function leave_attendance_report()
	{
		$i=1;
		$uri = $this->uri->segment(4);
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		
		$q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
		if($q->num_rows()>0)
		{
		foreach($q->result() as $teamdetail);
		 $teammembers = array();
		$query = $this->db->select('team_id, employee_id')->from('presto_team_members')->where('team_id',$teamdetail->team_id)->get();
        $res = $query->result();
         foreach($res as $teaminfo){
            $teammembers[] = $teaminfo->employee_id;
            
        }
	
		//$team = implode(',',$teammembers);
		$team = "'" . implode ( "', '", $teammembers ) . "'";
		
		}else
		{
		    $team='NA';
		}
		
		
		
		$attendance_date= array();
		$this->db->select('a.*, c.first_name as hrfname, c.last_name as hrlname')->from('mark_your_attendance a')->join('system_users c','a.hr_id=c.user_id','left')->where('a.attendance_date',date('Y-m-d'))->where('a.absent_status','1');
		if($uri=='HOD'){
		    if($q->num_rows()>0){
		$this->db->where_in('a.employee_id',$team, false);
		}
		}else if(!empty($uri) && $uri!=='HOD'){
		  $this->db->where('a.employee_id',$uri);  
		}
		$this->db->order_by('a.id','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
	    if($row->employee_name==''){
	        
        $qy = $this->db->select('first_name, last_name')->from('system_users')->where('user_id',$row->employee_id)->get(); foreach($qy->result() as $row1);
        $empname = strtoupper($row1->first_name." ".$row1->last_name);
	        $markabsent="";
	    }else{
	        if($row->absent_status=='1'){
	            $markabsent="HR HAS MARKED ABSENT";
	        }else{
	           $markabsent = "<a href='".page_url."Master/User_management/mark_absent/".$row->id."'><span class='btn btn-danger btn-xs'>ABSENT</span></a>"; 
	        }
	        	
	        $empname = $row->employee_name;
	    }
	
		
			$attendance_date[] = array('sr_no'=>$i,
			'employee_name'=>$empname,
			'attendance_date'=>date('d-m-Y',strtotime($row->attendance_date)));
			$i++;
			
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($attendance_date),
			"iTotalDisplayRecords" => count($attendance_date),
			"aaData"=>$attendance_date);
			
		echo json_encode($results);
	}

public function changepassword(){
    $data = array('password'=>$this->input->post('password'));
    $this->db->where('user_id',$this->uri->segment(3));
    $this->db->update('system_users',$data);
     $this->session->set_flashdata('message','<div class="alert alert-success">Thank you, password successfully updated.</div>');
		redirect(page_url.'Master/User_management');
}


function getpotype($prno)
			{

			$rest=$this->db->select('type')->from('purchase_request')->where('prno',$prno)->get();
			if($rest->num_rows()>0)
			{
			foreach($rest->result() as $restt);

			return $restt->type;
			}else{

			return "NA";
			}

			}
			
	public function generateemployeecode(){
	  //$digits = 5;
//$randomnumber =  rand(pow(10, $digits-1), pow(10, $digits)-1); 
$seed = str_split('abcdefghijklmnopqrstuvwxyz'
                     .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                     .'0123456789'); // and any other characters
    shuffle($seed); // probably optional since array_is randomized; this may be redundant
    $rand = '';
    foreach (array_rand($seed, 5) as $k) $rand .= $seed[$k];
 
    $randomnumber = strtoupper($rand);
    
$query = $this->db->select('employeecode')->from('system_users')->where('employeecode',$randomnumber)->get();
if($query->num_rows()>0){
    $this->session->set_flashdata('message','<div class="alert alert-info">Sorry! this number already exist please try again.</div>');
	redirect(page_url.'Master/User_management'); 
}else{
    $data = array('employeecode'=>$randomnumber);
    $this->db->where('user_id',$this->uri->segment(4));
    $this->db->update('system_users',$data);
    $this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully updated.</div>');
	redirect(page_url.'Master/User_management'); 
    
}
	    
	}
	
	function start_train()
	{
		$userid=$this->uri->segment(4);
		$sval=$this->uri->segment(5);
		$field_name='user_id';
		$table='system_users';
		if($sval=='1')
		{
			$st_train = 0;
			}else
			{
				$st_train = 1;
				}
		$data = array('start_training'=>$st_train);
		$res = $this->master->update_records($table,$data,$userid,$field_name);
		$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Training successfully updated.</div>');
		redirect(page_url.'Master/User_management/');
		
	}


	public function user_attendance_history(){
	

		$this->load->view('master/user_attendance_history');
	}
	
	public function user_attendance_history_report()
	{
		$i=1;
		$uri = $this->uri->segment(4);
		$user_id =$this->session->userdata['logged_in']['user_id'];			
		$attendance_date= array();
		$query = $this->db->select('a.*, c.first_name as hrfname, c.last_name as hrlname')
						  ->from('mark_your_attendance a')
						  ->join('system_users c','a.hr_id=c.user_id','left')
						  ->where('a.employee_id',$user_id)
						  ->order_by('a.id','desc')
		 				  ->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
	    		if($row->employee_name==''){
	        
        $qy = $this->db->select('a.first_name, a.last_name, d.department')->from('system_users a')->join('departments d','a.department_id=d.department_id','left')->where('a.user_id',$row->employee_id)->get(); 

        foreach($qy->result() as $row1);
        $empname = strtoupper($row1->first_name." ".$row1->last_name);
        $departmentname = $row1->department;
        if($row->absent_status=='1'){
	            $markabsent="HR HAS MARKED ABSENT";
	        }else if($row->morning_time!=='00:00:00'){
	            $markabsent="";
	        }else{
	           $markabsent = "<a href='".page_url."Master/User_management/mark_absent/".$row->id."'><span class='btn btn-danger btn-xs'>ABSENT</span></a>"; 
	        }
	          
	    }else{
	        if($row->absent_status=='1'){
	            $markabsent="HR HAS MARKED ABSENT";
	        }else{
	           $markabsent = "<a href='".page_url."Master/User_management/mark_absent/".$row->id."'><span class='btn btn-danger btn-xs'>ABSENT</span></a>"; 
	        }
	        	
	        $empname = $row->employee_name;
	        $departmentname="Factory";
	    }
		if($row->morning_selfie!==''){
			$morningselfie = "<img src='".selfiepath.$row->morning_selfie."' width='100px'>";
		}else{
			$morningselfie="";
		}
		if($row->evening_selfie!==''){
			$eveningselfie = "<img src='".selfiepath.$row->evening_selfie."' width='100px'>";
		}else{
			$eveningselfie="";
		}
			
		if($row->absent_status=='1'){
		   $evntime="";
		   $morningtime = "";
		   
		}else{
		   $evntime="";
		   if($row->morning_time=='00:00:00'){
		      $morningtime=""; 
		   }else{
		      $morningtime = date('h:i a',strtotime($row->morning_time)); 
		   }
		   
		    if($row->evening_time!=='00:00:00'){
		        $evntime=date('h:i A',strtotime($row->evening_time));
		        
		    }
			    
			}
			
			$remarks="";
			    $markattendance="";
			    
			    $markattendance  ="<a href='".page_url."Master/User_management/hr_mark_attendance/".$row->id."'><span class='btn btn-success'>Update Attendance </span></a><br><br>";
			   
			        if($row->remarks){
			        $remarks= $row->remarks."<br><br> <strong>UPDATED BY </strong>".$row->hrfname." ".$row->hrlname;
			        }
			    
			  
		
			
			if($row->evening_time!=='00:00:00'){
			  $time1 = $row->morning_time;
            $time2 =$row->evening_time;  
            
            $diff = abs(strtotime($time1) - strtotime($time2));

            $tmins = $diff/60;
            $hours = floor($tmins/60);
            $mins = $tmins%60;
            $difference= "<b>$hours</b> hours and <b>$mins</b> minutes</b>";
			}else{
			    $difference="";
			}
			
		if($row->absent_status=='1'){
		    $attendace_status="Absent";
		}else if($row->onleave=='1'){
		    $attendace_status="Leave";
		}else{
		    $attendace_status="Present";
		}
		
	
			    //$markleave = "<a href='".page_url."Master/User_management/hr_mark_attendance/".$row->id."'><span class='btn btn-success btn-xs'>SHORT LEAVE</span></a>";	
			$attendance_date[] = array('sr_no'=>$i,
			'employee_name'=>$empname,
			'attendance_date'=>date('d-m-Y',strtotime($row->attendance_date)),
			'morning_time'=>$morningtime,
			'department'=>$departmentname,
			'morning_selfie'=>$morningselfie,
			'evening_selfie'=>$eveningselfie,
			'evening_time'=>$evntime,
			'daystat'=>$difference,
			'attendace_status'=>$attendace_status,
			'markattendance'=>$markattendance." ".$remarks,
			'markpresntabsent'=>$markabsent);
			$i++;
			
		}
	}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($attendance_date),
			"iTotalDisplayRecords" => count($attendance_date),
			"aaData"=>$attendance_date);
			
		echo json_encode($results);
	}

	public function hod_attendance(){
		$this->load->view('master/hod_attendance');
	}
	
	public function hod_attendance_report()
	{
		$i=1;
		$user = $this->uri->segment(4);
		$start_date = $this->uri->segment(5);
		$end_date = $this->uri->segment(6);

		$user_id =$this->session->userdata['logged_in']['user_id'];	

		$attendance_date= array();
		$this->db->select('a.*, c.first_name as hrfname, c.last_name as hrlname')
				 ->from('mark_your_attendance a')
				 ->join('system_users c','a.employee_id=c.user_id','left');
				
				if($user != '') {
					$this->db->where('a.employee_id',$user);
				}

				if($start_date != '' && $end_date != '') {
					$this->db->where('a.attendance_date >=',$start_date);
					$this->db->where('a.attendance_date <=',$end_date);
				}

		
		$this->db->order_by('a.id','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){

	        if($row->absent_status=='1'){
	            $markabsent="HR HAS MARKED ABSENT";
	        }else{
	           $markabsent = "<a href='".page_url."Master/User_management/mark_absent_hod/".$row->id."'><span class='btn btn-danger btn-xs'>ABSENT</span></a>"; 
	        }
	        	
	        $empname = $row->employee_name;
	        $departmentname="Factory";
		if($row->morning_selfie!==''){
			$morningselfie = "<img src='".selfiepath.$row->morning_selfie."' width='100px'>";
		}else{
			$morningselfie="";
		}
		if($row->evening_selfie!==''){
			$eveningselfie = "<img src='".selfiepath.$row->evening_selfie."' width='100px'>";
		}else{
			$eveningselfie="";
		}
			
		if($row->absent_status=='1'){
		   $evntime="";
		   $morningtime = "";
		   
		}else{
		   $evntime="";
		   if($row->morning_time=='00:00:00'){
		      $morningtime=""; 
		   }else{
		      $morningtime = date('h:i a',strtotime($row->morning_time)); 
		   }
		   
		    if($row->evening_time!=='00:00:00'){
		        $evntime=date('h:i A',strtotime($row->evening_time));
		        
		    }
			    
			}
			
			$remarks="";
			    $markattendance="";
			    
			    $markattendance  ="<a href='".page_url."Master/User_management/hr_mark_attendance/".$row->id."'><span class='btn btn-success'>Update Attendance </span></a><br><br>";
			   
			        if($row->remarks){
			        $remarks= $row->remarks."<br><br> <strong>UPDATED BY </strong>".$row->hrfname." ".$row->hrlname;
			        }
			    
			  
		
			
			if($row->evening_time!=='00:00:00'){
			  $time1 = $row->morning_time;
            $time2 =$row->evening_time;  
            
            $diff = abs(strtotime($time1) - strtotime($time2));

            $tmins = $diff/60;
            $hours = floor($tmins/60);
            $mins = $tmins%60;
            $difference= "<b>$hours</b> hours and <b>$mins</b> minutes</b>";
			}else{
			    $difference="";
			}
			
		if($row->absent_status=='1'){
		    $attendace_status="Absent";
		}else if($row->onleave=='1'){
		    $attendace_status="Leave";
		}else{
		    $attendace_status="Present";
		}
		
	
			    //$markleave = "<a href='".page_url."Master/User_management/hr_mark_attendance/".$row->id."'><span class='btn btn-success btn-xs'>SHORT LEAVE</span></a>";	
			$attendance_date[] = array('sr_no'=>$i,
			'employee_name'=>$row->hrfname.' '.$row->hrlname,
			'attendance_date'=>date('d-m-Y',strtotime($row->attendance_date)),
			'morning_time'=>$morningtime,
			'department'=>$departmentname,
			'morning_selfie'=>$morningselfie,
			'evening_selfie'=>$eveningselfie,
			'evening_time'=>$evntime,
			'daystat'=>$difference,
			'attendace_status'=>$attendace_status,
			'markattendance'=>$markattendance." ".$remarks,
			'markpresntabsent'=>$markabsent);
			$i++;
			
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($attendance_date),
			"iTotalDisplayRecords" => count($attendance_date),
			"aaData"=>$attendance_date);
			
		echo json_encode($results);
	}


public function add_vendor_via_ajax()
  {
    
    date_default_timezone_set("Asia/Kolkata");
    $date =  date('Y-m-d H:i:s'); 
    $table = "vendors";
    if($this->input->post('del_type')=='Other'){
       $deliverytime = $this->input->post('otherdtype'); 
    }else{
       $deliverytime= $this->input->post('del_type');
    }
          
    $data = array('name'=>$this->input->post('vendor_name'),
    'code'=>$this->input->post('vendor_code'),
    'phone'=>$this->input->post('contact'),
    'company'=>$this->input->post('company'),
    'email'=>$this->input->post('email'),
    'address'=>$this->input->post('address'),
    'contactperson'=>$this->input->post('contactperson'),
    'pan'=>$this->input->post('pan'),
    'gst'=>$this->input->post('gst'),
    'addedBy'=>$_SESSION['logged_in']['user_id'],
    'status'=>'1',
    'addedOn'=>$date,
  );
        
    $result  = $this->master->insert_record($table,$data);  

     $lid=$this->db->insert_id();
      if($this->db->affected_rows()>0)
      {
        echo $lid;
      }else
      {
        echo false;
      } 

  }

  function sendemail(){
  	ini_set('memory_limit', '6144M');
  	$file = UPLOADPATH."reference/Shubham-Pack-Catalog.pdf";
  	if(!empty($this->session->userdata['logged_in']['smtpemailid'])){
			$smtpemail  = $this->session->userdata['logged_in']['smtpemailid'];
		}else{
			$smtpemail = '';
		}
		if(!empty($this->session->userdata['logged_in']['smtppassword'])){
			$smtppassword  = $this->session->userdata['logged_in']['smtppassword'];
		}else{
			$smtppassword='';
		}

		if($this->input->post('Exhibition')==16){
			$exihi = $this->input->post('Exhibition');
		$this->sendemailtopropacasia2025($smtpemail, $smtppassword, $exihi);
		}else{

			exit;

	if($this->input->post('country')==101){
		$this->sendemailtoindiancustomer();
	}else{
		$user_id =$this->session->userdata['logged_in']['user_id'];

  	$q = $this->db->select('title, first_name, last_name, contact_number')->from('system_users')->where('user_id',$user_id)->get();
  	foreach($q->result() as $userinfo);
  	$contact_number = $userinfo->contact_number;
  	$usersignature = ucwords(strtolower($userinfo->title." ".$userinfo->first_name." ".$userinfo->last_name))."<br>"."Marketing<br>"."+91-".$contact_number."<br>".$smtpemail;
	$exhibition = $this->input->post('Exhibition');
	$subject = $this->input->post('subject');
	$message = $this->input->post('email_template')."<br>".$usersignature;
	$q = $this->db->select('b.email')->from('leads a')->join('customer_detail b','a.company_name=b.id','left')->where('a.exhibition',$exhibition);
	if($this->input->post('country')<>'All'){
		$this->db->where('a.country',$this->input->post('country'));
	}
	if($user_id==139 || $user_id==61 || $user_id==161){

	}else{
		$this->db->where('a.added_by',$user_id);
	}
	$this->db->get();
	if($q->num_rows()>0){

		$batch_size= 10;
	    $batch_counter = 0;
        foreach ($q->result() as $leaddata) {
            $this->email->clear(TRUE);
	        $this->email->from($smtpemail, 'Shubham Flexible Packaging Machines Pvt. Ltd.');
            $this->email->to($leaddata->email);
            $this->email->cc('shubham@shubhampack.com,'.$smtpemail);
            $this->email->bcc('mangleshup@gmail.com');
            $this->email->subject($subject);
            $this->email->message($message);
            $this->email->attach($file);
            $customeremail = $leaddata->email;
            if (!$this->email->send()) {
                log_message('error', 'Email to ' .$customeremail. ' failed: ' . $this->email->print_debugger());
            } else {
                log_message('info', 'Email sent to ' .$customeremail);
            }
            $batch_counter++;
            if ($batch_counter % $batch_size == 0) {
                sleep(1); // Sleep for a second to avoid overloading the server
            }
        }
        $this->emailmarkassent($user_id,$exhibition);

	}
	}
}
  	
	
	//echo $this->email->print_debugger(); exit;


  }

function emailmarkassent($user_id, $exhibition){
	$data = array('exhibition_id'=>$exhibition,
		'added_on'=>date('Y-m-d H:i:s'),
		'added_by'=>$user_id);
		$q = $this->db->select('id')->from('exhibition_intro_email_delivery')->where('exhibition_id',$exhibition)->get();
		if($q->num_rows()>0){

		}else{
		$this->db->insert('exhibition_intro_email_delivery',$data);
		}
}

public function sendintroemail(){
	$this->load->view('master/intro_email');
}

public function sendintroemailtocustomers(){
	$this->load->view('master/send-email-to-exhibition-customers');
}

public function fetchselectedemailbodysubject()
	{
	
	$id= $this->input->post('Exhibition');

	$q = $this->db->select('email_subject')->from('departmentwise_email_template')->where('exhibition_id',$id)->get();
	foreach($q->result() as $row);
	//echo "<pre>"; print_r($row); exit;
	echo $row->email_subject; exit;
		
	}

public function fetchselectedemailbody()
	{
	
	$id= $this->input->post('Exhibition');

	$q = $this->db->select('email_template')->from('departmentwise_email_template')->where('exhibition_id',$id)->get();
	foreach($q->result() as $row);
	echo $row->email_template; exit;
		
	}

	public function sendemailtopropacasia2025($smtpemail, $smtppassword, $exhibitionid) {
		exit;
    ini_set('memory_limit', '6144M');
    $file = UPLOADPATH . "reference/Shubham-Pack-Catalog.pdf";

    // Email configuration
    $config = array(
        'protocol' => 'smtp',
        'smtp_host' => 'smtp.gmail.com',
        'smtp_port' => 465,
        'smtp_user' => $smtpemail,
        'smtp_pass' => $smtppassword,
        'mailtype' => 'html',
        'smtp_timeout' => 100,
        'charset' => 'utf-8',
        'newline' => "\r\n",
        'smtp_crypto' => 'ssl',
        'starttls' => FALSE
    );

    $this->email->initialize($config);
    $this->email->set_mailtype("html");
    $this->email->set_newline("\r\n");

    // Get user signature
    $user_id = $this->session->userdata['logged_in']['user_id'];
    $q = $this->db->select('title, first_name, last_name, contact_number')->from('system_users')->where('user_id', $user_id)->get();

    if ($q->num_rows() > 0) {
        $userinfo = $q->row();
        $contact_number = $userinfo->contact_number;
        $usersignature = ucwords(strtolower($userinfo->title . " " . $userinfo->first_name . " " . $userinfo->last_name)) . "<br>Marketing<br>+91-" . $contact_number . "<br>" . $smtpemail;
    } else {
        log_message('error', 'User not found for user_id: ' . $user_id);
        return;
    }


    // Email subject and body
    $subject = "Thank You for Visiting SHUBHAM PACK INDIA (High Speed FFS Machine expert) at PROPAK ASIA 2025";
    $qq = $this->db->select('email_subject, email_template')->from('departmentwise_email_template')->where('exhibition_id',$exhibitionid)->get();
    foreach($qq->result() as $row);
    $message = $row->email_template;
    //$encoded_subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    $encoded_subject = $subject;

    // Static email list (deduplicated)
    $customer_emails = array_unique([
		'SoonThon.thongIM@kerry.com',
        'verasak261155@gmail.com'
    ]);

    // Batch control
    $batch_size = 10;
    $batch_counter = 0;

    foreach ($customer_emails as $email) {
        $this->email->clear(TRUE);
        $this->email->from($smtpemail, 'Shubham Flexible Packaging Machines Pvt. Ltd.');
        $this->email->to($email);
        $this->email->cc(['projects2@shubhampack.com', 'shubham@shubhampack.com','virendra@shubhampack.com']);
        $this->email->bcc(['mangleshup@gmail.com']);
        $this->email->subject($encoded_subject);
        $this->email->message($message);

        if (file_exists($file)) {
            $this->email->attach($file);
        } else {
            log_message('error', 'Attachment file not found: ' . $file);
        }

        if (!$this->email->send()) {
            log_message('error', 'Email to ' . $email . ' failed: ' . $this->email->print_debugger());
        } else {
            log_message('info', 'Email sent to ' . $email);
        }
        // echo $this->email->print_debugger();
        //exit;

        $batch_counter++;
        if ($batch_counter % $batch_size == 0) {
            sleep(1); // Avoid server overload
        }
    }

    echo $this->email->print_debugger();
    exit;
}


 function sendemailtoindiancustomer() {
    ini_set('memory_limit', '6144M');
    $file = UPLOADPATH . "reference/Shubham-Pack-Catalog.pdf";
    $smtppassword = 'Shubham@11'; // Use environment variables ideally
    $smtpemail = 'sales@shubhampack.com'; // Use environment variables ideally

    // Email configuration
    $config = array(
        'protocol' => 'smtp',
        'smtp_host' => 'smtp.gmail.com',   // Updated to smtp.gmail.com
        'smtp_port' => 465,  // Use SSL with port 465
        'smtp_user' => $smtpemail,
        'smtp_pass' => $smtppassword,
        'mailtype' => 'html',
        'smtp_timeout' => 100,
        'charset' => 'utf-8',  // Ensure utf-8 encoding
        'newline' => "\r\n",
        'smtp_crypto' => 'ssl',  // Use SSL encryption
        'starttls' => FALSE  // Set to false when using SSL
    );

    // Initialize the email library with the updated config
    $this->email->initialize($config);
    $this->email->set_mailtype("html");
    $this->email->set_newline("\r\n");

    // Get user information
    $user_id = $this->session->userdata['logged_in']['user_id'];
    $q = $this->db->select('title, first_name, last_name, contact_number')->from('system_users')->where('user_id', $user_id)->get();

    if ($q->num_rows() > 0) {
        $userinfo = $q->row();
        $contact_number = $userinfo->contact_number;
        $usersignature = ucwords(strtolower($userinfo->title . " " . $userinfo->first_name . " " . $userinfo->last_name)) . "<br>" . "Marketing<br>" . "+91-" . $contact_number . "<br>" . $smtpemail;
    } else {
        log_message('error', 'User not found for user_id: ' . $user_id);
        $usersignature='';
        return;
    }

    // Alternatively, you can use a static signature
    // $usersignature = 'Mr. Manoj Dubey<br>
    // Marketing<br>
    // +91-8130192039<br>
    // sales@shubhampack.com<br>';

    // Get email content
    $subject = $this->input->post('subject');
    $message = $this->input->post('email_template') . "<br>" . $usersignature;

    // Base64 encode the subject to handle special characters
    $encoded_subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

    // Fetch customer emails
    $q = $this->db->select('id, email')->from('customer_detail')
        ->where('country', 101)
        ->where('exhibitiion_email_sent', 0)
        ->where('banglore_exhibition', 1)
        ->limit(3)
        ->get();

//echo "<pre>"; print_r($q->result()); exit;
    if ($q->num_rows() > 0) {
        $batch_size = 10;
        $batch_counter = 0;

        foreach ($q->result() as $leaddata) {
            $this->email->clear(TRUE);
            $this->email->from($smtpemail, 'Shubham Flexible Packaging Machines Pvt. Ltd.');
            $this->email->to($leaddata->email);
            $this->email->cc(array('sales@shubhampack.com', 'shubham@shubhampack.com'));  // Use array for CC addresses
            $this->email->bcc(array('mangleshup@gmail.com'));
            $this->email->set_header('Subject', $encoded_subject);
            // Set email body message
            $this->email->message($message);

            // Attach file if it exists
            if (file_exists($file)) {
                $this->email->attach($file);
            } else {
                log_message('error', 'Attachment file not found: ' . $file);
            }

            // Attempt to send the email
            if (!$this->email->send()) {
                log_message('error', 'Email to ' . $leaddata->email . ' failed: ' . $this->email->print_debugger());
            } else {
                log_message('info', 'Email sent to ' . $leaddata->email);
                $this->emailmarkassenttocustomers($leaddata->id);
            }

            // Control batch size to avoid server overload
            $batch_counter++;
            if ($batch_counter % $batch_size == 0) {
                sleep(1); // Sleep for a second to avoid overloading the server
            }
        }
    } else {
        log_message('info', 'No customers found matching the criteria.');
    }

    // Print email debug information
    echo $this->email->print_debugger();
    exit;
}


function emailmarkassenttocustomers($customerid){

$data = array('exhibitiion_email_sent'=>1);
$this->db->where('id',$customerid);
$this->db->update('customer_detail',$data);

  }

  public function machine_master(){
		
			$this->load->view('master/machine_master');
		
	}


	public function machine_master_list()
	{
		
		$data = array();
		$this->db->select('*')->from('machine_master');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row){

			 $this->db->select('id')->from('bom_spare_parts')->where('machine_id', $row->id);
        $bom_query = $this->db->get();
        $bom_data = $bom_query->result();
												
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Master/User_management/machine_master_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Master/User_management/machine_master_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Master/User_management/edit_machine_master/".$row->id."'><i class='fa fa-pencil'></i></a>";
			
			$bom = "<a href='".page_url."Master/User_management/create_machine_bom/".$row->id."'><span class='btn btn-dark btn-xs'>Create Bom</span></a>";

				// $view_bom = "<a href='".page_url."Master/User_management/view_spare_parts_bom/".$row->id."'><span class='btn btn-info btn-xs'>View</span></a>";

				  if (!empty($bom_data)) {
            $view_bom = "<a href='" . page_url . "Master/User_management/view_spare_parts_bom/" . $row->id . "'><span class='btn btn-info btn-xs'>View</span></a>";
        } else {
            $view_bom = "<span class='btn btn-warning btn-xs'>No Data</span>";
        }
			
			$data[] = array('sr_no'=>$i,
			'name'=>$row->name,
			'bom'=>$bom,
			'view_bom'=>$view_bom,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($data),
			"iTotalDisplayRecords" => count($data),
			"aaData"=>$data);
			
		echo json_encode($results);
	}


	public function create_machine_bom(){

		$this->load->view('master/create_machine_bom');

	}

		public function view_spare_parts_bom(){

		$this->load->view('master/view_spare_parts_bom');

	}

	public function spare_parts_bom_list()
	{
		$id = $this->uri->segment(4);
		$data = array();
		$this->db->select('a.id, a.spare_id, a.machine_id, a.quantity, b.code, b.description, c.name')->from('bom_spare_parts a')->join('spare_parts b', 'a.spare_id=b.id')->join('machine_master c', 'a.machine_id=c.id');
		$this->db->where('a.machine_id', $id);
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row){
												
				$delete = "<a href='".page_url."Master/User_management/delete_spare_parts_bom/".$row->id."/".$this->uri->segment(4)."'><span class='btn btn-danger btn-xs' onclick='return confirm(\"Are you sure you want to delete?\");'><i class='fa fa-trash'></i></span></a>";
			
			$data[] = array('sr_no'=>$i,
			'spare_id'=>$row->description.'-'.$row->code,
			'machine_id'=>$row->name,
			'quantity'=>$row->quantity,
			'delete'=>$delete);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($data),
			"iTotalDisplayRecords" => count($data),
			"aaData"=>$data);
			
		echo json_encode($results);
	}


	public function delete_spare_parts_bom($id)
{
   $id = $this->uri->segment(4);
   $flag = $this->uri->segment(5);

    $this->db->where('id', $id);
    $this->db->delete('bom_spare_parts');

  
    if ($this->db->affected_rows() > 0) {
        $this->session->set_flashdata('message', '<div class="alert alert-success">Record deleted successfully.</div>');
		 
    } else {
        $this->session->set_flashdata('message', '<div class="alert alert-danger">An error occurred while deleting the record.</div>');
		   
    }

	  redirect(page_url.'Master/User_management/view_spare_parts_bom/'.$flag); 
 
}

	public function add_machine_bom() {
    $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
    $this->form_validation->set_rules('spare_id[]', 'Back Office Code', 'required|trim');
    $this->form_validation->set_rules('revision[]', 'Revision', 'required|trim');
    $this->form_validation->set_rules('quantity[]', 'Quantity', 'required|trim|greater_than[0]');

    $user_id = $this->session->userdata['logged_in']['user_id'];        
    if ($this->form_validation->run() == FALSE) {
        $this->load->view('master/create_machine_bom');
    } else {
        date_default_timezone_set("Asia/Kolkata");
        $date = date('Y-m-d H:i:s');
        $machine_id = $this->uri->segment(4);
        $table = "bom_spare_parts";

    
        $spare_ids = $this->input->post('spare_id');
        $quantities = $this->input->post('quantity');
        
        foreach ($spare_ids as $index => $spare_id) {
            $data = array(
                'spare_id' => $spare_id,
                'quantity' => $quantities[$index],
                'machine_id' => $machine_id,
                'added_on' => $date,
                'added_by' => $user_id
            );
      
            $this->master->insert_record($table, $data);
        } 

   
        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
            redirect(page_url.'Master/User_management/machine_master');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">Sorry, a technical error occurred.</div>');
            redirect(page_url.'Master/User_management/create_machine_bom');
        }
    }
}



	public function machine_master_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(4);
		$sval =  $this->uri->segment(5);
		$field_name = "id";
		$table = "machine_master";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$this->db->where('id',$this->uri->segment(4));
			$res = $this->db->update($table,$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success"> Status successfully updated.</div>');
			redirect(page_url.'Master/User_management/machine_master');
		}



		public function add_machine_master()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('name', 'Name', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/machine_master');
		}
		else
		{
		
			date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s');
		$table = "machine_master";
			$data = array(
			'name'=>$this->input->post('name'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Master/User_management/machine_master');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div>');
			redirect(page_url.'Master/User_management/machine_master');
		}
		
	}
		
	}


	public function edit_machine_master(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('name', 'Name', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/edit_machine_master');
		}
		else
		{
			$id=$this->uri->segment(4);
			$table="machine_master";
			
	
	   $data = array(
		'name' =>$this->input->post('name'),
		'status' =>$this->input->post('status'),
		'added_on'=>date("Y-m-d h:i:s"));
		
		$this->db->where('id',$id);
		$this->db->update($table,$data);
    $this->session->set_flashdata('success','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
	  redirect(page_url.'Master/User_management/machine_master');
		}
	}



	  public function spare_parts(){
		
			$this->load->view('master/spare_parts');
		
	}


	public function spare_parts_list()
	{
		
		$data = array();
		$this->db->select('*')->from('spare_parts');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Master/User_management/spare_parts_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Master/User_management/spare_parts_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Master/User_management/edit_spare_parts/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			
			$data[] = array('sr_no'=>$i,
			'code'=>$row->code,
			'revision'=>$row->revision,
			'description'=>$row->description,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($data),
			"iTotalDisplayRecords" => count($data),
			"aaData"=>$data);
			
		echo json_encode($results);
	}


	public function add_spare_parts()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('code', 'Name', 'required|trim');
		$this->form_validation->set_rules('revision', 'Name', 'required|trim');
		$this->form_validation->set_rules('description', 'Name', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/spare_parts');
		}
		else
		{
		
			date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s');
		$table = "spare_parts";
			$data = array(
			'code'=>$this->input->post('code'),
			'revision'=>$this->input->post('revision'),
			'description'=>$this->input->post('description'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Master/User_management/spare_parts');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div>');
			redirect(page_url.'Master/User_management/spare_parts');
		}
		
	}
		
	}

	public function edit_spare_parts(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('code', 'Name', 'required|trim');
		$this->form_validation->set_rules('revision', 'Name', 'required|trim');
		$this->form_validation->set_rules('description', 'Name', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/edit_spare_parts');
		}
		else
		{
			$id=$this->uri->segment(4);
			$table="spare_parts";
			
	
	   $data = array(
	'code'=>$this->input->post('code'),
			'revision'=>$this->input->post('revision'),
			'description'=>$this->input->post('description'),
			'status'=>$this->input->post('status'),
		'added_on'=>date("Y-m-d h:i:s"));
		
		$this->db->where('id',$id);
		$this->db->update($table,$data);
    $this->session->set_flashdata('success','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
	  redirect(page_url.'Master/User_management/spare_parts');
		}
	}


	public function spare_parts_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(4);
		$sval =  $this->uri->segment(5);
		$field_name = "id";
		$table = "spare_parts";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$this->db->where('id',$this->uri->segment(4));
			$res = $this->db->update($table,$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success"> Status successfully updated.</div>');
			redirect(page_url.'Master/User_management/spare_parts');
		}



		public function get_spare_parts()
	{
  $backCodeId = $this->input->post('backcode_id');

   
    $this->db->select('revision');
    $this->db->from('spare_parts');
    $this->db->where('id', $backCodeId);
    $query = $this->db->get();

    if ($query->num_rows() > 0) {
       
        $row = $query->row();
        echo json_encode([
            'status' => 'success', 
            'revision' => $row->revision
        ]);
    } else {
       
        echo json_encode(['status' => 'error']);
    }
		
		}


		public function shortage_spare_parts(){

		$this->load->view('master/shortage_spare_parts');

	}


	public function add_shortage_spare_parts() {
    $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
    $this->form_validation->set_rules('shortage_spare_id[]', 'Back Office Code', 'required|trim');
    $this->form_validation->set_rules('quantity[]', 'Quantity', 'required|trim|greater_than[0]');

    $user_id = $this->session->userdata['logged_in']['user_id'];        
    if ($this->form_validation->run() == FALSE) {
        $this->load->view('master/shortage_spare_parts');
    } else {
        date_default_timezone_set("Asia/Kolkata");
        $date = date('Y-m-d H:i:s');
      
        $table = "shortage_spare_parts";

		$df__id = $this->uri->segment(4);
    
       // Get the posted data
        $spare_ids = $this->input->post('shortage_spare_id'); // Array of spare IDs
        $quantities = $this->input->post('quantity'); // Array of quantities

        // Loop through all the data and insert them into the database
        foreach ($spare_ids as $index => $spare_id) {
            // Prepare data for insertion
            $data = array(
                'shortage_spare_id' => $spare_id,
                'quantity' => $quantities[$index],
				'df_id'=> $df__id,
                'added_on' => $date,
                'added_by' => $user_id
            );
      
            $this->master->insert_record($table, $data);
        } 

   
        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
            redirect(page_url.'Dashboard');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">Sorry, a technical error occurred.</div>');
            redirect(page_url.'Dashboard');
        }
    }
}




 public function delay_table(){
		
			$this->load->view('master/delay_table');
		
	}

 public function export_delay_table_department_excel()
 {
	$department_label = trim((string) $this->input->post('department_label'));
	$export_rows_json = (string) $this->input->post('export_rows_json');
	$export_token = trim((string) $this->input->post('export_token'));
	$export_rows = json_decode($export_rows_json, true);

	if (!is_array($export_rows) || empty($export_rows)) {
		show_error('No department-wise rows were provided for export.', 400, 'Export Error');
		return;
	}

	$this->load->library('excel');
	$object = new PHPExcel();
	$object->setActiveSheetIndex(0);
	$sheet = $object->getActiveSheet();

	$clean_department_label = $department_label !== '' ? $department_label : 'Department';
	$sheet_title = substr(preg_replace('/[^A-Za-z0-9 ]+/', '', $clean_department_label) . ' Tasks', 0, 31);
	if ($sheet_title === '') {
		$sheet_title = 'Department Tasks';
	}
	$sheet->setTitle($sheet_title);

	$table_columns = array(
		'DF No.',
		'DF Description',
		'Company',
		'PO No.',
		'Marketing Person',
		'Department',
		'Task',
		'Responsible Person',
		'Start Date',
		'End Date',
		'Actual Completion Date',
		'Delay In Day',
		'Task Remark',
		'Ticket Info'
	);

	$column_letters = range('A', 'N');
	foreach ($table_columns as $index => $field) {
		$sheet->setCellValue($column_letters[$index] . '1', $field);
	}

	$header_style = array(
		'font' => array(
			'bold' => true,
			'color' => array('rgb' => 'FFFFFF')
		),
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('rgb' => '2A7DB8')
		),
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
		),
		'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		)
	);

	$body_style = array(
		'alignment' => array(
			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
		),
		'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		)
	);

	$sheet->getStyle('A1:N1')->applyFromArray($header_style);
	$sheet->getDefaultStyle()->getAlignment()->setWrapText(true);
	$sheet->freezePane('A2');

	$excel_row = 2;
	foreach ($export_rows as $row) {
		$sheet->setCellValueExplicit('A' . $excel_row, isset($row['df_no']) ? (string) $row['df_no'] : '');
		$sheet->setCellValue('B' . $excel_row, isset($row['df_description']) ? (string) $row['df_description'] : '');
		$sheet->setCellValue('C' . $excel_row, isset($row['company_name']) ? (string) $row['company_name'] : '');
		$sheet->setCellValueExplicit('D' . $excel_row, isset($row['po_no']) ? (string) $row['po_no'] : '');
		$sheet->setCellValue('E' . $excel_row, isset($row['marketing_person']) ? (string) $row['marketing_person'] : '');
		$sheet->setCellValue('F' . $excel_row, $clean_department_label);
		$sheet->setCellValue('G' . $excel_row, isset($row['task_name']) ? (string) $row['task_name'] : '');
		$sheet->setCellValue('H' . $excel_row, isset($row['assigned_to']) ? (string) $row['assigned_to'] : '');
		$sheet->setCellValue('I' . $excel_row, isset($row['start_date']) ? (string) $row['start_date'] : '');
		$sheet->setCellValue('J' . $excel_row, isset($row['end_date']) ? (string) $row['end_date'] : '');
		$sheet->setCellValue('K' . $excel_row, isset($row['actual_completion_date']) ? (string) $row['actual_completion_date'] : '');
		$sheet->setCellValue('L' . $excel_row, isset($row['delay_in_day']) ? (string) $row['delay_in_day'] : '');
		$sheet->setCellValue('M' . $excel_row, isset($row['task_remark']) ? (string) $row['task_remark'] : '');
		$sheet->setCellValue('N' . $excel_row, isset($row['ticket_info']) ? (string) $row['ticket_info'] : '');
		$excel_row++;
	}

	if ($excel_row > 2) {
		$sheet->getStyle('A2:N' . ($excel_row - 1))->applyFromArray($body_style);
	}

	$column_widths = array(
		'A' => 12,
		'B' => 34,
		'C' => 28,
		'D' => 18,
		'E' => 24,
		'F' => 18,
		'G' => 28,
		'H' => 24,
		'I' => 16,
		'J' => 16,
		'K' => 20,
		'L' => 16,
		'M' => 34,
		'N' => 42
	);

	foreach ($column_widths as $column => $width) {
		$sheet->getColumnDimension($column)->setWidth($width);
	}

	$sheet->getStyle('A:N')->getAlignment()->setWrapText(true);

	$file_name = 'running_df_task_detail_' . strtolower(preg_replace('/[^A-Za-z0-9]+/', '_', $clean_department_label)) . '_' . date('Ymd_His') . '.xlsx';

	$writer = PHPExcel_IOFactory::createWriter($object, 'Excel2007');

	if (ob_get_length()) {
		ob_end_clean();
	}

	if ($export_token !== '') {
		setcookie('delay_table_export_token', $export_token, 0, '/');
	}

	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment; filename="' . $file_name . '"');
	header('Cache-Control: max-age=0');
	$writer->save('php://output');
	exit;
 }



	public function posendintroemail(){
	$this->load->view('master/po_intro_email');
}
	

}
?>
