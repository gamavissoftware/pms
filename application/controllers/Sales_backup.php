<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales extends CI_Controller {
	
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
		$config = array();  
		$config['protocol'] = 'smtp';  
		$config['smtp_host'] = 'localhost';  
		$config['smtp_user'] = 'donotreply@packingtest.com';  
		$config['smtp_pass'] = 'Presto@#21';  
		$config['smtp_port'] = 25;  
		$this->email->initialize($config);  

		$this->email->set_newline("\r\n");  
		$this->load->library('email', $config);
		
	}
	public function local_conveyance_dashboard(){
		$this->load->view('conveyance_voucher/conveyance_voucher');
	}
	
	public function conveyance_voucher(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('date', 'date', 'required|trim');
	$this->form_validation->set_rules('from', 'from', 'required|trim');
		$this->form_validation->set_rules('proceed_to', 'proceed_to', 'required|trim');
	$this->form_validation->set_rules('mode', 'mode', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		    
			$this->load->view('conveyance_voucher/local_conveyance_form');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		 
		   $mode = $this->input->post('mode');
		
		if($mode=='1'){
			$amount  = $this->input->post('amount');
			$vehicletype=0;
			$startreading=0;
			$endreading=0;
			$rateperkms=0;
			
		}else
		{
		    $amount=$this->input->post('finalamount');
		    $vehicletype=$this->input->post('vtype');
		      $startreading = $this->input->post('start_reading');
		   $endreading = $this->input->post('end_reading');
		   $rateperkms = $this->input->post('rate_per_km');
		}
		   $data=
			array('travel_date'=>date('Y-m-d',strtotime($this->input->post('date'))),
			'from_location'=>strtoupper($this->input->post('from')),
			'proceed_to'=>strtoupper($this->input->post('proceed_to')),
			'mode'=>$mode,
			'start_reading'=>$startreading,
			'end_reading'=>$endreading,
			'rate_per_km'=>$rateperkms,
			'vehicletype'=>$vehicletype,
			'amount'=>$amount,
			'added_by'=>$user_id,
			'status'=>'0',
			'added_on'=>$added_time);
			
			$res = $this->db->insert('conveyance_voucher',$data);
			$last_id = $this->db->insert_id();
			if($res)
			{
			    
			    /** CHECK IF HOD BY PASS IS ALLOWD **/
			    $query1234451 = $this->db->select('a.team_id')->from('presto_team_members a')->where('a.employee_id',$_SESSION['logged_in']['user_id'])->get();
			    if($query1234451->num_rows()>0)
			    {
                $query123445 = $this->db->select('a.team_id')->from('presto_team_members a')->join('prestogroup_teams b','a.team_id=b.team_id')->where('b.by_pass','1')->where('a.employee_id',$_SESSION['logged_in']['user_id'])->get();
                if($query123445->num_rows()>0)
                {
                    
                    $updata=array('hod_status'=>'1','hod_remarks'=>'AUTO BYPASSED HOD');
                    $this->db->where('id',$last_id);
                    $this->db->update('conveyance_voucher',$updata);
                 }
                 
			    }else
			    {
			        
			         $updata=array('hod_status'=>'1','hod_remarks'=>'AUTO BYPASSED HOD');
                    $this->db->where('id',$last_id);
                    $this->db->update('conveyance_voucher',$updata);
			        
			        
			    }
       
			    /** END **/
				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
				redirect(page_url.'Sales/conveyance_voucher');
				}
		  
			}
	}

public function local_conveyance_hod_dashboard(){
		$this->load->view('conveyance_voucher/conveyance_voucher_hod_dashboard');
	}

public function local_conveyance_account_dashboard(){
		$this->load->view('conveyance_voucher/conveyance_voucher_account_dashboard');
	}

public function local_conveyance_hr_dashboard(){
		$this->load->view('conveyance_voucher/conveyance_voucher_hr_dashboard');
	}	
public function conveyance_voucher_list()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		
		$scheduler_data = array();
		$query = $this->db->select('*')->from('conveyance_voucher')->where('added_by',$user_id)->order_by('travel_date','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			
			if($row->mode=='1'){
				$md = "PUBLIC CONVEYANCE";
			}else{
				$md = "OWN";
			}
			$hodremarks = "";
			$status = $row->status;
			if($status=='0'){
				$sta = "UNDER REVIEW";
			}else{
				$sta = "CLEARED";
			}
			
			$hod_status= $row->hod_status;
			$account_status= $row->account_status;
			$hr_status= $row->hr_status;
			if($hod_status=='0'){
				$hodsta = "UNDER REVIEW";
			}else if($hod_status=='1'){
				$hodsta = "APPROVED";
			}else{
				$hodsta = "MARKED AS NOT APPROVED"."<br><br>Reason: ".$row->hod_remarks;;
			}
			if($hod_status=='0'){
				$accountsta="";
				$hrsta="";
			}else{
			$account_status= $row->account_status;
			if($account_status=='0'){
				$accountsta = "UNDER REVIEW";
			}else if($hod_status=='1'){
				$accountsta = "APPROVED";
			}else{
				$accountsta = "MARKED AS NOT APPROVED"."<br><br>Reason: ".$row->account_remarks;;
			}
			}
			if($account_status=='0' && $hod_status=='0'){
				$hrsta="";
				$accountsta="";
				$hodsta="";
			}else{
			$hr_status= $row->hr_status;
			if($hr_status=='0'){
				$hrsta = "UNDER REVIEW";
			}else if($hod_status=='1'){
				$hrsta = "APPROVED";
			}else{
				$hrsta = "MARKED AS NOT APPROVED"."<br><br>Reason: ".$row->hr_remarks;;
			}
			}
			
			/** vehicle Type **/
			
			 $vtypes=$this->db->select('type')->from('conveyance_vehicle_rate')->where('id',$row->vehicletype)->get();
                if($vtypes->num_rows()>0)
                {
                foreach($vtypes->result() as $vtypes1);
                $vtype=$vtypes1->type;
                }else
                {
                    $vtype='';
                }
                
			
			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$scheduler_data[] = array('sr_no'=>$i,
			'travel_date'=>strtoupper($row->travel_date),
			'from_location'=>strtoupper($row->from_location),
			'proceed_to'=>strtoupper($row->proceed_to),
			'mode'=>$md,
			'vehicletype'=>$vtype,
			'start_reading'=>$row->start_reading,
			'end_reading'=>$row->end_reading,
			'rate_per_km'=>$row->rate_per_km,
			'amount'=>$row->amount,
			'deduction'=>$row->deduction,
			'status'=>$sta,
			'hod_status'=>$hodsta,
			'account_status'=>$accountsta,
			'hr_status'=>$hrsta,
			'edit'=>$edit);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}

