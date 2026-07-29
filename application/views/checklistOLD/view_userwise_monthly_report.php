<?php 
$user_id =  $user_name;
$tat = $tat;
$searchmonth = $searchmonth;


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
				font-size:10px;
			}

<style>
            .requiredclass{
                border: 2px solid #E12830;
            }
        </style>
		<style>
table.manglesh thead th {
				background: #003366;
				color:#fff;
				font-weight:bold;
			}
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
<div class="wrapper">

            <div class="container-fluid" style="background-color:#fff">
	<!-- Page-Title -->

                <div class="row">
				
				<div class="col-sm-12">

                        <div class="page-title-box">
<div class="row">
<div class="col-md-12">
<h4 class="text-center" style="color:red"><?php 
$query = $this->db->select('user_id, title, first_name, last_name')->from('system_users')->where('user_id',$user_id)->get();
foreach($query->result() as $row);
echo strtoupper($row->title)." ".strtoupper($row->first_name)." ".strtoupper($row->last_name);

?> <strong style="color:#000;"><?php echo $month_name = strtoupper(date("F", mktime(0, 0, 0, $searchmonth, 10)));?></strong> MONTH CHECKLIST REPORT</h4><hr>
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
								
								$query = $this->db->select('id, turnaroundtime, status,sortbynumber')->from('compliance_tat')->where('status','1')->order_by('sortbynumber','asc')->get();
								foreach($query->result() as $timing){
								?>
								
									<th class="text-center">TAT</th>
								<?php $k++;}?>
									</tr>
								</thead>
								<tbody>
								<tr>
								<?php 
								$k=1;
								
								$query = $this->db->select('id, turnaroundtime, status,sortbynumber')->from('compliance_tat')->where('status','1')->order_by('sortbynumber','asc')->get();
								foreach($query->result() as $timing){
								?>
								
									<th class="text-center"><a style="text-decoration:none; color:#000"  href="javascript:void(0);"><?php echo $timing->turnaroundtime;?></a> (<?php 
										$query = $this->db->select('task_id, tat_id, status,company_id')->from('compliance_task_report')->where('tat_id',$timing->id)->where('user_id',$user_id)->where('status','1')->get();
										$res = $query->result();
										echo count($res);

										?>)</th>
									
							
								<?php $k++;}?>
									</tr>
								
								
								</tbody>
							</table>
						</div>
						
						<div class="col-md-4">
						<table class="table table-striped table-bordered report">
								<thead>
									<tr>
										<th colspan="3" style="text-align:center">MIS REPORT OF COMPLETE MONTH (<?php echo $month_name = strtoupper(date("F", mktime(0, 0, 0, $searchmonth, 10)));?>)</th>
										
									</tr>
								
									<tr>
						<th style="text-align:center">MONTHLY REPORT</th>
						
										
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="text-align:center"><?php 
										
						$stdate =  date('Y')."-".$searchmonth."-01";
						$lastdate =  date('Y')."-".$searchmonth."-31";
						
						$tatarray = array();
				           $date = date('Y-m-d');
						   $this->db->distinct();
				           $this->db->select('task_id, tat_id,company_id')->from('compliance_task_report')->where('user_id',$user_id)->where('status','1');
						   $query = $this->db->get();
				           $res = $query->result();
						   if($query->num_rows()>0){
				           foreach($res as $row){
				               $tatarray[] = $row->task_id;
				           }
						   }
											
											
						
						
						$query = $this->db->select('id')->from('checklist_done_notdone')->where_in('task_id',$tatarray)->where('task_date BETWEEN "'. $stdate. '" and "'.$lastdate.'"')->where('status','0')->get();
						$res = count($query->result());
											
						$result = $res*10;
						if($result=='0'){
							echo $result."%";
						}else{
						echo "-".$result."%";
						}
						?></td>
						
						</tr>
								</tbody>
							</table>
						</div>						
					
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
									<th>Turnaround Time</th>
									<?php 
									$month = $searchmonth; 
									
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
	
$this->db->select('a.*,a.task_id as recordid, b.user_id, b.first_name, b.last_name, b.title, c.id, c.turnaroundtime')->from('compliance_task_report a')->join('compliance_tat c','a.tat_id=c.id','left')->join('system_users b','a.user_id=b.user_id','left')->where('a.user_id',$user_id);
$this->db->where('a.status','1');
$this->db->where('a.user_id',$user_id);
if($tat){
$this->db->where('c.id',$tat);	
}
$query = $this->db->get();
$res = $query->result();
foreach($res as $row){
	$recordid = $row->recordid;
?>
<tr>
	<td><?php echo $m;?></td>
	
	<td><a href="<?php echo $row->video_link;?>"><?php echo $row->task;?></a></td>
	<td><?php echo $row->turnaroundtime;?></td>
	<?php 
									$month = $searchmonth; 
									$year = date('Y'); 
									$start_date = "01-".$month."-".$year; 
									$start_time = strtotime($start_date); 
									$end_time = strtotime("+1 month", $start_time); 
									for($i=$start_time; $i<$end_time; $i+=86400) { 
									$taskdate = date('Y-m-d', $i);
									
									?>
									<?php 
									$qu = $this->db->select('task_id, status, task_date')->from('checklist_done_notdone')->where('task_id',$recordid)->where('task_date',$taskdate)->get();
									if($qu->num_rows()>0){
									foreach($qu->result() as $taskstatusinfo);
									$status = $taskstatusinfo->status;
									if($status=='1'){
										$donenotdoneval =  "Y";
									}else if($status=='0'){
										$donenotdoneval =  "N";
									}else{
										$donenotdoneval =  "";
									}
									}else{
										$donenotdoneval =  "";
									}
									if($donenotdoneval=='Y'){
										$class = "donebackgroundcolor";
									}else if($donenotdoneval=='N'){
										$class = "notdonebackgroundcolor";
									}else{
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

                <!-- end row -->
	
 

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