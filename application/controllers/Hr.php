<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hr extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$session = $this->session->userdata('logged_in');
		/*if($session == FALSE)
		{
		redirect(page_url);
		
		}*/
$user_id =$this->session->userdata['logged_in']['user_id'];
	/*if(empty($user_id))
         {
         redirect(site_url(),'refresh');
         }*/
		$this->load->model('User_model','user');
		$this->load->model('Master_model','master');
		$config = array();  
		$config['protocol'] = 'smtp';  
		$config['smtp_host'] = 'smtp.googlemail.com';  
		$config['smtp_user'] = 'sundarindustrialsoftware@gmail.com';  
		$config['smtp_pass'] = 'SundarIndst@323';   
		$config['smtp_port'] = 465;  
		$config['smtp_auth'] = true;  
		$config['smtp_crypto'] = 'ssl';  
		$this->email->initialize($config);  
		$this->email->set_newline("\r\n");  
		$this->load->library('email', $config); 
		
	}
	public function leave_application(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('from', 'from', 'required|trim');
		$this->form_validation->set_rules('to', 'to', 'required|trim');
		$this->form_validation->set_rules('totaldays', 'totaldays', 'required|trim');
		$this->form_validation->set_rules('reason', 'reason', 'required|trim');
		$this->form_validation->set_rules('leave_for', 'leave_for', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		    
			$this->load->view('hr/leave_application');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		 
		   $leavefor = $this->input->post('leave_for');
		   if($leavefor=='3'){
		       $fromtime = $this->input->post('from_time');
		        $totime = $this->input->post('to_time');
		   }else{
		       $fromtime="";
		       $totime="";
		   }
			$firstdate = date('Y-m-d',strtotime($this->input->post('from')));
			$lastdate  = date('Y-m-d',strtotime($this->input->post('to')));
			$now = strtotime($firstdate);
			$your_date = strtotime($lastdate);
			$datediff = $your_date - $now;	
			$totaldays= round($datediff / (60 * 60 * 24));
			$totaldaysleave = $totaldays+1; 
			
		   $data=
			array(
			'employee_id'=>$user_id,
			'from_loc'=>date('Y-m-d',strtotime($this->input->post('from'))),
			'to_loc'=>date('Y-m-d',strtotime($this->input->post('to'))),
			'total_days'=>$totaldaysleave,
			'phone_no'=>$this->input->post('phone_number'),
			'reason'=>$this->input->post('reason'),
			'leave_for'=>$this->input->post('leave_for'),
			'from_time'=>$fromtime,
			'to_time'=>$totime,
			'added_on'=>$added_time);
			
			$res = $this->db->insert('leave_application',$data);
		    $lastid=$this->db->insert_id();
		    $url = "https://crm.sunderindoil.com/index.php/Hr/directapproval/".$lastid;
		    $approvallink = "<a href='".$url."'><span style='background-color:green; padding:2px 2px 2px 2px; color:#fff;'>Mark as approved</span></a>";
		    
		    $disappurl = "https://crm.sunderindoil.com/index.php/Hr/directreject/".$lastid;
		     $disapprovallink = "<a href='".$disappurl."'><span style='background-color:red; padding:2px 2px 2px 2px; color:#fff;'>Mark as rejected</span></a>";
			if($this->input->post('leave_for')=='1'){
			    $leavefor = "Full Day";
			}else if($this->input->post('leave_for')=='
			2'){
			   $leavefor = "Half Day"; 
			}else{
			    $leavefor = "Short Leave";
			}
			$reason = $this->input->post('reason');
			$todate = date('d-m-Y',strtotime($this->input->post('to')));
			$fromdate = date('d-m-Y',strtotime($this->input->post('from')));
			$query = $this->db->select('first_name, last_name, email')->from('system_users')->where('user_id',$user_id)->get();
			foreach($query->result() as $employeeinfo);
			/*HOD for leave approval*/
			$query = $this->db->select('a.team_id, b.team_leader, c.email, c.contact_number')->from('presto_team_members a')->join('prestogroup_teams b','a.team_id=b.team_id','left')->join('system_users c','b.team_leader=c.user_id','left')->where('a.employee_id',$user_id)->get();
			if($query->num_rows()>0){
            foreach($query->result() as $hodinfo);	
            	$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
				   <tr>
					<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://crm.sunderindoil.com/assets/images/logo_mitr.png" width="200px;" alt="HPCL" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>LEAVE APPLICATION REQUEST</strong><br></td>
					  </tr> 
					  <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br>Dear Sir,<br>  Please find leave application request of the Employee. </td>
					  </tr> 
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Name -  </strong> : '.$employeeinfo->first_name." ".$employeeinfo->last_name.'<br></td>
					  </tr>
					   <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>From -  </strong> : '.$fromdate.'<br></td>
					  </tr>
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>To -  </strong> : '.$todate.'<br></td>
					  </tr>
					  <tr>
					  	<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Total Days -  </strong> : '.$totaldaysleave.'<br></td>
					  </tr>
					   <tr>
					  	<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Leave for -  </strong> : '.$leavefor.'<br></td>
					  </tr>
					   <tr>
					  	<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Reason -  </strong> : '.$reason.'<br></td>
					  </tr>
					   <tr>
					  	<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Click here to approve -  </strong> : '.$approvallink.'<br><br></td>
					  </tr>
					  <tr></tr>
					  <tr>
					  	<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Click here to reject -  </strong> : '.$disapprovallink.'</td>
					  </tr>
					  
					   <tr>
						<td>&nbsp;</td>
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
				$subjectname = "LEAVE APPLICATION REQUEST ".strtoupper($employeeinfo->first_name." ".$employeeinfo->last_name)." for ".$totaldaysleave;
					$this->email->set_mailtype("html");
					$this->email->to($hodinfo->email);
					//$this->email->cc('hr@prestogroup.com');
					$this->email->bcc('mangleshup@gmail.com');
					$this->email->from('sundarindustrialsoftware@gmail.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();
    				$url1 = "https://crm.sunderindoil.com/";
    				

					/**WHATSAPP INTEGRATION**/
					
					$smsmessage = "Dear Sir,\nPlease find the leave application detail.\nName: ".$employeeinfo->first_name." ".$employeeinfo->last_name."\nFrom: ".$fromdate." To: ".$todate." Total Days: ".$totaldaysleave."\nLeave Type: ".$leavefor."\nReason: ".$reason."\nclick here to approve: ".$url."\nclick here to reject: ".$disappurl."\n";
					//echo $smsmessage; exit;
					
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					// 'receiverMobileNo' => $phone_no,
					'receiverMobileNo' => '91'.$hodinfo->contact_number,
					'username' => whatsappuser,
					'password' => whatsapppass,
					'message'=>strip_tags($smsmessage)		
					);

					//echo "<pre>";print_r($post);exit;

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
					//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					/* end */
					
    				
    	}
            /*HOD for leave approval*/
				
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
			redirect(page_url.'Hr/leave_application');
		  
			}
	}
	
	public function leave_application_dashboard(){
		$data['gamavisleavereport']=$this->gamavis_leave();
		//echo "<pre>"; print_r($data); exit;
		$this->load->view('hr/leave_application_dashboard',$data);
	}
	 
	public function leave_application_dashboard_list()
	{
		$date = date('Y-m-d')." 00-00-00";
		$todays = date('Y-m-d');
		$d2 = date('Y-m-d', strtotime('-150 days'));
		$seconddate = $d2." 00-00-00";
		$leave_data = array();
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$this->db->select('a.*,b.first_name, b.last_name, c.department, d.user_role')->from('leave_application_view a')->join('system_users_view b','a.employee_id=b.user_id','left')->join('departments c','b.department_id=c.department_id','left')->join('user_role d','b.user_role_id=d.user_role_id','left');
		// ->where('a.added_on BETWEEN "'.$seconddate. '" and "'.$date.'"')
		$query = $this->db->order_by('added_on','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		
			if($row->leave_for=='1'){
				$leavefor = "Full Day";
			}else if($row->leave_for=='2'){
				$leavefor = "Half Day";
			}else{
                $time1 = strtotime($row->from_time);
                $time2 = strtotime($row->to_time);
                $difference = round(abs($time2 - $time1) / 3600,2);
			    $leavefor = "Short Leave.<br/>";
			    $leavefor.=$row->from_time."<br/>";
			    $leavefor.=$row->to_time."<br/>";
			    $leavefor.="Total Hour : <strong>".$difference."</strong><br/>";
			}
			

			if($row->approval_status=='0'){
				$hodstatus = "Pending for Review";
			}else if($row->approval_status=='1'){
				$hodstatus = "Approved";
			}else{
				$hodstatus = "Rejected";
			}
			
			if($row->leave_type==''){
			$taskupdation = '<div class="row"><div class="col-md-12">
				<select class="form-control" name="markas" id="markas'.$i.'" onchange="changestatus('.$i.',)">
				<option value="PL">PL</option>
				<option value="CL">CL</option>
				<option value="SL">SL</option>
				</select>
				<input type="hidden" name="recordid[]" id="recordid'.$i.'" value="'.$row->id.'" >
			</div></div>';
			}else{
			    $taskupdation = $row->leave_type;
			}	
			date_default_timezone_set("Asia/Kolkata");
			$added_time = date('Y-m-d h:i A',strtotime($row->added_on));
			$leave_data[] = array('sr_no'=>$i,
			'employee_name'=>strtoupper($row->first_name." ".$row->last_name),
			'timestamp'=>$added_time,
			'leave_date'=>date('d-m-Y',strtotime($row->leave_date)),
			'from_loc'=>date('d-m-Y',strtotime($row->from_loc)),
			'to_loc'=>date('d-m-Y',strtotime($row->to_loc)),
			'reason'=>strtoupper($row->reason),
			'total_days'=>strtoupper($row->total_days),
			'department'=>strtoupper($row->department),
			'user_role'=>strtoupper($row->user_role),
			'phone_no'=>strtoupper($row->phone_no),
			'leave_type'=>$taskupdation,
			'leave_for'=>$leavefor,
			'hodstatus'=>$hodstatus);
			$i++;
		}
		
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($leave_data),
	"iTotalDisplayRecords" => count($leave_data),
	"aaData"=>$leave_data);
	echo json_encode($results);
}

