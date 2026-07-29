<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?></title>

        <!-- Table Responsive css -->
		<link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
		 
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
		table.headercss thead th {
				background: #003366;
				color:#fff;
				font-weight:bold;
			}

body{margin-top:40px;}


			
			</style>
			 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
			
    </head>


   <body>
<header id="topnav">
<?php $this->load->view('common/nav-menu.php');?>
</header>
<div class="wrapper" style="padding-top: 10px !important;">

            <div class="container-fluid" style="background-color:#fff">
	<!-- Page-Title -->

                <div class="row">
				
				<div class="col-sm-12">
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
                        <div class="page-title-box">
<div class="row">
<div class="col-md-9">
<h4 class="text-center">PRESTO MONTH TARGET</h4>
</div>
<div class="col-md-3">
	<button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal"><i class="fa fa-inr"></i> Add Daily Billing Amount</button>
	</div>
	

</div>
 </div>

                    </div><hr>
<?php 
$q = $this->db->select('working_year, working_month, target')->from('target_billing_month')->where('id',$this->uri->segment(3))->get();
foreach($q->result() as $row);
?>
<div class="row">
	<div class="col-md-12">
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
				   
					<th class="text-center" style="font-size:20px; background-color:red; color:#fff; width:50%"><?php 
					if($row->working_month=='01'){
					    echo "January";
					}else if($row->working_month=='02'){
					    echo "February";
					}
					else if($row->working_month=='03'){
					    echo "March";
					}
					else if($row->working_month=='04'){
					    echo "April";
					}
					else if($row->working_month=='05'){
					    echo "May";
					}
					else if($row->working_month=='06'){
					    echo "June";
					}
					else if($row->working_month=='07'){
					    echo "July";
					}
					else if($row->working_month=='08'){
					    echo "August";
					}
					else if($row->working_month=='09'){
					    echo "September";
					}
					else if($row->working_month=='10'){
					    echo "October";
					}
					else if($row->working_month=='11'){
					    echo "November";
					}else if($row->working_month=='12'){
					    echo "December";
					}
					
					?> <?php echo $row->working_year;?></th>
					<th class="text-center" style="font-size:20px; background-color:yellow; width:50%"><?php 
					if($row->working_month=='01'){
					    echo "January";
					}else if($row->working_month=='02'){
					    echo "February";
					}
					else if($row->working_month=='03'){
					    echo "March";
					}
					else if($row->working_month=='04'){
					    echo "April";
					}
					else if($row->working_month=='05'){
					    echo "May";
					}
					else if($row->working_month=='06'){
					    echo "June";
					}
					else if($row->working_month=='07'){
					    echo "July";
					}
					else if($row->working_month=='08'){
					    echo "August";
					}
					else if($row->working_month=='09'){
					    echo "September";
					}
					else if($row->working_month=='10'){
					    echo "October";
					}
					else if($row->working_month=='11'){
					    echo "November";
					}else if($row->working_month=='12'){
					    echo "December";
					}
					
					?> Billing Target - <?php echo $row->target;?> </th>
				</tr>
			</thead>
		</table>
	</div>
</div>					
															

                </div>

                <!-- end page title end breadcrumb -->

		

		<div class="row">

                    <div class="col-sm-12">

                        <div class="card-box table-responsive">

                            <table id="monthlydata" class="table table-striped table-bordered manglesh">

                                <thead>

                                <tr>
									<?php 
									$month = $row->working_month; 
									$year = $row->working_year; 
									$start_date = "01-".$month."-".$year; 
									$enddates = "31-".$month."-".$year;
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
<tr>
<?php 
									$month = $row->working_month; 
									$year = $row->working_year; 
									$start_date = "01-".$month."-".$year; 
									$enddates = "31-".$month."-".$year;
									$start_time = strtotime($start_date); 
									$end_time = strtotime("+1 month", $start_time); 
									for($i=$start_time; $i<$end_time; $i+=86400) { 
									$d  =date('d', $i);
									$query = $this->db->select('amount')->from('daily_billing_entry')->where('record_id',$this->uri->segment(3))->where('billing_date',$d)->where('billing_month',$month)->get();
									if($query->num_rows()>0){
									foreach($query->result() as $row1);
									}
									?>
									<td><?php if($query->num_rows()>0){ echo $row1->amount; }?></td>
									<?php }?>
