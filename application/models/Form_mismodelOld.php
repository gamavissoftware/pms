<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Form_mismodel extends CI_Model {

	public function __construct()
	{
		
		parent::__construct();
		$this->load->model('Fms_model', 'fmsmodel');
	}
	
	function getuserwisedashboard($userid)
	{
		$formtitle=array();
		$rest=$this->db->select('id')->from('dynamic_forms')->where('user_id',$userid)->where('status','1')->get();
		if($rest->num_rows()>0)
		{
			
			foreach($rest->result() as $rest1)
			{
				$formtitle[]=$rest1->id;
				
			}
			
		}
		
		return $formtitle;
	}
	
	function getdashboardname($formid)
	{
		
		$formid=$this->db->select('dashboard_title')->from('dynamic_forms')->where('id',$formid)->get();
		if($formid->num_rows()>0)
		{
			foreach($formid->result() as $formids);
			
			$formname=$formids->dashboard_title;
		return $formname;
		}else
		{
			echo "FORM NOT FOUND";exit;
		}
	}
	
	
	function getoverallformtask($formid)
	{
		$allformtask=$this->db->select('id')->from('dynamic_form_data')->where('form_id',$formid)->get();
		
		return $allformtask->num_rows();
		
	}
	
	
	function getallformtaskdone($formid)
	{
		$allformtask=$this->db->select('id')->from('dynamic_form_data')->where('form_id',$formid)->where('work_status','1')->get();
		
		return $allformtask->num_rows();
		
	}
	
	
	function worknotdonemisscore($totaldone,$totaloveralltask)
	{
		if($totaloveralltask==0)
		{
			return 0;
			
		}else{
			
			$worknotpercentage=($totaldone-$totaloveralltask)/$totaloveralltask;
			
			$worknotpercentagescore=$worknotpercentage*100;
			
			
			return round($worknotpercentagescore,2);
			
			
		}
		
	}
	
	function formworknotdonetaskdetails($formid)
	{
		$taskdetail='';
		$allformtask=$this->db->select('id,responseid,planned_date')->from('dynamic_form_data')->where('form_id',$formid)->where('work_status','0')->get();
		if($allformtask->num_rows()>0)
		{
			foreach($allformtask->result() as $allformtask1)
			{
				$taskdetail.="RESPONSE ID# ".strtoupper($allformtask1->responseid)."-PT -".date('d/m/y g:i A',strtotime($allformtask1->planned_date)).'<br/>';
			}
			
			
			return $taskdetail;
			
		}
		
		
	}
	
	
	function weeklytaskdone($startdate,$enddate,$formid)
	{
		$startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		
		$restdone=$this->db->select('a.id')->from('dynamic_form_data a')->where('a.work_status','1')->where('a.added_on BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('a.form_id',$formid)->get();
		$totaltaskdone=$restdone->num_rows();
		return $totaltaskdone;
	}
	
	
	function weeklytasknotdoneontime($startdate,$enddate,$formid)
	{
		$startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		
		$restdone=$this->db->select('a.id')->from('dynamic_form_data a')->where('a.work_status','1')->where('a.added_on BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('a.form_id',$formid)->where('a.task_complition_time<=a.planned_date')->get();
		$totaltaskdone=$restdone->num_rows();
		return $totaltaskdone;
	}
	
	/** END **/
	
	function worknotdoneontimemisscore($totaldone,$totaloveralltask)
	{
		if($totaloveralltask==0)
		{
			return 0;
			
		}else{
			if($totaldone==$totaloveralltask)
			{
				$worknotpercentage=(0-$totaldone)/$totaloveralltask;
			
			$worknotpercentagescore=$worknotpercentage*100;
			
			
			return round($worknotpercentagescore,2);
			
			}else
			{
			$worknotpercentage=($totaldone-$totaloveralltask)/$totaloveralltask;
			
			$worknotpercentagescore=$worknotpercentage*100;
			
			
			return round($worknotpercentagescore,2);
			}
			
			
		}
		
	}
	
	
	function formworknotdoneontimetaskdetails($startdate,$enddate,$formid)
	{
		$startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		$taskdetail='';
		$restdone=$this->db->select('a.id,a.responseid,planned_date,a.task_complition_time')->from('dynamic_form_data a')->where('a.work_status','1')->where('a.added_on BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('a.form_id',$formid)->where('a.task_complition_time>a.planned_date')->get();
		$totaltaskdone=$restdone->num_rows();
		if($totaltaskdone>0)
		{
		
		foreach($restdone->result() as $allformtask1)
			{
				$taskdetail.="RESPONSE ID# ".strtoupper($allformtask1->responseid)."-PT -".date('d/m/y g:i A',strtotime($allformtask1->planned_date)).'<br/>';
			}
			
		}
			
			
			return $taskdetail;
	}
	
	function delaycalculationinworknotdonrontimemiscore($startdate,$enddate,$formid)
	{
		$startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		$query = $this->db->select('tatdays,tattime')->from('dynamic_forms')->where('id',$formid)->get();
	if($query->num_rows()>0){
		foreach($query->result() as $row);
		$totaldays = $row->tatdays;
		$totaltime = $row->tattime;
	
	$restdone=$this->db->select('a.id,a.responseid,a.planned_date,a.added_on,a.task_complition_time')->from('dynamic_form_data a')->where('a.work_status','1')->where('a.added_on BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('a.form_id',$formid)->where('a.task_complition_time>a.planned_date')->get();
		$totaltaskdone=$restdone->num_rows();

		if($totaltaskdone>0)
		{
		
		foreach($restdone->result() as $allformtask1)
			{
				$start=strtotime($allformtask1->added_on);
				$end=strtotime($allformtask1->planned_date);
				$doneon=strtotime($allformtask1->task_complition_time);
				$plannedOn=strtotime($allformtask1->planned_date);
				$totaltatdelaymin=round(abs($doneon-$plannedOn) / 60,2);
				$totaldelayinhours=$totaltatdelaymin/60;
				
				$totaltatinmin=round(abs($start - $end) / 60,2);
				$totaltatinhours=($totaltatinmin)*(-1)/60;
								
				$delayscore=round($totaldelayinhours/$totaltatinhours,2);
				
				$oddetail[]=$delayscore;
				
			
			}
			return array_sum($oddetail);
			
		}else{
			
			return 0;
		}

	
	}
	
	
	}
	
	
	
	
	
	function checkifformisassigned($userid)
	{
		$formtitle=array();
		$rest=$this->db->select('id')->from('dynamic_forms')->where('user_id',$userid)->where('status','1')->get();
		return $rest->num_rows();
		
	}
	
	function checkifchecklistassigned($userid)
	{
		$rest=$this->db->select('task_id')->from('compliance_task_report')->where('user_id',$userid)->get();
		return $rest->num_rows();
		
	}
	
	
	function getallchecklistassigned($userid)
	{
		$checklist=array();
		$rest=$this->db->select('task_id,task')->from('compliance_task_report')->where('user_id',$userid)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $rest)
			{
				$checklist[]=$rest->task_id;
			}
			return $checklist;
		}else{
			return $checklist;
		}
		
		
	}
	
	function getchecklistname($checklistid)
	{
		
		$rest=$this->db->select('task')->from('compliance_task_report')->where('task_id',$checklistid)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $rest);
			
			$checklist=$rest->task;
			return $checklist;
		}
		
	}
	
	function checklistalltaskassigned($userid,$startdate,$enddate)
	{
			$tatarray = array();
			$lastWeek = array();
			$prevMon = abs(strtotime("this monday"));
			$currentDate = abs(strtotime("today"));
			$seconds = 86400; //86400 seconds in a day
			$dayDiff = ceil( ($currentDate-$prevMon)/$seconds ); 
			if( $dayDiff < 7 )
			{
			$dayDiff += 1; //if it's monday the difference will be 0, thus add 1 to it
			$prevMon = strtotime( "previous monday", strtotime("-$dayDiff day") );
			}
			$prevMon = date("Y-m-d",$prevMon);

			// create the dates from Monday to Sunday
			for($i=0; $i<7; $i++)
			{
			$d = date("Y-m-d", strtotime( $prevMon." + $i day") );
			$lastWeek[]=$d;
			}
			$current_monday = $lastWeek[0];
			$current_saturday = $lastWeek[5];
	
		
				           $date = date('Y-m-d');
						   $this->db->distinct();
				           $this->db->select('task_id, tat_id,company_id')->from('compliance_task_report')->where('user_id',$userid);
						   $query = $this->db->get();
				           $res = $query->result();
						   if($query->num_rows()>0){
				           foreach($res as $row){
				               $tatarray[] = $row->task_id;
				           }
						   }
			
			$query = $this->db->select('id')->from('checklist_done_notdone')->where_in('task_id',$tatarray)->where('task_date BETWEEN "'. $current_monday. '" and "'.$current_saturday.'"')->get();
						$res = count($query->result());
			
			
			return $res;
			
		}
		
				
		
	
	
	
	function checklistalltaskassigneddone($userid,$startdate,$enddate)
	{
			$tatarray = array();
			$lastWeek = array();
			$prevMon = abs(strtotime("this monday"));
			$currentDate = abs(strtotime("today"));
			$seconds = 86400; //86400 seconds in a day
			$dayDiff = ceil( ($currentDate-$prevMon)/$seconds ); 
			if( $dayDiff < 7 )
			{
			$dayDiff += 1; //if it's monday the difference will be 0, thus add 1 to it
			$prevMon = strtotime( "previous monday", strtotime("-$dayDiff day") );
			}
			$prevMon = date("Y-m-d",$prevMon);

			// create the dates from Monday to Sunday
			for($i=0; $i<7; $i++)
			{
			$d = date("Y-m-d", strtotime( $prevMon." + $i day") );
			$lastWeek[]=$d;
			}
			$current_monday = $lastWeek[0];
			$current_saturday = $lastWeek[5];
	
		
				           $date = date('Y-m-d');
						   $this->db->distinct();
				           $this->db->select('task_id, tat_id,company_id')->from('compliance_task_report')->where('user_id',$userid);
						   $query = $this->db->get();
				           $res = $query->result();
						   if($query->num_rows()>0){
				           foreach($res as $row){
				               $tatarray[] = $row->task_id;
				           }
						   }
			
			$query = $this->db->select('id')->from('checklist_done_notdone')->where_in('task_id',$tatarray)->where('task_date BETWEEN "'. $current_monday. '" and "'.$current_saturday.'"')->where('status','1')->get();
						$res = count($query->result());
			
			
			return $res;
			
		}
		
		
		function worknotdonemisscoreforchecklist($percentslab,$totaldone,$totaloveralltask)
	{
		if($totaloveralltask==0)
		{
			return 0;
			
		}else{
			
			$worknotpercentage=($totaldone-$totaloveralltask)/$totaloveralltask;
			
			$worknotpercentagescore=$worknotpercentage*$percentslab;
			
			
			return round($worknotpercentagescore,2);
			
			
		}
		
	}
	
	
}