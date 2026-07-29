<?php
$ci =&get_instance();
function getprevioustimestamp($tatstage,$plannedon,$jobcardid,$flowstage,$orderstageid)
	{
		$previousorder=$tatstage;
					
				/** Check for Primary Rejection **/

					$inprocessqc=$ci->db->select('backtrackto,addedOn')->from('qcremarks')->where('jobcardid',$jobcardid)->where('backtrackto !=','0')->where('orderstageid',$orderstageid)->get();
					$inprocessqcrow=$inprocessqc->num_rows();
					
				$finalqc=$ci->db->select('backtrackto,addedOn')->from('qcfinalremarks')->where('jobcardid',$jobcardid)->where('backtrackto !=','0')->where('orderstageid',$orderstageid)->get();
					$finalqcrow=$finalqc->num_rows();

				
				/* End **/
					
	if(($inprocessqcrow==0) && ($finalqcrow==0))
		{
		
		/** Check for Dependent Data **/
		$isdependss=$ci->db->select('dependentflowid')->from('flowdependency')->where('flowid',$flowstage)->get();
		if($isdependss->num_rows()==0)
		{

		/** End **/
		if($previousorder==0)
		{
		$previouscomtime=$plannedon;
		$previouscomdate=date('d-M-Y g:i A',strtotime($previouscomtime));
		return $previouscomdate;

		}else
		{
		//echo $previousorder;exit;
		$oldtime1=$ci->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$jobcardid)->order_by('id','DESC')->limit(1)->get();
		if($oldtime1->num_rows()>0){
		foreach($oldtime1->result() as $pastinfo);
			
		$previouscomtime=$pastinfo->addedOn;
		return $previouscomdate=date('d-M-Y g:i A',strtotime($pastinfo->addedOn));
		
		}else
		{
		$previouscomtime=$plannedon;
		return $previouscomdate=date('d-M-Y g:i A',strtotime($previouscomtime));
		
		}
		}
		
		}else
		{
				$alldates=array();
				foreach($isdependss->result() as $dependflow)
				{
					$oldtime1=$ci->db->select('addedOn')->from('order_stage')->where('flowstage',$dependflow->dependentflowid)->where('jobcardid',$jobcardid)->order_by('id','DESC')->limit(1)->get();
					
					if($oldtime1->num_rows()>0)
				{
					foreach($oldtime1->result() as $oldtime11);
				    $alldates[]=$oldtime11->addedOn;
					

							
				}else{  
						
					/** Need to check**/
						$previouscomtime=$plannedon;
						$previouscomdate=date('d-M-Y g:i A',strtotime($previouscomtime));
						 $alldates[]=$previouscomtime; 
					 
					}
					
					
				}

			//echo "<pre>"; print_r($alldates);exit;
							$max = max(array_map('strtotime', $alldates));
							$previouscomdate=date('d-M-Y g:i A', $max);
							return $previouscomdate;
				
				}
		
		
		}else
		{
		 if($inprocessqcrow!=0)
		{
		
			 foreach($inprocessqc->result() as $inprocess);
			 $previouscomdate=date('d-M-Y g:i A',strtotime($inprocess->addedOn));
		      return  $previouscomdate;
		
		}

		
		 if($finalqcrow!=0)
		{
		
		 foreach($finalqc->result() as $finalprocess);
		  $previouscomdate=date('d-M-Y g:i A',strtotime($finalprocess->addedOn));
			
			 
			return $previouscomdate;
		
		}

		
	}
					
	}
	
	
	
		function getupcomingtatdate($tatstage,$plannedon,$jobcardid,$timestamp,$daysslab,$orderstageid)
	{
			
	$datetime = DateTime::createFromFormat('d-M-Y g:i A', $timestamp);
		$TATDATE1=$datetime->format('Y-m-d');
	$TATDATE=date('Y-m-d',strtotime($TATDATE1."+".$daysslab." days"));
		$previousorder=$tatstage;
		
				if($previousorder==0)
				{
					$previouscomtime=$plannedon;
					$TATDATE1=date('d-M-Y',strtotime($previouscomtime));
					$TATDATE=date('Y-m-d',strtotime($TATDATE1."+".$daysslab." days"));
					
					
				}else
				{
					//echo $previousorder;exit;
					$oldtime1=$ci->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$jobcardid)->order_by('id','DESC')->limit(1)->get();
							if($oldtime1->num_rows()>0){

								foreach($oldtime1->result() as $pastinfo);
								
								
							if($pastinfo->addedOn=='0000-00-00 00:00:00')
{
								/** Check for Primary Rejection **/

					$inprocessqc=$ci->db->select('backtrackto,addedOn')->from('qcremarks')->where('jobcardid',$jobcardid)->where('backtrackto !=','0')->where('orderstageid',$orderstageid)->get();
					$inprocessqcrow=$inprocessqc->num_rows();
					
				$finalqc=$ci->db->select('backtrackto,addedOn')->from('qcfinalremarks')->where('jobcardid',$jobcardid)->where('backtrackto !=','0')->where('orderstageid',$orderstageid)->get();
					$finalqcrow=$finalqc->num_rows();
	if(($inprocessqcrow==0) && ($finalqcrow==0))
		{
		
 $previouscomtime=$proc->plannedOn;
}else
{
		if($inprocessqcrow!=0)
		{
		
			 foreach($inprocessqc->result() as $inprocess);
			 $previouscomtime=$inprocess->addedOn;
		      
		
		}

		
		 if($finalqcrow!=0)
		{
		
		 foreach($finalqc->result() as $finalprocess);
		  $previouscomtime=$finalprocess->addedOn;
			
			
		
		}
}

				
				/* End **/
								

}else
{
								$previouscomtime=$pastinfo->addedOn;
								$previouscomdate=date('d-M-Y',strtotime($pastinfo->addedOn))."<br>";
								$previouscomtimeonly=date('g:i A',strtotime($pastinfo->addedOn));
								
}
								


							}else
							{
								
								$previouscomtime=$proc->plannedOn;
					$previouscomdate=date('d-M-Y',strtotime($previouscomtime))."<br>";
					$previouscomtimeonly=date('g:i A',strtotime($previouscomtime));
							}
				}
				
				
				
				$TATDATEFORHOLIDAYCHECK=$TATDATE;

					$timestampforcheck=date('Y-m-d',strtotime($previouscomtime));
					$date_from=strtotime($timestampforcheck);
					$date_to=strtotime($TATDATEFORHOLIDAYCHECK);
	
					$betweendates=array();
					for ($g=$date_from; $g<=$date_to; $g+=86400) {  
						$betweendates[]= date("Y-m-d", $g);  
					} 
					
					
					
					if(count($betweendates)>0)
				{
					
					$alldates = "'" . implode ( "', '", $betweendates ) . "'";
					
					$ifholiday=$ci->db->select('holiday_id')->from('prestogroup_holidays')->where_in('holiday_date',$alldates,false)->get();
					if($ifholiday->num_rows()>0)
					{
			
						$TATDATEFORHOLIDAYCHECK=date('Y-m-d', strtotime($TATDATEFORHOLIDAYCHECK."+".$ifholiday->num_rows()." days"));
						
						
					}else{  
						$TATDATEFORHOLIDAYCHECK=$TATDATEFORHOLIDAYCHECK;
					}
				}else
				{
					$TATDATEFORHOLIDAYCHECK=$TATDATEFORHOLIDAYCHECK;
				}
				
				
				
					$query = $ci->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$TATDATEFORHOLIDAYCHECK)->get();
			$chhutti=array();
			if($query->num_rows()>0){
				
			foreach($query->result() as $holidays);
				$nextdate= date('Y-m-d', strtotime($holidays->holiday_date."+1 days"));
				$query = $ci->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();
				if($query->num_rows()>0){
					
					foreach($query->result() as $afterholiday);
					$aftrholidaydate = $afterholiday->holiday_date;
					$TATDATE= date('d-M-Y', strtotime($aftrholidaydate."+1 days"));
					return $TATDATE;
					
				}else {
				$TATDATE= date('d-M-Y', strtotime($nextdate));
				return $TATDATE;
				}
			}else{
			$TATDATE=date('d-M-Y', strtotime($TATDATEFORHOLIDAYCHECK));
			return $TATDATE;
			}
		
		
	}
	
	
	
	function sortFunction( $a, $b ) {
    return strtotime($a[1]) - strtotime($b[1]);
}

function gettotaldays($status,$startdate,$enddate)
{
	$startdate=date('Y-m-d',strtotime($startdate));
	if($status=='0')
	{
		$enddate=date('Y-m-d');
	}
	else{ 
	$enddate=date('Y-m-d',strtotime($enddate));
	}
	
$date1 = new DateTime($startdate);
$date2 = new DateTime($enddate);
$interval = $date1->diff($date2);
$totinterval=$interval->days;

/** Check for Holiday **/
$betweendates=array();
					for ($g=strtotime($startdate); $g<=strtotime($enddate); $g+=86400) {  
						$betweendates[]= date("Y-m-d", $g);  
					} 
					$alldates="'" . implode ( "','", $betweendates ) . "'";
				
				if(count($betweendates)>0)
				{
					$ifholiday=$ci->db->select('holiday_id')->from('prestogroup_holidays')->where_in('holiday_date',$alldates,false)->get();
					$holiday=$ifholiday->num_rows();
					
				}else{ $holiday=0;  }
				
			
return $totinterval-$holiday;
/** END **/

	
}

?>
