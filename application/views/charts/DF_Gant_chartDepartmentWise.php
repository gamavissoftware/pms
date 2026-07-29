<?php 
$CI =& get_instance();
$CI->load->model('Task_model');
$df_id=$this->uri->segment(3);
$department=$this->uri->segment(4);
$date_range=$CI->Task_model->getWeekStartDates($df_id,$department);
$tasks=$CI->Task_model->getDFTaskScheduledDepartmentWise($df_id,$department);
//echo "<pre>"; print_r($tasks); exit;
if(count($date_range)>0)
{
	$stdate=$date_range[0];
	$etdate=$date_range[1];
	//echo $stdate."<br/>".$etdate; exit;
	$Dates=$CI->Task_model->GetallDates($stdate,$etdate);
	$days_count=$CI->Task_model->getDaysCountForEachMonth($stdate,$etdate);
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

.start-delay
{
	  background-color: #ffa500 !important;
}
.holiday{
	background-color: #d3d3d3 !important;
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
height: 800px;
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
<h1>Department Wise DF Progress Gant Chart</h1>
</div>
</div>
</div>

<div class="row">
<div class="col">
	<select id="switch" onchange="switchData();">
		<option value="3">Department Wise</option>
		<option value="2">Day Wise</option>
		<option value="1">Week Wise</option>
		<option value="4">Department & Week Wise</option>
		
	</select>

</div>
<!-- <div class="col">
	<select name="filter" id="filter" class="form-control" onchange="filter_Gant();">
		<option value="" selected="">All Departments</option>
				<option value="20">ACCOUNTS</option>
				<option value="18">ASSEMBLY</option>
				<option value="14">AUTOMATION</option>
				<option value="11">DESIGN</option>
				<option value="19">DISPATCH</option>
				<option value="21">ELECTRICAL</option>
				<option value="9">MARKETING</option>
				<option value="12">PRODUCTION/PPC</option>
				<option value="13">PURCHASE</option>
				<option value="15">QUALITY CONTROL</option>
				<option value="22">SERVICE</option>
				<option value="16">STORE</option>
				<option value="33">TRIAL</option>
			</select>
</div> -->
<div class="col"></div>


</div>
<div class="row" style="margin-top:20px;">
	
	<div class="col">
	<table border="1" id="myTable" class="table table-bordered" style="padding:10px;background-color: #efefef;">
	<tr style="font-size:16px;">
	<th>DF NO<br/><span style="color:brown;"><?php echo $runningdfno;?><span></th>
	<th>Company Name<br/><span style="color:brown;"><?php echo $companyname;?><span></th>
	<th>DF Release Date<br/><span style="color:brown;"><?php echo $df_addedOn;?></span></th>
	<th>Marketing Person<br/><span style="color:brown;"><?php echo $marketing;?></span></th>
	</tr>
	</table>
	</div>


	<div class="col">
	<table border="1" class="table table-bordered" style="padding:10px;background-color: #efefef;">
	<tr style="font-size:16px;">
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
<th style="background-color:white;padding: 0; vertical-align: bottom;" rowspan="2">
<table>
<tbody><tr>
<td style="text-align: center; border-bottom: 1px solid lightgray; font-size: 20px;">
Indicators</td>
</tr>
</tbody></table>
 <table style="">
<tbody><tr>
<td class="blue" width="10%"></td>
<td class="" width="10%">Planned</td>
<td class="start-delay" width="10%"></td>
<td class="" width="10%">Start Delay</td>
<td class="green" width="10%"></td>
<td class="" width="10%">Actual</td>
<td class="red" width="10%"></td>
<td class="" width="10%">End Delay</td>
<td class="holiday" width="10%"></td>
<td class="" width="10%">Holiday</td>

</tr>




</tbody></table>




<table style="width: 100%;" border="1">


<tbody>
<tr>
<th scope="col" class="lighter_grey">
<div style="width: 30px;height: 60px;">Sr. No.</div>
</th>
<th scope="col" class="lighter_grey">
<div style="width: 120px;height: 60px;">Department</div>
</th>
<!-- <th scope="col" class="lighter_grey">
<div style="width: 50px;height: 60px;">PLN<br/>ACT</div>
</th> -->
<th scope="col" class="lighter_grey">
<div style="width: 110px;height: 60px;">PLN ST. Date<br/>PLN ED. Date</div>
</th>
<th scope="col" class="lighter_grey">
<div style="width: 110px;height: 60px;">ACT ST. Date<br/>ACT ED Date</div>
</th>

<th scope="col" class="lighter_grey">
<div style="width:90px;height: 60px;">ST. Delay <br/> ED. Delay</div>
</th>
<th scope="col" class="lighter_grey">
<div style="width:90px;height: 60px;">Work Completed %</div>
</th>
</tr>

</tbody></table>
</th>
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

	$holiday=$CI->Task_model->checkifholiday($date);
	if($holiday==0)
	{
		$css='';
	}else
	{
		$css="holiday";
	}
?>
<td scope="col" class="right light_grey <?php echo $css;?>">
<div style="width: 50px;"><?php echo date('d M Y',strtotime($date));?></div>
</td>
<?php } } ?>
<tr>



