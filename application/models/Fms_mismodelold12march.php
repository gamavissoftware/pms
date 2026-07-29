<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fms_mismodel extends CI_Model {

	public function __construct()
	{
		
		parent::__construct();
$this->load->model('Fms_model', 'fmsmodel');
		$this->load->model('Form_mismodel');
		$this->load->model('Delegation_model');
	}

function getallproductionflowformis()
{
	$restyu=$this->db->select('id')->from('production_flow')->where('mis','1')->get();
	if($restyu->num_rows()>0)
	{
		foreach($restyu->result() as $resty1)
		{
			$pflow[]=$resty1->id;
		}
		
		$availableflowstages = "'" . implode ( "', '", $pflow) . "'";
		
		return 	$availableflowstages;
		
		
	}else{
		
		$availableflowstages=0;
		return 	$availableflowstages;
	}
	
	
}
	function getallfmsuser($allproductionflow)
	{
		$alluser=array();
		$rest=$this->db->select('b.user_id')->from('fms_flow a')->join('system_users b','a.who_wedo=b.user_id')->group_by('a.who_wedo')->where_in('a.production_flow_id',$allproductionflow,false)->order_by('b.first_name','ASC')->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $rest1)
			{
				$alluser[]=$rest1->user_id; 
			}
			
			
			return $alluser;
		}else
		{
			return $alluser;
		}
		
	}
	
	
	
	function getsusername($userid)
	{
		$rest=$this->db->select('first_name,last_name')->from('system_users')->where('user_id',$userid)->order_by('first_name','ASC')->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $rest1)
			{
				//$alluser[]=$rest1->first_name." ".$rest1->last_name;
				$alluser[]=strtoupper($rest1->first_name);
			}
			
			
			return $alluser;
		}else
		{
			return $alluser;
		}
		
	}
	
	
	function getsuserwisefms($userid,$allproductionflow)
	{
		$alluser=array();
		$rest=$this->db->select('flow_id')->from('fms_flow a')->where('who_wedo',$userid)->where_in('production_flow_id',$allproductionflow,false)->order_by('production_flow_id','ASC')->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $rest1)
			{
				$alluser[]=strtoupper($rest1->flow_id);
			}
			
			
			return $alluser;
		}else
		{
			return $alluser;
		}
		
	}
	
	function getfmsname($flowid)
	{
		$alluser='';
		$rest=$this->db->select('fms_flow,setorder')->from('fms_flow')->where('flow_id',$flowid)->order_by('setorder','ASC')->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $rest1);
				$alluser=strtoupper("P".$rest1->setorder.". ".$rest1->fms_flow);
			
			
			
			return $alluser;
		}else
		{
			return $alluser;
		}
		
	}
	
	
	function worknotdonemisfortotaltaskoverall($startdate,$enddate,$flowids,$allusers)
	{
		$startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		/** Get Total task during this period **/
		$resttotal=$this->db->select('id')->from('order_stage')->where('flowstage',$flowids)->get();
		$totaltaskoverall=$resttotal->num_rows();
		return $totaltaskoverall;
		/** End **/
		
	}
	
	
	function worknotdonemisfortotaltaskweekly($startdate,$enddate,$flowids,$allusers)
	{
		$startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		/** Get Total task during this period **/
		$resttotal=$this->db->select('id')->from('fmstatdate')->where('tasktat BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('flowstage',$flowids)->get();
		$totaltaskoverall=$resttotal->num_rows();
		return $totaltaskoverall;
		/** End **/
		
	}
	
	
	
	function worknotdonemisfordonetask($startdate,$enddate,$flowids,$allusers)
	{
		$startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		/** Get Total task done during this period **/
		$restdone=$this->db->select('a.id')->from('order_stage a')->where('a.flowstage',$flowids)->where('a.userstatus','1')->get();
		$totaltaskdone=$restdone->num_rows();
		return $totaltaskdone;
		/** End **/
		
	}
	
	function worknotdonemisdetailsfordonenotdone($startdate,$enddate,$flowids,$allusers)
	{
		$oddetail='';
		$startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		/** Get Total task done during this period **/
		$restdone=$this->db->select('a.id as orderstageid,a.jobcardid,c.job_card_no,d.plannedOn')->from('order_stage a')->join('order_instruments c','a.jobcardid=c.id')->join('order_planning d','c.id=d.jobcard_id')->where('a.flowstage',$flowids)->where('a.userstatus','0')->get();
		if($restdone->num_rows()>0)
		{
			foreach($restdone->result() as $restdone1)
			{
				$QRY = $this->db->select('total_days, tat, setorder')->from('fms_flow')->where('flow_id',$flowids)->get();
				$res = $QRY->result();
				foreach($res as $fmsinformation);
				$totaldaysslave = $fmsinformation->total_days;
				$tatstage = $fmsinformation->tat;
				$setorder=$fmsinformation->setorder;
			
				  $timestamp=$this->fmsmodel->getprevioustimestamp($tatstage,$restdone1->plannedOn,$restdone1->jobcardid,$flowids,$restdone1->orderstageid);
				  //echo $timestamp;exit;
			    $tatdate=$this->fmsmodel->getupcomingtatdate($tatstage,$restdone1->plannedOn,$restdone1->jobcardid,$timestamp,$totaldaysslave,$restdone1->orderstageid);
				$tattime = DateTime::createFromFormat('d-M-Y g:i A', $timestamp);
				$finaltattime=$tattime->format('g:i A');
				
				$taskplannedOn=$tatdate." ".$finaltattime;
				  
				$oddetail.="ORDER# ".$restdone1->job_card_no." -PT -".date('d/m/y g:i:A',strtotime($taskplannedOn))."<br/>";
			}
			
			return $oddetail;
		}else
		{
			return $oddetail;
		}
		
		/** End **/
	
		
	}
	
	
	function weeklyoveralldonetask($startdate,$enddate,$flowids,$allusers)
	{
		$startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		/** Get Total task done during this period **/
		//$restdone=$this->db->select('a.id')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->where('a.addedOn BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('a.flowstage',$flowids)->where('a.userstatus','1')->get();
		$restdone=$this->db->select('a.id')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->where('a.addedOn BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('a.flowstage',$flowids)->where('a.userstatus','1')->get();
		$totaltaskdone=$restdone->num_rows();
		return $totaltaskdone;
		/** End **/
		
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
	
	
	function worknotdoneontimemisscore($totaldone,$totaloveralltask)
	{
		if($totaloveralltask==0)
		{
			return 0;
			
		}else{
			
			if($totaldone!=0)
			{
			$worknotpercentage=($totaldone-$totaloveralltask)/$totaloveralltask;
			
			$worknotpercentagescore=$worknotpercentage*100;
			
			
			return round($worknotpercentagescore,2);
			}else
			{
			return 0;	
			}
			
			
		}
		
	}
	
	
	function weeklytasknotdoneontime($startdate,$enddate,$flowids,$allusers)
	{
		$startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		/** Get Total task done during this period **/
		//$restdone=$this->db->select('a.id,a.addedOn,b.tasktat')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->where('a.addedOn BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('a.flowstage',$flowids)->where('a.userstatus','1')->where('a.addedOn>b.tasktat')->get();
				$restdone=$this->db->select('a.id,a.addedOn,b.tasktat')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->where('a.addedOn BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('a.flowstage',$flowids)->where('a.userstatus','1')->where('a.addedOn<=b.tasktat')->get();
		$totaltaskdone=$restdone->num_rows();
		return $totaltaskdone;
		/** End **/
		
	}
	


function weeklytasknotdoneontimedetails($startdate,$enddate,$flowids,$allusers)
	{
		$oddetail='';
		$startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		/** Get Total task done during this period **/
		$restdone=$this->db->select('a.id as orderstageid,a.jobcardid,c.job_card_no,d.plannedOn')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->join('order_instruments c','a.jobcardid=c.id')->join('order_planning d','c.id=d.jobcard_id')->where('a.addedOn BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('a.flowstage',$flowids)->where('a.userstatus','1')->where('a.addedOn>b.tasktat')->get();
		if($restdone->num_rows()>0)
		{
			foreach($restdone->result() as $restdone1)
			{
			$QRY = $this->db->select('total_days, tat, setorder')->from('fms_flow')->where('flow_id',$flowids)->get();
				$res = $QRY->result();
				foreach($res as $fmsinformation);
				$totaldaysslave = $fmsinformation->total_days;
				$tatstage = $fmsinformation->tat;
				$setorder=$fmsinformation->setorder;
				
				$timestamp=$this->fmsmodel->getprevioustimestamp($tatstage,$restdone1->plannedOn,$restdone1->jobcardid,$flowids,$restdone1->orderstageid);
				$tatdate=$this->fmsmodel->getupcomingtatdate($tatstage,$restdone1->plannedOn,$restdone1->jobcardid,$timestamp,$totaldaysslave,$restdone1->orderstageid);
				$tattime = DateTime::createFromFormat('d-M-Y g:i A', $timestamp);
				$finaltattime=$tattime->format('g:i A');
				
				$taskplannedOn=$tatdate." ".$finaltattime;
				$oddetail.="ORDER# ".$restdone1->job_card_no." -PT -".date('d/m/y g:i A',strtotime($taskplannedOn))."<br/>";
			}
			
			return $oddetail;
		}else
		{
			return $oddetail;
		}
		/** End **/
		
		
		
	}
	
	
	
	function weeklydifferenceintimedetails($startdate,$enddate,$flowids,$allusers)
	{
		$oddetail=array();
		/** Get flow TAT **/
		$QRY = $this->db->select('tat,')->from('fms_flow')->where('flow_id',$flowids)->get();
			$res = $QRY->result();
			foreach($res as $fmsinformation);
			$tatstage = $fmsinformation->tat;
		/** End **/
		
		$startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		/** Get Total task done during this period **/
		$restdone=$this->db->select('a.id as orderstageid,a.jobcardid,c.job_card_no,a.addedOn,b.tasktat,d.plannedOn')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->join('order_instruments c','a.jobcardid=c.id')->join('order_planning d','c.id=d.jobcard_id')->where('a.addedOn BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('a.flowstage',$flowids)->where('a.userstatus','1')->where('a.addedOn>b.tasktat')->get();
		if($restdone->num_rows()>0)
		{
			foreach($restdone->result() as $restdone1)
			{
				
				$timestamp=$this->fmsmodel->getprevioustimestamp($tatstage,$restdone1->plannedOn,$restdone1->jobcardid,$flowids,$restdone1->orderstageid);
				
				$starttime=date('Y-m-d H:i',strtotime($timestamp));
				$planneddeadline=date('Y-m-d H:i',strtotime($restdone1->tasktat));
				$actualcompletedon=date('Y-m-d H:i',strtotime($restdone1->addedOn));
				
				$start=strtotime($starttime);
				$end=strtotime($planneddeadline);
				
				$actualcomplete=strtotime($actualcompletedon);
				
				
				$totaltatdelaymin=round(abs($actualcomplete-$end) / 60,2);
				$totaldelayinhours=$totaltatdelaymin/60;
				
				$totaltatinmin=round(abs($start - $end) / 60,2);
				$totaltatinhours=($totaltatinmin)*(-1)/60;
				
				
				$delayscore=round($totaldelayinhours/$totaltatinhours,2);
				
				$oddetail[]=$delayscore;
			}
			
			return array_sum($oddetail);
		}else
		{
			return 0;
		}
		/** End **/
		
		
		
	}
	
	
	
/*** HERE START OVERALL MIS FOR FMS FUNCTIONS**/
	function fmscurrentweekscore($user_id)
	{
		$currentweekscore=array();
		
		/** This WEEK MONDAY & SAT **/
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
			/** End **/
			$allproductionflow= $this->getallproductionflowformis();
			if($allproductionflow=='0')
			{
			echo "NO PRODUCTION FLOEW INCLUDED IN MIS";EXIT;
			}
			
			$getflowidforuser=$this->fmsmodel->flowassignedtouser($user_id,$allproductionflow);
			if(count($getflowidforuser)>0)
			{
				$startdate=$current_monday;
				$enddate=$current_saturday;
				$allusers=$user_id;
				/** FMS MIS **/
			foreach($getflowidforuser as $flowids)
			{
			/** Part one **/
			$worknotdonemisfortotaltaskoverall=$this->worknotdonemisfortotaltaskoverall($startdate,$enddate,$flowids,$allusers);

			$worknotdonemisfordonetask=$this->worknotdonemisfordonetask($startdate,$enddate,$flowids,$allusers);

			$diff=$worknotdonemisfordonetask-$worknotdonemisfortotaltaskoverall;
			$worknotdonemisscore=$this->worknotdonemisscore($worknotdonemisfordonetask,$worknotdonemisfortotaltaskoverall);
			/** End **/
			
			/** Part Two **/
			$weeklyoveralldonetask=$this->weeklyoveralldonetask($startdate,$enddate,$flowids,$allusers);
			$weeklytasknotdoneontime=$this->weeklytasknotdoneontime($startdate,$enddate,$flowids,$allusers);
			$diff2=$weeklytasknotdoneontime-$weeklyoveralldonetask;
			$worknotdoneontimemisscore=$this->worknotdonemisscore($weeklytasknotdoneontime,$weeklyoveralldonetask);
			/** End **/
			
			/** START THIRD PART **/
			$weeklydifferenceintimedetailsmiscore=$this->weeklydifferenceintimedetails($startdate,$enddate,$flowids,$allusers);
			
			$currentweekscore[]=($worknotdonemisscore)+($worknotdoneontimemisscore)+($weeklydifferenceintimedetailsmiscore);
			}

			$currentweeklyscore=array_sum($currentweekscore);
			return $currentweeklyscore;
			/** END **/
			
			}else
			{

			$currentweeklyscore='0';	
			return 	$currentweeklyscore;		
			}
		
	}
	
	
	
	function fmspreviousweekscore($user_id)
	{
		$currentweekscore=array();
		
		/** This WEEK MONDAY & SAT **/
			$lastWeek = array();
			$prevMon = abs(strtotime("previous monday"));
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
			/** End **/
			$allproductionflow= $this->getallproductionflowformis();
			if($allproductionflow=='0')
			{
			echo "NO PRODUCTION FLOEW INCLUDED IN MIS";EXIT;
			}
			
			$getflowidforuser=$this->fmsmodel->flowassignedtouser($user_id,$allproductionflow);
			if(count($getflowidforuser)>0)
			{
				$startdate=$current_monday;
				$enddate=$current_saturday;
				$allusers=$user_id;
				/** FMS MIS **/
			foreach($getflowidforuser as $flowids)
			{
			/** Part one **/
			$worknotdonemisfortotaltaskoverall=$this->worknotdonemisfortotaltaskoverall($startdate,$enddate,$flowids,$allusers);

			$worknotdonemisfordonetask=$this->worknotdonemisfordonetask($startdate,$enddate,$flowids,$allusers);

			$diff=$worknotdonemisfordonetask-$worknotdonemisfortotaltaskoverall;
			$worknotdonemisscore=$this->worknotdonemisscore($worknotdonemisfordonetask,$worknotdonemisfortotaltaskoverall);
			/** End **/
			
			/** Part Two **/
			$weeklyoveralldonetask=$this->weeklyoveralldonetask($startdate,$enddate,$flowids,$allusers);
			$weeklytasknotdoneontime=$this->weeklytasknotdoneontime($startdate,$enddate,$flowids,$allusers);
			$diff2=$weeklytasknotdoneontime-$weeklyoveralldonetask;
			$worknotdoneontimemisscore=$this->worknotdonemisscore($weeklytasknotdoneontime,$weeklyoveralldonetask);
			/** End **/
			
			/** START THIRD PART **/
			$weeklydifferenceintimedetailsmiscore=$this->weeklydifferenceintimedetails($startdate,$enddate,$flowids,$allusers);
			
			$currentweekscore[]=($worknotdonemisscore)+($worknotdoneontimemisscore)+($weeklydifferenceintimedetailsmiscore);
			}

			$currentweeklyscore=array_sum($currentweekscore);
			return $currentweeklyscore;
			/** END **/
			
			}else
			{

			$currentweeklyscore='0';	
			return 	$currentweeklyscore;		
			}
		
	}
	
	
	function checklistcurrentweekmis($userid)
	{
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
	
	  $startdate=$current_monday;
	  $enddate=$current_saturday;
	
		 $checklistalltaskassigned=$this->Form_mismodel->checklistalltaskassigned($userid,$startdate,$enddate);
		
		 $checklistalltaskassigneddone=$this->Form_mismodel->checklistalltaskassigneddone($userid,$startdate,$enddate);
		if($checklistalltaskassigneddone<>0)
		 {
		 $checlistdiff=$checklistalltaskassigneddone-$checklistalltaskassigned;
		 }else{
			 
			 $checlistdiff=0-$checklistalltaskassigned;
		 }
		 
		  $checklistalltaskassigneddonemisscore=$this->Form_mismodel->worknotdonemisscoreforchecklist('100',$checklistalltaskassigneddone,$checklistalltaskassigned);
		  
		  return $checklistalltaskassigneddonemisscore;
		
	}
	
	
	function checklistpreviousweekmis($userid)
	{
		
		$lastWeek = array();
$prevMon = abs(strtotime("previous monday"));
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
	
	  $startdate=$current_monday;
	  $enddate=$current_saturday;
	  
	 
	  
		 $checklistalltaskassigned=$this->Form_mismodel->checklistalltaskassigned($userid,$startdate,$enddate);
		
		
		 $checklistalltaskassigneddone=$this->Form_mismodel->checklistalltaskassigneddone($userid,$startdate,$enddate);
		
		if($checklistalltaskassigneddone<>0)
		 {
		 $checlistdiff=$checklistalltaskassigneddone-$checklistalltaskassigned;
		 }else{
			 
			 $checlistdiff=0-$checklistalltaskassigned;
		 }
		 
		
		  $checklistalltaskassigneddonemisscore=$this->Form_mismodel->worknotdonemisscoreforchecklist('100',$checklistalltaskassigneddone,$checklistalltaskassigned);
	
		  return $checklistalltaskassigneddonemisscore;
		
	}
	
	function getpreviousweekalldates()
	{
		
		/** This WEEK MONDAY & SAT **/
			$lastWeek = array();
			$prevMon = abs(strtotime("previous monday"));
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
		
		
		return $lastWeek;
	}
	
	
	function getcurrentweekalldates()
	{
		
		/** This WEEK MONDAY & SAT **/
			$lastWeek = array();
			$prevMon = abs(strtotime("this monday"));
			$currentDate = abs(strtotime("today"));
			$seconds = 86400; //86400 seconds in a day
			$dayDiff = ceil( ($currentDate-$prevMon)/$seconds );
			if( $dayDiff < 7 )
			{
			$dayDiff += 1; //if it's monday the difference will be 0, thus add 1 to it
			$prevMon = strtotime( "this monday", strtotime("-$dayDiff day") );
			}
			$prevMon = date("Y-m-d",$prevMon);

			// create the dates from Monday to Sunday
			for($i=0; $i<7; $i++)
			{
			$d = date("Y-m-d", strtotime( $prevMon." + $i day") );
			$lastWeek[]=$d;
			}
		
		
		return $lastWeek;
	}
	/** END **/
	
	
	function getallnonfmsuser()
	{
		$alluser=array();
		$alluser1=array();
		$rest=$this->db->select('b.user_id')->from('fms_flow a')->join('system_users b','a.who_wedo=b.user_id')->group_by('a.who_wedo')->order_by('b.first_name','ASC')->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $rest1)
			{
				$alluser[]=$rest1->user_id; 
			}
			
		}
		
		$this->db->select('user_id')->from('system_users');
		if(count($alluser)>0)
		{
			$allfmsuser="'" . implode ( "', '", $alluser ) . "'";
		$this->db->where_not_in('user_id',$alluser);
		}
		$this->db->where('user_status','1');
		$this->db->where('hide_profile','0');
		$rest11=$this->db->get();
		
		if($rest11->num_rows()>0)
		{
			foreach($rest11->result() as $rest12)
			{
				$alluser1[]=$rest12->user_id; 
			}
			
			return $alluser1;
		}else{ return $alluser1;   }
		
		
	}
	
	
	function delegationcurrentweekmis($allusers)
	{
		$delegationmiscore=0;
		
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

		$startdate=$current_monday;
		$enddate=$current_saturday;
	  
		$delegationlist=$this->Delegation_model->checkforalldelegationassigned($allusers,$startdate,$enddate);
		if(count($delegationlist)>0)
		{
		$actualdele=$this->Delegation_model->checkforalldelegationassignedmarkeddone($allusers,$startdate,$enddate);
	
$delegationdiff=$actualdele-array_sum($delegationlist);
$notdonetaskdetails=$this->Delegation_model->checkforalldelegationassignedmarkednotdone($allusers,$startdate,$enddate);
$delegationmiscore=$this->Delegation_model->delegationmiscore($allusers);

return $delegationmiscore;
		}else{
			
			return $delegationmiscore;
			
		}
		
		
		
	}
	

function delegationpreviousweekmis($allusers)
	{
		$delegationmiscore=0;
		
		$lastWeek = array();
		$prevMon = abs(strtotime("previous monday"));
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

		$startdate=$current_monday;
		$enddate=$current_saturday;
	  
		$delegationlist=$this->Delegation_model->checkforalldelegationassigned($allusers,$startdate,$enddate);
		if(count($delegationlist)>0)
		{
		$actualdele=$this->Delegation_model->checkforalldelegationassignedmarkeddone($allusers,$startdate,$enddate);
	
$delegationdiff=$actualdele-array_sum($delegationlist);
$notdonetaskdetails=$this->Delegation_model->checkforalldelegationassignedmarkednotdone($allusers,$startdate,$enddate);
$delegationmiscore=$this->Delegation_model->delegationmiscore($allusers);

return $delegationmiscore;
		}else{
			
			return $delegationmiscore;
			
		}
		
		
		
	}
	
function formcurrentweekmis($allusers)
{
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

$startdate=date('Y-m-d',strtotime($lastWeek[0]))." 00:00:00";
$enddate=date('Y-m-d',strtotime( $lastWeek[5]))." 23:59:59";
		
	 $formids=$this->Form_mismodel->getuserwisedashboard($allusers);
	 $useroverallmis=array();
	  if(count($formids)>0)
	  {
		  foreach($formids as $formid)
		  {
			$getalltaskcount=$this->Form_mismodel->getoverallformtask($formid);
			$getalltaskdonecount=$this->Form_mismodel->getallformtaskdone($formid);
			$formdiffpart1=$getalltaskdonecount-$getalltaskcount;
			$formworknotdonemisscore=$this->Form_mismodel->worknotdonemisscore($getalltaskdonecount,$getalltaskcount);
			$formworknotdonetaskdetails=$this->Form_mismodel->formworknotdonetaskdetails($formid);
			/** END PART ONE **/
			$weeklytaskdone=$this->Form_mismodel->weeklytaskdone($startdate,$enddate,$formid);
			$weeklytasknotdoneontime=$this->Form_mismodel->weeklytasknotdoneontime($startdate,$enddate,$formid);
			$formdiffpart2=$weeklytasknotdoneontime-$weeklytaskdone;
			$formworknotdoneontimemisscore=$this->Form_mismodel->worknotdonemisscore($weeklytasknotdoneontime,$weeklytaskdone);
			$formworknotdoneontimetaskdetails=$this->Form_mismodel->formworknotdoneontimetaskdetails($startdate,$enddate,$formid);
			
			/** END PART TWO **/
			 $delaycalculationinworknotdonrontimemiscore=$this->Form_mismodel->delaycalculationinworknotdonrontimemiscore($startdate,$enddate,$formid);
			
			$totalmisforuserforform=($formworknotdonemisscore)+($formworknotdoneontimemisscore)+($delaycalculationinworknotdonrontimemiscore);
	
	$useroverallmis[]=$totalmisforuserforform;
			  
			  
			  
			  
			  
			  
		  }
		  
		 if(count($useroverallmis)>0)
		 {
			 return array_sum($useroverallmis);
		 }else{  return 0;  }
		  
		  
	  }else{  return 0; }
	
	
	
	
}


function formpreviousweekmis($allusers)
{
	$lastWeek = array();
$prevMon = abs(strtotime("previous monday"));
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

$startdate=date('Y-m-d',strtotime($lastWeek[0]))." 00:00:00";
$enddate=date('Y-m-d',strtotime( $lastWeek[5]))." 23:59:59";
	 $formids=$this->Form_mismodel->getuserwisedashboard($allusers);
	 $useroverallmis=array();
	  if(count($formids)>0)
	  {
		  foreach($formids as $formid)
		  {
			$getalltaskcount=$this->Form_mismodel->getoverallformtask($formid);
			$getalltaskdonecount=$this->Form_mismodel->getallformtaskdone($formid);
			$formdiffpart1=$getalltaskdonecount-$getalltaskcount;
			$formworknotdonemisscore=$this->Form_mismodel->worknotdonemisscore($getalltaskdonecount,$getalltaskcount);
			$formworknotdonetaskdetails=$this->Form_mismodel->formworknotdonetaskdetails($formid);
			/** END PART ONE **/
			$weeklytaskdone=$this->Form_mismodel->weeklytaskdone($startdate,$enddate,$formid);
			$weeklytasknotdoneontime=$this->Form_mismodel->weeklytasknotdoneontime($startdate,$enddate,$formid);
			$formdiffpart2=$weeklytasknotdoneontime-$weeklytaskdone;
			$formworknotdoneontimemisscore=$this->Form_mismodel->worknotdonemisscore($weeklytasknotdoneontime,$weeklytaskdone);
			$formworknotdoneontimetaskdetails=$this->Form_mismodel->formworknotdoneontimetaskdetails($startdate,$enddate,$formid);
			
			/** END PART TWO **/
			 $delaycalculationinworknotdonrontimemiscore=$this->Form_mismodel->delaycalculationinworknotdonrontimemiscore($startdate,$enddate,$formid);
			
			$totalmisforuserforform=($formworknotdonemisscore)+($formworknotdoneontimemisscore)+($delaycalculationinworknotdonrontimemiscore);
	
	$useroverallmis[]=$totalmisforuserforform;
			  
			  
			  
			  
			  
			  
		  }
		  
		 if(count($useroverallmis)>0)
		 {
			 return array_sum($useroverallmis);
		 }else{  return 0;  }
		  
		  
	  }else{  return 0; }
	
	
	
	
}
	
}