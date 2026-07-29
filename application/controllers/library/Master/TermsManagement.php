<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TermsManagement extends CI_Controller {
	
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
		
	}

	public function index()
	{   
		
		$this->load->view('master/view_all_terms');
	}

		public function view_all_terms()
	{  
		
		$this->load->view('master/view_all_terms');
	}
	
	

		public function all_created_terms()
	{
		$team_data = array();
		$i=1;
		$this->db->select('id,title,description,status, term_conditions_for')->from('terms_and_conditions_master');
		$this->db->order_by('id','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Master/TermsManagement/update_terms_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
				
				
			}else
			{
				$sta =  "<a href='".page_url."Master/TermsManagement/update_terms_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Master/TermsManagement/edit_terms/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			if($row->term_conditions_for=='1'){
				$for = "FOR QUOTATION";
			}else{
				$for = "FOR PI";
			}
			$terms_data[] = array('sr_no'=>$i,
			'title'=>$row->title,
			'description'=>$row->description,
			'status'=>$sta,
			'forterms'=>$for,
			'edit'=>$edit);
			$i++;
		}
		//echo "<pre>"; print_r($terms_data); exit;
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($terms_data),
			"iTotalDisplayRecords" => count($terms_data),
			"aaData"=>$terms_data);
			
		echo json_encode($results);
	}

	public function create_terms()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('terms_title', 'Terms Title', 'required|trim');
		$this->form_validation->set_rules('terms_description', 'Terms Description', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/view_all_terms');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "terms_and_conditions_master";
	
		
		
			$data = array('title'=>$this->input->post('terms_title'),
			'description'=>$this->input->post('terms_description'),
			'status'=>$this->input->post('status'),
			'term_conditions_for'=>$this->input->post('term_conditions_for'));
			//echo "<pre>"; print_r($data); exit;
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Master/TermsManagement/view_all_terms');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/TermsManagement/view_all_terms');
		}
		
		
	}
		
	}

	public function edit_terms(){
		$this->load->view('master/edit_terms');
	}	

	public function update_terms()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('terms_title', 'Terms Title', 'required|trim');
		$this->form_validation->set_rules('terms_description', 'Terms Description', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/edit_terms');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "terms_and_conditions_master";
		
		
		
			$data = array('title'=>$this->input->post('terms_title'),
			'description'=>$this->input->post('terms_description'),
			'status'=>$this->input->post('status'),
			'term_conditions_for'=>$this->input->post('term_conditions_for'));
			$this->db->where('id',$this->uri->segment(4));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'Master/TermsManagement/view_all_terms');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/TermsManagement/view_all_terms');
		}
		
		
	}
		
	}

	public function update_terms_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(4);
		$sval =  $this->uri->segment(5);
		$field_name = "id";
		$table = "terms_and_conditions_master";
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
			redirect('Master/TermsManagement/view_all_terms');
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
		//echo "<pre>"; print_r($res); exit;
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry! This record already exist.</div>');
			redirect(page_url.'Master/User_management/Departments');
			
		}else{
		
		
			$data = array('business_loc_id'=>$this->input->post('business_loc'),
			'department'=>$this->input->post('department_name'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Master/User_management/Departments');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/User_management/Departments');
		}
		}
		
	}
		
	}
	public function department_list()
	{
		$i=1;
		$department_data= array();
		$this->db->select('a.*,b.business_loc_id,company_name')->from('departments a');
		$this->db->join('business_location b','a.business_loc_id=b.business_loc_id','left');
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
			$department_data[] = array('sr_no'=>$i,
			'company_name'=>$row->company_name,
			'department'=>$row->department,
			'google'=>$add,
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
			redirect('Master/User_management/Departments');
		}

	public function edit_department(){
		$this->load->view('master/edit_department');
		
	}	
	
	public function update_department()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('business_loc', 'Business Location', 'required|trim');
		$this->form_validation->set_rules('department_name', 'Department Name', 'required|trim');
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
		$query = $this->db->select('business_loc_id, department')->from('departments')->where('business_loc_id',$this->input->post('business_loc'))->where('department',$this->input->post('department_name'))->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry! This record already exist.</div>');
			redirect(page_url.'Master/User_management/Departments');
			
		}else{
		
		
			$data = array('business_loc_id'=>$this->input->post('business_loc'),
			'department'=>$this->input->post('department_name'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			$this->db->where('department_id',$this->uri->segment(4));
		$result  = $this->db->update($table,$data);	
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
		$department_data = array();
		$this->db->select('a.*,b.business_loc_id,b.company_name,c.department_id, c.department')->from('user_role a');
		$this->db->join('business_location b','a.business_loc_id=b.business_loc_id','left');
		$this->db->join('departments c','a.department_id=c.department_id','left');
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
			$department_data[] = array('sr_no'=>$i,
			'company_name'=>$row->company_name,
			'department'=>$row->department,
			'user_role'=>$row->user_role,
			'permission'=>$set_permission,
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
			redirect('Master/User_management/user_role');
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
	echo "<option value=''>--Select Department--</option>";
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
			move_uploaded_file($_FILES["adharcard"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/image_bank/users/document/' . $aadhar_card);
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
			move_uploaded_file($_FILES["photo"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/image_bank/users/' . $profile_photo);
		}else
		{
			$profile_photo="";
			}
			
			$rand_number  = mt_rand(15, 50);
		$password = "Prestogroup-".$rand_number;
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
			'added_by'=>$user_id);
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
		$this->db->select('a.user_id,a.first_name, a.last_name, a.email , a.contact_number, a.alternate_number, a.profile_image, a.business_location, a.department_id, a.user_role_id, a.user_status, a.hide_profile,b.business_loc_id,b.company_name,c.department_id,c.department,d.user_role_id,d.user_role')->from('system_users a');
		$this->db->join('business_location b','a.business_location=b.business_loc_id','left');
		$this->db->join('departments c','a.department_id=c.department_id','left');
		$this->db->join('user_role d','a.user_role_id=d.user_role_id','left');
		$this->db->where('a.hide_profile','0');
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
				$edit = "<a href='".page_url."Master/User_management/edit_user_profile/".$row->user_id."'><i class='fa fa-pencil'></i></a>";
				$delete = "<a href='".page_url."Master/User_management/delete_user/".$row->user_id."'><i class='fa fa-trash' title='Remove User Profile'></i></a>";
			$notify = "<a href='".page_url."Master/User_management/send_login_detail/".$row->user_id."'><span class='btn btn-warning btn-xs'>Send Login Detail</span></a>";
			
			$img = "<img src='".user_profile.$row->profile_image."' width='100px' class='img-circle'>";	
			$user_data[] = array('sr_no'=>$i,
			'profile_image'=>$img,
			'first_name'=>$row->first_name." ".$row->last_name,
			'email'=>$row->email,
			'contact_number'=>$row->contact_number."<br>".$row->alternate_number,
			'company_name'=>$row->company_name,
			'department'=>$row->department,
			'user_role'=>$row->user_role,
			'notify'=>$notify,
			'status'=>$sta,
			'edit'=>$edit." l ".$delete);
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
	
	public function send_login_detail(){
	    $query = $this->db->select('user_id, first_name, last_name, email, password')->from('system_users')->where('user_id',$this->uri->segment(4))->get();
	    foreach($query->result() as $user_information);
	    
	    
	    	$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
				   <tr>
					<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="http://crm.packingtest.com/assets/images/logo-1.png" width="200px;" alt="Prestogroup" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>CRM Login Credential </strong><br><br></td>
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
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Login URL </strong> : http://crm.packingtest.com</td>
					  </tr>
					  <tr>
						<td>&nbsp;</td>
					  </tr>
					 
					  
					  <tr>
						<td>Regards,<br><br> Presto Team</td>
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
				$subjectname = "CRM Login Credential";
					$this->email->set_mailtype("html");
					$this->email->to($user_information->email);
					$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('support@prestogroup.com');
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
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('user_status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', 'Status successfully updated.');
			redirect('Master/User_management');
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
			redirect('Master/User_management');
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
			move_uploaded_file($_FILES["adharcard"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/image_bank/users/document/' . $aadhar_card);
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
			move_uploaded_file($_FILES["photo"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/image_bank/users/' . $profile_photo);
		}else
		{
			$profile_photo=$old_img;
			}
			
		//$rand_number  = mt_rand(15, 50);
		//$password = "Prestogroup-".$rand_number;
			$data = array('first_name'=>$this->input->post('first_name'),
			'last_name'=>$this->input->post('last_name'),
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
			'added_by'=>$user_id);
			$this->db->where('user_id',$this->uri->segment(4));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
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
		$query = $this->db->select('business_loc_id, department_id, team_name')->from('prestogroup_teams')->where('business_loc_id',$this->input->post('business_loc'))->where('department_id',$this->input->post('department'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','Sorry! This record already exist.');
			redirect(page_url.'Master/User_management/presto_team_list');
			
		}else{
		
		
			$data = array('business_loc_id'=>$this->input->post('business_loc'),
			'department_id'=>$this->input->post('department'),
			'team_leader'=>$this->input->post('team_leader'),
			'team_name'=>$this->input->post('team_name'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>'1');
			//echo "<pre>"; print_r($data); exit;
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Master/User_management/presto_team_list');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/User_management/presto_team_list');
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
			redirect('Master/User_management/presto_team_list');
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
		$query = $this->db->select('business_loc_id, department_id, team_name')->from('prestogroup_teams')->where('business_loc_id',$this->input->post('business_loc'))->where('department_id',$this->input->post('department'))->where('team_leader',$this->input->post('team_leader'))->where('team_name',$this->input->post('team_name'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','Sorry! This record already exist.');
			redirect(page_url.'Master/User_management/presto_team_list');
			
		}else{
		
		
			$data = array('business_loc_id'=>$this->input->post('business_loc'),
			'department_id'=>$this->input->post('department'),
			'team_name'=>$this->input->post('team_leader'),
			'team_name'=>$this->input->post('team_name'),
			'status'=>$this->input->post('status'),
			'added_by'=>'1');
			$this->db->where('team_id',$this->uri->segment(4));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'Master/User_management/presto_team_list');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/User_management/presto_team_list');
		}
		}
		
	}
		
	}
	public function select_team_member()
	{
	echo "<option value=''>--Select Team--</option>";
	$department = $this->input->post('department');
	$business_loc = $this->input->post('business_loc');
		$query = $this->db->select('user_id, first_name, last_name, business_location, department_id')->from('system_users')->where('business_location',$business_loc)->where('department_id',$department)->get();
			foreach($query->result() as $users)
			{
				echo "<option value=".$users->user_id.">".$users->first_name." ".$users->last_name."</option>";
				}
		
		}
	
	public function add_member_in_team()
	{
	$this->load->view('master/add_member_in_team');	
	}
	
	public function add_members(){
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "presto_team_members";
		$user_id =$this->session->userdata['logged_in']['user_id'];
		if(isset($_REQUEST['my_multi_select1'])){	
					$tags1=count($_REQUEST['my_multi_select1']);
					if($tags1>0)
					{
					$employee=$_REQUEST['my_multi_select1'];
					for($x=0;$x<$tags1;$x++){
					if($employee[$x]!='')
						{
							$data=array('business_loc_id'=>$this->uri->segment(5),
							'department_id'=>$this->uri->segment(6),
							'team_id'=>$this->uri->segment(4),
							'employee_id'=>$employee[$x],
							'added_on'=>$date,
							'added_by'=>$user_id);
							$query = $this->db->select('business_loc_id,department_id,team_id,employee_id')->from('presto_team_members')->where('business_loc_id',$this->uri->segment(5))->where('department_id',$this->uri->segment(6))->where('team_id',$this->uri->segment(4))->where('employee_id',$employee[$x])->get();
							$res = $query->result();
							if($res){}else{
							$this->master->insert_record($table,$data);
							}
						}
					}
					}
					}
		
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Master/User_management/presto_team_list');
		
	}
	
	public function view_team_list(){
		$this->load->view('master/view_team_members');
	}
	
	public function presto_team_list(){
		$this->load->view('master/view_team_list');
		
	}
	
	public function all_created_team()
	{
		$team_data = array();
		$i=1;
		$this->db->select('a.*,a.department_id as presto_department_id, b.business_loc_id,b.company_name,c.department_id, c.department, d.user_id, d.first_name, d.last_name')->from('prestogroup_teams a');
		$this->db->join('business_location b','a.business_loc_id=b.business_loc_id','left');
		$this->db->join('departments c','a.department_id=c.department_id','left');
		$this->db->join('system_users d','a.team_leader=d.user_id','left');
		//$this->db->where('a.department_id',$this->uri->segment(4));
		$this->db->order_by('a.team_name','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Master/User_management/update_team_status/".$row->team_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
				
				$add_team = "<a href='".page_url."Master/User_management/add_member_in_team/".$row->team_id."/".$row->business_loc_id."/".$row->presto_department_id."'><span class='btn btn-success btn-xs'><i class='fa fa-group'></i>&nbsp;Add Member</span></a>";
				
				$view_team_members = "<a href='".page_url."Master/User_management/view_team_list/".$row->team_id."'><span class='btn btn-warning btn-xs'><i class='fa fa-group'></i>&nbsp;View Members</span></a>";
				/*1st segment is team id, 2nd is business location and 3rd is department ID*/
			}else
			{
				$sta =  "<a href='".page_url."Master/User_management/update_team_status/".$row->team_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
				$add_team="";
				$view_team_members="";
			}
			$edit = "<a href='".page_url."Master/User_management/edit_team/".$row->team_id."'><i class='fa fa-pencil'></i></a>";	
			
			$team_data[] = array('sr_no'=>$i,
			'company_name'=>$row->company_name,
			'department'=>$row->department,
			'team_leader'=>$row->first_name." ".$row->last_name,
			'team'=>$row->team_name,
			'add_team'=>$add_team." ".$view_team_members,
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
					$title = $this->input->post('title');
					for($x=0;$x<$tags1;$x++){
					if($content_attruibute[$x]!='')
						{
							$data=array('google_sheet_url'=>$content_attruibute[$x],
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
			$addedtime = "<br>". date('g:i A', strtotime($time));									
			$edit = "<a href='".page_url."Master/User_management/edit_googlesheet_link/".$row->sheet_id."'><i class='fa fa-pencil'></i></a>";	
			$google_sheet_data[] = array('sr_no'=>$i,
			'title'=>"<a href='".$row->google_sheet_url."' target='_blank'>".$row->title."</a>",
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
			$google_sheet_data[] = array('sr_no'=>$i,
			'business_loc'=>$row->company_name."<br>".$row->address,
			'department'=>$row->department,
			'title'=>$link,
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
			'added_on'=>$date,
			'added_by'=>$user_id);
			$this->db->where('sheet_id',$this->uri->segment(4));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'Master/User_management/google_sheet_link/'.$department_id);
			
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
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'Master/User_management/change_password');
		}else{
		    $this->session->set_flashdata('message','Password combination is not matched.');
			redirect(page_url.'Master/User_management/change_password');
		}
		
	}
	
	function assigncapabilities()
{
	$roleid=$this->uri->segment('4');
	$module=$this->input->post('module');
	for($i=0;$i<count($module); $i++)
	{
		$moduleid=$module[$i];
	   $moduleac=$_REQUEST['moduleaccess'.$moduleid];
	  
	  $data=array('role_id'=>$roleid,'moduleid'=>$moduleid,'access'=>$moduleac,'addedOn'=>date('Y-m-d h:i:s'));
			
			$this->db->insert('module_access',$data);
			$lid=$this->db->insert_id();
			
			
			$submodule=$_REQUEST['submodule'.$moduleid];
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
				
				
				$data1=array('acessid'=>$lid,'role_id'=>$roleid,'moduleid'=>$moduleid,'submoduleid'=>$submoduleid,'submodule_access'=>$submoduleaccess,'madd'=>$add,'medit'=>$edit,'mremove'=>$remove,'addedOn'=>date('Y-m-d h:i:s'));
				
				$this->db->insert('module_capablity',$data1);
				}
				
				}
				
				}
				
				redirect(page_url.'Master/User_management/user_role');
				
				
				
}


function edit_capablities()
{

$roleid=$this->uri->segment('4');
//echo $roleid;exit;
	$module=$this->input->post('module');
	for($i=0;$i<count($module); $i++)
	{
		$moduleid=$module[$i];
	   $moduleac=$_REQUEST['moduleaccess'.$moduleid];
	  
	  $modfg=$this->db->select('id')->from('module_access')->where('role_id',$roleid)->where('moduleid',$moduleid)->get();
	  if($modfg->num_rows()>0)
	  {
			$data=array('role_id'=>$roleid,'moduleid'=>$moduleid,'access'=>$moduleac,'addedOn'=>date('Y-m-d h:i:s'));         
			$this->db->where('moduleid',$moduleid);
			$this->db->where('role_id',$roleid);
			$this->db->update('module_access',$data);
			$modfg1=$this->db->select('id')->from('module_access')->where('role_id',$roleid)->where('moduleid',$moduleid)->get();
			foreach($modfg1->result() as $modfg12);
			$lid=$modfg12->id;
			
	  }else
	  {
		  $data=array('role_id'=>$roleid,'moduleid'=>$moduleid,'access'=>$moduleac,'addedOn'=>date('Y-m-d h:i:s'));
		 $this->db->insert('module_access',$data);
		 $lid=$this->db->insert_id(); 
		  
	  }
			
			
			
			
			$submodule=$_REQUEST['submodule'.$moduleid];
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
				
				$isthere=$this->db->select('*')->from('module_capablity')->where('submoduleid',$submoduleid)->where('role_id',$roleid)->get();
				if($isthere->num_rows()=='0')
				{
					//echo "hi";exit;
				
				$data1=array('acessid'=>$lid,'role_id'=>$roleid,'moduleid'=>$moduleid,'submoduleid'=>$submoduleid,'submodule_access'=>$submoduleaccess,'madd'=>$add,'medit'=>$edit,'mremove'=>$remove,'addedOn'=>date('Y-m-d h:i:s'));
					//echo "<pre>"; print_r($data1);exit;
			
				$this->db->insert('module_capablity',$data1);
					
				}else
				{
				
					$data1=array('role_id'=>$roleid,'moduleid'=>$moduleid,'submoduleid'=>$submoduleid,'submodule_access'=>$submoduleaccess,'madd'=>$add,'medit'=>$edit,'mremove'=>$remove,'addedOn'=>date('Y-m-d h:i:s'));
					
				
				$this->db->where('submoduleid',$submoduleid);
				$this->db->where('role_id',$roleid);
				$this->db->update('module_capablity',$data1);
				}
				}
				}
			
			
	}
	
	
	redirect(page_url.'User/edit_permission/'.$roleid);

}


	}
?>