<?php 
$CI =& get_instance();
$CI->load->model('Task_model');
$df_id=$this->uri->segment(3);
$department=$this->uri->segment(4);
$date_range=$CI->Task_model->getWeekStartDates($df_id,$department);
$tasks=$CI->Task_model->getDFTaskScheduled($df_id,$department);
if(count($date_range)>0)
{
	$stdate=$date_range[0];
	$etdate=$date_range[1];
	//$Dates=$CI->Task_model->GetallDates($stdate,$etdate);
	$Dates=$CI->Task_model->getMondays($stdate,$etdate);
	$days_count=$CI->Task_model->getDaysCountForEachMonthFromArray($Dates);
	//echo "<pre>";print_r($days_count); exit;
}else
{
	echo "Date Not Found"; exit;
}
$df_data = $CI->Task_model->getallrunningdfByID($this->uri->segment(3));
$department_name = $CI->Task_model->textFormatting($CI->Task_model->getDepartmentBYID($this->uri->segment(4)));
$work_done_per = $CI->Task_model->workCompleted($this->uri->segment(3),$this->uri->segment(4));
$work_delayed_per11 = $CI->Task_model->workDelayed($this->uri->segment(3),$this->uri->segment(4));
$work_delayed_per=$work_delayed_per11;
//$work_delayed_per12=explode('~',$work_delayed_per11);
//$work_delayed_per=$work_delayed_per12[0];
$planned_end_date = $CI->Task_model->getPlannedEndDate($this->uri->segment(3),$this->uri->segment(4));
$estimated_end_date = $CI->Task_model->GetEstimatedWithDelay($this->uri->segment(3),$this->uri->segment(4));
$ActualDelay = $CI->Task_model->getActualDelay($this->uri->segment(3),$this->uri->segment(4));
if(count($df_data)>0)
{
	$runningdfno=strtoupper($df_data[1]);
	$companyname=$CI->Task_model->textFormatting($df_data[3]);
	$pono=$CI->Task_model->textFormatting($df_data[4]);
	$podate=$CI->Task_model->textFormatting($df_data[5]);
	$df_addedOn=date('d-m-Y',strtotime($df_data[7]));
	$marketing=$CI->Task_model->textFormatting($df_data[8]);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="GAMAVIS SOFTECH PRIVATE LIMITED">

<link rel="shortcut icon" href="">

<title>PROJECT UPDATES |  SUPPLIER GANTT CHART</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js">
</script> 

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/js/bootstrap.min.js" integrity="sha512-7rusk8kGPFynZWu26OKbTeI+QPoYchtxsmPeBqkHIEXJxeun4yJ4ISYe7C6sz9wdxeE1Gk3VxsIWgCZTc+vX3g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<style>

.holiday{
	background-color: #ffc0cb !important;
}
.completed{
background-color: #00FF00;
}

.completed1{
background-color: #73d673 !important;
background-image: repeating-linear-gradient(135deg, #70596E, #70596E 0.5px, transparent 0.5px, transparent 16px);
background-size: 23px 23px;

}
.earlycomplete
{
background-color: #a2c1e0;
opacity: 0.50 !important;

}

.earlycomplete1
{
background-color: #a2c1e0 !important;
background-image: repeating-linear-gradient(135deg, #70596E, #70596E 0.5px, transparent 0.5px, transparent 16px);
background-size: 23px 23px;

}

.org{
background-color: #B7833A;
}

.act{
background-color: #70596ed1;
background-image: repeating-linear-gradient(135deg, #70596E, #70596E 0.5px, transparent 0.5px, transparent 16px);
background-size: 23px 23px;
}

.org1{
background-color: #b7833a9c;
background-image: repeating-linear-gradient(135deg, #B7833A, #B7833A 0.5px, transparent 0.5px, transparent 16px);
background-size: 23px 23px;
}

.beyondcomplete{
background-color: #FAA0A0;
/*         background-image: repeating-linear-gradient(135deg, #B7833A, #B7833A 0.5px, transparent 0.5px, transparent 16px);*/
/*    background-size: 23px 23px;*/
}

.beyondcomplete1
{
background-color: #f29b9b !important;
/*          background-image: repeating-linear-gradient(135deg, #B7833A, #B7833A 0.5px, transparent 0.5px, transparent 16px);*/
background-size: 23px 23px;
}

.progress{
margin: 0;
height: 45px;
background-color: #ebeff2;
border-radius: 20px;


/*        background-image: repeating-linear-gradient(135deg, #000, #000 0.5px, transparent 0.5px, transparent 16px);*/
}

.progress1{
/*            background-image: repeating-linear-gradient(135deg, #000, #000 0.5px, transparent 0.5px, transparent 16px);*/
}
h1{ position: relative;
color: #000;
display: inline-block;
margin: 10px 0;
text-shadow: 0 0 2px black;
text-align: center;
font-weight: 600;
}
h1:before {
content: "";
position: absolute;
background: #ffd69d;
width: 100px;
height: 100px;
border-radius: 50%;
top: 50%;
left: 50%;
-webkit-transform: translate(-50%, -50%);
transform: translate(-50%, -50%);
z-index: -1;
}

h3{ position: relative;
color: #000;
display: inline-block;
margin: auto;
text-shadow: 0 0 2px black;
text-align: center !important;
font-weight: 300;
}

h4{ position: relative;
color: #000;
display: inline-block;
margin: auto;
text-shadow: 0 0 2px black;
text-align: center !important;
font-weight: 300;
}


.table-scroll {
position: relative;
width: 100%;
z-index: 1;
margin: auto;
overflow: auto;
height: 350px;
}

.table-scroll table {
width: 100%;
margin: auto;
border-collapse: separate;
border-spacing: 0;
}

.table-wrap {
position: relative;
}

.table-scroll th,
.table-scroll td {
/*padding: 5px;*/
/* vertical-align: top; */
/* border-radius: 2px; */
/* box-shadow: rgb(204 219 232 / 0%) 3px 3px 6px 0px inset, rgb(255 255 255) -3px -3px 6px 1px inset; */

font-size: 13px;
text-align: center;
}

.table-scroll thead th {
position: -webkit-sticky;
position: sticky;
top: 0;
text-align: center;
font-size: 13px;
box-shadow: rgb(204 219 232 / 0%) 3px 3px 6px 0px inset, rgb(255 255 255) -3px -3px 6px 1px inset;
}

.table-scroll th,thead{
	word-wrap: break-word !important; /* or overflow-wrap: break-word; */
}

/* safari and ios need the tfoot itself to be position:sticky also */

.table-scroll tfoot,
.table-scroll tfoot th,
.table-scroll tfoot td {
position: -webkit-sticky;
position: sticky;
bottom: 0;
z-index: 4;
}

th:nth-child(1) {
position: -webkit-sticky;
position: sticky;
left: 0;
z-index: 2;
}

thead th:nth-child(1),
tfoot th:first-child {
z-index: 5;
}

.packing {
/* border: 1px solid black; */
height: 700px;
overflow-y: auto;
margin-top: 25px;
border: 1px solid black;
border-radius: 3px;
}

.right div {
writing-mode: vertical-rl;
font-size: 12px;
font-weight: 600;
}

.light_grey {
background-color: #efefef;
/* box-shadow: inset 0 0 15px 0 #c7c7c7; */
vertical-align: top;
transform: rotate(180deg);
}

.nxt {
vertical-align: top;
transform: rotate(180deg);
}

.lighter_grey {
background-color: #dddddd;
}



.light_blue {
background-color: #c5dcf4;
/* box-shadow: rgb(204 219 232 / 0%) 3px 3px 6px 0px inset, rgb(255 255 255) -3px -3px 6px 1px inset; */
}

.light_red {
background-color: #eed2db;
/* box-shadow: rgb(204 219 232 / 0%) 3px 3px 6px 0px inset, rgb(255 255 255) -3px -3px 6px 1px inset; */
}

.light_green {
background-color: #01fe4b;

}

.lighter_green {
background-color: #01fe4b;
box-shadow: inset 0 0 15px 0 #c7c7c7;
}

.red {
background-color:#e3696a;
color: white;
text-align: center;
}

.yellow {
background-color: yellow;
color: black;
text-align: center;
}

.green {
background-color: #59ab77;
color: white;
text-align: center;
}

.black {
background-color: black;
color: white;
text-align: center;
}

.orange {
background-color: orange;
color: black;
text-align: center;
}

.blue {
background-color: #afcbe3;
color: white;
text-align: center;
}

.font_size {
font-size: 14px !important;
}

.na {
color: red;
}

.supplier_planned
{
background-color: #ebeff2;
border-color: #ebeff2;
border:none;
}

.supplier_not_started
{
/*    background-image: repeating-linear-gradient(135deg, #000, #000 0.5px, transparent 0.5px, transparent 16px);*/
border:none;
background-color: #DAF7A6   ;
}

.supplier_neutral
{
background-color: #fff;
border-color: #000;

}

</style>

</head>

<body>

<div class="wrapper">
<div class="container-fluid">
<div class="row">
<div class="col-sm-12">
<div class="text-center">
<h1>Progress Gant Chart</h1>
</div>
</div>
</div>

<div class="row">
<div class="col">
	<select id="switch" onchange="switchData();">
		<option value="1" selected>Week Wise</option>
		<option value="2">Day Wise</option>
		<option value="3">Department Wise</option>
		<option value="4">Department & Week Wise</option>
	</select>
</div>
<div class="col">
	<select name="filter" id="filter" class="form-control" onchange="filter_Gant();">
		<option value="" <?php if($this->uri->segment(4)==''){?> selected <?php } ?>>All Departments</option>
		<?php 
			$rest=$this->db->select('a.department_id,b.department')->from('task_department_wise_scheduling a')->join('departments b','a.department_id=b.department_id')->where('b.status',1)->where('b.business_loc_id',2)->group_by('a.department_id')->order_by('b.department','ASC')->get();
			if($rest->num_rows()>0)
			{
			foreach($rest->result() as $row)
			{
		?>
		<option value="<?php echo $row->department_id;?>" <?php if($this->uri->segment(4)==$row->department_id){?> selected <?php } ?>><?php echo strtoupper($row->department);?></option>
		<?php
			} 
			}
		?>
	</select>
</div>
<div class="col">
	
<!-- <button class="btn btn-success waves-effect waves-light pull-right" data-toggle="modal" data-target=".bs-example-modal-lg" >SETTING</button> -->
<!-- Full screen modal -->

    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">

    <form id="loginForm" method="post" action="<?php echo page_url;?>Master/User_management/add_new_user" enctype="multipart/form-data">

    <div class="modal-dialog modal-full">

    <div class="modal-content">

    <div class="modal-header">

    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

    <h4 class="modal-title">Add New User</h4>

    </div>

  <div class="modal-body">
	<?php 
		$q = $this->db->select('department_id, department')->from('departments')->where('status',1)->where('business_loc_id',2)->get();
		foreach($q->result() as $department){


	?>
<div class="row">
	<h5><?php echo $department->department;?></h5><hr>
<?php 
	$q1 = $this->db->select('task_id, task_name')
	->from('task_management')
	->where('department_id',$department->department_id)
	->where('status',1)
	->get();
	foreach($q1->result() as $taskdata){
?>
<div class="col-md-10">
	<input type="text" class="form-control" name="taskname" value="">
	<input type="hidden" name="taskid" value="">
</div>
<div class="col-md-2">
	<input type="checkbox" name="">
</div>
<?php }?>
</div>
<?php }?>

<div class="row">
<div class="col-md-4"></div>
<div class="col-md-4">
<input type="submit" style="width: 100%;" name="" onclick="taskupdationvalidation();" value="Submit" class="btn btn-success">
</div>
</div>

</div>

    <div class="modal-footer">

    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

    <input type="submit" id="usersave" class="btn btn-info" value="Submit"> 

    </div>

    </div>

    </div>

    </form>

    </div><!-- /.modal -->


</div>


</div>
<div class="row" style="margin-top:20px;">
	
	<div class="col">
	<table border="1" class="table table-bordered" style="padding:10px;background-color: #efefef;">
	<tr style="font-size:12px;">
	<th>DF NO<br/><span style="color:brown;"><?php echo $runningdfno;?><span></th>
	<th>Company Name<br/><span style="color:brown;"><?php echo $companyname;?><span></th>
	<th>DF Release Date<br/><span style="color:brown;"><?php echo $df_addedOn;?></span></th>
	<th>Marketing Person<br/><span style="color:brown;"><?php echo $marketing;?></span></th>
	</tr>
	</table>
	</div>


	<div class="col">
	<table border="1" class="table table-bordered" style="padding:10px;background-color: #efefef;">
	<tr style="font-size:12px;">
	<th>Department<br/><span style="color:brown;"><?php echo $department_name;?><span></th>
	<th>Work Completed<br/><span style="color:brown;"><?php echo $work_done_per;?>%</span></th>
	<th>Delay<br/><span style="color:brown;"><?php echo $work_delayed_per;?> Days</span></th>
	<th>PLN. End Date<br/><span style="color:brown;"><?php echo date('d-M-Y',strtotime($planned_end_date));?></span></th>
	<th>EST End Date<br/><span style="color:brown;"><?php
	$planneddateofcompletion = date('Y-m-d',strtotime($planned_end_date));
	//echo $planneddateofcompletion; exit;
	if($work_delayed_per>0){
		
function addDaysToDate($dateString, $daysToAdd) {
    try {
        $date = new DateTime($dateString);
        $date->modify("+$daysToAdd days");
        return $date->format('Y-m-d');
    } catch (Exception $e) {
        return "Error: " . $e->getMessage();
    }
}

$originalDate = $planneddateofcompletion; // Replace with your date
$daysToAdd = $work_delayed_per;

$newDate = addDaysToDate($originalDate, $daysToAdd);

$nextcompletiondate = date('d-M-Y',strtotime($newDate));


	}else{
		$nextcompletiondate = date('Y-m-d',strtotime($planned_end_date));
	}
	 echo $nextcompletiondate;?></span></th>
	<th>ACT End Date<br/>
	</th>

	</tr>
	</table>

	</div>

	

</div>



<div class="col-sm-12 card-box">
<div class="packing table-scroll">
<table id="main-table" class="main-table left" border="1">
<thead>

<tr>
<th style="background-color:white;padding: 0; vertical-align: bottom;" rowspan="3">
<table>
<tbody><tr>
<td style="text-align: center; border-bottom: 1px solid lightgray; font-size: 20px;">
Indicators</td>
</tr>
</tbody></table>
 <table style="">
<tbody><tr>
<td class="blue" width="20%"></td>

<td class="" width="10%">Planned</td>

<td class="green" width="20%"></td>

<td class="" width="10%">Actual</td>

<td class="red" width="20%"></td>

<td class="" width="10%">Delay</td>

</tr>




</tbody></table>





<table style="width: 100%;" border="1">


<tbody>
<tr>
<th scope="col" class="lighter_grey">
<div style="width: 30px;">Sr. No.</div>
</th>
<th scope="col" class="lighter_grey">
<div style="width: 100px;">Department</div>
</th>
<th scope="col" class="lighter_grey">
<div style="width: 250px;">Task</div>
</th>
<th scope="col" class="lighter_grey">
<div style="width: 130px;">HOD</div>
</th>
<th scope="col" class="lighter_grey">
<div style="width: 50px;">PLN Vs ACT</div>
</th>
<th scope="col" class="lighter_grey">
<div style="width: 90px;">Start Date</div>
</th>
<th scope="col" class="lighter_grey">
<div style="width: 90px;">End Date</div>
</th>
<th scope="col" class="lighter_grey">
<div style="width:90px;">Delay (in Days)</div>
</th>



</tr>

</tbody></table>


<?php
if(count($days_count)>0)
{ 
	foreach ($days_count as $month => $days)
	{
	?>
<th class="text-center lighter_grey" colspan="<?php echo $days;?>"><?php echo $month;?></th>
<?php } } ?>
</tr>

<?php
if(count($Dates)>0)
{
	foreach($Dates as $date)
	{
		$week=$CI->Task_model->getWeekNumberFromDate($date);
	?>
<th class="text-center lighter_grey" style="z-index: 4"><?php echo $week;?></th>
<?php } } ?>
</tr>

</th>
<?php 
if(count($Dates)>0)
{
	foreach($Dates as $date)
	{

	$holiday=$CI->Task_model->checkifholiday($date);
	if($holiday==0)
	{
		$css='';
	}else
	{
		$css="";
	}
?>
<td scope="col" class="right light_grey <?php echo $css;?>">
<div style="width: 50px;"><?php echo date('d M Y',strtotime($date));?></div>
</td>
<?php } } ?>

</tr>
<tr>

<?php 
// echo "<pre>"; print_r($tasks); exit; 
if(count($tasks)>0)
{
	$i=1;
	foreach($tasks as $row)
	{
		$HOD=$CI->Task_model->getHOD($row['department_id']);


?>
<tr>
<th style="background-color: white; padding: 0px; border: none;" rowspan="2">
<table border="1">
<tbody><tr>
<td rowspan="2">
<div style="width: 30px;"><?php echo $i;?><?php //echo $row['task_id'];?></div>
</td>
<td rowspan="2">
<div style="width: 100px;"><?php echo ucwords(strtolower($row['department']));?></div>
</td>
<td rowspan="2">
<div style="width: 250px;"><?php echo ucwords(strtolower($row['task_name']));?></div>
</td>

<td rowspan="2">
<div style="width: 130px;"><?php echo ucwords(strtolower($HOD));?></div>
</td>
<td style="">
<div style="width: 50px;text-align: LEFT;font-size: 10px;">PLANNED</div>
</td>
<td>
<div style="width: 90px;"><?php echo date('d M Y',strtotime($row['start_date']));?></div>
</td>
<td>
<div style="width: 90px;"><?php echo date('d M Y',strtotime($row['end_date']));?></div>
</td>
<?php
$actualDone=$CI->Task_model->checkActualDoneStatus($row['task_id'],$df_id);
if($actualDone<>'')
{
	if(strtotime($actualDone)>strtotime($row['end_date']))
	{
	// $date_first = new DateTime(date('Y-m-d',strtotime($row['end_date'])));
	// $date_second = new DateTime(date('Y-m-d',strtotime($actualDone)));
	// $interval = $date_first->diff($date_second);
	// $delayDays=abs($interval->d)." Days";

		$earlier = new DateTime($actualDone);
$later = new DateTime($row['end_date']);

$delayDays = $later->diff($earlier)->format("%a")." Days"; //3
	}else
	{
		$delayDays='';
	}
}else
{
	$delayDays='';
}
?>
<td rowspan="2"><div style="width:90px;"><?php echo $delayDays;?> <?php //echo $row['end_date'];?><?php //echo date('Y-m-d',strtotime($actualDone));?></div></td>
</tr>

<tr>
<td class="" style="text-align: LEFT;font-size: 10px;">ACTUAL</td>
<td colspan="2"><?php echo $actualDone;?></td>


</tr>

</tbody></table>
</th>


<?php 
if(count($Dates)>0)
{
	foreach($Dates as $date)
	{
	
	$holiday=$CI->Task_model->checkifholiday($date);
	if($holiday==0)
	{
		$plannedWeeks=$CI->Task_model->get_week_numbers_between_dates($row['start_date'],$row['end_date']);
		//echo $row['start_date'].'<br/>'.$row['end_date']; exit;
		//echo "<pre>"; print_r($plannedWeeks); exit;
		$currentDateweek=$CI->Task_model->getWeekNumberFromDate($date);
		

		if(in_array($currentDateweek,$plannedWeeks))
		{
			$css="blue";
		}else{
			$css="";
		}
	}else
	{
		$css="";
	}
?>
<td class="<?php echo $css;?>" style="color:black;"></td>
<?php 
} 
}
?>



</tr>
<tr>

<?php 
if(count($Dates)>0)
{
	$css='';
	foreach($Dates as $date)
	{
	$holiday=$CI->Task_model->checkifholiday($date);
	if($holiday==0)
	{
		$actual=$CI->Task_model->checkActualStatus($row['task_id'],$df_id);
		//echo "<pre>"; print_r($actual); exit;
		if(count($actual)>0)
		{
			$css='';
			$task_status=$actual[0];
			$assigned_on=$actual[1];
			$completedOn=$actual[2];
			$tstdate=$actual[3];
			$tsetdate=$actual[4];
			// $betweenDate=$CI->Task_model->checkDateInBetween($date,$row['start_date'],$row['end_date']);
			 $betweenDate=$CI->Task_model->is_date_in_week($date,$row['start_date']);
			 $betweenDate1=$CI->Task_model->is_date_in_week($date,$row['end_date']);
			if($task_status==1)
			{
				$currentDateweek=$CI->Task_model->getWeekNumberFromDate($date);
				$weeksplanned=$CI->Task_model->getWeekNumberFromDate($row['end_date']);
				$weeksplannedStart=$CI->Task_model->getWeekNumberFromDate($row['start_date']);

				$completedWeek=$CI->Task_model->getWeekNumberFromDate(date('Y-m-d',strtotime($completedOn)));

				$allplannedweeks=$CI->Task_model->get_week_numbers_between_dates($row['start_date'],$row['end_date']);
				$allcompletedWeeks=$CI->Task_model->get_week_numbers_between_dates($row['start_date'],date('Y-m-d',strtotime($completedOn)));

				// echo "<pre>"; print_r($allplannedweeks)."<br>".print_r($allcompletedWeeks);

				$previousmonday=$CI->Task_model->get_previous_monday($row['start_date']);

				$previousmondayEND=$CI->Task_model->get_previous_monday(date('Y-m-d',strtotime($completedOn)));
				if((in_array($currentDateweek,$allplannedweeks)))
				{
					$css="green";
				}else
				{
					 $b1=count($allplannedweeks);
					 $b2=count($allcompletedWeeks); 
					 if($row['task_id']==87)
					 {
					 	//echo $previousmonday."<br/>".$date;exit;
					 }
					if($b1<$b2 && strtotime($previousmonday)<=strtotime($date) && strtotime($previousmondayEND)>=strtotime($date))
					{
						$css="red";
					}else
					{
					$css='';
					}
				}

				/** CHECK FOR DELAY **/

				// if($betweenDate==1 || $betweenDate1==1)
				// {
				// $css="green";
				// }

			// $is_lie=$CI->Task_model->is_date_in_week($date,date('Y-m-d',strtotime($completedOn)));

			// if($is_lie==1)
			// {
			// 	$css="green";
			// }else
			// {
			// 	$css="red";
			// }
			// if(strtotime(date('Y-m-d',strtotime($completedOn)))>strtotime($tsetdate))
			// {

			// 	$allDatesExceed=$CI->Task_model->GetallDates($tsetdate,date('Y-m-d',strtotime($completedOn)));
			// 	array_shift($allDatesExceed);
			// 	// Reset the keys
			// 	$allDatesExceed1 = array_values($allDatesExceed);
			// 	if(in_array($date,$allDatesExceed))
			// 	{
			// 		$css="red";
			// 	}
			// }
			}else
			{
				// not done 
				// start date monday 
				$previousmondaySTART=$CI->Task_model->get_previous_monday($row['start_date']);
				//end date monday 
				$previousmondayEND=$CI->Task_model->get_previous_monday($row['end_date']);
				// today monday
				$previousmondaytoday=$CI->Task_model->get_previous_monday(date('Y-m-d'));

				if(strtotime($previousmondaytoday)>strtotime($previousmondayEND))
				{
					//if(strtotime($previousmondayEND)<=strtotime($date) && strtotime($previousmondaytoday)>=strtotime($date))
					if(strtotime($date)>=strtotime($previousmondaySTART) && strtotime($date)<=strtotime($previousmondaytoday))
					{
					$css="red";
					}else
					{
						$css="";
					}
				}else
				{
					$css="";
				}

				// if(strtotime($date)>strtotime($tsetdate) && strtotime($date)<=strtotime(date('Y-m-d')))
				// {
				// 	$css="red";
				// }

			}
		}


	}else
	{
		$css="";
	}
?>
<td class="<?php echo $css;?>" style="color:black;"></td>
<?php } } ?>





</tr>

<?php $i++;
} } ?>




</thead>
</table>
</div>

</div>





<!-- Footer -->
<!-- End Footer -->
</div>

</div>
</div>


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header text-center">
<h3 id="exampleModalLabel text-center">Remarks for Stage <span id="stagrmk"></span></h3>

</div>
<div class="modal-body" id="modalbody">

</div>
<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
</div>
</div>
</div>

<script type="text/javascript">
// function view_remarks(order_won_id, supplier_stage_id, supplier_gc_id,partid) {
// $.ajax({
// type:"post",
// url:"https://hongyijig.in/index.php/Open_forms/view_supplier_remarks_new",
// data: { 
// order_won_id: order_won_id,
// supplier_stage_id: supplier_stage_id,
// supplier_gc_id: supplier_gc_id,
// partid: partid
// },
// success:function(data) {

// $.ajax({
// type:"post",
// url:"https://hongyijig.in/index.php/Open_forms/get_stages_data",
// data: { 
// stage_id: supplier_stage_id,
// order_won: order_won_id,
// partid:partid
// },
// success:function(data) {
// var dd=data.split('~');

// $("#pstage").text(dd[0]);
// $(".statusbar").css('width',dd[3]+"%");
// $(".statusbar").text(dd[3]+"% completed");

// }
// });


// $("#remarks_modal").modal('show');
// $("#show_remarks").html(data);

// }
// });
// }


function open_popup(order_won_id,flag2,partid,mouldid,supplier_stage_id,input_type,supplier_gc_id)
{

$.ajax({
type:"post",
url:"https://hongyijig.in/index.php/Open_forms/get_all_remarks",
data: { 
order_won_id: order_won_id,
partid: partid,
mouldid:mouldid,
supplier_stage_id: supplier_stage_id,
input_type:input_type,
supplier_gc_id:supplier_gc_id
},
success:function(data) {
var d=data.split('~');
$("#exampleModal").modal('show');
$("#modalbody").html(d[0]);
$("#stagrmk").html(d[1]);

}
});

}

function filter_Gant()
{
var filter=$('#filter').val();
document.location="<?php echo page_url;?>Task/dfgantchartSharmaji/<?php echo $this->uri->segment(3);?>/"+filter;
}


function switchData() {
        var swi = $("#switch").val();
        if (swi == 1) {
        window.location = "https://pms.shubhampack.in/index.php/Task/dfgantchartSharmaji/<?php echo $this->uri->segment(3);?>/";
        } else if (swi == 2) {
        window.location = "https://pms.shubhampack.in/index.php/Task/dfgantchartNew/<?php echo $this->uri->segment(3);?>/";
        } else if(swi==3) {
        window.location = "https://pms.shubhampack.in/index.php/Task/dfgantchartDepartmentwise/<?php echo $this->uri->segment(3);?>";
        }else
        {
             window.location = "https://pms.shubhampack.in/index.php/Task/finalgantchart/<?php echo $this->uri->segment(3);?>";
        }
        }

</script>

</body>

</html>