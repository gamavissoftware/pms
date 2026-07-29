<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Power_factor extends CI_Controller {
	
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
		
	}

	public function index()
	{
		
		$this->load->view('power_factor/add_office');
	}
    
    public function diesel_dashboard(){
		$this->load->view('power_factor/dashboard');
	}
	public function electricity_dashboard(){
		$this->load->view('power_factor/electricity_dashboard');
	}
	
	public function generator_dashboard(){
		$this->load->view('power_factor/generator_dashboard');
	}
	
	public function list_offices()
	{
		$business_data = array();
		$this->db->select('a.*,b.country_id,b.country_name,c.state_name,c.state_id,d.city_id,d.city_name')->from('offices a');
		$this->db->join('countries b','a.country_id=b.country_id','left');
		$this->db->join('states c','a.state_id=c.state_id','left');
		$this->db->join('cities d','a.city_id=d.city_id','left');
		$query = $this->db->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;									
		$i=1;
		foreach($res as $row)
		{
			$status = $row->office_status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Power_factor/update_office_status/".$row->office_id."/".$row->office_status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Power_factor/update_office_status/".$row->office_id."/".$row->office_status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
				$edit = "<a href='".page_url."Power_factor/edit_office_detail/".$row->office_id."'><i class='fa fa-pencil'></i></a>";
			$add = "<a href='".page_url."Power_factor/add_reading/".$row->office_id."'><span class='btn btn-warning btn-xs'>Add/View Reading</span></a>";	
			$add_diesel = "<a href='".page_url."Power_factor/add_diesel_qty/".$row->office_id."'><span class='btn btn-danger btn-xs'>Add/View Quantity</span></a>";	
			$business_data[] = array('sr_no'=>$i,
			'country_name'=>$row->country_name,
			'state_name'=>$row->state_name,
			'city_name'=>$row->city_name,
			'company_name'=>$row->company_name,
			'address'=>$row->address,
			'add_reading'=>$add,
			'add_diesel'=>$add_diesel,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
		//echo "<pre>"; print_r($business_data); exit;
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($business_data),
			"iTotalDisplayRecords" => count($business_data),
			"aaData"=>$business_data);
			
		echo json_encode($results);
	}
	
	
	public function add()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('country_name', 'Country Name', 'required|trim');
		$this->form_validation->set_rules('state', 'State Name', 'required|trim');
		$this->form_validation->set_rules('company_name', 'Company Name', 'required|trim');
		$this->form_validation->set_rules('city_name', 'City Name', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('power_factor/add_office');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "offices";
			$data = array('country_id'=>$this->input->post('country_name'),
			'state_id'=>$this->input->post('state'),
			'city_id'=>$this->input->post('city_name'),
			'company_name'=>$this->input->post('company_name'),
			'address'=>$this->input->post('address'),
			'office_status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Power_factor');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Power_factor');
		}
		
	}
		
	}
	
	public function update_office_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "office_id";
		$table = "offices";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('office_status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', 'Status successfully updated.');
			redirect('Power_factor');
		}
	
	public function edit_office_detail()
	{
		$this->load->view('power_factor/edit_office');
		
	}
	
	public function update_office_detail()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('country_name', 'Country Name', 'required|trim');
		$this->form_validation->set_rules('state', 'State Name', 'required|trim');
		$this->form_validation->set_rules('company_name', 'Company Name', 'required|trim');
		$this->form_validation->set_rules('city_name', 'City Name', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('power_factor/edit_office');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "offices";	
		$identifier = $this->uri->segment(3);
		$field_name = "office_id";		
		$data = array('country_id'=>$this->input->post('country_name'),
			'state_id'=>$this->input->post('state'),
			'city_id'=>$this->input->post('city_name'),
			'company_name'=>$this->input->post('company_name'),
			'address'=>$this->input->post('address'),
			'office_status'=>$this->input->post('status'),
			'updated_on'=>$date,
			'added_by'=>$user_id);
		$result  = $this->master->update_records($table,$data,$identifier,$field_name);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'Power_factor');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Power_factor');	
			
		}
			
	}
		
	}
	
	public function reading_report()
	{
		$power_factor_data = array();
		$this->db->select('*');
		$this->db->from('power_factors');
		$this->db->where('office_id',$this->uri->segment(3));
		$this->db->order_by('reading_date','desc');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			$edit = "<a href='".page_url."Power_factor/edit_reading_detail/".$row->id."/".$this->uri->segment(3)."'><i class='fa fa-pencil'></i></a>";
			$addeddate = date('Y-m-d', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time));
			$power_factor_data[] = array('sr_no'=>$i,
			'reading_date'=>$row->reading_date,
			'morning_kwh'=>$row->morning_kwh,
			'morning_kvah'=>$row->morning_kvah,
			'evening_kwh'=>$row->evening_kwh,
			'evening_kvah'=>$row->evening_kvah,
			'unit_consumed_morning_kwh'=>$row->unit_consumed_morning_kwh,
			'unit_consumed_morning_kvah'=>$row->unit_consumed_morning_kvah,
			'unit_consumed_evening_kwh'=>$row->unit_consumed_evening_kwh,
			'unit_consumed_evening_kvah'=>$row->unit_consumed_evening_kvah,
			'remarks'=>$row->remarks,
			'added_time'=>$addeddate.$addedtime,
			'edit'=>$edit);
			$i++;
		}
		//echo "<pre>"; print_r($business_data); exit;
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($power_factor_data),
			"iTotalDisplayRecords" => count($power_factor_data),
			"aaData"=>$power_factor_data);
			
		echo json_encode($results);
	}
	
	
	public function add_reading()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('reading_date', 'Date', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('power_factor/add_reading');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "power_factors";
		$date = date('Y-m-d',strtotime($this->input->post('reading_date')));
		$query = $this->db->select('office_id, reading_date')->from('power_factors')->where('office_id',$this->uri->segment(3))->where('reading_date',$date)->get();
		$res = $query->result();
		if($res){
		$this->session->set_flashdata('message','Sorry!, This record already exist.');
		redirect(page_url.'Power_factor/add_reading/'.$this->uri->segment(3));	
			
		}else{
			
		
			$aday_past = date('Y-m-d', strtotime($date . ' -1 day'));
			$qry  = $this->db->select('*')->from('power_factors')->where('reading_date',$aday_past)->where('office_id',$this->uri->segment(3))->get();
			//echo "<pre>"; print_r($qry->result()); exit;
			foreach($qry->result() as $last_date_data)
			$lastdatedata= "0";
			$lastdatedata_evn_kvah = "0";
			
			if($last_date_data->evening_kwh==''){
				$lastdatedata = "0";
			}else{
				$lastdatedata = $last_date_data->evening_kwh;
			}
			if($last_date_data->evening_kvah==''){
				$lastdatedata_evn_kvah = "0";
			}else{
				$lastdatedata_evn_kvah = $last_date_data->evening_kvah;
			}
			
			$morning_kwh = $this->input->post('morning_kwh');
			$morning_kvah = $this->input->post('morning_kvah');
			$evening_kwh = $this->input->post('evening_kwh');
			$evening_kvah = $this->input->post('evening_kvah');
			
			$last_night_value_in_kwh =$lastdatedata;
			$last_night_value_in_kvah = $lastdatedata_evn_kvah;
			/*Calculate Todays value-last night value = Consumed*/
			$morning_entry_kwh = $morning_kwh - $last_night_value_in_kwh;
			$morning_entry_kvah = $morning_kvah - $last_night_value_in_kvah;
			/*Calculate Todays value-last night value = Consumed*/
			$evening_cal_kwh = $evening_kwh-$morning_kwh;
			$evening_cal_kvah = $evening_kvah-$morning_kvah;
			
			
			$data = array('office_id'=>$this->uri->segment(3),
			'reading_date'=>$date,
			'morning_kwh'=>$this->input->post('morning_kwh'),
			'morning_kvah'=>$this->input->post('morning_kvah'),
			'evening_kwh'=>$this->input->post('evening_kwh'),
			'evening_kvah'=>$this->input->post('evening_kvah'),
			'unit_consumed_morning_kwh'=>$morning_entry_kwh,
			'unit_consumed_morning_kvah'=>$morning_entry_kvah,
			'unit_consumed_evening_kwh'=>$evening_cal_kwh,
			'unit_consumed_evening_kvah'=>$evening_cal_kvah,
			'remarks'=>$this->input->post('remarks'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Power_factor/add_reading/'.$this->uri->segment(3));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Power_factor/add_reading/'.$this->uri->segment(3));
		}
		}
	}
		
	}
	
	
	public function edit_reading_detail()
	{
		$this->load->view('power_factor/edit_power_factor');
		
	}
	
	public function update_reading_detail()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('reading_date', 'Reading Date', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('power_factor/edit_power_factor');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$addeddate =  date('Y-m-d H:i:s'); 
		$table = "power_factors";	
		$identifier = $this->uri->segment(3);
		$field_name = "id";		
		
		
			$date = date('Y-m-d',strtotime($this->input->post('reading_date')));
			$aday_past = date('Y-m-d', strtotime($date . ' -1 day'));
			$qry  = $this->db->select('*')->from('power_factors')->where('reading_date',$aday_past)->where('office_id',$this->uri->segment(4))->get();
			foreach($qry->result() as $last_date_data)
			$lastdatedata= "0";
			$lastdatedata_evn_kvah = "0";
			
			if($last_date_data->evening_kwh==''){
				$lastdatedata = "0";
			}else{
				$lastdatedata = $last_date_data->evening_kwh;
			}
			if($last_date_data->evening_kvah==''){
				$lastdatedata_evn_kvah = "0";
			}else{
				$lastdatedata_evn_kvah = $last_date_data->evening_kvah;
			}
			
			$morning_kwh = $this->input->post('morning_kwh');
			$morning_kvah = $this->input->post('morning_kvah');
			$evening_kwh = $this->input->post('evening_kwh');
			$evening_kvah = $this->input->post('evening_kvah');
			
			$last_night_value_in_kwh =$lastdatedata;
			$last_night_value_in_kvah = $lastdatedata_evn_kvah;
			/*Calculate Todays value-last night value = Consumed*/
			$morning_entry_kwh = $morning_kwh - $last_night_value_in_kwh;
			$morning_entry_kvah = $morning_kvah - $last_night_value_in_kvah;
			/*Calculate Todays value-last night value = Consumed*/
			$evening_cal_kwh = $evening_kwh-$morning_kwh;
			$evening_cal_kvah = $evening_kvah-$morning_kvah;
			
			
			$data = array('office_id'=>$this->uri->segment(4),
			'reading_date'=>$date,
			'morning_kwh'=>$this->input->post('morning_kwh'),
			'morning_kvah'=>$this->input->post('morning_kvah'),
			'evening_kwh'=>$this->input->post('evening_kwh'),
			'evening_kvah'=>$this->input->post('evening_kvah'),
			'unit_consumed_morning_kwh'=>$morning_entry_kwh,
			'unit_consumed_morning_kvah'=>$morning_entry_kvah,
			'unit_consumed_evening_kwh'=>$evening_cal_kwh,
			'unit_consumed_evening_kvah'=>$evening_cal_kvah,
			'remarks'=>$this->input->post('remarks'),
			'added_on'=>$addeddate,
			'added_by'=>$user_id);
			
			$result  = $this->master->update_records($table,$data,$identifier,$field_name);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'Power_factor/add_reading/'.$this->uri->segment(4));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Power_factor/add_reading/'.$this->uri->segment(4));	
			
		}
			
	}
		
	}
