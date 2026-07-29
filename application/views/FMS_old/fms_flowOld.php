<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Prestogroup FMS Flow</title>

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
		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 
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

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
		<style>
table.manglesh thead th {
				background: #003366;
				color:#fff;
				font-weight:bold;
			}
</style>
    </head>
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
                    <div class="col-sm-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right" style="margin-top:30px">
						  <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">Add New FMS Flow</button>
                               
                            </div>
                           
                            <h4 class="page-title">FMS Flow list</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
		<div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th>SR NO.</th>
									<th>PRODCTION FLOW</th>
                                    <th>FMS FLOW</th>
                                    <th>WHO</th>
                                    <th>WHAT</th>
                                    <th>HOW</th>
									<th>WHEN</th>
									<th>VIDEO LINK</th>
									<th>ORDER</th>
									<th>STATUS</th>
									<th>ACTION</th>
                                </tr>
                                </thead>


                                <tbody>
								
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
 <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="<?php echo page_url;?>FMS/fms_flow">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Add New FMS Flow</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
											 <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">PRODUCTION FLOW</label>
														<span id="error_production_flow" style="color:red;">*</span>
																																									<select class="form-control select3" style="text-transform: uppercase;" name="production_flow" id="production_flow" onchange="getusers()";>
															<option value="">--SELECT PRODCTION FLOW--</option>
															<?php 
															$query = $this->db->select('id,production_flow, sortorder, status')->from('production_flow')->where('status','1')->order_by('sortorder','asc')->get();
															foreach($query->result() as $row){
															?>
															<option value="<?php echo $row->id;?>"><?php echo $row->production_flow;?></option>
															<?php }?>
														</select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">FLOW NAME</label>
														<span id="error_flowname" style="color:red;">*</span>
                                                        <input type="text" class="form-control" id="flowname" name="flowname" style="text-transform: uppercase;" placeholder="" required>
                                                    </div>
                                                </div>
												
											<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">WHO</label>
														<span id="error_who" style="color:red;">*</span>
                                                        <select class="form-control select3" style="text-transform: uppercase;" name="who" id="who">
															<option value="">--SELECT WHO--</option>
															
														</select>
                                                    </div>
                                                </div>
												
												<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">WHAT</label>
														<span id="error_what" style="color:red;">*</span>
                                                        <input type="text" class="form-control" style="text-transform: uppercase;" id="what" name="what" placeholder="" required>
                                                    </div>
                                                </div>
												<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">HOW</label>
														<span id="error_how" style="color:red;">*</span>
                                                        <input type="text" style="text-transform: uppercase;" class="form-control" id="how" name="how" placeholder="" required>
                                                    </div>
                                                </div>
												<div class="col-md-3">
													<div class="row">
														<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">DAYS</label>
														<span id="error_day" style="color:red;">*</span>
                                                        <input type="number" style="text-transform: uppercase;" class="form-control" id="days" name="days" value="" min="1" placeholder="" required>
                                                    </div>
                                                </div>
															<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">HOUR</label>
														<span id="error_time" style="color:red;"></span>
                                                        <input type="number" style="text-transform: uppercase;" class="form-control" id="time" name="time" value="" min="1" placeholder="">
                                                    </div>
                                                </div>
													</div>
												</div>
												
												
											
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">WHEN</label>
														<span id="error_when" style="color:red;">*</span>
                                                        <input type="text" style="text-transform: uppercase;"  class="form-control" id="when" name="when" placeholder="" required>
                                                    </div>
                                                </div>
												
												<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">VIDEO LINK</label>
														<span id="error_name" style="color:red;"></span>
                                                        <input type="text" class="form-control" id="video_link" name="video_link" placeholder="">
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
												<div class="form-group">
												<label for="field-1" class="control-label">SET ORDER</label>

												<select name="set_order" id="set_order" class="form-control">
												<option value="">Set Order</option>
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
												<label for="field-2" class="control-label">STATUS</label>
												<span id="error_status" style="color:red;"></span>
												<select class="form-control" id="status" name="status">
													<option value="">--SELECT STATUS--</option>
													<option value="1">ACTIVE</option>
													<option value="0">INACTIVE</option>
												</select>
												</div>
											</div>
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div><!-- /.modal -->


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
<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
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
		</script>
 <script>
$( document ).ready(function() {
$('.select2').select2({ });
$('.select3').select2({ });
$('.select4').select2({ });
$('#example').dataTable({
 "bProcessing": false,
 "pagination":true,
 "sAjaxSource": "<?php echo page_url;?>FMS/fms_flow_list/",
 "aoColumns": [
						{ mData: 'sr_no' } ,
                        { mData: 'production_flow' },
						{ mData: 'fms_flow' },
						{ mData: 'who' },
                        { mData: 'what_wedo' },
						{ mData: 'how_wedo' },
						{ mData: 'when_wedo' },
						{ mData: 'video_link' },
						{ mData: 'setorder' },
						{ mData: 'status' },
						{ mData: 'edit' }
						
                ]
        });   
});

</script>

<script language="javascript" type="text/javascript">   
$(document).ready(function() {
$("#save").click(function() {
	
	var production_flow = $("#production_flow").val();
if(production_flow=='')
{
	$("#error_production_flow").html('Required!');
}
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

if(production_flow=='' || flowname=='' || who=='' || what=='' || how==''|| days==''|| when=='' || status=='')
{
	
	return false;
}

});
});


function getusers()
{
	
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
		}
		});
		
	}
	
}
</script>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
</body>
</html>