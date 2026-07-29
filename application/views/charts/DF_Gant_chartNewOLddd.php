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
	$Dates=$CI->Task_model->GetallDates($stdate,$etdate);
	$days_count=$CI->Task_model->getDaysCountForEachMonth($stdate,$etdate);
}else
{
	echo "Date Not Found"; exit;
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="GAMAVIS SOFTECH PRIVATE LIMITED">

<link rel="shortcut icon" href="https://hongyijig.in/assets/images/favicon.ico">

<title>PROJECT UPDATES |  SUPPLIER GANTT CHART</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js">
</script> 

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/js/bootstrap.min.js" integrity="sha512-7rusk8kGPFynZWu26OKbTeI+QPoYchtxsmPeBqkHIEXJxeun4yJ4ISYe7C6sz9wdxeE1Gk3VxsIWgCZTc+vX3g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<style>

.holiday{
	background-color: #FFD366 !important;
}
.completed{
background-color: #ECEBC9;
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
margin: 47px 0;
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
background-color: red;
color: white;
text-align: center;
}

.yellow {
background-color: yellow;
color: black;
text-align: center;
}

.green {
background-color: #2b7723;
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
background-color: #649fe1;
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
<div class="col"></div>
<div class="col">
	<select name="filter" id="filter" class="form-control" onchange="filter_Gant();">
		<option value="" <?php if($this->uri->segment(4)==''){?> selected <?php } ?>>All Departments</option>
		<?php 
			$rest=$this->db->select('department_id,department')->from('departments')->where('status',1)->where('business_loc_id',2)->get();
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
<div class="col"></div>
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
<table style="margin-top: 10px;">
<tbody><tr>
<td class="blue" width="10%"></td>

<td class="" width="20%">Planned</td>

<td class="" width="10%"></td>

<td class="holiday" width="10%"></td>

<td class="" width="20%">Holiday</td>

</tr>

<tr style="margin-top:20px;">
<td class="green" width="10%"></td>

<td class="" width="20%">Actual</td>

<td class="" width="10%"></td>

<td class="red" width="10%"></td>

<td class="" width="20%">Delay</td>

</tr>



</tbody></table>





<table style="width: 100%;" border="1">


<tbody>
<tr>
<th scope="col" class="right nxt">
<div style="width: 30px;">Sr. No.</div>
</th>
<th scope="col" class="right nxt">
<div style="width: 100px;">Department</div>
</th>
<th scope="col" class="right nxt">
<div style="width: 100px;">Task</div>
</th>
<th scope="col" class="right nxt">
<div style="width: 130px;">HOD</div>
</th>
<th scope="col" class="right nxt">
<div style="width: 50px;">Status PLN Vs ACT</div>
</th>
<th scope="col" class="right nxt">
<div style="width: 90px;">Start Date</div>
</th>
<th scope="col" class="right nxt">
<div style="width: 90px;">End Date</div>
</th>

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

</tr>
<tr>
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
<div style="width: 30px;"><?php echo $i;?></div>
</td>
<td rowspan="2">
<div style="width: 100px;"><?php echo ucwords(strtolower($row['department']));?></div>
</td>
<td rowspan="2">
<div style="width: 100px;"><?php echo ucwords(strtolower($row['task_name']));?></div>
</td>

<td rowspan="2">
<div style="width: 130px;"><?php echo ucwords(strtolower($HOD));?></div>
</td>
<td >
<div style="width: 50px;">PLN</div>
</td>
<td>
<div style="width: 90px;"><?php echo date('d M Y',strtotime($row['start_date']));?></div>
</td>
<td>
<div style="width: 90px;"><?php echo date('d M Y',strtotime($row['end_date']));?></div>
</td>


</tr>
<tr>
<td class="">ACT</td>
<td></td>
<td></td>

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
		$planned=$CI->Task_model->checkDateInBetween($date,$row['start_date'],$row['end_date']);

		if($planned==1)
		{
			$css="blue";
		}else{
			$css="";
		}
	}else
	{
		$css="holiday";
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
	foreach($Dates as $date)
	{
	$holiday=$CI->Task_model->checkifholiday($date);
	if($holiday==0)
	{
		$planned=$CI->Task_model->checkDateInBetween($date,$row['start_date'],$row['end_date']);

		if($planned==1)
		{
			$css="";
		}else{
			$css="";
		}
	}else
	{
		$css="holiday";
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
document.location="<?php echo page_url;?>Task/dfgantchartNew/<?php echo $this->uri->segment(3);?>/"+filter;
}
</script>

</body>

</html>