<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Prestogroup</title>

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
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right">
						  
                               
                            </div>
                           
                            <h4 class="page-title text-center">Engineer Progress update Form</h4><hr>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">
								<?php 
								$query = $this->db->select('a.*, b.first_name, b.last_name')->from('engineer_visit a')->join('system_users b','a.user_id=b.user_id','left')->where('a.id',$this->uri->segment(3))->get();
								foreach($query->result() as $row);
								?>
								
                                   <form method="post" action="<?php echo page_url;?>Sales/engineer_progress_hod/<?php echo $this->uri->segment(3);?>" enctype="multipart/form-data">
										  <div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">YOUR NAME</label>
														 <span id="error_machine" style="color:red;">*</span>
														 <input type="text" class="form-control" name="yourname" id="yourname" value="<?php echo $row->first_name;?> <?php echo $row->last_name;?>" readonly>
													</div>
												</div>
												
												 <div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Company Name</label>
														 <span id="error_company_name" style="color:red;">*</span>
														<input type="text" class="form-control" name="company_name" id="company_name" value="<?php echo $row->company_name;?>" readonly>
													</div>
												</div>
												 <div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Sales Force No</label>
														 <span id="error_sales_force" style="color:red;">*</span>
														 <input type="text" class="form-control" name="sales_force" id="sales_force" value="<?php echo $row->sale_force_no;?>" readonly>
													</div>
												</div>
											
											 <div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Date of Visit</label>
														 <span id="error_sales_force" style="color:red;">*</span>
														 <input type="date" class="form-control" name="visit_date" id="visit_date" value="<?php echo $row->visit_date;?>" readonly>
													</div>
												</div>	
												<div class="col-md-2">
													<div class="form-group">
														<label>Warranty Status</label>
														<span id="error_warrenty_status" style="color:red;">*</span>
														<select class="form-control" name="warrenty_status" id="warrenty_status" readonly required>
													<?php if($row->warrenty_status=='Under Warranty'){	  ?>
														    <option value="1" <?php if($row->warrenty_status=='Under Warranty'){echo "selected";}?>>Under Warranty</option>
														    <?php }?>
														    <?php if($row->warrenty_status=='Warranty Expired'){?>
														    <option value="2" <?php if($row->warrenty_status=='Warranty Expired'){echo "selected";}?>>Warranty Expired</option>
														    <?php }?>
														</select>
													
													</div>
												</div>
											
											
												<div class="col-md-2" id="chargabledetail" <?php if($row->warrenty_status=='Warranty Expire'){}else{?> style="display:none"<?php }?>>
											    <div class="form-group">
											        <label>Chargable <span id="error_bill_number" style="color:red;">*</span></label>
											       <select class="form-control" name="chargable" id="chargable" readonly>
											           <option value="">Select Option</option>
											           <option value="1" <?php if($row->chargable=='1'){echo "selected";}?>>Yes</option>
											           <option value="2"  <?php if($row->chargable=='2'){echo "selected";}?>>No</option>
											       </select>
											    </div>
											</div>
											
											 
											
											<div class="col-md-2" id="pricedetail" <?php if($row->chargable=='1'){}else{?> style="display:none"<?php }?>>
											    <div class="form-group">
											        <label>Charges <span id="error_bill_number" style="color:red;">*</span></label>
											        <input type="number" class="form-control" name="charges" id="charges" value="<?php echo $row->charges;?>" step="0.01" readonly>
											    </div>
											</div>
											
												<div class="col-md-2" id="billdetail" <?php if($row->chargable=='1'){}else{?> style="display:none"<?php }?>>
											    <div class="form-group">
											        <label>Bill Number <span id="error_bill_number" style="color:red;">*</span></label>
											        <input type="text" class="form-control" name="bill_number" id="bill_number" value="<?php echo $row->bill_number;?>" readonly>
											    </div>
											</div>
											
												<div class="col-md-2" id="payment_collect" <?php if($row->chargable=='1'){}else{?> style="display:none"<?php }?>>
											    <div class="form-group">
											        <label>Payment to Collect <span id="error_bill_number" style="color:red;">*</span></label>
											       <select class="form-control" name="payment_to_collect" id="payment_to_collect" readonly>
											           <option value="1" <?php if($row->payment_to_collect=='1'){echo "selected";}?>>Yes</option>
											           <option value="0" <?php if($row->payment_to_collect=='0'){echo "selected";}?>>No</option>
											       </select>
											    </div>
											</div>
												
												<div class="col-md-4">
													<div class="form-group">
														<label>Nature of Complaints</label>
														<span id="error_nature_of_complaints" style="color:red;">*</span>
														<input type="text" class="form-control" name="nature_of_complaints" id="nature_of_complaints" value="<?php echo $row->nature_of_complaints;?>" readonly>
													</div>
												</div>
												
													<div class="col-md-2">
													<div class="form-group">
														<label>ENGINEER</label>
														<span id="error_nature_of_complaints" style="color:red;">*</span>
													<select class="form-control" name="engineer" id="engineer" readonly>
													   
													    <?php 
													    $query = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('user_status','1')->where('hide_profile','0')->where('user_id',$row->engineer)->get();
													    foreach($query->result() as $row1){
													    ?>
													    <option value="<?php echo $row1->user_id;?>" <?php if($row->engineer==$row1->user_id){echo "selected";}?>><?php echo $row1->first_name." ".$row1->last_name;?></option>
													    <?php }?>
													</select>
													</div>
												</div>
													<div class="col-md-6">
													<div class="form-group">
														<label>Actual Observation of Engineer</label>
														<span id="error_service_visit_report" style="color:red;">*</span>
														<input type="text" class="form-control" name="actual_ovservation" id="actual_ovservation" value="<?php echo $row->observation_of_engineer;?>">
														
													</div>
												</div>
													<div class="col-md-2">
													<div class="form-group">
														<label>Spare Parts</label>
														<span id="error_service_visit_report" style="color:red;">*</span>
													<select class="form-control" name="spare_parts" id="spare_parts">
													    <option>Select Option</option>
													    <option value="1" <?php if($row->spares_parts=='1'){echo "selected";}?>>Yes</option>
													    <option value="0" <?php if($row->spares_parts=='0'){echo "selected";}?>>No</option>
													</select>
													
													 <script>
												$(function() {
												 <?php if($row->spares_parts=='1'){
												     
												 }else{?>   
												$('#hidedetail').hide(); 
												$('#hidepicture').hide(); 
												<?php }?>
												$('#spare_parts').change(function(){
												if($('#spare_parts').val() == '1') {
												$('#hidedetail').show(); 
												$('#hidepicture').show();
												
												} else {
												$('#hidedetail').hide(); 
												$('#hidepicture').hide(); 
												
												} 
												});
												});
											</script>
														
													</div>
												</div>
												
												<div class="col-md-2" id="hidedetail" <?php if($row->spares_parts=='1'){}else{?>style="display:none" <?php }?>>
											    <div class="form-group">
											        <label>Parts Name</label>
											        <input type="" class="form-control" name="parts_name" id="parts_name" value="<?php echo $row->part_name;?>">
											    </div>
											</div>
											
												<div class="col-md-2" id="hidepicture" <?php if($row->spares_parts=='1'){}else{?>style="display:none" <?php }?>>
													<div class="form-group">
														<label>Picture</label>
														<span id="error_picture" style="color:red;">*</span>
														<input type="file" class="form-control" name="picture" id="picture" value="">
													</div>
												</div>
												
													<div class="col-md-2" style="display:none">
													<div class="form-group">
														<label>Service Visit Report</label>
														<span id="error_service_visit_report" style="color:red;">*</span>
														<input type="file" class="form-control" name="service_visit_report" id="service_visit_report" value="">
														
														
													</div>
												</div>
												
													<div class="col-md-2">
											    <div class="form-group">
											        <label>Payment Collected <span id="error_payment_collected" style="color:red;">*</span></label>
											       <select class="form-control" name="payment_collected" id="payment_collected">
											           <option value="1">Yes</option>
											           <option value="0">No</option>
											       </select>
											    </div>
											</div>
												
												<script>
												$(function() {
												   
												$('#remarkbox').hide(); 
											
											$('#payment_collected').change(function(){
												if($('#payment_collected').val() == '0') {
												$("#remarkbox").show();
												
												$("#charges").attr("required", true);
												
												} else {
												$('#remarkbox').hide(); 
												
												
												} 
												});
												});
											</script>
											
											<div class="col-md-4" id="remarkbox" style="display:none">
											    <div class="form-group">
											        <label>Remarks <span style="color:red">*</span></label>
											        <input type="text" class="form-control" name="remarks" id="remarks" value="">
											    </div>
											</div>
												<div class="col-md-2">
											    <div class="form-group">
											        <label>Next Action <span id="error_payment_collected" style="color:red;">*</span></label>
											       <select class="form-control" name="next_action" id="next_action">
											           <option value="Payment follow-up">Payment follow-up</option>
											           <option value="Send Quotation">Send Quotation</option>
											           <option value="Revisit">Revisit</option>
											           <option value="Phone Call">Phone Call</option>
											           <option value="Recommended Machine send to Factory">Recommended Machine send to Factory</option>
											       </select>
											    </div>
											</div>
											
											
										
											
											<script>
											    
											    function checkforvisittype()
											    {
											        var vtype=$("#visittype").val();
											        
											       if(vtype==1)
											       {
											           
											           $(".localtour").css('display','');
											           
											           $("#mode").attr('required',true);
											           $(".localtour :input").attr('required',true);
											       }else if(vtype==2)
											       {
											             $(".localtour").css('display','none');
											              $(".localtour :input").attr('required',false);
											           
											           $("#mode").attr('required',false);
											           
											       }else
											       {
											           
											             $(".localtour").css('display','none');
											              $(".localtour :input").attr('required',false);
											           
											           $("#mode").attr('required',false);
											           
											           
											       }
											        
											        
											        
											    }
											    
											    
											</script>
												<div class="col-md-2 localtour" style="display:none;">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">MODE</label>
														<span id="error_mode" style="color:red;">*</span>
                                                         <select class="form-control" name="mode" id="mode">
															<option value="">SELECT MODE</option>
															<option value="1">PUBLIC CONVEYANCE</option>
															<option value="2">OWN</option>
														 </select>
                                                    </div>
                                                </div>
												
												<script>
												$(function() {
												$('#row_dim').hide(); 
												$('#conveyance_amt').hide();
												$('#mode').change(function(){
												if($('#mode').val() == '2') {
												$('#row_dim').show();
												$('#expensereport').hide();
												$('#conveyance_amt').show();
												$('.ownconv').attr('required',true);
												$('.expensetype').attr('required',false);
												
												} else {
												$('#row_dim').hide(); 
												$('#expensereport').show();
												$('#conveyance_amt').hide();
												$('.expensetype').attr('required',true);
												$('.ownconv').attr('required',false);
												} 
												});
												});
											</script>
											
											<div class="row" style="display:none" id="expensereport">	
											
											<div class="col-md-12">
												
												
												<div class="col-md-3">
													<div class="form-group">
														<label>TRAVEL EXPENSE</label>
														<select class="form-control expensetype" name="travel_expense[]" id="travel_expense" onChange="fetch_expense_options(0);">
														<option value="">SELECT EXPENSE TYPE</option>
														<?php 
														$query =$this->db->select('id, options')->from('conveyance_type_options')->where('conveyance_type_id','2')->where('status','1')->get();
														foreach($query->result() as $row2){
														?>
														<option value="<?php echo $row2->id;?>"><?php echo $row2->options;?></option>
														<?php }?>
														</select>
														 
													</div>
												</div>
												
												<div class="col-md-2">
												<div class="form-group">
												<label>AMOUNT</label>
													<input type="number" class="form-control" name="travel_expense_amount[]" id="travel_expense_amount" value="" step="0.2">
													</div>
												</div>
												<div class="col-md-1">
												<div class="form-group" style="margin-top:25px">
														<button type="button" class="btn btn-warning" name="add" id="addmore_btn"><i class="fa fa-plus"></i></button>
													</div>
												</div>
											</div>
											</div>
												
												
											<div id="dynamictasks"></div>
												
													<div id="row_dim">
										
											    
											    <div class="col-md-2 localtour">
													<div class="form-group">
												<label for="field-2" class="control-label">Vehicle Type</label>
											<select name="vtype" id="vtype" class="form-control ownconv" onchange="getrate(this.value);">
											    <option value="">Select Vehicle</option>
											    <?php
											    $vtypes=$this->db->select('id,type')->from('conveyance_vehicle_rate')->get();
											    if($vtypes->num_rows()>0)
											    {
											        foreach($vtypes->result() as $vtypes1)
											        {
											    ?>
											    <option value="<?php echo $vtypes1->id;?>"><?php echo $vtypes1->type;?></option>
											    <?php
											        }
											    }
											    ?>
											    
											</select>
												</div>
												</div>
												
													<div class="col-md-2 localtour">
													<div class="form-group">
												<label for="field-2" class="control-label">RATE Kms.</label>
												<input type="number" class="form-control ownconv" name="rate_per_km" step="0.01" id="rate_per_km" value="" onkeyup="calculate_rate()" readonly>
												</div>
												</div>
												
												<div class="col-md-2 localtour">
													<div class="form-group">
												<label for="field-2" class="control-label">START READING</label>
												<input type="number" class="form-control ownconv" name="start_reading" id="start_reading" value="" onblur="calculatetotamount();">
												</div>
												</div>
											
												<div class="col-md-2 localtour">
													<div class="form-group">
												<label for="field-2" class="control-label">END READING</label>
												<input type="number" class="form-control ownconv" name="end_reading" id="end_reading" value="" onblur="calculatetotamount();">
												</div>
												</div>
										
												
											</div>
												<script>
												function calculate_rate(){
													var start_reading= $('#start_reading').val();
													var end_reading= $('#end_reading').val();
													var rate_per_km= $('#rate_per_km').val();
													if(start_reading!=='' && end_reading!=='' && rate_per_km!==''){
														
														var finalreading = end_reading-start_reading;
														var totalamount = finalreading*rate_per_km;
														
														$('#finalamount').val(totalamount);
													}
												}
											</script>
											
											<div class="col-md-3">
												<div class="form-group">
												<label for="field-2" class="control-label">Case</label>
												<span id="error_case" style="color:red;">*</span>
												<select class="form-control" id="cases" name="case">
												
													<option value="2" <?php if($row->case_status=='2'){echo "selected";}?>>Open </option>
													<option value="0" <?php if($row->case_status=='0'){echo "selected";}?>>Closed</option>
												</select>
												</div>
											</div>
												
                                            </div>
										<div class="col-md-9"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" class="btn btn-success" value="Save">
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

     <script language="javascript" type="text/javascript">   

