<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mom extends CI_Controller {
	
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
       
         $this->email->initialize($config);
		$this->email->set_mailtype("html");
		

	}

	private function build_iom_attachment_url($attachment_name)
	{
		$attachment_name = trim((string) $attachment_name);
		if ($attachment_name === '') {
			return '';
		}

		if (preg_match('#^https?://#i', $attachment_name)) {
			return $attachment_name;
		}

		$relative_path = str_replace('\\', '/', ltrim($attachment_name, '/'));

		if (strpos($relative_path, 'image_bank/') === 0) {
			$relative_path = substr($relative_path, strlen('image_bank/'));
		}

		if (strpos($relative_path, 'iomfile/') !== 0) {
			$relative_path = 'iomfile/' . ltrim($relative_path, '/');
		}

		$encoded_segments = array_map('rawurlencode', explode('/', $relative_path));
		return sfdocument . implode('/', $encoded_segments);
	}

	private function build_iom_attachment_link($attachment_name, $label = 'Click to Download')
	{
		$attachment_url = $this->build_iom_attachment_url($attachment_name);
		if ($attachment_url === '') {
			return '';
		}

		return '<a href="' . $attachment_url . '" target="_blank" download>' . $label . '</a>';
	}
	
	public function index(){
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('agenda', 'agenda', 'required|trim');
		$this->form_validation->set_rules('employee_name', 'employee_name', 'required|trim');
		$this->form_validation->set_rules('date', 'date', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		$department_id =$this->session->userdata['logged_in']['department_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		    $this->load->view('mom/index');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   $participants = implode(',',$this->input->post('participant'));
		    $data=
			array('particular'=>$this->input->post('agenda'),
			'added_by'=>$user_id,
			'mom_date'=>date('Y-m-d',strtotime($this->input->post('date'))),
			'mom_time'=>$this->input->post('time'),
			'participants'=>$participants,
			'added_on'=>$added_time);
			$res = $this->db->insert('mom',$data);
			$id = $this->db->insert_id();
			$table = "mom_points";
			$message = "Dear Members, <br> Please find the MOM of todays meeting.<br/><br/>";
			$message.='<table cellpadding="0" cellspacing="0" width="80%" align="left" border="1" style="font-size:14px"><thead>
			<tr>
				<td align="left" colspan="4" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="'.asset_url.'" width="200px;" alt="" />
				<span style="text-align:center; font-size:24px; padding-left:40%">MOM</span>
				</td>
			</tr>
				<tr style="background-color:#666699; color:#fff;">
				<th>SR NO</th>
				<th>PARTICULAR</th>
				<th>DUE DATE</th>
				<th>RESPONSIBLE PERSON</th>
				</tr>
			</thead><tbody>';
			
			if(isset($_REQUEST['description'])){	
					$tags1=count($_REQUEST['description']);
					if($tags1>0)
					{
					$description=$_REQUEST['description'];
					$due_date = $_REQUEST['due_date'];
					$username = $_REQUEST['username'];
					$department = $_REQUEST['department'];
					if(isset($_REQUEST['delegatetask'])){
					$delegatetask=$_REQUEST['delegatetask'];
					}else{
						$delegatetask="";
					}
					$i=1;
					for($x=0;$x<$tags1;$x++){
					if($description[$x]!='')
						{
							
							$q = $this->db->select('first_name, last_name')->from('system_users')->where('user_id',$username[$x])->get();
							foreach($q->result() as $userinformation);
						    if(isset($delegatetask[$x])){
								$delegationstatus = $delegatetask[$x];
							}else{
								$delegationstatus="";
							}
							$data=array('particular'=>$description[$x],
							'mom_id'=>$id,
							'due_date'=>date('Y-m-d',strtotime($due_date[$x])),
							'responsible_person'=>$username[$x],
							'department_id'=>$department[$x],
							'delegated'=>$delegationstatus);
							$this->db->insert($table,$data);
							
							$message.="<tr><td style='text-align:center;'>".$i."</td>
							<td style='text-align:center;'>".$description[$x]."</td>
							<td style='text-align:center;'>".date('d-m-Y',strtotime($due_date[$x]))."</td>
							<td style='text-align:center;'>".$userinformation->first_name." ".$userinformation->last_name."</td></tr>";
							
							if($delegationstatus=='1'){
							$query = $this->db->select('id')->from('delegation_task')->get();
							$res = count($query->result());
							if($res<=0){
							$caseno = "PD-1";
							}else{
							$caseno= "PD-".$res;
							}	
							$data=array('yourname'=>$user_id,
							'department_id'=>$department[$x],
							'delegate_to'=>$username[$x],
							'task'=>$description[$x],
							'image'=>'',
							'delegated_date'=>date('Y-m-d',strtotime($due_date[$x])),
							'targetdate'=>date('Y-m-d',strtotime($due_date[$x])),
							'added_by'=>$user_id,
							'case_no'=>$caseno,
							'added_on'=>$added_time);
							$res = $this->db->insert('delegation_task',$data);	
								
								
							}
						}
					$i++;	
					}
					}
					}
					$message.="</tbody></table>";
					//echo $message; exit;
					
					$SUB = "MINUTE OF MEETING ".date('d-m-Y');
					$emails = array();
					$member = explode(',', $participants);
					$participantperson=array();
					$q = $this->db->select('email')->from('system_users')->where_in('user_id',$member)->get();
					foreach($q->result() as $participantinfo){
					$participantperson[]= $participantinfo->email;
					}
					if(count($participantperson)>0){
					$ccemail = trim(implode(',',$participantperson),',');
					}else{
					  $ccemail="";  
					}
					//echo $ccemail; exit;
					$this->email->set_mailtype("html");
				    $this->email->to($ccemail);
					//$this->email->cc($ccemail);
					$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('taskmanagement@shubhampack.com');
    				$this->email->subject($SUB);
    			    $this->email->message($message);
    				$result11=$this->email->send();
					$messages = "Dear Members, <br> Please find the assigned of todays MOM.<br/><br/>";
					$messages.='<table  cellpadding="0" cellspacing="0" width="80%" align="left" border="1" style="font-size:14px"><thead>
			<tr>
				<td align="left" colspan="4" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://gamavis.com/PMS/assets/images/shubhampack.png" width="200px;" alt="Shubham Pack" />
				<span style="text-align:center; font-size:24px; padding-left:40%">MOM</span>
				</td>
			</tr>
				<tr style="background-color:#666699; color:#fff;">
				<th>SR NO</th>
				<th>PARTICULAR</th>
				<th>DUE DATE</th>
				<th>RESPONSIBLE PERSON</th>
				</tr>
			</thead><tbody>';
					$Q = $this->db->select('a.particular, a.due_date, b.first_name, b.last_name')->from('mom_points a')->join('system_users b','a.responsible_person=b.user_id','left')->where('a.mom_id',$id)->where('a.particular')->get();
					$k=1;
					foreach($Q->result() as $rows){
						$messages.="<tr><td style='text-align:center;'>".$k."</td>
							<td style='text-align:center;'>".$rows->particular."</td>
							<td style='text-align:center;'>".date('d-m-Y',strtotime($rows->due_date))."</td>
							<td style='text-align:center;'>".$rows->first_name." ".$rows->last_name."</td></tr>";
						$k++;
					}
					
					
				//echo $messages; exit;	
					
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'Mom');
			
		}
		
		
				
	}
	
	
	
	
	public function dashboard(){
		$this->load->view('mom/dashboard');
	}
	public function mom_list()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$momdata = array();
		$query = $this->db->select('a.id,a.particular, a.mom_date, a.mom_time, a.participants, a.added_on,b.first_name, b.last_name')->from('mom a')->join('system_users b','a.added_by=b.user_id','left')->order_by('a.mom_date','asc')->order_by('a.mom_time','asc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$member = explode(',', $row->participants);
			date_default_timezone_set("Asia/Kolkata");
			$added_time = date('Y-m-d H:i A',strtotime($row->added_on));
			$participantperson="";
			$q = $this->db->select('first_name, last_name')->from('system_users')->where_in('user_id',$member)->get();
			//echo "<pre>"; print_r($q->result()); exit;
			foreach($q->result() as $participantinfo){
				$participantperson.= $participantinfo->first_name." ".$participantinfo->last_name."<br><br>";
			}
			$view = "<a href='".page_url."Mom/view_mom/".$row->id."'><span class='btn btn-success btn-xs'>View MOM</span></a>";
			
			
			 $send='<button class="btn btn-warning btn-xs waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$row->id.'">Send Email</button>';
			 
			$send.='<div id="con-close-modal'.$row->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
			<form id="loginForm" method="post" action="'.page_url.'Mom/sendemailtouser/'.$row->id.'"  enctype="multipart/form-data">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Select User and Send MOM over Email</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                               
												<div class="col-md-12">
													<div class="form-group">
<label for="field-1" class="control-label">Username</label>
<span id="error_business_loc" style="color:red;"></span>
<select class="form-control" name="username" id="user_name" required>';
$q = $this->db->select('a.responsible_person, b.user_id, b.first_name, b.last_name')->from('mom_points a')->join('system_users b','a.responsible_person=b.user_id','left')->where('a.mom_id',$row->id)->group_by('a.responsible_person')->get();
foreach($q->result() as $momuser){
$send.='<option value="'.$momuser->user_id.'">'.$momuser->first_name." ".$momuser->last_name.'</option>';
}
$send.='</select>

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
			
			$momdata[] = array('sr_no'=>$i,
			'employee_name'=>$row->first_name." ".$row->last_name,
			'date'=>date('d-m-Y',strtotime($row->mom_date)),
			'time'=>date('h:i A',strtotime($row->mom_time)),
			'participant'=>$participantperson,
			'agenda'=>$row->particular,
			'added_time'=>$added_time,
			'sendemail'=>$send,
			'view_mom'=>$view);
			$i++;
		}
		//echo "<pre>"; print_r($scheduler_data); exit;
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($momdata),
	"iTotalDisplayRecords" => count($momdata),
	"aaData"=>$momdata);
	echo json_encode($results);
}
public function view_mom(){
	$this->load->view('mom/view_mom');
}
public function user_dashboard(){
		$this->load->view('mom/user_dashboard');
	}
	
