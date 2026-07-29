<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Prestogroup Support List</title>

        <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" >
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
            <div class="container-fluid">

                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12" style="margin-top:20px">
                       <button class="btn btn-primary waves-effect waves-light pull-right" data-toggle="modal" data-target=".bs-example-modal-lg">Add New User</button>
                        <h4 class="page-title">Prestogroup Users List</h4>
					<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>	
                    </div>
                </div>
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="<?php echo page_url;?>Master/User_management/add_new_user" enctype="multipart/form-data">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Add New User</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>First Name</label>
														<span id="error_first_name" style="color:red;">*</span>
														<input type="text" class="form-control" name="first_name" id="first_name" value="">
												
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Last Name</label>
														<span id="error_last_name" style="color:red;">*</span>
														<input type="text" class="form-control" name="last_name" id="last_name" value="">
												
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Email-ID</label>
														<span id="error_email" style="color:red;">*</span>
														<input type="text" class="form-control" name="email" id="email" value="">
												
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Contact No</label>
														 <span id="error_contact_number" style="color:red;">*</span>
														 <input placeholder="" data-mask="(999) 999-9999" class="form-control" type="text" name="contact_number" id="contact_number" value="">
													</div>
												</div>
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Alternate No.</label>
														<span id="error_name" style="color:red;"></span>
														<input type="text" class="form-control" name="alternate_number" id="alternate_number" value="">
												
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Father Name</label>
														<span id="error_father_name" style="color:red;">*</span>
														<input type="text" class="form-control" name="father_name" id="father_name" value="">
												
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Mother Name</label>
														<span id="error_mother_name" style="color:red;">*</span>
														<input type="text" class="form-control" name="mother_name" id="mother_name" value="">
												
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Date of Birth</label>
														<span id="error_date_of_birth" style="color:red;">*</span>
														<input type="text" class="form-control" name="date_of_birth" id="date_of_birth" placeholder="mm/dd/yyyy" id="date_of_birth" value="">
												
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Date of Joining</label>
														<span id="error_date_of_joining" style="color:red;">*</span>
														<input type="text" class="form-control" name="date_of_joining" id="date_of_joining" value="">
												
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Qualification</label>
														<span id="error_qualification" style="color:red;">*</span>
														<input type="text" class="form-control" name="qualification" id="qualification" value="">
												
                                                    </div>
                                                </div>
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Adhar Card</label>
														<span id="error_adharcard" style="color:red;">*</span>
														<input type="file" class="form-control" name="adharcard" id="adharcard" value="">
												
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>PAN Number</label>
														<span id="error_pancard" style="color:red;">*</span>
														<input type="text" class="form-control" name="pancard" id="pancard" value="">
												
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Profile Photo</label>
														<span id="error_photo" style="color:red;">*</span>
														<input type="file" class="form-control" name="photo" id="photo" value="">
												
                                                    </div>
                                                </div>
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Business Location</label>
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
												
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Department</label>
														<span id="error_department" style="color:red;">*</span>
														<select class="form-control" id="department" name="department">
													<option value="">--Select Department--</option>
													
												</select>
												<script type="text/javascript">
											
													$("#department").change(function(){
													var department=$("#department").val();
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Master/User_management/select_user_roles",
													data:"department="+department,
													success:function(data){
													$("#user_role").html(data);
													}
													});
													});
											
												</script>
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>User Role</label>
														<span id="error_user_role" style="color:red;">*</span>
														<select class="form-control" id="user_role" name="user_role">
													<option value="">--Select User role--</option>
													
												</select>
												
                                                    </div>
                                                </div>
												<div class="col-md-9">
												<div class="form-group">
														 <label for="field-2" class="control-label">Address </label>
														 <Span id="error_address" style="color:red;">*</span>
														 <textarea class="form-control" name="address" id="address">
														 
														 </textarea>
														 
													</div>
												</div>
											
												
												<div class="col-md-3">
												<div class="form-group">
												<label for="field-2" class="control-label">Status</label>
												<span id="error_status" style="color:red;">*</span>
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
							

                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
						<table id="example" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sr No</th>
                                        <th>Profile Image</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Contact Number</th>
                                        <th>Business Location</th>
                                        <th>Department</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>

									
                                </tbody>
                            </table>
                        </div>
                    </div><!-- end col -->
                </div>
                <!-- end row -->


                <!-- Footer -->
               <?php $this->load->view('common/footer');?>
                <!-- End Footer -->

            </div>
            <!-- end container -->



            <!-- Right Sidebar -->
            <div class="side-bar right-bar">
                <a href="javascript:void(0);" class="right-bar-toggle">
                    <i class="zmdi zmdi-close-circle-o"></i>
                </a>
                <h4 class="">Notifications</h4>
                <div class="notification-list nicescroll">
                    <ul class="list-group list-no-border user-list">
                        <li class="list-group-item">
                            <a href="#" class="user-list-item">
                                <div class="avatar">
                                    <img src="<?php echo assets_url;?>images/users/avatar-2.jpg" alt="">
                                </div>
                                <div class="user-desc">
                                    <span class="name">Michael Zenaty</span>
                                    <span class="desc">There are new settings available</span>
                                    <span class="time">2 hours ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="#" class="user-list-item">
                                <div class="icon bg-info">
                                    <i class="zmdi zmdi-account"></i>
                                </div>
                                <div class="user-desc">
                                    <span class="name">New Signup</span>
                                    <span class="desc">There are new settings available</span>
                                    <span class="time">5 hours ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="#" class="user-list-item">
                                <div class="icon bg-pink">
                                    <i class="zmdi zmdi-comment"></i>
                                </div>
                                <div class="user-desc">
                                    <span class="name">New Message received</span>
                                    <span class="desc">There are new settings available</span>
                                    <span class="time">1 day ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="list-group-item active">
                            <a href="#" class="user-list-item">
                                <div class="avatar">
                                    <img src="<?php echo assets_url;?>images/users/avatar-3.jpg" alt="">
                                </div>
                                <div class="user-desc">
                                    <span class="name">James Anderson</span>
                                    <span class="desc">There are new settings available</span>
                                    <span class="time">2 days ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="list-group-item active">
                            <a href="#" class="user-list-item">
                                <div class="icon bg-warning">
                                    <i class="zmdi zmdi-settings"></i>
                                </div>
                                <div class="user-desc">
                                    <span class="name">Settings</span>
                                    <span class="desc">There are new settings available</span>
                                    <span class="time">1 day ago</span>
                                </div>
                            </a>
                        </li>

                    </ul>
                </div>
            </div>
            <!-- /Right-bar -->

        </div>



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

       <script>
