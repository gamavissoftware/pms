<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Store extends CI_Controller {
	
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
		$this->load->model('Store_model','storemodel');
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
	public function add_rack_location()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('rack_location', 'rack_location', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('store/rack_location');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "store_rack_location";
		$query = $this->db->select('rack_location')->from('store_rack_location')->where('rack_location',$this->input->post('rack_location'))->get();
		$res = $query->result();
		
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry! This record already exist.</div>');
			redirect(page_url.'Store/add_rack_location');
			
		}else{
	
				
			$data = array('rack_location'=>$this->input->post('rack_location'));
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Store/add_rack_location');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Store/add_rack_location');
		}
		}
		
	}
		
	}
	public function rack_location_list()
	{
		$i=1;
		$vendor_data= array();
		$this->db->select('*')->from('store_rack_location');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			
			$edit = "<a href='".page_url."Store/edit_rack_location/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			
			
			$vendor_data[] = array('sr_no'=>$i,
			'rack_location'=>$row->rack_location,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($vendor_data),
			"iTotalDisplayRecords" => count($vendor_data),
			"aaData"=>$vendor_data);
			
		echo json_encode($results);
	}
	
	public function edit_rack_location(){
		$this->load->view('store/edit_rack_location');
	}
	
	public function update_rack_location()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('rack_location', 'Rack Location', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('store/edit_rack_location');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "store_rack_location";
		$query = $this->db->select('rack_location')->from('store_rack_location')->where('rack_location',$this->input->post('rack_location'))->get();
		$res = $query->result();
		
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry! This record already exist.</div>');
			redirect(page_url.'Store/add_rack_location');
			
		}else{
	
				
			$data = array('rack_location'=>$this->input->post('rack_location'));
			$this->db->where('id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Store/add_rack_location');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Store/add_rack_location');
		}
		}
		
	}
		
	}
	
	public function add_machine()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('machine', 'machine', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('store/machine');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "machine_master";
		$query = $this->db->select('machine')->from('machine')->where('machine',$this->input->post('machine'))->get();
		$res = $query->result();
		
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry! This record already exist.</div>');
			redirect(page_url.'Store/add_machine');
			
		}else{
	
				
			$data = array('rack_location'=>$this->input->post('rack_location'),
			'status'=>$this->input->post('status'),
			'added_by'=>$user_id,
			'added_on'=>$date);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Store/add_machine');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Store/add_machine');
		}
		}
		
	}
		
	}
	public function machine_list()
	{
		$i=1;
		$machine_data= array();
		$this->db->select('*')->from('machine_master');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			
$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Store/update_machine_status/".$row->machine_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Store/update_machine_status/".$row->machine_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			
			
			$edit = "<a href='".page_url."Store/edit_machine/".$row->machine_id."'><i class='fa fa-pencil'></i></a>";	
			
			
			$machine_data[] = array('sr_no'=>$i,
			'machine'=>$row->machine,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($machine_data),
			"iTotalDisplayRecords" => count($machine_data),
			"aaData"=>$machine_data);
			
		echo json_encode($results);
	}
	
	public function edit_machine(){
		$this->load->view('store/edit_machine');
	}
	
	public function update_machine()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('machine', 'Machine', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/edit_machine');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "machine_master";
				
			$data = array('machine'=>$this->input->post('machine'),
			'status'=>$this->input->post('status'));
			$this->db->where('machine_id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Store/add_machine');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Store/add_machine');
		}
		
		
	}
		
	}
	
	public function update_machine_status()
	{
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "machine_id";
		$table = "machine_master";
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
			redirect(page_url.'Store/add_machine');
		}

		
public function add_machine_parts()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('machine_part', 'machine_part', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('store/machine_parts');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "machine_parts_master";
		$query = $this->db->select('machine_part')->from('machine_parts_master')->where('machine_part',$this->input->post('machine_part'))->get();
		$res = $query->result();
		
		
		if($res){
			
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry! This record already exist.</div>');
			redirect(page_url.'Store/add_machine_parts');
			
		}else{
	
				
			$data = array('machine_part'=>$this->input->post('machine_part'),
			'status'=>$this->input->post('status'),
			'added_by'=>$user_id,
			'added_on'=>$date);
			
		$result  = $this->db->insert($table,$data);
		$lastid = $this->db->insert_id();		
		if($result)
		{
			if(isset($_REQUEST['vendor'])){	
					$tags1=count($_REQUEST['vendor']);
					if($tags1>0)
					{
					$vendor=$_REQUEST['vendor'];
					$price = $_REQUEST['price'];
					for($x=0;$x<$tags1;$x++){
					if($vendor[$x]!='')
						{
						    $data = array('vendor_id'=>$vendor[$x],
							'item_id'=>$lastid,
							'price'=>$price[$x],
							'added_on'=>$date,
							'added_by'=>$user_id);
							$this->db->insert('vendorwise_item_price',$data);
						}
					}
					}
					}
					
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Store/add_machine_parts');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Store/add_machine_parts');
		}
		}
		
	}
		
	}
	public function machinepart_list()
	{
		$i=1;
		$machinepart_data= array();
		$this->db->select('*')->from('machine_parts_master')->order_by('machine_part','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			
$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Store/update_machinepart_status/".$row->part_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Store/update_machinepart_status/".$row->part_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			
			
			$edit = "<a href='".page_url."Store/edit_machinepart/".$row->part_id."'><i class='fa fa-pencil'></i></a>";	
			$m=1;
			$html = "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center'>Sr No.</th><th style='padding:2px 2px 2px 2px; text-align:center'>Vendor Name</th><th style='padding:2px 2px 2px 2px; text-align:center'>Price</th></tr>";
			$instrumentsss = array();
			$query = $this->db->select('a.vendor_id, a.price, b.vendor_id, b.vendor_name')->from('vendorwise_item_price a')->join('vendor_list b','a.vendor_id=b.vendor_id','left')->where('item_id',$row->part_id)->get();
			foreach($query->result() as $pricelist){
				
					$backgroundcolor = "background-color:green; color:#fff; font-weight:bold;";
				
				$html.="<tr style='".$backgroundcolor."'>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$m."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($pricelist->vendor_name)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($pricelist->price)."</td>";
				$html.="</tr>";
			$m++;}
			$html.="</table>";
			
			
			$machinepart_data[] = array('sr_no'=>$i,
			'machine_part'=>$row->machine_part,
			'status'=>$sta,
			'vendorwise'=>$html,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($machinepart_data),
			"iTotalDisplayRecords" => count($machinepart_data),
			"aaData"=>$machinepart_data);
			
		echo json_encode($results);
	}
	
	public function edit_machinepart(){
		$this->load->view('store/edit_machine_part');
	}
	
	public function update_machinepart()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('machine_part', 'Machine Part', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/edit_machine_part');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "machine_parts_master";
				
		$data = array('machine_part'=>$this->input->post('machine_part'),
			'status'=>$this->input->post('status'));
			$this->db->where('part_id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			if(isset($_REQUEST['vendor'])){	
					$tags1=count($_REQUEST['vendor']);
					if($tags1>0)
					{
					$vendor=$_REQUEST['vendor'];
					$price = $_REQUEST['price'];
					$interval=$_REQUEST['interval'];
					$recordid = $_REQUEST['recordid'];
					for($x=0;$x<$tags1;$x++){
					if($vendor[$x]!='')
						{
						    $data = array('vendor_id'=>$vendor[$x],
							'item_id'=>$this->uri->segment(3),
							'price'=>$price[$x],
							'added_on'=>$date,
							'added_by'=>$user_id);
							if(isset($recordid[$x])){
								$this->db->where('id',$recordid[$x]);
								$this->db->update('vendorwise_item_price',$data);
							}else{
								$this->db->insert('vendorwise_item_price',$data);
							}
							
						}
					}
					}
					}
			
			
			
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Store/add_machine_parts');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Store/add_machine_parts');
		}
		
		
	}
		
	}
	public function update_machinepart_status()
	{
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "part_id";
		$table = "machine_parts_master";
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
			redirect(page_url.'Store/add_machine_parts');
		}
		
public function machine_part_data_with_picture(){
		$this->load->view('store/machine_part_data_with_picture');
	}
public function machine_part_data_with_picture_listOldd()
	{
		$i=1;
		$machinepart_data= array();
		$this->db->select('a.id, a.item_id, a.part_id, a.specification, a.makes, a.size_in_mm, a.material, a.raw_bop, a.fincode, a.qty, a.picture, b.instruments_name,c.machine_part, d.rack_location, p.category, a.vendor, a.price')->from('machine_parts_with_picture a')->join('presto_instruments b','a.item_id=b.id','left')->join('machine_parts_master c','a.part_id=c.part_id','left')->join(' store_rack_location d','a.location_id=d.id','left')->join('presto_machine_part_category p','a.category_id=p.id','left');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			
		$edit = "<a href='".page_url."Store/edit_machine_detail_picture/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			
			if($row->picture){
				$IMG = product_items.$row->picture;
				$image = "<img src='".$IMG."' width='100px'>";
			}else{
				$image="";
			}
			$machinepart_data[] = array('sr_no'=>"<span style='font-size:11px;'>".$i."</span>",
			'category'=>"<span style='font-size:11px;'>".$row->category."</span>",
			'machine'=>"<span style='font-size:11px;'>".$row->instruments_name."</span>",
			'machine_part'=>"<span style='font-size:11px;'>".$row->machine_part."</span>",
			'specification'=>"<span style='font-size:11px;'>".$row->specification."</span>",
			'makes'=>"<span style='font-size:11px;'>".$row->makes."</span>",
			'size_in_mm'=>"<span style='font-size:11px;'>".$row->size_in_mm."</span>",
			'material'=>"<span style='font-size:11px;'>".$row->material."</span>",
			'raw_bop'=>"<span style='font-size:11px;'>".$row->raw_bop."</span>",
			'fincode'=>"<span style='font-size:11px;'>".$row->fincode."</span>",
			'qty'=>"<span style='font-size:11px;'>".$row->qty."</span>",
			'location'=>"<span style='font-size:11px;'>".$row->rack_location."</span>",
			'vendor'=>"<span style='font-size:11px;'>".$row->vendor."</span>",
			'price'=>"<span style='font-size:11px;'>".$row->price."</span>",
			'image'=>"<span style='font-size:11px;'>".$image."</span>",
			'edit'=>"<span style='font-size:11px;'>".$edit."</span>");
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($machinepart_data),
			"iTotalDisplayRecords" => count($machinepart_data),
			"aaData"=>$machinepart_data);
			
		echo json_encode($results);
	}
public function edit_machine_detail_picture(){
		$this->load->view('store/edit_machine_detail_picture');
	}

public function update_machine_detail_picture()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('machine', 'Machine', 'required|trim');
		$this->form_validation->set_rules('machine_part', 'Machine Part', 'required|trim');
		$this->form_validation->set_rules('rack_location', 'rack_location', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('store/edit_machine_detail_picture');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "machine_parts_with_picture";
			
			$photo=$_FILES['picture']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$pic=time().'.'.$cat_image;
				move_uploaded_file($_FILES["picture"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/image_bank/product_items/' . $pic);
			}else
			{
				$pic=$this->input->post('old_img');
				}
				
			$data = array('item_id'=>$this->input->post('machine'),
			'part_id'=>$this->input->post('machine_part'),
			'specification'=>$this->input->post('specification'),
			'makes'=>$this->input->post('make'),
			'fincode'=>$this->input->post('fincode'),
			'qty'=>$this->input->post('qty'),
			'location_id'=>$this->input->post('rack_location'),
			'picture'=>$pic,
			'status'=>$this->input->post('status'));
			
			$this->db->where('id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Store/machine_part_data_with_picture');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Store/machine_part_data_with_picture');
		}
		
		
	}
		
	}
	
public function bom_materials(){
	$this->load->view('store/bom_materials');
}
public function bom_materials_via_category(){
	$this->load->view('store/bom_materials_via_category');
}
public function bom_materials_list()
	{
		$i=1;
		$uri = $this->uri->segment(3);
		$machinepart_data= array();
		$this->db->select('a.size_in_mm, a.material, a.row_bop, a.location, a.fin_code, a.qty, b.machine_name, c.machine_parts, d.category')->from('presto_machine_bop_items a')->join('presto_machines b','a.machine_id=b.id','left')->join('presto_machine_parts c','a.part_id=c.id','left')->join('presto_machine_part_category d','a.category_id=d.id','left');
		if($uri){
			$this->db->where('a.category_id',$this->uri->segment(3));
		}
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
		
		$machinepart_data[] = array('sr_no'=>$i,
			'category'=>$row->category,
			'machine_name'=>$row->machine_name,
			'machine_parts'=>$row->machine_parts,
			'size_in_mm'=>$row->size_in_mm,
			'material'=>$row->material,
			'row_bop'=>$row->row_bop,
			'location'=>$row->location,
			'fin_code'=>$row->fin_code,
			'qty'=>$row->qty);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($machinepart_data),
			"iTotalDisplayRecords" => count($machinepart_data),
			"aaData"=>$machinepart_data);
			
		echo json_encode($results);
	}
	
	public function add_machine_detail_picture()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('machine', 'Machine', 'required|trim');
		$this->form_validation->set_rules('machine_part', 'Machine Part', 'required|trim');
		$this->form_validation->set_rules('rack_location', 'rack_location', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('store/add_machine_with_picture');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "machine_parts_with_picture";
			
			$photo=$_FILES['picture']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$pic=time().'.'.$cat_image;
				move_uploaded_file($_FILES["picture"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/image_bank/product_items/' . $pic);
			}else
			{
				$pic="";
				}
				
			$data = array('item_id'=>$this->input->post('machine'),
			'part_id'=>$this->input->post('machine_part'),
			'specification'=>$this->input->post('specification'),
			'makes'=>$this->input->post('make'),
			'fincode'=>$this->input->post('fincode'),
			'qty'=>$this->input->post('qty'),
			'location_id'=>$this->input->post('rack_location'),
			'picture'=>$pic,
			'status'=>$this->input->post('status'));
			
			
		$result  = $this->db->insert($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Store/machine_part_data_with_picture');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Store/machine_part_data_with_picture');
		}
		
		
	}
		
	}
	
	
	
	function pr()
	{
		$this->load->view('store/invoice');
	}
	
	
	public function indent_form(){
$this->load->view('store/indent_form');
}

public function fetch_machine()
{
echo "<option value=''>--Select Machine--</option>";
$category = $this->input->post('category');
$query = $this->db->select('b.id, b.instruments_name')->from('machine_parts_with_picture a')->join('presto_instruments b','a.item_id=b.id')->where('a.category_id',$category)->group_by('b.instruments_name')->get();
if($query->num_rows()>0)
{
foreach($query->result() as $machinedetail)
{
echo "<option value=".$machinedetail->id.">".$machinedetail->instruments_name."</option>";
}
}

}
public function fetch_machine_part()
{
$category = $this->input->post('category');
$item_id = $this->input->post('item_name');
$query = $this->db->select('a.id, b.machine_part, specification')->from('machine_parts_with_picture a')->join('machine_parts_master b','a.part_id=b.part_id','left')->where('a.category_id',$category)->where('a.item_id',$item_id)->get();

echo "<option value=''>Select Machine Part</option>";

foreach($query->result() as $machinedetail)
{
echo "<option value=".$machinedetail->id.">".$machinedetail->machine_part." (".$machinedetail->specification.")</option>";
}

}
public function fetch_part_image()
{

$part_name = $this->input->post('part_name');

$query = $this->db->select('picture')->from('machine_parts_with_picture')->where('id',$part_name)->get();

foreach($query->result() as $machinedetail);

echo "<img src='".product_items.$machinedetail->picture."' width='100px'>";


}


function getcategory()
{
	$cat=$this->input->post('category');
	
$query = $this->db->select('id, category')->from('presto_machine_part_category')->where('cattype',$cat)->get();
$rw=$query->num_rows();
if($rw!=1)
{
echo '<option value="">SELECT CATEGORY</option>';
}
if($query->num_rows()>0)
{
foreach($query->result() as $category)
{
	if($rw==1)
	{
		$a="selected";
	}else{ $a="";}
echo '<option value="'.$category->id.'" '.$a.'>'.$category->category.'</option>';
}
}

}


function getunit()
{

$item_id = $this->input->post('item_name');
$query = $this->db->select('a.id,b.shortname')->from('machine_parts_with_picture a')->join('units b','a.unit=b.id')->where('a.part_id',$item_id)->get();
if($query->num_rows()>0)
{
	
	foreach($query->result() as $query1);
	
		$unit=strtoupper($query1->shortname);
	echo $unit;
}else{
	
	$unit='';
	echo $unit;
}
	
	
}


function getreturnunit($id)
{

$item_id = $id;
$query = $this->db->select('b.id,b.shortname')->from('units b')->where('b.id',$item_id)->get();
if($query->num_rows()>0)
{
	
	foreach($query->result() as $query1);
	
		$unit=strtoupper($query1->shortname);
	return $unit;
}else{
	
	$unit='';
	return $unit;
}
	
	
}


