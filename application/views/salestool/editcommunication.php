<?php
$salest=$this->db->select('*')->from('salestoolmessages')->where('id',$this->uri->segment(3))->get();
if($salest->num_rows()>0)
{
	foreach($salest->result() as $edit);
}else{
	echo "Invalid Access";exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Communication Edit</title>

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

                            <h4 class="page-title">Edit Communication</h4>

                        </div>

                    </div>

                </div>

                <!-- end page title end breadcrumb -->




<form id="loginForm" method="post" action="<?php echo page_url;?>Salestool/updatecommunication/<?php echo $this->uri->segment(3);?>">
                <div class="row" style="padding-top:50px;">

                    <div class="col-xs-12">

                        <div class="card-box">



                              <div class="row">
							  <div class="col-md-12">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">TYPE</label>
														<span id="error_holidayname" style="color:red;"></span>
                                                       <select name="type" id="type" class="form-control">
													   
													   <option value="">Select Type</option>
														<?php
														$restye=$this->db->select('id,type')->from('salestoolcommunication')->where('status','1')->get();
														if($restye->num_rows()>0)
														{
														foreach($restye->result() as $restye1)
														{
														?>
														<option value="<?php echo $restye1->id;?>" <?php if($restye1->id==$edit->type){?> selected <?php } ?>><?php echo $restye1->type;?></option>
														<?php
														}
														}
														?>
									
										
													   </select>
                                                    </div>
                                                </div>
												
												 <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Include PDF</label><br/>
														<span id="error_datepicker1" style="color:red;"></span>
                                                        <input type="checkbox" id="pdf" name="pdf" value="1" <?php if($edit->pdf=='1'){?> checked <?php } ?>>
                                                    </div>
                                                </div>
												
												 <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Include Video</label><br/>
														<span id="error_datepicker1" style="color:red;"></span>
                                                        <input type="checkbox" name="video" id="video" value="1" <?php if($edit->video=='1'){?> checked <?php } ?>>
                                                    </div>
                                                </div>
												<div style="clear:both;height:10px;"></div>
												
												 <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Message</label>
														<span id="error_holiday_days" style="color:red;"></span>
                                                        <textarea class="form-control" name="msg" id="msg" required><?php echo $edit->communication;?></textarea>
                                                    </div>
                                                </div>
												
												
												 <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Finishing Line</label>
														<span id="error_holiday_days" style="color:red;"></span>
                                                        <textarea class="form-control" name="fmsg" id="fmsg"><?php echo $edit->endline;?></textarea>
                                                    </div>
                                                </div>
												
												
												
												 <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Status</label>
														<span id="error_holiday_days" style="color:red;"></span>
                                                        <select class="form-control" name="status" id="status">
															<option value="1" <?php if($edit->status==1){?> selected <?php } ?>>Active</option>
															<option value="0" <?php if($edit->status==0){?> selected <?php } ?>>Inactive</option>

														</select>
                                                    </div>
                                                </div>
												</div>
												
												<div class="col-md-12">
												
												<div class="col-md-4"></div>
												<div class="col-md-4"></div>
												<div class="col-md-4"><input type="submit" class="btn btn-success btn-md pull-right" name="sub" value="Update"></div>
												</div>
                                               
											   
                                            </div>
											
											
                            <!-- end row -->

                        </div> <!-- end ard-box -->

                    </div><!-- end col-->



                </div>

</form>              
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