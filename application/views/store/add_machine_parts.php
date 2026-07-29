<?php
$name='';
$unit='';
$masterreqid='';
if($this->uri->segment(3)<>'')
{
	$resyue=$this->db->select('id,item,unit')->from('item_master_request')->where('id',$this->uri->segment(3))->get();
if($resyue->num_rows()>0)
{
	$i=1;
	foreach($resyue->result() as $resyue1);
	$name=$resyue1->item;
	$unit=$resyue1->unit;
	$masterreqid=$resyue1->id;
}
}
$CI =& get_instance();
$CI->load->model('Store_model');
$fincode=$CI->Store_model->getautofincode();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>ADD IMS ITEM</title>

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
				<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">

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
                           
                            <h4 class="page-title">Add New Machine Part</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

 <form method="post" action="<?php echo page_url;?>Store/add_parts/"  enctype="multipart/form-data">
      <input type="hidden" name="masterreqid" value="<?php echo $masterreqid;?>">
				<div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">
								<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Part Name <span id="error_machine_part" style="color:red;">*</span><span style="color:red;"><?php echo form_error('machine_part');?></span></label>
														 
														 <input type="text" name="machine_part" id="machine_part" class="form-control" value="<?php echo $name;?>">
														
													</div>
												</div>
												
												 <div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Rack Location <span id="error_rack_location" style="color:red;">*</span> <span style="color:red;"><?php echo form_error('rack_location');?></span></label>
														<select class="form-control select2" name="rack_location" id="rack_location">
														 <option value="">Select Rack Location</option>
														 <?php 
														 $query = $this->db->select('id, rack_location')->from('store_rack_location')->get();
														 foreach($query->result() as $row){
														 ?>
															<option value="<?php echo $row->id;?>"><?php echo $row->rack_location;?></option>
														 <?php }?>
														 </select>
													</div>
												</div>
												
												 <div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Category  <span id="error_rack_location" style="color:red;">*</span> <span style="color:red;"><?php echo form_error('category_id');?></span></label>
														
														 <select class="form-control" name="category_id" id="category_id">
														 <option value="">Select Category</option>
														 <?php 
														 $query = $this->db->select('id, category')->from('presto_machine_part_category')->get();
														 foreach($query->result() as $row){
														 ?>
															<option value="<?php echo $row->id;?>"><?php echo $row->category;?></option>
														 <?php }?>
														 </select>
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														<label>Specification</label>
														<input type="text" class="form-control" name="specification" id="specification" value="">
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														<label>Make</label>
														<input type="text" class="form-control" name="make" id="make" value="">
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														<label>Cas Number</label>
														<input type="text" class="form-control" name="size_in_mm" id="size_in_mm" value="">
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														<label>Material </label>
														<input type="text" class="form-control" name="material" id="material" value="">
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														<label>RAW/BOP <span id="error_machine_part" style="color:red;">*</span><span style="color:red;"><?php echo form_error('raw_bop');?></span></label>
														<select class="form-control" name="raw_bop" id="raw_bop">
														 <option value="">Select</option>
														 <option value="RAW">Raw</option>
														 <option value="BOP">BOP</option>
														
														 </select>
													</div>
												</div>


												<div class="col-md-3">
													<div class="form-group">
														<label>Product Code</label>
														<input type="text" class="form-control" name="fincode" id="fincode" value="<?php echo $fincode;?>" readonly>
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														<label>Qty <span id="error_machine_part" style="color:red;">*</span><span style="color:red;"><?php echo form_error('qty');?></span></label>
														<input type="text" class="form-control" name="qty" id="qty" value="">
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														<label>Unit <span id="error_machine_part" style="color:red;">*</span><span style="color:red;"><?php echo form_error('unit');?></span></label>
														 <select class="form-control select2" name="unit" id="unit">
														 <option value="">Select Unit</option>
														 <?php 
														 $query = $this->db->select('id, shortname')->from('units')->order_by('name','ASC')->get();
														 foreach($query->result() as $row){
                                                    if($row->id==$unit)
                                                    {
                                                    $a="selected";
                                                    }else{
                                                    $a="";
                                                    }
													?>
															<option value="<?php echo $row->id;?>" <?php echo $a;?>><?php echo strtoupper($row->shortname);?></option>
														 <?php }?>
														 </select>
													</div>
												</div>
												
												<div class="col-md-3">
												    <div class="form-group" style="padding-top: 18px;">
												        <label>Conversion Applicable?</label><br>
												        <input type="checkbox" name="conversion_application" id="conversion_application" value="1">
												    </div>
												</div>
                        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
                        <script type="text/javascript">
                        $(document).ready(function(){
                        $('#conversion_application').change(function(){
                        if($(this).is(":checked")){
                        $('#multipleunitdiv').fadeIn('slow');
                       $('#diffvaluediv').fadeIn('slow');
                        }
                        else{
                        $('#multipleunitdiv').fadeOut('slow');
                        $('#diffvaluediv').fadeOut('slow');
                        }
                        });
                        });
                        </script>
						<div class="col-md-3" style="display:none;" id="multipleunitdiv">
													<div class="form-group">
														<label>Conversion Unit <span id="error_conversion_unit" style="color:red;">*</span><span style="color:red;"><?php echo form_error('unit');?></span></label>
														 <select class="form-control select2" name="conversion_unit" id="conversion_unit">
														 <option value="">Select Unit</option>
														 <?php 
														 $query = $this->db->select('id, shortname')->from('units')->order_by('name','ASC')->get();
														 foreach($query->result() as $row){
                                                    if($row->id==$unit)
                                                    {
                                                    $a="selected";
                                                    }else{
                                                    $a="";
                                                    }
													?>
															<option value="<?php echo $row->id;?>" <?php echo $a;?>><?php echo strtoupper($row->shortname);?></option>
														 <?php }?>
														 </select>
													</div>
								</div>
										<div class="col-md-3" id="diffvaluediv"  style="display:none">
										    <div class="form-group">
										        <label>Conversion Weight</label>
										        <input type="text" class="form-control" name="conversion_weight" id="conversion_weight" value="">
										    </div>
										</div>		
												
												<div class="col-md-3">
													<div class="form-group">
														<label>Picture</label>
														<input type="file" class="form-control" name="picture" id="picture" value="">
														
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														<label>HSN CODE</label>
													<input type="text" class="form-control" name="hsn" id="hsn" value="" placeholder="HSN Code">
													</div>
														<div class="form-group">
														<label>GST IN %</label>
													<input type="text" class="form-control" name="gst" id="gst" value="" placeholder="GST IN %">
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														<label>Stock <span id="error_machine_part" style="color:red;">*</span><span style="color:red;"><?php echo form_error('stock');?></span></label>
														<input type="text" class="form-control" name="stock" id="stock" value="">
														
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														<label>Min Stock <span id="error_machine_part" style="color:red;">*</span><span style="color:red;"><?php echo form_error('minstock');?></span></label>
														<input type="text" class="form-control" name="minstock" id="minstock" value="">
														
													</div>
												</div>
												
													<div class="col-md-3">
													<div class="form-group">
														<label>Critical Item?<span id="error_machine_part" style="color:red;">*</span><span style="color:red;"><?php echo form_error('minstock');?></span></label><br/>
														<input type="checkbox" class="" name="critical" id="critical" value="1">
														
													</div>
												</div>
										
												
                                            </div>
										
									
							
                                   

                                </div>
								
									<div class="row">
											<div class="col-md-12">
											<h4 style="font-weight:bold;">Item Suppliers</h4>
											<hr>
												 <div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">VENDOR </label>
														 <span id="error_item_name" style="color:red;"></span>
														<select class="form-control select2" name="vendor[]" id="vendor" style="font-size:13px;">
														<option value="">--SELECT VENDOR--</option>
														<?php 
														
														$query = $this->db->select('id,name')->from('vendors')->where('status','1')->order_by('name','ASC')->get();
														foreach($query->result() as $vendor){
														?>
														<option value="<?php echo $vendor->id;?>"><?php echo $vendor->name;?></option>
														<?php }?>
														</select>
													</div>
												</div>
												
													<div class="col-md-2">
													<div class="form-group">
														<label>LIST PRICE</label>
														<input type="text" name="lprice[]" id="lprice0" value="" class="form-control allow_decimal" onkeyup="getdiscountpricefornew(0);">
													</div>
												</div>
												
												
												<div class="col-md-2">
													<div class="form-group">
														<label>DISCOUNT</label>
														<input type="text" name="discount[]" id="discount0" value=""  class="form-control allow_decimal" onkeyup="getdiscountpricefornew(0);" >
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														<label>DISCOUNTED PRICE</label>
														<input type="text" name="price[]" id="price0" value=""  class="form-control allow_decimal" readonly >
													</div>
												</div>
												
											

												<div class="col-md-1">
													<div class="form-group" style="margin-top:25px">
														<button type="button" class="btn btn-warning" name="add" id="addmore_btn1"><i class="fa fa-plus"></i></button>
													</div>
												</div>
											</div>
											<div class="col-md-12">
												<div id="dynamictasks1"></div></div>
                                            </div>
											
											<div class="row">
											<div class="col-md-12">
											
											<div class="col-md-9"></div>
											<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
											<label>&nbsp;</label>
											<input type="submit" class="btn btn-success" value="Save">
											</div>
											</div>
											
											</div>
											</div>
											
											

                            </div>
                            <!-- end row -->
                        </div> <!-- end ard-box -->
                    </div><!-- end col-->
					</form>
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
	$(".select2").select2({});
