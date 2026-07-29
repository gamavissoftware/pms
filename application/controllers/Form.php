<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Form extends CI_Controller {
	
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

		$this->email->set_newline("\r\n");  
		$this->load->library('email', $config);
		
		
	}
	
	public function create_new_form(){
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('department', 'department', 'required|trim');
		$this->form_validation->set_rules('form_title', 'form_title', 'required|trim');
		$this->form_validation->set_rules('dashboard_title', 'dashboard_title', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('form/design_new_form');
			}else
		{
			
		date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		  
		  if($this->input->post('tatappl')<>'')
		  {
		      $tatt=1;
		  }else
		  {
		      $tatt=0;
		  }
		  
		 
		   $data=
			array('form_title'=>strtoupper($this->input->post('form_title')),
			'alias'=>strtoupper($this->input->post('formcode')),
			'department_id'=>strtoupper($this->input->post('department')),
			'user_id'=>strtoupper($this->input->post('user_id')),
			'description'=>strtoupper($this->input->post('description')),
			'action_to_be_taken'=>strtoupper($this->input->post('action_to_be_taken')),
			'dashboard_title'=>strtoupper($this->input->post('dashboard_title')),
		    'form_type'=>$this->input->post('form_type'),
		     'tatappl'=>$tatt,
		    'ref_no'=>$this->input->post('ref_applicable'),
			'status'=>'0',
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			
			
			$res = $this->db->insert('dynamic_forms',$data);
			$last_id = $this->db->insert_id();
			$data1 = array('moduleid'=>'2',
			'submodule'=>strtoupper($this->input->post('form_title')),
			'status'=>'1',
			'addedOn'=>$added_time);
			$this->db->insert('submodule',$data1);
			
			if($res)
			{
				if(isset($_REQUEST['dashboard_shown_to'])){	
				$tags2=count($_REQUEST['dashboard_shown_to']);
				if($tags2>0)
					{
					$dashboard_shown_to=$_REQUEST['dashboard_shown_to'];
					for($y=0;$y<$tags2;$y++){
						
					if($dashboard_shown_to[$y]!='')
						{
							
							$data=array('form_id'=>$last_id,
							'user_id'=>$dashboard_shown_to[$y],
							'added_on'=>$added_time,
							'added_by'=>$user_id);
							$this->db->insert('dynamic_form_dashboard_access',$data);
						}
					}
					}
				}
				
				
				if(isset($_REQUEST['input_type'])){	
					$tags1=count($_REQUEST['input_type']);
					if($tags1>0)
					{
					$input_type=$_REQUEST['input_type'];
					$fieldtype = $this->input->post('fieldtype');
					for($x=0;$x<$tags1;$x++){
						
					if($input_type[$x]!='')
						{
							$fieldtype = $_REQUEST['fieldtype'][$x];
							$name1= preg_replace('/\s+/', '', $input_type[$x]);
							$name = strtolower($name1);
							if($fieldtype=='1'){
								$design = '<input type="text" class="form-control" name="'.$name.'" id="'.$name.'" value="">';
							}else if($fieldtype=='2'){
								$design = '<textarea class="form-control" name="'.$name.'" id="'.$name.'"></textarea>';
							}else if($fieldtype=='3'){
								$design = '<select class="form-control" name="'.$name.'" id="'.$name.'"></select>';
							}else if($fieldtype=='4'){
								$design = '<input type="checkbox" name="'.$name.'[]" id="'.$name.'" value="">';
							}else if($fieldtype=='5'){
								$design = '<input class="form-control" type="file" name="'.$name.'" id="'.$name.'" value="">';
							}else if($fieldtype=='6'){
								$design = '<input class="form-control" type="date" name="'.$name.'" id="'.$name.'" value="">';
							}else if($fieldtype=='7'){
								$design = '<input class="form-control" type="time" name="'.$name.'" id="'.$name.'" value="">';
							}else if($fieldtype=='8'){
								$design = '<input class="form-control" type="number" name="'.$name.'" id="'.$name.'" value="">';
							}else if($fieldtype=='9'){
								$design = '<input type="radio" name="'.$name.'" id="'.$name.'" value="">';
							}else{
								$design="";
							}
							
							
							$data=array('field_design'=>$design,
							'form_id'=>$last_id,
							'field_type'=>$fieldtype,
							'form_label'=>$input_type[$x],
							'added_on'=>$added_time,
							'added_by'=>$user_id);
							$this->db->insert('master_dynamic_fields',$data);
						}
					}
					}
					}
				
				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
				redirect(page_url.'Form/create_new_form');
				}
		  
			
			}
	}

function pending_for_review(){
	$this->load->view('form/pending_forms');
}

public function pending_forms_list()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$scheduler_data = array();
		$this->db->select('a.department_id,a.form_running_status,a.user_id, a.dashboard_title, a.form_title, a.id, a.description, a.status,a.added_on, a.added_by, b.department_id, b.department, c.user_id, c.first_name, c.last_name, c.title, d.first_name as responsible_person, d.last_name as responsible_person_lname')->from('dynamic_forms a')->join('departments b','a.department_id=b.department_id','left')->join('system_users c','a.added_by=c.user_id','left')->join('system_users d','a.user_id=d.user_id','left');
		$this->db->where('a.status',0);
		$query = $this->db->order_by('a.added_on','desc')->get();
		$res = $query->result();
		
		$i=1;
		foreach($res as $row)
		{
			$assignedto = array();
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			$action = "<a href='".page_url."Form/setting/".$row->id."'><span class='btn btn-success btn-xs'>Pending for Review</span></a>";
			
			$status = $row->status;
			if($status=='0')
			{
				$sta =  "<a href='".page_url."Form/update_house_keeping_item_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Mark as Not Active</span></a>";
			}else
			{
				
			}
			
			$backgroundcolor = "yellow";
			$html = "<table border='1' style='width:300px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center'>ASSIGNED TO</th></tr>";
			$query = $this->db->select('a.user_id, b.first_name, b.last_name')->from('dynamic_form_dashboard_access a')->join('system_users b','a.user_id=b.user_id','left')->where('a.form_id',$row->id)->get();
			if($query->num_rows()>0){
				foreach($query->result() as $assignedto){
					$html.="<tr style='".$backgroundcolor."'>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($assignedto->first_name." ".$assignedto->last_name)."</td>";
				$html.="</tr>";
					
				}
			}else{
				$html.= "";
			}
			$html.="</table>";
			
			
			
			$scheduler_data[] = array('sr_no'=>$i,
			'department'=>strtoupper($row->department),
			'responsible'=>strtoupper($row->responsible_person)." ".strtoupper($row->responsible_person_lname),
			'form_title'=>strtoupper($row->form_title),
			'description'=>strtoupper($row->description),
			'dashboard_title'=>strtoupper($row->dashboard_title),
			'dashboard_assigned_to'=>$html,
			'added_on'=>$addeddate.$addedtime,
			'added_by'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'action'=>$action);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}

public function form_mark_as_not_active()
	{
		$identifier =  $this->uri->segment(3);
		$field_name = "id";
		$table = "dynamic_forms";
     	$status = "1";
			$data = array('form_running_status'=>$status);
			$this->db->where('id',$identifier);
			$res = $this->db->update($table,$data);
			
			$q=$this->db->select('form_title')->from('dynamic_forms')->where('id',$identifier)->get();
			$res = $q->result();
			foreach($res as $row);
			$datas = array('status'=>'0');
			$this->db->where('submodule',$row->form_title);
			$this->db->update('submodule',$datas);
			
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Status successfully updated.</div>');
			redirect(page_url.'Form/all_forms');
		}

function setting(){
	$this->load->view('form/setting');
}

public function form_marking_as_approve(){
		
		$user_id =$this->session->userdata['logged_in']['user_id'];
			
		date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   
		   $tat = $this->input->post('tat');
		   $tattime = $this->input->post('tattime');
		   
		   $data=
			array('status'=>'1',
			'approved_by'=>$user_id,
			'tatdays'=>$tat,
			'tattime'=>$tattime,
			'approved_on'=>$added_time);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update('dynamic_forms',$data);
			$last_id = $this->uri->segment(3);
			if($res)
			{
				
				if(isset($_REQUEST['input_type'])){	
					$tags1=count($_REQUEST['input_type']);
					if($tags1>0)
					{
					$input_type=$_REQUEST['input_type'];
					$labelname=$_REQUEST['labelname'];
					$fieldtype = $this->input->post('fieldtype');
					for($x=0;$x<$tags1;$x++){
						
					if($input_type[$x]!='')
						{
							$recordid = $_REQUEST['recordid'][$x];
							$fieldtype = $_REQUEST['fieldtype'][$x];
							$required = $_REQUEST['isrequired'][$x];
							if($required=='1'){
								$rqd = "required";
							}else{
								$rqd = "";
							}
							$name1= preg_replace('/\s+/', '', $labelname[$x]);
							$name = strtolower($name1);
							if($fieldtype=='1'){
								$design = '<input type="text" class="form-control" name="'.$name.'" id="'.$name.'" value="" '.$rqd.'>';
							}else if($fieldtype=='2'){
								$design = '<textarea class="form-control" name="'.$name.'" id="'.$name.'" '.$rqd.'></textarea>';
							}else if($fieldtype=='3'){
								$option="";
								$optiondata = $_REQUEST['optiondata'.$recordid];
								$value = explode(',',$optiondata);
								for($m=0; $m<count($value); $m++){
									$option.='<option value="'.strtoupper($value[$m]).'">'.strtoupper($value[$m]).'</option>';
								}
								
								$design = '<select class="form-control" name="'.$name.'" id="'.$name.'" '.$rqd.'>'.$option.'</select>';
								
							}else if($fieldtype=='4'){
								$option="";
								$optiondata = $_REQUEST['optiondata'.$recordid];
								$value = explode(',',$optiondata);
								for($m=0; $m<count($value); $m++){
									$option.='<input type="checkbox" name="'.$name.'[]" id="'.$name.'" value="'.strtoupper($value[$m]).'" '.$rqd.'> &nbsp; '.strtoupper($value[$m])."<br>";
								}
								
								$design = $option;
								//echo $design; exit;
							}else if($fieldtype=='5'){
								$design = '<input class="form-control" type="file" name="'.$name.'" id="'.$name.'" value="" '.$rqd.'>';
							}else if($fieldtype=='6'){
								$design = '<input class="form-control" type="date" name="'.$name.'" id="'.$name.'" value="" '.$rqd.'>';
							}else if($fieldtype=='7'){
								$design = '<input class="form-control" type="time" name="'.$name.'" id="'.$name.'" value="" '.$rqd.'>';
							}else if($fieldtype=='8'){
								$min = $_REQUEST['min'.$recordid];
								$max = $_REQUEST['max'.$recordid];
								
								$design = '<input class="form-control" type="number" name="'.$name.'" id="'.$name.'" value="" minlength="'.$min.'" maxlength="'.$max.'" '.$rqd.'>';
							}else if($fieldtype=='9'){
								$option="";
								$optiondata = $_REQUEST['optiondata'.$recordid];
								$value = explode(',',$optiondata);
								for($m=0; $m<count($value); $m++){
									$option.='<input type="radio" name="'.$name.'" id="'.$name.'" value="'.strtoupper($value[$m]).'" '.$rqd.'> &nbsp; '.strtoupper($value[$m])."<br>";
								}
								
								$design = $option;
								
							}else{
								$design="";
							}
							
							
							$data=array('field_design'=>$design,
							'field_required'=>$required);
							
							$this->db->where('field_id',$recordid);
							$this->db->update('master_dynamic_fields',$data);
						}
					}
					}
					}
				
				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
				redirect(page_url.'Form/create_new_form');
				}
		  
			
		
	}

public function view(){
	$this->load->view('form/dynamic_form');
}

function getdata(){
	$formdata= array();
	$data = $this->input->post('formfield');
	$field_type = $this->input->post('field_type');
		$tatappl=$this->input->post('tatappl');
	
	for($i=0; $i<count($data); $i++){
	if($field_type[$i]=='4'){
		$checkboxval = strtoupper($this->input->post($data[$i]));
		$finaldata = implode(',',$checkboxval);
	}else if($field_type[$i]=='5')
	{
	    $finaldata="";
		$photo=$_FILES[$data[$i]]['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$finaldata=time().'.'.$cat_image;
				move_uploaded_file($_FILES[$data[$i]]["tmp_name"],UPLOADPATH.'dynamicfile/' . $finaldata);
			}else
			{
				$finaldata="";
				}	
	}else if($field_type[$i]=='7'){
		$time = $this->input->post($data[$i]);
		$finaldata= date('H:i:s',strtotime($time));
	}
	else{
		$finaldata = strtoupper($this->input->post($data[$i]));
	}	
	$formdata[$data[$i]] = $finaldata; 
		
	}
	
	$user_id =$this->session->userdata['logged_in']['user_id'];
	date_default_timezone_set("Asia/Kolkata");
    $added_time = date('Y-m-d H:i:s');
	$query = $this->db->select('tatdays, tattime')->from('dynamic_forms')->where('id',$this->uri->segment(3))->get();
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
	
		if($tatappl==1)
	{
	    	$finaldate =  $finaldate; 
	}else
	{
	    	$finaldate = '';
	}
	
	$query11 = $this->db->select('form_type,alias')->from('dynamic_forms')->where('id',$this->uri->segment(3))->get();
	foreach($query11->result() as $forminfo);
	if($forminfo->form_type=='1'){
		$completelydone = "0";
	}else{
		$completelydone = "1";
	}
	
	
	
	$resty=$this->db->select('id')->from('dynamic_form_data')->where('form_id',$this->uri->segment(3))->get();
	$prevdata=$resty->num_rows();
	if($prevdata==0)
	{
		$code=1;
       $num_padded = sprintf("%03d", $code);
	   
	}else{
		$code=$prevdata+1;
		$num_padded = sprintf("%03d", $code);
	}
	
	$respid=$forminfo->alias.$num_padded;
	
	$response = json_encode($formdata);
//	echo "<pre>"; print_r($response); exit;
	$data = array('form_id'=>$this->uri->segment(3),
		'responseid'=>$respid,
	'formdata'=>$response,
	'added_by'=>$user_id,
	'planned_date'=>$finaldate,
	'completely_done'=>$completelydone,			  
	'added_on'=>$added_time);
	    $this->db->insert('dynamic_form_data',$data);
	$last_id = $this->db->insert_id();
	$formid = $this->uri->segment(3);
	
	$markasdone = "<a href='".page_url."Form/task_marked_as_done/".$last_id."/".$formid."'><span class='btn btn-success btn-xs'>MARK AS DONE</span></a>";
	$data = array('record_id'=>$last_id,
	'mark_as_done'=>$markasdone);
	$this->db->insert('dynamic_form_data_mark_done',$data);
	$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
	redirect(page_url.'Form/view/'.$this->uri->segment(3));
}

function data_report(){
	$this->load->view('form/form_data');
}

function user_data_report(){
	$this->load->view('form/userwise_form_data');
}
function view_history(){
	$this->load->view('form/view_history');
}


public function task_marked_as_done()

	{
			$formid = $this->uri->segment(4);
			$user_id =$this->session->userdata['logged_in']['user_id'];
			date_default_timezone_set("Asia/Kolkata");
			$added_time = date('Y-m-d H:i:s');
			$identifier =  $this->uri->segment(3);
			$data= array('work_status'=>1,
			'task_complition_time'=>$added_time,
			'completed_by'=>$user_id);
			$this->db->where('id',$identifier);
			$this->db->update('dynamic_form_data',$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success" style="color:#000">Status successfully updated.</div>');
			redirect(page_url.'Form/data_report/'.$formid);

		}

function master_index(){
	$this->load->view('form/master_index');
}
public function user_list(){
		echo "<option value=''>--Select User--</option>";
	$department = $this->input->post('department');
		$query =$this->db->select('user_id, first_name, last_name,business_location, hide_profile, user_status')->from('system_users')->where('department_id',$department)->where('hide_profile','0')->where('user_status','1')->get();
		foreach($query->result() as $users)
			{
				echo "<option value=".$users->user_id.">".strtoupper($users->first_name." ".$users->last_name)."</option>";
				}
	}
	
	function edit_form(){
	$this->load->view('form/edit_form');
}

public function update_form_detail(){
		
		$user_id =$this->session->userdata['logged_in']['user_id'];
			
		date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   
		   $tat = $this->input->post('tat');
		   $tattime = $this->input->post('tattime');
		   $form_title = $this->input->post('form_title');
		   $form_description = $this->input->post('form_description');
		   $data=
			array('form_title'=>$form_title,
			'description'=>$form_description,
			'ref_no'=>$this->input->post('ref_applicable'),
			'alias'=>strtoupper(trim($this->input->post('formcode'))),
			'form_video_link'=>trim($this->input->post('form_video_link')),
			'dashboard_video_link'=>trim($this->input->post('dashboard_video_link')),
			'action_to_be_taken'=>$this->input->post('action_to_be_taken'),
			'tatdays'=>$tat,
		     'form_type'=>$this->input->post('form_type'),
			'tattime'=>$tattime);
			$oldformtitle = $this->input->post('old_form_title');
			$olddata = array('submodule'=>$form_title);
			$this->db->where('submodule',$oldformtitle);
			$this->db->update('submodule',$olddata);
			
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update('dynamic_forms',$data);
			$last_id = $this->uri->segment(3);
			if($res)
			{
				
				if(isset($_REQUEST['dashboard_shown_to'])){	
				$tags2=count($_REQUEST['dashboard_shown_to']);
				if($tags2>0)
					{
					$dashboard_shown_to=$_REQUEST['dashboard_shown_to'];
					for($y=0;$y<$tags2;$y++){
						
					if($dashboard_shown_to[$y]!='')
						{
							
							$data=array('form_id'=>$last_id,
							'user_id'=>$dashboard_shown_to[$y],
							'added_on'=>$added_time,
							'added_by'=>$user_id);
							$this->db->insert('dynamic_form_dashboard_access',$data);
						}
					}
					}
				}
				
				if(isset($_REQUEST['input_type'])){	
					$tags1=count($_REQUEST['input_type']);
					if($tags1>0)
					{
					$input_type=$_REQUEST['input_type'];
					$labelname=$_REQUEST['labelname'];
					$fieldtype = $this->input->post('fieldtype');
					for($x=0;$x<$tags1;$x++){
						
					if($input_type[$x]!='')
						{
							$recordid = $_REQUEST['recordid'][$x];
							$fieldtype = $_REQUEST['fieldtype'][$x];
							$required = $_REQUEST['isrequired'][$x];
							if($required=='1'){
								$rqd = "required";
								
								}else{
								$rqd = "";
								}
							$defaultrequired = $_REQUEST['defaultrequired'][$x];
							$name1= preg_replace('/\s+/', '', $labelname[$x]);
							$name = strtolower($name1);
							if($fieldtype=='1'){
								$design = '<input type="text" class="form-control" name="'.$name.'" id="'.$name.'" value="" '.$rqd.'>';
							}else if($fieldtype=='2'){
								$design = '<textarea class="form-control" name="'.$name.'" id="'.$name.'" '.$rqd.'></textarea>';
							}else if($fieldtype=='3'){
								$option="";
								$optiondata = $_REQUEST['optiondata'.$recordid];
								$value = explode(',',$optiondata);
								for($m=0; $m<count($value); $m++){
									$option.='<option value="'.strtoupper($value[$m]).'">'.strtoupper($value[$m]).'</option>';
								}
								$query = $this->db->select('form_id,field_design')->from('master_dynamic_fields')->where('form_id',$last_id)->where('field_type','3')->where('field_id',$recordid)->get();
								
								foreach($query->result() as $selectval);
								$design = $selectval->field_design;
								if($optiondata!=''){
									
								$lastvalue = $selectval->field_design;
								
								$optiondata1 = explode('</select>',$lastvalue);
								$previousselectoption  = $optiondata1[0];
								
								if($defaultrequired!=$required){
								if($required=='0'){
									$rqdcheck = str_replace('required','',$previousselectoption);
									
								}else{
									$rqdcheck = substr_replace($previousselectoption,' required ',8,0);
								}
								}else{
									$rqdcheck = $previousselectoption;
								}
								$previousselectoptionendtag  = $optiondata1[1];				
								$design = $rqdcheck.$option."</select>";
								
								}else{
								   $design = $design; 
								}
								
								
								
								//echo $design; exit;
							}else if($fieldtype=='4'){
								$optionss="";
								$optiondata = $_REQUEST['optiondata'.$recordid];
								$value = explode(',',$optiondata);
								for($m=0; $m<count($value); $m++){
									$optionss.='<input type="checkbox" name="'.$name.'[]" id="'.$name.'" value="'.strtoupper($value[$m]).'" '.$rqd.'> &nbsp; '.strtoupper($value[$m])."<br>";
								}
								//echo $optionss; exit;
								if($optiondata!=''){
								
								$query = $this->db->select('form_id,field_design')->from('master_dynamic_fields')->where('form_id',$last_id)->where('field_type','4')->where('field_id',$recordid)->get();
								foreach($query->result() as $selectval);
								$lastvalue = $selectval->field_design;
											
								$design = $lastvalue."&nbsp".$optionss;
								}else{
								   $design = $optionss; 
								}
								
								
								
								//echo $design; exit;
								
							}else if($fieldtype=='5'){
								$design = '<input class="form-control" type="file" name="'.$name.'" id="'.$name.'" value="" '.$rqd.'>';
							}else if($fieldtype=='6'){
								$design = '<input class="form-control" type="date" name="'.$name.'" id="'.$name.'" value="" '.$rqd.'>';
							}else if($fieldtype=='7'){
								$design = '<input class="form-control" type="time" name="'.$name.'" id="'.$name.'" value="" '.$rqd.'>';
							}else if($fieldtype=='8'){
								$min = $_REQUEST['min'.$recordid];
								$max = $_REQUEST['max'.$recordid];
								
								$design = '<input class="form-control" type="number" name="'.$name.'" id="'.$name.'" value="" minlength="'.$min.'" maxlength="'.$max.'" '.$rqd.'>';
							}else if($fieldtype=='9'){
								$option="";
								$optiondata = $_REQUEST['optiondata'.$recordid];
								$value = explode(',',$optiondata);
								for($m=0; $m<count($value); $m++){
									$option.='<input type="radio" name="'.$name.'" id="'.$name.'" value="'.strtoupper($value[$m]).'" '.$rqd.'> &nbsp; '.strtoupper($value[$m])."<br>";
								}
								if($optiondata!=''){
								   	$query = $this->db->select('form_id,field_design')->from('master_dynamic_fields')->where('form_id',$last_id)->where('field_type','9')->where('field_id',$recordid)->get();
								foreach($query->result() as $radiodata);
								$lastvalue = $radiodata->field_design; 
								$design = $lastvalue."&nbsp".$option;
								}else{
								 	$design = $option;   
								}
								
								}else{
								$design="";
							}
							
							
							$data=array('field_design'=>$design,
							'field_required'=>$required);
							
							//echo "<pre>"; print_r($data); 
							$this->db->where('field_id',$recordid);
							$this->db->update('master_dynamic_fields',$data);
						}
						
					}
					
				
					
					}
					}
				
				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
				redirect(page_url.'Form/master_index');
				}
		  
			
		
	}
	public function get_sub_ref(){
		echo "<option value=''>--Select Sub Reference--</option>";
	$ref = $this->input->post('ref');
		$query =$this->db->select('sub_ref_id,sub_ref_title')->from('department_sub_reference')->where('ref_id',$ref)->where('status','1')->get();
		foreach($query->result() as $subref)
			{
				echo "<option value=".$subref->sub_ref_id.">".strtoupper($subref->sub_ref_title)."</option>";
				}
	}
	
	
		public function get_sub_sub_ref(){
	
	$ref = $this->input->post('subref');
		$query =$this->db->select('sub_subref_id,sub_ref_title')->from('department_sub_sub_reference')->where('sub_ref_id',$ref)->where('status','1')->get();
		if($query->num_rows()>0)
		{
		echo "<option value=''>--Select Sub Sub Reference--</option>";
		foreach($query->result() as $subref)
			{
				echo "<option value=".$subref->sub_subref_id.">".strtoupper($subref->sub_ref_title)."</option>";
			}
		}else
		{
		    echo "NA";
		}
	}
	
	public function fetch_ref_doc(){
		$refid = $this->uri->segment(3);
		$sub_refid = $this->uri->segment(4);
		
		$subsub_refid = $this->uri->segment(5);
		
		if($subsub_refid=='')
		{
		$q = $this->db->select('attachment, video_link')->from('department_sub_reference')->where('sub_ref_id',$sub_refid)->where('ref_id',$refid)->get();
		foreach($q->result() as $row);
		if($row->video_link<>''){
		    echo "<script>
    window.location = '".$row->video_link."';
</script>";
		}else{
		$attachment = referencefilepath.$row->attachment;
		echo "<script>
    window.location = '".$attachment."';
</script>";
}
}else
{
    
    	$q = $this->db->select('attachment')->from('department_sub_sub_reference')->where('sub_subref_id',$subsub_refid)->where('ref_id',$refid)->where('sub_ref_id',$sub_refid)->get();
		foreach($q->result() as $row);
		$attachment = referencefilepath.$row->attachment;
		echo "<script>
    window.location = '".$attachment."';
</script>";
    
}
		
	}
	
	public function filter_user_checklist(){
		$departmentid = $this->input->post('departmentid');
		$user_id = $this->input->post('checklist_user'.$departmentid);
		redirect(page_url.'Checklist/view_user_checklist/'.$user_id);
		
	}
	
	public function delete_member(){
	$id = $this->uri->segment(3);
	$this->db->where('id',$id);
	$res = $this->db->delete('dynamic_form_dashboard_access');
	if($res){
		$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Record successfully deleted.</span></div><br/>');
				redirect(page_url.'Form/edit_form/'.$this->uri->segment(4));
	}
}
	public function update_solution_resolution_remarks(){
	$user_id =$this->session->userdata['logged_in']['user_id'];
	date_default_timezone_set("Asia/Kolkata");
	$remarks = $this->input->post('remarks');
	$data = array('form_id'=>$this->uri->segment(4),
	'record_id'=>$this->uri->segment(3),
	'remarks'=>$remarks,
	'added_by'=>$user_id,
	'added_on'=>date('Y-m-d H:i:s'));
	
	$this->db->insert('dynamic_form_solution_resolution_remarks',$data);
	$data2 = array('work_status'=>'0');
	$this->db->where('id',$this->uri->segment(3));
	$res = $this->db->update('dynamic_form_data',$data2);
	if($res){
		$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Record successfully added.</span></div><br/>');
				redirect(page_url.'Form/view/'.$this->uri->segment(4));
	}
	
}


public function mark_as_resolved(){
	
	$data = array('completely_done'=>'0');
	$this->db->where('id',$this->uri->segment(3));
	$res = $this->db->update('dynamic_form_data',$data);
	if($res){
		$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Record successfully added.</span></div><br/>');
				redirect(page_url.'Form/view/'.$this->uri->segment(4));
	}
	
}
public function stand_alone_dashboard_permission(){
	$this->load->view('master/standalone_dashboard_access');
}

public function stand_alone_dashboard_list()
	{
		$business_data = array();
		$this->db->select('a.*, b.department_id, b.department')->from('standalone_dashboard_access a');
		$this->db->join('departments b','a.department_id=b.department_id','left');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
				$edit = "<a href='".page_url."Form/edit_standalone_dashboard/".$row->id."'><i class='fa fa-pencil'></i></a>";
				
				$formvideo = "<a href='".$row->form_vide_link."' target='_blank'><span class='btn btn-success btn-xs'>View Form Video</span></a>";
				$dashboardvideo = "<a href='".$row->dashboard_video_link."' target='_blank'><span class='btn btn-success btn-xs'>View dashboard Video</span></a>";
			
			$html = "<table border='1' style='width:200px;'><tr style='background-color:yellow;'><th style='padding:2px 2px 2px 2px'>SR NO.</th><th style='padding:2px 2px 2px 2px'>USER NAME</th></tr>";
			$instrumentsss = array();
			$a=1;
			$query1 = $this->db->select('a.user_id, b.first_name, b.last_name')->from('standalone_dashboard_userwise_permission a')->join('system_users b','a.user_id=b.user_id','left')->where('a.dashboard_id',$row->id)->get();
			foreach($query1->result() as $userinfo){
				
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".$a."</td>";
			
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($userinfo->first_name." ".$userinfo->last_name)."</td>";
				$html.="</tr>";
			
			    $a++;
			}
			$html.="</table>";
			
				
			$business_data[] = array('sr_no'=>$i,
			'form_title'=>$row->form_title,
			'form_video'=>$formvideo,
			'dashboard_video'=>$dashboardvideo,
			'dashboard_title'=>$row->dashboard_title,
			'department'=>$row->department,
			'assign_to_user'=>$html,
			'edit'=>$edit);
			$i++;
		}
		
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($business_data),
			"iTotalDisplayRecords" => count($business_data),
			"aaData"=>$business_data);
			
		echo json_encode($results);
	}
	
