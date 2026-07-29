<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Saleszone extends CI_Controller {
	
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
		
	}
	
	function index()
	{
		
		$this->load->view('master/saleszone');
		
	}
	
	function addzone()
	{
		$zone=trim($this->input->post('zone'));
		$assuser=$this->input->post('assuser');
		
		$data=array('zone'=>$zone,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
		$this->db->insert('saleszone',$data);
		$lid=$this->db->insert_id();
		
		for($i=0;$i<count($assuser);$i++)
		{
			$data1=array('zoneid'=>$lid,'userid'=>$assuser[$i],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
			
			$this->db->insert('saleszoneusers',$data1);
		}
			
			$this->session->set_flashdata('message','<div class="alert-success">Record Added</a>');
			redirect(page_url.'Master/saleszone');	
		
	}
	
	function zone_list()
	{
		
		$business_data = array();
		$user='';
		$this->db->select('id,zone')->from('saleszone')->where('status',1);
		$query = $this->db->get();
		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		    	$user='';
			$qe=$this->db->select('a.userid,b.first_name,b.last_name')->from('saleszoneusers a')->join('system_users b','a.userid=b.user_id')->where('a.zoneid',$row->id)->get();
			if($qe->num_rows()>0)
			{
				foreach($qe->result() as $qer)
				{
				$user.=	ucwords(strtolower($qer->first_name." ".$qer->last_name.'<br/>'));
				}				
			}
			
				$edit = "<a href='".page_url."Master/Saleszone/editzone/".$row->id."'><i class='fa fa-pencil'></i></a>";
				
			$business_data[] = array('sr_no'=>$i,
			'salezone'=>strtoupper($row->zone),
			'users'=>$user,
			'edit'=>$edit);
			$i++;
		}
		
		}
		//echo "<pre>"; print_r($business_data); exit;
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($business_data),
			"iTotalDisplayRecords" => count($business_data),
			"aaData"=>$business_data);
			
		echo json_encode($results);
		
		
		
	}
	
	function editzone()
	{
		
		$this->load->view('master/edit_zone');
	}
	
	function update_zone()
	{
		$id=$this->uri->segment(4);
	
		
		$zone=trim($this->input->post('zone'));
		$assuser=$this->input->post('assuser');
		
		$data=array('zone'=>$zone);
		$this->db->where('id',$id);
		$this->db->update('saleszone',$data);
		$lid=$id;
		
		/** DELETE PREV RECORD **/
		$this->db->where('zoneid',$id);
		$this->db->delete('saleszoneusers');
		/** END **/
		
		for($i=0;$i<count($assuser);$i++)
		{
			$data1=array('zoneid'=>$lid,'userid'=>$assuser[$i],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
			
			$this->db->insert('saleszoneusers',$data1);
		}
			
			$this->session->set_flashdata('message','<div class="alert-success">Record Updated</a>');
			redirect(page_url.'Master/saleszone');	
		
		
		
	}
	
}