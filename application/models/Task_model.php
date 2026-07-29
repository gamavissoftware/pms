<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Task_model extends CI_Model {

	public function __construct()
	{
		
		parent::__construct();

	}

	public function checkholiday(){
		$holiday = array();
		$q1 = $this->db->select('holiday_date')->from('prestogroup_holidays')->get();
		foreach ($q1->result() as $holidaydata) {
			$holiday[] = $holidaydata->holiday_date;
		}

		return $holiday;


	}

	public function holidayscountdays($startdate, $enddate){
		$q1 = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date>=',$startdate)->where('holiday_date<=',$enddate)->get();
		return $q1->num_rows();
	}

	public function adjustDateRangeForHolidays($start_date, $end_date) {
        // Fetch holidays within the date range
        $this->db->where('holiday_date >=', $start_date);
        $this->db->where('holiday_date <=', $end_date);
        $query = $this->db->get('prestogroup_holidays');
        $holidays = $query->result();

        // Adjust end date if it falls on a holiday
        foreach ($holidays as $holiday) {
            if ($holiday->holiday_date == $end_date) {
                $end_date = date('Y-m-d', strtotime('+1 day', strtotime($end_date)));
                return $this->adjustDateRangeForHolidays($start_date, $end_date); // Recursively check the adjusted end date
            }
        }

        return array('start_date' => $start_date, 'end_date' => $end_date);
    }

		public function adjustDateRangeForHolidaysssssssss($start_date, $end_date) {
        // Fetch holidays within the date range
        $this->db->where('holiday_date >=', $start_date);
        $this->db->where('holiday_date <=', $end_date);
        $query = $this->db->get('prestogroup_holidays');
        $holidays = $query->result();

        // Check if any date within the range falls within a holiday slab
        $date = $start_date;
        while ($date <= $end_date) {
            foreach ($holidays as $holiday) {
                if ($holiday->holiday_date == $date) {
                    // Increase dates to avoid holiday slabs
                    $end_date = date('Y-m-d', strtotime('+1 day', strtotime($end_date)));
                    return $this->adjustDateRangeForHolidays($start_date, $end_date); // Recursively check the adjusted end date
                }
            }
            $date = date('Y-m-d', strtotime('+1 day', strtotime($date)));
        }

        // Check if the adjusted end date falls on a holiday
        $this->db->where('holiday_date', $end_date);
        $query = $this->db->get('prestogroup_holidays');
        $holidays = $query->result();
        
        // If adjusted end date falls on a holiday, increase it
        if (!empty($holidays)) {
            $end_date = date('Y-m-d', strtotime('+1 day', strtotime($end_date)));
            return $this->adjustDateRangeForHolidays($start_date, $end_date); // Recursively check the adjusted end date
        }

        return array('start_date' => $start_date, 'end_date' => $end_date);
    }


function isHoliday($date, $holidays) {
   
    
    return in_array($date, $holidays);
}

function triggernotificationondfrelease($dfn,$dfid){
		

		$q = $this->db->select('task_message, department_id, taskid')->from('task_related_messages')->where('taskid',2)->get();
		if($q->num_rows()>0){

			foreach($q->result() as $checkmsg){

		/*Trigger Notification of DF Release*/

		$this->db->select('department_id')->from('task_management')->where('status',1);
		if($checkmsg->department_id==0){

		}else{
			$this->db->where('department_id',$checkmsg->department_id);
		}
		$q = $this->db->group_by('department_id')->get();
		foreach($q->result() as $dfnotification){

			$dfmessage=  $checkmsg->task_message;
				
				$q = $this->db->select('a.department_id, b.first_name, b.last_name, b.user_id, b.contact_number, b.email, c.department')->from('prestogroup_teams a')->join('system_users b','a.team_leader=b.user_id','left')->join('departments c','a.department_id=c.department_id','left')->where('a.department_id',$dfnotification->department_id)->get();

				if($q->num_rows()>0){
					foreach($q->result() as $sendmessage);
					$name = ucfirst($sendmessage->first_name)." ".ucfirst($sendmessage->last_name);
					$mobileno = $sendmessage->contact_number;
					$contactnumber = $sendmessage->contact_number;
					//$emailid = $row2->email;
					//$contactno = "9718991797";
					$emailid = $sendmessage->email;
					$departmentname = $sendmessage->department;
					$find = array('{df_number}','{department_name}', '{department_hod}');
							$replace = array(ucwords(strtolower($dfn)), ucwords(strtolower($departmentname)), ucwords(strtolower($name)));
					$message_body = str_replace($find, $replace, $dfmessage);

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					//'receiverMobileNo' => '91'.$contactnumber,
					'receiverMobileNo' => '91'.$contactnumber,
					'username' => whatsappuser1,
					'password' => whatsapppass1,
					'message'=>strip_tags($message_body));

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
						//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					
					/* end */

					$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
				   <tr>
					<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://shubhampack.com/wp-content/uploads/2021/05/Logo.png" width="200px;" alt="Shubham Flexible Packaging Machines Pvt. Ltd. " /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strongExciting News, Team Leaders! </strong><br><br></td>
					  </tr> 
					 
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;">'.$message_body.' 

						</td>
					  </tr>
					  
				
					  <tr>
						<td>
					   </td>
					  </tr>
					</table>
					</td>
				  </tr>
				</table>';
				$subjectname = "Exciting News, Team Leaders!";
					$this->email->set_mailtype("html");
					//$this->email->to('mangleshup@gmail.com');
					$this->email->to($emailid);
					//$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('taskmanagement@shubhampack.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();

    				$data = array('department_id'=>$sendmessage->department_id,
    					'user_id'=>$sendmessage->user_id,
    					'df_id'=>$dfid,
    					'message'=>$dfmessage,
    					'added_on'=>date('Y-m-d H:i:s'),
    					'status'=>0);

    				$this->db->insert('task_intimation_alert',$data);


				}



		}
		
	}
		/*Trigegr Notification of DF Release*/
	}
}

public function marketingtaskautoassign($dfid,$userid,$departid){
	$q = $this->db->select('id')->from('task_department_wise_scheduling')->where('df_id',$dfid)->where('department_id',$departid)->get();
	foreach($q->result() as $row){
		$data = array('assigned_user'=>$userid,
			'assigned_by'=>$userid,
			'assigned_on'=>date('Y-m-d H:i:s'));
		$this->db->where('id',$row->id);
		$this->db->update('task_department_wise_scheduling',$data);
	}

}

public function getdepartment($taskid){
	$q = $this->db->select('department_id')->from('task_management')->where('task_id',$taskid)->get();
			foreach($q->result() as $row2);
			$departmentid = $row2->department_id;
			return $departmentid;
}

public function addnotification($dfid, $taskremark, $userid, $departmentid, $assigneduser,$lastinsertid){
	$data2 = array(
			'df_id'=>$dfid,
			'message'=>$taskremark,
			'added_by'=>$userid,
			'added_on'=>date('Y-m-d H:i:s'),
			'department_id'=>$departmentid,
			'user_id'=>$assigneduser);
			$this->db->insert('task_intimation_alert',$data2);

			$this->previoussteptasknotification($lastinsertid);

}

function compareDateThisWeek($date)
{
	$fd = strtotime('monday this week'); // First date
	$ld = strtotime('sunday this week'); // last date
	$birthday_date = strtotime($date); // Birthday date
	if (($birthday_date > $fd) && ($birthday_date < $ld)) {
	return true;
	} else {
	return false;
	}

}

function getAssignedDepartment($user_id)
{
	$departmentid=array();
	$q = $this->db->select('department_id')->from('prestogroup_teams')->where('team_leader',$user_id )->get();
		if($q->num_rows()>0){
			foreach($q->result() as $rowss){
				$departmentid[] = $rowss->department_id;
			}
		}

		return array_values(array_unique($departmentid));
}

function getLeaderDepartmentIdsByPermission($user_id, $permission_field = '')
{
	$table_name = 'prestogroup_teams';
	$department_ids = array();

	if (!$this->db->table_exists($table_name)) {
		return $department_ids;
	}

	$this->db->select('department_id')
		->from($table_name)
		->where('team_leader', (int) $user_id)
		->where('status', 1);

	if ($permission_field !== '') {
		if (!$this->db->field_exists($permission_field, $table_name)) {
			return $department_ids;
		}
		$this->db->where($permission_field, 1);
	}

	$q = $this->db->get();
	if ($q->num_rows() > 0) {
		foreach ($q->result() as $row) {
			$department_ids[] = (int) $row->department_id;
		}
	}

	return array_values(array_unique($department_ids));
}

function getLeaderViewDepartmentIds($user_id)
{
	return $this->getLeaderDepartmentIdsByPermission($user_id, 'show_all_team_tasks');
}

function getLeaderAssignmentDepartmentIds($user_id)
{
	return $this->getLeaderDepartmentIdsByPermission($user_id, 'allow_task_assignment');
}

function canLeaderViewAllTeamTasks($user_id, $department_id = 0)
{
	if (!$this->db->field_exists('show_all_team_tasks', 'prestogroup_teams')) {
		return false;
	}

	$this->db->select('team_id')
		->from('prestogroup_teams')
		->where('team_leader', (int) $user_id)
		->where('show_all_team_tasks', 1)
		->where('status', 1);

	if ((int) $department_id > 0) {
		$this->db->where('department_id', (int) $department_id);
	}

	return $this->db->limit(1)->get()->num_rows() > 0;
}

function canLeaderAssignTasks($user_id, $department_id = 0)
{
	if (!$this->db->field_exists('allow_task_assignment', 'prestogroup_teams')) {
		return false;
	}

	$this->db->select('team_id')
		->from('prestogroup_teams')
		->where('team_leader', (int) $user_id)
		->where('allow_task_assignment', 1)
		->where('status', 1);

	if ((int) $department_id > 0) {
		$this->db->where('department_id', (int) $department_id);
	}

	return $this->db->limit(1)->get()->num_rows() > 0;
}

function getDays($date1,$date2,$flag)
{
	//echo $date1."<br>".$date2; exit;
	if($flag==1)
	{

	// $date_first = new DateTime($date2);
	// $date_second = new DateTime($date1);
	// $interval = $date_first->diff($date_second);
	// $interval = $interval->d;


	$earlier = new DateTime($date1);
$later = new DateTime($date2);

$interval = $later->diff($earlier)->format("%a"); //3


	}else
	{
		$earlier = new DateTime($date1);
$later = new DateTime($date2);
$interval = $later->diff($earlier)->format("%a"); //3


	// $date_first = new DateTime($date1);
	// $date_second = new DateTime($date2);
	// $interval = $date_first->diff($date_second);
	}

	return $interval;
	

}

function get_all_df_details()
{
	$ret=$this->db->select('id,df_no')->from('df_release')->get();
	return $ret->result();

}

function get_departments($df)
{
	$departments=array();
	$rty=$this->db->select('a.department_id,b.department')->from('task_department_wise_scheduling a')->join('departments b','a.department_id=b.department_id')->where('df_id',$df)->group_by('a.department_id')->get();
	if($rty->num_rows()>0)
	{
		$t=1;
		foreach($rty->result() as $row)
		{
			$text="<a href='".page_url."Task/task_wise_gantchart/".$row->department_id."/".$df."' target='_blank'><u>".$row->department."</u></a>";
			$departments[]=str_replace('"','',array("label"=>$text,"id"=>$t));
		$t++;
		}

	}

	return json_encode($departments,JSON_PRETTY_PRINT);

}

function getTaskdateIntervals($dfid)
{

$departments=array();
$departments1=array();
	$rty=$this->db->select('min(start_date) as min_date,max(end_date) as max_date')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id')->where('a.df_id',$dfid)->get();
	if($rty->num_rows()>0)
	{
		$t=1;
		foreach($rty->result() as $row);


		$allmonths=self::getMonthsInRange($row->min_date,$row->max_date);
		if(count($allmonths)>0)
		{
			$w=1;
			foreach($allmonths as $monthdata)
			{
				if($w==1)
				{
					$daystart=(int) date('d',strtotime($row->min_date));
					if($daystart<16)
					{
						$mi="01";
					}else
					{
						$mi="16";
					}
				$start=date($monthdata['year']."-".$monthdata['month'].'-'.$mi);
				}else
				{
					$start=date($monthdata['year']."-".$monthdata['month'].'-01');
				}

				$end=date('Y-m-t',strtotime($start));

				$allweek=self::getallweeks($start,$end);

				$departments[]=str_replace('"','',array("start"=>date('d/m/Y',strtotime($start)),"end"=>date('d/m/Y',strtotime($end)),"label"=>date('F',strtotime($start))));

				if(count($allweek)>0)
				{	
					foreach($allweek as $allweeks)
					{
					$departments1[]=str_replace('"','',array("start"=>date('d/m/Y',strtotime($allweeks['start_date'])),"end"=>date('d/m/Y',strtotime($allweeks['end_date'])),"label"=>"WEEK ".$w));
					$w++;
				}
				}


			}
		}

		
		

	}

	return json_encode($departments,JSON_PRETTY_PRINT)."~".json_encode($departments1,JSON_PRETTY_PRINT);


}

function getMonthsInRange($startDate, $endDate)
{
    $months = array();

    while (strtotime($startDate) <= strtotime($endDate)) {
        $months[] = array(
            'year' => date('Y', strtotime($startDate)),
            'month' => date('m', strtotime($startDate)),
        );

        // Set date to 1 so that new month is returned as the month changes.
        $startDate = date('01 M Y', strtotime($startDate . '+ 1 month'));
    }

    return $months;
}

function getallweeks($start_date,$end_Date)
{
	$data=array();
$date1 = new DateTime($start_date);
$date2 = new DateTime($end_Date);
$interval = $date1->diff($date2);

$weeks = floor(($interval->days) / 7);

for($i = 1; $i <= $weeks; $i++){    
    $week = $date1->format("W");
    $date1->add(new DateInterval('P4D'));
    $data[]=array('week'=>$week,'start_date'=>$start_date,'end_date'=>$date1->format('Y-m-d'));
    $date1->add(new DateInterval('P3D'));
    $start_date = $date1->format('Y-m-d');
}

return $data; 
}

function getTaskStatus($dfid)
{
	$data=array();
	$rty=$this->db->select('a.department_id,b.department')->from('task_department_wise_scheduling a')->join('departments b','a.department_id=b.department_id')->where('df_id',$dfid)->group_by('a.department_id')->get();
	if($rty->num_rows()>0)
	{
		$t=1;
		foreach($rty->result() as $row)
		{

			$rty=$this->db->select('min(start_date) as min_date,max(end_date) as max_date')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id')->where('a.df_id',$dfid)->where('a.department_id',$row->department_id)->get();
			if($rty->num_rows()>0)
			{
			
			foreach($rty->result() as $row11);
				$data[]=str_replace('"','',array("label"=>"Planned",
				'processid'=>$t,
				'start'=>date('d/m/Y',strtotime($row11->min_date)),
				'end'=>date('d/m/Y',strtotime($row11->max_date)),
				'id'=>$t."-1",
				'color'=>'#39A7FF',
				'alpha'=>"100",
				'height'=>"27%",
				'toppadding'=>"32%"
				));

			}

			// CHECK FOR DONE
			$rty=$this->db->select('min(start_date) as task_min_date,max(DATE(task_completed_on)) as max_completion_date,max(end_date) as task_max_date')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id')->where('a.df_id',$dfid)->where('a.department_id',$row->department_id)->where('task_status',1)->get();
			if($rty->num_rows()>0)
			{
				foreach($rty->result() as $row12);
					// echo $row12->task_min_date."<br/>".$row12->max_completion_date;exit;
				if($row12->task_min_date<>'' && $row12->max_completion_date<>'')
				{
				$per=round(self::getalltaskpercentage($row->department_id,$dfid));
				$data[]=str_replace('"','',array("label"=>"Actual",
				'processid'=>$t,
				'start'=>date('d/m/Y',strtotime($row12->task_min_date)),
				'end'=>date('d/m/Y',strtotime($row12->max_completion_date)),
				'id'=>$t,
				'color'=>'#5DD99B',
				'alpha'=>"100",
				'height'=>"27%",
				'toppadding'=>"65%",
				'percentcomplete'=>$per
				));

				//echo $row12->max_completion_date."<br/>".$row11->max_date; exit;
				if(strtotime($row12->max_completion_date)>strtotime($row11->max_date))
				{
					$date1=date_create($row11->max_date);
					$date2=date_create($row12->max_completion_date);
					$diff=date_diff($date1,$date2);
					$delay_days=$diff->format("%R%a");
					$delay_days=(int) $delay_days;
					$startDate=date('d/m/Y',strtotime($row12->max_completion_date." -".$delay_days." Days"));
					$endDATE=date('d/m/Y',strtotime($row12->max_completion_date));
					
				
				$data[]=str_replace('"','',array("label"=>"Delay",
				'processid'=>$t,
				'start'=>$startDate,
				'end'=>$endDATE,
				'id'=>$t."-2",
				'color'=>'#e44a00',
				'alpha'=>"100",
				'height'=>"27%",
				'toppadding'=>"65%",
				'tooltext'=>"Delayed by $delay_days days."
				));


				}
			}

			}

	
		$t++;
		}

		





		

}

//echo "<pre>"; print_r($data); exit;
	return json_encode($data,JSON_PRETTY_PRINT);
}

function getalltaskpercentage($department,$dfid)
{
	$per=0;
	$total=0;
	$done=array();
	$done[]=0;
	$data=$this->db->select('id,task_status')->from('task_department_wise_scheduling')->where('df_id',$dfid)->where('department_id',$department)->get();
	if($data->num_rows()>0)
	{
		foreach($data->result() as $d)
		{
			if($d->task_status==1)
			{
			$done[]=1;
			}
		}
	}

	if($data->num_rows()>0)
	{
	$per=(array_sum($done)*100)/$data->num_rows();
	}


return $per;

}

function getTaskHead1($dfid)
{
	$departments=array();
	$rty=$this->db->select('a.department_id,b.department')->from('task_department_wise_scheduling a')->join('departments b','a.department_id=b.department_id')->where('df_id',$dfid)->group_by('a.department_id')->get();
	if($rty->num_rows()>0)
	{
		$t=1;
		foreach($rty->result() as $row)
		{

			$rty=$this->db->select('min(start_date) as min_date,max(end_date) as max_date')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id')->where('a.df_id',$dfid)->where('a.department_id',$row->department_id)->get();
			if($rty->num_rows()>0)
			{
			foreach($rty->result() as $row11);
			$lab=date('d/m/Y',strtotime($row11->min_date)).'<br/><br/>'.date('d/m/Y',strtotime($row11->max_date));
			$departments[]=str_replace('"','',array("label"=>$lab));
			}
		$t++;
		}

	}

	return json_encode($departments,JSON_PRETTY_PRINT);
}

