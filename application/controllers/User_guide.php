<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_guide extends CI_Controller {
	
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
		$ip = $_SERVER["REMOTE_ADDR"];
		 /*$query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
            if($query->num_rows()=='0'){
            $this->session->set_flashdata('message','<span class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</span><br/>');
            redirect(page_url.'Dashboard');    
            }*/
		
	}
	public function index(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('module_name', 'module_name', 'required|trim');
		$this->form_validation->set_rules('description', 'description', 'required|trim');
		$this->form_validation->set_rules('video', 'video', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		    
			$this->load->view('user_guide/user_guide');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		 
		   $data=
			array(
			'module_name'=>strtoupper($this->input->post('module_name')),
			'description'=>$this->input->post('description'),
			'video_link'=>$this->input->post('video'),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			
			$res = $this->db->insert('user_guide_videos',$data);
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
			redirect(page_url.'User_guide');
		  
			}
	}
	
	public function user_guide_dashboard(){
		$this->load->view('user_guide/user_guide_list');
	}
	
	public function user_guide_dashboard_list()
	{
		$leave_data = array();
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$this->db->select('a.*,b.first_name, b.last_name')->from('user_guide_videos a')->join('system_users b','a.added_by=b.user_id','left');
		$query = $this->db->order_by('module_name','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			$videolink = "<a href='' target='_blank'><span class='btn btn-primary btn-xs'>Click here to view Video</span></a>";
			$edit = "<a href='".page_url."User_guide/edit_user_guide/".$row->id."'><i class='fa fa-pencil-square-o'></i></a>";
			
			date_default_timezone_set("Asia/Kolkata");
			$added_time = date('Y-m-d h:i A',strtotime($row->added_on));
			$leave_data[] = array('sr_no'=>$i,
			'module_name'=>$row->module_name,
			'timestamp'=>$added_time,
			'description'=>$row->description,
			'video'=>$videolink,
			'added_by'=>$row->first_name." ".$row->last_name,
			'action'=>$edit);
			$i++;
		}
		
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($leave_data),
	"iTotalDisplayRecords" => count($leave_data),
	"aaData"=>$leave_data);
	echo json_encode($results);
}

public function edit_user_guide(){
		$this->load->view('user_guide/edit_user_guide');
	}
	
public function update_user_guide(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('module_name', 'module_name', 'required|trim');
		$this->form_validation->set_rules('description', 'description', 'required|trim');
		$this->form_validation->set_rules('video', 'video', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		    
			$this->load->view('user_guide/edit_user_guide');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		 
		   $data=
			array(
			'module_name'=>strtoupper($this->input->post('module_name')),
			'description'=>$this->input->post('description'),
			'video_link'=>$this->input->post('video'),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update('user_guide_videos',$data);
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
			redirect(page_url.'User_guide/user_guide_dashboard');
		  
			}
	}


}