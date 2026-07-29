<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
// 		 "hi";exit;
		$this->load->library('user_agent'); 
		if($this->agent->is_browser('Internet Explorer')) { echo "<h1 style='text-align:center;'>PMS can not be accessed via Internet Explorer. Please use some other browser. </h1>"; exit;  }else{
		    if(strpos($_SERVER['HTTP_USER_AGENT'],'Edg')){
		        echo "<h1 style='text-align:center;'>PMS can not be accessed via Microsoft Edge. Please use some other browser. </h1>"; exit;
		    }
		}
		
$this->load->model('User_model','user');
$this->load->model('Store_model');
$this->load->library('Master_profile_guard');
$this->master_profile_guard->block_methods(
	array('user_permission', 'edit_permission', 'access_user_dashboard'),
	'This EA profile cannot access permission setup or master administration shortcuts.'
);
$config = array(
		'protocol' => 'smtp', 
		'smtp_host' => 'smtp.logix.in',
		'smtp_port' => 465,   	
		'smtp_user' => 'taskmanagement@shubhampack.com', 
		'smtp_pass' => 'ficihlqnfcdrrqkb', 
		'mailtype' => 'html', 
		'charset' => 'iso-8859-1',
		'newline'=>"\r\n",
		'starttls'=>TRUE);
		$this->email->initialize($config);
		$this->email->set_mailtype("html");
		
	}
		public function registration()
	{
	   // $this->user->send_notification();
	    //$this->user->reminder_on_time_period();
		$this->load->view('users/registration');
	}
    public function addregistration()
	{
	   $this->form_validation->set_rules('email', 'Email ID', 'required|trim');
		$this->form_validation->set_rules('person_name', 'Person Name', 'required|trim');
		$this->form_validation->set_rules('company_name', 'Company Name', 'required|trim');
		$this->form_validation->set_rules('contact_number', 'Contact Number', 'required|trim');
		//$this->form_validation->set_rules('password', 'Password', 'required|trim');
		//$this->form_validation->set_rules('logo', 'Logo', 'required|trim');
		//$this->form_validation->set_rules('address', 'Address', 'required|trim');
		$this->form_validation->set_error_delimiters('<div style="color:green;">', '</div>');
		
		if ($this->form_validation->run() == FALSE)
		{
		$this->load->view('users/registration');
		}else
		{
		    
			$username = $this->input->post('email');
			//echo $username; exit;
			$query=$this->db->select('id')->from('company_information')->where('email',$username)->get();
			//$result=$query->num_rows();
			
	    	if($query->num_rows() >0)
			{
			    $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000; float-left:20px;"><span style="color:red; float-left:20px;">This Email/Username name already Exited</span></div><br/>');
				 redirect(page_url.'User/registration');
			}
			else
			{
			    
			$photo=$_FILES['logo']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$screenshot=time().'.'.$cat_image;
				move_uploaded_file($_FILES["logo"]["tmp_name"],UPLOADPATH.'registration/' . $screenshot);
			}else
			{
				$screenshot='';
			}
				$data = array();
                 $data = array('company_name' => $this->input->post('company_name'),
				     'person_name' => $this->input->post('person_name'),
				     'contact_number' => $this->input->post('contact_number'),
					 'email'=>$this->input->post('email'),
					 'password'=>$this->input->post('password'),
					 'logo'=>$screenshot,
					 'address'=>$this->input->post('address'),
					 'config_mail'=>$this->input->post('configemail'),
					 'config_password'=>$this->input->post('configpassword'),
					  'smtp_host'=>$this->input->post('smtp'),
					  'port_number'=>$this->input->post('port'),
					  'auth_key'=>$this->input->post('auth'),
					  'senderid'=>$this->input->post('sender'),
					  'sms_provider_name'=>$this->input->post('smsname'),
					  'colorcode'=>$this->input->post('colorcode'),
					  'created_on'=>date('Y-m-d h:m:s'));
					$this->db->insert('company_information',$data);
             
				
			 $this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;"><span style="color:red; float-left:20px;">Your Registration sucessfully Accept. Thank you</span></div><br/>');
			 redirect(page_url.'User/registration');
			
			}
		}
	}
		public function edit_registration()
	{
		$this->load->view('users/edit_registration');
	}
	public function updateregistration()
	{
	   $this->form_validation->set_rules('email', 'Email ID', 'required|trim');
		$this->form_validation->set_rules('person_name', 'Person Name', 'required|trim');
		$this->form_validation->set_rules('company_name', 'Company Name', 'required|trim');
		$this->form_validation->set_rules('contact_number', 'Contact Number', 'required|trim');
		//$this->form_validation->set_rules('password', 'Password', 'required|trim');
		//$this->form_validation->set_rules('logo', 'Logo', 'required|trim');
		//$this->form_validation->set_rules('address', 'Address', 'required|trim');
		$this->form_validation->set_error_delimiters('<div style="color:green;">', '</div>');
		
		if ($this->form_validation->run() == FALSE)
		{
		$this->load->view('users/edit_registration');
		}else
		{
		    
			$username = $this->input->post('email');
			$olglogo = $this->input->post('oldimage');
			//echo $username; exit;
			//$query=$this->db->select('id')->from('company_information')->where('email',$username)->get();
			//$result=$query->num_rows();
			
	    	/*if($query->num_rows() >0)
			{
			    $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000; float-left:20px;"><span style="color:red; float-left:20px;">This Email/Username name already Exited</span></div><br/>');
				 redirect(page_url.'User/registration');
			}
			else
			{*/
			    
			$photo=$_FILES['logo']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$screenshot=time().'.'.$cat_image;
				move_uploaded_file($_FILES["logo"]["tmp_name"],UPLOADPATH.'registration/' . $screenshot);
			}else
			{
				$screenshot=$olglogo;
			}
				$data = array();
                 $data = array('company_name' => $this->input->post('company_name'),
				     'person_name' => $this->input->post('person_name'),
				     'contact_number' => $this->input->post('contact_number'),
					 'email'=>$this->input->post('email'),
					 'password'=>$this->input->post('password'),
					 'logo'=>$screenshot,
					 'address'=>$this->input->post('address'),
					 'config_mail'=>$this->input->post('configemail'),
					 'config_password'=>$this->input->post('configpassword'),
					  'smtp_host'=>$this->input->post('smtp'),
					  'port_number'=>$this->input->post('port'),
					  'auth_key'=>$this->input->post('auth'),
					  'senderid'=>$this->input->post('sender'),
					  'sms_provider_name'=>$this->input->post('smsname'),
					  'colorcode'=>$this->input->post('colorcode'),
					  'updated_on'=>date('Y-m-d h:m:s'));
					  $this->db->where('id','1');
					$this->db->update('company_information',$data);
             
				
			 $this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;"><span style="color:red; float-left:20px;">Your data updated sucessfully. Thank you</span></div><br/>');
			 redirect(page_url.'User/edit_registration');
			
			
		}
	}

	public function index()
	{
	   // $this->user->send_notification();
	    //$this->user->reminder_on_time_period();
		$this->load->view('users/login');
	}
	
	public function authenticate()
	{
		
		$this->form_validation->set_rules('email', 'Username', 'required|trim');
		$this->form_validation->set_rules('password', 'Password', 'required|trim');
		$this->form_validation->set_error_delimiters('<div style="color:green;">', '</div>');
		
		if ($this->form_validation->run() == FALSE)
		{
		$this->load->view('login');
		}else
		{
			$ip = $_SERVER["REMOTE_ADDR"];
			$username = $this->input->post('email');
			$password = $this->input->post('password');
			$query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
		    
		     $result=$this->user->authentication($username,$password);
	    	if($result)
			{
				$data = array();
             foreach($result as $row) {
                 
                 
                 $ip = $_SERVER["REMOTE_ADDR"];
                 
                 $ip_data = array('user_id'=>$row->user_id,
				    'login_time'=>date('h:i:s'),
				    'login_date'=>date('Y-m-d'),
				    'ip_address'=>$ip,
				    'added_on'=>date('Y-m-d h:i:s'));
			        $this->db->insert('user_login_ip_tracking',$ip_data);
                 
				 
                 $data = array('user_id' => $row->user_id,
				     'user_name' => $row->first_name,
				     'last_name' => $row->last_name,
					 'email'=>$row->email,
					 'role'=>$row->user_role_id,
					 'employeecode'=>$row->employeecode,
					 'business_location'=>$row->business_location,
					 'department_id'=>$row->department_id,
					  'profile_image'=>$row->profile_image,
					  'smtpemailid'=>$row->smtp_email,
					  'dynachem_id'=>$row->dynachem_id,
					  'smtppassword'=>$row->smtp_password,
                     'user_status'=>$row->user_status);
					$this->session->set_userdata('logged_in',$data);
					
					
			    /** CHECK IF ITS LOGGED IN BY ADMIN THEN WHITELIST THE IP **/
				
				if($row->user_role_id=='1')
				{

					$isavailable=$this->checkforipaddress($ip);
				    if($isavailable==0)
				    {
				        $ipdata=array('ipaddress'=>$ip,'added_on'=>date('Y-m-d H:i:s'),'added_by'=>$row->user_id);
				        
				        $this->db->insert('ipblocking',$ipdata);
				        
				    }
				    
				}
					
					
				/** END **/
					
			 }
			
			
				
				
			 //$employee_ID=$row->employee_ID;
			 redirect(page_url.'Dashboard');
			
			}
			else
			{
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000; float-left:20px;"><span style="color:red; float-left:20px;">Invalid username/password combination</span></div><br/>');
				 redirect(page_url);	
			}  
		    
			
			
			
		}
	}
	
	 function signout()
	{
	   	$user_id=$this->session->userdata['logged_in']['user_id'];
		if($user_id){
		$user_id=$this->session->userdata['logged_in']['user_id'];
		$user_name=$this->session->userdata['logged_in']['user_name'];
		 $email=$this->session->userdata['logged_in']['email'];
		  $business_location=$this->session->userdata['logged_in']['business_location'];
        $status=$this->session->userdata['logged_in']['user_status'];
         $department=$this->session->userdata['logged_in']['department_id'];
          $role=$this->session->userdata['logged_in']['role'];
		$log_array = array('user_id' => $user_id, 'user_name' =>$user_name,'role'=>$role,'email'=>$email,'status'=>$status,'business_location'=>$business_location,'department'=>$department);
		$this->session->unset_userdata($log_array);
        $this->session->sess_destroy();
        
		redirect(page_url);
		}else{
		 redirect(page_url);   
		}
	}
	