</tr>



<?php 
if(count($tasks)>0)
{
	$i=1;
	foreach($tasks as $row)
	{
		$HOD=$CI->Task_model->getHOD($row['department_id']);

		$actualSTETDate=$CI->Task_model->getActualStart_End_Date($row['department_id'],$df_id);
		if(count($actualSTETDate)>0)
		{
			$actualStartDate=$actualSTETDate[0];
			$allCompleted=$actualSTETDate[1];
			$actualEndDate=$actualSTETDate[2];
			$completion_per=$actualSTETDate[3];

			if($actualSTETDate[0]<>'')
			{
			$actualStats=date('d M Y',strtotime($actualStartDate))."<br/>";
			}else
			{
				$actualStats='Not Started';
			}

			if($actualStartDate<>'')
			{
			if($actualEndDate<>''){  
			$actualStats.=date('d M Y',strtotime($actualEndDate)); 
			}else{ 
			$actualStats.='Ongoing'; 
			} 
			}else
			{
				$actualStats.=''; 
			}

			if(strtotime($row['start_date'])<strtotime($actualStartDate))
			{
			$startdelay=$CI->Task_model->getDateDiffInDays(date('Y-m-d',strtotime($row['start_date'])),date('Y-m-d',strtotime($actualStartDate)));
			$startdelay=$startdelay." Days";
			}else
			{
				$startdelay="0 Days";
			}

			if($actualEndDate<>'' && strtotime($row['end_date'])<strtotime($actualEndDate))
			{
				$enddelay=$CI->Task_model->getDateDiffInDays(date('Y-m-d',strtotime($row['end_date'])),date('Y-m-d',strtotime($actualEndDate)));
				$enddelay=$enddelay." Days";
			}else
			{
				$enddelay="NA";
			}

		}else
		{
			$actualStartDate='';
			$allCompleted=0;
			$actualEndDate='';
			$actualStats='';
			$startdelay='';
			$enddelay='';
			$completion_per=0;
		}
?>
<tr>
<th style="background-color: white; padding: 0px; border: none;" rowspan="2">
<table border="1">
<tbody>
<tr>
<td rowspan="2">
<div style="width: 30px;height: 60px;"><?php echo $i;?><?php //echo $row['task_id'];?></div>
</td>
<td rowspan="2">
<div style="width: 120px;height: 60px;"><?php echo ucwords(strtolower($row['department']));?><br/><?php echo ucwords(strtolower($HOD));?></div>
</td>

<!-- <td style="">
<div style="width: 50px;height: 60px;text-align: center;">PLN</div>
</td> -->
<td>
<div style="width: 110px;height: 60px;"><?php echo date('d M Y',strtotime($row['start_date']));?><br/><?php echo date('d M Y',strtotime($row['end_date']));?></div>
</td>
<td>
<div style="width: 110px;height: 60px;"><?php echo $actualStats;?></div>
</td>

<td>
	<div style="width:90px;height: 60px;"><?php echo $startdelay;?><br/><?php echo $enddelay;?></div>
</td>
<td>
	<div style="width:90px;height: 60px; font-weight: bold;coor:red;"><i><u><a href='javascript:;' onclick="getData(<?php echo $df_id;?>,<?php echo $row['department_id'];?>);"><?php echo $completion_per;?>%</a></u></i>
		<input type="hidden" class="dep" value="<?php echo $row['department_id'];?>"></div>
</td>

</tr>

</tbody></table>
</th>

<?php 
$tttaskid=array();
if(count($Dates)>0)
{
	foreach($Dates as $date)
	{
	
	$holiday=$CI->Task_model->checkifholiday($date);
	if($holiday==0)
	{
		$planned=$CI->Task_model->checkDateInBetween($date,$row['start_date'],$row['end_date']);

		if($planned==1)
		{
			$css="blue";

			$tname=$CI->Task_model->CheckTaskLieInTheDate($date,$row['department_id'],$df_id);
	foreach($tname as $tt)
	{
		if(!in_array($tt,$tttaskid))
		{
			$tttaskid[]=$tt;
			$word=$CI->Task_model->GetTaskName($tt);
		}else
		{
			$word=$word;

		}
	}



		}else{
			$css="";
			$tt='';
		}
	}else
	{
		$css="holiday";
		$tt='';
	}

	
?>
<td class="<?php echo $css;?> planningtd<?php echo $row['department_id'];?>" style="color:black;"><?php //echo $tt;?></td>
<?php 
} 
}
?>

</tr>
<tr>

	<?php 
if(count($Dates)>0)
{
	foreach($Dates as $date)
	{
	$holiday=$CI->Task_model->checkifholiday($date);
	if($holiday==0)
	{
		
		$actual=$CI->Task_model->checkwhenWorkStarted_n_Completed($row['department_id'],$df_id);
		if(count($actual)>0)
		{
			$startDate=$actual[0];
			$endDate=$actual[1];

		$actualData=$CI->Task_model->checkDateInBetween($date,date('Y-m-d',strtotime($startDate)),date('Y-m-d',strtotime($endDate)));

		if($actualData==1)
		{
			$css="green";


			if(strtotime(date('Y-m-d',strtotime($endDate)))>strtotime($row['end_date']))
			{

			$allDatesExceed=$CI->Task_model->GetallDates($row['end_date'],date('Y-m-d',strtotime($endDate)));
			array_shift($allDatesExceed);
			// Reset the keys
			$allDatesExceed1 = array_values($allDatesExceed);
			if(in_array($date,$allDatesExceed))
			{
			$css="red";
			}
			}



		}else{

			$css="";

			if(strtotime($date)>=strtotime($row['start_date']) && strtotime($date)<=strtotime($startDate))
			{
				$css="start-delay";
			}


		}


		}



	}else
	{
		$css="holiday";
	}
?>
 <td class="<?php echo $css;?>" style="color:black;"></td> 
<?php } } ?>


</tr>

<?php  $i++; } } ?>





