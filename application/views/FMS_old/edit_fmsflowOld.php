<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Edit FMS Flow</title>

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
                            <h4 class="page-title">Edit FMS Flow</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->


                <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">

								<?php
								$id = $this->uri->segment(3);
								$this->db->select('*')->from('fms_flow')->where('flow_id',$id);
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $row)
								
								?>
								
                                   <form method="post" action="<?php echo page_url;?>FMS/update_fms_flow/<?php echo $row->flow_id;?>">
								   <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">PRODUCTION FLOW</label>
														<span id="error_production_flow" style="color:red;">*</span>
																																									<select class="form-control select3" style="text-transform: uppercase;" name="production_flow" id="production_flow" onchange="getusers();">
															<option value="">--SELECT PRODCTION FLOW--</option>
															<?php 
															$query = $this->db->select('id,production_flow, sortorder, status')->from('production_flow')->where('status','1')->order_by('sortorder','asc')->get();
															foreach($query->result() as $row2){
															?>
															<option value="<?php echo $row2->id;?>" <?php if($row->production_flow_id==$row2->id){echo "selected";}?>><?php echo $row2->production_flow;?></option>
															<?php }?>
														</select>
                                                    </div>
                                                </div>
										<div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">FLOW NAME</label>
														<span id="error_flowname" style="color:red;">*</span>
                                                        <input type="text" class="form-control" id="flowname" name="flowname" style="text-transform: uppercase;" placeholder="" required value="<?php echo $row->fms_flow;?>" readonly>
                                                    </div>
                                                </div>
												
												<div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">WHO</label>
														<span id="error_who" style="color:red;">*</span>
                                                        <select class="form-control select3" style="text-transform: uppercase;" name="who" id="who">
															<option value="">--SELECT WHO--</option>
															
														</select>
                                                    </div>
                                                </div>
												
												<div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">WHAT</label>
														<span id="error_what" style="color:red;">*</span>
                                                        <input type="text" class="form-control" id="what" name="what" placeholder="" style="text-transform: uppercase;" value="<?php echo $row->what_wedo;?>" required>
                                                    </div>
                                                </div>
												<div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">HOW</label>
														<span id="error_how" style="color:red;">*</span>
                                                        <input type="text" class="form-control" id="how" name="how" placeholder="" style="text-transform: uppercase;" value="<?php echo $row->how_wedo;?>" required>
                                                    </div>
                                                </div>
												
												<div class="col-md-1">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">DAYS</label>
														<span id="error_day" style="color:red;">*</span>
                                                        <input type="number" class="form-control" id="days" name="days" style="text-transform: uppercase;" value="<?php echo $row->total_days;?>" min="1" placeholder="" required>
                                                    </div>
                                                </div>
									   
									   <div class="col-md-1">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">HOUR</label>
														<span id="error_time" style="color:red;"></span>
                                                        <input type="number" class="form-control" id="time" name="time" style="text-transform: uppercase;" value="<?php echo $row->set_time;?>" min="0" placeholder="">
                                                    </div>
                                                </div>
												
												<div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">WHEN</label>
														<span id="error_when" style="color:red;">*</span>
                                                        <input type="text" class="form-control" id="when" name="when" style="text-transform: uppercase;" value="<?php echo $row->when_wedo;?>" placeholder="" required>
                                                    </div>
                                                </div>
												
												<div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">VIDEO LINK</label>
														<span id="error_name" style="color:red;"></span>
                                                        <input type="text" class="form-control" id="video_link" value="<?php echo $row->video_link;?>" name="video_link" placeholder="">
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">SET ORDER</label>
														
														<select name="set_order" id="set_order" class="form-control">
														<option value="">Set Order</option>
														<option value="<?php echo $row->setorder;?>" selected><?php echo $row->setorder;?></option>
														<?php
														$resttui=$this->db->select('setorder')->from('fms_flow')->get();
														$previousorder=array();
														if($resttui->num_rows()>0)
														{
															foreach($resttui->result() as $resttui1)
															{
																$previousorder[]=$resttui1->setorder;
															}
														}else
														{
															$previousorder=array();
														}
														
														for($i=1;$i<16;$i++)
														{
															if (!in_array($i, $previousorder)) 
														{ 
 														?>
														<option value="<?php echo $i;?>"><?php echo $i;?></option>
														<?php
														}
														}
														?>
														</select>
                                                       
                                                    </div>
                                                </div>

										<div class="col-md-3">
											<div class="form-group">
												<label>STATUS</label>
												<select class="form-control" id="status" name="status">
													<option value="<?php echo $row->status;?>"><?php if($row->status=='1'){echo "Active";}else{echo "Inactive";}?></option>
													<option value="1" <?php if($row->status=='1'){echo "selected";}?>>ACTIVE</option>
													<option value="0" <?php if($row->status=='0'){echo "selected";}?>>INACTIVE</option>
												</select>
											</div>
										</div>
										<div class="col-md-9"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" id="save" class="btn btn-success" value="UPDATE">
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
<script>

		// Time Picker
            jQuery('#timepicker').timepicker({
                defaultTIme : false
            });
			jQuery('#timepicker4').timepicker({
                defaultTIme : false
            });
            jQuery('#timepicker2').timepicker({
                showMeridian : false
            });
            jQuery('#timepicker3').timepicker({
                minuteStep : 15
            });
			 jQuery('#datepicker').datepicker();
            jQuery('#datepicker-autoclose').datepicker({
                autoclose: true,
                todayHighlight: true
            });
			jQuery('#datepicker-autoclose1').datepicker({
                autoclose: true,
                todayHighlight: true
            });
		
		</script>
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<script language="javascript" type="text/javascript">   
$(document).ready(function() {
	getusers();
$('.select2').select2({ });
$('.select3').select2({ });
$('.select4').select2({ });
$("#save").click(function() {
var flowname = $("#flowname").val();
if(flowname=='')
{
	$("#error_flowname").html('Required!');
}
var who = $("#who").val();
if(who=='')
{
	
	$("#error_who").html('Required!');
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
var days = $("#days").val();
if(days=='')
{
	
	$("#error_days").html('Required!');
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

if(flowname=='' || who=='' || what=='' || how==''|| days==''|| when=='' || status=='')
{
	
	return false;
}

});
});

function getusers()
{
	var previous="<?php echo $row->who_wedo;?>";
	
	var fmsp=$("#production_flow").val();
	if(fmsp!='' || fmsp!='0')
	{
		$("#who").find('option').not(':first').remove();
		$.ajax({
		type:"post",
		url:"<?php echo page_url;?>Master/User_management/getfmswiseuser/"+fmsp,
		data:"1",
		success:function(data){
		$("#who").html(data);
		$("#who").select2("val", previous);
		//$('#who option[value='+previous+']').attr('selected','selected');
		}
		});
		
	}
	
}
</script>
</body>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
</html>