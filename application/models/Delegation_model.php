<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Delegation_model extends CI_Model {

	public function __construct()
	{
		
		parent::__construct();
		
	}

	
	function currentweekmisfortaskdelegatedtome($userid)
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
			$current_monday = $current_monday." 00:00:00";
			$current_saturday = $current_saturday." 23:59:59";
		
			$delegatedtome=$this->db->select('id')->from('delegation_task')->where('targetdate BETWEEN "'.$current_monday. '" and "'.$current_saturday.'"')->where('delegate_to',$userid)->where('done_ontime_or_late','0')->get();
			
			$delgatedtomyself=$delegatedtome->num_rows()*-10;
			
			return $delgatedtomyself;
		
		
	}
	
	
	function previousweekmisfortaskdelegatedtome($userid)
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
			$current_monday = $current_monday." 00:00:00";
			$current_saturday = $current_saturday." 23:59:59";
		
			$delegatedtome=$this->db->select('id')->from('delegation_task')->where('targetdate BETWEEN "'.$current_monday. '" and "'.$current_saturday.'"')->where('delegate_to',$userid)->where('done_ontime_or_late','0')->get();
			
			$delgatedtomyself=$delegatedtome->num_rows()*-10;
			
			return $delgatedtomyself;
		
		
	}
	
	
	function currentweekmisforfollowuptakenontaskdelegatedbyme($userid)
	{
		
			$lastWeek = array();
			$tasktotalscore = array();
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
			$current_monday = $current_monday." 00:00:00";
			$current_saturday = $current_saturday." 23:59:59";
		
			$delegatedtome=$this->db->select('id,delegated_date,second_date,third_date')->from('delegation_task')->where('targetdate BETWEEN "'.$current_monday. '" and "'.$current_saturday.'"')->where('yourname',$userid)->where('done_ontime_or_late','0')->get();
			$delgatedtosomeoneelse=$delegatedtome->num_rows();
			if($delgatedtosomeoneelse>0)
			{
				
				foreach($delegatedtome->result() as $delegatedtome1)
				{
					
					$firstdate=$delegatedtome1->delegated_date;
					$secondate=$delegatedtome1->second_date;
					$thirddate=$delegatedtome1->third_date;
					$firstfscore=0;
					$secondfscore=0;
					$thirdfscore=0;
					if(in_array($firstdate,$lastWeek))
					{
						if($firstdate<>date('Y-m-d'))
						{
						$firstfollowup=$this->db->select('doneontimeornot')->from('delegated_task_followup')->where('task_id',$delegatedtome1->id)->order_by('id','ASC')->limit('0,1')->get();
						if($firstfollowup->num_rows()>0)
						{
							foreach($firstfollowup->result() as $firstfollowup1);
							if($firstfollowup1->doneontimeornot=='1')
							{
							$firstfscore=-10;
							}
							
						}else{
							
							$firstfscore=-10;
						}
					}
					}
					
					
					
					if(in_array($secondate,$lastWeek))
					{
						if($secondate<>date('Y-m-d'))
						{
						$secondfollowup=$this->db->select('doneontimeornot')->from('delegated_task_followup')->where('task_id',$delegatedtome1->id)->order_by('id','ASC')->limit('1,1')->get();
						if($secondfollowup->num_rows()>0)
						{
							foreach($secondfollowup->result() as $secondfollowup1);
							if($secondfollowup1->doneontimeornot=='1')
							{
							$secondfscore=-10;
							}
							
						}else{
							
							$secondfscore=-10;
						}
					}
					}
					
					
					if(in_array($thirddate,$lastWeek))
					{
						if($thirddate<>date('Y-m-d'))
						{
						$thirdfollowup=$this->db->select('doneontimeornot')->from('delegated_task_followup')->where('task_id',$delegatedtome1->id)->order_by('id','ASC')->limit('2,1')->get();
						if($secondfollowup->num_rows()>0)
						{
							foreach($thirdfollowup->result() as $thirdfollowup1);
							if($thirdfollowup1->doneontimeornot=='1')
							{
							$thirdfscore=-10;
							}
							
						}else{
							
							$thirdfscore=-10;
						}
					}
					
					}
					
					
					$tasktotalscore[]=$firstfscore+$secondfscore+$thirdfscore;
					
				}
				
				
				return array_sum($tasktotalscore);
				
			}else
			{
				$tasktotalscore=0;
				return $tasktotalscore;
				
				
			}
			
			
		
		
	}
	
	
	
	function previousweekmisforfollowuptakenontaskdelegatedbyme($userid)
	{
		
		
		
			$lastWeek = array();
			$tasktotalscore = array();
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
			$current_monday = $current_monday." 00:00:00";
			$current_saturday = $current_saturday." 23:59:59";
		
			$delegatedtome=$this->db->select('id,delegated_date,second_date,third_date')->from('delegation_task')->where('targetdate BETWEEN "'.$current_monday. '" and "'.$current_saturday.'"')->where('yourname',$userid)->where('done_ontime_or_late','0')->get();
			$delgatedtosomeoneelse=$delegatedtome->num_rows();
			if($delgatedtosomeoneelse>0)
			{
				
				foreach($delegatedtome->result() as $delegatedtome1)
				{
					
					$firstdate=$delegatedtome1->delegated_date;
					$secondate=$delegatedtome1->second_date;
					$thirddate=$delegatedtome1->third_date;
					$firstfscore=0;
					$secondfscore=0;
					$thirdfscore=0;
					if(in_array($firstdate,$lastWeek))
					{
						if($firstdate<>date('Y-m-d'))
						{
						$firstfollowup=$this->db->select('doneontimeornot')->from('delegated_task_followup')->where('task_id',$delegatedtome1->id)->order_by('id','ASC')->limit('0,1')->get();
						if($firstfollowup->num_rows()>0)
						{
							foreach($firstfollowup->result() as $firstfollowup1);
							if($firstfollowup1->doneontimeornot=='1')
							{
							$firstfscore=-10;
							}
							
						}else{
							
							$firstfscore=-10;
						}
					}
					}
					
					
					
					if(in_array($secondate,$lastWeek))
					{
						if($secondate<>date('Y-m-d'))
						{
						$secondfollowup=$this->db->select('doneontimeornot')->from('delegated_task_followup')->where('task_id',$delegatedtome1->id)->order_by('id','ASC')->limit('1,1')->get();
						if($secondfollowup->num_rows()>0)
						{
							foreach($secondfollowup->result() as $secondfollowup1);
							if($secondfollowup1->doneontimeornot=='1')
							{
							$secondfscore=-10;
							}
							
						}else{
							
							$secondfscore=-10;
						}
					}
					}
					
					
					if(in_array($thirddate,$lastWeek))
					{
						if($thirddate<>date('Y-m-d'))
						{
						$thirdfollowup=$this->db->select('doneontimeornot')->from('delegated_task_followup')->where('task_id',$delegatedtome1->id)->order_by('id','ASC')->limit('2,1')->get();
						if($secondfollowup->num_rows()>0)
						{
							foreach($thirdfollowup->result() as $thirdfollowup1);
							if($thirdfollowup1->doneontimeornot=='1')
							{
							$thirdfscore=-10;
							}
							
						}else{
							
							$thirdfscore=-10;
						}
					}
					
					}
					
					
					$tasktotalscore[]=$firstfscore+$secondfscore+$thirdfscore;
					
				}
				
					
				return array_sum($tasktotalscore);
				
			}else
			{
				$tasktotalscore=0;
				return $tasktotalscore;
				
				
			}
	}
	
	function checkforalldelegationassigned($userid,$startdate,$enddate)
	{
			$tasktotalscore = array();
			$current_monday = $startdate;
			$current_saturday = $enddate;
			$current_monday = $current_monday." 00:00:00";
			$current_saturday = $current_saturday." 23:59:59";
			$alltaskid=array();
			$taskid=$this->db->select('id')->from('delegation_task')->where('delegate_to',$userid)->where('delegated_date BETWEEN "'. $current_monday. '" and "'.$current_saturday.'"')->get();
			if($taskid->num_rows()>0)
			{
			foreach($taskid->result() as $taskid1)
			{
			$alltaskid[]=1;
			}


			}
			
			
			$taskid=$this->db->select('id')->from('delegation_task')->where('delegate_to',$userid)->where('second_date BETWEEN "'. $current_monday. '" and "'.$current_saturday.'"')->get();
			if($taskid->num_rows()>0)
			{
				foreach($taskid->result() as $taskid1)
				{
					$alltaskid[]=1;
				}
				
				
			}
			
			
			$taskid=$this->db->select('id')->from('delegation_task')->where('delegate_to',$userid)->where('third_date BETWEEN "'. $current_monday. '" and "'.$current_saturday.'"')->get();
			if($taskid->num_rows()>0)
			{
				foreach($taskid->result() as $taskid1)
				{
					$alltaskid[]=1;
				}
				
				
			}
		
		
		return $alltaskid;
		
	}
	
	
	
	
	function checkforalldelegationassignedmarkeddone($userid,$startdate,$enddate)
	{
		
			$tasktotalscore = array();
			$current_monday = $startdate;
			$current_saturday = $enddate;
			$current_monday = $current_monday;
			$current_saturday = $current_saturday;
			$alltaskid=array();
			$taskid=$this->db->select('id')->from('delegation_task')->where('delegate_to',$userid)->where('delegated_date BETWEEN "'. $current_monday. '" and "'.$current_saturday.'"')->get();
			if($taskid->num_rows()>0)
			{
				foreach($taskid->result() as $taskid1)
				{
					$alltaskid[]=$taskid1->id;
				}
				
				
			}
			
			
			$taskid=$this->db->select('id')->from('delegation_task')->where('delegate_to',$userid)->where('second_date BETWEEN "'. $current_monday. '" and "'.$current_saturday.'"')->get();
			if($taskid->num_rows()>0)
			{
				foreach($taskid->result() as $taskid1)
				{
					$alltaskid[]=$taskid1->id;
				}
				
				
			}
			
			
			$taskid=$this->db->select('id')->from('delegation_task')->where('delegate_to',$userid)->where('third_date BETWEEN "'. $current_monday. '" and "'.$current_saturday.'"')->get();
			if($taskid->num_rows()>0)
			{
				foreach($taskid->result() as $taskid1)
				{
					$alltaskid[]=$taskid1->id;
				}
				
				
			}
		
		
		if(count($alltaskid)>0)
		{
		
		$alltasksss = "'" . implode ( "', '", $alltaskid ) . "'";
		$this->db->select('id')->from('delegation_task');
		$this->db->where_in('id',$alltasksss,false);
		$this->db->where('task_status','1');
		$qyr=$this->db->get();
		$actual=$qyr->num_rows();
		
		return $actual;
		}else{
			
			$actual=0;
			return $actual;
		}
		
		
	}
	
	
	
	function checkforalldelegationassignedmarkednotdone($userid,$startdate,$enddate)
	{
		
		
			$tasktotalscore = array();
			
			
			
			$current_monday = $startdate;
			$current_saturday = $enddate;
			$current_monday = $current_monday." 00:00:00";
			$current_saturday = $current_saturday." 23:59:59";
			$alltaskid=array();
			$taskid=$this->db->select('id')->from('delegation_task')->where('delegate_to',$userid)->where('delegated_date BETWEEN "'. $current_monday. '" and "'.$current_saturday.'"')->get();
			if($taskid->num_rows()>0)
			{
				foreach($taskid->result() as $taskid1)
				{
					$alltaskid[]=$taskid1->id;
				}
				
				
			}
			
			
			$taskid=$this->db->select('id')->from('delegation_task')->where('delegate_to',$userid)->where('second_date BETWEEN "'. $current_monday. '" and "'.$current_saturday.'"')->get();
			if($taskid->num_rows()>0)
			{
				foreach($taskid->result() as $taskid1)
				{
					$alltaskid[]=$taskid1->id;
				}
				
				
			}
			
			
			$taskid=$this->db->select('id')->from('delegation_task')->where('delegate_to',$userid)->where('third_date BETWEEN "'. $current_monday. '" and "'.$current_saturday.'"')->get();
			if($taskid->num_rows()>0)
			{
				foreach($taskid->result() as $taskid1)
				{
					$alltaskid[]=$taskid1->id;
				}
				
				
			}
		
		
		if(count($alltaskid)>0)
		{
		
		$taskdetail='';
		$alltasksss = "'" . implode ( "', '", $alltaskid ) . "'";
		$this->db->select('id,task,delegated_date,second_date,third_date')->from('delegation_task');
		$this->db->where_in('id',$alltasksss,false);
		$this->db->where('task_status','0');
		$qyr=$this->db->get();
		$actual=$qyr->num_rows();
		if($actual>0)
		{
			foreach($qyr->result() as $qty1)
			{
				if($qty1->third_date<>'0000-00-00')
				{
					$ptdate=date('d/m/y',strtotime($qty1->third_date));
				}else if($qty1->second_date<>'0000-00-00')
				{
					$ptdate=date('d/m/y',strtotime($qty1->second_date));
				}else{
					
					$ptdate=date('d/m/y',strtotime($qty1->delegated_date));
				}
				
				$taskdetail.="TASK# ".STRTOUPPER($qty1->task)." -PT -".$ptdate."<br/>";
			}
			
		 return $taskdetail;
		
		
		}else
		{
			return $taskdetail;
			
		}
		}else{
			
			
			return $taskdetail;
		}
		
		
	}
	
	
	function delegationmiscore($userid)
	{
				
		    $lastWeek = array();
		    $ptscore[]=0;
			$tasktotalscore = array();
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
			$current_monday = $current_monday." 00:00:00";
			$current_saturday = $current_saturday." 23:59:59";
			$alltaskid=array();
			$taskid=$this->db->select('id')->from('delegation_task')->where('delegate_to',$userid)->where('delegated_date BETWEEN "'. $current_monday. '" and "'.$current_saturday.'"')->get();
			if($taskid->num_rows()>0)
			{
				foreach($taskid->result() as $taskid1)
				{
					$alltaskid[]=$taskid1->id;
				}
				
				
			}
			
			
			$taskid=$this->db->select('id')->from('delegation_task')->where('delegate_to',$userid)->where('second_date BETWEEN "'. $current_monday. '" and "'.$current_saturday.'"')->get();
			if($taskid->num_rows()>0)
			{
				foreach($taskid->result() as $taskid1)
				{
					$alltaskid[]=$taskid1->id;
				}
				
				
			}
			
			
			$taskid=$this->db->select('id')->from('delegation_task')->where('delegate_to',$userid)->where('third_date BETWEEN "'. $current_monday. '" and "'.$current_saturday.'"')->get();
			if($taskid->num_rows()>0)
			{
				foreach($taskid->result() as $taskid1)
				{
					$alltaskid[]=$taskid1->id;
				}
				
				
			}
		
		
		if(count($alltaskid)>0)
		{
		
		$ptscore=array();
		$ptscore[]=0;
		$alltasksss = "'" . implode ( "', '", $alltaskid ) . "'";
		$this->db->select('id,task,delegated_date,second_date,third_date')->from('delegation_task');
		$this->db->where_in('id',$alltasksss,false);
		$this->db->where('task_status','0');
		$qyr=$this->db->get();
		$actual=$qyr->num_rows();
		if($actual>0)
		{
			foreach($qyr->result() as $qty1)
			{
				if($qty1->third_date<>'0000-00-00')
				{
					$ptscore[]="-300";
				}else if($qty1->second_date<>'0000-00-00')
				{
					$ptscore[]="-200";
				}else{
					
					$ptscore[]="-100";
				}
				
				
			}
			
		 return array_sum($ptscore);
		
		
		}else
		{
			return array_sum($ptscore);
			
		}
		}else{
			
			
			return array_sum($ptscore);
		}
		
		
	}
	
	
	
	
	
	
	
	
}