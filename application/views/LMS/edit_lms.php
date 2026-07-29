<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Edit LMS Material Form</title>

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
				background:<?php echo $LOGO->colorcode;?>;
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


        <div class="wrapper">
            <div class="container">

                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="btn-group pull-right">

                            </div>
                            <h4 class="page-title">Edit LMS Material Form</h4>
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
								<form method="post" id="loginForm" action="<?php echo page_url;?>LMS/update_edit/" enctype="multipart/form-data">
								    <div id="pageloader">
                                    <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
                                    </div>

												<div class="col-md-12">
												<div class="col-md-3">
												<div class="form-group">
														 <label for="field-2" class="control-label">DEPARTMENT</label>
														 <span id="error_department" style="color:red;">*</span>
														 <select class="form-control" name="department" id="department" readonly>
														 <option value="">-- SELECT DEPARTMENT--</option>
														 <?php $query = $this->db->select('department_id,status,department')->from('departments')->where('status','1')->order_by('department','asc')->get();
														 $depid=$this->uri->segment(3);
														 
													foreach($query->result() as $department){?>
													<option value="<?php echo $department->department_id;?>"<?php if($department->department_id==$depid){echo "SELECTED";}?> ><?php echo strtoupper($department->department);?></option>
													<?php }?>
														 </select>

													</div>
													</div>
													</div>
													<?php
														$id=$this->uri->segment(4);
													$cha=$this->db->select('*')->from('lms_study')->where('lms_id',$id)->get();
														if($cha->num_rows() >0)
														{
															foreach($cha->result() as $query);
														}else
														{
																
														}
													?>
													<hr style="clear:both; color:#000; border:1px solid;">
													<div class="row daysrow" id="day1">
													<input type="hidden" name="uid" value="<?php echo $query->lms_id; ?>">
													<input type="hidden" name="old_file" value="<?php echo $query->pdf_file; ?>">
													<div class="col-md-12">
													<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Day</label>
														 <span id="error_form_title" style="color:red;">*</span>
														 <input	 class="form-control" type="text" name="daytest" id="daytest11[]" value="<?php echo $query->s_day; ?>" style="text-transform: uppercase;" required>

													</div>
													</div>
													<!--<div class="col-md-3">
													<div class="form-group" style="margin-top:25px;" >
														<button type="button" class="btn btn-info btn-xs" name="add" id="addmore_btn1" onclick="additem(1);"><i class="fa fa-plus"></i> </button></div>
													</div>-->
													</div>
													</div>
													<div class="row subitemrow1" id="lms11">
													<div class="col-md-12">
													<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">video Name</label>
														 <span style="color:red" id="error_user_id">*</span>
														<input type="text" class="form-control" name="videoname" id="videoname" value="<?php echo $query->video_name; ?>" required>
														</select>

													</div>
													</div>
													<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">video Link</label>
														 <span style="color:red" id="error_user_id">*</span>
														<input type="text" class="form-control" name="videolink" id="videolink" value="<?php echo $query->video_link;?>" required  >

													</div>
													</div>
													<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">PDF NAME</label>
														 <span id="error_dashboard_title" style="color:red;">*</span>
														 <input class="form-control" type="text" name="pdf_name" id="pdf_name" value="<?php echo $query->pdf_name;?>" style="text-transform: uppercase;">

													</div>
													</div>
													<div class="col-md-2">
														<div class="form-group">
														 <label for="field-2" class="control-label">PDF File</label>
														 <span id="error_action_to_be_taken" style="color:red;">*</span>
														 <input class="form-control" type="file" name="pdf_file" id="pdf_file" value="<?php echo $query->pdf_file; ?>" >

													</div>
													<span><a href="<?php echo image_url; ?>/lmsstudy/<?php echo $query->pdf_file; ?>">Download</a></span>
													</div>
													<!--<div class="col-md-2">
														<div class="form-group" style="margin-top:25px;" >
														<button type="button" class="btn btn-warning btn-xs" name="add" id="addmore_btn2" onclick="addmoresubitem('1');"><i class="fa fa-plus"></i> </button></div>
													</div>-->
													</div>
													</div>

												<div id="dynamictasksday1"></div>
													
												<div id="dynamictasksaddparent"></div>
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



