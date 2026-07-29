<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title><?php echo sitetitle; ?> Create IOM</title>
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
						<h4 class="page-title text-center">DF MEETING NOTIFICATION <a href="<?php echo page_url;?>Form/dfmeetingnotificationlist"><span class="btn btn-danger btn-xs pull-right">DF Meeting List</span></a></h4>
							
                        </div>
                        <div class="col-md-12">
                        	<?php echo $this->session->flashdata('message');?>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">
						<form id="loginForm" method="post" action="<?php echo page_url;?>Form/feeddfmeetingnotification" enctype="multipart/form-data" onsubmit="return validation();">
						
                             <div class="row">
                               <?php 
								$first_name =$this->session->userdata['logged_in']['user_name'];	
								$last_name =$this->session->userdata['logged_in']['last_name'];
								?> 
								<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label>Agenda of Meeting <span style="color:red;" id="error_agenda">*</span></label>
										<?php echo form_error('agenda'); ?>
										<input type="text" class="form-control" name="agenda" id="agenda" value="" required>
									</div>
								</div>
								
											 <div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">Form Filled By</label>
														 <span id="error_employee_name" style="color:red;">*</span>
														 <input type="text" class="form-control" name="employee_name" id="employee_name" value="<?php echo $first_name;?> <?php echo $last_name;?>" readonly>
													</div>
												</div>
												
												 <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Date</label>
														<span id="error_material_out_date" style="color:red;">*</span>
                                                        <input type="date" id="date" name="date" class="form-control" autocomplete="off" value="<?php echo date('d-m-Y');?>" required>
                                                    </div>
                                                </div>
												 <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Time</label>
														<span id="error_material_out_date" style="color:red;">*</span>
                                                        <input type="time" id="time" name="time" class="form-control" autocomplete="off" value="<?php echo date('h:i A');?>" required>
                                                    </div>
                                                </div>
												 <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Participant</label>
														<?php echo form_error('participant'); ?>
														<span id="error_participant" style="color:red;">*</span>
                                                       <select multiple class="select2" name="participant[]" id="participant" required>
													   <?php $q = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('user_status','1')->where('hide_profile','0')->where('business_location',2)->get();
													   foreach($q->result() as $row){?>
													   <option value="<?php echo $row->user_id;?>"><?php echo ucwords(strtolower($row->first_name." ".$row->last_name));?></option>
													   <?php }?>
													   </select>
                                                    </div>
                                                </div>
												</div>

											
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
		
	}
	var agenda = $("#agenda").val();
	if(agenda==''){
		$("#error_agenda").html('Required!');
		
	}
	

	if(participant=='' || agenda==''){
		return false;
	}
}
</script>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>


    </body>
</html>