public function edit_standalone_dashboard(){
$this->load->view('master/edit_standalone_dashboard.php');
}
public function update_standalone_permission()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('department', 'department', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/edit_standalone_dashboard.php');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "standalone_dashboard_access";
			$data = array('form_title'=>$this->input->post('form_title'),
			'dashboard_title'=>$this->input->post('dashboard_title'),
			'department_id'=>$this->input->post('department'),
			'form_vide_link'=>$this->input->post('form_video'),
			'dashboard_video_link'=>$this->input->post('dashboard_video'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			$this->db->where('id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
		   $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Form/stand_alone_dashboard_permission');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div>');
			redirect(page_url.'Form/stand_alone_dashboard_permission');
		}
		
	}
		
	}
	
	public function set_permission_of_access(){
		$this->load->view('form/set_permission_of_access');
		
	}
	
	public function set_permission(){
		
					$formid = $this->uri->segment(3);
					$user_id = $_REQUEST['user_id'];
					$usercount = count($user_id);
					
					for($i=0; $i<$usercount; $i++){
						
						$userid=$user_id[$i];
						$label = $_REQUEST['label_id'.$userid];
						
						for($j=0; $j<count($label); $j++){
							$labelid=$label[$j];
											
							if(isset($_REQUEST['pemission'.$userid.$labelid])){
								$value = $_REQUEST['pemission'.$userid.$labelid];
								//echo $value;exit;
								if($value==''){
									$val="0";
								}else{
									$val="1";
								}
							}else
							{
								$val=0;
							}
							
							$data = array('form_id'=>$formid,
							'user_id'=>$userid,
							'label_id'=>$labelid,
							'access_per'=>$val);
							
							$query = $this->db->select('id,form_id, user_id, label_id')->from('dynamic_form_access_permission')->where('form_id',$formid)->where('user_id',$userid)->where('label_id',$labelid)->get();
							$res = $query->result();
							if($query->num_rows()>0){
								foreach($res as $result);
								$this->db->where('id',$result->id);
								$this->db->update('dynamic_form_access_permission',$data);
								
							}else{
							$this->db->insert('dynamic_form_access_permission',$data);	
							}
							
							
						}
						
						
						
					}
					$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank You! permission successfully updated.</div>');
					redirect(page_url.'Form/set_permission_of_access/'.$formid);
					
		
		
	}
	
	public function all_forms(){
	    $this->load->view('form/all_form_list');
	}
	
	public function all_forms_list()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$scheduler_data = array();
		$this->db->select('a.department_id, a.form_running_status,a.user_id, a.dashboard_title, a.form_title, a.id, a.description, a.status,a.added_on, a.added_by, b.department_id, b.department, c.user_id, c.first_name, c.last_name, c.title, d.first_name as responsible_person, d.last_name as responsible_person_lname')->from('dynamic_forms a')->join('departments b','a.department_id=b.department_id','left')->join('system_users c','a.added_by=c.user_id','left')->join('system_users d','a.user_id=d.user_id','left');
		$query = $this->db->order_by('a.added_on','desc')->get();
		$res = $query->result();
		
		$i=1;
		foreach($res as $row)
		{
			$assignedto = array();
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			$action = "<a href='".page_url."Form/setting/".$row->id."'><span class='btn btn-success btn-xs'>Pending for Review</span></a>";
			
			
			$running_status = $row->form_running_status;
			if($running_status=='0')
			{
				$runningstatus =  "<a href='".page_url."Form/form_mark_as_not_active/".$row->id."/".$row->form_running_status."'><span class='btn btn-success btn-xs'>Mark as Not Active</span></a>";
			}else
			{
				$runningstatus="<span style='color:red; font-weight:bold;'>Form Marked as Not Active</span>";
			}
			$backgroundcolor = "yellow";
			$html = "<table border='1' style='width:300px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center'>ASSIGNED TO</th></tr>";
			$query = $this->db->select('a.user_id, b.first_name, b.last_name')->from('dynamic_form_dashboard_access a')->join('system_users b','a.user_id=b.user_id','left')->where('a.form_id',$row->id)->get();
			if($query->num_rows()>0){
				foreach($query->result() as $assignedto){
					$html.="<tr style='".$backgroundcolor."'>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($assignedto->first_name." ".$assignedto->last_name)."</td>";
				$html.="</tr>";
					
				}
			}else{
				$html.= "";
			}
			$html.="</table>";
			
			
			
			$scheduler_data[] = array('sr_no'=>$i,
			'department'=>strtoupper($row->department),
			'responsible'=>strtoupper($row->responsible_person)." ".strtoupper($row->responsible_person_lname),
			'form_title'=>strtoupper($row->form_title),
			'description'=>strtoupper($row->description),
			'dashboard_title'=>strtoupper($row->dashboard_title),
			'dashboard_assigned_to'=>$html,
			'added_on'=>$addeddate.$addedtime,
			'added_by'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'action'=>$action,
			'runningstatus'=>$runningstatus);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}


