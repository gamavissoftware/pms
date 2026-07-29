<?php
ini_set("memory_limit","256M");
	defined('BASEPATH') OR exit('No direct script access allowed');

	class Orderstage extends CI_Controller {
		
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
			$this->load->model('User_model','user');
			$this->load->model('Master_model','master');
			$this->load->model('Fms_model','fmsmodel');
				$this->load->model('Store_model','storemodel');
		
$ip = $_SERVER["REMOTE_ADDR"];
		 /*$query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
            if($query->num_rows()=='0'){
            $this->session->set_flashdata('message','<span class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</span><br/>');
            redirect(page_url.'Dashboard');    
            }*/		
		}
		
		function process()
		{
		$flowid=$this->uri->segment(3);
		$fflow=$this->db->select('a.filename')->from('uitype a')->join('fms_flow b','a.id=b.uitype')->where('b.flow_id',$flowid)->where('uitype !=','0')->get();
		if($fflow->num_rows()>0)
		{
			foreach($fflow->result() as $flow);
			$pagename=$flow->filename;
			//echo $pagename;exit;
			$this->load->view('FMS/'.$pagename);
		
		/*if($flowid=='1')
		{
		$this->load->view('FMS/full-kitting-bop');
		}else if($flowid=='2' || $flowid=='3' || $flowid=='4' || $flowid=='5')
		{
		$this->load->view('FMS/orderprocess');
			
		}else if($flowid=='6')
		{
			$this->load->view('FMS/qc-inprocess');
			
		}
		else if($flowid=='7')
		{
		
			$this->load->view('FMS/material-out-paint');
			
		}else if($flowid=='8')
		{
		
			$this->load->view('FMS/material-in-paint');
			
		}else if($flowid=='13')
		{
		
			$this->load->view('FMS/qc-final');
			
		}else if($flowid=='9')
		{
			$this->load->view('FMS/material-out-plating');
			
			
		}else if($flowid=='10')
		{
			$this->load->view('FMS/material-in-plating');
			
			
		}else if($flowid=='11')
		{
			$this->load->view('FMS/orderprocess');
			
		}else if($flowid=='12'){
			$this->load->view('FMS/orderprocess');
		}**/
		
		}else{
			
			echo "Response Type not assigned";exit;
		}
		
		
		}
		
		
		function processoldd12()
		{
		$flowid=$this->uri->segment(3);
		if($flowid=='1')
		{
		$this->load->view('FMS/full-kitting-bop');
		}else if($flowid=='2' || $flowid=='3' || $flowid=='4' || $flowid=='5')
		{
		$this->load->view('FMS/orderprocess');
			
		}else if($flowid=='6')
		{
			$this->load->view('FMS/qc-inprocess');
			
		}
		else if($flowid=='7')
		{
		/** Material IN **/
			
			
		}else if($flowid=='8')
		{
		/** Material Out **/
			
			
		}else if($flowid=='9')
		{
			$this->load->view('FMS/orderprocess');
			
		}else if($flowid=='10')
		{
			$this->load->view('FMS/orderprocess');
			
		}else if($flowid=='11')
		{
			
			$this->load->view('FMS/qc-final');
		}
		}
		function processoldd()
		{
		$flowid=$this->uri->segment(3);
		if($flowid=='1' || $flowid=='2')
		{
		$this->load->view('FMS/orderprocess');
		}else if($flowid=='3')
		{
			$this->load->view('FMS/full-kitting-bop');
		}else if($flowid=='4')
		{
			$this->load->view('FMS/orderprocess');
			
		}else if($flowid=='5')
		{
			$this->load->view('FMS/qc-inprocess');
			
		}else if($flowid=='6')
		{
			$this->load->view('FMS/orderprocess');
			
		}else if($flowid=='7')
		{
			$this->load->view('FMS/orderprocess');
			
		}else if($flowid=='8')
		{
			$this->load->view('FMS/qc-final');
		}
		}
		
		function order_list()
		{
			$scheduler_data=array();
			$flowstage=$this->uri->segment(3);
			$productionflowid=$this->uri->segment(4);
		/*GET CURRENT PROCESS SORT ORDER*/
			$QRY = $this->db->select('total_days,tat,set_time, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();
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
			$currentprocessorder = $flowstage;
		$setorder=$fmsinformation->setorder;
			/** End **/
			
			
			$user_id =$this->session->userdata['logged_in']['user_id'];
			$fmsprocess=$productionflowid;
	
	if($fmsprocess<>0)
	{
		/** Get Process Name **/
		$this->db->select('d.file_number,f.ordertype,f.fileno,d.file_number,a.jumpfabricationappl,a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate, c.lot_no, b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow,d.fabrication,f.planstartsfrom')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage);
		if($_SESSION['logged_in']['role']!=1){
		$this->db->where('a.who_wedo',$user_id);
	}
	$process=$this->db->order_by('b.userstatus','ASC')->order_by('b.addedOn','DESC')->order_by('e.selforder','ASC')->get();
		/** End **/
		if($process->num_rows()>0)
		{
			//	echo "<pre>"; print_r($process->result());exit;
			$i=1;
			foreach($process->result() as $proc)
		{

			$ordertype = $proc->ordertype;
			if($ordertype=='1'){
				
				$fileno=$proc->file_number;
			}else{
			
				$fileno=$proc->fileno;
			}


				if($proc->userstatus=='0')
				{
					$insname=str_replace("'",'',$proc->instruments_name);
				$prestui="<span class='btn btn-danger btn-xs'>Pending</span>";
				$action='<a href="javascript:;" onclick="markstagedone('."'".$proc->orderstageid."'".','."'".$proc->flowstage."'".','."'".$proc->originalorderid."'".','."'".$proc->jobcardid."'".','."'".$productionflowid."'".','."'".$proc->job_card_no."'".','."'".$insname."'".')"><span class="btn btn-xs btn-warning">Mark as done</span></a>';
				$completedate="";
				$completetime="";
				}else{
				$compdate=date('d-M-Y H:i:s',strtotime($proc->stagecompletedate));
				$prestui="<span class='btn btn-success btn-xs'>Machine Started</span>";
				$action="This Task has been completed by You.";
				date_default_timezone_set("Asia/Kolkata");
				$completedate = date('d-M-Y', strtotime($proc->stagecompletedate));
				$time1 = date('H:i:s', strtotime($proc->stagecompletedate));
				$completetime = "<br>". date('g:i A', strtotime($time1));
				}
				
			
				/** Get User Department **/
				$udep=$this->db->select('b.department')->from('system_users a')->join('departments b','a.department_id=b.department_id')->where('a.user_id',$_SESSION['logged_in']['user_id'])->get(); 
				if($udep->num_rows()>0)
				{
					foreach($udep->result() as $depp);
					$depart=$depp->department;
					
				}else
				{
					$depart='';
				}
				/** End **/
				
				
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
				

				
				/** End **/
				
				if($markapp==1)
				{
			   $timestamp=$this->getprevioustimestamp($tatstage,$proc->plannedOn,$proc->jobcardid,$flowstage,$proc->orderstageid);
				$tattime = DateTime::createFromFormat('d-M-Y g:i A', $timestamp);
				$finaltattime=$tattime->format('g:i A');
			   $tatdate=$this->getupcomingtatdatenew($tatstage,$proc->plannedOn,$proc->jobcardid,$timestamp,$totaldaysslave,$proc->orderstageid,$day);
				
					if($proc->userstatus=='0')
					{
						$completedon="";
					}else
					{
						$completedon=date('d-M-Y g:i A',strtotime($proc->stagecompletedate));
					}

					$prdetailshow= $this->fmsmodel->checkforprdetailshow($flowstage);
					$autopr=$this->fmsmodel->checkifthisstepisautoprstep($flowstage);
					
					if($prdetailshow>0 || $autopr==1)
					{
						$prno=$this->fmsmodel->checkforprno($proc->jobcardid);
						if($prno<>'')
					{
							$pstatus=$this->storemodel->getprstatus($prno);
					}else
					{
						$prno='';
						$pstatus='';
					}
					}else
					{
						$prno='';
						$pstatus='';
					}

					$odtype=$this->getordertype($proc->originalorderid);
					$scheduler_data[] = array('sr_no'=>$i,
					'orderdate'=>$timestamp,
					'qty'=>$proc->qty,
					'odtype'=>$odtype,
					'mname'=>strtoupper($proc->instruments_name),
					'jobcard'=>strtoupper($proc->job_card_no),
					'lotno'=>$proc->lot_no,
					'factory'=>strtoupper($depart),
					'tat'=>$tatdate,
					'actual'=>$completedon,
					'totdays'=>'',
					'prno'=>"<a href='".page_url."Store/pr/".$prno."' target='_blank'>".$prno."</a>",
					'prstatus'=>$pstatus,
					'status'=>$prestui,
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
	}
	
		
		function order_listtempold()
		{
			$scheduler_data=array();
			$flowstage=$this->uri->segment(3);
			$productionflowid=$this->uri->segment(4);
			/*GET CURRENT PROCESS SORT ORDER*/
			$QRY = $this->db->select('total_days, tat, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();
			$res = $QRY->result();
			foreach($res as $fmsinformation);
			$totaldaysslave = $fmsinformation->total_days;
			$tatstage = $fmsinformation->tat;
			//echo $tatstage;exit;	
			$currentprocessorder = $flowstage;
			$setorder=$fmsinformation->setorder;
			
			
			
			
			/*GET CURRENT PROCESS SORT ORDER*/
			
			
			
			
			$user_id =$this->session->userdata['logged_in']['user_id'];
			/*$isfms=$this->db->select('fms_process')->from('system_users')->where('user_id',$user_id)->get();
	if($isfms->num_rows()>0)
	{
	foreach($isfms->result() as $isfmsa);
	$fmsprocess=$isfmsa->fms_process;
	}else{ $fmsprocess=0; } */
	$fmsprocess=$productionflowid;
	/** END **/
	if($fmsprocess<>0)
	{
		/** Get Process Name **/
		$process=$this->db->select('a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('a.who_wedo',$user_id)->order_by('b.userstatus','ASC')->order_by('b.addedOn','DESC')->get();
		/** End **/
		if($process->num_rows()>0)
		{
			//	echo "<pre>"; print_r($process->result());exit;
			$i=1;
			foreach($process->result() as $proc)
		{
				if($proc->userstatus=='0')
				{
				$prestui="<span class='btn btn-danger btn-xs'>Pending</span>";
				$action='<a href="javascript:;" onclick="markstagedone('."'".$proc->orderstageid."'".','."'".$proc->flowstage."'".','."'".$proc->originalorderid."'".','."'".$proc->jobcardid."'".','."'".$productionflowid."'".','."'".$proc->job_card_no."'".','."'".$proc->instruments_name."'".')"><span class="btn btn-xs btn-warning">Mark as done</span></a>';
				$completedate="";
				$completetime="";
				}else{
				$compdate=date('d-M-Y H:i:s',strtotime($proc->stagecompletedate));
				$prestui="<span class='btn btn-success btn-xs'>Machine Started</span>";
				$action="This Task has been completed by You.";
				date_default_timezone_set("Asia/Kolkata");
				$completedate = date('d-M-Y', strtotime($proc->stagecompletedate));
				$time1 = date('H:i:s', strtotime($proc->stagecompletedate));
				$completetime = "<br>". date('g:i A', strtotime($time1));
				}
				
				
				date_default_timezone_set("Asia/Kolkata");
				$addeddate = date('d-M-Y', strtotime($proc->added_on));
				$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
				$time = date('H:i:s', strtotime($proc->added_on));
				$addedtime = "<br>". date('g:i A', strtotime($time));
				$time1=date('H:i:s', strtotime($proc->plannedOn));
				$addedtime1 = "<br>". date('g:i A', strtotime($time1));
				$planneddate1=date('Y-m-d',strtotime($proc->plannedOn));
				/*TAT Date*/
				$lastcompdate = date('Y-m-d', strtotime($proc->added_on));
				$TATDATE=date('d-M-Y', strtotime($lastcompdate."+".$totaldaysslave." days"));
				$totalday="1";
				
				
				
				
			/*GET DATE OF PRIVIOUS STEPS*/
						$previousorder = $tatstage;
						
						if($previousorder<>0){
						$abc = $this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$proc->jobcardid)->order_by('id','DESC')->limit(1)->get();
							if($abc->num_rows()>0){
						foreach($abc->result() as $pastinfo);
						$complitiontime = $pastinfo->addedOn;
							
								$lasttaskcompletedtime = "<br>". date('g:i A', strtotime($complitiontime));

								
							}else{
							$complitiontime = $proc->added_on;
									$lasttaskcompletedtime = "<br>". date('g:i A', strtotime($complitiontime));

							}
						}else{
						$complitiontime = $proc->plannedOn;	
								$lasttaskcompletedtime = "<br>". date('g:i A', strtotime($complitiontime));

						}
						
						/*PREVIOUS COMPLITION DATE*/
						
						$checkpreviousdate = date('Y-m-d', strtotime($complitiontime));
						$previouscomplitiondate = date('d-M-Y', strtotime($complitiontime));
						$previouscomplitiontime = date('H:i:s', strtotime($proc->added_on));
						$previouscomplitionfinaltime = "<br>". date('g:i A', strtotime($previouscomplitiontime));
				
						/*GET DATE OF PRIVIOUS STEPS*/
						$date11 = new DateTime($previouscomplitiondate); 
						$date22 = new DateTime($completedate); 
						$interval1 = $date11->diff($date22); 
						$ttldays = $interval1->d; 
						if($ttldays=='0'){
						$totalday = "1";
						}else{
						$totalday = $ttldays;
				}
				
				/*TAT Date*/
				$lastcompdate = date('Y-m-d', strtotime($checkpreviousdate));
				$TATDATE=date('d-M-Y', strtotime($lastcompdate."+".$totaldaysslave." days"));
				//echo $TATDATE; 
				/*TAT Date*/
				
			
			
	/*** MANGLESH TEST**/
		$previousorder = $setorder-1; 
				$productionflowids = $this->uri->segment(4);
		$query22 = $this->db->select('id, parallel')->from('production_flow')->where('id',$productionflowid)->get();		
		foreach($query22->result() as $productionflowinfo);
		if($productionflowinfo->parallel=='1'){
			
			if($this->uri->segment(3)=='2'){
			$qy = $this->db->select('jobcard_id, plannedOn')->from('order_planning')->where('jobcard_id',$proc->jobcardid)->get();
				foreach($qy->result() as $plannedorderinfomation);
				$addeddate = date('Y-m-d', strtotime($plannedorderinfomation->plannedOn));
				$time = date('H:i:s', strtotime($plannedorderinfomation->plannedOn));
				$addedtime = "<br>". date('g:i A', strtotime($time));
			
			}
			else{
		  $QRY = $this->db->select('total_days, tat,setorder, flow_id ')->from('fms_flow')->where('setorder',$previousorder)->get();
		if($QRY->num_rows()>0){
		foreach($QRY->result() as $paststepdate);
			$flowid = $paststepdate->flow_id;
		$QRY1 = $this->db->select('jobcardid, flowstage,addedOn')->from('order_stage')->where('jobcardid',$proc->jobcardid)->where('flowstage',$flowid)->get();	
		if($QRY1->num_rows()>0){
		foreach($QRY1->result() as $timestamp){
		$addeddate1 = date('Y-m-d', strtotime($timestamp->addedOn));
	$time = date('H:i:s', strtotime($timestamp->addedOn));
	$addedtime = "<br>". date('g:i A', strtotime($time));
		}
		}	
		}
		}	
			
		}else{
		  $QRY = $this->db->select('total_days, tat,setorder, flow_id ')->from('fms_flow')->where('setorder',$previousorder)->get();
		if($QRY->num_rows()>0){
		foreach($QRY->result() as $paststepdate);
			$flowid = $paststepdate->flow_id;
		$QRY1 = $this->db->select('jobcardid, flowstage,addedOn')->from('order_stage')->where('jobcardid',$proc->jobcardid)->where('flowstage',$flowid)->get();	
		if($QRY1->num_rows()>0){
		foreach($QRY1->result() as $timestamp){
		$addeddate1 = date('Y-m-d', strtotime($timestamp->addedOn));
	$time = date('H:i:s', strtotime($timestamp->addedOn));
	$addedtime = "<br>". date('g:i A', strtotime($time));
		}
		}	
		}else{
		$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
		}
		}		
				
		
		/*** MANGLESH TEST**/
				
				/** Get User Department **/
				$udep=$this->db->select('b.department')->from('system_users a')->join('departments b','a.department_id=b.department_id')->where('a.user_id',$_SESSION['logged_in']['user_id'])->get(); 
				if($udep->num_rows()>0)
				{
					foreach($udep->result() as $depp);
					$depart=$depp->department;
					
				}else
				{
					$depart='';
				}
				
				/** End **/
				
				/** Check for dependency **/
				
				$checkdepend=$this->db->select('dependency')->from('fms_flow')->where('flow_id',$flowstage)->where('dependency','1')->get();
				$depend=$checkdepend->num_rows();
				//echo $depend;exit;
				if($depend==1)
				{
					
					$restt=$this->db->select('dependentflowid')->from('flowdependency')->where('flowid',$flowstage)->get();
					if($restt->num_rows()>0)
					{
						$dependcount=$restt->num_rows();
						$donarr=array();
						$donarr[]=0;
						//echo "<pre>";print_r($restt->result());exit;
						foreach($restt->result() as $resttyu)
						{
							$isdone=$this->db->select('id,userstatus')->from('order_stage')->where('flowstage',$resttyu->dependentflowid)->where('jobcardid',$proc->jobcardid)->where('orderid',$proc->originalorderid)->order_by('id','DESC')->limit(1)->get();
							if($isdone->num_rows()>0)
							{
								foreach($isdone->result() as $isdone11);
								$donarr[]=$isdone11->userstatus;
								
							}else
							{
								$markapp=1;
							}
							
						}
						//echo $dependcount.'<br>'."<pre>"; print_r($donarr);
						if($dependcount==array_sum($donarr))
						{
							$markapp=1;
						}else{
							$markapp=0;
						}
						
					}else{
						$markapp=1;
					}
					
				}else{
					
					$markapp=1;
				}
				/** End **/
				/** New Timestamp query **/
				$previousorder=$tatstage;
				if($previousorder==0)
				{
					$previouscomtime=$proc->plannedOn;
					$previouscomdate=date('d-M-Y',strtotime($previouscomtime))."<br>";
					$previouscomtimeonly=date('g:i A',strtotime($previouscomtime));
					
				}else
				{
					//echo $previousorder;exit;
					$oldtime1=$this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$proc->jobcardid)->order_by('id','DESC')->limit(1)->get();
							if($oldtime1->num_rows()>0){
								foreach($oldtime1->result() as $pastinfo);
								$previouscomtime=$pastinfo->addedOn;
								$previouscomdate=date('d-M-Y',strtotime($pastinfo->addedOn))."<br>";
								$previouscomtimeonly=date('g:i A',strtotime($pastinfo->addedOn));
							}else
							{
								$previouscomtime=$proc->plannedOn;
					$previouscomdate=date('d-M-Y',strtotime($previouscomtime))."<br>";
					$previouscomtimeonly=date('g:i A',strtotime($previouscomtime));
							}
				}

		
				
				/** End **/
			//echo "<pre>"; print_r($proc);exit;
				if($markapp==1)
				{
			/** Holiday Check**/
				
					$TATDATEFORHOLIDAYCHECK=date('Y-m-d', strtotime($TATDATE));
					$timestampforcheck=date('Y-m-d',strtotime($previouscomtime));
					$date_from=strtotime($timestampforcheck);
					$date_to=strtotime($TATDATEFORHOLIDAYCHECK);
					$betweendates=array();
					for ($g=$date_from; $g<=$date_to; $g+=86400) {  
						$betweendates[]= date("Y-m-d", $g);  
					} 
				
				//echo "<pre>"; print_r($betweendates);
					if(count($betweendates)>0)
				{
					
					$alldates = "'" . implode ( "', '", $betweendates ) . "'";
					
					$ifholiday=$this->db->select('holiday_id')->from('prestogroup_holidays')->where_in('holiday_date',$alldates,false)->get();
					if($ifholiday->num_rows()>0)
					{
			
						//echo "hi".$proc->job_card_no;
						$TATDATEFORHOLIDAYCHECK=date('Y-m-d', strtotime($TATDATEFORHOLIDAYCHECK."+".$ifholiday->num_rows()." days"));
						//echo $TATDATEFORHOLIDAYCHECK;
						
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
				//$TATDATE= date('d-M-Y', strtotime($nextdate."+1 days"));
				$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();
				if($query->num_rows()>0){
					
					foreach($query->result() as $afterholiday);
					$aftrholidaydate = $afterholiday->holiday_date;
					$TATDATE= date('d-M-Y', strtotime($aftrholidaydate."+1 days"));
					
				}else {
				$TATDATE= date('d-M-Y', strtotime($nextdate));
				}
			}else{
			$TATDATE=date('d-M-Y', strtotime($TATDATEFORHOLIDAYCHECK));
			}
					
				/** End **/
					
			$scheduler_data[] = array('sr_no'=>$i,
				'orderdate'=>$previouscomdate.$previouscomtimeonly,
				'mname'=>strtoupper($proc->instruments_name),
				'jobcard'=>strtoupper($proc->job_card_no),
				'factory'=>strtoupper($depart),
				'planned'=>date('d-M-Y',strtotime($proc->plannedOn)).$addedtime1,
				'tat'=>$TATDATE."<br>".$previouscomtimeonly,
				'actual'=>$completedate.$completetime,
				'totdays'=>$totalday,
				'status'=>$prestui,
				'action'=>$action);	
				}			
										  
										  
		$i++;
		}
		
		$results = array(
		"sEcho" => 1,
		"iTotalRecords" => count($scheduler_data),
		"iTotalDisplayRecords" => count($scheduler_data),
		"aaData"=>$scheduler_data);
		echo json_encode($results);
			
		}
		
	}
	}

		
		function markdone()
	{
		$orderstage=$this->uri->segment(3);
		$flowstage=$this->uri->segment(4);
		$orderid=$this->uri->segment(5);
		$selforder=$this->fmsmodel->getselforder($orderid);
		$jobcardid=$this->uri->segment(6);
		$productionflowid=$this->uri->segment(7);
		$user_id =$this->session->userdata['logged_in']['user_id'];
		$newstage=[];
		$isfinalstep=0;
		
		/** Move to next stage **/
			$currentstage=$flowstage;
			$getset=$this->db->select('setorder,finalstep,total_days,set_time')->from('fms_flow')->where('production_flow_id',$productionflowid)->where('flow_id',$currentstage)->get();
			
				foreach($getset->result() as $ggetset);
				$currentsetorder=$ggetset->setorder;
				$isfinalstep=$ggetset->finalstep;
				$tatday=$ggetset->total_days;
				$tathours=$ggetset->set_time;
				
				/** Get Next Order **/
				$getneworder=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id',$productionflowid)->where('setorder>',$currentsetorder)->limit(1)->order_by('setorder','ASC')->get();
				if($getneworder->num_rows()>0)
			{
				foreach($getneworder->result() as $getneworder1);
				/** Check Move **/	
				$restymove=$this->db->select('moveto')->from('fms_flow')->where('flow_id',$flowstage)->get();
				foreach($restymove->result() as $restymove1)
				{
					//echo $restymove1->moveto;exit;
				if(($restymove1->moveto<>0) && ($restymove1->moveto<>''))
				{
				$moveflow=$restymove1->moveto;
				$movearr=explode(',',$moveflow);
				foreach($movearr as $movea)
				{
				$newstage[]=$movea;
				}
				
				} else
				{
			
					$newstage[]=$getneworder1->flow_id;
				} 
				}			
				
				
				
				//$newstage=$getneworder1->flow_id;
				
			}
				/** End **/
				
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);
			$effrow=$this->db->affected_rows();	
			
			
			if(count($newstage)>0)
			{
				
			
			for($u=0;$u<count($newstage);$u++)
			{
				
			$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$newstage[$u])->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
			if($newstage[$u]<>0)
			{
			$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage[$u],'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
			/** Add Tat **/
						$stageid=$this->db->insert_id();
						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
					
						
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
    						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
					
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$newstage[$u]);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			}
			}else{

foreach($checkodd->result() as $checkodd1);


							/** Add Tat **/
							$stageid=$checkodd1->id;
							$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);

							if($settatdate<>'')
							{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
							if($checkfmstat->num_rows()==0)
							{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
							$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
							$this->db->insert('fmstatdate',$settat);
							}else
							{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));

							$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'));
							$this->db->where('flowstage',$newstage[$u]);
							$this->db->where('jobcardid',$jobcardid);
							$this->db->update('fmstatdate',$settat);
							}
							}
							/** END **/

			}				
				
			}
			
			
			/** End **/
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Updated! Record moved in next stage.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		
		
			}else{
				
				/** Check if its the last step **/
				
				if($isfinalstep!=0)
				{
					
						/** Check if Merge is available **/
				  $merger=$this->db->select('id,mergewith,flowid')->from('fmsmerge')->where('productionflow',$productionflowid)->get();
					if($merger->num_rows()>0)
					{
						foreach($merger->result() as $mergerdata);
						$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$mergerdata->flowid)->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
						$orderstmergedata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$mergerdata->flowid,'userstatus'=>'0','remarks'=>'','addedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('order_stage',$orderstmergedata); 
				        $odm=$this->db->insert_id();
						/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			}else
			{
				foreach($checkodd->result() as $prevorderstage);
				$odm=$prevorderstage->id;
				/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
				
			}
					
						if($odm<>0)
						{
							$mergehistdata=array('orderstageid'=>$odm,'oldproductionflow'=>$productionflowid,'jobcardid'=>$jobcardid,'newproductionflow'=>$mergerdata->mergewith,'oldfmsflow'=>$flowstage,'newfmsflow'=>$mergerdata->flowid,'addedOn'=>date('Y-m-d H:i:s'));
							$this->db->insert('fmsmergehistory',$mergehistdata);
							
							$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Merged.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
							
							}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to merge flow.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
					}else { 
					$newdata=array('complete'=>'1');
					$this->db->where('order_id',$orderid);
					$this->db->where('id',$jobcardid);
					$this->db->update('order_instruments',$newdata);


					/** IF THIS IS SALESFORCE ORDER **/
					$this->checkifthisissforderandlastonetogetcompleted($orderid);

					/** END **/
					
					if($selforder=='1')
					{
					$this->fmsmodel->addstock($jobcardid);
					}

$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);					
					}
					/** Merge **/
					$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}else
				{
							
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}
			}
	}
	
	
	function markdoneold28jan2020()
	{
		$orderstage=$this->uri->segment(3);
		$flowstage=$this->uri->segment(4);
		$orderid=$this->uri->segment(5);
		$jobcardid=$this->uri->segment(6);
		$productionflowid=$this->uri->segment(7);
		$user_id =$this->session->userdata['logged_in']['user_id'];
		$newstage=[];
		$isfinalstep=0;
		
		/** Move to next stage **/
			$currentstage=$flowstage;
			$getset=$this->db->select('setorder,finalstep')->from('fms_flow')->where('production_flow_id',$productionflowid)->where('flow_id',$currentstage)->get();
			if($getset->num_rows()>0)
			{
				foreach($getset->result() as $ggetset);
				$currentsetorder=$ggetset->setorder;
				$isfinalstep=$ggetset->finalstep;
				
				/** Get Next Order **/
				$getneworder=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id',$productionflowid)->where('setorder>',$currentsetorder)->limit(1)->order_by('setorder','ASC')->get();
				if($getneworder->num_rows()>0)
			{
				foreach($getneworder->result() as $getneworder1);
				/** Check Move **/	
				$restymove=$this->db->select('moveto')->from('fms_flow')->where('flow_id',$flowstage)->get();
				foreach($restymove->result() as $restymove1)
				{
					//echo $restymove1->moveto;exit;
				if(($restymove1->moveto<>0) && ($restymove1->moveto<>''))
				{
				$moveflow=$restymove1->moveto;
				$movearr=explode(',',$moveflow);
				foreach($movearr as $movea)
				{
				$newstage[]=$movea;
				}
				
				} else
				{
			
					$newstage[]=$getneworder1->flow_id;
				} 
				}			
				
				
				
				//$newstage=$getneworder1->flow_id;
				
			}else{
				
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			}
				/** End **/
				
			}else{
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			}
			
			if(count($newstage)>0)
			{
				
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);
			$effrow=$this->db->affected_rows();	
			if($effrow>0)
			{
			for($u=0;$u<count($newstage);$u++)
			{
				
			$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$newstage[$u])->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
			if($newstage[$u]<>0)
			{
			$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage[$u],'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
			}
			}
				
			}
			
			
			/** End **/
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Updated! Record moved in next stage.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}else{
			
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Please try again later.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
		
			}else{
				
				/** Check if its the last step **/
				if($isfinalstep!=0)
				{
					$newdata=array('complete'=>'1');
					$this->db->where('order_id',$orderid);
					$this->db->where('id',$jobcardid);
					$this->db->update('order_instruments',$newdata);
					/** Check if Merge is available **/
				  $merger=$this->db->select('id,mergewith,flowid')->from('fmsmerge')->where('productionflow',$productionflowid)->get();
					if($merger->num_rows()>0)
					{
						foreach($merger->result() as $mergerdata);
						$orderstmergedata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$mergerdata->flowid,'userstatus'=>'0','remarks'=>'','addedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('order_stage',$orderstmergedata);
						$odm=$this->db->insert_id();
						if($odm<>0)
						{
							$mergehistdata=array('orderstageid'=>$odm,'oldproductionflow'=>$productionflowid,'newproductionflow'=>$merger->mergewith,'oldfmsflow'=>$flowstage,'newfmsflow'=>$mergerdata->flowid,'addedOn'=>date('Y-m-d H:i:s'));
							$this->db->insert('fmsmergehistory',$mergehistdata);
							
							$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Merged.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
							
							}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to merge flow.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
					}
					/** Merge **/
					$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}else
				{
							
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}
			}
	}



			function machining_process3()
	{
	$scheduler_data=array();
	
	$flowstage=$this->uri->segment(3);
	$productionflowid=$this->uri->segment(4);
	/*GET CURRENT PROCESS SORT ORDER*/
		$QRY = $this->db->select('total_days,tat,set_time, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();
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
	$currentprocessorder = $flowstage;
	$setorder= $fmsinformation->setorder;
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$fmsprocess=$productionflowid;

	/** END **/
	if($fmsprocess<>0)
	{
	/** Get Process Name **/
	$process=$this->db->select('d.file_number,f.ordertype,f.fileno,a.jumpfabricationappl,a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,d.alias,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow,d.fabrication,f.planstartsfrom')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('a.who_wedo',$user_id)->order_by('b.userstatus','ASC')->order_by('b.addedOn','DESC')->order_by('e.selforder','ASC')->get();
	/** End **/
	if($process->num_rows()>0)
	{
	// echo "<pre>"; print_r($process->result());exit;
	$i=1;	
	foreach($process->result() as $proc)
	{

		$ordertype = $proc->ordertype;
			if($ordertype=='1'){
				
				$fileno=$proc->file_number;
			}else{
			
				$fileno=$proc->fileno;
			}


	$podetails='';
	if($proc->userstatus=='0')
	{
	$prestui="<span class='btn btn-danger btn-xs'>Update Pending</span>";
	//$action='<a href="javascript:;" onclick="markstagedone('."'".$proc->orderstageid."'".','."'".$proc->flowstage."'".','."'".$proc->originalorderid."'".','."'".$proc->jobcardid."'".')"><span class="btn btn-xs btn-warning">Mark as done</span></a>';
	$html='<button class="btn btn-xs btn-warning waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$proc->orderstageid.'">Mark as Done</button>';

	$html.= ' <div id="con-close-modal'.$proc->orderstageid.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<form action="'.page_url.'Orderstage/kittingbop/'.$proc->orderstageid.'/'.$proc->flowstage.'/'.$proc->originalorderid.'/'.$fmsprocess.'" method="post" id="loginForm" onSubmit="return checkvalidation('.$proc->orderstageid.'); checkifqtyisfilled();">

	<div id="pageloader">
	<img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
	</div>
	<div class="modal-dialog modal-lg">
	<div class="modal-content">
	<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	<h4 class="modal-title">FULL KITTING FOR <span style="color:red;">'.strtoupper($proc->instruments_name).'</span></h4>
	</div>
	<div class="modal-body">

	<div class="row">
	<div class="col-md-2">
	<div class="form-group">
	<label for="field-1" class="control-label">JOBCARD NUMBER</label><br/>
	<span id="error_purpose" style="color:red;"></span>
	<input type="text" class="form-control" id="jobcard_no'.$proc->orderstageid.'" style="text-transform: uppercase;width:100%;" name="jobcard_no'.$proc->orderstageid.'" placeholder="" value="'.$proc->job_card_no.'" readonly required>
<input type="hidden" name="jobcardid'.$proc->orderstageid.'" value="'.$proc->jobcardid.'">
	</div>
	</div>

	<div class="col-md-4">
	<div class="form-group">
	<label for="field-2" class="control-label">MATERIAL IS IN STOCK <span id="error_status" style="color:red;">*</span></label><br>

	<select class="form-control" id="stock'.$proc->orderstageid.'" name="stock'.$proc->orderstageid.'" required onchange="getstockdetail('.$proc->orderstageid.'); getmaterialsforpr('."'".$proc->orderstageid."'".','."'".$proc->item_id."'".');">
	<option value="">SELECT</option>
	<option value="1">YES</option>
	<option value="0">NO</option>
	</select>
	</div>
	</div>
	</div>';

	$prnos=$this->db->select('id,purno')->from('purchase_request')->group_by('purno')->order_by('purno','DESC')->limit(1)->get();
	$num=$prnos->num_rows();
	if($num==0)
	{
		$num1=1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPR'.$num_padded;
	}else{
	     foreach($prnos->result() as $prnoss);
	    $numpart =$prnoss->purno;
	    	$num1=$numpart+1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPR'.$num_padded;
		
	}
	
	$html.='<div class="row" id="prreq'.$proc->orderstageid.'" style="margin-top:20px;display:none">
	<div class="col-md-3">
	<div class="form-group">
	<label for="field-1" class="control-label">PR NUMBER</label><span id="error_purpose" style="color:red;">*</span><br/>

	<input type="text" class="form-control" id="prno'.$proc->orderstageid.'" style="text-transform: uppercase;" name="prno'.$proc->orderstageid.'" placeholder="" value="'.$code.'" readonly>
	</div>
	</div>
	
	<div class="col-md-3">
	<div class="form-group">
	<label for="field-1" class="control-label">&nbsp;</label><br/>
	<a href="javascript:;" onclick="generatehelpticket('.$proc->jobcardid.');"><span class="btn btn-success btn-xs">Raise Incorrect BOM Helpticket</span></a>
	</div>
	</div>

	<div class="col-md-6" style="display:none">
	<div class="form-group">
	<label for="field-1" class="control-label">MATERIAL NAME</label><span id="error_purpose" style="color:red;">*</span><br/>

	<textarea class="form-control" id="mtname'.$proc->orderstageid.'" style="text-transform: uppercase;" name="mtname'.$proc->orderstageid.'" placeholder="" value="" col="20"></textarea>

	</div>
	</div>';
	
	
	$html.='<div class="col-md-12">
	
	<div class="text-center" style="font-size:15px">Choose Item to Generate PR</div> 
<div class="pull-right"><input type="text" onkeyup="searchtable('.$proc->orderstageid.');" name="search" id="searchableee" placeholder="Search Item" class="form-control" style="height:30px"></div>
<div style="height:300px;overflow-y:auto;width:100%;"> 	
 <table class="table table-bordered" id="materialtable'.$proc->orderstageid.'">
    <thead>
      <tr>
		<th style="width:2%">Select</th>
        <th style="width:8%">Item Name</th>
        <th style="width:8%">Fincode</th>
		<th style="width:8%">Specification</th>
        <th style="width:8%">Quantity</th>
		<th style="width:5%">Current Stock</th>
        <th style="width:10%">Picture</th>
		<th style="width:10%">Reason</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
</div>	
	
	
	</div>
    </div>


	</div>
	<div class="modal-footer">
	<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
	<input type="submit" id="save'.$proc->orderstageid.'" class="btn btn-info" value="Submit">
	</div>
	</div>
	</div>
	</form>
	</div>';


	$actualdate="";
	$actualtime="";
	$completedate="";
	$addedtimefinal="";
	}else{
	$compdate=date('d-M-Y H:i:s',strtotime($proc->stagecompletedate));

	date_default_timezone_set("Asia/Kolkata");
	$actualdate = date('d-M-Y', strtotime($proc->stagecompletedate));
	$time = date('H:i:s', strtotime($proc->stagecompletedate));
	$actualtime = "<br>". date('g:i A', strtotime($time)); 


	$prestui="<span class='btn btn-success btn-xs'>Remarks Updated</span>";
	$action="Task completed on ".$compdate;
	$html="Action has been taken by You.";

	date_default_timezone_set("Asia/Kolkata");
	$completedate = date('Y-m-d', strtotime($proc->stagecompletedate));
	$completetime = date('H:i:s', strtotime($proc->stagecompletedate));
	$addedtimefinal = "<br>". date('g:i A', strtotime($completetime));
	}



	$material="";
	$prno="";
	$stockinfo="";
	
	$query = $this->db->select('jobcardid, stock, prno, material')->from('kitting_bop_details')->where('jobcardid',$proc->jobcardid)->get();
	if($query->num_rows()>0){
	$res = $query->result();
	foreach($res as $row);
	$stock = $row->stock; 
	if($stock=='0'){
	$stockinfo = "OUT OF STOCK";
	$prno = "<a href='".page_url."Store/pr/".$row->prno."' target='_blank'>".$row->prno."</a>";
	$material = $row->material;
	}else{
	$stockinfo = "IN STOCK";
	$prno="";	
	$material="";
	}
	}


	/** Get User Department **/
	$udep=$this->db->select('b.department')->from('system_users a')->join('departments b','a.department_id=b.department_id')->where('a.user_id',$_SESSION['logged_in']['user_id'])->get(); 
	if($udep->num_rows()>0)
	{
	foreach($udep->result() as $depp);
	$depart=$depp->department;

	}else
	{
	$depart='';
	}

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
					
					/** Check if instrument requires fabrication **/
					/**$ryt=$this->db->select('b.fabrication')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->where('a.id',$proc->jobcardid)->get();
					if($ryt->num_rows()>0)
					{
						foreach($ryt->result() as $rtyeyte);
						$fabreq=$rtyeyte->fabrication;
						
					}else{ $fabreq=0 ;}
					
					if($fabreq==0)
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
					}else{ $markapp=1;  }
					
				}else{
					
					$markapp=1;
				}
				/** End **/
	/** End **/

		/** Check for merge fms intersection **/
				if($markapp==1)
				{
					
				$markapp=$this->fmsmodel->checkformergerpoint($flowstage,$proc->jobcardid,$proc->originalorderid,$proc->fabrication,$proc->jumpfabricationappl);
				}
				

				
				/** End **/

	if($markapp==1)
	{	
	$timestamp=$this->getprevioustimestamp($tatstage,$proc->plannedOn,$proc->jobcardid,$flowstage,$proc->orderstageid);

	$tattime = DateTime::createFromFormat('d-M-Y g:i A', $timestamp);
		
	$finaltattime=$tattime->format('g:i A');
	$tatdate=$this->getupcomingtatdatenew($tatstage,$proc->plannedOn,$proc->jobcardid,$timestamp,$totaldaysslave,$proc->orderstageid,$day);

	if($proc->userstatus=='0')
	{
	$completedon="";
	}else
	{
	$completedon=date('d-M-Y g:i A',strtotime($proc->stagecompletedate));
	}

if($proc->alias<>0)
{
$machinealias="(".$this->fmsmodel->getmachinealias($proc->alias);
}else
{
$machinealias='';
}



$podetails=$this->storemodel->checkispoismade($proc->jobcardid);



    $odtype=$this->getordertype($proc->originalorderid);
	$scheduler_data[] = array('sr_no'=>$i,
	'orderdate'=>$timestamp,
	'odtype'=>$odtype,
	'mname'=>strtoupper($proc->instruments_name)."<br/>".$machinealias,
	'jobcard'=>strtoupper($proc->job_card_no),
	'fileno'=>$fileno,
	'factory'=>strtoupper($depart),
	'tat'=>$tatdate,
	'actual'=>$completedon,
	'totdays'=>'',
	'stockinfo'=>$stockinfo,
	'prno'=>$prno,
	'pono'=>$podetails,
	'material'=>$material,
	'status'=>$prestui,
	'action'=>$html);

	$i++;
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
	} 

			function machining_process3Oldbeforestore()
	{
	$scheduler_data=array();
	$flowstage=$this->uri->segment(3);
	$productionflowid=$this->uri->segment(4);
	/*GET CURRENT PROCESS SORT ORDER*/
		$QRY = $this->db->select('total_days,tat,set_time, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();
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
	$currentprocessorder = $flowstage;
	$setorder= $fmsinformation->setorder;
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$fmsprocess=$productionflowid;

	/** END **/
	if($fmsprocess<>0)
	{
	/** Get Process Name **/
	$process=$this->db->select('a.jumpfabricationappl,a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow,d.fabrication,f.planstartsfrom')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('a.who_wedo',$user_id)->order_by('b.userstatus','ASC')->order_by('b.addedOn','DESC')->get();
	/** End **/
	if($process->num_rows()>0)
	{
	// echo "<pre>"; print_r($process->result());exit;
	$i=1;	
	foreach($process->result() as $proc)
	{
	if($proc->userstatus=='0')
	{
	$prestui="<span class='btn btn-danger btn-xs'>Update Pending</span>";
	//$action='<a href="javascript:;" onclick="markstagedone('."'".$proc->orderstageid."'".','."'".$proc->flowstage."'".','."'".$proc->originalorderid."'".','."'".$proc->jobcardid."'".')"><span class="btn btn-xs btn-warning">Mark as done</span></a>';
	$html='<button class="btn btn-xs btn-warning waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$proc->orderstageid.'">Mark as Done</button>';

/**	$html.= ' <div id="con-close-modal'.$proc->orderstageid.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<form action="'.page_url.'Orderstage/kittingbop/'.$proc->orderstageid.'/'.$proc->flowstage.'/'.$proc->originalorderid.'/'.$fmsprocess.'" method="post" id="loginForm">

	<div id="pageloader">
	<img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
	</div>
	<div class="modal-dialog modal-lg">
	<div class="modal-content">
	<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	<h4 class="modal-title">FULL KITTING BOP</h4>
	</div>
	<div class="modal-body">

	<div class="row">
	<div class="col-md-6">
	<div class="form-group">
	<label for="field-1" class="control-label">JOBCARD NUMBER</label><br/>
	<span id="error_purpose" style="color:red;"></span>
	<input type="text" class="form-control" id="jobcard_no'.$proc->orderstageid.'" style="text-transform: uppercase;" name="jobcard_no'.$proc->orderstageid.'" placeholder="" value="'.$proc->job_card_no.'" readonly required>
	<input type="hidden" name="jobcardid'.$proc->orderstageid.'" value="'.$proc->jobcardid.'">
	</div>
	</div>

	<div class="col-md-6">
	<div class="form-group">
	<label for="field-2" class="control-label">MATERIAL IS IN STOCK <span id="error_status" style="color:red;">*</span></label><br>

	<select class="form-control" id="stock'.$proc->orderstageid.'" name="stock'.$proc->orderstageid.'" required onchange="getstockdetail('.$proc->orderstageid.');">
	<option value="">SELECT</option>
	<option value="1">YES</option>
	<option value="0">NO</option>
	</select>
	</div>
	</div>
	</div>

	<div class="row" id="prreq'.$proc->orderstageid.'" style="margin-top:20px;display:none">
	<div class="col-md-6">
	<div class="form-group">
	<label for="field-1" class="control-label">PR NUMBER</label><span id="error_purpose" style="color:red;">*</span><br/>

	<input type="text" class="form-control" id="prno'.$proc->orderstageid.'" style="text-transform: uppercase;" name="prno'.$proc->orderstageid.'" placeholder="" value="">
	</div>
	</div>

	<div class="col-md-6">
	<div class="form-group">
	<label for="field-1" class="control-label">MATERIAL NAME</label><span id="error_purpose" style="color:red;">*</span><br/>

	<textarea class="form-control" id="mtname'.$proc->orderstageid.'" style="text-transform: uppercase;" name="mtname'.$proc->orderstageid.'" placeholder="" value="" col="20"></textarea>

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
	</div>';**/
	
	
	$html.= ' <div id="con-close-modal'.$proc->orderstageid.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<form action="'.page_url.'Orderstage/kittingbop/'.$proc->orderstageid.'/'.$proc->flowstage.'/'.$proc->originalorderid.'/'.$fmsprocess.'" method="post" id="loginForm" onSubmit="return checkvalidation('.$proc->orderstageid.'); checkifqtyisfilled();">

	<div id="pageloader">
	<img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
	</div>
	<div class="modal-dialog modal-lg">
	<div class="modal-content">
	<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	<h4 class="modal-title">FULL KITTING FOR <span style="color:red;">'.strtoupper($proc->instruments_name).'</span></h4>
	</div>
	<div class="modal-body">

	<div class="row">
	<div class="col-md-2">
	<div class="form-group">
	<label for="field-1" class="control-label">JOBCARD NUMBER</label><br/>
	<span id="error_purpose" style="color:red;"></span>
	<input type="text" class="form-control" id="jobcard_no'.$proc->orderstageid.'" style="text-transform: uppercase;width:100%;" name="jobcard_no'.$proc->orderstageid.'" placeholder="" value="'.$proc->job_card_no.'" readonly required>
<input type="hidden" name="jobcardid'.$proc->orderstageid.'" value="'.$proc->jobcardid.'">
	</div>
	</div>

	<div class="col-md-4">
	<div class="form-group">
	<label for="field-2" class="control-label">MATERIAL IS IN STOCK <span id="error_status" style="color:red;">*</span></label><br>

	<select class="form-control" id="stock'.$proc->orderstageid.'" name="stock'.$proc->orderstageid.'" required onchange="getstockdetail('.$proc->orderstageid.'); getmaterialsforpr('."'".$proc->orderstageid."'".','."'".$proc->item_id."'".');">
	<option value="">SELECT</option>
	<option value="1">YES</option>
	<option value="0">NO</option>
	</select>
	</div>
	</div>
	</div>';

	$prnos=$this->db->select('id,purno')->from('purchase_request')->group_by('purno')->order_by('purno','DESC')->limit(1)->get();
	$num=$prnos->num_rows();
	if($num==0)
	{
		$num1=1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPR'.$num_padded;
	}else{
	     foreach($prnos->result() as $prnoss);
	    $numpart =$prnoss->purno;
	    	$num1=$numpart+1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESPR'.$num_padded;
		
	}
	
	$html.='<div class="row" id="prreq'.$proc->orderstageid.'" style="margin-top:20px;display:none">
	<div class="col-md-3">
	<div class="form-group">
	<label for="field-1" class="control-label">PR NUMBER</label><span id="error_purpose" style="color:red;">*</span><br/>

	<input type="text" class="form-control" id="prno'.$proc->orderstageid.'" style="text-transform: uppercase;" name="prno'.$proc->orderstageid.'" placeholder="" value="'.$code.'" readonly>
	</div>
	</div>

	<div class="col-md-6" style="display:none">
	<div class="form-group">
	<label for="field-1" class="control-label">MATERIAL NAME</label><span id="error_purpose" style="color:red;">*</span><br/>

	<textarea class="form-control" id="mtname'.$proc->orderstageid.'" style="text-transform: uppercase;" name="mtname'.$proc->orderstageid.'" placeholder="" value="" col="20"></textarea>

	</div>
	</div>';
	
	
	$html.='<div class="col-md-12">
	
	<div class="text-center" style="font-size:15px">Choose Item to Generate PR</div> 
<div class="pull-right"><input type="text" onkeyup="searchtable('.$proc->orderstageid.');" name="search" id="searchableee" placeholder="Search Item" class="form-control" style="height:30px"></div>
<div style="height:300px;overflow-y:auto;width:100%;"> 	
 <table class="table table-bordered" id="materialtable'.$proc->orderstageid.'">
    <thead>
      <tr>
		<th style="width:2%">Select</th>
        <th style="width:8%">Item Name</th>
        <th style="width:8%">Fincode</th>
		<th style="width:8%">Specification</th>
        <th style="width:8%">Quantity</th>
		<th style="width:5%">Current Stock</th>
        <th style="width:10%">Picture</th>
		<th style="width:10%">Reason</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
</div>	
	
	
	</div>
    </div>


	</div>
	<div class="modal-footer">
	<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
	<input type="submit" id="save'.$proc->orderstageid.'" class="btn btn-info" value="Submit">
	</div>
	</div>
	</div>
	</form>
	</div>';

	


	$actualdate="";
	$actualtime="";
	$completedate="";
	$addedtimefinal="";
	}else{
	$compdate=date('d-M-Y H:i:s',strtotime($proc->stagecompletedate));

	date_default_timezone_set("Asia/Kolkata");
	$actualdate = date('d-M-Y', strtotime($proc->stagecompletedate));
	$time = date('H:i:s', strtotime($proc->stagecompletedate));
	$actualtime = "<br>". date('g:i A', strtotime($time)); 


	$prestui="<span class='btn btn-success btn-xs'>Remarks Updated</span>";
	$action="Task completed on ".$compdate;
	$html="Action has been taken by You.";

	date_default_timezone_set("Asia/Kolkata");
	$completedate = date('Y-m-d', strtotime($proc->stagecompletedate));
	$completetime = date('H:i:s', strtotime($proc->stagecompletedate));
	$addedtimefinal = "<br>". date('g:i A', strtotime($completetime));
	}



	$material="";
	$prno="";
	$stockinfo="";
	$query = $this->db->select('jobcardid, stock, prno, material')->from('kitting_bop_details')->where('jobcardid',$proc->jobcardid)->get();
	$res = $query->result();
	if($query->num_rows()>0){
	foreach($res as $row){
	$stock = $row->stock; 
	if($stock=='0'){
	$stockinfo = "OUT OF STOCK";
	$prno = $row->prno;
	$material = $row->material;
	}else{
	$stockinfo = "IN STOCK";
	$prno="";	
	$material="";
	}
	}
	}


	/** Get User Department **/
	$udep=$this->db->select('b.department')->from('system_users a')->join('departments b','a.department_id=b.department_id')->where('a.user_id',$_SESSION['logged_in']['user_id'])->get(); 
	if($udep->num_rows()>0)
	{
	foreach($udep->result() as $depp);
	$depart=$depp->department;

	}else
	{
	$depart='';
	}

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
					
					/** End **/
					
					/** Check if instrument requires fabrication **/
					/**$ryt=$this->db->select('b.fabrication')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->where('a.id',$proc->jobcardid)->get();
					if($ryt->num_rows()>0)
					{
						foreach($ryt->result() as $rtyeyte);
						$fabreq=$rtyeyte->fabrication;
						
					}else{ $fabreq=0 ;}
					
					if($fabreq==0)
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

					$this->db->select('a.dependentflowid')->from('flowdependency a')->join('order_stage b','a.dependentflowid=b.flowstage')->where('a.flowid',$flowstage);

					//$availableflowstages = "'" . implode ( "', '", $availableflowstage ) . "'";

					//$this->db->where_in('a.dependentflowid',$availableflowstages,false);

					$this->db->where('b.jobcardid',$proc->jobcardid)->where('b.orderid',$proc->originalorderid);
		
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
					}else{ $markapp=1;  }
					
				}else{
					
					$markapp=1;
				}
				/** End **/
	/** End **/

		/** Check for merge fms intersection **/
				if($markapp==1)
				{
					
				$markapp=$this->fmsmodel->checkformergerpoint($flowstage,$proc->jobcardid,$proc->originalorderid,$proc->fabrication,$proc->jumpfabricationappl);
				}
				

				
				/** End **/

	if($markapp==1)
	{	
	$timestamp=$this->getprevioustimestamp($tatstage,$proc->plannedOn,$proc->jobcardid,$flowstage,$proc->orderstageid);

	$tattime = DateTime::createFromFormat('d-M-Y g:i A', $timestamp);
		
	$finaltattime=$tattime->format('g:i A');
	$tatdate=$this->getupcomingtatdatenew($tatstage,$proc->plannedOn,$proc->jobcardid,$timestamp,$totaldaysslave,$proc->orderstageid,$day);

	if($proc->userstatus=='0')
	{
	$completedon="";
	}else
	{
	$completedon=date('d-M-Y g:i A',strtotime($proc->stagecompletedate));
	}

	$scheduler_data[] = array('sr_no'=>$i,
	'orderdate'=>$timestamp,
	'mname'=>strtoupper($proc->instruments_name),
	'jobcard'=>strtoupper($proc->job_card_no),
	'factory'=>strtoupper($depart),
	'tat'=>$tatdate,
	'actual'=>$completedon,
	'totdays'=>'',
	'stockinfo'=>$stockinfo,
	'prno'=>$prno,
	'material'=>$material,
	'status'=>$prestui,
	'action'=>$html);

	$i++;
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
	} 

			
		function machining_process3oldtemp()
	{
	$scheduler_data=array();
	$flowstage=$this->uri->segment(3);
	$productionflowid=$this->uri->segment(4);
			/*GET CURRENT PROCESS SORT ORDER*/
			$QRY = $this->db->select('total_days, tat, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();
			$res = $QRY->result();
			foreach($res as $fmsinformation);
			$totaldaysslave = $fmsinformation->total_days;
			$tatstage = $fmsinformation->tat;
			$currentprocessorder = $flowstage;
			$setorder= $fmsinformation->setorder;
	/*GET CURRENT PROCESS SORT ORDER*/
	$user_id =$this->session->userdata['logged_in']['user_id'];
	/*$isfms=$this->db->select('fms_process')->from('system_users')->where('user_id',$user_id)->get();
	if($isfms->num_rows()>0)
	{
	foreach($isfms->result() as $isfmsa);
	$fmsprocess=$isfmsa->fms_process;
	}else{ $fmsprocess=0; }*/

	$fmsprocess=$productionflowid;
	//echo $fmsprocess;exit;
	/** END **/
	if($fmsprocess<>0)
	{
	/** Get Process Name **/
	$process=$this->db->select('a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('a.who_wedo',$user_id)->order_by('b.userstatus','ASC')->order_by('b.addedOn','DESC')->get();
	/** End **/
	if($process->num_rows()>0)
	{
	// echo "<pre>"; print_r($process->result());exit;
	$i=1;
	foreach($process->result() as $proc)
	{
	if($proc->userstatus=='0')
	{
	$prestui="<span class='btn btn-danger btn-xs'>Update Pending</span>";
	//$action='<a href="javascript:;" onclick="markstagedone('."'".$proc->orderstageid."'".','."'".$proc->flowstage."'".','."'".$proc->originalorderid."'".','."'".$proc->jobcardid."'".')"><span class="btn btn-xs btn-warning">Mark as done</span></a>';
	$html='<button class="btn btn-xs btn-warning waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$proc->orderstageid.'">Mark as Done</button>';

	$html.= ' <div id="con-close-modal'.$proc->orderstageid.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<form action="'.page_url.'Orderstage/kittingbop/'.$proc->orderstageid.'/'.$proc->flowstage.'/'.$proc->originalorderid.'/'.$fmsprocess.'" method="post" id="loginForm">
		
	  <div id="pageloader">
	   <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
	</div>
									<div class="modal-dialog modal-lg">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
												<h4 class="modal-title">FULL KITTING BOP</h4>
											</div>
											<div class="modal-body">
										
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label for="field-1" class="control-label">JOBCARD NUMBER</label><br/>
	<span id="error_purpose" style="color:red;"></span>
															<input type="text" class="form-control" id="jobcard_no'.$proc->orderstageid.'" style="text-transform: uppercase;" name="jobcard_no'.$proc->orderstageid.'" placeholder="" value="'.$proc->job_card_no.'" readonly required>
	<input type="hidden" name="jobcardid'.$proc->orderstageid.'" value="'.$proc->jobcardid.'">
														</div>
													</div>

													<div class="col-md-6">
	<div class="form-group">
	<label for="field-2" class="control-label">MATERIAL IS IN STOCK <span id="error_status" style="color:red;">*</span></label><br>

	<select class="form-control" id="stock'.$proc->orderstageid.'" name="stock'.$proc->orderstageid.'" required onchange="getstockdetail('.$proc->orderstageid.');">
	<option value="">SELECT</option>
	<option value="1">YES</option>
	<option value="0">NO</option>
	</select>
	</div>
	</div>
												</div>
												
												<div class="row" id="prreq'.$proc->orderstageid.'" style="margin-top:20px;display:none">
												<div class="col-md-6">
														<div class="form-group">
															<label for="field-1" class="control-label">PR NUMBER</label><span id="error_purpose" style="color:red;">*</span><br/>

															<input type="text" class="form-control" id="prno'.$proc->orderstageid.'" style="text-transform: uppercase;" name="prno'.$proc->orderstageid.'" placeholder="" value="">
														</div>
													</div>
													
													<div class="col-md-6">
														<div class="form-group">
															<label for="field-1" class="control-label">MATERIAL NAME</label><span id="error_purpose" style="color:red;">*</span><br/>

															<textarea class="form-control" id="mtname'.$proc->orderstageid.'" style="text-transform: uppercase;" name="mtname'.$proc->orderstageid.'" placeholder="" value="" col="20"></textarea>

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


	$actualdate="";
	$actualtime="";
		$completedate="";
	$addedtimefinal="";
	}else{
	$compdate=date('d-M-Y H:i:s',strtotime($proc->stagecompletedate));

	date_default_timezone_set("Asia/Kolkata");
				$actualdate = date('d-M-Y', strtotime($proc->stagecompletedate));
				$time = date('H:i:s', strtotime($proc->stagecompletedate));
				$actualtime = "<br>". date('g:i A', strtotime($time)); 
				
				
	$prestui="<span class='btn btn-success btn-xs'>Remarks Updated</span>";
	$action="Task completed on ".$compdate;
	$html="Action has been taken by You.";
		
		date_default_timezone_set("Asia/Kolkata");
	$completedate = date('Y-m-d', strtotime($proc->stagecompletedate));
	$completetime = date('H:i:s', strtotime($proc->stagecompletedate));
	$addedtimefinal = "<br>". date('g:i A', strtotime($completetime));
	}
	date_default_timezone_set("Asia/Kolkata");
	$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
	$addedtime = "<br>". date('g:i A', strtotime($time));

	$time1=date('H:i:s', strtotime($proc->plannedOn));
	$addedtime1 = "<br>". date('g:i A', strtotime($time1));
	$planneddate1=date('Y-m-d',strtotime($proc->plannedOn));

	$date1 = new DateTime($addeddate1);
	$date2 = new DateTime($planneddate1);
	$interval = $date1->diff($date2);

	/*GET DATE OF PRIVIOUS STEPS*/

						$previousorder = $tatstage;
						
							if($previousorder<>0){
						$abc = $this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$proc->jobcardid)->order_by('id','DESC')->limit(1)->get();
							if($abc->num_rows()>0){
						foreach($abc->result() as $pastinfo);
						$complitiontime = $pastinfo->addedOn;
							
								$lasttaskcompletedtime = "<br>". date('g:i A', strtotime($complitiontime));

								
							}else{
							$complitiontime = $proc->added_on;
									$lasttaskcompletedtime = "<br>". date('g:i A', strtotime($complitiontime));

							}
						}else{
						$complitiontime = $proc->plannedOn;	
								$lasttaskcompletedtime = "<br>". date('g:i A', strtotime($complitiontime));

						}
						
						/*PREVIOUS COMPLITION DATE*/
						
						$checkpreviousdate = date('Y-m-d', strtotime($complitiontime));
						$previouscomplitiondate = date('d-M-Y', strtotime($complitiontime));
						$previouscomplitiontime = date('H:i:s', strtotime($proc->added_on));
						$previouscomplitionfinaltime = "<br>". date('g:i A', strtotime($previouscomplitiontime));
				
						/*GET DATE OF PRIVIOUS STEPS*/
						$date11 = new DateTime($previouscomplitiondate); 
						$date22 = new DateTime($completedate); 
						$interval1 = $date11->diff($date22); 
						$ttldays = $interval1->d; 
						if($ttldays=='0'){
						$totalday = "1";
						}else{
						$totalday = $ttldays;
				}
				
				/*TAT Date*/
				$lastcompdate = date('Y-m-d', strtotime($checkpreviousdate));
				$TATDATE=date('d-M-Y', strtotime($lastcompdate."+".$totaldaysslave." days"));
				
				/*TAT Date*/
		date_default_timezone_set("Asia/Kolkata");
		if($this->uri->segment(3)=='1'){
			$query = $this->db->select('jobcard_id, plannedOn')->from('order_planning')->where('jobcard_id',$proc->jobcardid)->get();
			if($query->num_rows()>0){
			foreach($query->result() as $plannedinformation){
				$addeddate = date('d-M-Y', strtotime($plannedinformation->plannedOn));
	$time = date('H:i:s', strtotime($plannedinformation->plannedOn));
	$addedtime = "<br>". date('g:i A', strtotime($time));
			}
			}else{
			$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
	$addedtime = "<br>". date('g:i A', strtotime($time));
			}
		

		}else{
			$previousorder = $setorder-1; 
		$QRY = $this->db->select('total_days, tat,setorder, flow_id ')->from('fms_flow')->where('setorder',$previousorder)->get();
		if($QRY->num_rows()>0){
		foreach($QRY->result() as $paststepdate);
			$flowid = $paststepdate->flow_id;
		$QRY1 = $this->db->select('jobcardid, flowstage,addedOn')->from('order_stage')->where('jobcardid',$proc->jobcardid)->where('flowstage',$flowid)->get();	
		if($QRY1->num_rows()>0){
		foreach($QRY1->result() as $timestamp){
		$addeddate1 = date('Y-m-d', strtotime($timestamp->addedOn));
	$time = date('H:i:s', strtotime($timestamp->addedOn));
	$addedtime = "<br>". date('g:i A', strtotime($time));
		}
		}	
		}else{
		$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
		}
		}
		
		$material="";
		$prno="";
		$stockinfo="";
		$query = $this->db->select('jobcardid, stock, prno, material')->from('kitting_bop_details')->where('jobcardid',$proc->jobcardid)->get();
		$res = $query->result();
		if($query->num_rows()>0){
		foreach($res as $row){
		$stock = $row->stock; 
			if($stock=='0'){
			$stockinfo = "OUT OF STOCK";
				$prno = $row->prno;
				$material = $row->material;
			}else{
			$stockinfo = "IN STOCK";
			$prno="";	
				$material="";
			}
		}
		}

			/** Get User Department **/
				$udep=$this->db->select('b.department')->from('system_users a')->join('departments b','a.department_id=b.department_id')->where('a.user_id',$_SESSION['logged_in']['user_id'])->get(); 
				if($udep->num_rows()>0)
				{
					foreach($udep->result() as $depp);
					$depart=$depp->department;
					
				}else
				{
					$depart='';
				}
				
				/** Check for dependency **/
				
				$checkdepend=$this->db->select('dependency')->from('fms_flow')->where('flow_id',$flowstage)->where('dependency','1')->get();
				$depend=$checkdepend->num_rows();
				//echo $depend;exit;
				if($depend==1)
				{
					
					$restt=$this->db->select('dependentflowid')->from('flowdependency')->where('flowid',$flowstage)->get();
					if($restt->num_rows()>0)
					{
						$dependcount=$restt->num_rows();
						$donarr=array();
						$donarr[]=0;
						//echo "<pre>";print_r($restt->result());exit;
						foreach($restt->result() as $resttyu)
						{
							$isdone=$this->db->select('id,userstatus')->from('order_stage')->where('flowstage',$resttyu->dependentflowid)->where('jobcardid',$proc->jobcardid)->where('orderid',$proc->originalorderid)->order_by('id','DESC')->limit(1)->get();
							if($isdone->num_rows()>0)
							{
								foreach($isdone->result() as $isdone11);
								$donarr[]=$isdone11->userstatus;
								
							}else
							{
								$markapp=1;
							}
							
						}
						//echo $dependcount.'<br>'."<pre>"; print_r($donarr);exit;
						if($dependcount==array_sum($donarr))
						{
							$markapp=1;
						}else{
							$markapp=0;
						}
						
					}else{
						$markapp=1;
					}
					
				}else{
					
					$markapp=1;
				}
				/** End **/
		
		/** New Timestamp query **/
				$previousorder=$tatstage;
				if($previousorder==0)
				{
					$previouscomtime=$proc->plannedOn;
					$previouscomdate=date('d-M-Y',strtotime($previouscomtime))."<br>";
					$previouscomtimeonly=date('g:i A',strtotime($previouscomtime));
					
				}else
				{
					//echo $previousorder;exit;
					$oldtime1=$this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$proc->jobcardid)->order_by('id','DESC')->limit(1)->get();
							if($oldtime1->num_rows()>0){
								foreach($oldtime1->result() as $pastinfo);
								$previouscomtime=$pastinfo->addedOn;
								$previouscomdate=date('d-M-Y',strtotime($pastinfo->addedOn))."<br>";
								$previouscomtimeonly=date('g:i A',strtotime($pastinfo->addedOn));
							}else
							{
								$previouscomtime=$proc->plannedOn;
					$previouscomdate=date('d-M-Y',strtotime($previouscomtime))."<br>";
					$previouscomtimeonly=date('g:i A',strtotime($previouscomtime));
							}
				}

		
				
				/** End **/
		
				
	if($markapp==1)
	{	
		
		/** Holiday Check**/
				
					$TATDATEFORHOLIDAYCHECK=date('Y-m-d', strtotime($TATDATE));
					$timestampforcheck=date('Y-m-d',strtotime($previouscomtime));
					$date_from=strtotime($timestampforcheck);
					$date_to=strtotime($TATDATEFORHOLIDAYCHECK);
					$betweendates=array();
					for ($g=$date_from; $g<=$date_to; $g+=86400) {  
						$betweendates[]= date("Y-m-d", $g);  
					} 
				
				//echo "<pre>"; print_r($betweendates);
					if(count($betweendates)>0)
				{
					
					$alldates = "'" . implode ( "', '", $betweendates ) . "'";
					
					$ifholiday=$this->db->select('holiday_id')->from('prestogroup_holidays')->where_in('holiday_date',$alldates,false)->get();
					if($ifholiday->num_rows()>0)
					{
			
						//echo "hi".$proc->job_card_no;
						$TATDATEFORHOLIDAYCHECK=date('Y-m-d', strtotime($TATDATEFORHOLIDAYCHECK."+".$ifholiday->num_rows()." days"));
						//echo $TATDATEFORHOLIDAYCHECK;
						
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
				//$TATDATE= date('d-M-Y', strtotime($nextdate."+1 days"));
				$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();
				if($query->num_rows()>0){
					
					foreach($query->result() as $afterholiday);
					$aftrholidaydate = $afterholiday->holiday_date;
					$TATDATE= date('d-M-Y', strtotime($aftrholidaydate."+1 days"));
					
				}else {
				$TATDATE= date('d-M-Y', strtotime($nextdate));
				}
			}else{
			$TATDATE=date('d-M-Y', strtotime($TATDATEFORHOLIDAYCHECK));
			}
					
				/** End **/
		
	$scheduler_data[] = array('sr_no'=>$i,
	'orderdate'=>$previouscomdate.$previouscomtimeonly,
	'mname'=>strtoupper($proc->instruments_name),
	'jobcard'=>strtoupper($proc->job_card_no),
	'factory'=>strtoupper($depart),
	'planned'=>date('d-M-Y',strtotime($proc->plannedOn)),
	'tat'=>$TATDATE."<br>".$previouscomtimeonly,
	'actual'=>$completedate.$addedtimefinal,
	'totdays'=>$totalday,
	'stockinfo'=>$stockinfo,
	'prno'=>$prno,
	'material'=>$material,
	'status'=>$prestui,
	'action'=>$html);

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

	}
	} 

		



		function kittingbopoldfmstatdate()
	{
	$orderstage=$this->uri->segment(3);
	$flowstage=$this->uri->segment(4);
	$orderid=$this->uri->segment(5);
	$selforder=$this->fmsmodel->getselforder($orderid);
	$productionflowid=$this->uri->segment(6);
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$newstage=[];
	$isfinalstep=0;
	$jobcardid=$this->input->post('jobcardid'.$orderstage);
	$stock=$this->input->post('stock'.$orderstage);
	/** Move to next stage **/
			$currentstage=$flowstage;
			
			$getset=$this->db->select('setorder,finalstep,total_days,set_time')->from('fms_flow')->where('flow_id',$currentstage)->where('production_flow_id',$productionflowid)->get();
			
				foreach($getset->result() as $ggetset);
				$currentsetorder=$ggetset->setorder;	
				$isfinalstep=$ggetset->finalstep;
					$tatday=$ggetset->total_days;
				$tathours=$ggetset->set_time;
				/** Get Next Order **/
				$getneworder=$this->db->select('flow_id')->from('fms_flow')->where('setorder>',$currentsetorder)->where('production_flow_id',$productionflowid)->limit(1)->order_by('setorder','ASC')->get();
				if($getneworder->num_rows()>0)
			{
				foreach($getneworder->result() as $getneworder1);
				$restymove=$this->db->select('moveto')->from('fms_flow')->where('flow_id',$flowstage)->get();
				foreach($restymove->result() as $restymove1)
				{
				if(($restymove1->moveto<>0) && ($restymove1->moveto<>''))
				{
				$moveflow=$restymove1->moveto;
				$movearr=explode(',',$moveflow);
				foreach($movearr as $movea)
				{
				$newstage[]=$movea;
				}
				
				} else
				{
					$newstage[]=$getneworder1->flow_id;
				} 
				}
				
			}


	if($stock=='0')
				{
					$pr=$this->input->post('prno'.$orderstage);
					$mtname=$this->input->post('mtname'.$orderstage);
					
				}else{ $pr=''; $mtname=''; }
			/** Detail Entery **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'stock'=>$stock,'prno'=>$pr,'material'=>$mtname,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->insert('kitting_bop_details',$detailentry);
			$effrow1=$this->db->affected_rows();	
			
			/** End**/
			if($effrow1>0)
			{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);
			$effrow=$this->db->affected_rows();	
			}
			
			
		if(count($newstage)>0)
			{
		
			for($u=0;$u<count($newstage);$u++)
			{
			
	$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$newstage[$u])->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{	
			
		if($newstage[$u]<>0)
			{	$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage[$u],'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
			
			/** Add Tat **/
						$stageid=$this->db->insert_id();
						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$newstage[$u]);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			
			}
			}else
			{
				foreach($checkodd->result() as $checkodd1);
				/** Add Tat **/
						$stageid=$checkodd1->id;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$newstage[$u]);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
				
			}
			
			}
			
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Order Moved to Next Process.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			
		}else{
				
				
				if($isfinalstep!=0)
				{
					
						/** Check if Merge is available **/
				  $merger=$this->db->select('id,mergewith,flowid')->from('fmsmerge')->where('productionflow',$productionflowid)->get();
					if($merger->num_rows()>0)
					{
						foreach($merger->result() as $mergerdata);
						$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$mergerdata->flowid)->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
						$orderstmergedata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$mergerdata->flowid,'userstatus'=>'0','remarks'=>'','addedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('order_stage',$orderstmergedata); 
				        $odm=$this->db->insert_id();
						/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			}else
			{
				foreach($checkodd->result() as $prevorderstage);
				$odm=$prevorderstage->id;
				
				/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
				
				
				
			}
					
						if($odm<>0)
						{
							$mergehistdata=array('orderstageid'=>$odm,'oldproductionflow'=>$productionflowid,'jobcardid'=>$jobcardid,'newproductionflow'=>$mergerdata->mergewith,'oldfmsflow'=>$flowstage,'newfmsflow'=>$mergerdata->flowid,'addedOn'=>date('Y-m-d H:i:s'));
							$this->db->insert('fmsmergehistory',$mergehistdata);
							
							$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Merged.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
							
							}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to merge flow.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
					}else { 
					$newdata=array('complete'=>'1');
					$this->db->where('order_id',$orderid);
					$this->db->where('id',$jobcardid);
					$this->db->update('order_instruments',$newdata);
					
					if($selforder=='1')
					{
					$this->fmsmodel->addstock($jobcardid);
					}

$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);					
					}
					/** Merge **/
					$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}else
				{
				
				/** End **/
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}
			}
				/** End **/
		

			
	}


	function kittingbopbeforepr()
	{
	$orderstage=$this->uri->segment(3);
	$flowstage=$this->uri->segment(4);
	$orderid=$this->uri->segment(5);
	$selforder=$this->fmsmodel->getselforder($orderid);
	$productionflowid=$this->uri->segment(6);
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$newstage=[];
	$isfinalstep=0;
	$jobcardid=$this->input->post('jobcardid'.$orderstage);
	$stock=$this->input->post('stock'.$orderstage);
	/** Move to next stage **/
			$currentstage=$flowstage;
			
			$getset=$this->db->select('setorder,finalstep,total_days,set_time')->from('fms_flow')->where('flow_id',$currentstage)->where('production_flow_id',$productionflowid)->get();
			
				foreach($getset->result() as $ggetset);
				$currentsetorder=$ggetset->setorder;	
				$isfinalstep=$ggetset->finalstep;
					$tatday=$ggetset->total_days;
				$tathours=$ggetset->set_time;
				/** Get Next Order **/
				$getneworder=$this->db->select('flow_id')->from('fms_flow')->where('setorder>',$currentsetorder)->where('production_flow_id',$productionflowid)->limit(1)->order_by('setorder','ASC')->get();
				if($getneworder->num_rows()>0)
			{
				foreach($getneworder->result() as $getneworder1);
				$restymove=$this->db->select('moveto')->from('fms_flow')->where('flow_id',$flowstage)->get();
				foreach($restymove->result() as $restymove1)
				{
				if(($restymove1->moveto<>0) && ($restymove1->moveto<>''))
				{
				$moveflow=$restymove1->moveto;
				$movearr=explode(',',$moveflow);
				foreach($movearr as $movea)
				{
				$newstage[]=$movea;
				}
				
				} else
				{
					$newstage[]=$getneworder1->flow_id;
				} 
				}
				
			}


	if($stock=='0')
				{
					$pr=$this->input->post('prno'.$orderstage);
					$mtname=$this->input->post('mtname'.$orderstage);
					
				}else{ $pr=''; $mtname=''; }
			/** Detail Entery **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'stock'=>$stock,'prno'=>$pr,'material'=>$mtname,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->insert('kitting_bop_details',$detailentry);
			$effrow1=$this->db->affected_rows();	
			
			/** MAKE ENTRY IN PR **/
			if($stock=='0')
			{
				if(count($this->input->post('itemselected'))>0)
				{
				$this->storemodel->generatepr($pr,$jobcardid);
				}

			}				
		
/*** PR END **/

			/** End**/
			if($effrow1>0)
			{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);
			$effrow=$this->db->affected_rows();	
			}
			
			
		if(count($newstage)>0)
			{
		
			for($u=0;$u<count($newstage);$u++)
			{
			
	$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$newstage[$u])->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{	
			
		if($newstage[$u]<>0)
			{	$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage[$u],'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
			
			/** Add Tat **/
						$stageid=$this->db->insert_id();
						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$newstage[$u]);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			
			}
			}else
			{
				foreach($checkodd->result() as $checkodd1);
				/** Add Tat **/
						$stageid=$checkodd1->id;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$newstage[$u]);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
				
			}
			
			}
			
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Order Moved to Next Process.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			
		}else{
				
				
				if($isfinalstep!=0)
				{
					
						/** Check if Merge is available **/
				  $merger=$this->db->select('id,mergewith,flowid')->from('fmsmerge')->where('productionflow',$productionflowid)->get();
					if($merger->num_rows()>0)
					{
						foreach($merger->result() as $mergerdata);
						$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$mergerdata->flowid)->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
						$orderstmergedata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$mergerdata->flowid,'userstatus'=>'0','remarks'=>'','addedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('order_stage',$orderstmergedata); 
				        $odm=$this->db->insert_id();
						/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			}else
			{
				foreach($checkodd->result() as $prevorderstage);
				$odm=$prevorderstage->id;
				
				/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
				
				
				
			}
					
						if($odm<>0)
						{
							$mergehistdata=array('orderstageid'=>$odm,'oldproductionflow'=>$productionflowid,'jobcardid'=>$jobcardid,'newproductionflow'=>$mergerdata->mergewith,'oldfmsflow'=>$flowstage,'newfmsflow'=>$mergerdata->flowid,'addedOn'=>date('Y-m-d H:i:s'));
							$this->db->insert('fmsmergehistory',$mergehistdata);
							
							$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Merged.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
							
							}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to merge flow.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
					}else { 
					$newdata=array('complete'=>'1');
					$this->db->where('order_id',$orderid);
					$this->db->where('id',$jobcardid);
					$this->db->update('order_instruments',$newdata);
					
					if($selforder=='1')
					{
					$this->fmsmodel->addstock($jobcardid);
					}

$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);					
					}
					/** Merge **/
					$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}else
				{
				
				/** End **/
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}
			}
				/** End **/
		

			
	}


	function kittingbop()
	{
	    
	$selitem=array();
	$gprno='';
	$orderstage=$this->uri->segment(3);
	$flowstage=$this->uri->segment(4);
	$orderid=$this->uri->segment(5);
	$selforder=$this->fmsmodel->getselforder($orderid);
	$productionflowid=$this->uri->segment(6);
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$newstage=[];
	$isfinalstep=0;
	$jobcardid=$this->input->post('jobcardid'.$orderstage);
	$stock=$this->input->post('stock'.$orderstage);
	$machid=$this->storemodel->getmachineid($jobcardid);
	/** Move to next stage **/
			$currentstage=$flowstage;
			
			$getset=$this->db->select('setorder,finalstep,total_days,set_time')->from('fms_flow')->where('flow_id',$currentstage)->where('production_flow_id',$productionflowid)->get();
			
				foreach($getset->result() as $ggetset);
				$currentsetorder=$ggetset->setorder;	
				$isfinalstep=$ggetset->finalstep;
					$tatday=$ggetset->total_days;
				$tathours=$ggetset->set_time;
				/** Get Next Order **/
				$getneworder=$this->db->select('flow_id')->from('fms_flow')->where('setorder>',$currentsetorder)->where('production_flow_id',$productionflowid)->limit(1)->order_by('setorder','ASC')->get();
				if($getneworder->num_rows()>0)
			{
				foreach($getneworder->result() as $getneworder1);
				$restymove=$this->db->select('moveto')->from('fms_flow')->where('flow_id',$flowstage)->get();
				foreach($restymove->result() as $restymove1)
				{
				if(($restymove1->moveto<>0) && ($restymove1->moveto<>''))
				{
				$moveflow=$restymove1->moveto;
				$movearr=explode(',',$moveflow);
				foreach($movearr as $movea)
				{
				$newstage[]=$movea;
				}
				
				} else
				{
					$newstage[]=$getneworder1->flow_id;
				} 
				}
				
			}


	if($stock=='0')
				{
					$pr=$this->input->post('prno'.$orderstage);
					$mtname=$this->input->post('mtname'.$orderstage);
					
				}else{ $pr=''; $mtname=''; }
			/** Detail Entery **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'stock'=>$stock,'prno'=>$pr,'material'=>$mtname,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->insert('kitting_bop_details',$detailentry);
			$effrow1=$this->db->affected_rows();
			
			/** MAKE ENTRY IN PR **/
			if(count($this->input->post('itemselected'))>0)
				{
					$selitem=$this->input->post('itemselected');
				$gprno=$this->storemodel->generatepr($pr,$jobcardid);
				}
			
			/** BLOCK ALL ITEMS IN JOBCARD **/
			$this->storemodel->blockallitems($machid,$jobcardid,$selitem,$gprno);
			/** END **/
		
		
			
/*** PR END **/
			
			/** End**/
			if($effrow1>0)
			{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);
			$effrow=$this->db->affected_rows();	
			}
			
			
		if(count($newstage)>0)
			{
		
			for($u=0;$u<count($newstage);$u++)
			{
			
	$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$newstage[$u])->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{	
			
		if($newstage[$u]<>0)
			{	$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage[$u],'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
			
			/** Add Tat **/
						$stageid=$this->db->insert_id();
						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$newstage[$u]);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			
			}
			}else
			{
				foreach($checkodd->result() as $checkodd1);
				/** Add Tat **/
						$stageid=$checkodd1->id;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$newstage[$u]);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
				
			}
			
			}
			
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Order Moved to Next Process.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			
		}else{
				
				
				if($isfinalstep!=0)
				{
					
						/** Check if Merge is available **/
				  $merger=$this->db->select('id,mergewith,flowid')->from('fmsmerge')->where('productionflow',$productionflowid)->get();
					if($merger->num_rows()>0)
					{
						foreach($merger->result() as $mergerdata);
						$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$mergerdata->flowid)->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
						$orderstmergedata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$mergerdata->flowid,'userstatus'=>'0','remarks'=>'','addedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('order_stage',$orderstmergedata); 
				        $odm=$this->db->insert_id();
						/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			}else
			{
				foreach($checkodd->result() as $prevorderstage);
				$odm=$prevorderstage->id;
				
				/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
				
				
				
			}
					
						if($odm<>0)
						{
							$mergehistdata=array('orderstageid'=>$odm,'oldproductionflow'=>$productionflowid,'jobcardid'=>$jobcardid,'newproductionflow'=>$mergerdata->mergewith,'oldfmsflow'=>$flowstage,'newfmsflow'=>$mergerdata->flowid,'addedOn'=>date('Y-m-d H:i:s'));
							$this->db->insert('fmsmergehistory',$mergehistdata);
							
							$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Merged.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
							
							}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to merge flow.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
					}else { 
					    
					   
					$newdata=array('complete'=>'1');
					$this->db->where('order_id',$orderid);
					$this->db->where('id',$jobcardid);
					$this->db->update('order_instruments',$newdata);


					/** IF THIS IS SALESFORCE ORDER **/
					$this->checkifthisissforderandlastonetogetcompleted($orderid);
					/** END **/
					   
					
					if($selforder=='1')
					{
					$this->fmsmodel->addstock($jobcardid);
					}

$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);					
					}
					/** Merge **/
					$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}else
				{
				
				/** End **/
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}
			}
				/** End **/
		

			
	}

		
	function kittingbopOlddd28Jan2020()
	{
	$orderstage=$this->uri->segment(3);
	$flowstage=$this->uri->segment(4);
	$orderid=$this->uri->segment(5);
	$productionflowid=$this->uri->segment(6);
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$newstage=[];
	$isfinalstep=0;
	$jobcardid=$this->input->post('jobcardid'.$orderstage);
	$stock=$this->input->post('stock'.$orderstage);
	/** Move to next stage **/
			$currentstage=$flowstage;
			
			$getset=$this->db->select('setorder,finalstep')->from('fms_flow')->where('flow_id',$currentstage)->where('production_flow_id',$productionflowid)->get();
			if($getset->num_rows()>0)
			{
				foreach($getset->result() as $ggetset);
				$currentsetorder=$ggetset->setorder;	
				$isfinalstep=$ggetset->finalstep;
				/** Get Next Order **/
				$getneworder=$this->db->select('flow_id')->from('fms_flow')->where('setorder>',$currentsetorder)->where('production_flow_id',$productionflowid)->limit(1)->order_by('setorder','ASC')->get();
				if($getneworder->num_rows()>0)
			{
				foreach($getneworder->result() as $getneworder1);
				$restymove=$this->db->select('moveto')->from('fms_flow')->where('flow_id',$flowstage)->get();
				foreach($restymove->result() as $restymove1)
				{
				if(($restymove1->moveto<>0) && ($restymove1->moveto<>''))
				{
				$moveflow=$restymove1->moveto;
				$movearr=explode(',',$moveflow);
				foreach($movearr as $movea)
				{
				$newstage[]=$movea;
				}
				
				} else
				{
					$newstage[]=$getneworder1->flow_id;
				} 
				}


	/** End **/
		
			}else{
				
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			}
				/** End **/
				
			}else{
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			}
			//echo "<pre>"; print_r($newstage); exit;
			if(count($newstage)>0)
			{
				if($stock=='0')
				{
					$pr=$this->input->post('prno'.$orderstage);
					$mtname=$this->input->post('mtname'.$orderstage);
					
				}else{ $pr=''; $mtname=''; }
			/** Detail Entery **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'stock'=>$stock,'prno'=>$pr,'material'=>$mtname,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->insert('kitting_bop_details',$detailentry);
			$effrow1=$this->db->affected_rows();	
			
			/** End**/
			if($effrow1>0)
			{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);
			$effrow=$this->db->affected_rows();	
		if($effrow>0)
		{
			for($u=0;$u<count($newstage);$u++)
			{
			
	$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$newstage[$u])->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{	
			
		if($newstage[$u]<>0)
			{	$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage[$u],'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
			}
			}
			
			}
			
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Order Moved to Next Process.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			}
		}
		
			}else{
				
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			}
		
	}
		
		
		
				function machining_process5()
	{
		$scheduler_data=array();
	$flowstage=$this->uri->segment(3);
	$productionflowid=$this->uri->segment(4);
	/*GET CURRENT PROCESS SORT ORDER*/
				$QRY = $this->db->select('total_days,tat,set_time, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();
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
			$currentprocessorder = $flowstage;
			$setorder=$fmsinformation->setorder;
	/*GET CURRENT PROCESS SORT ORDER*/
		$user_id =$this->session->userdata['logged_in']['user_id'];
		$fmsprocess=$productionflowid;
	/** END **/
	if($fmsprocess<>0)
	{
	/** Get Process Name **/
	$this->db->select('d.file_number,f.ordertype,f.fileno,a.skipatthistep,a.jumpfabricationappl,a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow,d.fabrication,f.planstartsfrom')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('a.who_wedo',$user_id)->order_by('b.userstatus','ASC')->order_by('b.addedOn','DESC')->order_by('e.selforder','ASC');
	$process=$this->db->get();
	/** End **/
	if($process->num_rows()>0)
	{
	// echo "<pre>"; print_r($process->result());exit;
	$i=1;
	foreach($process->result() as $proc)
	{

		$ordertype = $proc->ordertype;
			if($ordertype=='1'){
				
				$fileno=$proc->file_number;
			}else{
			
				$fileno=$proc->fileno;
			}

	if($proc->userstatus=='0')
	{
	$prestui="<span class='btn btn-danger btn-xs'>Update Pending</span>";
	//$action='<a href="javascript:;" onclick="markstagedone('."'".$proc->orderstageid."'".','."'".$proc->flowstage."'".','."'".$proc->originalorderid."'".','."'".$proc->jobcardid."'".')"><span class="btn btn-xs btn-warning">Mark as done</span></a>';
	$html='<button class="btn btn-xs btn-warning waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$proc->orderstageid.'">Mark as Done</button>';

	$html.= ' <div id="con-close-modal'.$proc->orderstageid.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<form action="'.page_url.'Orderstage/qcremarks/'.$proc->orderstageid.'/'.$proc->flowstage.'/'.$proc->originalorderid.'/'.$fmsprocess.'" method="post" id="loginForm">
		
	  <div id="pageloader">
	   <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
	</div>
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
												<h4 class="modal-title">IN PROCESS QC</h4>
											</div>
											<div class="modal-body">
										
												<div class="row">
												
												
													<div class="col-md-6">
														<div class="form-group">
															<label for="field-2" class="control-label">JOBCARD NUMBER</label>
															
															<input type="text" style="width:250px" class="form-control" id="jobcard_no'.$proc->orderstageid.'" style="text-transform: uppercase;" name="jobcard_no'.$proc->orderstageid.'" placeholder="" value="'.$proc->job_card_no.'" readonly>
															<input type="hidden" name="jobcardid'.$proc->orderstageid.'" value="'.$proc->jobcardid.'">
														</div>
													</div>

													<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">QC STATUS</label><br>

													<select class="form-control" id="status'.$proc->orderstageid.'" name="status'.$proc->orderstageid.'" required style="width:250px" onchange="getstockdetail('.$proc->orderstageid.');">
													<option value="">SELECT</option>
													<option value="1">PASS</option>
													<option value="0">REJECTED</option>
													</select>
													</div>
													</div>
												</div>
												
												
												<div class="row" id="prreq'.$proc->orderstageid.'" style="display:none; padding-top:20px">
												<div class="col-md-6">
														<div style="margin-bottom: 15px;">
															<label>BACKTRACK TO STAGE</label><span id="error_purpose" style="color:red;">*</span><br/>

															<select style="width:250px" class="form-control" id="backtrack'.$proc->orderstageid.'" name="backtrack'.$proc->orderstageid.'">
	<option value="">SELECT</option>';
	$restt=$this->db->select('flow_id,fms_flow')->from('fms_flow')->where('flow_id<',$flowstage)->where('production_flow_id',$productionflowid)->get();
	if($restt->num_rows()>0)
	{
		foreach($restt->result() as $restttt)
		{
	$html.= '<option value="'.$restttt->flow_id.'">'.$restttt->fms_flow.'</option>';
		}
	}
	$html.=  '</select>
														</div>
													</div>
													
													<div class="col-md-6">
														<div class="form-group">
															<label for="field-1" class="control-label">REASON FOR REJECTION</label><span id="error_purpose" style="color:red;">*</span><br/>

															<textarea style="width:250px" class="form-control" id="reason'.$proc->orderstageid.'" style="text-transform: uppercase;" name="reason'.$proc->orderstageid.'" placeholder="" value="" col="20"></textarea>

														</div>
													</div>';
													
													
												
												
												$html.='</div>';

                                    if($proc->skipatthistep<>'0')
                                    	{
														$skipdata=$this->db->select('flow_id,fms_flow')->from('fms_flow')->where('production_flow_id',$productionflowid)->where('skippable','1')->where('super','1')->order_by('flow_id','ASC')->get();
												

													$html.='<div class="row skip'.$proc->orderstageid.'" style="display:none"><div class="col-md-12">
														<div class="form-group">
															<label for="field-1" class="control-label" style="color:red">Choose Step You want to Skip (None for No Step to skip,Both for Skipping Both the step)</label><span id="error_purpose" style="color:red;">*</span><br/>
															<select class="form-control" id="skipstep'.$proc->orderstageid.'" name="skipstep'.$proc->orderstageid.'" required style="width:250px">
															
										<option value="N" selected>NONE</option>';
															
															if($skipdata->num_rows()>0)
														{
															$allf=array();
															foreach($skipdata->result() as $skipdatas)
															{
																$allf[]=$skipdatas->flow_id;
															}
															$both=implode(',',$allf);
															$html.='<option value="'.$both.'">BOTH</option>';
															foreach($skipdata->result() as $skipdatas)
															{
															
															$html.='<option value="'.$skipdatas->flow_id.'">'.strtoupper($skipdatas->fms_flow).'</option>';
															}
															
															
														}
															
													$html.='</select>

														</div>
													</div></div>';
													
													}

											$html.='</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
												<input type="submit" id="save" class="btn btn-info" value="Submit">
											</div>
										</div>
									</div>
	</form>
								</div>';


	//$compdate=date('d-M-Y H:i:s',strtotime($proc->stagecompletedate));
	$completedate="";	
	$addedtimepunch = "";
		$completetime="";

	}else{


	$compdate=date('d-M-Y H:i:s',strtotime($proc->stagecompletedate));
	$completedate=date('d-M-Y',strtotime($proc->stagecompletedate));
	$prestui="<span class='btn btn-success btn-xs'>Remarks Updated</span>";
	$action="Task completed on ".$compdate;
	$html="This Task has been completed by you.	";

	$compdate=date('d-M-Y H:i:s',strtotime($proc->stagecompletedate));
	$completedate=date('d-M-Y',strtotime($proc->stagecompletedate));	
	$completetime = date('H:i:s', strtotime($proc->stagecompletedate));
	$addedtimepunch = "<br>". date('g:i A', strtotime($completetime));
	}
	date_default_timezone_set("Asia/Kolkata");
	$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
	$addedtime = "<br>". date('g:i A', strtotime($time));



	$time1=date('H:i:s', strtotime($proc->plannedOn));
	$addedtime1 = "<br>". date('g:i A', strtotime($time1));
	$planneddate1=date('Y-m-d',strtotime($proc->plannedOn));

	$previousorder = $tatstage;
						
		

						/*PREVIOUS COMPLITION DATE*/

			
		$QRY = $this->db->select('total_days, tat, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();

	if($this->uri->segment(3)=='1'){
		date_default_timezone_set("Asia/Kolkata");
	$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
	$addedtime = "<br>". date('g:i A', strtotime($time));
		}else{
		$previousorder = $setorder-1; 
		$QRY = $this->db->select('total_days, tat,setorder, flow_id ')->from('fms_flow')->where('setorder',$previousorder)->get();
		if($QRY->num_rows()>0){
		foreach($QRY->result() as $paststepdate);
			$flowid = $paststepdate->flow_id;
		$QRY1 = $this->db->select('jobcardid, flowstage,addedOn')->from('order_stage')->where('jobcardid',$proc->jobcardid)->where('flowstage',$flowid)->get();	
		if($QRY1->num_rows()>0){
		foreach($QRY1->result() as $timestamp){
		$addeddate1 = date('Y-m-d', strtotime($timestamp->addedOn));
	$time = date('H:i:s', strtotime($timestamp->addedOn));
	$addedtime = "<br>". date('g:i A', strtotime($time));
		}
		}	
		}else{
		$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
		}
		}

	$qcstatus="";
	$backtrack="";
	$qry = $this->db->select('a.jobcardid,a.qcstatus,a.rejectreason,a.backtrackto, b.flow_id, b.fms_flow')->from('qcremarks a')->join('fms_flow b','a.backtrackto=b.flow_id','left')->where('a.jobcardid',$proc->jobcardid)->where('a.orderstageid',$proc->orderstageid)->order_by('a.id','DESC')->get();
	if($qry->num_rows()>0){
		foreach($qry->result() as $row);
		if($row->qcstatus=='1'){
			$qcstatus = "PASS";
			$backtrack="";

	}else if($row->qcstatus=='0'){
		$qcstatus = "REJECTED<br>".$row->rejectreason;
		$backtrack = $row->fms_flow;
	}
	}	
		
		/** Get User Department **/
				$udep=$this->db->select('b.department')->from('system_users a')->join('departments b','a.department_id=b.department_id')->where('a.user_id',$_SESSION['logged_in']['user_id'])->get(); 
				if($udep->num_rows()>0)
				{
					foreach($udep->result() as $depp);
					$depart=$depp->department;
					
				}else
				{
					$depart='';
				}
				
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
					
					/** Check if instrument requires fabrication **/
					/** $ryt=$this->db->select('b.fabrication')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->where('a.id',$proc->jobcardid)->get();
					if($ryt->num_rows()>0)
					{
						foreach($ryt->result() as $rtyeyte);
						$fabreq=$rtyeyte->fabrication;
						
					}else{ $fabreq=0 ;} **/
					
						/** Get Last Step of Fabrication **/
					/** if($fabreq==0)
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
				/** End **/
			
		/** Check for merge fms intersection **/
				if($markapp==1)
				{
					
				$markapp=$this->fmsmodel->checkformergerpoint($flowstage,$proc->jobcardid,$proc->originalorderid,$proc->fabrication,$proc->jumpfabricationappl);
				}
		/** End **/	
				
			if($markapp==1)
			{

$timestamp=$this->getprevioustimestamp($tatstage,$proc->plannedOn,$proc->jobcardid,$flowstage,$proc->orderstageid);

	$tattime = DateTime::createFromFormat('d-M-Y g:i A', $timestamp);
		
	//$finaltattime=$tattime->format('g:i A');
	$finaltattime=date('g:i A',strtotime($timestamp));
	$tatdate=$this->getupcomingtatdatenew($tatstage,$proc->plannedOn,$proc->jobcardid,$timestamp,$totaldaysslave,$proc->orderstageid,$day);

	if($proc->userstatus=='0')
	{
	$completedon="";
	}else
	{
	$completedon=date('d-M-Y g:i A',strtotime($proc->stagecompletedate));
	}
			
		$odtype=$this->getordertype($proc->originalorderid);		
			
	$scheduler_data[] = array('sr_no'=>$i,
	'orderdate'=>$timestamp,
	'odtype'=>$odtype,
	'mname'=>strtoupper($proc->instruments_name),
	'jobcard'=>strtoupper($proc->job_card_no),
	'fileno'=>$fileno,
	'factory'=>strtoupper($depart),
	'tat'=>$tatdate,
	'actual'=>$completedon,
	'totdays'=>'',
	'status'=>$prestui,
	'qcstatus'=>$qcstatus,
	'backtrack'=>$backtrack,
	'action'=>$html);
			
	 
	$i++;
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
	} 
		
	
	
		function machining_process5oldbeforeskippart17march()
	{
		$scheduler_data=array();
	$flowstage=$this->uri->segment(3);
	$productionflowid=$this->uri->segment(4);
	/*GET CURRENT PROCESS SORT ORDER*/
				$QRY = $this->db->select('total_days,tat,set_time, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();
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
			$currentprocessorder = $flowstage;
			$setorder=$fmsinformation->setorder;
	/*GET CURRENT PROCESS SORT ORDER*/
		$user_id =$this->session->userdata['logged_in']['user_id'];
		$fmsprocess=$productionflowid;
	/** END **/
	if($fmsprocess<>0)
	{
	/** Get Process Name **/
	$this->db->select('a.skipatthisstep,a.jumpfabricationappl,a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow,d.fabrication,f.planstartsfrom')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('a.who_wedo',$user_id)->order_by('b.userstatus','ASC')->order_by('b.addedOn','DESC');
	$process=$this->db->get();
	/** End **/
	if($process->num_rows()>0)
	{
	// echo "<pre>"; print_r($process->result());exit;
	$i=1;
	foreach($process->result() as $proc)
	{
	if($proc->userstatus=='0')
	{
	$prestui="<span class='btn btn-danger btn-xs'>Update Pending</span>";
	//$action='<a href="javascript:;" onclick="markstagedone('."'".$proc->orderstageid."'".','."'".$proc->flowstage."'".','."'".$proc->originalorderid."'".','."'".$proc->jobcardid."'".')"><span class="btn btn-xs btn-warning">Mark as done</span></a>';
	$html='<button class="btn btn-xs btn-warning waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$proc->orderstageid.'">Mark as Done</button>';

$html.= ' <div id="con-close-modal'.$proc->orderstageid.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<form action="'.page_url.'Orderstage/qcremarks/'.$proc->orderstageid.'/'.$proc->flowstage.'/'.$proc->originalorderid.'/'.$fmsprocess.'" method="post" id="loginForm">
		
	  <div id="pageloader">
	   <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
	</div>
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
												<h4 class="modal-title">IN PROCESS QC</h4>
											</div>
											<div class="modal-body">
										
												<div class="row">
												
												
													<div class="col-md-6">
														<div class="form-group">
															<label for="field-2" class="control-label">JOBCARD NUMBER</label>
															
															<input type="text" style="width:250px" class="form-control" id="jobcard_no'.$proc->orderstageid.'" style="text-transform: uppercase;" name="jobcard_no'.$proc->orderstageid.'" placeholder="" value="'.$proc->job_card_no.'" readonly>
															<input type="hidden" name="jobcardid'.$proc->orderstageid.'" value="'.$proc->jobcardid.'">
														</div>
													</div>

													<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">QC STATUS</label><br>

													<select class="form-control" id="status'.$proc->orderstageid.'" name="status'.$proc->orderstageid.'" required style="width:250px" onchange="getstockdetail('.$proc->orderstageid.');">
													<option value="">SELECT</option>
													<option value="1">PASS</option>
													<option value="0">REJECTED</option>
													</select>
													</div>
													</div>
												</div>
												
												<div class="row" id="prreq'.$proc->orderstageid.'" style="display:none; padding-top:20px">
												<div class="col-md-6">
														<div style="margin-bottom: 15px;">
															<label>BACKTRACK TO STAGE</label><span id="error_purpose" style="color:red;">*</span><br/>

															<select style="width:250px" class="form-control" id="backtrack'.$proc->orderstageid.'" name="backtrack'.$proc->orderstageid.'">
	<option value="">SELECT</option>';
	$restt=$this->db->select('flow_id,fms_flow')->from('fms_flow')->where('flow_id<',$flowstage)->where('production_flow_id',$productionflowid)->get();
	if($restt->num_rows()>0)
	{
		foreach($restt->result() as $restttt)
		{
	$html.= '<option value="'.$restttt->flow_id.'">'.$restttt->fms_flow.'</option>';
		}
	}
	$html.=  '</select>
														</div>
													</div>
													
													<div class="col-md-6">
														<div class="form-group">
															<label for="field-1" class="control-label">REASON FOR REJECTION</label><span id="error_purpose" style="color:red;">*</span><br/>

															<textarea style="width:250px" class="form-control" id="reason'.$proc->orderstageid.'" style="text-transform: uppercase;" name="reason'.$proc->orderstageid.'" placeholder="" value="" col="20"></textarea>

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



	//$compdate=date('d-M-Y H:i:s',strtotime($proc->stagecompletedate));
	$completedate="";	
	$addedtimepunch = "";
		$completetime="";

	}else{


	$compdate=date('d-M-Y H:i:s',strtotime($proc->stagecompletedate));
	$completedate=date('d-M-Y',strtotime($proc->stagecompletedate));
	$prestui="<span class='btn btn-success btn-xs'>Remarks Updated</span>";
	$action="Task completed on ".$compdate;
	$html="This Task has been completed by you.	";

	$compdate=date('d-M-Y H:i:s',strtotime($proc->stagecompletedate));
	$completedate=date('d-M-Y',strtotime($proc->stagecompletedate));	
	$completetime = date('H:i:s', strtotime($proc->stagecompletedate));
	$addedtimepunch = "<br>". date('g:i A', strtotime($completetime));
	}
	date_default_timezone_set("Asia/Kolkata");
	$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
	$addedtime = "<br>". date('g:i A', strtotime($time));



	$time1=date('H:i:s', strtotime($proc->plannedOn));
	$addedtime1 = "<br>". date('g:i A', strtotime($time1));
	$planneddate1=date('Y-m-d',strtotime($proc->plannedOn));

	$previousorder = $tatstage;
						
		

						/*PREVIOUS COMPLITION DATE*/

			
		$QRY = $this->db->select('total_days, tat, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();

	if($this->uri->segment(3)=='1'){
		date_default_timezone_set("Asia/Kolkata");
	$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
	$addedtime = "<br>". date('g:i A', strtotime($time));
		}else{
		$previousorder = $setorder-1; 
		$QRY = $this->db->select('total_days, tat,setorder, flow_id ')->from('fms_flow')->where('setorder',$previousorder)->get();
		if($QRY->num_rows()>0){
		foreach($QRY->result() as $paststepdate);
			$flowid = $paststepdate->flow_id;
		$QRY1 = $this->db->select('jobcardid, flowstage,addedOn')->from('order_stage')->where('jobcardid',$proc->jobcardid)->where('flowstage',$flowid)->get();	
		if($QRY1->num_rows()>0){
		foreach($QRY1->result() as $timestamp){
		$addeddate1 = date('Y-m-d', strtotime($timestamp->addedOn));
	$time = date('H:i:s', strtotime($timestamp->addedOn));
	$addedtime = "<br>". date('g:i A', strtotime($time));
		}
		}	
		}else{
		$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
		}
		}

	$qcstatus="";
	$backtrack="";
	$qry = $this->db->select('a.jobcardid,a.qcstatus,a.rejectreason,a.backtrackto, b.flow_id, b.fms_flow')->from('qcremarks a')->join('fms_flow b','a.backtrackto=b.flow_id','left')->where('a.jobcardid',$proc->jobcardid)->where('a.orderstageid',$proc->orderstageid)->order_by('a.id','DESC')->get();
	if($qry->num_rows()>0){
		foreach($qry->result() as $row);
		if($row->qcstatus=='1'){
			$qcstatus = "PASS";
			$backtrack="";

	}else if($row->qcstatus=='0'){
		$qcstatus = "REJECTED<br>".$row->rejectreason;
		$backtrack = $row->fms_flow;
	}
	}	
		
		/** Get User Department **/
				$udep=$this->db->select('b.department')->from('system_users a')->join('departments b','a.department_id=b.department_id')->where('a.user_id',$_SESSION['logged_in']['user_id'])->get(); 
				if($udep->num_rows()>0)
				{
					foreach($udep->result() as $depp);
					$depart=$depp->department;
					
				}else
				{
					$depart='';
				}
				
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
					
					/** End **/
					
					/** Check if instrument requires fabrication **/
					/** $ryt=$this->db->select('b.fabrication')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->where('a.id',$proc->jobcardid)->get();
					if($ryt->num_rows()>0)
					{
						foreach($ryt->result() as $rtyeyte);
						$fabreq=$rtyeyte->fabrication;
						
					}else{ $fabreq=0 ;} **/
					
						/** Get Last Step of Fabrication **/
					/** if($fabreq==0)
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
					$this->db->select('a.dependentflowid')->from('flowdependency a')->join('order_stage b','a.dependentflowid=b.flowstage')->where('a.flowid',$flowstage);

					//$availableflowstages = "'" . implode ( "', '", $availableflowstage ) . "'";

					//$this->db->where_in('a.dependentflowid',$availableflowstages,false);

					$this->db->where('b.jobcardid',$proc->jobcardid)->where('b.orderid',$proc->originalorderid);

							
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
				/** End **/
			
		/** Check for merge fms intersection **/
				if($markapp==1)
				{
					
				$markapp=$this->fmsmodel->checkformergerpoint($flowstage,$proc->jobcardid,$proc->originalorderid,$proc->fabrication,$proc->jumpfabricationappl);
				}
		/** End **/	
				
			if($markapp==1)
			{

$timestamp=$this->getprevioustimestamp($tatstage,$proc->plannedOn,$proc->jobcardid,$flowstage,$proc->orderstageid);

	$tattime = DateTime::createFromFormat('d-M-Y g:i A', $timestamp);
		
	//$finaltattime=$tattime->format('g:i A');
	$finaltattime=date('g:i A',strtotime($timestamp));
	$tatdate=$this->getupcomingtatdatenew($tatstage,$proc->plannedOn,$proc->jobcardid,$timestamp,$totaldaysslave,$proc->orderstageid,$day);

	if($proc->userstatus=='0')
	{
	$completedon="";
	}else
	{
	$completedon=date('d-M-Y g:i A',strtotime($proc->stagecompletedate));
	}
			
				
			
	$scheduler_data[] = array('sr_no'=>$i,
	'orderdate'=>$timestamp,
	'mname'=>strtoupper($proc->instruments_name),
	'jobcard'=>strtoupper($proc->job_card_no),
	'factory'=>strtoupper($depart),
	'tat'=>$tatdate,
	'actual'=>$completedon,
	'totdays'=>'',
	'status'=>$prestui,
	'qcstatus'=>$qcstatus,
	'backtrack'=>$backtrack,
	'action'=>$html);
			
	 
	$i++;
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
	} 
		
		
		function machining_process5oldtemp()
	{
		$scheduler_data=array();
	$flowstage=$this->uri->segment(3);
	$productionflowid=$this->uri->segment(4);
	/*GET CURRENT PROCESS SORT ORDER*/
			$QRY = $this->db->select('total_days, tat, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();

			$res = $QRY->result();
			foreach($res as $fmsinformation);
			$totaldaysslave = $fmsinformation->total_days;
			$tatstage = $fmsinformation->tat;
			$currentprocessorder = $flowstage;
			$setorder=$fmsinformation->setorder;
	/*GET CURRENT PROCESS SORT ORDER*/



	$user_id =$this->session->userdata['logged_in']['user_id'];
	/*$isfms=$this->db->select('fms_process')->from('system_users')->where('user_id',$user_id)->get();
	if($isfms->num_rows()>0)
	{
	foreach($isfms->result() as $isfmsa);
	$fmsprocess=$isfmsa->fms_process;
	}else{ $fmsprocess=0; } */

	$fmsprocess=$productionflowid;
	/** END **/
	if($fmsprocess<>0)
	{
	/** Get Process Name **/
	$process=$this->db->select('a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('a.who_wedo',$user_id)->order_by('b.userstatus','ASC')->order_by('b.addedOn','DESC')->get();
	/** End **/
	if($process->num_rows()>0)
	{
	// echo "<pre>"; print_r($process->result());exit;
	$i=1;
	foreach($process->result() as $proc)
	{
	if($proc->userstatus=='0')
	{
	$prestui="<span class='btn btn-danger btn-xs'>Update Pending</span>";
	//$action='<a href="javascript:;" onclick="markstagedone('."'".$proc->orderstageid."'".','."'".$proc->flowstage."'".','."'".$proc->originalorderid."'".','."'".$proc->jobcardid."'".')"><span class="btn btn-xs btn-warning">Mark as done</span></a>';
	$html='<button class="btn btn-xs btn-warning waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$proc->orderstageid.'">Mark as Done</button>';

	$html.= ' <div id="con-close-modal'.$proc->orderstageid.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<form action="'.page_url.'Orderstage/qcremarks/'.$proc->orderstageid.'/'.$proc->flowstage.'/'.$proc->originalorderid.'/'.$fmsprocess.'" method="post" id="loginForm">
		
	  <div id="pageloader">
	   <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
	</div>
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
												<h4 class="modal-title">IN PROCESS QC</h4>
											</div>
											<div class="modal-body">
										
												<div class="row">
												
												
													<div class="col-md-6">
														<div class="form-group">
															<label for="field-2" class="control-label">JOBCARD NUMBER</label>
															
															<input type="text" style="width:250px" class="form-control" id="jobcard_no'.$proc->orderstageid.'" style="text-transform: uppercase;" name="jobcard_no'.$proc->orderstageid.'" placeholder="" value="'.$proc->job_card_no.'" readonly>
															<input type="hidden" name="jobcardid'.$proc->orderstageid.'" value="'.$proc->jobcardid.'">
														</div>
													</div>

													<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">QC STATUS</label><br>

													<select class="form-control" id="status'.$proc->orderstageid.'" name="status'.$proc->orderstageid.'" required style="width:250px" onchange="getstockdetail('.$proc->orderstageid.');">
													<option value="">SELECT</option>
													<option value="1">PASS</option>
													<option value="0">REJECTED</option>
													</select>
													</div>
													</div>
												</div>
												
												<div class="row" id="prreq'.$proc->orderstageid.'" style="display:none; padding-top:20px">
												<div class="col-md-6">
														<div style="margin-bottom: 15px;">
															<label>BACKTRACK TO STAGE</label><span id="error_purpose" style="color:red;">*</span><br/>

															<select style="width:250px" class="form-control" id="backtrack'.$proc->orderstageid.'" name="backtrack'.$proc->orderstageid.'">
	<option value="">SELECT</option>';
	$restt=$this->db->select('flow_id,fms_flow')->from('fms_flow')->where('flow_id<',$flowstage)->where('production_flow_id',$productionflowid)->get();
	if($restt->num_rows()>0)
	{
		foreach($restt->result() as $restttt)
		{
	$html.= '<option value="'.$restttt->flow_id.'">'.$restttt->fms_flow.'</option>';
		}
	}
	$html.=  '</select>
														</div>
													</div>
													
													<div class="col-md-6">
														<div class="form-group">
															<label for="field-1" class="control-label">REASON FOR REJECTION</label><span id="error_purpose" style="color:red;">*</span><br/>

															<textarea style="width:250px" class="form-control" id="reason'.$proc->orderstageid.'" style="text-transform: uppercase;" name="reason'.$proc->orderstageid.'" placeholder="" value="" col="20"></textarea>

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


	//$compdate=date('d-M-Y H:i:s',strtotime($proc->stagecompletedate));
	$completedate="";	
	$addedtimepunch = "";
		$completetime="";

	}else{


	$compdate=date('d-M-Y H:i:s',strtotime($proc->stagecompletedate));
	$completedate=date('d-M-Y',strtotime($proc->stagecompletedate));
	$prestui="<span class='btn btn-success btn-xs'>Remarks Updated</span>";
	$action="Task completed on ".$compdate;
	$html="This Task has been completed by you.	";

	$compdate=date('d-M-Y H:i:s',strtotime($proc->stagecompletedate));
	$completedate=date('d-M-Y',strtotime($proc->stagecompletedate));	
	$completetime = date('H:i:s', strtotime($proc->stagecompletedate));
	$addedtimepunch = "<br>". date('g:i A', strtotime($completetime));
	}
	date_default_timezone_set("Asia/Kolkata");
	$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
	$addedtime = "<br>". date('g:i A', strtotime($time));



	$time1=date('H:i:s', strtotime($proc->plannedOn));
	$addedtime1 = "<br>". date('g:i A', strtotime($time1));
	$planneddate1=date('Y-m-d',strtotime($proc->plannedOn));

	$previousorder = $tatstage;
						
		
							if($previousorder<>0){
						$abc = $this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$proc->jobcardid)->order_by('id','DESC')->limit(1)->get();
							if($abc->num_rows()>0){
						foreach($abc->result() as $pastinfo);
						$complitiontime = $pastinfo->addedOn;
							
								$lasttaskcompletedtime = "<br>". date('g:i A', strtotime($complitiontime));

								
							}else{
							$complitiontime = $proc->added_on;
									$lasttaskcompletedtime = "<br>". date('g:i A', strtotime($complitiontime));

							}
						}else{
						$complitiontime = $proc->plannedOn;	
								$lasttaskcompletedtime = "<br>". date('g:i A', strtotime($complitiontime));

						}

						/*PREVIOUS COMPLITION DATE*/

	/*PREVIOUS COMPLITION DATE*/
						
						$checkpreviousdate = date('Y-m-d', strtotime($complitiontime));
		
						$previouscomplitiondate = date('d-M-Y', strtotime($complitiontime));
		
						$previouscomplitiontime = date('H:i:s', strtotime($proc->added_on));
						$previouscomplitionfinaltime = "<br>". date('g:i A', strtotime($previouscomplitiontime));
				
						/*GET DATE OF PRIVIOUS STEPS*/
						$date11 = new DateTime($previouscomplitiondate); 
						$date22 = new DateTime($completedate); 
						$interval1 = $date11->diff($date22); 
						$ttldays = $interval1->d; 
						if($ttldays=='0'){
						$totalday = "1";
						}else{
						$totalday = $ttldays;
				}
				
				/*TAT Date*/
				$lastcompdate = date('Y-m-d', strtotime($checkpreviousdate));
				$TATDATE=date('d-M-Y', strtotime($lastcompdate."+".$totaldaysslave." days"));
				
				/*TAT Date*/
		
		$QRY = $this->db->select('total_days, tat, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();

	if($this->uri->segment(3)=='1'){
		date_default_timezone_set("Asia/Kolkata");
	$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
	$addedtime = "<br>". date('g:i A', strtotime($time));
		}else{
		$previousorder = $setorder-1; 
		$QRY = $this->db->select('total_days, tat,setorder, flow_id ')->from('fms_flow')->where('setorder',$previousorder)->get();
		if($QRY->num_rows()>0){
		foreach($QRY->result() as $paststepdate);
			$flowid = $paststepdate->flow_id;
		$QRY1 = $this->db->select('jobcardid, flowstage,addedOn')->from('order_stage')->where('jobcardid',$proc->jobcardid)->where('flowstage',$flowid)->get();	
		if($QRY1->num_rows()>0){
		foreach($QRY1->result() as $timestamp){
		$addeddate1 = date('Y-m-d', strtotime($timestamp->addedOn));
	$time = date('H:i:s', strtotime($timestamp->addedOn));
	$addedtime = "<br>". date('g:i A', strtotime($time));
		}
		}	
		}else{
		$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
		}
		}

	$qcstatus="";
	$backtrack="";
	$qry = $this->db->select('a.jobcardid,a.qcstatus,a.rejectreason,a.backtrackto, b.flow_id, b.fms_flow')->from('qcremarks a')->join('fms_flow b','a.backtrackto=b.flow_id','left')->where('a.jobcardid',$proc->jobcardid)->where('a.orderstageid',$proc->orderstageid)->order_by('a.id','DESC')->get();
	if($qry->num_rows()>0){
		foreach($qry->result() as $row);
		if($row->qcstatus=='1'){
			$qcstatus = "PASS";
			$backtrack="";

	}else if($row->qcstatus=='0'){
		$qcstatus = "REJECTED<br>".$row->rejectreason;
		$backtrack = $row->fms_flow;
	}
	}	
		
		/** Get User Department **/
				$udep=$this->db->select('b.department')->from('system_users a')->join('departments b','a.department_id=b.department_id')->where('a.user_id',$_SESSION['logged_in']['user_id'])->get(); 
				if($udep->num_rows()>0)
				{
					foreach($udep->result() as $depp);
					$depart=$depp->department;
					
				}else
				{
					$depart='';
				}
				
				/** Check for dependency **/
				
				$checkdepend=$this->db->select('dependency')->from('fms_flow')->where('flow_id',$flowstage)->where('dependency','1')->get();
				$depend=$checkdepend->num_rows();
				//echo $depend;exit;
				if($depend==1)
				{
					
					$restt=$this->db->select('dependentflowid')->from('flowdependency')->where('flowid',$flowstage)->get();
					if($restt->num_rows()>0)
					{
						$dependcount=$restt->num_rows();
						$donarr=array();
						$donarr[]=0;
						//echo "<pre>";print_r($restt->result());exit;
						foreach($restt->result() as $resttyu)
						{
							$isdone=$this->db->select('id,userstatus')->from('order_stage')->where('flowstage',$resttyu->dependentflowid)->where('jobcardid',$proc->jobcardid)->where('orderid',$proc->originalorderid)->order_by('id','DESC')->limit(1)->get();
							if($isdone->num_rows()>0)
							{
								foreach($isdone->result() as $isdone11);
								$donarr[]=$isdone11->userstatus;
								
							}else
							{
								$markapp=1;
							}
							
						}
						//echo $dependcount.'<br>'."<pre>"; print_r($donarr);exit;
						if($dependcount==array_sum($donarr))
						{
							$markapp=1;
						}else{
							$markapp=0;
						}
						
					}else{
						$markapp=1;
					}
					
				}else{
					
					$markapp=1;
				}
				/** End **/
				
				/** New Timestamp query **/
				$previousorder=$tatstage;
				if($previousorder==0)
				{
					$previouscomtime=$proc->plannedOn;
					$previouscomdate=date('d-M-Y',strtotime($previouscomtime))."<br>";
					$previouscomtimeonly=date('g:i A',strtotime($previouscomtime));
					
				}else
				{
					//echo $previousorder;exit;
					$oldtime1=$this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$proc->jobcardid)->order_by('id','DESC')->limit(1)->get();
							if($oldtime1->num_rows()>0){
								foreach($oldtime1->result() as $pastinfo);
								$previouscomtime=$pastinfo->addedOn;
								$previouscomdate=date('d-M-Y',strtotime($pastinfo->addedOn))."<br>";
								$previouscomtimeonly=date('g:i A',strtotime($pastinfo->addedOn));
							}else
							{
								$previouscomtime=$proc->plannedOn;
					$previouscomdate=date('d-M-Y',strtotime($previouscomtime))."<br>";
					$previouscomtimeonly=date('g:i A',strtotime($previouscomtime));
							}
				}

		
				
				/** End **/
			
				
			if($markapp==1)
			{	
				/** Holiday Check**/
				
					$TATDATEFORHOLIDAYCHECK=date('Y-m-d', strtotime($TATDATE));
					$timestampforcheck=date('Y-m-d',strtotime($previouscomtime));
					$date_from=strtotime($timestampforcheck);
					$date_to=strtotime($TATDATEFORHOLIDAYCHECK);
					$betweendates=array();
					for ($g=$date_from; $g<=$date_to; $g+=86400) {  
						$betweendates[]= date("Y-m-d", $g);  
					} 
				
				//echo "<pre>"; print_r($betweendates);
					if(count($betweendates)>0)
				{
					
					$alldates = "'" . implode ( "', '", $betweendates ) . "'";
					
					$ifholiday=$this->db->select('holiday_id')->from('prestogroup_holidays')->where_in('holiday_date',$alldates,false)->get();
					if($ifholiday->num_rows()>0)
					{
			
						//echo "hi".$proc->job_card_no;
						$TATDATEFORHOLIDAYCHECK=date('Y-m-d', strtotime($TATDATEFORHOLIDAYCHECK."+".$ifholiday->num_rows()." days"));
						//echo $TATDATEFORHOLIDAYCHECK;
						
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
				//$TATDATE= date('d-M-Y', strtotime($nextdate."+1 days"));
				$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();
				if($query->num_rows()>0){
					
					foreach($query->result() as $afterholiday);
					$aftrholidaydate = $afterholiday->holiday_date;
					$TATDATE= date('d-M-Y', strtotime($aftrholidaydate."+1 days"));
					
				}else {
				$TATDATE= date('d-M-Y', strtotime($nextdate));
				}
			}else{
			$TATDATE=date('d-M-Y', strtotime($TATDATEFORHOLIDAYCHECK));
			}
					
				/** End **/
	$scheduler_data[] = array('sr_no'=>$i,
	'orderdate'=>$previouscomdate.$previouscomtimeonly,
	'mname'=>strtoupper($proc->instruments_name),
	'jobcard'=>strtoupper($proc->job_card_no),
	'factory'=>strtoupper($depart),
	'planned'=>date('d-M-Y',strtotime($proc->plannedOn)).$addedtime1,
	'tat'=>$TATDATE."<br>".$previouscomtimeonly,
	'actual'=>$completedate.$addedtimepunch,
	'totdays'=>$totalday,
	'status'=>$prestui,
	'qcstatus'=>$qcstatus,
	'backtrack'=>$backtrack,
	'action'=>$html);
			
	 
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

	}
	} 

	function qcremarksoldbeforefmstatdate()
	{
		
	$orderstage=$this->uri->segment(3);
	$flowstage=$this->uri->segment(4);
	$orderid=$this->uri->segment(5);
	$selforder=$this->fmsmodel->getselforder($orderid);
	$productionflowid=$this->uri->segment(6);
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$newstage=[];
	$isfinalstep=0;
	$jobcardid=$this->input->post('jobcardid'.$orderstage);
	$qcstatus=$this->input->post('status'.$orderstage);

	$currentstage=$flowstage;
	$getset=$this->db->select('setorder,finalstep,total_days,set_time')->from('fms_flow')->where('flow_id',$currentstage)->where('production_flow_id',$productionflowid)->get();
	
	if($qcstatus=='1')
	{	
		/** Move to next stage **/
			$currentstage=$flowstage;
		
			
				foreach($getset->result() as $ggetset);
				$currentsetorder=$ggetset->setorder;
				$isfinalstep=$ggetset->finalstep;
				$tatday=$ggetset->total_days;
				$tathours=$ggetset->set_time;
				
				/** Get Next Order **/
				$getneworder=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id',$productionflowid)->where('setorder>',$currentsetorder)->limit(1)->order_by('setorder','ASC')->get();
					if($getneworder->num_rows()>0)
					{
					foreach($getneworder->result() as $getneworder1);
					/** Move Check **/
					$restymove=$this->db->select('moveto')->from('fms_flow')->where('flow_id',$flowstage)->get();
					foreach($restymove->result() as $restymove1)
					{
					if(($restymove1->moveto!=0)&&($restymove1->moveto!=''))
					{
					$moveflow=$restymove1->moveto;
					$movearr=explode(',',$moveflow);
					foreach($movearr as $movea)
					{
					$newstage[]=$movea;
					}

					} else
					{
					$newstage[]=$getneworder1->flow_id;
					} 
					}
					}
				
				
				/** Detail Entery **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'qcstatus'=>$qcstatus,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->insert('qcremarks',$detailentry);
			$effrow1=$this->db->affected_rows();	
			
			/** End**/
			if($effrow1>0)
			{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);

		if(count($newstage)>0)
		{
			for($u=0;$u<count($newstage);$u++)
			{
			$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$newstage[$u])->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
			if($newstage[$u]<>0)
			{
			$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage[$u],'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
			/** Add Tat **/
						$stageid=$this->db->insert_id();
						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$newstage[$u]);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			
			}
			}else{

foreach($checkodd->result() as $checkodd1);


							/** Add Tat **/
							$stageid=$checkodd1->id;
							$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);

							if($settatdate<>'')
							{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
							if($checkfmstat->num_rows()==0)
							{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
							$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
							$this->db->insert('fmstatdate',$settat);
							}else
							{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));

							$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
							$this->db->where('flowstage',$newstage[$u]);
							$this->db->where('jobcardid',$jobcardid);
							$this->db->update('fmstatdate',$settat);
							}
							}
							/** END **/

			}	
			}
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Order Moved to Next Process.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}else{
			
			
				if($isfinalstep!=0)
				{
					
						/** Check if Merge is available **/
				  $merger=$this->db->select('id,mergewith,flowid')->from('fmsmerge')->where('productionflow',$productionflowid)->get();
					if($merger->num_rows()>0)
					{
						foreach($merger->result() as $mergerdata);
						$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$mergerdata->flowid)->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
						$orderstmergedata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$mergerdata->flowid,'userstatus'=>'0','remarks'=>'','addedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('order_stage',$orderstmergedata); 
				        $odm=$this->db->insert_id();
						/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			}else
			{
				foreach($checkodd->result() as $prevorderstage);
				$odm=$prevorderstage->id;
				/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
				
				
			}
					
						if($odm<>0)
						{
							$mergehistdata=array('orderstageid'=>$odm,'oldproductionflow'=>$productionflowid,'jobcardid'=>$jobcardid,'newproductionflow'=>$mergerdata->mergewith,'oldfmsflow'=>$flowstage,'newfmsflow'=>$mergerdata->flowid,'addedOn'=>date('Y-m-d H:i:s'));
							$this->db->insert('fmsmergehistory',$mergehistdata);
							
							$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Merged.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
							
							}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to merge flow.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
					}else { 
					$newdata=array('complete'=>'1');
					$this->db->where('order_id',$orderid);
					$this->db->where('id',$jobcardid);
					$this->db->update('order_instruments',$newdata);
					
					if($selforder=='1')
					{
					$this->fmsmodel->addstock($jobcardid);
					}

$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);					
					}
					/** Merge **/
					$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}else
				{
				
				/** End **/
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}
			
		}
		
		}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); 
		}
			
		
	}else{
		
		$backtrackid=$this->input->post('backtrack'.$orderstage);
		$reason=$this->input->post('reason'.$orderstage);
		/** Detail Entery **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'qcstatus'=>$qcstatus,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id,'rejectreason'=>$reason,'backtrackto'=>$backtrackid);
			$this->db->insert('qcremarks',$detailentry);
			$qcinsertid=$this->db->insert_id();
			$effrow1=$this->db->affected_rows();
			if($effrow1>0)
			{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);
			$effrow=$this->db->affected_rows();	
		if($effrow>0)
		{
			if($backtrackid<>0)
			{
			$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$backtrackid,'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
			$stageid=$this->db->insert_id();
			$datanewoid=array('neworderstageid'=>$stageid);
			$this->db->where('id',$qcinsertid);
			$this->db->update('qcremarks',$datanewoid);
			
					
			
			/** Add Tat **/
						$stageid=$stageid;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$backtrackid,0,$jobcardid,$orderid);
						
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$backtrackid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$backtrackid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$backtrackid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			
			
			
			}
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Order Moved to backtracked Process.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}else { 
		$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
		
			}else{
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to update please try again!</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			}
		
	}

	}


function qcremarks()
	{
		
	$orderstage=$this->uri->segment(3);
	$flowstage=$this->uri->segment(4);
	$orderid=$this->uri->segment(5);
	$selforder=$this->fmsmodel->getselforder($orderid);
	$productionflowid=$this->uri->segment(6);
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$newstage1=[];
	$newstage=array();
	$isfinalstep=0;
	$jobcardid=$this->input->post('jobcardid'.$orderstage);
	$qcstatus=$this->input->post('status'.$orderstage);

	$currentstage=$flowstage;
	$getset=$this->db->select('setorder,finalstep,total_days,set_time')->from('fms_flow')->where('flow_id',$currentstage)->where('production_flow_id',$productionflowid)->get();
	foreach($getset->result() as $ggetset);
				$currentsetorder=$ggetset->setorder;
				$isfinalstep=$ggetset->finalstep;
				$tatday=$ggetset->total_days;
				$tathours=$ggetset->set_time;
	
	if($qcstatus=='1')
	{	
		/** Move to next stage **/
			$currentstage=$flowstage;
		
			
				/** Get Next Order **/
				$getneworder=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id',$productionflowid)->where('setorder>',$currentsetorder)->limit(1)->order_by('setorder','ASC')->get();
					if($getneworder->num_rows()>0)
					{
					foreach($getneworder->result() as $getneworder1);
					/** Move Check **/
					$restymove=$this->db->select('moveto')->from('fms_flow')->where('flow_id',$flowstage)->get();
					foreach($restymove->result() as $restymove1)
					{
					if(($restymove1->moveto!=0)&&($restymove1->moveto!=''))
					{
					$moveflow=$restymove1->moveto;
					$movearr=explode(',',$moveflow);
					foreach($movearr as $movea)
					{
					$newstage1[]=$movea;
					}

					} else
					{
					$newstage1[]=$getneworder1->flow_id;
					} 
					}
					}
				/** Check if skipp is applicable so remove all the data **/
				$stepskip=$this->input->post('skipstep'.$orderstage);
				if($stepskip<>'N')
				{
					$expval=explode(',',$stepskip);
					$newstage=array_values(array_diff($newstage1,$expval));
					
					
				}else{
					
					$newstage=$newstage1;
					
				}
				
			
				if($isfinalstep==0)
				{
				if(count($newstage)==0)
				{
					$getendflow=end($expval);
					$nextsetorder=$this->getsetorder($getendflow);
					$currentsetorder=$nextsetorder+2;
					$fflow=$this->getnextflowid($currentsetorder,$productionflowid);
				
					if($fflow=='' || $fflow=='0')
					{
						echo "NO NEW FLOW AVAILABLE";exit;
					}
					$newstage[]=$fflow;
				}
				/** END **/
				
				//echo "<pre>"; print_r($newstage);exit;
				if($stepskip<>'N')
				{
					foreach($newstage as $newstagesss)
					{
					$this->addskiprecord($jobcardid,$orderid,$expval,$flowstage,$orderstage,$newstagesss);
					}					
				}
				
				}
				
				
				/** Detail Entery **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'qcstatus'=>$qcstatus,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->insert('qcremarks',$detailentry);
			$effrow1=$this->db->affected_rows();	
			
			/** End**/
			if($effrow1>0)
			{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);


		if(count($newstage)>0)
		{

			
			for($u=0;$u<count($newstage);$u++)
			{
			$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$newstage[$u])->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
			if($newstage[$u]<>0)
			{
			$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage[$u],'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
			/** Add Tat **/
						$stageid=$this->db->insert_id();
						
						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$newstage[$u]);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			
			}
			}else{

foreach($checkodd->result() as $checkodd1);


							/** Add Tat **/
							$stageid=$checkodd1->id;
							$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);

							if($settatdate<>'')
							{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
							if($checkfmstat->num_rows()==0)
							{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
							$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
							$this->db->insert('fmstatdate',$settat);
							}else
							{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));

							$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
							$this->db->where('flowstage',$newstage[$u]);
							$this->db->where('jobcardid',$jobcardid);
							$this->db->update('fmstatdate',$settat);
							}
							}
							/** END **/

			}	
			}
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Order Moved to Next Process.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}else{
			
			
				if($isfinalstep!=0)
				{
					
						/** Check if Merge is available **/
				  $merger=$this->db->select('id,mergewith,flowid')->from('fmsmerge')->where('productionflow',$productionflowid)->get();
					if($merger->num_rows()>0)
					{
						foreach($merger->result() as $mergerdata);
						$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$mergerdata->flowid)->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
						$orderstmergedata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$mergerdata->flowid,'userstatus'=>'0','remarks'=>'','addedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('order_stage',$orderstmergedata); 
				        $odm=$this->db->insert_id();
						/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			}else
			{
				foreach($checkodd->result() as $prevorderstage);
				$odm=$prevorderstage->id;
				/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
				
				
			}
					
						if($odm<>0)
						{
							$mergehistdata=array('orderstageid'=>$odm,'oldproductionflow'=>$productionflowid,'jobcardid'=>$jobcardid,'newproductionflow'=>$mergerdata->mergewith,'oldfmsflow'=>$flowstage,'newfmsflow'=>$mergerdata->flowid,'addedOn'=>date('Y-m-d H:i:s'));
							$this->db->insert('fmsmergehistory',$mergehistdata);
							
							$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Merged.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
							
							}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to merge flow.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
					}else { 
					$newdata=array('complete'=>'1');
					$this->db->where('order_id',$orderid);
					$this->db->where('id',$jobcardid);
					$this->db->update('order_instruments',$newdata);


					/** IF THIS IS SALESFORCE ORDER **/
					$this->checkifthisissforderandlastonetogetcompleted($orderid);

					/** END **/
					
					if($selforder=='1')
					{
					$this->fmsmodel->addstock($jobcardid);
					}

$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);					
					}
					/** Merge **/
					$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}else
				{
				
				/** End **/
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}
			
		}
		
		}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); 
		}
			
		
	}else{
		
		$backtrackid=$this->input->post('backtrack'.$orderstage);
		$reason=$this->input->post('reason'.$orderstage);
		/** Detail Entery **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'qcstatus'=>$qcstatus,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id,'rejectreason'=>$reason,'backtrackto'=>$backtrackid);
			$this->db->insert('qcremarks',$detailentry);
			$qcinsertid=$this->db->insert_id();
			$effrow1=$this->db->affected_rows();
			if($effrow1>0)
			{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);
			$effrow=$this->db->affected_rows();	
		if($effrow>0)
		{
			if($backtrackid<>0)
			{
			$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$backtrackid,'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
			$stageid=$this->db->insert_id();
			$datanewoid=array('neworderstageid'=>$stageid);
			$this->db->where('id',$qcinsertid);
			$this->db->update('qcremarks',$datanewoid);
			
					
			
			/** Add Tat **/
						$stageid=$stageid;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$backtrackid,0,$jobcardid,$orderid);
						
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$backtrackid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$backtrackid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$backtrackid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			
			
			
			}
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Order Moved to backtracked Process.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}else { 
		$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
		
			}else{
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to update please try again!</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			}
		
	}

	}

	
	function qcremarksoldbeforeskipppart17march()
	{
		
	$orderstage=$this->uri->segment(3);
	$flowstage=$this->uri->segment(4);
	$orderid=$this->uri->segment(5);
	$selforder=$this->fmsmodel->getselforder($orderid);
	$productionflowid=$this->uri->segment(6);
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$newstage=[];
	$isfinalstep=0;
	$jobcardid=$this->input->post('jobcardid'.$orderstage);
	$qcstatus=$this->input->post('status'.$orderstage);

	$currentstage=$flowstage;
	$getset=$this->db->select('setorder,finalstep,total_days,set_time')->from('fms_flow')->where('flow_id',$currentstage)->where('production_flow_id',$productionflowid)->get();
	foreach($getset->result() as $ggetset);
				$currentsetorder=$ggetset->setorder;
				$isfinalstep=$ggetset->finalstep;
				$tatday=$ggetset->total_days;
				$tathours=$ggetset->set_time;
	
	if($qcstatus=='1')
	{	
		/** Move to next stage **/
			$currentstage=$flowstage;
		
			
				/** Get Next Order **/
				$getneworder=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id',$productionflowid)->where('setorder>',$currentsetorder)->limit(1)->order_by('setorder','ASC')->get();
					if($getneworder->num_rows()>0)
					{
					foreach($getneworder->result() as $getneworder1);
					/** Move Check **/
					$restymove=$this->db->select('moveto')->from('fms_flow')->where('flow_id',$flowstage)->get();
					foreach($restymove->result() as $restymove1)
					{
					if(($restymove1->moveto!=0)&&($restymove1->moveto!=''))
					{
					$moveflow=$restymove1->moveto;
					$movearr=explode(',',$moveflow);
					foreach($movearr as $movea)
					{
					$newstage[]=$movea;
					}

					} else
					{
					$newstage[]=$getneworder1->flow_id;
					} 
					}
					}
				
				
				/** Detail Entery **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'qcstatus'=>$qcstatus,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->insert('qcremarks',$detailentry);
			$effrow1=$this->db->affected_rows();	
			
			/** End**/
			if($effrow1>0)
			{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);

		if(count($newstage)>0)
		{
			for($u=0;$u<count($newstage);$u++)
			{
			$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$newstage[$u])->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
			if($newstage[$u]<>0)
			{
			$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage[$u],'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
			/** Add Tat **/
						$stageid=$this->db->insert_id();
						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$newstage[$u]);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			
			}
			}else{

foreach($checkodd->result() as $checkodd1);


							/** Add Tat **/
							$stageid=$checkodd1->id;
							$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);

							if($settatdate<>'')
							{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
							if($checkfmstat->num_rows()==0)
							{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
							$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
							$this->db->insert('fmstatdate',$settat);
							}else
							{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));

							$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
							$this->db->where('flowstage',$newstage[$u]);
							$this->db->where('jobcardid',$jobcardid);
							$this->db->update('fmstatdate',$settat);
							}
							}
							/** END **/

			}	
			}
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Order Moved to Next Process.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}else{
			
			
				if($isfinalstep!=0)
				{
					
						/** Check if Merge is available **/
				  $merger=$this->db->select('id,mergewith,flowid')->from('fmsmerge')->where('productionflow',$productionflowid)->get();
					if($merger->num_rows()>0)
					{
						foreach($merger->result() as $mergerdata);
						$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$mergerdata->flowid)->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
						$orderstmergedata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$mergerdata->flowid,'userstatus'=>'0','remarks'=>'','addedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('order_stage',$orderstmergedata); 
				        $odm=$this->db->insert_id();
						/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			}else
			{
				foreach($checkodd->result() as $prevorderstage);
				$odm=$prevorderstage->id;
				/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
				
				
			}
					
						if($odm<>0)
						{
							$mergehistdata=array('orderstageid'=>$odm,'oldproductionflow'=>$productionflowid,'jobcardid'=>$jobcardid,'newproductionflow'=>$mergerdata->mergewith,'oldfmsflow'=>$flowstage,'newfmsflow'=>$mergerdata->flowid,'addedOn'=>date('Y-m-d H:i:s'));
							$this->db->insert('fmsmergehistory',$mergehistdata);
							
							$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Merged.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
							
							}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to merge flow.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
					}else { 
					$newdata=array('complete'=>'1');
					$this->db->where('order_id',$orderid);
					$this->db->where('id',$jobcardid);
					$this->db->update('order_instruments',$newdata);
					
					if($selforder=='1')
					{
					$this->fmsmodel->addstock($jobcardid);
					}

$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);					
					}
					/** Merge **/
					$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}else
				{
				
				/** End **/
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}
			
		}
		
		}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); 
		}
			
		
	}else{
		
		$backtrackid=$this->input->post('backtrack'.$orderstage);
		$reason=$this->input->post('reason'.$orderstage);
		/** Detail Entery **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'qcstatus'=>$qcstatus,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id,'rejectreason'=>$reason,'backtrackto'=>$backtrackid);
			$this->db->insert('qcremarks',$detailentry);
			$qcinsertid=$this->db->insert_id();
			$effrow1=$this->db->affected_rows();
			if($effrow1>0)
			{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);
			$effrow=$this->db->affected_rows();	
		if($effrow>0)
		{
			if($backtrackid<>0)
			{
			$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$backtrackid,'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
			$stageid=$this->db->insert_id();
			$datanewoid=array('neworderstageid'=>$stageid);
			$this->db->where('id',$qcinsertid);
			$this->db->update('qcremarks',$datanewoid);
			
					
			
			/** Add Tat **/
						$stageid=$stageid;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$backtrackid,0,$jobcardid,$orderid);
						
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$backtrackid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$backtrackid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$backtrackid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			
			
			
			}
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Order Moved to backtracked Process.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}else { 
		$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
		
			}else{
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to update please try again!</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			}
		
	}

	}


		
		function qcremarksoldolddd()
	{
		
	$orderstage=$this->uri->segment(3);
	$flowstage=$this->uri->segment(4);
	$orderid=$this->uri->segment(5);
	$productionflowid=$this->uri->segment(6);
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$newstage=[];
	$isfinalstep=0;
	$jobcardid=$this->input->post('jobcardid'.$orderstage);
	$qcstatus=$this->input->post('status'.$orderstage);

	if($qcstatus=='1')
	{
		
		/** Move to next stage **/
			$currentstage=$flowstage;
			$getset=$this->db->select('setorder,finalstep')->from('fms_flow')->where('flow_id',$currentstage)->where('production_flow_id',$productionflowid)->get();
			if($getset->num_rows()>0)
			{
				foreach($getset->result() as $ggetset);
				$currentsetorder=$ggetset->setorder;
				$isfinalstep=$ggetset->finalstep;
				echo $isfinalstep;exit;
				/** Get Next Order **/
				$getneworder=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id',$productionflowid)->where('setorder>',$currentsetorder)->limit(1)->order_by('setorder','ASC')->get();
				if($getneworder->num_rows()>0)
			{
				foreach($getneworder->result() as $getneworder1);
				/** Move Check **/
				$restymove=$this->db->select('moveto')->from('fms_flow')->where('flow_id',$flowstage)->get();
				foreach($restymove->result() as $restymove1)
				{
				if(($restymove1->moveto!=0)&&($restymove1->moveto!=''))
				{
				$moveflow=$restymove1->moveto;
				$movearr=explode(',',$moveflow);
				foreach($movearr as $movea)
				{
				$newstage[]=$movea;
				}
				
				} else
				{
					$newstage[]=$getneworder1->flow_id;
				} 
				}
				
			}else{
				
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			}
				/** End **/
	}else{
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			}
			
			//echo "<pre>"; print_r($newstage);exit;
			if(count($newstage)>0)
			{
				
			/** Detail Entery **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'qcstatus'=>$qcstatus,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->insert('qcremarks',$detailentry);
			$effrow1=$this->db->affected_rows();	
			
			/** End**/
			if($effrow1>0)
			{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);
			$effrow=$this->db->affected_rows();	
		if($effrow>0)
		{
			for($u=0;$u<count($newstage);$u++)
			{
			$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$newstage[$u])->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
			if($newstage[$u]<>0)
			{
			$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage[$u],'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
			}
			}
			}
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Order Moved to Next Process.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}else{
			
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
		
		}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
				
			}else{
				
				/** Check if its the last step **/
				
				if($isfinalstep!=0)
				{
					$newdata=array('complete'=>'1');
					$this->db->where('order_id',$orderid);
					$this->db->where('id',$jobcardid);
					$this->db->update('order_instruments',$newdata);
					$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}else
				{
				
				/** End **/
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}
			}

		
	}else{
		
		$backtrackid=$this->input->post('backtrack'.$orderstage);
		$reason=$this->input->post('reason'.$orderstage);
		/** Detail Entery **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'qcstatus'=>$qcstatus,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id,'rejectreason'=>$reason,'backtrackto'=>$backtrackid);
			$this->db->insert('qcremarks',$detailentry);
			$effrow1=$this->db->affected_rows();
			if($effrow1>0)
			{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);
			$effrow=$this->db->affected_rows();	
		if($effrow>0)
		{
			if($backtrackid<>0)
			{
			$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$backtrackid,'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
			}
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Order Moved to backtracked Process.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}else { 
		$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
		
			}else{
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to update please try again!</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			}
		
	}

	}

		
		function machining_process8()
	{
				$scheduler_data=array();
	$flowstage=$this->uri->segment(3);
	$productionflowid=$this->uri->segment(4);
	/*GET CURRENT PROCESS SORT ORDER*/
		$QRY = $this->db->select('total_days,tat,set_time, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();
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
	$currentprocessorder = $flowstage;
	$setorder=$fmsinformation->setorder;
	/*GET CURRENT PROCESS SORT ORDER*/
		$user_id =$this->session->userdata['logged_in']['user_id'];
		$fmsprocess=$productionflowid;

	if($fmsprocess<>0)
	{
	/** Get Process Name **/
	$process=$this->db->select('d.file_number,f.ordertype,f.fileno,a.jumpfabricationappl,a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow,d.fabrication,f.planstartsfrom')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('a.who_wedo',$user_id)->order_by('b.userstatus','ASC')->order_by('b.addedOn','DESC')->order_by('e.selforder','ASC')->get();
	/** End **/
	if($process->num_rows()>0)
	{
	// echo "<pre>"; print_r($process->result());exit;
	$i=1;
	foreach($process->result() as $proc)
	{

		$ordertype = $proc->ordertype;
			if($ordertype=='1'){
				
				$fileno=$proc->file_number;
			}else{
			
				$fileno=$proc->fileno;
			}

	if($proc->userstatus=='0')
	{
	$prestui="<span class='btn btn-danger btn-xs'>Update Pending</span>";
	//$action='<a href="javascript:;" onclick="markstagedone('."'".$proc->orderstageid."'".','."'".$proc->flowstage."'".','."'".$proc->originalorderid."'".','."'".$proc->jobcardid."'".')"><span class="btn btn-xs btn-warning">Mark as done</span></a>';
	$html='<button class="btn btn-xs btn-warning waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$proc->orderstageid.'">Mark as Done</button>';

	$html.= ' <div id="con-close-modal'.$proc->orderstageid.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<form action="'.page_url.'Orderstage/qcfinalremarks/'.$proc->orderstageid.'/'.$proc->flowstage.'/'.$proc->originalorderid.'/'.$fmsprocess.'" method="post" id="loginForm" enctype="multipart/form-data">
		
	  <div id="pageloader">
	   <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
	</div>
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
												<h4 class="modal-title">QC FINAL</h4>
											</div>
											<div class="modal-body">
										
												<div class="row">
												
												
													<div class="col-md-6">
														<div class="form-group">
															<label for="field-2" class="control-label">JOBCARD NUMBER<span style="color:red">*</span></label>
															
															<input type="text" style="width:250px" class="form-control" id="jobcard_no'.$proc->orderstageid.'" style="text-transform: uppercase;" name="jobcard_no'.$proc->orderstageid.'" placeholder="" value="'.$proc->job_card_no.'" readonly>
															<input type="hidden" name="jobcardid'.$proc->orderstageid.'" value="'.$proc->jobcardid.'">
														</div>
													</div>
													
													 <div class="col-md-6">
														<div class="form-group">
															<label for="field-2" class="control-label">INSTRUMENT NAME<span style="color:red">*</span></label>
															
															<input type="text" style="width:250px" class="form-control" id="jobcard_no'.$proc->orderstageid.'" style="text-transform: uppercase;" name="jobcard_no'.$proc->orderstageid.'" placeholder="" value="'.$proc->instruments_name.'" readonly>
															<input type="hidden" name="jobcardid'.$proc->orderstageid.'" value="'.$proc->jobcardid.'">
															<input type="hidden" name="instrumentid'.$proc->orderstageid.'" value="'.$proc->item_id.'">
														</div>
													</div>

													<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">QC STATUS<span style="color:red">*</span></label><br>

													<select class="form-control" id="status'.$proc->orderstageid.'" name="status'.$proc->orderstageid.'" required style="width:250px" onchange="getstockdetail('.$proc->orderstageid.');">
													<option value="">SELECT</option>
													<option value="1">PASS</option>
													<option value="0">REJECTED</option>
													</select>
													</div>
													</div>
													
													<div class="col-md-6 checklist'.$proc->orderstageid.'" style="display:none">
													<div class="form-group">
													<label for="field-2" class="control-label">Checklist Upload<span style="color:red">*</span></label><br>

													<input type="file" class="form-control" id="checklists'.$proc->orderstageid.'" name="checklists'.$proc->orderstageid.'" required style="width:250px">
													
													</div>
													</div>
												</div>
												
												<div class="row" id="prreq'.$proc->orderstageid.'" style="display:none; padding-top:20px">
												<div class="col-md-6">
														<div style="margin-bottom: 15px;">
															<label>BACKTRACK TO STAGE</label><span id="error_purpose" style="color:red;">*</span><br/>

															<select style="width:250px" class="form-control" id="backtrack'.$proc->orderstageid.'" name="backtrack'.$proc->orderstageid.'">
	<option value="">SELECT</option>';
	$restt=$this->db->select('flow_id,fms_flow')->from('fms_flow')->where('flow_id<',$flowstage)->where('production_flow_id',$productionflowid)->get();
	if($restt->num_rows()>0)
	{
		foreach($restt->result() as $restttt)
		{
	$html.= '<option value="'.$restttt->flow_id.'">'.$restttt->fms_flow.'</option>';
		}
	}
	$html.=  '</select>
														</div>
													</div>
													
													<div class="col-md-6">
														<div class="form-group">
															<label for="field-1" class="control-label">REASON FOR REJECTION</label><span id="error_purpose" style="color:red;">*</span><br/>

															<textarea style="width:250px" class="form-control" id="reason'.$proc->orderstageid.'" style="text-transform: uppercase;" name="reason'.$proc->orderstageid.'" placeholder="" value="" col="20"></textarea>

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


	$completedate="";
	$addedtimefinal="";
	}else{
	$compdate=date('d-M-Y H:i:s',strtotime($proc->stagecompletedate));
	$prestui="<span class='btn btn-success btn-xs'>Remarks Updated</span>";
	$action="Task completed on ".$compdate;
	$html="This Task has been done by You.";
	date_default_timezone_set("Asia/Kolkata");

	$completedate = date('d-M-Y', strtotime($proc->stagecompletedate));
	$completetime = date('H:i:s', strtotime($proc->stagecompletedate));
	$addedtimefinal = "<br>". date('g:i A', strtotime($completetime));
	}
	date_default_timezone_set("Asia/Kolkata");
	$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
	$addedtime = "<br>". date('g:i A', strtotime($time));

	$time1=date('H:i:s', strtotime($proc->plannedOn));
	$addedtime1 = "<br>". date('g:i A', strtotime($time1));
	$planneddate1=date('Y-m-d',strtotime($proc->plannedOn));

	$previousorder = $tatstage;
	
	

	$qcstatus="";
	$backtrack="";
	$qry = $this->db->select('a.jobcardid,a.qcstatus,a.rejectreason,a.backtrackto, a.checklist, b.flow_id, b.fms_flow')->from('qcfinalremarks a')->join('fms_flow b','a.backtrackto=b.flow_id','left')->where('a.jobcardid',$proc->jobcardid)->where('a.orderstageid',$proc->orderstageid)->order_by('a.id','DESC')->get();
	if($qry->num_rows()>0){
		foreach($qry->result() as $row);
		if($row->qcstatus=='1'){
			$qcstatus = "PASS</br></br><a href='".qc_checklist."".$row->checklist."' download>DOWNLOAD CHECKLIST</a>";
			$backtrack="";

	}else if($row->qcstatus=='0'){
		$qcstatus = "REJECTED<br>".$row->rejectreason;
		$backtrack = $row->fms_flow;
	}

		
		
	}

	/** Get User Department **/
				$udep=$this->db->select('b.department')->from('system_users a')->join('departments b','a.department_id=b.department_id')->where('a.user_id',$_SESSION['logged_in']['user_id'])->get(); 
				if($udep->num_rows()>0)
				{
					foreach($udep->result() as $depp);
					$depart=$depp->department;
					
				}else
				{
					$depart='';
				}

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
					
					
					/** Check if instrument requires fabrication **/
					/**$ryt=$this->db->select('b.fabrication')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->where('a.id',$proc->jobcardid)->get();
					if($ryt->num_rows()>0)
					{
						foreach($ryt->result() as $rtyeyte);
						$fabreq=$rtyeyte->fabrication;
						
					}else{ $fabreq=0 ;} **/
					
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
				/** End **/
				
		/** Check for merge fms intersection **/
				if($markapp==1)
				{
				$markapp=$this->fmsmodel->checkformergerpoint($flowstage,$proc->jobcardid,$proc->originalorderid,$proc->fabrication,$proc->jumpfabricationappl);
				}
				

				
				/** End **/
		
				if($markapp==1)
				{
					
	
	$timestamp=$this->getprevioustimestamp($tatstage,$proc->plannedOn,$proc->jobcardid,$flowstage,$proc->orderstageid);
		
	$tattime = DateTime::createFromFormat('d-M-Y g:i A', $timestamp);
	
	//$finaltattime=$tattime->format('g:i A');
	$finaltattime=date('g:i: A',strtotime($timestamp));
	$tatdate=$this->getupcomingtatdatenew($tatstage,$proc->plannedOn,$proc->jobcardid,$timestamp,$totaldaysslave,$proc->orderstageid,$day);

	if($proc->userstatus=='0')
	{
	$completedon="";
	}else
	{
	$completedon=date('d-M-Y g:i A',strtotime($proc->stagecompletedate));
	}
			
	$odtype=$this->getordertype($proc->originalorderid);
		
	$scheduler_data[] = array('sr_no'=>$i,
	'orderdate'=>$timestamp,
	'odtype'=>$odtype,
	'mname'=>strtoupper($proc->instruments_name),
	'jobcard'=>strtoupper($proc->job_card_no),
	'fileno'=>$fileno,
	'factory'=>strtoupper($depart),
	'tat'=>$tatdate,
	'actual'=>$completedon,
	'totdays'=>'',
	'qcstatus'=>$qcstatus,
	'backto'=>$backtrack,
	'status'=>$prestui,
	'action'=>$html);
	 
	 
	$i++;
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
	} 

		
		function machining_process8oldtempp()
	{
	$flowstage=$this->uri->segment(3);
	$productionflowid=$this->uri->segment(4);
	/*GET CURRENT PROCESS SORT ORDER*/
			$QRY = $this->db->select('total_days, tat, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();
			$res = $QRY->result();
			foreach($res as $fmsinformation);
			$totaldaysslave = $fmsinformation->total_days;
			$tatstage = $fmsinformation->tat;
			$currentprocessorder = $flowstage;
			$setorder=$fmsinformation->setorder;
	/*GET CURRENT PROCESS SORT ORDER*/

	$user_id =$this->session->userdata['logged_in']['user_id'];
	/**$isfms=$this->db->select('fms_process')->from('system_users')->where('user_id',$user_id)->get();
	if($isfms->num_rows()>0)
	{
	foreach($isfms->result() as $isfmsa);
	$fmsprocess=$isfmsa->fms_process;
	}else{ $fmsprocess=0; }**/

	$fmsprocess=$productionflowid;
	/** END **/
	if($fmsprocess<>0)
	{
	/** Get Process Name **/
	$process=$this->db->select('a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('a.who_wedo',$user_id)->order_by('b.userstatus','ASC')->order_by('b.addedOn','DESC')->get();
	/** End **/
	if($process->num_rows()>0)
	{
	// echo "<pre>"; print_r($process->result());exit;
	$i=1;
	foreach($process->result() as $proc)
	{
	if($proc->userstatus=='0')
	{
	$prestui="<span class='btn btn-danger btn-xs'>Update Pending</span>";
	//$action='<a href="javascript:;" onclick="markstagedone('."'".$proc->orderstageid."'".','."'".$proc->flowstage."'".','."'".$proc->originalorderid."'".','."'".$proc->jobcardid."'".')"><span class="btn btn-xs btn-warning">Mark as done</span></a>';
	$html='<button class="btn btn-xs btn-warning waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$proc->orderstageid.'">Mark as Done</button>';

	$html.= ' <div id="con-close-modal'.$proc->orderstageid.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<form action="'.page_url.'Orderstage/qcfinalremarks/'.$proc->orderstageid.'/'.$proc->flowstage.'/'.$proc->originalorderid.'/'.$fmsprocess.'" method="post" id="loginForm" enctype="multipart/form-data">
		
	  <div id="pageloader">
	   <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
	</div>
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
												<h4 class="modal-title">QC FINAL</h4>
											</div>
											<div class="modal-body">
										
												<div class="row">
												
												
													<div class="col-md-6">
														<div class="form-group">
															<label for="field-2" class="control-label">JOBCARD NUMBER<span style="color:red">*</span></label>
															
															<input type="text" style="width:250px" class="form-control" id="jobcard_no'.$proc->orderstageid.'" style="text-transform: uppercase;" name="jobcard_no'.$proc->orderstageid.'" placeholder="" value="'.$proc->job_card_no.'" readonly>
															<input type="hidden" name="jobcardid'.$proc->orderstageid.'" value="'.$proc->jobcardid.'">
														</div>
													</div>
													
													 <div class="col-md-6">
														<div class="form-group">
															<label for="field-2" class="control-label">INSTRUMENT NAME<span style="color:red">*</span></label>
															
															<input type="text" style="width:250px" class="form-control" id="jobcard_no'.$proc->orderstageid.'" style="text-transform: uppercase;" name="jobcard_no'.$proc->orderstageid.'" placeholder="" value="'.$proc->instruments_name.'" readonly>
															<input type="hidden" name="jobcardid'.$proc->orderstageid.'" value="'.$proc->jobcardid.'">
															<input type="hidden" name="instrumentid'.$proc->orderstageid.'" value="'.$proc->item_id.'">
														</div>
													</div>

													<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">QC STATUS<span style="color:red">*</span></label><br>

													<select class="form-control" id="status'.$proc->orderstageid.'" name="status'.$proc->orderstageid.'" required style="width:250px" onchange="getstockdetail('.$proc->orderstageid.');">
													<option value="">SELECT</option>
													<option value="1">PASS</option>
													<option value="0">REJECTED</option>
													</select>
													</div>
													</div>
													
													<div class="col-md-6 checklist'.$proc->orderstageid.'" style="display:none">
													<div class="form-group">
													<label for="field-2" class="control-label">Checklist Upload<span style="color:red">*</span></label><br>

													<input type="file" class="form-control" id="checklists'.$proc->orderstageid.'" name="checklists'.$proc->orderstageid.'" required style="width:250px">
													
													</div>
													</div>
												</div>
												
												<div class="row" id="prreq'.$proc->orderstageid.'" style="display:none; padding-top:20px">
												<div class="col-md-6">
														<div style="margin-bottom: 15px;">
															<label>BACKTRACK TO STAGE</label><span id="error_purpose" style="color:red;">*</span><br/>

															<select style="width:250px" class="form-control" id="backtrack'.$proc->orderstageid.'" name="backtrack'.$proc->orderstageid.'">
	<option value="">SELECT</option>';
	$restt=$this->db->select('flow_id,fms_flow')->from('fms_flow')->where('flow_id<',$flowstage)->where('production_flow_id',$productionflowid)->get();
	if($restt->num_rows()>0)
	{
		foreach($restt->result() as $restttt)
		{
	$html.= '<option value="'.$restttt->flow_id.'">'.$restttt->fms_flow.'</option>';
		}
	}
	$html.=  '</select>
														</div>
													</div>
													
													<div class="col-md-6">
														<div class="form-group">
															<label for="field-1" class="control-label">REASON FOR REJECTION</label><span id="error_purpose" style="color:red;">*</span><br/>

															<textarea style="width:250px" class="form-control" id="reason'.$proc->orderstageid.'" style="text-transform: uppercase;" name="reason'.$proc->orderstageid.'" placeholder="" value="" col="20"></textarea>

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


	$completedate="";
	$addedtimefinal="";
	}else{
	$compdate=date('d-M-Y H:i:s',strtotime($proc->stagecompletedate));
	$prestui="<span class='btn btn-success btn-xs'>Remarks Updated</span>";
	$action="Task completed on ".$compdate;
	$html="This Task has been done by You.";
	date_default_timezone_set("Asia/Kolkata");

	$completedate = date('d-M-Y', strtotime($proc->stagecompletedate));
	$completetime = date('H:i:s', strtotime($proc->stagecompletedate));
	$addedtimefinal = "<br>". date('g:i A', strtotime($completetime));
	}
	date_default_timezone_set("Asia/Kolkata");
	$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
	$addedtime = "<br>". date('g:i A', strtotime($time));

	$time1=date('H:i:s', strtotime($proc->plannedOn));
	$addedtime1 = "<br>". date('g:i A', strtotime($time1));
	$planneddate1=date('Y-m-d',strtotime($proc->plannedOn));

	/*GET DATE OF PRIVIOUS STEPS*/
						$previousorder = $tatstage;
						
							if($previousorder<>0){
						$abc = $this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$proc->jobcardid)->order_by('id','DESC')->limit(1)->get();
							if($abc->num_rows()>0){
						foreach($abc->result() as $pastinfo);
						$complitiontime = $pastinfo->addedOn;
							
								$lasttaskcompletedtime = "<br>". date('g:i A', strtotime($complitiontime));

								
							}else{
							$complitiontime = $proc->added_on;
									$lasttaskcompletedtime = "<br>". date('g:i A', strtotime($complitiontime));

							}
						}else{
						$complitiontime = $proc->plannedOn;	
								$lasttaskcompletedtime = "<br>". date('g:i A', strtotime($complitiontime));

						}
						
						/*PREVIOUS COMPLITION DATE*/
						
						$checkpreviousdate = date('Y-m-d', strtotime($complitiontime));
						$previouscomplitiondate = date('d-M-Y', strtotime($complitiontime));
						$previouscomplitiontime = date('H:i:s', strtotime($proc->added_on));
						$previouscomplitionfinaltime = "<br>". date('g:i A', strtotime($previouscomplitiontime));
				
						/*GET DATE OF PRIVIOUS STEPS*/
						$date11 = new DateTime($previouscomplitiondate); 
						$date22 = new DateTime($completedate); 
						$interval1 = $date11->diff($date22); 
						$ttldays = $interval1->d; 
						if($ttldays=='0'){
						$totalday = "1";
						}else{
						$totalday = $ttldays;
				}
				
				/*TAT Date*/
				$lastcompdate = date('Y-m-d', strtotime($checkpreviousdate));
				$TATDATE=date('d-M-Y', strtotime($lastcompdate."+".$totaldaysslave." days"));
				
				/*TAT Date*/
		$QRY = $this->db->select('total_days, tat, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();

	if($this->uri->segment(3)=='1'){
		date_default_timezone_set("Asia/Kolkata");
	$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
	$addedtime = "<br>". date('g:i A', strtotime($time));
		}else{
		$previousorder = $setorder-1; 
		$QRY = $this->db->select('total_days, tat,setorder, flow_id ')->from('fms_flow')->where('setorder',$previousorder)->get();
		if($QRY->num_rows()>0){
		foreach($QRY->result() as $paststepdate);
			$flowid = $paststepdate->flow_id;
		$QRY1 = $this->db->select('jobcardid, flowstage,addedOn')->from('order_stage')->where('jobcardid',$proc->jobcardid)->where('flowstage',$flowid)->get();	
		if($QRY1->num_rows()>0){
		foreach($QRY1->result() as $timestamp){
		$addeddate1 = date('Y-m-d', strtotime($timestamp->addedOn));
	$time = date('H:i:s', strtotime($timestamp->addedOn));
	$addedtime = "<br>". date('g:i A', strtotime($time));
		}
		}	
		}else{
		$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
		}
		}

	$qcstatus="";
	$backtrack="";
	$qry = $this->db->select('a.jobcardid,a.qcstatus,a.rejectreason,a.backtrackto, a.checklist, b.flow_id, b.fms_flow')->from('qcfinalremarks a')->join('fms_flow b','a.backtrackto=b.flow_id','left')->where('a.jobcardid',$proc->jobcardid)->where('a.orderstageid',$proc->orderstageid)->order_by('a.id','DESC')->get();
	if($qry->num_rows()>0){
		foreach($qry->result() as $row);
		if($row->qcstatus=='1'){
			$qcstatus = "PASS</br></br><a href='".qc_checklist."".$row->checklist."' download>DOWNLOAD CHECKLIST</a>";
			$backtrack="";

	}else if($row->qcstatus=='0'){
		$qcstatus = "REJECTED<br>".$row->rejectreason;
		$backtrack = $row->fms_flow;
	}

		
		
	}

	/** Get User Department **/
				$udep=$this->db->select('b.department')->from('system_users a')->join('departments b','a.department_id=b.department_id')->where('a.user_id',$_SESSION['logged_in']['user_id'])->get(); 
				if($udep->num_rows()>0)
				{
					foreach($udep->result() as $depp);
					$depart=$depp->department;
					
				}else
				{
					$depart='';
				}

	/** Check for dependency **/
				
				$checkdepend=$this->db->select('dependency')->from('fms_flow')->where('flow_id',$flowstage)->where('dependency','1')->get();
				$depend=$checkdepend->num_rows();
				//echo $depend;exit;
				if($depend==1)
				{
					
					$restt=$this->db->select('dependentflowid')->from('flowdependency')->where('flowid',$flowstage)->get();
					if($restt->num_rows()>0)
					{
						$dependcount=$restt->num_rows();
						$donarr=array();
						$donarr[]=0;
						//echo "<pre>";print_r($restt->result());exit;
						foreach($restt->result() as $resttyu)
						{
							$isdone=$this->db->select('id,userstatus')->from('order_stage')->where('flowstage',$resttyu->dependentflowid)->where('jobcardid',$proc->jobcardid)->where('orderid',$proc->originalorderid)->order_by('id','DESC')->limit(1)->get();
							if($isdone->num_rows()>0)
							{
								foreach($isdone->result() as $isdone11);
								$donarr[]=$isdone11->userstatus;
								
							}else
							{
								$markapp=1;
							}
							
						}
						//echo $dependcount.'<br>'."<pre>"; print_r($donarr);exit;
						if($dependcount==array_sum($donarr))
						{
							$markapp=1;
						}else{
							$markapp=0;
						}
						
					}else{
						$markapp=1;
					}
					
				}else{
					
					$markapp=1;
				}
				/** End **/
				
				/** New Timestamp query **/
				$previousorder=$tatstage;
				if($previousorder==0)
				{
					$previouscomtime=$proc->plannedOn;
					$previouscomdate=date('d-M-Y',strtotime($previouscomtime))."<br>";
					$previouscomtimeonly=date('g:i A',strtotime($previouscomtime));
					
				}else
				{
					//echo $previousorder;exit;
					$oldtime1=$this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$proc->jobcardid)->order_by('id','DESC')->limit(1)->get();
							if($oldtime1->num_rows()>0){
								foreach($oldtime1->result() as $pastinfo);
								$previouscomtime=$pastinfo->addedOn;
								$previouscomdate=date('d-M-Y',strtotime($pastinfo->addedOn))."<br>";
								$previouscomtimeonly=date('g:i A',strtotime($pastinfo->addedOn));
							}else
							{
								$previouscomtime=$proc->plannedOn;
					$previouscomdate=date('d-M-Y',strtotime($previouscomtime))."<br>";
					$previouscomtimeonly=date('g:i A',strtotime($previouscomtime));
							}
				}

		
				
				/** End **/
		
				if($markapp==1)
				{
					
	/** Holiday Check**/
				
					$TATDATEFORHOLIDAYCHECK=date('Y-m-d', strtotime($TATDATE));
					$timestampforcheck=date('Y-m-d',strtotime($previouscomtime));
					$date_from=strtotime($timestampforcheck);
					$date_to=strtotime($TATDATEFORHOLIDAYCHECK);
					$betweendates=array();
					for ($g=$date_from; $g<=$date_to; $g+=86400) {  
						$betweendates[]= date("Y-m-d", $g);  
					} 
				
				//echo "<pre>"; print_r($betweendates);
					if(count($betweendates)>0)
				{
					
					$alldates = "'" . implode ( "', '", $betweendates ) . "'";
					
					$ifholiday=$this->db->select('holiday_id')->from('prestogroup_holidays')->where_in('holiday_date',$alldates,false)->get();
					if($ifholiday->num_rows()>0)
					{
			
						//echo "hi".$proc->job_card_no;
						$TATDATEFORHOLIDAYCHECK=date('Y-m-d', strtotime($TATDATEFORHOLIDAYCHECK."+".$ifholiday->num_rows()." days"));
						//echo $TATDATEFORHOLIDAYCHECK;
						
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
				//$TATDATE= date('d-M-Y', strtotime($nextdate."+1 days"));
				$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();
				if($query->num_rows()>0){
					
					foreach($query->result() as $afterholiday);
					$aftrholidaydate = $afterholiday->holiday_date;
					$TATDATE= date('d-M-Y', strtotime($aftrholidaydate."+1 days"));
					
				}else {
				$TATDATE= date('d-M-Y', strtotime($nextdate));
				}
			}else{
			$TATDATE=date('d-M-Y', strtotime($TATDATEFORHOLIDAYCHECK));
			}
					
				/** End **/
					
	$scheduler_data[] = array('sr_no'=>$i,
	'orderdate'=>$previouscomdate.$previouscomtimeonly,
	'mname'=>strtoupper($proc->instruments_name),
	'jobcard'=>strtoupper($proc->internal_order_no).'/'.strtoupper($proc->job_card_no),
	'factory'=>strtoupper($depart),
	'planned'=>date('d-M-Y',strtotime($proc->plannedOn)).$addedtime1,
	'tat'=>$TATDATE."<br>".$previouscomtimeonly,
	'actual'=>$completedate.$addedtimefinal,
	'totdays'=>$totalday,
	'qcstatus'=>$qcstatus,
	'backto'=>$backtrack,
	'status'=>$prestui,
	'action'=>$html);
	 
	 
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

	}
	} 

		
		
			function qcfinalremarks()
	{
		
	$orderstage=$this->uri->segment(3);
	$flowstage=$this->uri->segment(4);
	$orderid=$this->uri->segment(5);
	$selforder=$this->fmsmodel->getselforder($orderid);
	$productionflowid=$this->uri->segment(6);
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$newstage=[];
	$isfinalstep=0;
	$jobcardid=$this->input->post('jobcardid'.$orderstage);
	$qcstatus=$this->input->post('status'.$orderstage);
	$instrumentid=$this->input->post('instrumentid'.$orderstage);
	$instrumentname=$this->input->post('jobcard_no'.$orderstage);

$currentstage=$flowstage;
			$getset=$this->db->select('setorder,finalstep,total_days,set_time')->from('fms_flow')->where('flow_id',$currentstage)->where('production_flow_id',$productionflowid)->get();
			
			foreach($getset->result() as $ggetset);
			$currentsetorder=$ggetset->setorder;
			$isfinalstep=$ggetset->finalstep;
			$tatday=$ggetset->total_days;
			$tathours=$ggetset->set_time;
			
	if($qcstatus=='1')
	{
		
		/** Move to next stage **/
			
			
		
			/** Get Next Order **/
				$getneworder=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id',$productionflowid)->where('setorder>',$currentsetorder)->limit(1)->order_by('setorder','ASC')->get();
					if($getneworder->num_rows()>0)
					{
					foreach($getneworder->result() as $getneworder1);
					/** Move Check **/
					$restymove=$this->db->select('moveto')->from('fms_flow')->where('flow_id',$flowstage)->get();
					foreach($restymove->result() as $restymove1)
					{
					if(($restymove1->moveto!=0)&&($restymove1->moveto!=''))
					{
					$moveflow=$restymove1->moveto;
					$movearr=explode(',',$moveflow);
					foreach($movearr as $movea)
					{
					$newstage[]=$movea;
					}

					} else
					{
					$newstage[]=$getneworder1->flow_id;
					} 
					}
					}
				
				

				$files=$_FILES['checklists'.$orderstage]['name'];
				$ex=explode('.',$files);
				$ext=end($ex);
				$newname=$instrumentname.'_'.rand(10000,99999).'.'.$ext;
				move_uploaded_file($_FILES['checklists'.$orderstage]['tmp_name'],$_SERVER['DOCUMENT_ROOT'].'/image_bank/qcchecklist/'.$newname);
				
			/** Detail Entery **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'qcstatus'=>$qcstatus,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id,'checklist'=>$newname);
			$this->db->insert('qcfinalremarks',$detailentry);
			$effrow1=$this->db->affected_rows();	
			
			/** End**/
			if($effrow1>0)
			{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('jobcardid',$jobcardid);
			$this->db->update('order_stage',$data);
			$effrow=$this->db->affected_rows();	
			
			if(count($newstage)>0)
		{
			for($u=0;$u<count($newstage);$u++)
			{
			$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$newstage[$u])->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
			if($newstage[$u]<>0)
			{
			$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage[$u],'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
			/** Add Tat **/
						$stageid=$this->db->insert_id();
						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$newstage[$u]);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			}
			}else
			{
				foreach($checkodd->result() as $checkodd1);


							/** Add Tat **/
							$stageid=$checkodd1->id;
							$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);

							if($settatdate<>'')
							{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
							if($checkfmstat->num_rows()==0)
							{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
							$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
							$this->db->insert('fmstatdate',$settat);
							}else
							{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));

							$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
							$this->db->where('flowstage',$newstage[$u]);
							$this->db->where('jobcardid',$jobcardid);
							$this->db->update('fmstatdate',$settat);
							}
							}
							/** END **/
				
				
			}
			}
			
				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Order Moved to Next Process.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}else{
			
			
			if($isfinalstep!=0)
				{
					
						/** Check if Merge is available **/
				  $merger=$this->db->select('id,mergewith,flowid')->from('fmsmerge')->where('productionflow',$productionflowid)->get();
					if($merger->num_rows()>0)
					{
						foreach($merger->result() as $mergerdata);
						$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$mergerdata->flowid)->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
						$orderstmergedata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$mergerdata->flowid,'userstatus'=>'0','remarks'=>'','addedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('order_stage',$orderstmergedata); 
				        $odm=$this->db->insert_id();
						/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			}else
			{
				foreach($checkodd->result() as $prevorderstage);
				$odm=$prevorderstage->id;
				
				/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
				
				
			}
					
						if($odm<>0)
						{
							$mergehistdata=array('orderstageid'=>$odm,'oldproductionflow'=>$productionflowid,'jobcardid'=>$jobcardid,'newproductionflow'=>$mergerdata->mergewith,'oldfmsflow'=>$flowstage,'newfmsflow'=>$mergerdata->flowid,'addedOn'=>date('Y-m-d H:i:s'));
							$this->db->insert('fmsmergehistory',$mergehistdata);
							
							$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Merged.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
							
							}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to merge flow.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
					}else {
						
						
					$newdata=array('complete'=>'1');
					$this->db->where('order_id',$orderid);
					$this->db->where('id',$jobcardid);
					$this->db->update('order_instruments',$newdata);
					
					if($selforder=='1')
					{
					$this->fmsmodel->addstock($jobcardid);
					}

					/** IF THIS IS SALESFORCE ORDER **/
					$this->checkifthisissforderandlastonetogetcompleted($orderid);

					/** END **/

$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);					
					}
					/** Merge **/
					$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}else
				{
				
				/** End **/
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}
			
			
		}
			
		
		
		}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
				
			

		
	}else{
		
		$backtrackid=$this->input->post('backtrack'.$orderstage);
		
		$reason=$this->input->post('reason'.$orderstage);
		/** Detail Entery **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'qcstatus'=>$qcstatus,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id,'rejectreason'=>$reason,'backtrackto'=>$backtrackid);
			$this->db->insert('qcfinalremarks',$detailentry);
			$qcinsertid=$this->db->insert_id();
			$effrow1=$this->db->affected_rows();
			if($effrow1>0)
			{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);
			$effrow=$this->db->affected_rows();	
		if($effrow>0)
		{
			
			$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$backtrackid,'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
			$stageid=$this->db->insert_id();
			$datanewoid=array('neworderstageid'=>$stageid);
			$this->db->where('id',$qcinsertid);
			$this->db->update('qcfinalremarks',$datanewoid);
			/** Add Tat **/
						$stageid=$stageid;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$backtrackid,0,$jobcardid,$orderid);
						
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$backtrackid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$backtrackid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$backtrackid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">ORDER BACKTRACKED.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}else { 
		$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
		
			}else{
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to update please try again!</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			}
		
	}




	}
	

    	function qcfinalremarksoldbeforetatinfmsttatdate()
    	{
    		
    	$orderstage=$this->uri->segment(3);
    	$flowstage=$this->uri->segment(4);
    	$orderid=$this->uri->segment(5);
    	$selforder=$this->fmsmodel->getselforder($orderid);
    	$productionflowid=$this->uri->segment(6);
    	$user_id =$this->session->userdata['logged_in']['user_id'];
    	$newstage=[];
    	$isfinalstep=0;
    	$jobcardid=$this->input->post('jobcardid'.$orderstage);
    	$qcstatus=$this->input->post('status'.$orderstage);
    	$instrumentid=$this->input->post('instrumentid'.$orderstage);
    	$instrumentname=$this->input->post('jobcard_no'.$orderstage);
    
    	if($qcstatus=='1')
    	{
    		
    		/** Move to next stage **/
    			$currentstage=$flowstage;
    			$getset=$this->db->select('setorder,finalstep')->from('fms_flow')->where('flow_id',$currentstage)->where('production_flow_id',$productionflowid)->get();
    			
    			foreach($getset->result() as $ggetset);
    			$currentsetorder=$ggetset->setorder;
    			$isfinalstep=$ggetset->finalstep;
    		
    			/** Get Next Order **/
    				$getneworder=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id',$productionflowid)->where('setorder>',$currentsetorder)->limit(1)->order_by('setorder','ASC')->get();
    					if($getneworder->num_rows()>0)
    					{
    					foreach($getneworder->result() as $getneworder1);
    					/** Move Check **/
    					$restymove=$this->db->select('moveto')->from('fms_flow')->where('flow_id',$flowstage)->get();
    					foreach($restymove->result() as $restymove1)
    					{
    					if(($restymove1->moveto!=0)&&($restymove1->moveto!=''))
    					{
    					$moveflow=$restymove1->moveto;
    					$movearr=explode(',',$moveflow);
    					foreach($movearr as $movea)
    					{
    					$newstage[]=$movea;
    					}
    
    					} else
    					{
    					$newstage[]=$getneworder1->flow_id;
    					} 
    					}
    					}
    				
    				
    
    				$files=$_FILES['checklists'.$orderstage]['name'];
    				$ex=explode('.',$files);
    				$ext=end($ex);
    				$newname=$instrumentname.'_'.rand(10000,99999).'.'.$ext;
    				move_uploaded_file($_FILES['checklists'.$orderstage]['tmp_name'],$_SERVER['DOCUMENT_ROOT'].'/image_bank/qcchecklist/'.$newname);
    				
    			/** Detail Entery **/
    			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'qcstatus'=>$qcstatus,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id,'checklist'=>$newname);
    			$this->db->insert('qcfinalremarks',$detailentry);
    			$effrow1=$this->db->affected_rows();	
    			
    			/** End**/
    			if($effrow1>0)
    			{
    			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
    			$this->db->where('id',$orderstage);
    			$this->db->where('jobcardid',$jobcardid);
    			$this->db->update('order_stage',$data);
    			$effrow=$this->db->affected_rows();	
    			
    			if(count($newstage)>0)
    		{
    			for($u=0;$u<count($newstage);$u++)
    			{
    			$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$newstage[$u])->where('userstatus','0')->get();
    			if($checkodd->num_rows()==0)
    			{
    			if($newstage[$u]<>0)
    			{
    			$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage[$u],'userstatus'=>'0');
    			$this->db->insert('order_stage',$newdata);
    			/** Add Tat **/
    						$stageid=$this->db->insert_id();
    						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
    						if($settatdate<>'')
    						{
    							/** Check if exist **/
    							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
    						if($checkfmstat->num_rows()==0)
    						{
    						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
    						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
    						$this->db->insert('fmstatdate',$settat);
    						}else
    						{
    							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
    						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'));
    						$this->db->where('flowstage',$newstage[$u]);
    						$this->db->where('jobcardid',$jobcardid);
    						$this->db->update('fmstatdate',$settat);
    						}
    						}
    						/** END **/
    			}
    			}else
    			{
    				foreach($checkodd->result() as $checkodd1);
    
    
    							/** Add Tat **/
    							$stageid=$checkodd1->id;
    							$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
    
    							if($settatdate<>'')
    							{
    							/** Check if exist **/
    							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
    							if($checkfmstat->num_rows()==0)
    							{
    							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
    							$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
    							$this->db->insert('fmstatdate',$settat);
    							}else
    							{
    							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
    
    							$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'));
    							$this->db->where('flowstage',$newstage[$u]);
    							$this->db->where('jobcardid',$jobcardid);
    							$this->db->update('fmstatdate',$settat);
    							}
    							}
    							/** END **/
    				
    				
    			}
    			}
    			
    				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Order Moved to Next Process.</span></div>');
    				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
    		}else{
    			
    			
    			if($isfinalstep!=0)
    				{
    					
    						/** Check if Merge is available **/
    				  $merger=$this->db->select('id,mergewith,flowid')->from('fmsmerge')->where('productionflow',$productionflowid)->get();
    					if($merger->num_rows()>0)
    					{
    						foreach($merger->result() as $mergerdata);
    						$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$mergerdata->flowid)->where('userstatus','0')->get();
    			if($checkodd->num_rows()==0)
    			{
    						$orderstmergedata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$mergerdata->flowid,'userstatus'=>'0','remarks'=>'','addedOn'=>date('Y-m-d H:i:s'));
    						$this->db->insert('order_stage',$orderstmergedata); 
    				        $odm=$this->db->insert_id();
    						/** Add Tat **/
    						$stageid=$odm;
    						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
    						if($settatdate<>'')
    						{
    							/** Check if exist **/
    							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
    						if($checkfmstat->num_rows()==0)
    						{
    						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
    						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
    						$this->db->insert('fmstatdate',$settat);
    						}else
    						{
    							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
    						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'));
    						$this->db->where('flowstage',$mergerdata->flowid);
    						$this->db->where('jobcardid',$jobcardid);
    						$this->db->update('fmstatdate',$settat);
    						}
    						}
    						/** END **/
    			}else
    			{
    				foreach($checkodd->result() as $prevorderstage);
    				$odm=$prevorderstage->id;
    				
    				/** Add Tat **/
    						$stageid=$odm;
    						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
    						if($settatdate<>'')
    						{
    							/** Check if exist **/
    							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
    						if($checkfmstat->num_rows()==0)
    						{
    						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
    						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
    						$this->db->insert('fmstatdate',$settat);
    						}else
    						{
    							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
    						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'));
    						$this->db->where('flowstage',$mergerdata->flowid);
    						$this->db->where('jobcardid',$jobcardid);
    						$this->db->update('fmstatdate',$settat);
    						}
    						}
    						/** END **/
    				
    				
    			}
    					
    						if($odm<>0)
    						{
    							$mergehistdata=array('orderstageid'=>$odm,'oldproductionflow'=>$productionflowid,'jobcardid'=>$jobcardid,'newproductionflow'=>$mergerdata->mergewith,'oldfmsflow'=>$flowstage,'newfmsflow'=>$mergerdata->flowid,'addedOn'=>date('Y-m-d H:i:s'));
    							$this->db->insert('fmsmergehistory',$mergehistdata);
    							
    							$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Merged.</span></div>');
    							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
    							
    							}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to merge flow.</span></div>');
    							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
    					}else {
    						
    						
    					$newdata=array('complete'=>'1');
    					$this->db->where('order_id',$orderid);
    					$this->db->where('id',$jobcardid);
    					$this->db->update('order_instruments',$newdata);
    					
    					if($selforder=='1')
    					{
    					$this->fmsmodel->addstock($jobcardid);
    					}
    
    $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
    			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);					
    					}
    					/** Merge **/
    					$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
    			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
    				}else
    				{
    				
    				/** End **/
    				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
    			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
    				}
    			
    			
    		}
    			
    		
    		
    		}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
    		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
    				
    			
    
    		
    	}else{
    		
    		$backtrackid=$this->input->post('backtrack'.$orderstage);
    		
    		$reason=$this->input->post('reason'.$orderstage);
    		/** Detail Entery **/
    			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'qcstatus'=>$qcstatus,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id,'rejectreason'=>$reason,'backtrackto'=>$backtrackid);
    			$this->db->insert('qcfinalremarks',$detailentry);
    			$qcinsertid=$this->db->insert_id();
    			$effrow1=$this->db->affected_rows();
    			if($effrow1>0)
    			{
    			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
    			$this->db->where('id',$orderstage);
    			$this->db->where('orderid',$orderid);
    			$this->db->update('order_stage',$data);
    			$effrow=$this->db->affected_rows();	
    		if($effrow>0)
    		{
    			
    			$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$backtrackid,'userstatus'=>'0');
    			$this->db->insert('order_stage',$newdata);
    			$stageid=$this->db->insert_id();
    			$datanewoid=array('neworderstageid'=>$stageid);
    			$this->db->where('id',$qcinsertid);
    			$this->db->update('qcfinalremarks',$datanewoid);
    			/** Add Tat **/
    						$stageid=$stageid;
    						$settatdate=$this->fmsmodel->gettatformis($stageid,$backtrackid,0,$jobcardid,$orderid);
    						
    						if($settatdate<>'')
    						{
    							/** Check if exist **/
    							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$backtrackid)->where('jobcardid',$jobcardid)->get();
    						if($checkfmstat->num_rows()==0)
    						{
    						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
    						$settat=array('orderstageid'=>$stageid,'flowstage'=>$backtrackid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
    						$this->db->insert('fmstatdate',$settat);
    						}else
    						{
    							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
    						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'));
    						$this->db->where('flowstage',$backtrackid);
    						$this->db->where('jobcardid',$jobcardid);
    						$this->db->update('fmstatdate',$settat);
    						}
    						}
    						/** END **/
    			
    			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">ORDER BACKTRACKED.</span></div>');
    				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
    		}else { 
    		$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
    		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
    		
    			}else{
    				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to update please try again!</span></div>');
    				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
    			}
    		
    	}
    
    
    
    
    	}
    	
	function qcfinalremarksoldiee28jan20202()
	{
		
	$orderstage=$this->uri->segment(3);
	$flowstage=$this->uri->segment(4);
	$orderid=$this->uri->segment(5);
	$productionflowid=$this->uri->segment(6);
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$newstage=[];
	$jobcardid=$this->input->post('jobcardid'.$orderstage);
	$qcstatus=$this->input->post('status'.$orderstage);
	$instrumentid=$this->input->post('instrumentid'.$orderstage);
	$instrumentname=$this->input->post('jobcard_no'.$orderstage);

	if($qcstatus=='1')
	{
		
		/** Move to next stage **/
			$currentstage=$flowstage;
			$getset=$this->db->select('setorder')->from('fms_flow')->where('flow_id',$currentstage)->get();
			
				
				$files=$_FILES['checklists'.$orderstage]['name'];
				$ex=explode('.',$files);
				$ext=end($ex);
				$newname=$instrumentname.'_'.rand(10000,99999).'.'.$ext;
			//	echo $newname;exit;
				move_uploaded_file($_FILES['checklists'.$orderstage]['tmp_name'],$_SERVER['DOCUMENT_ROOT'].'/image_bank/qcchecklist/'.$newname);
				
			/** Detail Entery **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'qcstatus'=>$qcstatus,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id,'checklist'=>$newname);
			$this->db->insert('qcfinalremarks',$detailentry);
			$effrow1=$this->db->affected_rows();	
			
			/** End**/
			if($effrow1>0)
			{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('jobcardid',$jobcardid);
			$this->db->update('order_stage',$data);
			$effrow=$this->db->affected_rows();	
		if($effrow>0)
		{
			
			$newdata=array('complete'=>'1');
			$this->db->where('order_id',$orderid);
			$this->db->where('id',$jobcardid);
			$this->db->update('order_instruments',$newdata);
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Success! Order has been completed.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}else{
			
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
		
		}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
				
			

		
	}else{
		
		$backtrackid=$this->input->post('backtrack'.$orderstage);
		$reason=$this->input->post('reason'.$orderstage);
		/** Detail Entery **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'qcstatus'=>$qcstatus,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id,'rejectreason'=>$reason,'backtrackto'=>$backtrackid);
			$this->db->insert('qcfinalremarks',$detailentry);
			$effrow1=$this->db->affected_rows();
			if($effrow1>0)
			{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);
			$effrow=$this->db->affected_rows();	
		if($effrow>0)
		{
			
			$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$backtrackid,'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
			
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">ORDER BACKTRACKED.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}else { 
		$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
		
			}else{
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to update please try again!</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			}
		
	}

	}

		
		function machining_material_out_paintprocess()
	{
				$scheduler_data=array();
	$flowstage=$this->uri->segment(3);
	$productionflowid=$this->uri->segment(4);
	/*GET CURRENT PROCESS SORT ORDER*/
			$QRY = $this->db->select('total_days,tat,set_time, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();
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
			$currentprocessorder = $flowstage;
		$setorder=$fmsinformation->setorder;
	/*GET CURRENT PROCESS SORT ORDER*/

	$user_id =$this->session->userdata['logged_in']['user_id'];
	$fmsprocess=$productionflowid;
	if($fmsprocess<>0)
	{
	/** Get Process Name **/
	$process=$this->db->select('d.file_number,f.ordertype,f.fileno,a.jumpfabricationappl,a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow,d.fabrication,f.planstartsfrom')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('a.who_wedo',$user_id)->order_by('b.userstatus','ASC')->order_by('b.addedOn','DESC')->get();
	/** End **/
	if($process->num_rows()>0)
	{
	// echo "<pre>"; print_r($process->result());exit;
	$i=1;
	foreach($process->result() as $proc)
	{
		$ordertype = $proc->ordertype;
			if($ordertype=='1'){
				
				$fileno=$proc->file_number;
			}else{
			
				$fileno=$proc->fileno;
			}

	if($proc->userstatus=='0')
	{
	$prestui="<span class='btn btn-danger btn-xs'>Update Pending</span>";
	//$action='<a href="javascript:;" onclick="markstagedone('."'".$proc->orderstageid."'".','."'".$proc->flowstage."'".','."'".$proc->originalorderid."'".','."'".$proc->jobcardid."'".')"><span class="btn btn-xs btn-warning">Mark as done</span></a>';
	$html='<button class="btn btn-xs btn-warning waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$proc->orderstageid.'">Mark as Done</button>';

	$html.= ' <div id="con-close-modal'.$proc->orderstageid.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<form action="'.page_url.'Orderstage/materialpaintout/'.$proc->orderstageid.'/'.$proc->flowstage.'/'.$proc->originalorderid.'/'.$fmsprocess.'" method="post" id="loginForm" enctype="multipart/form-data">
		
	  <div id="pageloader">
	   <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
	</div>
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
												<h4 class="modal-title">MATERIAL OUT (PAINT)</h4>
											</div>
											<div class="modal-body">
										
												<div class="row">
												
												
													<div class="col-md-6">
														<div class="form-group">
															<label for="field-2" class="control-label">JOBCARD NUMBER<span style="color:red">*</span></label>
															
															<input type="text" style="width:250px" class="form-control" id="jobcard_no'.$proc->orderstageid.'" style="text-transform: uppercase;" name="jobcard_no'.$proc->orderstageid.'" placeholder="" value="'.$proc->job_card_no.'" readonly>
															<input type="hidden" name="jobcardid'.$proc->orderstageid.'" value="'.$proc->jobcardid.'">
														</div>
													</div>
													
													 <!--<div class="col-md-6">
														<div class="form-group">
															<label for="field-2" class="control-label">INSTRUMENT NAME<span style="color:red">*</span></label>
															
															<input type="text" style="width:250px" class="form-control" id="jobcard_no'.$proc->orderstageid.'" style="text-transform: uppercase;" name="jobcard_no'.$proc->orderstageid.'" placeholder="" value="'.$proc->instruments_name.'" readonly>
															<input type="hidden" name="jobcardid'.$proc->orderstageid.'" value="'.$proc->jobcardid.'">
															<input type="hidden" name="instrumentid'.$proc->orderstageid.'" value="'.$proc->item_id.'">
														</div>
													</div>-->

													<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">CHALLAN NO.<span style="color:red">*</span></label><br>

													<input type="text" class="form-control" id="challanno'.$proc->orderstageid.'" name="challanno'.$proc->orderstageid.'" required style="width:250px">
													</div>
													</div>
													
													
													<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">WEIGHT<span style="color:red">*</span></label><br>

													<input type="text" class="form-control" id="weight'.$proc->orderstageid.'" name="weight'.$proc->orderstageid.'" required style="width:250px" placeholder="IN Kg">
													</div>
													</div>
													
													
													<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">PCS<span style="color:red">*</span></label><br>

													<input type="number" class="form-control" id="pcs'.$proc->orderstageid.'" name="pcs'.$proc->orderstageid.'" required style="width:250px" min="0">
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

	$completedate = "";
	$addedtimefinal = "";


	}else{
	$compdate=date('d-M-Y H:i:s',strtotime($proc->stagecompletedate));
	$completedate = date('d-M-Y', strtotime($proc->stagecompletedate));
	$completetime = date('H:i:s', strtotime($proc->stagecompletedate));
	$addedtimefinal = "<br>". date('g:i A', strtotime($completetime));


	$prestui="<span class='btn btn-success btn-xs'>Remarks Updated</span>";
	$action="Task completed on ".$compdate;
	$html="This Task has been completed by You.";
	}
	

$previousorder = $tatstage;

	$QRY = $this->db->select('jobcardid, challanno, weight, pcs')->from('material_paint_out')->where('jobcardid',$proc->jobcardid)->get();
	$res = $QRY->result();
	if($QRY->num_rows()>0){
	foreach($QRY->result() as $outinfo);
	$challanno = $outinfo->challanno;
	$weight = $outinfo->weight;
	$pcs = $outinfo->pcs;
	}else{
	$challanno="";
	$weight="";
	$pcs="";	
	}

		
	/** Get User Department **/
				$udep=$this->db->select('b.department')->from('system_users a')->join('departments b','a.department_id=b.department_id')->where('a.user_id',$_SESSION['logged_in']['user_id'])->get(); 
				if($udep->num_rows()>0)
				{
					foreach($udep->result() as $depp);
					$depart=$depp->department;
					
				}else
				{
					$depart='';
				}
				
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
					
					/** Check if instrument requires fabrication **/
					/** $ryt=$this->db->select('b.fabrication')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->where('a.id',$proc->jobcardid)->get();
					if($ryt->num_rows()>0)
					{
						foreach($ryt->result() as $rtyeyte);
						$fabreq=$rtyeyte->fabrication;
						
					}else{ $fabreq=0 ;} **/
					
					/** if($fabreq==0)
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
				

				
				/** End **/
			
	if($markapp==1)
	{
		
	$timestamp=$this->getprevioustimestamp($tatstage,$proc->plannedOn,$proc->jobcardid,$flowstage,$proc->orderstageid);

	$tattime = DateTime::createFromFormat('d-M-Y g:i A', $timestamp);

	$finaltattime=$tattime->format('g:i A');
	$tatdate=$this->getupcomingtatdatenew($tatstage,$proc->plannedOn,$proc->jobcardid,$timestamp,$totaldaysslave,$proc->orderstageid,$day);

	if($proc->userstatus=='0')
	{
	$completedon="";
	}else
	{
	$completedon=date('d-M-Y g:i A',strtotime($proc->stagecompletedate));
	}
	
	$odtype=$this->getordertype($proc->originalorderid);
	$scheduler_data[] = array('sr_no'=>$i,
	'orderdate'=>$timestamp,
	'odtype'=>$odtype,
	'mname'=>strtoupper($proc->instruments_name),
	'jobcard'=>strtoupper($proc->job_card_no),
	'fileno'=>$fileno,
	'factory'=>strtoupper($depart),
	'tat'=>$tatdate,
	'actual'=>$completedon,
	'totdays'=>'',
	'challan'=>$challanno,
	'weight'=>$weight,
	'pcs'=>$pcs,
	'status'=>$prestui,
	'action'=>$html);
	 
	 
	$i++; 
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
	} 
		

	function machining_material_out_paintprocessoldtempp()
	{
	$flowstage=$this->uri->segment(3);
	$productionflowid=$this->uri->segment(4);
	/*GET CURRENT PROCESS SORT ORDER*/
			$QRY = $this->db->select('total_days, tat, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();
			$res = $QRY->result();
			foreach($res as $fmsinformation);
			$totaldaysslave = $fmsinformation->total_days;
			$tatstage = $fmsinformation->tat;
			$currentprocessorder = $flowstage;
		$setorder=$fmsinformation->setorder;
	/*GET CURRENT PROCESS SORT ORDER*/

	$user_id =$this->session->userdata['logged_in']['user_id'];
	/*$isfms=$this->db->select('fms_process')->from('system_users')->where('user_id',$user_id)->get();
	if($isfms->num_rows()>0)
	{
	foreach($isfms->result() as $isfmsa);
	$fmsprocess=$isfmsa->fms_process;
	}else{ $fmsprocess=0; }*/
	/** END **/
	$fmsprocess=$productionflowid;
	if($fmsprocess<>0)
	{
	/** Get Process Name **/
	$process=$this->db->select('a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('a.who_wedo',$user_id)->order_by('b.userstatus','ASC')->order_by('b.addedOn','DESC')->get();
	/** End **/
	if($process->num_rows()>0)
	{
	// echo "<pre>"; print_r($process->result());exit;
	$i=1;
	foreach($process->result() as $proc)
	{
	if($proc->userstatus=='0')
	{
	$prestui="<span class='btn btn-danger btn-xs'>Update Pending</span>";
	//$action='<a href="javascript:;" onclick="markstagedone('."'".$proc->orderstageid."'".','."'".$proc->flowstage."'".','."'".$proc->originalorderid."'".','."'".$proc->jobcardid."'".')"><span class="btn btn-xs btn-warning">Mark as done</span></a>';
	$html='<button class="btn btn-xs btn-warning waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$proc->orderstageid.'">Mark as Done</button>';

	$html.= ' <div id="con-close-modal'.$proc->orderstageid.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<form action="'.page_url.'Orderstage/materialpaintout/'.$proc->orderstageid.'/'.$proc->flowstage.'/'.$proc->originalorderid.'/'.$fmsprocess.'" method="post" id="loginForm" enctype="multipart/form-data">
		
	  <div id="pageloader">
	   <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
	</div>
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
												<h4 class="modal-title">MATERIAL OUT (PAINT)</h4>
											</div>
											<div class="modal-body">
										
												<div class="row">
												
												
													<div class="col-md-6">
														<div class="form-group">
															<label for="field-2" class="control-label">JOBCARD NUMBER<span style="color:red">*</span></label>
															
															<input type="text" style="width:250px" class="form-control" id="jobcard_no'.$proc->orderstageid.'" style="text-transform: uppercase;" name="jobcard_no'.$proc->orderstageid.'" placeholder="" value="'.$proc->job_card_no.'" readonly>
															<input type="hidden" name="jobcardid'.$proc->orderstageid.'" value="'.$proc->jobcardid.'">
														</div>
													</div>
													
													 <!--<div class="col-md-6">
														<div class="form-group">
															<label for="field-2" class="control-label">INSTRUMENT NAME<span style="color:red">*</span></label>
															
															<input type="text" style="width:250px" class="form-control" id="jobcard_no'.$proc->orderstageid.'" style="text-transform: uppercase;" name="jobcard_no'.$proc->orderstageid.'" placeholder="" value="'.$proc->instruments_name.'" readonly>
															<input type="hidden" name="jobcardid'.$proc->orderstageid.'" value="'.$proc->jobcardid.'">
															<input type="hidden" name="instrumentid'.$proc->orderstageid.'" value="'.$proc->item_id.'">
														</div>
													</div>-->

													<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">CHALLAN NO.<span style="color:red">*</span></label><br>

													<input type="text" class="form-control" id="challanno'.$proc->orderstageid.'" name="challanno'.$proc->orderstageid.'" required style="width:250px">
													</div>
													</div>
													
													
													<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">WEIGHT<span style="color:red">*</span></label><br>

													<input type="text" class="form-control" id="weight'.$proc->orderstageid.'" name="weight'.$proc->orderstageid.'" required style="width:250px" placeholder="IN Kg">
													</div>
													</div>
													
													
													<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">PCS<span style="color:red">*</span></label><br>

													<input type="number" class="form-control" id="pcs'.$proc->orderstageid.'" name="pcs'.$proc->orderstageid.'" required style="width:250px" min="0">
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

	$completedate = "";
	$addedtimefinal = "";


	}else{
	$compdate=date('d-M-Y H:i:s',strtotime($proc->stagecompletedate));
	$completedate = date('d-M-Y', strtotime($proc->stagecompletedate));
	$completetime = date('H:i:s', strtotime($proc->stagecompletedate));
	$addedtimefinal = "<br>". date('g:i A', strtotime($completetime));


	$prestui="<span class='btn btn-success btn-xs'>Remarks Updated</span>";
	$action="Task completed on ".$compdate;
	$html="This Task has been completed by You.";
	}
	date_default_timezone_set("Asia/Kolkata");
	$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
	$addedtime = "<br>". date('g:i A', strtotime($time));

	$time1=date('H:i:s', strtotime($proc->plannedOn));
	$addedtime1 = "<br>". date('g:i A', strtotime($time1));
	$planneddate1=date('Y-m-d',strtotime($proc->plannedOn));

		
						/*GET DATE OF PRIVIOUS STEPS*/
						$previousorder = $tatstage;
						
						if($previousorder<>0){
						$abc = $this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$proc->jobcardid)->order_by('id','DESC')->limit(1)->get();
							if($abc->num_rows()>0){
						foreach($abc->result() as $pastinfo);
						$complitiontime = $pastinfo->addedOn;
							
								$lasttaskcompletedtime = "<br>". date('g:i A', strtotime($complitiontime));

								
							}else{
							$complitiontime = $proc->added_on;
									$lasttaskcompletedtime = "<br>". date('g:i A', strtotime($complitiontime));

							}
						}else{
						$complitiontime = $proc->plannedOn;	
								$lasttaskcompletedtime = "<br>". date('g:i A', strtotime($complitiontime));

						}
						
						/*PREVIOUS COMPLITION DATE*/
						
						$checkpreviousdate = date('Y-m-d', strtotime($complitiontime));
						$previouscomplitiondate = date('d-M-Y', strtotime($complitiontime));
						$previouscomplitiontime = date('H:i:s', strtotime($proc->added_on));
						$previouscomplitionfinaltime = "<br>". date('g:i A', strtotime($previouscomplitiontime));
				
						/*GET DATE OF PRIVIOUS STEPS*/
						$date11 = new DateTime($previouscomplitiondate); 
						$date22 = new DateTime($completedate); 
						$interval1 = $date11->diff($date22); 
						$ttldays = $interval1->d; 
						if($ttldays=='0'){
						$totalday = "1";
						}else{
						$totalday = $ttldays;
				}
				
				/*TAT Date*/
				$lastcompdate = date('Y-m-d', strtotime($checkpreviousdate));
				$TATDATE=date('d-M-Y', strtotime($lastcompdate."+".$totaldaysslave." days"));
				
				/*TAT Date*/
		
	$QRY = $this->db->select('total_days, tat, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();

	if($this->uri->segment(3)=='1'){
		date_default_timezone_set("Asia/Kolkata");
	$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
	$addedtime = "<br>". date('g:i A', strtotime($time));
		}else{
		$previousorder = $setorder-1; 
		$QRY = $this->db->select('total_days, tat,setorder, flow_id ')->from('fms_flow')->where('setorder',$previousorder)->get();
		if($QRY->num_rows()>0){
		foreach($QRY->result() as $paststepdate);
			$flowid = $paststepdate->flow_id;
		$QRY1 = $this->db->select('jobcardid, flowstage,addedOn')->from('order_stage')->where('jobcardid',$proc->jobcardid)->where('flowstage',$flowid)->get();	
		if($QRY1->num_rows()>0){
		foreach($QRY1->result() as $timestamp){
		$addeddate1 = date('Y-m-d', strtotime($timestamp->addedOn));
	$time = date('H:i:s', strtotime($timestamp->addedOn));
	$addedtime = "<br>". date('g:i A', strtotime($time));
		}
		}	
		}else{
		$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
		}
		}

	$QRY = $this->db->select('jobcardid, challanno, weight, pcs')->from('material_paint_out')->where('jobcardid',$proc->jobcardid)->get();
	$res = $QRY->result();
	if($QRY->num_rows()>0){
	foreach($QRY->result() as $outinfo);
	$challanno = $outinfo->challanno;
	$weight = $outinfo->weight;
	$pcs = $outinfo->pcs;
	}else{
	$challanno="";
	$weight="";
	$pcs="";	
	}

		
	/** Get User Department **/
				$udep=$this->db->select('b.department')->from('system_users a')->join('departments b','a.department_id=b.department_id')->where('a.user_id',$_SESSION['logged_in']['user_id'])->get(); 
				if($udep->num_rows()>0)
				{
					foreach($udep->result() as $depp);
					$depart=$depp->department;
					
				}else
				{
					$depart='';
				}
				
				/** Check for dependency **/
				
				$checkdepend=$this->db->select('dependency')->from('fms_flow')->where('flow_id',$flowstage)->where('dependency','1')->get();
				$depend=$checkdepend->num_rows();
				//echo $depend;exit;
				if($depend==1)
				{
					
					$restt=$this->db->select('dependentflowid')->from('flowdependency')->where('flowid',$flowstage)->get();
					if($restt->num_rows()>0)
					{
						$dependcount=$restt->num_rows();
						$donarr=array();
						$donarr[]=0;
						//echo "<pre>";print_r($restt->result());exit;
						foreach($restt->result() as $resttyu)
						{
							$isdone=$this->db->select('id,userstatus')->from('order_stage')->where('flowstage',$resttyu->dependentflowid)->where('jobcardid',$proc->jobcardid)->where('orderid',$proc->originalorderid)->order_by('id','DESC')->limit(1)->get();
							if($isdone->num_rows()>0)
							{
								foreach($isdone->result() as $isdone11);
								$donarr[]=$isdone11->userstatus;
								
							}else
							{
								$markapp=1;
							}
							
						}
						//echo $dependcount.'<br>'."<pre>"; print_r($donarr);exit;
						if($dependcount==array_sum($donarr))
						{
							$markapp=1;
						}else{
							$markapp=0;
						}
						
					}else{
						$markapp=1;
					}
					
				}else{
					
					$markapp=1;
				}
				/** End **/
				
				/** New Timestamp query **/
				$previousorder=$tatstage;
				if($previousorder==0)
				{
					$previouscomtime=$proc->plannedOn;
					$previouscomdate=date('d-M-Y',strtotime($previouscomtime))."<br>";
					$previouscomtimeonly=date('g:i A',strtotime($previouscomtime));
					
				}else
				{
					//echo $previousorder;exit;
					$oldtime1=$this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$proc->jobcardid)->order_by('id','DESC')->limit(1)->get();
							if($oldtime1->num_rows()>0){
								foreach($oldtime1->result() as $pastinfo);
								$previouscomtime=$pastinfo->addedOn;
								$previouscomdate=date('d-M-Y',strtotime($pastinfo->addedOn))."<br>";
								$previouscomtimeonly=date('g:i A',strtotime($pastinfo->addedOn));
							}else
							{
								$previouscomtime=$proc->plannedOn;
					$previouscomdate=date('d-M-Y',strtotime($previouscomtime))."<br>";
					$previouscomtimeonly=date('g:i A',strtotime($previouscomtime));
							}
				}

		
				
				/** End **/
		

	if($markapp==1)
	{
		
	/** Holiday Check**/
				
					$TATDATEFORHOLIDAYCHECK=date('Y-m-d', strtotime($TATDATE));
					$timestampforcheck=date('Y-m-d',strtotime($previouscomtime));
					$date_from=strtotime($timestampforcheck);
					$date_to=strtotime($TATDATEFORHOLIDAYCHECK);
					$betweendates=array();
					for ($g=$date_from; $g<=$date_to; $g+=86400) {  
						$betweendates[]= date("Y-m-d", $g);  
					} 
				
				//echo "<pre>"; print_r($betweendates);
					if(count($betweendates)>0)
				{
					
					$alldates = "'" . implode ( "', '", $betweendates ) . "'";
					
					$ifholiday=$this->db->select('holiday_id')->from('prestogroup_holidays')->where_in('holiday_date',$alldates,false)->get();
					if($ifholiday->num_rows()>0)
					{
			
						//echo "hi".$proc->job_card_no;
						$TATDATEFORHOLIDAYCHECK=date('Y-m-d', strtotime($TATDATEFORHOLIDAYCHECK."+".$ifholiday->num_rows()." days"));
						//echo $TATDATEFORHOLIDAYCHECK;
						
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
				//$TATDATE= date('d-M-Y', strtotime($nextdate."+1 days"));
				$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();
				if($query->num_rows()>0){
					
					foreach($query->result() as $afterholiday);
					$aftrholidaydate = $afterholiday->holiday_date;
					$TATDATE= date('d-M-Y', strtotime($aftrholidaydate."+1 days"));
					
				}else {
				$TATDATE= date('d-M-Y', strtotime($nextdate));
				}
			}else{
			$TATDATE=date('d-M-Y', strtotime($TATDATEFORHOLIDAYCHECK));
			}
					
				/** End **/
	$scheduler_data[] = array('sr_no'=>$i,
	'orderdate'=>$previouscomdate.$previouscomtimeonly,
	'mname'=>strtoupper($proc->instruments_name),
	'jobcard'=>strtoupper($proc->job_card_no),
	'factory'=>strtoupper($depart),
	'planned'=>date('d-M-Y',strtotime($proc->plannedOn)).$addedtime1,
	'tat'=>$TATDATE."<br>".$previouscomtimeonly,
	'actual'=>$completedate.$addedtimefinal,
	'totdays'=>$totalday,
	'challan'=>$challanno,
	'weight'=>$weight,
	'pcs'=>$pcs,
	'status'=>$prestui,
	'action'=>$html);
	 
	 
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

	}
	} 


	function materialpaintout()
	{
		
	$orderstage=$this->uri->segment(3);
	$flowstage=$this->uri->segment(4);
	$orderid=$this->uri->segment(5);
	$selforder=$this->fmsmodel->getselforder($orderid);
	$productionflowid=$this->uri->segment(6);
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$newstage=[];
	$isfinalstep=0;
	$jobcardid=$this->input->post('jobcardid'.$orderstage);
	$challanno=$this->input->post('challanno'.$orderstage);
	$weight=$this->input->post('weight'.$orderstage);
	$pcs=$this->input->post('pcs'.$orderstage);

		
		/** Move to next stage **/
			$currentstage=$flowstage;
			$getset=$this->db->select('setorder,finalstep,total_days,set_time')->from('fms_flow')->where('flow_id',$currentstage)->where('production_flow_id',$productionflowid)->get();
			
				foreach($getset->result() as $ggetset);
				$currentsetorder=$ggetset->setorder;
				$isfinalstep=$ggetset->finalstep;	
				$tatday=$ggetset->total_days;
				$tathours=$ggetset->set_time;			   
				/** Get Next Order **/
				$getneworder=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id',$productionflowid)->where('setorder>',$currentsetorder)->limit(1)->order_by('setorder','ASC')->get();
				if($getneworder->num_rows()>0)
			{
				foreach($getneworder->result() as $getneworder1);
				/** Check Move **/	
				$restymove=$this->db->select('moveto')->from('fms_flow')->where('flow_id',$flowstage)->get();
				foreach($restymove->result() as $restymove1)
				{
					//echo $restymove1->moveto;exit;
				if(($restymove1->moveto<>0) && ($restymove1->moveto<>''))
				{
				$moveflow=$restymove1->moveto;
				$movearr=explode(',',$moveflow);
				foreach($movearr as $movea)
				{
				$newstage[]=$movea;
				}
				
				} else
				{
			
					$newstage[]=$getneworder1->flow_id;
				} 
				}

				
			}
				/** End **/

			//echo "<pre>"; print_r($newstage);exit;
			if(count($newstage)>0)
			{
				
			/** Detail Entery **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'challanno'=>$challanno,'weight'=>$weight,'pcs'=>$pcs,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->insert('material_paint_out',$detailentry);
			$effrow1=$this->db->affected_rows();	
			
			/** End**/
			if($effrow1>0)
			{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);
			$effrow=$this->db->affected_rows();	
		if($effrow>0)
		{
			for($u=0;$u<count($newstage);$u++)
			{
			
	$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$newstage[$u])->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
		
	if($newstage[$u]<>0)
	{
		$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage[$u],'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
			/** Add Tat **/
						$stageid=$this->db->insert_id();
						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$newstage[$u]);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
	}
			}else
			{
				
				foreach($checkodd->result() as $checkodd1);
				/** Add Tat **/
						$stageid=$checkodd1->id;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$newstage[$u]);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
				
				
			}
			
			}
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Order Moved to Next Process.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}else{
			
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
		
		}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
				
			}else{
				
				
				if($isfinalstep!=0)
				{
					
						/** Check if Merge is available **/
				  $merger=$this->db->select('id,mergewith,flowid')->from('fmsmerge')->where('productionflow',$productionflowid)->get();
					if($merger->num_rows()>0)
					{
						foreach($merger->result() as $mergerdata);
						$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$mergerdata->flowid)->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
						$orderstmergedata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$mergerdata->flowid,'userstatus'=>'0','remarks'=>'','addedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('order_stage',$orderstmergedata); 
				        $odm=$this->db->insert_id();
						/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			}else
			{
				foreach($checkodd->result() as $prevorderstage);
				$odm=$prevorderstage->id;
				/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
				
				
			}
					
						if($odm<>0)
						{
							$mergehistdata=array('orderstageid'=>$odm,'oldproductionflow'=>$productionflowid,'jobcardid'=>$jobcardid,'newproductionflow'=>$mergerdata->mergewith,'oldfmsflow'=>$flowstage,'newfmsflow'=>$mergerdata->flowid,'addedOn'=>date('Y-m-d H:i:s'));
							$this->db->insert('fmsmergehistory',$mergehistdata);
							
							$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Merged.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
							
							}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to merge flow.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
					}else { 
					$newdata=array('complete'=>'1');
					$this->db->where('order_id',$orderid);
					$this->db->where('id',$jobcardid);
					$this->db->update('order_instruments',$newdata);


					/** IF THIS IS SALESFORCE ORDER **/
					$this->checkifthisissforderandlastonetogetcompleted($orderid);

					/** END **/

					if($selforder=='1')
					{
					$this->fmsmodel->addstock($jobcardid);
					}
$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);					
					}
					/** Merge **/
					$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}else
				{
				
				/** End **/
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}
			}

		


	}

	
	function materialpaintoutoldfmstatdate()
	{
		
	$orderstage=$this->uri->segment(3);
	$flowstage=$this->uri->segment(4);
	$orderid=$this->uri->segment(5);
	$selforder=$this->fmsmodel->getselforder($orderid);
	$productionflowid=$this->uri->segment(6);
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$newstage=[];
	$isfinalstep=0;
	$jobcardid=$this->input->post('jobcardid'.$orderstage);
	$challanno=$this->input->post('challanno'.$orderstage);
	$weight=$this->input->post('weight'.$orderstage);
	$pcs=$this->input->post('pcs'.$orderstage);

		
		/** Move to next stage **/
			$currentstage=$flowstage;
			$getset=$this->db->select('setorder,finalstep')->from('fms_flow')->where('flow_id',$currentstage)->where('production_flow_id',$productionflowid)->get();
			
				foreach($getset->result() as $ggetset);
				$currentsetorder=$ggetset->setorder;
			   $isfinalstep=$ggetset->finalstep;		
				/** Get Next Order **/
				$getneworder=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id',$productionflowid)->where('setorder>',$currentsetorder)->limit(1)->order_by('setorder','ASC')->get();
				if($getneworder->num_rows()>0)
			{
				foreach($getneworder->result() as $getneworder1);
				/** Check Move **/	
				$restymove=$this->db->select('moveto')->from('fms_flow')->where('flow_id',$flowstage)->get();
				foreach($restymove->result() as $restymove1)
				{
					//echo $restymove1->moveto;exit;
				if(($restymove1->moveto<>0) && ($restymove1->moveto<>''))
				{
				$moveflow=$restymove1->moveto;
				$movearr=explode(',',$moveflow);
				foreach($movearr as $movea)
				{
				$newstage[]=$movea;
				}
				
				} else
				{
			
					$newstage[]=$getneworder1->flow_id;
				} 
				}

				
			}
				/** End **/

			//echo "<pre>"; print_r($newstage);exit;
			if(count($newstage)>0)
			{
				
			/** Detail Entery **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'challanno'=>$challanno,'weight'=>$weight,'pcs'=>$pcs,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->insert('material_paint_out',$detailentry);
			$effrow1=$this->db->affected_rows();	
			
			/** End**/
			if($effrow1>0)
			{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);
			$effrow=$this->db->affected_rows();	
		if($effrow>0)
		{
			for($u=0;$u<count($newstage);$u++)
			{
			
	$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$newstage[$u])->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
		
	if($newstage[$u]<>0)
	{
		$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage[$u],'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
			/** Add Tat **/
						$stageid=$this->db->insert_id();
						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->where('flowstage',$newstage[$u]);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
	}
			}else
			{
				
				foreach($checkodd->result() as $checkodd1);
				/** Add Tat **/
						$stageid=$checkodd1->id;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->where('flowstage',$newstage[$u]);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
				
				
			}
			
			}
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Order Moved to Next Process.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}else{
			
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
		
		}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
				
			}else{
				
				
				if($isfinalstep!=0)
				{
					
						/** Check if Merge is available **/
				  $merger=$this->db->select('id,mergewith,flowid')->from('fmsmerge')->where('productionflow',$productionflowid)->get();
					if($merger->num_rows()>0)
					{
						foreach($merger->result() as $mergerdata);
						$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$mergerdata->flowid)->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
						$orderstmergedata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$mergerdata->flowid,'userstatus'=>'0','remarks'=>'','addedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('order_stage',$orderstmergedata); 
				        $odm=$this->db->insert_id();
						/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			}else
			{
				foreach($checkodd->result() as $prevorderstage);
				$odm=$prevorderstage->id;
				/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
				
				
			}
					
						if($odm<>0)
						{
							$mergehistdata=array('orderstageid'=>$odm,'oldproductionflow'=>$productionflowid,'jobcardid'=>$jobcardid,'newproductionflow'=>$mergerdata->mergewith,'oldfmsflow'=>$flowstage,'newfmsflow'=>$mergerdata->flowid,'addedOn'=>date('Y-m-d H:i:s'));
							$this->db->insert('fmsmergehistory',$mergehistdata);
							
							$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Merged.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
							
							}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to merge flow.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
					}else { 
					$newdata=array('complete'=>'1');
					$this->db->where('order_id',$orderid);
					$this->db->where('id',$jobcardid);
					$this->db->update('order_instruments',$newdata);

					if($selforder=='1')
					{
					$this->fmsmodel->addstock($jobcardid);
					}
$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);					
					}
					/** Merge **/
					$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}else
				{
				
				/** End **/
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}
			}

		


	}

	


	function materialpaintoutold28jan20202()
	{
		
	$orderstage=$this->uri->segment(3);
	$flowstage=$this->uri->segment(4);
	$orderid=$this->uri->segment(5);
	$productionflowid=$this->uri->segment(6);
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$newstage=[];
	$jobcardid=$this->input->post('jobcardid'.$orderstage);
	$challanno=$this->input->post('challanno'.$orderstage);
	$weight=$this->input->post('weight'.$orderstage);
	$pcs=$this->input->post('pcs'.$orderstage);

		
		/** Move to next stage **/
			$currentstage=$flowstage;
			$getset=$this->db->select('setorder')->from('fms_flow')->where('flow_id',$currentstage)->get();
			if($getset->num_rows()>0)
			{
				foreach($getset->result() as $ggetset);
				$currentsetorder=$ggetset->setorder;	
				/** Get Next Order **/
				$getneworder=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id',$productionflowid)->where('setorder>',$currentsetorder)->limit(1)->order_by('setorder','ASC')->get();
				if($getneworder->num_rows()>0)
			{
				foreach($getneworder->result() as $getneworder1);
				/** Check Move **/	
				$restymove=$this->db->select('moveto')->from('fms_flow')->where('flow_id',$flowstage)->get();
				foreach($restymove->result() as $restymove1)
				{
					//echo $restymove1->moveto;exit;
				if(($restymove1->moveto<>0) && ($restymove1->moveto<>''))
				{
				$moveflow=$restymove1->moveto;
				$movearr=explode(',',$moveflow);
				foreach($movearr as $movea)
				{
				$newstage[]=$movea;
				}
				
				} else
				{
			
					$newstage[]=$getneworder1->flow_id;
				} 
				}

				
			}else{
				
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			}
				/** End **/
	}else{
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			}
			//echo "<pre>"; print_r($newstage);exit;
			if(count($newstage)>0)
			{
				
			/** Detail Entery **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'challanno'=>$challanno,'weight'=>$weight,'pcs'=>$pcs,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->insert('material_paint_out',$detailentry);
			$effrow1=$this->db->affected_rows();	
			
			/** End**/
			if($effrow1>0)
			{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);
			$effrow=$this->db->affected_rows();	
		if($effrow>0)
		{
			for($u=0;$u<count($newstage);$u++)
			{
			
	$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$newstage[$u])->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
		
	if($newstage[$u]<>0)
	{
		$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage[$u],'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
	}
			}
			
			}
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Order Moved to Next Process.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}else{
			
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
		
		}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
				
			}else{
				
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			}

		


	}



		function machining_material_in_paintprocess()
	{
				$scheduler_data=array();
	$flowstage=$this->uri->segment(3);
	$productionflowid=$this->uri->segment(4);
	/*GET CURRENT PROCESS SORT ORDER*/
			$QRY = $this->db->select('total_days,tat,set_time, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();
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
			$currentprocessorder = $flowstage;
		$setorder=$fmsinformation->setorder;
		$user_id =$this->session->userdata['logged_in']['user_id'];
		$fmsprocess=$productionflowid;
	/** END **/
	if($fmsprocess<>0)
	{
	/** Get Process Name **/
	$process=$this->db->select('d.file_number,f.ordertype,f.fileno,a.jumpfabricationappl,a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow,d.fabrication,f.planstartsfrom')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('a.who_wedo',$user_id)->order_by('b.userstatus','ASC')->order_by('b.addedOn','DESC')->get();
	/** End **/
	if($process->num_rows()>0)
	{
	// echo "<pre>"; print_r($process->result());exit;
	$i=1;
	foreach($process->result() as $proc)
	{

		$ordertype = $proc->ordertype;
			if($ordertype=='1'){
				
				$fileno=$proc->file_number;
			}else{
			
				$fileno=$proc->fileno;
			}

	if($proc->userstatus=='0')
	{
	$prestui="<span class='btn btn-danger btn-xs'>Update Pending</span>";
	//$action='<a href="javascript:;" onclick="markstagedone('."'".$proc->orderstageid."'".','."'".$proc->flowstage."'".','."'".$proc->originalorderid."'".','."'".$proc->jobcardid."'".')"><span class="btn btn-xs btn-warning">Mark as done</span></a>';
	$html='<button class="btn btn-xs btn-warning waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$proc->orderstageid.'">Mark as Done</button>';

	$html.= ' <div id="con-close-modal'.$proc->orderstageid.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<form action="'.page_url.'Orderstage/materialpaintin/'.$proc->orderstageid.'/'.$proc->flowstage.'/'.$proc->originalorderid.'/'.$fmsprocess.'" method="post" id="loginForm" enctype="multipart/form-data">
		
	  <div id="pageloader">
	   <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
	</div>
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
												<h4 class="modal-title">MATERIAL IN (PAINT)</h4>
											</div>
											<div class="modal-body">
										
												<div class="row">
												
												
													<div class="col-md-6">
														<div class="form-group">
															<label for="field-2" class="control-label">JOBCARD NUMBER<span style="color:red">*</span></label>
															
															<input type="text" style="width:250px" class="form-control" id="jobcard_no'.$proc->orderstageid.'" style="text-transform: uppercase;" name="jobcard_no'.$proc->orderstageid.'" placeholder="" value="'.$proc->job_card_no.'" readonly>
															<input type="hidden" name="jobcardid'.$proc->orderstageid.'" value="'.$proc->jobcardid.'">
														</div>
													</div>';
													$weightin=array();
													$pcsin=array();
													$resttt=$this->db->select('challanno,weight,pcs')->from('material_paint_out')->where('jobcardid',$proc->jobcardid)->get();
													if($resttt->num_rows()>0)
													{
														foreach($resttt->result() as $restuy);
														$challanno=$restuy->challanno;
														$weight=$restuy->weight;
														$pcs=$restuy->pcs;
													}else{
														$challanno='';
														$weight=0;
														$pcs=0;
													}
													
													$resttt=$this->db->select('weight,pcs')->from('material_paint_in')->where('jobcardid',$proc->jobcardid)->get();
													if($resttt->num_rows()>0)
													{
														foreach($resttt->result() as $restuy)
														{
														
														$weightin[]=$restuy->weight;
														$pcsin[]=$restuy->pcs;
														}
													}else{
														$weightin[]=0;
														$pcsin[]=0;
													}
													if(count($weightin)>0)
													{
														$weightin1=array_sum($weightin);
													}else{
														$weightin1=0;
														
													}
													
													
													if(count($pcsin)>0)
													{
														$pcsin1=array_sum($pcsin);
													}else
													{
														$pcsin1=0;
													}
													
													$finalweight=$weight-$weightin1;
													$weightslab=$finalweight*0.1;
													$finweight=$finalweight+$weightslab;
													$pcsfinal=$pcs-$pcsin1;
													$html.='<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">CHALLAN NO.<span style="color:red">*</span></label><br>
													<input type="hidden" name="outweight'.$proc->orderstageid.'" value="'.$weight.'">
													<input type="hidden" name="outpcs'.$proc->orderstageid.'" value="'.$pcs.'">

													<input type="text" class="form-control" id="challanno'.$proc->orderstageid.'" name="challanno'.$proc->orderstageid.'" required style="width:250px" value="'.$challanno.'" readonly>
													</div>
													</div>
													
													
													<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">WEIGHT<span style="color:red">*</span>&nbsp;&nbsp;&nbsp;<span style="font-size:10px;color:red;">OUT WEIGHT: '.floatval($finalweight).'KG </span></label><br>

													<input type="number" step="0.001" class="form-control" id="weight'.$proc->orderstageid.'" name="weight'.$proc->orderstageid.'" max="'.$finweight.'" required style="width:250px" placeholder="IN Kg">
													</div>
													</div>
													
													
													<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">PCS<span style="color:red">*</span>&nbsp;&nbsp;&nbsp;<span style="font-size:10px;color:red;">OUT PCS: '.$pcsfinal.'</span></label><br>

													<input type="number" class="form-control" id="pcs'.$proc->orderstageid.'" name="pcs'.$proc->orderstageid.'" required style="width:250px" min="0"  max="'.$pcsfinal.'">
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


	$completedate="";
	$addedfinaltime="";

	}else{
	$compdate=date('d-M-Y H:i:s',strtotime($proc->stagecompletedate));
	$prestui="<span class='btn btn-success btn-xs'>Remarks Updated</span>";
	$action="Task completed on ".$compdate;
	$html="This Task has been completed by You.";

	date_default_timezone_set("Asia/Kolkata");
	$completedate = date('d-M-Y', strtotime($proc->stagecompletedate));
	$completetime = date('H:i:s', strtotime($proc->stagecompletedate));
	$addedfinaltime = "<br>". date('g:i A', strtotime($completetime));

	}
	
$previousorder = $tatstage;
						

	$QRY = $this->db->select('jobcardid, challanno, weight, pcs')->from('material_paint_out')->where('jobcardid',$proc->jobcardid)->get();
		if($QRY->num_rows()>0){
	foreach($QRY->result() as $outinfo);
	$challanno = $outinfo->challanno;
	$weight = $outinfo->weight;
	$pcs = $outinfo->pcs;
		}else{
			$challanno = "";
	$weight = "";
	$pcs = "";
	}
	$QRY1 = $this->db->select('jobcardid, challanno, weight, pcs')->from('material_paint_in')->where('jobcardid',$proc->jobcardid)->get();
	$m=1;
		if($proc->userstatus=='0')
	{
	$htmldata ="<p style='text-align:center;color:red;font-weight:bold;'>Material Inward Pending</p>";	
	}else
	{
	$htmldata ="<p style='text-align:center;color:red;font-weight:bold;'>Material Inward Complete</p>";
	}
	$htmldata.= "<table border='1' style='width:200px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center'>WEIGHT IN KG</th><th style='padding:2px 2px 2px 2px; text-align:center'>PCS</th></tr><tbody>";
	$result = $QRY1->result();
	if($result>0){
		foreach($result as $indata){
		$htmldata.="<tr><td style='padding:2px 2px 2px 2px; text-align:center'>".$indata->weight."</td><td style='padding:2px 2px 2px 2px; text-align:center'>".$indata->pcs."</td></tr>";
		$m++;
		}
	}
	$htmldata.="</tbody></table>";

		
	/** Get User Department **/
				$udep=$this->db->select('b.department')->from('system_users a')->join('departments b','a.department_id=b.department_id')->where('a.user_id',$_SESSION['logged_in']['user_id'])->get(); 
				if($udep->num_rows()>0)
				{
					foreach($udep->result() as $depp);
					$depart=$depp->department;
					
				}else
				{
					$depart='';
				}


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
					
					
					/** Check if instrument requires fabrication **/
					/**$ryt=$this->db->select('b.fabrication')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->where('a.id',$proc->jobcardid)->get();
					if($ryt->num_rows()>0)
					{
						foreach($ryt->result() as $rtyeyte);
						$fabreq=$rtyeyte->fabrication;
						
					}else{ $fabreq=0 ;} **/
					
					/** Get Last Step of Fabrication **/

				/**	if($fabreq==0)
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
				/** End **/
						
		/** Check for merge fms intersection **/
				if($markapp==1)
				{
					
				$markapp=$this->fmsmodel->checkformergerpoint($flowstage,$proc->jobcardid,$proc->originalorderid,$proc->fabrication,$proc->jumpfabricationappl);
				}
				

				
				/** End **/
		
				if($markapp==1)
				{
					
				$timestamp=$this->getprevioustimestamp($tatstage,$proc->plannedOn,$proc->jobcardid,$flowstage,$proc->orderstageid);
				$tattime = DateTime::createFromFormat('d-M-Y g:i A', $timestamp);
				$finaltattime=$tattime->format('g:i A');
				$tatdate=$this->getupcomingtatdatenew($tatstage,$proc->plannedOn,$proc->jobcardid,$timestamp,$totaldaysslave,$proc->orderstageid,$day);
				if($proc->userstatus=='0')
				{
				$completedon="";
				}else
				{
				$completedon=date('d-M-Y g:i A',strtotime($proc->stagecompletedate));
				}

					
					$odtype=$this->getordertype($proc->originalorderid);
	$scheduler_data[] = array('sr_no'=>$i,
	'orderdate'=>$timestamp,
	'odtype'=>$odtype,
	'mname'=>strtoupper($proc->instruments_name),
	'jobcard'=>strtoupper($proc->job_card_no),
	'fileno'=>$fileno,
	'factory'=>strtoupper($depart),
	'tat'=>$tatdate,
	'actual'=>$completedon,
	'totdays'=>'',
	'challan'=>$challanno,
	'weight'=>$weight,
	'pcs'=>$pcs,
	'htmldata'=>$htmldata,
	'status'=>$prestui,
	'action'=>$html);
	 
	 
	$i++;
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
	} 



	function machining_material_in_paintprocessoldtempp()
	{
	$flowstage=$this->uri->segment(3);
	$productionflowid=$this->uri->segment(4);
	/*GET CURRENT PROCESS SORT ORDER*/
			$QRY = $this->db->select('total_days, tat, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();
			$res = $QRY->result();
			foreach($res as $fmsinformation);
			$totaldaysslave = $fmsinformation->total_days;
			$tatstage = $fmsinformation->tat;
			$currentprocessorder = $flowstage;
		$setorder=$fmsinformation->setorder;
	/*GET CURRENT PROCESS SORT ORDER*/
	$user_id =$this->session->userdata['logged_in']['user_id'];
	/*$isfms=$this->db->select('fms_process')->from('system_users')->where('user_id',$user_id)->get();
	if($isfms->num_rows()>0)
	{
	foreach($isfms->result() as $isfmsa);
	$fmsprocess=$isfmsa->fms_process;
	}else{ $fmsprocess=0; }*/
	$fmsprocess=$productionflowid;
	/** END **/
	if($fmsprocess<>0)
	{
	/** Get Process Name **/
	$process=$this->db->select('a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('a.who_wedo',$user_id)->order_by('b.userstatus','ASC')->order_by('b.addedOn','DESC')->get();
	/** End **/
	if($process->num_rows()>0)
	{
	// echo "<pre>"; print_r($process->result());exit;
	$i=1;
	foreach($process->result() as $proc)
	{
	if($proc->userstatus=='0')
	{
	$prestui="<span class='btn btn-danger btn-xs'>Update Pending</span>";
	//$action='<a href="javascript:;" onclick="markstagedone('."'".$proc->orderstageid."'".','."'".$proc->flowstage."'".','."'".$proc->originalorderid."'".','."'".$proc->jobcardid."'".')"><span class="btn btn-xs btn-warning">Mark as done</span></a>';
	$html='<button class="btn btn-xs btn-warning waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$proc->orderstageid.'">Mark as Done</button>';

	$html.= ' <div id="con-close-modal'.$proc->orderstageid.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<form action="'.page_url.'Orderstage/materialpaintin/'.$proc->orderstageid.'/'.$proc->flowstage.'/'.$proc->originalorderid.'/'.$fmsprocess.'" method="post" id="loginForm" enctype="multipart/form-data">
		
	  <div id="pageloader">
	   <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
	</div>
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
												<h4 class="modal-title">MATERIAL IN (PAINT)</h4>
											</div>
											<div class="modal-body">
										
												<div class="row">
												
												
													<div class="col-md-6">
														<div class="form-group">
															<label for="field-2" class="control-label">JOBCARD NUMBER<span style="color:red">*</span></label>
															
															<input type="text" style="width:250px" class="form-control" id="jobcard_no'.$proc->orderstageid.'" style="text-transform: uppercase;" name="jobcard_no'.$proc->orderstageid.'" placeholder="" value="'.$proc->job_card_no.'" readonly>
															<input type="hidden" name="jobcardid'.$proc->orderstageid.'" value="'.$proc->jobcardid.'">
														</div>
													</div>';
													$weightin=array();
													$pcsin=array();
													$resttt=$this->db->select('challanno,weight,pcs')->from('material_paint_out')->where('jobcardid',$proc->jobcardid)->get();
													if($resttt->num_rows()>0)
													{
														foreach($resttt->result() as $restuy);
														$challanno=$restuy->challanno;
														$weight=$restuy->weight;
														$pcs=$restuy->pcs;
													}else{
														$challanno='';
														$weight=0;
														$pcs=0;
													}
													
													$resttt=$this->db->select('weight,pcs')->from('material_paint_in')->where('jobcardid',$proc->jobcardid)->get();
													if($resttt->num_rows()>0)
													{
														foreach($resttt->result() as $restuy)
														{
														
														$weightin[]=$restuy->weight;
														$pcsin[]=$restuy->pcs;
														}
													}else{
														$weightin[]=0;
														$pcsin[]=0;
													}
													if(count($weightin)>0)
													{
														$weightin1=array_sum($weightin);
													}else{
														$weightin1=0;
														
													}
													
													
													if(count($pcsin)>0)
													{
														$pcsin1=array_sum($pcsin);
													}else
													{
														$pcsin1=0;
													}
													
													$finalweight=$weight-$weightin1;
													$weightslab=$finalweight*0.1;
													$finweight=$finalweight+$weightslab;
													$pcsfinal=$pcs-$pcsin1;
													$html.='<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">CHALLAN NO.<span style="color:red">*</span></label><br>
													<input type="hidden" name="outweight'.$proc->orderstageid.'" value="'.$weight.'">
													<input type="hidden" name="outpcs'.$proc->orderstageid.'" value="'.$pcs.'">

													<input type="text" class="form-control" id="challanno'.$proc->orderstageid.'" name="challanno'.$proc->orderstageid.'" required style="width:250px" value="'.$challanno.'" readonly>
													</div>
													</div>
													
													
													<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">WEIGHT<span style="color:red">*</span>&nbsp;&nbsp;&nbsp;<span style="font-size:10px;color:red;">OUT WEIGHT: '.floatval($finalweight).'KG </span></label><br>

													<input type="number" step="0.001" class="form-control" id="weight'.$proc->orderstageid.'" name="weight'.$proc->orderstageid.'" max="'.$finweight.'" required style="width:250px" placeholder="IN Kg">
													</div>
													</div>
													
													
													<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">PCS<span style="color:red">*</span>&nbsp;&nbsp;&nbsp;<span style="font-size:10px;color:red;">OUT PCS: '.$pcsfinal.'</span></label><br>

													<input type="number" class="form-control" id="pcs'.$proc->orderstageid.'" name="pcs'.$proc->orderstageid.'" required style="width:250px" min="0"  max="'.$pcsfinal.'">
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


	$completedate="";
	$addedfinaltime="";

	}else{
	$compdate=date('d-M-Y H:i:s',strtotime($proc->stagecompletedate));
	$prestui="<span class='btn btn-success btn-xs'>Remarks Updated</span>";
	$action="Task completed on ".$compdate;
	$html="This Task has been completed by You.";

	date_default_timezone_set("Asia/Kolkata");
	$completedate = date('d-M-Y', strtotime($proc->stagecompletedate));
	$completetime = date('H:i:s', strtotime($proc->stagecompletedate));
	$addedfinaltime = "<br>". date('g:i A', strtotime($completetime));

	}
	date_default_timezone_set("Asia/Kolkata");
	$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
	$addedtime = "<br>". date('g:i A', strtotime($time));

	$time1=date('H:i:s', strtotime($proc->plannedOn));
	$addedtime1 = "<br>". date('g:i A', strtotime($time1));
	$planneddate1=date('Y-m-d',strtotime($proc->plannedOn));

	/*GET DATE OF PRIVIOUS STEPS*/
						$previousorder = $tatstage;
						
							if($previousorder<>0){
						$abc = $this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$proc->jobcardid)->order_by('id','DESC')->limit(1)->get();
							if($abc->num_rows()>0){
						foreach($abc->result() as $pastinfo);
						$complitiontime = $pastinfo->addedOn;
							
								$lasttaskcompletedtime = "<br>". date('g:i A', strtotime($complitiontime));

								
							}else{
							$complitiontime = $proc->added_on;
									$lasttaskcompletedtime = "<br>". date('g:i A', strtotime($complitiontime));

							}
						}else{
						$complitiontime = $proc->plannedOn;	
								$lasttaskcompletedtime = "<br>". date('g:i A', strtotime($complitiontime));

						}
						/*PREVIOUS COMPLITION DATE*/
						
						$checkpreviousdate = date('Y-m-d', strtotime($complitiontime));
						$previouscomplitiondate = date('d-M-Y', strtotime($complitiontime));
						$previouscomplitiontime = date('H:i:s', strtotime($proc->added_on));
						$previouscomplitionfinaltime = "<br>". date('g:i A', strtotime($previouscomplitiontime));
				
						/*GET DATE OF PRIVIOUS STEPS*/
						$date11 = new DateTime($previouscomplitiondate); 
						$date22 = new DateTime($completedate); 
						$interval1 = $date11->diff($date22); 
						$ttldays = $interval1->d; 
						if($ttldays=='0'){
						$totalday = "1";
						}else{
						$totalday = $ttldays;
				}
				
				/*TAT Date*/
				$lastcompdate = date('Y-m-d', strtotime($checkpreviousdate));
				$TATDATE=date('d-M-Y', strtotime($lastcompdate."+".$totaldaysslave." days"));
				
				/*TAT Date*/
		
	$QRY = $this->db->select('total_days, tat, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();

	if($this->uri->segment(3)=='1'){
		date_default_timezone_set("Asia/Kolkata");
	$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
	$addedtime = "<br>". date('g:i A', strtotime($time));
		}else{
		$previousorder = $setorder-1; 
		$QRY = $this->db->select('total_days, tat,setorder, flow_id ')->from('fms_flow')->where('setorder',$previousorder)->get();
		if($QRY->num_rows()>0){
		foreach($QRY->result() as $paststepdate);
			$flowid = $paststepdate->flow_id;
		$QRY1 = $this->db->select('jobcardid, flowstage,addedOn')->from('order_stage')->where('jobcardid',$proc->jobcardid)->where('flowstage',$flowid)->get();	
		if($QRY1->num_rows()>0){
		foreach($QRY1->result() as $timestamp){
		$addeddate1 = date('Y-m-d', strtotime($timestamp->addedOn));
	$time = date('H:i:s', strtotime($timestamp->addedOn));
	$addedtime = "<br>". date('g:i A', strtotime($time));
		}
		}	
		}else{
		$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
		}
		}

	$QRY = $this->db->select('jobcardid, challanno, weight, pcs')->from('material_paint_out')->where('jobcardid',$proc->jobcardid)->get();
		if($QRY->num_rows()>0){
	foreach($QRY->result() as $outinfo);
	$challanno = $outinfo->challanno;
	$weight = $outinfo->weight;
	$pcs = $outinfo->pcs;
		}else{
			$challanno = "";
	$weight = "";
	$pcs = "";
	}
	$QRY1 = $this->db->select('jobcardid, challanno, weight, pcs')->from('material_paint_in')->where('jobcardid',$proc->jobcardid)->get();
	$m=1;
		if($proc->userstatus=='0')
	{
	$htmldata ="<p style='text-align:center;color:red;font-weight:bold;'>Material Inward Pending</p>";	
	}else
	{
	$htmldata ="<p style='text-align:center;color:red;font-weight:bold;'>Material Inward Complete</p>";
	}
	$htmldata.= "<table border='1' style='width:200px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center'>WEIGHT IN KG</th><th style='padding:2px 2px 2px 2px; text-align:center'>PCS</th></tr><tbody>";
	$result = $QRY1->result();
	if($result>0){
		foreach($result as $indata){
		$htmldata.="<tr><td style='padding:2px 2px 2px 2px; text-align:center'>".$indata->weight."</td><td style='padding:2px 2px 2px 2px; text-align:center'>".$indata->pcs."</td></tr>";
		$m++;
		}
	}
	$htmldata.="</tbody></table>";

		
	/** Get User Department **/
				$udep=$this->db->select('b.department')->from('system_users a')->join('departments b','a.department_id=b.department_id')->where('a.user_id',$_SESSION['logged_in']['user_id'])->get(); 
				if($udep->num_rows()>0)
				{
					foreach($udep->result() as $depp);
					$depart=$depp->department;
					
				}else
				{
					$depart='';
				}


	/** Check for dependency **/
				
				$checkdepend=$this->db->select('dependency')->from('fms_flow')->where('flow_id',$flowstage)->where('dependency','1')->get();
				$depend=$checkdepend->num_rows();
				//echo $depend;exit;
				if($depend==1)
				{
					
					$restt=$this->db->select('dependentflowid')->from('flowdependency')->where('flowid',$flowstage)->get();
					if($restt->num_rows()>0)
					{
						$dependcount=$restt->num_rows();
						$donarr=array();
						$donarr[]=0;
						//echo "<pre>";print_r($restt->result());exit;
						foreach($restt->result() as $resttyu)
						{
							$isdone=$this->db->select('id,userstatus')->from('order_stage')->where('flowstage',$resttyu->dependentflowid)->where('jobcardid',$proc->jobcardid)->where('orderid',$proc->originalorderid)->order_by('id','DESC')->limit(1)->get();
							if($isdone->num_rows()>0)
							{
								foreach($isdone->result() as $isdone11);
								$donarr[]=$isdone11->userstatus;
								
							}else
							{
								$markapp=1;
							}
							
						}
						//echo $dependcount.'<br>'."<pre>"; print_r($donarr);exit;
						if($dependcount==array_sum($donarr))
						{
							$markapp=1;
						}else{
							$markapp=0;
						}
						
					}else{
						$markapp=1;
					}
					
				}else{
					
					$markapp=1;
				}
				/** End **/
				/** New Timestamp query **/
				$previousorder=$tatstage;
				if($previousorder==0)
				{
					$previouscomtime=$proc->plannedOn;
					$previouscomdate=date('d-M-Y',strtotime($previouscomtime))."<br>";
					$previouscomtimeonly=date('g:i A',strtotime($previouscomtime));
					
				}else
				{
					//echo $previousorder;exit;
					$oldtime1=$this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$proc->jobcardid)->order_by('id','DESC')->limit(1)->get();
							if($oldtime1->num_rows()>0){
								foreach($oldtime1->result() as $pastinfo);
								$previouscomtime=$pastinfo->addedOn;
								$previouscomdate=date('d-M-Y',strtotime($pastinfo->addedOn))."<br>";
								$previouscomtimeonly=date('g:i A',strtotime($pastinfo->addedOn));
							}else
							{
								$previouscomtime=$proc->plannedOn;
					$previouscomdate=date('d-M-Y',strtotime($previouscomtime))."<br>";
					$previouscomtimeonly=date('g:i A',strtotime($previouscomtime));
							}
				}

		
				
				/** End **/
				
				if($markapp==1)
				{
					/** Holiday Check**/
				
					$TATDATEFORHOLIDAYCHECK=date('Y-m-d', strtotime($TATDATE));
					$timestampforcheck=date('Y-m-d',strtotime($previouscomtime));
					$date_from=strtotime($timestampforcheck);
					$date_to=strtotime($TATDATEFORHOLIDAYCHECK);
					$betweendates=array();
					for ($g=$date_from; $g<=$date_to; $g+=86400) {  
						$betweendates[]= date("Y-m-d", $g);  
					} 
				
				//echo "<pre>"; print_r($betweendates);
					if(count($betweendates)>0)
				{
					
					$alldates = "'" . implode ( "', '", $betweendates ) . "'";
					
					$ifholiday=$this->db->select('holiday_id')->from('prestogroup_holidays')->where_in('holiday_date',$alldates,false)->get();
					if($ifholiday->num_rows()>0)
					{
			
						//echo "hi".$proc->job_card_no;
						$TATDATEFORHOLIDAYCHECK=date('Y-m-d', strtotime($TATDATEFORHOLIDAYCHECK."+".$ifholiday->num_rows()." days"));
						//echo $TATDATEFORHOLIDAYCHECK;
						
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
				//$TATDATE= date('d-M-Y', strtotime($nextdate."+1 days"));
				$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();
				if($query->num_rows()>0){
					
					foreach($query->result() as $afterholiday);
					$aftrholidaydate = $afterholiday->holiday_date;
					$TATDATE= date('d-M-Y', strtotime($aftrholidaydate."+1 days"));
					
				}else {
				$TATDATE= date('d-M-Y', strtotime($nextdate));
				}
			}else{
			$TATDATE=date('d-M-Y', strtotime($TATDATEFORHOLIDAYCHECK));
			}
					
				/** End **/
	$scheduler_data[] = array('sr_no'=>$i,
	'orderdate'=>$previouscomdate.$previouscomtimeonly,
	'mname'=>strtoupper($proc->instruments_name),
	'jobcard'=>strtoupper($proc->job_card_no),
	'factory'=>strtoupper($depart),
	'planned'=>date('d-M-Y',strtotime($proc->plannedOn)).$addedtime1,
	'tat'=>$TATDATE."<br>".$previouscomtimeonly,
	'actual'=>$completedate.$addedfinaltime,
	'totdays'=>$totalday,
	'challan'=>$challanno,
	'weight'=>$weight,
	'pcs'=>$pcs,
	'htmldata'=>$htmldata,
	'status'=>$prestui,
	'action'=>$html);
	 
	 
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

	}
	} 

		
    	function materialpaintinoldbeforefmstatdate()
    	{
    		
    	$orderstage=$this->uri->segment(3);
    	$flowstage=$this->uri->segment(4);
    	$orderid=$this->uri->segment(5);
    	$selforder=$this->fmsmodel->getselforder($orderid);
    	$productionflowid=$this->uri->segment(6);
    	$user_id =$this->session->userdata['logged_in']['user_id'];
    	$newstage=[];
    	$isfinalstep=0;
    	$jobcardid=$this->input->post('jobcardid'.$orderstage);
    	$challanno=$this->input->post('challanno'.$orderstage);
    	$weight=$this->input->post('weight'.$orderstage);
    	$pcs=$this->input->post('pcs'.$orderstage);
    	$outweight=$this->input->post('outweight'.$orderstage);
    	$outpcs=$this->input->post('outpcs'.$orderstage);
    
    		
    		/** Move to next stage **/
    			$currentstage=$flowstage;
    			$getset=$this->db->select('setorder,finalstep')->from('fms_flow')->where('flow_id',$currentstage)->where('production_flow_id',$productionflowid)->get();
    			
    				foreach($getset->result() as $ggetset);
    				$currentsetorder=$ggetset->setorder;
    				$isfinalstep=$ggetset->finalstep;
    				/** Get Next Order **/
    				$getneworder=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id',$productionflowid)->where('setorder>',$currentsetorder)->limit(1)->order_by('setorder','ASC')->get();
    				if($getneworder->num_rows()>0)
    			{
    				foreach($getneworder->result() as $getneworder1);
    				/** Check Move **/	
    				$restymove=$this->db->select('moveto')->from('fms_flow')->where('flow_id',$flowstage)->get();
    				foreach($restymove->result() as $restymove1)
    				{
    					//echo $restymove1->moveto;exit;
    				if(($restymove1->moveto<>0) && ($restymove1->moveto<>''))
    				{
    				$moveflow=$restymove1->moveto;
    				$movearr=explode(',',$moveflow);
    				foreach($movearr as $movea)
    				{
    				$newstage[]=$movea;
    				}
    				
    				} else
    				{
    			
    					$newstage[]=$getneworder1->flow_id;
    				} 
    				}
    			
    			
    			}
    				/** End **/
    
    			//echo "<pre>"; print_r($newstage);exit;
    			
    				
    				/** get previous in Paint pcs **/
    			
    				$previnpcs=array();
    				$restttprevpaintin=$this->db->select('pcs')->from('material_paint_in')->where('jobcardid',$jobcardid)->get();
    				if($restttprevpaintin->num_rows()>0)
    				{
    				foreach($restttprevpaintin->result() as $restuy)
    				{
    
    				$previnpcs[]=$restuy->pcs;
    				}
    				$totalprvpcs=array_sum($previnpcs);
    				}else 
    				{  
    				$totalprvpcs=0;
    				}
    				/** End **/
    				
    			/** Detail Entery **/
    			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'challanno'=>$challanno,'weight'=>$weight,'pcs'=>$pcs,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
    			$this->db->insert('material_paint_in',$detailentry);
    			$effrow1=$this->db->affected_rows();	
    			
    			/** End**/
    			if($effrow1>0)
    			{
    				
    			$overallpcs=$totalprvpcs+$pcs;
    			//echo $overallpcs;exit;
    		if($overallpcs==$outpcs)
    		{
    			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
    			$this->db->where('id',$orderstage);
    			$this->db->where('orderid',$orderid);
    			$this->db->update('order_stage',$data);
    			$effrow=$this->db->affected_rows();	
    		if($effrow>0)
    		{
    			if(count($newstage)>0)
    			{
    			for($u=0;$u<count($newstage);$u++)
    			{
    			
    	$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$newstage[$u])->where('userstatus','0')->get();
    				
    			if($checkodd->num_rows()==0)
    			{	
    		if($newstage[$u]<>0)
    	{
    		$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage[$u],'userstatus'=>'0');
    			$this->db->insert('order_stage',$newdata);
    			/** Add Tat **/
    						$stageid=$this->db->insert_id();
    						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
    						if($settatdate<>'')
    						{
    							/** Check if exist **/
    							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
    						if($checkfmstat->num_rows()==0)
    						{
    						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
    						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
    						$this->db->insert('fmstatdate',$settat);
    						}else
    						{
    							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
    						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'));
    						$this->db->where('flowstage',$newstage[$u]);
    						$this->db->where('jobcardid',$jobcardid);
    						$this->db->update('fmstatdate',$settat);
    						}
    						}
    						/** END **/
    		}
    		}else{
    			
    			foreach($checkodd->result() as $checkodd1);
    			
    			/** Add Tat **/
    						$stageid=$checkodd1->id;
    						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
    						if($settatdate<>'')
    						{
    							/** Check if exist **/
    							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
    						if($checkfmstat->num_rows()==0)
    						{
    						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
    						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
    						$this->db->insert('fmstatdate',$settat);
    						}else
    						{
    							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
    						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'));
    						$this->db->where('flowstage',$newstage[$u]);
    						$this->db->where('jobcardid',$jobcardid);
    						$this->db->update('fmstatdate',$settat);
    						}
    						}
    						/** END **/
    			
    		}
    			}
    			
    				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Moved to Next Process</span></div>');
    				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
    				
    			}else{
    				
    				if($isfinalstep!=0)
    				{
    					
    						/** Check if Merge is available **/
    				  $merger=$this->db->select('id,mergewith,flowid')->from('fmsmerge')->where('productionflow',$productionflowid)->get();
    					if($merger->num_rows()>0)
    					{
    						foreach($merger->result() as $mergerdata);
    						$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$mergerdata->flowid)->where('userstatus','0')->get();
    			if($checkodd->num_rows()==0)
    			{
    						$orderstmergedata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$mergerdata->flowid,'userstatus'=>'0','remarks'=>'','addedOn'=>date('Y-m-d H:i:s'));
    						$this->db->insert('order_stage',$orderstmergedata); 
    				        $odm=$this->db->insert_id();
    						/** Add Tat **/
    						$stageid=$odm;
    						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
    						if($settatdate<>'')
    						{
    							/** Check if exist **/
    							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
    						if($checkfmstat->num_rows()==0)
    						{
    						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
    						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
    						$this->db->insert('fmstatdate',$settat);
    						}else
    						{
    							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
    						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'));
    						$this->db->where('flowstage',$mergerdata->flowid);
    						$this->db->where('jobcardid',$jobcardid);
    						$this->db->update('fmstatdate',$settat);
    						}
    						}
    						/** END **/
    			}else
    			{
    				foreach($checkodd->result() as $prevorderstage);
    				$odm=$prevorderstage->id;
    				/** Add Tat **/
    						$stageid=$odm;
    						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
    						if($settatdate<>'')
    						{
    							/** Check if exist **/
    							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
    						if($checkfmstat->num_rows()==0)
    						{
    						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
    						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
    						$this->db->insert('fmstatdate',$settat);
    						}else
    						{
    							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
    						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'));
    						$this->db->where('flowstage',$mergerdata->flowid);
    						$this->db->where('jobcardid',$jobcardid);
    						$this->db->update('fmstatdate',$settat);
    						}
    						}
    						/** END **/
    				
    				
    			}
    					
    						if($odm<>0)
    						{
    							$mergehistdata=array('orderstageid'=>$odm,'oldproductionflow'=>$productionflowid,'jobcardid'=>$jobcardid,'newproductionflow'=>$mergerdata->mergewith,'oldfmsflow'=>$flowstage,'newfmsflow'=>$mergerdata->flowid,'addedOn'=>date('Y-m-d H:i:s'));
    							$this->db->insert('fmsmergehistory',$mergehistdata);
    							
    							$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Merged.</span></div>');
    							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
    							
    							}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to merge flow.</span></div>');
    							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
    					}else { 
    					$newdata=array('complete'=>'1');
    					$this->db->where('order_id',$orderid);
    					$this->db->where('id',$jobcardid);
    					$this->db->update('order_instruments',$newdata);
    					if($selforder=='1')
    					{
    					$this->fmsmodel->addstock($jobcardid);
    					}
    
    $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
    			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);					
    					}
    					/** Merge **/
    					$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
    			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
    				}else
    				{
    				
    				/** End **/
    				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
    			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
    				}
    				
    				
    			}
    				
    			}else{
    			
    			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
    				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
    		}
    		
    		}else{
    			
    			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Material IN details Updated but order is still pending.</span></div>');
    				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
    		}
    		
    		}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
    		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
    				
    			
    
    	}
    	
    		function materialpaintin()
	{
		
	$orderstage=$this->uri->segment(3);
	$flowstage=$this->uri->segment(4);
	$orderid=$this->uri->segment(5);
	$selforder=$this->fmsmodel->getselforder($orderid);
	$productionflowid=$this->uri->segment(6);
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$newstage=[];
	$isfinalstep=0;
	$jobcardid=$this->input->post('jobcardid'.$orderstage);
	$challanno=$this->input->post('challanno'.$orderstage);
	$weight=$this->input->post('weight'.$orderstage);
	$pcs=$this->input->post('pcs'.$orderstage);
	$outweight=$this->input->post('outweight'.$orderstage);
	$outpcs=$this->input->post('outpcs'.$orderstage);

		
		/** Move to next stage **/
			$currentstage=$flowstage;
			$getset=$this->db->select('setorder,finalstep,total_days,set_time')->from('fms_flow')->where('flow_id',$currentstage)->where('production_flow_id',$productionflowid)->get();
			
				foreach($getset->result() as $ggetset);
				$currentsetorder=$ggetset->setorder;
				$isfinalstep=$ggetset->finalstep;
				$tatday=$ggetset->total_days;
				$tathours=$ggetset->set_time;
				/** Get Next Order **/
				$getneworder=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id',$productionflowid)->where('setorder>',$currentsetorder)->limit(1)->order_by('setorder','ASC')->get();
				if($getneworder->num_rows()>0)
			{
				foreach($getneworder->result() as $getneworder1);
				/** Check Move **/	
				$restymove=$this->db->select('moveto')->from('fms_flow')->where('flow_id',$flowstage)->get();
				foreach($restymove->result() as $restymove1)
				{
					//echo $restymove1->moveto;exit;
				if(($restymove1->moveto<>0) && ($restymove1->moveto<>''))
				{
				$moveflow=$restymove1->moveto;
				$movearr=explode(',',$moveflow);
				foreach($movearr as $movea)
				{
				$newstage[]=$movea;
				}
				
				} else
				{
			
					$newstage[]=$getneworder1->flow_id;
				} 
				}
			
			
			}
				/** End **/

			//echo "<pre>"; print_r($newstage);exit;
			
				
				/** get previous in Paint pcs **/
			
				$previnpcs=array();
				$restttprevpaintin=$this->db->select('pcs')->from('material_paint_in')->where('jobcardid',$jobcardid)->get();
				if($restttprevpaintin->num_rows()>0)
				{
				foreach($restttprevpaintin->result() as $restuy)
				{

				$previnpcs[]=$restuy->pcs;
				}
				$totalprvpcs=array_sum($previnpcs);
				}else 
				{  
				$totalprvpcs=0;
				}
				/** End **/
				
			/** Detail Entery **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'challanno'=>$challanno,'weight'=>$weight,'pcs'=>$pcs,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->insert('material_paint_in',$detailentry);
			$effrow1=$this->db->affected_rows();	
			
			/** End**/
			if($effrow1>0)
			{
				
			$overallpcs=$totalprvpcs+$pcs;
			//echo $overallpcs;exit;
		if($overallpcs==$outpcs)
		{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);
			$effrow=$this->db->affected_rows();	
		if($effrow>0)
		{
			if(count($newstage)>0)
			{
			for($u=0;$u<count($newstage);$u++)
			{
			
	$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$newstage[$u])->where('userstatus','0')->get();
				
			if($checkodd->num_rows()==0)
			{	
		if($newstage[$u]<>0)
	{
		$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage[$u],'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
			/** Add Tat **/
						$stageid=$this->db->insert_id();
						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$newstage[$u]);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
		}
		}else{
			
			foreach($checkodd->result() as $checkodd1);
			
			/** Add Tat **/
						$stageid=$checkodd1->id;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$newstage[$u]);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			
		}
			}
			
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Moved to Next Process</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				
			}else{
				
				if($isfinalstep!=0)
				{
					
						/** Check if Merge is available **/
				  $merger=$this->db->select('id,mergewith,flowid')->from('fmsmerge')->where('productionflow',$productionflowid)->get();
					if($merger->num_rows()>0)
					{
						foreach($merger->result() as $mergerdata);
						$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$mergerdata->flowid)->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
						$orderstmergedata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$mergerdata->flowid,'userstatus'=>'0','remarks'=>'','addedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('order_stage',$orderstmergedata); 
				        $odm=$this->db->insert_id();
						/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			}else
			{
				foreach($checkodd->result() as $prevorderstage);
				$odm=$prevorderstage->id;
				/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
				
				
			}
					
						if($odm<>0)
						{
							$mergehistdata=array('orderstageid'=>$odm,'oldproductionflow'=>$productionflowid,'jobcardid'=>$jobcardid,'newproductionflow'=>$mergerdata->mergewith,'oldfmsflow'=>$flowstage,'newfmsflow'=>$mergerdata->flowid,'addedOn'=>date('Y-m-d H:i:s'));
							$this->db->insert('fmsmergehistory',$mergehistdata);
							
							$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Merged.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
							
							}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to merge flow.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
					}else { 
					$newdata=array('complete'=>'1');
					$this->db->where('order_id',$orderid);
					$this->db->where('id',$jobcardid);
					$this->db->update('order_instruments',$newdata);


					/** IF THIS IS SALESFORCE ORDER **/
					$this->checkifthisissforderandlastonetogetcompleted($orderid);

					/** END **/

					if($selforder=='1')
					{
					$this->fmsmodel->addstock($jobcardid);
					}

$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);					
					}
					/** Merge **/
					$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}else
				{
				
				/** End **/
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}
				
				
			}
				
			}else{
			
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
		
		}else{
			
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Material IN details Updated but order is still pending.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
		
		}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
				
			

	}
	
    	
	function materialpaintinold28jan2020()
	{
		
	$orderstage=$this->uri->segment(3);
	$flowstage=$this->uri->segment(4);
	$orderid=$this->uri->segment(5);
	$productionflowid=$this->uri->segment(6);
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$newstage=[];
	$jobcardid=$this->input->post('jobcardid'.$orderstage);
	$challanno=$this->input->post('challanno'.$orderstage);
	$weight=$this->input->post('weight'.$orderstage);
	$pcs=$this->input->post('pcs'.$orderstage);
	$outweight=$this->input->post('outweight'.$orderstage);
	$outpcs=$this->input->post('outpcs'.$orderstage);

		
		/** Move to next stage **/
			$currentstage=$flowstage;
			$getset=$this->db->select('setorder')->from('fms_flow')->where('flow_id',$currentstage)->get();
			if($getset->num_rows()>0)
			{
				foreach($getset->result() as $ggetset);
				$currentsetorder=$ggetset->setorder;	
				/** Get Next Order **/
				$getneworder=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id',$productionflowid)->where('setorder>',$currentsetorder)->limit(1)->order_by('setorder','ASC')->get();
				if($getneworder->num_rows()>0)
			{
				foreach($getneworder->result() as $getneworder1);
				/** Check Move **/	
				$restymove=$this->db->select('moveto')->from('fms_flow')->where('flow_id',$flowstage)->get();
				foreach($restymove->result() as $restymove1)
				{
					//echo $restymove1->moveto;exit;
				if(($restymove1->moveto<>0) && ($restymove1->moveto<>''))
				{
				$moveflow=$restymove1->moveto;
				$movearr=explode(',',$moveflow);
				foreach($movearr as $movea)
				{
				$newstage[]=$movea;
				}
				
				} else
				{
			
					$newstage[]=$getneworder1->flow_id;
				} 
				}
			
			
			}else{
				
				$this->session->set_flashdata('message','Next Flow Not Defined');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			}
				/** End **/
	}else{
				$this->session->set_flashdata('message','Next Flow Not Defined');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			}
			//echo "<pre>"; print_r($newstage);exit;
			if(count($newstage)>0)
			{
				
				/** get previous in Paint pcs **/
			
				$previnpcs=array();
				$restttprevpaintin=$this->db->select('pcs')->from('material_paint_in')->where('jobcardid',$jobcardid)->get();
				if($restttprevpaintin->num_rows()>0)
				{
				foreach($restttprevpaintin->result() as $restuy)
				{

				$previnpcs[]=$restuy->pcs;
				}
				$totalprvpcs=array_sum($previnpcs);
				}else 
				{  
				$totalprvpcs=0;
				}
				/** End **/
				
			/** Detail Entery **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'challanno'=>$challanno,'weight'=>$weight,'pcs'=>$pcs,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->insert('material_paint_in',$detailentry);
			$effrow1=$this->db->affected_rows();	
			
			/** End**/
			if($effrow1>0)
			{
				
			$overallpcs=$totalprvpcs+$pcs;
			//echo $overallpcs;exit;
		if($overallpcs==$outpcs)
		{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);
			$effrow=$this->db->affected_rows();	
		if($effrow>0)
		{
			for($u=0;$u<count($newstage);$u++)
			{
			
	$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$newstage[$u])->where('userstatus','0')->get();
				
			if($checkodd->num_rows()==0)
			{	
		if($newstage[$u]<>0)
	{
		$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage[$u],'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
		}
		}
			}
			
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Moved to Next Process</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}else{
			
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
		
		}else{
			
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Material IN details Updated but order is still pending.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
		
		}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
				
			}else{
				
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			}

	}



		function machining_material_out_platingprocess()
	{
				$scheduler_data=array();
	$flowstage=$this->uri->segment(3);
	$productionflowid=$this->uri->segment(4);
	/*GET CURRENT PROCESS SORT ORDER*/
				$QRY = $this->db->select('total_days,tat,set_time, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();
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
			$currentprocessorder = $flowstage;
		$setorder=$fmsinformation->setorder;
	/*GET CURRENT PROCESS SORT ORDER*/
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$fmsprocess=$productionflowid;
	/** END **/
	if($fmsprocess<>0)
	{
	/** Get Process Name **/
	$process=$this->db->select('d.file_number,f.ordertype,f.fileno,d.file_number,a.jumpfabricationappl,a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow,d.fabrication,f.planstartsfrom')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('a.who_wedo',$user_id)->order_by('b.userstatus','ASC')->order_by('b.addedOn','DESC')->get();
	/** End **/
	if($process->num_rows()>0)
	{
	// echo "<pre>"; print_r($process->result());exit;
	$i=1;
	foreach($process->result() as $proc)
	{


$ordertype = $proc->ordertype;
			if($ordertype=='1'){
				
				$fileno=$proc->file_number;
			}else{
			
				$fileno=$proc->fileno;
			}

	if($proc->userstatus=='0')
	{
	$prestui="<span class='btn btn-danger btn-xs'>Update Pending</span>";
	//$action='<a href="javascript:;" onclick="markstagedone('."'".$proc->orderstageid."'".','."'".$proc->flowstage."'".','."'".$proc->originalorderid."'".','."'".$proc->jobcardid."'".')"><span class="btn btn-xs btn-warning">Mark as done</span></a>';
	$html='<button class="btn btn-xs btn-warning waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$proc->orderstageid.'">Mark as Done</button>';

	$html.= ' <div id="con-close-modal'.$proc->orderstageid.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<form action="'.page_url.'Orderstage/materialplatingout/'.$proc->orderstageid.'/'.$proc->flowstage.'/'.$proc->originalorderid.'/'.$fmsprocess.'" method="post" id="loginForm" enctype="multipart/form-data">
		
	  <div id="pageloader">
	   <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
	</div>
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
												<h4 class="modal-title">MATERIAL OUT (PLATING)</h4>
											</div>
											<div class="modal-body">
										
												<div class="row">
												
												
													<div class="col-md-6">
														<div class="form-group">
															<label for="field-2" class="control-label">JOBCARD NUMBER<span style="color:red">*</span></label>
															
															<input type="text" style="width:250px" class="form-control" id="jobcard_no'.$proc->orderstageid.'" style="text-transform: uppercase;" name="jobcard_no'.$proc->orderstageid.'" placeholder="" value="'.$proc->job_card_no.'" readonly>
															<input type="hidden" name="jobcardid'.$proc->orderstageid.'" value="'.$proc->jobcardid.'">
														</div>
													</div>
													
													 <!--<div class="col-md-6">
														<div class="form-group">
															<label for="field-2" class="control-label">INSTRUMENT NAME<span style="color:red">*</span></label>
															
															<input type="text" style="width:250px" class="form-control" id="jobcard_no'.$proc->orderstageid.'" style="text-transform: uppercase;" name="jobcard_no'.$proc->orderstageid.'" placeholder="" value="'.$proc->instruments_name.'" readonly>
															<input type="hidden" name="jobcardid'.$proc->orderstageid.'" value="'.$proc->jobcardid.'">
															<input type="hidden" name="instrumentid'.$proc->orderstageid.'" value="'.$proc->item_id.'">
														</div>
													</div>-->

													<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">CHALLAN NO.<span style="color:red">*</span></label><br>

													<input type="text" class="form-control" id="challanno'.$proc->orderstageid.'" name="challanno'.$proc->orderstageid.'" required style="width:250px">
													</div>
													</div>
													
													
													<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">WEIGHT<span style="color:red">*</span></label><br>

													<input type="text" class="form-control" id="weight'.$proc->orderstageid.'" name="weight'.$proc->orderstageid.'" required style="width:250px" placeholder="IN Kg">
													</div>
													</div>
													
													
													<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">PCS<span style="color:red">*</span></label><br>

													<input type="number" class="form-control" id="pcs'.$proc->orderstageid.'" name="pcs'.$proc->orderstageid.'" required style="width:250px" min="0">
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


	$completedate="";
	$addedtimefinal="";
	}else{
	$compdate=date('d-M-Y H:i:s',strtotime($proc->stagecompletedate));
	$prestui="<span class='btn btn-success btn-xs'>Remarks Updated</span>";
	$action="Task completed on ".$compdate;
	$html="This Task has been done by You.";
	date_default_timezone_set("Asia/Kolkata");
	$completedate = date('d-M-Y', strtotime($proc->stagecompletedate));
	$completetime = date('H:i:s', strtotime($proc->stagecompletedate));
	$addedtimefinal = "<br>". date('g:i A', strtotime($completetime));


	}
	


	$previousorder = $tatstage;

	$QRY = $this->db->select('jobcardid, challanno, weight, pcs')->from('material_plating_out')->where('jobcardid',$proc->jobcardid)->get();
	$res = $QRY->result();
	if($QRY->num_rows()>0){
	foreach($QRY->result() as $outinfo);
	$challanno = $outinfo->challanno;
	$weight = $outinfo->weight;
	$pcs = $outinfo->pcs;
	}else{
	$challanno="";
	$weight="";
	$pcs="";	
	}


		/** Get User Department **/
				$udep=$this->db->select('b.department')->from('system_users a')->join('departments b','a.department_id=b.department_id')->where('a.user_id',$_SESSION['logged_in']['user_id'])->get(); 
				if($udep->num_rows()>0)
				{
					foreach($udep->result() as $depp);
					$depart=$depp->department;
					
				}else
				{
					$depart='';
				}
				
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
					
					/** End **/
					
					/** Check if instrument requires fabrication **/
					/**$ryt=$this->db->select('b.fabrication')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->where('a.id',$proc->jobcardid)->get();
					if($ryt->num_rows()>0)
					{
						foreach($ryt->result() as $rtyeyte);
						$fabreq=$rtyeyte->fabrication;
						
					}else{ $fabreq=0 ;} **/
					
					/** if($fabreq==0)
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
				/** End **/
			
		/** Check for merge fms intersection **/
				if($markapp==1)
				{
					
				$markapp=$this->fmsmodel->checkformergerpoint($flowstage,$proc->jobcardid,$proc->originalorderid,$proc->fabrication,$proc->jumpfabricationappl);
				}
				

				
				/** End **/
				
	if($markapp==1)
	{
		
			$timestamp=$this->getprevioustimestamp($tatstage,$proc->plannedOn,$proc->jobcardid,$flowstage,$proc->orderstageid);
			$tattime = DateTime::createFromFormat('d-M-Y g:i A', $timestamp);
			$finaltattime=$tattime->format('g:i A');
			$tatdate=$this->getupcomingtatdatenew($tatstage,$proc->plannedOn,$proc->jobcardid,$timestamp,$totaldaysslave,$proc->orderstageid,$day);
			if($proc->userstatus=='0')
			{
			$completedon="";
			}else
			{
			$completedon=date('d-M-Y g:i A',strtotime($proc->stagecompletedate));
			}
	
		$odtype=$this->getordertype($proc->originalorderid);
	$scheduler_data[] = array('sr_no'=>$i,
	'orderdate'=>$timestamp,
	'odtype'=>$odtype,
	'mname'=>strtoupper($proc->instruments_name),
	'jobcard'=>strtoupper($proc->job_card_no),
	'fileno'=>$fileno,
	'factory'=>strtoupper($depart),
	'tat'=>$tatdate,
	'actual'=>$completedon,
	'totdays'=>'',
	'challan'=>$challanno,
	'weight'=>$weight,
	'pcs'=>$pcs,
	'status'=>$prestui,
	'action'=>$html);
	 
	 
	$i++;
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
	} 



	function machining_material_out_platingprocessoldtempp()
	{
	$flowstage=$this->uri->segment(3);
	$productionflowid=$this->uri->segment(4);
	/*GET CURRENT PROCESS SORT ORDER*/
			$QRY = $this->db->select('total_days, tat, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();
			$res = $QRY->result();
			foreach($res as $fmsinformation);
			$totaldaysslave = $fmsinformation->total_days;
			$tatstage = $fmsinformation->tat;
			$currentprocessorder = $flowstage;
		$setorder=$fmsinformation->setorder;
	/*GET CURRENT PROCESS SORT ORDER*/
	$user_id =$this->session->userdata['logged_in']['user_id'];
	/**$isfms=$this->db->select('fms_process')->from('system_users')->where('user_id',$user_id)->get();
	if($isfms->num_rows()>0)
	{
	foreach($isfms->result() as $isfmsa);
	$fmsprocess=$isfmsa->fms_process;
	}else{ $fmsprocess=0; }**/
	$fmsprocess=$productionflowid;
	/** END **/
	if($fmsprocess<>0)
	{
	/** Get Process Name **/
	$process=$this->db->select('a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('a.who_wedo',$user_id)->order_by('b.userstatus','ASC')->order_by('b.addedOn','DESC')->get();
	/** End **/
	if($process->num_rows()>0)
	{
	// echo "<pre>"; print_r($process->result());exit;
	$i=1;
	foreach($process->result() as $proc)
	{
	if($proc->userstatus=='0')
	{
	$prestui="<span class='btn btn-danger btn-xs'>Update Pending</span>";
	//$action='<a href="javascript:;" onclick="markstagedone('."'".$proc->orderstageid."'".','."'".$proc->flowstage."'".','."'".$proc->originalorderid."'".','."'".$proc->jobcardid."'".')"><span class="btn btn-xs btn-warning">Mark as done</span></a>';
	$html='<button class="btn btn-xs btn-warning waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$proc->orderstageid.'">Mark as Done</button>';

	$html.= ' <div id="con-close-modal'.$proc->orderstageid.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<form action="'.page_url.'Orderstage/materialplatingout/'.$proc->orderstageid.'/'.$proc->flowstage.'/'.$proc->originalorderid.'/'.$fmsprocess.'" method="post" id="loginForm" enctype="multipart/form-data">
		
	  <div id="pageloader">
	   <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
	</div>
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
												<h4 class="modal-title">MATERIAL OUT (PLATING)</h4>
											</div>
											<div class="modal-body">
										
												<div class="row">
												
												
													<div class="col-md-6">
														<div class="form-group">
															<label for="field-2" class="control-label">JOBCARD NUMBER<span style="color:red">*</span></label>
															
															<input type="text" style="width:250px" class="form-control" id="jobcard_no'.$proc->orderstageid.'" style="text-transform: uppercase;" name="jobcard_no'.$proc->orderstageid.'" placeholder="" value="'.$proc->job_card_no.'" readonly>
															<input type="hidden" name="jobcardid'.$proc->orderstageid.'" value="'.$proc->jobcardid.'">
														</div>
													</div>
													
													 <!--<div class="col-md-6">
														<div class="form-group">
															<label for="field-2" class="control-label">INSTRUMENT NAME<span style="color:red">*</span></label>
															
															<input type="text" style="width:250px" class="form-control" id="jobcard_no'.$proc->orderstageid.'" style="text-transform: uppercase;" name="jobcard_no'.$proc->orderstageid.'" placeholder="" value="'.$proc->instruments_name.'" readonly>
															<input type="hidden" name="jobcardid'.$proc->orderstageid.'" value="'.$proc->jobcardid.'">
															<input type="hidden" name="instrumentid'.$proc->orderstageid.'" value="'.$proc->item_id.'">
														</div>
													</div>-->

													<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">CHALLAN NO.<span style="color:red">*</span></label><br>

													<input type="text" class="form-control" id="challanno'.$proc->orderstageid.'" name="challanno'.$proc->orderstageid.'" required style="width:250px">
													</div>
													</div>
													
													
													<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">WEIGHT<span style="color:red">*</span></label><br>

													<input type="text" class="form-control" id="weight'.$proc->orderstageid.'" name="weight'.$proc->orderstageid.'" required style="width:250px" placeholder="IN Kg">
													</div>
													</div>
													
													
													<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">PCS<span style="color:red">*</span></label><br>

													<input type="number" class="form-control" id="pcs'.$proc->orderstageid.'" name="pcs'.$proc->orderstageid.'" required style="width:250px" min="0">
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


	$completedate="";
	$addedtimefinal="";
	}else{
	$compdate=date('d-M-Y H:i:s',strtotime($proc->stagecompletedate));
	$prestui="<span class='btn btn-success btn-xs'>Remarks Updated</span>";
	$action="Task completed on ".$compdate;
	$html="This Task has been done by You.";
	date_default_timezone_set("Asia/Kolkata");
	$completedate = date('d-M-Y', strtotime($proc->stagecompletedate));
	$completetime = date('H:i:s', strtotime($proc->stagecompletedate));
	$addedtimefinal = "<br>". date('g:i A', strtotime($completetime));


	}
	date_default_timezone_set("Asia/Kolkata");
	$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
	$addedtime = "<br>". date('g:i A', strtotime($time));

	$time1=date('H:i:s', strtotime($proc->plannedOn));
	$addedtime1 = "<br>". date('g:i A', strtotime($time1));
	$planneddate1=date('Y-m-d',strtotime($proc->plannedOn));

	/*GET DATE OF PRIVIOUS STEPS*/
						$previousorder = $tatstage;
						
					if($previousorder<>0){
						$abc = $this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$proc->jobcardid)->order_by('id','DESC')->limit(1)->get();
							if($abc->num_rows()>0){
						foreach($abc->result() as $pastinfo);
						$complitiontime = $pastinfo->addedOn;
							
								$lasttaskcompletedtime = "<br>". date('g:i A', strtotime($complitiontime));

								
							}else{
							$complitiontime = $proc->added_on;
									$lasttaskcompletedtime = "<br>". date('g:i A', strtotime($complitiontime));

							}
						}else{
						$complitiontime = $proc->plannedOn;	
								$lasttaskcompletedtime = "<br>". date('g:i A', strtotime($complitiontime));

						}
						
						/*PREVIOUS COMPLITION DATE*/
						
						$checkpreviousdate = date('Y-m-d', strtotime($complitiontime));
						$previouscomplitiondate = date('d-M-Y', strtotime($complitiontime));
						$previouscomplitiontime = date('H:i:s', strtotime($proc->added_on));
						$previouscomplitionfinaltime = "<br>". date('g:i A', strtotime($previouscomplitiontime));
				
						/*GET DATE OF PRIVIOUS STEPS*/
						$date11 = new DateTime($previouscomplitiondate); 
						$date22 = new DateTime($completedate); 
						$interval1 = $date11->diff($date22); 
						$ttldays = $interval1->d; 
						if($ttldays=='0'){
						$totalday = "1";
						}else{
						$totalday = $ttldays;
				}
				
				/*TAT Date*/
				$lastcompdate = date('Y-m-d', strtotime($checkpreviousdate));
				$TATDATE=date('d-M-Y', strtotime($lastcompdate."+".$totaldaysslave." days"));
				
				/*TAT Date*/
		
		$QRY = $this->db->select('total_days, tat, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();

	if($this->uri->segment(3)=='1'){
		date_default_timezone_set("Asia/Kolkata");
	$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
	$addedtime = "<br>". date('g:i A', strtotime($time));
		}else{
		$previousorder = $setorder-1; 
		$QRY = $this->db->select('total_days, tat,setorder, flow_id ')->from('fms_flow')->where('setorder',$previousorder)->get();
		if($QRY->num_rows()>0){
		foreach($QRY->result() as $paststepdate);
			$flowid = $paststepdate->flow_id;
		$QRY1 = $this->db->select('jobcardid, flowstage,addedOn')->from('order_stage')->where('jobcardid',$proc->jobcardid)->where('flowstage',$flowid)->get();	
		if($QRY1->num_rows()>0){
		foreach($QRY1->result() as $timestamp){
		$addeddate1 = date('Y-m-d', strtotime($timestamp->addedOn));
	$time = date('H:i:s', strtotime($timestamp->addedOn));
	$addedtime = "<br>". date('g:i A', strtotime($time));
		}
		}	
		}else{
		$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
		}
		}

	$QRY = $this->db->select('jobcardid, challanno, weight, pcs')->from('material_plating_out')->where('jobcardid',$proc->jobcardid)->get();
	$res = $QRY->result();
	if($QRY->num_rows()>0){
	foreach($QRY->result() as $outinfo);
	$challanno = $outinfo->challanno;
	$weight = $outinfo->weight;
	$pcs = $outinfo->pcs;
	}else{
	$challanno="";
	$weight="";
	$pcs="";	
	}


		/** Get User Department **/
				$udep=$this->db->select('b.department')->from('system_users a')->join('departments b','a.department_id=b.department_id')->where('a.user_id',$_SESSION['logged_in']['user_id'])->get(); 
				if($udep->num_rows()>0)
				{
					foreach($udep->result() as $depp);
					$depart=$depp->department;
					
				}else
				{
					$depart='';
				}
				
				/** Check for dependency **/
				
				$checkdepend=$this->db->select('dependency')->from('fms_flow')->where('flow_id',$flowstage)->where('dependency','1')->get();
				$depend=$checkdepend->num_rows();
				//echo $depend;exit;
				if($depend==1)
				{
					
					$restt=$this->db->select('dependentflowid')->from('flowdependency')->where('flowid',$flowstage)->get();
					if($restt->num_rows()>0)
					{
						$dependcount=$restt->num_rows();
						$donarr=array();
						$donarr[]=0;
						//echo "<pre>";print_r($restt->result());exit;
						foreach($restt->result() as $resttyu)
						{
							$isdone=$this->db->select('id,userstatus')->from('order_stage')->where('flowstage',$resttyu->dependentflowid)->where('jobcardid',$proc->jobcardid)->where('orderid',$proc->originalorderid)->order_by('id','DESC')->limit(1)->get();
							if($isdone->num_rows()>0)
							{
								foreach($isdone->result() as $isdone11);
								$donarr[]=$isdone11->userstatus;
								
							}else
							{
								$markapp=1;
							}
							
						}
						//echo $dependcount.'<br>'."<pre>"; print_r($donarr);exit;
						if($dependcount==array_sum($donarr))
						{
							$markapp=1;
						}else{
							$markapp=0;
						}
						
					}else{
						$markapp=1;
					}
					
				}else{
					
					$markapp=1;
				}
				/** End **/
				
				/** New Timestamp query **/
				$previousorder=$tatstage;
				if($previousorder==0)
				{
					$previouscomtime=$proc->plannedOn;
					$previouscomdate=date('d-M-Y',strtotime($previouscomtime))."<br>";
					$previouscomtimeonly=date('g:i A',strtotime($previouscomtime));
					
				}else
				{
					//echo $previousorder;exit;
					$oldtime1=$this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$proc->jobcardid)->order_by('id','DESC')->limit(1)->get();
							if($oldtime1->num_rows()>0){
								foreach($oldtime1->result() as $pastinfo);
								$previouscomtime=$pastinfo->addedOn;
								$previouscomdate=date('d-M-Y',strtotime($pastinfo->addedOn))."<br>";
								$previouscomtimeonly=date('g:i A',strtotime($pastinfo->addedOn));
							}else
							{
								$previouscomtime=$proc->plannedOn;
					$previouscomdate=date('d-M-Y',strtotime($previouscomtime))."<br>";
					$previouscomtimeonly=date('g:i A',strtotime($previouscomtime));
							}
				}

		
				
				/** End **/
		
			
	if($markapp==1)
	{
		/** Holiday Check**/
				
					$TATDATEFORHOLIDAYCHECK=date('Y-m-d', strtotime($TATDATE));
					$timestampforcheck=date('Y-m-d',strtotime($previouscomtime));
					$date_from=strtotime($timestampforcheck);
					$date_to=strtotime($TATDATEFORHOLIDAYCHECK);
					$betweendates=array();
					for ($g=$date_from; $g<=$date_to; $g+=86400) {  
						$betweendates[]= date("Y-m-d", $g);  
					} 
				
				//echo "<pre>"; print_r($betweendates);
					if(count($betweendates)>0)
				{
					
					$alldates = "'" . implode ( "', '", $betweendates ) . "'";
					
					$ifholiday=$this->db->select('holiday_id')->from('prestogroup_holidays')->where_in('holiday_date',$alldates,false)->get();
					if($ifholiday->num_rows()>0)
					{
			
						//echo "hi".$proc->job_card_no;
						$TATDATEFORHOLIDAYCHECK=date('Y-m-d', strtotime($TATDATEFORHOLIDAYCHECK."+".$ifholiday->num_rows()." days"));
						//echo $TATDATEFORHOLIDAYCHECK;
						
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
				//$TATDATE= date('d-M-Y', strtotime($nextdate."+1 days"));
				$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();
				if($query->num_rows()>0){
					
					foreach($query->result() as $afterholiday);
					$aftrholidaydate = $afterholiday->holiday_date;
					$TATDATE= date('d-M-Y', strtotime($aftrholidaydate."+1 days"));
					
				}else {
				$TATDATE= date('d-M-Y', strtotime($nextdate));
				}
			}else{
			$TATDATE=date('d-M-Y', strtotime($TATDATEFORHOLIDAYCHECK));
			}
					
				/** End **/
	$scheduler_data[] = array('sr_no'=>$i,
	'orderdate'=>$previouscomdate.$previouscomtimeonly,
	'mname'=>strtoupper($proc->instruments_name),
	'jobcard'=>strtoupper($proc->job_card_no),
	'factory'=>strtoupper($depart),
	'planned'=>date('d-M-Y',strtotime($proc->plannedOn)).$addedtime1,
	'tat'=>$TATDATE."<br>".$previouscomtimeonly,
	'actual'=>$completedate.$addedtimefinal,
	'totdays'=>$totalday,
	'challan'=>$challanno,
	'weight'=>$weight,
	'pcs'=>$pcs,
	'status'=>$prestui,
	'action'=>$html);
	 
	 
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

	}
	} 


	function materialplatingout()
	{
	$orderstage=$this->uri->segment(3);
	$flowstage=$this->uri->segment(4);
	$orderid=$this->uri->segment(5);
	$selforder=$this->fmsmodel->getselforder($orderid);
	$productionflowid=$this->uri->segment(6);
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$newstage=[];
	$isfinalstep=0;
	$jobcardid=$this->input->post('jobcardid'.$orderstage);
	$challanno=$this->input->post('challanno'.$orderstage);
	$weight=$this->input->post('weight'.$orderstage);
	$pcs=$this->input->post('pcs'.$orderstage);

		
		/** Move to next stage **/
			$currentstage=$flowstage;
			$getset=$this->db->select('setorder,finalstep,total_days,set_time')->from('fms_flow')->where('flow_id',$currentstage)->where('production_flow_id',$productionflowid)->get();
			
				foreach($getset->result() as $ggetset);
				$currentsetorder=$ggetset->setorder;
				$isfinalstep=$ggetset->finalstep;	
$tatday=$ggetset->total_days;
				$tathours=$ggetset->set_time;				
				/** Get Next Order **/
				$getneworder=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id',$productionflowid)->where('setorder>',$currentsetorder)->limit(1)->order_by('setorder','ASC')->get();
				if($getneworder->num_rows()>0)
			{
				foreach($getneworder->result() as $getneworder1);
				/** Check Move **/	
				$restymove=$this->db->select('moveto')->from('fms_flow')->where('flow_id',$flowstage)->get();
				foreach($restymove->result() as $restymove1)
				{
					//echo $restymove1->moveto;exit;
				if(($restymove1->moveto<>0) && ($restymove1->moveto<>''))
				{
				$moveflow=$restymove1->moveto;
				$movearr=explode(',',$moveflow);
				foreach($movearr as $movea)
				{
				$newstage[]=$movea;
				}
				
				} else
				{
			
					$newstage[]=$getneworder1->flow_id;
				} 
				}
				
			}
				/** End **/

			/** Detail Entery **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'challanno'=>$challanno,'weight'=>$weight,'pcs'=>$pcs,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->insert('material_plating_out',$detailentry);
			$effrow1=$this->db->affected_rows();	
			
			/** End**/
			if($effrow1>0)
			{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);
			$effrow=$this->db->affected_rows();	
		if(count($newstage)>0)
		{
			for($u=0;$u<count($newstage);$u++)
			{
			
	$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$newstage[$u])->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{		
			
				if($newstage[$u]<>0)
	{
	$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage[$u],'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
			/** Add Tat **/
						$stageid=$this->db->insert_id();
						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$newstage[$u]);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			}
			}else
			{
				foreach($checkodd->result() as $checkodd1);
				/** Add Tat **/
						$stageid=$checkodd1->id;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$newstage[$u]);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
				
			}
			}
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Order Moved to Next Process</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}else{
				
				if($isfinalstep!=0)
				{
					
						/** Check if Merge is available **/
				  $merger=$this->db->select('id,mergewith,flowid')->from('fmsmerge')->where('productionflow',$productionflowid)->get();
					if($merger->num_rows()>0)
					{
						foreach($merger->result() as $mergerdata);
						$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$mergerdata->flowid)->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
						$orderstmergedata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$mergerdata->flowid,'userstatus'=>'0','remarks'=>'','addedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('order_stage',$orderstmergedata); 
				        $odm=$this->db->insert_id();
						/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			}else
			{
				foreach($checkodd->result() as $prevorderstage);
				$odm=$prevorderstage->id;
				
				/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
				
				
			}
					
						if($odm<>0)
						{
							$mergehistdata=array('orderstageid'=>$odm,'oldproductionflow'=>$productionflowid,'jobcardid'=>$jobcardid,'newproductionflow'=>$mergerdata->mergewith,'oldfmsflow'=>$flowstage,'newfmsflow'=>$mergerdata->flowid,'addedOn'=>date('Y-m-d H:i:s'));
							$this->db->insert('fmsmergehistory',$mergehistdata);
							
							$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Merged.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
							
							}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to merge flow.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
					}else { 
					$newdata=array('complete'=>'1');
					$this->db->where('order_id',$orderid);
					$this->db->where('id',$jobcardid);
					$this->db->update('order_instruments',$newdata);
					


					/** IF THIS IS SALESFORCE ORDER **/
					$this->checkifthisissforderandlastonetogetcompleted($orderid);

					/** END **/

					if($selforder=='1')
					{
					$this->fmsmodel->addstock($jobcardid);
					}

$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);					
					}
					/** Merge **/
					$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}else
				{
				
				/** End **/
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}
				
				
			}
		
		}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
				
		

		
	}
	
	function materialplatingoutolsfmstatdate()
	{
	$orderstage=$this->uri->segment(3);
	$flowstage=$this->uri->segment(4);
	$orderid=$this->uri->segment(5);
	$selforder=$this->fmsmodel->getselforder($orderid);
	$productionflowid=$this->uri->segment(6);
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$newstage=[];
	$isfinalstep=0;
	$jobcardid=$this->input->post('jobcardid'.$orderstage);
	$challanno=$this->input->post('challanno'.$orderstage);
	$weight=$this->input->post('weight'.$orderstage);
	$pcs=$this->input->post('pcs'.$orderstage);

		
		/** Move to next stage **/
			$currentstage=$flowstage;
			$getset=$this->db->select('setorder,finalstep')->from('fms_flow')->where('flow_id',$currentstage)->where('production_flow_id',$productionflowid)->get();
			
				foreach($getset->result() as $ggetset);
				$currentsetorder=$ggetset->setorder;
				$isfinalstep=$ggetset->finalstep;			
				/** Get Next Order **/
				$getneworder=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id',$productionflowid)->where('setorder>',$currentsetorder)->limit(1)->order_by('setorder','ASC')->get();
				if($getneworder->num_rows()>0)
			{
				foreach($getneworder->result() as $getneworder1);
				/** Check Move **/	
				$restymove=$this->db->select('moveto')->from('fms_flow')->where('flow_id',$flowstage)->get();
				foreach($restymove->result() as $restymove1)
				{
					//echo $restymove1->moveto;exit;
				if(($restymove1->moveto<>0) && ($restymove1->moveto<>''))
				{
				$moveflow=$restymove1->moveto;
				$movearr=explode(',',$moveflow);
				foreach($movearr as $movea)
				{
				$newstage[]=$movea;
				}
				
				} else
				{
			
					$newstage[]=$getneworder1->flow_id;
				} 
				}
				
			}
				/** End **/

			/** Detail Entery **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'challanno'=>$challanno,'weight'=>$weight,'pcs'=>$pcs,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->insert('material_plating_out',$detailentry);
			$effrow1=$this->db->affected_rows();	
			
			/** End**/
			if($effrow1>0)
			{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);
			$effrow=$this->db->affected_rows();	
		if(count($newstage)>0)
		{
			for($u=0;$u<count($newstage);$u++)
			{
			
	$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$newstage[$u])->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{		
			
				if($newstage[$u]<>0)
	{
	$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage[$u],'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
			/** Add Tat **/
						$stageid=$this->db->insert_id();
						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->where('flowstage',$newstage[$u]);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			}
			}else
			{
				foreach($checkodd->result() as $checkodd1);
				/** Add Tat **/
						$stageid=$checkodd1->id;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->where('flowstage',$newstage[$u]);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
				
			}
			}
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Order Moved to Next Process</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}else{
				
				if($isfinalstep!=0)
				{
					
						/** Check if Merge is available **/
				  $merger=$this->db->select('id,mergewith,flowid')->from('fmsmerge')->where('productionflow',$productionflowid)->get();
					if($merger->num_rows()>0)
					{
						foreach($merger->result() as $mergerdata);
						$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$mergerdata->flowid)->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
						$orderstmergedata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$mergerdata->flowid,'userstatus'=>'0','remarks'=>'','addedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('order_stage',$orderstmergedata); 
				        $odm=$this->db->insert_id();
						/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			}else
			{
				foreach($checkodd->result() as $prevorderstage);
				$odm=$prevorderstage->id;
				
				/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
				
				
			}
					
						if($odm<>0)
						{
							$mergehistdata=array('orderstageid'=>$odm,'oldproductionflow'=>$productionflowid,'jobcardid'=>$jobcardid,'newproductionflow'=>$mergerdata->mergewith,'oldfmsflow'=>$flowstage,'newfmsflow'=>$mergerdata->flowid,'addedOn'=>date('Y-m-d H:i:s'));
							$this->db->insert('fmsmergehistory',$mergehistdata);
							
							$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Merged.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
							
							}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to merge flow.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
					}else { 
					$newdata=array('complete'=>'1');
					$this->db->where('order_id',$orderid);
					$this->db->where('id',$jobcardid);
					$this->db->update('order_instruments',$newdata);
					
					if($selforder=='1')
					{
					$this->fmsmodel->addstock($jobcardid);
					}

$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);					
					}
					/** Merge **/
					$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}else
				{
				
				/** End **/
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}
				
				
			}
		
		}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
				
		

		
	}
	
	
	function materialplatingoutold28jan2020()
	{
		
	$orderstage=$this->uri->segment(3);
	$flowstage=$this->uri->segment(4);
	$orderid=$this->uri->segment(5);
	$productionflowid=$this->uri->segment(6);
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$newstage=[];
	$jobcardid=$this->input->post('jobcardid'.$orderstage);
	$challanno=$this->input->post('challanno'.$orderstage);
	$weight=$this->input->post('weight'.$orderstage);
	$pcs=$this->input->post('pcs'.$orderstage);

		
		/** Move to next stage **/
			$currentstage=$flowstage;
			$getset=$this->db->select('setorder')->from('fms_flow')->where('flow_id',$currentstage)->get();
			if($getset->num_rows()>0)
			{
				foreach($getset->result() as $ggetset);
				$currentsetorder=$ggetset->setorder;	
				/** Get Next Order **/
				$getneworder=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id',$productionflowid)->where('setorder>',$currentsetorder)->limit(1)->order_by('setorder','ASC')->get();
				if($getneworder->num_rows()>0)
			{
				foreach($getneworder->result() as $getneworder1);
				/** Check Move **/	
				$restymove=$this->db->select('moveto')->from('fms_flow')->where('flow_id',$flowstage)->get();
				foreach($restymove->result() as $restymove1)
				{
					//echo $restymove1->moveto;exit;
				if(($restymove1->moveto<>0) && ($restymove1->moveto<>''))
				{
				$moveflow=$restymove1->moveto;
				$movearr=explode(',',$moveflow);
				foreach($movearr as $movea)
				{
				$newstage[]=$movea;
				}
				
				} else
				{
			
					$newstage[]=$getneworder1->flow_id;
				} 
				}
				
			}else{
				
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			}
				/** End **/
	}else{
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			}
			
			//echo "<pre>"; print_r($newstage);exit;
			if(count($newstage)>0)
			{
				
			/** Detail Entery **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'challanno'=>$challanno,'weight'=>$weight,'pcs'=>$pcs,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->insert('material_plating_out',$detailentry);
			$effrow1=$this->db->affected_rows();	
			
			/** End**/
			if($effrow1>0)
			{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);
			$effrow=$this->db->affected_rows();	
		if($effrow>0)
		{
			for($u=0;$u<count($newstage);$u++)
			{
			
	$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$newstage[$u])->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{		
			
				if($newstage[$u]<>0)
	{
	$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage[$u],'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
			}
			}
			}
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Order Moved to Next Process</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}else{
			
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
		
		}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
				
			}else{
				
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			}

		


	}


		function machining_material_in_platingprocess()
	{
		$scheduler_data= array();
	$flowstage=$this->uri->segment(3);
	$productionflowid=$this->uri->segment(4);
	/*GET CURRENT PROCESS SORT ORDER*/
			$QRY = $this->db->select('total_days,tat,set_time, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();
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
			$currentprocessorder = $flowstage;
		$setorder=$fmsinformation->setorder;
	/*GET CURRENT PROCESS SORT ORDER*/
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$fmsprocess=$productionflowid;
	/** END **/
	if($fmsprocess<>0)
	{
	/** Get Process Name **/
	$process=$this->db->select('d.file_number,f.ordertype,f.fileno,a.jumpfabricationappl,a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow,d.fabrication,f.planstartsfrom')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('a.who_wedo',$user_id)->order_by('b.userstatus','ASC')->order_by('b.addedOn','DESC')->get();
	/** End **/
	if($process->num_rows()>0)
	{
	// echo "<pre>"; print_r($process->result());exit;
	$i=1;
	foreach($process->result() as $proc)
	{

		$ordertype = $proc->ordertype;
			if($ordertype=='1'){
				
				$fileno=$proc->file_number;
			}else{
			
				$fileno=$proc->fileno;
			}

	if($proc->userstatus=='0')
	{
	$prestui="<span class='btn btn-danger btn-xs'>Update Pending</span>";
	//$action='<a href="javascript:;" onclick="markstagedone('."'".$proc->orderstageid."'".','."'".$proc->flowstage."'".','."'".$proc->originalorderid."'".','."'".$proc->jobcardid."'".')"><span class="btn btn-xs btn-warning">Mark as done</span></a>';
	$html='<button class="btn btn-xs btn-warning waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$proc->orderstageid.'">Mark as Done</button>';

	$html.= ' <div id="con-close-modal'.$proc->orderstageid.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<form action="'.page_url.'Orderstage/materialplatingin/'.$proc->orderstageid.'/'.$proc->flowstage.'/'.$proc->originalorderid.'/'.$fmsprocess.'" method="post" id="loginForm" enctype="multipart/form-data">
		
	  <div id="pageloader">
	   <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
	</div>
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
												<h4 class="modal-title">MATERIAL IN (PLATING)</h4>
											</div>
											<div class="modal-body">
										
												<div class="row">
												
												
													<div class="col-md-6">
														<div class="form-group">
															<label for="field-2" class="control-label">JOBCARD NUMBER<span style="color:red">*</span></label>
															
															<input type="text" style="width:250px" class="form-control" id="jobcard_no'.$proc->orderstageid.'" style="text-transform: uppercase;" name="jobcard_no'.$proc->orderstageid.'" placeholder="" value="'.$proc->job_card_no.'" readonly>
															<input type="hidden" name="jobcardid'.$proc->orderstageid.'" value="'.$proc->jobcardid.'">
														</div>
													</div>';
													$weightin=array();
													$pcsin=array();
													$resttt=$this->db->select('challanno,weight,pcs')->from('material_plating_out')->where('jobcardid',$proc->jobcardid)->get();
													if($resttt->num_rows()>0)
													{
														foreach($resttt->result() as $restuy);
														$challanno=$restuy->challanno;
														$weight=$restuy->weight;
														$pcs=$restuy->pcs;
													}else{
														$challanno='';
														$weight=0;
														$pcs=0;
													}
													
													$resttt=$this->db->select('weight,pcs')->from('material_plating_in')->where('jobcardid',$proc->jobcardid)->get();
													if($resttt->num_rows()>0)
													{
														foreach($resttt->result() as $restuy)
														{
														
														$weightin[]=$restuy->weight;
														$pcsin[]=$restuy->pcs;
														}
													}else{
														$weightin[]=0;
														$pcsin[]=0;
													}
													if(count($weightin)>0)
													{
														$weightin1=array_sum($weightin);
													}else{
														$weightin1=0;
														
													}
													
													
													if(count($pcsin)>0)
													{
														$pcsin1=array_sum($pcsin);
													}else
													{
														$pcsin1=0;
													}
													
													$finalweight=$weight-$weightin1;
													$weightslab=$finalweight*0.1;
													$finweight=$finalweight+$weightslab;
													$pcsfinal=$pcs-$pcsin1;
													$html.='<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">CHALLAN NO.<span style="color:red">*</span></label><br>
	<input type="hidden" name="outweight'.$proc->orderstageid.'" value="'.$weight.'">
	<input type="hidden" name="outpcs'.$proc->orderstageid.'" value="'.$pcs.'">

													<input type="text" class="form-control" id="challanno'.$proc->orderstageid.'" name="challanno'.$proc->orderstageid.'" required style="width:250px" value="'.$challanno.'" readonly>
													</div>
													</div>
													
													
													<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">WEIGHT<span style="color:red">*</span>&nbsp;&nbsp;&nbsp;<span style="font-size:10px;color:red;">OUT WEIGHT: '.floatval($finalweight).'KG </span></label><br>

													<input type="number" step="0.001" class="form-control" id="weight'.$proc->orderstageid.'" name="weight'.$proc->orderstageid.'" max="'.$finweight.'" required style="width:250px" placeholder="IN Kg">
													</div>
													</div>
													
													
													<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">PCS<span style="color:red">*</span>&nbsp;&nbsp;&nbsp;<span style="font-size:10px;color:red;">OUT PCS: '.$pcsfinal.'</span></label><br>

													<input type="number" class="form-control" id="pcs'.$proc->orderstageid.'" name="pcs'.$proc->orderstageid.'" required style="width:250px" min="0"  max="'.$pcsfinal.'">
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


	$completedate="";
	$addedfinaltime="";

	}else{
	$compdate=date('d-M-Y H:i:s',strtotime($proc->stagecompletedate));
	$prestui="<span class='btn btn-success btn-xs'>Remarks Updated</span>";
	$action="Task completed on ".$compdate;
	$html="This Task has been done by You.";

	date_default_timezone_set("Asia/Kolkata");

	$completedate = date('d-M-Y', strtotime($proc->stagecompletedate));
	$completetime = date('H:i:s', strtotime($proc->stagecompletedate));
	$addedfinaltime = "<br>". date('g:i A', strtotime($completetime));

	}
$previousorder = $tatstage;
					

	$QRY = $this->db->select('jobcardid, challanno, weight, pcs')->from('material_plating_out')->where('jobcardid',$proc->jobcardid)->get();
	foreach($QRY->result() as $outinfo);
	$challanno = $outinfo->challanno;
	$weight = $outinfo->weight;
	$pcs = $outinfo->pcs;

	$QRY1 = $this->db->select('jobcardid, challanno, weight, pcs')->from('material_plating_in')->where('jobcardid',$proc->jobcardid)->get();
	$m=1;
	$htmldata = "<table border='1' style='width:200px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center'>WEIGHT IN KG</th><th style='padding:2px 2px 2px 2px; text-align:center'>PCS</th></tr><tbody>";
	$result = $QRY1->result();
	if($result>0){
		foreach($result as $indata){
		$htmldata.="<tr><td style='padding:2px 2px 2px 2px; text-align:center'>".$indata->weight."</td><td style='padding:2px 2px 2px 2px; text-align:center'>".$indata->pcs."</td></tr>";
		$m++;
		}
	}
	$htmldata.="</tbody></table>";

		/** Get User Department **/
				$udep=$this->db->select('b.department')->from('system_users a')->join('departments b','a.department_id=b.department_id')->where('a.user_id',$_SESSION['logged_in']['user_id'])->get(); 
				if($udep->num_rows()>0)
				{
					foreach($udep->result() as $depp);
					$depart=$depp->department;
					
				}else
				{
					$depart='';
				}

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
					
					/** End **/
					
					/** Check if instrument requires fabrication **/
					/**$ryt=$this->db->select('b.fabrication')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->where('a.id',$proc->jobcardid)->get();
					if($ryt->num_rows()>0)
					{
						foreach($ryt->result() as $rtyeyte);
						$fabreq=$rtyeyte->fabrication;
						
					}else{ $fabreq=0 ;}
					
					if($fabreq==0)
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
				

				
				/** End **/

if($markapp==1)
{
	
		$timestamp=$this->getprevioustimestamp($tatstage,$proc->plannedOn,$proc->jobcardid,$flowstage,$proc->orderstageid);
		$tattime = DateTime::createFromFormat('d-M-Y g:i A', $timestamp);
		$finaltattime=$tattime->format('g:i A');
		$tatdate=$this->getupcomingtatdatenew($tatstage,$proc->plannedOn,$proc->jobcardid,$timestamp,$totaldaysslave,$proc->orderstageid,$day);
		if($proc->userstatus=='0')
		{
		$completedon="";
		}else
		{
		$completedon=date('d-M-Y g:i A',strtotime($proc->stagecompletedate));
		}	
	
	$odtype=$this->getordertype($proc->originalorderid);
	$scheduler_data[] = array('sr_no'=>$i,
	'orderdate'=>$timestamp,
	'odtype'=>$odtype,
	'mname'=>strtoupper($proc->instruments_name),
	'jobcard'=>strtoupper($proc->job_card_no),
	'fileno'=>$fileno,
	'factory'=>strtoupper($depart),
	'tat'=>$tatdate,
	'actual'=>$completedon,
	'totdays'=>'',
	'challan'=>$challanno,
	'weight'=>$weight,
	'pcs'=>$pcs,
	'htmldata'=>$htmldata,
	'status'=>$prestui,
	'action'=>$html);
	 
	 
	$i++;
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
	} 

	function machining_material_in_platingprocessoldtempp()
	{
		$scheduler_data= array();
	$flowstage=$this->uri->segment(3);
	$productionflowid=$this->uri->segment(4);
	/*GET CURRENT PROCESS SORT ORDER*/
			$QRY = $this->db->select('total_days, tat, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();
			$res = $QRY->result();
			foreach($res as $fmsinformation);
			$totaldaysslave = $fmsinformation->total_days;
			$tatstage = $fmsinformation->tat;
			$currentprocessorder = $flowstage;
		$setorder=$fmsinformation->setorder;
	/*GET CURRENT PROCESS SORT ORDER*/
	$user_id =$this->session->userdata['logged_in']['user_id'];
	/*$isfms=$this->db->select('fms_process')->from('system_users')->where('user_id',$user_id)->get();
	if($isfms->num_rows()>0)
	{
	foreach($isfms->result() as $isfmsa);
	$fmsprocess=$isfmsa->fms_process;
	}else{ $fmsprocess=0; }*/
	$fmsprocess=$productionflowid;
	/** END **/
	if($fmsprocess<>0)
	{
	/** Get Process Name **/
	$process=$this->db->select('a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('a.who_wedo',$user_id)->order_by('b.userstatus','ASC')->order_by('b.addedOn','DESC')->get();
	/** End **/
	if($process->num_rows()>0)
	{
	// echo "<pre>"; print_r($process->result());exit;
	$i=1;
	foreach($process->result() as $proc)
	{
	if($proc->userstatus=='0')
	{
	$prestui="<span class='btn btn-danger btn-xs'>Update Pending</span>";
	//$action='<a href="javascript:;" onclick="markstagedone('."'".$proc->orderstageid."'".','."'".$proc->flowstage."'".','."'".$proc->originalorderid."'".','."'".$proc->jobcardid."'".')"><span class="btn btn-xs btn-warning">Mark as done</span></a>';
	$html='<button class="btn btn-xs btn-warning waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$proc->orderstageid.'">Mark as Done</button>';

	$html.= ' <div id="con-close-modal'.$proc->orderstageid.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<form action="'.page_url.'Orderstage/materialplatingin/'.$proc->orderstageid.'/'.$proc->flowstage.'/'.$proc->originalorderid.'/'.$fmsprocess.'" method="post" id="loginForm" enctype="multipart/form-data">
		
	  <div id="pageloader">
	   <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
	</div>
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
												<h4 class="modal-title">MATERIAL IN (PLATING)</h4>
											</div>
											<div class="modal-body">
										
												<div class="row">
												
												
													<div class="col-md-6">
														<div class="form-group">
															<label for="field-2" class="control-label">JOBCARD NUMBER<span style="color:red">*</span></label>
															
															<input type="text" style="width:250px" class="form-control" id="jobcard_no'.$proc->orderstageid.'" style="text-transform: uppercase;" name="jobcard_no'.$proc->orderstageid.'" placeholder="" value="'.$proc->job_card_no.'" readonly>
															<input type="hidden" name="jobcardid'.$proc->orderstageid.'" value="'.$proc->jobcardid.'">
														</div>
													</div>';
													$weightin=array();
													$pcsin=array();
													$resttt=$this->db->select('challanno,weight,pcs')->from('material_plating_out')->where('jobcardid',$proc->jobcardid)->get();
													if($resttt->num_rows()>0)
													{
														foreach($resttt->result() as $restuy);
														$challanno=$restuy->challanno;
														$weight=$restuy->weight;
														$pcs=$restuy->pcs;
													}else{
														$challanno='';
														$weight=0;
														$pcs=0;
													}
													
													$resttt=$this->db->select('weight,pcs')->from('material_plating_in')->where('jobcardid',$proc->jobcardid)->get();
													if($resttt->num_rows()>0)
													{
														foreach($resttt->result() as $restuy)
														{
														
														$weightin[]=$restuy->weight;
														$pcsin[]=$restuy->pcs;
														}
													}else{
														$weightin[]=0;
														$pcsin[]=0;
													}
													if(count($weightin)>0)
													{
														$weightin1=array_sum($weightin);
													}else{
														$weightin1=0;
														
													}
													
													
													if(count($pcsin)>0)
													{
														$pcsin1=array_sum($pcsin);
													}else
													{
														$pcsin1=0;
													}
													
													$finalweight=$weight-$weightin1;
													$weightslab=$finalweight*0.1;
													$finweight=$finalweight+$weightslab;
													$pcsfinal=$pcs-$pcsin1;
													$html.='<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">CHALLAN NO.<span style="color:red">*</span></label><br>
	<input type="hidden" name="outweight'.$proc->orderstageid.'" value="'.$weight.'">
	<input type="hidden" name="outpcs'.$proc->orderstageid.'" value="'.$pcs.'">

													<input type="text" class="form-control" id="challanno'.$proc->orderstageid.'" name="challanno'.$proc->orderstageid.'" required style="width:250px" value="'.$challanno.'" readonly>
													</div>
													</div>
													
													
													<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">WEIGHT<span style="color:red">*</span>&nbsp;&nbsp;&nbsp;<span style="font-size:10px;color:red;">OUT WEIGHT: '.floatval($finalweight).'KG </span></label><br>

													<input type="number" step="0.001" class="form-control" id="weight'.$proc->orderstageid.'" name="weight'.$proc->orderstageid.'" max="'.$finweight.'" required style="width:250px" placeholder="IN Kg">
													</div>
													</div>
													
													
													<div class="col-md-6">
													<div class="form-group">
													<label for="field-2" class="control-label">PCS<span style="color:red">*</span>&nbsp;&nbsp;&nbsp;<span style="font-size:10px;color:red;">OUT PCS: '.$pcsfinal.'</span></label><br>

													<input type="number" class="form-control" id="pcs'.$proc->orderstageid.'" name="pcs'.$proc->orderstageid.'" required style="width:250px" min="0"  max="'.$pcsfinal.'">
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


	$completedate="";
	$addedfinaltime="";

	}else{
	$compdate=date('d-M-Y H:i:s',strtotime($proc->stagecompletedate));
	$prestui="<span class='btn btn-success btn-xs'>Remarks Updated</span>";
	$action="Task completed on ".$compdate;
	$html="This Task has been done by You.";

	date_default_timezone_set("Asia/Kolkata");

	$completedate = date('d-M-Y', strtotime($proc->stagecompletedate));
	$completetime = date('H:i:s', strtotime($proc->stagecompletedate));
	$addedfinaltime = "<br>". date('g:i A', strtotime($completetime));

	}
	date_default_timezone_set("Asia/Kolkata");
	$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
	$addedtime = "<br>". date('g:i A', strtotime($time));

	$time1=date('H:i:s', strtotime($proc->plannedOn));
	$addedtime1 = "<br>". date('g:i A', strtotime($time1));
	$planneddate1=date('Y-m-d',strtotime($proc->plannedOn));

	/*GET DATE OF PRIVIOUS STEPS*/
						$previousorder = $tatstage;
					
							if($previousorder<>0){
						$abc = $this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$proc->jobcardid)->order_by('id','DESC')->limit(1)->get();
							if($abc->num_rows()>0){
						foreach($abc->result() as $pastinfo);
						$complitiontime = $pastinfo->addedOn;
							
								$lasttaskcompletedtime = "<br>". date('g:i A', strtotime($complitiontime));

								
							}else{
							$complitiontime = $proc->added_on;
									$lasttaskcompletedtime = "<br>". date('g:i A', strtotime($complitiontime));

							}
						}else{
						$complitiontime = $proc->plannedOn;	
								$lasttaskcompletedtime = "<br>". date('g:i A', strtotime($complitiontime));

						}
						
						/*PREVIOUS COMPLITION DATE*/
						
						$checkpreviousdate = date('Y-m-d', strtotime($complitiontime));
						$previouscomplitiondate = date('d-M-Y', strtotime($complitiontime));
						$previouscomplitiontime = date('H:i:s', strtotime($proc->added_on));
						$previouscomplitionfinaltime = "<br>". date('g:i A', strtotime($previouscomplitiontime));
				
						/*GET DATE OF PRIVIOUS STEPS*/
						$date11 = new DateTime($previouscomplitiondate); 
						$date22 = new DateTime($completedate); 
						$interval1 = $date11->diff($date22); 
						$ttldays = $interval1->d; 
						if($ttldays=='0'){
						$totalday = "1";
						}else{
						$totalday = $ttldays;
				}
				
				/*TAT Date*/
				$lastcompdate = date('Y-m-d', strtotime($checkpreviousdate));
				$TATDATE=date('d-M-Y', strtotime($lastcompdate."+".$totaldaysslave." days"));
				
				/*TAT Date*/
		$QRY = $this->db->select('total_days, tat, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();

	if($this->uri->segment(3)=='1'){
		date_default_timezone_set("Asia/Kolkata");
	$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
	$addedtime = "<br>". date('g:i A', strtotime($time));
		}else{
		$previousorder = $setorder-1; 
		$QRY = $this->db->select('total_days, tat,setorder, flow_id ')->from('fms_flow')->where('setorder',$previousorder)->get();
		if($QRY->num_rows()>0){
		foreach($QRY->result() as $paststepdate);
			$flowid = $paststepdate->flow_id;
		$QRY1 = $this->db->select('jobcardid, flowstage,addedOn')->from('order_stage')->where('jobcardid',$proc->jobcardid)->where('flowstage',$flowid)->get();	
		if($QRY1->num_rows()>0){
		foreach($QRY1->result() as $timestamp){
		$addeddate1 = date('Y-m-d', strtotime($timestamp->addedOn));
	$time = date('H:i:s', strtotime($timestamp->addedOn));
	$addedtime = "<br>". date('g:i A', strtotime($time));
		}
		}	
		}else{
		$addeddate = date('d-M-Y', strtotime($proc->added_on));
	$addeddate1 = date('Y-m-d', strtotime($proc->added_on));
	$time = date('H:i:s', strtotime($proc->added_on));
		}
		}

	$QRY = $this->db->select('jobcardid, challanno, weight, pcs')->from('material_plating_out')->where('jobcardid',$proc->jobcardid)->get();
	foreach($QRY->result() as $outinfo);
	$challanno = $outinfo->challanno;
	$weight = $outinfo->weight;
	$pcs = $outinfo->pcs;

	$QRY1 = $this->db->select('jobcardid, challanno, weight, pcs')->from('material_plating_in')->where('jobcardid',$proc->jobcardid)->get();
	$m=1;
	$htmldata = "<table border='1' style='width:200px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center'>WEIGHT IN KG</th><th style='padding:2px 2px 2px 2px; text-align:center'>PCS</th></tr><tbody>";
	$result = $QRY1->result();
	if($result>0){
		foreach($result as $indata){
		$htmldata.="<tr><td style='padding:2px 2px 2px 2px; text-align:center'>".$indata->weight."</td><td style='padding:2px 2px 2px 2px; text-align:center'>".$indata->pcs."</td></tr>";
		$m++;
		}
	}
	$htmldata.="</tbody></table>";

		/** Get User Department **/
				$udep=$this->db->select('b.department')->from('system_users a')->join('departments b','a.department_id=b.department_id')->where('a.user_id',$_SESSION['logged_in']['user_id'])->get(); 
				if($udep->num_rows()>0)
				{
					foreach($udep->result() as $depp);
					$depart=$depp->department;
					
				}else
				{
					$depart='';
				}

	/** Check for dependency **/
				
				$checkdepend=$this->db->select('dependency')->from('fms_flow')->where('flow_id',$flowstage)->where('dependency','1')->get();
				$depend=$checkdepend->num_rows();
				//echo $depend;exit;
				if($depend==1)
				{
					
					$restt=$this->db->select('dependentflowid')->from('flowdependency')->where('flowid',$flowstage)->get();
					if($restt->num_rows()>0)
					{
						$dependcount=$restt->num_rows();
						$donarr=array();
						$donarr[]=0;
						//echo "<pre>";print_r($restt->result());exit;
						foreach($restt->result() as $resttyu)
						{
							$isdone=$this->db->select('id,userstatus')->from('order_stage')->where('flowstage',$resttyu->dependentflowid)->where('jobcardid',$proc->jobcardid)->where('orderid',$proc->originalorderid)->order_by('id','DESC')->limit(1)->get();
							if($isdone->num_rows()>0)
							{
								foreach($isdone->result() as $isdone11);
								$donarr[]=$isdone11->userstatus;
								
							}else
							{
								$markapp=1;
							}
							
						}
						//echo $dependcount.'<br>'."<pre>"; print_r($donarr);exit;
						if($dependcount==array_sum($donarr))
						{
							$markapp=1;
						}else{
							$markapp=0;
						}
						
					}else{
						$markapp=1;
					}
					
				}else{
					
					$markapp=1;
				}
				/** End **/

	/** New Timestamp query **/
				$previousorder=$tatstage;
				if($previousorder==0)
				{
					$previouscomtime=$proc->plannedOn;
					$previouscomdate=date('d-M-Y',strtotime($previouscomtime))."<br>";
					$previouscomtimeonly=date('g:i A',strtotime($previouscomtime));
					
				}else
				{
					//echo $previousorder;exit;
					$oldtime1=$this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$proc->jobcardid)->order_by('id','DESC')->limit(1)->get();
							if($oldtime1->num_rows()>0){
								foreach($oldtime1->result() as $pastinfo);
								$previouscomtime=$pastinfo->addedOn;
								$previouscomdate=date('d-M-Y',strtotime($pastinfo->addedOn))."<br>";
								$previouscomtimeonly=date('g:i A',strtotime($pastinfo->addedOn));
							}else
							{
								$previouscomtime=$proc->plannedOn;
					$previouscomdate=date('d-M-Y',strtotime($previouscomtime))."<br>";
					$previouscomtimeonly=date('g:i A',strtotime($previouscomtime));
							}
				}

		
				
				/** End **/
			if($markapp==1)
				{
					
	/** Holiday Check**/
				
					$TATDATEFORHOLIDAYCHECK=date('Y-m-d', strtotime($TATDATE));
					$timestampforcheck=date('Y-m-d',strtotime($previouscomtime));
					$date_from=strtotime($timestampforcheck);
					$date_to=strtotime($TATDATEFORHOLIDAYCHECK);
					$betweendates=array();
					for ($g=$date_from; $g<=$date_to; $g+=86400) {  
						$betweendates[]= date("Y-m-d", $g);  
					} 
				
				//echo "<pre>"; print_r($betweendates);
					if(count($betweendates)>0)
				{
					
					$alldates = "'" . implode ( "', '", $betweendates ) . "'";
					
					$ifholiday=$this->db->select('holiday_id')->from('prestogroup_holidays')->where_in('holiday_date',$alldates,false)->get();
					if($ifholiday->num_rows()>0)
					{
			
						//echo "hi".$proc->job_card_no;
						$TATDATEFORHOLIDAYCHECK=date('Y-m-d', strtotime($TATDATEFORHOLIDAYCHECK."+".$ifholiday->num_rows()." days"));
						//echo $TATDATEFORHOLIDAYCHECK;
						
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
				//$TATDATE= date('d-M-Y', strtotime($nextdate."+1 days"));
				$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();
				if($query->num_rows()>0){
					
					foreach($query->result() as $afterholiday);
					$aftrholidaydate = $afterholiday->holiday_date;
					$TATDATE= date('d-M-Y', strtotime($aftrholidaydate."+1 days"));
					
				}else {
				$TATDATE= date('d-M-Y', strtotime($nextdate));
				}
			}else{
			$TATDATE=date('d-M-Y', strtotime($TATDATEFORHOLIDAYCHECK));
			}
					
				/** End **/
	$scheduler_data[] = array('sr_no'=>$i,
	'orderdate'=>$previouscomdate.$previouscomtimeonly,
	'mname'=>strtoupper($proc->instruments_name),
	'jobcard'=>strtoupper($proc->job_card_no),
	'factory'=>strtoupper($depart),
	'planned'=>date('d-M-Y',strtotime($proc->plannedOn)).$addedtime1,
	'tat'=>$TATDATE."<br>".$previouscomtimeonly,
	'actual'=>$completedate.$addedfinaltime,
	'totdays'=>$totalday,
	'challan'=>$challanno,
	'weight'=>$weight,
	'pcs'=>$pcs,
	'htmldata'=>$htmldata,
	'status'=>$prestui,
	'action'=>$html);
	 
	 
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

	}
	} 


		
	function materialplatinginoldbeforefmstatdate()
	{
	$orderstage=$this->uri->segment(3);
	$flowstage=$this->uri->segment(4);
	$orderid=$this->uri->segment(5);
	$selforder=$this->fmsmodel->getselforder($orderid);
	$productionflowid=$this->uri->segment(6);
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$newstage=[];
	$isfinalstep=0;
	$jobcardid=$this->input->post('jobcardid'.$orderstage);
	$challanno=$this->input->post('challanno'.$orderstage);
	$weight=$this->input->post('weight'.$orderstage);
	$pcs=$this->input->post('pcs'.$orderstage);
	$outweight=$this->input->post('outweight'.$orderstage);
	$outpcs=$this->input->post('outpcs'.$orderstage);

		
		/** Move to next stage **/
			$currentstage=$flowstage;
			$getset=$this->db->select('setorder,finalstep')->from('fms_flow')->where('flow_id',$currentstage)->where('production_flow_id',$productionflowid)->get();
			
				foreach($getset->result() as $ggetset);
				$currentsetorder=$ggetset->setorder;
				$isfinalstep=$ggetset->finalstep;			
				/** Get Next Order **/
				$getneworder=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id',$productionflowid)->where('setorder>',$currentsetorder)->limit(1)->order_by('setorder','ASC')->get();
				if($getneworder->num_rows()>0)
			{
				foreach($getneworder->result() as $getneworder1);
				/** Check Move **/	
				$restymove=$this->db->select('moveto')->from('fms_flow')->where('flow_id',$flowstage)->get();
				foreach($restymove->result() as $restymove1)
				{
					//echo $restymove1->moveto;exit;
				if(($restymove1->moveto<>0) && ($restymove1->moveto<>''))
				{
				$moveflow=$restymove1->moveto;
				$movearr=explode(',',$moveflow);
				foreach($movearr as $movea)
				{
				$newstage[]=$movea;
				}
				
				} else
				{
			
					$newstage[]=$getneworder1->flow_id;
				} 
				}
			}
				/** End **/
				$previnpcs=array();
				$restttprevpaintin=$this->db->select('pcs')->from('material_plating_in')->where('jobcardid',$jobcardid)->get();
				if($restttprevpaintin->num_rows()>0)
				{
				foreach($restttprevpaintin->result() as $restuy)
				{

				$previnpcs[]=$restuy->pcs;
				}
				$totalprvpcs=array_sum($previnpcs);
				}else 
				{  
				$totalprvpcs=0;
				}
				/** End **/
				
			/** Detail Entery **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'challanno'=>$challanno,'weight'=>$weight,'pcs'=>$pcs,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->insert('material_plating_in',$detailentry);
			$effrow1=$this->db->affected_rows();	
			
			/** End**/
			if($effrow1>0)
			{
			
			$overallpcs=$totalprvpcs+$pcs;
			//echo $overallpcs;exit;
		if($overallpcs==$outpcs)
		{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);
			$effrow=$this->db->affected_rows();	
		if(count($newstage)>0)
		{
			for($u=0;$u<count($newstage);$u++)
			{
			
			$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$newstage[$u])->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
				if($newstage[$u]<>0)
	{
	$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage[$u],'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
			
			/** Add Tat **/
						$stageid=$this->db->insert_id();
						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->where('flowstage',$newstage[$u]);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			
			
			}
			}else
			{
				foreach($checkodd->result() as $checkodd1);
				/** Add Tat **/
						$stageid=$checkodd1->id;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->where('flowstage',$newstage[$u]);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
				
			}
			}
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Order Moved to Next Process.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}else{
				
				if($isfinalstep!=0)
				{
					
						/** Check if Merge is available **/
				  $merger=$this->db->select('id,mergewith,flowid')->from('fmsmerge')->where('productionflow',$productionflowid)->get();
					if($merger->num_rows()>0)
					{
						foreach($merger->result() as $mergerdata);
						$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$mergerdata->flowid)->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
						$orderstmergedata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$mergerdata->flowid,'userstatus'=>'0','remarks'=>'','addedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('order_stage',$orderstmergedata); 
				        $odm=$this->db->insert_id();
						
						/** Add Tat **/
						$stageid=$odm();
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			}else
			{
				foreach($checkodd->result() as $prevorderstage);
				$odm=$prevorderstage->id;
				
				/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
				
				
			}
					
						if($odm<>0)
						{
							$mergehistdata=array('orderstageid'=>$odm,'oldproductionflow'=>$productionflowid,'jobcardid'=>$jobcardid,'newproductionflow'=>$mergerdata->mergewith,'oldfmsflow'=>$flowstage,'newfmsflow'=>$mergerdata->flowid,'addedOn'=>date('Y-m-d H:i:s'));
							$this->db->insert('fmsmergehistory',$mergehistdata);
							
							$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Merged.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
							
							}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to merge flow.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
					}else { 
					$newdata=array('complete'=>'1');
					$this->db->where('order_id',$orderid);
					$this->db->where('id',$jobcardid);
					$this->db->update('order_instruments',$newdata);
					
					if($selforder=='1')
					{
					$this->fmsmodel->addstock($jobcardid);
					}

$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);					
					}
					/** Merge **/
					$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}else
				{
				
				/** End **/
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}
				
				
			}
		
		}else{
			
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Material IN details Updated but order is still pending.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
		
		}else{ 
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to update order.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);  
				}
				
			

		}

	function materialplatingin()
	{
	$orderstage=$this->uri->segment(3);
	$flowstage=$this->uri->segment(4);
	$orderid=$this->uri->segment(5);
	$selforder=$this->fmsmodel->getselforder($orderid);
	$productionflowid=$this->uri->segment(6);
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$newstage=[];
	$isfinalstep=0;
	$jobcardid=$this->input->post('jobcardid'.$orderstage);
	$challanno=$this->input->post('challanno'.$orderstage);
	$weight=$this->input->post('weight'.$orderstage);
	$pcs=$this->input->post('pcs'.$orderstage);
	$outweight=$this->input->post('outweight'.$orderstage);
	$outpcs=$this->input->post('outpcs'.$orderstage);

		
		/** Move to next stage **/
			$currentstage=$flowstage;
			$getset=$this->db->select('setorder,finalstep,total_days,set_time')->from('fms_flow')->where('flow_id',$currentstage)->where('production_flow_id',$productionflowid)->get();
			
				foreach($getset->result() as $ggetset);
				$currentsetorder=$ggetset->setorder;
				$isfinalstep=$ggetset->finalstep;
				$tatday=$ggetset->total_days;
				$tathours=$ggetset->set_time;				
				/** Get Next Order **/
				$getneworder=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id',$productionflowid)->where('setorder>',$currentsetorder)->limit(1)->order_by('setorder','ASC')->get();
				if($getneworder->num_rows()>0)
			{
				foreach($getneworder->result() as $getneworder1);
				/** Check Move **/	
				$restymove=$this->db->select('moveto')->from('fms_flow')->where('flow_id',$flowstage)->get();
				foreach($restymove->result() as $restymove1)
				{
					//echo $restymove1->moveto;exit;
				if(($restymove1->moveto<>0) && ($restymove1->moveto<>''))
				{
				$moveflow=$restymove1->moveto;
				$movearr=explode(',',$moveflow);
				foreach($movearr as $movea)
				{
				$newstage[]=$movea;
				}
				
				} else
				{
			
					$newstage[]=$getneworder1->flow_id;
				} 
				}
			}
				/** End **/
				$previnpcs=array();
				$restttprevpaintin=$this->db->select('pcs')->from('material_plating_in')->where('jobcardid',$jobcardid)->get();
				if($restttprevpaintin->num_rows()>0)
				{
				foreach($restttprevpaintin->result() as $restuy)
				{

				$previnpcs[]=$restuy->pcs;
				}
				$totalprvpcs=array_sum($previnpcs);
				}else 
				{  
				$totalprvpcs=0;
				}
				/** End **/
				
			/** Detail Entery **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'challanno'=>$challanno,'weight'=>$weight,'pcs'=>$pcs,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->insert('material_plating_in',$detailentry);
			$effrow1=$this->db->affected_rows();	
			
			/** End**/
			if($effrow1>0)
			{
			
			$overallpcs=$totalprvpcs+$pcs;
			//echo $overallpcs;exit;
		if($overallpcs==$outpcs)
		{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);
			$effrow=$this->db->affected_rows();	
		if(count($newstage)>0)
		{
			for($u=0;$u<count($newstage);$u++)
			{
			
			$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$newstage[$u])->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
				if($newstage[$u]<>0)
	{
	$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage[$u],'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
			
			/** Add Tat **/
						$stageid=$this->db->insert_id();
						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$newstage[$u]);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			
			
			}
			}else
			{
				foreach($checkodd->result() as $checkodd1);
				/** Add Tat **/
						$stageid=$checkodd1->id;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$newstage[$u]);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
				
			}
			}
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Order Moved to Next Process.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}else{
				
				if($isfinalstep!=0)
				{
					
						/** Check if Merge is available **/
				  $merger=$this->db->select('id,mergewith,flowid')->from('fmsmerge')->where('productionflow',$productionflowid)->get();
					if($merger->num_rows()>0)
					{
						foreach($merger->result() as $mergerdata);
						$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$mergerdata->flowid)->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
						$orderstmergedata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$mergerdata->flowid,'userstatus'=>'0','remarks'=>'','addedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('order_stage',$orderstmergedata); 
				        $odm=$this->db->insert_id();
						
						/** Add Tat **/
						$stageid=$odm();
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			}else
			{
				foreach($checkodd->result() as $prevorderstage);
				$odm=$prevorderstage->id;
				
				/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
				
				
			}
					
						if($odm<>0)
						{
							$mergehistdata=array('orderstageid'=>$odm,'oldproductionflow'=>$productionflowid,'jobcardid'=>$jobcardid,'newproductionflow'=>$mergerdata->mergewith,'oldfmsflow'=>$flowstage,'newfmsflow'=>$mergerdata->flowid,'addedOn'=>date('Y-m-d H:i:s'));
							$this->db->insert('fmsmergehistory',$mergehistdata);
							
							$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Merged.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
							
							}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to merge flow.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
					}else { 
					$newdata=array('complete'=>'1');
					$this->db->where('order_id',$orderid);
					$this->db->where('id',$jobcardid);
					$this->db->update('order_instruments',$newdata);


					/** IF THIS IS SALESFORCE ORDER **/
					$this->checkifthisissforderandlastonetogetcompleted($orderid);

					/** END **/
					
					if($selforder=='1')
					{
					$this->fmsmodel->addstock($jobcardid);
					}

$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);					
					}
					/** Merge **/
					$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}else
				{
				
				/** End **/
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}
				
				
			}
		
		}else{
			
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Material IN details Updated but order is still pending.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
		
		}else{ 
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to update order.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);  
				}
				
			

		}


	function materialplatinginold28jan20202()
	{
		
	$orderstage=$this->uri->segment(3);
	$flowstage=$this->uri->segment(4);
	$orderid=$this->uri->segment(5);
	$productionflowid=$this->uri->segment(6);
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$newstage=[];
	$jobcardid=$this->input->post('jobcardid'.$orderstage);
	$challanno=$this->input->post('challanno'.$orderstage);
	$weight=$this->input->post('weight'.$orderstage);
	$pcs=$this->input->post('pcs'.$orderstage);
	$outweight=$this->input->post('outweight'.$orderstage);
	$outpcs=$this->input->post('outpcs'.$orderstage);

		
		/** Move to next stage **/
			$currentstage=$flowstage;
			$getset=$this->db->select('setorder')->from('fms_flow')->where('flow_id',$currentstage)->get();
			if($getset->num_rows()>0)
			{
				foreach($getset->result() as $ggetset);
				$currentsetorder=$ggetset->setorder;	
				/** Get Next Order **/
				$getneworder=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id',$productionflowid)->where('setorder>',$currentsetorder)->limit(1)->order_by('setorder','ASC')->get();
				if($getneworder->num_rows()>0)
			{
				foreach($getneworder->result() as $getneworder1);
				/** Check Move **/	
				$restymove=$this->db->select('moveto')->from('fms_flow')->where('flow_id',$flowstage)->get();
				foreach($restymove->result() as $restymove1)
				{
					//echo $restymove1->moveto;exit;
				if(($restymove1->moveto<>0) && ($restymove1->moveto<>''))
				{
				$moveflow=$restymove1->moveto;
				$movearr=explode(',',$moveflow);
				foreach($movearr as $movea)
				{
				$newstage[]=$movea;
				}
				
				} else
				{
			
					$newstage[]=$getneworder1->flow_id;
				} 
				}
				

				//	echo "<pre>"; print_r($newstage);exit;
				
			}else{
				
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			}
				/** End **/
	}else{
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			}
			//echo "<pre>"; print_r($newstage);exit;
			if(count($newstage)>0)
			{
				
				/** get previous in Paint pcs **/
			
				$previnpcs=array();
				$restttprevpaintin=$this->db->select('pcs')->from('material_plating_in')->where('jobcardid',$jobcardid)->get();
				if($restttprevpaintin->num_rows()>0)
				{
				foreach($restttprevpaintin->result() as $restuy)
				{

				$previnpcs[]=$restuy->pcs;
				}
				$totalprvpcs=array_sum($previnpcs);
				}else 
				{  
				$totalprvpcs=0;
				}
				/** End **/
				
			/** Detail Entery **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'challanno'=>$challanno,'weight'=>$weight,'pcs'=>$pcs,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->insert('material_plating_in',$detailentry);
			$effrow1=$this->db->affected_rows();	
			
			/** End**/
			if($effrow1>0)
			{
				
			$overallpcs=$totalprvpcs+$pcs;
			//echo $overallpcs;exit;
		if($overallpcs==$outpcs)
		{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);
			$effrow=$this->db->affected_rows();	
		if($effrow>0)
		{
			for($u=0;$u<count($newstage);$u++)
			{
			
			$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$newstage[$u])->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
				if($newstage[$u]<>0)
	{
	$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage[$u],'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
			}
			}
			}
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Order Moved to Next Process.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}else{
			
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
		
		}else{
			
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Material IN details Updated but order is still pending.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
		
		}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
				
			}else{
				
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			}

		


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
	
				
					function getprevioustimestampoldbeforeskipp17march($tatstage,$plannedon,$jobcardid,$flowstage,$orderstageid)
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
	
			
				function getprevioustimestampolfbrfordirectentry18feb2020($tatstage,$plannedon,$jobcardid,$flowstage,$orderstageid)
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
	
	
	function getupcomingtatdatebeforehoursimplementation($tatstage,$plannedon,$jobcardid,$timestamp,$daysslab,$orderstageid)
	{
			
	$datetime = DateTime::createFromFormat('d-M-Y g:i A', $timestamp);
	//$TATDATE1=$datetime->format('Y-m-d');
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
						
function getupcomingtatdateolddd($tatstage,$plannedon,$jobcardid,$timestamp,$daysslab)
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
					$oldtime1=$this->db->select('addedOn1')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$jobcardid)->order_by('id','DESC')->limit(1)->get();
							if($oldtime1->num_rows()>0){

								foreach($oldtime1->result() as $pastinfo);
								$previouscomtime=$pastinfo->addedOn;
								$previouscomdate=date('d-M-Y',strtotime($pastinfo->addedOn))."<br>";
								$previouscomtimeonly=date('g:i A',strtotime($pastinfo->addedOn));
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

function getflowidname($flowid)
{
	$ffl=$this->db->select('fms_flow')->from('fms_flow')->where('flow_id',$flowid)->get();
	if($ffl->num_rows()>0)
	{
		foreach($ffl->result() as $ffl1);
		
		return $ffl1->fms_flow;
		
	}else{
		return "";
	}
}
	
		
							
	function instock()
		{
			$scheduler_data=array();
	
		
		$rest=$this->db->select('a.planid,b.jobcard_id,b.plannedOn,b.order_id')->from('instockfms a')->join('order_planning b','a.planid=b.id')->order_by('a.id','DESC')->get();
		
		if($rest->num_rows()>0)
		{	
		
	
				$i=1;
			foreach($rest->result() as $restyy)
			{
				$inst=$this->db->select('a.id,b.instruments_name,a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->where('a.id',$restyy->jobcard_id)->get();
				if($inst->num_rows()>0)
				{
					foreach($inst->result() as $insst);
					$mname=$insst->instruments_name;
					$jobcardid=$insst->job_card_no;
				}else
				{
					$mname="";
					$jobcardid="";
				}
				
				$odtype=$this->getordertype($restyy->order_id);
				$scheduler_data[] = array('sr_no'=>$i,
				'orderdate'=>date('d-M-Y H:i:s',strtotime($restyy->plannedOn)),
				'odtype'=>$odtype,
				'mname'=>$mname,
				'jobcard'=>$jobcardid);	
			$i++;
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
	
		


		function boughtout_process()
	{
	$scheduler_data=array();
	$flowstage=$this->uri->segment(3);
	$productionflowid=$this->uri->segment(4);
	
	/*GET CURRENT PROCESS SORT ORDER*/
	$QRY = $this->db->select('total_days, tat, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();
	$res = $QRY->result();
	foreach($res as $fmsinformation);
	$totaldaysslave = $fmsinformation->total_days;
	$tatstage = $fmsinformation->tat;
	$currentprocessorder = $flowstage;
	$setorder= $fmsinformation->setorder;
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$fmsprocess=$productionflowid;
	/** END **/
	
	$bout=$this->db->select('a.id as boughtoutid,b.plannedOn,b.order_id as orderstageid,a.stockstatus,a.prno,a.addedOn,b.jobcard_id,c.item_id,d.instruments_name,c.job_card_no,a.updatedOn')->from('boughtoutfms a')->join('order_planning b','a.planid=b.id')->join('order_instruments c','b.jobcard_id=c.id')->join('presto_instruments d','c.item_id=d.id')->order_by('a.id','DESC')->get();
	if($bout->num_rows()>0)
	{
	$i=1;
	foreach($bout->result() as $bout1)
	{
		if($bout1->stockstatus=='1')
		{
			$stock="Stock Available";
		}else if($bout1->stockstatus=='2')
		{
			$stock="Stock Not Available";
		}else{
			
			$stock="";
		}
		if($bout1->updatedOn=='0000-00-00 00:00:00')
		{
			$upd="";
		}else{
			
			$upd=date('d-M-Y g:i A',strtotime($bout1->updatedOn));
		}
		
		if($bout1->stockstatus=='0')
	{
		
		$prestui="<span class='btn btn-danger btn-xs'>Update Pending</span>";
	//$action='<a href="javascript:;" onclick="markstagedone('."'".$proc->orderstageid."'".','."'".$proc->flowstage."'".','."'".$proc->originalorderid."'".','."'".$proc->jobcardid."'".')"><span class="btn btn-xs btn-warning">Mark as done</span></a>';
	$html='<button class="btn btn-xs btn-warning waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$bout1->boughtoutid.'">Mark as Done</button>';

	$html.= ' <div id="con-close-modal'.$bout1->boughtoutid.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<form action="'.page_url.'Orderstage/boughtoutdetails/'.$bout1->boughtoutid.'" method="post" id="loginForm">
		<input type="hidden" name="instrumentid'.$bout1->boughtoutid.'" value="'.$bout1->item_id.'">
	<input type="hidden" name="jobcardid'.$bout1->boughtoutid.'" value="'.$bout1->jobcard_id.'">
	<input type="hidden" name="flowstages" value="'.$flowstage.'">
	<input type="hidden" name="productionflowids" value="'.$productionflowid.'">
		<input type="hidden" name="oid'.$bout1->boughtoutid.'" value="'.$bout1->orderstageid.'">

	<div id="pageloader">
	<img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
	</div>
	<div class="modal-dialog modal-lg">
	<div class="modal-content">
	<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	<h4 class="modal-title">BOUGHT OUT</h4>
	</div>
	<div class="modal-body">

	<div class="row">
	<div class="col-md-4">
	<div class="form-group">
	<label for="field-1" class="control-label">JOBCARD NUMBER</label><br/>
	<span id="error_purpose" style="color:red;"></span>
	<input type="text" class="form-control" id="jobcard_no" style="text-transform: uppercase;" name="jobcard_no" placeholder="" value="'.$bout1->job_card_no.'" readonly required>
	<input type="hidden" name="jobcardid'.$bout1->boughtoutid.'" value="'.$bout1->jobcard_id.'">
	</div>
	</div>
	
	<div class="col-md-4">
	<div class="form-group">
	<label for="field-1" class="control-label">INSTRUMENT NAME</label><br/>
	<span id="error_purpose" style="color:red;"></span>
	<input type="text" class="form-control" id="instrumentname" style="text-transform: uppercase;" name="instrumentname" placeholder="" value="'.$bout1->instruments_name.'" readonly required>
	<input type="hidden" name="instrument'.$bout1->boughtoutid.'" value="'.$bout1->jobcard_id.'">
	</div>
	</div>

	<div class="col-md-4">
	<div class="form-group">
	<label for="field-2" class="control-label">MATERIAL IS IN STOCK <span id="error_status" style="color:red;">*</span></label><br>

	<select class="form-control" id="stock'.$bout1->boughtoutid.'" name="stock'.$bout1->boughtoutid.'" required onchange="getstockdetail('.$bout1->boughtoutid.');">
	<option value="">SELECT</option>
	<option value="1">YES</option>
	<option value="2">NO</option>
	</select>
	</div>
	</div>
	</div>

	<div class="row" id="prreq'.$bout1->boughtoutid.'" style="margin-top:20px;display:none">
	<div class="col-md-6">
	<div class="form-group">
	<label for="field-1" class="control-label">PR NUMBER</label><span id="error_purpose" style="color:red;">*</span><br/>

	<input type="text" class="form-control" id="prno'.$bout1->boughtoutid.'" style="text-transform: uppercase;" name="prno'.$bout1->boughtoutid.'" placeholder="" value="">
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


	$actualdate="";
	$actualtime="";
	$completedate="";
	$addedtimefinal="";
		
		
	}else
	{
		$prestui='';
		$html='Task Completed On '. $upd;
	}
		
		
$timestamp=date('d-M-Y',strtotime($bout1->addedOn));
$finaltattime=date('g:i A',strtotime($bout1->addedOn));
$TATDATE=date('d-M-Y',strtotime($timestamp."+".$totaldaysslave." days"));
$TATDATEFORHOLIDAYCHECK=date('Y-m-d',strtotime($TATDATE));
		$timestampforcheck=date('Y-m-d',strtotime($bout1->addedOn));
		$date_from=strtotime($timestampforcheck);
		$date_to=strtotime($TATDATEFORHOLIDAYCHECK);
		$betweendates=array();
		for ($g=$date_from; $g<=$date_to; $g+= (86400)) {  
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
		

		}else {
		$TATDATE= date('d-M-Y', strtotime($nextdate));
		
		}
		}else{
		$TATDATE=date('d-M-Y', strtotime($TATDATEFORHOLIDAYCHECK));
		
		}
		
		/** Total Days **/
		
		$startdate=$bout1->addedOn;
	if($bout1->stockstatus=='0')
	{
		$enddate=date('Y-m-d');
	}
	else{ 
	$enddate=$bout1->updatedOn;
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
				
			
$totaldaystillnow=$totinterval-$holiday;
		
		$odtype=$this->getordertype($bout1->orderstageid);
	$scheduler_data[] = array('sr_no'=>$i,
	'orderdate'=>date('d-M-Y g:i A',strtotime($bout1->addedOn)),
	'odtype'=>$odtype,
	'mname'=>$bout1->instruments_name,
	'jobcard'=>$bout1->job_card_no,
	'tat'=>$TATDATE." ".$finaltattime,
	'actual'=>$upd,
	'totdays'=>$totaldaystillnow,
	'stockinfo'=>$stock,
	'prno'=>$bout1->prno,
	'action'=>$html);
	$i++;
	}


	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
	
	}else{
		
		
		
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
		
		
	}

	
	
} 


function boughtoutdetails()
{
	$boughtid=$this->uri->segment('3');
	$stock=$this->input->post('stock'.$boughtid);
	$jobcardid=$this->input->post('jobcardid'.$boughtid);
	$orderid=$this->input->post('oid'.$boughtid);
	$instrumentids=$this->input->post('instrumentid'.$boughtid);
	$flowstages=$this->input->post('flowstages');
	$productionflowids=$this->input->post('productionflowids');
	if($stock=='1')
	{
		$prno='';
		
	}else{
		
		$prno=$this->input->post('prno'.$boughtid);
		
	}
	
		$insstock=$this->checkinstrumentstock($instrumentids);
	$data=array('stockstatus'=>$stock,'prno'=>$prno,'updatedOn'=>date('Y-m-d H:i:s'),'updatedBy'=>$_SESSION['logged_in']['user_id']);
	$this->db->where('id',$boughtid);
	$this->db->update('boughtoutfms',$data);
	
	$datacom=array('complete'=>'1','packed'=>'1');
	$this->db->where('id',$jobcardid);
	$this->db->update('order_instruments',$datacom);

		/** IF THIS IS SALESFORCE ORDER **/
		$this->checkifthisissforderandlastonetogetcompleted($orderid);
		/** END **/
	
		/** CHECK IF ORDER IS SELF ASSIGNED **/
	$selforder=$this->fmsmodel->checkifselforder($orderid);
    if($selforder=='1')
    {
        
       $newstock=$insstock+1;
       $updsa=array('stock'=>$newstock);
       $this->db->where('id',$instrumentids);
       $this->db->update('presto_instruments',$updsa);
        
        
        
    }
    
	/** END **/
	
	
/**	$closedata=array('closeorder'=>'1','closedOn'=>date('Y-m-d H:i:s'));
			$this->db->where('order_id',$orderid);
			$this->db->update('prestogroup_orders',$closedata); **/
	
	redirect(page_url.'Orderstage/process/'.$flowstages.'/'.$productionflowids);
	
	
}

		

function nextinqueue()
{
	$this->load->view('FMS/nextinque');
		
}

function nextupcoming_list()
{
	$scheduler_data=array();
	$availableflowstages='';
	$availableflowstage=array();
	$flowid=$this->uri->segment(3);
	$productionflowid=$this->uri->segment(4);
	$QRY = $this->db->select('tat,setorder,dependency')->from('fms_flow')->where('flow_id',$flowid)->get();
	$res = $QRY->result();
	$resttts=$this->db->select('id')->from('production_flow')->where('id',$productionflowid)->where('parallel','1')->get();
	$nrow=$resttts->num_rows();
	foreach($res as $fmsinformation);
	$tatstage = $fmsinformation->tat;
	$dependent=$fmsinformation->dependency;
	$setorder=$fmsinformation->setorder;
	if($dependent==0)
	{
		$newsetorder=$setorder-1;
		$qytre = $this->db->select('flow_id')->from('fms_flow')->where('setorder',$newsetorder)->where('production_flow_id',$productionflowid)->get();
		if($qytre->num_rows()>0)
		{
			foreach($qytre->result() as $qytre1);
			if($nrow=='1')
{
	if($setorder!='1' && $setorder!='2')
{
			$availableflowstage[]=$qytre1->flow_id;
}
				
}
			
		}
		
		
		
	}else
	{
				
				$ifdependexist=$this->db->select('a.dependentflowid')->from('flowdependency a')->where('flowid',$flowid)->get();
				if($ifdependexist->num_rows()>0)
				{
				foreach($ifdependexist->result() as $ifdependexist1)
				{
				$availableflowstage[]=$ifdependexist1->dependentflowid;
				}

				}
}	


				if(count($availableflowstage)>0)
				{
				$availableflowstages = "'" . implode ( "', '", $availableflowstage ) . "'";
				}		

if($availableflowstages<>'')
{
	$process=$this->db->select('d.file_number,b.flowstage,c.job_card_no,d.instruments_name,e.internal_order_no,f.plannedOn')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->where_in('b.flowstage',$availableflowstages,false)->where('b.userstatus','0')->order_by('b.id','DESC')->get();
	if($process->num_rows()>0)
	{
	$h=1;
foreach($process->result() as $proc)
{
	
	$scheduler_data[] = array(
				'sr_no'=>$h,
				'mname'=>strtoupper($proc->instruments_name),
				'jobcard'=>strtoupper($proc->job_card_no),
				'fileno'=>$proc->file_number,
				'planned'=>date('d-M-Y H:i:s',strtotime($proc->plannedOn))
				);
$h++;
}

$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);	
	
	}else{

$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);	
	
	}
	}else{

$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);	
	
	}
	
}

function getnextflowid($nextsetorder,$productionflowid)
{
	$seto=$this->db->select('flow_id')->from('fms_flow')->where('setorder',$nextsetorder)->where('production_flow_id',$productionflowid)->get();
	if($seto->num_rows()>0)
	{
		foreach($seto->result() as $setoo);

return $setoo->flow_id;		
	}else{
		
		echo "NO NEXT FLOW AVAILABLE";exit;
	}
	
}

function getsetorder($flowid)
{
	
	$rest=$this->db->select('setorder')->from('fms_flow')->where('flow_id',$flowid)->get();
	if($rest->num_rows()>0)
	{
		foreach($rest->result() as $restuie);
		
		return $restuie->setorder;
	}else{
		
		echo "NO NEXT FLOW AVAILABLE";exit;
	}
}

function addskiprecord($jobcardid,$orderid,$expval,$flowstage,$orderstage,$sendflowstage)
{
	for($i=0;$i<count($expval);$i++)
	{
		$data=array('order_id'=>$orderid,'flowstage'=>$flowstage,'sourceorderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'skippedflow'=>$expval[$i],'addedBy'=>$_SESSION['logged_in']['user_id'],'addedOn'=>date('Y-m-d H:i:s'),'sendto'=>$sendflowstage);
	
		$this->db->insert('skipflow',$data);
		
	}
	
	return true;
	
	
}



function checkinstrumentstock($id)
{
    
    $restyu=$this->db->select('stock')->from('presto_instruments')->where('id',$id)->get();
    if($restyu->num_rows()>0)
    {
        foreach($restyu->result() as $restyu1);
        
        $stock=$restyu1->stock;
        return $stock;
    }else
    {
        
        echo "INVALID MACHINE";exit;
        
    }
    
    
}



function machining_processservicestore()
	{
	$scheduler_data=array();
	$racklocation='';
	$flowstage=$this->uri->segment(3);
	$productionflowid=$this->uri->segment(4);
	/*GET CURRENT PROCESS SORT ORDER*/
		$QRY = $this->db->select('total_days,tat,set_time, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();
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
	$currentprocessorder = $flowstage;
	$setorder= $fmsinformation->setorder;
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$fmsprocess=$productionflowid;

	/** END **/
	if($fmsprocess<>0)
	{
	/** Get Process Name **/
	$process=$this->db->select('d.file_number,f.ordertype,f.fileno,a.jumpfabricationappl,a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,d.alias,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow,d.fabrication,f.planstartsfrom,d.fincode')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('a.who_wedo',$user_id)->order_by('b.userstatus','ASC')->order_by('b.addedOn','DESC')->get();
	/** End **/
	if($process->num_rows()>0)
	{
	// echo "<pre>"; print_r($process->result());exit;
	$i=1;	
	foreach($process->result() as $proc)
	{
		$ordertype = $proc->ordertype;
			if($ordertype=='1'){
				
				$fileno=$proc->file_number;
			}else{
			
				$fileno=$proc->fileno;
			}
		
		$bomstock=$this->getstockandidfrombombyfincode($proc->fincode);
		if(count($bomstock)>0)
		{
		foreach($bomstock as $bomstock1);

		$itemid=$bomstock1->id;
		$current_stocks=$bomstock1->current_stock;
		$locationid=$bomstock1->location_id;
		$racklocation=$this->getracklocationname($locationid);
		}else{
			
			$itemid=0;
			$current_stocks=0;
			$racklocation='';
		}

   
	if($proc->userstatus=='0')
	{
		
		
  if(floatval($proc->qty)>floatval($current_stocks))
  {
	$ach="selected";
	$bch="";
	$y="NO";
	  
  }else{
	  
	$ach="";
	$bch="selected";
	$y="YES";
	  
  }
  
  
	$prestui="<span class='btn btn-danger btn-xs'>Update Pending</span>";
	//$action='<a href="javascript:;" onclick="markstagedone('."'".$proc->orderstageid."'".','."'".$proc->flowstage."'".','."'".$proc->originalorderid."'".','."'".$proc->jobcardid."'".')"><span class="btn btn-xs btn-warning">Mark as done</span></a>';
	$html='<button class="btn btn-xs btn-warning waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$proc->orderstageid.'" onclick="getmaterialsforpr('."'".$proc->orderstageid."'".','."'".$itemid."'".','."'".$proc->fincode."'".','."'".$proc->item_id."'".','."'".$proc->qty."'".','."'".$y."'".');">Mark as Done</button>';

	$html.= '<div id="con-close-modal'.$proc->orderstageid.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<form action="'.page_url.'Orderstage/servicekittingbop/'.$proc->orderstageid.'/'.$proc->flowstage.'/'.$proc->originalorderid.'/'.$fmsprocess.'" method="post" id="loginForm" onSubmit="return checkvalidation('.$proc->orderstageid.'); checkifqtyisfilled();">

	<div id="pageloader">
	<img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
	</div>
	<div class="modal-dialog modal-lg">
	<div class="modal-content">
	<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	<h4 class="modal-title">FULL KITTING FOR <span style="color:red;">'.strtoupper($proc->instruments_name).'</span></h4>
	</div>
	<div class="modal-body">

	
	<div class="row">
	<div class="col-md-2">
	<div class="form-group">
	<label for="field-1" class="control-label">JOBCARD NUMBER</label><br/>
	<span id="error_purpose" style="color:red;"></span>
	<input type="text" class="form-control" id="jobcard_no'.$proc->orderstageid.'" style="text-transform: uppercase;width:100%;" name="jobcard_no'.$proc->orderstageid.'" placeholder="" value="'.$proc->job_card_no.'" readonly required>
	<input type="hidden" name="jobcardid'.$proc->orderstageid.'" value="'.$proc->jobcardid.'">
	<input type="hidden" name="instrumentid'.$proc->orderstageid.'" value="'.$proc->item_id.'">
	</div>
	</div>';

 
   
	$html.='<div class="col-md-4">
	<div class="form-group">
	<label for="field-2" class="control-label">MATERIAL IS IN STOCK <span id="error_status" style="color:red;">*</span></label><br>

	<select class="form-control" id="stock'.$proc->orderstageid.'" name="stock'.$proc->orderstageid.'" required onchange="getstockdetail('.$proc->orderstageid.');">';
	
	if($y=='YES')
	{
		$html.='<option value="1">YES</option>';
	}else{

		$html.='<option value="0">NO</option>';
		
	}
	
	
	$html.='</select>
	</div>
	</div>
	
	<div class="col-md-3">
	
	<div class="form-group">
	<label for="field-1" class="control-label">Required Stock</label><span id="error_purpose" style="color:red;">*</span><br/>

	<input type="text" class="form-control" id="currrstst'.$proc->orderstageid.'" style="text-transform: uppercase;" name="currrstst'.$proc->orderstageid.'" placeholder="" value="'.$proc->qty.'" readonly>
	</div>
	
	</div>
	
	
	<div class="col-md-3">
	
	<div class="form-group"> 
	<label for="field-1" class="control-label">Current Stock</label><span id="error_purpose" style="color:red;">*</span><br/>

	<input type="text" class="form-control" id="requusus'.$proc->orderstageid.'" style="text-transform: uppercase;" name="requusus'.$proc->orderstageid.'" placeholder="" value="'.$current_stocks.'" readonly>
	</div>
	
	</div>
	
		
	</div>';


	
	$prnos=$this->db->query('SELECT id, purno FROM purchase_request ORDER BY  CAST(purno AS decimal) DESC LIMIT 1');
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
	
	if($y=='YES')
	{
		$adsdd="none";
	}else{
		
		$adsdd="block";
	}
	$html.='<div class="row" id="prreq'.$proc->orderstageid.'" style="margin-top:20px;display:'.$adsdd.'">
	<div class="col-md-3">
	<div class="form-group">
	<label for="field-1" class="control-label">PR NUMBER</label><span id="error_purpose" style="color:red;">*</span><br/>

	<input type="text" class="form-control" id="prno'.$proc->orderstageid.'" style="text-transform: uppercase;" name="prno'.$proc->orderstageid.'" placeholder="" value="'.$code.'" readonly>
	</div>
	</div>

	<div class="col-md-6" style="display:none">
	<div class="form-group">
	<label for="field-1" class="control-label">MATERIAL NAME</label><span id="error_purpose" style="color:red;">*</span><br/>

	<textarea class="form-control" id="mtname'.$proc->orderstageid.'" style="text-transform: uppercase;" name="mtname'.$proc->orderstageid.'" placeholder="" value="" col="20"></textarea>

	</div>
	</div>';
	
	
	$html.='<div class="col-md-12">
	
	<div class="text-center" style="font-size:15px">Choose Item to Generate PR</div> 
<div class="pull-right"><input type="text" onkeyup="searchtable('.$proc->orderstageid.');" name="search" id="searchableee" placeholder="Search Item" class="form-control" style="height:30px"></div>
<div style="height:300px;overflow-y:auto;width:100%;"> 	
 <table class="table table-bordered" id="materialtable'.$proc->orderstageid.'">
    <thead>
      <tr>
		<th style="width:2%">Select</th>
        <th style="width:8%">Item Name</th>
        <th style="width:8%">Fincode</th>
		<th style="width:8%">Specification</th>
        <th style="width:8%">Quantity</th>
		<th style="width:5%">Current Stock</th>
        <th style="width:10%">Picture</th>
		<th style="width:10%">Reason</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
</div>	
	
	
	</div>
    </div>


	</div>
	<div class="modal-footer">
	<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
	<input type="submit" id="save'.$proc->orderstageid.'" class="btn btn-info" value="Submit">
	</div>
	</div>
	</div>
	</form>
	</div>';


	$actualdate="";
	$actualtime="";
	$completedate="";
	$addedtimefinal="";
	}else{
	$compdate=date('d-M-Y H:i:s',strtotime($proc->stagecompletedate));

	date_default_timezone_set("Asia/Kolkata");
	$actualdate = date('d-M-Y', strtotime($proc->stagecompletedate));
	$time = date('H:i:s', strtotime($proc->stagecompletedate));
	$actualtime = "<br>". date('g:i A', strtotime($time)); 


	$prestui="<span class='btn btn-success btn-xs'>Remarks Updated</span>";
	$action="Task completed on ".$compdate;
	$html="Action has been taken by You.";

	date_default_timezone_set("Asia/Kolkata");
	$completedate = date('Y-m-d', strtotime($proc->stagecompletedate));
	$completetime = date('H:i:s', strtotime($proc->stagecompletedate));
	$addedtimefinal = "<br>". date('g:i A', strtotime($completetime));
	}



	$material="";
	$prno="";
	$stockinfo="";
	$query = $this->db->select('jobcardid, stock, prno, material')->from('kitting_bop_details')->where('jobcardid',$proc->jobcardid)->get();
	$res = $query->result();
	if($query->num_rows()>0){
	foreach($res as $row){
	$stock = $row->stock; 
	if($stock=='0'){
	$stockinfo = "OUT OF STOCK";
	$prno = "<a href='".page_url."Store/pr/".$row->prno."' target='_blank'>".$row->prno."</a>";
	$material = $row->material;
	}else{
	$stockinfo = "IN STOCK";
	$prno="";	
	$material="";
	}
	}
	}


	/** Get User Department **/
	$udep=$this->db->select('b.department')->from('system_users a')->join('departments b','a.department_id=b.department_id')->where('a.user_id',$_SESSION['logged_in']['user_id'])->get(); 
	if($udep->num_rows()>0)
	{
	foreach($udep->result() as $depp);
	$depart=$depp->department;

	}else
	{
	$depart='';
	}

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
					
					/** Check if instrument requires fabrication **/
					/**$ryt=$this->db->select('b.fabrication')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->where('a.id',$proc->jobcardid)->get();
					if($ryt->num_rows()>0)
					{
						foreach($ryt->result() as $rtyeyte);
						$fabreq=$rtyeyte->fabrication;
						
					}else{ $fabreq=0 ;}
					
					if($fabreq==0)
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
					}else{ $markapp=1;  }
					
				}else{
					
					$markapp=1;
				}
				/** End **/
	/** End **/

		/** Check for merge fms intersection **/
				if($markapp==1)
				{
					
				$markapp=$this->fmsmodel->checkformergerpoint($flowstage,$proc->jobcardid,$proc->originalorderid,$proc->fabrication,$proc->jumpfabricationappl);
				}
				

				
				/** End **/

	if($markapp==1)
	{	
	$timestamp=$this->getprevioustimestamp($tatstage,$proc->plannedOn,$proc->jobcardid,$flowstage,$proc->orderstageid);

	$tattime = DateTime::createFromFormat('d-M-Y g:i A', $timestamp);
		
	$finaltattime=$tattime->format('g:i A');
	$tatdate=$this->getupcomingtatdatenew($tatstage,$proc->plannedOn,$proc->jobcardid,$timestamp,$totaldaysslave,$proc->orderstageid,$day);

	if($proc->userstatus=='0')
	{
	$completedon="";
	}else
	{
	$completedon=date('d-M-Y g:i A',strtotime($proc->stagecompletedate));
	}

$odtype=$this->getordertype($proc->originalorderid);
$machinealias=$this->fmsmodel->getmachinealiasfromims($proc->fincode);
if(count($machinealias)>0)
{
$aliasname=$machinealias['name'];
	if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$proc->fincode.'.jpg')){

	$IMG = product_items.$proc->fincode.'.jpg';
	$image = "<img src='".$IMG."' style='width:100px;height:100px;'>";
	}else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$proc->fincode.'.JPG')){

	$IMG = product_items.$proc->fincode.'.JPG';
	$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
	}else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$proc->fincode.'.jpeg')){
	$IMG = product_items.$proc->fincode.'.jpeg';
	$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
	}else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$proc->fincode.'.JPEG')){

	$IMG = product_items.$proc->fincode.'.JPEG';
	$image = "<img src='".$IMG."'  style='width:100px;height:100px;'>";
	}else{
	$image="";
	}
}else
{
$aliasname='';
$image='';
}

	if($y=='YES')
	{
	$ys="IN STOCK";
	}else
	{
	$ys="OUT OF STOCK";
	}
	
	$scheduler_data[] = array('sr_no'=>$i,
	'orderdate'=>$timestamp,
	'odtype'=>$odtype,
	'image'=>$image,
	'mname'=>strtoupper($proc->instruments_name)."<br/><br/>".$aliasname,
	'fincode'=>$proc->fincode,
	'qty'=>strtoupper($proc->qty),
	'stockstatus'=>$ys,
	'jobcard'=>strtoupper($proc->job_card_no),
	'fileno'=>$fileno,
	'racklocation'=>$racklocation,
	'factory'=>strtoupper($depart),
	'tat'=>$tatdate,
	'actual'=>$completedon,
	'totdays'=>'',
	'stockinfo'=>$stockinfo,
	'prno'=>$prno,
	'material'=>$material,
	'status'=>$prestui,
	'action'=>$html);

	$i++;
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
	} 
	
	function getstockandidfrombombyfincode($fincode)
	{
		
		$restyu=$this->db->select('id,current_stock,location_id')->from('machine_parts_with_picture')->where('fincode',$fincode)->get();
		return $restyu->result();
		
	}
	
	
	function servicekittingbop()
	{
	    
	$selitem=array();
	$gprno='';
	$orderstage=$this->uri->segment(3);
	$flowstage=$this->uri->segment(4);
	$orderid=$this->uri->segment(5);
	$selforder=$this->fmsmodel->getselforder($orderid);
	$productionflowid=$this->uri->segment(6);
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$newstage=[];
	$isfinalstep=0;
	$jobcardid=$this->input->post('jobcardid'.$orderstage);
	$stock=$this->input->post('stock'.$orderstage);
	$instrumentid=$this->input->post('instrumentid'.$orderstage);
	$machid=$this->storemodel->getmachineid($jobcardid);
	
	
	/** Move to next stage **/
			$currentstage=$flowstage;
			
			$getset=$this->db->select('setorder,finalstep,total_days,set_time')->from('fms_flow')->where('flow_id',$currentstage)->where('production_flow_id',$productionflowid)->get();
			
				foreach($getset->result() as $ggetset);
				$currentsetorder=$ggetset->setorder;	
				$isfinalstep=$ggetset->finalstep;
					$tatday=$ggetset->total_days;
				$tathours=$ggetset->set_time;
				/** Get Next Order **/
				$getneworder=$this->db->select('flow_id')->from('fms_flow')->where('setorder>',$currentsetorder)->where('production_flow_id',$productionflowid)->limit(1)->order_by('setorder','ASC')->get();
				if($getneworder->num_rows()>0)
			{
				foreach($getneworder->result() as $getneworder1);
				$restymove=$this->db->select('moveto')->from('fms_flow')->where('flow_id',$flowstage)->get();
				foreach($restymove->result() as $restymove1)
				{
				if(($restymove1->moveto<>0) && ($restymove1->moveto<>''))
				{
				$moveflow=$restymove1->moveto;
				$movearr=explode(',',$moveflow);
				foreach($movearr as $movea)
				{
				$newstage[]=$movea;
				}
				
				} else
				{
					$newstage[]=$getneworder1->flow_id;
				} 
				}
				
			}


	if($stock=='0')
				{
					$pr=$this->input->post('prno'.$orderstage);
					$mtname=$this->input->post('mtname'.$orderstage);
					
				}else{ $pr=''; $mtname=''; }
				
			/** Detail Entry **/
			$detailentry=array('orderstageid'=>$orderstage,'jobcardid'=>$jobcardid,'stock'=>$stock,'prno'=>$pr,'material'=>$mtname,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->insert('kitting_bop_details',$detailentry);
			$effrow1=$this->db->affected_rows();
			
			/** MAKE ENTRY IN PR **/
			if(count($this->input->post('itemselected'))>0)
				{
				$selitem=$this->input->post('itemselected');
				$gprno=$this->storemodel->generatepr($pr,$jobcardid);
				}
			
			
				if(count($this->input->post('itemselected'))>0)
				{
				$seleee=$this->input->post('itemselected');
				for($r=0;$r<count($this->input->post('itemselected'));$r++)
				{
					$itemid=$seleee[$r];
					$qtyss=$this->input->post('qtyparts'.$itemid);
					/** BLOCK ALL ITEMS IN JOBCARD **/
					$this->storemodel->blockallserviceitems($instrumentid,$jobcardid,$itemid,$gprno,$qtyss);
					/** END **/
				}

				}
		
		
			
/*** PR END **/
			
			/** End**/
			if($effrow1>0)
			{
			$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
			$this->db->where('id',$orderstage);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$data);
			$effrow=$this->db->affected_rows();	
			}
			
			
		if(count($newstage)>0)
			{
		
			for($u=0;$u<count($newstage);$u++)
			{
			
	$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$newstage[$u])->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{	
			
		if($newstage[$u]<>0)
			{	$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage[$u],'userstatus'=>'0');
			$this->db->insert('order_stage',$newdata);
			
			/** Add Tat **/
						$stageid=$this->db->insert_id();
						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$newstage[$u]);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			
			}
			}else
			{
				foreach($checkodd->result() as $checkodd1);
				/** Add Tat **/
						$stageid=$checkodd1->id;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$newstage[$u],0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$newstage[$u])->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$newstage[$u],'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$newstage[$u]);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
				
			}
			
			}
			
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Order Moved to Next Process.</span></div>');
				redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
			
		}else{
				
				
				if($isfinalstep!=0)
				{
					
						/** Check if Merge is available **/
				  $merger=$this->db->select('id,mergewith,flowid')->from('fmsmerge')->where('productionflow',$productionflowid)->get();
					if($merger->num_rows()>0)
					{
						foreach($merger->result() as $mergerdata);
						$checkodd=$this->db->select('id')->from('order_stage')->where('orderid',$orderid)->where('jobcardid',$jobcardid)->where('flowstage',$mergerdata->flowid)->where('userstatus','0')->get();
			if($checkodd->num_rows()==0)
			{
						$orderstmergedata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$mergerdata->flowid,'userstatus'=>'0','remarks'=>'','addedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('order_stage',$orderstmergedata); 
				        $odm=$this->db->insert_id();
						/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
			}else
			{
				foreach($checkodd->result() as $prevorderstage);
				$odm=$prevorderstage->id;
				
				/** Add Tat **/
						$stageid=$odm;
						$settatdate=$this->fmsmodel->gettatformis($stageid,$mergerdata->flowid,0,$jobcardid,$orderid);
						if($settatdate<>'')
						{
							/** Check if exist **/
							$checkfmstat=$this->db->select('id')->from('fmstatdate')->where('flowstage',$mergerdata->flowid)->where('jobcardid',$jobcardid)->get();
						if($checkfmstat->num_rows()==0)
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$mergerdata->flowid,'jobcardid'=>$jobcardid,'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}else
						{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'tasktat'=>$returnedtatdate,'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->where('flowstage',$mergerdata->flowid);
						$this->db->where('jobcardid',$jobcardid);
						$this->db->update('fmstatdate',$settat);
						}
						}
						/** END **/
				
				
				
			}
					
						if($odm<>0)
						{
							$mergehistdata=array('orderstageid'=>$odm,'oldproductionflow'=>$productionflowid,'jobcardid'=>$jobcardid,'newproductionflow'=>$mergerdata->mergewith,'oldfmsflow'=>$flowstage,'newfmsflow'=>$mergerdata->flowid,'addedOn'=>date('Y-m-d H:i:s'));
							$this->db->insert('fmsmergehistory',$mergehistdata);
							
							$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Merged.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
							
							}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to merge flow.</span></div>');
							  redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
					}else { 
					    
					   
					$newdata=array('complete'=>'1');
					$this->db->where('order_id',$orderid);
					$this->db->where('id',$jobcardid);
					$this->db->update('order_instruments',$newdata);


					/** IF THIS IS SALESFORCE ORDER **/
					$this->checkifthisissforderandlastonetogetcompleted($orderid);

					/** END **/
					   
					
					if($selforder=='1')
					{
					$this->fmsmodel->addstock($jobcardid);
					}

$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);					
					} 
					/** Merge **/
					$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Order Completed.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}else
				{
				
				/** End **/
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
				}
			}
				/** End **/
		

			
	}

	

function getlasttwodigit($str,$n)
{
    $start = strlen($str) - $n; 
  
// New string 
$str1 = ''; 
  
for ($x = $start; $x < strlen($str); $x++) { 
      
    // Appending characters to the new string 
    $str1 .= $str[$x]; 
} 
  
  return $str1;
    
}


function getracklocationname($locationid)
{
    $rlkoc='';
    $Resteyr=$this->db->select('rack_location')->from('store_rack_location')->where('id',$locationid)->get();
    if($Resteyr->num_rows()>0)
    {
        foreach($Resteyr->result() as $Resteyr1);
         $rlkoc=$Resteyr1->rack_location;
    }
    
    return $rlkoc;
    
}



  function getordertype($odid)
    {
        $od='';
        $reste=$this->db->select('order_type,selforder')->from('prestogroup_orders')->where('order_id',$odid)->get();
        if($reste->num_rows()>0)
        {
            foreach($reste->result() as $reste1);
            if($reste1->selforder=='1')
            {
                $od="PRESTO INTERNAL";
            }else
        {
            $od=strtoupper($reste1->order_type);
            
        }
        }
        
        return $od;
        
    }
	    




	function checkifthisissforderandlastonetogetcompleted($orderid)
	{

		$rest=$this->db->select('sforder,oppid')->from('prestogroup_orders')->where('order_id',$orderid)->where('sforder>','0')->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $restt);

			$oppid=$restt->oppid;
			

			$restyu=$this->db->select('id')->from('order_instruments')->where('complete','0')->where('order_id',$orderid)->get();
			if($restyu->num_rows()==0)
			{
				/** SEND INTIMATION THAT PRODUCTION IS READY **/

				/** GET AUTHENTICATION TOKEN FROM SALESFORCE **/
				
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
				$result=json_decode($apiResponse,true);
				if(count($result)>0)
				{

				$acctoken=$result['access_token'];	
				if($acctoken<>'')
				{


					/** MARK AS READY **/

					$ch = curl_init();

					curl_setopt($ch, CURLOPT_URL, 'https://presto.my.salesforce.com/services/data/v50.0/sobjects/Opportunity/'.$oppid.'/');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');

					curl_setopt($ch, CURLOPT_POSTFIELDS,"{\"Production_Ready_Status__c\":\"true\"}");

					$headers = array();
					$headers[] = 'Authorization: Bearer '.$acctoken;
					$headers[] = 'Content-Type: application/json';
					curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

					$result = curl_exec($ch);
					//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);

					/** END **/


				}


				}


				/** END **/


			}

		}



	}


}

?>
