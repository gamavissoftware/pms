<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Edit Product Detail</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
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
                           
                            <h4 class="page-title">Edit Product Detail</h4>
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
								$this->db->select('*')->from('presto_product_specifications')->where('product_id',$id);
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $products)
								?>
								
                                   <form method="post" action="<?php echo page_url;?>Quotation/update_products_detail/<?php echo $products->product_id;?>" enctype="multipart/form-data">
								   <input type="hidden" name="old_img" value="<?php echo $products->product_image;?>">
										<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">Product Name</label>
														 <span id="error_product_name" style="color:red;">*</span>
														 <input type="text" class="form-control" name="product_name" id="product_name" placeholder="Product Name" value="<?php echo $products->product_name;?>">
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">Product Image</label>
														  <Span id="contact_error" style="color:red;">*</span>
														 <input type="file" class="form-control" name="photo" id="photo"  value="">
														 <img src="<?php echo product_path;?><?php echo $products->product_image;?>" width="100px">
													</div>
												
												</div>
												<div class="col-md-4">
												    
												    	<div class="form-group">
												<label for="field-2" class="control-label">Status</label>
												<span id="error_status" style="color:red;">*</span>
												<select class="form-control" id="status" name="status">
													<option value="<?php echo $products->status;?>">--Select Status--</option>
													<option value="1" <?php if($products->status=='1'){echo "selected";}?> >Active</option>
													<option value="0" <?php if($products->status=='0'){echo "selected";}?>>Inactive</option>
												</select>
												</div>
												</div>
												
			<?php 
				$query =$this->db->select('*')->from('quotation_product_specifications')->where('product_id',$products->product_id)->get();
				$res= $query->result();
				foreach($res as $specifications){
			?>									
			<div class="row">
<input type="hidden" name="record_id[]" value="<?php echo $specifications->id;?>">			
			<div class="col-md-8">
			    <div class="form-group">
		        <label for="field-2" class="control-label">Specification </label><span id="error_spacification" style="color:red;">*</span>
				<textarea class="form-control" name="specification[]" id="specification" placeholder="Product Specification"><?php echo $specifications->product_specifications;?></textarea> 
				</div>
				</div>
				
					<div class="col-md-2">
			    <div class="form-group">
		        <label for="field-2" class="control-label">Price </label>
				<span id="error_price" style="color:red;">*</span>
				<input type="text" class="form-control" name="price[]" id="price" placeholder="Product Price" value="<?php echo $specifications->price;?>">							 
				 
				</div>
				</div>
				</div>
				<?php }?>
                <div class="col-md-2">
                <div class="form-group" style="margin-top:25px">
                <button type="button" class="btn btn-warning" name="add" id="addmore_btn"><i class="fa fa-plus"></i></button>
                </div>
                </div>
				<div class="col-md-12">                                          
				<div id="dynamictasks"></div>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
 <script type="text/javascript">
         $(document).ready(function(){
			 
 var i=1;
 $('#addmore_btn').click(function(){
 i++;
 
 $('#dynamictasks').append('<div id="row'+i+'" class="row"><input type="hidden" name="record_id[]" value=""><div class="col-md-8"><div class="form-group"><label for="field-2" class="control-label">Specification </label><span id="error_spacification" style="color:red;">*</span><textarea class="form-control" name="specification[]" id="specification" placeholder="Product Specification"></textarea></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Price </label><span id="error_price" style="color:red;">*</span><input type="text" class="form-control" name="price[]" id="price" placeholder="Product Price"></div></div><div class="col-md-2"><div class="form-group pull-left" style="padding-top:30px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div><br/>');
 
 });
 
 
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id+'').remove();
 });
 
});
      </script> 
<script language="javascript" type="text/javascript">   
jQuery.noConflict();
$(document).ready(function() {
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
    </body>
</html>