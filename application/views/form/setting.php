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
 <p class="page-title text-center"><?php $query = $this->db->select('a.id, a.form_title,a.tatappl, a.description, a.department_id, b.department_id, b.department')->from('dynamic_forms a')->join('departments b','a.department_id=b.department_id','left')->where('a.id',$this->uri->segment(3))->get();
							foreach($query->result() as $row){
								echo "<span style='font-size:18px'>".strtoupper($row->form_title)."</span>";
								echo "<span style='font-size:14px'>  (".strtoupper($row->description).")</span>";
							}?></p><hr>
							<form method="post" id="loginForm" action="<?php echo page_url;?>Form/form_marking_as_approve/<?php echo $this->uri->segment(3);?>">
                            <div class="row">
							<div class="col-md-2"></div>
                                <div class="col-sm-12 col-xs-12 col-md-8" style="border:1px solid #E8E7E6; padding:20px 20px 20px 20px">
								<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
								
								    <div id="pageloader">
                                    <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
                                    </div>	
									
										<?php
									if($row->tatappl==0)
									{
									$as="readonly";
									$ase=0;
									}else
									{
									    $as='';
									    $ase='';
									}
									?>
									<div class="col-md-6">
													<div class="form-group">
													<label>SET TAT in Days <span style="color:red">*</span></label>
														<input class="form-control" type="number" name="tat" min="0" value="<?php echo $ase;?>" <?php echo $as;?> required>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
													<label>SET TAT Time in Hour<span style="color:red"></span></label>
														<input class="form-control" type="number" name="tattime" min="0" value="<?php echo $ase;?>" <?php echo $as;?>>
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
															<option value="1">YES</option>
															<option value="0">NO</option>
														</select>
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
									
									
                                   

                                </div>
								<div class="col-md-2"></div>
                            </div>
								</form>
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
        
        <script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>
        <script src="<?php echo assets_url;?>js/detect.js"></script>
        <script src="<?php echo assets_url;?>js/fastclick.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.slimscroll.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.blockUI.js"></script>
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