<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Edit Quarterly TAT</title>

       
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
		<link href="<?php echo assets_url;?>plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

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
          <?php $this->load->view('common/nav-menu');?>
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
                            <h4 class="page-title">Edit Quarterly TAT</h4>
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
								$this->db->select('*')->from('quarterly_tat')->where('id',$id);
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $row)
								
								?>
								
                                   <form method="post" action="<?php echo page_url;?>Tat_management/update_quarterly_tat/<?php echo $row->id;?>">
										<div class="col-md-4">
											 <div class="form-group">
                                                        <label for="field-1" class="control-label">Month</label>
														<span id="error_monthly" style="color:red;">*</span>
														<select class="form-control" name="month" id="month">
														
														<option value="January" <?php if($row->month=='January'){echo "selected";}?>>January</option>
														<option value="February" <?php if($row->month=='February'){echo "selected";}?>>February </option>
														<option value="March" <?php if($row->month=='March'){echo "selected";}?>>March </option>
														<option value="April" <?php if($row->month=='April'){echo "selected";}?>>April </option>
														<option value="May" <?php if($row->month=='May'){echo "selected";}?>>May </option>
														<option value="June" <?php if($row->month=='June'){echo "selected";}?>>June </option>
														<option value="July" <?php if($row->month=='July'){echo "selected";}?>>July </option>
														<option value="August" <?php if($row->month=='August'){echo "selected";}?>>August </option>
														<option value="September" <?php if($row->month=='September'){echo "selected";}?>>September </option>
														<option value="October" <?php if($row->month=='October'){echo "selected";}?>>October </option>
														<option value="November" <?php if($row->month=='November'){echo "selected";}?>>November</option>
														<option value="December" <?php if($row->month=='December'){echo "selected";}?>>December</option>
														
														</select>
                                                    </div>
										</div>
										
										
										<div class="col-md-4">
											<div class="form-group">
												<label>Status</label>
												<select class="form-control" id="status" name="status">
													<option value="<?php echo $row->status;?>"><?php if($row->status=='1'){echo "Active";}else{echo "Inactive";}?></option>
													<option value="1" <?php if($row->status=='1'){echo "selected";}?>>Active</option>
													<option value="0" <?php if($row->status=='0'){echo "selected";}?>>Inactive</option>
												</select>
											</div>
										</div>
										<div class="col-md-9"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" id="save" class="btn btn-success" value="Update">
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
		<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
		<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script>

		// Time Picker
            jQuery('#timepicker').timepicker({
                defaultTIme : false
            });
			jQuery('#timepicker4').timepicker({
                defaultTIme : false
            });
            jQuery('#timepicker2').timepicker({
                showMeridian : false
            });
            jQuery('#timepicker3').timepicker({
                minuteStep : 15
            });
			 jQuery('#datepicker').datepicker();
            jQuery('#datepicker-autoclose').datepicker({
                autoclose: true,
                todayHighlight: true
            });
			jQuery('#datepicker-autoclose1').datepicker({
                autoclose: true,
                todayHighlight: true
            });
		
		</script>
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script language="javascript" type="text/javascript">   
jQuery.noConflict();
$(document).ready(function() {
$("#save").click(function() {

var month = $("#month").val();
if(month=='')
{
	
	$("#error_month").html('Required!');
}

var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}


if(month=='' || status=='' )
{
	
	return false;
}

});
});
</script>


    </body>
</html>