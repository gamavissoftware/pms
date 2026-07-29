<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Prestogroup Set IMS header Permission</title>

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
                           
				<?php
				$user_id=$this->uri->segment(4);
				$userd=$this->db->select('user_id, title, first_name, last_name')->from('system_users')->where('user_id',$user_id)->get();
				foreach($userd->result() as $users);
				?>

	                            <h4 class="page-title text-center">Set IMS/Imported Items Header Permission for <?php echo $users->title;?> <?php echo $users->first_name;?> <?php echo $users->last_name;?><br><span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>  </h4><hr>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">
<?php 
$query = $this->db->select('qty, vendor, price,bstock,gst')->from('ims_header_permission')->where('user_id',$this->uri->segment(4))->get();
foreach($query->result() as $row);
?>
                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">

								 <form method="post" action="<?php echo page_url;?>Master/User_management/set_ims_header_permission/<?php echo $this->uri->segment(4);?>"  enctype="multipart/form-data">
								 <div class="row">
								 				<div class="col-md-3">
														 <label for="field-2" class="control-label">STOCK</label>
														 <span id="error_reference_title" style="color:red;"></span>
														 <select class="form-control" name="min_qty" id="min_qty">
														 <option value="0" <?php if($row->qty=='0'){echo "selected";}?>>NO</option>
														 <option value="1" <?php if($row->qty=='1'){echo "selected";}?>>YES</option>
														 </select>
													</div>
											
											<div class="col-md-3">
														 <label for="field-2" class="control-label">VENDOR NAME</label>
														 <span id="error_reference_title" style="color:red;"></span>
														 <select class="form-control" name="vendor" id="vendor">
														 <option value="0" <?php if($row->vendor=='0'){echo "selected";}?>>NO</option>
														 <option value="1" <?php if($row->vendor=='1'){echo "selected";}?>>YES</option>
														 </select>
													</div>
												
												<div class="col-md-3">
														 <label for="field-2" class="control-label">ITEM PRICE</label>
														 <span id="error_reference_title" style="color:red;"></span>
														 <select class="form-control" name="item_price" id="item_price">
														 <option value="0" <?php if($row->price=='0'){echo "selected";}?>>NO</option>
														 <option value="1" <?php if($row->price=='1'){echo "selected";}?>>YES</option>
														 </select>
													</div>
													
														<div class="col-md-3">
														 <label for="field-2" class="control-label">BLOCKED STOCK</label>
														 <span id="error_reference_title" style="color:red;"></span>
														 <select class="form-control" name="blocked" id="blocked">
														 <option value="0" <?php if($row->bstock=='0'){echo "selected";}?>>NO</option>
														 <option value="1" <?php if($row->bstock=='1'){echo "selected";}?>>YES</option>
														 </select>
													</div>
													
													
														<div class="col-md-3">
														 <label for="field-2" class="control-label">GST</label>
														 <span id="error_reference_title" style="color:red;"></span>
														 <select class="form-control" name="gst" id="gst">
														 <option value="0" <?php if($row->gst=='0'){echo "selected";}?>>NO</option>
														 <option value="1" <?php if($row->gst=='1'){echo "selected";}?>>YES</option>
														 </select>
													</div>
													
													
											<div class="col-md-3">
														 
													</div>
													</div>
												<?php 
												$query = $this->db->select('per_unit_price, opening_stock, min_qty, total_received, total_issued, issue_item, receive_item,block_items')->from('imported_items_permission')->where('user_id',$this->uri->segment(4))->get();
												foreach($query->result() as $row);
												?>	
											<div class="row">
											<div style="padding-top:30px"></div>
											<h4>Set Permission for Imported Items Data</h4><hr>
											
											<div class="col-md-3">
														 <label for="field-2" class="control-label">PER UNIT PRICE</label>
														 <span id="error_per_unit_price" style="color:red;"></span>
														 <select class="form-control" name="per_unit_price" id="per_unit_price">
														 <option value="0" <?php if($row->per_unit_price=='0'){echo "selected";}?>>NO</option>
														 <option value="1" <?php if($row->per_unit_price=='1'){echo "selected";}?>>YES</option>
														 </select>
													</div>
													
													<div class="col-md-3">
														 <label for="field-2" class="control-label">OPENING STOCK</label>
														 <span id="error_reference_title" style="color:red;"></span>
														 <select class="form-control" name="opening_stock" id="opening_stock">
														 <option value="0" <?php if($row->opening_stock=='0'){echo "selected";}?>>NO</option>
														 <option value="1" <?php if($row->opening_stock=='1'){echo "selected";}?>>YES</option>
														 </select>
													</div>
													
													
													<div class="col-md-3">
														 <label for="field-2" class="control-label">MIN QTY</label>
														 <span id="error_reference_title" style="color:red;"></span>
														 <select class="form-control" name="min_qty" id="min_qty">
														 <option value="0" <?php if($row->min_qty=='0'){echo "selected";}?>>NO</option>
														 <option value="1" <?php if($row->min_qty=='1'){echo "selected";}?>>YES</option>
														 </select>
													</div>
													
													<div class="col-md-3">
														 <label for="field-2" class="control-label">TOTAL RECEIVED</label>
														 <span id="error_total_received" style="color:red;"></span>
														 <select class="form-control" name="total_received" id="total_received">
														 <option value="0" <?php if($row->total_received=='0'){echo "selected";}?>>NO</option>
														 <option value="1" <?php if($row->total_received=='1'){echo "selected";}?>>YES</option>
														 </select>
													</div>
													
													<div class="col-md-3">
														 <label for="field-2" class="control-label">TOTAL ISSUED</label>
														 <span id="error_reference_title" style="color:red;"></span>
														 <select class="form-control" name="total_issued" id="total_issued">
														 <option value="0" <?php if($row->total_issued=='0'){echo "selected";}?>>NO</option>
														 <option value="1" <?php if($row->total_issued=='1'){echo "selected";}?>>YES</option>
														 </select>
													</div>
													
													<div class="col-md-3">
														 <label for="field-2" class="control-label">ISSUE ITEM</label>
														 <span id="error_reference_title" style="color:red;"></span>
														 <select class="form-control" name="issue_item" id="issue_item">
														 <option value="0" <?php if($row->issue_item=='0'){echo "selected";}?>>NO</option>
														 <option value="1" <?php if($row->issue_item=='1'){echo "selected";}?>>YES</option>
														 </select>
													</div>
													
													<div class="col-md-3">
														 <label for="field-2" class="control-label">RECEIVE ITEM</label>
														 <span id="error_receive_item" style="color:red;"></span>
														 <select class="form-control" name="receive_item" id="receive_item">
														 <option value="0" <?php if($row->receive_item=='0'){echo "selected";}?>>NO</option>
														 <option value="1" <?php if($row->receive_item=='1'){echo "selected";}?>>YES</option>
														 </select>
													</div>
													<div class="col-md-3">
														 <label for="field-2" class="control-label">BLOCK ITEMS</label>
														 <span id="error_block_items" style="color:red;"></span>
														 <select class="form-control" name="block_items" id="block_items">
														 <option value="0" <?php if($row->block_items=='0'){echo "selected";}?>>NO</option>
														 <option value="1" <?php if($row->block_items=='1'){echo "selected";}?>>YES</option>
														 </select>
													</div>	
													
											</div>
										<div class="col-md-12">
											<div class="form-group pull-right" style="padding-top:24px">
												<label>&nbsp;</label>
												<input type="submit" class="btn btn-success" value="Update">
											</div>
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
$("#save").click(function() {
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

if(country_name=='' || state=='' || city_name=='' || company_name=='' || address=='' || contact_number=='' )
{
	
	return false;
}

});
});
</script>
    </body>
</html>