$( document ).ready(function() {
$('#example').dataTable({
"bProcessing": true,
"pagination":true,
"sAjaxSource": "<?php echo page_url;?>Master/User_management/all_system_users_list",
"aoColumns": [
				{ mData: 'sr_no' } ,
				{ mData: 'profile_image' },
				{ mData: 'first_name' },
				{ mData: 'email' },
				{ mData: 'contact_number' },
				{ mData: 'company_name' },
				{ mData: 'department' },
				{ mData: 'user_role' },
				{ mData: 'status' },
				{ mData: 'edit' }
				
		]
});   
});

</script>
		<script>
		 // Date Picker
            jQuery('#date_of_birth').datepicker();
			jQuery('#date_of_joining').datepicker();
            jQuery('#datepicker-autoclose').datepicker({
                autoclose: true,
                todayHighlight: true
            });
		</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script language="javascript" type="text/javascript">   
jQuery.noConflict();
$(document).ready(function() {
$("#save").click(function() {
var first_name = $("#first_name").val();
if(first_name=='')
{
	$("#error_first_name").html('Required!');
}
var last_name = $("#last_name").val();
if(last_name=='')
{
	
	$("#error_last_name").html('Required!');
}
var email = $("#email").val();
if(email=='')
{
	
	$("#error_email").html('Required!');
}
var contact_number = $("#contact_number").val();
if(contact_number=='')
{
	
	$("#error_contact_number").html('Required!');
}
var father_name = $("#father_name").val();
if(father_name=='')
{
	
	$("#error_father_name").html('Required!');
}
var mother_name = $("#mother_name").val();
if(mother_name=='')
{
	
	$("#error_mother_name").html('Required!');
}
var qualification = $("#qualification").val();
if(qualification=='')
{
	
	$("#error_qualification").html('Required!');
}
var date_of_birth = $("#date_of_birth").val();
if(date_of_birth=='')
{
	
	$("#error_date_of_birth").html('Required!');
}
var date_of_joining = $("#date_of_joining").val();
if(date_of_joining=='')
{
	
	$("#error_date_of_joining").html('Required!');
}
var adharcard = $("#adharcard").val();
if(adharcard=='')
{
	
	$("#error_adharcard").html('Required!');
}
var pancard = $("#pancard").val();
if(pancard=='')
{
	
	$("#error_pancard").html('Required!');
}
var business_loc = $("#business_loc").val();
if(business_loc=='')
{
	
	$("#error_business_loc").html('Required!');
}
var department = $("#department").val();
if(department=='')
{
	
	$("#error_department").html('Required!');
}
var user_role = $("#user_role").val();
if(user_role=='')
{
	
	$("#error_user_role").html('Required!');
}
var address = $("#address").val();
if(address=='')
{
	
	$("#error_address").html('Required!');
}
var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}


if(first_name=='' || last_name==''|| email=='' || contact_number=='' || father_name=='' || mother_name=='' || date_of_birth=='' || date_of_joining=='' || qualification=='' || adharcard=='' || pancard=='' || business_loc=='' || department==''|| user_role=='' || address=='' || status=='')
{
	
	return false;
}

});
});
</script>
    </body>
</html>