<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fms_model extends CI_Model {

	public function __construct()
	{
		
		parent::__construct();
		
	}

	public $jobcardara = array();
	
	function getprevioustimestamp($tatstage,$plannedon,$jobcardid,$flowstage,$orderstageid)
	{
		$previousorder=$tatstage;
					
				/** Check for Primary Rejection **/

					$inprocessqc=$this->db->select('backtrackto,addedOn')->from('qcremarks')->where('jobcardid',$jobcardid)->where('backtrackto !=','0')->where('neworderstageid',$orderstageid)->get();
					$inprocessqcrow=$inprocessqc->num_rows();
					
				$finalqc=$this->db->select('backtrackto,addedOn')->from('qcfinalremarks')->where('jobcardid',$jobcardid)->where('backtrackto !=','0')->where('neworderstageid',$orderstageid)->get();
					$finalqcrow=$finalqc->num_rows();

				
				/* End **/
						
	if(($inprocessqcrow==0) && ($finalqcrow==0))
		{
		
		/** Check for Dependent Data **/
		$isdependss=$this->db->select('dependentflowid')->from('flowdependency')->where('flowid',$flowstage)->get();
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
		$oldtime1=$this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$jobcardid)->order_by('id','DESC')->limit(1)->get();
		if($oldtime1->num_rows()>0){
		foreach($oldtime1->result() as $pastinfo);
			
			if($pastinfo->addedOn=='0000-00-00 00:00:00')
{
				$previouscomtime=$plannedon;
		return $previouscomdate=date('d-M-Y g:i A',strtotime($previouscomtime));

}else
{
			
		$previouscomtime=$pastinfo->addedOn;
		return $previouscomdate=date('d-M-Y g:i A',strtotime($pastinfo->addedOn));
}
		
		}else
		{
			
			$jump=$this->db->select('addedOn')->from('jumpjobcard')->where('jumpto',$flowstage)->where('jobcardid',$jobcardid)->get();
			if($jump->num_rows()>0)
			{
				foreach($jump->result() as $jump1);
				$previouscomtime=$jump1->addedOn;
		return $previouscomdate=date('d-M-Y g:i A',strtotime($previouscomtime));
				
			}else{
			
		$previouscomtime=$plannedon;
		return $previouscomdate=date('d-M-Y g:i A',strtotime($previouscomtime));
			}
		
		}
		}
		
		}else
		{
				$alldates=array();
				foreach($isdependss->result() as $dependflow)
				{
					$oldtime1=$this->db->select('addedOn')->from('order_stage')->where('flowstage',$dependflow->dependentflowid)->where('jobcardid',$jobcardid)->order_by('id','DESC')->limit(1)->get();
					
					if($oldtime1->num_rows()>0)
				{
					foreach($oldtime1->result() as $oldtime11);
				    $alldates[]=$oldtime11->addedOn;
					

							
				}else{  
						
						
					/** CHECK FOR SKIP TIME **/
			
			$restskip=$this->db->select('id,flowstage')->from('skipflow')->where('jobcardid',$jobcardid)->where('sendto',$flowstage)->order_by('id','DESC')->get();
			if($restskip->num_rows()==0)
			{
			/** END **/
			
					/** Need to check**/
						$previouscomtime=$plannedon;
						$previouscomdate=date('d-M-Y g:i A',strtotime($previouscomtime));
						 $alldates[]=$previouscomtime;

			}else{
					
					foreach($restskip->result() as $restskip1);
					$prevflow=$restskip1->flowstage;
					$prevdata=$this->db->select('addedOn')->from('order_stage')->where('jobcardid',$jobcardid)->where('flowstage',$prevflow)->get();
					if($prevdata->num_rows()>0)
					{
						foreach($prevdata->result() as $prevvd);
						$prevtime=$prevvd->addedOn;
						if($prevtime=='0000-00-00 00:00:00')
						{
						$previouscomtime=$plannedon;
						$previouscomdate=date('d-M-Y g:i A',strtotime($previouscomtime));
						$alldates[]=$previouscomtime;
						}else{
						$previouscomtime=$prevtime;
						$previouscomdate=date('d-M-Y g:i A',strtotime($previouscomtime));
						$alldates[]=$previouscomtime;
						}
					
					
					}else{
					
							$previouscomtime=$plannedon;
							$previouscomdate=date('d-M-Y g:i A',strtotime($previouscomtime));
							$alldates[]=$previouscomtime;
							
					}

			}	
			
			
			
			
			
			
					 
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
	
	
	function getprevioustimestampoldbefpredirectentry18fef20202($tatstage,$plannedon,$jobcardid,$flowstage,$orderstageid)
	{
		$previousorder=$tatstage;
					
				/** Check for Primary Rejection **/

					$inprocessqc=$this->db->select('backtrackto,addedOn')->from('qcremarks')->where('jobcardid',$jobcardid)->where('backtrackto !=','0')->where('orderstageid',$orderstageid)->get();
					$inprocessqcrow=$inprocessqc->num_rows();
					
				$finalqc=$this->db->select('backtrackto,addedOn')->from('qcfinalremarks')->where('jobcardid',$jobcardid)->where('backtrackto !=','0')->where('orderstageid',$orderstageid)->get();
					$finalqcrow=$finalqc->num_rows();

				
				/* End **/
					
	if(($inprocessqcrow==0) && ($finalqcrow==0))
		{
		
		/** Check for Dependent Data **/
		$isdependss=$this->db->select('dependentflowid')->from('flowdependency')->where('flowid',$flowstage)->get();
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
		$oldtime1=$this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$jobcardid)->order_by('id','DESC')->limit(1)->get();
		if($oldtime1->num_rows()>0){
		foreach($oldtime1->result() as $pastinfo);
			
			if($pastinfo->addedOn=='0000-00-00 00:00:00')
{
				$previouscomtime=$plannedon;
		return $previouscomdate=date('d-M-Y g:i A',strtotime($previouscomtime));

}else
{
			
		$previouscomtime=$pastinfo->addedOn;
		return $previouscomdate=date('d-M-Y g:i A',strtotime($pastinfo->addedOn));
}
		
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
					$oldtime1=$this->db->select('addedOn')->from('order_stage')->where('flowstage',$dependflow->dependentflowid)->where('jobcardid',$jobcardid)->order_by('id','DESC')->limit(1)->get();
					
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
	
	function getupcomingtatdatenew($tatstage,$plannedon,$jobcardid,$timestamp,$daysslab,$orderstageid,$day)
	{
	    
	    
	    $tat='';
	    $restyu=$this->db->select('tasktat')->from('fmstatdate')->where('orderstageid',$orderstageid)->where('jobcardid',$jobcardid)->get();
	    if($restyu->num_rows()>0)
	    {
	        foreach($restyu->result() as $restyu1);
	        
	        $tatdate=date('d-M-Y g:i A',strtotime($restyu1->tasktat));
	        
	        return $tatdate;
	        
	    }else{ return $tat; }
	    
	    
	}
	
		function getupcomingtatdate($tatstage,$plannedon,$jobcardid,$timestamp,$daysslab,$orderstageid,$day)
	{
	
$officestarttime="9:30";
$officeendtime="18:00";		
	$datetime = DateTime::createFromFormat('d-M-Y g:i A', $timestamp);
	//$TATDATE1=$datetime->format('Y-m-d');
	$TATDATE1=date('Y-m-d',strtotime($timestamp));
	if($day==1)
	{
	    
	$TATDATE=date('Y-m-d',strtotime($TATDATE1."+".$daysslab." days"));
	$newtime=date('g:i A',strtotime($timestamp));
	}else{
	/** HOURS DATA **/


$previoussteptime=date('H:i',strtotime($timestamp));
$tattime = date('H:i', strtotime($previoussteptime.'+'.$daysslab.' hour'));
if(strtotime($officeendtime)<strtotime($tattime))
{
	/** TAT IS GREATER **/
	$timediff=round(abs(strtotime($tattime)-strtotime($officeendtime))/60,2);
	$TATDATE=date('Y-m-d',strtotime($TATDATE1."+1 days"));
	$newtime=date('g:i A',strtotime('+'.$timediff.' minutes',strtotime($officestarttime)));
}else
{
	/** NOT GREATER **/
	$TATDATE=$TATDATE1;
	$newtime=date('g:i A',strtotime($tattime));
}

   /** END **/	
		
	}
	
		$previousorder=$tatstage;
				if($previousorder==0)
				{
					$previouscomtime=$plannedon;
					$TATDATE1=date('d-M-Y',strtotime($previouscomtime));
						if($day==1)
						{
						$TATDATE=date('Y-m-d',strtotime($TATDATE1."+".$daysslab." days"));
						}else{
						
						$previoussteptime=date('H:i',strtotime($timestamp));
						$tattime = date('H:i', strtotime($previoussteptime.'+'.$daysslab.' hour'));
						if(strtotime($officeendtime)<strtotime($tattime))
						{
						/** TAT IS GREATER **/
						$timediff=round(abs(strtotime($tattime)-strtotime($officeendtime))/60,2);
						$TATDATE=date('Y-m-d',strtotime($TATDATE1."+1 days"));
						$newtime=date('g:i A',strtotime('+'.$timediff.' minutes',strtotime($officestarttime)));
						}else
						{
						/** NOT GREATER **/
						$TATDATE=$TATDATE1;
						$newtime=date('g:i A',strtotime($tattime));
						}

						/** END **/	

						}
	
	
					
					
				}else
				{
					//echo $previousorder;exit;
					$oldtime1=$this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$jobcardid)->order_by('id','DESC')->limit(1)->get();
							if($oldtime1->num_rows()>0){

								foreach($oldtime1->result() as $pastinfo);
								
							
							if($pastinfo->addedOn=='0000-00-00 00:00:00')
{
								/** Check for Primary Rejection **/

					$inprocessqc=$this->db->select('backtrackto,addedOn')->from('qcremarks')->where('jobcardid',$jobcardid)->where('backtrackto !=','0')->where('orderstageid',$orderstageid)->get();
					$inprocessqcrow=$inprocessqc->num_rows();
					
				$finalqc=$this->db->select('backtrackto,addedOn')->from('qcfinalremarks')->where('jobcardid',$jobcardid)->where('backtrackto !=','0')->where('orderstageid',$orderstageid)->get();
					$finalqcrow=$finalqc->num_rows();
	if(($inprocessqcrow==0) && ($finalqcrow==0))
		{
		
 $previouscomtime=$plannedon;
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
								
								$previouscomtime=$plannedon;
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
					
					$ifholiday=$this->db->select('holiday_id')->from('prestogroup_holidays')->where_in('holiday_date',$alldates,false)->get();
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
				
				
				
					$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$TATDATEFORHOLIDAYCHECK)->get();
			$chhutti=array();
			if($query->num_rows()>0){
				
			foreach($query->result() as $holidays);
				$nextdate= date('Y-m-d', strtotime($holidays->holiday_date."+1 days"));
				$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();
				if($query->num_rows()>0){
					
					foreach($query->result() as $afterholiday);
					$aftrholidaydate = $afterholiday->holiday_date;
					$TATDATE= date('d-M-Y', strtotime($aftrholidaydate."+1 days"));
					return $TATDATE." ".$newtime;
					
				}else {
				$TATDATE= date('d-M-Y', strtotime($nextdate));
				return $TATDATE." ".$newtime;
				}
			}else{
			$TATDATE=date('d-M-Y', strtotime($TATDATEFORHOLIDAYCHECK));
			return $TATDATE." ".$newtime;
			}
			
		
		
		
		
		
	}
	
	
		function getupcomingtatdateOLDDBEFOREHOUR12MRACH2020($tatstage,$plannedon,$jobcardid,$timestamp,$daysslab,$orderstageid)
	{
			
	$datetime = DateTime::createFromFormat('d-M-Y g:i A', $timestamp);
		$TATDATE1=date('Y-m-d',strtotime($timestamp));
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
					$oldtime1=$this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$jobcardid)->order_by('id','DESC')->limit(1)->get();
							if($oldtime1->num_rows()>0){

								foreach($oldtime1->result() as $pastinfo);
								
								
							if($pastinfo->addedOn=='0000-00-00 00:00:00')
{
								/** Check for Primary Rejection **/

					$inprocessqc=$this->db->select('backtrackto,addedOn')->from('qcremarks')->where('jobcardid',$jobcardid)->where('backtrackto !=','0')->where('orderstageid',$orderstageid)->get();
					$inprocessqcrow=$inprocessqc->num_rows();
					
				$finalqc=$this->db->select('backtrackto,addedOn')->from('qcfinalremarks')->where('jobcardid',$jobcardid)->where('backtrackto !=','0')->where('orderstageid',$orderstageid)->get();
					$finalqcrow=$finalqc->num_rows();
	if(($inprocessqcrow==0) && ($finalqcrow==0))
		{
		
 $previouscomtime=$plannedon;
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
								
								$previouscomtime=$plannedon;
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
					
					$ifholiday=$this->db->select('holiday_id')->from('prestogroup_holidays')->where_in('holiday_date',$alldates,false)->get();
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
				
				
				
					$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$TATDATEFORHOLIDAYCHECK)->get();
			$chhutti=array();
			if($query->num_rows()>0){
				
			foreach($query->result() as $holidays);
				$nextdate= date('Y-m-d', strtotime($holidays->holiday_date."+1 days"));
				$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();
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
					$ifholiday=$this->db->select('holiday_id')->from('prestogroup_holidays')->where_in('holiday_date',$alldates,false)->get();
					$holiday=$ifholiday->num_rows();
					
				}else{ $holiday=0;  }
				
			
return $totinterval-$holiday;
/** END **/

	
}
	
	
	function checkholiday($startdate,$enddate){
/** Check for Holiday **/
$betweendates=array();
for ($g=strtotime($startdate); $g<=strtotime($enddate); $g+=86400) {  
$betweendates[]= date("Y-m-d", $g);  
}
$alldates="'" . implode ( "','", $betweendates ) . "'";

if(count($betweendates)>0)
{
$ifholiday=$this->db->select('holiday_id')->from('prestogroup_holidays')->where_in('holiday_date',$alldates,false)->get();
$holiday=$ifholiday->num_rows();

}else{ $holiday=0;  }


return $holiday;
/** END **/
}
	
	
	
	function checkforavailabilityofdependentflow($flowstage,$jobcard,$orderid,$fabreq)
{
$fabflow=array();
$fabricadlow='';
		if($fabreq=='1')
		{
$restfab=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id','5')->get();
			if($restfab->num_rows()>0)
			{
				foreach($restfab->result() as $restfabflow)
				{
					$fabflow[]=$restfabflow->flow_id;
				}
				
			}
		}
		
		if(count($fabflow)>0)
		{
		$fabricadlow= "'" . implode ( "', '", $fabflow ) . "'";	
		}
	$arry=array();
	$this->db->select('dependentflowid')->from('flowdependency')->where('flowid',$flowstage);
	if($fabricadlow<>'')
	{
		$this->db->where_not_in('dependentflowid',$fabricadlow,false);
		
	}
		
	$dependflow=$this->db->get();
	if($dependflow->num_rows()>0)
	{
		foreach($dependflow->result() as $dependflow1)
		{
			$rest=$this->db->select('id')->from('order_stage')->where('jobcardid',$jobcard)->where('flowstage',$dependflow1->dependentflowid)->get();
			if($rest->num_rows()==0)
			{
				$arry[]=$dependflow1->dependentflowid;	
				
			
			}
	}
		

	
	}
	

		return $arry;
}
	
	
	
	function gettatformis($orderstageid,$flow_id,$isfinal,$jobcardid,$orderid)
	{
	

		$officestarttime="9:30";
$officeendtime="18:00";	

if($isfinal=='0')
{
		/** Get TAT FROM AND SLAB **/
		$QRY = $this->db->select('total_days,tat,set_time, setorder')->from('fms_flow')->where('flow_id',$flow_id)->get();
			$res = $QRY->result();
			foreach($res as $fmsinformation);
			if($fmsinformation->set_time<>0)
			{
			$totaldaysslave = $fmsinformation->set_time;
			$daysslab=$fmsinformation->set_time;
			$day=0;
			}else{
				$totaldaysslave = $fmsinformation->total_days;
				$daysslab=$fmsinformation->total_days;
				$day=1;
			}
			
		
			$tatstage = $fmsinformation->tat;
			
	
	$getdates=$this->db->select('plannedon')->from('order_planning')->where('jobcard_id',$jobcardid)->where('order_id',$orderid)->get();
				if($getdates->num_rows()>0)
				{
					foreach($getdates->result() as $getdates1);
					$plannedon=$getdates1->plannedon;
				}else{ echo "Planning Data not available";exit; }
				
			if($tatstage==0)
			{
				
				$previouscomtime=$plannedon;
				$newtime=date('H:i:s',strtotime($previouscomtime));
				$TATDATE1=date('d-M-Y',strtotime($previouscomtime));
				if($day==1)
						{
						$TATDATE=date('Y-m-d',strtotime($TATDATE1."+".$daysslab." days"));
						}else{
							
                        $todaysdate=date('Y-m-d');
                        $officestarttime=date('Y-m-d',strtotime($todaysdate."+1 days"))." "."9:30";
                        $officestime="9:30";
                        $officeendtime=date('Y-m-d')." 18:00";
                        $officeetime="18:00";
        
								$previoussteptime=date('H:i',strtotime($previouscomtime));
						$tattime = date('Y-m-d H:i', strtotime($previoussteptime.'+'.$daysslab.' hour'));
						if(strtotime($officeendtime)<strtotime($tattime))
						{
						/** TAT IS GREATER **/
						$timediff=round(abs(strtotime($tattime)-strtotime($officeendtime))/60,2);
						$TATDATE=date('Y-m-d',strtotime($TATDATE1."+1 days"));
						$newtime=date('g:i A',strtotime('+'.$timediff.' minutes',strtotime($officestarttime)));
						}else
						{
						/** NOT GREATER **/
						$TATDATE=$TATDATE1;
						$newtime=date('g:i A',strtotime($tattime));
						}
					
							
						}
					
				
				
			}else
			{
			    $previouscomtime=$this->getprevioustimestampformis($tatstage,$plannedon,$jobcardid,$flow_id,$orderstageid);
			
				$newtime=date('g:i A',strtotime($previouscomtime));
				$TATDATE1=date('d-M-Y',strtotime($previouscomtime));
				if($day==1)
						{
						$TATDATE=date('Y-m-d',strtotime($TATDATE1."+".$daysslab." days"));
						}else{
						
						$todaysdate=date('Y-m-d');
                        $officestarttime=date('Y-m-d',strtotime($todaysdate."+1 days"))." "."9:30";
                        $officestime="9:30";
                        $officeendtime=date('Y-m-d')." 18:00";
                        $officeetime="18:00";
                        
						$previoussteptime=date('H:i',strtotime($previouscomtime));
						$tattime = date('Y-m-d H:i', strtotime($previoussteptime.'+'.$daysslab.' hour'));
						if(strtotime($officeendtime)<strtotime($tattime))
						{
						/** TAT IS GREATER **/
						$timediff=round(abs(strtotime($tattime)-strtotime($officeendtime))/60,2);
						$TATDATE=date('Y-m-d',strtotime($TATDATE1."+1 days"));
						$newtime=date('g:i A',strtotime('+'.$timediff.' minutes',strtotime($officestarttime)));
						}else
						{
						/** NOT GREATER **/
						$TATDATE=$TATDATE1;
						$newtime=date('g:i A',strtotime($tattime));
						}

						/** END **/	

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

			$ifholiday=$this->db->select('holiday_id')->from('prestogroup_holidays')->where_in('holiday_date',$alldates,false)->get();
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
			
			$convertdate=date('Y-m-d',strtotime($TATDATEFORHOLIDAYCHECK));
				$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$convertdate)->get();
				$chhutti=array();
				if($query->num_rows()>0){

				foreach($query->result() as $holidays);
				$nextdate= date('Y-m-d', strtotime($holidays->holiday_date."+1 days"));
				$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();
				if($query->num_rows()>0){

				foreach($query->result() as $afterholiday);
				$aftrholidaydate = $afterholiday->holiday_date;
				$TATDATE= date('d-M-Y', strtotime($aftrholidaydate."+1 days"));
				return $TATDATE." ".$newtime;

				}else {
				$TATDATE= date('d-M-Y', strtotime($nextdate));
				return $TATDATE." ".$newtime;
				}
				}else{
				$TATDATE=date('d-M-Y', strtotime($TATDATEFORHOLIDAYCHECK));
				return $TATDATE." ".$newtime;
				}
		
		}	
		}
		
		
	function gettatformisolddbeforehours12march2020($orderstageid,$flow_id,$isfinal,$jobcardid,$orderid)
	{
		
if($isfinal=='0')
{
		/** Get TAT FROM AND SLAB **/
		$QRY = $this->db->select('total_days,tat')->from('fms_flow')->where('flow_id',$flow_id)->get();
			$res = $QRY->result();
			foreach($res as $fmsinformation);
			$daysslab = $fmsinformation->total_days;
			$tatstage = $fmsinformation->tat;
	
	$getdates=$this->db->select('plannedon')->from('order_planning')->where('jobcard_id',$jobcardid)->where('order_id',$orderid)->get();
				if($getdates->num_rows()>0)
				{
					foreach($getdates->result() as $getdates1);
					$plannedon=$getdates1->plannedon;
				}else{ echo "Planning Data not available";exit; }
				
			if($tatstage==0)
			{
				
				$previouscomtime=$plannedon;
				$finaltime=date('H:i:s',strtotime($previouscomtime));
				$TATDATE1=date('d-M-Y',strtotime($previouscomtime));
				$TATDATE=date('Y-m-d',strtotime($TATDATE1."+".$daysslab." days"));
					
				
				
			}else
			{
			    $previouscomtime=$this->getprevioustimestampformis($tatstage,$plannedon,$jobcardid,$flow_id,$orderstageid);
				
				$finaltime=date('g:i A',strtotime($previouscomtime));
				$TATDATE1=date('d-M-Y',strtotime($previouscomtime));
				$TATDATE=date('Y-m-d',strtotime($TATDATE1."+".$daysslab." days"));
				
				
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

			$ifholiday=$this->db->select('holiday_id')->from('prestogroup_holidays')->where_in('holiday_date',$alldates,false)->get();
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
			
			
				$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$TATDATEFORHOLIDAYCHECK)->get();
				$chhutti=array();
				if($query->num_rows()>0){

				foreach($query->result() as $holidays);
				$nextdate= date('Y-m-d', strtotime($holidays->holiday_date."+1 days"));
				$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();
				if($query->num_rows()>0){

				foreach($query->result() as $afterholiday);
				$aftrholidaydate = $afterholiday->holiday_date;
				$TATDATE= date('d-M-Y', strtotime($aftrholidaydate."+1 days"));
				return $TATDATE." ".$finaltime;

				}else {
				$TATDATE= date('d-M-Y', strtotime($nextdate));
				return $TATDATE." ".$finaltime;
				}
				}else{
				$TATDATE=date('d-M-Y', strtotime($TATDATEFORHOLIDAYCHECK));
				return $TATDATE." ".$finaltime;
				}
		
		}	
		}
		
		
		
		function getprevioustimestampformis($tatstage,$plannedon,$jobcardid,$flowstage,$orderstageid)
	{
		$previousorder=$tatstage;
					
				/** Check for Primary Rejection **/

					$inprocessqc=$this->db->select('backtrackto,addedOn')->from('qcremarks')->where('jobcardid',$jobcardid)->where('backtrackto !=','0')->where('neworderstageid',$orderstageid)->get();
					$inprocessqcrow=$inprocessqc->num_rows();
					
				$finalqc=$this->db->select('backtrackto,addedOn')->from('qcfinalremarks')->where('jobcardid',$jobcardid)->where('backtrackto !=','0')->where('neworderstageid',$orderstageid)->get();
					$finalqcrow=$finalqc->num_rows();

				
				/* End **/
					
	if(($inprocessqcrow==0) && ($finalqcrow==0))
		{
		
		/** Check for Dependent Data **/
		$isdependss=$this->db->select('dependentflowid')->from('flowdependency')->where('flowid',$flowstage)->get();
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
		$oldtime1=$this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$jobcardid)->order_by('id','DESC')->limit(1)->get();
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
					$oldtime1=$this->db->select('addedOn')->from('order_stage')->where('flowstage',$dependflow->dependentflowid)->where('jobcardid',$jobcardid)->order_by('id','DESC')->limit(1)->get();
					
					if($oldtime1->num_rows()>0)
				{
					foreach($oldtime1->result() as $oldtime11);
				    $alldates[]=$oldtime11->addedOn;
					

							
				}else{  
						
						/** CHECK FOR SKIP TIME **/
			
			$restskip=$this->db->select('id,flowstage')->from('skipflow')->where('jobcardid',$jobcardid)->where('sendto',$flowstage)->order_by('id','DESC')->get();
			if($restskip->num_rows()==0)
			{
			/** END **/
			
					/** Need to check**/
						$previouscomtime=$plannedon;
						$previouscomdate=date('d-M-Y g:i A',strtotime($previouscomtime));
						 $alldates[]=$previouscomtime;

			}else{
					
					foreach($restskip->result() as $restskip1);
					$prevflow=$restskip1->flowstage;
					$prevdata=$this->db->select('addedOn')->from('order_stage')->where('jobcardid',$jobcardid)->where('flowstage',$prevflow)->get();
					if($prevdata->num_rows()>0)
					{
						foreach($prevdata->result() as $prevvd);
						$prevtime=$prevvd->addedOn;
						if($prevtime=='0000-00-00 00:00:00')
						{
						$previouscomtime=$plannedon;
						$previouscomdate=date('d-M-Y g:i A',strtotime($previouscomtime));
						$alldates[]=$previouscomtime;
						}else{
						$previouscomtime=$prevtime;
						$previouscomdate=date('d-M-Y g:i A',strtotime($previouscomtime));
						$alldates[]=$previouscomtime;
						}
					
					
					}else{
					
							$previouscomtime=$plannedon;
							$previouscomdate=date('d-M-Y g:i A',strtotime($previouscomtime));
							$alldates[]=$previouscomtime;
							
					}

			}	
			 
					 
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
	
	function getselforder($orderid)
	{
		$oid=$this->db->select('selforder')->from('prestogroup_orders')->where('order_id',$orderid)->get();
		if($oid->num_rows()>0)
		{
			foreach($oid->result() as $ooid);
			return $ooid->selforder;
			
		}else{
			
			return 0;
		}
	}
	
	function addstock($jobcardid)
	{
		$sttock=$this->db->select('a.item_id,b.stock')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->where('a.id',$jobcardid)->get();
		if($sttock->num_rows()>0)
		{
			foreach($sttock->result() as $sttock11)
			{
				$stock=$sttock11->stock;
				$newstock=$stock+1;
				$upd=array('stock'=>$newstock);
				$this->db->where('id',$sttock11->item_id);
				$this->db->update('presto_instruments',$upd);
				
			}
			
			
		}
		
	}
	
	
	
	function nextinqueuecount($flowstage)
	{
		$QRY = $this->db->select('tat,dependency')->from('fms_flow')->where('flow_id',$flowstage)->get();
	$res = $QRY->result();
	foreach($res as $fmsinformation);
	$tatstage = $fmsinformation->tat;
	$dependent=$fmsinformation->dependency;
	if($dependent==0)
	{
	if($tatstage==0)
	{
		return 0;
	}else{
		
		$cou=$this->db->select('id')->from('order_stage')->where('flowstage',$tatstage)->where('userstatus','0')->get();
		
		return $cou->num_rows();
		
	}
	}else
	{
		if($tatstage==0)
	{
		return 0;
	}else{
		$availableflowstage=array();
					$ifdependexist=$this->db->select('a.dependentflowid')->from('flowdependency a')->where('flowid',$flowstage)->get();
					if($ifdependexist->num_rows()>0)
					{
						foreach($ifdependexist->result() as $ifdependexist1)
						{
							$availableflowstage[]=$ifdependexist1->dependentflowid;
						}
						
					}
					
					if(count($availableflowstage)>0)
					{
					$availableflowstages = "'" . implode ( "', '", $availableflowstage ) . "'";
					
					$cou=$this->db->select('id')->from('order_stage')->where_in('flowstage',$availableflowstages,false)->where('userstatus','0')->get();
		
		return $cou->num_rows();
					}else
					{
return 0;
					}						
					
					
	}		
					
		
	}
		
	}
	
	
	
	function getnotrecievedorder($userid)
	{
		
	$odplan=$this->db->select('a.id')->from('order_planning_remarks a')->where('a.status','0')->get();
		return $odplan->num_rows();
		
	}
	
	
	function getremakrsonnotrecievedorder($userid)
	{
		
		$odplan=$this->db->select('a.remarks,e.id')->from('order_planning_remarks
 a')->join('order_planning e','a.planid=e.id')->join('prestogroup_orders b','e.order_id=b.order_id')->where_in('e.orderstatus','2,3',false)->where('e.plannedby',$userid)->get();
		
		return $odplan->num_rows();
		
	}
	
	
		function menunotifications($flow_id,$dependency)
	{
			$complete=array();
				$complete[]=0;
				
				if($flow_id<>'36' && $flow_id<>'35')
				{
				
				/** Check if Task is assigned **/
				$resyut=$this->db->select('count(id) as taskcount')->from('order_stage')->where('flowstage',$flow_id)->where('userstatus','0')->get();

				foreach($resyut->result() as $resyutui);

				if($dependency==1)
				{
				$restt=$this->db->select('dependentflowid')->from('flowdependency')->where('flowid',$flow_id)->get();
				if($restt->num_rows()>0)
				{	
				foreach($restt->result() as $resttyu)
				{
				$donarr[]=$resttyu->dependentflowid;
				}
				}

				if(count($donarr)>0)
				{

				$alljobcardid=$this->db->select('jobcardid')->from('order_stage')->where('flowstage',$flow_id)->where('userstatus','0')->get();
				if($alljobcardid->num_rows()>0)
				{
				
				foreach($alljobcardid->result() as $alljob)
				{
				$availableflow=array();
				for($t=0;$t<count($donarr);$t++)
				{

				$restysys=$this->db->select('id')->from('order_stage')->where('flowstage',$donarr[$t])->where('jobcardid',$alljob->jobcardid)->get(); 
				if($restysys->num_rows()>0)
				{
				$availableflow[]=$donarr[$t];
				}
				}

				if(count($availableflow)>0)
				{
				$allflow = "'" . implode ( "', '", $availableflow ) . "'";
				$rest=$this->db->select('id')->from('order_stage')->where_in('flowstage',$allflow,false)->where('jobcardid',$alljob->jobcardid)->where('userstatus','1')->get();
				$alldone=$rest->num_rows();
				if($alldone==count($availableflow))
				{
				$complete[]=1;
				}else
				{
				$complete[]=0;
				}

				}else{

				$complete[]=1;
				}
}
}else{

				$dependcount=0;
				}
				}
				}


				if($dependency==0)
				{	  
				if($resyutui->taskcount==0)
				{
				$flowtottask=0;
				return $flowtottask;
				}else
				{
				$flowtottask= $resyutui->taskcount;
				return $flowtottask;

				}

				}else{

				$flowtottask= array_sum($complete);
				return $flowtottask;
				}
				
		}else
		{
		   
		   
		    
		    
		}
		
		
		
	}
	
	
	function flowassignedtouser($userid,$production_flow)
	{
		$flows=array();
		$rest=$this->db->select('flow_id')->from('fms_flow')->where('who_wedo',$userid)->where_in('production_flow_id',$production_flow,false)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $rest1)
			{
				$flows[]=$rest1->flow_id;
			}
			
			
			return $flows;
			
		}else{
			
			return $flows;
		}
		
		
	}
	
	
	
		function checkformergerpoint($flowid,$jobcardid,$orderid,$fabreq,$jumpfabricationappl)
{
	
	
	$rest=$this->db->select('id,productionflow,mergewith')->from('fmsmerge')->where('flowid',$flowid)->get();
	if($rest->num_rows()>0)
	{
		if($fabreq==1)
		{
		foreach($rest->result() as $rest1);
		
		/** GET ALL FMS for merged fms **/
		$resty=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id',$rest1->productionflow)->order_by('setorder','DESC')->limit(1)->get();
		if($resty->num_rows()>0)
		{ 
			foreach($resty->result() as $resty1);
			
				$newflowid=$resty1->flow_id;
			
			if($jumpfabricationappl=='1')
			{
			$isthere=$this->db->select('a.id')->from('order_stage a')->join('qcremarks b','a.id=b.orderstageid')->where('a.flowstage',$newflowid)->where('a.jobcardid',$jobcardid)->where('a.orderid',$orderid)->where('a.userstatus','1')->where('b.qcstatus','1')->order_by('a.id','DESC')->limit(1)->get();
			if($isthere->num_rows()>0)
			{
				$markup=1;
				return $markup; 
			}else{
				$markup=0;
				return $markup; 
			}
			}else
			{
			   
			/** CHECK IF THERE IS DATA FOR ALL DEPENDENT PRODUCTION FLOWS FLOW ID **/   
			   $sourcepid=$rest1->mergewith;
			   $tobemergedpid=$rest1->productionflow;
			   $tobemergedlastflowid=$newflowid;
			   $allsourceflowpid=$this->getallproductionflowids($sourcepid);
			   $alltobemergedflowpid=$this->getallproductionflowids($tobemergedpid);
			   
			   $checkfordata=$this->db->select('id')->from('order_stage')->where('jobcardid',$jobcardid)->where('orderid',$orderid)->where_in('flowstage',$alltobemergedflowpid,false)->get();
			   
			   if($checkfordata->num_rows()>0)
			   {
			       		$isthere=$this->db->select('a.id')->from('order_stage a')->join('qcremarks b','a.id=b.orderstageid')->where('a.flowstage',$newflowid)->where('a.jobcardid',$jobcardid)->where('a.orderid',$orderid)->where('a.userstatus','1')->where('b.qcstatus','1')->order_by('a.id','DESC')->limit(1)->get();
			if($isthere->num_rows()>0)
			{
				$markup=1;
				return $markup; 
			}else{
				$markup=0;
				return $markup; 
			}
			   
			   }else
			   {
			   /** END **/
				
				$markup=1;
				return $markup; 
			   }
			}
			
			
		}else{ $markup=1;
		return $markup;  }
		/** End **/
		
	}else{
	$markup=1;
		return $markup;	
	}
	
}else{
		
		$markup=1;
		return $markup;
	}
	
	
}


	function checkformergerpointOlddbeforebetaedit($flowid,$jobcardid,$orderid,$fabreq,$jumpfabricationappl)
{
	
	
	$rest=$this->db->select('id,productionflow')->from('fmsmerge')->where('flowid',$flowid)->get();
	if($rest->num_rows()>0)
	{
		if($fabreq==1)
		{
		foreach($rest->result() as $rest1);
		
		/** GET ALL FMS for merged fms **/
		$resty=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id',$rest1->productionflow)->order_by('setorder','DESC')->limit(1)->get();
		if($resty->num_rows()>0)
		{ 
			foreach($resty->result() as $resty1);
			
				$newflowid=$resty1->flow_id;
			
			if($jumpfabricationappl=='1')
			{
			$isthere=$this->db->select('a.id')->from('order_stage a')->join('qcremarks b','a.id=b.orderstageid')->where('a.flowstage',$newflowid)->where('a.jobcardid',$jobcardid)->where('a.orderid',$orderid)->where('a.userstatus','1')->where('b.qcstatus','1')->order_by('a.id','DESC')->limit(1)->get();
			if($isthere->num_rows()>0)
			{
				$markup=1;
				return $markup; 
			}else{
				$markup=0;
				return $markup; 
			}
			}else
			{
				$markup=1;
				return $markup; 
			}
			
			
		}else{ $markup=1;
		return $markup;  }
		/** End **/
		
	}else{
	$markup=1;
		return $markup;	
	}
	
}else{
		
		$markup=1;
		return $markup;
	}
	
	
}


function getplanstartsfromorderno($flowid)
{
	$fmsflow=$this->db->select('setorder')->from('fms_flow')->where('flow_id',$flowid)->get();
	if($fmsflow->num_rows()>0)
	{
		foreach($fmsflow->result() as $fmsflow1);
		$setorder=$fmsflow1->setorder;
		return $setorder;
	}else
	{
		$setorder='0';
		return $setorder;
	}
	
}


function getproductionflowname($factory)
{
	
	$reslongre=$this->db->select('production_flow')->from('production_flow')->where('id',$factory)->get();
										if($reslongre->num_rows()>0)
										{
										foreach($reslongre->result() as $reslongre1)
										
										return $reslongre1->production_flow;
										
										}else{
											
											echo "NO PRODUCTION FLOW FOUND";exit;
										}
	
	
}
	
	
	function getallproductionflowids($pid)
{
    $allflowid=array();
   $resty=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id',$pid)->get();
   if($resty->num_rows()>0)
   {
       foreach($resty->result() as $resty1)
       {
           $allflowid[]=$resty1->flow_id;
       }
       
   }else
   {
       $allflowid[]=0;
       
   }
    
    
    $result = "'" . implode ( "', '", $allflowid ) . "'";
    
    return $result;
    
}
	


function checkifmsmerge($flowid)
{
	
	$restyu=$this->db->select('id')->from('fmsmerge')->where('productionflow',$flowid)->get();
	
	return $restyu->num_rows();
	
	
}
	
	
	
	function menunotificationsformergeflow($flowid,$productionflow)
	{
		
		$arr[]=0;
			$flowstage=$flowid;
			$productionflowid=$productionflow;
			$user_id =$this->session->userdata['logged_in']['user_id'];
			$fmsprocess=$productionflowid;
		if($fmsprocess<>0)
		{
		/** Get Process Name **/
		$process=$this->db->select('a.jumpfabricationappl,a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow,d.fabrication,f.planstartsfrom')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('b.userstatus','0')->where('a.who_wedo',$user_id)->order_by('b.userstatus','ASC')->order_by('b.addedOn','DESC')->get();
		/** End **/
		if($process->num_rows()>0)
		{
			//	echo "<pre>"; print_r($process->result());exit;
			$i=1;
			foreach($process->result() as $proc)
			{
				
				
				/** Check for dependency **/
				
				$checkdepend=$this->db->select('dependency')->from('fms_flow')->where('flow_id',$flowstage)->where('dependency','1')->get();
				$depend=$checkdepend->num_rows();
				//echo $depend;exit;
				if($depend==1)
				{
					/** Check if Dependent flow has any data in orderstage **/
					$availableflowstage=array();
				/** Check if plan has been started from setorder 1 **/
				$plannedstageorderno=$this->getplanstartsfromorderno($proc->planstartsfrom);
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
					
					/** End **/
					
					/** Check if instrument requires fabrication **/
					/**$ryt=$this->db->select('b.fabrication')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->where('a.id',$proc->jobcardid)->get();
					if($ryt->num_rows()>0)
					{
						foreach($ryt->result() as $rtyeyte);
						$fabreq=$rtyeyte->fabrication;
						
					}else{ $fabreq=0 ;} **/
					
					/** Get Last Step of Fabrication **/
					/**if($fabreq==0)
							{
						
								$isffin=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id','5')->where('finalstep','1')->get();
								$isfinal=$isffin->num_rows();
								if($isfinal!=0)
								{
									foreach($isffin->result() as $isffin1);
									$ignoreflow=$isffin1->flow_id;
								}
												
							}else{ $isfinal=0; } **/
					/** End **/
					
				
					
if(count($availableflowstage)>0)
					{	
				
					
				if($plannedstageorderno!='1')
				{
				$this->db->select('a.dependentflowid')->from('flowdependency a')->join('order_stage b','a.dependentflowid=b.flowstage')->where('a.flowid',$flowstage);

					//$availableflowstages = "'" . implode ( "', '", $availableflowstage ) . "'";

					//$this->db->where_in('a.dependentflowid',$availableflowstages,false);

					$this->db->where('b.jobcardid',$proc->jobcardid)->where('b.orderid',$proc->originalorderid);
				}else
				{
				    $this->db->select('a.dependentflowid')->from('flowdependency a')->where('a.flowid',$flowstage);
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
					
				$markapp=$this->checkformergerpoint($flowstage,$proc->jobcardid,$proc->originalorderid,$proc->fabrication,$proc->jumpfabricationappl);
				}
				

				
				/** End **/
				
				if($markapp==1)
				{
			  
					
					$arr[]=1;
					
				}			
										  
										  
		
			}
		}
		
		
	}
		
		return array_sum($arr);
		
		
	}	
	
	
	
	function menunotificationsformergeflowoverall($flowid,$productionflow)
	{
		$arr[]=0;
			$flowstage=$flowid;
			$productionflowid=$productionflow;
			$user_id =$this->session->userdata['logged_in']['user_id'];
			$fmsprocess=$productionflowid;
		if($fmsprocess<>0)
		{
		/** Get Process Name **/
		$process=$this->db->select('a.jumpfabricationappl,a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow,d.fabrication,f.planstartsfrom')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('b.userstatus','0')->get();
		/** End **/
		if($process->num_rows()>0)
		{
			//	echo "<pre>"; print_r($process->result());exit;
			$i=1;
			foreach($process->result() as $proc)
			{
				
				
				/** Check for dependency **/
				
				$checkdepend=$this->db->select('dependency')->from('fms_flow')->where('flow_id',$flowstage)->where('dependency','1')->get();
				$depend=$checkdepend->num_rows();
				//echo $depend;exit;
				if($depend==1)
				{
					/** Check if Dependent flow has any data in orderstage **/
					$availableflowstage=array();
				/** Check if plan has been started from setorder 1 **/
				$plannedstageorderno=$this->getplanstartsfromorderno($proc->planstartsfrom);
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
					
					
					
					$checkskiiped=$this->checkifdatahascomeskipped($proc->jobcardid,$flowstage);
					
					if($checkskiiped>0)
					{
						$availableflowstage = array_diff($availableflowstage,$checkskiiped);
					}
					
				}
					
					/** End **/
					
					/** Check if instrument requires fabrication **/
					/**$ryt=$this->db->select('b.fabrication')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->where('a.id',$proc->jobcardid)->get();
					if($ryt->num_rows()>0)
					{
						foreach($ryt->result() as $rtyeyte);
						$fabreq=$rtyeyte->fabrication;
						
					}else{ $fabreq=0 ;} **/
					
					/** Get Last Step of Fabrication **/
					/**if($fabreq==0)
							{
						
								$isffin=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id','5')->where('finalstep','1')->get();
								$isfinal=$isffin->num_rows();
								if($isfinal!=0)
								{
									foreach($isffin->result() as $isffin1);
									$ignoreflow=$isffin1->flow_id;
								}
												
							}else{ $isfinal=0; } **/
					/** End **/
					
				
					
if(count($availableflowstage)>0)
					{	
				
				$availableflowstages = "'" . implode ( "', '", $availableflowstage ) . "'";
				if($plannedstageorderno!='1')
				{
				$this->db->select('a.dependentflowid')->from('flowdependency a')->join('order_stage b','a.dependentflowid=b.flowstage')->where('a.flowid',$flowstage);

					//$availableflowstages = "'" . implode ( "', '", $availableflowstage ) . "'";

					//$this->db->where_in('a.dependentflowid',$availableflowstages,false);

					$this->db->where('b.jobcardid',$proc->jobcardid)->where('b.orderid',$proc->originalorderid);
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
					
				$markapp=$this->checkformergerpoint($flowstage,$proc->jobcardid,$proc->originalorderid,$proc->fabrication,$proc->jumpfabricationappl);
				}
				

				
				/** End **/
				
				if($markapp==1)
				{
			  
					
					$arr[]=1;
					
				}			
										  
										  
		
			}
		}
		
		
	}
		
		return array_sum($arr);
		
		
	}	
	
	
	
	function checkifdatahascomeskipped($jobcard,$flowstage)
	{
		$sday=array();
		
		$restyu=$this->db->select('skippedflow')->from('skipflow')->where('jobcardid',$jobcard)->where('sendto',$flowstage)->get();
		if($restyu->num_rows()>0)
		{
			foreach($restyu->result() as $restyu1)
			{
				$sday[]=$restyu1->skippedflow+1;
			}
			
		}
		
		return $sday;
		
		
	}
	
	function checkifstepisskippable($flowstage)
	{
	    
	    	$restyu=$this->db->select('flow_id')->from('fms_flow')->where('skiprecepitent','1')->where('flow_id',$flowstage)->get();
	
	    
	    return $restyu->num_rows();
	    
	}
	
	
	
		function getzones()
		{
		$resty=$this->db->select('zone,id')->from('saleszone')->order_by('zone','ASC')->get();
		return $resty->result();

		}
		
		function getzonename($zoneid)
		{
		    $z='';
		    	$resty=$this->db->select('zone')->from('saleszone')->where('id',$zoneid)->get();
		    if($resty->num_rows()>0)
		    {
		        foreach($resty->result() as $resty123);
		        
		        $z=$resty123->zone;
		    }
		    
		    return $z;
		}

	
	
	function dashboardplannedtoactual($productionflow,$flowid)
	{
	    /*GET CURRENT PROCESS SORT ORDER*/
        $QRY = $this->db->select('total_days,set_time, tat')->from('fms_flow')->where('flow_id',$flowid)->where('production_flow_id',$productionflow)->get();
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
        
        	/** Get Pending Count **/
	 $mmer=$this->checkifmsmerge($flowid);
	$skkipl=$this->checkifstepisskippable($flowid);
	/** CHECK FOR DEPENDENCY **/
    $checkdepend=$this->db->select('dependency')->from('fms_flow')->where('flow_id',$flowid)->where('dependency','1')->get();
    $depend=$checkdepend->num_rows();
	 
		if($mmer==0 && $skkipl==0 && $depend==0)
		 {
			$pend=$this->db->select('id as orderstageid,jobcardid')->from('order_stage')->where('userstatus','0')->where('flowstage',$flowid)->get();
			$pendcount=$pend->num_rows();
		 }else{
			 
			 $pendcount=$this->menunotificationsformergeflowoverall($flowid,$productionflow);
			 
		
			
		 }
		 
		 	 return $pendcount;
		
	    
	    
	
	}
	
	
		function getvendorlowerprice($itemid)
	{
	
		$price=0;
		$resul=$this->db->select('min(price) as minrate')->from('vendors_price')->where('itemid',$itemid)->get();
		if($resul->num_rows()>0)
		{
			
			foreach($resul->result() as $resul1);
			
			$price=$resul1->minrate;
		}
		
		return $price;
		
		
		
	}
	
	
	
		function totalstockvalue()
	{
		
		$totalsum=array();
		$html=array();
		/** GET IMS STOCK DETAILS **/
		$restyuiu=$this->db->select('id,category')->from('presto_machine_part_category')->where('cattype','1')->where('stockvalue','1')->get();
		if($restyuiu->num_rows()>0)
		{
			foreach($restyuiu->result() as $restyuiu1)
			{
				$catwisecost=array();
				$restyui=$this->db->select('a.id,a.current_stock')->from('machine_parts_with_picture a')->join('vendors_price b','a.id=b.itemid')->where('a.category_id',$restyuiu1->id)->get();
				if($restyui->num_rows()>0)
				{
					foreach($restyui->result() as $restyui11)
					{
						$vprice=$this->getvendorlowerprice($restyui11->id);
						
						$stock=$restyui11->current_stock;
						
						$total=$vprice*$stock;
						
						$catwisecost[]=$total;
						
						
					}
					
				
					if(count($catwisecost)>0)
					{
					$html[]=round(array_sum($catwisecost),2)." - ".$restyuiu1->category;
					$totalsum[]=array_sum($catwisecost);
					}else
					{
						$html[]="0 -".$restyuiu1->category;
						$totalsum[]=0;
					}
					
				}else
					{
						$html[]="0 -".$restyuiu1->category;
						$totalsum[]=0;
					}
			
			
			}
			
			
			
		}
		
		/** END **/
		
		/** IMPORTED STOCK **/
		$imprice=array();
		$imported=$this->db->select('stock,mvalue')->from('presto_instruments')->where('type','1')->where('stock !=','0')->where('mvalue>','0')->get();
		if($imported->num_rows()>0)
		{
			foreach($imported->result() as $imported1)
			{
				$imprice[]=$imported1->stock*$imported1->mvalue;
				
				
			}
			if(count($imprice)>0)
			{
				$impr=array_sum($imprice);
				$totalsum[]=array_sum($imprice);
			}else{
				
				$impr=0;
				$totalsum[]=0;
			}
			
			$html[]=round($impr,2)." - IMPORTED STOCK- ";
		}else{
			
			$html[]="0 - IMPORTED STOCK";
			$totalsum[]=0;
		}
		
		/** END **/
		
		$wipcost=array();
	$restyu=$this->productionflowlongreport();
	if($restyu==0)
	{
				$restqwee=$this->db->select('flow_id,fms_flow,stockvalue,dependency')->from('fms_flow')->where_in('production_flow_id',$restyu,false)->where('stockvalue>','0')->order_by('production_flow_id')->get();
				if($restqwee->num_rows()>0)
				{
				
				foreach($restqwee->result() as $restqwee1)
				{

				/** GET ALL INSTRUMENTS ON THIS STAGE **/
			if($restqwee1->dependency==0)
				{
				$details=$this->onstagejobcard($restqwee1->flow_id,$restqwee1->stockvalue);
				}else{
					
				$details=$this->onstagejobcardfordependent($restqwee1->flow_id,$restqwee1->stockvalue);
				}

				/** END **/
				$wipcost[]=$details['stagetotal'];

				}
			}
	}
	
		if(count($wipcost)>0)
		{
		$html[]=round(array_sum($wipcost),2)." - WIP COST";
		$totalsum[]=array_sum($wipcost);

		}else{
    
		$html[]="0 - WIP COST ";
		$totalsum[]=0;
		}
	
	
	/** FINISHED GOODS **/
				$finprice=array();
				$imported=$this->db->select('a.order_id,a.item_id,c.mvalue')->from('order_instruments a')->join('order_planning b','a.item_id=b.jobcard_id')->join('presto_instruments
			c','a.item_id=c.id')->join('prestogroup_orders d','a.order_id=d.order_id')->where('b.factory !=','7')->where('a.complete','1')->where('a.finalpacked','0')->where('c.mvalue>','0')->where('c.type','0')->get();
				if($imported->num_rows()>0)
				{
				foreach($imported->result() as $imported1)
				{
				$fourty=0.4*$imported1->mvalue;
				$fin=$imported1->mvalue-$fourty;
				$finprice[]=$fin;


				}
				if(count($finprice)>0)
				{
				$finprice1=array_sum($finprice);
				$totalsum[]=array_sum($finprice);
				}else{

				$finprice1=0;
				$totalsum[]=0;
				}

				$html[]=round($finprice1,2)." - FINISHED GOODS- ";
				}else{

				$html[]="0 - FINISHED GOODS";
				$totalsum[]=0;
				}

				/** END **/
				
				
	if(count($totalsum)>0)
	{
		$tot=array_sum($totalsum);
	}else{
		
		$tot=0;
	}
	
	
		$html[]="Total Stock Value - ".round($tot,2);

return $html;

			
		
	
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


function onstagejobcard($flowid,$stockvalue)
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

	$data=$this->db->select('a.jobcardid,b.job_card_no,c.instruments_name,c.mvalue')->from('order_stage a')->join('order_instruments b','a.jobcardid=b.id')->join('presto_instruments c','b.item_id=c.id')->where('a.flowstage',$flowid)->where('a.userstatus','0')->get();
	$total=array();
	if($data->num_rows()>0)
	{
		
		foreach($data->result() as $datas)
		{
			if(!in_array($datas->jobcardid,$this->jobcardara))
			{
				array_push($this->jobcardara, $datas->jobcardid);
			$machinewisevalue=$this->getmachinevalue($cp,$stockvalue,$datas->mvalue,$datas->jobcardid);
			$total[]=$machinewisevalue;
			
			}
			
			
		}
		
	}
	
	$overallstagetot=$this->giveoveralldata($total);
	return array('stagetotal'=>$overallstagetot);
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



function getalldispatchedjobcard($odid)
{
  
    
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


 function lot_ordercount()
	{
		$lot=array();
		$restyui=$this->db->select('a.item_id')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->join('prestogroup_orders c','a.order_id=c.order_id')->where('c.closeorder','0')->group_by('a.item_id')->order_by('b.instruments_name')->get();
		if($restyui->num_rows()>0)
		{
			$i=1;
			foreach($restyui->result() as $restyui1)
			{
			    $query1=$this->db->select('b.company_name,a.job_card_no,b.internal_order_no,a.order_id')->from('order_instruments a')->join('prestogroup_orders b','a.order_id=b.order_id')->where('a.item_id',$restyui1->item_id)->group_by('a.order_id')->get();
			
			if($query1->num_rows()>0)
			{
			
			foreach($query1->result() as $instruments){
				
			$isdispatched=$this->getalldispatchedjobcard($instruments->order_id);
			
			}
			
		   }
			
			if($isdispatched==0)
				{
			        $lot[]=1;
				}
			}
	}

        if(count($lot)>0)
        {
            return array_sum($lot);
        }else
        {
            return 0;
        }
	
	}
	
	
function pendingordercount()
{
    $lott=array();
            $order=$this->db->select('a.order_id,b.internal_order_no,b.added_on,b.company_name,c.first_name,c.last_name')->from('order_instruments a')->join('prestogroup_orders b','a.order_id=b.order_id')->join('order_planning d','d.order_id=b.order_id')->join('system_users c','b.marketing_person=c.user_id')->where('a.complete','0')->where('factory !=','2')->group_by('a.order_id')->order_by('b.added_on','ASC')->get();
        
        if($order->num_rows()>0)
        {
            foreach($order->result() as $order1)
            { 
                    $inst=$this->db->select('c.factory,b.id,a.instruments_name,b.job_card_no')->from('presto_instruments a')->join('order_instruments b','a.id=b.item_id')->join('order_planning c','b.id=c.jobcard_id')->where('b.complete','0')->where('b.order_id',$order1->order_id)->get();
                    if($inst->num_rows()>0)
                    {
                        $lott[]=1;
                    }
            }			
        
        }
        
        if(count($lott)>0)
        {
           return array_sum($lott);
        }else
        {
            return 0;
        }
        
}


function pendingordercountforservice()
{
    $lott=array();
            $order=$this->db->select('a.order_id,b.internal_order_no,b.added_on,b.company_name,c.first_name,c.last_name')->from('order_instruments a')->join('prestogroup_orders b','a.order_id=b.order_id')->join('order_planning d','d.order_id=b.order_id')->join('system_users c','b.marketing_person=c.user_id')->where('a.complete','0')->where('factory !=','2')->where('b.order_type','SERVICE')->group_by('a.order_id')->order_by('b.added_on','ASC')->get();
        
        if($order->num_rows()>0)
        {
            foreach($order->result() as $order1)
            { 
                    $inst=$this->db->select('c.factory,b.id,a.instruments_name,b.job_card_no')->from('presto_instruments a')->join('order_instruments b','a.id=b.item_id')->join('order_planning c','b.id=c.jobcard_id')->where('b.complete','0')->where('b.order_id',$order1->order_id)->get();
                    if($inst->num_rows()>0)
                    {
                        $lott[]=1;
                    }
            }			
        
        }
        
        if(count($lott)>0)
        {
           return array_sum($lott);
        }else
        {
            return 0;
        }
        
}


    public function readyordercountOld()
    {
    $ready=array();
    $this->db->select('a.order_id')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left')->where('a.order_status','1');
    $query = $this->db->where('a.closeorder','0')->where('a.movetodispatch','0')->order_by('a.order_id','desc')->get();
    $res = $query->result();
    $i=1;
    if($query->num_rows()>0)
    {
    foreach($res as $row)
    {
    $completedjobcard=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1')->get();
    $completedjobcards=$completedjobcard->num_rows();
    
    $query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name, a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
    $alljobcard=$query1->num_rows();
    
    if($completedjobcards==$alljobcard)
    {
    $ready[]=1;
    }
    }
    
    }
    
    if(count($ready)>0)
    {
    return array_sum($ready);
    }else
    {
    return 0;
    }
    
    
    }
    
    
    public function readyordercount($userinfo)
	{
	    $read=array();
	$this->db->select('a.order_id')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left')->where('a.order_status','1');
	if($userinfo<>0){
	    $this->db->where('a.marketing_person',$userinfo);
	}
		$query = $this->db->where('a.closeorder','0')->where('a.movetodispatch','0')->get();
		$res = $query->result();
		$i=1;
		if($query->num_rows()>0)
		{
		foreach($res as $row)
	    {
			
			$completedjobcard=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1')->get();
			$completedjobcards=$completedjobcard->num_rows();
			
			$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name, a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			$alljobcard=$query1->num_rows();
			
        if($completedjobcards==$alljobcard)
        {
        
        $read[]=1;
        }
        }
    }
    
    if(count($read)>0)
    {
    
        return array_sum($read);
    }else
    {
        return 0;
    }
	
}


  public function readyordercountforservice($userinfo)
	{
	    $read=array();
	$this->db->select('a.order_id')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left')->where('a.order_status','1')->where('a.order_type','SERVICE');
	if($userinfo<>0){
	    $this->db->where('a.marketing_person',$userinfo);
	}
		$query = $this->db->where('a.closeorder','0')->where('a.movetodispatch','0')->get();
		$res = $query->result();
		$i=1;
		if($query->num_rows()>0)
		{
		foreach($res as $row)
	    {
			
			$completedjobcard=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1')->get();
			$completedjobcards=$completedjobcard->num_rows();
			
			$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name, a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			$alljobcard=$query1->num_rows();
			
        if($completedjobcards==$alljobcard)
        {
        
        $read[]=1;
        }
        }
    }
    
    if(count($read)>0)
    {
    
        return array_sum($read);
    }else
    {
        return 0;
    }
	
}


 function dispatchfortommorowcount($userinfo)
	{
		$disp=array();
		$this->db->select('a.order_id')->from('prestogroup_orders a');
		$this->db->where('a.order_status','1')->where('a.closeorder','0')->where('a.movetodispatch','1');
		if($userinfo<>0){
		    $this->db->where('a.marketing_person',$userinfo);
		}
		$query = $this->db->get();
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
		$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name,a.finalpacked,a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
		$alljobcard=$query1->num_rows();
		if($alljobcard<>0)
		{
		if($completedjobcards==$alljobcard)
		{
		$disp[]=1;
		}
		}
		}
		}
		
		if(count($disp)>0)
		{
		    return array_sum($disp);
		}else
		{
		    return 0;
		}
	
}


 function dispatchfortommorowcountservice($userinfo)
	{
		$disp=array();
		$this->db->select('a.order_id')->from('prestogroup_orders a');
		$this->db->where('a.order_status','1')->where('a.order_type','SERVICE')->where('a.closeorder','0')->where('a.movetodispatch','1');
		if($userinfo<>0){
		    $this->db->where('a.marketing_person',$userinfo);
		}
		$query = $this->db->get();
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
		$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name,a.finalpacked,a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
		$alljobcard=$query1->num_rows();
		if($alljobcard<>0)
		{
		if($completedjobcards==$alljobcard)
		{
		$disp[]=1;
		}
		}
		}
		}
		
		if(count($disp)>0)
		{
		    return array_sum($disp);
		}else
		{
		    return 0;
		}
	
}

function nondispatchedorders($userinfo)
{
  $disp=array();
		$this->db->select('a.order_id')->from('prestogroup_orders a');
		$this->db->where('a.order_status','1')->where('a.closeorder','0');
		if($userinfo<>0){
		    $this->db->where('a.marketing_person',$userinfo);
		}
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		if($query->num_rows()>0)
		{
            foreach($res as $row)
            {
            
            $restyui=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('finalpacked','0')->get();
            
            if($restyui->num_rows()>0)
            {
            $disp[]=1;
            }
            }
		}
		
		if(count($disp)>0)
		{
		    return array_sum($disp);
		}else
		{
		    return 0;
		}
	  
    
    
}


function nondispatchedordersforservice($userinfo)
{
  $disp=array();
		$this->db->select('a.order_id')->from('prestogroup_orders a');
		$this->db->where('a.order_status','1')->where('order_type','SERVICE')->where('a.closeorder','0');
		if($userinfo<>0){
		    $this->db->where('a.marketing_person',$userinfo);
		}
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		if($query->num_rows()>0)
		{
            foreach($res as $row)
            {
            
            $restyui=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('finalpacked','0')->get();
            
            if($restyui->num_rows()>0)
            {
            $disp[]=1;
            }
            }
		}
		
		if(count($disp)>0)
		{
		    return array_sum($disp);
		}else
		{
		    return 0;
		}
	  
    
    
}



function unplannedordercount()
{
    $unplan=array();
   $restyu= $this->db->select('order_id')->from('prestogroup_orders')->where('closeorder','0')->where('order_status','1')->get();
   if($restyu->num_rows()>0)
   {
     
       foreach($restyu->result() as $restyu1)
       {
       
      
        /** Get order planned **/
            $restt=$this->db->select('id')->from('order_instruments')->where('order_id',$restyu1->order_id)->get();
            $totjobcard= $restt->num_rows();
            
            $restt1=$this->db->select('id')->from('order_planning')->where('order_id',$restyu1->order_id)->get();
          
            $totjobcardplanned= $restt1->num_rows();
            if($totjobcard<>$totjobcardplanned)
            {
               $unplan[]=1; 
            }
                
   }
   
   }
   
 
   
   if(count($unplan)>0)
   {
       return array_sum($unplan);
       
   }else
   {
       
        return 0;
   }
}


function unplannedordercountforservice()
{
    $unplan=array();
   $restyu= $this->db->select('order_id')->from('prestogroup_orders')->where('closeorder','0')->where('order_status','1')->where('order_type','SERVICE')->get();
   if($restyu->num_rows()>0)
   {
     
       foreach($restyu->result() as $restyu1)
       {
       
      
        /** Get order planned **/
            $restt=$this->db->select('id')->from('order_instruments')->where('order_id',$restyu1->order_id)->get();
            $totjobcard= $restt->num_rows();
            
            $restt1=$this->db->select('id')->from('order_planning')->where('order_id',$restyu1->order_id)->get();
          
            $totjobcardplanned= $restt1->num_rows();
            if($totjobcard<>$totjobcardplanned)
            {
               $unplan[]=1; 
            }
                
   }
   
   }
   
 
   
   if(count($unplan)>0)
   {
       return array_sum($unplan);
       
   }else
   {
       
        return 0;
   }
}


function povsprcount()
{
    
    	$rest = $this->db->select('a.id, a.itemid, a.prno, a.jobcardid,a.qty, a.unit, a.addedBy,a.addedOn, b.prno, c.first_name, c.last_name')->from('purchase_request a')->join('purchase_order b','b.prno=a.prno','left')->join('system_users c','a.addedBy=c.user_id','left')->where('a.approvalstatus','0')->group_by('a.prno')->get();
    	
    	return $rest->num_rows();
}

function indentvspo()
{
    	$rest = $this->db->select('a.id, a.indendno,a.prefix, a.itemid, a.qty, a.unit, a.addedOn, a.addedBy, a.approvalstatus, c.first_name, c.last_name')->from('intend_request a')->join('purchase_request b','b.sourceid=a.indendno','left')->join('system_users c','a.addedBy=c.user_id','left')->where('b.source','2')->where('a.approvalstatus','0')->group_by('a.indendno')->get();
			return $rest->num_rows();
}

function povsdelivery()
{
    
    	$rest=$this->db->select('a.id')->from('purchase_order a')->where('a.approved','1')->where('a.gateentrycomplete','0')->where('a.completed','0')->order_by('a.addedOn','DESC')->get();
		
		return $rest->num_rows();
}


function checkifselforder($order_id)
        {
        
            $rqt=$this->db->select('order_id')->from('prestogroup_orders')->where('order_id',$order_id)->where('selforder','1')->get();
            
            return $rqt->num_rows();
            
            
        }
        
    function productionname($pid)
     {
        
        $prodname='';
        $resty=$this->db->select('production_flow')->from('production_flow')->where('id',$pid)->get();
        if($resty->num_rows()>0)
        {
            foreach($resty->result() as $restyu);
            
            $prodname=$restyu->production_flow;
            
        }
            
            
            return $prodname;
        
     }
     
     function salespendingorders($userid)
     {
         
         	$order=$this->db->select('a.order_id,b.internal_order_no,b.added_on,b.company_name,c.first_name,c.last_name')->from('order_instruments a')->join('prestogroup_orders b','a.order_id=b.order_id')->join('order_planning d','d.order_id=b.order_id')->join('system_users c','b.marketing_person=c.user_id')->where('a.complete','0')->where('factory !=','2')->where('b.marketing_person',$userid)->group_by('a.order_id')->get();
         	
         	return $order->num_rows();
         
     }
     
     function reordermachine()
     {
         $r=array();
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
		    
		    $r[]=1;
		}
		
	}
		
}
	
	
    if(count($r)>0)
    {
    return array_sum($r);
    
    }else
    {
    return 0;
    }
	
         
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

                
                
            function finishcost()
            {
            
            $type = "'0','1'";
            $machinetotval[]=0;
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
            
            $machinetotval[]=$finval*$totstock;
            /** END **/
            
            
            
            }
            
            }
            
            return array_sum($machinetotval);
            
            }
            
            
            function getsalestarget($uid)
            {
                
                $restyu=$this->db->select('salestarget')->from('system_users')->where('user_id',$uid)->get();
                if($restyu->num_rows()>0)
                {
                    foreach($restyu->result() as $restyu1);
                    
                    return $restyu1->salestarget;
                }else
                {
                   echo "INVALID USER";exit;
                }
                
                
            
            }



 function getsalestargetachieved($uid)
            {
                
                $sdate=date('Y-m-01');
                $ldate=date('Y-m-t');
                
                $achiv=array();
                $restyu=$this->db->select('order_value_after_discount')->from('prestogroup_orders')->where('selforder','0')->where('marketing_person',$uid)->where('added_on BETWEEN "'. $sdate. '" and "'.$ldate.'"')->get();
                if($restyu->num_rows()>0)
                {
                    foreach($restyu->result() as $restyu1)
                    {
                        
                       $achiv[] =$restyu1->order_value_after_discount;
                    }
                    
                    return array_sum($achiv);
                }else
                {
                   return 0;
                }
            }
            
            
            
            
             function getpaymentcollected($uid)
            {
                
                $sdate=date('Y-m-01');
                $ldate=date('Y-m-t');
                
                $achiv=array();
                $restyu=$this->db->select('order_value_after_discount')->from('prestogroup_orders')->where('selforder','0')->where('marketing_person',$uid)->where('closeorder','1')->where('added_on BETWEEN "'. $sdate. '" and "'.$ldate.'"')->get();
                if($restyu->num_rows()>0)
                {
                    foreach($restyu->result() as $restyu1)
                    {
                        
                       $achiv[] =$restyu1->order_value_after_discount;
                    }
                    
                    return array_sum($achiv);
                }else
                {
                   return 0;
                }
            }
            
            function machineforinstallation()
            {
                $a[]=0;
                $query = $this->db->select('a.order_id')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left')->where('a.order_status','1')->where('a.closeorder','0')->where('a.installation_charges','1')->order_by('a.order_id','desc')->get();
                $res = $query->result();
                if($query->num_rows()>0)
                {
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
                        $finalpacked=array();
                        foreach($query1->result() as $instruments){
                        
                        if($instruments->finalpacked=='1')
                        {
                        $finalpacked[]=1;
                        }else
                        {
                        $finalpacked[]=0;
                        }   
                        }
                        
                        if($completedjobcards==$alljobcard)
                        {
                            if(!in_array("0", $finalpacked)) 
                            {
                            $a[]=1;
                            
                            }
                        }
                    
                    }
                    
                }else
                {
                    
                    return 0;
                    
                }
                
                return array_sum($a);
                                       
                
            }
            
            
            
            

function workinprogresscountforinstallation()
{
        $comp=array();
		$comp[]=0;
        $query = $this->db->select('a.order_id,a.added_on')->from('prestogroup_orders a')->where('a.order_status','1')->where('a.closeorder','0')->where('a.installation_charges','1')->order_by('a.order_id','desc')->get();
        $res = $query->result();
		$i=1;
		if($query->num_rows()>0)
		{
		foreach($res as $row)
		{
			$completedjobcard=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1')->get();
			$completedjobcards=$completedjobcard->num_rows();
			
			$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name,a.finalpacked,a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			$alljobcard=$query1->num_rows();
			
				$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name,a.finalpacked,a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			$alljobcard=$query1->num_rows();
			if($alljobcard>0)
			{
				foreach($query1->result() as $alluee);
					$finalpack=$alluee->finalpacked;
					
			}else{ $finalpack=0; }
			
			
			if($alljobcard>0)
			{
				foreach($query1->result() as $alluee);
					$finalpack=$alluee->finalpacked;
					
			}else{ $finalpack=0; }
			
		if($completedjobcards==$alljobcard)
		{
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
			
				
			}
			
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
if(!in_array("0", $finalpacked)) 
{

$dura='';
$q=$this->db->select('next_followup,remarks,status')->from('service_request_followup')->where('record_id',$row->order_id)->limit(1)->order_by('id','desc')->get();

if($q->num_rows()>0){
    foreach($q->result() as $follow);
   if($follow->status=='0')
   {
       $comp[]=1;
       
   }
}

}
}
}
}

return array_sum($comp);
	
}
       
       
       
function workinprogresscustomerinstallation()
{
        $comp=array();
		$comp[]=0;
        $query = $this->db->select('a.order_id,a.added_on')->from('prestogroup_orders a')->where('a.order_status','1')->where('a.closeorder','0')->where('a.installation_charges','1')->order_by('a.order_id','desc')->get();
        $res = $query->result();
		$i=1;
		if($query->num_rows()>0)
		{
		foreach($res as $row)
		{
			$completedjobcard=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1')->get();
			$completedjobcards=$completedjobcard->num_rows();
			
			$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name,a.finalpacked,a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			$alljobcard=$query1->num_rows();
			
				$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name,a.finalpacked,a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			$alljobcard=$query1->num_rows();
			if($alljobcard>0)
			{
				foreach($query1->result() as $alluee);
					$finalpack=$alluee->finalpacked;
					
			}else{ $finalpack=0; }
			
			
			if($alljobcard>0)
			{
				foreach($query1->result() as $alluee);
					$finalpack=$alluee->finalpacked;
					
			}else{ $finalpack=0; }
			
		if($completedjobcards==$alljobcard)
		{
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
			
				
			}
			
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
if(!in_array("0", $finalpacked)) 
{

$dura='';
$q=$this->db->select('next_followup,remarks,status')->from('service_request_followup')->where('record_id',$row->order_id)->limit(1)->order_by('id','desc')->get();

if($q->num_rows()>0){
    foreach($q->result() as $follow);
   if($follow->status=='2')
   {
       $comp[]=1;
       
   }
}

}
}
}
}

return array_sum($comp);
	
}
       
       

function incompleteinstallation()
{
        $comp=array();
		$comp[]=0;
        $query = $this->db->select('a.order_id,a.added_on')->from('prestogroup_orders a')->where('a.order_status','1')->where('a.closeorder','0')->where('a.installation_charges','1')->order_by('a.order_id','desc')->get();
        $res = $query->result();
		$i=1;
		if($query->num_rows()>0)
		{
		foreach($res as $row)
		{
			$completedjobcard=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1')->get();
			$completedjobcards=$completedjobcard->num_rows();
			
			$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name,a.finalpacked,a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			$alljobcard=$query1->num_rows();
			
				$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name,a.finalpacked,a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			$alljobcard=$query1->num_rows();
			if($alljobcard>0)
			{
				foreach($query1->result() as $alluee);
					$finalpack=$alluee->finalpacked;
					
			}else{ $finalpack=0; }
			
			
			if($alljobcard>0)
			{
				foreach($query1->result() as $alluee);
					$finalpack=$alluee->finalpacked;
					
			}else{ $finalpack=0; }
			
		if($completedjobcards==$alljobcard)
		{
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
			
				
			}
			
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
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
$q=$this->db->select('next_followup,remarks,status')->from('service_request_followup')->where('record_id',$row->order_id)->limit(1)->order_by('id','desc')->get();

if($q->num_rows()>0){
    foreach($q->result() as $follow);
   if($follow->status=='0')
   {
      
       
   }
}else
{
     $comp[]=1;
}
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
   
  
    

}
}
}
}

return array_sum($comp);
	
}
 
 
 function newinstallation()
{
        $comp=array();
		$comp[]=0;
        $query = $this->db->select('a.order_id,a.added_on')->from('prestogroup_orders a')->where('a.order_status','1')->where('a.closeorder','0')->where('a.installation_charges','1')->order_by('a.order_id','desc')->get();
        $res = $query->result();
		$i=1;
		if($query->num_rows()>0)
		{
		foreach($res as $row)
		{
			$completedjobcard=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1')->get();
			$completedjobcards=$completedjobcard->num_rows();
			
			$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name,a.finalpacked,a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			$alljobcard=$query1->num_rows();
			
				$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name,a.finalpacked,a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			$alljobcard=$query1->num_rows();
			if($alljobcard>0)
			{
				foreach($query1->result() as $alluee);
					$finalpack=$alluee->finalpacked;
					
			}else{ $finalpack=0; }
			
			
			if($alljobcard>0)
			{
				foreach($query1->result() as $alluee);
					$finalpack=$alluee->finalpacked;
					
			}else{ $finalpack=0; }
			
		if($completedjobcards==$alljobcard)
		{
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
			
				
			}
			
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
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
$q=$this->db->select('next_followup,remarks,status')->from('service_request_followup')->where('record_id',$row->order_id)->limit(1)->order_by('id','desc')->get();

if($q->num_rows()>0){
    foreach($q->result() as $follow);
   if($follow->status=='0')
   {
      
       
   }
}else
{
     
}
$generatedtime=date('Y-m-d H:i:s',strtotime($closedon));
$onedayold=date('Y-m-d H:i:s', strtotime("+1 day", strtotime($generatedtime)));
$ti1 = strtotime(date('Y-m-d H:i:s'));
$ti2 = strtotime($onedayold);
$hour = abs($ti2 - $ti1)/(60*60);

if($hour<='24')
{
   $comp[]=1;
}else
{
     $dura="";
}
   
  
    

}
}
}
}

return array_sum($comp);
	
}
      
      
 function engineervisitinstallation()
{
        $comp=array();
		$comp[]=0;
        $query = $this->db->select('a.order_id,a.added_on')->from('prestogroup_orders a')->where('a.order_status','1')->where('a.closeorder','0')->where('a.installation_charges','1')->order_by('a.order_id','desc')->get();
        $res = $query->result();
		$i=1;
		if($query->num_rows()>0)
		{
		foreach($res as $row)
		{
			$completedjobcard=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1')->get();
			$completedjobcards=$completedjobcard->num_rows();
			
			$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name,a.finalpacked,a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			$alljobcard=$query1->num_rows();
			
				$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name,a.finalpacked,a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			$alljobcard=$query1->num_rows();
			if($alljobcard>0)
			{
				foreach($query1->result() as $alluee);
					$finalpack=$alluee->finalpacked;
					
			}else{ $finalpack=0; }
			
			
			if($alljobcard>0)
			{
				foreach($query1->result() as $alluee);
					$finalpack=$alluee->finalpacked;
					
			}else{ $finalpack=0; }
			
		if($completedjobcards==$alljobcard)
		{
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
			
				
			}
			
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
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
$q=$this->db->select('next_followup,remarks,status')->from('service_request_followup')->where('record_id',$row->order_id)->limit(1)->order_by('id','desc')->get();

if($q->num_rows()>0){
    foreach($q->result() as $follow);
   if($follow->status=='5')
   {
      $comp[]=1;
       
   }
}else
{
     
}
$generatedtime=date('Y-m-d H:i:s',strtotime($closedon));
$onedayold=date('Y-m-d H:i:s', strtotime("+1 day", strtotime($generatedtime)));
$ti1 = strtotime(date('Y-m-d H:i:s'));
$ti2 = strtotime($onedayold);
$hour = abs($ti2 - $ti1)/(60*60);

if($hour<='24')
{
   
}else
{
     $dura="";
}
   
  
    

}
}
}
}