public function forgot_password()
	{
		$this->form_validation->set_rules('email', 'Email', 'trim|required');		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');		if ($this->form_validation->run() == FALSE)		{				
			$this->load->view('users/forgot-password');
		}else
		{			
			$email_ID = $this->input->post('email');			
		$query = $this->db->select('first_name, last_name,email,password')->from('system_users')->where('email',$email_ID)->get();				$result = $query->result();			if($result)			{			
			foreach($result as $userinfo)			
			$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">			<tr>				
		<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://gamavis.com/PMS/assets/images/shubhampack.png" width="200px;" alt="Shubham Pack" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>Password Recovery Request</strong><br></td>
					  </tr> 
					  <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br>Dear, '.$userinfo->first_name.' '.$userinfo->last_name.' Please find your login detail. </td>
					  </tr> 
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Email -  </strong> : '.$userinfo->email.'</td>
					  </tr>
					   <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Password -  </strong> : '.$userinfo->password.'</td>
					  </tr>
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					 
					<tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;;">Best Regards, <br>
						Prestogroup Team
						</td>
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
				$subjectname = "Password Recovery Request ";
					$this->email->set_mailtype("html");
					$this->email->to($userinfo->email);
					$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('taskmanagement@shubhampack.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();		 
			$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank You, Your Password has been sent on your registered email. </span><br/>');
				 redirect(page_url);
			}
			else
			{
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Email-ID is not exist in the system</span><br/>');
				redirect(page_url);	
			}			
			
		}
		
	}
	
	public function user_permission()
		{
			$this->ensure_service_payment_permission_submodules();
		
			$this->load->view('master/set_permission');
			
		}
		
		function edit_permission()
		{
			$this->ensure_service_payment_permission_submodules();
			$this->load->view('master/edit_permission');
		}

	private function ensure_service_payment_permission_submodules()
	{
		$this->ensure_permission_submodule(17, 'SERVICE PAYMENT REQUESTS');
		$this->ensure_permission_submodule(17, 'SERVICE PAYMENT APPROVALS');
	}

	private function ensure_permission_submodule($module_id, $submodule_name)
	{
		$existing = $this->db->select('id')
			->from('submodule')
			->where('moduleid', (int) $module_id)
			->where('submodule', (string) $submodule_name)
			->limit(1)
			->get()
			->row();

		if (!empty($existing)) {
			return (int) $existing->id;
		}

		$insert_data = array(
			'moduleid' => (int) $module_id,
			'submodule' => (string) $submodule_name,
			'status' => 1,
			'addedOn' => date('Y-m-d H:i:s')
		);

		if ($this->db->field_exists('dynachem', 'submodule')) {
			$insert_data['dynachem'] = 0;
		}

		if ($this->db->field_exists('shubhampack', 'submodule')) {
			$insert_data['shubhampack'] = 2;
		}

		$this->db->insert('submodule', $insert_data);

		return (int) $this->db->insert_id();
	}
		
		public function view_profile(){
		$this->load->view('users/profile');
	}
	public function IT_item_list()
	{
		$i=1;
		$tech_item_data = array();
		$this->db->select('a.asset_id, a.business_location_id, a.department_id, a.asset_type ,a.computer_name,a.added_by,a.purchase_date,b.location_id, b.installed_location,d.business_loc_id, d.city_id, d.company_name, e.user_id, e.first_name, e.last_name,c.type_id,c.asset_type as asset_name,f.brand_id,f.brand_name,g.city_id, g.city_name')->from('presto_it_assets a')->join('installed_location b','a.department_id=b.location_id','left')->join('business_location d','a.business_location_id=d.business_loc_id','left')->join('asset_type c','a.asset_type=c.type_id','left')->join('system_users e','e.user_id=a.added_by','left')->join('asset_brands f','a.brand_id=f.brand_id','left')->join('cities g','d.city_id=g.city_id','left')->where('a.assigned_to',$this->uri->segment(3));
		$this->db->order_by('c.asset_type','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			 if($row->purchase_date=='0000-00-00'){
			     $purchase_Date = "";
			 }else{ $purchase_Date =  date('d-m-Y',strtotime($row->purchase_date));
			     
			 }									
			
			$edit = "<a href='".page_url."IT_Assets/edit_item/".$row->asset_id."'><i class='fa fa-pencil'></i></a>";
			$view = "<a href='".page_url."IT_Assets/view_item_detail/".$row->asset_id."'><span class='btn btn-warning btn-xs'>View Detail</span></a>";			
			$tech_item_data[] = array('sr_no'=>$i,
			'business_location'=>$row->company_name."<br>".$row->city_name,
			'location'=>$row->installed_location,
			'asset_type'=>$row->asset_name,
			'brand_name'=>$row->brand_name,
			'purchase_date'=>$purchase_Date,
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
		

	public function delegation_response()
	{
		
		$this->form_validation->set_rules('user_response', 'user_response', 'required|trim');
		$this->form_validation->set_error_delimiters('<div style="color:green;">', '</div>');
		
		if ($this->form_validation->run() == FALSE)
		{
		$this->load->view('users/delegation_response');
		}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
			$data = array('task_id'=>$this->uri->segment(3),
			'user_response'=>strtoupper($this->input->post('user_response')),
			'updated_on'=>$added_time);
			$res = $this->db->insert('user_response_on_delegated_task',$data);
			if($res)
			{
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank You! your response successfully added.</span></div><br/>');
            redirect(page_url."User/delegation_response/".$this->uri->segment(3));	
			
			}
			else
			{
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000; float-left:20px;">Please try again.</span></div><br/>');
				 redirect(page_url."User/delegation_response/".$this->uri->segment(3));	
			}
			
		}
	}	
	
	public function update_profile()
	{

		$photo=$_FILES['profile']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$finaldata=time().'.'.$cat_image;
				move_uploaded_file($_FILES['profile']["tmp_name"],UPLOADPATH.'users/' . $finaldata);
			}else
			{
				$finaldata="";
				}
				
			$data = array('profile_image'=>$finaldata);
			$this->db->where('user_id',$this->uri->segment(3));
		$result  = $this->db->update('system_users',$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, Your profile picture succesfully updated.</div>');
			redirect(page_url.'Dashboard');
			
		}
		
	}
	
	/*
		if($username=='mangleshup@gmail.com' && $password=='upadhyay'){
			    	$result=$this->user->authentication($username,$password);  
			}else if($ip=='122.176.39.101' || $ip=='103.95.83.199' || $ip=='122.176.16.104' || '1.38.48.56'){
			    	$result=$this->user->authentication($username,$password);  
			}else if($ip=='122.176.39.102' || $ip=='103.95.83.199' || $ip=='122.176.16.104' || '1.38.48.56'){
				$result=$this->user->authentication($username,$password);
			}else{
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000; float-left:20px;">Sorry! You can not login your account with this IP Address '.$ip.'</span></div><br/>');
				 redirect(page_url);
				
				
			}
	*/
	
		public function access_user_dashboard()
    	{
		
	    	$ip = $_SERVER["REMOTE_ADDR"];
			$userid = $this->uri->segment(3);
			$query = $this->db->select('user_id, first_name, last_name, email, user_role_id, business_location, department_id, profile_image, user_status, smtp_email, smtp_password, dynachem_id')->from('system_users')->where('user_id',$userid)->get();
			
			$result=$query->result();
		
			if($result)
			{
				$data = array();
             foreach($result as $row) {
				 
                 $data = array('user_id' => $row->user_id,
				     'user_name' => $row->first_name,
				     'last_name' => $row->last_name,
					 'email'=>$row->email,
					 'role'=>$row->user_role_id,
					 'business_location'=>$row->business_location,
					 'department_id'=>$row->department_id,
					  'profile_image'=>$row->profile_image,
					  'smtpemailid'=>$row->smtp_email,
					  'dynachem_id'=>$row->dynachem_id,
					  'smtppassword'=>$row->smtp_password,
                     'user_status'=>$row->user_status);
					$this->session->set_userdata('logged_in',$data);
					
			 }
				
			//echo "<pre>"; print_r($_SESSION); exit;
			 redirect(page_url.'Dashboard');
			
			}
		
			
		
	}
	
	public function edit_home_image(){
	    $this->load->view('master/edit_background_image.php');
	}
	
	public function update_background_image(){
	    
	     date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
           $user_id =$this->session->userdata['logged_in']['user_id'];	
	     $photo=$_FILES['homepageimg']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$screenshot=time().'.'.$cat_image;
				move_uploaded_file($_FILES["homepageimg"]["tmp_name"],UPLOADPATH.'home/' . $screenshot);
			}else
			{
				$screenshot=$this->input->post('oldimage');
				}	
				
			$data = array('image'=>$screenshot,
			'added_on'=>$added_time,
			'added_by'=>$user_id);
			$this->db->where('id',$this->uri->segment(3));
			$this->db->update('loginpage_image',$data);
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully updated.</span></div><br/>');
            redirect(page_url.'User/edit_home_image');
	}
	
	public function Login_ip_tracking(){
$this->load->view('master/login_ip_tracking');	
}

