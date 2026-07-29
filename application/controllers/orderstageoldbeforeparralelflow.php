<?php
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
		$this->load->model('User_model','user');
		$this->load->model('Master_model','master');
		
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
	$process=$this->db->select('a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('a.who_wedo',$user_id)->order_by('b.userstatus','ASC')->get();
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
				 $action='<a href="javascript:;" onclick="markstagedone('."'".$proc->orderstageid."'".','."'".$proc->flowstage."'".','."'".$proc->originalorderid."'".','."'".$proc->jobcardid."'".','."'".$productionflowid."'".')"><span class="btn btn-xs btn-warning">Mark as done</span></a>';
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
			//echo $TATDATE; exit;
			/*TAT Date*/
			if($this->uri->segment(3)=='1' || $this->uri->segment(3)=='2'){
					$date11 = new DateTime($addeddate); 
					$date22 = new DateTime($completedate); 
					$interval1 = $date11->diff($date22); 
					$ttldays = $interval1->d; 
					if($ttldays=='0'){
					$totalday = "1";
			}else{
			$totalday = $ttldays;
			}
			/*TAT Date*/
			$lastcompdate = date('Y-m-d', strtotime($proc->added_on));
			$TATDATE=date('d-M-Y', strtotime($lastcompdate."+".$totaldaysslave." days"));
			/*TAT Date*/
			
			}else{
						
						
					/*GET DATE OF PRIVIOUS STEPS*/
					$previousorder = $tatstage;
					
					if($previousorder<>0){
					$abc = $this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$proc->jobcardid)->get();
						if($abc->num_rows()>0){
					foreach($abc->result() as $pastinfo);
					$complitiontime = $pastinfo->addedOn;
						}else{
						$complitiontime = $proc->added_on;}
					}else{
					$complitiontime = $proc->added_on;	
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
			} 
			
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
			
			
		$scheduler_data[] = array('sr_no'=>$i,
			'orderdate'=>$addeddate.$addedtime,
			'mname'=>strtoupper($proc->instruments_name),
			'jobcard'=>strtoupper($proc->job_card_no),
			'factory'=>strtoupper($depart),
			'planned'=>date('d-M-Y',strtotime($proc->plannedOn)).$addedtime1,
			'tat'=>$TATDATE.$addedtime,
			'actual'=>$completedate.$completetime,
			'totdays'=>$totalday,
			'status'=>$prestui,
			'action'=>$action);			
									  
									  
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
	$jobcardid=$this->uri->segment(6);
	$productionflowid=$this->uri->segment(7);
	$user_id =$this->session->userdata['logged_in']['user_id'];
	$newstage="0";
	/** Move to next stage **/
		$currentstage=$flowstage;
		$getset=$this->db->select('setorder')->from('fms_flow')->where('production_flow_id',$productionflowid)->where('flow_id',$currentstage)->get();
		if($getset->num_rows()>0)
		{
			foreach($getset->result() as $ggetset);
			$currentsetorder=$ggetset->setorder;	
			/** Get Next Order **/
			$getneworder=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id',$productionflowid)->where('setorder>',$currentsetorder)->limit(1)->order_by('setorder','ASC')->get();
			if($getneworder->num_rows()>0)
		{
			foreach($getneworder->result() as $getneworder1);
			$newstage=$getneworder1->flow_id;
			
		}else{
			
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
			/** End **/
			
		}else{
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
		
		if($newstage<>0)
		{
	$data=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
	$this->db->where('id',$orderstage);
	$this->db->where('orderid',$orderid);
	$this->db->update('order_stage',$data);
	$effrow=$this->db->affected_rows();	
	if($effrow>0)
	{
		
		$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage,'userstatus'=>'0');
		$this->db->insert('order_stage',$newdata);
		/** End **/
		$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Updated! Record moved in next stage.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
	}else{
		
		$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Please try again later.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
	}
	
		}else{
			
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
}


		
	function machining_process3()
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
$process=$this->db->select('a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('a.who_wedo',$user_id)->order_by('b.userstatus','ASC')->get();
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
					$abc = $this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$proc->jobcardid)->get();
					foreach($abc->result() as $pastinfo);
					$complitiontime = $pastinfo->addedOn;
					}else{
					$complitiontime = $proc->added_on;	
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
$scheduler_data[] = array('sr_no'=>$i,
'orderdate'=>$addeddate.$addedtime,
'mname'=>strtoupper($proc->instruments_name),
'jobcard'=>strtoupper($proc->job_card_no),
'factory'=>strtoupper($depart),
'planned'=>date('d-M-Y',strtotime($proc->plannedOn)).$addedtime1,
'tat'=>$TATDATE.$addedtimefinal,
'actual'=>$completedate.$addedtimefinal,
'totdays'=>$totalday,
'stockinfo'=>$stockinfo,
'prno'=>$prno,
'material'=>$material,
'status'=>$prestui,
'action'=>$html);
 
 
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

function kittingbop()
{
$orderstage=$this->uri->segment(3);
$flowstage=$this->uri->segment(4);
$orderid=$this->uri->segment(5);
$productionflowid=$this->uri->segment(6);
$user_id =$this->session->userdata['logged_in']['user_id'];
$newstage="0";
$jobcardid=$this->input->post('jobcardid'.$orderstage);
$stock=$this->input->post('stock'.$orderstage);
/** Move to next stage **/
		$currentstage=$flowstage;
		$getset=$this->db->select('setorder')->from('fms_flow')->where('flow_id',$currentstage)->get();
		if($getset->num_rows()>0)
		{
			foreach($getset->result() as $ggetset);
			$currentsetorder=$ggetset->setorder;	
			/** Get Next Order **/
			$getneworder=$this->db->select('flow_id')->from('fms_flow')->where('setorder>',$currentsetorder)->where('production_flow_id',$productionflowid)->limit(1)->order_by('setorder','ASC')->get();
			if($getneworder->num_rows()>0)
		{
			foreach($getneworder->result() as $getneworder1);
			$newstage=$getneworder1->flow_id;
			
		}else{
			
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
			/** End **/
			
		}else{
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
		
		if($newstage<>0)
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
		
		$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage,'userstatus'=>'0');
		$this->db->insert('order_stage',$newdata);
		
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
$process=$this->db->select('a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('a.who_wedo',$user_id)->order_by('b.userstatus','ASC')->get();
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
$restt=$this->db->select('flow_id,fms_flow')->from('fms_flow')->where('flow_id<',$flowstage)->get();
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
					$abc = $this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$proc->jobcardid)->get();
						if($abc->num_rows()>0){
					foreach($abc->result() as $pastinfo);
					$complitiontime = $pastinfo->addedOn;
						}
						$complitiontime = $proc->added_on;	
					}else{
					$complitiontime = $proc->added_on;	
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
$qry = $this->db->select('a.jobcardid,a.qcstatus,a.rejectreason,a.backtrackto, b.flow_id, b.fms_flow')->from('qcremarks a')->join('fms_flow b','a.backtrackto=b.flow_id','left')->where('a.jobcardid',$proc->jobcardid)->get();
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
$scheduler_data[] = array('sr_no'=>$i,
'orderdate'=>$addeddate.$addedtime,
'mname'=>strtoupper($proc->instruments_name),
'jobcard'=>strtoupper($proc->job_card_no),
'factory'=>strtoupper($depart),
'planned'=>date('d-M-Y',strtotime($proc->plannedOn)).$addedtime1,
'tat'=>$TATDATE.$addedtime,
'actual'=>$completedate.$addedtimepunch,
'totdays'=>$totalday,
'status'=>$prestui,
'qcstatus'=>$qcstatus,
'backtrack'=>$backtrack,
'action'=>$html);
 
 
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

	
	function qcremarks()
{
	
$orderstage=$this->uri->segment(3);
$flowstage=$this->uri->segment(4);
$orderid=$this->uri->segment(5);
$productionflowid=$this->uri->segment(6);
$user_id =$this->session->userdata['logged_in']['user_id'];
$newstage="0";
$jobcardid=$this->input->post('jobcardid'.$orderstage);
$qcstatus=$this->input->post('status'.$orderstage);

if($qcstatus=='1')
{
	
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
			$newstage=$getneworder1->flow_id;
			
		}else{
			
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
			/** End **/
}else{
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
		
		if($newstage<>0)
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
		
		$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage,'userstatus'=>'0');
		$this->db->insert('order_stage',$newdata);
		
		$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Order Moved to Next Process.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
	}else{
		
		$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
	}
	
	}else{ $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Unable to Update Pleae try again.</span></div>');
	redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid); }
			
		}else{
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Next Flow Not Defined.</span></div>');
			redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
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
		
		$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$backtrackid,'userstatus'=>'0');
		$this->db->insert('order_stage',$newdata);
		
		$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000;">Order Moved to Next Process.</span></div>');
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
$process=$this->db->select('a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('a.who_wedo',$user_id)->order_by('b.userstatus','ASC')->get();
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
$restt=$this->db->select('flow_id,fms_flow')->from('fms_flow')->where('flow_id<',$flowstage)->get();
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
					$abc = $this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$proc->jobcardid)->get();
					foreach($abc->result() as $pastinfo);
					$complitiontime = $pastinfo->addedOn;
					}else{
					$complitiontime = $proc->added_on;	
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
$qry = $this->db->select('a.jobcardid,a.qcstatus,a.rejectreason,a.backtrackto, a.checklist, b.flow_id, b.fms_flow')->from('qcfinalremarks a')->join('fms_flow b','a.backtrackto=b.flow_id','left')->where('a.jobcardid',$proc->jobcardid)->get();
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

$scheduler_data[] = array('sr_no'=>$i,
'orderdate'=>$addeddate.$addedtime,
'mname'=>strtoupper($proc->instruments_name),
'jobcard'=>strtoupper($proc->internal_order_no).'/'.strtoupper($proc->job_card_no),
'factory'=>strtoupper($depart),
'planned'=>date('d-M-Y',strtotime($proc->plannedOn)).$addedtime1,
'tat'=>$TATDATE.$addedtime,
'actual'=>$completedate.$addedtimefinal,
'totdays'=>$totalday,
'qcstatus'=>$qcstatus,
'backto'=>$backtrack,
'status'=>$prestui,
'action'=>$html);
 
 
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

	
	
function qcfinalremarks()
{
	
$orderstage=$this->uri->segment(3);
$flowstage=$this->uri->segment(4);
$orderid=$this->uri->segment(5);
$productionflowid=$this->uri->segment(6);
$user_id =$this->session->userdata['logged_in']['user_id'];
$newstage="0";
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
$process=$this->db->select('a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('a.who_wedo',$user_id)->order_by('b.userstatus','ASC')->get();
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
					$abc = $this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$proc->jobcardid)->get();
						if($abc->num_rows()>0){
					foreach($abc->result() as $pastinfo);
					$complitiontime = $pastinfo->addedOn;
					}else{
							$complitiontime = $proc->added_on;	
						}}else{
					$complitiontime = $proc->added_on;	
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

$scheduler_data[] = array('sr_no'=>$i,
'orderdate'=>$addeddate.$addedtime,
'mname'=>strtoupper($proc->instruments_name),
'jobcard'=>strtoupper($proc->job_card_no),
'factory'=>strtoupper($depart),
'planned'=>date('d-M-Y',strtotime($proc->plannedOn)).$addedtime1,
'tat'=>$TATDATE.$addedtime,
'actual'=>$completedate.$addedtimefinal,
'totdays'=>$totalday,
'challan'=>$challanno,
'weight'=>$weight,
'pcs'=>$pcs,
'status'=>$prestui,
'action'=>$html);
 
 
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


function materialpaintout()
{
	
$orderstage=$this->uri->segment(3);
$flowstage=$this->uri->segment(4);
$orderid=$this->uri->segment(5);
$productionflowid=$this->uri->segment(6);
$user_id =$this->session->userdata['logged_in']['user_id'];
$newstage="0";
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
			$newstage=$getneworder1->flow_id;
			
		}else{
			
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
			/** End **/
}else{
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
		
		if($newstage<>0)
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
		
		$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage,'userstatus'=>'0');
		$this->db->insert('order_stage',$newdata);
		
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
$process=$this->db->select('a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('a.who_wedo',$user_id)->order_by('b.userstatus','ASC')->get();
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
					$abc = $this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$proc->jobcardid)->get();
					foreach($abc->result() as $pastinfo);
					$complitiontime = $pastinfo->addedOn;
					}else{
					$complitiontime = $proc->added_on;	
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

$scheduler_data[] = array('sr_no'=>$i,
'orderdate'=>$addeddate.$addedtime,
'mname'=>strtoupper($proc->instruments_name),
'jobcard'=>strtoupper($proc->job_card_no),
'factory'=>strtoupper($depart),
'planned'=>date('d-M-Y',strtotime($proc->plannedOn)).$addedtime1,
'tat'=>$TATDATE.$addedtime,
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

$results = array(
"sEcho" => 1,
"iTotalRecords" => count($scheduler_data),
"iTotalDisplayRecords" => count($scheduler_data),
"aaData"=>$scheduler_data);
echo json_encode($results);

}

}
} 