function saveindentOlddd()
{
	
	$cat=$this->input->post('category');
	$itemname=$this->input->post('item_names');
	$qty=$this->input->post('qty');
	
	/** Check for any previous intend**/
		$inde=$this->db->select('id')->from('intend_request')->group_by('indendno')->get();
		$ninde=$inde->num_rows();
		if($ninde==0)
		{
			$no=1;
		}else{
			$no=$ninde+1;
		}
		$indno= sprintf("%03d", $no);
		/** END **/
		
	for($i=0;$i<count($itemname);$i++)
	{
		$instrumentid=$itemname[$i];
		$quantity=$qty[$i];
		
		/** Get Unit **/
		$query = $this->db->select('a.unit')->from('house_keeping_items a')->where('a.id',$instrumentid)->get();
		if($query->num_rows()>0)
		{
		foreach($query->result() as $query1);
		$unit=$query1->unit;
		}else
		{
		$unit=0;
		}
		/** END **/
		
		
		
		$data=array('indendno'=>$indno,'prefix'=>'IND','itemid'=>$instrumentid,'qty'=>$quantity,'unit'=>$unit,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
		
		$this->db->insert('intend_request',$data);
		
	}
	
	$this->session->set_flashdata('message','Indent Request Created');
	redirect(page_url.'Store/indent_form');
	
	
	
}

function generateprfromintendOlddd()
{
	
	$indentno=$this->uri->segment(3);
	
		$prnos=$this->db->select('id')->from('purchase_request')->group_by('prno')->get();
		$num=$prnos->num_rows();
		if($num==0)
		{
		$num1=1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPR'.$num_padded;
		}else{
		$num1=$num+1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPR'.$num_padded;

	}
	
	$resty=$this->db->select('itemid,qty,unit')->from('intend_request')->where('indendno',$indentno)->get();
	if($resty->num_rows()>0)
	{
		
		foreach($resty->result() as $resty1)
		{
			
			$data=array('itemid'=>$resty1->itemid,'source'=>'2','sourceid'=>$indentno,'prno'=>$code,'qty'=>$resty1->qty,'unit'=>$resty1->unit,'addedBy'=>$_SESSION['logged_in']['user_id'],'addedOn'=>date('Y-m-d H:i:s'));
			
			$this->db->insert('purchase_request',$data);
			
		}
		
		/** UPDATE INTEND APPROVAL **/
		$datau=array('approvalstatus'=>'1','approvedOn'=>date('Y-m-d H:i:s'),'approvedBy'=>$_SESSION['logged_in']['user_id']);
		$this->db->where('indendno',$indentno);
		$this->db->update('intend_request',$datau);
		/** END **/
		
		redirect(page_url.'Reporting/pendingindend/'.$code);
		
		
	}else{
		
		echo "INDEND NOT AVAILABLE";exit;
	}
	
	
	
}
	
	
	
	function saveindent()
{
	
	$type=$this->input->post('type');
	$itemname=$this->input->post('instruments');
	$qty=$this->input->post('qty');
	/** Check for any previous intend**/
		$inde=$this->db->select('indendno,id,indendno')->from('intend_request')->group_by('indendno')->limit(1)->order_by('id','desc')->get();
		$ninde=$inde->num_rows();
		if($ninde==0)
		{
			$no=1;
		}else{
		foreach($inde->result() as $prnoss);
		$numpart = $prnoss->indendno;
			$no=$numpart+1;
		}
		
		$indno= sprintf("%03d", $no);
		//echo $indno; exit;
		/** END **/
		
	for($i=0;$i<count($itemname);$i++)
	{
		$instrumentid=$itemname[$i];
		$quantity=$qty[$i];
		
		if($type=='1'){
			/** Get Unit **/
		$query = $this->db->select('a.unit')->from('machine_parts_with_picture a')->where('a.id',$instrumentid)->get();
		if($query->num_rows()>0)
		{
		foreach($query->result() as $query1);
		$unit=$query1->unit;
		}else
		{
		$unit=0;
		}
		/** END **/
		/** CHECK PRICE OF THE ITEMS**/
		$Q = $this->db->select('price')->from('vendors_price')->where('itemid',$instrumentid)->where('green_supplier','1')->get();
		if($Q->num_rows()>0){
		foreach($Q->result() as $itemprice);
		$total[] = $itemprice->price*$quantity;
		}else{
			
			$Q1 = $this->db->select('price')->from('vendors_price')->where('itemid',$instrumentid)->order_by('id','desc')->limit(1)->get();
			if($Q1->num_rows()>0){
				foreach($Q1->result() as $itemprice);
				$total[] = $itemprice->price*$quantity;
			}else{
				$total[] = "0";
			}
			
		}
		/** CHECK PRICE OF THE ITEMS**/
		
		
		}else{
			/** Get Unit **/
		$query = $this->db->select('a.unit')->from('house_keeping_items a')->where('a.id',$instrumentid)->get();
		if($query->num_rows()>0)
		{
		foreach($query->result() as $query1);
		$unit=$query1->unit;
		}else
		{
		$unit=0;
		}
		
		/** CHECK PRICE OF THE ITEMS**/
		$Q = $this->db->select('price')->from('vendorwise_house_keeping_item_price')->where('item_id',$instrumentid)->where('green_supplier','1')->get();
		if($Q->num_rows()>0){
		foreach($Q->result() as $itemprice);
		$total[] = $itemprice->price*$quantity;
		}else{
			$Q1 = $this->db->select('price')->from('vendorwise_house_keeping_item_price')->where('item_id',$instrumentid)->order_by('id','desc')->limit(1)->get();
			if($Q1->num_rows()>0){
				foreach($Q1->result() as $itemprice);
				$total[] = $itemprice->price*$quantity;
			}else{
				$total[] = "0";
			}
			
		}
		/** CHECK PRICE OF THE ITEMS**/
		/** END **/
		}
		
		
		
		
		$data=array('indendno'=>$indno,
		'prefix'=>'IND',
		'itemid'=>$instrumentid,
		'qty'=>$quantity,
		'unit'=>$unit,
		'addedOn'=>date('Y-m-d H:i:s'),
		'indent_type'=>$type,
		'addedBy'=>$_SESSION['logged_in']['user_id']);
		
		$this->db->insert('intend_request',$data);
		
	}
	
	$grandtotal =  array_sum($total);
	if($grandtotal>500){
		$data = array('approvalstatus'=>'0');
		$this->db->where('indendno',$indno);
		$this->db->update('intend_request',$data);
	}else{
		$data = array('approvalstatus'=>'1');
		$this->db->where('indendno',$indno);
		$this->db->update('intend_request',$data);
	}
	$this->session->set_flashdata('message','<div class="alert alert-success">Indent Request Created.</div>');
	redirect(page_url.'Store/indent_form');
	
	
	
}

function generateprfromintend()
{
	
	$indentno=$this->uri->segment(3);
	
		$prnos=$this->db->select('id')->from('purchase_request')->group_by('prno')->order_by('id','desc')->limit(1)->get();
		$num=$prnos->num_rows();
		if($num==0)
		{
		$num1=1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPR'.$num_padded;
		}else{
		$num1=$num+1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPR'.$num_padded;

	}
	
	$resty=$this->db->select('itemid,qty,unit, indent_type')->from('intend_request')->where('indendno',$indentno)->get();
	if($resty->num_rows()>0)
	{
		
		foreach($resty->result() as $resty1)
		{
			/** if indent type is 1 then this is machine indent*/
			/** if indent type is 2 then this is general Item indent*/
			if($resty1->indent_type=='1'){
				$indenttype = "0";
			}else if($resty1->indent_type=='2'){
				$indenttype = "1";
			}
			
			$data=array('masterid'=>$resty1->itemid,'type'=>$indenttype,'itemid'=>$resty1->itemid,'source'=>'2','sourceid'=>$indentno,'prno'=>$code,'qty'=>$resty1->qty,'unit'=>$resty1->unit,'addedBy'=>$_SESSION['logged_in']['user_id'],'addedOn'=>date('Y-m-d H:i:s'));
			
			$this->db->insert('purchase_request',$data);
			
		}
		
		/** UPDATE INTEND APPROVAL **/
		$datau=array('approvalstatus'=>'1','approvedOn'=>date('Y-m-d H:i:s'),'approvedBy'=>$_SESSION['logged_in']['user_id'],'pr_status'=>'1');
		$this->db->where('indendno',$indentno);
		$this->db->update('intend_request',$datau);
		/** END **/
		$this->session->set_flashdata('message','<div class="alert alert-success">Thank You! PR successfully generated.</div>');
		redirect(page_url.'Reporting/pendingindend/');
		
		
	}else{
		
		echo "INDEND NOT AVAILABLE";exit;
	}
	
	
	
}



public function add_general_items()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('item_name', 'item name', 'required|trim');
		$this->form_validation->set_rules('qty', 'qty', 'required|trim');
		$this->form_validation->set_rules('min_qty', 'Qty', 'required|trim');
		$this->form_validation->set_rules('unit', 'Unit', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('store/general_items');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "house_keeping_items";
		$query = $this->db->select('item_name')->from('house_keeping_items')->where('item_name',strtoupper($this->input->post('item_name')))->get();
		$res = $query->result();
		
		
		if($res){
			
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry! This record already exist.</div>');
			redirect(page_url.'Store/add_general_items');
			
		}else{
	
			$photo=$_FILES['picture']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$pic=time().'.'.$cat_image;
				move_uploaded_file($_FILES["picture"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/image_bank/housekeeping/' . $pic);
			}else
			{
				$pic="";
				}
				
			$data = array('item_name'=>strtoupper($this->input->post('item_name')),
			'qty'=>$this->input->post('qty'),
			'min_qty'=>$this->input->post('min_qty'),
			'unit'=>$this->input->post('unit'),
			'picture'=>$pic,
			'remarks'=>$this->input->post('remarks'),
			'category_id'=>$this->input->post('category'),
			'status'=>$this->input->post('status'),
			'added_by'=>$user_id,
			'added_on'=>$date);
			
		$result  = $this->db->insert($table,$data);
		$lastid = $this->db->insert_id();		
		if($result)
		{
			if(isset($_REQUEST['vendor'])){	
					$tags1=count($_REQUEST['vendor']);
					if($tags1>0)
					{
					$vendor=$_REQUEST['vendor'];
					$price = $_REQUEST['price'];
					for($x=0;$x<$tags1;$x++){
					if($vendor[$x]!='')
						{
						    $data = array('vendor_id'=>$vendor[$x],
							'item_id'=>$lastid,
							'price'=>$price[$x],
							'added_on'=>$date,
							'added_by'=>$user_id);
							$this->db->insert('vendorwise_house_keeping_item_price',$data);
						}
					}
					}
					}
					
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Store/add_general_items');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Store/add_general_items');
		}
		}
		
	}
		
	}
	public function housekeeping_item_list()
	{
		$i=1;
		$housekeeping_data= array();
		$this->db->select('a.*,b.category, c.name')->from('house_keeping_items a')->join(' presto_machine_part_category b','a.category_id=b.id','left')->join('units c','a.unit=b.id','left')->order_by('a.item_name','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			
		$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Store/update_house_keeping_item_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Store/update_house_keeping_item_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			
			
			$edit = "<a href='".page_url."Store/edit_house_keeping_item/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			$m=1;
			$html = "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center'>Sr No.</th><th style='padding:2px 2px 2px 2px; text-align:center'>Vendor Name</th><th style='padding:2px 2px 2px 2px; text-align:center'>Price</th></tr>";
			$instrumentsss = array();
			$query = $this->db->select('a.vendor_id, a.price, b.vendor_id, b.vendor_name')->from('vendorwise_house_keeping_item_price a')->join('vendor_list b','a.vendor_id=b.vendor_id','left')->where('item_id',$row->id)->get();
			foreach($query->result() as $pricelist){
				
					$backgroundcolor = "background-color:green; color:#fff; font-weight:bold;";
				
				$html.="<tr style='".$backgroundcolor."'>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$m."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($pricelist->vendor_name)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($pricelist->price)."</td>";
				$html.="</tr>";
			$m++;}
			$html.="</table>";
			if($row->picture){
			$img = "<img src='".housekeeping.$row->picture."' width='60px'>";
			}else{
				$img = "";
			}
			$housekeeping_data[] = array('sr_no'=>$i,
			'item_name'=>$row->item_name,
			'category'=>$row->category,
			'qty'=>$row->qty,
			'min_qty'=>$row->min_qty,
			'name'=>$row->name,
			'picture'=>$img,
			'remarks'=>$row->remarks,
			'status'=>$sta,
			'vendorwise'=>$html,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($housekeeping_data),
			"iTotalDisplayRecords" => count($housekeeping_data),
			"aaData"=>$housekeeping_data);
			
		echo json_encode($results);
	}
	
	public function edit_house_keeping_item(){
		$this->load->view('store/edit_house_keeping_item');
	}
	
	public function update_house_keeping_items()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('item_name', 'Item Name', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/edit_house_keeping_item');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "house_keeping_items";
		
$photo=$_FILES['picture']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$pic=time().'.'.$cat_image;
				move_uploaded_file($_FILES["picture"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/image_bank/housekeeping/' . $pic);
			}else
			{
				$pic=$this->input->post('old_img');
				}
				
		$data = array('item_name'=>strtoupper($this->input->post('item_name')),
			'qty'=>$this->input->post('qty'),
			'min_qty'=>$this->input->post('min_qty'),
			'unit'=>$this->input->post('unit'),
			'picture'=>$pic,
			'remarks'=>$this->input->post('remarks'),
			'category_id'=>$this->input->post('category'),
			'status'=>$this->input->post('status'),
			'added_by'=>$user_id,
			'added_on'=>$date);
			$this->db->where('id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			if(isset($_REQUEST['vendor'])){	
					$tags1=count($_REQUEST['vendor']);
					if($tags1>0)
					{
					$vendor=$_REQUEST['vendor'];
					$price = $_REQUEST['price'];
					$interval=$_REQUEST['interval'];
					$recordid = $_REQUEST['recordid'];
					for($x=0;$x<$tags1;$x++){
					if($vendor[$x]!='')
						{
						    $data = array('vendor_id'=>$vendor[$x],
							'item_id'=>$this->uri->segment(3),
							'price'=>$price[$x],
							'added_on'=>$date,
							'added_by'=>$user_id);
							if(isset($recordid[$x])){
								$this->db->where('id',$recordid[$x]);
								$this->db->update('vendorwise_house_keeping_item_price',$data);
							}else{
								$this->db->insert('vendorwise_house_keeping_item_price',$data);
							}
							
						}
					}
					}
					}
			
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Store/add_general_items');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Store/add_general_items');
		}
		
		
	}
		
	}
	
	public function update_house_keeping_item_status()
	{
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "id";
		$table = "house_keeping_items";
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
			redirect(page_url.'Store/add_general_items');
		}
	
	
	
	
	function ageing()
{
	$this->load->view('store/ageing');
}


function ageninglist()
{
	
	$scheduler_data=array();
	

		$restqwee=$this->db->select('*')->from('stockageing')->get();
		if($restqwee->num_rows()>0)
		{
			$i=1;
			foreach($restqwee->result() as $restqwee1);
			
			$edit="<a href='".page_url."Store/editageing/".$restqwee1->id."'><span class='btn btn-success btn-xs'>Edit</span></a>";
				
		$scheduler_data[] = array('sr_no'=>$i,
			'days'=>$restqwee1->days,
			'factor'=>$restqwee1->factor,
			'updatedOn'=>date('d-M-Y',strtotime($restqwee1->updatedOn)),
			'edit'=>$edit
			);
			
			$i++;
			
		}
			
			

	$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
	
	
	
}

function editageing()
{
	$this->load->view('store/edit_ageing');
}

