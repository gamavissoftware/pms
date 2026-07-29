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
				move_uploaded_file($_FILES["picture"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/newbeta/image_bank/product_items/' . $pic);
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
				move_uploaded_file($_FILES["picture"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/newbeta/image_bank/product_items/' . $pic);
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


function saveindent()
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

function generateprfromintend()
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
				move_uploaded_file($_FILES["picture"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/newbeta/image_bank/housekeeping/' . $pic);
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
				move_uploaded_file($_FILES["picture"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/newbeta/image_bank/housekeeping/' . $pic);
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
		$this->db->select('*')->from('presto_instruments')->where('type','1')->order_by('instruments_name','asc');
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
			
			/** RECIEVE ITEMS **/
			$rcvqty=$this->db->select('sum(qty) as recvqty')->from('imported_items_recieved')->where('itemid',$row->id)->get();
			foreach($rcvqty->result() as $rcvqty1);
			/*** END **/
			
			$imported_data[] = array('sr_no'=>$i,
			'description'=>$row->instruments_name,
			'current_stock'=>$row->stock,
			'opening_stock'=>'',
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
				'returnable'=>$this->input->post('returnable'),
				'return_date'=>$rdate,
				'orderno'=>$ono,
				'added_on'=>date('Y-m-d H:i:s'),
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
	
	
	public function issued_item_dashboard(){
	    $this->load->view('store/issue_items_report');
	}
	
	
	public function received_item_dashboard(){
	    $this->load->view('store/received_item_dashboard');
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
				$pic=time().'.'.$cat_image;
				move_uploaded_file($_FILES["picture"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/newbeta/image_bank/product_item/' . $pic);
			}else
			{
				$pic="";
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
			'picture'=>$pic,
			'unit'=>$this->input->post('unit'),
			'current_stock'=>$this->input->post('stock'),
			'min_stock'=>$this->input->post('minstock'),
			'status'=>'1',
			'addedOn'=>date('Y-m-d H:i:s'),
			'addedBy'=>$_SESSION['logged_in']['user_id']);
			
		$result  = $this->db->insert($table,$data);	
		$lid = $this->db->insert_id();	
		if($this->db->affected_rows()>0)
		{
			/** ADD SUPPLIERS **/
			$vendor=$this->input->post('vendor');
			
			$price=$this->input->post('price');
			for($i=0;$i<count($vendor);$i++)
			{
				if(trim($vendor[$i])<>'' && trim($price[$i]<>''))
				{
				
				$vdata=array('masterid'=>$lid,'itemid'=>$lid,'vendorid'=>$vendor[$i],'price'=>$price[$i]);
				
				$this->db->insert('vendors_price',$vdata);
				
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
	
	
	public function machine_part_data_with_picture_list()
	{
		$i=1;
		$machinepart_data= array();
		$this->db->select('a.id,a.part,a.unit,a.current_stock,a.min_stock, a.specification, a.makes, a.size_in_mm, a.material, a.raw_bop, a.fincode, a.qty, a.picture, d.rack_location, p.category')->from('machine_parts_with_picture  a')->join(' store_rack_location d','a.location_id=d.id','left')->join('presto_machine_part_category p','a.category_id=p.id','left');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			
		$edit = "<a href='".page_url."Store/edit_parts/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			
			if($row->picture){
				$IMG = product_items.$row->picture;
				$image = "<img src='".$IMG."' width='100px'>";
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
				$pic=time().'.'.$cat_image;
				move_uploaded_file($_FILES["picture"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/newbeta/image_bank/product_item/' . $pic);
				unlink($_SERVER['DOCUMENT_ROOT'].'/newbeta/image_bank/product_item/'.$oldimg);
			}else
			{
				$pic=$oldimg;
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
			'picture'=>$pic,
			'unit'=>$this->input->post('unit'),
			'current_stock'=>$this->input->post('stock'),
			'min_stock'=>$this->input->post('minstock'),
			'status'=>'1',
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
			
			$price=$this->input->post('price');
			for($i=0;$i<count($vendor);$i++)
			{
				if(trim($vendor[$i])<>'' && trim($price[$i]<>''))
				{
				
				$vdata=array('masterid'=>$lid,'itemid'=>$lid,'vendorid'=>$vendor[$i],'price'=>$price[$i]);
				
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
				$price=$this->input->post('exiprice'.$exid);
				if(trim($vendor)<>'' && trim($price<>''))
				{
				
				$vdata=array('masterid'=>$lid,'itemid'=>$lid,'vendorid'=>$vendor,'price'=>$price);
				
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
	'qty'=>$this->input->post('qty'),'remarks'=>$this->input->post('remarks'),'addedOn'=>date('Y-m-d H:i:s'),
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
					
		$imported_data[] = array('sr_no'=>$i,
		'instruments_name'=>$row->instruments_name,
			'rtype'=>$r,
			'qty'=>$row->qty,
			'remarks'=>$row->remarks,
			'addedBy'=>$to,
			'addedOn'=>date('d-m-Y g:i A',strtotime($row->addedOn)));
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
			redirect(page_url.'Reporting/pendingpo/'.$gpono);
			
		}else{
			
			$this->session->set_flashdata('response','Unable to generate PO');
			redirect(page_url.'Store/createpo/'.$prno);
			
		}
		
	}
	
	
	
		function getnextponumber()
	{
		$prnos=$this->db->select('id')->from('purchase_order')->group_by('pono')->get();
	$num=$prnos->num_rows();
	if($num==0)
	{
		$num1=1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPO'.$num_padded;
	}else{
		$num1=$num+1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPO'.$num_padded;
		
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
	

	


}