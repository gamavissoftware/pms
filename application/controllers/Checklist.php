<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Checklist extends CI_Controller {

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
	$this->load->library('session'); 
	$this->load->helper('url'); 
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
 /*if($user_id=='66' || $user_id=='67' || $user_id=='1'){

			

		}else{

$ip = $_SERVER["REMOTE_ADDR"];

		 $query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();

            if($query->num_rows()=='0'){

            $this->session->set_flashdata('message','<span class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</span><br/>');

            redirect(page_url.'Dashboard');    

            }

		}*/

	}



	

public function turnaroundtime()



	{



		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');

		$this->form_validation->set_rules('turnaroundtime', 'turnaroundtime', 'required|trim');

		$this->form_validation->set_rules('startdate', 'startdate', 'required|trim');

		$this->form_validation->set_rules('time_interval', 'time_interval', 'required|trim');

		$this->form_validation->set_rules('frequency', 'frequency', 'required|trim');

		$user_id =$this->session->userdata['logged_in']['user_id'];		

		if ($this->form_validation->run() == FALSE)



		{



			$this->load->view('checklist/turnaroundtime');



		}



		else



		{



		date_default_timezone_set("Asia/Kolkata");



		$date =  date('Y-m-d H:i:s'); 



		$table = "compliance_tat";		



		$varification_field = "turnaroundtime";



		$varify_data = $this->input->post('turnaroundtime');



		$res = $this->master->varification($table,$varification_field,$varify_data);



		if($res)



		{



			$this->session->set_flashdata('message', '<div class="alert alert-danger" style="color:#000">Sorry, this record already exits.</div>');



			redirect(page_url.'Checklist/turnaroundtime');



		}else{



			$data = array('turnaroundtime'=>strtoupper($this->input->post('turnaroundtime')),

			'date'=>date('Y-m-d', strtotime($this->input->post('startdate'))),

			'time_interval'=>strtoupper($this->input->post('time_interval')),

			'frequency'=>strtoupper($this->input->post('frequency')),

			'status'=>$this->input->post('status'),

			'added_on'=>$date,

			'added_by'=>$user_id);



		$result  = $this->master->insert_record($table,$data);	



		if($result)



		{



			$this->session->set_flashdata('message','<div class="alert alert-success" style="color:#000">Thank you, record successfully added.</div>');



			redirect(page_url.'Checklist/turnaroundtime');



			



		}else



		{



			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');



			redirect(page_url.'Checklist/turnaroundtime');



			



		}



		}





	}



		



	}



	

	

	public function turnaroundtime_list()

	{

		$invoice_data = array();

		$this->db->distinct();

		$query = $this->db->select('*')->from('compliance_tat')->get();

		$res = $query->result();

		$i=1;

		foreach($res as $row)

		{

			date_default_timezone_set("Asia/Kolkata");

			$addeddate = date('d-m-Y', strtotime($row->added_on));

			$time = date('H:i:s', strtotime($row->added_on));

			$addedtime = "<br>". date('g:i A', strtotime($time)); 

			$edit = "<a href='".page_url."Checklist/edit_turnaroundtime/".$row->id."'><i class='fa fa-pencil'></i></a>";

			if($row->status=='1'){

			$status = "<a href='".page_url."Checklist/update_turnaroundtime_status/".$row->id."/".$row->status."'><span class='btn btn-xs btn-success'>Active</span></a>";

			}else{

			$status = "<a href='".page_url."Checklist/update_turnaroundtime_status/".$row->id."/".$row->status."'><span class='btn btn-xs btn-danger'>Not Active</span></a>";

			}

			

			$timeinterval = $row->time_interval;

			if($timeinterval=='1'){

				$interval = "Per Day";

			}elseif($timeinterval=='2'){

				$interval = "Week";

			}elseif($timeinterval=='3'){

				$interval = "Month";

			}elseif($timeinterval=='4'){

				$interval = "Year";

			}else{

				$interval="";

			}

			$invoice_data[] = array('sr_no'=>$i,

			'turnaroundtime'=>$row->turnaroundtime,

			'date'=>date('d-m-Y', strtotime($row->date)),

			'time_interval'=>$interval,

			'frequency'=>$row->frequency,

			'edit'=>$edit,

			'status'=>$status ,

			'sortorder'=>$row->sortbynumber,

			'added_on'=>$addeddate.$addedtime);

			$i++;

		}

	$results = array(

	"sEcho" => 1,

	"iTotalRecords" => count($invoice_data),

	"iTotalDisplayRecords" => count($invoice_data),

	"aaData"=>$invoice_data);

	echo json_encode($results);

}



	public function update_turnaroundtime_status()



	{



		/*************Dynamic information****************/



		$identifier =  $this->uri->segment(3);



		$sval =  $this->uri->segment(4);



		$field_name = "id";



		$table = "compliance_tat";



		if($sval=='1')



			{



				$status = 0;



				}else



				{



					$status = 1;



					}



			$data = array('status'=>$status);



			$res = $this->master->update_records($table,$data,$identifier,$field_name);



			$this->session->set_flashdata('message', '<div class="alert alert-success" style="color:#000">Status successfully updated.</div>');



			redirect(page_url.'Checklist/turnaroundtime');



		}



		



	public function edit_turnaroundtime()



	{



		$this->load->view('checklist/edit_turnaroundtime');



	}



	



	public function update_turnaroundtime()



	{



		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');

		$this->form_validation->set_rules('turnaroundtime', 'turnaroundtime', 'required|trim');

		$this->form_validation->set_rules('startdate', 'startdate', 'required|trim');

		$this->form_validation->set_rules('time_interval', 'time_interval', 'required|trim');

		$this->form_validation->set_rules('frequency', 'frequency', 'required|trim');

		$user_id =$this->session->userdata['logged_in']['user_id'];	

		

		if ($this->form_validation->run() == FALSE)



		{



			$this->load->view('checklist/edit_turnaroundtime');



		}



		else



		{



		date_default_timezone_set("Asia/Kolkata");



		$date =  date('Y-m-d H:i:s'); 

		$table = "compliance_tat";	

		$identifier = $this->uri->segment(3);

		$field_name = "id";		

		$data = array('turnaroundtime'=>strtoupper($this->input->post('turnaroundtime')),

			'date'=>date('Y-m-d', strtotime($this->input->post('startdate'))),

			'time_interval'=>strtoupper($this->input->post('time_interval')),

			'frequency'=>strtoupper($this->input->post('frequency')),

			'sortbynumber'=>strtoupper($this->input->post('sortbynumber')),

			'status'=>$this->input->post('status'),

			'added_on'=>$date,

			'added_by'=>$user_id);

		$result  = $this->master->update_records($table,$data,$identifier,$field_name);		



		if($result)



		{

			$this->session->set_flashdata('message','<div class="alert alert-success" style="color:#000">Thank you, record successfully added.</div>');

			redirect(page_url.'Checklist/turnaroundtime');

		}else



		{

			$this->session->set_flashdata('message','<div class="alert alert-info" style="color:#000">Sorry,technical error accure.</div><br/>');

			redirect(page_url.'Checklist/turnaroundtime');	

		}

		}

		}

		

		

	public function create_checklist()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('business_loc', 'business_loc', 'required|trim');
		$this->form_validation->set_rules('user_id', 'user_id', 'required|trim');
		$this->form_validation->set_rules('task', 'Task', 'required|trim');
		$this->form_validation->set_rules('turnaroundtime', 'turnaroundtime', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		$this->load->view('checklist/create_checklist');
		}
