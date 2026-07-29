<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Office_maintenance extends CI_Controller {
	
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
		$ip = $_SERVER["REMOTE_ADDR"];
		 $query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
            if($query->num_rows()=='0'){
            $this->session->set_flashdata('message','<span class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</span><br/>');
            redirect(page_url.'Dashboard');    
            }
	}
	public function index(){
		$this->load->view('maintenance/dashboard');
	}
	
	
	public function Reminder_meeting()
{
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('rm_date', 'RM date', 'required|trim');
	$this->form_validation->set_rules('rm_time', 'RM Time', 'required|trim');
	$this->form_validation->set_rules('rm_message', 'Message for Reminder', 'required|trim');
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('maintenance/reminders');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
           $date = date('Y-m-d',strtotime($this->input->post('rm_date')));
			$table = "reminders";
			$data=
			array('rm_date'=>$date,
			'rm_time'=>$this->input->post('rm_time'),
			'message'=>$this->input->post('rm_message'),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			
			$res = $this->db->insert('reminders',$data);
			if($res)
			{
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'Office_maintenance/Reminder_meeting');
				}
				
			}
	}
	
	public function reminders(){
		$this->load->view('maintenance/Reminder_meeting');
		
	}
	public function reminder_list()
	{
		$currency_data = array();
		$query = $this->db->select('id,rm_date, rm_time, message,added_on')->from('reminders')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			$edit = "<a href='".page_url."Office_maintenance/edit_reminder/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$delete = "<a href='".page_url."Office_maintenance/delete_reminders/".$row->id."'><i class='fa fa-trash'></i></a>";
			$currency_data[] = array('sr_no'=>$i,
			'rm_date'=>$row->rm_date,
			'rm_time'=>$row->rm_time,
			'message'=>$row->message,
			'edit'=>$edit." l ".$delete);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($currency_data),
	"iTotalDisplayRecords" => count($currency_data),
	"aaData"=>$currency_data);
/*while($row = $result->fetch_array(MYSQLI_ASSOC)){
  $results["data"][] = $row ;
}*/
 
echo json_encode($results);
}
	public function edit_reminder()
	{
		
		$this->load->view('maintenance/edit_reminder');
	}
	
	public function update_reminders()
{
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('rm_date', 'RM date', 'required|trim');
	$this->form_validation->set_rules('rm_time', 'RM Time', 'required|trim');
	$this->form_validation->set_rules('rm_message', 'Message for Reminder', 'required|trim');
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('maintenance/edit_reminder');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
           $date = date('Y-m-d',strtotime($this->input->post('rm_date')));
			$table = "reminders";
			$data=
			array('rm_date'=>$date,
			'rm_time'=>$this->input->post('rm_time'),
			'message'=>$this->input->post('rm_message'),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update('reminders',$data);
			if($res)
			{
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully updated.</span><br/>');
				redirect(page_url.'Office_maintenance/Reminder_meeting');
				}
			}
	}
	
	public function delete_reminders()
{
		$id = $this->uri->segment(3);
		 $this->db->where('id', $id);
   			$this->db->delete('reminders');
			$this->session->set_flashdata('message', '<span style="color:red;">Thank you! Record successfully deleted.</span>');
			redirect(page_url.'Office_maintenance/Reminder_meeting'); 

	}
	
	public function office_phone_testing(){
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('phone_area', 'Phone Area', 'required|trim');
	$this->form_validation->set_rules('testrpt', 'Test Report', 'required|trim');
	$this->form_validation->set_rules('report', 'Report', 'required|trim');
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('maintenance/office_phone_testing');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
          $table = "office_phone_testreport";
			$data=
			array('phone_area'=>$this->input->post('phone_area'),
			'testing_report'=>$this->input->post('testrpt'),
			'message'=>$this->input->post('report'),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			
			$res = $this->db->insert($table,$data);
			if($res)
			{
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'Office_maintenance/office_phone_testing');
				}
			
			}
	}
	public function Phone_test_report(){
		$this->load->view('maintenance/office_phone_testing');
		
	}
	
	public function test_Report_list()
	{
		$currency_data = array();
		$query = $this->db->select('*')->from('office_phone_testreport')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('Y-m-d', strtotime($row->added_on));
$time = date('H:i:s', strtotime($row->added_on));
$addedtime = "<br>". date('g:i A', strtotime($time)); 
			$edit = "<a href='".page_url."Office_maintenance/edit_phone_testing/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$delete = "<a href='".page_url."Office_maintenance/delete_phone_testing/".$row->id."'><i class='fa fa-trash'></i></a>";
			$currency_data[] = array('sr_no'=>$i,
			'added_on'=>$addeddate.''.$addedtime,
			'phone_area'=>$row->phone_area,
			'testing_report'=>$row->testing_report,
			'message'=>$row->message,
			'edit'=>$edit." l ".$delete);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($currency_data),
	"iTotalDisplayRecords" => count($currency_data),
	"aaData"=>$currency_data);
