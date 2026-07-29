<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Competitor_analysis extends CI_Controller {
	
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
		$ip = $_SERVER["REMOTE_ADDR"];
		/* $query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
            if($query->num_rows()=='0'){
            $this->session->set_flashdata('message','<span class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</span><br/>');
            redirect(page_url.'Dashboard');    
            }*/
	}

	public function index()
	{
		$this->load->view('dashboard/dashboard');
	}
	
	public function product_category()
	{
		$this->load->view('compititor_analysis/product_category');
	}
	
	public function add_category()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('product_category', 'Product Category', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('compititor_analysis/product_category');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "product_category";
		$query = $this->db->select('product_category')->from('product_category')->where('product_category',$this->input->post('product_category'))->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		if($res){
			$this->session->set_flashdata('message','Sorry! This record already exist.');
			redirect(page_url.'Competitor_analysis/product_category');
			
		}else{
		
		
			$data = array('product_category'=>$this->input->post('product_category'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Competitor_analysis/product_category');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Competitor_analysis/product_category');
		}
		}
		
	}
		
	}
	public function product_category_list()
	{
		$i=1;
		$department_data = array();
		$this->db->select('prd_category_id, product_category, status')->from('product_category');
		$this->db->order_by('product_category','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Competitor_analysis/update_product_category_status/".$row-> 	prd_category_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Competitor_analysis/update_product_category_status/".$row-> 	prd_category_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Competitor_analysis/edit_product_category/".$row->prd_category_id."'><i class='fa fa-pencil'></i></a>";	
				
			$department_data[] = array('sr_no'=>$i,
			'product_category'=>$row->product_category,
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
	
	public function update_product_category_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "prd_category_id";
		$table = "product_category";
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
			redirect('Competitor_analysis/product_category');
		}

	public function edit_product_category(){
		$this->load->view('compititor_analysis/edit_product_category');
		
	}	
	
	public function update_product_category()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('product_category', 'Product Category', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('compititor_analysis/edit_product_category');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "product_category";
		$query = $this->db->select('product_category')->from('product_category')->where('product_category',$this->input->post('product_category'))->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		if($res){
			$this->session->set_flashdata('message','Sorry! This record already exist.');
			redirect(page_url.'Competitor_analysis/product_category');
			
		}else{
		
		
			$data = array('product_category'=>$this->input->post('product_category'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		$this->db->where('prd_category_id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'Competitor_analysis/product_category');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Competitor_analysis/product_category');
		}
		}
		
	}
		
	}
	
	public function add_sub_category()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('product_category', 'Product Category', 'required|trim');
		$this->form_validation->set_rules('sub_category', 'Product Sub Category', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('compititor_analysis/product_sub_category');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "product_sub_category";
		$query = $this->db->select('category_id, sub_category')->from('product_sub_category')->where('category_id',$this->input->post('product_category'))->where('sub_category',$this->input->post('sub_category'))->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-warning" style="color:#fff;">Sorry! This record already exist.</div>');
			redirect(page_url.'Competitor_analysis/add_sub_category');
			
		}else{
		
		
			$data = array('category_id'=>$this->input->post('product_category'),
			'sub_category'=>$this->input->post('sub_category'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully added.</div>');
			redirect(page_url.'Competitor_analysis/add_sub_category');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Competitor_analysis/add_sub_category');
		}
		}
		
	}
		
	}
	public function product_sub_category_list()
	{
		$i=1;
		$sub_category_data = array();
		$this->db->select('a.prd_sub_category_id,a.category_id, a.sub_category, a.status,b.prd_category_id, b.product_category')->from('product_sub_category a')->join('product_category b','a.category_id=b.prd_category_id','left');
		$this->db->order_by('a.sub_category','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Competitor_analysis/update_product_sub_category_status/".$row->prd_sub_category_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Competitor_analysis/update_product_sub_category_status/".$row-> 	prd_sub_category_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Competitor_analysis/edit_product_sub_category/".$row->prd_sub_category_id."'><i class='fa fa-pencil'></i></a>";	
				
			$sub_category_data[] = array('sr_no'=>$i,
			'product_category'=>$row->product_category,
			'sub_category'=>$row->sub_category,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($sub_category_data),
			"iTotalDisplayRecords" => count($sub_category_data),
			"aaData"=>$sub_category_data);
			
		echo json_encode($results);
	}
	
	public function update_product_sub_category_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "prd_sub_category_id";
		$table = "product_sub_category";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', '<div class="alert alert-danger" style="color:#fff;">Status successfully updated.</div>');
			redirect('Competitor_analysis/add_sub_category');
		}

	public function edit_product_sub_category(){
		$this->load->view('compititor_analysis/edit_product_sub_category');
	}	
	
	public function update_product_sub_category()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('product_category', 'Product Category', 'required|trim');
		$this->form_validation->set_rules('sub_category', 'Product Sub Category', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('compititor_analysis/edit_product_sub_category');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "product_sub_category";
		$query = $this->db->select('category_id, sub_category')->from('product_sub_category')->where('category_id',$this->input->post('product_category'))->where('sub_category',$this->input->post('sub_category'))->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-warning" style="color:#fff;">Sorry! This record already exist.</div>');
			redirect(page_url.'Competitor_analysis/add_sub_category');
			
		}else{
		
		
			$data = array('category_id'=>$this->input->post('product_category'),
			'sub_category'=>$this->input->post('sub_category'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			$this->db->where('prd_sub_category_id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully added.</div>');
			redirect(page_url.'Competitor_analysis/add_sub_category');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Competitor_analysis/add_sub_category');
		}
		}
		
	}
		
	}
	
	/********Compititor detail*********/
	public function add_compititors()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('zone_name', 'Zone', 'required|trim');
		$this->form_validation->set_rules('location', 'city', 'required|trim');
		$this->form_validation->set_rules('company_name', 'Company Name', 'required|trim');
		$this->form_validation->set_rules('contact_number', 'Contact Number', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('compititor_analysis/add_compititors');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "compititors";
			$data = array('zone_id'=>$this->input->post('zone_name'),
			'location_id'=>$this->input->post('location'),
			'company_name'=>$this->input->post('company_name'),
			'address'=>$this->input->post('address'),
			'status'=>$this->input->post('status'),
			'contact_number'=>$this->input->post('contact_number'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
			redirect(page_url.'Competitor_analysis/add_compititors');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Competitor_analysis/add_compititors');
		}
		
	}
		
	}
	public function Compititors_list()
	{
	    $compititor_data = array();
		$this->db->select('a.*,b.zone_id,b.zone,c.state_name,c.state_id')->from('compititors a');
		$this->db->join('working_zone b','a.zone_id=b.zone_id','left');
		$this->db->join('states c','a.location_id=c.state_id','left');
		$query = $this->db->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;									
		$i=1;
		foreach($res as $row)
		{
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Competitor_analysis/Compititors_status/".$row->compititor_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Competitor_analysis/Compititors_status/".$row->compititor_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
				$edit = "<a href='".page_url."Competitor_analysis/edit_compititors_detail/".$row->compititor_id."'><i class='fa fa-pencil'></i></a>";
			$add_product = "<a href='".page_url."Competitor_analysis/compititor_products_specification/".$row->compititor_id."'><span class='btn btn-warning btn-xs' title='Add Compititor product specifications'>+ Add/View Products and specs..</span></a>";	
			$compititor_data[] = array('sr_no'=>$i,
			'zone'=>$row->zone,
			'location'=>$row->state_name,
			'company_name'=>$row->company_name,
			'address'=>$row->address,
			'contact_number'=>$row->contact_number,
			'add_product'=>$add_product,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
		//echo "<pre>"; print_r($compititor_data); exit;
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($compititor_data),
			"iTotalDisplayRecords" => count($compititor_data),
			"aaData"=>$compititor_data);
			
		echo json_encode($results);
	}
	
	public function Compititors_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "compititor_id";
		$table = "compititors";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', '<div class="alert alert-danger" style="color:#fff;">Status successfully updated.</div>');
			redirect('Competitor_analysis/add_compititors');
		}

	public function edit_compititors_detail(){
		$this->load->view('compititor_analysis/edit_compititors');
	}	
	
	public function update_compititors_detail()
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
			$this->load->view('compititor_analysis/edit_compititors');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "compititors";	
		$identifier = $this->uri->segment(3);
		$field_name = "compititor_id";		
		$data = array('country_id'=>$this->input->post('country_name'),
			'state_id'=>$this->input->post('state'),
			'city_id'=>$this->input->post('city_name'),
			'company_name'=>$this->input->post('company_name'),
			'address'=>$this->input->post('address'),
			'status'=>$this->input->post('status'),
			'contact_number'=>$this->input->post('contact_number'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		$result  = $this->master->update_records($table,$data,$identifier,$field_name);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger">Thank you, record successfully updated.</div>');
			redirect(page_url.'Competitor_analysis/add_compititors');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Competitor_analysis/add_compititors');	
			
		}
			
	}
		
	}
	
	
	public function compititor_products_specification()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('product_name', 'Product Name', 'required|trim');
		$this->form_validation->set_rules('spacification', 'spacification', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('compititor_analysis/add_compititors_specification');
		}
		else
		{
		$compititor_id = $this->uri->segment(3);
		$product_id = $this->input->post('product_name');
		$query = $this->db->select('compititor_id, product_id')->from('compititor_product_specifications')->where('compititor_id',$compititor_id)->where('product_id',$product_id)->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Sorry, This record already exist.</div>');
			redirect(page_url.'Competitor_analysis/compititor_products_specification/'.$this->uri->segment(3));
			
		}else{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "compititor_product_specifications";
			$data = array('compititor_id'=>$this->uri->segment(3),
			'product_id'=>$this->input->post('product_name'),
			'specifications'=>$this->input->post('spacification'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Competitor_analysis/compititor_products_specification/'.$this->uri->segment(3));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Competitor_analysis/compititor_products_specification/'.$this->uri->segment(3));
		}
		}
	}
		
	}
	public function specifications_list()
	{
	    $compititor_product_data = array();
		$this->db->select('a.*,b.prd_category_id, b.product_category, c.prd_sub_category_id, c.sub_category, d.product_id, d.category_id, d.sub_category_id, d.product_name, d.product_image')->from('compititor_product_specifications a');
		$this->db->join('product_specifications d','a.product_id=d.product_id','left');
		$this->db->join('product_category b','d.category_id=b.prd_category_id','left');
		$this->db->join('product_sub_category c','d.sub_category_id=c. 	prd_sub_category_id','left');
		$this->db->where('a.compititor_id',$this->uri->segment(3));
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Competitor_analysis/product_specifications_status/".$row->prd_specification_id."/".$row->status."/".$row->compititor_id."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Competitor_analysis/product_specifications_status/".$row->prd_specification_id."/".$row->status."/".$row->compititor_id."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
				$edit = "<a href='".page_url."Competitor_analysis/edit_product_specification/".$row->prd_specification_id."'><i class='fa fa-pencil'></i></a>";
				$img = "<img src='".product_path.$row->product_image."' width='100px'>";
			$compititor_product_data[] = array('sr_no'=>$i,
			'product_category'=>$row->product_category,
			'sub_category'=>$row->sub_category,
			'product_name'=>$row->product_name,
			'product_image'=>$img,
			'specifications'=>$row->specifications,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
		//echo "<pre>"; print_r($compititor_data); exit;
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($compititor_product_data),
			"iTotalDisplayRecords" => count($compititor_product_data),
			"aaData"=>$compititor_product_data);
			
		echo json_encode($results);
	}
	
	public function product_specifications_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$compititor_id =  $this->uri->segment(5);
		$field_name = "prd_specification_id";
		$table = "compititor_product_specifications";
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
			redirect('Competitor_analysis/compititor_products_specification/'.$compititor_id);
		}

	public function edit_product_specification(){
		$this->load->view('compititor_analysis/edit_compititors_specification');
	}	
	
	public function update_product_specifications()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('product_name', 'Product Name', 'required|trim');
		$this->form_validation->set_rules('spacification', 'spacification', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('compititor_analysis/edit_compititors_specification');
		}
		else
		{
			
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "compititor_product_specifications";
			$data = array('specifications'=>$this->input->post('spacification'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		$this->db->where('prd_specification_id',$this->uri->segment(3));	
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Competitor_analysis/compititor_products_specification/'.$this->uri->segment(4));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Competitor_analysis/compititor_products_specification/'.$this->uri->segment(4));
		}
		
	}
		
	}
	public function select_sub_category()
	{
	echo "<option value=''>--Select Sub Category--</option>";
	$product_category = $this->input->post('product_category');
		$query = $this->db->select('prd_sub_category_id, category_id, sub_category, status')->from('product_sub_category')->where('category_id',$product_category)->where('status','1')->get();
			foreach($query->result() as $prd_sub_category)
			{
				echo "<option value=".$prd_sub_category->prd_sub_category_id.">".$prd_sub_category->sub_category."</option>";
				}
		
		}
	
	public function our_products()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('product_category', 'Product Category', 'required|trim');
		//$this->form_validation->set_rules('sub_category', 'Sub Category', 'required|trim');
		$this->form_validation->set_rules('product_name', 'Product Name', 'required|trim');
		$this->form_validation->set_rules('spacification', 'spacification', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('compititor_analysis/products');
		}
		else
		{
		$photo=$_FILES['photo']['name'];
		if($photo<>'')
		{
			$image1=explode('.',$photo);
			$cat_image=end($image1);
			$product_image=time().'.'.$cat_image;
			move_uploaded_file($_FILES["photo"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/image_bank/compititor/product_img/' . $product_image);
		}else
		{
			$product_image="";
			}	
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "product_specifications";
			$data = array('category_id'=>$this->input->post('product_category'),
			'product_name'=>$this->input->post('product_name'),
			'specifications'=>$this->input->post('spacification'),
			'status'=>$this->input->post('status'),
			'product_image'=>$product_image,
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
			redirect(page_url.'Competitor_analysis/our_products/'.$this->uri->segment(3));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Competitor_analysis/add_compititors/'.$this->uri->segment(3));
		}
		
	}
		
	}
	public function products_list()
	{
	    $prestogroup_products = array();
		$this->db->select('a.*,b.prd_category_id, b.product_category, c.prd_sub_category_id, c.sub_category')->from('product_specifications a');
		$this->db->join('product_category b','a.category_id=b.prd_category_id','left');
		$this->db->join('product_sub_category c','a.sub_category_id=c. 	prd_sub_category_id','left');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Competitor_analysis/products_status/".$row->product_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Competitor_analysis/products_status/".$row->product_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$compare = "<a href='".page_url."Competitor_analysis/compare_competitors_products/".$row->product_id."'><span class='btn btn-danger btn-xs'>Compare with Competitor</span></a>";
			$edit = "<a href='".page_url."Competitor_analysis/edit_products_detail/".$row->product_id."'><i class='fa fa-pencil'></i></a>";
			$img = "<img src='".product_path.$row->product_image."' width='100px'>";
			$prestogroup_products[] = array('sr_no'=>$i,
			'product_category'=>$row->product_category,
			'product_name'=>$row->product_name."<br><br>".$compare,
			'product_image'=>$img,
			'specifications'=>$row->specifications,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
		//echo "<pre>"; print_r($compititor_data); exit;
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($prestogroup_products),
			"iTotalDisplayRecords" => count($prestogroup_products),
			"aaData"=>$prestogroup_products);
			
		echo json_encode($results);
	}
	
	public function products_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$compititor_id =  $this->uri->segment(5);
		$field_name = "product_id";
		$table = "product_specifications";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', '<div class="alert alert-danger" style="color:#fff;">Status successfully updated.</div>');
			redirect('Competitor_analysis/our_products/');
		}

	public function edit_products_detail(){
		$this->load->view('compititor_analysis/edit_products');
	}	
	
	public function update_products_detail()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('product_category', 'Product Category', 'required|trim');
		//$this->form_validation->set_rules('sub_category', 'Sub Category', 'required|trim');
		$this->form_validation->set_rules('product_name', 'Product Name', 'required|trim');
		$this->form_validation->set_rules('spacification', 'spacification', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('compititor_analysis/edit_products');
		}
		else
		{
		$photo=$_FILES['photo']['name'];
		if($photo<>'')
		{
			$image1=explode('.',$photo);
			$cat_image=end($image1);
			$product_image=time().'.'.$cat_image;
			move_uploaded_file($_FILES["photo"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/image_bank/compititor/product_img/' . $product_image);
		}else
		{
			$product_image=$this->input->post('old_img');
			}	
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "product_specifications";
			$data = array('category_id'=>$this->input->post('product_category'),
			'sub_category_id'=>$this->input->post('sub_category'),
			'product_name'=>$this->input->post('product_name'),
			'specifications'=>$this->input->post('spacification'),
			'status'=>$this->input->post('status'),
			'product_image'=>$product_image,
			'added_on'=>$date,
			'added_by'=>$user_id);
		$this->db->where('product_id',$this->uri->segment(3));	
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
			redirect(page_url.'Competitor_analysis/our_products/');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Competitor_analysis/our_products/');
		}
		
	}
		
	}
	
	
	public function compare_competitors_products(){
		
		$this->load->view('compititor_analysis/compare_product_specifications');
		
	}
	
	public function filter_data(){
		//$category_name = $this->input->post('category_name');
		$product_name = $this->input->post('product_name');
		$competitor_name= $this->input->post('competitor_name');
		$location= $this->input->post('location');
		$this->db->select('a.product_id, a.category_id, a.product_name, a.product_image, a.specifications as presto_specification');
		$this->db->from('product_specifications a');
		/*if($category_name){
			$this->db->where('a.category_id',$category_name);	
		}*/
		if($product_name)
		{
			$this->db->where('a.product_id',$product_name);
		}
		$this->db->limit(1);
		$query = $this->db->get();
		$data['res'] = $query->result();
		
		if(!empty($competitor_name)){
		$this->db->select('a.product_id, a.category_id, a.product_name, a.product_image, a.specifications as presto_specification, b.prd_specification_id, b.compititor_id, b.product_id, b.specifications,c.compititor_id, c.zone_id, c.location_id, c.company_name');
		$this->db->from('product_specifications a');
		$this->db->join('compititor_product_specifications b','a.product_id=b.product_id','left');
		$this->db->join('compititors c','b.compititor_id=c.compititor_id','left');
		if($location){
			$this->db->where('c.location_id',$location);	
		}
		if($product_name)
		{
			$this->db->where('a.product_id',$product_name);
		}
		if($competitor_name)
		{
			$this->db->where('b.compititor_id',$competitor_name);
		}
		$query = $this->db->get();
		$data['competitor_data'] = $query->result();
	    }else{
	    $data['competitor_data'] = '';	
	    }
		
		$this->load->view('compititor_analysis/compare_chart',$data);
	}
	/********Compititor detail*********/
	
	public function add_zone(){
		$this->load->view('compititor_analysis/add_zone');
	}
	
	public function add_zone_with_location(){
		
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('zone', 'Zone', 'required|trim');
	$this->form_validation->set_rules('status', 'Status', 'required|trim');
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('compititor_analysis/add_zone');
			}else
		{
			$zone = $this->input->post('zone');
			
		$query = $this->db->select('zone')->from('working_zone')->where('zone',$zone)->get();
		$res = $query->result();
		if($res){
			
			$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Sorry, record already exist.</span><br/>');
			redirect(page_url.'Competitor_analysis/add_zone/');
			
			
		}else{
		date_default_timezone_set("Asia/Kolkata");
          $added_time = date('Y-m-d H:i:s');
		  $table = "working_zone";
		  $data=
			array('zone'=>$zone,
			'status'=>$this->input->post('status'),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			$res = $this->db->insert($table,$data);
			$zone_id = $this->db->insert_id();
			if($res)
			{
				$table = "zone_wise_locations";
					if(isset($_REQUEST['my_multi_select1'])){	
					$tags1=count($_REQUEST['my_multi_select1']);
					if($tags1>0)
					{
					$date =  date('Y-m-d H:i:s'); 
					$locations=$_REQUEST['my_multi_select1'];
					
					for($x=0;$x<$tags1;$x++){

					if($locations[$x]!='')
						{
							$data=array('location_id'=>$locations[$x],
							'zone'=>$zone_id,
							'added_on'=>$date,
							'added_by'=>$user_id);
							$this->master->insert_record($table,$data);
						}
					}
					}
					}
				
			
			$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you! Record successfully added.</span><br/>');
			redirect(page_url.'Competitor_analysis/add_zone/');

			
				}
					}
				}
	}
	
