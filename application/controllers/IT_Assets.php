<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class IT_Assets extends CI_Controller {
	
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
		if($user_id=='66' || $user_id=='67' || $user_id=='1'){
			
		}else{ 
		$ip = $_SERVER["REMOTE_ADDR"];
		 $query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
            if($query->num_rows()=='0'){
            $this->session->set_flashdata('message','<span class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</span><br/>');
            redirect(page_url.'Dashboard');    
            }
		}
	}

	public function index()
	{
		$this->load->view('it_assets/add_assets');
	}
	
	
	public function add_items()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('business_loc', 'Business Location', 'required|trim');
		$this->form_validation->set_rules('location', 'location', 'required|trim');
		$this->form_validation->set_rules('asset_type', 'Asset Type', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('it_assets/add_assets');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "presto_it_assets";
		$photo=$_FILES['attachment']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$screenshot=time().'.'.$cat_image;
				move_uploaded_file($_FILES["attachment"]["tmp_name"],UPLOADPATH.'itassets/' . $screenshot);
			}else
			{
				$screenshot="";
				}	
			$data = array('business_location_id'=>$this->input->post('business_loc'),
			'department_id'=>$this->input->post('location'),
			'antivirus_key'=>$this->input->post('antivirus_key'),
			'it_asset_name'=>$this->input->post('it_asset_name'),
			'asset_type'=>$this->input->post('asset_type'),
			'battery_purchase_date'=>$this->input->post('battery_purchase_date'),
			'battery_warrenty_expire'=>$this->input->post('battery_warranty_expr'),
			'brand_id'=>$this->input->post('brand_name'),
			'amc_purchase_date'=>$this->input->post('amc_purchase_date'),
			'computer_name'=>$this->input->post('computer_name'),
			'customer_care'=>$this->input->post('customer_care'),
			'harddisk_drive'=>$this->input->post('harddisk_drive'),
			'ip_aadress'=>$this->input->post('ip_address'),
			'invoice_attached'=>$this->input->post('invoice_attached'),
			'issue'=>$this->input->post('issue'),
			'keyboard'=>$this->input->post('keyboard'),
			'license_number'=>$this->input->post('license_no'),
			'login_password'=>$this->input->post('login_password'),
			'login_user'=>$this->input->post('login_user'),
			'ms_office'=>$this->input->post('ms_office'),
			'ms_office_license_type'=>$this->input->post('ms_office_license_type'),
			'window_license_type'=>$this->input->post('window_license_type'),
			'model_number'=>$this->input->post('model_no'),
			'monitor_tft_sr_number'=>$this->input->post('monitor_tft_sr_no'),
			'mouse'=>$this->input->post('mouse'),
			'operating_system'=>$this->input->post('operating_system'),
			'operating_sys_type'=>$this->input->post('operating_system_type'),
			'part_number'=>$this->input->post('part_no'),
			'processor'=>$this->input->post('processor'),
			'purchase_date'=>$this->input->post('purchase_date'),
			'ram'=>$this->input->post('ram'),
			'relationship_acc_no'=>$this->input->post('relationship_acc_no'),
			'renewal_to_be_done'=>$this->input->post('renewal_done'),
			'screen'=>$this->input->post('screen'),
			'serial_number'=>$this->input->post('serial_number'),
			'service_tag'=>$this->input->post('service_tag'),
			'software_key'=>$this->input->post('software_key'),
			'telephone_number'=>$this->input->post('telephone'),
			'user_name'=>$this->input->post('user_name'),
			'vendor_name'=>$this->input->post('vendor_name'),
			'warranty_end_date'=>$this->input->post('warranty_end_date'),
			'website'=>$this->input->post('website'),
			'assigned_to'=>$this->input->post('assigned_to'),
			'attachment_file'=>$screenshot,
			'available_in_stock'=>$this->input->post('available_status'),
			'assets_value'=>$this->input->post('asset_value'),
			'status'=>'1',
			'added_on'=>$date,
			'added_by'=>$user_id,
			'it_remarks'=>$this->input->post('it_remarks'));
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully added.</div>');
			redirect(page_url.'IT_Assets');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'IT_Assets');
		}
	
		
	}
		
	}
	public function IT_item_list()
	{
		$i=1;
		$tech_item_data = array();
	$this->db->select('a.*,b.location_id, b.installed_location,d.business_loc_id, d.city_id, d.company_name, e.user_id, e.first_name, e.last_name,c.type_id,c.asset_type as asset_name,f.brand_id,f.brand_name,g.city_id, g.city_name, s.user_id, s.first_name as assigned_person_fname, s.last_name as assigned_person_lname, ms.ms_office_id, ms.ms_office, msl.ms_office_id,msl.ms_office_licence_type,msw.ms_office_id, msw.ms_office_licence_type as ms_office_window_licence_type, o.operationg_id, o.operating_system')->from('presto_it_assets a')->join('installed_location b','a.department_id=b.location_id','left')->join('business_location d','a.business_location_id=d.business_loc_id','left')->join('asset_type c','a.asset_type=c.type_id','left')->join('system_users e','e.user_id=a.added_by','left')->join('asset_brands f','a.brand_id=f.brand_id','left')->join('cities g','d.city_id=g.city_id','left');
		$this->db->join('system_users s','a.assigned_to=s.user_id','left');
		$this->db->join('asset_microsift_office ms','a.ms_office=ms.ms_office_id','left');
		$this->db->join('asset_microsift_office_licence_type msl','a.ms_office_license_type=msl. 	ms_office_id','left');
		$this->db->join('asset_microsift_windows_licence_type msw','a.window_license_type=msw. 	ms_office_id','left');
		$this->db->join('asset_operating_system o','a.operating_system=o.operationg_id','left');
		$this->db->order_by('c.asset_type','asc');
		$query = $this->db->get();
		$res = $query->result();
		
		foreach($res as $row){
			$stock = $row->available_in_stock; 
			if($stock=='1'){
			    $stockvalue = "Yes";
			}else{
			    $stockvalue = "No";
			}
			
			$edit = "<a href='".page_url."IT_Assets/edit_item/".$row->asset_id."'><i class='fa fa-pencil'></i></a>";
			$delete = "<a href='".page_url."IT_Assets/delete_it_item/".$row->asset_id."'><i class='fa fa-trash' style='color:red;'></i></a>";
			$view = "<a href='".page_url."IT_Assets/view_item_detail/".$row->asset_id."'><i class='fa fa-eye'></i></a>";			
			$tech_item_data[] = array('sr_no'=>$i,
			'assigned_to'=>$row->assigned_person_fname." ".$row->assigned_person_lname,
			'it_asset_name'=>$row->it_asset_name,
			'asset_type'=>$row->asset_name,
			'user_name'=>$row->user_name,
			'brand_name'=>$row->brand_name,
			'model_number'=>$row->model_number,
			'serial_number'=>$row->serial_number,
			'issue'=>$row->issue,
			'business_location'=>$row->company_name."<br>".$row->city_name,
			'location'=>$row->installed_location,
			'purchase_date'=>$row->purchase_date,
			'warranty_end_date'=>$row->warranty_end_date,
			'ram'=>$row->ram,
			'harddisk_drive'=>$row->harddisk_drive,
			'part_number'=>$row->part_number,
			'website'=>$row->website,
			'amc_purchase_date'=>$row->amc_purchase_date,
			'customer_care'=>$row->customer_care,
			'processor'=>$row->processor,
			'ms_office'=>$row->ms_office,
			'ms_office_licence_type'=>$row->ms_office_licence_type,
			'operating_system'=>$row->operating_system,
			'ms_office_window_licence_type'=>$row->ms_office_window_licence_type,
			'vendor_name'=>$row->vendor_name,
			'monitor_tft_sr_number'=>$row->monitor_tft_sr_number,
			'battery_purchase_date'=>$row->battery_purchase_date,
			'antivirus_key'=>$row->antivirus_key,
			'relationship_acc_no'=>$row->relationship_acc_no,
			'telephone_number'=>$row->telephone_number,
			'software_key'=>$row->software_key,
			'license_number'=>$row->license_number,
			'login_user'=>$row->login_user,
			'login_password'=>$row->login_password,
			'keyboard'=>$row->keyboard,
			'mouse'=>$row->mouse,
			'screen'=>$row->screen,
			'ip_aadress'=>$row->ip_aadress,
			'service_tag'=>$row->service_tag,
			'renewal_to_be_done'=>$row->renewal_to_be_done,
			'computer_name'=>$row->computer_name,
			'invoice_attached'=>$row->invoice_attached,
			'it_remarks'=>$row->it_remarks,
			'available_in_stock'=>$stockvalue,
			'edit'=>$edit." &nbsp;  | ".$view." &nbsp; | <br><br>".$delete);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($tech_item_data),
			"iTotalDisplayRecords" => count($tech_item_data),
			"aaData"=>$tech_item_data);
			
		echo json_encode($results);
	}
	
	function view_item_detail(){
		$this->load->view('it_assets/view_detail');
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

	public function edit_item(){
		$this->load->view('it_assets/edit_item');
	}	
	
	public function update_item_detail()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('business_loc', 'Business Location', 'required|trim');
		$this->form_validation->set_rules('location', 'location', 'required|trim');
		
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('it_assets/edit_item');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "presto_it_assets";
		$old_image = $this->input->post('old_image');
			$photo=$_FILES['attachment']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$screenshot=time().'.'.$cat_image;
				move_uploaded_file($_FILES["attachment"]["tmp_name"],UPLOADPATH.'itassets/' . $screenshot);
			}else
			{
				$screenshot=$old_image;
				}	
				
		
			$data = array('business_location_id'=>$this->input->post('business_loc'),
			'department_id'=>$this->input->post('location'),
			'antivirus_key'=>$this->input->post('antivirus_key'),
			'it_asset_name'=>$this->input->post('it_asset_name'),
			'asset_type'=>$this->input->post('asset_type'),
			'battery_purchase_date'=>$this->input->post('battery_purchase_date'),
			'battery_warrenty_expire'=>$this->input->post('battery_warranty_expr'),
			'brand_id'=>$this->input->post('brand_name'),
			'amc_purchase_date'=>$this->input->post('amc_purchase_date'),
			'computer_name'=>$this->input->post('computer_name'),
			'customer_care'=>$this->input->post('customer_care'),
			'harddisk_drive'=>$this->input->post('harddisk_drive'),
			'ip_aadress'=>$this->input->post('ip_address'),
			'invoice_attached'=>$this->input->post('invoice_attached'),
			'issue'=>$this->input->post('issue'),
			'keyboard'=>$this->input->post('keyboard'),
			'license_number'=>$this->input->post('license_no'),
			'login_password'=>$this->input->post('login_password'),
			'login_user'=>$this->input->post('login_user'),
			'ms_office'=>$this->input->post('ms_office'),
			'ms_office_license_type'=>$this->input->post('ms_office_license_type'),
			'window_license_type'=>$this->input->post('window_license_type'),
			'model_number'=>$this->input->post('model_no'),
			'monitor_tft_sr_number'=>$this->input->post('monitor_tft_sr_no'),
			'mouse'=>$this->input->post('mouse'),
			'operating_system'=>$this->input->post('operating_system'),
			'operating_sys_type'=>$this->input->post('operating_system_type'),
			'part_number'=>$this->input->post('part_no'),
			'processor'=>$this->input->post('processor'),
			'purchase_date'=>$this->input->post('purchase_date'),
			'ram'=>$this->input->post('ram'),
			'relationship_acc_no'=>$this->input->post('relationship_acc_no'),
			'renewal_to_be_done'=>$this->input->post('renewal_done'),
			'screen'=>$this->input->post('screen'),
			'serial_number'=>$this->input->post('serial_number'),
			'service_tag'=>$this->input->post('service_tag'),
			'software_key'=>$this->input->post('software_key'),
			'telephone_number'=>$this->input->post('telephone'),
			'user_name'=>$this->input->post('user_name'),
			'vendor_name'=>$this->input->post('vendor_name'),
			'warranty_end_date'=>$this->input->post('warranty_end_date'),
			'website'=>$this->input->post('website'),
			'assigned_to'=>$this->input->post('assigned_to'),
			'attachment_file'=>$screenshot,
			'available_in_stock'=>$this->input->post('available_status'),
			'status'=>'1',
			'added_on'=>$date,
			'added_by'=>$user_id,
			'assets_value'=>$this->input->post('asset_value'),
			'it_remarks'=>$this->input->post('it_remarks'));
			
			
		$this->db->where('asset_id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
		$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#fff">Thank you, record successfully updated.</span></div>');
		redirect(page_url.'IT_Assets');
			
		}else
		{
		$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry,technical error accure.</div><br/>');
		redirect(page_url.'IT_Assets');
		}
	
		
	}
		
	}