function updateageing()
{
		$id=$this->uri->segment(3);
		$data=array('days'=>$this->input->post('day'),'factor'=>$this->input->post('factor'),'updatedBy'=>$_SESSION['logged_in']['user_id'],'updatedOn'=>date('Y-m-d H:i:s'));
		$this->db->where('id',$id);
		$this->db->update('stockageing',$data);
		$this->session->set_flashdata('message','Record Added');
		redirect(page_url.'Store/ageing');	
}
public function imported_items(){
	$this->load->view('store/imported_items');
}
public function imported_items_listolfffff()
	{
		$i=1;
		$imported_data= array();
		$this->db->select('*')->from('imported_items')->order_by('description','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			$issueitem = "<a href='".page_url."Store/issue_items/".$row->id."'><span class='btn btn-success btn-xs'>Issue Item</span></a>";
			$receive_item = "<a href='".page_url."Store/receive_item/".$row->id."'><span class='btn btn-warning btn-xs'>Receive Item</span></a>";
			$blockitem = "<a href='".page_url."Store/block_items/".$row->id."'><span class='btn btn-danger btn-xs'>Block Item</span></a>";
			$edit = "<a href='".page_url."Store/edit_imported_items/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			
			$html="<table border='1'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center;'>Party Name</th><th style='padding:2px 2px 2px 2px; text-align:center;'>Jobcard No</th><th style='padding:2px 2px 2px 2px; text-align:center'>Blocked Qty</th><tbody><tr>";
			
			$query = $this->db->select('party_name,jobcard,blocked_qty')->from('imported_item_blocked')->where('item_id',$row->id)->get();
			foreach($query->result() as $result){
			$html.="<tr><td style='padding:2px 2px 2px 2px; text-align:center'>".$result->party_name."</td><td style='padding:2px 2px 2px 2px; text-align:center;'>".$result->jobcard."</td><td style='padding:2px 2px 2px 2px; text-align:center;'>".$result->blocked_qty."</td></tr>";	
			}
			$html.="</tbody></table>";
			
			
			$imported_data[] = array('sr_no'=>$i,
			'description'=>$row->description,
			'current_stock'=>$row->current_stock,
			'opening_stock'=>$row->opening_stock,
			'min_stock'=>$row->min_stock,
			'total_received'=>$row->total_received,
			'price'=>$row->price,
			'total_issued'=>$row->total_issued,
			'update_status'=>$issueitem,
			'block_item'=>$blockitem."<br><br>".$html,
			'receive_item'=>$receive_item,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($imported_data),
			"iTotalDisplayRecords" => count($imported_data),
			"aaData"=>$imported_data);
			
		echo json_encode($results);
	}
	
	
	public function imported_items_list()
	{
		$i=1;
		$imported_data= array();
		$this->db->select('*')->from('presto_instruments')->where('type','1')->where('status','1')->order_by('instruments_name','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			if($row->stock!=0)
			{
			$issueitem = "<a href='".page_url."Store/issue_items/".$row->id."'><span class='btn btn-success btn-xs'>Issue Item</span></a>";
		}else{
			$issueitem="<span style='color:red;'>Stock Not Available</span><br/>";
				$issueitem .= "<a href='".page_url."Store/issue_items/".$row->id."/1'><span class='btn btn-success btn-xs'>Issue List</span></a>";
		}
			$receive_item = "<a href='".page_url."Store/receive_item/".$row->id."'><span class='btn btn-warning btn-xs'>Receive Item</span></a>";
			if($row->stock!=0)
			{
			$blockitem = "<a href='".page_url."Store/block_items/".$row->id."'><span class='btn btn-danger btn-xs'>Block Item</span></a>";
			}else
			{
			    $blockitem="<span style='color:red;'>Stock Not Available</span>";
			}
			$edit = "<a href='".page_url."Store/edit_imported_items/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			
		$html="<table border='1'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center;'>Party Name</th><th style='padding:2px 2px 2px 2px; text-align:center;'>Jobcard No</th><th style='padding:2px 2px 2px 2px; text-align:center'>Blocked Qty</th><th style='padding:2px 2px 2px 2px; text-align:center'>Action</th><tbody><tr>";
			
			$query = $this->db->select('id,party_name,jobcard,blocked_qty,cancelled_order, cancellation_remarks')->from('imported_item_blocked')->where('item_id',$row->id)->get();
			foreach($query->result() as $result){
			    if($result->cancelled_order=='0'){
				$return = "<a href='".page_url."Store/cancel_order/".$result->id."/".$row->id."'><span class='btn btn-success btn-xs'>Cancel</span></a>";
				}else{
					$return = $result->cancellation_remarks;
				}
			$html.="<tr><td style='padding:2px 2px 2px 2px; text-align:center'>".$result->party_name."</td><td style='padding:2px 2px 2px 2px; text-align:center;'>".$result->jobcard."</td><td style='padding:2px 2px 2px 2px; text-align:center;'>".$result->blocked_qty."</td><td style='padding:2px 2px 2px 2px; text-align:center;'>".$return."</td></tr>";	
			}
			$html.="</tbody></table>";
			
			/** ISSUE ITEMS **/
			$isqty=$this->db->select('sum(qty) as issueqty')->from('issue_imported_items')->where('item_id',$row->id)->get();
			foreach($isqty->result() as $isqty1);
			/*** END **/
			$serialnumber="";
				/** ISSUE ITEMS for serial number **/
			$isqty=$this->db->select('machine_serial_number')->from('issue_imported_items')->where('item_id',$row->id)->get();
			foreach($isqty->result() as $srnum){
			    $serialnumber = $srnum->machine_serial_number;
			}
				/** ISSUE ITEMS for serial number **/
			
			/** RECIEVE ITEMS **/
			$rcvqty=$this->db->select('sum(qty) as recvqty')->from('imported_items_recieved')->where('itemid',$row->id)->get();
			foreach($rcvqty->result() as $rcvqty1);
			/*** END **/
			
			
			
			$imported_data[] = array('sr_no'=>$i,
			'description'=>$row->instruments_name,
			'current_stock'=>$row->stock,
			'opening_stock'=>'',
			'machine_serial_number'=>$serialnumber,
			'min_stock'=>$row->minstock,
			'total_received'=>$rcvqty1->recvqty,
			'price'=>$row->mvalue,
			'total_issued'=>$isqty1->issueqty,
			'update_status'=>$issueitem,
			'block_item'=>$blockitem."<br><br>".$html,
			'receive_item'=>$receive_item,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($imported_data),
			"iTotalDisplayRecords" => count($imported_data),
			"aaData"=>$imported_data);
			
		echo json_encode($results);
	}
	
	
	
	public function add_imported_item()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('description', 'item name', 'required|trim');
		$this->form_validation->set_rules('qty', 'qty', 'required|trim');
		$this->form_validation->set_rules('min_qty', 'Qty', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('store/imported_items');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "imported_items";
		$query = $this->db->select('description')->from('imported_items')->where('description',strtoupper($this->input->post('description')))->get();
		$res = $query->result();
		
		
		if($res){
			
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry! This record already exist.</div>');
			redirect(page_url.'Store/imported_items');
			
		}else{
	
				
			$data = array('description'=>strtoupper($this->input->post('description')),
			'opening_stock'=>$this->input->post('qty'),
			'min_stock'=>$this->input->post('min_qty'),
			'current_stock'=>$this->input->post('current_stock'),
			'added_by'=>$user_id,
			'price'=>$this->input->post('price'),
			'added_on'=>$date);
			
		$result  = $this->db->insert($table,$data);
		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Store/imported_items');		
		
		}
		
	}
		
	}
	
	public function edit_imported_items(){
		$this->load->view('store/edit_imported_items');
	}
	
	public function update_imported_items()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('description', 'item name', 'required|trim');
		$this->form_validation->set_rules('current_stock', 'current_stock', 'required|trim');
		$this->form_validation->set_rules('qty', 'qty', 'required|trim');
		$this->form_validation->set_rules('min_qty', 'min_qty', 'required|trim');
		$this->form_validation->set_rules('price_per_unit', 'price_per_unit', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('store/edit_imported_items');
		}
		else
		{
			date_default_timezone_set("Asia/Kolkata");
			$date =  date('Y-m-d H:i:s'); 
			$table = "imported_items";
			$data = array('description'=>strtoupper($this->input->post('description')),
			'opening_stock'=>$this->input->post('qty'),
			'min_stock'=>$this->input->post('min_qty'),
			'current_stock'=>$this->input->post('current_stock'),
			'added_by'=>$user_id,
			'price'=>$this->input->post('price_per_unit'),
			'added_on'=>$date);
			$this->db->where('id',$this->uri->segment(3));
			$result  = $this->db->update($table,$data);
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Store/imported_items');		
		
		
		
	}
		
	}
	
	public function issue_items(){
		$this->load->view('store/issue_item');
	}
	
	public function issue_sold_itemoldddd()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('issue_type', 'issue_type', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('store/issue_item');
		}
		else
		{
			date_default_timezone_set("Asia/Kolkata");
			$date =  date('Y-m-d H:i:s'); 
			$table = "issue_imported_items";
			$type = $this->input->post('issue_type');
			if($type=='1'){
				$userid = "0";
				$qty = "0";
				$returndate = date('Y-m-d');
				$remarks= "";
				$companyname = $this->input->post('company_name');
				$sold_qty = $this->input->post('sold_qty');
				$sold_remarks = $this->input->post('sold_remarks');
				
			}else{
				$companyname="";
				$sold_qty="0";
				$sold_remarks="";
				$userid = $this->input->post('user_id');
				$qty = $this->input->post('qty');
				$returndate = date('Y-m-d',strtotime($this->input->post('date')));
				$remarks = $this->input->post('remarks');
			}
			
			$data = array('issue_type'=>$type,
			'item_id'=>$this->uri->segment(3),
			'user_id'=>$userid,
			'qty'=>$qty,
			'return_date'=>$returndate,
			'remarks'=>$remarks,
			'company_name'=>$companyname,
			'sold_qty'=>$sold_qty,
			'sold_remarks'=>$sold_remarks,
			'added_by'=>$user_id,
			'added_on'=>$date);
			$res = $this->db->insert($table,$data);
			if($res){
				$qu = $this->db->select('opening_stock')->from('imported_items')->where('id',$this->uri->segment(3))->get();
				foreach($qu->result() as $row);
				if($sold_qty=='0'){
					$qtty=$qty;
				}else{
					$qtty = $sold_qty;
				}
				$opening_stock = $row->opening_stock-$qtty;
				$data = array('opening_stock'=>$opening_stock);
				$this->db->where('id',$this->uri->segment(3));
				$this->db->update('imported_items',$data);
				
			}
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Store/imported_items');		
		
		
		
	}
		
	}
	
	
		public function issue_sold_item()
	{
		
			date_default_timezone_set("Asia/Kolkata");
			$date =  date('Y-m-d H:i:s'); 
			$table = "issue_imported_items";
			$t = $this->input->post('user');
			
			if($this->input->post('returnable')=='1')
			{
				$rdate=date('Y-m-d',strtotime($this->input->post('rdate')));
				$ono='';
			}else{
				
				$rdate="0000-00-00";
				$ono=$this->input->post('ono');
			}
			
			$itemstock=$this->db->select('stock')->from('presto_instruments')->where('id',$this->uri->segment(3))->get();
				if($itemstock->num_rows()>0)
				{
				foreach($itemstock->result() as $itemstocks);
				$importedmachinestock=$itemstocks->stock;

				}else{ $importedmachinestock=0; }
				
				if($importedmachinestock<>0)
				{
			
				$data = array('item_id'=>$this->uri->segment(3),
				'user_id'=>$this->input->post('user'),
				'qty'=>$this->input->post('qty'),
				'whyneed'=>$this->input->post('whyneed'),
				'machine_serial_number'=>$this->input->post('machine_serial_number'),
				'returnable'=>$this->input->post('returnable'),
				'return_date'=>$rdate,
				'orderno'=>$ono,
				'added_on'=>$date,
				'added_by'=>$_SESSION['logged_in']['user_id']);
				
			$res = $this->db->insert($table,$data);
			if($this->db->affected_rows()>0){
			$curr=$importedmachinestock-$this->input->post('qty');
			$stdata=array('stock'=>$curr);
			$this->db->where('id',$this->uri->segment(3));
			$this->db->update('presto_instruments',$stdata);
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Store/imported_items');	
			
			}else{
				
				
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Unable to issue.</div>');
			redirect(page_url.'Store/imported_items');
				
			}
			}else{
				
				
				$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Unable to issue.</div>');
			redirect(page_url.'Store/imported_items');
				
			}
				
		
		}
	
	
	public function receive_item(){
		$this->load->view('store/receive_item');
	}	
	
	

public function block_itemsolddd()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('party_name', 'party_name', 'required|trim');
		$this->form_validation->set_rules('qty', 'qty', 'required|trim');
		$this->form_validation->set_rules('jobcard', 'jobcard', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('store/block_items');
		}
		else
		{
			date_default_timezone_set("Asia/Kolkata");
			$date =  date('Y-m-d H:i:s'); 
			$table = "imported_item_blocked";
			$party_name = $this->input->post('party_name');
			$qty = $this->input->post('qty');
			$jobcard = $this->input->post('jobcard');
			$remarks = $this->input->post('remarks');
			
			$data = array('party_name'=>$party_name,
			'item_id'=>$this->uri->segment(3),
			'blocked_qty'=>$qty,
			'remarks'=>$remarks,
			'jobcard'=>$jobcard,
			'added_by'=>$user_id,
			'added_on'=>$date);
			$res = $this->db->insert($table,$data);
			if($res){
				$qu = $this->db->select('current_stock')->from('imported_items')->where('id',$this->uri->segment(3))->get();
				foreach($qu->result() as $row);
				$current_stock = $row->current_stock-$qty;
				$data = array('current_stock'=>$current_stock);
				$this->db->where('id',$this->uri->segment(3));
				$this->db->update('imported_items',$data);
				
			}
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Store/imported_items');		
		
		
		
	}
		
	}	

public function block_items()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('party_name', 'party_name', 'required|trim');
		$this->form_validation->set_rules('qty', 'qty', 'required|trim');
		$this->form_validation->set_rules('jobcard', 'jobcard', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('store/block_items');
		}
		else
		{
			date_default_timezone_set("Asia/Kolkata");
			$date =  date('Y-m-d H:i:s'); 
			$table = "imported_item_blocked";
			$party_name = $this->input->post('party_name');
			$qty = $this->input->post('qty');
			$jobcard = $this->input->post('jobcard');
			$remarks = $this->input->post('remarks');
			
			$itemstock=$this->db->select('stock')->from('presto_instruments')->where('id',$this->uri->segment(3))->get();
				if($itemstock->num_rows()>0)
				{
				foreach($itemstock->result() as $itemstocks);
				$importedmachinestock=$itemstocks->stock;

				}else{ $importedmachinestock=0; }
				
				if($importedmachinestock<>0)
			{
					$data = array('party_name'=>$party_name,
					'item_id'=>$this->uri->segment(3),
					'blocked_qty'=>$qty,
					'remarks'=>$remarks,
					'jobcard'=>$jobcard,
					'added_by'=>$user_id,
					'added_on'=>$date);
			$res = $this->db->insert($table,$data);
			if($this->db->affected_rows()>0)
			{


			$current_stock = $importedmachinestock-$qty;
			$data = array('stock'=>$current_stock);
			$this->db->where('id',$this->uri->segment(3));
			$this->db->update('presto_instruments',$data);

			}
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Store/imported_items');	

			}else{

			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Unable to block</div>');
			redirect(page_url.'Store/imported_items');	


			}					
		
		
		
	}
		
	}	



public function blocked_item_listoldd()
	{
		$i=1;
		$imported_data= array();
		$this->db->select('a.*, c.first_name, c.last_name')->from('imported_item_blocked a')->join('system_users c','a.added_by=c.user_id','left')->where('a.item_id',$this->uri->segment(3));
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
		date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			$imported_data[] = array('sr_no'=>$i,
			'party_name'=>$row->party_name,
			'qty'=>$row->blocked_qty,
			'jobcard'=>$row->jobcard,
			'remarks'=>$row->remarks,
			'added_by'=>$row->first_name." ".$row->last_name,
			'added_on'=>$addeddate.$addedtime);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($imported_data),
			"iTotalDisplayRecords" => count($imported_data),
			"aaData"=>$imported_data);
			
		echo json_encode($results);
	}
	
	
	public function blocked_item_list()
	{
		$i=1;
		$imported_data= array();
		$this->db->select('a.*, c.first_name, c.last_name')->from('imported_item_blocked a')->join('system_users c','a.added_by=c.user_id','left')->where('a.item_id',$this->uri->segment(3));
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
		date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			if($row->source==0)
			{
			    $jobcardno=$row->jobcard;
			}else
			{
			   $restj=$this->db->select('job_card_no')->from('order_instruments')->where('id',$row->jobcard)->get();
			   if($restj->num_rows()>0)
			   {
			       foreach($restj->result() as $restj1);
			       $jobcardno=$restj1->job_card_no;
			       
			   }else
			   {
			       $jobcardno='';
			       
			   }
			    
			}
			$imported_data[] = array('sr_no'=>$i,
			'party_name'=>$row->party_name,
			'qty'=>$row->blocked_qty,
			'jobcard'=>$jobcardno,
			'remarks'=>$row->remarks,
			'added_by'=>$row->first_name." ".$row->last_name,
			'added_on'=>$addeddate.$addedtime);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($imported_data),
			"iTotalDisplayRecords" => count($imported_data),
			"aaData"=>$imported_data);
			
		echo json_encode($results);
	}
	
	
	
	
public function issue_imported_itemsoldd()
	{
		$i=1;
		$imported_data= array();
		$this->db->select('a.qty, a.return_date, a.remarks,a.added_on, a.added_by, b.first_name, b.last_name, c.first_name as fname, c.last_name as lname')->from('issue_imported_items a')->join('system_users b','a.user_id=b.user_id','left')->join('system_users c','a.added_by=c.user_id','left')->where('a.item_id',$this->uri->segment(3))->where('a.issue_type','2');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
		date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			$imported_data[] = array('sr_no'=>$i,
			'user'=>$row->first_name." ".$row->last_name,
			'qty'=>$row->qty,
			'return_date'=>$row->return_date,
			'remarks'=>$row->remarks,
			'added_by'=>$row->fname." ".$row->lname,
			'assigned_to'=>$addeddate.$addedtime);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($imported_data),
			"iTotalDisplayRecords" => count($imported_data),
			"aaData"=>$imported_data);
			
		echo json_encode($results);
	}	


public function issue_imported_items()
	{
		$i=1;
		$imported_data= array();
		$this->db->select('a.qty, a.return_date, a.remarks,a.added_on, a.added_by, b.first_name, b.last_name, c.first_name as fname, c.last_name as lname')->from('issue_imported_items a')->join('system_users b','a.user_id=b.user_id','left')->join('system_users c','a.added_by=c.user_id','left')->where('a.item_id',$this->uri->segment(3))->where('a.issue_type','2');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
		date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			$imported_data[] = array('sr_no'=>$i,
			'user'=>$row->first_name." ".$row->last_name,
			'qty'=>$row->qty,
			'return_date'=>$row->return_date,
			'remarks'=>$row->remarks,
			'added_by'=>$row->fname." ".$row->lname,
			'assigned_to'=>$addeddate.$addedtime);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($imported_data),
			"iTotalDisplayRecords" => count($imported_data),
			"aaData"=>$imported_data);
			
		echo json_encode($results);
	}	
	
	
	
		
