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
        $config['smtp_host'] = 'smtpout.secureserver.net';  
        $config['smtp_user'] = 'mitr@prestomitr.com';  
        $config['smtp_pass'] = 'Presto@123!@#';   
        $config['smtp_port'] = 587;  
        $this->email->initialize($config);  
        $this->email->set_newline("\r\n");  
        $this->load->library('email', $config);

$ip = $_SERVER["REMOTE_ADDR"];

	if($user_id=='66' || $user_id=='67' || $user_id=='1'){
			
		}else{
		 $query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
            if($query->num_rows()=='0'){
            $this->session->set_flashdata('message','<span class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</span><br/>');
            redirect(page_url.'Dashboard');    
            }
		
		}
		
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
		$this->db->select('a.id,a.gst, a.item_id, a.part_id, a.specification, a.makes, a.size_in_mm, a.material, a.raw_bop, a.fincode, a.qty, a.picture, b.instruments_name,c.machine_part, d.rack_location, p.category, a.vendor, a.price')->from('machine_parts_with_picture a')->join('presto_instruments b','a.item_id=b.id','left')->join('machine_parts_master c','a.part_id=c.part_id','left')->join(' store_rack_location d','a.location_id=d.id','left')->join('presto_machine_part_category p','a.category_id=p.id','left');
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
			'gst'=>"<span style='font-size:11px;'>".$row->gst."</span>",
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
				move_uploaded_file($_FILES["picture"]["tmp_name"],UPLOADPATH.'product_items/' . $pic);
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
				move_uploaded_file($_FILES["picture"]["tmp_name"],UPLOADPATH.'product_items/' . $pic);
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
	
	$prnos=$this->db->select('id,purno')->from('purchase_request')->order_by('id','DESC')->limit(1)->get();
	$num=$prnos->num_rows();
	if($num==0)
	{
		$num1=1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPR'.$num_padded;
	}else{
	     foreach($prnos->result() as $prnoss);
	    $numpart =$prnoss->purno;
	    	$num1=$numpart+1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPR'.$num_padded;
		
	}
	
	$resty=$this->db->select('itemid,qty,unit')->from('intend_request')->where('indendno',$indentno)->get();
	if($resty->num_rows()>0)
	{
		
		foreach($resty->result() as $resty1)
		{
		$prnumonly=preg_replace('/[^0-9]/', '', $code);
			
			$data=array('itemid'=>$resty1->itemid,'source'=>'2','sourceid'=>$indentno,'prno'=>$code,'qty'=>$resty1->qty,'unit'=>$resty1->unit,'addedBy'=>$_SESSION['logged_in']['user_id'],'addedOn'=>date('Y-m-d H:i:s'),'purno'=>$prnumonly);
			
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
	$employeeid = $this->input->post('employeecode');
	$remarks  = $this->input->post('remarks');
	$qty=$this->input->post('qty');
	/** Check for any previous intend**/
            $inde=$this->db->select('indendno')->from('intend_request')->order_by('indendno','DESC')->limit(1)->get();
            $ninde=$inde->num_rows();
            if($ninde==0)
            {
            $no=1;
            }else{
            foreach($inde->result() as $prnoss);
            $str = $prnoss->indendno;
            $numpart = (int) filter_var($str, FILTER_SANITIZE_NUMBER_INT);
            
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
		'employeecode'=>$employeeid,
		'remarks'=>$remarks,
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
		$this->generateprfromintendautoapproval($indno);
	}
	$this->session->set_flashdata('message','<div class="alert alert-success">Indent Request Created.</div>');
	redirect(page_url.'Store/indent_form');
	
	
	
}

function generateprfromintend()
{
	
	$indentno=$this->uri->segment(3);
	
    		$prnos=$this->db->query('SELECT id, purno FROM purchase_request ORDER BY  CAST(purno AS decimal) DESC LIMIT 1');
	//$prnos=$this->db->select('id,purno')->from('purchase_request')->order_by('id','DESC')->limit(1)->get();
	$num=$prnos->num_rows();
	if($num==0)
	{
		$num1=1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPR'.$num_padded;
	}else{
	     foreach($prnos->result() as $prnoss);
	    $numpart = $prnoss->purno;
	    	$num1=$numpart+1;
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


$prnumonly=preg_replace('/[^0-9]/', '', $code);			
			$data=array('masterid'=>$resty1->itemid,'type'=>$indenttype,'itemid'=>$resty1->itemid,'source'=>'2','sourceid'=>$indentno,'prno'=>$code,'qty'=>$resty1->qty,'unit'=>$resty1->unit,'addedBy'=>$_SESSION['logged_in']['user_id'],'addedOn'=>date('Y-m-d H:i:s'),'purno'=>$prnumonly);
			
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
		    
		 $masterid=$this->input->post('masterreqid');
		 //echo $masterid;exit;
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
				move_uploaded_file($_FILES["picture"]["tmp_name"],UPLOADPATH.'housekeeping/' . $pic);
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
			'gst'=>$this->input->post('gst'),
			'status'=>$this->input->post('status'),
			'added_by'=>$user_id,
			'added_on'=>$date);
			
		$result  = $this->db->insert($table,$data);
		$lastid = $this->db->insert_id();		
		if($result)
		{
		    
		    
			if($masterid<>'')
			{
				$this->generateautoindent($masterid,$lastid);
				
			}
			
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
				
					if($masterid<>'')
			{
			    	$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added and indent has been created</div>');
			redirect(page_url.'Store/add_general_items');
			}else
			{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Store/add_general_items');
			}
			
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
			$query = $this->db->select('a.vendor_id, a.price, b.id, b.name')->from('vendorwise_house_keeping_item_price a')->join('vendors b','a.vendor_id=b.id','left')->where('a.item_id',$row->id)->get();
			foreach($query->result() as $pricelist){
				
					$backgroundcolor = "background-color:green; color:#fff; font-weight:bold;";
				
				$html.="<tr style='".$backgroundcolor."'>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$m."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($pricelist->name)."</td>";
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
			'gst'=>$row->gst,
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
				move_uploaded_file($_FILES["picture"]["tmp_name"],UPLOADPATH.'housekeeping/' . $pic);
			}else
			{
				$pic=$this->input->post('old_img');
				}
				
		$data = array('item_name'=>strtoupper($this->input->post('item_name')),
			'qty'=>$this->input->post('qty'),
			'min_qty'=>$this->input->post('min_qty'),
			'unit'=>$this->input->post('unit'),
			'picture'=>$pic,
			'gst'=>$this->input->post('gst'),
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
		    $blockitem="";
			if($row->stock!=0)
			{
			$issueitem = "<a href='".page_url."Store/issue_items/".$row->id."'><span class='btn btn-success btn-xs'>Issue Item</span></a>";
		}else{
			
				$issueitem= "<a href='".page_url."Store/issue_items/".$row->id."/1'><span class='btn btn-success btn-xs'>Issue List</span></a>";
				$issueitem.="<span style='color:red;'>Stock Not Available</span><br/>";
		}
			$receive_item = "<a href='".page_url."Store/receive_item/".$row->id."'><span class='btn btn-warning btn-xs'>Receive Item</span></a>";
			if($row->stock!=0)
			{
			$blockitem = "";
			}else
			{
			    $blockitem="<span style='color:red;'>Stock Not Available</span>";
			}
			$edit = "<a href='".page_url."Store/edit_imported_items/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			
		$html="<table border='1'><tr style=''><th style='padding:2px 2px 2px 2px; text-align:center;'>Party Name</th><th style='padding:2px 2px 2px 2px; text-align:center;'>Jobcard No</th><th style='padding:2px 2px 2px 2px; text-align:center'>Blocked Qty</th><th style='padding:2px 2px 2px 2px; text-align:center'>Action</th><tbody><tr>";
			
			$query = $this->db->select('id,party_name,jobcard,blocked_qty,cancelled_order, cancellation_remarks')->from('imported_item_blocked')->where('item_id',$row->id)->where('dispatched','0')->get();
			$blockedcount  = count($query->result());   
			foreach($query->result() as $result){
			    if($result->cancelled_order=='0'){
				$return = "<a href='".page_url."Store/cancel_order/".$result->id."/".$row->id."'><span class='btn btn-success btn-xs'>Cancel</span></a>";
				}else{
					$return = $result->cancellation_remarks;
				}
				$jobcardname=$this->getjobcardno($result->jobcard);
			$html.="<tr><td style='padding:2px 2px 2px 2px; text-align:center'>".$result->party_name."</td><td style='padding:2px 2px 2px 2px; text-align:center;'>".$jobcardname."</td><td style='padding:2px 2px 2px 2px; text-align:center;'>".$result->blocked_qty."</td><td style='padding:2px 2px 2px 2px; text-align:center;'>".$return."</td></tr>";	
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
			
			if($row->stock>0)
			{
			    if($row->stock>$blockedcount)
			    {
			$extra = $row->stock-$blockedcount;
			    }else
			    {
			        $extra=0;
			    }
			}else
			{
			    $extra=0;
			}
			
			$imported_data[] = array('sr_no'=>$i,
			'description'=>$row->instruments_name,
			'current_stock'=>$row->stock,
			'blocked'=>$blockedcount,
			'extra'=>$extra,
			'opening_stock'=>'',
			'machine_serial_number'=>$serialnumber,
			'min_stock'=>$row->minstock,
			'total_received'=>$rcvqty1->recvqty,
			'price'=>$row->mvalue,
			'total_issued'=>$isqty1->issueqty,
			'update_status'=>$issueitem,
			'block_item'=>$html."<br><br>".$blockitem,
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
				    if($this->input->post('whyneed')=='DEMO')
				    {
				      $customername=$this->input->post('custname');
				      $rgpno=$this->input->post('rgpno');
				    }else
				    {
				        $customername='';
				        $rgpno='';
				    }
				    
			
				$data = array('item_id'=>$this->uri->segment(3),
				'user_id'=>$this->input->post('user'),
				'qty'=>$this->input->post('qty'),
				'whyneed'=>$this->input->post('whyneed'),
				'machine_serial_number'=>$this->input->post('machine_serial_number'),
				'returnable'=>$this->input->post('returnable'),
				'return_date'=>$rdate,
				'orderno'=>$ono,
				'added_on'=>$date,
				'added_by'=>$_SESSION['logged_in']['user_id'],
				'customername'=>$customername,
				'rgpno'=>$rgpno);
				
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
		if($this->uri->segment(3)){
		    $this->db->where('a.item_id',$this->uri->segment(3));
		}
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
		    $masterid=$this->input->post('masterreqid');
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "machine_parts_with_picture";
			
			$photo=$_FILES['picture']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$pic=$this->input->post('fincode').'.'.$cat_image;
				move_uploaded_file($_FILES["picture"]["tmp_name"],UPLOADPATH.'product_item/' . $pic);
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
			'conversion_unit'=>$this->input->post('conversion_unit'),
			'conversion_weight'=>$this->input->post('conversion_weight'),
			'gst'=>$this->input->post('gst'),
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
			
			
			if($masterid<>'')
			{
			$this->generateautoindent($masterid,$lid);
			}
			
			if($masterid<>'')
			{
			$this->session->set_flashdata('message','<div class="alert alert-info alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Store/machineparts');
			}else
			{
			    	$this->session->set_flashdata('message','<div class="alert alert-info alert-dismissable">Thank you, record successfully updated and indent created</div>');
			redirect(page_url.'Store/machineparts');
			    
			}
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Store/add_parts');
		}
		
		
	}
		
	}
	
	
	
		public function machine_part_data_with_picture_list()
	{
		$i=1;
		$machinepart_data= array();
		$this->db->select('a.id,a.gst,a.part,a.unit,a.current_stock,a.min_stock, a.specification, a.makes, a.size_in_mm, a.material, a.raw_bop, a.fincode, a.qty, a.picture, d.rack_location, p.category')->from('machine_parts_with_picture  a')->join('store_rack_location d','a.location_id=d.id','left')->join('presto_machine_part_category p','a.category_id=p.id','left');
	
//	$this->db->select('a.id,a.part,a.unit,a.current_stock,a.min_stock, a.specification, a.makes, a.size_in_mm, a.material, a.raw_bop, a.fincode, a.qty, a.picture, d.rack_location, p.category')->from('machine_bom w')->join('machine_parts_with_picture  a','w.partid=a.id')->join('store_rack_location d','a.location_id=d.id','left')->join('presto_machine_part_category p','a.category_id=p.id','left')->group_by('w.partid');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			
		$edit = "<a href='".page_url."Store/edit_parts/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			
			if($row->picture=='')
			{
		if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.jpg')){
			    
				$IMG = product_items.$row->fincode.'.jpg';
				$image = "<img src='".$IMG."' style='width:100px;height:100px;'>";
			}else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.JPG')){
			    
				$IMG = product_items.$row->fincode.'.JPG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.jpeg')){
			    	$IMG = product_items.$row->fincode.'.jpeg';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
		    }else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.JPEG')){
			    
				$IMG = product_items.$row->fincode.'.JPEG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else{
				$image="";
			}
			
			}else
			{
                	if(file_exists(UPLOADPATH.'product_item/'.$row->picture.'.jpg')){
			    
				$IMG = product_items.$row->picture.'.jpg';
				$image = "<img src='".$IMG."' style='width:100px;height:100px;'>";
			}else if(file_exists(UPLOADPATH.'product_item/'.$row->picture.'.JPG')){
			    
				$IMG = product_items.$row->picture.'.JPG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else if(file_exists(UPLOADPATH.'product_item/'.$row->picture.'.jpeg')){
			    	$IMG = product_items.$row->picture.'.jpeg';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
		    }else if(file_exists(UPLOADPATH.'product_item/'.$row->picture.'.JPEG')){
			    
				$IMG = product_items.$row->picture.'.JPEG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else{
			
			if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.jpg')){
			    
				$IMG = product_items.$row->fincode.'.jpg';
				$image = "<img src='".$IMG."' style='width:100px;height:100px;'>";
			}else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.JPG')){
			    
				$IMG = product_items.$row->fincode.'.JPG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.jpeg')){
			    	$IMG = product_items.$row->fincode.'.jpeg';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
		    }else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.JPEG')){
			    
				$IMG = product_items.$row->fincode.'.JPEG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else{
				$image="";
			}
			}
			    
			    
			    
			}
			
			
				/** ISSUE ITEMS **/
		$isss="<a href='".page_url."Store/issuemachineitems/".$row->id."' class='btn btn-warning btn-xs'>Issue Item</a>";
		/** END **/
		
		
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
			
				$blockeddata=$this->getactiveblockeddetails($row->id);
				$blockeddatacount=$this->getactiveblockedcount($row->id);
			
				$addstock="<a href='".page_url."Store/updatestock/".$row->id."'><span class='btn btn-warning btn-xs'>Add Stock</span></a>";
			
			/** END **/
			$extrastock=$row->current_stock-$blockeddatacount;
			
			$machinepart_data[] = array('sr_no'=>$i,
			'issue'=>$isss,
			'addstock'=>$addstock,
			'category'=>$row->category,
			'machine_part'=>$row->part,
			'specification'=>$row->specification,
			'makes'=>$row->makes,
			'size_in_mm'=>$row->size_in_mm,
			'material'=>$row->material,
			'fincode'=>$row->fincode."<br/>".$row->raw_bop,
			'unit'=>$unitname,
			'stock'=>$row->current_stock,
			'gst'=>$row->gst,
			'minstock'=>$row->min_stock,
			'minstockstatus'=>$st,
			'extrastock'=>$extrastock,
			'qty'=>$row->qty,
			'location'=>$row->rack_location,
			'vendor'=>$vendor,
			'image'=>$image,
			'edit'=>$edit,
			'blockedstock'=>$blockeddata);
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
			
		if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.jpg')){
			    
				$IMG = product_items.$row->fincode.'.jpg';
				$image = "<img src='".$IMG."' style='width:100px;height:100px;'>";
			}else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.JPG')){
			    
				$IMG = product_items.$row->fincode.'.JPG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.jpeg')){
			    	$IMG = product_items.$row->fincode.'.jpeg';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
		    }else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.JPEG')){
			    
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
			
			
		
               move_uploaded_file($_FILES['picture']['tmp_name'],UPLOADPATH.'product_item/'.$picss);
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
			'gst'=>$this->input->post('gst'),
			'raw_bop'=>$this->input->post('raw_bop'),
			'conversion_unit'=>$this->input->post('conversion_unit'),
			'conversion_weight'=>$this->input->post('conversion_weight'),
			'location_id'=>$this->input->post('rack_location'),
			'picture'=>$picss,
			'unit'=>$this->input->post('unit'),
			'current_stock'=>$this->input->post('stock'),
			'hsn'=>$this->input->post('hsn'),
			'min_stock'=>$this->input->post('minstock'),
			'status'=>'1',
			'critical'=>$upd,
			'consumption'=>$this->input->post('consumption'),
			'safetyfactor'=>$this->input->post('safety'),
			'leadtime'=>$this->input->post('ltime'),
			'maxstock'=>$this->input->post('maxstock'),
			'reorder'=>$this->input->post('reorder'),
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
		$this->db->select('a.*,b.instruments_name')->from('imported_items_recieved a')->join('presto_instruments b','a.itemid=b.id','left');
		if($this->uri->segment(3)){
		    $this->db->where('a.itemid',$this->uri->segment(3));
		}
		$restyu=$this->db->order_by('id','DESC')->get();
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
	
	$pri=$this->db->select('price,discount,listprice')->from('vendors_price')->where('masterid',$masterid)->where('itemid',$partid)->where('vendorid',$supid)->get();
	if($pri->num_rows()>0)
	{
		foreach($pri->result() as $price);
		if($price->discount>0)
		{
		echo floatval($price->price);
		}else
		{
		echo floatval($price->listprice);
		}
		
		
		
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
		
		
        $type=$this->getprtype($prno);
        
        if($source==1)
        {
        $jobcard=$this->input->post('jobcardno');
        }else{
        
        $jobcard=0;
        }

		if(count($supp)>0)
		{
			
		$usupp=array_values(array_unique(array_filter($supp)));
			$pon=array();
			for($i=0;$i<count($usupp);$i++)
			{
			$ponumber=$this->getnextponumber();
		
			$supplid=$usupp[$i];
			$itemss=$this->input->post('checkit'.$supplid);
		
			for($t=0;$t<count($itemss);$t++)
			{
			$item=$itemss[$t];
			$qty=$this->input->post('qty'.$item);
			$iprice=$this->input->post('itemprice'.$item);
			$currstock=$this->input->post('currentstock'.$item);
			$unit=$this->input->post('unit'.$item);
			$iremarks=$this->input->post('itemremarks'.$item);
			$oprice=$this->input->post('originalprice'.$item);
			//echo "i".$item.'<br/>'."q".$qty.'<br/>'."pri".$iprice;exit;
			if($item<>'' && $qty<>'' && $iprice<>'')
			{
			   if($oprice<>$iprice)
			   {
			       $priceedit='1';
			       $ogprice=$oprice;
			   }else
			   {
			        $priceedit='0';
			       $ogprice=$oprice;
			       
			   }
			  // $discount=$this->getitemdiscount($supplid,$item,$type);
			  $discount=$this->input->post('discountper'.$item);
			$pon[]=$ponumber;
			
			$qq = $this->db->select('deliveryby')->from('vendors')->where('id',$supplid)->get();
			foreach($qq->result() as $vendordata);
			if($vendordata->deliveryby=='By Vendor'){
			    $freight = "1";
			    
			}else{
			    $freight = "0";
			}
			
			$ponumonly=preg_replace('/[^0-9]/', '', $ponumber);
			$data=array('itemid'=>$item,'price'=>$iprice,'qty'=>$qty,'vendor'=>$supplid,'pono'=>$ponumber,'prno'=>$prno,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id'],'source'=>$source,'sourceid'=>$sourceid,'jobcard'=>$jobcard,'unit'=>$unit,'potype'=>$type,'remarks'=>$iremarks,'discount'=>$discount,'pricechange'=>$priceedit,'originalprice'=>$ogprice,'fright'=>$freight,'ponum'=>$ponumonly);
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



				/** item wise approval **/
				$data2=array('approvalstatus'=>'1','approvaldate'=>date('Y-m-d H:i:s'),'approvedBy'=>$_SESSION['logged_in']['user_id']);
				$this->db->where('prno',$prno);
				$this->db->where('itemid',$item);
				$this->db->update('purchase_request',$data2);
				
				/** end **/
			}

			}

			$c=count($usupp);
			if(count($pon)>0)
			{
			$gpono=base64_encode(implode(',',$pon));
			}else{

			$gpono='';			
			}


			$this->session->set_flashdata('message','<div class="alert alert-success">'.$c.' Po(s) generated</div>');
			redirect(page_url.'Reporting/pendingpr');

		}else{

			$this->session->set_flashdata('response','<div class="alert alert-danger">Unable to generate PO</div>');
			redirect(page_url.'Store/createpo/'.$prno);

			}
		
	}

function generatepoldbeforesingleselection()
	{
	   	$type=$this->input->post('type');
		$supp=$this->input->post('supplier');
		$prno=$this->input->post('pr');
		$source=$this->input->post('source');
		$sourceid=$this->input->post('sourceid');
		$insid=$this->input->post('instrumentid');
		
		
        $type=$this->getprtype($prno);
        
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
			$iremarks=$this->input->post('itemremarks'.$item);
			$oprice=$this->input->post('originalprice'.$item);
			if($item<>'' && $qty<>'' && $iprice<>'')
			{
			   if($oprice<>$iprice)
			   {
			       $priceedit='1';
			       $ogprice=$oprice;
			   }else
			   {
			        $priceedit='0';
			       $ogprice=$oprice;
			       
			   }
			   $discount=$this->getitemdiscount($supplid,$item,$type);
			$pon[]=$ponumber;
			
			$qq = $this->db->select('deliveryby')->from('vendors')->where('id',$supplid)->get();
			foreach($qq->result() as $vendordata);
			if($vendordata->deliveryby=='By Vendor'){
			    $freight = "1";
			    
			}else{
			    $fright = "0";
			}
			
			$data=array('itemid'=>$item,'price'=>$iprice,'qty'=>$qty,'vendor'=>$supplid,'pono'=>$ponumber,'prno'=>$prno,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id'],'source'=>$source,'sourceid'=>$sourceid,'jobcard'=>$jobcard,'unit'=>$unit,'potype'=>$type,'remarks'=>$iremarks,'discount'=>$discount,'pricechange'=>$priceedit,'originalprice'=>$ogprice,'fright'=>$freight);
			
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
			redirect(page_url.'Reporting/pendingpr');

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
		$prnos=$this->db->select('ponum')->from('purchase_order')->order_by('id','DESC')->limit(1)->get();
	$num=$prnos->num_rows();
	if($num==0)
	{
		$num1=1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPO'.$num_padded;
	}else{
	     foreach($prnos->result() as $prnoss);
		 //echo $prnoss->pono; exit;
	    $numpart = $prnoss->ponum;
	   // echo $numpart; exit;
	    
	   
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

	$prnos=$this->db->query('SELECT id, purno FROM purchase_request ORDER BY  CAST(purno AS decimal) DESC LIMIT 1');
	//$prnos=$this->db->select('id,purno')->from('purchase_request')->order_by('id','DESC')->limit(1)->get();
	$num=$prnos->num_rows();
	if($num==0)
	{
		$num1=1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPR'.$num_padded;
	}else{
	     foreach($prnos->result() as $prnoss);
	    $numpart = $prnoss->purno;
	    	$num1=$numpart+1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPR'.$num_padded;
		
	}
		
	
		if(count($item)>0)
		{
		for($i=0;$i<count($item);$i++)
		{
		  $prnumonly=preg_replace('/[^0-9]/', '', $code);
			$itemid=$item[$i];
			$unit=$this->input->post('unit'.$itemid);
			$stock=$this->input->post('currstock'.$itemid);
			$qty=$this->input->post('qty'.$itemid);
			
			
			$data=array('masterid'=>$itemid,'itemid'=>$itemid,'source'=>'3','prno'=>$code,'qty'=>$qty,'unit'=>$unit,'stockattimeofpr'=>$stock,'addedBy'=>$_SESSION['logged_in']['user_id'],'addedOn'=>date('Y-m-d H:i:s'),'purno'=>$prnumonly);
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
		
		echo floatval(round($price->price,1));
		
		
		
		
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
		
		/*GET VENDOR DELIVERY DATE*/
		$q = $this->db->select('vendor')->from('purchase_order')->where('pono',$pono)->get();
		foreach($q->result() as $row);
		$q1 = $this->db->select('deliverytime')->from('vendors')->where('id',$row->vendor)->get();
		foreach($q1->result() as $row1);
		//echo "<pre>"; print_r($row1); exit;
		$deliverytime = $row1->deliverytime;
		$adaybefore = $deliverytime-1;
		//echo $adaybefore; exit;
		$data= array('po_no'=>$pono,
		'followupdate'=>date('Y-m-d'));
		$this->db->insert('vendor_followup',$data);
		
		$seconddate = $deliverytime/2;
		$secondfollowupdate = floor($seconddate);
		$get2ndate = "+ ".$secondfollowupdate." day";
		$get3rdate = "+ ".$adaybefore." day";
		$secondscheduleddate = date('Y-m-d', strtotime($get2ndate));
		$thirdscheduleddate = date('Y-m-d', strtotime($get3rdate));
		//$data1 = array('po_no'=>$pono,
		//'followupdate'=>$secondscheduleddate);
		//$this->db->insert('vendor_followup',$data1);
		
		/*$data2 = array('po_no'=>$pono,
		'followupdate'=>$thirdscheduleddate);
		$this->db->insert('vendor_followup',$data2);*/
		
		/** SEND MAIL WHATSAPP FOR SUPPLIER **/
	//	$this->sendemailtosupplier($pono);
		
		/** END **/
		/*GET VENDOR DELIVERY DATE*/
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
		$datadb=array('dbno'=>$dbno,'pono'=>$this->input->post('pono'),'mrnhistoryid'=>$this->input->post('mrnhistoryid'),'itemid'=>$this->input->post('itemid'),'qty'=>$this->input->post('rqty'),'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id'],'approved'=>'1','reason'=>$this->input->post('reason'));
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
			
			
			$restyyu=$this->db->select('*')->from('service_repair_request')->where('flag', 0)->order_by('qc','ASC')->get();
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
						//$sta="<a href='".page_url."FMS/order/".base64_encode('service-'.$row->id)."'><span class='btn btn-warning btn-xs'>Generate Order</span></a>";
$sta="<a href='javascript:;' onclick='showReturnModal(".$row->id.")'><span class='btn btn-warning btn-xs'>Next Step</span></a>";
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
			
		if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.jpg')){
			    
				$IMG = product_items.$row->fincode.'.jpg';
				$image = "<img src='".$IMG."' style='width:100px;height:100px;'>";
			}else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.JPG')){
			    
				$IMG = product_items.$row->fincode.'.JPG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.jpeg')){
			    	$IMG = product_items.$row->fincode.'.jpeg';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
		    }else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.JPEG')){
			    
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
			
		if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.jpg')){
			    
				$IMG = product_items.$row->fincode.'.jpg';
				$image = "<img src='".$IMG."' style='width:100px;height:100px;'>";
			}else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.JPG')){
			    
				$IMG = product_items.$row->fincode.'.JPG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.jpeg')){
			    	$IMG = product_items.$row->fincode.'.jpeg';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
		    }else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.JPEG')){
			    
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
			
		if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.jpg')){
			    
				$IMG = product_items.$row->fincode.'.jpg';
				$image = "<img src='".$IMG."' style='width:100px;height:100px;'>";
			}else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.JPG')){
			    
				$IMG = product_items.$row->fincode.'.JPG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.jpeg')){
			    	$IMG = product_items.$row->fincode.'.jpeg';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
		    }else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.JPEG')){
			    
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
			
		if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.jpg')){
			    
				$IMG = product_items.$row->fincode.'.jpg';
				$image = "<img src='".$IMG."' style='width:100px;height:100px;'>";
			}else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.JPG')){
			    
				$IMG = product_items.$row->fincode.'.JPG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.jpeg')){
			    	$IMG = product_items.$row->fincode.'.jpeg';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
		    }else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.JPEG')){
			    
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
		
		$prnos=$this->db->query('SELECT id, purno FROM purchase_request ORDER BY  CAST(purno AS decimal) DESC LIMIT 1');
		//$prnos=$this->db->select('id,purno')->from('purchase_request')->order_by('id','DESC')->limit(1)->get();
		$num=$prnos->num_rows();
		if($num==0)
		{
		$num1=1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPR'.$num_padded;
		}else{
		foreach($prnos->result() as $prnoss);
		$numpart = $prnoss->purno;
		$num1=$numpart+1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPR'.$num_padded;

		}
	
	
		if(count($item)>0)
		{
		for($i=0;$i<count($item);$i++)
		{
		   $prnumonly=preg_replace('/[^0-9]/', '', $code);

			$itemid=$item[$i];
			$unit=$this->input->post('unit'.$itemid);
			$stock=$this->input->post('currstock'.$itemid);
			$qty=$this->input->post('qty'.$itemid);
			
			
			$data=array('masterid'=>$itemid,'itemid'=>$itemid,'source'=>'3','prno'=>$code,'qty'=>$qty,'unit'=>$unit,'stockattimeofpr'=>$stock,'addedBy'=>$_SESSION['logged_in']['user_id'],'addedOn'=>date('Y-m-d H:i:s'),'purno'=>$prnumonly);
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
		if(isset($_GET['previousvalue'])&&($_GET['previousvalue']<>''))
		{
		$prevval=urldecode($_GET['previousvalue']);
		$resd=explode(',',$prevval);
		$prevval="'" . implode ( "', '", $resd ) . "'";
		}else
		{
		$prevval='';
		}
		
		$type = $this->uri->segment(3);
		if($type=='1'){
		
		$this->db->select('id, part,specification,size_in_mm,fincode,material')->from('machine_parts_with_picture')->like('part',$searchtrm,'both',false)->where('status','1');
	
		if($prevval<>'')
		{
		$this->db->where_not_in('id',$prevval,false);
		}
		
		$query = $this->db->get();
		
		if($query->num_rows()>0)
		{
		foreach($query->result() as $machine_items){
        
        if($machine_items->specification<>'')
        {
            $secondpa=$machine_items->specification;
        }else
        {
            $secondpa=$machine_items->size_in_mm;
        }
        if($machine_items->material<>'')
        {
            $thirdpart=$machine_items->material;
        }else{
            
              $thirdpart='';
            
        }
		$json[] = array('id'=>$machine_items->id, 'text'=>$machine_items->part."-".$secondpa.' - '.$thirdpart.' ('.$machine_items->fincode.')');

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


	function getmachinesolddd()
	{
		$searchtrm= $_GET['q'];
		if(isset($_GET['previousvalue'])&&($_GET['previousvalue']<>''))
		{
		$prevval=urldecode($_GET['previousvalue']);
		$resd=explode(',',$prevval);
		$prevval="'" . implode ( "', '", $resd ) . "'";
		}else
		{
		$prevval='';
		}
		
		$type = $this->uri->segment(3);
		if($type=='1'){
		
		$this->db->select('id, part,specification,size_in_mm')->from('machine_parts_with_picture')->like('part',$searchtrm,'both',false)->where('status','1');
	
		if($prevval<>'')
		{
		$this->db->where_not_in('id',$prevval,false);
		}
		
		$query = $this->db->get();
		
		if($query->num_rows()>0)
		{
		foreach($query->result() as $machine_items){
        
        if($machine_items->specification<>'')
        {
            $secondpa=$machine_items->specification;
        }else
        {
            $secondpa=$machine_items->size_in_mm;
        }
		$json[] = array('id'=>$machine_items->id, 'text'=>$machine_items->part."-".$secondpa);

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



function generateprfromintendautoapproval($indentno)
{

	
		$prnos=$this->db->query('SELECT id, purno FROM purchase_request ORDER BY  CAST(purno AS decimal) DESC LIMIT 1');
	//$prnos=$this->db->select('id,purno')->from('purchase_request')->order_by('id','DESC')->limit(1)->get();
	$num=$prnos->num_rows();
	if($num==0)
	{
		$num1=1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPR'.$num_padded;
	}else{
	     foreach($prnos->result() as $prnoss);
	    $numpart = $prnoss->purno;
	    	$num1=$numpart+1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPR'.$num_padded;
		
	}
	

	//echo $code; exit;
		
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
			
			$prnumonly=preg_replace('/[^0-9]/', '', $code);

			$data=array('masterid'=>$resty1->itemid,'type'=>$indenttype,'itemid'=>$resty1->itemid,'source'=>'2','sourceid'=>$indentno,'prno'=>$code,'qty'=>$resty1->qty,'unit'=>$resty1->unit,'addedBy'=>$_SESSION['logged_in']['user_id'],'addedOn'=>date('Y-m-d H:i:s'),'purno'=>$prnumonly);
			
			$this->db->insert('purchase_request',$data);
			
		}
		
		/** UPDATE INTEND APPROVAL **/
		$datau=array('approvalstatus'=>'1','approvedOn'=>date('Y-m-d H:i:s'),'approvedBy'=>$_SESSION['logged_in']['user_id'],'pr_status'=>'1');
		$this->db->where('indendno',$indentno);
		$this->db->update('intend_request',$datau);
		/** END **/
	
		
		
	}else{
		
		echo "INDEND NOT AVAILABLE";exit;
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

function sendemailtosupplier()
{
    $pono=$this->input->post('pono');
	$html='';
	$restteye=$this->db->select('*')->from('purchase_order')->where('pono',$pono)->get();
	if($restteye->num_rows()>0)
	{
		foreach($restteye->result() as $restteye111);
		$vendorname=$this->storemodel->getvendornameotherdetails($restteye111->vendor);
		if(count($vendorname)>0)
		{
			$vendname=$vendorname['name'];
			$contactperson=$vendorname['contactperson'];
			$email=$vendorname['email'];
			$phone=$vendorname['phone'];
			if($contactperson=='')
			{
				$contactperson=$vendname;
			}
		}else{
			
			echo "NO SUPPLIER FOUND";exit;
			$vendname='';
			$contactperson='';
			$email='';
			$phone='';
		}
			$html.= '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
				   <tr>
					<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://www.prestogroup.com/images-new/logo-1.png" width="200px;" alt="" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" />Hello, '.$contactperson.'<br><br><strong>New PO Released from Prest Stantest Pvt. Ltd. - '.$pono.'</strong><hr></td>
					  </tr> 
					  
					    <tr>
						<td height="50" style="text-align:center;">Particulars</td>
					  </tr> 
					
					  
					  <tr>
						<table width="600" border="1" align="center" cellpadding="0" cellspacing="0" style="font-size:12px;text-align:center;">
						
						<thead>
						<tr style="text-align:center;">
						<td>Sno.</td>
						<td>Item</td>
						<td>HSN</td>
						<td>Specification</td>
						<td>Quanity</td>
						<td>Rate</td>
				
						</tr>
						</thead>';
						
						$i=1;
						$totarray=array();
						foreach($restteye->result() as $restteye1)
						{
							$getunit=$this->storemodel->getunit($restteye1->unit);
							if($restteye1->potype==0)
							{
								$idata=$this->storemodel->getmachineotherdetails($restteye1->itemid);
								$iname=$idata['name'];
								$special=$idata['specialization'];
								$hsn=$idata['hsn'];
							}
							else{
								
								$idata=$this->storemodel->getgeneralitemname($restteye1->itemid);
								$iname=$idata;
								$special='';
								$hsn='';
							}
							
							$totarray[]=floatval($restteye1->qty*$restteye1->price);
					$html.='<tr>
						<td style="width:20px">'.strtoupper($i).'</td>
						<td style="width:100px">'.strtoupper($iname).'</td>
						<td style="width:100px">'.strtoupper($hsn).'</td>
						<td style="width:100px">'.strtoupper($special).'</td>
						<td style="width:20px">'.floatval($restteye1->qty).' '.strtoupper($getunit).'</td>
						<td style="width:20px">'.floatval($restteye1->qty*$restteye1->price).'</td>
						</tr>';
						$i++;
						}
						
						if(count($totarray)>0)
						{
							$tot=array_sum($totarray);
						}else{
							
							$tot=0;
						}
								
					$html.='<tr>
						<td colspan="4"></td>
						<td>Grand Total</td>
						<td>'.$tot.'</td>
						</tr>';
						
						$html.='</table>
					  </tr>
					 
					 
					 </table>
					</td>
				  </tr>
				</table>';
		
	
		$filepath=UPLOADPATH.'poimage/'.$pono.'.jpg';
		
		$subjectname = "PURCHASE ORDER  | PRESTO STANTEST PVT LTD";
		$this->email->set_mailtype("html");
		$this->email->to('sdsrbh5@gmail.com');
		$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
		$this->email->from('mitr@prestomitr.com');
		$this->email->subject($subjectname);
		$this->email->attach($filepath);
		$this->email->message($html);
		$result11=$this->email->send();
		
		$overallpath=page_url."image_bank/poimage/".$pono.'.jpg';
		
		
			/***WHATSAPP INTEGRATION***/
			// supplier phone $phone
			$contact="918447031736";
			$data = [
			'phone' => $contact, 
			'body' => $overallpath,
			'filename'=>$pono.'jpg',
			'caption'=>"Hello Mr. ".$contactperson."\nNew Purchase Order has been released by Presto Stantest Pvt. Ltd.\n Regards\nTeam Presto"
			];  


			$json = json_encode($data); 
			//echo $json;exit;
			$url = 'https://api.chat-api.com/instance88514/sendFile?token=lwpwzff7ubbp6dc6';
			$options = stream_context_create(['http' => [
			'method'  => 'POST',
			'header'  => 'Content-type: application/json',
			'content' => $json
			]
			]);
			$result = file_get_contents($url, false, $options);

		//	echo $result;exit;

			/***WHATSAPP INTEGRATION***/
		
		
	}
	
}


function savepoimage()
{
	
	$image = $_POST['image'];
	$pono = $_POST['ponumber'];
$location = UPLOADPATH."poimage/";
$image_parts = explode(";base64,", $image);
$image_base64 = base64_decode($image_parts[1]);
$filename = $pono.'.jpg';
$file = $location . $filename;
file_put_contents($file, $image_base64);
	
	
}

public function followup_dashboard(){
	$this->load->view('store/followup-dashboard');
}

public function field_boy_scheduler_dashboard(){
	$this->load->view('store/field_boy_scheduler_dashboard');
}

public function add_field_boy_data(){
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$table = "delivery_boy_schedule";
	date_default_timezone_set('Asia/Kolkata');
	$added_time = date('Y-m-d H:i:s');
	if(isset($_REQUEST['pickeditem'])){	
					$tags1=count($_REQUEST['pickeditem']);
					if($tags1>0)
					{
					$content_attruibute=$_REQUEST['pickeditem'];
					$field_boy = $this->input->post('field_boy');
					$scheduled_date=$_REQUEST['scheduled_date'];
					for($x=0;$x<$tags1;$x++){
					if($content_attruibute[$x]!='')
						{
							$data=array(
							'scheduled_date'=>date('Y-m-d',strtotime($scheduled_date)),
							'field_boy'=>$field_boy,
							'status'=>'1',
							'updated_on'=>$added_time,
							'updated_by'=>$user_id);
							$this->db->where('id',$content_attruibute[$x]);
							$this->db->update($table,$data);
						}
					}
					}
					}
	
	$this->session->set_flashdata('message','<div class="alert alert-success">Thank You! pick-up has been scheduled.</div>');
	redirect(page_url.'Store/field_boy_scheduler_dashboard');
}
public function field_boy_dashboard(){
	$this->load->view('store/field_boy_dashboard');
}
public function remove_scheduler(){
	$poid = $this->uri->segment(3);
	$data = array('status'=>'0',
	'field_boy'=>'0',
	'scheduled_date'=>'000-00-00',
	'updated_by'=>'1',
	'updated_on'=>'0000-00-00 00:00:00');
	
	$this->db->where('pono',$poid);
	$this->db->update('delivery_boy_schedule',$data);
	$this->session->set_flashdata('message','<div class="alert alert-success">Thank You! record successfully removed.</div>');
	redirect(page_url.'Store/field_boy_dashboard');
}

public function update_scheduled_date(){
	$id = $this->input->post('recordid');
	$data = array('scheduled_date'=>date('Y-m-d',strtotime($this->input->post('nextdate'))));
	$this->db->where('id',$id);
	$this->db->update('delivery_boy_schedule',$data);
	$this->session->set_flashdata('message','<div class="alert alert-success">Thank You! record successfully updated.</div>');
	redirect(page_url.'Store/field_boy_scheduler_dashboard');
}

public function send_schedule(){
	
	$pono = $this->uri->segment(3);
	$contactno = $this->uri->segment(4);

	$date = base64_decode($this->uri->segment(5));
	$name = base64_decode($this->uri->segment(6));
	
	$q2 = $this->db->select('a.id,a.pono,b.vendor, c.name, c.address, a.scheduled_date,b.potype')->from('delivery_boy_schedule a')->join('purchase_order b','a.pono=b.pono','left')->join('vendors c','b.vendor=c.id','left')->where('a.status','1')->where('a.scheduled_date',$date)->get();
	foreach($q2->result() as $row);
	if($row->potype=='0'){
	$indenttype = "Machine Related Items";
	$rest123=$this->db->select('b.part as item_name,a.qty,a.unit,c.shortname')->from('purchase_order a')->join('machine_parts_with_picture b','a.itemid=b.id','left')->join('units c','a.unit=c.id','left')->where('a.pono',$row->pono)->get();	
	}else{
	$indenttype = "General Items";
	$rest123=$this->db->select('b.item_name,a.qty,a.unit, c.shortname')->from('purchase_order a')->join('house_keeping_items b','a.itemid=b.id','left')->join('units c','a.unit=c.id','left')->where('a.pono',$row->pono)->get();	
	}
	
	$message = $name."\n\n"."Your visit schedule for ".date('d-M-Y',strtotime($date))."\n\n";
	
	foreach($rest123->result() as $row3){
		$message.="Vendor Name: ".$row->name."\n\nAddress: ".$row->address."\n\n";
	$message.="Item - ".$row3->item_name." Qty (".floatval($row3->qty)." ".$row3->shortname.")\n\n";	
		
	}
	$message.="Best Regards\nPrestogroup";
	/**** SMS INTEGRATION***/

	$postData = array(
    'authkey' => '266631AuRXQ3UyZ5c839e9b',
    'mobiles' => $contactno,
    'message' => $message,
    'sender' => 'PRESTO',
    'route' => '4'
);


$url="http://api.msg91.com/api/sendhttp.php";
$ch = curl_init();
curl_setopt_array($ch, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postData
));



curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
 $response = curl_exec($ch);
$err = curl_error($ch);

curl_close($ch); 

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
} 
	
/**** SMS INTEGRATION***/
	
}


function requestmaster()
{
	$itemssa=$this->input->post('item');
	$qty=$this->input->post('qty');
	$unitnew=$this->input->post('unit');
	$itemtype=$this->input->post('itemtype');
	
	$itemssa=explode(',',$itemssa);
	$qty=explode(',',$qty);
	$unitnew=explode(',',$unitnew);
	
	if(count($itemssa)>0)
	{
		if((count($itemssa) == count($qty)) && (count($qty) == count($unitnew)))
		{
		for($i=0;$i<count($itemssa);$i++)
		{
			$data=array('item'=>$itemssa[$i],'qty'=>$qty[$i],'unit'=>$unitnew[$i],'requestedby'=>$_SESSION['logged_in']['user_id'],'itemtype'=>$itemtype,'addedOn'=>date('Y-m-d H:i:s'));
			$this->db->insert('item_master_request',$data);
			
		}
		
		}else{
			echo "0";
		
	}
	

}else{
	echo "0";
	
}
}


function masterrequest()
{
	$this->load->view('store/masterrequest');
	
}

function pendingmasterrequest()
{
	
	$machinepart_data=array();

$resyue=$this->db->select('*')->from('item_master_request')->where('indentcreated','0')->order_by('id','DESC')->get();
if($resyue->num_rows()>0)
{
	$i=1;
	foreach($resyue->result() as $resyue1)
	{
		$uname=$this->storemodel->getunit($resyue1->unit);
		$pername=$this->storemodel->getsusername($resyue1->requestedby);
		if($resyue1->itemtype=='1')
		{
			$ty="Machine Items";
			$mas="Store/add_parts/".$resyue1->id;
		}else{
			$ty="General Items";
			$mas="Store/add_general_items/".$resyue1->id;
		}
		
		$mast="<a href='".page_url.$mas."'><span class='btn btn-xs btn-warning'>Create Master</span></a>";
	$machinepart_data[] = array('sr_no'=>$i,
	'itemtype'=>$ty,
	'item'=>$resyue1->item,
	'qty'=>$resyue1->qty.' '.$uname,
	'by'=>$pername,
	'master'=>$mast);
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


	function generateautoindent($masterid,$itemid)
	{
	
						$resyue=$this->db->select('id,item,itemtype,qty,unit')->from('item_master_request')->where('id',$masterid)->get();
						if($resyue->num_rows()>0)
						{
						foreach($resyue->result() as $resyue1);
						if($resyue1->itemtype=='1')
						{
						$ty=1;
						}else if($resyue1->itemtype=='2')
						{
						$ty=2;
						}
						$type=$ty;
						$itemname=$itemid;
						$qty=$resyue1->qty;
						/** Check for any previous intend**/
						$inde=$this->db->select('indendno')->from('intend_request')->order_by('indendno','DESC')->limit(1)->get();
						$ninde=$inde->num_rows();
						if($ninde==0)
						{
						$no=1;
						}else{
						foreach($inde->result() as $prnoss);
						$str = $prnoss->indendno;
						$numpart = (int) filter_var($str, FILTER_SANITIZE_NUMBER_INT);
						
						$no=$numpart+1;
						}

						$indno= sprintf("%03d", $no);
						//echo $indno; exit;


						$instrumentid=$itemname;
						$quantity=$qty;

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
						$total= $itemprice->price*$quantity;
						}else{

						$Q1 = $this->db->select('price')->from('vendors_price')->where('itemid',$instrumentid)->order_by('id','desc')->limit(1)->get();
						if($Q1->num_rows()>0){
						foreach($Q1->result() as $itemprice);
						$total = $itemprice->price*$quantity;
						}else{
						$total= "0";
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
						$total= $itemprice->price*$quantity;
						}else{
						$Q1 = $this->db->select('price')->from('vendorwise_house_keeping_item_price')->where('item_id',$instrumentid)->order_by('id','desc')->limit(1)->get();
						if($Q1->num_rows()>0){
						foreach($Q1->result() as $itemprice);
						$total = $itemprice->price*$quantity;
						}else{
						$total= "0";
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
							
						//	echo "<pre>"; print_r($data);exit;
						$this->db->insert('intend_request',$data);


						$grandtotal =  $total;
						if($grandtotal>500){
						$data = array('approvalstatus'=>'0');
						$this->db->where('indendno',$indno);
						$this->db->update('intend_request',$data);
						}else{
						$data = array('approvalstatus'=>'1');
						$this->db->where('indendno',$indno);
						$this->db->update('intend_request',$data);
                        $this->generateprfromintendautoapproval($indno);
						}
						/** END **/
						
						/** ADD MASTER DONE **/
                    $data21322=array('indentcreated'=>'1','indentno'=>$indno);
                    $this->db->where('id',$masterid);
                    $this->db->update('item_master_request',$data21322);
                    /** END **/
                    


					

						}else{

						echo "INDENT CANNOT BE CREATED";exit;
						}

	
	}
	
	
	function mergerqcmrnOldddd()
{


$selectedid=$this->input->post('check');

if(count($selectedid)>0)
{

for($i=0;$i<count($selectedid);$i++)
{
/** CREATE GATE ENTRY **/

$poid=$selectedid[$i];
$iitem=$this->input->post('item'.$poid);
$po=$this->input->post('po'.$poid);
$vendor=$this->input->post('vendor'.$poid);
$gateentryno=$this->input->post('gateentry'.$poid);
$source=$this->input->post('source'.$poid);
$jobcard=$this->input->post('jobcardno'.$poid);
$mid=$this->input->post('instrumentid'.$poid);
$potype=$this->input->post('potype'.$poid);

$itemid=$iitem;
$recvqty=$this->input->post('recvqty'.$poid);
$reqqty=$this->input->post('originalqty'.$poid);

$unit=$this->input->post('unit'.$poid);

if($recvqty==$reqqty)
{
$c=1;
}else{
$c=0;
}


$data=array('itemid'=>$itemid,'pono'=>$po,'gateentryno'=>$gateentryno,'reqty'=>$reqqty,'recqty'=>$recvqty,'unit'=>$unit,'source'=>$source,'jobcardno'=>$jobcard,'gateentryOn'=>date('Y-m-d H:i:s'),'gateentryBy'=>$_SESSION['logged_in']['user_id'],'addedOn'=>date('Y-m-d H:i:s'),'mrndone'=>'1','mrndoneOn'=>date('Y-m-d H:i:s'),'mrndoneBy'=>$_SESSION['logged_in']['user_id']);
$this->db->insert('mrn',$data);
$recordid=$this->db->insert_id();

if($c==1)
{
$data1=array('gateentrycomplete'=>'1','completed'=>'1','gateentryno'=>$gateentryno);
$this->db->where('itemid',$itemid);
$this->db->where('pono',$po);
$this->db->update('purchase_order',$data1);
}
/** END **/

/** QC **/
$user_id =$this->session->userdata['logged_in']['user_id'];	
date_default_timezone_set("Asia/Kolkata");
$added_time = date('Y-m-d H:i:s');
$table = "mrn_history";


$itemid=$itemid;
$supp=$vendor;
$recordid = $recordid;
$approved_qty = $this->input->post('approved_qty'.$poid);
$reject_qty = $this->input->post('reject'.$poid);
$chtype =  $this->input->post('chtype'.$poid);
$unit = $unit;

if($potype=='0')
{
$prevstockforitem=$this->getcurrentstock($itemid);
}else
{
$prevstockforitem=$this->getcurrentstockforgeneralitem($itemid);
}


		$dataudyuu=array('itemid'=>$itemid,
		'record_id'=>$recordid,
		'po_no'=>$po,
		'accept_qty'=>$approved_qty,
		'reject_qty'=>$reject_qty,
		'added_on'=>$added_time,
		'reject_reason'=>$chtype,
		'added_by'=>$user_id);
	//echo "<pre>"; print_r($data);exit;
	$this->db->insert($table,$dataudyuu);
	$lid=$this->db->insert_id();

		if($reject_qty>0)
		{

		$datadb=array('pono'=>$po,'mrnhistoryid'=>$lid,'itemid'=>$itemid,'qty'=>$reject_qty,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
		$this->db->insert('item_rejection_request',$datadb);
		}


				/**	if($potype=='0')
					{
							if($approved_qty>0)
							{
							$currstock=$prevstockforitem;
							$newstock=$currstock+$approved_qty;
							$datadb1=array('current_stock'=>$newstock);
							$this->db->where('id',$itemid);
							$this->db->update('machine_parts_with_picture',$datadb1);
							}
					
					}else
					{

					
							if($approved_qty>0)
							{

							$currstock=$prevstockforitem;
							$newstock=$currstock+$approved_qty;
							$datadb1=array('qty'=>$newstock);
							$this->db->where('id',$itemid);
							$this->db->update('house_keeping_items',$datadb1);

							}

					}**/

						/** MARK AS QC DONE **/
						$mrnqcdata=array('qcstatus'=>'1','qcdoneOn'=>date('Y-m-d H:i:s'),'qcdoneBy'=>$_SESSION['logged_in']['user_id']);
						$this->db->where('pono',$po);
						$this->db->where('itemid',$itemid);
						$this->db->update('mrn',$mrnqcdata);

						/** END **/
						

				/** END **/
/** END **/
}


$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">QC HAS BEEN DONE</span><br/>');
redirect(page_url.'Reporting/gateentry');

}else
{

$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Please Select Atleast an Item for QC.</span><br/>');
redirect(page_url.'Reporting/gateentry');

}



}


public function po_delayed_report(){
	$this->load->view('store/po_delayed_report');
}

public function canwait(){
    $id  =$this->input->post('delayid');
    $data = array('delayed_status'=>'1','hodaction'=>'1','hoddelayremarks'=>$this->input->post('canwaitremarks'));
    $this->db->where('id',$id);
    $this->db->where('followup_status','3');
    $this->db->update('vendor_followup',$data);
    $this->session->set_flashdata('message','<span class="alert alert-info">Thank You! status successfully updated.</span><br/>');
redirect(page_url.'Store/po_delayed_report');
    
}

public function checktoapprovepo(){
    
	date_default_timezone_set("Asia/Kolkata");
	$user_id =$this->session->userdata['logged_in']['user_id'];	
	$id = $this->input->post('id');
	$pono = $this->input->post('pono');
	$itemid = $this->input->post('itemid');
	$type = $this->input->post('type');
	if($type=='1')
{
	$data = array('approved'=>'1',
	'approvedOn'=>date('Y-m-d H:i:s'),
	'approvedBy'=>$user_id);
}else
{
    $data = array('approved'=>'0',
    'approvedOn'=>'',
    'approvedBy'=>'');
}
	$this->db->where('id',$id);
	$res = $this->db->update('purchase_order',$data);
	if($res){
		$data1= array('poid'=>$id,'po_no'=>$pono,
	    'itemid'=>$itemid,
		'followupdate'=>date('Y-m-d'));
		$this->db->insert('vendor_followup',$data1);
		echo "PO marked as approved.";
	}
}

public function amendment_in_po(){
    
    	date_default_timezone_set("Asia/Kolkata");
	$user_id =$this->session->userdata['logged_in']['user_id'];	
    $id = $this->uri->segment(3);
    $pono = $this->uri->segment(4);
    $fid=$this->input->post('followupid');
   
    
    $previousqty = $this->input->post('previousqty');
    $qty = $this->input->post('qty');
    if(empty($qty)){
        $updatedqty = $previousqty;
        $amendstatus = "0";
    }else{
        $updatedqty=$qty;
        $amendstatus = "1";
        
        $data = array('pono'=>$pono,
        'item_id'=>$id,
        'qty'=>$previousqty,
        'added_on'=>date('Y-m-d H:i:s'),
        'added_by'=>$user_id);
        $this->db->insert('po_amendend_history',$data);
        
    }
    
    $data1 = array('qty'=>$updatedqty,
    'amended_status'=>$amendstatus);
    
    $this->db->where('itemid',$id);
    $this->db->where('pono',$pono);
    $this->db->update('purchase_order',$data1);
    
    $data2=array('hodaction'=>'1','hoddelayremarks'=>'PO AMMENDED');
    $this->db->where('id',$fid);
    $this->db->update('vendor_followup',$data2);
    
    $this->session->set_flashdata('message','<span class="alert alert-info">Thank You! PO qty successfully changed.</span><br/>');
redirect(page_url.'Store/po_delayed_report');
}


function storereciept()
{
	$this->load->view('store/storereciept');

}

function markstoreaccepted()
{
	$mrnid=$this->input->post('mrnid');
	
	if(count($mrnid)>0)
	{
		for($i=0;$i<count($mrnid);$i++)
		{
			$mrnid=$mrnid[$i];
			$qty=$this->input->post('qty'.$mrnid);
			$potype=$this->input->post('potype'.$mrnid);
			$itemid=$this->input->post('itemid'.$mrnid);
			
			if($potype=='0')
			{
			$prevstockforitem=$this->getcurrentstock($itemid);
			}else
			{
			$prevstockforitem=$this->getcurrentstockforgeneralitem($itemid);
			}

			if($potype==0)
			{
				$currstock=$prevstockforitem;
				$newstock=$currstock+$qty;
				$datadb1=array('current_stock'=>$newstock);
				$this->db->trans_begin();
				
				$this->db->where('id',$itemid);
				$this->db->update('machine_parts_with_picture',$datadb1);
				
                if ($this->db->trans_status() === FALSE)
                {
                $this->db->trans_rollback();
                }
                else
                {
                $this->db->trans_commit();
                }
	
				
			
			}else{
				
				$currstock=$prevstockforitem;
				$newstock=$currstock+$qty;
				$datadb1=array('qty'=>$newstock);
				$this->db->trans_begin();
				$this->db->where('id',$itemid);
				$this->db->update('house_keeping_items',$datadb1);
                if ($this->db->trans_status() === FALSE)
                {
                $this->db->trans_rollback();
                }
                else
                {
                $this->db->trans_commit();
                }
	
				
				}
			
			
			$data12323=array('storereciept'=>'1','storerecvon'=>date('Y-m-d H:i:s'),'storerecvby'=>$_SESSION['logged_in']['user_id']);
				$this->db->trans_begin();
				$this->db->where('id',$mrnid);
				$this->db->update('mrn_history',$data12323);
				if($this->db->trans_status() === FALSE)
                {
                $this->db->trans_rollback();
                }
                else
                {
                $this->db->trans_commit();
                }
				
			
			
		}
		
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Items marked as recieved.</span><br/>');
				redirect(page_url.'Store/storereciept');

				}else
				{
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Unable to mark items as recieved.</span><br/>');
				redirect(page_url.'Store/storereciept');
				}
	
}

function markstoreacceptedOLDD()
{
	$mrnid=$this->input->post('mrnid');
	$array=array();
	$array1=array();
	$array2=array();
	$array3=array();
	if(count($mrnid)>0)
	{
		for($i=0;$i<count($mrnid);$i++)
		{
		
		$mrnid1=$mrnid[$i];
		
			$qty=$this->input->post('qty'.$mrnid1);
			$potype=$this->input->post('potype'.$mrnid1);
			$itemid=$this->input->post('itemid'.$mrnid1);
			
			if($potype=='0')
			{
			$prevstockforitem=$this->getcurrentstock($itemid);
			}else
			{
			$prevstockforitem=$this->getcurrentstockforgeneralitem($itemid);
			}

			if($potype==0)
			{
				$currstock=$prevstockforitem;
				$newstock=$currstock+$qty;
				$datadb1=array('current_stock'=>$newstock);
				
				$this->db->where('id',$itemid);
				$this->db->update('machine_parts_with_picture',$datadb1);
				
				
			
			}else{
				
				$currstock=$prevstockforitem;
				$newstock=$currstock+$qty;
				$datadb1=array('qty'=>$newstock);
				$this->db->where('id',$itemid);
				$this->db->update('house_keeping_items',$datadb1);
				
				}
			
			
			$data12323=array('storereciept'=>'1','storerecvon'=>date('Y-m-d H:i:s'),'storerecvby'=>$_SESSION['logged_in']['user_id']);
				$this->db->where('id',$mrnid1);
				$this->db->update('mrn_history',$data12323);
				
			
			
		}
		
		
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Items marked as recieved.</span><br/>');
				redirect(page_url.'Store/storereciept');

				}else
				{
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Unable to mark items as recieved.</span><br/>');
				redirect(page_url.'Store/storereciept');
				}
	
}

function readyvsmrnqc()
{
	$this->load->view('store/readyvsmrnqc');

}

function getjobcardno($jobcard)
{
    $restey=$this->db->select('job_card_no')->from('order_instruments')->where('id',$jobcard)->get();
    if($restey->num_rows()>0)
    {
        foreach($restey->result() as $restey12);
        
        return $restey12->job_card_no;
        
    }else
    {
        return '';
    }
    
    
}



function paymentdone()
{
	if($this->input->post('pono')<>'')
	{
	
	$data=array('utrno'=>$this->input->post('utr'),'pono'=>$this->input->post('pono'));
	$this->db->insert('paymentdetails',$data);
	
	$datasass=array('payment'=>'1');
	$this->db->where('pono',$this->input->post('pono'));
	$this->db->update('purchase_order',$datasass);
	$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Payment Done</span><br/>');
	redirect(page_url.'Reporting/pendingpoforpayment');
	}else{
		
		$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Unable to add payment</span><br/>');
	redirect(page_url.'Reporting/pendingpoforpayment');
	}
	
	
}

public function anytime_rejection(){
    
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('yourname', 'yourname', 'required|trim');
	$this->form_validation->set_rules('rejection_type', 'rejection_type', 'required|trim');
	//	$this->form_validation->set_rules('item_name', 'item_name', 'required|trim');
		//$this->form_validation->set_rules('reason', 'reason', 'required|trim');

		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		    
			$this->load->view('store/anytime_rejection');
			}else
		{
		 
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
           
           if($this->input->post('rejection_type')!='JOBCARD')
           {
           $photo1=$_FILES['photo']['name'];
			if($photo1<>'')
			{
				$image2=explode('.',$photo1);
				$cat_image1=end($image2);
				$picture=time().rand(1,9999).'.'.$cat_image1;
					$path1=UPLOADPATH.'anytime_rejection/'.$picture;
				move_uploaded_file($_FILES['photo']['tmp_name'],$path1);
			}else
			{
				$picture="";
				}
		 
		 $data1=
			array(
			'yourname'=>$user_id,
			'rejection_type'=>strtoupper($this->input->post('rejection_type')),
			'item_name'=>strtoupper($this->input->post('item_name')),
			'reason'=>strtoupper($this->input->post('reason')),
			'qty'=>strtoupper($this->input->post('qty')),
			'images'=>$picture,
			'added_on'=>$added_time);
			$res = $this->db->insert('anytime_rejection',$data1);
			
           }else
           {
               /* JOBCARD CASE **/
               $checkitem=$this->input->post('checkit');
              if(count($checkitem)>0)
              {
                for($hu=0;$hu<count($checkitem);$hu++)
              {
                  $blockedid=$checkitem[$hu];
                  $items=$this->input->post('items'.$blockedid);
                  $rejqty=$this->input->post('rejqty'.$blockedid);
                  $reason=$this->input->post('reason'.$blockedid);
                  
                $photo1=$_FILES['rejfile'.$blockedid]['name'];
                if($photo1<>'')
                {
                $image2=explode('.',$photo1);
                $cat_image1=end($image2);
                $picture=time().rand(1,9999).'.'.$cat_image1;
                $path1=UPLOADPATH.'anytime_rejection/'.$picture;
                move_uploaded_file($_FILES['rejfile'.$blockedid]['tmp_name'],$path1);
                }else
                {
                $picture="";
                }

				
                $data1123=
                array(
                'yourname'=>$user_id,
                'rejection_type'=>strtoupper($this->input->post('rejection_type')),
                'item_name'=>$items,
                'reason'=>$reason,
                'qty'=>$rejqty,
                'images'=>$picture,
                'added_on'=>$added_time);
              
                $res = $this->db->insert('anytime_rejection',$data1123);
                
                /** DECREASE THE AMOUNT ISSUED TO REJECTED **/
                $alreadyissuedqty=$this->getblockedstockdetail($blockedid);
                if($alreadyissuedqty<>0)
                {
                   if($alreadyissuedqty>$rejqty)
                   {
                            $balanceqty=$alreadyissuedqty-$rejqty;
                            
                            $data=array('stock'=>$balanceqty);
                            $this->db->where('id',$blockedid);
                            $this->db->update('issuestocktousers');
                       
                   }else if($alreadyissuedqty==$rejqty)
                   {
                       $this->db->where('id',$blockedid);
                       $this->db->delete();
                       
                   }else
                   {
                       /** IF NOT ISSUED IN THIS **/
                       
                        $balanceqty=$rejqty-$alreadyissuedqty;
                        $this->db->where('id',$blockedid);
                        $this->db->delete();
                        /** PENDING FROM HERE **/
                       // $this->db->select('stock')->from('issuestocktousers')->where('')
                       
                    //   $this
                       
                       /** END **/
                       
                       
                   }
                    
                    
                }
                /** END **/
                 
                 
              } 
                  
              }
               /** END **/
               
           }
			$this->session->set_flashdata('message','<span class="alert alert-success">Record successfully added.</span>');
			redirect(page_url.'Store/anytime_rejection');
			}
	}
	
	public function anytime_rejection_dashboard(){
	    $this->load->view('store/anytime_rejection_dashboard');
	}
	
	public function anytimerejectionstoreupdate(){
		date_default_timezone_set("Asia/Kolkata");
        $added_time = date('Y-m-d H:i:s');
	    $data = array('store_remarks'=>$this->input->post('remarks'),'store_status'=>'1',
		'store_marking_time'=>$added_time);
	    $this->db->where('id',$this->uri->segment(3));
	    $this->db->update('anytime_rejection',$data);
	    	$this->session->set_flashdata('message','<span class="alert alert-info">Thank you! Record successfully updated.</span>');
			redirect(page_url.'Store/anytime_rejection_dashboard');
	}
	
		public function anytime_rejection_purchase_dashboard(){
	    $this->load->view('store/anytime_rejection_purchase_dashboard');
	}
	
		public function anytimerejectionpurchaseupdate(){
			date_default_timezone_set("Asia/Kolkata");
        $added_time = date('Y-m-d H:i:s');
	    $data = array(
	        'vendor_id'=>$this->input->post('vendor_name'),
	        'purchase_remarks'=>$this->input->post('remarks'),
	        'purchase_status'=>'1',
			'purchase_marking_time'=>$added_time);
	    $this->db->where('id',$this->uri->segment(3));
	    $this->db->update('anytime_rejection',$data);
	    	$this->session->set_flashdata('message','<span class="alert alert-info">Thank you! Record successfully updated.</span>');
			redirect(page_url.'Store/anytime_rejection_purchase_dashboard');
	}
	
		public function anytime_rejection_rgp_dashboard(){
	    $this->load->view('store/anytime_rejection_rgp_dashboard');
	}
	
		public function anytimerejectionrgpupdate(){
		date_default_timezone_set("Asia/Kolkata");
		$added_time = date('Y-m-d H:i:s');
	    $data = array(
	        'challan_number'=>$this->input->post('challan_number'),
	        'rgp_remarks'=>$this->input->post('remarks'),
	        'rgp_status'=>'1',
			'rgp_timing'=>$added_time);
	    $this->db->where('id',$this->uri->segment(3));
	    $this->db->update('anytime_rejection',$data);
	    	$this->session->set_flashdata('message','<span class="alert alert-info">Thank you! Record successfully updated.</span>');
			redirect(page_url.'Store/anytime_rejection_rgp_dashboard');
	}
		public function anytime_rejection_gateentry_dashboard(){
	    $this->load->view('store/anytime_rejection_gateentry_dashboard');
	}
	
		public function anytimerejectiongateentryupdate(){
			date_default_timezone_set("Asia/Kolkata");
		$added_time = date('Y-m-d H:i:s');
	    $data = array(
	        'received_qty'=>$this->input->post('qty'),
	        'gate_entry_remarks'=>$this->input->post('remarks'),
	        'gate_entry_status'=>'1',
			'gate_entry_timing'=>$added_time);
	    $this->db->where('id',$this->uri->segment(3));
	    $this->db->update('anytime_rejection',$data);
	    	$this->session->set_flashdata('message','<span class="alert alert-info">Thank you! Record successfully updated.</span>');
			redirect(page_url.'Store/anytime_rejection_gateentry_dashboard');
	}
	
	function checkreceivedqty(){
	    $id = $this->input->post('id');
	    $qty = $this->input->post('qty');
	    $q = $this->db->select('qty')->from('anytime_rejection')->where('qty',$qty)->where('id',$id)->get();
	    if($q->num_rows()>0){
	        echo "data matched";
	    }else{
	        echo "1";
	    }
	}
	public function anytime_rejection_qc_dashboard(){
	    $this->load->view('store/anytime_rejection_qc_dashboard');
	}
	
		public function anytimerejectionqcupdate(){
			date_default_timezone_set("Asia/Kolkata");
		$added_time = date('Y-m-d H:i:s');
	    $data = array(
	        'qc_status'=>$this->input->post('status'),
	        'qc_remarks'=>$this->input->post('remarks'),
	        'gate_entry_timing'=>$added_time);
	    $this->db->where('id',$this->uri->segment(3));
	    $this->db->update('anytime_rejection',$data);
	    	$this->session->set_flashdata('message','<span class="alert alert-info">Thank you! Record successfully updated.</span>');
			redirect(page_url.'Store/anytime_rejection_qc_dashboard');
	}
	
	function getitemdiscount($suppid,$masterid,$flag)
	{
	    $disc=0;
	    $suplierid=$suppid;
	    $masterid=$masterid;
	    $flag=$flag;
	    
        if($flag==0)
        {
            /** MACHINE RELATED **/
            $Restyru=$this->db->select('discount')->from('vendors_price')->where('masterid',$masterid)->where('vendorid',$suplierid)->get();
            if($Restyru->num_rows()>0)
            {
            foreach($Restyru->result() as $Restyru1);
            $disc= $Restyru1->discount;
            return $disc;
            }
        
        }else if($flag==1)
        {
            /** HOUSE KEEPING **/
            $Restyru=$this->db->select('discount')->from('vendorwise_house_keeping_item_price')->where('item_id',$masterid)->where('vendor_id',$suplierid)->get();
            if($Restyru->num_rows()>0)
            {
            foreach($Restyru->result() as $Restyru1);
            $disc= $Restyru1->discount;
            return $disc;
            }
        
        }
	    
	    
	}
	
	
	function getprtype($prno)
	{
	    $Redyeyre=$this->db->select('type')->from('purchase_request')->where('prno',$prno)->get();
	    if($Redyeyre->num_rows()>0)
	    {
	        foreach($Redyeyre->result() as $Redyeyre1);
	        
	        return $Redyeyre1->type;
	        
	    }else
	    {
	        echo "PR TYPE NOT AVAILABLE PO CANNOT BE GENERATED";exit;
	    }
	    
	}
	
	
	function setpoinstructions()
{
	
	$this->load->view('store/poinstructions');
	
}

function poinstructionscripts()
{
		$scheduler_data=array();
		
		$restyui12=$this->db->select('*')->from('poinstructions')->get();
		if($restyui12->num_rows()>0)
		{
			$i=1;
			foreach($restyui12->result() as $restyui121)
			{
				
				$edit="<a href='".page_url."Store/editscript/".$restyui121->id."'><span class='btn btn-xs btn-warning'>EDIT</span></a>";
		$scheduler_data[] = array('sr_no'=>$i,
			'script'=>$restyui121->script,
			'action'=>$edit);
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

function editscript()
{
	$this->load->view('store/edit_script');
}


function updatesamplescript()
{
	
	$data=array('script'=>$this->input->post('script'));
	
	$this->db->where('id',$this->uri->segment('3'));
	$this->db->update('poinstructions',$data);
	
$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Record Updated</span><br/>');
	redirect(page_url.'Store/setpoinstructions');
	
}



    function getitemdiscountforajax()
    {
    $disc=0;
    $suplierid=$this->input->post('suppid');
    $masterid=$this->input->post('masterid');;
    $flag=$this->input->post('flag');
    
    if($flag==0)
    {
    /** MACHINE RELATED **/
    $Restyru=$this->db->select('discount')->from('vendors_price')->where('masterid',$masterid)->where('vendorid',$suplierid)->get();
    if($Restyru->num_rows()>0)
    {
    foreach($Restyru->result() as $Restyru1);
    $disc= $Restyru1->discount;
    
    }
    }else if($flag==1)
    {
    /** HOUSE KEEPING **/
    $Restyru=$this->db->select('discount')->from('vendorwise_house_keeping_item_price')->where('item_id',$masterid)->where('vendor_id',$suplierid)->get();
    if($Restyru->num_rows()>0)
    {
    foreach($Restyru->result() as $Restyru1);
    $disc= $Restyru1->discount;
    
    }
    
    }
    echo floatval($disc); exit;
    }
	
	
	function consolidatedpoinvoice()
	{
	   $this->load->view('store/consolidatedpoinvoice'); 
	}
	
	
	function issuemachineitems()
	{
		$this->load->view('store/issuemachineitemtouser');
	}



	function issueuserwisemachineitem()
{
	
	$itemid=$this->uri->segment(3);
	
	if($this->input->post('usertype')=='1')
	{
		$crm=$this->input->post('crmusers');
		$type=1;
	}else{
		
		$crm=$this->input->post('noncrm');
		$type=2;
	}
	
	$data=array('itemtype'=>'1','jobcardid'=>'0','issuetype'=>'2','itemid'=>$itemid,'stock'=>$this->input->post('iqty'),'issuedto'=>$crm,'usertype'=>$type,'issuedBy'=>$_SESSION['logged_in']['user_id'],'remarks'=>$this->input->post('remarks'),'issuedOn'=>date('Y-m-d H:i:s'),'issuedBy'=>$_SESSION['logged_in']['user_id']);
	//$data=array('itemid'=>$itemid,'qty'=>$this->input->post('iqty'),'crmuser'=>$crm,'stockthattime'=>$this->input->post('currentstock'),'noncrmuser'=>$noncrm,'remarks'=>$this->input->post('remarks'),'issuedOn'=>date('Y-m-d H:i:s'),'issuedBy'=>$_SESSION['logged_in']['user_id']);
	$this->db->insert('issuestocktousers',$data);
		if($this->db->affected_rows()>0)
		{
		$qt=$this->input->post('iqty');


		$this->db->set('current_stock', 'current_stock-'.$qt, false);
		$this->db->where('id' , $itemid);
		$this->db->update('machine_parts_with_picture');
		if($this->db->affected_rows()>0)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success">Item Issued</div>');
			redirect(page_url.'Store/issuemachineitems/'.$itemid);
			
		}else{
			
			$this->session->set_flashdata('message','<div class="alert alert-danger">Item Issued but unable to update stock</div>');
			redirect(page_url.'Store/issuemachineitems/'.$itemid);
		}
			
		}else{
			
			echo "ERROR OCCURED";EXIT;
			
		}
	
	
	
	
	
}

function unacknowledgeditems()
{
	$this->load->view('store/storeissueacknowledge');
	
}

function acceptissueditems()
{
	$id=$this->input->post('id');
	
	$data=array('storeaccept'=>'1','acceptedOn'=>date('Y-m-d H:i:s'),'acceptedBy'=>$_SESSION['logged_in']['user_id']);
	$this->db->where('id',$id);
	$this->db->update('issuestocktousers',$data);
	
	echo true;
	
}
function issueslip()
{
	$this->load->view('store/issueslip');
	
	
}




function acceptissueditems1()
{
	$id=$this->input->post('id');
	$data=array('storeaccept'=>'1','acceptedOn'=>date('Y-m-d H:i:s'),'acceptedBy'=>$_SESSION['logged_in']['user_id']);
	$this->db->where('id',$id);
	$this->db->update('issuegeneralstock',$data);
	
	echo true;
	
}

function unacknowledgedissueditems()
{
	$this->load->view('store/unacknowledgedissueditems');
}

function generalissueslip()
{
	$this->load->view('store/generalissueslip');
	
	
}

function getissuedjobcarditems()
{
    $html='';
    $jbcardid=$this->input->post('jobcardid');
    
    $rest=$this->db->select('a.*,b.job_card_no,c.instruments_name,d.part,d.fincode,d.specification,d.current_stock,d.category_id,d.size_in_mm,d.unit,f.category,a.issued')->from('blockedstock a')->join('order_instruments b','a.jobcardid=b.id','left')->join('presto_instruments c','a.machineid=c.id','left')->join('machine_parts_with_picture d','a.itemid=d.id','left')->join('presto_machine_part_category f','d.category_id=f.id','left')->where('a.jobcardid',$jbcardid)->where('a.active','0')->where('a.issued','1')->group_by('d.category_id')->get();
    
     $html.="<table border='1' style='width:100%;'>
                <tr>
                <th style='padding:2px 2px 2px 2px; text-align:center'>#</th>
                <th style='padding:2px 2px 2px 2px; text-align:center'>Sr No.</th>
                <th style='padding:2px 2px 2px 2px; text-align:center'>Picture.</th>
                <th style='padding:2px 2px 2px 2px; text-align:center'>Item Name</th>
                <th style='padding:2px 2px 2px 2px; text-align:center'>Fincode</th>
                <th style='padding:2px 2px 2px 2px; text-align:center'>Specification</th>
                <th style='padding:2px 2px 2px 2px; text-align:center'>Issued QTY</th>
           
                <th style='padding:2px 2px 2px 2px; text-align:center'>Rejected Qty</th>
                 <th style='padding:2px 2px 2px 2px; text-align:center'>Reason for Rejection</th>
                 <th style='padding:2px 2px 2px 2px; text-align:center'>Rejection Image</th>
                </tr>";
                
		    if($rest->num_rows()>0)
			{
                $i=1;  
                foreach($rest->result() as $data)
                {
                    /** GET ISSUED QTY **/
                    $getissueddata=$this->getissueddetails($data->id,$data->itemid);
                    if(count($getissueddata)>0)
                    {
                        $issuedqty=$getissueddata['totalissued'];
                    }else{  $issuedqty=0; }
                    /** END **/
                    
                    $restyu=$this->db->select('shortname')->from('units')->where('id',$data->unit)->get();
                    if($restyu->num_rows()>0)
                    {
                    foreach($restyu->result() as $restyu1);
                    $unival=$restyu1->shortname;	
                    }else{
                    
                    $unival='';							
                    }
                    $html.="<tr>
                    <td style='text-align:center;'><input type='checkbox' name='checkit[]' value='".$data->id."' onchange='openqtybox(".$data->id.");' id='checked".$data->id."'><input type='hidden' name='items".$data->id."' value='".$data->itemid."'></td>
                    <td style='text-align:center;'>".$i."</td>
                    <td style='text-align:center;'></td>
                    
                    <td style='text-align:center;'>".strtoupper($data->part)."</td>
                    <td style='text-align:center;'>".$data->fincode."</td>
                    <td style='text-align:center;'>".$data->specification."</td>
                    <td style='text-align:center;'>".floatval($issuedqty)." ".$unival."<input type='hidden' id='issueqty".$data->id."' value='".floatval($issuedqty)."'></td>
                    <td style='text-align:center;'><input type='text' name='rejqty".$data->id."' id='rejqty".$data->id."' class='form-control decall qtyy".$data->id."' style='width:200px;margin:auto;display:none;' placeholder='Rejected Qty' onblur='checkforqty(".$data->id.")'></td>
                    
                    <td style='text-align:center;'><textarea name='reason".$data->id."'  id='reason".$data->id."' class='form-control qtyy".$data->id."' placeholder='Remarks' style='display:none;'></textarea></td>
                     <td style='text-align:center;'><input type='file' name='rejfile".$data->id."' id='rejfile".$data->id."' class='form-control qtyy".$data->id."' style='margin:auto;display:none;'></td>
                    
                    
                    
                    </tr>";
                
               $i++;
               }
				
				
			}else
			{
			    $html.="<tr>
                <td colspan='10' style='text-align:center;font-weight:bold;'>NO Item Issued against this jobcard</td></tr>";
			}
			
			echo $html;
    
}

                function getissueddetails($blockedid,$itemid)
                {
                    $issue=array();
                    $Restye=$this->db->select('sum(stock) as totissued')->from('issuestocktousers')->where('itemid',$itemid)->where('blockedid',$blockedid)->get();
                    if($Restye->num_rows()>0)
                    {
                        foreach($Restye->result() as $Restye1);
                        
                        $issue['totalissued']=$Restye1->totissued;
                        
                    }
                    
                    return $issue;
                    
                }


function getblockedstockdetail($issueid)
{
    $iss=0;
    $restye=$this->db->select('stock')->form('issuestocktousers')->where('id',$issueid)->get();
    if($restye->num_rows()>0)
    {
    foreach($restye->result() as $restye1);
    
    $iss=$restye1->stock;
    
    }
    
    return $iss;
}

function mrngateentrydone()
{
    $poid=$this->input->post('poid');
    $pono=$this->input->post('pono');
    $reqqty=$this->input->post('pendqty');
    $recqty=$this->input->post('recqty');
    $gateentry=$this->input->post('gateentry');
    $billno=$this->input->post('billno');
    
    $podetail=$this->getpodetails($poid);

    if(count($podetail)>0)
    {
    $data=array('itemid'=>$podetail['itemid'],'pono'=>$pono,'gateentryno'=>$gateentry,'reqty'=>$reqqty,'recqty'=>$recqty,'unit'=>$podetail['unit'],'source'=>$podetail['source'],'jobcardno'=>$podetail['jobcard'],'gateentryOn'=>date('Y-m-d H:i:s'),'gateentryBy'=>$_SESSION['logged_in']['user_id'],'addedOn'=>date('Y-m-d H:i:s'),'mrndone'=>'1','mrndoneOn'=>date('Y-m-d H:i:s'),'mrndoneBy'=>$_SESSION['logged_in']['user_id'],'billno'=>$billno,'poid'=>$poid);
        $this->db->insert('mrn',$data);
        
        if($recqty==$reqqty)
        {
        $c=1;
        }else{
        $c=0;
        }
    
if($c==1)
{
$data1=array('completed'=>'1');
$this->db->where('id',$poid);
$this->db->where('itemid',$podetail['itemid']);
$this->db->where('pono',$pono);
$this->db->update('purchase_order',$data1);
}
   
   
$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">MRN & GATE ENTRY UPDATED.</div>');
redirect(page_url.'Reporting/itemmrn');
        
    }else
    {
        echo "PO DETAILS NOT FOUND"; exit;
    }
   


}


function getpodetails($poid)
{
    $podetail=array();
    $reste=$this->db->select('itemid,unit,source,jobcard')->from('purchase_order')->where('id',$poid)->get();   
    if($reste->num_rows()>0)
    {
        foreach($reste->result() as $reste1);
        $podetail['itemid']=$reste1->itemid;
        $podetail['unit']=$reste1->unit;
        $podetail['source']=$reste1->source;
        $podetail['jobcard']=$reste1->jobcard;
        return $podetail;
    }else
    {
        echo "INVALID PO"; exit;
    }
    
}


	function mergerqcmrn()
{
	

$selectedid=$this->input->post('check');

if(count($selectedid)>0)
{

for($i=0;$i<count($selectedid);$i++)
{
/** CREATE GATE ENTRY **/

$poid=$selectedid[$i]; //this is mrn id
$iitem=$this->input->post('item'.$poid);
$po=$this->input->post('po'.$poid);
$vendor=$this->input->post('vendor'.$poid);
$gateentryno=$this->input->post('gateentry'.$poid);
$source=$this->input->post('source'.$poid);
$jobcard=$this->input->post('jobcardno'.$poid);
$mid=$this->input->post('instrumentid'.$poid);
$potype=$this->input->post('potype'.$poid);

$itemid=$iitem;
$recvqty=$this->input->post('recvqty'.$poid);
$reqqty=$this->input->post('originalqty'.$poid);
$unit=$this->input->post('unit'.$poid);
$recordid=$poid;
/** END **/

/** QC **/
$user_id =$this->session->userdata['logged_in']['user_id'];	
date_default_timezone_set("Asia/Kolkata");
$added_time = date('Y-m-d H:i:s');
$table = "mrn_history";


$itemid=$itemid;
$supp=$vendor;
$recordid = $recordid;
$approved_qty = $this->input->post('approved_qty'.$poid);
$reject_qty = $this->input->post('reject'.$poid);
$chtype =  $this->input->post('chtype'.$poid);
$unit = $unit;

if($potype=='0')
{
$prevstockforitem=$this->getcurrentstock($itemid);
}else
{
$prevstockforitem=$this->getcurrentstockforgeneralitem($itemid);
}


		$dataudyuu=array('itemid'=>$itemid,
		'record_id'=>$recordid,
		'po_no'=>$po,
		'accept_qty'=>$approved_qty,
		'reject_qty'=>$reject_qty,
		'added_on'=>$added_time,
		'reject_reason'=>$chtype,
		'added_by'=>$user_id);
	$this->db->insert($table,$dataudyuu);
	$lid=$this->db->insert_id();

		if($reject_qty>0)
		{

		$datadb=array('pono'=>$po,'mrnhistoryid'=>$lid,'itemid'=>$itemid,'qty'=>$reject_qty,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
		$this->db->insert('item_rejection_request',$datadb);
		}


				/**	if($potype=='0')
					{
							if($approved_qty>0)
							{
							$currstock=$prevstockforitem;
							$newstock=$currstock+$approved_qty;
							$datadb1=array('current_stock'=>$newstock);
							$this->db->where('id',$itemid);
							$this->db->update('machine_parts_with_picture',$datadb1);
							}
					
					}else
					{

					
							if($approved_qty>0)
							{

							$currstock=$prevstockforitem;
							$newstock=$currstock+$approved_qty;
							$datadb1=array('qty'=>$newstock);
							$this->db->where('id',$itemid);
							$this->db->update('house_keeping_items',$datadb1);

							}

					}**/

						/** MARK AS QC DONE **/
						$mrnqcdata=array('qcstatus'=>'1','qcdoneOn'=>date('Y-m-d H:i:s'),'qcdoneBy'=>$_SESSION['logged_in']['user_id']);
						$this->db->where('id',$poid);
						$this->db->update('mrn',$mrnqcdata);

						/** END **/
						

				/** END **/
/** END **/
}


$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">QC HAS BEEN DONE</span><br/>');
redirect(page_url.'Reporting/gateentry');

}else
{

$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Please Select Atleast an Item for QC.</span><br/>');
redirect(page_url.'Reporting/gateentry');

}
}

function blockedims()
{
   $this->load->view('store/blockedims'); 
}


	public function machine_part_data_with_picture_listforblocked()
	{
		$i=1;
		$machinepart_data= array();
		$this->db->select('a.id,a.part,a.current_stock,a.min_stock, a.specification, a.makes, a.size_in_mm, a.fincode, a.qty, a.picture, d.rack_location, p.category')->from('machine_parts_with_picture  a')->join('store_rack_location d','a.location_id=d.id','left')->join('presto_machine_part_category p','a.category_id=p.id','left');
	
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
	
			
		if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.jpg')){
			    
				$IMG = product_items.$row->fincode.'.jpg';
				$image = "<img src='".$IMG."' style='width:100px;height:100px;'>";
			}else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.JPG')){
			    
				$IMG = product_items.$row->fincode.'.JPG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.jpeg')){
			    	$IMG = product_items.$row->fincode.'.jpeg';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
		    }else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.JPEG')){
			    
				$IMG = product_items.$row->fincode.'.JPEG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else{
				$image="";
			}
			
		$blockeddata=$this->getactiveblockeddetails($row->id);
            $machinepart_data[] = array('sr_no'=>$i,
            'category'=>$row->category,
            'machine_part'=>$row->part,
            'specification'=>$row->specification,
            'fincode'=>$row->fincode,
            'stock'=>$row->current_stock,
            'minstock'=>$row->min_stock,
            'location'=>$row->rack_location,
            'image'=>$image,
            'blockeddata'=>$blockeddata);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($machinepart_data),
			"iTotalDisplayRecords" => count($machinepart_data),
			"aaData"=>$machinepart_data);
			
		echo json_encode($results);
	}


        function getactiveblockeddetails($itemid)
        {
        
        $html='';
            $resteyue=$this->db->select('a.prno,a.pono,b.job_card_no,c.internal_order_no,a.stock')->from('blockedstock a')->join('order_instruments b','a.jobcardid=b.id')->join('prestogroup_orders c','b.order_id=c.order_id')->where('a.itemid',$itemid)->where('a.active','1')->where('a.issued','0')->get();
            
            if($resteyue->num_rows()>0)
            {
                    $html.= "<table border='1' style='width:500px;'><tr><th style='padding:2px 2px 2px 2px; text-align:center'>IO NO. </th><th style='padding:2px 2px 2px 2px; text-align:center'>JOBCARD NO.</th><th style='padding:2px 2px 2px 2px; text-align:center'>QTY</th><th style='padding:2px 2px 2px 2px; text-align:center'>PR/PO.</th><th style='padding:2px 2px 2px 2px; text-align:center'>Current Stage</th><th style='padding:2px 2px 2px 2px; text-align:center'>Expected Days</th></tr>";
                    $r=1;
                    foreach($resteyue->result() as $resteyue1)
                    {
                        if($resteyue1->prno<>'')
                        {
                        $stage=$this->storemodel->getprstatus($resteyue1->prno);
                        $expdays=$this->storemodel->getexpecteddays($resteyue1->prno,$itemid);
                        }else
                        {
                            $stage='';
                            $expdays='';
                        }
                        
                     
                        $html.="<tr style='text-align:center;'>
                        <td>".$resteyue1->internal_order_no."</td>
                        <td>".$resteyue1->job_card_no."</td>
                        <td>".$resteyue1->stock."</td>
                        <td>".$resteyue1->prno."<br/>".$resteyue1->pono."</td>
                        <td>".$stage."</td>
                        <td></td>
                        </tr>";
                   $r++;
                   }
                
                
            }
            
            return $html;
        
        }
        
        
        function getactiveblockedcount($itemid)
        {
        
        $resteyue=$this->db->select('sum(a.stock) as ststock')->from('blockedstock a')->where('a.itemid',$itemid)->where('a.active','1')->where('a.issued','0')->get();
        if($resteyue->num_rows()>0)
        {
        foreach($resteyue->result() as $resteyue1);
        return $resteyue1->ststock;
        
        }else
        {
        return 0;
        }
        
        }




    function filterbycondition()
{
	$this->load->view('store/imsfilterbynotpresent');
}


		public function machine_part_data_with_picture_listforfiltered()
		{
			$type=$this->uri->segment(3);
			$i=1;
			$machinepart_data= array();

			if($type<>3)
			{
			$this->db->select('a.id,a.gst,a.part,a.unit,a.current_stock,a.min_stock, a.specification, a.makes, a.size_in_mm, a.material, a.raw_bop, a.fincode, a.qty, a.picture, d.rack_location, p.category')->from('machine_parts_with_picture  a')->join('store_rack_location d','a.location_id=d.id','left')->join('presto_machine_part_category p','a.category_id=p.id','left');
			if($type=='1')
			{
			$this->db->where('a.gst','0.00');
			}else if($type==2)
			{
			$this->db->where('a.fincode',NULL);
			$this->db->or_where('a.fincode','');
			}else if($type==4)
			{
			$this->db->where('a.location_id','0');
			$this->db->or_where('a.location_id',NULL);
			}else if($type==5)
			{
			$this->db->where('a.picture',NULL);
			$this->db->or_where('a.picture','');
			}

			$query = $this->db->get();
			$res = $query->result();
			foreach($res as $row){

			$edit = "<a href='".page_url."Store/edit_parts/".$row->id."'><i class='fa fa-pencil'></i></a>";	

			if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.jpg')){

			$IMG = product_items.$row->fincode.'.jpg';
			$image = "<img src='".$IMG."' style='width:100px;height:100px;'>";
			}else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.JPG')){

			$IMG = product_items.$row->fincode.'.JPG';
			$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.jpeg')){
			$IMG = product_items.$row->fincode.'.jpeg';
			$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.JPEG')){

			$IMG = product_items.$row->fincode.'.JPEG';
			$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else{
			$image="";
			}


			/** ISSUE ITEMS **/
			$isss="<a href='".page_url."Store/issuemachineitems/".$row->id."' class='btn btn-warning btn-xs'>Issue Item</a>";
			/** END **/


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

			$blockeddata=$this->getactiveblockeddetails($row->id);
			$blockeddatacount=$this->getactiveblockedcount($row->id);

			/** END **/
			$extrastock=$row->current_stock-$blockeddatacount;

			$machinepart_data[] = array('sr_no'=>$i,
			'issue'=>$isss,
			'category'=>$row->category,
			'machine_part'=>$row->part,
			'specification'=>$row->specification,
			'makes'=>$row->makes,
			'size_in_mm'=>$row->size_in_mm,
			'material'=>$row->material,
			'fincode'=>$row->fincode."<br/>".$row->raw_bop,
			'unit'=>$unitname,
			'stock'=>$row->current_stock,
			'gst'=>$row->gst,
			'minstock'=>$row->min_stock,
			'minstockstatus'=>$st,
			'extrastock'=>$extrastock,
			'qty'=>$row->qty,
			'location'=>$row->rack_location,
			'vendor'=>$vendor,
			'image'=>$image,
			'edit'=>$edit,
			'blockedstock'=>$blockeddata);
			$i++;
			}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($machinepart_data),
			"iTotalDisplayRecords" => count($machinepart_data),
			"aaData"=>$machinepart_data);

			echo json_encode($results);
			}else{

			
			$restyey=$this->db->select('a.id,b.itemid')->from('machine_parts_with_picture a')->join('vendors_price b','a.id=b.itemid','left')->where('b.itemid',NULL)->get();
			if($restyey->num_rows()>0)
			{
			foreach($restyey->result() as $row1234){
			
			$this->db->select('a.id,a.gst,a.part,a.unit,a.current_stock,a.min_stock, a.specification, a.makes, a.size_in_mm, a.material, a.raw_bop, a.fincode, a.qty, a.picture, d.rack_location, p.category')->from('machine_parts_with_picture  a')->join('store_rack_location d','a.location_id=d.id','left')->join('presto_machine_part_category p','a.category_id=p.id','left')->where('a.id',$row1234->id);
			$query = $this->db->get();
			$res = $query->result();
			foreach($res as $row);

			$edit = "<a href='".page_url."Store/edit_parts/".$row->id."'><i class='fa fa-pencil'></i></a>";	
if($row->picture=='')
			{
		if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.jpg')){
			    
				$IMG = product_items.$row->fincode.'.jpg';
				$image = "<img src='".$IMG."' style='width:100px;height:100px;'>";
			}else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.JPG')){
			    
				$IMG = product_items.$row->fincode.'.JPG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.jpeg')){
			    	$IMG = product_items.$row->fincode.'.jpeg';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
		    }else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.JPEG')){
			    
				$IMG = product_items.$row->fincode.'.JPEG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else{
				$image="";
			}
			
			}else
			{
                	if(file_exists(UPLOADPATH.'product_item/'.$row->picture.'.jpg')){
			    
				$IMG = product_items.$row->picture.'.jpg';
				$image = "<img src='".$IMG."' style='width:100px;height:100px;'>";
			}else if(file_exists(UPLOADPATH.'product_item/'.$row->picture.'.JPG')){
			    
				$IMG = product_items.$row->picture.'.JPG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else if(file_exists(UPLOADPATH.'product_item/'.$row->picture.'.jpeg')){
			    	$IMG = product_items.$row->picture.'.jpeg';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
		    }else if(file_exists(UPLOADPATH.'product_item/'.$row->picture.'.JPEG')){
			    
				$IMG = product_items.$row->picture.'.JPEG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else{
			
			if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.jpg')){
			    
				$IMG = product_items.$row->fincode.'.jpg';
				$image = "<img src='".$IMG."' style='width:100px;height:100px;'>";
			}else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.JPG')){
			    
				$IMG = product_items.$row->fincode.'.JPG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.jpeg')){
			    	$IMG = product_items.$row->fincode.'.jpeg';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
		    }else if(file_exists(UPLOADPATH.'product_item/'.$row->fincode.'.JPEG')){
			    
				$IMG = product_items.$row->fincode.'.JPEG';
				$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
			}else{
				$image="";
			}
			}
			    
			    
			    
			}
			


			/** ISSUE ITEMS **/
			$isss="<a href='".page_url."Store/issuemachineitems/".$row->id."' class='btn btn-warning btn-xs'>Issue Item</a>";
			/** END **/


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

			$blockeddata=$this->getactiveblockeddetails($row->id);
			$blockeddatacount=$this->getactiveblockedcount($row->id);

			/** END **/
			$extrastock=$row->current_stock-$blockeddatacount;

			$machinepart_data[] = array('sr_no'=>$i,
			'issue'=>$isss,
			'category'=>$row->category,
			'machine_part'=>$row->part,
			'specification'=>$row->specification,
			'makes'=>$row->makes,
			'size_in_mm'=>$row->size_in_mm,
			'material'=>$row->material,
			'fincode'=>$row->fincode."<br/>".$row->raw_bop,
			'unit'=>$unitname,
			'stock'=>$row->current_stock,
			'gst'=>$row->gst,
			'minstock'=>$row->min_stock,
			'minstockstatus'=>$st,
			'extrastock'=>$extrastock,
			'qty'=>$row->qty,
			'location'=>$row->rack_location,
			'vendor'=>$vendor,
			'image'=>$image,
			'edit'=>$edit,
			'blockedstock'=>$blockeddata);
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
			
		}
		
		
		
		
		function updatestock()
		{
		   
			$this->load->view('store/add_stock');
			
		}
		
		function updatedexistingstock()
		{
			$itemid=$this->uri->segment(3);
			$this->form_validation->set_rules('stock', 'Stock', 'required|trim');
			$user_id =$this->session->userdata['logged_in']['user_id'];		
			if ($this->form_validation->run() == FALSE)
			{
				$this->load->view('store/add_stock');
			}else
			{
				$stock=$this->input->post('stock');
				$curstock=$this->input->post('curstock');
				
			$this->db->set('current_stock', 'current_stock+'.$stock, false);
			$this->db->where('id' , $itemid);
			$this->db->update('machine_parts_with_picture');
			if($this->db->affected_rows()>0)
			{
			/** ADD HISTORY **/
			$data=array('itemid'=>$itemid,'stock'=>$stock,'stockattimeofupdate'=>$curstock);
			$this->db->insert('additionalstockhistory',$data);
			/** END **/
			
			  $this->session->set_flashdata('message','<div class="alert alert-success">Stock Updated</div>');
			  redirect(page_url.'Store/machineparts');
			
			}else
			{
				$this->session->set_flashdata('message','<div class="alert alert-danger">Unable to update Stock</div>');
				redirect(page_url.'Store/machineparts');
				
			}
			
						
				
			}
			
			
			
		}
		
		
		 function getitemlistpriceforajax()
    {
    $disc=0;
    $suplierid=$this->input->post('suppid');
    $masterid=$this->input->post('masterid');;
    $flag=$this->input->post('flag');
    
    if($flag==0)
    {
    /** MACHINE RELATED **/ 
    $Restyru=$this->db->select('listprice')->from('vendors_price')->where('masterid',$masterid)->where('vendorid',$suplierid)->get();
    if($Restyru->num_rows()>0)
    {
    foreach($Restyru->result() as $Restyru1);
    $disc= $Restyru1->listprice;
    
    }
    }else if($flag==1)
    {
    /** HOUSE KEEPING **/
    $Restyru=$this->db->select('price')->from('vendorwise_house_keeping_item_price')->where('item_id',$masterid)->where('vendor_id',$suplierid)->get();
    if($Restyru->num_rows()>0)
    {
    foreach($Restyru->result() as $Restyru1);
    $disc= $Restyru1->price;
    
    }
    
    }
    echo floatval($disc); exit;
    }
    
    function deliverystatuspo()
    {
    
    $this->load->view('store/poinvoiceforinfo');
    
    }
    
    
    	function manualblockjobcard()
	{


     $this->load->view('store/manualjobcardblock');

	}


	function checkformanualjobcard()
	{

		$jbcard=$this->input->post('jobcard');

		$restye=$this->checkifjbisnotalreadyblocked($jbcard);
		if($restye==0)
		{
			$itemid=$this->getmachineid($jbcard);
			$this->storemodel->manualblockallitems($itemid,$jbcard);
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Jobcard has been blocked</span></div><br/>');
			redirect(page_url.'Store/manualblockjobcard');
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Jobcard already blocked</span></div><br/>');
			redirect(page_url.'Store/manualblockjobcard');
		}
	}


function checkifjbisnotalreadyblocked($jbcard)
{
	$qwe=$this->db->select('id')->from('blockedstock')->where('jobcardid',$jbcard)->get();
	return $qwe->num_rows();



}

	function getalljobcardlist()
{
	$searchtrm= $_GET['q'];
	$rest=$this->db->select('id,job_card_no')->from('order_instruments')->like('job_card_no',$searchtrm,'both')->get();
	
	if($rest->num_rows()>0)
	{
	foreach($rest->result() as $instruments){

	$json[] = array('id'=>$instruments->id, 'text'=>$instruments->job_card_no);

	}
	}else{

	$json[] = array('id'=>"", 'text'=>"No Data Available");

	}

	echo json_encode($json);

}
	
	function getmachineid($jbcard)
	{

$qwe=$this->db->select('item_id')->from('order_instruments')->where('id',$jbcard)->get();
	if($qwe->num_rows()>0)
	{
		foreach($qwe->result() as $qwe11);

		return $qwe11->item_id;

	}else
	{

		echo "Machine Associated with the obcard not found"; exit;
	}


	}


	function mrnformat()
	{
		$this->load->view('store/mrnformat');
	}


function createbulkpo()
	{

		$this->load->view('store/bulkpo');
	}




function bulkgeneratepo()
	{

		$supp=$this->input->post('supplier');

		if(count($supp)>0)
		{
			
			$usupp=array_values(array_unique(array_filter($supp)));
			//echo "<pre>"; print_r($usupp); exit;
			$pon=array();
			for($i=0;$i<count($usupp);$i++)
			{
				$ponumber=$this->getnextponumber();
				$supplid=$usupp[$i];
				$itemss=$this->input->post('checkit'.$supplid);

				for($t=0;$t<count($itemss);$t++)
				{
						$rowid=$itemss[$t];
						$prno=$this->input->post('pr'.$rowid);
						$source=$this->input->post('source'.$rowid);
						$sourceid=$this->input->post('sourceid'.$rowid);
						$insid=$this->input->post('instrumentid'.$rowid);
						$type=$this->input->post('type'.$rowid);
						$item=$_REQUEST['hiddenitem'.$rowid];
						$type=$this->getprtype($prno);

						if($source==1)
						{
						$jobcard=$this->input->post('jobcardno'.$rowid);
						}else{

						$jobcard=0;
						}

						$qty=$this->input->post('qty'.$rowid);
						$iprice=$this->input->post('itemprice'.$rowid);
						$currstock=$this->input->post('currentstock'.$rowid);
						$unit=$this->input->post('unit'.$rowid);
						$iremarks=$this->input->post('itemremarks'.$rowid);
						$oprice=$this->input->post('originalprice'.$rowid);

						if($item<>'' && $qty<>'' && $iprice<>'')
						{
						if($oprice<>$iprice)
						{
						$priceedit='1';
						$ogprice=$oprice;
						}else
						{
						$priceedit='0';
						$ogprice=$oprice;

						}
						//$discount=$this->getitemdiscount($supplid,$item,$type);
						$discount=$this->input->post('discountper'.$rowid);
						$pon[]=$ponumber;

						$qq = $this->db->select('deliveryby')->from('vendors')->where('id',$supplid)->get();
						foreach($qq->result() as $vendordata);
						if($vendordata->deliveryby=='By Vendor'){
						$freight = "1";

						}else{
						$freight = "0";
						}

						$ponumonly=preg_replace('/[^0-9]/', '', $ponumber);
						$data=array('itemid'=>$item,'price'=>$iprice,'qty'=>$qty,'vendor'=>$supplid,'pono'=>$ponumber,'prno'=>$prno,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id'],'source'=>$source,'sourceid'=>$sourceid,'jobcard'=>$jobcard,'unit'=>$unit,'potype'=>$type,'remarks'=>$iremarks,'discount'=>$discount,'pricechange'=>$priceedit,'originalprice'=>$ogprice,'fright'=>$freight,'ponum'=>$ponumonly);
						$this->db->insert('purchase_order',$data);


				}else
				{
					echo "REQUIRED DATA IS MISSING"; exit;
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


					/** item wise approval **/
					$data2=array('approvalstatus'=>'1','approvaldate'=>date('Y-m-d H:i:s'),'approvedBy'=>$_SESSION['logged_in']['user_id']);
					$this->db->where('prno',$prno);
					$this->db->where('itemid',$item);
					$this->db->update('purchase_request',$data2);

					/** end **/

			}


					$c=count($usupp);
					if(count($pon)>0)
					{
					$gpono=base64_encode(implode(',',$pon));
					}else{

					$gpono='';			
					}


				


		}


	$this->session->set_flashdata('message','<div class="alert alert-success">'.$c.' Po(s) generated</div>');
					redirect(page_url.'Store/createbulkpo');

	}else
	{
			$this->session->set_flashdata('response','<div class="alert alert-danger">Unable to generate PO</div>');
			redirect(page_url.'Store/createpo/');

	}



}
	


	function closepritem()
{

$id=$this->uri->segment(3);
$data=array('closed'=>'1','closedOn'=>date('Y-m-d H:i:s'),'closedBy'=>$_SESSION['logged_in']['user_id']);

$this->db->where('id',$id);
$this->db->update('purchase_request',$data);

$this->session->set_flashdata('message','<div class="alert alert-success">PR Item(s) Closed</div>');
redirect(page_url.'Store/createbulkpo');

}


function emailnotifications()
{


	$this->load->view('store/emailnotification');
}


function previewpo()
{

$this->load->view('store/previewpdfformail');

}

function sendmailtosupplierwithpdf()
{

$filename1=$this->storemodel->getvendornameotherdetails($this->uri->segment(3));
if(count($filename1)>0)
{
	$filename=$filename1['name'];
	$contactperson=$filename1['contactperson'];
	$email=$filename1['email'];


}else
{
	$filename='';
	$contactperson='';
	$email='';
}


$html='';

$html.= '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
		<td style="padding: 20px; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
		</tr>
		<tr>
		<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://www.prestogroup.com/images-new/logo-1.png" width="200px;" alt="" /></td>
		</tr>
		<tr>
		<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" />Dear Sir/Maam,<br/><br/>Greeting From PRESTO! <br/><br/>New Purchase Order has been released.<br/><br/> Please find PO attached<br/>';
		$html.='</td></tr>
		
		<!--<tr>
		<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001">Regards<br/>Team Service<br/><a href="mailto:service@presogroup.com">service@presogroup.com</a><br/><a href="tel:91-1294272727">91-129-427-2727</a></td>
		
		</td>
		</tr>-->


		</table>
		</tr>
		</table>';


		$pinooino=$filename.'.pdf';
		$filepath=UPLOADPATH.'popdf/'.$pinooino;

if($email!='')
{
		if(file_exists($filepath))
		{

		$sub="Purchase Order | Presto Stantest Pvt Ltd";
		$subjectname = $sub;
		$this->email->set_mailtype("html");

//		$this->email->to('audit1@prestogroup.com,keshav@prestogroup.com,sdsrbh5@gmail.com');
		$this->email->to($email);
		//$this->email->cc($cc_email);
		$this->email->bcc('sdsrbh5@gmail.com,audit1@prestogroup.com,keshav@prestogroup.com');
		$this->email->from('mitr@prestomitr.com');
		$this->email->subject($subjectname);
		$this->email->attach($filepath);
		$this->email->message($html);
		$result11=$this->email->send();

		/** UPDATE EMAIL NOTIFIEDD **/
		$data=array('emailnotified'=>'1','notifiedon'=>date('Y-m-d H:i:s'),'notifiedBy'=>$_SESSION['logged_in']['user_id']);
		$this->db->where('vendor',$this->uri->segment(3));
		$this->db->where('completed','0');
		$this->db->where('approved','1');
		$this->db->update('purchase_order',$data);
		/** END **/

		$this->session->set_flashdata('message','Email Sent to Customer');
		redirect(page_url.'Store/emailnotifications');

		}else
		{

		$this->session->set_flashdata('message','File Does not Exist hence mail cannot be sent.');
		redirect(page_url.'Store/previewpo/'.$this->uri->segment(3));

		}


}else
{
	$this->session->set_flashdata('message','Supplier Email is not set');
		redirect(page_url.'Store/previewpo/'.$this->uri->segment(3));
}
}



function getpreviousprdetails()
{
     $item=$this->input->post('item');
     $ot=$this->input->post('ot');
     $unit=$this->input->post('unit');
     
    $idata=$this->storemodel->getmachineotherdetails($item);
    if(count($idata)>0)
    {
        $iname=$idata['name'];
        $special=$idata['specialization'];
        $fincode=$idata['fincode'];
        $size=$idata['size'];
      
    }else
    {
        $iname=''; 
        $special='';
        $fincode='';
        $size='';
    }
								
     $html="<table border='1' style='width:100%;line-height:14px;font-size:11px;'><tr style='background-color:white;'><th style='padding:0px 0px 0px 0px;text-align:center;width:33%;'>INDENT NO.</th><th style='padding:0px 0px 0px 0px;text-align:center;width:33%;'>ITEM</th><th style='padding:0px 0px 0px 0px;text-align:center;width:20%;'>QTY.</th><th style='padding:0px 0px 0px 0px;text-align:center;width:33%;'>PR/PO NO.</th><th style='padding:0px 0px 0px 0px;text-align:center;width:33%;'>CURRENT STAGE</th><th style='padding:0px 0px 0px 0px;text-align:center;'>APPROVED ON</th><th style='padding:0px 0px 0px 0px;text-align:center;'>RAISED BY</th></tr>";
     
    
     
     $resteu=$this->db->select('prno,addedOn,addedBy,qty,sourceid,approvalstatus')->from('purchase_request')->where('source','2')->where('itemid',$item)->get();
     
     if($resteu->num_rows()>0)
     {
      foreach($resteu->result() as $prdetail)
     {
    
         $Resteyu=$this->db->select('id,pono,prno,qty,addedOn,addedBy,sourceid')->from('purchase_order')->where('itemid',$item)->where('prno',$prdetail->prno)->where('completed','0')->get();
         if($Resteyu->num_rows()>0)
         {


             foreach($Resteyu->result() as $Resteyu111)
             {
             
		$addedby=$this->getsusername($Resteyu111->addedBy);

		$currentstage=$this->getpostatusforindent($Resteyu111->id);

		$indno=$this->getindentno($Resteyu111->prno);


		$html.="<tr>";
		$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>IND- ".$indno."</td>";
		$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($iname)." (".$fincode.")<br/>".$special."<br/>".$size."</td>";
		$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($Resteyu111->qty)." ".$unit."</td>";
		$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($Resteyu111->prno)."/".strtoupper($Resteyu111->pono)." ".$unit."</td>";
		$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'><strong>".strtoupper($currentstage)."</strong></td>";
		$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".date('d-M-Y H:i:s',strtotime($Resteyu111->addedOn))."</td>";
		$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($addedby)."</td>";
		$html.="</tr>";

                 
                 
             }
            
             
             
             
             
         }else
         {
             
             foreach($resteu->result() as $prdetail)
             {
                 
              $addedby=$this->getsusername($prdetail->addedBy);
                 
                
       if($prdetail->approvalstatus=='0')
       {
                
                $html.="<tr>";
                $html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>IND- ".strtoupper($prdetail->sourceid)."</td>";
                $html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($iname)." (".$fincode.")<br/>".$special."<br/>".$size."</td>";
                $html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($prdetail->qty)." ".$unit."</td>";
                $html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($prdetail->prno)."/PO Not Found</td>";
                $html.="<td style='padding:2px 2px 2px 2px; text-align:center;'><strong>PO YET TO BE CREATED</strong></td>";
                $html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".date('d-M-Y H:i:s',strtotime($prdetail->addedOn))."</td>";
                 $html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($addedby)."</td>";
                $html.="</tr>";
                
         }else
         {
         
     $html.="<tr>";
                $html.="<td style='padding:2px 2px 2px 2px; text-align:center;' colspan='7'>No PR Found</td>";
              
                $html.="</tr>";
         
         
         }
             }
         }
         
         
         }
         
     }else
     {
         
         $html.="<tr>";
                $html.="<td style='padding:2px 2px 2px 2px; text-align:center;' colspan='7'>No PR Found</td>";
              
                $html.="</tr>";
         
     }
     
     echo $html;
     
     
}


function getpostatusforindent($poid)
{
    $cur='';
    $rste=$this->db->select('id,completed')->from('purchase_order')->where('approved','1')->where('id',$poid)->get();
    
    if($rste->num_rows()>0)
    {
        foreach($rste->result() as $rste1);
        if($rste1->completed==0)
        {
            $cur="MRN Pending";
            
        }else
        {
            $cur="MRN Completed";
        }
        
        
    
    
    }else
    {
        $cur="Pending for Approval";
    }
    
    
    return $cur; exit;
    
}



	function getissueconfirmationagainstjobcard()
	{
	    $issueid=$this->input->post('issueid');
	    $sourceblockid=$this->input->post('sourceblockid');
	    $destinationblockedid=$this->input->post('selfblockedid');
	 //  echo $issueid.'<br/>'.$sourceblockid.'<br/>'.$destinationblockedid; exit;
	    if($sourceblockid<>$destinationblockedid)
	    {
       
            /** CHECK FOR ANY PR RAISED **/
            
            $prraise=$this->checkfordestinationprifany($destinationblockedid,$sourceblockid);
            
           // echo $prraise; exit;
            if($prraise==0)
            {
               
               /** RAISE NEW PR **/ 
                
                $newprdetails=$this->getitemetcdetailsfromblocked($sourceblockid);
                if(count($newprdetails)>0)
                {
                 
                    $itemid=$newprdetails['itemid'];
                    $newjbcard=$newprdetails['jobcard'];
                    $newstock=$newprdetails['stock'];
                    
                    $data1122=array('itemid'=>$itemid,'qty'=>$newstock,'type'=>'0','prtype'=>'0','generatedOn'=>date('Y-m-d H:i:s'),'generatedby'=>$_SESSION['logged_in']['user_id'],'sendtopurchase'=>'0','sendon'=>date('Y-m-d H:i:s'),'sendby'=>$_SESSION['logged_in']['user_id'],'jobcardid'=>$newjbcard);
                  
                    
                    $this->db->insert('prduetomaterialdiversion',$data1122);
                    
                    
                }
                
                
                /** END **/
                
            }
            
            /** END **/
            
       
        
        
        /** MATERIAL DIVERSION HISTORY **/
        
        $destinationjbcard= $this->getjobcardidfromblocked($destinationblockedid);
        $sourcejbcard=$this->getjobcardidfromblocked($sourceblockid);
        
        $dataww=array('sourceblockedid'=>$sourceblockid,'destinationblockedid'=>$destinationblockedid,'sourcejobcard'=>$sourcejbcard,'destinationjobcard'=>$destinationjbcard,'itemid'=>$itemid,'qty'=>$newstock);
        $this->db->insert('materialdiversionhistory',$dataww); 
        /** END **/
   
	    }
        /** MARK RECIEVED **/
        
        $data=array('storeaccept'=>'1','acceptedOn'=>date('Y-m-d H:i:s'),'acceptedBy'=>$_SESSION['logged_in']['user_id']);
        $this->db->where('id',$issueid);
        $this->db->update('issuestocktousers',$data);
        
        /** END **/ 
        
	   
        $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Material Issue Acknowledged.</div>');
        
        redirect(page_url.'Store/unacknowledgeditems');
	    
	}
	
	
	function checkfordestinationprifany($destinationblockedid,$sourceblockedid)
	{
            $raise=0;
            $restey=$this->db->select('prno,itemid')->from('blockedstock')->where('id',$destinationblockedid)->get();
            if($restey->num_rows()>0)
            {
            foreach($restey->result() as $reste);
            $prno=$reste->prno;
            $itemid=$reste->itemid;
            if($prno<>'')
            {
            
            $raise=$this->checkifpriscompleted($prno,$destinationblockedid,$sourceblockedid,$itemid);
            
            }
            
            
            }
	        
	        return $raise;
	    
	}
	
	
	function checkifpriscompleted($pr,$destinationblockedid,$sourceblockedid,$itemid)
	{
	    $rwytew=$this->db->select('id,pono')->from('purchase_order')->where('approved','1')->where('prno',$pr)->where('itemid',$itemid)->where('completed',0)->get();
	    
	   if($rwytew->num_rows()>0)
	   {
	       foreach($rwytew->result() as $ssss);
	       $this->ifprisunderprocessthenchangethejobcardno($pr,$destinationblockedid,$sourceblockedid,$ssss->pono,$itemid);
	       $raise=1;
	   }else
	   {
	       $raise=0;
	   }
	   
	   return $raise;
	    
	}


function ifprisunderprocessthenchangethejobcardno($pr,$destinationblockedid,$sourceblockedid,$pono,$itemid)
{
   $destinationjbcard= $this->getjobcardidfromblocked($destinationblockedid);
   $sourcejbcard=$this->getjobcardidfromblocked($sourceblockedid);
   
   /** UPDATE PURCHASE REQUEST **/
    $data=array('jobcardid'=>$sourcejbcard);
    $this->db->where('prno',$pr);
    $this->db->where('itemid',$itemid);;
    $this->db->where('jobcardid',$destinationjbcard);
    $this->db->update('purchase_request',$data);
    
    /** UPDATE PURCHASE ORDER **/
    $data1=array('jobcard'=>$sourcejbcard);
    $this->db->where('prno',$pr);
    $this->db->where('itemid',$itemid);
    $this->db->where('jobcard',$destinationjbcard);
    $this->db->update('purchase_order',$data1);
    /** END **/
    
    /** UPDATE MRN **/
    $data2=array('jobcardno'=>$sourcejbcard);
    $this->db->where('itemid',$itemid);
    $this->db->where('pono',$pono);
    $this->db->where('jobcardno',$destinationjbcard);
    /** END **/
    
    /** UPDATE PR NO. FOR BLOCKED ITEM **/
    
    $data3=array('prno'=>$pr,'pono'=>$pono);
    $this->db->where('id',$sourceblockedid);
    $this->db->update('blockedstock',$data3);
    
    /** END **/
    
    
}


function getjobcardidfromblocked($blockedid)
{
    $jbid='0';
    $resteye=$this->db->select('jobcardid')->from('blockedstock')->where('id',$blockedid)->get();
    if($resteye->num_rows()>0)
    {
        foreach($resteye->result() as $row);
        
        $jbid=$row->jobcardid;
        
    }else
    {
        echo "NO JOB CARD ID FOUND"; exit;
    }
    
    return $jbid;
    
    
}


function getitemetcdetailsfromblocked($sourceblockedid)
{
    $newprdetails=array();
   $resteye=$this->db->select('itemid,jobcardid,stock')->from('blockedstock')->where('id',$sourceblockedid)->get();
    if($resteye->num_rows()>0)
    {
        foreach($resteye->result() as $row);
        
        $newprdetails['itemid']=$row->itemid;
        $newprdetails['jobcard']=$row->jobcardid;
        $newprdetails['stock']=$row->stock;
        
        
    }
    
    return $newprdetails; 

}

function sendtopogeneration()
{
 $id=$this->uri->segment(3);
 
 $newpr=$this->getnewprnumber();
 
 $prno = (int) filter_var($newpr, FILTER_SANITIZE_NUMBER_INT);  
 
 $resteyre=$this->db->select('*')->from('prduetomaterialdiversion')->where('id',$id)->get();
 if($resteyre->num_rows()>0)
 {
     
     foreach($resteyre->result() as $resteyre1); 
     
     $prdetails=$this->getpritemdetails($resteyre1->itemid);
     if(count($prdetails)>0)
     {
     $data=array('masterid'=>$resteyre1->itemid,'itemid'=>$resteyre1->itemid,'source'=>'1','type'=>'0','sourceid'=>'','prno'=>$newpr,'jobcardid'=>$resteyre1->jobcardid,'qty'=>$resteyre1->qty,'unit'=>$prdetails['unit'],'prraisereason'=>'','stockattimeofpr'=>$prdetails['stock'],'addedBy'=>$_SESSION['logged_in']['user_id'],'addedOn'=>date('Y-m-d H:i:s'),'approvalstatus'=>'0','approvaldate'=>'','approvedBy'=>'','updatedOn'=>'','updatedBy'=>'','purno'=>$prno,'closed'=>'0','closedOn'=>'','closedBy'=>'');
     
     //echo "<pre>"; print_r($data); exit;
     
     $this->db->insert('purchase_request',$data);
     
     if($this->db->affected_rows()>0)
     {
        $edata=array('sendtopurchase'=>'1');
        $this->db->where('id',$id);
        $this->db->update('prduetomaterialdiversion',$edata);
     }
     
     $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Send for PR Generation</div>');
     
     redirect(page_url.'Reporting/prduetomaterialdiversion');
     
         
         
     }else
     {
           $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Unable to Migrate</div>');
     
     redirect(page_url.'Reporting/prduetomaterialdiversion');
         
     }
     
     
     
     
      
 }
 
 
 
    
    
    
}


function getnewprnumber()
{
    
    
    $prnos=$this->db->query('SELECT id, purno FROM purchase_request ORDER BY  CAST(purno AS decimal) DESC LIMIT 1');
      //  $prnos=$this->db->select('max(purno) as purn')->from('purchase_request')->limit(1)->get();
        $num=$prnos->num_rows();
        if($num==0)
        {
        $num1=1;
        $num_padded = sprintf("%02d", $num1);
        $code='PRESPR'.$num_padded;
        }else{
        foreach($prnos->result() as $prnoss);
        $numpart =$prnoss->purn;
        $num1=$numpart+1;
        $num_padded = sprintf("%02d", $num1);
        $code='PRESPR'.$num_padded;
        
        }
        
        return $code;
        
        
}

function getpritemdetails($itemid)
{
    $restdata=array();
    $restye=$this->db->select('current_stock,unit')->from('machine_parts_with_picture')->where('id',$itemid)->get();
    if($restye->num_rows()>0)
    {
        foreach($restye->result() as $restye1);
        $restdata['stock']=$restye1->current_stock;
        $restdata['unit']=$restye1->unit;
        
    }
    
    return $restdata; 
    
}



function getblockedlistforthisitem()
{
    $selfblocked='';
    
    $id=$this->input->post('id');
    
    $resty=$this->db->select('itemtype,issuetype,blockedid,itemid,stock')->from('issuestocktousers')->where('id',$id)->get();
    if($resty->num_rows()>0)
    {
        foreach($resty->result() as $resty1);
        
        if(trim($resty1->blockedid)>0)
        {
            $selfblocked=$this->getcurrentotherblockeddetail($resty1->blockedid,$resty1->itemid);
            
           
        
        }
        
        
    }
    
    
     echo $selfblocked; exit;
}
	
	
	function getcurrentotherblockeddetail($blockedd,$itemid)
	{
	    $html='';
	    $rewtye=$this->db->select('a.*,b.job_card_no')->from('blockedstock a')->join('order_instruments b','a.jobcardid=b.id')->where('a.id !=',$blockedd)->where('itemid',$itemid)->where('active','1')->where('issued','0')->get();
        if($rewtye->num_rows()>0)
        {
            $html.='<div style="overflow-y: scroll;height:300px;"><table class="table table-bordered">
        <thead>
        <tr>
        <th>#</th>
        <th>Jobcard</th>
        <th>Qty</th>
        </tr>
        </thead>
        <tbody>';
        
         $html.='<tr>
        <td><input type="radio" name="sourceblockid"  value="'.$blockedd.'"><input type="hidden" name="selfblockedid" value="'.$blockedd.'"></td>
        <td>SELF</td>
        <td></td>
        </tr>';
        
        foreach($rewtye->result() as $row)
        {
            
        $html.='<tr>
        <td><input type="radio" name="sourceblockid" value="'.$row->id.'" required></td>
        <td>'.$row->job_card_no.'</td>
        <td>'.floatval($row->stock).'</td>
        </tr>';
        
        }
    
        $html.='</tbody>
        </table></div>';
        
        }
	    
	    
	    return $html;
	    
	}


function getthisitemdetails()
{
    $restyu='';
    
    $id=$this->input->post('id');
    
    $resty=$this->db->select('a.itemid,a.stock,a.jobcardid,b.part,b.specification,b.fincode')->from('issuestocktousers a')->join('machine_parts_with_picture b','a.itemid=b.id')->where('a.id',$id)->get();
    if($resty->num_rows()>0)
    {
            foreach($resty->result() as $resty1);
            $jobcard=$this->getjobcardno($resty1->jobcardid);
            $restyu=$resty1->part.'|'.$resty1->stock.'|'.$jobcard;
    }
    
    
    echo $restyu;


}

function closethispr()
{

$Resteyur=$this->uri->segment('3');

$data=array('closed'=>'1');

$this->db->where('id',$Resteyur);
$this->db->update('prduetomaterialdiversion',$data);
$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">PR Closed</div>');
redirect(page_url.'Reporting/prduetomaterialdiversion');



}


function saveActionType() {
			$serviceRepairID = $this->input->post('hidden_id');
			$remarks = $this->input->post('remarks');
			$io_no = $this->input->post('io_no');
			$action_type = $this->input->post('action_type');

			$data = array(
 					'remarks' => $remarks,
 					'action_type' => $action_type,
 					'io_no' => $io_no,
 					'flag' => 1
					);

			$result = $this->storemodel->saveAction($data, $serviceRepairID);

			if($result > 0) {
				if($action_type == 2) {
					redirect(page_url.'Salesforceorder/fetchservicemissingorder/'.$io_no.'/2');
				} else {
					$this->session->set_flashdata('response', '<div class="alert alert-danger">Service Repair Request Closed.</div>');
					redirect(page_url.'Store/servicerequestdashboardforservice');
				}
			}
		}
		
		
		function servicerequestdashboardforservicehistory()
		{
		
			$this->load->view('store/servicerepairdashboardforservicehistory');
			
			
		}
		
		
		
			function getrepairservicerequestforservicehistory()
		{
			$imported_data=array();
			
			
			$restyyu=$this->db->select('*')->from('service_repair_request')->where('flag', 1)->order_by('qc','ASC')->get();
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
						//$sta="<a href='".page_url."FMS/order/".base64_encode('service-'.$row->id)."'><span class='btn btn-warning btn-xs'>Generate Order</span></a>";
						$sta="<a href='javascript:;' onclick='showReturnModal(".$row->id.")'><span class='btn btn-warning btn-xs'>Next Step</span></a>";

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

			if($row->action_type == 1) {
				$type = 'RETURN WITHOUT REPAIR';
			} else if($row->action_type == 2) {
				$type = 'GENERATE INTERNAL ORDER';
			} else if($row->action_type == 3) {
				$type = 'OTHER';
			} else {
				$type = '';
			}

			if ($row->io_no == 0) {
				$io_no = '';
			} else {
				$io_no = $row->io_no;
			}
			

			$imported_data[] = array('sr_no'=>$i,
			'status'=>$sta,
			'company'=>$row->companyname,
			'instrument'=>$html,
			'qcon'=>$qcdone,
			'qcby'=>$alluser1,
			'challan'=>$im,
			'addedOn'=>date('d-m-Y g:i A',strtotime($row->addedOn)),
			'raisedby'=>$alluser,
			'type' => $type,
			'remarks' => $row->remarks,
			'io_no' => $io_no
		);
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
		
		function getindentno($prno)
		{
		$reste=$this->db->select('sourceid')->from('purchase_request')->where('prno',$prno)->get();
		if($reste->num_rows()>0)
		{
		foreach($reste->result() as $reste1);
		return $reste1->sourceid;
		}else
		{
		return '';
		}
		
		}
		
		
		function pendingpoitemsforstore()
		{
		$this->load->view('store/pendingpoitemlistforstore');
		}
	
	
	function searchpendingitems()
	{
	$item=$this->input->post('item');
	
	redirect(page_url.'Store/pendingpoitemsforstore/'.$item);
	
	}
	
	
	function getmachinesforims()
	{
		$searchtrm= $_GET['q'];
		if(isset($_GET['previousvalue'])&&($_GET['previousvalue']<>''))
		{
		$prevval=urldecode($_GET['previousvalue']);
		$resd=explode(',',$prevval);
		$prevval="'" . implode ( "', '", $resd ) . "'";
		}else
		{
		$prevval='';
		}
		
		$type = 1;
		if($type=='1'){
		
		$this->db->select('id, part,specification,size_in_mm,fincode,material')->from('machine_parts_with_picture')->like('part',$searchtrm,'both',false)->where('status','1');
	
		if($prevval<>'')
		{
		$this->db->where_not_in('id',$prevval,false);
		}
		
		$query = $this->db->get();
		
		if($query->num_rows()>0)
		{
		foreach($query->result() as $machine_items){
        
        if($machine_items->specification<>'')
        {
            $secondpa=$machine_items->specification;
        }else
        {
            $secondpa=$machine_items->size_in_mm;
        }
        if($machine_items->material<>'')
        {
            $thirdpart=$machine_items->material;
        }else{
            
              $thirdpart='';
            
        }
		$json[] = array('id'=>$machine_items->id, 'text'=>$machine_items->part."-".$secondpa.' - '.$thirdpart.' ('.$machine_items->fincode.')');

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
function approvebulkpos()
{
    $approved=$this->input->post('approvedrecord');
	date_default_timezone_set("Asia/Kolkata");
	$user_id =$this->session->userdata['logged_in']['user_id'];
    
    if(count($approved)>0)
    {
        for($i=0; $i<count($approved);$i++)
        {
        $id = $approved[$i];
        $pono = $this->input->post('mypo'.$id);
        $itemid = $this->input->post('myitemid'.$id);
        
        $data = array('approved'=>'1',
        'approvedOn'=>date('Y-m-d H:i:s'),
        'approvedBy'=>$user_id);
        $this->db->where('id',$id);
        $res = $this->db->update('purchase_order',$data);
        if($res){
        $data1= array('po_no'=>$pono,
        'itemid'=>$itemid,
        'followupdate'=>date('Y-m-d'),
        'poid'=>$id);
        $this->db->insert('vendor_followup',$data1);
        }
        }
        
        $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">PO Approved</div>');
        redirect(page_url.'Reporting/pendingpoforapproval');
    
        
    }else
    {
       	$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Select atleast one item to approve</div>');
        redirect(page_url.'Reporting/pendingpoforapproval');
        
    }
    
}

function addporemarks()
{
    $pono= $this->input->post('pono');
    
    $data1=array('approved'=>'2');
    $this->db->where('pono',$pono);
    $this->db->update('purchase_order',$data1);
    
    
    $data=array('pono'=>$pono,'remarks'=>$this->input->post('remarks'),'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
    $this->db->insert('porejectremarks',$data);

$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">PO Rejected.</span></div>');
redirect(page_url.'Reporting/pendingpoforapproval');

    
}



}