return array_sum($comp);
	
}

function getserviceinstallation()
{
   $rresytye=$this->db->select('record_id')->from('service_request_followup')->where('status','5')->where('closedbyhod','0')->get();
    
    return $rresytye->num_rows();
    
    
}

function getpaymentscollected()
{
$pcollected=array();
$day = date('w');
$week_start = date('Y-m-d', strtotime('-'.$day.' days')).' 00:00:00';
$week_end = date('Y-m-d', strtotime('+'.(6-$day).' days')).' 23:59:59';

$resty=$this->db->select('payment')->from('payment_collected')->where('addedOn BETWEEN "'. $week_start. '" and "'.$week_end.'"')->where('addedBy',$_SESSION['logged_in']['user_id'])->get();
if($resty->num_rows()>0)
{
    foreach($resty->result() as $restyy)
    {
        
       $pcollected[]=$restyy->payment;
        
    }
    
    return array_sum($pcollected);
    
}else
{
    return 0;
}
  
    
}


function getbomprice($mid)
{

	$Restey=$this->db->select('price')->from('vendors_price')->where('itemid',$mid)->get();
	if($Restey->num_rows()>0)
	{
	foreach($Restey->result() as $Restey1);

	return $Restey1->price;

	}else{

	return 0;
	}
	
	
}