else
{
date_default_timezone_set("Asia/Kolkata");
$date =  date('Y-m-d H:i:s'); 
$table = "compliance_task_report";		
$data = array(

			'company_id'=>$this->input->post('business_loc'),
			'user_id'=>$this->input->post('user_id'),
			'task'=>strtoupper($this->input->post('task')),
			'video_link'=>$this->input->post('video_link'),
			'evidence_required'=>$this->input->post('evidence'),
			'tat_id'=>$this->input->post('turnaroundtime'),
			'complition_time'=>$this->input->post('duetime'),
			'status'=>'1',
			'added_on'=>$date,
			'added_by'=>$user_id);



		$result  = $this->db->insert($table,$data);	

		$last_id = $this->db->insert_id();
		$frequency = $this->input->post('turnaroundtime');
		if($frequency=='12'){
			
			$duetime = $this->input->post('duetime');
			$endtime = "17";
			$starttime = date('H',strtotime($duetime));
			$elapsed = $endtime-$starttime;
			$a=  date('h',$elapsed);
			$secondtime = round($elapsed/2);
			$timeformat = $secondtime*60;
			$secondtime1 = date('h:i:s',$timeformat);
			$timestamp = strtotime($duetime) + $timeformat*60;
			$seconddefinedtime = date('H:i:s', $timestamp);
			
			$data = array('task_id'=>$last_id,
			'frequency'=>$frequency,
			'due_date'=>date('Y-m-d',strtotime($this->input->post('duedate'))),
			'first_time'=>$duetime,
			'second_time'=>$seconddefinedtime,
			'third_time'=>'',
			'added_on'=>date('Y-m-d h:i:s'));
			$this->db->insert('checklist_frequency_management',$data);
		}
		
		if($frequency=='13'){
			
			$duetime = $this->input->post('duetime');
			$endtime = "17";
			$starttime = date('H',strtotime($duetime));
			$elapsed = $endtime-$starttime;
			
			$a=  date('h',$elapsed);
			$secondtime = $elapsed/3;
			$timeformat = $secondtime*60;
			$timeformat1 = $timeformat*2;
			$secondtime1 = date('h:i:s',$timeformat);
			$timestamp = strtotime($duetime) + $timeformat*60;
			$seconddefinedtime = date('H:i:s', $timestamp);
			$timestamp1 = strtotime($seconddefinedtime) + $timeformat*60;
			$thirddefinedtime = date('H:i:s', $timestamp1);
			
			
			$data = array('task_id'=>$last_id,
			'frequency'=>$frequency,
			'due_date'=>date('Y-m-d',strtotime($this->input->post('duedate'))),
			'first_time'=>$duetime,
			'second_time'=>$seconddefinedtime,
			'third_time'=>$thirddefinedtime,
			'added_on'=>date('Y-m-d h:i:s'));
			$this->db->insert('checklist_frequency_management',$data);
		}
		
		
		
		$data1 = array('task_id'=>$last_id,'dateforemail'=>date('Y-m-d',strtotime($this->input->post('duedate'))));
		$this->db->insert('compliance_set_date',$data1);
		if($result)
		{
		$this->session->set_flashdata('message','<div class="alert alert-success" style="color:#000">Thank you, record successfully added.</div>');
		redirect(page_url.'Checklist/create_checklist');
		}else
		{
		$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
		redirect(page_url.'Checklist/create_checklist');
		}

}

}



	

	

	public function checklist_task_list()

	{
		
		$invoice_data = array();
		$this->db->distinct();
		$user_id = $this->uri->segment(3);
		$this->db->select('a.*, b.user_id, b.first_name, b.last_name, b.title, c.id, c.turnaroundtime, d.department')->from('compliance_task_report a')->join('compliance_tat c','a.tat_id=c.id','left')->join('system_users b','a.user_id=b.user_id','left')->join('departments d','b.department_id=d.department_id','left')->where('b.user_status','1')->order_by('b.first_name','asc');

		if($user_id){

			$this->db->where('a.user_id',$user_id);

		}

		$this->db->where('a.status','1');

		$query = $this->db->get();	

		$res = $query->result();

		$i=1;

		foreach($res as $row)

		{

			date_default_timezone_set("Asia/Kolkata");

			$addeddate = date('d-m-Y', strtotime($row->added_on));

			$time = date('H:i:s', strtotime($row->added_on));

			$addedtime = "&nbsp;". date('g:i A', strtotime($time)); 

			$history = "<a href='".page_url."Checklist/remark_history/".$row->task_id."'><span class='btn btn-danger btn-xs'>View History</span></a>";

			$edit = "<a href='".page_url."Checklist/edit_checklist/".$row->task_id."'><i class='fa fa-pencil'></i></a>";

			if($row->status=='1'){

			$status = "<a href='".page_url."Master/Compliance/update_compliance_task_status/".$row->task_id."/".$row->status."'><span class='btn btn-xs btn-success'>Active</span></a>";

			}else{

			$status = "<a href='".page_url."Master/Compliance/update_compliance_task_status/".$row->task_id."/".$row->status."'><span class='btn btn-xs btn-danger'>Not Active</span></a>";

			}

			$todaydate = date('Y-m-d');
			
			$query = $this->db->select('*')->from('compliance_set_date')->where('task_id',$row->task_id)->get();

			$res = $query->result();

			if($res){

				foreach($res as $emaildata);

				$nextduedate = date('d-M-Y',strtotime($emaildata->dateforemail));

				if($emaildata->dateforemail==$todaydate || $emaildata->dateforemail<$todaydate){
				$update_report = "<input type='radio' name='pick_items".$row->task_id."' class='dny' id='pick_items".$i."' value='0' onChange='display_qtybox(".$i.",".$row->task_id.")'>&nbsp;Not Done";

				$update_report1 = "<input type='radio' name='pick_items".$row->task_id."' class='dny' id='pick_items1".$i."' value='1' onChange='display_qtybox(".$i.",".$row->task_id.",".$row->evidence_required.")'>&nbsp;Done";
				
				$hidden2 = $row->evidence_required;
				
				if($row->evidence_required=='1'){
				$update_report2 = "<input type='file' class='form-control' name='evidence".$row->task_id."' class='dny' id='evidence".$i."' value=''>
				<input type='hidden' name='taskid[]' value='".$row->task_id."'>
				<input type='hidden' name='evidence_required[]' value='".$hidden2."'>
				&nbsp;Evidence";
			}else{
				$update_report2 = "<input type='file' class='form-control' name='evidence".$row->task_id."' class='dny' id='evidence".$i."' value=''>
				<input type='hidden' name='taskid[]' value='".$row->task_id."'>
				<input type='hidden' name='evidence_required[]' value='".$hidden2."'>
				&nbsp;Evidence";
			}
				

				$hidden="<div id='makenewhidden".$i."'></div>";

				$html = "<div class='row'><div class='col-md-12'><div class='col-md-6'>".$update_report."</div><div class='col-md-6'>".$update_report1."</div><div class='col-md-12'>".$update_report2."</div></div></div>".$hidden;

				

				}else{

					$html ="<div class='row'><div class='col-md-12'>Reopens on <strong>".date('d-M-Y',strtotime($emaildata->dateforemail))."</strong></div></div>";

					$updateremarks="Reopens on<br> <strong>".date('d-M-Y',strtotime($emaildata->dateforemail))."</strong>";

				}

			}else{

				$nextduedate="";

			}

			

			

			$query = $this->db->select('a.id, a.task_id, a.status,a.remarkdate, b.task_id, b.tat_id, a.momfile')->from('compliance_task_progress_remarks a')->join('compliance_task_report b','a.task_id=b.task_id','left')->where('a.task_id',$row->task_id)->where('b.tat_id','6')->where('a.remarkdate',date('Y-m-d'))->limit('1')->order_by('a.id','desc')->get();

			$result = $query->result();

			if($result){

			foreach($query->result() as $progress_remarks);

			if($progress_remarks->status=='1'){

			    $current_status = "<span style='color:green; font-weight:bold;'>Done</span>";

			}elseif($progress_remarks->status=='0'){

			    $current_status = "<span style='color:red; font-weight:bold;'>Not Done</span>";

			}else{

			    $current_status = "";

			}

			

			}else{

			    $current_status = "";

			    

			}

			
			$whatsapp= '<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						
						<select class="form-control" name="setwhatsapp" id="setwhatsapp'.$row->task_id.'" onchange="setwhatsappreminder('.$row->task_id.')">
							<option value="">Select Optionsss</option>
							<option value="1">Yes</option>
							<option value="0">No</option>
						</select>
					</div>
				</div>
			</div>';
			
			if($row->whatsapp_notification=='1'){
			$whatsapp.='<strong style="color:red;">Given</strong>';	
			}else{
			$whatsapp.='No';	
			}

if($row->tat_id=='12'){
	$q = $this->db->select('first_time, second_time, third_time, first_time_status, second_time_status, third_time_status')->from('checklist_frequency_management')->where('frequency',$row->tat_id)->where('task_id',$row->task_id)->get();
	foreach($q->result() as $rowss);
	if($rowss->first_time_status=='0' && $rowss->second_time_status=='0'){
		$complitiontime = date('h:i A',strtotime($rowss->first_time));
	}else if($rowss->first_time_status=='1' && $rowss->second_time_status=='0'){
		$complitiontime = date('h:i A',strtotime($rowss->second_time));
	}else{
		$complitiontime = date('h:i A',strtotime($rowss->first_time));
	}
}else if($row->tat_id=='13'){
	$q = $this->db->select('first_time, second_time, third_time, first_time_status, second_time_status, third_time_status')->from('checklist_frequency_management')->where('frequency',$row->tat_id)->where('task_id',$row->task_id)->get();
	if($q->num_rows()>0){
	foreach($q->result() as $rowss);

	if($rowss->first_time_status=='0' && $rowss->second_time_status=='0' &&  $rowss->third_time_status=='0'){
		$complitiontime = date('h:i A',strtotime($rowss->first_time));
	}else if($rowss->first_time_status=='1' && $rowss->second_time_status=='0' &&  $rowss->third_time_status=='0'){
		$complitiontime = date('h:i A',strtotime($rowss->second_time));
	}else if($rowss->first_time_status=='1' && $rowss->second_time_status=='1' &&  $rowss->third_time_status=='0'){
		$complitiontime = date('h:i A',strtotime($rowss->third_time));
	}else{
		$complitiontime = date('h:i A',strtotime($rowss->first_time));
	}
}
}else{

$complitiontime = date('h:i A',strtotime($row->complition_time));
}
			
			if($row->evidence_required=='1'){
				$evidence_required= "Yes";
			}else{
				$evidence_required = "No";
			}

			$invoice_data[] = array('sr_no'=>$i,

			'assigned_to'=>$row->title." ".$row->first_name." ".$row->last_name,

			'task'=>"<a href='".$row->video_link."'>".$row->task."</a>",

			'turnaroundtime'=>$row->turnaroundtime,
			'department'=>$row->department,

			'nextduedate'=>$nextduedate,
			'duetime'=>$complitiontime,
			'evidence_required'=>$evidence_required,

			'edit'=>$edit,

			'updateremarks'=>$html,

			'status'=>$status,
			'whatsapp'=>$whatsapp,

			'added_on'=>$addeddate.$addedtime);

			$i++;

		}

	$results = array(

	"sEcho" => 1,

	"iTotalRecords" => count($invoice_data),

	"iTotalDisplayRecords" => count($invoice_data),

	"aaData"=>$invoice_data);

	echo json_encode($results);

}



	public function update_compliance_task_status()



	{



		/*************Dynamic information****************/



		$identifier =  $this->uri->segment(3);



		$sval =  $this->uri->segment(4);



		$field_name = "task_id";



		$table = "compliance_task_report";



		if($sval=='1')



			{



				$status = 0;



				}else



				{



					$status = 1;



					}



			$data = array('status'=>$status);



			$res = $this->master->update_records($table,$data,$identifier,$field_name);



			$this->session->set_flashdata('message', '<div class="alert alert-success" style="color:#000">Status successfully updated.</div>');



			redirect('Checklist/create_checklist');



		}



		



	public function edit_checklist()



	{



		$this->load->view('checklist/edit_checklist');



	}



public function checklist_filter_by_category()



	{

		$this->load->view('checklist/checklist_filter_by_category');



	}

	

	public function view_your_checklist()
	{

		$this->load->view('checklist/view_your_checklist');
	}





	



public function update_checklist()

