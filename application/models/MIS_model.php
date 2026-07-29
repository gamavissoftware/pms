<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mis_model extends CI_Model {

	public function __construct() {
		parent::__construct();

		$this->load->model('Dashboard_model','dashboardmodel');
		$this->load->model('Salescrm_model','salescrm');
		

	}

public function allassignedtask($userid, $enddate, $dfid){
	$this->db->select('a.id')->from('task_department_wise_scheduling a')->join('task_management c','c.task_id=a.taskid')->join('df_release b','a.df_id=b.id','left')->where('a.assigned_user',$userid)->where('a.end_date<',$enddate)->where('a.on_hold',0);
	if($dfid<>'ALL'){
	$this->db->where('a.df_id',$dfid);
	}
	$q = $this->db->get();
	$res = $q->result();
	return count($res);
}

public function totalassignedworkdone($userid,$enddate,$dfid)
{
    $this->db->select('COUNT(DISTINCT a.id) as total');
    $this->db->from('task_department_wise_scheduling a');

    // latest ticket per task
    $this->db->join(
        '(SELECT c1.*
          FROM communication_ticket_system c1
          INNER JOIN (
                SELECT task_record_id, MAX(id) maxid
                FROM communication_ticket_system
                GROUP BY task_record_id
          ) c2
          ON c1.id=c2.maxid
        ) ticket',
        'ticket.task_record_id=a.id',
        'left'
    );

    $this->db->join('task_management c','c.task_id=a.taskid');
    $this->db->join('df_release b','a.df_id=b.id','left');

    if($dfid!='ALL'){
        $this->db->where('a.df_id',$dfid);
    }

    $this->db->where('a.assigned_user',$userid);
    $this->db->where('a.end_date <',date('Y-m-d'));
    $this->db->where('a.on_hold',0);

    /*
        DONE CONDITION:

        1) task_status=1

        OR

        2) task not done BUT latest ticket open
    */

    $this->db->group_start();

        $this->db->where('a.task_status',1);

        $this->db->or_group_start();

            $this->db->where('a.task_status !=',1);
            $this->db->where('ticket.ticket_status',0);

        $this->db->group_end();

    $this->db->group_end();

    $q=$this->db->get();

    return $q->row()->total;
}


public function totalassignedworknotdone($userid,$enddate,$dfid){
	$this->db->select('a.id')->from('task_department_wise_scheduling a')->join('task_management c','c.task_id=a.taskid')->join('df_release b','a.df_id=b.id','left');
	if($dfid<>'ALL'){
		$this->db->where('a.df_id',$dfid);
	}
	$q = $this->db->where('a.assigned_user',$userid)->where('a.task_status',0)->where('a.end_date<=',$enddate)->where('a.on_hold',0)->get();
	$res = $q->result();
	return count($res);
}

	function allnotdonetaskdetail($st,$et,$user,$dfid)
	{
		$current_saturday=$st;
		$current_monday=$et;
		$html='<table class="table table-bordered" style="width:100%;table-layout:fixed;">
		<thead>
		<tr>
		<th>Sr No.</th>
		<th style="width:30%; text-align:center;">Task Name</th>
		<th style="text-align:center;">DM Number</th>
		<th style="text-align:center;">Estimated Completion Date</th>
		<th style="text-align:center;">Delayed in Days</th>
		<th style="text-align:center;">Remarks (If Any)</th>
		</tr>
		</thead>
		<tbody>';
		$startdate = $st;
		$enddate = $et;
		$userid = $user;
		$i=1;
		$this->db->select('
a.id,
b.task_name,
a.end_date,
c.df_no,
a.remarks
');

$this->db->from('task_department_wise_scheduling a');

$this->db->join(
'(SELECT c1.*
    FROM communication_ticket_system c1
    INNER JOIN (
        SELECT task_record_id,
        MAX(id) maxid
        FROM communication_ticket_system
        GROUP BY task_record_id
    ) c2
    ON c1.id=c2.maxid
) ticket',
'ticket.task_record_id=a.id',
'left'
);

$this->db->join(
'task_management b',
'a.taskid=b.task_id',
'left'
);

$this->db->join(
'df_release c',
'a.df_id=c.id',
'left'
);

$this->db->where('a.assigned_user',$userid);

$this->db->where('a.task_status',0);

$this->db->where(
'a.end_date <',
$enddate
);

$this->db->where(
'a.on_hold',
0
);

if($dfid<>'' && $dfid<>'ALL')
{
    $this->db->where(
        'a.id',
        $dfid
    );
}

/*

NOT DONE CONDITION

Show if:

NO ticket

OR

latest ticket NOT OPEN

*/

$this->db->group_start();

    $this->db->where(
        'ticket.id IS NULL',
        NULL,
        FALSE
    );

    $this->db->or_where(
        'ticket.ticket_status !=',
        0
    );

$this->db->group_end();

$q=$this->db
->order_by(
'end_date',
'asc'
)
->get();
				//echo "<pre>"; print_r($q->result()); exit;
				if($q->num_rows()>0)
				{
		foreach ($q->result() as $row) {
			$today = date('Y-m-d');
			$date1 = new DateTime($today);
			$date2 = new DateTime($row->end_date);

			$interval = $date1->diff($date2);
			$totaldays =  $interval->format('%R%a days');


			$html.='<tr>
				<td style="text-align:center;">'.$i.'</td>
				<td style="text-align:center;">'.$row->task_name.'</td>
				<td style="text-align:center;">'.$row->df_no.'</td>
				<td style="text-align:center;">'.date('d-M-Y',strtotime($row->end_date)).'</td>
				<td style="text-align:center; color:red; font-weight:bold;">'.$totaldays.'</td>
				<td style="text-align:center;">
';

if (empty($row->remarks) && $_SESSION['logged_in']['user_id'] == $userid) {

    $html .= '
    <div class="remarks-box" style="display:flex; flex-direction:column; gap:6px; align-items:center;">
        
        <textarea 
            id="remark_'.$row->id.'" 
            class="form-control remark-input" 
            data-id="'.$row->id.'"
            placeholder="Enter remarks..."
            style="width:90%; height:60px; resize:none; font-size:12px;"
        ></textarea>

        <button 
            class="btn btn-success btn-sm save-remark-btn"
            data-id="'.$row->id.'"
            style="padding:3px 10px; font-size:12px;"
        >
            Save
        </button>

    </div>';

} else {

    $html .= '<span id="remark_text_'.$row->id.'">'.(!empty($row->remarks) ? $row->remarks : '-').'</span>';

}

$html .= '</td>
			</tr>';
			$i++;
		}
	}else
	{
			$html.='<tr>
				<td colspan="6" style="text-align:center;">No Data Available</td>
			</tr>';
	}
			$html.='</tbody>
  </table>';



$html.='<br/<br/><h4 class="text-center">Preclosed but not approved<br/>(Please coordinate with your HOD for approval)
	</h3><br/><table class="table table-bordered" style="width:100%;table-layout:fixed;">
		<thead>
		<tr>
		<th>Sr No.</th>
		<th style="width:30%; text-align:center;">Task Name</th>
		<th style="text-align:center;">DM Number</th>
		<th style="text-align:center;">Estimated Completion Date</th>
		<th style="text-align:center;">Delayed in Days</th>
		<th style="text-align:center;">Remarks (If Any)</th>
		</tr>
		</thead>
		<tbody>';
		$startdate = $st;
		$enddate = $et;
		$userid = $user;
		$i=1;
		$this->db->select('b.task_name, a.end_date, c.df_no, a.remarks')->from('task_department_wise_scheduling a')->join('task_management b','a.taskid=b.task_id','left')->join('df_release c','a.df_id=c.id','left')->where('a.assigned_user',$userid)->where('a.task_status',2)->where('a.end_date<',$enddate)->where('a.on_hold',0);
			if($dfid<>'' && $dfid<>'ALL')
				{
					$this->db->where('a.id',$dfid);
				}

				$q = $this->db->order_by('a.end_date','asc')->get();
				if($q->num_rows()>0)
				{
				//echo "<pre>"; print_r($q->result()); exit;
				foreach ($q->result() as $row) {
				$today = date('Y-m-d');
				$date1 = new DateTime($today);
				$date2 = new DateTime($row->end_date);

				$interval = $date1->diff($date2);
				$totaldays =  $interval->format('%R%a days');


			$html.='<tr>
				<td style="text-align:center;">'.$i.'</td>
				<td style="text-align:center;">'.$row->task_name.'</td>
				<td style="text-align:center;">'.$row->df_no.'</td>
				<td style="text-align:center;">'.date('d-M-Y',strtotime($row->end_date)).'</td>
				<td style="text-align:center; color:red; font-weight:bold;">'.$totaldays.'</td>
				<td style="text-align:center;">'.$row->remarks.'</td>
			</tr>';
			$i++;
		}
		}else
		{
			$html.='<tr>
				<td colspan="6" style="text-align:center;">No Data Available</td>
			</tr>';
		}

			$html.='</tbody>
  </table>';
  //echo $html;
		
		return $html;
	}


