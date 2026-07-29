<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Dashboard</title>

        <!--Morris Chart CSS -->
		<link rel="stylesheet" href="<?php echo assets_url;?>plugins/morris/morris.css">

        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

<style>
.panel-group {
    margin-bottom: 0px;
}

.sidenav1 {
   width: 227px;
    position: fixed;
    z-index: 9999;
    top: 137px;
    right: 10px;
    color: white;
    background: rgba(0, 0, 0, 0.8);
    overflow-x: hidden;
    padding: 0px 0;
}

.sidenav1 a {
    padding: 2px 4px 2px 8px;
    text-decoration: none;
    font-size: 15px;
    color: #fff;
    display: block;
}

.sidenav1 a:hover {
    color: white;
}

.main1 {
    margin-left: 140px; /* Same width as the sidebar + left position in px */
    font-size: 28px; /* Increased text to enable scrolling */
    padding: 0px 10px;
}

@media screen and (max-height: 450px) {
    .sidenav1 {}
    .sidenav1 a {font-size: 14px;}
}


		.table th{text-align:center;}

		/* #fabrictable th{ background-color:green;color:white;}*/

		.theading{background-color:green;color:white;}
		.total{background-color:red;color:white;}
		
		
		.blink{
		font-size: 15px;
		color:#F4F9AA;
		
		animation: blink 1s infinite;
	 }
	 
	 @keyframes blink{
		0%{opacity: 1;}
		75%{opacity: 1;}
		76%{ opacity: 0;}
		100%{opacity: 0;}
	 }


</style>
    </head>


    <body>

        <!-- Navigation Bar-->
       <?php $this->load->view('common/nav-menu');?>
        <!-- End Navigation Bar-->


        <div class="wrapper">
            <div class="container-fluid" style="background-color:#fff; min-height:463px;">

                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <?php
						$dep=$this->db->select('audit_type')->from('audit_type')->where('audit_type_id',$this->uri->segment('3'))->get();
						foreach($dep->result() as $depp);
						?>
                        <h4 class="page-title text-center"><?php echo $depp->audit_type;?> Department MIS</h4>
                    </div>
                   
                </div>
                
 <div class="col-md-12">
					<div class="row">
	<?php 
	$overallpms=array();