public function diesel_qty_report()
	{
		$power_factor_data = array();
		$this->db->select('*');
		$this->db->from('diesel_quantity');
		$this->db->where('office_id',$this->uri->segment(3));
		$this->db->order_by('reading_date','desc');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			$edit = "<a href='".page_url."Power_factor/edit_diesel_qty_detail/".$row->id."/".$this->uri->segment(3)."'><i class='fa fa-pencil'></i></a>";
			$addeddate = date('Y-m-d', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time));
			$power_factor_data[] = array('sr_no'=>$i,
			'reading_date'=>$row->reading_date,
			'morning_qty'=>$row->morning_qty,
			'evening_qty'=>$row->evening_qty,
			'morning_consumed_Data'=>$row->total_qty_consumed_morning,
			'evening_consumed_data'=>$row->total_qty_consumed_evening,
			'remarks'=>$row->remarks,
			'added_on'=>$addeddate.$addedtime,
			'edit'=>$edit);
			$i++;
		}
		//echo "<pre>"; print_r($business_data); exit;
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($power_factor_data),
			"iTotalDisplayRecords" => count($power_factor_data),
			"aaData"=>$power_factor_data);
			
		echo json_encode($results);
	}
	
	
	public function add_diesel_qty()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('reading_date', 'Date', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('power_factor/add_diesel_qty');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "diesel_quantity";
		$date = date('Y-m-d',strtotime($this->input->post('reading_date')));
		$query = $this->db->select('office_id, reading_date')->from('diesel_quantity')->where('office_id',$this->uri->segment(3))->where('reading_date',$date)->get();
		$res = $query->result();
		if($res){
		$this->session->set_flashdata('message','Sorry!, This record already exist.');
		redirect(page_url.'Power_factor/add_diesel_qty/'.$this->uri->segment(3));	
			
		}else{
			
			
			$aday_past = date('Y-m-d', strtotime($date . ' -1 day'));
			$morning_reading = $this->input->post('morning_qty');
			$evening_reading = $this->input->post('evening_qty');
		    if($morning_reading!=='' && $evening_reading!==''){
			$consumed_unit = $morning_reading-$evening_reading;
		    }else if($evening_reading=='0'){
		       $consumed_unit = "0"; 
		    }else{
		       $consumed_unit = "0";  
		    }
			$data = array('office_id'=>$this->uri->segment(3),
			'reading_date'=>$date,
			'morning_qty'=>$this->input->post('morning_qty'),
			'evening_qty'=>$this->input->post('evening_qty'),
			'total_qty_consumed_morning'=>$consumed_unit,
			'remarks'=>$this->input->post('remarks'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Power_factor/add_diesel_qty/'.$this->uri->segment(3));	
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Power_factor/add_diesel_qty/'.$this->uri->segment(3));	
		}
		}
	}
		
	}
	
	
	public function edit_diesel_qty_detail()
	{
		$this->load->view('power_factor/edit_diesel_qty');
		
	}
	
	public function update_diesel_qty_detail()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('reading_date', 'Reading Date', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('power_factor/edit_diesel_qty');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$added_date =  date('Y-m-d H:i:s'); 
		$table = "diesel_quantity";	
		$identifier = $this->uri->segment(3);
		$field_name = "id";	
       $date = date('Y-m-d',strtotime($this->input->post('reading_date')));		
		$aday_past = date('Y-m-d', strtotime($date . ' -1 day'));
		$morning_reading = $this->input->post('morning_qty');
		$evening_reading = $this->input->post('evening_qty');
		 if($morning_reading!=='' && $evening_reading!==''){
			$consumed_unit = $morning_reading-$evening_reading;
		    }else if($evening_reading=='0'){
		       $consumed_unit = "0"; 
		    }else{
		        $consumed_unit = "0"; 
		    }
		$data = array('office_id'=>$this->uri->segment(4),
			'reading_date'=>$date,
			'morning_qty'=>$this->input->post('morning_qty'),
			'total_qty_consumed_morning'=>$consumed_unit,
			'evening_qty'=>$this->input->post('evening_qty'),
			'remarks'=>$this->input->post('remarks'),
			'updated_on'=>$added_date,
			'updated_by'=>$user_id);
		
		$result  = $this->master->update_records($table,$data,$identifier,$field_name);	
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
				redirect(page_url.'Power_factor/add_diesel_qty/'.$this->uri->segment(4));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
				redirect(page_url.'Power_factor/add_diesel_qty/'.$this->uri->segment(4));	
			
		}
			
	}
		
	}


