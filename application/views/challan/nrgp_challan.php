<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?></title>

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
		<style>
			.divheight{
			padding-top:50px;
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
            <div class="container-fluid">

                <!-- Page-Title -->
				
                <div class="row">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						
                            <h4 class="page-title text-center">NRGP CHALLAN</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">
						<form id="loginForm" method="post" action="<?php echo page_url;?>Challan/add_nrgp_challan/<?php echo $this->uri->segment(3);?>">
							 <div id="pageloader">
						  
						</div>
                                			<div class="row">
											<div class="col-md-3">
												<div class="form-group">
													<label>Supplier</label>
													<select class="form-control" name="supplier" id="supplier" onChange="getsuppilier_items();">
													<option value="">Select Supplier</option>
													<?php 
													
														$query = $this->db->select('id, name')->from('vendors')->where('status','1')->get();
														foreach($query->result() as $row){
														?>
														<option value="<?php echo $row->id;?>"><?php echo $row->name;?></option>
														<?php 
														}
													?>
												</select>
												
												<script>
													function getsuppilier_items(){
													var supplier=$("#supplier").val();
													var source = "<?php echo $this->uri->segment(3);?>";
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Challan/get_supplier_item_list",
													data:"supplier="+supplier+"&source="+source,
													success:function(data){
													$(".itemsclass").html(data);
													}
													});
													}
													function getsuppilier_items_more(){
													var supplier=$("#supplier").val();
													var source = "<?php echo $this->uri->segment(3);?>";
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Challan/get_supplier_item_list",
													data:"supplier="+supplier+"&source="+source,
													success:function(data){
													$(".itemsclasss").html(data);
													}
													});
													}
													
												</script>
												</div>
											</div>
											<div class="col-md-3">
													<div class="form-group">
                                                        <label for="field-1" class="control-label">Challan No<span style="color:red;0">*</span></label>
														<span id="error_challan_no" style="color:red;"></span>
														<input type="text" class="form-control" id="challan_no" name="challan_no" value="<?php 
														$query = $this->db->select('id,challan_no')->from('nrgp_challan')->get();
														$res = $query->result();
														$countnumber = count($res);
														if($countnumber=='0'){
														$number = '0001';
														}else{
														$number = sprintf('%04d',$countnumber+1);
														}
														$challan_number="PRESNRCH-".$number;
														echo $challan_number;?>" placeholder="Challan No" readonly>
												
                                                    </div>
													
													
												</div>
												<div class="col-md-3">
												<div class="form-group">
                                                        <label for="field-1" class="control-label">Challan Date<span style="color:red;0">*</span></label>
														<span id="error_challan_date" style="color:red;"></span>
														<input type="text" class="form-control" id="challan_date" name="challan_date" placeholder="Challan Date" value="<?php echo date('d-m-Y');?>" readonly>
												</div>
												</div>
												
													
												
													
												<div class="col-md-3">
													<div class="form-group">
                                                        <label for="field-1" class="control-label">Vehicle Type<span style="color:red;0">*</span></label><input type="text" class="form-control" name="vehicle_type" id="vehicle_type" style="text-transform:uppercase" placeholder="Vehicle Type" required></div>
												</div>
												
												</div>
												<div class="row">
											 <div class="col-md-3">
													<div class="form-group">
                                                        <label for="field-1" class="control-label">ITEM <span style="color:red;0">*</span></label>
														<span id="error_itemname" style="color:red;"></span>
														<select class="form-control itemsclass" name="itemname[]" id="itemname0" onChange="getitem_unit(0); get_vendor_price(0);" required>
														<option value="">Select Item</option>
														</select>
														
												
                                                    </div>
												</div>
												<script type="text/javascript">
											
													function getitem_unit(i){
													var itemname=$("#itemname"+i).val();
													var source = "<?php echo $this->uri->segment(3);?>";
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Challan/get_item_unit",
													data:"itemname="+itemname+"&source="+source,
													success:function(data){
													$("#unit"+i).val(data);
													}
													});
													}
													
													function get_vendor_price(i){
													var supplier=$("#supplier").val();
													var itemname=$("#itemname"+i).val();
													var source = "<?php echo $this->uri->segment(3);?>";
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Challan/get_item_price",
													data:"itemname="+itemname+"&source="+source+"&supplier="+supplier,
													success:function(data){
													$("#rate_per_unit"+i).val(data);
													}
													});
													}
											
												</script>
												
										<div class="col-md-1">
													<div class="form-group">
                                                        <label for="field-1" class="control-label">Unit <span style="color:red;0">*</span> </label>
														
														<input type="text" class="form-control" name="unit[]" id="unit0" value="" readonly>
												
                                                    </div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
                                                        <label for="field-1" class="control-label">Sent Qty<span style="color:red;0">*</span></label>
														<span id="error_qty" style="color:red;"></span>
														<input  type="number" class="form-control" id="qty" name="qty[]" placeholder="Sent Quantity"  required="required" style="text-transform:uppercase" >
												
                                                    </div>
												</div>
												
												 <div class="col-md-2">
													<div class="form-group">
                                                        <label for="field-1" class="control-label">Weight of Qty<span style="color:red;0">*</span></label>
														<span id="error_qty" style="color:red;"></span>
														<input  type="text" class="form-control" id="weight_of_qty" name="weight_of_qty[]" placeholder="Weight of Qty"  required="required" style="text-transform:uppercase">
												
                                                    </div>
												</div>
													<div class="col-md-2">
													<div class="form-group">
                                                        <label for="field-1" class="control-label">Rate/Unit <span style="color:red;0">*</span></label>
														<input type="number" class="form-control" id="rate_per_unit0" name="rate_per_unit[]" value="" readonly>
												
                                                    </div>
												</div>
												
												
												
											<div class="col-md-1">
													<div class="form-group" style="margin-top:25px">
														<button type="button" class="btn btn-warning" name="add" onclick="getsuppilier_items_more();" id="addmore_btn"><i class="fa fa-plus"></i></button>
													</div>
												</div>
											
											</div>
											<div class="row">
												<div id="dynamictasks">
											</div>
											</div>
											
										<div class="col-md-11"></div>
										<div class="col-md-1">
											<div class="form-group">
												<input type="submit" class="btn btn-success" value="Submit">
											</div>
										</div>
                                
									<div style="padding-bottom:20px"></div>
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
		<script type="text/javascript">
		 $(document).ready(function(){
 var i=1;
 $('#addmore_btn').click(function(){
 i++;
 
 $('#dynamictasks').append('<div id="row'+i+'" class="row"><div class="col-md-12"><div class="col-md-3"><div class="form-group"><label for="field-1" class="control-label">ITEM <span style="color:red;0">*</span></label><span id="error_itemname" style="color:red;"></span><select class="form-control itemsclasss" name="itemname[]" id="itemname'+i+'" onChange="getitem_unit('+i+'); get_vendor_price('+i+');"><option value="">Select Item</option></select></div></div><div class="col-md-1"><div class="form-group"><label for="field-1" class="control-label">Unit <span style="color:red;0">*</span> </label><input type="text" class="form-control" name="unit[]" id="unit'+i+'" value="" readonly></div></div><div class="col-md-2"><div class="form-group"><label for="field-1" class="control-label">Sent Qty<span style="color:red;0">*</span></label><span id="error_qty" style="color:red;"></span><input  type="number" class="form-control" id="qty" name="qty[]" placeholder="Sent Quantity"  required="required" style="text-transform:uppercase"></div></div><div class="col-md-2"><div class="form-group"><label for="field-1" class="control-label">Weight of Qty<span style="color:red;0">*</span></label><span id="error_qty" style="color:red;"></span><input  type="text" class="form-control" id="weight_of_qty" name="weight_of_qty[]" placeholder="Weight of Qty"  required="required" style="text-transform:uppercase"></div></div><div class="col-md-2"><div class="form-group"><label for="field-1" class="control-label">Rate/Unit <span style="color:red;0">*</span></label><input type="number" name="rate_per_unit[]" class="form-control" id="rate_per_unit'+i+'" value="" readonly></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:30px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div></div><br/>');
 
 initialisedatepick();
 initializeSelect2('itemName'+i);
 });
 
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id+'').remove();
 });
 
});
	  </script>