function getHODName($dfid)
{
	$departments=array();
	$rty=$this->db->select('a.department_id,b.department')->from('task_department_wise_scheduling a')->join('departments b','a.department_id=b.department_id')->where('df_id',$dfid)->group_by('a.department_id')->get();
	if($rty->num_rows()>0)
	{
		$t=1;
		foreach($rty->result() as $row)
		{
			$hod=self::getHOD($row->department_id);
			$departments[]=str_replace('"','',array("label"=>$hod));
		}

	}

	return json_encode($departments,JSON_PRETTY_PRINT);
}

function getHOD($df)
{
	$hod='';
$q = $this->db->select('b.title,b.first_name, b.last_name')->from('prestogroup_teams a')->join('system_users b','a.team_leader=b.user_id')->where('a.department_id',$df)->get();

if($q->num_rows()>0){
foreach($q->result() as $sendmessage);
$hod = ucfirst($sendmessage->title." ".$sendmessage->first_name)." ".ucfirst($sendmessage->last_name);
}


return $hod;
}

function get_all_df_detailsByID($dfno)
{
	$ret=$this->db->select('id,df_no')->from('df_release')->where('id',$dfno)->get();
	return $ret->result();

}

function getDepartmentBYID($department)
{
	if($department>0)
	{
	$dep='';
	$rty=$this->db->select('b.department')->from('departments b')->where('b.department_id',$department)->get();
	if($rty->num_rows()>0)
	{
		foreach($rty->result() as $row);
		$dep=$row->department;
	}
	}else
	{
		$dep="All Departments";
	}

	return $dep;
}


function get_departments_task($department_id,$df)
{
	$departments=array();
	$rty=$this->db->select('b.task_id,b.task_name')->from('task_department_wise_scheduling a')->join('task_management b','a.taskid=b.task_id')->where('a.department_id',$department_id)->where('a.df_id',$df)->where('b.task_frequency!=',2)->where('b.task_id!=',1)->get();
	if($rty->num_rows()>0)
	{
		$t=1;
		foreach($rty->result() as $row)
		{
			$text=$row->task_name;
			$departments[]=str_replace('"','',array("label"=>$text,"id"=>$t));
		$t++;
		}

	}


	return json_encode($departments,JSON_PRETTY_PRINT);

}

function getTaskdateIntervalsByDepartment($department_id,$dfid)
{

$departments=array();
$departments1=array();
	$rty=$this->db->select('min(start_date) as min_date,max(end_date) as max_date')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id')->where('a.df_id',$dfid)->where('a.department_id',$department_id)->get();
	if($rty->num_rows()>0)
	{
		$t=1;
		foreach($rty->result() as $row);


		$allmonths=self::getMonthsInRange($row->min_date,$row->max_date);
		if(count($allmonths)>0)
		{
			$w=1;
			foreach($allmonths as $monthdata)
			{
				if($w==1)
				{
					$daystart=(int) date('d',strtotime($row->min_date));
					if($daystart<16)
					{
						$mi="01";
					}else
					{
						$mi="16";
					}
				$start=date($monthdata['year']."-".$monthdata['month'].'-'.$mi);
				}else
				{
					$start=date($monthdata['year']."-".$monthdata['month'].'-01');
				}

				$end=date('Y-m-t',strtotime($start));

				$allweek=self::getallweeks($start,$end);

				$departments[]=str_replace('"','',array("start"=>date('d/m/Y',strtotime($start)),"end"=>date('d/m/Y',strtotime($end)),"label"=>date('F',strtotime($start))));

				if(count($allweek)>0)
				{	
					foreach($allweek as $allweeks)
					{
					$departments1[]=str_replace('"','',array("start"=>date('d/m/Y',strtotime($allweeks['start_date'])),"end"=>date('d/m/Y',strtotime($allweeks['end_date'])),"label"=>"WEEK ".$w));
					$w++;
				}
				}


			}
		}

		
		

	}

	return json_encode($departments,JSON_PRETTY_PRINT)."~".json_encode($departments1,JSON_PRETTY_PRINT);


}

function getTaskStatusByDepartment($department_id,$dfid)
{
	$data=array();
	$rty=$this->db->select('a.taskid,a.department_id,b.department')->from('task_department_wise_scheduling a')->join('departments b','a.department_id=b.department_id')->where('df_id',$dfid)->where('a.department_id',$department_id)->get();
	if($rty->num_rows()>0)
	{
		$t=1;
		foreach($rty->result() as $row)
		{

			$rty=$this->db->select('min(start_date) as min_date,max(end_date) as max_date')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id')->where('a.df_id',$dfid)->where('a.department_id',$row->department_id)->where('a.taskid',$row->taskid)->get();
			if($rty->num_rows()>0)
			{
			
			foreach($rty->result() as $row11);
				$data[]=str_replace('"','',array("label"=>"Planned",
				'processid'=>$t,
				'start'=>date('d/m/Y',strtotime($row11->min_date)),
				'end'=>date('d/m/Y',strtotime($row11->max_date)),
				'id'=>$t."-1",
				'color'=>'#39A7FF',
				'alpha'=>"100",
				'height'=>"27%",
				'toppadding'=>"32%"
				));

			}

			// CHECK FOR DONE
			$rty=$this->db->select('min(start_date) as task_min_date,max(DATE(task_completed_on)) as max_completion_date,max(end_date) as task_max_date')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id')->where('a.df_id',$dfid)->where('a.department_id',$row->department_id)->where('a.taskid',$row->taskid)->where('task_status',1)->get();
			if($rty->num_rows()>0)
			{
				foreach($rty->result() as $row12);
					// echo $row12->task_min_date."<br/>".$row12->max_completion_date;exit;
				if($row12->task_min_date<>'' && $row12->max_completion_date<>'')
				{
				$per=round(self::getalltaskpercentage($row->department_id,$dfid));
				$data[]=str_replace('"','',array("label"=>"Actual",
				'processid'=>$t,
				'start'=>date('d/m/Y',strtotime($row12->task_min_date)),
				'end'=>date('d/m/Y',strtotime($row12->max_completion_date)),
				'id'=>$t,
				'color'=>'#5DD99B',
				'alpha'=>"100",
				'height'=>"27%",
				'toppadding'=>"65%",
				'percentcomplete'=>100
				));

				//echo $row12->max_completion_date."<br/>".$row11->max_date; exit;
				if(strtotime($row12->max_completion_date)>strtotime($row11->max_date))
				{
					$date1=date_create($row11->max_date);
					$date2=date_create($row12->max_completion_date);
					$diff=date_diff($date1,$date2);
					$delay_days=$diff->format("%R%a");
					$delay_days=(int) $delay_days;
					$startDate=date('d/m/Y',strtotime($row12->max_completion_date." -".$delay_days." Days"));
					$endDATE=date('d/m/Y',strtotime($row12->max_completion_date));
					
				
				$data[]=str_replace('"','',array("label"=>"Delay",
				'processid'=>$t,
				'start'=>$startDate,
				'end'=>$endDATE,
				'id'=>$t."-2",
				'color'=>'#e44a00',
				'alpha'=>"100",
				'height'=>"27%",
				'toppadding'=>"65%",
				'tooltext'=>"Delayed by $delay_days days."
				));


				}
			}

			}

	
		$t++;
		}

		





		

}

//echo "<pre>"; print_r($data); exit;
	return json_encode($data,JSON_PRETTY_PRINT);
}


function getTaskHead1ByDepartment($department_id,$dfid)
{
	$departments=array();
	$rty=$this->db->select('a.department_id,b.department')->from('task_department_wise_scheduling a')->join('departments b','a.department_id=b.department_id')->where('df_id',$dfid)->where('a.department_id',$department_id)->get();
	if($rty->num_rows()>0)
	{
		$t=1;
		foreach($rty->result() as $row)
		{

			$rty=$this->db->select('min(start_date) as min_date,max(end_date) as max_date')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id')->where('a.df_id',$dfid)->where('a.department_id',$row->department_id)->get();
			if($rty->num_rows()>0)
			{
			foreach($rty->result() as $row11);
			$lab=date('d/m/Y',strtotime($row11->min_date)).'<br/><br/>'.date('d/m/Y',strtotime($row11->max_date));
			$departments[]=str_replace('"','',array("label"=>$lab));
			}
		$t++;
		}

	}

	return json_encode($departments,JSON_PRETTY_PRINT);
}

function getUserNameByDepartment($department_id,$dfid)
{
	$departments=array();
	$rty=$this->db->select('c.first_name,c.last_name,a.department_id,b.department')->from('task_department_wise_scheduling a')->join('departments b','a.department_id=b.department_id')->join('system_users c','a.assigned_user=c.user_id')->where('df_id',$dfid)->where('a.department_id',$department_id)->get();
	if($rty->num_rows()>0)
	{
		$t=1;
		foreach($rty->result() as $row)
		{
			$hod=ucwords(strtolower($row->first_name." ".$row->last_name));
			$departments[]=str_replace('"','',array("label"=>$hod));
		}

	}

	return json_encode($departments,JSON_PRETTY_PRINT);
}


function gettaskidfrommaster($id){
	$q = $this->db->select('taskid')->from('task_department_wise_scheduling')->where('id',$id)->get();
	if($q->num_rows()>0){
		foreach($q->result() as $row);
		return $row->taskid;
	}
}

function getdfidfrommaster($id){
	$q = $this->db->select('df_id')->from('task_department_wise_scheduling')->where('id',$id)->get();
	if($q->num_rows()>0){
		foreach($q->result() as $row);
		return $row->df_id;
	}
}

function getdepartmentoftask($taskid,$dfid){
	$q = $this->db->select('department_id')->from('task_department_wise_scheduling')->where('df_id',$dfid)->where('taskid',$taskid)->get();
	if($q->num_rows()>0){
		foreach($q->result() as $row);
		return $row->department_id;
	}
}

function getdfdetail($dfid){
	$q = $this->db->select('df_no')->from('df_release')->where('id',$dfid)->get();
	foreach($q->result() as $row);
	return $row->df_no;

}

function getdepartmentuserwhomdfassigned($taskid,$departmentoftask,$df_id){
	$q = $this->db->select('a.department_id, a.task_message, department')->from('task_related_messages a')->join('departments b','a.department_id=b.department_id','left')->where('a.taskid',$taskid)->get();
	if($q->num_rows()>0){
		foreach($q->result() as $row){
			$departmentid = $row->department_id;
			$departmentname = $row->department;
			$dfnumber = $this->getdfdetail($df_id);
			$task_message = $row->task_message;
			if($departmentid==0){
				$this->fetchalldepartmentofdf($df_id,$task_message,$dfnumber);
			}else{
				$this->fetchuserbydepartmentanddf($departmentid, $df_id, $task_message,$departmentname,$dfnumber);
			}
			

		}
	}
}


function fetchuserbydepartmentanddf($departmentid, $df_id, $task_message,$departmentname,$dfnumber){
	$q = $this->db->select('b.first_name, b.last_name, b.email, b.contact_number')->from('task_department_wise_scheduling a')->join('system_users b','a.assigned_user=b.user_id','left')->where('a.department_id',$departmentid)->where('a.df_id',$df_id)->group_by('a.assigned_user')->get();
			if($q->num_rows()>0){
				foreach($q->result() as $row1){
					$name = $row1->first_name." ".$row1->last_name;
					$email = $row1->email;
					$contactnumber = $row1->contact_number;
					$emailid = $row1->email;
					$find = array('{df_number}','{department_name}', '{department_hod}');
							$replace = array(ucwords(strtoupper($dfnumber)), ucwords(strtolower($departmentname)), ucwords(strtolower($name)));

					$message_body = str_replace($find, $replace, $task_message);
					//$contactnumber = "9718991797";
					$msg = $this->clean($message_body);
					
					/*WhatsApp API*/
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					//'receiverMobileNo' => '91'.$contactnumber,
					'receiverMobileNo' => '91'.$contactnumber,
					'usernamess' => whatsappuser1,
					'passwordss' => whatsapppass1,
					'message'=>strip_tags($msg));

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
						//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					/* end */



					$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">			
					<tr>				
						<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://gamavis.com/dynachem/assets/images/shubhampack.png" width="200px;" alt="Shubham Flexible Packaging Machines Pvt. Ltd" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>Updates Related to DF NO '.$dfnumber.'</strong><br></td>
					  </tr> 
					  <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br> '.$message_body.'</td>
					  </tr> 
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					 
					 
					<tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;;">Best Regards, <br>
						Shubham Pack Team
						</td>
					  </tr>
					 <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td>
					   </td>
					  </tr>
					</table>
					</td>
				  </tr>
				</table>';
				$subjectname = "Updates related to DF NO ".$dfnumber;
					$this->email->set_mailtype("html");
					$this->email->to($email);
					//$this->email->to('mangleshup@gmail.com');
					//$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('taskmanagement@shubhampack.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();	
    				


				}
			}
}

function fetchalldepartmentofdf($df_id, $task_message, $dfnumber){

	$q = $this->db->select('department_id')->from('task_department_wise_scheduling')->where('df_id',$df_id)->group_by('department_id')->get();
	foreach($q->result() as $row){
		$q = $this->db->select('b.first_name, b.last_name, b.email, b.contact_number, c.department')->from('prestogroup_teams a')->join('system_users b','a.team_leader=b.user_id','left')->join('departments c','a.department_id=c.department_id','left')->where('a.department_id',$row->department_id)->get();
		foreach($q->result() as $row1){

					$name = $row1->first_name." ".$row1->last_name;
					$contactnumber = $row1->contact_number;
					$emailid = $row1->email;
					$departmentname = $row1->department;
					$find = array('{df_number}','{department_name}', '{department_hod}');
					$replace = array(ucwords(strtoupper($dfnumber)), ucwords(strtolower($departmentname)), ucwords(strtolower($name)));

					$message_body = str_replace($find, $replace, $task_message);
					//$contactnumber = "9718991797";
					$msg = $this->clean($message_body);
					

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					//'receiverMobileNo' => '91'.$contactnumber,
					'receiverMobileNo' => '91'.$contactnumber,
					'usernamess' => whatsappuser1,
					'passwordss' => whatsapppass1,
					'message'=>strip_tags($msg));

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
						//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					/* end */

					$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">			
					<tr>				
						<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://gamavis.com/dynachem/assets/images/shubhampack.png" width="200px;" alt="Shubham Flexible Packaging Machines Pvt. Ltd" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>Updates Related to DF NO '.$dfnumber.'</strong><br></td>
					  </tr> 
					  <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br> '.$message_body.'</td>
					  </tr> 
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					 
					 
					<tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;;">Best Regards, <br>
						Shubham Pack Team
						</td>
					  </tr>
					 <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td>
					   </td>
					  </tr>
					</table>
					</td>
				  </tr>
				</table>';
				
				
				 $subjectname = "Updates related to DF NO ".$dfnumber;
					$this->email->set_mailtype("html");
					$this->email->to($emailid);
					//$this->email->to('mangleshup@gmail.com');
					//$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('taskmanagement@shubhampack.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();	
    				

		}
	}

}

function clean($str)
{       
    $str = utf8_decode($str);
    //str_replace("&nbsp;", "", $str);
    $str = str_replace('&nbsp;', ' ', html_entity_decode($str));
    $str = preg_replace("/\s+/", " ", $str);
    $str = trim($str);
    return $str;
}

function getallrunningdf(){
	$q = $this->db->select('id,df_no')->from('df_release')->where('df_status',0)->where('on_hold',0)->order_by('df_no','asc')->get();
	return $q->result();
	
}

function getRoot()
{
	
		// $this->db->query('SELECT t1.task_id,t1.task_name,c.department_id
		// FROM task_management t1
		// JOIN departments c ON t1.department_id=c.department_id
		// LEFT JOIN task_management t2 ON t1.id = t2.start_from
		// WHERE t2.start_from IS NULL;');
	 $resty=$this->db->select('a.task_id,a.task_name,a.department_id,b.department')->from('task_management a')->join('departments b','b.department_id=a.department_id')->where('a.root',1)->where('a.status',1)->get();
	if($resty->num_rows()>0)
	{
		foreach($resty->result() as $row)
		{
		$data=array('name'=>$row->department,'title'=>$row->task_id.'-'.$row->task_name,'children'=>$this->getchildren($row->task_id));
		}


		
	}

	//echo "<pre>"; print_r($data); exit;
	return str_replace('"', "'", json_encode($data));
	//return json_encode($data);
}

function getchildren($taskid)
{
	$d=array();
	$resty=$this->db->select('a.task_id,a.task_name,a.department_id,b.department')->from('task_management a')->join('departments b','b.department_id=a.department_id')->where('a.tat_start_from',$taskid)->where('a.status',1)->get();
	if($resty->num_rows()>0)
	{
		
      foreach($resty->result() as $row1)
      {

   	    	$d[]=array('name'=>$row1->department,'title'=>$row1->task_id.'-'.$row1->task_name,'children'=>$this->getchildren($row1->task_id)); 

      }
  }

      return $d;

}

	function reversetree($tasks, $parentId = null, $level = 0) {
    $result = [];

    foreach ($tasks as $task) {
        if ($task['tat_start_from'] == $parentId) {
            $task['level'] = $level;
            $result[] = $task;
            $children =$this->reversetree($tasks, $task['task_id'], $level + 1);
            $result = array_merge($result, $children);
        }
    }

    return $result;
}

 // Function to get tasks sorted by their execution order
    public function get_sorted_tasks() {
        $tasks = $this->db->select('task_id,task_name,tat_start_from')->get('task_management')->result_array(); // Assuming 'task_management' is the table name
        $sorted_tasks = array();

        // Create a mapping of task IDs to their corresponding task data
        $task_map = array();
        foreach ($tasks as $task) {
            $task_map[$task['task_id']] = $task;
        }

        // Initialize an array to keep track of visited tasks
        $visited = array();

        // Iterate through each task and perform DFS to get the sorted tasks
        foreach ($tasks as $task) {
            if (!isset($visited[$task['task_id']])) {
                $this->dfs($task['task_id'], $task_map, $sorted_tasks, $visited);
            }
        }


        //echo "<pre>";print_r($sorted_tasks); exit;
        $t=0;
        foreach($sorted_tasks as $tasks)
        {
            
            $dd=array('system_created_sort_order'=>$t);
            $this->db->where('task_id',$tasks['task_id']);
            $this->db->update('task_management',$dd);

        $t++;
        }

        //return $sorted_tasks;
    }

    // Recursive function for DFS traversal
    private function dfs($task_id, $task_map, &$sorted_tasks, &$visited) {
        if (!isset($visited[$task_id])) {
            $visited[$task_id] = true;
            $task = $task_map[$task_id];
            $start_from_task_id = $task['tat_start_from'];

            // If the task has no parent task, add it to the sorted tasks array
            if ($start_from_task_id == null || !isset($task_map[$start_from_task_id])) {
                $sorted_tasks[] = $task;
            } else {
                // If the task has a parent task, recursively call dfs for the parent task first
                $this->dfs($start_from_task_id, $task_map, $sorted_tasks, $visited);
                $sorted_tasks[] = $task;
            }

            // If the task has child tasks, recursively call dfs for each child task
            foreach ($task_map as $child_task_id => $child_task) {
                if ($child_task['tat_start_from'] == $task_id) {
                    $this->dfs($child_task_id, $task_map, $sorted_tasks, $visited);
                }
            }
        }
    }


