<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?>Create Task</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script>
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
			<style>
				body.task-form-page {
					background: #f4f7fb;
					color: #1f2937;
				}
				.task-form-page .wrapper {
					padding-top: 22px;
				}
				.task-form-header {
					align-items: center;
					background: #ffffff;
					border: 1px solid #e5ebf2;
					border-radius: 8px;
					box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
					display: flex;
					gap: 18px;
					justify-content: space-between;
					margin-bottom: 16px;
					padding: 16px 18px;
				}
				.task-form-title h4 {
					color: #0f172a;
					font-size: 22px;
					font-weight: 700;
					line-height: 1.2;
					margin: 0 0 5px;
				}
				.task-form-title span {
					color: #64748b;
					display: block;
					font-size: 13px;
					font-weight: 600;
				}
				.task-form-actions {
					display: flex;
					flex-wrap: wrap;
					gap: 10px;
					justify-content: flex-end;
				}
				.task-form-actions .btn,
				.task-submit-actions .btn {
					border-radius: 6px;
					font-weight: 700;
				}
				.task-form-card {
					background: #ffffff;
					border: 1px solid #e5ebf2;
					border-radius: 8px;
					box-shadow: 0 10px 28px rgba(15, 23, 42, 0.07);
					padding: 20px;
				}
				.task-form-page .form-group label {
					color: #475569;
					font-size: 12px;
					font-weight: 800;
					letter-spacing: 0;
					margin-bottom: 6px;
					text-transform: uppercase;
				}
				.task-form-page .form-control,
				.task-form-page .select2-container .select2-selection--single,
				.task-form-page .select2-container .select2-selection--multiple {
					border: 1px solid #d8e0ea !important;
					border-radius: 6px !important;
					box-shadow: none;
					min-height: 38px;
				}
				.task-form-page .form-control:focus {
					border-color: #0f6c94 !important;
					box-shadow: 0 0 0 3px rgba(15, 108, 148, 0.12);
				}
				.task-section-title {
					align-items: center;
					background: #eef8fb;
					border: 1px solid #d7eef5;
					border-radius: 8px;
					color: #0f5f7f;
					display: flex;
					font-size: 15px;
					font-weight: 800;
					justify-content: space-between;
					margin: 12px 0 18px;
					padding: 12px 14px;
				}
				.task-help-text {
					background: #fff7ed;
					border: 1px solid #fed7aa;
					border-radius: 6px;
					color: #9a3412 !important;
					display: inline-block;
					font-size: 12px;
					font-weight: 700;
					margin-bottom: 8px;
					padding: 6px 8px;
				}
				.task-submit-actions {
					border-top: 1px solid #e5ebf2;
					margin-top: 18px;
					padding-top: 18px;
					text-align: right;
				}
				.task-field-error {
					color: #d14343 !important;
					font-size: 12px;
					font-weight: 700;
					margin-left: 4px;
				}
				.task-page-loader {
					align-items: center;
					background: rgba(248, 250, 252, 0.86);
					display: none;
					height: 100%;
					justify-content: center;
					left: 0;
					position: fixed;
					top: 0;
					width: 100%;
					z-index: 9999;
				}
				.task-loader-card {
					align-items: center;
					background: #ffffff;
					border: 1px solid #e2e8f0;
					border-radius: 8px;
					box-shadow: 0 18px 45px rgba(15, 23, 42, 0.18);
					display: flex;
					gap: 13px;
					min-width: 260px;
					padding: 16px 18px;
				}
				.task-loader-spinner {
					animation: taskSpin 0.8s linear infinite;
					border: 3px solid #dbeafe;
					border-top-color: #0f6c94;
					border-radius: 50%;
					height: 30px;
					width: 30px;
				}
				.task-loader-text {
					color: #0f172a;
					font-weight: 800;
				}
				@keyframes taskSpin {
					to { transform: rotate(360deg); }
				}
				@media (max-width: 767px) {
					.task-form-header {
						align-items: stretch;
						flex-direction: column;
					}
					.task-form-actions {
						justify-content: flex-start;
					}
				}
			</style>
    </head>


	    <body class="task-form-page">
		<div id="taskPageLoader" class="task-page-loader">
			<div class="task-loader-card">
				<div class="task-loader-spinner"></div>
				<div class="task-loader-text" id="taskLoaderText">Saving task...</div>
			</div>
		</div>


        <!-- Navigation Bar-->
        <header id="topnav">
          <?php $this->load->view('common/nav-menu');?>
        </header>
        <!-- End Navigation Bar-->


	        <div class="wrapper">
	            <div class="container-fluid">

                <!-- Page-Title -->
	                <div class="row">
	                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
	                        <div class="task-form-header">
								<div class="task-form-title">
									<h4>Create Task</h4>
									<span>Add a task, choose its hierarchy, and define department messages.</span>
								</div>
								<div class="task-form-actions">
									<a href="<?php echo page_url;?>Task/taskmanagement" class="btn btn-primary"><i class="fa fa-list"></i> Task Listing</a>
									<a href="<?php echo page_url;?>Task/graphStructure" class="btn btn-default"><i class="fa fa-sitemap"></i> Tree View</a>
								</div>
	                        </div>
	                    </div>
	                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

               <div class="row">
                    <div class="col-xs-12">
	                        <div class="card-box task-form-card">
	<form method="post" id="loginForm" action="<?php echo page_url;?>Task/add_task" onsubmit="return validation();">
                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Department</label>
															<span id="error_department" class="task-field-error">*</span>
														<select class="form-control" name="department" id="department">
															<option value="">Select Department</option>
															<?php 
																	$businessloc = 2;
																	$q = $this->db->select('department_id, department')->from('departments')->where('business_loc_id',2)->where('status',1)->get();
																	foreach($q->result() as $row){
															?>
															<option value="<?php echo $row->department_id;?>"><?php echo $row->department;?></option>
															<?php }?>
														</select>
												    </div>
                                                </div>

                                                <script type="text/javascript">

													$("#department").change(function(){
													var department=$("#department").val();
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Delegation/user_list_new",
													data:"department="+department,
													success:function(data){
														//alert(data);
													$("#responsibleperson").html(data);
													}
													});
													});

													</script>

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Responsible Person</label>
															<span id="error_responsibleperson" class="task-field-error">*</span>
														<select class="form-control" name="responsibleperson" id="responsibleperson">
															
														</select>
												    </div>
                                                </div>
                                                
												
												
												
												<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Task Name</label>
															<span id="error_taskname" class="task-field-error">*</span>
														<input type="text" class="form-control" name="taskname" id="taskname" value="">
                                                    </div>
                                                </div>
												
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Task Type</label>
															 <span id="error_task_type" class="task-field-error">*</span>
														<select class="form-control" name="task_type" id="task_type">
															<option value="1">Main Task</option>
															<option value="2">Sub Task</option>
														</select>
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">TAT (in Days)</label>
															 <span id="error_tat" class="task-field-error">*</span>
														<input type="number" class="form-control" name="tat" id="tat" value="">
													</div>
												</div>

												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">TAT Start From</label>
															 <span id="error_startfrom" class="task-field-error">*</span>
														<select class="form-control" name="startfrom" id="startfrom"> 
															<option value="0">Select Option</option>
															<?php 
																$q = $this->db->select('task_id, task_name')->from('task_management')->where('status',1)->where_not_in('task_id',$this->uri->segment(3))->get();
																if($q->num_rows()>0){
																	foreach($q->result() as $roww){
																
															?>
															<option value="<?php echo $roww->task_id;?>"><?php echo $roww->task_name;?></option>
															<?php }}?>
														</select>
													</div>
												</div>

												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Task Frequency</label>
															 <span id="error_taskfrequency" class="task-field-error">*</span>
														<select class="form-control" name="taskfrequency" id="taskfrequency"> 
															<option value="1">Onetime</option>
															<option value="2">Recurring Task</option>
														</select>
													</div>
												</div>

												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Sort Order</label>
															 <span id="error_sortorder" class="task-field-error">*</span>
														<input type="text" class="form-control" name="sortorder" id="sortorder">
													</div>
												</div>

												<div class="col-md-2">
													<div class="form-group">
														<label>Is it Final Step?</label>
														<input type="checkbox" name="isitfinalstep" value="1">
													</div>
												</div>

												<!-- <div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Status</label>
														 <span id="error_status" style="color:red;">*</span>
														<select class="form-control" name="status" id="status"> 
															<option value="1">Active</option>
															<option value="2">Inactive</option>
														</select>
													</div>
												</div> -->
												<div class="col-md-12">
														<div class="task-section-title"><span>Define Messages for Departments</span><i class="fa fa-comments-o"></i></div>
												</div>
										
												
											
												<div class="col-md-4">
													<div class="form-group">
														<label>Department</label> 
														<input type="hidden" name="row[]" value="0">
															<span class="task-field-error" id="error_messagefordepartment">*</span>
												<select class="form-control multipleselect" id="messagefordepartment" name="messagefordepartment0[]" multiple="">
													
													<?php
													$businessloc = 2;
													 $query = $this->db->select('department_id,status,department')->from('departments')->where('status','1')->where('business_loc_id',$businessloc)->get();
													foreach($query->result() as $department){?>
													<option value="<?php echo $department->department_id;?>"><?php echo strtoupper($department->department);?></option>
													<?php }?>
												</select>
													</div>
												</div>
												
												<div class="col-md-7">
														<span class="task-help-text">Use tags: df_number, department_name, department_hod</span><br>
													<label>Message</label>

														<span id="error_message" class="task-field-error">*</span>
													<div class="form-group">
														<textarea class="form-control ckeditor" name="definemessage[]" id="definemessage"></textarea>
													</div>
												</div>
											
												<div class="col-md-1">
												<div class="form-group" style="padding-top:70px">
													<button type="button" class="btn btn-warning" name="add" id="addmore_btn1"><i class="fa fa-plus"></i></button>
												</div>
												
												</div>
												
												
												<div id="dynamictasks1"></div>

											
													<div class="col-md-12">
														<div class="task-submit-actions">
														<button type="submit" class="btn btn-success" name="Save" id="savedata"><i class="fa fa-save"></i> Save Task</button>
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

