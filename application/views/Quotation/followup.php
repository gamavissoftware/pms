<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

       <title><?php echo sitetitle; ?> Quotation follow-up</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
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
		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script>


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
            <div class="container">

                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right">
						  
                               
                            </div>
                           
                            <h4 class="page-title">Update Follow-up</h4>
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
								$id = $this->uri->segment(3);
								$this->db->select('*')->from('quotation_request')->where('id',$id);
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $row)
								?>
								
                                   <form method="post" action="<?php echo page_url;?>Quotation/quotation_followup/<?php echo $this->uri->segment(3);?>" enctype="multipart/form-data">
								   <div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Customer Name</label>
														 <span id="error_product_name" style="color:red;">*</span>
														 <input type="text" class="form-control" name="followup_date" id="followup_date" placeholder="Customer Name" value="<?php echo $row->customer_name;?>" readonly>
													</div>
												</div>
										
											<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Reference Number</label>
														 <span id="error_product_name" style="color:red;">*</span>
														 <input type="text" class="form-control" name="followup_date" id="followup_date" placeholder="Reference Number" value="<?php echo $row->reference_number;?>" readonly>
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Next Follow-up Date</label>
														 <span id="error_product_name" style="color:red;">*</span>
														 <input type="date" class="form-control" name="followup_date" id="followup_date" placeholder="Next Follow-up" value="" required>
													</div>
												</div>
												
												<div class="col-md-3">
												  <div class="form-group">
												<label for="field-2" class="control-label">Status</label>
												<span id="error_status" style="color:red;">*</span>
												<select class="form-control" id="status" name="status" onchange="orderstatuschange();" required>
													<option value="">--Select Status--</option>
													<option value="1">ON HOLD</option>
													<option value="2">DEAD</option>
													<option value="3">PI SENT</option>
													<option value="4">HOT LEAD</option>
													<option value="5">ORDER LOST</option>
													<option value="6">ORDER CLOSED WON</option>
												</select>
												</div>
												</div>
												<script>
												function orderstatuschange(){
													var status = $("#status").val();
													if(status=='5'){
														$("#reasonbox").show();
													}else{
														$("#reasonbox").hide();
													}
												}
												</script>
												
												<div class="col-md-12" style="display:none" id="reasonbox">
													<div class="form-group">
														<label>Order Lost Reason</label>
														<textarea class="form-control" name="orderlostreason" id="orderlostreason"></textarea>
													</div>
												</div>
												
												<?php 
												$i=1;
												$q = $this->db->select('a.id,a.quotation_id, a.product_quantity, b.instruments_name,b.mvalue,b.model_number, a.product_quantity, a.discount_type, a.discount')->from('quotation_product_details a')->join('presto_instruments b','a.product_id=b.id','left')->where('a.quotation_id',$this->uri->segment(3))->get();
												foreach($q->result() as $prdrows){
												?>
												<div class="col-md-12">
												<input type="hidden" class="form-control" name="recordid[]" value="<?php echo $prdrows->id;?>">
												<div class="col-md-6">
													<div class="form-group">
														<label>Product Name</label>
														<input type="text" class="form-control" name="products_name[]" id="products_name" value="<?php echo $prdrows->instruments_name;?>" readonly>
													</div>
												</div>
												<div class="col-md-1">
													<div class="form-group">
														<label>Qty</label>
														<input type="text" class="form-control" name="qty[]" id="qty" value="<?php echo $prdrows->product_quantity;?>">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														<label>Discount In</label>
														<select class="form-control" name="discount_type[]" id="discount_type<?php echo $prdrows->id;?>" onchange="getdiscounttype(<?php echo $prdrows->id;?>);">
															<option value="">Select Option</option>
															<option value="0" <?php if($prdrows->discount_type=='0'){echo "selected";}?>>In Percent (%)</option>
															<option value="1" <?php if($prdrows->discount_type=='1'){echo "selected";}?>>Fix Amount</option>
															
														</select>
													</div>
												</div>
												
												<script>  
												
												function getdiscounttype(i){
													
												$("#discount_box"+i).show();	
												if ( this.value == '0')
												{
												$("#inpercent"+i).show();
												$("#fixedamount"+i).hide();
												}
												else if(this.value == '1')
												{
												$("#fixedamount"+i).show();
												$("#inpercent"+i).hide();
												}
												}
												
												</script>
												
												<div class="col-md-3" <?php if($prdrows->discount){}else{?> style="display:none;" <?php }?> id="discount_box<?php echo $prdrows->id;?>">
													<div class="form-group">
														<label>Discount <?php if($prdrows->discount_type=='0'){echo "In (%)";}?><span id="inpercent<?php echo $prdrows->id;?>" style="display:none;">In (%)</span><span id="fixedamount<?php echo $prdrows->id;?>" style="display:none;"></span></label>
														<input type="text" class="form-control" id="discount" name="discount[]" value="<?php echo $prdrows->discount;?>">
													</div>
												</div>
												
												
												
												</div>
												<?php }?>
												
												<div style="clear:both;height:10px"></div>
												<div class="col-md-4">
												<div class="form-group">
												<label for="field-1" class="control-label">Product Name</label>
												<span id="error_product" style="color:red;"></span>
												<select class="form-control select3" id="product" name="product[]">
																															<option value="">--Select Product--</option>
																																				<?php 
								$this->db->select('id, instruments_name, model_number')->from('presto_instruments')->where('status','1')->where('instruments_of','2');
								$this->db->order_by('instruments_name','asc');
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $rows){
									?>
																																				<option value="<?php echo $rows->id;?>"><?php echo strtoupper($rows->instruments_name);?> (<?php echo strtoupper($rows->model_number);?>)</option>
																																		<?php }?>       
																																			</select>
																					   
												</div>
												</div>
                                
               
             <div class="col-md-1">
                <div class="form-group">
                    <label for="field-1" class="control-label">Quantity</label>
                    <span id="error_product_quantity" style="color:red;"></span>
                    <input type="number" class="form-control" id="productquantity" name="productquantity[]" value="">
            
                </div>
            </div>                     
                                                
             <div class="col-md-2">
            <div class="form-group" style="margin-top:25px">
            <button type="button" class="btn btn-warning" name="add" id="addmore_btn">Add More Product<i class="fa fa-plus"></i></button>
            </div>
            </div>
			
			<div class="col-md-12">                                          
			<div id="dynamictasks"></div>
			</div>
												<script>  
												$(document).ready(function(){
												$('#status').on('change', function() {
												if ( this.value == '3')
												{
												$("#termconditionsdiv").show();
												}
												else
												{
												$("#termconditionsdiv").hide();
												}
												});
												});
												</script>											
												<div class="col-md-12" id="termconditionsdiv" style="display:none">
													<div class="form-group">
														<label>Term & Conditions</label>
														<?php if($row->terms_conditions==''){?>
														<textarea class="form-control" name="terms_conditions" id="terms_conditions"><?php $q = $this->db->select('title, description')->from('terms_and_conditions_master')->where('status','1')->get();
														foreach($q->result() as $termconditions){
														echo "<p>".$termconditions->title." : ".$termconditions->description."</p><br/>";
														}?></textarea>	
														<?php }else{?>
														<textarea class="form-control" name="terms_conditions" id="terms_conditions"><?php echo $row->terms_conditions;?></textarea>
														<?php }?>
														<script>CKEDITOR.replace( 'terms_conditions' );</script>
														 
													</div>
												</div>
												
												<div class="col-md-12">
													<div class="form-group">
														<label>Remarks</label>
														<textarea class="form-control" name="remarks" id="remarks"></textarea>
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														<label>Packing Type</label>
														<select class="form-control" name="packingtype" id="packingtype" onchange="getpackinginfo();">
															<option value="">Select Option</option>
															<option value="1" <?php if($row->packingcharges_type=='1'){echo "selected";}?>>In Percent</option>
															<option value="2" <?php if($row->packingcharges_type=='2'){echo "selected";}?>>Fixed Amount</option>
														</select>
													</div> 
												</div>
												
												<script>  
												
												function getpackinginfo(i){
												var packing_charges= $("#packing_charges").val();											
												$("#packingbox").show();	
												if ( this.value == '1')
												{
												if(packing_charges.length >100){
													 $('#packing_charges').val('');
												}
												$("#inpercentshow").show();
												$("#fixedamountshow").hide();
												}
												else if(this.value == '1')
												{
												$("#fixedamountshow"+i).show();
												$("#inpercentshow"+i).hide();
												}
												}
												
												</script>
												
												<div class="col-md-3" <?php if($row->packingcharges_type){}else{?> style="display:none;" <?php }?> id="packingbox">
													<div class="form-group">
														<label>Packing Charges <?php if($row->packingcharges_type=='1'){echo "In (%)";}?><span id="inpercentshow" style="display:none;">In (%)</span><span id="fixedamountshow" style="display:none;"></span></label>
														<input type="text" class="form-control" name="packing_charges" id="packing_charges" value="<?php echo $row->packing_charges;?>">
													</div> 
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														<label>Freight Charges</label>
														<input type="text" class="form-control" name="freight_charges" id="freight_charges" value="<?php echo $row->packing_charges;?>">
													</div> 
												</div>
												
										<div class="col-md-9"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" class="btn btn-success" value="Update">
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
 

