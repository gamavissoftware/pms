
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title> Add New Distributor</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" rel="stylesheet"/>
        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<style>
			.divheight{
			padding-top:110px;
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
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						
                        <h4 class="page-title text-center">Add New Distributor</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
                <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
				
               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">
<form id="loginForm" method="post" action="<?php echo page_url;?>Distributor/add_new_distributor/" enctype="multipart/form-data">
        <div id="pageloader">
        <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
        </div>	
                             <div class="row">
                                           	<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Firm Name</label>
														 <span id="error_firm_name" style="color:red;">*</span>
														 <input type="text" class="form-control" name="firm_name" id="firm_name" value="" required>
													</div>
												</div> 
												<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Owner Name</label>
														 <span id="error_owner_name" style="color:red;">*</span>
														 <input type="text" class="form-control" name="owner_name" id="owner_name" value="" required>
													</div>
												</div> 
												<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">Office Address</label>
														 <span id="error_office_address" style="color:red;">*</span>
														 <input type="text" class="form-control" name="office_address" id="office_address" value="" required>
													</div>
												</div> 
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">City</label>
														 <span id="error_city" style="color:red;">*</span>
														 <input type="text" class="form-control" name="city" id="city" value="" required>
													</div>
												</div> 
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">District</label>
														 <span id="error_district" style="color:red;">*</span>
														 <input type="text" class="form-control" name="district" id="district" value="" required>
													</div>
												</div> 
													<div class="col-sm-2">
													<div class="form-group">
													<label for="field-2" class="control-label">State</label>
													<span style="color:red;"></span>
													<select class="form-control" name="state" id="state" required>
													<option value="">Select State</option>
													<?php 
													$q = $this->db->select('state_id, state_name')->from('states')->where('country_id',101)->get();
													foreach($q->result() as $rowssss){
													?>
													<option value="<?php echo $rowssss->state_name;?>"><?php echo $rowssss->state_name;?></option>
													<?php }?>
													</select>

													</div>
													</div>

											
													<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Pincode</label>
														 <span id="error_pincode" style="color:red;">*</span>
														 <input type="text" class="form-control" name="pincode" id="pincode" value="" required>
													</div>
												</div> 

												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Phone no</label>
														 <span id="error_phone_no" style="color:red;">*</span>
														 <input type="text" class="form-control" name="phone_no" id="phone_no" value="" required>
													</div>
												</div> 

												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Mobile no</label>
														 <span id="error_mobile_no" style="color:red;">*</span>
														 <input type="text" class="form-control" name="mobile_no" id="mobile_no" value="" required>
													</div>
												</div> 

												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Email ID</label>
														 <span id="error_emailid" style="color:red;">*</span>
														 <input type="text" class="form-control" name="emailid" id="emailid" value="" required>
													</div>
												</div>
												<div class="col-md-12">
													<div class="form-group">
														 <label for="field-2" class="control-label">Delivery Address</label>
														 <span id="error_delivery_address" style="color:red;">*</span>
														<textarea class="form-control" name="delivery_address" id="delivery_address"></textarea>
													</div>
												</div>

												<div class="col-md-2">
													<div class="form-group">
														<label>Warehouse Person Name</label>
														 <span id="error_warehouse_contact_person" style="color:red;">*</span>
														<input type="text" class="form-control" name="warehouse_contact_person" id="warehouse_contact_person" value="">
													</div>
												</div>

												<div class="col-md-2">
													<div class="form-group">
														<label>Contact No</label>
														 <span id="error_warehouse_contact_person_no" style="color:red;">*</span>
														<input type="text" class="form-control" name="warehouse_contact_person_no" id="warehouse_contact_person_no" value="">
													</div>
												</div>

												<div class="col-md-4">
													<div class="form-group">
														<label>GSTN</label>
														 <span id="error_gstn" style="color:red;">*</span>
														<input type="text" class="form-control" name="gstn" id="gstn" value="">
													</div>
												</div>

												<div class="col-md-2">
													<div class="form-group">
														<label>Firm Type</label>
														 <span id="error_firm_type" style="color:red;">*</span>
														<select class="form-control" name="firm_type" id="firm_type">
															<option value="1">Proprietorship</option>
															<option value="2">Partnership</option>
															<option value="3">Pvt. Ltd.</option>
															<option value="4">Ltd.</option>
															<option value="5">Individual</option>
														</select>
													</div>
												</div>

													<div class="col-md-2">
													<div class="form-group">
														<label>Business Type</label>
														 <span id="error_business_type" style="color:red;">*</span>
														<select class="form-control" name="business_type" id="business_type">
															<option value="1">Agency</option>
															<option value="2">Wholesale</option>
															<option value="3">Retail</option>
															<option value="4">Other</option>
															
														</select>
													</div>
												</div>

												<div class="col-md-12">
													<div class="form-group">
														<label>Do you have any product/Company's Agency or Distributor?</label>
														 <span id="error_doyouhaveanyotherproduct" style="color:red;">*</span>
														<select class="form-control" name="doyouhaveanyotherproduct" id="doyouhaveanyotherproduct" onchange="doyouhaveanyotherproducts();">
															<option value="">Select Option</option>
															<option value="1">Yes</option>
															<option value="2">No</option>
														
															
														</select>
													</div>
												</div>
											</div>
											<script type="text/javascript">
												function doyouhaveanyotherproducts() {
													var doyouhaveanyotherproduct = $("#doyouhaveanyotherproduct").val();
													if(doyouhaveanyotherproduct==1){
														$("#productsection").show();
													}else{
														$("#productsection").hide();
													}
												}
											</script>

												<div class="row" style="display: none;" id="productsection">
												<div class="col-md-2">
													<div class="form-group">
														<label>Company Name</label> 
														<span style="color:red;">*</span>
														<input type="text" class="form-control" name="company_name[]" id="company_name" value="">
														
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 
														 <label>Product Name</label> 
														 <span style="color:red;">*</span>
														<input type="text" class="form-control" name="product_name[]" id="product_name" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														 <label>Area</label> 
														 <span style="color:red;">*</span>
														<input type="text" class="form-control" name="area[]" id="area" value="">
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group">
														 <label>Monthly Turnover (Approx in Lakh)</label> 
														 <span style="color:red;">*</span>
														<input type="text" class="form-control" name="turnover[]" id="turnover" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														 <label>Distributior Since?</label> 
														 <span style="color:red;">*</span>
														<input type="text" class="form-control" name="distributor_since[]" id="distributor_since" value="">
													</div>
												</div>
												<div class="col-md-1">
												<div class="form-group" style="padding-top:20px">
													<button type="button" class="btn btn-warning" name="add" id="addmore_btn1"><i class="fa fa-plus"></i></button>
												</div>
												
												</div>
												</div>
												<div id="dynamictasks1"></div>
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label>Other Experience Information</label>
															<textarea class="form-control" name="other_experience" id="other_experience"></textarea>
														</div>
													</div>

													<div class="col-md-6">
														<div class="form-group">
															<label>Business Related work Area</label>
															<textarea class="form-control" name="business_related_area" id="business_related_area"></textarea>
														</div>
													</div>

													<div class="col-md-2">
														<div class="form-group">
															<label>Useful?</label>
															<select class="form-control" name="useful" id="useful">
																<option value="1">Yes</option>
																<option value="0">No</option>
															</select>
														</div>
													</div>
												<div class="col-md-10">
											<div class="form-group pull-right">
												<input type="submit" id="save" class="btn btn-info" value="Submit">
											</div>												
											</div>

												</div>
												
                             
							 
							 
							 </form>           
										
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
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script language="javascript" type="text/javascript">   
$(document).ready(function() {
$("#save").click(function() {
	var firm_name= $("#firm_name").val();
if(firm_name=='')
{
	$("#error_firm_name").html('Required!');
}
var owner_name = $("#owner_name").val();
if(owner_name=='')
{
	$("#error_owner_name").html('Required!');
}
var office_address = $("#office_address").val();
if(office_address=='')
{
	$("#error_office_address").html('Required!');
}


var city = $("#city").val();
if(city=='')
{
	$("#error_city").html('Required!');
}


var district = $("#district").val();
if(district=='')
{
	
	$("#error_district").html('Required!');
}

var state = $("#state").val();
if(state=='')
{
	$("#error_state").html('Required!');
}

var phone_no = $("#phone_no").val();
if(phone_no=='')
{
	$("#error_phone_no").html('Required!');
}

var mobile_no = $("#mobile_no").val();
if(mobile_no=='')
{
	$("#error_mobile_no").html('Required!');
}
var emailid = $("#emailid").val();
if(emailid=='')
{
	$("#error_emailid").html('Required!');
}

var delivery_address = $("#delivery_address").val();
if(delivery_address=='')
{
	$("#error_delivery_address").html('Required!');
}

var warehouse_contact_person = $("#warehouse_contact_person").val();
if(warehouse_contact_person=='')
{
	$("#error_warehouse_contact_person").html('Required!');
}

var warehouse_contact_person_no = $("#warehouse_contact_person_no").val();
if(warehouse_contact_person_no=='')
{
	$("#error_warehouse_contact_person_no").html('Required!');
}

var gstn = $("#gstn").val();
if(gstn=='')
{
	$("#error_gstn").html('Required!');
}

var firm_type = $("#firm_type").val();
if(firm_type=='')
{
	$("#error_firm_type").html('Required!');
}

var business_type = $("#business_type").val();
if(business_type=='')
{
	$("#error_business_type").html('Required!');
}

var doyouhaveanyotherproduct = $("#doyouhaveanyotherproduct").val();
if(doyouhaveanyotherproduct=='')
{
	$("#error_doyouhaveanyotherproduct").html('Required!');
}



if(firm_name==''|| owner_name=='' || office_address=='' || city=='' || district=='' || state=='' || phone_no=='' || mobile_no=='' || emailid=='' || delivery_address=='' || warehouse_contact_person=='' || warehouse_contact_person_no=='' || gstn=='' || firm_type=='' || business_type=='' || doyouhaveanyotherproduct=='' )
{
	
	return false;
}

});
});
</script>
 <script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
 <script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready
</script>


            <script type="text/javascript">
$(document).ready(function(){
 var i=1;
 $('#addmore_btn1').click(function(){
 i++;
 
 $('#dynamictasks1').append('<div id="row'+i+'" class="row"><div class="col-md-2"><div class="form-group"><label>Company Name</label><input type="text" class="form-control" name="company_name[]" id="company_name" value=""></div></div><div class="col-md-2"><div class="form-group"><label>Product Name</label><input type="text" class="form-control" name="product_name[]" id="product_name" value=""></div></div><div class="col-md-2"><div class="form-group"><label>Area</label><input type="text" class="form-control" name="area[]" id="area" value=""></div></div><div class="col-md-3"><div class="form-group"><label>Monthly Turnover (Approx in Lakh)</label><input type="text" class="form-control" name="turnover[]" id="turnover" value=""></div></div><div class="col-md-2"><div class="form-group"><label>Distributior Since?</label><input type="text" class="form-control" name="distributor_since[]" id="distributor_since" value=""></div></div><div class="col-md-1"><div class="form-group" style="padding-top:20px"><button type="button" class="btn btn-warning" name="add" id="addmore_btn1"><i class="fa fa-plus"></i></button></div></div></div><br/>');
 
 });
 
 
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id+'').remove();
 });
 
});
	  </script>
    </body>
</html>