public function user_mom_list()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$momdata = array();
		$query = $this->db->select('a.id,a.particular, a.mom_date, a.mom_time, a.participants, a.added_on,b.first_name, b.last_name')->from('mom a')->join('system_users b','a.added_by=b.user_id','left')->order_by('a.mom_date','asc')->order_by('a.mom_time','asc')->where('a.added_by',$user_id)->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$member = explode(',', $row->participants);
			date_default_timezone_set("Asia/Kolkata");
			$added_time = date('Y-m-d H:i A',strtotime($row->added_on));
			$participantperson="";
			$q = $this->db->select('first_name, last_name')->from('system_users')->where_in('user_id',$member)->get();
			foreach($q->result() as $participantinfo){
				$participantperson.= $participantinfo->first_name." ".$participantinfo->last_name."<br><br>";
			}
			$view = "<a href='".page_url."Mom/view_mom/".$row->id."'><span class='btn btn-success btn-xs'>View MOM</span></a>";
			$momdata[] = array('sr_no'=>$i,
			'employee_name'=>$row->first_name." ".$row->last_name,
			'date'=>date('d-m-Y',strtotime($row->mom_date)),
			'time'=>date('h:i A',strtotime($row->mom_time)),
			'participant'=>$participantperson,
			'agenda'=>$row->particular,
			'added_time'=>$added_time,
			'view_mom'=>$view);
			$i++;
		}
		
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($momdata),
	"iTotalDisplayRecords" => count($momdata),
	"aaData"=>$momdata);
	echo json_encode($results);
}
public function mom_assigned_to_you(){
		$this->load->view('mom/assigned_mom');
	}