public function All_login_activity()
	{
		$user_data = array();
		$this->db->select('a.*,b.user_id, b.first_name, b.last_name')->from('user_login_ip_tracking a');
		$this->db->join('system_users b','a.user_id=b.user_id','left');
		$this->db->order_by('a.login_date','desc');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		    
		    if($row->login_time){
		    $logintime = date('h:i A',strtotime($row->login_time));
		    }else{
		     $logintime="";   
		    }
		    
			$user_data[] = array('sr_no'=>$i,
			'user_name'=>$row->first_name." ".$row->last_name,
			'date'=>$row->login_date,
			'login_time'=>$logintime,'ip_Address'=>$row->ip_address);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($user_data),
			"iTotalDisplayRecords" => count($user_data),
			"aaData"=>$user_data);
			
		echo json_encode($results);
	}
	
		public function edit_top_image(){
	    $this->load->view('master/edit_top_image.php');
	}
	
	public function update_top_image(){
	    
	     date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
           $user_id =$this->session->userdata['logged_in']['user_id'];	
	     $photo=$_FILES['homepageimg']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$screenshot=time().'.'.$cat_image;
				move_uploaded_file($_FILES["homepageimg"]["tmp_name"],UPLOADPATH.'home/' . $screenshot);
			}else
			{
				$screenshot=$this->input->post('oldimage');
				}	
				
			$data = array('image'=>$screenshot,
			'added_on'=>$added_time,
			'added_by'=>$user_id);
			$this->db->where('id',$this->uri->segment(3));
			$this->db->update('top_image',$data);
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully updated.</span></div><br/>');
            redirect(page_url.'User/edit_home_image');
	}

public function add_ip(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('ipaddress', 'ipaddress', 'required|trim');
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('users/ipaddress.php');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		  
		   
		   $data=
			array('ipaddress'=>strtoupper($this->input->post('ipaddress')),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			
			$res = $this->db->insert('ipblocking',$data);
			$last_id = $this->db->insert_id();
			if($res)
			{
				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
				redirect(page_url.'User/add_ip');
				}
		   
			
			}
	}
	
	public function ipaddress_list()
	{
		$scheduler_data = array();
		$query = $this->db->select('*')->from('ipblocking')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			
			$delete = "<a href='".page_url."User/delete_ip/".$row->id."'><i class='fa fa-trash'></i></a>";
			
			$scheduler_data[] = array('sr_no'=>$i,
			'ipaddress'=>$row->ipaddress,
			'delete'=>$delete);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}

function delete_ip(){
    $this->db->where('id',$this->uri->segment(3));
    $this->db->delete('ipblocking');
    	$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully deleted.</span></div><br/>');
				redirect(page_url.'User/add_ip');
}


function checkforipaddress($ip)
{
    $resty=$this->db->select('id')->from('ipblocking')->where('ipaddress',$ip)->get();
    
    return $resty->num_rows();
    
    
}

public function indent_form(){
    $this->load->view('users/indent_form');
}

	function saveindent()
{
	
	$type=$this->input->post('type');
	$itemname=$this->input->post('instruments');
	$employeeid = $this->input->post('employeecode');
	$remarks  = $this->input->post('remarks');
	$qty=$this->input->post('qty');
	
	$q = $this->db->select('employee_id')->from('employee_code_management')->where('employee_id',$employeeid)->get();
	if($q->num_rows()>0){
	
	/** Check for any previous intend**/
            $inde=$this->db->select('indendno')->from('intend_request')->order_by('id','DESC')->limit(1)->get();
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
		'addedBy'=>'0');
		
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
	redirect(page_url.'User/indent_form');
	}else{
	   $this->session->set_flashdata('message','<div class="alert alert-info">Sorry! Your Employee code is wrong please try again.</div>');
	redirect(page_url.'User/indent_form'); 
	}
	
}

function generateprfromintendautoapproval($indentno)
{
	$prnos=$this->db->select('prno')->from('purchase_request')->order_by('id','desc')->limit(1)->get();
		$num=$prnos->num_rows();
		if($num==0)
		{
		$num1=1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPR'.$num_padded;
		}else{
		    foreach($prnos->result() as $prnos1);
		    $str=$prnos1->prno;
		    $num = (int) filter_var($str, FILTER_SANITIZE_NUMBER_INT);
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
			
			$data=array('masterid'=>$resty1->itemid,'type'=>$indenttype,'itemid'=>$resty1->itemid,'source'=>'2','sourceid'=>$indentno,'prno'=>$code,'qty'=>$resty1->qty,'unit'=>$resty1->unit,'addedBy'=>'0','addedOn'=>date('Y-m-d H:i:s'));
			
			$this->db->insert('purchase_request',$data);
			
		}
		
		/** UPDATE INTEND APPROVAL **/
		$datau=array('approvalstatus'=>'1','approvedOn'=>date('Y-m-d H:i:s'),'approvedBy'=>'0','pr_status'=>'1');
		$this->db->where('indendno',$indentno);
		$this->db->update('intend_request',$datau);
		/** END **/
		
	}else{
		
		echo "INDEND NOT AVAILABLE";exit;
	}
	
}




function issuejobcard()
{
	$this->load->view('store/searchforjobcardforissue');
}

function getopenjobcardlist()
{
	$searchtrm= $_GET['q'];
	$rest=$this->db->select('a.jobcardid,b.job_card_no')->from('blockedstock a')->join('order_instruments b','a.jobcardid=b.id')->like('b.job_card_no',$searchtrm,'both',false)->where('a.active','1')->where('a.issued','0')->group_by('a.jobcardid')->get();
	
	if($rest->num_rows()>0)
	{
	foreach($rest->result() as $instruments){

	$json[] = array('id'=>$instruments->jobcardid, 'text'=>$instruments->job_card_no);

	}
	}else{

	$json[] = array('id'=>"", 'text'=>"No Data Available");

	}

	echo json_encode($json);

}