$date1=date('Y-m-d');
$day1=$date1.' 00:00:00';
$day2=$date1.' 23:59:59';
	$this->db->select('a.*,b.*')->from('audit_tasks a')->join('timing_slot b','a.tentative_time=b.timing_id')->where('audit_type_id',$this->uri->segment('3'))->group_by('a.tentative_time');
	$query = $this->db->get();
	$res = $query->result();
	//echo "<pre>"; print_r($res);exit;
	if($query->num_rows()>0)
	{
	foreach($res as $audit_type){
	?>
	<div class="col-lg-3 col-md-3 col-xs-12 col-sm-12">
     <div class="card-box widget-user" style="min-height: 140px;">
     <div class="text-center">
	<div class="row">
	<div class="col-md-12"><h4><strong><?php echo $audit_type->timing_period;?></strong></h4></div>
	<div class="panel-group" id="accordion">
    <div class="panel panel-default">
     <div>
        
	   <div class="panel-body">

<table class="table">
 
  <tbody>
  <?php
  
  $query=$this->db->select('a.*')->from('audit_tasks a')->where('a.audit_type_id',$this->uri->segment('3'))->where('a.tentative_time',$audit_type->tentative_time)->group_by('a.task_id')->get();

$totaltask=$query->num_rows();
  
  ?>
    <tr>
      <th scope="row">Total Task</th>
      <td><?php echo $totaltask;?></td>
    
    </tr>

<?php

if(ucfirst($audit_type->timing_period)=='Daily')
  {
$query1=$this->db->select('a.*,b.*')->from('audit_tasks a')->join('audit_tasks_remark b','a.task_id=b.taskid')->where('a.audit_type_id',$this->uri->segment('3'))->where('a.tentative_time',$audit_type->tentative_time)->where('b.taskstatus','1')->where('b.addedOn>',$day1)->where('b.addedOn<',$day2)->group_by('a.task_id')->get();

$totaltaskdone=$query1->num_rows();
 }else if (ucfirst($audit_type->timing_period)=='Twice in a Week')
 {

$weekdays=array(); 
// set current date
$date = date('Y-m-d');
// parse about any English textual datetime description into a Unix timestamp 
$ts = strtotime($date);
// find the year (ISO-8601 year number) and the current week
$year = date('o', $ts);
$week = date('W', $ts);
// print week for the current date
for($i = 1; $i <= 7; $i++) {
// timestamp from ISO week date format
$ts = strtotime($year.'W'.$week.$i);
$weekdays[]=date("Y-m-d", $ts);

}

$query1=$this->db->select('a.*,b.*')->from('audit_tasks a')->join('audit_tasks_remark b','a.task_id=b.taskid')->where('a.audit_type_id',$this->uri->segment('3'))->where('a.tentative_time',$audit_type->tentative_time)->where('b.taskstatus','1')->where('b.addedOn>',$weekdays[0].' 0:00:00')->where('b.addedOn<',$weekdays[6].' 23:59:59')->get();

$totaltaskdone1=$query1->num_rows();


$totaltaskdone=$totaltaskdone1/2;
//echo "<pre>"; print_r($weekdays);exit;
	 
 }else if (ucfirst($audit_type->timing_period)=='Weekly')
 {

$weekdays=array(); 
// set current date
$date = date('Y-m-d');
// parse about any English textual datetime description into a Unix timestamp 
$ts = strtotime($date);
// find the year (ISO-8601 year number) and the current week
$year = date('o', $ts);
$week = date('W', $ts);
// print week for the current date
for($i = 1; $i <= 7; $i++) {
// timestamp from ISO week date format
$ts = strtotime($year.'W'.$week.$i);
$weekdays[]=date("Y-m-d", $ts);

}

$query1=$this->db->select('a.*,b.*')->from('audit_tasks a')->join('audit_tasks_remark b','a.task_id=b.taskid')->where('a.audit_type_id',$this->uri->segment('3'))->where('a.tentative_time',$audit_type->tentative_time)->where('b.taskstatus','1')->where('b.addedOn>',$weekdays[0].' 0:00:00')->where('b.addedOn<',$weekdays[6].' 23:59:59')->get();

$totaltaskdone1=$query1->num_rows();


$totaltaskdone=$totaltaskdone1;
//echo "<pre>"; print_r($weekdays);exit;
	 
 }
 else if (ucfirst($audit_type->timing_period)=='Twice a Month')
 {

$curMonth = date('F');
   $curYear  = date('Y');
   $timestamp    = strtotime($curMonth.' '.$curYear);
   $firstdate = date('Y-m-01 00:00:00', $timestamp);
   $lastdate  = date('Y-m-t 23:59:59', $timestamp); 
   
  
$query1=$this->db->select('a.*,b.*')->from('audit_tasks a')->join('audit_tasks_remark b','a.task_id=b.taskid')->where('a.audit_type_id',$this->uri->segment('3'))->where('a.tentative_time',$audit_type->tentative_time)->where('b.taskstatus','1')->where('b.addedOn>',$firstdate)->where('b.addedOn<',$lastdate)->get();

$totaltaskdone1=$query1->num_rows();


$totaltaskdone=$totaltaskdone1/2;
//echo "<pre>"; print_r($weekdays);exit;
	 
 }else if (ucfirst($audit_type->timing_period)=='Monthly')
 {

$curMonth = date('F');
   $curYear  = date('Y');
   $timestamp    = strtotime($curMonth.' '.$curYear);
   $firstdate = date('Y-m-01 00:00:00', $timestamp);
   $lastdate  = date('Y-m-t 23:59:59', $timestamp); 
   
  
$query1=$this->db->select('a.*,b.*')->from('audit_tasks a')->join('audit_tasks_remark b','a.task_id=b.taskid')->where('a.audit_type_id',$this->uri->segment('3'))->where('a.tentative_time',$audit_type->tentative_time)->where('b.taskstatus','1')->where('b.addedOn>',$firstdate)->where('b.addedOn<',$lastdate)->get();

$totaltaskdone1=$query1->num_rows();


$totaltaskdone=$totaltaskdone1;
//echo "<pre>"; print_r($weekdays);exit;
	 
 }else if (ucfirst($audit_type->timing_period)=='Twice in a Day')
 {
   $datetime1 = date('Y-m-d 00:00:00');
   $datetime2  = date('Y-m-d 23:59:59'); 
   
  
$query1=$this->db->select('a.*,b.*')->from('audit_tasks a')->join('audit_tasks_remark b','a.task_id=b.taskid')->where('a.audit_type_id',$this->uri->segment('3'))->where('a.tentative_time',$audit_type->tentative_time)->where('b.taskstatus','1')->where('b.addedOn>',$datetime1)->where('b.addedOn<',$datetime2)->get();

$totaltaskdone1=$query1->num_rows();


$totaltaskdone=$totaltaskdone1/2;
//echo "<pre>"; print_r($weekdays);exit;
	 
 }else if (ucfirst($audit_type->timing_period)=='Quarterly')
 {
   
   function getStartOfQuarter()
{
     return date(sprintf('Y-%s-01', floor((date('n') - 1) / 3) * 3 + 1));
}

function getEndOfQuarter()
{
    return date(sprintf('Y-%s-t', floor((date('n') + 2) / 3) * 3));
}

$startquateer=getStartOfQuarter();
$endquateer=getEndOfQuarter();

$query1=$this->db->select('a.*,b.*')->from('audit_tasks a')->join('audit_tasks_remark b','a.task_id=b.taskid')->where('a.audit_type_id',$this->uri->segment('3'))->where('a.tentative_time',$audit_type->tentative_time)->where('b.taskstatus','1')->where('b.addedOn>',$startquateer)->where('b.addedOn<',$endquateer)->get();

$totaltaskdone1=$query1->num_rows();


$totaltaskdone=$totaltaskdone1;
//echo "<pre>"; print_r($weekdays);exit;
	 
 }
 else if (ucfirst($audit_type->timing_period)=='Twice in a Year')
 {

$slot1=array('01','02','03','04','05','06'); 
$slot2=array('07','08','09','10','11','12');
$curmnt=date('m');

	if (in_array($curmnt, $slot1))
	{
	$startdate=date('Y-01-01 00:00:00');
	$enddate=date('Y-06-01 23:59:59');
	}
	
		if (in_array($curmnt, $slot2))
	{
	$startdate=date('Y-06-01 00:00:00');
	$enddate=date('Y-12-01 23:59:59');
	}
	
	
	
	
   
   $query1=$this->db->select('a.*,b.*')->from('audit_tasks a')->join('audit_tasks_remark b','a.task_id=b.taskid')->where('a.audit_type_id',$this->uri->segment('3'))->where('a.tentative_time',$audit_type->tentative_time)->where('b.taskstatus','1')->where('b.addedOn>',$startdate)->where('b.addedOn<',$enddate)->get();

$totaltaskdone1=$query1->num_rows();


$totaltaskdone=$totaltaskdone1;
//echo "<pre>"; print_r($weekdays);exit;
	 
 }else if (ucfirst($audit_type->timing_period)=='Yearly')
 {


$startdate=date('Y-01-01 00:00:00');
$enddate=date('Y-12-31 23:59:59');
	
	
   
   $query1=$this->db->select('a.*,b.*')->from('audit_tasks a')->join('audit_tasks_remark b','a.task_id=b.taskid')->where('a.audit_type_id',$this->uri->segment('3'))->where('a.tentative_time',$audit_type->tentative_time)->where('b.taskstatus','1')->where('b.addedOn>',$startdate)->where('b.addedOn<',$enddate)->get();

$totaltaskdone1=$query1->num_rows();


$totaltaskdone=$totaltaskdone1;
//echo "<pre>"; print_r($weekdays);exit;
	 
 }
?>

 <tr style="background-color:green;color:white;">
      <th scope="row" style="color:white">Done</th>
      <td><?php echo $totaltaskdone;?></td>
    
    </tr>
    <?php
	if(ucfirst($audit_type->timing_period)=='Daily')
  {
	$query2=$this->db->select('a.*,b.*')->from('audit_tasks a')->join('audit_tasks_remark b','a.task_id=b.taskid')->where('a.audit_type_id',$this->uri->segment('3'))->where('a.tentative_time',$audit_type->tentative_time)->where('b.taskstatus','0')->where('b.addedOn>',$day1)->where('b.addedOn<',$day2)->group_by('a.task_id')->get();

$totaltasknotdone=$query2->num_rows();
  }else if (ucfirst($audit_type->timing_period)=='Twice in a Week')
{
			$weekdays=array(); 
			// set current date
			$date = date('Y-m-d');
			// parse about any English textual datetime description into a Unix timestamp 
			$ts = strtotime($date);
			// find the year (ISO-8601 year number) and the current week
			$year = date('o', $ts);
			$week = date('W', $ts);
			// print week for the current date
			for($i = 1; $i <= 7; $i++) {
			// timestamp from ISO week date format
			$ts = strtotime($year.'W'.$week.$i);
			$weekdays[]=date("Y-m-d", $ts);
			}
			
			//echo "<pre>"; print_r($weekdays);exit;
			
			$query2=$this->db->select('a.*,b.*')->from('audit_tasks a')->join('audit_tasks_remark b','a.task_id=b.taskid')->where('a.audit_type_id',$this->uri->segment('3'))->where('a.tentative_time',$audit_type->tentative_time)->where('b.taskstatus','0')->where('b.addedOn>',$weekdays[0].' 00:00:00')->where('b.addedOn<',$weekdays[6].' 23:59:59')->get();
			
$totaltasknotdone1=$query2->num_rows();


$totaltasknotdone=$totaltasknotdone1/2;
	  
  }else if (ucfirst($audit_type->timing_period)=='Weekly')
{
			$weekdays=array(); 
			// set current date
			$date = date('Y-m-d');
			// parse about any English textual datetime description into a Unix timestamp 
			$ts = strtotime($date);
			// find the year (ISO-8601 year number) and the current week
			$year = date('o', $ts);
			$week = date('W', $ts);
			// print week for the current date
			for($i = 1; $i <= 7; $i++) {
			// timestamp from ISO week date format
			$ts = strtotime($year.'W'.$week.$i);
			$weekdays[]=date("Y-m-d", $ts);
			}
			
			//echo "<pre>"; print_r($weekdays);exit;
			
			$query2=$this->db->select('a.*,b.*')->from('audit_tasks a')->join('audit_tasks_remark b','a.task_id=b.taskid')->where('a.audit_type_id',$this->uri->segment('3'))->where('a.tentative_time',$audit_type->tentative_time)->where('b.taskstatus','0')->where('b.addedOn>',$weekdays[0].' 00:00:00')->where('b.addedOn<',$weekdays[6].' 23:59:59')->get();
			
$totaltasknotdone1=$query2->num_rows();


$totaltasknotdone=$totaltasknotdone1;
	  
  }else if(ucfirst($audit_type->timing_period)=='Twice a Month')
  {
	  
	  $curMonth = date('F');
   $curYear  = date('Y');
   $timestamp    = strtotime($curMonth.' '.$curYear);
   $firstdate = date('Y-m-01 00:00:00', $timestamp);
   $lastdate  = date('Y-m-t 23:59:59', $timestamp); 
	  	
	$query2=$this->db->select('a.*,b.*')->from('audit_tasks a')->join('audit_tasks_remark b','a.task_id=b.taskid')->where('a.audit_type_id',$this->uri->segment('3'))->where('a.tentative_time',$audit_type->tentative_time)->where('b.taskstatus','0')->where('b.addedOn>',$firstdate)->where('b.addedOn<',$lastdate)->get();
	
	$totaltasknotdone1=$query2->num_rows();
	
	
	$totaltasknotdone=$totaltasknotdone1/2;
	  
  }else if(ucfirst($audit_type->timing_period)=='Monthly')
  {
	  
	  $curMonth = date('F');
   $curYear  = date('Y');
   $timestamp    = strtotime($curMonth.' '.$curYear);
   $firstdate = date('Y-m-01 00:00:00', $timestamp);
   $lastdate  = date('Y-m-t 23:59:59', $timestamp); 
	  	
	$query2=$this->db->select('a.*,b.*')->from('audit_tasks a')->join('audit_tasks_remark b','a.task_id=b.taskid')->where('a.audit_type_id',$this->uri->segment('3'))->where('a.tentative_time',$audit_type->tentative_time)->where('b.taskstatus','0')->where('b.addedOn>',$firstdate)->where('b.addedOn<',$lastdate)->get();
	
	$totaltasknotdone1=$query2->num_rows();
	
	
	$totaltasknotdone=$totaltasknotdone1;
	  
  }else if(ucfirst($audit_type->timing_period)=='Twice in a Day')
  {
   $datetime1 = date('Y-m-d 00:00:00');
   $datetime2  = date('Y-m-d 23:59:59');
  
	  	
	$query2=$this->db->select('a.*,b.*')->from('audit_tasks a')->join('audit_tasks_remark b','a.task_id=b.taskid')->where('a.audit_type_id',$this->uri->segment('3'))->where('a.tentative_time',$audit_type->tentative_time)->where('b.taskstatus','0')->where('b.addedOn>',$firstdate)->where('b.addedOn<',$lastdate)->get();
	
	$totaltasknotdone1=$query2->num_rows();
	
	
	$totaltasknotdone=$totaltasknotdone1/2;  
  }else if (ucfirst($audit_type->timing_period)=='Quarterly')
 {
   
   function getStartOfQuarter1()
{
     return date(sprintf('Y-%s-01', floor((date('n') - 1) / 3) * 3 + 1));
}

function getEndOfQuarter1()
{
    return date(sprintf('Y-%s-t', floor((date('n') + 2) / 3) * 3));
}

$startquateer=getStartOfQuarter1();
$endquateer=getEndOfQuarter1();

	  	
	$query2=$this->db->select('a.*,b.*')->from('audit_tasks a')->join('audit_tasks_remark b','a.task_id=b.taskid')->where('a.audit_type_id',$this->uri->segment('3'))->where('a.tentative_time',$audit_type->tentative_time)->where('b.taskstatus','0')->where('b.addedOn>',$startquateer)->where('b.addedOn<',$endquateer)->get();
	
	$totaltasknotdone1=$query2->num_rows();
	
	
	$totaltasknotdone=$totaltasknotdone1;
	 
 }else if (ucfirst($audit_type->timing_period)=='Twice in a Year')
 {

$slot1=array('01','02','03','04','05','06'); 
$slot2=array('07','08','09','10','11','12');
$curmnt=date('m');

	if (in_array($curmnt, $slot1))
	{
	$startdate=date('Y-01-01 00:00:00');
	$enddate=date('Y-06-01 23:59:59');
	}
	
		if (in_array($curmnt, $slot2))
	{
	$startdate=date('Y-06-01 00:00:00');
	$enddate=date('Y-12-01 23:59:59');
	}
	
$query2=$this->db->select('a.*,b.*')->from('audit_tasks a')->join('audit_tasks_remark b','a.task_id=b.taskid')->where('a.audit_type_id',$this->uri->segment('3'))->where('a.tentative_time',$audit_type->tentative_time)->where('b.taskstatus','0')->where('b.addedOn>',$startdate)->where('b.addedOn<',$enddate)->get();
$totaltasknotdone1=$query2->num_rows();
$totaltasknotdone=$totaltasknotdone1;
//echo "<pre>"; print_r($weekdays);exit;
	 
 }else if(ucfirst($audit_type->timing_period)=='Yearly')
 {

$startdate=date('Y-01-01 00:00:00');
$enddate=date('Y-12-31 23:59:59');

	
$query2=$this->db->select('a.*,b.*')->from('audit_tasks a')->join('audit_tasks_remark b','a.task_id=b.taskid')->where('a.audit_type_id',$this->uri->segment('3'))->where('a.tentative_time',$audit_type->tentative_time)->where('b.taskstatus','0')->where('b.addedOn>',$startdate)->where('b.addedOn<',$enddate)->get();
$totaltasknotdone1=$query2->num_rows();	
$totaltasknotdone=$totaltasknotdone1;
//echo "<pre>"; print_r($weekdays);exit;
	 
 }
	?>
     <tr style="background-color:red;color:white;">
      <th scope="row" style="color:white">Not Done</th>
      <td><?php echo $totaltasknotdone;?></td>
    
    </tr> 
    <?php
	$notupdated=$totaltask-($totaltaskdone+$totaltasknotdone);
	?>
     <tr>
      <th scope="row">Not Updated</th>
      <td><?php echo $notupdated;?></td>
    
    </tr>
    
    <?php
	$pms=$totaltaskdone*100/$totaltask;
	$overallpms[]=round($pms,2);
	?>
    <tr>
      <th scope="row">MIS %</th>
      <td><?php echo round($pms,2).' %';?></td>
      
      
    
    </tr> 
      
  </tbody>
</table> 
			
		</div>
	 
      </div>
    </div>
   </div>			
	</div>
    </div>
      </div>
          </div>
				
								<?php }
	}else
	{
		$overallpms[]=0;
	}?>		
				
				
				
                </div>
                <!-- end row -->
			</div>

                
                 <!-- Side Barr-->