public function leave_application_hod_dashboard_listforkaran()
	{
		$leave_data = array();
	
	    $user_id =$this->session->userdata['logged_in']['user_id'];	
		$this->db->select('a.*,b.first_name, b.last_name, c.department, d.user_role')->from('leave_application a')->join('system_users b','a.employee_id=b.user_id','left')->join('departments c','b.department_id=c.department_id','left')->join('user_role d','b.user_role_id=d.user_role_id','left')->where('a.approval_status','0');
		$query = $this->db->order_by('leave_date','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			if($row->leave_for=='1'){
				$leavefor = "Full Day";
			}else if($row->leave_for=='2'){
				$leavefor = "Half Day";
			}else{
			    $time1 = strtotime($row->from_time);
                $time2 = strtotime($row->to_time);
                $difference = round(abs($time2 - $time1) / 3600,2);
			    $leavefor = "Short Leave.<br/>";
			    $leavefor.=$row->from_time."<br/>";
			    $leavefor.=$row->to_time."<br/>";
			    $leavefor.="Total Hour : <strong>".$difference."</strong><br/>";
			    
			   
                
                
			    
			}
			if($row->approval_status=='0'){
				$approve = "<a href='".page_url."Hr/mark_as_approved/".$row->id."/1'><span class='btn btn-success btn-xs'>Mark as Approved</span></a><br/>";
				$reject = "<a href='".page_url."Hr/mark_as_approved/".$row->id."/2'><span class='btn btn-danger btn-xs'>Mark as Rejected</span></a>";
				$hodstatus = $approve." ".$reject;
			}else if($row->approval_status=='1'){
				$hodstatus = "Approved";
			}else{
				$hodstatus = "Rejected";
			}
			
			date_default_timezone_set("Asia/Kolkata");
			$added_time = date('Y-m-d h:i A',strtotime($row->added_on));
			$leave_data[] = array('sr_no'=>$i,
			'employee_name'=>strtoupper($row->first_name." ".$row->last_name),
			'timestamp'=>$added_time,
			'leave_date'=>date('d-m-Y',strtotime($row->leave_date)),
			'from_loc'=>date('d-m-Y',strtotime($row->from_loc)),
			'to_loc'=>date('d-m-Y',strtotime($row->to_loc)),
			'reason'=>strtoupper($row->reason),
			'total_days'=>strtoupper($row->total_days),
			'department'=>strtoupper($row->department),
			'user_role'=>strtoupper($row->user_role),
			'phone_no'=>strtoupper($row->phone_no),
			'leave_type'=>$row->leave_type,
			'leave_for'=>$leavefor,
			'hodstatus'=>$hodstatus);
			$i++;
		}
	
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($leave_data),
	"iTotalDisplayRecords" => count($leave_data),
	"aaData"=>$leave_data);
	echo json_encode($results);
}
public function leave_application_hod_dashboard_list()
	{
		$leave_data = array();
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		
		$q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
	
		if($q->num_rows()>0)
		{
		foreach($q->result() as $teamdetail);
		 $teammembers = array();
		$query = $this->db->select('team_id, employee_id')->from('presto_team_members')->where('team_id',$teamdetail->team_id)->get();
        $res = $query->result();
        
         foreach($res as $teaminfo){
            $teammembers[] = $teaminfo->employee_id;
            
        }
	
		//$team = implode(',',$teammembers);
		$team = "'" . implode ( "', '", $teammembers ) . "'";
		
		}else
		{
		    $team='NA';
		}
		
if($team<>'NA')
		{
		
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$this->db->select('a.*,b.first_name, b.last_name, c.department, d.user_role')->from('leave_application a')->join('system_users b','a.employee_id=b.user_id','left')->join('departments c','b.department_id=c.department_id','left')->join('user_role d','b.user_role_id=d.user_role_id','left');
		$this->db->where_in('a.employee_id',$team,false)->where('a.approval_status','0');
		$query = $this->db->order_by('leave_date','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			if($row->leave_for=='1'){
				$leavefor = "Full Day";
			}else if($row->leave_for=='2'){
				$leavefor = "Half Day";
			}else{
			    $time1 = strtotime($row->from_time);
                $time2 = strtotime($row->to_time);
                $difference = round(abs($time2 - $time1) / 3600,2);
			    $leavefor = "Short Leave.<br/>";
			    $leavefor.=$row->from_time."<br/>";
			    $leavefor.=$row->to_time."<br/>";
			    $leavefor.="Total Hour : <strong>".$difference."</strong><br/>";
			    
			   
                
                
			    
			}
			if($row->approval_status=='0'){
				$approve = "<a href='".page_url."Hr/mark_as_approved/".$row->id."/1'><span class='btn btn-success btn-xs'>Mark as Approved</span></a><br/>";
				$reject = "<a href='".page_url."Hr/mark_as_approved/".$row->id."/2'><span class='btn btn-danger btn-xs'>Mark as Rejected</span></a>";
				$hodstatus = $approve." ".$reject;
			}else if($row->approval_status=='1'){
				$hodstatus = "Approved";
			}else{
				$hodstatus = "Rejected";
			}
			
			date_default_timezone_set("Asia/Kolkata");
			$added_time = date('Y-m-d h:i A',strtotime($row->added_on));
			$leave_data[] = array('sr_no'=>$i,
			'employee_name'=>strtoupper($row->first_name." ".$row->last_name),
			'timestamp'=>$added_time,
			'leave_date'=>date('d-m-Y',strtotime($row->leave_date)),
			'from_loc'=>date('d-m-Y',strtotime($row->from_loc)),
			'to_loc'=>date('d-m-Y',strtotime($row->to_loc)),
			'reason'=>strtoupper($row->reason),
			'total_days'=>strtoupper($row->total_days),
			'department'=>strtoupper($row->department),
			'user_role'=>strtoupper($row->user_role),
			'phone_no'=>strtoupper($row->phone_no),
			'leave_type'=>$row->leave_type,
			'leave_for'=>$leavefor,
			'hodstatus'=>$hodstatus);
			$i++;
		}
		}
		
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($leave_data),
	"iTotalDisplayRecords" => count($leave_data),
	"aaData"=>$leave_data);
	echo json_encode($results);
}

