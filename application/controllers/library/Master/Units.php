<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Units extends CI_Controller {
	
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
		
		$this->load->view('master/units');
		
	}
	
	function addunit()
	{
		$unitname=trim($this->input->post('unitname'));
		$shortname=trim($this->input->post('shortname'));
		$restyyuu=$this->db->select('id')->from('units')->where('name',$unitname)->get();
		if($restyyuu->num_rows()==0)
		{
			
			$data=array('name'=>$unitname,'shortname'=>$shortname,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
			$this->db->insert('units',$data);
			
			$this->session->set_flashdata('message','Record Added');
			redirect(page_url.'Master/Units');	
			
			
		}else
		{
			$this->session->set_flashdata('message','Record Exists');
			redirect(page_url.'Master/Units');	
		}
		
		
	}
	
	function unit_list()
	{
		
		$business_data = array();
		$this->db->select('*')->from('units');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
				$edit = "<a href='".page_url."Master/Units/editunit/".$row->id."'><i class='fa fa-pencil'></i></a>";
				
			$business_data[] = array('sr_no'=>$i,
			'unit'=>strtoupper($row->name),
			'short_name'=>strtoupper($row->shortname),
			'edit'=>$edit);
			$i++;
		}
		//echo "<pre>"; print_r($business_data); exit;
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($business_data),
			"iTotalDisplayRecords" => count($business_data),
			"aaData"=>$business_data);
			
		echo json_encode($results);
		
		
		
	}
	
	function editunit()
	{
		
		$this->load->view('master/edit_unit');
	}
	
	function update_unit()
	{
		$ids=$this->uri->segment(4);
		$data=array('name'=>trim($this->input->post('unitname')),'shortname'=>trim($this->input->post('shortname')));
		
		$this->db->where('id',$ids);
		$this->db->update('units',$data);
		 $this->session->set_flashdata('message','Record Updated');
			redirect(page_url.'Master/Units');	
		
		
		
		
		
	}
	
}