public function mom_assigned_to_you_data()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$momdata = array();
		$query = $this->db->select('c.mom_id, a.id,a.particular, a.mom_date, a.mom_time, a.participants, a.added_on,b.first_name, b.last_name')->from('mom_points c')->join('mom a','c.mom_id=a.id','left')->join('system_users b','a.added_by=b.user_id','left')->where('c.responsible_person',$user_id)->order_by('a.mom_date','asc')->order_by('a.mom_time','asc')->group_by('c.mom_id')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			$member = explode(',', $row->participants);
			date_default_timezone_set("Asia/Kolkata");
			$added_time = date('Y-m-d H:i A',strtotime($row->added_on));
			$participantperson="";
			$q = $this->db->select('first_name, last_name')->from('system_users')->where_in('user_id',$member)->get();
			foreach($q->result() as $participantinfo){
				$participantperson.= $participantinfo->first_name." ".$participantinfo->last_name."<br><br>";
			}
			$view = "<a href='".page_url."Mom/view_mom/".$row->id."'><span class='btn btn-success btn-xs'>View MOM</span></a>";
			$k=1;
			$html = "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; width:10%'>SR NO.</th><th style='padding:2px 2px 2px 2px; width:60%'>PARTICULAR</th> <th style='padding:2px 2px 2px 2px; width:10'>DUE DATE</th><th style='width:10%'>Update Status</th><th style='width:10%'>Completion Time</th></tr>";
			$instrumentsss = array();
			$q = $this->db->select('id,particular, due_date,workstatus, update_on')->from('mom_points')->where('mom_id',$row->mom_id)->where('responsible_person',$user_id)->get();
			
			foreach($q->result() as $record){
				
				
				if($record->workstatus=='1'){
					$updatestatus="Done";
					$completiontime = date('Y-m-d H:i A',strtotime($record->update_on));
				}else{
				 $updatestatus='<button class="btn btn-warning btn-xs waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$record->id.'">Update Status</button>';
				 $completiontime="";
				}
			 
			$updatestatus.='<div id="con-close-modal'.$record->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
			<form id="loginForm" method="post" action="'.page_url.'Mom/updatetaskstatus/'.$record->id.'"  enctype="multipart/form-data">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">'.strtoupper($record->particular).'</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                               
											<div class="col-md-12">
											<div class="form-group">
											<label for="field-1" class="control-label">Update Status</label><br/>
											<span id="error_business_loc" style="color:red;"></span>
											<select class="form-control" name="status" id="status" style="width:300px" required><option value="1">Done</option>
											<option value="0">Not Done</option></select>
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
				
				
				
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$k."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->particular)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".date('d-m-Y',strtotime($record->due_date))."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$updatestatus."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$completiontime."</td>";
				$html.="</tr>";
			$k++;
			}
			$html.="</table>";
			$momdata[] = array('sr_no'=>$i,
			'employee_name'=>$row->first_name." ".$row->last_name,
			'date'=>date('d-m-Y',strtotime($row->mom_date)),
			'time'=>date('h:i A',strtotime($row->mom_time)),
			'participant'=>$participantperson,
			'agenda'=>$row->particular,
			'added_time'=>$added_time,
			'particulardata'=>$html,
			'view_mom'=>$view);
			$i++;
		}
		
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($momdata),
	"iTotalDisplayRecords" => count($momdata),
	"aaData"=>$momdata);
	echo json_encode($results);
}

function sendemailtouser(){
	$id = $this->uri->segment(3);
	$q  =$this->db->select('first_name, last_name, email')->from('system_users')->where('user_id',$this->input->post('username'))->get();
	foreach($q->result() as $row);
	$username = $row->first_name." ".$row->last_name; 
	
	$messages = "Dear ".$username.", <br> Please find the MOM points assigned to you.<br/><br/>";
	$q  =$this->db->select('particular')->from('mom')->where('id',$this->uri->segment(3))->get();
	foreach($q->result() as $row1);
	$messages.="Agenda of Meeting: ".$row1->particular."<br/></br>";
					$messages.='<table  cellpadding="0" cellspacing="0" width="80%" align="left" border="1" style="font-size:14px"><thead>
			<tr>
				<td align="left" colspan="4" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://www.easemysale.com/hpcl/assets/images/logo_mitr.png" width="200px;" alt="HPCL Lubricants CFA M/s SUNDER INDUSTRIAL OIL" />
				
				</td>
			</tr>
				<tr style="background-color:#666699; color:#fff;">
				<th>SR NO</th>
				<th>PARTICULAR</th>
				<th>DUE DATE</th>
				<th>RESPONSIBLE PERSON</th>
				</tr>
			</thead><tbody>';
					$Q = $this->db->select('a.particular, a.due_date, b.first_name, b.last_name')->from('mom_points a')->join('system_users b','a.responsible_person=b.user_id','left')->where('a.mom_id',$id)->where('a.responsible_person',$this->input->post('username'))->get();
					//echo "<pre>"; print_r($Q->result()); exit;
					$k=1;
					foreach($Q->result() as $rows){
						$messages.="<tr><td style='text-align:center;'>".$k."</td>
							<td style='text-align:center;'>".$rows->particular."</td>
							<td style='text-align:center;'>".date('d-m-Y',strtotime($rows->due_date))."</td>
							<td style='text-align:center;'>".$rows->first_name." ".$rows->last_name."</td></tr>";
						$k++;
					}
					$SUB = "MINUTE OF MEETING ";
					$this->email->set_mailtype("html");
				    $this->email->to($row->email);
					$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('taskmanagement@shubhampack.com');
    				$this->email->subject($SUB);
    			    $this->email->message($messages);
    				$result11=$this->email->send();
					
					$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Thank you, Email successfully sent to user.</span><br/>');
				redirect(page_url.'Mom/dashboard');
	
	
}