function issueblockeditems()
{
	$this->load->view('store/jobcarditemissueforopen');
}

function searchjobcard()
{
	$code=trim($this->input->post('code'));
	$typess=trim($this->input->post('type'));
	
	
	$restyru=$this->checkforvalidcode($code);
	
	if(count($restyru)==0)
	{
		$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Invalid Employee Code</span></div><br/>');
		
		redirect(page_url.'User/issuejobcard');
	}else
	{
		$userid=$restyru['userid'];
		$type=$restyru['type'];
		if($typess=='1')
		{
		$jobcard=trim($this->input->post('jobcard'));
		redirect(page_url.'User/issueblockeditems/'.$jobcard.'/'.$userid.'/'.$type);
		}else if($typess=='2'){
			
		redirect(page_url.'User/othermachineitemissue/'.$userid.'/'.$type);
			
		}else if($typess=='3'){
			
			redirect(page_url.'User/generalitemissue/'.$userid.'/'.$type);
		}
		
	}
	
}


function checkforvalidcode($code)
{
$resteyre=array();
$quwery=$this->db->select('user_id')->from('system_users')->where('employeecode',$code)->get();	
if($quwery->num_rows()>0)
{
	foreach($quwery->result() as $quwery1);
	$resteyre['userid']=$quwery1->user_id;
	$resteyre['type']='1';
}else{
	
	$quwery=$this->db->select('id')->from('prestogroup_employees')->where('employee_code',$code)->get();	
if($quwery->num_rows()>0)
{
	foreach($quwery->result() as $quwery1);
	$resteyre['userid']=$quwery1->id;
	$resteyre['type']='2';
}
	
}

return $resteyre; 	
	
}