{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('business_loc', 'business_loc', 'required|trim');
		$this->form_validation->set_rules('user_id', 'user_id', 'required|trim');
		$this->form_validation->set_rules('task', 'Task', 'required|trim');
		$this->form_validation->set_rules('turnaroundtime', 'turnaroundtime', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];

		

		if ($this->form_validation->run() == FALSE)



		{

		$this->load->view('Master/compliance/edit_compliance_task');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 

		$table = "compliance_task_report";	

		$identifier = $this->uri->segment(3);

		$field_name = "task_id";
		$data = array(

			'company_id'=>$this->input->post('business_loc'),

			'user_id'=>$this->input->post('user_id'),

			'task'=>strtoupper($this->input->post('task')),

			'video_link'=>$this->input->post('video_link'),
			'evidence_required'=>$this->input->post('evidence'),
			'complition_time'=>$this->input->post('duetime'),

			'tat_id'=>$this->input->post('turnaroundtime'),
			'added_on'=>$date,

			'status'=>$this->input->post('status'),

			'added_by'=>$user_id);

			



		$result  = $this->master->update_records($table,$data,$identifier,$field_name);		



		if($result)



		{

		    $duedate = $this->input->post('duedate');

		  //  echo $duedate;exit;

		    $setdatedata = array('task_id'=>$identifier,

		                  'dateforemail'=>date('Y-m-d',strtotime($duedate))

		                  );

		                  

		       			// echo "<pre>";print_r($data); exit;            

		    $qry = $this->db->select('id, task_id')->from('compliance_set_date')->where('task_id',$identifier)->get();

		    $res = $qry->result();

		    if($qry->num_rows()>0){

		        foreach($res as $record);

		        $this->db->where('id',$record->id);

		        $this->db->update('compliance_set_date',$setdatedata);

		    }else{

		        $this->db->insert('compliance_set_date',$setdatedata);

		    }

		    

			$this->session->set_flashdata('message','<div class="alert alert-success" style="color:#000">Thank you, record successfully added.</div>');

			redirect(page_url.'Checklist/create_checklist');

		}else



		{

			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');

			redirect(page_url.'Checklist/create_checklist');	

		}

		}

		}

		

	public function checklist_task_list_tatwise()

	{

		$invoice_data = array();

		$this->db->distinct();

		$tatid = $this->uri->segment(3);

		$user_id = $this->uri->segment(4);

		$this->db->select('a.*,b.user_id, b.first_name, b.last_name, b.title, c.id, c.turnaroundtime')->from('compliance_task_report a')->join('system_users b','a.user_id=b.user_id','left')->join('compliance_tat c','a.tat_id=c.id','left')->where('a.tat_id',$tatid)->where('b.user_status','1');

		$this->db->where('a.status','1');

		if($user_id){

			$this->db->where('a.user_id',$user_id);

		}

		$query = $this->db->order_by('a.added_on','desc')->get();

		$res = $query->result();

		$i=1;

		foreach($res as $row)

		{

			date_default_timezone_set("Asia/Kolkata");

			$addeddate = date('d-m-Y', strtotime($row->added_on));

			$time = date('H:i:s', strtotime($row->added_on));

			$addedtime = "<br>". date('g:i A', strtotime($time)); 

			

			

			$todaydate = date('Y-m-d');

			$query = $this->db->select('*')->from('compliance_set_date')->where('task_id',$row->task_id)->get();

			$res = $query->result();

			if($res){

				foreach($res as $emaildata);

				 

				$nextduedate = date('d-M-Y',strtotime($emaildata->dateforemail));

				if($emaildata->dateforemail==$todaydate || $emaildata->dateforemail<$todaydate){

				

					$update_report = "<input type='radio' name='pick_items".$row->task_id."' class='dny' id='pick_items".$i."' value='0' onChange='display_qtybox(".$i.",".$row->task_id.")'>&nbsp;Not Done";

				$update_report1 = "<input type='radio' name='pick_items".$row->task_id."' class='dny' id='pick_items1".$i."' value='1' onChange='display_qtybox(".$i.",".$row->task_id.")'>&nbsp;Done";

				

				$hidden="<div id='makenewhidden".$i."'></div>";

				$html = "<div class='row'><div class='col-md-12'><div class='col-md-6'>".$update_report."</div><div class='col-md-6'>".$update_report1."</div></div></div>".$hidden;

				

				}else{

					$html ="Reopens on<br> <strong>".date('d-M-Y',strtotime($emaildata->dateforemail))."</strong>";

					$updateremarks="Reopens on<br> <strong>".date('d-M-Y',strtotime($emaildata->dateforemail))."</strong>";

				}

			}else{

				$nextduedate="";

			}

			

			

			$query = $this->db->select('a.id, a.task_id, a.status,a.remarkdate, b.task_id, b.tat_id')->from('compliance_task_progress_remarks a')->join('compliance_task_report b','a.task_id=b.task_id','left')->where('a.task_id',$row->task_id)->limit('1')->order_by('a.id','desc')->get();

			$result = $query->result();

			if($result){

			foreach($query->result() as $progress_remarks);

			if($progress_remarks->status=='1'){

			    $current_status = "<span style='color:green; font-weight:bold;'>Done</span>";

			}elseif($progress_remarks->status=='0'){

			    $current_status = "<span style='color:red; font-weight:bold;'>Not Done</span>";

			}else{

			    $current_status = "";

			}

			

			}else{

			    $current_status = "";

			   

			}

			

			$history = "<a href='".page_url."Checklist/remark_history/".$row->task_id."'><span class='btn btn-danger btn-xs'>View History</span></a>";

			

			$invoice_data[] = array('sr_no'=>$i,

			'user_name'=>$row->title." ".$row->first_name." ".$row->last_name,

			'task'=>"<a href='".$row->video_link."'>".$row->task."</a>",

			'turnaroundtime'=>$row->turnaroundtime,

			'updateremarks'=>$html,

			'current_status'=>$current_status,

			'nextduedate'=>$nextduedate,

			'added_on'=>$addeddate.$addedtime);

			$i++;

		}

		

	$results = array(

	"sEcho" => 1,

	"iTotalRecords" => count($invoice_data),

	"iTotalDisplayRecords" => count($invoice_data),

	"aaData"=>$invoice_data);

	echo json_encode($results);

}

		

	

	

	

	

	public function task_progress_list()

	{

		$invoice_data = array();

		$this->db->distinct();

		$taskid = $this->uri->segment(3);

		$query = $this->db->select('a.id, a.task_id, a.status, a.remarks,a.added_by, a.added_on, b.user_id, b.first_name, b.last_name')->from('compliance_task_progress_remarks a')->join('system_users b','a.added_by=b.user_id','left')->where('task_id',$taskid)->order_by('added_on','desc')->get();

		$res = $query->result();

		$i=1;

		foreach($res as $row)

		{

			date_default_timezone_set("Asia/Kolkata");

			$addeddate = date('d-m-Y', strtotime($row->added_on));

			$time = date('H:i:s', strtotime($row->added_on));

			$addedtime = "<br>". date('g:i A', strtotime($time)); 

			

			$addedbyuser = $row->first_name." ".$row->last_name;

			//$edit = "<a href='".page_url."Master/Compliance/edit_compliance_task/".$row->task_id."'><i class='fa fa-pencil'></i></a>";

			if($row->status=='1'){

			$status = "<span class='btn btn-xs btn-success'>Done</span>";

			}else{

			$status = "<span class='btn btn-xs btn-danger'>Not Done</span>";

			}

			

			

			$invoice_data[] = array('sr_no'=>$i,

			'datetime'=>$addeddate.$addedtime,

			'status'=>$status,

			'remarks'=>$row->remarks,

			'added_by'=>$addedbyuser);

			$i++;

		}

	$results = array(

	"sEcho" => 1,

	"iTotalRecords" => count($invoice_data),

	"iTotalDisplayRecords" => count($invoice_data),

	"aaData"=>$invoice_data);

	echo json_encode($results);

}





public function compiled_report(){

	

	$date = date('Y-m-d');

	$query = $this->db->select('a.id, a.task_id, a.status, a.remarkdate, a.remarks, b.task_id, b.category_id, b.task, b.tat_id, c.id, c.turnaroundtime, d.category_id, d.category_name, e.task_id, e.dateforemail')->from('compliance_task_progress_remarks a')->join('compliance_task_report b','a.task_id=b.task_id','left')->join('compliance_tat c','b.tat_id=c.id','left')->join('compliance_category d','b.category_id=d.category_id','left')->join('compliance_set_date e','a.task_id=e.task_id','left')->where('a.remarkdate',$date)->order_by('a.status','asc')->get();

	

	$message="Dear Rupinder Sir,<br/><br/>Please find the detail of Compliance report.<br/><br/>";



$message.='<table cellpadding="0" cellspacing="0" width="80%" align="left" border="1" style="font-size:20px"> 

<thead>    

<tr>      

<th>Sr No.</th> 

<th>Task Name</th> 

<th>Category</th>

<th>Frequency</th> 

<th>Status</th> 

<th>Remarks</th>

<th>Next Scheduled Date</th>

</tr> 

</thead>

<tbody>';

$i=1;

foreach($query->result() as $row){

	$status = $row->status;

	if($status=='1'){

		$sta = "Done";

		$rowcolor = "Green";

	}else{

		$sta = "Not Done";

		$rowcolor = "Red";

	}

   $message.='<tr align="center" style="background-color:'.$rowcolor.'; color:white">

   <td scope="row">'.$i.'</td>

      <td>'.$row->task.'</td>

      <td>'.$row->category_name.'</td>

      <td>'.$row->turnaroundtime.'</td>

  <td>'.$sta.'</td>

      <td>'.$row->remarks.'</td>

      <td>'.date('d-M-Y',strtotime($row->dateforemail)).'</td>





    </tr>';

	$i++;}

	



$message.='</tbody>

</table> ';



					$this->email->set_mailtype("html");

					$this->email->to("mangleshup@gmail.com");

					$this->email->bcc('webdevelopment1@gamavis.com');

					$this->email->from('donotreply@skexports.in');

    				$this->email->subject('Compliance Report of the Day '.date('d-m-Y'));

    			    $this->email->message($message);

    				$result11=$this->email->send();

    			    echo $this->email->print_debugger(array('headers')); exit;

	

	

		

	

	

}

public function compliance_task_by_category()



	{



		$this->load->view('Master/compliance/filterbycategory');



	}



	public function compliance_category_dashboard(){

		$this->load->view('Master/compliance/compliance_category_dashboard');

	}



public function compliance_task_list_categorywise()

	{

		$invoice_data = array();

		$this->db->distinct();

		$tatid = $this->uri->segment(4);

		$query = $this->db->select('a.*,b.category_id, b.category_name, c.id, c.turnaroundtime')->from('compliance_task_report a')->join('compliance_category b','a.category_id=b.category_id','left')->join('compliance_tat c','a.tat_id=c.id','left')->where('a.category_id',$tatid)->order_by('b.category_id','asc')->get();

		$res = $query->result();

		$i=1;

		foreach($res as $row)

		{

			date_default_timezone_set("Asia/Kolkata");

			$addeddate = date('d-m-Y', strtotime($row->added_on));

			$time = date('H:i:s', strtotime($row->added_on));

			$addedtime = "<br>". date('g:i A', strtotime($time)); 

			

			$edit = "<a href='".page_url."Master/Compliance/edit_compliance_task/".$row->task_id."'><i class='fa fa-pencil'></i></a>";

			if($row->status=='1'){

			$status = "<a href='".page_url."Master/Compliance/update_compliance_task_status/".$row->task_id."/".$row->status."'><span class='btn btn-xs btn-success'>Active</span></a>";

			}else{

			$status = "<a href='".page_url."Master/Compliance/update_compliance_task_status/".$row->task_id."/".$row->status."'><span class='btn btn-xs btn-danger'>Not Active</span></a>";

			}

			

			

			$todaydate = date('Y-m-d');

			$query = $this->db->select('*')->from('compliance_set_date')->where('task_id',$row->task_id)->get();

			$res = $query->result();

			if($res){

				foreach($res as $emaildata)

				if($emaildata->dateforemail==$todaydate || $emaildata->dateforemail<$todaydate){

				if($row->annexure_file!==''){

					$html = "<a href='".page_url."Master/Compliance/update_remarks/".$row->task_id."'><span class='btn btn-xs btn-warning'>Update Remark</span></a>";

				}else{

					$update_report = "<input type='radio' name='pick_items".$row->task_id."' class='dny' id='pick_items".$i."' value='0' onChange='display_qtybox(".$i.",".$row->task_id.")'>&nbsp;Not Done";

				$update_report1 = "<input type='radio' name='pick_items".$row->task_id."' class='dny' id='pick_items1".$i."' value='1' onChange='display_qtybox(".$i.",".$row->task_id.")'>&nbsp;Done";

				$reporting_box = "<textarea name='rtrm".$row->task_id."' class='form-control' style='display:none; width:100%' id='qty".$i."'></textarea>";

				$hidden="<div id='makenewhidden".$i."'></div>";

				$html = "<div class='row'><div class='col-md-12'><div class='col-md-6'>".$update_report."</div><div class='col-md-6'>".$update_report1."</div></div><div class='col-md-12'><div class='col-md-12'>".$reporting_box."</div></div></div>".$hidden;

				}

					

				}elseif($row->tat_id=='6'){

				    if($row->annexure_file!==''){

					$html = "<a href='".page_url."Master/Compliance/update_remarks/".$row->task_id."'><span class='btn btn-xs btn-warning'>Update Remark</span></a>";

				}else{

					$update_report = "<input type='radio' name='pick_items".$row->task_id."' class='dny' id='pick_items".$i."' value='0' onChange='display_qtybox(".$i.",".$row->task_id.")'>&nbsp;Not Done";

				$update_report1 = "<input type='radio' name='pick_items".$row->task_id."' class='dny' id='pick_items1".$i."' value='1' onChange='display_qtybox(".$i.",".$row->task_id.")'>&nbsp;Done";

				$reporting_box = "<textarea name='rtrm".$row->task_id."' class='form-control' style='display:none; width:100%' id='qty".$i."'></textarea>";

				$hidden="<div id='makenewhidden".$i."'></div>";

				$html = "<div class='row'><div class='col-md-12'><div class='col-md-6'>".$update_report."</div><div class='col-md-6'>".$update_report1."</div></div><div class='col-md-12'><div class='col-md-12'>".$reporting_box."</div></div></div>".$hidden;

				}

				}else{

					$html ="Reopens on<br> <strong>".date('d-M-Y',strtotime($emaildata->dateforemail))."</strong>";

					$updateremarks="Reopens on<br> <strong>".date('d-M-Y',strtotime($emaildata->dateforemail))."</strong>";

				}

			}else{

			if($row->annexure_file!==''){

					$html = "<a href='".page_url."Master/Compliance/update_remarks/".$row->task_id."'><span class='btn btn-xs btn-warning'>Update Remark</span></a>";

				}else{

					$update_report = "<input type='radio' name='pick_items".$row->task_id."' class='dny' id='pick_items".$i."' value='0' onChange='display_qtybox(".$i.",".$row->task_id.")'>&nbsp;Not Done";

				$update_report1 = "<input type='radio' name='pick_items".$row->task_id."' class='dny' id='pick_items1".$i."' value='1' onChange='display_qtybox(".$i.",".$row->task_id.")'>&nbsp;Done";

				$reporting_box = "<textarea name='rtrm".$row->task_id."' class='form-control' style='display:none; width:100%' id='qty".$i."'></textarea>";

				$hidden="<div id='makenewhidden".$i."'></div>";

				$html = "<div class='row'><div class='col-md-12'><div class='col-md-6'>".$update_report."</div><div class='col-md-6'>".$update_report1."</div></div><div class='col-md-12'><div class='col-md-12'>".$reporting_box."</div></div></div>".$hidden;

				}

			}

			

			

				$query = $this->db->select('a.id, a.task_id, a.status,a.remarkdate, b.task_id, b.tat_id, a.momfile')->from('compliance_task_progress_remarks a')->join('compliance_task_report b','a.task_id=b.task_id','left')->where('a.task_id',$row->task_id)->where('b.tat_id','6')->where('a.remarkdate',date('Y-m-d'))->limit('1')->order_by('a.id','desc')->get();

			$result = $query->result();

			if($result){

			foreach($query->result() as $progress_remarks);

			if($progress_remarks->status=='1'){

			    $current_status = "<span style='color:green; font-weight:bold;'>Done</span>";

			}elseif($progress_remarks->status=='0'){

			    $current_status = "<span style='color:red; font-weight:bold;'>Not Done</span>";

			}else{

			    $current_status = "";

			}

			$momfile = $progress_remarks->momfile;

			}else{

			    $current_status = "";

			    $momfile = "";

			}

			

			$query = $this->db->select('a.id, a.task_id, a.status,a.remarkdate, b.task_id, b.tat_id, a.momfile, a.added_on, a.added_by, c.user_id, c.first_name, c.last_name')->from('compliance_task_progress_remarks a')->join('compliance_task_report b','a.task_id=b.task_id','left')->join('system_users c','a.added_by=c.user_id','left')->where('a.task_id',$row->task_id)->limit('1')->order_by('a.id','desc')->get();

			$result1 = $query->result();

			if($result1){

			    foreach($result1 as $momfile1){

			        $momfile = $momfile1->momfile;

			        if($momfile){

			        $addeddate1 = date('d-m-Y', strtotime($momfile1->added_on));

			$time1 = date('H:i:s', strtotime($momfile1->added_on));

			$addedtime1 = "<br>". date('g:i A', strtotime($time1)); 

			$updatedby  = $momfile1->first_name." ".$momfile1->last_name;

			        }else{

			            $addeddate1="";

			    $addedtime1=""; 

			    $updatedby="";

			        }

			    }

			}else{

			    $momfile="";

			    $addeddate1="";

			    $addedtime1="";

			    $updatedby="";

			}

			$history = "<a href='".page_url."Checklist/remark_history/".$row->task_id."'><span class='btn btn-danger btn-xs'>View History</span></a>";

			

			$invoice_data[] = array('sr_no'=>$i,

			'category_name'=>$row->category_name,

			'task'=>$row->task,

			'turnaroundtime'=>$row->turnaroundtime,

			'edit'=>$edit,

			'status'=>$status,

			'annexurefile'=>"<a href='".compliance_file.$row->annexure_file."' download>".$row->annexure_file."</a>",

			'momfile'=>"<a href='".compliance_file.$momfile."' download>".$momfile."</a>"."<br>".$addeddate1.$addedtime1."<br>".$updatedby,

			'updateremarks'=>$html."<br><br>".$history,

			'current_status'=>$current_status,

			'added_on'=>$addeddate.$addedtime);

			$i++;

		}

		

	$results = array(

	"sEcho" => 1,

	"iTotalRecords" => count($invoice_data),

	"iTotalDisplayRecords" => count($invoice_data),

	"aaData"=>$invoice_data);

	echo json_encode($results);

}