public function conveyance_voucher_for_hod_list()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
		if($q->num_rows()>0)
		{
		foreach($q->result() as $teamdetail);
		 $teammembers = array();
		$query = $this->db->select('team_id, employee_id')->from('presto_team_members')->where('team_id',$teamdetail->team_id)->get();
        $res = $query->result();
         foreach($res as $teaminfo){
            $teammembers[] = $teaminfo->employee_id;
            
        }
	
		//$team = implode(',',$teammembers);
		$team = "'" . implode ( "', '", $teammembers ) . "'";
		}else
		{
		    $team='NA';
		}
		
		
		
		$scheduler_data = array();
		if($team<>'NA')
		{
		$query = $this->db->select('a.*, b.first_name, b.last_name')->from('conveyance_voucher a')->join('system_users b','a.added_by=b.user_id','left')->where_in('a.added_by',$team,false)->where('a.status','0')->order_by('a.hod_status','asc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			
			if($row->mode=='1'){
				$md = "PUBLIC CONVEYANCE";
			}else{
				$md = "OWN";
			}
			
			$hodremarks = "";
			$status = $row->hod_status;
			if($status=='0'){
				$pendingforapporaval= "<a href='".page_url."/Sales/hod_mark_as_approved/".$row->id."'><span class='btn btn-success btn-xs'>MARK AS APPROVED</span></a>";
				$hodremarks.= "<span class='btn btn-danger btn-xs' data-toggle='modal' data-target='#hod_not_approved".$row->id."'>MARK AS NOT APPROVED</span>";
			}else if($status=='2'){
				$pendingforapporaval = "NOT APPROVED.</br><br><strong style='color:red; font-weight:bold;'>".$row->hod_remarks."</strong>";
				
			}else{
				$pendingforapporaval= "APPROVED";
				
				
			}
			
			
			$hodremarks.= '<div id="hod_not_approved'.$row->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="'.page_url.'Sales/hod_marked_as_not_approved/'.$row->id.'">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">WHY YOU ARE MARKING AS NOT APPROVED? PLEASE SPECIFY</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARK</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<textarea class="form-control" style="width:500px" name="remarks" required></textarea>
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
			
				/** vehicle Type **/
			
			 $vtypes=$this->db->select('type')->from('conveyance_vehicle_rate')->where('id',$row->vehicletype)->get();
                if($vtypes->num_rows()>0)
                {
                foreach($vtypes->result() as $vtypes1);
                $vtype=$vtypes1->type;
                }else
                {
                    $vtype='';
                }
				
				
			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$scheduler_data[] = array('sr_no'=>$i,
			'user_name'=>strtoupper($row->first_name." ".$row->last_name),
			'travel_date'=>strtoupper($row->travel_date),
			'from_location'=>strtoupper($row->from_location),
			'proceed_to'=>strtoupper($row->proceed_to),
			'mode'=>$md,
			'vehicletype'=>$vtype,
			'start_reading'=>$row->start_reading,
			'end_reading'=>$row->end_reading,
			'rate_per_km'=>$row->rate_per_km,
			'amount'=>$row->amount,
			'hodremarks'=>$pendingforapporaval."  ".$hodremarks,
			'edit'=>$edit);
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


public function conveyance_voucher_for_account_list()
	{
	$account_data = array();
		$query = $this->db->select('a.*,b.first_name, b.last_name')->from('conveyance_voucher a')->join('system_users b','a.added_by=b.user_id','left')->where('a.status','0')->where('a.hod_status','1')->order_by('a.account_status','asc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			
			if($row->mode=='1'){
				$md = "PUBLIC CONVEYANCE";
			}else{
				$md = "OWN";
			}
			
			$accountremarks = "";
			$status = $row->account_status;
			if($status=='0'){
				//$pendingforapporaval= "<a href='".page_url."/Sales/account_mark_as_approved/".$row->id."'><span class='btn btn-success btn-xs'>MARK AS APPROVED</span></a>";
				$accountremarks.= "<span class='btn btn-danger btn-xs' data-toggle='modal' data-target='#account_not_approved".$row->id."'>MARK AS APPROVED</span>";
			}else if($status=='2'){
				$pendingforapporaval = "NOT APPROVED<br><br><strong style='color:red'>".$row->account_remarks."</strong>";
			}else{
				$pendingforapporaval = "APPROVED";
				
				
			}
			
			
			$accountremarks.= '<div id="account_not_approved'.$row->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="'.page_url.'Sales/account_marked_as_not_approved/'.$row->id.'">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">MARK AS APPROVED</h4>
                                        </div>
                                        <div class="modal-body">
                                        
                                        <div class="row">
                                         <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">AMOUNT</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<input type="text" class="form-control" style="width:100%" name="amt" required value="'.$row->amount.'" readonly>
												    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">DEDUCTION (if any)</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<input type="text" class="form-control" style="width:100%" name="deduction" required value="0"></textarea>
												    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARK</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<textarea class="form-control" style="width:500px" name="remarks" required></textarea>
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
			
			$status = $row->hod_status;
			if($status=='2'){
				$hodstatus = "NOT APPROVED.</br><br><strong style='color:red; font-weight:bold;'>".$row->hod_remarks."</strong>";
				
			}else{
				$hodstatus= "APPROVED";
				
				
			}
			
				/** vehicle Type **/
			
			 $vtypes=$this->db->select('type')->from('conveyance_vehicle_rate')->where('id',$row->vehicletype)->get();
                if($vtypes->num_rows()>0)
                {
                foreach($vtypes->result() as $vtypes1);
                $vtype=$vtypes1->type;
                }else
                {
                    $vtype='';
                }
				
			
			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$account_data[] = array('sr_no'=>$i,
			'user_name'=>strtoupper($row->first_name." ".$row->last_name),
			'travel_date'=>strtoupper($row->travel_date),
			'from_location'=>strtoupper($row->from_location),
			'proceed_to'=>strtoupper($row->proceed_to),
			'mode'=>$md,
			'vehicletype'=>$vtype,
			'start_reading'=>$row->start_reading,
			'end_reading'=>$row->end_reading,
			'rate_per_km'=>$row->rate_per_km,
			'amount'=>$row->amount,
			'hod_status'=>$hodstatus,
			'accountremarks'=>$accountremarks,
			'edit'=>$edit);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($account_data),
	"iTotalDisplayRecords" => count($account_data),
	"aaData"=>$account_data);
	echo json_encode($results);
}


public function conveyance_voucher_for_hr_list()
	{
		$hr_data = array();
		$query = $this->db->select('a.*,b.first_name, b.last_name')->from('conveyance_voucher a')->join('system_users b','a.added_by=b.user_id','left')->where('a.status','0')->where('a.hod_status','1')->where('a.account_status','1')->order_by('a.account_status','asc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			
			if($row->mode=='1'){
				$md = "PUBLIC CONVEYANCE";
			}else{
				$md = "OWN";
			}
			
			$hrremarks = "";
			$status = $row->hr_status;
			if($status=='0'){
				$pendingforapporaval= "<a href='".page_url."/Sales/hr_mark_as_approved/".$row->id."'><span class='btn btn-success btn-xs'>MARK AS APPROVED</span></a>";
				$hrremarks.= "<span class='btn btn-danger btn-xs' data-toggle='modal' data-target='#hr_not_approved".$row->id."'>MARK AS NOT APPROVED</span>";
			}else if($status=='2'){
				$pendingforapporaval = "NOT APPROVED<br><br><strong style='color:red'>".$row->account_remarks."</strong>";
			}else{
				$pendingforapporaval = "APPROVED";
				
				
			}
			
			
			$hrremarks.= '<div id="hr_not_approved'.$row->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="'.page_url.'Sales/hr_marked_as_not_approved/'.$row->id.'">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">WHY YOU ARE MARKING AS NOT APPROVED? PLEASE SPECIFY</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARK</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<textarea class="form-control" style="width:500px" name="remarks" required></textarea>
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
			
			$status = $row->hod_status;
			if($status=='2'){
				$hodstatus = "NOT APPROVED.</br><br><strong style='color:red; font-weight:bold;'>".$row->hod_remarks."</strong>";
				
			}else{
				$hodstatus= "APPROVED";
			}
			
			$status1 = $row->account_status;
			if($status1=='2'){
				$accountstatus = "NOT APPROVED.</br><br><strong style='color:red; font-weight:bold;'>".$row->account_remarks."</strong>";
				
			}else{
				$accountstatus= "APPROVED";
			}
			
			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$hr_data[] = array('sr_no'=>$i,
			'user_name'=>strtoupper($row->first_name." ".$row->last_name),
			'travel_date'=>strtoupper($row->travel_date),
			'from_location'=>strtoupper($row->from_location),
			'proceed_to'=>strtoupper($row->proceed_to),
			'mode'=>$md,
			'start_reading'=>$row->start_reading,
			'end_reading'=>$row->end_reading,
			'rate_per_km'=>$row->rate_per_km,
			'amount'=>$row->amount,
			'hod_status'=>$hodstatus,
			'accountstatus'=>$accountstatus,
			'hrremarks'=>$pendingforapporaval."  ".$hrremarks,
			'edit'=>$edit);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($hr_data),
	"iTotalDisplayRecords" => count($hr_data),
	"aaData"=>$hr_data);
	echo json_encode($results);
}

	
	public function hod_mark_as_approved()
	{
		 $table = "conveyance_voucher";
			$data = array('hod_status'=>'1');
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/local_conveyance_hod_dashboard');
		}
	
	public function account_mark_as_approved()
	{
		    $table = "conveyance_voucher";
			$data = array('account_status'=>'1');
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/local_conveyance_account_dashboard');
		}
	
	public function hr_mark_as_approved()
	{
		    $table = "conveyance_voucher";
			$data = array('hr_status'=>'1','status'=>'1');
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/local_conveyance_hr_dashboard');
		}
		
	public function hod_marked_as_not_approved()
	{
		    $table = "conveyance_voucher";
			$remark = $this->input->post('remarks');
			$data = array('hod_status'=>'2',
			'hod_remarks'=>$remark);
			
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/local_conveyance_hod_dashboard');
		}
		
	public function account_marked_as_not_approved()
	{
		    $table = "conveyance_voucher";
			$remark = $this->input->post('remarks');
			$dedu=$this->input->post('deduction');
			$data = array('account_status'=>'1',
			'deduction'=>$dedu,
			'account_remarks'=>$remark);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/local_conveyance_account_dashboard');
		}	
	public function hr_marked_as_not_approved()
	{
		    $table = "conveyance_voucher";
			$remark = $this->input->post('remarks');
			$data = array('hr_status'=>'2',
			'hr_remarks'=>$remark);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/local_conveyance_hr_dashboard');
		}	
			
		
		
public function edit_conveyance_voucher(){
	$this->form_validation->set_rules('date', 'date', 'required|trim');
	$this->form_validation->set_rules('from', 'from', 'required|trim');
		$this->form_validation->set_rules('proceed_to', 'proceed_to', 'required|trim');
	$this->form_validation->set_rules('mode', 'mode', 'required|trim');
	
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('conveyance_voucher/edit_conveyance_voucher');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   $startreading = $this->input->post('start_reading');
		   $endreading = $this->input->post('end_reading');
		   $final = $endreading-$startreading;
		   $rateperkms = $this->input->post('rate_per_km');
		   $mode = $this->input->post('mode');
		   if($rateperkms>0){
			   $amount = $final*$rateperkms;
		   }else{
			   $amount = "";
		   }
		if($mode=='1'){
			$amount  = $this->input->post('amount');
		}
		   $data=
			array('travel_date'=>date('Y-m-d',strtotime($this->input->post('date'))),
			'from_location'=>strtoupper($this->input->post('from')),
			'proceed_to'=>strtoupper($this->input->post('proceed_to')),
			'mode'=>$mode,
			'start_reading'=>$startreading,
			'end_reading'=>$endreading,
			'rate_per_km'=>$rateperkms,
			'amount'=>$amount,
			'added_by'=>$user_id,
			'status'=>'0',
			'added_on'=>$added_time);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update('conveyance_voucher',$data);
			
			if($res)
			{
				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully updated.</span></div><br/>');
				redirect(page_url.'Sales/conveyance_voucher');
				}
		   
			
			}
	}
	

public function sale_service_conveyance_voucher(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('purpose_of_trip', 'purpose_of_trip', 'required|trim');
	$this->form_validation->set_rules('start_date', 'start_date', 'required|trim');
		$this->form_validation->set_rules('end_date', 'end_date', 'required|trim');
	$this->form_validation->set_rules('departure_time', 'departure_time', 'required|trim');
	$this->form_validation->set_rules('arrival_time', 'arrival_time', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('conveyance_voucher/tour_conveyance_form');
			}else
		{
		   date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   $start_date = date('Y-m-d',strtotime($this->input->post('start_date')));
		   $end_date = date('Y-m-d',strtotime($this->input->post('end_date')));
		    
		   $photo=$_FILES['attach_bill']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$bills=time().'.'.$cat_image;
				move_uploaded_file($_FILES["attach_bill"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/image_bank/tourbills/' . $bills);
			}else
			{
				$bills="";
				}	
		   
		   $data=
			array('purpose_of_trip'=>$this->input->post('purpose_of_trip'),
			'employee_id'=>$user_id,
			'tour_start_date'=>$start_date,
			'tour_end_date'=>$end_date,
			'tour_bills'=>$bills,
			'departure_time'=>$this->input->post('departure_time'),
			'arrival_time'=>$this->input->post('arrival_time'),
			'total_days'=>$this->input->post('total_days_of_trip'),
			'status'=>'0',
			'added_on'=>$added_time);
			
			$res = $this->db->insert('member_conveyance_information',$data);
			$last_id = $this->db->insert_id();
			if($res)
			{
				
				if(isset($_REQUEST['traveldate'])){	
					$tags1=count($_REQUEST['traveldate']);
					if($tags1>0)
					{
					$traveldate=$_REQUEST['traveldate'];
					$city_name=$_REQUEST['city_name'];
					$company_name=$_REQUEST['company_name'];
					$livingexpense=$_REQUEST['livingexpense'];
					$livingexpense_amount=$_REQUEST['livingexpense_amount'];
					$travel_expense=$_REQUEST['travel_expense'];
					$travel_expense_amount=$_REQUEST['travel_expense_amount'];
					
					
					for($x=0;$x<$tags1;$x++){
					if($traveldate[$x]!='')
						{
						 $data=array('conv_id'=>$last_id,
							'employee_id'=>$user_id,
							'tour_date'=>date('Y-m-d',strtotime($traveldate[$x])),
							'city'=>$city_name[$x],
							'company_name'=>$company_name[$x],
							'living_expense_id'=>$livingexpense[$x],
							'living_exp_amount'=>$livingexpense_amount[$x],
							'travel_exp_id'=>$travel_expense[$x],
							'travel_exp_amount'=>$travel_expense_amount[$x],
							'added_on'=>date('Y-m-d H:i:s'));
							$this->db->insert('member_conveyance_brief',$data);
						   
						}
					}
					}
					}
				
				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
				redirect(page_url.'Sales/sale_service_conveyance_voucher');
				}
		  
			}
	}

public function fetch_expense_options(){
		echo "<option value=''>--Select Expense Options--</option>";
	$expense_type = $this->input->post('expense_type');
		$query =$this->db->select('id, options')->from('conveyance_type_options')->where('conveyance_type_id',$expense_type)->where('status','1')->get();
		foreach($query->result() as $options)
			{
				echo "<option value=".$options->id.">".strtoupper($options->options)."</option>";
				}
	}

public function travel_conveyance_voucher_list()
	{
		$CONVEYANCE_DATA = array();
		$query = $this->db->select('a.*, b.first_name, b.last_name')->from('member_conveyance_information a')->join('system_users b','a.employee_id=b.user_id','left')->order_by('a.id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			
			if($row->purpose_of_trip=='1'){
				$purpose = "SALE";
			}else{
				$purpose = "SERVICE";
			}
			$status = $row->status;
			if($status=='0'){
				$sta = "UNDER REVIEW";
			}else{
				$sta = "CLEARED";
			}
			
			if($row->tour_bills){
				$attached_bills = "<a href='".tourbills.$row->tour_bills."' download>Click Here to Download</a>";
			}else{
				$attached_bills="";
			}
			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row->id."'><i class='fa fa-pencil'></i></a>";
			if($row->hod_status=='1'){
				$hod_status = "APPROVED";
			}else if($row->hod_status=='2'){
				$hod_status="MARKED AS NOT APPROVED<br><strong style='color:red'>".$row->hod_remarks."</strong>";
			}else{
				$hod_status = "PENDING FOR REVIEW";
			}
			
			if($row->account_status=='1'){
				$account_status = "APPROVED";
			}else if($row->account_status=='2'){
				$account_status="MARKED AS NOT APPROVED<br><strong style='color:red'>".$row->account_remarks."</strong>";
			}else{
				$account_status = "PENDING FOR REVIEW";
			}
			
			if($row->hr_status=='1'){
				$hr_status = "APPROVED";
			}else if($row->hr_status=='2'){
				$hr_status="MARKED AS NOT APPROVED<br><strong style='color:red'>".$row->hr_remarks."</strong>";
			}else{
				$hr_status = "PENDING FOR REVIEW";
			}
			
			
			$livingamt= array();
			$travelamt = array();
			$livingamt[] = 0;
			$travelamt[] = 0;
			$html = "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px'>DATE</th><th style='padding:2px 2px 2px 2px'>CITY</th> <th style='padding:2px 2px 2px 2px'>COMPANY NAME</th> <th style='padding:2px 2px 2px 2px'>LIVING EXPENSE</th> <th style='padding:2px 2px 2px 2px'>AMOUNT</th> <th style='padding:2px 2px 2px 2px'>TRAVEL EXPENSE</th> <th style='padding:2px 2px 2px 2px'>AMOUNT</th></tr>";
			$instrumentsss = array();
			$query = $this->db->select('a.conv_id, a.tour_date, a.city, a.company_name, a.living_expense_id, a.living_exp_amount, a.travel_exp_id, a.travel_exp_amount, b.options, c.options as expensetype')->from('member_conveyance_brief a')->join('conveyance_type_options b','a.living_expense_id=b.id','left')->join('conveyance_type_options c','a.travel_exp_id=c.id','left')->where('a.conv_id',$row->id)->get();
			foreach($query->result() as $record){
				
				$livingamt[] = $record->living_exp_amount;
				$travelamt[] = $record->travel_exp_amount;
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".date('d-M-Y',strtotime($record->tour_date))."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->city)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->company_name)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->options)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->living_exp_amount)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->expensetype)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->travel_exp_amount)."</td>";
				$html.="</tr>";
				
			}
			$lv_amount = array_sum($livingamt);
			$trv_amount =  array_sum($travelamt);
			$grandtotal = $lv_amount+$trv_amount;
			$html.="<tr>
				<td colspan='6'></td>
				<td> <strong style='color:red;'>TOTAL INR-".$grandtotal."</strong><td>
			</tr></table>";
			
			$CONVEYANCE_DATA[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'purpose'=>strtoupper($purpose),
			'start_date'=>date('d-M-Y',strtotime($row->tour_start_date)),
			'end_date'=>date('d-M-Y',strtotime($row->tour_end_date)),
			'departure_time'=>date('H:i:a',strtotime($row->departure_time)),
			'arrival_time'=>date('H:i:a',strtotime($row->arrival_time)),
			'total_days'=>$row->total_days,
			'attached_bills'=>$attached_bills,
			'status'=>$sta,
			'traveldata'=>$html,
			'hod_status'=>$hod_status,
			'account_status'=>$account_status,
			'edit'=>$edit);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($CONVEYANCE_DATA),
	"iTotalDisplayRecords" => count($CONVEYANCE_DATA),
	"aaData"=>$CONVEYANCE_DATA);
	echo json_encode($results);
}

