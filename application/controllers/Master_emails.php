<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master_emails extends CI_Controller {
	
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
			'This EA profile cannot access support email master screens.'
		);
		$ip = $_SERVER["REMOTE_ADDR"];
		/* $query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
            if($query->num_rows()=='0'){
            $this->session->set_flashdata('message','<span class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</span><br/>');
            redirect(page_url.'Dashboard');    
            }*/
	}

	public function index(){
		
		$this->load->view('master_email/add_emails.php');
		
	}
	
	public function feed_members_emailid(){
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$data = array('support_option'=>$this->input->post('support_option'),
		'email'=>$this->input->post('email'),
		'name'=>$this->input->post('member'),
		'added_by'=>$user_id,
		'added_on'=>$date);
		
		$this->db->insert('support_email_option',$data);
		$table = "support_email_options";
					if(isset($_REQUEST['members'])){	
					$tags1=count($_REQUEST['members']);
					if($tags1>0)
					{
					$date =  date('Y-m-d H:i:s'); 
					$member_attruibute=$_REQUEST['members'];
					$email=$_REQUEST['emails'];
					$support_option = $_REQUEST['support_option'];
					for($x=0;$x<$tags1;$x++){
					if($member_attruibute[$x]!='')
						{
							$data=array('name'=>$member_attruibute[$x],
							'support_option'=>$support_option,
							'email'=>$email[$x],
							'added_on'=>$date,
							'added_by'=>$user_id);
							$this->db->insert('support_email_options',$data);
							
						}
					}
					}
					}
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Master_emails');
		
	}
	
		public function email_list()
	{
		$email_data = array();
		$this->db->distinct();
		$query = $this->db->select('a.*')->from('support_email_option a')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('Y-m-d', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time));
			if($row->support_option=='1'){
				$support = "IT Support";
			}elseif($row->support_option=='2'){
				$support = "Dispatch Support";
			}elseif($row->support_option=='3'){
				$support = "Service Support";
			}elseif($row->support_option=='4'){
				$support = "Maintenance Support";
			}
			$cc_info = "";
			$j=1;
			$qry = $this->db->select('*')->from('support_email_options')->where('support_option',$row->support_option)->get();
			foreach($qry->result() as $cc_option){
				$cc_info.= $cc_option->name." (".$cc_option->email.")<br>";
				$j++;
			}
			
			$edit = "<a href='".page_url."Master_emails/edit_emails/".$row->id."/".$row->support_option."'><i class='fa fa-pencil'></i></a>";
			
			$email_data[] = array('sr_no'=>$i,
			'section'=>$support,
			'main_name'=>$row->name,
			'main_email'=>$row->email,
			'cc_info'=>$cc_info,
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
public function edit_emails(){
		
		$this->load->view('master_email/edit_mails');
		
	}

	public function update_email_detail()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('support_option', 'Support Option', 'required|trim');
		$this->form_validation->set_rules('email', 'Email', 'required|trim');
		$this->form_validation->set_rules('member', 'Members', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			
			$this->load->view('master_email/edit_mails');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
			$table = "support_email_option";	
			$identifier = $this->uri->segment(3);
			$field_name = "id";		
			
		$data = array('support_option'=>$this->input->post('support_option'),
		'email'=>$this->input->post('email'),
		'name'=>$this->input->post('member'),
		'added_on'=>$user_id);
			
		$result  = $this->master->update_records($table,$data,$identifier,$field_name);		
		
		if(isset($_REQUEST['members'])){	
					$tags1=count($_REQUEST['members']);
					if($tags1>0)
					{
					$date =  date('Y-m-d H:i:s'); 
					$member_attruibute=$_REQUEST['members'];
					$email=$_REQUEST['emails'];
					$support_option = $_REQUEST['support_option'];
					$record_id = $_REQUEST['record_id'];
					//echo "<pre>"; print_r($record_id); exit;
					for($x=0;$x<$tags1;$x++){
					if($member_attruibute[$x]!='')
						{
							$data=array('name'=>$member_attruibute[$x],
							'support_option'=>$support_option,
							'email'=>$email[$x],
							'added_on'=>$date,
							'added_by'=>$user_id);
							
							if($record_id[$x]){
								$this->db->where('id',$record_id[$x]);
								$this->db->update('support_email_options',$data);
							}else{
							$this->db->insert('support_email_options',$data);
							}
						}
					}
					}
					}
					
					
	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Master_emails');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master_emails');
			
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
