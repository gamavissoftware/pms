<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HOD_report extends CI_Controller {
	
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
		$config = array();  
		$config['protocol'] = 'smtp';  
		$config['smtp_host'] = 'localhost';  
		$config['smtp_user'] = 'donotreply@packingtest.com';  
		$config['smtp_pass'] = 'Presto@#21';  
		$config['smtp_port'] = 25;  
		$this->email->initialize($config);  
		$this->email->set_newline("\r\n");  
		$this->load->library('email', $config);
		$ip = $_SERVER["REMOTE_ADDR"];
		 /*$query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
            if($query->num_rows()=='0'){
            $this->session->set_flashdata('message','<span class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</span><br/>');
            redirect(page_url.'Dashboard');    
            }*/
		
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
                                            <h4 class="modal-title">Please Specify the reason?</h4>
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

public function travel_conveyance_voucher_hod_list()
	{
		
		$CONVEYANCE_DATA = array();
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
		if($q->num_rows()>0){
		foreach($q->result() as $teamdetail);
		 $teammembers = array();
		$query = $this->db->select('team_id, employee_id')->from('presto_team_members')->where('team_id',$teamdetail->team_id)->get();
        $res = $query->result();
         foreach($res as $teaminfo){
            $teammembers[] = $teaminfo->employee_id;
            
        }
		
		$team = "'" . implode ( "', '", $teammembers ) . "'";
		}else
		{
		    $team='NA';
		}
	
	if($team<>'NA')
		{	
		$this->db->select('a.*, b.first_name, b.last_name')->from('member_conveyance_information a')->join('system_users b','a.employee_id=b.user_id','left')->where('a.status','0')->where('hod_status','0');
		if($q->num_rows()>0){
		$this->db->where_in('a.employee_id',$team, false);
		}
	$query = 	$this->db->order_by('a.id','desc')->get();
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
                                            <h4 class="modal-title">Please Specify the reason?</h4>
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
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($CONVEYANCE_DATA),
	"iTotalDisplayRecords" => count($CONVEYANCE_DATA),
	"aaData"=>$CONVEYANCE_DATA);
	echo json_encode($results);
}

public function visit_schedule_list()
	{
		
		$scheduler_data = array();
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
		if($q->num_rows()>0){
		foreach($q->result() as $teamdetail);
		 $teammembers = array();
		$query = $this->db->select('team_id, employee_id')->from('presto_team_members')->where('team_id',$teamdetail->team_id)->get();
        $res = $query->result();
         foreach($res as $teaminfo){
            $teammembers[] = $teaminfo->employee_id;
            
        }
		
		$team = "'" . implode ( "', '", $teammembers ) . "'";
		}else
		{
		    $team='NA';
		}
	
	if($team<>'NA')
		{
		$this->db->select('a.*, b.first_name, b.last_name, c.first_name as fname, c.last_name as lname')->from('visit_schedule a')->join('system_users b','a.employee_id=b.user_id','left')->join('system_users c','a.added_by=c.user_id','left');
		if($q->num_rows()>0){
		$this->db->where_in('a.employee_id',$team, false);
		}
		$query = $this->db->order_by('a.visit_date','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			$added_time = date('Y-m-d H:i A',strtotime($row->added_on));
			$edit = "<a href='".page_url."Sales/edit_visit_schedule/".$row->id."'><i class='fa fa-pencil'></i></a>";
			
			if($row->status!==''){
			    $updatestatus=$row->status."<br><br>".$row->remarks;
			}else{
			    	$updatestatus = "<a href='".page_url."Sales/update_visit_status/".$row->id."'><span class='btn btn-success btn-xs'>Update Status</span></a>";
			}
		$scheduler_data[] = array('sr_no'=>$i,
			'customer_name'=>strtoupper($row->customer_name),
			'contact_number'=>strtoupper($row->contact_number),
			'company_name'=>strtoupper($row->company_name),
			'address'=>strtoupper($row->address),
			'visit_date'=>$row->visit_date,
			'meeting_time'=>date('h:i A',strtotime($row->meeting_time)),
			'employee'=>strtoupper($row->first_name." ".$row->last_name),
			'added_by'=>strtoupper($row->fname." ".$row->lname),
			'added_time'=>$added_time,
			'edit'=>$edit,
			'updatestatus'=>$updatestatus);
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

public function sales_daily_update_dashboard_list()
	{
		
		$scheduler_data = array();
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
		if($q->num_rows()>0){
		foreach($q->result() as $teamdetail);
		 $teammembers = array();
		$query = $this->db->select('team_id, employee_id')->from('presto_team_members')->where('team_id',$teamdetail->team_id)->get();
        $res = $query->result();
         foreach($res as $teaminfo){
            $teammembers[] = $teaminfo->employee_id;
            
        }
		
		$team = "'" . implode ( "', '", $teammembers ) . "'";
		}else
		{
		    $team='NA';
		}
	
	if($team<>'NA')
		{
		 $this->db->select('a.*, b.first_name, b.last_name')->from('field_sales_daily_update a')->join('system_users b','a.employee_id=b.user_id','left');
		if($q->num_rows()>0){
		$this->db->where_in('a.employee_id',$team, false);
		}
		$query =$this->db->order_by('a.id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			if($row->visiting_card){
				$visitingcard = "<a href='".sale_visit."".$row->visiting_card."' download>Download</a>";
			}else{
				$visitingcard="";
			}
			date_default_timezone_set("Asia/Kolkata");
			$added_time = date('Y-m-d H:i A',strtotime($row->added_on));
			$edit = "<a href='".page_url."Sales/edit_sales_daily_update/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$scheduler_data[] = array('sr_no'=>$i,
			'employee'=>strtoupper($row->first_name." ".$row->last_name),
			'customer_name'=>strtoupper($row->customer_name),
			'contact_number'=>strtoupper($row->contact_number),
			'company_name'=>strtoupper($row->company_name),
			'visit_number'=>strtoupper($row->visit_number),
			'action_taken'=>strtoupper($row->action_taken),
			'stage'=>strtoupper($row->stage),
			'remarks'=>$row->remarks,
			'sale_value'=>strtoupper($row->sale_value),
			'machine_name'=>strtoupper($row->machine_name),
			'next_action_plan'=>strtoupper($row->next_action_plan),
			'date_of_next_plan'=>$row->date_of_next_plan,
			'visitingcard'=>$visitingcard,
			'added_time'=>$added_time,
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

	public function leave_application_hod_dashboard_list()
	{
		$leave_data = array();
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
		
if($team<>'NA')
		{
		
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$this->db->select('a.*,b.first_name, b.last_name, c.department, d.user_role')->from('leave_application a')->join('system_users b','a.employee_id=b.user_id','left')->join('departments c','b.department_id=c.department_id','left')->join('user_role d','b.user_role_id=d.user_role_id','left');
			$this->db->where_in('a.employee_id',$team, false);
		$query = $this->db->order_by('leave_date','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			if($row->leave_for=='1'){
				$leavefor = "Full Day";
			}else if($row->leave_for=='2'){
				$leavefor = "Half Day";
			}else{
			    $time1 = strtotime($row->from_time);
                $time2 = strtotime($row->to_time);
                $difference = round(abs($time2 - $time1) / 3600,2);
			    $leavefor = "Short Leave.<br/>";
			    $leavefor.=$row->from_time."<br/>";
			    $leavefor.=$row->to_time."<br/>";
			    $leavefor.="Total Hour : <strong>".$difference."</strong><br/>";
			    
			   
                
                
			    
			}
			if($row->approval_status=='0'){
				$approve = "<a href='".page_url."Hr/mark_as_approved/".$row->id."/1'><span class='btn btn-success btn-xs'>Mark as Approved</span></a><br/>";
				$reject = "<a href='".page_url."Hr/mark_as_approved/".$row->id."/2'><span class='btn btn-danger btn-xs'>Mark as Rejected</span></a>";
				$hodstatus = $approve." ".$reject;
			}else if($row->approval_status=='1'){
				$hodstatus = "Approved";
			}else{
				$hodstatus = "Rejected";
			}
			
			date_default_timezone_set("Asia/Kolkata");
			$added_time = date('Y-m-d h:i A',strtotime($row->added_on));
			$leave_data[] = array('sr_no'=>$i,
			'employee_name'=>strtoupper($row->first_name." ".$row->last_name),
			'timestamp'=>$added_time,
			'leave_date'=>date('d-m-Y',strtotime($row->leave_date)),
			'from_loc'=>$row->from_loc,
			'to_loc'=>strtoupper($row->to_loc),
			'reason'=>strtoupper($row->reason),
			'total_days'=>strtoupper($row->total_days),
			'department'=>strtoupper($row->department),
			'user_role'=>strtoupper($row->user_role),
			'phone_no'=>strtoupper($row->phone_no),
			'leave_type'=>$row->leave_type,
			'leave_for'=>$leavefor,
			'hodstatus'=>$hodstatus);
			$i++;
		}
		}
		
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($leave_data),
	"iTotalDisplayRecords" => count($leave_data),
	"aaData"=>$leave_data);
	echo json_encode($results);
}
public function task_delegated_to_you()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$scheduler_data = array();
		
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
		
if($team<>'NA')
		{
		$this->db->select('a.id as recordid, a.second_date, a.third_date, a.task_status, a.yourname, a.department_id, a.delegate_to, a.task,a.image, a.delegated_date, a.case_no, a.added_on, b.department,b.department_id,c.user_id, c.title, c.first_name, c.last_name')->from('delegation_task a')->join('departments b','a.department_id=b.department_id','left')->join('system_users c','a.yourname=c.user_id','left');
		
		if($q->num_rows()>0){
		$this->db->where_in('a.delegate_to',$team, false);
		}
		
		$this->db->where('a.task_status','0');
		$query = $this->db->order_by('c.added_on','desc')->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		$i=1;
		foreach($res as $row)
		{
		    $second_date="";
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			if($row->image){
				$attachment = "<a href='".delegationfile.$row->image."' download>DOWNLOAD</a>";
			}else{
			$attachment = "";
			}
			
			if($row->second_date!=='0000-00-00'){
				$seconddate = date('d-M-Y',strtotime($row->second_date));
			}else{
				$second_date="";
			}
			
			if($row->third_date!=='0000-00-00'){
				$thirddate = date('d-M-Y',strtotime($row->third_date));
			}else{
				$thirddate="";
			}
			
			
			/**USER RESPONSE**/
			$userresponse = "";
			$q1 = $this->db->select('id,task_id, user_response, updated_on')->from('user_response_on_delegated_task')->where('task_id',$row->recordid)->limit(1)->order_by('id','desc')->get();
			if($q1->num_rows()>0){
				foreach($q1->result() as $userinput);
				$responsedate = date('d-M-Y', strtotime($userinput->updated_on));
				$rtimes = date('H:i:s', strtotime($userinput->updated_on));
				$responsetime = "<br>". date('g:i A', strtotime($rtimes)); 
				$userresponse=$userinput->user_response."<br>".$responsedate.$responsetime;
				
			}
			/**USER RESPONSE**/
			
			
			
			$scheduler_data[] = array('sr_no'=>$i,
			'timestamp'=>$addeddate.$addedtime,
			'delegated_to'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'caseno'=>strtoupper($row->case_no),
			'task'=>strtoupper($row->task),
			'attachment'=>$attachment,
			'delegated_date'=>date('d-M-Y', strtotime($row->delegated_date)),
			'second_date'=>$second_date,
			'third_date'=>$thirddate,
			'response'=>$userresponse);
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

public function dispatchfortommorow_order_list()
	{
		$scheduler_data = array();
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
		
if($team<>'NA')
		{
		$this->db->select('a.order_id, a.order_type, a.marketing_person,a.po_number, a.company_name,a.closedOn, a.address, a.email, a.mobile_number, a.internal_order_no, a.discount, a.order_value_after_discount, a.advance_amount, a.payment_terms, a.installation_charges, a.order_status, a.added_on,a.packing_type, a.installation_type, b.user_id, b.title, b.first_name, b.last_name, a.installation_amount, a.packing_charges, a.packing_amount, a.freight_type, a.freight_amount,a.remarks')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left')->where('a.order_status','1')->where('a.closeorder','1');
		if($q->num_rows()>0){
		$this->db->where_in('a.added_by',$team, false);
		}
		
		$query = $this->db->order_by('a.order_id','desc')->get();
		$res = $query->result();
		$i=1;
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
			
		if($alljobcard<>0)
		{
		if($completedjobcards==$alljobcard)
		{
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			
			$html = "<table border='1' style='width:500px;'><tr style='background-color:white;'><th style='padding:2px 2px 2px 2px'>INSTRUMENT</th><th style='padding:2px 2px 2px 2px'>JOB CARD NO.</th></tr>";
			$instrumentsss = array();
			
			
			
			foreach($query1->result() as $instruments){
				if($instruments->complete=='1'){
					$backgroundcolor = "background-color:green; color:white !important; font-weight:bold;";
				}else{
					$backgroundcolor="";
				}
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->instruments_name)."</td>";
				//$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->qty)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->job_card_no)."</td>";
				$html.="</tr>";
			}
			$html.="</table>";
			$ins_charges = "";
			/** Get order planned **/
			$restt=$this->db->select('count(id) as totaljobcard')->from('order_instruments')->where('order_id',$row->order_id)->get();
			foreach($restt->result() as $instcount);
			$totjobcard= $instcount->totaljobcard;
			
			
	
			
			if($row->installation_charges=='1'){
				$charges= "<strong>INSTALLATION TYPE </strong>".$row->installation_type."<br> <strong>INSTALLATION CHARGES</strong> - REQUIRED";
				
				$ins_charges= $charges."<br> AMOUNT - ".$row->installation_amount;
			}else{
					$ins_charges = "NOT REQUIRED";
			}
			
			if($row->packing_charges=='1'){
				$packcharges = "PAID BY PARTY";
				$packingcharges= $packcharges."<br> Amount - <strong>".$row->packing_amount."</strong>";
			}else{
					$packingcharges = "INCLUSIVE";
			}
			
			if($row->freight_type=='1'){
				$freigntcharges = "TO PAY BASIS";
				
			}else if($row->freight_type=='2'){
				$freigntcharges = "PAID BY PRESTO";
			}else if($row->freight_type=='3'){
				$frtcharges = "BILLED IN INVOICE";
				$freigntcharges= $frtcharges."<br> Amount - <strong>".$row->freight_amount."</strong>";
			}else{
				$freigntcharges = "OWN PICK-UP";
					
			}
			
			$closedon=date('d-M-Y g:i A',strtotime($row->closedOn));
			$plannedtime = date('d-M-Y g:i A', strtotime($closedon . ' +1 day'));

			
			
$printpackinglabel='<a href="'.page_url.'Reporting/generatepackingslip/'.$row->order_id.'" target="_blank"><span class="btn btn-success btn-sm">Print Packing Label</span></a>';
			
			if($finalpack=='1')
{
			$restyupacku=$this->db->select('addedOn')->from('order_finalpacking_details')->where('orderid',$row->order_id)->get();	
				if($restyupacku->num_rows()>0)
{
				foreach($restyupacku->result() as $restyupacku2);
				$actualtime=date('d-M-Y g:i A',strtotime($restyupacku2->addedOn));
				
}else{  

	$actualtime="";
					}
		$completeorder='Task has been done';		
	
}else{  $actualtime=""; 
	 
	  	
	  $completeorder='<a href="javascript:;" onclick="markstagedone('."'".$row->order_id."'".','."'".strtoupper($row->company_name)."'".','."'".$row->internal_order_no."'".')"><span class="btn btn-warning btn-sm">Mark as Done</span></a>';

}
			
			$scheduler_data[] = array('sr_no'=>$i,
			'closeorder'=>$completeorder,
									  'packinglabel'=>$printpackinglabel,
			'added_on'=>$closedon,
			 'plannedtime'=>$plannedtime,
			 'actualtime'=>$actualtime,
			'order_type'=>strtoupper($row->order_type),
			'marketing_person'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'po_number'=>strtoupper($row->po_number),
			'company_name'=>strtoupper($row->company_name),
			'address'=>strtoupper($row->address),
			'email'=>strtoupper($row->email),
			'mobile_number'=>strtoupper($row->mobile_number),
			'internal_order_no'=>strtoupper($row->internal_order_no),
			'itemname'=>$html,
			'discount'=>strtoupper($row->discount)."%",
			'order_value_after_discount'=>strtoupper($row->order_value_after_discount),
			'advance_amount'=>strtoupper($row->advance_amount),
			'payment_terms'=>strtoupper($row->payment_terms),
			'installation_charges'=>strtoupper($ins_charges),
			'packingcharges'=>"<strong>Packing Type</strong> -".$row->packing_type."<br>".$packingcharges,
			'freigntcharges'=>$freigntcharges,
			'remarks'=>$row->remarks);
			$i++;
		}
		}
	}
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
	
}	
public function visit_form_list()
	{
		$visit_data = array();
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
		
if($team<>'NA')
		{
		
	$this->db->select('a.*, b.first_name, b.last_name, c.first_name as efname, c.last_name as e_lname')->from('engineer_visit a')->join('system_users b','a.user_id=b.user_id','left')->join('system_users c','a.engineer=c.user_id','left');
		
		if($q->num_rows()>0){
		$this->db->where_in('a.engineer',$team, false);
		}
		
		$query = 	$this->db->order_by('a.id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		    $engineer = $row->efname." ".$row->e_lname;
			date_default_timezone_set("Asia/Kolkata");
			if($row->case_status){
				$case = "OPEN";
			}else{
				$case = "CLOSED";
			}
			
			if($row->service_report){
				$attached_report = "<a href='".service_visit_report.$row->service_report."' download>Click Here to Download</a>";
			}else{
				$attached_report="";
			}
			
			
			$added_time = date('Y-m-d h:i A',strtotime($row->added_on));
			
			if($row->picture){
				$picture = "<a href='".service_visit_report.$row->picture."' download>Click Here to Picture</a>";
			}else{
				$picture="";
			}
			$spare_parts = $row->spares_parts;
			if($spare_parts=='1'){
			    $s_part = "Yes";
			}else{
			   $s_part = "No"; 
			}
			
			$casestatus = $row->case_status;
			if($casestatus=='1'){
				$edit="PROGRESS HAS BEEN UPDATED";
			}else{
				$edit = "<a href='".page_url."Sales/engineer_progress/".$row->id."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
			}
			
			$chargable = $row->chargable;
			
		    if($chargable=='0'){
		        $c = "No";
		    }else{
		        $c="Yes";
		    }
		    $payment_to_collect = $row->payment_to_collect;
			
		    if($payment_to_collect=='0'){
		        $p = "No";
		    }else{
		        $p="Yes";
		    }
			
			$warranty = $row->warrenty_status;
			if($warranty=='Warranty Expired'){
			    $wa  = "<span style='color:red; font-weight:bold;'>".strtoupper($warranty)."</span>";
			}else{
			    $wa = strtoupper($warranty);
			}
			
			$visit_data[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'company_name'=>strtoupper($row->company_name),
			'contact_person'=>strtoupper($row->contact_person),
			'contact_number'=>strtoupper($row->contact_number),
			'sale_force_no'=>strtoupper($row->sale_force_no),
			'engineer'=>strtoupper($engineer),
			'warrenty_status'=>$wa,
			'nature_of_complaints'=>strtoupper($row->nature_of_complaints),
			'service_report'=>$attached_report,
			'case_status'=>$case,
			'picture'=>$picture,
			'spare_parts'=>$s_part,
			'part_name'=>$row->part_name,
			'visit_date'=>date('d-m-Y',strtotime($row->visit_date)),
			'chargable'=>$c,
			'payment_to_collect'=>$p,
			'charges'=>$row->charges,
			'bill_number'=>$row->bill_number,
			'next_action'=>$row->next_status,
			'observation_of_engineer'=>$row->observation_of_engineer,
			'edit'=>$edit,
			'addedon'=>$added_time);
			$i++;
		}
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($visit_data),
	"iTotalDisplayRecords" => count($visit_data),
	"aaData"=>$visit_data);
	echo json_encode($results);
}

	public function packed_order_list()
	{
		$scheduler_data = array();
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
		
if($team<>'NA')
		{
		$this->db->select('a.order_id, a.order_type, a.marketing_person,a.po_number, a.company_name, a.address, a.email, a.mobile_number, a.internal_order_no, a.discount, a.order_value_after_discount, a.advance_amount, a.payment_terms, a.installation_charges, a.order_status, a.added_on,a.packing_type, a.installation_type, b.user_id, b.title, b.first_name, b.last_name, a.installation_amount, a.packing_charges, a.packing_amount, a.freight_type, a.freight_amount,a.remarks')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left')->where('a.order_status','1');
		
		    if($q->num_rows()>0){
		$this->db->where_in('a.added_by',$team, false);
		}
		
		$query = $this->db->where('a.closeorder','0')->order_by('a.order_id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			$completedjobcard=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1')->get();
			$completedjobcards=$completedjobcard->num_rows();
			
			$query1 = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name, a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			$alljobcard=$query1->num_rows();
			
		if($completedjobcards==$alljobcard)
		{
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			
			$html = "<table border='1' style='width:500px;'><tr style='background-color:white;'><th style='padding:2px 2px 2px 2px'>INSTRUMENT</th><th style='padding:2px 2px 2px 2px'>JOB CARD NO.</th></tr>";
			$instrumentsss = array();
			
			
			
			foreach($query1->result() as $instruments){
				if($instruments->complete=='1'){
					$backgroundcolor = "background-color:green; color:white !important; font-weight:bold;";
				}else{
					$backgroundcolor="";
				}
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->instruments_name)."</td>";
				//$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->qty)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->job_card_no)."</td>";
				$html.="</tr>";
			}
			$html.="</table>";
			$ins_charges = "";
			/** Get order planned **/
			$restt=$this->db->select('count(id) as totaljobcard')->from('order_instruments')->where('order_id',$row->order_id)->get();
			foreach($restt->result() as $instcount);
			$totjobcard= $instcount->totaljobcard;
			
			
	
			
			if($row->installation_charges=='1'){
				$charges= "<strong>INSTALLATION TYPE </strong>".$row->installation_type."<br> <strong>INSTALLATION CHARGES</strong> - REQUIRED";
				
				$ins_charges= $charges."<br> AMOUNT - ".$row->installation_amount;
			}else{
					$ins_charges = "NOT REQUIRED";
			}
			
			if($row->packing_charges=='1'){
				$packcharges = "PAID BY PARTY";
				$packingcharges= $packcharges."<br> Amount - <strong>".$row->packing_amount."</strong>";
			}else{
					$packingcharges = "INCLUSIVE";
			}
			
			if($row->freight_type=='1'){
				$freigntcharges = "TO PAY BASIS";
				
			}else if($row->freight_type=='2'){
				$freigntcharges = "PAID BY PRESTO";
			}else if($row->freight_type=='3'){
				$frtcharges = "BILLED IN INVOICE";
				$freigntcharges= $frtcharges."<br> Amount - <strong>".$row->freight_amount."</strong>";
			}else{
				$freigntcharges = "OWN PICK-UP";
					
			}
			
			$completeorder='<a href="javascript:;" onclick="markstagedone('."'".$row->order_id."'".','."'".strtoupper($row->company_name)."'".','."'".$row->internal_order_no."'".')"><span class="btn btn-warning">Close Order</span></a>';
			
			$scheduler_data[] = array('sr_no'=>$i,
			'closeorder'=>$completeorder,
			'added_on'=>$addeddate."".$addedtime,
			'order_type'=>strtoupper($row->order_type),
			'marketing_person'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'po_number'=>strtoupper($row->po_number),
			'company_name'=>strtoupper($row->company_name),
			'address'=>strtoupper($row->address),
			'email'=>strtoupper($row->email),
			'mobile_number'=>strtoupper($row->mobile_number),
			'internal_order_no'=>strtoupper($row->internal_order_no),
			'itemname'=>$html,
			'discount'=>strtoupper($row->discount)."%",
			'order_value_after_discount'=>strtoupper($row->order_value_after_discount),
			'advance_amount'=>strtoupper($row->advance_amount),
			'payment_terms'=>strtoupper($row->payment_terms),
			'installation_charges'=>strtoupper($ins_charges),
			'packingcharges'=>"<strong>Packing Type</strong> -".$row->packing_type."<br>".$packingcharges,
			'freigntcharges'=>$freigntcharges,
			'remarks'=>$row->remarks);
			$i++;
		}
		}
	}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}
function sampleallrequests()
{
	$scheduler_data=array();
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
		
if($team<>'NA')
		{
	$this->db->select('a.*')->from('sampletestrequest a');
	if($q->num_rows()>0){
		$this->db->where_in('a.addedBy',$team, false);
		}
	$restyui= $this->db->order_by('a.status','ASC')->order_by('a.addedOn','DESC')->get();
		if($restyui->num_rows()>0)
		{
			
	$i=1;
	foreach($restyui->result() as $instruments)
		{
			$items='';	
				
	$restyui12=$this->db->select('*')->from('sampletobetested')->where('samplereqid',$instruments->id)->order_by('sample','ASC')->get();
		if($restyui12->num_rows()>0)
		{
			foreach($restyui12->result() as $restyui121)
			{
				$items.=strtoupper($restyui121->sample).'<br/>';
			}
	
	
		}
		if($instruments->status==0)
		{
			$sta="<span class='btn btn-warning btn-xs'>PENDING</span>";
			$rep="<a href='".page_url."Sampletesting/addresults/".$instruments->id."'><span class='btn btn-xs'>CREATE</span></a>";
		}else
		{
			$sta="<span class='btn btn-warning'>COMPLETED</span>";
			$rep="<a href='".page_url."Sampletesting/generatesampletestingreport/".$instruments->id."'><span class='btn btn-xs'>VIEW REPORT</span></a>";
		}
	
	$scheduler_data[] = array('sr_no'=>$i,
	'raisedon'=>date('d-m-Y g:i A',strtotime($instruments->addedOn)),
			'sampleid'=>$instruments->sampletestid,
			'companyname'=>$instruments->companyname,
			'items'=>$items,
			'status'=>$sta,
			'report'=>$rep);
			$i++;
	
			}
	
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