function randString($length) {
    $char = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $char = str_shuffle($char);
    for($i = 0, $rand = '', $l = strlen($char) - 1; $i < $length; $i ++) {
        $rand .= $char[mt_rand(0, $l)];
    }
    return $rand;
}


function checkalias()
{
	
	$alias=trim($this->input->post('alias'));
	
	$restyu=$this->db->select('id')->from('dynamic_forms')->where('alias',$alias)->get();
	echo $restyu->num_rows();exit;
	
	
}

public function update_ref_remarks(){
    
    $data = array('ref_remarks'=>$this->input->post('ref_no'));
   
    $this->db->where('id',$this->uri->segment(3));
   $res =  $this->db->update('dynamic_form_data',$data);
    if($res){
       $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank You! Remark successfully added.</div>');
		redirect(page_url.'Form/data_report/'.$this->uri->segment(4)); 
    }
}

public function consolidate_support_ticket_report(){
    $this->load->view('form/consolidate_report');
}

public function view_master_index(){
    $this->load->view('form/masterindex');
}

public function dashboard_quick_links(){
	$this->load->view('master/dashboard_links');
}

public function hod_dashboard_quick_links(){
	$this->load->view('master/hod_dashboard_links');
}

public function dashboard_quick_links_list()
	{
		$business_data = array();
		$this->db->select('*')->from('dashboard_short_links');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
				$edit = "<a href='".page_url."Form/edit_quick_dashboard/".$row->id."'><i class='fa fa-pencil'></i></a>";
				
				$formvideo = "<a href='".$row->form_video_link."' target='_blank'><span class='btn btn-success btn-xs'>View Form Video</span></a>";
				$dashboardvideo = "<a href='".$row->dashboard_video_link."' target='_blank'><span class='btn btn-success btn-xs'>View dashboard Video</span></a>";
			
			$html = "<table border='1' style='width:200px;'><tr style='background-color:yellow;'><th style='padding:2px 2px 2px 2px'>SR NO.</th><th style='padding:2px 2px 2px 2px'>USER NAME</th><th style='padding:2px 2px 2px 2px'>ACTION</th></tr>";
			$instrumentsss = array();
			$a=1;
			$query1 = $this->db->select('a.user_id, a.id, b.first_name, b.last_name')->from('standalone_dashboard_userwise_permission a')->join('system_users b','a.user_id=b.user_id','left')->where('a.dashboard_id',$row->id)->get();
			foreach($query1->result() as $userinfo){
				
				$delete = '<a href="'.page_url.'Form/delete_dashboard_link_access/'.$userinfo->id.'"><i class="fa fa-trash" title="remove access"></i></a>';
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".$a."</td>";
			
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($userinfo->first_name." ".$userinfo->last_name)."</td>";
					$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".$delete."</td>";
				$html.="</tr>";
			
			    $a++;
			}
			$html.="</table>";
			
				
			$business_data[] = array('sr_no'=>$i,
			'form_title'=>$row->form_name,
			'form_video'=>$formvideo,
			'dashboard_video'=>$dashboardvideo,
			'history_title'=>$row->history_title,
			'assign_to_user'=>$html,
			'edit'=>$edit);
			$i++;
		}
		
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($business_data),
			"iTotalDisplayRecords" => count($business_data),
			"aaData"=>$business_data);
			
		echo json_encode($results);
	}
	
	