public function hod_leave_dashboard(){
	$this->load->view('hr/hod_leave_application_dashboard');
}

public function mark_as_approved(){
	$id = $this->uri->segment(3);
	$status = $this->uri->segment(4);
	$data = array('approval_status'=>$status);
	$this->db->where('id',$id);
	$this->db->update('leave_application',$data);
	
	$query = $this->db->select('a.employee_id, b.first_name, b.last_name, b.contact_number, ')->from('leave_application a')->join('system_users b','a.employee_id=b.user_id','left')->where('a.id',$id)->get();
	
	foreach($query->result() as $userinfo);
	if($status=='1'){
		$sta = "has been *Approved* ✅\n\nWish you a speedy recovery if unwell 💐\n\nIf on Vacation have a great time 😊\n\nYour Team HPCL 🚀 ";
	}else{
		$sta = "is *Not Approved* ❌\n\nYou are too important to us. Plan a break later ☺️\n\nYour Team HPCL 🚀";  
	}
	$smsmessage="Hello ".$userinfo->first_name." ".$userinfo->last_name.",\n\nYour Leave Application ".$sta;
	
	//echo $smsmessage; exit;
$qrr = $this->db->select('module_id, user_id, sms, email, whatsaap')->from('module_email_sms_whatsapp_notofication')->where('module_id','1')->where('user_id',$userinfo->employee_id)->get();
			if($qrr->num_rows()>0){
			   $c = $userinfo->contact_number; 
foreach($qrr->result() as $accesscheck);
		
	    	$q1 = $this->db->select('*')->from('email_sms_whatsapp_template')->where('sms_id','3')->get();
			foreach($q1->result() as $smsdata);

/**WHATSAPP INTEGRATION**/
					
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					// 'receiverMobileNo' => $phone_no,
					'receiverMobileNo' => '91'.$c,
					'username' => whatsappuser,
					'password' => whatsapppass,
					'message'=>strip_tags($smsmessage)		
					);

					//echo "<pre>";print_r($post);exit;

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
					//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					/* end */
	    
	    $q1 = $this->db->select('*')->from('email_sms_whatsapp_template')->where('sms_id','2')->get();
			foreach($q1->result() as $smsdata);
			
			

/** EMAIL INTEGRATION **/

$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
				   <tr>
					<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://www.easemysale.com/hpcl/assets/images/logo_mitr.png" width="200px;" alt="HPCL" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>Leave Application Status</strong><br></td>
					  </tr> 
					  <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br>Dear, '.ucfirst($userinfo->first_name).' '.ucfirst($userinfo->last_name).' <br> Please find the update on your application.<br/><br/></td>
					  </tr> 
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Message </strong> : '.$smsmessage.'</td>
					  </tr>
					  
					   <tr>
						<td>&nbsp;</td>
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
			    	$subjectname = "Your Leave Application Status";
					$this->email->set_mailtype("html");
					$this->email->to($rows->email);
					//$this->email->bcc('hr@prestogroup.com');
					$this->email->from('sundarindustrialsoftware@gmail.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();
					
/** EMAIL INTEGRATION**/
	
	
			}
	
	
	
	
	
	
	$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, status successfully updated.</span></div><br/>');
	redirect(page_url.'Hr/hod_leave_dashboard');
}

