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
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
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

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" rel="stylesheet"/>

    </head>


     <body>





        <!-- Navigation Bar-->

        <header id="topnav">
	<?php $this->load->view('common/nav-menu.php');?>


        </header>

        <!-- End Navigation Bar-->





        <div class="wrapper">

            <div class="container">



                <!-- Page-Title -->

                <div class="row">

                    <div class="col-sm-12">

                        <div class="page-title-box">

                            <div class="btn-group pull-right">

                              

                            </div>

                            <h4 class="page-title">Edit Turnaround Time</h4>

                        </div>

                    </div>

                </div>

                <!-- end page title end breadcrumb -->





                <div class="row">

                    <div class="col-xs-12">

                        <div class="card-box">



                            <div class="row">

                                <div class="col-sm-12 col-xs-12 col-md-12">



								<?php

								$id = $this->uri->segment(3);

								$this->db->select('*')->from('compliance_tat')->where('id',$id);

								$query = $this->db->get();
                                
                                if($query->num_rows() > 0) {
								$res = $query->result();

								foreach($res as $row);
                                  $id = $row->id;
								  $turnaroundtime = $row->turnaroundtime;
								  $startdate = date("d-m-Y", strtotime($row->date));
								  $time_interval = $row->time_interval;
								  $frequency = $row->frequency;
								  $sortbynumber = $row->sortbynumber;
								  $status = $row->status;
                                } else {
                                  $id = '';
                                  $turnaroundtime = '';
                                  $startdate = '';
                                  $time_interval = '';
                                  $frequency = '';
                                  $sortbynumber = '';
                                  $status = '';
                                }
								?>

								 <form method="post" action="<?php echo page_url;?>Checklist/update_turnaroundtime/<?php echo $id;?>" onsubmit="return validate()";>

										<div class="col-md-3">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">Turnaround Time</label>

														<span id="error_turnaroundtime" style="color:red;">*</span>

                                                        <input type="text" class="form-control" id="turnaroundtime" name="turnaroundtime" placeholder="Turnaround Time" value="<?php echo $turnaroundtime;?>" style="text-transform:uppercase" required>

                                                    </div>

                                                </div>

												<div class="col-md-3">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">Start Date</label>

														<span id="error_startdate" style="color:red;">*</span>

                                                        <input type="text" class="form-control" id="startdate" name="startdate" placeholder="Start Date" value="<?php echo $startdate;?>" style="text-transform:uppercase" required>

                                                    </div>

                                                </div>
												 <div class="col-md-3">
												<div class="form-group">
												<label for="field-2" class="control-label">Time Interval</label>

												<span id="error_time_interval" style="color:red;">*</span>

												<select class="form-control" id="time_interval" name="time_interval" style="text-transform:uppercase" required>

													<option value="">--Select Time Interval--</option>
													<option value="1" <?php if($time_interval=='1'){echo "selected";}?>>Per Day</option>
													<option value="2" <?php if($time_interval=='2'){echo "selected";}?>>Week</option>
													<option value="3" <?php if($time_interval=='3'){echo "selected";}?>>Month</option>
													<option value="4" <?php if($time_interval=='4'){echo "selected";}?>>Year</option>

												</select>

												</div>

											</div>
											
											<div class="col-md-3">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">Frequency</label>

														<span id="error_frequency" style="color:red;">*</span>

                                                        <input type="number" class="form-control" id="frequency" name="frequency" placeholder="Frequency" style="text-transform:uppercase" value="<?php echo $frequency;?>" required>

                                                    </div>

                                                </div>

											<div class="col-md-3">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">Sort by Order (Example 1-N Number)</label>

														<span id="error_frequency" style="color:red;"></span>

                                                        <input type="number" class="form-control" id="sortbynumber" name="sortbynumber" placeholder="" value="<?php echo $sortbynumber;?>" style="text-transform:uppercase" required>

                                                    </div>

                                                </div>

										<div class="col-md-3">

											<div class="form-group">

												<label>Status</label>

												<select class="form-control" id="status" name="status" style="text-transform:uppercase" required>

										<option value="">--Select---</option>

											<option value="1" <?php if($status=='1') {echo 'selected';}?>>Active</option>

											<option value="0"  <?php if($status=='0') {echo 'selected';}?>>Inactive</option>

												</select>

											</div>

										</div>
										<div class="col-md-6"></div>
										<div class="col-md-3">

											<div class="form-group pull-right" style="padding-top:24px;">

												<label>&nbsp;</label>

												<input type="submit" class="btn btn-success" value="Update" id="saves">

											</div>

										</div>

									

									</form>

                                   



                                </div>



                            </div>

                            <!-- end row -->

                        </div> <!-- end ard-box -->

                    </div><!-- end col-->



                </div>

                <!-- end row -->





                <!-- Footer -->

                <footer class="footer text-right">

                    <div class="container">

                        <div class="row">

                            <div class="col-xs-12 text-center">

                                © 2016. All rights reserved.

                            </div>

                        </div>

                    </div>

                </footer>

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
		<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
		<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

 <script type="text/javascript">
             $(document).ready(function() {
            $("#startdate").datepicker({
					todayHighlight:true,
		        	startDate: '-0m',
		        	format: 'dd-mm-yyyy'
				});
				
            });
        </script>

<script language="javascript" type="text/javascript">   


$(document).ready(function() {

$("#saves").click(function() {

var turnaroundtime = $("#turnaroundtime").val();
if(turnaroundtime=='')
{
$("#error_turnaroundtime").html('Required!');
}
var startdate = $("#startdate").val();
if(startdate=='')
{
$("#error_startdate").html('Required!');
}

var time_interval = $("#time_interval").val();
if(time_interval=='')
{
$("#error_time_interval").html('Required!');
}
var frequency = $("#frequency").val();
if(frequency=='')
{
$("#error_frequency").html('Required!');
}

var status = $("#status").val();

if(status=='')

{

	

	$("#error_status").html('Required!');

}



if(turnaroundtime=='' || startdate=='' || time_interval=='' || frequency=='' ||  status=='')

{

	

	return false;

}



});

});

function validate()

{

	 $("#saves").attr('disabled',false);

	$("#saves").val('Submit');



$("#form :input").attr('required',false);



var isValid=0;

$("#form .mand").each(function() {

var element = $(this).val();

if (element=="") {



isValid=1;

}





});

 

 

 if(isValid==0)

 {

	$("#saves").attr('disabled',true);

	$("#saves").val('Please Wait..');

     return true;

	 

 }else

 {

	 $("#saves").attr('disabled',false);

	$("#saves").val('Submit');

     alert('All Fields are mandatory');

     return false;

 }

    

}

</script>

    </body>
</html>