function materialpaintin()
{
	
$orderstage=$this->uri->segment(3);
$flowstage=$this->uri->segment(4);
$orderid=$this->uri->segment(5);
$productionflowid=$this->uri->segment(6);
$user_id =$this->session->userdata['logged_in']['user_id'];
$newstage="0";
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
			$newstage=$getneworder1->flow_id;
			
		}else{
			
			$this->session->set_flashdata('message','Next Flow Not Defined');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
			/** End **/
}else{
			$this->session->set_flashdata('message','Next Flow Not Defined');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
		
		if($newstage<>0)
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
		
		$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage,'userstatus'=>'0');
		$this->db->insert('order_stage',$newdata);
		
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
$process=$this->db->select('a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('a.who_wedo',$user_id)->order_by('b.userstatus','ASC')->get();
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
					$abc = $this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$proc->jobcardid)->get();
					foreach($abc->result() as $pastinfo);
					$complitiontime = $pastinfo->addedOn;
					}else{
					$complitiontime = $proc->added_on;	
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

$scheduler_data[] = array('sr_no'=>$i,
'orderdate'=>$addeddate.$addedtime,
'mname'=>strtoupper($proc->instruments_name),
'jobcard'=>strtoupper($proc->job_card_no),
'factory'=>strtoupper($depart),
'planned'=>date('d-M-Y',strtotime($proc->plannedOn)).$addedtime1,
'tat'=>$TATDATE.$addedtime,
'actual'=>$completedate.$addedtimefinal,
'totdays'=>$totalday,
'challan'=>$challanno,
'weight'=>$weight,
'pcs'=>$pcs,
'status'=>$prestui,
'action'=>$html);
 
 
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


function materialplatingout()
{
	
$orderstage=$this->uri->segment(3);
$flowstage=$this->uri->segment(4);
$orderid=$this->uri->segment(5);
$productionflowid=$this->uri->segment(6);
$user_id =$this->session->userdata['logged_in']['user_id'];
$newstage="0";
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
			$newstage=$getneworder1->flow_id;
			
		}else{
			
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
			/** End **/
}else{
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
		
		if($newstage<>0)
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
		
		$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage,'userstatus'=>'0');
		$this->db->insert('order_stage',$newdata);
		
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
$process=$this->db->select('a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('a.who_wedo',$user_id)->order_by('b.userstatus','ASC')->get();
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
					$abc = $this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->where('jobcardid',$proc->jobcardid)->get();
					foreach($abc->result() as $pastinfo);
					$complitiontime = $pastinfo->addedOn;
					}else{
					$complitiontime = $proc->added_on;	
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

$scheduler_data[] = array('sr_no'=>$i,
'orderdate'=>$addeddate.$addedtime,
'mname'=>strtoupper($proc->instruments_name),
'jobcard'=>strtoupper($proc->job_card_no),
'factory'=>strtoupper($depart),
'planned'=>date('d-M-Y',strtotime($proc->plannedOn)).$addedtime1,
'tat'=>$TATDATE.$addedtime,
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

$results = array(
"sEcho" => 1,
"iTotalRecords" => count($scheduler_data),
"iTotalDisplayRecords" => count($scheduler_data),
"aaData"=>$scheduler_data);
echo json_encode($results);

}

}
} 



function materialplatingin()
{
	
$orderstage=$this->uri->segment(3);
$flowstage=$this->uri->segment(4);
$orderid=$this->uri->segment(5);
$productionflowid=$this->uri->segment(6);
$user_id =$this->session->userdata['logged_in']['user_id'];
$newstage="0";
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
			$newstage=$getneworder1->flow_id;
			
		}else{
			
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
			/** End **/
}else{
			$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000;">Next Flow Not Defined.</span></div>');
		redirect(page_url.'Orderstage/process/'.$flowstage.'/'.$productionflowid);
		}
		
		if($newstage<>0)
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
		
		$newdata=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$newstage,'userstatus'=>'0');
		$this->db->insert('order_stage',$newdata);
		
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

	
}
