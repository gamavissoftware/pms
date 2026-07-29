<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="<?php echo copyright; ?>">

		<link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

		<title><?php echo sitetitle; ?> Instruments Master</title>

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

		<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->

        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

        <!--[if lt IE 9]>

        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>

        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>

        <![endif]-->

<?PHP 

$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();

foreach($q->result() as $LOGO);

?>

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

    <style>

table.manglesh thead th {

				background: <?php echo $LOGO->colorcode;?>;

				color:#fff;

				font-weight:bold;

			}

			

		

.input-color input {

    padding-left: 20px;

}

.input-color .color-box {

    width: 10px;

    height: 10px;

    display: inline-block;

    background-color: #ccc;

    position: absolute;

    left: 5px;

    top: 5px;

}



			.backgroundcolor{ background-color:#EBEFF2 !important; }

			

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

<?php

if($this->uri->segment(4)=='1')

		{

		$asd="without fincode";

		}else

		{

		$asd="";

		}



?>

                <!-- Page-Title -->

                <div class="row">

                <div class="col-md-4"><h4 class="page-title">Prestogroup Instruments list <?php echo $asd;?></h4></div>

                

                     

		<?php

		if($this->uri->segment(3)=='2')

		{

		$Resteye=$this->db->select('id')->from('presto_instruments')->where('type','2')->where('fincode','')->get();

		?>

		<div class="col-md-4"> <a href="<?php echo page_url;?>FMS/filtered_instrument/<?php echo $this->uri->segment(3);?>/1"><button class="btn btn-warning" style="font-weight:bold;">ITEMS WITHOUT FINCODE (<?php echo $Resteye->num_rows();?>)</button></a></div>

		<?php

		}else{

			?>

			<div class="col-md-4"></div>

		<?php }

		

		?>



                 

                  <div class="col-md-4 pull-right"><button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">Add New Instrument</button></div>

				  

				   <div class="col-md-4 pull-right"><a href="<?php echo page_url;?>FMS/testronix_instruments_master"><span class="btn btn-primary">View Testronix Instruments</span></a></div>

                   

                </div>

                <!-- end page title end breadcrumb -->

	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

		<div class="row">

                    <div class="col-sm-12">

                       

                        <div class="card-box table-responsive">

                       

                            <table id="example" class="table table-bordered manglesh">

                                <thead>

                                <tr>

                                    <th>Sr No.</th>

                                    <th>Instrument Name</th>

                                    <?php

                                    if($_SESSION['logged_in']['department_id']=='1')

                                    {

                                    ?>

                                    <th>Cost Sheet</th>

                                    <?php

                                    }

                                    ?>

									<th>Opening Stock</th>

									<th>Similar Diversion</th>

									<th>Model No.</th>

									<th>File No.</th>

									<th>BOM UPDATED?</th>

									<th>Create/View BOM</th>

									<th>Alias Updated</th>

									<th>Machine Value</th>

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

 <form id="loginForm" method="post" action="<?php echo page_url;?>FMS/instruments" enctype="multipart/form-data">

  <div id="pageloader">

   <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />

</div>

                                <div class="modal-dialog modal-lg">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">Add New Instrument</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">

                                                <div class="col-md-4">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">INSTRUMENT NAME</label>

														<span id="error_instrument_name" style="color:red;"></span>

                                                        <input type="text" class="form-control" id="instrument_name" style="text-transform: uppercase;" name="instrument_name" placeholder="" required>

                                                    </div>

                                                </div>

												

												

												 <div class="col-md-4">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">MODEL NO.</label>

														<span id="error_instrument_model" style="color:red;"></span>

                                                        <input type="text" class="form-control" id="instrument_model" style="text-transform: uppercase;" name="instrument_model" placeholder="" required>

                                                    </div>

                                                </div>

												

												

												 <div class="col-md-4">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">FILE NO.</label>

														<span id="error_instrument_file" style="color:red;"></span>

                                                        <input type="text" class="form-control" id="instrument_file" style="text-transform: uppercase;" name="instrument_file" placeholder="" required>

                                                    </div>

                                                </div>

												

												 <div class="col-md-2">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">STOCK</label>

														<span id="error_instrument_stock" style="color:red;"></span>

                                                        <input type="number" min="0" class="form-control" id="instrument_stock" style="text-transform: uppercase;" name="instrument_stock" placeholder="" required>

                                                    </div>

                                                </div>

												

												<div class="col-md-2">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">MIN STOCK</label>

														<span id="error_instrument_minstock" style="color:red;"></span>

                                                        <input type="number" min="0" class="form-control" id="instrument_minstock" style="text-transform: uppercase;" name="instrument_minstock" placeholder="" required>

                                                    </div>

                                                </div>

												

												 <div class="col-md-4">

												<div class="form-group">

												<label for="field-2" class="control-label">FABRICATION REQUIRED</label><br/>

											

												<input type="checkbox" name="fabrication" id="fabrication" value="1">

												</div>

											</div>

											

												 <div class="col-md-4">

												<div class="form-group">

												<label for="field-2" class="control-label">MACHINE VALUE</label>

												<span id="error_mvalue" style="color:red;"></span>

											

												<input type="text" class="form-control" name="mvalue" id="mvalue" value="" required>

												</div>

											</div>

											

											

												 <div class="col-md-4">

												<div class="form-group">

												<label for="field-2" class="control-label">CALIBRATION COST</label>

												<span id="error_mvalue" style="color:red;"></span>

											

												<input type="number" class="form-control" name="ccost" id="ccost" value="">

												</div>

											</div>

											

											<div class="col-md-4">

												<div class="form-group">

												<label for="field-2" class="control-label">INSTRUMENTS OF</label>

												<span id="error_instruments_of" style="color:red;"></span>

											

												<select class="form-control" name="instruments_of" id="instruments_of" required>

												<option value="">Select Option</option>

												<option value="1">Presto Group</option>

												<option value="2">Testronix</option>

												</select>

												</div>

											</div>

											

											

												<div style="clear:both;height:10px"></div>

												

											   <div class="col-md-4">

												<div class="form-group">

												<label for="field-2" class="control-label">UNIT</label><br/>

												<span id="error_status" style="color:red;"></span>

												<select name="unit[]" class="select2" multiple required>

												

												<?php

												$this->db->select('*')->from('units');

												$query = $this->db->get();

												$res = $query->result();

												$i=1;

												

												if($query->num_rows()>0)

												{

												foreach($res as $row12)

												{

												

												?>

												<option value="<?php echo $row12->id;?>"><?php echo strtoupper($row12->shortname);?></option>

												<?php

												}

												}

												?>

												</select>

												</div>

											</div>





 <div class="col-md-4">

												<div class="form-group">

												<label for="field-2" class="control-label">PLATE SIZE</label>

												<span id="error_status" style="color:red;"></span>

												<select class="form-control" id="psize" name="psize" required>

													<option value="">--Select Status--</option>

													<option value="SMALL">SMALL</option>

													<option value="LARGE">LARGE</option>

													<option value="MEDIUM">MEDIUM</option>

												</select>

												</div>

											</div>



											<div class="col-md-4">

											<div class="form-group">

												<label>INSTRUMENT IMAGE</label>

												<input type="file" name="picture" id="picture" class="form-control" value="">

											</div>

											</div>

                                                <div class="col-md-4">

												<div class="form-group">

												<label for="field-2" class="control-label">STATUS</label>

												<span id="error_status" style="color:red;"></span>

												<select class="form-control" id="status" name="status">

													<option value="">--Select Status--</option>

													<option value="1">Active</option>

													<option value="0">Inactive</option>

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

        <script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>

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

	

$('#example').dataTable({

 "bProcessing": false,

 "pagination":true,

	fixedHeader: true,

	stateSave: true,

 "sAjaxSource": "<?php echo page_url;?>FMS/instruments_list/<?php echo $this->uri->segment('3');?>/<?php echo $this->uri->segment('4');?>",

 "aoColumns": [

						{ mData: 'sr_no' } ,

                        { mData: 'instruments_name' },

                        <?php

                        if($_SESSION['logged_in']['department_id']=='1')

                        {

                        ?>

                        { mData: 'costsheet' },

                        <?php

                        }

                        ?>

			{ mData: 'openingstock' },

			{ mData: 'similardiversion' },

			{ mData: 'modelno' },

			{ mData: 'fileno' },

			{ mData: 'bomupdate' },

			{ mData: 'bom' },

			{ mData: 'aliasupdate' },

			{ mData: 'mvalue' },

			{ mData: 'status' },

			{ mData: 'edit' }



			],"initComplete": function(settings, json) {



			getcolors();

			}

        });   

        

        

          $('#example').on('draw.dt', function() {

    // do action here



    getcolors();

});  



  $('#example').on('search.dt', function() {

   

    getcolors();

});  

});



