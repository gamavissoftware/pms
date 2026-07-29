<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tech_support extends CI_Controller {
	
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
$config['smtp_host'] = 'smtpout.secureserver.net';  
$config['smtp_user'] = 'mitr@prestomitr.com';  
$config['smtp_pass'] = 'Presto@123!@#';   
$config['smtp_port'] = 587;  
$this->email->initialize($config);  
  
$this->email->set_newline("\r\n");  
$this->load->library('email', $config);
	$ip = $_SERVER["REMOTE_ADDR"];
		 /*$query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
            if($query->num_rows()=='0'){
            $this->session->set_flashdata('message','<span class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</span><br/>');
            redirect(page_url.'Dashboard');    
            }	*/
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
		$this->form_validation->set_rules('business_loc', 'Location', 'required|trim');
		$this->form_validation->set_rules('location', 'Location', 'required|trim');
		$this->form_validation->set_rules('user_id', 'User Name', 'required|trim');
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
			'ticket_raised_by'=>$this->input->post('user_id'),
			'item_name'=>$this->input->post('item_name'),
			'remarks'=>$this->input->post('remarks'),
			'screenshot'=>$screenshot,
			'priority'=>$this->input->post('priority'),
			'ticket'=>$ticket_id,
			'status'=>'0',
			'added_on'=>$date,
			'added_by'=>$user_id);
			
			//echo "<pre>"; print_r($data); exit;
			
		$result  = $this->db->insert($table,$data);	
		 $insert_id = $this->db->insert_id();
		if($insert_id)
		{
			$query22 = $this->db->select('department_id, business_loc_id, department')->from('departments')->where('department_id',$this->input->post('location'))->where('business_loc_id',$this->input->post('business_loc'))->get();
			$res = $query22->result();
			foreach($res as $raised_by_department)
			
		    $query = $this->db->select('a.*,b.company_name, a.ticket as raised_ticket, b.business_loc_id, b.address,d.user_id,d.user_id, d.first_name as raised_by_fname, d.last_name as raised_by_lname, e.department_id, e.department,g.item_id, g.item_name, g.unique_id')->from('tech_support_ticket a')->join('business_location b','a.business_loc_id=b.business_loc_id','left')->join('departments e','e.department_id=a.location_id','left')->join('system_users d','a.added_by=d.user_id','left')->join(' tech_items g','a.item_name=g.item_id','left')->where('ticket_id',$insert_id)->get();
			foreach($query->result() as $ticket_info);
             $this->db->select('email,name')->from('support_email_option')->where('support_option',1);
             $query = $this->db->get();
			 $res = $query->row_array();
	
             $toemail = $res['email'];
             $toname = $res['name'];

			

			 $this->db->select('email')->from('support_email_options')->where('support_option',1);
             $query = $this->db->get();
			 $res = $query->result_array();
			 foreach($res as $element){
             $ccemail[] = $element['email'];

			 }
			 $ccemails = implode(',',$ccemail);

			$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
				   <tr>
					<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="http://crm.packingtest.com/assets/images/logo-1.png" width="200px;" alt="Prestogroup" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>TECH SUPPORT REQUEST</strong><br></td>
					  </tr> 
					  
					  <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>Ticket ID #'.$ticket_info->raised_ticket.'</strong><br><br></td>
					  </tr> 
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;">Dear IT Team, New Ticket has been raised. Please find the detail.
						</td>
					  </tr>
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					  
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;;">Raised By '.$ticket_info->raised_by_fname.' '.$ticket_info->raised_by_lname.'</td>
					  </tr>
					  
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Department </strong> : '.$raised_by_department->department.'</td>
					  </tr>
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					 
					  <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Remark </strong> : '.$ticket_info->remarks.'</td>
					  </tr>
					   <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Priority </strong> : '.$ticket_info->priority.'</td>
					  </tr>
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Ticket URL </strong> : http://crm.packingtest.com/Tech_support/view_history/'.$insert_id.'</td>
					  </tr>
					  
					  <tr>
						<td>&nbsp;</td>
					  </tr>
					
					  
					  	<tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;;">Best Regards, <br>Prestogroup CRM</td>
					  </tr>
					 <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td>
					   </td>
					  </tr>
					</table>
					</td>
				  </tr>
				</table>';
				//echo $Message; exit;
					$subjectname = "Tech Support Request ".$ticket_info->raised_ticket;
					$this->email->set_mailtype("html");
					$this->email->to($toemail);
					$this->email->cc($ccemails);
					$this->email->bcc('mangleshup@gmail.com');
					$this->email->from('mitr@prestomitr.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();
					
					
					$userinfo = $this->db->select('user_id, first_name, last_name, email')->from('system_users')->where('user_id',$user_id)->get();
					foreach($userinfo->result() as $acknowledge)
					
					$Message1 = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
				   <tr>
					<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="http://crm.packingtest.com/assets/images/logo-1.png" width="200px;" alt="Prestogroup" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>TECH SUPPORT REQUEST</strong><br></td>
					  </tr> 
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>Ticket ID #'.$ticket_info->raised_ticket.'</strong><br><br></td>
					  </tr> 
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;">Thank you! '.$acknowledge->first_name.' '.$acknowledge->last_name.', Your Ticket Successfully Raised. You can check your Ticket status by using below link.	</td>
					  </tr>
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					 
					   <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Ticket URL </strong> : http://crm.packingtest.com/Tech_support/view_history/'.$insert_id.'</td>
					  </tr>
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					 
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;;">Best Regards, <br>
						Presto IT Team
						</td>
					  </tr>
					</table>
					</td>
				  </tr>
				</table>';
				
				
				//echo $Message1; exit;
					$subjectname = "Tech Support Request ".$ticket_info->raised_ticket;
					$this->email->set_mailtype("html");
					$this->email->to($acknowledge->email);
					$this->email->from('mitr@prestomitr.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message1);
    				$result11=$this->email->send();	
			
			$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully added.</div>');
			redirect(page_url.'Tech_support/view_status');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tech_support/view_status');
		}
		
		
	}
		
	}
	public function raised_ticket_list()
	{
		$i=1;
		$support_ticket = array();
		$this->db->select('a.*,b.department_id, b.department, c.user_id, c.first_name, c.last_name,d.user_id, d.first_name as closed_by_fname, d.last_name as closed_by_lname, e.type_id, e.asset_type as items')->from('tech_support_ticket a')->join('departments b','a.location_id=b.department_id','left')->join('system_users c','a.ticket_raised_by=c.user_id','left')->join('system_users d','a.updated_by=d.user_id','left')->join('asset_type e','a.item_name=e.type_id','left');
		$this->db->where('a.status','0');
		$this->db->order_by('a.added_on','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$status = "<a href='".page_url."Tech_support/update_ticket_status/".$row->ticket_id."/".$row->status."'><span class='btn btn-success btn-xs'>Reopen</span></a>";
				//$status = "<span class='btn btn-success btn-xs'>Closed</span>";
				$updateddate = date('Y-m-d', strtotime($row->updated_on));
				$time = date('H:i:s', strtotime($row->updated_on));
				$updatedtime = "<br>". date('g:i A', strtotime($time)); 
				$sta = $status."<br>"."This ticket has been closed by <strong>".$row->closed_by_fname." ".$row->closed_by_lname."</strong><br> at ".$updateddate." ".$updatedtime;
				
			}else
			{
				$sta ="<a href='".page_url."Tech_support/update_ticket_status/".$row->ticket_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Pending</span></a>";
			}
			$history = "<a href='".page_url."Tech_support/view_history/".$row->ticket_id."'><span class='btn btn-warning btn-xs'><i class='fa fa-eye'></i>&nbsp; Add /See Comments</span></a>";
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
			'location'=>$row->department,
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
	
	public function raised_ticket_list_by_department()
	{
		$query = $this->db->select('user_id, department_id')->from('system_users')->where('department_id',$this->uri->segment(3))->get();
		$res = $query->result();
		foreach($res as $departmentdata)
		$user_id[] = $departmentdata->user_id;
		$i=1;
		$support_ticket = array();
			$this->db->select('a.*,b.department_id, b.department, c.user_id, c.first_name, c.last_name,d.user_id, d.first_name as closed_by_fname, d.last_name as closed_by_lname, e.type_id, e.asset_type as items')->from('tech_support_ticket a')->join('departments b','a.location_id=b.department_id','left')->join('system_users c','a.ticket_raised_by=c.user_id','left')->join('system_users d','a.updated_by=d.user_id','left')->join('asset_type e','a.item_name=e.type_id','left');
			$this->db->where('a.status','0');
		$this->db->where_in('a.added_by',$user_id);
		$this->db->order_by('a.added_on','desc');
		$query = $this->db->get();
		$res = $query->result();
	foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$status = "<a href='".page_url."Tech_support/update_ticket_status/".$row->ticket_id."/".$row->status."'><span class='btn btn-success btn-xs'>Reopen</span></a>";
				//$status = "<span class='btn btn-success btn-xs'>Closed</span>";
				$updateddate = date('Y-m-d', strtotime($row->updated_on));
				$time = date('H:i:s', strtotime($row->updated_on));
				$updatedtime = "<br>". date('g:i A', strtotime($time)); 
				$sta = $status."<br>"."This ticket has been closed by <strong>".$row->closed_by_fname." ".$row->closed_by_lname."</strong><br> at ".$updateddate." ".$updatedtime;
				
			}else
			{
				$sta ="<a href='".page_url."Tech_support/update_ticket_status/".$row->ticket_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Pending</span></a>";
			}
			$history = "<a href='".page_url."Tech_support/view_history/".$row->ticket_id."'><span class='btn btn-warning btn-xs'><i class='fa fa-eye'></i>&nbsp; Add /See Comments</span></a>";
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
			'location'=>$row->department,
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
			
			$query = $this->db->select('a.added_by,a.ticket,a.ticket_id,a.updated_on, a.updated_by,b.user_id,a.remarks, b.first_name as assigned_to_first_name, b.last_name as assigned_to_last_name,b.email ,c.user_id, c.first_name as closed_by_first_name, c.last_name as closed_by_last_name')->from('tech_support_ticket a')->join('system_users b','a.added_by=b.user_id','left')->join('system_users c','a.updated_by=c.user_id','left')->where('a.ticket_id',$identifier)->get();
			foreach($query->result() as $ticket_info);
			$updateddate = date('Y-m-d', strtotime($ticket_info->updated_on));
				$time = date('H:i:s', strtotime($ticket_info->updated_on));
				$updatedtime =  date('g:i A', strtotime($time)); 

			 $this->db->select('email')->from('system_users')->where('user_id',$user_id);
             $query = $this->db->get();
			 $res = $query->row_array();
             $fromemail = $res['email'];

			 $this->db->select('email')->from('support_email_options')->where('support_option',1);
             $query = $this->db->get();
			 $res = $query->result_array();
			 foreach($res as $element){
             $ccemail[] = $element['email'];

			 }
			 $ccemails = implode(',',$ccemail);

			$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
				   <tr>
					<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="http://crm.packingtest.com/assets/images/logo-1.png" width="200px;" alt="Prestogroup" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>TECH SUPPORT REQUEST</strong><br></td>
					  </tr> 
					  <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>Ticket ID #'.$ticket_info->ticket.'</strong><br><br></td>
					  </tr> 
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;">This ticket has been closed by '.$ticket_info->closed_by_first_name.' '.$ticket_info->closed_by_last_name.'. on '.$updateddate.' '.$updatedtime.'
						</td>
					  </tr>
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					  
					  
					  <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Task Name </strong> : '.$ticket_info->remarks.'</td>
					  </tr>
					 
					  
					 
					 <tr>
						<td>Warm Regards,<br> '.$ticket_info->closed_by_first_name.' '.$ticket_info->closed_by_last_name.'</td>
					  </tr>
					  <tr>
						<td>
					   </td>
					  </tr>
					</table>
					</td>
				  </tr>
				</table>';
				//echo $Message; exit;
				$subjectname = "Tech Support Request ".$ticket_info->ticket;
					$this->email->set_mailtype("html");
					$this->email->to($ticket_info->email);
					$this->email->cc($ccemails);
					$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from($fromemail);
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();
					
					
					
			$this->session->set_flashdata('message', '<div class="alert alert-danger" style="color:#fff;">Status successfully updated.</div>');
			redirect('Tech_support/view_status');
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
			
			$query = $this->db->select('a.*, b.ticket_id,b.ticket as support_ticket, c.user_id, c.first_name as updated_by_fname, c.last_name as updated_by_lname,c.email')->from('tech_support_ticket_progress a')->join('tech_support_ticket b','b.ticket_id=a.id','left')->join('system_users c','a.added_by=c.user_id','left')->where('a.ticket_id',$this->uri->segment(3))->get();
			foreach($query->result() as $ticket_info);
			 $this->db->select('email,name')->from('support_email_option')->where('support_option',1);
             $query = $this->db->get();
			 $res = $query->row_array();
	
             $toemail = $res['email'];
             $toname = $res['name'];

			

			 $this->db->select('email')->from('support_email_options')->where('support_option',1);
             $query = $this->db->get();
			 $res = $query->result_array();
			 foreach($res as $element){
             $ccemail[] = $element['email'];

			 }
			 $ccemails = implode(',',$ccemail);
			 
			$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
				   <tr>
					<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="http://crm.packingtest.com/assets/images/logo-1.png" width="200px;" alt="Prestogroup" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>TECH SUPPORT REQUEST</strong><br></td>
					  </tr> 
					  <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>Ticket ID #'.$ticket_info->support_ticket.'</strong><br><br></td>
					  </tr> 
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;">'.$ticket_info->updated_by_fname.' '.$ticket_info->updated_by_lname.' has updated the remarks on raised ticket.</td>
					  </tr>
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					  
					  
					  <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Remarks </strong> : '.$ticket_info->remarks.'</td>
					  </tr>
					 
					  
					 
					 <tr>
						<td>Warm Regards,<br> '.$ticket_info->updated_by_fname.' '.$ticket_info->updated_by_lname.'</td>
					  </tr>
					  <tr>
						<td>
					   </td>
					  </tr>
					</table>
					</td>
				  </tr>
				</table>';
				//echo $Message; exit;
				$subjectname = "Tech Support Request ".$ticket_info->support_ticket;
					$this->email->set_mailtype("html");
						$this->email->to($toemail);
					$this->email->cc($ccemails,$ticket_info->email);
					$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('mitr@prestomitr.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();
					
					
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
	
	public function asset_type()
	{
		$this->load->view('techsupport/asset_type');
	}
	
	public function add_asset_type()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('asset_type', 'Location', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('techsupport/asset_type');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "asset_type";
		$query = $this->db->select('asset_type')->from('asset_type')->where('asset_type',$this->input->post('asset_type'))->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		if($res){
			$this->session->set_flashdata('message','Sorry! This record already exist.');
			redirect(page_url.'Tech_support/asset_type');
			
		}else{
		
		
			$data = array(
			'asset_type'=>$this->input->post('asset_type'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Tech_support/asset_type');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tech_support/asset_type');
		}
		}
		
	}
		
	}
	public function asset_type_list()
	{
		$i=1;
		$asset_type_data = array();
		$this->db->select('*')->from('asset_type');
		$this->db->order_by('asset_type','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Tech_support/update_asset_type_status/".$row->type_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Tech_support/update_asset_type_status/".$row->type_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Tech_support/edit_asset_type/".$row->type_id."'><i class='fa fa-pencil'></i></a>";	
				
			$asset_type_data[] = array('sr_no'=>$i,
			'asset_type'=>$row->asset_type,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($asset_type_data),
			"iTotalDisplayRecords" => count($asset_type_data),
			"aaData"=>$asset_type_data);
			
		echo json_encode($results);
	}
	
	public function update_asset_type_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "type_id";
		$table = "asset_type";
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
			redirect('Tech_support/asset_type');
		}

	public function edit_asset_type(){
		$this->load->view('techsupport/edit_asset_type');
		
	}	
	
	public function update_asset_type()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('asset_type', 'Asset Type', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('techsupport/edit_asset_type');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "asset_type";
		$query = $this->db->select('asset_type')->from('asset_type')->where('asset_type',$this->input->post('asset_type'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','Sorry! This record already exist.');
			redirect(page_url.'Tech_support/asset_type');
			
		}else{
		
		
			$data = array(
			'asset_type'=>$this->input->post('asset_type'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		$this->db->where('type_id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'Tech_support/asset_type');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Tech_support/asset_type');
		}
		}
		
	}
		
	}


	public function sendlevelone()
	{   $json = array();
		$ticket_id = $this->input->post('ticket_id');
		
	    
	    $user_id =$this->session->userdata['logged_in']['user_id'];		
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "tech_support_ticket_progress";
			$data = array('ticket_id'=>$ticket_id,
			'remarks'=>'Please update the progess on the task',
			'added_on'=>$date,
			'added_by'=>$user_id,
            'level_flag'=>1);
			
			
		$result  = $this->master->insert_record($table,$data);		
		if($result)
		{
			
			$query = $this->db->select('a.*, b.ticket_id,b.ticket as support_ticket, c.user_id, c.first_name as updated_by_fname, c.last_name as updated_by_lname,c.email')->from('tech_support_ticket_progress a')->join('tech_support_ticket b','b.ticket_id=a.id','left')->join('system_users c','a.added_by=c.user_id','left')->where('a.ticket_id',$ticket_id)->get();
			foreach($query->result() as $ticket_info);
			    $this->db->select('email,name')->from('support_email_option')->where('support_option',1);
             $query = $this->db->get();
			 $res = $query->row_array();
	
             $toemail = $res['email'];
             $toname = $res['name'];

			

			 $this->db->select('email')->from('support_email_options')->where('support_option',1);
             $query = $this->db->get();
			 $res = $query->result_array();
			 foreach($res as $element){
             $ccemail[] = $element['email'];

			 }
			 $ccemails = implode(',',$ccemail);
			$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
				   <tr>
					<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="http://crm.packingtest.com/assets/images/logo-1.png" width="200px;" alt="Prestogroup" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>TECH SUPPORT REQUEST</strong><br></td>
					  </tr> 
					  <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>Ticket ID #'.$ticket_info->support_ticket.'</strong><br><br></td>
					  </tr> 
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;">'.$ticket_info->updated_by_fname.' '.$ticket_info->updated_by_lname.' has updated the remarks on raised ticket.</td>
					  </tr>
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					  
					  
					  <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Remarks </strong> : '.$data['remarks'].'</td>
					  </tr>
					 
					  
					 
					 <tr>
						<td>Warm Regards,<br> '.$ticket_info->updated_by_fname.' '.$ticket_info->updated_by_lname.'</td>
					  </tr>
					  <tr>
						<td>
					   </td>
					  </tr>
					</table>
					</td>
				  </tr>
				</table>';
				//echo $Message; exit;
				$subjectname = "Tech Support Request ".$ticket_info->support_ticket;
					$this->email->set_mailtype("html");
					$this->email->to($toemail);
					$this->email->cc($ccemails);
					$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('mitr@prestomitr.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();
					
					
			       $json['success'] = '<span class="alert alert-success">Level 1 query sent</span>';
                   header('Content-Type: application/json');
                   echo json_encode($json);
			
		
		
		
	}
		}


		public function sendleveltwo()
	    {   
	    $json = array();
		$ticket_id = $this->input->post('ticket_id');
	    $user_id =$this->session->userdata['logged_in']['user_id'];		
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "tech_support_ticket_progress";
			$data = array('ticket_id'=>$ticket_id,
			'remarks'=>'Not received any response from the Tech Support. Please look into this',
			'added_on'=>$date,
			'added_by'=>$user_id,
            'level_flag'=>2);
			
			
		$result  = $this->master->insert_record($table,$data);		
		if($result)
		{
			
			$query = $this->db->select('a.*, b.ticket_id,b.ticket as support_ticket, c.user_id, c.first_name as updated_by_fname, c.last_name as updated_by_lname,c.email')->from('tech_support_ticket_progress a')->join('tech_support_ticket b','b.ticket_id=a.id','left')->join('system_users c','a.added_by=c.user_id','left')->where('a.ticket_id',$ticket_id)->get();
			foreach($query->result() as $ticket_info);
			    $this->db->select('email')->from('support_email_option')->where('support_option',1);
             $query = $this->db->get();
			 $res = $query->row_array();
	
             $toemail = $res['email'];

			

			 $this->db->select('email')->from('support_email_options')->where('support_option',1);
             $query = $this->db->get();
			 $res = $query->result_array();
			 foreach($res as $element){
             $ccemail[] = $element['email'];

			 }
			 $ccemails = implode(',',$ccemail);
			$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
				   <tr>
					<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="http://crm.packingtest.com/assets/images/logo-1.png" width="200px;" alt="Prestogroup" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>TECH SUPPORT REQUEST</strong><br></td>
					  </tr> 
					  <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>Ticket ID #'.$ticket_info->support_ticket.'</strong><br><br></td>
					  </tr> 
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;">'.$ticket_info->updated_by_fname.' '.$ticket_info->updated_by_lname.' has updated the remarks on raised ticket.</td>
					  </tr>
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					  
					  
					  <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Remarks </strong> : '.$data['remarks'].'</td>
					  </tr>
					 
					  
					 
					 <tr>
						<td>Warm Regards,<br> '.$ticket_info->updated_by_fname.' '.$ticket_info->updated_by_lname.'</td>
					  </tr>
					  <tr>
						<td>
					   </td>
					  </tr>
					</table>
					</td>
				  </tr>
				</table>';
			
				$subjectname = "Tech Support Request ".$ticket_info->support_ticket;
					$this->email->set_mailtype("html");
					$this->email->to('gaurav@prestogroup.com,vishal@prestogroup.com');
					$this->email->cc($toemail);
					$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('mitr@prestomitr.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();
					
					
			       $json['success'] = '<span class="alert alert-success">Level 2 query sent</span>';
                   header('Content-Type: application/json');
                   echo json_encode($json);
			
		
		
		
	}
		}


		
public function closed_ticket_list(){
    $this->load->view('techsupport/closed_ticket_list');
}	
	
	public function Tech_closed_ticket_list()
	{
		$i=1;
		$support_ticket = array();
			$this->db->select('a.*,b.department_id, b.department, c.user_id, c.first_name, c.last_name,d.user_id, d.first_name as closed_by_fname, d.last_name as closed_by_lname, e.type_id, e.asset_type as items')->from('tech_support_ticket a')->join('departments b','a.location_id=b.department_id','left')->join('system_users c','a.ticket_raised_by=c.user_id','left')->join('system_users d','a.updated_by=d.user_id','left')->join('asset_type e','a.item_name=e.type_id','left');
		$this->db->where('a.status','1');
		$this->db->order_by('a.added_on','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$status = "<a href='".page_url."Tech_support/update_ticket_status/".$row->ticket_id."/".$row->status."'><span class='btn btn-success btn-xs'>Reopen</span></a>";
				//$status = "<span class='btn btn-success btn-xs'>Closed</span>";
				$updateddate = date('Y-m-d', strtotime($row->updated_on));
				$time = date('H:i:s', strtotime($row->updated_on));
				$updatedtime = "<br>". date('g:i A', strtotime($time)); 
				$sta = $status."<br>"."This ticket has been closed by <strong>".$row->closed_by_fname." ".$row->closed_by_lname."</strong><br> at ".$updateddate." ".$updatedtime;
				
			}else
			{
				$sta ="<a href='".page_url."Tech_support/update_ticket_status/".$row->ticket_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Pending</span></a>";
			}
			$history = "<a href='".page_url."Tech_support/view_history/".$row->ticket_id."'><span class='btn btn-warning btn-xs'><i class='fa fa-eye'></i>&nbsp; Add /See Comments</span></a>";
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
			'location'=>$row->department,
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
	
	public function closed_ticket_list_by_department()
	{
		$query = $this->db->select('user_id, department_id')->from('system_users')->where('department_id',$this->uri->segment(3))->get();
		$res = $query->result();
		foreach($res as $departmentdata)
		$user_id[] = $departmentdata->user_id;
		$i=1;
		$support_ticket = array();
			$this->db->select('a.*,b.department_id, b.department, c.user_id, c.first_name, c.last_name,d.user_id, d.first_name as closed_by_fname, d.last_name as closed_by_lname, e.type_id, e.asset_type as items')->from('tech_support_ticket a')->join('departments b','a.location_id=b.department_id','left')->join('system_users c','a.ticket_raised_by=c.user_id','left')->join('system_users d','a.updated_by=d.user_id','left')->join('asset_type e','a.item_name=e.type_id','left');
			$this->db->where('a.status','1');
		$this->db->where_in('a.added_by',$user_id);
		$this->db->order_by('a.added_on','desc');
		$query = $this->db->get();
		$res = $query->result();
	foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$status = "<a href='".page_url."Tech_support/update_ticket_status/".$row->ticket_id."/".$row->status."'><span class='btn btn-success btn-xs'>Reopen</span></a>";
				//$status = "<span class='btn btn-success btn-xs'>Closed</span>";
				$updateddate = date('Y-m-d', strtotime($row->updated_on));
				$time = date('H:i:s', strtotime($row->updated_on));
				$updatedtime = "<br>". date('g:i A', strtotime($time)); 
				$sta = $status."<br>"."This ticket has been closed by <strong>".$row->closed_by_fname." ".$row->closed_by_lname."</strong><br> at ".$updateddate." ".$updatedtime;
				
			}else
			{
				$sta ="<a href='".page_url."Tech_support/update_ticket_status/".$row->ticket_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Pending</span></a>";
			}
			$history = "<a href='".page_url."Tech_support/view_history/".$row->ticket_id."'><span class='btn btn-warning btn-xs'><i class='fa fa-eye'></i>&nbsp; Add /See Comments</span></a>";
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
			'location'=>$row->department,
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
}