public function totaldonetaskwithdate($user_id, $startdate, $enddate,$dfid){
	$stdate = $startdate." 00:00:00";
	$enddt = $enddate." 23:59:59";
	$this->db->select('a.id')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id','left')->where('a.assigned_user',$user_id)->where('a.on_hold',0);
	if($dfid<>'' && $dfid<>'ALL'){
		$this->db->where('a.df_id',$dfid);
	}
	$q = $this->db->where('a.task_status',1)->where('a.task_completed_on BETWEEN "'.$stdate. '" and "'.$enddt.'"')->get();
	$res = $q->result();
	return count($res);
}

public function totaldonetaskwithdateontime($user_id, $startdate, $enddate, $dfid){
	$count[] = 0;
	$stdate = $startdate." 00:00:00";
	$enddt = $enddate." 23:59:59";
	$this->db->select('a.id, DATE(a.task_completed_on) as completeddatetime, a.end_date')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id','left')->where('a.assigned_user',$user_id)->where('a.task_status',1)->where('a.on_hold',0);
	if($dfid<>'ALL' && $dfid<>''){
		$this->db->where('a.df_id',$dfid);
	}
	$q = $this->db->where('a.task_completed_on BETWEEN "'.$stdate. '" and "'.$enddt.'"')->get();
	if($q->num_rows()>0){
		foreach($q->result() as $row){
			if(strtotime($row->completeddatetime)<=strtotime($row->end_date)){
				$count[] = 1;
			}else{
				$count[] = 0;
			}

		}
	}


	//echo "<pre>"; print_r($count); exit;
	$cunt = array_sum($count);
	return $cunt;
}

public function alldelayednotdonetaskdetail($user_id, $startdate, $enddate, $dfid){
	$html='<table class="table table-bordered" style="width:100%;table-layout:fixed;">
		<thead>
		<tr>
		<th>Sr No.</th>
		<th style="width:30%; text-align:center;">Task Name</th>
		<th style="text-align:center;">DM Number</th>
		<th style="text-align:center;">Estimated Completion Date</th>
		<th style="text-align:center;">Done Date</th>
		<th style="text-align:center;">Delayed in Days</th>
		<th style="text-align:center;">Remarks (If Any)</th>

		</tr>
		</thead>
		<tbody>';

	$count[] = 0;
	
	$stdate = $startdate." 00:00:00";
	$enddt = $enddate." 23:59:59";
	$this->db->select('a.id,b.task_name, a.end_date, c.df_no, a.remarks, DATE(a.task_completed_on) as completeddatetime')->from('task_department_wise_scheduling a')->join('task_management b','a.taskid=b.task_id','left')->join('df_release c','a.df_id=c.id','left')->where('a.assigned_user',$user_id)->where('a.task_status',1)->where('a.on_hold',0);
	if($dfid<>'ALL'){
		$this->db->where('a.df_id',$dfid);
	}
	$q = $this->db->where('a.task_completed_on BETWEEN "'.$stdate. '" and "'.$enddt.'"')->get();
	if($q->num_rows()>0){
		$i=1;
		foreach($q->result() as $row){
			if(strtotime($row->completeddatetime)>strtotime($row->end_date)){
				
				$today = date('Y-m-d');
				$date1 = new DateTime($row->completeddatetime);
				$date2 = new DateTime($row->end_date);

				$interval = $date1->diff($date2);
				$totaldays =  $interval->format('%R%a days');

					$html.='<tr>
					<td style="text-align:center;">'.$i.'</td>
				<td style="text-align:center;">'.$row->task_name.'</td>
				<td style="text-align:center;">'.$row->df_no.'</td>
				<td style="text-align:center;">'.date('d-M-Y',strtotime($row->end_date)).'</td>
				<td style="text-align:center;">'.date('d-M-Y',strtotime($row->completeddatetime)).'</td>
				<td style="text-align:center; color:red; font-weight:bold;">'.$totaldays.'</td><td>';

				if (empty($row->remarks) && $_SESSION['logged_in']['user_id'] == $user_id) {

    $html .= '
    <div class="remarks-box" style="display:flex; flex-direction:column; gap:6px; align-items:center;">
        
        <textarea 
            id="remark_'.$row->id.'" 
            class="form-control remark-input" 
            data-id="'.$row->id.'"
            placeholder="Enter remarks..."
            style="width:90%; height:60px; resize:none; font-size:12px;"
        ></textarea>

        <button 
            class="btn btn-success btn-sm save-remark-btn"
            data-id="'.$row->id.'"
            style="padding:3px 10px; font-size:12px;"
        >
            Save
        </button>

    </div>';

} else {

    $html .= '<span id="remark_text_'.$row->id.'">'.(!empty($row->remarks) ? $row->remarks : '-').'</span>';

}

			 $html .= '</td></tr>';
			$i++;

			}else{
				$html.='';
			}

		}
	}

	$html.='</tbody>
  </table>';

  echo $html; exit;
		
		return $html;
}