function getinstrumentname($machinename)
{
	$machinedata=array();
	$treyte=$this->db->select('instruments_name')->from('presto_instruments')->where('id',$machinename)->get();
	if($treyte->num_rows()>0)
	{
		foreach($treyte->result() as $treyte1);
		
		$machinedata['name']=$treyte1->instruments_name;
		
		
	}
	
	return $machinedata;
	
}

function getnextdaysandstageforsales($productionid,$setorder)
{
    $send=array();
   $newsetorder= $setorder+1;
   	$odstage=$this->db->select('b.fms_flow,b.pdays')->from('fms_flow b')->where('b.production_flow_id',$productionid)->where('b.setorder',$newsetorder)->get();
   	if($odstage->num_rows()>0)
   	{
   	    foreach($odstage->result() as $odstage1);
   	    
   	    $send['stage']=$odstage1->fms_flow;
   	    $send['days']=$odstage1->pdays." DAYS";
   	    
   	    
   	}
   
   return $send;
    
}


	function onstagejobcardfordependent($flowid,$stockvalue)
{
				/** GET MACHINE CP **/
				$total[]=0;
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
				$plannedstageorderno=$this->getplanstartsfromorderno($proc->planstartsfrom);
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


				$checkskiiped=$this->checkifdatahascomeskipped($proc->jobcardid,$flowstage);


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

				$markapp=$this->checkformergerpoint($flowstage,$proc->jobcardid,$proc->originalorderid,$proc->fabrication,$proc->jumpfabricationappl);
				}

				if($markapp==1)
				{
					
					$machinewisevalue=$this->getmachinevalue($cp,$stockvalue,$proc->mvalue,$proc->jobcardid);
					$total[]=$machinewisevalue;

				}



				}


				}

			$overallstagetot=$this->giveoveralldata($total);
	        return array('stagetotal'=>$overallstagetot);
}


