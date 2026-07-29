<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fms_model extends CI_Model {

	public function __construct()
	{
		
		parent::__construct();
		
	}

	
	
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
							
								$previoussteptime=date('H:i',strtotime($previouscomtime));
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
						
						$previoussteptime=date('H:i',strtotime($previouscomtime));
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
		
		$odplan=$this->db->select('a.id')->from('order_planning a')->join('prestogroup_orders b','a.order_id=b.order_id')->where_in('a.orderstatus','2,3',false)->where('b.added_by',$userid)->get();
		
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
	
	
	
	
}