<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Edit Ticket</title>

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
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						
                            <h4 class="page-title">Raise New Ticket (Dispatch)</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">
<form id="loginForm" method="post" action="<?php echo page_url;?>Dispatch_support/raise_ticket" enctype="multipart/form-data">
                             <div class="row">
                                             
											<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">Business Location</label>
														 <span id="error_business_loc" style="color:red;">*</span>
														 <select class="form-control" id="business_loc" name="business_loc">
													<option value="">--Select Business Location--</option>
													<?php 
													$this->db->select('a.company_name, a.business_loc_id,a.state_id, a.city_id,b. 	state_id,b.state_name,c.city_id,c.city_name')->from('business_location a')->join('states b','a.state_id=b.state_id','left')->join('cities c','a.city_id=c.city_id','left')->where('business_loc_status','1');
													$this->db->order_by('a.company_name','asc');
													$query = $this->db->get();
													$res = $query->result();
													foreach($res as $row){
													?>
													<option value="<?php echo $row->business_loc_id;?>"><?php echo $row->company_name;?>(<?php echo $row->state_name;?>, <?php echo $row->city_name;?>)</option>
											<?php }?>		
												</select>
												<script type="text/javascript">
											
													$("#business_loc").change(function(){
													var business_loc=$("#business_loc").val();
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Master/User_management/select_department",
													data:"business_loc="+business_loc,
													success:function(data){
													$("#department").html(data);
													}
													});
													});
											
												</script>
												
													</div>
												</div>
												
											   <div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">Department</label>
														 <span id="error_department" style="color:red;">*</span>
														 <select class="form-control" id="department" name="department">
													<option value="">--Select Department--</option>
													
												</select>
																																		<script type="text/javascript">
											
													$("#department").change(function(){
													var department=$("#department").val();
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Dispatch_support/user_list",
													data:"department="+department,
													success:function(data){
													$("#user_id").html(data);
													}
													});
													});
											
												</script>
													</div>
												</div>
												
												
												<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">User</label>
														 <span id="error_user_id" style="color:red;">*</span>
														 <select class="form-control select3" id="user_id" name="user_id">
													<option value="">--Select User--</option>
													
												</select>
													</div>
												</div>
												
												<div class="col-md-12">
												<div class="form-group">
												<label for="field-2" class="control-label">Remarks/ Problem/ Description</label>
												<span id="error_remarks" style="color:red;">*</span>
												<textarea class="form-control" name="remarks" id="remarks"></textarea>
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
	var business_loc= $("#business_loc").val();
if(business_loc=='')
{
	$("#error_business_loc").html('Required!');
}
var item_name = $("#item_name").val();
if(item_name=='')
{
	$("#error_item_name").html('Required!');
}
var location = $("#location").val();
if(location=='')
{
	$("#error_location").html('Required!');
}

var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}


if(business_loc==''|| item_name=='' || location=='' ||  status=='' )
{
	
	return false;
}

});
});
</script>
 <script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
    </body>
</html>