public function issue_item_list()
	{
		$i=1;
		$imported_data= array();
		$this->db->select('a.*, b.instruments_name')->from('issue_imported_items a')->join('presto_instruments b','a.item_id=b.id','left');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
		date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			if($row->returnable=='1')
			{
				$ret="YES";
				$retdate=date('d-m-Y',strtotime($row->return_date));
				$odno='';
			}else{
				$ret="NO";
				$retdate='NA';
				$odno=$row->orderno;
			}
			
			$to=$this->getsusername($row->user_id);
			$from=$this->getsusername($row->added_by);
			
			$imported_data[] = array('sr_no'=>$i,
			'party_name'=>$to,
			'instruments_name'=>$row->instruments_name,
			'qty'=>$row->qty,
			'machine_serial_number'=>$row->machine_serial_number,
			'issuereason'=>$row->whyneed,
			'returntype'=>$ret,
			'returndate'=>$retdate,
			'ordertype'=>$odno,
			'added_by'=>$from,
			'added_on'=>date('d-m-Y H:i:s',strtotime($row->added_on)));
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($imported_data),
			"iTotalDisplayRecords" => count($imported_data),
			"aaData"=>$imported_data);
			
		echo json_encode($results);
	}
	
	
	
		
	public function imported_stock_form()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('item_name', 'item_name', 'required|trim');
		$this->form_validation->set_rules('returnable', 'returnable', 'required|trim');
		$this->form_validation->set_rules('why_do_you', 'why_do_you', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('store/imported_stock_form');
		}
		else
		{
			date_default_timezone_set("Asia/Kolkata");
			$date =  date('Y-m-d H:i:s'); 
			$table = "imported_item_request";
			
			$data = array('item_id'=>$this->input->post('item_name'),
			'returnable'=>$this->input->post('returnable'),
			'why_do_you_need_it'=>$this->input->post('why_do_you'),
			'serial_number'=>$this->input->post('serial_number'),
			'qty'=>$this->input->post('qty'),
			'added_by'=>$user_id,
			'added_on'=>$date);
			$res = $this->db->insert($table,$data);
			
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Store/imported_stock_form');		
		
		
		
	}
		
	}
	
	
	function getsusername($userid)
	{
	    $alluser='';
		$rest=$this->db->select('first_name,last_name')->from('system_users')->where('user_id',$userid)->order_by('first_name','ASC')->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $rest1)
			{
				//$alluser[]=$rest1->first_name." ".$rest1->last_name;
				$alluser=strtoupper($rest1->first_name);
			}
			
			
			return $alluser;
		}else
		{
			return $alluser;
		}
		
	}
	
	
	function machineparts()
{
	
	$this->load->view('store/machine_part_data_with_picture');
}




	public function add_parts()
	{
		
		$this->form_validation->set_error_delimiters('<span style="color:red;font-size:12px">&nbsp;', '</span>');
		$this->form_validation->set_rules('machine_part', 'Machine Part', 'required|trim');
		$this->form_validation->set_rules('rack_location', 'Rack Location', 'required|trim');
		$this->form_validation->set_rules('category_id', 'Catgeory', 'required|trim');
		$this->form_validation->set_rules('raw_bop', 'RAW/BOP Type', 'required|trim');
		$this->form_validation->set_rules('unit', 'Unit', 'required|trim');
		$this->form_validation->set_rules('fincode', 'Finsys Code', 'required|trim');
		$this->form_validation->set_rules('stock', 'Stock', 'required|trim');
		$this->form_validation->set_rules('minstock', 'Minimum Stock', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('store/add_machine_parts');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "machine_parts_with_picture";
			
			$photo=$_FILES['picture']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$pic=$this->input->post('fincode').'.'.$cat_image;
				move_uploaded_file($_FILES["picture"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/' . $pic);
			}else
			{
				$pic="";
				}
				
				if($this->input->post('critical')=='')
				{
				    $upd=0;
				}else
				{
				$upd=1;
				}
			$data = array('part'=>$this->input->post('machine_part'),
			'specification'=>$this->input->post('specification'),
			'category_id'=>$this->input->post('category_id'),
			'makes'=>$this->input->post('make'),
			'size_in_mm'=>$this->input->post('size_in_mm'),
			'material'=>$this->input->post('material'),
			'hsn'=>$this->input->post('hsn'),
			'fincode'=>$this->input->post('fincode'),
			'qty'=>$this->input->post('qty'),
			'raw_bop'=>$this->input->post('raw_bop'),
			'location_id'=>$this->input->post('rack_location'),
			'picture'=>$pic,
			'unit'=>$this->input->post('unit'),
			'current_stock'=>$this->input->post('stock'),
			'min_stock'=>$this->input->post('minstock'),
			'status'=>'1',
			'critical'=>$upd,
			'addedOn'=>date('Y-m-d H:i:s'),
			'addedBy'=>$_SESSION['logged_in']['user_id']);
			
		$result  = $this->db->insert($table,$data);	
		$lid = $this->db->insert_id();	
		if($this->db->affected_rows()>0)
		{
			/** ADD SUPPLIERS **/
		/**	$vendor=$this->input->post('vendor');
			
			$price=$this->input->post('price');
			$lprice=$this->input->post('lprice');
			$discount=$this->input->post('discount');
			for($i=0;$i<count($vendor);$i++)
			{
				if(trim($vendor[$i])<>'' && trim($price[$i]<>'')  && trim($lprice[$i]<>''))
				{
				
				$vdata=array('masterid'=>$lid,'itemid'=>$lid,'vendorid'=>$vendor[$i],'listprice'=>$lprice[$i],'discount'=>$discount[$i],'price'=>$price[$i]);
				
				$this->db->insert('vendors_price',$vdata);
				
				}
				
				
			}
			**/
			/** END **/
			
			
			
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Store/machineparts');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Store/add_parts');
		}
		
		
	}
		
	}
	
	
	
		public function machine_part_data_with_picture_list()
	{
		$i=1;
		$machinepart_data= array();
		$this->db->select('a.id,a.part,a.unit,a.current_stock,a.min_stock, a.specification, a.makes, a.size_in_mm, a.material, a.raw_bop, a.fincode, a.qty, a.picture, d.rack_location, p.category')->from('machine_parts_with_picture  a')->join('store_rack_location d','a.location_id=d.id','left')->join('presto_machine_part_category p','a.category_id=p.id','left');
	
//	$this->db->select('a.id,a.part,a.unit,a.current_stock,a.min_stock, a.specification, a.makes, a.size_in_mm, a.material, a.raw_bop, a.fincode, a.qty, a.picture, d.rack_location, p.category')->from('machine_bom w')->join('machine_parts_with_picture  a','w.partid=a.id')->join('store_rack_location d','a.location_id=d.id','left')->join('presto_machine_part_category p','a.category_id=p.id','left')->group_by('w.partid');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			
		$edit = "<a href='".page_url."Store/edit_parts/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			
		if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$row->fincode.'.jpg')){
			    
				$IMG = product_items.$row->fincode.'.jpg';
				$image = "<img src='".$IMG."' style='width:100px;height:100px;'>";
			}else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$row->fincode.'.JPG')){
			    
				$IMG = product_items.$row->fincode.'.JPG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$row->fincode.'.jpeg')){
			    	$IMG = product_items.$row->fincode.'.jpeg';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
		    }else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$row->fincode.'.JPEG')){
			    
				$IMG = product_items.$row->fincode.'.JPEG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else{
				$image="";
			}
			
			$unitname=$this->getreturnunit($row->unit);
			$vendor=$this->getreturnvendorandprice($row->id);
			
			/** GET STOCK WISE COLOR **/
			
			$curst=$row->current_stock;
			$min=$row->min_stock;
			
            /** 33 PER **/
            
            $thr33=$min*0.33;
            
            /** 62 PER **/
            $thr62=$min*0.62;
            
            
            /** 100 PER **/
            $thr100=$min*1;
			
			
			if($curst<=0.00)
			{
				$st="BLACK";
			}
			
		
			
			if($curst>'0.00' && $curst<=$thr62)
			{
			$st="RED";	
			}
			
			
			if($curst>$thr33 && $curst<=$thr62)
			{
				$st="YELLOW";
			}
			
			
			if($curst>$thr62 && $curst<=$thr100)
			{
				$st="GREEN";
			}
			
			
			if($curst>$row->min_stock)
			{
				$st="BLUE";
			}
			
			
			
			/** END **/
			
			$machinepart_data[] = array('sr_no'=>$i,
			'category'=>$row->category,
			'machine_part'=>$row->part,
			'specification'=>$row->specification,
			'makes'=>$row->makes,
			'size_in_mm'=>$row->size_in_mm,
			'material'=>$row->material,
			'raw_bop'=>$row->raw_bop,
			'fincode'=>$row->fincode,
			'unit'=>$unitname,
			'stock'=>$row->current_stock,
			'minstock'=>$row->min_stock,
			'minstockstatus'=>$st,
			'qty'=>$row->qty,
			'location'=>$row->rack_location,
			'vendor'=>$vendor,
			'image'=>$image,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($machinepart_data),
			"iTotalDisplayRecords" => count($machinepart_data),
			"aaData"=>$machinepart_data);
			
		echo json_encode($results);
	}

	
	public function machine_part_data_with_picture_listOlddddddddddddddddddd()
	{
		$i=1;
		$machinepart_data= array();
		$this->db->select('a.id,a.part,a.unit,a.current_stock,a.min_stock, a.specification, a.makes, a.size_in_mm, a.material, a.raw_bop, a.fincode, a.qty, a.picture, d.rack_location, p.category')->from('machine_parts_with_picture  a')->join('store_rack_location d','a.location_id=d.id','left')->join('presto_machine_part_category p','a.category_id=p.id','left');
	
//	$this->db->select('a.id,a.part,a.unit,a.current_stock,a.min_stock, a.specification, a.makes, a.size_in_mm, a.material, a.raw_bop, a.fincode, a.qty, a.picture, d.rack_location, p.category')->from('machine_bom w')->join('machine_parts_with_picture  a','w.partid=a.id')->join('store_rack_location d','a.location_id=d.id','left')->join('presto_machine_part_category p','a.category_id=p.id','left')->group_by('w.partid');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			
		$edit = "<a href='".page_url."Store/edit_parts/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			
		if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$row->fincode.'.jpg')){
			    
				$IMG = product_items.$row->fincode.'.jpg';
				$image = "<img src='".$IMG."' style='width:100px;height:100px;'>";
			}else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$row->fincode.'.JPG')){
			    
				$IMG = product_items.$row->fincode.'.JPG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$row->fincode.'.jpeg')){
			    	$IMG = product_items.$row->fincode.'.jpeg';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
		    }else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$row->fincode.'.JPEG')){
			    
				$IMG = product_items.$row->fincode.'.JPEG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else{
				$image="";
			}
			
			$unitname=$this->getreturnunit($row->unit);
			$vendor=$this->getreturnvendorandprice($row->id);
			
			$machinepart_data[] = array('sr_no'=>$i,
			'category'=>$row->category,
			'machine_part'=>$row->part,
			'specification'=>$row->specification,
			'makes'=>$row->makes,
			'size_in_mm'=>$row->size_in_mm,
			'material'=>$row->material,
			'raw_bop'=>$row->raw_bop,
			'fincode'=>$row->fincode,
			'unit'=>$unitname,
			'stock'=>$row->current_stock,
			'minstock'=>$row->min_stock,
			'qty'=>$row->qty,
			'location'=>$row->rack_location,
			'vendor'=>$vendor,
			'image'=>$image,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($machinepart_data),
			"iTotalDisplayRecords" => count($machinepart_data),
			"aaData"=>$machinepart_data);
			
		echo json_encode($results);
	}

	
	
	
	function getreturnvendorandprice($masterid)
	{
		$html='';
		
		$deta=$this->db->select('a.price,b.name')->from('vendors_price a')->join('vendors b','a.vendorid=b.id')->where('a.masterid',$masterid)->get();
		if($deta->num_rows()>0)
		{
			foreach($deta->result() as $deta1)
			{
				
				$html.=$deta1->name."-".floatval($deta1->price)."<br/>";
			}
			
		}
		
		return $html;
		
	}




function edit_parts()
{
	
	
	$this->load->view('store/edit_machine_parts');
}




	public function update_parts()
	{
		
		$id=$this->uri->segment(3);
		$this->form_validation->set_error_delimiters('<span style="color:red;font-size:12px">&nbsp;', '</span>');
		$this->form_validation->set_rules('machine_part', 'Machine Part', 'required|trim');
		$this->form_validation->set_rules('rack_location', 'Rack Location', 'required|trim');
		$this->form_validation->set_rules('category_id', 'Catgeory', 'required|trim');
		$this->form_validation->set_rules('raw_bop', 'RAW/BOP Type', 'required|trim');
		$this->form_validation->set_rules('unit', 'Unit', 'required|trim');
		$this->form_validation->set_rules('fincode', 'Finsys Code', 'required|trim');
		$this->form_validation->set_rules('stock', 'Stock', 'required|trim');
		$this->form_validation->set_rules('minstock', 'Minimum Stock', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('store/add_machine_parts');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "machine_parts_with_picture";
		$oldimg=$this->input->post('oldimage');
			
			$photo=$_FILES['picture']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$picss=$this->input->post('fincode').'.'.$cat_image;
			
			
		
               move_uploaded_file($_FILES['picture']['tmp_name'],$_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$picss);
			//	if (file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$oldimg)) {
				//unlink($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$oldimg);
				//}
			}else
			{
			  
				$picss=$this->input->post('fincode').'.'.$cat_image;
			}
			
			if($this->input->post('critical')=='')
			{
			    $upd="0";
			}else
			{
			    $upd="1";
			}

			$data = array('part'=>$this->input->post('machine_part'),
			'specification'=>$this->input->post('specification'),
			'category_id'=>$this->input->post('category_id'),
			'makes'=>$this->input->post('make'),
			'size_in_mm'=>$this->input->post('size_in_mm'),
			'material'=>$this->input->post('material'),
			'fincode'=>$this->input->post('fincode'),
			'qty'=>$this->input->post('qty'),
			'raw_bop'=>$this->input->post('raw_bop'),
			'location_id'=>$this->input->post('rack_location'),
			'picture'=>$picss,
			'unit'=>$this->input->post('unit'),
			'current_stock'=>$this->input->post('stock'),
			'hsn'=>$this->input->post('hsn'),
			'min_stock'=>$this->input->post('minstock'),
			'status'=>'1',
			'critical'=>$upd,
			'addedOn'=>date('Y-m-d H:i:s'),
			'addedBy'=>$_SESSION['logged_in']['user_id']);
			

			$this->db->where('id',$id);
		 $this->db->update($table,$data);	
		$lid = $id;	
		if($this->db->affected_rows()>0)
		{
			/** ADD SUPPLIERS **/
			if($this->input->post('addsuppl')=='1')
			{
			$vendor=$this->input->post('vendor');
			
			$lprice=$this->input->post('lprice');
			$discount=$this->input->post('discount');
			$price=$this->input->post('price');
			for($i=0;$i<count($vendor);$i++)
			{
				if(trim($vendor[$i])<>'' && trim($price[$i]<>'') && trim($lprice[$i]))
				{
				
				$vdata=array('masterid'=>$lid,'itemid'=>$lid,'vendorid'=>$vendor[$i],'listprice'=>$lprice[$i],'discount'=>$discount[$i],'price'=>$price[$i]);
				
				$this->db->insert('vendors_price',$vdata);
				
				}
				
				
			}
			}
			
			/** END **/
			
			
			/** UPDATE EXSITING SUPPLIERS **/
		
			  
			$exissuppl=$this->input->post('exissuppl');
		
			
			for($j=0;$j<count($exissuppl);$j++)
			{
				$exid=$exissuppl[$j];
			$vendor=$this->input->post('exivendor'.$exid);
				$lprice=$this->input->post('exilprice'.$exid);
				$discount=$this->input->post('exidiscount'.$exid);
				$price=$this->input->post('exiprice'.$exid);
				if(trim($vendor)<>'' && trim($price<>''))
				{
				
				$vdata=array('masterid'=>$lid,'itemid'=>$lid,'vendorid'=>$vendor,'listprice'=>$lprice,'discount'=>$discount,'price'=>$price);
			
				$this->db->where('id',$exid);
				$this->db->update('vendors_price',$vdata);
				
				
				}
				
				
			}
			
		
			/** END **/
			
			
			
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Store/machineparts');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Store/add_parts');
		}
		
		
	}
		
	}
	
	
	
	function deletesupplier()
	{
		$rowid=$this->uri->segment(3);
		$recid=$this->uri->segment(4);
		
		$this->db->where('id',$rowid);
		$this->db->delete('vendors_price');
		
		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Supplier Deleted.</div><br/>');
		redirect(page_url.'Store/edit_parts/'.$recid);
		
	}
	
	
	
		function bom()
	{
		
		$this->load->view('store/instrumentbom');
		
	}
	
	
	
		function getallpartsOld()
	{
		 $prevbom=array();
		$searchtrm= $_GET['q'];
	
	$mid=$this->uri->segment(3);
	$pbom=$this->db->select('partid')->from('machine_bom')->where('mid',$mid)->get();
	if($pbom->num_rows()>0)
	{
		foreach($pbom->result() as $pboms)
		{
			$prevbom[]=$pboms->partid;
		}
	}
	
	
	$this->db->select('part,specification,id')->from('machine_parts_with_picture
	')->like('part',$searchtrm);
	if(count($prevbom)>0)
	{
		$result = "'" . implode ( "', '", $prevbom ) . "'";
		$this->db->where_not_in('id',$result,false);
	}

	$que=$this->db->get();
	if($que->num_rows()>0)
	{
		foreach($que->result() as $itemdata)
		{
			$json[] = array('id'=>$itemdata->id, 'text'=>$itemdata->part."-".$itemdata->specification);
		}
	}else{
	$json[] = array('id'=>"", 'text'=>"No Data Available");
	}
	
	echo json_encode($json);
		
		
		
	}
	
	function getallparts()
	{
		 $prevbom=array();
		$searchtrm= $_GET['q'];
	
	$mid=$this->uri->segment(3);
	$pbom=$this->db->select('partid')->from('machine_bom')->where('mid',$mid)->get();
	if($pbom->num_rows()>0)
	{
		foreach($pbom->result() as $pboms)
		{
			$prevbom[]=$pboms->partid;
		}
	}
	
	
	$this->db->select('fincode,part,specification,id')->from('machine_parts_with_picture
	')->like('fincode',$searchtrm,'both',false);
	if(count($prevbom)>0)
	{
		$result = "'" . implode ( "', '", $prevbom ) . "'";
		$this->db->where_not_in('id',$result,false);
	}

	$que=$this->db->get();
	if($que->num_rows()>0)
	{
		foreach($que->result() as $itemdata)
		{
			$json[] = array('id'=>$itemdata->id, 'text'=>$itemdata->fincode);
		}
	}else{
	$json[] = array('id'=>"", 'text'=>"No Data Available");
	}
	
	echo json_encode($json);
		
		
		
	}
	
	
	function update_bom()
	{
		
		$p=$this->input->post('parts');
		$qty=$this->input->post('qty');
		$drawing=$this->input->post('draw');
		$mid=$this->uri->segment('3');
		for($i=0;$i<count($p);$i++)
		{
			
			$data=array('mid'=>$mid,'partid'=>$p[$i],'qty'=>$qty[$i],'drawingno'=>$drawing[$i],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
			
			$this->db->insert('machine_bom',$data);
			
		}
		
		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">BOM Updated</div><br/>');
		redirect(page_url.'Store/bom/'.$mid);
		
	}
	
		function update_bomOld()
	{
		
		$p=$this->input->post('parts');
		$mid=$this->uri->segment('3');
		for($i=0;$i<count($p);$i++)
		{
			$data=array('mid'=>$mid,'partid'=>$p[$i],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
			
			$this->db->insert('machine_bom',$data);
			
		}
		
		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">BOM Updated</div><br/>');
		redirect(page_url.'Store/bom/'.$mid);
		
	}
	
	
	
	
		function deltebommaterial()
	{
		
		$partid=$this->uri->segment('3');
		$mid=$this->uri->segment('4');

		$this->db->where('id',$partid);
		$this->db->delete('machine_bom');
		
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">BOM Updated</div><br/>');
		redirect(page_url.'Store/bom/'.$mid);
		
	}
	
	public function cancel_order()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('remarks', 'remarks', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('store/cancel_order');
		}
		else
		{
			date_default_timezone_set("Asia/Kolkata");
			$date =  date('Y-m-d H:i:s'); 
			$table = "imported_item_blocked";
			$qty = $this->input->post('blocked_qty');
			
			
		
			$itemstock=$this->db->select('stock')->from('presto_instruments')->where('id',$this->uri->segment(3))->get();
				if($itemstock->num_rows()>0)
				{
				foreach($itemstock->result() as $itemstocks);
				$importedmachinestock=$itemstocks->stock;

				}else{ $importedmachinestock=0; }
				
		
			$current_stock = $importedmachinestock+$qty;
			$data = array('stock'=>$current_stock);
			$this->db->where('id',$this->uri->segment(4));
			$this->db->update('presto_instruments',$data);
			

			$data = array('cancelled_order'=>'1',
			'cancellation_remarks'=>$this->input->post('remarks'));
			
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update('imported_item_blocked',$data);
			if($res){
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, Order successfully cancelled.</div>');
			redirect(page_url.'Store/imported_items');		
			}
		
		
	}
		
	}

public function blocked_item_list_cancel_report()
	{
		$i=1;
		$imported_data= array();
		$this->db->select('a.*, c.first_name, c.last_name')->from('imported_item_blocked a')->join('system_users c','a.added_by=c.user_id','left')->where('a.item_id',$this->uri->segment(3))->where('a.cancelled_order','1');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
		date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			if($row->source==0)
			{
			    $jobcardno=$row->jobcard;
			}else
			{
			   $restj=$this->db->select('job_card_no')->from('order_instruments')->where('id',$row->jobcard)->get();
			   if($restj->num_rows()>0)
			   {
			       foreach($restj->result() as $restj1);
			       $jobcardno=$restj1->job_card_no;
			       
			   }else
			   {
			       $jobcardno='';
			       
			   }
			    
			}
			
			$imported_data[] = array('sr_no'=>$i,
			'party_name'=>$row->party_name,
			'qty'=>$row->blocked_qty,
			'jobcard'=>$jobcardno,
			'remarks'=>$row->remarks,
			'cancellation_remarks'=>$row->cancellation_remarks,
			'added_by'=>$row->first_name." ".$row->last_name,
			'added_on'=>$addeddate.$addedtime);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($imported_data),
			"iTotalDisplayRecords" => count($imported_data),
			"aaData"=>$imported_data);
			
		echo json_encode($results);
	}
	
	
	
	function recieveitem()
	{
		$itemstock=$this->db->select('stock')->from('presto_instruments')->where('id',$this->uri->segment(3))->get();
			if($itemstock->num_rows()>0)
			{
			foreach($itemstock->result() as $itemstocks);
			$importedmachinestock=$itemstocks->stock;

			}else{
				
				echo "IMPORTED ITEM NOT FOUND";exit;
			}
		
	$data=array('itemid'=>$this->uri->segment(3),
	'rtype'=>$this->input->post('rtype'),
	'qty'=>$this->input->post('qty'),'remarks'=>$this->input->post('remarks'),'serial_number'=>$this->input->post('serialnumber'),'addedOn'=>date('Y-m-d H:i:s'),
	'addedBy'=>$_SESSION['logged_in']['user_id']);
	$this->db->insert('imported_items_recieved',$data);
	if($this->db->affected_rows()>0)
	{
		$newstock=$importedmachinestock+$this->input->post('qty');
			
		$data1=array('stock'=>$newstock);
		$this->db->where('id',$this->uri->segment(3));
		$this->db->update('presto_instruments',$data1);		
				
	$this->session->set_flashdata('message','Record Added');
	redirect(page_url.'Store/receive_item/'.$this->uri->segment(3));
	}else{
		$this->session->set_flashdata('message','Unable to add');
	redirect(page_url.'Store/receive_item/'.$this->uri->segment(3));
	}
	
	}
	
function allrecieveditems()
	{
		$imported_data=array();
		$id=$this->uri->segment(3);
		$restyu=$this->db->select('a.*,b.instruments_name')->from('imported_items_recieved a')->join('presto_instruments b','a.itemid=b.id','left')->order_by('id','DESC')->get();
		if($restyu->num_rows()>0)
		{
			$i=1;
			foreach($restyu->result() as $row)
			{
					if($row->rtype==1)
					{
					$r="New Purchased Item";
					}else{
					$r="Returnable Item";
					}
				
				$to=$this->getsusername($row->addedBy);
				
				$edit= "<a href='".page_url."Store/received_items_edit/".$row->id."'><i class='fa fa-pencil-square-o'></i></a>";
					
		$imported_data[] = array('sr_no'=>$i,
		'instruments_name'=>$row->instruments_name,
			'rtype'=>$r,
			'qty'=>$row->qty,
			'remarks'=>$row->remarks,
			'serial_number'=>$row->serial_number,
			'addedBy'=>$to,
			'addedOn'=>date('d-m-Y g:i A',strtotime($row->addedOn)),
			'action'=>$edit);
			$i++;
		}
	}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($imported_data),
			"iTotalDisplayRecords" => count($imported_data),
			"aaData"=>$imported_data);
			
		echo json_encode($results);
		
	}
	
	
	function imported_stock_request()
	{
		
		$this->load->view('store/imported_stock_request');
		
	}
	
	function listimported_stock_request()
	{
		
		$imported_data=array();
		$id=$this->uri->segment(3);
		$restyu=$this->db->select('a.*,b.instruments_name')->from('imported_item_request a')->join('presto_instruments b','a.item_id=b.id')->order_by('status','ASC')->order_by('a.added_on','DESC')->get();
		if($restyu->num_rows()>0)
		{
			$i=1;
			foreach($restyu->result() as $row)
			{
					if($row->returnable==1)
					{
					$r="Yes";
					}else{
					$r="No";
					}
					if($row->status==1)
					{
											$closeby=$this->getsusername($row->closedby);
						$sta="<span class=='btn btn-xs btn-success'>Closed</span>";
						
					}else{
						
							$closeby='';
						$sta="<a href='javascript:;' onclick='markdone(".$row->id.");'><span class='btn btn-xs btn-warning'>Mark Done</span></a>";
						
					}
				
				$to=$this->getsusername($row->added_by);
				

					
		$imported_data[] = array('sr_no'=>$i,
			'status'=>$sta,
			'closedby'=>$closeby,
			'item'=>$row->instruments_name,
			'qty'=>$row->qty,
			'return'=>$r,
			'purpose'=>$row->why_do_you_need_it,
			'serial_number'=>$row->serial_number,
			'addedBy'=>$to,
			'addedOn'=>date('d-m-Y g:i A',strtotime($row->added_on)));
			$i++;
		}
	}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($imported_data),
			"iTotalDisplayRecords" => count($imported_data),
			"aaData"=>$imported_data);
			
		echo json_encode($results);
		
		
		
	}
	
	
	function closerequest()
	{
		
		$id=$this->uri->segment(3);
		
		$data=array('status'=>'1','closedby'=>$_SESSION['logged_in']['user_id']);
		$this->db->where('id',$id);
		$this->db->update('imported_item_request',$data);
		
		$this->session->set_flashdata('message','Request Closed');
		redirect(page_url.'Store/imported_stock_request');
		
	}
	
	
	function createpo()
{
	
	
	$this->load->view('store/createpodashboard');
	
	
}