<script language="javascript" type="text/javascript">   
$(document).ready(function() {
$("#save").click(function() {
	var employee_name= $("#employee_name").val();
if(employee_name=='')
{
	$("#error_employee_name").html('Required!');
}
var material_out_date = $("#material_out_date").val();
if(material_out_date)
{
	$("#error_material_out_date").html('Required!');
}
var out_time = $("#out_time").val();
if(out_time=='')
{
	$("#error_out_time").html('Required!');
}

var item_name = $("#item_name").val();
if(item_name=='')
{
	
	$("#error_item_name").html('Required!');
}
var to = $("#to").val();
if(to=='')
{
	
	$("#error_to").html('Required!');
}
var job_card_number = $("#job_card_number").val();
if(job_card_number=='')
{
	
	$("#error_job_card_number").html('Required!');
}
var weight = $("#weight").val();
if(weight=='')
{
	
	$("#error_weight").html('Required!');
}
var qty = $("#qty").val();
if(qty=='')
{
	
	$("#error_qty").html('Required!');
}
var challan_no = $("#challan_no").val();
if(challan_no=='')
{
	
	$("#error_challan_no").html('Required!');
}

var out_from = $("#out_from").val();
if(out_from=='')
{
	
	$("#error_out_from").html('Required!');
}
if(employee_name==''|| material_out_date=='' || out_time=='' ||  item_name=='' || to=='' || job_card_number=='' || weight=='' || qty=='' || challan_no=='' || out_from=='')
{
	
	return false;
}

});
});
</script>

    </body>
</html>