/*while($row = $result->fetch_array(MYSQLI_ASSOC)){
  $results["data"][] = $row ;
}*/
 
echo json_encode($results);
}

public function edit_phone_testing()
{
	$this->load->view('maintenance/edit_office_phone_testing');
}

public function update_phone_test_report(){
	
$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('phone_area', 'Phone Area', 'required|trim');
	$this->form_validation->set_rules('testrpt', 'Test Report', 'required|trim');
	$this->form_validation->set_rules('report', 'Report', 'required|trim');
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('maintenance/edit_office_phone_testing');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
          $table = "office_phone_testreport";
			$data=
			array('phone_area'=>$this->input->post('phone_area'),
			'testing_report'=>$this->input->post('testrpt'),
			'message'=>$this->input->post('report'),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);
			if($res)
			{
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully updated.</span><br/>');
				redirect(page_url.'Office_maintenance/Phone_test_report');
				}
				
			}
	}
	
public function delete_phone_testing()
{
		$id = $this->uri->segment(3);
		 $this->db->where('id', $id);
   			$this->db->delete('office_phone_testreport');
			$this->session->set_flashdata('message', '<span style="color:red;">Thank you! Record successfully deleted.</span>');
			redirect(page_url.'Office_maintenance/Phone_test_report'); 

	}
	
	public function Camera_testing(){
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('camera_loc', 'Camera Location', 'required|trim');
	$this->form_validation->set_rules('camera_report', 'Camera Report', 'required|trim');
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('maintenance/camera_testing');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
          $table = "camera_testing_report";
			$data=
			array('camera_location'=>$this->input->post('camera_loc'),
			'message'=>$this->input->post('camera_report'),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			
			$res = $this->db->insert($table,$data);
			if($res)
			{
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'Office_maintenance/Camera_testing');
				}
				
			}
	}
	public function Camera_test_report(){
		$this->load->view('maintenance/camera_testing');
		
	}
	
	public function Camera_Report_list()
	{
		$currency_data = array();
		$query = $this->db->select('*')->from('camera_testing_report')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('Y-m-d', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			$edit = "<a href='".page_url."Office_maintenance/edit_camera_testing/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$delete = "<a href='".page_url."Office_maintenance/delete_camera_testing/".$row->id."'><i class='fa fa-trash'></i></a>";
			$currency_data[] = array('sr_no'=>$i,
			'added_on'=>$addeddate.''.$addedtime,
			'camera_location'=>$row->camera_location,
			'message'=>$row->message,
			'edit'=>$edit." l ".$delete);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($currency_data),
	"iTotalDisplayRecords" => count($currency_data),
	"aaData"=>$currency_data);
/*while($row = $result->fetch_array(MYSQLI_ASSOC)){
  $results["data"][] = $row ;
}*/
 
echo json_encode($results);
}

public function edit_camera_testing()
{
	$this->load->view('maintenance/edit_camera_testing');
}

public function update_camera_test_report(){
	
$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
$this->form_validation->set_rules('camera_loc', 'Camera Location', 'required|trim');
$this->form_validation->set_rules('camera_report', 'Camera Report', 'required|trim');
$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('maintenance/edit_camera_testing');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
          $table = "camera_testing_report";
			$data=
			array('camera_location'=>$this->input->post('camera_loc'),
			'message'=>$this->input->post('camera_report'),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);
			if($res)
			{
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully updated.</span><br/>');
				redirect(page_url.'Office_maintenance/Camera_test_report');
				}
				
			}
	}
	
public function delete_camera_testing()
{
		$id = $this->uri->segment(3);
		 $this->db->where('id', $id);
   			$this->db->delete('camera_testing_report');
			$this->session->set_flashdata('message', '<span style="color:red;">Thank you! Record successfully deleted.</span>');
			redirect(page_url.'Office_maintenance/Camera_test_report'); 

	}

	public function Electricity_load()
{
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('elecricity_load_date', 'Date', 'required|trim');
	$this->form_validation->set_rules('elecricity_load', 'Electricity Load', 'required|trim');
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('maintenance/Electricity_load');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
           $date = date('Y-m-d',strtotime($this->input->post('elecricity_load_date')));
			$table = "electricity_load_whn_office_close";
			$data=
			array('todaydate'=>$date,
			'electricity_load'=>$this->input->post('elecricity_load'),
			'remarks'=>$this->input->post('remarks'),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			
			$res = $this->db->insert('electricity_load_whn_office_close',$data);
			if($res)
			{
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'Office_maintenance/');
				}
				
			}
	}
	
	public function view_electricity_load_when_office_close(){
		$this->load->view('maintenance/Electricity_load');
	}
	
	public function view_electricity_load_report(){
	$currency_data = array();
		$query = $this->db->select('*')->from('electricity_load_whn_office_close')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('Y-m-d', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			$edit = "<a href='".page_url."Office_maintenance/edit_electricity_load/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$delete = "<a href='".page_url."Office_maintenance/delete_elecricity_load/".$row->id."'><i class='fa fa-trash'></i></a>";
			$currency_data[] = array('sr_no'=>$i,
			'date'=>$row->todaydate,
			'electricity_load'=>$row->electricity_load,
			'remarks'=>$row->remarks,
			'added_on'=>$addeddate."".$addedtime,
			'edit'=>$edit." l ".$delete);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($currency_data),
	"iTotalDisplayRecords" => count($currency_data),
	"aaData"=>$currency_data);
	echo json_encode($results);
}

