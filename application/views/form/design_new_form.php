<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="<?php echo copyright; ?>">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title><?php echo sitetitle; ?> Create New Form</title>



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

		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">

		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">

        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

		<link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">

		<link href="<?php echo assets_url;?>plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">



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

<style>

table.manglesh thead th {

				background: <?php echo $LOGO->colorcode;?>;

				color:#fff;

				font-weight:bold;

			}

			

#pageloader

{

  background: rgba( 255, 255, 255, 0.8 );

  display: none;

  height: 100%;

  position: fixed;

  width: 100%;

  z-index: 9999;

}

#pageloader img

{

  left: 30%;

  margin-left: -10px;

  margin-top: -10px;

  position: absolute;

  top: 30%;

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

            <div class="container">



                <!-- Page-Title -->

                <div class="row">

                    <div class="col-sm-12">

                        <div class="page-title-box">

                            <div class="btn-group pull-right">

                              

                            </div>

                            <h4 class="page-title">Design New Form</h4>

                        </div>

                    </div>

                </div>

                <!-- end page title end breadcrumb -->





                <div class="row">

                    <div class="col-xs-12">

                        <div class="card-box">



                            <div class="row">

                                <div class="col-sm-12 col-xs-12 col-md-12">

								<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

								<form method="post" id="loginForm" action="<?php echo page_url;?>Form/create_new_form/" enctype="multipart/form-data">

								    <div id="pageloader">

                                    <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />

                                    </div>	

												

												<div class="col-md-3">

												<div class="form-group">

														 <label for="field-2" class="control-label">DEPARTMENT</label>

														 <span id="error_department" style="color:red;">*</span>

														 <select class="form-control" name="department" id="department">

														 <option value="">-- SELECT DEPARTMENT--</option>

														 <?php $query = $this->db->select('department_id,status,department')->from('departments')->where('status','1')->order_by('department','asc')->get();

													foreach($query->result() as $department){?>

													<option value="<?php echo $department->department_id;?>"><?php echo strtoupper($department->department);?></option>

													<?php }?>

														 </select>

														 

													</div>

													<div class="form-group">

														 <label for="field-2" class="control-label">FORM TITLE</label>

														 <span id="error_form_title" style="color:red;">*</span>

														 <input class="form-control" type="text" name="form_title" id="form_title" value="" style="text-transform: uppercase;" required>

														 

													</div>

													

													<div class="form-group">

														 <label for="field-2" class="control-label">FORM TYPE</label>

														 <span id="error_form_type" style="color:red;">*</span>

														<select class="form-control" name="form_type" id="form_type" required>

															<option value="1">Resolution</option>

															<option value="2">Solution/Resolution</option>

														</select>

														 

													</div>

													

													<script type="text/javascript">

											

													$("#department").change(function(){

													var department=$("#department").val();

													$.ajax({

													type:"post",

													url:"<?php echo page_url;?>Form/user_list",

													data:"department="+department,

													success:function(data){

													$("#user_id").html(data);

													}

													});

													

													

													});

											

												</script>

													<div class="form-group" style="margin-top:25px">

														<button type="button" class="btn btn-warning" name="add" id="addmore_btn1"><i class="fa fa-plus"></i> CLICK HERE TO ADD FORM FIELD </button></div>

												</div>

												

												

												

												<div class="col-md-3">

												<div class="form-group">

														 <label for="field-2" class="control-label">USER</label>

														 <span style="color:red" id="error_user_id">*</span>

														<select class="form-control" name="user_id" id="user_id" required>

														</select>

														 

													</div>

													

													<div class="form-group">

														 <label for="field-2" class="control-label">FORM CODE</label>

														 <span style="color:red" id="error_user_id">*</span>

														<input type="text" class="form-control" name="formcode" id="formcode" required onblur="checkalias(this.value)"; style="text-transform:uppercase">

														

													</div>

													

													<div class="form-group">

														 <label for="field-2" class="control-label">DASHBOARD TITLE</label>

														 <span id="error_dashboard_title" style="color:red;">*</span>

														 <input class="form-control" type="text" name="dashboard_title" id="dashboard_title" value="" style="text-transform: uppercase;">

														 

													</div>

														<div class="form-group">

														 <label for="field-2" class="control-label">ACTION TO BE TAKEN</label>

														 <span id="error_action_to_be_taken" style="color:red;">*</span>

														 <input class="form-control" type="text" name="action_to_be_taken" id="action_to_be_taken" value="" style="text-transform: uppercase;" required>

														 

													</div>

													

												

													

												</div>

												<div class="col-md-4">

												<div class="form-group">

														 <label for="field-2" class="control-label">DASHBOARD SHOW PERMISSION</label>

														 

														<select multiple class="select3" name="dashboard_shown_to[]" id="dashboard_shown_to">

														<option value=""></option>

														<?php 

															$query = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('user_status','1')->where('hide_profile','0')->get();

															foreach($query->result() as $row){

														?>

														<option value="<?php echo $row->user_id;?>"><?php echo strtoupper($row->first_name." ".$row->last_name);?></option>

															<?php }?>

														</select>

														 

													</div>

													<div class="form-group">

														 <label for="field-2" class="control-label">OBJECTIVE</label>

														<span id="error_description" style="color:red;">*</span>

														<textarea class="form-control" name="description" id="description" style="text-transform: uppercase;"></textarea>

														 

													</div>

													

														<div class="form-group">

														 <label for="field-2" class="control-label">Ref No Applicable</label>

														<span id="error_ref_applicable" style="color:red;">*</span>

													<select class="form-control" name="ref_applicable" id="ref_applicable">

													    <option value="0">No</option>

													    <option value="1">Yes</option>

													</select>

														 

													</div>

												</div>

												

													<div class="col-md-2">

												   <div class="form-group">

												   <label>TAT Appl?</label><br/>

											<input type="checkbox" name="tatappl" value="1" checked> 

												   </div> 

												    

												    

												</div>

												

												

												<div id="dynamictasks1"></div>

											

											<div class="col-md-9"></div>

										<div class="col-md-3">

											<div class="form-group pull-right" style="padding-top:24px;">

												<label>&nbsp;</label>

												<input type="submit" id="save" class="btn btn-success" value="Update">

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

		<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>

		<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>



        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>



<script type="text/javascript">

$(document).ready(function(){

 var i=1;

 $('#addmore_btn1').click(function(){

 i++;

 

 $('#dynamictasks1').append('<div id="row'+i+'" class="row"><div class="col-md-12"><div class="col-md-6"><div class="form-group"><label for="field-2" class="control-label">FIELD TITLE<span style="color:red;"></span></label><span id="" style="color:red;"></span><input type="text" style="text-transform: uppercase;" class="form-control" name="input_type[]" id="input_type" placeholder="FIELD LABEL" value="" required></div></div><div class="col-md-5"><div class="form-group"><label for="field-2" class="control-label">FIELD TYPE<span style="color:red;">*</span></label><span id="error_user_name" style="color:red;"></span><select class="form-control" name="fieldtype[]" id="fieldtype" required><option value="1">INPUT TYPE</option><option value="2">DESCRIPTION BOX</option><option value="3">SELECT BOX</option><option value="4">CHECKBOX BOX</option><option value="5">FILE UPLOAD</option><option value="6">DATE PICKER</option><option value="7">TIME PICKER</option><option value="8">NUMBER</option><option value="9">RADIO BUTTON</option></select></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:30px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div></div><br/>');

 

 });

 

 

 $(document).on('click', '.btn_remove', function(){

 var button_id = $(this).attr("id");

 $('#row'+button_id+'').remove();

 });

 

});

	  </script>

<script language="javascript" type="text/javascript">   

$(document).ready(function() {

$("#save").click(function() {

var department = $("#department").val();

if(department=='')

{

	$("#error_department").html('Required!');

	

}

var user_id = $("#user_id").val();

if(user_id=='')

{

	$("#error_user_id").html('Required!');

	

}



var form_title = $("#form_title").val();

if(form_title=='')

{

	$("#error_form_title").html('Required!');

	

}

var dashboard_title = $("#dashboard_title").val();

if(dashboard_title=='')

{

	$("#error_dashboard_title").html('Required!');

	

}

var description = $("#description").val();

if(description=='')

{

	$("#error_description").html('Required!');

	

}





if(department=='' || user_id=='' || form_title=='' || description=='' || dashboard_title==''){

	return false;

}



});

});









function checkalias(vall)

{





	$.ajax({

	type:"post",

	url:"<?php echo page_url;?>Form/checkalias",

	data:"alias="+vall,

	success:function(data){

		if(data.trim()>0)

		{

			

			alert('Alias already in use');

			$("#formcode").val('');

			return false;

			

		}

	}

	});





}	



	



</script>

<script>

$(document).ready(function(){

	$('.select2').select2({ });

$('.select3').select2({ });

$('.select4').select2({ });

  $("#loginForm").on("submit", function(){

    $("#pageloader").fadeIn();

  });//submit

});//document ready

</script>

<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>



<script> $(document).ready(function() {

				$("#datepicker1").datepicker();

                $("#datepicker1btn").click(function(event) {

                    event.preventDefault();

                    $("#datepicker1").focus();

                })



            });</script>

			</body>

</html>