function issueblockedqty()
{
	//echo "h";exit;
$cat=$this->uri->segment(3);
$userid=$this->uri->segment(4);
$usertype=$this->uri->segment(5);
if($cat<>'' && $userid<>'' && $usertype<>'')
{

$itemid=$this->input->post('checkit'.$cat);
$jobcardid=$this->input->post('jobcardsid');
$issuesession=time();
for($i=0;$i<count($itemid);$i++)
{
	$itemids=$itemid[$i];
	
	$blocked=$this->input->post('blockedstockid'.$cat.$itemids);
	$reqstock=$this->input->post('reqstock'.$cat.$itemids);
	$blockedid=$blocked;
	
	$reqst=$reqstock;
	$isqty=$this->input->post('issueqty'.$itemids);
	
	$issueto=$this->input->post('issuetos'.$cat);

	
	$data=array('itemtype'=>'1','issuetype'=>'1','blockedid'=>$blockedid,'itemid'=>$itemids,'stock'=>$isqty,'issuedto'=>$issueto,'issuedOn'=>date('Y-m-d H:i:s'),'jobcardid'=>$jobcardid,'usertype'=>$usertype,'issuesession'=>$issuesession);
	$this->db->insert('issuestocktousers',$data);
	

	if($reqst==$isqty)
	{
	  
		$data1=array('active'=>'0','issued'=>'1','updatedOn'=>date('Y-m-d H:i:s'));
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

$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Selected Item Issued</span></div><br/>');

redirect(page_url.'User/issueblockeditems/'.$jobcardid.'/'.$userid.'/'.$usertype);

}else{
	
	echo "INVALID REQUEST PLEASE TRY AGAIN"; exit;
}
	
	
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


function othermachineitemissue()
{

	$this->load->view('store/consumable_item_issue');
	
	
}




	public function consumable_listforissue()
	{
		$i=1;
		$machinepart_data= array();
		$this->db->select('a.id,a.gst,a.part,a.unit,a.current_stock,a.min_stock, a.specification, a.makes, a.size_in_mm, a.material, a.raw_bop, a.fincode, a.qty, a.picture, d.rack_location, p.category')->from('machine_parts_with_picture  a')->join('store_rack_location d','a.location_id=d.id','left')->join('presto_machine_part_category p','a.category_id=p.id','left');
	
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row){
	
		/** ISSUE ITEMS **/
		$restblockedst=$this->db->select('sum(stock) as blockstock')->from('blockedstock')->where('itemid',$row->id)->where('active','1')->where('issued','0')->get();
		if($restblockedst->num_rows()>0)
		{
		foreach($restblockedst->result() as $restblockedstock);
		
		if($restblockedstock->blockstock<>'')
		{
		$blockedparts=floatval($restblockedstock->blockstock);
		}else{
		$blockedparts=0;
		}
		}else{ 
		$blockedparts=0;  
		}

		if($row->current_stock==0)
		{
		$curstock=0;
		}else{
		$curstock=$row->current_stock-$blockedparts;
		}
	
	
	$curstock=$row->current_stock;
		$unitname=$this->getreturnunit($row->unit);

		//$isss="<a href='".page_url."Store/issuemachineitems/".$row->id."' class='btn btn-warning btn-xs'>Issue Item</a>";
		if($curstock>0)
		{
		$isss="<input type='checkbox' name='checkitem[]' id='checkitem".$row->id."' value='".$row->id."' onchange='openqtybox(".$row->id.",".$i.")'>";
		}else{
			$isss="<span style='color:red;'>Stock Not Available</span>";
		}
	
		
		$qtybox='<input type="text" name="qtybox'.$row->id.'" class="form-control only-numeric qtyb'.$row->id.'" style="display:none" id="qtybox'.$row->id.$i.'" style="width:50%" placeholder="Enter Qty in '.$unitname.'" onkeyup="checkforqty('."'".$row->id."'".','."'".$i."'".');"><input type="hidden" name="currentstock'.$row->id.'" id="currentstock'.$row->id.$i.'" value="'.$curstock.'">';
		/** END **/
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
			
			
			//$vendor=$this->getreturnvendorandprice($row->id);
			
			
			$machinepart_data[] = array('sr_no'=>$i,
			'issue'=>$isss,
			'qtybox'=>$qtybox,
			'category'=>$row->category,
			'machine_part'=>$row->part,
			'specification'=>$row->specification,
			'fincode'=>$row->fincode,
			'unit'=>$unitname,
			'stock'=>$curstock,
			'location'=>$row->rack_location,
			'image'=>$image);
			$i++;
		}
			
			
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($machinepart_data),
			"iTotalDisplayRecords" => count($machinepart_data),
			"aaData"=>$machinepart_data);
			
		echo json_encode($results);
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


function consumableissue()
{

$items=$this->input->post('checkitem');
$usertype=$this->input->post('usertype');
$userid=$this->input->post('userid');

if(count($items)>0)
{
    $issuesession=time();
	
	for($i=0;$i<count($items);$i++)
	{
	    
		$itemid=$items[$i];
		$qty=$this->input->post('qtybox'.$itemid);
		
		$data=array('itemtype'=>'1','jobcardid'=>'0','issuetype'=>'2','itemid'=>$itemid,'stock'=>$qty,'issuedto'=>$userid,'usertype'=>$usertype,'issuedOn'=>date('Y-m-d H:i:s'),'issuesession'=>$issuesession);
		
		
		$this->db->insert('issuestocktousers',$data);
		if($this->db->affected_rows()>0)
		{
			
			$this->db->set('current_stock', 'current_stock-'.$qty, false);
			$this->db->where('id' , $itemid);
			$this->db->update('machine_parts_with_picture');
			
		
		}
		
		
	}
	
	        $this->session->set_flashdata('message','<div class="alert alert-danger">Item Issued</div>');
			redirect(page_url.'User/othermachineitemissue/'.$userid.'/'.$usertype);
	}else
	{
		
		$this->session->set_flashdata('message','<div class="alert alert-danger">Please Select Items to issue.</div>');
		redirect(page_url.'User/othermachineitemissue/'.$userid.'/'.$usertype);
		
		
	}

	
}


function allgeneralitems()
{
		$housekeeping_data= array();
		$this->db->select('a.*,b.category,c.shortname')->from('house_keeping_items a')->join('presto_machine_part_category b','a.category_id=b.id','left')->join('units c','a.unit=c.id','left')->order_by('a.item_name','ASC');
		$query = $this->db->get();
		$res = $query->result();
		if($query->num_rows()>0)
		{
			$i=1;
		foreach($res as $row){
			
			if($row->qty>0)
			{
				$isss="<input type='checkbox' name='checkitem[]' id='checkitem".$row->id."' value='".$row->id."' onchange='openqtybox(".$row->id.",".$i.")'>";
			}else{
				
				$isss="<span style='color:red'>Stock Not Available</span>";
				
			}
			
			
			$qtybox='<input type="text" name="qtybox'.$row->id.'" class="form-control only-numeric qtyb'.$row->id.'" style="display:none" id="qtybox'.$row->id.$i.'" style="width:50%" placeholder="Enter Qty in '.$row->shortname.'" onkeyup="checkforqty('."'".$row->id."'".','."'".$i."'".');"><input type="hidden" name="currentstock'.$row->id.'" id="currentstock'.$row->id.$i.'" value="'.$row->qty.'">';
			
			
			$housekeeping_data[] = array('sr_no'=>$i,
			'item_name'=>$row->item_name,
			'category'=>$row->category,
			'qty'=>$row->qty.' '.$row->shortname,
			'issueqty'=>$qtybox,
			'edit'=>$isss);
			
			$i++;
		}
		}
		
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($housekeeping_data),
			"iTotalDisplayRecords" => count($housekeeping_data),
			"aaData"=>$housekeeping_data);
			echo json_encode($results);
	
	
}


function generalitemissue()
{
	$this->load->view('store/general_item_issue');
	
}



function generalissue()
{

$items=$this->input->post('checkitem');
$usertype=$this->input->post('usertype');
$userid=$this->input->post('userid');

if(count($items)>0)
{
	$issuesession=time();
	for($i=0;$i<count($items);$i++)
	{
		$itemid=$items[$i];
		$qty=$this->input->post('qtybox'.$itemid);
		$currentst=$this->input->post('currentstock'.$itemid);
		
		if($usertype=='1')
		{
			$crmuser=$userid;
			$noncrm=0;
		}else
		{
			$crmuser=0;
			$noncrm=$userid;
		}
		$data=array('itemid'=>$itemid,'qty'=>$qty,'crmuser'=>$crmuser,'stockthattime'=>$currentst,'noncrmuser'=>$noncrm,'issuedOn'=>date('Y-m-d H:i:s'),'issuesession'=>$issuesession);
	
		
		$this->db->insert('issuegeneralstock',$data);
		if($this->db->affected_rows()>0)
		{
			
			$this->db->set('qty', 'qty-'.$qty, false);
			$this->db->where('id' , $itemid);
			$this->db->update('house_keeping_items');
			
		
		}
		
		
	}
	
	        $this->session->set_flashdata('message','<div class="alert alert-danger">Item Issued</div>');
			redirect(page_url.'User/generalitemissue/'.$userid.'/'.$usertype);
	}else
	{
		
		$this->session->set_flashdata('message','<div class="alert alert-danger">Please Select Items to issue.</div>');
		redirect(page_url.'User/generalitemissue/'.$userid.'/'.$usertype);
		
		
	}

	
}

function genratebommissinghelpticket()
{
   
    $userid=$this->uri->segment('3');
    $usertype=$this->uri->segment('4');
    $jobcardid=$this->uri->segment('5');
    $formid=51;
    $inst=$this->input->post('instid');
    $itemname=$this->input->post('itemname');
    $qty=$this->input->post('qty');
    $instrumentname=$this->Store_model->getinstrumentname($inst);
 
    $planneddate=$this->getformdata('51');
    if($usertype=='1')
    {
        /** SYSTEM USER **/
         $uname=$this->Store_model->getudata($userid);
    }else
    {
        /** NON CRM USER **/
        
        $uname=$this->Store_model->getnoncrmusername($userid);
    }
    
    $des=$instrumentname." Bom is Missing request is raised by ".$uname." Bom Details are Item Name-".$itemname." Quantity-".$qty;
     $ar=array("yourname"=>$uname,"descriptionofissue"=>$des,"remarks"=>$des,"uploadimageorvideo(ifany)"=>'');
     
     $rem=json_encode($ar);
    
    $formresp=$this->getnewresponseid();
    $l=strlen($formresp);
    if($l==1)
    {
        $tra='00';
    }else if($l==2)
    {
        $tra='0';
    }else if($l>2)
    {
        $tra='';
    }
    $newnum=$formresp+1;
    $newnum='HTP'.$newnum;
    $data=array('form_id'=>'51','responseid'=>$newnum,'formdata'=>$rem,'work_status'=>'0','added_by'=>$_SESSION['logged_in']['user_id'],'added_on'=>date('Y-m-d H:i:s'),'planned_date'=>date('Y-m-d H:i:s'));
    $this->db->insert('dynamic_form_data',$data); 
    if($this->db->affected_rows()>0)
    {
        $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000; float-left:20px;"><span style="color:red; float-left:20px;">Help ticket Raised</span></div><br/>');
        redirect(page_url.'User/issueblockeditems/'.$jobcardid.'/'.$userid.'/'.$usertype);
    }else
    {
           $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000; float-left:20px;"><span style="color:red; float-left:20px;">Unable to Raise Help ticket</span></div><br/>');
        redirect(page_url.'User/issueblockeditems/'.$jobcardid.'/'.$userid.'/'.$usertype);
        
    }
    
    
}



function getformdata($formid)
{
  $query = $this->db->select('tatdays, tattime')->from('dynamic_forms')->where('id',$formid)->get();
	if($query->num_rows()>0){
		foreach($query->result() as $row);
		$totaldays = $row->tatdays;
		$totaltime = $row->tattime;
		$currenttime = date('H:i');
		$todaysdate = date('Y-m-d');
		if($totaltime){
		$time = date('H:i:s', strtotime($currenttime.'+'.$totaltime.' hour'));
		}else{
			$time = date('H:i:s');
		}
		
			$officestarttime=date('Y-m-d',strtotime($todaysdate."+1 days"))." "."9:30";
		$officestime="9:30";
        $officeendtime=date('Y-m-d')." 18:00";
        $officeetime="18:00";
        
        
		if($totaldays>0){
			
			  $planneddate = date('Y-m-d', strtotime("+".$totaldays." day", strtotime($todaysdate)));
		  $finaldate=$planneddate." ".$currenttime;
		
			
		}else{
			
			$previoussteptime=date('H:i',strtotime($currenttime));
				$tattime = date('Y-m-d H:i', strtotime($previoussteptime.'+'.$totaltime.' hour'));
				
				
			//	echo $officeendtime.'<br/>'.$tattime;exit;
				if(strtotime($officeendtime)<strtotime($tattime))
					{
					   
						    
						    $timediff=round(abs(strtotime($tattime)-strtotime($officeendtime))/60,2);
						  
						    $TATDATE=date('Y-m-d',strtotime($todaysdate."+1 days"));
						    $newtime=date('g:i A',strtotime($officestarttime.'+'.$timediff.' minutes'));
						    
						    
						}else
						{
						   
                            $TATDATE=$todaysdate;
                            $newtime=date('g:i A',strtotime($tattime)); 
						}
			    
			$finaldate = $TATDATE." ".$newtime;
		}
		
			$finaltime=date('H:i:s',strtotime($finaldate));
		$planneddate=date('Y-m-d',strtotime($finaldate));

		
			$q = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$planneddate)->get();
			if($q->num_rows()>0){
				$nextdate = date('Y-m-d', strtotime("+1 day", strtotime($planneddate)));
				$q1 = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();
				if($q1->num_rows>0){
					$nextdate1 = date('Y-m-d', strtotime("+1 day", strtotime($nextdate)));
					$q2 = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate1)->get();
					if($q2->num_rows>0){
						$finaldate = date('Y-m-d', strtotime("+1 day", strtotime($nextdate1)));
					}else{
						$finaldate=$nextdate1." ".$finaltime;
					}
				}else{
					$finaldate = $nextdate." ".$finaltime;
				}
			}else{
				$finaldate= $planneddate." ".$finaltime;
			
			}
			
			
		$finaldate =  $finaldate; 
	}
	
	
	return $finaldate;
	
	
    
}

function getnewresponseid()
{
    $int='';
    $resteye=$this->db->select('responseid')->from('dynamic_form_data')->where('form_id','51')->order_by('id','DESC')->limit(1)->get();
    if($resteye->num_rows()>0)
    {
        foreach($resteye->result() as $resteye1);
    
        $int = $str = preg_replace('/\D/', '', $resteye1->responseid);
        
        
        
    }
    
    return $int;
}