public function avgdelay($user_id, $startdate, $enddate,$dfid){
	
	$avgval= 0;
	$tdays[] = 0;
	$countrecord = 0;
	$stdate = $startdate." 00:00:00";
	$enddt = $enddate." 23:59:59";
	$this->db->select('b.task_name, a.end_date, c.df_no, a.remarks, DATE(a.task_completed_on) as completeddatetime')->from('task_department_wise_scheduling a')->join('task_management b','a.taskid=b.task_id','left')->join('df_release c','a.df_id=c.id','left')->where('a.assigned_user',$user_id)->where('a.task_status',1)->where('a.on_hold',0);
	if($dfid<>'ALL'){
		$this->db->where('df_id',$dfid);
	}
	$q = $this->db->where('a.task_completed_on BETWEEN "'.$stdate. '" and "'.$enddt.'"')->get();
	if($q->num_rows()>0){
		$countrecord = count($q->result());
		foreach($q->result() as $row){
			if(strtotime($row->completeddatetime)>strtotime($row->end_date)){
				
				$today = date('Y-m-d');
				$date1 = new DateTime($row->completeddatetime);
				$date2 = new DateTime($row->end_date);
				$interval = $date1->diff($date2);
				$totaldays =  $interval->format('%R%a');

					$tdays[] = $totaldays;


			}else{
				$tdays[] = 0;
			}

		}
		$finalval = array_sum($tdays);
		$avgval = $finalval/$countrecord;
		return $avgval; exit;
	}


}


function get_percentage($diff,$all)
{
if($all>0)
{
$per=($diff*100)/$all;
}else
{
$per=0;
}

return floor($per);
}


function allassigneTickets($user_id,$dfid)
{

	$rest=$this->db->select('id')->from('communication_ticket_system')->where('user_id',$user_id);
	if($dfid<>'ALL')
	{
		$this->db->where('df_id',$dfid);
	}
	$rest=$this->db->get();

	return $rest->num_rows();

}


function allassigneTicketsDone($user_id,$dfid)
{

	$rest=$this->db->select('id')->from('communication_ticket_system')->where('user_id',$user_id);
	if($dfid<>'ALL')
	{
		$this->db->where('df_id',$dfid);
	}
	//$this->db->where('updated_remarks!=','');
	$this->db->where('ticket_status',1);
	$rest=$this->db->get();

	return $rest->num_rows();

}

function allnotclosedTicket($user,$st,$et,$dfid)
{

		$html='<table class="table table-bordered" style="width:100%;table-layout:fixed;">
		<thead>
		<tr>
		<th style="width:10px;">Sr No.</th>
		<th style="width:100px;text-align:center;">Ticket No.</th>
		<th style="text-align:center;">DF Number</th>
		<th style="text-align:center;width:130px;">Task Name</th>
		<th style="text-align:center;width:250px;">Ticket Particular</th>
		<th style="text-align:center;">Created By</th>
		<th style="text-align:center;">Created On</th>
		<th style="text-align:center;">Pending Since</th>
		</tr>
		</thead>
		<tbody>';

		$this->db->select('a.id, a.user_id, a.help_ticket_no, a.remarks,a.added_by, a.updated_remarks, a.added_on, a.updated_on, b.department, c.task_name, d.title, d.first_name, d.last_name, e.title as usertitle, e.first_name as fname, e.last_name as lname, f.df_no')->from('communication_ticket_system a')->join('departments b','a.department_id=b.department_id')->join('task_management c','a.task_id=c.task_id')->join('system_users d','a.added_by=d.user_id','left')->join('system_users e','a.user_id=e.user_id')->join('df_release f','a.df_id=f.id','left')->where('a.user_id',$user);
		if($dfid<>'ALL')
		{
		$this->db->where('a.df_id',$dfid);
		}

		//$this->db->where('a.updated_remarks','');

		$this->db->where('ticket_status',0);

		$rest=$this->db->get();

		if($rest->num_rows()>0)
		{
		$i=1;
		foreach($rest->result() as $row)
		{
			$days=$this->calculateDayDiff(date('Y-m-d',strtotime($row->added_on)),date('Y-m-d'));
		$html.='<tr>
		<td style="text-align:center;">'.$i.'</td>
		<td style="text-align:center;">'.$row->help_ticket_no.'</td>
		<td style="text-align:center;">'.$row->df_no.'</td>
		<td style="text-align:center;">'.ucwords(strtolower($row->task_name)).'</td>
		<td style="text-align:center; color:red; font-weight:bold;">'.$row->remarks.'</td>
		<td style="text-align:center;">'.ucwords(strtolower($row->title." ".$row->first_name." ".$row->last_name)).'</td>
		<td style="text-align:center;">'.date('d-m-Y',strtotime($row->added_on))."<br> ".date('h:i A',strtotime($row->added_on)).'</td>
		<td style="text-align:center;color:red;font-weight:bold;">'.$days.' days</td>
		</tr>';
		$i++;
		}
		}

		$html.='</tbody></table>';

		echo $html;

}

function calculateDayDiff($d1,$d2)
{

	// Define the two dates
$date1 = new DateTime($d1);
$date2 = new DateTime($d2);

// Calculate the difference between the two dates
$interval = $date1->diff($date2);

// Output the difference in days
return $interval->days;




}