function sale_service_dashboard(){
	$this->load->view('conveyance_voucher/sale_service_conveyance_voucher');
}


function sale_service_conveyance_hod_dashboard(){
	$this->load->view('conveyance_voucher/sale_service_conveyance_hod_dashboard');
}
public function travel_conveyance_voucher_hod_list()
	{
		
		$CONVEYANCE_DATA = array();
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
		foreach($q->result() as $teamdetail);
		 $teammembers = array();
		$query = $this->db->select('team_id, employee_id')->from('presto_team_members')->where('team_id',$teamdetail->team_id)->get();
        $res = $query->result();
         foreach($res as $teaminfo){
            $teammembers[] = $teaminfo->employee_id;
            
        }
		//$team = implode(',',$teammembers);
		$team = "'" . implode ( "', '", $teammembers ) . "'";
		$query = $this->db->select('a.*, b.first_name, b.last_name')->from('member_conveyance_information a')->join('system_users b','a.employee_id=b.user_id','left')->where('a.status','0')->where('hod_status','0')->where_in('a.employee_id',$team, false)->order_by('a.id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			
			if($row->purpose_of_trip=='1'){
				$purpose = "SALE";
			}else{
				$purpose = "SERVICE";
			}
			
			if($row->tour_bills){
				$attached_bills = "<a href='".tourbills.$row->tour_bills."' download>Click Here to Download</a>";
			}else{
				$attached_bills="";
			}
			$hodremarks = "";
			$status = $row->hod_status;
			if($status=='0'){
				$pendingforapporaval= "<a href='".page_url."/Sales/sale_service_hod_mark_as_approved/".$row->id."'><span class='btn btn-success btn-xs'>MARK AS APPROVED</span></a><br><br>";
				$hodremarks.= "<span class='btn btn-danger btn-xs' data-toggle='modal' data-target='#hod_not_approved".$row->id."'>MARK AS NOT APPROVED</span><br><br>";
			}else if($status=='2'){
				$pendingforapporaval = "NOT APPROVED.</br><br><strong style='color:red; font-weight:bold;'>".$row->hod_remarks."</strong><br><br>";
				
			}else{
				$pendingforapporaval= "APPROVED";
				
				
			}
			
			
			$hodremarks.= '<div id="hod_not_approved'.$row->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="'.page_url.'Sales/sale_service_hod_marked_as_not_approved/'.$row->id.'">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">WHY YOU ARE MARKING AS NOT APPROVED? PLEASE SPECIFY</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARK</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<textarea class="form-control" style="width:500px" name="remarks" required></textarea>
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
			
			
			$livingamt= array();
			$travelamt = array();
			$livingamt[] = 0;
			$travelamt[] = 0;
			$html = "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center'>DATE</th><th style='padding:2px 2px 2px 2px; text-align:center'>CITY</th> <th style='padding:2px 2px 2px 2px; text-align:center'>COMPANY NAME</th> <th style='padding:2px 2px 2px 2px; text-align:center'>LIVING EXPENSE</th> <th style='padding:2px 2px 2px 2px; text-align:center'>AMOUNT</th> <th style='padding:2px 2px 2px 2px; text-align:center'>TRAVEL EXPENSE</th> <th style='padding:2px 2px 2px 2px; text-align:center'>AMOUNT</th></tr>";
			$instrumentsss = array();
			$query = $this->db->select('a.conv_id, a.tour_date, a.city, a.company_name, a.living_expense_id, a.living_exp_amount, a.travel_exp_id, a.travel_exp_amount, b.options, c.options as expensetype')->from('member_conveyance_brief a')->join('conveyance_type_options b','a.living_expense_id=b.id','left')->join('conveyance_type_options c','a.travel_exp_id=c.id','left')->where('a.conv_id',$row->id)->get();
			foreach($query->result() as $record){
				
				$livingamt[] = $record->living_exp_amount;
				$travelamt[] = $record->travel_exp_amount;
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".date('d-M-Y',strtotime($record->tour_date))."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->city)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->company_name)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->options)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->living_exp_amount)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->expensetype)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->travel_exp_amount)."</td>";
				$html.="</tr>";
				
			}
			$lv_amount = array_sum($livingamt);
			$trv_amount =  array_sum($travelamt);
			$grandtotal = $lv_amount+$trv_amount;
			$html.="<tr>
				<td colspan='6'></td>
				<td> <strong style='color:red;'>TOTAL INR-".$grandtotal."</strong><td>
			</tr></table>";
			
			$CONVEYANCE_DATA[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'purpose'=>strtoupper($purpose),
			'start_date'=>date('d-M-Y',strtotime($row->tour_start_date)),
			'end_date'=>date('d-M-Y',strtotime($row->tour_end_date)),
			'departure_time'=>date('H:i:a',strtotime($row->departure_time)),
			'arrival_time'=>date('H:i:a',strtotime($row->arrival_time)),
			'total_days'=>$row->total_days,
			'attached_bills'=>$attached_bills,
			'hod_status'=>$pendingforapporaval." ".$hodremarks,
			'traveldata'=>$html);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($CONVEYANCE_DATA),
	"iTotalDisplayRecords" => count($CONVEYANCE_DATA),
	"aaData"=>$CONVEYANCE_DATA);
	echo json_encode($results);
}
public function sale_service_hod_mark_as_approved()
	{
		 $table = "member_conveyance_information";
			$data = array('hod_status'=>'1');
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/sale_service_conveyance_hod_dashboard');
		}

