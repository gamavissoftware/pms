<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Prestogroup Edit Employee Detail</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
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
<style>
table.manglesh thead th {
				background: #003366;
				color:#fff;
				font-weight:bold;
			}
			
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
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="btn-group pull-right">
                              
                            </div>
                            <h4 class="page-title">Edit Employee Detail</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->


                <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">
								<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
								<?php
								$id = $this->uri->segment(3);
								$this->db->select('id,employee_name, employee_code, reporting_manager, factory,employee_for')->from('prestogroup_employees')->where('id',$id);
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $row)
								
								?>
								
                                   <form method="post" id="loginForm" action="<?php echo page_url;?>Hr/update_employee_data/<?php echo $row->id;?>">
								    <div id="pageloader">
                                    <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
                                    </div>
									
												
											<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Employee Name</label>
														 <span id="error_employee_name" style="color:red;"></span>
														 <input type="text" class="form-control" name="employee_name" id="employee_name"  value="<?php echo $row->employee_name;?>" required>
													</div>
												</div>
													<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Employee Code</label>
														 <span id="error_employee_code" style="color:red;"></span>
														 <input type="text" class="form-control" name="employee_code" id="employee_code"  value="<?php echo $row->employee_code;?>">
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Reporting Manager</label>
														 <span id="error_hod_name" style="color:red;"></span>
														<select class="form-control" name="hod_name" id="hod_name">
														    <option value="">Select HOD</option>
														    <?php 
														    $query = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('hide_profile','0')->where('user_status','1')->get();
														    foreach($query->result() as $row1){?>
														<option value="<?php echo $row1->user_id;?>" <?php if($row->reporting_manager==$row1->user_id){echo "selected";}?>><?php echo $row1->first_name." ".$row1->last_name;?></option>    
														    <?php
														    }
														    ?>
														</select>
													</div>
												</div>
												
											
												
											
												<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Factory</label>
														 <span id="error_factory" style="color:red;"></span>
														<select class="form-control" name="factory">
														    <option value="A WING" <?php if($row->factory=='A WING'){echo "selected";}?>>A WING</option>
														    <option value="B WING" <?php if($row->factory=='B WING'){echo "selected";}?>>B WING</option>
														    <option value="I-10" <?php if($row->factory=='I-10'){echo "selected";}?>>I-10</option>
														</select>
													</div>
												</div>	
												<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Employee For</label>
														 <span id="error_factory" style="color:red;"></span>
														<select class="form-control" name="employee_for">
														    <option value="1" <?php if($row->employee_for=='1'){echo "selected";}?>>PRESTOGROUP EMPLOYEE</option>
														    <option value="2" <?php if($row->employee_for=='2'){echo "selected";}?>>TESTRONIX EMPLOYEE</option>
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

        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script language="javascript" type="text/javascript">   
$(document).ready(function() {
	$('.select2').select2({ });
$('.select3').select2({ });
$('.select4').select2({ });
$("#save").click(function() {
	
	var business_loc = $("#business_loc").val();
if(business_loc=='')
{
	$("#error_business_loc").html('Required!');
}
var user_id = $("#user_id").val();
if(user_id=='')
{
	$("#error_user_id").html('Required!');
}
var reporting_head = $("#reporting_head").val();
if(reporting_head=='')
{
	
	$("#error_reporting_head").html('Required!');
}

var what = $("#what").val();
if(what=='')
{
	
	$("#error_what").html('Required!');
}

var how = $("#how").val();
if(how=='')
{
	
	$("#error_how").html('Required!');
}
var when = $("#when").val();
if(when=='')
{
	
	$("#error_when").html('Required!');
}


var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}


if(business_loc=='' || user_id=='' || reporting_head=='' || what=='' || how==''|| when=='' || status=='' || set_order=='' || response_type=='' || actiontobetaken=='')
{
	return false;
}

});
});


	

</script>

<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready
</script>
<script> $(document).ready(function() {
				$("#datepicker1").datepicker();
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
                })

            });</script>
			</body>
</html>