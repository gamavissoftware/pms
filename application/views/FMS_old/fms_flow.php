<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="<?php echo copyright; ?>">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title><?php echo sitetitle; ?> FMS Flow</title>



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

</style>

    </head>

    </head>





    <body>





        <!-- Navigation Bar-->

                <header id="topnav">

          <?php $this->load->view('common/nav-menu');?>

        </header>

        <!-- End Navigation Bar-->


		<?php $this->load->view('common/info-section.php');?>


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

									<th>MOVE TO</th>

									<th>DEPENDENT</th>

									<th>STOCK VALUE</th>

									

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

																																									<select class="form-control select3" style="text-transform: uppercase;" name="production_flow" id="production_flow" onchange="getusers(); getsortorder(); gettat(); getfmsinfo(); getdependentfms();">

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

                                                        <input type="number" style="text-transform: uppercase;" class="form-control" id="days" name="days" value="" min="0" placeholder="" required>

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

                                                        <label for="field-1" class="control-label">TAT FROM</label>

														<span id="error_name" style="color:red;">*</span>

														<select name="tat" id="tat" class="form-control" required>

														<option value="">TAT FROM</option>

															

														

														</select>

                                                       

                                                    </div>

                                                </div>

											

												

												<div class="col-md-6">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">WHEN</label>

														<span id="error_when" style="color:red;">*</span>

                                                        <input type="text" style="text-transform: uppercase;"  class="form-control" id="when" name="when" placeholder="" required>

                                                    </div>

                                                </div>

												

												<div class="col-md-6">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">MOVE TO</label>

														<span id="error_moveto" style="color:red;">*</span>

														<select name="moveto[]" id="moveto" class="select3" multiple>

														<option value="">MOVE TO </option>

															

														

														</select>

                                                       

                                                    </div>

                                                </div>

												

												

													<div class="col-md-2">

													<div class="form-group">

														<label>Days to perpare <span style="color:red">*</span></label><br>

													<input type="number" name="pdays" class="form-control" required>

													</div>

												</div>

												

												

												<div class="col-md-2">

													<div class="form-group">

														<label>Final step?</label><br>

														<input type="checkbox" name="final" value="1" id="final">

													</div>

												</div>

												

												

												

												 <div class="col-md-3">

													<div class="form-group">

														<label>Fabrication Jump Appl?</label><br>

														<input type="checkbox" name="jumpfabrication" value="1" id="jumpfabrication">

													</div>

												</div>

												

												<div class="col-md-2">

													<div class="form-group">

														<label>Dependent</label><br>

														<input type="checkbox" name="dependent" value="1" id="dependent">

													</div>

												</div>

												<script>

													$('#dependent').change(function(){

													if (this.checked) {

													$('#showdependent').fadeIn('slow');

													}

													else {

													$('#showdependent').fadeOut('slow');

													}                   

													});

												</script>

												

												<div class="col-md-4" id="showdependent" style="display:none">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">DEPENDENT TO</label>

														<span id="error_dependentto" style="color:red;">*</span>

														<select name="dependentto[]" id="dependentto" class="select3" multiple>

														

														</select>

                                                       

                                                    </div>

                                                </div>

												

												

												

												<div class="col-md-6">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">VIDEO LINK</label>

														<span id="error_name" style="color:red;"></span>

                                                        <input type="text" class="form-control" id="video_link" name="video_link" placeholder="">

                                                    </div>

                                                </div>

												

												<div class="col-md-4">

												<div class="form-group">

												<label for="field-1" class="control-label">SET ORDER</label>

												<span id="setorder" style="color:red;">*</span>

												<select name="set_order" id="set_order" class="form-control" required>

												<option value="">Set Order</option>

												

												</select>



												</div>

												</div>

												

												<div class="col-md-4">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">RESPONSE TYPE</label>

														<span id="responsetype" style="color:red;">*</span>

														<select name="response_type" id="response_type" class="form-control" required>

														<option value="">RESPONSE TYPE</option>

														<?php

														$restyu=$this->db->select('id,pagetype')->from('uitype')->get();

														

														if($restyu->num_rows()>0)

														{

															foreach($restyu->result() as $uittype)

															{

														?>

														<option value="<?php echo $uittype->id;?>"><?php echo $uittype->pagetype;?></option>

														

														<?php

															}

														}

														

														?>

														</select>

                                                       

                                                    </div>

                                                </div>