</script>



<script language="javascript" type="text/javascript">   

$(document).ready(function() {

    $(".select2").select2({});

$("#save").click(function() {

var instrument_name = $("#instrument_name").val();

if(instrument_name=='')

{

	$("#error_instrument_name").html('Required!');

}

	

	

	

	var instrument_stock = $("#instrument_stock").val();

if(instrument_stock=='')

{

	$("#error_instrument_stock").html('Required!');

}

	

		var instrument_minstock = $("#instrument_minstock").val();

if(instrument_minstock=='')

{

	$("#error_instrument_minstock").html('Required!');

}

	var instruments_of = $("#instruments_of").val();

if(instruments_of=='')

{

	$("#error_instruments_of").html('Required!');

}

	

	

var status = $("#status").val();

if(status=='')

{

	

	$("#error_status").html('Required!');

}



if(instrument_name=='' || status=='' || instrument_stock=='' || instrument_minstock=='' || instruments_of=='')

{

	

	return false;

}



});

});



function getcolors()

{



 

$("#example tr").each(function(){

		

	var currentRow=$(this);

	

	    var col1_value=currentRow.find("td:eq(4)").text();

	   col1_value=col1_value.trim();

	  

		if(col1_value=='NO')

		{

			currentRow.addClass("backgroundcolor");

		} 

	



	 

});

}



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

