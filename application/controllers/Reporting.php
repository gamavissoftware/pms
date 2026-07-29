<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reporting extends CI_Controller { 

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
		$this->load->model('Fms_model','fmsmodel');
		$this->load->model('Fms_mismodel','fmsmismodel');
		$this->load->model('Delegation_model');
		$this->load->model('Store_model','storemodel');
		


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




		}
	
	
	public $jobcardara = array();
	
	public function index(){


$data=array('remark_flag'=>'0');
$this->db->where('tentative_time','1');
$this->db->where('remark_flag','1');
$this->db->update('audit_tasks',$data);
return true;
}
	
	public function reporting_index(){
	    $this->load->view('store/reporting_index');
	}
	
	public function update_access(){
		
		$userid = $this->input->post('userid');
		$submodule = $this->input->post('moduleid');
		$add = $this->input->post('addattr');
		
		$data = array('madd'=>$add,
		'upadtedOn'=>date('Y-m-d H:i:s'));
		
		$this->db->where('role_id',$userid);
		$this->db->where('submoduleid',$submodule);
		$res = $this->db->update('module_capablity',$data);
		if($res){
			if($add=='1'){
			echo "<span style='color:red;'>Given</span>"; exit;
			}else{
			echo "<span style='color:red;'>Removed</span>"; exit;	
			}
		}
		
	}
	
	public function update_edit_access(){
		
		$userid = $this->input->post('userid');
		$submodule = $this->input->post('moduleid');
		$edit = $this->input->post('editattr');
		
		$data = array('medit'=>$edit,
		'upadtedOn'=>date('Y-m-d H:i:s'));
		
		$this->db->where('role_id',$userid);
		$this->db->where('submoduleid',$submodule);
		$res = $this->db->update('module_capablity',$data);
		if($res){
			if($edit=='1'){
			echo "<span style='color:red;'>Given</span>"; exit;
			}else{
			echo "<span style='color:red;'>Removed</span>"; exit;	
			}
		}
		
	}
	
	
	public function update_sms_access(){
		
		$userid = $this->input->post('userid');
		$submodule = $this->input->post('moduleid');
		$sms = $this->input->post('smsattr');
		
		$data = array('sms'=>$sms,
		'upadtedOn'=>date('Y-m-d H:i:s'));
		
		$this->db->where('role_id',$userid);
		$this->db->where('submoduleid',$submodule);
		$res = $this->db->update('module_capablity',$data);
		if($res){
			if($sms=='1'){
			echo "<span style='color:red;'>Given</span>"; exit;
			}else{
			echo "<span style='color:red;'>Removed</span>"; exit;	
			}
		}
		
	}
	
	public function update_email_access(){
		
		$userid = $this->input->post('userid');
		$submodule = $this->input->post('moduleid');
		$email = $this->input->post('emailattr');
		
		$data = array('email'=>$email,
		'upadtedOn'=>date('Y-m-d H:i:s'));
		
		$this->db->where('role_id',$userid);
		$this->db->where('submoduleid',$submodule);
		$res = $this->db->update('module_capablity',$data);
		if($res){
			if($email=='1'){
			echo "<span style='color:red;'>Given</span>"; exit;
			}else{
			echo "<span style='color:red;'>Removed</span>"; exit;	
			}
		}
		
	}
	
	public function update_whatsapp_access(){
		
		$userid = $this->input->post('userid');
		$submodule = $this->input->post('moduleid');
		$whatsapp = $this->input->post('whatsappattr');
		
		$data = array('whatsapp'=>$whatsapp,
		'upadtedOn'=>date('Y-m-d H:i:s'));
		
		$this->db->where('role_id',$userid);
		$this->db->where('submoduleid',$submodule);
		$res = $this->db->update('module_capablity',$data);
		if($res){
			if($whatsapp=='1'){
			echo "<span style='color:red;'>Given</span>"; exit;
			}else{
			echo "<span style='color:red;'>Removed</span>"; exit;	
			}
		}
		
	}
	
	public function update_frequency_access(){
		
		$userid = $this->input->post('userid');
		$submodule = $this->input->post('moduleid');
		$frequency = $this->input->post('frequency');
		
		$data = array('frequency'=>$frequency,
		'upadtedOn'=>date('Y-m-d H:i:s'));
		
		$this->db->where('role_id',$userid);
		$this->db->where('submoduleid',$submodule);
		$res = $this->db->update('module_capablity',$data);
		if($res){
			if($frequency=='1'){
			echo "<span style='color:red;'>Given</span>"; exit;
			}else{
			echo "<span style='color:red;'>Removed</span>"; exit;	
			}
		}
		
	}
	
	public function dailyaudit()
	{
	  $id=array();
	  $currenttime=date('H:i');
	  //echo $currenttime;exit;
    $res=$this->db->select('*')->from('audit_tasks a')->join('daily_tat b','a.duration_id=b.id')->where('a.tentative_time','1')->get();
    foreach($res->result() as $rest)
    {
	    // $ad=date('');
	    $time=$rest->timing;
	    //echo $time;exit;
	    if($currenttime==$time)
	    {
	        //echo "hi";exit;
	        $data=array('remark_flag'=>'0');
	        $id=$rest->task_id;
	       // echo "bye";exit;
	        $this->db->where('task_id',$id);
	        $this->db->update('audit_tasks',$data);
	        
	        /*	$subjectname = "Daily Audit";
					$this->email->set_mailtype("html");
					$this->email->to('mangleshup@gmail.com');
					$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('support@prestogroup.com');
    				$this->email->subject($subjectname);
    				$this->email->message("hello");
    				$result11=$this->email->send()*/
	        
	    }
	    
	    
	}
	
	
	
	    
	  
	}
	
	
	public function twiceweekaudit()
	{
	  $id=array();
	  $currenttime=date('l');
	 //echo $currenttime;exit;
    $res=$this->db->select('*')->from('audit_tasks a')->join('twice_in_a_week_tat b','a.duration_id=b.id')->where('a.tentative_time','2')->get();
    foreach($res->result() as $rest)
    {
            $day1=$rest->first_day;
           // echo $day1;exit;
            $day2=$rest->second_day;
	    //echo $time;exit;
	    if($currenttime==$day1 || $currenttime==$day2)
	    {
	        //echo "hi";exit;
	        $data=array('remark_flag'=>'0');
	        $id=$rest->task_id;
	       // echo "bye";exit;
	        $this->db->where('task_id',$id);
	        $this->db->update('audit_tasks',$data);
	        
	        	$subjectname = "Twice a week Audit";
					$this->email->set_mailtype("html");
					$this->email->to('mangleshup@gmail.com');
					$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('support@prestogroup.com');
    				$this->email->subject($subjectname);
    				$this->email->message("hello");
    				$result11=$this->email->send();
    				
	   }	}
	   
	
	
	
	    
	  
	}
	




	public function weeklyaudit()
	{
	  $id=array();
	  $currenttime=date('l');
	 //echo $currenttime;exit;
    $res=$this->db->select('*')->from('audit_tasks a')->join('weekly_tat b','a.duration_id=b.id')->where('a.tentative_time','3')->get();
    foreach($res->result() as $rest)
    {
            $day1=ucfirst($rest->weekday);
          // echo $day1;exit;
          
	    //echo $time;exit;
	    if($currenttime==$day1)
	    {
	        //echo "hi";exit;
	        $data=array('remark_flag'=>'0');
	        $id=$rest->task_id;
	       // echo "bye";exit;
	        $this->db->where('task_id',$id);
	        $this->db->update('audit_tasks',$data);
	        
	        
	        	$subjectname = "Weekly Audit";
					
	    
	   }	}
	
	
	
	  
	}
	
	
	public function twicemonthaudit()
	{
	  $id=array();
	  $currenttime=date('d');
	  //echo $currenttime;exit;
	 //echo $currenttime;exit;
    $res=$this->db->select('*')->from('audit_tasks a')->join('twice_in_a_month_tat b','a.duration_id=b.id')->where('a.tentative_time','4')->get();
    foreach($res->result() as $rest)
    {
        $day1=ucfirst($rest->first_date);
        $day2=ucfirst($rest->second_date);
        
        
	    if($currenttime==$day1 || $currenttime==$day2)
	    {
	        //echo "hi";exit;
	        $data=array('remark_flag'=>'0');
	        $id=$rest->task_id;
	       // echo "bye";exit;
	        $this->db->where('task_id',$id);
	        $this->db->update('audit_tasks',$data);
	        
	        
	        	$subjectname = "Twice Month Audit";
				
    				
	   }	}
	
	
	
	    
	  
	}
	
	
	
	public function monthlyaudit()
	{
	  $id=array();
	  $currenttime=date('d');
	  //echo $currenttime;exit;
	 //echo $currenttime;exit;
    $res=$this->db->select('*')->from('audit_tasks a')->join('monthly_tat b','a.duration_id=b.id')->where('a.tentative_time','5')->get();
    foreach($res->result() as $rest)
    {
        $day1=ucfirst($rest->monthly);
        $day=trim((int) filter_var($day1, FILTER_SANITIZE_NUMBER_INT));
        
         if($currenttime==$day)
	    {
	        //echo "hi";exit;
	        $data=array('remark_flag'=>'0');
	        $id=$rest->task_id;
	       // echo "bye";exit;
	        $this->db->where('task_id',$id);
	        $this->db->update('audit_tasks',$data);
	        
	        
	        	$subjectname = "Monthly Audit";
				
    				
	   }	}
	
	
	
	    
	  
	}
	
	
	
		public function quaterlyaudit()
	{
	  $id=array();
	  $currenttime=ucfirst(date('F'));
	  $currdate=date('d-m-y');
	// echo $currdate;exit;
	 //echo $currenttime;exit;
    $res=$this->db->select('*')->from('audit_tasks a')->join('quarterly_tat b','a.duration_id=b.id')->where('a.tentative_time','6')->get();
    foreach($res->result() as $rest)
    {
        //echo "<pre>"; print_r($rest);exit;
        $day1=ucfirst($rest->month);
       
        
         if($currenttime==$day1)
	    {
	        //echo "hi";exit;
	        $data=array('remark_flag'=>'0');
	        $id=$rest->task_id;
	       // echo "bye";exit;
	        $this->db->where('task_id',$id);
	        $this->db->update('audit_tasks',$data);
	        
	        
	        	$subjectname = "Quaterly Audit";
					
    				
	   }	}
	
	
	
	    
	  
	}
	
	
		public function twiceyear()
	{
	  $id=array();
	  $currenttime=ucfirst(date('F'));
	 
//echo $currenttime;exit;
	 //echo $currenttime;exit;
    $res=$this->db->select('*')->from('audit_tasks a')->join('twice_in_a_year_tat b','a.duration_id=b.id')->where('a.tentative_time','7')->get();
    foreach($res->result() as $rest)
    {
       // echo "<pre>"; print_r($rest);exit;
        $day1=ucfirst($rest->first_month);
        $day2=ucfirst($rest->second_month);
        
         if($currenttime==$day1 || $currenttime==$day2)
	    {
	        //echo "hi";exit;
	        $data=array('remark_flag'=>'0');
	        $id=$rest->task_id;
	       // echo "bye";exit;
	        $this->db->where('task_id',$id);
	        $this->db->update('audit_tasks',$data);
	        
	        
	        	$subjectname = "Twice Year Audit";
					
	   }	}
	
	
	
	    
	  
	}
	
	
	
	
		public function yearly()
	{
	  $id=array();
	  $currenttime=ucfirst(date('F'));
	 
//echo $currenttime;exit;
	 //echo $currenttime;exit;
    $res=$this->db->select('*')->from('audit_tasks a')->join('yearly_tat b','a.duration_id=b.id')->where('a.tentative_time','7')->get();
    foreach($res->result() as $rest)
    {
       // echo "<pre>"; print_r($rest);exit;
        $day1=ucfirst($rest->yearly);
     
        
         if($currenttime==$day1)
	    {
	        //echo "hi";exit;
	        $data=array('remark_flag'=>'0');
	        $id=$rest->task_id;
	       // echo "bye";exit;
	        $this->db->where('task_id',$id);
	        $this->db->update('audit_tasks',$data);
	        
	        
	        	$subjectname = "Yearly Audit";
				
				
    				
    				
	   }	}
	
	
	
	    
	  
	}
	
	
	
		public function twiceaday()
	{
	  $id=array();
	  $currenttime=date('H:i');
	  //echo $currenttime;exit;
	 
//echo $currenttime;exit;
	 //echo $currenttime;exit;
    $res=$this->db->select('*')->from('audit_tasks a')->join('twice_in_a_day_tat b','a.duration_id=b.id')->where('a.tentative_time','7')->get();
    foreach($res->result() as $rest)
    {
      // echo "<pre>"; print_r($rest);exit;
        $day1=ucfirst($rest->first_time);
        $day2=ucfirst($rest->second_time);
        
         if($currenttime==$day1 || $currenttime==$day2)
	    {
	        //echo "hi";exit;
	        $data=array('remark_flag'=>'0');
	        $id=$rest->task_id;
	       // echo "bye";exit;
	        $this->db->where('task_id',$id);
	        $this->db->update('audit_tasks',$data);
	        
	        
	         	$subjectname = "Twice a Day";
				
         }}}
	
	
	function warrantynotifications()
	{
	    $currdate=date('Y-m-d');
	    $sixteendaybefore=date('Y-m-d', strtotime($currdate. ' + 15 days'));
	    $sevendaysbefore=date('Y-m-d',strtotime($currdate. ' + 7 days'));
	    $onedaysbefore=date('Y-m-d',strtotime($currdate. ' + 1 days'));
	    
	   $rest=$this->db->select('warranty_end_date,serial_number,model_number,model_number')->from('presto_it_assets')->where('warranty_end_date>',$currdate.' 0:00:00')->where('warranty_end_date<',$sixteendaybefore.' 23:59:59')->get();
	    //echo "<pre>"; print_r($rest->result());exit;
	    if($rest->num_rows()<>0)
	    {
	        
	        
					  
	    foreach($rest->result() as $restt)
	    {
                if(($restt->warranty_end_date==$sixteendaybefore)|| ($restt->warranty_end_date==$sevendaysbefore) || ($restt->warranty_end_date==$onedaysbefore))
                {
                    $msg=$restt->asset_type;
                
                echo $msg;exit;
                $subjectname = "Twice a Day";
               
                
                
                
                }
	       
	    }
	    
	    }
	    
	}
	
	
	function misscore()
	{
		$this->load->view('FMS/overallmisscore');
		
	}
	
	
	
		function misscoreoverall()
	{
		$scheduler_data=array();
		
		if($this->uri->segment(3)<>'')
		{
		$fsdate=date('Y-m-d',strtotime(base64_decode($this->uri->segment(3))));
		$fedate=date('Y-m-d',strtotime(base64_decode($this->uri->segment(4))));
		}
		else{
			$fsdate='';
			$fedate='';
		}
		
		
		/** Previous Planned Score **/
	//$previousweekdates=$this->fmsmismodel->getpreviousweekalldates();
	//	$currentweekdates=$this->fmsmismodel->getcurrentweekalldates();
	
	$previousweekdates=$this->fmsmismodel->getcurrenttoprevioustopreviousweekalldatess();
	$currentweekdates=$this->fmsmismodel->getcurrenttopreviousweekalldatess();
	

	
		$users=$this->db->select('user_id,first_name,last_name')->from('system_users')->where('user_status','1')->where('hide_profile','0')->order_by('first_name','ASC')->get();
		if($users->num_rows()>0)
		{
			$t=1;
		
		foreach($users->result() as $users1)
		{ 	
		
		/** Get Previous planned date **/
		$rest=$this->db->select('plannedscore')->from('userwiseplannedscore')->where('weekstartdate',$previousweekdates[0])->where('weekstartdate',$previousweekdates[5])->where('userid',$users1->user_id)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $rest1);
			$previousagreedscore=$rest1->plannedscore;
		}else{
			$previousagreedscore='';
		}

		/** End **/
		
		/** Get Current planned date **/
		$rest1=$this->db->select('plannedscore')->from('userwiseplannedscore')->where('weekstartdate',$currentweekdates[0])->where('weekenddate',$currentweekdates[5])->where('userid',$users1->user_id)->get();
		if($rest1->num_rows()>0)
		{
			foreach($rest->result() as $rest1);
			$previousagreedscore=$rest1->plannedscore;
		}else{
			$previousagreedscore='';
		}

		/** End **/
		
		/**FMS  CORRECT **/
			$currentweeklyfmsscore=$this->fmsmismodel->fmscurrentweekscore($users1->user_id,$fsdate,$fedate);
			$previousweeklyfmsscore=$this->fmsmismodel->fmspreviousweekscore($users1->user_id,$fsdate,$fedate);
			    /** FMS **/
			
			/** CHECKLIST CORRECT **/
			$currentweeklychecklistscore=$this->fmsmismodel->checklistcurrentweekmis($users1->user_id,$fsdate,$fedate);
			$previousweeklychecklistscore=$this->fmsmismodel->checklistpreviousweekmis($users1->user_id,$fsdate,$fedate);
			/**END **/
			
			/** DELEGATION  PREVIOUS WEEK PENDING**/
			$currentweeklydelegationscore=$this->fmsmismodel->delegationcurrentweekmis($users1->user_id,$fsdate,$fedate);
		
			$delegationpreviousweekmis=$this->fmsmismodel->delegationpreviousweekmis($users1->user_id,$fsdate,$fedate);
			/**END **/
			
			/** FORM MIS **/
			$formcurrentweekmis=$this->fmsmismodel->formcurrentweekmis($users1->user_id,$fsdate,$fedate);
			$formrepreviousweekmis=$this->fmsmismodel->formpreviousweekmis($users1->user_id,$fsdate,$fedate);
			/**END **/

		
			/** STORE RECIEPT **/
			$storerecieptcurrentweekmis1=$this->fmsmismodel->storerecieptcurrentweekmis($users1->user_id,$fsdate,$fedate);


			/** END **/
			
		/** SUM ALL CURRENT SCORES **/
		$currentweeksum=($currentweeklychecklistscore)+($currentweeklyfmsscore)+(round($formcurrentweekmis,2))+($currentweeklydelegationscore);
		/** end **/


		
		/** SUM ALL PREVIOUS SCORES **/
		
		$previousweeksum=$previousweeklychecklistscore+$previousweeklyfmsscore+$formrepreviousweekmis+$delegationpreviousweekmis;
		/** end **/
	
		if($currentweeklychecklistscore=='0')
		{	
				$currentweeklychecklistscore='0';
		}else{
				$currentweeklychecklistscore=$currentweeklychecklistscore." %";
		}
		

if($currentweeklyfmsscore==0)
		{	
				$currentweeklyfmsscore='0%';
		}else{
				$currentweeklyfmsscore=$currentweeklyfmsscore." %";
		}
		
		
		if($previousweeksum=='0')
		{	
				$previousweeksum='0%';
		}else{
				$previousweeksum=$previousweeksum." %";
		}
		
	
	$arr[]=array('name'=>$users1->first_name." ".$users1->last_name,'previousscore'=>$previousweeksum,'fmsscore'=>$currentweeklyfmsscore,'checklistscore'=>$currentweeklychecklistscore,'delegationscore'=>$currentweeklydelegationscore,'formscore'=>round($formcurrentweekmis,2),'totalscore'=>abs(round($currentweeksum,2)),'previousplannedscore'=>$previousagreedscore,'futureplannedscore'=>'','user_id'=>$users1->user_id);
		$t++;
		}
		}
		
	$price = array_column($arr, 'totalscore');
    array_multisort($price, SORT_DESC, $arr);
	
	if($users->num_rows()>0)
		{
		    $ta=1;
	foreach($arr as $arrs)
	{
	    
	  if($arrs['totalscore']<>'0' || $arrs['totalscore']<>'0.00')
	  {
	      $to="-".$arrs['totalscore'];
	  }else
	  {
	      $to=0;
	  }
	    
	    $tyeue=$this->db->select('id')->from('misemdone')->where('emdate',date('Y-m-d'))->where('userid',$arrs['user_id'])->get();
	    if($tyeue->num_rows()==0)
	    {
	    $em="<input type='checkbox' name='checkit' id='emdone".$arrs['user_id']."' onclick='markemdone(".$arrs['user_id'].",".$ta.")'>";
	    }else
	    {
	       $em="EM DONE"; 
	    }
	   
	   
			

	   $scheduler_data[] = array(
				'sr_no'=>$ta,
				'name'=>$arrs['name'],
				'previousscore'=>$arrs['previousscore'],
				'checklistscore'=>$arrs['checklistscore'],
				'delegationscore'=>$arrs['delegationscore'],
				'fmsscore'=>$arrs['fmsscore'],
				'formscore'=>$arrs['formscore'],
				'other'=>$storerecieptcurrentweekmis1,
				'totalscore'=>$to,
				'previousplannedscore'=>$arrs['previousplannedscore'],
				'futureplannedscore'=>'',
				'remarks'=>$em
				);
	    
   $ta++; }
	
    $results = array(
    "sEcho" => 1,
    "iTotalRecords" => count($scheduler_data),
    "iTotalDisplayRecords" => count($scheduler_data),
    "aaData"=>$scheduler_data);
    echo json_encode($results);	
    
    }else
    {
    $results = array(
    "sEcho" => 1,
    "iTotalRecords" => count($scheduler_data),
    "iTotalDisplayRecords" => count($scheduler_data),
    "aaData"=>$scheduler_data);
    echo json_encode($results);	
    
    }

		
		
		
	}
	
	
		function misscoreoveralljustbackup()
	{
		$scheduler_data=array();
		
		if($this->uri->segment(3)<>'')
		{
		$fsdate=date('Y-m-d',strtotime(base64_decode($this->uri->segment(3))));
		$fedate=date('Y-m-d',strtotime(base64_decode($this->uri->segment(4))));
		}
		else{
			$fsdate='';
			$fedate='';
		}
		
		
		/** Previous Planned Score **/
	$previousweekdates=$this->fmsmismodel->getpreviousweekalldates();
		$currentweekdates=$this->fmsmismodel->getcurrentweekalldates();
		
		$users=$this->db->select('user_id,first_name,last_name')->from('system_users')->where('user_status','1')->where('hide_profile','0')->order_by('first_name','ASC')->get();
		if($users->num_rows()>0)
		{
			$t=1;
		
		foreach($users->result() as $users1)
		{ 	
		
		/** Get Previous planned date **/
		$rest=$this->db->select('plannedscore')->from('userwiseplannedscore')->where('weekstartdate',$previousweekdates[0])->where('weekstartdate',$previousweekdates[5])->where('userid',$users1->user_id)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $rest1);
			$previousagreedscore=$rest1->plannedscore;
		}else{
			$previousagreedscore='';
		}

		/** End **/
		
		/** Get Current planned date **/
		$rest1=$this->db->select('plannedscore')->from('userwiseplannedscore')->where('weekstartdate',$currentweekdates[0])->where('weekenddate',$currentweekdates[5])->where('userid',$users1->user_id)->get();
		if($rest1->num_rows()>0)
		{
			foreach($rest->result() as $rest1);
			$previousagreedscore=$rest1->plannedscore;
		}else{
			$previousagreedscore='';
		}

		/** End **/
		
		/**FMS  CORRECT **/
			$currentweeklyfmsscore=$this->fmsmismodel->fmscurrentweekscore($users1->user_id,$fsdate,$fedate);
			$previousweeklyfmsscore=$this->fmsmismodel->fmspreviousweekscore($users1->user_id,$fsdate,$fedate);
			/** FMS **/
			
			/** CHECKLIST CORRECT **/
			$currentweeklychecklistscore=$this->fmsmismodel->checklistcurrentweekmis($users1->user_id,$fsdate,$fedate);
			$previousweeklychecklistscore=$this->fmsmismodel->checklistpreviousweekmis($users1->user_id,$fsdate,$fedate);
			/**END **/
			
			/** DELEGATION  PREVIOUS WEEK PENDING**/
			$currentweeklydelegationscore=$this->fmsmismodel->delegationcurrentweekmis($users1->user_id,$fsdate,$fedate);
		
			$delegationpreviousweekmis=$this->fmsmismodel->delegationpreviousweekmis($users1->user_id,$fsdate,$fedate);
			/**END **/
			
			/** FORM MIS **/
			$formcurrentweekmis=$this->fmsmismodel->formcurrentweekmis($users1->user_id,$fsdate,$fedate);
			$formrepreviousweekmis=$this->fmsmismodel->formpreviousweekmis($users1->user_id,$fsdate,$fedate);
			/**END **/
			
			
			
		/** SUM ALL CURRENT SCORES **/
		$currentweeksum=($currentweeklychecklistscore)+($currentweeklyfmsscore)+($formcurrentweekmis)+($currentweeklydelegationscore);
		/** end **/
		
		/** SUM ALL PREVIOUS SCORES **/
		
		$previousweeksum=$previousweeklychecklistscore+$previousweeklyfmsscore+$formrepreviousweekmis+$delegationpreviousweekmis;
		/** end **/
	
		if($currentweeklychecklistscore=='0')
		{	
				$currentweeklychecklistscore='0';
		}else{
				$currentweeklychecklistscore=$currentweeklychecklistscore." %";
		}
		

if($currentweeklyfmsscore==0)
		{	
				$currentweeklyfmsscore='0%';
		}else{
				$currentweeklyfmsscore=$currentweeklyfmsscore." %";
		}
		
		
		if($previousweeksum=='0')
		{	
				$previousweeksum='0%';
		}else{
				$previousweeksum=$previousweeksum." %";
		}
		
	
		
	
		$scheduler_data[] = array(
				'sr_no'=>$t,
				'name'=>$users1->first_name." ".$users1->last_name,
				'previousscore'=>$previousweeksum,
				'checklistscore'=>$currentweeklychecklistscore,
				'delegationscore'=>$currentweeklydelegationscore,
				'fmsscore'=>$currentweeklyfmsscore,
				'formscore'=>$formcurrentweekmis,
				'totalscore'=>round($currentweeksum,2),
				'previousplannedscore'=>$previousagreedscore,
				'futureplannedscore'=>'',
				'remarks'=>''
				);
		$t++;
		}
		
		
		$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);	
	
	}else
	{
		$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);	
		
	}
		
		
		
	}
	
	function misscoreoverallold10march()
	{
		$scheduler_data=array();
		
		/** Previous Planned Score **/
	$previousweekdates=$this->fmsmismodel->getpreviousweekalldates();
		$currentweekdates=$this->fmsmismodel->getcurrentweekalldates();
		
		$users=$this->db->select('user_id,first_name,last_name')->from('system_users')->where('user_status','1')->where('hide_profile','0')->order_by('first_name','ASC')->get();
		if($users->num_rows()>0)
		{
			$t=1;
		
		foreach($users->result() as $users1)
		{ 	
		
		/** Get Previous planned date **/
		$rest=$this->db->select('plannedscore')->from('userwiseplannedscore')->where('weekstartdate',$previousweekdates[0])->where('weekstartdate',$previousweekdates[5])->where('userid',$users1->user_id)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $rest1);
			$previousagreedscore=$rest1->plannedscore;
		}else{
			$previousagreedscore='';
		}

		/** End **/
		
		/** Get Current planned date **/
		$rest1=$this->db->select('plannedscore')->from('userwiseplannedscore')->where('weekstartdate',$currentweekdates[0])->where('weekenddate',$currentweekdates[5])->where('userid',$users1->user_id)->get();
		if($rest1->num_rows()>0)
		{
			foreach($rest->result() as $rest1);
			$previousagreedscore=$rest1->plannedscore;
		}else{
			$previousagreedscore='';
		}

		/** End **/
		
			/**FMS  **/
			$currentweeklyfmsscore=$this->fmsmismodel->fmscurrentweekscore($users1->user_id);
			$previousweeklyfmsscore=$this->fmsmismodel->fmspreviousweekscore($users1->user_id);
			/** FMS **/
			
			/** CHECKLIST **/
			$currentweeklychecklistscore=$this->fmsmismodel->checklistcurrentweekmis($users1->user_id);
			$previousweeklychecklistscore=$this->fmsmismodel->checklistpreviousweekmis($users1->user_id);
			/**END **/
			
			/** DELEGATION **/
			
			/** current week **/
				$taskdelegatedtomescore=$this->Delegation_model->currentweekmisfortaskdelegatedtome($users1->user_id);
				$taskdelegatedbymescore=$this->Delegation_model->currentweekmisforfollowuptakenontaskdelegatedbyme($users1->user_id);
				$delegationoverallscore=$taskdelegatedtomescore+$taskdelegatedbymescore;
				$delegationoverallscore=$delegationoverallscore;


				/** Previous week **/
				$taskdelegatedtomescoreprevious=$this->Delegation_model->previousweekmisfortaskdelegatedtome($users1->user_id);
				$taskdelegatedbymescoreprevious=$this->Delegation_model->previousweekmisforfollowuptakenontaskdelegatedbyme($users1->user_id);
				$delegationoverallscoreprevious=$taskdelegatedtomescoreprevious+$taskdelegatedbymescoreprevious;
				$delegationoverallscoreprevious=$delegationoverallscoreprevious;
							
			/** END DELEGATION **/
			
		/** SUM ALL CURRENT SCORES **/
		$currentweeksum=($currentweeklychecklistscore)+($currentweeklyfmsscore)+($delegationoverallscore);
		/** end **/
		
		/** SUM ALL PREVIOUS SCORES **/
		
		$previousweeksum=$previousweeklychecklistscore+$previousweeklyfmsscore+$delegationoverallscoreprevious;
		/** end **/
	
		if($currentweeklychecklistscore=='0')
		{	
				$currentweeklychecklistscore='';
		}else{
				$currentweeklychecklistscore=$currentweeklychecklistscore." %";
		}
		

if($currentweeklyfmsscore==0)
		{	
				$currentweeklyfmsscore='';
		}else{
				$currentweeklyfmsscore=$currentweeklyfmsscore." %";
		}
		
		
		if($previousweeksum=='0')
		{	
				$previousweeksum='';
		}else{
				$previousweeksum=$previousweeksum." %";
		}
		
		
		if($delegationoverallscore=='0')
		{	
				$delegationoverallscore='';
		}else{
				$delegationoverallscore=$delegationoverallscore." %";
		}
		
	
		
	
		$scheduler_data[] = array(
				'sr_no'=>$t,
				'name'=>$users1->first_name." ".$users1->last_name,
				'previousscore'=>$previousweeksum,
				'checklistscore'=>$currentweeklychecklistscore,
				'delegationscore'=>$delegationoverallscore,
				'fmsscore'=>$currentweeklyfmsscore,
				'totalscore'=>$currentweeksum,
				'previousplannedscore'=>$previousagreedscore,
				'futureplannedscore'=>'',
				'remarks'=>''
				);
		$t++;
		}
		
		
		$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);	
	
	}else
	{
		$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);	
		
	}
		
		
		
	}
	
	function misscoreoverallolddddd()
	{
		$scheduler_data=array();
		
		/** Previous Planned Score **/
	$previousweekdates=$this->fmsmismodel->getpreviousweekalldates();
		$currentweekdates=$this->fmsmismodel->getcurrentweekalldates();
		
		$users=$this->db->select('user_id,first_name,last_name')->from('system_users')->where('user_status','1')->where('hide_profile','0')->order_by('first_name','ASC')->get();
		if($users->num_rows()>0)
		{
			$t=1;
		
		foreach($users->result() as $users1)
		{ 	
		
		/** Get Previous planned date **/
		$rest=$this->db->select('plannedscore')->from('userwiseplannedscore')->where('weekstartdate',$previousweekdates[0])->where('weekstartdate',$previousweekdates[5])->where('userid',$users1->user_id)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $rest1);
			$previousagreedscore=$rest1->plannedscore;
		}else{
			$previousagreedscore='';
		}

		/** End **/
		
		/** Get Current planned date **/
		$rest1=$this->db->select('plannedscore')->from('userwiseplannedscore')->where('weekstartdate',$currentweekdates[0])->where('weekenddate',$currentweekdates[5])->where('userid',$users1->user_id)->get();
		if($rest1->num_rows()>0)
		{
			foreach($rest->result() as $rest1);
			$previousagreedscore=$rest1->plannedscore;
		}else{
			$previousagreedscore='';
		}

		/** End **/
		
			/**FMS  **/
			$currentweeklyfmsscore=$this->fmsmismodel->fmscurrentweekscore($users1->user_id);
			$previousweeklyfmsscore=$this->fmsmismodel->fmspreviousweekscore($users1->user_id);
			/** FMS **/
			
			/** CHECKLIST **/
			$currentweeklychecklistscore=$this->fmsmismodel->checklistcurrentweekmis($users1->user_id);
			$previousweeklychecklistscore=$this->fmsmismodel->checklistpreviousweekmis($users1->user_id);
			/**END **/
			
		/** SUM ALL CURRENT SCORES **/
		$currentweeksum=$currentweeklychecklistscore+$currentweeklyfmsscore;
		/** end **/
		
		/** SUM ALL PREVIOUS SCORES **/
		$previousweeksum=$previousweeklychecklistscore+$previousweeklyfmsscore;
		/** end **/
	
		if($currentweeklychecklistscore=='0')
		{	
				$currentweeklychecklistscore='';
		}else{
				$currentweeklychecklistscore=$currentweeklychecklistscore." %";
		}
		

if($currentweeklyfmsscore==0)
		{	
				$currentweeklyfmsscore='';
		}else{
				$currentweeklyfmsscore=$currentweeklyfmsscore." %";
		}
		
		
		if($previousweeksum=='0')
		{	
				$previousweeksum='';
		}else{
				$previousweeksum=$previousweeksum." %";
		}
		
	
		
	
		$scheduler_data[] = array(
				'sr_no'=>$t,
				'name'=>$users1->first_name." ".$users1->last_name,
				'previousscore'=>$previousweeksum,
				'checklistscore'=>$currentweeklychecklistscore,
				'delegationscore'=>'',
				'fmsscore'=>$currentweeklyfmsscore,
				'totalscore'=>$currentweeksum,
				'previousplannedscore'=>$previousagreedscore,
				'futureplannedscore'=>'',
				'remarks'=>''
				);
		$t++;
		}
		
		
		$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);	
	
	}else
	{
		$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);	
		
	}
		
		
		
	}

	
	
	function pendingorders()
	{
		$this->load->view('FMS/pendingorders');
		
	}
	
	
		
	function pendingordersforservice()
	{
		$this->load->view('FMS/pendingordersforservice');
		
	}
	

	function pending_order_list()
	{$finaldate="";
	    $id=$this->uri->segment(3);
		$scheduler_data=array();
	$currentstage='';
	$oddays='';
        $this->db->select('a.order_id,b.internal_order_no,b.added_on,b.company_name,c.first_name,c.last_name,b.sono,b.pono')->from('order_instruments a')->join('prestogroup_orders b','a.order_id=b.order_id')->join('order_planning d','d.order_id=b.order_id')->join('system_users c','b.marketing_person=c.user_id')->where('a.complete','0')->where('factory !=','2');
        
        if($id<>0)
        {
        if($id=='1')
        {
        $this->db->where('b.selforder','0');
        $this->db->where('b.order_type','SALE');
        }else if($id=='2')
        {
        $this->db->where('b.selforder','1');
         $this->db->where('b.order_type','SALE');
        }else if($id=='3')
        {
            $this->db->where('b.order_type','SERVICE');
        }
        }
        
        $order=$this->db->group_by('a.order_id')->order_by('b.added_on','ASC')->get();
		
		if($order->num_rows()>0)
		{
			$t=1;
			foreach($order->result() as $order1)
			{
		
		/** GET ALL INCOMPLETE JOBCARD **/
		$html = "<table border='1' style='width:100%;line-height:14px;font-size:11px;'><tr style='background-color:white;'><th style='padding:0px 0px 0px 0px;text-align:center;width:33%;'>INSTRUMENT</th><th style='padding:0px 0px 0px 0px;text-align:center;width:20%;'>JOB CARD NO.</th><th style='padding:0px 0px 0px 0px;text-align:center;width:33%;'>CURRENT STAGE</th><th style='padding:0px 0px 0px 0px;text-align:center;'>APPROX. DAYS TO COMPLETE</th></tr>";
		$inst=$this->db->select('c.factory,b.id,a.instruments_name,b.job_card_no')->from('presto_instruments a')->join('order_instruments b','a.id=b.item_id')->join('order_planning c','b.id=c.jobcard_id')->where('b.complete','0')->where('b.order_id',$order1->order_id)->get();
		if($inst->num_rows()>0)
		{
			
		foreach($inst->result() as $inst1)
		{ 
		if($inst1->factory=='1' || $inst1->factory=='4' || $inst1->factory=='8' ||  $inst1->factory=='9')
		{
			$odstage=$this->db->select('a.flowstage,b.fms_flow,b.pdays')->from('order_stage a')->join('fms_flow b','a.flowstage=b.flow_id')->where('a.jobcardid',$inst1->id)->where('a.orderid',$order1->order_id)->where('a.userstatus','0')->where('a.flowstage!=','1')->order_by('a.flowstage','ASC')->limit(1)->get();
			if($odstage->num_rows()>0)
			{
				$odstages=array();
				foreach($odstage->result() as $odstage1)
				{
				$odstages[]=$odstage1->fms_flow;
				}
				$currentstage=implode(',',$odstages);
				$oddays=$odstage1->pdays;
			}else{
				$currentstage='';
				$oddays=$odstage1->pdays."  Days";
			}
		}else if($inst1->factory=='2')
		{
			$currentstage='IN STOCK FMS';
		}else if($inst1->factory=='3'){
			
			$currentstage='BOUGHT OUT FMS';
		}else if($inst1->factory=='7'){
		    	$currentstage='IMPORTED ITEMS';
		}
		
		
		$getLatestDays = $this->db->select('days,updated_on')
						  ->from('order_days')
						  ->where('job_card_id',$inst1->id)
						  ->order_by('id','DESC')
						  ->limit(1)
						  ->get();
		
	$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($inst1->instruments_name)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($inst1->job_card_no)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($currentstage)."</td>";
				if($getLatestDays->num_rows() > 0) {
						foreach ($getLatestDays->result() as $getDays);

				$Date = date('Y-m-d',strtotime($getDays->updated_on));
				$finaldate=date('d-M-Y', strtotime($Date. ' + '.$getDays->days.' days'));


					$html .= "<td style='padding:2px 2px 2px 2px; text-align:center;'><span id='day".$inst1->id."'>".strtoupper($getDays->days)."</span> Days<br/><span id='finaldate".$inst1->id."'>".$finaldate."</span>";
					if ($_SESSION['logged_in']['user_id'] == 3) {
						$html .= "&nbsp;<a href='javascript:;' onclick='editOrderDays(".$inst1->id.")'><i class='fa fa-pencil'></i></a></td>";
						}
					} else {
					
						$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'><span id='day".$inst1->id."'>".strtoupper($odstage1->pdays)."</span> Days<br/><span id='finaldate".$inst1->id."'>".$finaldate."</span>";
						if ($_SESSION['logged_in']['user_id'] == 3) {
						$html .= "&nbsp;<a href='javascript:;' onclick='editOrderDays(".$inst1->id.")'><i class='fa fa-pencil'></i></a></td>";
						}
					}
				$html.="</tr>";
		}
		}
		$html.="</table>";
		
		/** END **/
		if($inst->num_rows()>0)
		{
			if($order1->pono=='')
			{
				
				if(file_exists($_SERVER['DOCUMENT_ROOT'].'/sfpo/'.$order1->internal_order_no.'.pdf'))
				{
					$pono=$order1->internal_order_no;
				}else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/sfpo/'.$order1->internal_order_no.'.PDF'))
				{
						$pono=$order1->internal_order_no;
				}else
				{
					$pono='';
				}
			}else
			{
				$pono=$order1->pono;
			}
		$scheduler_data[] = array(
				'sr_no'=>$t,
				'custname'=>$order1->company_name,
				'iono'=>$order1->internal_order_no,
				'sono'=>$order1->sono,
				'pono'=>$pono,
				'orderdate'=>date('d-m-Y',strtotime($order1->added_on)),
				'region'=>$order1->first_name." ".$order1->last_name,
				'product'=>$html,
				'currentstage'=>''
				);
			
			$t++;
		}
			}			
			
		
		$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
	
	}else
	{
		$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
		
	}
		
	}
	
	
		function pending_order_list_forservice()
	{
		$scheduler_data=array();
	
		$order=$this->db->select('a.order_id,b.internal_order_no,b.added_on,b.company_name,c.first_name,c.last_name')->from('order_instruments a')->join('prestogroup_orders b','a.order_id=b.order_id')->join('order_planning d','d.order_id=b.order_id')->join('system_users c','b.marketing_person=c.user_id')->where('a.complete','0')->where('factory !=','2')->where('b.order_type','SERVICE')->group_by('a.order_id')->order_by('b.added_on','ASC')->get();
		
		if($order->num_rows()>0)
		{
			$t=1;
			foreach($order->result() as $order1)
			{
		
		/** GET ALL INCOMPLETE JOBCARD **/
		$html = "<table border='1' style='width:100%;line-height:14px;font-size:11px;'><tr style='background-color:white;'><th style='padding:0px 0px 0px 0px;text-align:center;width:33%;'>INSTRUMENT</th><th style='padding:0px 0px 0px 0px;text-align:center;width:20%;'>JOB CARD NO.</th><th style='padding:0px 0px 0px 0px;text-align:center;width:33%;'>CURRENT STAGE</th><th style='padding:0px 0px 0px 0px;text-align:center;'>APPROX. DAYS TO COMPLETE</th></tr>";
		$inst=$this->db->select('c.factory,b.id,a.instruments_name,b.job_card_no')->from('presto_instruments a')->join('order_instruments b','a.id=b.item_id')->join('order_planning c','b.id=c.jobcard_id')->where('b.complete','0')->where('b.order_id',$order1->order_id)->get();
		if($inst->num_rows()>0)
		{
			
		foreach($inst->result() as $inst1)
		{ 
		if($inst1->factory=='1' || $inst1->factory=='4' || $inst1->factory=='8' || $inst1->factory=='9')
		{
			$odstage=$this->db->select('a.flowstage,b.fms_flow,b.pdays')->from('order_stage a')->join('fms_flow b','a.flowstage=b.flow_id')->where('a.jobcardid',$inst1->id)->where('a.orderid',$order1->order_id)->where('a.userstatus','0')->where('a.flowstage!=','1')->order_by('a.flowstage','ASC')->limit(1)->get();
			if($odstage->num_rows()>0)
			{
				$odstages=array();
				foreach($odstage->result() as $odstage1)
				{
				$odstages[]=$odstage1->fms_flow;
				}
				$currentstage=implode(',',$odstages);
			}else{
				$currentstage='';
			}
		}else if($inst1->factory=='2')
		{
			$currentstage='IN STOCK FMS';
		}else if($inst1->factory=='3'){
			
			$currentstage='BOUGHT OUT FMS';
		}else if($inst1->factory=='7'){
		    	$currentstage='IMPORTED ITEMS';
		}
		
		
	$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($inst1->instruments_name)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($inst1->job_card_no)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($currentstage)."</td>";
					$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($odstage1->pdays)." Days</td>";
				$html.="</tr>";
		}
		}
		$html.="</table>";
		
		/** END **/
		if($inst->num_rows()>0)
		{
		$scheduler_data[] = array(
				'sr_no'=>$t,
				'custname'=>$order1->company_name,
				'iono'=>$order1->internal_order_no,
				'orderdate'=>date('d-m-Y',strtotime($order1->added_on)),
				'region'=>$order1->first_name." ".$order1->last_name,
				'product'=>$html,
				'currentstage'=>''
				);
			
			$t++;
		}
			}			
			
		
		$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
	
	}else
	{
		$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
		
	}
		
	}
	
	
	function readyforpacking()
	{
		$this->load->view('FMS/ordersreadyforpacking');
	}
	
	function readyforpackinglist()
	{
		$scheduler_data = array();
		$pack=$this->db->select('b.factory,b.id as planid,a.id,a.job_card_no,b.plannedOn,c.instruments_name')->from('order_instruments a')->join('order_planning b','a.id=b.jobcard_id')->join('presto_instruments c','a.item_id=c.id')->where('a.complete','1')->where('a.packed','0')->get();
if($pack->num_rows()>0)
{
	$t=1;
	foreach($pack->result() as $pack1)
	{
		
		if($pack1->factory!='2' && $pack1->factory!='3')
		{
				$timest=$this->db->select('addedOn')->from('order_stage')->where('jobcardid',$pack1->id)->order_by('addedOn','DESC')->limit(1)->get();
				if($timest->num_rows()>0)
				{
				foreach($timest->result() as $timest1);

				$timesp=date('d-M-Y g:i A',strtotime($timest1->addedOn));

				}else{ $timesp="";   }
		
		}else if($pack1->factory=='2')
		{
			$timest=$this->db->select('addedOn')->from('instockfms')->where('planid',$pack1->planid)->get();
				if($timest->num_rows()>0)
				{
				foreach($timest->result() as $timest1);

				$timesp=date('d-M-Y g:i A',strtotime($timest1->addedOn));

				}else{ $timesp="";   }
			
			
		}else if($pack1->factory=='3')
		{
			
			$timest=$this->db->select('updatedOn')->from('boughtoutfms')->where('planid',$pack1->planid)->get();
				if($timest->num_rows()>0)
				{
				foreach($timest->result() as $timest1);

				$timesp=date('d-M-Y g:i A',strtotime($timest1->updatedOn));

				}else{ $timesp="";   }
			
		}
		
		/** PLANNED DATE **/
		$tattime=date('g:i A',strtotime($timesp));
		$times=date('Y-m-d',strtotime($timesp));
		$TATDATE=date('d-M-Y',strtotime($times."+ 1days")).' '.$tattime;
		
		/** END **/
		

$html='<a href="javascript:;" onclick="markstagedone('."'".$pack1->id."'".','."'".$pack1->job_card_no."'".')"><span class="btn btn-warning">Mark Done</span></a>';
			$scheduler_data[] = array(
			'sr_no'=>$t,
			'timestamp'=>$timesp,
			'jobcardno'=>$pack1->job_card_no,
			'plannedon'=>$TATDATE,
			'machinename'=>$pack1->instruments_name,
			'markdone'=>$html
			);
			
	$t++;
	}
		
		$results = array(
		"sEcho" => 1,
		"iTotalRecords" => count($scheduler_data),
		"iTotalDisplayRecords" => count($scheduler_data),
		"aaData"=>$scheduler_data);
		echo json_encode($results);
}else
{
	$results = array(
		"sEcho" => 1,
		"iTotalRecords" => count($scheduler_data),
		"iTotalDisplayRecords" => count($scheduler_data),
		"aaData"=>$scheduler_data);
		echo json_encode($results);
	
}
		
		
	}
	
	function markpacked()
	{
		$jobcard=$this->uri->segment(3);
		$data=array('jobcardid'=>$jobcard,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
		$this->db->insert('order_packing_details',$data);
		
		$edata=array('packed'=>'1');
		$this->db->where('id',$jobcard);
		$this->db->update('order_instruments',$edata);
		
		$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Packed.</span></div>');
		redirect(page_url.'Reporting/readyforpacking');
		
		
	}
	
	function completedorders()
	{
		
		
		$this->load->view('FMS/crmcompletedorders');
	
		
		
	}
	
	
		function completedordersservice()
	{
		
		
		$this->load->view('FMS/crmcompletedordersforservice');
		
		
		
	}
	
	
	public function packed_order_list()
	{
	    $zoneid=$this->uri->segment(4);
		
		if($zoneid<>'')
		{
			$user=$this->getallzoneusers($zoneid);
			$users= "'" . implode ( "', '", $user ) . "'";
		}
		
		$scheduler_data = array();
		$this->db->select('a.pono,a.sono,a.order_id, a.order_type, a.marketing_person,a.po_number, a.company_name, a.address, a.email, a.mobile_number, a.internal_order_no, a.discount, a.order_value_after_discount, a.advance_amount, a.payment_terms, a.installation_charges, a.order_status, a.added_on,a.packing_type, a.installation_type, b.user_id, b.title, b.first_name, b.last_name, a.installation_amount, a.packing_charges, a.packing_amount, a.freight_type, a.freight_amount,a.remarks')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left')->where('a.order_status','1');
	if($this->uri->segment(3)<>'' && $this->uri->segment(3)<>'NA'){
		    $this->db->where('a.marketing_person',$this->uri->segment(3));
		}
		if($zoneid<>'')
		{
		
			 $this->db->where_in('a.marketing_person',$users,false);
		}
		$query = $this->db->where('a.closeorder','0')->where('a.movetodispatch','0')->order_by('a.order_id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			$completedjobcard=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1')->get();
			$completedjobcards=$completedjobcard->num_rows();
			
			$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name, a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			$alljobcard=$query1->num_rows();
			
		if($completedjobcards==$alljobcard)
		{
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			
			$html = "<table border='1' style='width:500px;'><tr style='background-color:white;'><th style='padding:2px 2px 2px 2px'>INSTRUMENT</th><th style='padding:2px 2px 2px 2px'>JOB CARD NO.</th></tr>";
			$instrumentsss = array();
			$isinstrumentavailable=0;
			
			
			foreach($query1->result() as $instruments){
				if($instruments->complete=='1'){
					$backgroundcolor = "background-color:#10C469; color:white !important; font-weight:bold;";
				}else{
					$backgroundcolor="";
				}
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->instruments_name)."</td>";
				//$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->qty)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->job_card_no)."</td>";
				$html.="</tr>";
				$isinstrumentavailable=1;
			}
			$html.="</table>";
			$ins_charges = "";
			/** Get order planned **/
			$restt=$this->db->select('count(id) as totaljobcard')->from('order_instruments')->where('order_id',$row->order_id)->get();
			foreach($restt->result() as $instcount);
			$totjobcard= $instcount->totaljobcard;
			
			
	
			
			if($row->installation_charges=='1'){
				$charges= "<strong>INSTALLATION TYPE </strong>".$row->installation_type."<br> <strong>INSTALLATION CHARGES</strong> - REQUIRED";
				
				$ins_charges= $charges."<br> AMOUNT - ".$row->installation_amount;
			}else{
					$ins_charges = "NOT REQUIRED";
			}
			
			if($row->packing_charges=='1'){
				$packcharges = "PAID BY PARTY";
				$packingcharges= $packcharges."<br> Amount - <strong>".$row->packing_amount."</strong>";
			}else{
					$packingcharges = "INCLUSIVE";
			}
			
			if($row->freight_type=='1'){
				$freigntcharges = "TO PAY BASIS";
				
			}else if($row->freight_type=='2'){
				$freigntcharges = "PAID BY PRESTO";
			}else if($row->freight_type=='3'){
				$frtcharges = "BILLED IN INVOICE";
				$freigntcharges= $frtcharges."<br> Amount - <strong>".$row->freight_amount."</strong>";
			}else{
				$freigntcharges = "OWN PICK-UP";
					
			}
			
			$completeorder='<a href="javascript:;" onclick="markstagedone('."'".$row->order_id."'".')"><span class="btn btn-warning">Move to Dispatch</span></a>';
			
			if($isinstrumentavailable==1)
			{
			
			$po='';
			if(file_exists(sfpo.$row->internal_order_no.'.pdf'))
			{
			$po="<a href='".page_url."sfpo/".$row->internal_order_no.".pdf' target='_blank'>".$row->internal_order_no."</a>";
			}else
			{
			if(file_exists(sfpo.$row->pono.'.PDF'))
			{
			$po="<a href='".page_url."sfpo/".$row->internal_order_no.".PDF' target='_blank'>".$row->internal_order_no."</a>";
			}else
			{
			
			
			}
			}
			
			$scheduler_data[] = array('sr_no'=>$i,
			'closeorder'=>$completeorder,
			'added_on'=>$addeddate."".$addedtime,
			'order_type'=>strtoupper($row->order_type),
			'marketing_person'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'po_number'=>strtoupper($row->po_number),
			'company_name'=>strtoupper($row->company_name),
			'address'=>strtoupper($row->address),
			'email'=>strtoupper($row->email),
			'mobile_number'=>strtoupper($row->mobile_number),
			'internal_order_no'=>strtoupper($row->internal_order_no),
			'itemname'=>$html,
			'discount'=>strtoupper($row->discount)."%",
			'order_value_after_discount'=>strtoupper($row->order_value_after_discount),
			'advance_amount'=>strtoupper($row->advance_amount),
			'payment_terms'=>strtoupper($row->payment_terms),
			'installation_charges'=>strtoupper($ins_charges),
			'packingcharges'=>"<strong>Packing Type</strong> -".$row->packing_type."<br>".$packingcharges,
			'freigntcharges'=>$freigntcharges,
			'remarks'=>$row->remarks,
			'pono'=>$po,
			'sono'=>$row->sono);
			}
			$i++;
		}
	}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}


	public function packed_order_listforservice()
	{
	    $zoneid=$this->uri->segment(4);
		
		if($zoneid<>'')
		{
			$user=$this->getallzoneusers($zoneid);
			$users= "'" . implode ( "', '", $user ) . "'";
		}
		
		$scheduler_data = array();
		$this->db->select('a.order_id, a.order_type, a.marketing_person,a.po_number, a.company_name, a.address, a.email, a.mobile_number, a.internal_order_no, a.discount, a.order_value_after_discount, a.advance_amount, a.payment_terms, a.installation_charges, a.order_status, a.added_on,a.packing_type, a.installation_type, b.user_id, b.title, b.first_name, b.last_name, a.installation_amount, a.packing_charges, a.packing_amount, a.freight_type, a.freight_amount,a.remarks')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left')->where('a.order_status','1')->where('a.order_type','SERVICE');
	if($this->uri->segment(3)<>'' && $this->uri->segment(3)<>'NA'){
		    $this->db->where('a.marketing_person',$this->uri->segment(3));
		}
		if($zoneid<>'')
		{
		
			 $this->db->where_in('a.marketing_person',$users,false);
		}
		$query = $this->db->where('a.closeorder','0')->where('a.movetodispatch','0')->order_by('a.order_id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			$completedjobcard=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1')->get();
			$completedjobcards=$completedjobcard->num_rows();
			
			$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name, a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			$alljobcard=$query1->num_rows();
			
		if($completedjobcards==$alljobcard)
		{
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			
			$html = "<table border='1' style='width:500px;'><tr style='background-color:white;'><th style='padding:2px 2px 2px 2px'>INSTRUMENT</th><th style='padding:2px 2px 2px 2px'>JOB CARD NO.</th></tr>";
			$instrumentsss = array();
			
			
			
			foreach($query1->result() as $instruments){
				if($instruments->complete=='1'){
					$backgroundcolor = "background-color:#10C469; color:white !important; font-weight:bold;";
				}else{
					$backgroundcolor="";
				}
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->instruments_name)."</td>";
				//$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->qty)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->job_card_no)."</td>";
				$html.="</tr>";
			}
			$html.="</table>";
			$ins_charges = "";
			/** Get order planned **/
			$restt=$this->db->select('count(id) as totaljobcard')->from('order_instruments')->where('order_id',$row->order_id)->get();
			foreach($restt->result() as $instcount);
			$totjobcard= $instcount->totaljobcard;
			
			
	
			
			if($row->installation_charges=='1'){
				$charges= "<strong>INSTALLATION TYPE </strong>".$row->installation_type."<br> <strong>INSTALLATION CHARGES</strong> - REQUIRED";
				
				$ins_charges= $charges."<br> AMOUNT - ".$row->installation_amount;
			}else{
					$ins_charges = "NOT REQUIRED";
			}
			
			if($row->packing_charges=='1'){
				$packcharges = "PAID BY PARTY";
				$packingcharges= $packcharges."<br> Amount - <strong>".$row->packing_amount."</strong>";
			}else{
					$packingcharges = "INCLUSIVE";
			}
			
			if($row->freight_type=='1'){
				$freigntcharges = "TO PAY BASIS";
				
			}else if($row->freight_type=='2'){
				$freigntcharges = "PAID BY PRESTO";
			}else if($row->freight_type=='3'){
				$frtcharges = "BILLED IN INVOICE";
				$freigntcharges= $frtcharges."<br> Amount - <strong>".$row->freight_amount."</strong>";
			}else{
				$freigntcharges = "OWN PICK-UP";
					
			}
			
			$completeorder='<a href="javascript:;" onclick="markstagedone('."'".$row->order_id."'".')"><span class="btn btn-warning">Move to Dispatch</span></a>';
			
			$scheduler_data[] = array('sr_no'=>$i,
			'closeorder'=>$completeorder,
			'added_on'=>$addeddate."".$addedtime,
			'order_type'=>strtoupper($row->order_type),
			'marketing_person'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'po_number'=>strtoupper($row->po_number),
			'company_name'=>strtoupper($row->company_name),
			'address'=>strtoupper($row->address),
			'email'=>strtoupper($row->email),
			'mobile_number'=>strtoupper($row->mobile_number),
			'internal_order_no'=>strtoupper($row->internal_order_no),
			'itemname'=>$html,
			'discount'=>strtoupper($row->discount)."%",
			'order_value_after_discount'=>strtoupper($row->order_value_after_discount),
			'advance_amount'=>strtoupper($row->advance_amount),
			'payment_terms'=>strtoupper($row->payment_terms),
			'installation_charges'=>strtoupper($ins_charges),
			'packingcharges'=>"<strong>Packing Type</strong> -".$row->packing_type."<br>".$packingcharges,
			'freigntcharges'=>$freigntcharges,
			'remarks'=>$row->remarks);
			$i++;
		}
	}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}


function closeorder()
{
	$orderid=$this->uri->segment(3);
	
	$data=array('closeorder'=>'1','closedOn'=>date('Y-m-d H:i:s'));
	$this->db->where('order_id',$orderid);
	$this->db->update('prestogroup_orders',$data);
	
	$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Closed.</span></div>');
		redirect(page_url.'Reporting/completedorders');
	
	
}


function previousorders()
	{
		
			$this->load->view('FMS/previousordershistory');
		
		
		
	}
	
public function previous_order_list()
	{
		$scheduler_data = array();
		$query = $this->db->select('a.order_id,a.closedOn, a.order_type, a.marketing_person,a.po_number, a.company_name, a.address, a.email, a.mobile_number, a.internal_order_no, a.discount, a.order_value_after_discount, a.advance_amount, a.payment_terms, a.installation_charges, a.order_status, a.added_on,a.packing_type, a.installation_type, b.user_id, b.title, b.first_name, b.last_name, a.installation_amount, a.packing_charges, a.packing_amount, a.freight_type, a.freight_amount,a.remarks')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left')->where('a.order_status','1')->where('a.closeorder','1')->order_by('a.order_id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			$completedjobcard=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->get();
			$completedjobcards=$completedjobcard->num_rows();
			
			$query1 = $this->db->select('a.id as jbcid,a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name, a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			$alljobcard=$query1->num_rows();
			
		if($completedjobcards==$alljobcard)
		{
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			
			$html = "<table border='1' style='width:500px;'><tr style='background-color:white;'><th style='padding:2px 2px 2px 2px'>INSTRUMENT</th><th style='padding:2px 2px 2px 2px'>JOB CARD NO.</th>><th style='padding:2px 2px 2px 2px'>FILE NO.</th></tr>";
			$instrumentsss = array();
			
			
			
			foreach($query1->result() as $instruments){
				if($instruments->complete=='1'){
					$backgroundcolor = "background-color:green; color:white !important; font-weight:bold;";
				}else{
					$backgroundcolor="";
				}
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->instruments_name)."</td>";
				//$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->qty)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->job_card_no)."</td>";
				
				$fileno=$this->getfilenofromplanning($instruments->jbcid);
					$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($fileno)."</td>";
				$html.="</tr>";
			}
			$html.="</table>";
			$ins_charges = "";
			/** Get order planned **/
			$restt=$this->db->select('count(id) as totaljobcard')->from('order_instruments')->where('order_id',$row->order_id)->get();
			foreach($restt->result() as $instcount);
			$totjobcard= $instcount->totaljobcard;
			
			
	
			
			if($row->installation_charges=='1'){
				$charges= "<strong>INSTALLATION TYPE </strong>".$row->installation_type."<br> <strong>INSTALLATION CHARGES</strong> - REQUIRED";
				
				$ins_charges= $charges."<br> AMOUNT - ".$row->installation_amount;
			}else{
					$ins_charges = "NOT REQUIRED";
			}
			
			if($row->packing_charges=='1'){
				$packcharges = "PAID BY PARTY";
				$packingcharges= $packcharges."<br> Amount - <strong>".$row->packing_amount."</strong>";
			}else{
					$packingcharges = "INCLUSIVE";
			}
			
			if($row->freight_type=='1'){
				$freigntcharges = "TO PAY BASIS";
				
			}else if($row->freight_type=='2'){
				$freigntcharges = "PAID BY PRESTO";
			}else if($row->freight_type=='3'){
				$frtcharges = "BILLED IN INVOICE";
				$freigntcharges= $frtcharges."<br> Amount - <strong>".$row->freight_amount."</strong>";
			}else{
				$freigntcharges = "OWN PICK-UP";
					
			}
			
			$completeorder=date('d-M-Y g:i A',strtotime($row->closedOn));
			
			$scheduler_data[] = array('sr_no'=>$i,
			'closeorder'=>$completeorder,
			'added_on'=>$addeddate."".$addedtime,
			'order_type'=>strtoupper($row->order_type),
			'marketing_person'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'po_number'=>strtoupper($row->po_number),
			'company_name'=>strtoupper($row->company_name),
			'address'=>strtoupper($row->address),
			'email'=>strtoupper($row->email),
			'mobile_number'=>strtoupper($row->mobile_number),
			'internal_order_no'=>strtoupper($row->internal_order_no),
			'itemname'=>$html,
			'discount'=>strtoupper($row->discount)."%",
			'order_value_after_discount'=>strtoupper($row->order_value_after_discount),
			'advance_amount'=>strtoupper($row->advance_amount),
			'payment_terms'=>strtoupper($row->payment_terms),
			'installation_charges'=>strtoupper($ins_charges),
			'packingcharges'=>"<strong>Packing Type</strong> -".$row->packing_type."<br>".$packingcharges,
			'freigntcharges'=>$freigntcharges,
			'remarks'=>$row->remarks);
			$i++;
		}
	}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}
	
	
function dispatchfortommorow()
	{

$this->load->view('FMS/dispatchforrommorow');

 }
	
	
public function dispatchfortommorow_order_list()
	{
	$sendtobill='';
		$scheduler_data = array();
		$urldata = $this->uri->segment(3);
		$userid = $this->uri->segment(4);
		$this->db->select('a.tempono,a.docketnumber,a.docketno,a.movedOn,a.order_id, a.order_type, a.marketing_person,a.po_number, a.company_name,a.closedOn, a.address, a.email, a.mobile_number, a.internal_order_no, a.discount, a.order_value_after_discount, a.advance_amount, a.payment_terms, a.installation_charges, a.order_status, a.added_on,a.packing_type, a.installation_type, b.user_id, b.title, b.first_name, b.last_name, a.installation_amount, a.packing_charges, a.packing_amount, a.freight_type, a.freight_amount,a.remarks,ttype,tname,totalpacket,customername,customercontact')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left');
		if($userid<>''){
		    $this->db->where('a.marketing_person',$userid);
		}
		$query = $this->db->where('a.order_status','1')->where('a.movetodispatch','1')->order_by('a.order_id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
		$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1');
			if($urldata<>'')
			{
			    if($urldata=='0')
			    {
			       $this->db->where('finalpacked','0'); 
			    }else
			    {
			         $this->db->where('finalpacked','1');
			    }
			    
			}
			$completedjobcard=$this->db->get();
			$completedjobcards=$completedjobcard->num_rows();
			
			$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name,a.finalpacked,a.job_card_no,a.mserialno,a.extraserialno')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			$alljobcard=$query1->num_rows();
			if($alljobcard>0)
			{
				foreach($query1->result() as $alluee);
					$finalpack=$alluee->finalpacked;
					
			}else{ $finalpack=0; }
			
		if($alljobcard<>0)
		{
		if($completedjobcards==$alljobcard)
		{
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			
			$html = "<table border='1' style='width:500px;'><tr style='background-color:white;'><th style='padding:2px 2px 2px 2px;width:300px;'>INSTRUMENT</th><th style='padding:2px 2px 2px 2px;width:100px;'>JOB CARD NO.</th><th style='padding:2px 2px 2px 2px;width:100px;'>SERIAL NO.</th></tr>";
			$instrumentsss = array();
			
			
			
			foreach($query1->result() as $instruments){
				if($instruments->complete=='1'){
					$backgroundcolor = "background-color:#10C469; color:white !important; font-weight:bold;";
				}else{
					$backgroundcolor="";
				}
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->instruments_name)."</td>";
				//$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->qty)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->job_card_no)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->mserialno)."</td>";
				$html.="</tr>";
			}
			$html.="</table>";
			$ins_charges = "";
			/** Get order planned **/
			$restt=$this->db->select('count(id) as totaljobcard')->from('order_instruments')->where('order_id',$row->order_id)->get();
			foreach($restt->result() as $instcount);
			$totjobcard= $instcount->totaljobcard;
			
			
	
			
			if($row->installation_charges=='1'){
				$charges= "<strong>INSTALLATION TYPE </strong>".$row->installation_type."<br> <strong>INSTALLATION CHARGES</strong> - REQUIRED";
				
				$ins_charges= $charges."<br> AMOUNT - ".$row->installation_amount;
			}else{
					$ins_charges = "NOT REQUIRED";
			}
			
			if($row->packing_charges=='1'){
				$packcharges = "PAID BY PARTY";
				$packingcharges= $packcharges."<br> Amount - <strong>".$row->packing_amount."</strong>";
			}else{
					$packingcharges = "INCLUSIVE";
			}
			
			if($row->freight_type=='1'){
				$freigntcharges = "TO PAY BASIS";
				
			}else if($row->freight_type=='2'){
				$freigntcharges = "PAID BY PRESTO";
			}else if($row->freight_type=='3'){
				$frtcharges = "BILLED IN INVOICE";
				$freigntcharges= $frtcharges."<br> Amount - <strong>".$row->freight_amount."</strong>";
			}else{
				$freigntcharges = "OWN PICK-UP";
					
			}
			
			$closedon=date('d-M-Y g:i A',strtotime($row->movedOn));
			$plannedtime = date('d-M-Y g:i A', strtotime($closedon . ' +1 day'));

if($row->installation_charges==1)
			{
				$ser=1;
			}else{
				
				$ser=0;
			}
			
			
$printpackinglabel='<a href="'.page_url.'Reporting/generatepackingslip/'.$row->order_id.'" target="_blank"><span class="btn btn-success btn-sm">Print Packing Label</span></a>';
			
			if($finalpack=='1')
{
			$restyupacku=$this->db->select('addedOn')->from('order_finalpacking_details')->where('orderid',$row->order_id)->get();	
				if($restyupacku->num_rows()>0)
{
				foreach($restyupacku->result() as $restyupacku2);
				$actualtime=date('d-M-Y g:i A',strtotime($restyupacku2->addedOn));
				
}else{  

	$actualtime="";
					}
		$completeorder='Task has been done';		
	
}else{  $actualtime=""; 
	 
	 
	 if($row->totalpacket=='' || $row->totalpacket=='0' )
	{	

	$sendtobill='<a href="javascript:;" onclick="updatedetailsforaccount('."'".$row->order_id."'".','."'".$row->internal_order_no."'".','."'".$ser."'".')"><span class="btn btn-warning btn-sm">Send to Accounts</span></a>';
	$completeorder='';
	}else
	{
		  $completeorder='<a href="javascript:;" onclick="markstagedone('."'".$row->order_id."'".','."'".$row->internal_order_no."'".','."'".$ser."'".')"><span class="btn btn-warning btn-sm">Mark as Done</span></a>';


		$html1="Packets- ".$row->totalpacket.'<br/>';
		$html1.="Transport Type- ".$row->ttype.'<br/>';
		if($row->ttype=='By Hand')
		{
		$html1.="Customer Name- ".$row->customername.'<br/>';
		$html1.="Customer Contact- ".$row->customercontact.'<br/>';
		}else if($row->ttype=='By Tempo')
		{
		
		}else
		{
		$html1.="Transporter Name- ".$row->tname.'<br/>';
		}

			$html1.="Tempo No.- ".$row->tempono.'<br/>';
			
		 $sendtobill=$html1;
		
		//$sendtobill='';
	}

	  	
	 // $completeorder='<a href="javascript:;" onclick="markstagedone('."'".$row->order_id."'".','."'".$row->internal_order_no."'".','."'".$ser."'".')"><span class="btn btn-warning btn-sm">Mark as Done</span></a>';

}
if($row->docketno<>'')
{
    $dock="<a href='".page_url."image_bank/docketno/".$row->docketno."' download>Docket Image</a>";
}else
{ 
     $dock='';
}
	

	//$completeorder='<a href="javascript:;" onclick="markstagedone('."'".$row->order_id."'".','."'".$row->internal_order_no."'".','."'".$ser."'".')"><span class="btn btn-warning btn-sm">Mark as Done</span></a>';

	//$sendtobill='';


			$scheduler_data[] = array('sr_no'=>$i,
			'closeorder'=>$completeorder,
			'docket'=>$dock,
			'packinglabel'=>$printpackinglabel,
			'added_on'=>$closedon,
			 'plannedtime'=>$plannedtime,
			 'actualtime'=>$actualtime,
			'order_type'=>strtoupper($row->order_type),
			'marketing_person'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'po_number'=>strtoupper($row->po_number),
			'company_name'=>strtoupper($row->company_name),
			'address'=>strtoupper($row->address),
			'email'=>strtoupper($row->email),
			'mobile_number'=>strtoupper($row->mobile_number),
			'internal_order_no'=>strtoupper($row->internal_order_no),
			'itemname'=>$html,
			'discount'=>strtoupper($row->discount)."%",
			'order_value_after_discount'=>strtoupper($row->order_value_after_discount),
			'advance_amount'=>strtoupper($row->advance_amount),
			'payment_terms'=>strtoupper($row->payment_terms),
			'installation_charges'=>strtoupper($ins_charges),
			'packingcharges'=>"<strong>Packing Type</strong> -".$row->packing_type."<br>".$packingcharges,
			'freigntcharges'=>$freigntcharges,
			'sendforbilling'=>$sendtobill,
			'remarks'=>$row->remarks);
			$i++;
		}
		}
	}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}
	
function markfinalpacked()
{

$orderid=$this->uri->segment(3);
$install=$this->uri->segment(4);

$testronixorder=$this->fmsmodel->checkfortestronixorder($orderid);
            $files=$_FILES['docketno']['name'];
            if($files<>'')
            {
            $ex=explode('.',$files);
            $ext=end($ex);
            $newname=time().'_'.rand(10000,99999).'.'.$ext;
            
            move_uploaded_file($_FILES['docketno']['tmp_name'],UPLOADPATH.'docketno/'.$newname);
            
            }else
            {
            $newname='';
            }


            $files1=$_FILES['bcopy']['name'];
            if($files1<>'')
            {
            $ex1=explode('.',$files1);
            $ext1=end($ex1);
            $newname1=time().'_'.rand(10000,99999).rand(1,9).'.'.$ext1;
            
            move_uploaded_file($_FILES['bcopy']['tmp_name'],UPLOADPATH.'docketno/'.$newname1);
            
            }else
            {
            $newname1='';
            }
			
			
			$documentpath=sfdocument.'docketno/';

			
			$billno=$this->input->post('billno');
			$billdate=$this->input->post('billdate');
			$packet=$this->input->post('packet');
			$shipment=$this->input->post('shipment');
			$docketnumber=$this->input->post('docketnumber');
			$dodamount=$this->input->post('dodamount');
			$freightamount=$this->input->post('freightamount');
	

		$data=array('orderid'=>$orderid,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
		$this->db->insert('order_finalpacking_details',$data);
		
		$edata=array('finalpacked'=>'1');
		$this->db->where('order_id',$orderid);
		$this->db->update('order_instruments',$edata);
		 
		if($install==0)
		{
			$cldata=array('closeorder'=>'1','closedOn'=>date('Y-m-d H:i:s'),'closedby'=>$_SESSION['logged_in']['user_id'],'docketno'=>$newname,'docketnumber'=>$docketnumber,'billno'=>$billno,'billdate'=>$billdate,'totalpacket'=>$packet,'shipmentmode'=>$shipment,'dodamount'=>$dodamount,'frieghtamount'=>$freightamount,'billcopy'=>$newname1);
			$this->db->where('order_id',$orderid);
			$this->db->update('prestogroup_orders',$cldata);
			
		}else
		{
		    	$cldata=array('docketno'=>$newname,'docketnumber'=>$docketnumber,'billno'=>$billno,'billdate'=>$billdate,'totalpacket'=>$packet,'shipmentmode'=>$shipment,'dodamount'=>$dodamount,'frieghtamount'=>$freightamount,'billcopy'=>$newname1);
			$this->db->where('order_id',$orderid);
			$this->db->update('prestogroup_orders',$cldata);
		}
		

			/*** CHECK IF ORDER IS SF THEN SEND DISPATCH DETAILS VIA API **/

			$rest=$this->db->select('sforder,oppid')->from('prestogroup_orders')->where('order_id',$orderid)->where('sforder>','0')->get();
			if($rest->num_rows()>0)
			{

			$this->senddispatchdatatosalesforce($orderid,$docketnumber,$billno,$billdate,$packet,$shipment,$dodamount,$freightamount,$documentpath,$newname,$newname1);
			//$this->senddispatchintimationtoclient($orderid);
			
			}


			if($testronixorder==0)
			{
			$this->senddispatchintimationtoclient($orderid);
			}else
			{
				$this->senddispatchintimationtoclientfortestronix($orderid);
			}


			/** END **/
		

		
		
		/** GET ALL JOBCARD CHECK IF IMPORTED THEN MINUS STOCK AND UPDATE THE BLOCKED REPORT **/
		$getalljb=$this->checkifanyjobcardidimported($orderid);
		 
		//echo count($getalljb);exit;
		if(count($getalljb)>0)
		{
		   for($i=0;$i<count($getalljb);$i++)
		   {
		      
		      
				if($getalljb[$i]['stock']>0)
				{
				    $curr=$getalljb[$i]['stock']-1;
				}else{
				    $curr=0;
				}
				$newstock=array('stock'=>$curr);
				$this->db->where('id',$getalljb[$i]['itemid']);
				$this->db->update('presto_instruments',$newstock); 
				
				
				/** MARK BLOCKED AS DISPATCHED **/
				$wdsds=array('dispatched'=>'1');
				$this->db->where('item_id',$getalljb[$i]['itemid']);
				$this->db->where('jobcard',$getalljb[$i]['jobcardid']);
				$this->db->update('imported_item_blocked',$wdsds);
				
		   }
				/** END **/
			
			
		}
		
		/** END **/


	
		$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Dispatched.</span></div>');
		redirect(page_url.'Reporting/dispatchfortommorow');

	
}

	
	function generatepackingslip()
{
	
	
	$this->load->view('docformat/packingslip');
	
	
}
	

public function service_request_list()
	{
		$scheduler_data = array();
        $query = $this->db->select('a.order_id, a.order_type, a.marketing_person,a.po_number, a.company_name,a.closedOn, a.address, a.email, a.mobile_number, a.internal_order_no, a.discount, a.order_value_after_discount, a.advance_amount, a.payment_terms, a.installation_charges, a.order_status, a.added_on,a.packing_type, a.installation_type, b.user_id, b.title, b.first_name, b.last_name, a.installation_amount, a.packing_charges, a.packing_amount, a.freight_type, a.freight_amount,a.remarks')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left')->where('a.order_status','1')->where('a.closeorder','0')->where('a.installation_charges','1')->order_by('a.order_id','desc')->get();
        $res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$completedjobcard=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1')->get();
			$completedjobcards=$completedjobcard->num_rows();
			
			$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name,a.finalpacked,a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			$alljobcard=$query1->num_rows();
			if($alljobcard>0)
			{
				foreach($query1->result() as $alluee);
					$finalpack=$alluee->finalpacked;
					
			}else{ $finalpack=0; }
			
		if($completedjobcards==$alljobcard)
		{
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			
			$html = "<table border='1' style='width:500px;'><tr style='background-color:white;'><th style='padding:2px 2px 2px 2px'>INSTRUMENT</th><th style='padding:2px 2px 2px 2px'>JOB CARD NO.</th></tr>";
			$instrumentsss = array();
			
			
			$finalpacked=array();
			foreach($query1->result() as $instruments){
				if($instruments->complete=='1'){
					$backgroundcolor = "background-color:green; color:white !important; font-weight:bold;";
				}else{
					
					$backgroundcolor="";
				}
				
				if($instruments->finalpacked=='1')
					{
				$finalpacked[]=1;
					}else
					{
				$finalpacked[]=0;
					}
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->instruments_name)."</td>";
				//$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->qty)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->job_card_no)."</td>";
				$html.="</tr>";
				
			}
			$html.="</table>";
			$ins_charges = "";
			/** Get order planned **/
			$restt=$this->db->select('count(id) as totaljobcard')->from('order_instruments')->where('order_id',$row->order_id)->get();
			foreach($restt->result() as $instcount);
			$totjobcard= $instcount->totaljobcard;
			
			
	
			
			if($row->installation_charges=='1'){
				$charges= "<strong>INSTALLATION TYPE </strong>".$row->installation_type."<br> <strong>INSTALLATION CHARGES</strong> - REQUIRED";
				
				$ins_charges= $charges."<br> AMOUNT - ".$row->installation_amount;
			}else{
					$ins_charges = "NOT REQUIRED";
			}
			
			if($row->packing_charges=='1'){
				$packcharges = "PAID BY PARTY";
				$packingcharges= $packcharges."<br> Amount - <strong>".$row->packing_amount."</strong>";
			}else{
					$packingcharges = "INCLUSIVE";
			}
			
			if($row->freight_type=='1'){
				$freigntcharges = "TO PAY BASIS";
				
			}else if($row->freight_type=='2'){
				$freigntcharges = "PAID BY PRESTO";
			}else if($row->freight_type=='3'){
				$frtcharges = "BILLED IN INVOICE";
				$freigntcharges= $frtcharges."<br> Amount - <strong>".$row->freight_amount."</strong>";
			}else{
				$freigntcharges = "OWN PICK-UP";
					
			}
			
			

			
$restynxjs=$this->db->select('addedOn')->from('service_details')->where('order_id',$row->order_id)->get();
			if($restynxjs->num_rows()>0)
{
$printpackinglabel="Task has been done";
				foreach($restynxjs->result() as $restynxjs11);
$actualtime=date('d-M-Y g:i A',strtotime($restynxjs11->addedOn));
							
$completeorder='Task has been done';
}else
{

		
				$actualtime='';
				
			
$completeorder=' <button class="btn btn-warning btn-xs waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$i.'">Update</button>';
$completeorder.= '<div id="con-close-modal'.$i.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
           <form id="loginForm" method="post" action="'.page_url.'Reporting/markservicedone/'.$row->order_id.'">
  
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">UPDATE STATUS</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                               <div class="col-md-3">
													<div class="form-group">
														<label for="field-1" class="control-label">Status</label><br>
														<select class="form-control" name="status" id="status'.$i.'" onchange="onchangestatus('.$i.');">
															<option value="1">TASK COMPLETE</option>
															<option value="0">WORK IN PROGRESS</option>
															<option value="2">CUSTOMER END PENDING</option>
																<option value="5">ENGINEER VISIT REQUIRED</option>
														</select>
													</div> 
												</div>
												<script>
												function onchangestatus(i){
												if($("#status"+i).val() == "2" || $("#status"+i).val()=="0") {
												
												$("#remarksbox"+i).show(); 
												$("#followupbox"+i).show();
												$("#remarks"+i).attr("required",true);
												$("#next_followup_date"+i).attr("required",true);
												}else if($("#status"+i).val() == "5") { 
                                            $("#remarksbox"+i).show(); 
                                            $("#remarks"+i).attr("required",true);
                                            	$("#followupbox"+i).hide();
                                            	$("#next_followup_date"+i).attr("required",false);
                                            	
                                            } else {
												$("#remarksbox"+i).hide(); 
												$("#followupbox"+i).hide(); 
												$("#remarks"+i).attr("required",false);
												$("#next_followup_date"+i).attr("required",false);
												} 
												}
												
											</script>
												 <div class="col-md-6" style="display:none" id="remarksbox'.$i.'">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARKS</label><br>
														<span id="error_color_name" style="color:red;"></span>
                                                       <textarea class="form-control" name="remarks" id="remarks" style="width:400px"></textarea>
                                                    </div>
                                                </div>
												
												
												<div class="col-md-3" style="display:none" id="followupbox'.$i.'">
													<div class="form-group">
														<label>Follow-up Date</label>
														<input class="form-control" type="date" name="next_followup_date" id="next_followup_date" value="" min="'.date("Y-m-d").'">
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
}
			

			
			if(!in_array("0", $finalpacked)) 
				{
		$restyupacku=$this->db->select('addedOn')->from('order_finalpacking_details')->where('orderid',$row->order_id)->get();	
				if($restyupacku->num_rows()>0)
{
				foreach($restyupacku->result() as $restyupacku2)
				$plannedtime = date('d-M-Y g:i A', strtotime($restyupacku2->addedOn . ' +7 day'));
					$closedon=date('d-M-Y g:i A',strtotime($restyupacku2->addedOn));
					
}else
{
			$plannedtime="";	
					$closedon='';
}


$dura='';
$q= $this->db->select('next_followup,remarks,status')->from('service_request_followup')->where('record_id',$row->order_id)->limit(1)->order_by('id','desc')->get();

if($q->num_rows()>0){
    foreach($q->result() as $follow);
    $nxtfollowup = date('d-m-Y',strtotime($follow->next_followup))."<br><br>".$follow->remarks;
   
   if($follow->status=='1')
   {
       $currsta="Complete";
       
   }else  if($follow->status=='0')
   {
       $currsta="Work In Progress";
       
   }else if($follow->status=='2')
   {
       $currsta="Customer End Pending";
       
   }else if($follow->status=='5')
   {
       $currsta="Engineer Visit Required";
       
   }
}else{

$generatedtime=date('Y-m-d H:i:s',strtotime($closedon));
$onedayold=date('Y-m-d H:i:s', strtotime("+1 day", strtotime($generatedtime)));
$ti1 = strtotime(date('Y-m-d H:i:s'));
$ti2 = strtotime($onedayold);
$hour = abs($ti2 - $ti1)/(60*60);

if($hour<='24')
{
    $dura="NEW";
}else
{
     $dura="";
}
    $nxtfollowup = "";
    $currsta="Incomplete";
}
	
	//echo $nxtfollowup; exit;			
			$scheduler_data[] = array('sr_no'=>$i,
			'closeorder'=>$completeorder,
			'currstatus'=>$currsta,
			'type'=>$dura,
			'nextfollowup'=>$nxtfollowup,			
			'added_on'=>$closedon,
			 'plannedtime'=>$plannedtime,
			 'actualtime'=>$actualtime,
			'order_type'=>strtoupper($row->order_type),
			'marketing_person'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'po_number'=>strtoupper($row->po_number),
			'company_name'=>strtoupper($row->company_name),
			'address'=>strtoupper($row->address),
			'email'=>strtoupper($row->email),
			'mobile_number'=>strtoupper($row->mobile_number),
			'internal_order_no'=>strtoupper($row->internal_order_no),
			'itemname'=>$html,
			'discount'=>strtoupper($row->discount)."%",
			'order_value_after_discount'=>strtoupper($row->order_value_after_discount),
			'advance_amount'=>strtoupper($row->advance_amount),
			'payment_terms'=>strtoupper($row->payment_terms),
			'installation_charges'=>strtoupper($ins_charges),
			'packingcharges'=>"<strong>Packing Type</strong> -".$row->packing_type."<br>".$packingcharges,
			'freigntcharges'=>$freigntcharges,
			'remarks'=>$row->remarks);
			$i++;
				}
		}
	}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}
	
	
function servicerequest()
{
$this->load->view('FMS/servicerequest');
}
	
function markservicedone()
    {
	$orderid=$this->uri->segment(3);
	if($this->input->post('next_followup_date')){
	$nextdate = date('Y-m-d',strtotime($this->input->post('next_followup_date')));	
	}else{
		$nextdate="0000-00-00";
	}
	
	date_default_timezone_set("Asia/Kolkata");	
	$data = array('record_id'=>$orderid,
	'remarks'=>$this->input->post('remarks'),
	'status'=>$this->input->post('status'),
	'next_followup'=>$nextdate,
	'added_on'=>date('Y-m-d H:i:s'),
	'added_by'=>$_SESSION['logged_in']['user_id']);
	$this->db->insert('service_request_followup',$data);	
	if($this->input->post('status')=='1' || $this->input->post('status')=='5' ){	
		
	$orderid=$this->uri->segment(3);
    $data=array('order_id'=>$orderid,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
    $this->db->insert('service_details',$data);
    $cldata=array('closeorder'=>'1','closedby'=>$_SESSION['logged_in']['user_id'],'closedOn'=>date('Y-m-d H:i:s'));
    $this->db->where('order_id',$orderid);
    $this->db->update('prestogroup_orders',$cldata);
    }
    $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order status has been updated.</span></div>');
    redirect(page_url.'Reporting/servicerequest');

    }
	

function generateinternalorderslip()
{
	$this->load->view('internalorderformat/invoice');
	
	
}
	

function sendmisonwhatsapp()
{
	$uid=$_POST['userids'];
	$restyu=$this->db->select('contact_number')->from('system_users')->where('user_id',$uid)->get();
	foreach($restyu->result() as $rest);
$contact="91".$rest->contact_number;
	$image = $_POST['image'];
$location = $_SERVER['DOCUMENT_ROOT']."/upload/misscreenshot/";
$image_parts = explode(";base64,", $image);

$image_base64 = base64_decode($image_parts[1]);

$filename = uniqid().'.png';

$file = $location . $filename;

file_put_contents($file, $image_base64);

$overallpath=page_url."upload/misscreenshot/".$filename;


/***WHATSAPP INTEGRATION***/
$data = [
    'phone' => $contact, // Receivers phone
    'body' => $overallpath,
	'filename'=>$filename,
	'caption'=>"Your MIS Score"
	// Message
];

	
$json = json_encode($data); // Encode data to JSON
// URL for request POST /message
$url = 'https://api.chat-api.com/instance88514/sendFile?token=lwpwzff7ubbp6dc6';
// Make a POST request
$options = stream_context_create(['http' => [
        'method'  => 'POST',
        'header'  => 'Content-type: application/json',
        'content' => $json
    ]
]);
// Send a request
$result = file_get_contents($url, false, $options);

echo $result;exit;
/***WHATSAPP INTEGRATION***/
	
	
}


function lotorderlist()
{
	$this->load->view('FMS/lotorderlist');
	 
}

 
public function lot_order_listtt()
	{
		$scheduler_data = array();
		 
			$restyui=$this->db->select('a.item_id,b.instruments_name,b.stock')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->join('prestogroup_orders c','a.order_id=c.order_id')->where('c.closeorder','0')->group_by('a.item_id')->order_by('b.instruments_name')->get();
		if($restyui->num_rows()>0)
		{
			$i=1;
			
			foreach($restyui->result() as $restyui1)
			{
			    $query1=$this->db->select('b.company_name,a.job_card_no,b.internal_order_no,a.order_id,a.id as jobcid')->from('order_instruments a')->join('prestogroup_orders b','a.order_id=b.order_id')->where('a.item_id',$restyui1->item_id)->group_by('a.order_id')->get();
			
			
			if($query1->num_rows()>0)
			{
				$html = "<table border='1' style='width:800px;'><tr style='background-color:white;width:100px;'><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:200px;'>I/O NO.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:200px;'>JOBCARD NO.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:400px;'>PARTY NAME.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:100px;'>Days.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:100px;'>Divert.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:100px;'>Cancel</th></tr>";
			
			
			$comdate=array();
			$arr[]=0;
			foreach($query1->result() as $instruments){
				
				$isdispatched=$this->getalldispatchedjobcard($instruments->order_id);
					if($isdispatched==0)
				{
			
				$arr[]=1;
				$isready=$this->checkifalljobcardsareready($instruments->order_id);
				$isready=$this->checkifsinglejobcardsareready($instruments->jobcid);
				
				if($isready==true)
				{
				    $back="background-color:#10C469";
				    $fcolor="color:#fff;";
				    	$dorder="<a href='".page_url."FMS/divertorder/".$instruments->order_id."/1'><span class='btn btn-xs btn-success'>Divert order</span></a>";
				    $comdate=$this->dayssinceorderisready($instruments->order_id);
				    
				    //echo "<pre>"; print_r($comdate);exit;
                    if(count($comdate)>0)
                    {
                    $recentdate=max($comdate);
                    $todaysdate=date('Y-m-d');
                    $days = (strtotime($todaysdate) - strtotime($recentdate)) / (60 * 60 * 24);
                    }else
                    {
                    $days='';
                    }

				}else
				{
				    $back="";
				    $fcolor="";
				     $days='';
				     	$dorder="<a href='".page_url."FMS/divertorder/".$instruments->order_id."/1'><span class='btn btn-xs btn-success'>Divert order</span></a>";
				}
				
				
					$cancell="<a href='".page_url."FMS/cancelorder/".$instruments->order_id."/".$instruments->jobcid."'><span class='btn btn-xs btn-danger'>Cancel</span></a>";
				
				
				$html.="<tr style='".$back."'>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:left;width:10px;".$fcolor."'>".strtoupper($instruments->internal_order_no)."</td>";
					$html.="<td style='padding:2px 2px 2px 2px; text-align:left;width:10px;".$fcolor."'>".strtoupper($instruments->job_card_no)."</td>";
			
				$html.="<td style='padding:2px 2px 2px 2px; text-align:left;width:100px;".$fcolor."'>".strtoupper($instruments->company_name)."</td>";
			
					$html.="<td style='padding:2px 2px 2px 2px; text-align:left;width:100px;".$fcolor."'>".$days."</td>";
						$html.="<td style='padding:2px 2px 2px 2px; text-align:left;width:100px;'>".$dorder."</td>";
						$html.="<td style='padding:2px 2px 2px 2px; text-align:left;width:100px;'>".$cancell."</td>";
				$html.="</tr>";
			}
			
			}
			
		   
			
			$html.="</table>";
			}else{
				
				$html="";
			}
			
		
	
			if(array_sum($arr)>0)
			{
			$scheduler_data[] = array('sr_no'=>$i,
			'instruments'=>$restyui1->instruments_name,
			'stock'=>$restyui1->stock,
			'orderlist'=>$html);
			$i++;
				}
			}
	}
	
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
	
	}



 
 public function lot_order_list()
	{
		$scheduler_data = array();
		$type = "'0','1'";
			$restyui=$this->db->select('id, instruments_name, stock')->from('presto_instruments')->where_in('type',$type,false)->where('status','1')->get();
			
		if($restyui->num_rows()>0)
		{
		    $html="";
		   	$ass=0;
			$i=1;
		
			foreach($restyui->result() as $restyui1)
			{
			     $artt[]=0;
			    $query1=$this->db->select('a.id as jobcid,b.company_name,a.job_card_no,b.internal_order_no,a.order_id,c.first_name,c.last_name,a.mserialno')->from('order_instruments a')->join('prestogroup_orders b','a.order_id=b.order_id')->join('system_users c','b.marketing_person=c.user_id','left')->where('a.item_id',$restyui1->id)->get();
			
			 $html = "<table border='1' style='width:800px;'><tr style='background-color:white;width:100px;'><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:200px;'>S NO.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:200px;'>I/O NO.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:200px;'>Job Card No.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:400px;'>PARTY NAME.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:400px;'>SR. NO.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:100px;'>Days.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:100px;'>Dealing Manager.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:100px;'>Divert.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:100px;'>Cancel</th></tr>";
			 
			 	
			if($query1->num_rows()>0)
			{
			   
			$comdate=array();
			$exstock=array();
		
		    $o=1;
		   
			foreach($query1->result() as $instruments){
			    
				if($instruments->order_id>0){
				    
				$isdispatched=$this->getalldispatchedjobcard($instruments->order_id);
					if($isdispatched==0)
				{
			$artt[]=1;
				$isready=$this->checkifsinglejobcardsareready($instruments->jobcid);
				if($isready==true)
				{
				    $ass=1;
				    $back="background-color:#10C469;";
				    $fcolor="color:#fff;";
					$dorder="<a href='".page_url."FMS/divertorder/".$instruments->order_id."/1/1'><span class='btn btn-xs btn-success'>Divert order</span></a>";
				    
				    $comdate=$this->dayssinceorderisready($instruments->order_id);
				    
				    //echo "<pre>"; print_r($comdate);exit;
                    if(count($comdate)>0)
                    {
                    $recentdate=max($comdate);
                    $todaysdate=date('Y-m-d');
                    $days = (strtotime($todaysdate) - strtotime($recentdate)) / (60 * 60 * 24);
                    }else
                    {
                    $days='';
                    }
                    
                    if(strpos($instruments->company_name,'PRESTO')!== false)
                    {
                    $exstock[]=1;
                    }else
                    {
                    $exstock[]=0;
                    }

                    

				}else
				{
				    $back="";
				    $fcolor="";
				     $days='';
				     $exstock[]=0;
					 
					 $dorder="<a href='".page_url."FMS/divertorder/".$instruments->order_id."/1/0'><span class='btn btn-xs btn-success'>Divert order</span></a>";
				     
				}
				
				
				$cancell="<a href='".page_url."FMS/cancelorder/".$instruments->order_id."/".$instruments->jobcid."'><span class='btn btn-xs btn-danger'>Cancel</span></a>";
				
				$html.="<tr style='".$back."'>";
					$html.="<td style='padding:2px 2px 2px 2px; text-align:left;".$fcolor."'>".$o."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:left;".$fcolor."'>".strtoupper($instruments->internal_order_no)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:left;".$fcolor."'>".strtoupper($instruments->job_card_no)."</td>";
			
				$html.="<td style='padding:2px 2px 2px 2px; text-align:left;".$fcolor."'>".strtoupper($instruments->company_name)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:left;".$fcolor."'>".strtoupper($instruments->mserialno)."</td>";
			
					$html.="<td style='padding:2px 2px 2px 2px; text-align:left;".$fcolor."'>".$days."</td>";
					
						$html.="<td style='padding:2px 2px 2px 2px; text-align:left;".$fcolor."'>".$instruments->first_name." ".$instruments->last_name."</td>";
						$html.="<td style='padding:2px 2px 2px 2px; text-align:left;'>".$dorder."</td>";
						$html.="<td style='padding:2px 2px 2px 2px; text-align:left;'>".$cancell."</td>";
				$html.="</tr>";
				
				$o++;	
			}
				}else{
				    
				}
			
		
			    
			}
			
		   
			
			$html.="</table>";
			
		
		
			}else{
				$isdispatched="0";
				$html=$html;
					$artt[]=0;
			}
			
			
				if(array_sum($artt)>0)
			{
			    $html=$html;
			}else
			{
			    $html='';
			}
			
		
			
			
				   
			$scheduler_data[] = array('sr_no'=>$i,
			'instruments'=>$restyui1->instruments_name,
			'stock'=>$restyui1->stock,
			'orderlist'=>$html);
			$i++;
				
			}
	}
	
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
	
	}
	
	
	
public function lot_order_listOldddddddddddddddddddddd()
	{
		$scheduler_data = array();
		 
			$restyui=$this->db->select('b.id as item_id,b.instruments_name,b.stock')->from('presto_instruments b')->where_in('b.type','0','1')->order_by('b.instruments_name')->get();
		if($restyui->num_rows()>0)
		{
			$i=1;
			
			foreach($restyui->result() as $restyui1)
			{
			    $query1=$this->db->select('b.company_name,a.job_card_no,b.internal_order_no,a.order_id,a.id as jobcid')->from('order_instruments a')->join('prestogroup_orders b','a.order_id=b.order_id')->where('a.item_id',$restyui1->item_id)->group_by('a.order_id')->get();
			
			
			if($query1->num_rows()>0)
			{
				$html = "<table border='1' style='width:800px;'><tr style='background-color:white;width:100px;'><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:200px;'>I/O NO.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:200px;'>JOBCARD NO.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:400px;'>PARTY NAME.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:100px;'>Days.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:100px;'>Divert.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:100px;'>Cancel</th></tr>";
			
			
			$comdate=array();
			$arr[]=0;
			foreach($query1->result() as $instruments){
				
				$isdispatched=$this->getalldispatchedjobcard($instruments->order_id);
					if($isdispatched==0)
				{
			
				$arr[]=1;
				$isready=$this->checkifalljobcardsareready($instruments->order_id);
				$isready=$this->checkifsinglejobcardsareready($instruments->jobcid);
				
				if($isready==true)
				{
				    $back="background-color:#10C469";
				    $fcolor="color:#fff;";
				    	$dorder="<a href='".page_url."FMS/divertorder/".$instruments->order_id."/1'><span class='btn btn-xs btn-success'>Divert order</span></a>";
				    $comdate=$this->dayssinceorderisready($instruments->order_id);
				    
				    //echo "<pre>"; print_r($comdate);exit;
                    if(count($comdate)>0)
                    {
                    $recentdate=max($comdate);
                    $todaysdate=date('Y-m-d');
                    $days = (strtotime($todaysdate) - strtotime($recentdate)) / (60 * 60 * 24);
                    }else
                    {
                    $days='';
                    }

				}else
				{
				    $back="";
				    $fcolor="";
				     $days='';
				     	$dorder="<a href='".page_url."FMS/divertorder/".$instruments->order_id."/1'><span class='btn btn-xs btn-success'>Divert order</span></a>";
				}
				
				
					$cancell="<a href='".page_url."FMS/cancelorder/".$instruments->order_id."/".$instruments->jobcid."'><span class='btn btn-xs btn-danger'>Cancel</span></a>";
				
				
				$html.="<tr style='".$back."'>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:left;width:10px;".$fcolor."'>".strtoupper($instruments->internal_order_no)."</td>";
					$html.="<td style='padding:2px 2px 2px 2px; text-align:left;width:10px;".$fcolor."'>".strtoupper($instruments->job_card_no)."</td>";
			
				$html.="<td style='padding:2px 2px 2px 2px; text-align:left;width:100px;".$fcolor."'>".strtoupper($instruments->company_name)."</td>";
			
					$html.="<td style='padding:2px 2px 2px 2px; text-align:left;width:100px;".$fcolor."'>".$days."</td>";
						$html.="<td style='padding:2px 2px 2px 2px; text-align:left;width:100px;'>".$dorder."</td>";
						$html.="<td style='padding:2px 2px 2px 2px; text-align:left;width:100px;'>".$cancell."</td>";
				$html.="</tr>";
			}
			
			}
			
		   
			
			$html.="</table>";
			}else{
				
				$html="";
			}
			
		
	
		
			$scheduler_data[] = array('sr_no'=>$i,
			'instruments'=>$restyui1->instruments_name,
			'stock'=>$restyui1->stock,
			'orderlist'=>$html);
			$i++;
				}
			
	}
	
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
	
	}





public function lot_order_listtmppold()
	{
		$scheduler_data = array();
		
			$restyui=$this->db->select('a.item_id,b.instruments_name,b.stock')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->join('prestogroup_orders c','a.order_id=c.order_id')->where('c.closeorder','0')->group_by('a.item_id')->order_by('b.instruments_name')->get();
		if($restyui->num_rows()>0)
		{
			$i=1;
			foreach($restyui->result() as $restyui1)
			{
			    $query1=$this->db->select('b.company_name,a.job_card_no,b.internal_order_no,a.order_id,a.id as jobcid')->from('order_instruments a')->join('prestogroup_orders b','a.order_id=b.order_id')->where('a.item_id',$restyui1->item_id)->group_by('a.order_id')->get();
			
			if($query1->num_rows()>0)
			{
				$html = "<table border='1' style='width:800px;'><tr style='background-color:white;width:100px;'><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:200px;'>I/O NO.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:200px;'>JOBCARD NO.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:400px;'>PARTY NAME.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:100px;'>Days.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:100px;'>Divert.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:100px;'>Cancel</th></tr>";
			
			
			$comdate=array();
			foreach($query1->result() as $instruments){
				
				$isdispatched=$this->getalldispatchedjobcard($instruments->order_id);
					if($isdispatched==0)
				{
			
				$isready=$this->checkifalljobcardsareready($instruments->order_id);
				$isready=$this->checkifsinglejobcardsareready($instruments->jobcid);
				
				if($isready==true)
				{
				    $back="background-color:#10C469";
				    $fcolor="color:#fff;";
				    	$dorder="<a href='".page_url."FMS/divertorder/".$instruments->order_id."/1'><span class='btn btn-xs btn-success'>Divert order</span></a>";
				    $comdate=$this->dayssinceorderisready($instruments->order_id);
				    
				    //echo "<pre>"; print_r($comdate);exit;
                    if(count($comdate)>0)
                    {
                    $recentdate=max($comdate);
                    $todaysdate=date('Y-m-d');
                    $days = (strtotime($todaysdate) - strtotime($recentdate)) / (60 * 60 * 24);
                    }else
                    {
                    $days='';
                    }

				}else
				{
				    $back="";
				    $fcolor="";
				     $days='';
				     	$dorder="<a href='".page_url."FMS/divertorder/".$instruments->order_id."/1'><span class='btn btn-xs btn-success'>Divert order</span></a>";
				}
				
				
					$cancell="<a href='".page_url."FMS/cancelorder/".$instruments->order_id."/".$instruments->jobcid."'><span class='btn btn-xs btn-danger'>Cancel</span></a>";
				
				
				$html.="<tr style='".$back."'>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:left;width:10px;".$fcolor."'>".strtoupper($instruments->internal_order_no)."</td>";
					$html.="<td style='padding:2px 2px 2px 2px; text-align:left;width:10px;".$fcolor."'>".strtoupper($instruments->job_card_no)."</td>";
			
				$html.="<td style='padding:2px 2px 2px 2px; text-align:left;width:100px;".$fcolor."'>".strtoupper($instruments->company_name)."</td>";
			
					$html.="<td style='padding:2px 2px 2px 2px; text-align:left;width:100px;".$fcolor."'>".$days."</td>";
						$html.="<td style='padding:2px 2px 2px 2px; text-align:left;width:100px;'>".$dorder."</td>";
						$html.="<td style='padding:2px 2px 2px 2px; text-align:left;width:100px;'>".$cancell."</td>";
				$html.="</tr>";
			}
			
			}
			
		   
			
			$html.="</table>";
			}else{
				
				$html="";
			}
			
			if($isdispatched==0)
				{
			$scheduler_data[] = array('sr_no'=>$i,
			'instruments'=>$restyui1->instruments_name,
			'stock'=>$restyui1->stock,
			'orderlist'=>$html);
			$i++;
				}
			}
	}
	
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
	
	}


	public function lot_order_listOlddsaurabhtoday()
	{
		$scheduler_data = array();
		
		$restyui=$this->db->select('a.item_id,b.instruments_name')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->where('complete','0')->group_by('a.item_id')->order_by('b.instruments_name')->get();
		if($restyui->num_rows()>0)
		{
			$i=1;
			foreach($restyui->result() as $restyui1)
			{
				
				
				$query1=$this->db->select('b.company_name,a.job_card_no,b.internal_order_no')->from('order_instruments a')->join('prestogroup_orders b','a.order_id=b.order_id')->where('a.item_id',$restyui1->item_id)->where('a.complete','0')->get();
			if($query1->num_rows()>0)
			{
				$html = "<table border='1' style='width:500px;'><tr style='background-color:white;text-align:center;'><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;'>I/O NO.</th><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;'>PARTY NAME.</th></tr>";
			
			
			
			foreach($query1->result() as $instruments){
				
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($instruments->internal_order_no)."</td>";
			
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($instruments->company_name)."</td>";
				$html.="</tr>";
			}
			
			$html.="</table>";
			}else{
				
				$html="";
			}
			
			
		$scheduler_data[] = array('sr_no'=>$i,
			'instruments'=>$restyui1->instruments_name,
			'orderlist'=>$html);
			$i++;
			}
		}
		
			$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}


function userwisepermisionreport()
{
	
	$this->load->view('master/userwisepermisionreport');
	
	
}



function machinecostprice()
{
	
	$this->load->view('FMS/stockcost');
	
	
}

function listproductionwipcost()
{
	$scheduler_data=array();
	
	$restyu=$this->productionflowlongreport();
	if($restyu==0)
	{
		$restqwee=$this->db->select('flow_id,fms_flow,stockvalue,production_flow_id,dependency')->from('fms_flow')->where('stockvalue>','0')->where_in('production_flow_id',$restyu,false)->order_by('production_flow_id')->get();
		if($restqwee->num_rows()>0)
		{
			$i=1;
			foreach($restqwee->result() as $restqwee1)
			{
				
			/** GET ALL INSTRUMENTS ON THIS STAGE **/
				if($restqwee1->dependency==0)
				{
				$details=$this->onstagejobcard($restqwee1->flow_id,$restqwee1->stockvalue,$restqwee1->production_flow_id);
				}else{
					
					$details=$this->onstagejobcardfordependent($restqwee1->flow_id,$restqwee1->stockvalue,$restqwee1->production_flow_id);
				}
				
	
	$scheduler_data[] = array('sr_no'=>$i,
			'stage'=>$restqwee1->fms_flow,
			'stagevalue'=>$restqwee1->stockvalue." %",
			'detail'=>$details['table'],
			'totalcost'=>$details['stagetotal']
			);
			
			$i++;
			}
		}
			
			
	}
	
	$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
}

function productionflowlongreport()
{	
	$prd=array();
	$restyu=$this->db->select('id')->from('production_flow')->where('longreport','1')->get();
	if($restyu->num_rows()>0)
	{
		foreach($restyu->result() as $restyu1)
		{
			
			$prd[]=$restyu1->id;
		}
		
		$result = "'" . implode ( "', '", $prd ) . "'";
		return $result;
	}else{
		
		return 0;
	}
	
	
}

function onstagejobcard($flowid,$stockvalue,$productionid)
{
	/** GET MACHINE CP **/
	$cp=0;
	$restyuiioo=$this->db->select('cp')->from('machinecp')->get();
	if($restyuiioo->num_rows()>0)
	{
		foreach($restyuiioo->result() as $restyuiioo1);
		$cp=$restyuiioo1->cp;
	}
	/** END **/
	$htm='';
	$htm.="<table border='1' style='width:100%;'><tr style='background-color:white;text-align:left;'><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:70%;'>INSTRUMENT.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:15%;'>JOBCARD.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:15%;'>VALUE.</th></tr>";
	$data=$this->db->select('a.jobcardid,b.job_card_no,c.instruments_name,c.mvalue')->from('order_stage a')->join('order_instruments b','a.jobcardid=b.id')->join('presto_instruments c','b.item_id=c.id')->where('a.flowstage',$flowid)->where('a.userstatus','0')->get();
	$total=array();
/**	if($flowid=='7')
	{
	   $alljb=$this->checkforduplication($flowid+1);
	   echo "<pre>"; print_r($alljb);exit;
	}else
	{
	    $alljb=array();
	}
	**/
	
/**	$restyyyee=$this->getsuper($productionid,$flowid);
	if($restyyyee>0)
	{
	    
	}**/
	
	if($data->num_rows()>0)
	{
		
		foreach($data->result() as $datas)
		{
		    if(!in_array($datas->jobcardid,$this->jobcardara))
			{
				array_push($this->jobcardara, $datas->jobcardid);
			
			$machinewisevalue=$this->getmachinevalue($cp,$stockvalue,$datas->mvalue,$datas->jobcardid);
			$total[]=$machinewisevalue;
			$htm.="<tr>";
							$htm.="<td style='padding:2px 2px 2px 2px; text-align:left;'>".strtoupper($datas->instruments_name)."</td>";

							$htm.="<td style='padding:2px 2px 2px 2px; text-align:left'>".strtoupper($datas->job_card_no)."</td>";
							$htm.="<td style='padding:2px 2px 2px 2px; text-align:left;'>".$machinewisevalue."</td>";
							$htm.="</tr>";
			}
			
		}
		
	}else{
		
		
							$htm.="<td colspan='3' style='padding:2px 2px 2px 2px; text-align:center;'>NO Machines Available</td>";
		
	}
	
	
	$overallstagetot=$this->giveoveralldata($total);
	return array('table'=>$htm,'stagetotal'=>$overallstagetot);
}


function getmachinevalue($cp,$stockvalue,$mvalue,$jobcard)
{
	
	$getproductioncost=($cp*$mvalue)/100;
	$onlyprdcost=$getproductioncost;
	
	$mval=($onlyprdcost*$stockvalue)/100;
	
	return $mval;
	
}

function giveoveralldata($total)
{
	if(count($total)>0)
	{
		
		return array_sum($total);
		
	}else{
		
		return 0;
		
	}
}



function pendingpr()
{
 
$this->load->view('store/pendingpr');
	
}

function pendingprlistoldddieieie()
{
	
			$scheduler_data = array();
            $type=$this->uri->segment('3');
		$rest=$this->db->select('a.jobcardid,a.type,a.sourceid,a.source,a.addedOn,a.prno,e.first_name,e.last_name,a.itemid,e.department_id')->from('purchase_request a')->join('machine_bom b','a.itemid=b.partid','left')->join('system_users e','e.user_id=a.addedBy')->where('a.approvalstatus','0');
		if($type<>'')
        {
            $this->db->where('a.source',$type);
        }
		$rest=$this->db->order_by('a.addedOn','DESC')->group_by('a.prno')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				
					$jobcard='';

					if($restyui1->source==1)
					{
					$job=$this->db->select('job_card_no')->from('order_instruments')->where('id',$restyui1->jobcardid)->get();
					if($job->num_rows()>0)
					{
					foreach($job->result() as $job1);
					$jobcard='Jobcard-'.$job1->job_card_no;
					}else{
					$jobcard='';
					}
					}else if($restyui1->source==2)
					{
					/** Intend **/
					$jobcard='INDENT- IND'.$restyui1->sourceid;
					}else
					{
					    $jobcard="IMS AUTO PR";
					}

if($restyui1->type==0)
{
	$pr="pr";
	$polink="createpo";
}else{
$pr="housekeepingpr";
$polink="creategeneralpo";
}	

$html='<a href="'.page_url.'Store/'.$polink.'/'.$restyui1->prno.'" class="btn btn-warning btn-xs">Recieved & Raise PO</a>';

			$scheduler_data[] = array('sr_no'=>$i,
			'prno'=>"<a href='".page_url."Store/".$pr."/".$restyui1->prno."' target='_blank'>".$restyui1->prno."</a>",
			'jobcardno'=>$jobcard,
			'createdby'=>ucfirst($restyui1->first_name." ".$restyui1->last_name),
			'createdon'=>date('d-M-Y g:i A',strtotime($restyui1->addedOn)),
			'markrecvd'=>$html);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
	


}


function pendingprlist()
{
	
			$scheduler_data = array();

		$rest=$this->db->select('a.jobcardid,a.type,a.sourceid,a.source,a.addedOn,a.prno,e.first_name,e.last_name,a.itemid,e.department_id, f.first_name as approvedbyfname, f.last_name as approvedlname, i.approvedBy')->from('purchase_request a')->join('system_users e','e.user_id=a.addedBy','left')->join('intend_request i','a.sourceid=i.indendno','left')->join('system_users f','f.user_id=i.approvedBy','left')->where('a.approvalstatus','0')->where('a.closed','1')->order_by('a.addedOn','DESC')->group_by('a.prno')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				
					$jobcard='';

					if($restyui1->source==1)
					{
					$job=$this->db->select('job_card_no')->from('order_instruments')->where('id',$restyui1->jobcardid)->get();
					if($job->num_rows()>0)
					{
					foreach($job->result() as $job1);
					$jobcard='Jobcard-'.$job1->job_card_no;
					}else{
					$jobcard='';
					}
					}else if($restyui1->source==2)
					{
					/** Intend **/
					$jobcard='INDENT- IND'.$restyui1->sourceid;
					}	

if($restyui1->type=='0')
{
	$pr="pr";
	$polink="createpo";
}else{
$pr="housekeepingpr";
$polink="creategeneralpo";
}	

$html='<a href="'.page_url.'Store/'.$polink.'/'.$restyui1->prno.'" class="btn btn-warning btn-xs">Raise PO</a>';

			$scheduler_data[] = array('sr_no'=>$i,
			'prno'=>"<a href='".page_url."Store/".$pr."/".$restyui1->prno."' target='_blank'>".$restyui1->prno."</a>",
			'jobcardno'=>$jobcard,
			'createdby'=>ucfirst($restyui1->first_name." ".$restyui1->last_name),
			'approvedby'=>$restyui1->approvedbyfname." ".$restyui1->approvedlname,
			'createdon'=>date('d-M-Y g:i A',strtotime($restyui1->addedOn)),
			'markrecvd'=>$html);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
	


}




function pendingprlistold()
{
	
			$scheduler_data = array();
            $type=$this->uri->segment('3');
		$rest=$this->db->select('a.jobcardid,a.type,a.sourceid,a.source,a.addedOn,a.prno,e.first_name,e.last_name,a.itemid,e.department_id')->from('purchase_request a')->join('machine_bom b','a.itemid=b.partid','left')->join('system_users e','e.user_id=a.addedBy')->where('a.approvalstatus','0');
		if($type<>'')
        {
            $this->db->where('a.source',$type);
        }
		$rest=$this->db->order_by('a.addedOn','DESC')->group_by('a.prno')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				
					$jobcard='';

					if($restyui1->source==1)
					{
					$job=$this->db->select('job_card_no')->from('order_instruments')->where('id',$restyui1->jobcardid)->get();
					if($job->num_rows()>0)
					{
					foreach($job->result() as $job1);
					$jobcard='Jobcard-'.$job1->job_card_no;
					}else{
					$jobcard='';
					}
					}else if($restyui1->source==2)
					{
					/** Intend **/
					$jobcard='INDENT- IND'.$restyui1->sourceid;
					}else
					{
					    $jobcard="IMS AUTO PR";
					}

if($restyui1->type==0)
{
	$pr="pr";
	$polink="createpo";
}else{
$pr="housekeepingpr";
$polink="creategeneralpo";
}	

$html='<a href="'.page_url.'Store/'.$polink.'/'.$restyui1->prno.'" class="btn btn-warning btn-xs">Recieved & Raise PO</a>';

			$scheduler_data[] = array('sr_no'=>$i,
			'prno'=>"<a href='".page_url."Store/".$pr."/".$restyui1->prno."' target='_blank'>".$restyui1->prno."</a>",
			'jobcardno'=>$jobcard,
			'createdby'=>ucfirst($restyui1->first_name." ".$restyui1->last_name),
			'createdon'=>date('d-M-Y g:i A',strtotime($restyui1->addedOn)),
			'markrecvd'=>$html);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
	


}


function pendingprlistOlddddddd()
{
	
			$scheduler_data = array();

		$rest=$this->db->select('a.jobcardid,a.sourceid,a.source,a.addedOn,a.prno,e.first_name,e.last_name,a.itemid,e.department_id')->from('purchase_request a')->join('machine_bom b','a.itemid=b.partid','left')->join('system_users e','e.user_id=a.addedBy')->where('a.approvalstatus','0')->order_by('a.addedOn','DESC')->group_by('a.prno')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				
					$jobcard='';

					if($restyui1->source==1)
					{
					$job=$this->db->select('job_card_no')->from('order_instruments')->where('id',$restyui1->jobcardid)->get();
					if($job->num_rows()>0)
					{
					foreach($job->result() as $job1);
					$jobcard='Jobcard-'.$job1->job_card_no;
					}else{
					$jobcard='';
					}
					}else if($restyui1->source==2)
					{
					/** Intend **/
					$jobcard='INDENT- IND'.$restyui1->sourceid;
					}					

$html='<a href="'.page_url.'Store/createpo/'.$restyui1->prno.'" class="btn btn-warning btn-xs">Recieved & Raise PO</a>';

			$scheduler_data[] = array('sr_no'=>$i,
			'prno'=>"<a href='".page_url."Store/pr/".$restyui1->prno."' target='_blank'>".$restyui1->prno."</a>",
			'jobcardno'=>$jobcard,
			'createdby'=>ucfirst($restyui1->first_name." ".$restyui1->last_name),
			'createdon'=>date('d-M-Y g:i A',strtotime($restyui1->addedOn)),
			'markrecvd'=>$html);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
	


}


function pendingprlistOlddddd()
{
	
			$scheduler_data = array();

		$rest=$this->db->select('a.jobcardid,a.sourceid,a.source,a.addedOn,a.prno,e.first_name,e.last_name,a.itemid,e.department_id')->from('purchase_request a')->join('machine_bom b','a.itemid=b.partid','left')->join('system_users e','e.user_id=a.addedBy')->where('a.approvalstatus','0')->order_by('a.addedOn','DESC')->group_by('a.prno')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				
					$jobcard='';

					if($restyui1->source==1)
					{
					$job=$this->db->select('job_card_no')->from('order_instruments')->where('id',$restyui1->jobcardid)->get();
					if($job->num_rows()>0)
					{
					foreach($job->result() as $job1);
					$jobcard='Jobcard-'.$job1->job_card_no;
					}else{
					$jobcard='';
					}
					}else if($restyui1->source==2)
					{
					/** Intend **/
					$jobcard='INDENT- IND'.$restyui1->sourceid;
					}					

$html='<a href="'.page_url.'Store/createpo/'.$restyui1->prno.'" class="btn btn-warning btn-xs">Recieved & Raise PO</a>';

			$scheduler_data[] = array('sr_no'=>$i,
			'prno'=>"<a href='".page_url."Store/pr/".$restyui1->prno."' target='_blank'>".$restyui1->prno."</a>",
			'jobcardno'=>$jobcard,
			'createdby'=>ucfirst($restyui1->first_name." ".$restyui1->last_name),
			'createdon'=>date('d-M-Y g:i A',strtotime($restyui1->addedOn)),
			'markrecvd'=>$html);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
	


}




function pendingprlistOldddd()
{
	
			$scheduler_data = array();

		$rest=$this->db->select('a.jobcardid,a.sourceid,a.source,a.addedOn,a.prno,e.first_name,e.last_name,a.itemid,e.department_id')->from('purchase_request a')->join('machine_parts_master b','a.itemid=b.part_id')->join('system_users e','e.user_id=a.addedBy')->where('a.approvalstatus','0')->order_by('a.addedOn','DESC')->group_by('a.prno')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				
					$jobcard='';

					if($restyui1->source==1)
					{
					$job=$this->db->select('job_card_no')->from('order_instruments')->where('id',$restyui1->jobcardid)->get();
					if($job->num_rows()>0)
					{
					foreach($job->result() as $job1);
					$jobcard='Jobcard-'.$job1->job_card_no;
					}else{
					$jobcard='';
					}
					}else if($restyui1->source==2)
					{
					/** Intend **/
					$jobcard='INDENT- IND'.$restyui1->sourceid;
					}					

$html='<a href="" class="btn btn-warning btn-xs">Recieved & Raise PO</a>';

			$scheduler_data[] = array('sr_no'=>$i,
			'prno'=>"<a href='".page_url."Store/pr/".$restyui1->prno."' target='_blank'>".$restyui1->prno."</a>",
			'jobcardno'=>$jobcard,
			'createdby'=>ucfirst($restyui1->first_name." ".$restyui1->last_name),
			'createdon'=>date('d-M-Y g:i A',strtotime($restyui1->addedOn)),
			'markrecvd'=>$html);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
	


}


function pendingindend()
{
	
	$this->load->view('store/intendrequest');
	
}

function pendingindentlist()
{
	$scheduler_data = array();
		$htm="";
		
		$rest=$this->db->select('a.id as intendid,a.addedOn,a.prefix,a.indendno,e.first_name,e.last_name,a.itemid, a.indent_type')->from('intend_request a')->join('system_users e','e.user_id=a.addedBy','left')->where('a.approvalstatus','0')->where('pr_status','0')->order_by('a.addedOn','DESC')->group_by('a.indendno')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				
				if($restyui1->indent_type=='1'){
					$indenttype = "Machine Related Items";
				$rest123=$this->db->select('b.part as item_name,a.qty,a.unit')->from('intend_request a')->join('machine_parts_with_picture b','a.itemid=b.id')->where('a.indendno',$restyui1->indendno)->get();	
				}else{
					$indenttype = "General Items";
				$rest123=$this->db->select('b.item_name,a.qty,a.unit')->from('intend_request a')->join('house_keeping_items b','a.itemid=b.id')->where('a.indendno',$restyui1->indendno)->get();	
				}
				
				
				if($rest123->num_rows()>0)
				{
					$htm='';
					$htm.="<table border='1' style='width:500px;'><tr style='background-color:white;text-align:center;'><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;'>ITEM.</th><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;'>QTY.</th></tr>";
					foreach($rest123->result() as $rest1231)
					{
					
						
							$htm.="<tr>";
							$htm.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($rest1231->item_name)."</td>";

							$htm.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($rest1231->qty)."</td>";
							$htm.="</tr>";
					}
					
					$htm.="</table>";
			}
			
$prquery = $this->db->select('prno')->from('purchase_request')->where('sourceid',$restyui1->indendno)->get();
if($prquery->num_rows()>0){
	foreach($prquery->result() as $prdata);
	$html="<a href='".page_url."Store/pendingindend/".$prdata->prno."/".$restyui1->indent_type."' class='btn btn-success btn-xs'>".$prdata->prno."</a>";
}else{
	$html='<a href="'.page_url.'Store/generateprfromintend/'.$restyui1->indendno.'/" class="btn btn-warning btn-xs">Recieved & Generate PR</a>';
}		
			


			$scheduler_data[] = array('sr_no'=>$i,
			'indent_type'=>$indenttype,
			'prno'=>$restyui1->prefix.'-'.$restyui1->indendno,
			'itemdetail'=>$htm,
			'createdby'=>ucfirst($restyui1->first_name." ".$restyui1->last_name),
			'createdon'=>date('d-M-Y g:i A',strtotime($restyui1->addedOn)),
			'markrecvd'=>$html);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
	


}
function pendingindentlist_old()
{
	
			$scheduler_data = array();
		$htm="";
		
		$rest=$this->db->select('a.id as intendid,a.addedOn,a.prefix,a.indendno,e.first_name,e.last_name,a.itemid')->from('intend_request a')->join('system_users e','e.user_id=a.addedBy')->where('a.approvalstatus','0')->order_by('a.addedOn','DESC')->group_by('a.indendno')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				
				$rest123=$this->db->select('b.item_name,a.qty,a.unit')->from('intend_request a')->join('house_keeping_items b','a.itemid=b.id')->where('a.indendno',$restyui1->indendno)->get();
				if($rest123->num_rows()>0)
				{
					$htm='';
					$htm.="<table border='1' style='width:500px;'><tr style='background-color:white;text-align:center;'><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;'>ITEM.</th><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;'>QTY.</th></tr>";
					foreach($rest123->result() as $rest1231)
					{
					
						
							$htm.="<tr>";
							$htm.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($rest1231->item_name)."</td>";

							$htm.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($rest1231->qty)."</td>";
							$htm.="</tr>";
					}
					
					$htm.="</table>";
			}
			
			
			
$html='<a href="'.page_url.'Store/generateprfromintend/'.$restyui1->indendno.'/" class="btn btn-warning btn-xs">Recieved & Generate PR</a>';

			$scheduler_data[] = array('sr_no'=>$i,
			'prno'=>$restyui1->prefix.'-'.$restyui1->indendno,
			'itemdetail'=>$htm,
			'createdby'=>ucfirst($restyui1->first_name." ".$restyui1->last_name),
			'createdon'=>date('d-M-Y g:i A',strtotime($restyui1->addedOn)),
			'markrecvd'=>$html);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
	


}


function generatejobcard()
{
	
	
	$this->load->view('jobcardformat/index');
	
	
}


function pendingpolist()
{

			$scheduler_data = array();
$genpo=base64_decode($this->uri->segment(3));
$genp=explode(',',$genpo);
		$rest=$this->db->select('a.jobcard,a.source,a.sourceid,a.addedOn,a.pono,e.first_name,e.last_name,a.itemid,e.department_id')->from('purchase_order a')->join('machine_bom b','a.itemid=b.partid','left')->join('system_users e','e.user_id=a.addedBy')->where('a.completed','0')->order_by('a.addedOn','DESC')->group_by('a.pono')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				if(in_array($restyui1->pono,$genp))
				{
					$bc="red";
				}else{
					$bc='';
				}
				
					$jobcard='';

					if($restyui1->source==1)
					{
					$job=$this->db->select('job_card_no')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
					if($job->num_rows()>0)
					{
					foreach($job->result() as $job1);
					$jobcard='Jobcard-'.$job1->job_card_no;
					}else{
					$jobcard='';
					}
					$polink="po";
					$mrnlink="mrn";
					}else if($restyui1->source==2)
					{
					/** Intend **/
					$jobcard='INDENT- IND'.$restyui1->sourceid;
					$polink="generalpo";
					$mrnlink="generalmrn";
					}else if($restyui1->source==3)
					{
					/** Intend **/
					$jobcard='AUTO PR';
					$polink="po";
					$mrnlink="mrn";
					}					


$html='<a href="'.page_url.'Store/'.$mrnlink.'/'.$restyui1->pono.'" class="btn btn-warning btn-xs">CREATE MRN</a>';

			$scheduler_data[] = array('sr_no'=>$i,
			'prno'=>"<a href='".page_url."Store/".$polink."/".$restyui1->pono."' target='_blank' style='color:".$bc."'>".$restyui1->pono."</a>",
			'jobcardno'=>$jobcard,
			'createdby'=>ucfirst($restyui1->first_name." ".$restyui1->last_name),
			'createdon'=>date('d-M-Y g:i A',strtotime($restyui1->addedOn)),
			'markrecvd'=>$html);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
	



}


function pendingpolistOlddd()
{

			$scheduler_data = array();
$genpo=base64_decode($this->uri->segment(3));
$genp=explode(',',$genpo);
		$rest=$this->db->select('a.jobcard,a.source,a.addedOn,a.pono,e.first_name,e.last_name,a.itemid,e.department_id')->from('purchase_order a')->join('machine_bom b','a.itemid=b.partid','left')->join('system_users e','e.user_id=a.addedBy')->where('a.completed','0')->order_by('a.addedOn','DESC')->group_by('a.pono')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				if(in_array($restyui1->pono,$genp))
				{
					$bc="red";
				}else{
					$bc='';
				}
				
					$jobcard='';

					if($restyui1->source==1)
					{
					$job=$this->db->select('job_card_no')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
					if($job->num_rows()>0)
					{
					foreach($job->result() as $job1);
					$jobcard='Jobcard-'.$job1->job_card_no;
					}else{
					$jobcard='';
					}
					}else if($restyui1->source==2)
					{
					/** Intend **/
					$jobcard='INDENT- IND'.$restyui1->sourceid;
					}else if($restyui1->source==3)
					{
					/** Intend **/
					$jobcard='AUTO PR';
					}					


$html='<a href="'.page_url.'Store/mrn/'.$restyui1->pono.'" class="btn btn-warning btn-xs">CREATE MRN</a>';

			$scheduler_data[] = array('sr_no'=>$i,
			'prno'=>"<a href='".page_url."Store/po/".$restyui1->pono."' target='_blank' style='color:".$bc."'>".$restyui1->pono."</a>",
			'jobcardno'=>$jobcard,
			'createdby'=>ucfirst($restyui1->first_name." ".$restyui1->last_name),
			'createdon'=>date('d-M-Y g:i A',strtotime($restyui1->addedOn)),
			'markrecvd'=>$html);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
	


}



function pendingpolistoldddddd()
{

			$scheduler_data = array();
$genpo=base64_decode($this->uri->segment(3));
$genp=explode(',',$genpo);
		$rest=$this->db->select('a.jobcard,a.source,a.addedOn,a.pono,e.first_name,e.last_name,a.itemid,e.department_id')->from('purchase_order a')->join('machine_bom b','a.itemid=b.partid','left')->join('system_users e','e.user_id=a.addedBy')->where('a.completed','0')->order_by('a.addedOn','DESC')->group_by('a.pono')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				if(in_array($restyui1->pono,$genp))
				{
					$bc="red";
				}else{
					$bc='';
				}
				
					$jobcard='';

					if($restyui1->source==1)
					{
					$job=$this->db->select('job_card_no')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
					if($job->num_rows()>0)
					{
					foreach($job->result() as $job1);
					$jobcard='Jobcard-'.$job1->job_card_no;
					}else{
					$jobcard='';
					}
					}else if($restyui1->source==2)
					{
					/** Intend **/
					$jobcard='INDENT- IND'.$restyui1->sourceid;
					}					


$html='<a href="'.page_url.'Store/createpo/'.$restyui1->pono.'" class="btn btn-warning btn-xs">CREATE MRN</a>';

			$scheduler_data[] = array('sr_no'=>$i,
			'prno'=>"<a href='".page_url."Store/po/".$restyui1->pono."' target='_blank' style='color:".$bc."'>".$restyui1->pono."</a>",
			'jobcardno'=>$jobcard,
			'createdby'=>ucfirst($restyui1->first_name." ".$restyui1->last_name),
			'createdon'=>date('d-M-Y g:i A',strtotime($restyui1->addedOn)),
			'markrecvd'=>$html);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
	


}

	function pendingpo()
	{
		
		$this->load->view('store/pendingpo');
		
		
	}
	
	
	
	function closedpo()
	{
		
		$this->load->view('store/closedpo');
		
		
	}
	
	
	
function closedpolist()
{

			$scheduler_data = array();
		$rest=$this->db->select('a.jobcard,a.source,a.addedOn,a.pono,e.first_name,e.last_name,a.itemid,e.department_id,a.sourceid,a.prno')->from('purchase_order a')->join('system_users e','e.user_id=a.addedBy')->where('a.completed','1')->order_by('a.addedOn','DESC')->group_by('a.pono')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				
					$jobcard='';

					if($restyui1->source==1)
					{	
					$job=$this->db->select('job_card_no')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
					if($job->num_rows()>0)
					{
					foreach($job->result() as $job1);
					$jobcard='Jobcard-'.$job1->job_card_no;
					}else{
					$jobcard='';
					}
					}else if($restyui1->source==2)
					{
					$sourceid=$this->storemodel->getindentno($restyui1->prno);
					/** Intend **/
					$jobcard='INDENT- IND'.$sourceid;
					}else
					{
					$jobcard="IMS";
					}					


$html='<a href="'.page_url.'Store/mrn/'.$restyui1->pono.'" class="btn btn-warning btn-xs">CREATE MRN</a>';
/** MRN DATE LATEST **/

$restyad=$this->db->select('addedOn')->from('mrn')->where('pono',$restyui1->pono)->order_by('id','DESC')->get();
if($restyad->num_rows()>0)
{
	foreach($restyad->result() as $restyad1);
	$bandw=date('d-m-Y g:i A',strtotime($restyad1->addedOn));
}else
{
	$bandw='';
}

			$scheduler_data[] = array('sr_no'=>$i,
			'prno'=>"<a href='".page_url."Store/po/".$restyui1->pono."' target='_blank'>".$restyui1->pono."</a>",
			'jobcardno'=>$jobcard,
			'createdby'=>ucfirst($restyui1->first_name." ".$restyui1->last_name),
			'createdon'=>date('d-M-Y g:i A',strtotime($restyui1->addedOn)),
			'markrecvd'=>$bandw);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
	


}



	function mrnqc()
	{
	$this->load->view('store/mrnqc');
	}
	
	
function mrnqcrequest()
	{
		
			$scheduler_data = array();
		$rest=$this->db->select('a.*,b.source')->from('mrn a')->join('purchase_order b','a.pono=b.pono')->where('mrndone','1')->where('qcstatus','0')->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				
				if($restyui1->source==1 || $restyui1->source==3)
				{
				$itemname=$this->getmachineitemname($restyui1->itemid);
				$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
				$link='accept_reject_debit_note';
				if(count($otherdetails)>0)
				{
			
				$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
				}else{
					$fincode='';
					$specification='';
				}
					$polink="po";
				}else{
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
					$link='accept_reject_debit_noteforgeneralitems';
					$polink="generalpo";
				}
				
				
				if($restyui1->qcstatus==0)
				{
			$action="<a href='".page_url."Store/".$link."/".$restyui1->id."/".$restyui1->pono."'><span class='btn btn-warning btn-xs'>UPDATE</span></a>";
				}else{
					$action="<span class='btn btn-warning btn-xs'>QC DONE</span>";
				}
				
				$uname=$this->getunit($restyui1->unit);
			$scheduler_data[] = array('sr_no'=>$i,
			'item'=>$itemname,
			'fincode'=>$fincode,
			'specialization'=>$specification,
			'qty'=>floatval($restyui1->recqty)." ".$uname,
			'pono'=>"<a href='".page_url."Store/".$polink."/".$restyui1->pono."' target='_blank'>".$restyui1->pono."</a>",
			'action'=>$action);
				
			$i++;
			}
			}
			
			
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
		
		
	}

function mrnqcrequestold()
	{
		
			$scheduler_data = array();
		$rest=$this->db->select('a.*,b.source')->from('mrn a')->join('purchase_order b','a.pono=b.pono')->where('mrndone','1')->where('qcstatus','0')->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				
				if($restyui1->source==1 || $restyui1->source==3)
				{
				$itemname=$this->getmachineitemname($restyui1->itemid);
				$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
				$link='accept_reject_debit_note';
				if(count($otherdetails)>0)
				{
			
				$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
				}else{
					$fincode='';
					$specification='';
				}
					$polink="po";
				}else{
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
					$link='accept_reject_debit_noteforgeneralitems';
					$polink="generalpo";
				}
				
				
				if($restyui1->qcstatus==0)
				{
			$action="<a href='".page_url."Store/".$link."/".$restyui1->id."/".$restyui1->pono."'><span class='btn btn-warning btn-xs'>UPDATE</span></a>";
				}else{
					$action="<span class='btn btn-warning btn-xs'>QC DONE</span>";
				}
				
				$uname=$this->getunit($restyui1->unit);
			$scheduler_data[] = array('sr_no'=>$i,
			'item'=>$itemname,
			'fincode'=>$fincode,
			'specialization'=>$specification,
			'qty'=>floatval($restyui1->recqty)." ".$uname,
			'pono'=>"<a href='".page_url."Store/".$polink."/".$restyui1->pono."' target='_blank'>".$restyui1->pono."</a>",
			'action'=>$action);
				
			$i++;
			}
			}
			
			
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
		
		
	}
	
	
	function autopr()
	{
	$this->load->view('store/autoprrequest');
	}
	
	function autoprlist()
	{
	$scheduler_data=array();
		$resty=$this->db->select('id,min_stock,maxstock,current_stock,part,category_id,fincode,specification,unit')->from('machine_parts_with_picture')->where('current_stock<min_stock')->where('min_stock>','0')->where('maxstock>','0')->group_by('part','ASC')->get();
		if($resty->num_rows()>0)
		{
			$i=1;
			foreach($resty->result() as $restyui1)
			{
			    $pror=$this->checkifpoisraised($restyui1->id);
			    if($pror==0)
			    {
				$prqty=$restyui1->maxstock-$restyui1->current_stock;
				$scheduler_data[] = array('sr_no'=>$i,
				'actio'=>"<input type='checkbox' class='checkitem' name='itemid[]' value='".$restyui1->id."'>",
				'item'=>$restyui1->part,
				'fincode'=>$restyui1->fincode,
				'specialization'=>$restyui1->specification,
				'qty'=>"<input type='text' class='form-control' onkeypress='return isNumberKey(event);' style='width:40%' name='qty".$restyui1->id."' value='".$prqty."'><input type='hidden' name='unit".$restyui1->id."' value='".$restyui1->unit."'><input type='hidden' name='currstock".$restyui1->id."' value='".$restyui1->current_stock."'>");
			    
				$i++;
			}
			}
			
		}
		
		
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
		
		
		
		
	}



function getunit($unitid)
{

$query = $this->db->select('b.shortname')->from('units b')->where('b.id',$unitid)->get();
if($query->num_rows()>0)
{
	
	foreach($query->result() as $query1);
	
		$unit=strtoupper($query1->shortname);
	return $unit;
}else{
	
	$unit='';
	return $unit;
}
	
	
}


function generalmrnqcrequest()
	{
		
			$scheduler_data = array();
		$rest=$this->db->select('a.*,b.part,b.specification,b.fincode')->from('mrn a')->join('machine_parts_with_picture b','a.itemid=b.id')->order_by('a.qcstatus','ASC')->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				
				if($restyui1->qcstatus==0)
				{
			$action="<span class='btn btn-warning btn-xs'>UPDATE</span>";
				}else{
					$action="<span class='btn btn-warning btn-xs'>QC DONE</span>";
				}
			$scheduler_data[] = array('sr_no'=>$i,
			'item'=>$restyui1->part,
			'fincode'=>$restyui1->fincode,
			'specialization'=>$restyui1->specification,
			'qty'=>floatval($restyui1->recqty),
			'pono'=>"<a href='".page_url."Store/po/".$restyui1->pono."' target='_blank'>".$restyui1->pono."</a>",
			'action'=>$action);
				
			$i++;
			}
			}
			
			
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
		
		
	}
	
	
	
	function pendingpoforapproval()
	{
	   	$this->load->view('store/pendingpoforapproval');
		
		
	}
	
	function pendingpolistforapprovalold01sept2020()
{

			$scheduler_data = array();
$genpo=base64_decode($this->uri->segment(3));
$genp=explode(',',$genpo);
		$rest=$this->db->select('a.jobcard,a.potype,a.source,a.sourceid,a.addedOn,a.pono,e.first_name,e.last_name,a.itemid,e.department_id')->from('purchase_order a')->join('system_users e','e.user_id=a.addedBy','left')->where('a.approved','0')->order_by('a.addedOn','DESC')->group_by('a.pono')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				if(in_array($restyui1->pono,$genp))
				{
					$bc="red";
				}else{
					$bc='';
				}
				
					$jobcard='';

					if($restyui1->source==1)
					{
					$job=$this->db->select('job_card_no')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
					if($job->num_rows()>0)
					{
					foreach($job->result() as $job1);
					$jobcard='Jobcard-'.$job1->job_card_no;
					}else{
					$jobcard='';
					}
					
					}else if($restyui1->source==2)
					{
					/** Intend **/
					$jobcard='INDENT- IND'.$restyui1->sourceid;
					
					}else if($restyui1->source==3)
					{
					/** Intend **/
					$jobcard='AUTO PR';
					
					}					

if($restyui1->potype=='0'){
	$polink="po";
	$mrnlink="mrn";
}else{
	$polink="generalpo";
	$mrnlink="generalmrn";
}

$html='<a href="javascript:;" onclick="approvepo('."'".$restyui1->pono."'".');" class="btn btn-warning btn-xs">APPROVE PO</a>';

			$scheduler_data[] = array('sr_no'=>$i,
			'prno'=>"<a href='".page_url."Store/".$polink."/".$restyui1->pono."' target='_blank' style='color:".$bc."'>".$restyui1->pono."</a>",
			'jobcardno'=>$jobcard,
			'createdby'=>ucfirst($restyui1->first_name." ".$restyui1->last_name),
			'createdon'=>date('d-M-Y g:i A',strtotime($restyui1->addedOn)),
			'markrecvd'=>$html);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
	



}
	
	
		function pendingpolistforapproval()
{

    $scheduler_data = array();
    $genpo=base64_decode($this->uri->segment(3));
    $genp=explode(',',$genpo);
		$rest=$this->db->select('a.pricechange,a.originalprice,a.id,a.prno,a.jobcard,a.source,a.sourceid,a.potype,a.addedOn,a.pono,e.first_name,e.last_name,a.itemid,e.department_id, v.name, v.address')->from('purchase_order a')->join('system_users e','e.user_id=a.addedBy','left')->join('vendors v','a.vendor=v.id','left')->where('a.approved','0')->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				if(in_array($restyui1->pono,$genp))
				{
					$bc="red";
				}else{
					$bc='';
				}
				
					$jobcard='';

					if($restyui1->source==1)
					{
					$job=$this->db->select('job_card_no')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
					if($job->num_rows()>0)
					{
					foreach($job->result() as $job1);
					$jobcard='Jobcard-'.$job1->job_card_no;
					}else{
					$jobcard='';
					}
					$polink="po";
					$mrnlink="mrn";
					}else if($restyui1->source==2)
					{
					/** Intend **/
					$jobcard='INDENT- IND'.$restyui1->sourceid;
					$polink="generalpo";
					$mrnlink="generalmrn";
					}else if($restyui1->source==3)
					{
					/** Intend **/
					$jobcard='AUTO PR';
					$polink="po";
					$mrnlink="mrn";
					}					


				
				if($restyui1->potype=='0')
				{
					$polink="po";
					$mrnlink="mrn";
				}else
				{
					$polink="generalpo";
					$mrnlink="generalmrn";
				}

$html='<a href="javascript:;" onclick="approvepo('."'".$restyui1->pono."'".');" class="btn btn-warning btn-xs">APPROVE PO</a>';
$approved = '<input type="checkbox" name="approvedrecord[]" value="'.$restyui1->id.'" onchange="markasapproved('."'".$restyui1->id."'".','."'".$restyui1->pono."'".');">';
$html1='<a href="javascript:;" class="btn btn-warning btn-xs" onclick="showpopup('."'".$restyui1->pono."'".');">REJECT PO</a>';

$htm="";


if($restyui1->potype=='0'){
    
					$indenttype = "Machine Related Items";
				$rest123=$this->db->select('b.part as item_name,a.qty,a.price,a.unit,a.itemid')->from('purchase_order a')->join('machine_parts_with_picture b','a.itemid=b.id')->where('b.id',$restyui1->itemid)->get();	
				}else{
					$indenttype = "General Items";
				$rest123=$this->db->select('b.item_name,a.qty,a.unit,a.price, a.itemid')->from('purchase_order a')->join('house_keeping_items b','a.itemid=b.id')->where('b.id',$restyui1->itemid)->get();	
				}
				
				
				if($rest123->num_rows()>0)
				{
					
					foreach($rest123->result() as $rest1231)
					$price = $rest1231->price;
					$oldprice="";
					$query = $this->db->select('price')->from('purchase_order')->where('itemid',$rest1231->itemid)->where('approved','1')->limit('1')->order_by('id','desc')->get();
					if($query->num_rows()>0){
						foreach($query->result() as $oldata);
						$oldprice = $oldata->price;
						$diff = abs($price-$oldprice);
					}else{
					    $oldprice="New Item";
					    $diff="";
					}
					$itemname = strtoupper($rest1231->item_name);
					$qty = $rest1231->qty;
					$price = $rest1231->price;
			}

            if($restyui1->pricechange==1)
            {
            $pri="<span style='color:red;font-weight:bold;'>".$price."</span>" ;
            }else
            {
            $pri=$price;
            }
            
          

			$scheduler_data[] = array('sr_no'=>$i,
			'prno'=>"<a href='".page_url."Store/".$polink."/".$restyui1->pono."' target='_blank' style='color:".$bc."'>".$restyui1->pono."</a>",
			'jobcardno'=>$jobcard,
			'createdby'=>ucfirst($restyui1->first_name." ".$restyui1->last_name),
			'itemname'=>$itemname,
			'qty'=>$qty,
			'price'=>$pri,
			'oldprice'=>$oldprice,
			'vendor'=>$restyui1->name,
			'diff'=>$diff,
			'prnumber'=>$restyui1->prno,
			'createdon'=>date('d-M-Y g:i A',strtotime($restyui1->addedOn)),
			'markrecvd'=>$approved,
			'markrrej'=>$html1);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
}


	function pendingpolistforapproval_old()
{

			$scheduler_data = array();
$genpo=base64_decode($this->uri->segment(3));
$genp=explode(',',$genpo);
		$rest=$this->db->select('a.jobcard,a.source,a.sourceid,a.addedOn,a.pono,e.first_name,e.last_name,a.itemid,e.department_id')->from('purchase_order a')->join('machine_bom b','a.itemid=b.partid','left')->join('system_users e','e.user_id=a.addedBy')->where('a.approved','0')->order_by('a.addedOn','DESC')->group_by('a.pono')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				if(in_array($restyui1->pono,$genp))
				{
					$bc="red";
				}else{
					$bc='';
				}
				
					$jobcard='';

					if($restyui1->source==1)
					{
					$job=$this->db->select('job_card_no')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
					if($job->num_rows()>0)
					{
					foreach($job->result() as $job1);
					$jobcard='Jobcard-'.$job1->job_card_no;
					}else{
					$jobcard='';
					}
					$polink="po";
					$mrnlink="mrn";
					}else if($restyui1->source==2)
					{
					/** Intend **/
					$jobcard='INDENT- IND'.$restyui1->sourceid;
					$polink="generalpo";
					$mrnlink="generalmrn";
					}else if($restyui1->source==3)
					{
					/** Intend **/
					$jobcard='AUTO PR';
					$polink="po";
					$mrnlink="mrn";
					}					


$html='<a href="javascript:;" onclick="approvepo('."'".$restyui1->pono."'".');" class="btn btn-warning btn-xs">APPROVE PO</a>';

			$scheduler_data[] = array('sr_no'=>$i,
			'prno'=>"<a href='".page_url."Store/".$polink."/".$restyui1->pono."' target='_blank' style='color:".$bc."'>".$restyui1->pono."</a>",
			'jobcardno'=>$jobcard,
			'createdby'=>ucfirst($restyui1->first_name." ".$restyui1->last_name),
			'createdon'=>date('d-M-Y g:i A',strtotime($restyui1->addedOn)),
			'markrecvd'=>$html);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
	



}


function gateentry()
{
	
	$this->load->view('store/gateentry');
	
}


function openpoforgateentryoLDDD()
{

			$scheduler_data = array();
$genpo=base64_decode($this->uri->segment(3));
$genp=explode(',',$genpo);
		$rest=$this->db->select('a.*')->from('purchase_order a')->where('a.approved','1')->where('a.gateentrycomplete','0')->where('a.completed','0')->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
$vendorname=$this->getvendorname($restyui1->vendor);
				if($restyui1->potype==0)
				{
					
					$type="Machine Item";
					$itemname=$this->getmachineitemname($restyui1->itemid);
					$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
					if(count($otherdetails)>0)
					{

					$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
					}else{
					$fincode='';
					$specification='';
					}
			
					
				}else if($restyui1->potype==1)
				{
					$type="General Item";
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
					
				}
				

						if($restyui1->potype==0)
						{
							
						if($restyui1->source==1)
						{

							$job=$this->db->select('job_card_no,item_id as machineid')->from('order_instruments')->where('id',$restyui1->jobcardid)->get();
							if($job->num_rows()>0)
							{
							foreach($job->result() as $job1);
							$macid=$job1->machineid;
							}else{
							$macid=0;
							}

						}else 
						{
							$macid=0;
						}

						}else 
						{
							$macid=0;
						}
					

						/** **/
						$prevqty=$this->storemodel->checkifanypreviousqtyisinwarded($restyui1->itemid,$restyui1->pono);

						/** END **/
							

						$restyu=$this->db->select('shortname')->from('units')->where('id',$restyui1->unit)->get();
						if($restyu->num_rows()>0)
						{
						foreach($restyu->result() as $restyu1);
						$unival=$restyu1->shortname;
						}else{
						$unival='';							
						}

	if($prevqty>0)
{
$originalleftqty=$restyui1->qty-$prevqty;
}else
{
$originalleftqty=$restyui1->qty;
}


$html='<a href="'.page_url.'Reporting/vendorwisepoforgateentry/'.$restyui1->vendor.'" class="btn btn-warning btn-xs">Open PO(s)</a>';
$checkbox="<input type='checkbox' name='check[]' class='qccheck' id='checkone".$restyui1->id."' onchange='checkitem(".$restyui1->id.");' value='".$restyui1->id."'><input type='hidden' value='".floatval($originalleftqty)."' name='originalqty".$restyui1->id."' id='originalqty".$restyui1->id."'><input type='hidden' value='".$restyui1->itemid."' name='item".$restyui1->id."' id='item".$restyui1->id."'><input type='hidden' value='".$restyui1->pono."' name='po".$restyui1->id."' id='po".$restyui1->id."'><input type='hidden' value='".$restyui1->vendor."' name='vendor".$restyui1->id."' id='vendor".$restyui1->id."'><input type='hidden' value='".$restyui1->source."' name='source".$restyui1->id."' id='source".$restyui1->id."'><input type='hidden' value='".$restyui1->jobcard."' name='jobcardno".$restyui1->id."' id='jobcardno".$restyui1->id."'><input type='hidden' value='".$macid."' name='instrumentid".$restyui1->id."' id='instrumentid".$restyui1->id."'><input type='hidden' value='".$restyui1->unit."' name='unit".$restyui1->id."' id='unit".$restyui1->id."'><input type='hidden' value='".$restyui1->potype."' name='potype".$restyui1->id."' id='potype".$restyui1->id."'>";
$recvqty="<input type='text' class='form-control formdata".$restyui1->id."' name='recvqty".$restyui1->id."' id='recvqty".$restyui1->id."' style='display:none' placeholder='RECV QTY' value='0'>";
$gateentry="<input type='text' class='form-control formdata".$restyui1->id."' name='gateentry".$restyui1->id."' id='gateentry".$restyui1->id."' placeholder='Gate Entry No.' style='display:none;'>";
$approved='<input type="number" class="form-control formdata'.$restyui1->id.'"  name="approved_qty'.$restyui1->id.'"  id="approved_qty'.$restyui1->id.'" style="display:none;" value="" placeholder="Approved Qty" value="0">';
$reject='<input type="number" class="form-control formdata'.$restyui1->id.'"  name="reject'.$restyui1->id.'"  id="reject'.$restyui1->id.'"  style="display:none;" value=""  placeholder="Reject Qty" value="0">';
$rejectremarks='<textarea class="form-control formdata'.$restyui1->id.'" name="chtype'.$restyui1->id.'" id="chtype'.$restyui1->id.'" style="display:none;" value=""  placeholder="Rejection Remarks"></textarea>';
$rejectfile='<input type="file" class="form-control formdata'.$restyui1->id.'"  name="rejfile'.$restyui1->id.'"  id="rejfile'.$restyui1->id.'"  style="display:none;"  value="" placeholder="Rejection Remarks">';
$save='<input type="submit" class="btn btn-success formdata'.$restyui1->id.'" style="display:none;" >';



			$scheduler_data[] = array('sr_no'=>$checkbox,
			'pono'=>$restyui1->pono,
			'vendorname'=>$vendorname,
			'itemname'=>$itemname,
			'itemtype'=>$type,
			'fincode'=>$fincode,
			'specialization'=>$specification,
			'previousinwarded'=>$prevqty.' '.$unival,
			'reqty'=>$originalleftqty.' '.$unival,
			'recqty'=>$recvqty,
			'gateentry'=>$gateentry,
			'approved'=>$approved,
			'reject'=>$reject,
			'rejremarks'=>$rejectremarks,
			'rejectimage'=>$rejectfile);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);

}



function openpoforgateentryOLDDDBEFORESINGLEPAGEALLMRNQC()
{

			$scheduler_data = array();
$genpo=base64_decode($this->uri->segment(3));
$genp=explode(',',$genpo);
		$rest=$this->db->select('a.pono,a.vendor,b.name')->from('purchase_order a')->join('vendors b','a.vendor=b.id')->where('a.approved','1')->where('a.gateentrycomplete','0')->where('a.completed','0')->order_by('a.addedOn','DESC')->group_by('a.vendor')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				
				$restyui=$this->db->select('id')->from('purchase_order')->where('vendor',$restyui1->vendor)->where('approved','1')->where('gateentrycomplete','0')->where('completed','0')->group_by('pono')->get();
				$openpo=$restyui->num_rows();
				
$html='<a href="'.page_url.'Reporting/vendorwisepoforgateentry/'.$restyui1->vendor.'" class="btn btn-warning btn-xs">Open PO(s)</a>';

			$scheduler_data[] = array('sr_no'=>$i,
			'vendorname'=>$restyui1->name,
			'openpo'=>$openpo,
			'markrecvd'=>$html);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
	



}



function openpoforgateentryOldddddddd()
{

			$scheduler_data = array();
$genpo=base64_decode($this->uri->segment(3));
$genp=explode(',',$genpo);
		$rest=$this->db->select('a.pono,a.vendor,b.name')->from('purchase_order a')->join('vendors b','a.vendor=b.id')->where('a.approved','1')->where('a.gateentrycomplete','0')->where('a.completed','0')->order_by('a.addedOn','DESC')->group_by('a.vendor')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				
				$restyui=$this->db->select('id')->from('purchase_order')->where('vendor',$restyui1->vendor)->where('approved','1')->where('gateentrycomplete','0')->where('completed','0')->get();
				$openpo=$restyui->num_rows();
				
$html='<a href="'.page_url.'Reporting/vendorwisepoforgateentry/'.$restyui1->vendor.'" class="btn btn-warning btn-xs">Open PO(s)</a>';

			$scheduler_data[] = array('sr_no'=>$i,
			'vendorname'=>$restyui1->name,
			'openpo'=>$openpo,
			'markrecvd'=>$html);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
	



}


function vendorwisepoforgateentry()
{
	
	$this->load->view('store/vendorwisepoforgateentry');
	
}


function vendorpoforgateentry()
{

$id=$this->uri->segment(3);
			$scheduler_data = array();
		$rest=$this->db->select('a.pono,a.source')->from('purchase_order a')->where('a.approved','1')->where('a.gateentrycomplete','0')->where('a.completed','0')->where('a.vendor',$id)->group_by('a.pono')->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				$restsysyu=$this->db->select('id')->from('purchase_order')->where('pono',$restyui1->pono)->where('gateentrycomplete','0')->get();
				$itemsno=$restsysyu->num_rows();
				
				if($restyui1->source==2)
				{
					$mrnlink="generalgateentry";
					
				}else{
					
					$mrnlink="gateentry";
				}
$html='<a href="'.page_url.'Store/'.$mrnlink.'/'.$restyui1->pono.'" class="btn btn-warning btn-xs">Create Gate Entry</a>';

			$scheduler_data[] = array('sr_no'=>$i,
			'pono'=>$restyui1->pono,
			'items'=>$itemsno,
			'markrecvd'=>$html);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
	



}


function vendorpoforgateentryOlddff()
{

$id=$this->uri->segment(3);
			$scheduler_data = array();
		$rest=$this->db->select('a.pono,a.source')->from('purchase_order a')->where('a.approved','1')->where('a.gateentrycomplete','0')->where('a.completed','0')->where('a.vendor',$id)->group_by('a.pono')->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				$restsysyu=$this->db->select('id')->from('purchase_order')->where('pono',$restyui1->pono)->get();
				$itemsno=$restsysyu->num_rows();
				
				if($restyui1->source==2)
				{
					$mrnlink="generalgateentry";
					
				}else{
					
					$mrnlink="gateentry";
				}
$html='<a href="'.page_url.'Store/'.$mrnlink.'/'.$restyui1->pono.'" class="btn btn-warning btn-xs">Create Gate Entry</a>';

			$scheduler_data[] = array('sr_no'=>$i,
			'pono'=>$restyui1->pono,
			'items'=>$itemsno,
			'markrecvd'=>$html);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
	



}

function pendinggateentrymrn()
{
 
	 $this->load->view('store/pendinggateentrymrn'); 
}

function pendinggateentryformrnlist()
{
			$scheduler_data = array();
			$rest=$this->db->select('a.*')->from('mrn a')->where('a.mrndone','0')->group_by('a.gateentryno')->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				$rest12345=$this->db->select('a.id')->from('mrn a')->where('a.gateentryno',$restyui1->gateentryno)->get();
				
			$html='<a href="'.page_url.'Reporting/pendingmrnrequest/'.trim($restyui1->gateentryno).'" class="btn btn-warning btn-xs">Proceed for MRN</a>';

			$scheduler_data[] = array('sr_no'=>$i,
			'gateentry'=>$restyui1->gateentryno,
			'pono'=>$restyui1->pono,
			'item'=>$rest12345->num_rows(),
			'gateentryon'=>date('d-m-Y g:i A',strtotime($restyui1->gateentryOn)),
			'mrnrecieve'=>$html);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
}
function pendinggateentryformrnlistold()
{
			$scheduler_data = array();
			$rest=$this->db->select('a.*')->from('mrn a')->where('a.mrndone','0')->group_by('a.gateentryno')->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				$rest12345=$this->db->select('a.id')->from('mrn a')->where('a.gateentryno',$restyui1->gateentryno)->get();
				
			$html='<a href="'.page_url.'Reporting/pendingmrnrequest/'.trim($restyui1->gateentryno).'" class="btn btn-warning btn-xs">Proceed for MRN</a>';

			$scheduler_data[] = array('sr_no'=>$i,
			'gateentry'=>$restyui1->gateentryno,
			'pono'=>$restyui1->pono,
			'item'=>$rest12345->num_rows(),
			'gateentryon'=>date('d-m-Y g:i A',strtotime($restyui1->gateentryOn)),
			'mrnrecieve'=>$html);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
}


function pendinggateentryformrnlistOlddhddhd()
{
			$scheduler_data = array();
			$rest=$this->db->select('a.*')->from('mrn a')->where('a.mrndone','0')->group_by('a.gateentryno')->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				$rest12345=$this->db->select('a.id')->from('mrn a')->where('a.gateentryno',$restyui1->gateentryno)->get();
				
			$html='<a href="'.page_url.'Reporting/pendingmrnrequest/'.trim($restyui1->gateentryno).'" class="btn btn-warning btn-xs">Proceed for MRN</a>';

			$scheduler_data[] = array('sr_no'=>$i,
			'gateentry'=>$restyui1->gateentryno,
			'pono'=>$restyui1->pono,
			'item'=>$rest12345->num_rows(),
			'gateentryon'=>date('d-m-Y g:i A',strtotime($restyui1->gateentryOn)),
			'mrnrecieve'=>$html);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
}


function pendingmrnrequest()
{
	$this->load->view('store/pendingmrn');
	
	
}


function pendingmrnlist()
{
		$gateno=$this->uri->segment(3);
	
			$scheduler_data = array();
			$rest=$this->db->select('a.*')->from('mrn a')->where('a.gateentryno',$gateno)->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			
			foreach($rest->result() as $restyui1)
			{
				$source=$this->getposource($restyui1->pono);
				$unitna=$this->getunit($restyui1->unit);
				
				if($source==1 || $source==3)
				{
				$itemname=$this->getmachineitemname($restyui1->itemid);
				}else{
					$itemname=$this->getgeneralitemname($restyui1->itemid);
				}
				
				if($restyui1->mrndone==0)
				{
			$html='<a href="javascript:;" onclick="markasrecieved('."'".$restyui1->id."'".','."'".strtoupper($restyui1->pono)."'".','."'".$itemname."'".')" class="btn btn-warning btn-xs">Mark Recieved</a>';
				}else{
					
					$html='MRN DONE ON '.date('d-m-Y g:i A',strtotime($restyui1->mrndoneOn));
				}

			$scheduler_data[] = array('sr_no'=>$i,
			'gateentry'=>$restyui1->gateentryno,
			'pono'=>$restyui1->pono,
			'item'=>$itemname,
			'recqty'=>floatval($restyui1->recqty)." ".$unitna,
			'gateentryon'=>date('d-m-Y g:i A',strtotime($restyui1->gateentryOn)),
			'mrnrecieve'=>$html);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
	
}

function pendingmrnlistOldd()
{
		$gateno=$this->uri->segment(3);
			$scheduler_data = array();
			$rest=$this->db->select('a.*')->from('mrn a')->where('a.gateentryno',$gateno)->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
			    $source=$this->getposource($restyui1->pono);
				$unitna=$this->getunit($restyui1->unit);
				
				if($restyui1->source==1 || $restyui1->source==3)
				{
				$itemname=$this->getmachineitemname($restyui1->itemid);
				}else{
					$itemname=$this->getgeneralitemname($restyui1->itemid);
				}
				
				if($restyui1->mrndone==0)
				{
			$html='<a href="javascript:;" onclick="markasrecieved('."'".$restyui1->id."'".','."'".strtoupper($restyui1->pono)."'".','."'".$itemname."'".')" class="btn btn-warning btn-xs">Mark Recieved</a>';
				}else{
					
					$html='MRN DONE ON '.date('d-m-Y g:i A',strtotime($restyui1->mrndoneOn));
				}

			$scheduler_data[] = array('sr_no'=>$i,
			'gateentry'=>$restyui1->gateentryno,
			'pono'=>$restyui1->pono,
			'item'=>$itemname,
			'recqty'=>floatval($restyui1->recqty)." ".$unitna,
			'gateentryon'=>date('d-m-Y g:i A',strtotime($restyui1->gateentryOn)),
			'mrnrecieve'=>$html);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
	
}


function getmachineitemname($itemid)
{
	$iname='';
	$resty=$this->db->select('part')->from('machine_parts_with_picture')->where('id',$itemid)->get();
	if($resty->num_rows()>0)
	{
		foreach($resty->result() as $resty1);
		
		$iname=$resty1->part;
	}
	
	return $iname;
	
}


function getgeneralitemname($itemid)
{
	$iname='';
	$resty=$this->db->select('item_name')->from('house_keeping_items')->where('id',$itemid)->get();
	if($resty->num_rows()>0)
	{
		foreach($resty->result() as $resty1);
		
		$iname=$resty1->item_name;
	}
	
	return $iname;
	
}

function getmachineotherdetailsolf($itemid)
{
	
	$iname=array();
	$resty=$this->db->select('fincode,specification')->from('machine_parts_with_picture')->where('id',$itemid)->get();
	if($resty->num_rows()>0)
	{
		foreach($resty->result() as $resty1);
		$iname['fincode']=$resty1->fincode;
		$iname['specialization']=$resty1->specification;
	}
	
	return $iname;
	
}


function getmachineotherdetails($itemid)
{
	
	$iname=array();
	$resty=$this->db->select('a.size_in_mm,a.material,a.conversion_unit,a.conversion_weight,a.fincode,a.specification,b.rack_location,a.part,a.unit')->from('machine_parts_with_picture a')->join('store_rack_location b','a.location_id=b.id')->where('a.id',$itemid)->get();
	if($resty->num_rows()>0)
	{
		foreach($resty->result() as $resty1);
		$iname['name']=$resty1->part;
		$iname['fincode']=$resty1->fincode;
		$iname['specialization']=$resty1->specification;
		$iname['rack_location']=$resty1->rack_location;
		$iname['unit']=$resty1->unit;
		$iname['conunit']=$resty1->conversion_unit;
		$iname['conweight']=$resty1->conversion_weight;
		$iname['size']=$resty1->size_in_mm;
		$iname['material']=$resty1->material;
		$iname['conweight']=$resty1->conversion_weight;
	}
	
	return $iname;
	
}


public function accept_reject_debit_note(){
		$this->load->view('store/accept_reject_debit_note');
	}


	public function update_accept_reject_qty_po(){
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
           $table = "mrn_history";
			if(isset($_REQUEST['itemid'])){	
					$tags1=count($_REQUEST['itemid']);
					if($tags1>0)
					{
					$itemid=$_REQUEST['itemid'];
					$recordid = $_REQUEST['recordid'];
					$approved_qty = $_REQUEST['approved_qty'];
					$debit_note_qty = $_REQUEST['debit_note_qty'];
					$reject_qty = $_REQUEST['reject_qty'];
					for($x=0;$x<$tags1;$x++){
					if($itemid[$x]!='')
						{
						 
							$data=array('itemid'=>$itemid[$x],
							'po_no'=>$this->uri->segment(4),
							'record_id'=>$recordid[$x],
							'accept_qty'=>$approved_qty[$x],
							'debit_qty'=>$debit_note_qty[$x],
							'reject_qty'=>$reject_qty[$x],
							'added_on'=>$added_time,
							'added_by'=>$user_id);
							$this->db->insert($table,$data);
						}
					}
					}
					}
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'Reporting/mrnqc');
		
		
	}
public function debit_note(){
	
		$this->load->view('store/debit_note');
	
}

public function indent_vs_pr_report(){
	
		$this->load->view('store/indent-vs-pr-report');
	
}

function indent_vs_pr_report_list()
{

        $reportingdata = array();
		$rest = $this->db->select('a.id, a.indendno,a.prefix, a.itemid, a.qty, a.unit, a.addedOn, a.addedBy, a.approvalstatus, c.first_name, c.last_name')->from('intend_request a')->join('purchase_request b','b.sourceid=a.indendno','left')->join('system_users c','a.addedBy=c.user_id','left')->where('b.source','2')->where('a.approvalstatus','0')->group_by('a.indendno')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				
			$html = "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center;'>ITEM NAME</th><th style='padding:2px 2px 2px 2px; text-align:center;'>QTY</th><th style='padding:2px 2px 2px 2px; text-align:center;'>UNIT</th></tr>";
			$instrumentsss = array();
			$query = $this->db->select('a.itemid ,a.indendno,b.id, b.part,a.qty, a.unit, c.shortname')->from('intend_request a')->join('machine_parts_with_picture b','a.itemid=b.id','left')->join('units c','a.unit=c.id','left')->where('a.indendno',$restyui1->indendno)->get();
			foreach($query->result() as $record){
				
				
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->part)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->qty)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->shortname)."</td>";
				$html.="</tr>";
				
			}
			$html.="</table>";
			$addedon = $restyui1->addedOn;
			$reportingdata[] = array('sr_no'=>$i,
			'addedon'=>$addedon,
			'indentno'=>$restyui1->indendno,
			'item_detail'=>$html,
			'added_by'=>$restyui1->first_name." ".$restyui1->last_name);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($reportingdata),
			"iTotalDisplayRecords" => count($reportingdata),
			"aaData"=>$reportingdata);
			echo json_encode($results);
	
	


}

public function pr_vs_po_report(){
	 
$this->load->view('store/pr-vs-po-report'); 
		
	
}


public function pr_vs_po_list()
	{
		$type=$this->uri->segment(3);
		$i=1;
		$department_data = array();
		$this->db->select('a.addedOn,a.id,a.prno,a.masterid,a.prraisereason,a.type,a.sourceid,a.source,a.jobcardid,d.part as machine_part,d.fincode,d.size_in_mm,d.specification,d.current_stock,e.first_name,e.last_name,a.itemid,a.qty,e.department_id,u.shortname')
					  ->from('purchase_request a')
					  ->join('machine_parts_with_picture d','d.id=a.masterid')
					  ->join('system_users e','e.user_id=a.addedBy')
					  ->join('units u','u.id=a.unit')
					  ->where('a.approvalstatus','0')
					  ->where('a.closed','0');
					  if($type<>'')
					{

						$this->db->where('a.source',$type);

					}
					  $query =$this->db->order_by('a.id','DESC')->get();

		foreach($query->result() as $row) {
						$jobcard='';

							if($row->source == 1) {
							$job = $this->db->select('job_card_no,item_id as machineid')->from('order_instruments')
										    ->where('id',$row->jobcardid)
										    ->get();

							if($job->num_rows() > 0) {

							foreach($job->result() as $job1);
							$jobcard='Jobcard-'.$job1->job_card_no;
							$macid=$job1->machineid;
							} else {
							$jobcard='';
							$macid=0;
							}
							} else if ($row->source == 2) {
							$jobcard='INDENT- IND'.$row->sourceid;
							$macid=0;
							} else if ($row->source == 3) {
							$jobcard='IMS AUTO PR';
							$macid=0;
							}

							$puraddedon=date('Y-m-d',strtotime($row->addedOn));

							$start = strtotime($puraddedon);
							$end = strtotime(date('Y-m-d'));

							$days_between = ceil(abs($end - $start) / 86400);
		
				
			$department_data[] = array('sr_no'=>$i,
									   'pr_no'=>$row->prno,
									   'source' => $jobcard,
									   'indenter_ref'=>ucwords($row->first_name)." ".ucwords($row->last_name),
									   'item' => strtoupper($row->machine_part),
									   'fincode' => $row->fincode,
									   'specification' => $row->specification,
										'qty'=>floatval($row->qty)." ".$row->shortname,
										'pendsince'=>$days_between." Day"
										);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($department_data),
			"iTotalDisplayRecords" => count($department_data),
			"aaData"=>$department_data);
			
		echo json_encode($results);
	}



public function pr_vs_po() {
	$this->load->view('store/pr_vs_po_report');
}




public function pr_vs_po_listOLddd()
	{
		$i=1;
		$department_data = array();
		$query = $this->db->select('a.id,a.prno,a.masterid,a.prraisereason,a.type,a.sourceid,a.source,a.jobcardid,d.part as machine_part,d.fincode,d.size_in_mm,d.specification,d.current_stock,e.first_name,e.last_name,a.itemid,a.qty,e.department_id,u.shortname')
					  ->from('purchase_request a')
					  ->join('machine_parts_with_picture d','d.id=a.masterid')
					  ->join('system_users e','e.user_id=a.addedBy')
					  ->join('units u','u.id=a.unit')
					  ->where('a.approvalstatus','0')
					  ->where('a.closed','0')
					  ->order_by('a.id','DESC')
					  ->get();

		foreach($query->result() as $row) {
						$jobcard='';

							if($row->source == 1) {
							$job = $this->db->select('job_card_no,item_id as machineid')			->from('order_instruments')
										    ->where('id',$row->jobcardid)
										    ->get();

							if($job->num_rows() > 0) {

							foreach($job->result() as $job1);
							$jobcard='Jobcard-'.$job1->job_card_no;
							$macid=$job1->machineid;
							} else {
							$jobcard='';
							$macid=0;
							}
							} else if ($row->source == 2) {
							$jobcard='INDENT- IND'.$row->sourceid;
							$macid=0;
							} else if ($row->source == 3) {
							$jobcard='AUTO PR';
							$macid=0;
							}
		
				
			$department_data[] = array('sr_no'=>$i,
									   'pr_no'=>$row->prno,
									   'source' => $jobcard,
									   'indenter_ref'=>ucwords($row->first_name)." ".ucwords($row->last_name),
									   'item' => strtoupper($row->machine_part),
									   'fincode' => $row->fincode,
									   'specification' => $row->specification,
										'qty'=>floatval($row->qty)." ".$row->shortname
										);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($department_data),
			"iTotalDisplayRecords" => count($department_data),
			"aaData"=>$department_data);
			
		echo json_encode($results);
	}
	
	
function pr_vs_po_report_listOlddd()
{

        $reportingdata = array();
		$rest = $this->db->select('a.sourceid,a.source,a.id, a.itemid, a.prno as purno, a.jobcardid,a.qty, a.unit, a.addedBy,a.addedOn, b.prno, c.first_name, c.last_name')->from('purchase_request a')->join('purchase_order b','b.prno=a.prno','left')->join('system_users c','a.addedBy=c.user_id','left')->where('a.approvalstatus','0')->group_by('a.prno')->get();
		
			if($rest->num_rows()>0)
			{
			$i=1;
		
			foreach($rest->result() as $restyui1)
			{
			
			if($restyui1->source==1)
			{
			
			$jobcard=$this->getjobcardno($restyui1->jobcardid);
			$sou="JOBCARD - ".$jobcard;
			}else if($restyui1->source==2)
			{
			$sou="INDENT - IND-".$restyui1->sourceid;
			}else if($restyui1->source==3)
			{
			$sou="IMS";
			}else
			{
			$sou='';
			}
			
				
			$html = "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center'>ITEM NAME</th><th style='padding:2px 2px 2px 2px; text-align:center'>QTY</th><th style='padding:2px 2px 2px 2px; text-align:center'>UNIT</th><th style='padding:2px 2px 2px 2px; text-align:center'>STATUS</th></tr>";
			$instrumentsss = array();
			$query = $this->db->select('a.itemid ,a.prno,b.id, b.part,a.qty, a.unit, c.shortname')->from('purchase_request a')->join('machine_parts_with_picture b','a.itemid=b.id','left')->join('units c','a.unit=c.id','left')->where('a.prno',$restyui1->purno)->get();
			foreach($query->result() as $record){
				
				
				$prnooo=$this->storemodel->getprstatusitemwise($restyui1->purno,$record->itemid);
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->part)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->qty)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->shortname)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$prnooo."</td>";
				$html.="</tr>";
				
			}
			$html.="</table>";
			$addedon = $restyui1->addedOn;
			$reportingdata[] = array('sr_no'=>$i,
			'addedon'=>$addedon,
			'prno'=>$restyui1->purno,
			'source'=>$sou,
			'item_detail'=>$html,
		
			'added_by'=>$restyui1->first_name." ".$restyui1->last_name);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($reportingdata),
			"iTotalDisplayRecords" => count($reportingdata),
			"aaData"=>$reportingdata);
			echo json_encode($results);
	
	


}

public function po_vs_delivery(){
	
		$this->load->view('store/po-vs-delivery-report');
	
}

function po_vs_delivery_report_list()
{

        $reportingdata = array();
		$rest = $this->db->select('a.pono,a.addedOn, b.first_name, b.last_name')->from('purchase_order a')->join('system_users b','a.addedBy=b.user_id','left')->where('a.gateentrycomplete','0')->group_by('a.pono')->get();
		
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				
			$html = "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center'>ITEM NAME</th><th style='padding:2px 2px 2px 2px; text-align:center'>QTY</th><th style='padding:2px 2px 2px 2px; text-align:center'>PRICE</th><th style='padding:2px 2px 2px 2px; text-align:center'>UNIT</th>
			<th style='padding:2px 2px 2px 2px; text-align:center'>Supplier Name</th></tr>";
			$instrumentsss = array();
			$query = $this->db->select('a.itemid ,a.pono,a.price,b.id, b.part,a.qty, a.unit, c.shortname,a.vendor, d.name')->from('purchase_order a')->join('machine_parts_with_picture b','a.itemid=b.id','left')->join('units c','a.unit=c.id','left')->join('vendors d','a.vendor=d.id','left')->where('a.pono',$restyui1->pono)->get();
			foreach($query->result() as $record){
				
				
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->part)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->qty)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->price)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->shortname)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->name)."</td>";
				$html.="</tr>";
				
			}
			$html.="</table>";
			$addedon = $restyui1->addedOn;
			$reportingdata[] = array('sr_no'=>$i,
			'addedon'=>$addedon,
			'prno'=>$restyui1->pono,
			'item_detail'=>$html,
			'added_by'=>$restyui1->first_name." ".$restyui1->last_name);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($reportingdata),
			"iTotalDisplayRecords" => count($reportingdata),
			"aaData"=>$reportingdata);
			echo json_encode($results);
}


function qcdonereport()
{
	
	$this->load->view('store/mrnqcreport');
}


function mrnqccompleted()
	{
		
			$scheduler_data = array();
		$rest=$this->db->select('a.*,a.recqty as totqty,b.source,b.potype')->from('mrn a')->join('purchase_order b','a.pono=b.pono')->where('a.mrndone','1')->where('a.qcstatus','1')->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
					if($restyui1->potype==0)
					{
                        $itemname=$this->getmachineitemname($restyui1->itemid);
                        $otherdetails=$this->getmachineotherdetails($restyui1->itemid);
                        if(count($otherdetails)>0)
                        {
                        
                        $fincode=$otherdetails['fincode'];
                        $specification=$otherdetails['specialization'];
                        }else{
                        $fincode='';
                        $specification='';
                        }
                        
                        $link="po";
					}else
					{

					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
					 $link="generalpo";
					}

					
				
				
				$medata=$this->getmrnhistorydata($restyui1->itemid,$restyui1->pono,$restyui1->id);
	
				if(count($medata)>0)
				{
					$app=$medata['app'];
					$debit=$medata['debit'];
					$replace=$medata['replace'];
					$debitno=$medata['dbno'];
					
				}else{
					
					$app='';
					$debit='';
					$replace='';
					$debitno='';
				}
			
				
				$uname=$this->getunit($restyui1->unit);
			$scheduler_data[] = array('sr_no'=>$i,
			'item'=>$itemname,
			'fincode'=>$fincode,
			'specialization'=>$specification,
			'qty'=>floatval($restyui1->totqty)." ".$uname,
			'pono'=>"<a href='".page_url."Store/".$link."/".$restyui1->pono."' target='_blank'>".$restyui1->pono."</a>",
			'app'=>$app." ".$uname,
			'replaced'=>$replace." ".$uname,
			'db'=>$debitno,
			'challan'=>'');
				
			$i++;
			}
			}
			
			
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
		
		
	}
	
	
function mrnqccompletedold04sept2020()
	{
		
			$scheduler_data = array();
		$rest=$this->db->select('a.*,sum(a.recqty) as totqty,b.source')->from('mrn a')->join('purchase_order b','a.pono=b.pono')->where('mrndone','1')->where('qcstatus','1')->group_by('a.itemid')->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				
				if($restyui1->source==1 || $restyui1->source==3)
				{
				$itemname=$this->getmachineitemname($restyui1->itemid);
				$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
				if(count($otherdetails)>0)
				{
			
				$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
				}else{
					$fincode='';
					$specification='';
				}
				}else{
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
				}
				
				
				$medata=$this->getmrnhistorydata($restyui1->itemid,$restyui1->pono,$restyui1->id);
	
				if(count($medata)>0)
				{
					$app=$medata['app'];
					$debit=$medata['debit'];
					$replace=$medata['replace'];
					$debitno=$medata['dbno'];
					
				}else{
					
					$app='';
					$debit='';
					$replace='';
					$debitno='';
				}
			
				
				$uname=$this->getunit($restyui1->unit);
			$scheduler_data[] = array('sr_no'=>$i,
			'item'=>$itemname,
			'fincode'=>$fincode,
			'specialization'=>$specification,
			'qty'=>floatval($restyui1->totqty)." ".$uname,
			'pono'=>"<a href='".page_url."Store/po/".$restyui1->pono."' target='_blank'>".$restyui1->pono."</a>",
			'app'=>$app." ".$uname,
			'rejected'=>$debit." ".$uname,
			'replaced'=>$replace." ".$uname,
			'db'=>$debitno,
			'challan'=>'');
				
			$i++;
			}
			}
			
			
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
		
		
	}
	
	function getmrnhistorydata($itemid,$pono,$id)
	{
		$menhist=array();
		
		$restyu34=$this->db->select('accept_qty,debit_qty,reject_qty')->from('mrn_history')->where('po_no',$pono)->where('itemid',$itemid)->where('record_id',$id)->get();
		if($restyu34->num_rows()>0)
		{
			foreach($restyu34->result() as $restyu341);
			$menhist['app']=$restyu341->accept_qty;
			$menhist['debit']=$restyu341->debit_qty;
			$menhist['replace']=$restyu341->reject_qty;
			
			if($restyu341->debit_qty>0)
			{
				$dbnos=$this->getdebinoteno($itemid,$pono);
				$menhist['dbno']=$dbnos;
			}else{
				
				$menhist['dbno']='';
			}
		
		}
		
		return $menhist;
		
	}
	
	
	function getdebinoteno($itemid,$pono)
	{
		$dbno='';
		$restyu34=$this->db->select('dbno')->from('debitnote')->where('pono',$pono)->where('itemid',$itemid)->get();
		if($restyu34->num_rows()>0)
		{
		foreach($restyu34->result() as $restyu3412344);
		
		$dbno="<a href='".page_url."Store/debit_note/".$restyu3412344->dbno."' target='_blank'>".$restyu3412344->dbno."</a>";
		
		}
		return $dbno;
		
	}
	
	
	
	function debitnote()
	{
		
		$this->load->view('store/debitnoterequest');
		
	}
	
	function debitnoterequest()
	{
		
		$scheduler_data = array();
		$rest=$this->db->select('a.*,b.po_no,b.itemid,b.reject_qty,c.source,d.name,c.unit,c.potype')->from('debitnote a')->join('mrn_history b','a.mrnhistoryid=b.id','left')->join('purchase_order c','b.po_no=c.pono','left')->join('vendors d','c.vendor=d.id','left')->order_by('a.approved','ASC')->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				
				if($restyui1->potype==0)
				{
				$itemname=$this->getmachineitemname($restyui1->itemid);
				$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
				if(count($otherdetails)>0)
				{
			
				$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
				}else{
					$fincode='';
					$specification='';
				}
				$link="po";
				}else{
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
						$link="generalpo";
				}
				
				
				if($restyui1->approved==0)
				{
					$edit="<a href='javascript:;' onclick='create(".$restyui1->id.");'><span class='btn btn-xs btn-warning'>Generate Debit Note</span></a>";
				}else{
					
					$edit="<a href='".page_url."Store/debit_note/".$restyui1->dbno."' target='_blank'>".$restyui1->dbno."</a>";
				}
				
				
				$uname=$this->getunit($restyui1->unit);
			$scheduler_data[] = array('sr_no'=>$i,
			'partyname'=>$restyui1->name,
			'ponumber'=>"<a href='".page_url."Store/".$link."/".$restyui1->po_no."' target='_blank'>".$restyui1->po_no."</a>",
			'item'=>$itemname,
			'fincode'=>$fincode,
			'specialization'=>$specification,
			'qty'=>floatval($restyui1->reject_qty)." ".$uname,
			'challan'=>$edit);
				
			$i++;
			}
			}
			
			
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
		
		
	}
	

function rejectionchallanreq()
	{
		
		$this->load->view('store/rejectionchallanrequest');
		
	}

function rejectionchallanrequest()
	{
		
		$scheduler_data = array();
		$rest=$this->db->select('a.*,b.itemid,c.source,d.name,c.unit,b.quantity')->from('outwardchallan a')->join('outwardchallan_item b','a.id=b.challanid','left')->join('purchase_order c','a.pono=c.pono','left')->join('vendors d','a.supplier=d.id','left')->order_by('a.approved','ASC')->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				
				if($restyui1->source==1 || $restyui1->source==3)
				{
				$itemname=$this->getmachineitemname($restyui1->itemid);
				$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
				if(count($otherdetails)>0)
				{
			
				$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
				}else{
					$fincode='';
					$specification='';
				}
				$link="po";
				}else{
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
						$link="generalpo";
				}
				
				
				if($restyui1->approved==0)
				{
					$edit="<a href='".page_url."Store/createrejectionchallan/".$restyui1->id."'><span class='btn btn-xs btn-warning'>Generate Rejection Challan</span></a>";
				}else{
					
					$edit="<a href='".page_url."Store/rejectionchallan/".$restyui1->id."' target='_blank'>".$restyui1->challanno."</a>";
				}
				
				
				$uname=$this->getunit($restyui1->unit);
			$scheduler_data[] = array('sr_no'=>$i,
			'partyname'=>$restyui1->name,
			'ponumber'=>"<a href='".page_url."Store/".$link."/".$restyui1->pono."' target='_blank'>".$restyui1->pono."</a>",
			'item'=>$itemname,
			'fincode'=>$fincode,
			'specialization'=>$specification,
			'qty'=>floatval($restyui1->quantity)." ".$uname,
			'challan'=>$edit);
				
			$i++;
			}
			}
			
			
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
		
		
	}
	
	
	function rejecteditemreq()
	{
		
		$this->load->view('store/rejecteditemrequest');
		
	}
	
	
	
		function rejecteditemrequestlist()
	{
		
		$scheduler_data = array();
		$rest=$this->db->select('a.*,a.pono,a.itemid,a.qty as reject_qty')->from('item_rejection_request
 a')->join('mrn_history b','a.mrnhistoryid=b.id','left')->order_by('a.approved','ASC')->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			
			
			foreach($rest->result() as $restyui1)
			{
				
				
			$podetail=$this->getpoinfo($restyui1->pono);
			
			if(count($podetail)>0)
			{
				foreach($podetail as $podetails);
			}else{
				
				echo "PO DETAIL NOT FOUND";exit;
			}
				
				if($podetails->potype=='0')
			{
				
					$itemname=$this->getmachineitemname($restyui1->itemid);
				$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
				if(count($otherdetails)>0)
				{
			
				$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
				}else{
					$fincode='';
					$specification='';
				}

                    $link="po";
				}else{
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
						$link="generalpo";
				}
				
				
				if($restyui1->approved==0)
				{
					$edit='<div class="col-md-5"><select class="form-control" name="chtype" id="chtype'.$restyui1->id.'">
														 <option value="">Select</option>
														 <option value="1">Rejection Challan</option>
														 <option value="2">Debit Note</option>
														 </select></div><div class="col-md-4"><input type="submit" onclick="createchallan('.$restyui1->id.');" name="sub" class="btn btn-success"></div>';
				}else{
					
					if($restyui1->chtype=='1')
					{
					
					$rejno=$this->getcreatedchallanno($restyui1->mrnhistoryid);
					if(count($rejno)>0)
					{
						$no=$rejno['no'];
						$id=$rejno['id'];
					}else{
						
						$no='';
						$id='';
					}
					$edit="<a href='".page_url."Store/rejectionchallan/".$id."' target='_blank'>Rejection Challan - ".$no."</a>";
					}else{
						
						
						$rejno=$this->getcreateddbno($restyui1->mrnhistoryid);
					
						if(count($rejno)>0)
					{
						$no=$rejno['no'];
						$id=$rejno['id'];
					}else{
						
						$no='';
						$id='';
					}
						
					$edit="<a href='".page_url."Store/debit_note/".$no."' target='_blank'>Debit Note Challan - ".$no."</a>";
						
					}
				}
				
				
				$uname=$this->getunit($podetails->unit);
			$scheduler_data[] = array('sr_no'=>$i,
			'partyname'=>$podetails->name,
			'ponumber'=>"<a href='".page_url."Store/".$link."/".$restyui1->pono."' target='_blank'>".$restyui1->pono."</a>",
			'item'=>$itemname,
			'fincode'=>$fincode,
			'specialization'=>$specification,
			'qty'=>floatval($restyui1->reject_qty)." ".$uname,
			'challan'=>$edit);
				
			$i++;
			
			}
			}
			
			
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
		
		
	}
	


		function rejecteditemrequestlistOldd04sept2020()
	{
		
		$scheduler_data = array();
		$rest=$this->db->select('a.*,a.pono,a.itemid,a.qty as reject_qty')->from('item_rejection_request
 a')->join('mrn_history b','a.mrnhistoryid=b.id','left')->order_by('a.approved','ASC')->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			
			
			foreach($rest->result() as $restyui1)
			{
				
				
			$podetail=$this->getpoinfo($restyui1->pono);
			
			if(count($podetail)>0)
			{
				foreach($podetail as $podetails);
			}else{
				
				echo "PO DETAIL NOT FOUND";exit;
			}
				
				if($podetails->source==1 || $podetails->source==3)
				{
				
				$itemname=$this->getmachineitemname($restyui1->itemid);
				$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
				if(count($otherdetails)>0)
				{
			
				$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
				}else{
					$fincode='';
					$specification='';
				}
				$link="po";
				}else{
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
						$link="generalpo";
				}
				
				
				if($restyui1->approved==0)
				{
					$edit='<div class="col-md-5"><select class="form-control" name="chtype" id="chtype'.$restyui1->id.'">
														 <option value="">Select</option>
														 <option value="1">Rejection Challan</option>
														 <option value="2">Debit Note</option>
														 </select></div><div class="col-md-4"><input type="submit" onclick="createchallan('.$restyui1->id.');" name="sub" class="btn btn-success"></div>';
				}else{
					
					if($restyui1->chtype=='1')
					{
					
					$rejno=$this->getcreatedchallanno($restyui1->mrnhistoryid);
					if(count($rejno)>0)
					{
						$no=$rejno['no'];
						$id=$rejno['id'];
					}else{
						
						$no='';
						$id='';
					}
					$edit="<a href='".page_url."Store/rejectionchallan/".$id."' target='_blank'>Rejection Challan - ".$no."</a>";
					}else{
						
						
						$rejno=$this->getcreateddbno($restyui1->mrnhistoryid);
					
						if(count($rejno)>0)
					{
						$no=$rejno['no'];
						$id=$rejno['id'];
					}else{
						
						$no='';
						$id='';
					}
						
					$edit="<a href='".page_url."Store/debit_note/".$no."' target='_blank'>Debit Note Challan - ".$no."</a>";
						
					}
				}
				
				
				$uname=$this->getunit($podetails->unit);
			$scheduler_data[] = array('sr_no'=>$i,
			'partyname'=>$podetails->name,
			'ponumber'=>"<a href='".page_url."Store/".$link."/".$restyui1->pono."' target='_blank'>".$restyui1->pono."</a>",
			'item'=>$itemname,
			'fincode'=>$fincode,
			'specialization'=>$specification,
			'qty'=>floatval($restyui1->reject_qty)." ".$uname,
			'challan'=>$edit);
				
			$i++;
			
			}
			}
			
			
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
		
		
	}
	




	function rejecteditemrequestlistOldwrong()
	{
		
		$scheduler_data = array();
		$rest=$this->db->select('a.*,b.po_no,b.itemid,b.reject_qty')->from('item_rejection_request
 a')->join('mrn_history b','a.mrnhistoryid=b.id','left')->order_by('a.approved','ASC')->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $rt);
			$podetail=$this->getpoinfo($rt->pono);
			if(count($podetail)>0)
			{
				foreach($podetail as $podetails);
			}else{
				
				echo "PO DETAIL NOT FOUND";exit;
			}
			foreach($rest->result() as $restyui1)
			{
				
				
				if($podetails->source==1 || $podetails->source==3)
				{
				$itemname=$this->getmachineitemname($restyui1->itemid);
				$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
				if(count($otherdetails)>0)
				{
			
				$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
				}else{
					$fincode='';
					$specification='';
				}
				}else{
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
				}
				
				
				if($restyui1->approved==0)
				{
					$edit='<div class="col-md-5"><select class="form-control" name="chtype" id="chtype'.$restyui1->id.'">
														 <option value="">Select</option>
														 <option value="1">Rejection Challan</option>
														 <option value="2">Debit Note</option>
														 </select></div><div class="col-md-4"><input type="submit" onclick="createchallan('.$restyui1->id.');" name="sub" class="btn btn-success"></div>';
				}else{
					
					if($restyui1->chtype=='1')
					{
					
					$rejno=$this->getcreatedchallanno($restyui1->mrnhistoryid);
					if(count($rejno)>0)
					{
						$no=$rejno['no'];
						$id=$rejno['id'];
					}else{
						
						$no='';
						$id='';
					}
					$edit="<a href='".page_url."Store/rejectionchallan/".$id."' target='_blank'>Rejection Challan - ".$no."</a>";
					}else{
						
						
						$rejno=$this->getcreateddbno($restyui1->mrnhistoryid);
					
						if(count($rejno)>0)
					{
						$no=$rejno['no'];
						$id=$rejno['id'];
					}else{
						
						$no='';
						$id='';
					}
						
					$edit="<a href='".page_url."Store/debit_note/".$no."' target='_blank'>Debit Note Challan - ".$no."</a>";
						
					}
				}
				
				
				$uname=$this->getunit($podetails->unit);
			$scheduler_data[] = array('sr_no'=>$i,
			'partyname'=>$podetails->name,
			'ponumber'=>"<a href='".page_url."Store/po/".$restyui1->po_no."' target='_blank'>".$restyui1->po_no."</a>",
			'item'=>$itemname,
			'fincode'=>$fincode,
			'specialization'=>$specification,
			'qty'=>floatval($restyui1->reject_qty)." ".$uname,
			'challan'=>$edit);
				
			$i++;
			}
			}
			
			
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
		
		
	}
	

	function rejecteditemrequestlistOlddd()
	{
		
		$scheduler_data = array();
		$rest=$this->db->select('a.*,b.po_no,b.itemid,b.reject_qty,c.source,d.name,c.unit')->from('item_rejection_request
 a')->join('mrn_history b','a.mrnhistoryid=b.id','left')->join('purchase_order c','b.po_no=c.pono','left')->join('vendors d','c.vendor=d.id','left')->order_by('a.approved','ASC')->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				
				if($restyui1->source==1 || $restyui1->source==3)
				{
				$itemname=$this->getmachineitemname($restyui1->itemid);
				$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
				if(count($otherdetails)>0)
				{
			
				$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
				}else{
					$fincode='';
					$specification='';
				}
				}else{
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
				}
				
				
				if($restyui1->approved==0)
				{
					$edit='<div class="col-md-5"><select class="form-control" name="chtype" id="chtype'.$restyui1->id.'">
														 <option value="">Select</option>
														 <option value="1">Rejection Challan</option>
														 <option value="2">Debit Note</option>
														 </select></div><div class="col-md-4"><input type="submit" onclick="createchallan('.$restyui1->id.');" name="sub" class="btn btn-success"></div>';
				}else{
					
					if($restyui1->chtype=='1')
					{
					
					$rejno=$this->getcreatedchallanno($restyui1->mrnhistoryid);
					if(count($rejno)>0)
					{
						$no=$rejno['no'];
						$id=$rejno['id'];
					}else{
						
						$no='';
						$id='';
					}
					$edit="<a href='".page_url."Store/rejectionchallan/".$id."' target='_blank'>Rejection Challan - ".$no."</a>";
					}else{
						
						
						$rejno=$this->getcreateddbno($restyui1->mrnhistoryid);
					
						if(count($rejno)>0)
					{
						$no=$rejno['no'];
						$id=$rejno['id'];
					}else{
						
						$no='';
						$id='';
					}
						
					$edit="<a href='".page_url."Store/debit_note/".$no."' target='_blank'>Debit Note Challan - ".$no."</a>";
						
					}
				}
				
				
				$uname=$this->getunit($restyui1->unit);
			$scheduler_data[] = array('sr_no'=>$i,
			'partyname'=>$restyui1->name,
			'ponumber'=>"<a href='".page_url."Store/po/".$restyui1->po_no."' target='_blank'>".$restyui1->po_no."</a>",
			'item'=>$itemname,
			'fincode'=>$fincode,
			'specialization'=>$specification,
			'qty'=>floatval($restyui1->reject_qty)." ".$uname,
			'challan'=>$edit);
				
			$i++;
			}
			}
			
			
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
		
		
	}
	


function getcreatedchallanno($mrnno)
{
	$rejch=array();
	$mrns=$this->db->select('id,challanno')->from('outwardchallan')->where('mrnhistoryid',$mrnno)->get();
	if($mrns->num_rows()>0)
	{
		foreach($mrns->result() as $mrnsas);
		
		$rejch['no']=$mrnsas->challanno;
		$rejch['id']=$mrnsas->id;
	}
	
	return $rejch;
		
}



function getcreateddbno($mrnno)
{
	$rejch=array();
	$mrns=$this->db->select('id,dbno')->from('debitnote')->where('mrnhistoryid',$mrnno)->get();
	if($mrns->num_rows()>0)
	{
		foreach($mrns->result() as $mrnsas);
		
		$rejch['no']=$mrnsas->dbno;
		$rejch['id']=$mrnsas->id;
	}
	
	return $rejch;
	
}


function getposource($pono)
{
	
	$resytyuuiw=$this->db->select('source')->from('purchase_order')->where('pono',$pono)->get();
	if($resytyuuiw->num_rows()>0)
	{
		foreach($resytyuuiw->result() as $resytyuuiw1);
		
		$source=$resytyuuiw1->source;
		
		return $source;
	}else{
		
		echo "PO NOT FOUND";exit;
	}
	
}


function getpoinfo($pono)
{
	
	$resytyuuiw=$this->db->select('a.source,a.unit,b.name,a.potype')->from('purchase_order a')->join('vendors b','a.vendor=b.id','left')->where('a.pono',$pono)->group_by('a.pono')->get();
	if($resytyuuiw->num_rows()>0)
	{
		return $resytyuuiw->result();
	}else{
		
		echo "PO NOT FOUND";exit;
	}
	
	
	
}


function issuegeneralitems()
{
	
	$this->load->view('store/issuegeneralitems');
}

function allgeneralitems()
{
		$housekeeping_data= array();
		$this->db->select('a.*,b.category,c.shortname')->from('house_keeping_items a')->join('presto_machine_part_category b','a.category_id=b.id','left')->join('units c','a.unit=c.id','left')->order_by('a.item_name','ASC');
		$query = $this->db->get();
		$res = $query->result();
		if($query->num_rows()>0)
		{
			$i=1;
		foreach($res as $row){
			
			if($row->qty>0)
			{
		
			$edit="<a href='".page_url."Store/issuegeneralitem/".$row->id."' class='btn btn-sm btn-warning'>Issue Items</a>";
			}else{
				$edit="<span style='color:red;'>Stock Not Available</span>";
			}
			
			$housekeeping_data[] = array('sr_no'=>$i,
			'item_name'=>$row->item_name,
			'category'=>$row->category,
			'qty'=>$row->qty.' '.$row->shortname,
			'min_qty'=>$row->min_qty.' '.$row->shortname,
			'edit'=>$edit);
			
			$i++;
		}
		}
		
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($housekeeping_data),
			"iTotalDisplayRecords" => count($housekeeping_data),
			"aaData"=>$housekeeping_data);
			echo json_encode($results);
	
	
}

function zonewisedispatchfortommorow()
	{
	$this->load->view('FMS/zonewisedispatchforrommorow');
	}
	
	
	
public function zonewisedispatchfortommorow_order_list()
	{
		$zoneid=$this->uri->segment(3);
		if($zoneid<>'')
		{
			$user=$this->getallzoneusers($zoneid);
			$users= "'" . implode ( "', '", $user ) . "'";
		}
		$scheduler_data = array();
		$this->db->select('a.order_id, a.order_type, a.marketing_person,a.po_number, a.company_name,a.closedOn, a.address, a.email, a.mobile_number, a.internal_order_no, a.discount, a.order_value_after_discount, a.advance_amount, a.payment_terms, a.installation_charges, a.order_status, a.added_on,a.packing_type, a.installation_type, b.user_id, b.title, b.first_name, b.last_name, a.installation_amount, a.packing_charges, a.packing_amount, a.freight_type, a.freight_amount,a.remarks,a.docketnumber,a.billno,a.billdate,a.dodamount,a.frieghtamount,a.shipmentmode')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left')->where('a.order_status','1');
		if($zoneid<>'' && $users<>'')
		{
			$this->db->where_in('a.added_by',$users,false);
		}
		$this->db->order_by('a.closedOn','DESC');
		
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			$completedjobcard=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1')->where('finalpacked','0')->get();
			$completedjobcards=$completedjobcard->num_rows();
			
			$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name,a.finalpacked,a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			$alljobcard=$query1->num_rows();
			if($alljobcard>0)
			{
				foreach($query1->result() as $alluee);
					$finalpack=$alluee->finalpacked;
					
			}else{ $finalpack=0; }
			
		if($alljobcard<>0)
		{
		if($completedjobcards==$alljobcard)
		{
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			
			$html = "<table border='1' style='width:500px;'><tr style='background-color:white;'><th style='padding:2px 2px 2px 2px'>INSTRUMENT</th><th style='padding:2px 2px 2px 2px'>JOB CARD NO.</th></tr>";
			$instrumentsss = array();
			
			
			
			foreach($query1->result() as $instruments){
				if($instruments->complete=='1'){
					$backgroundcolor = "background-color:green; color:white !important; font-weight:bold;";
				}else{
					$backgroundcolor="";
				}
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->instruments_name)."</td>";
				//$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->qty)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->job_card_no)."</td>";
				$html.="</tr>";
			}
			$html.="</table>";
			$ins_charges = "";
			/** Get order planned **/
			$restt=$this->db->select('count(id) as totaljobcard')->from('order_instruments')->where('order_id',$row->order_id)->get();
			foreach($restt->result() as $instcount);
			$totjobcard= $instcount->totaljobcard;
			
			
	
			
			if($row->installation_charges=='1'){
				$charges= "<strong>INSTALLATION TYPE </strong>".$row->installation_type."<br> <strong>INSTALLATION CHARGES</strong> - REQUIRED";
				
				$ins_charges= $charges."<br> AMOUNT - ".$row->installation_amount;
			}else{
					$ins_charges = "NOT REQUIRED";
			}
			
			if($row->packing_charges=='1'){
				$packcharges = "PAID BY PARTY";
				$packingcharges= $packcharges."<br> Amount - <strong>".$row->packing_amount."</strong>";
			}else{
					$packingcharges = "INCLUSIVE";
			}
			
			if($row->freight_type=='1'){
				$freigntcharges = "TO PAY BASIS";
				
			}else if($row->freight_type=='2'){
				$freigntcharges = "PAID BY PRESTO";
			}else if($row->freight_type=='3'){
				$frtcharges = "BILLED IN INVOICE";
				$freigntcharges= $frtcharges."<br> Amount - <strong>".$row->freight_amount."</strong>";
			}else{
				$freigntcharges = "OWN PICK-UP";
					
			}
			
			$closedon=date('d-M-Y g:i A',strtotime($row->closedOn));
			$plannedtime = date('d-M-Y g:i A', strtotime($closedon . ' +1 day'));

			
			
$printpackinglabel='<a href="'.page_url.'Reporting/generatepackingslip/'.$row->order_id.'" target="_blank"><span class="btn btn-success btn-sm">Print Packing Label</span></a>';
			
			if($finalpack=='1')
{
			$restyupacku=$this->db->select('addedOn')->from('order_finalpacking_details')->where('orderid',$row->order_id)->get();	
				if($restyupacku->num_rows()>0)
{
				foreach($restyupacku->result() as $restyupacku2);
				$actualtime=date('d-M-Y g:i A',strtotime($restyupacku2->addedOn));
				
}else{  

	$actualtime="";
					}
		$completeorder='Task has been done';	
		
			   $ehe="Docket No.-".$row->docketnumber.'<br/>';
$ehe.="Bill No.-".$row->billno.'<br/>';
$ehe.="Bill Date-".$row->billdate.'<br/>';
$ehe.="Shipment.-".$row->shipmentmode.'<br/>';
$ehe.="DOD Amt.-".$row->dodamount.'<br/>';
$ehe.="Frieght Amt.-".$row->frieghtamount.'<br/>';		
	
}else{  $actualtime=""; 
	 
	  	
	  $completeorder='<a href="javascript:;" onclick="markstagedone('."'".$row->order_id."'".','."'".strtoupper($row->company_name)."'".','."'".$row->internal_order_no."'".')"><span class="btn btn-warning btn-sm">Mark as Done</span></a>';
	  
 $ehe='';	

}
			
			$scheduler_data[] = array('sr_no'=>$i,
			'closeorder'=>$completeorder,
									  'packinglabel'=>$printpackinglabel,
			'added_on'=>$closedon,
			 'plannedtime'=>$plannedtime,
			 'actualtime'=>$actualtime,
			'order_type'=>strtoupper($row->order_type),
			'marketing_person'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'po_number'=>strtoupper($row->po_number),
			'company_name'=>strtoupper($row->company_name),
			'address'=>strtoupper($row->address),
			'email'=>strtoupper($row->email),
			'mobile_number'=>strtoupper($row->mobile_number),
			'internal_order_no'=>strtoupper($row->internal_order_no),
			'itemname'=>$html,
			'discount'=>strtoupper($row->discount)."%",
			'order_value_after_discount'=>strtoupper($row->order_value_after_discount),
			'advance_amount'=>strtoupper($row->advance_amount),
			'payment_terms'=>strtoupper($row->payment_terms),
			'installation_charges'=>strtoupper($ins_charges),
			'packingcharges'=>"<strong>Packing Type</strong> -".$row->packing_type."<br>".$packingcharges,
			'freigntcharges'=>$freigntcharges,
			'remarks'=>$row->remarks);
			$i++;
		}
		}
	}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}

function getallzoneusers($zoneid)
{
	$user=array();
	$resty=$this->db->select('userid')->from('saleszoneusers')->where('zoneid',$zoneid)->get();
	if($resty->num_rows()>0)
	{
		foreach($resty->result() as $resty1)
		{
		$user[]=$resty1->userid;
		}
	}
	
	return $user;
	
}

function movetodispatch()
{
	$orderid=$this->uri->segment(3);
		$flag=$this->uri->segment(4);

	$data=array('movetodispatch'=>'1','movedby'=>$_SESSION['logged_in']['user_id'],'movedOn'=>date('Y-m-d H:i:s'));
	$this->db->where('order_id',$orderid);
	$this->db->update('prestogroup_orders',$data);
	
	if($flag=='')
	{
	$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Moved To Dispatch.</span></div>');
		redirect(page_url.'Reporting/completedorders');
	}else
	{
	    	$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Moved To Dispatch.</span></div>');
		redirect(page_url.'Reporting/completedordersservice');
	}
	
	
}

function checkifalljobcardsareready($odid)
{
    $ordid=$this->db->select('id')->from('order_instruments')->where('order_id',$odid)->get();
    
    $alljobcard=$ordid->num_rows();
    
     $ordid1=$this->db->select('id')->from('order_instruments')->where('order_id',$odid)->where('complete','1')->where('packed','1')->where('finalpacked','0')->get();
    
    $completedjobcard=$ordid1->num_rows();
    
    if($alljobcard==$completedjobcard)
    {
        return true;
    }else
    {
        return false;
    }
    
}

function dayssinceorderisready($odid)
{
    $daysss=array();
    $restyu=$this->db->select('id')->from('order_instruments')->where('order_id',$odid)->get();
    if($restyu->num_rows()>0)
    {
        foreach($restyu->result() as $restyu1)
        {
           $startpoint=$this->getplanstartingpoint($odid,$restyu1->id);
           $planid=$this->getplanid($odid,$restyu1->id);
           if($startpoint==1 || $startpoint==4 )
           {
               $uiyt=$this->db->select('max(addedOn) as lastdate')->from('order_stage')->where('orderid',$odid)->get();
               if($uiyt->num_rows()>0)
               {
                   foreach($uiyt->result() as $uiyt1);
                   
                   $lastdate=date('Y-m-d',strtotime($uiyt1->lastdate));
                  $daysss[]= $lastdate;
                   
               }else
               {
                   
               }
               
               
           }else if($startpoint==2 || $startpoint==7 )
           {
              $lastdate=$this->getplanningdate($odid,$restyu1->id);
              $daysss[]=date('Y-m-d',strtotime($lastdate));
           }else if($startpoint==3)
           {
               
                $lastdate=$this->getboughtoutdate($planid);
              $daysss[]=date('Y-m-d',strtotime($lastdate));
               
               
           }
            
        }
        
        
    }else
    {
        
    }
    
    return $daysss;
    
   
}

function getplanstartingpoint($orderid,$jobcard)
{
    $sdfsr=$this->db->select('factory')->from('order_planning')->where('jobcard_id',$jobcard)->where('order_id',$orderid)->get();
    if($sdfsr->num_rows()>0)
    {
        foreach($sdfsr->result() as $sdfsr1);
        
        return $sdfsr1->factory;
        
        
        
    }else
    {
        return null;
    }
    
    
}

function getplanningdate($orderid,$jobcard)
{
    $sdfsr=$this->db->select('plannedOn')->from('order_planning')->where('jobcard_id',$jobcard)->where('order_id',$orderid)->get();
    if($sdfsr->num_rows()>0)
    {
        foreach($sdfsr->result() as $sdfsr1);
        
        return $sdfsr1->plannedOn;
        
        
        
    }else
    {
        return null;
    }
    
    
}


function getboughtoutdate($planid)
{
    
     $sdfsr=$this->db->select('updatedOn')->from('boughtoutfms')->where('planid ',$planid)->get();
    if($sdfsr->num_rows()>0)
    {
        foreach($sdfsr->result() as $sdfsr1);
        
        return $sdfsr1->updatedOn;
        
        
        
    }else
    {
        return null;
    }
    
}

function compareDates($date1, $date2){
      return strtotime($date1) - strtotime($date2);
   }
   
   function getplanid($orderid,$jobcardid)
   {
       $restyus=$this->db->select('id')->from('order_planning')->where('jobcard_id',$jobcardid)->where('order_id',$orderid)->get();
       if($restyus->num_rows()>0)
       {
           foreach($restyus->result() as $restyus1);
           
           return $restyus1->id;
       }else
       {
           return 0;
       }
       
   }
   
   public function dispatchfortommorow_order_listforsales()
	{
		$scheduler_data = array();
$type=$this->uri->segment(3);

		$userid = $_SESSION['logged_in']['user_id'];
		$this->db->select('a.docketno,a.movedOn,a.order_id, a.order_type, a.marketing_person,a.po_number, a.company_name,a.closedOn, a.address, a.email, a.mobile_number, a.internal_order_no, a.discount, a.order_value_after_discount, a.advance_amount, a.payment_terms, a.installation_charges, a.order_status, a.added_on,a.packing_type, a.installation_type, b.user_id, b.title, b.first_name, b.last_name, a.installation_amount, a.packing_charges, a.packing_amount, a.freight_type, a.freight_amount,a.remarks')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left');
		if($userid<>''){
		    $this->db->where('a.marketing_person',$userid);
		}
		$query = $this->db->where('a.order_status','1')->where('a.movetodispatch','1')->order_by('a.order_id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
		$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1');
		
		if($type<>'')
			{
			    if($type=='0')
			    {
			       $this->db->where('finalpacked','0'); 
			    }else
			    {
			         $this->db->where('finalpacked','1');
			    }
			    
			}
			
			$completedjobcard=$this->db->get();
			$completedjobcards=$completedjobcard->num_rows();
			
			$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name,a.finalpacked,a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			$alljobcard=$query1->num_rows();
			if($alljobcard>0)
			{
				foreach($query1->result() as $alluee);
					$finalpack=$alluee->finalpacked;
					
			}else{ $finalpack=0; }
			
		if($alljobcard<>0)
		{
		if($completedjobcards==$alljobcard)
		{
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			
			$html = "<table border='1' style='width:500px;'><tr style='background-color:white;'><th style='padding:2px 2px 2px 2px'>INSTRUMENT</th><th style='padding:2px 2px 2px 2px'>JOB CARD NO.</th></tr>";
			$instrumentsss = array();
			
			
			
			foreach($query1->result() as $instruments){
				if($instruments->complete=='1'){
					$backgroundcolor = "background-color:#10C469; color:white !important; font-weight:bold;";
				}else{
					$backgroundcolor="";
				}
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->instruments_name)."</td>";
				//$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->qty)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->job_card_no)."</td>";
				$html.="</tr>";
			}
			$html.="</table>";
			$ins_charges = "";
			/** Get order planned **/
			$restt=$this->db->select('count(id) as totaljobcard')->from('order_instruments')->where('order_id',$row->order_id)->get();
			foreach($restt->result() as $instcount);
			$totjobcard= $instcount->totaljobcard;
			
			
	
			
			if($row->installation_charges=='1'){
				$charges= "<strong>INSTALLATION TYPE </strong>".$row->installation_type."<br> <strong>INSTALLATION CHARGES</strong> - REQUIRED";
				
				$ins_charges= $charges."<br> AMOUNT - ".$row->installation_amount;
			}else{
					$ins_charges = "NOT REQUIRED";
			}
			
			if($row->packing_charges=='1'){
				$packcharges = "PAID BY PARTY";
				$packingcharges= $packcharges."<br> Amount - <strong>".$row->packing_amount."</strong>";
			}else{
					$packingcharges = "INCLUSIVE";
			}
			
			if($row->freight_type=='1'){
				$freigntcharges = "TO PAY BASIS";
				
			}else if($row->freight_type=='2'){
				$freigntcharges = "PAID BY PRESTO";
			}else if($row->freight_type=='3'){
				$frtcharges = "BILLED IN INVOICE";
				$freigntcharges= $frtcharges."<br> Amount - <strong>".$row->freight_amount."</strong>";
			}else{
				$freigntcharges = "OWN PICK-UP";
					
			}
			
			$closedon=date('d-M-Y g:i A',strtotime($row->movedOn));
			$plannedtime = date('d-M-Y g:i A', strtotime($closedon . ' +1 day'));

if($row->installation_charges==1)
			{
				$ser=1;
			}else{
				
				$ser=0;
			}
			
			
$printpackinglabel='<a href="'.page_url.'Reporting/generatepackingslip/'.$row->order_id.'" target="_blank"><span class="btn btn-success btn-sm">Print Packing Label</span></a>';
			
			if($finalpack=='1')
{
			$restyupacku=$this->db->select('addedOn')->from('order_finalpacking_details')->where('orderid',$row->order_id)->get();	
				if($restyupacku->num_rows()>0)
{
				foreach($restyupacku->result() as $restyupacku2);
				$actualtime=date('d-M-Y g:i A',strtotime($restyupacku2->addedOn));
				
}else{  

	$actualtime="";
					}
			$completeorder="<span class='btn btn-success btn-sm'>Dispatch Done</span>";		
	
}else{  $actualtime=""; 
	 
	  	$completeorder="<span class='btn btn-warning btn-sm'>Dispatch Pending</span>";
	 // $completeorder='<a href="javascript:;" onclick="markstagedone('."'".$row->order_id."'".','."'".strtoupper($row->company_name)."'".','."'".$row->internal_order_no."'".','."'".$ser."'".')"><span class="btn btn-warning btn-sm">Mark as Done</span></a>';

}
	
	
	if($row->docketno<>'')
{
    $dock="<a href='".page_url."image_bank/docketno/".$row->docketno."' download>Docket Image</a>";
}else
{ 
     $dock='';
}

		
			$scheduler_data[] = array('sr_no'=>$i,
			'closeorder'=>$completeorder,
			'docket'=>$dock,
			'packinglabel'=>$printpackinglabel,
			'added_on'=>$closedon,
			 'plannedtime'=>$plannedtime,
			 'actualtime'=>$actualtime,
			'order_type'=>strtoupper($row->order_type),
			'marketing_person'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'po_number'=>strtoupper($row->po_number),
			'company_name'=>strtoupper($row->company_name),
			'address'=>strtoupper($row->address),
			'email'=>strtoupper($row->email),
			'mobile_number'=>strtoupper($row->mobile_number),
			'internal_order_no'=>strtoupper($row->internal_order_no),
			'itemname'=>$html,
			'discount'=>strtoupper($row->discount)."%",
			'order_value_after_discount'=>strtoupper($row->order_value_after_discount),
			'advance_amount'=>strtoupper($row->advance_amount),
			'payment_terms'=>strtoupper($row->payment_terms),
			'installation_charges'=>strtoupper($ins_charges),
			'packingcharges'=>"<strong>Packing Type</strong> -".$row->packing_type."<br>".$packingcharges,
			'freigntcharges'=>$freigntcharges,
			'remarks'=>$row->remarks);
			$i++;
		}
		}
	}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}
	
	  public function dispatchfortommorow_order_listforservice()
	{
		$scheduler_data = array();
$type=$this->uri->segment(3);

		$userid = $_SESSION['logged_in']['user_id'];
		$this->db->select('a.docketno,a.movedOn,a.order_id, a.order_type, a.marketing_person,a.po_number, a.company_name,a.closedOn, a.address, a.email, a.mobile_number, a.internal_order_no, a.discount, a.order_value_after_discount, a.advance_amount, a.payment_terms, a.installation_charges, a.order_status, a.added_on,a.packing_type, a.installation_type, b.user_id, b.title, b.first_name, b.last_name, a.installation_amount, a.packing_charges, a.packing_amount, a.freight_type, a.freight_amount,a.remarks')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left');
	
		$query = $this->db->where('a.order_status','1')->where('a.closeorder','0')->where('a.movetodispatch','1')->where('a.order_type','SERVICE')->order_by('a.order_id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
		$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1');
		
		if($type<>'')
			{
			    if($type=='0')
			    {
			       $this->db->where('finalpacked','0'); 
			    }else
			    {
			         $this->db->where('finalpacked','1');
			    }
			    
			}
			
			$completedjobcard=$this->db->get();
			$completedjobcards=$completedjobcard->num_rows();
			
			$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name,a.finalpacked,a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			$alljobcard=$query1->num_rows();
			if($alljobcard>0)
			{
				foreach($query1->result() as $alluee);
					$finalpack=$alluee->finalpacked;
					
			}else{ $finalpack=0; }
			
		if($alljobcard<>0)
		{
		if($completedjobcards==$alljobcard)
		{
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			
			$html = "<table border='1' style='width:500px;'><tr style='background-color:white;'><th style='padding:2px 2px 2px 2px'>INSTRUMENT</th><th style='padding:2px 2px 2px 2px'>JOB CARD NO.</th></tr>";
			$instrumentsss = array();
			
			
			
			foreach($query1->result() as $instruments){
				if($instruments->complete=='1'){
					$backgroundcolor = "background-color:#10C469; color:white !important; font-weight:bold;";
				}else{
					$backgroundcolor="";
				}
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->instruments_name)."</td>";
				//$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->qty)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->job_card_no)."</td>";
				$html.="</tr>";
			}
			$html.="</table>";
			$ins_charges = "";
			/** Get order planned **/
			$restt=$this->db->select('count(id) as totaljobcard')->from('order_instruments')->where('order_id',$row->order_id)->get();
			foreach($restt->result() as $instcount);
			$totjobcard= $instcount->totaljobcard;
			
			
	
			
			if($row->installation_charges=='1'){
				$charges= "<strong>INSTALLATION TYPE </strong>".$row->installation_type."<br> <strong>INSTALLATION CHARGES</strong> - REQUIRED";
				
				$ins_charges= $charges."<br> AMOUNT - ".$row->installation_amount;
			}else{
					$ins_charges = "NOT REQUIRED";
			}
			
			if($row->packing_charges=='1'){
				$packcharges = "PAID BY PARTY";
				$packingcharges= $packcharges."<br> Amount - <strong>".$row->packing_amount."</strong>";
			}else{
					$packingcharges = "INCLUSIVE";
			}
			
			if($row->freight_type=='1'){
				$freigntcharges = "TO PAY BASIS";
				
			}else if($row->freight_type=='2'){
				$freigntcharges = "PAID BY PRESTO";
			}else if($row->freight_type=='3'){
				$frtcharges = "BILLED IN INVOICE";
				$freigntcharges= $frtcharges."<br> Amount - <strong>".$row->freight_amount."</strong>";
			}else{
				$freigntcharges = "OWN PICK-UP";
					
			}
			
			$closedon=date('d-M-Y g:i A',strtotime($row->movedOn));
			$plannedtime = date('d-M-Y g:i A', strtotime($closedon . ' +1 day'));

if($row->installation_charges==1)
			{
				$ser=1;
			}else{
				
				$ser=0;
			}
			
			
$printpackinglabel='<a href="'.page_url.'Reporting/generatepackingslip/'.$row->order_id.'" target="_blank"><span class="btn btn-success btn-sm">Print Packing Label</span></a>';
			
			if($finalpack=='1')
{
			$restyupacku=$this->db->select('addedOn')->from('order_finalpacking_details')->where('orderid',$row->order_id)->get();	
				if($restyupacku->num_rows()>0)
{
				foreach($restyupacku->result() as $restyupacku2);
				$actualtime=date('d-M-Y g:i A',strtotime($restyupacku2->addedOn));
				
}else{  

	$actualtime="";
					}
			$completeorder="<span class='btn btn-success btn-sm'>Dispatch Done</span>";		
	
}else{  $actualtime=""; 
	 
	  	$completeorder="<span class='btn btn-warning btn-sm'>Dispatch Pending</span>";
	 // $completeorder='<a href="javascript:;" onclick="markstagedone('."'".$row->order_id."'".','."'".strtoupper($row->company_name)."'".','."'".$row->internal_order_no."'".','."'".$ser."'".')"><span class="btn btn-warning btn-sm">Mark as Done</span></a>';

}
	
	
	if($row->docketno<>'')
{
    $dock="<a href='".page_url."image_bank/docketno/".$row->docketno."' download>Docket Image</a>";
}else
{ 
     $dock='';
}

		
			$scheduler_data[] = array('sr_no'=>$i,
			'closeorder'=>'Pending',
			'docket'=>$dock,
			'packinglabel'=>$printpackinglabel,
			'added_on'=>$closedon,
			 'plannedtime'=>$plannedtime,
			 'actualtime'=>$actualtime,
			'order_type'=>strtoupper($row->order_type),
			'marketing_person'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'po_number'=>strtoupper($row->po_number),
			'company_name'=>strtoupper($row->company_name),
			'address'=>strtoupper($row->address),
			'email'=>strtoupper($row->email),
			'mobile_number'=>strtoupper($row->mobile_number),
			'internal_order_no'=>strtoupper($row->internal_order_no),
			'itemname'=>$html,
			'discount'=>strtoupper($row->discount)."%",
			'order_value_after_discount'=>strtoupper($row->order_value_after_discount),
			'advance_amount'=>strtoupper($row->advance_amount),
			'payment_terms'=>strtoupper($row->payment_terms),
			'installation_charges'=>strtoupper($ins_charges),
			'packingcharges'=>"<strong>Packing Type</strong> -".$row->packing_type."<br>".$packingcharges,
			'freigntcharges'=>$freigntcharges,
			'remarks'=>$row->remarks);
			$i++;
		}
		}
	}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}
	
	
	function salesdispatchfortommorow()
	{

$this->load->view('FMS/salesdispatchforrommorow');


     }
     
     
     	function salesdispatchfortommorowforservice()
	{

$this->load->view('FMS/salesdispatchforrommorowforservice');


     }
     
     
     function jobcarditem()
{
	$this->load->view('store/openjobcards');
	
	
}


function blockeditemagainstjobcard()
{
	
	$scheduler_data = array();
		$rest=$this->db->select('a.*,b.job_card_no,count(a.itemid) as icount,c.instruments_name')->from('blockedstock a')->join('order_instruments b','a.jobcardid=b.id')->join('presto_instruments c','a.machineid=c.id')->where('a.active','1')->where('a.issued','0')->group_by('a.jobcardid')->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			
			foreach($rest->result() as $restyui1)
			{
				$edit="<a href='".page_url."store/issueblockeditems/".$restyui1->jobcardid."'><span class='btn btn-xs btn-warning'>Issue Items</span></a>";
			$scheduler_data[] = array('sr_no'=>$i,
			'jobcardno'=>$restyui1->job_card_no,
			'pono'=>$restyui1->pono,
			'machinename'=>$restyui1->instruments_name,
			'item'=>$restyui1->icount,
			'issue'=>$edit);
				
			$i++;
			}
			}
			
			
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
		
	
	
}

function getalldispatchedjobcard($odid)
{
    $dispatchedjobcard=array();
    
    $ordid=$this->db->select('id')->from('order_instruments')->where('order_id',$odid)->get();
    
    $alljobcard=$ordid->num_rows();
    
    
      $ordid1=$this->db->select('id')->from('order_instruments')->where('order_id',$odid)->where('complete','1')->where('packed','1')->where('finalpacked','1')->get();
    
    $completedjobcard=$ordid1->num_rows();
    
    if($alljobcard==$completedjobcard)
    {
        return true;
    }else
    {
        return false;
    }
    
}


function divertedorderlist()
{
    
    $this->load->view('FMS/divertedorders');
}


function divertedorderlisting()
{
     $scheduler_data=array();
    $resty=$this->db->select('a.*,b.company_name,b.internal_order_no')->from('divertedorders a')->join('prestogroup_divertedordershisory b','a.divertedorderid=b.order_id')->order_by('a.addedOn','DESC')->get();
    if($resty->num_rows()>0)
    {
       
        $i=1;
    
       foreach($resty->result() as $resty1)
       {
           $restyu=$this->db->select('first_name,last_name')->from('system_users')->where('user_id',$resty1->addedBy)->get();
           if($restyu->num_rows()>0)
           {
               foreach($restyu->result() as $restyu11);
               $adby=$restyu11->first_name.' '.$restyu11->last_name;
           }else
           {
                $adby='';
           }

          
        $scheduler_data[] = array('sr_no'=>$i,
        'divertedod'=>$resty1->company_name."-".$resty1->internal_order_no,
        'divertedto'=>$resty1->divertcustomer."-".$resty1->divertiono,
        'divertedon'=>date('d-m-Y',strtotime($resty1->addedOn)),
        'divertedby'=>$adby);
		 $i++;
       }
        
        
        
    }
    
    	
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
		
    
}


function salesstockvaluelist()
{
     
			$this->load->view('FMS/stockvaluelistforsales');
    
}
public function stockvalue_listforsales()
	{
		$scheduler_data = array();
		$testronix=$this->uri->segment(3);
		$type = "'0','1'";
		$this->db->select('id, instruments_name, stock')->from('presto_instruments')->where_in('type',$type,false)->where('status','1');
			if($testronix==1)
			{
				$this->db->where('instruments_of','2');
			}
		$restyui=$this->db->get();
		if($restyui->num_rows()>0)
		{
		    $html="";
		   	$ass=0;
			$i=1;
			foreach($restyui->result() as $restyui1)
			{
			    $query1=$this->db->select('b.company_name,a.job_card_no,b.internal_order_no,a.order_id,c.first_name,c.last_name')->from('order_instruments a')->join('prestogroup_orders b','a.order_id=b.order_id')->join('system_users c','b.marketing_person=c.user_id')->where('a.item_id',$restyui1->id)->group_by('a.order_id')->get();
			
			 $html = "<table border='1' style='width:800px;'><tr style='background-color:white;width:100px;'><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:200px;'>I/O NO.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:400px;'>PARTY NAME.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:100px;'>Days.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:100px;'>Dealing Manager.</th></tr>";
			 
			if($query1->num_rows()>0)
			{
			   
			$comdate=array();
			$exstock=array();
		
			foreach($query1->result() as $instruments){
			    
				if($instruments->order_id>0){
				    
				$isdispatched=$this->getalldispatchedjobcard($instruments->order_id);
					if($isdispatched==0)
				{
			
				$isready=$this->checkifalljobcardsareready($instruments->order_id);
				if($isready==true)
				{
				    $ass=1;
				    $back="background-color:#10C469;";
				    $fcolor="color:#fff;";
				    
				    $comdate=$this->dayssinceorderisready($instruments->order_id);
				    
				    //echo "<pre>"; print_r($comdate);exit;
                    if(count($comdate)>0)
                    {
                    $recentdate=max($comdate);
                    $todaysdate=date('Y-m-d');
                    $days = (strtotime($todaysdate) - strtotime($recentdate)) / (60 * 60 * 24);
                    }else
                    {
                    $days='';
                    }
                    
                    if(strpos($instruments->company_name,'PRESTO')!== false)
                    {
                    $exstock[]=1;
                    }else
                    {
                    $exstock[]=0;
                    }

                    

				}else
				{
				    $back="";
				    $fcolor="";
				     $days='';
				     $exstock[]=0;
				     
				}
				
				
				
				$html.="<tr style='".$back."'>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:left;width:10px;".$fcolor."'>".strtoupper($instruments->internal_order_no)."</td>";
			
				$html.="<td style='padding:2px 2px 2px 2px; text-align:left;width:100px;".$fcolor."'>".strtoupper($instruments->company_name)."</td>";
			
					$html.="<td style='padding:2px 2px 2px 2px; text-align:left;width:100px;".$fcolor."'>".$days."</td>";
					
						$html.="<td style='padding:2px 2px 2px 2px; text-align:left;width:100px;".$fcolor."'>".$instruments->first_name." ".$instruments->last_name."</td>";
					
				$html.="</tr>";
				
				
			}
				}else{
				    
				}
			
			}
			
		   
			
			$html.="</table>";
			}else{
				$isdispatched="0";
				$html=$html;
			}
			
			
			
			
				   
			$scheduler_data[] = array('sr_no'=>$i,
			'instruments'=>$restyui1->instruments_name,
			'stock'=>$restyui1->stock,
			'orderlist'=>$html);
			$i++;
				
			}
	}
	
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
	
	}
	
	function filteroverallmis()
{
	$start=$this->input->post('start');
	$end=$this->input->post('end');
	redirect(page_url.'Reporting/misscore/'.base64_encode($start).'/'.base64_encode($end));
}

function salespendingorders()
{
   
$this->load->view('FMS/salespendingorder');
}


function salespending_order_list()
{
		    
		    $scheduler_data=array();
	
		$order=$this->db->select('a.order_id,b.internal_order_no,b.added_on,b.company_name,c.first_name,c.last_name')->from('order_instruments a')->join('prestogroup_orders b','a.order_id=b.order_id')->join('order_planning d','d.order_id=b.order_id')->join('system_users c','b.marketing_person=c.user_id')->where('a.complete','0')->where('factory !=','2')->where('b.marketing_person',$_SESSION['logged_in']['user_id'])->group_by('a.order_id')->order_by('b.added_on','ASC')->get();
		
		if($order->num_rows()>0)
		{
			$t=1;
			foreach($order->result() as $order1)
			{
		
		/** GET ALL INCOMPLETE JOBCARD **/
		$html = "<table border='1' style='width:100%;line-height:14px;font-size:11px;'><tr style='background-color:white;'><th style='padding:0px 0px 0px 0px;text-align:center;width:33%;'>INSTRUMENT</th><th style='padding:0px 0px 0px 0px;text-align:center;width:20%;'>JOB CARD NO.</th><th style='padding:0px 0px 0px 0px;text-align:center;width:33%;'>CURRENT STAGE</th><th style='padding:0px 0px 0px 0px;text-align:center;'>APPROX. DAYS TO COMPLETE</th></tr>";
		$inst=$this->db->select('c.factory,b.id,a.instruments_name,b.job_card_no')->from('presto_instruments a')->join('order_instruments b','a.id=b.item_id')->join('order_planning c','b.id=c.jobcard_id')->where('b.complete','0')->where('b.order_id',$order1->order_id)->get();
		if($inst->num_rows()>0)
		{
			
		foreach($inst->result() as $inst1)
		{ 
		if($inst1->factory=='1' || $inst1->factory=='4')
		{
			$odstage=$this->db->select('a.flowstage,b.fms_flow,b.pdays,b.donotshowtosales,b.setorder,b.flow_id,b.production_flow_id')->from('order_stage a')->join('fms_flow b','a.flowstage=b.flow_id')->where('a.jobcardid',$inst1->id)->where('a.orderid',$order1->order_id)->where('a.userstatus','0')->where('a.flowstage!=','1')->order_by('a.flowstage','ASC')->limit(1)->get();
			if($odstage->num_rows()>0)
			{
				$odstages=array();
				foreach($odstage->result() as $odstage1)
				{
				$odstages[]=$odstage1->fms_flow;
				}
				$currentstage=implode(',',$odstages);
				$oddays=$odstage1->pdays." Days";
				$donotshowforsales=$odstage1->donotshowtosales;
				$settorder=$odstage1->setorder;
				
                /** EXCEPTIONAL CASE  FOR FULL KITTING TYPES **/
                if($donotshowforsales=='1')
                {
                    $nextstage=$this->fmsmodel->getnextdaysandstageforsales($odstage1->production_flow_id,$settorder);
                   if(count($nextstage)>0)
                   {
                        $currentstage=$nextstage['stage'];
                        $oddays=$nextstage['days'];
                   }else
                   {
                       $currentstage=$currentstage;
                       $oddays=$oddays;
                   }
                }
                /*** END **/
		
		
			}else{
                $currentstage='';
                $oddays='0';
                $donotshowforsales='';
			}
		}else if($inst1->factory=='2')
		{
			$currentstage='IN STOCK FMS';
				$oddays='0';
		}else if($inst1->factory=='3'){
			
			$currentstage='BOUGHT OUT FMS';
				$oddays='0';
		}else if($inst1->factory=='7'){
		    	$currentstage='IMPORTED ITEMS';
		    	$oddays='0';
		}
		
		
		$getLatestDays = $this->db->select('days,updated_on')
						  ->from('order_days')
						  ->where('job_card_id',$inst1->id)
						  ->order_by('id','DESC')
						  ->limit(1)
						  ->get();
		
	
		
		
 $html.="<tr>";
                $html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($inst1->instruments_name)."</td>";
                $html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($inst1->job_card_no)."</td>";
                $html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($currentstage)."</td>";

                if($getLatestDays->num_rows() > 0) {
                        foreach ($getLatestDays->result() as $getDays);

                    $Date = date('Y-m-d',strtotime($getDays->updated_on));
                    $finaldate=date('d-M-Y', strtotime($Date. ' + '.$getDays->days.' days'));

                    $html .= "<td style='padding:2px 2px 2px 2px; text-align:center;'>".$finaldate;
                    if ($_SESSION['logged_in']['user_id'] == 3) {
                        $html .= "<a href='javascript:;' onclick='editOrderDays(".$inst1->id.")'><i class='fa fa-pencil'></i></a></td>";
                        }
                    } else {


                        $html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($oddays)." Days";
                        if ($_SESSION['logged_in']['user_id'] == 3) {
                        $html .= "<a href='javascript:;' onclick='editOrderDays(".$inst1->id.")'><i class='fa fa-pencil'></i></a></td>";
                        }
                    }


                $html.="</tr>";
		}
		}
		$html.="</table>";
		
		/** END **/
		if($inst->num_rows()>0)
		{
		$scheduler_data[] = array(
				'sr_no'=>$t,
				'custname'=>$order1->company_name,
				'iono'=>$order1->internal_order_no,
				'orderdate'=>date('d-m-Y',strtotime($order1->added_on)),
				'region'=>$order1->first_name." ".$order1->last_name,
				'product'=>$html,
				'currentstage'=>''
				);
			
			$t++;
		}
			}			
			
		
		$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
	
	}else
	{
		$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
		
	}
		}
		
	 public function dispatchfortommorow_order_listforsales_history()
	{
		$scheduler_data = array();
$type=$this->uri->segment(3);

		$userid = $_SESSION['logged_in']['user_id'];
		$this->db->select('a.docketno,a.movedOn,a.order_id, a.order_type, a.marketing_person,a.po_number, a.company_name,a.closedOn, a.address, a.email, a.mobile_number, a.internal_order_no, a.discount, a.order_value_after_discount, a.advance_amount, a.payment_terms, a.installation_charges, a.order_status, a.added_on,a.packing_type, a.installation_type, b.user_id, b.title, b.first_name, b.last_name, a.installation_amount, a.packing_charges, a.packing_amount, a.freight_type, a.freight_amount,a.remarks')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left');
		if($userid<>''){
		    $this->db->where('a.marketing_person',$userid);
		}
		$query = $this->db->where('a.order_status','1')->where('a.movetodispatch','1')->order_by('a.order_id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
		$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1');
		$type="1";
		if($type<>'')
			{
			    if($type=='0')
			    {
			       $this->db->where('finalpacked','0'); 
			    }else
			    {
			         $this->db->where('finalpacked','1');
			    }
			    
			}
			
			$completedjobcard=$this->db->get();
			$completedjobcards=$completedjobcard->num_rows();
			
			$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name,a.finalpacked,a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			$alljobcard=$query1->num_rows();
			if($alljobcard>0)
			{
				foreach($query1->result() as $alluee);
					$finalpack=$alluee->finalpacked;
					
			}else{ $finalpack=0; }
			
		if($alljobcard<>0)
		{
		if($completedjobcards==$alljobcard)
		{
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			
			$html = "<table border='1' style='width:500px;'><tr style='background-color:white;'><th style='padding:2px 2px 2px 2px'>INSTRUMENT</th><th style='padding:2px 2px 2px 2px'>JOB CARD NO.</th></tr>";
			$instrumentsss = array();
			
			
			
			foreach($query1->result() as $instruments){
				if($instruments->complete=='1'){
					$backgroundcolor = "background-color:#10C469; color:white !important; font-weight:bold;";
				}else{
					$backgroundcolor="";
				}
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->instruments_name)."</td>";
				//$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->qty)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->job_card_no)."</td>";
				$html.="</tr>";
			}
			$html.="</table>";
			$ins_charges = "";
			/** Get order planned **/
			$restt=$this->db->select('count(id) as totaljobcard')->from('order_instruments')->where('order_id',$row->order_id)->get();
			foreach($restt->result() as $instcount);
			$totjobcard= $instcount->totaljobcard;
			
			
	
			
			if($row->installation_charges=='1'){
				$charges= "<strong>INSTALLATION TYPE </strong>".$row->installation_type."<br> <strong>INSTALLATION CHARGES</strong> - REQUIRED";
				
				$ins_charges= $charges."<br> AMOUNT - ".$row->installation_amount;
			}else{
					$ins_charges = "NOT REQUIRED";
			}
			
			if($row->packing_charges=='1'){
				$packcharges = "PAID BY PARTY";
				$packingcharges= $packcharges."<br> Amount - <strong>".$row->packing_amount."</strong>";
			}else{
					$packingcharges = "INCLUSIVE";
			}
			
			if($row->freight_type=='1'){
				$freigntcharges = "TO PAY BASIS";
				
			}else if($row->freight_type=='2'){
				$freigntcharges = "PAID BY PRESTO";
			}else if($row->freight_type=='3'){
				$frtcharges = "BILLED IN INVOICE";
				$freigntcharges= $frtcharges."<br> Amount - <strong>".$row->freight_amount."</strong>";
			}else{
				$freigntcharges = "OWN PICK-UP";
					
			}
			
			$closedon=date('d-M-Y g:i A',strtotime($row->movedOn));
			$plannedtime = date('d-M-Y g:i A', strtotime($closedon . ' +1 day'));

if($row->installation_charges==1)
			{
				$ser=1;
			}else{
				
				$ser=0;
			}
			
			
$printpackinglabel='<a href="'.page_url.'Reporting/generatepackingslip/'.$row->order_id.'" target="_blank"><span class="btn btn-success btn-sm">Print Packing Label</span></a>';
			
			if($finalpack=='1')
{
			$restyupacku=$this->db->select('addedOn')->from('order_finalpacking_details')->where('orderid',$row->order_id)->get();	
				if($restyupacku->num_rows()>0)
{
				foreach($restyupacku->result() as $restyupacku2);
				$actualtime=date('d-M-Y g:i A',strtotime($restyupacku2->addedOn));
				
}else{  

	$actualtime="";
					}
			$completeorder="<span class='btn btn-success btn-sm'>Dispatch Done</span>";		
	
}else{  $actualtime=""; 
	 
	  	$completeorder="<span class='btn btn-warning btn-sm'>Dispatch Pending</span>";
	 // $completeorder='<a href="javascript:;" onclick="markstagedone('."'".$row->order_id."'".','."'".strtoupper($row->company_name)."'".','."'".$row->internal_order_no."'".','."'".$ser."'".')"><span class="btn btn-warning btn-sm">Mark as Done</span></a>';

}
	
	
	if($row->docketno<>'')
{
    $dock="<a href='".page_url."image_bank/docketno/".$row->docketno."' download>Docket Image</a>";
}else
{ 
     $dock='';
}

		
			$scheduler_data[] = array('sr_no'=>$i,
			'closeorder'=>$completeorder,
			'docket'=>$dock,
			'packinglabel'=>$printpackinglabel,
			'added_on'=>$closedon,
			 'plannedtime'=>$plannedtime,
			 'actualtime'=>$actualtime,
			'order_type'=>strtoupper($row->order_type),
			'marketing_person'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'po_number'=>strtoupper($row->po_number),
			'company_name'=>strtoupper($row->company_name),
			'address'=>strtoupper($row->address),
			'email'=>strtoupper($row->email),
			'mobile_number'=>strtoupper($row->mobile_number),
			'internal_order_no'=>strtoupper($row->internal_order_no),
			'itemname'=>$html,
			'discount'=>strtoupper($row->discount)."%",
			'order_value_after_discount'=>strtoupper($row->order_value_after_discount),
			'advance_amount'=>strtoupper($row->advance_amount),
			'payment_terms'=>strtoupper($row->payment_terms),
			'installation_charges'=>strtoupper($ins_charges),
			'packingcharges'=>"<strong>Packing Type</strong> -".$row->packing_type."<br>".$packingcharges,
			'freigntcharges'=>$freigntcharges,
			'remarks'=>$row->remarks);
			$i++;
		}
		}
	}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}


function reorderlist()
{
   
$this->load->view('FMS/reorderlist');
	
}

function reorderlisting()
{
	$scheduler_data = array();
	$restyu=$this->db->select('instruments_name,type,model_number,file_number,stock,minstock,id')->from('presto_instruments')->where('stock<minstock')->where('minstock !=','0')->where_in('type','0','1',false)->order_by('instruments_name','ASC')->get();
	if($restyu->num_rows()>0)
	{
		$i=1;
		foreach($restyu->result() as $restyui1)
		{
			$alreadyo=$this->checkifreorderalreadyinplace($restyui1->id);
            $cur=$restyui1->stock;
			$min=$restyui1->minstock;
			$odq=$min-$cur;
			
            $odq=$odq-$alreadyo;
            
            
			
		$reorder="<a href='".page_url."FMS/reordermachine/".$restyui1->id."/".$odq."'><span class='btn btn-success'>Reorder</span></a>";
	
		if($odq>0)
		{
		$scheduler_data[] = array(
		
		'sr_no'=>$i,
		'ins'=>$restyui1->instruments_name,
		'type'=>$restyui1->model_number,
		'currstock'=>$restyui1->stock,
		'minstock'=>$restyui1->minstock,
		'runningorder'=>$alreadyo,
		'reorderqty'=>"<span style='color:red;font-weight:bold;'>".$odq."</span>",
		'reorder'=>$reorder);
			
			
		$i++;
		}
		}
		
		
		
		
	}
	
	
	
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
	
	
	
}

function checkifreorderalreadyinplace($id)
{
   	$restyu=$this->db->select('a.id,b.order_id')->from('order_instruments a')->join('prestogroup_orders b','a.order_id=b.order_id')->where('b.selforder','1')->where('b.order_status','1')->where('a.item_id',$id)->where('a.complete','0')->get();
        
        /** if($id=='101')
        {
        foreach($restyu->result() as $restyu1);
       echo $restyu1->order_id.'<br/>';exit;
        } **/
    
	    return $restyu->num_rows();
	
	
}


function checkifsinglejobcardsareready($jbid)
{
    
        $ordid1=$this->db->select('id')->from('order_instruments')->where('id',$jbid)->where('complete','1')->get();
    
       $completedjobcard=$ordid1->num_rows();
       
       if($completedjobcard>0)
       {
       return true;
       }else
       {
           return false;
       }
    
    
}

function finishedgoods()
{
    
    $this->load->view('FMS/finishedgoods');
    
}



public function finishgoodsvalue()
	{
		$scheduler_data = array();
		$html='';
		$type = "'0','1'";
			$restyui=$this->db->select('id, instruments_name,stock,mvalue')->from('presto_instruments')->where_in('type',$type,false)->where('status','1')->get();
		if($restyui->num_rows()>0)
		{
		    $html="";
			$i=1;
			foreach($restyui->result() as $restyui1)
			{
            
            $Restyut=$this->db->select('a.id')->from('order_instruments a')->join('prestogroup_orders b','a.order_id=b.order_id')->where('a.item_id',$restyui1->id)->where('a.complete','1')->where('a.finalpacked','0')->where('b.closeorder','0')->where('a.item_id',$restyui1->id)->get();
          
            $orders=$Restyut->num_rows();
            $stock=$restyui1->stock;
               
             $totstock= $orders+$stock;
             
             /** GET 75% OF VALUE **/
             if($restyui1->mvalue>0)
             {
             $val=$restyui1->mvalue;
             $seventyfive=75/100;
             $finval=$val-$seventyfive;
             }else
             {
               $finval=0; 
             }
             
             $machinetotval=$finval*$totstock;
             /** END **/
        
        
			$scheduler_data[] = array('sr_no'=>$i,
			'instruments'=>$restyui1->instruments_name,
			'readymachines'=>$orders,
			'stock'=>$stock,
			'valper'=>'75%',
			'totalvalue'=>$machinetotval);
			$i++;
				
			}
	}
	
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
	
	}
	
	function minimumlevelitems()
	{
	    
	    $this->load->view('store/minimumlevelitems');
	    
	}
	
	
	function maximumlevelitem()
{
    
    
    $this->load->view('store/maximumlevelitem');

}


	function emdone()
	{
	    
	    $uid=$this->input->post('userid');
	    $s=$this->uri->segment('sdate');
	    $e=$this->uri->segment('edate');
	    
	    $da=array('userid'=>$uid,'emdate'=>date('Y-m-d'),'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
	    
	    $this->db->insert('misemdone',$da);
	    
	    if($this->db->affected_rows()>0)
	    {
	        echo "1";
	    }else
	    {
	         echo "0";
	    }
	    
            
            
	}
	
	function criticalitems()
	{
	   $this->load->view('store/criticalitem');
	}
	
	function checkforduplication($flowstage)
	{
	        $jbcard=array();
	    	$data=$this->db->select('a.jobcardid,b.job_card_no,c.instruments_name,c.mvalue')->from('order_stage a')->join('order_instruments b','a.jobcardid=b.id')->join('presto_instruments c','b.item_id=c.id')->where('a.flowstage',$flowstage)->where('a.userstatus','0')->get();
	    	if($data->num_rows()>0)
	    	{
	    	    foreach($jbcard->result() as $jbcard1)
	    	    {
	    	        $jbcard[]=$jbcard1->jobcardid;
	    	    }
	    	    
	    	    
	    	}
	    
	    
	    return $jbcard;
	}
	
	function getsuper($productionid,$flowid)
	{
	    
	    $Restst=$this->db->select('flow_id,setorder')->from('fms_flow')->where('flowid',$flowid)->where('super','1')->where('base','1')->where('production_flow_id',$productionid)->get();
	    if($Restst->num_rows()>0)
	    {
	        foreach($Restst->result() as $Restst1);
	        return $Restst1->setorder;
	    }else
	    {
	    return "NA";
	    }
	    
	}
	


function servicefilterlist()
{
    $this->load->view('FMS/servicerequestfilter');
    
    
}


public function filterservicerequestlist()
	{
	    $type=$this->uri->segment(3);
	   
		$scheduler_data = array();
        $query = $this->db->select('a.order_id, a.order_type, a.marketing_person,a.po_number, a.company_name,a.closedOn, a.address, a.email, a.mobile_number, a.internal_order_no, a.discount, a.order_value_after_discount, a.advance_amount, a.payment_terms, a.installation_charges, a.order_status, a.added_on,a.packing_type, a.installation_type, b.user_id, b.title, b.first_name, b.last_name, a.installation_amount, a.packing_charges, a.packing_amount, a.freight_type, a.freight_amount,a.remarks')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left')->where('a.order_status','1')->where('a.closeorder','0')->where('a.installation_charges','1')->order_by('a.order_id','desc')->get();
        $res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$completedjobcard=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1')->get();
			$completedjobcards=$completedjobcard->num_rows();
			
			$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name,a.finalpacked,a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			$alljobcard=$query1->num_rows();
			if($alljobcard>0)
			{
				foreach($query1->result() as $alluee);
					$finalpack=$alluee->finalpacked;
					
			}else{ $finalpack=0; }
			
		if($completedjobcards==$alljobcard)
		{
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			
			$html = "<table border='1' style='width:500px;'><tr style='background-color:white;'><th style='padding:2px 2px 2px 2px'>INSTRUMENT</th><th style='padding:2px 2px 2px 2px'>JOB CARD NO.</th></tr>";
			$instrumentsss = array();
			
			
			$finalpacked=array();
			foreach($query1->result() as $instruments){
				if($instruments->complete=='1'){
					$backgroundcolor = "background-color:green; color:white !important; font-weight:bold;";
				}else{
					
					$backgroundcolor="";
				}
				
				if($instruments->finalpacked=='1')
					{
				$finalpacked[]=1;
					}else
					{
				$finalpacked[]=0;
					}
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->instruments_name)."</td>";
				//$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->qty)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->job_card_no)."</td>";
				$html.="</tr>";
				
			}
			$html.="</table>";
			$ins_charges = "";
			/** Get order planned **/
			$restt=$this->db->select('count(id) as totaljobcard')->from('order_instruments')->where('order_id',$row->order_id)->get();
			foreach($restt->result() as $instcount);
			$totjobcard= $instcount->totaljobcard;
			
			
	
			
			if($row->installation_charges=='1'){
				$charges= "<strong>INSTALLATION TYPE </strong>".$row->installation_type."<br> <strong>INSTALLATION CHARGES</strong> - REQUIRED";
				
				$ins_charges= $charges."<br> AMOUNT - ".$row->installation_amount;
			}else{
					$ins_charges = "NOT REQUIRED";
			}
			
			if($row->packing_charges=='1'){
				$packcharges = "PAID BY PARTY";
				$packingcharges= $packcharges."<br> Amount - <strong>".$row->packing_amount."</strong>";
			}else{
					$packingcharges = "INCLUSIVE";
			}
			
			if($row->freight_type=='1'){
				$freigntcharges = "TO PAY BASIS";
				
			}else if($row->freight_type=='2'){
				$freigntcharges = "PAID BY PRESTO";
			}else if($row->freight_type=='3'){
				$frtcharges = "BILLED IN INVOICE";
				$freigntcharges= $frtcharges."<br> Amount - <strong>".$row->freight_amount."</strong>";
			}else{
				$freigntcharges = "OWN PICK-UP";
					
			}
			
			

			
$restynxjs=$this->db->select('addedOn')->from('service_details')->where('order_id',$row->order_id)->get();
			if($restynxjs->num_rows()>0)
{
$printpackinglabel="Task has been done";
				foreach($restynxjs->result() as $restynxjs11);
$actualtime=date('d-M-Y g:i A',strtotime($restynxjs11->addedOn));
							
$completeorder='Task has been done';
}else
{

		
				$actualtime='';
				
			
$completeorder=' <button class="btn btn-warning btn-xs waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$i.'">Update</button>';
$completeorder.= '<div id="con-close-modal'.$i.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
           <form id="loginForm" method="post" action="'.page_url.'Reporting/markservicedone/'.$row->order_id.'">
  
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">UPDATE STATUS</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                               <div class="col-md-3">
													<div class="form-group">
														<label for="field-1" class="control-label">Status</label><br>
														<select class="form-control" name="status" id="status'.$i.'" onchange="onchangestatus('.$i.');">
															<option value="1">TASK COMPLETE</option>
															<option value="0">WORK IN PROGRESS</option>
															<option value="2">CUSTOMER END PENDING</option>
										<option value="5">ENGINEER VISIT REQUIRED</option>
														</select>
													</div> 
												</div>
												<script>
												function onchangestatus(i){
												if($("#status"+i).val() == "2" || $("#status"+i).val()=="0") {
												
												$("#remarksbox"+i).show(); 
												$("#followupbox"+i).show();
												$("#remarks"+i).attr("required",true);
												$("#next_followup_date"+i).attr("required",true);
                                            } else if($("#status"+i).val() == "3") { 
                                            $("#remarksbox"+i).show(); 
                                            $("#remarks"+i).attr("required",true);
                                            }else
												{
												$("#remarksbox"+i).hide(); 
												$("#followupbox"+i).hide(); 
												$("#remarks"+i).attr("required",false);
												$("#next_followup_date"+i).attr("required",false);
												} 
												}
												
											</script>
												 <div class="col-md-6" style="display:none" id="remarksbox'.$i.'">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARKS</label><br>
														<span id="error_color_name" style="color:red;"></span>
                                                       <textarea class="form-control" name="remarks" id="remarks" style="width:400px" required></textarea>
                                                    </div>
                                                </div>
												
												
												<div class="col-md-3" style="display:none" id="followupbox'.$i.'">
													<div class="form-group">
														<label>Follow-up Date</label>
														<input class="form-control" type="date" name="next_followup_date" id="next_followup_date" value="" min="'.date("Y-m-d").'">
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
}
			

			
			if(!in_array("0", $finalpacked)) 
				{
		$restyupacku=$this->db->select('addedOn')->from('order_finalpacking_details')->where('orderid',$row->order_id)->get();	
				if($restyupacku->num_rows()>0)
{
				foreach($restyupacku->result() as $restyupacku2)
				$plannedtime = date('d-M-Y g:i A', strtotime($restyupacku2->addedOn . ' +7 day'));
					$closedon=date('d-M-Y g:i A',strtotime($restyupacku2->addedOn));
					
}else
{
			$plannedtime="";	
					$closedon='';
}


$dura='';
$followss='100';
$q= $this->db->select('next_followup,remarks,status')->from('service_request_followup')->where('record_id',$row->order_id)->limit(1)->order_by('id','desc')->get();

if($q->num_rows()>0){
    foreach($q->result() as $follow);
    $nxtfollowup = date('d-m-Y',strtotime($follow->next_followup))."<br><br>".$follow->remarks;
   $followss=$follow->status;
   
   if($follow->status=='1')
   {
       $currsta="Complete";
       
   }else  if($follow->status=='0')
   {
       $currsta="Work In Progress";
       
   }else if($follow->status=='2')
   {
       $currsta="Customer End Pending";
       
   }else if($follow->status=='5')
   {
       $currsta="Engineer Visit Required";
       
   }
}else{

$followss='100';
$generatedtime=date('Y-m-d H:i:s',strtotime($closedon));
$onedayold=date('Y-m-d H:i:s', strtotime("+1 day", strtotime($generatedtime)));
$ti1 = strtotime(date('Y-m-d H:i:s'));
$ti2 = strtotime($onedayold);
$hour = abs($ti2 - $ti1)/(60*60);

if($hour<='24')
{
    $dura="NEW";
    $followss='4';
}else
{
     $dura="";
}
    $nxtfollowup = "";
    $currsta="Incomplete";
    $followss='3';
}
	
	//echo $nxtfollowup; exit;	

	if($type==$followss)
	{
	    
	    
			$scheduler_data[] = array('sr_no'=>$i,
			'closeorder'=>$completeorder,
			'currstatus'=>$currsta,
			'type'=>$dura,
			'nextfollowup'=>$nxtfollowup,			
			'added_on'=>$closedon,
			 'plannedtime'=>$plannedtime,
			 'actualtime'=>$actualtime,
			'order_type'=>strtoupper($row->order_type),
			'marketing_person'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'po_number'=>strtoupper($row->po_number),
			'company_name'=>strtoupper($row->company_name),
			'address'=>strtoupper($row->address),
			'email'=>strtoupper($row->email),
			'mobile_number'=>strtoupper($row->mobile_number),
			'internal_order_no'=>strtoupper($row->internal_order_no),
			'itemname'=>$html,
			'discount'=>strtoupper($row->discount)."%",
			'order_value_after_discount'=>strtoupper($row->order_value_after_discount),
			'advance_amount'=>strtoupper($row->advance_amount),
			'payment_terms'=>strtoupper($row->payment_terms),
			'installation_charges'=>strtoupper($ins_charges),
			'packingcharges'=>"<strong>Packing Type</strong> -".$row->packing_type."<br>".$packingcharges,
			'freigntcharges'=>$freigntcharges,
			'remarks'=>$row->remarks);
			$i++;
	    
			
	}
				}
		}
	}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}

function serviceengineerrequest()
{
    $this->load->view('FMS/serviceengineerrequestforhod');
}


public function enginner_request_list()
	{
		$scheduler_data = array();
		
		
		$rresytye=$this->db->select('record_id')->from('service_request_followup')->where('status','5')->where('closedbyhod','0')->get();
		if($rresytye->num_rows()>0)
		{
		
		foreach($rresytye->result() as $rresytye1)
		{
		
        $query = $this->db->select('a.order_id, a.order_type, a.marketing_person,a.po_number, a.company_name,a.closedOn, a.address, a.email, a.mobile_number, a.internal_order_no, a.discount, a.order_value_after_discount, a.advance_amount, a.payment_terms, a.installation_charges, a.order_status, a.added_on,a.packing_type, a.installation_type, b.user_id, b.title, b.first_name, b.last_name, a.installation_amount, a.packing_charges, a.packing_amount, a.freight_type, a.freight_amount,a.remarks')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left')->where('a.order_status','1')->where('a.order_id',$rresytye1->record_id)->order_by('a.order_id','desc')->get();
        $res = $query->result();
		$i=1;
		foreach($res as $row);
		
			$completedjobcard=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1')->get();
			$completedjobcards=$completedjobcard->num_rows();
			
			$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name,a.finalpacked,a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			$alljobcard=$query1->num_rows();
			if($alljobcard>0)
			{
				foreach($query1->result() as $alluee);
					$finalpack=$alluee->finalpacked;
					
			}else{ $finalpack=0; }
			
		if($completedjobcards==$alljobcard)
		{
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			
			$html = "<table border='1' style='width:500px;'><tr style='background-color:white;'><th style='padding:2px 2px 2px 2px'>INSTRUMENT</th><th style='padding:2px 2px 2px 2px'>JOB CARD NO.</th></tr>";
			$instrumentsss = array();
			
			
			$finalpacked=array();
			foreach($query1->result() as $instruments){
				if($instruments->complete=='1'){
					$backgroundcolor = "background-color:green; color:white !important; font-weight:bold;";
				}else{
					
					$backgroundcolor="";
				}
				
				if($instruments->finalpacked=='1')
					{
				$finalpacked[]=1;
					}else
					{
				$finalpacked[]=0;
					}
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->instruments_name)."</td>";
				//$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->qty)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->job_card_no)."</td>";
				$html.="</tr>";
				
			}
			$html.="</table>";
			$ins_charges = "";
			/** Get order planned **/
			$restt=$this->db->select('count(id) as totaljobcard')->from('order_instruments')->where('order_id',$row->order_id)->get();
			foreach($restt->result() as $instcount);
			$totjobcard= $instcount->totaljobcard;
			
			
	
			
			if($row->installation_charges=='1'){
				$charges= "<strong>INSTALLATION TYPE </strong>".$row->installation_type."<br> <strong>INSTALLATION CHARGES</strong> - REQUIRED";
				
				$ins_charges= $charges."<br> AMOUNT - ".$row->installation_amount;
			}else{
					$ins_charges = "NOT REQUIRED";
			}
			
			if($row->packing_charges=='1'){
				$packcharges = "PAID BY PARTY";
				$packingcharges= $packcharges."<br> Amount - <strong>".$row->packing_amount."</strong>";
			}else{
					$packingcharges = "INCLUSIVE";
			}
			
			if($row->freight_type=='1'){
				$freigntcharges = "TO PAY BASIS";
				
			}else if($row->freight_type=='2'){
				$freigntcharges = "PAID BY PRESTO";
			}else if($row->freight_type=='3'){
				$frtcharges = "BILLED IN INVOICE";
				$freigntcharges= $frtcharges."<br> Amount - <strong>".$row->freight_amount."</strong>";
			}else{
				$freigntcharges = "OWN PICK-UP";
					
			}
		
$completeorder='<a href="'.page_url.'Sales/visit_form/'.$row->order_id.'"><span class="btn btn-warning btn-xs">Schedule Visit</span></a>';
		
			if(!in_array("0", $finalpacked)) 
				{
		$restyupacku=$this->db->select('addedOn')->from('order_finalpacking_details')->where('orderid',$row->order_id)->get();	
				if($restyupacku->num_rows()>0)
{
				foreach($restyupacku->result() as $restyupacku2)
				$plannedtime = date('d-M-Y g:i A', strtotime($restyupacku2->addedOn . ' +7 day'));
					$closedon=date('d-M-Y g:i A',strtotime($restyupacku2->addedOn));
					
}else
{
			$plannedtime="";	
					$closedon='';
}


	//echo $nxtfollowup; exit;			
			$scheduler_data[] = array('sr_no'=>$i,
			'closeorder'=>$completeorder,
			'order_type'=>strtoupper($row->order_type),
			'marketing_person'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'po_number'=>strtoupper($row->po_number),
			'company_name'=>strtoupper($row->company_name),
			'address'=>strtoupper($row->address),
			'email'=>strtoupper($row->email),
			'mobile_number'=>strtoupper($row->mobile_number),
			'internal_order_no'=>strtoupper($row->internal_order_no),
			'itemname'=>$html,
			'discount'=>strtoupper($row->discount)."%",
			'order_value_after_discount'=>strtoupper($row->order_value_after_discount),
			'advance_amount'=>strtoupper($row->advance_amount),
			'payment_terms'=>strtoupper($row->payment_terms),
			'installation_charges'=>strtoupper($ins_charges),
			'packingcharges'=>"<strong>Packing Type</strong> -".$row->packing_type."<br>".$packingcharges,
			'freigntcharges'=>$freigntcharges,
			'remarks'=>$row->remarks);
			$i++;
				}
		}
	}
	}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}
	
	
	function getfilenofromplanning($jobcardid)
	{
	    $jbcid=$this->db->select('fileno')->from('order_planning')->where('jobcard_id',$jobcardid)->get();
	    if($jbcid->num_rows()>0)
	    {
	        foreach($jbcid->result() as $jbciddi);
	        
	        return $jbciddi->fileno;
	         
	    }else
	    {
	        return "";
	    }
	    
	}
	
	
		function getservicepartname($id,$type)
	{
		if($type=='1')
		{
		$restyeyue=$this->db->select('part')->from('machine_parts_with_picture')->where('id',$id)->get();
		if($restyeyue->num_rows()>0)
		{
			foreach($restyeyue->result() as $restyeyue1);
			
			return $restyeyue1->part;
			
		}else{
			
			
			return "";
		}
		}else{
			
			$restyeyue=$this->db->select('instruments_name')->from('presto_instruments')->where('id',$id)->get();
		if($restyeyue->num_rows()>0)
		{
			foreach($restyeyue->result() as $restyeyue1);
			
			return $restyeyue1->instruments_name;
			
		}else{
			
			
			return "";
		}
			
		}
		
		
	}
	    
	
	public function pending_indent_dashboard(){
	$this->load->view('store/pending_indent');
}	

function pending_indent_for_review()
{
	$scheduler_data = array();
		$htm="";
		
		$rest=$this->db->select('a.id as intendid,a.addedOn,a.unit,a.prefix,a.indendno,e.first_name,e.last_name,a.itemid, a.indent_type,a.remarks')->from('intend_request a')->join('system_users e','e.user_id=a.addedBy')->where('a.approvalstatus','0')->where('a.cancel_status','0')->order_by('a.addedOn','DESC')->group_by('a.indendno')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				
				$unitname=$this->getunit($restyui1->unit);
				if($restyui1->indent_type=='1'){
					$indenttype = "Machine Related Items";
				$rest123=$this->db->select('b.part as item_name,a.qty,a.unit,b.specification,b.size_in_mm')->from('intend_request a')->join('machine_parts_with_picture b','a.itemid=b.id')->where('a.indendno',$restyui1->indendno)->get();	
				}else{
					$indenttype = "General Items";
				$rest123=$this->db->select('b.item_name,a.qty,a.unit')->from('intend_request a')->join('house_keeping_items b','a.itemid=b.id')->where('a.indendno',$restyui1->indendno)->get();	
				}
				
				
				if($rest123->num_rows()>0)
				{
					$htm='';
					$htm.="<table border='1' style='width:700px;'><tr style='background-color:white;text-align:center;width:100px;'><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;width:200px;'>ITEM.</th><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;width:100px;'>QTY.</th>";

					if($restyui1->indent_type=='1'){
					$htm.="<th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;width:300px;'>SPECS.</th></tr>";
				}
					foreach($rest123->result() as $rest1231)
					{
					
						
							$htm.="<tr>";
							$htm.="<td style='padding:2px 2px 2px 2px; text-align:center;width:200px;'>".strtoupper($rest1231->item_name)."</td>";

							$htm.="<td style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>".strtoupper($rest1231->qty)." ".$unitname."</td>";

							if($restyui1->indent_type=='1'){
							$htm.="<td style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>".$rest1231->specification."<br/>Size: ".$rest1231->size_in_mm."</td>";
							}
							$htm.="</tr>";
					}
					
					$htm.="</table>";
			}
			
			
			
$html='<a href="'.page_url.'Reporting/approvependingindents/'.$restyui1->indendno.'" class="btn btn-warning btn-xs">Mark as Approved</a>';

$cancel = '<a href="'.page_url.'Reporting/cancel_indent/'.$restyui1->indendno.'"><span class="btn btn-danger btn-xs">Cancel</span></a>';

			$scheduler_data[] = array('sr_no'=>$i,
			'indent_type'=>$indenttype,
			'prno'=>$restyui1->prefix.'-'.$restyui1->indendno,
			'itemdetail'=>$htm,
			'remarks'=>$restyui1->remarks,
			'createdby'=>ucfirst($restyui1->first_name." ".$restyui1->last_name),
			'createdon'=>date('d-M-Y g:i A',strtotime($restyui1->addedOn)),
			'markrecvd'=>$html,
			'cancel'=>$cancel);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
	


}	







function approvependingindents(){
	date_default_timezone_set("Asia/Kolkata");
$data = array('approvalstatus'=>'1',
'approvedOn'=>date('Y-m-d H:i:s'),
'addedBy'=>$_SESSION['logged_in']['user_id']);
$this->db->where('indendno',$this->uri->segment(3));
$this->db->update('intend_request',$data);

$this->generateprfromintendautoapproval($this->uri->segment(3));

$this->session->set_flashdata('message','<div class="alert alert-success">Thank You! Record successfully updated.</div>');
	redirect(page_url.'Reporting/pending_indent_dashboard');

	
}


function getpotype($pono)
{
	
	$resytyuuiw=$this->db->select('potype')->from('purchase_order')->where('pono',$pono)->get();
	if($resytyuuiw->num_rows()>0)
	{
		foreach($resytyuuiw->result() as $resytyuuiw1);
		
		$source=$resytyuuiw1->potype;
		
		return $source;
	}else{
		
		echo "PO NOT FOUND";exit;
	}
	
}

function pending_po_followuplist()
{
$followupdata = array();
		$rest=$this->db->select('a.id,a.po_no, a.followupdate, b.vendor,b.potype,b.itemid, c.name')->from('vendor_followup a')->join('purchase_order b','a.po_no=b.pono','left')->join('vendors c','b.vendor=c.id','left')->where('a.followup_status','0')->where('a.todays_followup_status','0')->where('a.followupdate',date('Y-m-d'))->group_by('a.po_no')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
	



$html='<button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$restyui1->id.'">Update follow-up</button>';

$html.= '<div id="con-close-modal'.$restyui1->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="'.page_url.'Reporting/updatefollowup/'.$restyui1->id.'" onsubmit="return validateme();">
 <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Update follow-up remarks</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Remark</label><br>
														<span id="error_remarks" style="color:red;"></span>
                                                        <textarea class="form-control" style="width:760px" name="remarks" id="remarks" required></textarea>
                                                    </div>
                                                </div>
												
												 <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Docket Number</label><br>
														<span id="error_datepicker1" style="color:red;"></span>
                                                        <input type="text" id="docket_no" name="docket_no" class="form-control" value="">
                                                    </div>
                                                </div>
												
												 <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Is it Closed?</label><br>
														<span id="error_datepicker1" style="color:red;"></span>
                                                        <select class="form-control" name="isitdone" id="isitdone">
														<option value="0">NO</option>
														<option value="1">YES</option></select>
                                                    </div>
                                                </div>
												
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit" > 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>';	

$htm="";

if($restyui1->potype=='0'){
					$indenttype = "Machine Related Items";
				$rest123=$this->db->select('b.part as item_name,a.qty,a.unit')->from('intend_request a')->join('machine_parts_with_picture b','a.itemid=b.id')->where('b.id',$restyui1->itemid)->get();	
				}else{
					$indenttype = "General Items";
				$rest123=$this->db->select('b.item_name,a.qty,a.unit')->from('intend_request a')->join('house_keeping_items b','a.itemid=b.id')->where('b.id',$restyui1->itemid)->get();	
				}
				
				
				if($rest123->num_rows()>0)
				{
					$htm='';
					$htm.="<table border='1' style='width:500px;'><tr style='background-color:white;text-align:center;'><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;'>ITEM.</th><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;'>QTY.</th></tr>";
					foreach($rest123->result() as $rest1231)
					{
					
						
							$htm.="<tr>";
							$htm.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($rest1231->item_name)."</td>";

							$htm.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($rest1231->qty)."</td>";
							$htm.="</tr>";
					}
					
					$htm.="</table>";
			}




			$followupdata[] = array('sr_no'=>$i,
			'prno'=>$restyui1->po_no,
			'itemdetail'=>$htm,
			'vendor_name'=>$restyui1->name,
			'followdate'=>$restyui1->followupdate,
			'status'=>$html);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($followupdata),
			"iTotalDisplayRecords" => count($followupdata),
			"aaData"=>$followupdata);
			echo json_encode($results);
	
	



}



function generateprfromintendautoapproval($indentno)
{

	$prnos=$this->db->query('SELECT id, purno FROM purchase_request ORDER BY  CAST(purno AS decimal) DESC LIMIT 1');
	$num=$prnos->num_rows();
	if($num==0)
	{
		$num1=1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPR'.$num_padded;
	}else{
	     foreach($prnos->result() as $prnoss);
	    $numpart = $prnoss->purno;
	    	$num1=$numpart+1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPR'.$num_padded;
		
	}
	
	
	$resty=$this->db->select('itemid,qty,unit, indent_type')->from('intend_request')->where('indendno',$indentno)->get();
	if($resty->num_rows()>0)
	{
		
		foreach($resty->result() as $resty1)
		{
			/** if indent type is 1 then this is machine indent*/
			/** if indent type is 2 then this is general Item indent*/
			if($resty1->indent_type=='1'){
				$indenttype = "0";
			}else if($resty1->indent_type=='2'){
				$indenttype = "1";
			}
			
			$prnumonly=preg_replace('/[^0-9]/', '', $code);
			$data=array('masterid'=>$resty1->itemid,'type'=>$indenttype,'itemid'=>$resty1->itemid,'source'=>'2','sourceid'=>$indentno,'prno'=>$code,'qty'=>$resty1->qty,'unit'=>$resty1->unit,'addedBy'=>$_SESSION['logged_in']['user_id'],'addedOn'=>date('Y-m-d H:i:s'),'purno'=>$prnumonly);
			
			$this->db->insert('purchase_request',$data);
			
		}
		
		/** UPDATE INTEND APPROVAL **/
		$datau=array('approvalstatus'=>'1','approvedOn'=>date('Y-m-d H:i:s'),'approvedBy'=>$_SESSION['logged_in']['user_id'],'pr_status'=>'1');
		$this->db->where('indendno',$indentno);
		$this->db->update('intend_request',$datau);
		/** END **/
	
		
		
	}else{
		
		echo "INDEND NOT AVAILABLE";exit;
	}
	
	
	
}


public function updatefollowup(){
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$id = $this->uri->segment(3);

	$pono = $this->uri->segment(4);
	$itemid = $this->uri->segment(5);
	$poid = $this->uri->segment(6);
	$expecteddate = date('Y-m-d',strtotime($this->input->post('expected_date')));
		$previousfollowupdate = date('Y-m-d',strtotime($this->input->post('previousfollowupdate')));
	$delivery_detail= $this->input->post('delivery_detail');
	$deldays=$this->input->post('deliverydays');

	if($this->input->post('isitdone')=='4'){
       // $now = time(); 
       $now=strtotime($previousfollowupdate);
        //echo $expecteddate;exit;
        $your_date = strtotime($expecteddate);
        $datediff = $your_date-$now;
        $totaldays =  round($datediff / (60 * 60 * 24));
        $seconddatescount = floor($totaldays/2);
       
        $thirddatescount = $totaldays-1;
        $seconddate = "+ ".$seconddatescount." days";
        $secondfollowup = date('Y-m-d',strtotime($seconddate));
        $thirddate = "+ ".$thirddatescount." days";
        $thirddatefollowup = date('Y-m-d',strtotime($thirddate));
        
      
 
        $data = array('po_no'=>$pono,
       'followupdate'=>$secondfollowup,
       'itemid'=>$itemid,
       'followup_status'=>'0',
       'todays_followup_status'=>'0',
       'followup_close'=>'0',
       'po_status'=>'0',
       'poid'=>$poid);
       
       $this->db->insert('vendor_followup',$data);
        $data1 = array('po_no'=>$pono,
       'followupdate'=>$thirddatefollowup,
       'followup_status'=>'0',
       'itemid'=>$itemid,
       'todays_followup_status'=>'0',
       'followup_close'=>'0',
       'po_status'=>'0',
       'poid'=>$poid);
       $this->db->insert('vendor_followup',$data1);
       
       /** CLOSE PREVIOUS FOLLOWUP **/
       
       
	}
	
	
	if($this->input->post('isitdone')=='3'){
	    $expecteddate = date('Y-m-d',strtotime($this->input->post('expecteddateofdelivery')));
         $now=strtotime($previousfollowupdate);
        $your_date = strtotime($expecteddate);
        $datediff = $your_date-$now;
        $totaldays =  round($datediff / (60 * 60 * 24));
        $seconddatescount = floor($totaldays/2);
        $thirddatescount = $totaldays-1;
        $seconddate = "+ ".$seconddatescount." days";
        $secondfollowup = date('Y-m-d',strtotime($previousfollowupdate." ".$seconddate));
        $thirddate = "+ ".$thirddatescount." days";
        $thirddatefollowup = date('Y-m-d',strtotime($previousfollowupdate." ".$thirddate));
  
        /*Delete record*/
        
        $this->db->where('id>',$id);
        $this->db->where('po_no',$pono);
        $this->db->where('itemid',$itemid);
        $this->db->where('poid',$poid);
        $this->db->delete('vendor_followup');
        /*Delete record*/
        
        $data = array('po_no'=>$pono,
       'followupdate'=>$secondfollowup,
       'followup_status'=>'0',
       'itemid'=>$itemid,
       'expected_date'=>$expecteddate,
       'todays_followup_status'=>'0',
       'followup_close'=>'0',
       'po_status'=>'0',
       'poid'=>$poid);
	   //echo "<pre>"; print_r($data); exit;
       $this->db->insert('vendor_followup',$data);
       
       
        $data1 = array('po_no'=>$pono,
       'followupdate'=>$thirddatefollowup,
       'followup_status'=>'0',
       'itemid'=>$itemid,
       'todays_followup_status'=>'0',
       'followup_close'=>'0',
       'po_status'=>'0',
       'poid'=>$poid);
       $this->db->insert('vendor_followup',$data1);
       
       /** GET PO APPROVAL DATE **/
       $approvaldate=$this->getpoapprovaldate($pono,$itemid);
          $fdelat = "+ ".$deldays." days";
        $firstdate = date('Y-m-d',strtotime($approvaldate." ".$fdelat));
       /** END **/
       
       $data2 = array(
          'first_date'=>$firstdate,
       'second_date'=>$expecteddate,
       'po_no'=>$pono,
       'item_id'=>$itemid,
       'poid'=>$poid);
       $this->db->insert('vendor_followup_delay_history',$data2);
       
	}


	
	if($this->input->post('isitdone')=='1'){
		
		$data = array('remarks'=>$this->input->post('remarks'),
	'docket_number'=>$this->input->post('docket_no'),
	'followup_status'=>$this->input->post('isitdone'),
	'todays_followup_status'=>'1',
		'followup_close'=>'1');
$this->db->where('po_no',$pono);
$this->db->where('itemid',$itemid);
$this->db->where('poid',$poid);
$this->db->update('vendor_followup',$data);
	
		date_default_timezone_set('Asia/Kolkata');
		/* PICKUP SCHEDULER*/
		if($delivery_detail=='By Presto'){
			$data2 = array('pono'=>$pono,
			'status'=>'0',
			'added_on'=>date('Y-m-d h:i:s'),
			'added_by'=>$user_id,
			'poid'=>$poid);
			$this->db->insert('delivery_boy_schedule',$data2);
			
		}
		
	
	}
	
    /** UPDATE STATUS **/
    
    $upfinaldata=array('followup_status'=>$this->input->post('isitdone'),'todays_followup_status'=>'1');
    
    $this->db->where('id',$id);
    $this->db->update('vendor_followup',$upfinaldata);
    
    
    /** END **/
	
	$this->session->set_flashdata('message','<div class="alert alert-success">Thank You! Record successfully added.</div>');
	redirect(page_url.'Store/followup_dashboard');
	
}

function polisthistory()
{
$date = date('Y-m-d')." 00-00-00";
$todays = date('Y-m-d');
$d2 = date('Y-m-d', strtotime('-90 days'));
$seconddate = $d2." 00-00-00";
$scheduler_data = array();
    $genpo=base64_decode($this->uri->segment(3));
    $genp=explode(',',$genpo);
		$rest=$this->db->select('a.approvedOn,a.jobcard,a.prno,a.source,a.sourceid,a.potype,a.addedOn,a.pono,e.first_name,e.last_name,a.itemid,e.department_id, v.name, v.address,a.approved')->from('purchase_order_view a')->join('system_users_view e','e.user_id=a.addedBy','left')->join('vendors_view v','a.vendor=v.id','left')->where('a.approvedOn BETWEEN "'.$seconddate. '" and "'.$date.'"')->where('a.approved !=','0')->order_by('a.approvedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				if(in_array($restyui1->pono,$genp))
				{
					$bc="red";
				}else{
					$bc='';
				}
				
					$jobcard='';

					if($restyui1->source==1)
					{
					$job=$this->db->select('job_card_no')->from('order_instruments_view')->where('id',$restyui1->jobcard)->get();
					if($job->num_rows()>0)
					{
					foreach($job->result() as $job1);
					$jobcard='Jobcard-'.$job1->job_card_no;
					}else{
					$jobcard='';
					}
					$polink="po";
					$mrnlink="mrn";
					}else if($restyui1->source==2)
					{
					/** Intend **/
					$jobcard='INDENT- IND'.$restyui1->sourceid;
					$polink="generalpo";
					$mrnlink="generalmrn";
					}else if($restyui1->source==3)
					{
					/** Intend **/
					$jobcard='AUTO PR';
					$polink="po";
					$mrnlink="mrn";
					}					


				
				if($restyui1->potype=='0')
				{
					$polink="po";
					$mrnlink="mrn";
				}else
				{
					$polink="generalpo";
					$mrnlink="generalmrn";
				}


if($restyui1->approved=='1')
{
    $app="Approved";
    $rmk='';
}else
{
    $app="Rejected";
    
    $resteyrttt=$this->db->select('remarks')->from('porejectremarks_view')->where('pono',$restyui1->pono)->get();
    if($resteyrttt->num_rows()>0)
    {
        foreach($resteyrttt->result() as $resteyrttt1);
    $rmk=$resteyrttt1->remarks;
    }else
    {
        $rmk='';
    }
}

$htm="";

if($restyui1->potype=='0'){
    
					$indenttype = "Machine Related Items";
				$rest123=$this->db->select('b.id,a.price,a.itemid, b.part as item_name,a.qty,a.unit,c.shortname')->from('purchase_order_view a')->join('machine_parts_with_picture_view b','a.itemid=b.id')->join('units_view c','a.unit=c.id','left')->where('a.pono',$restyui1->pono)->where('b.id',$restyui1->itemid)->get();
				}else{
					$indenttype = "General Items";
			$rest123=$this->db->select('b.id,a.price,a.itemid, b.item_name,a.qty,a.unit, c.shortname')->from('purchase_order a')->join('house_keeping_items b','a.itemid=b.id','left')->join('units c','a.unit=c.id','left')->where('a.pono',$restyui1->pono)->where('b.id',$restyui1->itemid)->get();		
				}
				
				
				if($rest123->num_rows()>0)
				{
					
					foreach($rest123->result() as $rest1231)
					$price = $rest1231->price;
					$oldprice="";
					$query = $this->db->select('price')->from('purchase_order_view')->where('itemid',$rest1231->itemid)->where('approved','1')->limit('1')->order_by('id','desc')->get();
					if($query->num_rows()>0){
						foreach($query->result() as $oldata);
						$oldprice = $oldata->price;
						$diff = abs($price-$oldprice);
					}else{
					    $oldprice="New Item";
					    $diff="0";
					}
					$itemname = strtoupper($rest1231->item_name);
					$qty = $rest1231->qty;
					$price = $rest1231->price;
					$itemid = $rest1231->id;
					$shortname= $rest1231->shortname;
					
					$amendment="";
					$q = $this->db->select('qty')->from('po_amendend_history')->where('pono',$restyui1->pono)->where('item_id',$itemid)->order_by('id','desc')->limit('1')->get();
										if($q->num_rows()>0){
										    foreach($q->result() as $rowss);
										    $amendment =  "(AMENDED ".floatval($rowss->qty)." ".$shortname." to ".floatval($qty)." ".$shortname.")";
										}
					
			}



			$scheduler_data[] = array('sr_no'=>$i,
			'prno'=>"<a href='".page_url."Store/".$polink."/".$restyui1->pono."' target='_blank' style='color:".$bc."'>".$restyui1->pono."</a> ".$amendment,
			'jobcardno'=>$jobcard,
			'createdby'=>ucfirst($restyui1->first_name." ".$restyui1->last_name),
			'itemname'=>$itemname,
			'qty'=>$qty,
			'price'=>$price,
			'oldprice'=>$oldprice,
			'vendor'=>$restyui1->name,
			'diff'=>$diff,
			'prnumber'=>$restyui1->prno,
			'createdon'=>date('d-M-Y g:i A',strtotime($restyui1->addedOn)),
			'approvredon'=>date('d-M-Y g:i:A',strtotime($restyui1->approvedOn)),
			'status'=>$app,
			'remarks'=>$rmk);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
}

function polisthistoryoooo()
{

$scheduler_data = array();
    $genpo=base64_decode($this->uri->segment(3));
    $genp=explode(',',$genpo);
		$rest=$this->db->select('a.approvedOn,a.jobcard,a.prno,a.source,a.sourceid,a.potype,a.addedOn,a.pono,e.first_name,e.last_name,a.itemid,e.department_id, v.name, v.address,a.approved')->from('purchase_order a')->join('system_users e','e.user_id=a.addedBy','left')->join('vendors v','a.vendor=v.id','left')->where('a.approved !=','0')->order_by('a.approvedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				if(in_array($restyui1->pono,$genp))
				{
					$bc="red";
				}else{
					$bc='';
				}
				
					$jobcard='';

					if($restyui1->source==1)
					{
					$job=$this->db->select('job_card_no')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
					if($job->num_rows()>0)
					{
					foreach($job->result() as $job1);
					$jobcard='Jobcard-'.$job1->job_card_no;
					}else{
					$jobcard='';
					}
					$polink="po";
					$mrnlink="mrn";
					}else if($restyui1->source==2)
					{
					/** Intend **/
					$jobcard='INDENT- IND'.$restyui1->sourceid;
					$polink="generalpo";
					$mrnlink="generalmrn";
					}else if($restyui1->source==3)
					{
					/** Intend **/
					$jobcard='AUTO PR';
					$polink="po";
					$mrnlink="mrn";
					}					


				
				if($restyui1->potype=='0')
				{
					$polink="po";
					$mrnlink="mrn";
				}else
				{
					$polink="generalpo";
					$mrnlink="generalmrn";
				}


if($restyui1->approved=='1')
{
    $app="Approved";
    $rmk='';
}else
{
    $app="Rejected";
    
    $resteyrttt=$this->db->select('remarks')->from('porejectremarks')->where('pono',$restyui1->pono)->get();
    if($resteyrttt->num_rows()>0)
    {
        foreach($resteyrttt->result() as $resteyrttt1);
    $rmk=$resteyrttt1->remarks;
    }else
    {
        $rmk='';
    }
}

$htm="";

if($restyui1->potype=='0'){
    
					$indenttype = "Machine Related Items";
				$rest123=$this->db->select('b.id,a.price,a.itemid, b.part as item_name,a.qty,a.unit,c.shortname')->from('purchase_order a')->join('machine_parts_with_picture b','a.itemid=b.id')->join('units c','a.unit=c.id','left')->where('a.pono',$restyui1->pono)->where('b.id',$restyui1->itemid)->get();
				}else{
					$indenttype = "General Items";
			$rest123=$this->db->select('b.id,a.price,a.itemid, b.item_name,a.qty,a.unit, c.shortname')->from('purchase_order a')->join('house_keeping_items b','a.itemid=b.id','left')->join('units c','a.unit=c.id','left')->where('a.pono',$restyui1->pono)->where('b.id',$restyui1->itemid)->get();		
				}
				
				
				if($rest123->num_rows()>0)
				{
					
					foreach($rest123->result() as $rest1231)
					$price = $rest1231->price;
					$oldprice="";
					$query = $this->db->select('price')->from('purchase_order')->where('itemid',$rest1231->itemid)->where('approved','1')->limit('1')->order_by('id','desc')->get();
					if($query->num_rows()>0){
						foreach($query->result() as $oldata);
						$oldprice = $oldata->price;
						$diff = abs($price-$oldprice);
					}else{
					    $oldprice="New Item";
					    $diff="0";
					}
					$itemname = strtoupper($rest1231->item_name);
					$qty = $rest1231->qty;
					$price = $rest1231->price;
					$itemid = $rest1231->id;
					$shortname= $rest1231->shortname;
					
					$amendment="";
					$q = $this->db->select('qty')->from('po_amendend_history')->where('pono',$restyui1->pono)->where('item_id',$itemid)->order_by('id','desc')->limit('1')->get();
										if($q->num_rows()>0){
										    foreach($q->result() as $rowss);
										    $amendment =  "(AMENDED ".floatval($rowss->qty)." ".$shortname." to ".floatval($qty)." ".$shortname.")";
										}
					
			}



			$scheduler_data[] = array('sr_no'=>$i,
			'prno'=>"<a href='".page_url."Store/".$polink."/".$restyui1->pono."' target='_blank' style='color:".$bc."'>".$restyui1->pono."</a> ".$amendment,
			'jobcardno'=>$jobcard,
			'createdby'=>ucfirst($restyui1->first_name." ".$restyui1->last_name),
			'itemname'=>$itemname,
			'qty'=>$qty,
			'price'=>$price,
			'oldprice'=>$oldprice,
			'vendor'=>$restyui1->name,
			'diff'=>$diff,
			'prnumber'=>$restyui1->prno,
			'createdon'=>date('d-M-Y g:i A',strtotime($restyui1->addedOn)),
			'approvredon'=>date('d-M-Y g:i:A',strtotime($restyui1->approvedOn)),
			'status'=>$app,
			'remarks'=>$rmk);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
}


function addporemarks()
{
    $pono= $this->input->post('pono');
    
    $data1=array('approved'=>'2');
    $this->db->where('pono',$pono);
    $this->db->update('purchase_order',$data1);
    
    
    $data=array('pono'=>$pono,'remarks'=>$this->input->post('remarks'),'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
    $this->db->insert('porejectremarks',$data);

$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">PO Rejected.</span></div>');
redirect(page_url.'Reporting/pendingpoforapproval');

    
}
function getrejectedremarks($pono)
{
	$wjew=array();
	$restyeur=$this->db->select('remarks,addedOn')->from('porejectremarks')->where('pono',$pono)->get();
	if($restyeur->num_rows()>0)
	{
	foreach($restyeur->result() as $restyeur1);

	$wjew[]=$restyeur1->remarks;
	$wjew[]=date('d-m-Y H:i:s',strtotime($restyeur1->addedOn));
	return $wjew;

	}else{ return $wjew; }

}  

public function cancel_indent(){
    $indentcode = $this->uri->segment(3);
    $data = array('cancel_status'=>'1','cancelby'=>$_SESSION['logged_in']['user_id'],'cancelledOn'=>date('Y-m-d H:i:s'));
    $this->db->where('indendno',$indentcode);
    $this->db->update('intend_request',$data);
    $this->session->set_flashdata('message','<div class="alert alert-info"><span style="color:#000;">Thank You! Indent request has been cancelled.</span></div>');
redirect(page_url.'Reporting/pending_indent_dashboard');
}


function getvendorname($vendorname)
{
	$Restye=$this->db->select('name')->from('vendors')->where('id',$vendorname)->get();
	
	if($Restye->num_rows()>0)
	{
		foreach($Restye->result() as $Restye1);
		
		return $Restye1->name;
		
	}else{
		
		return "";
	}
	
}


function checkifpoisraised($itemid)
{
// 1 for no 0 for yes raise
    
    $Reteaa=$this->db->select('id,approvalstatus')->from('purchase_request')->where('itemid',$itemid)->where('source','3')->where('closed','0')->order_by('id','desc')->limit(1)->get();
    if($Reteaa->num_rows()>0)
    {
   	foreach($Reteaa->result() as $Reteaa1);
   	if($Reteaa1->approvalstatus==0)
   	{
   	
   	return 1;
   	}else
   	{
   	
   	 $Rete=$this->db->select('id,completed')->from('purchase_order')->where('itemid',$itemid)->where('source','3')->order_by('id','desc')->limit(1)->get();
   	 if($Rete->num_rows()>0)
   	 {
   	 foreach($Rete->result() as $Rete1);
   	 if($Rete1->completed==0)
   	 {
   	 return 1;
   	 }else
   	 {
   	 return 0;
   	 }
   	 }else
   	 {
   	 return 0;
   	 }
   	 
   	}
   	
    }else
    {
    return 0;
    }
    
    
   
    
    
}


function checkifpoisraisedOLdddd($itemid)
{
    
    $Rete=$this->db->select('id')->from('purchase_order')->where('itemid',$itemid)->where('source','3')->where('approved','1')->where('completed','0')->get();
    
    return $Rete->num_rows();
    
    
}



function storerecieptsss()
{
	$rack='';
	$scheduler_data = array();
			$rest=$this->db->select('a.*,a.record_id,c.mrndoneOn,c.unit')->from('mrn_history a')->join('mrn c','a.record_id=c.id')->where('a.storereciept','0')->group_by('a.id')->order_by('a.id','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
			
			
			$podetail=$this->getpodetails($restyui1->po_no);
			
			if(count($podetail)>0)
			{
			$potype=$podetail['potype'];
			$sources=$podetail['source'];
			$sourceid=$podetail['sourceid'];
			$unit=$podetail['unit'];
			$pr=$podetail['prno'];
			
			}else
			{
			
			$potype='';
			$sources='';
			$sourceid='';
			$unit='';
			$pr;
			
			}
				
				if($potype==0)
				{
				$itemname=$this->getmachineitemname($restyui1->itemid);
				$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
				if(count($otherdetails)>0)
				{
			
				$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
					$rack=$otherdetails['rack_location'];
					
				}else{
					$fincode='';
					$specification='';
				}
				}else{
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
				}
				
				$jobcards=$this->getjobcardid($restyui1->record_id);
				
				if($sources=='1')
				{
				    $jobcard=$this->getjobcardno($jobcards);
				    $source='Jobcard - '.$jobcard;
				    
				}else if($sources=='2')
				{
				    $sourceid=$this->getsourceid($pr);
				      $source='INDENT IND-'.$sourceid;
				      
				}else
				{
				    
				      $source='IMS AUTO PR';
				}
				
				
			
				$uname=$this->getunit($restyui1->unit);
				
				if(floatval($restyui1->accept_qty)>0)
				{
			$scheduler_data[] = array('check'=>'<span class="recv'.$i.'"><input type="checkbox" class="checkitems" name="mrnid[]" id="checkthis'.$restyui1->id.'" value="'.$restyui1->id.'">
			<input type="hidden" name="qty'.$restyui1->id.'" value="'.$restyui1->accept_qty.'"><input type="hidden" name="potype'.$restyui1->id.'" value="'.$potype.'">
			<input type="hidden" name="itemid'.$restyui1->id.'" value="'.$restyui1->itemid.'"></span>',
			'sr_no'=>$i,
			'mrnon'=>date('d-M-Y H:i:s',strtotime($restyui1->mrndoneOn)),
			'ponumber'=>$restyui1->po_no,
			'source'=>$source,
			'racklocation'=>$rack,
			'item'=>$itemname,
			'fincode'=>$fincode,
			'specialization'=>$specification,
			'qty'=>floatval($restyui1->accept_qty)." ".$uname);
				
			$i++;
			}
			}
			}
			
			
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
		
	
	
}
function storerecieptOLDDDDDDDDDDDDDDDDDDDDDD()
{
	$rack='';
	$scheduler_data = array();
			$rest=$this->db->select('a.*,a.record_id,c.mrndoneOn')->from('mrn_history a')->join('mrn c','a.record_id=c.id')->where('a.storereciept','0')->group_by('a.id')->order_by('a.id','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
			
			
			$podetail=$this->getpodetails($restyui1->po_no);
			
			if(count($podetail)>0)
			{
			$potype=$podetail['potype'];
			$sources=$podetail['source'];
			$sourceid=$podetail['sourceid'];
			$unit=$podetail['unit'];
			
			}else
			{
			
			$potype='';
			$sources='';
			$sourceid='';
			$unit='';
			
			}
				
				if($potype==0)
				{
				$itemname=$this->getmachineitemname($restyui1->itemid);
				$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
				if(count($otherdetails)>0)
				{
			
				$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
					$rack=$otherdetails['rack_location'];
					
				}else{
					$fincode='';
					$specification='';
				}
				}else{
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
				}
				
				$jobcards=$this->getjobcardid($restyui1->record_id);
				
				if($sources=='1')
				{
				    $jobcard=$this->getjobcardno($jobcards);
				    $source='Jobcard - '.$jobcard;
				    
				}else if($sources=='2')
				{
				    
				      $source='INDENT IND-'.$sourceid;
				      
				}else
				{
				    
				      $source='IMS AUTO PR';
				}
				
				
				$uname=$this->getunit($unit);
			$scheduler_data[] = array('check'=>'<span class="recv'.$i.'"><input type="checkbox" class="checkitems" name="mrnid[]" id="checkthis'.$restyui1->id.'" value="'.$restyui1->id.'">
			<input type="hidden" name="qty'.$restyui1->id.'" value="'.$restyui1->accept_qty.'"><input type="hidden" name="potype'.$restyui1->id.'" value="'.$potype.'">
			<input type="hidden" name="itemid'.$restyui1->id.'" value="'.$restyui1->itemid.'"></span>',
			'sr_no'=>$i,
			'mrnon'=>date('d-M-Y H:i:s',strtotime($restyui1->mrndoneOn)),
			'ponumber'=>$restyui1->po_no,
			'source'=>$source,
			'racklocation'=>$rack,
			'item'=>$itemname.' '.$restyui1->id,
			'fincode'=>$fincode,
			'specialization'=>$specification,
			'qty'=>floatval($restyui1->accept_qty)." ".$uname);
				
			$i++;
			}
			}
			
			
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
		
	
	
}

public function rejected_po_report(){
    
    $this->load->view('store/rejected_po_report');
}


function rejectedporeport()
{

$scheduler_data = array();
    $genpo=base64_decode($this->uri->segment(3));
    $genp=explode(',',$genpo);
		$rest=$this->db->select('a.jobcard,a.prno,a.source,a.sourceid,a.potype,a.addedOn,a.pono,e.first_name,e.last_name,a.itemid,e.department_id, v.name, v.address,a.approved')->from('purchase_order a')->join('system_users e','e.user_id=a.addedBy','left')->join('vendors v','a.vendor=v.id','left')->where('a.approved','2')->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				if(in_array($restyui1->pono,$genp))
				{
					$bc="red";
				}else{
					$bc='';
				}
				
					$jobcard='';

					if($restyui1->source==1)
					{
					$job=$this->db->select('job_card_no')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
					if($job->num_rows()>0)
					{
					foreach($job->result() as $job1);
					$jobcard='Jobcard-'.$job1->job_card_no;
					}else{
					$jobcard='';
					}
					$polink="po";
					$mrnlink="mrn";
					}else if($restyui1->source==2)
					{
					/** Intend **/
					$jobcard='INDENT- IND'.$restyui1->sourceid;
					$polink="generalpo";
					$mrnlink="generalmrn";
					}else if($restyui1->source==3)
					{
					/** Intend **/
					$jobcard='AUTO PR';
					$polink="po";
					$mrnlink="mrn";
					}					


				
				if($restyui1->potype=='0')
				{
					$polink="po";
					$mrnlink="mrn";
				}else
				{
					$polink="generalpo";
					$mrnlink="generalmrn";
				}


if($restyui1->approved=='1')
{
    $app="Approved";
    $rmk='';
}else
{
    $app="Rejected";
    
    $resteyrttt=$this->db->select('remarks')->from('porejectremarks')->where('pono',$restyui1->pono)->get();
    if($resteyrttt->num_rows()>0)
    {
        foreach($resteyrttt->result() as $resteyrttt1);
    $rmk=$resteyrttt1->remarks;
    }else
    {
        $rmk='';
    }
}

$htm="";

if($restyui1->potype=='0'){
    
					$indenttype = "Machine Related Items";
				$rest123=$this->db->select('b.id,a.price,a.itemid, b.part as item_name,a.qty,a.unit,c.shortname')->from('purchase_order a')->join('machine_parts_with_picture b','a.itemid=b.id')->join('units c','a.unit=c.id','left')->where('a.pono',$restyui1->pono)->where('b.id',$restyui1->itemid)->get();
				}else{
					$indenttype = "General Items";
			$rest123=$this->db->select('b.id,a.price,a.itemid, b.item_name,a.qty,a.unit, c.shortname')->from('purchase_order a')->join('house_keeping_items b','a.itemid=b.id','left')->join('units c','a.unit=c.id','left')->where('a.pono',$restyui1->pono)->where('b.id',$restyui1->itemid)->get();		
				}
				
				
				if($rest123->num_rows()>0)
				{
					
					foreach($rest123->result() as $rest1231)
					$price = $rest1231->price;
					$oldprice="";
					$query = $this->db->select('price')->from('purchase_order')->where('itemid',$rest1231->itemid)->where('approved','1')->limit('1')->order_by('id','desc')->get();
					if($query->num_rows()>0){
						foreach($query->result() as $oldata);
						$oldprice = $oldata->price;
						$diff = abs($price-$oldprice);
					}else{
					    $oldprice="New Item";
					    $diff="0";
					}
					$itemname = strtoupper($rest1231->item_name);
					$qty = $rest1231->qty;
					$price = $rest1231->price;
					$itemid = $rest1231->id;
					$shortname= $rest1231->shortname;
					
					$amendment="";
					$q = $this->db->select('qty')->from('po_amendend_history')->where('pono',$restyui1->pono)->where('item_id',$itemid)->order_by('id','desc')->limit('1')->get();
										if($q->num_rows()>0){
										    foreach($q->result() as $rowss);
										    $amendment =  "(AMENDED ".floatval($rowss->qty)." ".$shortname." to ".floatval($qty)." ".$shortname.")";
										}
					
			}



			$scheduler_data[] = array('sr_no'=>$i,
			'prno'=>"<a href='".page_url."Store/".$polink."/".$restyui1->pono."' target='_blank' style='color:".$bc."'>".$restyui1->pono."</a> ".$amendment,
			'jobcardno'=>$jobcard,
			'createdby'=>ucfirst($restyui1->first_name." ".$restyui1->last_name),
			'itemname'=>$itemname,
			'qty'=>$qty,
			'price'=>$price,
			'oldprice'=>$oldprice,
			'vendor'=>$restyui1->name,
			'diff'=>$diff,
			'prnumber'=>$restyui1->prno,
			'createdon'=>date('d-M-Y g:i A',strtotime($restyui1->addedOn)),
			'status'=>$app,
			'remarks'=>$rmk);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
}

function pendingordersreport(){
    $this->load->view('store/pendingorders');
}

function pending_orderlist()
{

$scheduler_data = array();
    $genpo=base64_decode($this->uri->segment(3));
    $genp=explode(',',$genpo);
		$rest=$this->db->select('a.jobcard,a.prno,a.source,a.sourceid,a.potype,a.addedOn,a.pono,e.first_name,e.last_name,a.itemid,e.department_id, v.name, v.address,a.approved, v.phone, v.contactperson')->from('purchase_order a')->join('system_users e','e.user_id=a.addedBy','left')->join('vendors v','a.vendor=v.id','left')->where('a.approved','1')->where('a.completed','0')->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				if(in_array($restyui1->pono,$genp))
				{
					$bc="red";
				}else{
					$bc='';
				}
				
					$jobcard='';

					if($restyui1->source==1)
					{
					$job=$this->db->select('job_card_no')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
					if($job->num_rows()>0)
					{
					foreach($job->result() as $job1);
					$jobcard='Jobcard-'.$job1->job_card_no;
					}else{
					$jobcard='';
					}
					$polink="po";
					$mrnlink="mrn";
					}else if($restyui1->source==2)
					{
					/** Intend **/
					$jobcard='INDENT- IND'.$restyui1->sourceid;
					$polink="generalpo";
					$mrnlink="generalmrn";
					}else if($restyui1->source==3)
					{
					/** Intend **/
					$jobcard='AUTO PR';
					$polink="po";
					$mrnlink="mrn";
					}					


				
				if($restyui1->potype=='0')
				{
					$polink="po";
					$mrnlink="mrn";
				}else
				{
					$polink="generalpo";
					$mrnlink="generalmrn";
				}


if($restyui1->approved=='1')
{
    $app="Approved";
    $rmk='';
}else
{
    $app="Rejected";
    
    $resteyrttt=$this->db->select('remarks')->from('porejectremarks')->where('pono',$restyui1->pono)->get();
    if($resteyrttt->num_rows()>0)
    {
        foreach($resteyrttt->result() as $resteyrttt1);
    $rmk=$resteyrttt1->remarks;
    }else
    {
        $rmk='';
    }
}

$htm="";

if($restyui1->potype=='0'){
    
					$indenttype = "Machine Related Items";
				$rest123=$this->db->select('b.id,a.price,a.itemid, b.part as item_name,a.qty,a.unit,c.shortname')->from('purchase_order a')->join('machine_parts_with_picture b','a.itemid=b.id')->join('units c','a.unit=c.id','left')->where('a.pono',$restyui1->pono)->where('b.id',$restyui1->itemid)->get();
				}else{
					$indenttype = "General Items";
			$rest123=$this->db->select('b.id,a.price,a.itemid, b.item_name,a.qty,a.unit, c.shortname')->from('purchase_order a')->join('house_keeping_items b','a.itemid=b.id','left')->join('units c','a.unit=c.id','left')->where('a.pono',$restyui1->pono)->where('b.id',$restyui1->itemid)->get();		
				}
				
				
				if($rest123->num_rows()>0)
				{
					
					foreach($rest123->result() as $rest1231)
					$price = $rest1231->price;
					$oldprice="";
					$query = $this->db->select('price')->from('purchase_order')->where('itemid',$rest1231->itemid)->where('approved','1')->limit('1')->order_by('id','desc')->get();
					if($query->num_rows()>0){
						foreach($query->result() as $oldata);
						$oldprice = $oldata->price;
						$diff = abs($price-$oldprice);
					}else{
					    $oldprice="New Item";
					    $diff="0";
					}
					$itemname = strtoupper($rest1231->item_name);
					$qty = $rest1231->qty;
					$price = $rest1231->price;
					$itemid = $rest1231->id;
					$shortname= $rest1231->shortname;
					
					$amendment="";
					$q = $this->db->select('qty')->from('po_amendend_history')->where('pono',$restyui1->pono)->where('item_id',$itemid)->order_by('id','desc')->limit('1')->get();
										if($q->num_rows()>0){
										    foreach($q->result() as $rowss);
										    $amendment =  "(AMENDED ".floatval($rowss->qty)." ".$shortname." to ".floatval($qty)." ".$shortname.")";
										}
					
			}

/** PENDING SINCE **/

$cdate=new DateTime($restyui1->addedOn);
$tday=new DateTime(date('Y-m-d'));
$difference = $cdate->diff($tday);
/** END **/

			$scheduler_data[] = array('sr_no'=>$i,
			'prno'=>"<a href='".page_url."Store/".$polink."/".$restyui1->pono."' target='_blank' style='color:".$bc."'>".$restyui1->pono."</a> ".$amendment,
			'jobcardno'=>$jobcard,
			'createdby'=>ucfirst($restyui1->first_name." ".$restyui1->last_name),
			'itemname'=>$itemname,
			'qty'=>$qty,
			'price'=>$price,
			'oldprice'=>$oldprice,
			'vendor'=>$restyui1->name,
			'address'=>$restyui1->address,
			'phone'=>$restyui1->phone,
			'contactperson'=>$restyui1->contactperson,
			'diff'=>$diff,
			'prnumber'=>$restyui1->prno,
			'createdon'=>date('d-M-Y g:i A',strtotime($restyui1->addedOn)),
			'status'=>$app,
			'remarks'=>$rmk,
			'pendingsince'=>$difference->d.' Days');
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
}

function filterbyvendor(){
    $this->load->view('store/filter_by_vendor');
}

function vendorwise_purchase_report(){
    $data = array('vendor'=>$this->input->post('vendor'),
    'startdate'=>date('Y-m-d',strtotime($this->input->post('startdate'))),
    'enddate'=>date('Y-m-d',strtotime($this->input->post('enddate'))),);
    $this->load->view('store/vendorwise_purchase_report',$data);
}

function vendorwise_purchase_report_list()
{

$scheduler_data = array();
    
    $vendor = $this->uri->segment(3);
     $startdate = $this->uri->segment(4);
     $firstdate = $startdate." 00:00:00";
      $enddate = $this->uri->segment(5);
      $lastdate = $enddate." 23:59:59";
		$this->db->select('a.jobcard,a.prno,a.source,a.sourceid,a.potype,a.addedOn,a.pono,e.first_name,e.last_name,a.itemid,e.department_id, v.name, v.address,a.approved')->from('purchase_order a')->join('system_users e','e.user_id=a.addedBy','left')->join('vendors v','a.vendor=v.id','left')->where('a.approved','1');
		if($this->uri->segment(3)){
		    $this->db->where('a.vendor',$vendor);
		}
		if($this->uri->segment(4)!=='' && $this->uri->segment(5)!==''){
		    $this->db->where('a.addedOn BETWEEN "'.$firstdate. '" and "'.$lastdate.'"');
		}
		$rest=$this->db->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
					$jobcard='';

					if($restyui1->source==1)
					{
					$job=$this->db->select('job_card_no')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
					if($job->num_rows()>0)
					{
					foreach($job->result() as $job1);
					$jobcard='Jobcard-'.$job1->job_card_no;
					}else{
					$jobcard='';
					}
					$polink="po";
					$mrnlink="mrn";
					}else if($restyui1->source==2)
					{
					/** Intend **/
					$jobcard='INDENT- IND'.$restyui1->sourceid;
					$polink="generalpo";
					$mrnlink="generalmrn";
					}else if($restyui1->source==3)
					{
					/** Intend **/
					$jobcard='AUTO PR';
					$polink="po";
					$mrnlink="mrn";
					}					


				
				if($restyui1->potype=='0')
				{
					$polink="po";
					$mrnlink="mrn";
				}else
				{
					$polink="generalpo";
					$mrnlink="generalmrn";
				}


if($restyui1->approved=='1')
{
    $app="Approved";
    $rmk='';
}else
{
    $app="Rejected";
    
    $resteyrttt=$this->db->select('remarks')->from('porejectremarks')->where('pono',$restyui1->pono)->get();
    if($resteyrttt->num_rows()>0)
    {
        foreach($resteyrttt->result() as $resteyrttt1);
    $rmk=$resteyrttt1->remarks;
    }else
    {
        $rmk='';
    }
}

$htm="";

if($restyui1->potype=='0'){
    
					$indenttype = "Machine Related Items";
				$rest123=$this->db->select('b.id,a.price,a.itemid, b.part as item_name,a.qty,a.unit,c.shortname')->from('purchase_order a')->join('machine_parts_with_picture b','a.itemid=b.id')->join('units c','a.unit=c.id','left')->where('a.pono',$restyui1->pono)->where('b.id',$restyui1->itemid)->get();
				}else{
					$indenttype = "General Items";
			$rest123=$this->db->select('b.id,a.price,a.itemid, b.item_name,a.qty,a.unit, c.shortname')->from('purchase_order a')->join('house_keeping_items b','a.itemid=b.id','left')->join('units c','a.unit=c.id','left')->where('a.pono',$restyui1->pono)->where('b.id',$restyui1->itemid)->get();		
				}
				
				
				if($rest123->num_rows()>0)
				{
					
					foreach($rest123->result() as $rest1231)
					$price = $rest1231->price;
					$oldprice="";
					$query = $this->db->select('price')->from('purchase_order')->where('itemid',$rest1231->itemid)->where('approved','1')->limit('1')->order_by('id','desc')->get();
					if($query->num_rows()>0){
						foreach($query->result() as $oldata);
						$oldprice = $oldata->price;
						$diff = abs($price-$oldprice);
					}else{
					    $oldprice="New Item";
					    $diff="0";
					}
					$itemname = strtoupper($rest1231->item_name);
					$qty = $rest1231->qty;
					$price = $rest1231->price;
					$itemid = $rest1231->id;
					$shortname= $rest1231->shortname;
					
					$amendment="";
					$q = $this->db->select('qty')->from('po_amendend_history')->where('pono',$restyui1->pono)->where('item_id',$itemid)->order_by('id','desc')->limit('1')->get();
										if($q->num_rows()>0){
										    foreach($q->result() as $rowss);
										    $amendment =  "(AMENDED ".floatval($rowss->qty)." ".$shortname." to ".floatval($qty)." ".$shortname.")";
										}
					
			}
            $totalvalue = $this->storemodel->getpototal($restyui1->pono);
			$scheduler_data[] = array('sr_no'=>$i,
			'prno'=>"<a href='".page_url."Store/".$polink."/".$restyui1->pono."' target='_blank' style='color:red'>".$restyui1->pono."</a> ".$amendment,
			'jobcardno'=>$jobcard,
			'createdby'=>ucfirst($restyui1->first_name." ".$restyui1->last_name),
			'itemname'=>$itemname,
			'totalvalue'=>$totalvalue,
			'qty'=>$qty,
			'price'=>$price,
			'oldprice'=>$oldprice,
			'vendor'=>$restyui1->name,
			'diff'=>$diff,
			'prnumber'=>$restyui1->prno,
			'createdon'=>date('d-M-Y g:i A',strtotime($restyui1->addedOn)),
			'status'=>$app,
			'remarks'=>$rmk);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
}

public function repeated_item_report(){
    $this->load->view('store/repeated_item_report');
}


function checkifanyjobcardidimported($orderid)
{
	$imp=array();
	$Resteyru=$this->db->select('a.item_id,a.id,b.stock')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->where('a.order_id',$orderid)->where('b.type','1')->get();
	if($Resteyru->num_rows()>0)
	{
	   
		foreach($Resteyru->result() as $Resteyru1)
		{
		$imp[]=array('itemid'=>$Resteyru1->item_id,'jobcardid'=>$Resteyru1->id,'stock'=>$Resteyru1->stock);
		}
	}
	
	
	return $imp;
	
}

function pendingpoforpayment()
{
    $this->load->view('store/accountspayment');
    
}


function pendingpaymentreport()
{
    $scheduler_data=array();
	$debitnotetotal=0;
	$challanddata='';
	$a=0;
	$Restey=$this->db->select('a.pono,a.itemid,a.vendor,b.name,b.phone,b.payment_terms,b.payment_mode')->from('purchase_order a')->join('vendors b','a.vendor=b.id')->where('a.completed','1')->where('a.payment','0')->group_by('a.pono')->get();

	if($Restey->num_rows()>0)
	{
	        $ta=1;
    	    foreach($Restey->result() as $Restey1)
	    {
	        $allmrn=$this->checkifallmrnaredone($Restey1->pono);
	        if(count($allmrn)>0)
	        {
			
	        $restur=$this->checkmrndata($Restey1->pono,$allmrn);
	        if($restur==true)
	        {
	
			$pototal=$this->storemodel->getpototal($Restey1->pono);
            $medata=$this->getmrnhistorydataforpayment($Restey1->pono);
            
            if(count($medata)>0)
            {
            $app=$medata['app'];
            $replace=$medata['replace'];
			if($replace>0)
			{
				
			 $Restyr=$this->checkforrejectionaction($Restey1->pono);
			 if($Restyr>0)
			 {
				 $a=1;
			 }else{
				 
				$challanddata=$this->checkfordbandrgp($Restey1->pono);
					
	           $debitnotetotal=$this->checkfordbtotal($Restey1->pono);
				 
				$openrgp=$this->checkifanyrgpisopen($Restey1->pono);
				if($openrgp>0)
				{
					$a=2;
				}else{
					
					$a=0;
				}
			 
			 }
				
			}
            
            
            }else{
            
            $app='';
            $replace='';
           
            }
				
				if($debitnotetotal>0)
				{
					$dbtot=$debitnotetotal;
					$balancepayment=$pototal-$dbtot;
					
				}else{
					
					$balancepayment=$pototal;
					$dbtot=0;
					
				}

					/** Payment  Button **/

					if($a==0)
					{
						$payment='<span class="btn btn-warning" onclick="paymentmodal('."'".$Restey1->pono."'".','."'".$Restey1->payment_mode."'".','."'".$Restey1->name."'".')">Enter Payment Details</span>';
					}else
					{
						
						if($a=='1')
						{
							$payment="REJECTION ACTION PENDING";
						}else{
							
							$payment="PAYMENT ON HOLD";
						}
						
					}
					/** end **/
	   $scheduler_data[] = array(
				'sr_no'=>$ta,
				'po'=>$Restey1->pono,
				'vendor'=>$Restey1->name,
				'contact'=>$Restey1->phone,
				'paymentterm'=>$Restey1->payment_terms,
				'paymentmode'=>$Restey1->payment_mode,
				'approvedqty'=>$app,
				'rejectedqty'=>$replace,
				'rejectedaction'=>$challanddata,
				'totalpayment'=>$pototal,
				'dbtotal'=>$dbtot,
				'balancetotal'=>$balancepayment,
				'paymentstatus'=>$payment
				);
	        $ta++;   }
	        }
	    

	    }
	}
    
	
    $results = array(
    "sEcho" => 1,
    "iTotalRecords" => count($scheduler_data),
    "iTotalDisplayRecords" => count($scheduler_data),
    "aaData"=>$scheduler_data);
    echo json_encode($results);	
    
   
    
}



function checkifallmrnaredone($pono)
{
	
	
    $items=array();
    $Restyr=$this->db->select('itemid')->from('purchase_order')->where('pono',$pono)->where('approved','1')->where('gateentrycomplete','1')->get();
    if($Restyr->num_rows()>0)
    {
        foreach($Restyr->result() as $restyrur)
        {
            $items[]=$restyrur->itemid;
        }
         
    }
    
    return $items;
}


function checkmrndata($pono,$allmrn)
{
    $mdata=array();
    $itemss = "'" . implode ( "', '", $allmrn ) . "'";
   
    $Resteyu=$this->db->select('id')->from('mrn')->where('pono',$pono)->where_in('itemid',$itemss,false)->group_by('itemid',$itemss)->get();
    if(count($allmrn)==$Resteyu->num_rows())
    {
    return true;
    
    }else
    {
    return false;
    }
     
 }



function getmrnhistorydataforpayment($pono)
	{
		$menhist=array();
		
		$restyu34=$this->db->select('a.id as mrnhistoryid ,sum(a.accept_qty) as acceptqty,sum(a.debit_qty) as debitqty,sum(a.reject_qty) as rejectqty')->from('mrn_history a')->join('item_rejection_request
 b','b.mrnhistoryid=a.id','left')->where('a.po_no',$pono)->get();
		if($restyu34->num_rows()>0)
		{
			foreach($restyu34->result() as $restyu341);
	
			$menhist['app']=$restyu341->acceptqty;
			$menhist['replace']=$restyu341->rejectqty;
				
		}
		
		return $menhist;
		
	}




function hsntogst()
{
$resteyur=$this->db->select('*')->from('hsn')->group_by('hsno')->get();
if($resteyur->num_rows()>0)
{
foreach($resteyur->result() as $resteyur1)
{

/** Machinre Items **/
$data=array('gst'=>$resteyur1->gst); 
$this->db->where('hsn',trim($resteyur1->hsno));
$this->db->update('house_keeping_items',$data);
/** end **/

}

}

}



function checkforrejectionaction($pono)
{
	
	$Restyuriut=$this->db->select('chtype')->from('item_rejection_request')->where('pono',$pono)->where('chtype','0')->get();
	return $Restyuriut->num_rows();
	
	
}


function checkfordbandrgp($pono)
{
	$challandata='';
	$Restyuriut=$this->db->select('chtype')->from('item_rejection_request')->where('pono',$pono)->get();
	if($Restyuriut->num_rows()>0)
	{
		
		foreach($Restyuriut->result() as $Restyuriut1)
		{
			if($Restyuriut1->chtype=='1')
			{
				/** REJECTION CHALLAN **/
				$Restyrut=$this->db->select('id,challanno')->from('outwardchallan')->where('pono',$pono)->get();
				if($Restyrut->num_rows()>0)
				{
					foreach($Restyrut->result() as $Restyrut)
					{
						
						$challandata.="<a href='".page_url."/Store/rejectionchallan/".$Restyrut->id."' target='_blank'>Rejection Challan- ".$Restyrut->challanno.'</a><br/>';
						
					}
					
					
				}
				/** END **/
				
			}
			
			
			if($Restyuriut1->chtype=='2')
			{
				/** DEBIT CHALLAN **/
				$Restyrut=$this->db->select('id,dbno')->from('debitnote')->where('pono',$pono)->get();
				if($Restyrut->num_rows()>0)
				{
					foreach($Restyrut->result() as $Restyrut)
					{
						
						$challandata.="<a href='".page_url."Store/debit_note/".$Restyrut->dbno."'>Debit Note- ".$Restyrut->dbno.'</a><br/>';
						
					}
					
					
				}
				/** END **/
				
			}
			
			
		}
		
		
	}
	
	return $challandata;
	
	
}


function checkfordbtotal($pono)
{
	
	$dbtotal[]=0;
	$Restyuriut=$this->db->select('chtype')->from('item_rejection_request')->where('pono',$pono)->where('chtype','2')->get();
	if($Restyuriut->num_rows()>0)
	{
		
		foreach($Restyuriut->result() as $Restyuriut1)
		{
			
			$Restyrut=$this->db->select('id,dbno,qty,itemid')->from('debitnote')->where('pono',$pono)->get();
				if($Restyrut->num_rows()>0)
				{
					foreach($Restyrut->result() as $Restyrut)
					{
						$itemprice=$this->getpopriceforitem($Restyrut->itemid,$pono);
						$dbtotal[]=$Restyrut->qty*$itemprice;
						
					}
					
					
				}
			
			
		}
		
		
	}
	
	return array_sum($dbtotal);
	

}


function getpopriceforitem($itemid,$pono)
{
	
	$Resyturir=$this->db->select('price')->from('purchase_order')->where('itemid',$itemid)->where('pono',$pono)->get();
	
	if($Resyturir->num_rows()>0)
	{
		foreach($Resyturir->result() as $Resyturir1);
		
		return $Resyturir1->price;
		
	}else{
		
		return 0;
	}
	
	
}

function checkifanyrgpisopen($pono)
{
	$Reyeur=$this->db->select('id')->from('outwardchallan')->where('open','0')->where('qc','0')->where('pono',$pono)->get();
	
	return $Reyeur->num_rows();
	
	
	
}


function donepaymentreport()
{
    $scheduler_data=array();
	$debitnotetotal=0;
	$challanddata='';
	$a=0;
	$Restey=$this->db->select('a.pono,a.itemid,a.vendor,b.name,b.phone,b.payment_terms,b.payment_mode,c.utrno')->from('purchase_order a')->join('vendors b','a.vendor=b.id')->join('paymentdetails c','a.pono=c.pono','left')->where('a.gateentrycomplete','1')->where('a.payment','1')->get();

	if($Restey->num_rows()>0)
	{
	        $ta=1;
    	    foreach($Restey->result() as $Restey1)
	    {
	        $allmrn=$this->checkifallmrnaredone($Restey1->pono);
	        if(count($allmrn)>0)
	        {
			
	        $restur=$this->checkmrndata($Restey1->pono,$allmrn);
	        if($restur==true)
	        {
	
			$pototal=$this->storemodel->getpototal($Restey1->pono);
            $medata=$this->getmrnhistorydataforpayment($Restey1->pono);
            
            if(count($medata)>0)
            {
            $app=$medata['app'];
            $replace=$medata['replace'];
			if($replace>0)
			{
				
			 $Restyr=$this->checkforrejectionaction($Restey1->pono);
			 if($Restyr>0)
			 {
				 $a=1;
			 }else{
				 
				$challanddata=$this->checkfordbandrgp($Restey1->pono);
					
	           $debitnotetotal=$this->checkfordbtotal($Restey1->pono);
				 
				$openrgp=$this->checkifanyrgpisopen($Restey1->pono);
				if($openrgp>0)
				{
					$a=2;
				}else{
					
					$a=0;
				}
			 
			 }
				
			}
            
            
            }else{
            
            $app='';
            $replace='';
           
            }
				
				if($debitnotetotal>0)
				{
					$dbtot=$debitnotetotal;
					$balancepayment=$pototal-$dbtot;
					
				}else{
					
					$balancepayment=$pototal;
					$dbtot=0;
					
				}

					/** Payment  Button **/

					if($a==0)
					{
						$payment='<span class="btn btn-warning" onclick="paymentmodal('."'".$Restey1->pono."'".','."'".$Restey1->payment_mode."'".','."'".$Restey1->name."'".')">Enter Payment Details</span>';
					}else
					{
						
						if($a=='1')
						{
							$payment="REJECTION ACTION PENDING";
						}else{
							
							$payment="PAYMENT ON HOLD";
						}
						
					}
					/** end **/
	   $scheduler_data[] = array(
				'sr_no'=>$ta,
				'po'=>$Restey1->pono,
				'vendor'=>$Restey1->name,
				'contact'=>$Restey1->phone,
				'paymentterm'=>$Restey1->payment_terms,
				'paymentmode'=>$Restey1->payment_mode,
				
				'totalpayment'=>$pototal,
				'dbtotal'=>$dbtot,
				'balancetotal'=>$balancepayment,
				'paymentstatus'=>$Restey1->utrno
				);
	        $ta++;   }
	        }
	    

	    }
	}
    
	
    $results = array(
    "sEcho" => 1,
    "iTotalRecords" => count($scheduler_data),
    "iTotalDisplayRecords" => count($scheduler_data),
    "aaData"=>$scheduler_data);
    echo json_encode($results);	
    
   
    
}


function getpoapprovaldate($pono,$itemid)
{
    $appdate='';
    $posos=$this->db->select('approvedOn')->from('purchase_order')->where('pono',$pono)->where('itemid',$itemid)->get();
        if($posos->num_rows()>0)
        {
            foreach($posos->result() as $posos1);
            
            $appdate=date('Y-m-d',strtotime($posos1->approvedOn));
        }
    
    
    return $appdate;
}





function pendingpoconsolidated()
{
    $this->load->view('store/pendingpoconsolidated');
}

function vendorwiseconsolidatedpending_orderlist()
{

$scheduler_data = array();
    $genpo=base64_decode($this->uri->segment(3));
    $genp=explode(',',$genpo);
		$rest=$this->db->select('a.vendor,a.jobcard,a.prno,a.source,a.sourceid,a.potype,a.addedOn,a.pono,e.first_name,e.last_name,a.itemid,e.department_id, v.name, v.address,a.approved, v.phone, v.contactperson')->from('purchase_order a')->join('system_users e','e.user_id=a.addedBy','left')->join('vendors v','a.vendor=v.id','left')->where('a.approved','1')->where('a.completed','0')->group_by('a.vendor')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{



			$scheduler_data[] = array('sr_no'=>$i,
	
			'createdby'=>ucfirst($restyui1->first_name." ".$restyui1->last_name),
	
			'vendor'=>"<a href='".page_url."Store/consolidatedpoinvoice/".$restyui1->vendor."' target='_blank'>".$restyui1->name."</a>",
			'address'=>$restyui1->address,
			'phone'=>$restyui1->phone,
			'contactperson'=>$restyui1->contactperson);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
}



function storeunaccpteditems()
{
	
	$scheduler_data=array();	
	$uri=$this->uri->segment(3);
	$Restey=$this->db->select('a.*')->from('issuestocktousers_view a')->where('a.storeaccept','0');

	if($uri<>'')
	{
		if($uri==1)
		{
			$startdate=date('Y-m-d 09:30:00');
			$enddate=date('Y-m-d 13:00:00');
			$this->db->where('a.issuedOn BETWEEN "'.$startdate. '" and "'.$enddate.'"');
		}else
		{
			$startdate=date('Y-m-d 13:30:00');
			$enddate=date('Y-m-d 18:00:00');
			$this->db->where('a.issuedOn BETWEEN "'.$startdate. '" and "'.$enddate.'"');

		}

	
	}

	$Restey=$this->db->ORDER_BY('a.issuedOn','DESC')->get();
	if($Restey->num_rows()>0)
	{	
		$a=1;
		foreach($Restey->result() as $Restey1)
		{
			
				$jobcard=$this->getjobcardno($Restey1->jobcardid);
				
				$iss='<a href="'.page_url.'Store/issueslip/'.$Restey1->usertype.'/'.$Restey1->issuedto.'/'.$Restey1->jobcardid.'/'.$Restey1->issuesession.'" target="_blank">Issue Slip</a>';
			
				$machinedetails=$this->getmachineotherdetails($Restey1->itemid);
				if(count($machinedetails)>0)
				{
				$fincode=$machinedetails['fincode'];
				$specification=$machinedetails['specialization'];
				$part=$machinedetails['name'];
				$rack_location=$machinedetails['rack_location'];
				$unit=$machinedetails['unit'];
				}else{
				$fincode='';
				$specification='';
				$part='';
				$rack_location='';
				$unit='';
				}
				
				if($unit<>'')
				{
				$unitname=$this->getunit($unit);
				}else
				{
				$unitname='';
				}
				
				if($Restey1->usertype==1)
				{
					$issueduser=$this->storemodel->getudata($Restey1->issuedto);
					
				}else if($Restey1->usertype==2)
				{
					$issueduser=$this->storemodel->getnoncrmusername($Restey1->issuedto);
					
				}else
				{
					$issueduser='';
				}

if($uri == '') {
	if($jobcard<>'')
	{
	   
	   $instruments_name=$this->getjobcardinstrument($Restey1->jobcardid);
	   // $sshs='<span id="c'.$Restey1->id.'"><input type="button" name="checkthis" value="Accept" id="accept'.$Restey1->id.'" onclick="acceptitems('.$Restey1->id.')"></span><span id="ce'.$Restey1->id.'" style="color:red; display:none">Accepted</span>';
	   $check = '<span id="c'.$Restey1->id.'"><input type="checkbox" name="issueid[]" class="checkitems1" value="'.$Restey1->id.'" id="accept'.$Restey1->id.'"></span><span id="ce'.$Restey1->id.'" style="color:red; display:none">Accepted</span>';
	}else
	{
		$instruments_name='';
	     // $sshs='<span id="c'.$Restey1->id.'"><input type="button" name="checkthis" value="Accept" id="accept'.$Restey1->id.'" onclick="acceptnonjobcarditems('.$Restey1->id.')"></span><span id="ce'.$Restey1->id.'" style="color:red; display:none">Accepted</span>';
		$check = '<span id="c'.$Restey1->id.'"><input type="checkbox" name="issueid[]" value="'.$Restey1->id.'" id="accept'.$Restey1->id.'" class="checkitems1"></span><span id="ce'.$Restey1->id.'" style="color:red; display:none">Accepted</span>'; 
	    
	}
}

if($uri == 1) {
	if($jobcard<>'')
	{
	   
	   $instruments_name=$this->getjobcardinstrument($Restey1->jobcardid);
	   // $sshs='<span id="c'.$Restey1->id.'"><input type="button" name="checkthis" value="Accept" id="accept'.$Restey1->id.'" onclick="acceptitems('.$Restey1->id.')"></span><span id="ce'.$Restey1->id.'" style="color:red; display:none">Accepted</span>';
	   $check = '<span id="c'.$Restey1->id.'"><input type="checkbox" name="issueid[]" class="checkitems2" value="'.$Restey1->id.'" id="accept'.$Restey1->id.'"></span><span id="ce'.$Restey1->id.'" style="color:red; display:none">Accepted</span>';
	}else
	{
		$instruments_name='';
	     // $sshs='<span id="c'.$Restey1->id.'"><input type="button" name="checkthis" value="Accept" id="accept'.$Restey1->id.'" onclick="acceptnonjobcarditems('.$Restey1->id.')"></span><span id="ce'.$Restey1->id.'" style="color:red; display:none">Accepted</span>';
		$check = '<span id="c'.$Restey1->id.'"><input type="checkbox" name="issueid[]" value="'.$Restey1->id.'" id="accept'.$Restey1->id.'" class="checkitems2"></span><span id="ce'.$Restey1->id.'" style="color:red; display:none">Accepted</span>'; 
	    
	}
}

if($uri == 2) {
	if($jobcard<>'')
	{
	   
	   $instruments_name=$this->getjobcardinstrument($Restey1->jobcardid);
	   // $sshs='<span id="c'.$Restey1->id.'"><input type="button" name="checkthis" value="Accept" id="accept'.$Restey1->id.'" onclick="acceptitems('.$Restey1->id.')"></span><span id="ce'.$Restey1->id.'" style="color:red; display:none">Accepted</span>';
	   $check = '<span id="c'.$Restey1->id.'"><input type="checkbox" name="issueid[]" class="checkitems3" value="'.$Restey1->id.'" id="accept'.$Restey1->id.'"></span><span id="ce'.$Restey1->id.'" style="color:red; display:none">Accepted</span>';
	}else
	{
		$instruments_name='';
	     // $sshs='<span id="c'.$Restey1->id.'"><input type="button" name="checkthis" value="Accept" id="accept'.$Restey1->id.'" onclick="acceptnonjobcarditems('.$Restey1->id.')"></span><span id="ce'.$Restey1->id.'" style="color:red; display:none">Accepted</span>';
		$check = '<span id="c'.$Restey1->id.'"><input type="checkbox" name="issueid[]" value="'.$Restey1->id.'" id="accept'.$Restey1->id.'" class="checkitems3"></span><span id="ce'.$Restey1->id.'" style="color:red; display:none">Accepted</span>'; 
	    
	}
}
			

	$forchallan="<input type='checkbox' class='forchallan' name='fetchforchallan[]' value='".$Restey1->id."''>";


	/** PENDING SINCE **/

$cdate=new DateTime($Restey1->issuedOn);
$tday=new DateTime(date('Y-m-d'));
$difference = $cdate->diff($tday);
/** END **/

$check_new = '<input type="checkbox" name="issueid[]" value="'.$Restey1->id.'" id="accept_NEW'.$Restey1->id.'" class="checkitems3"></span><span id="ce'.$Restey1->id.'" style="color:red; display:none">Accepted</span>';


				$scheduler_data[] = array(
				'sr_no'=>$a,
				'pendingsince'=>"<strong style='color:red'>".$difference->d.' Days'."</strong>",
				'accept'=>$check,
				'issueslip'=>$iss,
				'pending_since'=>$difference,
				'jobcard'=>$jobcard,
				'instrument'=>$instruments_name,
				'itemname'=>$part,
				'fincode'=>$fincode,
				'specialization'=>$specification,
				'racklocation'=>$rack_location,
				'qty'=>$Restey1->stock." ".$unitname,
				'issuedto'=>$issueduser,
				'challanselect'=>$forchallan,
				'issuedOn'=>date('d-M-Y H:i:s',strtotime($Restey1->issuedOn)),
				'issuedreason'=>$Restey1->reason,
				'rollback'=>"<span id='removed".$Restey1->id."' style='color:red;'><input type='checkbox' name='rollback[]' id='rollback".$Restey1->id."' href='javascript:;' onchange='rollbackrecord(".$Restey1->id.",".$Restey1->blockedid.")'></span>"
				);
	     		
		$a++;
		}
	
	}
	$results = array(
    "sEcho" => 1,
    "iTotalRecords" => count($scheduler_data),
    "iTotalDisplayRecords" => count($scheduler_data),
    "aaData"=>$scheduler_data);
    echo json_encode($results);	
    
	
}

function storeunaccpteditemsOLDDDDDDDDDD()
{
	
	$scheduler_data=array();	
	
	$Restey=$this->db->select('a.*')->from('issuestocktousers a')->where('a.storeaccept','0')->ORDER_BY('issuedOn','DESC')->get();
	if($Restey->num_rows()>0)
	{	
		$a=1;
		foreach($Restey->result() as $Restey1)
		{
			
				$jobcard=$this->getjobcardno($Restey1->jobcardid);
				$iss='<a href="'.page_url.'Store/issueslip/'.$Restey1->usertype.'/'.$Restey1->issuedto.'/'.$Restey1->jobcardid.'/'.$Restey1->issuesession.'" target="_blank">Issue Slip</a>';
			
				$machinedetails=$this->getmachineotherdetails($Restey1->itemid);
				if(count($machinedetails)>0)
				{
				$fincode=$machinedetails['fincode'];
				$specification=$machinedetails['specialization'];
				$part=$machinedetails['name'];
				$rack_location=$machinedetails['rack_location'];
				$unit=$machinedetails['unit'];
				}else{
				$fincode='';
				$specification='';
				$part='';
				$rack_location='';
				$unit='';
				}
				
				if($unit<>'')
				{
				$unitname=$this->getunit($unit);
				}else
				{
				$unitname='';
				}
				
				if($Restey1->usertype==1)
				{
					$issueduser=$this->storemodel->getudata($Restey1->issuedto);
					
				}else if($Restey1->usertype==2)
				{
					$issueduser=$this->storemodel->getnoncrmusername($Restey1->issuedto);
					
				}else
				{
					$issueduser='';
				}


				
				$scheduler_data[] = array(
				'sr_no'=>$a,
				'accept'=>'<span id="c'.$Restey1->id.'"><input type="checkbox" name="checkthis" value="'.$Restey1->id.'" id="accept'.$Restey1->id.'" onchange="acceptitems('.$Restey1->id.')"></span><span id="ce'.$Restey1->id.'" style="color:red; display:none">Accepted</span>',
				'issueslip'=>$iss,
				'jobcard'=>$jobcard,
				'itemname'=>$part,
				'fincode'=>$fincode,
				'specialization'=>$specification,
				'racklocation'=>$rack_location,
				'qty'=>$Restey1->stock." ".$unitname,
				'issuedto'=>$issueduser,
				'issuedOn'=>date('d-M-Y H:i:s',strtotime($Restey1->issuedOn))
				);
	     		
		$a++;
		}
	
	}
	$results = array(
    "sEcho" => 1,
    "iTotalRecords" => count($scheduler_data),
    "iTotalDisplayRecords" => count($scheduler_data),
    "aaData"=>$scheduler_data);
    echo json_encode($results);	
    
	
}


function getjobcardno($jobcard)
{
    $restey=$this->db->select('job_card_no')->from('order_instruments')->where('id',$jobcard)->get();
    if($restey->num_rows()>0)
    {
        foreach($restey->result() as $restey12);
        
        return $restey12->job_card_no;
        
    }else
    {
        return '';
    }
    

}
	
	
	
function storeunaccpteditemsforhousekeeping()
{
	
	 $scheduler_data=array();	
	
	$Restey=$this->db->select('a.*')->from('issuegeneralstock a')->where('a.storeaccept','0')->ORDER_BY('issuedOn','DESC')->get();
	if($Restey->num_rows()>0)
	{	
		$a=1;
		foreach($Restey->result() as $Restey1)
		{
		$machinedetails=$this->storemodel->getgeneralitemname($Restey1->itemid);
		$unitname=$this->storemodel->getgeneralitemunit($Restey1->itemid);

		if($Restey1->crmuser>0)
		{
		$issueduser=$this->storemodel->getudata($Restey1->crmuser);
		$a=1;

		}else if($Restey1->noncrmuser>0)
		{
		$issueduser=$this->storemodel->getnoncrmusername($Restey1->noncrmuser);
		$a=2;
		}else
		{
		$issueduser='';
		$a=0;
		}
        
        $iss='<a href="'.page_url.'Store/generalissueslip/'.$Restey1->issuesession.'/'.$a.'" target="_blank">Issue Slip</a>';

		$scheduler_data[] = array(
		'sr_no'=>$a,
		'accept'=>'<span id="c1'.$Restey1->id.'"><input type="checkbox" name="checkthis" value="'.$Restey1->id.'" id="accept1'.$Restey1->id.'" onchange="acceptitems1('.$Restey1->id.')"></span><span id="ce1'.$Restey1->id.'" style="color:red; display:none">Accepted</span>',
		'issueslip'=>$iss,
		'itemname'=>$machinedetails,
		'qty'=>floatval($Restey1->qty)." ".$unitname,
		'issuedto'=>$issueduser,
		'issuedOn'=>date('d-M-Y H:i:s',strtotime($Restey1->issuedOn))
		);

		$a++;
		}

	
	}
	$results = array(
    "sEcho" => 1,
    "iTotalRecords" => count($scheduler_data),
    "iTotalDisplayRecords" => count($scheduler_data),
    "aaData"=>$scheduler_data);
    echo json_encode($results);	
    
	
}




function todaysissueditems()
{
	$scheduler_data=array();	
	$firstdate=date('Y-m-d')." 00:00:00";
	$lastdate=date('Y-m-d')." 23:59:59";
	$Restey=$this->db->select('a.*')->from('issuestocktousers a')->where('a.issuedOn BETWEEN "'.$firstdate. '" and "'.$lastdate.'"')->get();
	if($Restey->num_rows()>0)
	{	
		$a=1;
		foreach($Restey->result() as $Restey1)
		{
			if($Restey1->jobcardid!=0)
			{
				$jobcard=$this->getjobcardno($Restey1->jobcardid);
				
			}else{
				
				$jobcard='';
				
			}
			
			
				$machinedetails=$this->getmachineotherdetails($Restey1->itemid);
				if(count($machinedetails)>0)
				{
				$fincode=$machinedetails['fincode'];
				$specification=$machinedetails['specialization'];
				$part=$machinedetails['name'];
				$rack_location=$machinedetails['rack_location'];
				$unit=$machinedetails['unit'];
				}else{
				$fincode='';
				$specification='';
				$part='';
				$rack_location='';
				$unit='';
				}
				
				if($unit<>'')
				{
				$unitname=$this->getunit($unit);
				}else
				{
				$unitname='';
				}
				
				if($Restey1->usertype==1)
				{
					$issueduser=$this->storemodel->getudata($Restey1->issuedto);
					
				}else if($Restey1->usertype==2)
				{
					$issueduser=$this->storemodel->getnoncrmusername($Restey1->issuedto);
					
				}else
				{
					$issueduser='';
				}


				
				$scheduler_data[] = array(
				'sr_no'=>$a,
				'jobcard'=>$jobcard,
				'itemname'=>$part,
				'fincode'=>$fincode,
				'specialization'=>$specification,
				'racklocation'=>$rack_location,
				'qty'=>$Restey1->stock." ".$unitname,
				'issuedto'=>$issueduser,
				'issuedOn'=>date('d-M-Y H:i:s',strtotime($Restey1->issuedOn))
				);
	     		
		$a++;
		}
	
	}
	$results = array(
    "sEcho" => 1,
    "iTotalRecords" => count($scheduler_data),
    "iTotalDisplayRecords" => count($scheduler_data),
    "aaData"=>$scheduler_data);
    echo json_encode($results);	
    
	
}


function todyasissueditemsforhousekeeping()
{
	
	 $scheduler_data=array();	
	$firstdate=date('Y-m-d')." 00:00:00";
	$lastdate=date('Y-m-d')." 23:59:59";

	$Restey=$this->db->select('a.*')->from('issuegeneralstock a')->where('a.issuedOn BETWEEN "'.$firstdate. '" and "'.$lastdate.'"')->get();
	if($Restey->num_rows()>0)
	{	
		$a=1;
		foreach($Restey->result() as $Restey1)
		{
		$machinedetails=$this->storemodel->getgeneralitemname($Restey1->itemid);
		$unitname=$this->storemodel->getgeneralitemunit($Restey1->itemid);

		if($Restey1->crmuser>0)
		{
		$issueduser=$this->storemodel->getudata($Restey1->crmuser);

		}else if($Restey1->noncrmuser>0)
		{
		$issueduser=$this->storemodel->getnoncrmusername($Restey1->noncrmuser);
		}else
		{
		$issueduser='';
		}

		$scheduler_data[] = array(
		'sr_no'=>$a,
		'itemname'=>$machinedetails,
		'qty'=>floatval($Restey1->qty)." ".$unitname,
		'issuedto'=>$issueduser,
		'issuedOn'=>date('d-M-Y H:i:s',strtotime($Restey1->issuedOn))
		);

		$a++;
		}

	
	}
	$results = array(
    "sEcho" => 1,
    "iTotalRecords" => count($scheduler_data),
    "iTotalDisplayRecords" => count($scheduler_data),
    "aaData"=>$scheduler_data);
    echo json_encode($results);	
    
	
}


function unacknowledgedissueditems()
{
	$this->load->view('store/unacknowledgedissueditems');
}



function storeunaccpteditemsforhousekeepingforaudit()
{
	
		$scheduler_data=array();	
	
		$Restey=$this->db->select('a.*')->from('issuegeneralstock a')->where('a.storeaccept','0')->ORDER_BY('issuedOn','DESC')->get();
		if($Restey->num_rows()>0)
		{	
		$a=1;
		foreach($Restey->result() as $Restey1)
		{
		$machinedetails=$this->storemodel->getgeneralitemname($Restey1->itemid);
		$unitname=$this->storemodel->getgeneralitemunit($Restey1->itemid);

		if($Restey1->crmuser>0)
		{
		$issueduser=$this->storemodel->getudata($Restey1->crmuser);

		}else if($Restey1->noncrmuser>0)
		{
		$issueduser=$this->storemodel->getnoncrmusername($Restey1->noncrmuser);
		}else
		{
		$issueduser='';
		}

		$scheduler_data[] = array(
		'sr_no'=>$a,
		'itemname'=>$machinedetails,
		'qty'=>floatval($Restey1->qty)." ".$unitname,
		'issuedto'=>$issueduser,
		'issuedOn'=>date('d-M-Y H:i:s',strtotime($Restey1->issuedOn))
		);

		$a++;
		}

	
	}
	$results = array(
    "sEcho" => 1,
    "iTotalRecords" => count($scheduler_data),
    "iTotalDisplayRecords" => count($scheduler_data),
    "aaData"=>$scheduler_data);
    echo json_encode($results);	
    
	
}



function storeunaccpteditemsforaudit()
{
	
	 $scheduler_data=array();	
	
	$Restey=$this->db->select('a.*')->from('issuestocktousers a')->where('a.storeaccept','0')->ORDER_BY('issuedOn','DESC')->get();
	if($Restey->num_rows()>0)
	{	
		$a=1;
		foreach($Restey->result() as $Restey1)
		{
			if($Restey1->jobcardid!=0)
			{
				$jobcard=$this->getjobcardno($Restey1->jobcardid);
			}else{
				
				$jobcard='';
				
			}
			
			
				$machinedetails=$this->getmachineotherdetails($Restey1->itemid);
				if(count($machinedetails)>0)
				{
				$fincode=$machinedetails['fincode'];
				$specification=$machinedetails['specialization'];
				$part=$machinedetails['name'];
				$rack_location=$machinedetails['rack_location'];
				$unit=$machinedetails['unit'];
				}else{
				$fincode='';
				$specification='';
				$part='';
				$rack_location='';
				$unit='';
				}
				
				if($unit<>'')
				{
				$unitname=$this->getunit($unit);
				}else
				{
				$unitname='';
				}
				
				if($Restey1->usertype==1)
				{
					$issueduser=$this->storemodel->getudata($Restey1->issuedto);
					
				}else if($Restey1->usertype==2)
				{
					$issueduser=$this->storemodel->getnoncrmusername($Restey1->issuedto);
					
				}else
				{
					$issueduser='';
				}


				
				$scheduler_data[] = array(
				'sr_no'=>$a,
				'jobcard'=>$jobcard,
				'itemname'=>$part,
				'fincode'=>$fincode,
				'specialization'=>$specification,
				'racklocation'=>$rack_location,
				'qty'=>$Restey1->stock." ".$unitname,
				'issuedto'=>$issueduser,
				'issuedOn'=>date('d-M-Y H:i:s',strtotime($Restey1->issuedOn))
				);
	     		
		$a++;
		}
	
	}
	$results = array(
    "sEcho" => 1,
    "iTotalRecords" => count($scheduler_data),
    "iTotalDisplayRecords" => count($scheduler_data),
    "aaData"=>$scheduler_data);
    echo json_encode($results);	
    
	
}


function todaysissueditembystore()
{
    
    $this->load->view('store/todaysissueditems');
    
}

function freight_approval_dashboard()
{
 
$this->load->view('store/freightapprovaldashboard');
	
}

function updatefreightcharges(){
    
    $pono = $this->uri->segment(3);
    
    if($this->input->post('freight_type')=='' || $this->input->post('freight_charges')==''){
        $this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Please fill all the required fileds.</div><br/>');
			redirect(page_url.'Reporting/freight_approval_dashboard');
    }else{
    $data = array('freighttype'=>$this->input->post('freight_type'),
    'fright_charges'=>$this->input->post('freight_charges'),
    'packing_charges'=>$this->input->post('packing_charges'),
    'freight_added'=>'1');
    
    $this->db->where('pono',$pono);
    $this->db->update('purchase_order',$data);
    $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank You! Freight charges successfully added.</div><br/>');
			redirect(page_url.'Reporting/freight_approval_dashboard');
}
}

function generatebomcostsheet()
{
	$this->load->view('costsheetformat/index');
}


function itemmrn()
{
	$this->load->view('store/itemmrnqc');
	
}


function mrnitemrequest()
{

			$scheduler_data = array();
		$rest=$this->db->select('a.*')->from('purchase_order_view a')->where('a.approved','1')->where('a.gateentrycomplete','0')->where('a.completed','0')->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
$vendorname=$this->getvendorname($restyui1->vendor);
				if($restyui1->potype==0)
				{
					
					$type="Machine Item";
					$itemname=$this->getmachineitemname($restyui1->itemid);
					$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
					if(count($otherdetails)>0)
					{

					$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
					$conunit=$otherdetails['conunit'];
					$conweight=$otherdetails['conweight'];
					}else{
					$fincode='';
					$specification='';
					$conunit=0;
					$conweight='';
					}
			
					
				}else if($restyui1->potype==1)
				{
					$type="General Item";
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
					$conunit=0;
					$conweight='';
					
				}
				

						if($restyui1->potype==0)
						{
							
						if($restyui1->source==1)
						{
						

							$job=$this->db->select('job_card_no,item_id as machineid')->from('order_instruments_view')->where('id',$restyui1->jobcard)->get();
							if($job->num_rows()>0)
							{
							foreach($job->result() as $job1);
							$macid=$job1->machineid;
							}else{
							$macid=0;
							}

						}else 
						{
							$macid=0;
						}

						}else 
						{
							$macid=0;
						}
					

						/** **/
						$prevqty=$this->storemodel->checkifanypreviousqtyisinwardedinmrn($restyui1->itemid,$restyui1->pono,$restyui1->id);

                        if($prevqty>0)
                        {
                           $requiretobeinwarded=$restyui1->qty-$prevqty;
                        }else
                        {
                             $requiretobeinwarded=$restyui1->qty;
                        }
						/** END **/
							

						$restyu=$this->db->select('shortname')->from('units_view')->where('id',$restyui1->unit)->get();
						if($restyu->num_rows()>0)
						{
						foreach($restyu->result() as $restyu1);
						$unival=$restyu1->shortname;
						}else{
						$unival='';							
						}


	if($conunit>0)
	{

			$restyu1=$this->db->select('shortname')->from('units_view')->where('id',$conunit)->get();
			if($restyu1->num_rows()>0)
			{
			foreach($restyu1->result() as $restyu11);
			$unival121=$restyu11->shortname;
			}else{
			$unival121='';							
			}

		$conversion="<span style='color:red'>Conversion-".$conweight." ".$unival121."</span><br/><span>Total Qty - ".$conweight*$requiretobeinwarded." ".$unival121."</span>";

	}else
	{
		$conversion='';
		$unival121='';
	}


$html='<a href="javascript:;" class="btn btn-warning btn-xs" onclick="opemmodalpopup('."'".$restyui1->id."'".','."'".$restyui1->pono."'".','."'".floatval($requiretobeinwarded)."'".','."'".$unival."'".','."'".$conunit."'".','."'".$conweight."'".','."'".$unival121."'".')">Gate Entry & MRN</a>';


	/** PENDING SINCE **/

$cdate=new DateTime($restyui1->approvedOn);
$tday=new DateTime(date('Y-m-d'));
$difference = $cdate->diff($tday);
/** END **/


            $scheduler_data[] = array('sr_no'=>$i,
            'pendingsince'=>"<strong style='color:red;font-weight:bold;'>".$difference->d."</strong>",
			'pono'=>$restyui1->pono,
			'vendorname'=>$vendorname,
			'itemname'=>$itemname,
			'itemtype'=>$type,
			'qty'=>$requiretobeinwarded ." ".$unival."<br/>".$conversion,
			'prevqty'=>$prevqty ." ".$unival,
			'fincode'=>$fincode,
			'specialization'=>$specification,
			'gentry'=>$html);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);

}

function mrnitemrequestssjlkjljl()
{

			$scheduler_data = array();
		$rest=$this->db->select('a.*')->from('purchase_order a')->where('a.approved','1')->where('a.gateentrycomplete','0')->where('a.completed','0')->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
$vendorname=$this->getvendorname($restyui1->vendor);
				if($restyui1->potype==0)
				{
					
					$type="Machine Item";
					$itemname=$this->getmachineitemname($restyui1->itemid);
					$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
					if(count($otherdetails)>0)
					{

					$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
					$conunit=$otherdetails['conunit'];
					$conweight=$otherdetails['conweight'];
					}else{
					$fincode='';
					$specification='';
					$conunit=0;
					$conweight='';
					}
			
					
				}else if($restyui1->potype==1)
				{
					$type="General Item";
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
					$conunit=0;
					$conweight='';
					
				}
				

						if($restyui1->potype==0)
						{
							
						if($restyui1->source==1)
						{
						

							$job=$this->db->select('job_card_no,item_id as machineid')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
							if($job->num_rows()>0)
							{
							foreach($job->result() as $job1);
							$macid=$job1->machineid;
							}else{
							$macid=0;
							}

						}else 
						{
							$macid=0;
						}

						}else 
						{
							$macid=0;
						}
					

						/** **/
						$prevqty=$this->storemodel->checkifanypreviousqtyisinwardedinmrn($restyui1->itemid,$restyui1->pono,$restyui1->id);

                        if($prevqty>0)
                        {
                           $requiretobeinwarded=$restyui1->qty-$prevqty;
                        }else
                        {
                             $requiretobeinwarded=$restyui1->qty;
                        }
						/** END **/
							

						$restyu=$this->db->select('shortname')->from('units')->where('id',$restyui1->unit)->get();
						if($restyu->num_rows()>0)
						{
						foreach($restyu->result() as $restyu1);
						$unival=$restyu1->shortname;
						}else{
						$unival='';							
						}


	if($conunit>0)
	{

			$restyu1=$this->db->select('shortname')->from('units')->where('id',$conunit)->get();
			if($restyu1->num_rows()>0)
			{
			foreach($restyu1->result() as $restyu11);
			$unival121=$restyu11->shortname;
			}else{
			$unival121='';							
			}

		$conversion="<span style='color:red'>Conversion-".$conweight." ".$unival121."</span><br/><span>Total Qty - ".$conweight*$requiretobeinwarded." ".$unival121."</span>";

	}else
	{
		$conversion='';
		$unival121='';
	}


$html='<a href="javascript:;" class="btn btn-warning btn-xs" onclick="opemmodalpopup('."'".$restyui1->id."'".','."'".$restyui1->pono."'".','."'".floatval($requiretobeinwarded)."'".','."'".$unival."'".','."'".$conunit."'".','."'".$conweight."'".','."'".$unival121."'".')">Gate Entry & MRN</a>';



            $scheduler_data[] = array('sr_no'=>$i,
			'pono'=>$restyui1->pono,
			'vendorname'=>$vendorname,
			'itemname'=>$itemname,
			'itemtype'=>$type,
			'qty'=>$requiretobeinwarded ." ".$unival."<br/>".$conversion,
			'prevqty'=>$prevqty ." ".$unival,
			'fincode'=>$fincode,
			'specialization'=>$specification,
			'gentry'=>$html);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);

}



function mrnitemrequestoldddddddddddddddddddddddddddddddddddddddd()
{

			$scheduler_data = array();
		$rest=$this->db->select('a.*')->from('purchase_order a')->where('a.approved','1')->where('a.gateentrycomplete','0')->where('a.completed','0')->order_by('a.approvedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
$vendorname=$this->getvendorname($restyui1->vendor);
				if($restyui1->potype==0)
				{
					
					$type="Machine Item";
					$itemname=$this->getmachineitemname($restyui1->itemid);
					$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
					if(count($otherdetails)>0)
					{

					$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
					}else{
					$fincode='';
					$specification='';
					}
			
					
				}else if($restyui1->potype==1)
				{
					$type="General Item";
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
					
				}
				

						if($restyui1->potype==0)
						{
							
						if($restyui1->source==1)
						{
						

							$job=$this->db->select('job_card_no,item_id as machineid')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
							if($job->num_rows()>0)
							{
							foreach($job->result() as $job1);
							$macid=$job1->machineid;
							}else{
							$macid=0;
							}

						}else 
						{
							$macid=0;
						}

						}else 
						{
							$macid=0;
						}
					

						/** **/
				$prevqty=$this->storemodel->checkifanypreviousqtyisinwardedinmrn($restyui1->itemid,$restyui1->pono,$restyui1->id);

                        if($prevqty>0)
                        {
                           $requiretobeinwarded=$restyui1->qty-$prevqty;
                        }else
                        {
                             $requiretobeinwarded=$restyui1->qty;
                        }
						/** END **/
							

						$restyu=$this->db->select('shortname')->from('units')->where('id',$restyui1->unit)->get();
						if($restyu->num_rows()>0)
						{
						foreach($restyu->result() as $restyu1);
						$unival=$restyu1->shortname;
						}else{
						$unival='';							
						}



$html='<a href="javascript:;" class="btn btn-warning btn-xs" onclick="opemmodalpopup('."'".$restyui1->id."'".','."'".$restyui1->pono."'".','."'".floatval($requiretobeinwarded)."'".','."'".$unival."'".')">Gate Entry & MRN</a>';


            $scheduler_data[] = array('sr_no'=>$i,
			'pono'=>$restyui1->pono,
			'vendorname'=>$vendorname,
			'itemname'=>$itemname,
			'itemtype'=>$type,
			'qty'=>$requiretobeinwarded ." ".$unival,
			'prevqty'=>$prevqty ." ".$unival,
			'fincode'=>$fincode,
			'specialization'=>$specification,
			'gentry'=>$html);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);

}

function openpoforgateentry()
{

		$scheduler_data = array();
		$genpo=base64_decode($this->uri->segment(3));
		$genp=explode(',',$genpo);
		$rest=$this->db->select('a.*,a.gateentryno as gateno,a.id as mrnid,b.*')->from('mrn_view a')->join('purchase_order_view b','a.poid=b.id')->where('b.approved','1')->where('a.qcstatus','0')->group_by('a.id')->order_by('a.addedOn','DESC')->get();
		//->where('a.completed','0')
			if($rest->num_rows()>0)
			{
			$i=1;
		//	echo "<pre>"; print_r($rest->result()); exit;
			foreach($rest->result() as $restyui1)
			{
			$vendorname=$this->getvendorname($restyui1->vendor);
				if($restyui1->potype==0)
				{
					
					$type="Machine Item";
					$itemname=$this->getmachineitemname($restyui1->itemid);
					$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
					if(count($otherdetails)>0)
					{
					$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
					}else{
					$fincode='';
					$specification='';
					}
			
					
				}else if($restyui1->potype==1)
				{
					$type="General Item";
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
					
				}
				

						if($restyui1->potype==0)
						{
							
						if($restyui1->source==1)
						{

							$job=$this->db->select('job_card_no,item_id as machineid')->from('order_instruments_view')->where('id',$restyui1->jobcard)->get();
							if($job->num_rows()>0)
							{
							foreach($job->result() as $job1);
							$macid=$job1->machineid;
							}else{
							$macid=0;
							}

						}else 
						{
							$macid=0;
						}

						}else 
						{
							$macid=0;
						}
					

						/** **/
						//$prevqty=$this->storemodel->checkifanypreviousqtyisinwarded($restyui1->itemid,$restyui1->pono);

						/** END **/
							

						$restyu=$this->db->select('shortname')->from('units_view')->where('id',$restyui1->unit)->get();
						if($restyu->num_rows()>0)
						{
						foreach($restyu->result() as $restyu1);
						$unival=$restyu1->shortname;
						}else{
						$unival='';							
						}


$originalleftqty=$restyui1->recqty;
$gateentry=$restyui1->gateno;
$billno=$restyui1->billno;

$html='<a href="'.page_url.'Reporting/vendorwisepoforgateentry/'.$restyui1->vendor.'" class="btn btn-warning btn-xs">Open PO(s)</a>';
$checkbox="<input type='checkbox' name='check[]' class='qccheck' id='checkone".$restyui1->mrnid."' onchange='checkitem(".$restyui1->mrnid.");' value='".$restyui1->mrnid."'><input type='hidden' value='".floatval($originalleftqty)."' name='originalqty".$restyui1->mrnid."' id='originalqty".$restyui1->mrnid."'><input type='hidden' value='".$restyui1->itemid."' name='item".$restyui1->mrnid."' id='item".$restyui1->mrnid."'><input type='hidden' value='".$restyui1->pono."' name='po".$restyui1->mrnid."' id='po".$restyui1->mrnid."'><input type='hidden' value='".$restyui1->vendor."' name='vendor".$restyui1->mrnid."' id='vendor".$restyui1->mrnid."'><input type='hidden' value='".$restyui1->source."' name='source".$restyui1->mrnid."' id='source".$restyui1->mrnid."'><input type='hidden' value='".$restyui1->jobcard."' name='jobcardno".$restyui1->mrnid."' id='jobcardno".$restyui1->mrnid."'><input type='hidden' value='".$macid."' name='instrumentid".$restyui1->mrnid."' id='instrumentid".$restyui1->mrnid."'><input type='hidden' value='".$restyui1->unit."' name='unit".$restyui1->mrnid."' id='unit".$restyui1->mrnid."'><input type='hidden' value='".$restyui1->potype."' name='potype".$restyui1->mrnid."' id='potype".$restyui1->mrnid."'>";
$recvqty11=floatval($originalleftqty)." ".$unival;
$recvqty=$recvqty11."<input type='hidden' class='form-control formdata".$restyui1->mrnid."' name='recvqty".$restyui1->mrnid."' id='recvqty".$restyui1->mrnid."' style='display:none' placeholder='RECV QTY' value='".floatval($originalleftqty)."'>";
$gateentry=$gateentry;
$approved='<input type="text" class="form-control allow_decimal formdata'.$restyui1->mrnid.'"  name="approved_qty'.$restyui1->mrnid.'"  id="approved_qty'.$restyui1->mrnid.'" style="display:none;" value="" placeholder="Approved Qty" value="0" onkeyup="allowdeciamlonly(this)">';
$reject='<input type="text" class="form-control allowdecimal formdata'.$restyui1->mrnid.'"  name="reject'.$restyui1->mrnid.'"  id="reject'.$restyui1->mrnid.'"  style="display:none;" value=""  placeholder="Reject Qty" value="0" onkeyup="allowdeciamlonly(this)" >';
$rejectremarks='<textarea class="form-control formdata'.$restyui1->mrnid.'" name="chtype'.$restyui1->mrnid.'" id="chtype'.$restyui1->mrnid.'" style="display:none;" value=""  placeholder="Rejection Remarks"></textarea>';
$rejectfile='<input type="file" class="form-control formdata'.$restyui1->mrnid.'"  name="rejfile'.$restyui1->mrnid.'"  id="rejfile'.$restyui1->mrnid.'"  style="display:none;"  value="" placeholder="Rejection Remarks">';
$save='<input type="submit" class="btn btn-success formdata'.$restyui1->mrnid.'" style="display:none;" >';


	$planneddate=$this->storemodel->gettatformis($restyui1->mrndoneOn);
	
		$cdate=new DateTime($restyui1->mrndoneOn);
		$tday=new DateTime(date('Y-m-d'));
		$difference = $cdate->diff($tday);
		/** END **/
		


			$scheduler_data[] = array('sr_no'=>$checkbox,
			'mrnon'=>date('d-m-Y H:i:s',strtotime($restyui1->mrndoneOn)),
			'pendingsince'=>"<strong style='color:red'>".$difference->d.' Days'."</strong>",
			'pono'=>$restyui1->pono,
			'vendorname'=>$vendorname,
			'itemname'=>$itemname,
			'planneddate'=>$planneddate,
			'itemtype'=>$type,
			'fincode'=>$fincode,
			'specialization'=>$specification,
		    //'previousinwarded'=>$prevqty.' '.$unival,
			//'reqty'=>$originalleftqty.' '.$unival,
			'recqty'=>$recvqty,
			'gateentry'=>$gateentry,
			'billno'=>$billno,
			'approved'=>$approved,
			'reject'=>$reject,
			'rejremarks'=>$rejectremarks,
			'rejectimage'=>$rejectfile);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);

}
function openpoforgateentryss()
{

			$scheduler_data = array();
$genpo=base64_decode($this->uri->segment(3));
$genp=explode(',',$genpo);
		$rest=$this->db->select('a.*,a.gateentryno as gateno,a.id as mrnid,b.*')->from('mrn a')->join('purchase_order b','a.poid=b.id')->where('b.approved','1')->where('a.qcstatus','0')->group_by('a.id')->order_by('a.addedOn','DESC')->get();
		//->where('a.completed','0')
			if($rest->num_rows()>0)
			{
			$i=1;
		//	echo "<pre>"; print_r($rest->result()); exit;
			foreach($rest->result() as $restyui1)
			{
$vendorname=$this->getvendorname($restyui1->vendor);
				if($restyui1->potype==0)
				{
					
					$type="Machine Item";
					$itemname=$this->getmachineitemname($restyui1->itemid);
					$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
					if(count($otherdetails)>0)
					{

					$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
					}else{
					$fincode='';
					$specification='';
					}
			
					
				}else if($restyui1->potype==1)
				{
					$type="General Item";
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
					
				}
				

						if($restyui1->potype==0)
						{
							
						if($restyui1->source==1)
						{

							$job=$this->db->select('job_card_no,item_id as machineid')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
							if($job->num_rows()>0)
							{
							foreach($job->result() as $job1);
							$macid=$job1->machineid;
							}else{
							$macid=0;
							}

						}else 
						{
							$macid=0;
						}

						}else 
						{
							$macid=0;
						}
					

						/** **/
						//$prevqty=$this->storemodel->checkifanypreviousqtyisinwarded($restyui1->itemid,$restyui1->pono);

						/** END **/
							

						$restyu=$this->db->select('shortname')->from('units')->where('id',$restyui1->unit)->get();
						if($restyu->num_rows()>0)
						{
						foreach($restyu->result() as $restyu1);
						$unival=$restyu1->shortname;
						}else{
						$unival='';							
						}


$originalleftqty=$restyui1->recqty;
$gateentry=$restyui1->gateno;
$billno=$restyui1->billno;

$html='<a href="'.page_url.'Reporting/vendorwisepoforgateentry/'.$restyui1->vendor.'" class="btn btn-warning btn-xs">Open PO(s)</a>';
$checkbox="<input type='checkbox' name='check[]' class='qccheck' id='checkone".$restyui1->mrnid."' onchange='checkitem(".$restyui1->mrnid.");' value='".$restyui1->mrnid."'><input type='hidden' value='".floatval($originalleftqty)."' name='originalqty".$restyui1->mrnid."' id='originalqty".$restyui1->mrnid."'><input type='hidden' value='".$restyui1->itemid."' name='item".$restyui1->mrnid."' id='item".$restyui1->mrnid."'><input type='hidden' value='".$restyui1->pono."' name='po".$restyui1->mrnid."' id='po".$restyui1->mrnid."'><input type='hidden' value='".$restyui1->vendor."' name='vendor".$restyui1->mrnid."' id='vendor".$restyui1->mrnid."'><input type='hidden' value='".$restyui1->source."' name='source".$restyui1->mrnid."' id='source".$restyui1->mrnid."'><input type='hidden' value='".$restyui1->jobcard."' name='jobcardno".$restyui1->mrnid."' id='jobcardno".$restyui1->mrnid."'><input type='hidden' value='".$macid."' name='instrumentid".$restyui1->mrnid."' id='instrumentid".$restyui1->mrnid."'><input type='hidden' value='".$restyui1->unit."' name='unit".$restyui1->mrnid."' id='unit".$restyui1->mrnid."'><input type='hidden' value='".$restyui1->potype."' name='potype".$restyui1->mrnid."' id='potype".$restyui1->mrnid."'>";
$recvqty11=floatval($originalleftqty)." ".$unival;
$recvqty=$recvqty11."<input type='hidden' class='form-control formdata".$restyui1->mrnid."' name='recvqty".$restyui1->mrnid."' id='recvqty".$restyui1->mrnid."' style='display:none' placeholder='RECV QTY' value='".floatval($originalleftqty)."'>";
$gateentry=$gateentry;
$approved='<input type="text" class="form-control allow_decimal formdata'.$restyui1->mrnid.'"  name="approved_qty'.$restyui1->mrnid.'"  id="approved_qty'.$restyui1->mrnid.'" style="display:none;" value="" placeholder="Approved Qty" value="0" onkeyup="allowdeciamlonly(this)">';
$reject='<input type="text" class="form-control allowdecimal formdata'.$restyui1->mrnid.'"  name="reject'.$restyui1->mrnid.'"  id="reject'.$restyui1->mrnid.'"  style="display:none;" value=""  placeholder="Reject Qty" value="0" onkeyup="allowdeciamlonly(this)" >';
$rejectremarks='<textarea class="form-control formdata'.$restyui1->mrnid.'" name="chtype'.$restyui1->mrnid.'" id="chtype'.$restyui1->mrnid.'" style="display:none;" value=""  placeholder="Rejection Remarks"></textarea>';
$rejectfile='<input type="file" class="form-control formdata'.$restyui1->mrnid.'"  name="rejfile'.$restyui1->mrnid.'"  id="rejfile'.$restyui1->mrnid.'"  style="display:none;"  value="" placeholder="Rejection Remarks">';
$save='<input type="submit" class="btn btn-success formdata'.$restyui1->mrnid.'" style="display:none;" >';


	$planneddate=$this->storemodel->gettatformis($restyui1->mrndoneOn);
	

			$scheduler_data[] = array('sr_no'=>$checkbox,
			'mrnon'=>date('d-m-Y H:i:s',strtotime($restyui1->mrndoneOn)),
			'pono'=>$restyui1->pono,
			'vendorname'=>$vendorname,
			'itemname'=>$itemname,
			'planneddate'=>$planneddate,
			'itemtype'=>$type,
			'fincode'=>$fincode,
			'specialization'=>$specification,
		    //'previousinwarded'=>$prevqty.' '.$unival,
			//'reqty'=>$originalleftqty.' '.$unival,
			'recqty'=>$recvqty,
			'gateentry'=>$gateentry,
			'billno'=>$billno,
			'approved'=>$approved,
			'reject'=>$reject,
			'rejremarks'=>$rejectremarks,
			'rejectimage'=>$rejectfile);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);

}



function openpoforgateentryolddddddddddddddddddddddddddddddddddddddd()
{

			$scheduler_data = array();
$genpo=base64_decode($this->uri->segment(3));
$genp=explode(',',$genpo);
			$rest=$this->db->select('a.*,a.gateentryno as gateno,a.id as mrnid,b.*')->from('mrn a')->join('purchase_order b','a.poid=b.id')->where('b.approved','1')->where('a.qcstatus','0')->group_by('a.id')->order_by('a.addedOn','DESC')->get();
		//->where('a.completed','0')
			if($rest->num_rows()>0)
			{
			$i=1;
		//	echo "<pre>"; print_r($rest->result()); exit;
			foreach($rest->result() as $restyui1)
			{
$vendorname=$this->getvendorname($restyui1->vendor);
				if($restyui1->potype==0)
				{
					
					$type="Machine Item";
					$itemname=$this->getmachineitemname($restyui1->itemid);
					$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
					if(count($otherdetails)>0)
					{

					$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
					}else{
					$fincode='';
					$specification='';
					}
			
					
				}else if($restyui1->potype==1)
				{
					$type="General Item";
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
					
				}
				

						if($restyui1->potype==0)
						{
							
						if($restyui1->source==1)
						{

							$job=$this->db->select('job_card_no,item_id as machineid')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
							if($job->num_rows()>0)
							{
							foreach($job->result() as $job1);
							$macid=$job1->machineid;
							}else{
							$macid=0;
							}

						}else 
						{
							$macid=0;
						}

						}else 
						{
							$macid=0;
						}
					

						/** **/
						//$prevqty=$this->storemodel->checkifanypreviousqtyisinwarded($restyui1->itemid,$restyui1->pono);

						/** END **/
							

						$restyu=$this->db->select('shortname')->from('units')->where('id',$restyui1->unit)->get();
						if($restyu->num_rows()>0)
						{
						foreach($restyu->result() as $restyu1);
						$unival=$restyu1->shortname;
						}else{
						$unival='';							
						}


$originalleftqty=$restyui1->recqty;
$gateentry=$restyui1->gateno;
$billno=$restyui1->billno;

$html='<a href="'.page_url.'Reporting/vendorwisepoforgateentry/'.$restyui1->vendor.'" class="btn btn-warning btn-xs">Open PO(s)</a>';
$checkbox="<input type='checkbox' name='check[]' class='qccheck' id='checkone".$restyui1->mrnid."' onchange='checkitem(".$restyui1->mrnid.");' value='".$restyui1->mrnid."'><input type='hidden' value='".floatval($originalleftqty)."' name='originalqty".$restyui1->mrnid."' id='originalqty".$restyui1->mrnid."'><input type='hidden' value='".$restyui1->itemid."' name='item".$restyui1->mrnid."' id='item".$restyui1->mrnid."'><input type='hidden' value='".$restyui1->pono."' name='po".$restyui1->mrnid."' id='po".$restyui1->mrnid."'><input type='hidden' value='".$restyui1->vendor."' name='vendor".$restyui1->mrnid."' id='vendor".$restyui1->mrnid."'><input type='hidden' value='".$restyui1->source."' name='source".$restyui1->mrnid."' id='source".$restyui1->mrnid."'><input type='hidden' value='".$restyui1->jobcard."' name='jobcardno".$restyui1->mrnid."' id='jobcardno".$restyui1->mrnid."'><input type='hidden' value='".$macid."' name='instrumentid".$restyui1->mrnid."' id='instrumentid".$restyui1->mrnid."'><input type='hidden' value='".$restyui1->unit."' name='unit".$restyui1->mrnid."' id='unit".$restyui1->mrnid."'><input type='hidden' value='".$restyui1->potype."' name='potype".$restyui1->mrnid."' id='potype".$restyui1->mrnid."'>";
$recvqty11=floatval($originalleftqty)." ".$unival;
$recvqty=$recvqty11."<input type='hidden' class='form-control formdata".$restyui1->mrnid."' name='recvqty".$restyui1->mrnid."' id='recvqty".$restyui1->mrnid."' style='display:none' placeholder='RECV QTY' value='".floatval($originalleftqty)."'>";
$gateentry=$gateentry;
$approved='<input type="text" class="form-control allow_decimal formdata'.$restyui1->mrnid.'"  name="approved_qty'.$restyui1->mrnid.'"  id="approved_qty'.$restyui1->mrnid.'" style="display:none;" value="" placeholder="Approved Qty" value="0" onkeyup="allowdeciamlonly(this)">';
$reject='<input type="text" class="form-control allowdecimal formdata'.$restyui1->mrnid.'"  name="reject'.$restyui1->mrnid.'"  id="reject'.$restyui1->mrnid.'"  style="display:none;" value=""  placeholder="Reject Qty" value="0" onkeyup="allowdeciamlonly(this)" >';
$rejectremarks='<textarea class="form-control formdata'.$restyui1->mrnid.'" name="chtype'.$restyui1->mrnid.'" id="chtype'.$restyui1->mrnid.'" style="display:none;" value=""  placeholder="Rejection Remarks"></textarea>';
$rejectfile='<input type="file" class="form-control formdata'.$restyui1->mrnid.'"  name="rejfile'.$restyui1->mrnid.'"  id="rejfile'.$restyui1->mrnid.'"  style="display:none;"  value="" placeholder="Rejection Remarks">';
$save='<input type="submit" class="btn btn-success formdata'.$restyui1->mrnid.'" style="display:none;" >';



			$scheduler_data[] = array('sr_no'=>$checkbox,
			'mrnon'=>date('d-m-Y H:i:s',strtotime($restyui1->mrndoneOn)),
			'pono'=>$restyui1->pono,
			'vendorname'=>$vendorname,
			'itemname'=>$itemname,
			'itemtype'=>$type,
			'fincode'=>$fincode,
			'specialization'=>$specification,
		    //'previousinwarded'=>$prevqty.' '.$unival,
			//'reqty'=>$originalleftqty.' '.$unival,
			'recqty'=>$recvqty,
			'gateentry'=>$gateentry,
			'billno'=>$billno,
			'approved'=>$approved,
			'reject'=>$reject,
			'rejremarks'=>$rejectremarks,
			'rejectimage'=>$rejectfile);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);

}


function finishedvalue()
{
	$this->load->view('FMS/finishedcost');
	
}

function finishedvaluelist()
{
	
	$a=1;
			$imported=$this->db->select('a.order_id,a.job_card_no,d.internal_order_no,a.item_id,c.mvalue,c.instruments_name')->from('order_instruments a')->join('order_planning b','a.item_id=b.jobcard_id')->join('presto_instruments
			c','a.item_id=c.id')->join('prestogroup_orders d','a.order_id=d.order_id')->where('b.factory !=','7')->where('a.complete','1')->where('a.finalpacked','0')->where('c.mvalue>','0')->where('c.type','0')->get();
				if($imported->num_rows()>0)
				{
				foreach($imported->result() as $imported1)
				{
                    $fourty=0.4*$imported1->mvalue;
                    $fin=floatval($imported1->mvalue-$fourty);
					$scheduler_data[] = array(
					'sr_no'=>$a,
					'instrument'=>ucwords(strtolower($imported1->instruments_name)),
					'iono'=>$imported1->internal_order_no,
					'jobcardno'=>$imported1->job_card_no,
					'machinevalue'=>floatval($fin));
					$a++;
				}

				}
	
	$results = array(
    "sEcho" => 1,
    "iTotalRecords" => count($scheduler_data),
    "iTotalDisplayRecords" => count($scheduler_data),
    "aaData"=>$scheduler_data);
    echo json_encode($results);	
}



	function onstagejobcardfordependent($flowid,$stockvalue,$productionid)
{
				/** GET MACHINE CP **/
				$cp=0;
				$total=array();
				$restyuiioo=$this->db->select('cp')->from('machinecp')->get();
				if($restyuiioo->num_rows()>0)
				{
				foreach($restyuiioo->result() as $restyuiioo1);
				$cp=$restyuiioo1->cp;
				}
				/** END **/
				$htm='';
				$htm.="<table border='1' style='width:100%;'><tr style='background-color:white;text-align:left;'><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:70%;'>INSTRUMENT.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:15%;'>JOBCARD.</th><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:15%;'>VALUE.</th></tr>";

				/** Get Process Name **/
				$process=$this->db->select('f.fileno,d.file_number,a.jumpfabricationappl,d.mvalue,a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow,d.fabrication,f.planstartsfrom')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('b.flowstage',$flowid)->where('b.userstatus','0')->get();
				/** End **/
				
				if($process->num_rows()>0)
				{
				//	echo "<pre>"; print_r($process->result());exit;
				$i=1;
				foreach($process->result() as $proc)
				{
				$flowstage=$flowid;

				/** Check for dependency **/

				$checkdepend=$this->db->select('dependency')->from('fms_flow')->where('flow_id',$flowstage)->where('dependency','1')->get();
				$depend=$checkdepend->num_rows();
				//echo $depend;exit;
				if($depend==1)
				{
				/** Check if Dependent flow has any data in orderstage **/
				$availableflowstage=array();
				/** Check if plan has been started from setorder 1 **/
				$plannedstageorderno=$this->fmsmodel->getplanstartsfromorderno($proc->planstartsfrom);
				if($plannedstageorderno!='1')
				{
				$ifdependexist=$this->db->select("a.dependentflowid")->from('flowdependency a')->join('order_stage b','a.dependentflowid=b.flowstage')->where('b.jobcardid',$proc->jobcardid)->where('a.flowid',$flowstage)->get();
				if($ifdependexist->num_rows()>0)
				{
				foreach($ifdependexist->result() as $ifdependexist1)
				{
				$availableflowstage[]=$ifdependexist1->dependentflowid;
				}

				}
				}else
				{
				$ifdependexist=$this->db->select("a.dependentflowid")->from('flowdependency a')->where('a.flowid',$flowstage)->get();
				if($ifdependexist->num_rows()>0)
				{
				foreach($ifdependexist->result() as $ifdependexist1)
				{
				$availableflowstage[]=$ifdependexist1->dependentflowid;
				}

				}
				}


				$checkskiiped=$this->fmsmodel->checkifdatahascomeskipped($proc->jobcardid,$flowstage);


				if(count($checkskiiped)>0)
				{
				$availableflowstage = array_diff($availableflowstage,$checkskiiped);
				}
				/** End **/
				if(count($availableflowstage)>0)
				{	

				$availableflowstages = "'" . implode ( "', '", $availableflowstage ) . "'";
				if($plannedstageorderno!='1')
				{
				$this->db->select('a.dependentflowid')->from('flowdependency a')->join('order_stage b','a.dependentflowid=b.flowstage')->where('a.flowid',$flowstage);

				//$availableflowstages = "'" . implode ( "', '", $availableflowstage ) . "'";

				//$this->db->where_in('a.dependentflowid',$availableflowstages,false);

				$this->db->where_in('a.dependentflowid',$availableflowstages,false);
				$this->db->where('b.jobcardid',$proc->jobcardid)->where('b.orderid',$proc->originalorderid);
				}else
				{
				$this->db->select('a.dependentflowid')->from('flowdependency a')->where('a.flowid',$flowstage);
				$this->db->where_in('a.dependentflowid',$availableflowstages,false);

				}


				$restt=$this->db->get();
				if($restt->num_rows()>0)
				{
				$dependcount=$restt->num_rows();
				$donarr=array();
				$donarr[]=0;
				//echo "<pre>";print_r($restt->result());exit;
				foreach($restt->result() as $resttyu)
				{


				$this->db->select('id,userstatus')->from('order_stage')->where('flowstage',$resttyu->dependentflowid)->where('jobcardid',$proc->jobcardid)->where('orderid',$proc->originalorderid);

				$this->db->order_by('id','DESC');
				$this->db->limit(1);
				$isdone=$this->db->get();
				if($isdone->num_rows()>0)
				{
				foreach($isdone->result() as $isdone11);
				$donarr[]=$isdone11->userstatus;

				}else
				{
				$markapp=1;
				}

				}

				if($dependcount==array_sum($donarr))
				{
				$markapp=1;
				}else{
				$markapp=0;
				}

				}else{
				$markapp=1;
				}
				}else{ $markapp=1; }

				}else{

				$markapp=1;
				}
				/** End **/

				/** Check for merge fms intersection **/
				if($markapp==1)
				{

				$markapp=$this->fmsmodel->checkformergerpoint($flowstage,$proc->jobcardid,$proc->originalorderid,$proc->fabrication,$proc->jumpfabricationappl);
				}

				if($markapp==1)
				{
						$machinewisevalue=$this->getmachinevalue($cp,$stockvalue,$proc->mvalue,$proc->jobcardid);
						$total[]=$machinewisevalue;

				$htm.="<tr><td style='padding:2px 2px 2px 2px; text-align:left;'>".strtoupper($proc->instruments_name)."</td>";

				$htm.="<td style='padding:2px 2px 2px 2px; text-align:left'>".strtoupper($proc->job_card_no)."</td>";
				$htm.="<td style='padding:2px 2px 2px 2px; text-align:left;'>".$machinewisevalue."</td></tr>";



				}



				}


				}else{

				$htm.="<td colspan='3' style='padding:2px 2px 2px 2px; text-align:center;'>NO Machines Available</td>";

				}

				$htm.='</table>';


	
	
	$overallstagetot=$this->giveoveralldata($total);
	return array('table'=>$htm,'stagetotal'=>$overallstagetot);
}




	function orderstarttoendreport()
	{
	$this->load->view('FMS/orderstarttoendreport');	
	}

	
	public function list_orderstarttoendreport()
	{
	   
		
		$scheduler_data = array();
		
		/******************************************************/
	$query=$this->db->select('a.id,a.job_card_no,b.order_type,b.added_on,b.internal_order_no,b.company_name,c.instruments_name,b.po_number,d.first_name,d.last_name,e.factory')->from('order_instruments a')->join('prestogroup_orders b','a.order_id=b.order_id')->join('presto_instruments c','a.item_id=c.id')->join('system_users d','b.marketing_person=d.user_id')->join('order_planning e','a.id=e.jobcard_id')->where('a.complete','1')->where('a.packed','1')->where('b.order_status','1')->where('b.closeorder','0')->where_in('e.factory','1','4','5',false)->order_by('b.added_on','DESC')->get();

		$i=1;
		foreach($query->result() as $row)
		{

			$html=$this->fmsmodel->stepwisetime($row->id,$row->instruments_name,$row->job_card_no,$row->factory);
		
			
			$scheduler_data[] = array('sr_no'=>$i,
			'added_on'=>$row->added_on,
			'order_type'=>strtoupper($row->order_type),
			'marketing_person'=>$row->first_name.' '.$row->last_name,
			'po_number'=>strtoupper($row->po_number),
			'company_name'=>strtoupper($row->company_name),
			'internal_order_no'=>strtoupper($row->internal_order_no),
			 'job_card_no'=>strtoupper($row->job_card_no),
			'itemname'=>$html);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}



function yourindents()
{
$this->load->view('store/yourcreated_indent');
}





function yourcreated_indent()
{
	$user_id=$_SESSION['logged_in']['user_id'];
	$scheduler_data = array();
		$htm="";
		
		$rest=$this->db->select('a.id as intendid,a.addedOn,a.prefix,a.indendno,e.first_name,e.last_name,a.itemid, a.indent_type,a.approvalstatus,a.remarks')->from('intend_request a')->join('system_users e','e.user_id=a.addedBy')->order_by('a.addedOn','DESC')->group_by('a.indendno')->get();
		//->where('a.addedBY',$user_id)
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				
				if($restyui1->indent_type=='1'){
					$indenttype = "Machine Related Items";
				$rest123=$this->db->select('b.part as item_name,a.qty,a.unit,b.specification,b.size_in_mm,a.remarks')->from('intend_request a')->join('machine_parts_with_picture b','a.itemid=b.id')->where('a.indendno',$restyui1->indendno)->get();	
				}else{
					$indenttype = "General Items";
				$rest123=$this->db->select('b.item_name,a.qty,a.unit')->from('intend_request a')->join('house_keeping_items b','a.itemid=b.id')->where('a.indendno',$restyui1->indendno)->get();	
				}
				
				
				if($rest123->num_rows()>0)
				{
					$htm='';
					$htm.="<table border='1' style='width:500px;'><tr style='background-color:white;text-align:center;'><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;width:200px;'>ITEM.</th><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;width:300px;'>SPECS.</th><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;width:300px;'>SIZE.</th><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;width:150px;'>QTY.</th></tr>";
					foreach($rest123->result() as $rest1231)
					{
						if($restyui1->indent_type=='1'){
							$spec=$rest1231->specification;
							$size=$rest1231->size_in_mm;
						}else
						{
							$spec='';
							$size='';
						}
					
						
							$htm.="<tr>";
							$htm.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($rest1231->item_name)."</td>";
							$htm.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".$spec."</td>";
							$htm.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".$size."</td>";

							$htm.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($rest1231->qty)."</td>";
							$htm.="</tr>";
					}
					
					$htm.="</table>";
			}
			
			
			$indstatus=$this->getindentstatus($restyui1->indendno,$restyui1->approvalstatus);
			
$html='<a href="'.page_url.'Reporting/approvependingindents/'.$restyui1->indendno.'" class="btn btn-warning btn-xs">Mark as Approved</a>';

$cancel = '<a href="'.page_url.'Reporting/cancel_indent/'.$restyui1->indendno.'"><span class="btn btn-danger btn-xs">Cancel</span></a>';

			$scheduler_data[] = array('sr_no'=>$i,
			'indent_type'=>$indenttype,
			'prno'=>$restyui1->prefix.'-'.$restyui1->indendno,
			'itemdetail'=>$htm,
			'createdby'=>ucfirst($restyui1->first_name." ".$restyui1->last_name),
			'createdon'=>date('d-M-Y g:i A',strtotime($restyui1->addedOn)),
			'remarks'=>$restyui1->remarks,
			'markrecvd'=>$indstatus);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
	


}	




function getindentstatus($indno,$approvestatus)
{
			if($approvestatus==0)
			{
				return "Indent not Approved";
			}else
			{

				$restyu=$this->db->select('id,approvalstatus,prno')->from('purchase_request')->where('sourceid',$indno)->get();
				if($restyu->num_rows()>0)
				{
					foreach($restyu->result() as $restyu11);
					if($restyu11->approvalstatus=='1')
					{
						/** CHECK FOR PO **/
						$restyu11=$this->db->select('id,approved,pono,completed')->from('purchase_order')->where('prno',$restyu11->prno)->get();
						if($restyu11->num_rows()>0)
						{

							foreach($restyu11->result() as $restyu11111);

							if($restyu11111->approved=='1')
							{

								if($restyu11111->completed=='1')
								{

									return "PO Completed"; 
								}else
								{
									return "PO Under Process";
								}

							}else
							{
								if($restyu11111->approved=='0')
								{

								return "PO -".$restyu11111->pono." is Pending for Approval";
								}else
								{
									return "PO -".$restyu11111->pono."is Rejected";
								}
							}



						}else
						{
							return "PO Not Raised"; 
						}

						/** END **/



					}else
					{

						return "PR -".$restyu11->prno." Not Approved";
					}


				}else
				{

					return "PR Not Raised";
				}




			}



}



function senddispatchdatatosalesforce($orderid,$docketnumber,$billno,$billdate,$packet,$shipment,$dodamount,$freightamount,$documentpath,$docketimage,$billcopy)
{

$sforderno='';
$odtype='';
	$restey=$this->db->select('sforderno,order_type')->from('prestogroup_orders')->where('order_id',$orderid)->get();
	if($restey->num_rows()>0)
	{
		foreach($restey->result() as $restey1);

		$sforderno=$restey1->sforderno;
		$odtype=$restey1->order_type;

	}

$post = [
    'username' => 'gaurav@prestogroup.in',
    'password' => 'perform@2021',
    'grant_type'   => 'password',
    'client_id'=>'3MVG9Y6d_Btp4xp4S10slvMAduKdtgZQSQHCtfSzx3tl1wgyumCAXZ5bauqfwVO5v3yE1ANqVgZLVp7JOVvLh',
    'client_secret'=>'4FE70F9317CE78F0D7731B6B7BED0F3B13950A7284D1AA998BDAE67970AE40B4'
];





$cURLConnection = curl_init('https://login.salesforce.com/services/oauth2/token');
curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $post);
curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

$apiResponse = curl_exec($cURLConnection);
curl_close($cURLConnection);
$resultssssssssssss=json_decode($apiResponse,true);


$restyeure=$this->db->select('*')->from('order_instruments')->where('order_id',$orderid)->get();

if($restyeure->num_rows()>0)
{

$r=0;
	foreach($restyeure->result() as $row)
	{

				if(count($resultssssssssssss)>0)
				{

				$acctoken=$resultssssssssssss['access_token'];	
				if($acctoken<>'')
				{
				$lineid=$row->lineitemno;
				$machinno=$row->mserialno;
				$serialno=str_replace('-','',$row->mserialno);

				/*** LINE ITEM UPDATE CODE **/

				$ch = curl_init();

				curl_setopt($ch, CURLOPT_URL, 'https://presto.my.salesforce.com/services/data/v50.0/sobjects/OpportunityLineItem/'.$lineid);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
				// FOR DATE YYYY-MM-DDThh:mm:ssZ

				$billfinaldate=date('Y-m-d').'T10:00:00Z';
				if($odtype=='SERVICE')
				{

				$serialno='';
				}else
				{
				$serialno=$serialno;

				}
				$data=array("Serial_No__c"=>$serialno,"Machine_No__c"=>$serialno,'Invoice_No__c'=>$billno,'Invoice_Date__c'=>$billfinaldate,'Dispatch_Date__c'=>$billfinaldate,'Dispatched_Order__c'=>'true');
				$esjson=stripcslashes(json_encode($data,JSON_UNESCAPED_SLASHES));
				//$billdate=date('Y-m-d',strtotime($billdate));
				curl_setopt($ch, CURLOPT_POSTFIELDS,$esjson);
				$headers = array();
				$headers[] = 'Authorization: Bearer '.$acctoken;
				$headers[] = 'Content-Type: application/json';
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

				$result = curl_exec($ch);
				
				
				if (curl_errno($ch)) {
				echo 'Error:' . curl_error($ch);
				}
				curl_close($ch);

				/** end **/






				}
				}





	$r++;
}



				if($sforderno<>'')
				{
				/** INTERNAL ORDER UPDATE **/

$docketimge=$documentpath.$docketimage;
$billcopy=$documentpath.$billcopy;
				$ch = curl_init();

				
				curl_setopt($ch, CURLOPT_URL, 'https://presto.my.salesforce.com/services/data/v50.0/sobjects/Internal_Factory_Order__c/'.$sforderno);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
				// FOR DATE YYYY-MM-DDThh:mm:ssZ

				$data=array("Total_No_of_Packets__c"=>$packet,"Mode_of_Despatch__c"=>$shipment,'Docket_No__c'=>$docketnumber,'DOD_Amount__c'=>$dodamount,'Freight__c'=>$freightamount,'Docket_Copy_Uploaded__c'=>$docketimge,'Invoice_Copy_Uploaded__c'=>$billcopy);
				$esjson1=stripcslashes(json_encode($data,JSON_UNESCAPED_SLASHES));
				curl_setopt($ch, CURLOPT_POSTFIELDS,$esjson1);
				$headers = array();
				$headers[] = 'Authorization: Bearer '.$acctoken;
				$headers[] = 'Content-Type: application/json';
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

				$result12344 = curl_exec($ch);
							
		
				if (curl_errno($ch)) {
				echo 'Error:' . curl_error($ch);
				}
				curl_close($ch);
			}

				/** END **/



}





}





function historystorerecieptssssss()
{
	
	$date = date('Y-m-d');
	$date = strtotime($date);
	$date = date('Y-m-d',strtotime("-15 day", $date)).' 00:00:00';
			
			
	$rack='';
	$scheduler_data = array();
			$rest=$this->db->select('a.*,c.mrndoneOn,c.poid,c.unit')->from('mrn_history a')->join('mrn c','a.record_id=c.id')->where('storereciept','1')->where('a.added_on>=',$date)	->order_by('a.id','DESC')->group_by('a.id')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				
				
				$podetail=$this->getpodetails($restyui1->po_no);
			
			if(count($podetail)>0)
			{
			$potype=$podetail['potype'];
			$sources=$podetail['source'];
			$sourceid=$podetail['sourceid'];
			$unit=$podetail['unit'];
			$pr=$podetail['prno'];
			
			}else
			{
			
			$potype='';
			$sources='';
			$sourceid='';
			$unit='';
			$pr='';
			
			}
				
				
				
				
				if($potype==0)
				{
				$itemname=$this->getmachineitemname($restyui1->itemid);
				$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
				if(count($otherdetails)>0)
				{
			
				$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
					$rack=$otherdetails['rack_location'];
				}else{
					$fincode='';
					$specification='';
				}
				}else{
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
				}

				$storerevon=date('d-M-Y h:i:s',strtotime($restyui1->storerecvon));
				
				$storeby=$this->getusername($restyui1->storerecvby);
				
				$jobcards=$this->getjobcardid($restyui1->record_id);
				
					if($sources=='1')
				{
				    $jobcard=$this->getjobcardno($jobcards);
				    $source='Jobcard - '.$jobcard;
				    
				}else if($sources=='2')
				{
				     $sourceid=$this->getsourceid($pr);
				      $source='INDENT IND-'.$sourceid;
				      
				}else
				{
				    
				      $source='IMS AUTO PR';
				}
				
				
				
				$uname=$this->getunit($restyui1->unit);
			$scheduler_data[] = array('sr_no'=>$i,
				'mrnon'=>date('d-M-Y H:i:s',strtotime($restyui1->mrndoneOn)),
				'ponumber'=>$restyui1->po_no,
				'source'=>$source,
				'item'=>$itemname,
				'racklocation'=>$rack,
				'fincode'=>$fincode,
				'specialization'=>$specification,
				'qty'=>floatval($restyui1->accept_qty)." ".$uname,
				'recvon'=>$storerevon,
				'recvby'=>$storeby);
				
			$i++;
			}
			}
			
			
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
		
	
	
}


function historystorerecieptOLDDDDDDDDDDDDDDDDDDDDD()
{
	
	$date = date('Y-m-d');
	$date = strtotime($date);
	$date = date('Y-m-d',strtotime("-7 day", $date)).' 00:00:00';
			
			
	$rack='';
	$scheduler_data = array();
			$rest=$this->db->select('a.*,c.mrndoneOn')->from('mrn_history a')->join('mrn c','a.record_id=c.id')->where('storereciept','1')->where('a.added_on>=',$date)	->order_by('a.id','DESC')->group_by('a.id')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				
				
				$podetail=$this->getpodetails($restyui1->po_no);
			
			if(count($podetail)>0)
			{
			$potype=$podetail['potype'];
			$sources=$podetail['source'];
			$sourceid=$podetail['sourceid'];
			$unit=$podetail['unit'];
			
			}else
			{
			
			$potype='';
			$sources='';
			$sourceid='';
			$unit='';
			
			}
				
				
				
				
				if($potype==0)
				{
				$itemname=$this->getmachineitemname($restyui1->itemid);
				$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
				if(count($otherdetails)>0)
				{
			
				$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
					$rack=$otherdetails['rack_location'];
				}else{
					$fincode='';
					$specification='';
				}
				}else{
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
				}

				$storerevon=date('d-M-Y h:i:s',strtotime($restyui1->storerecvon));
				
				$storeby=$this->getusername($restyui1->storerecvby);
				
				$jobcards=$this->getjobcardid($restyui1->record_id);
				
					if($sources=='1')
				{
				    $jobcard=$this->getjobcardno($jobcards);
				    $source='Jobcard - '.$jobcard;
				    
				}else if($sources=='2')
				{
				    
				      $source='INDENT IND-'.$sourceid;
				      
				}else
				{
				    
				      $source='IMS AUTO PR';
				}
				
				
				
				$uname=$this->getunit($unit);
			$scheduler_data[] = array('sr_no'=>$i,
				'mrnon'=>date('d-M-Y H:i:s',strtotime($restyui1->mrndoneOn)),
				'ponumber'=>$restyui1->po_no,
				'source'=>$source,
				'item'=>$itemname,
				'racklocation'=>$rack,
				'fincode'=>$fincode,
				'specialization'=>$specification,
				'qty'=>floatval($restyui1->accept_qty)." ".$uname,
				'recvon'=>$storerevon,
				'recvby'=>$storeby);
				
			$i++;
			}
			}
			
			
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
		
	
	
}


function getusername($userid)
{

$query=$this->db->select('first_name,last_name')->from('system_users')->where('user_id',$userid)->get();
if($query->num_rows()>0)
{

	foreach($query->result() as $que);

	return $que->first_name." ".$que->last_name;
}
else
{
	return '';

}


}






function mrnitemrequesthistoryoldddddddd()
{

			$scheduler_data = array();
		$rest=$this->db->select('a.*,b.*,a.gateentryno as mrngatentry')->from('mrn a')->join('purchase_order b','a.pono=b.pono')->where('a.mrndone','1')->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
$vendorname=$this->getvendorname($restyui1->vendor);
				if($restyui1->potype==0)
				{
					
					$type="Machine Item";
					$itemname=$this->getmachineitemname($restyui1->itemid);
					$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
					if(count($otherdetails)>0)
					{

					$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
					}else{
					$fincode='';
					$specification='';
					}
			
					
				}else if($restyui1->potype==1)
				{
					$type="General Item";
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
					
				}
				

						if($restyui1->potype==0)
						{
							
						if($restyui1->source==1)
						{
						

							$job=$this->db->select('job_card_no,item_id as machineid')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
							if($job->num_rows()>0)
							{
							foreach($job->result() as $job1);
							$macid=$job1->machineid;
							}else{
							$macid=0;
							}

						}else 
						{
							$macid=0;
						}

						}else 
						{
							$macid=0;
						}
					

						/** **/
		
							

			$restyu=$this->db->select('shortname')->from('units')->where('id',$restyui1->unit)->get();
			if($restyu->num_rows()>0)
			{
			foreach($restyu->result() as $restyu1);
			$unival=$restyu1->shortname;
			}else{
			$unival='';							
			}


			$gby=$this->getusername($restyui1->gateentryBy);

			if($restyui1->qcstatus=='1')
			{
			$qcstatus="Done";
			$bac="green";
			}else
			{
			$qcstatus="Not Done";
			$bac="red";
			}

            $scheduler_data[] = array('sr_no'=>$i,
           	'mrndoc'=>'<a href="'.page_url.'Store/mrnformat/'.$restyui1->mrngatentry.'" target="_blank">Print MRN For Gate No.'.$restyui1->mrngatentry.'</a>',
			'pono'=>$restyui1->pono,
			'vendorname'=>$vendorname,
			'itemname'=>$itemname,
			'itemtype'=>$type,
			'qty'=>$restyui1->reqty ." ".$unival,
			'recvqty'=>$restyui1->recqty ." ".$unival,
			'fincode'=>$fincode,
			'specialization'=>$specification,
			'gentry'=>$restyui1->mrngatentry,
			'billno'=>$restyui1->billno,
			'gateentryby'=>$gby,
			'gateentryOn'=>date('Y-m-d H:i:s',strtotime($restyui1->gateentryOn)),
			'qcstatus'=>"<span style='color:".$bac.";font-weight:bold;'>".$qcstatus."</span>");
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);

}




function pendingemails()
{

		$scheduler_data = array();
		$rest=$this->db->select('a.vendor')->from('purchase_order a')->where('a.approved','1')->where('a.emailnotified','0')->where('a.completed','0')->group_by('a.vendor')->get();
		if($rest->num_rows()>0)
		{
		$i=1;
		foreach($rest->result() as $restyui1)
		{
		$vendorname=$this->getvendorname($restyui1->vendor);

		$html='<a href="'.page_url.'poformat/tcpdf/index.php?quoteid='.$restyui1->vendor.'" class="btn btn-warning btn-xs">Review & Send Email</a>';


		$scheduler_data[] = array('sr_no'=>$i,

		'vendorname'=>$vendorname, //.$restyui1->vendor,
		'gentry'=>$html);
		$i++;
		}
		}

		$results = array(
		"sEcho" => 1,
		"iTotalRecords" => count($scheduler_data),
		"iTotalDisplayRecords" => count($scheduler_data),
		"aaData"=>$scheduler_data);
		echo json_encode($results);

}



function pending_indent_for_histroy()
{
	$scheduler_data = array();
		$htm="";
		
		$rest=$this->db->select('a.id as intendid,a.addedOn,a.unit,a.prefix,a.indendno,e.first_name,e.last_name,a.itemid, a.indent_type,a.remarks,a.approvalstatus,a.approvedOn,a.approvedBy,a.cancelby,a.cancelledOn,a.cancel_status')->where('a.approvalstatus !=','0')->from('intend_request a')->join('system_users e','e.user_id=a.addedBy')->order_by('a.addedOn','DESC')->group_by('a.indendno')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			//echo "<pre>"; print_r($rest->result() ); exit;
			foreach($rest->result() as $restyui1)
			{
				
				$unitname=$this->getunit($restyui1->unit);
				if($restyui1->indent_type=='1'){
					$indenttype = "Machine Related Items";
				$rest123=$this->db->select('b.part as item_name,a.qty,a.unit,b.specification,b.size_in_mm')->from('intend_request a')->join('machine_parts_with_picture b','a.itemid=b.id')->where('a.indendno',$restyui1->indendno)->get();	
				}else{
					$indenttype = "General Items";
				$rest123=$this->db->select('b.item_name,a.qty,a.unit')->from('intend_request a')->join('house_keeping_items b','a.itemid=b.id')->where('a.indendno',$restyui1->indendno)->get();	
				}
				
				
				if($rest123->num_rows()>0)
				{
					$htm='';
					$htm.="<table border='1' style='width:700px;'><tr style='background-color:white;text-align:center;width:100px;'><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;width:200px;'>ITEM.</th><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;width:100px;'>QTY.</th>";

if($restyui1->indent_type=='1'){
					$htm.="<th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;width:300px;'>SPECS.</th></tr>";
				}


					foreach($rest123->result() as $rest1231)
					{
					
						
							$htm.="<tr>";
							$htm.="<td style='padding:2px 2px 2px 2px; text-align:center;width:200px;'>".strtoupper($rest1231->item_name)."</td>";

							$htm.="<td style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>".strtoupper($rest1231->qty)." ".$unitname."</td>";

if($restyui1->indent_type=='1'){
							$htm.="<td style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>".$rest1231->specification."<br/>Size: ".$rest1231->size_in_mm."</td>";
						}


							$htm.="</tr>";
					}
					
					$htm.="</table>";
			}
			
			
		
		if(($restyui1->approvalstatus=='1') && ($restyui1->cancel_status==0))
		{
$html='<a href="javascript:;" class="btn btn-warning btn-xs">Approved</a>';

$name=$this->storemodel->getsusername($restyui1->approvedBy);
$con=date('d-M-Y h:i:s',strtotime($restyui1->approvedOn));

}else if(($restyui1->approvalstatus=='0') && ($restyui1->cancel_status==0))
{
$html='<a href="javascript:;" class="btn btn-warning btn-xs">Pending For Approval</a>';
$name='';
$con='';
}else
{

$html = '<a href="javascript:;"><span class="btn btn-danger btn-xs">Cancelled</span></a>';
$name=$this->storemodel->getsusername($restyui1->cancelby);
$con=date('d-M-Y h:i:s',strtotime($restyui1->cancelledOn));
}

			$scheduler_data[] = array('sr_no'=>$i,
			'indent_type'=>$indenttype,
			'prno'=>$restyui1->prefix.'-'.$restyui1->indendno,
			'itemdetail'=>$htm,
			'remarks'=>$restyui1->remarks,
			'createdby'=>ucfirst($restyui1->first_name." ".$restyui1->last_name),
			'createdon'=>date('d-M-Y g:i A',strtotime($restyui1->addedOn)),
			'markrecvd'=>$html,
			'cancel'=>$name,
			'cancelon'=>$con);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
	
	


}	

function indentreportforaudit()
{
	$this->load->view('store/pending_indentforaudiit');

}


function senddispatchintimationtoclient($orderid)
{
	
	$html='';
	$restyeur=$this->db->select('marketing_person,internal_order_no,contact_person,company_name,mobile_number,docketnumber,docketno,billcopy,billno,billdate,totalpacket,shipmentmode,dodamount,frieghtamount,email,tname')->from('prestogroup_orders')->where('order_id',$orderid)->get();
	if($restyeur->num_rows()>0)
	{
		$insdata=$this->getorderinstumentdetails($orderid);
		
		foreach($restyeur->result() as $restyeur1);

$marketemail=$this->fmsmodel->getmarketingpersonemail($restyeur1->marketing_person);

if($restyeur1->contact_person!='')
{
$html.='Dear '.ucwords($restyeur1->contact_person).' Ji,<br/><br/>';
}else
{
$html.='Dear Customer,<br/><br/>';
}
$html.='We are pleased to inform you that your order no. '.$restyeur1->internal_order_no.' for the testing instruments have been dispatched as on '.date('d-M-Y').'  as per the details below.<br/><br/>';
$html.='Instruments Name';
$html.=$insdata.'<br/><br/><br/>';	


$html.='Total No. of Packets: '.$restyeur1->totalpacket.'<br/>';
$html.='Mode of Shipment: '.$restyeur1->shipmentmode.'<br/>';
$html.='Transporter Type: '.$restyeur1->tname.'<br/>';
$html.='Docket No / Tempo No.: '.$restyeur1->docketnumber.'<br/>';
$html.='Freight Amount: '.$restyeur1->frieghtamount.'<br/>';
$html.='DOD Amount: '.$restyeur1->dodamount.'<br/><br/>';

$html.='Please unpack and check the safe delivery of material.<br/><br/>';
$html.='Kindly acknowledge receipt of the same. For any technical or Service query please mail us : <a href="mailto:service@prestogroup.com">service@prestogroup.com</a><br/>';
$html.='We thank you for working with PRESTO,<br/><br/>';
$html.='We assure you of our best possible Service at all times.<br/><br/>';

$html.='Regards,<br/>
Dispatch Team<br/>
Presto Stantest Private Limited<br/>
I-42, DLF Industrial Area, Phase-1<br/>
Delhi Mathura Road, Faridabad-121003<br/>
Haryana, India<br/>
Phone - <a href="tel:0129-4272727">0129-4272727</a> (50 Lines)<br/>
Email - <a href="mailto:ops@prestogroup.com">ops@prestogroup.com</a>';


$billcopy=UPLOADPATH.'docketno/'.$restyeur1->billcopy;
$docketcopy=UPLOADPATH.'docketno/'.$restyeur1->docketno;
//echo $html; exit;

	$subjectname = "Order Dispatched -".$restyeur1->company_name;
	$this->email->set_mailtype("html");
	$this->email->to($restyeur1->email);	
	$this->email->cc('audit2@prestogroup.com,'.$marketemail.',service@prestogroup.com,ops@prestogroup.com,audit@prestogroup.com');
	
	$this->email->bcc('sdsrbh5@gmail.com');
	$this->email->from('ops@prestogroup.com','PRESTO STANTEST PRIVATE LIMITED');
	$this->email->subject($subjectname);
	$this->email->message($html);
	if($restyeur1->billcopy<>'')
	{
	$this->email->attach($billcopy);
	}
	if($restyeur1->docketno<>'')
	{
	$this->email->attach($docketcopy);
	}
	$result11=$this->email->send();


if($result11)
{
	/**** SMS INTEGRATION***/

	$smsmessage='We have Dispatched ordered PRESTO Testing Instruments.Details mailed to your e-mail id.%0a%0a Presto Testing Instruments.';

	$postData = array(
	'authkey' => '266631AuRXQ3UyZ5c839e9b',
	'mobiles' => $restyeur1->mobile_number.',8447031736',
	'message' => $smsmessage,
	'sender' => 'PRESTO',
	'route' => '4'
	);


	$url="http://api.msg91.com/api/sendhttp.php";
	$ch = curl_init();
	curl_setopt_array($ch, array(
	CURLOPT_URL => $url,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_POST => true,
	CURLOPT_POSTFIELDS => $postData
	));



	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$response = curl_exec($ch);
	$err = curl_error($ch);

	curl_close($ch); 

	if ($err) {
	echo "cURL Error #:" . $err;
	} else {
	//echo $response;
	} 

}

	/**** SMS INTEGRATION***/
	

//exit;

	}



}


function getorderinstumentdetails($orderid)
{
	$table='';
	$resteyue=$this->db->select('a.qty,b.instruments_name')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->where('a.order_id',$orderid)->get();
	if($resteyue->num_rows()>0)
	{
		$table.='<table style="padding: 10px;
        border: 1px solid black;
        border-collapse: collapse;">';
		foreach($resteyue->result() as $resteyue1)
		{
		$table.='<tr style="padding: 10px;
        border: 1px solid black;
        border-collapse: collapse;">
		<td style="padding: 10px;
        border: 1px solid black;
        border-collapse: collapse;">'.$resteyue1->instruments_name.'</td>
		<td style="padding: 10px;
        border: 1px solid black;
        border-collapse: collapse;">'.$resteyue1->qty.'</td>
		</tr>';
		}

		$table.='</table>';

	}

	return $table;
}



function mrnitemrequesthistoryforaccounts()
{

$conunit=0;
$conweight='';
			$scheduler_data = array();
		$rest=$this->db->select('a.*,b.*,a.gateentryno as mrngatentry,a.id as mrn_id')->from('mrn a')->join('purchase_order b','a.poid=b.id')->where('a.mrndone','1')->where('a.account_accepted',0)->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
$vendorname=$this->getvendorname($restyui1->vendor);
				if($restyui1->potype==0)
				{
					
					$type="Machine Item";
					$itemname=$this->getmachineitemname($restyui1->itemid);
					$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
					if(count($otherdetails)>0)
					{

					$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
					$conunit=$otherdetails['conunit'];
					$conweight=$otherdetails['conweight'];
					}else{
					$fincode='';
					$specification='';
					}
			
					
				}else if($restyui1->potype==1)
				{
					$type="General Item";
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
					
				}
				

						if($restyui1->potype==0)
						{
							
						if($restyui1->source==1)
						{
						

							$job=$this->db->select('job_card_no,item_id as machineid')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
							if($job->num_rows()>0)
							{
							foreach($job->result() as $job1);
							$macid=$job1->machineid;
							}else{
							$macid=0;
							}

						}else 
						{
							$macid=0;
						}

						}else 
						{
							$macid=0;
						}
					

						/** **/
		
							

			$restyu=$this->db->select('shortname')->from('units')->where('id',$restyui1->unit)->get();
			if($restyu->num_rows()>0)
			{
			foreach($restyu->result() as $restyu1);
			$unival=$restyu1->shortname;
			}else{
			$unival='';							
			}


			$gby=$this->getusername($restyui1->gateentryBy);

			if($restyui1->qcstatus=='1')
			{
			$qcstatus="Done";
			$bac="green";
			}else
			{
			$qcstatus="Not Done";
			$bac="red";
			}

			if($conunit>0)
			{
			$unival1=$this->storemodel->getunit($conunit);
			$finalreq="Converted Qty ".$conweight*$restyui1->reqty." ".$unival1;
			$finalrecv="Converted Qty ".$conweight*$restyui1->recqty." ".$unival1;
			}else
			{
			$finalreq='';
			$finalrecv='';
			}

	$accept = "<input type='checkbox' name='accept[]' id='accept' class='accept' value='".$restyui1->mrn_id."'>";
            $scheduler_data[] = array('sr_no'=>$i,
            'accept'=>$accept,
           	'mrndoc'=>'<a href="'.page_url.'Store/mrnformat/'.$restyui1->mrngatentry.'/1" target="_blank">Print MRN For Gate No.'.$restyui1->mrngatentry.'</a>',
			'pono'=>'<a href="'.page_url.'Store/po/'.$restyui1->pono.'" target="_blank">'.$restyui1->pono.'</a>',
			'vendorname'=>$vendorname,
			'itemname'=>$itemname,
			'itemtype'=>$type,
			'qty'=>$restyui1->reqty." ".$unival."<br/>".$finalreq,
			'recvqty'=>$restyui1->recqty." ".$unival."<br/>".$finalrecv,
			'fincode'=>$fincode,
			'specialization'=>$specification,
			'gentry'=>$restyui1->mrngatentry,
			'billno'=>$restyui1->billno,
			'gateentryby'=>$gby,
			'gateentryOn'=>date('Y-m-d H:i:s',strtotime($restyui1->gateentryOn)),
			'qcstatus'=>"<span style='color:".$bac.";font-weight:bold;'>".$qcstatus."</span>");
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);

}


function accountsmrnreport()
{


	$this->load->view('store/mrnreportforaccounts');
}


function adddetailsforaccounts()
{

$segment=$this->input->post('segment');
$customername='';
$customercontact='';
$tempono='';
$shipmentpartner='';
$orderid=$this->input->post('orderid');
$iono=$this->input->post('iono');
$packets=$this->input->post('packets');
$shipment=$this->input->post('shipment');
$tempono=$this->input->post('tempono');

if($shipment=='By Hand')
{
$customername=$this->input->post('customername');
$customercontact=$this->input->post('customercontact');

}else if($shipment=='By Tempo')
{
$tempono=$this->input->post('tempono');


}else
{
$shipmentpartner=$this->input->post('shipmentpartner');
if($shipmentpartner=='OTHER')
{
$shipmentpartner=$this->input->post('othershipmentpartner');
}else
{
$shipmentpartner=$shipmentpartner;
}
}


$data=array('tname'=>$shipmentpartner,'ttype'=>$shipment,'shipmentmode'=>$shipment,'customername'=>$customername,'customercontact'=>$customercontact,'totalpacket'=>$packets,'tempono'=>$tempono);
//echo "<pre>"; print_r($data); exit;
$this->db->where('order_id',$orderid);
$this->db->update('prestogroup_orders',$data);

redirect(page_url.'Reporting/dispatchfortommorow/'.$segment);

}

function getfilleddetails()
{

$iono=$this->input->post('internalorderno');
$Restue=$this->db->select('tname,ttype,docketnumber,shipmentmode,totalpacket,marketing_person')->from('prestogroup_orders')->where('internal_order_no',$iono)->get();

if($Restue->num_rows()>0)
{

foreach($Restue->result() as $Restue1);

$a= $Restue1->tname.'|'.$Restue1->ttype.'|'.$Restue1->docketnumber.'|'.$Restue1->shipmentmode.'|'.$Restue1->totalpacket.'|'.$Restue1->marketing_person;

echo $a; exit;

}else
{
	echo "NA"; exit;
}


}

function dispatchforaccounts()
{

	$this->load->view('FMS/dispatchforrommorowforaccounts');
}



public function dispatchfortommorow_order_listforaccounts()
	{
		$scheduler_data = array();
		$urldata = $this->uri->segment(3);
		$userid = $this->uri->segment(4);
		$this->db->select('a.pono,a.tempono,a.sono,a.tname,a.gstno,a.ttype,a.docketnumber,a.totalpacket,a.docketno,a.movedOn,a.order_id, a.order_type, a.marketing_person,a.po_number, a.company_name,a.closedOn, a.address, a.email, a.mobile_number, a.internal_order_no, a.discount, a.order_value_after_discount, a.advance_amount, a.payment_terms, a.installation_charges, a.order_status, a.added_on,a.packing_type, a.installation_type, b.user_id, b.title, b.first_name, b.last_name, a.installation_amount, a.packing_charges, a.packing_amount, a.freight_type, a.freight_amount,a.remarks,ttype,tname,totalpacket,customername,customercontact')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left');
		if($userid<>''){
		    $this->db->where('a.marketing_person',$userid);
		}
		$query = $this->db->where('a.order_status','1')->where('a.closeorder','0')->where('a.movetodispatch','1')->where('a.totalpacket !=','')->order_by('a.order_id','desc')->get();
		$res = $query->result();
		$i=1;
		if($query->num_rows()>0)
		{
		foreach($res as $row)
		{
			
		$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1');
			$this->db->where('finalpacked','0'); 

			$completedjobcard=$this->db->get();
			$completedjobcards=$completedjobcard->num_rows();
			
			$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name,a.finalpacked,a.job_card_no,a.mserialno,a.extraserialno')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			$alljobcard=$query1->num_rows();
			if($alljobcard>0)
			{
				foreach($query1->result() as $alluee);
					$finalpack=$alluee->finalpacked;
					
			}else{ $finalpack=0; }
			
		if($alljobcard<>0)
		{
		if($completedjobcards==$alljobcard)
		{
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			
			$html = "<table border='1' style='width:500px;'><tr style='background-color:white;'><th style='padding:2px 2px 2px 2px;width:300px;'>INSTRUMENT</th><th style='padding:2px 2px 2px 2px;width:100px;'>JOB CARD NO.</th><th style='padding:2px 2px 2px 2px;width:100px;'>SERIAL NO.</th></tr>";
			$instrumentsss = array();
			
			
			
			foreach($query1->result() as $instruments){
				if($instruments->complete=='1'){
					$backgroundcolor = "background-color:#10C469; color:white !important; font-weight:bold;";
				}else{
					$backgroundcolor="";
				}
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->instruments_name)."</td>";
				//$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->qty)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->job_card_no)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->mserialno)."</td>";
				$html.="</tr>";
			}
			$html.="</table>";
			$ins_charges = "";
			/** Get order planned **/
			$restt=$this->db->select('count(id) as totaljobcard')->from('order_instruments')->where('order_id',$row->order_id)->get();
			foreach($restt->result() as $instcount);
			$totjobcard= $instcount->totaljobcard;
			
			
	
			
			if($row->installation_charges=='1'){
				$charges= "<strong>INSTALLATION TYPE </strong>".$row->installation_type."<br> <strong>INSTALLATION CHARGES</strong> - REQUIRED";
				
				$ins_charges= $charges."<br> AMOUNT - ".$row->installation_amount;
			}else{
					$ins_charges = "NOT REQUIRED";
			}
			
			if($row->packing_charges=='1'){
				$packcharges = "PAID BY PARTY";
				$packingcharges= $packcharges."<br> Amount - <strong>".$row->packing_amount."</strong>";
			}else{
					$packingcharges = "INCLUSIVE";
			}
			
			if($row->freight_type=='1'){
				$freigntcharges = "TO PAY BASIS";
				
			}else if($row->freight_type=='2'){
				$freigntcharges = "PAID BY PRESTO";
			}else if($row->freight_type=='3'){
				$frtcharges = "BILLED IN INVOICE";
				$freigntcharges= $frtcharges."<br> Amount - <strong>".$row->freight_amount."</strong>";
			}else{
				$freigntcharges = "OWN PICK-UP";
					
			}
			
			$closedon=date('d-M-Y g:i A',strtotime($row->movedOn));
			$plannedtime = date('d-M-Y g:i A', strtotime($closedon . ' +1 day'));

if($row->installation_charges==1)
			{
				$ser=1;
			}else{
				
				$ser=0;
			}
			
			
$printpackinglabel='<a href="'.page_url.'Reporting/generatepackingslip/'.$row->order_id.'" target="_blank"><span class="btn btn-success btn-sm">Print Packing Label</span></a>';
			
			if($finalpack=='1')
{
			$restyupacku=$this->db->select('addedOn')->from('order_finalpacking_details')->where('orderid',$row->order_id)->get();	
				if($restyupacku->num_rows()>0)
{
				foreach($restyupacku->result() as $restyupacku2);
				$actualtime=date('d-M-Y g:i A',strtotime($restyupacku2->addedOn));
				
}else{  

	$actualtime="";
					}
		$completeorder='Task has been done';		
	
}else{  $actualtime=""; 
	 
	 if($row->ttype=='' || $row->tname=='' || $row->totalpacket=='')
	{	

	$sendtobill='<a href="javascript:;" onclick="updatedetailsforaccount('."'".$row->order_id."'".','."'".$row->internal_order_no."'".','."'".$ser."'".')"><span class="btn btn-warning btn-sm">Send to Accounts</span></a>';
	$completeorder='';
	}else
	{
		  $completeorder='<a href="javascript:;" onclick="markstagedone('."'".$row->order_id."'".','."'".$row->internal_order_no."'".','."'".$ser."'".')"><span class="btn btn-warning btn-sm">Mark as Done</span></a>';


		$html1="Packets- ".$row->totalpacket.'<br/>';
		$html1.="Transport Type- ".$row->ttype.'<br/>';
		if($row->ttype=='By Hand')
		{
		$html1.="Customer Name- ".$row->customername.'<br/>';
		$html1.="Customer Contact- ".$row->customercontact.'<br/>';
		}else if($row->ttype=='By Tempo')
		{
		$html1.="Tempo No.- ".$row->docketnumber.'<br/>';
		}else
		{
		$html1.="Transporter Name- ".$row->tname.'<br/>';
		}

		 $sendtobill=$html1;
		
		//$sendtobill='';
	}

	  	
	 // $completeorder='<a href="javascript:;" onclick="markstagedone('."'".$row->order_id."'".','."'".$row->internal_order_no."'".','."'".$ser."'".')"><span class="btn btn-warning btn-sm">Mark as Done</span></a>'; 

}
if($row->docketno<>'')
{
    $dock="<a href='".page_url."image_bank/docketno/".$row->docketno."' download>Docket Image</a>";
}else
{ 
     $dock='';
}
	

	//$completeorder='<a href="javascript:;" onclick="markstagedone('."'".$row->order_id."'".','."'".$row->internal_order_no."'".','."'".$ser."'".')"><span class="btn btn-warning btn-sm">Mark as Done</span></a>';

	//$sendtobill='';


$po='';
			if(file_exists(sfpo.$row->internal_order_no.'.pdf'))
			{
			$po="<a href='".page_url."sfpo/".$row->internal_order_no.".pdf' target='_blank'>".$row->internal_order_no."</a>";
			}else
			{
			if(file_exists(sfpo.$row->pono.'.PDF'))
			{
			$po="<a href='".page_url."sfpo/".$row->internal_order_no.".PDF' target='_blank'>".$row->internal_order_no."</a>";
			}else
			{
			
			
			}
			}
		
			$scheduler_data[] = array('sr_no'=>$i,
			'internal_order_no'=>strtoupper($row->internal_order_no),
			'company_name'=>strtoupper($row->company_name),
			'gst'=>$row->gstno,
			'itemname'=>$html,
			'packet'=>$row->totalpacket,
			'transporter'=>$row->tname,
			'transportertype'=>$row->ttype,
			'tempono'=>$row->tempono, 
			'freigntcharges'=>$freigntcharges,
			'payment_terms'=>strtoupper($row->payment_terms),
			'sono'=>$row->sono,
			'pocopy'=>$po

		);
			
			$i++;
		}
		}
		}
	}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}





function readyforpackingforservice()
	{
		$this->load->view('FMS/ordersreadyforpackingforservice');
	}
	
	function readyforpackinglistforservice()
	{
		$scheduler_data = array();
		$pack=$this->db->select('b.factory,b.id as planid,a.id,a.job_card_no,b.plannedOn,c.instruments_name')->from('order_instruments a')->join('order_planning b','a.id=b.jobcard_id')->join('presto_instruments c','a.item_id=c.id')->join('prestogroup_orders d','a.order_id=d.order_id')->where('a.complete','1')->where('a.packed','0')->where('d.order_type','SERVICE')->get();
if($pack->num_rows()>0)
{
	$t=1;
	foreach($pack->result() as $pack1)
	{
		
		if($pack1->factory!='2' && $pack1->factory!='3')
		{
				$timest=$this->db->select('addedOn')->from('order_stage')->where('jobcardid',$pack1->id)->order_by('addedOn','DESC')->limit(1)->get();
				if($timest->num_rows()>0)
				{
				foreach($timest->result() as $timest1);

				$timesp=date('d-M-Y g:i A',strtotime($timest1->addedOn));

				}else{ $timesp="";   }
		
		}else if($pack1->factory=='2')
		{
			$timest=$this->db->select('addedOn')->from('instockfms')->where('planid',$pack1->planid)->get();
				if($timest->num_rows()>0)
				{
				foreach($timest->result() as $timest1);

				$timesp=date('d-M-Y g:i A',strtotime($timest1->addedOn));

				}else{ $timesp="";   }
			
			
		}else if($pack1->factory=='3')
		{
			
			$timest=$this->db->select('updatedOn')->from('boughtoutfms')->where('planid',$pack1->planid)->get();
				if($timest->num_rows()>0)
				{
				foreach($timest->result() as $timest1);

				$timesp=date('d-M-Y g:i A',strtotime($timest1->updatedOn));

				}else{ $timesp="";   }
			
		}
		
		/** PLANNED DATE **/
		$tattime=date('g:i A',strtotime($timesp));
		$times=date('Y-m-d',strtotime($timesp));
		$TATDATE=date('d-M-Y',strtotime($times."+ 1days")).' '.$tattime;
		
		/** END **/
		

$html='<a href="javascript:;" onclick="markstagedone('."'".$pack1->id."'".','."'".$pack1->job_card_no."'".')"><span class="btn btn-warning">Mark Done</span></a>';
			$scheduler_data[] = array(
			'sr_no'=>$t,
			'timestamp'=>$timesp,
			'jobcardno'=>$pack1->job_card_no,
			'plannedon'=>$TATDATE,
			'machinename'=>$pack1->instruments_name,
			'markdone'=>$html
			);
			
	$t++;
	}
		
		$results = array(
		"sEcho" => 1,
		"iTotalRecords" => count($scheduler_data),
		"iTotalDisplayRecords" => count($scheduler_data),
		"aaData"=>$scheduler_data);
		echo json_encode($results);
}else
{
	$results = array(
		"sEcho" => 1,
		"iTotalRecords" => count($scheduler_data),
		"iTotalDisplayRecords" => count($scheduler_data),
		"aaData"=>$scheduler_data);
		echo json_encode($results);
	
}
		
		
	}
	
	function markpackedforservice()
	{
		$jobcard=$this->uri->segment(3);
		$data=array('jobcardid'=>$jobcard,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
		$this->db->insert('order_packing_details',$data);
		
		$edata=array('packed'=>'1');
		$this->db->where('id',$jobcard);
		$this->db->update('order_instruments',$edata);
		
		$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Packed.</span></div>');
		redirect(page_url.'Reporting/readyforpackingforservice');
		
		
	}
	
	
	
	public function addOrderDayswithphpreload() {

			$data = array(
					'days' => $this->input->post('order_days'),
					'job_card_id' => $this->input->post('hidden_id'),
					'updated_on' => date('Y-m-d H:i:s'),
					'added_by'=> $_SESSION['logged_in']['user_id']
					);
			$result = $this->fmsmismodel->saveOrderDays($data);

			if($result > 0) {
				$this->session->set_flashdata('message', 'Record successfully updated.');
				redirect(page_url.'Reporting/pendingorders');
			}
		}
		
		
		public function addOrderDays() {

			$data = array(
					'days' => $this->input->post('order_days'),
					'job_card_id' => $this->input->post('hidden_id'),
					'updated_on' => date('Y-m-d H:i:s'),
					'added_by'=> $_SESSION['logged_in']['user_id']
					);
			$result = $this->fmsmismodel->saveOrderDays($data);

			
			$Date = date('Y-m-d');
			$finaldate=date('d-M-Y', strtotime($Date. ' + '.$this->input->post('order_days').' days'));

			echo $this->input->post('order_days').'|'.$finaldate;
		}
			function mrnitemrequesthistory()
{

$date = date('Y-m-d');
			$date = strtotime($date);
			$date = date('Y-m-d',strtotime("-200 day", $date)).' 00:00:00';
			$scheduler_data = array();
		$rest=$this->db->select('a.*,a.id as mrnid,b.*,a.gateentryno as mrngatentry')->from('mrn_view a')->join('purchase_order_view b','a.poid=b.id')->where('a.mrndone','1')->where('a.addedOn>=',$date)->order_by('a.addedOn','DESC')->group_by('a.id')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
$vendorname=$this->getvendorname($restyui1->vendor);
				if($restyui1->potype==0)
				{
					
					$type="Machine Item";
					$itemname=$this->getmachineitemname($restyui1->itemid);
					$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
					if(count($otherdetails)>0)
					{

					$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
					$conunit=$otherdetails['conunit'];
					}else{
					$fincode='';
					$specification='';
					$conunit='';
					}
			
					
				}else if($restyui1->potype==1)
				{
					$type="General Item";
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
					$conunit='';
					
				}
				

						if($restyui1->potype==0)
						{
							
						if($restyui1->source==1)
						{
						

							$job=$this->db->select('job_card_no,item_id as machineid')->from('order_instruments_view')->where('id',$restyui1->jobcard)->get();
							if($job->num_rows()>0)
							{
							foreach($job->result() as $job1);
							$macid=$job1->machineid;
							}else{
							$macid=0;
							}

						}else 
						{
							$macid=0;
						}

						}else 
						{
							$macid=0;
						}
					

						/** **/
		
							

			$restyu=$this->db->select('shortname')->from('units_view')->where('id',$restyui1->unit)->get();
			if($restyu->num_rows()>0)
			{
			foreach($restyu->result() as $restyu1);
			$unival=$restyu1->shortname;
			}else{
			$unival='';							
			}


			if($conunit>0)
			{

	$restyuss=$this->db->select('shortname')->from('units_view')->where('id',$conunit)->get();
			if($restyuss->num_rows()>0)
			{
			foreach($restyuss->result() as $restyu1dd);
			$unival12345=$restyu1dd->shortname;
			}else{
			$unival12345='';							
			}
		}else
		{
			$unival12345='';
		}


			$gby=$this->getusername($restyui1->gateentryBy);

			if($restyui1->qcstatus=='1')
			{
			$qcstatus="Done";
			$bac="green";
			$editmrn='';
			}else
			{
			$qcstatus="Not Done";
			$bac="red";
			$editmrn='<a href='.page_url."Reporting/editmrn/".$restyui1->mrnid.' class="btn btn-info btn-xs">EDIT MRN</a>';
	
			}
			
			
			if($restyui1->account_accepted == 1) {
				$accept = 'YES';
				$accname=$this->getusername($restyui1->account_accepted_by);
				$accon=date('d-M-Y',strtotime($restyui1->account_accepted_on));
			} else {
				$accept = 'NO';
				$accname='';
				$accon='';
			}



            $scheduler_data[] = array('sr_no'=>$i,
           	'mrndoc'=>'<a href="'.page_url.'Store/mrnformat/'.$restyui1->mrngatentry.'" target="_blank">Print MRN For Gate No.'.$restyui1->mrngatentry.'</a>',
			'pono'=>$restyui1->pono,
			'vendorname'=>$vendorname,
			'itemname'=>$itemname,
			'itemtype'=>$type,
			'qty'=>$restyui1->reqty ." ".$unival,
			'recvqty'=>$restyui1->recqty ." ".$unival,
			'recvweight'=>$restyui1->recweight." ".$unival12345,
			'fincode'=>$fincode,
			'specialization'=>$specification,
			'gentry'=>$restyui1->mrngatentry,
			'billno'=>$restyui1->billno,
			'gateentryby'=>$gby,
			'gateentryOn'=>date('Y-m-d H:i:s',strtotime($restyui1->gateentryOn)),
			'qcstatus'=>"<span style='color:".$bac.";font-weight:bold;'>".$qcstatus."</span>",
			'accepted'=>$accept,
			'accepted_on'=>$accon,
			'accepted_by'=>$accname,
			'editmrn'=>$editmrn
			);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);

}	
		
		function mrnitemrequesthistoryollol()
{

$date = date('Y-m-d');
			$date = strtotime($date);
			$date = date('Y-m-d',strtotime("-45 day", $date)).' 00:00:00';
			$scheduler_data = array();
		$rest=$this->db->select('a.*,b.*,a.gateentryno as mrngatentry')->from('mrn a')->join('purchase_order b','a.poid=b.id')->where('a.mrndone','1')->where('a.addedOn>=',$date)->order_by('a.addedOn','DESC')->group_by('a.id')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
$vendorname=$this->getvendorname($restyui1->vendor);
				if($restyui1->potype==0)
				{
					
					$type="Machine Item";
					$itemname=$this->getmachineitemname($restyui1->itemid);
					$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
					if(count($otherdetails)>0)
					{

					$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
					$conunit=$otherdetails['conunit'];
					}else{
					$fincode='';
					$specification='';
					$conunit='';
					}
			
					
				}else if($restyui1->potype==1)
				{
					$type="General Item";
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
					$conunit='';
					
				}
				

						if($restyui1->potype==0)
						{
							
						if($restyui1->source==1)
						{
						

							$job=$this->db->select('job_card_no,item_id as machineid')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
							if($job->num_rows()>0)
							{
							foreach($job->result() as $job1);
							$macid=$job1->machineid;
							}else{
							$macid=0;
							}

						}else 
						{
							$macid=0;
						}

						}else 
						{
							$macid=0;
						}
					

						/** **/
		
							

			$restyu=$this->db->select('shortname')->from('units')->where('id',$restyui1->unit)->get();
			if($restyu->num_rows()>0)
			{
			foreach($restyu->result() as $restyu1);
			$unival=$restyu1->shortname;
			}else{
			$unival='';							
			}


			if($conunit>0)
			{

	$restyuss=$this->db->select('shortname')->from('units')->where('id',$conunit)->get();
			if($restyuss->num_rows()>0)
			{
			foreach($restyuss->result() as $restyu1dd);
			$unival12345=$restyu1dd->shortname;
			}else{
			$unival12345='';							
			}
		}else
		{
			$unival12345='';
		}


			$gby=$this->getusername($restyui1->gateentryBy);

			if($restyui1->qcstatus=='1')
			{
			$qcstatus="Done";
			$bac="green";
			}else
			{
			$qcstatus="Not Done";
			$bac="red";
			}
			
			
			if($restyui1->account_accepted == 1) {
				$accept = 'YES';
				$accname=$this->getusername($restyui1->account_accepted_by);
				$accon=date('d-M-Y',strtotime($restyui1->account_accepted_on));
			} else {
				$accept = 'NO';
				$accname='';
				$accon='';
			}



            $scheduler_data[] = array('sr_no'=>$i,
           	'mrndoc'=>'<a href="'.page_url.'Store/mrnformat/'.$restyui1->mrngatentry.'" target="_blank">Print MRN For Gate No.'.$restyui1->mrngatentry.'</a>',
			'pono'=>$restyui1->pono,
			'vendorname'=>$vendorname,
			'itemname'=>$itemname,
			'itemtype'=>$type,
			'qty'=>$restyui1->reqty ." ".$unival,
			'recvqty'=>$restyui1->recqty ." ".$unival,
			'recvweight'=>$restyui1->recweight." ".$unival12345,
			'fincode'=>$fincode,
			'specialization'=>$specification,
			'gentry'=>$restyui1->mrngatentry,
			'billno'=>$restyui1->billno,
			'gateentryby'=>$gby,
			'gateentryOn'=>date('Y-m-d H:i:s',strtotime($restyui1->gateentryOn)),
			'qcstatus'=>"<span style='color:".$bac.";font-weight:bold;'>".$qcstatus."</span>",
			'accepted'=>$accept,
			'accepted_on'=>$accon,
			'accepted_by'=>$accname
			);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);

}



		
		function mrnitemrequesthistoryOLdddddddddddddddddddddddddddddddd()
{

			$date = date('Y-m-d');
			$date = strtotime($date);
			$date = date('Y-m-d',strtotime("-7 day", $date)).' 00:00:00';
			$scheduler_data = array();
		$rest=$this->db->select('a.*,a.itemid as mrnitemid,b.*,a.gateentryno as mrngatentry')->from('mrn a')->join('purchase_order b','a.poid=b.id')->where('a.mrndone','1')->where('a.addedOn>=',$date)->order_by('a.addedOn','DESC')->group_by('a.id')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
$vendorname=$this->getvendorname($restyui1->vendor);
				if($restyui1->potype==0)
				{
					
					$type="Machine Item";
					$itemname=$this->getmachineitemname($restyui1->mrnitemid);
					$otherdetails=$this->getmachineotherdetails($restyui1->mrnitemid);
					if(count($otherdetails)>0)
					{

					$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
					}else{
					$fincode='';
					$specification='';
					}
			
					
				}else if($restyui1->potype==1)
				{
					$type="General Item";
					$itemname=$this->getgeneralitemname($restyui1->mrnitemid);
					$fincode='';
					$specification='';
					
				}
				

						if($restyui1->potype==0)
						{
							
						if($restyui1->source==1)
						{
						

							$job=$this->db->select('job_card_no,item_id as machineid')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
							if($job->num_rows()>0)
							{
							foreach($job->result() as $job1);
							$macid=$job1->machineid;
							}else{
							$macid=0;
							}

						}else 
						{
							$macid=0;
						}

						}else 
						{
							$macid=0;
						}
					

						/** **/
		
							

			$restyu=$this->db->select('shortname')->from('units')->where('id',$restyui1->unit)->get();
			if($restyu->num_rows()>0)
			{
			foreach($restyu->result() as $restyu1);
			$unival=$restyu1->shortname;
			}else{
			$unival='';							
			}


			$gby=$this->getusername($restyui1->gateentryBy);

			if($restyui1->qcstatus=='1')
			{
			$qcstatus="Done";
			$bac="green";
			}else
			{
			$qcstatus="Not Done";
			$bac="red";
			}

            $scheduler_data[] = array('sr_no'=>$i,
           	'mrndoc'=>'<a href="'.page_url.'Store/mrnformat/'.$restyui1->mrngatentry.'" target="_blank">Print MRN For Gate No.'.$restyui1->mrngatentry.'</a>',
			'pono'=>$restyui1->pono,
			'vendorname'=>$vendorname,
			'itemname'=>$itemname,
			'itemtype'=>$type,
			'qty'=>$restyui1->reqty ." ".$unival,
			'recvqty'=>$restyui1->recqty ." ".$unival,
			'fincode'=>$fincode,
			'specialization'=>$specification,
			'gentry'=>$restyui1->mrngatentry,
			'billno'=>$restyui1->billno,
			'gateentryby'=>$gby,
			'gateentryOn'=>date('Y-m-d H:i:s',strtotime($restyui1->gateentryOn)),
			'qcstatus'=>"<span style='color:".$bac.";font-weight:bold;'>".$qcstatus."</span>");
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);

}


function qchistoryoLDLLLLLLLLL()
{

			$scheduler_data = array();
		$date = date('Y-m-d');
		$date = strtotime($date);
		$date = date('Y-m-d',strtotime("-7 day", $date)).' 00:00:00';
		$genpo=base64_decode($this->uri->segment(3));
$genp=explode(',',$genpo);
		$rest=$this->db->select('a.*,a.gateentryno as gateno,a.id as mrnid,b.*,b.id as poid')->from('mrn a')->join('purchase_order b','a.poid=b.id')->where('b.approved','1')->where('a.qcstatus','1')->where('a.addedOn>=',$date)->group_by('a.id')->order_by('a.addedOn','DESC')->get();
		//->where('a.completed','0')
			if($rest->num_rows()>0)
			{
			$i=1;
		//	echo "<pre>"; print_r($rest->result()); exit;
			foreach($rest->result() as $restyui1)
			{
$vendorname=$this->getvendorname($restyui1->vendor);
				if($restyui1->potype==0)
				{
					
					$type="Machine Item";
					$itemname=$this->getmachineitemname($restyui1->itemid);
					$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
					if(count($otherdetails)>0)
					{

					$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
					}else{
					$fincode='';
					$specification='';
					}
			
					
				}else if($restyui1->potype==1)
				{
					$type="General Item";
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
					
				}
				

						if($restyui1->potype==0)
						{
							
						if($restyui1->source==1)
						{

							$job=$this->db->select('job_card_no,item_id as machineid')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
							if($job->num_rows()>0)
							{
							foreach($job->result() as $job1);
							$macid=$job1->machineid;
							}else{
							$macid=0;
							}

						}else 
						{
							$macid=0;
						}

						}else 
						{
							$macid=0;
						}
					

						/** **/
						//$prevqty=$this->storemodel->checkifanypreviousqtyisinwarded($restyui1->itemid,$restyui1->pono);

						/** END **/
							

						$restyu=$this->db->select('shortname')->from('units')->where('id',$restyui1->unit)->get();
						if($restyu->num_rows()>0)
						{
						foreach($restyu->result() as $restyu1);
						$unival=$restyu1->shortname;
						}else{
						$unival='';							
						}


$originalleftqty=$restyui1->recqty;
$gateentry=$restyui1->gateno;
$billno=$restyui1->billno;

$html='<a href="'.page_url.'Reporting/vendorwisepoforgateentry/'.$restyui1->vendor.'" class="btn btn-warning btn-xs">Open PO(s)</a>';
$gateentry=$gateentry;

$recvqty11=floatval($originalleftqty)." ".$unival;
$recvqty=$recvqty11;

$datas=$this->storemodel->getmrnhistorydata($restyui1->mrnid);
if(count($datas)>0)
{

$approved=$datas['accept'];
$reject=$datas['reject'];;
$rejectremarks=$datas['remarks'];
$rejectfile=$datas['image'];
    
    
}else
{
$approved='Data not available';
$reject='Data not available';
$rejectremarks='Data not available';
$rejectfile='Data not available';
}


			$scheduler_data[] = array('sr_no'=>$i,
			'mrnon'=>date('d-m-Y H:i:s',strtotime($restyui1->mrndoneOn)),
			'pono'=>$restyui1->pono,
			'vendorname'=>$vendorname,
			'itemname'=>$itemname,
			'itemtype'=>$type,
			'fincode'=>$fincode,
			'specialization'=>$specification,
		    //'previousinwarded'=>$prevqty.' '.$unival,
			//'reqty'=>$originalleftqty.' '.$unival,
			'recqty'=>$recvqty,
			'gateentry'=>$gateentry,
			'billno'=>$billno,
			'approved'=>$approved,
			'reject'=>$reject,
			'rejremarks'=>$rejectremarks,
			'rejectimage'=>$rejectfile);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);

}


function qchistory()
{

$date = date('Y-m-d');
$date = strtotime($date);
$date = date('Y-m-d',strtotime("-7 day", $date)).' 00:00:00';
$scheduler_data = array();
$genpo=base64_decode($this->uri->segment(3));
$genp=explode(',',$genpo);
		$rest=$this->db->select('a.*,a.gateentryno as gateno,a.id as mrnid,b.*,b.id as poid')->from('mrn_view a')->join('purchase_order_view b','a.poid=b.id')->where('b.approved','1')->where('a.qcstatus','1')->where('a.addedOn>=',$date)->group_by('a.id')->order_by('a.addedOn','DESC')->get();
		//->where('a.completed','0')
			if($rest->num_rows()>0)
			{
			$i=1;
		//	echo "<pre>"; print_r($rest->result()); exit;
			foreach($rest->result() as $restyui1)
			{
			$vendorname=$this->getvendorname($restyui1->vendor);
				if($restyui1->potype==0)
				{
					
					$type="Machine Item";
					$itemname=$this->getmachineitemname($restyui1->itemid);
					$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
					if(count($otherdetails)>0)
					{

					$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
					}else{
					$fincode='';
					$specification='';
					}
			
					
				}else if($restyui1->potype==1)
				{
					$type="General Item";
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
					
				}
				

						if($restyui1->potype==0)
						{
							
						if($restyui1->source==1)
						{

							$job=$this->db->select('job_card_no,item_id as machineid')->from('order_instruments_view')->where('id',$restyui1->jobcard)->get();
							if($job->num_rows()>0)
							{
							foreach($job->result() as $job1);
							$macid=$job1->machineid;
							}else{
							$macid=0;
							}

						}else 
						{
							$macid=0;
						}

						}else 
						{
							$macid=0;
						}
					

						/** **/
						//$prevqty=$this->storemodel->checkifanypreviousqtyisinwarded($restyui1->itemid,$restyui1->pono);

						/** END **/
							

						$restyu=$this->db->select('shortname')->from('units_view')->where('id',$restyui1->unit)->get();
						if($restyu->num_rows()>0)
						{
						foreach($restyu->result() as $restyu1);
						$unival=$restyu1->shortname;
						}else{
						$unival='';							
						}


$originalleftqty=$restyui1->recqty;
$gateentry=$restyui1->gateno;
$billno=$restyui1->billno;

$html='<a href="'.page_url.'Reporting/vendorwisepoforgateentry/'.$restyui1->vendor.'" class="btn btn-warning btn-xs">Open PO(s)</a>';
$gateentry=$gateentry;

$recvqty11=floatval($originalleftqty)." ".$unival;
$recvqty=$recvqty11;

$datas=$this->storemodel->getmrnhistorydata($restyui1->mrnid);
if(count($datas)>0)
{

$approved=$datas['accept'];
$reject=$datas['reject'];;
$rejectremarks=$datas['remarks'];  
$rejectfile=$datas['image'];
    
    
}else
{
$approved='Data not available';
$reject='Data not available';
$rejectremarks='Data not available';
$rejectfile='Data not available';
}


			$scheduler_data[] = array('sr_no'=>$i,
			'mrnon'=>date('d-m-Y H:i:s',strtotime($restyui1->mrndoneOn)),
			'pono'=>$restyui1->pono,
			'vendorname'=>$vendorname,
			'itemname'=>$itemname,
			'itemtype'=>$type,
			'fincode'=>$fincode,
			'specialization'=>$specification,
		    //'previousinwarded'=>$prevqty.' '.$unival,
			//'reqty'=>$originalleftqty.' '.$unival,
			'recqty'=>$recvqty,
			'gateentry'=>$gateentry,
			'billno'=>$billno,
			'approved'=>$approved,
			'reject'=>$reject,
			'rejremarks'=>$rejectremarks,
			'rejectimage'=>$rejectfile);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);

}



function getjobcardid($record)
{

$resteye=$this->db->select('jobcardno')->from('mrn')->where('id',$record)->get();
if($resteye->num_rows()>0)
{

foreach($resteye->result() as $resteye1);

return $resteye1->jobcardno;

}else
{

return 0;

}

}

function getpodetails($pono)
{
$ponoo=array();
$resteyu=$this->db->select('source,sourceid,unit,potype,prno')->from('purchase_order')->where('pono',$pono)->get();
if($resteyu->num_rows()>0)
{
foreach($resteyu->result() as $resteyu1);

$ponoo['source']=$resteyu1->source;
$ponoo['sourceid']=$resteyu1->sourceid;
$ponoo['unit']=$resteyu1->unit;
$ponoo['potype']=$resteyu1->potype;
$ponoo['prno']=$resteyu1->prno;

}


return $ponoo;
}


public function servicedispatchhistory()
{
$this->load->view('FMS/dispatchfortommorowforservice');
}
 public function dispatchfortommorow_order_list_service_history()
	{
	$scheduler_data = array();
	$type='1';

		$userid = $_SESSION['logged_in']['user_id'];
		$this->db->select('a.docketno,a.movedOn,a.order_id, a.order_type, a.marketing_person,a.po_number, a.company_name,a.closedOn, a.address, a.email, a.mobile_number, a.internal_order_no, a.discount, a.order_value_after_discount, a.advance_amount, a.payment_terms, a.installation_charges, a.order_status, a.added_on,a.packing_type, a.installation_type, b.user_id, b.title, b.first_name, b.last_name, a.installation_amount, a.packing_charges, a.packing_amount, a.freight_type, a.freight_amount,a.remarks')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left')->where('order_type','SERVICE');
		
		$query = $this->db->where('a.order_status','1')->where('a.movetodispatch','1')->order_by('a.order_id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
		$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1');
		
		if($type<>'')
			{
			    if($type=='0')
			    {
			       $this->db->where('finalpacked','0'); 
			    }else
			    {
			         $this->db->where('finalpacked','1');
			    }
			    
			}
			
			$completedjobcard=$this->db->get();
			$completedjobcards=$completedjobcard->num_rows();
			
			$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name,a.finalpacked,a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			$alljobcard=$query1->num_rows();
			if($alljobcard>0)
			{
				foreach($query1->result() as $alluee);
					$finalpack=$alluee->finalpacked;
					
			}else{ $finalpack=0; }
			
		if($alljobcard<>0)
		{
		if($completedjobcards==$alljobcard)
		{
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			
			$html = "<table border='1' style='width:500px;'><tr style='background-color:white;'><th style='padding:2px 2px 2px 2px'>INSTRUMENT</th><th style='padding:2px 2px 2px 2px'>JOB CARD NO.</th></tr>";
			$instrumentsss = array();
			
			
			
			foreach($query1->result() as $instruments){
				if($instruments->complete=='1'){
					$backgroundcolor = "background-color:#10C469; color:white !important; font-weight:bold;";
				}else{
					$backgroundcolor="";
				}
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->instruments_name)."</td>";
				//$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->qty)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->job_card_no)."</td>";
				$html.="</tr>";
			}
			$html.="</table>";
			$ins_charges = "";
			/** Get order planned **/
			$restt=$this->db->select('count(id) as totaljobcard')->from('order_instruments')->where('order_id',$row->order_id)->get();
			foreach($restt->result() as $instcount);
			$totjobcard= $instcount->totaljobcard;
			
			
	
			
			if($row->installation_charges=='1'){
				$charges= "<strong>INSTALLATION TYPE </strong>".$row->installation_type."<br> <strong>INSTALLATION CHARGES</strong> - REQUIRED";
				
				$ins_charges= $charges."<br> AMOUNT - ".$row->installation_amount;
			}else{
					$ins_charges = "NOT REQUIRED";
			}
			
			if($row->packing_charges=='1'){
				$packcharges = "PAID BY PARTY";
				$packingcharges= $packcharges."<br> Amount - <strong>".$row->packing_amount."</strong>";
			}else{
					$packingcharges = "INCLUSIVE";
			}
			
			if($row->freight_type=='1'){
				$freigntcharges = "TO PAY BASIS";
				
			}else if($row->freight_type=='2'){
				$freigntcharges = "PAID BY PRESTO";
			}else if($row->freight_type=='3'){
				$frtcharges = "BILLED IN INVOICE";
				$freigntcharges= $frtcharges."<br> Amount - <strong>".$row->freight_amount."</strong>";
			}else{
				$freigntcharges = "OWN PICK-UP";
					
			}
			
			$closedon=date('d-M-Y g:i A',strtotime($row->movedOn));
			$plannedtime = date('d-M-Y g:i A', strtotime($closedon . ' +1 day'));

if($row->installation_charges==1)
			{
				$ser=1;
			}else{
				
				$ser=0;
			}
			
			
$printpackinglabel='<a href="'.page_url.'Reporting/generatepackingslip/'.$row->order_id.'" target="_blank"><span class="btn btn-success btn-sm">Print Packing Label</span></a>';
			
			if($finalpack=='1')
{
			$restyupacku=$this->db->select('addedOn')->from('order_finalpacking_details')->where('orderid',$row->order_id)->get();	
				if($restyupacku->num_rows()>0)
{
				foreach($restyupacku->result() as $restyupacku2);
				$actualtime=date('d-M-Y g:i A',strtotime($restyupacku2->addedOn));
				
}else{  

	$actualtime="";
					}
			$completeorder="<span class='btn btn-success btn-sm'>Dispatch Done</span>";		
	
}else{  $actualtime=""; 
	 
	  	$completeorder="<span class='btn btn-warning btn-sm'>Dispatch Pending</span>";
	 // $completeorder='<a href="javascript:;" onclick="markstagedone('."'".$row->order_id."'".','."'".strtoupper($row->company_name)."'".','."'".$row->internal_order_no."'".','."'".$ser."'".')"><span class="btn btn-warning btn-sm">Mark as Done</span></a>';

}
	
	
	if($row->docketno<>'')
{
    $dock="<a href='".page_url."image_bank/docketno/".$row->docketno."' download>Docket Image</a>";
}else
{ 
     $dock='';
}

		
			$scheduler_data[] = array('sr_no'=>$i,
			'closeorder'=>$completeorder,
			'docket'=>$dock,
			'packinglabel'=>$printpackinglabel,
			'added_on'=>$closedon,
			 'plannedtime'=>$plannedtime,
			 'actualtime'=>$actualtime,
			'order_type'=>strtoupper($row->order_type),
			'marketing_person'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'po_number'=>strtoupper($row->po_number),
			'company_name'=>strtoupper($row->company_name),
			'address'=>strtoupper($row->address),
			'email'=>strtoupper($row->email),
			'mobile_number'=>strtoupper($row->mobile_number),
			'internal_order_no'=>strtoupper($row->internal_order_no),
			'itemname'=>$html,
			'discount'=>strtoupper($row->discount)."%",
			'order_value_after_discount'=>strtoupper($row->order_value_after_discount),
			'advance_amount'=>strtoupper($row->advance_amount),
			'payment_terms'=>strtoupper($row->payment_terms),
			'installation_charges'=>strtoupper($ins_charges),
			'packingcharges'=>"<strong>Packing Type</strong> -".$row->packing_type."<br>".$packingcharges,
			'freigntcharges'=>$freigntcharges,
			'remarks'=>$row->remarks);
			$i++;
		}
		}
	}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}


function prduetomaterialdiversion()
{
   $this->load->view('store/prduetomaterialdiversion') ;
}


function prrquestfor()
{
	
	$scheduler_data=array();	
	
	$Restey=$this->db->select('a.*,b.part,b.specification,b.size_in_mm,b.material,b.fincode,a.jobcardid')->from('prduetomaterialdiversion a')->join('machine_parts_with_picture b','a.itemid=b.id')->where('a.sendtopurchase','0')->where('a.closed','0')->order_by('a.id','DESC')->get();
	if($Restey->num_rows()>0)
	{	
		$a=1;
		foreach($Restey->result() as $Restey1) 
		{
		$jobcard=$this->getjobcardno($Restey1->jobcardid);
			
			$html="<a href='javascript:;' onclick='convertintopr(".$Restey1->id.");'><span class='btn btn-warning'>Send to Purchase</span></a>";
			
			
			$html1="<a href='javascript:;' onclick='closethispr(".$Restey1->id.");'><span class='btn btn-danger'>Close</span></a>";
			

        	$scheduler_data[] = array(
				'sr_no'=>$a,
				'jobcard'=>$jobcard,
				'item'=>$Restey1->part,
				'specs'=>$Restey1->specification.'/'.$Restey1->size_in_mm,
				'fincode'=>$Restey1->fincode,
				'qty'=>$Restey1->qty,
				'generatedOn'=>date('d-M-Y H:i:s',strtotime($Restey1->generatedOn)),
				'sendtopurchase'=>$html,
				'close'=>$html1
				);
	     		
		$a++;
		}
	
	}
	$results = array(
    "sEcho" => 1,
    "iTotalRecords" => count($scheduler_data),
    "iTotalDisplayRecords" => count($scheduler_data),
    "aaData"=>$scheduler_data);
    echo json_encode($results);	
    
	
}



function storeunaccpteditemshistory()
{
	
	$date = date('Y-m-d');
	$date = strtotime($date);
	$date = date('Y-m-d',strtotime("-90 day", $date)).' 00:00:00';
				
	$scheduler_data=array();	
	
	$Restey=$this->db->select('a.*')->from('issuestocktousers_view a')->where('a.storeaccept','1')->where('a.issuedOn>=',$date)->ORDER_BY('issuedOn','DESC')->get();
	if($Restey->num_rows()>0)
	{	
		$a=1;
		foreach($Restey->result() as $Restey1)
		{
			
				$jobcard=$this->getjobcardno($Restey1->jobcardid);
				$iss='<a href="'.page_url.'Store/issueslip/'.$Restey1->usertype.'/'.$Restey1->issuedto.'/'.$Restey1->jobcardid.'/'.$Restey1->issuesession.'" target="_blank">Issue Slip</a>';
			
				$machinedetails=$this->getmachineotherdetails($Restey1->itemid);
				if(count($machinedetails)>0)
				{
				$fincode=$machinedetails['fincode'];
				$specification=$machinedetails['specialization'];
				$part=$machinedetails['name'];
				$rack_location=$machinedetails['rack_location'];
				$unit=$machinedetails['unit'];
				}else{
				$fincode='';
				$specification='';
				$part='';
				$rack_location='';
				$unit='';
				}
				
				if($unit<>'')
				{
				$unitname=$this->getunit($unit);
				}else
				{
				$unitname='';
				}
				
				if($Restey1->usertype==1)
				{
					$issueduser=$this->storemodel->getudata($Restey1->issuedto);
					
				}else if($Restey1->usertype==2)
				{
					$issueduser=$this->storemodel->getnoncrmusername($Restey1->issuedto);
					
				}else
				{
					$issueduser='';
				}


if($jobcard<>'')
{
   
    $instruments_name=$this->getjobcardinstrument($Restey1->jobcardid);
   $sshs='<span id="c'.$Restey1->id.'"><input type="button" name="checkthis" value="Accept" id="accept'.$Restey1->id.'" onclick="acceptitems('.$Restey1->id.')"></span><span id="ce'.$Restey1->id.'" style="color:red; display:none">Accepted</span>';
}else
{
	 $instruments_name='';
     $sshs='<span id="c'.$Restey1->id.'"><input type="button" name="checkthis" value="Accept" id="accept'.$Restey1->id.'" onclick="acceptnonjobcarditems('.$Restey1->id.')"></span><span id="ce'.$Restey1->id.'" style="color:red; display:none">Accepted</span>';
    
}

$mid = $this->getjobcardinstrumentid($Restey1->jobcardid);
if($jobcard!==''){
$issueslip = "<a href='".page_url."FMS/jobcard_wise_issue_slip/".$Restey1->jobcardid."/".$mid."'><span class='btn btn-warning btn-xs'>Issue Slip</span></a>";
}else{
	$issueslip = "";
}

$forchallan="<input type='checkbox' class='forchallan' name='fetchforchallan[]' value='".$Restey1->id."''>";

				$scheduler_data[] = array(
				'sr_no'=>$a,
				'issueslip'=>$iss."<br/>".$Restey1->id,
				'challanselect'=>$forchallan,
				'jobcard'=>$jobcard,
				'instruments_name'=>$instruments_name,
				'itemname'=>$part,
				'fincode'=>$fincode,
				'specialization'=>$specification,
				'racklocation'=>$rack_location,
				'qty'=>$Restey1->stock." ".$unitname,
				'issuedto'=>$issueduser,
				'jobcardissueslip'=>$issueslip,
				'issuedOn'=>date('d-M-Y H:i:s',strtotime($Restey1->issuedOn)),
				'acceptOn'=>date('d-M-Y H:i:s',strtotime($Restey1->acceptedOn)),
				'issuedreason'=>$Restey1->reason
				);
	     		
		$a++;
		}
	
	}
	$results = array(
    "sEcho" => 1,
    "iTotalRecords" => count($scheduler_data),
    "iTotalDisplayRecords" => count($scheduler_data),
    "aaData"=>$scheduler_data);
    echo json_encode($results);	
    
	
}

function getsourceid($pr)
{
$Restweyue=$this->db->select('sourceid')->from('purchase_request')->where('prno',$pr)->get();
if($Restweyue->num_rows()>0)
{
foreach($Restweyue->result() as $Restweyue1);

return $Restweyue1->sourceid;
}else
{
return '';

}


}


public function exportconsolidatedpoinvoice() {


		$excel_row = 2;
		$this->load->library("excel");
		 $object = new PHPExcel();
		 $object->createSheet(0);
		$object->setActiveSheetIndex(0);

		$table_columns = array("Sno.","Vendor Name","PO No.","Item Name", "Specs/Size", "Fincode", "Qty");


			$styleArray = array(
      /*'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
          )
      ), */
	  'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        )
  );
$object->getDefaultStyle()->applyFromArray($styleArray);

		$object->getDefaultStyle()->getAlignment()->setWrapText(true);
		$object->getActiveSheet()->getColumnDimension('A')->setWidth(10);
		$object->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$object->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$object->getActiveSheet()->getColumnDimension('D')->setWidth(30);
		$object->getActiveSheet()->getColumnDimension('E')->setWidth(20);
		$object->getActiveSheet()->getColumnDimension('F')->setWidth(20);
		$object->getActiveSheet()->getColumnDimension('G')->setWidth(20);
  		$object->getActiveSheet()->setTitle('CONSOLIDATED ITEMS'.'-'.date('d-M-Y'));

  		$column = 0;
  		 foreach($table_columns as $field) {
		   $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
		   $column++;
		  }

		  $query = $this->db->select('a.id, a.pono, a.qty, b.name, c.part, c.specification, c.size_in_mm, c.fincode, d.shortname')
		      				 ->from('purchase_order a')
		      				 ->join('vendors b' , 'b.id=a.vendor')
		      				 ->join('machine_parts_with_picture c' , 'c.id=a.itemid')
		      				 ->join('units d' , 'd.id=a.unit')
		      				 ->where('a.completed','0')
		      				 ->where('a.approved','1')
		      				 ->order_by('b.name')
		      			
		      				 ->get();

      if ($query->num_rows() > 0) {
      	$i = 1;
		foreach ($query->result() as $row) {
			$pono = $row->pono;
			$vendor = $row->name;
			$item = $row->part;
			$specification = $row->specification;
			$size = $row->size_in_mm;
			$fincode = $row->fincode;
			$qty = $row->qty;
			$unit = $row->shortname;
			$object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i);
		$object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $vendor);
		$object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $pono);
		$object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $item);
		$object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $specification."/".$size);
		$object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $fincode);
		$object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $qty." ".$unit);
		$excel_row++;
		$i++;
			}
		} else {
			$pono = "NA";
			$vendor = "NA";
			$item = "NA";
			$specification = "NA";
			$size = "NA";
			$fincode = "NA";
			$qty = "NA";
			$unit = "NA";
		}

		

$title="CONSOLIDATED_ITEMS_YET_TO_BE_RECEIVED";
$filename=$title.'-'.date('d-M-Y').'.xls';
$savepath=$_SERVER['DOCUMENT_ROOT'].'/consolidatedpoinvoice/'.$filename;
$object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
$object_writer->save($savepath); 
header("Content-type:application/vnd.ms-excel");
header('Content-Disposition: attachment; filename=' . $filename);
readfile( $savepath );

 
 }



function mrnitemrequesthistoryforaccountsaccepted()
{

		$conunit=0;
		$conweight='';
			$scheduler_data = array();
		$rest=$this->db->select('a.*,b.*,a.gateentryno as mrngatentry, a.id as mrn_id')->from('mrn a')->join('purchase_order b','a.poid=b.id')->where('a.mrndone','1')->where('a.account_accepted',1)->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
$vendorname=$this->getvendorname($restyui1->vendor);
				if($restyui1->potype==0)
				{
					
					$type="Machine Item";
					$itemname=$this->getmachineitemname($restyui1->itemid);
					$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
					if(count($otherdetails)>0)
					{

					$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
					$conunit=$otherdetails['conunit'];
					$conweight=$otherdetails['conweight'];
					}else{
					$fincode='';
					$specification='';
					}
			
					
				}else if($restyui1->potype==1)
				{
					$type="General Item";
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
					
				}
				

						if($restyui1->potype==0)
						{
							
						if($restyui1->source==1)
						{
						

							$job=$this->db->select('job_card_no,item_id as machineid')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
							if($job->num_rows()>0)
							{
							foreach($job->result() as $job1);
							$macid=$job1->machineid;
							}else{
							$macid=0;
							}

						}else 
						{
							$macid=0;
						}

						}else 
						{
							$macid=0;
						}
					

						/** **/
		
							

			$restyu=$this->db->select('shortname')->from('units')->where('id',$restyui1->unit)->get();
			if($restyu->num_rows()>0)
			{
			foreach($restyu->result() as $restyu1);
			$unival=$restyu1->shortname;
			}else{
			$unival='';							
			}


			$gby=$this->getusername($restyui1->gateentryBy);

			if($restyui1->qcstatus=='1')
			{
			$qcstatus="Done";
			$bac="green";
			}else
			{
			$qcstatus="Not Done";
			$bac="red";
			}

			$accept = "<input type='checkbox' name='accept[]' id='accept' value='".$restyui1->mrn_id."'>";


				if($conunit>0)
			{
			$unival1=$this->storemodel->getunit($conunit);
			$finalreq="Converted Qty ".$conweight*$restyui1->reqty." ".$unival1;
			$finalrecv="Converted Qty ".$conweight*$restyui1->recqty." ".$unival1;
			}else
			{
			$finalreq='';
			$finalrecv='';
			}

            $scheduler_data[] = array('sr_no'=>$i,
            'accept'=>$accept,
           	'mrndoc'=>'<a href="'.page_url.'Store/mrnformat/'.$restyui1->mrngatentry.'/1" target="_blank">Print MRN For Gate No.'.$restyui1->mrngatentry.'</a>',
			'pono'=>$restyui1->pono,
			'vendorname'=>$vendorname,
			'itemname'=>$itemname,
			'itemtype'=>$type,
			'qty'=>$restyui1->reqty ." ".$unival."<br/>".$finalreq,
			'recvqty'=>$restyui1->recqty ." ".$unival."<br/>".$finalrecv,
			'fincode'=>$fincode,
			'specialization'=>$specification,
			'gentry'=>$restyui1->mrngatentry,
			'billno'=>$restyui1->billno,
			'gateentryby'=>$gby,
			'gateentryOn'=>date('Y-m-d H:i:s',strtotime($restyui1->gateentryOn)),
			'qcstatus'=>"<span style='color:".$bac.";font-weight:bold;'>".$qcstatus."</span>");
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);

}


function update_flag()
{

	$accept = $this->input->post('accept');

	for ($i=0; $i < count($accept); $i++) { 
	$data = array(
			'account_accepted' => 1,
			'account_accepted_on' => date('Y-m-d H:i:s'),
			'account_accepted_by' => $_SESSION['logged_in']['user_id']
			);
	$result = $this->db->where('id',$accept[$i])
			 		   ->update('mrn',$data);
	}

	if ($result > 0) {
		redirect(page_url.'Reporting/accountsmrnreport');
	}
}




function pendingpolistforstore()
{

$scheduler_data = array();
    $itemid=$this->uri->segment(3);
  
		$rest=$this->db->select('a.id,a.qty,a.unit,a.prno,a.approvedOn,a.jobcard,a.prno,a.source,a.sourceid,a.potype,a.addedOn,a.pono,e.first_name,e.last_name,a.itemid,a.approved')->from('purchase_order a')->join('system_users e','e.user_id=a.addedBy')->where('a.completed','0')->where('a.itemid',$itemid)->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				
				$getunit=$this->storemodel->getunit($restyui1->unit);
				
					if($restyui1->source==1)
					{

					$job=$this->db->select('job_card_no')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
					if($job->num_rows()>0)
					{
					foreach($job->result() as $job1);
					$jobcardno=$job1->job_card_no;
					}else{
					$jobcardno='';
					}

					$sourceadd="Jobcard- ".$jobcardno;
					}else if($restyui1->source=='2')
					{
					$inds=$this->getindentno($restyui1->prno);
					$sourceadd="Indent- ".$inds;
					}else
					{
					$sourceadd="IMS";
					}
						
						
				  $itemname=$this->storemodel->getmachineitemname($restyui1->itemid);
					$otherdetails=$this->storemodel->getmachineotherdetails($restyui1->itemid);
					if(count($otherdetails)>0)
					{
					$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
					$size=$otherdetails['size'];
					$material=$otherdetails['material'];
					}else{
					$fincode='';
					$specification='';
					$size='';
					$material='';
					}
			
			
			$currstatus=$this->getpostatus($restyui1->id);
			

			$scheduler_data[] = array('sr_no'=>$i,
			'pono'=>"<a href='".page_url."Store/po/".$restyui1->pono."' target='_blank'>".$restyui1->pono."</a>",
			'source'=>$sourceadd,
			'item'=>$itemname,
			'specs'=>$specification,
			'size'=>$size,
			'material'=>$material,
			'fincode'=>$fincode,
			'qty'=>floatval($restyui1->qty)." ".$getunit,
			'createdon'=>date('d-M-Y H:i:s',strtotime($restyui1->addedOn)),
			'approvedon'=>date('d-M-Y H:i:s',strtotime($restyui1->approvedOn)),
			'currentstatus'=>$currstatus);
			$i++;
			}
			
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
}


function getindentno($prno)
		{
		$reste=$this->db->select('sourceid')->from('purchase_request')->where('prno',$prno)->get();
		if($reste->num_rows()>0)
		{
		foreach($reste->result() as $reste1);
		return $reste1->sourceid;
		}else
		{
		return '';
		}
		
		}


function getpostatus($poid)
{
    $cur='';
    $rste=$this->db->select('id,completed')->from('purchase_order')->where('approved','1')->where('id',$poid)->get();
    
    if($rste->num_rows()>0)
    {
        foreach($rste->result() as $rste1);
        if($rste1->completed==0)
        {
            $cur="MRN Pending";
            
        }else
        {
            $cur="MRN Completed";
        }
        
        
    
    
    }else
    {
        $cur="Pending for Approval";
    }
    
    
    return $cur; exit;
    
}


public function po_vs_delivery_report() {
		$this->load->view('store/po_vs_delivery');
	}




	function po_vs_delivery_list()
{

			$scheduler_data = array();
			$vendorid=$this->uri->segment(3);

		$this->db->select('a.*')->from('purchase_order a')->where('a.approved','1')->where('a.gateentrycomplete','0')->where('a.completed','0');
		if($vendorid<>'')
		{
			$this->db->where('a.vendor',$vendorid);
		}
		$rest=$this->db->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
$vendorname=$this->getvendorname($restyui1->vendor);
				if($restyui1->potype==0)
				{
					
					$type="Machine Item";
					$itemname=$this->getmachineitemname($restyui1->itemid);
					$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
					if(count($otherdetails)>0)
					{

					$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
					}else{
					$fincode='';
					$specification='';
					}
			
					
				}else if($restyui1->potype==1)
				{
					$type="General Item";
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
					
				}
				

						if($restyui1->potype==0)
						{
							
						if($restyui1->source==1)
						{
						

							$job=$this->db->select('job_card_no,item_id as machineid')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
							if($job->num_rows()>0)
							{
							foreach($job->result() as $job1);
							$macid=$job1->machineid;
							}else{
							$macid=0;
							}

						}else 
						{
							$macid=0;
						}

						}else 
						{
							$macid=0;
						}
					

						/** **/
						$prevqty=$this->storemodel->checkifanypreviousqtyisinwardedinmrn($restyui1->itemid,$restyui1->pono,$restyui1->id);

                        if($prevqty>0)
                        {
                           $requiretobeinwarded=$restyui1->qty-$prevqty;
                        }else
                        {
                             $requiretobeinwarded=$restyui1->qty;
                        }
						/** END **/
							

						$restyu=$this->db->select('shortname')->from('units')->where('id',$restyui1->unit)->get();
						if($restyu->num_rows()>0)
						{
						foreach($restyu->result() as $restyu1);
						$unival=$restyu1->shortname;
						}else{
						$unival='';							
						}

						/** CHECK FOR ANY FOLLOWUP **/
						$podelidate=$this->storemodel->expcteddateofdelivery($restyui1->id,$restyui1->prno);
						/** END **/


            $scheduler_data[] = array('sr_no'=>$i,
			'pono'=>$restyui1->pono,
			'vendorname'=>$vendorname,
			'itemname'=>$itemname,
			'itemtype'=>$type,
			'qty'=>$requiretobeinwarded ." ".$unival,
			'prevqty'=>$prevqty ." ".$unival,
			'fincode'=>$fincode,
			'specialization'=>$specification,
			'deliverydate'=>$podelidate
		);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);

}


public function indent_vs_pr() {
		$this->load->view('store/indent_vs_pr');
	}

	public function indent_vs_pr_list()
	{
		$i=1;
		$department_data = array();
		$query = $this->db->select('a.id,a.prno,a.masterid,a.prraisereason,a.type,a.sourceid,a.source,a.jobcardid,d.part as machine_part,d.fincode,d.size_in_mm,d.specification,d.current_stock,e.first_name,e.last_name,a.itemid,a.qty,e.department_id,u.shortname')
					  ->from('purchase_request a')
					  ->join('machine_parts_with_picture d','d.id=a.masterid')
					  ->join('system_users e','e.user_id=a.addedBy')
					  ->join('units u','u.id=a.unit')
					  ->where('a.approvalstatus','0')
					  ->where('a.closed','0')
					  ->where('a.source', '2')
					  ->order_by('a.id','DESC')
					  ->get();

		foreach($query->result() as $row) {
						$jobcard='';

							if($row->source == 1) {
							$job = $this->db->select('job_card_no,item_id as machineid')			->from('order_instruments')
										    ->where('id',$row->jobcardid)
										    ->get();

							if($job->num_rows() > 0) {

							foreach($job->result() as $job1);
							$jobcard='Jobcard-'.$job1->job_card_no;
							$macid=$job1->machineid;
							} else {
							$jobcard='';
							$macid=0;
							}
							} else if ($row->source == 2) {
							$jobcard='INDENT- IND'.$row->sourceid;
							$macid=0;
							} else if ($row->source == 3) {
							$jobcard='AUTO PR';
							$macid=0;
							}
		
				
			$department_data[] = array('sr_no'=>$i,
									   'pr_no'=>$row->prno,
									   'source' => $jobcard,
									   'indenter_ref'=>ucwords($row->first_name)." ".ucwords($row->last_name),
									   'item' => strtoupper($row->machine_part),
									   'fincode' => $row->fincode,
									   'specification' => $row->specification,
										'qty'=>floatval($row->qty)." ".$row->shortname
										);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($department_data),
			"iTotalDisplayRecords" => count($department_data),
			"aaData"=>$department_data);
			
		echo json_encode($results);
	}
	
	
		function pohistory()
	{
	   	$this->load->view('store/pohistory');
		
	}

	public function editpohistory() {
		$editID = $this->uri->segment(3);
		$data['pohistory'] = $this->storemodel->getEditPoHistory($editID);
		$this->load->view('store/editpohistory', $data);
	}

	public function getPriceDetails() {
		$vendorid = $this->input->post('vendor');
		$result = $this->storemodel->getPriceDetails($vendorid);

			foreach ($result as $row);
				echo $row->listprice.'|'.$row->discount;
	}

	public function update_po_history() {
		$rowid = $this->uri->segment(3);

		 $data = array(
		 		'itemid' => $this->input->post('item_id'),
		 		'vendor' => $this->input->post('vendor'),
		 		'price' => $this->input->post('listprice'),
		 		'qty' => $this->input->post('quantity'),
		 		'discount' => $this->input->post('discount'),
		 		'approved' => 0,
		 		'approvedOn' => '0000-00-00 00:00:00',
		 		'approvedBy' => 0
		 		);

		 $result = $this->storemodel->update_po_history($data, $rowid);

		 	if ($result > 0) {
		 		$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000;">PO Updated Successfully.</span></div>');
		 		redirect(page_url.'Reporting/pohistory');
		 	} else {
		 		$this->session->set_flashdata('message', '<div class="alert alert-danger"><span style="color:#000;">Sorry! PO could not be updated. Please try again later.</span></div>');
		 		redirect(page_url.'Reporting/pohistory');
		 	}
	}
	
	
	
	
	function polisthistoryforrejected()
{

$scheduler_data = array();
    $genpo=base64_decode($this->uri->segment(3));
    $genp=explode(',',$genpo);
		$rest=$this->db->select('a.id, a.approvedOn,a.jobcard,a.prno,a.source,a.sourceid,a.potype,a.addedOn,a.pono,e.first_name,e.last_name,a.itemid,e.department_id, v.name, v.address,a.approved')->from('purchase_order a')->join('system_users e','e.user_id=a.addedBy','left')->join('vendors v','a.vendor=v.id','left')->where('a.approved',2)->order_by('a.approvedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				if(in_array($restyui1->pono,$genp))
				{
					$bc="red";
				}else{
					$bc='';
				}
				
					$jobcard='';

					if($restyui1->source==1)
					{
					$job=$this->db->select('job_card_no')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
					if($job->num_rows()>0)
					{
					foreach($job->result() as $job1);
					$jobcard='Jobcard-'.$job1->job_card_no;
					}else{
					$jobcard='';
					}
					$polink="po";
					$mrnlink="mrn";
					}else if($restyui1->source==2)
					{
					/** Intend **/
					$jobcard='INDENT- IND'.$restyui1->sourceid;
					$polink="generalpo";
					$mrnlink="generalmrn";
					}else if($restyui1->source==3)
					{
					/** Intend **/
					$jobcard='AUTO PR';
					$polink="po";
					$mrnlink="mrn";
					}					


				
				if($restyui1->potype=='0')
				{
					$polink="po";
					$mrnlink="mrn";
				}else
				{
					$polink="generalpo";
					$mrnlink="generalmrn";
				}


if($restyui1->approved=='1')
{
    $app="Approved";
    $rmk='';
}else
{
    $app="Rejected";
    
    $resteyrttt=$this->db->select('remarks')->from('porejectremarks')->where('pono',$restyui1->pono)->get();
    if($resteyrttt->num_rows()>0)
    {
        foreach($resteyrttt->result() as $resteyrttt1);
    $rmk=$resteyrttt1->remarks;
    }else
    {
        $rmk='';
    }
}

$htm="";

if($restyui1->potype=='0'){
    
					$indenttype = "Machine Related Items";
				$rest123=$this->db->select('b.id,a.price,a.itemid, b.part as item_name,a.qty,a.unit,c.shortname')->from('purchase_order a')->join('machine_parts_with_picture b','a.itemid=b.id')->join('units c','a.unit=c.id','left')->where('a.pono',$restyui1->pono)->where('b.id',$restyui1->itemid)->get();
				}else{
					$indenttype = "General Items";
			$rest123=$this->db->select('b.id,a.price,a.itemid, b.item_name,a.qty,a.unit, c.shortname')->from('purchase_order a')->join('house_keeping_items b','a.itemid=b.id','left')->join('units c','a.unit=c.id','left')->where('a.pono',$restyui1->pono)->where('b.id',$restyui1->itemid)->get();		
				}
				
				
				if($rest123->num_rows()>0)
				{
					
					foreach($rest123->result() as $rest1231)
					$price = $rest1231->price;
					$oldprice="";
					$query = $this->db->select('price')->from('purchase_order')->where('itemid',$rest1231->itemid)->where('approved','1')->limit('1')->order_by('id','desc')->get();
					if($query->num_rows()>0){
						foreach($query->result() as $oldata);
						$oldprice = $oldata->price;
						$diff = abs($price-$oldprice);
					}else{
					    $oldprice="New Item";
					    $diff="0";
					}
					$itemname = strtoupper($rest1231->item_name);
					$qty = $rest1231->qty;
					$price = $rest1231->price;
					$itemid = $rest1231->id;
					$shortname= $rest1231->shortname;
					
					$amendment="";
					$q = $this->db->select('qty')->from('po_amendend_history')->where('pono',$restyui1->pono)->where('item_id',$itemid)->order_by('id','desc')->limit('1')->get();
										if($q->num_rows()>0){
										    foreach($q->result() as $rowss);
										    $amendment =  "(AMENDED ".floatval($rowss->qty)." ".$shortname." to ".floatval($qty)." ".$shortname.")";
										}
					
			}

			$edit = "<a href='".page_url."Reporting/editpohistory/".$restyui1->id."' class='btn btn-warning'>EDIT</a>";

			$scheduler_data[] = array('sr_no'=>$i,
			'prno'=>"<a href='".page_url."Store/".$polink."/".$restyui1->pono."' target='_blank' style='color:".$bc."'>".$restyui1->pono."</a> ".$amendment,
			'jobcardno'=>$jobcard,
			'createdby'=>ucfirst($restyui1->first_name." ".$restyui1->last_name),
			'itemname'=>$itemname,
			'qty'=>$qty,
			'price'=>$price,
			'oldprice'=>$oldprice,
			'vendor'=>$restyui1->name,
			'diff'=>$diff,
			'prnumber'=>$restyui1->prno,
			'createdon'=>date('d-M-Y g:i A',strtotime($restyui1->addedOn)),
			'approvredon'=>date('d-M-Y g:i:A',strtotime($restyui1->approvedOn)),
			'status'=>$app,
			'remarks'=>$rmk,
			'action' => $edit);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
}
	
	
	
	

	public function packed_order_listforsales()
	{
		
		
		$scheduler_data = array();
		$this->db->select('a.pono,a.sono,a.order_id, a.order_type, a.marketing_person,a.po_number, a.company_name, a.address, a.email, a.mobile_number, a.internal_order_no, a.discount, a.order_value_after_discount, a.advance_amount, a.payment_terms, a.installation_charges, a.order_status, a.added_on,a.packing_type, a.installation_type, b.user_id, b.title, b.first_name, b.last_name, a.installation_amount, a.packing_charges, a.packing_amount, a.freight_type, a.freight_amount,a.remarks')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left')->where('a.order_status','1');
		if($this->uri->segment(3)){
		    $this->db->where('a.marketing_person',$this->uri->segment(3));
		}
	
		$query = $this->db->where('a.closeorder','0')->where('a.movetodispatch','0')->order_by('a.order_id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			$completedjobcard=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1')->get();
			$completedjobcards=$completedjobcard->num_rows();
			
			$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name, a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			$alljobcard=$query1->num_rows();
			
		if($completedjobcards==$alljobcard)
		{
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			
			$html = "<table border='1' style='width:500px;'><tr style='background-color:white;'><th style='padding:2px 2px 2px 2px'>INSTRUMENT</th><th style='padding:2px 2px 2px 2px'>JOB CARD NO.</th></tr>";
			$instrumentsss = array();
			$isinstrumentavailable=0;
			
			
			foreach($query1->result() as $instruments){
				if($instruments->complete=='1'){
					$backgroundcolor = "background-color:#10C469; color:white !important; font-weight:bold;";
				}else{
					$backgroundcolor="";
				}
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->instruments_name)."</td>";
				//$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->qty)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->job_card_no)."</td>";
				$html.="</tr>";
				$isinstrumentavailable=1;
			}
			$html.="</table>";
			$ins_charges = "";
			/** Get order planned **/
			$restt=$this->db->select('count(id) as totaljobcard')->from('order_instruments')->where('order_id',$row->order_id)->get();
			foreach($restt->result() as $instcount);
			$totjobcard= $instcount->totaljobcard;
			
			
	
			
			if($row->installation_charges=='1'){
				$charges= "<strong>INSTALLATION TYPE </strong>".$row->installation_type."<br> <strong>INSTALLATION CHARGES</strong> - REQUIRED";
				
				$ins_charges= $charges."<br> AMOUNT - ".$row->installation_amount;
			}else{
					$ins_charges = "NOT REQUIRED";
			}
			
			if($row->packing_charges=='1'){
				$packcharges = "PAID BY PARTY";
				$packingcharges= $packcharges."<br> Amount - <strong>".$row->packing_amount."</strong>";
			}else{
					$packingcharges = "INCLUSIVE";
			}
			
			if($row->freight_type=='1'){
				$freigntcharges = "TO PAY BASIS";
				
			}else if($row->freight_type=='2'){
				$freigntcharges = "PAID BY PRESTO";
			}else if($row->freight_type=='3'){
				$frtcharges = "BILLED IN INVOICE";
				$freigntcharges= $frtcharges."<br> Amount - <strong>".$row->freight_amount."</strong>";
			}else{
				$freigntcharges = "OWN PICK-UP";
					
			}
			
			$completeorder='<a href="javascript:;" onclick="markstagedone('."'".$row->order_id."'".')"><span class="btn btn-warning">Move to Dispatch</span></a>';
			
			if($isinstrumentavailable==1)
			{
			
			$po='';
			if(file_exists(sfpo.$row->internal_order_no.'.pdf'))
			{
			$po="<a href='".page_url."sfpo/".$row->internal_order_no.".pdf' target='_blank'>".$row->internal_order_no."</a>";
			}else
			{
			if(file_exists(sfpo.$row->pono.'.PDF'))
			{
			$po="<a href='".page_url."sfpo/".$row->internal_order_no.".PDF' target='_blank'>".$row->internal_order_no."</a>";
			}else
			{
			
			
			}
			}
			
			$scheduler_data[] = array('sr_no'=>$i,
			'closeorder'=>$completeorder,
			'added_on'=>$addeddate."".$addedtime,
			'order_type'=>strtoupper($row->order_type),
			'marketing_person'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'po_number'=>strtoupper($row->po_number),
			'company_name'=>strtoupper($row->company_name),
			'address'=>strtoupper($row->address),
			'email'=>strtoupper($row->email),
			'mobile_number'=>strtoupper($row->mobile_number),
			'internal_order_no'=>strtoupper($row->internal_order_no),
			'itemname'=>$html,
			'discount'=>strtoupper($row->discount)."%",
			'order_value_after_discount'=>strtoupper($row->order_value_after_discount),
			'advance_amount'=>strtoupper($row->advance_amount),
			'payment_terms'=>strtoupper($row->payment_terms),
			'installation_charges'=>strtoupper($ins_charges),
			'packingcharges'=>"<strong>Packing Type</strong> -".$row->packing_type."<br>".$packingcharges,
			'freigntcharges'=>$freigntcharges,
			'remarks'=>$row->remarks,
			'pono'=>$po,
			'sono'=>$row->sono);
			}
			$i++;
		}
	}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}
	
	

	function completedordersforsales()
	{
		
$this->load->view('FMS/crmcompletedordersforsales');
	
	}
	
	
	
	function movetodispatchbysales()
{
	$orderid=$this->uri->segment(3);
		$userid=$this->uri->segment(4);
	

	$data=array('movetodispatch'=>'1','movedby'=>$_SESSION['logged_in']['user_id'],'movedOn'=>date('Y-m-d H:i:s'));
	$this->db->where('order_id',$orderid);
	$this->db->update('prestogroup_orders',$data);
	
	
	$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Moved To Dispatch.</span></div>');
		redirect(page_url.'Reporting/completedordersforsales/'.$userid);
	
	
	
}

function pendingconsolidatedpovsdelivery()
{

	$this->load->view('store/pendingdeliveryconsolidation'); 
}
	


	function po_vs_delivery_list_vendorwise()
{

			$scheduler_data = array();
		$rest=$this->db->select('a.id,count(a.id) as vendcount,a.vendor')->from('purchase_order a')->where('a.approved','1')->where('a.gateentrycomplete','0')->where('a.completed','0')->order_by('vendcount','DESC')->group_by('a.vendor','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
$vendorname=$this->getvendorname($restyui1->vendor);
				

			$scheduler_data[] = array('sr_no'=>$i,

			'vendorname'=>"<a href='".page_url."Reporting/po_vs_delivery_report/".$restyui1->vendor."'>".$vendorname."</a>",
			'icount'=>"<a href='".page_url."Reporting/po_vs_delivery_report/".$restyui1->vendor."'>".$restyui1->vendcount."</a>"
			);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);

}



function getjobcardinstrument($jobcard)
{
    $restey=$this->db->select('a.item_id,b.instruments_name')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->where('a.id',$jobcard)->get();
    if($restey->num_rows()>0)
    {
        foreach($restey->result() as $restey12);
        
        return $restey12->instruments_name;
        
    }else
    {
        return '';
    }
    

}

	

public function po_vs_delivery_reportforproduction() {
		$this->load->view('store/po_vs_deliveryforproduction');
	}




	function po_vs_delivery_listforproduction()
{

			$scheduler_data = array();
			$vendorid=$this->uri->segment(3);

		$this->db->select('a.*')->from('purchase_order a')->where('a.approved','1')->where('a.gateentrycomplete','0')->where('a.completed','0');
		if($vendorid<>'')
		{
			$this->db->where('a.vendor',$vendorid);
		}
		$rest=$this->db->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
$vendorname=$this->getvendorname($restyui1->vendor);
				if($restyui1->potype==0)
				{
					
					$type="Machine Item";
					$itemname=$this->getmachineitemname($restyui1->itemid);
					$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
					if(count($otherdetails)>0)
					{

					$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
					}else{
					$fincode='';
					$specification='';
					}
			
					
				}else if($restyui1->potype==1)
				{
					$type="General Item";
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
					
				}
				

						if($restyui1->potype==0)
						{
							
						if($restyui1->source==1)
						{
						

							$job=$this->db->select('job_card_no,item_id as machineid')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
							if($job->num_rows()>0)
							{
							foreach($job->result() as $job1);
							$macid=$job1->machineid;
							}else{
							$macid=0;
							}

						}else 
						{
							$macid=0;
						}

						}else 
						{
							$macid=0;
						}
					

						/** **/
						$prevqty=$this->storemodel->checkifanypreviousqtyisinwardedinmrn($restyui1->itemid,$restyui1->pono,$restyui1->id);

                        if($prevqty>0)
                        {
                           $requiretobeinwarded=$restyui1->qty-$prevqty;
                        }else
                        {
                             $requiretobeinwarded=$restyui1->qty;
                        }
						/** END **/
							

						$restyu=$this->db->select('shortname')->from('units')->where('id',$restyui1->unit)->get();
						if($restyu->num_rows()>0)
						{
						foreach($restyu->result() as $restyu1);
						$unival=$restyu1->shortname;
						}else{
						$unival='';							
						}

						/** CHECK FOR ANY FOLLOWUP **/
						$podelidate=$this->storemodel->expcteddateofdelivery($restyui1->id,$restyui1->prno);
						/** END **/


            $scheduler_data[] = array('sr_no'=>$i,
			'pono'=>$restyui1->prno,
			'vendorname'=>$vendorname,
			'itemname'=>$itemname,
			'itemtype'=>$type,
			'qty'=>$requiretobeinwarded ." ".$unival,
			'prevqty'=>$prevqty ." ".$unival,
			'fincode'=>$fincode,
			'specialization'=>$specification,
			'deliverydate'=>$podelidate
		);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);

}



function senddispatchintimationtoclientfortestronix($orderid)
{

	
	
	$html='';
	$restyeur=$this->db->select('marketing_person,internal_order_no,contact_person,company_name,mobile_number,docketnumber,docketno,billcopy,billno,billdate,totalpacket,shipmentmode,dodamount,frieghtamount,email,tname')->from('prestogroup_orders')->where('order_id',$orderid)->get();
	if($restyeur->num_rows()>0)
	{
		$insdata=$this->getorderinstumentdetails($orderid);
		
		foreach($restyeur->result() as $restyeur1);

$marketemail=$this->fmsmodel->getmarketingpersonemail($restyeur1->marketing_person);

if($restyeur1->contact_person!='')
{
$html.='Dear '.ucwords($restyeur1->contact_person).' Ji,<br/><br/>';
}else
{
$html.='Dear Customer,<br/><br/>';
}
$html.='We are pleased to inform you that your order no. '.$restyeur1->internal_order_no.' for the testing instruments have been dispatched as on '.date('d-M-Y').'  as per the details below.<br/><br/>';
$html.='Instruments Name';
$html.=$insdata.'<br/><br/><br/>';	


$html.='Total No. of Packets: '.$restyeur1->totalpacket.'<br/>';
$html.='Mode of Shipment: '.$restyeur1->shipmentmode.'<br/>';
$html.='Transporter Type: '.$restyeur1->tname.'<br/>';
$html.='Docket No / Tempo No.: '.$restyeur1->docketnumber.'<br/>';
$html.='Freight Amount: '.$restyeur1->frieghtamount.'<br/>';
$html.='DOD Amount: '.$restyeur1->dodamount.'<br/><br/>';

$html.='Please unpack the parcel on the same date of receiving and check the safe and accurate delivery of the instrument <br/><br/>';
$html.='Kindly acknowledge receipt of the same. For any technical or Service query please mail us : <a href="mailto:info@testronixinstruments.com">info@testronixinstruments.com</a><br/>';
$html.='We thank you for having a sound faith on Testronix !!<br/><br/>';
$html.='Hoping to meet your future needs.<br/><br/>';

$html.='Regards,<br/>
Dispatch Team<br/>
Testronix Instruments<br/>
I-10 A, DLF Industrial Area, Phase-1<br/>
Delhi Mathura Road, Faridabad-121003<br/>
Haryana, India<br/>
Phone - <a href="tel:919313140140">+91-9313140140</a><br/>';


$billcopy=UPLOADPATH.'docketno/'.$restyeur1->billcopy;
$docketcopy=UPLOADPATH.'docketno/'.$restyeur1->docketno;

	$subjectname = "Order Dispatched -".$restyeur1->company_name;

$this->load->library('email');
	$config1 = array();  
	$config1['protocol'] = 'smtp';  
	$config1['smtp_host'] = 'smtpout.secureserver.net';  
	$config1['smtp_user'] = 'info@testronixinstruments.com';  
	$config1['smtp_pass'] = '>KLu87%$#i';   
	$config1['smtp_port'] = 465;  
	 $this->email->initialize($config1);
	$this->email->set_newline("\r\n");  
	$this->email->set_mailtype("html");
	$this->email->to($restyeur1->email.',sdsrbh5@gmail.com');	
	$this->email->cc('audit2@prestogroup.com,'.$marketemail.',audit@prestogroup.com');
	
	$this->email->bcc('sdsrbh5@gmail.com');
	$this->email->from('info@testronixinstruments.com','TESTRONIX INSTRUMENTS');
	$this->email->subject($subjectname);
	$this->email->message($html);
	if($restyeur1->billcopy<>'')
	{
	$this->email->attach($billcopy);
	}
	if($restyeur1->docketno<>'')
	{
	$this->email->attach($docketcopy);
	}
	$result11=$this->email->send();
//echo $this->email->print_debugger();exit;

if($result11)
{
	/**** SMS INTEGRATION***/

	$smsmessage='We have Dispatched ordered TESTRONIX Testing Instruments.Details mailed to your e-mail id.%0a%0aTeam TESTRONIX';

	$postData = array(
	'authkey' => '266631AuRXQ3UyZ5c839e9b',
	'mobiles' => $restyeur1->mobile_number.',8447031736',
	'message' => $smsmessage,
	'sender' => 'PRESTO',
	'route' => '4'
	);


	$url="http://api.msg91.com/api/sendhttp.php";
	$ch = curl_init();
	curl_setopt_array($ch, array(
	CURLOPT_URL => $url,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_POST => true,
	CURLOPT_POSTFIELDS => $postData
	));



	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$response = curl_exec($ch);
	$err = curl_error($ch);

	curl_close($ch); 

	if ($err) {
	echo "cURL Error #:" . $err;
	} else {
	//echo $response;
	} 

}

	/**** SMS INTEGRATION***/
	

//exit;

	}



}



function issuereportforstore()
{

$scheduler_data = array();
$sdate=$this->uri->segment(3);
$edate=$this->uri->segment(4);
$item=$this->uri->segment(5);

$scheduler_data=array();	
	$firstdate=date('Y-m-d',strtotime($sdate))." 00:00:00";
	$lastdate=date('Y-m-d',strtotime($edate))." 23:59:59";
	$Restey=$this->db->select('a.*')->from('issuestocktousers a')->where('a.issuedOn BETWEEN "'.$firstdate. '" and "'.$lastdate.'"');
	if($item<>'')
	{
		$this->db->where('a.itemid',$item);
	}
	$Restey=$this->db->get();
	if($Restey->num_rows()>0)
	{	
		$a=1;
		foreach($Restey->result() as $Restey1)
		{
			if($Restey1->jobcardid!=0)
			{
				$jobcard=$this->getjobcardno($Restey1->jobcardid);
				
			}else{
				
				$jobcard='';
				
			}
			
			if($Restey1->storeaccept==1)
			{

				$storeapp="YES <br/>".date('d-M-Y',strtotime($Restey1->acceptedOn));
			}else
			{
				$storeapp="NO";

			}
			
				$machinedetails=$this->getmachineotherdetails($Restey1->itemid);
				if(count($machinedetails)>0)
				{
				$fincode=$machinedetails['fincode'];
				$specification=$machinedetails['specialization'];
				$part=$machinedetails['name'];
				$rack_location=$machinedetails['rack_location'];
				$unit=$machinedetails['unit'];
				$size=$machinedetails['size'];
				$material=$machinedetails['material'];
				}else{
				$fincode='';
				$specification='';
				$part='';
				$rack_location='';
				$unit='';
				$size='';
				$material='';
				}
				
				if($unit<>'')
				{
				$unitname=$this->getunit($unit);
				}else
				{
				$unitname='';
				}
				
				if($Restey1->usertype==1)
				{
					$issueduser=$this->storemodel->getudata($Restey1->issuedto);
					
				}else if($Restey1->usertype==2)
				{
					$issueduser=$this->storemodel->getnoncrmusername($Restey1->issuedto);
					
				}else
				{
					$issueduser='';
				}


				
				$scheduler_data[] = array(
				'sr_no'=>$a,
				'jobcard'=>$jobcard,
				'itemname'=>$part,
				'fincode'=>$fincode,
				'specialization'=>$specification,
				'size'=>$size,
				'material'=>$material,
				'qty'=>$Restey1->stock." ".$unitname,
				'issuedto'=>$issueduser,
				'issuedon'=>date('d-M-Y H:i:s',strtotime($Restey1->issuedOn)),
				'storeaccept'=>$storeapp
				);
	     		
		$a++;
		}
	
	}
	$results = array(
    "sEcho" => 1,
    "iTotalRecords" => count($scheduler_data),
    "iTotalDisplayRecords" => count($scheduler_data),
    "aaData"=>$scheduler_data);
    echo json_encode($results);	


}

	public function getdepartment_user(){
	   $userid = $this->input->post('userid');
	   
	   $res=$this->db->select('user_id,first_name,last_name,department_id,user_status')->from('system_users')->where('department_id',$userid)->where('user_status','1')->where('hide_profile','0')->get();
	   if($res->num_rows() >0)
	   {
	       foreach($res->result() as $rows)
	       {
	         $html='<input type="checkbox" name="userid[]" value="'.$rows->user_id.'" id="userid"/>'.' '.$rows->first_name .' '.$rows->last_name.'</br>';  
	         echo $html;
	       }
	   }
	   
	   //echo $html;
	}
	
	public function setpermission(){
	    $mouduleid=$this->uri->segment(3);
	   
	    $q = $this->db->select('moduleid,id')->from('submodule')->where('id',$mouduleid)->get();
	    foreach($q->result() as $row);
	    $mdid=$row->moduleid; 
	   $userid = $this->input->post('userid');
	   //echo "<pre>"; print_r($userid);exit;
	   $user_id =$this->session->userdata['logged_in']['user_id'];           
        date_default_timezone_set("Asia/Kolkata");
        $added_time = date('Y-m-d H:i:s');
      
       
        if(isset($_REQUEST['userid'])){ 
        $tags1=count($_REQUEST['userid']);
        if($tags1>0)
        {
        $username=$_REQUEST['userid'];
     
        for($x=0;$x<$tags1;$x++){
        if($username[$x]!='')
        {
           // echo "test"; exit;
        $add=1;
        $edit=0;
        $remove=0;
          /*----check module permissson------*/
        $res=$this->db->select('id')->from('module_access')->where('role_id',$username[$x])->where('moduleid',$mdid)->get();
        if($res->num_rows() >0)
        {
            foreach($res->result() as $rows);
            $lid=$rows->id;
        }else
        {
            $data=array('role_id'=>$username[$x],'moduleid'=>$mdid,'access'=>'1','addedOn'=>date('Y-m-d h:i:s'));
			$this->db->insert('module_access',$data);
				$lid=$this->db->insert_id();
        }
       $submoduleaccess=1;
       	$data1=array('acessid'=>$lid,'role_id'=>$username[$x],'moduleid'=>$mdid,'submoduleid'=>$mouduleid,'submodule_access'=>$submoduleaccess,'madd'=>$add,'medit'=>$edit,'mremove'=>$remove,'addedOn'=>date('Y-m-d h:i:s'));
	   $this->db->insert('module_capablity',$data1);
       
        }
        }
        }
        }
        $this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span><br/>');
        redirect(page_url.'Reporting/reporting_index');

	   
	   //echo $html;
	}
	
	public function show_modules() {
        // echo 'hi';exit;
        $this->load->view('store/show_modules');
    }
    
     public function system_reports()
	{
		$scheduler_data = array();
	    $query = $this->db->select('*')
    		              ->from('system_reports')
                          ->order_by('id','asc')->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		$i=1;
		foreach($res as $row)
		{
		    $second_date="";
		    $edit = "<a href='".page_url."Reporting/edit_module/".$row->id."'><i class='fa fa-pencil'></i></a>";	
		    $url = "<a href='".page_url.$row->url."'>View URL</a>";	
		    $video = "<a href='".$row->video."' target='_blank'>View Video</a>";
		    
		    $img="<img src='".sfdocument."Reportingimage/".$row->icon."' width='100px'>";
			$scheduler_data[] = array('sr_no' => $i,
                        			  'report_name' => $row->report_name,
                        			  'title' => $row->title,
			                          'description' => $row->description,
			                          'pimage'=>$img,
			                          'video' => $video,
			                          'url' => $url,
			                          'edit' => $edit
			);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}

    public function edit_module() {
        $id = $this->uri->segment(3);
        $data['getEditModule'] = $this->storemodel->getEditModule($id);
        $this->load->view('store/edit_module', $data);
    }
    
    	public function update_module() {
		$id=$this->uri->segment(3);
		 $adharcard=$_FILES['pimage']['name'];
		 $old_image=$this->input->post('oldimage');
		if($adharcard<>'')
		{
		   
			$image1=explode('.',$adharcard);
			$cat_image=end($image1);
			$aadhar_card=time().'.'.$cat_image;
			move_uploaded_file($_FILES["pimage"]["tmp_name"],UPLOADPATH.'Reportingimage/' . $aadhar_card);
		}else
		{
			$aadhar_card=$old_image;
			}
	
		
	        $data = array(
						'report_name' => $this->input->post('report_name'),
						'title' => $this->input->post('title'),
						'video' => $this->input->post('video'),
						'icon' => $aadhar_card,
						'description' => $this->input->post('description')
						);
						
		

		$result = $this->storemodel->update_module($id, $data);
		
     $this->session->set_flashdata('success','<div class="alert alert-success">Record has been updated successfully.</div>');
	  redirect(page_url.'Reporting/show_modules');
		

	}

	function pendingpoforapproval_change()
	{
	   	$this->load->view('store/pendingpoforapproval_change');
		
		
	}

		function pendingpolistforapproval_changes()
{

    $scheduler_data = array();
    $genpo=base64_decode($this->uri->segment(3));
    $genp=explode(',',$genpo);
	
		$res=$this->db->select('v.name, v.address')->from('vendors')->join('purchase_order a','a.vendor=v.id','left')->get();
		if($res->num_rows() >0)
		{
			foreach($res->result() as $vendor){
				
			
		$rest=$this->db->select('a.pricechange,a.originalprice,a.id,a.prno,a.jobcard,a.source,a.sourceid,a.potype,a.addedOn,a.pono,e.first_name,e.last_name,a.itemid,e.department_id, v.name, v.address')->from('purchase_order a')->join('system_users_view e','e.user_id=a.addedBy','left')->join('vendors v','a.vendor=v.id','left')->where('a.approved','0')->order_by('a.addedOn','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				if(in_array($restyui1->pono,$genp))
				{
					$bc="red";
				}else{
					$bc='';
				}
				
					$jobcard='';

					if($restyui1->source==1)
					{
					$job=$this->db->select('job_card_no')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
					if($job->num_rows()>0)
					{
					foreach($job->result() as $job1);
					$jobcard='Jobcard-'.$job1->job_card_no;
					}else{
					$jobcard='';
					}
					$polink="po";
					$mrnlink="mrn";
					}else if($restyui1->source==2)
					{
					/** Intend **/
					$jobcard='INDENT- IND'.$restyui1->sourceid;
					$polink="generalpo";
					$mrnlink="generalmrn";
					}else if($restyui1->source==3)
					{
					/** Intend **/
					$jobcard='AUTO PR';
					$polink="po";
					$mrnlink="mrn";
					}					


				
				if($restyui1->potype=='0')
				{
					$polink="po";
					$mrnlink="mrn";
				}else
				{
					$polink="generalpo";
					$mrnlink="generalmrn";
				}

$html='<a href="javascript:;" onclick="approvepo('."'".$restyui1->pono."'".');" class="btn btn-warning btn-xs">APPROVE PO</a>';
$approved = '<input type="checkbox" name="approvedrecord[]" value="'.$restyui1->id.'" onchange="markasapproved('."'".$restyui1->id."'".','."'".$restyui1->pono."'".');">';
$html1='<a href="javascript:;" class="btn btn-warning btn-xs" onclick="showpopup('."'".$restyui1->pono."'".');">REJECT PO</a>';

$htm="";


if($restyui1->potype=='0'){
    
					$indenttype = "Machine Related Items";
				$rest123=$this->db->select('b.part as item_name,a.qty,a.price,a.unit,a.itemid')->from('purchase_order a')->join('machine_parts_with_picture_view b','a.itemid=b.id')->where('b.id',$restyui1->itemid)->get();	
				}else{
					$indenttype = "General Items";
				$rest123=$this->db->select('b.item_name,a.qty,a.unit,a.price, a.itemid')->from('purchase_order a')->join('house_keeping_items b','a.itemid=b.id')->where('b.id',$restyui1->itemid)->get();	
				}
				
				
				if($rest123->num_rows()>0)
				{
					
					foreach($rest123->result() as $rest1231)
					$price = $rest1231->price;
					$oldprice="";
					$query = $this->db->select('price')->from('purchase_order')->where('itemid',$rest1231->itemid)->where('approved','1')->limit('1')->order_by('id','desc')->get();
					if($query->num_rows()>0){
						foreach($query->result() as $oldata);
						$oldprice = $oldata->price;
						$diff = abs($price-$oldprice);
					}else{
					    $oldprice="New Item";
					    $diff="";
					}
					$itemname = strtoupper($rest1231->item_name);
					$qty = $rest1231->qty;
					$price = $rest1231->price;
			}

            if($restyui1->pricechange==1)
            {
            $pri="<span style='color:red;font-weight:bold;'>".$price."</span>" ;
            }else
            {
            $pri=$price;
            }
            
          

			$scheduler_data[] = array('sr_no'=>$i,
			'prno'=>"<a href='".page_url."Store/".$polink."/".$restyui1->pono."' target='_blank' style='color:".$bc."'>".$restyui1->pono."</a>",
			'jobcardno'=>$jobcard,
			'createdby'=>ucfirst($restyui1->first_name." ".$restyui1->last_name),
			'itemname'=>$itemname,
			'qty'=>$qty,
			'price'=>$pri,
			'oldprice'=>$oldprice,
			'vendor'=>$restyui1->name,
			'diff'=>$diff,
			'prnumber'=>$restyui1->prno,
			'createdon'=>date('d-M-Y g:i A',strtotime($restyui1->addedOn)),
			'markrecvd'=>$approved,
			'markrrej'=>$html1);
			$i++;
			}
			}
			}
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
}

function itemmrn_history()
{
	$this->load->view('store/itemmrnqc_history');
	
}

function pendingpoforapproval_history()
	{
	   	$this->load->view('store/pendingforapprovalhistory');
		
		
	}

	function itemmrn_byitemname()
	{
		$this->load->view('store/itemmrnqc_byitemname');
		
	}

	function mrnitemrequest_byitemname()
		{
			$scheduler_data = array();
			$itemid=$this->uri->segment(3);
			$filterid=$this->uri->segment(4);
			if($itemid <>''){
			if($filterid=='1'){
		
		$rest=$this->db->select('a.*')->from('purchase_order_view a')->where('a.approved','1')->where('a.gateentrycomplete','0')->where('a.completed','0')->where('a.itemid',$itemid)->order_by('a.addedOn','DESC')->get();
		}else
		{
			$rest=$this->db->select('a.*')->from('purchase_order_view a')->where('a.approved','1')->where('a.gateentrycomplete','0')->where('a.completed','0')->where('a.vendor',$itemid)->order_by('a.addedOn','DESC')->get();
		}
		if($rest->num_rows()>0)
		{
		$i=1;
		foreach($rest->result() as $restyui1)
		{
		$vendorname=$this->getvendorname($restyui1->vendor);
		if($restyui1->potype==0)
		{

		$type="Machine Item";
		$itemname=$this->getmachineitemname($restyui1->itemid);
		$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
		if(count($otherdetails)>0)
		{

		$fincode=$otherdetails['fincode'];
		$specification=$otherdetails['specialization'];
		$conunit=$otherdetails['conunit'];
		$conweight=$otherdetails['conweight'];
		}else{
		$fincode='';
		$specification='';
		$conunit=0;
		$conweight='';
		}


		}else if($restyui1->potype==1)
		{
		$type="General Item";
		$itemname=$this->getgeneralitemname($restyui1->itemid);
		$fincode='';
		$specification='';
		$conunit=0;
		$conweight='';

		}


		if($restyui1->potype==0)
		{

		if($restyui1->source==1)
		{


		$job=$this->db->select('job_card_no,item_id as machineid')->from('order_instruments_view')->where('id',$restyui1->jobcard)->get();
		if($job->num_rows()>0)
		{
		foreach($job->result() as $job1);
		$macid=$job1->machineid;
		}else{
		$macid=0;
		}

		}else 
		{
		$macid=0;
		}

		}else 
		{
		$macid=0;
		}


		/** **/
		$prevqty=$this->storemodel->checkifanypreviousqtyisinwardedinmrn($restyui1->itemid,$restyui1->pono,$restyui1->id);

		if($prevqty>0)
		{
		$requiretobeinwarded=$restyui1->qty-$prevqty;
		}else
		{
		$requiretobeinwarded=$restyui1->qty;
		}
		/** END **/


		$restyu=$this->db->select('shortname')->from('units_view')->where('id',$restyui1->unit)->get();
		if($restyu->num_rows()>0)
		{
		foreach($restyu->result() as $restyu1);
		$unival=$restyu1->shortname;
		}else{
		$unival='';							
		}


		if($conunit>0)
		{

		$restyu1=$this->db->select('shortname')->from('units_view')->where('id',$conunit)->get();
		if($restyu1->num_rows()>0)
		{
		foreach($restyu1->result() as $restyu11);
		$unival121=$restyu11->shortname;
		}else{
		$unival121='';							
		}

		$conversion="<span style='color:red'>Conversion-".$conweight." ".$unival121."</span><br/><span>Total Qty - ".$conweight*$requiretobeinwarded." ".$unival121."</span>";

		}else
		{
		$conversion='';
		$unival121='';
		}


		$html='<a href="javascript:;" class="btn btn-warning btn-xs" onclick="opemmodalpopup('."'".$restyui1->id."'".','."'".$restyui1->pono."'".','."'".floatval($requiretobeinwarded)."'".','."'".$unival."'".','."'".$conunit."'".','."'".$conweight."'".','."'".$unival121."'".')">Gate Entry & MRN</a>';

		$checkbox='<input type="checkbox" class="subcheckbox" name="itemid[]" onchange="showqty(this.value)" id="show'.$restyui1->id.'" value="'.$restyui1->id.'">';
		$checkinput='<div id="showdis'.$restyui1->id.'" class="itemrequest" style="display:none"><input type="hidden" name="poid[]" value="'.$restyui1->id.'"><input type="hidden" name="pono'.$restyui1->id.'" value="'.$restyui1->pono.'"><input type="hidden" name="pendqty'.$restyui1->id.'" id="pendqty'.$i.'" value="'.floatval($requiretobeinwarded).'"><input type="text" class="itemreq"  name="recqty'.$restyui1->id.'" onkeyup="checkifitsvalid(this.value,'.$i.','.$restyui1->id.'); checkifvalid(this)" id="receviedqty'.$restyui1->id.'" value="'.$requiretobeinwarded.'"></div>';
		

		$scheduler_data[] = array('sr_no'=>$i,
		'checkbox'=>$checkbox,
		'pono'=>$restyui1->pono,
		'vendorname'=>$vendorname,
		'itemname'=>$itemname,
		'itemtype'=>$type,
		'qty'=>$requiretobeinwarded ." ".$unival."<br/>".$conversion,
		'prevqty'=>$prevqty ." ".$unival,
		'fincode'=>$fincode,
		'specialization'=>$specification,
		'gentry'=>$checkinput);
		$i++;
		}
		}
		}
		$results = array(
		"sEcho" => 1,
		"iTotalRecords" => count($scheduler_data),
		"iTotalDisplayRecords" => count($scheduler_data),
		"aaData"=>$scheduler_data);
		echo json_encode($results);

		}

public function	getmrnitemname()
	{
		$searchtrm=$_GET['searchTerm'];
	$this->db->select('a.id as pid,a.itemid,b.id as machineid,b.part,b.specification')->from('purchase_order_view a')->join('machine_parts_with_picture_view b', 'a.itemid=b.id')->like('b.part',$searchtrm,'both')->where('a.approved','1')->where('a.gateentrycomplete','0')->where('a.completed','0')->group_by('b.part');
	$que=$this->db->get();
	if($que->num_rows()>0)
	{
		foreach($que->result() as $itemdata){
				{
			$json[] = array('id'=>$itemdata->itemid, 'text'=>$itemdata->part."-".$itemdata->specification);
		}
	}
	}else{
	$json[] = array('id'=>"", 'text'=>"No Data Available");
	}
	
	echo json_encode($json);
	}

public function	getmrnvendor()
	{
		$searchtrm=$_GET['searchTerm'];
	$this->db->select('a.id as pid,a.name')->from('vendors a')->like('a.name',$searchtrm,'both')->where('a.status','1');
	$que=$this->db->get();
	if($que->num_rows()>0)
	{
		foreach($que->result() as $itemdata){
				{
			$json[] = array('id'=>$itemdata->pid, 'text'=>$itemdata->name);
		}
	}
	}else{
	$json[] = array('id'=>"", 'text'=>"No Data Available");
	}
	
	echo json_encode($json);
	}
public function editmrn()
	{
		$this->load->view('store/edit_mrn');
	}


	public function update_mrn()
	{
		$id=$this->uri->segment(3);

		$data=array(
			'recqty'=>$this->input->post('recqty'),
			'gateentryno'=>$this->input->post('gateentry'),
			'billno'=>$this->input->post('bill_no')
		);
		$this->db->where('id',$id);
		$this->db->update('mrn',$data);

		$poid=$this->input->post('poid');
		$itemid=$this->input->post('itemid');
		$recqty=$this->input->post('recqty');

		$res=$this->db->select('qty')->from('purchase_order')->where('itemid',$itemid)->where('id',$poid)->get();
		if($res->num_rows() > 0){
		foreach ($res->result() as $key);
		$purchaseqty=$key->qty;
	}else{
		$purchaseqty=0;
	}

		$totalqty= $this->db->select('sum(recqty) as purchaseqty')->from('mrn')->where('poid',$poid)->get();
if ($totalqty->num_rows() > 0) {
	foreach ($totalqty->result() as $keyss);
}
		if ($purchaseqty==$totalqty->purchaseqty) {
			$data1=array(
				'completed'=>1);
			$this->db->where('id',$poid);
			$this->db->update('purchase_order',$data1);

		}else{

			$data1=array(
				'completed'=>0);
			$this->db->where('id',$poid);
			$this->db->update('purchase_order',$data1);	

			$data2=array('mrndone'=>0);
			$this->db->where('id',$id);
			$this->db->update('mrn',$data2);		
}

$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:000;">MRN Updated Successfully!!!.</span></div>');
		redirect(page_url.'Reporting/itemmrn');

		
	}
function gateentry_history()
{
	$this->load->view('store/gateentry_history');
	
}

function qchistory_data()
{

        $date = date('Y-m-d');
		$date = strtotime($date);
		$date = date('Y-m-d',strtotime("-7 day", $date)).' 00:00:00';
			$scheduler_data = array();
$genpo=base64_decode($this->uri->segment(3));
$genp=explode(',',$genpo);
		$rest=$this->db->select('a.*,a.gateentryno as gateno,a.id as mrnid,b.*,b.id as poid')->from('mrn a')->join('purchase_order b','a.poid=b.id')->where('b.approved','1')->where('a.qcstatus','1')->group_by('a.id')->order_by('a.addedOn','DESC')->get();
		//->where('a.completed','0')
			if($rest->num_rows()>0)
			{
			$i=1;
		//	echo "<pre>"; print_r($rest->result()); exit;
			foreach($rest->result() as $restyui1)
			{
$vendorname=$this->getvendorname($restyui1->vendor);
				if($restyui1->potype==0)
				{
					
					$type="Machine Item";
					$itemname=$this->getmachineitemname($restyui1->itemid);
					$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
					if(count($otherdetails)>0)
					{

					$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
					}else{
					$fincode='';
					$specification='';
					}
			
					
				}else if($restyui1->potype==1)
				{
					$type="General Item";
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
					
				}
				

						if($restyui1->potype==0)
						{
							
						if($restyui1->source==1)
						{

							$job=$this->db->select('job_card_no,item_id as machineid')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
							if($job->num_rows()>0)
							{
							foreach($job->result() as $job1);
							$macid=$job1->machineid;
							}else{
							$macid=0;
							}

						}else 
						{
							$macid=0;
						}

						}else 
						{
							$macid=0;
						}
					

						/** **/
						//$prevqty=$this->storemodel->checkifanypreviousqtyisinwarded($restyui1->itemid,$restyui1->pono);

						/** END **/
							

						$restyu=$this->db->select('shortname')->from('units')->where('id',$restyui1->unit)->get();
						if($restyu->num_rows()>0)
						{
						foreach($restyu->result() as $restyu1);
						$unival=$restyu1->shortname;
						}else{
						$unival='';							
						}


$originalleftqty=$restyui1->recqty;
$gateentry=$restyui1->gateno;
$billno=$restyui1->billno;

$html='<a href="'.page_url.'Reporting/vendorwisepoforgateentry/'.$restyui1->vendor.'" class="btn btn-warning btn-xs">Open PO(s)</a>';
$gateentry=$gateentry;

$recvqty11=floatval($originalleftqty)." ".$unival;
$recvqty=$recvqty11;

$datas=$this->storemodel->getmrnhistorydata($restyui1->mrnid);
if(count($datas)>0)
{

$approved=$datas['accept'];
$reject=$datas['reject'];;
$rejectremarks=$datas['remarks'];  
$rejectfile=$datas['image'];
    
    
}else
{
$approved='Data not available';
$reject='Data not available';
$rejectremarks='Data not available';
$rejectfile='Data not available';
}


			$scheduler_data[] = array('sr_no'=>$i,
			'mrnon'=>date('d-m-Y H:i:s',strtotime($restyui1->mrndoneOn)),
			'pono'=>$restyui1->pono,
			'vendorname'=>$vendorname,
			'itemname'=>$itemname,
			'itemtype'=>$type,
			'fincode'=>$fincode,
			'specialization'=>$specification,
		    //'previousinwarded'=>$prevqty.' '.$unival,
			//'reqty'=>$originalleftqty.' '.$unival,
			'recqty'=>$recvqty,
			'gateentry'=>$gateentry,
			'billno'=>$billno,
			'approved'=>$approved,
			'reject'=>$reject,
			'rejremarks'=>$rejectremarks,
			'rejectimage'=>$rejectfile);
			$i++;
			}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);

}

function storereciept()
{
	$rack='';
	$scheduler_data = array();
			$rest=$this->db->select('a.*,a.record_id,c.mrndoneOn,c.unit')->from('mrn_history_view a')->join('mrn_view c','a.record_id=c.id')->where('a.storereciept','0')->group_by('a.id')->order_by('a.id','DESC')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
			$podetail=$this->getpodetails($restyui1->po_no);
			
			if(count($podetail)>0)
			{
			$potype=$podetail['potype'];
			$sources=$podetail['source'];
			$sourceid=$podetail['sourceid'];
			$unit=$podetail['unit'];
			$pr=$podetail['prno'];
			
			}else
			{
			
			$potype='';
			$sources='';
			$sourceid='';
			$unit='';
			$pr;
			
			}
				
				if($potype==0)
				{
				$itemname=$this->getmachineitemname($restyui1->itemid);
				$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
				if(count($otherdetails)>0)
				{
			
				$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
					$rack=$otherdetails['rack_location'];
					
				}else{
					$fincode='';
					$specification='';
				}
				}else{
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
				}
				
				$jobcards=$this->getjobcardid($restyui1->record_id);
				
				if($sources=='1')
				{
				    $jobcard=$this->getjobcardno($jobcards);
				    $source='Jobcard - '.$jobcard;
				    
				}else if($sources=='2')
				{
				    $sourceid=$this->getsourceid($pr);
				      $source='INDENT IND-'.$sourceid;
				      
				}else if($sources=='3')
				{
				    
				      $source='IMS AUTO PR';
				}else if($sources==4)
				{
					$source="AUTO PR AGAINST BLOCKAGE";
				}else
				{
					$source='';
				}
				
				
			
				$uname=$this->getunit($restyui1->unit);
				
				if(floatval($restyui1->accept_qty)>0)
				{
					$shortmaterial="<a href='javascript:void(0)' onclick='showModalreject_sales(".$restyui1->id.','.$restyui1->accept_qty.','.$restyui1->itemid.")'><span class='btn btn-success btn-xs'>Short Material</button></span>";

	/** PENDING SINCE **/

		$cdate=new DateTime($restyui1->added_on);
$tday=new DateTime(date('Y-m-d'));
$difference = $cdate->diff($tday);
			/** END **/
		

			$scheduler_data[] = array('check'=>'<span class="recv'.$i.'"><input type="checkbox" class="checkitems" name="mrnid[]" id="checkthis'.$restyui1->id.'" value="'.$restyui1->id.'">
			<input type="hidden" name="qty'.$restyui1->id.'" value="'.$restyui1->accept_qty.'"><input type="hidden" name="potype'.$restyui1->id.'" value="'.$potype.'">
			<input type="hidden" name="itemid'.$restyui1->id.'" value="'.$restyui1->itemid.'"></span>',

		


			'sr_no'=>$i,
			'mrnon'=>date('d-M-Y H:i:s',strtotime($restyui1->mrndoneOn)),
			'pendingsince'=>"<strong style='color:red'>".$difference->d.' Days'."</strong>",
			'ponumber'=>$restyui1->po_no,
			'source'=>$source,
			'racklocation'=>$rack,
			'item'=>$itemname,
			'fincode'=>$fincode,
			'shortmaterial'=>$shortmaterial,
			'specialization'=>$specification,
			'qty'=>floatval($restyui1->accept_qty)." ".$uname);
				
			$i++;
			}
			}
			}
			
			
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
		
	
	
}

function historystorereciept()
{
		
	
	
	$date = date('Y-m-d');
	$date = strtotime($date);
	$sdate = date('Y-m-d',strtotime("-180 day", $date)).' 00:00:00';
	
	
		

			
	$rack='';
	$scheduler_data = array();
			$this->db->select('a.*,c.mrndoneOn,c.poid,c.unit')->from('mrn_history_view a')->join('mrn_view c','a.record_id=c.id')->where('storereciept','1')->where('a.added_on>=',$sdate)->order_by('a.id','DESC')->group_by('a.id');


			$rest=$this->db->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
				$res=$this->db->select('id,purchase_remarks')->from('mrn_short_received')->where('mrn_id',$restyui1->record_id)->where('purchase_remarks !=','')->get();
			if($res->num_rows() > 0)
			{
				foreach($res->result() as $mrnshort);
				$shortrecevied='YES';
				$accountsrmk=$mrnshort->purchase_remarks;
			}else
			{
				$shortrecevied='NO';
				$accountsrmk='';	
			}
				
				$podetail=$this->getpodetails($restyui1->po_no);
			
			if(count($podetail)>0)
			{
			$potype=$podetail['potype'];
			$sources=$podetail['source'];
			$sourceid=$podetail['sourceid'];
			$unit=$podetail['unit'];
			$pr=$podetail['prno'];
			
			}else
			{
			
			$potype='';
			$sources='';
			$sourceid='';
			$unit='';
			$pr='';
			
			}
				
				
				
				
				if($potype==0)
				{
				$itemname=$this->getmachineitemname($restyui1->itemid);
				$otherdetails=$this->getmachineotherdetails($restyui1->itemid);
				if(count($otherdetails)>0)
				{
			
				$fincode=$otherdetails['fincode'];
					$specification=$otherdetails['specialization'];
					$rack=$otherdetails['rack_location'];
				}else{
					$fincode='';
					$specification='';
				}
				}else{
					$itemname=$this->getgeneralitemname($restyui1->itemid);
					$fincode='';
					$specification='';
				}

				$storerevon=date('d-M-Y h:i:s',strtotime($restyui1->storerecvon));
				
				$storeby=$this->getusername($restyui1->storerecvby);
				
				$jobcards=$this->getjobcardid($restyui1->record_id);
				
					if($sources=='1')
				{
				    $jobcard=$this->getjobcardno($jobcards);
				    $source='Jobcard - '.$jobcard;
				    
				}else if($sources=='2')
				{
				     $sourceid=$this->getsourceid($pr);
				      $source='INDENT IND-'.$sourceid;
				      
				}else if($sources=='3')
				{
				    
				      $source='IMS AUTO PR';

				}else if($sources=='4')
				{
				    
				      $source='AUTO PR AGAINST BLOCKAGE';
				}
				
					
				
				$uname=$this->getunit($restyui1->unit);
			$scheduler_data[] = array('sr_no'=>$i,
				'mrnon'=>date('d-M-Y H:i:s',strtotime($restyui1->mrndoneOn)),
				'ponumber'=>$restyui1->po_no,
				'source'=>$source,
				'item'=>$itemname,
				'racklocation'=>$rack,
				'fincode'=>$fincode,
				'specialization'=>$specification,
				'qty'=>floatval($restyui1->accept_qty)." ".$uname,
				'recvon'=>$storerevon,
				'shortrecevied'=>$shortrecevied.'<br>'.$accountsrmk,
				'recvby'=>$storeby);
				

				


			$i++;
			}
			}
			
			
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
		
	
	
}

public function penalitydf(){
	$this->load->view('master/penalitydf');
}

public function machinereadyonfloor(){
	$this->load->view('master/machinereadyonfloor');
}
public function dispatchinnext15days(){
	$this->load->view('master/dispatchinnext15days');
}
public function fatsinnext15days(){
	$this->load->view('master/fatinnext15days');
}
public function paymentsinnext15days(){
	$this->load->view('master/paymentsinnext15days');
}
public function duetodaytasks(){
	$this->load->view('master/duetodaytasks');
}
public function paymentoverdue(){
	$this->load->view('customer/payment_overdue');
}
public function filterpaymentoverdue()
{
	$seg = $this->uri->segment(3);
	$comp = $this->input->post('company');
	$overdue = $this->input->post('overdue');

	if ($seg != '') {
		$u = $seg;
	} else {
		$u = "NA";
	}

	redirect(page_url . "Reporting/paymentoverdue/" . $u . '/' . $comp . '/' . $overdue);
}
}
