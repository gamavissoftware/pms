<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Delegation extends CI_Controller {
	
	public function __construct()
	{
		
		parent::__construct();
	
		$session = $this->session->userdata('logged_in');
		
		$this->load->model('User_model','user');
		
		$this->load->model('Master_model','master');
		$config = Array(
        'protocol' => 'smtp',
        'smtp_host' => 'ssl://smtp.googlemail.com',
        'smtp_port' => 465,
        'smtp_user' => 'taskmanagement@shubhampack.com',
        'smtp_pass' => 'ficihlqnfcdrrqkb',
        'mailtype'  => 'html', 
        'charset'   => 'utf-8',
        'newline'   => "\r\n"
        //'smtp_crypto'   => 'tls'
        );
        //echo "<pre>"; print_r($config); exit;
         $this->email->initialize($config);
		$this->email->set_mailtype("html");
		//$this->email->set_newline("\r\n");
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
		
		
	}
	
	public function delegation_master(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('business_loc', 'business_loc', 'required|trim');
	$this->form_validation->set_rules('user_id', 'user_id', 'required|trim');
		$this->form_validation->set_rules('what', 'what', 'required|trim');
	$this->form_validation->set_rules('when', 'when', 'required|trim');
	
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('delegation/delegation_master');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   
		   $query = $this->db->select('assigned_to,what_to_do')->from('delegation_master')->where('assigned_to',$this->input->post('user_id'))->where('what_to_do',$this->input->post('what'))->where('video',$this->input->post('form_video_link'))->get();
		   $res = $query->result();
		   if($res){
			   $this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Sorry,This record already exist.</span><br/>');
				redirect(page_url.'Delegation/delegation_master');
			   
		   }else{
		   
		   
		   $data=
			array('business_id'=>strtoupper($this->input->post('business_loc')),
			'assigned_to'=>strtoupper($this->input->post('user_id')),
			'what_to_do'=>strtoupper($this->input->post('what')),
			'when_to_do'=>strtoupper($this->input->post('when')),
			'video'=>$this->input->post('form_video_link'),
			'dashboard_video_link'=>$this->input->post('dashboard_video'),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			
			$res = $this->db->insert('delegation_master',$data);
			$last_id = $this->db->insert_id();
			if($res)
			{
				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
				redirect(page_url.'Delegation/delegation_master');
				}
		   }
			
			}
	}

public function user_list(){
		echo "<option value=''>--Select User--</option>";
	$business_loc = $this->input->post('business_loc');
		$query =$this->db->select('user_id, first_name, last_name,business_location, hide_profile, user_status')->from('system_users_view')->where('business_location',$business_loc)->where('hide_profile','0')->where('user_status','1')->get();
		foreach($query->result() as $users)
			{
				echo "<option value=".$users->user_id.">".strtoupper($users->first_name." ".$users->last_name)."</option>";
				}
	}

	public function user_list_new(){
		echo "<option value=''>--Select User--</option>";
	$department = $this->input->post('department');
		$query =$this->db->select('user_id, first_name, last_name,department_id, hide_profile, user_status')->from('system_users')->where('department_id',$department)->where('hide_profile','0')->where('user_status','1')->get();
		foreach($query->result() as $users)
			{
				echo "<option value=".$users->user_id.">".strtoupper($users->first_name)." ".strtoupper($users->last_name)."</option>";
				}
	}
	
public function delegation_list()
	{
		$scheduler_data = array();
		$query = $this->db->select('a.*, b.business_loc_id, b.company_name, c.user_id, c.title, c.first_name, c.last_name, d.state_name, e.city_name')->from('delegation_master a')->join('business_location b','a.business_id=b.business_loc_id','left')->join('system_users_view c','a.assigned_to=c.user_id','left')->join('states d','b.state_id=d.state_id','left')->join('cities e','b.city_id=e.city_id','left')->order_by('c.first_name','asc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			
			
			$edit = "<a href='".page_url."Delegation/edit_delegation_master/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$scheduler_data[] = array('sr_no'=>$i,
			'business_location'=>strtoupper($row->company_name),
			'assigned_to'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'what_to_do'=>strtoupper($row->what_to_do),
			'when_to_do'=>strtoupper($row->when_to_do),
			'video'=>$row->video,
			'edit'=>$edit);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}

public function edit_delegation_master(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('business_loc', 'business_loc', 'required|trim');
	$this->form_validation->set_rules('user_id', 'user_id', 'required|trim');
	$this->form_validation->set_rules('what', 'what', 'required|trim');
	$this->form_validation->set_rules('when', 'when', 'required|trim');
	
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('delegation/edit_delegation');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		    $data=
			array('business_id'=>strtoupper($this->input->post('business_loc')),
			'assigned_to'=>strtoupper($this->input->post('user_id')),
			'what_to_do'=>strtoupper($this->input->post('what')),
			'when_to_do'=>strtoupper($this->input->post('when')),
			'video'=>$this->input->post('form_video_link'),
			'dashboard_video_link'=>$this->input->post('dashboard_video'),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update('delegation_master',$data);
			
			if($res)
			{
				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully updated.</span></div><br/>');
				redirect(page_url.'Delegation/delegation_master');
				}
		   
			
			}
	}
public function update_delegation_master_status()
	{
		
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "id";
		$table = "delegation_master";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Status successfully updated.</span></div>');
			redirect(page_url.'Delegation/delegation_master');
		}

public function delegation_task(){
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('your_name', 'your_name', 'required|trim');
	$this->form_validation->set_rules('department', 'department', 'required|trim');
	$this->form_validation->set_rules('delegate_to', 'delegate_to', 'required|trim');
	$this->form_validation->set_rules('task', 'task', 'required|trim');
	$this->form_validation->set_rules('work_completion_date', 'work_completion_date', 'required|trim');
	
// 	$conturi= $this->uri->segment(2);
// 	$iduri= $this->uri->segment(3);

// if($conturi=='delegation_task' && $iduri=='1'){
    
//     $query = $this->db->select('*')->from('system_users_view')->where('user_id',$iduri)->get();
//     foreach($query->result() as $row);
//      $data = array('user_id' => $row->user_id,
// 				     'user_name' => $row->first_name,
// 				     'last_name' => $row->last_name,
// 					 'email'=>$row->email,
// 					 'role'=>$row->user_role_id,
// 					 'business_location'=>$row->business_location,
// 					 'department_id'=>$row->department_id,
// 					  'profile_image'=>$row->profile_image,
//                      'user_status'=>$row->user_status);
// 					$this->session->set_userdata('logged_in',$data);
    
// }


	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('delegation/delegate_task');
			}else
		{
		   date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   $photo=$_FILES['image']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$screenshot=time().'.'.$cat_image;
				move_uploaded_file($_FILES["image"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/image_bank/delegation/' . $screenshot);
			}else
			{
				$screenshot="";
				}	
		   
		   $query = $this->db->select('id')->from('delegation_task')->get();
		   $res = count($query->result());
		   if($res<=0){
			   $caseno = "SP-1";
		   }else{
			   $caseno= "SP-".$res;
		   }
		   
		   $department = $this->input->post('department');
		   $data=
			array('yourname'=>strtoupper($this->input->post('your_name')),
			'department_id'=>$department,
			'delegate_to'=>$this->input->post('delegate_to'),
			'task'=>strtoupper($this->input->post('task')),
			'image'=>$screenshot,
			'delegated_date'=>date('Y-m-d',strtotime($this->input->post('work_completion_date'))),
			'targetdate'=>date('Y-m-d',strtotime($this->input->post('work_completion_date'))),
			'added_by'=>$user_id,
			'case_no'=>$caseno,
			'added_on'=>$added_time);
			//echo "<pre>"; print_r($data); exit;
			$res = $this->db->insert('delegation_task',$data);
			$last_id = $this->db->insert_id();
			if($res)
			{
				
				$qry = $this->db->select('first_name, last_name')->from('system_users_view')->where('user_id',$this->input->post('your_name'))->get();
				foreach($qry->result() as $assignedto);

				$assignedbypersonname = ucwords(strtolower($assignedto->first_name." ".$assignedto->last_name));
				$businessname = "";
				$query = $this->db->select('user_id, email, contact_number, first_name, last_name, business_location')->from('system_users_view')->where('user_id',$this->input->post('delegate_to'))->get();
				foreach($query->result() as $rows);
				if($rows->business_location==1){
					$businessname= "Shubham Flexible Packaging";
				}else{
					$businessname = "Shubham Flexible Packaging";
				}
				$userresponseurl = page_url."User/delegation_response/".$last_id;
			$contactnumber = $rows->contact_number;
			$task = $this->input->post('task');	
			$targetdat = date('d-m-Y',strtotime($this->input->post('work_completion_date')));
			//echo $targetdat; exit;
			$q1 = $this->db->select('*')->from('email_sms_whatsapp_template')->where('sms_id','1')->get();
			foreach($q1->result() as $smsdata);
			$assgnedby = $assignedto->first_name." ".$assignedto->last_name;
			
			//$smsmessage = $smsdata->first_field." ".$rows->first_name.", ".$smsdata->third_field." ".$assgnedby." Task :".$task." ".$smsdata->sixth_field." ".$targetdat." ".$smsdata->eighth_field." Presto Testing Instruments.";
	
			$qrr = $this->db->select('module_id, user_id, sms, email, whatsaap')->from('module_email_sms_whatsapp_notofication')->where('module_id','1')->where('user_id',$this->input->post('delegate_to'))->get();
			
			// if($qrr->num_rows()>0){
			// foreach($qrr->result() as $accesscheck);
	

	    $completiondate = date('d-m-Y',strtotime($this->input->post('work_completion_date')));
	    	$q1 = $this->db->select('*')->from('email_sms_whatsapp_template')->where('sms_id','3')->get();
			foreach($q1->result() as $smsdata);
			$smsmessage1 = '';
		//$smsmessage = $smsdata->first_field." ".$rows->first_name."\n".$smsdata->third_field."\n".$assgnedby."\nTASK: ".$task." \n".$smsdata->sixth_field." \n".date('d-m-Y',strtotime($this->input->post('work_completion_date')))." ".$smsdata->eighth_field." \n";
	    
	    $username = strtoupper(ucfirst($rows->first_name));
	    $smsmessage1 = "Dear ".ucwords(strtolower($username)).",<br>
Important Task delegated to you ⏱️

TASK: *".$task."* 
Complete it by: *".$completiondate."*. 
Assigned By : *".$assignedbypersonname."*
*$businessname* 🚀";

if($user_id==139){
 /**WHATSAPP INTEGRATION**/
					
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					// 'receiverMobileNo' => $phone_no,
					'receiverMobileNo' => '91'.$contactnumber,
					'username' => whatsappuser2,
					'password' => whatsapppass2,
					'message'=>strip_tags($smsmessage1));

					//echo "<pre>";print_r($post);exit;

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
						//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					/* end */
}else{
	 /**WHATSAPP INTEGRATION**/
					
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					// 'receiverMobileNo' => $phone_no,
					'receiverMobileNo' => '91'.$contactnumber,
					'username' => whatsappuser1,
					'password' => whatsapppass1,
					'message'=>strip_tags($smsmessage1));

					//echo "<pre>";print_r($post);exit;

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
						//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
}
	    

$q1 = $this->db->select('*')->from('email_sms_whatsapp_template')->where('sms_id','2')->get();
			foreach($q1->result() as $smsdata);
			
			//$smsmessage = $smsdata->first_field." ".$rows->first_name."<br><br> ".$smsdata->third_field."<br><br> ".$assignedto->first_name."<br><br> TASK: ".$task." <br><br> ".$smsdata->sixth_field." <br><br> ".date('d-m-Y',strtotime($this->input->post('work_completion_date')))." ".$smsdata->eighth_field." <br><br> ";

			$taskdetail = ucwords(strtolower($task));
			$delegatedby = $assignedbypersonname;
			$duedatetocomplete = date('d-m-Y',strtotime($this->input->post('work_completion_date')));

/** EMAIL INTEGRATION **/

$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif; background-color: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 8px;">
    <tr>
        <td style="background-color: #4872b8; padding: 10px; text-align: center;">
            <img src="https://shubhampack.com/wp-content/uploads/2021/05/Logo.png" width="160" alt="Shubham Packs" style="display: block; margin: 0 auto;" />
        </td>
    </tr>
    <tr>
        <td style="padding: 15px; background-color: #ffffff;">
            <h2 style="color: #333333; font-size: 20px; margin: 0; text-align: center;">WORK DELEGATION NOTIFICATION</h2>
            <hr style="border: 0; border-top: 1px solid #e0e0e0; margin: 15px 0;">
            <p style="color: #555555; font-size: 14px; margin: 0;">
                <strong>Dear '.$rows->first_name.' '.$rows->last_name.',</strong>
                <br> A new task has been delegated to you. Below are the task details:
            </p>
            <p style="color: #555555; font-size: 14px; margin: 10px 0;">
                <strong>Task Details:</strong> '.$taskdetail.'
            </p>
            <p style="color: #555555; font-size: 14px; margin: 10px 0;">
                <strong>Task completion Date:</strong> '.$duedatetocomplete.'
            </p>
            <p style="color: #555555; font-size: 14px; margin: 10px 0;">
                <strong>Delegated By:</strong> '.$delegatedby.'
            </p>
            <p style="color: #555555; font-size: 14px; margin: 10px 0;">
                Kindly log in to your PMS to update the progress on the task at the earliest.
            </p>
            <hr style="border: 0; border-top: 1px solid #e0e0e0; margin: 15px 0;">
            <p style="color: #555555; font-size: 13px; text-align: center; margin: 0;">
                If you have any questions, contact us at <a href="mailto:taskmanagement@shubhampack.com" style="color: #4872b8; text-decoration: none;">taskmanagement@shubhampack.com</a>.
            </p>
        </td>
    </tr>
    <tr>
        <td style="background-color: #4872b8; color: #ffffff; padding: 10px; text-align: center; font-size: 12px;">
            &copy; '.date("Y").' Shubham Packs. All rights reserved.
        </td>
    </tr>
</table>';



$subjectname = "Work Delegation Notification";
$this->email->set_mailtype("html");
$this->email->to($rows->email);
$this->email->bcc('mangleshup@gmail.com');
$this->email->from('taskmanagement@shubhampack.com', 'Shubham Flexible Packaging');
$this->email->subject($subjectname);
$this->email->message($Message);
$result11 = $this->email->send();
$this->email->print_debugger(); 

					
/** EMAIL INTEGRATION**/
	

$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
redirect(page_url.'Delegation/delegation_task');
				}
		   
			
			}
	}		

public function delegation_dashboard(){
	

	$this->load->view('delegation/delegation_dashboard');
}

