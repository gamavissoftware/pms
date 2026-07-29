<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reminder extends CI_Controller {
	
	public function __construct()
	{
parent::__construct();
$this->load->model('User_model','user');
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
		
$user_id =$this->session->userdata['logged_in']['user_id'];
	if(empty($user_id))
         {
         redirect(site_url(),'refresh');
         }
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

public function index()
	{
	$this->load->view('reminder/reminder');
	}
public function add_new_reminder()
	{
	    
	  $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('task_name', 'Task Name', 'required|trim');
	$this->form_validation->set_rules('detail', 'Detail', 'required|trim');
	$this->form_validation->set_rules('billing_date', 'Billing Date', 'required|trim');
	$this->form_validation->set_rules('tasktype', 'Task Type', 'required|trim');
	
	$this->form_validation->set_rules('assign_to', 'Assign To', 'required|trim');
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
				$this->load->view('reminder/reminder');
			}else
		{
		
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "reminder_tasks";
		$reminderscheduler = $this->input->post('reminderin');
		if(!empty($reminderscheduler)){
		//echo "<pre>"; print_r($reminderscheduler); exit;
		if(count($reminderscheduler)>0){
		    $checked = implode(',', $reminderscheduler);
		}
		}else{
			$checked="";
		}
		
						
		$data = array('task_name'=>$this->input->post('task_name'),
		'detail'=>$this->input->post('detail'),
		'billing_date'=>date('Y-m-d',strtotime($this->input->post('billing_date'))),
		'reminder_date'=>date('Y-m-d',strtotime($this->input->post('reminder_date'))),
		'assign_to'=>$this->input->post('assign_to'),
		'reminder_schedule'=>$checked,
		'tasktype'=>$this->input->post('tasktype'),
		'added_on'=>$date,
		'added_by'=>$_SESSION['logged_in']['user_id']);


		$result  = $this->db->insert($table,$data);	
		$id= $this->db->insert_id();
		if($result)
		{
		 
		 $query = $this->db->select('tasktype, reminder_schedule, reminder_date')->from('reminder_tasks')->where('id',$id)->get();   
		 foreach($query->result() as $row);
		     $reminder_date = $row->reminder_date;
		     if($row->tasktype=='One time'){
		         
		         $scheduleddate = "";
		         $scheduleddate1 = "";
		         $scheduleddate2 = "";
		     }else{
		         $str_arr = explode (",", $row->reminder_schedule); 
		         if($str_arr[0]=='2'){
		             $days= "2";
                $date = strtotime("+".$days." days", strtotime($reminder_date));
                $scheduleddate =  date("Y-m-d", $date);
                 }else if($str_arr[0]=='5'){
                    $days= "5";
                    $date = strtotime("+".$days." days", strtotime($reminder_date));
                    $scheduleddate =  date("Y-m-d", $date);
                
		         }else if($str_arr[0]=='7'){
		           $days= "7";
                    $date = strtotime("+".$days." days", strtotime($reminder_date));
                    $scheduleddate =  date("Y-m-d", $date);  
		         }else{
		            $scheduleddate=""; 
		         }
		         if($str_arr[1]=='2'){
		             $days= "2";
                    $date = strtotime("+".$days." days", strtotime($reminder_date));
                    $scheduleddate1 =  date("Y-m-d", $date);
		             
		         }else if($str_arr[1]=='5'){
		             $days= "5";
                    $date = strtotime("+".$days." days", strtotime($reminder_date));
                    $scheduleddate1 =  date("Y-m-d", $date);
		             
		         }else if($str_arr[1]=='7'){
		               $days= "7";
                    $date = strtotime("+".$days." days", strtotime($reminder_date));
                    $scheduleddate1 =  date("Y-m-d", $date);
		             
		         }else{
		             $scheduleddate1="";
		         }
		         if($str_arr[2]=='2'){
		               $days= "2";
                    $date = strtotime("+".$days." days", strtotime($reminder_date));
                    $scheduleddate2 =  date("Y-m-d", $date);
		             
		         }else if($str_arr[2]=='5'){
		             $days= "5";
                    $date = strtotime("+".$days." days", strtotime($reminder_date));
                    $scheduleddate2 =  date("Y-m-d", $date);
		             
		         }else if($str_arr[2]=='7'){
		             $days= "7";
                    $date = strtotime("+".$days." days", strtotime($reminder_date));
                    $scheduleddate2 =  date("Y-m-d", $date);
		             
		         }else{
		            $scheduleddate2=""; 
		         }
		         
		         
		             
		         }
		     }
		     
		   $data = array('reminder_date1'=>$scheduleddate,
		   'reminder_date2'=>$scheduleddate1,
		   'reminder_3'=>$scheduleddate2);  
		   $this->db->where('id',$id);
		   $this->db->update('reminder_tasks',$data);
		 
		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
		redirect(page_url.'Reminder');

		}  
		
	  
	  }  
	    

	
	public function reminder_dashboard()
	{
		$this->load->view('reminder/reminder_list');
	}
	public function reminder_list()
	{
		$i=1;
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$vendor_data= array();
		$this->db->distinct();
		$this->db->select('a.*, b.first_name, b.last_name, c.first_name as firstname, c.last_name as lastname')->from('reminder_tasks a')->join('system_users b','a.assign_to=b.user_id','left')->join('system_users c','a.added_by=c.user_id','left')->where('a.added_by',$user_id);
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
		
		$addedby = $row->firstname." ".$row->lastname;
		$edit = "<a href='".page_url."Reminder/edit_reminder/".$row->id."'><i class='fa fa-pencil-square-o'></i></a>";
			$vendor_data[] = array('sr_no'=>$i,
			'task_name'=>$row->task_name,
			'detail'=>$row->detail,
			'billing_date'=>date('d-m-Y',strtotime($row->billing_date)),
			'reminder_date'=>date('d-m-Y',strtotime($row->reminder_date)),
			'tasktype'=>$row->tasktype,
			'assigned_to'=>$row->first_name." ".$row->last_name,
			'action'=>$edit,
			'added_by'=>$addedby);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($vendor_data),
			"iTotalDisplayRecords" => count($vendor_data),
			"aaData"=>$vendor_data);
			
		echo json_encode($results);
	}
	
	public function edit_reminder(){
		$this->load->view('reminder/edit_reminder');
	}
	