function genrateconsumableitemmissinghelpticket()
{
   
    $userid=$this->uri->segment('3');
    $usertype=$this->uri->segment('4');
    $formid=51;
    $itemname=$this->input->post('itemname');
    $personname=$this->input->post('personname');
    $planneddate=$this->getformdata('51');
    if($usertype=='1')
    {
        /** SYSTEM USER **/
         $uname=$this->Store_model->getudata($userid);
    }else
    {
        /** NON CRM USER **/
        
        $uname=$this->Store_model->getnoncrmusername($userid);
    }
    
    $des="Machine Consumable item is Missing request is raised by ".$uname." Item Name-".$itemname;
     $ar=array("yourname"=>$uname,"descriptionofissue"=>$des,"remarks"=>$des,"uploadimageorvideo(ifany)"=>'');
     
     $rem=json_encode($ar);
    
    $formresp=$this->getnewresponseid();
    $l=strlen($formresp);
    if($l==1)
    {
        $tra='00';
    }else if($l==2)
    {
        $tra='0';
    }else if($l>2)
    {
        $tra='';
    }
    $newnum=$formresp+1;
    $newnum='HTP'.$newnum;
    $data=array('form_id'=>'51','responseid'=>$newnum,'formdata'=>$rem,'work_status'=>'0','added_by'=>$userid,'added_on'=>date('Y-m-d H:i:s'),'planned_date'=>date('Y-m-d H:i:s'));
    $this->db->insert('dynamic_form_data',$data); 
    if($this->db->affected_rows()>0)
    {
        $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000; float-left:20px;"><span style="color:red; float-left:20px;">Help ticket Raised</span></div><br/>');
        redirect(page_url.'User/othermachineitemissue/'.$userid.'/'.$usertype);
    }else
    {
           $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000; float-left:20px;"><span style="color:red; float-left:20px;">Unable to Raise Help ticket</span></div><br/>');
        redirect(page_url.'User/othermachineitemissue/'.$userid.'/'.$usertype);
        
    }
    
    
}



function genrategeneralitemmissinghelpticket()
{
   
    $userid=$this->uri->segment('3');
    $usertype=$this->uri->segment('4');
    $formid=51;
    $itemname=$this->input->post('itemname');
    $personname=$this->input->post('personname');
    $planneddate=$this->getformdata('51');
    if($usertype=='1')
    {
        /** SYSTEM USER **/
         $uname=$this->Store_model->getudata($userid);
    }else
    {
        /** NON CRM USER **/
        
        $uname=$this->Store_model->getnoncrmusername($userid);
    }
    
    $des="General item missing request is raised by ".$uname." Item Name-".$itemname;
     $ar=array("yourname"=>$uname,"descriptionofissue"=>$des,"remarks"=>$des,"uploadimageorvideo(ifany)"=>'');
     
     $rem=json_encode($ar);
    
    $formresp=$this->getnewresponseid();
    $l=strlen($formresp);
    if($l==1)
    {
        $tra='00';
    }else if($l==2)
    {
        $tra='0';
    }else if($l>2)
    {
        $tra='';
    }
    $newnum=$formresp+1;
    $newnum='HTP'.$newnum;
    $data=array('form_id'=>'51','responseid'=>$newnum,'formdata'=>$rem,'work_status'=>'0','added_by'=>$userid,'added_on'=>date('Y-m-d H:i:s'),'planned_date'=>date('Y-m-d H:i:s'));
    $this->db->insert('dynamic_form_data',$data); 
    if($this->db->affected_rows()>0)
    {
        $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000; float-left:20px;"><span style="color:red; float-left:20px;">Help ticket Raised</span></div><br/>');
        redirect(page_url.'User/generalitemissue/'.$userid.'/'.$usertype);
    }else
    {
           $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000; float-left:20px;"><span style="color:red; float-left:20px;">Unable to Raise Help ticket</span></div><br/>');
        redirect(page_url.'User/generalitemissue/'.$userid.'/'.$usertype);
        
    }
    
    
}

function factory_workers()
{
	$this->load->view('users/factory_workers');
}

