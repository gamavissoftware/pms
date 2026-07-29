<?php 
$business_location = $this->session->userdata['logged_in']['business_location'];
$team_task_settings_enabled = $this->db->field_exists('show_all_team_tasks', 'prestogroup_teams') && $this->db->field_exists('allow_task_assignment', 'prestogroup_teams');
?>
<!DOCTYPE html>

<html>
	<head>
    <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title><?php echo sitetitle; ?> Team Management</title>
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

		 <?PHP 

$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();

foreach($q->result() as $LOGO);

?>

		<style>table.manglesh thead th {

				background: <?php echo $LOGO->colorcode;?>;

				color:#fff;

				font-weight:bold;

				text-align:center;

			}
		table.manglesh tbody td {

				
				text-align:center;

			}
		</style>

    </head>
    <body>
        <!-- Navigation Bar-->

        <header id="topnav">
         <?php $this->load->view('common/nav-menu');?>
        </header>

        <!-- End Navigation Bar-->


        <?php $this->load->view('common/info-section.php');?>


        <div class="wrapper">

            <div class="container-fluid">
            	<!-- Page-Title -->

                <div class="row" style="margin-top:20px;">

                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">

                        <div class="page-title-box">

						 <div class="btn-group pull-right">

						  <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">Create New Team</button>
						</div>

                           

                            <h4 class="page-title text-center">

							<?php 
							$query = $this->db->select('a.business_loc_id, a.company_name')->from('business_location a')->where('a.business_loc_id',$business_location)->get();
							foreach($query->result() as $companyinfo){

								echo strtoupper($companyinfo->company_name); 

							}

							?>

							TEAMS</h4>

                        </div>

                    </div>

                </div>

                <!-- end page title end breadcrumb -->

				<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
				<div class="row">

                    <div class="col-sm-12">

                        <div class="card-box table-responsive">

                            <table id="example" class="table table-striped table-bordered manglesh">

                                <thead>

                                <tr>

                                    <th>Sr No.</th>
                                    <!-- <th>Company Name</th> -->
                                    <th>Department</th>
                                    <th>Team Leader</th>
                                    <th>Team Name</th>
                                     <th style="width: 300px;">Team Users</th>
                                    <?php if($team_task_settings_enabled){ ?>
                                    <th>Show All Team Tasks</th>
                                    <th>Assign Task Permission</th>
                                    <?php } ?>
                                    <th>Add/View Member in Team</th>
                                   
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

 <form id="loginForm" method="post" action="<?php echo page_url;?>Master/User_management/create_team/">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">Create New Team</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">

                                                <div class="col-md-6">

                                                    <div class="form-group">

                                                        <label>Business Location</label>

														<span id="error_business_loc" style="color:red;">*</span>

														<select class="form-control" id="business_loc" name="business_loc">

													<option value="">--Select Business Location--</option>

													<?php 

													$this->db->select('a.company_name, a.business_loc_id,a.state_id, a.city_id,b. 	state_id,b.state_name,c.city_id,c.city_name')->from('business_location a')->join('states b','a.state_id=b.state_id','left')->join('cities c','a.city_id=c.city_id','left')->where('business_loc_status','1')->where('a.business_loc_id',$business_location);

													$this->db->order_by('a.company_name','asc');

													$query = $this->db->get();

													$res = $query->result();

													foreach($res as $row){

													?>

													<option value="<?php echo $row->business_loc_id;?>" selected><?php echo $row->company_name;?>(<?php echo $row->state_name;?>, <?php echo $row->city_name;?>)</option>

											<?php }?>		

												</select>

													<script type="text/javascript">

													function initTeamSearchableSelects(){
														if(!$.fn.select2){
															return;
														}
														var modal = $("#con-close-modal");
														$("#department, #team_leader").each(function(){
															var selectBox = $(this);
															if(selectBox.data("select2")){
																selectBox.select2("destroy");
															}
															selectBox.select2({
																width: '100%',
																dropdownParent: modal
															});
														});
													}

													function refreshDepartmentOptions(optionsHtml){
														$("#department").html(optionsHtml);
														initTeamSearchableSelects();
													}

													function loadDepartmentOptions(){
														var business_loc=$("#business_loc").val();
														if(business_loc===''){
															refreshDepartmentOptions('<option value="">--Select Department--</option>');
															return;
														}
														$.ajax({
														type:"post",
														url:"<?php echo page_url;?>Master/User_management/select_department",
														data:"business_loc="+business_loc,
														success:function(data){
														refreshDepartmentOptions(data);
														refreshTeamLeaderOptions('<option value="">--Select Team Leader--</option>');
														}
														});
													}

													$("#business_loc").change(function(){
													loadDepartmentOptions();
													});

													$(document).ready(function(){
														if($("#business_loc").val() !== ''){
															loadDepartmentOptions();
														}
														initTeamSearchableSelects();
													});

													$('#con-close-modal').on('shown.bs.modal', function(){
														initTeamSearchableSelects();
													});
													</script>

												

                                                    </div>

                                                </div>

												<div class="col-md-6">

                                                    <div class="form-group">

                                                        <label>Department</label>

														<span id="error_department" style="color:red;">*</span>

														<select class="form-control" id="department" name="department">

													<option value="">--Select Department--</option>
													<?php
													$this->db->select('department_id, department')->from('departments')->where('business_loc_id',$business_location)->where('status','1');
													$this->db->order_by('department','asc');
													$department_query = $this->db->get();
													foreach($department_query->result() as $department_row){
													?>
													<option value="<?php echo (int) $department_row->department_id;?>"><?php echo $department_row->department;?></option>
													<?php }?>

												</select>

													<script type="text/javascript">

														function refreshTeamLeaderOptions(optionsHtml){
															$("#team_leader").html(optionsHtml);
															initTeamSearchableSelects();
														}

														$("#department").change(function(){

														var department=parseInt($("#department").val(), 10) || 0;
														var business_loc=$("#business_loc").val();
														if(department===0){
															refreshTeamLeaderOptions('<option value="">--Select Team Leader--</option>');
															return;
														}

														$.ajax({

														type:"post",

														url:"<?php echo page_url;?>Master/User_management/select_team_member",

														data:{
															business_loc: business_loc,
															department: department
														},

														success:function(data){

														refreshTeamLeaderOptions(data);

														}

														});

														});

														$("#business_loc").change(function(){
														refreshTeamLeaderOptions('<option value="">--Select Team Leader--</option>');
														});

													</script>

                                                    </div>

                                                </div>

												

												<div class="col-md-6">

                                                    <div class="form-group">

                                                        <label>Team Leader</label>

														<span id="error_team_leader" style="color:red;">*</span>

														<select class="form-control" id="team_leader" name="team_leader">

													<option value="">--Select Team Leader--</option>

													

												</select>

												

                                                    </div>

                                                </div>
												<div class="col-md-6">

													<div class="form-group">

														 <label for="field-2" class="control-label">Team Name</label>

														 <span id="error_team_name" style="color:red;"></span>

														 <input type="text" class="form-control" name="team_name" id="team_name"  value="">

													</div>

												</div>

												

												<div class="col-md-6" style="display:none">

													<div class="form-group">

														 <label for="field-2" class="control-label">By Pass Conveyance Approval</label>

														 <span id="error_by_pass" style="color:red;"></span>

														 <select class="form-control" name="by_pass" id="by_pass">

															<option value="1">Yes</option>

															<option value="0">No</option>

														 </select>

													</div>

												</div>

												

												<div class="col-md-6">

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
											<?php if($team_task_settings_enabled){ ?>
											<div class="col-md-6">
												<div class="checkbox checkbox-primary" style="margin-top:28px;">
													<input type="checkbox" id="show_all_team_tasks" name="show_all_team_tasks" value="1">
													<label for="show_all_team_tasks">Show all tasks of all team users</label>
												</div>
											</div>
											<div class="col-md-6">
												<div class="checkbox checkbox-primary" style="margin-top:28px;">
													<input type="checkbox" id="allow_task_assignment" name="allow_task_assignment" value="1">
													<label for="allow_task_assignment">Allow this team leader to assign tasks from dashboard</label>
												</div>
											</div>
											<?php } ?>

                                            </div>

											

											

                                        </div>

                                        <div class="modal-footer">

                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

                                            <input type="submit" id="teamsave" class="btn btn-info" value="Submit"> 

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
		$(document).ready(function() {
			if($.fn.select2){
				$('.select2').select2({ });
			}
		});
		</script>
		 <script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
		 

