<?php 
$business_location = $this->session->userdata['logged_in']['business_location'];
$master_access_control_ready = $this->db->field_exists('master_write_access', 'user_role');
?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright;?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle;?> Edit Role</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
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
						  
                               
                            </div>
                           
                            <h4 class="page-title">EDIT ROLE INFORMATION</h4>
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
								$this->db->select('*')->from('user_role')->where('user_role_id',$id);
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $user_role)
								$master_write_access = isset($user_role->master_write_access) ? (string)$user_role->master_write_access : '1';
								?>
								
                                   <form method="post" id="loginForm" action="<?php echo page_url;?>Master/User_management/update_user_role/<?php echo $user_role->user_role_id;?>">
										 <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Business Location</label>
														<span id="error_business_loc" style="color:red;">*</span>
												<select class="form-control" id="business_loc" name="business_loc" readonly>
													<?php 
													$this->db->select('a.company_name, a.business_loc_id,a.state_id, a.city_id,b. 	state_id,b.state_name,c.city_id,c.city_name')->from('business_location a')->join('states b','a.state_id=b.state_id','left')->join('cities c','a.city_id=c.city_id','left')->where('business_loc_status','1')->where('a.business_loc_id',$business_location);
													$this->db->order_by('a.company_name','asc');
													$query = $this->db->get();
													$res = $query->result();
													foreach($res as $row){
													?>
													<option value="<?php echo $row->business_loc_id;?>" <?php if($user_role->business_loc_id==$row->business_loc_id){echo "selected";}?>><?php echo $row->company_name;?>(<?php echo $row->state_name;?>, <?php echo $row->city_name;?>)</option>
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
                                                        <label for="field-1" class="control-label">Department</label>
														<span id="error_department" style="color:red;">*</span>
												<select class="form-control" id="department" name="department" readonly>
													<option value="<?php echo $user_role->department_id;?>"><?php $query = $this->db->select('department_id,department')->from('departments')->where('department_id',$user_role->department_id)->get();
													foreach($query->result() as $departmentrec){
														echo $departmentrec->department;
													}
													?></option>
													
												</select>
												    </div>
                                                </div>
												
												<div class="col-md-6">
													<div class="form-group">
														 <label for="field-2" class="control-label">User Role</label>
														 <span id="error_user_role" style="color:red;">*</span>
														 <input type="text" class="form-control" name="user_role" id="user_role"  value="<?php echo $user_role->user_role;?>">
													</div>
												</div>
												
												<div class="col-md-3">
												<div class="form-group">
												<label for="field-2" class="control-label">Status</label>
												<span id="error_status" style="color:red;">*</span>
												<select class="form-control" id="status" name="status">
													<option value="<?php echo $user_role->status;?>">--Select Status--</option>
													<option value="1" <?php if($user_role->status=='1'){echo "selected";}?>>Active</option>
													<option value="0" <?php if($user_role->status=='0'){echo "selected";}?>>Inactive</option>
												</select>
												</div>
											</div>

											<div class="col-md-3">
												<div class="form-group">
													<label for="field-2" class="control-label">Master Access</label>
													<?php if(!$master_access_control_ready){ ?>
														<div style="color:#d9534f;font-size:12px;">Run `Database/ea_master_profile_001.sql` to enable EA master control.</div>
													<?php } ?>
													<select class="form-control" id="master_write_access" name="master_write_access" <?php if(!$master_access_control_ready){ echo 'disabled'; } ?>>
														<option value="1" <?php if($master_write_access === '1'){echo "selected";}?>>Allow Master Changes</option>
														<option value="0" <?php if($master_write_access === '0'){echo "selected";}?>>View Only (EA)</option>
													</select>
													<?php if(!$master_access_control_ready){ ?>
														<input type="hidden" name="master_write_access" value="<?php echo $master_write_access; ?>">
													<?php } ?>
												</div>
											</div>
                                            </div>
										<div class="col-md-9"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" class="btn btn-success" id="roleupdate" value="Update">
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
<script>
$(document).ready(function(){
	   $("#roleupdate").attr('disabled',false);
	   $("#roleupdate").val('Update');
  $("#loginForm").on("submit", function(){
   // $("#pageloader").fadeIn();
   $("#roleupdate").attr('disabled',true);
     $("#roleupdate").val('Please Wait...');
  });//submit
});//document ready
</script>
<script language="javascript" type="text/javascript">   
jQuery.noConflict();
$(document).ready(function() {
$("#save").click(function() {
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

var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}


if(business_loc=='' || department==''|| user_role=='' || status=='' )
{
	
	return false;
}

});
});
</script>
    </body>
</html>
