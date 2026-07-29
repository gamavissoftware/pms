<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master_access extends CI_Controller {
	
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
		$this->load->library('Master_profile_guard');
		$this->master_profile_guard->allow_only_methods(
			array(),
			'This EA profile cannot access support master setup screens.'
		);
		$ip = $_SERVER["REMOTE_ADDR"];
		 /*$query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
            if($query->num_rows()=='0'){
            $this->session->set_flashdata('message','<span class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</span><br/>');
            redirect(page_url.'Dashboard');    
            }*/
	}

	public function index(){
		
		$this->load->view('master_access/add_access');
		
	}
	
	public function select_users()
	{
	echo "<option value=''>--Select User--</option>";
	$department = $this->input->post('department');
	$query = $this->db->select('user_id, first_name, last_name, user_status, hide_profile,department_id')->from('system_users')->where('department_id',$department)->where('user_status','1')->where('hide_profile','0')->get();
	
			foreach($query->result() as $users)
			{
				echo "<option value=".$users->user_id.">".$users->first_name." ".$users->last_name."</option>";
				}
		
		}
		
	
	public function set_access(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('support_option', 'Support Option', 'required|trim');
		$this->form_validation->set_rules('business_loc', 'Business Location', 'required|trim');
		$this->form_validation->set_rules('department', 'Department', 'required|trim');
		$this->form_validation->set_rules('users', 'users', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master_access/add_access');
		}
		else
		{
		$qry = $this->db->select('*')->from('support_module_access')->where('support_id',$this->input->post('support_option'))->where('business_location',$this->input->post('business_loc'))->where('department_id',$this->input->post('department'))->where('user_id',$this->input->post('users'))->get();
		$res = $qry->result();
		if($res){
			$this->session->set_flashdata('message','Sorry!, This record already exist.');
			redirect(page_url.'Master_access');
			
		}else{
				$user_id =$this->session->userdata['logged_in']['user_id'];		
				date_default_timezone_set("Asia/Kolkata");
				$date =  date('Y-m-d H:i:s'); 
				$data = array('support_id'=>$this->input->post('support_option'),
				'business_location'=>$this->input->post('business_loc'),
				'department_id'=>$this->input->post('department'),
				'user_id'=>$this->input->post('users'),
				'added_by'=>$user_id,
				'added_on'=>$date);
				$this->db->insert('support_module_access',$data);
				$this->session->set_flashdata('message','Thank you, record successfully added.');
				redirect(page_url.'Master_access');
		}
			
		
		}
	}
	
		public function access_list()
	{
		$email_data = array();
		$this->db->distinct();
		$this->db->select('a.*,b.business_loc_id, b.company_name, c.department_id, c.department, d.user_id, d.first_name, d.last_name')->from('support_module_access a');
		$this->db->join('business_location b','a.business_location=b.business_loc_id','left');
		$this->db->join('departments c','a.department_id=c.department_id','left');
		$this->db->join('system_users d','a.user_id=d.user_id','left');
		$res= $this->db->get();
		$result = $res->result();
		$i=1;
		foreach($result as $row)
		{
			if($row->support_id=='1'){
				$support_name = "Tech Support";
			}else if($row->support_id=='2'){
				$support_name = "Dispatch Support";
			}else if($row->support_id=='3'){
				$support_name = "Service Support";
			}
			else{
				$support_name = "Maintenance Support";
			}
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('Y-m-d', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time));
			
			
			$edit = "<a href='".page_url."Master_access/edit_access/".$row->id."'><i class='fa fa-pencil'></i></a>";
			
			$email_data[] = array('sr_no'=>$i,
			'section'=>$support_name,
			'company_name'=>$row->company_name,
			'department'=>$row->department,
			'user_name'=>$row->first_name." ".$row->last_name,
			'added_time'=>$addeddate.$addedtime,
			'edit'=>$edit);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($email_data),
	"iTotalDisplayRecords" => count($email_data),
	"aaData"=>$email_data);
	echo json_encode($results);
}
public function edit_access(){
		
		$this->load->view('master_access/edit_access');
		
	}

	public function update_support_access()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('support_option', 'Support Option', 'required|trim');
		$this->form_validation->set_rules('business_loc', 'Business Location', 'required|trim');
		$this->form_validation->set_rules('department', 'Department', 'required|trim');
		$this->form_validation->set_rules('users', 'users', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			
			$this->load->view('master_access/edit_access');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		    $date =  date('Y-m-d H:i:s'); 
			$qry = $this->db->select('*')->from('support_module_access')->where('support_id',$this->input->post('support_option'))->where('business_location',$this->input->post('business_loc'))->where('department_id',$this->input->post('department'))->where('user_id',$this->input->post('users'))->get();
		$res = $qry->result();
		if($res){
			$this->session->set_flashdata('message','Sorry!, This record already exist.');
			redirect(page_url.'Master_access');
			
		}else{	
			
			
		$data = array('support_id'=>$this->input->post('support_option'),
				'business_location'=>$this->input->post('business_loc'),
				'department_id'=>$this->input->post('department'),
				'user_id'=>$this->input->post('users'),
				'added_by'=>$user_id,
				'added_on'=>$date);
			$this->db->where('id',$this->uri->segment(3));
			$result  = $this->db->update('support_module_access',$data);		
		
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'Master_access');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master_access');
			
		}
		}
		
	}
		
	}
	
	public function delete_emails(){
		$this->db->where('id', $this->uri->segment(3));
		$this->db->delete('support_email_options');
		$this->session->set_flashdata('message','Thank you, record successfully deleted.');
		redirect(page_url.'Master_emails');

	}
	
}
