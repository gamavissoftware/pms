<?php
$CI =& get_instance();
$CI->load->model('Store_model');
$itemname='';
$pono= $this->uri->segment(3);
$itemid=$this->uri->segment(4);

if($pono<>'' && $itemid<>'')
{
$rest=$this->db->select('type')->from('purchase_request')->where('prno',$pono)->get();
if($rest->num_rows()>0)
{
	foreach($rest->result() as $restt);
	
	if($restt->type==0)
	{
		
		$itemname=$CI->Store_model->getmachineitemname($itemid);
		
	}else if($restt->type==1)
	{

		$itemname=$CI->Store_model->getgeneralitemname($itemid);

	}else{
		
		echo "NO PR TYPE FOUND"; exit;
	}
	
	
}

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

        <title>Prestogroup Add New Vendor</title>

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
						
                        <h4 class="page-title">New Vendor Form</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
                <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
				
               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">
<form id="loginForm" method="post" action="<?php echo page_url;?>Vendor/add_new_vendor/<?php echo $this->uri->segment(3);?>" enctype="multipart/form-data">
<input type="hidden" name="item_id" value="<?php echo $itemid;?>">
<input type="hidden" name="prno" value="<?php echo $pono;?>">
        <div id="pageloader">
        <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
        </div>	
                             <div class="row">
                                           	<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Item Name</label>
														 <span id="error_item_name" style="color:red;">*</span>
														 <input type="text" class="form-control" name="item_name" id="item_name" value="<?php echo $itemname;?>" readonly required>
													</div>
												</div>  
												</div>
												 <div class="row">
											<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Vendor Name</label>
														 <span id="error_vendor_name" style="color:red;">*</span>
														 <input type="text" class="form-control" name="vendor_name" id="vendor_name" value="" required>
													</div>
												</div>
												
													<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Phone Number</label>
														 <span id="error_phone_number" style="color:red;">*</span>
														 <input type="text" class="form-control" name="phone_number" id="phone_number" value="" required>
													</div>
												</div>
												
												
											
												<div class="col-md-3">
												<div class="form-group">
												<label for="field-2" class="control-label">Address</label>
												<span id="error_task" style="color:red;">*</span>
												<textarea class="form-control" name="address" id="address" required></textarea>
												</div>
											</div>
										
										<div style="clear:both;height:0px"></div>
										
													<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Original Price</label>
														 <span id="error_original_price" style="color:red;">*</span>
														 <input type="text" class="form-control" name="original_price" id="original_price" value="" required>
													</div>
												</div>
												
													<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Discount type</label>
														 <span id="error_discount_type" style="color:red;">*</span>
														<select class="form-control" name="discount_type" id="discount_type">
														    <option value="">Select Option</option>
														    <option value="1">Fix Discount</option>
														    <option value="2">In Percent</option>
														    
														</select>
													</div>
												</div>
												
													<script>
																						$(function() {

											$('#fixed_amount').hide();
											$('#discount_percentage').hide();

											$('#discount_type').change(function(){
											$("#total_price").val('');
											if($('#discount_type').val() == '1') {
											$("#fixed_amount").show();
											$("#discounted_price").attr("required", true);
											$('#discount_percentage').hide();

											$('#discounted_percentage').attr('required',false);

											
											$("#total_price").val('');


											} else if($('#discount_type').val() == '2'){

											$("#fixed_amount").hide('');
											$('#discount_percentage').show();
											$('#discounted_percentage').attr('required',true);
											$("#discounted_price").attr("required", false);


											}else {

											$('#fixed_amount').hide(); 
											$('#discount_percentage').hide();

											$('#discounted_percentage').attr('required',true);
											$("#total_price").val('');
											$("#discounted_price").attr("required", true);


											} 
											});
											});
											</script>
												
													<div class="col-md-3" id="fixed_amount" style="display:none">
													<div class="form-group">
														 <label for="field-2" class="control-label">Discount (in rs)</label>
														 <span id="error_discounted_price" style="color:red;">*</span>
											<input type="text" class="form-control" name="discounted_price" id="discounted_price" value="">
													</div>
												</div>
												
													<div class="col-md-3" id="discount_percentage" style="display:none">
													<div class="form-group">
														 <label for="field-2" class="control-label">Discounted %</label>
														 <span id="error_discounted_price" style="color:red;">*</span>
											<input type="text" class="form-control" name="discounted_percentage" id="discounted_percentage" value="">
													</div>
												</div>
												
												
<script>
$(document).ready(function(){
  $("#discounted_percentage").keyup(function(){
    var discounted_percentage= $("#discounted_percentage").val();
    var original_price = $("#original_price").val();
    
    
    	                                $.ajax({
													type:"post",
													url:"<?php echo page_url;?>Vendor/totalpricecalculator",
													data:"discounted_percentage="+discounted_percentage+"&original_price="+original_price,
													success:function(data){
												$("#total_price").val(data);
													}
													});
    
  });
  
    $("#discounted_price").keyup(function(){
    var discounted_price= $("#discounted_price").val();
    var original_price = $("#original_price").val();
    
    
    	                                $.ajax({
													type:"post",
													url:"<?php echo page_url;?>Vendor/totalpricecalculator_after_dis",
													data:"discounted_price="+discounted_price+"&original_price="+original_price,
													success:function(data){
												$("#total_price").val(data);
													}
													});
    
  });
 
});
</script>
												
													<div class="col-md-3">
													<div class="form-group">
													<label for="field-2" class="control-label">Total</label>
											<span id="error_total_price" style="color:red;">*</span>
										<input type="text" class="form-control" name="total_price" id="total_price" value="" required readonly>
													</div>
												</div>
											
												 <div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Delivery By</label>
														 <span id="dby" style="color:red;">*</span>
														<select  class="form-control" name="dby" id="dby" required>
														<option value="">Delivery By</option>
														<option value="By Presto">By Presto</option>
														<option value="By Vendor">By Vendor</option>
														</select>
													</div>
												</div>
												
												
												 <div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Delivery Time</label>
														 <span id="ddays" style="color:red;">*</span>
														<select  class="form-control" name="delivery_days" id="delivery_days" required onchange="deltypecheck(this.value);">
														<option value="">Delivery Time</option>
														<option value="2">Ready</option>
													
														<option value="7">7 days</option>
														<option value="15">15 days</option>
														<option value="30">30 days</option>
													<!--	<option value="Other">Other</option>-->
														</select>
													</div>
												</div>
												
											<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Payment Terms</label>
														 <span id="error_payment_terms" style="color:red;">*</span>
														 <input type="text" class="form-control" name="payment_terms" id="payment_terms" value="" required>
													</div>
												</div>
												
												 <div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Payment Mode</label>
														 <span id="error_vendor_name" style="color:red;">*</span>
													 <select class="form-control" name="pmode" id="pmode"  value="" required>
												<option value="">Select Mode</option>
											
												<option value="Cheque">Cheque</option>
												<option value="NEFT">NEFT</option>
											
												
													     </select>
													</div>
												</div>
												
												
												
											
											
											<div class="col-md-12">
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
    jQuery.noConflict();
    
$("#save").click(function() {
    alert('test');
	var item_name= $("#item_name").val();
if(item_name=='')
{
	$("#error_item_name").html('Required!');
}
var vendor_name = $("#vendor_name").val();
if(vendor_name=='')
{
	$("#error_vendor_name").html('Required!');
}
var phone_number = $("#phone_number").val();
if(phone_number=='')
{
	$("#error_phone_number").html('Required!');
}


var dby = $("#dby").val();
if(dby=='')
{
	$("#dby").html('Required!');
}


var address = $("#address").val();
if(address=='')
{
	
	$("#error_address").html('Required!');
}

var delivery_days = $("#delivery_days").val();
if(delivery_days=='')
{
	$("#ddays").html('Required!');
}

var payment_terms = $("#payment_terms").val();
if(payment_terms=='')
{
	$("#error_payment_terms").html('Required!');
}

var original_price = $("#original_price").val();
if(original_price=='')
{
	$("#error_original_price").html('Required!');
}

var discount_type=$("#discount_type").val);
if(discount_type=='1')
{
var discounted_price = $("#discounted_price").val();
if(discounted_price=='')
{
	$("#error_discounted_price").html('Required!');
}

}else{
	var discounted_price=1;
}

if(item_name==''|| vendor_name=='' || phone_number=='' || dby=='' || address=='' || delivery_days=='' || payment_terms=='' || original_price=='' || discounted_price=='')
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
 <script> $(document).ready(function() {
 var date = new Date();
date.setDate(date.getDate());
   
$('.datepicker').datepicker({
    todayHighlight:true
    
    
});

                $("#datepicker1").datepicker({
					orientation: 'bottom',
					todayHighlight:true
				});
			//	$('.datepicker').datepicker({todayHighlight:true});
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
					
                })

            });</script>
    </body>
</html>