public function notificationofprocessdone($taskid){
	$q = $this->db->select('department_id, task_message')->from('task_related_messages')->where('taskid',$taskid)->get();
	if($q->num_rows()>0){
		foreach($q->result() as $row){

			if($row->department_id==0){
				$qq = $this->db->select('department_id, team_leader')->from('prestogroup_teams')->where('business_loc_id',2)->get();
				if($qq->num_rows()>0){

					foreach($qq->result() as $row1){
						$q2 = $this->db->select('first_name, last_name, email, contact_number')->from('system_users')->where('department_id',$row1->department_id)->where('user_id',$row1->team_leader)->where('business_location',2)->get();
						if($q2->num_rows()>0){
							foreach($q2->result() as $row2){

					$msg = $row->task_message;
					$contactnumber = $row2->contact_number;
					$emailid = $row2->email;
					//$contactno = "9718991797";
					
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					//'receiverMobileNo' => '91'.$contactnumber,
					'receiverMobileNo' => '91'.$contactnumber,
					'username' => whatsappuser1,
					'password' => whatsapppass1,
					'message'=>strip_tags($msg));

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
						//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					
					/* end */

					$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
				   <tr>
					<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://shubhampack.com/wp-content/uploads/2021/05/Logo.png" width="200px;" alt="Shubham Flexible Packaging Machines Pvt. Ltd. " /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>Exciting News, Team Leaders! </strong><br><br></td>
					  </tr> 
					 
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;">'.$msg.' 

						</td>
					  </tr>
					  
				
					  <tr>
						<td>
					   </td>
					  </tr>
					</table>
					</td>
				  </tr>
				</table>';
				
					$subjectname = "Exciting News, Team Leaders!";
					$this->email->set_mailtype("html");
					$this->email->to($emailid);
					//$this->email->to('mangleshup@gmail.com');
					$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('taskmanagement@shubhampack.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();	
    			
    					}
						}

					}
				}

			}else{

				$qq = $this->db->select('department_id, team_leader')->from('prestogroup_teams')->where('department_id',$row->department_id)->where('business_loc_id',2)->get();
				if($qq->num_rows()>0){

					foreach($qq->result() as $row1){
						$q2 = $this->db->select('first_name, last_name, email, contact_number')->from('system_users')->where('department_id',$row1->department_id)->where('user_id',$row1->team_leader)->where('business_location',2)->get();
						if($q2->num_rows()>0){
							foreach($q2->result() as $row2){

					$msg = $row->task_message;
					$contactnumber = $row2->contact_number;
					$emailid = $row2->email;
					//$contactno = "9718991797";
					
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					//'receiverMobileNo' => '91'.$contactnumber,
					'receiverMobileNo' => '91'.$contactnumber,
					'username' => whatsappuser1,
					'password' => whatsapppass1,
					'message'=>strip_tags($msg));

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
						//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					
					/* end */

					$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
				   <tr>
					<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://shubhampack.com/wp-content/uploads/2021/05/Logo.png" width="200px;" alt="Shubham Flexible Packaging Machines Pvt. Ltd. " /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>Exciting News, Team Leaders! </strong><br><br></td>
					  </tr> 
					 
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;">'.$msg.' 

						</td>
					  </tr>
					  
				
					  <tr>
						<td>
					   </td>
					  </tr>
					</table>
					</td>
				  </tr>
				</table>';
				
					$subjectname = "Exciting News, Team Leaders!";
					$this->email->set_mailtype("html");
					$this->email->to($emailid);
					//$this->email->to('mangleshup@gmail.com');
					//$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('taskmanagement@shubhampack.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();	
    			





							}
						}

					}
				}

			}
		}
	}
}


public function sendnotificationtorespectiveteammemberforassignment($dfid, $message, $userid, $assignedby){

	$q = $this->db->select('first_name, last_name, email, contact_number')->from('system_users')->where('user_id',$userid)->get();
	if($q->num_rows()>0){
		foreach($q->result() as $row);

		$q1 = $this->db->select('df_no')->from('df_release')->where('id',$dfid)->get();
		if($q1->num_rows()>0){
			foreach($q1->result() as $row1);
			$dfno = $row1->df_no;
		}else{
			$dfno="";
		}

		$q2 = $this->db->select('first_name, last_name')->from('system_users')->where('user_id',$assignedby)->get();
		if($q2->num_rows()>0){
			foreach($q2->result() as $row2);
			$assignedbyuser = ucfirst($row2->first_name." ".$row2->last_name);
		}else{
			$assignedbyuser = "Assigned by PMS Software";
		}


					$msg = "New Task of DF - ".$dfno." Assigned to You by ".$$assignedbyuser. "\n".$message;
					$contactnumber = $row->contact_number;
					$emailid = $row->email;
					//$contactno = "9718991797";
					
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					//'receiverMobileNo' => '91'.$contactnumber,
					'receiverMobileNo' => '91'.$contactnumber,
					//'username' => whatsappuser1,
					//'password' => whatsapppass1,
					'username' => '',
					'password' => '',
					'message'=>strip_tags($msg));

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
						//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					
					/* end */

					$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
				   <tr>
					<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://shubhampack.com/wp-content/uploads/2021/05/Logo.png" width="200px;" alt="Shubham Flexible Packaging Machines Pvt. Ltd. " /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>New Task of DF - '.$dfno.' Assigned to You. </strong><br><br></td>
					  </tr> 
					 
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;">'.$message.' 

						</td>
					  </tr>
					  
				
					  <tr>
						<td>
					   </td>
					  </tr>
					</table>
					</td>
				  </tr>
				</table>';
				
					$subjectname = "New Task of DF - ".$dfno." Assigned to You by ".$assignedbyuser;
					$this->email->set_mailtype("html");
					$this->email->to($emailid);
					//$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('taskmanagement@shubhampack.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();	


	}

}


/**
 * Sends a single "digest" notification for multiple tasks.
 *
 * @param int $userid The user receiving the notification
 * @param array $df_no_list An array of DF numbers (e.g., ["DF-001", "DF-002"])
 * @param string $message The custom message from the HOD
 * @param string $assignedbyuser The name of the assigner
 */
public function send_digest_notification($userid, $df_no_list, $message, $assignedbyuser) {

    // 1. Get user info (only 1 query)
    $q = $this->db->select('first_name, last_name, email, contact_number')
                   ->from('system_users')
                   ->where('user_id', $userid)
                   ->get();

    if ($q->num_rows() > 0) {
        $row = $q->row();
        
        $task_count = count($df_no_list);
        $df_list_string = implode(", ", $df_no_list);

        $contactnumber = $row->contact_number;
        $emailid = $row->email;

        // --- 2. Build WhatsApp Message ---
        $msg_header = "You have been assigned $task_count new task(s) by $assignedbyuser for DF(s): $df_list_string";
        $msg = $msg_header . "\n\n" . $message;
        
        // --- 3. Send ONE cURL Call ---
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $post = array(
            'receiverMobileNo' => '91' . $contactnumber,
            'username' => '', // Add your username
            'password' => '', // Add your password
            'message' => strip_tags($msg)
        );

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $result = curl_exec($ch);
        // You should add error logging here if $result is false
        curl_close($ch);

        // --- 4. Build ONE Email ---
        $email_subject = "You have been assigned $task_count new task(s) by $assignedbyuser";
        
        // Build an HTML list for the email
        $df_html_list = "<ul>";
        foreach ($df_no_list as $df_no) {
             $df_html_list .= "<li>" . htmlspecialchars($df_no) . "</li>";
        }
        $df_html_list .= "</ul>";

        $Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
                <td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://shubhampack.com/wp-content/uploads/2021/05/Logo.png" width="200px;" alt="Shubham Flexible Packaging Machines Pvt. Ltd. " /></td>
                    </tr>
                    <tr>
                        <td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>' . $email_subject . '</strong><br><br></td>
                    </tr>
                    <tr><td>You have been assigned the following tasks:</td></tr>
                    <tr><td>' . $df_html_list . '</td></tr>
                    <tr>
                        <td style="font-family: \'Montserrat\', sans-serif; font-size: 18px; color: #666666; padding-top: 20px;">
                            <strong>Message from ' . $assignedbyuser . ':</strong><br>
                            ' . nl2br(htmlspecialchars($message)) . '
                        </td>
                    </tr>
                </table></td>
            </tr>
        </table>';

        // --- 5. Send ONE Email ---
        $this->email->set_mailtype("html");
        $this->email->to($emailid);
        $this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
        $this->email->from('taskmanagement@shubhampack.com');
        $this->email->subject($email_subject);
        $this->email->message($Message);
        $result11 = $this->email->send();
    }
}


public function getmessageoftaskcompletionandtrigger($mastertaskid, $completedby, $dfid){
	$q = $this->db->select('a.department_id, a.task_message, b.task_name')->from('task_related_messages a')->join('task_management b','a.taskid=b.task_id')->where('a.taskid',$mastertaskid)->get();
	if($q->num_rows()>0){
		foreach($q->result() as $row){

			$ab = $this->db->select('first_name, last_name')->from('system_users')->where('user_id',$completedby)->get();
			foreach($ab->result() as $taskcompletedby);
			$taskcompletedbypersonname = ucfirst($taskcompletedby->first_name." ".$taskcompletedby->last_name);
			$dfno = $this->getdfno($dfid);
			$message1 = "\nDF No. ".$dfno."\nTask ".$row->task_name."\nCompleted By ".$taskcompletedbypersonname;
			$message2 = "<br>DF No. ".$dfno."<br>Task ".$row->task_name."<br>Completed By ".$taskcompletedbypersonname;
			if($row->department_id==0){
				$qq = $this->db->select('department_id, team_leader')->from('prestogroup_teams')->where('business_loc_id',2)->get();
				if($qq->num_rows()>0){

					foreach($qq->result() as $row1){
						$q2 = $this->db->select('first_name, last_name, email, contact_number')->from('system_users')->where('department_id',$row1->department_id)->where('user_id',$row1->team_leader)->where('business_location',2)->get();
						if($q2->num_rows()>0){
							foreach($q2->result() as $row2){

					$msg = $row->task_message.$message1;
					$emailmessage = $row->task_message.$message2;
					$contactnumber = $row2->contact_number;
					$emailid = $row2->email;
					//$contactno = "9718991797";
					
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					//'receiverMobileNo' => '91'.$contactnumber,
					'receiverMobileNo' => '91'.$contactnumber,
					'username' => whatsappuser1,
					'password' => whatsapppass1,
					'message'=>strip_tags($msg));

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
						//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					
					/* end */

					$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
				   <tr>
					<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://shubhampack.com/wp-content/uploads/2021/05/Logo.png" width="200px;" alt="Shubham Flexible Packaging Machines Pvt. Ltd. " /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong> Please find update on DF NO. '.$dfno.'</strong><br><br></td>
					  </tr> 
					 
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;">'.$emailmessage.' 

						</td>
					  </tr>
					  <tr>
						<td>
					   </td>
					  </tr>
					</table>
					</td>
				  </tr>
				</table>';
				
					$subjectname = "Task- ".$row->task_name." has been Completed by ".$taskcompletedbypersonname;
					$this->email->set_mailtype("html");
					//$this->email->to($emailid);
					$this->email->to('mangleshup@gmail.com');
					//$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('taskmanagement@shubhampack.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();	

    						}
						}

					}
				}

			}else{

				$qq = $this->db->select('department_id, team_leader')->from('prestogroup_teams')->where('department_id',$row->department_id)->where('business_loc_id',2)->get();
				if($qq->num_rows()>0){

					foreach($qq->result() as $row1){
						$q2 = $this->db->select('first_name, last_name, email, contact_number')->from('system_users')->where('department_id',$row1->department_id)->where('user_id',$row1->team_leader)->where('business_location',2)->get();
						if($q2->num_rows()>0){
							foreach($q2->result() as $row2){

					$msg = $row->task_message.$message1;
					$emailmessage = $row->task_message.$message2;
					$contactnumber = $row2->contact_number;
					$emailid = $row2->email;
					//$contactno = "9718991797";
					
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					//'receiverMobileNo' => '91'.$contactnumber,
					'receiverMobileNo' => '91'.$contactnumber,
					'username' => whatsappuser1,
					'password' => whatsapppass1,
					'message'=>strip_tags($msg));

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
						//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);

					$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
				   <tr>
					<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://shubhampack.com/wp-content/uploads/2021/05/Logo.png" width="200px;" alt="Shubham Flexible Packaging Machines Pvt. Ltd. " /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong> Please find update on DF NO. '.$dfno.'</strong><br><br></td>
					  </tr> 
					 
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;">'.$emailmessage.' 

						</td>
					  </tr>
					  
				
					  <tr>
						<td>
					   </td>
					  </tr>
					</table>
					</td>
				  </tr>
				</table>';
				
					$subjectname = "Task- ".$row->task_name." has been Completed by ".$taskcompletedbypersonname;
					$this->email->set_mailtype("html");
					//$this->email->to($emailid);
					$this->email->to('mangleshup@gmail.com');
					$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('taskmanagement@shubhampack.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();	

    			





							}
						}

					}
				}

			}
		}
	}
}


public function getdfno($dfid){
	$abc = $this->db->select('df_no')->from('df_release')->where('id',$dfid)->get();
	$res = $abc->result();
	foreach($res as $row);
	$dfno = $row->df_no;
	return $dfno;
}

public function finaldateofdf($dfid){
	$q = $this->db->select('MAX(end_date) as lastdate')->from('task_department_wise_scheduling')->where('df_id',$dfid)->get();
	foreach($q->result() as $row);
	$lastdate = $row->lastdate;
	return $lastdate;
}

function dfreleasedate($dfid){
	$q = $this->db->select('added_on')->from('df_release')->where('id',$dfid)->get();
	foreach($q->result() as $row);
	$dfreleasedate = date('Y-m-d',strtotime($row->added_on));
	return $dfreleasedate;
}


 public function SKIP_holidays($start_date, $end_date) {
        // Your code to get holiday dates (e.g., from a database or an API)
        $holidays = $this->checkholiday();
        
        // Convert start and end dates to DateTime objects
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        
        // Check if start date is a holiday
        while (in_array($start->format('Y-m-d'), $holidays)) {
            $start->modify('+1 day');
        }

        // Check if any holidays between start and end dates and increase days accordingly
        $current_date = clone $start;
        while ($current_date <= $end) {
            if (in_array($current_date->format('Y-m-d'), $holidays)) {
                $end->modify('+1 day');
            }
            $current_date->modify('+1 day');
        }

        // Check if end date is a holiday
        while (in_array($end->format('Y-m-d'), $holidays)) {
            $end->modify('+1 day');
        }

        return array('start_date' => $start->format('Y-m-d'), 'end_date' => $end->format('Y-m-d'));
    }

    function getpreviousstagehodinfo($sendbackstageid){
    	$q = $this->db->select('department_id, assigned_user')->from('task_department_wise_scheduling')->where('')->get();
    }

function previoussteptasknotification($lastinsertid){
	$q = $this->db->select('b.first_name, b.last_name, b.email, b.contact_number, c.task_name, d.df_no, e.first_name as assignedbyfname, e.last_name as assignedbylname, a.department_id, a.assigned_user, a.remarks')->from('task_department_wise_scheduling a')->join('system_users b','a.assigned_user=b.user_id','left')->join('task_management c','a.taskid=c.task_id')->join('df_release d','a.df_id=d.id')->join('system_users e','a.assigned_by=e.user_id')->where('a.id',$lastinsertid)->group_by('a.assigned_user')->get();

			if($q->num_rows()>0){
				foreach($q->result() as $row1);
				$email = $row1->email;

				/*Get HOD Contact Detail*/
				$q5 = $this->db->select('b.first_name, b.last_name, b.email, contact_number')->from('prestogroup_teams a')->join('system_users b','a.team_leader=b.user_id')->where('a.department_id',$row1->department_id)->get();
				if($q5->num_rows()>0){
					foreach($q5->result() as $hod);
					$hodemail = $hod->email;
					$hodcontactno = $hod->contact_number;
				}else{
					$hodemail = "";
					$hodcontactno = "";
				}
				$taskremark = $row1->remarks;

				/*Get HOD Contact Detail*/


				/*Get User information and Department Head Detail for notification*/
				$q = $this->db->select('user_id, first_name, last_name, contact_number')->from('system_users')->where('user_id',$row1->assigned_user)->get();
				if($q->num_rows()>0){
					foreach($q->result() as $rowsssssss);
					$name = $rowsssssss->first_name." ".$rowsssssss->last_name;
					$contactnumber = $rowsssssss->contact_number;
					$assignedbyusername = $row1->assignedbyfname." ".$row1->assignedbylname;
					$whatsappmessage = "Dear \n".$name."\n"."DF No - *".$row1->df_no."*\n\nTask *".$row1->task_name."* has been reassigned to you. \n\nHere is the last remarks added by ".$assignedbyusername."\n\nRemark is mentioned below.\n\n*".$taskremark."*\n\n Note* _If you are HOD of the Team this message is only for your knowledge_\n\nBest Regards,\nShubham Flexible Packaging";
					
					/*WhatsApp API*/
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					'receiverMobileNo' => '91'.$contactnumber.",".$hodcontactno,
					//'receiverMobileNo' => '919718991797,'.$hodcontactno,
					'username' => whatsappuser1,
					'password' => whatsapppass1,
					'message'=>strip_tags($whatsappmessage));

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
						//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					/* end */

					$mailmessage = "Dear <br>".$name."<br><br>"."DF No : <b>".$row1->df_no."</b><br>Task : <b>".$row1->task_name."</b> has been reassigned to you. <br><br>Here is the reason added by <b>".$assignedbyusername."</b><br>Remark is mentioned below.<br>Remarks: <b>".$taskremark."</b>";

					$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">			
					<tr>				
						<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://gamavis.com/dynachem/assets/images/shubhampack.png" width="200px;" alt="Shubham Flexible Packaging Machines Pvt. Ltd" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>Task has been sent back to you for some reason mentioned below. DF No. '.$row1->df_no.'</strong><br></td>
					  </tr> 
					  <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br> '.$mailmessage.'</td>
					  </tr> 
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					 
					 
					<tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;;">Best Regards, <br>
						Shubham Pack Team
						</td>
					  </tr>
					 <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td>
					   </td>
					  </tr>
					</table>
					</td>
				  </tr>
				</table>';
				$subjectname = "Some issue with your Work. Task has been transfered in your panel for review ".$row1->df_no;
					$this->email->set_mailtype("html");
					$this->email->to($email);
					//$this->email->to('mangleshup@gmail.com');
					$this->email->cc($hodemail);
					//$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('taskmanagement@shubhampack.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();	

				}
				/*Get User information and Department Head Detail for notification*/
			}
}

