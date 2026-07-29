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
                           
                            <h4 class="page-title">Edit Item</h4>
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
								$this->db->select('id,description,current_stock, opening_stock, min_stock, price')->from('imported_items')->where('id',$id);
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $items)
								?>
								
                                   <form method="post" action="<?php echo page_url;?>Store/update_imported_items/<?php echo $items->id;?>"  enctype="multipart/form-data">
								   
										  <div class="col-md-6">
													<div class="form-group">
														 <label for="field-2" class="control-label">Description</label>
														 <span id="error_description" style="color:red;"></span>
														 <input type="text" class="form-control" name="description" id="description"  value="<?php echo $items->description;?>" required>
													</div>
												</div>
												
												 <div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Current Stock</label>
														 <span id="error_current_stock" style="color:red;"></span>
														 <input type="number" class="form-control" name="current_stock" id="current_stock"  value="<?php echo $items->current_stock;?>">
													</div>
												</div>
												
											<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Opening Stock</label>
														 <span id="error_qty" style="color:red;"></span>
														 <input type="number" class="form-control" name="qty" id="qty"  value="<?php echo $items->opening_stock;?>">
													</div>
												</div>
												
												 <div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Minimum Qty</label>
														 <span id="error_min_qty" style="color:red;"></span>
														 <input type="number" class="form-control" name="min_qty" id="min_qty"  value="<?php echo $items->min_stock;?>">
													</div>
												</div>
												
												 <div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Price Per Unit</label>
														 <span id="error_min_qty" style="color:red;"></span>
														 <input type="number" class="form-control" name="price_per_unit" id="price_per_unit"  value="<?php echo $items->price;?>">
													</div>
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
$("#save").click(function() {
var description = $("#description").val();
if(description=='')
{
	$("#error_description").html('Required!');
}

var qty = $("#qty").val();
if(qty=='')
{
	$("#error_qty").html('Required!');
}

var min_qty = $("#min_qty").val();
if(min_qty=='')
{
	$("#error_min_qty").html('Required!');
}


if(description=='' || qty=='' || min_qty=='')
{
	
	return false;
}

});
});
</script>
    </body>
</html>