public function edit_quick_dashboard(){
$this->load->view('master/edit_quick_dashboard.php');
}
public function update_quick_dashboard()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('form_title', 'form_title', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/edit_quick_dashboard.php');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "dashboard_short_links";
			$data = array('form_name'=>$this->input->post('form_title'),
			'history_title'=>$this->input->post('history_title'),
			'form_video_link'=>$this->input->post('form_video'),
			'dashboard_video_link'=>$this->input->post('dashboard_video'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			$this->db->where('id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
		    $userid = $this->input->post('user_name');
		    if(!empty($userid)){
		        
		        $query = $this->db->select('id')->from('standalone_dashboard_userwise_permission')->where('user_id',$userid)->where('dashboard_id',$this->uri->segment(3))->get();
		        if($query->num_rows()>0){
		            
		        }else{
		          $data1 = array('dashboard_id'=>$this->uri->segment(3),
			'user_id'=>$userid,
			'added_on'=>$date,
			'added_by'=>$user_id);
			$this->db->insert('standalone_dashboard_userwise_permission',$data1);   
		        }
		    }
		    
		    
		    
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Form/dashboard_quick_links');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div>');
			redirect(page_url.'Form/dashboard_quick_links');
		}
		
	}
		
	}
	
	function delete_dashboard_link_access(){
	   $this->db->where('id',$this->uri->segment(3));
	   $res=$this->db->delete('standalone_dashboard_userwise_permission');
	   $this->session->set_flashdata('message','<div class="alert alert-info alert-dismissable" style="color:#000;">Thank You! Access successfully removed.</div>');
			redirect(page_url.'Form/dashboard_quick_links');
	}
	
	public function hod_dashboard_quick_links_list()
	{
		$business_data = array();
		$this->db->select('*')->from('dashboard_short_links_for_hod');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
				$edit = "<a href='".page_url."Form/edit_hod_quick_dashboard/".$row->id."'><i class='fa fa-pencil'></i></a>";
				
				$formvideo = "<a href='".$row->form_video_link."' target='_blank'><span class='btn btn-success btn-xs'>View Form Video</span></a>";
				$dashboardvideo = "<a href='".$row->dashboard_video_link."' target='_blank'><span class='btn btn-success btn-xs'>View dashboard Video</span></a>";
			
			$html = "<table border='1' style='width:200px;'><tr style='background-color:yellow;'><th style='padding:2px 2px 2px 2px'>SR NO.</th><th style='padding:2px 2px 2px 2px'>USER NAME</th><th style='padding:2px 2px 2px 2px'>ACTION</th></tr>";
			$instrumentsss = array();
			$a=1;
			$query1 = $this->db->select('a.user_id, a.id, b.first_name, b.last_name')->from('standalone_dashboard_hodwise_permission a')->join('system_users b','a.user_id=b.user_id','left')->where('a.dashboard_id',$row->id)->get();
			foreach($query1->result() as $userinfo){
				
				$delete = '<a href="'.page_url.'Form/delete_hod_dashboard_link_access/'.$userinfo->id.'"><i class="fa fa-trash" title="remove access"></i></a>';
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".$a."</td>";
			
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($userinfo->first_name." ".$userinfo->last_name)."</td>";
					$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".$delete."</td>";
				$html.="</tr>";
			
			    $a++;
			}
			$html.="</table>";
			
				
			$business_data[] = array('sr_no'=>$i,
			'form_title'=>$row->form_name,
			'form_video'=>$formvideo,
			'dashboard_video'=>$dashboardvideo,
			'history_title'=>$row->history_title,
			'assign_to_user'=>$html,
			'edit'=>$edit);
			$i++;
		}
		
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($business_data),
			"iTotalDisplayRecords" => count($business_data),
			"aaData"=>$business_data);
			
		echo json_encode($results);
	}
	
