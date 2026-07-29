<?php 
$q = $this->db->select('address, company_name, customer_name, email, contact_number')->from('quotation_request')->where('id',$this->uri->segment(4))->get();
if($q->num_rows()>0){
	foreach($q->result() as $tidata);
	$company_name = $tidata->company_name;
	$address = $tidata->address;
	$customer_name = $tidata->customer_name;
	$email = $tidata->email;
	$contact_number = $tidata->contact_number;
	
}else{
	$company_name="";
	$address = "";
	$customer_name = "";
	$email = "";
	$contact_number = "";
}


?>
<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="NJ Media">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title><?php echo sitetitle;?> ORDER MANAGEMENT</title>



        <!-- Table Responsive css -->

		<script src="<?php echo assets_url;?>js/angular.min.js"></script>

		 <!-- DataTables -->

        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />

		<link href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.dataTables.min.css">

        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />

		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

<link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">

		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 

        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">

		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script>

		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">

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

				font-size:11px;

				font-weight:bold;

			}

table tbody tr td {

  font-size: 11px;

  color:#000;

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



.select2-container {

width: 100% !important;

padding: 0;

}





.backgroundcolor{ background-color:#FEFDCD !important; }

</style>
<?php if($this->uri->segment(3)=='TI'){?>
<script type="text/javascript">
    $(window).on('load',function(){
        $('#con-close-modal').modal('show');
    });
</script>
<?php }?>
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

						  <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">ADD NEW ORDER</button>

                               

                            </div>

                           

                            <h4 class="page-title">ORDER LIST</h4>

                        </div>

                    </div>

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

									<th>DOCS</th>

									<th>PLAN ORDER</th>

									<th>ACTION</th>

									<th>ADDED ON</th>

										<th>ORDER DUE TO DIVERSION</th>

									   <th>ORDER TYPE</th>

									<th style="width:50%">INSTRUMENTS</th>

                                 

                                    <th>REGION MARKETING PERSON</th>

                                    <th>PO NUMBNER</th>

                                    <th>COMPANY NAME</th>

									 <th>CONTACT PERSON</th>

									 <th>DESIGNATION</th>

									<th>ADDRESS WITH PINCODE</th>

									<th>EMAIL ID</th>

									<th>MOBILE NUMBER</th>

									<th>PHONE</th>

									<th>INTERNAL ORDER NUMBER</th>

									<th>DISCOUNT</th>

									<th>ORDER VALUE AFTER DISCOUNT</th>

									<th>ADVANCE AMOUNT RECEIVED</th>

									<th>PAYMENT TERMS</th>

									<th>INSTALLATION CHARGES TYPE</th>

									<th>PACKING CHARGES</th>

									<th>FREIGHT TYPE</th>

									<th>REMARKS</th>

									<th>STATUS</th>

									

                                </tr>

                                </thead>





                                <tbody>

								

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

                <!-- end row -->

 <div id="con-close-modal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

 <form id="loginForm" method="post" action="<?php echo page_url;?>FMS/ti_order">
<input type="hidden"  name="serviceorder" value="0">
  <div id="pageloader">

   <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />

</div>

                                <div class="modal-dialog modal-full">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">ADD NEW ORDER</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">

                                                <div class="col-md-3">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">ORDER TYPE</label>

														<span id="error_order_type" style="color:red;">*</span>

                                                        <select class="form-control" name="order_type" id="order_type">

													

													<!--	<option value="">SELECT ORDER TYPE</option>--->

														<option value="SALE">SALE</option>

														<!--<option value="SERVICE">SERVICE</option>-->
														<option value="SERVICE" selected>SERVICE</option>

														

														</select>

                                                    </div>

                                                </div>

												

												<div class="col-md-3">

                                                    <div class="form-group">

                                                        <label>SALES ZONE</label>

														<span id="error_business_loc" style="color:red;">*</span>

														<select class="form-control" id="business_loc" name="business_loc">

													<option value="">--SELECT SALES ZONE--</option>

													<?php 

													$this->db->select('id,zone')->from('saleszone');

													$this->db->order_by('zone','asc');

													$query = $this->db->get();

													$res = $query->result();

													foreach($res as $row){

													    if(strtoupper($row->zone)=='SERVICE')

													    {

													        

													    }else

													    {

													?>

													<option value="<?php echo $row->id;?>"><?php echo strtoupper($row->zone);?></option>

											<?php } }?>		

												</select>

												

												<script type="text/javascript">

											

													$("#business_loc").change(function(){

													var business_loc=$("#business_loc").val();

													$.ajax({

													type:"post",

													url:"<?php echo page_url;?>Master/User_management/fetch_marketing_users",

													data:"business_loc="+business_loc,

													success:function(data){

													$("#person_name").html(data);

													}

													});

													});

											

												</script>

												

                                                    </div>

                                                </div>

												

												

												<div class="col-md-3">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">REGION MARKETING PERSON</label>

														<span id="error_person_name" style="color:red; position:absolute;">*</span>

                                                        <select class="form-control select3" style="text-transform: uppercase;" name="person_name" id="person_name">

															<option value="">--SELECT PERSON--</option>

														</select>

                                                    </div>

                                                </div>

												

												<div class="col-md-3">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">PO NUMBNER</label>

														<span id="error_po_number" style="color:red;">*</span>

                                                        <input type="text" class="form-control" style="text-transform: uppercase;" id="po_number" name="po_number" placeholder="" required>

                                                    </div>

                                                </div>

												<div class="col-md-3">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">COMPANY NAME</label>

														<span id="error_company_name" style="color:red;">*</span>

                                                        <input type="text" style="text-transform: uppercase;" class="form-control" id="company_name" name="company_name" placeholder="" value="<?php echo $company_name;?>" required>

                                                    </div>

                                                </div>

												

												<div class="col-md-3">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">CONTACT PERSON</label>

														<span id="error_contact_person" style="color:red;">*</span>

                                                        <input type="text" style="text-transform: uppercase;" class="form-control" id="contactperson" name="contactperson" placeholder="" required value="<?php echo $customer_name;?>">

                                                    </div>

                                                </div>

												

												<div class="col-md-3">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">DESGINATION </label>

														

                                                        <input type="text" style="text-transform: uppercase;" class="form-control" id="designation" name="designation" placeholder="">

                                                    </div>

                                                </div>

												

												<div class="col-md-3">

													<div class="row">

													<div class="col-md-6">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">Auto I/O Number</label>

														<span id="error_auto_generated_io" style="color:red; position:absolute">*</span>

                                                        <input type="number" class="form-control" id="auto_generated_io" name="auto_generated_io" placeholder="" value="<?php $query = $this->db->select('internal_order_no')->from('prestogroup_orders')->limit(1)->order_by('order_id','desc')->get();

														if($query->num_rows()>0){

															foreach($query->result() as $row){

																$io = $row->internal_order_no;

																echo $io+1;

															}

														}

														?>" readonly>

                                                    </div>

                                                </div>

												

												<div class="col-md-6">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">I/O Number</label>

														<span id="error_internal_order_no" style="color:red; position:absolute"></span>

                                                        <input type="number" class="form-control" id="internal_order_no" name="internal_order_no" placeholder="">

                                                    </div>

                                                </div>

													</div>

												</div>

													

												

												

												<div class="col-md-4">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">ADDRESS</label>

														<span id="error_address" style="color:red;">*</span>

                                                        <input type="text" style="text-transform: uppercase;"  class="form-control" id="address" name="address" placeholder="<?php echo $address;?>" required>

                                                    </div>

                                                </div>

												

												<div class="col-md-2">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">PINCODE</label>

														<span id="error_pincode" style="color:red;">*</span>

                                                        <input type="number" class="form-control" id="pincode" name="pincode" value="" onblur="validatepincode();" placeholder="">

                                                    </div>

                                                </div>

												

												

												<div class="col-md-3">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">EMAIL ID</label>

														<span id="error_email_id" style="color:red;"></span>

                                                        <input type="email" class="form-control" id="email_id" name="email_id" placeholder="" value="<?php echo $email;?>">

                                                    </div>

                                                </div>

												

												<div class="col-md-3">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">MOBILE NUMBER</label>

														<span id="error_mobile_number" style="color:red;">*</span>

                                                        <input type="number" class="form-control mob" id="mobile_number" name="mobile_number" pattern="\d{3}[\-]\d{3}[\-]\d{4}" placeholder="" onfocus="phoneno();" onblur="validatedigit();" maxlength="10" required minlength="10" value="<?php echo $contact_number;?>">

                                                    </div>

                                                </div>

												

												<div class="col-md-3">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">PHONE NUMBER</label>

														

                                                        <input type="number" class="form-control mob" id="phone" name="phone" pattern="\d{3}[\-]\d{3}[\-]\d{4}" placeholder="" onfocus="phoneno();" onblur="validatedigit();" maxlength="10"  minlength="10">

                                                    </div>

                                                </div>

												

												<script>





												function phoneno(){          

												$('.mob').keypress(function(e) {

												var a = [];

												var k = e.which;



												for (i = 48; i < 58; i++)

												a.push(i);



												if (!(a.indexOf(k)>=0))

												e.preventDefault();

												});

												}





												function validatedigit()

												{

												var mob=$("#mobile_number").val();



												var len=mob.length;

												if(len=='10')

												{

												return true;

												}else

												{

												alert('Mobile number should be 10 digit only');

												var mob=$("#mobile_number").val('');

												}

												}

													

												function validatepincode()

												{

												var pincode=$("#pincode").val();



												var len=pincode.length;

												if(len=='6')

												{

												return true;

												}else

												{

												alert('Pincode should be 6 digit only');

												var pincode=$("#pincode").val('');

												}

												}



												</script>

												<div class="col-md-2">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">DISCOUNT IN (%)</label>

                                                        <input type="text" class="form-control" id="discount" name="discount" placeholder="">

                                                    </div>

                                                </div>

												

												<div class="col-md-2">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">ORDER VALUE AFTER DISCOUNT</label>

                                                        <input type="text" class="form-control" id="order_value_after_discount" name="order_value_after_discount" placeholder="">

                                                    </div>

                                                </div>

												

												<div class="col-md-2">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">ADVANCE AMOUNT RECEIVED</label>

														

                                                        <input type="text" class="form-control" id="advance_received" name="advance_received" placeholder="">

                                                    </div>

                                                </div>

												<div class="col-md-12">

													<div class="row">

													<div class="col-md-9">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">INSTRUMENT NAME</label>

														<span id="error_instruments" style="color:red;">*</span>

														

                                                        <select class="form-control" id="sele" style="text-transform: uppercase;" name="instruments[]" id="instruments">

														

															

														</select>

                                                    </div>

                                                </div>

												

												<div class="col-md-2">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">QUANTITY</label>

                                                        <input type="text" class="form-control" id="qty" name="qty[]" placeholder="" required>

                                                    </div>

                                                </div>

												

													<div class="col-md-1">

													<div class="form-group" style="margin-top:25px">

														<button type="button" class="btn btn-warning" name="add" id="addmore_btn1"><i class="fa fa-plus"></i></button>

													</div>

												</div>

												

													</div>

												</div>

												

											<div class="col-md-12">

												<div id="dynamictasks1">

												

											</div>

											</div>

												

												

												

												<div class="col-md-3">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">PAYMENT TERMS</label>

														<span id="error_payment_term" style="color:red;">*</span>

														<select class="form-control" id="payment_term" name="payment_term">

															<option value="">-SELECT PAYMENT TERMS-</option>

															<?php 

															$query = $this->db->select('id, payment_terms')->from('presto_payment_terms')->get();

															foreach($query->result() as $paymentterms){

															?>

															<option value="<?php echo strtoupper($paymentterms->payment_terms);?>"><?php echo strtoupper($paymentterms->payment_terms);?></option>

															<?php }?>

														</select>

                                               <script>

												$(function() {

												$('#row_paymenttype').hide(); 

													$("#otherpaymentoption").attr('required',false);

												$('#payment_term').change(function(){

												if($('#payment_term').val() == 'OTHER') {

												$('#row_paymenttype').show(); 

													$("#otherpaymentoption").attr('required',true);

												} else {

												$('#row_paymenttype').hide(); 

													$("#otherpaymentoption").attr('required',false);

												} 

												});

												});

											</script>

                                                    </div>

                                                </div>

											<div class="col-md-3" id="row_paymenttype">

												<div class="form-group">

												<label for="field-2" class="control-label">PAYMENT TERM</label>

												<span id="error_otherpaymentoption" style="color:red; position:absolute">*</span>

												<input type="text" class="form-control" name="otherpaymentoption" id="otherpaymentoption" value="">

												</div>

											</div>

											

											<div class="col-md-3">

												<div class="form-group">

												<label for="field-2" class="control-label">INSTALLATION CHARGES TYPE</label>

												<span id="error_installation_charges" style="color:red; position:absolute">*</span>

												<select class="form-control" id="installation_charges" name="installation_charges">

													<option value="">--SELECT INSTALLATION CHARGES TYPE--</option>

													<option value="1">REQUIRED</option>

													<option value="0">NOT REQUIRED</option>

												</select>

												</div>

											</div>

											<script>

												$(function() {

												$('#row_dim').hide(); 

												$('#installation_charges').change(function(){

												if($('#installation_charges').val() == '1') {

												$('#row_dim').show(); 

												} else {

												$('#row_dim').hide(); 

												} 

												});

												});

											</script>

											<div class="col-md-3" id="row_dim">

												<div class="form-group">

												<label for="field-2" class="control-label">INSTALLATION TYPE</label>

												<span id="error_installation_charges" style="color:red; position:absolute">*</span>

												<select class="form-control" name="installation_type" id="installation_type">

													<option value="">-- SELECT OPTION--</option>

													<option value="FREE INSTALLATION">-- FREE INSTALLATION-</option>

													<option value="CHARGEABLE">-- CHARGEABLE--</option>

												</select>

												<script>

												$(function() {

												$('#row_installationscharge').hide(); 

												$('#installation_type').change(function(){

												if($('#installation_type').val() == 'CHARGEABLE') {

												$('#row_installationscharge').show(); 

												} else {

												$('#row_installationscharge').hide(); 

												} 

												});

												});

											</script>

												</div>

											</div>

											

											<div class="col-md-3" id="row_installationscharge">

												<div class="form-group">

												<label for="field-2" class="control-label">INSTALLATION CHARGES</label>

												<span id="error_installation_charges" style="color:red; position:absolute">*</span>

												<input type="text" class="form-control" name="installation_charges_Amt" id="installation_charges_Amt" value="">

												</div>

											</div>

											

											<div class="col-md-3">

												<div class="form-group">

												<label for="field-2" class="control-label">PACKING TYPE</label>

												<span id="error_packing_type" style="color:red; position:absolute">*</span>

												<select class="form-control" id="packing_type" name="packing_type">

													<option value="">--SELECT  PACKING TYPE--</option>

													<option value="WOODEN">WOODEN</option>

													<option value="CARTON">CARTON</option>

												</select>

												</div>

											</div>

											

											

											<div class="col-md-3">

												<div class="form-group">

												<label for="field-2" class="control-label">PACKING CHARGES</label>

												<span id="error_packing_charges" style="color:red; position:absolute">*</span>

												<select class="form-control" id="packing_charges" name="packing_charges">

													<option value="">--SELECT  PACKING CHARGES--</option>

													<option value="1">PAID BY PARTY</option>

													<option value="0">INCLUSIVE</option>

												</select>

												</div>

											</div>

											<script>

												$(function() {

												$('#row_dim1').hide(); 

												$('#packing_charges').change(function(){

												if($('#packing_charges').val() == '1') {

												$('#row_dim1').show(); 

												} else {

												$('#row_dim1').hide(); 

												} 

												});

												});

											</script>

											

											<div class="col-md-3" id="row_dim1">

												<div class="form-group">

												<label for="field-2" class="control-label">PACKING AMOUNT</label>

												<span id="error_packing_amount" style="color:red; position:absolute">*</span>

												<input type="text" class="form-control" name="packing_amount" id="packing_amount" value="">

												</div>

											</div>

											

											<div class="col-md-3">

												<div class="form-group">

												<label for="field-2" class="control-label">FREIGHT TYPE</label>

												<span id="error_freight_type" style="color:red; position:absolute">*</span>

												<select class="form-control" id="freight_type" name="freight_type">

													<option value="">--SELECT  FREIGHT TYPE--</option>

													<option value="1">TO PAY BASIS</option>

													<option value="2">PAID BY PRESTO</option>

													<option value="3">BILLED IN INVOICE</option>

													<option value="4">OWN PICK-UP</option>

												</select>

												</div>

											</div>

											<script>

												$(function() {

												$('#row_dim2').hide(); 

												$('#freight_type').change(function(){

												if($('#freight_type').val() == '3') {

												$('#row_dim2').show(); 

												} else {

												$('#row_dim2').hide(); 

												} 

												});

												});

											</script>

											

											<div class="col-md-3" id="row_dim2">

												<div class="form-group">

												<label for="field-2" class="control-label">FREIGHT CHARGES</label>

												<span id="error_packing_amount" style="color:red; position:absolute">*</span>

												<input type="number" steps="0.01" class="form-control" name="freight_amount" id="freight_amount" value="">

												</div>

											</div>

											 <div class="col-md-3">

												<div class="form-group">

												<label for="field-2" class="control-label">STATUS</label>

												<span id="error_status" style="color:red;"></span>

												<select class="form-control" id="status" name="status">

													<option value="1">ACTIVE</option>

													

												</select>

												</div>

											</div>

											<div class="col-md-12">

												<div class="form-group">

												<label for="field-2" class="control-label">REMARKS</label>

												<textarea class="form-control" name="remarks" id="remarks"></textarea>

												<script>CKEDITOR.replace( 'remarks' );</script>

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

        <script src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>

<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>

        <!-- Datatable init js -->

        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>



        <!-- App js -->

        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

		<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>

		

		<script type="text/javascript">

$(document).ready(function(){

	





 var i=2;

 $('#addmore_btn1').click(function(){

 i++;

 

 $('#dynamictasks1').append('<div id="row'+i+'" class="row"><div class="col-md-9"><div class="form-group"><label for="field-1" class="control-label">INSTRUMENT NAME</label><span id="error_instruments" style="color:red;">*</span><select class="form-control select3'+i+'" style="text-transform: uppercase;" name="instruments[]" id="instruments'+i+'"></select></div></div><div class="col-md-2"><div class="form-group"><label for="field-1" class="control-label">QUANTITY</label><input type="text" class="form-control" id="qty" name="qty[]" placeholder="" required></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:30px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div><br/>');

 initializeSelect2('select3'+i);

 });

 

 

 

 $(document).on('click', '.btn_remove', function(){

 var button_id = $(this).attr("id");

 $('#row'+button_id+'').remove();

 });

});

	  </script>

	  <script>

	  function initializeSelect2(selectElementObj) {

		  var purl="<?php echo page_url;?>FMS/getmachines/";

         $('.'+selectElementObj).select2({

			 

			



			    placeholder: 'TYPE TO SELECT',

minmumInputLength:4,

		allowClear: true,



        ajax: {



          url: purl,



          dataType: 'json',



          delay: 250,

			

          processResults: function (data) {



			 



            return {



              results: data



            };



          },



          cache: true



        }

			

			 

			 

			 

		 });

         

      }

	  </script>

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

	

	var ot=$("#order_type").val();

	var purl="<?php echo page_url;?>FMS/getmachines/";

$('.select2').select2({ });

$('.select3').select2({ });

$('#sele').select2({ 





			    placeholder: 'TYPE TO SELECT',

minmumInputLength:4,

		allowClear: true,



        ajax: {



          url: purl,



          dataType: 'json',



          delay: 250,



          processResults: function (data) {



			 



            return {



              results: data



            };



          },



          cache: true



        }



			 

			 

			 

			 







});

$('.select4').select2({ });

  

$('#example').dataTable({

 "bProcessing": false,

 "pagination":true,

 fixedHeader: {

            header: true

        },

   scrollCollapse: true,

   fixedColumns:   {

            leftColumns: 3

        },

        createdRow: function (row, data, dataIndex) {

    $(row).addClass('parenttr');

},

 "sAjaxSource": "<?php echo page_url;?>FMS/order_list/",

 "aoColumns": [

						{ mData: 'sr_no' } ,

	 { mData: 'generatebutton' },

						{ mData: 'planorder' },

						{ mData: 'edit' },

                        { mData: 'added_on' },

                         {mData: 'oduetodiversion'},

                          { mData: 'order_type' },

						{ mData: 'itemname' },

                      

                        { mData: 'marketing_person' },

						{ mData: 'po_number' },

						{ mData: 'company_name' },

	 { mData: 'contactperson' },

	 { mData: 'designation' },

						{ mData: 'address' },

						{ mData: 'email' },

						{ mData: 'mobile_number' },

	 { mData: 'phone' },

						{ mData: 'internal_order_no' },

						{ mData: 'discount' },

						{ mData: 'order_value_after_discount' },

						{ mData: 'advance_amount' },

						{ mData: 'payment_terms' },

						{ mData: 'installation_charges' },

						{ mData: 'packingcharges' },

						{ mData: 'freigntcharges' },

						{ mData: 'remarks' },

						{ mData: 'status' }

						

						

                ],"initComplete": function(settings, json) {

  

   getcolors()

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

$("#save").click(function() {

var order_type = $("#order_type").val();

if(order_type=='')

{

	$("#error_order_type").html('Required!');

}

var person_name = $("#person_name").val();

if(person_name=='')

{

	

	$("#error_person_name").html('Required!');

}



var po_number = $("#po_number").val();

if(po_number=='')

{

	

	$("#error_po_number").html('Required!');

}



var company_name = $("#company_name").val();

if(company_name=='')

{

	

	$("#error_company_name").html('Required!');

}

var address = $("#address").val();

if(address=='')

{

	

	$("#error_address").html('Required!');

}

var email_id = $("#email_id").val();

if(email_id=='')

{

	

	$("#error_email_id").html('Required!');

}

var mobile_number = $("#mobile_number").val();

if(mobile_number=='')

{

	

	$("#error_mobile_number").html('Required!');

}

var auto_generated_io = $("#auto_generated_io").val();

if(auto_generated_io=='')

{

	

	$("#error_internal_order_no").html('Required!');

}

var instruments = $("#instruments").val();

if(instruments=='')

{

	

	$("#error_instruments").html('Required!');

}



var payment_term= $("#payment_term").val();

if(payment_term=='')

{

	

	$("#error_payment_term").html('Required!');

}



var packing_type= $("#packing_type").val();

if(packing_type=='')

{

	

	$("#error_packing_type").html('Required!');

}

var packing_charges= $("#packing_charges").val();

if(packing_charges=='')

{

	

	$("#error_packing_charges").html('Required!');

}

var installation_charges= $("#installation_charges").val();

if(installation_charges=='')

{

	

	$("#error_installation_charges").html('Required!');

}

var freight_type= $("#freight_type").val();

if(freight_type=='')

{

	

	$("#error_freight_type").html('Required!');

}



var status= $("#status").val();

if(status=='')

{

	

	$("#error_status").html('Required!');

}





var contactperson= $("#contactperson").val();

if(contactperson=='')

{

	

	$("#error_contact_person").html('Required!');

}   







if(order_type=='' || person_name=='' || po_number=='' || company_name==''|| address==''|| email_id=='' || mobile_number=='' || internal_order_no=='' || instruments=='' || payment_term=='' || installation_charges==''|| packing_type=='' || packing_charges=='' || freight_type=='' || status=='' || contactperson=='')

{

	

	return false;

}



});

});





function getcolors()

{



 

$("#example tr.parenttr").each(function(){

		

	var currentRow=$(this);

	

	    var col1_value=currentRow.find("td:eq(6)").text();

	   col1_value=col1_value.trim();

	 

		if(col1_value=='SERVICE' || col1_value=='service' )

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