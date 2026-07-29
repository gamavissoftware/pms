<?php

$id=$this->uri->segment(3);
if($id<>'')
{
	$this->db->select('a.*')->from('machine_parts_with_picture_view a')->where('id',$id);
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $editdata);
			
			
}else
{
	echo "INVALID ACCESS";exit;
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

        <title>Edit Machine Parts</title>

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
            <div class="container-fluid">

                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right">
						  
                               
                            </div>
                           
                            <h4 class="page-title">Edit Machine Part</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

 <form method="post" action="<?php echo page_url;?>Store/update_parts/<?php echo $id;?>"  enctype="multipart/form-data">
 <input type="hidden" name="oldimage" value="<?php echo $editdata->picture;?>">
				<div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">
								
								<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Part Name <span id="error_machine_part" style="color:red;">*</span><span style="color:red;"><?php echo form_error('machine_part');?></span></label>
														 
														
													</div>
														 <input type="text" name="machine_part" id="machine_part" class="form-control" value="<?php echo $editdata->part;?>">
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
															<option value="<?php echo $row->id;?>" <?php if($row->id==$editdata->location_id) {?> selected <?php }?> ><?php echo $row->rack_location;?></option>
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
															<option value="<?php echo $row->id;?>" <?php if($row->id==$editdata->category_id) {?> selected <?php }?> ><?php echo $row->category;?></option>
														 <?php }?>
														 </select>
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														<label>Specification</label>
														<input type="text" class="form-control" name="specification" id="specification" value="<?php echo $editdata->specification;?>">
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														<label>Make</label>
														<input type="text" class="form-control" name="make" id="make" value="<?php echo $editdata->makes;?>">
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														<label>Size in mm</label>
														<input type="text" class="form-control" name="size_in_mm" id="size_in_mm" value="<?php echo $editdata->size_in_mm;?>">
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														<label>Material </label>
														<input type="text" class="form-control" name="material" id="material" value="<?php echo $editdata->material;?>">
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														<label>RAW/BOP <span id="error_machine_part" style="color:red;">*</span><span style="color:red;"><?php echo form_error('raw_bop');?></span></label>
														<select class="form-control" name="raw_bop" id="raw_bop">
														 <option value="">Select</option>
														 <option value="RAW" <?php if($editdata->raw_bop=='RAW'){?> selected <?php }?>>Raw</option>
														 <option value="BOP" <?php if($editdata->raw_bop=='BOP'){?> selected <?php }?>>BOP</option>
														
														 </select>
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														<label>Fincode</label>
														<input type="text" class="form-control" name="fincode" id="fincode" value="<?php echo $editdata->fincode;?>">
													</div>
												</div>
												
												<div class="col-md-3" style="display:none;">
													<div class="form-group">
														<label>Qty <span id="error_machine_part" style="color:red;">*</span><span style="color:red;"><?php echo form_error('qty');?></span></label>
														<input type="text" class="form-control" name="qty" id="qty" value="<?php echo $editdata->qty;?>">
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
														 ?>
															<option value="<?php echo $row->id;?>" <?php if($editdata->unit==$row->id){?> selected <?php } ?>><?php echo strtoupper($row->shortname);?></option>
														 <?php }?>
														 </select>
													</div>
												</div>
												
												
													<div class="col-md-3">
												    <div class="form-group" style="padding-top: 18px;">
												        <label>Conversion Applicable?</label><br>
												        <input type="checkbox" name="conversion_application" id="conversion_application" value="1"  <?php if($editdata->conversion_unit){?>checked="checked"<?php }?>>
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
						<div class="col-md-3" <?php if($editdata->conversion_unit){}else{?> style="display:none;"<?php }?> id="multipleunitdiv">
													<div class="form-group" style="padding-top:10px">
														<label>Conversion Unit <span id="error_conversion_unit" style="color:red;">*</span><span style="color:red;"><?php echo form_error('unit');?></span></label>
														 <select class="form-control select2" name="conversion_unit" id="conversion_unit">
														 <option value="">Select Unit</option>
														 <?php 
														 $query12343 = $this->db->select('id, shortname')->from('units')->order_by('name','ASC')->get();
														 if($query12343->num_rows()>0)
														 {  
														 foreach($query12343->result() as $row12233){
                                                    if($row12233->id==$editdata->conversion_unit)
                                                    {
                                                    $a="selected";
                                                    }else{
                                                    $a="";
                                                    }
													?>
									    <option value="<?php echo $row12233->id;?>" <?php echo $a;?> <?php if($editdata->conversion_unit==$row12233->id){?> selected <?php } ?>><?php echo strtoupper($row12233->shortname);?></option>
														 <?php } } ?>
														 </select>
													</div>
								</div>
										<div class="col-md-3" id="diffvaluediv"  <?php if($editdata->conversion_weight){}else{?> style="display:none"<?php }?>>
										    <div class="form-group">
										        <label>Conversion Weight</label>
										        <input type="text" class="form-control" name="conversion_weight" id="conversion_weight" value="<?php echo $editdata->conversion_weight;?>">
										    </div>
										</div>		
												
												<div class="col-md-3">
													<div class="form-group">
														<label>Picture</label>
														<input type="file" class="form-control" name="picture" id="picture" value="">
												<?php		if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$editdata->fincode.'.jpg')){
			    
				$IMG = product_items.$editdata->fincode.'.jpg';
				$image = "<img src='".$IMG."' width='100px'>";
			}else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$editdata->fincode.'.JPG')){
			    
				$IMG = product_items.$editdata->fincode.'.JPG';
				$image = "<img src='".$IMG."' width='100px'>";
			}else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$editdata->fincode.'.jpeg')){
			    	$IMG = product_items.$editdata->fincode.'.jpeg';
				$image = "<img src='".$IMG."' width='100px'>";
		    }else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image_bank/product_item/'.$editdata->fincode.'.JPEG')){
			    
				$IMG = product_items.$editdata->fincode.'.JPEG';
				$image = "<img src='".$IMG."' width='100px'>";
			}else{
				$image="";
			}
			
			echo $image;
			?>
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														<label>HSN CODE</label>
													<input type="text" class="form-control" name="hsn" id="hsn" value="<?php echo $editdata->hsn;?>">
													</div>
														
												</div>
												<?php
												if($editdata->current_stock>0)
												{
												    $a="readonly";
												}else
												{
												    $a="readonly";
												}
												
												?>
												<div class="col-md-3">
													<div class="form-group">
														<label>Stock <span id="error_machine_part" style="color:red;">*</span><span style="color:red;"><?php echo form_error('stock');?></span></label>
														<input type="text" class="form-control" name="stock" id="stock" value="<?php echo $editdata->current_stock;?>" <?php echo $a;?>>
														
													</div>
												</div>


												<?php
												
												$resteyue=$this->db->select('sum(a.stock) as ststock')->from('blockedstock_view a')->where('a.itemid',$id)->where('a.active','1')->where('a.issued','0')->where('a.addedOn>','2021-10-31 23:59:59')->get();
												if($resteyue->num_rows()>0)
												{
												foreach($resteyue->result() as $resteyue1);
												$b=$resteyue1->ststock;

												}else
												{
												$b=0;
												}
        
												    $a="readonly";
												
												?>
												<div class="col-md-3">
													<div class="form-group">
														<label>BLOCKED STOCK <span id="error_machine_part" style="color:red;">*</span></label>
														<input type="text" class="form-control" name="blockedst" id="blockedst" value="<?php echo $b;?>" <?php echo $a;?> style="font-weight:; ">
														
													</div>
												</div>
												
												
												
												
													<div class="col-md-3">
													<div class="form-group">
														<label>Critical Item?<span id="error_machine_part" style="color:red;">*</span><span style="color:red;"><?php echo form_error('minstock');?></span></label><br/>
														<input type="checkbox" class="" name="critical" id="critical" value="1" <?php if($editdata->critical=='1'){?> checked <?php } ?>>
														
													</div>
												</div>
												
												<div style="clear:both;height:10px"></div>	
												
													<div class="col-md-3">
											
														<div class="form-group">
														<label>GST IN %</label>
													<input type="text" class="form-control" name="gst" id="gst" value="<?php echo $editdata->gst;?>">
													</div>
												</div>
												
												
												<div class="col-md-3">
											
														<div class="form-group">
														<label>Consumption Per Year</label>
													<input type="text" class="form-control" name="consumption" id="consumption" value="<?php echo $editdata->consumption;?>" onkeyup="checkformaxlevel();">
													</div>
												</div>
												
												
												
												<div class="col-md-3">
											
														<div class="form-group">
														<label>Safety Factor (in Nos,Pcs etc.)</label>
													<input type="text" class="form-control" name="safety" id="safety" value="<?php echo $editdata->safetyfactor;?>" onkeyup="checkformaxlevel();">
													</div>
												</div>
												
												
												
												<div class="col-md-3">
											
														<div class="form-group">
														<label>Lead Time <span id="error_machine_part" style="color:red;">*</span><span style="color:red;"><?php echo form_error('ltime');?></span></label>
													<select name="ltime" id="ltime" class="form-control" onchange="checkformaxlevel();">
													<option value="">Select</option>
													<option value="1" <?php if($editdata->leadtime=='1'){?> selected <?php } ?>>1 Day</option>
													<option value="7"  <?php if($editdata->leadtime=='7'){?> selected <?php } ?>>7 Day</option>
													<option value="15"  <?php if($editdata->leadtime=='15'){?> selected <?php } ?>>15 Day</option>
													<option value="30"  <?php if($editdata->leadtime=='30'){?> selected <?php } ?>>30 Day</option>
													<option value="45"  <?php if($editdata->leadtime=='45'){?> selected <?php } ?>>45 Day</option>
													</select>
													</div>
												</div>
												
												
												
												<div class="col-md-3">
													<div class="form-group">
														<label>Max Stock/Level <span id="error_machine_part" style="color:red;">*</span><span style="color:red;"><?php echo form_error('maxstock');?></span></label>
														<input type="text" class="form-control" name="maxstock" id="maxstock" value="<?php echo $editdata->maxstock;?>">
														
													</div>
												</div>
												
												
												
												<div class="col-md-3">
													<div class="form-group">
														<label>Min Stock/Level <span id="error_machine_part" style="color:red;">*</span><span style="color:red;"><?php echo form_error('minstock');?></span></label>
														<input type="text" class="form-control" name="minstock" id="minstock" value="<?php echo $editdata->min_stock;?>">
														
													</div>
												</div>
												
												
												
												<div class="col-md-3">
													<div class="form-group">
														<label>Rorder Level <span id="error_machine_part" style="color:red;">*</span><span style="color:red;"><?php echo form_error('reorder');?></span></label>
														<input type="text" class="form-control" name="reorder" id="reorder" value="<?php echo $editdata->reorder;?>">
														
													</div>
												</div>
												
												
                                            </div>

                                            <div style="clear:both;height: 10px;"></div>

											<div class="row">
											<div class="col-md-12">
												<h4 style="font-weight: bold;">Stock Conversion</h4>
												<hr/>
											<div class="col-md-2">
											<input type="checkbox" name="addconve" id="addconve" value="1" onchange="addnewconversionrow();" <?php  if($editdata->conversion_appl==1){?> checked <?php } ?>>&nbsp;Conversion Unit Applicable?									
											</div>

											<div class="col-md-3 stockconversion" style="display: none;">
											<div class="form-group">
											<label>Unit <span id="error_machine_part" style="color:red;">*</span><span style="color:red;"><?php echo form_error('unit');?></span></label>
											<select class="form-control" name="stockunit" id="stockunit">
											<option value="">Select Unit</option>
											<?php 
											$query = $this->db->select('id, shortname')->from('units')->order_by('name','ASC')->get();
											foreach($query->result() as $row){
											?>
											<option value="<?php echo $row->id;?>" <?php if($editdata->conversion_stock_unit==$row->id){?> selected <?php } ?>><?php echo strtoupper($row->shortname);?></option>
											<?php }?>
											</select>
											</div>
											</div>


											<div class="col-md-3 stockconversion" style="display:none;">
													<div class="form-group">
														<label>Conversion Value <span id="error_machine_part" style="color:red;">*</span><span style="color:red;"><?php echo form_error('reorder');?></span></label>
														<input type="text" class="form-control" name="convalue" id="convalue" value="<?php echo $editdata->conversion_value;?>">
														
													</div>
												</div>
												

												<script type="text/javascript">
													
													function addnewconversionrow()
													{
														if($('#addconve').is(":checked"))
														{
															$(".stockconversion").css('display','');
															$(".stockconversion :input").attr('required',true);
														}else { 
															$(".stockconversion").css('display','none');
															$(".stockconversion :input").attr('required',false);
														}
													}

												</script>




											</div>

											</div>

									
							
                                   
                                   	 <div style="clear:both;height: 10px;"></div>

								
								<?php
											$deta=$this->db->select('a.id,a.vendorid,a.price,a.listprice,a.discount')->from('vendors_price a')->join('vendors b','a.vendorid=b.id')->where('a.masterid',$id)->get();
											if($deta->num_rows()>0)
											{
											?>
									<div class="row">
											<h4 style="font-weight:bold; margin-left:20px">Existing Suppliers</h4>
											<hr>
											<?php
											foreach($deta->result() as $deta1)
											{
												
												?>
												<input type="hidden" name="exissuppl[]" value="<?php echo $deta1->id;?>">
											<div class="col-md-12">
											
											
												 <div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">VENDOR </label>
														 <span id="error_item_name" style="color:red;"></span>
														<select class="form-control select2" name="exivendor<?php echo $deta1->id;?>" id="exitvendor" style="font-size:13px;">
														<option value="">--SELECT VENDOR--</option>
														<?php 
														
														$query = $this->db->select('id,name')->from('vendors')->where('status','1')->order_by('name','ASC')->get();
														foreach($query->result() as $vendor){
														?>
														<option value="<?php echo $vendor->id;?>" <?php if($vendor->id==$deta1->vendorid){?> selected <?php } ?>><?php echo $vendor->name;?></option>
														<?php }?>
														</select>
													</div>
												</div>
													<div class="col-md-3">
													<div class="form-group">
														<label>LIST PRICE</label>
														<input type="text" name="exilprice<?php echo $deta1->id;?>" id="exilprice<?php echo $deta1->id;?>" step="0.2" class="form-control allow_decimal" value="<?php echo floatval($deta1->listprice);?>" onkeyup="getdiscountpriceforexisting(<?php echo $deta1->id;?>);">
													</div>
												</div>
												
												
													<div class="col-md-2">
													<div class="form-group">
														<label>DISCOUNT IN %</label>
														<input type="text" name="exidiscount<?php echo $deta1->id;?>" id="exidiscount<?php echo $deta1->id;?>" step="0.2" class="form-control allow_decimal" value="<?php echo floatval($deta1->discount);?>" onkeyup="getdiscountpriceforexisting(<?php echo $deta1->id;?>);">
													</div>
												</div>
											
												<div class="col-md-2">
													<div class="form-group">
														<label>DISCOUNTED PRICE</label>
														<input type="text" name="exiprice<?php echo $deta1->id;?>" id="exiprice<?php echo $deta1->id;?>"  class="form-control allow_decimal" value="<?php echo floatval($deta1->price);?>" readonly>
													</div>
												</div>

												<div class="col-md-1">
													<div class="form-group" style="margin-top:25px">
														<button type="button" class="btn btn-xs btn-warning" name="add" id="" onclick="deletesupplier(<?php echo $deta1->id;?>);"><i class="fa fa-close"></i></button>
													</div>
												</div>
											</div>
										<?php
											}
											}
											?>
                                            </div>
											
											
											
												<div class="row">
											
											<div class="col-md-12">
											<div class="col-md-4">
											<input type="checkbox" name="addsuppl" id="addsuppl" value="1" onchange="addnewrow();">Add new supplier
											
											</div>
											</div>
											
											<div class="col-md-12 suppl" style="display:none;margin-top:10px;">
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
														<input type="text" name="lprice[]" id="lprice0" value="" step="0.2" class="form-control allow_decimal" onkeyup="getdiscountpricefornew(0);" >
													</div>
												</div>
												
												
												<div class="col-md-2">
													<div class="form-group">
														<label>DISCOUNT</label>
														<input type="text" name="discount[]" id="discount0" value="" step="0.2" class="form-control allow_decimal" onkeyup="getdiscountpricefornew(0);">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														<label>DISCOUNTED PRICE</label>
														<input type="text" name="price[]" id="price0" value="" class="form-control allow_decimal" readonly required>
													</div>
												</div>

												<div class="col-md-1">
													<div class="form-group" style="margin-top:25px">
														<button type="button" class="btn btn-warning btn-xs" name="add" id="addmore_btn1"><i class="fa fa-plus"></i></button>
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
	addnewconversionrow();
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

 
 $('#dynamictasks1').append('<div id="row'+i+'" class="row"><div class="col-md-12"><div class="col-md-4"><div class="form-group"><label for="field-2" class="control-label">VENDOR </label><span id="error_item_name" style="color:red;"></span><select class="form-control select2'+i+'" name="vendor[]" id="vendor"  style="font-size:13px;"><option value="">--SELECT VENDOR--</option><?php $query = $this->db->select('id,name')->from('vendors')->where('status','1')->get(); foreach($query->result() as $vendor){?><option value="<?php echo $vendor->id;?>"><?php echo $vendor->name;?></option><?php }?></select></div></div><div class="col-md-2"><div class="form-group"><label>LIST PRICE</label><input type="text" name="lprice[]" id="lprice'+i+'" value="" class="form-control" onkeyup="getdiscountpricefornew('+i+'); allowdecimalvalues(lprice'+i+');"></div></div><div class="col-md-2"><div class="form-group"><label>DISCOUNT</label><input type="text" name="discount[]" id="discount'+i+'" value="" class="form-control allow_decimal" onkeyup="getdiscountpricefornew('+i+');"></div></div><div class="col-md-2"><div class="form-group"><label>DISCOUNTED PRICE</label><input type="text" name="price[]" id="price'+i+'" value="" class="form-control allow_decimal" readonly></div></div><div class="col-md-1"><div class="form-group pull-left"><label for="field-1" class="control-label">&nbsp;</label><br/><button type="button" name="add" class="btn_remove btn-xs  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div></div><br/>');
 initializeSelect2('select2'+i);
  i++;
 });
 
 
 
 $(document).ready(function(){

 $('.allow_numeric').keypress(function (event) {
            return isNumber(event, this)
        });

 
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



function isNumber(evt, element) {

        var charCode = (evt.which) ? evt.which : event.keyCode

        if (
            (charCode != 45 || $(element).val().indexOf('-') != -1) &&      // “-” CHECK MINUS, AND ONLY ONE.
            (charCode != 46 || $(element).val().indexOf('.') != -1) &&      // “.” CHECK DOT, AND ONLY ONE.
            (charCode < 48 || charCode > 57))
            return false;

        return true;
    }    

 function initializeSelect2(selectElementObj) {
         $('.'+selectElementObj).select2({});
         
      }
	  
	  function addnewrow()
	  {
		 if($('#addsuppl').is(":checked"))
		 {
			$(".suppl").css('display','');
			
	  }else
	  {
		  $(".suppl").css('display','none');
		  
	  }
	  }


function deletesupplier(id)
{
	var recid="<?php echo $this->uri->segment(3);?>";
	if(confirm('Do you really want to delete?'))
	{
		document.location="<?php echo page_url;?>Store/deletesupplier/"+id+"/"+recid;

		
	}else{
		return false;
	}
	
}

  $('.allow_numeric').keyup(function () { 
    this.value = this.value.replace(/[^0-9\.]/g,'');
});


function checkno(vall)
{
   
   var self = $(".all"+vall);

   self.val(self.val().replace(/[^0-9\.]/g, ''));
   if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
   {
       evt.preventDefault();
   }

}


 function getdiscountpriceforexisting(id)
{
	var lprice=$("#exilprice"+id).val();
	var exdis=$("#exidiscount"+id).val();

//alert(lprice);
	
	if((lprice!='' || lprice!=0))
	{
		
		
		if(exdis!=0)
		{
			var d=exdis/100;
			
			var lp=lprice*d;
			
			var fin=lprice-lp;
			
			$("#exiprice"+id).val(fin.toFixed(2));
		}else{
			
			$("#exiprice"+id).val(lprice);
		}
		
		
	}else{
		
		alert('List Price is required');
		$("#exiprice"+id).val('');
		$("#exidiscount"+id).val('');
	}
	
	
	
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


function checkformaxlevel()
{
var consump=$("#consumption").val();
var safety=$("#safety").val();
var ltime=$("#ltime").val();
if(ltime!='')
{
var safetyvalue=parseFloat(safety);
var consumptionvalue=parseFloat(consump)/365;

var maxlevel=parseFloat(ltime)*parseFloat(safetyvalue)*parseFloat(consumptionvalue);

var mxlebel=Math.ceil(maxlevel);
$("#maxstock").val(mxlebel);
var rorder=66/100;
var rorderq=parseFloat(mxlebel)*rorder;
var finalrorder=parseFloat(mxlebel)-parseFloat(rorderq);
$("#reorder").val(Math.ceil(finalrorder));

var minorder=33/100;
var minorderq=parseFloat(mxlebel)*minorder;
var minfinalrorder=parseFloat(mxlebel)-parseFloat(minorderq);
$("#minstock").val(Math.ceil(minfinalrorder));

}else
{
$("#maxstock").val('');
$("#minstock").val('');
$("#reorder").val('');

}



}


function allowdecimalvalues(ect)
{

 var self = $(ect);
   self.val(self.val().replace(/[^0-9\.]/g, ''));
   if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
   {
     evt.preventDefault();
   }

}	
	  </script>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
    </body>
</html>