public function delegated_dashboard_summary()
{
    date_default_timezone_set("Asia/Kolkata");

    $uid = $this->uri->segment(3);

    if ($uid == '') {
        $user_id = $this->session->userdata['logged_in']['user_id'];
    } else {
        $user_id = $uid;
    }

    $today = date('Y-m-d');

    $this->db->select('
        a.id,
        a.urgency,
        a.task_status,
        a.delegated_date,
        a.second_date,
        a.third_date,
        a.added_on,
        a.delegate_to,
        a.yourname,
        c.first_name,
        c.last_name
    ');
    $this->db->from('delegation_task a');
    $this->db->join('system_users_view c', 'a.delegate_to = c.user_id', 'left');
    $this->db->where('a.task_status', 0);
    $this->db->where('a.yourname', $user_id);

    $query = $this->db->get();

    $total_open = 0;
    $due_today = 0;
    $overdue = 0;
    $high_priority = 0;
    $second_followup_pending = 0;
    $third_followup_pending = 0;
    $no_response = 0;

    $user_wise = array();

    foreach ($query->result() as $row) {
        $total_open++;

        if ($row->urgency == 1) {
            $high_priority++;
        }

        if (
            $row->delegated_date == $today ||
            $row->second_date == $today ||
            $row->third_date == $today
        ) {
            $due_today++;
        }

        $last_due_date = $row->delegated_date;

        if ($row->third_date != '0000-00-00' && $row->third_date != '') {
            $last_due_date = $row->third_date;
        } else if ($row->second_date != '0000-00-00' && $row->second_date != '') {
            $last_due_date = $row->second_date;
        }

        if ($last_due_date != '' && $last_due_date != '0000-00-00' && $last_due_date < $today) {
            $overdue++;
        }

        if ($row->second_date == '0000-00-00' || $row->second_date == '') {
            $second_followup_pending++;
        }

        if (
            $row->second_date != '0000-00-00' &&
            $row->second_date != '' &&
            ($row->third_date == '0000-00-00' || $row->third_date == '')
        ) {
            $third_followup_pending++;
        }

        $response_check = $this->db
            ->select('id')
            ->from('user_response_on_delegated_task')
            ->where('task_id', $row->id)
            ->limit(1)
            ->get();

        if ($response_check->num_rows() == 0) {
            $no_response++;
        }

        $assigned_name = trim($row->first_name . ' ' . $row->last_name);

        if ($assigned_name == '') {
            $assigned_name = 'Not Assigned';
        }

        if (!isset($user_wise[$assigned_name])) {
            $user_wise[$assigned_name] = 0;
        }

        $user_wise[$assigned_name]++;
    }

    arsort($user_wise);

    $top_assignees = array();
    $counter = 0;

    foreach ($user_wise as $name => $count) {
        $top_assignees[] = array(
            'name' => strtoupper($name),
            'count' => $count
        );

        $counter++;

        if ($counter >= 5) {
            break;
        }
    }

    $response = array(
        'total_open' => $total_open,
        'due_today' => $due_today,
        'overdue' => $overdue,
        'high_priority' => $high_priority,
        'second_followup_pending' => $second_followup_pending,
        'third_followup_pending' => $third_followup_pending,
        'no_response' => $no_response,
        'top_assignees' => $top_assignees
    );

    echo json_encode($response);
}



	public function delegated_task_list()
{
    $poorf = "";

    $uid = $this->uri->segment(3);

    if ($uid == '') {
        $user_id = $this->session->userdata['logged_in']['user_id'];
        $disabled = 0;
        $buttondisable = "";
    } else {
        $user_id = $uid;
        $disabled = 1;
        $buttondisable = "disabled";
    }

    date_default_timezone_set("Asia/Kolkata");

    $scheduler_data = array();

    $this->db->select('
        a.id as recordid,
        a.urgency,
        a.task_status,
        a.second_date,
        a.third_date,
        a.yourname,
        a.department_id,
        a.delegate_to,
        a.task,
        a.image,
        a.delegated_date,
        a.case_no,
        a.added_on,
        b.department,
        b.department_id,
        c.user_id,
        c.title,
        c.first_name,
        c.last_name,
        d.first_name as delegated_by,
        d.last_name as delegated
    ');
    $this->db->from('delegation_task a');
    $this->db->join('departments b', 'a.department_id = b.department_id', 'left');
    $this->db->join('system_users_view c', 'a.delegate_to = c.user_id', 'left');
    $this->db->join('system_users_view d', 'a.yourname = d.user_id', 'left');
    $this->db->where('a.task_status', 0);
    $this->db->where('a.yourname', $user_id);

    $query = $this->db->order_by('a.added_on', 'desc')->get();
    $res = $query->result();

    $i = 1;

    foreach ($res as $row) {

        $today_filter_date = date('Y-m-d');

        $d_date = '';
        if ($row->delegated_date != '' && $row->delegated_date != '0000-00-00') {
            $d_date = date('d-M-Y', strtotime($row->delegated_date));
        }

        $date = date('Y-m-d');
        $seconddate = $row->second_date;
        $thirddate = $row->third_date;

        if ($date == $seconddate || $date == $thirddate) {
            $background = "style='background-color:yellow; color:#000;'";
        } else {
            $background = "";
        }

        $addeddate = date('d-M-Y', strtotime($row->added_on));
        $time = date('H:i:s', strtotime($row->added_on));
        $addedtime = "<br>" . date('g:i A', strtotime($time));

        $date1 = new DateTime($row->delegated_date);
        $date2 = new DateTime(date('Y-m-d', strtotime($row->added_on)));
        $interval = $date1->diff($date2);
        $days = $interval->d;

        $sc = $days / 2;
        $seconddatedeff = round($sc);

        $td = $seconddatedeff / 2;
        $thirddatediff = round($td);

        $secondplanneddate = date('Y-m-d', strtotime($row->delegated_date . "+" . $seconddatedeff . " days"));
        $thirdplanneddate = date('Y-m-d', strtotime($secondplanneddate . "+" . $thirddatediff . " days"));

        $remarks = "";

        /** LAST FOLLOW-UP REMARKS **/
        $q = $this->db
            ->select('task_id, followup_remarks, added_on')
            ->from('delegated_task_followup')
            ->where('task_id', $row->recordid)
            ->limit(1)
            ->order_by('id', 'desc')
            ->get();

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $followup);

            $followupdate = date('d-M-Y', strtotime($followup->added_on));
            $times = date('H:i:s', strtotime($followup->added_on));
            $followuptime = "<br>" . date('g:i A', strtotime($times));

            $remarks .= $followup->followup_remarks . "<br>" . $followupdate . $followuptime;
        }
        /** LAST FOLLOW-UP REMARKS **/

        /** USER RESPONSE **/
        $userresponse = "";
        $userremarks = "";

        $q1 = $this->db
            ->select('id, task_id, user_response, task_status, updated_on, proof')
            ->from('user_response_on_delegated_task')
            ->where('task_id', $row->recordid)
            ->limit(1)
            ->order_by('id', 'desc')
            ->get();

        if ($q1->num_rows() > 0) {
            foreach ($q1->result() as $userinput);

            if ($userinput->task_status == 1) {
                $sta = "<span class='btn btn-success btn-xs'>DONE</span>";
            } else {
                $sta = "<span class='btn btn-danger btn-xs'>PENDING</span>";
            }

            $responsedate = date('d-M-Y', strtotime($userinput->updated_on));
            $rtimes = date('H:i:s', strtotime($userinput->updated_on));
            $responsetime = "<br>" . date('h:i A', strtotime($rtimes));

            $userresponse = $sta . "<br><br>" . $responsedate . $responsetime;
            $userremarks = $userinput->user_response;

            $poorf = '';
        }
        /** USER RESPONSE **/

        $secondfollowupdate = "";
        $thirdfollowupdate = "";

        if ($row->second_date !== '0000-00-00' && $row->second_date != '') {

            $secondfollowupdate = date('d-M-Y', strtotime($row->second_date));

            if (($row->third_date == '0000-00-00' || $row->third_date == '') && $row->second_date !== '0000-00-00') {

                /** SET THIRD FOLLOW-UP DATE **/
                $thirdfollowupdate .= ' <button class="btn btn-danger btn-xs" data-toggle="modal" ' . $buttondisable . ' data-target="#con-close-modal3' . $i . '"><i class="fa fa-calendar"></i></button>';

                $thirdfollowupdate .= '<div id="con-close-modal3' . $i . '" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <form method="post" action="' . page_url . 'Delegation/update_third_followupdate/' . $row->recordid . '">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h4 class="modal-title"><span style="color:red; font-weight:bold">' . strtoupper($row->task) . '</span></h4>
                                </div>

                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label">3rd Follow-up Date</label><br>
                                                <input class="form-control" type="date" name="third_followdate" value="' . $thirdplanneddate . '" min="' . date('Y-m-d') . '">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                    <input type="submit" class="btn btn-info" value="Submit">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>';
                /** SET THIRD FOLLOW-UP DATE **/

            } else if ($row->second_date !== '0000-00-00' && $row->third_date !== '0000-00-00' && $row->third_date != '') {
                $thirdfollowupdate = date('d-M-Y', strtotime($row->third_date));
            }

        } else {

            /** SET SECOND FOLLOW-UP DATE **/
            $secondfollowupdate .= ' <button class="btn btn-danger btn-xs" ' . $buttondisable . ' data-toggle="modal" data-target="#con-close-modal2' . $i . '"><i class="fa fa-calendar"></i></button>';

            $secondfollowupdate .= '<div id="con-close-modal2' . $i . '" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <form method="post" action="' . page_url . 'Delegation/update_second_followupdate/' . $row->recordid . '">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title"><span style="color:red; font-weight:bold">' . strtoupper($row->task) . '</span></h4>
                            </div>

                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">2nd Follow-up Date</label><br>
                                            <input class="form-control" type="date" name="second_followdate" value="' . $secondplanneddate . '" min="' . date('Y-m-d') . '">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                <input type="submit" class="btn btn-info" value="Submit">
                            </div>
                        </div>
                    </div>
                </form>
            </div>';
            /** SET SECOND FOLLOW-UP DATE **/
        }

        /** REMARK BUTTON LOGIC **/
        $a = 0;

        $restyuu = $this->db
            ->select('id')
            ->from('delegated_task_followup')
            ->where('task_id', $row->recordid)
            ->get();

        $restyuu1 = $restyuu->num_rows();
        $newremarksfor = $restyuu1 + 1;

        if ($newremarksfor == '1') {
            $nrmk = "FIRST DATE";
            $fdate = $row->delegated_date;

            if ($fdate == '0000-00-00' || $fdate == '') {
                $a = 1;
            }

        } else if ($newremarksfor == '2') {
            $nrmk = "SECOND DATE";
            $fdate = $row->second_date;

            if ($fdate == '0000-00-00' || $fdate == '') {
                $a = 1;
            }

        } else {
            $nrmk = "THIRD DATE";
            $fdate = $row->third_date;

            if ($fdate == '0000-00-00' || $fdate == '') {
                $a = 1;
            }
        }

        if ($restyuu1 < 3) {
            $remarks .= ' <button class="btn btn-success btn-xs" data-toggle="modal" ' . $buttondisable . ' data-target="#con-close-modal' . $i . '">UPDATE ' . $nrmk . ' REMARKS</button>';
        } else {
            $remarks .= ' <button class="btn btn-success btn-xs">Please Reassign Task</button>';
        }

        $ontimeornot = '';

        if ($a <> 1) {
            if (date('Y-m-d') <= $fdate) {
                $ontimeornot = 0;
            } else {
                $ontimeornot = 1;
            }
        }

        $remarks .= '<div id="con-close-modal' . $i . '" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <form method="post" action="' . page_url . 'Delegation/update_followup_remarks/' . $row->recordid . '">
                <input type="hidden" name="timeornot" value="' . $ontimeornot . '">

                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title">UPDATE <span style="color:red; font-weight:bold">' . strtoupper($row->task) . '</span> FOLLOW-UP REMARKS</h4>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">UPDATE FOLLOW-UP REMARKS</label><br>
                                        <textarea class="form-control delegation-remarks-box" name="remarks" style="width:100%; min-height:180px; resize:vertical; font-size:14px; line-height:22px;" required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">';

        if ($a <> 1) {
            $remarks .= '<input type="submit" ' . $buttondisable . ' class="btn btn-info" value="Submit">
                         <button type="button" ' . $buttondisable . ' class="btn btn-default waves-effect" data-dismiss="modal">Close</button>';
        } else {
            $remarks .= '<span style="color:red;font-weight:bold;">Please set ' . $nrmk . ' to update followup</span>';
        }

        $remarks .= '</div>
                    </div>
                </div>
            </form>
        </div>';
        /** REMARK BUTTON LOGIC **/

        /** TASK STATUS SELECT **/
        $taskupdation = '<div class="row">
            <div class="col-md-12">
                <select class="form-control" name="markas" id="markas' . $i . '" onchange="changestatus(' . $i . ')">
                    <option value="">SELECT OPTION</option>
                    <option value="1">COMPLETED</option>
                    <option value="2">AGAIN DELEGATED</option>
                </select>
                <input type="hidden" name="recordid[]" id="recordid' . $i . '" value="' . $row->recordid . '">
            </div>
        </div>';
        /** TASK STATUS SELECT **/

        /** ATTACHMENTS **/
        $attachment = "";

        $sql2 = $this->db
            ->select('image')
            ->from('delegation_images')
            ->where('delegation_id', $row->recordid)
            ->get();

        if ($sql2->num_rows() > 0) {
            foreach ($sql2->result() as $row2) {
                if ($row2->image) {
                    $attachment .= "<a href='" . delegationfile . $row2->image . "' download>DOWNLOAD</a><br>";
                }
            }
        }
        /** ATTACHMENTS **/

        $edit = '<a href="' . page_url . 'Delegation/edit_new_delegation_task/' . $row->case_no . '" class="btn btn-primary btn-xs"><i class="fa fa-pencil" aria-hidden="true"></i></a><br>
                 <a href="' . page_url . 'Delegation/view_add_remarks/' . $row->recordid . '" class="btn btn-info btn-xs">View Remarks</a>';

        /** HIDDEN KPI FILTER FIELDS **/
        $latest_due_date_for_filter = $row->delegated_date;

        if ($row->third_date != '0000-00-00' && $row->third_date != '') {
            $latest_due_date_for_filter = $row->third_date;
        } else if ($row->second_date != '0000-00-00' && $row->second_date != '') {
            $latest_due_date_for_filter = $row->second_date;
        }

        $due_today_filter = 0;

        if (
            $row->delegated_date == $today_filter_date ||
            $row->second_date == $today_filter_date ||
            $row->third_date == $today_filter_date
        ) {
            $due_today_filter = 1;
        }

        $overdue_filter = 0;

        if ($latest_due_date_for_filter != '' && $latest_due_date_for_filter != '0000-00-00' && $latest_due_date_for_filter < $today_filter_date) {
            $overdue_filter = 1;
        }

        $no_response_filter = 0;

        if (trim(strip_tags($userresponse)) == '') {
            $no_response_filter = 1;
        }

        $followup_pending_filter = 0;

        if (
            $row->second_date == '0000-00-00' ||
            $row->second_date == '' ||
            (
                $row->second_date != '0000-00-00' &&
                $row->second_date != '' &&
                ($row->third_date == '0000-00-00' || $row->third_date == '')
            )
        ) {
            $followup_pending_filter = 1;
        }
        /** HIDDEN KPI FILTER FIELDS **/

        $scheduler_data[] = array(
            'sr_no' => $i . ' ' . $edit,
            'timestamp' => $addeddate . $addedtime,
            'delegated_by' => strtoupper($row->delegated_by . " " . $row->delegated),
            'delegated_to' => strtoupper($row->title . " " . $row->first_name . " " . $row->last_name),
            'caseno' => strtoupper($row->case_no),
            'task' => strtoupper($row->task),
            'attachment' => $attachment,
            'delegated_date' => $d_date,
            'second_date' => $secondfollowupdate,
            'third_date' => $thirdfollowupdate,
            'followup' => $remarks,
            'status_on_thirddate' => $taskupdation,
            'response' => ucwords(strtolower($userresponse)),
            'remarks' => ucwords(strtolower($userremarks . '<br>' . $poorf)),

            // Hidden fields for KPI filtering
            'urgency' => $row->urgency,
            'due_today_filter' => $due_today_filter,
            'overdue_filter' => $overdue_filter,
            'no_response_filter' => $no_response_filter,
            'followup_pending_filter' => $followup_pending_filter
        );

        $i++;
    }

    $results = array(
        "sEcho" => 1,
        "iTotalRecords" => count($scheduler_data),
        "iTotalDisplayRecords" => count($scheduler_data),
        "aaData" => $scheduler_data
    );

    echo json_encode($results);
}