</thead>
</table>
</div>

</div>





<!-- Footer -->
<!-- End Footer -->
</div>

</div>


<div class="modal fade" id="largeModal" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel" class="text-center"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="deparmentWiseDetails">
       
      </div>
     
    </div>
  </div>
</div>


<div class="modal fade" id="largeModalticket"  role="dialog" aria-labelledby="basicModal" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel1" class="text-center"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="enableModal();">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="ticketdetail">
       
      </div>
     
    </div>
  </div>
</div>

<script>
	function getData(dfid,department_id)
	{
		$("#largeModal").modal('show');
		$.ajax({
        type:"post",
        url:"<?php echo page_url;?>Task/getDataforDepartmentTask",
        data:"dfid="+dfid+"&departmentid="+department_id,
        success:function(data){
        	var d=data.split('~');
         $("#deparmentWiseDetails").html(d[0]);
         $("#myModalLabel").html('Tasks Detail For '+d[1]+' Department For DF No. '+d[2]);
    
        }
    });
	}

	function showhelptickets(dfid,department,taskid)
	{

		$("#largeModalticket").modal('show');
		$.ajax({
		type:"post",
		url:"<?php echo page_url;?>Task/gethelptickets",
		data:"dfid="+dfid+"&departmentid="+department+"&taskid="+taskid,
		success:function(data){
		$("#ticketdetail").html(data);
		$("#largeModal").css('display','none');
		}
		});


	}

	function enableModal()
	{	
		// alert('hi');
		$("#largeModal").css('display','block');
	}



	 function mergeTableColumns(table) {

	 	$('.planningtd td').each(function() {
	 		alert('hi');
	 	});
            // $(table + 'tr').each(function() {
            //     var previous = null, cellToExtend = null, colspan = 1;
            //     $(this).find('td').each(function() {
            //         var jthis = $(this), content = jthis.text();
            //         if (previous === content && content !== "") {
            //             //jthis.addClass('hidden');
            //             cellToExtend.attr("colspan", (colspan = colspan + 1));
            //         } else {
            //             colspan = 1;
            //             previous = content;
            //             cellToExtend = jthis;
            //         }
            //     });
            // });
            //$('td.hidden').remove();
        }


        $(document).ready(function() {
        	
			$('.dep').each(function() {
			var depvalue=$(this).val();
			var lastValue = null;
			var count = 1;
			$('.planningtd'+depvalue).each(function(index) {
				
			var $cell = $(this);
			if (index === 0) {
			lastValue = $cell.text();
			return true; // Continue to the next iteration
			}

			if ($cell.text() === lastValue && $cell.text()!='') {
			count++;
			// $cell.prev().attr('colspan', count);
			// $cell.prev().text(data);
			// $cell.remove();
			
			// var tid=$cell.text();
			// $.ajax({
            // type:"post",
            // url:"<?php echo page_url;?>Task/GetTaskName",
            // data:{taskid: tid},
            // success:function(data){
           	
                
            // }
            // });




			} else {
			lastValue = $cell.text();
			count = 1;
			}


			});
			});



        });

        function switchData()
{
	var swi=$("#switch").val();
	if(swi==1)
	{
		window.location="<?php echo page_url;?>Task/dfgantchartSharmaji/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>";
	}else if(swi==2)
	{
		window.location="<?php echo page_url;?>Task/dfgantchartNew/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>";
	}else 
	if(swi==3) {
        window.location = "<?php echo page_url;?>Task/dfgantchartDepartmentwise/<?php echo $this->uri->segment(3);?>";
        }else
        {
             window.location = "<?php echo page_url;?>Task/finalgantchart/<?php echo $this->uri->segment(3);?>";
        }

}
</script>

</body>

</html>