public function zone_list()
	{
	    $zone_data = array();
		$this->db->select('*')->from('working_zone');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Competitor_analysis/products_status/".$row->zone_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Competitor_analysis/products_status/".$row->zone_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
				$edit = "<a href='".page_url."Competitor_analysis/edit_zone/".$row->zone_id."'><i class='fa fa-pencil'></i></a>";
			$query = $this->db->select('a.zone_wise_loc_id, a.zone, a.location_id, b.state_id, b.state_name')->from('zone_wise_locations a')->join('states b','a.location_id=b.state_id','left')->where('a.zone',$row->zone_id)->get();
			
			foreach($query->result() as $location)
			
			
			$zone_data[] = array('sr_no'=>$i,
			'zone'=>$row->zone,
			'location'=>$location->state_name,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($zone_data),
			"iTotalDisplayRecords" => count($zone_data),
			"aaData"=>$zone_data);
			
		echo json_encode($results);
	}
	public function edit_zone(){
		$this->load->view('compititor_analysis/edit_zone');
	}
	
	public function select_zones()
	{
	//echo "<option value=''>--Select location--</option>";
	$zone_name = $this->input->post('zone_name');
	//echo $zone_name; exit;
		$query = $this->db->select('a.zone_wise_loc_id, a.zone, a.location_id, b.state_id, b.state_name')->from('zone_wise_locations a')->join('states b','a.location_id=b.state_id','left')->where('a.zone',$zone_name)->get();
			foreach($query->result() as $location)
			{
				echo "<option value=".$location->state_id.">".$location->state_name."</option>";
				}
		
		}
		
		public function select_locations()
	{
	//echo "<option value=''>--Select location--</option>";
	$location = $this->input->post('location');
	$product_id = $this->input->post('product_name');
	
		$query = $this->db->select('b.company_name, b.compititor_id')->from('compititor_product_specifications a')->join('compititors b','a.compititor_id=b.compititor_id')->where('a.product_id',$product_id)->where('b.location_id',$location)->get();
		    $result = $query->result();
		    if(!empty($result)){
			foreach($query->result() as $competitor)
			{
				echo "<option value=".$competitor->compititor_id.">".$competitor->company_name."</option>";
				}
			}else{

				echo '<option value="">No Competitors in this location</option>';

			}
		
		}
		
		
			function zone_comparision()
		{
			$this->load->view('compititor_analysis/zonecomparision');
		}
		
		
		
		
		public function add_zone_content()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('product_category', 'Product Category', 'required|trim');
		$this->form_validation->set_rules('sub_category', 'Product Sub Category', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('compititor_analysis/zonecomparision');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = " zone_comparison";
		$query = $this->db->select('zone_id, zonecontent')->from(' zone_comparison')->where('zone_id',$this->input->post('product_category'))->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-warning" style="color:#fff;">Sorry! This record already exist.</div>');
			redirect(page_url.'Competitor_analysis/zone_comparision');
			
		}else{
		
		
				$data = array('zone_id'=>$this->input->post('product_category'),
			'zonecontent'=>$this->input->post('sub_category'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully added.</div>');
			redirect(page_url.'Competitor_analysis/zone_comparision');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Competitor_analysis/zone_comparision');
		}
		}
		
	}
		
	}
	
	
	public function zone_content_list()
	{
		$i=1;
		$sub_category_data=array();
		$this->db->select('a.*,b.*,a.status as contentstatus')->from('zone_comparison a')->join('working_zone b','a.zone_id=b.zone_id','left');
		$this->db->order_by('a.prd_sub_category_id','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
								
											
			$status = $row->contentstatus;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Competitor_analysis/update_zone_content_status/".$row->prd_sub_category_id."/".$row->contentstatus."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Competitor_analysis/update_zone_content_status/".$row-> 	prd_sub_category_id."/".$row->contentstatus."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Competitor_analysis/edit_zone_content/".$row->prd_sub_category_id."'><i class='fa fa-pencil'></i></a>";	
				
			$sub_category_data[] = array('sr_no'=>$i,
			'product_category'=>$row->zone,
			'sub_category'=>$row->zonecontent,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($sub_category_data),
			"iTotalDisplayRecords" => count($sub_category_data),
			"aaData"=>$sub_category_data);
			
		echo json_encode($results);
	}
		
		
		
	public function update_zone_content_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		
		$sval =  $this->uri->segment(4);
		
		$field_name = "prd_sub_category_id";
		$table = "zone_comparison";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$this->db->where('prd_sub_category_id',$identifier);
			$res = $this->db->update('zone_comparison',$data);
			$this->session->set_flashdata('message', '<div class="alert alert-danger" style="color:#fff;">Status successfully updated.</div>');
			redirect('Competitor_analysis/zone_comparision');
		}
		
		
		
		
	public function edit_zone_content(){
		$this->load->view('compititor_analysis/edit_zone_content');
	}	
	
	public function update_zone_content()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('product_category', 'Product Category', 'required|trim');
		$this->form_validation->set_rules('sub_category', 'Product Sub Category', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('compititor_analysis/edit_zone_content');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "zone_comparison";
		$query = $this->db->select('zone_id,zonecontent')->from('zone_comparison')->where('zone_id',$this->input->post('product_category'))->where('zonecontent',$this->input->post('sub_category'))->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-warning" style="color:#fff;">Sorry! This record already exist.</div>');
			redirect(page_url.'Competitor_analysis/edit_zone_content/'.$this->uri->segment(3));
			
		}else{
		
		$data = array('zone_id'=>$this->input->post('product_category'),
			'zonecontent'=>$this->input->post('sub_category'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
	
			$this->db->where('prd_sub_category_id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully added.</div>');
			redirect(page_url.'Competitor_analysis/zone_comparision');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Competitor_analysis/zone_comparision');
		}
		}
		
	}
		
	}
		
		
		
}