public function updatetaskstatus() {
    $status = $this->input->post('status');
    $id = $this->uri->segment(3);
    date_default_timezone_set("Asia/Kolkata");
    $added_time = date('Y-m-d H:i:s');

    // Update the task
    $data = array(
        'workstatus' => $status,
        'remarks' => $this->input->post('remarks'),
        'update_on' => $added_time
    );
    $this->db->where('id', $id);
    $this->db->update('dfwise_iom_points', $data);

    // Fetch task details including MOM ID, task name
    $this->db->select('dfwise_iom_points.particular, dfwise_iom.id, dfwise_iom.added_by, dfwise_iom.participants, c.df_no, c.df_description');
    $this->db->from('dfwise_iom_points');
    $this->db->join('dfwise_iom', 'dfwise_iom.id = dfwise_iom_points.mom_id','left');
    $this->db->join('df_release c','dfwise_iom_points.df_id=c.id','left');
    $this->db->where('dfwise_iom_points.id', $id);
    $task_info = $this->db->get()->row();

    if ($task_info) {
        $task_name = $task_info->particular;
        $created_by = $task_info->added_by;
        $participants = explode(',', $task_info->participants); // Array of user IDs

        if($task_info->df_no<>''){
        	$dfinformation = $task_info->df_no." ".$task_info->df_description; 
        }else{
        	$dfinformation = '';
        }
        // Include creator in email list
        $all_user_ids = array_unique(array_merge($participants, array($created_by)));

        // Fetch email and name of all participants and creator
        $this->db->select('user_id, title, first_name, last_name, email');
        $this->db->from('system_users');
        $this->db->where_in('user_id', $all_user_ids);
        $users = $this->db->get()->result();
        $useid =$this->session->userdata['logged_in']['user_id'];	
        // Get updater’s name from session
        $updater_name = '';
        $q = $this->db->select('title, first_name, last_name')->from('system_users')->where('user_id',$useid)->get();
        if($q->num_rows()>0){
        	foreach($q->result() as $userinfo);
        	$updater_name = $userinfo->title." ".$userinfo->first_name." ".$userinfo->last_name;
        }

       
        $remark = $this->input->post('remarks');

        // Email subject and message
        // Email subject and styled message with logo
$subject = "MOM ".$task_info->particular." Task Updated: $task_name";
$logo_url = "https://pms.shubhampack.in/assets/images/shubhampack.png";

$message = "
    <div style='font-family: Arial, sans-serif; border:1px solid #ddd; padding:20px; max-width:600px; margin:auto; background:#f9f9f9;'>
        <div style='text-align:center; margin-bottom:20px;'>
            <img src='$logo_url' alt='Shubham Pack' style='max-width:200px;'>
        </div>
        <h2 style='color:#333;'>MOM Task Update Notification</h2>
        <p>Dear Team,</p>
        <p>The task <strong style='color:#4872b8;'>$task_name</strong> has been updated with the following details:</p>
        <table style='width:100%; border-collapse:collapse; margin:15px 0;'>
         <tr>
                <td style='padding:8px; border:1px solid #ddd;'><strong>DF No:</strong></td>
                <td style='padding:8px; border:1px solid #ddd;'>$dfinformation</td>
            </tr>
            <tr>
                <td style='padding:8px; border:1px solid #ddd;'><strong>Remarks:</strong></td>
                <td style='padding:8px; border:1px solid #ddd;'>$remark</td>
            </tr>
            <tr>
                <td style='padding:8px; border:1px solid #ddd;'><strong>Updated By:</strong></td>
                <td style='padding:8px; border:1px solid #ddd;'>$updater_name</td>
            </tr>
            <tr>
                <td style='padding:8px; border:1px solid #ddd;'><strong>Status:</strong></td>
                <td style='padding:8px; border:1px solid #ddd;'>$status</td>
            </tr>
        </table>
        <p style='color:#555;'>Please log in to your MOM dashboard for more details.</p>
        <div style='margin-top:30px; text-align:center; font-size:12px; color:#777;'>
            <p>Regards,<br><strong>Shubham Pack Team</strong></p>
            <hr style='border:none; border-top:1px solid #ddd; margin:20px 0;'>
            <p>This is an automated message from the Task Management System.<br>Do not reply to this email.</p>
        </div>
    </div>
";

// Load email library and send
$this->load->library('email');
foreach ($users as $user) {
    $this->email->from('taskmanagement@shubhampack.com', 'MOM Update - Shubham Pack');
    $this->email->to($user->email);
    $this->email->subject($subject);
    $this->email->message($message);
    $this->email->set_mailtype("html");
    $this->email->send();
}

    }

    $this->session->set_flashdata('message', '<span style="color:red; float-left:20px;" class="alert alert-success">Thank you, Your MOM status has been updated.</span><br/>');
    redirect(page_url.'Mom/dfwise_mom_assigned_to_you');
}


	public function user_list(){
		echo "<option value=''>--Select User--</option>";
	$department = $this->input->post('department');
		$query =$this->db->select('user_id, first_name, last_name,department_id, hide_profile, user_status')->from('system_users')->where('department_id',$department)->where('hide_profile','0')->where('business_location',2)->where('user_status','1')->get();
		foreach($query->result() as $users)
			{
				echo "<option value=".$users->user_id.">".strtoupper($users->first_name)." ".strtoupper($users->last_name)."</option>";
				}
	}
	public function form(){
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('agenda', 'Agenda', 'required|trim');
		$this->form_validation->set_rules('employee_name', 'Employee Name', 'required|trim');
		$this->form_validation->set_rules('company_name', 'Company Name', 'required|trim');
		$this->form_validation->set_rules('company_participants', 'Company Participant', 'required|trim');
		$this->form_validation->set_rules('emails', 'emails', 'required|trim');
		$this->form_validation->set_rules('date', 'date', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		$department_id =$this->session->userdata['logged_in']['department_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		    $this->load->view('mom/external_mom');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   $participants = implode(',',$this->input->post('participant'));
		    $data=
			array('particular'=>$this->input->post('agenda'),
			'added_by'=>$user_id,
			'company_name'=>$this->input->post('company_name'),
			'company_emailid'=>$this->input->post('emails'),
			'participants_from_customer_side'=>$this->input->post('company_participants'),
			'mom_date'=>date('Y-m-d',strtotime($this->input->post('date'))),
			'mom_time'=>$this->input->post('time'),
			'participants'=>$participants,
			'added_on'=>$added_time);
			$res = $this->db->insert('mom_external',$data);
			$id = $this->db->insert_id();
			$table = "mom_external_points";
			$message = "Dear Members, <br> Please find the MOM of todays meeting.<br/><br/>";
			$message.='<table cellpadding="0" cellspacing="0" width="80%" align="left" border="1" style="font-size:14px"><thead>
			<tr>
				<td align="left" colspan="4" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://www.easemysale.com/hpcl/assets/images/logo_mitr.png" width="200px;" alt="HPCL Lubricants CFA M/s SUNDER INDUSTRIAL OIL" />
				<span style="text-align:center; font-size:24px; padding-left:40%">MOM</span>
				</td>
			</tr>
				<tr style="background-color:#666699; color:#fff;">
				<th>SR NO</th>
				<th>PARTICULAR</th>
				<th>DUE DATE</th>
				<th>RESPONSIBLE PERSON</th>
				</tr>
			</thead><tbody>';
			
			if(isset($_REQUEST['description'])){	
					$tags1=count($_REQUEST['description']);
					if($tags1>0)
					{
					$description=$_REQUEST['description'];
					$due_date = $_REQUEST['due_date'];
					$username = $_REQUEST['responsibility'];
					
					$i=1;
					for($x=0;$x<$tags1;$x++){
					if($description[$x]!='')
						{
							
						
							$data=array('particular'=>$description[$x],
							'mom_id'=>$id,
							'due_date'=>date('Y-m-d',strtotime($due_date[$x])),
							'responsibleperson'=>$username[$x]);
							$this->db->insert($table,$data);
							
							$message.="<tr><td style='text-align:center;'>".$i."</td>
							<td style='text-align:center;'>".$description[$x]."</td>
							<td style='text-align:center;'>".date('d-m-Y',strtotime($due_date[$x]))."</td>
							<td style='text-align:center;'>".$username[$x]."</td></tr>";
							
						}
					$i++;	
					}
					}
					}
					$message.="</tbody></table>";
					//echo $message; exit;
					
					$SUB = "MINUTE OF MEETING ".date('d-m-Y');
					$emails = array();
					$member = explode(',', $participants);
					$participantperson=array();
					$q = $this->db->select('email')->from('system_users')->where_in('user_id',$member)->get();
					foreach($q->result() as $participantinfo){
					$participantperson[]= $participantinfo->email;
					}
					if(count($participantperson)>0){
					$ccemail = trim(implode(',',$participantperson),',');
					}else{
					  $ccemail="";  
					}
					$company_emailid = $this->input->post('emails');
					
					$this->email->set_mailtype("html");
				    $this->email->to($ccemail);
					if($company_emailid){
					$this->email->cc($company_emailid );	
					}
					
					$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('taskmanagement@shubhampack.com');
    				$this->email->subject($SUB);
    			    $this->email->message($message);
    				$result11=$this->email->send();
					$messages = "Dear Members, <br> Please find the assigned of todays MOM.<br/><br/>";
					$messages.='<table  cellpadding="0" cellspacing="0" width="80%" align="left" border="1" style="font-size:14px"><thead>
			<tr>
				<td align="left" colspan="4" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://gamavis.com/PMS/assets/images/shubhampack.png" width="200px;" alt="Shubham Pack" />
				
				</td>
			</tr>
				<tr style="background-color:#666699; color:#fff;">
				<th>SR NO</th>
				<th>PARTICULAR</th>
				<th>DUE DATE</th>
				<th>RESPONSIBLE PERSON</th>
				</tr>
			</thead><tbody>';
					$Q = $this->db->select('responsibleperson, particular, due_date')->from('mom_external_points')->where('mom_id',$id)->get();
					$k=1;
					foreach($Q->result() as $rows){
						$messages.="<tr><td style='text-align:center;'>".$k."</td>
							<td style='text-align:center;'>".$rows->particular."</td>
							<td style='text-align:center;'>".date('d-m-Y',strtotime($rows->due_date))."</td>
							<td style='text-align:center;'>".$rows->first_name." ".$rows->last_name."</td></tr>";
						$k++;
					}
					
					
				//echo $messages; exit;	
					
				$this->session->set_flashdata('message','<span class="alert alert-success">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'Mom/form/');
			
		}
		
		
				
	}
	
	public function external_dashboard(){
		$this->load->view('mom/external_dashboard');
	}
	
	public function external_mom_list()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$momdata = array();
		$query = $this->db->select('a.id,a.particular, a.mom_date, a.mom_time, a.participants, a.added_on,b.first_name, b.last_name')->from('mom_external a')->join('system_users b','a.added_by=b.user_id','left')->order_by('a.mom_date','asc')->order_by('a.mom_time','asc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$member = explode(',', $row->participants);
			date_default_timezone_set("Asia/Kolkata");
			$added_time = date('Y-m-d H:i A',strtotime($row->added_on));
			$participantperson="";
			$q = $this->db->select('first_name, last_name')->from('system_users')->where_in('user_id',$member)->get();
			//echo "<pre>"; print_r($q->result()); exit;
			foreach($q->result() as $participantinfo){
				$participantperson.= $participantinfo->first_name." ".$participantinfo->last_name."<br><br>";
			}
			$view = "<a href='".page_url."Mom/view_external_mom/".$row->id."'><span class='btn btn-success btn-xs'>View MOM</span></a>";
			
			
			 $send='<button class="btn btn-warning btn-xs waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$row->id.'">Send Email</button>';
			 
			$send.='<div id="con-close-modal'.$row->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="'.page_url.'Mom/sendemailtouser/'.$row->id.'"  enctype="multipart/form-data">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Select User and Send MOM over Email</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                               
												<div class="col-md-12">
													<div class="form-group">
<label for="field-1" class="control-label">Username</label>
<span id="error_business_loc" style="color:red;"></span>
<select class="form-control" name="username" id="user_name" required>';
$q = $this->db->select('a.responsible_person, b.user_id, b.first_name, b.last_name')->from('mom_points a')->join('system_users b','a.responsible_person=b.user_id','left')->where('a.mom_id',$row->id)->group_by('a.responsible_person')->get();
foreach($q->result() as $momuser){
$send.='<option value="'.$momuser->user_id.'">'.$momuser->first_name." ".$momuser->last_name.'</option>';
}
$send.='</select>

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
			
			$momdata[] = array('sr_no'=>$i,
			'employee_name'=>$row->first_name." ".$row->last_name,
			'date'=>date('d-m-Y',strtotime($row->mom_date)),
			'time'=>date('h:i A',strtotime($row->mom_time)),
			'participant'=>$participantperson,
			'agenda'=>$row->particular,
			'added_time'=>$added_time,
			'sendemail'=>$send,
			'view_mom'=>$view);
			$i++;
		}
		//echo "<pre>"; print_r($scheduler_data); exit;
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($momdata),
	"iTotalDisplayRecords" => count($momdata),
	"aaData"=>$momdata);
	echo json_encode($results);
}
public function view_external_mom(){
	$this->load->view('mom/view_external_mom');
}