function thisweekreconcilation($start,$end)
{
        $restyu=$this->db->select('sum(amount) as amt')->from('payment_reconciliation')->where('accepted_by',$_SESSION['logged_in']['user_id'])->where('bill_date BETWEEN "'. $start. '" and "'.$end.'"')->get();
        if($restyu->num_rows()>0)
        {
        foreach($restyu->result() as $restyu1);
        if($restyu1->amt<>'')
        {
        return $restyu1->amt;
        }else
        {
        return 0;
        }
        }else
        {
        return 0;
        }
   
    
    
}


function checkifanyothermachineissimilar($itemid)
		{
			$simmac=array();
			$simmac[]=0;
			$resteu=$this->db->select('similarmachine')->from('similardiversionmachines')->where('basemachine',$itemid)->get();
			if($resteu->num_rows()>0)
			{
				foreach($resteu->result() as $resteu1)
				{
					$simmach[]=$resteu1->similarmachine;
					
				}
			}
			
				return $simmach;



		}
		
		function getrefrencemachinename($Ref)
		{
			$restey=$this->db->select('b.instruments_name')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->where('a.job_card_no',$Ref)->get();
			if($restey->num_rows()>0)
			{
				foreach($restey->result() as $restey1);
				
				return $restey1->instruments_name;
			}else{
				
				return '';
			}
			
		}
		
		
		
		function getmachinealias($itemid)
		{

			$Restye=$this->db->select('part')->from('machine_parts_with_picture')->where('id',$itemid)->get();
			if($Restye->num_rows()>0)
			{
			foreach($Restye->result() as $Restye1);

			return $Restye1->part;

			}else
			{
			return '';
			}
		
		
		}	
		
		
		
		
		function stepwisetime($jobcardid,$instrumentname,$jobcardno,$factory)
		{


		$html = "<table border='1' style='width:500px;'>";

		$html.="<tr>";
		$html.="<td style='padding:2px 2px 2px 2px; text-align:center;' colspan='2'>".strtoupper($instrumentname)."<br/> Jobcard ".$jobcardno."</td>";
		$html.="</tr>";


	
		$flowid=$this->getfactoryfms($factory);
		if(count($flowid)>0)
		{
			$t=0;
		foreach($flowid as $flowss)
		{
			if($t==0)
			{

			$plandaystat=$this->getorderplandatediff($jobcardid);

			$html.="<tr>";
			$html.="<td style='padding:2px 2px 2px 2px; text-align:center;width:400px;'>ORDER PLANNING</td>";
			$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".$plandaystat."</td>";
			$html.="</tr>";
		}

		$elapseddays=$this->gettotaldayselapsed($jobcardid,$flowss['flowid'],$flowss['tatfrom']);
		$html.="<tr>";
		$html.="<td style='padding:2px 2px 2px 2px; text-align:center;width:400px;'>".strtoupper($flowss['fms_flow'])."</td>";
		$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".$elapseddays."</td>";
		$html.="</tr>";
		$t++;
		}
		}

		$html.="</table>";		

		return $html;

		}

		function getfactoryfms($factory)
		{
		$restey=array();
		$Rrestyu=$this->db->select('flow_id,fms_flow,tat')->from('fms_flow')->where('production_flow_id',$factory)->order_by('setorder','ASC')->get();
		if($Rrestyu->num_rows()>0)
		{

		foreach($Rrestyu->result() as $Rrestyu1)
		{

		$restey[]=array('flowid'=>$Rrestyu1->flow_id,'fms_flow'=>$Rrestyu1->fms_flow,'tatfrom'=>$Rrestyu1->tat);
		}
		return $restey;
		}else
		{
		return 0;
		}


		}


	function gettotaldayselapsed($jobcardid,$flowid,$tat)
	{
		$donedatefinal='';
		$startdate='';

		$donedate=$this->db->select('addedOn')->from('order_stage')->where('jobcardid',$jobcardid)->where('flowstage',$flowid)->get();

		if($donedate->num_rows()>0)
		{
		foreach($donedate->result() as $donedates);

		$donedatefinal=$donedates->addedOn;

		/** GET PREVIOUS DONE TAT **/

		if($tat==0)
		{
		$Rreyte=$this->db->select('plannedOn')->from('order_planning')->where('jobcard_id',$jobcardid)->get();
		if($Rreyte->num_rows()>0)
		{
		foreach($Rreyte->result() as $Rreyte1);

		$startdate=$Rreyte1->plannedOn;

		}

		}else
		{

		$donedate1=$this->db->select('addedOn')->from('order_stage')->where('jobcardid',$jobcardid)->where('flowstage',$tat)->get();

		if($donedate1->num_rows()>0)
		{
		foreach($donedate1->result() as $Rreyte1);

		$startdate=$Rreyte1->addedOn;

		}


		}

		/** END **/
		}


		/** GET DIFFERENCE BETWEEN DATES **/

		if($donedatefinal<>'' && $startdate<>'')
		{

			$start=strtotime($startdate);
			$end=strtotime($donedatefinal);

			$days_between = ceil(abs($end - $start) / 86400);

			return $days_between;

		}else
		{

			return 0;
		}

		/** END **/




	}

	function getorderplandatediff($jobcardid)
	{
		$addedon='';
		$planneddate='';

		$Resteye=$this->db->select('instrument_addedon')->from('order_instruments')->where('id',$jobcardid)->get();

		if($Resteye->num_rows()>0)
		{
		foreach($Resteye->result() as $Rreyte1);
		$addedon=$Rreyte1->instrument_addedon;
		}


		$Rreyte=$this->db->select('plannedOn')->from('order_planning')->where('jobcard_id',$jobcardid)->get();
		if($Rreyte->num_rows()>0)
		{
		foreach($Rreyte->result() as $Rreyte1);

		$planneddate=$Rreyte1->plannedOn;

		}



		if($addedon<>'' && $planneddate<>'')
		{

			$start=strtotime($addedon);
			$end=strtotime($planneddate);

			$days_between = ceil(abs($end - $start) / 86400);

			return $days_between;

		}else
		{

			return 0;
		}




	}
	
	
	function getmachinealiasfromims($fincode)
		{

$atsts=array();			
$Restye=$this->db->select('a.part,a.specification,a.picture')->from('machine_parts_with_picture a')->where('a.fincode',$fincode)->get();
			if($Restye->num_rows()>0)
			{
			foreach($Restye->result() as $Restye1);

			$atsts['name']= $Restye1->part." ".$Restye1->specification;
			$atsts['picture']= $Restye1->picture;

			}
			
			return $atsts;
		
		
		}
		
		
		
		
	
	function checkifthisstepisautoprstep($flowid)
	{
		$Resteu=$this->db->select('flow_id')->from('fms_flow')->where('flow_id',$flowid)->where('autoprstep','1')->get();
		return $Resteu->num_rows();
		
		
	}
	
	
	function raiseprforlowitems($jobcardid,$planstartfrom,$factory,$orderid,$tatday,$tathours)
	{
			
		    $selecteditems=array();
		    $selecteditemsqty=array();
			$mid=$this->getinstrumentid($jobcardid);
			$pr=$this->generatenewprnumber();
			
			$resty=$this->db->select('a.id,a.picture,a.part,a.unit,a.current_stock,b.qty,b.partid')->from('machine_parts_with_picture a')->join('machine_bom b','a.id=b.partid')->where('b.mid',$mid)->group_by('b.partid')->get();
			if($resty->num_rows()>0)
			{
				//echo "<pre>"; print_r($resty->result()); exit;
				$u=0;
			foreach($resty->result() as $restyu1)
			{
			
			
				/** Get Available blocked Stock **/
				$restblockedst=$this->db->select('sum(stock) as blockstock')->from('blockedstock')->where('itemid',$restyu1->partid)->where('active','1')->get();
				foreach($restblockedst->result() as $restblockedstock);
				$blockedparts=$restblockedstock->blockstock;
				if($blockedparts=='')
				{
				    $blockedparts=0;
				}else
				{
				    $blockedparts=$blockedparts;
				}
			    /** end **/
			    
			       /** REQUIRED **/
				$Requiredqty=$restyu1->qty;
				/** END **/
				
				/** PHYSICAL STOCK **/
				$physicalcurrentstock=$restyu1->current_stock;
			
				/** END **/


				 $remainingstock=$physicalcurrentstock-$blockedparts;
				 
				if($remainingstock>0)
				{
				/** for positive stock **/

				if($remainingstock>$Requiredqty)
				{
				$finalstock=0;
				}else
				{
				$finalstock=$Requiredqty-$remainingstock;
				}


				}else
				{
				/** for negetive stock **/

				$finalstock=$Requiredqty;

				}


				if($finalstock>0)
				{
				$selecteditems[]=$restyu1->partid;
				$selecteditemsqty[]=$finalstock;
				$selectedunit[]=$restyu1->unit;
				$selectedcurrentstock[]=$physicalcurrentstock;
				}





			} 


				/** BLOCK ALL ITEMS IN JOBCARD **/
				$this->storemodel->blockallitems($mid,$jobcardid,$selecteditems,$pr);
				/** END **/

		


		}
		 		
			
		
		/*** NOW RAISE THE DAMN PR **/
		
		//echo "<pre>"; print_r($selecteditems); exit;
		if(count($selecteditems)>0)
		{
			$prnumonly=preg_replace('/[^0-9]/', '', $pr);
			$this->generateprfromplanning($pr,$jobcardid,$selecteditems,$selecteditemsqty,$selectedunit,$selectedcurrentstock,$prnumonly);
		}
			/** Mark Step as done ***/
			$dataasskd=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'));
			$this->db->where('jobcardid',$jobcardid);
			$this->db->where('flowstage',$planstartfrom);
			$this->db->update('order_stage',$dataasskd);
			/** END **/

			$orderstage=$this->getorderstageid($jobcardid,$planstartfrom);
			if(count($selecteditems)>0)
				{
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'stock'=>'0','prno'=>$pr,'material'=>'','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
			$this->db->insert('kitting_bop_details',$detailentry);

			}else
			{
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'stock'=>'1','prno'=>'','material'=>'','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
			$this->db->insert('kitting_bop_details',$detailentry);
			}


			/** Check for next **/
			$nextff=$this->checkformoveto($planstartfrom);
			$restyuwew=$this->db->select('setorder')->from('fms_flow')->where('flow_id',$planstartfrom)->get();
			foreach($restyuwew->result() as $restyuwew112);
			$selectedfmsorder=$restyuwew112->setorder;
			if($selectedfmsorder==1)
			{

			if($nextff==0)
			{
			$nextfmsorder=$selectedfmsorder+1;
			//echo $nextfmsorder;exit;
			$restyuwew=$this->db->select('flow_id')->from('fms_flow')->where('setorder',$nextfmsorder)->where('production_flow_id',$factory)->get();
			if($restyuwew->num_rows()>0)
			{
			foreach($restyuwew->result() as $resttssa);
			$nextflow=$resttssa->flow_id;
			}else
			{
			$nextflow=0;
			}

			}else{

				$nextflow=$nextff;
			}

			if($nextflow!=0)
			{
			$stage=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$nextflow);
			$this->db->insert('order_stage',$stage);

			/** Add Tat **/
			$stageid=$this->db->insert_id();
			$settatdate=$this->fmsmodel->gettatformis($stageid,$nextflow,0,$jobcardid,$orderid);
			if($settatdate<>'')
			{
			$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
			$settat=array('orderstageid'=>$stageid,'flowstage'=>$nextflow,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
			$this->db->insert('fmstatdate',$settat);
			}
			/** END **/
			}
			}
			/** End **/
			
			
			
			/** Mark Full Kitting as Done **/

	if(count($selecteditems)==0)
		{

			$nextff=$this->checkformoveto($planstartfrom);
			if($nextff==0)
			{
			$updatenextflow=$this->getnextflowid($planstartfrom,$factory);
			}else
			{
				$updatenextflow=$nextff;
			}

			$odstage=$this->getorderstageid($jobcardid,$updatenextflow);

			$donedata=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'));
			$this->db->where('id',$odstage);
			$this->db->update('order_stage',$donedata);

			$settatdate=$this->fmsmodel->gettatformis($odstage,$updatenextflow,0,$jobcardid,$orderid);
			if($settatdate<>'')
			{
			$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
			$settatss=array('orderstageid'=>$odstage,'flowstage'=>$updatenextflow,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
			$this->db->insert('fmstatdate',$settatss);
			}

			/** UPDATE NEW ***/

			$nextff=$this->checkformoveto($updatenextflow);
			if($nextff==0)
			{
			$updatenextflow1=$this->getnextflowid($updatenextflow,$factory);
			}else
			{
				$updatenextflow1=$nextff;
			}


			$isthere=$this->checkiforderstageisalreadythere($updatenextflow1,$jobcardid);
			if($isthere==0)
			{

				$addstage=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$updatenextflow1,'userstatus'=>'0','addedBy'=>date('Y-m-d H:i:s'));
				$this->db->insert('order_stage',$addstage);
				$odstage11=$this->db->insert_id();
				$settatdate=$this->fmsmodel->gettatformis($odstage11,$updatenextflow1,0,$jobcardid,$orderid);
			if($settatdate<>'')
			{
			$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
			$settatss=array('orderstageid'=>$odstage11,'flowstage'=>$updatenextflow1,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
			$this->db->insert('fmstatdate',$settatss);
			}

			}






		}


			/** END **/
			
	
		
		
		
		/** END ***/
	}
	
	function getinstrumentid($jobcardid)
	{
		$Resteyu=$this->db->select('item_id')->from('order_instruments')->where('id',$jobcardid)->get();
		if($Resteyu->num_rows()>0)
		{
			foreach($Resteyu->result() as $Resteyu1);
			
			return $Resteyu1->item_id;
			
		}else
		{
			echo "INSTRUMENT NOT FOUND"; exit;
		}
		
		
	}


	
	
function generateprfromplanning($pr,$jobcardid,$selecteditems,$selecteditemsqty,$unit,$currentstock,$purnoonly)
{
	$selecteditem=$selecteditems;
	$selectedqty=$selecteditemsqty;		
	$units=$unit;
	$currentstocks=$currentstock;
	$userid=$_SESSION['logged_in']['user_id'];
	for($r=0;$r<count($selecteditem);$r++)
	{
		$itemid=$selecteditem[$r];
		$itemqty=$selectedqty[$r];
		$unit=$units[$r];
		$currstock=$currentstocks[$r];
		
		$qtyss=$itemqty;
		$masterid=$itemid;
		$prreason='';
		
		$dataya=array('masterid'=>$masterid,'itemid'=>$itemid,'qty'=>$qtyss,'prno'=>$pr,'jobcardid'=>$jobcardid,'unit'=>$unit,'addedBy'=>$userid,'addedOn'=>date('Y-m-d H:i:s'),'stockattimeofpr'=>$currstock,'prraisereason'=>$prreason,'source'=>'1','purno'=>$purnoonly);

		$this->db->insert('purchase_request',$dataya);
		
		
	}
	return $pr;
	
	
	
}
	
	function generatenewprnumber()
	{
		
		$prnos=$this->db->query('SELECT id, purno FROM mitrdemo.purchase_request ORDER BY  CAST(purno AS decimal) DESC LIMIT 1');
	//$prnos=$this->db->select('id,purno')->from('purchase_request')->order_by('id','DESC')->limit(1)->get();
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
		
	 return $code;
		
	
	}

	function checkformoveto($planstart)
	{
	$Resty=$this->db->select('moveto')->from('fms_flow')->where('flow_id',$planstart)->get();
	if($Resty->num_rows()>0)
	{
	foreach($Resty->result() as $Resty1);

	return $Resty1->moveto;

	}else
	{
	return 0;

	}

	}


	function getorderstageid($jobcardid,$planstartfrom)
	{
		$rrest=$this->db->select('id')->from('order_stage')->where('jobcardid',$jobcardid)->where('flowstage',$planstartfrom)->get();
		if($rrest->num_rows()>0)
		{
			foreach($rrest->result() as $resyyy);

			return $resyyy->id;

		}else
		{
			return 0;
		}

	}
	
	
	function getnextflowid($planstartfrom,$factory)
	{
			$Resteyueueue=$this->db->select('setorder')->from('fms_flow')->where('flow_id',$planstartfrom)->get();
			if($Resteyueueue->num_rows()>0)
			{ 
			foreach ($Resteyueueue->result() as $Resteyueueue1);
			$nextorder=$Resteyueueue1->setorder+1;

			$Resteyueueue1=$this->db->select('flow_id')->from('fms_flow')->where('setorder',$nextorder)->where('production_flow_id',$factory)->get();
			if($Resteyueueue1->num_rows()>0)
			{
			foreach ($Resteyueueue1->result() as $Resteyueueue111);

			return $Resteyueueue111->flow_id;

			}else
			{
			echo "Next Flow not found"; exit;
			}
			}else
			{
			echo "Next Flow not found"; exit;
			}
	}


	function checkiforderstageisalreadythere($flowstage,$jobcardid)
	{
		$rreteyu=$this->db->select('id')->from('order_stage')->where('flowstage',$flowstage)->where('jobcardid',$jobcardid)->get();
		return $rreteyu->num_rows();

	}

	function checkforprdetailshow($flowid)
	{
		$rrsteyr=$this->db->select('flow_id')->from('fms_flow')->where('giveprdetails','1')->where('flow_id',$flowid)->get();

		return $rrsteyr->num_rows();

	}

	function checkforprno($jobcard)
	{

	$restue=$this->db->select('prno')->from('purchase_request')->where('jobcardid',$jobcard)->group_by('prno')->get();
	if($restue->num_rows()>0)
	{
		foreach($restue->result() as $restue1);

		return $restue1->prno;

	}else
	{
		return '';
	}

	}


function getmarketingpersonemail($user_id)
{
$email='';

$reste=$this->db->select('email')->from('system_users')->where('user_id',$user_id)->get();
if($reste->num_rows()>0)
{
foreach($reste->result() as $reste1);

$email=$reste1->email;


}

return $email;

}

function checkreadyforbilling()
{

			$scheduler_data[]=0;
			$this->db->select('a.order_id')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left');
			$query = $this->db->where('a.order_status','1')->where('a.closeorder','0')->where('a.movetodispatch','1')->where('a.totalpacket !=','')->order_by('a.order_id','desc')->get();
			$res = $query->result();
			$i=1;
			foreach($res as $row)
			{

			$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1');
			$this->db->where('finalpacked','0'); 

			$completedjobcard=$this->db->get();
			$completedjobcards=$completedjobcard->num_rows();

			$query1 = $this->db->select('a.order_id,a.finalpacked')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
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

			$scheduler_data[] = 1;
			}
			}
			}
	

	return array_sum($scheduler_data); 


}

