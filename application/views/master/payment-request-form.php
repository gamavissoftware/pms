<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?>Payment Request form</title>

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
        <script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.3/classic/ckeditor.js"></script>

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
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-10 col-xs-12 col-md-10 col-lg-10">
                        <div class="page-title-box">
						
                            <h4 class="page-title text-center">Payment Request form</h4>
                        </div>
                    </div>
                    <div class="col-md-2 pull-right">
                    	<a href="<?php echo page_url;?>Form/your_payment_list_dashboard"><span class="btn btn-danger">Click Here to Payment Dashboard</span></a>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
				<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">

								
                                   <form id="loginForm" method="post" action="<?php echo page_url;?>Form/addpaymentform" enctype="multipart/form-data" onsubmit="handleFormSubmit(event)">
									<div id="pageloader">
									<img src="https://pms.shubhampack.in/assets/images/loading.gif" alt="processing..." />
									</div>	
                                   	<div class="col-md-3">
                                   		<div class="form-group">
                                   			<label>Date <span style="color:red;">*</span></label>
                                   			<input type="text" class="form-control" name="meetingdate" id="meetingdate" value="<?php echo date('d-m-Y');?>" readonly>
                                   		</div>
                                   	</div>
                                 
                                   		<div class="col-md-3" style="display: none;">
                                   		<div class="form-group">
                                   			<label>Time <span style="color:red;">*</span></label>
                                   			<input type="text" class="form-control" name="meetingtime" id="meetingtime" value="<?php echo date('h:i A');?>" readonly>
                                   		</div>
                                   	</div>
                                   
                                     	<div class="col-md-3">
                                   		<div class="form-group">
														<label>Company Name <span style="color:red;">*</span></label>
														<input type="text" class="form-control" name="companyname" id="companyname" value="" required>
													</div>
                                   	</div>
											<div class="col-md-3">
													<div class="form-group">
													<label for="field-2" class="control-label">Department <span id="error_department" style="color:red; position: relative;">*<?php echo form_error('department'); ?></span></label>
													
													<select class="form-control" id="department" name="department" required>
													<option value="">--Select Department--</option>
													
													<?php
														$department_id = $this->session->userdata['logged_in']['department_id'];

													 $q = $this->db->select('department_id, department')->from('departments')->where('business_loc_id',2)->where('status',1)->where('department_id',$department_id)->get();
													foreach($q->result() as $rows){?>
													<option value="<?php echo $rows->department_id;?>"><?php echo strtoupper($rows->department);?></option>
														<?php }?>
													</select>
													<script type="text/javascript">

													$("#department").change(function() {
													var department = $("#department").val();
													console.log("Selected Department: ", department); // Debugging line
													if (department === 'ALL') {
													$("#user_id").html('<option value="ALL">ALL</option>');
													} else {
													$.ajax({
													type: "post",
													url: "<?php echo page_url; ?>Form/user_list_new",
													data: { department: department },
													success: function(data) {
													$("#user_id").html(data);
													},
													error: function(xhr, status, error) {
													console.error("Error: ", xhr.responseText);
													alert("An error occurred while fetching user data. Please check the console for details.");
													}
													});
													}
													});
												</script>
												</div>
												</div>

												
												<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">User  <span id="error_user_id" style="color:red; position:relative;">*<?php echo form_error('user_id'); ?></span></label>
														<select class="form-control" id="user_id" name="user_id" required>
													
													
												</select>
													</div>
												
												</div>

												<div class="col-md-3">
													


													<div class="form-group">
														<label>Amount <span style="color:red;">*</span></label>
														<input type="text" class="form-control" name="amount" id="amount" value="" required>
													</div>

												<div class="form-group" id="paymentTypeField">
												<label>Payment Type <span style="color:red;">*</span></label>
												<select class="form-control" name="payment_type" id="paymentType">
												<option value="Advance">Advance</option>
												<option value="Pdc">PDC</option>
												<option value="Due Payment">Due Payment</option>
												<option value="Against Delivery">Against Delivery</option>
												</select>
												</div>

													<div class="form-group">
												<label for="field-2" class="control-label">Attachment</label>
												<span id="error_status" style="color:red;"></span>
												<input type="file" name="screen_shot" id="screen_shot" value="">
												</div>
												</div>
												
												<div class="col-md-9">
												<div class="form-group">
												<label for="field-2" class="control-label">Particular<span id="error_remarks" style="color:red; position:relative;">*</span></label>
												
												<textarea class="form-control" name="remarks" id="remarks" required></textarea>
												<script>
												CKEDITOR.replace( 'remarks' );
												</script>
												</div>
											</div>
											
											
											<div class="col-md-12">
												<div class="form-group pull-right">
												<input type="submit" id="save" class="btn btn-info" value="Submit"> 
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

       
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready
</script>
<script language="javascript" type="text/javascript">   

$(document).ready(function() {
	$('.select2').select2({ });
	$('.select3').select2({ });
	$('.select4').select2({ });
$("#save").click(function() {
	var dfno= $("#dfno").val();
if(dfno=='')
{
	$("#error_dfno").html('Required!');
}
var department = $("#department").val();
if(department=='')
{
	$("#error_department").html('Required!');
}

var user_id = $("#user_id").val();
if(user_id=='')
{
	
	$("#error_user_id").html('Required!');
}

if(dfno==''|| department=='' || user_id=='' )
{
	
	return false;
}

});
});
</script>
 <script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
    </body>
</html>