$("#save").click(function() {
var machine = $("#machine").val();
if(machine=='')
{
	$("#error_machine").html('Required!');
}
var machine_part = $("#machine_part").val();
if(machine_part=='')
{
	$("#error_machine_part").html('Required!');
}
var rack_location = $("#rack_location").val();
if(rack_location=='')
{
	$("#error_rack_location").html('Required!');
}

if(machine=='' || machine_part=='' || rack_location=='')
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

 
 $('#dynamictasks1').append('<div id="row'+i+'" class="row"><div class="col-md-12"><div class="col-md-4"><div class="form-group"><label for="field-2" class="control-label">VENDOR </label><span id="error_item_name" style="color:red;"></span><select class="form-control select2'+i+'" name="vendor[]" id="vendor"  style="font-size:13px;"><option value="">--SELECT VENDOR--</option><?php $query = $this->db->select('id,name')->from('vendors')->where('status','1')->get(); foreach($query->result() as $vendor){?><option value="<?php echo $vendor->id;?>"><?php echo $vendor->name;?></option><?php }?></select></div></div><div class="col-md-2"><div class="form-group"><label>LIST PRICE</label><input type="text" name="lprice[]" id="lprice'+i+'" value="" step="0.2" class="form-control allow_decimal" onkeyup="getdiscountpricefornew(0);" ></div></div><div class="col-md-2"><div class="form-group"><label>DISCOUNT</label><input type="text" name="discount[]" id="discount'+i+'" value=""  class="form-control allow_decimal" onkeyup="getdiscountpricefornew('+i+');"></div></div><div class="col-md-2"><div class="form-group"><label>DISCOUNTED PRICE</label><input type="text" name="price[]" id="price'+i+'" value="" class="form-control allow_decimal" readonly required></div></div><div class="col-md-1"><div class="form-group pull-left"><label for="field-1" class="control-label">&nbsp;</label><br/><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div></div><br/>');
 initializeSelect2('select2'+i);
  i++;
 });
 
 
 
 
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id+'').remove();
 });
 
 $(".allow_decimal").on("input", function(evt) {
   var self = $(this);
   self.val(self.val().replace(/[^0-9\.]/g, ''));
   if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
   {
     evt.preventDefault();
   }
 });
 
});


 function initializeSelect2(selectElementObj) {
         $('.'+selectElementObj).select2({});
         
      }


  function getdiscountpricefornew(id)
{
	var lprice=$("#lprice"+id).val();
	var exdis=$("#discount"+id).val();

//alert(lprice);
	
	if((lprice!='' || lprice!=0))
	{
		
		
		if(exdis!=0)
		{
			var d=exdis/100;
			
			var lp=lprice*d;
			
			var fin=lprice-lp;
			
			$("#price"+id).val(fin.toFixed(2));
		}else{
			
			$("#price"+id).val(lprice);
		}
		
		
	}else{
		
		alert('List Price is required');
		$("#price"+id).val('');
	}
	
	
	
}

	  </script>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
    </body>
</html>