function getWeekStartDates($df_id,$department)
{	$data=array();
	$this->db->select('min(start_date) as min_date,max(end_date) as max_date')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id')->join('departments c','a.department_id=c.department_id')->where('a.df_id',$df_id);
		if($department<>'')
		{
		$this->db->where('a.department_id',$department);
		}
	$rty=$this->db->get();
	if($rty->num_rows()>0)
	{
		foreach($rty->result() as $row);
		$data[]=$row->min_date;
		$data[]=$row->max_date;

	}

	return $data;


}


public function getMondaysInRange($dateFromString, $dateToString)
{
    $dateFrom = new \DateTime($dateFromString);
    $dateTo = new \DateTime($dateToString);
    $dates = [];

    if ($dateFrom > $dateTo) {
        return $dates;
    }

    if (1 != $dateFrom->format('N')) {
        $dateFrom->modify('next monday');
    }

    while ($dateFrom <= $dateTo) {
        $dates[] = $dateFrom->format('Y-m-d');
        $dateFrom->modify('+1 week');
    }

    return $dates;
}

function paymentfollowupnotification(){

$q = $this->db->select('a.next_followup, a.remarks, c.company_name, c.pono, d.first_name, d.last_name, d.email, d.contact_number, d.title, e.df_no, d.department_id')->from('payment_followup a')->join('task_department_wise_scheduling b','a.record_id=b.id')->join('poreceived c','b.po_id=c.id')->join('df_release e','b.df_id=e.id')->join('system_users d','a.added_by=d.user_id')->where('next_followup',date('Y-m-d'))->get();
if($q->num_rows()>0){
foreach($q->result() as $row);
	$personname = $row->title." ".$row->first_name." ".$row->last_name;
	$personcontactno = $row->contact_number;
	$personemailid = $row->email;


$ms = 'Hello *'.$personname.',* 
Greetings of the day! 💐

Just a quick heads-up that your customer follow-up is scheduled for today.

Kindly ensure to take the necessary action.

Customer details are as followed. 

* Customer Name: *'.$row->company_name.'*
* DF No. *'.$row->df_no.'*
* Last Follow-up Remarks: *'.ucwords(strtolower($row->remarks)).'*

 Thank You!';


/*Get HOD Contact Detail*/
$q5 = $this->db->select('b.first_name, b.last_name, b.email, contact_number')->from('prestogroup_teams a')->join('system_users b','a.team_leader=b.user_id')->where('a.department_id',$row->department_id)->get();
if($q5->num_rows()>0){
	foreach($q5->result() as $hod);
	$hodemail = $hod->email;
	$hodcontactno = $hod->contact_number;
}else{
	$hodemail = "";
	$hodcontactno = "";
}

/*Get HOD Contact Detail*/

					
					/*WhatsApp API*/
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					//'receiverMobileNo' => '919718991797',
					'receiverMobileNo' => '91'.$personcontactno.",".$hodcontactno,
					'username' => whatsappuser1,
					'password' => whatsapppass1,
					'message'=>strip_tags($ms));

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
						//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					/* end */

					$mailmessage = "Dear <br>".$personname."just a quick heads-up that your customer follow-up is scheduled for today. Kindly ensure to take the necessary action.<br><br>Customer details are as followed.";

					$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">			
					<tr>				
						<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://gamavis.com/dynachem/assets/images/shubhampack.png" width="200px;" alt="Shubham Flexible Packaging Machines Pvt. Ltd" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br> '.$mailmessage.'</td>
					  </tr> 
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>Customer Name: '.$row->company_name.'</strong><br></td>
					  </tr> 
					  <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>DF No.: '.$row->df_no.'</strong><br></td>
					  </tr> 

					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>Last Remarks: '.$row->remarks.'</strong><br></td>
					  </tr> 
					 
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					 
					 
					<tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;;">Best Regards, <br>
						Shubham Pack Team
						</td>
					  </tr>
					 <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td>
					   </td>
					  </tr>
					</table>
					</td>
				  </tr>
				</table>';
					$subjectname = "Payment Follow-up Scheduled Notification -  ".$row->company_name;
					$this->email->set_mailtype("html");
					$this->email->to($email);
					//$this->email->to('mangleshup@gmail.com');
					//$this->email->cc($hodemail);
					//$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('taskmanagement@shubhampack.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();	

}

}

function checkiftaskispaymentstage($id){

	$q2 = $this->db->select('a.id, b.task_name')->from('task_department_wise_scheduling a')->join('task_management b','a.taskid=b.task_id')->where('a.id',$id)->where('a.paymentstage',1)->get();
	if($q2->num_rows()>0){
		foreach($q2->result() as $record);
	$q = $this->db->select('c.company_name, c.pono, d.first_name, d.last_name, d.email, d.contact_number, d.title, e.df_no, d.department_id')->from('task_department_wise_scheduling a')->join('poreceived c','a.po_id=c.id')->join('df_release e','b.df_id=e.id')->join('system_users d','a.added_by=d.user_id')->where('a.next_followup',date('Y-m-d'))->get();
if($q->num_rows()>0){
foreach($q->result() as $row);
	$personname = $row->title." ".$row->first_name." ".$row->last_name;
	$personcontactno = $row->contact_number;
	$personemailid = $row->email;
	$taskname = $record->task_name;
	$completedby = $record->title." ".$record->first_name." ".$record->last_name;

$ms = 'Dear *'.$personname.',*

Greetings of the Day! 💐

I hope you are doing well today ☺️

I wanted to inform you that the scheduled task for the payment '.ucwords(strtolower($taskname)).' has been completed. Kindly follow up with the customer and update the progress in your *PMS Panel*.

If you have any queries feel free to ask the respective person *'.$completedby.'* 

Best Regards, 
Shubham Flexible Packaging Pvt Ltd.';


/*Get HOD Contact Detail*/
$q5 = $this->db->select('b.first_name, b.last_name, b.email, contact_number')->from('prestogroup_teams a')->join('system_users b','a.team_leader=b.user_id')->where('a.department_id',$row->department_id)->get();
if($q5->num_rows()>0){
	foreach($q5->result() as $hod);
	$hodemail = $hod->email;
	$hodcontactno = $hod->contact_number;
}else{
	$hodemail = "";
	$hodcontactno = "";
}

/*Get HOD Contact Detail*/

					
					/*WhatsApp API*/
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					//'receiverMobileNo' => '919718991797',
					'receiverMobileNo' => '91,'.$personcontactno.",".$hodcontactno,
					'username' => whatsappuser1,
					'password' => whatsapppass1,
					'message'=>strip_tags($ms));

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
						//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					/* end */

					$mailmessage = "Dear <br>".$personname."just a quick heads-up that your customer follow-up is scheduled for today. Kindly ensure to take the necessary action.<br><br>Customer details are as followed.";

					$mailmessage ="Hello Team Member,<br><br>

I hope this email finds you well. <br>I'm writing to inform you that the scheduled task for the payment has been successfully completed. It's now crucial to follow up with the customer and update the progress in your PMS Panel accordingly.<br><br>

Thank you for your attention to this matter.";

					$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">			
					<tr>				
						<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://gamavis.com/dynachem/assets/images/shubhampack.png" width="200px;" alt="Shubham Flexible Packaging Machines Pvt. Ltd" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br> '.$mailmessage.'</td>
					  </tr> 
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>Customer Name: '.$row->company_name.'</strong><br></td>
					  </tr> 
					  <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>DF No.: '.$row->df_no.'</strong><br></td>
					  </tr> 
 
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					 
					 
					<tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;;">Best Regards, <br>
						Shubham Pack Team
						</td>
					  </tr>
					 <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td>
					   </td>
					  </tr>
					</table>
					</td>
				  </tr>
				</table>';
					$subjectname = "Task Defined as Payment milestone has been completed -  ".$row->company_name;
					$this->email->set_mailtype("html");
					$this->email->to($email);
					$this->email->to('mangleshup@gmail.com');
					$this->email->cc($hodemail);
					//$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('taskmanagement@shubhampack.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();	

}
}
}


function GetallDates($strDateFrom,$strDateTo)
{
    // takes two dates formatted as YYYY-MM-DD and creates an
    // inclusive array of the dates between the from and to dates.

    // could test validity of dates here but I'm already doing
    // that in the main script

    $aryRange = [];
   if($strDateFrom<>'')
   {
    $iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
    $iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));

    if ($iDateTo >= $iDateFrom) {
        array_push($aryRange, date('Y-m-d', $iDateFrom)); // first entry
        while ($iDateFrom<$iDateTo) {
            $iDateFrom += 86400; // add 24 hours
            array_push($aryRange, date('Y-m-d', $iDateFrom));
        }
    }
	}
    return $aryRange;
}

function getDaysCountForEachMonth($start_date, $end_date) {
    // Convert string dates to DateTime objects
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    
    // Initialize an array to store days count for each month
    $days_count_per_month = array();
    
    // Check if start date is not the first day of the month
    if ($start->format('j') != '1') {
        // Get the year and month for the start date
        $year = $start->format('Y');
        $month = $start->format('m');
        
        // Calculate the number of days remaining in the start month
        $days_in_start_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $remaining_days = $days_in_start_month - ($start->format('j') - 1);
        
        // Store the days count for the start month
        $days_count_per_month[$start->format('F Y')] = $remaining_days;
        
        // Move to the next month
        $start->modify('first day of next month');
    }
    
    // Loop through each month between start and end dates
    while ($start <= $end) {
        // Get the year and month for the current date
        $year = $start->format('Y');
        $month = $start->format('m');
        
        // Check if the current month is the end month
        if ($start->format('Y-m') === $end->format('Y-m')) {
            // Calculate the number of days elapsed in the end month
            $days_elapsed = $end->format('j');
            
            // Store the days count for the end month
            $days_count_per_month[$start->format('F Y')] = $days_elapsed;
            
            // No need to continue iteration
            break;
        }
        
        // Get the number of days in the current month
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        
        // Store the days count for the current month
        $days_count_per_month[$start->format('F Y')] = $days_in_month;
        
        // Move to the next month
        $start->modify('first day of next month');
    }
    
    return $days_count_per_month;
}

function getDFTaskScheduledold($df_id,$department)
{
	$data=array();

	$this->db->select('a.assigned_user,b.task_id,b.task_name,a.department_id,c.department,a.start_date,a.end_date')->from('task_department_wise_scheduling a')->join('task_management b','a.taskid=b.task_id')->join('departments c','a.department_id=c.department_id')->where('a.df_id',$df_id)->where('b.task_frequency!=',2)->where('b.task_id!=',1)->order_by('sortorder','ASC');
	if($department<>'')
	{
		$this->db->where('c.department_id',$department);
	}
		$rty=$this->db->get();
		if($rty->num_rows()>0)
		{
		foreach($rty->result() as $row)
		{
			$data[]=array('task_id'=>$row->task_id,'task_name'=>$row->task_name,'department_id'=>$row->department_id,'department'=>$row->department,'start_date'=>$row->start_date,'end_date'=>$row->end_date,'assignedto'=>$row->assigned_user);
		}
		}

		$keys = array_column($data, 'start_date');

		// Sort the $fruits array based on the values in $keys
		array_multisort($keys, SORT_ASC, $data);

		return $data;
}

function getDFTaskScheduled($df_id,$department)
{
	$data=array();
	if($department<>''){
		 $data = array();
    $this->db->select('a.assigned_user, b.task_id, b.task_name, a.department_id, c.department, a.start_date, a.end_date, b.department_task_wise_order')
        ->from('task_department_wise_scheduling a')
        ->join('task_management b', 'a.taskid=b.task_id')
        ->join('departments c', 'a.department_id=c.department_id')
        ->where('a.df_id', $df_id)
       
        ->where('b.task_frequency !=', 2)
        ->where('b.task_id !=', 1);

    if ($department <> '') {
        $this->db->where('c.department_id', $department);
    }
    
    // Retrieve results
    $rty = $this->db->get();
    if ($rty->num_rows() > 0) {
        foreach ($rty->result() as $row) {
            $data[] = array(
                'task_id' => $row->task_id,
                'task_name' => $row->task_name,
                'department_id' => $row->department_id,
                'department' => $row->department,
                'start_date' => $row->start_date,
                'end_date' => $row->end_date,
                'assignedto' => $row->assigned_user,
                'department_task_wise_order' => $row->department_task_wise_order
            );
        }

        // Extract start_date and department_task_wise_order for sorting
        $start_dates = array_column($data, 'start_date');
        $orders = array_column($data, 'department_task_wise_order');

        // Sort the $data array by start_date and then department_task_wise_order
        array_multisort($start_dates, SORT_ASC, $orders, SORT_ASC, $data);
    }

    return $data;
	}else{
		$this->db->select('a.assigned_user,b.task_id,b.task_name,a.department_id,c.department,a.start_date,a.end_date')->from('task_department_wise_scheduling a')->join('task_management b','a.taskid=b.task_id')->join('departments c','a.department_id=c.department_id')->where('a.df_id',$df_id)->where('b.task_frequency!=',2)->where('b.task_id!=',1)->order_by('department_task_wise_order','ASC');
			//$this->db->where('b.task_id',62);
	if($department<>'')
	{
		$this->db->where('c.department_id',$department);
	}
		$rty=$this->db->get();
		if($rty->num_rows()>0)
		{
		foreach($rty->result() as $row)
		{
			$data[]=array('task_id'=>$row->task_id,'task_name'=>$row->task_name,'department_id'=>$row->department_id,'department'=>$row->department,'start_date'=>$row->start_date,'end_date'=>$row->end_date,'assignedto'=>$row->assigned_user);
		}
		}

		$keys = array_column($data, 'start_date');

		// Sort the $fruits array based on the values in $keys
		array_multisort($keys, SORT_ASC, $data);

		return $data;
	}
	
	
}


function checkDateInBetween($date_to_check, $start_date, $end_date) {
    // Convert string dates to DateTime objects
    //echo $date_to_check."<br/>".$start_date."<br/>".$end_date; exit;
    $date = new DateTime($date_to_check);
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);

    // Check if the date lies between the start and end dates (inclusive)
    return $date >= $start && $date <= $end;
}



    public function is_date_in_week($monday_date, $check_date) {
        // Ensure the dates are in the correct format
        $monday = strtotime($monday_date);
        $date_to_check = strtotime($check_date);

        // Check if the given Monday is actually a Monday
        if (date('N', $monday) != 1) {
            return false; // Not a Monday
        }

        // Calculate the start and end of the week
        $week_start = strtotime('monday this week', $monday);
        $week_end = strtotime('sunday this week', $monday);
        // Check if the date falls within the week
        if($date_to_check >= $week_start && $date_to_check <= $week_end)
        {
        	return true;
        }else
        {
        	return false;
        }
    }

function checkifholiday($date)
{
	$q1 = $this->db->select('holiday_id')->from('prestogroup_holidays')->where('holiday_date',$date)->get();

	return $q1->num_rows();

}

 public function SKIPsingle_holidays($expecteddate) {
     $date = $this->checkifholiday($expecteddate);
     if($date>0){
     	$expecteddate = date('Y-m-d',strtotime($expecteddate.' +1 Days'));
     	 $date = $this->checkifholiday($expecteddate);
     	}else{
     		return $expecteddate;
     	}
    }

    function triggernotificationondfmeetingalert($dfn, $dfdetail, $enddate, $dfid){
		

		$q = $this->db->select('task_message, department_id, taskid')->from('task_related_messages')->where('taskid',4)->get();
		if($q->num_rows()>0){

			foreach($q->result() as $checkmsg){

		/*Trigger Notification of DF Meeting Notification*/

		$this->db->select('department_id')->from('task_management')->where('status',1);
		if($checkmsg->department_id==0){

		}else{
			$this->db->where('department_id',$checkmsg->department_id);
		}
		$q = $this->db->group_by('department_id')->get();
		//echo "<pre>"; print_r($q->result()); exit;
		foreach($q->result() as $dfnotification){

				$dfmessage=  $checkmsg->task_message;
				$q = $this->db->select('a.department_id, b.title, b.first_name, b.last_name, b.user_id, b.contact_number, b.email, c.department')->from('prestogroup_teams a')->join('system_users b','a.team_leader=b.user_id','left')->join('departments c','a.department_id=c.department_id','left')->where('a.department_id',$dfnotification->department_id)->get();

				if($q->num_rows()>0){
					foreach($q->result() as $sendmessage);
					$name = ucfirst($sendmessage->title)." ".ucfirst($sendmessage->first_name)." ".ucfirst($sendmessage->last_name);
					$mobileno = $sendmessage->contact_number;
					$contactnumber = $sendmessage->contact_number;
					//$emailid = $row2->email;
					//$contactno = "9718991797";
					$emailid = $sendmessage->email;
					$departmentname = $sendmessage->department;
					$find = array('{df_number}','{department_name}', '{department_hod}');
					$replace = array(ucwords(strtolower($dfn)), ucwords(strtolower($departmentname)), ucwords(strtolower($name)));
					$message_body = str_replace($find, $replace, $dfmessage);
					//echo $message_body; exit;

$whatsappmessagedefined = '📢 *Notification:*
Hi '.$name.',

This is just a quick reminder that the Weekly Meeting for '.$dfdetail.' is scheduled for today.

Please make sure to plan your day accordingly and be prepared for the meeting. *Thank you!* ☺️🙏🏻';


					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					//'receiverMobileNo' => '919718991797',
					'receiverMobileNo' => '91'.$contactnumber,
					'username' => whatsappuser1,
					'password' => whatsapppass1,
					'message'=>strip_tags($whatsappmessagedefined));

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
						//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					
					/* end */

					$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
				   <tr>
					<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://shubhampack.com/wp-content/uploads/2021/05/Logo.png" width="200px;" alt="Shubham Flexible Packaging Machines Pvt. Ltd. " /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>DF Meeting is scheduled for today! </strong><br><br></td>
					  </tr> 
					 
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;">'.$message_body.' 

						</td>
					  </tr>
					  
				
					  <tr>
						<td>
					   </td>
					  </tr>
					</table>
					</td>
				  </tr>
				</table>';
				$subjectname = "DF Weekly Meeting Reminder";
					$this->email->set_mailtype("html");
					//$this->email->to('mangleshup@gmail.com');
					$this->email->to($emailid);
					//$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('taskmanagement@shubhampack.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();

    				$data = array('department_id'=>$sendmessage->department_id,
    					'user_id'=>$sendmessage->user_id,
    					'df_id'=>$dfid,
    					'message'=>$message_body,
    					'added_on'=>date('Y-m-d H:i:s'),
    					'status'=>0);

    				$this->db->insert('task_intimation_alert',$data);


				}



		}
		
	}
		/*Trigegr Notification of DF Release*/
	}
}

