<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Business_location extends CI_Controller {
	
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
		
		$this->load->view('master/business_location');
	}
	
	public function business_loc_listing()
	{
		$business_data = array();
		$this->db->select('a.*,b.country_id,b.country_name,c.state_name,c.state_id,d.city_id,d.city_name')->from('business_location a');
		$this->db->join('countries b','a.country_id=b.country_id','left');
		$this->db->join('states c','a.state_id=c.state_id','left');
		$this->db->join('cities d','a.city_id=d.city_id','left');
		$query = $this->db->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;									
		$i=1;
		foreach($res as $row)
		{
			$status = $row->business_loc_status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Master/Business_location/update_business_location_status/".$row->business_loc_id."/".$row->business_loc_status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Master/Business_location/update_business_location_status/".$row->business_loc_id."/".$row->business_loc_status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
				$edit = "<a href='".page_url."Master/Business_location/edit_business_location/".$row->business_loc_id."'><i class='fa fa-pencil'></i></a>";
				
			$business_data[] = array('sr_no'=>$i,
			'country_name'=>$row->country_name,
			'state_name'=>$row->state_name,
			'city_name'=>$row->city_name,
			'company_name'=>$row->company_name,
			'address'=>$row->address,
			'contact_number'=>$row->contact_number,
			'status'=>$sta,
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
	
	public function select_state()
	{
	echo "<option value=''>--Select State--</option>";
	$country = $this->input->post('country_id');
	
		//$ajax_department = $this->master->select_state($country);
			$res=$this->db->select('state_id, state_name')->from('states')->where('country_id',$country)->get();
			foreach($res->result() as $india_state)
			{
				echo "<option value=".$india_state->state_id.">".$india_state->state_name."</option>";
				}
		
		}

	public function select_country_code() {
	$country = $this->input->post('country_name');
	$query = $this->db->select('country_id, phonecode')->from('countries')->where('country_id',$country)->get();
		$res = $query->result();
		foreach($res as $country_code);
		echo $country_code->phonecode;
		
	}
		
	public function select_city()
	{
	echo "<option value=''>--Select city--</option>";
	$state = $this->input->post('state');
		$ajax_department = $this->master->select_city($state);
			foreach($ajax_department as $india_state)
			{
				echo "<option value='".$india_state->city_id."'>".$india_state->city_name."</option>";
				}
		
		}
	
	public function country(){
		$this->load->view('master/location_master/country');
	}
	
	public function countries_list()
	{
		$i=1;
		$country_data = array();
		$this->db->select('*')->from('countries');
		$this->db->order_by('country_name');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->country_status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Master/Business_location/update_country_status/".$row->country_id."/".$row->country_status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Master/Business_location/update_country_status/".$row->country_id."/".$row->country_status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
				
				
			$country_data[] = array('sr_no'=>$i,
			'country_name'=>$row->country_name,
			'country_code'=>$row->country_code,
			'status'=>$sta);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($country_data),
			"iTotalDisplayRecords" => count($country_data),
			"aaData"=>$country_data);
			
		echo json_encode($results);
	}
	
	public function update_country_status()
	{
		
		$identifier =  $this->uri->segment(4);
		$sval =  $this->uri->segment(5);
		$field_name = "country_id";
		$table = "countries";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('country_status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Status successfully updated.</div>');
			redirect('Master/Business_location/country');
		}
		
	public function states(){
		$this->load->view('master/location_master/states');
	}
	
	public function states_list()
	{
		$i=1;
		$state_data = array();
		$this->db->select('a.*,b.country_id,country_name')->from('states a');
		$this->db->join('countries b','a.country_id=b.country_id','left');
		$this->db->order_by('country_name','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->state_status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Master/Business_location/update_state_status/".$row->state_id."/".$row->state_status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Master/Business_location/update_state_status/".$row->state_id."/".$row->state_status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
				
				
			$state_data[] = array('sr_no'=>$i,
			'country_name'=>$row->country_name,
			'state_name'=>$row->state_name,
			'status'=>$sta);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($state_data),
			"iTotalDisplayRecords" => count($state_data),
			"aaData"=>$state_data);
			
		echo json_encode($results);
	}
	
	public function update_state_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(4);
		$sval =  $this->uri->segment(5);
		$field_name = "state_id";
		$table = "states";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('state_status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Status successfully updated.</div>');
			redirect('Master/Business_location/states');
		}

public function cities(){
		$this->load->view('master/location_master/cities');
	}

public function city_list()
	{
		$i=1;
		$city_data = array();
		$this->db->select('a.*,c.country_id,country_name, b.state_id,b.state_name')->from('cities a');
		$this->db->join('states b','a.state_id=b.state_id','left');
		$this->db->join('countries c','b.country_id=c.country_id','left');
		$this->db->order_by('c.country_name','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->city_status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Master/Business_location/update_city_status/".$row->city_id."/".$row->city_status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Master/Business_location/update_city_status/".$row->city_id."/".$row->city_status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
				
				
			$city_data[] = array('sr_no'=>$i,
			'country_name'=>$row->country_name,
			'state_name'=>$row->state_name,
			'city_name'=>$row->city_name,
			'status'=>$sta);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($city_data),
			"iTotalDisplayRecords" => count($city_data),
			"aaData"=>$city_data);
			
		echo json_encode($results);
	}
	public function update_city_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(4);
		$sval =  $this->uri->segment(5);
		$field_name = "city_id";
		$table = "cities";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('city_status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Status successfully updated.</div>');
			redirect('Master/Business_location/cities');
		}
		
	public function add()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('country_name', 'Country Name', 'required|trim');
		$this->form_validation->set_rules('state', 'State Name', 'required|trim');
		$this->form_validation->set_rules('company_name', 'Company Name', 'required|trim');
		$this->form_validation->set_rules('city_name', 'City Name', 'required|trim');
		$this->form_validation->set_rules('contact_number', 'Contact Number', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/business_location');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "business_location";
			$data = array('country_id'=>$this->input->post('country_name'),
			'state_id'=>$this->input->post('state'),
			'city_id'=>$this->input->post('city_name'),
			'company_name'=>$this->input->post('company_name'),
			'address'=>$this->input->post('address'),
			'business_loc_status'=>$this->input->post('status'),
			'contact_number'=>$this->input->post('contact_number'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Master/Business_location');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div>');
			redirect(page_url.'Master/Business_location');
		}
		
	}
		
	}
	
	public function update_business_location_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(4);
		$sval =  $this->uri->segment(5);
		$field_name = "business_loc_id";
		$table = "business_location";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('business_loc_status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', 'Status successfully updated.');
			redirect('Master/Business_location');
		}
	
	public function edit_business_location()
	{
		$this->load->view('master/edit_business_location');
		
	}
	
	public function update_business_location()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('country_name', 'Country Name', 'required|trim');
		$this->form_validation->set_rules('state', 'State Name', 'required|trim');
		$this->form_validation->set_rules('company_name', 'Company Name', 'required|trim');
		$this->form_validation->set_rules('city_name', 'City Name', 'required|trim');
		$this->form_validation->set_rules('contact_number', 'Contact Number', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/edit_business_location');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "business_location";	
		$identifier = $this->uri->segment(4);
		$field_name = "business_loc_id";		
		$data = array('country_id'=>$this->input->post('country_name'),
			'state_id'=>$this->input->post('state'),
			'city_id'=>$this->input->post('city_name'),
			'company_name'=>$this->input->post('company_name'),
			'address'=>$this->input->post('address'),
			'business_loc_status'=>$this->input->post('status'),
			'contact_number'=>$this->input->post('contact_number'),
			'updated_on'=>$date,
			'added_by'=>$user_id);
		$result  = $this->master->update_records($table,$data,$identifier,$field_name);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Master/Business_location');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div>');
			redirect(page_url.'Master/Business_location');	
			
		}
			
	}
		
	}
	
	
}