public function update_leave_status(){
        $status = $this->input->post('status');
		$recordid = $this->input->post('recordid');
		date_default_timezone_set("Asia/Kolkata");
		$data = array('leave_type'=>$status);
		$this->db->where('id',$recordid);
		$res = $this->db->update('leave_application',$data);
		if($res){
		    echo "updated."; exit;
		}
		
		
		
}

public function presto_employee_record(){
    $this->load->view('hr/presto_employee_list');
}

public function presto_employee_record_list()
	{
		$employeedata = array();
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$this->db->select('a.employee_for,a.id,a.employee_name,a.employee_code, a.reporting_manager,a.factory, b.user_id, b.first_name, b.last_name')->from('prestogroup_employees a')->join('system_users b','a.reporting_manager=b.user_id','left');
		$this->db->where('a.user_id','0');
		$query = $this->db->order_by('a.factory','asc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		    
		    $delete = "<a href='".page_url."Hr/delete_factory_employee/".$row->id."'><i class='fa fa-trash'></i>";
		    
		    $edit = "<a href='".page_url."Hr/edit_prestoemployee/".$row->id."'><i class='fa fa-pencil-square-o'></i></a>";
		    
		    $employeecode = $row->employee_code;
		    if($employeecode==''){
		        $generatecode = "<a href='".page_url."Hr/generateemployeecode/".$row->id."'><span class='btn btn-success btn-xs'>Generate Code</span></a>";
		    }else{
		        $generatecode=$employeecode;
		    }
		    if($row->employee_for==1)
		    	{
		    		$for="PRESTOGROUP EMPLOYEE";
		    	}else if($row->employee_for==2){
		    		$for="TESTRONIX EMPLOYEE";
		    	}else{
		    		$for="";
		    	}
		    
			$employeedata[] = array('sr_no'=>$i,
			'employee_name'=>strtoupper($row->employee_name),
			'employee_code'=>$generatecode,
			'hod'=>strtoupper($row->first_name." ".$row->last_name),
			'factory'=>strtoupper($row->factory),
			'for_emp'=>$for,
			'edit'=>$edit." | ".$delete);
			$i++;
		}
		
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($employeedata),
	"iTotalDisplayRecords" => count($employeedata),
	"aaData"=>$employeedata);
	echo json_encode($results);
}


public function generateemployeecode(){
	  //$digits = 5;
//$randomnumber =  rand(pow(10, $digits-1), pow(10, $digits)-1); 
$seed = str_split('abcdefghijklmnopqrstuvwxyz'
                     .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                     .'0123456789'); // and any other characters
    shuffle($seed); // probably optional since array_is randomized; this may be redundant
    $rand = '';
    foreach (array_rand($seed, 5) as $k) $rand .= $seed[$k];
 
    $randomnumber = strtoupper($rand);
   // echo $randomnumber; exit;
$query = $this->db->select('employee_code')->from('prestogroup_employees')->where('employee_code',$randomnumber)->get();
if($query->num_rows()>0){
    $this->session->set_flashdata('message','<div class="alert alert-info">Sorry! this number already exist please try again.</div>');
	redirect(page_url.'Hr/presto_employee_record'); 
}else{
    $data = array('employee_code'=>$randomnumber);
    $this->db->where('id',$this->uri->segment(3));
    $this->db->update('prestogroup_employees',$data);
    $this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully updated.</div>');
	redirect(page_url.'Hr/presto_employee_record'); 
    
}
	    
	}
public function view_your_leave(){
    $this->load->view('hr/view_your_leave');
}