function allCreatedTickets($user_id,$dfid)
{

	$rest=$this->db->select('id')->from('communication_ticket_system')->where('added_by',$user_id);
	if($dfid<>'ALL')
	{
		$this->db->where('df_id',$dfid);
	}
	$this->db->where('updated_remarks!=','');
	$rest=$this->db->get();

	return $rest->num_rows();

}



function allcreatedTicketsDone($user_id,$dfid)
{

	$rest=$this->db->select('id')->from('communication_ticket_system')->where('added_by',$user_id);
	if($dfid<>'ALL')
	{
		$this->db->where('df_id',$dfid);
	}
	$this->db->where('updated_remarks!=','');
	$this->db->where('ticket_status',1);

	$rest=$this->db->get();

	return $rest->num_rows();

}


function alldonenotclosedTicket($user,$st,$et,$dfid)
{
		$html='<table class="table table-bordered" style="width:100%;table-layout:fixed;">
		<thead>
		<tr>
		<th style="width:10px;">Sr No.</th>
		<th style="width:100px;text-align:center;">Ticket No.</th>
		<th style="text-align:center;">DF Number</th>
		<th style="text-align:center;width:130px;">Task Name</th>
		<th style="text-align:center;width:250px;">Ticket Particular</th>
		<th style="text-align:center;">Created By</th>
		<th style="text-align:center;">Created On</th>
		<th style="text-align:center;">Remarks Updated On</th>
		<th style="text-align:center;">Pending Closure Since</th>
		</tr>
		</thead>
		<tbody>';
		$this->db->select('a.id, a.user_id, a.help_ticket_no, a.remarks,a.added_by, a.updated_remarks, a.added_on, a.updated_on, b.department, c.task_name, d.title, d.first_name, d.last_name, e.title as usertitle, e.first_name as fname, e.last_name as lname, f.df_no')->from('communication_ticket_system a')->join('departments b','a.department_id=b.department_id')->join('task_management c','a.task_id=c.task_id')->join('system_users d','a.added_by=d.user_id','left')->join('system_users e','a.user_id=e.user_id')->join('df_release f','a.df_id=f.id','left')->where('a.added_by',$user);
		if($dfid<>'ALL')
		{
		$this->db->where('a.df_id',$dfid);
		}

		$this->db->where('a.updated_remarks!=','');
		$this->db->where('a.ticket_status',0);

		$rest=$this->db->get();

		if($rest->num_rows()>0)
		{
		$i=1;
		foreach($rest->result() as $row)
		{
			$days=$this->calculateDayDiff(date('Y-m-d',strtotime($row->updated_on)),date('Y-m-d'));
		$html.='<tr>
		<td style="text-align:center;">'.$i.'</td>
		<td style="text-align:center;">'.$row->help_ticket_no.'</td>
		<td style="text-align:center;">'.$row->df_no.'</td>
		<td style="text-align:center;">'.$row->task_name.'</td>
		<td style="text-align:center; color:red; font-weight:bold;">'.ucwords(strtolower($row->remarks)).'</td>
		<td style="text-align:center;">'.ucwords(strtolower($row->title." ".$row->first_name." ".$row->last_name)).'</td>
		<td style="text-align:center;">'.date('d-m-Y',strtotime($row->added_on))."<br> ".date('h:i A',strtotime($row->added_on)).'</td>
		<td style="text-align:center;">'.date('d-m-Y',strtotime($row->updated_on))."<br> ".date('h:i A',strtotime($row->updated_on)).'</td>
		<td style="text-align:center;color:red;font-weight:bold;">'.$days.' days</td>
		</tr>';
		$i++;
		}
		}

		$html.='</tbody></table>';

		echo $html;

}

function getTotalFollowupDone($user_id)
{
		
			$chk=" AND b.added_by=".$user_id;
			$conversion_lead_stage = $this->dashboardmodel->getConversionLeadStage();
			$getDeadEndLeadStage = $this->salescrm->getDeadEndLeadStage();
			array_push($getDeadEndLeadStage, $conversion_lead_stage);
			$dead_end_lead_stage = "'" . implode ( "', '", $getDeadEndLeadStage ) . "'";
 		    $resty=$this->db->query("SELECT a.id  FROM progress_remarks a JOIN leads b ON a.lead_id=b.id LEFT JOIN customer_detail c ON c.id=b.company_name WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status NOT IN ($dead_end_lead_stage) $chk  GROUP BY b.id  ORDER BY b.id DESC");
 		        return $resty->num_rows();


}


function getMissedFollowups($user_id)
{
		$conversion_lead_stage = $this->dashboardmodel->getConversionLeadStage();
		$getDeadEndLeadStage = $this->salescrm->getDeadEndLeadStage();
		array_push($getDeadEndLeadStage, $conversion_lead_stage);
		$dead_end_lead_stage = "'" . implode ( "', '", $getDeadEndLeadStage ) . "'";
		$dr="AND a.next_follow_date<'".date('Y-m-d')."'";
		$chk=" AND b.added_by=".$user_id;
	
		$resty=$this->db->query("SELECT a.lead_status,a.next_follow_date,b.added_by as leadmanager,c.company_name as mastercompanyname,b.machine_type,b.distributor,a.visitdate, a.visittime, b.alt_contact, b.designation, b.alt_contact_no,b.postal_address,a.nonqualifiedreason,a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.other_business, b.client_location,a.added_on as lastupdatedOn,a.added_by as updatedby  FROM progress_remarks a JOIN leads b ON a.lead_id=b.id LEFT JOIN customer_detail c ON c.id=b.company_name WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status NOT IN ($dead_end_lead_stage) $chk $dr GROUP BY b.id  ORDER BY b.id DESC");

		return $resty->num_rows();
}