public function yourtodaysduetaskreminderpdf($userid)
{
    // Fetch user details
    $q = $this->db->select('title, first_name, last_name')
                  ->from('system_users')
                  ->where('user_id', $userid)
                  ->get();

    if ($q->num_rows() === 0) {
        // User not found
        return false;
    }

    foreach ($q->result() as $us);
    $name = $us->title . " " . $us->first_name . " " . $us->last_name;

    $this->load->library('Pdf');
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('SHUBHAM PACK');
    $pdf->SetTitle("YOUR TODAYS SCHEDULED TASK");
    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);
    $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
    $pdf->setFontSubsetting(true);
    $pdf->SetFont('pdfahelvetica', '', 12, '', true);
    $pdf->AddPage();

    $html = '<style>li span { font-weight: bold; }
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid black; padding: 8px; text-align: center; font-size: 10px; }
            th { background-color: lightgrey; font-size: 12px; }
        </style>';

    $html .= '<table width="100%">
                <tr>
                    <td style="text-align:center;">
                        <img src="https://shubhampack.com/wp-content/uploads/2021/05/Logo.png" style="width:150px">
                    </td>
                </tr>
              </table>';

    // Fetch today's tasks
    $this->db->select('a.assigned_user, a.start_date, a.end_date, c.task_name, b.df_no, a.remarks')
             ->from('task_department_wise_scheduling a')
             ->join('df_release b', 'a.df_id = b.id', 'left')
             ->join('task_management c', 'a.taskid = c.task_id', 'left')
             ->where('a.task_status', 0)
             ->where('a.end_date', date('Y-m-d'))
             ->where('a.assigned_user', $userid);
    $todayQuery = $this->db->get();

    // Fetch overdue tasks
    $this->db->select('a.assigned_user, a.start_date, a.end_date, c.task_name, b.df_no, a.remarks')
             ->from('task_department_wise_scheduling a')
             ->join('df_release b', 'a.df_id = b.id', 'left')
             ->join('task_management c', 'a.taskid = c.task_id', 'left')
             ->where('a.task_status', 0)
             ->where('a.end_date <', date('Y-m-d'))
             ->where('a.assigned_user', $userid);
    $overdueQuery = $this->db->get();

    if ($todayQuery->num_rows() === 0 && $overdueQuery->num_rows() === 0) {
        // No tasks for this user (both today and overdue)
        return false;
    }

    // Today's Tasks Table
    $html .= '<h3 style="text-align:center;">HELLO ' . strtoupper($name) . ' YOUR TODAYS SCHEDULED TASKS ARE HERE!</h3>';
    $html .= '<table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>DF DETAIL</th>
                        <th>TASK NAME</th>
                        <th>START DATE</th>
                        <th>DUE DATE</th>
                        <th>LAST REMARKS</th>
                    </tr>
                </thead>
                <tbody>';

    if ($todayQuery->num_rows() > 0) {
        $m = 1;
        foreach ($todayQuery->result() as $row) {
            $html .= '<tr>
                        <td>' . $m++ . '</td>
                        <td>' . strtoupper($row->df_no) . '</td>
                        <td>' . strtoupper($row->task_name) . '</td>
                        <td>' . date('d-m-Y', strtotime($row->start_date)) . '</td>
                        <td>' . date('d-m-Y', strtotime($row->end_date)) . '</td>
                        <td>' . $row->remarks . '</td>
                      </tr>';
        }
    } else {
        $html .= '<tr><td colspan="6" style="text-align:center;">HURRAY! NO TASKS FOR TODAY!</td></tr>';
    }
    $html .= '</tbody></table><br><br>';

    // Overdue Tasks Table
    $html .= '<h3 style="text-align:center;">HELLO ' . strtoupper($name) . ' YOUR OVERDUE TASKS ARE HERE!</h3>';
    $html .= '<table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>DF DETAIL</th>
                        <th>TASK NAME</th>
                        <th>START DATE</th>
                        <th>DUE DATE</th>
                        <th>LAST REMARKS</th>
                        <th>DELAY IN DAYS</th>
                    </tr>
                </thead>
                <tbody>';

    if ($overdueQuery->num_rows() > 0) {
        $m = 1;
        foreach ($overdueQuery->result() as $row) {
            $date1 = new DateTime($row->end_date);
            $date2 = new DateTime(date('Y-m-d'));
            $delayDays = $date1->diff($date2)->format('%a days');

            $html .= '<tr>
                        <td>' . $m++ . '</td>
                        <td>' . strtoupper($row->df_no) . '</td>
                        <td>' . strtoupper($row->task_name) . '</td>
                        <td>' . date('d-m-Y', strtotime($row->start_date)) . '</td>
                        <td>' . date('d-m-Y', strtotime($row->end_date)) . '</td>
                        <td>' . $row->remarks . '</td>
                        <td>' . $delayDays . '</td>
                      </tr>';
        }
    } else {
        $html .= '<tr><td colspan="7" style="text-align:center;">HURRAY! NO OVERDUE TASKS!</td></tr>';
    }
    $html .= '</tbody></table>';

    // Generate and save the PDF
    $fullname = str_replace(' ', '-', ucwords(strtolower($name)));
    $filelocation = SITE_ROOT . 'image_bank/daily_reports/';
    $fileNL = $filelocation . $fullname . "_" . $userid . "_Today_Scheduled_Task_" . date('Y-m-d') . ".pdf";

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output($fileNL, 'F');

    // Return true to indicate PDF generation
    return true;
}


public function overduetaskreportsuserwise($userid)
{
    $userid = (int) $userid;
    if ($userid <= 0) {
        return false;
    }

    $user = $this->db->select('title, first_name, last_name')
        ->from('system_users')
        ->where('user_id', $userid)
        ->get()
        ->row();

    if (!$user) {
        return false;
    }

    $query = $this->db->select('a.assigned_user, a.start_date, a.end_date, a.id, a.taskid, c.task_name, d.df_no, d.added_on, a.remarks')
        ->from('task_department_wise_scheduling a')
        ->join('task_management c', 'a.taskid = c.task_id', 'left')
        ->join('df_release d', 'a.df_id = d.id', 'left')
        ->where('a.task_status', 0)
        ->where('a.on_hold', 0)
        ->where('a.end_date <', date('Y-m-d'))
        ->where('a.assigned_user', $userid)
        ->group_start()
            ->where('d.df_status', 0)
            ->or_where('d.df_status', 'running')
            ->or_where('d.df_status IS NULL', null, false)
        ->group_end()
        ->order_by('a.end_date', 'ASC')
        ->order_by('d.added_on', 'ASC')
        ->get();

    if ($query->num_rows() === 0) {
        return false;
    }

    $full_name = trim($user->title . ' ' . $user->first_name . ' ' . $user->last_name);
    if ($full_name === '') {
        $full_name = trim($user->first_name . ' ' . $user->last_name);
    }
    if ($full_name === '') {
        $full_name = 'Team Member';
    }

    $safe_name = preg_replace('/[^A-Za-z0-9\-]+/', '-', trim($user->first_name . '-' . $user->last_name));
    $safe_name = trim((string) $safe_name, '-');
    if ($safe_name === '') {
        $safe_name = 'user-' . $userid;
    }

    $tasks = [];
    $max_delay_days = 0;
    $oldest_due_date = null;
    $sr_no = 1;

    foreach ($query->result() as $row) {
        $due_date = !empty($row->end_date) ? $row->end_date : date('Y-m-d');
        $delay_days = (int) (new DateTime($due_date))->diff(new DateTime(date('Y-m-d')))->format('%a');
        $max_delay_days = max($max_delay_days, $delay_days);

        if ($oldest_due_date === null || strtotime($due_date) < strtotime($oldest_due_date)) {
            $oldest_due_date = $due_date;
        }

        $tasks[] = [
            'sr_no' => $sr_no++,
            'df_no' => trim((string) $row->df_no) !== '' ? $row->df_no : '-',
            'df_release_date' => !empty($row->added_on) ? date('d M Y', strtotime($row->added_on)) : '-',
            'task_name' => trim((string) $row->task_name) !== '' ? $row->task_name : 'Task Name Not Available',
            'start_date' => !empty($row->start_date) ? date('d M Y', strtotime($row->start_date)) : '-',
            'due_date' => !empty($row->end_date) ? date('d M Y', strtotime($row->end_date)) : '-',
            'remarks' => trim((string) $row->remarks) !== '' ? $row->remarks : 'No remarks updated yet.',
            'delay_days' => $delay_days,
            'delay_label' => $delay_days . ' day' . ($delay_days === 1 ? '' : 's'),
        ];
    }

    require_once FCPATH . 'application/third_party/dompdf/autoload.inc.php';

    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('defaultFont', 'DejaVu Sans');

    $dompdf = new \Dompdf\Dompdf($options);

    $report_data = [
        'employee_name' => ucwords(strtolower($full_name)),
        'report_date' => date('d M Y'),
        'generated_on' => date('d M Y h:i A'),
        'task_count' => count($tasks),
        'max_delay_days' => $max_delay_days,
        'oldest_due_date' => $oldest_due_date ? date('d M Y', strtotime($oldest_due_date)) : '-',
        'tasks' => $tasks,
        'logo_url' => 'https://shubhampack.com/wp-content/uploads/2021/05/Logo.png',
    ];

    $html = $this->load->view('reports/overdue_task_report_pdf', $report_data, true);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $filelocation = SITE_ROOT . 'image_bank/daily_reports/overduetask/';
    if (!is_dir($filelocation)) {
        mkdir($filelocation, 0777, true);
    }

    $fileName = $safe_name . '-' . $userid . '-Overdue-Task-Report-' . date('Y-m-d') . '.pdf';
    $full_file_path = $filelocation . $fileName;

    file_put_contents($full_file_path, $dompdf->output());

    return [
        'attachment_path' => $full_file_path,
        'public_url' => page_url1 . 'image_bank/daily_reports/overduetask/' . $fileName,
        'file_name' => $fileName,
        'employee_name' => $report_data['employee_name'],
        'task_count' => $report_data['task_count'],
        'max_delay_days' => $report_data['max_delay_days'],
        'oldest_due_date' => $report_data['oldest_due_date'],
        'generated_on' => $report_data['generated_on'],
    ];
}


function checkActualDoneStatus($taskid,$dfid)
{
			$data='';
			$q = $this->db->select('task_status,start_date,end_date,task_completed_on,taskid,assigned_on')->from('task_department_wise_scheduling')->where('df_id',$dfid)->where('taskid',$taskid)->where('task_status',1)->get();
			if($q->num_rows()>0)
			{
			foreach($q->result() as $row)
			$data=date('d-M-Y',strtotime($row->task_completed_on));
			}

			return $data;
}

function checkActualStatus($taskid,$dfid)
{
			$data=array();
			$q = $this->db->select('task_status,start_date,end_date,task_completed_on,taskid,assigned_on')->from('task_department_wise_scheduling')->where('df_id',$dfid)->where('taskid',$taskid)->get();
			if($q->num_rows()>0)
			{
			foreach($q->result() as $row)
			$data[]=$row->task_status;
			$data[]=$row->assigned_on;
			$data[]=$row->task_completed_on;
			$data[]=$row->start_date;
			$data[]=$row->end_date;
			}

			return $data;
}

function getallrunningdfByID($id){
	$data=array();
	$q = $this->db->select('c.first_name,c.last_name,a.added_on,a.id,df_no,b.id as poid,b.company_name,b.pono,b.podate,b.po_attachment')->from('df_release a')->join('poreceived b','b.df_id=a.id')->join('system_users c','c.user_id=a.added_by')->where('a.id',$id)->get();
	if($q->num_rows()>0)
	{
	foreach($q->result() as $row);
	$data[]=$row->id;
	$data[]=$row->df_no;
	$data[]=$row->poid;
	$data[]=$row->company_name;
	$data[]=$row->pono;
	$data[]=$row->podate;
	$data[]=$row->po_attachment;
	$data[]=date('d-M-Y',strtotime($row->added_on));
	$data[]=$row->first_name." ".$row->last_name;
	}
	return $data;
}

function textFormatting($data)
{
	return ucfirst(strtolower($data));
}

function workCompleted($dfid,$department)
	{

		
	$this->db->select('a.id')->from('task_department_wise_scheduling a')->join('departments b','a.department_id=b.department_id')->where('df_id',$dfid);

		if($department>0 || $department<>'')
		{
			$this->db->where('b.department_id',$department);
		}
		$q1=$this->db->get();
    $count = $q1->num_rows();


	$this->db->select('a.id')->from('task_department_wise_scheduling a')->join('departments b','a.department_id=b.department_id')->where('df_id',$dfid)->where('task_status',1);

		if($department>0 || $department<>'')
		{
			$this->db->where('b.department_id',$department);
		}

		$q2 = $this->db->get();
		$totaldone = $q2->num_rows();

		if($totaldone>0){
			$percetage =  round($totaldone*100/ $count);
		}else{
			$percetage =  0;
		}

		

		return $percetage;


		}

		function workDelayed($dfid,$department)
		{
			$delaycount=array();
			$delayed=0;
			$delaycount[]=0;
			$totaldayscountarray=array();
			$totaldayscountarray[]=0;
			$totaldone=$this->workCompleted($dfid,$department);
			$this->db->select('a.id,a.task_completed_on,a.end_date')->from('task_department_wise_scheduling a')->join('departments b','a.department_id=b.department_id')->where('df_id',$dfid)->where('task_status',1);

		if($department>0 || $department<>'')
		{
			$this->db->where('b.department_id',$department);
		}

		$q2 = $this->db->get();
		if($q2->num_rows()>0)
		{
		foreach($q2->result() as $rowss){
		if(date('Y-m-d',strtotime($rowss->task_completed_on))>$rowss->end_date){
		$delaycount[] = 1;

		$daysss = $this->Task_model->getDays($rowss->end_date, date('Y-m-d',strtotime($rowss->task_completed_on)),1);
		$totaldayscountarray[] = $daysss;
		}
		}
		}


		$this->db->select('a.id,a.task_completed_on,a.end_date')->from('task_department_wise_scheduling a')->join('departments b','a.department_id=b.department_id')->where('df_id',$dfid)->where('task_status',0);

		if($department>0 || $department<>'')
		{
			$this->db->where('b.department_id',$department);
		}

		$q2 = $this->db->get();
		if($q2->num_rows()>0)
		{
		foreach($q2->result() as $rowss){
		if(date('Y-m-d')>$rowss->end_date){
		$delaycount[] = 1;

		$daysss = $this->Task_model->getDays($rowss->end_date, date('Y-m-d'),1);
		$totaldayscountarray[] = $daysss;
		}
		}
		}

		if(count($totaldayscountarray)>0){
			$maxdays = max($totaldayscountarray);
		}else{
			$maxdays = 0;
		}
		return $maxdays; 

		$totaldays =  $this->gettotaldaysofdfplanned($dfid);
		

		// $delayed = array_sum($delaycount);
		// $totaldaysdelayed = array_sum( $totaldayscountarray);
		// if($totaldone>0)
		// {
		// $totaldelayedpercentage = round($delayed*100/$totaldone);
		// }else
		// {
		// 	$totaldelayedpercentage=0;
		// }

		//return $totaldaysdelayed.'~'.$delayed;


	}

	function gettotaldaysofdfplanned($df_id){
		$interval = 0;
		$q1 = $this->db->select('start_date')->from('task_department_wise_scheduling')->where('df_id',$df_id)->order_by('id','asc')->limit(1)->get();
		if($q1->num_rows()>0){
			foreach($q1->result() as $row1);
			$startdate = $row1->start_date;
		}

		$q2 = $this->db->select('MAX(end_date) as edndate ')->from('task_department_wise_scheduling')->where('df_id',$df_id)->order_by('id','desc')->limit(1)->get();
		if($q2->num_rows()>0){
			foreach($q2->result() as $row2);
			$end_date = $row2->edndate;
		}
		
		$days = $this->countDaysBetweenDates($startdate,$end_date);
		return $days;


	}
	function countDaysBetweenDates($date1, $date2) {
    $startDate = new DateTime($date1);
    $endDate = new DateTime($date2);

    $interval = $startDate->diff($endDate);

    return $interval->days;
}

	function getPlannedEndDate($dfid,$department)
	{
		$this->db->select('MAX(end_date) as enddate')->from('task_department_wise_scheduling a')->join('departments b','a.department_id=b.department_id')->where('df_id',$dfid);

		if($department>0 || $department<>'')
		{
			$this->db->where('b.department_id',$department);
		}
		$q1=$this->db->get();

		if($q1->num_rows()>0){
		foreach($q1->result() as $r);
		$planneddate = date('d-m-Y',strtotime($r->enddate));
		}else{
		$planneddate = '';
		}

		return $planneddate;
	}

	function GetEstimatedWithDelay($dfid,$department)
	{
		$planneddate=$this->getPlannedEndDate($dfid,$department);
		$totaldaysdelayed111=$this->workDelayed($dfid,$department);
		$work_delayed_per12=explode('~',$totaldaysdelayed111);
		$totaldaysdelayed=$work_delayed_per12[0];
    	$expecteddate =  date('d-m-Y',strtotime($planneddate.' +'.$totaldaysdelayed.' Days'));

    	return $expecteddate;

	}


	function getActualDelay($dfid,$department)
	{
		$edate='';
		$rt=$this->db->select('id')->from('task_department_wise_scheduling')->where('task_status',0)->where('df_id',$dfid)->where('department_id',$department)->get();
		if($rt->num_rows()>0)
		{
			return "NA";
		}else
		{
			$this->db->select('MAX(end_date) as enddate')->from('task_department_wise_scheduling a')->join('departments b','a.department_id=b.department_id')->where('df_id',$dfid);
		if($department>0 || $department<>'')
		{
		$this->db->where('b.department_id',$department);
		}
		$q1=$this->db->get();
		if($q1->num_rows()>0)
		{
			foreach($q1->result() as $row);
			$edate=date('d-M-Y',strtotime($row->enddate));
			return $edate;
		}else
		{
			return 'NA';
		}
		}
	}



    public function getMondays($startDate, $endDate) {
        // Convert strings to DateTime objects
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $end->modify('+1 day'); // Include the end date in the range

        // Adjust start date to the nearest previous Monday if it is not already a Monday
        if ($start->format('N') != 1) {
            $start->modify('last Monday');
        }

        $interval = new DateInterval('P1D'); // 1 day interval
        $datePeriod = new DatePeriod($start, $interval, $end);

        $mondays = array();

        foreach ($datePeriod as $date) {
            if ($date->format('N') == 1) { // 1 corresponds to Monday
                $mondays[] = $date->format('Y-m-d');
            }
        }

        return $mondays;
    }