</tr>

								 </tbody>

                            </table>
						
							
                        </div>

                    </div>

                </div>


<div class="row">
	<div class="col-md-4">
		<div class="row">
			 <div class="card-box table-responsive">

                            <table id="monthlydata" class="table table-striped table-bordered manglesh" style="font-size:16px">
<tbody>
<tr>
<td>Target <i class="fa fa-inr"></i></td>
<td><i class="fa fa-inr"></i> <?php echo $row->target;?></td>
</tr>
<tr>
<td>Achieved <i class="fa fa-inr"></i></td>
<td><i class="fa fa-inr"></i> 
<?php 
$fdate = "01";
$ldate  = "31";
$q = $this->db->select('amount')->from('daily_billing_entry')->where('record_id',$this->uri->segment(3))->where('billing_month',$month)->where('billing_date BETWEEN "'.$fdate. '" and "'.$ldate.'"')->get();
foreach($q->result() as $calculation){
	$total[] = $calculation->amount;
}
$amount = array_sum($total);
echo $amount.".00";
?>
</td>

</tr>
<tr>
<td>Required to Achieve <i class="fa fa-inr"></i></td>
<td><i class="fa fa-inr"></i> <?php 
echo $row->target-$amount.".00";
?></td>
</tr>
<tr>
<td>Days Left </td>
<td> <?php 
$y = date('Y');
$start = new DateTime($y."-".$month."-01");



if($month=='01'){
	$lastdate = "31";
	$end = new DateTime($y."-".$month."-".$lastdate);
	}else if($month=='02'){
	$lastdate = "28";
	$end = new DateTime($y."-".$month."-".$lastdate);
	}else if($month=='03'){
	$lastdate = "31";
	$end = new DateTime($y."-".$month."-".$lastdate);
	}else if($month=='04'){
	$lastdate = "30";
	$end = new DateTime($y."-".$month."-".$lastdate);
	}else if($month=='05'){
	$lastdate = "31";
	$end = new DateTime($y."-".$month."-".$lastdate);
	}else if($month=='06'){
	$lastdate = "30";
	$end = new DateTime($y."-".$month."-".$lastdate);
	}else if($month=='07'){
	$lastdate = "31";
	$end = new DateTime($y."-".$month."-".$lastdate);
	}else if($month=='08'){
	$lastdate = "31";
	$end = new DateTime($y."-".$month."-".$lastdate);
	}else if($month=='09'){
	$lastdate = "30";
	$end = new DateTime($y."-".$month."-".$lastdate);
	}else if($month=='10'){
	$lastdate = "31";
	$end = new DateTime($y."-".$month."-".$lastdate);
	}else if($month=='11'){
	$lastdate = "30";
	$end = new DateTime($y."-".$month."-".$lastdate);
	}else if($month=='12'){
	$lastdate = "31";
	$end = new DateTime($y."-".$month."-".$lastdate);
	}
	
$days = $start->diff($end, true)->days;
$sundays = intval($days / 7) + ($start->format('N') + $days % 7 >= 7);
	$today = date('d');
	$workingday = $lastdate-$sundays;
	echo $workingday-$today;
?></td>
</tr>


								 </tbody>

                            </table>
						
							
                        </div>
		</div>
	</div>
	<div class="col-md-2"></div>
	<div class="col-md-6">
		<div class="row">
			 <div class="card-box table-responsive">

                            <table id="monthlydata" class="table table-striped table-bordered manglesh" style="font-size:14px">
							<thead>
								<th colspan="4" style="text-align:center">Week Wise Billing Summary</th>
							</thead>
