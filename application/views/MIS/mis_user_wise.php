<?php 
$CI =& get_instance();
$CI->load->model('MIS_model','mis_model');
$user_id=$this->uri->segment(6);
$fname=$_SESSION['logged_in']['user_name'];
$lname=$_SESSION['logged_in']['last_name'];
$st=$this->uri->segment(4) ?: date('Y-m-d', strtotime('-1 year +1 day'));
$et=$this->uri->segment(5) ?: date('Y-m-d');
$dfid = $this->uri->segment(3) ?: 'ALL';
$user_role = $this->session->userdata['logged_in']['role'];
$totaldata = array();
$totaldata[] = 0;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="<?php echo copyright; ?>">

<link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

<title><?php echo sitetitle; ?> Userwise MIS</title>

<!-- Table Responsive css -->
<script src="<?php echo assets_url;?>js/angular.min.js"></script>
<!-- DataTables -->
<link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
<!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" rel="stylesheet"/>
<script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
<style>

#pageloader
{
background: rgba( 255, 255, 255, 0.8 );
display: none;
height: 100%;
position: fixed;
width: 100%;
z-index: 9999;
}
#pageloader img
{
left: 30%;
margin-left: -10px;
margin-top: -10px;
position: absolute;
top: 30%;
}
.stepwizard-step p {
margin-top: 10px;
}
.stepwizard-row {
display: table-row;
}
.stepwizard {
display: table;
width: 50%;
position: relative;
}
.stepwizard-step button[disabled] {
opacity: 1 !important;
filter: alpha(opacity=100) !important;
}
.stepwizard-row:before {
top: 14px;
bottom: 0;
position: absolute;
content: " ";
width: 100%;
height: 1px;
background-color: #ccc;
z-order: 0;
}
.stepwizard-step {
display: table-cell;
text-align: center;
position: relative;
}
.btn-circle {
width: 30px;
height: 30px;
text-align: center;
padding: 6px 0;
font-size: 12px;
line-height: 1.428571429;
border-radius: 15px;
}
table{

    font-size: 12px !important;

}
.appraisal-summary { font-size: 14px; font-weight: 700; }
.performance-badge { display:inline-block; min-width:145px; padding:8px 12px; border-radius:18px; color:#fff; font-weight:700; }
.performance-excellent { background:#159957; }
.performance-average { background:#f0a202; }
.performance-poor { background:#cf3f3f; }
.performance-legend { margin:0 0 15px; color:#58616b; }
.performance-legend span { margin-right:14px; white-space:nowrap; }
.performance-dot { display:inline-block; width:10px; height:10px; border-radius:50%; margin-right:4px; }
</style>

</head>


<body>


<!-- Navigation Bar-->
<header id="topnav">
<?php $this->load->view('common/nav-menu');?>
</header>
<!-- End Navigation Bar-->


<div class="wrapper">
<div class="container">

<!-- Page-Title -->
<div class="divheight hidden-xs"></div>
<div class="row">
<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
<div class="page-title-box">

<h4 class="page-title text-center">MIS DASHBOARD</h4>
</div>
</div>
</div>
<?php
	$can_filter_appraisal = in_array((int) $user_role, array(1, 12, 68, 84), true);
	if($can_filter_appraisal){?>

<form name="frm" action="<?php echo page_url;?>MIS/filter_mis" method="post">
<div class="row">
<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 card-box">

<div class="col-md-3">
	<div class="form-group">
		<label>FILTER BY DF NO</label>
		<select class="form-control multipleselect" name="filterbydf" id="filterbydf">
			<option value="ALL" <?php if($dfid=='ALL'){echo "selected";}?>>ALL</option>
			<?php 
				$q = $this->db->select('df_no, id, df_description')->from('df_release')->where('df_status',0)->where('on_hold',0)->order_by('id','desc')->get();
				if($q->num_rows()>0){
					foreach($q->result() as $row){
			?>
			<option value="<?php echo $row->id;?>" <?php if($this->uri->segment(3)==$row->id){echo "selected";}?> ><?php echo $row->df_no;?> <?php echo $row->df_description;?></option>
		<?php } }?>
		</select>
	</div>
</div>
<div class="col-md-2">
<div class="form-group">
<label>START DATE</label>
<input type="date" name="start_date" id="start_date" class="form-control" required value="<?php echo $st;?>">
</div>
</div>
<div class="col-md-2">
<div class="form-group">
<label>END DATE</label>
<input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo $et;?>"  required>
</div>
</div>

<div class="col-md-2">
<div class="form-group">
<label>DEPARTMENT</label>
<select name="department" id="department" class="form-control multipleselect" required onchange="GetUsers();">
<option value="ALL">ALL</option>
<?php 
$rtr = $this->db->select('c.department_id, c.department')->from('task_department_wise_scheduling a')->join('departments c','a.department_id=c.department_id','left')->where('c.business_loc_id',2)->where('c.status',1)->group_by('c.department_id')->get();
if($rtr->num_rows()>0)
{
foreach($rtr->result() as $rtr1)
{ 

?>
<option value="<?php echo $rtr1->department_id;?>" <?php if($this->uri->segment(7)==$rtr1->department_id){?> selected <?php } ?>><?php echo $rtr1->department;?></option>
<?php } }?>
</select>
</div>
</div>
<script>
	
	function GetUsers()
	{
		var department=$("#department").val();

		$.ajax({
		url: '<?php echo page_url;?>MIS/getUsers',
		type: 'post',
		data: {department: department},
		success: function(data){
		$("#user").html(data);
		}
		});


	}
</script>


<div class="col-md-2">
<div class="form-group">
<label>EMPLOYEE</label>
<select name="user" id="user" class="form-control multipleselect" required>
<option value="ALL">ALL</option>
<?php 
 $this->db->select('b.user_id,b.first_name,b.last_name, c.department')->from('task_department_wise_scheduling a')->join('system_users b','a.assigned_user=b.user_id','left')->join('departments c','b.department_id=c.department_id','left')->where('b.business_location',2)->where('b.user_role_id!=',1)->where('b.user_status',1)->where('user_role_id!=',12);
 if($this->uri->segment(7)<>'' && $this->uri->segment(7)<>'ALL')
 {
 	$this->db->where('b.department_id',$this->uri->segment(7));
 }
 	$rtr =$this->db->group_by('a.assigned_user')->get();
if($rtr->num_rows()>0)
{
foreach($rtr->result() as $rtr1)
{ 

?>
<option value="<?php echo $rtr1->user_id;?>" <?php if($this->uri->segment(6)==$rtr1->user_id){?> selected <?php } ?>><?php echo $rtr1->first_name;?>&nbsp;<?php echo $rtr1->last_name;?></option>
<?php } }?>
</select>
</div>
</div>
<div class="col-md-1 text-center">
	<div class="form-group" style="margin-top: 23px;"><input type="submit" value="Filter" name="sub" id="sub" class="btn btn-success"></div></div>
<div class="col-md-12 text-right">
	<button type="button" id="annualPeriod" class="btn btn-primary btn-sm">Last 12 Months</button>
	<small style="margin-left:8px;color:#666;">Annual appraisal period is the default.</small>
</div>

</div>
</div>
</form>
<script>
document.addEventListener('DOMContentLoaded', function () {
	var form = document.forms.frm;
	if (!form) return;
	var annualButton = document.getElementById('annualPeriod');
	if (annualButton) annualButton.addEventListener('click', function () {
		var end = new Date();
		var start = new Date(end.getFullYear() - 1, end.getMonth(), end.getDate() + 1);
		function iso(date) {
			var month = String(date.getMonth() + 1); if (month.length < 2) month = '0' + month;
			var day = String(date.getDate()); if (day.length < 2) day = '0' + day;
			return date.getFullYear() + '-' + month + '-' + day;
		}
		document.getElementById('start_date').value = iso(start);
		document.getElementById('end_date').value = iso(end);
		form.submit();
	});
	form.addEventListener('submit', function (event) {
		var start = document.getElementById('start_date').value;
		var end = document.getElementById('end_date').value;
		if (start && end && start > end) {
			event.preventDefault();
			alert('Start Date cannot be after End Date.');
		}
	});
});
</script>
<?php }?>




<!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

<div class="performance-legend">
	<strong>Performance classification:</strong>
	<span><i class="performance-dot performance-excellent"></i>Excellent: 90–100%</span>
	<span><i class="performance-dot performance-average"></i>Average: 75–89%</span>
	<span><i class="performance-dot performance-poor"></i>Needs Improvement: below 75%</span>
</div>

<div class="row" style="margin-top:20px;">

<?php 
// $this->db->select('b.user_id,b.first_name,b.last_name, c.department,b.department_id')->from('task_department_wise_scheduling a')->join('system_users b','a.assigned_user=b.user_id','left')->join('departments c','b.department_id=c.department_id','left')->where('b.business_location',2)->where('b.user_role_id!=',1)->where('b.user_status',1)->group_by('a.assigned_user');
$this->db->select('b.user_id,b.first_name,b.last_name, c.department,b.department_id')->from('system_users b')->join('departments c','b.department_id=c.department_id','left')->where('b.business_location',2)->where('b.user_role_id!=',1)->where('b.user_status',1)->where('b.user_role_id!=',12);

if($this->uri->segment(6)<>'ALL' && $this->uri->segment(6)<>'')
{
$this->db->where('user_id',$user_id);
}

if($this->uri->segment(7)<>'ALL' && $this->uri->segment(7)<>'')
{
$this->db->where('b.department_id',$this->uri->segment(7));
}

$rtr=$this->db->get();
if($rtr->num_rows()>0)
{
foreach($rtr->result() as $rtr11)
{ 
$user_id=$rtr11->user_id;
//echo $user_id; exit;
$fname=$rtr11->first_name;
$lname=$rtr11->last_name;
$department_id=$rtr11->department_id;
$assigned_module=array();
$assigned_module[]=0;
?>

<div class="col-xs-12">
<div class="card-box">
<!--<div id="pageloader">
<img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
</div>	-->



<div class="container-fluid">
<div class="row">
<table class="table table-bordered text-center" style="font-size:15px">
<thead>
<tr style="background-color: #f2f0ec;">
<th class="text-center">EMPLOYEE NAME</th>
<th  class="text-center">MODULE</th>
<th  class="text-center">PARAMETER</th>
<th  class="text-center">TOTAL ASSIGNED/DONE</th>
<th  class="text-center">TOTAL DONE ON TIME</th>
<th  class="text-center">DIFF</th>
<th  class="text-center">% OF WORK NOT DONE & DELAYED</th>
</tr>
</thead>
<tbody>
	<?php 
	if($department_id==9)
	{
		$cs="11";
	}else
	{
		$cs="10";
	}
	?>


<tr>
<td rowspan="<?php echo $cs;?>"><?php echo strtoupper($fname);?> <?php echo strtoupper($lname);?><br><br/>(<?php echo strtoupper($rtr11->department);?>)<?php //echo $user_id;?></td>
<td rowspan="2">TASK</td>
<td>% OF WORK NOT DONE<br/><span style="color:grey;font-weight: bold;">(Assigned vs completed tasks. Tasks with open help tickets are considered completed.)</span></td>
<td><?php $totalassigned = $CI->mis_model->allassignedtask($user_id,$st,$et,$dfid);
echo  $totalassigned;?></td>
<td><?php $totaldonetask =  $CI->mis_model->totalassignedworkdone($user_id,$st,$et,$dfid);
echo $totaldonetask;?></td>
<td><a href='javascript:;' onclick="gettaskmodel('<?php echo $user_id;?>','<?php echo $st;?>','<?php echo $et;?>',1,'<?php echo $dfid;?>');"><u><?php
$diff = $totalassigned-$totaldonetask;

 //$delayed =  $CI->mis_model->totalassignedworknotdone($user_id,$et,$dfid);
echo $diff; ?></u></a></td>
<td>
	<?php 
	$per2=$CI->mis_model->get_percentage($diff,$totalassigned);

	echo $per2;
	if($totalassigned>0)
	{
		$assigned_module[]=1;
	}
	?>

</td>
</tr>


<tr>
<td>% OF WORK DELAYED<br/><span style="color:grey;font-weight: bold;">(Task Done in week vs Task done on time.)</span></td>

<td><?php 
	$totaldonecount = $CI->mis_model->totaldonetaskwithdate($user_id, $st, $et,$dfid);
	echo $totaldonecount;
?></td>
<td><?php 
	$totaldoneontime = $CI->mis_model->totaldonetaskwithdateontime($user_id, $st, $et,$dfid);
	echo $totaldoneontime;
?></td>
<td><a href='javascript:;' onclick="gettaskmodel('<?php echo $user_id;?>','<?php echo $st;?>','<?php echo $et;?>',2,'<?php echo $dfid;?>');">
	<?php 
	$diff = $totaldonecount-$totaldoneontime;
	echo $diff ; 
?><u></a></td>
<td>
	<?php 
	
	$per3=$CI->mis_model->get_percentage($diff,$totaldonecount);
echo $per3;
if($totaldonecount>0)
	{
		$assigned_module[]=1;
	}
	?>
</td>

</tr>






<tr>
<td colspan="3"></td>
<td colspan="2" style="color:red; font-weight:bold;">AVERAGE DELAY <?php $avg =  $CI->mis_model->avgdelay($user_id, $st, $et, $dfid);
if($avg==''){echo "0";}else{echo round($avg,2);}?> DAYS</td>
<td></td>
</tr>
<tr>
<td>HELPTICKETS RAISED FOR YOU</td>
<td>% OF WORK NOT DONE</td>
<td><?php $totalassigned = $CI->mis_model->allassigneTickets($user_id,$dfid,$st,$et);
echo  $totalassigned;?></td>
<td><?php $totaldonetask =  $CI->mis_model->allassigneTicketsDone($user_id,$dfid,$st,$et);
echo $totaldonetask;?></td>
<td><a href='javascript:;' onclick="gettaskmodel('<?php echo $user_id;?>','<?php echo $st;?>','<?php echo $et;?>',3,'<?php echo $dfid;?>');"><u><?php
$diff = $totalassigned-$totaldonetask;

 //$delayed =  $CI->mis_model->totalassignedworknotdone($user_id,$et,$dfid);
echo $diff; ?></u></a></td>
<td>
	<?php 
	$per4=$CI->mis_model->get_percentage($diff,$totalassigned);

	echo $per4;

	if($totalassigned>0)
	{
		$assigned_module[]=1;
	}
	?>

</td>
</tr>

<tr>
<td>HELPTICKETS RAISED BY YOU (NOT CLOSED)</td>
<td>% OF WORK NOT DONE</td>
<td><?php $totalassigned = $CI->mis_model->allCreatedTickets($user_id,$dfid,$st,$et);
echo  $totalassigned;?></td>
<td><?php $totaldonetask =  $CI->mis_model->allcreatedTicketsDone($user_id,$dfid,$st,$et);
echo $totaldonetask;?></td>
<td><a href='javascript:;' onclick="gettaskmodel('<?php echo $user_id;?>','<?php echo $st;?>','<?php echo $et;?>',4,'<?php echo $dfid;?>');"><u><?php
$diff = $totalassigned-$totaldonetask;

 //$delayed =  $CI->mis_model->totalassignedworknotdone($user_id,$et,$dfid);
echo $diff; ?></u></a></td>
<td>
	<?php 
	$per5=$CI->mis_model->get_percentage($diff,$totalassigned);

	echo $per5;

	if($totalassigned>0)
	{
		$assigned_module[]=1;
	}
	?>

</td>
</tr>
<?php 
$per6=0;
?>
<?php if($department_id==9)
{
?>

<tr>
<td>MISSED FOLLOWUPS</td>
<td>% OF WORK NOT DONE</td>
<td><?php $totalassigned = $CI->mis_model->getTotalFollowupDone($user_id);
echo  $totalassigned;?></td>
<?php
$totalnotdonetask =  $CI->mis_model->getMissedFollowups($user_id,$dfid);
$done=$totalassigned-$totalnotdonetask;
?>
<td><?php 
echo $done;?></td>
<td><a href='javascript:;' onclick="gettaskmodelfollowup('<?php echo $user_id;?>');"><u><?php
 //$delayed =  $CI->mis_model->totalassignedworknotdone($user_id,$et,$dfid);
echo $totalnotdonetask; ?></u></a></td>
<td>
	<?php 
	$per6=$CI->mis_model->get_percentage($totalnotdonetask,$totalassigned);

	echo $per6;

	if($totalassigned>0)
	{
		$assigned_module[]=1;
	}
	?>

</td>
</tr>

<?php
}
?>


<?php 

$all_task=$CI->mis_model->all_task_delegated($st,$et,$user_id);
if(count($all_task)>0)
{
$all=count($all_task);
}else{
$all=0;
}
$all_task_done=$CI->mis_model->all_task_delegated_done($st,$et,$user_id);
if(count($all_task_done)>0)
{
$done=count($all_task_done);
}else{
$done=0;
}
$diff=$all-$done;
$per=$CI->mis_model->get_percentage($diff,$all);
if($per>0)
{
$sign="-";
}else{
$sign="";
}
// SECOND PARAMETER 

$all_task_done_this_week=$CI->mis_model->all_task_delegated_Done_this_week($st,$et,$user_id);
if(count($all_task_done_this_week)>0)
{
$all1=count($all_task_done_this_week);
}else{
$all1=0;
}

$all_task_delayed=$CI->mis_model->all_task_delegated_not_delayed($st,$et,$user_id);
if(count($all_task_delayed)>0)
{
$not_delayed=count($all_task_delayed);
}else{
$not_delayed=0;
}
$diff1=$all1-$not_delayed;
$per1=$CI->mis_model->get_percentage($diff1,$all1);
if($per1>0)
{
$sign1="-";
}else{
$sign1="";
}




?>

<tr>
<td rowspan="2">DELEGATED TASKS</td>
<td>% OF WORK NOT DONE</td>
<td><?php echo $all;?></td>
<td><?php 
echo $done;?></td>
<td><a href='javascript:;' onclick="get_delegation_modal('<?php echo $user_id;?>','<?php echo $st;?>','<?php echo $et;?>',1);"><u><?php echo $diff;?></u></a></td>
<td>
	<?php 
	$per7=$CI->mis_model->get_percentage($diff,$all);

	echo $per7;

	if($all>0)
	{
		$assigned_module[]=1;
	}
	?>

</td>
</tr>

<?php 

$all_task_done_this_week=$CI->mis_model->all_task_delegated_Done_this_week($st,$et,$user_id);
if(count($all_task_done_this_week)>0)
{
$all1=count($all_task_done_this_week);
}else{
$all1=0;
}

$all_task_delayed=$CI->mis_model->all_task_delegated_not_delayed($st,$et,$user_id);
if(count($all_task_delayed)>0)
{
$not_delayed=count($all_task_delayed);
}else{
$not_delayed=0;
}
$diff1=$all1-$not_delayed;
$per8=$CI->mis_model->get_percentage($diff1,$all1);
if($per8>0)
{
$sign1="-";
}else{
$sign1="";
}



	if($all1>0)
	{
		$assigned_module[]=1;
	}

?>
<tr>
<td class="thirty_weightage">% OF WORK DELAYED</td>

<td class="thirty_weightage"><?php echo $all1;?></td>
<td class="thirty_weightage"><?php echo $not_delayed;?></td>
<td class="thirty_weightage"><a href='javascript:;' onclick="get_delegation_modal('<?php echo $user_id;?>','<?php echo $st;?>','<?php echo $et;?>',2);"><u><?php echo $diff1;?></a></td>
<td class="thirty_weightage"><?php echo $per8;?></td>
</tr>



<?php
$getMomAssigned = $CI->mis_model->getTotalMomAssigned($st,$et,$user_id);
$getMomCompleted = $CI->mis_model->getTotalMomAssigned_completed($st,$et,$user_id);
$diff=$getMomAssigned-$getMomCompleted;
$per9=$CI->mis_model->get_percentage($diff,$getMomAssigned);

if($getMomAssigned>0)
	{
		$assigned_module[]=1;
	}


?>
<tr>
<td rowspan="2" class="thirty_weightage">MOM</td>
<td  class="thirty_weightage">% OF WORK NOT DONE</td>
<td  class="thirty_weightage"><?php echo $getMomAssigned;?></td>
<td class="thirty_weightage"><?php echo $getMomCompleted;?></td>
<td class="thirty_weightage"><a href='javascript:;' onclick="get_mom_modal('<?php echo $user_id;?>','<?php echo $st;?>','<?php echo $et;?>',1);"><u><?php echo $diff;?></u></a></td>
<td class="thirty_weightage"><?php echo $per9;?></td>
</tr>
<?php 
$mom_done_this_week = $CI->mis_model->getTotalMomAssigned_Done_This_Week($st,$et,$user_id);
$mom_done_not_delayed_this_week = $CI->mis_model->getTotalMomAssigned_Done_n_Not_Delayed_This_Week($st,$et,$user_id);

$diff=$mom_done_this_week-$mom_done_not_delayed_this_week;
$per10=$CI->mis_model->get_percentage($diff,$mom_done_this_week);
if($mom_done_this_week>0)
	{
		$assigned_module[]=1;
	}

?>

<tr>
<td  class="thirty_weightage">% OF WORK DELAYED</td>
<td  class="thirty_weightage"><?php echo $mom_done_this_week;?></td>
<td class="thirty_weightage"><?php echo $mom_done_not_delayed_this_week;?></td>
<td class="thirty_weightage"><a href='javascript:;' onclick="get_mom_modal('<?php echo $user_id;?>','<?php echo $st;?>','<?php echo $et;?>',2);"><u><?php echo $diff;?></u></a></td>
<td class="thirty_weightage"><?php echo $per10;?></td>
</tr>



<?php
$active_parameters = array_sum($assigned_module);
$calculated_issue_rate = $active_parameters > 0
	? round(($per2+$per3+$per4+$per5+$per6+$per7+$per8+$per9+$per10) / $active_parameters)
	: 0;
$issue_rate = $active_parameters > 0
	? $CI->mis_model->get_appraisal_issue_rate($user_id, $calculated_issue_rate)
	: 0;
$performance_score = $active_parameters > 0 ? max(0, 100 - $issue_rate) : null;
if ($performance_score === null) {
	$performance_label = 'Insufficient Data';
	$performance_class = 'performance-average';
} elseif ($performance_score >= 90) {
	$performance_label = 'Excellent';
	$performance_class = 'performance-excellent';
} elseif ($performance_score >= 75) {
	$performance_label = 'Average';
	$performance_class = 'performance-average';
} else {
	$performance_label = 'Needs Improvement';
	$performance_class = 'performance-poor';
}
?>
<tr class="appraisal-summary">
<td colspan="4" class="text-right">APPRAISAL PERFORMANCE</td>
<td><?php echo $active_parameters; ?> active parameter(s)</td>
<td>
	<span class="performance-badge <?php echo $performance_class; ?>">
		<?php echo $performance_score === null ? 'N/A' : $performance_score.'%'; ?> — <?php echo $performance_label; ?>
	</span>
	<div style="margin-top:5px;color:#666;font-size:11px;">
		Issue rate: <?php echo $issue_rate; ?>%
	</div>
</td>
</tr>



</tbody>
</table>


</div>
</div>
</div>

</div>
<?php } } ?>

<!-- Modal -->
<div id="delegationModal" class="modal fade" role="dialog">
<div class="modal-dialog modal-lg">

<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal">&times;</button>
<h4 class="modal-title"><span id='type'></span></h4>
</div>
<div class="modal-body" id="deldata">

</div>
<div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
</div>

</div>
</div>


<!--checklist model-->
<div id="checklistmodel" class="modal fade" role="dialog">
<div class="modal-dialog modal-lg">

<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal">&times;</button>
<h4 class="modal-title"><span id='type2'></span></h4>
</div>
<div class="modal-body" id="checklistdata">

</div>
<div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
</div>

</div>
</div>

<!-- MOM MODEL -->

<div id="MOMModal" class="modal fade" role="dialog">
<div class="modal-dialog modal-lg">

<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal">&times;</button>
<h4 class="modal-title"><span id='type1'></span></h4>
</div>
<div class="modal-body" id="deldata1">

</div>
<div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
</div>

</div>
</div>




<!-- end row -->
<!-- Footer -->
<?php $this->load->view('common/footer');?>
<!-- End Footer -->

</div> <!-- end container -->
</div>
<!-- end wrapper -->


<!-- jQuery  -->
<script src="<?php echo assets_url;?>js/jquery.min.js"></script>
<script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url;?>js/detect.js"></script>
<script src="<?php echo assets_url;?>js/fastclick.js"></script>
<script src="<?php echo assets_url;?>js/jquery.slimscroll.js"></script>
<script src="<?php echo assets_url;?>js/jquery.blockUI.js"></script>
<script src="<?php echo assets_url;?>js/waves.js"></script>
<script src="<?php echo assets_url;?>js/wow.min.js"></script>
<script src="<?php echo assets_url;?>js/jquery.nicescroll.js"></script>
<script src="<?php echo assets_url;?>js/jquery.scrollTo.min.js"></script>

<!-- Datatables-->
<script src="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/dataTables.bootstrap.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/dataTables.buttons.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/jszip.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/pdfmake.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/vfs_fonts.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/buttons.html5.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/buttons.print.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/dataTables.fixedHeader.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/dataTables.keyTable.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/dataTables.responsive.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/dataTables.scroller.min.js"></script>
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- Datatable init js -->
<script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

<!-- App js -->
<script src="<?php echo assets_url;?>js/jquery.core.js"></script>
<script src="<?php echo assets_url;?>js/jquery.app.js"></script>
</body>
<script type="text/javascript"> 

function gettaskmodel(user_id,sdate,edate,flag,dfid)
{


if(flag==1)
{
var dr="Work Not Done";
}else  if(flag==2)
{
var dr="Work done by Delayed";
}else if(flag==3)
{
var dr="Tickets Assigned to you but not Closed";
}else
{
var dr=	"Tickets Created By You Done By User But Not Closed By You";
}

$.ajax({
url: '<?php echo page_url;?>MIS/gettaskdata',
type: 'post',
data: {user_id: user_id,sdate:sdate,edate:edate,flag:flag,dfid:dfid},
success: function(data){
$("#delegationModal").modal('show');
$("#deldata").html(data);
$("#type").text(dr);
}
});


}

function gettaskmodelfollowup(user_id)
{

	$.ajax({
url: '<?php echo page_url;?>MIS/getmissedfollowups',
type: 'post',
data: {user_id: user_id},
success: function(data){
$("#type").html('Missed Followups');
	$("#delegationModal").modal('show');
	$("#deldata").html(data);
}
});

	
	

}


function get_delegation_modal(user_id,sdate,edate,flag)
{
if(flag==1)
{
var dr="Delegation Work Not Done";
}else 
{
var dr="Delegation Work Delayed";
}
$.ajax({
url: '<?php echo page_url;?>MIS/get_delegation_data',
type: 'post',
data: {user_id: user_id,sdate:sdate,edate:edate,flag:flag},
success: function(data){
$("#delegationModal").modal('show');
$("#deldata").html(data);
$("#type").text(dr);

}
});

}


function get_mom_modal(user_id,sdate,edate,flag)
{
if(flag==1)
{
var dr="MOM Work Not Done";
}else 
{
var dr="MOM Work Delayed";
}
$.ajax({
url: '<?php echo page_url;?>MIS/get_mom_data',
type: 'post',
data: {user_id: user_id,sdate:sdate,edate:edate,flag:flag},
success: function(data){
$("#MOMModal").modal('show');
$("#deldata1").html(data);
$("#type1").text(dr);

}
});


}



$(document).on('click', '.save-remark-btn', function () {

    var btn = $(this);
    var id = btn.data('id');
    var textarea = $('#remark_' + id);
    var remark = textarea.val().trim();

    // Remove old error
    textarea.next('.remark-error').remove();

    // ✅ Validation
    if (remark === '') {
        textarea.after('<div class="remark-error" style="color:red; font-size:11px;">Please enter remarks</div>');
        textarea.focus();
        return;
    }

    // Disable button
    btn.prop('disabled', true).text('Saving...');

    $.ajax({
        url: "<?php echo page_url.'MIS/save_remark';?>",   // ✅ FIXED URL
        type: "POST",
        dataType: "json",                            // ✅ auto JSON parse
        data: {
            id: id,
            remark: remark
        },
        success: function (res) {

            if (res.status === 'success') {

                // ✅ Replace textarea + button with saved remark
				var cell = $('#remark_' + res.id).closest('td').empty();
				$('<span>').attr('id', 'remark_text_' + res.id).text(res.remark).appendTo(cell);

            } else {
                alert('Failed to save');
                btn.prop('disabled', false).text('Save');
            }

        },
        error: function () {
            alert('Something went wrong!');
            btn.prop('disabled', false).text('Save');
        }
    });

});

</script>
<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
	   <script type="text/javascript">
            $( document ).ready(function() {
                    $('.multipleselect').select2();
            });
        </script>
</html>
