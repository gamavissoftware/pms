<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Prestogroup Edit</title>

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

                            <h4 class="page-title">Edit SMS Template</h4>

                        </div>

                    </div>

                </div>

                <!-- end page title end breadcrumb -->





                <div class="row" style="padding-top:50px;">

                    <div class="col-xs-12">

                        <div class="card-box">



                            <div class="row">

                                <div class="col-sm-12 col-xs-12 col-md-12">



								<?php

								$id = $this->uri->segment(3);

								$this->db->select('*')->from('salestooltemplete')->where('id',$id);

								$query = $this->db->get();

								$res = $query->result();

								foreach($res as $rows)

								

								?>

								 <form method="post" action="<?php echo page_url;?>Salestool/updatescript/<?php echo $rows->id;?>">
								 <div class="col-md-4">
										<div class="form-group">
											<input type="text" class="form-control" name="intro" value="<?php echo $rows->intro;?>" placeholder="Intro Line Optional">
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<input type="text" class="form-control" name="first_field" value="<?php echo $rows->first_field;?>">
										</div>
									</div>
									
									<div class="col-md-2">
										<div class="form-group">
											<input type="text" class="form-control" name="second_field"  readonly value="<?php echo $rows->person_name;?>">
										</div>
									</div>
									
									<div class="col-md-2">
										<div class="form-group">
											<input type="text" class="form-control" name="third_field" value="<?php echo $rows->salutation;?>" readonly>
										</div>
									</div>
									
									<div class="col-md-3">
										<div class="form-group">
											<input type="text" class="form-control" name="fourth_field" value="<?php echo $rows->second_field;?>">
										</div>
									</div>
									
									<div class="col-md-2">
										<div class="form-group">
											<input type="text" class="form-control" name="fifth_field" value="<?php echo $rows->first_sample_name;?>" readonly>
										</div>
									</div>
									
									
									<div class="col-md-2">
										<div class="form-group">
											<input type="text" class="form-control" name="sixth_field" value="<?php echo $rows->aftersamplename;?>">
										</div>
									</div>
									
									<div class="col-md-2">
										<div class="form-group">
											<input type="text" class="form-control" name="sevnth_field" value="<?php echo $rows->machine_detail;?>" readonly>
										</div>
									</div>
									
									<div class="col-md-3">
										<div class="form-group">
											<input type="text" class="form-control" name="eighth_field" value="<?php echo $rows->third_field;?>">
										</div>
									</div>
									
									<div class="col-md-2">
										<div class="form-group">
											<input type="text" class="form-control" name="ninth_field" value="<?php echo $rows->machine_model;?>" readonly>
										</div>
									</div>
									
									<div class="col-md-4">
										<div class="form-group">
											<input type="text" class="form-control" name="tenth_field" value="<?php echo $rows->fourth_field;?>">
										</div>
									</div>	
									<div class="col-md-2">
										<div class="form-group">
											<input type="text" class="form-control" name="eleventh_field" value="<?php echo $rows->sample_name;?>" readonly>
										</div>
									</div>
									
										<div class="col-md-2">
										<div class="form-group">
											<input type="text" class="form-control" name="twelveth_field" value="<?php echo $rows->fifth_field;?>">
										</div>
									</div>
									
										<div class="col-md-2">
										<div class="form-group">
											<input type="text" class="form-control" name="thirteen_field" value="<?php echo $rows->related_machines;?>" readonly>
										</div>
									</div>
									
										<div class="col-md-2">
										<div class="form-group">
											<input type="text" class="form-control" name="fourteen_field" value="<?php echo $rows->sixth_field;?>">
										</div>
									</div>
									
									<div class="col-md-2">
										<div class="form-group">
											<input type="text" class="form-control" name="fifteen_field" value="<?php echo $rows->location;?>" readonly>
										</div>
									</div>
									
									<div class="col-md-2">
										<div class="form-group">
											<input type="text" class="form-control" name="sixteen_field" value="<?php echo $rows->afterlocation;?>">
										</div>
									</div>
									
									<div class="col-md-2">
										<div class="form-group">
											<input type="text" class="form-control" name="seventeen_field" value="<?php echo $rows->company_detail;?>" readonly>
										</div>
									</div>
									
									<div class="col-md-2">
										<div class="form-group">
											<input type="text" class="form-control" name="eigthteen_field" value="<?php echo $rows->seventh_line;?>">
										</div>
									</div>
									
									<div class="col-md-2">
										<div class="form-group">
											<input type="text" class="form-control" name="nineteen_field" value="<?php echo $rows->again_person_name;?>" readonly>
										</div>
									</div>
									
									<div class="col-md-2">
										<div class="form-group">
											<input type="text" class="form-control" name="twenty_field" value="<?php echo $rows->again_machine_details;?>" readonly>
										</div>
									</div>
											
<div class="col-md-2">
										<div class="form-group">
											<input type="text" class="form-control" name="twentyone_field" value="<?php echo $rows->again_customer_overall_details;?>" readonly>
										</div>
									</div>
									
									<div class="col-md-4">
										<div class="form-group">
											<input type="text" class="form-control" name="twentytwo_field" value="<?php echo $rows->lastline;?>">
										</div>
									</div>
																					
											

										<div class="col-md-9"></div>
										<div class="col-md-3">

											<div class="form-group pull-right" style="padding-top:24px;">

												<label>&nbsp;</label>

												<input type="submit" class="btn btn-success" value="Update">

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
            $(function () {
                $('.datepicker-autoclose').datepicker({
                    autoclose:"true",
                    orientation: "bottom",
                    changeMonth: true,
    changeYear: true,
    format: 'yyyy-mm-dd'
                });
            });
        </script>

<script language="javascript" type="text/javascript">   


$(document).ready(function() {

$("#save").click(function() {

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

</script>

    </body>
</html>