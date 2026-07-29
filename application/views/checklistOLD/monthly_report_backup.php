<?php

$restyu=$this->db->select('factor')->from('checklistmisfactor')->get();
if($restyu->num_rows()>0)
{
    foreach($restyu->result() as $restyu1);
    $checklistfactor=floatval($restyu1->factor);
   
}else
{
    echo "CHECKLIST MIS FACTOR NOT AVAILABLE";EXIT;
    $checklistfactor='10';
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Checklist</title>

        <!-- Table Responsive css -->
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
            .requiredclass{
                border: 2px solid #E12830;
            }
        </style>
		<?PHP 
$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
foreach($q->result() as $LOGO);
?>
		<style>
table.manglesh thead th {
				background: <?php echo $LOGO->colorcode;?>;
				color:#fff;
				font-weight:bold;
			}

body{margin-top:40px;}
table.report thead th {
				background: #2E7DA6;
				color:#fff;
				font-weight:bold;
			}
table.report tbody td {
				font-weight:bold;
				color:#000;
			}
.feedback {
  background-color : #31B0D5;
  color: white;
  padding: 10px 20px;
  border-radius: 4px;
  border-color: #46b8da;
}

.tddata{
	padding:5px 5px 5px 5px; text-align:center; color:#fff; font-size:16px; font-weight:bold;
}
.donebackgroundcolor{
				background-color:green !important; 
				color:#fff !important;
				font-weight:bold !important;
			}
.notdonebackgroundcolor{
				background-color:red !important; 
				color:#fff !important;
				font-weight:bold !important;
			}
			
			</style>
			 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
			
    </head>


   <body>
<header id="topnav">
<?php $this->load->view('common/nav-menu.php');?>
</header>
<div class="wrapper" style="padding-top: 110px !important;">

            <div class="container-fluid" style="background-color:#fff">
	<!-- Page-Title -->

                <div class="row">
				
				<div class="col-sm-12">

                        <div class="page-title-box">
<div class="row">
<div class="col-md-9">
<h4 class="text-center">PRESTO CURRENT MONTH CHECKLIST REPORT</h4>
</div>
<div class="col-md-3 pull-right">
	<button class="btn btn-warning waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal"><i class="fa fa-arrow-right"></i> FILTER BY USER AND DATE</button>
	</div>
	

</div>
 </div>

                    </div><hr>
					
					<div class="row">
						<div class="col-md-8">
							<table class="table table-striped table-bordered report">
								<thead style="text-align:center">
								<tr>
								<?php 
								$k=1;
								$user_id=$_SESSION['logged_in']['user_id'];
								$query = $this->db->select('id, turnaroundtime, status,sortbynumber')->from('compliance_tat')->where('status','1')->order_by('sortbynumber','asc')->get();
									$count = count($query->result());
								foreach($query->result() as $timing);
								?>
								
										<th class="text-center" colspan="<?php echo $count;?>">TAT</th>
							
									</tr>
								</thead>
								<tbody>
								<tr>
								<?php 
								$k=1;
								$user_id=$_SESSION['logged_in']['user_id'];
								$query1 = $this->db->select('id, turnaroundtime, status,sortbynumber')->from('compliance_tat')->where('status','1')->order_by('sortbynumber','asc')->get();
								foreach($query1->result() as $timing){
								?>
								
									<th class="text-center"><a style="text-decoration:none; color:#000"  href="<?php echo page_url;?>Checklist/checklist_filter_by_category/<?php echo $timing->id;?>/<?php echo $user_id;?>"><?php echo $timing->turnaroundtime;?></a> (<?php 
										$query2 = $this->db->select('task_id, tat_id, status,company_id')->from('compliance_task_report')->where('tat_id',$timing->id)->where('status','1')->get();
										$res = $query2->result();
										echo count($res);

										?>)</th>
									
							
								<?php $k++;}?>
									</tr>
								
								
								</tbody>
							</table>
						</div>
						
						<!--<div class="col-md-4">
						<table class="table table-striped table-bordered report">
								<thead>
									<tr>
										<th colspan="3" style="text-align:center">MIS REPORT OF COMPLETE MONTH (<?php
										$month = date('Ymd');
										echo date('F Y', strtotime($month));?>)</th>
										
									</tr>
								
									<tr>
						<th style="text-align:center">THIS WEEK MIS </th>
						<th style="text-align:center">LAST WEEK MIS </th>
										
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="text-align:center"><?php 
						$lastWeek = array();
						$prevMon = abs(strtotime("this monday"));
						$currentDate = abs(strtotime("today"));
						$seconds = 86400; //86400 seconds in a day
						$dayDiff = ceil( ($currentDate-$prevMon)/$seconds ); 
						if( $dayDiff < 7 )
						{
						$dayDiff += 1; //if it's monday the difference will be 0, thus add 1 to it
						$prevMon = strtotime( "previous monday", strtotime("-$dayDiff day") );
						}
						$prevMon = date("Y-m-d",$prevMon);

						// create the dates from Monday to Sunday
						for($i=0; $i<7; $i++)
						{
						$d = date("Y-m-d", strtotime( $prevMon." + $i day") );
						$lastWeek[]=$d;
						}
						$current_monday = $lastWeek[0];
						$current_saturday = $lastWeek[5];
						
						$query = $this->db->select('id')->from('checklist_done_notdone')->where('task_date BETWEEN "'. $current_monday. '" and "'.$current_saturday.'"')->where('status','0')->get();
						$res = count($query->result());
						$result = $res*$checklistfactor;
						if($result=='0'){
							echo $result."%";
						}else{
						echo "-".$result."%";
						}
						?></td>
						<td style="text-align:center">
						<?php 
							$lastWeek = array();
						$prevMon = abs(strtotime("previous monday"));
						$currentDate = abs(strtotime("today"));
						$seconds = 86400; //86400 seconds in a day
						$dayDiff = ceil( ($currentDate-$prevMon)/$seconds ); 
						if( $dayDiff < 7 )
						{
						$dayDiff += 1; //if it's monday the difference will be 0, thus add 1 to it
						$prevMon = strtotime( "previous monday", strtotime("-$dayDiff day") );
						}
						$prevMon = date("Y-m-d",$prevMon);

						// create the dates from Monday to Sunday
						for($i=0; $i<7; $i++)
						{
						$d = date("Y-m-d", strtotime( $prevMon." + $i day") );
						$lastWeek[]=$d;
						}
						$lastmonday = $lastWeek[0];
						$lastsaturday = $lastWeek[5];
						
						$query3 = $this->db->select('id')->from('checklist_done_notdone')->where('task_date BETWEEN "'.$lastmonday. '" and "'.$lastsaturday.'"')->where('status','0')->get();
						$res = count($query3->result());
						$result = $res*$checklistfactor;
						if($result=='0'){
							echo $result."%";
						}else{
						echo "-".$result."%";
						}
						 
						?>
						</td>
						</tr>
								</tbody>
							</table>
						</div>	  -->						
					
					</div>
															
					
															
															
															

                </div>

                <!-- end page title end breadcrumb -->

		

		<div class="row">

                    <div class="col-sm-12">
<?php 
$yourchecklist= "yourcheck";
?>
                        <div class="card-box table-responsive">

                            <table id="monthlydata" class="table table-striped table-bordered manglesh">

                                <thead>

                                <tr>
									<th>Sr No.</th>
									<th>Task</th>
									<th>User Name</th>
									<th>Turnaround Time</th>
									<?php 
									$month = date('m'); 
									$year = date('Y'); 
									$start_date = "01-".$month."-".$year; 
									$start_time = strtotime($start_date); 
									$end_time = strtotime("+1 month", $start_time); 
									for($i=$start_time; $i<$end_time; $i+=86400) { 
									?>
									<th style="font-size:10px"><?php echo date('d D', $i);?></th>
									<?php
									}
									?>
									
                                    

                                </tr>

                                </thead>
<tbody>
<?php 
$m=1;
//$currmonth=date('Y-m').'-01 00:00:00';
//$endmonth=date('Y-m-t').'23:59:59';
$currmonth='2020-04-30 00:00:00';
$endmonth='2020-03-01 23:59:59';
$this->db->select('a.task_id as recordid, a.user_id, a.task, a.tat_id, a.video_link, a.status, c.id, c.turnaroundtime')->from('compliance_task_report a')->join('compliance_tat c','a.tat_id=c.id','left')->where('a.added_on BETWEEN "'.$currmonth. '" and "'.$endmonth.'"');
$this->db->where('a.status','1');
$query4 = $this->db->get();
		$res = $query4->result();

foreach($res as $row){
?>
<tr>
	<td><?php echo $m;?></td>
	
	<td><a href="<?php echo $row->video_link;?>"><?php echo strtoupper($row->task);?></a></td>
	<td><?php echo strtoupper($row->title);?> <?php echo strtoupper($row->first_name);?> <?php echo strtoupper($row->last_name);?></td>
	<td><?php echo strtoupper($row->turnaroundtime);?></td>
	<?php 
									$month = date('m'); 
									$year = date('Y'); 
									$start_date = "01-".$month."-".$year; 
									$start_time = strtotime($start_date); 
									$end_time = strtotime("+1 month", $start_time); 
									for($i=$start_time; $i<$end_time; $i+=86400) { 
									$taskdate = date('Y-m-d', $i);
									
									?>
									<?php 
									$qu = $this->db->select('task_id, status, task_date')->from('checklist_done_notdone')->where('task_id',$row->recordid)->where('task_date',$taskdate)->get();
									
									if($qu->num_rows()>0){
									foreach($qu->result() as $taskstatusinfo);
									$status = $taskstatusinfo->status;
									
									if($status=='1'){
										$donenotdoneval =  "Y";
										$class = "donebackgroundcolor";
									}else if($status=='0'){
										$donenotdoneval =  "N";
										$class = "notdonebackgroundcolor";
									}else{
										$donenotdoneval =  "";
										$class="";
									}
									}else{
										$donenotdoneval =  "";
										$class="";
									}
									
									?>
									<td class="<?php echo $class;?>"><?php echo $donenotdoneval;?></td>
									<?php
									}
									?>
	
</tr>
<?php $m++;}?>




								 </tbody>

                            </table>
						
							
                        </div>

                    </div>

                </div>

 

                <!-- Footer -->

                <?php $this->load->view('common/footer');?>

                <!-- End Footer -->
            </div> <!-- end container -->

        </div>

       <!-- jQuery  -->
       
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




</body></html>