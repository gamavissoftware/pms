<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DF_revision extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		ini_set('memory_limit', '-1');
		$this->load->model('User_model','user');
		$this->load->model('Task_model','task');
		$this->load->model('Ticket_model','Ticket_model');
		

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
		if (!$this->session->userdata('logged_in'))
        { 
            $this->session->set_flashdata('message','Session Logged Out. Login to continue', 'refresh');
            redirect(page_url);
        }

	}


public function create_revision()
{
    $user_id = $_SESSION['logged_in']['user_id'];

    if(in_array($user_id,[61,161,139]))
    {
        $this->db->select('id,df_no,df_description,df_sr_no');
    }
    else
    {
        $this->db->select('id,df_no,df_description,df_sr_no');
        $this->db->where('added_by',$user_id);
    }

    $data['df_list'] = $this->db
        ->order_by('df_sr_no','DESC')
        ->get('df_release')
        ->result_array();

    $this->load->view('df_change_control/create_revision',$data);
}


public function ajax_get_df_details()
{
    $df_id = $this->input->post('df_id');

    $df = $this->db
    ->select('dr.*,su.first_name as added_by_name,su.last_name')
    ->from('df_release dr')
    ->join('system_users su','su.user_id=dr.added_by','left')
    ->where('dr.id',$df_id)
    ->get()
    ->row_array();

    $tasks = $this->db
    ->select('
        s.*,
        tm.task_name,
        d.department,
        su.first_name as assigned_user_name,
        su.last_name
    ')
    ->from('task_department_wise_scheduling s')
    ->join('task_management tm','tm.task_id=s.taskid')
    ->join('departments d','d.department_id=s.department_id','left')
    ->join('system_users su','su.user_id=s.assigned_user','left')
    ->where('s.df_id',$df_id)
    ->order_by('s.start_date','ASC')
    ->get()
    ->result_array();

    $task_html = '';

    $i = 1;

    $completed_count = 0;
    $pending_count   = 0;

    foreach($tasks as $row)
    {
        if($row['task_status'] == 1)
        {
            $completed_count++;

            $row_class = 'task-row task-row-completed';

            $status = '
            <span class="status-completed">
                Completed
            </span>';
        }
        else
        {
            $pending_count++;

            $row_class = 'task-row task-row-pending';

            $status = '
            <span class="status-pending">
                Pending
            </span>';
        }

        $task_html .= '
        <tr class="'.$row_class.'">

            <td>'.$i.'</td>

            <td>
                <strong>'.$row['task_name'].'</strong>
            </td>

            <td>'.$row['department'].'</td>

            <td>
                '.$row['assigned_user_name'].' '.$row['last_name'].'
            </td>

            <td>
                '.date('d-m-Y',strtotime($row['start_date'])).'
            </td>

            <td>
                '.date('d-m-Y',strtotime($row['end_date'])).'
            </td>

            <td>
                '.$status.'
            </td>

        </tr>';

        $i++;
    }

    echo json_encode([
        'status'          => 1,
        'df'              => $df,
        'task_count'      => count($tasks),
        'completed_count' => $completed_count,
        'pending_count'   => $pending_count,
        'task_html'       => $task_html
    ]);
}


public function create_revision_iom($df_id=0)
{
    $df = $this->db
    ->where('id',$df_id)
    ->get('df_release')
    ->row_array();

    if(empty($df))
    {
        show_404();
    }

    $data['df'] = $df;

    $data['department_options'] = $this->db
    ->order_by('department','ASC')
    ->get('departments')
    ->result_array();

    $this->load->view(
        'df_change_control/create_revision_iom',
        $data
    );
}



public function save_df_revision()
{
    $user_id = $_SESSION['logged_in']['user_id'];

    $this->form_validation->set_error_delimiters(
        '<div style="color:red;">',
        '</div>'
    );

    $this->form_validation->set_rules(
        'df_id',
        'DF',
        'required|trim|integer'
    );

    $this->form_validation->set_rules(
        'request_type',
        'Request Type',
        'required|trim'
    );

    $this->form_validation->set_rules(
        'change_category',
        'Change Category',
        'required|trim'
    );

    $this->form_validation->set_rules(
        'priority',
        'Priority',
        'required|trim'
    );

    $this->form_validation->set_rules(
        'source_of_change',
        'Source Of Change',
        'required|trim'
    );

    $this->form_validation->set_rules(
        'title',
        'Title',
        'required|trim'
    );

    $this->form_validation->set_rules(
        'change_summary',
        'Change Summary',
        'required|trim'
    );

    $df_id = (int)$this->input->post('df_id');

    if ($this->form_validation->run() == FALSE)
    {
        $this->create_revision($df_id);
        return;
    }

    $df = $this->db
    ->where('id',$df_id)
    ->get('df_release')
    ->row_array();

    if(empty($df))
    {
        $this->session->set_flashdata(
            'message',
            '<div class="alert alert-danger">Invalid DF Selected.</div>'
        );

        redirect(
            page_url.'DF_revision/create_revision/'.$df_id
        );
    }

    try
    {
        $this->db->trans_begin();

        $attachment = $this->upload_change_attachment(
            'attachment'
        );

        $now = date('Y-m-d H:i:s');

        $revision_count = $this->db
        ->where('df_id',$df_id)
        ->where('change_category','REVISION')
        ->count_all_results('df_revision_control');

        $revision_no =
            $df['df_no'].'_V'.($revision_count + 1);

        $change_data = array(

            'change_no'                     => '',
            'df_id'                         => $df_id,
            'request_type'                  => 'IOM',
            'change_category'               => 'REVISION',
            'priority'                      => strtoupper(trim($this->input->post('priority'))),
            'source_of_change'              => strtoupper(trim($this->input->post('source_of_change'))),
            'reference_no'                  => trim($this->input->post('reference_no')),
            'revision_no'                   => $revision_no,
            'title'                         => trim($this->input->post('title')),
            'change_summary'                => trim($this->input->post('change_summary')),
            'impact_note'                   => trim($this->input->post('impact_note')),
            'requested_from_department_id'  => (int)$_SESSION['logged_in']['department_id'],
            'attachment'                    => $attachment,
            'status'                        => 'OPEN',
            'created_by'                    => $user_id,
            'created_on'                    => $now,
            'performa_change_required' =>
											(int)$this->input->post(
											'performa_change_required'
											)

        );

        $this->db->insert(
            'df_revision_control',
            $change_data
        );

        $revision_id = $this->db->insert_id();

        if(!$revision_id)
        {
            throw new Exception(
                'Unable to create revision.'
            );
        }

        $change_no =
            'IOM-REV-'.
            date('ymd').
            '-'.
            str_pad(
                $revision_id,
                5,
                '0',
                STR_PAD_LEFT
            );

        $this->db
        ->where('id',$revision_id)
        ->update(
            'df_revision_control',
            array(
                'change_no'=>$change_no
            )
        );


			/*
			-----------------------------------
			ARCHIVE DF + RESET TASKS
			-----------------------------------
			*/

			$this->archive_df_for_revision(
			$revision_id,
			$df_id,
			$revision_no
			);

			if($this->db->trans_status() === FALSE)
			{
			throw new Exception(
			'Database transaction failed.'
			);
			}

        $this->db->trans_commit();

        $this->send_df_revision_notification($revision_id);

        $this->session->set_flashdata(
            'message',
            '<div class="alert alert-success">
                DF Revision Request Created Successfully.
                <br>
                Reference No : <strong>'.$change_no.'</strong>
            </div>'
        );

        redirect(
            page_url.
            'DF_revision/revision_dashboard/'
        );
    }

    catch(Exception $e)
    {
        $this->db->trans_rollback();

        log_message(
            'error',
            'DF Revision Error : '.$e->getMessage()
        );

        $this->session->set_flashdata(
            'message',
            '<div class="alert alert-danger">
                '.$e->getMessage().'
            </div>'
        );

        redirect(
            page_url.
            'DF_revision/create_revision/'.
            $df_id
        );
    }


}




public function archive_df_for_revision(
    $revision_id,
    $df_id,
    $revision_no
)
{
    $user_id = $_SESSION['logged_in']['user_id'];
    $now     = date('Y-m-d H:i:s');

    /*
    -----------------------------------
    ARCHIVE DF RELEASE
    -----------------------------------
    */

    $df = $this->db
    ->where('id', $df_id)
    ->get('df_release')
    ->row_array();

    if (empty($df))
    {
        throw new Exception(
            'DF not found for revision.'
        );
    }

    $df_history = $df;

    unset($df_history['id']);

    $df_history['original_df_id']       = $df_id;
    $df_history['revision_control_id']  = $revision_id;
    $df_history['revision_no']          = $revision_no;
    $df_history['archived_on']          = $now;
    $df_history['archived_by']          = $user_id;

    $this->db->insert(
        'df_release_revision_history',
        $df_history
    );

    if (!$this->db->affected_rows())
    {
        throw new Exception(
            'Unable to archive DF.'
        );
    }

    /*
    -----------------------------------
    ARCHIVE TASKS
    -----------------------------------
    */

	$po_id = (int)$this->db
	->select('po_id')
	->where('df_id',$df_id)
	->where('po_id >',0)
	->order_by('id','DESC')
	->limit(1)
	->get('task_department_wise_scheduling')
	->row('po_id');

	$po_id = $po_id ? $po_id : 0;


    $tasks = $this->db
    ->where('df_id', $df_id)
    ->get('task_department_wise_scheduling')
    ->result_array();

    foreach ($tasks as $task)
    {
        $task_history = $task;

        $task_history['original_task_id']
            = $task['id'];

        unset($task_history['id']);

        $task_history['revision_control_id']
            = $revision_id;

        $task_history['revision_no']
            = $revision_no;

        $task_history['archived_on']
            = $now;

        $task_history['archived_by']
            = $user_id;

        $this->db->insert(
            'task_department_wise_scheduling_revision_history',
            $task_history
        );

        if (!$this->db->affected_rows())
        {
            throw new Exception(
                'Unable to archive task history.'
            );
        }
    }

    /*
    -----------------------------------
    DELETE CURRENT TASKS
    ENABLE AFTER TESTING
    -----------------------------------
    */

    
    $this->db
    ->where('df_id', $df_id)
    ->delete('task_department_wise_scheduling');
    

    /*
    -----------------------------------
    DELETE CURRENT DF
    ENABLE AFTER TESTING
    -----------------------------------
    */

    
    $this->db
    ->where('id', $df_id)
    ->delete('df_release');
    

    /*
    -----------------------------------
    TASK 1
    PO RECEIVED
    COMPLETED
    -----------------------------------
    */

    /*
-----------------------------------
TASK 1 TAT FROM MASTER
-----------------------------------
*/

$today = date('Y-m-d');

$task1_master = $this->db
->where('task_id',1)
->get('task_management')
->row_array();

$task1_tat_days = 1;

if(
    !empty($task1_master)
    &&
    !empty($task1_master['days'])
)
{
    $task1_tat_days = (int)$task1_master['days'];
}

$task1_start_date = $today;

$task1_end_date = date(
    'Y-m-d',
    strtotime(
        '+' . ($task1_tat_days - 1) . ' day',
        strtotime($task1_start_date)
    )
);

$task1_holiday_data = $this->SKIP_holidays(
    $task1_start_date,
    $task1_end_date
);

$task1_end_date      = $task1_holiday_data['end_date'];
$task1_holiday_count = $task1_holiday_data['holiday_count'];


    $task1 = array(

        'df_id'             => 0,
        'taskid'            => 1,
        'department_id'     => 9,
        'start_date'        => $task1_start_date,
        'end_date'          => $task1_end_date,
        'holidayscount'     => 0,
        'po_id'=>$po_id,
        'added_on'          => $now,
        'added_by'          => $user_id,
        'task_status'       => 1,
        'remarks'           => 'Auto completed during DF Revision',
        'task_completed_on' => $now,
        'task_completed_by' => $user_id,
        'userid'            => $user_id,
        'assigned_user'     => $user_id,
        'assigned_by'       => $user_id,
        'assigned_on'       => $now

    );

    
    $this->db->insert(
        'task_department_wise_scheduling',
        $task1
    );
    

    $performa_change_required = (int)$this->input->post(
    'performa_change_required'
);


    /*
    -----------------------------------
    TASK 3
    PI CREATION
    OPEN
    START = NEXT DAY AFTER TASK 1
    -----------------------------------
    */

    $task_master = $this->db
    ->where('task_id', 3)
    ->get('task_management')
    ->row_array();

    $tat_days = 1;

    if (
        !empty($task_master)
        &&
        !empty($task_master['days'])
    )
    {
        $tat_days = (int)$task_master['days'];
    }

    /*
    Start date = Next day after Task 1 end
    */

    $start_date = date(
        'Y-m-d',
        strtotime(
            '+1 day',
            strtotime($task1_end_date)
        )
    );

    /*
    Calculate end date including holidays
    */

    $end_date = date(
        'Y-m-d',
        strtotime(
            '+' . ($tat_days - 1) . ' day',
            strtotime($start_date)
        )
    );

   $holiday_data = $this->SKIP_holidays(
    $start_date,
    $end_date
);

$end_date      = $holiday_data['end_date'];
$holiday_count = $holiday_data['holiday_count'];

    /*
    Calculate holiday count
    */

  
      if($performa_change_required == 1)
{

    $task3 = array(

        'df_id'             =>0,
        'taskid'            => 3,
        'department_id'     => 9,
        'po_id'=>$po_id,
        'start_date'        => $start_date,
        'end_date'          => $end_date,
        'holidayscount'     => $holiday_count,
        'added_on'          => $now,
        'added_by'          => $user_id,
        'task_status'       => 0,
        'remarks'           => 'Auto generated after DF Revision',
        'userid'            => $user_id,
        'assigned_user'     => $user_id,
        'assigned_by'       => $user_id,
        'assigned_on'       => $now

    );

    
    $this->db->insert(
        'task_department_wise_scheduling',
        $task3
    );
    
 }else
{
    /*
    -----------------------------------
    TASK 3 AUTO COMPLETE
    -----------------------------------
    */

    $task3 = array(

        'df_id'             => 0,
        'taskid'            => 3,
        'department_id'     => 9,
        'po_id'             => $po_id,
        'start_date'        => $start_date,
        'end_date'          => $end_date,
        'holidayscount'     => $holiday_count,
        'added_on'          => $now,
        'added_by'          => $user_id,
        'task_status'       => 1,
        'remarks'           => 'Auto completed. Performa change not required.',
        'task_completed_on' => $now,
        'task_completed_by' => $user_id,
        'userid'            => $user_id,
        'assigned_user'     => $user_id,
        'assigned_by'       => $user_id,
        'assigned_on'       => $now

    );

    $this->db->insert(
        'task_department_wise_scheduling',
        $task3
    );

    /*
    -----------------------------------
    TASK 114 OPEN
    -----------------------------------
    */

    $task114_master = $this->db
    ->where('task_id',114)
    ->get('task_management')
    ->row_array();

    $task114_days = 1;

    if(
        !empty($task114_master)
        &&
        !empty($task114_master['days'])
    )
    {
        $task114_days = (int)$task114_master['days'];
    }

    $task114_start_date = date(
        'Y-m-d',
        strtotime(
            '+1 day',
            strtotime($end_date)
        )
    );

    $task114_end_date = date(
        'Y-m-d',
        strtotime(
            '+' . ($task114_days - 1) . ' day',
            strtotime($task114_start_date)
        )
    );

    $task114_holiday = $this->SKIP_holidays(
        $task114_start_date,
        $task114_end_date
    );

    $task114_end_date =
        $task114_holiday['end_date'];

    $task114_holiday_count =
        $task114_holiday['holiday_count'];

    $task114 = array(

        'df_id'             => 0,
        'taskid'            => 114,
        'department_id'     => 9,
        'po_id'             => $po_id,
        'start_date'        => $task114_start_date,
        'end_date'          => $task114_end_date,
        'holidayscount'     => $task114_holiday_count,
        'added_on'          => $now,
        'added_by'          => $user_id,
        'task_status'       => 0,
        'remarks'           => 'Auto generated after DF Revision',
        'userid'            => $user_id,
        'assigned_user'     => $user_id,
        'assigned_by'       => $user_id,
        'assigned_on'       => $now

    );

    $this->db->insert(
        'task_department_wise_scheduling',
        $task114
    );
}

    return true;
}


private function upload_change_attachment($field_name)
    {
        if (empty($_FILES[$field_name]['name'])) {
            return '';
        }

        $folder = UPLOADPATH . 'df_change_control/';
        if (!is_dir($folder)) {
            @mkdir($folder, 0775, true);
        }

        $original_name = $_FILES[$field_name]['name'];
        $extension = pathinfo($original_name, PATHINFO_EXTENSION);
        $filename = 'df-change-' . time() . '-' . rand(1000, 9999);
        if ($extension !== '') {
            $filename .= '.' . strtolower($extension);
        }

        if (move_uploaded_file($_FILES[$field_name]['tmp_name'], $folder . $filename)) {
            return $filename;
        }

        return '';
    }


    public function send_df_revision_notification($revision_id)
{
   $revision = $this->db
->select('
    r.*,
    d.df_no,
    CONCAT(
        su.first_name,
        " ",
        su.last_name
    ) as creator_name
')
->from('df_revision_control r')
->join('df_release d','d.id=r.df_id','left')
->join('system_users su','su.user_id=r.created_by','left')
->where('r.id',$revision_id)
->get()
->row_array();

    if(empty($revision))
    {
        return false;
    }

    $departments = $this->db
    ->where('business_loc_id',2)
    ->where('status',1)
    ->order_by('department','ASC')
    ->get('departments')
    ->result_array();

    foreach($departments as $department)
    {
        $team = $this->db
        ->where('department_id',$department['department_id'])
        ->where('business_loc_id',2)
        ->where('status',1)
        ->get('prestogroup_teams')
        ->row_array();

        if(empty($team))
        {
            continue;
        }

        $user = $this->db
        ->select('
            user_id,
            first_name,
            last_name,
            email
        ')
        ->where('user_id',$team['team_leader'])
        ->where('user_status',1)
        ->get('system_users')
        ->row_array();

        if(empty($user))
        {
            continue;
        }

        if(empty($user['email']))
        {
            continue;
        }

        $full_name = trim(
            $user['first_name'].' '.$user['last_name']
        );

		$email_body = $this->build_df_revision_email(
		$revision,
		$full_name
		);

		$this->send_email_from_system(
		'taskmanagement@shubhampack.com',
		$user['email'],
		'New DF Revision Raised - '.$revision['revision_no'],
		$email_body
		);
    }

    return true;
}


public function send_email_from_system(
    $from_email,
    $to_email,
    $subject,
    $message
)
{
   
  // $to_email="sdsrbh5@gmail.com";
 
    $this->email->from(
        $from_email,
        'DF Revision Notification'
    );

    $this->email->to($to_email);
    $this->email->subject($subject);
    $this->email->message($message);

    return $this->email->send();
}


private function build_df_revision_email($revision,$recipient_name='')
{
    $view_link =
        page_url.'DF_revision/view/'.$revision['id'];

    $attachment_link = '';

    if(!empty($revision['attachment']))
    {
        $attachment_link = '
        <tr>
            <td style="padding:8px 10px;border:1px solid #d9d9d9;font-weight:bold;">
                Attachment
            </td>
            <td style="padding:8px 10px;border:1px solid #d9d9d9;">
                <a href="'.uploads_url.$revision['attachment'].'" target="_blank">
                    View Attachment
                </a>
            </td>
        </tr>';
    }

    return '
    <table cellpadding="0"
           cellspacing="0"
           width="100%"
           style="font-family:Arial, Helvetica, sans-serif;background:#f4f7fb;padding:20px 0;">

        <tr>

            <td>

                <table align="center"
                       cellpadding="0"
                       cellspacing="0"
                       width="760"
                       style="max-width:760px;background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #dde3ec;">

                    <tr>

                        <td style="background:linear-gradient(135deg,#214f8a 0%,#0f172a 100%);
                                   color:#ffffff;
                                   padding:24px;">

                            <img src="'.assets_url.'images/shubhampack.png"
                                 width="170"
                                 alt="Shubham Pack"
                                 style="display:block;margin-bottom:16px;">

                            <div style="font-size:24px;font-weight:bold;">
                                DF Revision Control
                            </div>

                            <div style="font-size:14px;margin-top:6px;">
                                New DF Revision Request Raised
                            </div>

                        </td>

                    </tr>

                    <tr>

                        <td style="padding:24px;">

                            <p style="margin-top:0;">
                                Dear '.$recipient_name.',
                            </p>

                            <p>
                                A new DF Revision request has been created and requires your attention.
                            </p>

                            <table cellpadding="0"
                                   cellspacing="0"
                                   width="100%"
                                   style="border-collapse:collapse;font-size:14px;">

                                <tr>
                                    <td style="padding:8px 10px;border:1px solid #d9d9d9;font-weight:bold;">
                                        Reference No
                                    </td>
                                    <td style="padding:8px 10px;border:1px solid #d9d9d9;">
                                        '.htmlspecialchars($revision['change_no']).'
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:8px 10px;border:1px solid #d9d9d9;font-weight:bold;">
                                        DF No
                                    </td>
                                    <td style="padding:8px 10px;border:1px solid #d9d9d9;">
                                        '.htmlspecialchars($revision['df_no']).'
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:8px 10px;border:1px solid #d9d9d9;font-weight:bold;">
                                        Revision No
                                    </td>
                                    <td style="padding:8px 10px;border:1px solid #d9d9d9;">
                                        '.htmlspecialchars($revision['revision_no']).'
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:8px 10px;border:1px solid #d9d9d9;font-weight:bold;">
                                        Request Type
                                    </td>
                                    <td style="padding:8px 10px;border:1px solid #d9d9d9;">
                                        '.htmlspecialchars($revision['request_type']).'
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:8px 10px;border:1px solid #d9d9d9;font-weight:bold;">
                                        Category
                                    </td>
                                    <td style="padding:8px 10px;border:1px solid #d9d9d9;">
                                        '.htmlspecialchars($revision['change_category']).'
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:8px 10px;border:1px solid #d9d9d9;font-weight:bold;">
                                        Priority
                                    </td>
                                    <td style="padding:8px 10px;border:1px solid #d9d9d9;">
                                        '.htmlspecialchars($revision['priority']).'
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:8px 10px;border:1px solid #d9d9d9;font-weight:bold;">
                                        Source Of Change
                                    </td>
                                    <td style="padding:8px 10px;border:1px solid #d9d9d9;">
                                        '.htmlspecialchars($revision['source_of_change']).'
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:8px 10px;border:1px solid #d9d9d9;font-weight:bold;">
                                        Title
                                    </td>
                                    <td style="padding:8px 10px;border:1px solid #d9d9d9;">
                                        '.htmlspecialchars($revision['title']).'
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:8px 10px;border:1px solid #d9d9d9;font-weight:bold;">
                                        Requested By
                                    </td>
                                    <td style="padding:8px 10px;border:1px solid #d9d9d9;">
                                        '.htmlspecialchars($revision['creator_name']).'
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:8px 10px;border:1px solid #d9d9d9;font-weight:bold;">
                                        Summary
                                    </td>
                                    <td style="padding:8px 10px;border:1px solid #d9d9d9;">
                                        '.nl2br(htmlspecialchars($revision['change_summary'])).'
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:8px 10px;border:1px solid #d9d9d9;font-weight:bold;">
                                        Impact Note
                                    </td>
                                    <td style="padding:8px 10px;border:1px solid #d9d9d9;">
                                        '.nl2br(htmlspecialchars($revision['impact_note'])).'
                                    </td>
                                </tr>

                            

                            </table>

                           

                        </td>

                    </tr>

                </table>

            </td>

        </tr>

    </table>';
}


public function SKIP_holidays($start_date, $end_date)
{
    $start = new DateTime($start_date);
    $end   = new DateTime($end_date);

    while(true)
    {
        $holiday_count = $this->db
        ->where('holiday_date >=', $start->format('Y-m-d'))
        ->where('holiday_date <=', $end->format('Y-m-d'))
        ->count_all_results('prestogroup_holidays');

        $new_end = clone $start;

        $total_days =
            $start->diff($end)->days + 1;

        $new_end->modify(
            '+' .
            (($total_days - 1) + $holiday_count)
            . ' day'
        );

        $recheck_count = $this->db
        ->where('holiday_date >=', $start->format('Y-m-d'))
        ->where('holiday_date <=', $new_end->format('Y-m-d'))
        ->count_all_results('prestogroup_holidays');

        if($recheck_count == $holiday_count)
        {
            return array(
                'end_date'       => $new_end->format('Y-m-d'),
                'holiday_count'  => $holiday_count
            );
        }

        $end = $new_end;
    }
}


public function revision_dashboard()
{
    $data = array();

    $data['total_revision'] = $this->db
    ->count_all_results('df_revision_control');

    $this->load->view(
        'df_change_control/revision_dashboard',
        $data
    );
}


public function ajax_revision_list()
{
    $rows = $this->db
    ->select("
        r.*,
        d.df_no,
        d.df_sr_no,
        su.first_name,
        su.last_name
    ")
    ->from('df_revision_control r')
    ->join(
        'df_release_revision_history d',
        'd.revision_control_id=r.id',
        'left'
    )
    ->join(
        'system_users su',
        'su.user_id=r.created_by',
        'left'
    )
    ->group_by('r.id')
    ->order_by('r.id','DESC')
    ->get()
    ->result_array();

    $data = array();

    $i = 1;

    foreach($rows as $row)
    {
        $task_count = $this->db
        ->where(
            'revision_control_id',
            $row['id']
        )
        ->count_all_results(
            'task_department_wise_scheduling_revision_history'
        );

        $status = $row['status']=='OPEN'
        ?
        '<span class="label label-warning">OPEN</span>'
        :
        '<span class="label label-success">CLOSED</span>';

        $action='
        <a href="'.page_url.'DF_revision/view/'.$row['id'].'"
           class="btn btn-xs btn-primary">
            View
        </a>';

        $data[] = array(

            $i,

            $row['change_no'],

            $row['revision_no'],

            'DF-'.$row['df_sr_no'].'<br>'.$row['df_no'],

            $row['title'],

            $row['priority'],

            $task_count,

            $row['first_name'].' '.$row['last_name'],

            date(
                'd-m-Y H:i',
                strtotime($row['created_on'])
            ),

            $status

          //  $action

        );

        $i++;
    }

    echo json_encode(array(
        'data'=>$data
    ));
}

}