public function momdashboard(){
		$this->load->view('dashboard/staff_forms');
	}

public function createiom() {
    $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
    $this->form_validation->set_rules('dftype', 'DF Type', 'required|trim');
    $this->form_validation->set_rules('agenda', 'Agenda', 'required|trim');
    $this->form_validation->set_rules('employee_name', 'Employee Name', 'required|trim');
    $this->form_validation->set_rules('date', 'Date', 'required|trim');
    
    $user_id = $this->session->userdata['logged_in']['user_id'];        
    $department_id = $this->session->userdata['logged_in']['department_id'];        
    
    if ($this->form_validation->run() == FALSE) {
        $this->load->view('mom/create_iom');
    } else {
        date_default_timezone_set("Asia/Kolkata");
        $added_time = date('Y-m-d H:i:s');
        $participants = $this->input->post('participant'); // Participant user IDs
        
        $profilephoto = $_FILES['attachment']['name'];
        if ($profilephoto != '') {
            $image2 = explode('.', $profilephoto);
            $cat_image1 = end($image2);
            $profile_photo = time() . '.' . $cat_image1;
            move_uploaded_file($_FILES["attachment"]["tmp_name"], UPLOADPATH . 'iomfile/' . $profile_photo);
        } else {
            $profile_photo = "";
        }
        
        $data = array(
            'particular' => strtoupper($this->input->post('agenda')),
            'added_by' => $user_id,
            'df_id' => $this->input->post('dfno'),
            'company_name' => strtoupper($this->input->post('companyname')),
            'mom_date' => date('Y-m-d', strtotime($this->input->post('date'))),
            'mom_time' => strtoupper($this->input->post('time')),
            'participants' => implode(',', $participants),
            'iom_attachment' => $profile_photo,
            'added_on' => $added_time
        );

        $this->db->insert('dfwise_iom', $data);
        $id = $this->db->insert_id();

        $q = $this->db->select('df_no, df_description')->from('df_release')->where('id', $this->input->post('dfno'))->get();
        $dfno = $q->num_rows() > 0 ? strtoupper($q->row()->df_no . " " . $q->row()->df_description) : "";

        // Fetch participant names and emails
        $participant_names = [];
        $participant_emails = [];
        $this->db->select('first_name, last_name, email');
        $this->db->from('system_users');
        $this->db->where_in('user_id', $participants);
        $participant_query = $this->db->get();
        foreach ($participant_query->result() as $participant) {
            $participant_names[] = strtoupper($participant->first_name . " " . $participant->last_name);
            $participant_emails[] = $participant->email;
        }
        $participant_names_string = implode(', ', $participant_names);
        $ccemail = implode(',', $participant_emails);

        // Build the email table content dynamically
        $action_points_rows = "";
        if (isset($_REQUEST['description'])) {    
            $tags1 = count($_REQUEST['description']);
            if ($tags1 > 0) {
                $description = $_REQUEST['description'];
                $due_date = $_REQUEST['due_date'];
                $username = $_REQUEST['username'];
                $department = $_REQUEST['department'];
                $DFnos = $_REQUEST['particulardfno'];
                $i = 1;
                for ($x = 0; $x < $tags1; $x++) {
                    if ($description[$x] != '') {
                        $user_info = $this->db->select('first_name, last_name')->from('system_users')->where('user_id', $username[$x])->get()->row();
                        $df_detail_info = $this->db->select('df_no')->from('df_release')->where('id', $DFnos[$x])->get()->row();
                        $selecteddfno = $df_detail_info ? strtoupper($df_detail_info->df_no) : $dfno;

                        $data = array(
                            'particular' => strtoupper($description[$x]),
                            'mom_id' => $id,
                            'due_date' => date('Y-m-d', strtotime($due_date[$x])),
                            'responsible_person' => $username[$x],
                            'df_id' => $DFnos[$x],
                            'department_id' => $department[$x]
                        );
                        $this->db->insert('dfwise_iom_points', $data);

                        $action_points_rows .= "
                            <tr style='border: 1px solid #ddd;'>
                                <td style='text-align:center; border: 1px solid #ddd; padding: 10px;'>" . strtoupper($i) . "</td>
                                <td style='text-align:center; border: 1px solid #ddd; padding: 10px;'>" . strtoupper($description[$x]) . "</td>
                                <td style='text-align:center; border: 1px solid #ddd; padding: 10px;'>" . date('d-m-Y', strtotime($due_date[$x])) . "</td>
                                <td style='text-align:center; border: 1px solid #ddd; padding: 10px;'>" . strtoupper($user_info->first_name . " " . $user_info->last_name) . "</td>
                                <td style='text-align:center; border: 1px solid #ddd; padding: 10px;'>" . $selecteddfno . "</td>
                            </tr>";
                        $i++;    
                    }
                }
            }
        }

        $agendaofmeeting = strtoupper($this->input->post('agenda'));
        // Email HTML body
        $email_body = "
        <table cellpadding='0' cellspacing='0' width='100%' style='font-family: Arial, sans-serif; background-color: #f9f9f9; margin: 0; padding: 0;'>
          <tr>
            <td>
              <table align='center' cellpadding='0' cellspacing='0' width='80%' style='background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); margin: 20px auto; padding: 20px;'>
                <tr>
                  <td style='background-color: #4872b8; padding: 20px; text-align: center; color: #ffffff;'>
                    <img src='https://pms.shubhampack.in/assets/images/shubhampack.png' alt='Shubham Pack' width='150' style='display: block; margin: 0 auto;'>
                    <h1 style='font-size: 24px; margin: 10px 0;'>MINUTES OF MEETING (MOM)</h1>
                    <p style='margin: 0; font-size: 16px;'>PLEASE FIND THE DETAILS OF TODAY'S MEETING BELOW</p>
                  </td>
                </tr>
                <tr>
                  <td style='padding: 20px;'>
                    <h2 style='font-size: 18px; color: #333; margin-bottom: 10px;'>MEETING DETAILS</h2>
                    <table cellpadding='0' cellspacing='0' width='100%' style='border-collapse: collapse; margin-bottom: 20px;'>
                      <tr style='border: 1px solid #ddd;'>
                        <td style='font-weight: bold; padding: 10px; border: 1px solid #ddd;'>MEETING AGENDA:</td>
                        <td style='padding: 10px; border: 1px solid #ddd;'>{$agendaofmeeting}</td>
                      </tr>
                      <tr style='border: 1px solid #ddd;'>
                        <td style='font-weight: bold; padding: 10px; border: 1px solid #ddd;'>MEETING DATE:</td>
                        <td style='padding: 10px; border: 1px solid #ddd;'>" . date('d-m-Y', strtotime($this->input->post('date'))) . "</td>
                      </tr>
                      <tr style='border: 1px solid #ddd;'>
                        <td style='font-weight: bold; padding: 10px; border: 1px solid #ddd;'>PARTICIPANTS:</td>
                        <td style='padding: 10px; border: 1px solid #ddd;'>{$participant_names_string}</td>
                      </tr>
                    </table>
                    <h2 style='font-size: 18px; color: #333; margin-bottom: 10px;'>ACTION POINTS</h2>
                    <table cellpadding='0' cellspacing='0' width='100%' style='border-collapse: collapse; font-size: 14px;'>
                      <thead>
                        <tr style='background-color: #4872b8; color: #ffffff;'>
                          <th style='padding: 10px; border: 1px solid #ddd;'>SR NO</th>
                          <th style='padding: 10px; border: 1px solid #ddd;'>PARTICULAR</th>
                          <th style='padding: 10px; border: 1px solid #ddd;'>DUE DATE</th>
                          <th style='padding: 10px; border: 1px solid #ddd;'>RESPONSIBLE PERSON</th>
                          <th style='padding: 10px; border: 1px solid #ddd;'>DF NO</th>
                        </tr>
                      </thead>
                      <tbody>
                        {$action_points_rows}
                      </tbody>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td style='background-color: #f0f0f0; padding: 20px; text-align: center;'>
                    <p style='margin: 0; font-size: 14px; color: #666;'>FOR ANY QUERIES, PLEASE CONTACT US AT <a href='mailto:taskmanagement@shubhampack.com' style='color: #4872b8; text-decoration: none;'>TASKMANAGEMENT@SHUBHAMPACK.COM</a>.</p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>";

        $SUB = $dfno . " MOM " . date('d-m-Y');

        // Send Email
        $this->email->set_mailtype("html");
        $this->email->to($ccemail);
        if($this->input->post('customerparticipant')<>''){
        	$this->email->cc($this->input->post('customerparticipant'));
        }
        $this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
        $this->email->from('taskmanagement@shubhampack.com');
        $this->email->subject($SUB);
        $this->email->message($email_body);

        if ($this->email->send()) {
            $this->session->set_flashdata('message', '<span style="color:green;" class="alert alert-success">THANK YOU, YOUR RECORD SUCCESSFULLY ADDED, AND EMAIL SENT SUCCESSFULLY.</span>');
        } else {
            $this->session->set_flashdata('message', '<span style="color:red;" class="alert alert-danger">EMAIL SENDING FAILED. PLEASE CHECK EMAIL CONFIGURATION OR RECIPIENTS.</span>');
        }

        redirect(page_url . 'Mom/createiom');
    }
}





	public function iom_master_dashboard(){
		$this->load->view('mom/iom_master_dashboard');
	}

	public function iom_master_list()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$momdata = array();
		$query = $this->db->select('a.id,a.particular, a.iom_attachment, a.mom_date, a.mom_time, a.participants, a.added_on,b.title, b.first_name, b.last_name, c.df_no, c.df_description')->from('dfwise_iom a')->join('system_users b','a.added_by=b.user_id','left')->join('df_release c','a.df_id=c.id','left')->order_by('a.mom_date','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$member = explode(',', $row->participants);
			date_default_timezone_set("Asia/Kolkata");
			$date = date('d-m-Y',strtotime($row->added_on));
			$time = date('h:i A',strtotime($row->added_on));
			$added_time = $date."<br> ".$time;
			$participantperson="";
			$q = $this->db->select('title, first_name, last_name')->from('system_users')->where_in('user_id',$member)->get();
			

			$participant_names = array();
			foreach ($q->result() as $participantinfo) {
			$participant_names[] = ucwords(strtolower($participantinfo->title." ".$participantinfo->first_name . " " . $participantinfo->last_name));
			}
			$participantperson = implode(", ", $participant_names);

			$attachment = $this->build_iom_attachment_link($row->iom_attachment);
			$view = "<a href='".page_url."Mom/view_iom/".$row->id."'><span class='btn btn-success btn-xs'>View MOM</span></a>";
			$momdata[] = array('sr_no'=>$i,
			'employee_name'=>ucwords(strtolower($row->title." ".$row->first_name." ".$row->last_name)),
			'date'=>date('d-m-Y',strtotime($row->mom_date)),
			'time'=>date('h:i A',strtotime($row->mom_time)),
			'df_no'=>$row->df_no." ".$row->df_description,
			'participant'=>$participantperson,
			'attachment'=>$attachment,
			'agenda'=>$row->particular,
			'added_time'=>$added_time,
			'view_mom'=>$view);
			$i++;
		}
		
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($momdata),
	"iTotalDisplayRecords" => count($momdata),
	"aaData"=>$momdata);
	echo json_encode($results);
}