public function sale_service_hod_marked_as_not_approved()
	{
		    $table = "member_conveyance_information";
			$remark = $this->input->post('remarks');
			$data = array('hod_status'=>'2',
			'hod_remarks'=>$remark);
			
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/sale_service_conveyance_hod_dashboard');
		}
function sale_service_conveyance_account_dashboard(){
	$this->load->view('conveyance_voucher/sale_service_conveyance_account_dashboard');
}
public function travel_conveyance_voucher_account_list()
	{
		$CONVEYANCE_DATA = array();
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$query = $this->db->select('a.*, b.first_name, b.last_name')->from('member_conveyance_information a')->join('system_users b','a.employee_id=b.user_id','left')->where('a.status','0')->where('a.hod_status','1')->order_by('a.id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			
			if($row->purpose_of_trip=='1'){
				$purpose = "SALE";
			}else{
				$purpose = "SERVICE";
			}
			
			if($row->tour_bills){
				$attached_bills = "<a href='".tourbills.$row->tour_bills."' download>Click Here to Download</a>";
			}else{
				$attached_bills="";
			}
			$accountremarks = "";
			$status = $row->account_status;
			if($status=='0'){
				$pendingforapporaval= "<a href='".page_url."/Sales/sale_service_account_mark_as_approved/".$row->id."'><span class='btn btn-success btn-xs'>MARK AS APPROVED</span></a><br><br>";
				$accountremarks.= "<span class='btn btn-danger btn-xs' data-toggle='modal' data-target='#hod_not_approved".$row->id."'>MARK AS NOT APPROVED</span><br><br>";
			}else if($status=='2'){
				$pendingforapporaval = "NOT APPROVED.</br><br><strong style='color:red; font-weight:bold;'>".$row->account_remarks."</strong><br><br>";
				
			}else{
				$pendingforapporaval= "APPROVED";
				
				
			}
			
			
			$accountremarks.= '<div id="hod_not_approved'.$row->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="'.page_url.'Sales/sale_service_account_marked_as_not_approved/'.$row->id.'">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">WHY YOU ARE MARKING AS NOT APPROVED? PLEASE SPECIFY</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARK</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<textarea class="form-control" style="width:500px" name="remarks" required></textarea>
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
			
			
			$livingamt= array();
			$travelamt = array();
			$livingamt[] = 0;
			$travelamt[] = 0;
			$html = "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center'>DATE</th><th style='padding:2px 2px 2px 2px; text-align:center'>CITY</th> <th style='padding:2px 2px 2px 2px; text-align:center'>COMPANY NAME</th> <th style='padding:2px 2px 2px 2px; text-align:center'>LIVING EXPENSE</th> <th style='padding:2px 2px 2px 2px; text-align:center'>AMOUNT</th> <th style='padding:2px 2px 2px 2px; text-align:center'>TRAVEL EXPENSE</th> <th style='padding:2px 2px 2px 2px; text-align:center'>AMOUNT</th></tr>";
			$instrumentsss = array();
			$query = $this->db->select('a.conv_id, a.tour_date, a.city, a.company_name, a.living_expense_id, a.living_exp_amount, a.travel_exp_id, a.travel_exp_amount, b.options, c.options as expensetype')->from('member_conveyance_brief a')->join('conveyance_type_options b','a.living_expense_id=b.id','left')->join('conveyance_type_options c','a.travel_exp_id=c.id','left')->where('a.conv_id',$row->id)->get();
			foreach($query->result() as $record){
				
				$livingamt[] = $record->living_exp_amount;
				$travelamt[] = $record->travel_exp_amount;
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".date('d-M-Y',strtotime($record->tour_date))."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->city)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->company_name)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->options)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->living_exp_amount)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->expensetype)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->travel_exp_amount)."</td>";
				$html.="</tr>";
				
			}
			$lv_amount = array_sum($livingamt);
			$trv_amount =  array_sum($travelamt);
			$grandtotal = $lv_amount+$trv_amount;
			$html.="<tr>
				<td colspan='6'></td>
				<td> <strong style='color:red;'>TOTAL INR-".$grandtotal."</strong><td>
			</tr></table>";
			
			$CONVEYANCE_DATA[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'purpose'=>strtoupper($purpose),
			'start_date'=>date('d-M-Y',strtotime($row->tour_start_date)),
			'end_date'=>date('d-M-Y',strtotime($row->tour_end_date)),
			'departure_time'=>date('H:i:a',strtotime($row->departure_time)),
			'arrival_time'=>date('H:i:a',strtotime($row->arrival_time)),
			'total_days'=>$row->total_days,
			'attached_bills'=>$attached_bills,
			'hod_status'=>'APPROVED',
			'account_status'=>$pendingforapporaval." ".$accountremarks,
			'traveldata'=>$html);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($CONVEYANCE_DATA),
	"iTotalDisplayRecords" => count($CONVEYANCE_DATA),
	"aaData"=>$CONVEYANCE_DATA);
	echo json_encode($results);
}