public function remark_history(){

    $this->load->view('checklist/report_history');

}

function updateremarks()

{

  $user_id =$this->session->userdata['logged_in']['user_id'];
  date_default_timezone_set("Asia/Kolkata");
  $lastupdatedate = date('Y-m-d');
  $checkeditem=$this->input->post('countall');
  
 for($i=0;$i<count($checkeditem);$i++)

 {
	
	$taskid=$checkeditem[$i];
	$photo1=$_FILES["evidence".$taskid]["name"];
	if($photo1<>'')
	{
	$image2=explode('.',$photo1);
	$cat_image1=end($image2);
	$evidence=time().$taskid.'.'.$cat_image1;
	move_uploaded_file($_FILES["evidence".$taskid]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/image_bank/checklist/' . $evidence);
	}else
	{
	$evidence="";
	}	
	
	//echo $evidence; exit;

	 $yno=$_REQUEST['pick_items'.$taskid];

	if($yno=='0')

	{

	 $taskstatus='0';

	}else

	{
		$taskstatus='1';

	}

	

	 $rmkdate = date('Y-m-d');

	 $data=array('task_id'=>$taskid,
	'status'=>$taskstatus,
	'remarkdate'=>$rmkdate,
	'added_by'=>$user_id,
	'evidence_attachment'=>$evidence,
	'added_on'=>date('Y-m-d H:i:s'));
	
	$this->db->insert('compliance_task_progress_remarks',$data);
	$data1 = array('task_id'=>$taskid,
	'status'=>$taskstatus,
	'task_date'=>date('Y-m-d'));

	$this->db->insert('checklist_done_notdone',$data1);

	$query = $this->db->select('task_id, tat_id')->from('compliance_task_report')->where('task_id',$taskid)->get();

foreach($query->result() as $tatinfo){

	$tatid = $tatinfo->tat_id;
	$todaysdate = date('Y-m-d');
	$nextdate=date('Y-m-d');
	$query1 = $this->db->select('id, turnaroundtime')->from('compliance_tat')->where('id',$tatinfo->tat_id)->get();

	foreach($query1->result() as $row1)

	{

		$todaysdate = date('Y-m-d');

		$nextdate="";

		if($row1->id=='1'){

			/** For 1 daily**/

			$todaysdate = date('Y-m-d');

			$date = new DateTime($todaysdate); // Y-m-d

			$date->add(new DateInterval('P1D')); 

			$nextdate = $date->format('Y-m-d');

			/* CHECK HOLIDAY*/

			$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();

			if($query->num_rows()>0){

				$nextdate = date('Y-m-d', strtotime("+1 day", strtotime($nextdate)));

			}else{

				$nextdate = $nextdate;

			}

			/* CHECK HOLIDAY*/

		}elseif($row1->id=='2'){

			/** For Weekly**/

			$todaysdate = date('Y-m-d');
			$date = new DateTime($todaysdate);
			$date->add(new DateInterval('P7D')); 
			$nextdate = $date->format('Y-m-d');
			/* CHECK HOLIDAY*/

			$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();

			if($query->num_rows()>0){

				$nextdate = date('Y-m-d', strtotime("+1 day", strtotime($nextdate)));

			}else{

				$nextdate = $nextdate;

			}

			/* CHECK HOLIDAY*/

		}elseif($row1->id=='3'){

				/** For MONTHLY**/

			$todaysdate = date('Y-m-d');
			$date = new DateTime($todaysdate); // Y-m-d
			$date->add(new DateInterval('P30D')); 
			$nextdate = $date->format('Y-m-d');

			/* CHECK HOLIDAY*/

			$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();

			if($query->num_rows()>0){

				$nextdate = date('Y-m-d', strtotime("+1 day", strtotime($nextdate)));

			}else{

				$nextdate = $nextdate;

			}

			/* CHECK HOLIDAY*/

		}elseif($row1->id=='4'){

			/** For Yearly**/

			$todaysdate = date('Y-m-d');
			$date = new DateTime($todaysdate);
			$date->add(new DateInterval('P365D')); 
			$nextdate = $date->format('Y-m-d');
			/* CHECK HOLIDAY*/

			$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();

			if($query->num_rows()>0){

				$nextdate = date('Y-m-d', strtotime("+1 day", strtotime($nextdate)));

			}else{

				$nextdate = $nextdate;

			}

			/* CHECK HOLIDAY*/

		}elseif($row1->id=='5'){

			/** For twice in a week**/

			$todaysdate = date('Y-m-d');
			$date = new DateTime($todaysdate);
			$date->add(new DateInterval('P3D')); 
			$nextdate = $date->format('Y-m-d');
			/* CHECK HOLIDAY*/

			$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();
			if($query->num_rows()>0){
			$nextdate = date('Y-m-d', strtotime("+1 day", strtotime($nextdate)));
			}else{
			$nextdate = $nextdate;
			}

			/* CHECK HOLIDAY*/

		}elseif($row1->id=='8'){

			/** For FORTNIGHTLY**/

			$todaysdate = date('Y-m-d');
			$date = new DateTime($todaysdate);
			$date->add(new DateInterval('P14D')); 
			$nextdate = $date->format('Y-m-d');

			/* CHECK HOLIDAY*/

			$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();
			if($query->num_rows()>0){
			$nextdate = date('Y-m-d', strtotime("+1 day", strtotime($nextdate)));
			}else{
			$nextdate = $nextdate;
			}

			/* CHECK HOLIDAY*/

		}elseif($row1->id=='9'){

		    /*for QUARTERLY*/

		   $todaysdate = date('Y-m-d');
			$date = new DateTime($todaysdate);
			$date->add(new DateInterval('P90D')); 
			$nextdate = $date->format('Y-m-d');
			/* CHECK HOLIDAY*/

			$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();
			if($query->num_rows()>0){
			date('Y-m-d', strtotime("+1 day", strtotime($nextdate)));
			}else{

				$nextdate = $nextdate;

			}
		/* CHECK HOLIDAY*/ 

		}elseif($row1->id=='12'){

		   /*for TWICE A DAY*/
			
		   $q = $this->db->select('id,first_time_status, second_time_status')->from('checklist_frequency_management')->where('task_id',$taskid)->where('due_date',date('Y-m-d'))->get();
		   if($q->num_rows()>0){
			  
			   foreach($q->result() as $checkdata);
			   $data22 = array('first_time_status'=>0,
			   'second_time_status'=>0);
			   
			   $this->db->where('id',$checkdata->id);
			   $this->db->update('checklist_frequency_management',$data22);
			   if($checkdata->first_time_status==0 && $checkdata->second_time_status==0){
				   $data = array('first_time_status'=>1);
				   $this->db->where('task_id',$taskid);
				   $this->db->update('checklist_frequency_management',$data);
				   $nextdate = date('Y-m-d');
			   }else if($checkdata->first_time_status==1 && $checkdata->second_time_status==0){
				   $data = array('second_time_status'=>1);
				   $this->db->where('task_id',$taskid);
				   $this->db->update('checklist_frequency_management',$data);
					$todaysdate = date('Y-m-d');
					$date = new DateTime($todaysdate);
					$date->add(new DateInterval('P1D')); 
					$nextdate = $date->format('Y-m-d');

			   }else{
				$todaysdate = date('Y-m-d');
				$date = new DateTime($todaysdate);
				$date->add(new DateInterval('P1D')); 
				$nextdate = $date->format('Y-m-d');
			   }
		   }else{
			   $nextdate = date('Y-m-d');
		   }
		   

			/* CHECK HOLIDAY*/

			$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();

			if($query->num_rows()>0){

				date('Y-m-d', strtotime("+1 day", strtotime($nextdate)));

			}else{

				$nextdate = $nextdate;

			}

		

			/* CHECK HOLIDAY*/ 

		}elseif($row1->id=='13'){

		   /*for Thrice in A DAY*/
			
		   $q = $this->db->select('id,first_time_status, second_time_status,third_time_status')->from('checklist_frequency_management')->where('task_id',$taskid)->where('due_date',date('Y-m-d'))->get();
		   if($q->num_rows()>0){
			  
			  foreach($q->result() as $checkdata);
			   $data22 = array('first_time_status'=>0,
			   'second_time_status'=>0,
			   'third_time_status'=>0);
			   $this->db->where('id',$checkdata->id);
			   $this->db->update('checklist_frequency_management',$data22);
			   if($checkdata->first_time_status==0 && $checkdata->second_time_status==0 && $checkdata->third_time_status==0){
				   $data = array('first_time_status'=>1);
				   $this->db->where('task_id',$taskid);
				   $this->db->update('checklist_frequency_management',$data);
				   $nextdate = date('Y-m-d');
			   }else if($checkdata->first_time_status==1 && $checkdata->second_time_status==0 && $checkdata->third_time_status==0){
				   $data = array('second_time_status'=>1);
				   $this->db->where('task_id',$taskid);
				   $this->db->update('checklist_frequency_management',$data);
					$todaysdate = date('Y-m-d');
					$date = new DateTime($todaysdate);
					$date->add(new DateInterval('P1D')); 
					$nextdate = $date->format('Y-m-d');

			   }else if($checkdata->first_time_status==1 && $checkdata->second_time_status==1 && $checkdata->third_time_status==0){
				   $data = array('third_time_status'=>1);
				   $this->db->where('task_id',$taskid);
				   $this->db->update('checklist_frequency_management',$data);
					$todaysdate = date('Y-m-d');
					$date = new DateTime($todaysdate);
					$date->add(new DateInterval('P1D')); 
					$nextdate = $date->format('Y-m-d');

			   }else{
				$todaysdate = date('Y-m-d');
				$date = new DateTime($todaysdate);
				$date->add(new DateInterval('P1D')); 
				$nextdate = $date->format('Y-m-d');
			   }
		   }else{
			   $nextdate = date('Y-m-d');
		   }
		   

			/* CHECK HOLIDAY*/

			$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();

			if($query->num_rows()>0){

				date('Y-m-d', strtotime("+1 day", strtotime($nextdate)));

			}else{

				$nextdate = $nextdate;

			}

		

			/* CHECK HOLIDAY*/ 

		}elseif($row1->id=='11'){

		    /*for THRICE A DAY*/

		   $todaysdate = date('Y-m-d');

			$date = new DateTime($todaysdate);

			$date->add(new DateInterval('P1D')); 

			$nextdate = $date->format('Y-m-d');

			
			/* CHECK HOLIDAY*/

			$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();

			if($query->num_rows()>0){

				date('Y-m-d', strtotime("+1 day", strtotime($nextdate)));

			}else{

				$nextdate = $nextdate;

			}

			/* CHECK HOLIDAY*/ 

		}


elseif($row1->id=='15'){

		    /*for THRICE IN A WEEK*/

		   $todaysdate = date('Y-m-d');

			$date = new DateTime($todaysdate);

			$date->add(new DateInterval('P2D')); 

			$nextdate = $date->format('Y-m-d');

			
			/* CHECK HOLIDAY*/

			$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();

			if($query->num_rows()>0){

				date('Y-m-d', strtotime("+1 day", strtotime($nextdate)));

			}else{

				$nextdate = $nextdate;

			}

			/* CHECK HOLIDAY*/ 

		}
	

	$query = $this->db->select('*')->from('compliance_set_date')->where('task_id',$taskid)->get();
	$res = $query->result();

	if($query->num_rows()>0){

		foreach($res as $updatedata)

		$data = array('dateforemail'=>$nextdate,'last_update_date'=>date('Y-m-d'));

		$this->db->where('id',$updatedata->id);

		$this->db->update('compliance_set_date',$data);

		

	}else{

		

		$data = array('dateforemail'=>$nextdate,'task_id'=>$taskid,'last_update_date'=>date('Y-m-d'));

		$this->db->insert('compliance_set_date',$data);

	}

	

	}

}

}

$uri = $this->uri->segment(3);

	$userid = $this->uri->segment(4);

if($uri!=='yourcheck'){

$this->session->set_flashdata('message','<div class="alert alert-success" style="color:#000">Thank you, record successfully added.</div>');

    redirect(page_url.'Checklist/checklist_filter_by_category/'.$uri.".".$userid);

}else if($uri=='yourcheck'){

	$this->session->set_flashdata('message','<div class="alert alert-success" style="color:#000">Thank you, record successfully added.</div>');

    redirect(page_url.'Checklist/view_your_checklist/');

}else{

	$this->session->set_flashdata('message','<div class="alert alert-success" style="color:#000">Thank you, record successfully added.</div>');

    redirect(page_url.'Checklist/create_checklist/');

}	

}

public function take_printout(){

    $this->load->view('Master/compliance/take_printout');

}



public function done_not_done_report(){

    $this->load->view('Master/compliance/done_not_done_report');

}



public function compliance_task_done_Report()

	{

	$invoice_data = array();

		$this->db->distinct();

		$date= date('Y-m-d');

		$i=1;

		$companyid=$_SESSION['logged_in']['company'];

		$query = $this->db->select('a.id, a.task_id, a.status, a.remarkdate, a.remarks, a.added_on, a.added_by, b.task_id, b.company_id, b.category_id, b.task, b.tat_id, c.id, c.turnaroundtime, d.category_id, d.category_name, e.task_id, e.dateforemail, f.user_id, f.title, f.first_name, f.last_name')->from('compliance_task_progress_remarks a')->join('compliance_task_report b','a.task_id=b.task_id','left')->join('compliance_tat c','b.tat_id=c.id','left')->join('compliance_category d','b.category_id=d.category_id','left')->join('compliance_set_date e','a.task_id=e.task_id','left')->join('system_users f','a.added_by=f.user_id','left')->where('a.remarkdate',$date)->where('a.status','1')->where('b.company_id',$companyid)->order_by('a.status','asc')->get();

		

		foreach($query->result() as $row){

		    	$status = $row->status;

	if($status=='1'){

		$sta = "Done";

		$rowcolor = "Green";

	}else{

		$sta = "Not Done";

		$rowcolor = "Red";

	}

	$addeddate = date('d-m-Y', strtotime($row->added_on));

			$time = date('H:i:s', strtotime($row->added_on));

			$addedtime = "<br>". date('g:i A', strtotime($time));

			

			$invoice_data[] = array('sr_no'=>$i,

			'category_name'=>strtoupper($row->category_name),

			'task'=>strtoupper($row->task),

			'turnaroundtime'=>strtoupper($row->turnaroundtime),

			'status'=>$sta,

			'remarks'=>strtoupper($row->remarks),

			'added_time'=>$addeddate.$addedtime,

			'added_by'=>$row->title." ".$row->first_name." ".$row->last_name,

			'nextdate'=>date('d-M-Y',strtotime($row->dateforemail)));

			$i++;

		}

		

	$results = array(

	"sEcho" => 1,

	"iTotalRecords" => count($invoice_data),

	"iTotalDisplayRecords" => count($invoice_data),

	"aaData"=>$invoice_data);

	echo json_encode($results);

}



public function compliance_task_not_done_Report()

	{

	    $companyid=$_SESSION['logged_in']['company'];

		$invoice_data = array();

		$this->db->distinct();

		$date= date('Y-m-d');

		$i=1;

		$query = $this->db->select('a.id, a.task_id, a.status, a.remarkdate, a.remarks, a.added_on, a.added_by, b.task_id,b.company_id, b.category_id, b.task, b.tat_id, c.id, c.turnaroundtime, d.category_id, d.category_name, e.task_id, e.dateforemail, f.user_id, f.title, f.first_name, f.last_name')->from('compliance_task_progress_remarks a')->join('compliance_task_report b','a.task_id=b.task_id','left')->join('compliance_tat c','b.tat_id=c.id','left')->join('compliance_category d','b.category_id=d.category_id','left')->join('compliance_set_date e','a.task_id=e.task_id','left')->join('system_users f','a.added_by=f.user_id','left')->where('a.remarkdate',$date)->where('a.status','0')->where('b.company_id',$companyid)->order_by('a.status','asc')->get();

		

		foreach($query->result() as $row){

		    	$status = $row->status;

	if($status=='1'){

		$sta = "Done";

		$rowcolor = "Green";

	}else{

		$sta = "Not Done";

		$rowcolor = "Red";

	}

	$addeddate = date('d-m-Y', strtotime($row->added_on));

			$time = date('H:i:s', strtotime($row->added_on));

			$addedtime = "<br>". date('g:i A', strtotime($time));

			

			$invoice_data[] = array('sr_no'=>$i,

			'category_name'=>strtoupper($row->category_name),

			'task'=>strtoupper($row->task),

			'turnaroundtime'=>strtoupper($row->turnaroundtime),

			'status'=>$sta,

			'remarks'=>strtoupper($row->remarks),

			'added_time'=>$addeddate.$addedtime,

			'added_by'=>$row->title." ".$row->first_name." ".$row->last_name,

			'nextdate'=>date('d-M-Y',strtotime($row->dateforemail)));

			$i++;

		}

		

	$results = array(

	"sEcho" => 1,

	"iTotalRecords" => count($invoice_data),

	"iTotalDisplayRecords" => count($invoice_data),

	"aaData"=>$invoice_data);

	echo json_encode($results);

}

public function done_not_done_filter_by_date(){

    $data['datewise'] = array('date'=>date('Y-m-d',strtotime($this->input->post('fromdate'))));

    $this->load->view('Master/compliance/filter_done_not_done_report',$data);

}



public function compliance_task_done_Report_datewise()

	{

	$invoice_data = array();

		$this->db->distinct();

		$date= $this->uri->segment(4);

		$i=1;

		$companyid=$_SESSION['logged_in']['company'];

		$query = $this->db->select('a.id, a.task_id, a.status, a.remarkdate, a.remarks, a.added_on, a.added_by, b.task_id,b.company_id, b.category_id, b.task, b.tat_id, c.id, c.turnaroundtime, d.category_id, d.category_name, e.task_id, e.dateforemail, f.user_id, f.title, f.first_name, f.last_name')->from('compliance_task_progress_remarks a')->join('compliance_task_report b','a.task_id=b.task_id','left')->join('compliance_tat c','b.tat_id=c.id','left')->join('compliance_category d','b.category_id=d.category_id','left')->join('compliance_set_date e','a.task_id=e.task_id','left')->join('system_users f','a.added_by=f.user_id','left')->where('a.remarkdate',$date)->where('a.status','1')->where('b.company_id',$companyid)->order_by('a.status','asc')->get();

		

		foreach($query->result() as $row){

		    	$status = $row->status;

	if($status=='1'){

		$sta = "Done";

		$rowcolor = "Green";

	}else{

		$sta = "Not Done";

		$rowcolor = "Red";

	}

	$addeddate = date('d-m-Y', strtotime($row->added_on));

			$time = date('H:i:s', strtotime($row->added_on));

			$addedtime = "<br>". date('g:i A', strtotime($time));

			

			$invoice_data[] = array('sr_no'=>$i,

			'category_name'=>strtoupper($row->category_name),

			'task'=>strtoupper($row->task),

			'turnaroundtime'=>strtoupper($row->turnaroundtime),

			'status'=>$sta,

			'remarks'=>strtoupper($row->remarks),

			'added_time'=>$addeddate.$addedtime,

			'added_by'=>$row->title." ".$row->first_name." ".$row->last_name,

			'nextdate'=>date('d-M-Y',strtotime($row->dateforemail)));

			$i++;

		}

		

	$results = array(

	"sEcho" => 1,

	"iTotalRecords" => count($invoice_data),

	"iTotalDisplayRecords" => count($invoice_data),

	"aaData"=>$invoice_data);

	echo json_encode($results);

}



public function compliance_task_not_done_Report_datewise()

	{

		$invoice_data = array();

		$this->db->distinct();

		$date= $this->uri->segment(4);

		$i=1;

		$companyid=$_SESSION['logged_in']['company'];

		$query = $this->db->select('a.id, a.task_id, a.status, a.remarkdate, a.remarks, a.added_on,a.added_by, b.task_id,b.company_id, b.category_id, b.task, b.tat_id, c.id, c.turnaroundtime, d.category_id, d.category_name, e.task_id, e.dateforemail, f.user_id, f.title, f.first_name, f.last_name')->from('compliance_task_progress_remarks a')->join('compliance_task_report b','a.task_id=b.task_id','left')->join('compliance_tat c','b.tat_id=c.id','left')->join('compliance_category d','b.category_id=d.category_id','left')->join('compliance_set_date e','a.task_id=e.task_id','left')->join('system_users f','a.added_by=f.user_id','left')->where('a.remarkdate',$date)->where('a.status','0')->where('b.company_id',$companyid)->order_by('a.status','asc')->get();

		

		foreach($query->result() as $row){

		    	$status = $row->status;

	if($status=='1'){

		$sta = "Done";

		$rowcolor = "Green";

	}else{

		$sta = "Not Done";

		$rowcolor = "Red";

	}

	$addeddate = date('d-m-Y', strtotime($row->added_on));

			$time = date('H:i:s', strtotime($row->added_on));

			$addedtime = "<br>". date('g:i A', strtotime($time));

			

			$invoice_data[] = array('sr_no'=>$i,

			'category_name'=>strtoupper($row->category_name),

			'task'=>strtoupper($row->task),

			'turnaroundtime'=>strtoupper($row->turnaroundtime),

			'status'=>$sta,

			'remarks'=>strtoupper($row->remarks),

			'added_time'=>$addeddate.$addedtime,

			'added_by'=>$row->title." ".$row->first_name." ".$row->last_name,

			'nextdate'=>date('d-M-Y',strtotime($row->dateforemail)));

			$i++;

		}

		

	$results = array(

	"sEcho" => 1,

	"iTotalRecords" => count($invoice_data),

	"iTotalDisplayRecords" => count($invoice_data),

	"aaData"=>$invoice_data);

	echo json_encode($results);

}





public function add_Remarks()



	{



		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');



		$this->form_validation->set_rules('remarks', 'remarks', 'required|trim');



		$user_id =$this->session->userdata['logged_in']['user_id'];		



		if ($this->form_validation->run() == FALSE)



		{



			$this->load->view('Master/compliance/compliance_tasks');



		}



		else



		{



		date_default_timezone_set("Asia/Kolkata");



		$date =  date('Y-m-d H:i:s'); 



		$table = "compliance_common_remarks";		



			$data = array('remark_date'=>date('Y-m-d'),

			'remarks'=>$this->input->post('remarks'),

			'added_by'=>$user_id,

			'added_on'=>$date);



		$result  = $this->master->insert_record($table,$data);	



		if($result)



		{



			$this->session->set_flashdata('message','Thank you, record successfully added.');



			redirect(page_url.'Master/Compliance/compliance_task');



			



		}else



		{



			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');



			redirect(page_url.'Master/Compliance/compliance_task');	



			



		}



	



	}



		



	}

	

		public function remarks_list()

	{

		$invoice_data = array();

		$this->db->distinct();

		$query = $this->db->select('a.*, b.user_id, b.first_name, b.last_name')->from('compliance_common_remarks a')->join('system_users b','a.added_by=b.user_id','left')->order_by('a.added_on','desc')->get();

		$res = $query->result();

		$i=1;

		foreach($res as $row)

		{

			date_default_timezone_set("Asia/Kolkata");

			$addeddate = date('d-m-Y', strtotime($row->added_on));

			$time = date('H:i:s', strtotime($row->added_on));

			$addedtime = "<br>". date('g:i A', strtotime($time)); 

			

			$addedbyuser = $row->first_name." ".$row->last_name;

			

			

			

			

			$invoice_data[] = array('sr_no'=>$i,

			'datetime'=>date('d-m-Y',strtotime($row->remark_date)),

			'remarks'=>$row->remarks,

			'added_on'=>$addeddate.$addedtime,

			'added_by'=>$addedbyuser);

			$i++;

		}

	$results = array(

	"sEcho" => 1,

	"iTotalRecords" => count($invoice_data),

	"iTotalDisplayRecords" => count($invoice_data),

	"aaData"=>$invoice_data);

	echo json_encode($results);

}

public function remarks_history_list(){

    $this->load->view('Master/compliance/remarks_list');

}



public function compliance_monthly_report(){

     $this->load->view('Master/compliance/compliance_monthly_report');

}





public function monthly_performance_report(){

	    $guideline_data = array();

	    $start_date = date('Y-m-')."01";

         $end_date = date("Y-m-t", strtotime($start_date));

         $todaysdate = date('Y-m-d');

         $totaldays =  date('t');

        $num_sundays="";

        for ($i = 0; $i < ((strtotime($end_date) - strtotime($start_date)) / 86400); $i++){ 

        if(date('l',strtotime($start_date) + ($i * 86400)) == 'Sunday'){ 

        $num_sundays++; 

        } 

        } 

        $num1_sundays="";

        for ($i = 0; $i < ((strtotime($todaysdate) - strtotime($start_date)) / 86400); $i++){ 

        if(date('l',strtotime($start_date) + ($i * 86400)) == 'Sunday'){ 

        $num1_sundays++; 

        } 

        } 



$datetime1 = date_create($start_date);

$datetime2 = date_create($todaysdate);

$interval = date_diff($datetime1, $datetime2);

$total =  $interval->days;

$datewithoutsunday  =$total-$num1_sundays;



       $totalworkingdays = $totaldays-$num_sundays;

       $query = $this->db->select('remarkdate')->from('compliance_task_progress_remarks')->where('remarkdate BETWEEN "'. $start_date. '" and "'. $end_date.'"')->group_by('remarkdate')->get();

	    $totaldaystaskupdated = count($query->result());	

		$totaldaysnotupdated  = $datewithoutsunday-$totaldaystaskupdated;

			



			$guideline_data[] = array('sr_no'=>1,

			'number_of_days_working'=>$totalworkingdays,

			'totaldaytaskupdated'=>$totaldaystaskupdated,

			'totaldaytasknotupdated'=>$totaldaysnotupdated,

			'total_task_not_done'=>10

			);

			

	$results = array(

	"sEcho" => 1,

	"iTotalRecords" => count($guideline_data),

	"iTotalDisplayRecords" => count($guideline_data),

	"aaData"=>$guideline_data);

	echo json_encode($results);

}





public function over_due_compliance_report(){

    	$invoice_data = array();

		$this->db->distinct();

		$query = $this->db->select('a.id, a.task_id, a.dateforemail, b.task_id, b.category_id, b.task, b.tat_id, c.category_id, c.category_name, d.id, d.turnaroundtime')->from('compliance_set_date a')->join('compliance_task_report b','a.task_id=b.task_id','left')->join('compliance_category c','b.category_id=c.category_id','left')->join('compliance_tat d','b.tat_id=d.id','left')->where('a.dateforemail<',date('Y-m-d'))->order_by('a.dateforemail','desc')->get();

		$res = $query->result();

	 $message="";

	 $message."Dear Sir/Maam,<br>Please find the report of Over Due Compliance. <br>";

			 $message.='<table cellpadding="0" cellspacing="0" width="80%" align="left" border="1" style="font-size:12px; font-family:calibri;"> 

<thead style="background-color:yellow">    

<tr>      

<th>SR NO.</th> 

<th>CATEGORY</th> 

<th>TASK NAME</th>

<th>FREQUENCY</th>

<th>DUE DATE</th> 

</tr> 

</thead>

<tbody>';

$i=1;

foreach($res as $row){



   $message.='<tr align="center">

      <td scope="row">'.$i.'</td>

      <td>'.strtoupper($row->category_name).'</td>

      <td>'.strtoupper($row->task).'</td>

      <td>'.strtoupper($row->turnaroundtime).'</td>

      

      <td>'.date('d-M-Y',strtotime($row->dateforemail)).'</td>

      





    </tr>';

	$i++;}

	



$message.='</tbody>

</table> ';

//echo $message; exit;			 

$emails = array();

$query = $this->db->select('id, email, module_id, notify_permission')->from('email_for_notification_access')->where('module_id','3')->where('notify_permission','1')->get();

foreach($query->result() as $masteremail){

    $emails[] = $masteremail->email;

}

//echo "<pre>"; print_r($emails); exit;

if(count($emails>0)){

$ccemail = trim(implode(',',$emails),',');

}else{

  $ccemail="";  

}		 

			

			//	echo $message; exit;

				$subjectname = "Over Due Compliance Report ";

					$this->email->set_mailtype("html");

						$this->email->to('rupinder@skweaving.com');

					$this->email->cc($ccemails);

					$this->email->bcc('webdevelopment1@gamavis.com');

					$this->email->from('donotreply@skexports.in');

    				$this->email->subject($subjectname);

    				$this->email->message($message);

    				$result11=$this->email->send();

					

					

			$this->session->set_flashdata('message','Thank you, over due compliance report email sent.');

			redirect(page_url.'Master/Compliance/compliance_task');



    

}



Public function check_done_not_task(){

			/* CHECK THAT TASK HAS BEEN UPDATED OR NOT */

			$qry = $this->db->select('a.task_id, a.dateforemail, b.id, b.status')->from('compliance_set_date a')->join('compliance_tat b','a.task_id=b.id','left')->where('b.status','1')->where('a.dateforemail',date('Y-m-d'))->or_where('a.dateforemail<',date('Y-m-d'))->get();

	

			if($qry->num_rows()>0){

				

				$day = date('l');

				if($day=='Sunday'){

				$status = "1";

				}else{

					$status="0";

				}

				

				foreach($qry->result() as $row){

					$data = array('task_id'=>$row->task_id,

					'status'=>$status,

					'task_date'=>date('Y-m-d'));

					

					$this->db->insert('checklist_done_notdone',$data);

					$date = strtotime("+1 day");

					$nextdate = date('Y-m-d',$date);

					$data1 = array('dateforemail'=>$nextdate);

					$this->db->where('task_id',$row->task_id);

					$this->db->update('compliance_set_date',$data1);

				}

			}

	

}



public function user_monthly_report(){

	$this->load->view('checklist/monthly_report');

	

}



public function view_userwise_monthly_report(){

	

	$data = array('user_name'=>$this->input->post('user_id'),

	'tat'=>$this->input->post('turnaroundtime'),

	'searchmonth'=>$this->input->post('month'));

	

	$this->load->view('checklist/view_userwise_monthly_report',$data);

	

}

	

				public function checklist_dashboard(){

	$this->load->view('checklist/checklist_dashboard');

	

}



public function current_week_user_mis(){

	$this->load->view('checklist/current_week_mis');

	

}



public function last_week_user_mis(){

	$this->load->view('checklist/last_week_mis');

	

}





public function current_week_mis_list()

	{

	    

		$current_monday =  date('Y-m-d', strtotime( "previous monday" ));

		$current_saturday =  date('Y-m-d', strtotime( "next saturday" ));

						

		$mis_data = array();

		

		$user_id = $this->uri->segment(3);

		$this->db->select('a.task_id, a.task_date, a.status, b.user_id, b.first_name, b.last_name, b.title, c.id, c.turnaroundtime, d.task, d.tat_id, d.status, d.added_on, d.video_link')->from('checklist_done_notdone a')->join('compliance_task_report d','a.task_id=d.task_id','left')->join('compliance_tat c','d.tat_id=c.id','left')->join('system_users b','d.user_id=b.user_id','left')->where('a.task_date BETWEEN "'. $current_monday. '" and "'.$current_saturday.'"');

		if($user_id){

			$this->db->where('d.user_id',$user_id);

		}

		$this->db->where('a.status','0');

		$query = $this->db->get();

		$res = $query->result();

		//echo "<pre>"; print_r($res); exit;

		$i=1;

		foreach($res as $row)

		{

			date_default_timezone_set("Asia/Kolkata");

			$addeddate = date('d-m-Y', strtotime($row->added_on));

			$time = date('H:i:s', strtotime($row->added_on));

			$addedtime = "<br>". date('g:i A', strtotime($time)); 

			

			$mis_data[] = array('sr_no'=>$i,

			'assigned_to'=>$row->title." ".$row->first_name." ".$row->last_name,

			'task'=>$row->task,

			'video_link'=>$row->video_link,

			'turnaroundtime'=>$row->turnaroundtime,

			'status'=>"NOT DONE",

			'missing_date'=>$row->task_date,

			'added_on'=>$addeddate.$addedtime);

			$i++;

		}

	$results = array(

	"sEcho" => 1,

	"iTotalRecords" => count($mis_data),

	"iTotalDisplayRecords" => count($mis_data),

	"aaData"=>$mis_data);

	echo json_encode($results);

}

public function last_week_mis_list()

	{

	    

		$lastmonday =  date('Y-m-d', strtotime('Monday last week'));

		$lastsaturday =  date('Y-m-d', strtotime('Saturday last week'));

						

		$mis_data = array();

		

		$user_id = $this->uri->segment(3);

		$this->db->select('a.task_id, a.task_date, a.status, b.user_id, b.first_name, b.last_name, b.title, c.id, c.turnaroundtime, d.task, d.tat_id, d.status, d.added_on, d.video_link')->from('checklist_done_notdone a')->join('compliance_task_report d','a.task_id=d.task_id','left')->join('compliance_tat c','d.tat_id=c.id','left')->join('system_users b','d.user_id=b.user_id','left')->where('a.task_date BETWEEN "'.$lastmonday. '" and "'.$lastsaturday.'"');

		if($user_id){

			$this->db->where('d.user_id',$user_id);

		}

		$this->db->where('a.status','0');

		$query = $this->db->get();

		$res = $query->result();

		//echo "<pre>"; print_r($res); exit;

		$i=1;

		foreach($res as $row)

		{

			date_default_timezone_set("Asia/Kolkata");

			$addeddate = date('d-m-Y', strtotime($row->added_on));

			$time = date('H:i:s', strtotime($row->added_on));

			$addedtime = "<br>". date('g:i A', strtotime($time)); 

			

			$mis_data[] = array('sr_no'=>$i,

			'assigned_to'=>$row->title." ".$row->first_name." ".$row->last_name,

			'task'=>$row->task,

			'video_link'=>$row->video_link,

			'turnaroundtime'=>$row->turnaroundtime,

			'status'=>"NOT DONE",

			'missing_date'=>$row->task_date,

			'added_on'=>$addeddate.$addedtime);

			$i++;

		}

	$results = array(

	"sEcho" => 1,

	"iTotalRecords" => count($mis_data),

	"iTotalDisplayRecords" => count($mis_data),

	"aaData"=>$mis_data);

	echo json_encode($results);

}

	public function view_user_checklist()



	{

		$this->load->view('checklist/view_user_checklist');



	}

	

	

	function mispercentage()

	{

		

		$this->load->view('checklist/mispercentage');

	}

	

	function misfactor()

	{

		$mis_data=array();

		$resty=$this->db->select('id,factor')->from('checklistmisfactor')->get();

		if($resty->num_rows()>0)

		{

		foreach($resty->result() as $row);

	

		$edit="<a href='".page_url."Checklist/editfactor/".$row->id."'><span class='btn btn-success'>Edit</span></a>";

			$mis_data[] = array('sr_no'=>'1',

			'script'=>$row->factor." %",

			'edit'=>$edit);

		}

		

	$results = array(

	"sEcho" => 1,

	"iTotalRecords" => count($mis_data),

	"iTotalDisplayRecords" => count($mis_data),

	"aaData"=>$mis_data);

	echo json_encode($results);

		

		

		

	}

	

	function editfactor()

	{

	

		$this->load->view('checklist/edit_mispercent');

		

	}

	

	function updatefactor()

	{

		$factor=$this->input->post('factor');

		

		$this->db->where('id',$this->uri->segment(3));

		$data=array('factor'=>$factor);

		$this->db->update('checklistmisfactor',$data);

		

		$this->session->set_flashdata('message', '<div class="alert alert-danger" style="color:#000">Record Updated</div>');

		redirect(page_url.'Checklist/mispercentage');

		

	}

	

	public function auditor_check_dashboard(){

	    $this->load->view('checklist/auditor_check_dashboard');

	}

	

	

function update_audit_remarks()

{

 $user_id =$this->session->userdata['logged_in']['user_id'];

date_default_timezone_set("Asia/Kolkata");

$lastupdatedate = date('Y-m-d');



$checkeditem=$this->input->post('remarks');

$userid= $this->input->post('userid');

 for($i=0;$i<count($checkeditem);$i++)

 {

	 $remarks=$checkeditem[$i];

	 $usrid = $userid[$i];

	 $data=array('user_id'=>$usrid,

	 'remarks'=>$remarks,

	 'rmk_date'=>date('Y-m-d'),

	 'added_by'=>$user_id,

	 'added_on'=>date('Y-m-d H:i:s'));
if(!empty($checkeditem[$i])){
	 $query = $this->db->select('id,user_id')->from('checklist_audit_reporting')->where('user_id',$usrid)->where('rmk_date',date('Y-m-d'))->get();

	 if($query->num_rows()){

	   foreach($query->result() as $row);

	   

	     $this->db->where('id',$row->id);

	     $this->db->update('checklist_audit_reporting',$data);

	 }else{

	   $this->db->insert('checklist_audit_reporting',$data);  

	 }
 }



}


$this->session->set_flashdata('message', '<div class="alert alert-danger" style="color:#000">Remark has been updated.</div>');

		redirect(page_url.'Checklist/auditor_check_dashboard');

}



public function view_pending_checklist(){

    $this->load->view('checklist/user_pending_checklist');

}



	public function userwisechecklist_task_list()

	{

	   $invoice_data = array();

		$this->db->distinct();

		$user_id = $this->uri->segment(3);

		$this->db->select('a.*, b.user_id, b.first_name, b.last_name, b.title, c.id, c.turnaroundtime')->from('compliance_task_report a')->join('compliance_tat c','a.tat_id=c.id','left')->join('system_users b','a.user_id=b.user_id','left')->where('b.user_status','1')->order_by('c.id','asc');

		if($user_id){

			$this->db->where('a.user_id',$user_id);

		}

		$this->db->where('a.status','1');

		$query = $this->db->get();

		

		$res = $query->result();

		$i=1;

		foreach($res as $row)

		{

			date_default_timezone_set("Asia/Kolkata");

			$addeddate = date('d-m-Y', strtotime($row->added_on));

			$time = date('H:i:s', strtotime($row->added_on));

			$addedtime = "&nbsp;". date('g:i A', strtotime($time)); 

			$history = "<a href='".page_url."Checklist/remark_history/".$row->task_id."'><span class='btn btn-danger btn-xs'>View History</span></a>";

			$edit = "<a href='".page_url."Checklist/edit_checklist/".$row->task_id."'><i class='fa fa-pencil'></i></a>";

			if($row->status=='1'){

			$status = "<a href='".page_url."Master/Compliance/update_compliance_task_status/".$row->task_id."/".$row->status."'><span class='btn btn-xs btn-success'>Active</span></a>";

			}else{

			$status = "<a href='".page_url."Master/Compliance/update_compliance_task_status/".$row->task_id."/".$row->status."'><span class='btn btn-xs btn-danger'>Not Active</span></a>";

			}

			$todaydate = date('Y-m-d');

			$query = $this->db->select('*')->from('compliance_set_date')->where('task_id',$row->task_id)->get();

			$res = $query->result();

			if($res){

				foreach($res as $emaildata);
					
				$nextduedate = date('d-M-Y',strtotime($emaildata->dateforemail));
				$todaysdate = date('Y-m-d');
				if($emaildata->dateforemail==$todaysdate){
					$tremark = "Todays not updated Yet.";
				}else{
					$tremark ="";
				}
				if($emaildata->dateforemail==$todaydate || $emaildata->dateforemail<$todaydate){

				        

					$update_report = "<span style='color:red;'>Not Updated Yet</span>";

				

				

				$hidden="<div id='makenewhidden".$i."'></div>";

				$html = "<div class='row'><div class='col-md-12'><div class='col-md-6'>".$update_report."</div></div></div>".$hidden;

				

				}else{

					$html ="<div class='row'><div class='col-md-12'>Reopens on <strong>".date('d-M-Y',strtotime($emaildata->dateforemail))."</strong></div></div>";

					$updateremarks="Reopens on<br> <strong>".date('d-M-Y',strtotime($emaildata->dateforemail))."</strong>";

				}

			}else{

				$nextduedate="";

			}

			

			

			$query = $this->db->select('a.id, a.task_id, a.status,a.remarkdate, b.task_id, b.tat_id, a.momfile')->from('compliance_task_progress_remarks a')->join('compliance_task_report b','a.task_id=b.task_id','left')->where('a.task_id',$row->task_id)->where('b.tat_id','6')->where('a.remarkdate',date('Y-m-d'))->limit('1')->order_by('a.id','desc')->get();

			$result = $query->result();

			if($result){

			foreach($query->result() as $progress_remarks);

			if($progress_remarks->status=='1'){

			    $current_status = "<span style='color:green; font-weight:bold;'>Done</span>";

			}elseif($progress_remarks->status=='0'){

			    $current_status = "<span style='color:red; font-weight:bold;'>Not Done</span>";

			}else{

			    $current_status = "";

			}

			

			}else{

			    $current_status = "";

			    

			}

			

			

			

			$invoice_data[] = array('sr_no'=>$i,

			'assigned_to'=>$row->title." ".$row->first_name." ".$row->last_name,

			'task'=>"<a href='".$row->video_link."'>".$row->task."</a>",

			'turnaroundtime'=>$row->turnaroundtime,

			'nextduedate'=>$nextduedate,

			'edit'=>$edit,
			'tremark'=>$tremark,

			'updateremarks'=>$html,

			'status'=>$status,

			'added_on'=>$addeddate.$addedtime);

			$i++;

		}

	$results = array(

	"sEcho" => 1,

	"iTotalRecords" => count($invoice_data),

	"iTotalDisplayRecords" => count($invoice_data),

	"aaData"=>$invoice_data);

	echo json_encode($results);

}



public function user_list(){

		echo "<option value=''>--Select User--</option>";

	$business_loc = $this->input->post('business_loc');

		$query =$this->db->select('user_id, first_name, last_name,business_location, hide_profile, user_status')->from('system_users')->where('business_location',$business_loc)->where('hide_profile','0')->where('user_status','1')->get();

		foreach($query->result() as $users)

			{

				echo "<option value=".$users->user_id.">".strtoupper($users->first_name." ".$users->last_name)."</option>";

				}

	}
	
	public function view_department_wise_checklist()

	{
		$this->load->view('checklist/department_wise_checklist');

	}
	
		public function view_department_wise_checklist_data()
	{
	    
		$invoice_data = array();
		$this->db->distinct();
		$department = $this->uri->segment(3);
		$userid = array();
		$q = $this->db->select('user_id')->from('system_users')->where('department_id',$department)->get();
		foreach($q->result() as $row){
			$userid[] = $row->user_id;
		}
		$this->db->select('a.*, b.user_id, b.first_name, b.last_name, b.title, c.id, c.turnaroundtime')->from('compliance_task_report a')->join('compliance_tat c','a.tat_id=c.id','left')->join('system_users b','a.user_id=b.user_id','left')->where('b.user_status','1')->order_by('b.first_name','asc');
		
			$this->db->where_in('a.user_id',$userid,false);
	
		$this->db->where('a.status','1');
		$query = $this->db->get();
		
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-m-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "&nbsp;". date('g:i A', strtotime($time)); 
			$history = "<a href='".page_url."Checklist/remark_history/".$row->task_id."'><span class='btn btn-danger btn-xs'>View History</span></a>";
			$edit = "<a href='".page_url."Checklist/edit_checklist/".$row->task_id."'><i class='fa fa-pencil'></i></a>";
			if($row->status=='1'){
			$status = "<a href='".page_url."Master/Compliance/update_compliance_task_status/".$row->task_id."/".$row->status."'><span class='btn btn-xs btn-success'>Active</span></a>";
			}else{
			$status = "<a href='".page_url."Master/Compliance/update_compliance_task_status/".$row->task_id."/".$row->status."'><span class='btn btn-xs btn-danger'>Not Active</span></a>";
			}
			$todaydate = date('Y-m-d');
			$query = $this->db->select('*')->from('compliance_set_date')->where('task_id',$row->task_id)->get();
			$res = $query->result();
			if($res){
				foreach($res as $emaildata);
				$nextduedate = date('d-M-Y',strtotime($emaildata->dateforemail));
				if($emaildata->dateforemail==$todaydate || $emaildata->dateforemail<$todaydate){
				
					$update_report = "<input type='radio' name='pick_items".$row->task_id."' class='dny' id='pick_items".$i."' value='0' onChange='display_qtybox(".$i.",".$row->task_id.")'>&nbsp;Not Done";
				$update_report1 = "<input type='radio' name='pick_items".$row->task_id."' class='dny' id='pick_items1".$i."' value='1' onChange='display_qtybox(".$i.",".$row->task_id.")'>&nbsp;Done";
				
				$hidden="<div id='makenewhidden".$i."'></div>";
				$html = "<div class='row'><div class='col-md-12'><div class='col-md-6'>".$update_report."</div><div class='col-md-6'>".$update_report1."</div></div></div>".$hidden;
				
				}else{
					$html ="<div class='row'><div class='col-md-12'>Reopens on <strong>".date('d-M-Y',strtotime($emaildata->dateforemail))."</strong></div></div>";
					$updateremarks="Reopens on<br> <strong>".date('d-M-Y',strtotime($emaildata->dateforemail))."</strong>";
				}
			}else{
				$nextduedate="";
			}
			
			
			$query = $this->db->select('a.id, a.task_id, a.status,a.remarkdate, b.task_id, b.tat_id, a.momfile')->from('compliance_task_progress_remarks a')->join('compliance_task_report b','a.task_id=b.task_id','left')->where('a.task_id',$row->task_id)->where('b.tat_id','6')->where('a.remarkdate',date('Y-m-d'))->limit('1')->order_by('a.id','desc')->get();
			$result = $query->result();
			if($result){
			foreach($query->result() as $progress_remarks);
			if($progress_remarks->status=='1'){
			    $current_status = "<span style='color:green; font-weight:bold;'>Done</span>";
			}elseif($progress_remarks->status=='0'){
			    $current_status = "<span style='color:red; font-weight:bold;'>Not Done</span>";
			}else{
			    $current_status = "";
			}
			
			}else{
			    $current_status = "";
			    
			}
			
			
			
			$invoice_data[] = array('sr_no'=>$i,
			'assigned_to'=>$row->title." ".$row->first_name." ".$row->last_name,
			'task'=>"<a href='".$row->video_link."'>".$row->task."</a>",
			'turnaroundtime'=>$row->turnaroundtime,
			'nextduedate'=>$nextduedate,
			'edit'=>$edit,
			'updateremarks'=>$html,
			'status'=>$status,
			'added_on'=>$addeddate.$addedtime);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($invoice_data),
	"iTotalDisplayRecords" => count($invoice_data),
	"aaData"=>$invoice_data);
	echo json_encode($results);
}