$(document).ready(function() {
$("#savedata").click(function() {
var department = $("#department").val();
if(department=='')
{
	$("#department").css("border", "1px solid red");
	$("#error_department").html('Required!');
}
var taskname = $("#taskname").val();
if(taskname=='')
{
	$("#taskname").css("border", "1px solid red");	
	$("#error_taskname").html('Required!');
}
var task_type = $("#task_type").val();
if(task_type=='')
{
	$("#task_type").css("border", "1px solid red");	
	$("#error_task_type").html('Required!');
}
var tat = $("#tat").val();
if(tat=='')
{
	$("#tat").css("border", "1px solid red");	
	$("#error_tat").html('Required!');
}

var startfrom = $("#startfrom").val();
if(startfrom=='')
{
	$("#startfrom").css("border", "1px solid red");
	$("#error_startfrom").html('Required!');
}

var taskfrequency = $("#taskfrequency").val();
if(taskfrequency=='')
{
	$("#taskfrequency").css("border", "1px solid red");
	$("#error_taskfrequency").html('Required!');
}

var sortorder = $("#sortorder").val();
if(sortorder=='')
{
	$("#sortorder").css("border", "1px solid red");
	$("#error_sortorder").html('Required!');
}



if(department=='' || taskname=='' || task_type=='' || tat=='' || startfrom=='' || taskfrequency=='' ||  sortorder=='')
{
	
	return false;
}

});
});
</script>    
<script type="text/javascript">
$(document).ready(function(){
 var i=1;
 $('#addmore_btn1').click(function(){
 
 $('#dynamictasks1').append('<div id="row'+i+'"><div class="col-md-4"><input type="hidden" name="row[]" value="'+i+'"><div class="form-group"><label>Department</label> <span style="color: red;" id="error_messagefordepartment">*</span><select class="form-control" id="messagefordepartment'+i+'" name="messagefordepartment'+i+'[]" multiple><?php $businessloc = 2; $query = $this->db->select('department_id,status,department')->from('departments')->where('status','1')->where('business_loc_id',$businessloc)->get(); foreach($query->result() as $department){?><option value="<?php echo $department->department_id;?>"><?php echo strtoupper($department->department);?></option><?php }?></select></div></div><div class="col-md-7"><span style="color: red; background-color:#f1f1f1;">For DF No, use "df_number". For Department Name, use "department_name". For HOD, use "department_hod". </span><br><label>Message</label><span id="error_message">*</span><div class="form-group"><textarea class="form-control messagebox'+i+'" name="definemessage[]" id="definemessage'+i+'"></textarea></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:20px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div><br/>');
 	 
 	 intialzeselect2(i);
 	 CKEditorChange("definemessage"+i);
 i++;
 	

 });
 
 function intialzeselect2(i){
$('#messagefordepartment'+i).select2();
 }



 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id+'').remove();
 });
 
});