function getDaysCountForEachMonthFromArray($dates) {
    // Initialize an array to store days count for each month
    $days_count_per_month = array();

    foreach ($dates as $date) {
        // Convert the date string to a DateTime object
        $dateTime = new DateTime($date);

        // Extract the month and year in 'YYYY-MM' format
        $monthYear = $dateTime->format('M Y');

        // Initialize the count for this month if not already set
        if (!isset($days_count_per_month[$monthYear])) {
            $days_count_per_month[$monthYear] = 0;
        }

        // Increment the count for the current month
        $days_count_per_month[$monthYear]++;
    }

    return $days_count_per_month;
}

  public function getWeekNumberFromDate($date) {
        $dateTime = new DateTime($date);
        return $dateTime->format('W');
    }


//      public function get_week_numbers_between_dates($start_date, $end_date) {
//     $weeks = array();
//     $start_date = strtotime($start_date);
//     $end_date = strtotime($end_date);

//     if ($start_date === false || $end_date === false) {
//         // Handle invalid date formats
//         return $weeks;
//     }

//     // Get the week number for the start and end dates
//     $start_week_number = date('W', $start_date);
//     $end_week_number = date('W', $end_date);

//     // Add the start week number to the array
//     $weeks[] = $start_week_number;

//     // If start and end dates are in different years, handle the year transition
//     if (date('Y', $start_date) !== date('Y', $end_date)) {
//         $end_week_number += 52; // Add 52 weeks for a year
//     }

//     // Add the week numbers between start and end dates (inclusive)
//     for ($week_number = $start_week_number + 1; $week_number <= $end_week_number; $week_number++) {
//         $weeks[] = $week_number % 52; // Modulo 52 to handle year transitions
//     }

//     // Remove duplicates and sort the array
//     $weeks = array_unique($weeks);
//     //sort($weeks);

//     return $weeks;
// }


public function get_week_numbers_between_dates($start_date, $end_date) {
    // $weeks = array();
    // $start_date = strtotime($start_date);
    // $end_date = strtotime($end_date);

    // if ($start_date === false || $end_date === false) {
    //     // Handle invalid date formats
    //     return $weeks;
    // }

    // // Get the week number for the start and end dates
    // $start_week_number = date('W', $start_date);
    // $end_week_number = date('W', $end_date);

    // // Adjust for week number 0 (last week of the year)
    // if ($start_week_number == 0) $start_week_number = 52;
    // if ($end_week_number == 0) $end_week_number = 52;

    // // Add the start week number to the array
    // $weeks[] = sprintf('%02d', $start_week_number); // Format as 01, 02, etc.

    // // If start and end dates are in different years, handle the year transition
    // if (date('Y', $start_date) !== date('Y', $end_date)) {
    //     $end_week_number += 52; // Add 52 weeks for a year
    // }

    // // Add the week numbers between start and end dates (inclusive)
    // for ($week_number = $start_week_number + 1; $week_number <= $end_week_number; $week_number++) {
    //     $weeks[] = sprintf('%02d', ($week_number - 1) % 52 + 1); // Format as 01, 02, etc.
    // }

    // // Remove duplicates and sort the array
    // $weeks = array_unique($weeks);
    // sort($weeks);

    // return $weeks;


      $weeks = array();
    $start_date = strtotime($start_date);
    $end_date = strtotime($end_date);

    if ($start_date === false || $end_date === false) {
        // Handle invalid date formats
        return $weeks;
    }

    // Get the week number for the start and end dates
    $start_week_number = date('W', $start_date);
    $end_week_number = date('W', $end_date);

    // Add the start week number
    $weeks[] = sprintf('%02d', $start_week_number);

    // If dates span over the year boundary, add week 1 of the new year
    if ($start_week_number > $end_week_number) {
        $weeks[] = '01';
    } elseif ($start_week_number < $end_week_number) {
        // If dates are within the same year and don't span across a year boundary, add all weeks in between
        for ($week = $start_week_number + 1; $week <= $end_week_number; $week++) {
            $weeks[] = sprintf('%02d', $week);
        }
    }

    // Remove duplicates and sort the weeks array
    $weeks = array_unique($weeks);
    sort($weeks);

    return $weeks;
}



public function get_week_numbers_between_datesNew($start_date, $end_date) {
    $weeks = array();
    $start_date = strtotime($start_date);
    $end_date = strtotime($end_date);

    if ($start_date === false || $end_date === false) {
        // Handle invalid date formats
        return $weeks;
    }

    // Get the week number for the start and end dates
    $start_week_number = date('W', $start_date);
    $end_week_number = date('W', $end_date);

    // Add the start week number
    $weeks[] = sprintf('%02d', $start_week_number);

    // If dates span over the year boundary, add week 1 of the new year
    if ($start_week_number > $end_week_number) {
        $weeks[] = '01';
    } elseif ($start_week_number < $end_week_number) {
        // If dates are within the same year and don't span across a year boundary, add all weeks in between
        for ($week = $start_week_number + 1; $week <= $end_week_number; $week++) {
            $weeks[] = sprintf('%02d', $week);
        }
    }

    // Remove duplicates and sort the weeks array
    $weeks = array_unique($weeks);
    sort($weeks);

    return $weeks;
}





      public function get_previous_monday($date) {
        // Convert the input date to a timestamp
        $timestamp = strtotime($date);

        // Check if the given date is a Monday
        if (date('N', $timestamp) == 1) {
            // If the date is Monday, return the same date
            return date('Y-m-d', $timestamp);
        } else {
            // Calculate the previous Monday
            $previous_monday = strtotime('last monday', $timestamp);

            // Format the previous Monday to 'Y-m-d'
            return date('Y-m-d', $previous_monday);
        }
    }

    function createnewticket($selecteddepartmentid, $selecteduserinfo, $dfid, $mastertaskid, $id, $taskremarks){
    
    // --- Start of Added Logic ---
    // If the selected department is 18 (e.g., 'IT Support'),
    // automatically assign the ticket to user 180.
    if ($selecteddepartmentid == 18) {
        $selecteduserinfo = 180;
    }
    // --- End of Added Logic ---

    $user_id = $this->session->userdata['logged_in']['user_id'];
    $dfno = $this->getdfinfo($dfid);
    $ticketraisedby = $this->getuserinformation();
    $taskname = $this->getTaskNameById($mastertaskid);
    $checkdfno = $this->countdfticket($dfid);
    $count1 = $checkdfno + 1;
    $ticketno = $dfno . "-" . $count1;
    
    $data = array(
        'df_id' => $dfid,
        'task_id' => $mastertaskid,
        'task_record_id' => $id,
        'department_id' => $selecteddepartmentid,
        'user_id' => $selecteduserinfo, // This will now be 180 if department was 18
        'help_ticket_no' => $ticketno,
        'remarks' => $taskremarks,
        'added_on' => date('Y-m-d H:i:s'),
        'added_by' => $user_id,
        'pending_for_approval' => 0,
        'ticket_status' => 0
    );
    
    $this->db->insert('communication_ticket_system', $data);
    $ticket_id = $this->db->insert_id();

    if ($ticket_id > 0) {
        $notification_message = $this->buildTicketNotificationMessage(
            'New help ticket assigned',
            $ticketno,
            $dfno,
            $taskname,
            $taskremarks,
            $ticketraisedby
        );

        $this->createTicketNotifications(
            array(
                $selecteduserinfo,
                $this->getTeamLeaderIdByUser($selecteduserinfo)
            ),
            $ticket_id,
            $dfno,
            $notification_message
        );
    }
    
    // The notification will also be sent to the correct user (180 in this case)
    $this->sendticketinformationtouser($selecteduserinfo, $dfid, $taskremarks, $ticketno);
}

    private function getTicketNotificationPreview($text, $limit = 180)
    {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags((string) $text)));

        if ($text === '') {
            return '';
        }

        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            return mb_strlen($text) > $limit ? mb_substr($text, 0, $limit - 3) . '...' : $text;
        }

        return strlen($text) > $limit ? substr($text, 0, $limit - 3) . '...' : $text;
    }

    public function buildTicketNotificationMessage($headline, $ticketno, $dfno, $taskname = '', $remarks = '', $actorname = '')
    {
        $message = '<strong>' . htmlspecialchars((string) $headline, ENT_QUOTES, 'UTF-8') . '</strong>';

        if (!empty($ticketno)) {
            $message .= '<br>Ticket: #' . htmlspecialchars((string) $ticketno, ENT_QUOTES, 'UTF-8');
        }

        if (!empty($dfno)) {
            $message .= '<br>DF: ' . htmlspecialchars((string) $dfno, ENT_QUOTES, 'UTF-8');
        }

        if (!empty($taskname)) {
            $message .= '<br>Task: ' . htmlspecialchars((string) $taskname, ENT_QUOTES, 'UTF-8');
        }

        if (!empty($actorname)) {
            $message .= '<br>By: ' . htmlspecialchars((string) $actorname, ENT_QUOTES, 'UTF-8');
        }

        $remarks_preview = $this->getTicketNotificationPreview($remarks);
        if ($remarks_preview !== '') {
            $message .= '<br>Remark: ' . htmlspecialchars($remarks_preview, ENT_QUOTES, 'UTF-8');
        }

        return $message;
    }

    public function getTeamLeaderIdByUser($userid)
    {
        $userid = (int) $userid;

        if ($userid <= 0) {
            return 0;
        }

        $query = $this->db->select('b.team_leader')
            ->from('presto_team_members a')
            ->join('prestogroup_teams b', 'a.team_id=b.team_id', 'left')
            ->where('a.employee_id', $userid)
            ->limit(1)
            ->get();

        if ($query->num_rows() > 0) {
            return (int) $query->row()->team_leader;
        }

        return 0;
    }

    public function createTicketNotifications($recipient_ids, $ticket_id, $dfno, $message)
    {
        $ticket_id = (int) $ticket_id;
        $message = trim((string) $message);

        if ($ticket_id <= 0 || $message === '') {
            return;
        }

        $recipient_ids = array_values(array_unique(array_filter(array_map('intval', (array) $recipient_ids))));
        if (empty($recipient_ids)) {
            return;
        }

        $created_at = date('Y-m-d H:i:s');

        foreach ($recipient_ids as $recipient_id) {
            $existing = $this->db->select('id')
                ->from('notifications')
                ->where('user_id', $recipient_id)
                ->where('ticket_id', $ticket_id)
                ->where('message', $message)
                ->where('is_read', 0)
                ->limit(1)
                ->get();

            if ($existing->num_rows() > 0) {
                continue;
            }

            $this->db->insert('notifications', array(
                'user_id' => $recipient_id,
                'ticket_id' => $ticket_id,
                'df_no' => $dfno,
                'message' => $message,
                'is_read' => 0,
                'created_at' => $created_at
            ));
        }
    }

    function getdfinfo($dfid){
    	$q = $this->db->select('df_no')->from('df_release')->where('id',$dfid)->get();
    	foreach($q->result() as $row);
    	$dfno = $row->df_no;
    	return $dfno; 

    }

    function getTaskNameById($taskid){
        $taskid = (int) $taskid;
        if ($taskid <= 0) {
            return '';
        }

        $query = $this->db->select('task_name')->from('task_management')->where('task_id', $taskid)->limit(1)->get();
        if ($query->num_rows() > 0) {
            return (string) $query->row()->task_name;
        }

        return '';
    }

    function countdfticket($dfid){
    	$q = $this->db->select('id')->from('communication_ticket_system')->where('df_id',$dfid)->get();
    	$count = $q->num_rows();
    	return $count;
    }

    function getuserinformation(){
		$user_id =$this->session->userdata['logged_in']['user_id'];
		$q = $this->db->select('title, first_name, last_name')->from('system_users')->where('user_id',$user_id)->get();
    	foreach($q->result() as $row);
    	$name = ucwords(strtolower($row->title)." ".strtolower($row->first_name)." ".strtolower($row->last_name));
    	return $name;

    }

    function sendticketinformationtouser($selectedperson, $dfid, $taskremarks, $ticketno){
    	$ticketraisedby = $this->getuserinformation();
    	$ticketcreatedon = date('d-m-Y h:i A');
    	$dfno = $this->getdfinfo($dfid);
    	$q = $this->db->select('contact_number')->from('system_users')->where('user_id',$selectedperson)->get();
    	if($q->num_rows()>0){
    		foreach($q->result() as $row);

    		$message = '📢 New Help Ticket Alert!

A new help ticket has been raised for '.$dfno.'. 🛠️ Kindly take the desired action and close the ticket from your PMS panel. ✔️

Ticket No: '.$ticketno.'
Remark: '.$taskremarks.'
Ticket Raised by: '.$ticketraisedby.'
Ticket Created On: '.$ticketcreatedon.'

Thank you! 😊

Regards,
Shubham Pack Team';
//echo $message; exit;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
$post = array(
//'receiverMobileNo' => '91'.$contactnumber,
'receiverMobileNo' => '91'.$row->contact_number,
'username' => whatsappuser1,
'password' => whatsapppass1,
'message'=>strip_tags($message));

curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
$result = curl_exec($ch);
//echo $result; exit;
if (curl_errno($ch)) {
echo 'Error:' . curl_error($ch);
}
curl_close($ch);


    	}
    }

  public function getDepartmentHeadData($selectedDepartmentId) {
    $query = $this->db
        ->select('b.first_name, b.last_name, b.email, b.contact_number')
        ->from('prestogroup_teams a')
        ->join('system_users b', 'a.team_leader = b.user_id', 'left')
        ->where('a.department_id', $selectedDepartmentId)
        ->get();

    return $query->row(); // Return a single object
}

public function sendTicketInformationToDepartmentHead($selectedPerson, $dfid, $taskRemarks, $ticketNo, $selectedDepartmentId) {
    $departmentHeadData = $this->getDepartmentHeadData($selectedDepartmentId);

    if (!$departmentHeadData) {
        log_message('error', 'No department head found for department ID: ' . $selectedDepartmentId);
        return false;
    }

    $ticketRaisedBy = $this->getUserInformation();
    $ticketCreatedOn = date('d-m-Y h:i A');
    $dfNo = $this->getDfInfo($dfid);

    $message = "📢 New Help Ticket Alert!

A new help ticket has been raised for {$dfNo}. 🛠️ Kindly take the desired action and close the ticket from your PMS panel. ✔️

Ticket No: {$ticketNo}
Remark: {$taskRemarks}
Ticket Raised by: {$ticketRaisedBy}
Ticket Created On: {$ticketCreatedOn}

Thank you! 😊

Regards,
Shubham Pack Team";

    // Send Email
    $this->load->library('email');
    $this->email->from('noreply@yourdomain.com', 'Your Company');
    $this->email->to($departmentHeadData->email);
    $this->email->subject('New Help Ticket Raised: ' . $ticketNo);
    $this->email->message(strip_tags($message));

    if (!$this->email->send()) {
        log_message('error', 'Email could not be sent to ' . $departmentHeadData->email);
    }

    // Send WhatsApp Message
    if (!empty($departmentHeadData->contact_number)) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        $postData = [
            'receiverMobileNo' => '91' . $departmentHeadData->contact_number,
            'username' => 'whatsappuser1',
            'password' => 'whatsapppass1',
            'message' => strip_tags($message),
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            log_message('error', 'WhatsApp message failed: ' . curl_error($ch));
        } else {
            log_message('info', 'WhatsApp message sent successfully to ' . $departmentHeadData->contact_number);
        }

        curl_close($ch);
    } else {
        log_message('error', 'No contact number available for department head.');
    }

    return true;
}


function checkhelpticket($id, $dfid){
	$q = $this->db->select('id')->from('communication_ticket_system')->where('task_record_id',$id)->where('df_id',$dfid)->get();
	$count = $q->num_rows();
	return $count;
}


function getDFTaskScheduledDepartmentWise($df_id,$department)
{
	$data=array();
	$this->db->select('c.sort_order,c.department_id,c.department')->from('departments c')->where('c.sort_order>',0)->order_by('c.sort_order','ASC');
	// if($department<>'')
	// {
	// 	$this->db->where('c.department_id',$department);
	// }
		$rty=$this->db->get();
		if($rty->num_rows()>0)
		{
		foreach($rty->result() as $row)
		{

			$datees=$this->GetDepartmentWiseTaskTime($row->department_id,$df_id);
			if(count($datees)>0)
			{
				$st=$datees[0];
				$et=$datees[1];
			}else
			{
				$st='';
				$et='';
			}

			$data[]=array('task_id'=>$row->department_id,'task_name'=>$row->department,'department_id'=>$row->department_id,'department'=>$row->department,'start_date'=>$st,'end_date'=>$et,'sort_order'=>$row->sort_order);
		}
		}

		$keys = array_column($data, 'sort_order');

		// Sort the $fruits array based on the values in $keys
		array_multisort($keys, SORT_ASC, $data);

		return $data;
}