function viewdonereport(){
	$this->load->view('checklist/viewdonereport');
}

function setwhatsappreminder(){
	
	$id  =$this->input->post('checklist_id');
	$permission  =$this->input->post('permission');
	$data = array('whatsapp_notification'=>$permission);
	$this->db->where('task_id',$id);
	$res = $this->db->update('compliance_task_report',$data);
	if($res){
		echo "Permission Assigned."; exit;
	}
	
}

public function setfrequency(){
	 /*for TWICE A DAY*/
			
		   $q = $this->db->select('task_id,id,first_time_status, second_time_status')->from('checklist_frequency_management')->where('due_date',date('Y-m-d'))->get();
		   if($q->num_rows()>0){
			  
			   foreach($q->result() as $checkdata){
			   $data22 = array('first_time_status'=>0,
			   'second_time_status'=>0,
			   'third_time_status'=>0);
			   $this->db->where('id',$checkdata->id);
			   $this->db->update('checklist_frequency_management',$data22);
			   
			   
			$todaysdate = date('Y-m-d');
			$date = new DateTime($todaysdate);
			$date->add(new DateInterval('P0D')); 
			$nextdate = $date->format('Y-m-d');
			//echo $nextdate; exit;
			/* CHECK HOLIDAY*/

			$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();

			if($query->num_rows()>0){

				date('Y-m-d', strtotime("+1 day", strtotime($nextdate)));

			}else{

				$nextdate = $nextdate;

			}
			//echo $nextdate; exit;

			/* CHECK HOLIDAY*/ 
			
	$query = $this->db->select('*')->from('compliance_set_date')->where('task_id',$checkdata->task_id)->get();
	$res = $query->result();

	if($query->num_rows()>0){

		foreach($res as $updatedata)

		$data = array('dateforemail'=>$nextdate,'last_update_date'=>date('Y-m-d'));

		$this->db->where('id',$updatedata->id);

		$this->db->update('compliance_set_date',$data);
		
		$data22 = array('due_date'=>$nextdate);
		$this->db->where('id',$checkdata->id);
		$this->db->update('checklist_frequency_management',$data22);
		

	}else{

		$data = array('dateforemail'=>$nextdate,'task_id'=>$taskid,'last_update_date'=>date('Y-m-d'));

		$this->db->insert('compliance_set_date',$data);
		$data22 = array('due_date'=>$nextdate);
		$this->db->where('id',$checkdata->id);
		$this->db->update('checklist_frequency_management',$data22);

	}	
			   
	}
}
		   
}