public function add_generator_reading()
	{
		$this->load->view('power_factor/add_generator_reading');

	}
	public function edit_generator_reading()
	{
		$this->load->view('power_factor/edit_generator_reading');

	}

	public function addd_reading()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('morning_reading', 'Morning Reading', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('power_factor/add_generator_reading');
		}
		else
		{
		$query = $this->db->select('comapny_id, date')->from('generator_reading')->where('comapny_id',$this->uri->segment(3))->where('date',$this->input->post('create_date'))->get();
		$res = $query->result();
		if($res){
		$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry! This record already exist.</div>');
		redirect(page_url.'Power_factor/add_generator_reading/'.$this->uri->segment(3));
		}else{
			$date = $this->input->post('create_date');
			$aday_past = date('Y-m-d', strtotime($date . ' -1 day'));
			$qry  = $this->db->select('*')->from('generator_reading')->where('date',$aday_past)->where('comapny_id',$this->uri->segment(3))->get();
			foreach($qry->result() as $last_date_data)
			$lastdatedata= "0";
			$lastdatedata_evn = "0";
			
			if($last_date_data->evening_reading==''){
				$lastdatedata = "0";
			}else{
				$lastdatedata = $last_date_data->evening_reading;
			}
			$morning_reading = $this->input->post('morning_reading');
			$evening_reading = $this->input->post('evening_reading');
			$last_night_value =$lastdatedata;
			$morning_consumed_data = $morning_reading - $last_night_value;
			$evening_consumed_data = $evening_reading-$morning_reading;
			
			
		$data = array(
		'comapny_id' => $this->uri->segment(3),
		'date' =>$this->input->post('create_date'),
		'morning_reading' => $this->input->post('morning_reading'),
		'evening_reading' => $this->input->post('evening_reading'),
		'morning_consumed_data' =>$morning_consumed_data,
		'evening_consumed_data' =>$evening_consumed_data,
		'remarks' =>$this->input->post('remarks'),
		'addedOn'=>date("Y-m-d h:i:s"),
		'added_by'=>$user_id);
		}
	$this->db->insert('generator_reading', $data);
	$this->session->set_flashdata('message','<div class="alert alert-success">Thank You! Record successfully added.</div>');
	redirect(page_url.'Power_factor/add_generator_reading/'.$this->uri->segment(3));
   
		}
		


	}
	public function show_reading_list(){

	  $read_data = array();
		$this->db->select('*')->from('generator_reading')->where('comapny_id',$this->uri->segment(3));
		$this->db->order_by('date','ASC');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$edit = "<a href='".page_url."Power_factor/edit_generator_reading/".$row->id."/".$row->comapny_id."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";
			$read_data[] = array('sr_no'=>$i,
			'comapny_id'=>$row->comapny_id,
			'date'=>$row->date,
			'morning_reading'=>$row->morning_reading,
			'evening_reading'=>$row->evening_reading,
			'morning_consumed_data'=>$row->morning_consumed_data,
			'evening_consumed_data'=>$row->evening_consumed_data,
			'remarks'=>$row->remarks,
			'edit'=>$edit);
			$i++;
		}
		//echo "<pre>"; print_r($compititor_data); exit;
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($read_data),
			"iTotalDisplayRecords" => count($read_data),
			"aaData"=>$read_data);

		echo json_encode($results);
  }

	public function update_generator_reading()
	  {
		  
		  $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('morning_reading', 'Morning Reading', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('power_factor/add_generator_reading');
		}
		else
		{
			$date = $this->input->post('create_date');
			$aday_past = date('Y-m-d', strtotime($date . ' -1 day'));
			$qry  = $this->db->select('*')->from('generator_reading')->where('date',$aday_past)->where('comapny_id',$this->uri->segment(3))->get();
			foreach($qry->result() as $last_date_data)
			$lastdatedata= "0";
			$lastdatedata_evn = "0";
			
			if($last_date_data->evening_reading==''){
				$lastdatedata = "0";
			}else{
				$lastdatedata = $last_date_data->evening_reading;
			}
			$morning_reading = $this->input->post('morning_reading');
			$evening_reading = $this->input->post('evening_reading');
			$last_night_value =$lastdatedata;
			$morning_consumed_data = $morning_reading - $last_night_value;
			$evening_consumed_data = $evening_reading-$morning_reading;
			
	    	$id=$this->uri->segment(3);
			$table="generator_reading";
				
		$data = array(
		'date' =>$this->input->post('create_date'),
		'morning_reading'=> $this->input->post('morning_reading'),
		'evening_reading'=> $this->input->post('evening_reading'),
		'morning_consumed_data'=>$morning_consumed_data,
		'evening_consumed_data'=>$evening_consumed_data,
		'remarks'=>trim($this->input->post('remarks')),
		'updated_on'=>date("Y-m-d h:i:s"),
		'added_by'=>$user_id);
	
		$this->db->where('id',$id);
		$this->db->update($table,$data);
	    $this->session->set_flashdata('success','<div class="alert alert-success">Data  has been updated successfully.</div>');
		redirect(page_url."Power_factor/add_generator_reading/".$this->uri->segment(4));
		}
	  }	
	
}
