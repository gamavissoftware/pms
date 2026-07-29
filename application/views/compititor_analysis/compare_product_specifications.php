<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Compare Competitor Products</title>

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
        <link href="<?php echo assets_url;?>plugins/select2/dist/css/select2-bootstrap.css" rel="stylesheet" type="text/css">
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
                           
                            <h4 class="page-title">Compare Competitor Products</h4>
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
									<form method="post" action="<?php echo page_url;?>Competitor_analysis/filter_data/">
									<!--	<div class="col-md-2">
											<div class="form-group">
												<label>Category <span style="color:red;" id="error_category_name"></span></label>
												<select class="form-control select3" id="category_name" name="category_name">
												<option value="">--Select Category--</option>
												 <?php $query = $this->db->select('prd_category_id, product_category')->from('product_category')->where('status','1')->get();
												 foreach($query->result() as $category){?>
												 <option value="<?php echo $category->prd_category_id;?>"><?php echo $category->product_category;?></option>
												 <?php }?>
												</select>
											</div>
										</div>-->
										<div class="col-md-3">
											<div class="form-group">
												<label>Product Name </label>
												 <select class="select2 form-control" name="product_name" id="product_name" onchange="get_competitor()">
												 <?php 
												 $productid = $this->uri->segment(3);
												 if($productid)
													$query = $this->db->select('product_id, product_name')->from('product_specifications')->where('product_id',$this->uri->segment(3))->get();
													foreach($query->result() as $product_detail)
												
												 ?>
												 <option value="">--Select Product Name--</option>
												 <?php $query = $this->db->select('product_id, product_name')->from('product_specifications')->where('status','1')->get();
												 foreach($query->result() as $products){?>
												 <option value="<?php echo $products->product_id;?>" <?php if($productid){
												 if($product_detail->product_id==$products->product_id){echo "selected";}}?>><?php echo $products->product_name;?></option>
												 <?php }?>
												 </select>
												<span style="color:red;" id="error_product_name"></span>

											</div>
										</div>
											<div class="col-md-3">
											<div class="form-group">
												<label>Location</label>
												 <select class="form-control select4" name="location" id="location" onchange="get_competitor(this.value)">
												  <option value="">--Select Location--</option>
											     <?php $query = $this->db->select('state_id, state_name')->from('states')->where('country_id','101')->get();
												 foreach($query->result() as $states){?>
												 <option value="<?php echo $states->state_id;?>"><?php echo $states->state_name;?></option>
												 <?php }?>
												 </select>
												 <span style="color:red;" id="error_location"></span>

											</div>
										</div>
										  <script type="text/javascript">
											
													function get_competitor(loc){
														
												    var location=$("#location").val();
													var product_name=$("#product_name").val();
                                              /*  	if(!product_name)
                                                    {
                                                    jQuery('#product_name').parent('.form-group').after('<small class="text-danger">Please select product name first.</small>');
                                                    jQuery('.text-danger').delay(3200).fadeOut(300);
                                                    return false;
                                                    }
                                                    if(!location)
                                                    {
                                                    jQuery('#location').parent('.form-group').after('<small class="text-danger">Please select location.</small>');
                                                    jQuery('.text-danger').delay(3200).fadeOut(300);
                                                    return false;
                                                    }*/
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Competitor_analysis/select_locations",
													data:"location="+location+"&product_name="+product_name,
													success:function(data){
													$("#competitor_name").html(data);
													}
													});
													}
											   


											      $( document ).ready(function() {
											       
												    var location=$("#location").val();
													var product_name=$("#product_name").val();
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Competitor_analysis/select_locations",
													data:"location="+location+"&product_name="+product_name,
													success:function(data){
													$("#competitor_name").html(data);
													}
													});
													
														});
												</script>

										<div class="col-md-3">
											<div class="form-group">
												<label>Competitor Name</label>
												 <select class="form-control select4" name="competitor_name" id="competitor_name">
												  <option value="">--Select Competitor Name--</option>
												
												 
												 </select>
											</div>
										</div>
									<!--	<div class="col-md-2">
											<div class="form-group">
												<label>Zone</label>
												 <select class="form-control" name="zone" id="zone">
												  <option value="">--Select Zone--</option>
												<?php $query = $this->db->select('zone_id, zone, status')->from('working_zone')->where('status','1')->get();
												 foreach($query->result() as $zone){?>
												 <option value="<?php echo $zone->zone_id;?>"><?php echo $zone->zone;?></option>
												 <?php }?>
												 
												 </select>
												 <script type="text/javascript">
											
													$("#zone").change(function(){
													var zone=$("#zone").val();
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Competitor_analysis/select_zones",
													data:"zone="+zone,
													success:function(data){
													$("#location").html(data);
													}
													});
													});
											
												</script>
											</div>
										</div>-->
									
										
										<div class="col-md-9"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" id="save" class="btn btn-success" value="Search">
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
	$('.select3').select2({ });
	$('.select4').select2({ });
	
$("#save").click(function() {
var product_name = $("#product_name").val();
var location = $("#location").val();
if(product_name=='')
{
	$("#error_product_name").html('Please select product name');
}

if(location=='')
{
	$("#error_location").html('Please select customer location');
}


if(product_name=='' || location=='')
{
	
	return false;
}

});
});
</script>
 <script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
    </body>
</html>