function addmoresubitem(daysid)
{
	var numItems = $('.subitemrow'+daysid).length;
	
	var rid=parseFloat(numItems)+parseFloat(1);

	 
	 $('#dynamictasksday'+daysid).append('<div class="subitemrow'+daysid+'" id="lms'+daysid+rid+'"><div class="col-md-12"><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">video Name</label><span style="color:red" id="error_user_id">*</span><input type="text" class="form-control" name="videoname'+daysid+'[]" id="videoname" required></select></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">video Link</label><span style="color:red" id="error_user_id">*</span><input type="text" class="form-control" name="videolink'+daysid+'[]" id="videolink" required  ></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">PDF NAME</label><span id="error_dashboard_title" style="color:red;">*</span><input class="form-control" type="text" name="pdf_name'+daysid+'[]" id="pdf_name" value="" style="text-transform: uppercase;"></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">PDF File</label><span id="error_action_to_be_taken" style="color:red;">*</span><input class="form-control" type="file" name="pdf_file'+daysid+'[]" id="pdf_file" value=""  required></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:30px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger btn-xs" onclick="addmoresubitemremove('+daysid+rid+');"><i class="fa fa-close"></i></button></div></div></div></div><br/>');
	 
	
	
}

function addmoresubitemremove(lmsid)
{
	
	$('#lms'+lmsid).remove();
}

function additem()
{

var daysrows = $('.daysrow').length;
var daysectionid=parseFloat(daysrows)+parseFloat(1);
	//var vaaaal=parseFloat(vaal)+parseFloat(1);
	
	var numItems = $('.subitemrow'+daysectionid).length;
	var rid=parseFloat(numItems)+parseFloat(1);
	var rem=daysectionid+rid;
	 $('#dynamictasksaddparent').append('<div class="row daysrow" id="day'+daysectionid	+'"><input type="hidden" name="dayscount[]" value="'+daysectionid+'"><div class="col-md-12"><div class="col-md-3"><div class="form-group"><label for="field-2" class="control-label">Day</label><span id="error_form_title" style="color:red;">*</span><input class="form-control" type="text" name="daytest'+daysectionid+'[]" id="form_title" value="" style="text-transform: uppercase;" required></div></div><div class="col-md-3"><div class="form-group" style="margin-top:25px;" ><button type="button" class="btn btn-info btn-xs" name="add" onclick="additemremove('+daysectionid+','+rem+');" id="removeday"><i class="fa fa-minus"></i> </button></div></div></div></div><div class="row subitemrow'+daysectionid+'" id="lms'+daysectionid+rid+'"><div class="col-md-12"><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">video Name</label><span style="color:red" id="error_user_id">*</span><input type="text" class="form-control" name="videoname'+daysectionid+'[]" id="videoname" required></select></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">video Link</label><span style="color:red" id="error_user_id">*</span><input type="text" class="form-control" name="videolink'+daysectionid+'[]" id="videolink" required  ></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">PDF NAME</label><span id="error_dashboard_title" style="color:red;">*</span><input class="form-control" type="text" name="pdf_name'+daysectionid+'[]" id="pdf_name" value="" style="text-transform: uppercase;"></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">PDF File</label><span id="error_action_to_be_taken" style="color:red;">*</span><input class="form-control" type="file" name="pdf_file'+daysectionid+'[]" id="pdf_file" value=""  required></div></div><div class="col-md-2"><div class="form-group" style="margin-top:25px;" ><button type="button" class="btn btn-warning btn-xs" name="add" id="addmore_btn2" onclick="addmoresubitem('+daysectionid+');"><i class="fa fa-plus"></i> </button></div></div></div></div><div id="dynamictasksday'+daysectionid+'"></div></div></div><br/>');
	 
	
	
}

function additemremove(daysectionid,rem)
{	
	//alert(daysectionid);
	$('#day'+daysectionid).remove();
	$('#lms'+daysectionid+rem).remove();
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