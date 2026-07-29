<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Maintenance_support extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
	
	$user_id =$this->session->userdata['logged_in']['user_id'];
	
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
	}

	public function index()
	{
		$this->load->view('maintenance_support/raise_ticket');
	}
	
		
public function raise_ticket()
{
    $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
    $this->form_validation->set_rules('dfno', 'DF No', 'required|trim');
    $this->form_validation->set_rules('department', 'Department Selection', 'required|trim');
    $this->form_validation->set_rules('user_id[]', 'User Selection', 'trim'); // Allow multiple user selection
    $this->form_validation->set_rules('remarks', 'Remarks', 'required|trim');

    $user_id = $this->session->userdata['logged_in']['user_id'];

    if ($this->form_validation->run() == FALSE) {
        $this->load->view('maintenance_support/raise_ticket');
    } else {
        $rand_date = date('y-m-d');
        $rand = rand(1, 100);
        $ticket_id = "SPM-" . $rand_date . "-" . $rand;
        date_default_timezone_set("Asia/Kolkata");
        $date = date('Y-m-d H:i:s');

        $table = "maintenance_support";
        $photo = $_FILES['screen_shot']['name'];
        $screenshot = "";

        // Upload and compress image if attached
        if ($photo) {
            $image1 = explode('.', $photo);
            $cat_image = end($image1);
            $imgname = time();
            $screenshot = $imgname . '.' . $cat_image;
            $sourceurl = UPLOADPATH . 'maintenance/' . $screenshot;
            $this->compress_image($_FILES["screen_shot"]["tmp_name"], $sourceurl, 80);
            move_uploaded_file($_FILES["screen_shot"]["tmp_name"], $sourceurl);
        }

        // Determine department and user selection
        $department_id = ($this->input->post('department') == 'ALL') ? 0 : $this->input->post('department');
        $user_ids = $this->input->post('user_id') ?? []; // Handle multiple users or empty user selection

        $data = [
            'business_location' => 1,
            'df_id' => $this->input->post('dfno'),
            'department_id' => $department_id,
            'user_id' => implode(',', $user_ids), // Store user IDs as a comma-separated string
            'ticket' => $this->input->post('remarks'),
            'ticket_id' => $ticket_id,
            'status' => '0',
            'screenshot' => $screenshot,
            'added_on' => $date,
            'added_by' => $user_id
        ];

        $remark = $this->input->post('remarks');
        $result = $this->db->insert($table, $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            $ticket_info = $this->db->select('a.*, c.department, d.first_name, d.last_name')
                ->from('maintenance_support a')
                ->join('departments c', 'a.department_id = c.department_id', 'left')
                ->join('system_users d','a.added_by=d.user_id','left')
                ->where('a.id', $insert_id)
                ->get()
                ->row();

               //echo "<pre>"; print_r($user_ids); exit;

            $subject = "New Ticket: " . $ticket_id;
            $message = $this->generate_email_content($ticket_info, $this->input->post('remarks'), $insert_id);
            $notification_message = "New ticket generated with ID: $ticket_id.";

         if ($insert_id) {
        if ($department_id == 0) {
            // Notify and queue email for all users in all departments
            $all_users = $this->get_all_users();
            foreach ($all_users as $user) {
                $this->create_growl_notification($user['user_id'], $notification_message);
                $this->queue_email($user['email'], $subject, $message, $screenshot);
            }
        } elseif ($department_id != 0 && empty($user_ids)) {
            // Notify and queue email for all users in the selected department
            $department_users = $this->get_department_users($department_id);
            foreach ($department_users as $user) {
                $this->create_growl_notification($user['user_id'], $notification_message);
                $this->queue_email($user['email'], $subject, $message, $screenshot);
            }
        } elseif ($department_id != 0 && !empty($user_ids)) {
            // Notify and queue email for selected users
            $selected_users = $this->get_selected_users($user_ids);
            foreach ($selected_users as $user) {
                $this->create_growl_notification($user['user_id'], $notification_message);
                $this->queue_email($user['email'], $subject, $message, $screenshot);
            }
        }

        $this->session->set_flashdata('message', '<div class="alert alert-success">Thank you, record successfully added.</div>');
        redirect(page_url . 'Maintenance_support/viewalltickets');
    } else {
        $this->session->set_flashdata('message', '<div class="alert alert-danger">Sorry, technical error occurred.</div>');
        redirect(page_url . 'Maintenance_support/viewalltickets');
    }
    }
}
}

private function queue_email($to, $subject, $message, $attachment)
{
    $data = [
        'to_email' => is_array($to) ? implode(',', $to) : $to,
        'subject' => $subject,
        'message' => $message,
        'attachment' => $attachment,
        'status' => 0, // Pending
        'created_at' => date('Y-m-d H:i:s')
    ];
    //echo "<pre>"; print_r($data); exit;
    $this->db->insert('queue_emails', $data);
}



private function create_growl_notification($user_id, $message)
{
    $notification_data = [
        'user_id' => $user_id,
        'message' => $message,
        'is_read' => 0, // Mark as unread
        'created_at' => date('Y-m-d H:i:s')
    ];
    $this->db->insert('df_support_notifications', $notification_data);
}

private function get_all_users()
{
    $query = $this->db->select('user_id, email')
        ->from('system_users')
        ->where('user_status', 1) // Active users only
        ->get();
    return $query->result_array();
}


private function get_department_users($department_id)
{
    $query = $this->db->select('user_id, email')
        ->from('system_users')
        ->where('department_id', $department_id)
        ->where('status', 1) // Active users only
        ->get();
    return $query->result_array();
}


private function get_selected_users($user_ids)
{
    $query = $this->db->select('user_id, email')
        ->from('system_users')
        ->where_in('user_id', $user_ids)
        ->get();
    return $query->result_array();
}




function getdfdetail($dfid){
	$q = $this->db->select('df_no, df_description')->from('df_release')->where('id',$dfid)->get();
	foreach($q->result() as $row);
	return $row->df_no." ".$row->df_description;
} 

/**
 * Function to send an email
 */
private function send_email($to, $subject, $message, $attachment = null)
{
    $attachment_path = UPLOADPATH . 'maintenance/' . $attachment; // Assuming UPLOADPATH constant is defined

    $this->load->library('email');
    $this->email->set_mailtype("html");
    $this->email->from('taskmanagement@shubhampack.com', 'Shubham Pack DF Related Help Ticket');

    // Handle single or multiple recipients
    // if (is_array($to)) {
    //     $this->email->to(implode(',', $to));
    // } else {
    //     $this->email->to($to);
    // }

    $this->email->to('mangleshup@gmail.com');

    // Add CC email (if applicable)
    //$this->email->cc('groupceo@shubhampack.com');

    // Set email subject and message
    $this->email->subject($subject);
    $this->email->message($message);

    // Attach file if provided and exists
    if (!empty($attachment) && file_exists($attachment_path)) {
        $this->email->attach($attachment_path);
    }

    // Send the email and return the result
    return $this->email->send();
}


/**
 * Function to generate email content
 */
private function generate_email_content($ticket_info, $remark, $insert_id)
{
    return '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td align="left" style="background: #fff; padding: 15px 0;" bgcolor="#120001">
                            <img src="https://pms.shubhampack.in/assets/images/shubhampack.png" width="200px;" alt="Shubham Pack" />
                        </td>
                    </tr>
                    <tr>
                        <td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br>
                            <strong>DF SUPPORT REQUEST GENERATED IN PMS. PLEASE LOGIN TO SEE THE TICKET INFORMATION.</strong><br>
                        </td>
                    </tr>
                    <tr>
                        <td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br>
                            <strong>Ticket ID #'.$ticket_info->ticket_id.'</strong><br><br>
                        </td>
                    </tr>
                    <tr>
                        <td>
                             New Ticket has been raised. Please find the details below:<br><br>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Raised By:</strong> '.$ticket_info->first_name.' '.$ticket_info->last_name.'</td>
                    </tr>
                    <tr>
                        <td><strong>Remarks:</strong> '.$remark.'</td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>';
}

/**
 * Function to fetch department email
 */