<tbody>
<tr>
<td style="background-color:green; color:#fff;">Week 1 </td>
<td><?php 
$fdate = "01";
$ldate  = "07";
$total=array();
$q = $this->db->select('amount')->from('daily_billing_entry')->where('record_id',$this->uri->segment(3))->where('billing_month',$month)->where('billing_date BETWEEN "'.$fdate. '" and "'.$ldate.'"')->get();
foreach($q->result() as $calculation){
	$total[] = $calculation->amount;
}
$first_weektotal = array_sum($total);
echo $first_weektotal.".00";
?></td>
<td style="background-color:green; color:#fff;">Week 2</td>
<td><?php 
$total=array();
$fdate = "08";
$ldate  = "14";
$q = $this->db->select('amount')->from('daily_billing_entry')->where('record_id',$this->uri->segment(3))->where('billing_month',$month)->where('billing_date BETWEEN "'.$fdate. '" and "'.$ldate.'"')->get();
foreach($q->result() as $calculation){
	$total[] = $calculation->amount;
}
$second_weektotal = array_sum($total);
echo $second_weektotal.".00";
?></td>
</tr>

<tr>
<td style="background-color:green; color:#fff;">Week 3 </td>
<td><?php 
$total=array();
$fdate = "15";
$ldate  = "21";
$q = $this->db->select('amount')->from('daily_billing_entry')->where('record_id',$this->uri->segment(3))->where('billing_month',$month)->where('billing_date BETWEEN "'.$fdate. '" and "'.$ldate.'"')->get();
foreach($q->result() as $calculation){
	$total[] = $calculation->amount;
}
$third_weektotal = array_sum($total);
echo $third_weektotal.".00";
?></td>
<td style="background-color:green; color:#fff;">Week 4</td>
<td><?php 
$total=array();
$fdate = "22";
$ldate  = "28";
$q = $this->db->select('amount')->from('daily_billing_entry')->where('record_id',$this->uri->segment(3))->where('billing_month',$month)->where('billing_date BETWEEN "'.$fdate. '" and "'.$ldate.'"')->get();
foreach($q->result() as $calculation){
	$total[] = $calculation->amount;
}
$fourth_weektotal = array_sum($total);
echo $fourth_weektotal.".00";
?></td>
</tr>

<tr>
<td style="background-color:green; color:#fff;">Week 5 </td>
<td><?php 
$total=array();
$fdate = "29";
$ldate  = "31";
$total=array();
$q = $this->db->select('amount')->from('daily_billing_entry')->where('record_id',$this->uri->segment(3))->where('billing_month',$month)->where('billing_date BETWEEN "'.$fdate. '" and "'.$ldate.'"')->get();
foreach($q->result() as $calculation){
	$total[] = $calculation->amount;
}
$fifth_weektotal = array_sum($total);
echo $fifth_weektotal.".00";
?></td>
<td style="background-color:red; color:#fff;">Grand Total</td>
<td><?php 
$finalval = $first_weektotal+$second_weektotal+$third_weektotal+$fourth_weektotal+$fifth_weektotal;
echo $finalval; 
?></td>
</tr>




								 </tbody>

                            </table>
						
							
                        </div>
		</div>
	</div>
</div>
                <!-- end row -->
	<div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

 <form id="loginForm" method="post" action="<?php echo page_url;?>Movement_tracking/add_daily_billing_amount/<?php echo $this->uri->segment(3);?>/<?php echo $row->working_month;?>" enctype="multipart/form-data">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">Add Daily Billing Amount</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">
			
												<div class="col-md-6">
													<div class="form-group">
														 <label for="field-2" class="control-label">Date</label>
														 <span id="error_date" style="color:red;">*</span>
														 <select class="form-control " id="date" name="date" required>
									<?php 
									 $tdate = date('d');
									$month = $row->working_month; 
									$year = $row->working_year; 
									$start_date = "01-".$month."-".$year; 
									$enddates = "31-".$month."-".$year;
									$start_time = strtotime($start_date); 
									$end_time = strtotime("+1 month", $start_time); 
									for($i=$start_time; $i<$end_time; $i+=86400) { 
									$d  =date('d', $i);
									?>
									<option value="<?php echo date('d', $i);?>" <?php if($d==$tdate){echo "selected";}?>><?php echo date('d', $i);?></option>
									<?php
									}
									?>
												</select>
													</div>
												</div>
                                               
                                                
												<div class="col-md-6">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">Amount</label>

														<span id="error_month" style="color:red;">*</span>

														<input type="number" class="form-control" name="amount" id="amount" value="" step="0.01" required>

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

                            </div><!-- /.modal -->
 

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