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
	function getallfmsuser($allproductionflow,$uid)
	{
		$alluser=array();
		$this->db->select('b.user_id')->from('fms_flow a')->join('system_users b','a.who_wedo=b.user_id')->group_by('a.who_wedo')->where_in('a.production_flow_id',$allproductionflow,false)->order_by('b.first_name','ASC');
		if($uid<>'')
		{
		    $this->db->where('b.user_id',$uid);
		}
		
		$rest=$this->db->get();
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
		$totaltaskoverall=0;
		$markapp=array();
		$depen=$this->checkfordependency($flowids);
		$startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		/** Get Total task during this period **/
		if($depen==0)
		{
			
			
			$resttotal=$this->db->select('b.id')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->where('b.flowstage',$flowids)->where('b.tasktat<=',$enddate)->get();
		$totaltaskoverall=$resttotal->num_rows();
		return $totaltaskoverall;
		/** End **/
		}else{
			
			$depflow=$this->getdependentflows($flowids);
			
			if($flowids==5)
			{
			$ff='a.jobcardid';
			}else{
				$ff='a.jobcardid';
			}
				$resttotal=$this->db->select($ff.',a.id,a.orderid as originalorderid,b.tasktat')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->where('b.flowstage',$flowids)->where('b.tasktat<=',$enddate)->get();
			if($resttotal->num_rows()>0)
			{
				
				foreach($resttotal->result() as $resttotal1)
				{
					$availableflowstage=array();
					$plannedflowid=$this->checkforplanstartpoint($resttotal1->jobcardid);
					$plannedstageorderno=$this->fmsmodel->getplanstartsfromorderno($plannedflowid);
					$depflow=$this->getdependentflows($flowids);
					
				if(count($depflow)>0)
				{
						
					if($plannedstageorderno!=1)
					{
						$ifdependexist=$this->db->select("a.dependentflowid")->from('flowdependency a')->join('order_stage b','a.dependentflowid=b.flowstage')->where('b.jobcardid',$resttotal1->jobcardid)->where('a.flowid',$flowids)->get();
						if($ifdependexist->num_rows()>0)
						{
								foreach($ifdependexist->result() as $ifdependexist1)
								{
								$availableflowstage[]=$ifdependexist1->dependentflowid;
								}

						}
					}else
					{
								$ifdependexist=$this->db->select("a.dependentflowid")->from('flowdependency a')->where('a.flowid',$flowids)->get();
								if($ifdependexist->num_rows()>0)
								{
								foreach($ifdependexist->result() as $ifdependexist1)
								{
								$availableflowstage[]=$ifdependexist1->dependentflowid;
								}
								}
								
					}
					
					
					
					
					
					$checkskiiped=$this->fmsmodel->checkifdatahascomeskipped($resttotal1->jobcardid,$flowids);
					
					if(count($checkskiiped)>0)
					{
						$availableflowstage = array_diff($availableflowstage,$checkskiiped);
					}
					
					if(count($availableflowstage)>0)
			{	
				
				$availableflowstages = "'" . implode ( "', '", $availableflowstage ) . "'";
					if($flowids==5 && $resttotal1->jobcardid=='39')
					{
						$a="id";
					}else{
						$a="id";
					}
					
					$restyforcom=$this->db->select($a)->from('order_stage')->where_in('flowstage',$availableflowstages,false)->where('userstatus','1')->where('jobcardid',$resttotal1->jobcardid)->get();
					if($restyforcom->num_rows()==count($availableflowstage))
					{
						$markapp[]=1;
					}else{
						
						$markapp[]=0;
					}
					
			}else{
				
				$markapp[]=1;
				}
					
					
				
				}
					
			}
				
			}
			
	
			if(count($markapp)>0)
			{
				$totaltaskoverall=array_sum($markapp);
			}
			

	
			return $totaltaskoverall;
		}
		/** End **/
		
	}
	
	
	function worknotdonemisfortotaltaskoveralltmprenamebysaurabh27june2020($startdate,$enddate,$flowids,$allusers)
	{
		$startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		/** Get Total task during this period **/
	
		$resttotal=$this->db->select('a.id')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->where('a.flowstage',$flowids)->where('b.tasktat<=',$enddate)->get();
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
		/** $startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		$restdone=$this->db->select('a.id')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid
		')->where('a.flowstage',$flowids)->where('b.tasktat<=',$enddate)->where('a.userstatus','1')->get();
		$totaltaskdone=$restdone->num_rows();
		return $totaltaskdone; **/
		
		$totaltaskoverall=0;
		$markapp=array();
		$depen=$this->checkfordependency($flowids);
		$startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		/** Get Total task during this period **/
		if($depen==0)
		{
			
			
			$resttotal=$this->db->select('b.id')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->where('b.flowstage',$flowids)->where('b.tasktat<=',$enddate)->where('a.userstatus','1')->get();
		$totaltaskoverall=$resttotal->num_rows();
		return $totaltaskoverall;
		/** End **/
		}else{
			
			$depflow=$this->getdependentflows($flowids);
			
			if($flowids==5)
			{
			$ff='a.jobcardid';
			}else{
				$ff='a.jobcardid';
			}
				$resttotal=$this->db->select($ff.',a.id,a.orderid as originalorderid,b.tasktat')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->where('b.flowstage',$flowids)->where('b.tasktat<=',$enddate)->where('a.userstatus','1')->get();
			if($resttotal->num_rows()>0)
			{
				
				foreach($resttotal->result() as $resttotal1)
				{
					$availableflowstage=array();
					$plannedflowid=$this->checkforplanstartpoint($resttotal1->jobcardid);
					$plannedstageorderno=$this->fmsmodel->getplanstartsfromorderno($plannedflowid);
				
				if(count($depflow)>0)
				{
						
					if($plannedstageorderno!=1)
					{
						$ifdependexist=$this->db->select("a.dependentflowid")->from('flowdependency a')->join('order_stage b','a.dependentflowid=b.flowstage')->where('b.jobcardid',$resttotal1->jobcardid)->where('a.flowid',$flowids)->get();
						if($ifdependexist->num_rows()>0)
						{
								foreach($ifdependexist->result() as $ifdependexist1)
								{
								$availableflowstage[]=$ifdependexist1->dependentflowid;
								}

						}
					}else
					{
								$ifdependexist=$this->db->select("a.dependentflowid")->from('flowdependency a')->where('a.flowid',$flowids)->get();
								if($ifdependexist->num_rows()>0)
								{
								foreach($ifdependexist->result() as $ifdependexist1)
								{
								$availableflowstage[]=$ifdependexist1->dependentflowid;
								}
								}
								
					}
					
					
					
					
					
					$checkskiiped=$this->fmsmodel->checkifdatahascomeskipped($resttotal1->jobcardid,$flowids);
					
					if(count($checkskiiped)>0)
					{
						$availableflowstage = array_diff($availableflowstage,$checkskiiped);
					}
					
					if(count($availableflowstage)>0)
			{	
				
				$availableflowstages = "'" . implode ( "', '", $availableflowstage ) . "'";
					if($flowids==5 && $resttotal1->jobcardid=='558')
					{
						$a="id";
						echo $availableflowstages;exit;
					}else{
						$a="id";
					}
					
					$restyforcom=$this->db->select($a)->from('order_stage')->where_in('flowstage',$availableflowstages,false)->where('userstatus','1')->where('jobcardid',$resttotal1->jobcardid)->get();
					if($restyforcom->num_rows()==count($availableflowstage))
					{
						$markapp[]=1;
					}else{
						
						$markapp[]=0;
					}
					
			}else{
				
				$markapp[]=1;
				}
					
					
				
				}
					
			}
				
			}
			
	
			if(count($markapp)>0)
			{
				$totaltaskoverall=array_sum($markapp);
			}
			

	
			return $totaltaskoverall;
		}
		/** End **/

		
	}
	
	
	function worknotdonemisfordonetasktemprenamesaurabh27june2020($startdate,$enddate,$flowids,$allusers)
	{
		$startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		/** Get Total task done during this period **/
		$restdone=$this->db->select('a.id')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid
		')->where('a.flowstage',$flowids)->where('b.tasktat<=',$enddate)->where('a.userstatus','1')->get();
		$totaltaskdone=$restdone->num_rows();
		return $totaltaskdone;
		/** End **/
		
	}
	
	
		function worknotdonemisdetailsfordonenotdone($startdate,$enddate,$flowids,$allusers)
	{
		/** $startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		$restdone=$this->db->select('a.id')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid
		')->where('a.flowstage',$flowids)->where('b.tasktat<=',$enddate)->where('a.userstatus','1')->get();
		$totaltaskdone=$restdone->num_rows();
		return $totaltaskdone; **/
		
		$QRY = $this->db->select('total_days, tat, set_time,setorder')->from('fms_flow')->where('flow_id',$flowids)->get();
		$res = $QRY->result();
		foreach($res as $fmsinformation);
		if($fmsinformation->set_time<>0)
		{
		$totaldaysslave = $fmsinformation->set_time;
		$day=0;
		}else{
		$totaldaysslave = $fmsinformation->total_days;
		$day=1;
		}
		$tatstage = $fmsinformation->tat;
		$setorder=$fmsinformation->setorder;
		$oddetail='';
		$totaltaskoverall=0;
		$markapp=array();
		$depen=$this->checkfordependency($flowids);
		$startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		/** Get Total task during this period **/
		if($depen==0)
		{
			/** Get Total task done during this period **/
		$restdone=$this->db->select('a.id as orderstageid,a.jobcardid,c.job_card_no,d.plannedOn,e.tasktat')->from('order_stage a')->join('order_instruments c','a.jobcardid=c.id')->join('order_planning d','c.id=d.jobcard_id')->join('fmstatdate e','a.id=e.orderstageid')->where('a.flowstage',$flowids)->where('e.tasktat<=',$enddate)->where('a.userstatus','0')->get();
		if($restdone->num_rows()>0)
		{
		   
			foreach($restdone->result() as $restdone1)
			{
			
			
				  $timestamp=$this->fmsmodel->getprevioustimestamp($tatstage,$restdone1->plannedOn,$restdone1->jobcardid,$flowids,$restdone1->orderstageid);
				  //echo $timestamp;exit;
			    $tatdate=$this->fmsmodel->getupcomingtatdatenew($tatstage,$restdone1->plannedOn,$restdone1->jobcardid,$timestamp,$totaldaysslave,$restdone1->orderstageid,$day);
			    
				
				$taskplannedOn=$tatdate;

				  
				$oddetail.="ORDER# ".$restdone1->job_card_no." -PT -".date('d/m/y g:i:A',strtotime($taskplannedOn))."<br/>";
			}
			
			return $oddetail;
		}else
		{
			return $oddetail;
		}
		/** End **/
		}else{
			
			$depflow=$this->getdependentflows($flowids);
			
				$resttotal=$this->db->select('a.jobcardid,a.id as orderstageid,a.orderid as originalorderid,b.tasktat,c.job_card_no,d.plannedOn')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->join('order_instruments c','a.jobcardid=c.id')->join('order_planning d','c.id=d.jobcard_id')->where('b.flowstage',$flowids)->where('b.tasktat<=',$enddate)->where('a.userstatus','0')->get();
			if($resttotal->num_rows()>0)
			{
				
				foreach($resttotal->result() as $resttotal1)
				{
					$availableflowstage=array();
					$plannedflowid=$this->checkforplanstartpoint($resttotal1->jobcardid);
					$plannedstageorderno=$this->fmsmodel->getplanstartsfromorderno($plannedflowid);
				if(count($depflow)>0)
				{
						
					if($plannedstageorderno!=1)
					{
						$ifdependexist=$this->db->select("a.dependentflowid")->from('flowdependency a')->join('order_stage b','a.dependentflowid=b.flowstage')->where('b.jobcardid',$resttotal1->jobcardid)->where('a.flowid',$flowids)->get();
						if($ifdependexist->num_rows()>0)
						{
								foreach($ifdependexist->result() as $ifdependexist1)
								{
								$availableflowstage[]=$ifdependexist1->dependentflowid;
								}

						}
					}else
					{
								$ifdependexist=$this->db->select("a.dependentflowid")->from('flowdependency a')->where('a.flowid',$flowids)->get();
								if($ifdependexist->num_rows()>0)
								{
								foreach($ifdependexist->result() as $ifdependexist1)
								{
								$availableflowstage[]=$ifdependexist1->dependentflowid;
								}
								}
								
					}
					
					
					
					
					
					$checkskiiped=$this->fmsmodel->checkifdatahascomeskipped($resttotal1->jobcardid,$flowids);
					
					if(count($checkskiiped)>0)
					{
						$availableflowstage = array_diff($availableflowstage,$checkskiiped);
					}
					
					if(count($availableflowstage)>0)
			{	
				
				$availableflowstages = "'" . implode ( "', '", $availableflowstage ) . "'";
					if($flowids==5 && $resttotal1->jobcardid=='39')
					{
						$a="id";
					}else{
						$a="id";
					}
					
					$restyforcom=$this->db->select($a)->from('order_stage')->where_in('flowstage',$availableflowstages,false)->where('userstatus','1')->where('jobcardid',$resttotal1->jobcardid)->get();
					if($restyforcom->num_rows()==count($availableflowstage))
					{
						$timestamp=$this->fmsmodel->getprevioustimestamp($tatstage,$resttotal1->plannedOn,$resttotal1->jobcardid,$flowids,$resttotal1->orderstageid);
				  //echo $timestamp;exit;
			    $tatdate=$this->fmsmodel->getupcomingtatdatenew($tatstage,$resttotal1->plannedOn,$resttotal1->jobcardid,$timestamp,$totaldaysslave,$resttotal1->orderstageid,$day);
			    $taskplannedOn=$tatdate;

						$oddetail.="ORDER# ".$resttotal1->job_card_no." -PT -".date('d/m/y g:i:A',strtotime($taskplannedOn))."<br/>";
					}
					
			}else
			{
			    	$timestamp=$this->fmsmodel->getprevioustimestamp($tatstage,$resttotal1->plannedOn,$resttotal1->jobcardid,$flowids,$resttotal1->orderstageid);
				  //echo $timestamp;exit;
			    $tatdate=$this->fmsmodel->getupcomingtatdatenew($tatstage,$resttotal1->plannedOn,$resttotal1->jobcardid,$timestamp,$totaldaysslave,$resttotal1->orderstageid,$day);
			    $taskplannedOn=$tatdate;
			    	$oddetail.="ORDER# ".$resttotal1->job_card_no." -PT -".date('d/m/y g:i:A',strtotime($taskplannedOn))."<br/>";
			}
					
					
				
				}
					
			}
				
			}
			
	
			
			

	
			return $oddetail;
		}
		/** End **/

		
	}
	
	
	function worknotdonemisdetailsfordonenotdonetmprename7june2020($startdate,$enddate,$flowids,$allusers)
	{
	    	$QRY = $this->db->select('total_days, tat, set_time,setorder')->from('fms_flow')->where('flow_id',$flowids)->get();
				$res = $QRY->result();
				foreach($res as $fmsinformation);
			if($fmsinformation->set_time<>0)
			{
			$totaldaysslave = $fmsinformation->set_time;
			$day=0;
			}else{
				$totaldaysslave = $fmsinformation->total_days;
				$day=1;
			}
				$tatstage = $fmsinformation->tat;
				$setorder=$fmsinformation->setorder;
		$oddetail='';
		$startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		/** Get Total task done during this period **/
		$restdone=$this->db->select('a.id as orderstageid,a.jobcardid,c.job_card_no,d.plannedOn,e.tasktat')->from('order_stage a')->join('order_instruments c','a.jobcardid=c.id')->join('order_planning d','c.id=d.jobcard_id')->join('fmstatdate e','a.id=e.orderstageid')->where('a.flowstage',$flowids)->where('e.tasktat<=',$enddate)->where('a.userstatus','0')->get();
		if($restdone->num_rows()>0)
		{
		    if($flowids=='2')
		    {
		       // echo "<pre>"; print_r($restdone->result()); exit;
		    }
			foreach($restdone->result() as $restdone1)
			{
			
			
				  $timestamp=$this->fmsmodel->getprevioustimestamp($tatstage,$restdone1->plannedOn,$restdone1->jobcardid,$flowids,$restdone1->orderstageid);
				  //echo $timestamp;exit;
			    $tatdate=$this->fmsmodel->getupcomingtatdatenew($tatstage,$restdone1->plannedOn,$restdone1->jobcardid,$timestamp,$totaldaysslave,$restdone1->orderstageid,$day);
			    
				
				$taskplannedOn=$tatdate;

				  
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
		$totaltaskoverall=0;
		$markapp=array();
		$startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		$depen=$this->checkfordependency($flowids);
		/** Get Total task done during this period **/
		//$restdone=$this->db->select('a.id')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->where('a.addedOn BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('a.flowstage',$flowids)->where('a.userstatus','1')->get();
		if($depen==0)
		{
		$restdone=$this->db->select('a.id')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->where('b.tasktat BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('a.flowstage',$flowids)->where('a.userstatus','1')->get();
		$totaltaskdone=$restdone->num_rows();
		return $totaltaskdone;
		/** End **/
		}else{
			
		$depflow=$this->getdependentflows($flowids);
			
			if($flowids==5)
			{
			$ff='a.jobcardid';
			}else{
				$ff='a.jobcardid';
			}
				$resttotal=$this->db->select($ff.',a.id,a.orderid as originalorderid,b.tasktat')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->where('b.tasktat BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('b.flowstage',$flowids)->where('a.userstatus','1')->get();
			if($resttotal->num_rows()>0)
			{
				
				foreach($resttotal->result() as $resttotal1)
				{
					$availableflowstage=array();
					$plannedflowid=$this->checkforplanstartpoint($resttotal1->jobcardid);
					$plannedstageorderno=$this->fmsmodel->getplanstartsfromorderno($plannedflowid);
					$depflow=$this->getdependentflows($flowids);
				if(count($depflow)>0)
				{
						
					if($plannedstageorderno!=1)
					{
						$ifdependexist=$this->db->select("a.dependentflowid")->from('flowdependency a')->join('order_stage b','a.dependentflowid=b.flowstage')->where('b.jobcardid',$resttotal1->jobcardid)->where('a.flowid',$flowids)->get();
						if($ifdependexist->num_rows()>0)
						{
								foreach($ifdependexist->result() as $ifdependexist1)
								{
								$availableflowstage[]=$ifdependexist1->dependentflowid;
								}

						}
					}else
					{
								$ifdependexist=$this->db->select("a.dependentflowid")->from('flowdependency a')->where('a.flowid',$flowids)->get();
								if($ifdependexist->num_rows()>0)
								{
								foreach($ifdependexist->result() as $ifdependexist1)
								{
								$availableflowstage[]=$ifdependexist1->dependentflowid;
								}
								}
								
					}
					
					
					
					
					
					$checkskiiped=$this->fmsmodel->checkifdatahascomeskipped($resttotal1->jobcardid,$flowids);
					
					if(count($checkskiiped)>0)
					{
						$availableflowstage = array_diff($availableflowstage,$checkskiiped);
					}
					
					if(count($availableflowstage)>0)
			{	
				
				$availableflowstages = "'" . implode ( "', '", $availableflowstage ) . "'";
					if($flowids==5 && $resttotal1->jobcardid=='39')
					{
						$a="id";
					}else{
						$a="id";
					}
					
					$restyforcom=$this->db->select($a)->from('order_stage')->where_in('flowstage',$availableflowstages,false)->where('userstatus','1')->where('jobcardid',$resttotal1->jobcardid)->get();
					if($restyforcom->num_rows()==count($availableflowstage))
					{
						$markapp[]=1;
					}else{
						
						$markapp[]=0;
					}
					
			}else{
				
				$markapp[]=1;
				}
					
					
				
				}
					
			}
				
			}
			
				if(count($markapp)>0)
			{
				$totaltaskoverall=array_sum($markapp);
			}
			

	
			return $totaltaskoverall;
			
			
			
			
			
		}
		
	}
	
	
	
	
	function weeklyoveralldonetasktemprename27june20202($startdate,$enddate,$flowids,$allusers)
	{
		$startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		/** Get Total task done during this period **/
		//$restdone=$this->db->select('a.id')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->where('a.addedOn BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('a.flowstage',$flowids)->where('a.userstatus','1')->get();
		$restdone=$this->db->select('a.id')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->where('b.tasktat BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('a.flowstage',$flowids)->where('a.userstatus','1')->get();
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
			
			//$worknotpercentage=($totaldone-$totaloveralltask)/$totaloveralltask;
			
			$worknotpercentage=($totaldone-$totaloveralltask);
			
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
			
		
			$worknotpercentage=($totaldone-$totaloveralltask);
			
			$worknotpercentagescore=$worknotpercentage*100;
			
			
			return round($worknotpercentagescore,2);
		
			
			
		}
		
	}
	
	
	function weeklytasknotdoneontime($startdate,$enddate,$flowids,$allusers)
	{
		$totaltaskoverall=0;
		$markapp=array();
		$startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		$depen=$this->checkfordependency($flowids);
		/** Get Total task done during this period **/
		//$restdone=$this->db->select('a.id,a.addedOn,b.tasktat')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->where('a.addedOn BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('a.flowstage',$flowids)->where('a.userstatus','1')->where('a.addedOn>b.tasktat')->get();
		if($depen==0)
		{
			
				$restdone=$this->db->select('a.id,a.addedOn,b.tasktat')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->where('b.tasktat BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('a.flowstage',$flowids)->where('a.userstatus','1')->where('a.addedOn<=b.tasktat')->get();
		$totaltaskdone=$restdone->num_rows();
		return $totaltaskdone;
		/** End **/
		}else{
			
			
			$depflow=$this->getdependentflows($flowids);
			
			if($flowids==5)
			{
			$ff='a.jobcardid';
			}else{
				$ff='a.jobcardid';
			}
				$resttotal=$this->db->select($ff.',a.id,a.orderid as originalorderid,b.tasktat')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->where('b.tasktat BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('b.flowstage',$flowids)->where('a.userstatus','1')->where('a.addedOn<=b.tasktat')->get();
			if($resttotal->num_rows()>0)
			{
				
				foreach($resttotal->result() as $resttotal1)
				{
					$availableflowstage=array();
					$plannedflowid=$this->checkforplanstartpoint($resttotal1->jobcardid);
					$plannedstageorderno=$this->fmsmodel->getplanstartsfromorderno($plannedflowid);
					$depflow=$this->getdependentflows($flowids);
				if(count($depflow)>0)
				{
						
					if($plannedstageorderno!=1)
					{
						$ifdependexist=$this->db->select("a.dependentflowid")->from('flowdependency a')->join('order_stage b','a.dependentflowid=b.flowstage')->where('b.jobcardid',$resttotal1->jobcardid)->where('a.flowid',$flowids)->get();
						if($ifdependexist->num_rows()>0)
						{
								foreach($ifdependexist->result() as $ifdependexist1)
								{
								$availableflowstage[]=$ifdependexist1->dependentflowid;
								}

						}
					}else
					{
								$ifdependexist=$this->db->select("a.dependentflowid")->from('flowdependency a')->where('a.flowid',$flowids)->get();
								if($ifdependexist->num_rows()>0)
								{
								foreach($ifdependexist->result() as $ifdependexist1)
								{
								$availableflowstage[]=$ifdependexist1->dependentflowid;
								}
								}
								
					}
					
					
					
					
					
					$checkskiiped=$this->fmsmodel->checkifdatahascomeskipped($resttotal1->jobcardid,$flowids);
					
					if(count($checkskiiped)>0)
					{
						$availableflowstage = array_diff($availableflowstage,$checkskiiped);
					}
					
					if(count($availableflowstage)>0)
			{	
				
				$availableflowstages = "'" . implode ( "', '", $availableflowstage ) . "'";
					if($flowids==5 && $resttotal1->jobcardid=='39')
					{
						$a="id";
					}else{
						$a="id";
					}
					
					$restyforcom=$this->db->select($a)->from('order_stage')->where_in('flowstage',$availableflowstages,false)->where('userstatus','1')->where('jobcardid',$resttotal1->jobcardid)->get();
					if($restyforcom->num_rows()==count($availableflowstage))
					{
						$markapp[]=1;
					}else{
						
						$markapp[]=0;
					}
					
			}else{
				
				$markapp[]=1;
				}
					
					
				
				}
					
			}
				
			}
			
				if(count($markapp)>0)
			{
				$totaltaskoverall=array_sum($markapp);
			}
			

	
			return $totaltaskoverall;
			
			
			
			
			
			
			
			
			
			
		}
		
	}
	
	
	function weeklytasknotdoneontimetmprename27junr20202($startdate,$enddate,$flowids,$allusers)
	{
		$startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		/** Get Total task done during this period **/
		//$restdone=$this->db->select('a.id,a.addedOn,b.tasktat')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->where('a.addedOn BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('a.flowstage',$flowids)->where('a.userstatus','1')->where('a.addedOn>b.tasktat')->get();
				$restdone=$this->db->select('a.id,a.addedOn,b.tasktat')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->where('b.tasktat BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('a.flowstage',$flowids)->where('a.userstatus','1')->where('a.addedOn<=b.tasktat')->get();
		$totaltaskdone=$restdone->num_rows();
		return $totaltaskdone;
		/** End **/
		
	}
	


function weeklytasknotdoneontimedetailsOldddddddddddddddd04july2020($startdate,$enddate,$flowids,$allusers)
	{
		
		$oddetail='';
		$startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		/** Get Total task done during this period **/
		$depen=$this->checkfordependency($flowids);
		$QRY = $this->db->select('total_days, tat,set_time,setorder')->from('fms_flow')->where('flow_id',$flowids)->get();
		$res = $QRY->result();
		foreach($res as $fmsinformation);
		if($depen==0)
		{
		$restdone=$this->db->select('a.id as orderstageid,a.jobcardid,c.job_card_no,d.plannedOn,b.tasktat')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->join('order_instruments c','a.jobcardid=c.id')->join('order_planning d','c.id=d.jobcard_id')->where('b.tasktat BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('a.flowstage',$flowids)->where('a.userstatus','1')->where('a.addedOn>b.tasktat')->get();
		if($restdone->num_rows()>0)
		{
			foreach($restdone->result() as $restdone1)
			{
			
			if($fmsinformation->set_time<>0)
			{
			$totaldaysslave = $fmsinformation->set_time;
			$day=0;
			}else{
				$totaldaysslave = $fmsinformation->total_days;
				$day=1;
			}
				$tatstage = $fmsinformation->tat;
				$setorder=$fmsinformation->setorder;
				
				$timestamp=$this->fmsmodel->getprevioustimestamp($tatstage,$restdone1->plannedOn,$restdone1->jobcardid,$flowids,$restdone1->orderstageid);
				$tatdate=$this->fmsmodel->getupcomingtatdatenew($tatstage,$restdone1->plannedOn,$restdone1->jobcardid,$timestamp,$totaldaysslave,$restdone1->orderstageid,$day);
				
				$taskplannedOn=$tatdate;
	
				$oddetail.="ORDER# ".$restdone1->job_card_no." -PT -".date('d/m/y g:i A',strtotime($taskplannedOn))."<br/>";
			}
			
			return $oddetail;
		}else
		{
			return $oddetail;
		}
		
		}else{
			
			$resttotal=$this->db->select('a.id as orderstageid,a.jobcardid,c.job_card_no,d.plannedOn,b.tasktat')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->join('order_instruments c','a.jobcardid=c.id')->join('order_planning d','c.id=d.jobcard_id')->where('b.tasktat BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('a.flowstage',$flowids)->where('a.userstatus','1')->where('a.addedOn>b.tasktat')->get();
			if($resttotal->num_rows()>0)
			{
					
					foreach($resttotal->result() as $resttotal1)
				{
					$availableflowstage=array();
					$plannedflowid=$this->checkforplanstartpoint($resttotal1->jobcardid);
					$plannedstageorderno=$this->fmsmodel->getplanstartsfromorderno($plannedflowid);
					$depflow=$this->getdependentflows($flowids);
				if(count($depflow)>0)
				{
						
					if($plannedstageorderno!=1)
					{
						$ifdependexist=$this->db->select("a.dependentflowid")->from('flowdependency a')->join('order_stage b','a.dependentflowid=b.flowstage')->where('b.jobcardid',$resttotal1->jobcardid)->where('a.flowid',$flowids)->get();
						if($ifdependexist->num_rows()>0)
						{
								foreach($ifdependexist->result() as $ifdependexist1)
								{
								$availableflowstage[]=$ifdependexist1->dependentflowid;
								}

						}
					}else
					{
								$ifdependexist=$this->db->select("a.dependentflowid")->from('flowdependency a')->where('a.flowid',$flowids)->get();
								if($ifdependexist->num_rows()>0)
								{
								foreach($ifdependexist->result() as $ifdependexist1)
								{
								$availableflowstage[]=$ifdependexist1->dependentflowid;
								}
								}
								
					}
					
					
					
					
					
					$checkskiiped=$this->fmsmodel->checkifdatahascomeskipped($resttotal1->jobcardid,$flowids);
					
					if(count($checkskiiped)>0)
					{
						$availableflowstage = array_diff($availableflowstage,$checkskiiped);
					}
					
					if(count($availableflowstage)>0)
			{	
				
				$availableflowstages = "'" . implode ( "', '", $availableflowstage ) . "'";
					if($flowids==5 && $resttotal1->jobcardid=='39')
					{
						$a="id";
					}else{
						$a="id";
					}
					
					$restyforcom=$this->db->select($a)->from('order_stage')->where_in('flowstage',$availableflowstages,false)->where('userstatus','1')->where('jobcardid',$resttotal1->jobcardid)->get();
					if($restyforcom->num_rows()==count($availableflowstage))
					{
						
			if($fmsinformation->set_time<>0)
			{
			$totaldaysslave = $fmsinformation->set_time;
			$day=0;
			}else{
				$totaldaysslave = $fmsinformation->total_days;
				$day=1;
			}
				$tatstage = $fmsinformation->tat;
				$setorder=$fmsinformation->setorder;
				
				$timestamp=$this->fmsmodel->getprevioustimestamp($tatstage,$restdone1->plannedOn,$restdone1->jobcardid,$flowids,$restdone1->orderstageid);
				$tatdate=$this->fmsmodel->getupcomingtatdatenew($tatstage,$restdone1->plannedOn,$restdone1->jobcardid,$timestamp,$totaldaysslave,$restdone1->orderstageid,$day);
				$taskplannedOn=$tatdate;

					$oddetail.="ORDER# ".$restdone1->job_card_no." -PT -".date('d/m/y g:i A',strtotime($taskplannedOn))."<br/>";
					}
					
			}else{
				
				$oddetail.="ORDER# ".$restdone1->job_card_no." -PT -".date('d/m/y g:i A',strtotime($taskplannedOn))."<br/>";
				}
					
					
				
				}
					
			}




			}
		
		}
		/** End **/
		
		
		
	}
	
    function weeklytasknotdoneontimedetails($startdate,$enddate,$flowids,$allusers)
	{
		
		$oddetail='';
		$startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		/** Get Total task done during this period **/
		$depen=$this->checkfordependency($flowids);
		$QRY = $this->db->select('total_days, tat,set_time,setorder')->from('fms_flow')->where('flow_id',$flowids)->get();
		$res = $QRY->result();
		foreach($res as $fmsinformation);
		if($depen==0)
		{
		$restdone=$this->db->select('a.id as orderstageid,a.jobcardid,c.job_card_no,d.plannedOn,b.tasktat')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->join('order_instruments c','a.jobcardid=c.id')->join('order_planning d','c.id=d.jobcard_id')->where('b.tasktat BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('a.flowstage',$flowids)->where('a.userstatus','1')->where('a.addedOn>b.tasktat')->get();
		if($restdone->num_rows()>0)
		{
			foreach($restdone->result() as $restdone1)
			{
			
			if($fmsinformation->set_time<>0)
			{
			$totaldaysslave = $fmsinformation->set_time;
			$day=0;
			}else{
				$totaldaysslave = $fmsinformation->total_days;
				$day=1;
			}
				$tatstage = $fmsinformation->tat;
				$setorder=$fmsinformation->setorder;
				
				$timestamp=$this->fmsmodel->getprevioustimestamp($tatstage,$restdone1->plannedOn,$restdone1->jobcardid,$flowids,$restdone1->orderstageid);
				$tatdate=$this->fmsmodel->getupcomingtatdatenew($tatstage,$restdone1->plannedOn,$restdone1->jobcardid,$timestamp,$totaldaysslave,$restdone1->orderstageid,$day);
				
				$taskplannedOn=$tatdate;
	
				$oddetail.="ORDER# ".$restdone1->job_card_no." -PT -".date('d/m/y g:i A',strtotime($taskplannedOn))."<br/>";
			}
			
			return $oddetail;
		}else
		{
			return $oddetail;
		}
		
		}else{
			
			$resttotal=$this->db->select('a.id as orderstageid,a.jobcardid,c.job_card_no,d.plannedOn,b.tasktat')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->join('order_instruments c','a.jobcardid=c.id')->join('order_planning d','c.id=d.jobcard_id')->where('b.tasktat BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('a.flowstage',$flowids)->where('a.userstatus','1')->where('a.addedOn>b.tasktat')->get();
			if($resttotal->num_rows()>0)
			{
					
					foreach($resttotal->result() as $resttotal1)
				{
					$availableflowstage=array();
					$plannedflowid=$this->checkforplanstartpoint($resttotal1->jobcardid);
					$plannedstageorderno=$this->fmsmodel->getplanstartsfromorderno($plannedflowid);
					$depflow=$this->getdependentflows($flowids);
				if(count($depflow)>0)
				{
						
					if($plannedstageorderno!=1)
					{
						$ifdependexist=$this->db->select("a.dependentflowid")->from('flowdependency a')->join('order_stage b','a.dependentflowid=b.flowstage')->where('b.jobcardid',$resttotal1->jobcardid)->where('a.flowid',$flowids)->get();
						if($ifdependexist->num_rows()>0)
						{
								foreach($ifdependexist->result() as $ifdependexist1)
								{
								$availableflowstage[]=$ifdependexist1->dependentflowid;
								}

						}
					}else
					{
								$ifdependexist=$this->db->select("a.dependentflowid")->from('flowdependency a')->where('a.flowid',$flowids)->get();
								if($ifdependexist->num_rows()>0)
								{
								foreach($ifdependexist->result() as $ifdependexist1)
								{
								$availableflowstage[]=$ifdependexist1->dependentflowid;
								}
								}
								
					}
					
					
					
					
					
					$checkskiiped=$this->fmsmodel->checkifdatahascomeskipped($resttotal1->jobcardid,$flowids);
					
					if(count($checkskiiped)>0)
					{
						$availableflowstage = array_diff($availableflowstage,$checkskiiped);
					}
					
					if(count($availableflowstage)>0)
			{	
				
				$availableflowstages = "'" . implode ( "', '", $availableflowstage ) . "'";
					if($flowids==5 && $resttotal1->jobcardid=='39')
					{
						$a="id";
					}else{
						$a="id";
					}
					
					$restyforcom=$this->db->select($a)->from('order_stage')->where_in('flowstage',$availableflowstages,false)->where('userstatus','1')->where('jobcardid',$resttotal1->jobcardid)->get();
					if($restyforcom->num_rows()==count($availableflowstage))
					{
						
			if($fmsinformation->set_time<>0)
			{
			$totaldaysslave = $fmsinformation->set_time;
			$day=0;
			}else{
				$totaldaysslave = $fmsinformation->total_days;
				$day=1;
			}
				$tatstage = $fmsinformation->tat;
				$setorder=$fmsinformation->setorder;
				
				$timestamp=$this->fmsmodel->getprevioustimestamp($tatstage,$resttotal1->plannedOn,$resttotal1->jobcardid,$flowids,$resttotal1->orderstageid);
				$tatdate=$this->fmsmodel->getupcomingtatdatenew($tatstage,$resttotal1->plannedOn,$resttotal1->jobcardid,$timestamp,$totaldaysslave,$resttotal1->orderstageid,$day);
				$taskplannedOn=$tatdate;

					$oddetail.="ORDER# ".$resttotal1->job_card_no." -PT -".date('d/m/y g:i A',strtotime($taskplannedOn))."<br/>";
					}
					
			}else{
				
							
			if($fmsinformation->set_time<>0)
			{
			$totaldaysslave = $fmsinformation->set_time;
			$day=0;
			}else{
				$totaldaysslave = $fmsinformation->total_days;
				$day=1;
			}
				$tatstage = $fmsinformation->tat;
				$setorder=$fmsinformation->setorder;
				
				$timestamp=$this->fmsmodel->getprevioustimestamp($tatstage,$resttotal1->plannedOn,$resttotal1->jobcardid,$flowids,$resttotal1->orderstageid);
				$tatdate=$this->fmsmodel->getupcomingtatdatenew($tatstage,$resttotal1->plannedOn,$resttotal1->jobcardid,$timestamp,$totaldaysslave,$resttotal1->orderstageid,$day);
				$taskplannedOn=$tatdate;
				$oddetail.="ORDER# ".$resttotal1->job_card_no." -PT -".date('d/m/y g:i A',strtotime($taskplannedOn))."<br/>";
				}
					
					
				
				}
					
			}




			}
		
		}
		/** End **/
		
		
		
	}
	
	
	
function weeklytasknotdoneontimedetailsrenametemp27june2020($startdate,$enddate,$flowids,$allusers)
	{
		$oddetail='';
		$startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		/** Get Total task done during this period **/
		$restdone=$this->db->select('a.id as orderstageid,a.jobcardid,c.job_card_no,d.plannedOn,b.tasktat')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->join('order_instruments c','a.jobcardid=c.id')->join('order_planning d','c.id=d.jobcard_id')->where('b.tasktat BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('a.flowstage',$flowids)->where('a.userstatus','1')->where('a.addedOn>b.tasktat')->get();
		if($restdone->num_rows()>0)
		{
			foreach($restdone->result() as $restdone1)
			{
			$QRY = $this->db->select('total_days, tat,set_time,setorder')->from('fms_flow')->where('flow_id',$flowids)->get();
				$res = $QRY->result();
				foreach($res as $fmsinformation);
			if($fmsinformation->set_time<>0)
			{
			$totaldaysslave = $fmsinformation->set_time;
			$day=0;
			}else{
				$totaldaysslave = $fmsinformation->total_days;
				$day=1;
			}
				$tatstage = $fmsinformation->tat;
				$setorder=$fmsinformation->setorder;
				
				$timestamp=$this->fmsmodel->getprevioustimestamp($tatstage,$restdone1->plannedOn,$restdone1->jobcardid,$flowids,$restdone1->orderstageid);
				$tatdate=$this->fmsmodel->getupcomingtatdatenew($tatstage,$restdone1->plannedOn,$restdone1->jobcardid,$timestamp,$totaldaysslave,$restdone1->orderstageid,$day);
				
				$taskplannedOn=$tatdate;
	
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
		$depen=$this->checkfordependency($flowids);
		/** Get flow TAT **/
		$QRY = $this->db->select('total_days,tat,set_time, setorder')->from('fms_flow')->where('flow_id',$flowids)->get();
			$res = $QRY->result();
			foreach($res as $fmsinformation);
			if($fmsinformation->set_time<>0)
			{
			$totaldaysslave = $fmsinformation->set_time;
			$day=0;
			}else{
				$totaldaysslave = $fmsinformation->total_days;
				$day=1;
			}
			$tatstage = $fmsinformation->tat;
		/** End **/
		
		$startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		/** Get Total task done during this period **/
		if($depen==0)
		{
		$restdone=$this->db->select('a.id as orderstageid,a.jobcardid,c.job_card_no,a.addedOn,b.tasktat,d.plannedOn')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->join('order_instruments c','a.jobcardid=c.id')->join('order_planning d','c.id=d.jobcard_id')->where('b.tasktat BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('a.flowstage',$flowids)->where('a.userstatus','1')->where('a.addedOn>b.tasktat')->get();
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
				
				if($totaltatinhours<>0)
				{
				$delayscore=round($totaldelayinhours/$totaltatinhours,2);
				}else
				{
				    $delayscore=0;
				}
				
				$oddetail[]=$delayscore;
			}
			
			return array_sum($oddetail);
		}else
		{
			return 0;
		}
		
		}else{
			
			$resttotal=$this->db->select('a.id as orderstageid,a.jobcardid,c.job_card_no,a.addedOn,b.tasktat,d.plannedOn')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->join('order_instruments c','a.jobcardid=c.id')->join('order_planning d','c.id=d.jobcard_id')->where('b.tasktat BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('a.flowstage',$flowids)->where('a.userstatus','1')->where('a.addedOn>b.tasktat')->get();
			
			if($resttotal->num_rows()>0)
			{
				
				foreach($resttotal->result() as $resttotal1)
				{
					$availableflowstage=array();
					$plannedflowid=$this->checkforplanstartpoint($resttotal1->jobcardid);
					$plannedstageorderno=$this->fmsmodel->getplanstartsfromorderno($plannedflowid);
					$depflow=$this->getdependentflows($flowids);
				if(count($depflow)>0)
				{
						
					if($plannedstageorderno!=1)
					{
						$ifdependexist=$this->db->select("a.dependentflowid")->from('flowdependency a')->join('order_stage b','a.dependentflowid=b.flowstage')->where('b.jobcardid',$resttotal1->jobcardid)->where('a.flowid',$flowids)->get();
						if($ifdependexist->num_rows()>0)
						{
								foreach($ifdependexist->result() as $ifdependexist1)
								{
								$availableflowstage[]=$ifdependexist1->dependentflowid;
								}

						}
					}else
					{
								$ifdependexist=$this->db->select("a.dependentflowid")->from('flowdependency a')->where('a.flowid',$flowids)->get();
								if($ifdependexist->num_rows()>0)
								{
								foreach($ifdependexist->result() as $ifdependexist1)
								{
								$availableflowstage[]=$ifdependexist1->dependentflowid;
								}
								}
								
					}
					
					
					
					
					
					$checkskiiped=$this->fmsmodel->checkifdatahascomeskipped($resttotal1->jobcardid,$flowids);
					
					if(count($checkskiiped)>0)
					{
						$availableflowstage = array_diff($availableflowstage,$checkskiiped);
					}
					
					if(count($availableflowstage)>0)
			{	
				
				$availableflowstages = "'" . implode ( "', '", $availableflowstage ) . "'";
					if($flowids==5 && $resttotal1->jobcardid=='39')
					{
						$a="id";
					}else{
						$a="id";
					}
					
					$restyforcom=$this->db->select($a)->from('order_stage')->where_in('flowstage',$availableflowstages,false)->where('userstatus','1')->where('jobcardid',$resttotal1->jobcardid)->get();
					if($restyforcom->num_rows()==count($availableflowstage))
					{
						
						
						$timestamp=$this->fmsmodel->getprevioustimestamp($tatstage,$resttotal1->plannedOn,$resttotal1->jobcardid,$flowids,$resttotal1->orderstageid);
				
				$starttime=date('Y-m-d H:i',strtotime($timestamp));
				$planneddeadline=date('Y-m-d H:i',strtotime($resttotal1->tasktat));
				$actualcompletedon=date('Y-m-d H:i',strtotime($resttotal1->addedOn));
				
			
				$start=strtotime($starttime);
				$end=strtotime($planneddeadline);
				
				$actualcomplete=strtotime($actualcompletedon);
				
				
		
				$totaltatdelaymin=round(abs($actualcomplete-$end) / 60,2);
				$totaldelayinhours=$totaltatdelaymin/60;
				
				$totaltatinmin=round(abs($start - $end) / 60,2);
				$totaltatinhours=($totaltatinmin)*(-1)/60;
				
				if($totaltatinhours<>0)
				{
				$delayscore=round($totaldelayinhours/$totaltatinhours,2);
				}else
				{
				    $delayscore=0;
				}
				
				$oddetail[]=$delayscore;
						
						
						
						
					}else{
						
						$markapp[]=0;
					}
					
			}else{
				
				$timestamp=$this->fmsmodel->getprevioustimestamp($tatstage,$resttotal1->plannedOn,$resttotal1->jobcardid,$flowids,$resttotal1->orderstageid);
				
				$starttime=date('Y-m-d H:i',strtotime($timestamp));
				$planneddeadline=date('Y-m-d H:i',strtotime($resttotal1->tasktat));
				$actualcompletedon=date('Y-m-d H:i',strtotime($resttotal1->addedOn));
				
			
				$start=strtotime($starttime);
				$end=strtotime($planneddeadline);
				
				$actualcomplete=strtotime($actualcompletedon);
				
				
		
				$totaltatdelaymin=round(abs($actualcomplete-$end) / 60,2);
				$totaldelayinhours=$totaltatdelaymin/60;
				
				$totaltatinmin=round(abs($start - $end) / 60,2);
				$totaltatinhours=($totaltatinmin)*(-1)/60;
				
				if($totaltatinhours<>0)
				{
				$delayscore=round($totaldelayinhours/$totaltatinhours,2);
				}else
				{
				    $delayscore=0;
				}
				
				$oddetail[]=$delayscore;
				}
					
					
				
				}
					
			}
			
			return array_sum($oddetail);
				
			}else{
				 return 0;
			}
			
		
			
		}
		/** End **/
		
		
		
	}
	
	
	
	
	function weeklydifferenceintimedetailstmprename27june($startdate,$enddate,$flowids,$allusers)
	{
		$oddetail=array();
		/** Get flow TAT **/
		$QRY = $this->db->select('total_days,tat,set_time, setorder')->from('fms_flow')->where('flow_id',$flowids)->get();
			$res = $QRY->result();
			foreach($res as $fmsinformation);
			if($fmsinformation->set_time<>0)
			{
			$totaldaysslave = $fmsinformation->set_time;
			$day=0;
			}else{
				$totaldaysslave = $fmsinformation->total_days;
				$day=1;
			}
			$tatstage = $fmsinformation->tat;
		/** End **/
		
		$startdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$enddate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		/** Get Total task done during this period **/
		$restdone=$this->db->select('a.id as orderstageid,a.jobcardid,c.job_card_no,a.addedOn,b.tasktat,d.plannedOn')->from('order_stage a')->join('fmstatdate b','a.id=b.orderstageid')->join('order_instruments c','a.jobcardid=c.id')->join('order_planning d','c.id=d.jobcard_id')->where('b.tasktat BETWEEN "'. $startdate. '" and "'.$enddate.'"')->where('a.flowstage',$flowids)->where('a.userstatus','1')->where('a.addedOn>b.tasktat')->get();
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
				
				if($totaltatinhours<>0)
				{
				$delayscore=round($totaldelayinhours/$totaltatinhours,2);
				}else
				{
				    $delayscore=0;
				}
				
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
	function fmscurrentweekscore($user_id,$sdate,$edate)
	{
		$currentweekscore=array();
		
		/** This WEEK MONDAY & SAT **/
			$lastWeek = array();
			if($sdate=='')
		{
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
			
			//echo $current_monday.'<br/>'.$current_saturday; exit;
		}else
		{
		    
		    $current_monday = $sdate;
			$current_saturday = $edate;
		    
		    
		}
		
		//echo $current_monday.'<br/>'.$current_saturday;exit;
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
	
	
	
	function fmspreviousweekscore($user_id,$sdate,$edate)
	{
		$currentweekscore=array();
		
		/** This WEEK MONDAY & SAT **/
			$lastWeek = array();
			if($sdate=='')
			{
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
		    $lastWeek[]=date('Y-m-d',strtotime($d.' -7 Days'));
			}
			$current_monday = $lastWeek[0];
			$current_saturday = $lastWeek[5];
			}else
			{
			    
			    $current_monday = date('Y-m-d', strtotime($sdate.'monday last week'));
			$current_saturday = date('Y-m-d', strtotime($sdate.'saturday last week'));
			    
			}
			/** End **/
			$allproductionflow= $this->getallproductionflowformis();
			if($allproductionflow=='0')
			{
			echo "NO PRODUCTION FLOEW INCLUDED IN MIS";EXIT;
			}
			
			//echo $current_monday.'<br/>'.$current_saturday;exit;
			
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
	
	
	function checklistcurrentweekmis($userid,$sdate,$edate)
	{
			$lastWeek = array();
			if($sdate=='')
		{
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
		}else
		{
		    $current_monday = $sdate;
			$current_saturday = $edate;
		}
	
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
		 
		$checkfactor= $this->getchecklistfactor();
		
		  $checklistalltaskassigneddonemisscore=$this->Form_mismodel->worknotdonemisscoreforchecklist($checkfactor,$checklistalltaskassigneddone,$checklistalltaskassigned);
		  
		  
	
		
		  return $checklistalltaskassigneddonemisscore;
		
	}
	
	
	function checklistpreviousweekmis($userid,$sdate,$edate)
	{
		
		$lastWeek = array();
		if($sdate=='')
		{
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
$lastWeek[]=date('Y-m-d',strtotime($d.' -7 Days'));
}
$current_monday = $lastWeek[0];
$current_saturday = $lastWeek[5];
}else{
		
$current_monday = date('Y-m-d', strtotime($sdate.'monday last week'));
$current_saturday = date('Y-m-d', strtotime($sdate.'saturday last week'));		
	}
	
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
		 
		 $checkfactor=$this->getchecklistfactor();
		  $checklistalltaskassigneddonemisscore=$this->Form_mismodel->worknotdonemisscoreforchecklist($checkfactor,$checklistalltaskassigneddone,$checklistalltaskassigned);
	
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
	
	
	function getallnonfmsuser($uid)
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
		
		if($uid<>'')
		{
		    $this->db->where('user_id',$uid);
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
	
	
	function delegationcurrentweekmis($allusers,$sdate,$edate)
	{
		$delegationmiscore=0;
		
		$lastWeek = array();
		if($sdate=='')
		{
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
		}else
		{
		$current_monday = $sdate;
		$current_saturday = $edate;
		}

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
	

function delegationpreviousweekmis($allusers,$sdate,$edate)
	{
		$delegationmiscore=0;
		
		$lastWeek = array();
		if($sdate=='')
		{
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
        $lastWeek[]=date('Y-m-d',strtotime($d.' -7 Days'));
		}
		$current_monday = $lastWeek[0];
		$current_saturday = $lastWeek[5];
		}else
		{
		    
            $current_monday = date('Y-m-d', strtotime($sdate.'monday last week'));
            $current_saturday = date('Y-m-d', strtotime($sdate.'saturday last week'));
		}

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
	
function formcurrentweekmis($allusers,$sdate,$edate)
{
	$lastWeek = array();
	if($sdate=='')
{
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
}else
{
    $current_monday = $sdate;
$current_saturday = $edate;	
}

$startdate=date('Y-m-d',strtotime($current_monday))." 00:00:00";
$enddate=date('Y-m-d',strtotime($current_saturday))." 23:59:59";
		
	 $formids=$this->Form_mismodel->getuserwisedashboard($allusers);
	 $useroverallmis=array();
	  if(count($formids)>0)
	  {
		  foreach($formids as $formid)
		  {
			$getalltaskcount=$this->Form_mismodel->getoverallformtask($formid,$startdate,$enddate);
			$getalltaskdonecount=$this->Form_mismodel->getallformtaskdone($formid,$startdate,$enddate);
			$formdiffpart1=$getalltaskdonecount-$getalltaskcount;
			$formworknotdonemisscore=$this->Form_mismodel->worknotdonemisscore($getalltaskdonecount,$getalltaskcount);
			$formworknotdonetaskdetails=$this->Form_mismodel->formworknotdonetaskdetails($formid,$startdate,$enddate);
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


function formpreviousweekmis($allusers,$sdate,$edate)
{
	$lastWeek = array();
	if($sdate=='')
	{
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
        
        $lastWeek[]=date('Y-m-d',strtotime($d.' -7 Days'));
}
$current_monday = $lastWeek[0];
$current_saturday = $lastWeek[5];
}else
{
    $current_monday = date('Y-m-d', strtotime($sdate.'monday last week'));
	$current_saturday = date('Y-m-d', strtotime($sdate.'saturday last week'));
}

$startdate=date('Y-m-d',strtotime($current_monday))." 00:00:00";
$enddate=date('Y-m-d',strtotime($current_saturday))." 23:59:59";
	 $formids=$this->Form_mismodel->getuserwisedashboard($allusers);
	 $useroverallmis=array();
	  if(count($formids)>0)
	  {
		  foreach($formids as $formid)
		  {
			$getalltaskcount=$this->Form_mismodel->getoverallformtask($formid,$startdate,$enddate);
			$getalltaskdonecount=$this->Form_mismodel->getallformtaskdone($formid,$startdate,$enddate);
			$formdiffpart1=$getalltaskdonecount-$getalltaskcount;
			$formworknotdonemisscore=$this->Form_mismodel->worknotdonemisscore($getalltaskdonecount,$getalltaskcount);
			$formworknotdonetaskdetails=$this->Form_mismodel->formworknotdonetaskdetails($formid,$startdate,$enddate);
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


function getchecklistfactor()
{
    
$restyu=$this->db->select('factor')->from('checklistmisfactor')->get();
if($restyu->num_rows()>0)
{
    foreach($restyu->result() as $restyu1);
    $checklistfactor=floatval($restyu1->factor);
   
}else
{
   
    $checklistfactor='10';
}
    
    return $checklistfactor;
    
}
	
	
		function checkfordependency($flowid)
{
	$restyyu=$this->db->select('flow_id')->from('fms_flow')->where('flow_id',$flowid)->where('dependency','1')->get();
	return $restyyu->num_rows();
	
}

function getdependentflows($flowid)
{
	$depe=array();
	$restyyuu=$this->db->select('dependentflowid')->from('flowdependency')->where('flowid',$flowid)->get();
	if($restyyuu->num_rows()>0)
	{
		$depe=$restyyuu->result();
		return $depe;
		
	}else{
		
		return $depe;
	}

	
}

function checkforplanstartpoint($jobcardid)
{
	
		$rest123=$this->db->select('planstartsfrom')->from('order_planning')->where('jobcard_id',$jobcardid)->get();
        
    if($rest123->num_rows()>0)
    {
		foreach($rest123->result() as $rest1231);

		$plan=$rest1231->planstartsfrom;
		
		return $plan;
    }else
    {
        echo $jobcardid;exit;
        return 0;
        
    }
	
	
}


function getcurrenttopreviousweekalldatess()
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
 
    return $lastWeek;
    
}


function getcurrenttoprevioustopreviousweekalldatess()
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
        
        $lastWeek[]=date('Y-m-d',strtotime($d.' -7 Days'));
    }
 
    return $lastWeek;
    
}



function checklistcurrentweekmisforchecklistpage($userid,$sdate,$edate)
	{
			$lastWeek = array();
			if($sdate=='')
		{
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
		}else
		{
		    $current_monday = $sdate;
			$current_saturday = $edate;
		}
	
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
		 
		$checkfactor= $this->getchecklistfactor();
		
		  $checklistalltaskassigneddonemisscore=$this->Form_mismodel->worknotdonemisscoreforchecklist($checkfactor,$checklistalltaskassigneddone,$checklistalltaskassigned);
		  
		  
	
		
		  return $checklistalltaskassigneddonemisscore;
		
	}
	
	
	function checklistpreviousweekmisforchecklistpage($userid,$sdate,$edate)
	{
		
		$lastWeek = array();
		if($sdate=='')
		{
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
$lastWeek[]=date('Y-m-d',strtotime($d.' -7 Days'));
}
$current_monday = $lastWeek[0];
$current_saturday = $lastWeek[5];
}else{
		
$current_monday = date('Y-m-d', strtotime($sdate.'monday last week'));
$current_saturday = date('Y-m-d', strtotime($sdate.'saturday last week'));		
	}
	
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
		 
		 $checkfactor=$this->getchecklistfactor();
		  $checklistalltaskassigneddonemisscore=$this->Form_mismodel->worknotdonemisscoreforchecklist($checkfactor,$checklistalltaskassigneddone,$checklistalltaskassigned);
	
		  return $checklistalltaskassigneddonemisscore;
		
	}
    
    
	function currentweekdates()
	{
	
	$weekday=array();
	$monday = strtotime("last monday");
	$monday = date('w', $monday)==date('w') ? $monday+7*86400 : $monday;

	$sunday = strtotime(date("Y-m-d",$monday)." +6 days");

	$this_week_sd = date("Y-m-d",$monday);
	$this_week_ed = date("Y-m-d",$sunday);

	$weekday[]=$this_week_sd;
	$weekday[]=$this_week_ed;
	
	return $weekday;



	}
	
	
	
	function saveOrderDays($data) {
		$this->db->insert('order_days', $data);
		return 1;
	}
}