<div class="sidenav1">
<div class="row">
<div class="col-md-12">

<table class="table" style="margin-bottom:0px;font-size:12px;">
   
    <tbody>
   
	 
      <tr>
     
        <td style="color:white">Overall MIS</td>
        <?php
		 $sumpns=array_sum($overallpms);
		 $totalpms=count($overallpms);
		 $averagepms=$sumpns/$totalpms;
		?>
        <td style="color:yellow" class="blink"><?php echo round($averagepms,2);?> %</td>
		
     
      </tr>
   
  
   
    </tbody>
  </table>
</div>
</div>
</div>
<!--End side Baar-->


              

                <!-- Footer -->
              <?php $this->load->view('common/footer');?>
                <!-- End Footer -->

            </div>
            <!-- end container -->



            <!-- Right Sidebar -->
            <div class="side-bar right-bar">
                <a href="javascript:void(0);" class="right-bar-toggle">
                    <i class="zmdi zmdi-close-circle-o"></i>
                </a>
                <h4 class="">Notifications</h4>
                <div class="notification-list nicescroll">
                    <ul class="list-group list-no-border user-list">
                        <li class="list-group-item">
                            <a href="#" class="user-list-item">
                                <div class="avatar">
                                    <img src="<?php echo assets_url;?>images/users/avatar-2.jpg" alt="">
                                </div>
                                <div class="user-desc">
                                    <span class="name">Michael Zenaty</span>
                                    <span class="desc">There are new settings available</span>
                                    <span class="time">2 hours ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="#" class="user-list-item">
                                <div class="icon bg-info">
                                    <i class="zmdi zmdi-account"></i>
                                </div>
                                <div class="user-desc">
                                    <span class="name">New Signup</span>
                                    <span class="desc">There are new settings available</span>
                                    <span class="time">5 hours ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="#" class="user-list-item">
                                <div class="icon bg-pink">
                                    <i class="zmdi zmdi-comment"></i>
                                </div>
                                <div class="user-desc">
                                    <span class="name">New Message received</span>
                                    <span class="desc">There are new settings available</span>
                                    <span class="time">1 day ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="list-group-item active">
                            <a href="#" class="user-list-item">
                                <div class="avatar">
                                    <img src="<?php echo assets_url;?>images/users/avatar-3.jpg" alt="">
                                </div>
                                <div class="user-desc">
                                    <span class="name">James Anderson</span>
                                    <span class="desc">There are new settings available</span>
                                    <span class="time">2 days ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="list-group-item active">
                            <a href="#" class="user-list-item">
                                <div class="icon bg-warning">
                                    <i class="zmdi zmdi-settings"></i>
                                </div>
                                <div class="user-desc">
                                    <span class="name">Settings</span>
                                    <span class="desc">There are new settings available</span>
                                    <span class="time">1 day ago</span>
                                </div>
                            </a>
                        </li>

                    </ul>
                </div>
            </div>
            <!-- /Right-bar -->

        </div>




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

        <!-- KNOB JS -->
        <!--[if IE]>
        <script type="text/javascript" src="<?php echo assets_url;?>plugins/jquery-knob/excanvas.js"></script>
        <![endif]-->
        <script src="<?php echo assets_url;?>plugins/jquery-knob/jquery.knob.js"></script>

        <!--Morris Chart-->
		<script src="<?php echo assets_url;?>plugins/morris/morris.min.js"></script>
		<script src="<?php echo assets_url;?>plugins/raphael/raphael-min.js"></script>

        <!-- Dashboard init -->
        <script src="<?php echo assets_url;?>pages/jquery.dashboard.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

    </body>
</html>