<script>

$(document).ready(function(){

	   $("#teamsave").attr('disabled',false);

	   $("#teamsave").val('submit');

  $("#loginForm").on("submit", function(){

   // $("#pageloader").fadeIn();

   $("#teamsave").attr('disabled',true);

     $("#teamsave").val('Please Wait...');

  });//submit

});//document ready

</script>

       



        <script>

$( document ).ready(function() {

$('#example').dataTable({

"bProcessing": true,

	fixedHeader: true,

"pagination":true,
dom: 'lBfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Data export'
            }
        ],


"sAjaxSource": "<?php echo page_url;?>Master/User_management/all_created_team/",

"aoColumns": [

				{ mData: 'sr_no' } ,
				// { mData: 'company_name' },
				{ mData: 'department' },
				{ mData: 'team_leader' },
				{ mData: 'team' },
				{ mData: 'members' },
				<?php if($team_task_settings_enabled){ ?>
				{ mData: 'show_all_team_tasks' },
				{ mData: 'allow_task_assignment' },
				<?php } ?>
				{ mData: 'add_team' },
				
				{ mData: 'status' },
				{ mData: 'edit' }

	]

});   

});



</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script language="javascript" type="text/javascript">   
jQuery.noConflict();
$(document).ready(function() {
$("#teamsave").click(function() {
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

</script>

    </body>

</html>