public function your_leave_application_dashboard_list()
	{
		$leave_data = array();
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$this->db->select('a.*,b.first_name, b.last_name, c.department, d.user_role')->from('leave_application a')->join('system_users b','a.employee_id=b.user_id','left')->join('departments c','b.department_id=c.department_id','left')->join('user_role d','b.user_role_id=d.user_role_id','left')->where('a.employee_id',$user_id)->where('a.approval_status','0');
		$query = $this->db->order_by('leave_date','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		
			if($row->leave_for=='1'){
				$leavefor = "Full Day";
			}else if($row->leave_for=='2'){
				$leavefor = "Half Day";
			}else{
                $time1 = strtotime($row->from_time);
                $time2 = strtotime($row->to_time);
                $difference = round(abs($time2 - $time1) / 3600,2);
			    $leavefor = "Short Leave.<br/>";
			    $leavefor.=$row->from_time."<br/>";
			    $leavefor.=$row->to_time."<br/>";
			    $leavefor.="Total Hour : <strong>".$difference."</strong><br/>";
			}
			

			if($row->approval_status=='0'){
				$hodstatus = "Pending for Review";
			}else if($row->approval_status=='1'){
				$hodstatus = "Approved";
			}else{
				$hodstatus = "Rejected";
			}
			
			if($row->leave_type==''){
			$taskupdation = 'Pending for Review';
			}else{
			    $taskupdation = $row->leave_type;
			}	
			date_default_timezone_set("Asia/Kolkata");
			$added_time = date('d-m-Y h:i A',strtotime($row->added_on));
			$leave_data[] = array('sr_no'=>$i,
			'employee_name'=>strtoupper($row->first_name." ".$row->last_name),
			'timestamp'=>$added_time,
			'leave_date'=>date('d-m-Y',strtotime($row->leave_date)),
			'from_loc'=>date('d-m-Y',strtotime($row->from_loc)),
			'to_loc'=>date('d-m-Y',strtotime($row->to_loc)),
			'reason'=>strtoupper($row->reason),
			'total_days'=>strtoupper($row->total_days),
			'department'=>strtoupper($row->department),
			'user_role'=>strtoupper($row->user_role),
			'phone_no'=>strtoupper($row->phone_no),
			'leave_type'=>$taskupdation,
			'leave_for'=>$leavefor,
			'hodstatus'=>$hodstatus);
			$i++;
		}
		
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($leave_data),
	"iTotalDisplayRecords" => count($leave_data),
	"aaData"=>$leave_data);
	echo json_encode($results);
}

public function leave_history(){
    $this->load->view('hr/view_leave_history');
}
public function your_leave_application_history()
	{
		$leave_data = array();
		$type = array('1,2');
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$this->db->select('a.*,b.first_name, b.last_name, c.department, d.user_role')->from('leave_application a')->join('system_users b','a.employee_id=b.user_id','left')->join('departments c','b.department_id=c.department_id','left')->join('user_role d','b.user_role_id=d.user_role_id','left')->where('a.employee_id',$user_id);
		//->where_in('a.approval_status',$type,false);
		$query = $this->db->order_by('leave_date','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		
			if($row->leave_for=='1'){
				$leavefor = "Full Day";
			}else if($row->leave_for=='2'){
				$leavefor = "Half Day";
			}else{
                $time1 = strtotime($row->from_time);
                $time2 = strtotime($row->to_time);
                $difference = round(abs($time2 - $time1) / 3600,2);
			    $leavefor = "Short Leave.<br/>";
			    $leavefor.=$row->from_time."<br/>";
			    $leavefor.=$row->to_time."<br/>";
			    $leavefor.="Total Hour : <strong>".$difference."</strong><br/>";
			}
			

			if($row->approval_status=='0'){
				$hodstatus = "Pending for Review";
			}else if($row->approval_status=='1'){
				$hodstatus = "Approved";
			}else{
				$hodstatus = "Rejected";
			}
			
			if($row->leave_type==''){
			$taskupdation = 'Pending for Review';
			}else{
			    $taskupdation = $row->leave_type;
			}	
			date_default_timezone_set("Asia/Kolkata");
			$added_time = date('Y-m-d h:i A',strtotime($row->added_on));
			$leave_data[] = array('sr_no'=>$i,
			'employee_name'=>strtoupper($row->first_name." ".$row->last_name),
			'timestamp'=>$added_time,
			'leave_date'=>date('d-m-Y',strtotime($row->leave_date)),
			'from_loc'=>date('d-m-Y',strtotime($row->from_loc)),
			'to_loc'=>date('d-m-Y',strtotime($row->to_loc)),
			'reason'=>strtoupper($row->reason),
			'total_days'=>strtoupper($row->total_days),
			'department'=>strtoupper($row->department),
			'user_role'=>strtoupper($row->user_role),
			'phone_no'=>strtoupper($row->phone_no),
			'leave_type'=>$taskupdation,
			'leave_for'=>$leavefor,
			'hodstatus'=>$hodstatus);
			$i++;
		}
		
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($leave_data),
	"iTotalDisplayRecords" => count($leave_data),
	"aaData"=>$leave_data);
	echo json_encode($results);
}

public function overallleave(){
 $this->load->view('hr/overall_leave_application');   
    
}

