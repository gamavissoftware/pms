<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tech_support extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('User_model','user');
		$this->load->model('Master_model','master');
		
	}

	public function index()
	{
		$this->load->view('techsupport/raise_ticket');
	}
	
	public function view_status()
	{
		$this->load->view('techsupport/view_it_status');
	}
	
	public function location()
	{
		$this->load->view('techsupport/location');
	}
	
	public function add_location()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('location', 'Location', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('techsupport/ticket_list');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "installed_location";
		$query = $this->db->select('business_loc_id, installed_location')->from('installed_location')->where('business_loc_id',$this->input->post('business_loc'))->where('installed_location',$this->input->post('location'))->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		if($res){
			$this->session->set_flashdata('message','Sorry! This record already exist.');
			redirect(page_url.'Tech_support/location');
			
		}else{
		
		
			$data = array('business_loc_id'=>$this->input->post('business_loc'),
			'installed_location'=>$this->input->post('location'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Tech_support/location');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tech_support/location');
		}
		}
		
	}
		
	}
	public function installed_location_list()
	{
		$i=1;
		$this->db->select('a.location_id, a.business_loc_id,a.installed_location,a.status,b.company_name,b. 	business_loc_id,b.city_id,c.city_id, c.city_name')->from('installed_location a')->join('business_location b','a.business_loc_id=b.business_loc_id','left')->join('cities c','b.city_id=c.city_id','left');
		$this->db->order_by('installed_location','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Tech_support/update_installed_location_status/".$row-> 	location_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Tech_support/update_installed_location_status/".$row-> 	location_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Tech_support/edit_installed_location/".$row->location_id."'><i class='fa fa-pencil'></i></a>";	
				
			$installed_loc_data[] = array('sr_no'=>$i,
			'business_loc'=>$row->company_name." ".$row->city_name,
			'location'=>$row->installed_location,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($installed_loc_data),
			"iTotalDisplayRecords" => count($installed_loc_data),
			"aaData"=>$installed_loc_data);
			
		echo json_encode($results);
	}
	
	public function update_installed_location_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "location_id";
		$table = "installed_location";
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
			redirect('Tech_support/location');
		}

	public function edit_installed_location(){
		$this->load->view('techsupport/edit_location');
		
	}	
	
	public function update_installed_location()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('business_loc', 'Business Location', 'required|trim');
		$this->form_validation->set_rules('location', 'Location', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('techsupport/edit_location');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "installed_location";
		$query = $this->db->select('business_loc_id, installed_location')->from('installed_location')->where('business_loc_id',$this->input->post('business_loc'))->where('installed_location',$this->input->post('location'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','Sorry! This record already exist.');
			redirect(page_url.'Tech_support/location');
			
		}else{
		
		
			$data = array('business_loc_id'=>$this->input->post('business_loc'),
			'installed_location'=>$this->input->post('location'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		$this->db->where('location_id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'Tech_support/location');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tech_support/location');
		}
		}
		
	}
		
	}
	
	public function items()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('location', 'Location', 'required|trim');
		$this->form_validation->set_rules('item_name', 'Item Name', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('techsupport/items');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "tech_items";
		$query = $this->db->select('business_loc_id,location_id, item_name, unique_id')->from('tech_items')->where('business_loc_id',$this->input->post('business_loc'))->where('location_id',$this->input->post('location'))->where('item_name',$this->input->post('item_name'))->where('unique_id',$this->input->post('unique_id'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-warning" style="color:#fff;">Sorry! This record already exist.</div>');
			redirect(page_url.'Tech_support/items');
			
		}else{
		
		
			$data = array('location_id'=>$this->input->post('location'),
			'business_loc_id'=>$this->input->post('business_loc'),
			'user_id'=>$this->input->post('user_name'),
			'item_name'=>$this->input->post('item_name'),
			'unique_id'=>$this->input->post('unique_id'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully added.</div>');
			redirect(page_url.'Tech_support/items');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tech_support/items');
		}
		}
		
	}
		
	}
	public function tech_item_list()
	{
		$i=1;
		$tech_item_data = array();
		$this->db->select('a.*,b.location_id, b.installed_location,d.business_loc_id, d.company_name, e.user_id, e.first_name, e.last_name')->from('tech_items a')->join('installed_location b','a.location_id=b.location_id','left')->join('business_location d','a.business_loc_id=d.business_loc_id','left')->join('system_users e','e.user_id=a.user_id','left');
		$this->db->order_by('a.item_name','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Tech_support/update_tech_item_status/".$row->item_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Tech_support/update_tech_item_status/".$row->item_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Tech_support/edit_tech_items/".$row->item_id."'><i class='fa fa-pencil'></i></a>";	
				
			$tech_item_data[] = array('sr_no'=>$i,
			'business_location'=>$row->company_name,
			'location'=>$row->installed_location,
			'item_name'=>$row->item_name,
			'unique_id'=>$row->unique_id,
			'user'=>$row->first_name." ".$row->last_name,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($tech_item_data),
			"iTotalDisplayRecords" => count($tech_item_data),
			"aaData"=>$tech_item_data);
			
		echo json_encode($results);
	}
	
	public function update_tech_item_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "item_id";
		$table = "tech_items";
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
			redirect('Tech_support/items');
		}

	public function edit_tech_items(){
		$this->load->view('techsupport/edit_items');
	}	
	
	public function update_tech_items()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('location', 'Location', 'required|trim');
		$this->form_validation->set_rules('item_name', 'Product Sub Category', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('techsupport/edit_items');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "tech_items";
		$query = $this->db->select('location_id, item_name, unique_id')->from('tech_items')->where('business_loc_id',$this->input->post('business_loc'))->where('location_id',$this->input->post('location'))->where('item_name',$this->input->post('item_name'))->where('user_id',$this->input->post('user_name'))->where('unique_id',$this->input->post('unique_id'))->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-warning" style="color:#fff;">Sorry! This record already exist.</div>');
			redirect(page_url.'Tech_support/items');
			
		}else{
		
		
			$data = array('location_id'=>$this->input->post('location'),
			'business_loc_id'=>$this->input->post('business_loc'),
			'user_id'=>$this->input->post('user_name'),
			'item_name'=>$this->input->post('item_name'),
			'unique_id'=>$this->input->post('unique_id'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
			$this->db->where('item_id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully added.</div>');
			redirect(page_url.'Tech_support/items');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry,technical error accure.</div><br/>');
		redirect(page_url.'Tech_support/items');
		}
		}
		
	}
		
	}
	
	public function select_installed_location()
	{
	echo "<option value=''>--Select Installed Location--</option>";
	$business_location = $this->input->post('business_loc');
		$query = $this->db->select('location_id, business_loc_id, installed_location')->from('installed_location')->where('business_loc_id',$business_location)->get();
		
			foreach($query->result() as $location)
			{
				echo "<option value=".$location->location_id.">".$location->installed_location."</option>";
				}
		
		}
		
	public function select_department_items()
	{
	echo "<option value=''>--Select Item--</option>";
	$location = $this->input->post('location');
		$query = $this->db->select('item_id, location_id,item_name,status')->from('tech_items')->where('location_id',$location)->where('status','1')->get();
		
			foreach($query->result() as $item)
			{
				echo "<option value=".$item->item_id.">".$item->item_name."</option>";
				}
		
		}
		
	public function raise_ticket()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('location', 'Location', 'required|trim');
		$this->form_validation->set_rules('item_name', 'Item Name', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('techsupport/raise_ticket');
		}
		else
		{
		$rand_date = date('y-m-d');
		$rand = (rand(1,100));
		$ticket_id = "PGIT-".$rand_date."-".$rand;
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "tech_support_ticket";
		$photo=$_FILES['screen_shot']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$screenshot=time().'.'.$cat_image;
				move_uploaded_file($_FILES["screen_shot"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/image_bank/techsupport/' . $screenshot);
			}else
			{
				$screenshot="";
				}	
		
			$data = array('location_id'=>$this->input->post('location'),
			'business_loc_id'=>$this->input->post('business_loc'),
			'item_name'=>$this->input->post('item_name'),
			'remarks'=>$this->input->post('remarks'),
			'screenshot'=>$screenshot,
			'priority'=>$this->input->post('priority'),
			'ticket'=>$ticket_id,
			'status'=>'0',
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully added.</div>');
			redirect(page_url.'Tech_support');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tech_support/items');
		}
		
		
	}
		
	}
	public function raised_ticket_list()
	{
		$i=1;
		$support_ticket = array();
		$this->db->select('a.*,b.location_id, b.installed_location, c.user_id, c.first_name, c.last_name,d.user_id, d.first_name as closed_by_fname, d.last_name as closed_by_lname, e.item_id, e.item_name as items')->from('tech_support_ticket a')->join('installed_location b','a.location_id=b.location_id','left')->join('system_users c','a.added_by=c.user_id','left')->join('system_users d','a.updated_by=d.user_id','left')->join('tech_items e','a.item_name=e.item_id','left');
		$this->db->order_by('a.added_on','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				//$sta = "<a href='".page_url."Tech_support/update_tech_item_status/".$row->ticket_id."/".$row->status."'><span class='btn btn-success btn-xs'>Closed</span></a>";
				$status = "<span class='btn btn-success btn-xs'>Closed</span>";
				$updateddate = date('Y-m-d', strtotime($row->updated_on));
				$time = date('H:i:s', strtotime($row->updated_on));
				$updatedtime = "<br>". date('g:i A', strtotime($time)); 
				$sta = $status."<br>"."This ticket has been closed by <strong>".$row->closed_by_fname." ".$row->closed_by_lname."</strong><br> at ".$updateddate." ".$updatedtime;
				
			}else
			{
				$sta ="<a href='".page_url."Tech_support/update_ticket_status/".$row->ticket_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Pending</span></a>";
			}
			$history = "<a href='".page_url."Tech_support/view_history/".$row->ticket_id."'><span class='btn btn-warning btn-xs'><i class='fa fa-eye'></i>&nbsp; History/ Updates</span></a>";
			$edit = "<a href='".page_url."Tech_support/edit_raised_ticket/".$row->ticket_id."'><i class='fa fa-pencil'></i></a>";	
			if($row->screenshot){
			$screenshot = "<a href='".techpath.$row->screenshot."' target='_blank'><i class='fa fa-file-image-o' aria-hidden='true' style='text-align:center; font-size:30px'></i></a>";
			}else{
				$screenshot="";
			}
			$addeddate = date('Y-m-d', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 	
			$support_ticket[] = array('sr_no'=>$i,
			'location'=>$row->installed_location,
			'ticket'=>$row->ticket,
			'item_name'=>$row->items,
			'remarks'=>$row->remarks,
			'screenshot'=>$screenshot,
			'priority'=>$row->priority,
			'status'=>$sta,
			'added_on'=>$addeddate."".$addedtime,
			'added_by'=>$row->first_name." ".$row->last_name,
			'history'=>$history,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($support_ticket),
			"iTotalDisplayRecords" => count($support_ticket),
			"aaData"=>$support_ticket);
			
		echo json_encode($results);
	}
	
	public function update_ticket_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "ticket_id";
		$table = "tech_support_ticket";
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status,'updated_on'=>$date,'updated_by'=>$user_id);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', '<div class="alert alert-danger" style="color:#fff;">Status successfully updated.</div>');
			redirect('Tech_support/');
		}

	public function edit_raised_ticket(){
		$this->load->view('techsupport/edit_ticket');
	}	
	
	public function update_ticket_detail()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('location', 'Location', 'required|trim');
		$this->form_validation->set_rules('item_name', 'Product Sub Category', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('techsupport/edit_ticket');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "tech_support_ticket";
		$photo=$_FILES['screen_shot']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$screenshot=time().'.'.$cat_image;
				move_uploaded_file($_FILES["screen_shot"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/image_bank/techsupport/' . $screenshot);
			}else
			{
				$screenshot=$this->input->post('old_img');
				}	
		
			$data = array('location_id'=>$this->input->post('location'),
			'business_loc_id'=>$this->input->post('business_loc'),
			'item_name'=>$this->input->post('item_name'),
			'remarks'=>$this->input->post('remarks'),
			'screenshot'=>$screenshot,
			'priority'=>$this->input->post('priority'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			$this->db->where('ticket_id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully updated.</div>');
			redirect(page_url.'Tech_support/');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry,technical error accure.</div><br/>');
		redirect(page_url.'Tech_support/');
		}
		
		
	}
		
	}
  public function view_history(){
	  $this->load->view('techsupport/ticket_history');
  }		
  public function raised_ticket_detail()
	{
		$i=1;
		$support_ticket = array();
		$this->db->select('a.*,b.location_id, b.installed_location, c.user_id, c.first_name, c.last_name,d.user_id, d.first_name as closed_by_fname, d.last_name as closed_by_lname, e.item_id, e.item_name as items')->from('tech_support_ticket a')->join('installed_location b','a.location_id=b.location_id','left')->join('system_users c','a.added_by=c.user_id','left')->join('system_users d','a.updated_by=d.user_id','left')->join('tech_items e','a.item_name=e.item_id','left')->where('a.ticket_id',$this->uri->segment(3));
		$this->db->order_by('a.added_on','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				//$sta = "<a href='".page_url."Tech_support/update_tech_item_status/".$row->ticket_id."/".$row->status."'><span class='btn btn-success btn-xs'>Closed</span></a>";
				$status = "<span class='btn btn-success btn-xs'>Closed</span>";
				$updateddate = date('Y-m-d', strtotime($row->updated_on));
				$time = date('H:i:s', strtotime($row->updated_on));
				$updatedtime = "<br>". date('g:i A', strtotime($time)); 
				$sta = $status."<br>"."This ticket has been closed by <strong>".$row->closed_by_fname." ".$row->closed_by_lname."</strong><br> at ".$updateddate." ".$updatedtime;
				
			}else
			{
				$sta ="<a href='".page_url."Tech_support/update_ticket_status/".$row->ticket_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Pending</span></a>";
			}
			
			$edit = "<a href='".page_url."Tech_support/edit_tech_items/".$row->ticket_id."'><i class='fa fa-pencil'></i></a>";	
			$addeddate = date('Y-m-d', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 	
			$support_ticket[] = array('sr_no'=>$i,
			'location'=>$row->installed_location,
			'item_name'=>$row->items,
			'remarks'=>$row->remarks,
			'status'=>$sta,
			'added_on'=>$addeddate."".$addedtime,
			'added_by'=>$row->first_name." ".$row->last_name);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($support_ticket),
			"iTotalDisplayRecords" => count($support_ticket),
			"aaData"=>$support_ticket);
			
		echo json_encode($results);
	}
	
	public function update_progress()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('remarks', 'Remarks', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('techsupport/ticket_history');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "tech_support_ticket_progress";
			$data = array('ticket_id'=>$this->uri->segment(3),
			'remarks'=>$this->input->post('remarks'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Tech_support/view_history/'.$this->uri->segment(3));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tech_support/view_history'.$this->uri->segment(3));
		}
	}
	}
	
	public function tech_faqs()
	{
		$this->load->view('techsupport/faqs');
	}
	
	public function add_faqs()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('question', 'Question', 'required|trim');
		$this->form_validation->set_rules('answer', 'Answer', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('techsupport/faqs');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "tech_faqs";
		
			$data = array('question'=>$this->input->post('question'),
			'answer'=>$this->input->post('answer'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Tech_support/tech_faqs');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tech_support/tech_faqs');
		}
		
		
	}
		
	}
	public function tech_faqs_list()
	{
		$i=1;
		$faq_data = array();
		$this->db->select('*')->from('tech_faqs a');
		$this->db->order_by('id','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			
			$edit = "<a href='".page_url."Tech_support/edit_faqs/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			$updateddate = date('Y-m-d', strtotime($row->added_on));
				$time = date('H:i:s', strtotime($row->added_on));
				$updatedtime = "<br>". date('g:i A', strtotime($time));	
			$faq_data[] = array('sr_no'=>$i,
			'question'=>$row->question,
			'answer'=>$row->answer,
			'added_on'=>$updateddate."".$updatedtime,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($faq_data),
			"iTotalDisplayRecords" => count($faq_data),
			"aaData"=>$faq_data);
			
		echo json_encode($results);
	}
	
	

	public function edit_faqs(){
		$this->load->view('techsupport/edit_faqs');
		
	}	
	
	public function update_faqs()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('question', 'Question', 'required|trim');
		$this->form_validation->set_rules('answer', 'Answer', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('techsupport/edit_faqs');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "tech_faqs";
			$data = array('question'=>$this->input->post('question'),
			'answer'=>$this->input->post('answer'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		$this->db->where('id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'Tech_support/tech_faqs');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tech_support/tech_faqs');
		}
		
		
	}
		
	}
}