public function delegated_task_listolddd()
	{
		
		$user_id =$this->session->userdata['logged_in']['user_id'];
		$scheduler_data = array();
		$this->db->select('a.id as recordid,a.task_status,a.second_date, a.third_date, a.yourname, a.department_id, a.delegate_to, a.task,a.image, a.delegated_date, a.case_no, a.added_on, b.department,b.department_id,c.user_id, c.title, c.first_name, c.last_name')->from('delegation_task a')->join('departments b','a.department_id=b.department_id','left')->join('system_users_view c','a.delegate_to=c.user_id','left');
		$this->db->where('a.yourname',$user_id);
		$this->db->where('a.task_status',0);
		$query = $this->db->order_by('c.added_on','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$date = date('Y-m-d');
			$seconddate = $row->second_date;
			$thirddate = $row->third_date;
			if($date==$seconddate || $date==$thirddate){
				$background = "style='background-color:yellow; color:#000;'";
			}else{
				$background = "";
			}
			
			if($row->image){
				$attachment = "<a href='".delegationfile.$row->image."' download>CLICK HERE TO DOWNLOAD</a>";
			}else{
			$attachment = "";
			}
			
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			
			$date1 = new DateTime($row->delegated_date); 
			$date2 = new DateTime($addeddate); 
			$interval = $date1->diff($date2); 
			$days = $interval->d; 
			$sc= $days/2;
			$seconddatedeff  = round($sc);
			$td = $seconddatedeff/2;
			$thirddatediff = round($td);
			$secondplanneddate  = date('Y-m-d', strtotime($row->delegated_date."+".$seconddatedeff." days"));
			$thirdplanneddate  = date('Y-m-d', strtotime($secondplanneddate."+".$thirddatediff." days"));
			$remarks = "";
			/**LAST FOLLOW-UP REMARKS**/
			$q = $this->db->select('task_id, followup_remarks, added_on')->from('delegated_task_followup')->where('task_id',$row->recordid)->limit(1)->order_by('id','desc')->get();
			if($q->num_rows()>0){
				foreach($q->result() as $followup);
				$followupdate = date('d-M-Y', strtotime($followup->added_on));
				$times = date('H:i:s', strtotime($followup->added_on));
				$followuptime = "<br>". date('g:i A', strtotime($times)); 

				$remarks.=$followup->followup_remarks."<br>".$followupdate.$followuptime;
			}
			
			/**LAST FOLLOW-UP REMARKS**/
			
			/**USER RESPONSE**/
			$userresponse = "";
			$q1 = $this->db->select('id,task_id, user_response, updated_on')->from('user_response_on_delegated_task')->where('task_id',$row->recordid)->limit(1)->order_by('id','desc')->get();
			if($q1->num_rows()>0){
				foreach($q1->result() as $userinput);
				$responsedate = date('d-M-Y', strtotime($userinput->updated_on));
				$rtimes = date('H:i:s', strtotime($userinput->updated_on));
				$responsetime = "<br>". date('g:i A', strtotime($rtimes)); 
				$userresponse=$userinput->user_response."<br>".$responsedate.$responsetime;
				
			}
			$secondfollowupdate="";
			$thirdfollowupdate="";
			$query = $this->db->select('task_id')->from('delegated_task_followup')->where('task_id',$row->recordid)->get();
			if($query->num_rows()>0){
				
				if($row->second_date!=='0000-00-00'){
					$secondfollowupdate = date('d-M-Y',strtotime($row->second_date));
			
				if($row->third_date=='0000-00-00' && $row->second_date!=='0000-00-00'){
					
					/**SET NEW FOLLOWUP DATE**/
			$thirdfollowupdate.=' <button class="btn btn-warning btn-xs waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$i.'">SET NEXT FOLLOW-UP DATE</button>';
			$thirdfollowupdate.= '<div id="con-close-modal'.$i.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
           <form id="loginForm" method="post" action="'.page_url.'Delegation/update_third_followupdate/'.$row->recordid.'">
  
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title"><span style="color:red; font-weight:bold">'.strtoupper($row->task).'</span></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                               
												 <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">3rd Follow-up Date</label><br>
														<span id="error_color_name" style="color:red;"></span>
                                                      <input class="form-control" type="date" name="third_followdate" value="'.$thirdplanneddate.'">
                                                    </div>
                                                </div>
												
                                               
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>';
			/**SET NEW FOLLOWUP DATE**/
				}
				else if($row->second_date!=='0000-00-00' && $row->third_date!=='0000-00-00'){
					$thirdfollowupdate=date('d-M-Y',strtotime($row->third_date));
				}
					
				}else{
					$secondfollowupdate="";
			/**SET NEW FOLLOWUP DATE**/
			$secondfollowupdate.=' <button class="btn btn-warning btn-xs waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$i.'">SET NEXT FOLLOW-UP DATE</button>';
			$secondfollowupdate.= '<div id="con-close-modal'.$i.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
           <form id="loginForm" method="post" action="'.page_url.'Delegation/update_second_followupdate/'.$row->recordid.'">
  
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title"><span style="color:red; font-weight:bold">'.strtoupper($row->task).'</span></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                               
												 <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">2nd Follow-up Date</label><br>
														<span id="error_color_name" style="color:red;"></span>
                                                      <input class="form-control" type="date" name="second_followdate" value="'.$secondplanneddate.'">
                                                    </div>
                                                </div>
												
                                               
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>';
			/**SET NEW FOLLOWUP DATE**/
				}
				
			}
			
			
			
			/**USER RESPONSE**/
			
			$remarks.=' <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$i.'">UPDATE REMARKS</button>';
			$remarks.= '<div id="con-close-modal'.$i.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
           <form id="loginForm" method="post" action="'.page_url.'Delegation/update_followup_remarks/'.$row->recordid.'">
  
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">UPDATE <span style="color:red; font-weight:bold">'.strtoupper($row->task).'</span> FOLLOW-UP REMARKS</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                               
												 <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">UPDATE FOLLOW-UP REMARKS</label><br>
														<span id="error_color_name" style="color:red;"></span>
                                                       <textarea class="form-control" name="remarks" id="remarks" style="width:800px" required></textarea>
                                                    </div>
                                                </div>
												
                                               
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>';
			
			$taskupdation = '<div class="row"><div class="col-md-12">
				<select class="form-control" name="markas" id="markas'.$i.'" onchange="changestatus('.$i.',)">
				<option value="">SELECT OPTION</option>
				<option value="1">COMPLETED</option>
				<option value="2">AGAIN DELEGATED</option>
				</select>
				<input type="hidden" name="recordid[]" id="recordid'.$i.'" value="'.$row->recordid.'" >
			</div></div>';
			
			$scheduler_data[] = array('sr_no'=>$i,
			'timestamp'=>$addeddate.$addedtime,
			'delegated_to'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'caseno'=>strtoupper($row->case_no),
			'task'=>strtoupper($row->task),
			'delegated_date'=>date('d-M-Y', strtotime($row->delegated_date)),
			'second_date'=>$secondfollowupdate,
			'third_date'=>$thirdfollowupdate,
			'followup'=>$remarks,
			'attachment'=>$attachment,
			'status_on_thirddate'=>$taskupdation,
			'response'=>$userresponse);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}
public function delegated_task(){
	$this->load->view('delegation/delegated_task');
}

private function get_latest_delegation_response($task_id)
{
    $task_id = (int) $task_id;

    if ($task_id <= 0) {
        return null;
    }

    $latest_new_response = $this->db
        ->select('id, task_id, user_id, remarks, status, attachment, created_at')
        ->from('delegation_task_response')
        ->where('task_id', $task_id)
        ->order_by('id', 'DESC')
        ->limit(1)
        ->get();

    if ($latest_new_response->num_rows() > 0) {
        $row = $latest_new_response->row();

        return array(
            'remarks' => (string) $row->remarks,
            'status' => (int) $row->status,
            'attachment' => (string) $row->attachment,
            'updated_on' => (string) $row->created_at
        );
    }

    $latest_old_response = $this->db
        ->select('id, task_id, user_response, task_status, proof, updated_on')
        ->from('user_response_on_delegated_task')
        ->where('task_id', $task_id)
        ->order_by('id', 'DESC')
        ->limit(1)
        ->get();

    if ($latest_old_response->num_rows() > 0) {
        $row = $latest_old_response->row();

        return array(
            'remarks' => (string) $row->user_response,
            'status' => (int) $row->task_status,
            'attachment' => (string) $row->proof,
            'updated_on' => (string) $row->updated_on
        );
    }

    return null;
}

public function task_delegated_to_you()
{
    date_default_timezone_set("Asia/Kolkata");

    $user_id = $this->session->userdata['logged_in']['user_id'];
    $today = date('Y-m-d');

    $scheduler_data = array();

    $this->db->select('
        a.urgency,
        a.id as recordid,
        a.second_date,
        a.third_date,
        a.task_status,
        a.yourname,
        a.department_id,
        a.delegate_to,
        a.task,
        a.image,
        a.delegated_date,
        a.case_no,
        a.added_on,
        b.department,
        b.department_id,
        c.user_id,
        c.title,
        c.first_name,
        c.last_name
    ');
    $this->db->from('delegation_task a');
    $this->db->join('departments b', 'a.department_id = b.department_id', 'left');
    $this->db->join('system_users_view c', 'a.yourname = c.user_id', 'left');
    $this->db->where('a.delegate_to', $user_id);
    $this->db->where('a.task_status', '0');

    $query = $this->db->order_by('a.added_on', 'DESC')->get();
    $res = $query->result();

    $i = 1;

    foreach ($res as $row) {

        $addeddate = date('d-M-Y', strtotime($row->added_on));
        $time = date('H:i:s', strtotime($row->added_on));
        $addedtime = "<br><small>" . date('g:i A', strtotime($time)) . "</small>";

        $d_date = '';
        if ($row->delegated_date != '' && $row->delegated_date != '0000-00-00') {
            $d_date = date('d-M-Y', strtotime($row->delegated_date));
        }

        $seconddate = '';
        if ($row->second_date != '0000-00-00' && $row->second_date != '') {
            $seconddate = date('d-M-Y', strtotime($row->second_date));
        }

        $thirddate = '';
        if ($row->third_date != '0000-00-00' && $row->third_date != '') {
            $thirddate = date('d-M-Y', strtotime($row->third_date));
        }

        /** ATTACHMENTS **/
        $attachment = "";

        $sql2 = $this->db
            ->select('image')
            ->from('delegation_images')
            ->where('delegation_id', $row->recordid)
            ->get();

        if ($sql2->num_rows() > 0) {
            foreach ($sql2->result() as $row2) {
                if ($row2->image) {
                    $attachment .= "<a href='" . delegationfile . $row2->image . "' download class='btn btn-xs btn-default attach-btn'><i class='fa fa-download'></i> Download</a><br>";
                }
            }
        } else {
            $attachment = "<span class='text-muted'>No Attachment</span>";
        }

        /** PRIORITY **/
        $urgency_text = '';
        $urgency_badge = '';
        $urgency_filter = 0;

        if ($row->urgency == 1) {
            $urgency_text = 'HIGH';
            $urgency_badge = "<span class='priority-badge priority-high'>HIGH</span>";
            $urgency_filter = 1;
        } else if ($row->urgency == 2) {
            $urgency_text = 'MEDIUM';
            $urgency_badge = "<span class='priority-badge priority-medium'>MEDIUM</span>";
            $urgency_filter = 2;
        } else if ($row->urgency == 3) {
            $urgency_text = 'LOW';
            $urgency_badge = "<span class='priority-badge priority-low'>LOW</span>";
            $urgency_filter = 3;
        } else {
            $urgency_text = 'NORMAL';
            $urgency_badge = "<span class='priority-badge priority-normal'>NORMAL</span>";
            $urgency_filter = 0;
        }

        /** USER RESPONSE **/
        $userresponse = "";
        $userremarks = "";
        $response_status_text = "PENDING";
        $response_filter = 0;
        $done_response_filter = 0;
        $pending_response_filter = 1;
        $proof_link = "";

        $remarkdone = '<a href="' . page_url . 'Delegation/add_remarks/' . $row->recordid . '" class="btn btn-danger btn-xs remark-action-btn">
                            <i class="fa fa-pencil"></i> Add Remark
                       </a>';

        $latest_response = $this->get_latest_delegation_response($row->recordid);

        if (!empty($latest_response)) {
            $response_filter = 1;

            if ((int) $latest_response['status'] === 1) {
                $sta = "<span class='status-badge status-done'>DONE</span>";
                $response_status_text = "DONE";
                $done_response_filter = 1;
                $pending_response_filter = 0;
                $remarkdone = '';
            } else {
                $sta = "<span class='status-badge status-pending'>PENDING</span>";
                $response_status_text = "PENDING";
                $done_response_filter = 0;
                $pending_response_filter = 1;

                $remarkdone = '<a href="' . page_url . 'Delegation/add_remarks/' . $row->recordid . '" class="btn btn-danger btn-xs remark-action-btn">
                                    <i class="fa fa-pencil"></i> Update Remark
                               </a>';
            }

            $responsedate = date('d-M-Y', strtotime($latest_response['updated_on']));
            $rtimes = date('H:i:s', strtotime($latest_response['updated_on']));
            $responsetime = date('g:i A', strtotime($rtimes));

            $userremarks = nl2br(htmlspecialchars($latest_response['remarks']));

            if (!empty($latest_response['attachment'])) {
                $proof_link = "<br><a href='" . delegationfile . $latest_response['attachment'] . "' download class='btn btn-xs btn-default attach-btn'><i class='fa fa-download'></i> Proof</a>";
            }

            $userresponse = $sta . "<br><small>" . $responsedate . " " . $responsetime . "</small>";
        } else {
            $userresponse = "<span class='status-badge status-pending'>PENDING</span><br><small>No response yet</small>";
            $userremarks = "<span class='text-muted'>No remarks submitted yet.</span>";
        }

        /** DUE STATUS **/
        $latest_due_date = $row->delegated_date;

        if ($row->third_date != '0000-00-00' && $row->third_date != '') {
            $latest_due_date = $row->third_date;
        } else if ($row->second_date != '0000-00-00' && $row->second_date != '') {
            $latest_due_date = $row->second_date;
        }

        $due_today_filter = 0;
        $overdue_filter = 0;
        $upcoming_filter = 0;

        if (
            $row->delegated_date == $today ||
            $row->second_date == $today ||
            $row->third_date == $today
        ) {
            $due_today_filter = 1;
        }

        if ($latest_due_date != '' && $latest_due_date != '0000-00-00' && $latest_due_date < $today) {
            $overdue_filter = 1;
        }

        if ($latest_due_date != '' && $latest_due_date != '0000-00-00' && $latest_due_date > $today) {
            $upcoming_filter = 1;
        }

        $due_status = "<span class='due-badge due-normal'>OPEN</span>";

        if ($overdue_filter == 1) {
            $due_status = "<span class='due-badge due-overdue'>OVERDUE</span>";
        } else if ($due_today_filter == 1) {
            $due_status = "<span class='due-badge due-today'>DUE TODAY</span>";
        } else if ($upcoming_filter == 1) {
            $due_status = "<span class='due-badge due-upcoming'>UPCOMING</span>";
        }

        /** TASK CARD **/
        $task_html = "
            <div class='task-title'>" . strtoupper($row->task) . "</div>
            <div class='task-meta'>
                <span>Case: " . strtoupper($row->case_no) . "</span>
                <span>" . $due_status . "</span>
            </div>
        ";

        /** REMARK HTML **/
        $remarks_html = "
            <div class='remarks-preview'>
                " . $userremarks . $proof_link . "
            </div>
        ";

        $scheduler_data[] = array(
            'sr_no' => $i,
            'timestamp' => $addeddate . $addedtime,
            'delegated_to' => strtoupper($row->title . " " . $row->first_name . " " . $row->last_name),
            'caseno' => strtoupper($row->case_no),
            'task' => $task_html,
            'priority' => $urgency_badge,
            'attachment' => $attachment,
            'delegated_date' => $d_date,
            'second_date' => $seconddate,
            'third_date' => $thirddate,
            'remarkdone' => $remarkdone,
            'response' => $userresponse,
            'remarks' => $remarks_html,

            /** Hidden fields for KPI filters **/
            'urgency_raw' => $urgency_filter,
            'due_today_filter' => $due_today_filter,
            'overdue_filter' => $overdue_filter,
            'upcoming_filter' => $upcoming_filter,
            'response_filter' => $response_filter,
            'done_response_filter' => $done_response_filter,
            'pending_response_filter' => $pending_response_filter,
            'response_status_text' => $response_status_text
        );

        $i++;
    }

    $results = array(
        "sEcho" => 1,
        "iTotalRecords" => count($scheduler_data),
        "iTotalDisplayRecords" => count($scheduler_data),
        "aaData" => $scheduler_data
    );

    echo json_encode($results);
}


public function task_delegated_to_you_summary()
{
    date_default_timezone_set("Asia/Kolkata");

    $user_id = $this->session->userdata['logged_in']['user_id'];
    $today = date('Y-m-d');

    $this->db->select('
        a.id,
        a.urgency,
        a.second_date,
        a.third_date,
        a.task_status,
        a.delegated_date,
        a.added_on
    ');
    $this->db->from('delegation_task a');
    $this->db->where('a.delegate_to', $user_id);
    $this->db->where('a.task_status', '0');

    $query = $this->db->get();

    $total_open = 0;
    $due_today = 0;
    $overdue = 0;
    $upcoming = 0;
    $high_priority = 0;
    $responded = 0;
    $pending_response = 0;

    foreach ($query->result() as $row) {

        $total_open++;

        if ($row->urgency == 1) {
            $high_priority++;
        }

        if (
            $row->delegated_date == $today ||
            $row->second_date == $today ||
            $row->third_date == $today
        ) {
            $due_today++;
        }

        $latest_due_date = $row->delegated_date;

        if ($row->third_date != '0000-00-00' && $row->third_date != '') {
            $latest_due_date = $row->third_date;
        } else if ($row->second_date != '0000-00-00' && $row->second_date != '') {
            $latest_due_date = $row->second_date;
        }

        if ($latest_due_date != '' && $latest_due_date != '0000-00-00' && $latest_due_date < $today) {
            $overdue++;
        }

        if ($latest_due_date != '' && $latest_due_date != '0000-00-00' && $latest_due_date > $today) {
            $upcoming++;
        }

        $latest_response = $this->get_latest_delegation_response($row->id);

        if (!empty($latest_response)) {
            $responded++;
        } else {
            $pending_response++;
        }
    }

    $response = array(
        'total_open' => $total_open,
        'due_today' => $due_today,
        'overdue' => $overdue,
        'upcoming' => $upcoming,
        'high_priority' => $high_priority,
        'responded' => $responded,
        'pending_response' => $pending_response
    );

    echo json_encode($response);
}
	public function update_followup_remarks(){
	
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   $task = $this->uri->segment(3);
		   $donenotdoneontime = $this->input->post('timeornot');
		   $data=
			array('task_id'=>$task,
			'followup_remarks'=>strtoupper($this->input->post('remarks')),
			'doneontimeornot'=>$donenotdoneontime,
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			
			$res = $this->db->insert('delegated_task_followup',$data);
			if($res)
			{
				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
				redirect(page_url.'Delegation/delegation_dashboard');
				}
			
		
}

public function update_followup_remarksoldddd(){
	
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   $task = $this->uri->segment(3);
		   $data=
			array('task_id'=>$task,
			'followup_remarks'=>strtoupper($this->input->post('remarks')),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			
			$res = $this->db->insert('delegated_task_followup',$data);
			if($res)
			{
				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
				redirect(page_url.'Delegation/delegation_dashboard');
				}
			
		
}

public function update_second_followupdate() {
    $user_id = $this->session->userdata['logged_in']['user_id'];
    date_default_timezone_set("Asia/Kolkata");
    $added_time = date('Y-m-d H:i:s');
    $task = $this->uri->segment(3);
    $second_date = $this->input->post('second_followdate');
    $data = array('second_date' => $second_date);

    $this->db->where('id', $task);
    $res = $this->db->update('delegation_task', $data);

    if ($res) {
        $this->db->select('a.id as recordid, a.task_status, a.second_date, a.third_date, a.yourname, a.department_id, a.delegate_to, a.task, a.image, a.delegated_date, a.case_no, a.added_on, b.department, b.department_id, c.user_id, c.title, c.first_name, c.last_name, c.contact_number, c.email, d.first_name as delegated_by, d.last_name as delegated, d.email as delegatoremail')
                 ->from('delegation_task a')
                 ->join('departments b', 'a.department_id=b.department_id', 'left')
                 ->join('system_users_view c', 'a.delegate_to=c.user_id', 'left')
                 ->join('system_users_view d', 'a.yourname=d.user_id', 'left')
                 ->where('a.id', $task);

        $res = $this->db->get();
        foreach ($res->result() as $rows);

        $nextduedate = date('d-m-Y', strtotime($rows->second_date));
        $delegatedtask = ucwords(strtolower($rows->task));
        $emailMessage = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif; background-color: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 8px;">
            <tr>
                <td style="background-color: #4872b8; padding: 10px; text-align: center;">
                    <img src="https://shubhampack.com/wp-content/uploads/2021/05/Logo.png" width="160" alt="Shubham Packs" style="display: block; margin: 0 auto;" />
                </td>
            </tr>
            <tr>
                <td style="padding: 15px; background-color: #ffffff;">
                    <h2 style="color: #333333; font-size: 20px; margin: 0; text-align: center;">TASK FOLLOW-UP NOTIFICATION</h2>
                    <hr style="border: 0; border-top: 1px solid #e0e0e0; margin: 15px 0;">
                    <p style="color: #555555; font-size: 14px; margin: 0; line-height: 1.6;">
                        <strong>Dear ' . $rows->first_name . ' ' . $rows->last_name . ',</strong>
                        <br>
                        A new follow-up date has been updated for your task. Below are the details:
                    </p>
                    <p style="color: #555555; font-size: 14px; margin: 15px 0; line-height: 1.6;">
                        <strong>Task:</strong> ' . $delegatedtask . '
                    </p>
                    <p style="color: #555555; font-size: 14px; margin: 15px 0; line-height: 1.6;">
                        <strong>Next Due Date:</strong> ' . $nextduedate . '
                    </p>
                    <p style="color: #555555; font-size: 14px; margin: 15px 0; line-height: 1.6;">
                        Kindly log in to your PMS to update the progress on the task at the earliest.
                    </p>
                    <hr style="border: 0; border-top: 1px solid #e0e0e0; margin: 15px 0;">
                    <p style="color: #555555; font-size: 13px; text-align: center; margin: 0; line-height: 1.6;">
                        If you have any questions, contact us at <a href="mailto:' . $row->delegatoremail . '" style="color: #4872b8; text-decoration: none;">' . $row->delegatoremail . '</a>.
                    </p>
                </td>
            </tr>
            <tr>
                <td style="background-color: #4872b8; color: #ffffff; padding: 10px; text-align: center; font-size: 12px;">
                    &copy; ' . date("Y") . ' Shubham Packs. All rights reserved.
                </td>
            </tr>
        </table>';


        // Sending email
        $this->load->library('email');
        $this->email->set_mailtype("html");
        $this->email->to($rows->email);
        $this->email->bcc('mangleshup@gmail.com');
        $this->email->from('taskmanagement@shubhampack.com', 'Shubham Flexible Packaging');
        $this->email->subject('Task Follow-Up Notification');
        $this->email->message($emailMessage);
        $this->email->send();

        $this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000;">Thank you, the second follow-up date has been successfully updated and the email notification has been sent.</span></div>');
        redirect(page_url . 'Delegation/delegation_dashboard');
    }
}

public function update_third_followupdate() {
    $user_id = $this->session->userdata['logged_in']['user_id'];
    date_default_timezone_set("Asia/Kolkata");
    $added_time = date('Y-m-d H:i:s');
    $task = $this->uri->segment(3);
    $third_date = $this->input->post('third_followdate');
    $data = array('third_date' => $third_date);

    $this->db->where('id', $task);
    $res = $this->db->update('delegation_task', $data);

    if ($res) {
        $this->db->select('a.id as recordid, a.task_status, a.second_date, a.third_date, a.yourname, a.department_id, a.delegate_to, a.task, a.image, a.delegated_date, a.case_no, a.added_on, b.department, b.department_id, c.user_id, c.title, c.first_name, c.last_name, c.contact_number, c.email, d.first_name as delegated_by, d.last_name as delegated')
                 ->from('delegation_task a')
                 ->join('departments b', 'a.department_id=b.department_id', 'left')
                 ->join('system_users_view c', 'a.delegate_to=c.user_id', 'left')
                 ->join('system_users_view d', 'a.yourname=d.user_id', 'left')
                 ->where('a.id', $task);

        $res = $this->db->get();
        foreach ($res->result() as $rows);

        $nextduedate = date('d-m-Y', strtotime($rows->third_date));
        $emailMessage = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif; background-color: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 8px;">
            <tr>
                <td style="background-color: #4872b8; padding: 10px; text-align: center;">
                    <img src="https://shubhampack.com/wp-content/uploads/2021/05/Logo.png" width="160" alt="Shubham Packs" style="display: block; margin: 0 auto;" />
                </td>
            </tr>
            <tr>
                <td style="padding: 15px; background-color: #ffffff;">
                    <h2 style="color: #333333; font-size: 20px; margin: 0; text-align: center;">THIRD FOLLOW-UP NOTIFICATION</h2>
                    <hr style="border: 0; border-top: 1px solid #e0e0e0; margin: 15px 0;">
                    <p style="color: #555555; font-size: 14px; margin: 0; line-height: 1.6;">
                        <strong>Dear ' . $rows->first_name . ' ' . $rows->last_name . ',</strong>
                        <br>
                        The third follow-up date has been updated for your task. Below are the details:
                    </p>
                    <p style="color: #555555; font-size: 14px; margin: 15px 0; line-height: 1.6;">
                        <strong>Task:</strong> ' . $rows->task . '
                    </p>
                    <p style="color: #555555; font-size: 14px; margin: 15px 0; line-height: 1.6;">
                        <strong>Next Due Date:</strong> ' . $nextduedate . '
                    </p>
                    <p style="color: #555555; font-size: 14px; margin: 15px 0; line-height: 1.6;">
                        Kindly log in to your PMS to update the progress on the task at the earliest.
                    </p>
                    <hr style="border: 0; border-top: 1px solid #e0e0e0; margin: 15px 0;">
                     <p style="color: #555555; font-size: 13px; text-align: center; margin: 0;">
                        If you have any questions, contact us at <a href="mailto:'.$row->delegatoremail.'" style="color: #4872b8; text-decoration: none;">'.$row->delegatoremail.'</a>.
                    </p>
                </td>
            </tr>
            <tr>
                <td style="background-color: #4872b8; color: #ffffff; padding: 10px; text-align: center; font-size: 12px;">
                    &copy; ' . date("Y") . ' Shubham Packs. All rights reserved.
                </td>
            </tr>
        </table>';

        // Sending email
        $this->load->library('email');
        $this->email->set_mailtype("html");
        $this->email->to($rows->email);
        $this->email->cc('mangleshup@gmail.com');
        $this->email->from('taskmanagement@shubhampack.com', 'Shubham Flexible Packaging');
        $this->email->subject('Third Follow-Up Notification');
        $this->email->message($emailMessage);
        $this->email->send();

        $this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000;">Thank you, the third follow-up date has been successfully updated and the email notification has been sent.</span></div>');
        redirect(page_url . 'Delegation/delegation_dashboard');
    }
}


public function update_task_status()
{
    $status = $this->input->post('status');
    $recordid = $this->input->post('recordid');
    date_default_timezone_set("Asia/Kolkata");

    // Debug inputs
    log_message('debug', 'Received status=' . $status . ', recordid=' . $recordid);

    if ($status == 2) {
        echo "redirect";
        exit;
    }

    $date = date('Y-m-d');
    $query = $this->db->select('delegated_date,second_date,third_date')
                      ->from('delegation_task')
                      ->where('id', $recordid)
                      ->get();

    if ($query->num_rows() == 0) {
        echo "Error: No record found with the given ID.";
        exit;
    }

    $row = $query->row();

    $firstdate = $row->delegated_date;
    $second_date = $row->second_date;
    $third_date = $row->third_date;

    if ($date == $firstdate || $date < $firstdate) {
        $ontime = "1";
    } elseif ($second_date != '0000-00-00' && ($second_date == $date || $second_date > $date)) {
        $ontime = "1";
    } elseif ($third_date != '0000-00-00' && ($third_date == $date || $third_date > $date)) {
        $ontime = "1";
    } else {
        $ontime = "0";
    }

    $data = array(
        'task_status' => $status,
        'task_completed_time' => date('Y-m-d H:i:s'),
        'done_ontime_or_late' => $ontime
    );

    $updated = $this->db->where('id', $recordid)->update('delegation_task', $data);
    if (!$updated) {
        log_message('error', 'Failed to update delegation_task for ID: ' . $recordid);
        echo "Error: Failed to update the record.";
        exit;
    }

    // Fetch additional details for the WhatsApp message
    $res = $this->db->select('a.id as recordid,a.task_status,a.second_date, a.third_date, a.yourname, a.department_id, a.delegate_to, a.task,a.image, a.delegated_date, a.case_no, a.added_on, b.department,b.department_id,c.user_id, c.title, c.first_name, c.last_name, c.contact_number, d.first_name as delegated_by, d.last_name as delegated, d.title as delegatortitle')
                    ->from('delegation_task a')
                    ->join('departments b', 'a.department_id=b.department_id', 'left')
                    ->join('system_users_view c', 'a.delegate_to=c.user_id', 'left')
                    ->join('system_users_view d', 'a.yourname=d.user_id', 'left')
                    ->where('a.id', $recordid)
                    ->get();

    if ($res->num_rows() == 0) {
        echo "Error: Unable to fetch task details.";
        exit;
    }

    $rows = $res->row();
    $delegatedtopersonname = $rows->title . " " . $rows->first_name . " " . $rows->last_name;
    $usercontactno = $rows->contact_number;
    $delegatorinfo = $rows->delegatortitle . " " . $rows->delegated_by . " " . $rows->delegated;

    $smsmessage1 = "Dear {$delegatedtopersonname},

We are pleased to inform you that the following task has been marked as **COMPLETED**:

🔹 **Task Details:** {$rows->task}
🔹 **Assigned To:** {$delegatedtopersonname}
🔹 **Assigned By:** {$delegatorinfo} 
🔹 **Completion Status:** Task successfully marked as done.

This task has been completed under the supervision of {$rows->delegated_by}.

Thank you for your cooperation.

Best Regards,
Team Shubham Packs 🚀";

    // WhatsApp Integration
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    $post = array(
        'receiverMobileNo' => '91' . $usercontactno,
        'username' => whatsappuser1,
        'password' => whatsapppass1,
        'message' => strip_tags($smsmessage1)
    );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        log_message('error', 'cURL Error: ' . curl_error($ch));
    } else {
        log_message('info', 'WhatsApp Message Sent: ' . $result);
    }
    curl_close($ch);

    echo "refresh"; // Send the correct response for page reload
    exit;
}



public function update_task_statusoldddd()
	{
		
		$status = $this->input->post('status');
		$recordid = $this->input->post('recordid');
		$table = "delegation_master";
		if($status=='2'){
				echo "redirect"; exit;
			}else{
			$data = array('task_status'=>$status);
			$this->db->where('id',$recordid);
			$this->db->update('delegation_task',$data);
			echo "Thank You! This task marked as done."; exit;
			
			}
			
			
		}
public function re_assign_task(){
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('your_name', 'your_name', 'required|trim');
	$this->form_validation->set_rules('department', 'department', 'required|trim');
	$this->form_validation->set_rules('delegate_to', 'delegate_to', 'required|trim');
	$this->form_validation->set_rules('task', 'task', 'required|trim');
	$this->form_validation->set_rules('work_completion_date', 'work_completion_date', 'required|trim');
	
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('delegation/re-assign');
			}else
		{
			$delegated_by  = $this->input->post('delegated_by');
			if($delegated_by==66){
				$user_id = 66;
			}else if($delegated_by==67){
				$user_id = 67;
			}else{
			$user_id =$this->session->userdata['logged_in']['user_id'];		
			}
		   date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   $photo=$_FILES['image']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$screenshot=time().'.'.$cat_image;
				move_uploaded_file($_FILES["image"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/image_bank/delegation/' . $screenshot);
			}else
			{
				$screenshot=$this->input->post('oldfile');
				}	
		   
		   $query = $this->db->select('id')->from('delegation_task')->get();
		   $res = count($query->result());
		   if($res<=0){
			   $caseno = "PD-1";
		   }else{
			   $caseno= "PD-".$res;
		   }
		   $delegated_task_id = $this->input->post('delegated_task_id');
		   $this->db->where('id',$delegated_task_id);
		   $this->db->delete('delegation_task');
		   
		   $data=
			array('yourname'=>strtoupper($this->input->post('your_name')),
			'department_id'=>strtoupper($this->input->post('department')),
			'delegate_to'=>strtoupper($this->input->post('delegate_to')),
			'task'=>strtoupper($this->input->post('task')),
			'image'=>$screenshot,
			'delegated_date'=>date('Y-m-d',strtotime($this->input->post('work_completion_date'))),
			'added_by'=>$user_id,
			'case_no'=>$caseno,
			'added_on'=>$added_time);
			
			$res = $this->db->insert('delegation_task',$data);
			$last_id = $this->db->insert_id();
			if($res)
			{
				$prioritydate= date('d-m-Y',strtotime($this->input->post('work_completion_date')));
				$qry = $this->db->select('first_name')->from('system_users_view')->where('user_id',$this->input->post('your_name'))->get();
				foreach($qry->result() as $assignedto);
				
				$query = $this->db->select('user_id, email, contact_number, first_name, last_name')->from('system_users_view')->where('user_id',$this->input->post('delegate_to'))->get();
				foreach($query->result() as $rows);
				$userresponseurl = page_url."User/delegation_response/".$last_id;
			$contactnumber = $rows->contact_number;
			
			$q1 = $this->db->select('*')->from('email_sms_whatsapp_template')->where('sms_id','1')->get();
			foreach($q1->result() as $smsdata);
			
			$task = strtoupper($this->input->post('task'));	
			$smsmessage = $smsdata->first_field." ".$rows->first_name."\n".$smsdata->third_field."\n".$assignedto->first_name."\nTASK:".$task."\n".$smsdata->sixth_field."\n".$prioritydate." ".$smsdata->eighth_field."\n Presto Testing Instruments.";
			
		
			//$msg = $smsdata->first_field." ".$rows->first_name.",\n ".$assignedto->first_name."\n ".$rowdata->third_field." ".$task."\n ".$smsdata->fourth_field." ".date('d-m-Y',strtotime($this->input->post('work_completion_date')))." as it is Most Urgent & update.\n Team,\n PRESTO \n Click here to response ".$userresponseurl;
			
			/** CHECK THE PERMISSION OF THE NOTIFICATIONS**/
			$qrr = $this->db->select('module_id, user_id, sms, email, whatsaap')->from('module_email_sms_whatsapp_notofication')->where('module_id','1')->where('user_id',$this->input->post('delegate_to'))->get();
			
			if($qrr->num_rows()>0){
			foreach($qrr->result() as $accesscheck);
	if($accesscheck->sms=='1'){		
/**** SMS INTEGRATION***/

		$postData = array(
    'authkey' => '266631AuRXQ3UyZ5c839e9b',
    'mobiles' => $contactnumber,
    'message' => $smsmessage,
    'sender' => 'PRESTQ',
	'DLT_TE_ID'=>"1407162400116293568",
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
	if($accesscheck->whatsaap=='1'){
	    
	    	$q1 = $this->db->select('*')->from('email_sms_whatsapp_template')->where('sms_id','3')->get();
			foreach($q1->result() as $smsdata);
			$completiondate = date('d-m-Y',strtotime($this->input->post('work_completion_date')));
			$smsmessage = $smsdata->first_field." ".$rows->first_name."\n ".$smsdata->third_field."\n ".$assignedto->first_name."\n TASK: ".$task." \n ".$smsdata->sixth_field." \n ".date('d-m-Y',strtotime($this->input->post('work_completion_date')))." ".$smsdata->eighth_field." \n ";
	
	$smsmessage1 = "Dear ".$rows->first_name.",<br>
Important Task delegated to you ⏱️

TASK: *".$task."* 
Complete it by 
*".$completiondate."*. 

Shubham Flexible Packaging 🚀";
 /**WHATSAPP INTEGRATION**/
					
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					// 'receiverMobileNo' => $phone_no,
					'receiverMobileNo' => '91'.$contactnumber,
					'username' => whatsappuser,
					'password' => whatsapppass,
					'message'=>strip_tags($smsmessage1)		
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
	    
	    $q1 = $this->db->select('*')->from('email_sms_whatsapp_template')->where('sms_id','2')->get();
			foreach($q1->result() as $smsdata);
			
			$smsmessage = $smsdata->first_field." ".$rows->first_name."<br><br> ".$smsdata->third_field."<br><br> ".$assignedto->first_name."<br><br> TASK: ".$task." <br><br> ".$smsdata->sixth_field." <br><br> ".date('d-m-Y',strtotime($this->input->post('work_completion_date')))." ".$smsdata->eighth_field." <br><br> ";

/** EMAIL INTEGRATION **/

$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
				   <tr>
					<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://gamavis.com/PMS/assets/images/shubhampack.png" width="200px;" alt="Shubham Pack" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>WORK DELEGATION</strong><br></td>
					  </tr> 
					  <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br>Dear, '.$rows->first_name.' '.$rows->last_name.' <br></td>
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
				
				$subjectname = "WORK DELEGATION NOTIFICATION";
					$this->email->set_mailtype("html");
					$this->email->to($rows->email);
					//$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('taskmanagement@shubhampack.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();
					
/** EMAIL INTEGRATION**/
	
			}
					$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
					redirect(page_url.'Delegation/delegation_task');
				}
		   
			
			}
	}


public function delegated_task_history(){
	$this->load->view('delegation/delegated_task_history');
}
	public function delegated_task_history_list()
	{
	    if($this->uri->segment(3)){
	      $user_id = $this->uri->segment(3);   
	    }else{
	    $user_id =$this->session->userdata['logged_in']['user_id'];	    
	    }
		
		
		if($user_id=='114'){
		    $userinfo = array('61','114');
		}
		
		
		$scheduler_data = array();
		$this->db->select('a.id as recordid, a.second_date, a.third_date, a.task_status, a.yourname, a.department_id, a.delegate_to, a.task,a.image, a.delegated_date, a.case_no, a.added_on, b.department,b.department_id,c.user_id, c.title, c.first_name, c.last_name, d.first_name as delegatedby, d.last_name as d_lastname')->from('delegation_task a')->join('departments b','a.department_id=b.department_id','left')->join('system_users_view c','a.delegate_to=c.user_id','left')->join('system_users_view d','a.yourname=d.user_id','left');
		if($user_id=='114'){
		    
		    $this->db->where_in('a.yourname',$userinfo);
		}else{
		$this->db->where('a.yourname',$user_id);
		}
		$this->db->where('a.task_status',1);
		$query = $this->db->order_by('a.added_on','desc')->get();
		$res = $query->result();
		
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			if($row->second_date!=='0000-00-00'){
				$seconddate = date('d-M-Y',strtotime($row->second_date));
			}else{
				$seconddate="";
			}
			
			if($row->third_date!=='0000-00-00'){
				$thirddate = date('d-M-Y',strtotime($row->third_date));
			}else{
				$thirddate="";
			}
			
			
			
			/**USER RESPONSE**/
			$userresponse = "";
			$latest_response = $this->get_latest_delegation_response($row->recordid);
			if(!empty($latest_response)){
				$responsedate = date('d-M-Y', strtotime($latest_response['updated_on']));
				$rtimes = date('H:i:s', strtotime($latest_response['updated_on']));
				$responsetime = "<br>". date('g:i A', strtotime($rtimes));
				$statusbadge = ((int) $latest_response['status'] === 1)
					? "<span class='btn btn-success btn-xs'>DONE</span>"
					: "<span class='btn btn-danger btn-xs'>PENDING</span>";
				$userresponse = $statusbadge . "<br>" . nl2br(htmlspecialchars($latest_response['remarks'])) . "<br>" . $responsedate . $responsetime;
			}
			/**USER RESPONSE**/
			
			
			
			$scheduler_data[] = array('sr_no'=>$i,
			'timestamp'=>$addeddate.$addedtime,
			'delegated_to'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'caseno'=>strtoupper($row->case_no),
			'delegatedby'=>strtoupper($row->delegatedby." ".$row->d_lastname),
			'task'=>strtoupper($row->task),
			'delegated_date'=>date('d-M-Y', strtotime($row->delegated_date)),
			'second_date'=>$seconddate,
			'third_date'=>$thirddate,
			'response'=>$userresponse);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}

public function set_notification_permission(){
	$this->load->view('delegation/set_permission');
}	

function assigncapabilities()
{
	$user_id =$this->session->userdata['logged_in']['user_id'];	
	$userid=$this->uri->segment('3');
	$module=$this->input->post('module');
	
	
	for($i=0;$i<count($module); $i++)
	{
	$moduleid=$module[$i];
	if(isset($_REQUEST['sms'.$moduleid])){
		$sms = $_REQUEST['sms'.$moduleid];
	
	if($sms==''){
		$smsvalue = "0";
	}else{
		$smsvalue=$sms;
	}
	}else{
		$smsvalue = "0";
	}
	
	if(isset($_REQUEST['whatsapp'.$moduleid])){
		$whatsapp = $_REQUEST['whatsapp'.$moduleid];
	if($whatsapp==''){
		$whatsappvalue = "0";
	}else{
		$whatsappvalue=$whatsapp;
	}
	}else{
		$whatsappvalue="0";
	}
	if(isset($_REQUEST['email'.$moduleid])){
		$email = $_REQUEST['email'.$moduleid];
	if($email==''){
		$emailvalue = "0";
	}else{
		$emailvalue=$email;
	}
	}else{
		$emailvalue="0";
	}
	
	
	
	  $data=array('user_id'=>$userid,
	  'module_id'=>$moduleid,
	  'sms'=>$smsvalue,
	  'email'=>$emailvalue,
	  'whatsaap'=>$whatsappvalue,
	  'added_by'=>$user_id,
	  'added_on'=>date('Y-m-d h:i:s'));
	  //echo "<pre>"; print_r($data); exit;
	  $query = $this->db->select('module_id, user_id, id')->from('module_email_sms_whatsapp_notofication')->where('module_id',$moduleid)->where('user_id',$userid)->get();
	  if($query->num_rows()>0){
		  foreach($query->result() as $rows);
		  $this->db->where('id',$rows->id);
		  $this->db->update('module_email_sms_whatsapp_notofication',$data);
	  }else{
		$this->db->insert('module_email_sms_whatsapp_notofication',$data);
	    $lid=$this->db->insert_id();  
	  }
	  
	  
	  
	}
				
		$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000">Thank You! Notification Permission has been Set.</div></div>');
		redirect(page_url.'Master/User_management/userwise_permission_dashboard');
				
				
				
}
public function edit_permission(){
	$this->load->view('delegation/edit_permission');
}	

function updateassigncapabilities()
{
	$user_id =$this->session->userdata['logged_in']['user_id'];	
	$userid=$this->uri->segment('3');
	$module=$this->input->post('module');
	
	
	for($i=0;$i<count($module); $i++)
	{
	$moduleid=$module[$i];
	if(isset($_REQUEST['sms'.$moduleid])){
		$sms = $_REQUEST['sms'.$moduleid];
	
	if($sms==''){
		$smsvalue = "0";
	}else{
		$smsvalue=$sms;
	}
	}else{
		$smsvalue = "0";
	}
	
	if(isset($_REQUEST['whatsapp'.$moduleid])){
		$whatsapp = $_REQUEST['whatsapp'.$moduleid];
	if($whatsapp==''){
		$whatsappvalue = "0";
	}else{
		$whatsappvalue=$whatsapp;
	}
	}else{
		$whatsappvalue="0";
	}
	if(isset($_REQUEST['email'.$moduleid])){
		$email = $_REQUEST['email'.$moduleid];
	if($email==''){
		$emailvalue = "0";
	}else{
		$emailvalue=$email;
	}
	}else{
		$emailvalue="0";
	}
	
	
	
	  $data=array('user_id'=>$userid,
	  'module_id'=>$moduleid,
	  'sms'=>$smsvalue,
	  'email'=>$emailvalue,
	  'whatsaap'=>$whatsappvalue,
	  'added_by'=>$user_id,
	  'added_on'=>date('Y-m-d h:i:s'));
	  
	  $query = $this->db->select('module_id, user_id, id')->from('module_email_sms_whatsapp_notofication')->where('module_id',$moduleid)->where('user_id',$userid)->get();
	  if($query->num_rows()>0){
		  foreach($query->result() as $rows);
		  $this->db->where('id',$rows->id);
		  $this->db->update('module_email_sms_whatsapp_notofication',$data);
	  }else{
		$this->db->insert('module_email_sms_whatsapp_notofication',$data);
	    $lid=$this->db->insert_id();  
	  }
	  
	  
	}
				
		$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000">Thank You! Notification Permission has been modified.</div></div>');
		redirect(page_url.'Master/User_management/userwise_permission_dashboard');
				
				
				
}
public function audit_dashbaord(){
	$this->load->view('delegation/audit-report');
}

public function calls_delegation(){
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('your_name', 'your_name', 'required|trim');
	$this->form_validation->set_rules('department', 'department', 'required|trim');
	$this->form_validation->set_rules('delegate_to', 'delegate_to', 'required|trim');
	$this->form_validation->set_rules('customer_name', 'customer_name', 'required|trim');


	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('delegation/calls_delegation');
			}else
		{
		   date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   
		  $data=
			array(
			'department_id'=>strtoupper($this->input->post('department')),
			'employee_id'=>strtoupper($this->input->post('delegate_to')),
			'customer_name'=>strtoupper($this->input->post('customer_name')),
			'contact_number'=>strtoupper($this->input->post('contact_number')),
			'email'=>strtoupper($this->input->post('email')),
			'message'=>strtoupper($this->input->post('message')),
			'status'=>'0',
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			
			$res = $this->db->insert('calls_delegation',$data);
			if($res)
			{
			    
			     $query= $this->db->select('first_name, last_name, contact_number, email, department_id')->from('system_users_view')->where('user_id',$this->input->post('delegate_to'))->get();
			    foreach($query->result() as $rows);
			    
			    $employee_name = ucfirst($rows->first_name)." ".ucfirst($rows->last_name);
			    $contactnumber = $this->input->post('contact_number');
			    $customername = $this->input->post('customer_name');
			    $message = $this->input->post('message');
			    $department = $rows->department_id;
				if($department==44){
					$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
				   <tr>
					<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://www.prestomitr.com/assets/images/logo-1.png" width="200px;" alt="Prestomitr" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>CALL DELEGATED TO YOU</strong><br></td>
					  </tr> 
					  <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br>Dear '.$employee_name.',
						Please call back Mr/Ms. '.$customername.' phone number regarding '.$message.'
						<br>This call has been assigned to you from the front desk. Please CLOSE from the Dashboard thereafter. <br>
						Team Testronix</td>
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
				
				$subjectname = "CALL DELEGATED TO YOU ";
					$this->email->set_mailtype("html");
					$this->email->to($rows->email);
					//$this->email->cc('mangleshup@gmail.com');
					$this->email->from('mitr@prestomitr.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();
				}else{
			    
			    $Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
				   <tr>
					<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://www.prestomitr.com/assets/images/logo-1.png" width="200px;" alt="Prestomitr" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>CALL DELEGATED TO YOU</strong><br></td>
					  </tr> 
					  <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br>Dear '.$employee_name.',
						Please call back Mr/Ms. '.$customername.' phone number regarding '.$message.'
						<br>This call has been assigned to you from the front desk. Please CLOSE from the Dashboard thereafter. <br>
						Team Presto</td>
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
				
				    $subjectname = "CALL DELEGATED TO YOU ";
					$this->email->set_mailtype("html");
					$this->email->to($rows->email);
					//$this->email->cc('mangleshup@gmail.com');
					$this->email->from('mitr@prestomitr.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();
			    
			    }
			    
			    $smsmessage = "Dear."." ".$customername.",\n\nThank you for calling Presto Testing Instruments."."\n\nWe are arranging a call back from ".$employee_name." ji ( Mob- ".$rows->contact_number.")\n\nRegarding ".$message."\n\nThank You,\n Presto Testing Instruments.";

/**** SMS INTEGRATION***/

	$postData = array(
    'authkey' => '266631AuRXQ3UyZ5c839e9b',
    'mobiles' => $contactnumber,
    'message' => $smsmessage,
    'sender' => 'PRESTQ',
	'DLT_TE_ID'=>"1407163522330109626",
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
			    
				
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
                redirect(page_url.'Delegation/calls_delegation');
				}
		   
			
			}
	}
	public function calls_delegated_dashboard(){
	$this->load->view('delegation/calls_delegated_dashboard');
}
public function total_Calls_delegated(){
	$this->load->view('delegation/all_calls_delegated_dashboard');
}

	public function delegated_calls_list()
	{
	    $user_id =$this->session->userdata['logged_in']['user_id'];
	
		
		if($user_id=='234'){
		    $userinfo = array('234','66');
		}
		if($user_id=='63'){
		    $userinfo = array('63','67');
		}
		$scheduler_data = array();
		$this->db->select('a.*, b.first_name, b.last_name, c.first_name as delegatedby, c.last_name as delegatedby_lastname')->from('calls_delegation a')->join('system_users_view b','a.employee_id=b.user_id','left')->join('system_users_view c','a.added_by=c.user_id','left')->join('departments d','a.department_id=d.department_id','left');
		if($user_id=='234'){
		    
		    $this->db->where_in('a.employee_id',$userinfo);
		}else if($user_id=='63'){
		    $this->db->where_in('a.employee_id',$userinfo);
		}else{
		$this->db->where('a.employee_id',$user_id);
		}
		$this->db->where('a.status',0);
		$query = $this->db->order_by('a.added_on','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		    	date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			$status = "<a href='".page_url."Delegation/update_calls_status/".$row->id."'><span class='btn btn-success btn-xs'>Mark as Done</span></a>";
			
			$notpickedup = "<a href='".page_url."Delegation/client_notreachable/".$row->id."'><span class='btn btn-warning btn-xs'>Not Reachble</span></a>";	
			$scheduler_data[] = array('sr_no'=>$i,
			'timestamp'=>$addeddate.$addedtime,
			'delegated_by'=>$row->delegatedby." ".$row->delegatedby_lastname,
			'delegated_to'=>strtoupper($row->first_name." ".$row->last_name),
			'customer_name'=>strtoupper($row->customer_name),
			'contact_number'=>strtoupper($row->contact_number),
			'email'=>strtoupper($row->email),
			'message'=>strtoupper($row->message),
			'status'=>$status." ".$notpickedup,
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


public function total_Calls_delegated_history(){
	$this->load->view('delegation/all_calls_delegated_dashboard_history');
}
public function update_calls_status(){
    
    $user_id =$this->session->userdata['logged_in']['user_id'];
        date_default_timezone_set("Asia/Kolkata");
         $added_time = date('Y-m-d H:i:s');
        $data= array('status'=>'1',
        'completion_time'=>$added_time);
        $this->db->where('id',$this->uri->segment(3));
        $this->db->update('calls_delegation',$data);
        $query = $this->db->select('customer_name, contact_number, message')->from('calls_delegation')->where('id',$this->uri->segment(3))->get();
    foreach($query->result() as $row);
    
    $customername = ucfirst(strtolower($row->customer_name));
     $contactnumber=$row->contact_number;
    
    $query1 = $this->db->select('first_name, last_name, contact_number')->from('system_users_view')->where('user_id',$user_id)->get();
    foreach($query1->result() as $userinfo);
    
    $name = ucfirst($userinfo->first_name)." ".ucfirst($userinfo->last_name);
    $usercontactnumber= $userinfo->contact_number;
    
    $smsmessage = "Dear Mr. "." ".$customername.",\n\nGreetings from Presto Testing Instruments! "."\n\nIt was a pleasure talking to you. I will share the required details soon.\n\nIf you need any further information please contact me on ".$usercontactnumber."\n\nRegards, \n".$name."\n Presto Testing Instruments.";
/**** SMS INTEGRATION***/

	$postData = array(
    'authkey' => '266631AuRXQ3UyZ5c839e9b',
    'mobiles' => $contactnumber,
    'message' => $smsmessage,
    'sender' => 'PRESTQ',
	'DLT_TE_ID'=>"1407162409640752466",
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
        
        
        
    	$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
                redirect(page_url.'Delegation/calls_delegated_dashboard');
}

	public function alldelegated_calls_list()
	{
	    $user_id =$this->session->userdata['logged_in']['user_id'];
	
		$date = date('Y-m-d')." 00-00-00";
		$d2 = date('Y-m-d', strtotime('-15 days'));
		$seconddate = $d2." 00-00-00";
		$scheduler_data = array();
		$this->db->distinct();
		$this->db->select('a.*, b.first_name, b.last_name, c.first_name as delegatedby, c.last_name as delegatedby_lastname')->from('calls_delegation a')->join('system_users_view b','a.employee_id=b.user_id','left')->join('system_users_view c','a.added_by=c.user_id','left')->join('departments d','a.department_id=d.department_id','left')->where('a.added_on BETWEEN "'.$seconddate. '" and "'.$date.'"');
		
		$query = $this->db->order_by('a.added_on','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		    	date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			if($row->status=='0'){
			    if($row->notreachable=='1'){
			        $status="<span style='color:red; font-size:14px; font-weight:bold'>Not Reachble</span>";
			    }else{
			         $status = "<span style='color:red; font-size:14px; font-weight:bold'>Pending</span>";
			    }
			   
			}else{
			    $status  ="<span style='color:green; font-size:14px; font-weight:bold'>Done</span>";
			}
			
			
			
			$date1 = date('Y-m-d',strtotime($row->added_on));
										$date2 = date('Y-m-d');

										$diff = abs(strtotime($date2) - strtotime($date1));

										$years = floor($diff / (365*60*60*24));
										$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
										$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
									

				
			$scheduler_data[] = array('sr_no'=>$i,
			'timestamp'=>$addeddate.$addedtime,
			'delegated_by'=>$row->delegatedby." ".$row->delegatedby_lastname,
			'delegated_to'=>strtoupper($row->first_name." ".$row->last_name),
			'customer_name'=>strtoupper($row->customer_name),
			'contact_number'=>strtoupper($row->contact_number),
			'email'=>strtoupper($row->email),
			'message'=>strtoupper($row->message),
			'status'=>$status,
			'days'=>$days,
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

public function alldelegated_calls_list_history()
	{
	    $user_id =$this->session->userdata['logged_in']['user_id'];
	
		
		$scheduler_data = array();
		$this->db->distinct();
		$this->db->select('a.*, b.first_name, b.last_name, c.first_name as delegatedby, c.last_name as delegatedby_lastname')->from('calls_delegation a')->join('system_users_view b','a.employee_id=b.user_id','left')->join('system_users_view c','a.added_by=c.user_id','left')->join('departments d','a.department_id=d.department_id','left');
		
		$query = $this->db->order_by('a.added_on','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		    	date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			if($row->status=='0'){
			    if($row->notreachable=='1'){
			        $status="<span style='color:red; font-size:14px; font-weight:bold'>Not Reachble</span>";
			    }else{
			         $status = "<span style='color:red; font-size:14px; font-weight:bold'>Pending</span>";
			    }
			   
			}else{
			    $status  ="<span style='color:green; font-size:14px; font-weight:bold'>Done</span>";
			}
			
			
			
			$date1 = date('Y-m-d',strtotime($row->added_on));
										$date2 = date('Y-m-d');

										$diff = abs(strtotime($date2) - strtotime($date1));

										$years = floor($diff / (365*60*60*24));
										$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
										$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
									

				
			$scheduler_data[] = array('sr_no'=>$i,
			'timestamp'=>$addeddate.$addedtime,
			'delegated_by'=>$row->delegatedby." ".$row->delegatedby_lastname,
			'delegated_to'=>strtoupper($row->first_name." ".$row->last_name),
			'customer_name'=>strtoupper($row->customer_name),
			'contact_number'=>strtoupper($row->contact_number),
			'email'=>strtoupper($row->email),
			'message'=>strtoupper($row->message),
			'status'=>$status,
			'days'=>$days,
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


public function client_notreachable(){
    $user_id =$this->session->userdata['logged_in']['user_id'];
    $data = array('notreachable'=>'1');
    $this->db->where('id',$this->uri->segment(3));
    $this->db->update('calls_delegation',$data);
    $query = $this->db->select('customer_name, contact_number, message')->from('calls_delegation')->where('id',$this->uri->segment(3))->get();
    foreach($query->result() as $row);
    
    $customername = ucfirst(strtolower($row->customer_name));
    $contactnumber = $row->contact_number;
    $message = ucfirst(strtolower($row->message));
    
    $query1 = $this->db->select('first_name, last_name, contact_number')->from('system_users_view')->where('user_id',$user_id)->get();
    foreach($query1->result() as $userinfo);
    
    $name = ucfirst($userinfo->first_name)." ".ucfirst($userinfo->last_name);
    $usercontactnumber= $userinfo->contact_number;
    
    $smsmessage = "Dear Mr. "." ".$customername.",\n\nGreetings from Presto Testing Instruments! "."\n\nI tried contacting you regarding ".$message." \nYou were unavailable.You can call me on ".$usercontactnumber." as per your convenience.\nRegards, \n".$name."\n Presto Testing Instruments.";
/**** SMS INTEGRATION***/

	$postData = array(
    'authkey' => '266631AuRXQ3UyZ5c839e9b',
    'mobiles' => $contactnumber,
    'message' => $smsmessage,
    'sender' => 'PRESTQ',
	'DLT_TE_ID'=>"1407163522320987456",
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
			    
				
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
                redirect(page_url.'Delegation/calls_delegated_dashboard');
				
}
public function your_delegated_calls_dashboard(){
	$this->load->view('delegation/your_delegated_calls_list');
}

	public function your_delegated_calls_dashboard_list()
	{
	    $user_id =$this->session->userdata['logged_in']['user_id'];
	
		
		$scheduler_data = array();
		$this->db->select('a.*, b.first_name, b.last_name, c.first_name as delegatedby, c.last_name as delegatedby_lastname')->from('calls_delegation_view a')->join('system_users_view b','a.employee_id=b.user_id','left')->join('system_users_view c','a.added_by=c.user_id','left')->join('departments d','a.department_id=d.department_id','left')->where('a.added_by',$user_id);
		
		$query = $this->db->order_by('a.added_on','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		    	date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			if($row->status=='0'){
			    if($row->notreachable=='1'){
			        $status="<span style='color:red; font-size:14px; font-weight:bold'>Not Reachble</span>";
			    }else{
			         $status = "<span style='color:red; font-size:14px; font-weight:bold'>Pending</span>";
			    }
			   
			}else{
			    $status  ="<span style='color:green; font-size:14px; font-weight:bold'>Done</span>";
			}
			
			
			
			$date1 = date('Y-m-d',strtotime($row->added_on));
										$date2 = date('Y-m-d');

										$diff = abs(strtotime($date2) - strtotime($date1));

										$years = floor($diff / (365*60*60*24));
										$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
										$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
									

				
			$scheduler_data[] = array('sr_no'=>$i,
			'timestamp'=>$addeddate.$addedtime,
			'delegated_by'=>$row->delegatedby." ".$row->delegatedby_lastname,
			'delegated_to'=>strtoupper($row->first_name." ".$row->last_name),
			'customer_name'=>strtoupper($row->customer_name),
			'contact_number'=>strtoupper($row->contact_number),
			'email'=>strtoupper($row->email),
			'message'=>strtoupper($row->message),
			'status'=>$status,
			'days'=>$days,
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

public function Calls_delegation_report_for_auditor(){
	$this->load->view('delegation/all_calls_delegated_auditor_dashboard');
}
public function Calls_delegation_report_for_auditor_list()
	{
	    $user_id =$this->session->userdata['logged_in']['user_id'];
	
		
		$scheduler_data = array();
		$this->db->select('a.*, b.first_name, b.last_name, c.first_name as delegatedby, c.last_name as delegatedby_lastname')->from('calls_delegation a')->join('system_users_view b','a.employee_id=b.user_id','left')->join('system_users_view c','a.added_by=c.user_id','left')->join('departments d','a.department_id=d.department_id','left')->where('a.status','0');
		
		$query = $this->db->order_by('a.added_on','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		    	date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			if($row->status=='0'){
			    if($row->notreachable=='1'){
			        $status="<span style='color:red; font-size:14px; font-weight:bold'>Not Reachble</span>";
			    }else{
			         $status = "<span style='color:red; font-size:14px; font-weight:bold'>Pending</span>";
			    }
			   
			}else{
			    $status  ="<span style='color:green; font-size:14px; font-weight:bold'>Done</span>";
			}
			
			
			
			$date1 = date('Y-m-d',strtotime($row->added_on));
										$date2 = date('Y-m-d');

										$diff = abs(strtotime($date2) - strtotime($date1));

										$years = floor($diff / (365*60*60*24));
										$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
										$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
									

				
			$scheduler_data[] = array('sr_no'=>$i,
			'timestamp'=>$addeddate.$addedtime,
			'delegated_by'=>$row->delegatedby." ".$row->delegatedby_lastname,
			'delegated_to'=>strtoupper($row->first_name." ".$row->last_name),
			'customer_name'=>strtoupper($row->customer_name),
			'contact_number'=>strtoupper($row->contact_number),
			'email'=>strtoupper($row->email),
			'message'=>strtoupper($row->message),
			'status'=>$status,
			'days'=>$days,
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


public function sales_support(){
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('your_name', 'your_name', 'required|trim');
	$this->form_validation->set_rules('sales_person', 'sales_person', 'required|trim');
	$this->form_validation->set_rules('remarks', 'remarks', 'required|trim');


	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('delegation/sales_help');
			}else
		{
		   date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		  
		   
		   $query = $this->db->select('id')->from('sales_support')->get();
		   $res = count($query->result());
		   if($res<=0){
			   $caseno = "SSH-1";
		   }else{
			   $caseno= "SSH-".$res;
		   }
		   
		   $data=
			array('yourname'=>strtoupper($this->input->post('your_name')),
			'employee_id'=>strtoupper($this->input->post('sales_person')),
			'customer_name'=>strtoupper($this->input->post('customer_name')),
			'remarks'=>strtoupper($this->input->post('remarks')),
			'help_needed'=>$this->input->post('help_needed'),
			'ticket_id'=>$caseno,
			'task_status'=>'1',
			'added_on'=>$added_time);
			
			$res = $this->db->insert('sales_support',$data);
			$last_id = $this->db->insert_id();
			if($res)
			{
			

        $this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
        redirect(page_url.'Delegation/sales_support');
				}
		   
			
			}
	}	

public function sales_support_dashboard(){
	$this->load->view('delegation/sales_support_dashboard');
}

	public function sales_support_list()
	{
	    $user_id =$this->session->userdata['logged_in']['user_id'];		
		$scheduler_data = array();
		$this->db->select('a.*, b.first_name, b.last_name, c.first_name as salespersonf_name, c.last_name as salespersonl_name')->from('sales_support a')->join('system_users_view b','a.yourname=b.user_id','left')->join('system_users_view c','a.employee_id=c.user_id','left');
	    $this->db->where('a.employee_id',$user_id);
		$this->db->where('a.task_status',1);
		$query = $this->db->order_by('a.added_on','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		   
			$html="";
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			if($row->sales_team_remarks==''){
			    $html.=' <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$i.'">UPDATE REMARKS</button>';
			$html.= '<div id="con-close-modal'.$i.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
           <form id="loginForm" method="post" action="'.page_url.'Delegation/update_sales_task_status/'.$row->id.'">
  
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">UPDATE STATUS AND CLOSE THE TASK</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                               
												 <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARKS</label><br>
														<span id="error_color_name" style="color:red;"></span>
                                                       <textarea class="form-control" name="remarks" id="remarks" style="width:800px" required></textarea>
                                                    </div>
                                                </div>
												
                                               
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>';
			}else{
			    $html =$row->sales_team_remarks;
			}
			
		
		$markasdone = "<a href='".page_url."Delegation/sales_mark_as_Done/".$row->id."'><span class='btn btn-success btn-xs'>Mark as done</span></a>";	
			$scheduler_data[] = array('sr_no'=>$i,
			'timestamp'=>$addeddate.$addedtime,
			'delegated_by'=>$row->first_name." ".$row->last_name,
			'delegated_to'=>strtoupper($row->salespersonf_name." ".$row->salespersonl_name),
			'caseno'=>strtoupper($row->ticket_id),
			'customer_name'=>strtoupper($row->customer_name),
			'remarks'=>$row->remarks,
			'help_needed'=>$row->help_needed,
			'update_status'=>$html,
			'markasdone'=>$markasdone);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}

public function update_sales_task_status(){
    date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
    $data = array(
    'sales_team_remarks'=>$this->input->post('remarks'),
    'updated_on'=>$added_time);
    $this->db->where('id',$this->uri->segment(3));
    $this->db->update('sales_support',$data);
    
     $this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
        redirect(page_url.'Delegation/sales_support_dashboard');
}

public function sales_mark_as_Done(){
    date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
    $data = array('task_status'=>0,
    'updated_on'=>$added_time);
    $this->db->where('id',$this->uri->segment(3));
    $this->db->update('sales_support',$data);
    
     $this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
        redirect(page_url.'Delegation/sales_support_dashboard');
}

public function sales_support_auditor_dashboard(){
	$this->load->view('delegation/sales_support_auditor_dashboard');
}

	public function sales_support_auditor_list()
	{
	    $user_id =$this->session->userdata['logged_in']['user_id'];		
		$scheduler_data = array();
		$this->db->select('a.*, b.first_name, b.last_name, c.first_name as salespersonf_name, c.last_name as salespersonl_name')->from('sales_support a')->join('system_users_view b','a.yourname=b.user_id','left')->join('system_users_view c','a.employee_id=c.user_id','left');
	   // $this->db->where('a.employee_id',$user_id);
		$this->db->where('a.task_status',1);
		$query = $this->db->order_by('a.added_on','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		   
			$html="";
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			if($row->task_status=='1'){
			   $html.= "<span style='color:red; font-weight:bold'>Pending for rreview</span>";
			}
			
		
			
			$scheduler_data[] = array('sr_no'=>$i,
			'timestamp'=>$addeddate.$addedtime,
			'delegated_by'=>$row->first_name." ".$row->last_name,
			'delegated_to'=>strtoupper($row->salespersonf_name." ".$row->salespersonl_name),
			'caseno'=>strtoupper($row->ticket_id),
			'customer_name'=>strtoupper($row->customer_name),
			'remarks'=>$row->remarks,
			'help_needed'=>$row->help_needed,
			'update_status'=>$html);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}

public function sales_support_history(){
	$this->load->view('delegation/sales_support_history');
}

	public function sales_support_history_list()
	{
	    $ui = $this->uri->segment(3);
	    $user_id =$this->session->userdata['logged_in']['user_id'];		
		$scheduler_data = array();
		$this->db->select('a.*, b.first_name, b.last_name, c.first_name as salespersonf_name, c.last_name as salespersonl_name')->from('sales_support a')->join('system_users_view b','a.yourname=b.user_id','left')->join('system_users_view c','a.employee_id=c.user_id','left');
		if($ui){
		   $this->db->where('a.employee_id',$ui); 
		}
	  
		$this->db->where('a.task_status',0);
		$query = $this->db->order_by('a.added_on','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		   
			$html="";
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			
			   $html.= $row->sales_team_remarks;
		
			
		
			
			$scheduler_data[] = array('sr_no'=>$i,
			'timestamp'=>$addeddate.$addedtime,
			'delegated_by'=>$row->first_name." ".$row->last_name,
			'delegated_to'=>strtoupper($row->salespersonf_name." ".$row->salespersonl_name),
			'caseno'=>strtoupper($row->ticket_id),
			'customer_name'=>strtoupper($row->customer_name),
			'remarks'=>$row->remarks,
			'help_needed'=>$row->help_needed,
			'update_status'=>$html);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}

	public function sales_help_ticket_data()
	{
	    $user_id =$this->session->userdata['logged_in']['user_id'];		
		$scheduler_data = array();
		$this->db->select('a.*, b.first_name, b.last_name, c.first_name as salespersonf_name, c.last_name as salespersonl_name')->from('sales_support a')->join('system_users_view b','a.yourname=b.user_id','left')->join('system_users_view c','a.employee_id=c.user_id','left');
	    $this->db->where('a.yourname',$user_id);
		//$this->db->where('a.task_status',1);
		$query = $this->db->order_by('a.added_on','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		   
			$html="";
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			if($row->task_status=='1'){
			   $html.= "<span style='color:red; font-weight:bold'>Pending for rreview</span>";
			}else{
			    $html.= $row->sales_team_remarks;
			}
			
		
			
			$scheduler_data[] = array('sr_no'=>$i,
			'timestamp'=>$addeddate.$addedtime,
			'delegated_by'=>$row->first_name." ".$row->last_name,
			'delegated_to'=>strtoupper($row->salespersonf_name." ".$row->salespersonl_name),
			'caseno'=>strtoupper($row->ticket_id),
			'customer_name'=>strtoupper($row->customer_name),
			'remarks'=>$row->remarks,
			'help_needed'=>$row->help_needed,
			'update_status'=>$html);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}
public function responebyuser()
{
    $id = $this->input->post('hidden_id1');
    $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
    $this->form_validation->set_rules('status', 'Status', 'required|trim');
    $this->form_validation->set_rules('remarks', 'Remarks', 'required|trim');

    $user_id = $this->session->userdata['logged_in']['user_id'];

    if ($this->form_validation->run() == FALSE) {
        $this->load->view('delegation/delegated_task');
    } else {
        $photo = $_FILES['workproof']['name'];
        $screenshot = '';

        if ($photo <> '') {
            $image1 = explode('.', $photo);
            $cat_image = end($image1);
            $screenshot = time() . '.' . $cat_image;
            move_uploaded_file($_FILES["workproof"]["tmp_name"], UPLOADPATH . 'delegation/' . $screenshot);
        }

        date_default_timezone_set("Asia/Kolkata");
        $added_time = date('Y-m-d H:i:s');
        $remarks = strtoupper($this->input->post('remarks'));

        $data = array(
            'task_id' => $id,
            'user_response' => strtoupper($this->input->post('remarks')),
            'task_status' => $this->input->post('status'),
            'proof' => $screenshot,
            'updated_on' => $added_time
        );

        $res = $this->db->insert('user_response_on_delegated_task', $data);


        if ($res) {

        	$taskstatusbyuser = $this->input->post('status');
        	if($taskstatusbyuser==1){
        		$sta = "Done";
        	}else{
        		$sta = "Pending";
        	}
            // Fetch task details
            $taskDetailsQuery = $this->db->select('yourname, task, delegate_to')->from('delegation_task')->where('id', $id)->get();
            $taskDetails = $taskDetailsQuery->row();

            $task = $taskDetails->task;

            // Fetch delegator details
            $delegatorQuery = $this->db->select('first_name, last_name, email, contact_number')
                ->from('system_users')
                ->where('user_id', $taskDetails->yourname)
                ->get();
            $delegator = $delegatorQuery->row();

            // Fetch assignee details
            $assigneeQuery = $this->db->select('first_name, last_name, email')->from('system_users')->where('user_id', $taskDetails->delegate_to)->get();
            $assignee = $assigneeQuery->row();

            $delegatorName = ucwords(strtolower($delegator->first_name . " " . $delegator->last_name));
            $assigneeName = ucwords(strtolower($assignee->first_name . " " . $assignee->last_name));
            $delegatorEmail = $delegator->email;
            $delegatorContact = $delegator->contact_number;

            // Prepare email content
            $emailMessage = '
            <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif; background-color: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 8px;">
                <tr>
                    <td style="background-color: #4872b8; padding: 10px; text-align: center;">
                        <img src="https://shubhampack.com/wp-content/uploads/2021/05/Logo.png" width="160" alt="Shubham Packs" style="display: block; margin: 0 auto;" />
                    </td>
                </tr>
                <tr>
                    <td style="padding: 15px; background-color: #ffffff;">
                        <h2 style="color: #333333; font-size: 20px; margin: 0; text-align: center;">TASK RESPONSE NOTIFICATION</h2>
                        <hr style="border: 0; border-top: 1px solid #e0e0e0; margin: 15px 0;">
                        <p style="color: #555555; font-size: 14px; margin: 0; line-height: 1.6;">
                            <strong>Dear ' . strtoupper($delegatorName) . ',</strong>
                            <br>
                            The following task assigned to <strong>' . strtoupper($assigneeName) . '</strong> has received a response:
                        </p>
                        <p style="color: #555555; font-size: 14px; margin: 15px 0; line-height: 1.6;">
                            <strong>Task:</strong> ' . strtoupper($task) . '<br>
                            <strong>Current Status:</strong> ' . $sta . '<br>
                            <strong>Remarks:</strong> ' . strtoupper($remarks) . '<br>
                        </p>
                        <p style="color: #555555; font-size: 14px; margin: 15px 0; line-height: 1.6;">
                            <strong>Submitted By:</strong> ' . strtoupper($assigneeName) . '
                        </p>
                        <p style="color: #555555; font-size: 14px; margin: 15px 0; line-height: 1.6;">
                            Kindly review the response and take appropriate action.
                        </p>
                        <hr style="border: 0; border-top: 1px solid #e0e0e0; margin: 15px 0;">
                        <p style="color: #555555; font-size: 13px; text-align: center; margin: 0; line-height: 1.6;">
                            If you have any questions, contact us at <a href="mailto:' . $delegatorEmail . '" style="color: #4872b8; text-decoration: none;">' . $delegatorEmail . '</a>.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="background-color: #4872b8; color: #ffffff; padding: 10px; text-align: center; font-size: 12px;">
                        &copy; ' . date("Y") . ' Shubham Packs. All rights reserved.
                    </td>
                </tr>
            </table>';

            // Send email
            $this->load->library('email');
            $this->email->set_mailtype("html");
            $this->email->to($delegatorEmail);
            $this->email->cc('mangleshup@gmail.com');
            $this->email->from('taskmanagement@shubhampack.com', 'Shubham Packs');
            $this->email->subject('Task Response Notification');
            $this->email->message($emailMessage);
            $this->email->send();

            // WhatsApp Notification
            $whatsappMessage = "Dear {$delegatorName},\n\nThe following task assigned to {$assigneeName} has received a response:\n\nTask: {$task}\nRemarks: {$remarks}\nWork Status: {$sta}\n\nPlease review the response and take necessary action.\n\nBest Regards,\nTeam Shubham Packs 🚀";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            $post = array(
                'receiverMobileNo' => '91' . $delegatorContact,
                'username' => whatsappuser1,
                'password' => whatsapppass1,
                'message' => strip_tags($whatsappMessage)
            );
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_exec($ch);
            curl_close($ch);

            // Set success message
            $this->session->set_flashdata('message', '<div class="alert alert-success">Your response has been successfully submitted, and notifications have been sent to the delegator.</div>');
            redirect(page_url . 'Delegation/delegated_task');
        }
    }
}


public function delegationdashboard(){
		$this->load->view('dashboard/delegation_dashboard');
	}

public function delegation_history(){
	

	$this->load->view('delegation/delegation_history');
}

	public function delegation_history_list()
	{
		$buttondisable = '';
		$current_user_id = (int) $this->session->userdata['logged_in']['user_id'];
		$current_user_role = (int) $this->session->userdata['logged_in']['role'];
		$is_admin_history_view = ($current_user_role === 1);
		
		$scheduler_data = array();
		$this->db->select('a.id as recordid,a.urgency,a.task_status,a.second_date, a.third_date, a.yourname, a.department_id, a.delegate_to, a.task,a.image, a.delegated_date, a.case_no, a.added_on, b.department,b.department_id,c.user_id, c.title, c.first_name, c.last_name, d.first_name as delegated_by, d.last_name as delegated')->from('delegation_task a')->join('departments b','a.department_id=b.department_id','left')->join('system_users_view c','a.delegate_to=c.user_id','left')->join('system_users_view d','a.yourname=d.user_id','left');

		if($this->uri->segment(3) != 'NA' && $this->uri->segment(3) != '' && $this->uri->segment(4) != 'NA' && $this->uri->segment(4) != '') {
			$this->db->where('a.delegated_date >=',date('Y-m-d', strtotime($this->uri->segment(3))));
			$this->db->where('a.delegated_date <=',date('Y-m-d', strtotime($this->uri->segment(4))));
		} 

		if($is_admin_history_view) {
			if($this->uri->segment(5) != '' && $this->uri->segment(5) != 'All') {
				$this->db->where('a.delegate_to',$this->uri->segment(5));
			}
		} else {
			$this->db->where('a.delegate_to', $current_user_id);
		}

		
		// $this->db->where('a.task_status',0);
		$query = $this->db->order_by('a.added_on','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		    $poorf = "";
		    $d_date = date('d-M-Y',strtotime($row->delegated_date));
			$date = date('Y-m-d');
			$seconddate = $row->second_date;
			$thirddate = $row->third_date;
			if($date==$seconddate || $date==$thirddate){
				$background = "style='background-color:yellow; color:#000;'";
			}else{
				$background = "";
			}
			
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			
			$date1 = new DateTime($row->delegated_date); 
			$date2 = new DateTime($addeddate); 
			$interval = $date1->diff($date2); 
			$days = $interval->d; 
			$sc= $days/2;
			$seconddatedeff  = round($sc);
			$td = $seconddatedeff/2;
			$thirddatediff = round($td);
			$secondplanneddate  = date('Y-m-d', strtotime($row->delegated_date."+".$seconddatedeff." days"));
			$thirdplanneddate  = date('Y-m-d', strtotime($secondplanneddate."+".$thirddatediff." days"));
			$remarks = "";
			/**LAST FOLLOW-UP REMARKS**/
			$q = $this->db->select('task_id, followup_remarks, added_on')->from('delegated_task_followup')->where('task_id',$row->recordid)->limit(1)->order_by('id','desc')->get();
			if($q->num_rows()>0){
				foreach($q->result() as $followup);
				$followupdate = date('d-M-Y', strtotime($followup->added_on));
				$times = date('H:i:s', strtotime($followup->added_on));
				$followuptime = "<br>". date('g:i A', strtotime($times)); 

				$remarks.=$followup->followup_remarks."<br>".$followupdate.$followuptime;
			}
			
			/**LAST FOLLOW-UP REMARKS**/
			
			/**USER RESPONSE**/
			$userresponse = "<span class='btn btn-danger btn-xs'>PENDING</span><br><br><small>No response yet</small>";
			$userremarks = "";
			$latest_response = $this->get_latest_delegation_response($row->recordid);
			if(!empty($latest_response)){
				if((int) $latest_response['status'] === 1)
				{
					$sta="<span class='btn btn-success btn-xs'>DONE</span>";
				}else
				{
					$sta="<span class='btn btn-danger btn-xs'>PENDING</span>";
				}

				$responsedate = date('d-M-Y', strtotime($latest_response['updated_on']));
				$rtimes = date('H:i:s', strtotime($latest_response['updated_on']));
				$responsetime = "<br>". date('g:i A', strtotime($rtimes)); 
				$userresponse=$sta."<br><br>".$responsedate.$responsetime;

				$userremarks = nl2br(htmlspecialchars($latest_response['remarks']));
				if(!empty($latest_response['attachment'])){
					$poorf="<a href='".delegationfile.$latest_response['attachment']."' download>DOWNLOAD</a>";
				}
			}


			if($this->uri->segment(6) != '' && $this->uri->segment(6) != 'All') {
				if($this->uri->segment(6) == 1) {
					if(!empty($latest_response) && (int) $latest_response['status'] === 1) {
						$data1 = 1;
					} else {
						$data1 = 0;
					}
				} else {
					if(!empty($latest_response) && (int) $latest_response['status'] === 1) {
						$data1 = 0;
					} else {
						$data1 = 1;
					}

				}
			} else {
				$data1 = 1;
			}

			// echo $data1;exit;
			
		
		
			$attachment = "";
			$sql2 = $this->db->select('image')
							 ->from('delegation_images')
							 ->where('delegation_id', $row->recordid)
							 ->get();

				if($sql2->num_rows() > 0) {
					foreach($sql2->result() as $row2) {
						if($row2->image) {
							$attachment .= "<a href='".delegationfile.$row2->image."' download>DOWNLOAD</a><br>";
						}else{
							$attachment = "";
						}
					}
				}
			
			if($row->urgency==1)
			{
				$ur="<strong style='color:red;font-weight:bold;font-size:16px;'>HIGH</strong>";
			}else if($row->urgency==2)
			{
				$ur="<strong style='color:orange;font-weight:bold;font-size:16px;'>MEDIUM</strong>";
			}else if($row->urgency==3)
			{
				$ur="<strong style='color:green;font-weight:bold;font-size:16px;'>LOW</strong>";
			}else
			{
				$ur='';
			}


			if($data1 == 1) {

				
				$scheduler_data[] = array('sr_no'=>$i,
				'timestamp'=>$addeddate.$addedtime,
				'delegated_by'=>$row->delegated_by." ".$row->delegated,
				'delegated_to'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
				'caseno'=>strtoupper($row->case_no),
				'task'=>strtoupper($row->task),
				'urgency'=>$ur,
				'attachment'=>$attachment,
				'delegated_date'=>$d_date,
				'followup'=>'',
				'response'=>$userresponse,
				'remarks'=>$userremarks.($poorf !== '' ? '<br>'.$poorf : ''));
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

	function filter_history() {
		$users = $this->input->post('users');
		$task_status = $this->input->post('task_status');
		// echo $start_date;exit;
		if($this->input->post('start_date') == '') {
			$start_date = 'NA';
		} else {
			$start_date = $this->input->post('start_date');
		}

		if($this->input->post('end_date') == '') {
			$end_date = 'NA';
		} else {
			$end_date = $this->input->post('end_date');
		}

		redirect(page_url.'Delegation/delegation_history/'.$start_date.'/'.$end_date.'/'.$users.'/'.$task_status);
	}
	
	public function filter_delegation_dashboard(){
	

	$this->load->view('delegation/filter_delegation_dashboard');
}

	public function filter_delegation_dashboard_list()
	{
		$poorf="";
		$uid=$this->uri->segment(5);
		$startdate=$this->uri->segment(3);
		$enddate=$this->uri->segment(4);
		$disabled=1;
		$buttondisable="disabled";
		
		$scheduler_data = array();
		$this->db->select('a.id as recordid,a.urgency,a.task_status,a.second_date, a.third_date, a.yourname, a.department_id, a.delegate_to, a.task,a.image, a.delegated_date, a.case_no, a.added_on, b.department,b.department_id,c.user_id, c.title, c.first_name, c.last_name, d.first_name as delegated_by, d.last_name as delegated')->from('delegation_task a')->join('departments b','a.department_id=b.department_id','left')->join('system_users_view c','a.delegate_to=c.user_id','left')->join('system_users_view d','a.yourname=d.user_id','left');
		if($uid<>'ALL'){
		$this->db->where('a.delegate_to',$uid);
		}
		$this->db->where('delegated_date>=',$startdate)->where('delegated_date<=',$enddate);
		$query = $this->db->order_by('a.added_on','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		    $d_date = date('d-M-Y',strtotime($row->delegated_date));
			$date = date('Y-m-d');
			$seconddate = $row->second_date;
			$thirddate = $row->third_date;
			if($date==$seconddate || $date==$thirddate){
				$background = "style='background-color:yellow; color:#000;'";
			}else{
				$background = "";
			}
			
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			
			$date1 = new DateTime($row->delegated_date); 
			$date2 = new DateTime($addeddate); 
			$interval = $date1->diff($date2); 
			$days = $interval->d; 
			$sc= $days/2;
			$seconddatedeff  = round($sc);
			$td = $seconddatedeff/2;
			$thirddatediff = round($td);
			$secondplanneddate  = date('Y-m-d', strtotime($row->delegated_date."+".$seconddatedeff." days"));
			$thirdplanneddate  = date('Y-m-d', strtotime($secondplanneddate."+".$thirddatediff." days"));
			$remarks = "";
			/**LAST FOLLOW-UP REMARKS**/
			$q = $this->db->select('task_id, followup_remarks, added_on')->from('delegated_task_followup')->where('task_id',$row->recordid)->limit(1)->order_by('id','desc')->get();
			if($q->num_rows()>0){
				foreach($q->result() as $followup);
				$followupdate = date('d-M-Y', strtotime($followup->added_on));
				$times = date('H:i:s', strtotime($followup->added_on));
				$followuptime = "<br>". date('g:i A', strtotime($times)); 

				$remarks.=$followup->followup_remarks."<br>".$followupdate.$followuptime;
			}
			
			/**LAST FOLLOW-UP REMARKS**/
			
			/**USER RESPONSE**/
			$userresponse = "";
			$q1 = $this->db->select('id,task_id, user_response, updated_on')->from('user_response_on_delegated_task')->where('task_id',$row->recordid)->limit(1)->order_by('id','desc')->get();
			if($q1->num_rows()>0){
				foreach($q1->result() as $userinput);
				$responsedate = date('d-M-Y', strtotime($userinput->updated_on));
				$rtimes = date('H:i:s', strtotime($userinput->updated_on));
				$responsetime = "<br>". date('g:i A', strtotime($rtimes)); 
				$userresponse=$userinput->user_response."<br>".$responsedate.$responsetime;
				
			}
			$secondfollowupdate="";
			$thirdfollowupdate="";
			
				
				if($row->second_date!=='0000-00-00'){
					$secondfollowupdate = date('d-M-Y',strtotime($row->second_date));
			
				if($row->third_date=='0000-00-00' && $row->second_date!=='0000-00-00'){
					
					/**SET NEW FOLLOWUP DATE**/
					$thirdfollowupdate="";
			
			/**SET NEW FOLLOWUP DATE**/
				}
				else if($row->second_date!=='0000-00-00' && $row->third_date!=='0000-00-00'){
					$thirdfollowupdate=date('d-M-Y',strtotime($row->third_date));
				}
					
				}else{
					$secondfollowupdate="";
			/**SET NEW FOLLOWUP DATE**/
			
			/**SET NEW FOLLOWUP DATE**/
				}
				
			
			$userresponse = "";
			$userremarks='';
			$q1 = $this->db->select('id,task_id, user_response,task_status,updated_on,proof')->from('user_response_on_delegated_task')->where('task_id',$row->recordid)->limit(1)->order_by('id','desc')->get();
			if($q1->num_rows()>0){
				foreach($q1->result() as $userinput);
				if($userinput->task_status==1)
				{
					$sta="<span class='btn btn-success btn-xs'>DONE</span>";
				}else
				{
					$sta="<span class='btn btn-danger btn-xs'>PENDING </span>";
				}
				$responsedate = date('d-M-Y', strtotime($userinput->updated_on));
				$rtimes = date('H:i:s', strtotime($userinput->updated_on));
				$responsetime = "<br>". date('g:i A', strtotime($rtimes)); 
				$userresponse=$sta."<br><br>".$responsedate.$responsetime;
				$userremarks=$userinput->user_response;

				$poorf="<a href='".delegationfile.$userinput->proof."' download>DOWNLOAD</a>";
				
			}
			
			
			/**USER RESPONSE**/
			$a=0;
			$restyuu=$this->db->select('id')->from('delegated_task_followup')->where('task_id',$row->recordid)->get();
			$restyuu1=$restyuu->num_rows();
			$newremarksfor=$restyuu1+1;
			if($newremarksfor=='1')
			{
				$nrmk="FIRST DATE";
				$fdate=$row->delegated_date;
				if($fdate=='0000-00-00')
				{
					$a=1;
				}
			}else if($newremarksfor=='2')
			{
				$nrmk="SECOND DATE";
				$fdate=$row->second_date;
				if($fdate=='0000-00-00')
				{
					$a=1;
				}
		}else
		{
			$nrmk="THIRD DATE";
			$fdate=$row->third_date;
			if($fdate=='0000-00-00')
				{
					$a=1;
				}
		}
		
		if($restyuu1<3)
		{
			$remarks.=' <button class="btn btn-success btn-xs" data-toggle="modal" '.$buttondisable.' data-target="#con-close-modal'.$i.'">UPDATE '.$nrmk.' REMARKS</button>';
		}else{
			
			$remarks.=' <button class="btn btn-success btn-xs">Please Reassign Task</button>';
		}
		
		
			$ontimeornot='';
			if($a<>1)
			{ if(date('Y-m-d')<=$fdate)
			{ $ontimeornot=0; }else{$ontimeornot=1;}}
		
			$remarks.= '<div id="con-close-modal'.$i.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
           <form id="loginForm" method="post" action="'.page_url.'Delegation/update_followup_remarks/'.$row->recordid.'">';
  
				$remarks.= '<input type="hidden" name="timeornot" value="'.$ontimeornot.'">	';
					
                                $remarks.='<div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">UPDATE <span style="color:red; font-weight:bold">'.strtoupper($row->task).'</span> FOLLOW-UP REMARKS</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                               
												 <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">UPDATE FOLLOW-UP REMARKS</label><br>
														<span id="error_color_name" style="color:red;"></span>
                                                       <textarea class="form-control" name="remarks" id="remarks" style="width:800px" required></textarea>
                                                    </div>
                                                </div>
												
                                               
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                           ';
											
											if($a<>1)
											{
                                           $remarks.='<input type="submit" id="save" '.$buttondisable.' class="btn btn-info" value="Submit"><button type="button" '.$buttondisable.' class="btn btn-default waves-effect" data-dismiss="modal">Close</button>';
											}else{
												
												 $remarks.='<span style="color:red;font-weight:bold;">Please set '.$nrmk.' to update followup</span>';
												
											}
										   
                                        $remarks.='</div>
                                    </div>
                                </div>
								</form>
                            </div>';
			
			$taskupdation = '<div class="row"><div class="col-md-12">
				<select class="form-control" name="markas" id="markas'.$i.'" onchange="changestatus('.$i.')">
				<option value="">SELECT OPTION</option>
				<option value="1">COMPLETED</option>
				<option value="2">AGAIN DELEGATED</option>
				</select>
				<input type="hidden" name="recordid[]" id="recordid'.$i.'" value="'.$row->recordid.'" >
			</div></div>';
			$attachment = "";
			$sql2 = $this->db->select('image')
							 ->from('delegation_images')
							 ->where('delegation_id', $row->recordid)
							 ->get();

				if($sql2->num_rows() > 0) {
					foreach($sql2->result() as $row2) {
						if($row2->image) {
							$attachment .= "<a href='".delegationfile.$row2->image."' download>DOWNLOAD</a><br>";
						}else{
							$attachment = "";
						}
					}
				}
			
			if($row->urgency==1)
			{
				$ur="<strong style='color:red;font-weight:bold;font-size:16px;'>HIGH</strong>";
			}else if($row->urgency==2)
			{
				$ur="<strong style='color:orange;font-weight:bold;font-size:16px;'>MEDIUM</strong>";
			}else if($row->urgency==3)
			{
				$ur="<strong style='color:green;font-weight:bold;font-size:16px;'>LOW</strong>";
			}else
			{
				$ur='';
			}
			$scheduler_data[] = array('sr_no'=>$i,
			'timestamp'=>$addeddate.$addedtime,
			'delegated_by'=>$row->delegated_by." ".$row->delegated,
			'delegated_to'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'caseno'=>strtoupper($row->case_no),
			'task'=>strtoupper($row->task),
			'urgency'=>$ur,
			'attachment'=>$attachment,
			'delegated_date'=>$d_date,
			'second_date'=>$secondfollowupdate,
			'third_date'=>$thirdfollowupdate,
			'followup'=>$remarks,
			'status_on_thirddate'=>$taskupdation,
			'response'=>$userresponse,
			'remarks'=>$userremarks.'<br>'.$poorf);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}

function testemail(){

$config = Array(
        'protocol' => 'smtp',
        'smtp_host' => 'ssl://smtp.googlemail.com',
        'smtp_port' => 465,
        'smtp_user' => 'taskmanagement@shubhampack.com',
        'smtp_pass' => 'ficihlqnfcdrrqkb',
        'mailtype'  => 'html', 
        'charset'   => 'utf-8',
        'newline'   => "\r\n"
        //'smtp_crypto'   => 'tls'
        );
        //echo "<pre>"; print_r($config); exit;
         $this->email->initialize($config);
		$this->email->set_mailtype("html");

$subjectname = "Work Delegation Notification";
$Message = "Test body email";
$this->email->set_mailtype("html");
$this->email->to('mangleshup@gmail.com');
$this->email->bcc('mangleshup@gmail.com');
$this->email->from('taskmanagement@shubhampack.com', 'Shubham Flexible Packaging');
$this->email->subject($subjectname);
$this->email->message($Message);
$result11 = $this->email->send();
$this->email->print_debugger(); exit;

	
}


public function new_delegation_task(){
	$this->load->view('delegation/new_delegate_task');
}

public function edit_new_delegation_task(){
    $case_no = $this->uri->segment(3);

    // Common data (single row)
    $this->db->where('case_no', $case_no);
    $this->db->limit(1);
    $data['task'] = $this->db->get('delegation_task')->row();

    // All delegate_to users (multiple rows)
    $this->db->select('delegate_to');
    $this->db->where('case_no', $case_no);
    $query = $this->db->get('delegation_task')->result();

    $delegate_ids = [];
    foreach($query as $row){
        $delegate_ids[] = $row->delegate_to;
    }

    $data['selected_users'] = $delegate_ids;

    $this->load->view('delegation/edit_delegation_task', $data);
}



public function save_delegation_task(){
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('your_name', 'your_name', 'required|trim');
	$this->form_validation->set_rules('business_loc', 'business_loc', 'required|trim');
	$this->form_validation->set_rules('task', 'task', 'required|trim');
	$this->form_validation->set_rules('work_completion_date', 'work_completion_date', 'required|trim');

	$user_id =$this->session->userdata['logged_in']['user_id'];		
	$delegate_users = $this->input->post('delegate_to');

	if(!is_array($delegate_users)){
		$delegate_users = !empty($delegate_users) ? [$delegate_users] : [];
	}

	if ($this->form_validation->run() == FALSE || empty($delegate_users))
	{
		$this->load->view('delegation/new_delegate_task');
		return;
	}
	else
	{
		date_default_timezone_set("Asia/Kolkata");
		$added_time = date('Y-m-d H:i:s');

		$photo = isset($_FILES['image']['name']) ? $_FILES['image']['name'] : '';
		if($photo<>'')
		{
			$image1=explode('.',$photo);
			$cat_image=end($image1);
			$screenshot=time().'.'.$cat_image;
			move_uploaded_file($_FILES["image"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/image_bank/delegation/' . $screenshot);
		}
		else
		{
			$screenshot="";
		}	

		$query = $this->db->select('id')->from('delegation_task')->get();
		$res = count($query->result());
		if($res<=0){
			$caseno = "SP-1";
		}else{
			$caseno= "SP-".$res;
		}

		$qry = $this->db->select('first_name, last_name')
						->from('system_users_view')
						->where('user_id',$this->input->post('your_name'))
						->get();
		foreach($qry->result() as $assignedto);

		$assignedbypersonname = ucwords(strtolower($assignedto->first_name." ".$assignedto->last_name));

		foreach($delegate_users as $delegate_to){

			$data=
			array(
				'yourname'=>strtoupper($this->input->post('your_name')),
				// 'department_id'=>$department,
				'delegate_to'=>$delegate_to,
				'task'=>strtoupper($this->input->post('task')),
				'email_url'=>$this->input->post('email_url'),
				'image'=>$screenshot,
				'delegated_date'=>date('Y-m-d',strtotime($this->input->post('work_completion_date'))),
				'targetdate'=>date('Y-m-d',strtotime($this->input->post('work_completion_date'))),
				'added_by'=>$user_id,
				'case_no'=>$caseno,
				'added_on'=>$added_time
			);

			$res = $this->db->insert('delegation_task',$data);
			$last_id = $this->db->insert_id();

			if($res)
			{
				$query = $this->db->select('user_id, email, contact_number, first_name, last_name, business_location')
								->from('system_users_view')
								->where('user_id',$delegate_to)
								->get();

				foreach($query->result() as $rows);

				if($rows->business_location==1){
					$businessname= "Shubham Flexible Packaging";
				}else{
					$businessname = "Shubham Flexible Packaging";
				}

				$contactnumber = $rows->contact_number;
				$task = $this->input->post('task');	
				$targetdat = date('d-m-Y',strtotime($this->input->post('work_completion_date')));

				$username = strtoupper(ucfirst($rows->first_name));

				$smsmessage1 = "Dear ".ucwords(strtolower($username)).",<br>
Important Task delegated to you ⏱️

TASK: *".$task."* 
Complete it by: *".$targetdat."*. 
Assigned By : *".$assignedbypersonname."*
*$businessname* 🚀";

				// WHATSAPP (UNCHANGED)
				if($user_id==139){

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);

					$post = array(
						 'receiverMobileNo' => '91'.$contactnumber,
						'username' => whatsappuser2,
						'password' => whatsapppass2,
						'message'=>strip_tags($smsmessage1)
					);

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					curl_exec($ch);
					curl_close($ch);

				}else{

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);

					$post = array(
						 'receiverMobileNo' => '91'.$contactnumber,
						'username' => whatsappuser1,
						'password' => whatsapppass1,
						'message'=>strip_tags($smsmessage1)
					);

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					curl_exec($ch);
					curl_close($ch);
				}

				// EMAIL (UNCHANGED)
				$taskdetail = ucwords(strtolower($task));
				$delegatedby = $assignedbypersonname;
				$duedatetocomplete = date('d-m-Y',strtotime($this->input->post('work_completion_date')));

				$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif; background-color: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 8px;">
					<tr>
						<td style="background-color: #4872b8; padding: 10px; text-align: center;">
							<img src="https://shubhampack.com/wp-content/uploads/2021/05/Logo.png" width="160" />
						</td>
					</tr>
					<tr>
						<td style="padding: 15px; background-color: #ffffff;">
							<h2 style="color: #333333; font-size: 20px; margin: 0; text-align: center;">WORK DELEGATION NOTIFICATION</h2>
							<hr style="border: 0; border-top: 1px solid #e0e0e0; margin: 15px 0;">
							<p style="color: #555555; font-size: 14px; margin: 0;">
								<strong>Dear '.$rows->first_name.' '.$rows->last_name.',</strong>
								<br> A new task has been delegated to you. Below are the task details:
							</p>
							<p style="color: #555555; font-size: 14px; margin: 10px 0;">
								<strong>Task Details:</strong> '.$taskdetail.'
							</p>
							<p style="color: #555555; font-size: 14px; margin: 10px 0;">
								<strong>Task completion Date:</strong> '.$duedatetocomplete.'
							</p>
							<p style="color: #555555; font-size: 14px; margin: 10px 0;">
								<strong>Delegated By:</strong> '.$delegatedby.'
							</p>
						</td>
					</tr>
				</table>';

				$this->email->set_mailtype("html");
				$this->email->to($rows->email);
				$this->email->bcc('mangleshup@gmail.com');
				$this->email->from('taskmanagement@shubhampack.com', 'Shubham Flexible Packaging');
				$this->email->subject("Work Delegation Notification");
				$this->email->message($Message);
				$this->email->send();
			}
		}

		$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Thank you, Your record successfully added.</span></div><br/>');
		redirect(page_url.'Delegation/new_delegation_task');
	}
}


public function update_delegation_task(){

    $this->form_validation->set_rules('your_name', 'your_name', 'required|trim');
    $this->form_validation->set_rules('task', 'task', 'required|trim');
    $this->form_validation->set_rules('work_completion_date', 'work_completion_date', 'required|trim');

    $user_id = $this->session->userdata['logged_in']['user_id'];

    if ($this->form_validation->run() == FALSE){
        $this->load->view('delegation/edit_delegation_task');
        return;
    }

    date_default_timezone_set("Asia/Kolkata");
    $updated_time = date('Y-m-d H:i:s');

    $case_no = $this->input->post('case_no'); // hidden field se aayega

    // ✅ OLD DATA FETCH (for image)
    $old = $this->db->where('case_no', $case_no)->get('delegation_task')->row();

    // ================= IMAGE LOGIC =================
    if(!empty($_FILES['image']['name'])){

        $photo = $_FILES['image']['name'];
        $ext = pathinfo($photo, PATHINFO_EXTENSION);
        $new_image = time().'.'.$ext;

        move_uploaded_file($_FILES["image"]["tmp_name"], $_SERVER['DOCUMENT_ROOT'].'/image_bank/delegation/'.$new_image);

    }else{
        // ✅ old image same rahegi
        $new_image = $old->image;
    }

    // ================= USERS =================
    $new_users = $this->input->post('delegate_to');

    if(!is_array($new_users)){
        $new_users = [$new_users];
    }

    // OLD USERS
    $this->db->select('delegate_to');
    $this->db->where('case_no', $case_no);
    $old_rows = $this->db->get('delegation_task')->result();

    $old_users = [];
    foreach($old_rows as $row){
        $old_users[] = $row->delegate_to;
    }

    // ================= DELETE REMOVED USERS =================
    $users_to_delete = array_diff($old_users, $new_users);

    if(!empty($users_to_delete)){
        $this->db->where('case_no', $case_no);
        $this->db->where_in('delegate_to', $users_to_delete);
        $this->db->delete('delegation_task');
    }

    // ================= COMMON DATA =================
    $common_data = [
        'yourname' => $this->input->post('your_name'),
        'task' => strtoupper($this->input->post('task')),
        'email_url' => $this->input->post('email_url'),
        'image' => $new_image,
        'delegated_date' => date('Y-m-d',strtotime($this->input->post('work_completion_date'))),
        'targetdate' => date('Y-m-d',strtotime($this->input->post('work_completion_date'))),
        'updated_time' => $updated_time
    ];

    // ================= UPDATE EXISTING USERS =================
    $common_users = array_intersect($old_users, $new_users);

    if(!empty($common_users)){
        $this->db->where('case_no', $case_no);
        $this->db->where_in('delegate_to', $common_users);
        $this->db->update('delegation_task', $common_data);
    }

    // ================= INSERT NEW USERS =================
    $users_to_add = array_diff($new_users, $old_users);

    foreach($users_to_add as $user){

        $insert_data = $common_data;
        $insert_data['delegate_to'] = $user;
        $insert_data['case_no'] = $case_no;
        $insert_data['added_by'] = $user_id;
        $insert_data['added_on'] = $updated_time;

        $this->db->insert('delegation_task', $insert_data);
    }

    $this->session->set_flashdata('message','<div class="alert alert-success">Updated Successfully</div>');
    redirect(page_url.'Delegation/new_delegation_task');
}


public function add_remarks(){
	$this->load->view('delegation/add_remarks');
}

public function view_add_remarks(){
	$this->load->view('delegation/view_add_remarks');
}



public function user_wise_delegated_task_list()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$record_id= $this->uri->segment(3);
		$scheduler_data = array();
		$this->db->select('a.urgency,a.id as recordid, a.second_date, a.third_date, a.task_status, a.yourname, a.department_id, a.delegate_to, a.task,a.image, a.delegated_date, a.case_no, a.added_on,a.email_url,a.image, b.department,b.department_id,c.user_id, c.title, c.first_name, c.last_name, d.first_name as to_first_name,
    d.last_name as to_last_name,
    d.title as to_title')->from('delegation_task a')->join('departments b','a.department_id=b.department_id','left')->join('system_users_view c','a.yourname=c.user_id','left')->join('system_users_view d','a.delegate_to=d.user_id','left');
		$this->db->where('a.delegate_to',$user_id)->where('a.task_status','0')->where('a.id', $record_id)->group_by('a.task');
		$query = $this->db->order_by('a.added_on','DESC')->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		$i=1;
		foreach($res as $row)
		{
		    $second_date="";
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 


			 if (!empty($row->image)) {

    $attachment = '
    <a href="'.delegationfile.$row->image.'" target="_blank" 
       style="display:inline-flex; align-items:center; gap:5px; color:#4e73df; font-weight:500;">
       
       📎 View Attachment
    </a>';

} else {

    $attachment = '<span style="color:#999;">No Attachment</span>';
}
    


			
			// $attachment = "";
			// $sql2 = $this->db->select('image')
			// 				 ->from('delegation_images')
			// 				 ->where('delegation_id', $row->recordid)
			// 				 ->get();

			// 	if($sql2->num_rows() > 0) {
			// 		foreach($sql2->result() as $row2) {
			// 			if($row2->image) {
			// 				$attachment .= "<a href='".delegationfile.$row2->image."' download>DOWNLOAD</a><br>";
			// 			}else{
			// 				$attachment = "";
			// 			}
			// 		}
			// 	}
			
			if($row->second_date!='0000-00-00'){
				$seconddate = date('d-M-Y',strtotime($row->second_date));
			}else{
				$seconddate="";
			}
			
			if($row->third_date!='0000-00-00'){
				$thirddate = date('d-M-Y',strtotime($row->third_date));
			}else{
				$thirddate="";
			}
			

			
			/**USER RESPONSE**/
			$userresponse = "";
			$remarkdone="<a href='javascript:;' onclick='showModalreject(".$row->recordid.")' ><span class='btn btn-danger btn-xs' style='font-size:15px;width:66px;'>Remark</span></a>";
			$q1 = $this->db->select('id,task_id, user_response, updated_on,task_status')->from('user_response_on_delegated_task')->where('task_id',$row->recordid)->limit(1)->order_by('id','desc')->get();
			if($q1->num_rows()>0){
				foreach($q1->result() as $userinput);
				if($userinput->task_status==1)
				{
					$sta="<span class='btn btn-success btn-xs'>DONE</span>";
					$remarkdone = '';
				}else
				{
					$sta="<span class='btn btn-danger btn-xs'>PENDING </span>";
					$remarkdone="<a href='javascript:;' onclick='showModalreject(".$row->recordid.")' ><span class='btn btn-danger btn-xs' style='font-size:15px;width:66px;'>Remark</span></a>";
				}
				$responsedate = date('d-M-Y', strtotime($userinput->updated_on));
				$rtimes = date('H:i:s', strtotime($userinput->updated_on));
				$responsetime = "<br>". date('g:i A', strtotime($rtimes)); 
				$userresponse=$sta."<br>".$userinput->user_response."<br>".$responsedate.$responsetime;
				
				// if($userinput->task_id != $row->recordid){
				// 	$remarkdone="<a href='javascript:;' onclick='showModalreject(".$row->recordid.")' ><span class='btn btn-danger btn-xs' style='font-size:15px;width:66px;'>Remark</span></a>";
				// 	}else
				// 	{
				// 		$remarkdone="";
				// 	}
			}
			/**USER RESPONSE**/
			
				if($row->urgency==1)
			{
				$ur="<strong style='color:red;font-weight:bold;font-size:16px;'>HIGH</strong>";
			}else if($row->urgency==2)
			{
				$ur="<strong style='color:orange;font-weight:bold;font-size:16px;'>MEDIUM</strong>";
			}else if($row->urgency==3)
			{
				$ur="<strong style='color:green;font-weight:bold;font-size:16px;'>LOW</strong>";
			}else
			{
				$ur='';
			}

			if (!empty($row->email_url)) {

    $email_url = '
    <div style="display:flex; align-items:center; min-width:250px;">
        <input type="text" value="'.$row->email_url.'" 
            readonly 
            style="width:100%; padding:5px; border:1px solid #ddd; border-radius:5px; font-size:12px;"
            id="url_'.$row->recordid.'">

        <span onclick="copyUrl(\'url_'.$row->recordid.'\')" 
            style="cursor:pointer; margin-left:8px; font-size:16px;" title="Copy URL">
            📋
        </span>
    </div>

    <a href="'.$row->email_url.'" target="_blank" 
       style="display:inline-flex; align-items:center; gap:5px; color:#4e73df; font-weight:500; margin-top:5px;">
       🔗 View Link
    </a>';

} else {

    $email_url = '<span style="color:#999;">No URL Found</span>';
}
			
			
			$scheduler_data[] = array('sr_no'=>$i,
			'timestamp'=>$addeddate.$addedtime,
			'delegated_by'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
				'delegated_to'=>strtoupper($row->to_title." ".$row->to_first_name." ".$row->to_last_name),
			'caseno'=>strtoupper($row->case_no),
			'task'=>strtoupper($row->task),
			'urgency'=>$ur,
			'attachment'=>$attachment,
			'email_url' => $email_url,
			'second_date'=>$seconddate,
			'third_date'=>$thirddate,
			'delegated_date'=>date('d-M-Y', strtotime($row->delegated_date)),
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


public function save_response_ajax()
{
    $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
    $this->form_validation->set_rules('task_id', 'Task ID', 'required');
    // $this->form_validation->set_rules('status', 'Status', 'required|trim');
    $this->form_validation->set_rules('remarks', 'Remarks', 'required|trim');

    if ($this->form_validation->run() == FALSE) {
        echo json_encode([
            'status' => 'error',
            'message' => strip_tags(validation_errors())
        ]);
        exit;
    }

    $task_id = $this->input->post('task_id');
    // $status  = $this->input->post('status');
	$status = ($this->input->post('status') == 1) ? 1 : 2;
    $remarks = strtoupper($this->input->post('remarks'));
    $user_id = $this->session->userdata['logged_in']['user_id'];

    // =========================
    // ✅ FILE UPLOAD (same logic)
    // =========================
    $photo = $_FILES['attachment']['name'];
    $screenshot = '';

    if ($photo <> '') {
        $image1 = explode('.', $photo);
        $cat_image = end($image1);
        $screenshot = time() . '.' . $cat_image;
        move_uploaded_file($_FILES["attachment"]["tmp_name"], UPLOADPATH . 'delegation/' . $screenshot);
    }

    date_default_timezone_set("Asia/Kolkata");
    $added_time = date('Y-m-d H:i:s');

    // =========================
    // ✅ NEW TABLE INSERT
    // =========================
    $data = array(
        'task_id'   => $task_id,
        'user_id'   => $user_id,
        'remarks'   => $remarks,
        'status'    => $status,
        'attachment'=> $screenshot,
        'created_at'=> $added_time
    );

    $res = $this->db->insert('delegation_task_response', $data);

    if (!$res) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error'
        ]);
        exit;
    }

    // =========================
    // ✅ SAME STATUS LOGIC
    // =========================
    if ($status == 1) {
        $sta = "Done";
    } else {
        $sta = "Pending";
    }

    // =========================
    // ✅ SAME FETCH LOGIC
    // =========================
    $taskDetailsQuery = $this->db->select('yourname, task, delegate_to')
        ->from('delegation_task')
        ->where('id', $task_id)
        ->get();
    $taskDetails = $taskDetailsQuery->row();

    $task = $taskDetails->task;

    // Delegator
    $delegatorQuery = $this->db->select('first_name, last_name, email, contact_number')
        ->from('system_users')
        ->where('user_id', $taskDetails->yourname)
        ->get();
    $delegator = $delegatorQuery->row();

    // Assignee
    $assigneeQuery = $this->db->select('first_name, last_name, email')
        ->from('system_users')
        ->where('user_id', $taskDetails->delegate_to)
        ->get();
    $assignee = $assigneeQuery->row();

    $delegatorName = ucwords(strtolower($delegator->first_name . " " . $delegator->last_name));
    $assigneeName = ucwords(strtolower($assignee->first_name . " " . $assignee->last_name));
    $delegatorEmail = $delegator->email;
    $delegatorContact = $delegator->contact_number;

    // =========================
    // ✅ SAME EMAIL TEMPLATE (UNCHANGED)
    // =========================
    $emailMessage = '
    <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif; background-color: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 8px;">
        <tr>
            <td style="background-color: #4872b8; padding: 10px; text-align: center;">
                <img src="https://shubhampack.com/wp-content/uploads/2021/05/Logo.png" width="160" alt="Shubham Packs" style="display: block; margin: 0 auto;" />
            </td>
        </tr>
        <tr>
            <td style="padding: 15px; background-color: #ffffff;">
                <h2 style="color: #333333; font-size: 20px; margin: 0; text-align: center;">TASK RESPONSE NOTIFICATION</h2>
                <hr style="border: 0; border-top: 1px solid #e0e0e0; margin: 15px 0;">
                <p style="color: #555555; font-size: 14px; margin: 0; line-height: 1.6;">
                    <strong>Dear ' . strtoupper($delegatorName) . ',</strong>
                    <br>
                    The following task assigned to <strong>' . strtoupper($assigneeName) . '</strong> has received a response:
                </p>
                <p style="color: #555555; font-size: 14px; margin: 15px 0; line-height: 1.6;">
                    <strong>Task:</strong> ' . strtoupper($task) . '<br>
                    <strong>Current Status:</strong> ' . $sta . '<br>
                    <strong>Remarks:</strong> ' . strtoupper($remarks) . '<br>
                </p>
                <p style="color: #555555; font-size: 14px; margin: 15px 0; line-height: 1.6;">
                    <strong>Submitted By:</strong> ' . strtoupper($assigneeName) . '
                </p>
                <p style="color: #555555; font-size: 14px; margin: 15px 0; line-height: 1.6;">
                    Kindly review the response and take appropriate action.
                </p>
                <hr style="border: 0; border-top: 1px solid #e0e0e0; margin: 15px 0;">
                <p style="color: #555555; font-size: 13px; text-align: center; margin: 0; line-height: 1.6;">
                    If you have any questions, contact us at <a href="mailto:' . $delegatorEmail . '" style="color: #4872b8; text-decoration: none;">' . $delegatorEmail . '</a>.
                </p>
            </td>
        </tr>
        <tr>
            <td style="background-color: #4872b8; color: #ffffff; padding: 10px; text-align: center; font-size: 12px;">
                &copy; ' . date("Y") . ' Shubham Packs. All rights reserved.
            </td>
        </tr>
    </table>';

    // EMAIL SEND
    $this->load->library('email');
    $this->email->set_mailtype("html");
    $this->email->to($delegatorEmail);
	// $this->email->to('akashajaysharma1509@gmail.com');
    $this->email->cc('mangleshup@gmail.com');
    $this->email->from('taskmanagement@shubhampack.com', 'Shubham Packs');
    $this->email->subject('Task Response Notification');
    $this->email->message($emailMessage);
    $this->email->send();

    // =========================
    // ✅ SAME WHATSAPP MESSAGE
    // =========================
    $whatsappMessage = "Dear {$delegatorName},\n\nThe following task assigned to {$assigneeName} has received a response:\n\nTask: {$task}\nRemarks: {$remarks}\nWork Status: {$sta}\n\nPlease review the response and take necessary action.\n\nBest Regards,\nTeam Shubham Packs 🚀";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);

    $post = array(
        'receiverMobileNo' => '91' . $delegatorContact,
		    //  'receiverMobileNo' => '8588052104',
        'username' => whatsappuser1,
        'password' => whatsapppass1,
        'message' => strip_tags($whatsappMessage)
    );

    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_exec($ch);
    curl_close($ch);

    // =========================
    // ✅ FINAL JSON RESPONSE
    // =========================
    echo json_encode([
        'status' => 'success',
        'message' => 'Your response has been successfully submitted, and notifications have been sent to the delegator.'
    ]);
}

public function get_task_remarks()
{
    $task_id = $this->input->post('task_id');

    $this->db->select('r.*, u.first_name, u.last_name, u.profile_image');
    $this->db->from('delegation_task_response r');
    $this->db->join('system_users u', 'u.user_id = r.user_id');
    $this->db->where('r.task_id', $task_id);
    $this->db->order_by('r.id', 'DESC');

    $data = $this->db->get()->result();

    if(!$data){
        echo "<p>No remarks found</p>";
        return;
    }

	$current_user_id = $this->session->userdata('user_id');

foreach ($data as $row) {

    $is_me = ($row->user_id == $current_user_id) ? 'chat-right' : 'chat-left';

    // profile image
    $profile = (!empty($row->profile_image))
        ? page_url1.'image_bank/users/'.$row->profile_image
        : page_url1.'image_bank/users/userplaceholder.jpeg'; // default image

    $status = ($row->status == 1)
        ? '<span style="color:green;">Done</span>'
        : '<span style="color:red;">Pending</span>';

    echo '<div class="chat-msg '.$is_me.'">';

    // LEFT SIDE (show image)
    if($is_me == 'chat-left'){
        echo '<div class="chat-avatar">
                <img src="'.$profile.'">
              </div>';
    }

    echo '<div class="chat-bubble">

            <div class="chat-name">'
                . strtoupper($row->first_name.' '.$row->last_name) .
            '</div>

            <div>'.$row->remarks.'</div>';

    if($row->attachment != ''){
        echo '<div class="chat-attach">
                <a href="'.delegationfile.$row->attachment.'" target="_blank">📎 Attachment</a>
              </div>';
    }

    echo '<div class="chat-time">'
            . date('d M Y, h:i A', strtotime($row->created_at)) .
         '</div>

        </div>';

    echo '</div>';
}

    // foreach($data as $row){

    //     $status = ($row->status == 1)
    //         ? '<span style="color:green;">Done</span>'
    //         : '<span style="color:red;">Pending</span>';

    //     echo '
    //     <div style="border-bottom:1px solid #ddd; padding:10px;">
    //         <b>'.strtoupper($row->first_name.' '.$row->last_name).'</b><br>
    //         <small>'.date('d-m-Y H:i', strtotime($row->created_at)).'</small><br>
    //         <b>Status:</b> '.$status.'<br>
    //         <b>Remarks:</b> '.$row->remarks.'<br>';

    //     if($row->attachment){
    //         echo '<a href="'.page_url.'uploads/delegation/'.$row->attachment.'" target="_blank">View File</a>';
    //     }

    //     echo '</div>';
    // }
}


public function user_wise_by_delegated_task_list()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$record_id= $this->uri->segment(3);
		$scheduler_data = array();
		$this->db->select('a.urgency,a.id as recordid, a.second_date, a.third_date, a.task_status, a.yourname, a.department_id, a.delegate_to, a.task,a.image, a.delegated_date, a.case_no, a.added_on,a.email_url,a.image, b.department,b.department_id,c.user_id, c.title, c.first_name, c.last_name, d.first_name as to_first_name,
    d.last_name as to_last_name,
    d.title as to_title')->from('delegation_task a')->join('departments b','a.department_id=b.department_id','left')->join('system_users_view c','a.yourname=c.user_id','left')->join('system_users_view d','a.delegate_to=d.user_id','left');
		$this->db->where('a.yourname',$user_id)->where('a.task_status','0')->where('a.id', $record_id)->group_by('a.task');
		$query = $this->db->order_by('a.added_on','DESC')->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		$i=1;
		foreach($res as $row)
		{
		    $second_date="";
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 


			 if (!empty($row->image)) {

    $attachment = '
    <a href="'.delegationfile.$row->image.'" target="_blank" 
       style="display:inline-flex; align-items:center; gap:5px; color:#4e73df; font-weight:500;">
       
       📎 View Attachment
    </a>';

} else {

    $attachment = '<span style="color:#999;">No Attachment</span>';
}
    


			
			// $attachment = "";
			// $sql2 = $this->db->select('image')
			// 				 ->from('delegation_images')
			// 				 ->where('delegation_id', $row->recordid)
			// 				 ->get();

			// 	if($sql2->num_rows() > 0) {
			// 		foreach($sql2->result() as $row2) {
			// 			if($row2->image) {
			// 				$attachment .= "<a href='".delegationfile.$row2->image."' download>DOWNLOAD</a><br>";
			// 			}else{
			// 				$attachment = "";
			// 			}
			// 		}
			// 	}
			
			if($row->second_date!='0000-00-00'){
				$seconddate = date('d-M-Y',strtotime($row->second_date));
			}else{
				$seconddate="";
			}
			
			if($row->third_date!='0000-00-00'){
				$thirddate = date('d-M-Y',strtotime($row->third_date));
			}else{
				$thirddate="";
			}
			

			
			/**USER RESPONSE**/
			$userresponse = "";
			$remarkdone="<a href='javascript:;' onclick='showModalreject(".$row->recordid.")' ><span class='btn btn-danger btn-xs' style='font-size:15px;width:66px;'>Remark</span></a>";
			$q1 = $this->db->select('id,task_id, user_response, updated_on,task_status')->from('user_response_on_delegated_task')->where('task_id',$row->recordid)->limit(1)->order_by('id','desc')->get();
			if($q1->num_rows()>0){
				foreach($q1->result() as $userinput);
				if($userinput->task_status==1)
				{
					$sta="<span class='btn btn-success btn-xs'>DONE</span>";
					$remarkdone = '';
				}else
				{
					$sta="<span class='btn btn-danger btn-xs'>PENDING </span>";
					$remarkdone="<a href='javascript:;' onclick='showModalreject(".$row->recordid.")' ><span class='btn btn-danger btn-xs' style='font-size:15px;width:66px;'>Remark</span></a>";
				}
				$responsedate = date('d-M-Y', strtotime($userinput->updated_on));
				$rtimes = date('H:i:s', strtotime($userinput->updated_on));
				$responsetime = "<br>". date('g:i A', strtotime($rtimes)); 
				$userresponse=$sta."<br>".$userinput->user_response."<br>".$responsedate.$responsetime;
				
				// if($userinput->task_id != $row->recordid){
				// 	$remarkdone="<a href='javascript:;' onclick='showModalreject(".$row->recordid.")' ><span class='btn btn-danger btn-xs' style='font-size:15px;width:66px;'>Remark</span></a>";
				// 	}else
				// 	{
				// 		$remarkdone="";
				// 	}
			}
			/**USER RESPONSE**/
			
				if($row->urgency==1)
			{
				$ur="<strong style='color:red;font-weight:bold;font-size:16px;'>HIGH</strong>";
			}else if($row->urgency==2)
			{
				$ur="<strong style='color:orange;font-weight:bold;font-size:16px;'>MEDIUM</strong>";
			}else if($row->urgency==3)
			{
				$ur="<strong style='color:green;font-weight:bold;font-size:16px;'>LOW</strong>";
			}else
			{
				$ur='';
			}

			if (!empty($row->email_url)) {

    $email_url = '
    <div style="display:flex; align-items:center; min-width:250px;">
        <input type="text" value="'.$row->email_url.'" 
            readonly 
            style="width:100%; padding:5px; border:1px solid #ddd; border-radius:5px; font-size:12px;"
            id="url_'.$row->recordid.'">

        <span onclick="copyUrl(\'url_'.$row->recordid.'\')" 
            style="cursor:pointer; margin-left:8px; font-size:16px;" title="Copy URL">
            📋
        </span>
    </div>

    <a href="'.$row->email_url.'" target="_blank" 
       style="display:inline-flex; align-items:center; gap:5px; color:#4e73df; font-weight:500; margin-top:5px;">
       🔗 View Link
    </a>';

} else {

    $email_url = '<span style="color:#999;">No URL Found</span>';
}
			
			
			$scheduler_data[] = array('sr_no'=>$i,
			'timestamp'=>$addeddate.$addedtime,
			'delegated_by'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
				'delegated_to'=>strtoupper($row->to_title." ".$row->to_first_name." ".$row->to_last_name),
			'caseno'=>strtoupper($row->case_no),
			'task'=>strtoupper($row->task),
			'urgency'=>$ur,
			'attachment'=>$attachment,
			'email_url' => $email_url,
			'second_date'=>$seconddate,
			'third_date'=>$thirddate,
			'delegated_date'=>date('d-M-Y', strtotime($row->delegated_date)),
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
}