public function overallleave_data_list()
	{
		$leave_data = array();
		
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$this->db->select('a.*,b.first_name, b.last_name, c.department, d.user_role')->from('leave_application a')->join('system_users b','a.employee_id=b.user_id','left')->join('departments c','b.department_id=c.department_id','left')->join('user_role d','b.user_role_id=d.user_role_id','left')->where('from_loc',date('Y-m-d'))->group_by('a.employee_id');
		
		$query = $this->db->order_by('leave_date','desc')->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		$i=1;
		foreach($res as $row)
		{
			$query = $this->db->select('a.team_id, b.team_leader, c.first_name, c.last_name')->from('presto_team_members a')->join('prestogroup_teams b','a.team_id=b.team_id','left')->join('system_users c','b.team_leader=c.user_id','left')->where('a.employee_id',$row->employee_id)->get();
			if($query->num_rows()>0){
			foreach($query->result() as $teamleader);
			$leader = $teamleader->first_name." ".$teamleader->last_name;
			    
			}else{
			    $leader="";
			}
			
			
			if($row->leave_for=='1'){
				$leavefor = "Full Day";
			}else if($row->leave_for=='2'){
				$leavefor = "Half Day";
			}else{
			    $time1 = strtotime($row->from_time);
                $time2 = strtotime($row->to_time);
                $difference = round(abs($time2 - $time1) / 3600,2);
			    $leavefor = "Short Leave.<br/>";
			    $leavefor.=$row->from_time."<br/>";
			    $leavefor.=$row->to_time."<br/>";
			    $leavefor.="Total Hour : <strong>".$difference."</strong><br/>";
			    	}
			if($row->approval_status=='0'){
				
				$hodstatus = "Pending for review";
			}else if($row->approval_status=='1'){
				$hodstatus = "Approved";
			}else{
				$hodstatus = "Rejected";
			}
			if($row->leave_type==''){
			    $leavetype = "Yet to define";
			}else{
			    $leavetype = $row->leave_type;
			}
			
			$deleteleaveapp="<a href='".page_url."Hr/deleteappliction/".$row->id."'  class='btn btn-danger btn-xs'>DELETE</a>";

			date_default_timezone_set("Asia/Kolkata");
			$added_time = date('Y-m-d h:i A',strtotime($row->added_on));
			$leave_data[] = array('sr_no'=>$i,
			'employee_name'=>strtoupper($row->first_name." ".$row->last_name),
			'timestamp'=>$added_time,
			'leave_date'=>date('d-m-Y',strtotime($row->leave_date)),
			'from_loc'=>date('d-m-Y',strtotime($row->from_loc)),
			'to_loc'=>date('d-m-Y',strtotime($row->to_loc)),
			'reason'=>strtoupper($row->reason),
			'total_days'=>strtoupper($row->total_days),
			'department'=>strtoupper($row->department),
			'user_role'=>strtoupper($row->user_role),
			'phone_no'=>strtoupper($row->phone_no),
			'leave_type'=>$leavetype,
			'leader'=>$leader,
			'leave_for'=>$leavefor,
			'deleteappliction'=>$deleteleaveapp,
			'hodstatus'=>$hodstatus);
			$i++;
		}
		
		
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($leave_data),
	"iTotalDisplayRecords" => count($leave_data),
	"aaData"=>$leave_data);
	echo json_encode($results);
}

public function onleavetoday(){
 $this->load->view('hr/overall_leave_today');   
    
}

public function onleavetoday_data_list()
	{
		$leave_data = array();
		
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$this->db->select('a.*,b.first_name, b.last_name, c.department, d.user_role')->from('leave_application a')->join('system_users b','a.employee_id=b.user_id','left')->join('departments c','b.department_id=c.department_id','left')->join('user_role d','b.user_role_id=d.user_role_id','left')->where('from_loc',date('Y-m-d'));
		
		$query = $this->db->order_by('leave_date','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			if($row->leave_for=='1'){
				$leavefor = "Full Day";
			}else if($row->leave_for=='2'){
				$leavefor = "Half Day";
			}else{
			    $time1 = strtotime($row->from_time);
                $time2 = strtotime($row->to_time);
                $difference = round(abs($time2 - $time1) / 3600,2);
			    $leavefor = "Short Leave.<br/>";
			    $leavefor.=$row->from_time."<br/>";
			    $leavefor.=$row->to_time."<br/>";
			    $leavefor.="Total Hour : <strong>".$difference."</strong><br/>";
			    	}
			if($row->approval_status=='0'){
				
				$hodstatus = "Pending for review";
			}else if($row->approval_status=='1'){
				$hodstatus = "Approved";
			}else{
				$hodstatus = "Rejected";
			}
			if($row->leave_type==''){
			    $leavetype = "Yet to define";
			}else{
			    $leavetype = $row->leave_type;
			}
			
			date_default_timezone_set("Asia/Kolkata");
			$added_time = date('Y-m-d h:i A',strtotime($row->added_on));
			$leave_data[] = array('sr_no'=>$i,
			'employee_name'=>strtoupper($row->first_name." ".$row->last_name),
			'timestamp'=>$added_time,
			'leave_date'=>date('d-m-Y',strtotime($row->leave_date)),
			'from_loc'=>date('d-m-Y',strtotime($row->from_loc)),
			'to_loc'=>date('d-m-Y',strtotime($row->to_loc)),
			'reason'=>strtoupper($row->reason),
			'total_days'=>strtoupper($row->total_days),
			'department'=>strtoupper($row->department),
			'user_role'=>strtoupper($row->user_role),
			'phone_no'=>strtoupper($row->phone_no),
			'leave_type'=>$leavetype,
			'leave_for'=>$leavefor,
			'hodstatus'=>$hodstatus);
			$i++;
		}
		
		
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($leave_data),
	"iTotalDisplayRecords" => count($leave_data),
	"aaData"=>$leave_data);
	echo json_encode($results);
}