<div class="col-md-4">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">ACTION TO BE TAKEN</label>

														<span id="actiontobe_taken" style="color:red;">*</span>

                                                        <textarea class="form-control" id="actiontobetaken" name="actiontobetaken" placeholder="" required></textarea>

                                                    </div>

                                                </div>



<div class="col-md-4">

                                                    <div class="form-group">

STOCK VALUE				<span id="actiontobe_taken" style="color:red;">*</span>

                                                       <input type="number" min="0" class="form-control" name="stockvalue" id="stockvalue" value="" required>

                                                    </div>

                                                </div>





                                                <div class="col-md-4">

												<div class="form-group">

												<label for="field-2" class="control-label">STATUS</label>

												<span id="error_status" style="color:red;"></span>

												<select class="form-control" id="status" name="status">

													<option value="">--SELECT STATUS--</option>

													<option value="1" selected>ACTIVE</option>

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

	fixedHeader: true,

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

	                    {mData:'moveto'},

	                    {mData:'dependent'},

	                    {mData:'stockvalue'},

	                   

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

var set_order = $("#set_order").val();

if(set_order=='')

{

	

	$("#setorder").html('Required!');

}



var response_type = $("#response_type").val();

if(response_type=='')

{

	

	$("#responsetype").html('Required!');

}



var actiontobetaken = $("#actiontobetaken").val();

if(actiontobetaken=='')

{

	

	$("#actiontobe_taken").html('Required!');

}



if(production_flow=='' || flowname=='' || who=='' || what=='' || how==''|| days==''|| when=='' || status=='' || set_order=='' || response_type=='' || actiontobetaken=='')

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

	

	function getfmsinfo()

{

	

	var fmsp=$("#production_flow").val();

	if(fmsp!='' || fmsp!='0')

	{

		$("#moveto").find('option').not(':first').remove();

		$.ajax({

		type:"post",

		url:"<?php echo page_url;?>Master/User_management/getfmsinformation/"+fmsp,

		data:"1",

		success:function(data){

		$("#moveto").html(data);

		}

		});

		

	}

	

}



function getdependentfms()

{

	

	var fmsp=$("#production_flow").val();

	if(fmsp!='' || fmsp!='0')

	{

		$("#dependentto").find('option').not(':first').remove();

		$.ajax({

		type:"post",

		url:"<?php echo page_url;?>Master/User_management/getfmsinformation/"+fmsp,

		data:"1",

		success:function(data){

		$("#dependentto").html(data);

		}

		});

		

	}

	

}

	



function getsortorder()

{

	

	var fmsp=$("#production_flow").val();

	if(fmsp!='' || fmsp!='0')

	{

		$("#set_order").find('option').not(':first').remove();

		$.ajax({

		type:"post",

		url:"<?php echo page_url;?>Master/User_management/getsortorder/"+fmsp,

		data:"1",

		success:function(data){

		$("#set_order").html(data);

		}

		});

		

	}

	

}

	

	

	function gettat()

{

	

	var fmsp=$("#production_flow").val();

	if(fmsp!='' || fmsp!='0')

	{

		$("#tat").find('option').not(':first').remove();

		$.ajax({

		type:"post",

		url:"<?php echo page_url;?>Master/User_management/gettat/"+fmsp,

		data:"1",

		success:function(data){

		$("#tat").html(data);

		}

		});

		

	}

	

}

</script>

<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>

</body>

</html>