function getallmissedfollowup($user_id)
{


		$html='<table class="table table-bordered" style="width:100%;table-layout:fixed;">
		<thead>
		<tr>
		<th style="width:10px;">Sr No.</th>
		<th style="width:100px;text-align:center;">Opp. No./Date</th>
		<th style="text-align:center;">Company Name</th>
		<th style="text-align:center;width:130px;">Machine Type</th>
		<th style="text-align:center;">Manager</th>
		<th style="text-align:center;">Current Status</th>
		<th style="text-align:center;">Missed Followup Date</th>
		<th style="text-align:center;">Last Remarks</th>
		</tr>
		</thead>
		<tbody>';

		$conversion_lead_stage = $this->dashboardmodel->getConversionLeadStage();
		$getDeadEndLeadStage = $this->salescrm->getDeadEndLeadStage();
		array_push($getDeadEndLeadStage, $conversion_lead_stage);
		$dead_end_lead_stage = "'" . implode ( "', '", $getDeadEndLeadStage ) . "'";
		$dr="AND a.next_follow_date<'".date('Y-m-d')."'";
		$chk=" AND b.added_by=".$user_id;
	


		$resty=$this->db->query("SELECT a.lead_status,a.next_follow_date,b.added_by as leadmanager,c.company_name as mastercompanyname,b.machine_type,b.distributor,a.visitdate, a.visittime, b.alt_contact, b.designation, b.alt_contact_no,b.postal_address,a.nonqualifiedreason,a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.other_business, b.client_location,a.added_on as lastupdatedOn,a.added_by as updatedby  FROM progress_remarks a JOIN leads b ON a.lead_id=b.id LEFT JOIN customer_detail c ON c.id=b.company_name WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status NOT IN ($dead_end_lead_stage) $chk $dr GROUP BY b.id  ORDER BY b.id DESC");

		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
			$show=0;
			$lastfollowupdate=$row->next_follow_date;
			$show=1;
			if($show==1)
			{
			$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
			$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
			if($row->machine_type==1)
			{
				$type="Liquid";
			}else
			{	
				$type="Powder";
			}

			$lead_stage=$row->lead_status;
			$lead_stage=$this->salescrm->getLeadStagenames($lead_stage);

			$username=$this->salescrm->getusername($row->leadmanager);
			$html.='<tr>
			<td style="text-align:center;">'.$i.'</td>
			<td style="text-align:center;">'.$row->unique_id."<br/>".date('d-m-Y',strtotime($row->create_date)).'</td>
			<td style="text-align:center;">'.$row->mastercompanyname.'</td>
			<td style="text-align:center;">'.$type.'</td>
			<td style="text-align:center;">'.$username.'</td>
			<td style="text-align:center; color:red; font-weight:bold;">'.$lead_stage.'</td>
			<td style="text-align:center; color:red; font-weight:bold;">'.date('d-m-Y',strtotime($lastfollowupdate)).'</td>
			<td style="text-align:center; color:red; font-weight:bold;">'.ucwords(strtolower($row->remarks)).'</td>

			</tr>';

		$i++;
	}

	}

}else
{
			$html.='<tr>
			<td style="text-align:center;" colspan="9"></td>
				</tr>';
}

echo $html;



}



