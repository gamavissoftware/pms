<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Prestogroup Tech Item</title>
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
						 <div class="btn-group pull-right">
						  <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">Add New  IT Assets</button>
                           </div>
                           
                            <h4 class="page-title"> IT Assets List</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

                 <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>Sr No.</th>
									<th>Location</th>
									<th>Department</th>
									<th>Asset Name</th>
									<th>Unique ID</th>
									<th>User</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                    
                                </tr>
                                </thead>
                                
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
 <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="<?php echo page_url;?>Tech_support/items">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Add New  IT Assets</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                               
											   <div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">Location</label>
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
													url:"<?php echo page_url;?>Tech_support/select_installed_location",
													data:"business_loc="+business_loc,
													success:function(data){
													$("#location").html(data);
													}
													});
													});
											
												</script>
												
													</div>
												</div>
												
											   <div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">Department</label>
														 <span id="error_location" style="color:red;">*</span>
														 <select class="form-control select2" id="location" name="location">
													<option value="">--Select Department--</option>
													
												</select>
												
													</div>
												</div>
												
												<div class="col-md-4">
											<div class="form-group">
												<label>User <span style="color:red;" id="error_user_name">*</span></label>
												<select class="form-control select3" id="user_name" name="user_name">
												<option value="">--Select User--</option>
												 <?php $query = $this->db->select('user_id, first_name, last_name, user_status, hide_profile')->from('system_users')->where('user_status','1')->where('hide_profile','0')->get();
												 foreach($query->result() as $users){?>
												 <option value="<?php echo $users->user_id;?>"><?php echo $users->first_name;?> <?php echo $users->last_name;?></option>
												 <?php }?>
												</select>
											</div>
										</div>
												
												<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">Asset Name</label>
														 <span id="error_item_name" style="color:red;"></span>
														 <input type="text" class="form-control" name="item_name" id="item_name"  value="">
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">Unique ID</label>
														 <span id="error_unique_id" style="color:red;"></span>
														 <input type="text" class="form-control" name="unique_id" id="unique_id"  value="">
													</div>
												</div>
												
												
												<div class="col-md-4">
												<div class="form-group">
												<label for="field-2" class="control-label">Status</label>
												<span id="error_status" style="color:red;"></span>
												<select class="form-control" id="status" name="status">
													<option value="">--Select Status--</option>
													<option value="1">Active</option>
													<option value="0">Inactive</option>
												</select>
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

       

        <script>
$( document ).ready(function() {
$('#example').dataTable({
"bProcessing": true,
"pagination":true,
"sAjaxSource": "<?php echo page_url;?>Tech_support/tech_item_list",
"aoColumns": [
				{ mData: 'sr_no' } ,
				{ mData: 'business_location' },
				{ mData: 'location' },
				{ mData: 'item_name' },
				{ mData: 'unique_id' },
				{ mData: 'user' },
				{ mData: 'status' },
				{ mData: 'edit' }
				
				
		]
});   
});

</script>
<script language="javascript" type="text/javascript">   
$(document).ready(function() {
	$('.select2').select2({ });
	$('.select3').select2({ });
	$('.select4').select2({ });
$("#save").click(function() {
var item_name = $("#item_name").val();
if(item_name=='')
{
	$("#error_item_name").html('Required!');
}
var business_loc = $("#business_loc").val();
if(business_loc=='')
{
	$("#error_business_loc").html('Required!');
}

var user_name = $("#user_name").val();
if(user_name=='')
{
	$("#error_user_name").html('Required!');
}
var location = $("#location").val();
if(location=='')
{
	$("#error_location").html('Required!');
}
var unique_id = $("#unique_id").val();
if(unique_id=='')
{
	$("#error_unique_id").html('Required!');
}
var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}

if(business_loc==''|| item_name=='' || location=='' || user_name=='' || unique_id=='' ||  status=='' )
{
	
	return false;
}

});
});
</script>
 <script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
    </body>
</html>