public function brand()
	{
		$this->load->view('it_assets/brand');
	}
	
	public function add_brand()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('brand_name', 'Brand', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('it_assets/brand');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "asset_brands";
		$query = $this->db->select('brand_name')->from('asset_brands')->where('brand_name',$this->input->post('brand_name'))->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		if($res){
			$this->session->set_flashdata('message','Sorry! This record already exist.');
			redirect(page_url.'IT_Assets/brand');
			
		}else{
		
		
			$data = array(
			'brand_name'=>$this->input->post('brand_name'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'IT_Assets/brand');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'IT_Assets/brand');
		}
		}
		
	}
		
	}
	public function brand_list()
	{
		$i=1;
		$brand_data = array();
		$this->db->select('*')->from('asset_brands');
		$this->db->order_by('brand_name','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."IT_Assets/update_brand_status/".$row->brand_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."IT_Assets/update_brand_status/".$row->brand_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."IT_Assets/edit_brand/".$row->brand_id."'><i class='fa fa-pencil'></i></a>";	
				
			$brand_data[] = array('sr_no'=>$i,
			'brand_name'=>$row->brand_name,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($brand_data),
			"iTotalDisplayRecords" => count($brand_data),
			"aaData"=>$brand_data);
			
		echo json_encode($results);
	}
	
	public function update_brand_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "brand_id";
		$table = "asset_brands";
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
			redirect('IT_Assets/brand');
		}

	public function edit_brand(){
		$this->load->view('it_assets/edit_brand');
		
	}	
	
	public function update_brand()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('brand_name', 'Brand Name', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('it_assets/edit_brand');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "asset_brands";
		$query = $this->db->select('brand_name')->from('asset_brands')->where('brand_name',$this->input->post('brand_name'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','Sorry! This record already exist.');
			redirect(page_url.'IT_Assets/brand');
			
		}else{
		
		
			$data = array(
			'brand_name'=>$this->input->post('brand_name'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		$this->db->where('brand_id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'IT_Assets/brand');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'IT_Assets/brand');
		}
		}
		
	}
		
	}

public function microsoft_office()
	{
		$this->load->view('it_assets/microsoft_office');
	}
	
	public function add_microsoft_office()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('microsoft_office', 'Microsoft Office', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('it_assets/microsoft_office');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "asset_microsift_office";
		$query = $this->db->select('ms_office')->from('asset_microsift_office')->where('ms_office',$this->input->post('microsoft_office'))->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		if($res){
			$this->session->set_flashdata('message','Sorry! This record already exist.');
			redirect(page_url.'IT_Assets/microsoft_office');
			
		}else{
		
		
			$data = array(
			'ms_office'=>$this->input->post('microsoft_office'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'IT_Assets/microsoft_office');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'IT_Assets/microsoft_office');
		}
		}
		
	}
		
	}
	public function microsoft_office_list()
	{
		$i=1;
		$ms_office_data = array();
		$this->db->select('*')->from('asset_microsift_office');
		$this->db->order_by('ms_office','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."IT_Assets/update_ms_office_status/".$row->ms_office_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."IT_Assets/update_ms_office_status/".$row->ms_office_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."IT_Assets/edit_microsoft_office/".$row->ms_office_id."'><i class='fa fa-pencil'></i></a>";	
				
			$ms_office_data[] = array('sr_no'=>$i,
			'ms_office'=>$row->ms_office,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($ms_office_data),
			"iTotalDisplayRecords" => count($ms_office_data),
			"aaData"=>$ms_office_data);
			
		echo json_encode($results);
	}
	
	public function update_ms_office_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "ms_office_id";
		$table = "asset_microsift_office";
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
			redirect('IT_Assets/microsoft_office');
		}

	public function edit_microsoft_office(){
		$this->load->view('it_assets/edit_microsoft_office');
		
	}	
	
	public function update_microsoft_office()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('microsoft_office', 'MS Office', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('it_assets/edit_microsoft_office');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "asset_microsift_office";
		$query = $this->db->select('ms_office')->from('asset_microsift_office')->where('ms_office',$this->input->post('microsoft_office'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','Sorry! This record already exist.');
			redirect(page_url.'IT_Assets/microsoft_office');
			
		}else{
		
		
			$data = array(
			'ms_office'=>$this->input->post('microsoft_office'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		$this->db->where('ms_office_id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'IT_Assets/microsoft_office');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'IT_Assets/microsoft_office');
		}
		}
		
	}
		
	}	

	public function ms_office_license()
	{
		$this->load->view('it_assets/microsoft_office_license');
	}
	
	public function add_ms_office_license()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('office_license', 'Microsoft Office License', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('it_assets/microsoft_office_license');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "asset_microsift_office_licence_type";
		$query = $this->db->select('ms_office_licence_type')->from('asset_microsift_office_licence_type')->where('ms_office_licence_type',$this->input->post('office_license'))->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		if($res){
			$this->session->set_flashdata('message','Sorry! This record already exist.');
			redirect(page_url.'IT_Assets/ms_office_license');
			
		}else{
		
		
			$data = array(
			'ms_office_licence_type'=>$this->input->post('office_license'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'IT_Assets/ms_office_license');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'IT_Assets/ms_office_license');
		}
		}
		
	}
		
	}
	public function ms_office_license_list()
	{
		$i=1;
		$ms_office_data = array();
		$this->db->select('*')->from('asset_microsift_office_licence_type');
		$this->db->order_by('ms_office_licence_type','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."IT_Assets/update_ms_office_license_status/".$row->ms_office_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."IT_Assets/update_ms_office_license_status/".$row->ms_office_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."IT_Assets/edit_ms_office_license/".$row->ms_office_id."'><i class='fa fa-pencil'></i></a>";	
				
			$ms_office_data[] = array('sr_no'=>$i,
			'ms_office'=>$row->ms_office_licence_type,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($ms_office_data),
			"iTotalDisplayRecords" => count($ms_office_data),
			"aaData"=>$ms_office_data);
			
		echo json_encode($results);
	}
	
	public function update_ms_office_license_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "ms_office_id";
		$table = "asset_microsift_office_licence_type";
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
			redirect('IT_Assets/ms_office_license');
		}

	public function edit_ms_office_license(){
		$this->load->view('it_assets/edit_microsoft_office_license');
		
	}	
	
	public function update_ms_office_license()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('office_license', 'MS Office', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('it_assets/edit_microsoft_office_license');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "asset_microsift_office_licence_type";
		$query = $this->db->select('ms_office_licence_type')->from('asset_microsift_office_licence_type')->where('ms_office_licence_type',$this->input->post('office_license'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','Sorry! This record already exist.');
			redirect(page_url.'IT_Assets/ms_office_license');
			
		}else{
		
		
			$data = array(
			'ms_office_licence_type'=>$this->input->post('office_license'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		$this->db->where('ms_office_id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'IT_Assets/ms_office_license');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'IT_Assets/ms_office_license');
		}
		}
		
	}
		
	}	
	
	public function window_license_type()
	{
		$this->load->view('it_assets/windows_license');
	}
	
	public function add_window_license_type()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('window_license_type', 'Window License', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('it_assets/windows_license');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "asset_microsift_windows_licence_type";
		$query = $this->db->select('ms_office_licence_type')->from('asset_microsift_windows_licence_type')->where('ms_office_licence_type',$this->input->post('window_license_type'))->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		if($res){
			$this->session->set_flashdata('message','Sorry! This record already exist.');
			redirect(page_url.'IT_Assets/window_license_type');
			
		}else{
		
		
			$data = array(
			'ms_office_licence_type'=>$this->input->post('window_license_type'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'IT_Assets/window_license_type');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'IT_Assets/window_license_type');
		}
		}
		
	}
		
	}
	public function window_license_type_list()
	{
		$i=1;
		$ms_office_data = array();
		$this->db->select('*')->from('asset_microsift_windows_licence_type');
		$this->db->order_by('ms_office_licence_type','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."IT_Assets/update_window_license_type_status/".$row->ms_office_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."IT_Assets/update_window_license_type_status/".$row->ms_office_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."IT_Assets/edit_window_license_type/".$row->ms_office_id."'><i class='fa fa-pencil'></i></a>";	
				
			$ms_office_data[] = array('sr_no'=>$i,
			'ms_office'=>$row->ms_office_licence_type,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($ms_office_data),
			"iTotalDisplayRecords" => count($ms_office_data),
			"aaData"=>$ms_office_data);
			
		echo json_encode($results);
	}
	
	public function update_window_license_type_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "ms_office_id";
		$table = "asset_microsift_windows_licence_type";
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
			redirect('IT_Assets/window_license_type');
		}

	public function edit_window_license_type(){
		$this->load->view('it_assets/edit_windows_license');
		
	}	
	
	public function update_window_license_type()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('window_license_type', 'MS Office', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('it_assets/edit_windows_license');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "asset_microsift_windows_licence_type";
		$query = $this->db->select('ms_office_licence_type')->from('asset_microsift_windows_licence_type')->where('ms_office_licence_type',$this->input->post('window_license_type'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','Sorry! This record already exist.');
			redirect(page_url.'IT_Assets/window_license_type');
			
		}else{
		
		
			$data = array(
			'ms_office_licence_type'=>$this->input->post('window_license_type'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		$this->db->where('ms_office_id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'IT_Assets/window_license_type');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'IT_Assets/window_license_type');
		}
		}
		
	}
		
	}

public function operating_system()
	{
		$this->load->view('it_assets/operating_system');
	}
	
	public function add_operating_system()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('operating_system', 'Operating System', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('it_assets/operating_system');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "asset_operating_system";
		$query = $this->db->select('operating_system')->from('asset_operating_system')->where('operating_system',$this->input->post('operating_system'))->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		if($res){
			$this->session->set_flashdata('message','Sorry! This record already exist.');
			redirect(page_url.'IT_Assets/operating_system');
			
		}else{
		
		
			$data = array(
			'operating_system'=>$this->input->post('operating_system'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'IT_Assets/operating_system');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'IT_Assets/operating_system');
		}
		}
		
	}
		
	}
	public function operating_system_list()
	{
		$i=1;
		$ms_office_data = array();
		$this->db->select('*')->from('asset_operating_system');
		$this->db->order_by('operating_system','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."IT_Assets/update_operating_system_status/".$row->operationg_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."IT_Assets/update_operating_system_status/".$row->operationg_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."IT_Assets/edit_operating_system/".$row->operationg_id."'><i class='fa fa-pencil'></i></a>";	
				
			$ms_office_data[] = array('sr_no'=>$i,
			'operating_system'=>$row->operating_system,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($ms_office_data),
			"iTotalDisplayRecords" => count($ms_office_data),
			"aaData"=>$ms_office_data);
			
		echo json_encode($results);
	}
	
	public function update_operating_system_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "operationg_id";
		$table = "asset_operating_system";
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
			redirect('IT_Assets/operating_system');
		}

	public function edit_operating_system(){
		$this->load->view('it_assets/edit_operating_system');
		
	}	
	
	public function update_operating_system()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('operating_system', 'Operating System', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('it_assets/edit_operating_system');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "asset_operating_system";
		$query = $this->db->select('operating_system')->from('asset_operating_system')->where('operating_system',$this->input->post('operating_system'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','Sorry! This record already exist.');
			redirect(page_url.'IT_Assets/operating_system');
			
		}else{
		
		
			$data = array(
			'operating_system'=>$this->input->post('operating_system'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		$this->db->where('operationg_id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'IT_Assets/operating_system');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'IT_Assets/operating_system');
		}
		}
		
	}
		
	}
	public function filter_by_type(){
		
		$assettype = $this->input->post('assettype');
		$businessloc = $this->input->post('businessloc');
		$department = $this->input->post('department');
		$ms_office_license_type = $this->input->post('ms_office_license_type');
		$window_license_type = $this->input->post('window_license_type');
		$available_status = $this->input->post('available_status');
		$this->db->select('a.*,b.location_id, b.installed_location,d.business_loc_id, d.city_id, d.company_name, e.user_id, e.first_name, e.last_name,c.type_id,c.asset_type as asset_name,f.brand_id,f.brand_name,g.city_id, g.city_name, s.user_id, s.first_name as assigned_person_fname, s.last_name as assigned_person_lname, ms.ms_office_id, ms.ms_office, msl.ms_office_id,msl.ms_office_licence_type,msw.ms_office_id, msw.ms_office_licence_type as ms_office_window_licence_type, o.operationg_id, o.operating_system')->from('presto_it_assets a')->join('installed_location b','a.department_id=b.location_id','left')->join('business_location d','a.business_location_id=d.business_loc_id','left')->join('asset_type c','a.asset_type=c.type_id','left')->join('system_users e','e.user_id=a.added_by','left')->join('asset_brands f','a.brand_id=f.brand_id','left')->join('cities g','d.city_id=g.city_id','left');
		$this->db->join('system_users s','a.assigned_to=s.user_id','left');
		$this->db->join('asset_microsift_office ms','a.ms_office=ms.ms_office_id','left');
		$this->db->join('asset_microsift_office_licence_type msl','a.ms_office_license_type=msl. 	ms_office_id','left');
		$this->db->join('asset_microsift_windows_licence_type msw','a.window_license_type=msw. 	ms_office_id','left');
		$this->db->join('asset_operating_system o','a.operating_system=o.operationg_id','left');
		if($assettype){
			$this->db->where('a.asset_type',$assettype);
		}
		if($department){
			$this->db->where('a.department_id',$department);
		}
		if($window_license_type){
			$this->db->where('a.window_license_type',$window_license_type);
		}
		if($ms_office_license_type){
			$this->db->where('a.ms_office_license_type',$ms_office_license_type);
		}
		if($available_status){
			$this->db->where('a.available_in_stock',$available_status);
		}
		$this->db->order_by('c.asset_type','asc');
		$query = $this->db->get();
		
		$data['resultdata'] = $query->result_array();
		$value  = array('asset_type'=>$assettype,
		'location'=>$department,
		'window_license_type'=>$window_license_type,
		'ms_office_license_type'=>$ms_office_license_type,
		'available_status'=>$available_status);
		$data['inputdata']= $value;
		$this->load->view('it_assets/filter_data',$data);
	
	}
	
	public function assets_by_type(){
		$this->load->view('it_assets/assets_by_type');
	}
	
	public function it_Asset_by_type(){
		
		
		$i=1;
		$tech_item_data = array();
		$this->db->select('a.*,b.location_id, b.installed_location,d.business_loc_id, d.city_id, d.company_name, e.user_id, e.first_name, e.last_name,c.type_id,c.asset_type as asset_name,f.brand_id,f.brand_name,g.city_id, g.city_name, s.user_id, s.first_name as assigned_person_fname, s.last_name as assigned_person_lname, ms.ms_office_id, ms.ms_office, msl.ms_office_id,msl.ms_office_licence_type,msw.ms_office_id, msw.ms_office_licence_type as ms_office_window_licence_type, o.operationg_id, o.operating_system')->from('presto_it_assets a')->join('installed_location b','a.department_id=b.location_id','left')->join('business_location d','a.business_location_id=d.business_loc_id','left')->join('asset_type c','a.asset_type=c.type_id','left')->join('system_users e','e.user_id=a.added_by','left')->join('asset_brands f','a.brand_id=f.brand_id','left')->join('cities g','d.city_id=g.city_id','left');
		$this->db->join('system_users s','a.assigned_to=s.user_id','left');
		$this->db->join('asset_microsift_office ms','a.ms_office=ms.ms_office_id','left');
		$this->db->join('asset_microsift_office_licence_type msl','a.ms_office_license_type=msl. 	ms_office_id','left');
		$this->db->join('asset_microsift_windows_licence_type msw','a.window_license_type=msw. 	ms_office_id','left');
		$this->db->join('asset_operating_system o','a.operating_system=o.operationg_id','left');
		$this->db->where('a.asset_type',$this->uri->segment(3));
		$this->db->order_by('c.asset_type','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
											
			$edit = "<a href='".page_url."IT_Assets/edit_item/".$row->asset_id."'><i class='fa fa-pencil'></i></a>";
			$view = "<a href='".page_url."IT_Assets/view_item_detail/".$row->asset_id."'><span class='btn btn-warning btn-xs'>View Detail</span></a>";			
			$tech_item_data[] = array('sr_no'=>$i,
			'assigned_to'=>$row->assigned_person_fname." ".$row->assigned_person_lname,
			'user_name'=>$row->user_name,
			'it_asset_name'=>$row->it_asset_name,
			'asset_type'=>$row->asset_name,
			'brand_name'=>$row->brand_name,
			'model_number'=>$row->model_number,
			'serial_number'=>$row->serial_number,
			'issue'=>$row->issue,
			'ms_office'=>$row->ms_office,
			'ms_office_licence_type'=>$row->ms_office_licence_type,
			'operating_system'=>$row->operating_system,
			'ms_office_window_licence_type'=>$row->ms_office_window_licence_type,
			'antivirus_key'=>$row->antivirus_key,
			'business_location'=>$row->company_name."<br>".$row->city_name,
			'location'=>$row->installed_location,
			'warranty_end_date'=>$row->warranty_end_date,
			'edit'=>$edit." &nbsp; ".$view);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($tech_item_data),
			"iTotalDisplayRecords" => count($tech_item_data),
			"aaData"=>$tech_item_data);
			
		echo json_encode($results);
	}
	
	public function assets_by_department(){
		$this->load->view('it_assets/asset_by_department');
	}
	
	public function it_Asset_by_department(){
		
		
		$i=1;
		$tech_item_data = array();
		$this->db->select('a.it_asset_name,a.asset_id, a.business_location_id, a.department_id, a.model_number,a.serial_number, a.issue, a.asset_type ,a.computer_name,a.added_by,a.purchase_date,b.location_id, b.installed_location,d.business_loc_id, d.city_id, d.company_name, e.user_id, e.first_name, e.last_name,c.type_id,c.asset_type as asset_name,f.brand_id,f.brand_name,g.city_id, g.city_name, s.user_id, s.first_name as assigned_person_fname, s.last_name as assigned_person_lname, ms.ms_office_id, ms.ms_office, msl.ms_office_id,msl.ms_office_licence_type,msw.ms_office_id, msw.ms_office_licence_type as ms_office_window_licence_type, o.operationg_id, o.operating_system')->from('presto_it_assets a')->join('installed_location b','a.department_id=b.location_id','left')->join('business_location d','a.business_location_id=d.business_loc_id','left')->join('asset_type c','a.asset_type=c.type_id','left')->join('system_users e','e.user_id=a.added_by','left')->join('asset_brands f','a.brand_id=f.brand_id','left')->join('cities g','d.city_id=g.city_id','left');
		$this->db->join('system_users s','a.assigned_to=s.user_id','left');
		$this->db->join('asset_microsift_office ms','a.ms_office=ms.ms_office_id','left');
		$this->db->join('asset_microsift_office_licence_type msl','a.ms_office_license_type=msl. 	ms_office_id','left');
		$this->db->join('asset_microsift_windows_licence_type msw','a.window_license_type=msw. 	ms_office_id','left');
		$this->db->join('asset_operating_system o','a.operating_system=o.operationg_id','left');
		$this->db->where('a.department_id',$this->uri->segment(3));
		$this->db->order_by('c.asset_type','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
											
			$edit = "<a href='".page_url."IT_Assets/edit_item/".$row->asset_id."'><i class='fa fa-pencil'></i></a>";
			$view = "<a href='".page_url."IT_Assets/view_item_detail/".$row->asset_id."'><span class='btn btn-warning btn-xs'>View Detail</span></a>";			
			$tech_item_data[] = array('sr_no'=>$i,
			'assigned_to'=>$row->assigned_person_fname." ".$row->assigned_person_lname,
			'it_asset_name'=>$row->it_asset_name,
			'asset_type'=>$row->asset_name,
			'brand_name'=>$row->brand_name,
			'model_number'=>$row->model_number,
			'serial_number'=>$row->serial_number,
			'issue'=>$row->issue,
			'business_location'=>$row->company_name."<br>".$row->city_name,
			'location'=>$row->installed_location,
			'ms_office'=>$row->ms_office,
			'ms_office_licence_type'=>$row->ms_office_licence_type,
			'ms_office_window_licence_type'=>$row->ms_office_window_licence_type,
			'operating_system'=>$row->operating_system,
			'edit'=>$edit." &nbsp; ".$view);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($tech_item_data),
			"iTotalDisplayRecords" => count($tech_item_data),
			"aaData"=>$tech_item_data);
			
		echo json_encode($results);
	}
	
	public function assets_by_ms_office_license(){
		$this->load->view('it_assets/asset_by_ms_office_license');
	}
	
	public function it_Asset_by_ms_office_license(){
		
		
		$i=1;
		$tech_item_data = array();
		$this->db->select('a.it_asset_name,a.asset_id, a.business_location_id, a.department_id, a.model_number,a.serial_number, a.issue, a.asset_type ,a.computer_name,a.added_by,a.purchase_date,b.location_id, b.installed_location,d.business_loc_id, d.city_id, d.company_name, e.user_id, e.first_name, e.last_name,c.type_id,c.asset_type as asset_name,f.brand_id,f.brand_name,g.city_id, g.city_name, s.user_id, s.first_name as assigned_person_fname, s.last_name as assigned_person_lname, ms.ms_office_id, ms.ms_office, msl.ms_office_id,msl.ms_office_licence_type,msw.ms_office_id, msw.ms_office_licence_type as ms_office_window_licence_type, o.operationg_id, o.operating_system')->from('presto_it_assets a')->join('installed_location b','a.department_id=b.location_id','left')->join('business_location d','a.business_location_id=d.business_loc_id','left')->join('asset_type c','a.asset_type=c.type_id','left')->join('system_users e','e.user_id=a.added_by','left')->join('asset_brands f','a.brand_id=f.brand_id','left')->join('cities g','d.city_id=g.city_id','left');
		$this->db->join('system_users s','a.assigned_to=s.user_id','left');
		$this->db->join('asset_microsift_office ms','a.ms_office=ms.ms_office_id','left');
		$this->db->join('asset_microsift_office_licence_type msl','a.ms_office_license_type=msl.ms_office_id','left');
		$this->db->join('asset_microsift_windows_licence_type msw','a.window_license_type=msw. 	ms_office_id','left');
		$this->db->join('asset_operating_system o','a.operating_system=o.operationg_id','left');
		$this->db->where('a.ms_office_license_type',$this->uri->segment(3));
		$this->db->order_by('c.asset_type','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
											
			$edit = "<a href='".page_url."IT_Assets/edit_item/".$row->asset_id."'><i class='fa fa-pencil'></i></a>";
			$view = "<a href='".page_url."IT_Assets/view_item_detail/".$row->asset_id."'><span class='btn btn-warning btn-xs'>View Detail</span></a>";			
			$tech_item_data[] = array('sr_no'=>$i,
			'assigned_to'=>$row->assigned_person_fname." ".$row->assigned_person_lname,
			'it_asset_name'=>$row->it_asset_name,
			'asset_type'=>$row->asset_name,
			'brand_name'=>$row->brand_name,
			'model_number'=>$row->model_number,
			'serial_number'=>$row->serial_number,
			'issue'=>$row->issue,
			'business_location'=>$row->company_name."<br>".$row->city_name,
			'location'=>$row->installed_location,
			'ms_office'=>$row->ms_office,
			'ms_office_licence_type'=>$row->ms_office_licence_type,
			'ms_office_window_licence_type'=>$row->ms_office_window_licence_type,
			'operating_system'=>$row->operating_system,
			'edit'=>$edit." &nbsp; ".$view);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($tech_item_data),
			"iTotalDisplayRecords" => count($tech_item_data),
			"aaData"=>$tech_item_data);
			
		echo json_encode($results);
	}
	
		public function assets_by_window_license(){
		$this->load->view('it_assets/asset_by_window_license');
	}
	
	public function it_Asset_by_window_license(){
		
		
		$i=1;
		$tech_item_data = array();
		$this->db->select('a.it_asset_name,a.asset_id, a.business_location_id, a.department_id, a.model_number,a.serial_number, a.issue, a.asset_type ,a.computer_name,a.added_by,a.purchase_date,b.location_id, b.installed_location,d.business_loc_id, d.city_id, d.company_name, e.user_id, e.first_name, e.last_name,c.type_id,c.asset_type as asset_name,f.brand_id,f.brand_name,g.city_id, g.city_name, s.user_id, s.first_name as assigned_person_fname, s.last_name as assigned_person_lname, ms.ms_office_id, ms.ms_office, msl.ms_office_id,msl.ms_office_licence_type,msw.ms_office_id, msw.ms_office_licence_type as ms_office_window_licence_type, o.operationg_id, o.operating_system')->from('presto_it_assets a')->join('installed_location b','a.department_id=b.location_id','left')->join('business_location d','a.business_location_id=d.business_loc_id','left')->join('asset_type c','a.asset_type=c.type_id','left')->join('system_users e','e.user_id=a.added_by','left')->join('asset_brands f','a.brand_id=f.brand_id','left')->join('cities g','d.city_id=g.city_id','left');
		$this->db->join('system_users s','a.assigned_to=s.user_id','left');
		$this->db->join('asset_microsift_office ms','a.ms_office=ms.ms_office_id','left');
		$this->db->join('asset_microsift_office_licence_type msl','a.ms_office_license_type=msl. 	ms_office_id','left');
		$this->db->join('asset_microsift_windows_licence_type msw','a.window_license_type=msw. 	ms_office_id','left');
		$this->db->join('asset_operating_system o','a.operating_system=o.operationg_id','left');
		$this->db->where('a.window_license_type',$this->uri->segment(3));
		$this->db->order_by('c.asset_type','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
											
			$edit = "<a href='".page_url."IT_Assets/edit_item/".$row->asset_id."'><i class='fa fa-pencil'></i></a>";
			$view = "<a href='".page_url."IT_Assets/view_item_detail/".$row->asset_id."'><span class='btn btn-warning btn-xs'>View Detail</span></a>";			
			$tech_item_data[] = array('sr_no'=>$i,
			'assigned_to'=>$row->assigned_person_fname." ".$row->assigned_person_lname,
			'it_asset_name'=>$row->it_asset_name,
			'asset_type'=>$row->asset_name,
			'brand_name'=>$row->brand_name,
			'model_number'=>$row->model_number,
			'serial_number'=>$row->serial_number,
			'issue'=>$row->issue,
			'business_location'=>$row->company_name."<br>".$row->city_name,
			'location'=>$row->installed_location,
			'ms_office'=>$row->ms_office,
			'ms_office_licence_type'=>$row->ms_office_licence_type,
			'ms_office_window_licence_type'=>$row->ms_office_window_licence_type,
			'operating_system'=>$row->operating_system,
			'edit'=>$edit." &nbsp; ".$view);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($tech_item_data),
			"iTotalDisplayRecords" => count($tech_item_data),
			"aaData"=>$tech_item_data);
			
		echo json_encode($results);
	}
	
	public function expiry_warranty()
	{
		$this->load->view('techsupport/list_expiry_warrenty');
	}
	public function list_expire_warranty()
	{
		$i=1;
		$faq_data= array();
		  $condition1 = date('Y'.'-'.'m'.'-'.'d');
		  $condition2 = date('Y'.'-'.'m'.'-'.'d',strtotime('+15 days'));
		 // echo $condition2; exit;
		  $this->db->select('a.*,b.business_loc_id,b.company_name,c.department_id,c.department,d.type_id,d.asset_type,e.brand_id,e.brand_name');
		  $this->db->from('presto_it_assets a');
		  $this->db->join('business_location b','b.business_loc_id=a.business_location_id');
		  $this->db->join('departments c','c.department_id=a.department_id');
		  $this->db->join('asset_type d','d.type_id=a.asset_type');
		  $this->db->join('asset_brands e','e.brand_id=a.brand_id');
		  
		  $this->db->where('a.warranty_end_date BETWEEN "'. date('Y-m-d', strtotime($condition1)). '" and "'. date('Y-m-d', strtotime($condition2)).'"');
		  $res = $this->db->get();
		
		$faq_data = array();
		//echo "<pre>";print_r($res->result());exit;
		foreach($res->result() as $row){
				
			$faq_data[] = array('sr_no'=>$i,
			'business_location_id'=>$row->company_name,
			'department_id'=>$row->department,
			'it_asset_name'=>$row->it_asset_name,
			'asset_type'=>$row->asset_type,
			'brand_id'=>$row->brand_name,
			'computer_name'=>$row->computer_name,
			
			'model_number'=>$row->model_number,
			'serial_number'=>$row->serial_number,
			'vendor_name'=>$row->vendor_name,
			'available_in_stock'=>$row->available_in_stock,
			
			'warranty_end_date'=>$row->warranty_end_date
			);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($faq_data),
			"iTotalDisplayRecords" => count($faq_data),
			"aaData"=>$faq_data);
			
		echo json_encode($results);
	}
	
	public function delete_it_item(){
	    $id = $this->uri->segment(3);
	    $this->db->where('asset_id',$id);
	    $res = $this->db->delete('presto_it_assets');
	    if($res){
	        	$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully deleted.</div>');
			redirect(page_url.'IT_Assets');
	    }
	}
	
	public function assets_value_dashboard(){
		$this->load->view('it_assets/asset_value_dashboard');
	}
	
	public function assets_value_dashboard_list()
	{
	$i=1;
	$tech_item_data = array();
	$this->db->select('a.it_asset_name, a.computer_name, a.assets_value, b.installed_location,d.company_name, e.first_name, e.last_name,c.asset_type as asset_name,f.brand_id,f.brand_name,g.city_id, g.city_name, s.user_id, s.first_name as assigned_person_fname, s.last_name as assigned_person_lname')->from('presto_it_assets a')->join('installed_location b','a.department_id=b.location_id','left')->join('business_location d','a.business_location_id=d.business_loc_id','left')->join('asset_type c','a.asset_type=c.type_id','left')->join('system_users e','e.user_id=a.added_by','left')->join('asset_brands f','a.brand_id=f.brand_id','left')->join('cities g','d.city_id=g.city_id','left');
	$this->db->join('system_users s','a.assigned_to=s.user_id','left');
	$this->db->order_by('c.asset_type','asc');
	$query = $this->db->get();
	$res = $query->result();
		
		foreach($res as $row){
			
			$tech_item_data[] = array('sr_no'=>$i,
			'assigned_to'=>$row->assigned_person_fname." ".$row->assigned_person_lname,
			'it_asset_name'=>$row->it_asset_name,
			'asset_type'=>$row->asset_name,
			'user_name'=>$row->first_name." ".$row->last_name,
			'brand_name'=>$row->brand_name,
			'computer_name'=>$row->computer_name,
			'business_location'=>$row->company_name."<br>".$row->city_name,
			'assets_value'=>$row->assets_value,
			'location'=>$row->installed_location);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($tech_item_data),
			"iTotalDisplayRecords" => count($tech_item_data),
			"aaData"=>$tech_item_data);
			
		echo json_encode($results);
	}
}