public function your_iom_dashboard(){
		$this->load->view('mom/iom_dashboard');
	}

	public function your_iom_list()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$momdata = array();
		$query = $this->db->select('a.id,a.particular, a.iom_attachment, a.mom_date, a.mom_time, a.participants, a.added_on,b.first_name, b.last_name, c.df_no, c.df_description')->from('dfwise_iom a')->join('system_users b','a.added_by=b.user_id','left')->join('df_release c','a.df_id=c.id','left')->where('a.added_by',$user_id)->order_by('a.id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$member = explode(',', $row->participants);
			date_default_timezone_set("Asia/Kolkata");
			$added_time = date('Y-m-d H:i A',strtotime($row->added_on));
			$participantperson="";
			$q = $this->db->select('first_name, last_name')->from('system_users')->where_in('user_id',$member)->get();
			foreach($q->result() as $participantinfo){
				$participantperson.= $participantinfo->first_name." ".$participantinfo->last_name."<br><br>";
			}
			$attachment = $this->build_iom_attachment_link($row->iom_attachment);
			$view = "<a href='".page_url."Mom/view_iom/".$row->id."'><span class='btn btn-success btn-xs'>View MOM</span></a>";
			$momdata[] = array('sr_no'=>$i,
			'employee_name'=>$row->first_name." ".$row->last_name,
			'date'=>date('d-m-Y',strtotime($row->mom_date)),
			'time'=>date('h:i A',strtotime($row->mom_time)),
			'df_no'=>$row->df_no." ".$row->df_description,
			'participant'=>$participantperson,
			'attachment'=>$attachment,
			'agenda'=>$row->particular,
			'added_time'=>$added_time,
			'view_mom'=>$view);
			$i++;
		}
		
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($momdata),
	"iTotalDisplayRecords" => count($momdata),
	"aaData"=>$momdata);
	echo json_encode($results);
}
public function view_iom(){
	$this->load->view('mom/view_iom');
}