$(document).ready(function() {
     $('.select2').select2({ });
$("#save").click(function() {
var company_name = $("#company_name").val();
if(company_name=='')
{
	$("#error_company_name").html('Required!');
}
var sales_force = $("#sales_force").val();
if(sales_force=='')
{
	$("#error_sales_force").html('Required!');
}
var warrenty_status = $("#warrenty_status").val();
if(warrenty_status=='')
{
	$("#error_warrenty_status").html('Required!');
}
var nature_of_complaints = $("#nature_of_complaints").val();
if(nature_of_complaints=='')
{
	$("#error_nature_of_complaints").html('Required!');
}
var service_visit_report = $("#service_visit_report").val();
if(service_visit_report=='')
{
	$("#error_service_visit_report").html('Required!');
}
var cases = $("#cases").val();
if(cases=='')
{
	$("#error_case").html('Required!');
}


if(company_name=='' || sales_force=='' || warrenty_status=='' || nature_of_complaints=='' ||service_visit_report=='' || cases=='')
{
	
	return false;
}

});
});

function getrate(type)
{
   $.ajax({
		type:"post",
		url:"<?php echo page_url;?>Sales/getperkmrate/"+type,
		data:"1",
		success:function(data){
		   
		$("#rate_per_km").val(data);
		calculatetotamount();
		}
		});
    
    
}