public function update_reminder()
	{
	    
	$this->form_validation->set_rules('task_name', 'Task Name', 'required|trim');
	$this->form_validation->set_rules('detail', 'Detail', 'required|trim');
	$this->form_validation->set_rules('billing_date', 'Billing Date', 'required|trim');
	$this->form_validation->set_rules('tasktype', 'Task Type', 'required|trim');
	$this->form_validation->set_rules('assign_to', 'Assign To', 'required|trim');
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
				$this->load->view('reminder/edit_reminder');
			}else
		{
		
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "reminder_tasks";
					
		$data = array('task_name'=>$this->input->post('task_name'),
		'billing_date'=>date('Y-m-d',strtotime($this->input->post('billing_date'))),
		'reminder_date'=>date('Y-m-d',strtotime($this->input->post('reminder_date'))),
		'assign_to'=>$this->input->post('assign_to'),
		'added_on'=>$date,
		'detail'=>$this->input->post('detail'),
		'added_by'=>$_SESSION['logged_in']['user_id']);

        $this->db->where('id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
		redirect(page_url.'Reminder/reminder_dashboard');

		}  
		}  
	 
	    
	}

function getreminderdate(){
	$tasktype= $this->input->post('tasktype');
	$billing_date= date('Y-m-d',strtotime($this->input->post('billing_date')));
	if($tasktype=='Monthly'){
	$reminderdate = date('d-m-Y',(strtotime ( '+30 day' , strtotime ( $billing_date) ) ));
echo $reminderdate; exit;	
	}else if($tasktype=='Half Yearly'){
	$reminderdate = date('d-m-Y',(strtotime ( '+182 day' , strtotime ( $billing_date) ) ));
echo $reminderdate; exit;	
	}else if($tasktype=='Yearly'){
	$reminderdate = date('d-m-Y',(strtotime ( '+365 day' , strtotime ( $billing_date) ) ));
echo $reminderdate; exit;	
	}else{
		$reminderdate="";
	}
	
	
}


	public function taskdashboard(){
		$this->load->view('dashboard/task_dashboard');
	}
}