private function get_department_email($department_id, $user_id)
{
    $this->db->select('email')->from('system_users');

    if ($department_id == 0 && $user_id == 0) {
    	$this->db->where('user_status', 1);
        // No filtering required if both are 0
    } else if ($department_id <> 0 && $user_id == 0) {
        // Filter by department_id when user_id is 0
        $this->db->where('department_id', $department_id);
    } else {
        // Filter by department_id and user_id when both are provided
        $this->db->where('department_id', $department_id)->where('user_id', $user_id);
    }

    $query = $this->db->get();

    // Collect all email addresses in an array
    if ($query->num_rows() > 0) {
        $emails = array_column($query->result_array(), 'email');
        return $emails;
    }

    return null;
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
	public function viewalltickets(){
		 $this->load->model('Ticket_model');
		 $data['maintenance_support'] = $this->Ticket_model->getMaintenanceSupportList();
		$this->load->view('maintenance_support/ticket_list.php',$data);
	}

	public function list_tickets()
    {
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $search = $this->input->post('search')['value'];

        $this->load->model('Ticket_model');
        $data = $this->Ticket_model->get_tickets($start, $length, $search);
        $total_records = $this->Ticket_model->count_tickets();
        $filtered_records = $this->Ticket_model->count_tickets($search);

        $response = [
            "draw" => intval($this->input->post('draw')),
            "recordsTotal" => $total_records,
            "recordsFiltered" => $filtered_records,
            "data" => $data
        ];

        echo json_encode($response);
    }
	public function raised_ticket_list()
	{
		$i=1;
		$support_ticket = array();
		$this->db->select('a.*,b.company_name, b.business_loc_id, b.address, c.user_id, c.first_name, c.last_name,d.user_id,d.user_id, d.first_name as raised_by_fname, d.last_name as raised_by_lname, e.department_id, e.department, f.user_id, f.first_name as closed_by_fname, f.last_name as closed_by_lname')->from('maintenance_support a')->join('departments e','e.department_id=a.department_id','left')->join('system_users c','a.user_id=c.user_id','left')->join('system_users d','a.added_by=d.user_id','left')->join('system_users f','a.updated_by=f.user_id','left');
		$this->db->where('a.status','0');
		$this->db->order_by('a.added_on','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$status ="<a href='".page_url."Maintenance_support/update_ticket_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Reopen</span></a>";
				$updateddate = date('Y-m-d', strtotime($row->updated_on));
				$time = date('H:i:s', strtotime($row->updated_on));
				$updatedtime = "<br>". date('g:i A', strtotime($time)); 
				$sta = $status."<br>"."This ticket has been closed by <strong>".$row->closed_by_fname." ".$row->closed_by_lname."</strong><br> at ".$updateddate." ".$updatedtime;
				
			}else
			{
				$sta ="<a href='".page_url."Maintenance_support/update_ticket_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Pending</span></a>";
			}
			$history = "<a href='".page_url."Maintenance_support/view_history/".$row->id."'><span class='btn btn-warning btn-xs'><i class='fa fa-eye'></i>&nbsp; Add /See Comments</span></a>";
			$edit = "<a href='".page_url."Maintenance_support/edit_raised_ticket/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			
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
		$query = $this->db->select('user_id, department_id')->from('system_users')->where('department_id',$this->uri->segment(3))->get();
		$res = $query->result();
		foreach($res as $departmentdata)
		$user_id[] = $departmentdata->user_id;
		//echo "<pre>"; print_r($user_id); exit;
		$i=1;
		$support_ticket = array();
		$this->db->select('a.*,b.company_name, b.business_loc_id, b.address, c.user_id, c.first_name, c.last_name,d.user_id,d.user_id, d.first_name as raised_by_fname, d.last_name as raised_by_lname, e.department_id, e.department, f.user_id, f.first_name as closed_by_fname, f.last_name as closed_by_lname')->from('maintenance_support a')->join('business_location b','a.business_location=b.business_loc_id','left')->join('departments e','e.department_id=a.department_id','left')->join('system_users c','a.user_id=c.user_id','left')->join('system_users d','a.added_by=d.user_id','left')->join('system_users f','a.updated_by=f.user_id','left');
		$this->db->where('a.status','0');
		$this->db->where_in('a.added_by',$user_id);
		$this->db->order_by('a.added_on','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			$status = $row->status;
			if($status=='1')
			{
				
				$status ="<a href='".page_url."Maintenance_support/update_ticket_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Reopen</span></a>";
				$updateddate = date('Y-m-d', strtotime($row->updated_on));
				$time = date('H:i:s', strtotime($row->updated_on));
				$updatedtime = "<br>". date('g:i A', strtotime($time)); 
				$sta = $status."<br>"."This ticket was closed by <strong>".$row->closed_by_fname." ".$row->closed_by_lname."</strong><br> on ".$updateddate." ".$updatedtime;
				
			}else
			{
				$sta ="<a href='".page_url."Maintenance_support/update_ticket_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Pending</span></a>";
			}
			if($row->screenshot){
			$before = "<img src='".maintenance.$row->screenshot."' width='100px'>";
			}else{
			   $before=""; 
			}
			$after = "";
			$query = $this->db->select('id,ticket_id, screenshot')->from('maintenance_support_ticket_progress')->where('ticket_id',$row->id)->limit('1')->order_by('id','desc')->get();
			$screen_report = $query->result();
			foreach($screen_report as $getdata)
			{
			    if($getdata->screenshot!==''){
			     $after = "<img src='".maintenance.$getdata->screenshot."' width='100px'>";   
			    }else{
			        $after = "";
			    }
			}
		
			$history = "<a href='".page_url."Maintenance_support/view_history/".$row->id."'><span class='btn btn-warning btn-xs'><i class='fa fa-eye'></i>&nbsp; Add /See Comments</span></a>";
			$edit = "<a href='".page_url."Maintenance_support/edit_raised_ticket/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			
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
			'before'=>$before,
			'after'=>$after,
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
		
		$this->load->view('maintenance_support/view_status');
	}
	
	public function view_raised_ticket_list()
	{
		$i=1;
		$support_ticket = array();
		$this->db->select('a.*,b.company_name, b.business_loc_id, b.address, c.user_id, c.first_name, c.last_name,d.user_id,d.user_id, d.first_name as raised_by_fname, d.last_name as raised_by_lname, e.department_id, e.department, f.user_id, f.first_name as closed_by_fname, f.last_name as closed_by_lname')->from('maintenance_support a')->join('business_location b','a.business_location=b.business_loc_id','left')->join('departments e','e.department_id=a.department_id','left')->join('system_users c','a.user_id=c.user_id','left')->join('system_users d','a.added_by=d.user_id','left')->join('system_users f','a.updated_by=f.user_id','left');
		$this->db->where('a.status','0');
		$this->db->order_by('a.added_on','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				
			$status ="<a href='".page_url."Maintenance_support/update_ticket_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Reopen</span></a>";
				$updateddate = date('Y-m-d', strtotime($row->updated_on));
				$time = date('H:i:s', strtotime($row->updated_on));
				$updatedtime = "<br>". date('g:i A', strtotime($time)); 
				$sta = $status."<br>"."This ticket was closed by <strong>".$row->closed_by_fname." ".$row->closed_by_lname."</strong><br> on ".$updateddate." ".$updatedtime;
				
			}else
			{
				$sta ="<a href='".page_url."Maintenance_support/update_ticket_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Pending</span></a>";
			}
			$history = "<a href='".page_url."Maintenance_support/view_history/".$row->id."'><span class='btn btn-warning btn-xs'><i class='fa fa-eye'></i>&nbsp; Add /See Comments</span></a>";
			$edit = "<a href='".page_url."Maintenance_support/edit_raised_ticket/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			
			if($row->screenshot){
			$before = "<img src='".maintenance.$row->screenshot."' width='100px'>";
			}else{
			   $before=""; 
			}
			$after = "";
			$query = $this->db->select('id,ticket_id, screenshot')->from('maintenance_support_ticket_progress')->where('ticket_id',$row->id)->limit('1')->order_by('id','desc')->get();
			$screen_report = $query->result();
			foreach($screen_report as $getdata)
			{
			    if($getdata->screenshot!==''){
			     $after = "<img src='".maintenance.$getdata->screenshot."' width='100px'>";   
			    }else{
			        $after = "";
			    }
			}
		
		
			$addeddate = date('Y-m-d', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 	
			$support_ticket[] = array('sr_no'=>$i,
			'ticket_id'=>$row->ticket_id,
			'business_loc'=>$row->company_name,
			'department'=>$row->department,
			'ticket'=>$row->ticket,
			'status'=>$sta,
			'added_on'=>$addeddate."".$addedtime,
			'added_by'=>$row->raised_by_fname." ".$row->raised_by_lname,
			'before'=>$before,
			'after'=>$after,
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
		$table = "maintenance_support";
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
			
			$query = $this->db->select('a.added_by, a.id, a.ticket,a.ticket_id,a.updated_on, a.updated_by,b.user_id, b.first_name as assigned_to_first_name, b.last_name as assigned_to_last_name,b.email ,c.user_id, c.first_name as closed_by_first_name, c.last_name as closed_by_last_name')->from('maintenance_support a')->join('system_users b','a.added_by=b.user_id','left')->join('system_users c','a.updated_by=c.user_id','left')->where('a.id',$identifier)->get();
			foreach($query->result() as $ticket_info);
			$updateddate = date('Y-m-d', strtotime($ticket_info->updated_on));
				$time = date('H:i:s', strtotime($ticket_info->updated_on));
				$updatedtime =  date('g:i A', strtotime($time)); 

			 $this->db->select('email')->from('system_users')->where('user_id',$user_id);
             $query = $this->db->get();
			 $res = $query->row_array();
             $fromemail = $res['email'];

			 $this->db->select('email')->from('support_email_options')->where('support_option',4);
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
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://pms.shubhampack.in/assets/images/shubhampack.png" width="200px;" alt="Shubham Pack" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>MAINTENANCE SUPPORT REQUEST</strong><br></td>
					  </tr> 
					  <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>Ticket ID #'.$ticket_info->ticket_id.'</strong><br><br></td>
					  </tr> 
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;">This ticket has been closed by '.$ticket_info->closed_by_first_name.' '.$ticket_info->closed_by_last_name.'. on '.$updateddate.$updatedtime.'
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
				$subjectname = "Maintenance Support Request ".$ticket_info->ticket_id;
					$this->email->set_mailtype("html");
					$this->email->to($ticket_info->email);
					$this->email->cc($ccemails);
					$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from($fromemail);
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();
			
			
			
			$this->session->set_flashdata('message', '<div class="alert alert-danger" style="color:#fff;">Status successfully updated.</div>');
			redirect('Maintenance_support/view_status/');
		}

	public function edit_raised_ticket(){
		$this->load->view('maintenance_support/edit_ticket');
	}	
	
	public function update_ticket_detail()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('department', 'Department', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('maintenance_support/edit_ticket');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "maintenance_support";
		$old_image = $this->input->post('old_image');
		$photo=$_FILES['screen_shot']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$screenshot=time().'.'.$cat_image;
				move_uploaded_file($_FILES["screen_shot"]["tmp_name"],UPLOADPATH.'maintenance/' . $screenshot);
				unlink(UPLOADPATH.'maintenance/' . $old_image);
			}else
				$screenshot=$old_image;
				}	
				
				
				
			$data = array('business_location'=>$this->input->post('business_loc'),
			'department_id'=>$this->input->post('department'),
			'user_id'=>$this->input->post('user_id'),
			'ticket'=>$this->input->post('remarks'),
			'screenshot'=>$screenshot,
			'added_on'=>$date,
			'added_by'=>$user_id);
			$this->db->where('id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully updated.</div>');
			redirect(page_url.'Maintenance_support/');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry,technical error accure.</div><br/>');
		redirect(page_url.'Maintenance_support/');
		}
		
		
	}
		

  public function view_history(){
	  $this->load->view('maintenance_support/ticket_history');
  }		
  public function raised_ticket_detail()
	{
		$i=1;
		$support_ticket = array();
		$this->db->select('a.*, c.user_id,a.remarks, c.first_name, c.last_name,d.user_id,d.user_id, d.first_name as added_person, d.last_name as lastname, e.department_id, e.department, f.user_id, f.first_name as closed_by_fname, f.last_name as closed_by_lname')->from('maintenance_support a')->join('departments e','e.department_id=a.department_id','left')->join('system_users c','a.user_id=c.user_id','left')->join('system_users d','a.added_by=d.user_id','left')->join('system_users f','a.updated_by=f.user_id','left')->where('a.id',$this->uri->segment(3));
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
				$sta ="<a href='".page_url."Maintenance_support/update_ticket_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Pending</span></a>";
			}
			
			$edit = "<a href='".page_url."Maintenance_support/edit_tech_items/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			$addeddate = date('Y-m-d', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			if($row->department==0){
				$dept = "ALL";
			}else{
				$dept=$row->department;
			}	
			$support_ticket[] = array('sr_no'=>$i,
			'department'=>$dept,
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
			$this->load->view('maintenance_support/ticket_history');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$photo=$_FILES['screenshot']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$screenshot=time().'.'.$cat_image;
				move_uploaded_file($_FILES["screenshot"]["tmp_name"],UPLOADPATH.'maintenance/' . $screenshot);
			}else
			{
				$screenshot="";
				}	
				
		$table = "maintenance_support_ticket_progress";
			$data = array('ticket_id'=>$this->uri->segment(3),
			'remarks'=>$this->input->post('remarks'),
			'added_on'=>$date,
			'screenshot'=>$screenshot,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$query = $this->db->select('a.*, b.ticket_id, b.id, b.user_id, b.ticket_id, c.user_id, c.first_name as updated_by_fname, c.last_name as updated_by_lname, d.user_id,d.email,d.user_id')->from('maintenance_support_ticket_progress a')->join('maintenance_support b','a.ticket_id=b.id','left')->join('system_users c','a.added_by=c.user_id','left')->join('system_users d','b.added_by=d.user_id','left')->where('a.ticket_id',$this->uri->segment(3))->get();
			foreach($query->result() as $ticket_info);
			$this->db->select('email,name')->from('support_email_option')->where('support_option',1);
             $query = $this->db->get();
			 $res = $query->row_array();
			 $toemail = $res['email'];
             $toname = $res['name'];

			$this->db->select('email')->from('support_email_options')->where('support_option',4);
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
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://pms.shubhampack.in/assets/images/shubhampack.png" width="200px;" alt="Shubham Pack" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>MAINTENANCE SUPPORT REQUEST</strong><br></td>
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
				$subjectname = "Maintenance Support Request ".$ticket_info->ticket_id;
					$this->email->set_mailtype("html");
					//$this->email->to($ticket_info->email);
					$this->email->to($toemail);
					$this->email->cc($ccemails,$ticket_info->email);
					$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('taskmanagement@shubhampack.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();
			
			
			
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Maintenance_support/view_history/'.$this->uri->segment(3));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Maintenance_support/view_history'.$this->uri->segment(3));
		}
	}
	}

	public function sendlevelone()
	{   $json = array();
		$ticket_id = $this->input->post('ticket_id');
		
	    
	    $user_id =$this->session->userdata['logged_in']['user_id'];		
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "maintenance_support_ticket_progress";
			$data = array('ticket_id'=>$ticket_id,
			'remarks'=>'Please update the progess on the task',
			'added_on'=>$date,
			'added_by'=>$user_id,
            'level_flag'=>1);
			
			
		$result  = $this->master->insert_record($table,$data);		
		if($result)
		{
			
			$query = $this->db->select('a.*,b.company_name, b.business_loc_id, b.address, c.user_id, c.first_name, c.email, c.last_name,d.user_id,d.user_id, d.first_name as raised_by_fname, d.last_name as raised_by_lname, e.department_id, e.department, f.user_id, f.first_name as closed_by_fname, f.last_name as closed_by_lname')->from('maintenance_support a')->join('business_location b','a.business_location=b.business_loc_id','left')->join('departments e','e.department_id=a.department_id','left')->join('system_users c','a.user_id=c.user_id','left')->join('system_users d','a.added_by=d.user_id','left')->join('system_users f','a.updated_by=f.user_id','left')->where('id',$ticket_id)->get();
			foreach($query->result() as $ticket_info);
			    $this->db->select('email,name')->from('support_email_option')->where('support_option',4);
             $query = $this->db->get();
			 $res = $query->row_array();
	
             $toemail = $res['email'];
             $toname = $res['name'];

			

			 $this->db->select('email')->from('support_email_options')->where('support_option',4);
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
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://pms.shubhampack.in/assets/images/shubhampack.png" width="200px;" alt="Shubham Pack" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>MAINTENANCE SUPPORT REQUEST</strong><br></td>
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
				$subjectname = "Maintenance Support Request ".$ticket_info->ticket_id;
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
		$table = "maintenance_support_ticket_progress";
			$data = array('ticket_id'=>$ticket_id,
			'remarks'=>'Not received any response from the Tech Support. Please look into this',
			'added_on'=>$date,
			'added_by'=>$user_id,
            'level_flag'=>2);
			
			
		$result  = $this->master->insert_record($table,$data);		
		if($result)
		{
			
			$query = $this->db->select('a.*,b.company_name, b.business_loc_id, b.address, c.user_id, c.first_name, c.email, c.last_name,d.user_id,d.user_id, d.first_name as raised_by_fname, d.last_name as raised_by_lname, e.department_id, e.department, f.user_id, f.first_name as closed_by_fname, f.last_name as closed_by_lname')->from('maintenance_support a')->join('business_location b','a.business_location=b.business_loc_id','left')->join('departments e','e.department_id=a.department_id','left')->join('system_users c','a.user_id=c.user_id','left')->join('system_users d','a.added_by=d.user_id','left')->join('system_users f','a.updated_by=f.user_id','left')->where('id',$ticket_id)->get();
			foreach($query->result() as $ticket_info);
			    $this->db->select('email')->from('support_email_option')->where('support_option',4);
             $query = $this->db->get();
			 $res = $query->row_array();
	
             $toemail = $res['email'];

			

			 $this->db->select('email')->from('support_email_options')->where('support_option',4);
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
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://pms.shubhampack.in/assets/images/shubhampack.png" width="200px;" alt="Shubham Pack" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>MAINTENANCE SUPPORT REQUEST</strong><br></td>
					  </tr> 
					  <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>Ticket ID #'.$ticket_info->ticket_id.'</strong><br><br></td>
					  </tr> 
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;">Dear Sir ,<br> New Ticket has been raised. Please find the detail.
						</td>
					  </tr>
					  
					   <tr>
						<td>&nbsp;</td>
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
			   $subjectname = "Maintenance Support Request ".$ticket_info->ticket_id;
					$this->email->set_mailtype("html");
					//$this->email->to('gaurav@Shubham Pack.com,vishal@Shubham Pack.com');
					$this->email->cc($toemail);
					$this->email->bcc('mangleshup@gmail.com');
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
		    $this->load->view('maintenance_support/closed_ticket_list');
		}
		
		
		public function view_raised_ticket_closing_list()
	{
		$i=1;
		$support_ticket = array();
		$this->db->select('a.*,b.company_name, b.business_loc_id, b.address, c.user_id, c.first_name, c.last_name,d.user_id,d.user_id, d.first_name as raised_by_fname, d.last_name as raised_by_lname, e.department_id, e.department, f.user_id, f.first_name as closed_by_fname, f.last_name as closed_by_lname')->from('maintenance_support a')->join('business_location b','a.business_location=b.business_loc_id','left')->join('departments e','e.department_id=a.department_id','left')->join('system_users c','a.user_id=c.user_id','left')->join('system_users d','a.added_by=d.user_id','left')->join('system_users f','a.updated_by=f.user_id','left');
		$this->db->where('a.status','1');
		$this->db->order_by('a.added_on','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				
			$status ="<a href='".page_url."Maintenance_support/update_ticket_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Reopen</span></a>";
				$updateddate = date('Y-m-d', strtotime($row->updated_on));
				$time = date('H:i:s', strtotime($row->updated_on));
				$updatedtime = "<br>". date('g:i A', strtotime($time)); 
				$sta = $status."<br>"."This ticket was closed by <strong>".$row->closed_by_fname." ".$row->closed_by_lname."</strong><br> on ".$updateddate." ".$updatedtime;
				
			}else
			{
				$sta ="<a href='".page_url."Maintenance_support/update_ticket_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Pending</span></a>";
			}
			$history = "<a href='".page_url."Maintenance_support/view_history/".$row->id."'><span class='btn btn-warning btn-xs'><i class='fa fa-eye'></i>&nbsp; Add /See Comments</span></a>";
			$edit = "<a href='".page_url."Maintenance_support/edit_raised_ticket/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			
			if($row->screenshot){
			$before = "<img src='".maintenance.$row->screenshot."' width='100px'>";
			}else{
			   $before=""; 
			}
			$after = "";
			$query = $this->db->select('id,ticket_id, screenshot')->from('maintenance_support_ticket_progress')->where('ticket_id',$row->id)->limit('1')->order_by('id','desc')->get();
			$screen_report = $query->result();
			foreach($screen_report as $getdata)
			{
			    if($getdata->screenshot!==''){
			     $after = "<img src='".maintenance.$getdata->screenshot."' width='100px'>";   
			    }else{
			        $after = "";
			    }
			}
		
		
			$addeddate = date('Y-m-d', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 	
			$support_ticket[] = array('sr_no'=>$i,
			'ticket_id'=>$row->ticket_id,
			'business_loc'=>$row->company_name,
			'department'=>$row->department,
			'ticket'=>$row->ticket,
			'status'=>$sta,
			'added_on'=>$addeddate."".$addedtime,
			'added_by'=>$row->raised_by_fname." ".$row->raised_by_lname,
			'before'=>$before,
			'after'=>$after,
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
	
		public function view_raised_closed_ticket_list_by_department()
	{
		$query = $this->db->select('user_id, department_id')->from('system_users')->where('department_id',$this->uri->segment(3))->get();
		$res = $query->result();
		foreach($res as $departmentdata)
		$user_id[] = $departmentdata->user_id;
		//echo "<pre>"; print_r($user_id); exit;
		$i=1;
		$support_ticket = array();
		$this->db->select('a.*,b.company_name, b.business_loc_id, b.address, c.user_id, c.first_name, c.last_name,d.user_id,d.user_id, d.first_name as raised_by_fname, d.last_name as raised_by_lname, e.department_id, e.department, f.user_id, f.first_name as closed_by_fname, f.last_name as closed_by_lname')->from('maintenance_support a')->join('business_location b','a.business_location=b.business_loc_id','left')->join('departments e','e.department_id=a.department_id','left')->join('system_users c','a.user_id=c.user_id','left')->join('system_users d','a.added_by=d.user_id','left')->join('system_users f','a.updated_by=f.user_id','left');
		$this->db->where('a.status','1');
		$this->db->where_in('a.added_by',$user_id);
		$this->db->order_by('a.added_on','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			$status = $row->status;
			if($status=='1')
			{
				
				$status ="<a href='".page_url."Maintenance_support/update_ticket_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Reopen</span></a>";
				$updateddate = date('Y-m-d', strtotime($row->updated_on));
				$time = date('H:i:s', strtotime($row->updated_on));
				$updatedtime = "<br>". date('g:i A', strtotime($time)); 
				$sta = $status."<br>"."This ticket was closed by <strong>".$row->closed_by_fname." ".$row->closed_by_lname."</strong><br> on ".$updateddate." ".$updatedtime;
				
			}else
			{
				$sta ="<a href='".page_url."Maintenance_support/update_ticket_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Pending</span></a>";
			}
			if($row->screenshot){
			$before = "<img src='".maintenance.$row->screenshot."' width='100px'>";
			}else{
			   $before=""; 
			}
			$after = "";
			$query = $this->db->select('id,ticket_id, screenshot')->from('maintenance_support_ticket_progress')->where('ticket_id',$row->id)->limit('1')->order_by('id','desc')->get();
			$screen_report = $query->result();
			foreach($screen_report as $getdata)
			{
			    if($getdata->screenshot!==''){
			     $after = "<img src='".maintenance.$getdata->screenshot."' width='100px'>";   
			    }else{
			        $after = "";
			    }
			}
		
			$history = "<a href='".page_url."Maintenance_support/view_history/".$row->id."'><span class='btn btn-warning btn-xs'><i class='fa fa-eye'></i>&nbsp; Add /See Comments</span></a>";
			$edit = "<a href='".page_url."Maintenance_support/edit_raised_ticket/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			
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
			'before'=>$before,
			'after'=>$after,
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
	
	
	function compress_image($source_url, $destination_url, $quality) {


		$info = getimagesize($source_url);

    		if ($info['mime'] == 'image/jpeg')
        			$image = imagecreatefromjpeg($source_url);

    		elseif ($info['mime'] == 'image/gif')
        			$image = imagecreatefromgif($source_url);

   		elseif ($info['mime'] == 'image/png')
        			$image = imagecreatefrompng($source_url);

    		imagejpeg($image, $destination_url, $quality);
		return $destination_url;
	}


function thumbnail( $img, $source, $dest, $maxw, $maxh ) {      
    $jpg = $source.$img;
   // echo $jpg;exit;

    if( $jpg ) {
        list( $width, $height  ) = getimagesize( $jpg ); //$type will return the type of the image
        $source = imagecreatefromjpeg( $jpg );

        if( $maxw >= $width && $maxh >= $height ) {
            $ratio = 1;
        }elseif( $width > $height ) {
            $ratio = $maxw / $width;
        }else {
            $ratio = $maxh / $height;
        }

        $thumb_width = round( $width * $ratio ); //get the smaller value from cal # floor()
        $thumb_height = round( $height * $ratio );

        $thumb = imagecreatetruecolor( $thumb_width, $thumb_height );
        imagecopyresampled( $thumb, $source, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height );

        $path = $dest.$img;
       // echo $path;exit;
        imagejpeg( $thumb, $path, 75 );
    }
    imagedestroy( $thumb );
    imagedestroy( $source );
}


public function fetch_notificationscommon()
{
    $user_id = $this->session->userdata['logged_in']['user_id'];
    $department_id = $this->session->userdata['logged_in']['department_id'];

    $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    $this->output->set_header('Pragma: no-cache');
    $this->output->set_header('Expires: 0');

    $this->db->select('*')->from('df_support_notifications')
        ->where('is_read', 0)
        ->order_by('created_at', 'DESC');
    
    $query = $this->db->get();
    echo json_encode($query->result());
}

public function fetch_notificationsofusers()
{
    $user_id = $this->session->userdata['logged_in']['user_id'];
    $department_id = $this->session->userdata['logged_in']['department_id'];

    $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    $this->output->set_header('Pragma: no-cache');
    $this->output->set_header('Expires: 0');

    $this->db->select('*')->from('df_support_notifications')
        ->where('is_read', 0)
            ->where('user_id', $user_id)
        ->order_by('created_at', 'DESC');
    
    $query = $this->db->get();
    echo json_encode($query->result());
}

public function mark_notifications_read()
{
    $this->load->database();
    $user_id = $this->session->userdata('logged_in')['user_id'];
    $notification_id = $this->input->post('notification_id');
    $response = ['status' => 'error', 'message' => 'Unable to mark notifications as read.'];

    $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    $this->output->set_header('Pragma: no-cache');
    $this->output->set_header('Expires: 0');

    if ($notification_id) {
        $this->db->where('user_id', $user_id);
        $this->db->where('id', $notification_id);
        $updated = $this->db->update('df_support_notifications', ['is_read' => 1]);

        if ($updated) {
            $response = ['status' => 'success', 'message' => 'Notification marked as read.'];
        }
    } else {
        $this->db->where('user_id', $user_id);
        $updated = $this->db->update('df_support_notifications', ['is_read' => 1]);

        if ($updated) {
            $response = ['status' => 'success', 'message' => 'All notifications marked as read.'];
        }
    }

    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));
}

public function user_list_new() {
    $departments = $this->input->post('department'); // Accept multiple department IDs
    $this->db->select('user_id, first_name, last_name');
    $this->db->from('system_users');
    $this->db->where_in('department_id', $departments); // Use where_in for multiple departments
    $this->db->where('user_status', 1);
    $query = $this->db->get();

    $users = $query->result();
    $options = '';
    foreach ($users as $user) {
        $options .= '<option value="' . $user->user_id . '">' . strtoupper($user->first_name." ".$user->last_name) . '</option>';
    }
    echo $options;
}

private function calculate_meeting_duration($start_time, $end_time){
    if ($start_time == '' || $end_time == '') {
        return false;
    }

    $start_timestamp = strtotime($start_time);
    $end_timestamp = strtotime($end_time);

    if ($start_timestamp === false || $end_timestamp === false || $end_timestamp < $start_timestamp) {
        return false;
    }

    $duration_in_seconds = $end_timestamp - $start_timestamp;
    $hours = floor($duration_in_seconds / 3600);
    $minutes = floor(($duration_in_seconds % 3600) / 60);
    $seconds = $duration_in_seconds % 60;

    return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
}

private function format_meeting_time_range($start_time, $end_time = ''){
    if ($start_time == '' || strtotime($start_time) === false) {
        return '';
    }

    $formatted_start_time = date('h:i A', strtotime($start_time));

    if ($end_time != '' && $end_time != '00:00:00' && strtotime($end_time) !== false) {
        return $formatted_start_time . ' - ' . date('h:i A', strtotime($end_time));
    }

    return $formatted_start_time;
}

private function meeting_duration_to_seconds($duration){
    if ($duration == '' || strpos($duration, ':') === false) {
        return 0;
    }

    $duration_parts = explode(':', $duration);
    if (count($duration_parts) != 3) {
        return 0;
    }

    $hours = intval($duration_parts[0]);
    $minutes = intval($duration_parts[1]);
    $seconds = intval($duration_parts[2]);

    return max(0, ($hours * 3600) + ($minutes * 60) + $seconds);
}

private function format_duration_from_seconds($seconds){
    $seconds = max(0, intval($seconds));
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $remaining_seconds = $seconds % 60;

    return sprintf('%02d:%02d:%02d', $hours, $minutes, $remaining_seconds);
}

private function format_duration_human($seconds){
    $seconds = max(0, intval($seconds));
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $remaining_seconds = $seconds % 60;

    if ($hours > 0) {
        return $hours . 'h ' . str_pad($minutes, 2, '0', STR_PAD_LEFT) . 'm';
    }

    if ($minutes > 0) {
        return $minutes . 'm ' . str_pad($remaining_seconds, 2, '0', STR_PAD_LEFT) . 's';
    }

    return $remaining_seconds . 's';
}

private function build_text_preview($text, $limit = 120){
    $plain_text = trim(preg_replace('/\s+/', ' ', strip_tags((string) $text)));

    if (strlen($plain_text) <= $limit) {
        return $plain_text;
    }

    return rtrim(substr($plain_text, 0, $limit - 3)) . '...';
}

private function format_person_name($name, $fallback = 'Not Available'){
    $name = trim(preg_replace('/\s+/', ' ', (string) $name));

    if ($name == '') {
        return $fallback;
    }

    return ucwords(strtolower($name));
}

private function meeting_has_other_person_column(){
    static $has_other_person_column = null;

    if ($has_other_person_column === null) {
        $has_other_person_column = $this->db->field_exists('other_person_name', 'team_time_calculation');
    }

    return $has_other_person_column;
}

private function is_other_meeting_department($department_value){
    return strtoupper(trim((string) $department_value)) === 'OTHERS';
}

private function get_meeting_department_label($department_id, $department_name = ''){
    if ((string) $department_id === '0' || $this->is_other_meeting_department($department_name)) {
        return 'Others';
    }

    $department_name = trim((string) $department_name);

    if ($department_name == '') {
        return 'Unassigned';
    }

    return $department_name;
}

private function get_meeting_participant_label($meeting_row){
    $other_person_name = '';
    if (is_array($meeting_row) && isset($meeting_row['other_person_name'])) {
        $other_person_name = $meeting_row['other_person_name'];
    } elseif (is_object($meeting_row) && isset($meeting_row->other_person_name)) {
        $other_person_name = $meeting_row->other_person_name;
    }

    $department_id = is_array($meeting_row)
        ? (isset($meeting_row['department_id']) ? $meeting_row['department_id'] : '')
        : (isset($meeting_row->department_id) ? $meeting_row->department_id : '');

    if ((string) $department_id === '0') {
        return $this->format_person_name($other_person_name, 'External Participant');
    }

    $participant_name = '';
    if (is_array($meeting_row)) {
        $participant_name = trim(
            (isset($meeting_row['mainusertitle']) ? $meeting_row['mainusertitle'] . ' ' : '') .
            (isset($meeting_row['first_name']) ? $meeting_row['first_name'] . ' ' : '') .
            (isset($meeting_row['last_name']) ? $meeting_row['last_name'] : '')
        );
    } else {
        $participant_name = trim(
            (isset($meeting_row->mainusertitle) ? $meeting_row->mainusertitle . ' ' : '') .
            (isset($meeting_row->first_name) ? $meeting_row->first_name . ' ' : '') .
            (isset($meeting_row->last_name) ? $meeting_row->last_name : '')
        );
    }

    return $this->format_person_name($participant_name, 'Not Available');
}

private function get_meeting_logs($filters = array()){
    $filter_startdate = isset($filters['startdate']) ? trim((string) $filters['startdate']) : '';
    $filter_enddate = isset($filters['enddate']) ? trim((string) $filters['enddate']) : '';
    $filter_userid = isset($filters['userid']) ? trim((string) $filters['userid']) : '';
    $filter_department_id = isset($filters['department_id']) ? trim((string) $filters['department_id']) : '';

    $this->db->select('a.*, b.first_name, b.last_name, b.title as mainusertitle, c.department, d.title, d.first_name as fname, d.last_name as lname, e.df_no, e.df_description');
    $this->db->from('team_time_calculation a');
    $this->db->join('system_users b', 'a.user_id = b.user_id', 'left');
    $this->db->join('departments c', 'a.department_id = c.department_id', 'left');
    $this->db->join('system_users d', 'a.added_by = d.user_id', 'left');
    $this->db->join('df_release e', 'a.df_id = e.id', 'left');

    if ($_SESSION['logged_in']['role'] != 12) {
        $this->db->where('a.added_by', $_SESSION['logged_in']['user_id']);
    } elseif ($filter_userid != '') {
        $this->db->where('a.added_by', $filter_userid);
    }

    if ($filter_department_id != '') {
        $this->db->where('a.department_id', $filter_department_id);
    }

    if ($filter_startdate != '') {
        $this->db->where('a.added_on >=', date('Y-m-d', strtotime($filter_startdate)) . ' 00:00:00');
    }

    if ($filter_enddate != '') {
        $this->db->where('a.added_on <=', date('Y-m-d', strtotime($filter_enddate)) . ' 23:59:59');
    }

    $this->db->order_by('a.added_on', 'DESC');

    return $this->db->get()->result_array();
}

private function get_meeting_filter_options(){
    $this->db->select('department_id, department');
    $this->db->from('departments');
    $this->db->where('business_loc_id', 2);
    $this->db->where('status', 1);
    $this->db->order_by('department', 'ASC');
    $departments = $this->db->get()->result_array();
    $departments[] = array(
        'department_id' => '0',
        'department' => 'Others'
    );

    $this->db->select('user_id, title, first_name, last_name');
    $this->db->from('system_users');
    $this->db->where('user_status', 1);
    if ($_SESSION['logged_in']['role'] != 12) {
        $this->db->where('user_id', $_SESSION['logged_in']['user_id']);
    }
    $this->db->order_by('first_name', 'ASC');
    $users = $this->db->get()->result_array();

    return array(
        'departments' => $departments,
        'users' => $users
    );
}

private function build_meeting_filter_summary($filters, $users, $departments){
    $user_label = ($_SESSION['logged_in']['role'] == 12) ? 'All Conductors' : 'My Meetings';
    $department_label = 'All Departments';
    $period_label = 'All available records';
    $is_filtered = false;

    if (!empty($filters['userid'])) {
        foreach ($users as $user) {
            if ((string) $user['user_id'] === (string) $filters['userid']) {
                $user_label = $this->format_person_name($user['title'] . ' ' . $user['first_name'] . ' ' . $user['last_name'], 'Not Available');
                break;
            }
        }
        $is_filtered = true;
    }

    if (isset($filters['department_id']) && (string) $filters['department_id'] !== '') {
        foreach ($departments as $department) {
            if ((string) $department['department_id'] === (string) $filters['department_id']) {
                $department_label = $this->get_meeting_department_label($department['department_id'], $department['department']);
                break;
            }
        }
        $is_filtered = true;
    }

    if (!empty($filters['startdate']) && !empty($filters['enddate'])) {
        $period_label = date('d M Y', strtotime($filters['startdate'])) . ' to ' . date('d M Y', strtotime($filters['enddate']));
        $is_filtered = true;
    } elseif (!empty($filters['startdate'])) {
        $period_label = 'From ' . date('d M Y', strtotime($filters['startdate']));
        $is_filtered = true;
    } elseif (!empty($filters['enddate'])) {
        $period_label = 'Till ' . date('d M Y', strtotime($filters['enddate']));
        $is_filtered = true;
    }

    return array(
        'period' => $period_label,
        'user' => $user_label,
        'department' => $department_label,
        'is_filtered' => $is_filtered
    );
}

private function build_meeting_dashboard_data($tickets, $filters, $filter_summary){
    $prepared_tickets = array();
    $daily_trend = array();
    $department_breakdown = array();
    $conductor_breakdown = array();
    $participant_breakdown = array();
    $time_slot_breakdown = array(
        'Morning' => array('label' => 'Morning', 'count' => 0, 'seconds' => 0),
        'Afternoon' => array('label' => 'Afternoon', 'count' => 0, 'seconds' => 0),
        'Evening' => array('label' => 'Evening', 'count' => 0, 'seconds' => 0)
    );

    $total_meetings = count($tickets);
    $total_duration_seconds = 0;
    $manual_range_count = 0;
    $auto_timer_count = 0;
    $attachment_count = 0;
    $df_linked_count = 0;
    $unique_departments = array();
    $unique_participants = array();
    $unique_conductors = array();
    $last_logged_on_display = 'No records yet';
    $longest_meeting = null;

    foreach ($tickets as $ticket) {
        $department_name = $this->get_meeting_department_label(
            isset($ticket['department_id']) ? $ticket['department_id'] : '',
            isset($ticket['department']) ? $ticket['department'] : ''
        );

        $participant_name = $this->get_meeting_participant_label($ticket);
        $conductor_name = $this->format_person_name($ticket['title'] . ' ' . $ticket['fname'] . ' ' . $ticket['lname'], 'Not Available');

        $duration_seconds = $this->meeting_duration_to_seconds(isset($ticket['total_time_spend']) ? $ticket['total_time_spend'] : '');
        $duration_clock = $this->format_duration_from_seconds($duration_seconds);
        $duration_human = $this->format_duration_human($duration_seconds);
        $meeting_mode = (!empty($ticket['meeting_end_time']) && $ticket['meeting_end_time'] != '00:00:00') ? 'Manual Range' : 'Auto Timer';
        $meeting_time_display = $this->format_meeting_time_range($ticket['meeting_time'], isset($ticket['meeting_end_time']) ? $ticket['meeting_end_time'] : '');
        $has_attachment = !empty($ticket['attachment']);
        $has_df = !empty($ticket['df_no']);
        $meeting_date_key = !empty($ticket['meeting_date']) ? $ticket['meeting_date'] : 'Unknown';
        $meeting_date_display = !empty($ticket['meeting_date']) ? date('d M Y', strtotime($ticket['meeting_date'])) : 'N/A';

        $prepared_ticket = $ticket;
        $prepared_ticket['participant_name'] = $participant_name;
        $prepared_ticket['conductor_name'] = $conductor_name;
        $prepared_ticket['department_name'] = $department_name;
        $prepared_ticket['meeting_time_display'] = $meeting_time_display;
        $prepared_ticket['duration_seconds'] = $duration_seconds;
        $prepared_ticket['duration_clock'] = $duration_clock;
        $prepared_ticket['duration_human'] = $duration_human;
        $prepared_ticket['meeting_mode'] = $meeting_mode;
        $prepared_ticket['has_attachment'] = $has_attachment;
        $prepared_ticket['has_df'] = $has_df;
        $prepared_ticket['remarks_preview'] = $this->build_text_preview(isset($ticket['remarks']) ? $ticket['remarks'] : '', 120);
        $prepared_ticket['meeting_date_display'] = $meeting_date_display;
        $prepared_ticket['attachment_url'] = $has_attachment ? maintenance . $ticket['attachment'] : '';

        $prepared_tickets[] = $prepared_ticket;

        $total_duration_seconds += $duration_seconds;
        $unique_departments[$department_name] = true;
        $unique_participants[$participant_name] = true;
        $unique_conductors[$conductor_name] = true;

        if ($meeting_mode == 'Manual Range') {
            $manual_range_count++;
        } else {
            $auto_timer_count++;
        }

        if ($has_attachment) {
            $attachment_count++;
        }

        if ($has_df) {
            $df_linked_count++;
        }

        if (!isset($daily_trend[$meeting_date_key])) {
            $daily_trend[$meeting_date_key] = array(
                'label' => $meeting_date_display,
                'count' => 0,
                'seconds' => 0
            );
        }
        $daily_trend[$meeting_date_key]['count']++;
        $daily_trend[$meeting_date_key]['seconds'] += $duration_seconds;

        if (!isset($department_breakdown[$department_name])) {
            $department_breakdown[$department_name] = array(
                'label' => $department_name,
                'meeting_count' => 0,
                'seconds' => 0
            );
        }
        $department_breakdown[$department_name]['meeting_count']++;
        $department_breakdown[$department_name]['seconds'] += $duration_seconds;

        if (!isset($conductor_breakdown[$conductor_name])) {
            $conductor_breakdown[$conductor_name] = array(
                'label' => $conductor_name,
                'meeting_count' => 0,
                'seconds' => 0
            );
        }
        $conductor_breakdown[$conductor_name]['meeting_count']++;
        $conductor_breakdown[$conductor_name]['seconds'] += $duration_seconds;

        if (!isset($participant_breakdown[$participant_name])) {
            $participant_breakdown[$participant_name] = array(
                'label' => $participant_name,
                'meeting_count' => 0,
                'seconds' => 0
            );
        }
        $participant_breakdown[$participant_name]['meeting_count']++;
        $participant_breakdown[$participant_name]['seconds'] += $duration_seconds;

        $meeting_hour = intval(date('H', strtotime($ticket['meeting_time'])));
        if ($meeting_hour < 12) {
            $slot_key = 'Morning';
        } elseif ($meeting_hour < 17) {
            $slot_key = 'Afternoon';
        } else {
            $slot_key = 'Evening';
        }
        $time_slot_breakdown[$slot_key]['count']++;
        $time_slot_breakdown[$slot_key]['seconds'] += $duration_seconds;

        if ($longest_meeting === null || $duration_seconds > $longest_meeting['duration_seconds']) {
            $longest_meeting = array(
                'duration_seconds' => $duration_seconds,
                'duration_clock' => $duration_clock,
                'participant_name' => $participant_name,
                'conductor_name' => $conductor_name,
                'meeting_date_display' => $meeting_date_display,
                'department_name' => $department_name
            );
        }
    }

    foreach ($department_breakdown as $key => $department_data) {
        $department_breakdown[$key]['duration_clock'] = $this->format_duration_from_seconds($department_data['seconds']);
        $department_breakdown[$key]['duration_human'] = $this->format_duration_human($department_data['seconds']);
        $department_breakdown[$key]['avg_duration_clock'] = $this->format_duration_from_seconds($department_data['meeting_count'] > 0 ? floor($department_data['seconds'] / $department_data['meeting_count']) : 0);
    }

    foreach ($conductor_breakdown as $key => $conductor_data) {
        $conductor_breakdown[$key]['duration_clock'] = $this->format_duration_from_seconds($conductor_data['seconds']);
        $conductor_breakdown[$key]['duration_human'] = $this->format_duration_human($conductor_data['seconds']);
        $conductor_breakdown[$key]['avg_duration_clock'] = $this->format_duration_from_seconds($conductor_data['meeting_count'] > 0 ? floor($conductor_data['seconds'] / $conductor_data['meeting_count']) : 0);
    }

    foreach ($participant_breakdown as $key => $participant_data) {
        $participant_breakdown[$key]['duration_clock'] = $this->format_duration_from_seconds($participant_data['seconds']);
        $participant_breakdown[$key]['duration_human'] = $this->format_duration_human($participant_data['seconds']);
        $participant_breakdown[$key]['avg_duration_clock'] = $this->format_duration_from_seconds($participant_data['meeting_count'] > 0 ? floor($participant_data['seconds'] / $participant_data['meeting_count']) : 0);
    }

    foreach ($time_slot_breakdown as $key => $slot_data) {
        $time_slot_breakdown[$key]['duration_clock'] = $this->format_duration_from_seconds($slot_data['seconds']);
        $time_slot_breakdown[$key]['duration_human'] = $this->format_duration_human($slot_data['seconds']);
    }

    usort($department_breakdown, function($a, $b){
        if ($a['meeting_count'] == $b['meeting_count']) {
            return $b['seconds'] <=> $a['seconds'];
        }
        return $b['meeting_count'] <=> $a['meeting_count'];
    });

    usort($conductor_breakdown, function($a, $b){
        if ($a['meeting_count'] == $b['meeting_count']) {
            return $b['seconds'] <=> $a['seconds'];
        }
        return $b['meeting_count'] <=> $a['meeting_count'];
    });

    usort($participant_breakdown, function($a, $b){
        if ($a['meeting_count'] == $b['meeting_count']) {
            return $b['seconds'] <=> $a['seconds'];
        }
        return $b['meeting_count'] <=> $a['meeting_count'];
    });

    ksort($daily_trend);
    if (count($daily_trend) > 10) {
        $daily_trend = array_slice($daily_trend, -10, null, true);
    }

    $trend_labels = array();
    $trend_meeting_counts = array();
    $trend_duration_hours = array();
    foreach ($daily_trend as $trend_item) {
        $trend_labels[] = $trend_item['label'];
        $trend_meeting_counts[] = $trend_item['count'];
        $trend_duration_hours[] = round($trend_item['seconds'] / 3600, 2);
    }

    $time_slot_breakdown = array_values($time_slot_breakdown);

    $average_duration_seconds = $total_meetings > 0 ? floor($total_duration_seconds / $total_meetings) : 0;
    $average_meetings_per_day = count($daily_trend) > 0 ? round($total_meetings / count($daily_trend), 1) : 0;
    $attachment_rate = $total_meetings > 0 ? round(($attachment_count / $total_meetings) * 100) : 0;
    $manual_range_rate = $total_meetings > 0 ? round(($manual_range_count / $total_meetings) * 100) : 0;
    $top_department = !empty($department_breakdown) ? $department_breakdown[0] : null;
    $top_conductor = !empty($conductor_breakdown) ? $conductor_breakdown[0] : null;
    $top_participant = !empty($participant_breakdown) ? $participant_breakdown[0] : null;

    if (!empty($prepared_tickets) && !empty($prepared_tickets[0]['added_on'])) {
        $last_logged_on_display = date('d M Y h:i A', strtotime($prepared_tickets[0]['added_on']));
    }

    $insights = array();
    if ($top_department) {
        $insights[] = $top_department['label'] . ' leads the collaboration load with ' . $top_department['meeting_count'] . ' meetings and ' . $top_department['duration_human'] . ' of discussion time.';
    }
    if ($top_conductor) {
        $insights[] = $top_conductor['label'] . ' is the most active conductor with ' . $top_conductor['meeting_count'] . ' meetings logged in this view.';
    }
    if ($longest_meeting) {
        $insights[] = 'The longest meeting lasted ' . $longest_meeting['duration_clock'] . ' on ' . $longest_meeting['meeting_date_display'] . ' between ' . $longest_meeting['conductor_name'] . ' and ' . $longest_meeting['participant_name'] . '.';
    }
    $insights[] = $attachment_rate . '% of meetings include supporting files, while manual time ranges are used in ' . $manual_range_rate . '% of the records.';

    return array(
        'tickets' => $prepared_tickets,
        'dashboard' => array(
            'summary' => array(
                'total_meetings' => $total_meetings,
                'total_duration_clock' => $this->format_duration_from_seconds($total_duration_seconds),
                'total_duration_human' => $this->format_duration_human($total_duration_seconds),
                'average_duration_clock' => $this->format_duration_from_seconds($average_duration_seconds),
                'average_duration_human' => $this->format_duration_human($average_duration_seconds),
                'unique_departments' => count($unique_departments),
                'unique_participants' => count($unique_participants),
                'unique_conductors' => count($unique_conductors),
                'manual_range_count' => $manual_range_count,
                'auto_timer_count' => $auto_timer_count,
                'attachment_count' => $attachment_count,
                'attachment_rate' => $attachment_rate,
                'df_linked_count' => $df_linked_count,
                'average_meetings_per_day' => $average_meetings_per_day,
                'last_logged_on_display' => $last_logged_on_display,
                'manual_range_rate' => $manual_range_rate
            ),
            'filter_summary' => $filter_summary,
            'trend' => array(
                'labels' => $trend_labels,
                'meeting_counts' => $trend_meeting_counts,
                'duration_hours' => $trend_duration_hours
            ),
            'department_breakdown' => array_slice($department_breakdown, 0, 6),
            'conductor_breakdown' => array_slice($conductor_breakdown, 0, 5),
            'participant_breakdown' => array_slice($participant_breakdown, 0, 5),
            'time_slot_breakdown' => $time_slot_breakdown,
            'top_department' => $top_department,
            'top_conductor' => $top_conductor,
            'top_participant' => $top_participant,
            'longest_meeting' => $longest_meeting,
            'insights' => $insights
        )
    );
}

private function render_meeting_dashboard($filters = array()){
    $filter_options = $this->get_meeting_filter_options();
    $filter_summary = $this->build_meeting_filter_summary($filters, $filter_options['users'], $filter_options['departments']);
    $tickets = $this->get_meeting_logs($filters);
    $dashboard_payload = $this->build_meeting_dashboard_data($tickets, $filters, $filter_summary);

    $data = array(
        'tickets' => $dashboard_payload['tickets'],
        'dashboard' => $dashboard_payload['dashboard'],
        'filterdata' => $filters,
        'users' => $filter_options['users'],
        'departments' => $filter_options['departments']
    );

    $this->load->view('form/meeting-dashboard', $data);
}

function meetinglog(){
	$this->load->view('form/meeting-form.php');
}

function addmeetinginfo(){

    $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
    //$this->form_validation->set_rules('dfno', 'DF No', 'required|trim');
    $this->form_validation->set_rules('department', 'Department Selection', 'required|trim');
    $this->form_validation->set_rules('remarks', 'Remarks', 'required|trim');

    $user_id = $this->session->userdata['logged_in']['user_id'];

    $q = $this->db->select('department_id')->from('system_users')->where('user_id',$user_id)->get();
    foreach($q->result() as $departmentinfo);
    $logged_in_department_id = $departmentinfo->department_id;

	    if ($this->form_validation->run() == FALSE) {
	        $this->load->view('form/meeting-form.php');
	    } else {
            $selected_department = trim((string) $this->input->post('department'));
            $is_other_department = $this->is_other_meeting_department($selected_department);
            $meeting_df_id = trim((string) $this->input->post('dfno'));
            $meeting_user_id = trim((string) $this->input->post('user_id'));
            $other_person_name = trim((string) $this->input->post('other_person_name'));

            if ($is_other_department && !$this->meeting_has_other_person_column()) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger">Please run the latest meeting log database update before using the Others participant option.</div>');
                $this->load->view('form/meeting-form.php');
                return;
            }

            if (!$is_other_department && $meeting_df_id == '') {
                $this->session->set_flashdata('message', '<div class="alert alert-danger">Please select the DF for internal meetings.</div>');
                $this->load->view('form/meeting-form.php');
                return;
            }

            if (!$is_other_department && $meeting_user_id == '') {
                $this->session->set_flashdata('message', '<div class="alert alert-danger">Please select the meeting person.</div>');
                $this->load->view('form/meeting-form.php');
                return;
            }

            if ($is_other_department && $other_person_name == '') {
                $this->session->set_flashdata('message', '<div class="alert alert-danger">Please enter the outside participant name.</div>');
                $this->load->view('form/meeting-form.php');
                return;
            }

	        $logged_meeting_time = (string) $this->input->post('meetingtime');
	        $manual_meeting_start_time = trim((string) $this->input->post('manual_meeting_start_time'));
	        $manual_meeting_end_time = trim((string) $this->input->post('manual_meeting_end_time'));
	        $manual_time_requested = ($manual_meeting_start_time != '' || $manual_meeting_end_time != '');

	        if ($manual_time_requested) {
	            if ($manual_meeting_start_time == '' || $manual_meeting_end_time == '') {
	                $this->session->set_flashdata('message', '<div class="alert alert-danger">Please enter both meeting start time and meeting end time.</div>');
	                $this->load->view('form/meeting-form.php');
	                return;
	            }

	            $calculated_total_time = $this->calculate_meeting_duration($manual_meeting_start_time, $manual_meeting_end_time);
	            if ($calculated_total_time === false) {
	                $this->session->set_flashdata('message', '<div class="alert alert-danger">Meeting end time must be greater than or equal to meeting start time.</div>');
	                $this->load->view('form/meeting-form.php');
	                return;
	            }

	            $meeting_time = date('H:i:s', strtotime($manual_meeting_start_time));
	            $meeting_end_time = date('H:i:s', strtotime($manual_meeting_end_time));
	            $total_time_spend = $calculated_total_time;
	        } else {
	            $meeting_time = date('H:i:s', strtotime($logged_meeting_time));
	            $meeting_end_time = '';
	            $total_time_spend = (string) $this->input->post('total_time_spend');
	        }

            $meeting_department_id = $is_other_department ? 0 : $selected_department;
            $meeting_user_storage_id = $is_other_department ? 0 : $meeting_user_id;
            $meeting_department_name = 'Others';

            if (!$is_other_department) {
                $department_query = $this->db->select('department')->from('departments')->where('department_id', $meeting_department_id)->get();
                if ($department_query->num_rows() > 0) {
                    foreach ($department_query->result() as $department_row);
                    $meeting_department_name = $department_row->department;
                }
            }

	        $photo = $_FILES['screen_shot']['name'];
	        $screenshot = "";

        // Upload and compress image if attached
        if ($photo) {
            $image1 = explode('.', $photo);
            $cat_image = end($image1);
            $imgname = time();
            $screenshot = $imgname . '.' . $cat_image;
            $sourceurl = UPLOADPATH . 'maintenance/' . $screenshot;
            $this->compress_image($_FILES["screen_shot"]["tmp_name"], $sourceurl, 80);
            move_uploaded_file($_FILES["screen_shot"]["tmp_name"], $sourceurl);
        } else {
            $screenshot = '';
        }

       

	        $data = array(
	            'meeting_date' => date('Y-m-d'),
	            'meeting_time' => $meeting_time,
	            'df_id' => $meeting_df_id != '' ? (int) $meeting_df_id : 0,
	            'department_id' => $meeting_department_id,
	            'user_id' => $meeting_user_storage_id,
	            'total_time_spend' => $total_time_spend,
	            'remarks' => $this->input->post('remarks'),
	            'attachment' => $screenshot,
	            'added_on' => date('Y-m-d h:i:s'),
	            'added_by' => $user_id
	        );

	        if ($this->db->field_exists('meeting_end_time', 'team_time_calculation')) {
	            $data['meeting_end_time'] = ($meeting_end_time != '') ? $meeting_end_time : null;
	        }

            if ($this->meeting_has_other_person_column()) {
                $data['other_person_name'] = $is_other_department ? $other_person_name : null;
            }

	        $this->db->insert('team_time_calculation', $data);
        $insertid = $this->db->insert_id();

        if ($insertid) {
            // Fetch team leader information
            $q = $this->db->select('team_id')->from('presto_team_members')->where('employee_id', $user_id)->get();
            if ($q->num_rows() > 0) {
                foreach ($q->result() as $row);
                $q1 = $this->db->select('b.first_name, b.last_name, b.email')->from('prestogroup_teams a')
                    ->join('system_users b', 'a.team_leader=b.user_id', 'left')
                    ->where('a.team_id', $row->team_id)->get();
                if ($q1->num_rows() > 0) {
                    foreach ($q1->result() as $row);

                    // Team leader email details
                    $to_email_leader = $row->email;
                    $team_leader_name = $row->first_name . ' ' . $row->last_name;
                }
            }

            // Fetch meeting person details
            $meeting_person_name = $is_other_department ? $this->format_person_name($other_person_name, 'External Participant') : 'Not Available';
            if (!$is_other_department) {
                $q2 = $this->db->select('first_name, last_name, email')->from('system_users')->where('user_id', $meeting_user_id)->get();
                if ($q2->num_rows() > 0) {
                    foreach ($q2->result() as $meeting_user);
                    $meeting_person_name = $meeting_user->first_name . ' ' . $meeting_user->last_name;
                    $to_email_meeting_person = $meeting_user->email;
                }
            }

            // Fetch added by person details
            $q3 = $this->db->select('first_name, last_name, email')->from('system_users')->where('user_id', $user_id)->get();
            if ($q3->num_rows() > 0) {
                foreach ($q3->result() as $added_by_user);
                $added_by_name = $added_by_user->first_name . ' ' . $added_by_user->last_name;
                $added_by_email = $added_by_user->email;
            }

            // Prepare email content
            $remarks = $this->input->post('remarks');
            $dfinfo = 'Not Linked';
            if ($meeting_df_id != '') {
                $q = $this->db->select('df_no')->from('df_release')->where('id',$meeting_df_id)->get();
                if($q->num_rows()>0){
                	foreach($q->result() as $rowsssss);
                	$dfinfo = $rowsssss->df_no;
                }
            }
	            $meeting_time_label = $this->format_meeting_time_range($meeting_time, $meeting_end_time);
	            $meeting_date = date('Y-m-d');

	            $logo_url = "https://pms.shubhampack.in/assets/images/shubhampack.png";
	            $email_content = "
                <div style='font-family: Arial, sans-serif;'>
                    <img src='$logo_url' alt='Shubham Flexible Packaging' style='width: 200px; margin-bottom: 20px;'>
                    <p>Dear {recipient_name},</p>
	                    <p>The following meeting details have been recorded:</p>
	                    <ul>
	                        <li><strong>DF No:</strong> $dfinfo</li>
	                        <li><strong>Meeting Date:</strong> $meeting_date</li>
	                        <li><strong>Meeting Time:</strong> $meeting_time_label</li>
                            <li><strong>Department:</strong> " . $this->get_meeting_department_label($meeting_department_id, $meeting_department_name) . "</li>
                            <li><strong>Meeting With:</strong> " . $this->format_person_name($meeting_person_name, 'Not Available') . "</li>
	                        <li><strong>Total Time Spent:</strong> $total_time_spend</li>
	                        <li><strong>Remarks:</strong> $remarks</li>
	                    </ul>
	                    <p><strong>Information Filled By:</strong> $added_by_name ($added_by_email)</p>
                    <p>Best regards,<br>Team</p>
                </div>
            ";

            // Check if user's department is 11 and add RISHABH@SHUBHAMPACK.COM
           
            $additional_recipients = ($logged_in_department_id == 11) ? 'rishabhsharrma@shubhampack.com' : null;

            // Send email to Team Leader
            if (isset($to_email_leader)) {
                $this->email->from('taskmanagement@shubhampack.com', 'Shubham Flexible Packaging');
                $this->email->to($to_email_leader);
                if ($additional_recipients) {
                    $this->email->cc([$additional_recipients, $added_by_email]);
                } else {
                    $this->email->cc($added_by_email);
                }

                if($logged_in_department_id==11){
                    $this->email->cc('rishabhsharrma@shubhampack.com');
                }

                $this->email->bcc('mangleshup@gmail.com');
                $this->email->subject("Meeting Notification - Team Member");
                $this->email->message(str_replace("{recipient_name}", $team_leader_name, $email_content));

                if ($this->email->send()) {
                    log_message('info', "Email sent to Team Leader: $to_email_leader.");
                } else {
                    log_message('error', "Failed to send email to Team Leader: $to_email_leader.");
                }
            }

            // Send email to Meeting Person
            if (isset($to_email_meeting_person)) {
                $this->email->clear(); // Clear previous email settings
                $this->email->from('taskmanagement@shubhampack.com', 'Shubham Flexible Packaging');
                $this->email->to($to_email_meeting_person);
                if ($additional_recipients) {
                    $this->email->cc([$additional_recipients, $added_by_email]);
                } else {
                    $this->email->cc($added_by_email);
                }
                $this->email->bcc('mangleshup@gmail.com');
                $this->email->subject("Meeting Notification - Meeting Details");
                $this->email->message(str_replace("{recipient_name}", $meeting_person_name, $email_content));

                if ($this->email->send()) {
                    log_message('info', "Email sent to Meeting Person: $to_email_meeting_person.");
                } else {
                    log_message('error', "Failed to send email to Meeting Person: $to_email_meeting_person.");
                }
            }

            // Redirect
            $this->session->set_flashdata('message', '<div class="alert alert-success">Thank you, record successfully added.</div>');
            redirect(page_url . 'Maintenance_support/meetinglog');
        }
    }
}

function listmeetings(){
    $this->render_meeting_dashboard(array(
        'startdate' => '',
        'enddate' => '',
        'userid' => '',
        'department_id' => ''
    ));
}

function filterlistmeetings(){
    $startdate = trim((string) $this->input->post('startdate'));
    $enddate = trim((string) $this->input->post('enddate'));
    $userid = trim((string) $this->input->post('user_id'));
    $department_id = trim((string) $this->input->post('department_id'));

    $this->render_meeting_dashboard(array(
        'startdate' => $startdate != '' ? date('Y-m-d', strtotime($startdate)) : '',
        'enddate' => $enddate != '' ? date('Y-m-d', strtotime($enddate)) : '',
        'userid' => $userid,
        'department_id' => $department_id
    ));
}


function sendmeetingdonereport(){
    $departusr = array();
    $qq = $this->db->select('user_id')->from('system_users')->where('department_id', 11)->get();
    foreach($qq->result() as $row) {
        $departusr[] = $row->user_id; // Fixed this part to collect all user_ids
    }

    if (!empty($departusr)) {
	        $meeting_report_select = 'a.meeting_date, a.meeting_time, a.department_id, a.user_id, ';
	        if ($this->db->field_exists('meeting_end_time', 'team_time_calculation')) {
	            $meeting_report_select .= 'a.meeting_end_time, ';
	        }
            if ($this->meeting_has_other_person_column()) {
                $meeting_report_select .= 'a.other_person_name, ';
            }
	        $meeting_report_select .= 'b.df_no, c.department, d.title, d.first_name, d.last_name, a.total_time_spend, a.remarks, e.title as persontitle, e.first_name as fname, e.last_name as lname';

	        $results = $this->db->select($meeting_report_select)
	            ->from('team_time_calculation a')
	            ->join('df_release b', 'a.df_id=b.id', 'left')
	            ->join('departments c', 'a.department_id=c.department_id', 'left')
            ->join('system_users d', 'a.user_id=d.user_id', 'left')
            ->join('system_users e', 'a.added_by=e.user_id', 'left')
            ->where_in('a.added_by', $departusr)
            ->where('DATE(a.meeting_date)', date('Y-m-d'))
            ->get()
            ->result_array();
            //echo "<pre>"; print_r($results); exit;
    } else {
        $results = [];
        log_message('info', 'No users found for department_id 11.');
    }

    log_message('debug', 'Meeting Results: ' . print_r($results, true)); // Log debug information

    if (!empty($results)) {
        $emailBody = $this->generateEmailContent($results);
        $this->sendEmailToTeamLeaders($emailBody);
    } else {
        log_message('info', 'No meetings recorded for department_id 11 today.');
    }
}


/**
 * Generate the email content for meeting information in tabular format.
 *
 * @param array $results
 * @return string
 */
function generateEmailContent($results) {
    $tableRows = '';
    $srNo = 1;
    foreach ($results as $row) {
        $meetingTimeDisplay = $this->format_meeting_time_range($row['meeting_time'], isset($row['meeting_end_time']) ? $row['meeting_end_time'] : '');
        $meetingDepartmentLabel = $this->get_meeting_department_label(isset($row['department_id']) ? $row['department_id'] : '', isset($row['department']) ? $row['department'] : '');
        $meetingParticipantLabel = $this->get_meeting_participant_label(array(
            'department_id' => isset($row['department_id']) ? $row['department_id'] : '',
            'other_person_name' => isset($row['other_person_name']) ? $row['other_person_name'] : '',
            'mainusertitle' => isset($row['title']) ? $row['title'] : '',
            'first_name' => isset($row['first_name']) ? $row['first_name'] : '',
            'last_name' => isset($row['last_name']) ? $row['last_name'] : ''
        ));

        $tableRows .= '
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;">' . $srNo++ . '</td>
                <td style="padding: 10px; border: 1px solid #ddd;">' . date('d-m-Y',strtotime($row['meeting_date'])) . '</td>
                <td style="padding: 10px; border: 1px solid #ddd;">' . $meetingTimeDisplay . '</td>
                <td style="padding: 10px; border: 1px solid #ddd;">' . $row['total_time_spend'] . '</td>
                
                <td style="padding: 10px; border: 1px solid #ddd;">' . ucwords(strtolower($row['persontitle'] . ' ' . $row['fname'] . ' ' . $row['lname'])) . '</td>
                 <td style="padding: 10px; border: 1px solid #ddd;">' . $meetingDepartmentLabel . '</td>
                <td style="padding: 10px; border: 1px solid #ddd;">' . $meetingParticipantLabel . '</td>
               
                <td style="padding: 10px; border: 1px solid #ddd;">' . ucwords(strtolower($row['remarks'])) . '</td>
            </tr>
        ';
    }

    $emailBody = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Daily Meeting Report</title>
    </head>
    <body style="font-family: Arial, sans-serif; background-color: #f4f4f9; color: #333;">
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="https://pms.shubhampack.in/assets/images/shubhampack.png" alt="Shubham Pack Logo" style="max-width: 150px;">
        </div>
        <h2 style="text-align: center; color: #4872b8;">Daily Meeting Report of Design Department</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <tr style="background-color: #4872b8; color: #ffffff;">
                <th style="padding: 10px; border: 1px solid #ddd;">Sr No</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Meeting Date</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Meeting Time</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Total Time Spent</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Meeting Conducted By</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Department</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Meeting Done With</th>
                
                
                <th style="padding: 10px; border: 1px solid #ddd;">Remarks</th>
            </tr>
            ' . $tableRows . '
        </table>
    </body>
    </html>
    ';

    //echo $emailBody; exit;

    return $emailBody;
}

/**
 * Send the email to the respective team leaders.
 *
 * @param string $emailBody
 */
function sendEmailToTeamLeaders($emailBody) {
    
        $time = date('H:i');
        if($time=='18:00'){
            $this->email->from('taskmanagement@shubhampack.com', 'Shubham Flexible Packaging - Meeting Report of the day');
        $this->email->to('rishabhsharrma@shubhampack.com');
        $this->email->cc('mangleshup@gmail.com');
        // $this->email->to($leader['email']);
        $this->email->subject('Daily Meeting Report - Design Department');
        $this->email->message($emailBody);

        if ($this->email->send()) {
            log_message('info', 'Daily meeting report successfully sent to ');
        } else {
            log_message('error', 'Failed to send daily meeting report to ');
        } 
        }
       
    
}

public function markticketasclosed()
{
    $ticket_id = $this->input->post('ticket_id');
    $remark = $this->input->post('remark');

    $user_id =$this->session->userdata['logged_in']['user_id'];
    $closerinfo = $this->user->getuserinfo($user_id);
    foreach($closerinfo as $loggedinuser);
    $personinfo = ucwords(strtolower($loggedinuser->title." ".$loggedinuser->first_name." ".$loggedinuser->last_name)); 

    $ticket = $this->user->get_ticket_by_id($ticket_id);

    if (!$ticket) {
        echo json_encode(['success' => false, 'message' => 'Invalid ticket ID.']);
        return;
    }

    foreach ($ticket as $ticketinfo);

    $update = $this->user->mark_ticket_closed($ticket_id, $remark);

    $userinfo = $this->user->gethodemail($ticketinfo->user_id);
    $ccemail = "";
    if ($userinfo != '') {
        foreach ($userinfo as $teamleader);
        $ccemail = $teamleader->email;
    }
    //echo $ticketinfo->email; exit;
    if ($update) {
        $this->load->library('email');

        $this->email->from('taskmanagement@shubhampack.com', 'Support Team - Shubham Pack');
        $this->email->to($ticketinfo->email); // Use actual ticket owner's email
        if ($ccemail != '') {
            
            $this->email->cc($ccemail);
        }
        $this->email->bcc('mangleshup@gmail.com');
        $dearuser = ucwords(strtolower($ticketinfo->first_name." ".$ticketinfo->last_name));

        $this->email->subject('✅ Your Support Ticket has been Closed');

        $htmlMessage = '
<div style="font-family: Arial, sans-serif; background-color: #f4f6f9; padding: 30px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 0 8px rgba(0,0,0,0.1); overflow: hidden;">
        <div style="background-color: #ffffff; padding: 20px 30px; text-align: center;">
            <img src="https://shubhampack.in/beta1/assets/images/shubhampack.png" alt="Shubham Pack Logo" style="max-height: 70px; margin-bottom: 10px;">
            <h2 style="color: #2E8B57; font-size: 22px; margin-top: 10px;">Hello ' . htmlspecialchars($dearuser) . ',</h2>
        </div>
        <div style="padding: 20px 30px; color: #333;">
            <p style="font-size: 16px; line-height: 1.6;">
                We would like to inform you that your support ticket has been <strong style="color: #28a745;">successfully marked as closed</strong>. Please find the details below:
            </p>

            <table style="width: 100%; border-collapse: collapse; margin: 20px 0; background-color: #f9f9f9; border-radius: 5px; overflow: hidden;">
                <tr style="background-color: #eeeeee;">
                    <td style="padding: 12px; font-weight: bold;">🎫 Ticket ID:</td>
                    <td style="padding: 12px;">' . htmlspecialchars($ticketinfo->help_ticket_no) . '</td>
                </tr>
                <tr>
                    <td style="padding: 12px; font-weight: bold;">📝 Remark:</td>
                    <td style="padding: 12px;">' . nl2br(htmlspecialchars($remark)) . '</td>
                </tr>
            </table>

            <p style="font-size: 16px; line-height: 1.6;">
                If you have any further queries or require assistance, please feel free to contact us again. We’re always here to help!
            </p>

            <p style="font-size: 16px; margin-top: 30px;">
                Warm regards,<br>
                <strong>'.$personinfo.'</strong><br>
                <span style="color: #555;">Shubham Pack</span>
            </p>
        </div>
        <div style="background-color: #f1f1f1; padding: 15px 30px; text-align: center; font-size: 12px; color: #777;">
            This is an automated message from the internal support system of Shubham Pack.
        </div>
    </div>
</div>';


        $this->email->set_mailtype("html");
        $this->email->message($htmlMessage);

        $this->email->send();

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update ticket status.']);
    }
}



public function notifyhod()
{

    $user_id =$this->session->userdata['logged_in']['user_id'];
    $closerinfo = $this->user->getuserinfo($user_id);
    foreach($closerinfo as $loggedinuser);
    $personinfo = ucwords(strtolower($loggedinuser->title." ".$loggedinuser->first_name." ".$loggedinuser->last_name)); 

    $ticket_id = $this->input->post('ticket_id');

    $ticket = $this->user->get_ticket_by_id($ticket_id);
    if (!$ticket) {
        echo json_encode(['success' => false, 'message' => 'Invalid ticket ID.']);
        return;
    }

    foreach ($ticket as $ticketinfo); // Get the single record
    $ticketcreatedfor = ucwords(strtolower($ticketinfo->title." ".$ticketinfo->first_name." ".$ticketinfo->last_name));
    $ticketcreatedby = ucwords(strtolower($ticketinfo->ptitle." ".$ticketinfo->fname." ".$ticketinfo->lname));
    $created_date = new DateTime($ticketinfo->added_on); // Replace `created_at` with actual DB field
    $current_date = new DateTime();
    $pending_days = $created_date->diff($current_date)->days;



    $hod_info = $this->user->gethodemail($ticketinfo->user_id);
    foreach($hod_info as $hoddetail);
    //echo "<pre>"; print_r($hod_info ); exit;
    if (!$hoddetail || empty($hoddetail->email)) {
        echo json_encode(['success' => false, 'message' => 'HOD email not found.']);
        return;
    }

    $hod_email =  $hoddetail->email;
    $hod_name = ucwords(strtolower($hoddetail->title." ".$hoddetail->first_name." ".$hoddetail->last_name));
   // echo $hod_name; exit;
    // Send email
    $this->load->library('email');

    $this->email->from('taskmanagement@shubhampack.com', 'Support System');
   // $this->email->to('mangleshup@gmail.com');
    $this->email->to($hod_email);
    $this->email->bcc('mangleshup@gmail.com');
    $this->email->subject('Action Required: Pending Support Ticket');

   $message = '
<div style="font-family: Arial, sans-serif; background-color: #f4f6f9; padding: 30px;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05); overflow: hidden;">
        
        <div style="text-align: center; padding: 20px;">
            <img src="https://shubhampack.in/beta1/assets/images/shubhampack.png" alt="Shubham Pack Logo" style="max-height: 70px; margin-bottom: 10px;">
        </div>

        <div style="padding: 0 30px 30px 30px; color: #333;">
            <h2 style="color: #d9534f;">Dear ' . htmlspecialchars($hod_name) . ',</h2>

            <p style="font-size: 16px; line-height: 1.5;">
                A support ticket has been raised and <strong>requires your attention</strong>. Please find the details below:
            </p>

            <table style="width: 100%; border-collapse: collapse; margin: 20px 0; background-color: #f9f9f9; border: 1px solid #ddd;">
                <tr>
                    <td style="padding: 12px; font-weight: bold; background-color: #f0f0f0;">🎫 Ticket ID:</td>
                    <td style="padding: 12px;">' . htmlspecialchars($ticketinfo->help_ticket_no) . '</td>
                </tr>
                <tr>
                    <td style="padding: 12px; font-weight: bold; background-color: #f0f0f0;">👤 Raised For:</td>
                    <td style="padding: 12px;">' . htmlspecialchars($ticketcreatedfor) . '</td>
                </tr>
                  <tr>
                    <td style="padding: 12px; font-weight: bold; background-color: #f0f0f0;">👤 Raised By:</td>
                    <td style="padding: 12px;">' . htmlspecialchars($ticketcreatedby) . '</td>
                </tr>
                <tr>
                    <td style="padding: 12px; font-weight: bold; background-color: #f0f0f0;">📝 Issue:</td>
                    <td style="padding: 12px;">' . nl2br(htmlspecialchars($ticketinfo->remarks)) . '</td>
                </tr>
                <tr>
                <td style="padding: 12px; font-weight: bold; background-color: #f0f0f0;">📅 Pending Since:</td>
                <td style="padding: 12px;">' . $pending_days . ' day(s)</td>
                </tr>

            </table>

            <p style="font-size: 16px;">
                Kindly <strong>log in to your PMS</strong> and take the necessary action as soon as possible. If this attached task has been done. Kindly ask your team member to update remarks.
            </p>

            <p style="font-size: 16px; margin-top: 30px;">
                Regards,<br>
                <strong>' . htmlspecialchars($personinfo) . '</strong>
            </p>
        </div>

        <div style="background-color: #f1f1f1; padding: 15px; text-align: center; font-size: 12px; color: #777;">
            This is an automated message from the internal support system of Shubham Pack.
        </div>
    </div>
</div>';

    $this->email->set_mailtype("html");
    $this->email->message($message);
    $this->email->send();

    echo json_encode(['success' => true]);
}




}
