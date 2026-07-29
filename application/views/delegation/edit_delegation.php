<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Edit Delegation</title>

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


        <div class="wrapper">
            <div class="container">

                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="btn-group pull-right">
                              
                            </div>
                            <h4 class="page-title">Edit Delegation Master</h4>
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
								<?php
								$id = $this->uri->segment(3);
								$this->db->select('*')->from('delegation_master')->where('id',$id);
								$query = $this->db->get();
								
								if($query->num_rows() > 0) {
								$res = $query->result();
								foreach($res as $row);
								$what_to_do = $row->what_to_do;
								$when_to_do = $row->when_to_do;
								$video = $row->video;
								$dashboard_video_link = $row->dashboard_video_link;
								} else {
								$what_to_do = '';
								$when_to_do = '';
								$video = '';
								$dashboard_video_link = '';
								}
								
								?>
								
                                   <form method="post" id="loginForm" action="<?php echo page_url;?>Delegation/edit_delegation_master/<?php echo $row->id;?>">
								    <div id="pageloader">
                                    <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
                                    </div>
										<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">BUSINESS LOCATION</label>
														 <span id="error_business_loc" style="color:red;">*</span>
														 <select class="form-control" id="business_loc" name="business_loc" readonly>
													<?php 
													$this->db->select('a.company_name, a.business_loc_id,a.state_id, a.city_id,b. 	state_id,b.state_name,c.city_id,c.city_name')->from('business_location a')->join('states b','a.state_id=b.state_id','left')->join('cities c','a.city_id=c.city_id','left')->where('business_loc_status','1')->where('a.business_loc_id',2);
													$this->db->order_by('a.company_name','asc');
													$query = $this->db->get();
													if($query->num_rows() > 0) {
													$res = $query->result();
													foreach($res as $row1){
													?>
													<option value="<?php echo $row1->business_loc_id;?>" <?php if($row->business_id==$row1->business_loc_id){echo "selected";}?>><?php echo strtoupper($row1->company_name);?></option>
											<?php } } else {?>
											<option value=""></option>
											<?php } ?>
												</select>
												<script type="text/javascript">
											
													$("#business_loc").change(function(){
													var business_loc=$("#business_loc").val();
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Delegation/user_list",
													data:"business_loc="+business_loc,
													success:function(data){
													$("#user_id").html(data);
													}
													});
													
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Delegation/user_list",
													data:"business_loc="+business_loc,
													success:function(data){
													$("#reporting_head").html(data);
													}
													});
													});
											
												</script>
												
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">WHO</label>
														 <span id="error_user_id" style="color:red;">*</span>
														 <select class="form-control select3" id="user_id" name="user_id">
														 <?php 
														 $qry = $this->db->select('user_id, business_location, first_name, last_name')->from('system_users')->where('user_id',$row->assigned_to)->get();
												 if($query->num_rows() > 0) {
														 foreach($qry->result() as $assignedto){
														 ?>
													<option value="<?php echo $assignedto->user_id;?>" <?php if($row->assigned_to==$assignedto->user_id){echo "selected";}?>?><?php echo strtoupper($assignedto->first_name." ".$assignedto->last_name);?></option>
														 <?php } } else {?>
													<option value=""></option>
														 <?php } ?>
												</select>
													</div>
												</div>
												
												
												<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">WHAT</label>
														 <span id="error_what" style="color:red;">*</span>
														 <input class="form-control" type="text" name="what" id="what" value="<?php echo $what_to_do;?>">
														 
													</div>
												</div>
												
												
												<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">WHEN</label>
														 <span id="error_when" style="color:red;">*</span>
														 <input class="form-control" type="text" name="when" id="when" value="<?php echo $when_to_do;?>">
														 
													</div>
												</div>
												
													<div class="col-md-8">
													<div class="form-group">
														 <label for="field-2" class="control-label">Form Video Link</label>
														 
														 <input class="form-control" type="text" name="form_video_link" id="form_video_link" value="<?php echo $video;?>">
														 
													</div>
												</div>
												
													<div class="col-md-12">
													<div class="form-group">
														 <label for="field-2" class="control-label">Dashboard Video Link</label>
														
														 <input class="form-control" type="text" name="dashboard_video" id="dashboard_video" value="<?php echo $dashboard_video_link;?>">
														 
													</div>
												</div>
											
												 
										<div class="col-md-9"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" id="updsave" class="btn btn-success" value="Update">
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
<script>
$(document).ready(function(){
	   $("#updsave").attr('disabled',false);
	   $("#updsave").val('submit');
  $("#loginForm").on("submit", function(){
   // $("#pageloader").fadeIn();
   $("#updsave").attr('disabled',true);
     $("#updsave").val('Please Wait...');
  });//submit
});//document ready
</script>
<script language="javascript" type="text/javascript">   
$(document).ready(function() {
	$('.select2').select2({ });
$('.select3').select2({ });
$('.select4').select2({ });
$("#updsave").click(function() {
	
	var business_loc = $("#business_loc").val();
if(business_loc=='')
{
	$("#error_business_loc").html('Required!');
}
var user_id = $("#user_id").val();
if(user_id=='')
{
	$("#error_user_id").html('Required!');
} else {
    
    $("#error_user_id").html('');
}
var reporting_head = $("#reporting_head").val();
if(reporting_head=='')
{
	
	$("#error_reporting_head").html('Required!');
}

var what = $("#what").val();
if(what=='')
{
	
	$("#error_what").html('Required!');
}

var how = $("#how").val();
if(how=='')
{
	
	$("#error_how").html('Required!');
}
var when = $("#when").val();
if(when=='')
{
	
	$("#error_when").html('Required!');
}


var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}


if(business_loc=='' || user_id=='' || reporting_head=='' || what=='' || how==''|| when=='' || status=='' || set_order=='' || response_type=='' || actiontobetaken=='')
{
	return false;
}

});
});


	

</script>

<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
  
  var business_loc=$("#business_loc").val();
  var user_id=$("#user_id").val();
  
//   alert(user_id);
  
  

	$.ajax({
    	type:"post",
    	url:"<?php echo page_url;?>Delegation/user_list",
    	data:"business_loc="+business_loc,
    	success:function(data){
    	$("#user_id").html(data);
    	
        $('#user_id').val(user_id); 
        $('#user_id').trigger('change');

    	}
	});
	
	
});//document ready
</script>
<script> $(document).ready(function() {
				$("#datepicker1").datepicker();
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
                })

            });</script>
			</body>
</html>