function all_task_delegated($st,$et,$user)
	{
		$current_monday=$st;
		$current_saturday=$et;
		$alltaskid=array();
			$taskid=$this->db->select('id')->from('delegation_task')->where('delegate_to',$user)->where('delegated_date<=',$current_saturday)->where('delegated_date!=','0000-00-00')->where('second_date','0000-00-00')->where('third_date','0000-00-00')->get();
			if($taskid->num_rows()>0)
			{
			foreach($taskid->result() as $taskid1)
			{
			$alltaskid[]=$taskid1->id;
			}


			}
			
			
			$taskid=$this->db->select('id')->from('delegation_task')->where('delegate_to',$user)->where('second_date<=',$current_saturday)->where('delegated_date','0000-00-00')->where('second_date!=','0000-00-00')->where('third_date','0000-00-00')->get();
			if($taskid->num_rows()>0)
			{
				foreach($taskid->result() as $taskid1)
				{
					$alltaskid[]=$taskid1->id;
				}
				
				
			}
			
			
			$taskid=$this->db->select('id')->from('delegation_task')->where('delegate_to',$user)->where('third_date<=',$current_saturday)->where('delegated_date','0000-00-00')->where('second_date','0000-00-00')->where('third_date!=','0000-00-00')->get();
			if($taskid->num_rows()>0)
			{
				foreach($taskid->result() as $taskid1)
				{
					$alltaskid[]=$taskid1->id;
				}
				
				
			}
		
		
		return $alltaskid;
	}

	function all_task_delegated_done($st,$et,$user)
	{
		$current_monday=$st;
		$current_saturday=$et;
		$alltaskid=array();
			$taskid=$this->db->select('id')->from('delegation_task')->where('delegate_to',$user)->where('delegated_date<=',$current_saturday)->where('delegated_date!=','0000-00-00')->where('second_date','0000-00-00')->where('third_date','0000-00-00')->where('task_status',1)->get();
			if($taskid->num_rows()>0)
			{
			foreach($taskid->result() as $taskid1)
			{
			$alltaskid[]=$taskid1->id;
			}


			}
			
			
			$taskid=$this->db->select('id')->from('delegation_task')->where('delegate_to',$user)->where('second_date<=',$current_saturday)->where('delegated_date','0000-00-00')->where('second_date!=','0000-00-00')->where('third_date','0000-00-00')->where('task_status',1)->get();
			if($taskid->num_rows()>0)
			{
				foreach($taskid->result() as $taskid1)
				{
					$alltaskid[]=$taskid1->id;
				}
				
				
			}
			
			
			$taskid=$this->db->select('id')->from('delegation_task')->where('delegate_to',$user)->where('third_date<=',$current_saturday)->where('delegated_date','0000-00-00')->where('second_date','0000-00-00')->where('third_date!=','0000-00-00')->where('task_status',1)->get();
			if($taskid->num_rows()>0)
			{
				foreach($taskid->result() as $taskid1)
				{
					$alltaskid[]=$taskid1->id;
				}
				
				
			}
		
		
		return $alltaskid;
	}


	function all_task_delegated_not_delayed($st,$et,$user)
	{
		$current_monday=$st;
		$current_saturday=$et;
		$alltaskid=array();
			$taskid=$this->db->select('id,task_completed_time,delegated_date')->from('delegation_task')->where('delegate_to',$user)->where('delegated_date>=',$current_monday)->where('delegated_date<=',$current_saturday)->where('delegated_date!=','0000-00-00')->where('second_date','0000-00-00')->where('third_date','0000-00-00')->where('task_status',1)->get();
			if($taskid->num_rows()>0)
			{
			foreach($taskid->result() as $taskid1)
			{
				$task_completed_time=date('Y-m-d',strtotime($taskid1->task_completed_time));
				$delegate_date=date('Y-m-d',strtotime($taskid1->delegated_date));
				if(strtotime($task_completed_time)<=strtotime($delegate_date))
				{
				$alltaskid[]=$taskid1->id;
				}
			}


			}
			
			
			$taskid=$this->db->select('id,task_completed_time,second_date')->from('delegation_task')->where('delegate_to',$user)->where('second_date>=',$current_monday)->where('second_date<=',$current_saturday)->where('second_date!=','0000-00-00')->where('delegated_date','0000-00-00')->where('third_date','0000-00-00')->where('task_status',1)->get();
			if($taskid->num_rows()>0)
			{
				foreach($taskid->result() as $taskid1)
				{
					$task_completed_time=date('Y-m-d',strtotime($taskid1->task_completed_time));
					$delegate_date=date('Y-m-d',strtotime($taskid1->second_date));
					if(strtotime($task_completed_time)<=strtotime($delegate_date))
					{
					$alltaskid[]=$taskid1->id;
					}
				}
				
				
			}
			
			
			$taskid=$this->db->select('id,task_completed_time,third_date')->from('delegation_task')->where('delegate_to',$user)->where('third_date>=',$current_monday)->where('third_date<=',$current_saturday)->where('third_date!=','0000-00-00')->where('delegated_date','0000-00-00')->where('second_date','0000-00-00')->where('task_status',1)->get();
			if($taskid->num_rows()>0)
			{
				foreach($taskid->result() as $taskid1)
				{
					$task_completed_time=date('Y-m-d',strtotime($taskid1->task_completed_time));
					$delegate_date=date('Y-m-d',strtotime($taskid1->third_date));
					if(strtotime($task_completed_time)<=strtotime($delegate_date))
					{
					$alltaskid[]=$taskid1->id;
					}
				}
				
				
			}
		
		
		return $alltaskid;
	}

	function all_task_delegated_not_done_details($st,$et,$user)
	{
		$current_saturday=$st;
		$current_monday=$et;
		$html='<table class="table table-bordered" style="width:100%;table-layout:fixed;">
		<thead>
		<tr>
		<th style="width:30%">Task</th>
		<th>Delegated By</th>
		<th>Delegated To</th>
		<th>Due Date</th>
		</tr>
		</thead>
		<tbody>';
		$current_monday=$st;
		$current_saturday=$et;
		$alltaskid=array();
			$taskid=$this->db->select('id,task,delegate_to,yourname,delegated_date')->from('delegation_task')->where('delegate_to',$user)->where('delegated_date<=',$current_saturday)->where('delegated_date!=','0000-00-00')->where('second_date','0000-00-00')->where('third_date','0000-00-00')->where('task_status',0)->get();
			if($taskid->num_rows()>0)
			{
			foreach($taskid->result() as $taskid1)
			{
			$alltaskid[]=$taskid1->id;
			$delegate_to=$this->getusername($taskid1->delegate_to);
			$yourname=$this->getusername($taskid1->yourname);
			$html.='<tr>
					<td>'.$taskid1->task.'</td>
					<td>'.$yourname.'</td>
					<td>'.$delegate_to.'</td>
					<td>'.date('d-M-Y',strtotime($taskid1->delegated_date)).'</td>

					</tr>';
			}


			}
			
			
			$taskid=$this->db->select('id,task,delegate_to,yourname,delegated_date')->from('delegation_task')->where('delegate_to',$user)->where('second_date<=',$current_saturday)->where('second_date!=','0000-00-00')->where('delegated_date','0000-00-00')->where('third_date','0000-00-00')->where('task_status',0)->get();
			if($taskid->num_rows()>0)
			{
				foreach($taskid->result() as $taskid1)
				{
					$alltaskid[]=$taskid1->id;

					$delegate_to=$this->getusername($taskid1->delegate_to);
					$yourname=$this->getusername($taskid1->yourname);
					$html.='<tr>
					<td>'.$taskid1->task.'</td>
					<td>'.$yourname.'</td>
					<td>'.$delegate_to.'</td>
					<td>'.date('d-M-Y',strtotime($taskid1->delegated_date)).'</td>

					</tr>';


				}
				
				
			}
			
			
			$taskid=$this->db->select('id,task,delegate_to,yourname,delegated_date')->from('delegation_task')->where('delegate_to',$user)->where('third_date<=',$current_saturday)->where('third_date!=','0000-00-00')->where('second_date','0000-00-00')->where('delegated_date','0000-00-00')->where('task_status',0)->get();
			if($taskid->num_rows()>0)
			{
				foreach($taskid->result() as $taskid1)
				{
					$alltaskid[]=$taskid1->id;

					$delegate_to=$this->getusername($taskid1->delegate_to);
					$yourname=$this->getusername($taskid1->yourname);
					$html.='<tr>
					<td>'.$taskid1->task.'</td>
					<td>'.$yourname.'</td>
					<td>'.$delegate_to.'</td>
					<td>'.date('d-M-Y',strtotime($taskid1->delegated_date)).'</td>

					</tr>';
				}
				
				
			}
		
			$html.='</tbody>
  </table>';

  echo $html; exit;
		
		return $html;
	}



	function getusername($user_id)
	{
		$resteye=$this->db->select('first_name,last_name')->from('system_users')->where('user_id',$user_id)->get();
		if($resteye->num_rows()>0)
		{
			foreach($resteye->result() as $row);

			$user=$row->first_name." ".$row->last_name;
		}else
		{
			$user='';
		}

		return $user;

	}


	function all_task_delegated_delayed($st,$et,$user)
	{
		$current_monday=$st;
		$current_saturday=$et;
		$html='<table class="table table-bordered" style="width:100%;table-layout:fixed;">
		<thead>
		<tr>
		<th style="width:30%">Task</th>
		<th>Delegated By</th>
		<th>Delegated To</th>
		<th>Due Date</th>
		<th>Completed On</th>
		<th>Delay (in Days)</th>
		</tr>
		</thead>
		<tbody>';
		$current_monday=$st;
		$current_saturday=$et;
		$alltaskid=array();
			$taskid=$this->db->select('id,task,delegate_to,yourname,delegated_date,task_completed_time')->from('delegation_task')->where('delegate_to',$user)->where('delegated_date>=',$current_monday)->where('delegated_date<=',$current_saturday)->where('delegated_date!=','0000-00-00')->where('second_date','0000-00-00')->where('third_date','0000-00-00')->where('task_status',1)->get();
			if($taskid->num_rows()>0)
			{
			foreach($taskid->result() as $taskid1)
			{
			$alltaskid[]=$taskid1->id;
			$task_completed_time=date('Y-m-d',strtotime($taskid1->task_completed_time));
				$delegate_date=date('Y-m-d',strtotime($taskid1->delegated_date));
				if(strtotime($task_completed_time)>strtotime($delegate_date))
				{
			$delegate_to=$this->getusername($taskid1->delegate_to);
			$yourname=$this->getusername($taskid1->yourname);

				$earlier = new DateTime($task_completed_time);
				$later = new DateTime($delegate_date);

$days = $later->diff($earlier)->format("%a"); //3

			$html.='<tr>
					<td>'.$taskid1->task.'</td>
					<td>'.$yourname.'</td>
					<td>'.$delegate_to.'</td>
					<td>'.date('d-M-Y',strtotime($taskid1->delegated_date)).'</td>
					<td>'.date('d-M-Y',strtotime($taskid1->task_completed_time)).'</td>
					<td>'.$days.'</td>

					</tr>';
				}
			}


			}
			
			
			$taskid=$this->db->select('id,task,delegate_to,yourname,delegated_date,task_completed_time')->from('delegation_task')->where('delegate_to',$user)->where('second_date>=',$current_monday)->where('second_date<=',$current_saturday)->where('second_date!=','0000-00-00')->where('delegated_date','0000-00-00')->where('third_date','0000-00-00')->where('task_status',1)->get();
			if($taskid->num_rows()>0)
			{
				foreach($taskid->result() as $taskid1)
				{
					$alltaskid[]=$taskid1->id;
					$task_completed_time=date('Y-m-d',strtotime($taskid1->task_completed_time));
				$delegate_date=date('Y-m-d',strtotime($taskid1->delegated_date));
				if(strtotime($task_completed_time)>strtotime($delegate_date))
				{
					$delegate_to=$this->getusername($taskid1->delegate_to);
					$yourname=$this->getusername($taskid1->yourname);

					$earlier = new DateTime($task_completed_time);
					$later = new DateTime($delegate_date);

					$days = $later->diff($earlier)->format("%a"); //3

					$html.='<tr>
					<td>'.$taskid1->task.'</td>
					<td>'.$yourname.'</td>
					<td>'.$delegate_to.'</td>
					<td>'.date('d-M-Y',strtotime($taskid1->delegated_date)).'</td>
					<td>'.date('d-M-Y',strtotime($taskid1->task_completed_time)).'</td>
					<td>'.$days.'</td>

					</tr>';

				}

				}
				
				
			}
			
			
			$taskid=$this->db->select('id,task,delegate_to,yourname,delegated_date,task_completed_time')->from('delegation_task')->where('delegate_to',$user)->where('third_date>=',$current_monday)->where('third_date<=',$current_saturday)->where('third_date!=','0000-00-00')->where('second_date','0000-00-00')->where('delegated_date','0000-00-00')->where('task_status',1)->get();
			if($taskid->num_rows()>0)
			{
				foreach($taskid->result() as $taskid1)
				{
					$alltaskid[]=$taskid1->id;
					$task_completed_time=date('Y-m-d',strtotime($taskid1->task_completed_time));
				$delegate_date=date('Y-m-d',strtotime($taskid1->delegated_date));
				if(strtotime($task_completed_time)>strtotime($delegate_date))
				{
					$delegate_to=$this->getusername($taskid1->delegate_to);
					$yourname=$this->getusername($taskid1->yourname);

					$earlier = new DateTime($task_completed_time);
					$later = new DateTime($delegate_date);
					$days = $later->diff($earlier)->format("%a"); //3


					$html.='<tr>
					<td>'.$taskid1->task.'</td>
					<td>'.$yourname.'</td>
					<td>'.$delegate_to.'</td>
					<td>'.date('d-M-Y',strtotime($taskid1->delegated_date)).'</td>
					<td>'.date('d-M-Y',strtotime($taskid1->task_completed_time)).'</td>
					<td>'.$days.'</td>

					</tr>';
				}
				}
				
				
			}
		
			$html.='</tbody>
  </table>';

  echo $html; exit;
		
		
	}


	function all_task_delegated_Done_this_week($st,$et,$user)
	{

		$current_monday=$st;
		$current_saturday=$et;
		$alltaskid=array();
			$taskid=$this->db->select('id')->from('delegation_task')->where('delegate_to',$user)->where('delegated_date>=',$current_monday)->where('delegated_date<=',$current_saturday)->where('delegated_date!=','0000-00-00')->where('second_date','0000-00-00')->where('third_date','0000-00-00')->where('task_status',1)->get();
			if($taskid->num_rows()>0)
			{
			foreach($taskid->result() as $taskid1)
			{
			$alltaskid[]=$taskid1->id;
			}


			}
			
			
			$taskid=$this->db->select('id')->from('delegation_task')->where('delegate_to',$user)->where('second_date>=',$current_monday)->where('second_date<=',$current_saturday)->where('delegated_date','0000-00-00')->where('second_date!=','0000-00-00')->where('third_date','0000-00-00')->where('task_status',1)->get();
			if($taskid->num_rows()>0)
			{
				foreach($taskid->result() as $taskid1)
				{
					$alltaskid[]=$taskid1->id;
				}
				
				
			}
			
			
			$taskid=$this->db->select('id')->from('delegation_task')->where('delegate_to',$user)->where('third_date>=',$current_monday)->where('third_date<=',$current_saturday)->where('delegated_date','0000-00-00')->where('second_date','0000-00-00')->where('third_date!=','0000-00-00')->where('task_status',1)->get();
			if($taskid->num_rows()>0)
			{
				foreach($taskid->result() as $taskid1)
				{
					$alltaskid[]=$taskid1->id;
				}
				
				
			}
		
		
		return $alltaskid;
	}