public function leave_attendance_report()
	{
		$i=1;
		$uri = $this->uri->segment(4);
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		
		$q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
		if($q->num_rows()>0)
		{
		foreach($q->result() as $teamdetail);
		 $teammembers = array();
		$query = $this->db->select('team_id, employee_id')->from('presto_team_members')->where('team_id',$teamdetail->team_id)->get();
        $res = $query->result();
         foreach($res as $teaminfo){
            $teammembers[] = $teaminfo->employee_id;
            
        }
	
		//$team = implode(',',$teammembers);
		$team = "'" . implode ( "', '", $teammembers ) . "'";
		
		}else
		{
		    $team='NA';
		}
		$administrator = array('66','67','1','194');
		
		
		$attendance_date= array();
		$this->db->select('a.*, c.first_name as hrfname, c.last_name as hrlname')->from('mark_your_attendance_view a')->join('system_users_view c','a.hr_id=c.user_id','left')->where_not_in('a.employee_id',$administrator)->where('a.attendance_date',date('Y-m-d'))->where('a.absent_status','1');
		if($uri=='HOD'){
		    if($q->num_rows()>0){
		$this->db->where_in('a.employee_id',$team, false);
		}
		}else if(!empty($uri) && $uri!=='HOD'){
		  $this->db->where('a.employee_id',$uri);  
		}
		$this->db->order_by('a.id','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
	    if($row->employee_name==''){
	    $qy = $this->db->select('a.first_name, a.last_name, d.department')->from('system_users a')->join('departments d','a.department_id=d.department_id','left')->where('a.user_id',$row->employee_id)->get(); foreach($qy->result() as $row1);
        $empname = strtoupper($row1->first_name." ".$row1->last_name);
        $department = $row1->department;
	        $markabsent="";
	    }else{
	        if($row->absent_status=='1'){
	            $markabsent="HR HAS MARKED ABSENT";
	        }else{
	           $markabsent = "<a href='".page_url."Master/User_management/mark_absent/".$row->id."'><span class='btn btn-danger btn-xs'>ABSENT</span></a>"; 
	        }
	        	
	        $empname = $row->employee_name;
	        $department="Factory Employee";
	    }
	
		
			$attendance_date[] = array('sr_no'=>$i,
			'employee_name'=>$empname,
			'department'=>$department,
			'attendance_date'=>date('d-m-Y',strtotime($row->attendance_date)));
			$i++;
			
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($attendance_date),
			"iTotalDisplayRecords" => count($attendance_date),
			"aaData"=>$attendance_date);
			
		echo json_encode($results);
	}
	
	public function delete_factory_employee(){
	    
	    $id = $this->uri->segment(3);
	    $this->db->where('id',$id);
	    $this->db->delete('prestogroup_employees');
	    $this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, status successfully deleted.</span></div><br/>');
	redirect(page_url.'Hr/presto_employee_record');
	    
	}
	
	public function add_new_employee()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('employee_name', 'employee_name', 'required|trim');
		$this->form_validation->set_rules('hod_name', 'hod_name', 'required|trim');
		$this->form_validation->set_rules('factory', 'factory', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('hr/presto_employee_list');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "prestogroup_employees";
			
	
				
			$data = array(
			    'user_id'=>'0',
			'employee_name'=>$this->input->post('employee_name'),
			'employee_code'=>$this->input->post('employee_code'),
			'reporting_manager'=>$this->input->post('hod_name'),
			'factory'=>$this->input->post('factory'),
			'employee_for'=>$this->input->post('employee_for'),
			'system_user'=>'0',
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->db->insert($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Hr/presto_employee_record');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Hr/presto_employee_record');
		}
		
		
	}
		
	}
	
	public function edit_prestoemployee(){
    $this->load->view('hr/edit_presto_employee');
}

	public function update_employee_data()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('employee_name', 'employee_name', 'required|trim');
		$this->form_validation->set_rules('hod_name', 'hod_name', 'required|trim');
		$this->form_validation->set_rules('factory', 'factory', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			 $this->load->view('hr/edit_presto_employee');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "prestogroup_employees";
			
	
				
			$data = array(
				'employee_name'=>$this->input->post('employee_name'),
			'employee_code'=>$this->input->post('employee_code'),
			'reporting_manager'=>$this->input->post('hod_name'),
			'factory'=>$this->input->post('factory'),
			'employee_for'=>$this->input->post('employee_for'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			$this->db->where('id',$this->uri->segment(3));
			$result  = $this->db->update('prestogroup_employees',$data);
		
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Hr/presto_employee_record');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Hr/presto_employee_record');
		}
		
		
	}
		
	}
	
	public function directapproval(){
	    $q=$this->db->select('id, approval_status')->from('leave_application')->where('id',$this->uri->segment(3))->where('approval_status','0')->get();
	   
	   if($q->num_rows()>0){ 
	    
	    foreach($q->result() as $rowss);
	    $data = array('approval_status'=>'1');
	    $this->db->where('id',$this->uri->segment(3));
	    $this->db->update('leave_application',$data);
		
		$query = $this->db->select('a.employee_id, b.first_name, b.last_name, b.contact_number, a.approval_status')->from('leave_application a')->join('system_users b','a.employee_id=b.user_id','left')->where('a.id',$this->uri->segment(3))->get();
	
	foreach($query->result() as $userinfo);
	if($userinfo->approval_status=='1'){
		$sta = "has been Approved ✅\n\nWish you a speedy recovery if unwell 💐\n\nIf on Vacation have a great time 😊\n\nYour Team HPCL 🚀 ";
	}else{
		$sta = "is Not Approved ❌\n\nYou are too important to us. Plan a break later ☺️\n\nYour Team HPCL 🚀";  
	}
	$smsmessage="Hello ".$userinfo->first_name." ".$userinfo->last_name.",\n\n Your Leave Application ".$sta;
		
		
/**WHATSAPP INTEGRATION**/
					
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					// 'receiverMobileNo' => $phone_no,
					'receiverMobileNo' => '91'.$userinfo->contact_number,
					'username' => whatsappuser,
					'password' => whatsapppass,
					'message'=>strip_tags($smsmessage)		
					);

					//echo "<pre>";print_r($post);exit;

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
					//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					/* end */
					
		
	    $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank You! Attendance marked as approved. </div><br/>');
		redirect(page_url);
	   }else{
	       $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry! Attendace status already updated. </div><br/>');
		redirect(page_url);  
	   }
	    
	    
	}
	public function directreject(){
	    
	    $q=$this->db->select('id')->from('leave_application')->where('id',$this->uri->segment(3))->where('approval_status','0')->get();
	   
	   if($q->num_rows()>0){ 
	    
	    $data = array('approval_status'=>'2');
	    $this->db->where('id',$this->uri->segment(3));
	    $this->db->update('leave_application',$data);
		
		$query = $this->db->select('a.employee_id, b.first_name, b.last_name, b.contact_number,a.approval_status')->from('leave_application a')->join('system_users b','a.employee_id=b.user_id','left')->where('a.id',$this->uri->segment(3))->get();
	
	foreach($query->result() as $userinfo);
	if($userinfo->approval_status=='1'){
		$sta = "has been Approved ✅\n\nWish you a speedy recovery if unwell 💐\n\nIf on Vacation have a great time 😊\n\nYour Team HPCL 🚀 ";
	}else{
		$sta = "is Not Approved ❌\n\nYou are too important to us. Plan a break later ☺️\n\nYour Team HPCL 🚀";  
	}
	$smsmessage="Hello ".$userinfo->first_name." ".$userinfo->last_name.",\n\n Your Leave Application ".$sta;
	
/**WHATSAPP INTEGRATION**/
					
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					// 'receiverMobileNo' => $phone_no,
					'receiverMobileNo' => '91'.$userinfo->contact_number,
					'username' => whatsappuser,
					'password' => whatsapppass,
					'message'=>strip_tags($smsmessage)		
					);

					//echo "<pre>";print_r($post);exit;

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
					//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					/* end */
					


	    $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank You! Attendance marked as rejected. </div><br/>');
		redirect(page_url);
	   }else{
	        $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry! Attendace status already updated. </div><br/>');
		redirect(page_url);   
	   }
	    
	    
	}
	
	public function attendance_report(){
// 		$url="http://crm.gamavis.com/Mitr_api/attendace_report_of_month/";
// $ch = curl_init();
// $post = array('flag'=>'1');
// curl_setopt_array($ch, array(
//     CURLOPT_URL => $url,
//     CURLOPT_RETURNTRANSFER => true,
//     CURLOPT_POST => true,
//     CURLOPT_POSTFIELDS => $post
// ));
// //Ignore SSL certificate verification
// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
//  $response = curl_exec($ch);
// $data['reportofthemonnth'] = json_decode($response,true);
// //echo "<pre>"; print_r($data); exit;
// $err = curl_error($ch);

// curl_close($ch);

	    $this->load->view('hr/attendance_report');
	}
	
	public function attendance_monthly_report(){
	    $data = array('user_id'=>$this->input->post('user_id'),
	    'startdate'=>$this->input->post('startdate'),
	    'enddate'=>$this->input->post('enddate'));
	    //echo "<pre>"; print_r($data); exit;
	    $this->load->view('hr/filter_monthly_report',$data);
	}
	
	function employees_birthday(){
		$this->load->view('hr/birthday_list');
	}
	
	public function birthday_list()
	{
		$employeedata = array();
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		 $query = $this->db->select('a.first_name,a.date_of_birth, a.last_name,b.department')->from('system_users a')->join('departments b','a.department_id=b.department_id','left')->where('user_status','1')->where('hide_profile','0')->where("MONTH(date_of_birth) = MONTH(NOW())")->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		    
		   
			$employeedata[] = array('sr_no'=>$i,
			'employee_name'=>strtoupper($row->first_name." ".$row->last_name),
			'department'=>$row->department,
			'date_of_birth'=>date('d-M',strtotime($row->date_of_birth))
			);
			$i++;
		}
		
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($employeedata),
	"iTotalDisplayRecords" => count($employeedata),
	"aaData"=>$employeedata);
	echo json_encode($results);
}
/*------------GAMAIS LEAVE -----------*/
public function gamavis_leave()
	{


$url="http://crm.gamavis.com/Mitr_api/gamavis_leave_list_mitr/";
$ch = curl_init();
$post = array('flag'=>'1');
curl_setopt_array($ch, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $post
));
//Ignore SSL certificate verification
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
 $response = curl_exec($ch);
 //$data['gamavisleavereport'] = $response;
 //echo "<pre>"; print_r($data); exit;
