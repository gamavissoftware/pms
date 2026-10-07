<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="<?php echo copyright;?>">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title><?php echo sitetitle;?> Business Locations List</title>



        <!-- Table Responsive css -->

		<script src="<?php echo assets_url;?>js/angular.min.js"></script>

		 <!-- DataTables -->

        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>



        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />



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



table.pretty thead th {



background: <?php echo $LOGO->colorcode;?>;



color:#fff;



font-weight:bold;



text-align:center;



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

            <div class="container">



                <!-- Page-Title -->

                <div class="row" style="margin-top:20px;">

                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">

                        <div class="page-title-box">

						 <div class="btn-group pull-right">

						 <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">ADD NEW BUSINESS LOCATION</button>

                               

                            </div>

                           

                            <h4 class="page-title">BUSINESS LOCATION LIST</h4>

                        </div>

                    </div>

                </div>

                <!-- end page title end breadcrumb -->

<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>



                 <div class="row">

                    <div class="col-sm-12">

                        <div class="card-box table-responsive">

                            <table id="example" class="table table-striped table-bordered pretty">

                                <thead>

                                <tr>

                                    <th>Sr No.</th>

									 <th>Country</th>

                                    <th>State</th>

                                    <th>City</th>

                                    <th>Company Name</th>

                                    <th>Address</th>

                                    <th>Contact Number</th>

									<th>Status</th>

                                    <th>Action</th>

                                </tr>

                                </thead>

                                

                            </table>

                        </div>

                    </div>

                </div>

                <!-- end row -->

 <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

 <form id="loginForm" method="post" action="<?php echo page_url;?>Master/Business_location/add">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">Add New Business Location</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">

                                                <div class="col-md-6">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">Country</label>

														<span id="error_name" style="color:red;"></span>

												<select class="form-control" id="country_name" name="country_name">

													<option value="">--Select Country--</option>

													<?php 

													$this->db->select('*')->from('countries')->where('country_status','1');

													$this->db->order_by('country_name');

													$query = $this->db->get();

													$res = $query->result();

													foreach($res as $row){

													?>

													<option value="<?php echo $row->country_id;?>"><?php echo $row->country_name;?></option>

											<?php }?>		

												</select>

												

												 <script type="text/javascript">

											

													$("#country_name").change(function(){

													var country_name=$("#country_name").val();

													$.ajax({

													type:"post",

													url:"<?php echo page_url;?>Master/Business_location/select_state",

													data:"country_name="+country_name,

													success:function(data){

													$("#state").html(data);

													}

													});

													});

											

												</script>

												

                                                    </div>

                                                </div>

                                                <div class="col-md-6">

                                                    <div class="form-group">

                                                        <label for="field-2" class="control-label">State </label>

														<span id="error_state" style="color:red;"></span>

                                                        <select class="form-control" name="state" id="state">

														<option value="">--Select State--</option>

														</select>

														

												<script type="text/javascript">

											

													$("#state").change(function(){

													var state=$("#state").val();

													$.ajax({

													type:"post",

													url:"<?php echo page_url;?>Master/Business_location/select_city",

													data:"state="+state,

													success:function(data){

													$("#city_name").html(data);

													}

													});

													});

											

												</script>

                                                    </div>

                                                </div>

												

												 <div class="col-md-6">

                                                    <div class="form-group">

                                                        <label for="field-2" class="control-label">City </label>

														<span id="error_city" style="color:red;"></span>

														<select class="form-control" name="city_name" id="city_name">

														<option value="">--Select City--</option>

														</select>

                                                    </div>

                                                </div>

												

												<div class="col-md-6">

													<div class="form-group">

														 <label for="field-2" class="control-label">Company Name </label>

														 <span id="error_company" style="color:red;"></span>

														 <input type="text" class="form-control" name="company_name" id="company_name" placeholder="Company Name" value="">

													</div>

												</div>

												<div class="col-md-12">

												<div class="form-group">

														 <label for="field-2" class="control-label">Address </label>

														 <Span id="address_error" style="color:red;"></span>

										<textarea class="form-control" name="address" id="address" placeholder="Address"></textarea>
										
													</div>

												</div>

												<div class="col-md-6">

													<div class="form-group">

														 <label for="field-2" class="control-label">Contact Number </label>

														  <Span id="contact_error" style="color:red;"></span>

														 <input type="text" class="form-control" name="contact_number" id="contact_number" placeholder="Contact Number" value="">

													</div>

												</div>

												

												<div class="col-md-6">

												<div class="form-group">

												<label for="field-2" class="control-label">Status</label>

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

                                            <input type="submit" id="businesssave" class="btn btn-info" value="Submit"> 

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



        <!-- Datatable init js -->

        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>



        <!-- App js -->

        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

<script>

$(document).ready(function(){

	   $("#businesssave").attr('disabled',false);

	   $("#businesssave").val('submit');

  $("#loginForm").on("submit", function(){

   // $("#pageloader").fadeIn();

   $("#businesssave").attr('disabled',true);

     $("#businesssave").val('Please Wait...');

  });//submit

});//document ready

</script>

       



        <script>

$( document ).ready(function() {

$('#example').dataTable({

"bProcessing": true,

"pagination":true,

"sAjaxSource": "<?php echo page_url;?>Master/Business_location/business_loc_listing",

"aoColumns": [

				{ mData: 'sr_no' } ,

				{ mData: 'country_name' },

				{ mData: 'state_name' },

				{ mData: 'city_name' },

				{ mData: 'company_name' },

				{ mData: 'address' },

				{ mData: 'contact_number' },

				{ mData: 'status' },

				{ mData: 'edit' }

				

		]

});   

});



</script>

		

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<script language="javascript" type="text/javascript">   

jQuery.noConflict();

$(document).ready(function() {

$("#businesssave").click(function() {

// Clear last attempt's messages, otherwise a field the user has

// since filled in keeps showing "Required!".

$("#error_name, #error_state, #error_status, #error_city, #error_company, #address_error, #contact_error").html('');


var country_name = $("#country_name").val();

if(country_name=='')

{

	$("#error_name").html('Required!');

}

var state = $("#state").val();

if(state=='')

{

	

	$("#error_state").html('Required!');

}



var status = $("#status").val();

if(status=='')

{

	

	$("#error_status").html('Required!');

}



var city_name = $("#city_name").val();

if(city_name=='')

{

	

	$("#error_city").html('Required!');

}



var company_name = $("#company_name").val();

if(company_name=='')

{

	

	$("#error_company").html('Required!');

}



var address = $("#address").val();

if(address=='')

{

	

	$("#address_error").html('Required!');

}



var contact_number = $("#contact_number").val();

if(contact_number=='')

{

	

	$("#contact_error").html('Required!');

}



if(country_name=='' || state=='' || city_name=='' || company_name=='' || address=='' || contact_number=='' || status=='' )

{

	

	return false;

}



});

});

</script>

    </body>

</html>