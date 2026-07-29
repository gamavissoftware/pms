<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LMS extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
			$session = $this->session->userdata('logged_in');
		if($session == FALSE)
		{
		
		redirect(page_url);
		
		}
		$user_id =$this->session->userdata['logged_in']['user_id'];
	
		
		
		
		
		$this->load->model('User_model','user');
		$this->load->model('Master_model','master');
		$config = array();  
		$config['protocol'] = 'smtp';  
		$config['smtp_host'] = 'localhost';  
		$config['smtp_user'] = 'donotreply@packingtest.com';  
		$config['smtp_pass'] = 'Presto@#21';  
		$config['smtp_port'] = 25;  
		$this->email->initialize($config);  

		$this->email->set_newline("\r\n");  
		$this->load->library('email', $config);
		
	}
	
	public function listlms(){
		//echo "hi"; exit;
		$this->load->view('LMS/list_lms');
	}
	public function addlms(){
		//echo "hi"; exit;
		$this->load->view('LMS/add_lms');
	}
	function add_lms()
	{
		$department=$this->input->post('department');
		$dayscount=$this->input->post('dayscount');
		$totalday=$this->input->post('totalday');
		
		for($i=0; $i<count($dayscount); $i++) {
			$dayid=$dayscount[$i];
			
		$daytest=$this->input->post('daytest');
		$videonameid=$this->input->post('videoname'.$dayid);
		for($j=0; $j<count($videonameid); $j++)
		{
			$vid=$videonameid[$j];
			$daytest=$this->input->post('daytest'.$dayid);
			$videoname=$this->input->post('videoname'.$dayid);
			$videolink=$this->input->post('videolink'.$dayid);
			$pdfname=$this->input->post('pdf_name'.$dayid);
			//$pdffile=$this->input->file('pdffile'.$dayid);
			$pdffile=$_FILES['pdf_file'.$dayid]['name'][$j];
			if($pdffile<>'')
			{
			$image1=explode('.',$pdffile);
			$pdfnamefile=end($image1);
			$newname=time().$dayid.$j.'.'.$pdfnamefile;
			move_uploaded_file($_FILES["pdf_file".$dayid]["tmp_name"][$j],SITE_ROOT.'upload/lmsstudy/'. $newname);
			//echo $_SERVER['DOCUMENT_ROOT']; exit;
			}else
			{
			$newname='';
			}
			$data=array('department_id'=>$department,'s_day'=>$totalday[$i],'video_name'=>$videoname[$j],'video_link'=>$videolink[$j],'pdf_name'=>$pdfname[$j],'pdf_file'=>$newname,'status'=>1,'added_by'=>$_SESSION['logged_in']['user_id'],'added_on'=>date('Y-m-d H:i:s'));
			$res=$this->db->insert('lms_study',$data);
		}
		}
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully Update.</span></div><br/>');
				redirect(page_url.'LMS/listlms/'.$department);
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record Not Added.</span></div><br/>');
				redirect(page_url.'LMS/listlms/'.$this->uri->segment(3));
		}
		
		//redirect(page_url.'Challan/inward_challan_dashboard');
		
		
	}

	public function list_data()
	{	
		$i=1;
		$department_data= array();
		$this->db->select('a.*,b.department_id,department')->from('lms_study a');
		$this->db->join('departments b','a.department_id=b.department_id','left');
		//$this->db->order_by('b.company_name','asc');
		$this->db->where('a.department_id',$this->uri->segment(3));
		$query = $this->db->get();
		
		if($query->num_rows() > 0){
			$res = $query->result();
		foreach($res as $row){
												
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."LMS/update_lms_status/".$row->department_id."/".$row->lms_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."LMS/update_lms_status/".$row->department_id."/".$row->lms_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."LMS/edit_lms/".$row->department_id."/".$row->lms_id."'><i class='fa fa-pencil'></i></a>";	
			
			
			
			$matrialupload="<a href='".page_url."LMS/listlms/".$row->department_id."'><span class='btn btn-info btn-xs'>Add/view LMS</span></a>";
			
			$testquestion="<a href='".page_url."LMS/list_question/".$row->department_id."/".$row->s_day."'><span class='btn btn-info btn-xs'>Add/view</span></a>";
			
			$department_data[] = array('sr_no'=>$i,
			'department'=>$row->department,
			's_day'=>$row->s_day,
			'v_name'=>$row->video_name,
			'v_link'=>$row->video_link,
            'p_name'=>$row->pdf_name,
            'p_link'=>$row->pdf_file,
            'test_qus'=>$testquestion,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			
			
		
		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($department_data),
			"iTotalDisplayRecords" => count($department_data),
			"aaData"=>$department_data);
		echo json_encode($results);
	}

	
	

public function edit_lms(){
		$this->load->view('LMS/edit_lms');
	}



public function update_edit(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('daytest', 'daytest', 'required|trim');
		$this->form_validation->set_rules('videoname', 'videoname', 'required|trim');
		$this->form_validation->set_rules('videolink', 'videolink', 'required|trim');
		$this->form_validation->set_rules('pdf_name', 'pdf_name', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		    
			$this->load->view('LMS/edit_lms');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
			$uid=$this->input->post('uid');
			//echo $uid;exit;
			$oldfile=$this->input->post('old_file');
			$pdffile=$_FILES['pdf_file'.$dayid]['name'][$j];
			if($pdffile<>'')
			{
			$image1=explode('.',$pdffile);
			$pdfnamefile=end($image1);
			$newname=time().$dayid.$j.'.'.$pdfnamefile;
			move_uploaded_file($_FILES["pdf_file".$dayid]["tmp_name"][$j],SITE_ROOT.'upload/lmsstudy/'. $newname);
			//echo $_SERVER['DOCUMENT_ROOT']; exit;
			}else
			{
			$newname=$oldfile;
			}
			
		   $data=
			array('department_id'=>$this->input->post('department'),
			's_day'=>$this->input->post('daytest'),
			'video_name'=>$this->input->post('videoname'),
			'video_link'=>$this->input->post('videolink'),
			'pdf_name'=>$this->input->post('pdf_name'),
			'pdf_file'=>$newname,
			'added_by'=>$user_id,
			'update_on'=>$added_time);
			$this->db->where('lms_id',$uid);
			$res = $this->db->update('lms_study',$data);
			
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully Update.</span></div><br/>');
				redirect(page_url.'LMS/listlms/'.$this->input->post('department'));
		  
			}
	}
	public function update_lms_status()
	{
		/*************Dynamic information****************/
		$category =  $this->uri->segment(3);
		$identifier =  $this->uri->segment(4);
		$sval =  $this->uri->segment(5);
		$field_name = "lms_id";
		$table = "lms_study";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Status successfully updated.</div>');
			redirect(page_url.'LMS/listlms/'.$category);
	}
	public function add_paper()
	{
		$this->load->view('/LMS/add_questions');
	}
	function add_question()
	{
		$department=$this->input->post('department');
		$dayscount=$this->input->post('dayscount');
		$question=$this->input->post('questions');
		for($i=0; $i<count($question); $i++) {
			$dayid=$dayscount[$i];
			
		$daytest=$this->input->post('dayquestion');
		//echo "<pre>"; print_r($question); exit;
		$correctanswer1=$this->input->post('ans'.$dayid.'1');
		if($correctanswer1=='')
		{
			$cans1=0;
		}else
		{
			$cans1=$correctanswer1;
		}
		$correctanswer2=$this->input->post('ans'.$dayid.'2');
		if($correctanswer2=='')
		{
			$cans2=0;
		}else
		{
			$cans2=$correctanswer2;
		}
		$correctanswer3=$this->input->post('ans'.$dayid.'3');
		if($correctanswer3=='')
		{
			$cans3=0;
		}else
		{
			$cans3=$correctanswer3;
		}
		
		$answertext1=$this->input->post('anstext'.$dayid.'1');
		$answertext2=$this->input->post('anstext'.$dayid.'2');
		$answertext3=$this->input->post('anstext'.$dayid.'3');
		
		$data=array('department_id'=>$department,'day_qus'=>$daytest,'question'=>$question[$i],'status'=>1,'added_on'=>date('Y-m-d H:i:s'),'added_by'=>$_SESSION['logged_in']['user_id'],'added_on'=>date('Y-m-d H:i:s'));
		$resu=$this->db->insert('lms_questions',$data);
		
		$insert_id = $this->db->insert_id();
			
		$data=array('question_id'=>$insert_id,'answer'=>$answertext1,'flag'=>$cans1,'added_on'=>date('Y-m-d H:i:s'));
		$this->db->insert('lms_answer',$data);
		$data=array('question_id'=>$insert_id,'answer'=>$answertext2,'flag'=>$cans2,'added_on'=>date('Y-m-d H:i:s'));
		$this->db->insert('lms_answer',$data);
		$data=array('question_id'=>$insert_id,'answer'=>$answertext3,'flag'=>$cans3,'added_on'=>date('Y-m-d H:i:s'));
		$res=$this->db->insert('lms_answer',$data);
		
		}
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully Update.</span></div><br/>');
				redirect(page_url.'LMS/add_paper/'.$department);
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record Not Added.</span></div><br/>');
				redirect(page_url.'LMS/add_paper/'.$department);
		}
		
		//redirect(page_url.'Challan/inward_challan_dashboard');
		
		
	}
	public function list_question()
	{
		$this->load->view('LMS/list_questions');
	}
	public function list_questions_data()
	{
		$i=1;
		$department_data= array();
		$this->db->select('a.*,b.department_id,department')->from('lms_questions a');
		$this->db->join('departments b','a.department_id=b.department_id','left');
		//$this->db->order_by('b.company_name','asc');
		$this->db->where('a.department_id',$this->uri->segment(3));
		$this->db->where('a.day_qus',$this->uri->segment(4));
		$query = $this->db->get();
		
		if($query->num_rows() > 0){
			$res = $query->result();
		foreach($res as $row){
			
			$ques=$this->db->select('answer')->from('lms_answer')->where('question_id',$row->q_id)->get();
			$arr=array();
			foreach($ques->result() as $answer)
			{
				$arr[]=$answer->answer;
				
			}
			//echo "<pre>"; print_r($arr); exit;
			$answered=implode(',',$arr);
			//echo $answered; exit;
			
			$anss=$this->db->select('answer')->from('lms_answer')->where('question_id',$row->q_id)->where('flag',1)->get();
			foreach($anss->result() as $ans)
			{
				$coranswer=$ans->answer;
			}
			
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."LMS/update_question_status/".$row->department_id."/".$row->day_qus."/".$row->q_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."LMS/update_question_status/".$row->department_id."/".$row->day_qus."/".$row->q_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."LMS/edit_question_lms/".$row->department_id."/".$row->day_qus."/".$row->q_id."'><i class='fa fa-pencil'></i></a>";	
			
			$testquestion="<a href='".page_url."LMS/list_question/".$row->q_id."/".$row->day_qus."/".$row->day_qus."'><span class='btn btn-info btn-xs'>Add/view</span></a>";
			
			$department_data[] = array('sr_no'=>$i,
			'department'=>$row->department,
			's_day'=>$row->day_qus,
			'v_name'=>$row->question,
            'test_qus'=>$answered,
            'correct'=>$coranswer,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			
			
		
		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($department_data),
			"iTotalDisplayRecords" => count($department_data),
			"aaData"=>$department_data);
		echo json_encode($results);
	}
	
	public function update_question_status()
	{
		/*************Dynamic information****************/
		$category = $this->uri->segment(3);
		$days =  $this->uri->segment(4);
		$ques =  $this->uri->segment(5);
		$sval =  $this->uri->segment(6);
		$field_name = "q_id";
		$table = "lms_questions";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$ques,$field_name);
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Status successfully updated.</div>');
			redirect(page_url.'LMS/list_question/'.$category.'/'.$days);
	}
	
	public function edit_question_lms(){
		$this->load->view('LMS/edit_questions');
	}
	function update_question()
	{
		$department=$this->input->post('department');
		$dayscount=$this->input->post('dayscount');
		$question=$this->input->post('questions');
		
			
		$daytest=$this->input->post('dayquestion');
		$updid=$this->input->post('quesid');
		$dayid=$this->input->post('dayid');
	
		$data=array('department_id'=>$department,'day_qus'=>$daytest,'question'=>$question,'status'=>1,'update_on'=>date('Y-m-d H:i:s'),'added_by'=>$_SESSION['logged_in']['user_id']);
		$this->db->where('q_id',$updid);
		$res=$this->db->update('lms_questions',$data);
		
		$answerid1=$this->input->post('answerid');
		
		for($j=0; $j<count($answerid1); $j++)
		{
			$correctanswer1=$_REQUEST['anschk'];
		
		if($correctanswer1==$j)
		{
			$cans1=1;
			}else
			{
				$cans1=0;
			}
			$id=$answerid1[$j];
			$answertext1=$_REQUEST['anstext'][$j];
			
			$data=array('answer'=>$answertext1,'flag'=>$cans1,'added_on'=>date('Y-m-d H:i:s'));
			$this->db->where('a_id',$id);
			$resu=$this->db->update('lms_answer',$data);
		}
		
		
		
		
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully Update.</span></div><br/>');
				redirect(page_url.'LMS/list_question/'.$department.'/'.$dayid);
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record Not Added.</span></div><br/>');
				redirect(page_url.'LMS/list_question/'.$department.'/'.$dayid);
		}
		
		//redirect(page_url.'Challan/inward_challan_dashboard');
		
		
	}
	public function Start_training()
	{
		$this->load->view('LMS/dashboard_traininng');
	}
	
	public function trainingdone()
	{
		$department=$this->input->post('depid');
		$dayid=$this->input->post('day');
		$userid=$this->input->post('userid');
		$que=$this->db->select('*')->from('lms_user_confrim')->where('department_id',$department)->where('t_day',$dayid)->where('userid',$userid)->get();
		if($que->num_rows() >0)
		{
			$data=array('update_on'=>date('Y-m-d H:i:s'),
			'added_by'=>$userid);
			$this->db->where('department_id',$department);
			$this->db->where('t_day',$dayid);
			$this->db->where('userid',$userid);
			$res=$this->db->update('lms_user_confrim',$data);
			
			if($res){
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully Update.</span></div><br/>');
				redirect(page_url.'LMS/testquestion/'.$department.'/'.$dayid);
			}else
			{
				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record Not Added.</span></div><br/>');
					redirect(page_url.'LMS/Start_training/'.$department.'/'.$dayid);
			}
		}else{
		
		$data=array(
			'department_id'=>$department,
			't_day'=>$dayid,
			'userid'=>$userid,
			'status'=>1,
			'added_on'=>date('Y-m-d H:i:s'),
			'added_by'=>$userid
		);
		$res=$this->db->insert('lms_user_confrim',$data);
		
		
		
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully Update.</span></div><br/>');
				redirect(page_url.'LMS/testquestion/'.$department.'/'.$dayid);
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record Not Added.</span></div><br/>');
				redirect(page_url.'LMS/Start_training/'.$department.'/'.$dayid);
		}
		}
	}
	public function testquestion()
	{
		$this->load->view('LMS/testquestion');
	}
	public function test_lms_submit()
	{
		$department=$this->input->post('department');
		$questionid=$this->input->post('question');
		
		for($i=0; $i<count($questionid); $i++){
		$dayid=$this->input->post('tday');
		$userid=$this->input->post('user_id');
		$answerchk=$this->input->post('answerchk'.$questionid[$i]);
		//echo $answerchk; exit;
		$alredy=$ques=$this->db->select('id')->from('lms_user_answer')->where('department_id',$department)->where('s_day',$dayid)->where('user_id',$userid)->where('question_id',$questionid[$i])->get();
		if($alredy->num_rows() > 0)
		{
			$this->db->where('user_id', $userid);
			$this->db->where('department_id',$department );
			$this->db->where('question_id',$questionid[$i]);
			$this->db->delete('lms_user_answer');
		}
		$data=array(
		'department_id'=>$department,
		's_day'=>$dayid,
		'question_id'=>$questionid[$i],
		'answer_id'=>$answerchk,
		'user_id'=>$userid,
		'added_on'=>date('Y-m-d H:i:s'));
		$res=$this->db->insert('lms_user_answer',$data);
		
		}
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully Submited.</span></div><br/>');
				redirect(page_url.'LMS/testresult/'.$department.'/'.$dayid);
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record Not Added.</span></div><br/>');
				redirect(page_url.'LMS/Start_training/'.$department.'/'.$dayid);
		}
	}
	public function testresult()
	{
		$department=$this->uri->segment(3);
		$dayid=$this->uri->segment(4);
		$userid=$_SESSION['logged_in']['user_id'];
		
		$ques=$this->db->select('*')->from('lms_user_answer')->where('department_id',$department)->where('s_day',$dayid)->where('user_id',$userid)->get();
		if($ques->num_rows() >0)
		{
			foreach($ques->result() as $qaans)
			{
				$res=$this->db->select('flag')->from('lms_answer')->where('a_id',$qaans->answer_id)->where('flag','1')->get();
				foreach($res->result() as $ans)
				{
					//echo $ans->flag; exit;
				if($ans->flag =='1')
				{
				$data=array(
				'score'=>'1'
				);
				}	
				else
				{
				$data=array(
				'score'=>'0'
				);
					
				}
				$this->db->where('id',$qaans->id);
				$qu=$this->db->update('lms_user_answer',$data);
				
				}
			
				
			}
			$que=$this->db->select('q_id')->from('lms_questions')->where('day_qus',$dayid)->where('department_id',$department)->get();
			$totalques=$que->num_rows();
			$anscorrect=$this->db->select('id')->from('lms_user_answer')->where('department_id',$department)->where('s_day',$dayid)->where('user_id',$userid)->where('score',1)->get();
			$totalcorrect=$anscorrect->num_rows();
			
			$half=$totalques / 2;
			
			$percentage=$totalcorrect/$totalques*100;
			
			if($totalcorrect >= $half)
			{
				$data=array(
				'department_id'=>$department,
				's_day'=>$dayid,
				'total_score'=>$totalcorrect,
				'percentage'=>$percentage,
				'user_id'=>$userid,
				'fresult'=>'PASS',
				'added_on'=>date('Y-m-d H:i:s'));
				$res=$this->db->insert('lms_user_result',$data);
				
				$data=array(
				'department_id'=>$department,
				'test_day'=>$dayid,
				'userid'=>$userid,
				'status'=>'1',
				'added_on'=>date('Y-m-d H:i:s'));
				$res=$this->db->insert('lms_result',$data);
				
			}else
			{
				$data=array(
				'department_id'=>$department,
				's_day'=>$dayid,
				'total_score'=>$totalcorrect,
				'percentage'=>$percentage,
				'user_id'=>$userid,
				'fresult'=>'FAIL',
				'added_on'=>date('Y-m-d H:i:s'));
				$res=$this->db->insert('lms_user_result',$data);
			}
		}
		
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully Submited.</span></div><br/>');
				redirect(page_url.'LMS/result/'.$department.'/'.$dayid);
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record Not Added.</span></div><br/>');
				redirect(page_url.'LMS/result/'.$department.'/'.$dayid);
		}
	}
	/****extara code***/
	public function final_result()
	{
		$departmentid=$this->uri->segment(3);
		$dayid=$this->uri->segment(4);
		$userid=$_SESSION['logged_in']['user_id'];
		
		
		
		
		$wrong=$this->db->select('*')->from('lms_user_answer')->where('s_day',1)->where('department_id',$departmentid)->where('user_id',$_SESSION['logged_in']['user_id'])->where('score',0)->get();
			if($wrong->num_rows() > 0)
			{
			foreach($wrong->result() as $wrongans);
			}
		$rightan=$right->num_rows();
		$wrongan=$wrong->num_rows();
		$qusti=$que->num_rows();
		$percentage=$rightan/$qusti*100;
		if($rightan>= $wrongan)
		{
			$result="PASS";
			$isdone="1";
			$data2=array(
			'status'=>1,
			'added_on'=>date('Y-m-d H:i:s'));

			$this->db->where('department_id',$department);
			$this->db->where('userid',$userid);
			$this->db->where('test_day',$dayid);
			$this->db->update('lms_result',$data2);
		}else
		{
			$result="FAIL";
			$isdone="0";
			$data2=array(
		'status'=>0,
		'added_on'=>date('Y-m-d H:i:s'));
		
		$this->db->where('department_id',$department);
		$this->db->where('userid',$userid);
		$this->db->where('test_day',$dayid);
		$this->db->update('lms_result',$data2);
		}
		
		
			
				
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully Submited.</span></div><br/>');
				redirect(page_url.'LMS/result/'.$departmentid.'/'.$dayid);
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record Not Added.</span></div><br/>');
				redirect(page_url.'LMS/result/'.$departmentid.'/'.$dayid);
		}
	}
	/*end*/
	public function result()
	{
		$this->load->view('LMS/lms_result');
	}
	public function ceritficate()
	{
		$this->load->view('LMS/ceritficate');
	}
}