public function edit_electricity_load(){
	
	$this->load->view('maintenance/edit_electricity_load');
}

public function update_electricity_load()
{
$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('elecricity_load_date', 'Date', 'required|trim');
	$this->form_validation->set_rules('elecricity_load', 'Electricity Load', 'required|trim');
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('maintenance/edit_electricity_load');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
           $date = date('Y-m-d',strtotime($this->input->post('elecricity_load_date')));
			$table = "electricity_load_whn_office_close";
			$data=
			array('todaydate'=>$date,
			'electricity_load'=>$this->input->post('elecricity_load'),
			'remarks'=>$this->input->post('remarks'),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update('electricity_load_whn_office_close',$data);
			if($res)
			{
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully updated.</span><br/>');
				redirect(page_url.'Office_maintenance/Electricity_load');
				}
				
			}	
	
}

public function delete_elecricity_load()
{
		$id = $this->uri->segment(3);
		 $this->db->where('id', $id);
   			$this->db->delete('electricity_load_whn_office_close');
			$this->session->set_flashdata('message', '<span style="color:red;">Thank you! Record successfully deleted.</span>');
			redirect(page_url.'Office_maintenance/Electricity_load'); 

	}
	
public function unpaid_bills()
{
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('particular', 'particular', 'required|trim');
	$this->form_validation->set_rules('bill_payment_Date', 'Date', 'required|trim');
	$this->form_validation->set_rules('bill_amount', 'bill_amount', 'required|trim');
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('maintenance/unpaid_bills');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
           $date = date('Y-m-d',strtotime($this->input->post('bill_payment_Date')));
			$table = "unpaid_bills";
			$data=
			array('billdate'=>$date,
			'particular'=>$this->input->post('particular'),
			'bill_amount'=>$this->input->post('bill_amount'),
			'remarks'=>$this->input->post('remarks'),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			
			$res = $this->db->insert('unpaid_bills',$data);
			if($res)
			{
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'Office_maintenance/unpaid_bills');
				}
				
			}
	}
	
	
	public function view_unpaid_bills_report(){
	$currency_data = array();
		$query = $this->db->select('*')->from('unpaid_bills')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('Y-m-d', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			$edit = "<a href='".page_url."Office_maintenance/edit_unpaid_bills/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$delete = "<a href='".page_url."Office_maintenance/delete_uppaid_bills/".$row->id."'><i class='fa fa-trash'></i></a>";
			$currency_data[] = array('sr_no'=>$i,
			'billdate'=>$row->billdate,
			'particular'=>$row->particular,
			'bill_amount'=>$row->bill_amount,
			'remarks'=>$row->remarks,
			'added_on'=>$addeddate."".$addedtime,
			'edit'=>$edit." l ".$delete);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($currency_data),
	"iTotalDisplayRecords" => count($currency_data),
	"aaData"=>$currency_data);
	echo json_encode($results);
}

public function edit_unpaid_bills(){
	
	$this->load->view('maintenance/edit_unpaid_bills');
}

public function update_unpaid_bills()
{
$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('particular', 'particular', 'required|trim');
	$this->form_validation->set_rules('bill_payment_Date', 'Date', 'required|trim');
	$this->form_validation->set_rules('bill_amount', 'bill_amount', 'required|trim');
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('maintenance/edit_unpaid_bills');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
           $date = date('Y-m-d',strtotime($this->input->post('bill_payment_Date')));
			$table = "unpaid_bills";
			$data=
			array('billdate'=>$date,
			'particular'=>$this->input->post('particular'),
			'bill_amount'=>$this->input->post('bill_amount'),
			'remarks'=>$this->input->post('remarks'),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update('unpaid_bills',$data);
			if($res)
			{
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'Office_maintenance/unpaid_bills');
				}
			}
}

public function delete_uppaid_bills()
{
		$id = $this->uri->segment(3);
		 $this->db->where('id', $id);
   			$this->db->delete('unpaid_bills');
			$this->session->set_flashdata('message', '<span style="color:red;">Thank you! Record successfully deleted.</span>');
			redirect(page_url.'Office_maintenance/unpaid_bills'); 

	}
}