public function checklist_yesterday_report_list()

	{

		 $date = date('Y-m-d')." 00-00-00";

	$todays = date('Y-m-d');

$d2 = date('Y-m-d', strtotime('-1 days'));

$seconddate = $d2." 00-00-00";

		$invoice_data = array();

		$this->db->distinct();

		$taskid = $this->uri->segment(3);

		$query = $this->db->select('a.task_id, a.status,a.remarkdate,a.added_by,a.added_on,a.evidence_attachment,b.task, b.task_id,c.first_name, c.last_name')->from('compliance_task_progress_remarks a')->join('compliance_task_report b','a.task_id=b.task_id','left')->join('system_users c','a.added_by=c.user_id','left')->where('a.remarkdate',$d2)->get();

		$res = $query->result();

		$i=1;

		foreach($res as $row)

		{

			date_default_timezone_set("Asia/Kolkata");

			$addeddate = date('d-m-Y', strtotime($row->added_on));

			$time = date('H:i:s', strtotime($row->added_on));

			$addedtime = "<br>". date('g:i A', strtotime($time)); 

			

			$addedbyuser = $row->first_name." ".$row->last_name;

			//$edit = "<a href='".page_url."Master/Compliance/edit_compliance_task/".$row->task_id."'><i class='fa fa-pencil'></i></a>";

			if($row->status=='1'){

			$status = "<span class='btn btn-xs btn-success'><i class='fa fa-check'></i> Done</span>";

			}else{

			$status = "<span class='btn btn-xs btn-danger'><i class='fa fa-close'></i> Not Done</span>";

			}

			$evidence="<img src='".evidence_image.$row->evidence_attachment."'style='width: 25%;'>";

			$evidence1="<a href='".evidence_image.$row->evidence_attachment."'style='width: 25%;' target='_blank'><span class='btn btn-primary btn-xs'><i class='fa fa-eye'></i> View</span></a>";

			$dateformat=date('d-M-Y',strtotime($row->remarkdate));

			

			$invoice_data[] = array('sr_no'=>$i,

			'assigned_to'=>$addedbyuser,

			'task'=>$row->task,

			'status'=>$status,

			'evidence'=>$evidence1,
			'date'=>$dateformat);

			$i++;

		}

	$results = array(

	"sEcho" => 1,

	"iTotalRecords" => count($invoice_data),

	"iTotalDisplayRecords" => count($invoice_data),

	"aaData"=>$invoice_data);

	echo json_encode($results);

}

