<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Service_support extends CI_Controller {
	
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
		
	}

	public function index()
	{
		$this->load->view('service/raise_ticket');
	}
	
		
	public function raise_ticket()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('department', 'Department', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('service/raise_ticket');
		}
		else
		{
		$rand_date = date('y-m-d');
		$rand = (rand(1,100));
		$ticket_id = "PGS-".$rand_date."-".$rand;
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "service_support";
		$data = array('business_location'=>$this->input->post('business_loc'),
			'department_id'=>$this->input->post('department'),
			'user_id'=>$this->input->post('user_id'),
			'ticket'=>$this->input->post('remarks'),
			'ticket_id'=>$ticket_id,
			'status'=>'0',
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->db->insert($table,$data);	
		 $insert_id = $this->db->insert_id();
		if($insert_id)
		{
			$query = $this->db->select('a.*,b.company_name, b.business_loc_id, b.address, c.user_id, c.first_name, c.email, c.last_name,d.user_id,d.user_id, d.first_name as raised_by_fname, d.last_name as raised_by_lname, e.department_id, e.department, f.user_id, f.first_name as closed_by_fname, f.last_name as closed_by_lname')->from('service_support a')->join('business_location b','a.business_location=b.business_loc_id','left')->join('departments e','e.department_id=a.department_id','left')->join('system_users c','a.user_id=c.user_id','left')->join('system_users d','a.added_by=d.user_id','left')->join('system_users f','a.updated_by=f.user_id','left')->where('id',$insert_id)->get();
			foreach($query->result() as $ticket_info);

			 $this->db->select('email,name')->from('support_email_option')->where('support_option',3);
             $query = $this->db->get();
			 $res = $query->row_array();
	
             $toemail = $res['email'];
             $toname = $res['name'];

			

			 $this->db->select('email')->from('support_email_options')->where('support_option',3);
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
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>SERVICE SUPPORT REQUEST</strong><br></td>
					  </tr> 
					  <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>Ticket ID #'.$ticket_info->ticket_id.'</strong><br><br></td>
					  </tr> 
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;">Dear Service Team,<br><br> New Ticket has been raised. Please find the detail.
						</td>
					  </tr>
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					   <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Ticket Raised By </strong> : '.$ticket_info->first_name.' '.$ticket_info->last_name.'</td>
					  </tr>
					  <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Department </strong> : '.$ticket_info->department.'</td>
					  </tr>
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					 
					 <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Remarks </strong> : '.$ticket_info->ticket.'</td>
					  </tr>
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					  
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Ticket URL </strong> : http://crm.packingtest.com/Service_support/view_history/'.$insert_id.'</td>
					  </tr>
					 
					  
					  <tr>
						<td>&nbsp;</td>
					  </tr>
					<tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;;">Best Regards,<br>
						Prestogroup CRM</td>
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
				$subjectname = "Service Support Request ".$ticket_info->ticket_id;
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
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>Ticket ID #'.$ticket_info->ticket_id.'</strong><br><br></td>
					  </tr> 
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;">Thank you! '.$acknowledge->first_name.' '.$acknowledge->last_name.', Your Ticket Successfully Raised. You can check your Ticket status by using below link.	</td>
					  </tr>
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					 
					   <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Ticket URL </strong> : http://crm.packingtest.com/Service_support/view_history/'.$insert_id.'</td>
					  </tr>
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					 
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;;">Best Regards, <br>
						Presto Service Team
						</td>
					  </tr>
					</table>
					</td>
				  </tr>
				</table>';
				
				
				//echo $Message1; exit;
					$subjectname = "Service Support Request ".$ticket_info->ticket_id;
					$this->email->set_mailtype("html");
					$this->email->to($acknowledge->email);
    					$this->email->from('mitr@prestomitr.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message1);
    				$result11=$this->email->send();	
			
			$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully added.</div>');
			redirect(page_url.'Service_support/view_status');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry,technical error accure.</div><br/>');
		redirect(page_url.'Service_support/view_status');
		}
	
		
	}
		
	}
	
	public function user_list(){
		echo "<option value=''>--Select User--</option>";
	$department = $this->input->post('department');
		$query =$this->db->select('user_id, first_name, last_name,department_id, hide_profile, user_status')->from('system_users')->where('department_id',$department)->where('hide_profile','0')->where('user_status','1')->get();
		foreach($query->result() as $users)
			{
				echo "<option value=".$users->user_id.">".$users->first_name." ".$users->last_name."</option>";
				}
	}
	public function raised_ticket_list()
	{
		$i=1;
		$support_ticket = array();
		$this->db->select('a.*,b.company_name, b.business_loc_id, b.address, c.user_id, c.first_name, c.last_name,d.user_id,d.user_id, d.first_name as raised_by_fname, d.last_name as raised_by_lname, e.department_id, e.department, f.user_id, f.first_name as closed_by_fname, f.last_name as closed_by_lname')->from('service_support a')->join('business_location b','a.business_location=b.business_loc_id','left')->join('departments e','e.department_id=a.department_id','left')->join('system_users c','a.user_id=c.user_id','left')->join('system_users d','a.added_by=d.user_id','left')->join('system_users f','a.updated_by=f.user_id','left');
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
				$sta ="<a href='".page_url."Service_support/update_ticket_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Pending</span></a>";
			}
			$history = "<a href='".page_url."Service_support/view_history/".$row->id."'><span class='btn btn-warning btn-xs'><i class='fa fa-eye'></i>&nbsp; Add /See Comments</span></a>";
			$edit = "<a href='".page_url."Service_support/edit_raised_ticket/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			
			$addeddate = date('Y-m-d', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 	
			$support_ticket[] = array('sr_no'=>$i,
			'ticket_id'=>$row->ticket_id,
			'business_loc'=>$row->company_name,
			'department'=>$row->department,
			'user_name'=>$row->first_name." ".$row->last_name,
			'ticket'=>$row->ticket,
			'status'=>$sta,
			'added_on'=>$addeddate."".$addedtime,
			'added_by'=>$row->raised_by_fname." ".$row->raised_by_lname,
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
	
	public function view_status(){
		
		$this->load->view('service/view_status');
	}
	
	public function view_raised_ticket_list()
	{
		$i=1;
		$support_ticket = array();
		$this->db->select('a.*,b.company_name, b.business_loc_id, b.address, c.user_id, c.first_name, c.last_name,d.user_id,d.user_id, d.first_name as raised_by_fname, d.last_name as raised_by_lname, e.department_id, e.department, f.user_id, f.first_name as closed_by_fname, f.last_name as closed_by_lname')->from('service_support a')->join('business_location b','a.business_location=b.business_loc_id','left')->join('departments e','e.department_id=a.department_id','left')->join('system_users c','a.user_id=c.user_id','left')->join('system_users d','a.added_by=d.user_id','left')->join('system_users f','a.updated_by=f.user_id','left');
		$this->db->where('a.status','0');
		$this->db->order_by('a.added_on','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$status = "<a href='".page_url."Service_support/update_ticket_status/".$row->ticket_id."/".$row->status."'><span class='btn btn-success btn-xs'>Reopen</span></a>";
			//	$status = "<span class='btn btn-success btn-xs'>Closed</span>";
				$updateddate = date('Y-m-d', strtotime($row->updated_on));
				$time = date('H:i:s', strtotime($row->updated_on));
				$updatedtime = "<br>". date('g:i A', strtotime($time)); 
				$sta = $status."<br>"."This ticket has been closed by <strong>".$row->closed_by_fname." ".$row->closed_by_lname."</strong><br> at ".$updateddate." ".$updatedtime;
				
			}else
			{
				$sta ="<a href='".page_url."Service_support/update_ticket_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Pending</span></a>";
			}
			$history = "<a href='".page_url."Service_support/view_history/".$row->id."'><span class='btn btn-warning btn-xs'><i class='fa fa-eye'></i>&nbsp; Add /See Comments</span></a>";
			$edit = "<a href='".page_url."Service_support/edit_raised_ticket/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			
			$addeddate = date('Y-m-d', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 	
			$support_ticket[] = array('sr_no'=>$i,
			'ticket_id'=>$row->ticket_id,
			'business_loc'=>$row->company_name,
			'department'=>$row->department,
			'user_name'=>$row->first_name." ".$row->last_name,
			'ticket'=>$row->ticket,
			'status'=>$sta,
			'added_on'=>$addeddate."".$addedtime,
			'added_by'=>$row->raised_by_fname." ".$row->raised_by_lname,
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
	
	public function view_raised_ticket_list_by_department()
	{
		//echo "test"; exit;
		$query = $this->db->select('user_id, department_id')->from('system_users')->where('department_id',$this->uri->segment(3))->get();
		$res = $query->result();
		foreach($res as $departmentdata)
		$user_id[] = $departmentdata->user_id;
		
		$i=1;
		
		$support_ticket = array();
		$this->db->select('a.*,b.company_name, b.business_loc_id, b.address, c.user_id, c.first_name, c.last_name,d.user_id,d.user_id, d.first_name as raised_by_fname, d.last_name as raised_by_lname, e.department_id, e.department, f.user_id, f.first_name as closed_by_fname, f.last_name as closed_by_lname')->from('service_support a')->join('business_location b','a.business_location=b.business_loc_id','left')->join('departments e','e.department_id=a.department_id','left')->join('system_users c','a.user_id=c.user_id','left')->join('system_users d','a.added_by=d.user_id','left')->join('system_users f','a.updated_by=f.user_id','left');
		$this->db->where('a.status','0');
		$this->db->where_in('a.added_by',$user_id);
		$this->db->order_by('a.added_on','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$status = "<a href='".page_url."Service_support/update_ticket_status/".$row->ticket_id."/".$row->status."'><span class='btn btn-success btn-xs'>Reopen</span></a>";
			//	$status = "<span class='btn btn-success btn-xs'>Closed</span>";
				$updateddate = date('Y-m-d', strtotime($row->updated_on));
				$time = date('H:i:s', strtotime($row->updated_on));
				$updatedtime = "<br>". date('g:i A', strtotime($time)); 
				$sta = $status."<br>"."This ticket has been closed by <strong>".$row->closed_by_fname." ".$row->closed_by_lname."</strong><br> at ".$updateddate." ".$updatedtime;
				
			}else
			{
				$sta ="<a href='".page_url."Service_support/update_ticket_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Pending</span></a>";
			}
			$history = "<a href='".page_url."Service_support/view_history/".$row->id."'><span class='btn btn-warning btn-xs'><i class='fa fa-eye'></i>&nbsp; Add /See Comments</span></a>";
			$edit = "<a href='".page_url."Service_support/edit_raised_ticket/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			
			$addeddate = date('Y-m-d', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 	
			$support_ticket[] = array('sr_no'=>$i,
			'ticket_id'=>$row->ticket_id,
			'business_loc'=>$row->company_name,
			'department'=>$row->department,
			'user_name'=>$row->first_name." ".$row->last_name,
			'ticket'=>$row->ticket,
			'status'=>$sta,
			'added_on'=>$addeddate."".$addedtime,
			'added_by'=>$row->raised_by_fname." ".$row->raised_by_lname,
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
	
	
		public function raised_ticket_status()
	{
		$i=1;
		$support_ticket = array();
		$this->db->select('a.*,b.company_name, b.business_loc_id, b.address, c.user_id, c.first_name, c.last_name,d.user_id,d.user_id, d.first_name as raised_by_fname, d.last_name as raised_by_lname, e.department_id, e.department, f.user_id, f.first_name as closed_by_fname, f.last_name as closed_by_lname')->from('service_support a')->join('business_location b','a.business_location=b.business_loc_id','left')->join('departments e','e.department_id=a.department_id','left')->join('system_users c','a.user_id=c.user_id','left')->join('system_users d','a.added_by=d.user_id','left')->join('system_users f','a.updated_by=f.user_id','left')->where('a.id',$this->uri->segment(3));
		$this->db->order_by('a.added_on','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$status = "<a href='".page_url."Service_support/update_ticket_status/".$row->ticket_id."/".$row->status."'><span class='btn btn-success btn-xs'>Reopen</span></a>";
				//$status = "<span class='btn btn-success btn-xs'>Closed</span>";
				$updateddate = date('Y-m-d', strtotime($row->updated_on));
				$time = date('H:i:s', strtotime($row->updated_on));
				$updatedtime = "<br>". date('g:i A', strtotime($time)); 
				$sta = $status."<br>"."This ticket has been closed by <strong>".$row->closed_by_fname." ".$row->closed_by_lname."</strong><br> at ".$updateddate." ".$updatedtime;
				
			}else
			{
				$sta ="<a href='".page_url."Service_support/update_ticket_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Pending</span></a>";
			}
			$history = "<a href='".page_url."Service_support/view_history/".$row->id."'><span class='btn btn-warning btn-xs'><i class='fa fa-eye'></i>&nbsp; Add /See Comments</span></a>";
			$edit = "<a href='".page_url."Service_support/edit_raised_ticket/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			
			$addeddate = date('Y-m-d', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 	
			$support_ticket[] = array('sr_no'=>$i,
			'ticket_id'=>$row->ticket_id,
			'business_loc'=>$row->company_name,
			'department'=>$row->department,
			'user_name'=>$row->first_name." ".$row->last_name,
			'ticket'=>$row->ticket,
			'status'=>$sta,
			'added_on'=>$addeddate."".$addedtime,
			'added_by'=>$row->raised_by_fname." ".$row->raised_by_lname,
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
		$field_name = "id";
		$table = "service_support";
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
			
			$query = $this->db->select('a.added_by, a.id, a.ticket,a.ticket_id,a.updated_on, a.updated_by,b.user_id, b.first_name as assigned_to_first_name, b.last_name as assigned_to_last_name,b.email ,c.user_id, c.first_name as closed_by_first_name, c.last_name as closed_by_last_name')->from('service_support a')->join('system_users b','a.added_by=b.user_id','left')->join('system_users c','a.updated_by=c.user_id','left')->where('a.id',$identifier)->get();
			foreach($query->result() as $ticket_info);
			$updateddate = date('Y-m-d', strtotime($ticket_info->updated_on));
				$time = date('H:i:s', strtotime($ticket_info->updated_on));
				$updatedtime =  date('g:i A', strtotime($time)); 

			 $this->db->select('email')->from('system_users')->where('user_id',$user_id);
             $query = $this->db->get();
			 $res = $query->row_array();
             $fromemail = $res['email'];

			 $this->db->select('email')->from('support_email_options')->where('support_option',3);
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
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>SERVICE SUPPORT REQUEST</strong><br></td>
					  </tr> 
					  <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>Ticket ID #'.$ticket_info->ticket_id.'</strong><br><br></td>
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
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Task Name </strong> : '.$ticket_info->ticket.'</td>
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
				$subjectname = "Service Support Request ".$ticket_info->ticket_id;
					$this->email->set_mailtype("html");
					$this->email->to($ticket_info->email);
					$this->email->cc($ccemails);
					$this->email->bcc('mangleshup@gmail.com');
					$this->email->from($fromemail);
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();
			
			$this->session->set_flashdata('message', '<div class="alert alert-danger" style="color:#fff;">Status successfully updated.</div>');
			redirect('Service_support/view_status/');
		}

	public function edit_raised_ticket(){
		$this->load->view('service/edit_ticket');
	}	
	
	public function update_ticket_detail()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('department', 'Department', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('service/edit_ticket');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "service_support";
		
			$data = array('business_location'=>$this->input->post('business_loc'),
			'department_id'=>$this->input->post('department'),
			'user_id'=>$this->input->post('user_id'),
			'ticket'=>$this->input->post('remarks'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			$this->db->where('id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully updated.</div>');
			redirect(page_url.'Service_support/');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry,technical error accure.</div><br/>');
		redirect(page_url.'Service_support/');
		}
		
		
	}
		
	}
  public function view_history(){
	  $this->load->view('service/ticket_history');
  }		
  public function raised_ticket_detail()
	{
		$i=1;
		$support_ticket = array();
		$this->db->select('a.*,b.company_name, b.business_loc_id, b.address, c.user_id, c.first_name, c.last_name,d.user_id,d.user_id, d.first_name as added_person, d.last_name as lastname, e.department_id, e.department, f.user_id, f.first_name as closed_by_fname, f.last_name as closed_by_lname')->from('service_support a')->join('business_location b','a.business_location=b.business_loc_id','left')->join('departments e','e.department_id=a.department_id','left')->join('system_users c','a.user_id=c.user_id','left')->join('system_users d','a.added_by=d.user_id','left')->join('system_users f','a.updated_by=f.user_id','left')->where('a.id',$this->uri->segment(3));
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
				$sta ="<a href='".page_url."Service_support/update_ticket_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Pending</span></a>";
			}
			
			$edit = "<a href='".page_url."Service_support/edit_tech_items/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			$addeddate = date('Y-m-d', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 	
			$support_ticket[] = array('sr_no'=>$i,
			'business_loc'=>$row->company_name,
			'department'=>$row->department,
			'user_name'=>$row->first_name." ".$row->last_name,
			'ticket'=>$row->ticket,
			'status'=>$sta,
			'added_on'=>$addeddate."".$addedtime,
			'added_by'=>$row->added_person." ".$row->lastname);
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
			$this->load->view('service/ticket_history');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "service_support_ticket_progress";
			$data = array('ticket_id'=>$this->uri->segment(3),
			'remarks'=>$this->input->post('remarks'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$query = $this->db->select('a.*, b.ticket_id, b.id, b.user_id, b.ticket_id, c.user_id, c.first_name as updated_by_fname, c.last_name as updated_by_lname, d.user_id,d.email,d.user_id')->from('service_support_ticket_progress a')->join('service_support b','a.ticket_id=b.id','left')->join('system_users c','a.added_by=c.user_id','left')->join('system_users d','b.added_by=d.user_id','left')->where('a.ticket_id',$this->uri->segment(3))->get();
			foreach($query->result() as $ticket_info);
			 $this->db->select('email,name')->from('support_email_option')->where('support_option',3);
             $query = $this->db->get();
			 $res = $query->row_array();
	
             $toemail = $res['email'];
             $toname = $res['name'];

			

			 $this->db->select('email')->from('support_email_options')->where('support_option',3);
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
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>SERVICE SUPPORT REQUEST</strong><br></td>
					  </tr> 
					  <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>Ticket ID #'.$ticket_info->ticket_id.'</strong><br><br></td>
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
				$subjectname = "Service Support Request ".$ticket_info->ticket_id;
					$this->email->set_mailtype("html");
					$this->email->to($toemail);
					$this->email->cc($ccemails,$ticket_info->email);
					$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('mitr@prestomitr.com);
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();
			
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Service_support/view_history/'.$this->uri->segment(3));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Service_support/view_history'.$this->uri->segment(3));
		}
	}
	}

	public function sendlevelone()
	{   $json = array();
		$ticket_id = $this->input->post('ticket_id');
		
	    
	    $user_id =$this->session->userdata['logged_in']['user_id'];		
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "service_support_ticket_progress";
			$data = array('ticket_id'=>$ticket_id,
			'remarks'=>'Please update the progess on the task',
			'added_on'=>$date,
			'added_by'=>$user_id,
            'level_flag'=>1);
			
			
		$result  = $this->master->insert_record($table,$data);		
		if($result)
		{
			
		$query = $this->db->select('a.*,b.company_name, b.business_loc_id, b.address, c.user_id, c.first_name, c.email, c.last_name,d.user_id,d.user_id, d.first_name as raised_by_fname, d.last_name as raised_by_lname, e.department_id, e.department, f.user_id, f.first_name as closed_by_fname, f.last_name as closed_by_lname')->from('service_support a')->join('business_location b','a.business_location=b.business_loc_id','left')->join('departments e','e.department_id=a.department_id','left')->join('system_users c','a.user_id=c.user_id','left')->join('system_users d','a.added_by=d.user_id','left')->join('system_users f','a.updated_by=f.user_id','left')->where('id',$ticket_id)->get();
			foreach($query->result() as $ticket_info);

			 $this->db->select('email,name')->from('support_email_option')->where('support_option',3);
             $query = $this->db->get();
			 $res = $query->row_array();
	
             $toemail = $res['email'];
             $toname = $res['name'];

			

			 $this->db->select('email')->from('support_email_options')->where('support_option',3);
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
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>SERVICE SUPPORT REQUEST</strong><br></td>
					  </tr> 
					  <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>Ticket ID #'.$ticket_info->ticket_id.'</strong><br><br></td>
					  </tr> 
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;">Dear ,<br>'.$toname.' New Ticket has been raised. Please find the detail.
						</td>
					  </tr>
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Company Name </strong> : '.$ticket_info->company_name.'<br> '.$ticket_info->address.'</td>
					  </tr>
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Department </strong> : '.$ticket_info->department.'</td>
					  </tr>
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Ticket Raised By </strong> : '.$ticket_info->first_name.' '.$ticket_info->last_name.'</td>
					  </tr>
					  <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Remarks </strong> : '.$data['remarks'].'</td>
					  </tr>
					 
					  
					  <tr>
						<td>&nbsp;</td>
					  </tr>
					<tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;;">Best Regards, <br>'.$ticket_info->raised_by_fname.' '.$ticket_info->raised_by_lname.'</td>
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
				$subjectname = "Service Support Request ".$ticket_info->ticket_id;
					$this->email->set_mailtype("html");
					$this->email->to($toemail);
					$this->email->cc($ccemails);
					$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('mitr@prestomitr.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();
					
					
			       $json['success'] = '<div class="alert alert-success">Level 1 query sent</div>';
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
		$table = "service_support_ticket_progress";
			$data = array('ticket_id'=>$ticket_id,
			'remarks'=>'Not received any response from the Tech Support. Please look into this',
			'added_on'=>$date,
			'added_by'=>$user_id,
            'level_flag'=>2);
			
			
		$result  = $this->master->insert_record($table,$data);		
		if($result)
		{
			
			$query = $this->db->select('a.*,b.company_name, b.business_loc_id, b.address, c.user_id, c.first_name, c.email, c.last_name,d.user_id,d.user_id, d.first_name as raised_by_fname, d.last_name as raised_by_lname, e.department_id, e.department, f.user_id, f.first_name as closed_by_fname, f.last_name as closed_by_lname')->from('service_support a')->join('business_location b','a.business_location=b.business_loc_id','left')->join('departments e','e.department_id=a.department_id','left')->join('system_users c','a.user_id=c.user_id','left')->join('system_users d','a.added_by=d.user_id','left')->join('system_users f','a.updated_by=f.user_id','left')->where('id',$ticket_id)->get();
			foreach($query->result() as $ticket_info);

			 $this->db->select('email')->from('support_email_option')->where('support_option',3);
             $query = $this->db->get();
			 $res = $query->row_array();
	
             $toemail = $res['email'];

			

			 $this->db->select('email')->from('support_email_options')->where('support_option',3);
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
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>SERVICE SUPPORT REQUEST</strong><br></td>
					  </tr> 
					  <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>Ticket ID #'.$ticket_info->ticket_id.'</strong><br><br></td>
					  </tr> 
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;">Dear Sir, New Ticket has been raised. Please find the detail.
						</td>
					  </tr>
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Company Name </strong> : '.$ticket_info->company_name.'<br> '.$ticket_info->address.'</td>
					  </tr>
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Department </strong> : '.$ticket_info->department.'</td>
					  </tr>
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Ticket Raised By </strong> : '.$ticket_info->first_name.' '.$ticket_info->last_name.'</td>
					  </tr>
					  <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Remarks </strong> : '.$data['remarks'].'</td>
					  </tr>
					 
					  
					  <tr>
						<td>&nbsp;</td>
					  </tr>
					<tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;;">Best Regards, <br>'.$ticket_info->raised_by_fname.' '.$ticket_info->raised_by_lname.'</td>
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
				$subjectname = "Service Support Request ".$ticket_info->ticket_id;
					$this->email->set_mailtype("html");
					$this->email->to('gaurav@prestogroup.com,vishal@prestogroup.com');
					$this->email->cc($toemail);
					$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('mitr@prestomitr.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();
					
					
			       $json['success'] = '<div class="alert alert-success">Level 2 query sent</div>';
                   header('Content-Type: application/json');
                   echo json_encode($json);
			
		
		
		
	}
		}
	
public function closed_ticket_list(){
    
    $this->load->view('service/closed_ticket_list');
}
public function view_closed_ticket_list()
	{
		$i=1;
		$support_ticket = array();
		$this->db->select('a.*,b.company_name, b.business_loc_id, b.address, c.user_id, c.first_name, c.last_name,d.user_id,d.user_id, d.first_name as raised_by_fname, d.last_name as raised_by_lname, e.department_id, e.department, f.user_id, f.first_name as closed_by_fname, f.last_name as closed_by_lname')->from('service_support a')->join('business_location b','a.business_location=b.business_loc_id','left')->join('departments e','e.department_id=a.department_id','left')->join('system_users c','a.user_id=c.user_id','left')->join('system_users d','a.added_by=d.user_id','left')->join('system_users f','a.updated_by=f.user_id','left');
		$this->db->where('a.status','1');
		$this->db->order_by('a.added_on','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$status = "<a href='".page_url."Service_support/update_ticket_status/".$row->ticket_id."/".$row->status."'><span class='btn btn-success btn-xs'>Reopen</span></a>";
			//	$status = "<span class='btn btn-success btn-xs'>Closed</span>";
				$updateddate = date('Y-m-d', strtotime($row->updated_on));
				$time = date('H:i:s', strtotime($row->updated_on));
				$updatedtime = "<br>". date('g:i A', strtotime($time)); 
				$sta = $status."<br>"."This ticket has been closed by <strong>".$row->closed_by_fname." ".$row->closed_by_lname."</strong><br> at ".$updateddate." ".$updatedtime;
				
			}else
			{
				$sta ="<a href='".page_url."Service_support/update_ticket_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Pending</span></a>";
			}
			$history = "<a href='".page_url."Service_support/view_history/".$row->id."'><span class='btn btn-warning btn-xs'><i class='fa fa-eye'></i>&nbsp; Add /See Comments</span></a>";
			$edit = "<a href='".page_url."Service_support/edit_raised_ticket/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			
			$addeddate = date('Y-m-d', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 	
			$support_ticket[] = array('sr_no'=>$i,
			'ticket_id'=>$row->ticket_id,
			'business_loc'=>$row->company_name,
			'department'=>$row->department,
			'user_name'=>$row->first_name." ".$row->last_name,
			'ticket'=>$row->ticket,
			'status'=>$sta,
			'added_on'=>$addeddate."".$addedtime,
			'added_by'=>$row->raised_by_fname." ".$row->raised_by_lname,
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
	
	public function view_closed_ticket_list_by_department()
	{
		//echo "test"; exit;
		$query = $this->db->select('user_id, department_id')->from('system_users')->where('department_id',$this->uri->segment(3))->get();
		$res = $query->result();
		foreach($res as $departmentdata)
		$user_id[] = $departmentdata->user_id;
		
		$i=1;
		
		$support_ticket = array();
		$this->db->select('a.*,b.company_name, b.business_loc_id, b.address, c.user_id, c.first_name, c.last_name,d.user_id,d.user_id, d.first_name as raised_by_fname, d.last_name as raised_by_lname, e.department_id, e.department, f.user_id, f.first_name as closed_by_fname, f.last_name as closed_by_lname')->from('service_support a')->join('business_location b','a.business_location=b.business_loc_id','left')->join('departments e','e.department_id=a.department_id','left')->join('system_users c','a.user_id=c.user_id','left')->join('system_users d','a.added_by=d.user_id','left')->join('system_users f','a.updated_by=f.user_id','left');
		$this->db->where('a.status','1');
		$this->db->where_in('a.added_by',$user_id);
		$this->db->order_by('a.added_on','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$status = "<a href='".page_url."Service_support/update_ticket_status/".$row->ticket_id."/".$row->status."'><span class='btn btn-success btn-xs'>Reopen</span></a>";
			//	$status = "<span class='btn btn-success btn-xs'>Closed</span>";
				$updateddate = date('Y-m-d', strtotime($row->updated_on));
				$time = date('H:i:s', strtotime($row->updated_on));
				$updatedtime = "<br>". date('g:i A', strtotime($time)); 
				$sta = $status."<br>"."This ticket has been closed by <strong>".$row->closed_by_fname." ".$row->closed_by_lname."</strong><br> at ".$updateddate." ".$updatedtime;
				
			}else
			{
				$sta ="<a href='".page_url."Service_support/update_ticket_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Pending</span></a>";
			}
			$history = "<a href='".page_url."Service_support/view_history/".$row->id."'><span class='btn btn-warning btn-xs'><i class='fa fa-eye'></i>&nbsp; Add /See Comments</span></a>";
			$edit = "<a href='".page_url."Service_support/edit_raised_ticket/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			
			$addeddate = date('Y-m-d', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 	
			$support_ticket[] = array('sr_no'=>$i,
			'ticket_id'=>$row->ticket_id,
			'business_loc'=>$row->company_name,
			'department'=>$row->department,
			'user_name'=>$row->first_name." ".$row->last_name,
			'ticket'=>$row->ticket,
			'status'=>$sta,
			'added_on'=>$addeddate."".$addedtime,
			'added_by'=>$row->raised_by_fname." ".$row->raised_by_lname,
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