function getTotalMomAssigned($st,$et,$user_id) {
		 $current_date = date('Y-m-d',strtotime($et));

		 $sql = $this->db->select('id')
						 ->from('dfwise_iom_points')
						 ->where('responsible_person', $user_id)
						 ->where('CONCAT_WS(" ", due_date)< ',$current_date)
						 ->get();

			return $sql->num_rows();

	}

	function getTotalMomAssigned_completed($st,$et,$user_id) {
		 $current_date = date('Y-m-d',strtotime($et));

		 $sql = $this->db->select('id')
						 ->from('dfwise_iom_points')
						 ->where('responsible_person', $user_id)
						 ->where('CONCAT_WS(" ", due_date)< ',$current_date)
						 ->where('workstatus',1)
						 ->get();

			return $sql->num_rows();

	}

	function getTotalMomAssigned_Done_This_Week($st,$et,$user_id)
	{

		$start_date = date('Y-m-d',strtotime($st));
		$end_date = date('Y-m-d',strtotime($et));

		 $sql = $this->db->select('id')
						 ->from('dfwise_iom_points')
						 ->where('responsible_person', $user_id)
						 ->where('CONCAT_WS(" ", due_date)>=',$start_date)
						 ->where('CONCAT_WS(" ", due_date)<=',$end_date)
						 ->where('workstatus',1)
						 ->get();

			return $sql->num_rows();

	}

