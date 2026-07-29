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

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script><?PHP $q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();foreach($q->result() as $LOGO);?>
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
<script language="JavaScript" type="text/javascript">
$(document).ready(function(){
    $("a.delete").click(function(e){
        if(!confirm('Do you really want to delete this record?')){
            e.preventDefault();
            return false;
        }
        return true;
    });
     
    
});
</script>
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
                           
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->


                <div class="row">
				
                    <div class="col-xs-12 col-md-12">
                        <div class="card-box" style="min-height:500px">
						<p class="page-title text-center"><?php $query = $this->db->select('a.id, a.form_type, a.form_title, a.description, a.department_id, b.department_id, b.department, a.tatdays, a.tattime')->from('dynamic_forms a')->join('departments b','a.department_id=b.department_id','left')->where('a.id',$this->uri->segment(3))->get();
						foreach($query->result() as $row){
						echo "<span style='font-size:18px'>".strtoupper($row->form_title)."</span>";
						if($row->description){
						echo "<span style='font-size:14px'>  (".strtoupper($row->description).")</span>";
						}
						}?></p><hr>
                            <div class="row">
							
                                <div class="col-sm-12 col-xs-12 col-md-12">
								<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
								<form method="post" id="loginForm" action="<?php echo page_url;?>Form/set_permission/<?php echo $this->uri->segment(3);?>">
								    <div id="pageloader">
                                    <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
                                    </div>
									<div class="row">
									<?php 
									$query = $this->db->select('a.user_id, b.first_name, b.last_name')->from('dynamic_form_dashboard_access a')->join('system_users b','a.user_id=b.user_id','left')->where('a.form_id',$this->uri->segment(3))->get();
									foreach($query->result() as $user){
									?>
									<div class="col-md-4" style="border-right:1px dotted">
										<div class="row">
										<div class="col-md-8">
											<label>FIELD NAME</label>
										</div>
										<div class="col-md-4">
											<label>PERMISSION</label>
										</div><hr>
											<h4 class="text-center" style="color:red;"><?php echo $user->first_name." ".$user->last_name;?></h4><hr>
											<input type="hidden" name="user_id[]" value="<?php echo $user->user_id;?>">
											<?php 
											$query = $this->db->select('field_id,form_label')->from('master_dynamic_fields')->where('form_id',$this->uri->segment(3))->get();
											foreach($query->result() as $label){
											?>
											<div class="col-md-8">
												<div class="form-group">
												
													<label><?php echo $label->form_label;?> </label>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
												<?php 
												$query= $this->db->select('access_per')->from('dynamic_form_access_permission')->where('form_id',$this->uri->segment(3))->where('user_id',$user->user_id)->where('label_id',$label->field_id)->get();
												if($query->num_rows()){
													foreach($query->result() as $checkper);
													if($checkper->access_per=='1'){
														$checked="checked";
													}else{
														$checked="";
													}
												}else{
													$checked="";
												}
												?>
												<input type="hidden" name="label_id<?php echo $user->user_id;?>[]" value="<?php echo $label->field_id;?>">
												<label><input type="checkbox" <?php echo $checked;?> name="pemission<?php echo $user->user_id;?><?php echo $label->field_id;?>" value="1"> </label>
												</div>
											</div>
											<?php }?>
										</div>
									</div>	

									<?php }?>
</div><hr>
									<div class="row">
										<div class="col-md-4"></div>
										<div class="col-md-4">
											<div class="form-group text-center" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" id="save" class="btn btn-success" value="SET PERMISSION">
											</div>
										</div>
										<div class="col-md-4"></div>
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
 
 $('#dynamictasks1').append('<div id="row'+i+'" class="row"><div class="col-md-12"><div class="col-md-6"><div class="form-group"><label for="field-2" class="control-label">FIELD TITLE<span style="color:red;"></span></label><span id="" style="color:red;"></span><input type="text" style="text-transform: uppercase;" class="form-control" name="input_type[]" id="input_type" placeholder="FIELD LABEL" value="" required></div></div><div class="col-md-5"><div class="form-group"><label for="field-2" class="control-label">FIELD TYPE<span style="color:red;">*</span></label><span id="error_user_name" style="color:red;"></span><select class="form-control" name="fieldtype[]" id="fieldtype" required><option value="1">INPUT TYPE</option><option value="2">DESCRIPTION BOX</option><option value="3">SELECT BOX</option><option value="4">CHECKBOX BOX</option><option value="5">FILE UPLOAD</option><option value="6">DATE PICKER</option><option value="7">TIME PICKER</option></select></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:30px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div></div><br/>');
 
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

var form_title = $("#form_title").val();
if(form_title=='')
{
	$("#error_form_title").html('Required!');
	
}
var description = $("#description").val();
if(description=='')
{
	$("#error_description").html('Required!');
	
}


if(department=='' || form_title=='' || description==''){
	return false;
}

});
});


	

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
<script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
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