<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title><?php echo sitetitle; ?></title>
        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
	    <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
		<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" rel="stylesheet"/>
        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script>
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
	<style>
	input.largerCheckbox { 
            transform : scale(2); 
        } 
	</style>	
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
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						<h4 class="page-title text-center">MINUTES OF MEETING INTERNAL</h4>
						<span style="color:red;"><?php echo $this->session->flashdata('message');?></span>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">
						<form id="loginForm" method="post" action="<?php echo page_url;?>Mom" enctype="multipart/form-data" onsubmit="return validation();">
						
                             <div class="row">
                               <?php 
								$first_name =$this->session->userdata['logged_in']['user_name'];	
								$last_name =$this->session->userdata['logged_in']['last_name'];
								?> 
								<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label>Agenda of Meeting</label>
										<?php echo form_error('agenda'); ?>
										<input type="text" class="form-control" name="agenda" value="" >
									</div>
								</div>
											 <div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Employee Name</label>
														 <span id="error_employee_name" style="color:red;">*</span>
														 <input type="text" class="form-control" name="employee_name" id="employee_name" value="<?php echo $first_name." ".$last_name;?>" readonly>
													</div>
												</div>
												
												 <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Date</label>
														<span id="error_material_out_date" style="color:red;">*</span>
                                                        <input type="text" id="date" name="date" class="form-control" autocomplete="off" value="<?php echo date('d-m-Y');?>" readonly>
                                                    </div>
                                                </div>
												 <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Time</label>
														<span id="error_material_out_date" style="color:red;">*</span>
                                                        <input type="text" id="time" name="time" class="form-control" autocomplete="off" value="<?php echo date('h:i A');?>" readonly>
                                                    </div>
                                                </div>
												 <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Participant</label>
														<?php echo form_error('participant'); ?>
														<span id="error_participant" style="color:red;">*</span>
                                                       <select multiple class="select2" name="participant[]" id="participant" required>
													   <?php $q = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('user_status','1')->where('hide_profile','0')->where('business_location',2)->get();
													   foreach($q->result() as $row){?>
													   <option value="<?php echo $row->user_id;?>"><?php echo $row->first_name." ".$row->last_name;?></option>
													   <?php }?>
													   </select>
                                                    </div>
                                                </div>
												</div>
												<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label>Description of work</label>
														<input type="text" class="form-control" name="description[]" id="description" value="" required>
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														<label>Due Date</label>
														<input type="date" class="form-control" name="due_date[]" id="due_date" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Department </label>
														 <span id="error_department" style="color:red;">*</span>
														 <select class="form-control" id="department0" name="department[]" onchange="getusers(0);" required>
													<option value="">--SELECT DEPARTMENT--</option>
													<?php $query = $this->db->select('department_id,status,department')->from('departments')->where('status','1')->where('business_loc_id',2)->get();
													foreach($query->result() as $department){?>
													<option value="<?php echo $department->department_id;?>"><?php echo strtoupper($department->department);?></option>
													<?php }?>
												</select>
													</div>
												</div>
												<script type="text/javascript">
											
													function getusers(i){
													var department=$("#department"+i).val();
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Mom/user_list",
													data:"department="+department,
													success:function(data){
													$("#username"+i).html(data);
													}
													});
													}
												</script>
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Assign To</label>
														 <span id="error_delegate_to" style="color:red;">*</span>
														 <select class="form-control select3" id="username0" name="username[]" required>
													<option value="">--CHOOSE--</option>
													
												</select>
													</div>
												</div>
												<div class="col-md-1">
														<div class="form-group">
															<label>Delegate</label><br><br>
															<input type="checkbox" name="delegatetask[]" class="largerCheckbox" value="1">
														</div>
												</div>
												
												</div>
												
												<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														
														<input type="text" class="form-control" name="description[]" id="description" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														
														<input type="date" class="form-control" name="due_date[]" id="due_date" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														
														 <select class="form-control" id="department1" name="department[]" onchange="getusers(1);">
													<option value="">--SELECT DEPARTMENT--</option>
													<?php $query = $this->db->select('department_id,status,department')->from('departments')->where('status','1')->where('business_loc_id',2)->get();
													foreach($query->result() as $department){?>
													<option value="<?php echo $department->department_id;?>"><?php echo strtoupper($department->department);?></option>
													<?php }?>
												</select>
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 
														 <select class="form-control select3" id="username1" name="username[]">
													<option value="">--CHOOSE--</option>
													
												</select>
													</div>
												</div>
												<div class="col-md-1">
														<div class="form-group">
															<br>
															<input type="checkbox" name="delegatetask[]" class="largerCheckbox" value="1">
														</div>
												</div>
												
												</div>
												
												<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														
														<input type="text" class="form-control" name="description[]" id="description" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														
														<input type="date" class="form-control" name="due_date[]" id="due_date" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														
														 <select class="form-control" id="department2" name="department[]" onchange="getusers(2);">
													<option value="">--SELECT DEPARTMENT--</option>
													<?php $query = $this->db->select('department_id,status,department')->from('departments')->where('status','1')->where('business_loc_id',2)->get();
													foreach($query->result() as $department){?>
													<option value="<?php echo $department->department_id;?>"><?php echo strtoupper($department->department);?></option>
													<?php }?>
												</select>
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 
														 <select class="form-control select3" id="username2" name="username[]">
													<option value="">--CHOOSE--</option>
													
												</select>
													</div>
												</div>
												<div class="col-md-1">
														<div class="form-group">
															<br>
															<input type="checkbox" name="delegatetask[]" class="largerCheckbox" value="1">
														</div>
												</div>
												
												</div>
												
												<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														
														<input type="text" class="form-control" name="description[]" id="description" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														
														<input type="date" class="form-control" name="due_date[]" id="due_date" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														
														
														 <select class="form-control" id="department3" name="department[]" onchange="getusers(3);">
													<option value="">--SELECT DEPARTMENT--</option>
													<?php $query = $this->db->select('department_id,status,department')->from('departments')->where('status','1')->where('business_loc_id',2)->get();
													foreach($query->result() as $department){?>
													<option value="<?php echo $department->department_id;?>"><?php echo strtoupper($department->department);?></option>
													<?php }?>
												</select>
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 
														 <select class="form-control select3" id="username3" name="username[]">
													<option value="">--CHOOSE--</option>
													
												</select>
													</div>
												</div>
												<div class="col-md-1">
														<div class="form-group">
															<br>
															<input type="checkbox" name="delegatetask[]" class="largerCheckbox" value="1">
														</div>
												</div>
												
												</div>
												
												<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														
														<input type="text" class="form-control" name="description[]" id="description" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														
														<input type="date" class="form-control" name="due_date[]" id="due_date" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														 
														 <select class="form-control" id="department4" name="department[]" onchange="getusers(4);">
													<option value="">--SELECT DEPARTMENT--</option>
													<?php $query = $this->db->select('department_id,status,department')->from('departments')->where('status','1')->where('business_loc_id',2)->get();
													foreach($query->result() as $department){?>
													<option value="<?php echo $department->department_id;?>"><?php echo strtoupper($department->department);?></option>
													<?php }?>
												</select>
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														
														 <select class="form-control select3" id="username4" name="username[]">
													<option value="">--CHOOSE--</option>
													
												</select>
													</div>
												</div>
												<div class="col-md-1">
														<div class="form-group">
															<br>
															<input type="checkbox" name="delegatetask[]" class="largerCheckbox" value="1">
														</div>
												</div>
												
												</div>
												
												<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														
														<input type="text" class="form-control" name="description[]" id="description" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														
														<input type="date" class="form-control" name="due_date[]" id="due_date" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														 
														
														 <select class="form-control" id="department5" name="department[]" onchange="getusers(5);">
													<option value="">--SELECT DEPARTMENT--</option>
													<?php $query = $this->db->select('department_id,status,department')->from('departments')->where('status','1')->where('business_loc_id',2)->get();
													foreach($query->result() as $department){?>
													<option value="<?php echo $department->department_id;?>"><?php echo strtoupper($department->department);?></option>
													<?php }?>
												</select>
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														
													
														 <select class="form-control select3" id="username5" name="username[]">
													<option value="">--CHOOSE--</option>
													
												</select>
													</div>
												</div>
												<div class="col-md-1">
														<div class="form-group">
															<br>
															<input type="checkbox" name="delegatetask[]" class="largerCheckbox" value="1">
														</div>
												</div>
												
												</div>
												
												<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														
														<input type="text" class="form-control" name="description[]" id="description" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														
														<input type="date" class="form-control" name="due_date[]" id="due_date" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														 <select class="form-control" id="department6" name="department[]" onchange="getusers(6);">
													<option value="">--SELECT DEPARTMENT--</option>
													<?php $query = $this->db->select('department_id,status,department')->from('departments')->where('status','1')->where('business_loc_id',2)->get();
													foreach($query->result() as $department){?>
													<option value="<?php echo $department->department_id;?>"><?php echo strtoupper($department->department);?></option>
													<?php }?>
												</select>
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 
														 <select class="form-control select3" id="username6" name="username[]">
													<option value="">--CHOOSE--</option>
													
												</select>
													</div>
												</div>
												<div class="col-md-1">
														<div class="form-group">
															<br>
															<input type="checkbox" name="delegatetask[]" class="largerCheckbox" value="1">
														</div>
												</div>
												
												</div>
												
												<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														
														<input type="text" class="form-control" name="description[]" id="description" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														
														<input type="date" class="form-control" name="due_date[]" id="due_date" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														 
														 <select class="form-control" id="department7" name="department[]" onchange="getusers(7);">
													<option value="">--SELECT DEPARTMENT--</option>
													<?php $query = $this->db->select('department_id,status,department')->from('departments')->where('status','1')->where('business_loc_id',2)->get();
													foreach($query->result() as $department){?>
													<option value="<?php echo $department->department_id;?>"><?php echo strtoupper($department->department);?></option>
													<?php }?>
												</select>
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 
														 <select class="form-control select3" id="username7" name="username[]">
													<option value="">--CHOOSE--</option>
													
												</select>
													</div>
												</div>
												<div class="col-md-1">
														<div class="form-group">
															<br>
															<input type="checkbox" name="delegatetask[]" class="largerCheckbox" value="1">
														</div>
												</div>
												
												</div>
												
												<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														
														<input type="text" class="form-control" name="description[]" id="description" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														
														<input type="date" class="form-control" name="due_date[]" id="due_date" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														 
														 <select class="form-control" id="department8" name="department[]" onchange="getusers(8);">
													<option value="">--SELECT DEPARTMENT--</option>
													<?php $query = $this->db->select('department_id,status,department')->from('departments')->where('status','1')->where('business_loc_id',2)->get();
													foreach($query->result() as $department){?>
													<option value="<?php echo $department->department_id;?>"><?php echo strtoupper($department->department);?></option>
													<?php }?>
												</select>
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 
														 <select class="form-control select3" id="username8" name="username[]">
													<option value="">--CHOOSE--</option>
													
												</select>
													</div>
												</div>
												<div class="col-md-1">
														<div class="form-group">
															<br>
															<input type="checkbox" name="delegatetask[]" class="largerCheckbox" value="1">
														</div>
												</div>
												
												</div>
												
												<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														
														<input type="text" class="form-control" name="description[]" id="description" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														<input type="date" class="form-control" name="due_date[]" id="due_date" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														 
														 <select class="form-control" id="department9" name="department[]" onchange="getusers(9);">
													<option value="">--SELECT DEPARTMENT--</option>
													<?php $query = $this->db->select('department_id,status,department')->from('departments')->where('status','1')->where('business_loc_id',2)->get();
													foreach($query->result() as $department){?>
													<option value="<?php echo $department->department_id;?>"><?php echo strtoupper($department->department);?></option>
													<?php }?>
												</select>
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 
														 <select class="form-control select3" id="username9" name="username[]">
													<option value="">--CHOOSE--</option>
													
												</select>
													</div>
												</div>
												<div class="col-md-1">
														<div class="form-group"><br>
															<input type="checkbox" name="delegatetask[]" class="largerCheckbox" value="1">
														</div>
												</div>
												<div class="col-md-1">
												<div class="form-group" style="padding-top:20px">
													<button type="button" class="btn btn-warning" name="add" id="addmore_btn1"><i class="fa fa-plus"></i></button>
												</div>
												
												</div>
												</div>
												
												<div id="dynamictasks1"></div>
											<div class="row">
											<hr>
											<div class="col-md-12">
											<div class="form-group pull-right">
												<input type="submit" id="save" class="btn btn-info" value="Submit">
											</div>												
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
		<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