function calculatetotamount()
{
     $("#finalamount").val('');
    var start_reading=$("#start_reading").val().trim();
     var end_reading=$("#end_reading").val().trim();
     var rate_per_km=$("#rate_per_km").val().trim();
     
     if(start_reading!='' &&  end_reading!='' && rate_per_km!='' )
     {
         if(end_reading>start_reading)
         {
         var diff=end_reading-start_reading;
        var amount=rate_per_km*diff;
        $("#finalamount").val(amount);
         }else
         {
             alert('End reading cannot be less than Start reading');
             return false;
              
         }
         
         
     }
    
}
</script>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
<script type="text/javascript">
		 $(document).ready(function(){
	var i=1;
 $('#addmore_btn').click(function(){

 i++;
 $('#dynamictasks').append('<div id="row'+i+'" class="row"><div class="col-md-12"><div class="col-md-3"><div class="form-group"><label>TRAVEL EXPENSE</label><select class="form-control" name="travel_expense[]" id="travel_expense" onChange="fetch_expense_options(0);"><option value="">SELECT EXPENSE TYPE</option><?php $query =$this->db->select('id, options')->from('conveyance_type_options')->where('conveyance_type_id','2')->where('status','1')->get();foreach($query->result() as $row){?><option value="<?php echo $row->id;?>"><?php echo $row->options;?></option><?php }?></select></div></div><div class="col-md-2"><div class="form-group"><label>AMOUNT</label><input type="number" class="form-control" name="travel_expense_amount[]" id="travel_expense_amount" value="" step="0.2"></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:30px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div></div><br/>');
 
 });
 
 
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id+'').remove();
 });
 
});
	  </script>
    </body>
</html>