<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Sales help Ticket</title>
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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" rel="stylesheet"/>
        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
		<style>
			.divheight{
			padding-top:110px;
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
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						
                            <h4 class="page-title">SALES HELP TICKET</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
                <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
				
               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">
<form id="loginForm" method="post" action="<?php echo page_url;?>Delegation/sales_support" enctype="multipart/form-data">
        <div id="pageloader">
        <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
        </div>	
                             <div class="row">
                                             
											<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">YOUR NAME</label>
														 <span id="error_your_name" style="color:red;">*</span>
										<select class="form-control" id="your_name" name="your_name">
										<?php 
										$user_id =$this->session->userdata['logged_in']['user_id'];	
										$query = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('user_id',$user_id)->get();
										foreach($query->result() as $users){?>
										<option value="<?php echo $users->user_id;?>"><?php echo strtoupper($users->first_name." ".$users->last_name);?></option>
										<?php }?></select>
												</div>
												</div>
											
												<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">Sales Person</label>
														 <span id="error_sales_person" style="color:red;">*</span>
														<select class="form-control" id="sales_person" name="sales_person">
										<?php 
										$department = "29";	
										$query = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('department_id',$department)->where('user_status','1')->where('hide_profile','0')->get();
										foreach($query->result() as $users){?>
										<option value="<?php echo $users->user_id;?>"><?php echo strtoupper($users->first_name." ".$users->last_name);?></option>
										<?php }?></select>
													</div>
												</div>
												<div class="col-md-4">
												<div class="form-group">
												<label for="field-2" class="control-label">Customer Name</label>
												
												<input type="text" class="form-control" name="customer_name" id="customer_name" value="" required>
												</div>
											</div>
											
											
											 <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Remarks</label>
														<span id="error_remarks" style="color:red;">*</span>
                                                       <input type="text" class="form-control" name="remarks" id="remarks" value="" required>
                                                    </div>
                                                </div>
												 <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Help Needed</label>
														<span id="error_help_needed" style="color:red;">*</span>
                                                       <input type="text" class="form-control" name="help_needed" id="help_needed" value="" required>
                                                    </div>
                                                </div>
											
											<div class="col-md-12">
											<div class="form-group pull-right">
												<input type="submit" id="save" class="btn btn-info" value="Submit">
											</div>												
											</div>
											
                                        </div>
                             </form>           
										
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

<script language="javascript" type="text/javascript">   
$(document).ready(function() {
	$('.select2').select2({ });
	$('.select3').select2({ });
	$('.select4').select2({ });
$("#save").click(function() {
	var your_name= $("#your_name").val();
if(your_name=='')
{
	$("#error_your_name").html('Required!');
}
var sales_person = $("#sales_person").val();
if(sales_person=='')
{
	$("#error_sales_person").html('Required!');
}
var remarks = $("#remarks").val();
if(remarks=='')
{
	$("#error_remarks").html('Required!');
}



if(your_name==''|| sales_person=='' || remarks=='')
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
 var date = new Date();
date.setDate(date.getDate());
   
$('.datepicker').datepicker({
    todayHighlight:true
    
    
});

                $("#datepicker1").datepicker({
					orientation: 'bottom',
					todayHighlight:true
				});
			//	$('.datepicker').datepicker({todayHighlight:true});
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
					
                })

            });</script>
    </body>
</html>