<script language="javascript" type="text/javascript">   
$(document).ready(function() {
	$('.select2').select2({ });
$("#save").click(function() {
	var description= $("#description").val();
if(description=='')
{
	$("#error_description").html('Required!');
}

if(description=='')
{
	
	return false;
}

});
});
</script>

<script>
function validation(){
	var participant = $("#participant").val();
	if(participant==''){
		$("#error_participant").html('Required!');
		return false;
	}
}
</script>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
 var i=10;
 $('#addmore_btn1').click(function(){
 i++;
 
 $('#dynamictasks1').append('<div id="row'+i+'" class="row"><div class="col-md-4"><div class="form-group"><input type="text" class="form-control" name="description[]" id="description" value="" required></div></div><div class="col-md-2"><div class="form-group"><input type="date" class="form-control" name="due_date[]" id="due_date" value=""></div></div><div class="col-md-2"><div class="form-group"><select class="form-control" id="department'+i+'" name="department[]" onchange="getusers('+i+');" required><option value="">--SELECT DEPARTMENT--</option><?php $query = $this->db->select('department_id,status,department')->from('departments')->where('status','1')->where('business_loc_id',2)->get();foreach($query->result() as $department){?><option value="<?php echo $department->department_id;?>"><?php echo strtoupper($department->department);?></option><?php }?></select></div></div><div class="col-md-2"><div class="form-group"><select class="form-control select3" id="username'+i+'" name="username" required><option value="">--CHOOSE--</option></select></div></div><div class="col-md-1"><div class="form-group"><br><input type="checkbox" name="delegatetask[]" value="1" class="largerCheckbox"></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:20px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div><br/>');
 
 });
 
 
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id+'').remove();
 });
 
});
</script>

    </body>
</html>