<div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered pretty">
                                <thead>
                                <tr>
                                    <th>Sr No.</th>
									<th>Next Follow-up Date</th>
                                    <th>Current Status</th>
                                    <th>Remarks</th>
                                    
                                </tr>
                                </thead>
                                <tbody>
								<?php 
								$i=1;
								$q = $this->db->select('followup_date, status, remarks')->from('quotation_followup')->where('quotation_id',$this->uri->segment(3))->get();
								foreach($q->result() as $row){
								?>
									<tr>
										<td><?php echo $i;?></td>
										<td><?php echo date('d-m-Y',strtotime($row->followup_date));?></td>
										<td><?php 
										if($row->status=='1'){
											echo "ON HOLD";
										}else if($row->status=='2'){
											echo "DEAD";
										}else if($row->status=='3'){
											echo "PI SENT";
										}else if($row->status=='4'){
											echo "HOT LEAD";
										}else if($row->status=='5'){
											echo "ORDER LOST";
										}else{
											echo "";
										}
										?></td>
										<td><?php /*if($row->status=='4'){
											$q = $this->db->select('terms_conditions')->from('quotation_request')->where('id',$this->uri->segment(3))->get();
											foreach($q->result() as $rows){
												echo $rows->terms_conditions;
											}
											
										}*/
										echo $row->remarks;?></td>
									</tr>
								<?php $i++;}?>
								</tbody>
                            </table>
                        </div>
                    </div>
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
	$('.select3').select2({ });
