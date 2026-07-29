<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Competitor_analysis extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('User_model','user');
		$this->load->model('Master_model','master');
		
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
		$this->form_validation->set_rules('country_name', 'Country Name', 'required|trim');
		$this->form_validation->set_rules('state', 'State Name', 'required|trim');
		$this->form_validation->set_rules('company_name', 'Company Name', 'required|trim');
		$this->form_validation->set_rules('city_name', 'City Name', 'required|trim');
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
			$data = array('country_id'=>$this->input->post('country_name'),
			'state_id'=>$this->input->post('state'),
			'city_id'=>$this->input->post('city_name'),
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
		$this->db->select('a.*,b.country_id,b.country_name,c.state_name,c.state_id,d.city_id,d.city_name')->from('compititors a');
		$this->db->join('countries b','a.country_id=b.country_id','left');
		$this->db->join('states c','a.state_id=c.state_id','left');
		$this->db->join('cities d','a.city_id=d.city_id','left');
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
			'country_name'=>$row->country_name,
			'state_name'=>$row->state_name,
			'city_name'=>$row->city_name,
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
			move_uploaded_file($_FILES["photo"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/prestogroup/image_bank/compititor/product_img/' . $product_image);
		}else
		{
			$product_image="";
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
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
			redirect(page_url.'Competitor_analysis/products/'.$this->uri->segment(3));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Competitor_analysis/add_compititors/'.$this->uri->segment(3));
		}
		
	}
		
	}
	public function products_list()
	{
		$this->db->select('a.*,b.prd_category_id, b.product_category, c.prd_sub_category_id, c.sub_category')->from('product_specifications a');
		$this->db->join('product_category b','a.category_id=b.prd_category_id','left');
		$this->db->join('product_sub_category c','a.sub_category_id=c. 	prd_sub_category_id','left');
		//$this->db->where('a.compititor_id',$this->uri->segment(3));
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
				$edit = "<a href='".page_url."Competitor_analysis/edit_products_detail/".$row->product_id."'><i class='fa fa-pencil'></i></a>";
				$img = "<img src='".product_path.$row->product_image."' width='100px'>";
			$prestogroup_products[] = array('sr_no'=>$i,
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
			move_uploaded_file($_FILES["photo"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/prestogroup/image_bank/compititor/product_img/' . $product_image);
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
			redirect(page_url.'Competitor_analysis/products/');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Competitor_analysis/products/');
		}
		
	}
		
	}
	
	/********Compititor detail*********/
}