public function edit_hod_quick_dashboard(){
$this->load->view('master/edit_hod_quick_dashboard.php');
}
public function update_hod_quick_dashboard()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('form_title', 'form_title', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/edit_hod_quick_dashboard.php');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "dashboard_short_links_for_hod";
			$data = array('form_name'=>$this->input->post('form_title'),
			'history_title'=>$this->input->post('history_title'),
			'form_video_link'=>$this->input->post('form_video'),
			'dashboard_video_link'=>$this->input->post('dashboard_video'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			$this->db->where('id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
		    $userid = $this->input->post('user_name');
		    if(!empty($userid)){
		        
		        $query = $this->db->select('id')->from('standalone_dashboard_hodwise_permission')->where('user_id',$userid)->where('dashboard_id',$this->uri->segment(3))->get();
		        if($query->num_rows()>0){
		            
		        }else{
		          $data1 = array('dashboard_id'=>$this->uri->segment(3),
			'user_id'=>$userid,
			'added_on'=>$date,
			'added_by'=>$user_id);
			$this->db->insert('standalone_dashboard_hodwise_permission',$data1);   
		        }
		    }
		    
		    
		    
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Form/hod_dashboard_quick_links');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div>');
			redirect(page_url.'Form/hod_dashboard_quick_links');
		}
		
	}
		
	}
	
	function delete_hod_dashboard_link_access(){
	   $this->db->where('id',$this->uri->segment(3));
	   $res=$this->db->delete('standalone_dashboard_hodwise_permission');
	   $this->session->set_flashdata('message','<div class="alert alert-info alert-dismissable" style="color:#000;">Thank You! Access successfully removed.</div>');
			redirect(page_url.'Form/hod_dashboard_quick_links');
	}
	
	public function update_remark_on_given_task(){
	    $user_id =$this->session->userdata['logged_in']['user_id'];	
	    	date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
	    $data = array('data_id'=>$this->uri->segment(3),
	    'remarks'=>$this->input->post('remarks'),
	    'added_on'=>$date,
		'added_by'=>$user_id);
		
		$this->db->insert('dynamic_form_data_remarks',$data);
	    $this->session->set_flashdata('message','<div class="alert alert-info alert-dismissable" style="color:#000;">Thank You! Remark successfully added.</div>');
			redirect(page_url.'Form/data_report/'.$this->uri->segment(4));
	    
	    
	}
	
	public function report_mis_master(){
	    $this->load->view('master/report_mis_management');
	}
		public function report_list()
	{
		$business_data = array();
		$this->db->select('a.*,b.first_name, b.last_name')->from('report_mis_master a')->join('system_users b','a.user_id=b.user_id','left');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
				$edit = "<a href='".page_url."Form/edit_set_mis_report/".$row->id."'><i class='fa fa-pencil'></i></a>";
				
			$business_data[] = array('sr_no'=>$i,
			'report_name'=>$row->report_name,
			'tat_time'=>$row->tat_time,
			'tat_days'=>$row->tat_days,
			'user_name'=>$row->first_name." ".$row->last_name,
			'edit'=>$edit);
			$i++;
		}
		
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($business_data),
			"iTotalDisplayRecords" => count($business_data),
			"aaData"=>$business_data);
			
		echo json_encode($results);
	}
	
	public function edit_set_mis_report(){
$this->load->view('master/edit_report_mis_management.php');
}
public function update_set_mis_report()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('report_name', 'report_name', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/edit_report_mis_management.php');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "report_mis_master";
			$data = array('report_name'=>$this->input->post('report_name'),
			'user_id'=>$this->input->post('user'),
			'tat_time'=>$this->input->post('tattime'),
			'tat_days'=>$this->input->post('tatdays'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		
		$this->db->where('id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
		   
		   $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Form/report_mis_master');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div>');
			redirect(page_url.'Form/report_mis_master');
		}
		
	}
		
	}
	
	public function helpticket_dashboard(){
	    $this->load->view('form/helpticket_dashboard');
	}
	
	public function upload_docket_image(){
	    
	    $docfile=$_FILES['photo']['name'];
		if($docfile<>'')
		{
		$image2=explode('.',$docfile);
		$cat_image1=end($image2);
		$docket=time().'.'.$cat_image1;
		move_uploaded_file($_FILES["photo"]["tmp_name"],UPLOADPATH.'dynamicfile/docket/' . $docket);
		}else
		{
		$docket="";
		}

	    $user_id =$this->session->userdata['logged_in']['user_id'];	
	    date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
	    $data = array('form_id'=>$this->uri->segment(4),
	    'record_id'=>$this->input->post('recordids'),
	    'filename'=>$docket,
	    'added_on'=>$date,
	    'added_by'=>$user_id);
	    
	    $this->db->insert('dynamic_data_docketfile',$data);
	    $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank You! file successfully uploaded. </div>');
			redirect(page_url.'Form/data_report/'.$this->uri->segment(4));
	    
	}
	
	public function ea_escalation(){
	    $this->load->view('form/escalated');
	}
	
	public function eemarkasdone(){
	    
	    $data= array('ea_status'=>'1');
	    $this->db->where('id',$this->uri->segment(3));
	    $this->db->update('dynamic_form_data',$data);
	    $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank You! task has been marked as done. </div>');
			redirect(page_url.'Form/ea_escalation');
	}
	
	public function gm_escalation(){
	    $this->load->view('form/gm_escalated');
	}
	
	public function gmmarkasdone(){
	    
	    $data= array('gm_status'=>'1');
	    $this->db->where('id',$this->uri->segment(3));
	    $this->db->update('dynamic_form_data',$data);
	    $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank You! task has been marked as done. </div>');
			redirect(page_url.'Form/ea_escalation');
	}
	
	public function view_filtered_data(){
	    $data = array('start_date'=>date('Y-m-d',strtotime($this->input->post('start_date'))),
	    'end_date'=>date('Y-m-d',strtotime($this->input->post('end_date')))
	    );
	    
	    $this->load->view('form/filtered_data',$data);
	    
	}
	
	public function daily_report(){
	    $this->load->view('form/daily_report');
	    
	}

	public function paymentform(){
	    $this->load->view('master/payment-request-form');
	    
	}
	

	public function selecteusers(){
		$departmentid = $this->input->post('departmentid');
		$option='<option value="">SELECT PERSON</option>';
		$q = $this->db->select('user_id,title, first_name, last_name')->from('system_users')->where('department_id',$departmentid)->where('user_status',1)->get();
		foreach($q->result() as $row){
			$username = strtoupper($row->title." ".$row->first_name." ".$row->last_name);
			$option.='<option value="'.$row->user_id.'">'.$username.'</option>';
		}

		echo $option; exit;
	}


	function addpaymentform() {

    $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
    $this->form_validation->set_rules('department', 'Department Selection', 'required|trim');
    $this->form_validation->set_rules('user_id', 'User Selection', 'required|trim'); // Allow multiple user selection
    $this->form_validation->set_rules('amount', 'Amount', 'required|trim'); // Allow multiple user selection
    $this->form_validation->set_rules('companyname', 'Company Name', 'required|trim'); // Allow multiple user selection
    $this->form_validation->set_rules('payment_type', 'Payment Type', 'required|trim'); // Allow multiple user selection

    $this->form_validation->set_rules('remarks', 'Remarks', 'required|trim');

    $user_id = $this->session->userdata['logged_in']['user_id'];

    if ($this->form_validation->run() == FALSE) {
        $this->load->view('master/payment-request-form');
    } else {

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

        // Insert data into database
        $data = array(
            'date' => date('Y-m-d'),
            'time' => date('H:i:s', strtotime($this->input->post('meetingtime'))),
            'department' => $this->input->post('department'),
            'user_id' => $this->input->post('user_id'),
            'amount' => $this->input->post('amount'),
            'companyname'=>$this->input->post('companyname'),
            'payment_type'=>$this->input->post('payment_type'),
            'particular' => ucwords(strtolower($this->input->post('remarks'))),
            'attachment' => $screenshot,
            'payment_status' => 0,
            'added_on' => date('Y-m-d h:i:s'),
            'added_by' => $user_id
        );

        $comp = $this->input->post('companyname');
        $ptype = $this->input->post('payment_type');

        $this->db->insert('payment_request', $data);
        $insertid = $this->db->insert_id();

        if ($insertid) {

        	$q = $this->db->select('department')->from('departments')->where('department_id',$this->input->post('department'))->get();
        	foreach($q->result() as $r1);
        	$departmentname = ucwords(strtolower($r1->department));

        	$q = $this->db->select('title, first_name, last_name')->from('system_users')->where('user_id',$this->input->post('user_id'))->get();
        	foreach($q->result() as $r2);
        	$username = ucwords(strtolower($r2->title." ".$r2->first_name." ".$r2->last_name));
        	            // Send email to account department and user in CC
            $this->load->library('email'); // Load the email library

            // Get user email from the database
            $this->db->select('email');
            $this->db->where('user_id', $this->input->post('user_id'));
            $query = $this->db->get('system_users');  // Assuming 'users' is your user table
            $user_email = $query->row()->email;

            $to_email = 'account1@shubhampack.com'; // Replace with the actual email of the account department
            $subject = 'New Payment Request Added';

            // HTML email content with logo
            $message = "
                <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                    <div style='text-align: center; margin-bottom: 20px;'>
                        <img src='https://pms.shubhampack.in/assets/images/shubhampack.png' alt='Shubham Flexible Packaging' style='max-width: 200px;'>
                    </div>
                    <h2 style='text-align: center; color: #4872b8;'>New Payment Request Submitted</h2>
                    <p>Dear Accounts Team,</p>
                    <p>A new payment request has been submitted with the following details:</p>
                    <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                        <tr>
                            <td style='border: 1px solid #ddd; padding: 8px;'><strong>Department:</strong></td>
                            <td style='border: 1px solid #ddd; padding: 8px;'>" .$departmentname. "</td>
                        </tr>
                        <tr>
                            <td style='border: 1px solid #ddd; padding: 8px;'><strong>User:</strong></td>
                            <td style='border: 1px solid #ddd; padding: 8px;'>" . $username . "</td>
                        </tr>
                         <tr>
                            <td style='border: 1px solid #ddd; padding: 8px;'><strong>Company Name:</strong></td>
                            <td style='border: 1px solid #ddd; padding: 8px;'>" . $comp . "</td>
                        </tr>
                         <tr>
                            <td style='border: 1px solid #ddd; padding: 8px;'><strong>Payment Type:</strong></td>
                            <td style='border: 1px solid #ddd; padding: 8px;'>" . $ptype . "</td>
                        </tr>
                        <tr>
                            <td style='border: 1px solid #ddd; padding: 8px;'><strong>Amount:</strong></td>
                            <td style='border: 1px solid #ddd; padding: 8px;'>" . $this->input->post('amount') . "</td>
                        </tr>
                        <tr>
                            <td style='border: 1px solid #ddd; padding: 8px;'><strong>Remarks:</strong></td>
                            <td style='border: 1px solid #ddd; padding: 8px;'>" . ucwords(strtolower($this->input->post('remarks'))) . "</td>
                        </tr>
                    </table>
                    <p><strong>Attachment:</strong> " . ($screenshot ? "<a href='" . UPLOADPATH . "maintenance/" . $screenshot . "' target='_blank'>View Screenshot</a>" : "No attachment provided") . "</p>
                    <p>Regards,<br>Shubham Flexible Packaging Team</p>
                </div>
            ";

            // Email configuration
            $this->email->from('taskmanagement@shubhampack.in', 'Shubham Flexible Packaging');
            $this->email->to($to_email); // Recipient Email
            $this->email->cc($user_email); // CC to user email
            $this->email->subject($subject);
            $this->email->message($message);

            // Send the email
            if (!$this->email->send()) {
                log_message('error', 'Email failed to send: ' . $this->email->print_debugger());
            }

            // Redirect
            $this->session->set_flashdata('message', '<div class="alert alert-success">Thank you, record successfully added.</div>');
            redirect(page_url . 'Form/paymentform');
        }
    }
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


function payment_list(){


$this->load->view('form/payment-list');

}

public function updatePaymentStatus()
{
    $user_id = $this->session->userdata['logged_in']['user_id'];
    $id = $this->input->post('id');
    $status = $this->input->post('status');
    $remarks = $this->input->post('yourremark');
    $paymentType = $this->input->post('payment_type'); // New field for payment type

    // Prepare the update data
    $updateData = [
        'payment_status' => $status,
        'payment_remarks' => $remarks,
        'updated_on' => date('Y-m-d H:i:s'),
        'updated_by' => $user_id
    ];

    // Include payment type if the status is "Paid" (1)
    if ($status == 1) {
        $updateData['payment_type'] = $paymentType; // Add the payment type field
    }

    // Update the record in the database
    $this->db->where('id', $id);
    $update = $this->db->update('payment_request', $updateData);

    if ($update) {
        // Fetch the details of the user who updated the record
        $Q1 = $this->db->select('title, first_name, last_name')
            ->from('system_users')
            ->where('user_id', $user_id)
            ->get();

        foreach ($Q1->result() as $ro);

        // Fetch the details of the user who added the record
        $this->db->select('a.added_by, b.email, b.first_name, b.last_name, a.companyname, a.payment_type');
        $this->db->from('payment_request a');
        $this->db->join('system_users b', 'a.added_by = b.user_id', 'left');
        $this->db->where('a.id', $id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $user = $query->row();

            // Prepare email
            $this->load->library('email');
            $addedbyuser = $ro->title . " " . $ro->first_name . " " . $ro->last_name;
            $paymentTypeText = $status == 1 ? "<tr>
                        <td style='border: 1px solid #ddd; padding: 8px;'><strong>Payment Type:</strong></td>
                        <td style='border: 1px solid #ddd; padding: 8px;'>{$paymentType}</td>
                    </tr>" : ""; // Include payment type only if "Paid"



            $subject = 'Payment Request Status Updated';
            $message = "
                <p>Dear {$user->first_name} {$user->last_name},</p>
                <p>Your payment request has been updated with the following details:</p>
                <table style='width: 100%; border-collapse: collapse;'>
                    <tr>
                        <td style='border: 1px solid #ddd; padding: 8px;'><strong>Status:</strong></td>
                        <td style='border: 1px solid #ddd; padding: 8px;'>" . ($status == 1 ? 'Paid' : ($status == 2 ? 'Pending' : 'Rejected')) . "</td>
                    </tr>
                  
                     <tr>
                        <td style='border: 1px solid #ddd; padding: 8px;'><strong>Company Name:</strong></td>
                        <td style='border: 1px solid #ddd; padding: 8px;'>{$user->companyname}</td>
                    </tr>
                    <tr>
                        <td style='border: 1px solid #ddd; padding: 8px;'><strong>Remarks:</strong></td>
                        <td style='border: 1px solid #ddd; padding: 8px;'>{$remarks}</td>
                    </tr>
                    <tr>
                        <td style='border: 1px solid #ddd; padding: 8px;'><strong>Updated By:</strong></td>
                        <td style='border: 1px solid #ddd; padding: 8px;'>{$addedbyuser}</td>
                    </tr>
                </table>
                <p>Regards,<br>Accounts Team</p>
            ";

            // Email configuration
            $this->email->from('taskmanagement@shubhampack.in', 'Payment Request Update - Shubham Pack PMS');
            $this->email->to($user->email); // Send to the user who added the record
            $this->email->cc('account1@shubhampack.com');
            $this->email->subject($subject);
            $this->email->message($message);

            // Send the email
            if (!$this->email->send()) {
                log_message('error', 'Email failed to send: ' . $this->email->print_debugger());
            }
        }

        // Return success response
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
}


public function filterpaymentdata(){

$startdate =date('Y-m-d',strtotime($this->input->post('startdate')));
$enddate = date('Y-m-d',strtotime($this->input->post('enddate')));
$userid = $this->input->post('user_id');

redirect(page_url."Form/filterrecord/".$startdate."/".$enddate."/".$userid);
}


public function filterrecord(){
	$this->load->view('form/filter-payment-list');
}

function your_payment_list_dashboard(){


$this->load->view('form/your-payment-request-dashboard');

}

public function user_list_new(){
	$user_id = $this->session->userdata['logged_in']['user_id'];
		echo "<option value=''>--Select User--</option>";
	$department = $this->input->post('department');

		$query =$this->db->select('user_id, first_name, last_name,department_id, hide_profile, user_status')->from('system_users')->where('department_id',$department)->where('hide_profile','0')->where('user_id',$user_id)->where('user_status','1')->get();
		foreach($query->result() as $users)
			{
				echo "<option value=".$users->user_id.">".strtoupper($users->first_name)." ".strtoupper($users->last_name)."</option>";
				}
	}


public function dfmeetingnotification(){
	    $this->load->view('form/df-meeting-notification');
	    
	}

public function feeddfmeetingnotification() {
    $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
    $this->form_validation->set_rules('agenda', 'Agenda', 'required|trim');
    $this->form_validation->set_rules('date', 'Date', 'required|trim');
    
    $user_id = $this->session->userdata['logged_in']['user_id'];
    $department_id = $this->session->userdata['logged_in']['department_id'];
    
    if ($this->form_validation->run() == FALSE) {
        $this->load->view('form/df-meeting-notification');
    } else {
        date_default_timezone_set("Asia/Kolkata");
        $added_time = date('Y-m-d H:i:s');
        $participants = $this->input->post('participant'); // Participant user IDs

        // Insert meeting details into the main table
        $data = array(
            'agenda' => strtoupper($this->input->post('agenda')),
            'added_by' => $user_id,
            'date_of_meeting' => date('Y-m-d', strtotime($this->input->post('date'))),
            'time_of_meeting' => date('H:i:s', strtotime($this->input->post('time'))),
            'added_on' => $added_time
        );

        $this->db->insert('df_meeting_notification_alert', $data);
        $meeting_id = $this->db->insert_id(); // Meeting ID for reference

        // Fetch participant details and save in the participants table
        $this->db->select('user_id, first_name, last_name, email, contact_number');
        $this->db->from('system_users');
        $this->db->where_in('user_id', $participants);
        $participant_query = $this->db->get();

        foreach ($participant_query->result() as $participant) {
            // Insert participant data into a separate table
            $participant_data = array(
                'meeting_id' => $meeting_id,
                'participant_id' => $participant->user_id,
                'response_status' => 0, // Response pending
                'response_link' => page_url . 'User/userresponseofmeetinginvitation/' . $meeting_id . '/' . $participant->user_id // Response link
            );
            $this->db->insert('meeting_participants', $participant_data);
            $meetingidinfo = base64_encode($meeting_id);
             $userforwhatsapp = base64_encode($participant->user_id);
            // Prepare email and WhatsApp message
            $response_link = page_url . 'User/userresponseofmeetinginvitation/' . $meetingidinfo . '/' . $userforwhatsapp;
            $userforwhatsapp = base64_encode($participant->user_id);

            // Email Body with logo and details
            $email_body = '
            <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif; background-color: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 8px;">
                <tr>
                    <td style="background-color: #4872b8; padding: 10px; text-align: center;">
                        <img src="https://shubhampack.com/wp-content/uploads/2021/05/Logo.png" width="160" alt="Shubham Pack" style="display: block; margin: 0 auto;" />
                    </td>
                </tr>
                <tr>
                    <td style="padding: 15px; background-color: #ffffff;">
                        <h2 style="color: #333333; font-size: 20px; margin: 0; text-align: center;">DF MEETING INVITATION</h2>
                        <hr style="border: 0; border-top: 1px solid #e0e0e0; margin: 15px 0;">
                        <p style="color: #555555; font-size: 14px; margin: 0; line-height: 1.6;">
                            <strong>Dear ' . ucwords(strtolower($participant->first_name . ' ' . $participant->last_name)) . ',</strong>
                        </p>
                        <p style="color: #555555; font-size: 14px; margin: 15px 0; line-height: 1.6;">
                            MD Sir is inviting you to attend the following DF meeting:
                        </p>
                        <ul style="color: #555555; font-size: 14px; margin: 15px 0; line-height: 1.6;">
                            <li><strong>Agenda:</strong> ' .ucwords(strtoupper($this->input->post('agenda'))) . '</li>
                            <li><strong>Date:</strong> ' . date('d-m-Y', strtotime($this->input->post('date'))) . '</li>
                            <li><strong>Time:</strong> ' . date('h:i A', strtotime($this->input->post('time'))) . '</li>
                        </ul>
                        <p style="color: #555555; font-size: 14px; margin: 15px 0; line-height: 1.6;">
                            Please click the link below to respond to your availability:
                        </p>
                        <p style="text-align: left;">
                            <a href="' . $response_link . '" style="color: #ffffff; background-color: #4872b8; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-size: 14px;">RESPOND NOW</a>
                        </p>
                        <p style="color: #555555; font-size: 13px; text-align: center; margin: 20px 0; line-height: 1.6;">
                            If you have any questions, please contact us.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="background-color: #4872b8; color: #ffffff; padding: 10px; text-align: center; font-size: 12px;">
                        &copy; ' . date("Y") . ' Shubham Pack. All rights reserved.
                    </td>
                </tr>
            </table>';

            // WhatsApp Message with details
            $whatsappresponsemeetinglink = page_url . "User/userresponseofmeetinginvitation/" .$meetingidinfo."/".$userforwhatsapp;
            $whatsapp_message = "Dear " . ucwords(strtolower($participant->first_name . ' ' . $participant->last_name)) . ",\n\n"
                . "MD Sir is inviting you to attend the following DF meeting:\n\n"
                . "📌 *Agenda:* " . ucwords(strtolower($this->input->post('agenda'))) . "\n"
                . "📅 *Date:* " . date('d-m-Y', strtotime($this->input->post('date'))). "\n"
                . "⏰ *Time:* " . date('h:i A', strtotime($this->input->post('time'))) . "\n\n"
                . "📝 *Click below to respond:*\n" . $whatsappresponsemeetinglink . "\n\n"
                . "Thank you,\n*Shubham Pack Team*";



            // Send Email
            $this->load->library('email');
            $this->email->set_mailtype("html");
            $this->email->to($participant->email);
            $this->email->from('taskmanagement@shubhampack.com', 'Shubham Pack');
            $this->email->cc('mangleshup@gmail.com');
            $this->email->subject('DF Meeting Invitation: ' . strtoupper($this->input->post('agenda')));
            $this->email->message($email_body);
            $this->email->send();

            // Send WhatsApp Message
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            $post = array(
                'receiverMobileNo' => '91' . $participant->contact_number,
                'username' => whatsappuser1, // Replace with actual username
                'password' => whatsapppass1, // Replace with actual password
                'message' => strip_tags($whatsapp_message)
            );
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_exec($ch);
            curl_close($ch);
        }

        // Set success message and redirect
        $this->session->set_flashdata('message', '<span style="color:green;" class="alert alert-success">Meeting created successfully, and notifications have been sent to all participants.</span>');
        redirect(page_url . 'Form/dfmeetingnotification');
    }
}


public function submitResponse() {
    $this->form_validation->set_rules('response', 'Response', 'required|trim');
    $this->form_validation->set_rules('reason', 'Reason', 'trim');
    
    $meeting_id = base64_decode($this->input->post('meeting_id'));
    $participant_id = base64_decode($this->input->post('participant_id'));
    $response = $this->input->post('response');
    $reason = $this->input->post('reason');

    if ($response === 'Not Available' && empty($reason)) {
        $this->session->set_flashdata('error', 'Reason is required if you select "Not Available".');
        redirect(page_url . 'User/userresponse/' . $meeting_id . '/' . $participant_id);
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

    $meeting_id_for_url = base64_encode($meeting_id);
    $participant_id_for_url = base64_encode($participant_id);

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

    $participant_name = ucwords(strtolower($participant->first_name . ' ' . $participant->last_name));
    $creator_email = $creator->email;

    // Prepare email and WhatsApp message
    $response_status = $response === 'Available' ? 'Available to Join' : 'Not Available to Join';
    $email_body = '
    <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif; background-color: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 8px;">
        <tr>
            <td style="background-color: #4872b8; padding: 10px; text-align: center;">
                <img src="https://shubhampack.com/wp-content/uploads/2021/05/Logo.png" width="160" alt="Shubham Pack" style="display: block; margin: 0 auto;" />
            </td>
        </tr>
        <tr>
            <td style="padding: 15px; background-color: #ffffff;">
                <h2 style="color: #333333; font-size: 20px; margin: 0; text-align: center;">MEETING RESPONSE RECEIVED</h2>
                <hr style="border: 0; border-top: 1px solid #e0e0e0; margin: 15px 0;">
                <p style="color: #555555; font-size: 14px; margin: 0; line-height: 1.6;">
                    <strong>Dear ' . ucwords(strtolower($creator->first_name . ' ' . $creator->last_name)) . ',</strong>
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
                &copy; ' . date("Y") . ' Shubham Pack. All rights reserved.
            </td>
        </tr>
    </table>';

    $whatsapp_message = "Dear " . ucwords(strtolower($creator->first_name . ' ' . $creator->last_name)) . ",\n\n"
        . "Participant Response for DF Meeting:\n\n"
        . "👤 *Participant:* " . ucwords(strtolower($participant_name)) . "\n"
        . "📌 *Response:* " . $response_status . "\n" .
        ($response === 'Not Available' ? "📝 *Reason:* " . $reason . "\n" : "") . "\n"
        . "Thank you,\n*Task Management Team*";

    // Send email
    $this->load->library('email');
    $this->email->set_mailtype("html");
    $this->email->to($creator_email);
    $this->email->from('taskmanagement@shubhampack.com', 'Shubham Pack');
    $this->email->subject('Meeting Response Received: ' . ucwords(strtolower($meeting->agenda)));
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
    redirect(page_url . 'User/userresponseofmeetinginvitation/' . $meeting_id_for_url . '/' . $participant_id_for_url);
}


function dfmeetingnotificationlist(){


$this->load->view('form/all-scheduled-meeting-list');

}



public function updateAcceptReject() {
    $user_id = $this->session->userdata['logged_in']['user_id'];
    $id = $this->input->post('id');
    $email = $this->input->post('email');
    $action = $this->input->post('action');
    $remarks = $this->input->post('remarks');

    // Fetch the specific payment request details (Particular and Amount)
    $paymentDetails = $this->db->select('a.particular, a.amount, b.title, b.first_name, b.last_name')
        ->from('payment_request a')
        ->join('system_users b', 'a.added_by = b.user_id', 'left')
        ->where('a.id', $id)->get()
        ->row();

    // Ensure payment details exist
    if (!$paymentDetails) {
        echo json_encode(['status' => 'error', 'message' => 'Payment request not found.']);
        return;
    }

    $particular = $paymentDetails->particular;
    $amount = $paymentDetails->amount;
    $personname = trim($paymentDetails->title . ' ' . $paymentDetails->first_name . ' ' . $paymentDetails->last_name);

    // Prepare update data
    $data = [
        'account_accept_reject' => ($action === 'accept') ? 1 : 2,
        'accepted_rejected_on' => date('Y-m-d H:i:s'),
        'accepted_rejected_by' => $user_id,
    ];

    if ($action === 'reject') {
        $data['reject_remarks'] = $remarks;
    }

    // Update the database
    $this->db->where('id', $id);
    if ($this->db->update('payment_request', $data)) {
        // Prepare email content
        $subject = ($action === 'accept') ? 'Payment Request Accepted' : 'Payment Request Rejected';
        $statusText = ($action === 'accept') ? 'ACCEPTED' : 'REJECTED';
        $remarksText = ($action === 'reject') ? '<p><strong>Remarks:</strong> ' . ucfirst(strtolower($remarks)) . '</p>' : '';

        // Shubham Pack logo URL
        $logoUrl = 'https://shubhampack.in/beta1/assets/images/shubhampack.png';

        $message = '
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    background-color: #f9f9f9;
                    margin: 0;
                    padding: 20px;
                }
                .email-container {
                    max-width: 600px;
                    margin: 0 auto;
                    background: #ffffff;
                    border: 1px solid #e0e0e0;
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                }
                .email-header {
                    background-color: #4872b8;
                    color: #ffffff;
                    text-align: center;
                    padding: 15px;
                }
                .email-header img {
                    max-height: 50px;
                }
                .email-body {
                    padding: 20px;
                    color: #333333;
                }
                .email-footer {
                    text-align: center;
                    background-color: #f2f2f2;
                    padding: 10px;
                    color: #666666;
                    font-size: 14px;
                }
                .btn {
                    display: inline-block;
                    padding: 10px 15px;
                    background-color: #4872b8;
                    color: #ffffff;
                    text-decoration: none;
                    border-radius: 5px;
                    font-size: 14px;
                }
            </style>
        </head>
        <body>
            <div class="email-container">
                <div class="email-header">
                    <img src="' . $logoUrl . '" alt="Shubham Pack">
                    <h2>Payment Request ' . $statusText . '</h2>
                </div>
                <div class="email-body">
                    <p>Dear ' . ucwords(strtolower($personname)) . ',</p>
                    <p>Your payment request has been <strong>' . $statusText . '</strong>.</p>
                    <p><strong>Particular:</strong> ' . ucfirst(strtolower($particular)) . '</p>
                    <p><strong>Amount:</strong> ₹' . number_format($amount, 2) . '</p>
                    ' . $remarksText . '
                    <p>If you have any questions, feel free to contact us.</p>
                    <p style="text-align: center;">
                        <a href="mailto:account1@shubhampack.com" class="btn">Contact Us</a>
                    </p>
                </div>
                <div class="email-footer">
                    <p>&copy; ' . date('Y') . ' Shubham Pack. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ';

        // Send email
        $this->load->library('email');
        $this->email->from('taskmanagement@shubhampack.com', 'Shubham Pack - Payment Request');
        $this->email->to($email);
        $this->email->subject($subject);
        $this->email->message($message);
        $this->email->set_mailtype("html"); // Set mail type to HTML

        if ($this->email->send()) {
            echo json_encode(['status' => 'success', 'message' => 'Request processed and email sent successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Request updated but email failed to send.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update payment request.']);
    }
}



public function askReasonPage($taskId)
{
    // Fetch task details using the task ID
    $taskQuery = $this->db->select('a.id, d.df_no, b.task_name, c.title, c.first_name, c.last_name')
        ->from('task_department_wise_scheduling a')
        ->join('df_release d','a.df_id=d.id','left')
        ->join('task_management b', 'a.taskid = b.task_id', 'left')
        ->join('system_users c', 'a.task_completed_by = c.user_id', 'left')
        ->where('a.id', $taskId)
        ->get();

    if ($taskQuery->num_rows() > 0) {
        $taskDetails = $taskQuery->row_array();

        // Pass the task details to the view
        $this->load->view('form/ask_reason_form', ['taskDetails' => $taskDetails]);
    } else {
        // Handle invalid task ID (optional)
        show_404();
    }
}

public function sendReasonEmail()
{
    $dfNo = $this->input->post('df_no');
    $taskName = $this->input->post('task_name');
    $accountablePerson = $this->input->post('accountable_person');
    $remarks = $this->input->post('remarks');

    // Fetch team leader email
    $teamLeaderQuery = $this->db->select('a.team_id')
        ->from('presto_team_members a')
        ->where('a.employee_id', $accountablePerson) // Adjust as needed
        ->get();

    if ($teamLeaderQuery->num_rows() > 0) {
        $team_id = $teamLeaderQuery->row()->team_id;

        $q = $this->db->select('b.email')->from('prestogroup_teams a')->join('system_users b','a.team_leader=b.user_id','left')->where('a.team_id',$team_id)->get();
        if($q->num_rows()>0){
        	foreach($q->result() as $row);
        	 $teamLeaderEmail =  $row->email;

        	 $subject = "Reason Required for Task Delay";
        $message = "
            <p>Dear Team Leader,</p>
            <p>The following task requires your clarification:</p>
            <table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse;'>
                <tr><th>DF No.</th><td>{$dfNo}</td></tr>
                <tr><th>Task Name</th><td>{$taskName}</td></tr>
                <tr><th>Accountable Person</th><td>{$accountablePerson}</td></tr>
                <tr><th>Remark</th><td>{$remarks}</td></tr>
            </table>
            <p>Please provide your response at the earliest.</p>
            <p>Regards,<br>Rishi Sir</p>
        ";
        echo $message; exit;

        // Send email
        $this->load->library('email');
        $this->email->from('taskmanagement@shubhampack.com', 'Justification for Task Approval Rejection delay');
       // $this->email->to($teamLeaderEmail);
        $this->email->to('mangleshup@gmail.com');
        $this->email->subject($subject);
        $this->email->message($message);
        $this->email->set_mailtype('html');

        if ($this->email->send()) {
            $this->session->set_flashdata('success', 'Email sent successfully to the team leader.');
        } else {
            $this->session->set_flashdata('error', 'Failed to send email. Please try again.');
        }

        }

    } else {
        $this->session->set_flashdata('error', 'Team leader email not found.');
    }

    // Redirect back to the pending tasks page
    redirect(page_url.'Dashboard');
}


public function performa_invoice(){

	$this->load->view('form/performa_invoice');

}

function add_domestic_pi()
{ 		//exit;
	$po_id=$this->uri->segment(3);
	$lead_id=$this->uri->segment(4);
	$task_id=$this->uri->segment(5);
	$dr=array(
	'po_id'=>$po_id,
	'lead_id'=>$lead_id,
	'invoice_no'=>$this->input->post('prof_inv_no'),
	'invoice_date'=>$this->input->post('prof_inv_date'),
	'state'=>$this->input->post('state'),
	'place_of_supply'=>$this->input->post('place_supply'),
	'customer_po'=>$this->input->post('cust_po_no'),
	'customer_po_date'=>date('Y-m-d',strtotime($this->input->post('cust_po_date'))),
	'freight'=>$this->input->post('freight_terms'),
	'hsn'=>$this->input->post('hsn_code'),
	'remarks'=>$this->input->post('remarks'),
	'addedOn'=>date('Y-m-d H:i:s'),
	'addedBy'=>$_SESSION['logged_in']['user_id']);
	$this->db->insert('performa_invoice',$dr);
	$lid=$this->db->insert_id();


	/** ADD SHIP BILL DETAIL **/
	$dr=array(
		'record_id'=>$lid,
		'bill_to_name'=>$this->input->post('bill_to_name'),
		'bill_address'=>$this->input->post('bill_to_address'),
		'bill_state'=>$this->input->post('bill_to_state'),
		'bil_state_code'=>$this->input->post('bill_to_state_code'),
		'bill_gst'=>$this->input->post('bill_to_gst_no'),
		'bill_pan'=>$this->input->post('bill_to_pan_no'),
		'ship_to_name'=>$this->input->post('ship_to_name'),
		'ship_address'=>$this->input->post('ship_to_address'),
		'ship_state'=>$this->input->post('ship_to_state'),
		'ship_state_code'=>$this->input->post('ship_to_state_code'),
		'ship_gst'=>$this->input->post('ship_to_gst_no'),
		'ship_pan'=>$this->input->post('ship_to_pan_no'),
		'addedOn'=>date('Y-m-d H:i:s'),
		'addedBy'=>$_SESSION['logged_in']['user_id']);
		$this->db->insert('performa_invoice_address',$dr);


	/** ADD ITEMS **/

	$desc_goodss=$this->input->post('desc_goods');
	for($i=0;$i<count($desc_goodss);$i++)
	{

		if($this->input->post('desc_goods')[$i]<>'')
		{
		$dr=array(
		'record_id'=>$lid,
		
		'description'=>$this->input->post('desc_goods')[$i],
		
		'qty'=>$this->input->post('qty')[$i],
		'uom'=>$this->input->post('uom')[$i],
		'rate'=>$this->input->post('rate_inr')[$i],
		// 'discount_per'=>$this->input->post('discount')[$i],
		'addedBy'=>$_SESSION['logged_in']['user_id'],
		'addedOn'=>date('Y-m-d H:i:s')
		);
		$this->db->insert('performa_invoice_items',$dr);
		}
	}


	if($task_id>0 && $task_id<>'')
	{
		$dr=array('task_status'=>1,'task_completed_on'=>date('Y-m-d H:i:s'),'task_completed_by'=>$_SESSION['logged_in']['user_id']);
		$this->db->where('id',$task_id);
		$this->db->update('task_department_wise_scheduling',$dr);

		if($this->shouldQueuePreReleaseDfTask($po_id, array(114, 86, 87, 2))){

		$tomorrow = date('Y-m-d', strtotime('+1 day'));
		$nextWorkingDate = $this->iftomorrowisholiday($tomorrow);
		/** ADD TASK 114 FILL DF **/
		$addnewdata = array('df_id'=>0,
				'taskid'=>114,
				'department_id'=>9,
				'start_date'=>date('Y-m-d'),
				'end_date'=>date('Y-m-d',strtotime($nextWorkingDate)),
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$this->session->userdata['logged_in']['user_id'],
				'po_id'=>$po_id,
				'task_status'=>0,
				'userid'=>$this->session->userdata['logged_in']['user_id'],
				'assigned_user'=>$this->session->userdata['logged_in']['user_id'],
				'assigned_by'=>$this->session->userdata['logged_in']['user_id'],
				'assigned_on'=>date('Y-m-d H:i:s'));

			$this->db->insert('task_department_wise_scheduling',$addnewdata);
	}
	}

	redirect(page_url.'Formats/performa_invoice/'.$po_id.'/'.$lead_id);


}


private function shouldQueuePreReleaseDfTask($po_id, $blockedTaskIds = array()){
	if(!empty($blockedTaskIds)){
		$existing_task = $this->db->select('id')
			->from('task_department_wise_scheduling')
			->where('po_id', $po_id)
			->where('df_id', 0)
			->where('task_status', 0)
			->where_in('taskid', $blockedTaskIds)
			->limit(1)
			->get();

		if($existing_task->num_rows() > 0){
			return false;
		}
	}

	$active_df = $this->db->select('p.id')
		->from('poreceived p')
		->join('df_release dr', 'dr.id = p.df_id', 'left')
		->where('p.id', $po_id)
		->where('p.df_id >', 0)
		->group_start()
			->where('dr.df_status', 0)
			->or_where('dr.df_status', 'running')
			->or_where('dr.df_status IS NULL', null, false)
		->group_end()
		->limit(1)
		->get();

	return $active_df->num_rows() === 0;
}

function iftomorrowisholiday($date){
    while (true) {
        $q = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date', $date)->get();

        if ($q->num_rows() > 0) {
            // If it's a holiday, move to the next day
            $date = date('Y-m-d', strtotime($date . ' +1 day'));
        } else {
            // Found a working day
            break;
        }
    }
    return $date;
}


public function export_performa_invoice(){

	$this->load->view('form/export_performa_invoice');

}



function add_export_pi()
{
	//exit;
	$po_id=$this->uri->segment(3);
	$lead_id=$this->uri->segment(4);
	$task_id=$this->uri->segment(5);
	$dr=array(
	'po_id'=>$po_id,
	'lead_id'=>$lead_id,
	'invoice_no'=>$this->input->post('prof_inv_no'),
	'invoice_date'=>$this->input->post('prof_inv_date'),
	// 'state'=>$this->input->post('state'),
	// 'place_of_supply'=>$this->input->post('place_supply'),
	// 'customer_po'=>$this->input->post('cust_po_no'),
	// 'customer_po_date'=>date('Y-m-d',strtotime($this->input->post('cust_po_date'))),
	// 'freight'=>$this->input->post('freight_terms'),
	'remarks'=>$this->input->post('remarks'),
	'buyer_order_no'=>$this->input->post('buyer_order_no'),
	'buyer_order_date'=>$this->input->post('buyer_order_date'),
	// 'iec_code'=>$this->input->post('iec_code'),
	// 'rbi_code'=>$this->input->post('rbi_code'),
	// 'eepc_no'=>$this->input->post('eepc_no'),
	'hsn'=>$this->input->post('hsn_code'),
	'addedOn'=>date('Y-m-d H:i:s'),
	'addedBy'=>$_SESSION['logged_in']['user_id']);
	$this->db->insert('performa_invoice',$dr);
	$lid=$this->db->insert_id();


	/** ADD SHIP BILL DETAIL **/
	$dr=array(
		'record_id'=>$lid,
		'bill_to_name'=>$this->input->post('bill_to_name'),
		'bill_address'=>$this->input->post('bill_to_address'),
		'bill_state'=>$this->input->post('bill_to_state'),
		'bil_state_code'=>$this->input->post('bill_to_state_code'),
		'bill_gst'=>$this->input->post('bill_to_gst_no'),
		'bill_iec'=>$this->input->post('bill_to_iec_no'),
		// 'bill_pan'=>$this->input->post('bill_to_pan_no'),
		'ship_to_name'=>$this->input->post('ship_to_name'),
		'ship_address'=>$this->input->post('ship_to_address'),
		'ship_state'=>$this->input->post('ship_to_state'),
		'ship_state_code'=>$this->input->post('ship_to_state_code'),
		// 'ship_gst'=>$this->input->post('ship_to_gst_no'),
		// 'ship_pan'=>$this->input->post('ship_to_pan_no'),
		'addedOn'=>date('Y-m-d H:i:s'),
		'addedBy'=>$_SESSION['logged_in']['user_id']);
		$this->db->insert('performa_invoice_address',$dr);

		/** ADD PERFORMA EXPORT SHIP DETAILS **/

		$dr=array(
			'record_id'=>$lid,
			// 'pre_carriage'=>$this->input->post('pre_carriage'),
			// 'carriage_receipt'=>$this->input->post('place_receipt_pre_carriage'),
			'country_of_origin'=>$this->input->post('country_origin_goods'),
			'country_of_final_destination'=>$this->input->post('country_final_destination'),
			// 'vessel'=>$this->input->post('vessel_flight_no'),
			'port_of_loading'=>$this->input->post('port_loading'),
			// 'terms_of_payment'=>$this->input->post('terms_payment'),
			 'port_of_discharge'=>$this->input->post('port_discharge'),
			// 'final_destination'=>$this->input->post('final_destination'),
			
			'addedOn'=>date('Y-m-d H:i:s'),
			'addedBy'=>$_SESSION['logged_in']['user_id']
			);

		$this->db->insert('performa_invoice_export_shipping_details',$dr);





	/** ADD ITEMS **/
	$desc_goodss=$this->input->post('desc_goods');

	for($i=0;$i<count($desc_goodss);$i++)
	{

		if($this->input->post('desc_goods')[$i]<>'')
		{
		$dr=array(
		'record_id'=>$lid,
		
		'description'=>$this->input->post('desc_goods')[$i],
		
		'qty'=>$this->input->post('qty')[$i],
		'uom'=>$this->input->post('uom')[$i],
		'rate'=>$this->input->post('rate_inr')[$i],
		// 'discount_per'=>$this->input->post('discount')[$i],
		'addedBy'=>$_SESSION['logged_in']['user_id'],
		'addedOn'=>date('Y-m-d H:i:s')
		);
		$this->db->insert('performa_invoice_items',$dr);
		}
	}

	if($task_id>0 && $task_id<>'')
	{
		$dr=array('task_status'=>1,'task_completed_on'=>date('Y-m-d H:i:s'),'task_completed_by'=>$_SESSION['logged_in']['user_id']);
		$this->db->where('id',$task_id);
		$this->db->update('task_department_wise_scheduling',$dr);

		if($this->shouldQueuePreReleaseDfTask($po_id, array(114, 86, 87, 2))){

		$tomorrow = date('Y-m-d', strtotime('+1 day'));
		$nextWorkingDate = $this->iftomorrowisholiday($tomorrow);
		/** ADD TASK 114 FILL DF **/
		$addnewdata = array('df_id'=>0,
				'taskid'=>114,
				'department_id'=>9,
				'start_date'=>date('Y-m-d'),
				'end_date'=>date('Y-m-d',strtotime($nextWorkingDate)),
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$this->session->userdata['logged_in']['user_id'],
				'po_id'=>$po_id,
				'task_status'=>0,
				'userid'=>$this->session->userdata['logged_in']['user_id'],
				'assigned_user'=>$this->session->userdata['logged_in']['user_id'],
				'assigned_by'=>$this->session->userdata['logged_in']['user_id'],
				'assigned_on'=>date('Y-m-d H:i:s'));

			$this->db->insert('task_department_wise_scheduling',$addnewdata);
	}
	}

	redirect(page_url.'Formats/export_performa_invoice/'.$po_id.'/'.$lead_id);


}
	

public function edit_performa_invoice(){

	$this->load->view('form/edit_performa_invoice');

}

function DeleteItems()
{
	$id=$this->input->post('id');
	$this->db->where('id',$id);
	$this->db->delete('performa_invoice_items');
	echo $this->db->affected_rows();
}



function update_domestic_pi()
{
	$po_id=$this->uri->segment(3);
	$lead_id=$this->uri->segment(4);
	$record_id=$this->uri->segment(5);
	$dr=array(

	'invoice_no'=>$this->input->post('prof_inv_no'),
	'invoice_date'=>$this->input->post('prof_inv_date'),
	'state'=>$this->input->post('state'),
	'place_of_supply'=>$this->input->post('place_supply'),
	'customer_po'=>$this->input->post('cust_po_no'),
	'customer_po_date'=>date('Y-m-d',strtotime($this->input->post('cust_po_date'))),
	'freight'=>$this->input->post('freight_terms'),
	'hsn'=>$this->input->post('hsn_code'),
	'remarks'=>$this->input->post('remarks'),
	'updatedOn'=>date('Y-m-d H:i:s'),
	'updatedBy'=>$_SESSION['logged_in']['user_id']);
	$this->db->where('id',$record_id);
	$this->db->update('performa_invoice',$dr);
	


	/** ADD SHIP BILL DETAIL **/
	$dr=array(
		
		'bill_to_name'=>$this->input->post('bill_to_name'),
		'bill_address'=>$this->input->post('bill_to_address'),
		'bill_state'=>$this->input->post('bill_to_state'),
		'bil_state_code'=>$this->input->post('bill_to_state_code'),
		'bill_gst'=>$this->input->post('bill_to_gst_no'),
		'bill_pan'=>$this->input->post('bill_to_pan_no'),
		'ship_to_name'=>$this->input->post('ship_to_name'),
		'ship_address'=>$this->input->post('ship_to_address'),
		'ship_state'=>$this->input->post('ship_to_state'),
		'ship_state_code'=>$this->input->post('ship_to_state_code'),
		'ship_gst'=>$this->input->post('ship_to_gst_no'),
		'ship_pan'=>$this->input->post('ship_to_pan_no'),
		'updatedOn'=>date('Y-m-d H:i:s'),
		'updatedBy'=>$_SESSION['logged_in']['user_id']);

		$this->db->where('record_id',$record_id);
		$this->db->update('performa_invoice_address',$dr);

	/** UPDATE EXISTING ITEMS **/

	$itemsIDs=$this->input->post('itemsID');
	for($i=0;$i<count($itemsIDs);$i++)
	{
		$iid=$itemsIDs[$i];
		$dr=array(
		// 'item_code'=>$this->input->post('item_code'.$iid),
		'description'=>$this->input->post('desc_goods'.$iid),
		// 'hsn'=>$this->input->post('hsn_code'.$iid),
		'qty'=>$this->input->post('qty'.$iid),
		'uom'=>$this->input->post('uom'.$iid),
		'rate'=>$this->input->post('rate_inr'.$iid),
		// 'discount_per'=>$this->input->post('discount'.$iid),
		'updatedBy'=>$_SESSION['logged_in']['user_id'],
		'updatedOn'=>date('Y-m-d H:i:s')
		);
		$this->db->where('id',$iid);
		$this->db->update('performa_invoice_items',$dr);
	}

	/** ADD ITEMS **/
	if($this->input->post('check')==1)
	{
	$item_code=$this->input->post('item_code');

	for($i=0;$i<count($item_code);$i++)
	{

		$dr=array(
		'record_id'=>$record_id,
		// 'item_code'=>$this->input->post('item_code')[$i],
		'description'=>$this->input->post('desc_goods')[$i],
		// 'hsn'=>$this->input->post('hsn_code')[$i],
		'qty'=>$this->input->post('qty')[$i],
		'uom'=>$this->input->post('uom')[$i],
		'rate'=>$this->input->post('rate_inr')[$i],
		// 'discount_per'=>$this->input->post('discount')[$i],
		'addedBy'=>$_SESSION['logged_in']['user_id'],
		'addedOn'=>date('Y-m-d H:i:s')
		);
		$this->db->insert('performa_invoice_items',$dr);
	}
	}
	
	redirect(page_url.'Formats/performa_invoice/'.$po_id.'/'.$lead_id);


}


public function edit_export_performa_invoice(){

	$this->load->view('form/edit_export_performa_invoice');

}


function update_export_pi()
{

	$po_id=$this->uri->segment(3);
	$lead_id=$this->uri->segment(4);
	$record_id=$this->uri->segment(5);
	$dr=array(
	'invoice_no'=>$this->input->post('prof_inv_no'),
	'invoice_date'=>$this->input->post('prof_inv_date'),
	'state'=>$this->input->post('state'),
	'place_of_supply'=>$this->input->post('place_supply'),
	'customer_po'=>$this->input->post('cust_po_no'),
	'customer_po_date'=>date('Y-m-d',strtotime($this->input->post('cust_po_date'))),
	'freight'=>$this->input->post('freight_terms'),
	'remarks'=>$this->input->post('remarks'),
	'buyer_order_no'=>$this->input->post('buyer_order_no'),
	'buyer_order_date'=>$this->input->post('buyer_order_date'),
	'hsn'=>$this->input->post('hsn_code'),
	// 'iec_code'=>$this->input->post('iec_code'),
	// 'rbi_code'=>$this->input->post('rbi_code'),
	// 'eepc_no'=>$this->input->post('eepc_no'),
	'addedOn'=>date('Y-m-d H:i:s'),
	'addedBy'=>$_SESSION['logged_in']['user_id']);
	$this->db->where('id',$record_id);
	$this->db->update('performa_invoice',$dr);
	

	/** ADD SHIP BILL DETAIL **/
	$dr=array(
		
		'bill_to_name'=>$this->input->post('bill_to_name'),
		'bill_address'=>$this->input->post('bill_to_address'),
		'bill_state'=>$this->input->post('bill_to_state'),
		'bil_state_code'=>$this->input->post('bill_to_state_code'),
		'bill_gst'=>$this->input->post('bill_to_gst_no'),
		'bill_pan'=>$this->input->post('bill_to_pan_no'),
		'ship_to_name'=>$this->input->post('ship_to_name'),
		'ship_address'=>$this->input->post('ship_to_address'),
		'ship_state'=>$this->input->post('ship_to_state'),
		'ship_state_code'=>$this->input->post('ship_to_state_code'),
		'ship_gst'=>$this->input->post('ship_to_gst_no'),
		'ship_pan'=>$this->input->post('ship_to_pan_no'),
		'addedOn'=>date('Y-m-d H:i:s'),
		'addedBy'=>$_SESSION['logged_in']['user_id']);
		$this->db->where('record_id',$record_id);
		$this->db->update('performa_invoice_address',$dr);

		/** ADD PERFORMA EXPORT SHIP DETAILS **/

		$dr=array(
			
			// 'pre_carriage'=>$this->input->post('pre_carriage'),
			// 'carriage_receipt'=>$this->input->post('place_receipt_pre_carriage'),
			'country_of_origin'=>$this->input->post('country_origin_goods'),
			'country_of_final_destination'=>$this->input->post('country_final_destination'),
			// 'vessel'=>$this->input->post('vessel_flight_no'),
			'port_of_loading'=>$this->input->post('port_loading'),
			// 'terms_of_payment'=>$this->input->post('terms_payment'),
			'port_of_discharge'=>$this->input->post('port_discharge'),
			// 'final_destination'=>$this->input->post('final_destination'),
			'addedOn'=>date('Y-m-d H:i:s'),
			'addedBy'=>$_SESSION['logged_in']['user_id']
			);
		$this->db->where('record_id',$record_id);
		$this->db->update('performa_invoice_export_shipping_details',$dr);


	/** UPDATE EXISTING ITEMS **/

	$itemsIDs=$this->input->post('itemsID');
	for($i=0;$i<count($itemsIDs);$i++)
	{
		$iid=$itemsIDs[$i];
		$dr=array(
		// 'item_code'=>$this->input->post('item_code'.$iid),
		'description'=>$this->input->post('desc_goods'.$iid),
		// 'hsn'=>$this->input->post('hsn_code'.$iid),
		'qty'=>$this->input->post('qty'.$iid),
		'uom'=>$this->input->post('uom'.$iid),
		'rate'=>$this->input->post('rate_inr'.$iid),
		// 'discount_per'=>$this->input->post('discount'.$iid),
		'updatedBy'=>$_SESSION['logged_in']['user_id'],
		'updatedOn'=>date('Y-m-d H:i:s')
		);
		$this->db->where('id',$iid);
		$this->db->update('performa_invoice_items',$dr);
	}

	/** ADD ITEMS **/
	if($this->input->post('check')==1)
	{
	$item_code=$this->input->post('item_code');

	for($i=0;$i<count($item_code);$i++)
	{

		$dr=array(
		'record_id'=>$record_id,
		// 'item_code'=>$this->input->post('item_code')[$i],
		'description'=>$this->input->post('desc_goods')[$i],
		// 'hsn'=>$this->input->post('hsn_code')[$i],
		'qty'=>$this->input->post('qty')[$i],
		'uom'=>$this->input->post('uom')[$i],
		'rate'=>$this->input->post('rate_inr')[$i],
		// 'discount_per'=>$this->input->post('discount')[$i],
		'addedBy'=>$_SESSION['logged_in']['user_id'],
		'addedOn'=>date('Y-m-d H:i:s')
		);
		$this->db->insert('performa_invoice_items',$dr);
	}
	}
	
	redirect(page_url.'Formats/export_performa_invoice/'.$po_id.'/'.$lead_id);


}


}