public function sale_service_account_mark_as_approved()
	{
		    $table = "member_conveyance_information";
			$data = array('account_status'=>'1');
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/sale_service_conveyance_account_dashboard');
		}
public function sale_service_account_marked_as_not_approved()
	{
		    $table = "member_conveyance_information";
			$remark = $this->input->post('remarks');
			$data = array('account_status'=>'2',
			'account_remarks'=>$remark);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/sale_service_conveyance_account_dashboard');
		}
public function travel_conveyance_voucher_hr_list()
	{
		$CONVEYANCE_DATA = array();
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$query = $this->db->select('a.*, b.first_name, b.last_name')->from('member_conveyance_information a')->join('system_users b','a.employee_id=b.user_id','left')->where('a.status','0')->where('a.account_status','1')->order_by('a.id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			
			if($row->purpose_of_trip=='1'){
				$purpose = "SALE";
			}else{
				$purpose = "SERVICE";
			}
			
			if($row->tour_bills){
				$attached_bills = "<a href='".tourbills.$row->tour_bills."' download>Click Here to Download</a>";
			}else{
				$attached_bills="";
			}
			
			$hrremarks = "";
			$status = $row->hr_status;
			if($status=='0'){
				$pendingforapporaval= "<a href='".page_url."/Sales/sale_service_hr_mark_as_approved/".$row->id."'><span class='btn btn-success btn-xs'>MARK AS APPROVED</span></a><br><br>";
				$hrremarks.= "<span class='btn btn-danger btn-xs' data-toggle='modal' data-target='#hod_not_approved".$row->id."'>MARK AS NOT APPROVED</span><br><br>";
			}else if($status=='2'){
				$pendingforapporaval = "NOT APPROVED.</br><br><strong style='color:red; font-weight:bold;'>".$row->hr_remarks."</strong><br><br>";
				
			}else{
				$pendingforapporaval= "APPROVED";
				
				
			}
			
			
			$hrremarks.= '<div id="hod_not_approved'.$row->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="'.page_url.'Sales/sale_service_hr_marked_as_not_approved/'.$row->id.'">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">WHY YOU ARE MARKING AS NOT APPROVED? PLEASE SPECIFY</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARK</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<textarea class="form-control" style="width:500px" name="remarks" required></textarea>
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
			
			
			$livingamt= array();
			$travelamt = array();
			$livingamt[] = 0;
			$travelamt[] = 0;
			$html = "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center'>DATE</th><th style='padding:2px 2px 2px 2px; text-align:center'>CITY</th> <th style='padding:2px 2px 2px 2px; text-align:center'>COMPANY NAME</th> <th style='padding:2px 2px 2px 2px; text-align:center'>LIVING EXPENSE</th> <th style='padding:2px 2px 2px 2px; text-align:center'>AMOUNT</th> <th style='padding:2px 2px 2px 2px; text-align:center'>TRAVEL EXPENSE</th> <th style='padding:2px 2px 2px 2px; text-align:center'>AMOUNT</th></tr>";
			$instrumentsss = array();
			$query = $this->db->select('a.conv_id, a.tour_date, a.city, a.company_name, a.living_expense_id, a.living_exp_amount, a.travel_exp_id, a.travel_exp_amount, b.options, c.options as expensetype')->from('member_conveyance_brief a')->join('conveyance_type_options b','a.living_expense_id=b.id','left')->join('conveyance_type_options c','a.travel_exp_id=c.id','left')->where('a.conv_id',$row->id)->get();
			foreach($query->result() as $record){
				
				$livingamt[] = $record->living_exp_amount;
				$travelamt[] = $record->travel_exp_amount;
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".date('d-M-Y',strtotime($record->tour_date))."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->city)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->company_name)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->options)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->living_exp_amount)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->expensetype)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->travel_exp_amount)."</td>";
				$html.="</tr>";
				
			}
			$lv_amount = array_sum($livingamt);
			$trv_amount =  array_sum($travelamt);
			$grandtotal = $lv_amount+$trv_amount;
			$html.="<tr>
				<td colspan='6'></td>
				<td> <strong style='color:red;'>TOTAL INR-".$grandtotal."</strong><td>
			</tr></table>";
			
			$CONVEYANCE_DATA[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'purpose'=>strtoupper($purpose),
			'start_date'=>date('d-M-Y',strtotime($row->tour_start_date)),
			'end_date'=>date('d-M-Y',strtotime($row->tour_end_date)),
			'departure_time'=>date('H:i:a',strtotime($row->departure_time)),
			'arrival_time'=>date('H:i:a',strtotime($row->arrival_time)),
			'total_days'=>$row->total_days,
			'attached_bills'=>$attached_bills,
			'hod_status'=>'APPROVED',
			'account_status'=>'APPROVED',
			'hr_status'=>$pendingforapporaval." ".$hrremarks,
			'traveldata'=>$html);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($CONVEYANCE_DATA),
	"iTotalDisplayRecords" => count($CONVEYANCE_DATA),
	"aaData"=>$CONVEYANCE_DATA);
	echo json_encode($results);
}
function sale_service_conveyance_hr_dashboard(){
	$this->load->view('conveyance_voucher/sale_service_conveyance_hr_dashboard');
}
public function sale_service_hr_mark_as_approved()
	{
		    $table = "member_conveyance_information";
			$data = array('hr_status'=>'1',
			'status'=>'1');
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/sale_service_conveyance_hr_dashboard');
		}
public function sale_service_hr_marked_as_not_approved()
	{
		    $table = "member_conveyance_information";
			$remark = $this->input->post('remarks');
			$data = array('hr_status'=>'2',
			'hr_remarks'=>$remark);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/sale_service_conveyance_hr_dashboard');
		}
		
		function getperkmrate()
		{
		    $id=$this->uri->segment(3);
		   
		    $rate='';
                $vtypes=$this->db->select('rate')->from('conveyance_vehicle_rate')->where('id',$id)->get();
                if($vtypes->num_rows()>0)
                {
                foreach($vtypes->result() as $vtypes1);
                $rate=floatval($vtypes1->rate);
                }
                
                echo $rate;
		    
		    
		    
		}
}