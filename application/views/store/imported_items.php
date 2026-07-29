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
<style>
table.manglesh thead th {
				background: #003366;
				color:#fff;
				font-weight:bold;
				text-align:left;
			}
		table.manglesh tbody td {
				text-align:left;
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
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						 <!---<div class="btn-group pull-right">
						  <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">Add New Item</button>
                               
                            </div>--->
                           
                            <h4 class="page-title">Imported Item List</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
				<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
				<?php 
				$user_id =$this->session->userdata['logged_in']['user_id'];	
				
				$query = $this->db->select('per_unit_price, opening_stock, min_qty, total_received, total_issued, issue_item, receive_item,block_items')->from('imported_items_permission')->where('user_id',$user_id)->get();
					foreach($query->result() as $row);
				
				?>
				
				<div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table manglesh table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>SR NO</th>
									 <th>Instrument Name</th>
									 <th>Actual Stock</th>
									 <th>Blocked against order</th>
									 <th>Extra Stock</th>
									 <th>SERIAL NUMBER</th>
									  <?php if($row->opening_stock=='1'){?>
									 <th>Opening Stock<br>(Qty. in Pcs)</th>
									 <?php }?>
									  <?php if($row->min_qty=='1'){?>
									 <th>Min. Qty.</th>
									 <?php }?>
									  <?php if($row->total_received=='1'){?>
									 <th>Total Received</th>
									 <?php }?>
									  <?php if($row->total_issued=='1'){?>
									 <th>Total Issued </th>
									 <?php }?>
									  <?php if($row->issue_item=='1'){?>
									 <th>Update Status</th>
									 <?php }?>
									  <?php if($row->receive_item=='1'){?>
									 <th>Item Receive</th>
									 <?php }?>
									 <?php if($row->block_items=='1'){?>
									 <th>Block Item</th>
									 <?php }?>
									
									
                                </tr>
                                </thead>
								<tbody></tbody>
                                
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
 <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="<?php echo page_url;?>Store/add_imported_item"  enctype="multipart/form-data">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Add New Item</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                            
												<div class="col-md-12">
													<div class="form-group">
														 <label for="field-2" class="control-label">Instrument Name</label>
														 <span id="error_description" style="color:red;"></span>
														 <input type="text" class="form-control" name="description" id="description"  value="" required>
													</div>
												</div>
												
												 <div class="col-md-6">
													<div class="form-group">
														 <label for="field-2" class="control-label">Current Stock</label>
														 <span id="error_current_stock" style="color:red;"></span>
														 <input type="number" class="form-control" name="current_stock" id="current_stock"  value="">
													</div>
												</div>
												 
												 <div class="col-md-6">
													<div class="form-group">
														 <label for="field-2" class="control-label">Opening Stock</label>
														 <span id="error_qty" style="color:red;"></span>
														 <input type="number" class="form-control" name="qty" id="qty"  value="">
													</div>
												</div>
												
												 <div class="col-md-6">
													<div class="form-group">
														 <label for="field-2" class="control-label">Minimum Qty</label>
														 <span id="error_min_qty" style="color:red;"></span>
														 <input type="number" class="form-control" name="min_qty" id="min_qty"  value="">
													</div>
												</div>
												
												 <div class="col-md-6">
													<div class="form-group">
														 <label for="field-2" class="control-label">Per Unit Price</label>
														 <span id="error_price" style="color:red;"></span>
														 <input type="number" class="form-control" name="price" id="price"  value="" step="0.01" required>
													</div>
												</div>
										
											</div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div><!-- /.modal -->





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
"pageLength": 100,
"sAjaxSource": "<?php echo page_url;?>Store/imported_items_list",
"aoColumns": [
				{ mData: 'sr_no' } ,
				{ mData: 'description' },
				{ mData: 'current_stock' },
				{ mData: 'blocked' },
				{ mData: 'extra' },
				{mData:'machine_serial_number'}
				<?php if($row->opening_stock=='1'){?>
				,{ mData: 'opening_stock' }
				<?php }?>
				<?php if($row->min_qty=='1'){?>
				,{ mData: 'min_stock' }
				<?php }?>
				<?php if($row->total_received=='1'){?>
				,{ mData: 'total_received' }
				<?php }?>
				<?php if($row->total_issued=='1'){?>
				,{ mData: 'total_issued' }
				<?php }?>
				<?php if($row->issue_item=='1'){?>
				,{ mData: 'update_status' }
				<?php }?>
				<?php if($row->receive_item=='1'){?>
				,{ mData: 'receive_item' }
				<?php }?>
				<?php if($row->block_items=='1'){?>
				,{ mData: 'block_item' }
				<?php }?>
				
				
				
		]
});   
});

</script>
		
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

var current_stock = $("#current_stock").val();
if(current_stock=='')
{
	$("#error_current_stock").html('Required!');
}


if(description=='' || qty=='' || min_qty=='' || current_stock=='')
{
	
	return false;
}

});
});
</script>
    </body>
</html>