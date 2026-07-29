<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Prestogroup Edit User List</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
		 <link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" >
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
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
            <div class="container-fluid">
				<!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right">
						  
                           </div>
                           
                            <h4 class="page-title">Edit User Profile</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
			<div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">

								<?php
								$id = $this->uri->segment(4);
								$this->db->select('*')->from('system_users')->where('user_id',$id);
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $row)
								?>
								
                                   <form method="post" action="<?php echo page_url;?>Master/User_management/update_user_profile/<?php echo $row->user_id;?>" enctype="multipart/form-data">
								   <input type="hidden" name="old_img" value="<?php echo $row->profile_image;?>" >
								   <input type="hidden" name="old_adharcard" value="<?php echo $row->aadharcard;?>">
										 <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>First Name</label>
														<span id="error_first_name" style="color:red;">*</span>
														<input type="text" class="form-control" name="first_name" id="first_name" value="<?php echo $row->first_name;?>">
												
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Last Name</label>
														<span id="error_last_name" style="color:red;">*</span>
														<input type="text" class="form-control" name="last_name" id="last_name" value="<?php echo $row->last_name;?>">
												
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Email-ID</label>
														<span id="error_email" style="color:red;">*</span>
														<input type="text" class="form-control" name="email" id="email" value="<?php echo $row->email;?>">
												
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Contact No</label>
														 <span id="error_contact_number" style="color:red;">*</span>
														 <input placeholder="" data-mask="(999) 999-9999" class="form-control" type="text" name="contact_number" id="contact_number" value="<?php echo $row->contact_number;?>">
													</div>
												</div>
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Alternate No.</label>
														<span id="error_name" style="color:red;"></span>
														<input type="text" class="form-control" name="alternate_number" id="alternate_number" value="<?php echo $row->alternate_number;?>">
												
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Father Name</label>
														<span id="error_father_name" style="color:red;"></span>
														<input type="text" class="form-control" name="father_name" id="father_name" value="<?php echo $row->father_name;?>">
												
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Mother Name</label>
														<span id="error_mother_name" style="color:red;"></span>
														<input type="text" class="form-control" name="mother_name" id="mother_name" value="<?php echo $row->mother_name;?>">
												
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Date of Birth</label>
														<span id="error_date_of_birth" style="color:red;"></span>
														<input type="date" class="form-control" name="date_of_birth" id="date_of_birth" placeholder="mm/dd/yyyy" id="date_of_birth" value="<?php echo $row->date_of_birth;?>">
												
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Date of Joining</label>
														<span id="error_date_of_joining" style="color:red;"></span>
														<input type="date" class="form-control" name="date_of_joining" id="date_of_joining" value="<?php echo $row->date_of_joining;?>">
												
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Qualification</label>
														<span id="error_qualification" style="color:red;"></span>
														<input type="text" class="form-control" name="qualification" id="qualification" value="<?php echo $row->qualification;?>">
												
                                                    </div>
                                                </div>
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Adhar Card</label>
														<span id="error_adharcard" style="color:red;"></span>
														<div class="row">
															<div class="col-md-6">
																<input type="file" class="form-control" name="adharcard" id="adharcard" value="<?php echo $row->aadharcard;?>">
															</div>
															<div class="col-md-6">
																<img src="<?php echo user_profile;?>document/<?php echo $row->aadharcard;?>" width="25%" class="img-circle">
															</div>
														</div>
														
														
												
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>PAN Number</label>
														<span id="error_pancard" style="color:red;"></span>
														<input type="text" class="form-control" name="pancard" id="pancard" value="<?php echo $row->pancard;?>">
												
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Profile Photo</label>
														<span id="error_photo" style="color:red;">*</span>
															<div class="row">
															<div class="col-md-6">
																	<input type="file" class="form-control" name="photo" id="photo" value="<?php echo $row->profile_image;?>">
															</div>
															<div class="col-md-6">
																	<img src="<?php echo user_profile;?><?php echo $row->profile_image;?>" width="25%" class="img-circle">
															</div>
														</div>
													
													
                                                    </div>
                                                </div>
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Business Location</label>
														<span id="error_business_loc" style="color:red;">*</span>
														<select class="form-control" id="business_loc" name="business_loc">
													<option value="<?php echo $row->business_location;?>">--Select Business Location--</option>
													<?php 
													$this->db->select('a.company_name, a.business_loc_id,a.state_id, a.city_id,b. 	state_id,b.state_name,c.city_id,c.city_name')->from('business_location a')->join('states b','a.state_id=b.state_id','left')->join('cities c','a.city_id=c.city_id','left')->where('business_loc_status','1');
													$this->db->order_by('a.company_name','asc');
													$query = $this->db->get();
													$res = $query->result();
													foreach($res as $business_location){
													?>
													<option value="<?php echo $business_location->business_loc_id;?>" <?php if($row->business_location==$business_location->business_loc_id){echo "selected";}?>><?php echo $business_location->company_name;?>(<?php echo $business_location->state_name;?>, <?php echo $business_location->city_name;?>)</option>
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
													<option value="<?php echo $row->department_id;?>"><?php 
													$query = $this->db->select('department_id, department')->from('departments')->where('department_id',$row->department_id)->get();
													foreach($query->result() as $department){
														echo $department->department;
													}
													?></option>
													
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
													<option value="<?php echo $row->user_role_id;?>"><?php 
													$query = $this->db->select(' user_role_id, user_role')->from('user_role')->where('user_role_id',$row->user_role_id)->get();
													foreach($query->result() as $user_role){
														echo $user_role->user_role;
													}
													?></option>
													
												</select>
												
                                                    </div>
                                                </div>
												<div class="col-md-9">
												<div class="form-group">
														 <label for="field-2" class="control-label">Address</label>
														 <Span id="error_address" style="color:red;"></span>
														 <textarea class="form-control" name="address" cols="50" rows="4" id="address"><?php echo trim($row->address);?></textarea>
														 
													</div>
												</div>
											
											<?php
											if($row->user_role_id<>'1')
											{
											?>
											<div class="col-md-3">
												<div class="form-group">
												<label for="field-2" class="control-label">FMS Process</label>
												
												<select class="form-control" id="fmsp" name="fmsp">
													<option value="<?php echo $row->fms_process;?>">FMS Process</option>
													<?php
															$rep=$this->db->select('id,production_flow')->from('production_flow')->where('status','1')->order_by('sortorder','ASC')->get();
															if($rep->num_rows()>0)
															{
																foreach($rep->result() as $respp)
																{
															?>
															<option value="<?php echo $respp->id;?>" <?php if($respp->id==$row->fms_process){?> selected <?php } ?>><?php echo $respp->production_flow;?></option>
															<?php
																}
															}
															?>
												</select>
												</div>
											</div>
											<?php
											}else
											{
											?>
<input type="hidden" name="fmsp" value='0'>
											<?php
											}
											?>											
									<div class="col-md-3">
												<div class="form-group">
												<label for="field-2" class="control-label">Status</label>
												<span id="error_status" style="color:red;">*</span>
												<select class="form-control" id="status" name="status">
													<option value="<?php echo $row->user_status;?>">--Select Status--</option>
													<option value="1" <?php if($row->user_status=='1'){echo "selected";}?>>Active</option>
													<option value="0" <?php if($row->user_status=='0'){echo "selected";}?>>Inactive</option>
												</select>
												</div>
											</div>
                                            
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
		<!-- App js -->
		<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
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
<script>
		 // Date Picker
            jQuery('#date_of_birth').datepicker();
			jQuery('#date_of_joining').datepicker();
            jQuery('#datepicker-autoclose').datepicker({
                autoclose: true,
                todayHighlight: true
            });
		</script>
    </body>
</html>