$("#save").click(function() {

var product_name = $("#product_name").val();
if(product_name=='')
{
	
	$("#error_product_name").html('Required!');
}

var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}
var price = $("#price").val();
if(price=='')
{
	
	$("#error_price").html('Required!');
}

if(product_category=='' || sub_category=='' || product_name=='' || price=='' )
{
	
	return false;
}

});
});
</script>
<script type="text/javascript">
         $(document).ready(function(){
 var i=1;
 $('#addmore_btn').click(function(){

 $('#dynamictasks').append('<div id="row'+i+'" class="row">   <div class="col-md-6"><div class="form-group"><label for="field-1" class="control-label">Product Name</label><span id="error_support_option" style="color:red;"></span><select class="form-control select3'+i+'" id="product" name="product[]"><option value="">--Select Product--</option><?php $this->db->select('id, instruments_name, model_number')->from('presto_instruments')->where('status','1')->where('instruments_of','2');$this->db->order_by('instruments_name','asc');$query = $this->db->get();$res = $query->result();foreach($res as $row){?><option value="<?php echo $row->id;?>"><?php echo $row->instruments_name;?> (<?php echo $row->model_number;?>)</option><?php }?></select></div></div><div class="col-md-1"><div class="form-group"><label for="field-1" class="control-label">Quantity</label><input type="number" class="form-control" id="productquantity" name="productquantity[]" value="1"></div></div><div class="col-md-2"><div class="form-group pull-left" style="padding-top:23px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div><br/>');
initializeSelect2('select3'+i);
 i++;
 });
 
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id+'').remove();
 }); 
});
 </script> 
 <script>

	  function initializeSelect2(selectElementObj) {
		$('.'+selectElementObj).select2({
			
		 });
      }

	  </script>
	  <script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
    </body>
</html>