function GetDepartmentWiseTaskTime($departmentID,$df_id)
{
	$data=array();
	$rty=$this->db->select('min(start_date) as start_date,max(end_date) as end_date')->from('task_department_wise_scheduling a')->join('task_management c','c.task_id=a.taskid')->join('departments b','a.department_id=b.department_id')->where('a.department_id',$departmentID)->where('df_id',$df_id)->get();
	//->where('c.critical_to_sharmaji',1)
	if($rty->num_rows()>0)
	{
		$t=1;
		foreach($rty->result() as $row)
		{
			$data[]=$row->start_date;
			$data[]=$row->end_date;
			$t++;
		}

	}

	return $data;
}


function getdepartmentoftaskBYID($taskid){
	$q = $this->db->select('department')->from('task_management a')->join('departments b','b.department_id=a.department_id')->where('a.task_id',$taskid)->get();
	if($q->num_rows()>0){
		foreach($q->result() as $row);
		return $row->department;
	}
}

function checkwhenWorkStarted_n_Completed($department_id,$df_id)
{
	$data=array();
	$rest=$this->db->select('min(task_completed_on) as startdate,max(task_completed_on) as enddate')->from('task_department_wise_scheduling')->where('df_id',$df_id)->where('department_id',$department_id)->where('task_status',1)->get();
	if($rest->num_rows()>0)
	{
		foreach($rest->result() as $row);
		$data[]=$row->startdate;
		$data[]=$row->enddate;
	}

	return $data;

}

function getActualStart_End_Date($department_id,$df_id)
{
	$data=array();

	$rest=$this->db->select('min(task_completed_on) as startdate,max(task_completed_on) as enddate')->from('task_department_wise_scheduling')->where('df_id',$df_id)->where('department_id',$department_id)->where('task_status',1)->get();
	if($rest->num_rows()>0)
	{
		foreach($rest->result() as $row);
		$alltask=$this->CheckallTaskaredone($department_id,$df_id);
		$data[]=$row->startdate;
		if(count($alltask)>0)
		{
			$data[]=$alltask[0];
			$data[]=$alltask[1];
			$data[]=$alltask[2];
		}else
		{
			$data[]=0;
			$data[]='';
			$data[]=0;
		}
	}

	return $data;
}

function CheckallTaskaredone($department_id,$df_id)
{
	$data=array();
	$rest=$this->db->select('a.id')->from('task_department_wise_scheduling a')->join('task_management b','a.taskid=b.task_id')->where('a.df_id',$df_id)->where('b.department_id',$department_id)->get();

	$rest1=$this->db->select('id')->from('task_department_wise_scheduling a')->join('task_management b','a.taskid=b.task_id')->where('a.df_id',$df_id)->where('b.department_id',$department_id)->where('a.task_status',1)->get();

	if($rest->num_rows()==$rest1->num_rows())
	{
		$data[]=1;
		$rest2=$this->db->select('max(task_completed_on) as enddate')->from('task_department_wise_scheduling')->where('df_id',$df_id)->where('department_id',$department_id)->where('task_status',1)->get();
		if($rest2->num_rows()>0)
		{
		foreach($rest2->result() as $rrow);
		$data[]=$rrow->enddate;
		}

		

	}else
	{
		$data[]=0;
		$data[]='';
	}


	if($rest->num_rows()>0)
		{
		$data[]=round(($rest1->num_rows()*100)/($rest->num_rows()));
		}else
		{
			$data[]=0;
		}


	return $data;
}


 // public function getDateDiffInDays($date1, $date2) {
 //        // Convert the dates to DateTime objects
 //        $datetime1 = new DateTime($date1);
 //        $datetime2 = new DateTime($date2);

 //        // Calculate the difference between the two dates
 //        $interval = $datetime1->diff($datetime2);

 //        // Return the difference in days
 //        return $interval->days;
 //    }

public function getDateDiffInDays($date1, $date2) {

		$holidays=$this->checkholiday();
		//echo "<pre>"; print_r($holidays); exit;
        $datetime1 = new DateTime($date1);
        $datetime2 = new DateTime($date2);
        
        // Ensure $date1 is the earlier date
        if ($datetime1 > $datetime2) {
            $temp = $datetime1;
            $datetime1 = $datetime2;
            $datetime2 = $temp;
        }

        $interval = $datetime1->diff($datetime2);
        $days = $interval->days;

        $nonWorkingDays = 0;
        
        // Iterate through each day between the two dates
        for ($i = 0; $i <= $days; $i++) {
            $currentDate = clone $datetime1;
            $currentDate->modify("+$i day");

            // Check if it's a weekend (Saturday or Sunday)
            // if ($currentDate->format('N') >= 6) {
            //     $nonWorkingDays++;
            // }

            // Check if it's a holiday
            if (in_array($currentDate->format('Y-m-d'), $holidays)) {
                $nonWorkingDays++;
            }
        }

       //echo $nonWorkingDays; exit;
        // Subtract non-working days (weekends + holidays) from total days
        return $days - $nonWorkingDays;
    }

    function getusername($id)
    {
    	$name='';
    	$rest=$this->db->select('first_name,last_name')->from('system_users')->where('user_id',$id)->get();
    	if($rest->num_rows()>0)
    	{
    		foreach($rest->result() as $row);
    		$name=ucwords(strtolower($row->first_name." ".$row->last_name));
    	}

    	return $name;
    }


    function ticketsbydfandtask($dfid,$taskid)
    {
    	$data=$this->db->select('id')->from('communication_ticket_system')->where('df_id',$dfid)->where('task_id',$taskid)->get();
    		return $data->num_rows();
    }


     function ticketsbydfandtaskDetail($dfid,$taskid)
    {
    	$details=array();
    	$data=$this->db->select('id,task_record_id,user_id,help_ticket_no,remarks,ticket_status,added_on,added_by,updated_remarks,updated_on,updated_by,ticket_closed_by,ticket_closed_on')->from('communication_ticket_system')->where('df_id',$dfid)->where('task_id',$taskid)->get();
    	if($data->num_rows()>0)
    	{
    		foreach($data->result() as $row)
    		{
    			if($row->updated_on<>'0000-00-00 00:00:00')
    			{
    				$u=date('d-M-Y H:i',(strtotime($row->updated_on)));
    			}else
    			{
    				$u='';
    			}


    			if($row->ticket_status==1)
    			{
    				$clo=date('d-M-Y H:i',strtotime($row->ticket_closed_on));

    				$timeTaken=$this->getDateDiffInDays(date('Y-m-d',strtotime($row->added_on)),date('Y-m-d',strtotime($row->ticket_closed_on)));

    			}else
    			{
    				$clo='';
    				$timeTaken=$this->getDateDiffInDays(date('Y-m-d',strtotime($row->added_on)),date('Y-m-d'));
    			}
    			$assignedTo=ucwords(strtolower($this->getusername($row->user_id)));
    			$assignBy=ucwords(strtolower($this->getusername($row->added_by)));
    			$details['help_ticket_no'][]=strtoupper($row->help_ticket_no);
    			$details['remarks'][]=ucwords(strtolower($row->remarks));
    			$details['added_on'][]=date('d-M-Y H:i',(strtotime($row->added_on)));
    			$details['ticket_status'][]=$row->ticket_status;
    			$details['assignedTo'][]=$assignedTo;
    			$details['assignBy'][]=$assignBy;
    			$details['updated_remarks'][]=$row->updated_remarks;
    			$details['updated_on'][]=$u;
    			$details['task_record_id'][]=$row->task_record_id;
    			$details['id'][]=$row->id;
    			$details['ticket_closed_on'][]=$clo;
    			$details['timeTaken'][]=$timeTaken." Days";
    			
    		}

    	}

    	return $details;
    }


    function CheckTaskLieInTheDate($date,$department,$dfid)
    {	
    		$d=array();
			$rest=$this->db->query("SELECT a.taskid 
			FROM task_department_wise_scheduling a JOIN task_management b ON a.taskid=b.task_id
			WHERE '$date' BETWEEN a.start_date AND a.end_date AND b.department_id=$department AND df_id=$dfid;");
			if($rest->num_rows()>0)
			{
				foreach($rest->result() as $row)
				{
				$d[]=$row->taskid;
				}
			}

			return $d;
    }


    function GetTaskName($id)
    {
    	$name='';
    	$rest=$this->db->select('task_name')->from('task_management')->where('task_id',$id)->get();
    	if($rest->num_rows()>0)
    	{
    		foreach($rest->result() as $row);
    		$name=ucwords(strtolower($row->task_name));
    	}

    	return $name;
    }

           function formatIndianCurrency($number) {
            $number_parts = explode('.', $number);
            $integer_part = $number_parts[0];
            $decimal_part = isset($number_parts[1]) ? '.' . $number_parts[1] : '';

            $last_three_digits = substr($integer_part, -3);
            $remaining_digits = substr($integer_part, 0, -3);

            if ($remaining_digits != '') {
            $last_three_digits = ',' . $last_three_digits;
            }

            $formatted_number = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $remaining_digits) . $last_three_digits . $decimal_part;

            return $formatted_number;
            }


          public function getmaintaskenddate($df_id){

$dates = array();

// Define the df_id for which you want to get the min and max dates
$maintaskid[] = 0;
$q6 = $this->db->select('main_task_id')->from('mdgantchartmaster')->where('status',1)->get();
if($q6->num_rows()>0){
	foreach($q6->result() as $row5){
		$maintaskid[] = $row5->main_task_id;
	}
}

 // Replace this with the actual main_task_id

// Query to get the minimum start date and maximum end date for the specified df_id and main_task_id
$query = $this->db->select('MIN(start_date) as min_start_date, MAX(end_date) as max_end_date')
    ->from('task_department_wise_scheduling')
    ->where('df_id', $df_id)
    ->where_in('taskid', $maintaskid,false)
    ->get();

if ($query->num_rows() > 0) {
    $result = $query->row();
    $min_start_date = $result->min_start_date;
    $max_end_date = $result->max_end_date;

   $dates[] = $min_start_date;

   $completedDate=$this->checkforCompletedMaxDate($df_id);
   if(strtotime($completedDate)>strtotime($max_end_date))
   {
   	$dates[]=$completedDate;
   }else
   {
   $dates[] = $max_end_date;
	}

}

return $dates;


          }


    function calculateDelayInDays($startDate, $endDate) {
    // Create DateTime objects for both dates
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);

    // Calculate the difference
    $interval = $start->diff($end);

    // Get the difference in days
    $days = $interval->days;

    // If the end date is earlier than the start date, the delay is negative
    if ($interval->invert) {
        $days = 0;
    }

    return $days;
}


public function delaypercentagecount($recordid, $df_id, $taskid){

   

    // Query to get the total number of tasks in the department
    $total_tasks_query = $this->db->select('id')
        ->from('sharmajitaskmapping')
        ->where('report_id', $recordid)
        // ->where_in('task_id', $taskid, false)
        ->get();

    $totaltask = $total_tasks_query->num_rows();
	$r=array();
	$r[]=0;
	foreach($taskid as $t)
	{
    // Query to get the number of completed tasks in the department
    $completed_tasks_query = $this->db->select('id')
        ->from('task_department_wise_scheduling')
        ->where('taskid', $t)
        ->where('df_id', $df_id)
        ->where('task_status', 1) // Assuming '1' is the status for completed tasks
        ->get();
        if($completed_tasks_query->num_rows()>0)
        {
        	$r[]=1;

        }
    }

     
    

    // Calculate the percentage of work done
    if (array_sum($r) > 0) {
        $percentage_work_done = (array_sum($r)*100)/$totaltask;
    } else {
        $percentage_work_done = 0;
    }

    return $percentage_work_done;
}


function checkwhenWorkStarted_n_Completedsharmaji($department_id,$df_id,$taskid)
{
	$data=array();
	$rest=$this->db->select('min(task_completed_on) as startdate,max(task_completed_on) as enddate')->from('task_department_wise_scheduling')->where('df_id',$df_id)->where('department_id',$department_id)->where_in('taskid',$taskid,false)->where('task_status',1)->get();
	if($rest->num_rows()>0)
	{
		foreach($rest->result() as $row);
		$data[]=$row->startdate;
		$data[]=$row->enddate;
	}

	return $data;

}

function checkActualStatusfinal($taskid,$dfid)
{
			$data=array();
			$q = $this->db->select('task_status,start_date,end_date,task_completed_on,taskid,assigned_on')->from('task_department_wise_scheduling')->where('df_id',$dfid)->where_in('taskid',$taskid,false)->get();
			if($q->num_rows()>0)
			{
			foreach($q->result() as $row)
			$data[]=$row->task_status;
			$data[]=$row->assigned_on;
			$data[]=$row->task_completed_on;
			$data[]=$row->start_date;
			$data[]=$row->end_date;
			}

			return $data;
}


function isDateInWeek($date, $mondayDate)
{
	echo $date."<br>";
    echo $mondayDate."<br>";  
    // Convert the provided dates to DateTime objects
    $date = new DateTime($date);
    $monday = new DateTime($mondayDate);

    // Check if the provided Monday date is actually a Monday
    if ($monday->format('N') != 1) {
        throw new Exception('The provided date is not a Monday.');
    }

    // Calculate the start (Monday) and end (Sunday) dates of the week
    $startOfWeek = $monday;
    $endOfWeek = (new DateTime($mondayDate))->modify('+6 days');

    // Check if the given date is within the week
    return $date >= $startOfWeek && $date <= $endOfWeek;
}


function getDoneTaskMaxStart_EndDate($taskid,$df_id)
{	
	$data=array();
	if(count($taskid)>0)
	{
		$result1 = "'" . implode ( "', '", $taskid ) . "'";
	// $rest=$this->db->select('min(start_date) as min_date,max(end_date) as max_date')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id')->where_in('a.taskid',$result1,false)->where('a.df_id',$df_id)->where('a.task_status',1)->get();

			$rest=$this->db->select('min(task_completed_on) as min_date,max(task_completed_on) as max_date')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id')->where_in('a.taskid',$result1,false)->where('a.df_id',$df_id)->where('a.task_status',1)->get();
	if($rest->num_rows()>0)
	{
		foreach($rest->result() as $dataa);
		if($dataa->min_date<>'' && $dataa->max_date<>'')
		{
			$data[]=date('Y-m-d',strtotime($dataa->min_date));
			$data[]=date('Y-m-d',strtotime($dataa->max_date));
		}
	}
	}
	return $data;
}

function checkifalltasksaredone($taskid,$df_id)
{
		$d=0;
		$data=array();
		if(count($taskid)>0)
		{
		foreach($taskid as $t)
		{
		
			$rest=$this->db->select('a.id')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id')->where('a.taskid',$t)->where('a.df_id',$df_id)->where('a.task_status',1)->get();
			if($rest->num_rows()>0)
			{
				$data[]=1;
			}

		}
		}

		if(count($taskid)<=array_sum($data))
		{
			$d=1;
		}
		
		return $d;
}


function getTaskCompletedDate($taskid,$df_id)
{
	$task_completed_on='';
	 $q5 = $this->db->select('task_completed_on')
        ->from('task_department_wise_scheduling')
        ->where('taskid', $taskid)
        ->where('df_id', $df_id)
        ->get();
        if ($q5->num_rows() > 0) {
        $task_completed_on = $q5->row()->task_completed_on;
		}

		return $task_completed_on;

}

function checkforCompletedMaxDate($df_id)
{
	$maxdate='';
	$allmappedtask=$this->GetallMappedTask();
	if(count($allmappedtask)>0)
	{
	$result1 = "'" . implode ( "', '", $allmappedtask ) . "'";

	$rest=$this->db->select('max(a.task_completed_on) as maxtaskcompletion')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id')->where('a.df_id',$df_id)->where_in('a.taskid',$result1,false)->where('a.task_status',1)->get();
	if($rest->num_rows()>0)
	{
		foreach($rest->result() as $row);
		if($row->maxtaskcompletion<>'')
		{
			$maxdate=date('Y-m-d',strtotime($row->maxtaskcompletion));
		}
	}
	}

	return $maxdate;

}

function GetallMappedTask()
{
	$d=array();
	$rest = $this->db->select('a.task_id')->from('sharmajitaskmapping a')->join('task_management b','a.task_id=b.task_id')->get();
	if($rest->num_rows()>0)
	{
		foreach($rest->result() as $row)
		{
			$d[]=$row->task_id;
		}
	}

	return $d;
}


 public function getWeekNumberFromDateWithYear($date) {
 		$year=$this->getYearForWeek($date);
        $dateTime = new DateTime($date);
        $data=$year.$dateTime->format('W');

        return (int)$data;	
    }



public function get_week_numbers_between_datesWithYear($start_date, $end_date) {
        $weeks = array();
    
    // Convert the start and end dates to timestamps
    $start_date = strtotime($start_date);
    $end_date = strtotime($end_date);

    if ($start_date === false || $end_date === false) {
        // Handle invalid date formats
        return $weeks;
    }

    // Get the starting and ending years
    $start_year = date('Y', $start_date);
    $end_year = date('Y', $end_date);

    // Get the week number for the start and end dates
    $start_week_number = date('W', $start_date);
    $end_week_number = date('W', $end_date);

    // Adjust for week number 0 (last week of the year)
    if ($start_week_number == 0) $start_week_number = 52;
    if ($end_week_number == 0) $end_week_number = 52;

    // Add the start week number and year to the array
    $current_year = $start_year;
    $data=$current_year.sprintf('%02d', $start_week_number);
    $weeks[] = (int)$data; // Prefix with 0 if single digit

    // Loop through the weeks until we reach the end week number
    while ($start_date < $end_date) {
        $start_date = strtotime('+1 week', $start_date); // Increment the date by 1 week
        $week_number = date('W', $start_date); // Get the new week number
        $year = date('Y', $start_date); // Get the corresponding year

        // Adjust for week 0 (last week of the year)
        if ($week_number == 0) {
            $week_number = 52;
            $year--;
        }

        // Format the week and year, then add it to the array
        $data=$year. sprintf('%02d', $week_number);
        $weeks[] =(int)$data;
    }

    // Remove duplicates and sort the array
    $weeks = array_unique($weeks);
    sort($weeks);

    return $weeks;
}

