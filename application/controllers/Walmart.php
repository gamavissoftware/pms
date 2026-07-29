<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Walmart  extends CI_Controller { 

public function __construct()
		{
			parent::__construct();
			
		
			$this->load->model('User_model','user');
			$this->load->model('Store_model','storemodel');
			$ip = $_SERVER["REMOTE_ADDR"];
		/* $query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
            if($query->num_rows()=='0'){
            $this->session->set_flashdata('message','<span class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</span><br/>');
            redirect(page_url.'Dashboard');    
            }*/
			
		}
		
		
	public function login()
	{
		$this->load->view('wallmart/login');
		
	}
	
	
	public function authenticate()
	{
		
		
		$this->form_validation->set_rules('email', 'Email ID', 'required|trim');
		$this->form_validation->set_rules('password', 'Password', 'required|trim');
		$this->form_validation->set_error_delimiters('<div style="color:green;">', '</div>');
		
		if ($this->form_validation->run() == FALSE)
		{
		$this->load->view('Wallmart/login');
		}else
		{
			$ip = $_SERVER["REMOTE_ADDR"];
			$username = $this->input->post('email');
			$password = $this->input->post('password');
			$result=$this->user->authenticationforwallmart($username,$password);
		if($result)
			{
				$data = array();
             foreach($result as $row) {
                 
				$data = array('user_id' => $row->id,
					 'username'=>$row->username);
					$this->session->set_userdata('wallmart',$data);
					
			 }
			
				
			 //$employee_ID=$row->employee_ID;
			 redirect(page_url.'Walmart/dashboard');
			
			}
			else
			{
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000; float-left:20px;"><span style="color:red; float-left:20px;">Invalid username/password combination</span></div><br/>');
				 redirect(page_url.'Walmart/login');	
			}
			
		}
	}
	
	
	function dashboard()
	{
		$session = $this->session->userdata('wallmart');
			if($session == FALSE)
			{
			redirect(page_url.'Walmart/login');
			}
		$this->load->view('wallmart/dashboard');
		
		
	}
	
	function signout()
	{
		
			$user_id=$this->session->userdata['wallmart']['user_id'];
		if($user_id){
		$user_id=$this->session->userdata['wallmart']['user_id'];
		$username=$this->session->userdata['wallmart']['username'];
		
		$log_array = array('user_id' => $user_id, 'user_name' =>$username);
		$this->session->unset_userdata($log_array);
        $this->session->sess_destroy();
        
		redirect(page_url.'Walmart/login');
		
		}else{
			
			redirect(page_url.'Walmart/login');
			
		}
		
		
		
	}
	
	
	function sampleallrequests()
{
	$scheduler_data=array();
	$userid = $this->uri->segment(3);
	$this->db->select('a.*')->from('specificsampletestrequest a');
	if($userid){
	    $this->db->where('a.addedBy',$userid);
	}
	$restyui=$this->db->order_by('a.status','ASC')->order_by('a.addedOn','DESC')->get();
		if($restyui->num_rows()>0)
		{
			
	$i=1;
	foreach($restyui->result() as $instruments)
		{
			$items='';	
				
	$restyui12=$this->db->select('*')->from('specificsampletobetested')->where('samplereqid',$instruments->id)->order_by('sample','ASC')->get();
		if($restyui12->num_rows()>0)
		{
			foreach($restyui12->result() as $restyui121)
			{
				$items.=strtoupper($restyui121->sample).'<br/>';
			}
	
	
		}
		if($instruments->status==0)
		{
			$sta="<span class='btn btn-warning btn-xs'>PENDING</span>";
			$rep="";
		
		}else
		{
			$sta="<span class='btn btn-warning'>COMPLETED</span>";
			$rep="<a href='".page_url."Walmart/generatesampletestingreport/".$instruments->id."' target='_blank'><span class='btn btn-xs'>VIEW REPORT</span></a>";
		}
		
	$sales=$this->storemodel->getudata($instruments->sales);
	
	$scheduler_data[] = array('sr_no'=>$i,
	'raisedon'=>date('d-m-Y g:i A',strtotime($instruments->addedOn)),
			'sampleid'=>$instruments->sampletestid,
			'companyname'=>$instruments->companyname,
			'items'=>$items,
			'status'=>$sta,
			'report'=>$rep);
			$i++;
			
	
	
			}
	
		}
		
		$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
	
}


function generatesampletestingreport()
{
	
	$this->load->view('specificsampletest/sampletest/report');
	
	
	
}


	
}