function getTotalMomAssigned_Done_n_Not_Delayed_This_Week($st,$et,$user_id){

	$d=array();
	$d[]=0;

	$start_date = date('Y-m-d',strtotime($st));
		$end_date = date('Y-m-d',strtotime($et));

		 $sql = $this->db->select('id,due_date,update_on')
						 ->from('dfwise_iom_points')
						 ->where('responsible_person', $user_id)
						 ->where('CONCAT_WS(" ", due_date)>=',$start_date)
						 ->where('CONCAT_WS(" ", due_date)<=',$end_date)
						 ->where('workstatus',1)
						 ->get();
						 if($sql->num_rows()>0)
						 {
						 	foreach($sql->result() as $row)
						 	{
						 		$duetime=$row->due_date;
						 		$donetime=$row->update_on;
						 		if($donetime<>'0000-00-00')
						 		{
						 			$donetime=$donetime;
						 		}else
						 		{
						 			$donetime=$row->due_date;
						 		}

								if(strtotime($duetime)>strtotime($donetime))
								{
								$d[]=1;
								}	


						 	}

						 }


						 return array_sum($d);

	}

	

	


		function all_MOM_not_done_details($st,$et,$user_id)
	{
		$html='<table class="table table-bordered" style="width:100%;table-layout:fixed;">
		<thead>
		<tr>
		<th>MOM Agenda</th>
		<th style="width:30%">MOM Points</th>
			<th>Created By</th>
		<th>Created On</th>
		<th>Due Date</th>
		<th>Delay</th>
		</tr>
		</thead>
		<tbody>';
		$current_date = date('Y-m-d',strtotime($et));

		 $sql = $this->db->select('a.id,b.particular,a.particular as task,a.due_date,b.mom_date,b.mom_time,b.added_by')
						 ->from('dfwise_iom_points a')
						 ->join('dfwise_iom b','a.mom_id=b.id')
						 ->where('a.responsible_person', $user_id)
						 ->where('a.due_date<',$current_date)
						 ->where('a.workstatus',0)
						 ->get();

		if($sql->num_rows()>0)
		{
			foreach($sql->result() as $row)
			{

		
			
				$startDate = new DateTime($row->due_date);
				$endDate = new DateTime(date('Y-m-d'));
				$interval = $startDate->diff($endDate);
				$days = $interval->d;
				$yourname=$this->getusername($row->added_by);
				$html.='<tr>
					<td>'.ucwords(strtolower($row->particular)).'</td>
					<td>'.ucwords(strtolower($row->task)).'</td>
					<td>'.ucwords(strtolower($yourname)).'</td>
					<td>'.date('d-M-Y',strtotime($row->mom_date)).'</td>
					<td>'.date('d-M-Y',strtotime($row->due_date)).'</td>
					<td><strong style="color:red;">'.$days.' Days</strong></td>					

					</tr>';

			}

		}

		$html.='</tbody>
  </table>';
		echo $html; 

	}


	function all_MOM_done_n_delayed_details($st,$et,$user_id)
	{
		$html='<table class="table table-bordered" style="width:100%;table-layout:fixed;">
		<thead>
		<tr>
		<th>MOM Agenda</th>
		<th style="width:30%">MOM Points</th>
		<th>Due Date</th>
		<th>Done Date</th>
		<th>Delayed By</th>
		</tr>
		</thead>
		<tbody>';
		$start_date = date('Y-m-d',strtotime($st));
		$end_date = date('Y-m-d',strtotime($et));
		

		 $sql = $this->db->select('a.id,b.particular,a.particular as task,a.due_date,b.mom_date,b.mom_time,b.added_by,a.update_on')
						 ->from('dfwise_iom_points	 a')
						 ->join('dfwise_iom b','a.mom_id=b.id')
						 ->where('a.responsible_person', $user_id)
						 ->where('CONCAT_WS(" ", a.due_date)>= ',$start_date)
						 ->where('CONCAT_WS(" ", a.due_date)<= ',$end_date)
						 ->where('a.workstatus',1)
						 ->order_by('a.due_date','ASC')
						 ->get();

		if($sql->num_rows()>0)
		{
			foreach($sql->result() as $row)
			{


				$duetime=$row->due_date;
				$donetime=date('Y-m-d H:i',strtotime($row->update_on));
				if($donetime<>'0000-00-00')
				{
				$donetime=$donetime;
				}else
				{
				$donetime=$row->due_date;
				}

				//echo $duetime.'<br/>'.$donetime; exit;
				if(strtotime($duetime)<strtotime($donetime))
				{

				$dr='';
					 


				$yourname=$this->getusername($row->added_by);
				//echo date('Y-m-d',strtotime($row->due_date))." ".date('H:i',strtotime($row->due_time))."<br/>".date('Y-m-d H:i',strtotime($row->update_on)); exit;


				
				$date1 = new DateTime(date('Y-m-d',strtotime($row->due_date)));
				$date2 = new DateTime(date('Y-m-d',strtotime($row->update_on)));
				$diff = $date1->diff($date2);
				//echo "<pre>"; print_r($diff); exit;
				$delay=$diff->days." Days";

				

				$html.='<tr>
					<td>'.ucwords(strtolower($row->particular)).'</td>
					<td>'.ucwords(strtolower($row->task)).'</td>
					<td>'.date('d-M-Y',strtotime($row->due_date)).'</td>
					<td>'.date('d-M-Y H:i',strtotime($row->update_on)).'</td>
					<td style="color:red;font-weight:bold;">'.$delay.'</td>

					</tr>';

			}
		}

		}

		$html.='</tbody>
  </table>';
		echo $html; 

	}

}