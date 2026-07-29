<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Edit form</title>

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
				<div class="col-md-2"></div>
                    <div class="col-xs-12 col-md-8">
                        <div class="card-box" style="min-height:500px">
 <p class="page-title text-center"><?php $query = $this->db->select('a.id, a.form_type,a.alias, a.form_title,a.action_to_be_taken, a.description, a.dashboard_video_link, a.form_video_link, a.department_id, a.ref_no, b.department_id, b.department, a.tatdays, a.tattime')->from('dynamic_forms a')->join('departments b','a.department_id=b.department_id','left')->where('a.id',$this->uri->segment(3))->get();
							foreach($query->result() as $row){
								echo "<span style='font-size:18px'>".strtoupper($row->form_title)."</span>";
								echo "<span style='font-size:14px'>  (".strtoupper($row->description).")</span>";
							}?></p><hr>
                            <div class="row">
							<div class="col-md-2"></div>
                                <div class="col-sm-12 col-xs-12 col-md-8" style="border:1px solid #E8E7E6; padding:20px 20px 20px 20px">
								<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
								<form method="post" id="loginForm" action="<?php echo page_url;?>Form/update_form_detail/<?php echo $this->uri->segment(3);?>">
								    <div id="pageloader">
                                    <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
                                    </div>	
									<input class="form-control" type="hidden" name="old_form_title" value="<?php echo $row->form_title;?>" required>
									<div class="col-md-6">
													<div class="form-group">
													<label>FORM TITLE<span style="color:red">*</span></label>
														<input class="form-control" type="text" name="form_title" min="0" value="<?php echo $row->form_title;?>" required>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
													<label>DESCRIPTION<span style="color:red"></span></label>
														<input class="form-control" type="text" name="form_description" min="0" value="<?php echo $row->description;?>">
													</div>
												</div>
												
													<div class="col-md-12">
													<div class="form-group">
													<label>FORM VIDEO LINK<span style="color:red"></span></label>
														<input class="form-control" type="text" name="form_video_link" min="0" value="<?php echo $row->form_video_link;?>">
													</div>
												</div>
												
													<div class="col-md-12">
													<div class="form-group">
													<label>DASHBOARD VIDEO LINK<span style="color:red"></span></label>
														<input class="form-control" type="text" name="dashboard_video_link" min="0" value="<?php echo $row->dashboard_video_link;?>">
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														 <label for="field-2" class="control-label">FORM CODE</label>
														 <span style="color:red" id="error_user_id">*</span>
														<input type="text" class="form-control" name="formcode" id="formcode" value="<?php echo $row->alias;?>" required onblur="checkalias(this.value)"; style="text-transform:uppercase">
														
													</div>
													</div>
													<div class="col-md-6">
													    	<div class="form-group">
														 <label for="field-2" class="control-label">Ref No Applicable</label>
														<span id="error_ref_applicable" style="color:red;">*</span>
													<select class="form-control" name="ref_applicable" id="ref_applicable">
													    <option value="0" <?php if($row->ref_no=='0'){echo "selected";}?>>No</option>
													    <option value="1" <?php if($row->ref_no=='1'){echo "selected";}?>>Yes</option>
													</select>
														 
													</div>
													</div>
													<div class="col-md-12">
													<div class="form-group">
													<label>ACTION TO BE TAKEN<span style="color:red"></span></label>
														<input class="form-control" type="text" name="action_to_be_taken" value="<?php echo $row->action_to_be_taken;?>">
													</div>
												</div>
										<div class="col-md-12">
													<div class="form-group">
													<label>DASHBOARD SHOW PERMISSION<span style="color:red"></span>  </label><br>
													<?php 
													$frm_id = $this->uri->segment(3);
													$q = $this->db->select('a.id,a.user_id, a.form_id, b.user_id, b.first_name, b.last_name')->from('dynamic_form_dashboard_access a')->join('system_users b','a.user_id=b.user_id','left')->where('a.form_id',$this->uri->segment(3))->get();
													if($q->num_rows()>0){
														foreach($q->result() as $users){
															echo "<a href='".page_url."Form/delete_member/".$users->id."/".$frm_id."' class='delete'><span class='btn btn-danger btn-xs'>".$users->first_name."".$users->last_name." &nbsp; X</span>&nbsp; &nbsp;</a>";
														}
													}
													
													?>
														<select multiple class="select3" name="dashboard_shown_to[]" id="dashboard_shown_to">
														<option value=""></option>
														<?php 
															$query = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('user_status','1')->where('hide_profile','0')->get();
															foreach($query->result() as $rowss){
														?>
														<option value="<?php echo $rowss->user_id;?>"><?php echo strtoupper($rowss->first_name." ".$rowss->last_name);?></option>
															<?php }?>
														</select>
													</div>
												</div>	
									<div class="col-md-4">
													<div class="form-group">
														<label>Form Type</label>
														<select class="form-control" name="form_type" id="form_type" required>
															<option value="1" <?php if($row->form_type=='1'){echo "selected";}?>>Resolution</option>
															<option value="2" <?php if($row->form_type=='2'){echo "selected";}?>>Solution/Resolution</option>
														</select>
													</div>
												</div>
									
												
									<div class="col-md-4">
													<div class="form-group">
													<label>SET TAT in Days <span style="color:red">*</span></label>
														<input class="form-control" type="number" name="tat" min="0" value="<?php echo $row->tatdays?>" required>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
													<label>SET TAT Time in Hour<span style="color:red"></span></label>
														<input class="form-control" type="number" name="tattime" min="0" value="<?php echo $row->tattime?>">
													</div>
												</div>
									
												<?php 
												$query = $this->db->select('field_id, field_design, field_type, form_id, form_label, field_required')->from(' master_dynamic_fields')->where('form_id',$this->uri->segment(3))->get();
												foreach($query->result() as $rowdata){
												?>
												<div class="row">
												
												<div class="col-md-6">
													<div class="form-group">
												<!-- HIDDEN DATA-->
														<input type="hidden" name="recordid[]" value="<?php echo $rowdata->field_id;?>">
														<input type='hidden' name='input_type[]' value='<?php echo $rowdata->field_design;?>'>
														<input type='hidden' name='fieldtype[]' value='<?php echo $rowdata->field_type;?>'>
														<input type='hidden' name='labelname[]' value='<?php echo $rowdata->form_label;?>'>
												<!-- HIDDEN DATA-->
														 <label for="field-2" class="control-label"><?php echo strtoupper($rowdata->form_label);?></label> <?php if($rowdata->field_type=='4' || $rowdata->field_type=='9'){echo "<br>";}?>
														 
														<?php echo $rowdata->field_design;?>
														<?php if($rowdata->field_type=='3' || $rowdata->field_type=='4' || $rowdata->field_type=='9'){
															?>
															<input type="text" class="form-control" style="text-transform: uppercase;" name="optiondata<?php echo $rowdata->field_id;?>" value="" placeholder="ADD OPTIONS WITH COMMA">
															
															
															
															<?php
														}?>
														<?php if($rowdata->field_type=='8'){?>
															<div class="col-md-6">
																<div class="form-group">
																<label>MIN</label>
																<input type="number" class="form-control" name="min<?php echo $rowdata->field_id;?>" value="">
																</div>
															</div>
															<div class="col-md-6">
																<div class="form-group">
																<label>MAX</label>
																<input type="number" class="form-control" name="max<?php echo $rowdata->field_id;?>" value="">
																</div>
															</div>
															<?php }?>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
												<label for="field-2" class="control-label">REQUIRED</label><?php if($rowdata->field_type=='4' || $rowdata->field_type=='9'){echo "<br><br>";}?>
														<select class="form-control" name="isrequired[]" required>
															<option value="1" <?php if($rowdata->field_required=='1'){echo "selected";}?>>YES</option>
															<option value="0" <?php if($rowdata->field_required=='0'){echo "selected";}?>>NO</option>
														</select>
														<input type="hidden" name="defaultrequired[]" value="<?php echo $rowdata->field_required;?>">
													</div>
												</div>
												</div>
												<?php }?>
												
												
											
											<div class="col-md-9"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" id="save" class="btn btn-success" value="Update">
											</div>
										</div>
									
									</form>
                                   

                                </div>
								<div class="col-md-2"></div>
                            </div>
                            <!-- end row -->
                        </div> <!-- end ard-box -->
                    </div><!-- end col-->
                  <div class="col-md-2"></div>
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
			</body>
</html>