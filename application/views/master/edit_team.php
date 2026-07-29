<!DOCTYPE html>
<?php $team_task_settings_enabled = $this->db->field_exists('show_all_team_tasks', 'prestogroup_teams') && $this->db->field_exists('allow_task_assignment', 'prestogroup_teams'); ?>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Edit Team</title>

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
                           
                            <h4 class="page-title">Edit Team Detail</h4>
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
								$this->db->select('*')->from('prestogroup_teams')->where('team_id',$id);
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $team)
								?>
								
                                   <form method="post" id="loginForm" action="<?php echo page_url;?>Master/User_management/update_team/<?php echo $team->team_id;?>">
										 <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Business Location</label>
														<span id="error_business_loc" style="color:red;"></span>
												<select class="form-control" id="business_loc" name="business_loc">
													<?php 
													$this->db->select('a.company_name, a.business_loc_id,a.state_id, a.city_id,b. 	state_id,b.state_name,c.city_id,c.city_name')->from('business_location a')->join('states b','a.state_id=b.state_id','left')->join('cities c','a.city_id=c.city_id','left')->where('business_loc_id',$team->business_loc_id);
													$this->db->order_by('a.company_name','asc');
													$query = $this->db->get();
													$res = $query->result();
													foreach($res as $row){
													?>
													<option value="<?php echo $row->business_loc_id;?>" <?php if($team->business_loc_id==$row->business_loc_id){echo "selected";}?>><?php echo strtoupper($row->company_name);?></option>
											<?php }?>	
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
												</select>
												    </div>
                                                </div>
                                                
												
												
												<div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Department</label>
														<span id="error_department" style="color:red;">*</span>
														<select class="form-control" id="department" name="department">
													<option value="<?php echo $team->department_id;?>">--Select Department--</option>
													<?php 
													$this->db->select('department_id, business_loc_id, department,status ')->from('departments')->where('business_loc_id',$team->business_loc_id)->where('status','1');
													$this->db->order_by('department','asc');
													$query = $this->db->get();
													$res = $query->result();
													foreach($res as $dept){
													?>
													<option value="<?php echo $dept->department_id;?>" <?php if($team->department_id==$dept->department_id){echo "selected";}?>><?php echo $dept->department;?></option>
													<?php }?>
												</select>
												<script type="text/javascript">

													function loadEditTeamDepartments(){
													var business_loc=$("#business_loc").val();
													if(business_loc===''){
														$("#department").html('<option value="">--Select Department--</option>');
														$("#team_leader").html('<option value="">--Select Team Leader--</option>');
														return;
													}
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Master/User_management/select_department",
													data:"business_loc="+business_loc,
													success:function(data){
													$("#department").html(data);
													}
													});
													}
											
													$("#department").change(function(){
													var department=parseInt($("#department").val(), 10) || 0;
													var business_loc=$("#business_loc").val();
													if(department===0){
													$("#team_leader").html('<option value="">--Select Team Leader--</option>');
													return;
													}
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Master/User_management/select_team_member",
													data:{
														department: department,
														business_loc: business_loc
													},
													success:function(data){
													$("#team_leader").html(data);
													}
													});
													});
													$("#business_loc").change(function(){
													loadEditTeamDepartments();
													});
											
												</script>
                                                    </div>
                                                </div>
												
												<div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Team Leader</label>
														<span id="error_team_leader" style="color:red;">*</span>
														<select class="form-control" id="team_leader" name="team_leader">
													<option value="">--Select Team Leader--</option>
													<?php 
													$this->db->select('user_id, first_name, last_name,business_location, department_id, user_status')->from('system_users')->where('business_location',$team->business_loc_id)->where('department_id',$team->department_id)->where('user_status','1');
													$this->db->order_by('first_name','asc');
													$query = $this->db->get();
													$res = $query->result();
													foreach($res as $user){
													?>
													<option value="<?php echo $user->user_id;?>" <?php if($team->team_leader==$user->user_id){echo "selected";}?>><?php echo $user->first_name." ".$user->last_name;?></option>
													<?php }?>
												</select>
												
                                                    </div>
                                                </div>
												
												
												<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">Team Name</label>
														 <span id="error_team_name" style="color:red;"></span>
														 <input type="text" class="form-control" name="team_name" id="team_name"  value="<?php echo $team->team_name;?>">
													</div>
												</div>
												
												<div class="col-md-4" style="display: none;">
													<div class="form-group">
														 <label for="field-2" class="control-label">By Pass Conveyance Approval</label>
														 <span id="error_team_name" style="color:red;"></span>
														 <select class="form-control" name="by_pass" id="by_pass">
															<option value="1" <?php if($team->by_pass=='1'){echo "selected";}?>>Yes</option>
															<option value="0" <?php if($team->by_pass=='0'){echo "selected";}?>>No</option>
														 </select>
													</div>
												</div>
												<div class="col-md-4">
												<div class="form-group">
												<label for="field-2" class="control-label">Status</label>
												<span id="error_status" style="color:red;"></span>
												<select class="form-control" id="status" name="status">
													<option value="<?php echo $team->status;?>">--Select Status--</option>
													<option value="1" <?php if($team->status=='1'){echo "selected";}?>>Active</option>
													<option value="0" <?php if($team->status=='0'){echo "selected";}?>>Inactive</option>
												</select>
												</div>
											</div>
											<?php if($team_task_settings_enabled){ ?>
											<div class="col-md-6">
												<div class="checkbox checkbox-primary" style="margin-top:28px;">
													<input type="checkbox" id="show_all_team_tasks" name="show_all_team_tasks" value="1" <?php if(!empty($team->show_all_team_tasks)){echo "checked";}?>>
													<label for="show_all_team_tasks">Show all tasks of all team users</label>
												</div>
											</div>
											<div class="col-md-6">
												<div class="checkbox checkbox-primary" style="margin-top:28px;">
													<input type="checkbox" id="allow_task_assignment" name="allow_task_assignment" value="1" <?php if(!empty($team->allow_task_assignment)){echo "checked";}?>>
													<label for="allow_task_assignment">Allow this team leader to assign tasks from dashboard</label>
												</div>
											</div>
											<?php } ?>
                                            </div>
										<div class="col-md-9"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" class="btn btn-success" value="Update" id="teamupdate">
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
	   $("#teamupdate").attr('disabled',false);
	   $("#teamupdate").val('Update');
  $("#loginForm").on("submit", function(){
   // $("#pageloader").fadeIn();
   $("#teamupdate").attr('disabled',true);
     $("#teamupdate").val('Please Wait...');
  });//submit
});//document ready
</script>

<script language="javascript" type="text/javascript">   
jQuery.noConflict();
$(document).ready(function() {
$("#teamupdate").click(function() {
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
var team_leader = $("#team_leader").val();
if(team_leader=='')
{
	
	$("#error_team_leader").html('Required!');
}
var team_name = $("#team_name").val();
if(team_name=='')
{
	
	$("#error_team_name").html('Required!');
}

var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}


if(business_loc=='' || department=='' || team_leader=='' || team_name=='' || status=='' )
{
	
	return false;
}

});
});
</script>    </body>
</html>