function getitemprice()
{
	$supid=$this->input->post('suppid');
	$partid=$this->input->post('partid');
	$masterid=$this->input->post('masterid');
	
	$pri=$this->db->select('price')->from('vendors_price')->where('masterid',$masterid)->where('itemid',$partid)->where('vendorid',$supid)->get();
	if($pri->num_rows()>0)
	{
		foreach($pri->result() as $price);
		
		echo floatval($price->price);
		
		
		
	}else{
		
		echo "NA";
	}



}



function generatepo()
	{
		$type=$this->input->post('type');
		$supp=$this->input->post('supplier');
		$prno=$this->input->post('pr');
		$source=$this->input->post('source');
		$sourceid=$this->input->post('sourceid');
		$insid=$this->input->post('instrumentid');
		
		if($source==1)
		{
		$jobcard=$this->input->post('jobcardno');
		}else{
			
			$jobcard=0;
		}
		
		if(count($supp)>0)
		{
			
			$usupp=array_values(array_unique($supp));
			$pon=array();
			for($i=0;$i<count($usupp);$i++)
			{
			$ponumber=$this->getnextponumber();
			$supplid=$usupp[$i];
			$itemss=$this->input->post('item'.$supplid);
			for($t=0;$t<count($itemss);$t++)
			{
			$item=$itemss[$t];
			$qty=$this->input->post('qty'.$item);
			$iprice=$this->input->post('itemprice'.$item);
			$currstock=$this->input->post('currentstock'.$item);
			$unit=$this->input->post('unit'.$item);
			if($item<>'' && $qty<>'' && $iprice<>'')
			{
			$pon[]=$ponumber;
			$data=array('itemid'=>$item,'price'=>$iprice,'qty'=>$qty,'vendor'=>$supplid,'pono'=>$ponumber,'prno'=>$prno,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id'],'source'=>$source,'sourceid'=>$sourceid,'jobcard'=>$jobcard,'unit'=>$unit,'potype'=>$type);

			$this->db->insert('purchase_order',$data);
			}else{

			echo "Corrupted Request";exit;
			}
			
			if($source==1)
			{
			/** UPDATE PR IN BLOCK ITEM AGAINST JOBCARD **/
			$dblock=array('updatedOn'=>date('Y-m-d H:i:s'),'pono'=>$ponumber);
			$this->db->where('itemid',$item);
			$this->db->where('machineid',$insid);
			$this->db->where('jobcardid',$jobcard);
			$this->db->update('blockedstock',$dblock);
			/** END **/
			}


			}

			}

			$data2=array('approvalstatus'=>'1','approvaldate'=>date('Y-m-d H:i:s'),'approvedBy'=>$_SESSION['logged_in']['user_id']);
			$this->db->where('prno',$prno);
			$this->db->update('purchase_request',$data2);


			$c=count($usupp);
			if(count($pon)>0)
			{
			$gpono=base64_encode(implode(',',$pon));
			}else{

			$gpono='';			
			}


			$this->session->set_flashdata('message','<div class="alert alert-success">'.$c.' Po(s) generated</div>');
			redirect(page_url.'Reporting/pendingpoforapproval/'.$gpono);

		}else{

			$this->session->set_flashdata('response','<div class="alert alert-danger">Unable to generate PO</div>');
			redirect(page_url.'Store/createpo/'.$prno);

			}
		
	}
	
	
function generatepoOlddd()
	{
		$supp=$this->input->post('supplier');
		$prno=$this->input->post('pr');
		$source=$this->input->post('source');
		$insid=$this->input->post('instrumentid');
		
		if($source==1)
		{
		$jobcard=$this->input->post('jobcardno');
		}else{
			
			$jobcard=0;
		}
		//echo "<pre>"; print_r($supp);exit;
		if(count($supp)>0)
		{
			$usupp=array_values(array_unique($supp));
			$pon=array();
			for($i=0;$i<count($usupp);$i++)
			{
				$ponumber=$this->getnextponumber();
				$supplid=$usupp[$i];
				$itemss=$this->input->post('item'.$supplid);
				for($t=0;$t<count($itemss);$t++)
				{
					$item=$itemss[$t];
					$qty=$this->input->post('qty'.$item);
					$iprice=$this->input->post('itemprice'.$item);
					$currstock=$this->input->post('currentstock'.$item);
					$unit=$this->input->post('unit'.$item);
					if($item<>'' && $qty<>'' && $iprice<>'')
					{
						$pon[]=$ponumber;
					$data=array('itemid'=>$item,'price'=>$iprice,'qty'=>$qty,'vendor'=>$supplid,'pono'=>$ponumber,'prno'=>$prno,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id'],'source'=>$source,'jobcard'=>$jobcard,'unit'=>$unit);
					
					$this->db->insert('purchase_order',$data);
					}else{
						
						echo "Corrupted Request";exit;
					}
				
				
					/** BLOCK ITEM AGAINST JOBCARD **/
						$dblock=array('itemid'=>$item,'machineid'=>$insid,'jobcardid'=>$jobcard,'stock'=>$qty,'stockthattime'=>$currstock,'active'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id'],'updatedOn'=>date('Y-m-d H:i:s'));
						 $this->db->insert('blockedstock',$dblock);
						/** END **/
					
					
				}
				
			}
			
			$data2=array('approvalstatus'=>'1','approvaldate'=>date('Y-m-d H:i:s'),'approvedBy'=>$_SESSION['logged_in']['user_id']);
			$this->db->where('prno',$prno);
			$this->db->update('purchase_request',$data2);
			
		
			$c=count($usupp);
				if(count($pon)>0)
				{
				$gpono=base64_encode(implode(',',$pon));
				}else{

				$gpono='';			
				}
			
			
			$this->session->set_flashdata('message',$c.' Po(s) generated');
			redirect(page_url.'Reporting/pendingpoforapproval/'.$gpono);
			
		}else{
			
			$this->session->set_flashdata('response','Unable to generate PO');
			redirect(page_url.'Store/createpo/'.$prno);
			
		}
		
	}
	
	
	
		function getnextponumber()
	{
		$prnos=$this->db->select('id,pono')->from('purchase_order')->group_by('pono')->order_by('id','DESC')->limit(1)->get();
	$num=$prnos->num_rows();
	if($num==0)
	{
		$num1=1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPO'.$num_padded;
	}else{
	     foreach($prnos->result() as $prnoss);
		 //echo $prnoss->pono; exit;
	    $numpart = (int) filter_var($prnoss->pono, FILTER_SANITIZE_NUMBER_INT);
	    
	   
		$num1=$numpart+1;
		
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPO'.$num_padded;
		//echo $code; exit;
		
	}
		
		return $code;
		
	}
	
	function po()
	{
		
		$this->load->view('store/poinvoice');
	}
	
	
	
	function mrn()
	{
		
		$this->load->view('store/mrndashboard');
		
		
	}
	
	
	function creategateentry()
	{
		
		$iitem=$this->input->post('item');
		$po=$this->input->post('po');
		$vendor=$this->input->post('vendor');
		$gateentryno=$this->input->post('gateentryno');
		$source=$this->input->post('source');
		$jobcard=$this->input->post('jobcardno');
		$mid=$this->input->post('instrumentid');
		if(count($iitem)>0)
		{

				for($i=0;$i<count($iitem);$i++)
				{
					
					$itemid=$iitem[$i];
					$recvqty=$this->input->post('recvqty'.$itemid);
					$reqqty=$this->input->post('reqqty'.$itemid);
					$unit=$this->input->post('unit'.$itemid);
					
					if($recvqty==$reqqty)
					{
						$c=1;
					}else{
						$c=0;
					}
					
					$data=array('itemid'=>$itemid,'pono'=>$po,'gateentryno'=>$gateentryno,'reqty'=>$reqqty,'recqty'=>$recvqty,'unit'=>$unit,'source'=>$source,'jobcardno'=>$jobcard,'gateentryOn'=>date('Y-m-d H:i:s'),'gateentryBy'=>$_SESSION['logged_in']['user_id'],'addedOn'=>date('Y-m-d H:i:s'));
					$this->db->insert('mrn',$data);
					
					if($c==1)
					{
						$data1=array('gateentrycomplete'=>'1','gateentryno'=>$gateentryno);
						$this->db->where('itemid',$itemid);
						$this->db->where('pono',$po);
						$this->db->update('purchase_order',$data1);
						
					}
					
				}

					$this->session->set_flashdata('message','<div class="alert alert-success">Thank You! Gate Entry has been done.</div>');
					redirect(page_url.'Reporting/vendorwisepoforgateentry/'.$vendor);

		}
		
		
		
	}
	
	
	
	function createmrn()
	{
		
		$iitem=$this->input->post('item');
		$po=$this->input->post('po');
		$source=$this->input->post('source');
		$jobcard=$this->input->post('jobcardno');
		$mid=$this->input->post('instrumentid');

		if(count($iitem)>0)
		{

				for($i=0;$i<count($iitem);$i++)
				{
					
					$itemid=$iitem[$i];
					$recvqty=$this->input->post('recvqty'.$itemid);
					$reqqty=$this->input->post('reqqty'.$itemid);
					$unit=$this->input->post('unit'.$itemid);
					
					if($recvqty==$reqqty)
					{
						$c=1;
					}else{
						$c=0;
					}
					
					$data=array('itemid'=>$itemid,'pono'=>$po,'reqty'=>$reqqty,'recqty'=>$recvqty,'unit'=>$unit,'source'=>$source,'jobcardno'=>$jobcard,'addedOn'=>date('Y-m-d H:i:s'),'mrndone'=>$c);
					$this->db->insert('mrn',$data);
					
					if($c==1)
					{
						$data1=array('completed'=>$c);
						$this->db->where('itemid',$itemid);
						$this->db->where('pono',$po);
						$this->db->update('purchase_order',$data1);
						
					}
					
				}

					$this->session->set_flashdata('message','MRN Updated');
					redirect(page_url.'Reporting/pendingpo');

		}
		
		
		
	}
	
	
	
	function generateautopr()
	{
		
		$item=$this->input->post('itemid');

		$prnos=$this->db->select('id')->from('purchase_request')->group_by('prno')->get();
		$num=$prnos->num_rows();
		if($num==0)
		{
		$num1=1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPR'.$num_padded;
		}else{
		$num1=$num+1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPR'.$num_padded;

		}
	
		if(count($item)>0)
		{
		for($i=0;$i<count($item);$i++)
		{
			$itemid=$item[$i];
			$unit=$this->input->post('unit'.$itemid);
			$stock=$this->input->post('currstock'.$itemid);
			$qty=$this->input->post('qty'.$itemid);
			
			
			$data=array('masterid'=>$itemid,'itemid'=>$itemid,'source'=>'3','prno'=>$code,'qty'=>$qty,'unit'=>$unit,'stockattimeofpr'=>$stock,'addedBy'=>$_SESSION['logged_in']['user_id'],'addedOn'=>date('Y-m-d H:i:s'));
			$this->db->insert('purchase_request',$data);
			
		}
		
		$this->session->set_flashdata('message','PR GENERATED');
		redirect(page_url.'Reporting/pendingpr');
	}
		
		
	}
	



	function housekeepingpr()
	{
		
		
		$this->load->view('store/housekeepinginvoice');
		
	}
	
	function creategeneralpo()
{
	
	
	$this->load->view('store/creategeneralpodashboard');
	
	
}


function getitempriceforgeneral()
{
	$supid=$this->input->post('suppid');
	$partid=$this->input->post('partid');
	$masterid=$this->input->post('masterid');
	
	$pri=$this->db->select('price')->from('vendorwise_house_keeping_item_price')->where('item_id',$partid)->where('vendor_id',$supid)->get();
	if($pri->num_rows()>0)
	{
		foreach($pri->result() as $price);
		
		echo floatval($price->price);
		
		
		
	}else{
		
		echo "NA";
	}



}


function generalpo()
{
	
	$this->load->view('store/generalpoinvoice');
	
}

function generalmrn()
{
	
	$this->load->view('store/generalmrndashboard');
	
}

function approvepo()
{
		$pono=$this->uri->segment(3);
		
		$data=array('approved'=>1,'approvedOn'=>date('Y-m-d H:i:s'),'approvedBy'=>$_SESSION['logged_in']['user_id']);
		
		$this->db->where('pono',$pono);
		$this->db->update('purchase_order',$data);
		
		$this->session->set_flashdata('message','<div class="alert alert-success">Thank You! PO has been approved</div>');
		redirect(page_url.'Reporting/pendingpoforapproval');


}


function generalgateentry()
{
	
	$this->load->view('store/generalgateentry');
	
}


function gateentry()
{
	
	$this->load->view('store/gateentrydashboard');
	
}




function markmrn()
{
	$id=$this->uri->segment(3);
	$gateno=$this->uri->segment(4);
	
	$data=array('mrndone'=>'1','mrndoneOn'=>date('Y-m-d H:i:s'),'mrndoneBy'=>$_SESSION['logged_in']['user_id']);
	$this->db->where('id',$id);
	$this->db->update('mrn',$data);
	
	$this->session->set_flashdata('message','<div class="alert alert-success">Thank You! MRN has been done.</div>');
	redirect(page_url.'Reporting/pendingmrnrequest/'.$gateno);
	
	
}


public function debit_note(){
	
		$this->load->view('store/debit_note');
	
}

public function accept_reject_debit_note(){
		$this->load->view('store/accept_reject_debit_note');
	}
	
	
	public function accept_reject_debit_noteforgeneralitems(){
		$this->load->view('store/accept_reject_debit_noteforgeneralitems.php');
	}
	

public function update_accept_reject_qty_pooLDD(){
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
           $table = "mrn_history";
			if(isset($_REQUEST['itemid'])){	
					$tags1=count($_REQUEST['itemid']);
					if($tags1>0)
					{
					$itemid=$_REQUEST['itemid'];
					$recordid = $_REQUEST['recordid'];
					$approved_qty = $_REQUEST['approved_qty'];
					$debit_note_qty = $_REQUEST['debit_note_qty'];
					$reject_qty = $_REQUEST['reject_qty'];
					for($x=0;$x<$tags1;$x++){
					if($itemid[$x]!='')
						{
							$prevstockforitem=$this->getcurrentstock($itemid[$x]);
						 
							$data=array('itemid'=>$itemid[$x],
							'po_no'=>$this->uri->segment(4),
							'accept_qty'=>$approved_qty[$x],
							'debit_qty'=>$debit_note_qty[$x],
							'reject_qty'=>$reject_qty[$x],
							'added_on'=>$added_time,
							'added_by'=>$user_id);
							//echo "<pre>"; print_r($data);exit;
							$this->db->insert($table,$data);
							$lid=$this->db->insert_id();
							
							
							/*** CHECK DEBIT NOTE **/
						if($debit_note_qty[$x]>0)
						{
							$dbno=$this->getnextdbnumber();
							
							$datadb=array('dbno'=>$dbno,'pono'=>$this->uri->segment(4),'mrnhistoryid'=>$lid,'itemid'=>$itemid[$x],'qty'=>$debit_note_qty[$x],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
							
							$this->db->insert('debitnote',$datadb);
							
							
							
						}	
						
						/** END **/
						
					/*** CHECK APPROVED **/
						if($approved_qty[$x]>0)
						{
							
							$currstock=$prevstockforitem;
							
							$newstock=$currstock+$approved_qty[$x];
							
							$datadb1=array('current_stock'=>$newstock);
							
							$this->db->where('id',$itemid[$x]);
							$this->db->update('machine_parts_with_picture',$datadb1);
						
						}	
						/** END **/
						
						
						/** MARK AS QC DONE **/
						$mrnqcdata=array('qcstatus'=>'1','qcdoneOn'=>date('Y-m-d H:i:s'),'qcdoneBy'=>$_SESSION['logged_in']['user_id']);
						$this->db->where('pono',$this->uri->segment(4));
						$this->db->where('itemid',$itemid[$x]);
						$this->db->update('mrn',$mrnqcdata);
						
						/** END **/
						}
							
					}
					}
						$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Thank you, Your record successfully added.</span><br/>');
						redirect(page_url.'Reporting/mrnqc');
						
					}else{
						
						$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Request Failed please try again.</span><br/>');
				redirect(page_url.'Reporting/mrnqc');
				
						
					}
				
		
		
	}
	
	
	public function update_accept_reject_qty_po(){


		$user_id =$this->session->userdata['logged_in']['user_id'];	
		date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
           $table = "mrn_history";
			if(isset($_REQUEST['itemid'])){	
					$tags1=count($_REQUEST['itemid']);
					if($tags1>0)	
					{
					$itemid=$_REQUEST['itemid'];
					$supp=$_REQUEST['supplier'];
					$recordid = $_REQUEST['recordid'];
					$approved_qty = $_REQUEST['approved_qty'];
					$reject_qty = $_REQUEST['reject_qty'];
					$chtype = $_REQUEST['chtype'];
					$unit = $_REQUEST['unit'];
					for($x=0;$x<$tags1;$x++){
					if($itemid[$x]!='')
						{
							$prevstockforitem=$this->getcurrentstock($itemid[$x]);
						 
							$data=array('itemid'=>$itemid[$x],
							'po_no'=>$this->uri->segment(4),
							'accept_qty'=>$approved_qty,
							'reject_qty'=>$reject_qty,
							'added_on'=>$added_time,
							'reject_reason'=>$chtype,
							'added_by'=>$user_id);
							//echo "<pre>"; print_r($data);exit;
							$this->db->insert($table,$data);
							$lid=$this->db->insert_id();
							
							
							/*** CHECK DEBIT NOTE **/
						if($reject_qty>0)
						{
							
									$datadb=array('pono'=>$this->uri->segment(4),'mrnhistoryid'=>$lid,'itemid'=>$itemid[$x],'qty'=>$reject_qty,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
									$this->db->insert('item_rejection_request',$datadb);
							
							
							/**if($chtype=='2')
							{
									$dbno=$this->getnextdbnumber();
									$datadb=array('dbno'=>$dbno,'pono'=>$this->uri->segment(4),'mrnhistoryid'=>$lid,'itemid'=>$itemid[$x],'qty'=>$reject_qty[$x],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
									$this->db->insert('debitnote',$datadb);
							}
								
							
							if($chtype=='1')
							{
								
								
								$chno=$this->getnextchallanno();
								$datadb11=array('challanno'=>$chno,'supplier'=>$supp,'challandate'=>date('Y-m-d'),'mrnhistoryid'=>$lid,'pono'=>$this->uri->segment(4),'addedOn'=>date('Y-m-d'),'addedBy'=>$_SESSION['logged_in']['user_id']);
								$this->db->insert('outwardchallan',$datadb11);
								$lids=$this->db->insert_id();
								$data12343=array('challanid'=>$lids,'itemid'=>$itemid[$x],'quantity'=>$reject_qty[$x],'unit'=>$unit);
								
								$this->db->insert('outwardchallan_item',$data12343);
								
							} **/
							
							
							
						}	
						
						/** END **/
						
					/*** CHECK APPROVED **/
						if($approved_qty>0)
						{
							
							$currstock=$prevstockforitem;

							$newstock=$currstock+$approved_qty;

							$datadb1=array('current_stock'=>$newstock);

							$this->db->where('id',$itemid[$x]);
							$this->db->update('machine_parts_with_picture',$datadb1);
						
						}	
						
						/** END **/
						
											
						
						/** MARK AS QC DONE **/
						$mrnqcdata=array('qcstatus'=>'1','qcdoneOn'=>date('Y-m-d H:i:s'),'qcdoneBy'=>$_SESSION['logged_in']['user_id']);
						$this->db->where('pono',$this->uri->segment(4));
						$this->db->where('itemid',$itemid[$x]);
						$this->db->update('mrn',$mrnqcdata);
						
						/** END **/
						}
							
					}
					}
						$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Thank you, Your record successfully added.</span><br/>');
						redirect(page_url.'Reporting/mrnqc');
						
					}else{
						
						$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Request Failed please try again.</span><br/>');
				redirect(page_url.'Reporting/mrnqc');
				
						
					}
				
		
		
	}
	
	public function update_accept_reject_qty_poforgeneralitems(){


		$user_id =$this->session->userdata['logged_in']['user_id'];	
		date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
           $table = "mrn_history";
			if(isset($_REQUEST['itemid'])){	
					$tags1=count($_REQUEST['itemid']);
					if($tags1>0)	
					{
					$itemid=$_REQUEST['itemid'];
					$supp=$_REQUEST['supplier'];
					$recordid = $_REQUEST['recordid'];
					$approved_qty = $_REQUEST['approved_qty'];
					
					$reject_qty = $_REQUEST['reject_qty'];
					$chtype = $_REQUEST['chtype'];
					$unit = $_REQUEST['unit'];
					for($x=0;$x<$tags1;$x++){
					if($itemid[$x]!='')
						{
							$prevstockforitem=$this->getcurrentstockforgeneralitem($itemid[$x]);
						
						
							$data=array('itemid'=>$itemid[$x],
							'po_no'=>$this->uri->segment(4),
							'accept_qty'=>$approved_qty,
							'reject_qty'=>$reject_qty,
							'added_on'=>$added_time,
							'reject_reason'=>$chtype,
							'added_by'=>$user_id);
							//echo "<pre>"; print_r($data);exit;
							$this->db->insert($table,$data);
							$lid=$this->db->insert_id();
							
							
							/*** CHECK DEBIT NOTE **/
						if($reject_qty>0)
						{
							
									$datadb=array('pono'=>$this->uri->segment(4),'mrnhistoryid'=>$lid,'itemid'=>$itemid[$x],'qty'=>$reject_qty,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
									$this->db->insert('item_rejection_request',$datadb);
							
							
							/**if($chtype=='2')
							{
									$dbno=$this->getnextdbnumber();
									$datadb=array('dbno'=>$dbno,'pono'=>$this->uri->segment(4),'mrnhistoryid'=>$lid,'itemid'=>$itemid[$x],'qty'=>$reject_qty[$x],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
									$this->db->insert('debitnote',$datadb);
							}
								
							
							if($chtype=='1')
							{
								
								
								$chno=$this->getnextchallanno();
								$datadb11=array('challanno'=>$chno,'supplier'=>$supp,'challandate'=>date('Y-m-d'),'mrnhistoryid'=>$lid,'pono'=>$this->uri->segment(4),'addedOn'=>date('Y-m-d'),'addedBy'=>$_SESSION['logged_in']['user_id']);
								$this->db->insert('outwardchallan',$datadb11);
								$lids=$this->db->insert_id();
								$data12343=array('challanid'=>$lids,'itemid'=>$itemid[$x],'quantity'=>$reject_qty[$x],'unit'=>$unit);
								
								$this->db->insert('outwardchallan_item',$data12343);
								
							} **/
							
							
							
						}	
						
						/** END **/
						
					/*** CHECK APPROVED **/
						if($approved_qty>0)
						{
							
							$currstock=$prevstockforitem;

							$newstock=$currstock+$approved_qty;

							$datadb1=array('qty'=>$newstock);

							$this->db->where('id',$itemid[$x]);
							$this->db->update('house_keeping_items',$datadb1);
						
						}	
						
						/** END **/
						
											
						
						/** MARK AS QC DONE **/
						$mrnqcdata=array('qcstatus'=>'1','qcdoneOn'=>date('Y-m-d H:i:s'),'qcdoneBy'=>$_SESSION['logged_in']['user_id']);
						$this->db->where('pono',$this->uri->segment(4));
						$this->db->where('itemid',$itemid[$x]);
						$this->db->update('mrn',$mrnqcdata);
						
						/** END **/
						}
							
					}
					}
						$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Thank you, Your record successfully added.</span><br/>');
						redirect(page_url.'Reporting/mrnqc');
						
					}else{
						
						$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Request Failed please try again.</span><br/>');
				redirect(page_url.'Reporting/mrnqc');
				
						
					}
				
		
		
	}
	
	
	
	public function update_accept_reject_qty_poOldd1222(){
	
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
           $table = "mrn_history";
			if(isset($_REQUEST['itemid'])){	
					$tags1=count($_REQUEST['itemid']);
					if($tags1>0)	
					{
					$itemid=$_REQUEST['itemid'];
					$supp=$_REQUEST['supplier'];
					$recordid = $_REQUEST['recordid'];
					$approved_qty = $_REQUEST['approved_qty'];
					$reject_qty = $_REQUEST['reject_qty'];
					$chtype = $_REQUEST['chtype'];
					$unit = $_REQUEST['unit'];
					for($x=0;$x<$tags1;$x++){
					if($itemid[$x]!='')
						{
							$prevstockforitem=$this->getcurrentstock($itemid[$x]);
						 
							$data=array('itemid'=>$itemid[$x],
							'po_no'=>$this->uri->segment(4),
							'accept_qty'=>$approved_qty[$x],
							'reject_qty'=>$reject_qty[$x],
							'added_on'=>$added_time,
							'reject_reason'=>$chtype,
							'added_by'=>$user_id);
							//echo "<pre>"; print_r($data);exit;
							$this->db->insert($table,$data);
							$lid=$this->db->insert_id();
							
							
							/*** CHECK DEBIT NOTE **/
						if($reject_qty[$x]>0)
						{
							
									$datadb=array('pono'=>$this->uri->segment(4),'mrnhistoryid'=>$lid,'itemid'=>$itemid[$x],'qty'=>$reject_qty[$x],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
									$this->db->insert('item_rejection_request',$datadb);
							
							
							/**if($chtype=='2')
							{
									$dbno=$this->getnextdbnumber();
									$datadb=array('dbno'=>$dbno,'pono'=>$this->uri->segment(4),'mrnhistoryid'=>$lid,'itemid'=>$itemid[$x],'qty'=>$reject_qty[$x],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
									$this->db->insert('debitnote',$datadb);
							}
								
							
							if($chtype=='1')
							{
								
								
								$chno=$this->getnextchallanno();
								$datadb11=array('challanno'=>$chno,'supplier'=>$supp,'challandate'=>date('Y-m-d'),'mrnhistoryid'=>$lid,'pono'=>$this->uri->segment(4),'addedOn'=>date('Y-m-d'),'addedBy'=>$_SESSION['logged_in']['user_id']);
								$this->db->insert('outwardchallan',$datadb11);
								$lids=$this->db->insert_id();
								$data12343=array('challanid'=>$lids,'itemid'=>$itemid[$x],'quantity'=>$reject_qty[$x],'unit'=>$unit);
								
								$this->db->insert('outwardchallan_item',$data12343);
								
							} **/
							
							
							
						}	
						
						/** END **/
						
					/*** CHECK APPROVED **/
						if($approved_qty[$x]>0)
						{
							
							$currstock=$prevstockforitem;

							$newstock=$currstock+$approved_qty[$x];

							$datadb1=array('current_stock'=>$newstock);

							$this->db->where('id',$itemid[$x]);
							$this->db->update('machine_parts_with_picture',$datadb1);
						
						}	
						
						/** END **/
						
											
						
						/** MARK AS QC DONE **/
						$mrnqcdata=array('qcstatus'=>'1','qcdoneOn'=>date('Y-m-d H:i:s'),'qcdoneBy'=>$_SESSION['logged_in']['user_id']);
						$this->db->where('pono',$this->uri->segment(4));
						$this->db->where('itemid',$itemid[$x]);
						$this->db->update('mrn',$mrnqcdata);
						
						/** END **/
						}
							
					}
					}
						$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Thank you, Your record successfully added.</span><br/>');
						redirect(page_url.'Reporting/mrnqc');
						
					}else{
						
						$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Request Failed please try again.</span><br/>');
				redirect(page_url.'Reporting/mrnqc');
				
						
					}
				
		
		
	}
	
	
	
	public function update_accept_reject_qty_pooLDDAFTERFEEDBACK(){
	
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
           $table = "mrn_history";
			if(isset($_REQUEST['itemid'])){	
					$tags1=count($_REQUEST['itemid']);
					if($tags1>0)	
					{
					$itemid=$_REQUEST['itemid'];
					$supp=$_REQUEST['supplier'];
					$recordid = $_REQUEST['recordid'];
					$approved_qty = $_REQUEST['approved_qty'];
					$reject_qty = $_REQUEST['reject_qty'];
					$chtype = $_REQUEST['chtype'];
					$unit = $_REQUEST['unit'];
					for($x=0;$x<$tags1;$x++){
					if($itemid[$x]!='')
						{
							$prevstockforitem=$this->getcurrentstock($itemid[$x]);
						 
							$data=array('itemid'=>$itemid[$x],
							'po_no'=>$this->uri->segment(4),
							'accept_qty'=>$approved_qty[$x],
							'reject_qty'=>$reject_qty[$x],
							'added_on'=>$added_time,
							'added_by'=>$user_id);
							//echo "<pre>"; print_r($data);exit;
							$this->db->insert($table,$data);
							$lid=$this->db->insert_id();
							
							
							/*** CHECK DEBIT NOTE **/
						if($reject_qty[$x]>0)
						{
							
							if($chtype=='2')
							{
									$dbno=$this->getnextdbnumber();
									$datadb=array('dbno'=>$dbno,'pono'=>$this->uri->segment(4),'mrnhistoryid'=>$lid,'itemid'=>$itemid[$x],'qty'=>$reject_qty[$x],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
									$this->db->insert('debitnote',$datadb);
							}
							
							
							if($chtype=='1')
							{
								
								
								$chno=$this->getnextchallanno();
								$datadb11=array('challanno'=>$chno,'supplier'=>$supp,'challandate'=>date('Y-m-d'),'mrnhistoryid'=>$lid,'pono'=>$this->uri->segment(4),'addedOn'=>date('Y-m-d'),'addedBy'=>$_SESSION['logged_in']['user_id']);
								$this->db->insert('outwardchallan',$datadb11);
								$lids=$this->db->insert_id();
								$data12343=array('challanid'=>$lids,'itemid'=>$itemid[$x],'quantity'=>$reject_qty[$x],'unit'=>$unit);
								
								$this->db->insert('outwardchallan_item',$data12343);
								
							}
							
							
							
						}	
						
						/** END **/
						
					/*** CHECK APPROVED **/
						if($approved_qty[$x]>0)
						{
							
							$currstock=$prevstockforitem;

							$newstock=$currstock+$approved_qty[$x];

							$datadb1=array('current_stock'=>$newstock);

							$this->db->where('id',$itemid[$x]);
							$this->db->update('machine_parts_with_picture',$datadb1);
						
						}	
						
						/** END **/
						
											
						
						/** MARK AS QC DONE **/
						$mrnqcdata=array('qcstatus'=>'1','qcdoneOn'=>date('Y-m-d H:i:s'),'qcdoneBy'=>$_SESSION['logged_in']['user_id']);
						$this->db->where('pono',$this->uri->segment(4));
						$this->db->where('itemid',$itemid[$x]);
						$this->db->update('mrn',$mrnqcdata);
						
						/** END **/
						}
							
					}
					}
						$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Thank you, Your record successfully added.</span><br/>');
						redirect(page_url.'Reporting/mrnqc');
						
					}else{
						
						$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Request Failed please try again.</span><br/>');
				redirect(page_url.'Reporting/mrnqc');
				
						
					}
				
		
		
	}
	
	
	
	function getnextdbnumber()
	{
		$prnos=$this->db->select('id')->from('debitnote')->group_by('dbno')->get();
	$num=$prnos->num_rows();
	if($num==0)
	{
		$num1=1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESDB'.$num_padded;
	}else{
		$num1=$num+1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESDB'.$num_padded;
		
	}
		
		return $code;
		
	}
	
	
	function getcurrentstock($itemid)
{
		
	$qyer=$this->db->select('current_stock')->from('machine_parts_with_picture')->where('id',$itemid)->get();
	if($qyer->num_rows()>0)
	{
		
		foreach($qyer->result() as $qyer12);
		$currstock=$qyer12->current_stock;
		
		return $currstock;
		
	}else{
		
		echo "ITEM NOT FOUND";EXIT;
		
	}
	
	
		
		
}


	
	
	function generatedebit_note()
{
	$id=$this->uri->segment(3);
	if($id<>'')
	{
		
		$data=array('approved'=>'1');
		
		$this->db->where('id',$id);
		$this->db->update('debitnote',$data);
		$this->session->set_flashdata('message','Debit Note Generated');
		redirect(page_url.'Reporting/debitnote');
		
		
	}else{
		
		echo "Invalid Request";exit;
	}
}


function getnextchallanno()
{
	
	$prnos=$this->db->select('id')->from('outwardchallan')->group_by('challanno')->get();
	$num=$prnos->num_rows();
	if($num==0)
	{
		$num1=1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESCH-'.$num_padded;
	}else{
		$num1=$num+1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESCH'.$num_padded;
		
	}
		
		return $code;

	
	
}

function createrejectionchallan()
{
	
$this->load->view('store/createrejectionchallan');	
	
	
}


function generaterejectionchallan()
{
		$id=$this->uri->segment(3);
		$itemid=$this->input->post('itemid');
		
		$chno=$this->getnextchallanno();


		$data=array('challanno'=>$chno,'supplier'=>$this->input->post('supplierid'),'mrnhistoryid'=>$this->input->post('mrnhistoryid'),'challandate'=>date('Y-m-d',strtotime($this->input->post('cdate'))),'pono'=>$this->input->post('pono'),'remarks'=>$this->input->post('remarks'),'dispatchedby'=>$this->input->post('dispatch'),'approved'=>'1','approvedBy'=>$_SESSION['logged_in']['user_id'],'approvedOn'=>date('Y-m-d H:i:s'),'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);

		$this->db->insert('outwardchallan',$data);
		$lid=$this->db->insert_id();


		$data1=array('challanid'=>$lid,'itemid'=>$itemid,'unit'=>$this->input->post('unit'),'quantity'=>$this->input->post('rqty'),'expdate'=>date('Y-m-d',strtotime($this->input->post('rdate'))),'addedOn'=>date('Y-m-d H:i:s'));
		$this->db->insert('outwardchallan_item',$data1);
		
		
		$data2343=array('chtype'=>1,'approved'=>1);
		$this->db->where('id',$id);
		$this->db->update('item_rejection_request',$data2343);
		
		$this->session->set_flashdata('message','Challan Created');
		redirect(page_url.'Reporting/rejecteditemreq');

}


function generaterejectionchallanOldddd()
{
		$id=$this->uri->segment(3);
		$itemid=$this->input->post('itemid');

		$data=array('challandate'=>date('Y-m-d',strtotime($this->input->post('cdate'))),'remarks'=>date('Y-m-d',strtotime($this->input->post('remarks'))),'dispatchedby'=>$this->input->post('dispatch'),'approved'=>'1','approvedBy'=>$_SESSION['logged_in']['user_id'],'approvedOn'=>date('Y-m-d H:i:s'));

		$this->db->where('id',$id);
		$this->db->update('outwardchallan',$data);


		$data1=array('returnable'=>'1','expdate'=>date('Y-m-d',strtotime($this->input->post('rdate'))));

		$this->db->where('challanid',$id);
		$this->db->where('itemid',$itemid);
		$this->db->update('outwardchallan_item',$data1);
		
		$this->session->set_flashdata('message','Challan Created');
		redirect(page_url.'Reporting/rejecteditemreq');
	
	
	
	
}


public function rejectionchallan()
{
	$this->load->view('store/rejectionchallan');
}


function rejectiondbnote()
{
	$this->load->view('store/createdbbystore');
	
}

function createdebitnote()
{
	
	$this->load->view('store/createdebitnote');
	
}


function generatedebitnote()
{
	
		$dbno=$this->getnextdbnumber();
		$datadb=array('dbno'=>$dbno,'pono'=>$this->input->post('pono'),'mrnhistoryid'=>$this->input->post('mrnhistoryid'),'itemid'=>$this->input->post('itemid'),'qty'=>$this->input->post('rqty'),'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id'],'approved'=>'1');
		$this->db->insert('debitnote',$datadb);

		$id=$this->uri->segment('3');

		$data2343=array('chtype'=>2,'approved'=>1);
		$this->db->where('id',$id);
		$this->db->update('item_rejection_request',$data2343);
		
		$this->session->set_flashdata('message','Challan Created');
		redirect(page_url.'Reporting/rejecteditemreq');

	
}
	
	
		public function issued_item_dashboard(){
	    $this->load->view('store/issue_items_report');
	}
	
	
	public function received_item_dashboard(){
	    $this->load->view('store/received_item_dashboard');
	}
	
	
	
	function bulkrate()
{
		
	$this->load->view('store/bulkrateupdate');
	
	
	
}

function bulkupdate()
{
	
	$che=$this->input->post('checkit');
	
	for($i=0;$i<count($che);$i++)
	{
		$partid=$che[$i];
		
		$vendor=$this->input->post('vendor'.$partid);
		$lprice=$this->input->post('lprice'.$partid);
		$discount=$this->input->post('discount'.$partid);
		$price=$this->input->post('price'.$partid);
		$che1='1';
		
		//->where('vendorid',$vendor)
		$resty=$this->db->select('id')->from('vendors_price')->where('itemid',$partid)->get();
		if($resty->num_rows()>0)
		{
			
			
			$data=array('listprice'=>$lprice,'discount'=>$discount,'price'=>$price,'upd'=>'1');
			
			$this->db->where('itemid',$partid);
			//$this->db->where('vendorid',$vendor);
			$this->db->update('vendors_price',$data);
			
			if($this->db->affected_rows()>0)
			{
			$data11=array('up'=>'1');
			$this->db->where('id',$partid);
			$this->db->update('machine_parts_with_picture',$data11);
			}
			
			
		}else{
			
			
			
			$data=array('masterid'=>$partid,'itemid'=>$partid,'vendorid'=>$vendor,'listprice'=>$lprice,'discount'=>$discount,'price'=>$price,'upd'=>'1');
			
			$this->db->insert('vendors_price',$data);
			if($this->db->affected_rows()>0)
			{
			$data11=array('up'=>'1');
			$this->db->where('id',$partid);
			$this->db->update('machine_parts_with_picture',$data11);
			}
			
			
		}
		
		
		
		
	}
	
	
	$this->session->set_flashdata('message','Rates Updated');
	redirect(page_url.'Store/bulkrate');
	
	
}


function creategeneralgateentry()
	{
		
		$iitem=$this->input->post('item');
		$po=$this->input->post('po');
		$vendor=$this->input->post('vendor');
		$gateentryno=$this->input->post('gateentryno');
		
		$source=$this->input->post('source');
		$jobcard=$this->input->post('jobcardno');
		$mid=$this->input->post('instrumentid');
		if(count($iitem)>0)
		{

				for($i=0;$i<count($iitem);$i++)
				{
					
					$itemid=$iitem[$i];
					$recvqty=$this->input->post('recvqty'.$itemid);
					$reqqty=$this->input->post('reqqty'.$itemid);
					$unit=$this->input->post('unit'.$itemid);
					
					if($recvqty==$reqqty)
					{
						$c=1;
					}else{
						$c=0;
					}
					
					$data=array('itemid'=>$itemid,'pono'=>$po,'gateentryno'=>$gateentryno,'reqty'=>$reqqty,'recqty'=>$recvqty,'unit'=>$unit,'source'=>$source,'jobcardno'=>$jobcard,'gateentryOn'=>date('Y-m-d H:i:s'),'gateentryBy'=>$_SESSION['logged_in']['user_id'],'addedOn'=>date('Y-m-d H:i:s'));
					$this->db->insert('mrn',$data);
					
					if($c==1)
					{
						$data1=array('gateentrycomplete'=>'1','gateentryno'=>$gateentryno);
						$this->db->where('itemid',$itemid);
						$this->db->where('pono',$po);
						$this->db->update('purchase_order',$data1);
						
					}
					
				}

					$this->session->set_flashdata('message','Gate Entry Done');
					redirect(page_url.'Reporting/vendorwisepoforgateentry/'.$vendor);

		}
		
		
		
	}
	
	
	
		function getcurrentstockforgeneralitem($itemid)
{
		
		
	$qyer1234=$this->db->select('qty as current_stock')->from('house_keeping_items')->where('id',$itemid)->get();
	if($qyer1234->num_rows()>0)
	{
		
		foreach($qyer1234->result() as $qyer12);
		$currstock=$qyer12->current_stock;
		
		return $currstock;
		
	}else{
		
		echo "ITEM NOT FOUND";EXIT;
		
	}
	
	
		
		
}

function issuegeneralitem()
{
	
	$this->load->view('store/issuegeneralitemtouser');
}


function issueuserwisegeneralitem()
{
	
	$itemid=$this->uri->segment(3);
	
	if($this->input->post('usertype')=='1')
	{
		$crm=$this->input->post('crmusers');
		$noncrm='';
	}else{
		
		$noncrm=$this->input->post('noncrm');
		$crm=0;
	}
	$data=array('itemid'=>$itemid,'qty'=>$this->input->post('iqty'),'crmuser'=>$crm,'stockthattime'=>$this->input->post('currentstock'),'noncrmuser'=>$noncrm,'remarks'=>$this->input->post('remarks'),'issuedOn'=>date('Y-m-d H:i:s'),'issuedBy'=>$_SESSION['logged_in']['user_id']);
	
		$this->db->insert('issuegeneralstock',$data);
		if($this->db->affected_rows()>0)
		{
		$qt=$this->input->post('iqty');


		$this->db->set('qty', 'qty-'.$qt, false);
		$this->db->where('id' , $itemid);
		$this->db->update('house_keeping_items');
		if($this->db->affected_rows()>0)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success">Item Issued</div>');
			redirect(page_url.'Store/issuegeneralitem/'.$itemid);
			
		}else{
			
			$this->session->set_flashdata('message','<div class="alert alert-danger">Item Issued but unable to update stock</div>');
			redirect(page_url.'Store/issuegeneralitem/'.$itemid);
		}
			
		}else{
			
			echo "ERROR OCCURED";EXIT;
			
		}
	
	
	
	
	
}

function issueblockedqty()
{
	//echo "h";exit;
$cat=$this->uri->segment(3);

$itemid=$this->input->post('checkit'.$cat);
$jobcardid=$this->input->post('jobcardsid');

for($i=0;$i<count($itemid);$i++)
{
	$itemids=$itemid[$i];
	
	$blocked=$this->input->post('blockedstockid'.$cat.$itemids);
	$reqstock=$this->input->post('reqstock'.$cat.$itemids);
	$blockedid=$blocked;
	
	$reqst=$reqstock;
	$isqty=$this->input->post('issueqty'.$itemids);
	
	$issueto=$this->input->post('issuetos'.$cat);

	
	$data=array('itemtype'=>'1','issuetype'=>'1','blockedid'=>$blockedid,'itemid'=>$itemids,'stock'=>$isqty,'issuedto'=>$issueto,'issuedBy'=>$_SESSION['logged_in']['user_id'],'issuedOn'=>date('Y-m-d H:i:s'),'jobcardid'=>$jobcardid);

	$this->db->insert('issuestocktousers',$data);
	

	if($reqst==$isqty)
	{
		$data1=array('active'=>'0','issued'=>'1');
		$this->db->where('id',$blockedid);
		$this->db->update('blockedstock',$data1);
	}
	
	/** MINUS FROM STOCK **/
	$currstock=$this->getcurrentstock($itemids);
	$newqty=$currstock-$isqty;
	$ndata=array('current_stock'=>$newqty);
	$this->db->where('id',$itemids);
	$this->db->update('machine_parts_with_picture',$ndata);
	/** END **/
	
}

$this->session->set_flashdata('message','Issued');

redirect(page_url.'Reporting/jobcarditem');
	
	
}


function issueblockeditems()
{
	$this->load->view('store/jobcarditemissuedashboard');
}


function servicerequest()
{
	
	$this->load->view('store/servicerepairform');
	
}

function addservicerequest()
{
	
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		//$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('company', '', 'required|trim');
		//$this->form_validation->set_rules('cperson', '', 'required|trim');
		//$this->form_validation->set_rules('email', '', 'required|trim|valid_email');
		//$this->form_validation->set_rules('mobile', '', 'required|trim|min_length[10]|max_length[10]');
		$this->form_validation->set_rules('ins[]', 'Instrument', 'required|trim');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('store/servicerepairform');
		}
		else
		{
			
					$photo=$_FILES['image']['name'];
					if($photo<>'')
					{
					$image1=explode('.',$photo);
					$cat_image=end($image1);
					$picss=time().'.'.$cat_image;
					move_uploaded_file($_FILES['image']['tmp_name'],UPLOADPATH.'servicerequest/'.$picss);
					}else
					{
						$picss='';
					}
			
			$data=array('companyname'=>$this->input->post('company'),
			'personname'=>$this->input->post('company'),
			'email'=>$this->input->post('company'),
			'mobile'=>$this->input->post('mobile'),
			'rgpimage'=>$picss,
			'addedOn'=>date('Y-m-d H:i:s'),
			'addedBy'=>$user_id);
			
			$this->db->insert('service_repair_request',$data);
			$sid=$this->db->insert_id();
			$in=$this->input->post('ins');
			for($i=0;$i<count($in);$i++)
			{
				if($in[$i]<>'')
				{
					
					$data1=array('serviceid'=>$sid,'insname'=>$in[$i]);
					$this->db->insert('service_repair_instruments',$data1);
				}
				
				
			}
			$this->session->set_flashdata('message','<div class="alert alert-danger">Request Raised</div>');
			redirect(page_url.'Store/servicerequest');
			
		}
	
}


	
		
		function qcservicerequestdashboard()
		{
		
			$this->load->view('store/servicerepairdashboardforqc');
			
			
		}
		
		function getrepairservicerequest()
		{
			$imported_data=array();
			
			$restyyu=$this->db->select('*')->from('service_repair_request')->order_by('qc','ASC')->get();
			if($restyyu->num_rows()>0)
			{
				$i=1;
				foreach($restyyu->result() as $row)
				{
					if($row->qc=='0')
					{
						$sta="<a href='".page_url."Store/serviceqc/".$row->id."'><span class='btn btn-warning btn-sm'>QC Pending</span></a>";
						$pchange='';
					}else{
						
						$sta="<span class='btn btn-success btn-sm'>QC Done</span>";
						$pchange='';
					}
					$ins='';
			$estyyu1=$this->db->select('*')->from('service_repair_instruments')->where('serviceid',$row->id)->get();
			if($estyyu1->num_rows()>0)
			{
				foreach($estyyu1->result() as $row12)
				{
					$ins.=$row12->insname.'<br/>';
				}
				
			}

			$alluser='';
			$rest12=$this->db->select('first_name,last_name')->from('system_users')->where('user_id',$row->addedBy)->get();
			if($rest12->num_rows()>0)
			{
			foreach($rest12->result() as $rest1);

			$alluser=strtoupper($rest1->first_name);

			}

					
			$imported_data[] = array('sr_no'=>$i,
			'status'=>$sta,
			'company'=>$row->companyname,
			'instrument'=>$ins,
			'addedOn'=>date('d-m-Y g:i A',strtotime($row->addedOn)),
			'raisedby'=>$alluser);
			$i++;
				}
			}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($imported_data),
			"iTotalDisplayRecords" => count($imported_data),
			"aaData"=>$imported_data);
			
		echo json_encode($results);
			
		}
	
		
		
			function servicerequestdashboardforservice()
		{
		
			$this->load->view('store/servicerepairdashboardforservice');
			
			
		}
	
		function getrepairservicerequestforservice()
		{
			$imported_data=array();
			
			
			$restyyu=$this->db->select('*')->from('service_repair_request')->order_by('qc','ASC')->get();
			if($restyyu->num_rows()>0)
			{
					
				$i=1;
				foreach($restyyu->result() as $row)
				{
					if($row->service=='0')
					{
						if($row->qc=='1')
						{
						if($row->rgpimage<>'')
						{
						$sta="<a href='".page_url."FMS/order/".base64_encode('service-'.$row->id)."'><span class='btn btn-warning btn-xs'>Generate Order</span></a>";

						}else{

						$sta="<span style='color:red;'>QC Done but Challan Not Available</span><br/>";
						$sta.='<a href="javascript:;" onclick="showmodal('.$row->id.');">Add Challan</a>';
						}
						$qcdone=date('d-m-Y g:i A',strtotime($row->qcdoneon));
						}else{
							$sta="<span style='font-weight:bold;'>Pending at QC Stage</span>";
							$qcdone='';
						}
						$pchange='';
					}else{
						
						$sta="";
					}
					$ins='';
					$html='';
					$html="<table border='1' style='width:300px'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>Instrument</th><th style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>Image</th><th style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>Remarks</th><tbody><tr>";
					
			$estyyu1=$this->db->select('a.insname,a.id')->from('service_repair_instruments a')->where('a.serviceid',$row->id)->get();
			if($estyyu1->num_rows()>0)
			{
				foreach($estyyu1->result() as $row12)
				{
				    $restsy=$this->db->select('image,remarks')->from('qc_service_repair_request')->where('repairid',$row->id)->where('instrumentdetailid',$row12->id)->get();
				    if($restsy->num_rows()>0)
				    {
				    foreach($restsy->result() as $restsy11);
				    //b.image,b.remarks
					if($restsy11->image<>'')
					{
						if(file_exists(UPLOADPATH."qcforservicerequest/".$restsy11->image))
						{
						$img="<img src='".page_url."image_bank/qcforservicerequest/".$restsy11->image."' style='height:100px;width:100px'>";
						$rmk=$restsy11->remarks;
						}else{
							$img="";
							$rmk='';
						}
					}else
					{
						$img='';
						$rmk='';
					}
					
					
				    }else
				    {
				        $img='';
				        $rmk='';
				    }
					$html.="<tr><td style='padding:2px 2px 2px 2px; text-align:center;width:20%;'>".$row12->insname."</td><td style='padding:2px 2px 2px 2px; text-align:center;width:40%;'>".$img."</td><td style='padding:2px 2px 2px 2px; text-align:center;width:40%;'>".$rmk."</td></tr>";	
				}
				
			}


			if($row->rgpimage<>'')
			{
				$im='<a href="'.page_url.'image_bank/servicerequest/'.$row->rgpimage.'" download>Challan</a>';
			}
			else
			{
				$im='<span style="color:red;font-weight:bold;">Challan Not Available</span><br/>';
				
			}
			$alluser=$this->getsystemusername($row->addedBy);
			$alluser1=$this->getsystemusername($row->qcdoneby);
			

			$imported_data[] = array('sr_no'=>$i,
			'status'=>$sta,
			'company'=>$row->companyname,
			'instrument'=>$html,
			'qcon'=>$qcdone,
			'qcby'=>$alluser1,
			'challan'=>$im,
			'addedOn'=>date('d-m-Y g:i A',strtotime($row->addedOn)),
			'raisedby'=>$alluser);
			$i++;
				}
			}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($imported_data),
			"iTotalDisplayRecords" => count($imported_data),
			"aaData"=>$imported_data);
			
		echo json_encode($results);
			
		}
		
		function serviceqc()
		{
			$this->load->view('store/serviceqc');
		}
	
	
	function addserviceqcdetails()
	{
		
		$id=$this->uri->segment(3);
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		$this->form_validation->set_rules('company', '', 'required|trim');
		$this->form_validation->set_rules('ins[]', 'Instrument', 'required|trim');
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('store/servicerepairform');
			
		}else
		{
				
			$insid=$this->input->post('insdetailid');
			$a[]=0;
			for($i=0;$i<count($insid);$i++)
			{
					$photo=$_FILES['image'.$insid[$i]]['name'];
					$rmk=$this->input->post('rmk'.$insid[$i]);
					if($photo<>'')
					{
					$image1=explode('.',$photo);
					$cat_image=end($image1);
					$picss=time().$i.'.'.$cat_image;
					move_uploaded_file($_FILES['image'.$insid[$i]]['tmp_name'],UPLOADPATH.'qcforservicerequest/'.$picss);
					}else
					{
					$picss='';
					}
					
					$data=array('repairid'=>$id,'instrumentdetailid'=>$insid[$i],'image'=>$picss,'remarks'=>$rmk,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
					
					$this->db->insert('qc_service_repair_request',$data);
					if($this->db->affected_rows()>0)
					{
						$a[]=1;
					}
					
					
			}
					if(array_sum($a)==count($insid))
					{
					$data1=array('qc'=>'1','qcdoneOn'=>date('Y-m-d H:i:s'),'qcdoneby'=>$_SESSION['logged_in']['user_id']);
					$this->db->where('id',$id);
					$this->db->update('service_repair_request',$data1);
					}


			
			$this->session->set_flashdata('message','<div class="alert alert-danger">Request Raised</div>');
			redirect(page_url.'Store/qcservicerequestdashboard');
		}
		
		
	}


function getsystemusername($row)
{
	$alluser='';
	$rest12=$this->db->select('first_name,last_name')->from('system_users')->where('user_id',$row)->get();
	if($rest12->num_rows()>0)
	{
	foreach($rest12->result() as $rest1);

	$alluser=strtoupper($rest1->first_name);

	}

	return $alluser;
	
}

function uploadchallan()
{
	
	$id=$this->input->post('recordid');
	
	$photo=$_FILES['image']['name'];
				if($photo<>'')
				{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$picss=time().'.'.$cat_image;
				move_uploaded_file($_FILES['image']['tmp_name'],UPLOADPATH.'servicerequest/'.$picss);
				}else
				{
				$picss='';
				}

				$data=array('rgpimage'=>$picss);
				$this->db->where('id',$id);
				$this->db->update('service_repair_request',$data);
				
				
				$this->session->set_flashdata('message','<div class="alert alert-danger">Challan Updated</div>');
				redirect(page_url.'Store/servicerequestdashboardforservice');
	
	
}


	public function mininumlevelitems()
	{
		$i=1;
		$machinepart_data= array();
		$this->db->select('a.id,a.part,a.unit,a.current_stock,a.min_stock, a.specification, a.makes, a.size_in_mm, a.material, a.raw_bop, a.fincode, a.qty, a.picture, d.rack_location, p.category')->from('machine_parts_with_picture  a')->join('store_rack_location d','a.location_id=d.id','left')->join('presto_machine_part_category p','a.category_id=p.id','left')->where('a.current_stock<a.min_stock')->order_by('critical','DESC');
	

		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			
		$edit = "<a href='".page_url."Store/edit_parts/".$row->id."' style='color:white;'><i class='fa fa-pencil'></i></a>";	
			
		if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$row->fincode.'.jpg')){
			    
				$IMG = product_items.$row->fincode.'.jpg';
				$image = "<img src='".$IMG."' style='width:100px;height:100px;'>";
			}else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$row->fincode.'.JPG')){
			    
				$IMG = product_items.$row->fincode.'.JPG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$row->fincode.'.jpeg')){
			    	$IMG = product_items.$row->fincode.'.jpeg';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
		    }else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$row->fincode.'.JPEG')){
			    
				$IMG = product_items.$row->fincode.'.JPEG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else{
				$image="";
			}
			
			$unitname=$this->getreturnunit($row->unit);
			$vendor=$this->getreturnvendorandprice($row->id);
			
			/** GET STOCK WISE COLOR **/
			$curst=$row->current_stock;
			$min=$row->min_stock;
			if($curst<=0.00)
			{
				$st="BLACK";
			}
			
			/** 33 PER **/
			
			$thr33=$min*0.33;
			
			/** 62 PER **/
			$thr62=$min*0.62;
			
			
			/** 100 PER **/
			$thr100=$min*1;
			
			if($curst>'0.00' && $curst<=$thr62)
			{
			$st="RED";	
			}
			
			
			if($curst>$thr33 && $curst<=$thr62)
			{
				$st="YELLOW";
			}
			
			
			if($curst>$thr62 && $curst<=$thr100)
			{
				$st="GREEN";
			}
			
			
			if($curst>$row->min_stock)
			{
				$st="BLUE";
			}
			
			
			
			/** END **/
			
			$machinepart_data[] = array('sr_no'=>$i,
			'category'=>$row->category,
			'machine_part'=>$row->part,
			'specification'=>$row->specification,
			'makes'=>$row->makes,
			'size_in_mm'=>$row->size_in_mm,
			'material'=>$row->material,
			'raw_bop'=>$row->raw_bop,
			'fincode'=>$row->fincode,
			'unit'=>$unitname,
			'stock'=>"<span style='color:red;font-weight:bold;'>".floatval($row->current_stock)."</span>",
			'minstock'=>"<span style='color:red;font-weight:bold;'>".floatval($row->min_stock)."</span>",
			'minstockstatus'=>$st,
			'qty'=>$row->qty,
			'location'=>$row->rack_location,
			'vendor'=>$vendor,
			'image'=>$image,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($machinepart_data),
			"iTotalDisplayRecords" => count($machinepart_data),
			"aaData"=>$machinepart_data);
			
		echo json_encode($results);
	}


	public function criticallevelitems()
	{
		$i=1;
		$machinepart_data= array();
		$this->db->select('a.id,a.part,a.unit,a.current_stock,a.min_stock, a.specification, a.makes, a.size_in_mm, a.material, a.raw_bop, a.fincode, a.qty, a.picture, d.rack_location, p.category')->from('machine_parts_with_picture  a')->join('store_rack_location d','a.location_id=d.id','left')->join('presto_machine_part_category p','a.category_id=p.id','left')->where('a.current_stock<a.min_stock')->where('critical','1');
	

		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			
		$edit = "<a href='".page_url."Store/edit_parts/".$row->id."' style='color:white;'><i class='fa fa-pencil'></i></a>";	
			
		if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$row->fincode.'.jpg')){
			    
				$IMG = product_items.$row->fincode.'.jpg';
				$image = "<img src='".$IMG."' style='width:100px;height:100px;'>";
			}else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$row->fincode.'.JPG')){
			    
				$IMG = product_items.$row->fincode.'.JPG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$row->fincode.'.jpeg')){
			    	$IMG = product_items.$row->fincode.'.jpeg';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
		    }else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$row->fincode.'.JPEG')){
			    
				$IMG = product_items.$row->fincode.'.JPEG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else{
				$image="";
			}
			
			$unitname=$this->getreturnunit($row->unit);
			$vendor=$this->getreturnvendorandprice($row->id);
			
			/** GET STOCK WISE COLOR **/
			$curst=$row->current_stock;
			$min=$row->min_stock;
			if($curst<=0.00)
			{
				$st="BLACK";
			}
			
			/** 33 PER **/
			
			$thr33=$min*0.33;
			
			/** 62 PER **/
			$thr62=$min*0.62;
			
			
			/** 100 PER **/
			$thr100=$min*1;
			
			if($curst>'0.00' && $curst<=$thr62)
			{
			$st="RED";	
			}
			
			
			if($curst>$thr33 && $curst<=$thr62)
			{
				$st="YELLOW";
			}
			
			
			if($curst>$thr62 && $curst<=$thr100)
			{
				$st="GREEN";
			}
			
			
			if($curst>$row->min_stock)
			{
				$st="BLUE";
			}
			
			
			
			/** END **/
			
			$machinepart_data[] = array('sr_no'=>$i,
			'category'=>$row->category,
			'machine_part'=>$row->part,
			'specification'=>$row->specification,
			'makes'=>$row->makes,
			'size_in_mm'=>$row->size_in_mm,
			'material'=>$row->material,
			'raw_bop'=>$row->raw_bop,
			'fincode'=>$row->fincode,
			'unit'=>$unitname,
			'stock'=>"<span style='color:red;font-weight:bold;'>".floatval($row->current_stock)."</span>",
			'minstock'=>"<span style='color:red;font-weight:bold;'>".floatval($row->min_stock)."</span>",
			'minstockstatus'=>$st,
			'qty'=>$row->qty,
			'location'=>$row->rack_location,
			'vendor'=>$vendor,
			'image'=>$image,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($machinepart_data),
			"iTotalDisplayRecords" => count($machinepart_data),
			"aaData"=>$machinepart_data);
			
		echo json_encode($results);
	}





	public function maximumlevelitems()
	{
		$i=1;
		$machinepart_data= array();
		$this->db->select('a.id,a.part,a.unit,a.current_stock,a.min_stock, a.specification, a.makes, a.size_in_mm, a.material, a.raw_bop, a.fincode, a.qty, a.picture, d.rack_location, p.category')->from('machine_parts_with_picture  a')->join('store_rack_location d','a.location_id=d.id','left')->join('presto_machine_part_category p','a.category_id=p.id','left')->where('a.min_stock>','0')->where('a.current_stock>a.min_stock')->order_by('critical','DESC');
	

		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			
		$curstock=$row->current_stock;
		$maxlevel=$row->min_stock*0.10;
		$maxlevel=$row->min_stock+$maxlevel;
		
		if($curstock>$maxlevel)
		{
		$edit = "<a href='".page_url."Store/edit_parts/".$row->id."' style='color:white;'><i class='fa fa-pencil'></i></a>";	
			
		if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$row->fincode.'.jpg')){
			    
				$IMG = product_items.$row->fincode.'.jpg';
				$image = "<img src='".$IMG."' style='width:100px;height:100px;'>";
			}else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$row->fincode.'.JPG')){
			    
				$IMG = product_items.$row->fincode.'.JPG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$row->fincode.'.jpeg')){
			    	$IMG = product_items.$row->fincode.'.jpeg';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
		    }else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$row->fincode.'.JPEG')){
			    
				$IMG = product_items.$row->fincode.'.JPEG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else{
				$image="";
			}
			
			$unitname=$this->getreturnunit($row->unit);
			$vendor=$this->getreturnvendorandprice($row->id);
			
			/** GET STOCK WISE COLOR **/
			$curst=$row->current_stock;
			$min=$row->min_stock;
			if($curst<=0.00)
			{
				$st="BLACK";
			}
			
			/** 33 PER **/
			
			$thr33=$min*0.33;
			
			/** 62 PER **/
			$thr62=$min*0.62;
			
			
			/** 100 PER **/
			$thr100=$min*1;
			
			if($curst>'0.00' && $curst<=$thr62)
			{
			$st="RED";	
			}
			
			
			if($curst>$thr33 && $curst<=$thr62)
			{
				$st="YELLOW";
			}
			
			
			if($curst>$thr62 && $curst<=$thr100)
			{
				$st="GREEN";
			}
			
			
			if($curst>$row->min_stock)
			{
				$st="BLUE";
			}
			
			
			
			/** END **/
			
			$machinepart_data[] = array('sr_no'=>$i,
			'category'=>$row->category,
			'machine_part'=>$row->part,
			'specification'=>$row->specification,
			'makes'=>$row->makes,
			'size_in_mm'=>$row->size_in_mm,
			'material'=>$row->material,
			'raw_bop'=>$row->raw_bop,
			'fincode'=>$row->fincode,
			'unit'=>$unitname,
			'stock'=>"<span style='color:red;font-weight:bold;'>".floatval($row->current_stock)."</span>",
			'minstock'=>"<span style='color:red;font-weight:bold;'>".floatval($row->min_stock)."</span>",
			'minstockstatus'=>$st,
			'qty'=>$row->qty,
			'location'=>$row->rack_location,
			'vendor'=>$vendor,
			'image'=>$image,
			'edit'=>$edit);
			$i++;
		}
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($machinepart_data),
			"iTotalDisplayRecords" => count($machinepart_data),
			"aaData"=>$machinepart_data);
			
		echo json_encode($results);
	}
	
	function filteredims()
	{
	    $this->load->view('store/filteredims');
	}


	public function filtered_machine_part_data_with_picture_list()
	{
	    $type=$this->uri->segment(3);
	
		$machinepart_data= array();
		$this->db->select('a.id,a.part,a.unit,a.current_stock,a.min_stock, a.specification, a.makes, a.size_in_mm, a.material, a.raw_bop, a.fincode, a.qty, a.picture, d.rack_location, p.category')->from('machine_parts_with_picture  a')->join('store_rack_location d','a.location_id=d.id','left')->join('presto_machine_part_category p','a.category_id=p.id','left');

		$query = $this->db->get();
		$res = $query->result();
			$i=1;
		foreach($res as $row){
			
		$edit = "<a href='".page_url."Store/edit_parts/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			
		if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$row->fincode.'.jpg')){
			    
				$IMG = product_items.$row->fincode.'.jpg';
				$image = "<img src='".$IMG."' style='width:100px;height:100px;'>";
			}else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$row->fincode.'.JPG')){
			    
				$IMG = product_items.$row->fincode.'.JPG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$row->fincode.'.jpeg')){
			    	$IMG = product_items.$row->fincode.'.jpeg';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
		    }else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$row->fincode.'.JPEG')){
			    
				$IMG = product_items.$row->fincode.'.JPEG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else{
				$image="";
			}
			
			$unitname=$this->getreturnunit($row->unit);
			$vendor=$this->getreturnvendorandprice($row->id);
			
			/** GET STOCK WISE COLOR **/
			
			$curst=$row->current_stock;
			$min=$row->min_stock;
			
            /** 33 PER **/
            
            $thr33=$min*0.33;
            
            /** 62 PER **/
            $thr62=$min*0.62;
            
            
            /** 100 PER **/
            $thr100=$min*1;
		
			
			if($type=='1')
			{
			if($curst<=0.00)
			{
				$st="BLACK";
					$machinepart_data[] = array('sr_no'=>$i,
			'category'=>$row->category,
			'machine_part'=>$row->part,
			'specification'=>$row->specification,
			'makes'=>$row->makes,
			'size_in_mm'=>$row->size_in_mm,
			'material'=>$row->material,
			'raw_bop'=>$row->raw_bop,
			'fincode'=>$row->fincode,
			'unit'=>$unitname,
			'stock'=>$row->current_stock,
			'minstock'=>$row->min_stock,
			'minstockstatus'=>$st,
			'qty'=>$row->qty,
			'location'=>$row->rack_location,
			'vendor'=>$vendor,
			'image'=>$image,
			'edit'=>$edit);
			
			$i++;
			    
			}
			
			}
		
			
			if($type=='2')
			{
			if($curst>0.00 && $curst<=$thr62)
			{
			$st="RED";
			$ifappl=$this->storemodel->checkifprraised($row->id);
			if($ifappl==0)
			{
			$spr="<input type='checkbox' class='storecheck' name='itemid[]' value='".$row->id."'>";
			$remainstock=$row->min_stock-$row->current_stock;
		
			$prqty="<input type='hidden' name='qty".$row->id."' class='form-control' value='".$remainstock."'><input type='hidden' name='unit".$row->id."' value='".$row->unit."'><input type='hidden' name='currstock".$row->id."' value='".$row->current_stock."'>";
				$machinepart_data[] = array('pr'=>$spr,'sr_no'=>$i,
			'category'=>$row->category,
			'machine_part'=>$row->part." ".$prqty,
			'specification'=>$row->specification,
			'makes'=>$row->makes,
			'size_in_mm'=>$row->size_in_mm,
			'material'=>$row->material,
			'raw_bop'=>$row->raw_bop,
			'fincode'=>$row->fincode,
			'unit'=>$unitname,
			'stock'=>$row->current_stock,
			'minstock'=>$row->min_stock,
			'minstockstatus'=>$st,
			'qty'=>$row->qty,
			'location'=>$row->rack_location,
			'vendor'=>$vendor,
			'image'=>$image,
			'edit'=>$edit);
			$i++;
			  
			}  
			}
			}
			
			
            if($type=='3')
            {
            if($curst>$thr33 && $curst<=$thr62)
            {
            $st="YELLOW";
            $ifappl=$this->storemodel->checkifprraised($row->id);
			if($ifappl==0)
			{
            $spr="<input type='checkbox' class='storecheck' name='itemid[]' value='".$row->id."'>";
            $remainstock=$row->min_stock-$row->current_stock;
			
			$prqty="<input type='hidden' name='qty".$row->id."' class='form-control' value='".$remainstock."'><input type='hidden' name='unit".$row->id."' value='".$row->unit."'><input type='hidden' name='currstock".$row->id."' value='".$row->current_stock."'>";
			$machinepart_data[] = array('pr'=>$spr,'sr_no'=>$i,
            'category'=>$row->category,
            'machine_part'=>$row->part." ".$prqty,
            'specification'=>$row->specification,
            'makes'=>$row->makes,
            'size_in_mm'=>$row->size_in_mm,
            'material'=>$row->material,
            'raw_bop'=>$row->raw_bop,
            'fincode'=>$row->fincode,
            'unit'=>$unitname,
            'stock'=>$row->current_stock,
            'minstock'=>$row->min_stock,
            'minstockstatus'=>$st,
            'qty'=>$row->qty,
            'location'=>$row->rack_location,
            'vendor'=>$vendor,
            'image'=>$image,
            'edit'=>$edit);
            $i++;
                
            }
            }
            
            }
			
			
            if($type=='4')
            {
            if($curst>$thr62 && $curst<=$thr100)
            {
            $st="GREEN";
            $machinepart_data[] = array('sr_no'=>$i,
            'category'=>$row->category,
            'machine_part'=>$row->part,
            'specification'=>$row->specification,
            'makes'=>$row->makes,
            'size_in_mm'=>$row->size_in_mm,
            'material'=>$row->material,
            'raw_bop'=>$row->raw_bop,
            'fincode'=>$row->fincode,
            'unit'=>$unitname,
            'stock'=>$row->current_stock,
            'minstock'=>$row->min_stock,
            'minstockstatus'=>$st,
            'qty'=>$row->qty,
            'location'=>$row->rack_location,
            'vendor'=>$vendor,
            'image'=>$image,
            'edit'=>$edit);
           $i++;
           }
            
            }
			
			
			if($type=='5')
			{
			if($curst>$row->min_stock)
			{
				$st="BLUE";
					$machinepart_data[] = array('sr_no'=>$i,
			'category'=>$row->category,
			'machine_part'=>$row->part,
			'specification'=>$row->specification,
			'makes'=>$row->makes,
			'size_in_mm'=>$row->size_in_mm,
			'material'=>$row->material,
			'raw_bop'=>$row->raw_bop,
			'fincode'=>$row->fincode,
			'unit'=>$unitname,
			'stock'=>$row->current_stock,
			'minstock'=>$row->min_stock,
			'minstockstatus'=>$st,
			'qty'=>$row->qty,
			'location'=>$row->rack_location,
			'vendor'=>$vendor,
			'image'=>$image,
			'edit'=>$edit);
			
			$i++;
			    
			}
			
			}
			
			
			
			/** END **/
			
			
		

		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($machinepart_data),
			"iTotalDisplayRecords" => count($machinepart_data),
			"aaData"=>$machinepart_data);
			
		echo json_encode($results);
	}
	
	
	function generateimsautopr()
	{
		$type=$this->uri->segment(3);
	
		$item=$this->input->post('itemid');
		$prnos=$this->db->select('id')->from('purchase_request')->group_by('prno')->get();
		$num=$prnos->num_rows();
		if($num==0)
		{
		$num1=1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPR'.$num_padded;
		}else{
		$num1=$num+1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPR'.$num_padded;

		}
	
		if(count($item)>0)
		{
		for($i=0;$i<count($item);$i++)
		{
			$itemid=$item[$i];
			$unit=$this->input->post('unit'.$itemid);
			$stock=$this->input->post('currstock'.$itemid);
			$qty=$this->input->post('qty'.$itemid);
			
			
			$data=array('masterid'=>$itemid,'itemid'=>$itemid,'source'=>'3','prno'=>$code,'qty'=>$qty,'unit'=>$unit,'stockattimeofpr'=>$stock,'addedBy'=>$_SESSION['logged_in']['user_id'],'addedOn'=>date('Y-m-d H:i:s'));
			$this->db->insert('purchase_request',$data);
			
		}
		
		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">PR Generated.</div>');
		redirect(page_url.'Store/filteredims/'.$type);
	}else
	{
	    	
		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Select Item to raise PR.</div>');
		redirect(page_url.'Store/filteredims/'.$type);
	}
		
		
	}
	
	public function received_items_edit(){
	    $this->load->view('store/receive_item_edit');
	}
	
	public function update_received_item_serial_number(){
	    
	    $data = array('serial_number'=>$this->input->post('serial_number'));
	    $this->db->where('id',$this->uri->segment(3));
	    $this->db->update('imported_items_recieved',$data);
	    	$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank You! Record successfully updated.</div>');
		redirect(page_url.'Store/received_item_dashboard/');
	}
	
	function getmachines()
	{
		$searchtrm= $_GET['q'];
		$type = $this->uri->segment(3);
		if($type=='1'){
		
		$query = $this->db->select('id, part')->from('machine_parts_with_picture')->like('part',$searchtrm,'both',false)->where('status','1')->get();
		if($query->num_rows()>0)
		{
		foreach($query->result() as $machine_items){

		$json[] = array('id'=>$machine_items->id, 'text'=>$machine_items->part);

		}
		}else{

		$json[] = array('id'=>"", 'text'=>"No Data Available");

		}

		echo json_encode($json);
		}else{
			
			
			$query = $this->db->select('id, item_name, status')->from('house_keeping_items')->like('item_name',$searchtrm,'both',false)->where('status','1')->get();
		if($query->num_rows()>0)
		{
		foreach($query->result() as $instruments){

		$json[] = array('id'=>$instruments->id, 'text'=>$instruments->item_name);

		}
		}else{

		$json[] = array('id'=>"", 'text'=>"No Data Available");

		}

		echo json_encode($json);
		}
	
}

