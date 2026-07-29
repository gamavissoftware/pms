<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

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
						<h4 class="page-title text-center">MINUTES OF MEETING EXTERNAL</h4>
						<span style="color:red;"><?php echo $this->session->flashdata('message');?></span>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">
						<form id="loginForm" method="post" action="<?php echo page_url;?>Mom/form" enctype="multipart/form-data" onsubmit="return validation();">
						
                             <div class="row">
                               <?php 
								$first_name =$this->session->userdata['logged_in']['user_name'];	
								$last_name =$this->session->userdata['logged_in']['last_name'];
								?> 
								<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label>Agenda of Meeting <span style="color:red;">*</span></label>
										<?php echo form_error('agenda'); ?>
										<input type="text" class="form-control" name="agenda" value="" required>
									</div>
								</div>
								
								<div class="col-md-4">
									<div class="form-group">
										<label>Name of the Company <span style="color:red;">*</span></label>
										<?php echo form_error('agenda'); ?>
										<input type="text" class="form-control" name="company_name" value="" required>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Participant of Customer end <span style="color:red;">*</span></label>
										<?php echo form_error('company_participants'); ?>
										<input type="text" class="form-control" name="company_participants" value="" required>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Add Emails with Comma(,) <span style="color:red;">*</span></label>
										<?php echo form_error('emails'); ?>
										<input type="text" class="form-control" name="emails" value="" required>
									</div>
								</div>

											 <div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Prepared By</label>
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
                                                        <label for="field-1" class="control-label">Participant of our Team</label>
														<?php echo form_error('participant'); ?>
														<span id="error_participant" style="color:red;">*</span>
                                                       <select multiple class="select2" name="participant[]" id="participant" required>
													   <?php $q = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('user_status','1')->where('hide_profile','0')->get();
													   foreach($q->result() as $row){?>
													   <option value="<?php echo $row->user_id;?>"><?php echo $row->first_name." ".$row->last_name;?></option>
													   <?php }?>
													   </select>
                                                    </div>
                                                </div>
												</div>
												<div class="row">
												<div class="col-md-5">
													<div class="form-group">
														<label>Points discussed</label>
														<input type="text" class="form-control" name="description[]" id="description" value="" >
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														<label>Target Date</label>
														<input type="date" class="form-control" name="due_date[]" id="due_date" value="">
													</div>
												</div>
												
											<div class="col-md-5">
												<div class="form-group">
													<label>Dependibilty/Responsibility (Person/Company)</label>
													<input type="text" class="form-control" name="responsibility[]">
												</div>
											</div>
												</div>
												
												<div class="row">
												<div class="col-md-5">
													<div class="form-group">
														
														<input type="text" class="form-control" name="description[]" id="description" value="" >
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														
														<input type="date" class="form-control" name="due_date[]" id="due_date" value="">
													</div>
												</div>
												
											<div class="col-md-5">
												<div class="form-group">
													
													<input type="text" class="form-control" name="responsibility[]">
												</div>
											</div>
												</div>
												
												<div class="row">
												<div class="col-md-5">
													<div class="form-group">
														
														<input type="text" class="form-control" name="description[]" id="description" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														
														<input type="date" class="form-control" name="due_date[]" id="due_date" value="">
													</div>
												</div>
												
											<div class="col-md-5">
												<div class="form-group">
													
													<input type="text" class="form-control" name="responsibility[]">
												</div>
											</div>
												</div>
												
												<div class="row">
												<div class="col-md-5">
													<div class="form-group">
														
														<input type="text" class="form-control" name="description[]" id="description" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														
														<input type="date" class="form-control" name="due_date[]" id="due_date" value="">
													</div>
												</div>
												
											<div class="col-md-5">
												<div class="form-group">
													
													<input type="text" class="form-control" name="responsibility[]">
												</div>
											</div>
												</div>
												
												<div class="row">
												<div class="col-md-5">
													<div class="form-group">
														
														<input type="text" class="form-control" name="description[]" id="description" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														
														<input type="date" class="form-control" name="due_date[]" id="due_date" value="">
													</div>
												</div>
												
											<div class="col-md-5">
												<div class="form-group">
													
													<input type="text" class="form-control" name="responsibility[]">
												</div>
											</div>
												</div>
												
												<div class="row">
												<div class="col-md-5">
													<div class="form-group">
														
														<input type="text" class="form-control" name="description[]" id="description" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														
														<input type="date" class="form-control" name="due_date[]" id="due_date" value="">
													</div>
												</div>
												
											<div class="col-md-5">
												<div class="form-group">
													
													<input type="text" class="form-control" name="responsibility[]">
												</div>
											</div>
												</div>
												
												<div class="row">
												<div class="col-md-5">
													<div class="form-group">
														
														<input type="text" class="form-control" name="description[]" id="description" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														
														<input type="date" class="form-control" name="due_date[]" id="due_date" value="">
													</div>
												</div>
												
											<div class="col-md-5">
												<div class="form-group">
													
													<input type="text" class="form-control" name="responsibility[]">
												</div>
											</div>
												</div>
												
												<div class="row">
												<div class="col-md-5">
													<div class="form-group">
														
														<input type="text" class="form-control" name="description[]" id="description" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														
														<input type="date" class="form-control" name="due_date[]" id="due_date" value="">
													</div>
												</div>
												
											<div class="col-md-5">
												<div class="form-group">
													
													<input type="text" class="form-control" name="responsibility[]">
												</div>
											</div>
												</div>
												
												<div class="row">
												<div class="col-md-5">
													<div class="form-group">
														
														<input type="text" class="form-control" name="description[]" id="description" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														
														<input type="date" class="form-control" name="due_date[]" id="due_date" value="">
													</div>
												</div>
												
											<div class="col-md-5">
												<div class="form-group">
													
													<input type="text" class="form-control" name="responsibility[]">
												</div>
											</div>
												</div>
												
												<div class="row">
												<div class="col-md-5">
													<div class="form-group">
														
														<input type="text" class="form-control" name="description[]" id="description" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														
														<input type="date" class="form-control" name="due_date[]" id="due_date" value="">
													</div>
												</div>
												
											<div class="col-md-4">
												<div class="form-group">
													
													<input type="text" class="form-control" name="responsibility[]">
												</div>
											</div>
											<div class="col-md-1">
												<div class="form-group">
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
 var i=1;
 $('#addmore_btn1').click(function(){
 i++;
 
 $('#dynamictasks1').append('<div id="row'+i+'" class="row"><div class="col-md-5"><div class="form-group"><input type="text" class="form-control" name="description[]" id="description" value=""></div></div><div class="col-md-2"><div class="form-group"><input type="date" class="form-control" name="due_date[]" id="due_date" value=""></div></div><div class="col-md-4"><div class="form-group"><input type="text" class="form-control" name="responsibility[]"></div></div><div class="col-md-1"><div class="form-group pull-left" ><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div><br/>');
 
 });
 
 
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id+'').remove();
 });
 
});
	  </script>

    </body>
</html>