public function df_iom_assigned_to_you(){
		$this->load->view('mom/assigned_iom');
	}

public function df_iom_assigned_to_you_data()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$momdata = array();
		$query = $this->db->select('c.mom_id, a.iom_attachment, a.id,a.particular, a.mom_date, a.mom_time, a.participants, a.added_on,b.first_name, b.last_name, d.df_no, d.df_description')->from('dfwise_iom_points c')->join('dfwise_iom a','c.mom_id=a.id','left')->join('system_users b','a.added_by=b.user_id','left')->join('df_release d','a.df_id=d.id','left')->where('c.responsible_person',$user_id)->order_by('a.mom_date','asc')->order_by('a.mom_time','asc')->group_by('c.mom_id')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			$dfno = $row->df_no." ".$row->df_description;
			$attachment = $this->build_iom_attachment_link($row->iom_attachment);

			$member = explode(',', $row->participants);
			date_default_timezone_set("Asia/Kolkata");
			$date = date('d-m-Y',strtotime($row->added_on));
			$time = date('h:i A',strtotime($row->added_on));
			$added_time = $date."<br>".$time;
			$participantperson="";
			$q = $this->db->select('first_name, last_name')->from('system_users')->where_in('user_id',$member)->get();
			foreach($q->result() as $participantinfo){
				$participantperson.= $participantinfo->first_name." ".$participantinfo->last_name."<br><br>";
			}
			$view = "<a href='".page_url."Mom/view_mom/".$row->id."'><span class='btn btn-success btn-xs'>View MOM</span></a>";
			$k=1;
			$html = "<table border='1' style='width:500px;'><tr style='background-color:#0495cb; color:#fff;'><th style='padding:2px 2px 2px 2px; width:10%; color:#fff;'>SR NO.</th><th style='padding:2px 2px 2px 2px; width:60%; color:#fff;'>PARTICULAR</th> <th style='padding:2px 2px 2px 2px; width:10'>DUE DATE</th><th style='width:10%; color:#fff;'>Update Status</th><th style='width:10%; color:#fff;'>Completion Time</th></tr>";
			$instrumentsss = array();
			$q = $this->db->select('id,particular, due_date,workstatus, update_on')->from('dfwise_iom_points')->where('mom_id',$row->mom_id)->where('responsible_person',$user_id)->get();
			
			foreach($q->result() as $record){
				
				
				if($record->workstatus=='1'){
					$updatestatus="Done";
					$completiontime = date('Y-m-d H:i A',strtotime($record->update_on));
				}else{
				 $updatestatus='<button class="btn btn-warning btn-xs waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$record->id.'">Update Status</button>';
				 $completiontime="";
				}
			 
			$updatestatus.='<div id="con-close-modal'.$record->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
			<form id="loginForm" method="post" action="'.page_url.'Mom/updateimostatus/'.$record->id.'"  enctype="multipart/form-data">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">'.strtoupper($record->particular).'</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                               
											<div class="col-md-12">
											<div class="form-group">
											<label for="field-1" class="control-label">Update Status</label><br/>
											<span id="error_business_loc" style="color:red;"></span>
											<select class="form-control" name="status" id="status" style="width:300px" required><option value="1">Done</option>
											<option value="0">Not Done</option></select>
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
				
				
				
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$k."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->particular)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".date('d-m-Y',strtotime($record->due_date))."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$updatestatus."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$completiontime."</td>";
				$html.="</tr>";
			$k++;
			}
			$html.="</table>";
			$momdata[] = array('sr_no'=>$i,
			'employee_name'=>$row->first_name." ".$row->last_name,
			'date'=>date('d-m-Y',strtotime($row->mom_date)),
			'dfno'=>$dfno,
			'attachment'=>$attachment,
			'time'=>date('h:i A',strtotime($row->mom_time)),
			'participant'=>$participantperson,
			'agenda'=>$row->particular,
			'added_time'=>$added_time,
			'particulardata'=>$html,
			'view_mom'=>$view);
			$i++;
		}
		
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($momdata),
	"iTotalDisplayRecords" => count($momdata),
	"aaData"=>$momdata);
	echo json_encode($results);
}