function getitemunit(){
	$item_type = $this->input->post('ot');
	$item_id = $this->input->post('item');
	if($item_type=='1'){
		$q = $this->db->select('a.unit, b.id, b.shortname')->from('machine_parts_with_picture a')->join('units b','a.unit=b.id','left')->where('a.id',$item_id)->get();
		foreach($q->result() as $row);
		echo $row->shortname; exit;
	}else{
		$q = $this->db->select('a.unit, b.id, b.shortname')->from('house_keeping_items a')->join('units b','a.unit=b.id','left')->where('a.id',$item_id)->get();
		foreach($q->result() as $row);
		echo $row->shortname; exit;
	}
	
	

}

function getselected_itemprice(){
	$item_type = $this->input->post('ot');
	$item_id = $this->input->post('item');
	$qty = $this->input->post('qty');
	if($item_type=='1'){
		$q = $this->db->select('price')->from('vendors_price a')->where('itemid',$item_id)->get();
		if($q->num_rows()>0){
		foreach($q->result() as $row);
		echo $row->price*$qty; exit;
		}else{
			echo "Price not set";
		}
	}else{
		$q = $this->db->select('price')->from('vendorwise_house_keeping_item_price a')->where('item_id',$item_id)->get();
		if($q->num_rows()>0){
		foreach($q->result() as $row);
		echo $row->price*$qty; exit;
		}else{
			echo "Price not set";
		}
	}
	
	
}
public function followup_dashboard(){
	$this->load->view('store/followup-dashboard');
}
}