function CKEditorChange(name) {
      CKEDITOR.replace(name);
    }
	  </script>
	 
	  <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
		   <script type="text/javascript">
	            $( document ).ready(function() {
	                    $('.multipleselect').select2();
	            });
	        </script>
	<script type="text/javascript">
		function setTaskFieldState(selector, valid, messageSelector) {
			var field = jQuery(selector);
			if (valid) {
				field.css("border", "");
				if (messageSelector) {
					jQuery(messageSelector).html("*");
				}
				return;
			}
			field.css("border", "1px solid #d14343");
			if (messageSelector) {
				jQuery(messageSelector).html("Required!");
			}
		}

		function validation() {
			if (typeof CKEDITOR !== "undefined") {
				for (var instanceName in CKEDITOR.instances) {
					if (CKEDITOR.instances.hasOwnProperty(instanceName)) {
						CKEDITOR.instances[instanceName].updateElement();
					}
				}
			}

			var department = jQuery("#department").val();
			var responsibleperson = jQuery("#responsibleperson").val();
			var taskname = jQuery.trim(jQuery("#taskname").val());
			var taskType = jQuery("#task_type").val();
			var tat = jQuery("#tat").val();
			var startfrom = jQuery("#startfrom").val();
			var taskfrequency = jQuery("#taskfrequency").val();
			var sortorder = jQuery.trim(jQuery("#sortorder").val());

			setTaskFieldState("#department", department !== "", "#error_department");
			setTaskFieldState("#responsibleperson", responsibleperson !== "" && responsibleperson !== null, "#error_responsibleperson");
			setTaskFieldState("#taskname", taskname !== "", "#error_taskname");
			setTaskFieldState("#task_type", taskType !== "", "#error_task_type");
			setTaskFieldState("#tat", tat !== "", "#error_tat");
			setTaskFieldState("#startfrom", startfrom !== "", "#error_startfrom");
			setTaskFieldState("#taskfrequency", taskfrequency !== "", "#error_taskfrequency");
			setTaskFieldState("#sortorder", sortorder !== "", "#error_sortorder");

			return !(department === "" || responsibleperson === "" || responsibleperson === null || taskname === "" || taskType === "" || tat === "" || startfrom === "" || taskfrequency === "" || sortorder === "");
		}

		function showTaskFormLoader(message) {
			jQuery("#taskLoaderText").text(message || "Saving task...");
			jQuery("#taskPageLoader").fadeIn(120);
		}

		jQuery(function() {
			jQuery("#loginForm").off("submit.taskLoader").on("submit.taskLoader", function() {
				if (!validation()) {
					jQuery("#taskPageLoader").hide();
					return false;
				}
				jQuery("#savedata").prop("disabled", true).html("<i class='fa fa-spinner fa-spin'></i> Saving...");
				showTaskFormLoader("Saving task...");
				return true;
			});
		});
	</script>
	</body>
	</html>
