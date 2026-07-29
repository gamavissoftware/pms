<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Customer_type extends CI_Controller {

	

	public function __construct()

	{

		parent::__construct();

		

		$this->load->model('User_model','user');

		$this->load->model('Master_model','master');
		$ip = $_SERVER["REMOTE_ADDR"];
		// $query = $this->db->select('ipaddress')->from('ipblocking_view')->where('ipaddress',$ip)->get();
		// if($query->num_rows()=='0'){
		// $this->session->set_flashdata('message','<div class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</div><br/><br><br>');

		// redirect(page_url.'Dashboard');    

		// }
		

	}

	

	

	public function add_customer_type()

	{

		

		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');

		$this->form_validation->set_rules('patient_type', 'Patient Type', 'required|trim');

		$this->form_validation->set_rules('status', 'Status', 'required|trim');

		$user_id =$this->session->userdata['logged_in']['user_id'];		

		if ($this->form_validation->run() == FALSE)

		{

			$this->load->view('master/customer_type');

		}

		else

		{

		date_default_timezone_set("Asia/Kolkata");

		$date =  date('Y-m-d H:i:s'); 

		$table = "patient_type";

		$query = $this->db->select('patient_id,patient_type')->from('patient_type')->where('patient_type',$this->input->post('patient_type'))->get();

		$res = $query->result();

			if($res){

			$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry! This record already exist.</div>');

			redirect(page_url.'Master/Customer_type/add_customer_type');

			

		}else{

		

			$data = array('patient_type'=>$this->input->post('patient_type'),

			'status'=>$this->input->post('status'),

			'added_on'=>$date,
			'company_id'=>$_SESSION['logged_in']['business_location'],

			'added_by'=>$user_id);

			

		$result  = $this->master->insert_record($table,$data);	

		if($result)

		{

			$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully added.</div>');

			redirect(page_url.'Master/Customer_type/add_customer_type');

			

		}else

		{

			$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry,technical error accure.</div><br/>');

			redirect(page_url.'Master/Lead_Type/add_lead_source');

		}

		}

		

	}

		

	}

	public function customer_type_list()
 
	{

		$patient_type_data = array();

		$i=1;

		$this->db->select('*')->from('patient_type');

		$this->db->order_by('patient_type','asc');

		$query = $this->db->get();

		$res = $query->result();

		foreach($res as $row){

												

			$status = $row->status;

			if($status=='1')

			{

				$sta =  "<a href='".page_url."Master/Customer_type/update_customer_type_status/".$row->patient_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";

			}else

			{

				$sta =  "<a href='".page_url."Master/Customer_type/update_customer_type_status/".$row->patient_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";

			}

			$edit = "<a href='".page_url."Master/Customer_type/edit_customer_type/".$row->patient_id."'><i class='fa fa-pencil'></i></a>";	

				

			$patient_type_data[] = array('sr_no'=>$i,

			'patient_type'=>$row->patient_type,

			'status'=>$sta,

			'edit'=>$edit);

			$i++;

		}

			$results = array(

			"sEcho" => 1,

			"iTotalRecords" => count($patient_type_data),

			"iTotalDisplayRecords" => count($patient_type_data),

			"aaData"=>$patient_type_data);

			

		echo json_encode($results);

	}

	

	public function update_customer_type_status()

	{

		/*************Dynamic information****************/

		$identifier =  $this->uri->segment(4);

		$sval =  $this->uri->segment(5);

		$field_name = "patient_id";

		$table = "patient_type";

		if($sval=='1')

			{

				$status = 0;

				}else

				{

					$status = 1;

					}

			$data = array('status'=>$status);

			$res = $this->master->update_records($table,$data,$identifier,$field_name);

			$this->session->set_flashdata('message', '<div class="alert alert-info">Status successfully updated.</div>');

			redirect('Master/Customer_type/add_customer_type');

		}



	public function edit_customer_type(){

		$this->load->view('master/edit_customer_type');

	}	

	

	public function update_customer_type()

	{

		

		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');

		$this->form_validation->set_rules('patient_type', 'Patient Type', 'required|trim');

		$this->form_validation->set_rules('status', 'Status', 'required|trim');

		$user_id =$this->session->userdata['logged_in']['user_id'];		

		if ($this->form_validation->run() == FALSE)

		{

			$this->load->view('master/edit_customer_type');

		}

		else

		{

		date_default_timezone_set("Asia/Kolkata");

		$date =  date('Y-m-d H:i:s'); 

		$table = "patient_type";

		

		

			$data = array('patient_type'=>$this->input->post('patient_type'),

			'status'=>$this->input->post('status'),

			'added_on'=>$date,

			'added_by'=>'1');

			$this->db->where('patient_id',$this->uri->segment(4));

			$result  = $this->db->update($table,$data);	

		if($result)

		{

			$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully updated.</div>');

			redirect(page_url.'Master/Customer_type/add_customer_type');

			

		}else

		{

			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');

			redirect(page_url.'Master/Customer_type/add_customer_type');;

		}



		

	}

		

	}



		public function process_name() {

		  $this->load->view('master/process_name');

		}



               



        public function add_process_name() {               



        $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');

        $this->form_validation->set_rules('process_name', 'Service Name', 'required|trim');

        //$this->form_validation->set_rules('status', 'status', 'required|trim');

        $user_id =$this->session->userdata['logged_in']['user_id'];                       



			if ($this->form_validation->run() == FALSE) {

				$this->load->view('master/process_name');

			} else {

			   date_default_timezone_set("Asia/Kolkata");

           	   $added_time = date('Y-m-d H:i:s');



           $query = $this->db->select('process_name')->from('process_name')->where('process_name',strtoupper($this->input->post('process_name')))->get();

           $res = $query->result();



            if($res) {

              $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000; float-left:20px;">Sorry,This record already exist.</span></div><br/>');

              redirect(page_url.'Master/Customer_type/process_name');                                                 



            } else {                           



            $data = array(

            	'process_name' => strtoupper($this->input->post('process_name')),

            	'status'=>$this->input->post('status'),

                'added_on'=>$added_time,

                'added_by'=>$user_id

                );



                                               



				$this->db->insert('process_name',$data);



				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;"><span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');



				redirect(page_url.'Master/Customer_type/process_name');

                             

                	}

				}                      



         }



               



            function process_name_list() {

	            $process_name_data = array();



	            $this->db->select('*')->from('process_name');



	            $query = $this->db->get();



	            $res = $query->result();



	            $i=1;



                foreach($res as $row)  {



				date_default_timezone_set("Asia/Kolkata");



				$addeddate = date('d-M-Y', strtotime($row->added_on));



				$time = date('H:i:s', strtotime($row->added_on));



				$addedtime = "<br>". date('g:i A', strtotime($time));



				$status = $row->status;



				if($status=='1')



				{



				$sta =  "<a href='".page_url."Master/Customer_type/update_process_name_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";



				}else



				{



					$sta =  "<a href='".page_url."Master/Customer_type/update_process_name_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";



					}



					$edit = "<a href='".page_url."Master/Customer_type/edit_process_name/".$row->id."'><i class='fa fa-pencil'></i></a>";







					$process_name_data[] = array('sr_no'=>$i,

												 'process_name'=>strtoupper($row->process_name),

												 'status'=>$sta,

												 'added_on'=>$addeddate." ".$addedtime,

												 'edit'=>$edit

												);



					$i++;



                                }



                                //echo "<pre>"; print_r($process_name_type_data); exit;



                                                $results = array(



                                                "sEcho" => 1,



                                                "iTotalRecords" => count($process_name_data),



                                                "iTotalDisplayRecords" => count($process_name_data),



                                                "aaData"=>$process_name_data);





                                echo json_encode($results);              



                }



               



			function edit_process_name() {                              

				$this->load->view('master/edit_process_name');



			}           



            public function update_process_name() {



               



                $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');



                $this->form_validation->set_rules('process_name', 'Service Name', 'required|trim');



                //$this->form_validation->set_rules('status', 'status', 'required|trim');



                $user_id =$this->session->userdata['logged_in']['user_id'];                       



                                if ($this->form_validation->run() == FALSE)



                                {



                                                $this->load->view('master/edit_process_name');



                                                }else



                                {



                                                date_default_timezone_set("Asia/Kolkata");



           $added_time = date('Y-m-d H:i:s');



                                   $query = $this->db->select('process_name')->from('process_name')->where('process_name',strtoupper($this->input->post('process_name')))->get();



                                   $res = $query->result();



                                   if($res){



                                                   $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000; float-left:20px;">Sorry,This record already exist.</span></div><br/>');



                                                                redirect(page_url.'Master/Customer_type/process_name');



                                                  



                                   }else{



                                  



                                   $data=



                                                array('process_name'=>strtoupper($this->input->post('process_name')),



                                                'status'=>$this->input->post('status'),



                                                'added_on'=>$added_time);



                                                $this->db->where('id',$this->uri->segment(4));



                                                $res = $this->db->update('process_name',$data);



                                                if($res)



                                                {



                                                                $this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully updated.</span></div><br/>');



                                                                redirect(page_url.'Master/Customer_type/process_name');



                                                                }



                                   }



                                               



                                                }



                               



                }



               



                public function update_process_name_status()



                {



				$identifier = $this->uri->segment(4);



				$sval =  $this->uri->segment(5);



				$field_name = "id";



				$table = "process_name";



				if($sval=='1')



				{



				$status = 0;



				}else



				{



				$status = 1;



				}



				$data = array('status'=>$status);



				$this->db->where('id',$this->uri->segment(4));



				$res = $this->db->update($table,$data);



				$this->session->set_flashdata('message','<div class="alert alert-success">Status successfully updated.</div>');



				redirect(page_url.'Master/Customer_type/process_name');



				}

		public function add_reason()
		{

		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');

		$this->form_validation->set_rules('reason_name', 'Reason Name', 'required|trim');

		$this->form_validation->set_rules('status', 'Status', 'required|trim');

		$user_id =$this->session->userdata['logged_in']['user_id'];		

		if ($this->form_validation->run() == FALSE)

		{

			$this->load->view('master/unqualified_reason');

		}

		else

		{

		date_default_timezone_set("Asia/Kolkata");

		$date =  date('Y-m-d H:i:s'); 

		$table = "leads_unqualified_reason";

		$query = $this->db->select('reason_id,reason')->from('leads_unqualified_reason')->where('reason',$this->input->post('reason_name'))->get();

		$res = $query->result();

			if($res){

			$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry! This record already exist.</div>');

			redirect(page_url.'Master/Customer_type/add_reason');

			

		}else{

			$remarket=$this->input->post('remarket');
			if($remarket=='')
			{
				$remarket=0;
			}else
			{
				$remarket=1;
			}		

			$data = array('reason'=>$this->input->post('reason_name'),

			'status'=>$this->input->post('status'),
			'remarketing'=>$remarket,
			'added_on'=>$date,
			'company_id'=>$_SESSION['logged_in']['business_location'],
			'added_by'=>$user_id);

			

		$result  = $this->master->insert_record($table,$data);	

		if($result)

		{

			$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully added.</div>');

			redirect(page_url.'Master/Customer_type/add_reason');

			

		}else

		{

			$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry,technical error accure.</div><br/>');

			redirect(page_url.'Master/Customer_type/add_reason');

		}

		}

		

	}

		

	}

	public function unqualified_reason_list()

	{

		$patient_type_data = array();

		$i=1;

		$this->db->select('*')->from('leads_unqualified_reason');

		$this->db->order_by('reason','asc');

		$query = $this->db->get();

		$res = $query->result();

		foreach($res as $row){												

			$status = $row->status;

			if($status=='1')

			{

				$sta =  "<a href='".page_url."Master/Customer_type/update_unqualified_reason_status/".$row->reason_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";

			}else

			{

				$sta =  "<a href='".page_url."Master/Customer_type/update_unqualified_reason_status/".$row->reason_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";

			}

			$edit = "<a href='".page_url."Master/Customer_type/edit_unqualified_reason/".$row->reason_id."'><i class='fa fa-pencil'></i></a>";	

				if($row->remarketing==1)
				{
					$r="Yes";
				}else
				{
					$r="No";
				}

			$patient_type_data[] = array('sr_no'=>$i,

			'patient_type'=>$row->reason,
			'remarketing'=>$r,

			'status'=>$sta,

			'edit'=>$edit);

			$i++;

		}

			$results = array(

			"sEcho" => 1,

			"iTotalRecords" => count($patient_type_data),

			"iTotalDisplayRecords" => count($patient_type_data),

			"aaData"=>$patient_type_data);

			

		echo json_encode($results);

	}

	

	public function update_unqualified_reason_status()

	{

		/*************Dynamic information****************/

		$identifier =  $this->uri->segment(4);

		$sval =  $this->uri->segment(5);

		$field_name = "reason_id";

		$table = "leads_unqualified_reason";

		if($sval=='1')

			{

				$status = 0;

				}else

				{

					$status = 1;

					}

			$data = array('status'=>$status);
			$this->db->where('reason_id',$identifier);
			$res= $this->db->update($table,$data);

			//$res = $this->master->update_records($table,$data,$identifier,$field_name);

			$this->session->set_flashdata('message', 'Status successfully updated.');

			redirect(page_url.'Master/Customer_type/add_reason');

		}



	public function edit_unqualified_reason(){

		$this->load->view('master/edit_unqualified_reason');

	}	

	

	public function update_unqualified_reason()

	{

		

		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');

		$this->form_validation->set_rules('reason_name', 'Reason', 'required|trim');

		$this->form_validation->set_rules('status', 'Status', 'required|trim');

		$user_id =$this->session->userdata['logged_in']['user_id'];		

		if ($this->form_validation->run() == FALSE)

		{

			$this->load->view('master/edit_unqualified_reason');

		}

		else

		{

		date_default_timezone_set("Asia/Kolkata");

		$date =  date('Y-m-d H:i:s'); 

		$table = "leads_unqualified_reason";

			$remarket=$this->input->post('remarket');
			if($remarket=='')
			{
				$remarket=0;
			}else
			{
				$remarket=1;
			}		


		

		

			$data = array('reason'=>$this->input->post('reason_name'),

			'status'=>$this->input->post('status'),
			'remarketing'=>$remarket,

			'added_on'=>$date,

			'added_by'=>'1');

			$this->db->where('reason_id',$this->uri->segment(4));

			$result  = $this->db->update($table,$data);	

		if($result)

		{

			$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully updated.</div>');

			redirect(page_url.'Master/Customer_type/add_reason');

			

		}else

		{

			$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry,technical error accure.</div><br/>');

			redirect(page_url.'Master/Customer_type/add_reason');;

		}



		

	}

		

	}

}

?>