function add_workers()
{

			$photo=$_FILES['document']['name'];

			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$screenshot=time().'.'.$cat_image;
				move_uploaded_file($_FILES["document"]["tmp_name"],UPLOADPATH.'worker_docs/' . $screenshot);
			}else
			{
				$screenshot='';
			}


			$photo=$_FILES['pass_image']['name'];

			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$screenshot1=time().'.'.$cat_image;
				move_uploaded_file($_FILES["pass_image"]["tmp_name"],UPLOADPATH.'worker_docs/passport_image/' . $screenshot1);
			}else
			{
				$screenshot1='';
			}


	$data=array('name'=>$this->input->post('name'),
				'document_name'=>$this->input->post('name_id'),
				'location'=>$this->input->post('location'),
				'mobile'=>$this->input->post('mobile'),
				'doj'=>date('Y-m-d',strtotime($this->input->post('doj'))),
				'dob'=>date('Y-m-d',strtotime($this->input->post('dob'))),
				'salary'=>$this->input->post('salary'), 
				'document_type'=>$this->input->post('type'),
				'document'=>$screenshot,
				'passport_image'=>$screenshot1,
				'status'=>$this->input->post('status'),
				'addedOn'=>date('Y-m-d H:i:s'),
				'addedBy'=>$_SESSION['logged_in']['user_id']
				);

				$this->db->insert('workers_list',$data);
				 $this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;"><span style="color:red; float-left:20px;">Record succesfully Added</span></div><br/>');
				 redirect(page_url.'User/factory_workers');

}


				function workers_data()
				{

					$tech_item_data=array();
					$reste=$this->db->select('a.*,b.first_name,b.last_name,c.rack_location')->from('workers_list a')->join('system_users b','a.addedBy=b.user_id')->join('store_rack_location c','a.location=c.id')->get();
					if($reste->num_rows()>0)
					{
						$i=1;
						foreach($reste->result() as $row)
						{
							if($row->status==1)
							{
								$sta="<a href='javascript:;' class='btn btn-success btn-xs' onclick='add_remarks(".$row->id.",1);'>Active</a>";
							}else
							{
								$sta="<a href='javascript:;' class='btn btn-success btn-xs' onclick='add_remarks(".$row->id.",0);'>Inactive</a>";
							}
							$edit = "<a href='".page_url."User/edit_worker_detail/".$row->id."'><i class='fa fa-pencil'></i></a>";

							if(file_exists(UPLOADPATH."worker_docs/passport_image/".$row->passport_image))
							{
								$passimage="<img src='".sfdocument."worker_docs/passport_image/".$row->passport_image."' style='width:75px'>";
							}else
							{	
								$passimage="No Image Found";

							}

							$lchange="<a href='javascript:;' class='btn btn-xs btn-warning' onclick='openlocationpopup(".$row->id.");'>Location Transfer</a>";
							$tech_item_data[] = array('sr_no'=>$i,
							'name'=>"<a href='".page_url."User/workers_detail/".$row->id."'><u>".$row->name."</u></a>",
							'name_per_id'=>$row->document_name,
							'location'=>$row->rack_location,
							'mobile'=>$row->mobile,
							'doj'=>date('d-M-Y',strtotime($row->doj)),
							'dob'=>date('d-M-Y',strtotime($row->dob)),
							'document_uploaded'=>"<a href='".sfdocument."worker_docs/".$row->document."' download>".ucwords($row->document_type)."</a>",
							'passport_image'=>$passimage,
							'status'=>$sta,
							'addedon'=>date('d-M-Y',strtotime($row->addedOn)),
							'addedby'=>$row->first_name." ".$row->last_name,
							'edit'=>$edit,
							'locationchange'=>$lchange);


						$i++;
						}

					}

					$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($tech_item_data),
			"iTotalDisplayRecords" => count($tech_item_data),
			"aaData"=>$tech_item_data);
			
		echo json_encode($results);

				}

				function edit_worker_detail()
				{
					$this->load->view('users/edit_workers');
				}


				function update_workers()
				{

				$photo=$_FILES['document']['name'];

				if($photo<>'')
				{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$screenshot=time().'.'.$cat_image;
				move_uploaded_file($_FILES["document"]["tmp_name"],UPLOADPATH.'worker_docs/' . $screenshot);
				unlink(UPLOADPATH."worker_docs/".$this->input->post('olddoc'));
				}else
				{
				$screenshot=$this->input->post('olddoc');
				}


				$photo=$_FILES['pass_image']['name'];

				if($photo<>'')
				{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$screenshot1=time().'.'.$cat_image;
				move_uploaded_file($_FILES["pass_image"]["tmp_name"],UPLOADPATH.'worker_docs/passport_image/' . $screenshot1);
				unlink(UPLOADPATH."worker_docs/passport_image/".$this->input->post('oldpass'));
				}else
				{
				$screenshot1=$this->input->post('oldpass');
				}


	$data=array('name'=>$this->input->post('name'),
				'document_name'=>$this->input->post('name_id'),
				'mobile'=>$this->input->post('mobile'),
				'doj'=>date('Y-m-d',strtotime($this->input->post('doj'))),
				'dob'=>date('Y-m-d',strtotime($this->input->post('dob'))),
				'salary'=>$this->input->post('salary'),
				'document_type'=>$this->input->post('type'),
				'document'=>$screenshot,
				'passport_image'=>$screenshot1,
				'status'=>$this->input->post('status'),
				'updatedOn'=>date('Y-m-d H:i:s'),
				'updatedBy'=>$_SESSION['logged_in']['user_id']
				);

				$this->db->where('id',$this->uri->segment(3));
				$this->db->update('workers_list',$data);

				/** CHECK FOR SALARY CHANGE **/
				if($this->input->post('previous_salary')<>$this->input->post('salary'))
				{
					$psal=$this->input->post('previous_salary');
					$nsal=$this->input->post('salary');
					$log="Salary Changed from $psal to $nsal";
					$hdata=array('workerid'=>$this->uri->segment(3),'logs'=>$log,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id'],'log_type'=>1);
					$this->db->insert('worker_account_history',$hdata);
				}
				/** END **/
				 $this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;"><span style="color:red; float-left:20px;">Record succesfully Updated</span></div><br/>');
				 redirect(page_url.'User/edit_worker_detail/'.$this->uri->segment(3));
				}

				function getworkername()
				{
					$id=$this->uri->segment(3);
					$a=$this->db->select('name')->from('workers_list')->where('id',$id)->get();
					if($a->num_rows()>0)
					{
						foreach($a->result() as $aa);

						echo $aa->name;

					}else{
								echo "";
						}
				}


					function change_worker_status()
					{

						$id=$this->input->post('worker_id');
						$remarks=$this->input->post('remarks');

						if($id>0)
						{


							$worker_status=$this->input->post('worker_status');
							if($worker_status==1)
							{
								$log="Account Activated";
								$sta=1;
							}else
							{
								$log="Accout Deactivated";
								$sta=0;
							}


							$data1=array('status'=>$sta);
							$this->db->where('id',$id);
							$this->db->update('workers_list',$data1);


							$data=array('workerid'=>$id,'logs'=>$log,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id'],'remarks'=>$this->input->post('remarks'));
							$this->db->insert('worker_account_history',$data);

						}


						$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000; float-left:20px;"><span style="color:red; float-left:20px;">Record Updated</span></div><br/>');
						redirect(page_url.'User/factory_workers');



					}

					function getworker_details_location()
					{

							$id=$this->uri->segment(3);
							$a=$this->db->select('a.name,a.location,b.rack_location')->from('workers_list a')->join('store_rack_location b','a.location=b.id')->where('a.id',$id)->get();
							if($a->num_rows()>0)
							{
							foreach($a->result() as $aa);

							echo $aa->name."|".$aa->location."|".$aa->rack_location;

							}else{
							echo "NA";
							}

					}

					function get_store_location()
					{
						echo "<option value=''>Select Location</option>";
						$exist=$this->uri->segment(3);

						$qw=$this->db->select('id,rack_location')->from('store_rack_location')->where('id!=',$exist)->get();
						if($qw->num_rows()>0)
						{
							foreach($qw->result() as $aa)
							{
							echo "<option value='".$aa->id."'>".$aa->rack_location."</option>";
							}
						}else{
							echo "";
						}

					}


					function getlocationName($id)
					{
						$l='';
						$qw=$this->db->select('rack_location')->from('store_rack_location')->where('id',$id)->get();
						if($qw->num_rows()>0)
						{
							foreach($qw->result() as $aa);
							$l=$aa->rack_location;

						}

						return $l;

					}


					function change_worker_location()
					{
						$worker_id=$this->input->post('worker_id_for_location');
						$worker_location_id=$this->input->post('worker_location');
						$worker_current_location_name=$this->input->post('current_location');
						$newlocation=$this->input->post('transfer_location');
						$remarks=$this->input->post('transfer_remarks');

						$data=array('location'=>$newlocation);
						$this->db->where('id',$worker_id);
						$this->db->update('workers_list',$data);


						$worker_current_location_name=$this->getlocationName($worker_location_id);
						$new_location_name=$this->getlocationName($newlocation);

						/** PUNCH LOG **/
						$log="Location Changed from $worker_current_location_name to $new_location_name";
						$hdata=array('workerid'=>$worker_id,'logs'=>$log,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id'],'remarks'=>$this->input->post('transfer_remarks'));
						$this->db->insert('worker_account_history',$hdata);
						/** END **/

						$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000; float-left:20px;"><span style="color:red; float-left:20px;">Record Updated</span></div><br/>');
						redirect(page_url.'User/factory_workers');




					}

					function workers_detail()
					{
						$this->load->view('users/workers_detail');
					}

		function freesparelist(){
			$this->load->view('users/spares.php');
		}

		function paymenttermapprove(){
			$data = array('status'=>1);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update('payment_terms',$data);

			if($res){
				echo "Approved.";
			}

		}

		function paymenttermrejection(){
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->delete('payment_terms');

			if($res){
				echo "Rejected.";
			}else{
				echo "No Record Exist.";
			}

		}

	public function markasquotationapprove(){
	$user_id=139;	
	$id = $this->uri->segment(3);
	$status = 1;
	$remarks = "Quotation Approved.";
	if($status==1){
		$stage = 37;
		$remark_title = 'Quotation Approved.';
			$currentDate = new DateTime();
			$currentDate->modify('+2 days');
			$futureDate = $currentDate->format('Y-m-d');
			$data = array('lead_id'=>$id,
				'lead_status'=>$stage,
				'next_follow_date'=>$futureDate,
				'remarks'=>$remarks,
				'remark_title'=>$remark_title,
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id);
			
			$this->db->insert('progress_remarks',$data);

			$q = $this->db->select('b.title, b.first_name, b.last_name, contact_number')->from('leads a')->join('system_users b','a.added_by=b.user_id','left')->where('a.id',$id)->get();
			foreach($q->result() as $row);
			$leadownername = ucwords(strtolower($row->title." ".$row->first_name." ".$row->last_name));
			$usercontact = $row->contact_number;
			//$usercontact = "9718991797";
			/*Send WhatsApp Notification*/
			$message="Dear ".$leadownername.",

I am pleased to inform you that your quotation has been approved ✅. Kindly proceed with the necessary processing 📝.

Regards,
Shubham Pack 📦";


/*WhatsApp API*/
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					//'receiverMobileNo' => '91'.$contactnumber,
					'receiverMobileNo' => '91'.$usercontact,
					'username' => whatsappuser2,
					'password' => whatsapppass2,
					'message'=>strip_tags($message));

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
						//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					/* end */




			 $this->session->set_flashdata('message','<div class="alert alert-success">Thank You! Quotation has been Appoved. </div>', 'refresh');
            redirect(page_url);

	}
}

public function rejectquotation(){
	$this->load->view('users/rejectquotation');
}


public function putremarktoreject(){
	$id = $this->uri->segment(3);
	$user_id = 139;
	$status = $this->input->post('changestatus');
	if($status==1){
			$stage = 37;
		$remark_title = 'Quotation Approved.';
		$remarks = "Quotation Approved.";
	}else{
		$stage = 38;
		$remark_title = 'Quotation Rejected.';
		$remarks = $this->input->post('remarks');
	}

	
		
			$currentDate = new DateTime();
			$currentDate->modify('+2 days');
			$futureDate = $currentDate->format('Y-m-d');
			$data = array('lead_id'=>$id,
				'lead_status'=>$stage,
				'next_follow_date'=>$futureDate,
				'remarks'=>$remarks,
				'remark_title'=>$remark_title,
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id);
			
			$this->db->insert('progress_remarks',$data);

	if($status==1){


				$q = $this->db->select('b.title, b.first_name, b.last_name, contact_number')->from('leads a')->join('system_users b','a.added_by=b.user_id','left')->where('a.id',$id)->get();
			foreach($q->result() as $row);
			$leadownername = ucwords(strtolower($row->title." ".$row->first_name." ".$row->last_name));
			$usercontact = $row->contact_number;
			//$usercontact = "9718991797";
			/*Send WhatsApp Notification*/
			$message="Dear ".$leadownername.",

I am pleased to inform you that your quotation has been approved ✅. Kindly proceed with the necessary processing 📝.

Regards,
Shubham Pack 📦";


/*WhatsApp API*/
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					//'receiverMobileNo' => '91'.$contactnumber,
					'receiverMobileNo' => '91'.$usercontact,
					'username' => whatsappuser2,
					'password' => whatsapppass2,
					'message'=>strip_tags($message));

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
						//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					/* end */



			}else{

				$q = $this->db->select('b.title, b.first_name, b.last_name, contact_number')->from('leads a')->join('system_users b','a.added_by=b.user_id','left')->where('a.id',$id)->get();
			foreach($q->result() as $row);
			$leadownername = ucwords(strtolower($row->title." ".$row->first_name." ".$row->last_name));
			$usercontact = $row->contact_number;
			//$usercontact = "9718991797";
			/*Send WhatsApp Notification*/
			$message="Dear ".$leadownername.",

We regret to inform you that your quotation has been rejected ❌. The remarks provided are as follows: ".$remarks.".

Kindly review the remarks and make the necessary adjustments if needed 📝.

Regards,
Shubham Pack 📦

";



					/*WhatsApp API*/
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					//'receiverMobileNo' => '91'.$contactnumber,
					'receiverMobileNo' => '91'.$usercontact,
					'username' => whatsappuser2,
					'password' => whatsapppass2,
					'message'=>strip_tags($message));

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
						//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					/* end */
			}



			

			 $this->session->set_flashdata('message','<div class="alert alert-success">Thank You! Quotation status has been changed. </div>', 'refresh');
            redirect(page_url);
}

