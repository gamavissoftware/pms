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
?>
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
            <div class="container-fluid">

                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right">
						  <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">Add New House Keeping Item</button>
                               
                            </div>
                           
                            <h4 class="page-title">All House Keeping Items List</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
				<div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>SR NO</th>
									 <th>Category</th>
									 <th>Item Name</th>
									 <th>Qty</th>
									 <th>Minimum Qty</th>
									 <th>Unit</th>
									 <th>Picture</th>
									 <th>GST(%) </th>
									 <th>Remarks</th>
									 <th>Vendor Wise Price</th>
									 <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
								<tbody></tbody>
                                
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
 <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="<?php echo page_url;?>Store/add_general_items"  enctype="multipart/form-data">
      <input type="hidden" name="masterreqid" value="<?php echo $masterreqid;?>">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Add New House Keeping Item</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                            
												<div class="col-md-6">
													<div class="form-group">
														 <label for="field-2" class="control-label">Item Name</label>
														 <span id="error_item_name" style="color:red;"></span>
														 <input type="text" class="form-control" name="item_name" id="item_name"   value="<?php echo $name;?>" required>
													</div>
												</div>
												 <div class="col-md-6">
													<div class="form-group">
														 <label for="field-2" class="control-label">Picture</label>
														 <span id="error_item_name" style="color:red;"></span>
														 <input type="file" class="form-control" name="picture" id="picture"  value="">
													</div>
												</div>
												
												 <div class="col-md-6">
													<div class="form-group">
														 <label for="field-2" class="control-label">Qty</label>
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
												<label for="field-2" class="control-label">Unit</label>
												<span id="error_unit" style="color:red;"></span>
												<select class="form-control" id="unit" name="unit">
													<option value="">--Select Unit--</option>
													<?php 
													$query = $this->db->select('id, name')->from('units')->order_by('name','asc')->get();
													foreach($query->result() as $row){
													    
													    if($row->id==$unit)
														{
															$a="Selected";
														}else
														{
															$a="";
														}
													?>
													<option value="<?php echo $row->id;?>" <?php echo $a;?>><?php echo $row->name;?></option>
													<?php }?>
												</select>
												</div>
											</div>
												
												<div class="col-md-6">
												<div class="form-group">
												<label for="field-2" class="control-label">Status</label>
												<span id="error_status" style="color:red;"></span>
												<select class="form-control" id="status" name="status">
													<option value="">--Select Status--</option>
													<option value="1">Active</option>
													<option value="0">Inactive</option>
												</select>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label>Category<span id="error_category" style="color:red;">*</span></label>
													<select class="form-control" id="category" name="category">
													<option value="">--Select Category--</option>
													<?php $q= $this->db->select('id,category')->from('presto_machine_part_category')->where('cattype','2')->get();
													foreach($q->result() as $row){?>
													<option value="<?php echo $row->id;?>"><?php echo $row->category;?></option>
													<?php }?>
													
												</select>
												</div>
												<div class="form-group">
												    <label>GST IN %</label>
												    <input type="number" name="gst" id="gst" value="" class="form-control">
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label>Remarks</label>
													<textarea class="form-control" name="remarks" id="remarks"></textarea>
												</div>
											</div>
											</div>
											<div class="row">
											<div class="col-md-12">
												 <div class="col-md-8">
													<div class="form-group">
														 <label for="field-2" class="control-label">Vendor </label>
														 <span id="error_item_name" style="color:red;"></span>
														<select class="form-control" name="vendor[]" id="vendor" style="font-size:11px;">
														<option value="">--Select Vendor--</option>
														<?php 
														
														$query = $this->db->select('id, name')->from('vendors')->where('status','1')->get();
														foreach($query->result() as $vendor){
														?>
														<option value="<?php echo $vendor->id;?>"><?php echo $vendor->name;?></option>
														<?php }?>
														</select>
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group">
														<label>Price</label>
														<input type="number" name="price[]" id="price" value="" step="0.2" class="form-control">
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
"sAjaxSource": "<?php echo page_url;?>Store/housekeeping_item_list",
"aoColumns": [
				{ mData: 'sr_no' } ,
				{ mData: 'category' },
				{ mData: 'item_name' },
				{ mData: 'qty' },
				{ mData: 'min_qty' },
				{ mData: 'name' },
				{ mData: 'picture' },
					{ mData: 'gst' },
				{ mData: 'remarks' },
				{mData:'vendorwise'},
				{ mData: 'status' },
				{ mData: 'edit' }
				
				
		]
});   
});

</script>
<script type="text/javascript">
$(document).ready(function(){
var i=1;
 $('#addmore_btn1').click(function(){
 i++;
 
 $('#dynamictasks1').append('<div id="row'+i+'" class="row"><div class="col-md-12"><div class="col-md-8"><div class="form-group"><label for="field-2" class="control-label">Vendor </label><span id="error_item_name" style="color:red;"></span><select class="form-control" name="vendor[]" id="vendor" onChange="fetch_machine(0);" style="font-size:11px;"><option value="">--Select Vendor--</option><?php $query = $this->db->select('id,name')->from('vendors')->where('status','1')->get(); foreach($query->result() as $vendor){?><option value="<?php echo $vendor->id;?>"><?php echo $vendor->name;?></option><?php }?></select></div></div><div class="col-md-3"><div class="form-group"><label>Price</label><input type="number" name="price[]" id="price" value="" step="0.2" class="form-control"></div></div><div class="col-md-1"><div class="form-group pull-left"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div></div><br/>');
 initializeSelect2('select3'+i);
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
var item_name = $("#item_name").val();
if(item_name=='')
{
	$("#error_item_name").html('Required!');
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
var unit = $("#unit").val();
if(unit=='')
{
	$("#error_unit").html('Required!');
}


var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}


if(item_name=='' || qty=='' || min_qty=='' || unit=='' || status=='' )
{
	
	return false;
}

});
});
</script>
<?php if($this->uri->segment(3)<>'')
{
?>	
<script>
$(document).ready(function() {
	$("#con-close-modal").modal('show');
});
</script>	
	
<?php
}
?>
    </body>
</html>