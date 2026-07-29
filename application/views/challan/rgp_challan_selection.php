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
						
                            <h4 class="page-title text-center">SELECT NRGP CHALLAN</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
               <div class="row">
			   <div class="col-md-4"></div>
                   <div class="col-lg-2 col-md-2 col-xs-12 col-sm-12 ">
                        <a href="<?php echo page_url;?>Challan/rgp_challan_items/1"><div class="card-box">
                            <div class="text-center">
                                
							<div class="text-center">
                                 <div class="row">
								<div class="col-md-12"><h4>Instrument RGP</h4>
								</div>
								
							</div>
                            </div>
                            </div>
                        </div></a>
							</div>
							
							<div class="col-lg-2 col-md-2 col-xs-12 col-sm-12 ">
                        <a href="<?php echo page_url;?>Challan/rgp_challan_items/1"><div class="card-box">
                            <div class="text-center">
                                
							<div class="text-center">
                                 <div class="row">
								<div class="col-md-12"><h4>General Items RGP</h4>
								</div>
								
							</div>
                            </div>
                            </div>
                        </div></a>
							</div>
							<div class="col-md-4"></div>

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
 
 $('#dynamictasks').append('<div id="row'+i+'" class="row"><div class="col-md-12"><div class="col-md-3"><div class="row"><div class="col-md-6"><div class="form-group"><label for="field-1" class="control-label">ITEM <span style="color:red;0">*</span></label><span id="error_itemname" style="color:red;"></span><input type="text" class="form-control" name="itemname[]" id="itemname" value=""></div></div>	<div class="col-md-6"><div class="form-group"><label for="field-1" class="control-label">Item Description</label><textarea name="item_description[]" id="item_description" class="form-control"></textarea></div></div></div></div><div class="col-md-1"><div class="form-group"><label for="field-1" class="control-label">PO No.<span style="color:red;0">*</span></label><span id="error_qty" style="color:red;"></span><input  type="text" class="form-control" id="pono" name="pono[]" placeholder="PO No"  required="required" style="text-transform:uppercase"></div></div><div class="col-md-1"><div class="form-group"><label for="field-1" class="control-label">Qty<span style="color:red;0">*</span></label><span id="error_qty" style="color:red;"></span><input type="number" min="0.1" step="0.01" class="form-control" id="qty" name="qty[]" placeholder="Quantity" required="required" style="text-transform:uppercase"></div></div><div class="col-md-1"><div class="form-group"><label for="field-1" class="control-label">Unit <span style="color:red;0">*</span></label><select class="form-control" required name="unit[]" id="unit"><option value="">Select Unit</option><?php $query = $this->db->select('shortname')->from('units')->get();foreach($query->result() as $row){?><option value="<?php echo $row->shortname;?>"><?php echo $row->shortname;?></option><?php }?></select></div></div> <div class="col-md-1"><div class="form-group"><label for="field-1" class="control-label">Pckts/ Cartons<span style="color:red;0">*</span></label><span id="error_qty" style="color:red;"></span><input  type="text" class="form-control" id="Pckts" name="Pckts[]" placeholder="Pckts/ Cartons"  required="required" style="text-transform:uppercase"></div></div><div class="col-md-1"><div class="form-group"><label for="field-1" class="control-label">Returnable <span style="color:red;0">*</span></label><select class="form-control" name="returnable[]" id="returnable" onchange="checkifreturn('+i+',this.value)"><option value="">Select</option><option value="1">Yes</option><option value="0" checked>No</option></select></div></div><div class="col-md-1 returndate'+i+'"><div class="form-group"><label for="field-1" class="control-label">Date of Ret</label> <input type="text" class="form-control datepicker-autoclose date" placeholder="mm/dd/yyyy" id="expected_date" name="expected_date[]" ></div></div><div class="col-md-1"><div class="form-group"><label for="field-1" class="control-label">Billable <span style="color:red;0">*</span></label><select class="form-control" name="billable[]" id="billable" required><option value="1">Yes</option><option value="0" checked>No</option></select></div></div>	<div class="col-md-1"><div class="form-group"><label for="field-1" class="control-label">Bill No. <span style="color:red;0">*</span></label><input type="text" class="form-control" name="billno[]" id="billno" value=""></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:30px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div></div><br/>');
 
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