public function updateimostatus(){
	$status = $this->input->post('status');
	$id = $this->uri->segment(3);
	date_default_timezone_set("Asia/Kolkata");
   $added_time = date('Y-m-d H:i:s');
	$data = array('workstatus'=>$status,
	'update_on'=>$added_time);
	$this->db->where('id',$id);
	$this->db->update('dfwise_iom_points',$data);
	$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Thank you, Your MOM status has been updated.</span><br/>');
	redirect(page_url.'Mom/df_iom_assigned_to_you');
	
}

function getcompanyinfo(){
	$option='';
	$dfno = $this->input->post('dfno');
	$q = $this->db->select('id, company_name')->from('poreceived')->where('df_id',$dfno)->get();
	if($q->num_rows()>0){
		foreach($q->result() as $row);
		$option.='<option value="'.$row->id.'">'.$row->company_name.'</option>';
	}
	echo $option; exit;
	

}

public function dfwise_mom_assigned_to_you(){
		$this->load->view('mom/dfwise_mom_assigned');
	}

public function dfwise_mom_assigned_to_you_data()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$momdata = array();
		$query = $this->db->select('a.mom_id, b.id, b.particular,b.participants, b.mom_date, b.mom_time, b.added_on,c.title, c.first_name, c.last_name')->from('dfwise_iom_points a')->join('dfwise_iom b','a.mom_id=b.id','left')->join('system_users c','b.added_by=c.user_id','left')->where('a.responsible_person',$user_id)->order_by('b.mom_date','asc')->group_by('a.mom_id')->get();
		$res = $query->result();

		//echo "<pre>"; print_r($res); exit;
		$i=1;
		foreach($res as $row)
		{
			
			$member = explode(',', $row->participants);
			date_default_timezone_set("Asia/Kolkata");
			$added_time = date('Y-m-d H:i A',strtotime($row->added_on));
			$participantperson="";

			$q = $this->db->select('title, first_name, last_name')->from('system_users')->where_in('user_id',$member)->get();
			foreach($q->result() as $participantinfo){
				$participantperson.= ucwords(strtolower($participantinfo->title." ".$participantinfo->first_name." ".$participantinfo->last_name."<br><br>"));
			}
			$view = "<a href='".page_url."Mom/view_mom/".$row->id."'><span class='btn btn-success btn-xs'>View MOM</span></a>";
			$k=1;
			$html = "<table border='1' style='width:600px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; width:10%'>SR NO.</th><th style='padding:2px 2px 2px 2px; width:50%'>PARTICULAR</th> <th style='padding:2px 2px 2px 2px; width:20%'>DUE DATE</th><th style='width:10%'>Update Status</th><th style='width:10%'>Completion Time</th></tr>";
			$instrumentsss = array();
			$q = $this->db->select('id,particular, due_date,workstatus, update_on, remarks')->from('dfwise_iom_points')->where('mom_id',$row->mom_id)->where('responsible_person',$user_id)->get();
			
			foreach($q->result() as $record){
				
				
				if($record->workstatus=='1'){
					$updatestatus="Done";
					$completiondate = date('d-m-Y',strtotime($record->update_on));
					$completiontime = date('h:i A',strtotime($record->update_on)); 
				}else{
				 $updatestatus='<button class="btn btn-warning btn-xs waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$record->id.'">Update Status</button>';
				 $completiondate = "";
				 $completiontime="";
				}
			 
			$updatestatus.='<div id="con-close-modal'.$record->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
			<form id="loginForm" method="post" action="'.page_url.'Mom/updatetaskstatus/'.$record->id.'"  enctype="multipart/form-data">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">'.strtoupper($record->particular).'</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                               
											<div class="col-md-12">
											<div class="form-group">
											<label for="field-1" class="control-label">Update Status</label><br/>
											<span id="error_business_loc" style="color:red;"></span>
											<select class="form-control" name="status" id="status" style="width:500px" required><option value="1">Done</option>
											<option value="0">Not Done</option></select>
											</div>
											</div>
                                          


                                            <div class="col-md-12">
                                            <div class="form-group">
                                            	<label>Remarks</label>
                                            	<textarea class="form-control" name="remarks" id="remarks" style="width:500px" required></textarea>
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
				
				
				
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$k."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->particular)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".date('d-m-Y',strtotime($record->due_date))."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$updatestatus."<br><br> ".$record->remarks."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$completiondate." <br>".$completiontime."</td>";
				$html.="</tr>";
			$k++;
			}
			$html.="</table>";
			$momdata[] = array('sr_no'=>$i,
			'employee_name'=>ucwords(strtolower($row->title." ".$row->first_name." ".$row->last_name)),
			'date'=>date('d-m-Y',strtotime($row->mom_date)),
			'time'=>date('h:i A',strtotime($row->mom_time)),
			'participant'=>$participantperson,
			'agenda'=>$row->particular,
			'added_time'=>$added_time,
			'particulardata'=>$html,
			'view_mom'=>$view);
			$i++;
		}
		
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($momdata),
	"iTotalDisplayRecords" => count($momdata),
	"aaData"=>$momdata);
	echo json_encode($results);
}
}