function allflowandid()
{

$query = $this->db->select('a.fms_flow,a.flow_id,a.production_flow_id')->from('fms_flow a')->where('a.status','1')->where('a.production_flow_id','1')->get();
$res = $query->result();

return $res;

}


function dashboardplannedtoactualonlyforsales($productionflow,$flowid,$type,$selorder)
	{
	    /*GET CURRENT PROCESS SORT ORDER*/
        $QRY = $this->db->select('total_days,set_time, tat')->from('fms_flow')->where('flow_id',$flowid)->where('production_flow_id',$productionflow)->get();
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
        
        	/** Get Pending Count **/
	 $mmer=$this->checkifmsmerge($flowid);
	$skkipl=$this->checkifstepisskippable($flowid);
	/** CHECK FOR DEPENDENCY **/
    $checkdepend=$this->db->select('dependency')->from('fms_flow')->where('flow_id',$flowid)->where('dependency','1')->get();
    $depend=$checkdepend->num_rows();
	 
		if($mmer==0 && $skkipl==0 && $depend==0)
		 {
			$pend=$this->db->select('a.id as orderstageid,a.jobcardid')->from('order_stage a')->join('prestogroup_orders b','a.orderid=b.order_id')->where('b.order_type',$type)->where('b.selforder',$selorder)->where('a.userstatus','0')->where('a.flowstage',$flowid)->get();
			$pendcount=$pend->num_rows();
		 }else{
			 
			 $pendcount=$this->menunotificationsformergeflowoverallforsales($flowid,$productionflow,$type,$selorder);
			 
		
			
		 }
		 
		 	 return $pendcount;
		
	    
	    
	
	}
	
	
	
	function menunotificationsformergeflowoverallforsales($flowid,$productionflow,$type,$selorder)
	{
		$arr[]=0;
			$flowstage=$flowid;
			$productionflowid=$productionflow;
			$user_id =$this->session->userdata['logged_in']['user_id'];
			$fmsprocess=$productionflowid;
		if($fmsprocess<>0)
		{
		/** Get Process Name **/
		$process=$this->db->select('a.jumpfabricationappl,a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow,d.fabrication,f.planstartsfrom')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('e.order_type',$type)->where('e.selforder',$selorder)->where('b.userstatus','0')->get();
		/** End **/
		if($process->num_rows()>0)
		{
			//	echo "<pre>"; print_r($process->result());exit;
			$i=1;
			foreach($process->result() as $proc)
			{
				
				
				/** Check for dependency **/
				
				$checkdepend=$this->db->select('dependency')->from('fms_flow')->where('flow_id',$flowstage)->where('dependency','1')->get();
				$depend=$checkdepend->num_rows();
				//echo $depend;exit;
				if($depend==1)
				{
					/** Check if Dependent flow has any data in orderstage **/
					$availableflowstage=array();
				/** Check if plan has been started from setorder 1 **/
				$plannedstageorderno=$this->getplanstartsfromorderno($proc->planstartsfrom);
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
					
					
					
					$checkskiiped=$this->checkifdatahascomeskipped($proc->jobcardid,$flowstage);
					
					if($checkskiiped>0)
					{
						$availableflowstage = array_diff($availableflowstage,$checkskiiped);
					}
					
				}
					
					/** End **/
					
					/** Check if instrument requires fabrication **/
					/**$ryt=$this->db->select('b.fabrication')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->where('a.id',$proc->jobcardid)->get();
					if($ryt->num_rows()>0)
					{
						foreach($ryt->result() as $rtyeyte);
						$fabreq=$rtyeyte->fabrication;
						

					}else{ $fabreq=0 ;} **/
					
					/** Get Last Step of Fabrication **/
					/**if($fabreq==0)
							{
						
								$isffin=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id','5')->where('finalstep','1')->get();
								$isfinal=$isffin->num_rows();
								if($isfinal!=0)
								{
									foreach($isffin->result() as $isffin1);
									$ignoreflow=$isffin1->flow_id;
								}
												
							}else{ $isfinal=0; } **/
					/** End **/
					
				
					
if(count($availableflowstage)>0)
					{	
				
				$availableflowstages = "'" . implode ( "', '", $availableflowstage ) . "'";
				if($plannedstageorderno!='1')
				{
				$this->db->select('a.dependentflowid')->from('flowdependency a')->join('order_stage b','a.dependentflowid=b.flowstage')->where('a.flowid',$flowstage);

					//$availableflowstages = "'" . implode ( "', '", $availableflowstage ) . "'";

					//$this->db->where_in('a.dependentflowid',$availableflowstages,false);

					$this->db->where('b.jobcardid',$proc->jobcardid)->where('b.orderid',$proc->originalorderid);
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
					
				$markapp=$this->checkformergerpoint($flowstage,$proc->jobcardid,$proc->originalorderid,$proc->fabrication,$proc->jumpfabricationappl);
				}
				

				
				/** End **/
				
				if($markapp==1)
				{
			  
					
					$arr[]=1;
					
				}			
										  
										  
		
			}
		}
		
		
	}
		
		return array_sum($arr);
		
		
	}
	
	
	function pendingordercountsales()
{
    $lott=array();
            $order=$this->db->select('a.order_id,b.internal_order_no,b.added_on,b.company_name,c.first_name,c.last_name')->from('order_instruments a')->join('prestogroup_orders b','a.order_id=b.order_id')->join('order_planning d','d.order_id=b.order_id')->join('system_users c','b.marketing_person=c.user_id')->where('a.complete','0')->where('factory !=','2')->where('b.order_type','SALE')->where('b.selforder','0');
            $order=$this->db->group_by('a.order_id')->order_by('b.added_on','ASC')->get();
        
        if($order->num_rows()>0)
        {
            foreach($order->result() as $order1)
            { 
                    $inst=$this->db->select('c.factory,b.id,a.instruments_name,b.job_card_no')->from('presto_instruments a')->join('order_instruments b','a.id=b.item_id')->join('order_planning c','b.id=c.jobcard_id')->where('b.complete','0')->where('b.order_id',$order1->order_id)->get();
                    if($inst->num_rows()>0)
                    {
                        $lott[]=1;
                    }
            }			
        
        }
        
        if(count($lott)>0)
        {
           return array_sum($lott);
        }else
        {
            return 0;
        }
        
}



	function saveCompletionDate($data) {
		$this->db->insert('service_completion_date', $data);
		return $this->db->affected_rows();
	}

	function getServiceDates($order_id, $job_card_id) {
		$arr = array();
	$sql = $this->db->select('completion_date')
					 ->from('service_completion_date')
					 ->where('order_id', $order_id)
					 ->where('job_card_id', $job_card_id)
					 ->order_by('id', 'desc')
					 ->get();

		if ($sql->num_rows() > 0) {
			foreach ($sql->result() as $row) {
			$arr[] = date('d-M-Y',strtotime($row->completion_date));
			}
		}

		return $arr;
	}

	
}
