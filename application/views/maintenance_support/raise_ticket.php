<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Raise New Ticket</title>

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
						
                            <h4 class="page-title text-center">DF Related Support ? Raise New Ticket</h4>
                        </div>
                    </div>
                    <div class="col-md-2 pull-right">
                    	<a href="<?php echo page_url;?>Maintenance_support/viewalltickets"><span class="btn btn-danger">Click Here to View Tickets</span></a>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
				<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">

								
                                   <form id="loginForm" method="post" action="<?php echo page_url;?>Maintenance_support/raise_ticket" enctype="multipart/form-data">
                                   	<div class="col-md-4">
                                   		<div class="form-group">
                                   			<label>Select DF <span id="error_dfno" style="color:red; position: relative;">*<?php echo form_error('dfno'); ?></span></label>
                                   			
                                   			<select class="form-control select2" name="dfno" id="dfno" required>
                                   				<option value="">Select DF No</option>
                                   				<?php 
                                   				$q = $this->db->select('id, df_no, df_description')->from('df_release')->where('df_status',0)->get();
                                   					foreach ($q->result() as $row) {
                                   						
                                   					
                                   				?>
                                   				<option value="<?php echo $row->id;?>"><?php echo $row->df_no;?> <?php echo $row->df_description;?></option>
                                   			<?php }?>
                                   			</select>
                                   		</div>
                                   			</div>
											<div class="col-md-4">
													<div class="form-group">
													<label for="field-2" class="control-label">Department <span id="error_department" style="color:red; position: relative;">*<?php echo form_error('department'); ?></span></label>
													
													<select class="form-control" id="department" name="department" required>
													<option value="">--Select Department--</option>
													<option value="ALL">ALL</option>
													<?php $q = $this->db->select('department_id, department')->from('departments')->where('business_loc_id',2)->where('status',1)->get();
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
													url: "<?php echo page_url; ?>Delegation/user_list_new",
													data: { department: department },
													success: function(data) {
													$("#user_id").html('<option value="ALL">ALL</option>' + data);
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

												
												<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">User  <span id="error_user_id" style="color:red; position:relative;">*<?php echo form_error('user_id'); ?></span></label>
														<select class="select2" id="user_id" name="user_id[]" multiple required>
													
													
												</select>
													</div>
												</div>
												
												<div class="col-md-9">
												<div class="form-group">
												<label for="field-2" class="control-label">Remarks/ Problem/ Description <span id="error_remarks" style="color:red; position:relative;">*</span></label>
												
												<textarea class="form-control" name="remarks" id="remarks" required></textarea>
												<script>
												CKEDITOR.replace( 'remarks' );
												</script>
												</div>
											</div>
											
											<div class="col-md-3">
												<div class="form-group">
												<label for="field-2" class="control-label">Attachment</label>
												<span id="error_status" style="color:red;"></span>
												<input type="file" name="screen_shot" id="screen_shot" value="">
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