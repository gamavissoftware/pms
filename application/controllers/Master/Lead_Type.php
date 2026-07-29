<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lead_Type extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('User_model','user');
		$this->load->model('Master_model','master');
		
	}

	public function index()
	{
		
		$this->load->view('master/lead_type');
	}
	
	
	
	public function add_lead_type()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('lead_type', 'Lead Type', 'required|trim');
		// $this->form_validation->set_rules('status', 'Status', 'required|trim');
		//$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/lead_type');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "lead_stage";
		$query = $this->db->select('lead_id,lead_name')->from('lead_stage')->where('lead_name',$this->input->post('lead_type'))->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry! This record already exist.</div>');
			redirect(page_url.'Master/Lead_Type');
			
		}else{

			if($this->input->post('comparison_report') == '') {
				$comparison_report = 0;
			} else {
				$comparison_report = $this->input->post('comparison_report');
			}

			if($this->input->post('quotation_step') == '') {
				$quotation_step = 0;
			} else {
				$quotation_step = $this->input->post('quotation_step');
			}

			if($this->input->post('quotation_revised_step') == '') {
				$quotation_revised_step = 0;
			} else {
				$quotation_revised_step = $this->input->post('quotation_revised_step');
			}

			if($this->input->post('conversion_step') == '') {
				$conversion_step = 0;
			} else {
				$conversion_step = $this->input->post('conversion_step');
			}

			if($this->input->post('visit_step') == '') {
				$visit_step = 0;
			} else {
				$visit_step = $this->input->post('visit_step');
			}
			if($this->input->post('demo_scheduled') == '') {
				$demo_scheduled = 0;
			} else {
				$demo_scheduled = $this->input->post('demo_scheduled');
			}

			if($this->input->post('reason') == '') {
				$reason = 0;
			} else {
				$reason = $this->input->post('reason');
			}

			if($this->input->post('followup_date') == '') {
				$followup_date = 0;
			} else {
				$followup_date = $this->input->post('followup_date');
			}

			if($this->input->post('dead_end') == '') {
				$dead_end = 0;
			} else {
				$dead_end = $this->input->post('dead_end');
			}

			$icon=$_FILES['icon']['name'];
				if($icon <> '') {
					$image1=explode('.',$icon);
					$icon=end($image1);
					$newname=time().'.'.$icon;
					move_uploaded_file($_FILES["icon"]["tmp_name"],ASSETSUPLOADPATH.'lead_stage_icons/'.$newname);
				} else {
					$newname='';
		    	}
		    		$user_role=$this->input->post('user_role');


		    	if ($this->input->post('mis') == 1) {
		    		$mis = 1;
		    	} else {
		    		$mis = 0;
		    	}

		    	if ($this->input->post('day_time') == 1) {
		    		$day_time = 1;
		    	} else if($this->input->post('day_time') == 2) {
		    		$day_time = 2;
		    	} else {
		    		$day_time = 0;
		    	}

		    	if ($this->input->post('sample') == 1) {
		    		$sample = 1;
		    	} else {
		    		$sample = 0;
		    	}


		    	if ($this->input->post('trail') == 1) {
		    		$trail = 1;
		    	} else {
		    		$trail = 0;
		    	}

		
			$data = array(
					'lead_name' => $this->input->post('lead_type'),
					'user_role'=>$user_role,
					'sort_order' => $this->input->post('sort_order'),
					'comparison_report' => $comparison_report,
					'quotation_step' => $quotation_step,
					'quotation_revised_step' => $quotation_revised_step,
					'pi_revised_step' => 0,
					'pi_step' => 0,
					'conversion_step' => $conversion_step,
					'reason' => $reason,
					'followup_date' => $followup_date,
					'dead_end' => $dead_end,
					'sample'=>$sample,
					'trail'=>$trail,
					'mis' => $mis,
					'visit_step'=>$visit_step,
					'demo_scheduled'=>$demo_scheduled,
					'tat_type' => $this->input->post('tat_type'),
					'day_time' => $day_time,
					'day_text' => $this->input->post('day_text'),
					'time_text' => $this->input->post('time_text'),
					'icon' => $newname,
					'status' => 1,
					'added_on' => $date,
					'added_by' => $this->session->userdata['logged_in']['user_id']
					);
			// echo "<pre>";print_r($data);exit;
			$last_id = $this->master->insert_record($table,$data);	

		$datas = array(
					  'lead_stage_id' => $last_id,
					  'report_count' => 0,
					  'report_title' => $this->input->post('lead_type')
					  // 'company_id'=>$_SESSION['logged_in']['business_location']
					  );

				$this->db->insert('administrator_dashboard', $datas); 

		$data1 = array(
					  'module_id' => 1,
					  'lead_stage_id' => $last_id,
					  'sub_module_name' => $this->input->post('lead_type')
					  // 'company_id'=>$_SESSION['logged_in']['business_location']
					  );

		$result = $this->db->insert('dashboard_sub_modules', $data1); 

		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully added.</div>');
			redirect(page_url.'Master/Lead_Type');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/Lead_Type');
		}
		}
		
	}
		
	}

	public function checksortno() {
		$sort_order = $this->input->post('sort_order');

		$sql = $this->db->select('sort_order')
						->from('lead_stage')
						->where('sort_order', $sort_order)
						->get();

		if ($sql->num_rows() > 0) {
			echo 1;
		} else {
			echo 0;
		}
	}

	public function checksparessortno() {
		$sort_order = $this->input->post('sort_order');

		$sql = $this->db->select('sort_order')
						->from('spare_lead_stage')
						->where('sort_order', $sort_order)
						->get();

		if ($sql->num_rows() > 0) {
			echo 1;
		} else {
			echo 0;
		}
	}

	public function Lead_list()
	{
		
		$lead_data = array();
		$i=1;
		
		$query = $this->db->select('lead_id, lead_name, sort_order,user_role')
						  ->from('lead_stage')
						  ->order_by('sort_order','asc')
				 		  ->get();
		
		foreach($query->result() as $row) {

			$sql = $this->db->select('lead_stage_id')
							->from('lead_stage_relation')
							->where('lead_stage_id', $row->lead_id)
							->get();

			if($sql->num_rows() > 0) {
				$relation = "<a href='".page_url."Master/Lead_Type/edit_stage_relation/".$row->lead_id."' class='btn btn-success btn-xs'>View/Edit Relation</a>";
			} else {
				$relation = "<a href='".page_url."Master/Lead_Type/set_stage_relation/".$row->lead_id."' class='btn btn-warning btn-xs'>Set Relation</a>";
			}

			$user_role='';
			if($row->user_role<>''|| $row->user_role<>0)
			{
				$res=$this->db->select('user_role_id,user_role')->from('user_role')->where('status',1)->where('user_role_id',$row->user_role)->get();
                                             if($res->num_rows() >0)
                                             {
                                                foreach($res->result() as $row1);
                                                $user_role=$row1->user_role;

                                                }
			}
			$html="";
			//$lead_stage = $this->master->getLeadStage($row->lead_id);
			$stage_relation = $this->master->getLeadStageRelation($row->lead_id);
			//$getAllLeadStages = $this->master->getAllLeadStages();
			$j=1;
			if($stage_relation != '') {
			$html.='<table class="table table-bordered"><thead><tr><th>sr_no</th><th>Relation stage</th></tr></thead><tbody>';
			
			foreach($stage_relation as $leadstage) {
				$lead_stage = $this->master->getLeadStage($leadstage);
				$html.='<tr><td>'.$j.'</td><td>'.$lead_stage.'</td></tr>';
				
				$j++;
			}
			$html.='</tbody></table>';
			}
			if($i == 1) {
				$edit = "<a href='".page_url."Master/Lead_Type/edit_lead_type/".$row->lead_id."'><i class='fa fa-pencil'></i></a>";
			} else {
				$edit = "<a href='".page_url."Master/Lead_Type/edit_lead_type/".$row->lead_id."'><i class='fa fa-pencil'></i></a>";	
			}

			$lead_data[] = array('sr_no'=>$i,
			'lead_stage'=>$row->lead_name,
			'sort_order'=>$row->sort_order,
			'relation'=>$relation,
			'user_role'=>$user_role,
			'realation'=>$html,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}

	function set_stage_relation() {
		$this->load->view('master/set_stage_relation');
	}

	function edit_stage_relation() {
		$this->load->view('master/edit_stage_relation');
	}
	
	public function update_lead_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(4);
		$sval =  $this->uri->segment(5);
		$field_name = "lead_id";
		$table = "lead_type";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', '<div class="alert alert-success">Status successfully updated.</div>');
			redirect('Master/Lead_Type/');
		}

	public function edit_lead_type(){
		$this->load->view('master/edit_lead_type');
		
	}	
	
	public function update_lead_type()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('lead_type', 'Lead Type', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		//$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/lead_type');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		

		if($this->input->post('comparison_report') == '') {
				$comparison_report = 0;
			} else {
				$comparison_report = $this->input->post('comparison_report');
			}

		

			if($this->input->post('quotation_step') == '') {
				$quotation_step = 0;
			} else {
				$quotation_step = $this->input->post('quotation_step');
			}

			if($this->input->post('quotation_revised_step') == '') {
				$quotation_revised_step = 0;
			} else {
				$quotation_revised_step = $this->input->post('quotation_revised_step');
			}

			if($this->input->post('conversion_step') == '') {
				$conversion_step = 0;
			} else {
				$conversion_step = $this->input->post('conversion_step');
			}

			if($this->input->post('reason') == '') {
				$reason = 0;
			} else {
				$reason = $this->input->post('reason');
			}

			if($this->input->post('followup_date') == '') {
				$followup_date = 0;
			} else {
				$followup_date = $this->input->post('followup_date');
			}

			if($this->input->post('dead_end') == '') {
				$dead_end = 0;
			} else {
				$dead_end = $this->input->post('dead_end');
			}

				$old_icon = $this->input->post('old_icon');
				$icon_img=$_FILES['icon']['name'];
				if($icon_img<>'')
			    {
					$image1=explode('.',$icon_img);
					$end=end($image1);
					$newname=time().'.'.$end;
					move_uploaded_file($_FILES["icon"]["tmp_name"],ASSETSUPLOADPATH.'lead_stage_icons/' . $newname);
			     }else
			    {
			        $newname=$old_icon;
			    }

						$user_role=$this->input->post('user_role');

					if($this->input->post('mis')==1)
					{
						$tattype=$this->input->post('tat_type');
						$day_time=$this->input->post('day_time');
						$day_text=$this->input->post('day_text');
						$time_text=$this->input->post('time_text');
					

					}else
					{
						$tattype=0;
						$day_time=0;
						$day_text=0;
						$time_text=0;

					}


						if ($this->input->post('sample') == 1) {
						$sample = 1;
						} else {
						$sample = 0;
						}


						if ($this->input->post('trail') == 1) {
						$trail = 1;
						} else {
						$trail = 0;
						}



			$data = array(
					'lead_name' => $this->input->post('lead_type'),
					'sort_order' => $this->input->post('sort_order'),
					'user_role'=>$user_role,
					'comparison_report' => $comparison_report,
					'quotation_step' => $quotation_step,
					'quotation_revised_step' => $quotation_revised_step,
					'pi_revised_step' => 0,
					'pi_step' => 0,
					'sample'=>$sample,
					'trail'=>$trail,
					'conversion_step' => $conversion_step,
					'reason' => $reason,
					'followup_date' => $followup_date,
					'dead_end' => $dead_end,
					'status' => $this->input->post('status'),
					// 'mis' => $this->input->post('mis'),
					// 'tat_type' =>$tattype,
					// 'day_time' => $day_time,
					// 'day_text' => $day_text,
					// 'time_text' => $time_text,
					'icon' => $newname
					);

			// echo "<pre>";print_r($data);exit;
			
			$result = $this->db->where('lead_id', $this->uri->segment(4))
							   ->update('lead_stage',$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully updated.</div>');
			redirect(page_url.'Master/Lead_Type');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/Lead_Type');
		}

		
	}
		
	}
	
	
	public function add_lead_source()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('lead_source', 'Lead Source', 'required|trim');
		// $this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/lead_source');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "lead_source";
		$query = $this->db->select('source_id,lead_source')->from('lead_source')->where('lead_source',$this->input->post('lead_source'))->get();
		$res = $query->result();
			if($res){
			$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry! This record already exist.</div>');
			redirect(page_url.'Master/Lead_Type/add_lead_source');
			
		}else{
		
			$data = array(
						'lead_source'=>$this->input->post('lead_source'),
						'keyword'=>$this->input->post('source_abbr'),
						'status' => 1,
						'added_on'=>$date,
						'added_by'=>$user_id,
						'company_id'=>$_SESSION['logged_in']['business_location']
						);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully added.</div>');
			redirect(page_url.'Master/Lead_Type/add_lead_source');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/Lead_Type/add_lead_source');
		}
		}
		
	}
		
	}
	public function Lead_source_list()
	{
		$lead_source_data = array();
		$i=1;
		$this->db->select('*')->from('lead_source');
		$this->db->order_by('lead_source','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Master/Lead_Type/update_lead_source_status/".$row->source_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Master/Lead_Type/update_lead_source_status/".$row->source_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Master/Lead_Type/edit_lead_source/".$row->source_id."'><i class='fa fa-pencil'></i></a>";	
				
			$lead_source_data[] = array('sr_no'=>$i,
			'lead_source'=>$row->lead_source,
			'keyword'=>$row->keyword,
			// 'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_source_data),
			"iTotalDisplayRecords" => count($lead_source_data),
			"aaData"=>$lead_source_data);
			
		echo json_encode($results);
	}
	
	public function update_lead_source_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(4);
		$sval =  $this->uri->segment(5);
		$field_name = "source_id";
		$table = "lead_source";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', '<div class="alert alert-success">Status successfully updated.</div>');
			redirect('Master/Lead_Type/add_lead_source');
		}

	public function edit_lead_source(){
		$this->load->view('master/edit_lead_source');
	}	
	
	public function update_lead_source()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('lead_source', 'Lead Source', 'required|trim');
		// $this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/edit_lead_source');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "lead_source";
		
		
			$data = array(
						'lead_source'=>$this->input->post('lead_source'),
						'keyword'=>$this->input->post('source_abbr'),
						// 'status'=>$this->input->post('status'),
						'added_on'=>$date,
						'added_by'=>'1'
					);
			$this->db->where('source_id',$this->uri->segment(4));
			$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully updated.</div>');
			redirect(page_url.'Master/Lead_Type/add_lead_source');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/Lead_Type/add_lead_source');
		}

		
		}
		
	}

	function add_stage_relation() {

		$lead_stages = $this->input->post('multi_lead_stage');

		for($i=0; $i < count($lead_stages); $i++) {
			if($lead_stages != '') {
				$data = array(
							'lead_stage_id' => $this->uri->segment(4),
							'stage_relation' => $lead_stages[$i]
							);

				$this->db->insert('lead_stage_relation', $data);
			}
		}

		$this->session->set_flashdata('message','<div class="alert alert-success">Relation successfully saved.</div><br/>');
		redirect(page_url.'Master/Lead_Type/add_lead_type');

	}

	function update_stage_relation() {
		$lead_stages = $this->input->post('multi_lead_stage');
		$stage_id = $this->uri->segment(4);

		$this->db->where('lead_stage_id', $stage_id)
				 ->delete('lead_stage_relation');

		if($this->db->affected_rows()) {
			for($i=0; $i < count($lead_stages); $i++) {
				if($lead_stages != '') {
					$data = array(
								'lead_stage_id' => $stage_id,
								'stage_relation' => $lead_stages[$i]
								);

					$this->db->insert('lead_stage_relation', $data);
				}
			}
		}

		$this->session->set_flashdata('message','<div class="alert alert-success">Relation successfully saved.</div><br/>');
		redirect(page_url.'Master/Lead_Type/add_lead_type');
	}

	function checkIfAbbrExists() {
		$source_abbr = $this->input->post('source_abbr');

		$sql = $this->db->select('source_id')
						->from('lead_source')
						->where('keyword', $source_abbr)
						->where('company_id',$_SESSION['logged_in']['business_location'])
						->get();

		if ($sql->num_rows() > 0) {
			echo 1;
		} else {
			echo 0;
		}
	}

	public function add_lead_type_spares()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('lead_type', 'Lead Type', 'required|trim');
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/spares_lead_type');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "spare_lead_stage";
		$query = $this->db->select('lead_id,lead_name')->from('spare_lead_stage')->where('lead_name',$this->input->post('lead_type'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry! This record already exist.</div>');
			redirect(page_url.'Master/Lead_Type/add_lead_type_spares');
			
		}else{

			if($this->input->post('comparison_report') == '') {
				$comparison_report = 0;
			} else {
				$comparison_report = $this->input->post('comparison_report');
			}

			if($this->input->post('quotation_step') == '') {
				$quotation_step = 0;
			} else {
				$quotation_step = $this->input->post('quotation_step');
			}

			if($this->input->post('quotation_revised_step') == '') {
				$quotation_revised_step = 0;
			} else {
				$quotation_revised_step = $this->input->post('quotation_revised_step');
			}

			if($this->input->post('conversion_step') == '') {
				$conversion_step = 0;
			} else {
				$conversion_step = $this->input->post('conversion_step');
			}

			if($this->input->post('visit_step') == '') {
				$visit_step = 0;
			} else {
				$visit_step = $this->input->post('visit_step');
			}
			if($this->input->post('demo_scheduled') == '') {
				$demo_scheduled = 0;
			} else {
				$demo_scheduled = $this->input->post('demo_scheduled');
			}

			if($this->input->post('reason') == '') {
				$reason = 0;
			} else {
				$reason = $this->input->post('reason');
			}

			if($this->input->post('followup_date') == '') {
				$followup_date = 0;
			} else {
				$followup_date = $this->input->post('followup_date');
			}

			if($this->input->post('dead_end') == '') {
				$dead_end = 0;
			} else {
				$dead_end = $this->input->post('dead_end');
			}

			$icon=$_FILES['icon']['name'];
				if($icon <> '') {
					$image1=explode('.',$icon);
					$icon=end($image1);
					$newname=time().'.'.$icon;
					move_uploaded_file($_FILES["icon"]["tmp_name"],ASSETSUPLOADPATH.'lead_stage_icons/'.$newname);
				} else {
					$newname='';
		    	}
		    		$user_role=$this->input->post('user_role');


		    	if ($this->input->post('mis') == 1) {
		    		$mis = 1;
		    	} else {
		    		$mis = 0;
		    	}

		    	if ($this->input->post('day_time') == 1) {
		    		$day_time = 1;
		    	} else if($this->input->post('day_time') == 2) {
		    		$day_time = 2;
		    	} else {
		    		$day_time = 0;
		    	}

		    	if ($this->input->post('sample') == 1) {
		    		$sample = 1;
		    	} else {
		    		$sample = 0;
		    	}


		    	if ($this->input->post('trail') == 1) {
		    		$trail = 1;
		    	} else {
		    		$trail = 0;
		    	}

		
			$data = array(
					'lead_name' => $this->input->post('lead_type'),
					'user_role'=>$user_role,
					'sort_order' => $this->input->post('sort_order'),
					'comparison_report' => $comparison_report,
					'quotation_step' => $quotation_step,
					'quotation_revised_step' => $quotation_revised_step,
					'pi_revised_step' => 0,
					'pi_step' => 0,
					'conversion_step' => $conversion_step,
					'reason' => $reason,
					'followup_date' => $followup_date,
					'dead_end' => $dead_end,
					'sample'=>$sample,
					'trail'=>$trail,
					'mis' => $mis,
					'visit_step'=>$visit_step,
					'demo_scheduled'=>$demo_scheduled,
					'tat_type' => $this->input->post('tat_type'),
					'day_time' => $day_time,
					'day_text' => $this->input->post('day_text'),
					'time_text' => $this->input->post('time_text'),
					'icon' => $newname,
					'status' => 1,
					'added_on' => $date,
					'added_by' => $this->session->userdata['logged_in']['user_id']
					);
			$results = $this->master->insert_record($table,$data);	

	

		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully added.</div>');
			redirect(page_url.'Master/Lead_Type/add_lead_type_spares');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/Lead_Type/add_lead_type_spares');
		}
		}
		
	}
		
	}

	public function spares_Lead_list()
	{
		
		$lead_data = array();
		$i=1;
		
		$query = $this->db->select('lead_id, lead_name, sort_order,user_role')
						  ->from('spare_lead_stage')
						  ->order_by('sort_order','asc')
				 		  ->get();
		
		foreach($query->result() as $row) {

			$sql = $this->db->select('lead_stage_id')
							->from('spares_lead_stage_relation')
							->where('lead_stage_id', $row->lead_id)
							->get();

			if($sql->num_rows() > 0) {
				$relation = "<a href='".page_url."Master/Lead_Type/edit_spare_stage_relation/".$row->lead_id."' class='btn btn-success btn-xs'>View/Edit Relation</a>";
			} else {
				$relation = "<a href='".page_url."Master/Lead_Type/set_spare_stage_relation/".$row->lead_id."' class='btn btn-warning btn-xs'>Set Relation</a>";
			}

			$user_role='';
			if($row->user_role<>''|| $row->user_role<>0)
			{
				$res=$this->db->select('user_role_id,user_role')->from('user_role')->where('status',1)->where('user_role_id',$row->user_role)->get();
                                             if($res->num_rows() >0)
                                             {
                                                foreach($res->result() as $row1);
                                                $user_role=$row1->user_role;

                                                }
			}
			$html="";
			//$lead_stage = $this->master->getLeadStage($row->lead_id);
			$stage_relation = $this->master->getLeadStageRelation($row->lead_id);
			//$getAllLeadStages = $this->master->getAllLeadStages();
			$j=1;
			if($stage_relation != '') {
			$html.='<table class="table table-bordered"><thead><tr><th>sr_no</th><th>Relation stage</th></tr></thead><tbody>';
			
			foreach($stage_relation as $leadstage) {
				$lead_stage = $this->master->getspareLeadStage($leadstage);
				$html.='<tr><td>'.$j.'</td><td>'.$lead_stage.'</td></tr>';
				
				$j++;
			}
			$html.='</tbody></table>';
			}
			if($i == 1) {
				$edit = "<a href='".page_url."Master/Lead_Type/edit_spare_lead_type/".$row->lead_id."'><i class='fa fa-pencil'></i></a>";
			} else {
				$edit = "<a href='".page_url."Master/Lead_Type/edit_spare_lead_type/".$row->lead_id."'><i class='fa fa-pencil'></i></a>";	
			}

			$lead_data[] = array('sr_no'=>$i,
			'lead_stage'=>$row->lead_name,
			'sort_order'=>$row->sort_order,
			'relation'=>$relation,
			'user_role'=>$user_role,
			'realation'=>$html,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}

	function edit_spare_stage_relation() {
		$this->load->view('master/edit_spare_stage_relation');
	}
	function update_spare_stage_relation() {
		$lead_stages = $this->input->post('multi_lead_stage');
		$stage_id = $this->uri->segment(4);

		$this->db->where('lead_stage_id', $stage_id)
				 ->delete('spares_lead_stage_relation');

		if($this->db->affected_rows()) {
			for($i=0; $i < count($lead_stages); $i++) {
				if($lead_stages != '') {
					$data = array(
								'lead_stage_id' => $stage_id,
								'stage_relation' => $lead_stages[$i]
								);

					$this->db->insert('spares_lead_stage_relation', $data);
				}
			}
		}

		$this->session->set_flashdata('message','<div class="alert alert-success">Relation successfully saved.</div><br/>');
		redirect(page_url.'Master/Lead_Type/add_lead_type_spares');
	}

	function set_spare_stage_relation() {
		$this->load->view('master/set_spare_stage_relation');
	}

	function add_spare_stage_relation() {

		$lead_stages = $this->input->post('multi_lead_stage');

		for($i=0; $i < count($lead_stages); $i++) {
			if($lead_stages != '') {
				$data = array(
							'lead_stage_id' => $this->uri->segment(4),
							'stage_relation' => $lead_stages[$i]
							);

				$this->db->insert('spares_lead_stage_relation', $data);
			}
		}

		$this->session->set_flashdata('message','<div class="alert alert-success">Relation successfully saved.</div><br/>');
		redirect(page_url.'Master/Lead_Type/add_lead_type_spares');

	}

	public function edit_spare_lead_type(){
		$this->load->view('master/edit_spare_lead_type.php');
		
	}

	public function update_spare_lead_type()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('lead_type', 'Lead Type', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		//$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/edit_spare_lead_type');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		

		if($this->input->post('comparison_report') == '') {
				$comparison_report = 0;
			} else {
				$comparison_report = $this->input->post('comparison_report');
			}

		

			if($this->input->post('quotation_step') == '') {
				$quotation_step = 0;
			} else {
				$quotation_step = $this->input->post('quotation_step');
			}

			if($this->input->post('quotation_revised_step') == '') {
				$quotation_revised_step = 0;
			} else {
				$quotation_revised_step = $this->input->post('quotation_revised_step');
			}

			if($this->input->post('conversion_step') == '') {
				$conversion_step = 0;
			} else {
				$conversion_step = $this->input->post('conversion_step');
			}

			if($this->input->post('reason') == '') {
				$reason = 0;
			} else {
				$reason = $this->input->post('reason');
			}

			if($this->input->post('followup_date') == '') {
				$followup_date = 0;
			} else {
				$followup_date = $this->input->post('followup_date');
			}

			if($this->input->post('dead_end') == '') {
				$dead_end = 0;
			} else {
				$dead_end = $this->input->post('dead_end');
			}

				$old_icon = $this->input->post('old_icon');
				$icon_img=$_FILES['icon']['name'];
				if($icon_img<>'')
			    {
					$image1=explode('.',$icon_img);
					$end=end($image1);
					$newname=time().'.'.$end;
					move_uploaded_file($_FILES["icon"]["tmp_name"],ASSETSUPLOADPATH.'lead_stage_icons/' . $newname);
			     }else
			    {
			        $newname=$old_icon;
			    }

						$user_role=$this->input->post('user_role');

					if($this->input->post('mis')==1)
					{
						$tattype=$this->input->post('tat_type');
						$day_time=$this->input->post('day_time');
						$day_text=$this->input->post('day_text');
						$time_text=$this->input->post('time_text');
					

					}else
					{
						$tattype=0;
						$day_time=0;
						$day_text=0;
						$time_text=0;

					}


						if ($this->input->post('sample') == 1) {
						$sample = 1;
						} else {
						$sample = 0;
						}


						if ($this->input->post('trail') == 1) {
						$trail = 1;
						} else {
						$trail = 0;
						}



			$data = array(
					'lead_name' => $this->input->post('lead_type'),
					'sort_order' => $this->input->post('sort_order'),
					'user_role'=>$user_role,
					'comparison_report' => $comparison_report,
					'quotation_step' => $quotation_step,
					'quotation_revised_step' => $quotation_revised_step,
					'pi_revised_step' => 0,
					'pi_step' => 0,
					'sample'=>$sample,
					'trail'=>$trail,
					'conversion_step' => $conversion_step,
					'reason' => $reason,
					'followup_date' => $followup_date,
					'dead_end' => $dead_end,
					'status' => $this->input->post('status'),
					// 'mis' => $this->input->post('mis'),
					// 'tat_type' =>$tattype,
					// 'day_time' => $day_time,
					// 'day_text' => $day_text,
					// 'time_text' => $time_text,
					'icon' => $newname
					);

			// echo "<pre>";print_r($data);exit;
			
			$result = $this->db->where('lead_id', $this->uri->segment(4))
							   ->update('spare_lead_stage',$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully updated.</div>');
			redirect(page_url.'Master/Lead_Type/add_lead_type_spares');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/Lead_Type/add_lead_type_spares');
		}

		
	}
		
	}
}
?>