//$result=json_decode($response,true);

$err = curl_error($ch);

curl_close($ch);
//echo "<pre>"; print_r($result); exit;
return $response;

		//$this->load->view('hr/leave_application_dashboard',$data);


	}

	public function update_leave_status_gamavis(){
        $status = $this->input->post('status');
		$recordid = $this->input->post('recordid');
		date_default_timezone_set("Asia/Kolkata");
		
		$url="http://crm.gamavis.com/Mitr_api/update_status_gamavis_leave/";
$ch = curl_init();
$post = array('status'=>$status,'id'=>$recordid);
curl_setopt_array($ch, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $post
));
//Ignore SSL certificate verification
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
 $response = curl_exec($ch);
$result=json_decode($response,true);
//echo "<pre>"; print_r($post); exit;
$err = curl_error($ch);

curl_close($ch);

redirect(page_url.'Hr/leave_application_dashboard');
		
		
	}
	function deleteappliction()
	{

		$uri=$this->uri->segment(3);

		$this->db->where('id', $uri);
		$this->db->delete('leave_application');

		$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record Delete successfully.</span></div><br/>');
		redirect(page_url.'Hr/overallleave');

	}

public function gamavischecklist(){
		$data['gamavisleavereport']=$this->gamavis_leave();
		//echo "<pre>"; print_r($data); exit;
		$this->load->view('hr/leave_application_dashboard',$data);
	}
	
function leave_attendance_dashboard(){
		$this->load->view('hr/leave_attendance_dashboard');
	}

	function employee_monthly_attendance(){
		$this->load->view('hr/employee_month_attendence');

	}

	function filter_attendance()
	{
		$user_id=$this->input->post('user_id');
		$start_date=$this->input->post('startdate');
		redirect(page_url.'Hr/employee_monthly_attendance/'.$user_id.'/'.$start_date);
	}
	
	
}