public function checklist_yesterday_report()
{
  $this->load->view('checklist/checklist_yesterday_report');
}
public function filtertask()
	{
		$from_date=date('Y-m-d',strtotime($this->input->post('from_date')));
		$to_date=date('Y-m-d',strtotime($this->input->post('to_date')));
		$user=$this->input->post('user');

		$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you,  Record Fetched Successfully.</span></div><br/>');
				redirect(page_url.'Checklist/checklist_report_filtered_data/'.$from_date.'/'.$to_date.'/'.$user);

	}

	public function checklist_report_filtered_data()
	{
		$this->load->view('checklist/checklist_filtered_data');

	}


	public function checklist_report_filtered_list()

	{

		if($this->uri->segment(3)<>''){
	    $from_date=date('Y-m-d',strtotime($this->uri->segment(3)));
		$to_date=date('Y-m-d',strtotime($this->uri->segment(4)));
		$user=$this->uri->segment(5);
	}
		else{
			$from_date='';
			$to_date='';
		}

		$invoice_data = array();

		$this->db->distinct();

		$taskid = $this->uri->segment(3);

		$this->db->select('a.task_id, a.status,a.remarkdate,a.added_by,a.added_on,a.evidence_attachment,b.task, b.task_id,c.first_name, c.last_name')->from('compliance_task_progress_remarks a')->join('compliance_task_report b','a.task_id=b.task_id','left')->join('system_users c','a.added_by=c.user_id','left');
		if ($from_date!='') {
		$this->db->where('a.remarkdate >=', $from_date)->where('a.remarkdate <=', $to_date);
		$this->db->where('c.user_id',$user);
}
 $query=$this->db->order_by('id','desc')->get();

		$res = $query->result();

		$i=1;

		foreach($res as $row)

		{

			date_default_timezone_set("Asia/Kolkata");

			$addeddate = date('d-m-Y', strtotime($row->added_on));

			$time = date('H:i:s', strtotime($row->added_on));

			$addedtime = "<br>". date('g:i A', strtotime($time)); 

			

			$addedbyuser = $row->first_name." ".$row->last_name;

			//$edit = "<a href='".page_url."Master/Compliance/edit_compliance_task/".$row->task_id."'><i class='fa fa-pencil'></i></a>";

			if($row->status=='1'){

			$status = "<span class='btn btn-xs btn-success'><i class='fa fa-check'></i> Done</span>";

			}else{

			$status = "<span class='btn btn-xs btn-danger'><i class='fa fa-close'></i> Not Done</span>";

			}

			$evidence="<img src='".evidence_image.$row->evidence_attachment."'style='width: 25%;'>";

			$evidence1="<a href='".evidence_image.$row->evidence_attachment."'style='width: 25%;' target='_blank'><span class='btn btn-primary btn-xs'><i class='fa fa-eye'></i> View</span></a>";

			$dateformat=date('d-M-Y',strtotime($row->remarkdate));

			

			

			$invoice_data[] = array('sr_no'=>$i,

			'assigned_to'=>$addedbyuser,

			'task'=>$row->task,

			'status'=>$status,

			'evidence'=>$evidence1,
			'date'=>$dateformat);

			$i++;

		}

	$results = array(

	"sEcho" => 1,

	"iTotalRecords" => count($invoice_data),

	"iTotalDisplayRecords" => count($invoice_data),

	"aaData"=>$invoice_data);

	echo json_encode($results);

}

public function checklist_master(){
	$this->load->view('dashboard/checklist_master');
}
	
}

