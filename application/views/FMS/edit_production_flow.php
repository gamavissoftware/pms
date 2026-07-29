<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle;?> Edit Production Flow</title>

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
<style>
table.manglesh thead th {
				background: #003366;
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
                            <h4 class="page-title">EDIT PRODUCTION FLOW </h4>
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
								$this->db->select('*')->from('production_flow')->where('id',$id);
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $row)
								
								?>
								
                                   <form method="post" id="loginForm" action="<?php echo page_url;?>FMS/update_production_plan_flow/<?php echo $row->id;?>">
								    <div id="pageloader">
                                    <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
                                    </div>
										 <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">FLOW NAME</label>
														<span id="error_flow_name" style="color:red;"></span>
                                                        <input type="text" class="form-control" id="flow_name" style="text-transform: uppercase;" name="flow_name" placeholder="" value="<?php echo $row->production_flow;?>" required>
                                                    </div>
                                                </div>
												
												<div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">FLOW ORDER</label>
														<span id="error_flow_order" style="color:red;"></span>
                                                        <input type="number" class="form-control" id="flow_order" min="1" style="text-transform: uppercase;" name="flow_order" placeholder="" value="<?php echo $row->sortorder;?>" required>
                                                    </div>
                                                </div> 
									   
									  <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">PARALLEL FMS APPL?</label><br/>
														<span id="" style="color:red;"></span>
                                                        <input type="checkbox" id="parfms" name="parfms" value="1" <?php if($row->parallel=='1'){?> checked <?php } ?> >
                                                    </div>
                                                </div>
									   
									   <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">MERGE FMS?</label><br/>
														<span id="" style="color:red;"></span>
                                                        <input type="checkbox" id="mergefms" name="mergefms" value="1" <?php if($row->mergefms=='1'){?> checked <?php } ?> onchange="mergefmss();">
                                                    </div>
                                                </div> 
												
												
												<div class="col-md-2 mergedata" style="display:none;">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">MERGE WITH</label>
														<span id="" style="color:red;">*</span>
                                                        <select id="mergefmswith" name="mergefmswith" onchange="getfmsflowfms();" class="form-control">
														<option value="">PRODUCTION FLOW?</option>
														<?php
														/** Fms Merge Data **/
														$restp=$this->db->select('mergewith')->from('fmsmerge')->where('productionflow',$id)->get();
														if($restp->num_rows()>0)
														{
															foreach($restp->result() as $restp12);
															$existingmerge=$restp12->mergewith;
														}else { $existingmerge=""; }
														/** End **/
														$restp=$this->db->select('production_flow,id')->from('production_flow')->where('id !=',$id)->get();
														if($restp->num_rows()>0)
														{
															foreach($restp->result() as $restpp)
															{
														?>
														<option value="<?php echo $restpp->id;?>" <?php if($existingmerge==$restpp->id){?> selected <?php } ?>><?php echo $restpp->production_flow;?></option>
														<?php
															}
														}
														?>
														</select>
                                                    </div>
                                                </div>
												


<div class="col-md-2 mergedata" style="display:none">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">FLOW</label>
														<span id="" style="color:red;">*</span>
                                                        <select id="mergedflow" name="mergedflow" class="form-control">
														
														</select>
                                                    </div>
                                                </div>			
										
											<div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">INCLUDE IN LONG REPORT?</label><br/>
														<span id="" style="color:red;"></span>
                                                        <input type="checkbox" id="longreport" name="longreport" value="1" <?php if($row->longreport=='1'){?> checked <?php } ?>>
                                                    </div>
                                                </div> 
												
												
												<div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">INCLUDE IN MIS?</label><br/>
														<span id="" style="color:red;"></span>
                                                        <input type="checkbox" id="includemis" name="includemis" value="1" <?php if($row->longreport=='1'){?> checked <?php } ?>>
                                                    </div>
                                                </div> 

<div style="clear:both;height:10px"></div>		

										<div class="col-md-2">
											<div class="form-group">
												<label>STATUS</label>
												<select class="form-control" id="status" name="status">
													<option value="<?php echo $row->status;?>"><?php if($row->status=='1'){echo "Active";}else{echo "Inactive";}?></option>
													<option value="1" <?php if($row->status=='1'){echo "selected";}?>>ACTIVE</option>
													<option value="0" <?php if($row->status=='0'){echo "selected";}?>>INACTIVE</option>
												</select>
											</div>
										</div>
									   <div style="clear:both;height:10px"></div>
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
$("#save").click(function() {
var flow_name = $("#flow_name").val();
if(flow_name=='')
{
	$("#error_flow_name").html('Required!');
}

var flow_order = $("#flow_order").val();
if(flow_order=='')
{
	$("#error_flow_order").html('Required!');
}

var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}

if(flow_name==''|| flow_order=='' || status=='')
{
	
	return false;
}

});
	
	if($('#mergefms').is(":checked"))
	{
		$(".mergedata").css('display','');
		$("#mergefmswith").attr('required',true);
		$("#mergedflow").attr('required',true);
	}else{
		
		$(".mergedata").css('display','none');
		$("#mergefmswith").attr('required',false);
		$("#mergedflow").attr('required',false);
	}
	
	
	getfmsflowfms();
});
	
	
	function mergefmss()
{
	if($('#mergefms').is(":checked"))
	{
		$(".mergedata").css('display','');
		$("#mergefmswith").attr('required',true);
		$("#mergedflow").attr('required',true);
	}else{
		
		$(".mergedata").css('display','none');
		$("#mergefmswith").attr('required',false);
		$("#mergedflow").attr('required',false);
	}
	
}

function getfmsflowfms()
{
	var factory=$("#mergefmswith").val();
	  
	  $.ajax({
		type:"post",
		url:"<?php echo page_url;?>FMS/getfmsslowprocesswiseforproductionedit/"+factory+"/",
		data:"1",
		success:function(data){
		$("#mergedflow").html(data);
		}
		});
	
}
</script>
</body>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready
</script>
</html>