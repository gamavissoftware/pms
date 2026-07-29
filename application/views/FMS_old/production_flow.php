<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="<?php echo copyright; ?>">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title><?php echo sitetitle; ?> Production flow</title>



        <!-- Table Responsive css -->

		<script src="<?php echo assets_url;?>js/angular.min.js"></script>

		 <!-- DataTables -->

        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />

		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

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

  left: 50%;

  margin-left: -32px;

  margin-top: -32px;

  position: absolute;

  top: 50%;

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

            <div class="container-fluid">



                <!-- Page-Title -->

                <div class="row">

                    <div class="col-sm-12">

                        <div class="page-title-box">

						 <div class="btn-group pull-right" style="margin-top:30px">

						  <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">ADD PRODUCTION FLOW</button>

                               

                            </div>

                           

                            <h4 class="page-title">PRODUCTION FLOW LIST</h4>

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

                                    <th>Sr No.</th>

                                    <th>FLOW NAME</th>

									<th>PROCESS ORDER</th>

									<th>Status</th>

                                    <th>Action</th>

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

 <form id="loginForm" method="post" action="<?php echo page_url;?>FMS/production_plan_flow">

  <div id="pageloader">

   <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />

</div>

                                <div class="modal-dialog modal-lg">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">ADD NEW PRODUCTION FLOW</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">

                                                <div class="col-md-4">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">FLOW NAME</label>

														<span id="error_flow_name" style="color:red;"></span>

                                                        <input type="text" class="form-control" id="flow_name" style="text-transform: uppercase;" name="flow_name" placeholder="" value="" required>

                                                    </div>

                                                </div>

												

												<div class="col-md-4">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">FLOW ORDER</label>

														<span id="error_flow_order" style="color:red;"></span>

                                                        <input type="number" class="form-control" id="flow_order" min="1" style="text-transform: uppercase;" name="flow_order" placeholder="" value="" required>

                                                    </div>

                                                </div> 

												

												<div class="col-md-4">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">PARALLEL FMS APPL?</label><br/>

														<span id="" style="color:red;"></span>

                                                        <input type="checkbox" id="parfms" name="parfms" value="1">

                                                    </div>

                                                </div> 

											<div style="clear:both;height:10px"></div>

												<div class="col-md-4">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">INCLUDE IN LONG REPORT?</label><br/>

														<span id="" style="color:red;"></span>

                                                        <input type="checkbox" id="longreport" name="longreport" value="1">

                                                    </div>

                                                </div> 

												

												

												<div class="col-md-4">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">INCLUDE IN MIS?</label><br/>

														<span id="" style="color:red;"></span>

                                                        <input type="checkbox" id="includemis" name="includemis" value="1">

                                                    </div>

                                                </div> 

                                                <div class="col-md-4">

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

$( document ).ready(function() {

	

$('#example').dataTable({

 "bProcessing": false,

	fixedHeader: true,

 "pagination":true,

 "sAjaxSource": "<?php echo page_url;?>FMS/production_flow_list/",

 "aoColumns": [

						{ mData: 'sr_no' } ,

                        { mData: 'production_flow' },

						{ mData: 'sort_order' },

						{ mData: 'status' },

						{ mData: 'edit' }

						

                ]

        });   

});



</script>



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

});

</script>

<script>

$(document).ready(function(){

  $("#loginForm").on("submit", function(){

    $("#pageloader").fadeIn();

  });//submit

});//document ready

</script>

</body>

</html>