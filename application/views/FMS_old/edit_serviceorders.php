<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Edit Order</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
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
<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script>
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<script language="JavaScript" type="text/javascript">
$(document).ready(function(){
    $("a.delete").click(function(e){
        if(!confirm('Do you really want to delete this instrument? Once, you confirm this instrument will be deleted from planned order and FMS flow.')){
            e.preventDefault();
            return false;
        }
        return true;
    });
      $("a.copydata").click(function(e){
        if(!confirm('Do you really want to copy this record?')){
            e.preventDefault();
            return false;
        }
        return true;
    });
    
});
</script>
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
                            <h4 class="page-title">EDIT ORDER INFORMATION</h4>
							<?php echo $this->session->flashdata('message'); ?>
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
								$this->db->select('*')->from('prestogroup_orders')->where('order_id',$id);
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $row)
								
								?>
								
                                   <form method="post" action="<?php echo page_url;?>FMS/update_serviceorderinfo/<?php echo $row->order_id;?>">
										 <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">ORDER TYPE</label>
														<span id="error_order_type" style="color:red;">*</span>
                                                        <input type="text" class="form-control" id="order_type" name="order_type" style="text-transform: uppercase;" value="<?php echo $row->order_type;?>" placeholder="" required readonly>
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>SALES ZONE</label>
														<span id="error_business_loc" style="color:red;">*</span>
														<select class="form-control" id="business_loc" name="business_loc">
														
														<?php 
													$this->db->select('a.id,a.zone, b.userid')->from('saleszone a')->join('saleszoneusers b','a.id=b.zoneid','left')->where('b.userid',$row->marketing_person);
													$this->db->order_by('zone','asc');
													$query = $this->db->get();
													$res = $query->result();
													foreach($res as $rows){
													?>
													<option value="<?php echo $rows->id;?>"><?php echo strtoupper($rows->zone);?></option>
											<?php }?>			
												</select>
												
												<script type="text/javascript">
											
													$("#business_loc").change(function(){
													var business_loc=$("#business_loc").val();
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Master/User_management/fetch_users",
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
                                                        <select class="form-control select2" style="text-transform: uppercase;" name="person_name" id="person_name">
														<?php 
														$querys = $this->db->select('a.userid, b.first_name, b.last_name')->from('saleszoneusers a')->join('system_users b','a.userid=b.user_id','left')->where('a.userid',$row->marketing_person)->get();
														foreach($querys->result() as $marketingperson){
														?>
														<option value="<?php echo $marketingperson->userid;?>"><?php echo $marketingperson->first_name;?> <?php echo $marketingperson->last_name;?></option>
														<?php }?>
														</select>
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">PO NUMBNER</label>
														<span id="error_po_number" style="color:red;">*</span>
                                                        <input type="text" class="form-control" style="text-transform: uppercase;" id="po_number" name="po_number" placeholder="" value="<?php echo $row->po_number;?>" required>
                                                    </div>
                                                </div>
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">COMPANY NAME</label>
														<span id="error_company_name" style="color:red;">*</span>
                                                        <input type="text" style="text-transform: uppercase;" class="form-control" id="company_name" value="<?php echo $row->company_name;?>" name="company_name" placeholder="" required>
                                                    </div>
                                                </div>
												
									  <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">CONTACT PERSON</label>
														<span id="error_contact_person" style="color:red;">*</span>
                                                        <input type="text" style="text-transform: uppercase;" class="form-control" id="contactperson" name="contactperson" placeholder="" required value="<?php echo $row->contact_person;?>">
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">DESGINATION </label>
														
                                                        <input type="text" style="text-transform: uppercase;" class="form-control" id="designation" name="designation" placeholder="" value="<?php echo $row->designation;?>">
                                                    </div>
                                                </div>
												
												
												<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">ADDRESS WITH PINCODE</label>
														<span id="error_address" style="color:red;">*</span>
                                                        <input type="text" style="text-transform: uppercase;"  class="form-control" id="address" name="address" value="<?php echo $row->address;?>" placeholder="" required>
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">PINCODE</label>
														<span id="error_pincode" style="color:red;">*</span>
                                                        <input type="text" class="form-control" id="pincode" name="pincode" onblur="validatepincode();" placeholder="" <?php echo $row->pincode;?>>
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">EMAIL ID</label>
														<span id="error_email_id" style="color:red;"></span>
                                                        <input type="email" class="form-control" id="email_id" name="email_id" value="<?php echo $row->email;?>" placeholder="">
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">MOBILE NUMBER</label>
														<span id="error_mobile_number" style="color:red;">*</span>
                                                        <input type="number" class="form-control" id="mobile_number" name="mobile_number" value="<?php echo $row->mobile_number;?>"  placeholder="" onfocus="phoneno();" onblur="validatedigit();" maxlength="10" required minlength="10">
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
                                                    </div>
                                                </div>
												
									   
									
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">PHONE NUMBER</label>
														
                                                        <input type="number" class="form-control mob" id="phone" name="phone" pattern="\d{3}[\-]\d{3}[\-]\d{4}" placeholder="" onfocus="phoneno();" onblur="validatedigit();" value="<?php echo $row->phone;?>" maxlength="10"  minlength="10">
                                                    </div>
                                                </div>
									   
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">INTERNAL ORDER NUMBER</label>
														<span id="error_internal_order_no" style="color:red; position:absolute">*</span>
                                                        <input type="text" class="form-control" id="internal_order_no" name="internal_order_no" placeholder="" value="<?php echo $row->internal_order_no;?>" readonly>
                                                    </div>
                                                </div>
												
												<?php $query = $this->db->select('a.id as recordid, a.order_id, a.item_id,a.qty, b.id, b.instruments_name, a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('order_id',$row->order_id)->get();
			                                    foreach($query->result() as $instruments){?>
												<div class="col-md-12">
												<input type="hidden" name="recordid[]" value="<?php echo $instruments->order_id;?>">
													<div class="row">
													<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">INSTRUMENT NAME</label>
														<span id="error_instruments" style="color:red;">*</span>
                                                        <select class="form-control" style="text-transform: uppercase;" name="instrumentsdd[]" id="instruments" readonly>
															<?php 
														$query = $this->db->select('id, instruments_name, status')->from(' presto_instruments')->where('status','1')->where('id',$instruments->item_id)->get();
														foreach($query->result() as $instrument){
														?>
													  <option value="<?php echo $instrument->id;?>"><?php echo $instrument->instruments_name;?></option>
														<?php }?>
														</select>
                                                    </div>
                                                </div>
												
												<div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Quantity</label>
                                                        <input type="text" class="form-control" id="qtyfefef" name="qtyefeefe" placeholder="" value="<?php echo $instruments->qty;?>" readonly>
                                                    </div>
                                                </div>
												
												<div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">JOB CARD</label>
                                                        <input type="text" class="form-control" id="job_card" name="job_card[]" placeholder="" value="<?php echo $instruments->job_card_no;?>" readonly>
                                                    </div>
                                                </div>
												
													<div class="col-md-1">
													<div class="form-group" style="margin-top:25px">
														<a href="<?php echo page_url;?>FMS/remove_instruments/<?php echo $instruments->recordid;?>/<?php echo $this->uri->segment(3);?>" style="color:#fff;" class="delete"><button type="button" class="btn btn-danger" name="add"><i class="fa fa-trash"></i></button></a>
													</div>
												</div>
												
													</div>
												</div>
			<?php }?>
			
			<div class="col-md-12">
													<div class="row">
													<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">INSTRUMENT NAME</label>
														<span id="error_instruments" style="color:red;">*</span>
                                                        <select class="form-control select3" style="text-transform: uppercase;" name="instruments[]" id="instruments">
														<option value="">--SELECT INSTRUMENT--</option>
															<?php 
														$query = $this->db->select('id, instruments_name, status')->from(' presto_instruments')->where('status','1')->get();
														foreach($query->result() as $instruments){
														?>
													  <option value="<?php echo $instruments->id;?>"><?php echo strtoupper($instruments->instruments_name);?></option>
														<?php }?>
														</select>
                                                    </div>
                                                </div>
												<div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">QUANTITY</label>
                                                        <input type="text" class="form-control" id="qty" name="qty[]" placeholder="">
                                                    </div>
                                                </div>
												
												
													<div class="col-md-1">
													<div class="form-group" style="margin-top:25px">
														<button type="button" class="btn btn-warning" name="add" id="addmore_btn1"><i class="fa fa-plus"></i></button>
													</div>
												</div>
												
													</div>
												</div>
												<div class="col-md-12"><div id="dynamictasks1"></div></div>
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">DISCOUNT (%)</label>
                                                        <input type="number" class="form-control" id="discount" name="discount" value="<?php echo $row->discount;?>" placeholder="">
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">ORDER VALUE AFTER DISCOUNT</label>
														
                                                        <input type="text" class="form-control" id="order_value_after_discount" value="<?php echo $row->order_value_after_discount;?>" name="order_value_after_discount" placeholder="">
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">ADVANCE AMOUNT RECEIVED</label>
														
                                                        <input type="text" class="form-control" id="advance_received" value="<?php echo $row->advance_amount;?>" name="advance_received" placeholder="">
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">PAYMENT TERMS</label>
														<span id="error_payment_term" style="color:red;">*</span>
														<select class="form-control" id="payment_term" name="payment_term">
															<option value="<?php echo strtoupper($row->payment_terms);?>">-SELECT PAYMENT TERMS-</option>
															<?php 
															$query = $this->db->select('id, payment_terms')->from('presto_payment_terms')->get();
															foreach($query->result() as $paymentterms){
															?>
															<option value="<?php echo strtoupper($paymentterms->payment_terms);?>" <?php if(strtoupper($row->payment_terms)==strtoupper($paymentterms->payment_terms)){echo "selected";}?>><?php echo strtoupper($paymentterms->payment_terms);?></option>
															<?php }?>
														</select>
														 <script>
												$(function() {
													
												$('#row_paymenttype').hide(); 
												$('#payment_term').change(function(){
												if($('#payment_term').val() == 'OTHER') {
												$('#row_paymenttype').show(); 
												} else {
												$('#row_paymenttype').hide(); 
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
													<option value="1" <?php if($row->installation_charges=='1'){echo "selected";}?>>REQUIRED</option>
													<option value="0" <?php if($row->installation_charges=='0'){echo "selected";}?>>NOT REQUIRED</option>
												</select>
												</div>
											</div>
											
											<div class="col-md-3" id="row_dim">
												<div class="form-group">
												<label for="field-2" class="control-label">INSTALLATION TYPE</label>
												<span id="error_installation_charges" style="color:red; position:absolute">*</span>
												<select class="form-control" name="installation_type" id="installation_type">
													<option value="">-- SELECT OPTION--</option>
													<option value="FREE INSTALLATION" <?php if($row->installation_type=='FREE INSTALLATION'){echo "selected";}?>>-- FREE INSTALLATION-</option>
													<option value="CHARGEABLE" <?php if($row->installation_type=='CHARGEABLE'){echo "selected";}?>>-- CHARGEABLE--</option>
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
											<script>
												$(function() {
													<?php if($row->installation_charges=='0'){?>
												$('#row_dim').hide(); 
													<?php }?>
												$('#installation_charges').change(function(){
												if($('#installation_charges').val() == '1') {
												$('#row_dim').show(); 
												} else {
												$('#row_dim').hide(); 
												} 
												});
												});
											</script>
											<?php if($row->installation_charges=='1'){?>
											<div class="col-md-3" id="row_dim">
												<div class="form-group">
												<label for="field-2" class="control-label">INSTALLATION CHARGES</label>
												<span id="error_installation_charges" style="color:red; position:absolute">*</span>
												<input type="number" class="form-control" name="installation_charges_Amt" steps="0.01" id="installation_charges_Amt" value="<?php echo $row->installation_amount;?>">
												</div>
											</div><?php }?>

											<div class="col-md-3">
												<div class="form-group">
												<label for="field-2" class="control-label">PACKING TYPE</label>
												<span id="error_packing_type" style="color:red; position:absolute">*</span>
												<select class="form-control" id="packing_type" name="packing_type">
													<option value="">--SELECT  PACKING TYPE--</option>
													<option value="WOODEN" <?php if($row->packing_type=='WOODEN'){echo "selected";}?>>WOODEN</option>
													<option value="CARTON" <?php if($row->packing_type=='CARTON'){echo "selected";}?>>CARTON</option>
												</select>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
												<label for="field-2" class="control-label">PACKING CHARGES</label>
												<span id="error_packing_charges" style="color:red; position:absolute">*</span>
												<select class="form-control" id="packing_charges" name="packing_charges">
													<option value="">--SELECT  PACKING CHARGES--</option>
													<option value="1" <?php if($row->packing_charges=='1'){echo "selected";}?>>PAID BY PARTY</option>
													<option value="0" <?php if($row->packing_charges=='0'){echo "selected";}?>>INCLUSIVE</option>
												</select>
												</div>
											</div>
											<script>
												$(function() {
												<?php if($row->packing_charges=='0'){?>	
												$('#row_dim1').hide(); 
												<?php }?>
												$('#packing_charges').change(function(){
												if($('#packing_charges').val() == '1') {
												$('#row_dim1').show(); 
												} else {
												$('#row_dim1').hide(); 
												} 
												});
												});
											</script>
											<?php if($row->packing_charges=='1'){?>	 
											<div class="col-md-3" id="row_dim1">
												<div class="form-group">
												<label for="field-2" class="control-label">PACKING AMOUNT</label>
												<span id="error_packing_amount" style="color:red; position:absolute">*</span>
												<input type="number" class="form-control" name="packing_amount" id="packing_amount" steps="0.01" value="<?php echo $row->packing_amount;?>">
												</div>
											</div>
											<?php }?>
											
											<div class="col-md-3">
												<div class="form-group">
												<label for="field-2" class="control-label">FREIGHT TYPE</label>
												<span id="error_packing_charges" style="color:red; position:absolute">*</span>
												<select class="form-control" id="freight_type" name="freight_type">
													<option value="">--SELECT  FREIGHT TYPE--</option>
													<option value="1" <?php if($row->freight_type=='1'){echo "selected";}?>>TO PAY BASIS</option>
													<option value="2" <?php if($row->freight_type=='2'){echo "selected";}?>>PAID BY PRESTO</option>
													<option value="3" <?php if($row->freight_type=='3'){echo "selected";}?>>BILLED IN INVOICE</option>
													<option value="4" <?php if($row->freight_type=='4'){echo "selected";}?>>OWN PICK-UP</option>
												</select>
												</div>
											</div>
											<script>
												$(function() {
													<?php if($row->freight_type=='1' || $row->freight_type=='2'|| $row->freight_type=='4'){?>	
												$('#row_dim2').hide(); 
													<?php }?>
												$('#freight_type').change(function(){
												if($('#freight_type').val() == '3') {
												$('#row_dim2').show(); 
												} else {
												$('#row_dim2').hide(); 
												} 
												});
												});
											</script>
											<?php if($row->freight_type=='3'){?>		
											<div class="col-md-3" id="row_dim2">
												<div class="form-group">
												<label for="field-2" class="control-label">FREIGHT CHARGES</label>
												<span id="error_packing_amount" style="color:red; position:absolute">*</span>
												<input type="number" steps="0.01" class="form-control" name="freight_amount" id="freight_amount" value="<?php echo $row->freight_amount;?>">
												</div>
											</div>
											<?php }?>
											<div class="col-md-12">
												<div class="form-group">
												<label for="field-2" class="control-label">REMARKS</label>
												<textarea class="form-control" name="remarks" id="remarks"><?php echo $row->remarks;?></textarea>
												<script>CKEDITOR.replace( 'remarks' );</script>
												</div>
											</div>
										<div class="col-md-3">
											<div class="form-group">
												<label>Status</label>
												<select class="form-control" id="status" name="status">
													<option value="<?php echo $row->order_status;?>"><?php if($row->order_status=='1'){echo "Active";}else{echo "Inactive";}?></option>
													<option value="1" <?php if($row->order_status=='1'){echo "selected";}?>>Active</option>
													<option value="0" <?php if($row->order_status=='0'){echo "selected";}?>>Inactive</option>
												</select>
											</div>
										</div>
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
    var purl="<?php echo page_url;?>FMS/getmachines/";
$('.select2').select2({ });
$('.select3').select2({ 
    
    
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
var internal_order_no = $("#internal_order_no").val();
if(internal_order_no=='')
{
	
	$("#error_internal_order_no").html('Required!');
}
var instruments = $("#instruments").val();
if(instruments=='')
{
	
	$("#error_instruments").html('Required!');
}
var installation_charges= $("#installation_charges").val();
if(installation_charges=='')
{
	
	$("#error_installation_charges").html('Required!');
}
var status= $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}



if(order_type=='' || person_name=='' || po_number=='' || company_name==''|| address==''|| email_id=='' || mobile_number=='' || internal_order_no=='' || instruments=='' || payment_term=='' || installation_charges=='' || status=='')
{
	
	return false;
}

});
});
</script>
<script type="text/javascript">
$(document).ready(function(){
	


 var i=1;
 $('#addmore_btn1').click(function(){
 i++;
 
 $('#dynamictasks1').append('<div id="row'+i+'" class="row"><div class="col-md-9"><div class="form-group"><label for="field-1" class="control-label">INSTRUMENT NAME</label><span id="error_instruments" style="color:red;">*</span><select class="form-control select3'+i+'" style="text-transform: uppercase;" name="instruments[]" id="instruments'+i+'"></select></div></div><div class="col-md-2"><div class="form-group"><label for="field-1" class="control-label">QUANTITY</label><input type="text" class="form-control" id="qty" name="qty[]" placeholder=""></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:30px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div><br/>');
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
</body>
<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
</html>