public function userresponseofmeetinginvitation(){
	$this->load->view('form/meeting-response');
}


public function submitResponse() {
    $this->form_validation->set_rules('response', 'Response', 'required|trim');
    $this->form_validation->set_rules('reason', 'Reason', 'trim');
    
    $meeting_id = $this->input->post('meeting_id');
    $participant_id = base64_decode($this->input->post('participant_id'));
    $response = $this->input->post('response');
    $reason = $this->input->post('reason');

    if ($response === 'Not Available' && empty($reason)) {
        $this->session->set_flashdata('error', 'Reason is required if you select "Not Available".');
        redirect(page_url . 'Mom/respond/' . $meeting_id . '/' . $participant_id);
    }

    date_default_timezone_set("Asia/Kolkata");
    $response_time = date('Y-m-d H:i:s');

    // Save the response in the database
    $data = array(
        'response_status' => $response,
        'response_reason' => $reason,
        'response_time' => $response_time
    );
    $this->db->where('meeting_id', $meeting_id);
    $this->db->where('participant_id', $participant_id);
    $this->db->update('meeting_participants', $data);

    // Fetch meeting and participant details
    $meeting_query = $this->db->select('agenda, date_of_meeting, time_of_meeting, added_by')
                              ->from('df_meeting_notification_alert')
                              ->where('id', $meeting_id)
                              ->get();
    $meeting = $meeting_query->row();

    $participant_query = $this->db->select('first_name, last_name, email, contact_number')
                                  ->from('system_users')
                                  ->where('user_id', $participant_id)
                                  ->get();
    $participant = $participant_query->row();

    $creator_query = $this->db->select('first_name, last_name, email')
                              ->from('system_users')
                              ->where('user_id', $meeting->added_by)
                              ->get();
    $creator = $creator_query->row();

    $participant_name = strtoupper($participant->first_name . ' ' . $participant->last_name);
    $creator_email = $creator->email;

    // Prepare email and WhatsApp message
    $response_status = $response === 'Available' ? 'Available to Join' : 'Not Available to Join';
    $email_body = '
    <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif; background-color: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 8px;">
        <tr>
            <td style="background-color: #4872b8; padding: 10px; text-align: center;">
                <img src="https://shubhampack.com/wp-content/uploads/2021/05/Logo.png" width="160" alt="Shubham Packs" style="display: block; margin: 0 auto;" />
            </td>
        </tr>
        <tr>
            <td style="padding: 15px; background-color: #ffffff;">
                <h2 style="color: #333333; font-size: 20px; margin: 0; text-align: center;">MEETING RESPONSE RECEIVED</h2>
                <hr style="border: 0; border-top: 1px solid #e0e0e0; margin: 15px 0;">
                <p style="color: #555555; font-size: 14px; margin: 0; line-height: 1.6;">
                    <strong>Dear ' . strtoupper($creator->first_name . ' ' . $creator->last_name) . ',</strong>
                </p>
                <p style="color: #555555; font-size: 14px; margin: 15px 0; line-height: 1.6;">
                    The following participant has responded to the meeting:
                </p>
                <ul style="color: #555555; font-size: 14px; margin: 15px 0; line-height: 1.6;">
                    <li><strong>Participant:</strong> ' . $participant_name . '</li>
                    <li><strong>Response:</strong> ' . $response_status . '</li>' .
                    ($response === 'Not Available' ? '<li><strong>Reason:</strong> ' . $reason . '</li>' : '') . '
                </ul>
                <hr style="border: 0; border-top: 1px solid #e0e0e0; margin: 15px 0;">
                <p style="color: #555555; font-size: 13px; text-align: center; margin: 0; line-height: 1.6;">
                    Thank you,<br>Task Management Team
                </p>
            </td>
        </tr>
        <tr>
            <td style="background-color: #4872b8; color: #ffffff; padding: 10px; text-align: center; font-size: 12px;">
                &copy; ' . date("Y") . ' Shubham Packs. All rights reserved.
            </td>
        </tr>
    </table>';

    $whatsapp_message = "Dear " . strtoupper($creator->first_name . ' ' . $creator->last_name) . ",\n\n"
        . "Participant Response for DF Meeting:\n\n"
        . "👤 *Participant:* " . $participant_name . "\n"
        . "📌 *Response:* " . $response_status . "\n" .
        ($response === 'Not Available' ? "📝 *Reason:* " . $reason . "\n" : "") . "\n"
        . "Thank you,\n*Task Management Team*";

    // Send email
    $this->load->library('email');
    $this->email->set_mailtype("html");
    $this->email->to($creator_email);
    $this->email->from('taskmanagement@shubhampack.com', 'Shubham Packs');
    $this->email->subject('Meeting Response Received: ' . strtoupper($meeting->agenda));
    $this->email->message($email_body);
    $this->email->send();

    // Send WhatsApp message
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    $post = array(
        'receiverMobileNo' => '91' . $creator->contact_number,
        'username' => whatsappuser1,
        'password' => whatsapppass1,
        'message' => strip_tags($whatsapp_message)
    );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_exec($ch);
    curl_close($ch);

    // Redirect with success message
    $this->session->set_flashdata('message', '<span style="color:green;" class="alert alert-success">Your response has been submitted successfully.</span>');
    redirect(page_url . 'Mom/respond/' . $meeting_id . '/' . $participant_id);
}


public function switch_to_shubham(){
		if($this->uri->segment(3)){

							$shubh_id= base64_decode($this->uri->segment(3));

							$q = $this->db->select('*')->from('system_users')->where('user_id',$shubh_id)->get();

							if($q->num_rows()>0){

								foreach($q->result() as $row);
							   $data = array('user_id' => $row->user_id,
				     'user_name' => $row->first_name,
				     'last_name' => $row->last_name,
					 'email'=>$row->email,
					 'role'=>$row->user_role_id,
					 'employeecode'=>$row->employeecode,
					 'business_location'=>$row->business_location,
					 'dynachem_id'=>$row->dynachem_id,
					 'department_id'=>$row->department_id,
					  'profile_image'=>$row->profile_image,
                     'user_status'=>$row->user_status);
					$this->session->set_userdata('logged_in',$data);

					// echo "<pre>"; print_r($data); exit;

					redirect(page_url.'Dashboard');

						}

					}
}


}