function getYearForWeek($dateString) {
    // Create DateTime object from the given date string
    $date = new DateTime($dateString);
    
    // Check the ISO week year, which returns the year that the week belongs to based on ISO-8601
    $isoYear = $date->format("o"); // 'o' provides the ISO year of the date
    
    return $isoYear;
}



// public function get_week_numbers_between_datesWithYearNew($start_date, $end_date) {
// 	//echo $start_date."<br/>".$end_date; exit
//      $weeks = array();
//       // Convert the start and end dates to timestamps
//     $start_date = strtotime($start_date);
//     $end_date = strtotime($end_date);

//     if ($start_date === false || $end_date === false) {
//         // Handle invalid date formats
//         return $weeks;
//     }

//     // Get the starting and ending years
//     $start_year = date('Y', $start_date);
//     $end_year = date('Y', $end_date);

//     // Get the week number for the start and end dates
//     $start_week_number = date('W', $start_date);
//     $end_week_number = date('W', $end_date);

//     // Adjust for week number 0 (last week of the year)
//     if ($start_week_number == 0) $start_week_number = 52;
//     if ($end_week_number == 0) $end_week_number = 52;

//     // Add the start week number and year to the array
//     $current_year = $start_year;
//     $data=$current_year.sprintf('%02d', $start_week_number);
//     $weeks[] = (int)$data; // Prefix with 0 if single digit

//     // Loop through the weeks until we reach the end week number
//     while ($start_date < $end_date) {
//         $start_date = strtotime('+1 week', $start_date); // Increment the date by 1 week
//         $week_number = date('W', $start_date); // Get the new week number
//         $year = date('Y', $start_date); // Get the corresponding year

//         // Adjust for week 0 (last week of the year)
//         if ($week_number == 0) {
//             $week_number = 52;
//             $year--;
//         }

//         // Format the week and year, then add it to the array
//         $data=$year. sprintf('%02d', $week_number);
//         $weeks[] =(int)$data;
//     }

//     // Remove duplicates and sort the array
//     $weeks = array_unique($weeks);
//     sort($weeks);

//     return $weeks;
// }


function get_week_numbers_between_datesWithYearNewOlddddddd($start_date, $end_date) {
    $weeks = array();

    // Convert the start and end dates to timestamps
    $current_date = strtotime($start_date);
    $end_date = strtotime($end_date);

    if ($current_date === false || $end_date === false) {
        // Handle invalid date formats
        return $weeks;
    }

    // Loop through the weeks until we reach the end date
    while ($current_date <= $end_date) {
        // Get the ISO week number and year
        $week_number = date('W', $current_date); // ISO-8601 week number
        $year = date('o', $current_date);        // ISO-8601 year (handles week 1 of the new year)

        // Format the week and year as YYYYWW, then add it to the array
        $weeks[] = (int)($year . sprintf('%02d', $week_number));

        // Increment the date by 1 week
        $current_date = strtotime('+1 week', $current_date);
    }

    // Remove duplicates (in case of overlap) and sort the array
    $weeks = array_unique($weeks);
    sort($weeks);

    return $weeks;
}


function get_week_numbers_between_datesWithYearNew($start_date, $end_date) {
    $weeks = array();

    // Convert the start and end dates to timestamps
    $current_date = strtotime($start_date);
    $end_date = strtotime($end_date);

    if ($current_date === false || $end_date === false) {
        // Handle invalid date formats
        return $weeks;
    }

    // Loop through each day between the dates
    while ($current_date <= $end_date) {
        // Get the ISO week number and year
        $week_number = date('W', $current_date); // ISO-8601 week number
        $year = date('o', $current_date);        // ISO-8601 year (handles week 1 of the new year)

        // Format the week and year as YYYYWW, then add it to the array
        $weeks[] = (int)($year . sprintf('%02d', $week_number));

        // Increment the date by 1 day
        $current_date = strtotime('+1 day', $current_date);
    }

    // Remove duplicates and sort the array
    $weeks = array_unique($weeks);
    sort($weeks);

    return $weeks;
}

function checkdfclosed($df_id)
{
$Rest=$this->db->select('id')->from('task_department_wise_scheduling')->where('df_id',$df_id)->where('task_status',0)->get();
return $Rest->num_rows();
}


function GetSharmajiTasks($taskid,$dfid)
{

		    $data = array();
    $m11 = 1;

    $q = $this->db->select('b.task_name, b.task_id')
        ->from('sharmajitaskmapping a')
        ->join('task_management b', 'a.task_id=b.task_id', 'left')
        ->where('a.report_id', $taskid)
        ->get();

    foreach ($q->result() as $recod) {
        $q9 = $this->db->select('a. start_date, a.end_date, a.task_completed_on, a.remarks, b.title, b.first_name,b.last_name,a.task_status')
            ->from('task_department_wise_scheduling a')
            ->join('system_users b','a.assigned_user=b.user_id','left')
            ->where('a.df_id', $dfid)
            ->where('a.taskid', $recod->task_id)
            ->get();
        
        foreach ($q9->result() as $recoractualinfo);
        $enddatess = date('Y-m-d', strtotime($recoractualinfo->end_date));
        $currentDate = date('Y-m-d');

        if ($recoractualinfo->task_completed_on != '0000-00-00 00:00:00') {
            $actenddatess = date('Y-m-d', strtotime($recoractualinfo->task_completed_on));
            $printcompletiondate = date('d-M-Y', strtotime($recoractualinfo->task_completed_on));
            $ddayss = $this->calculateDelayInDays($enddatess, $actenddatess);
            if ($ddayss > 0) {
                $delaydays = "<span style='color:red; font-weight:bold;'>".$ddayss." Days</span>";
            } else {
                $delaydays = "<span style='color:green; font-weight:bold;'>0 Days</span>";
            }
        } else {
            $printcompletiondate = '<span style="color:red; font-weight:bold">Pending to Start</span>';
            if ($currentDate > $enddatess) {
                $ddayss = $this->calculateDelayInDays($enddatess, $currentDate);
                $delaydays = "<span style='color:red; font-weight:bold;'>".$ddayss." Days Overdue</span>";
            } else {
                $delaydays = "<span style='color:green; font-weight:bold;'>No Delay</span>";
            }
        }

        $responsibleperson = ucwords(strtolower($recoractualinfo->title." ".$recoractualinfo->first_name." ".$recoractualinfo->last_name));
        
        $data[] = array(
            'task_name' => ucwords(strtolower($recod->task_name)),
            'start_date' => date('Y-m-d', strtotime($recoractualinfo->start_date)),
            'end_date' => date('Y-m-d', strtotime($recoractualinfo->end_date)),
            'completionDate' => $printcompletiondate,
            'responsibleperson'=>$responsibleperson,
            'remarks' => ucwords(strtolower($recoractualinfo->remarks)),
            'delaydays' => $delaydays,
            'taskid' => $recod->task_id,
            'task_status'=>$recoractualinfo->task_status
        );
        $m11++;
    }


    if (count($data) > 0) {
        usort($data, function($a, $b) {
            $date1 = strtotime($a['start_date']);
            $date2 = strtotime($b['start_date']);
            return $date1 - $date2;
        });
    }

   
   return $data;

}


private $task_map = [];
    private $cache = [];
    private $df_release_date = '2025-04-06';

    public function getSpecificTaskDates($df_release_date = '2025-04-06')
    {
        $this->df_release_date = $df_release_date;

        // Fetch all tasks
        $query = $this->db->get('task_management');
        $tasks = $query->result_array();

        // Map tasks by task_id
        foreach ($tasks as $task) {
            $this->task_map[$task['task_id']] = $task;
        }

        $required_ids = [103, 52];
        $result = [];

        foreach ($required_ids as $task_id) {
            if (isset($this->task_map[$task_id])) {
                $visited_chain = [];
                $expected_date = $this->calculateSafeDate($task_id, $visited_chain);
                $result[] = [
                    'task_id' => $task_id,
                    'task_name' => $this->task_map[$task_id]['task_name'],
                    'expected_date' => $expected_date,
                    'note' => (in_array('loop_detected', $visited_chain)) ? 'Loop detected, calculated partial date' : 'OK',
                    'chain' => implode(' → ', array_filter($visited_chain, fn($v) => $v !== 'loop_detected'))
                ];
            } else {
                $result[] = [
                    'task_id' => $task_id,
                    'error' => 'Task not found',
                ];
            }
        }

        return $result;
    }

    private function calculateSafeDate($task_id, &$visited)
    {
        if (isset($this->cache[$task_id])) {
            return $this->cache[$task_id];
        }

        if (in_array($task_id, $visited)) {
            $visited[] = 'loop_detected';
            // Stop here and return current date
            return date('Y-m-d'); // optional: return today's date if loop happens
        }

        $visited[] = $task_id;
        $task = $this->task_map[$task_id];

        if ($task['tat_start_from'] == 0) {
            $start_date = $this->df_release_date;
        } else {
            $start_date = $this->calculateSafeDate($task['tat_start_from'], $visited);
        }

        $expected_date = date('Y-m-d', strtotime($start_date . ' + ' . $task['tat'] . ' days'));
        $this->cache[$task_id] = $expected_date;
        return $expected_date;
    }


    /**
 * Gets the 4 main KPI counts for the management report.
 */
public function get_management_kpi_data($start_date, $end_date)
{
    $data = [];

    // 1. No. of DF Release
    $q_released = $this->db->query(
        "SELECT COUNT(id) as count FROM df_release 
         WHERE DATE(added_on) BETWEEN ? AND ?",
        [$start_date, $end_date]
    );
    $data['released'] = $q_released->row()->count;

    // 2. No. of Task Assigned
    $q_assigned = $this->db->query(
        "SELECT COUNT(id) as count FROM task_department_wise_scheduling 
         WHERE DATE(assigned_on) BETWEEN ? AND ?",
        [$start_date, $end_date]
    );
    $data['assigned'] = $q_assigned->row()->count;

    // 3. No. of Task Completed
    $q_completed = $this->db->query(
        "SELECT COUNT(id) as count FROM task_department_wise_scheduling 
         WHERE DATE(task_completed_on) BETWEEN ? AND ?",
        [$start_date, $end_date]
    );
    $data['completed'] = $q_completed->row()->count;

    // 4. No. of Task Missed (Tasks that end_date was in the period but are not complete)
    $q_missed = $this->db->query(
        "SELECT COUNT(id) as count FROM task_department_wise_scheduling 
         WHERE end_date BETWEEN ? AND ? AND task_status IN (0, 2)",
        [$start_date, $end_date]
    );
    $data['missed'] = $q_missed->row()->count;

    return $data;
}

// --- Functions to get the details for each KPI ---

public function get_kpi_details_released($start_date, $end_date)
{
    $this->db->select('
        dr.df_no, 
        DATE_FORMAT(dr.added_on, "%d-%m-%Y") as date, 
        dr.df_description,
        DATE_FORMAT(p.podate, "%d-%m-%Y") as po_date,
        CONCAT(u.first_name, " ", u.last_name) as marketing_person
    ');
    $this->db->from('df_release as dr');
    $this->db->join('poreceived as p', 'p.df_id = dr.id', 'left');
    $this->db->join('system_users as u', 'u.user_id = dr.added_by', 'left');
    $this->db->where("DATE(dr.added_on) BETWEEN '$start_date' AND '$end_date'");
    $this->db->order_by('p.podate', 'ASC'); // Sort by PO Date Ascending
    return $this->db->get()->result_array();
}

public function get_kpi_details_assigned($start_date, $end_date)
{
    $this->db->select(
        't.id, t.df_id, t.task_status, ' .
        'DATE_FORMAT(t.assigned_on, "%d-%m-%Y") as date, ' .
        'DATE_FORMAT(t.start_date, "%d-%m-%Y") as start_date, ' .
        'DATE_FORMAT(t.end_date, "%d-%m-%Y") as end_date, ' .
        'DATE_FORMAT(po.podate, "%d-%m-%Y") as po_date, ' . // NEW: PO Date
        'CONCAT(mkt.first_name, " ", mkt.last_name) as marketing_person, ' . // NEW: Marketing Person
        
        'CASE 
            WHEN t.task_completed_on > "0000-00-00" 
            THEN DATE_FORMAT(t.task_completed_on, "%d-%m-%Y %h:%i %p") 
            ELSE "" 
         END as completion_datetime, ' .

        't.end_date as raw_end_date, t.task_completed_on as raw_completed_date, ' .

        'm.task_name, ' . 
        
        'CASE 
            WHEN d.df_no IS NOT NULL AND d.df_no != "" THEN d.df_no 
            ELSE po.df_number 
         END as df_no, ' .

        'CONCAT(u.first_name, " ", u.last_name) as assigned_to, ' .
        '(SELECT COUNT(id) FROM communication_ticket_system 
          WHERE task_record_id = t.id AND ticket_status = 0) as open_tickets'
    , false);
    $this->db->from('task_department_wise_scheduling t');
    $this->db->join('task_management m', 't.taskid = m.task_id', 'left');
    $this->db->join('df_release d', 't.df_id = d.id', 'left');
    $this->db->join('poreceived po', 't.df_id = po.df_id', 'left'); 
    $this->db->join('system_users u', 't.assigned_user = u.user_id', 'left');
    $this->db->join('system_users mkt', 'd.added_by = mkt.user_id', 'left'); // NEW: Join for Marketing Name
    $this->db->where("DATE(t.assigned_on) BETWEEN '$start_date' AND '$end_date'");
    $this->db->group_by('t.id'); 
    $this->db->order_by('po.podate', 'ASC'); // Requested: PO Date ASC
    return $this->db->get()->result_array();
}

public function get_kpi_details_completed($start_date, $end_date)
{
    $this->db->select(
        't.id, t.df_id, t.task_status, ' .
        'DATE_FORMAT(t.task_completed_on, "%d-%m-%Y") as date, ' .
        'DATE_FORMAT(t.start_date, "%d-%m-%Y") as start_date, ' .
        'DATE_FORMAT(t.end_date, "%d-%m-%Y") as end_date, ' .
        'DATE_FORMAT(po.podate, "%d-%m-%Y") as po_date, ' . // NEW: PO Date
        'CONCAT(mkt.first_name, " ", mkt.last_name) as marketing_person, ' . // NEW: Marketing Person
        
        'CASE 
            WHEN t.task_completed_on > "0000-00-00" 
            THEN DATE_FORMAT(t.task_completed_on, "%d-%m-%Y %h:%i %p") 
            ELSE "" 
         END as completion_datetime, ' .
        
        't.end_date as raw_end_date, t.task_completed_on as raw_completed_date, ' .

        'CASE 
            WHEN t.task_completed_on > t.end_date 
            THEN DATEDIFF(DATE(t.task_completed_on), DATE(t.end_date)) 
            ELSE 0 
         END as delay_days, ' .
        
        'm.task_name, ' .

        'CASE 
            WHEN d.df_no IS NOT NULL AND d.df_no != "" THEN d.df_no 
            ELSE po.df_number 
         END as df_no, ' .

        'CONCAT(u.first_name, " ", u.last_name) as completed_by, ' .
        '(SELECT COUNT(id) FROM communication_ticket_system 
          WHERE task_record_id = t.id AND ticket_status = 0) as open_tickets'
    , false);
    $this->db->from('task_department_wise_scheduling t');
    $this->db->join('task_management m', 't.taskid = m.task_id', 'left');
    $this->db->join('df_release d', 't.df_id = d.id', 'left');
    $this->db->join('poreceived po', 't.df_id = po.df_id', 'left'); 
    $this->db->join('system_users u', 't.task_completed_by = u.user_id', 'left');
    $this->db->join('system_users mkt', 'd.added_by = mkt.user_id', 'left'); // NEW: Join for Marketing Name
    $this->db->where("DATE(t.task_completed_on) BETWEEN '$start_date' AND '$end_date'");
    $this->db->group_by('t.id');
    $this->db->order_by('po.podate', 'ASC'); // Requested: PO Date ASC
    return $this->db->get()->result_array();
}

public function get_kpi_details_missed($start_date, $end_date)
{
    $this->db->select(
        't.id, t.df_id, t.task_status, ' . 
        'DATE_FORMAT(t.end_date, "%d-%m-%Y") as date, ' . 
        'DATE_FORMAT(t.start_date, "%d-%m-%Y") as start_date, ' .
        'DATE_FORMAT(po.podate, "%d-%m-%Y") as po_date, ' . // NEW: PO Date
        'CONCAT(mkt.first_name, " ", mkt.last_name) as marketing_person, ' . // NEW: Marketing Person
        't.end_date as raw_end_date, ' . 
        
        'm.task_name, ' .

        'CASE 
            WHEN d.df_no IS NOT NULL AND d.df_no != "" THEN d.df_no 
            ELSE po.df_number 
         END as df_no, ' .

        'CONCAT(u.first_name, " ", u.last_name) as assigned_to, t.remarks, ' .
        '(SELECT COUNT(id) FROM communication_ticket_system 
          WHERE task_record_id = t.id AND ticket_status = 0) as open_tickets'
    , false);
    $this->db->from('task_department_wise_scheduling t');
    $this->db->join('task_management m', 't.taskid = m.task_id', 'left');
    $this->db->join('df_release d', 't.df_id = d.id', 'left');
    $this->db->join('poreceived po', 't.df_id = po.df_id', 'left'); 
    $this->db->join('system_users u', 't.assigned_user = u.user_id', 'left');
    $this->db->join('system_users mkt', 'd.added_by = mkt.user_id', 'left'); // NEW: Join for Marketing Name
    $this->db->where("t.end_date BETWEEN '$start_date' AND '$end_date'");
    $this->db->where_in('t.task_status', [0, 2]); 
    $this->db->group_by('t.id');
    $this->db->order_by('po.podate', 'ASC'); // Requested: PO Date ASC
    return $this->db->get()->result_array();
}

/**
 * Gets daily completion counts for the line chart.
 */
public function get_daily_completion_trend($start_date, $end_date)
{
    $query = $this->db->query("
        SELECT 
            DATE_FORMAT(task_completed_on, '%Y-%m-%d') as date,
            COUNT(id) as count
        FROM task_department_wise_scheduling
        WHERE DATE(task_completed_on) BETWEEN ? AND ?
        GROUP BY DATE(task_completed_on)
        ORDER BY date ASC
    ", [$start_date, $end_date]);

    return $query->result_array();
}
}
