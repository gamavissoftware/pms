<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle;?> ORDER MANAGEMENT</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.dataTables.min.css">
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script>
		<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
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
				font-size:11px;
				font-weight:bold;
			}
table tbody tr td {
  font-size: 11px;
  color:#000;
}
#pageloader
{
  background: rgba( 255, 255, 255, 0.8 );
  display: none;
  height: 100%;
  position: fixed;
  width: 100%;
  z-index: 9999;
}
#pageloader img
{
  left: 50%;
  margin-left: -32px;
  margin-top: -32px;
  position: absolute;
  top: 50%;
}
</style>
    </head>
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
                    <div class="col-sm-12">
                        <div class="page-title-box">
						
                           
                            <h4 class="page-title">ORDER PLANNING LIST</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
		<div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th>Sr No.</th>
									<th>DOCS</th>
									<th>PLAN ORDER</th>
								
									<th>ADDED ON</th>
									<th style="width:50%">INSTRUMENTS</th>
                                    <th>ORDER TYPE</th>
                                    <th>REGION MARKETING PERSON</th>
                                    <th>PO NUMBNER</th>
                                    <th>COMPANY NAME</th>
									 <th>CONTACT PERSON</th>
									 <th>DESIGNATION</th>
									<th>ADDRESS WITH PINCODE</th>
									<th>EMAIL ID</th>
									<th>MOBILE NUMBER</th>
									<th>PHONE</th>
									<th>INTERNAL ORDER NUMBER</th>
									<th>DISCOUNT</th>
									<th>ORDER VALUE AFTER DISCOUNT</th>
									<th>ADVANCE AMOUNT RECEIVED</th>
									<th>PAYMENT TERMS</th>
									<th>INSTALLATION CHARGES TYPE</th>
									<th>PACKING CHARGES</th>
									<th>FREIGHT TYPE</th>
									<th>REMARKS</th>
								
									
                                </tr>
                                </thead>


                                <tbody>
								
                                </tbody>
                            </table>
                        </div>
                    </div>
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
        <script src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>
<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
		
		<script type="text/javascript">
$(document).ready(function(){
	


 var i=2;
 $('#addmore_btn1').click(function(){
 i++;
 
 $('#dynamictasks1').append('<div id="row'+i+'" class="row"><div class="col-md-9"><div class="form-group"><label for="field-1" class="control-label">INSTRUMENT NAME</label><span id="error_instruments" style="color:red;">*</span><select class="form-control select3'+i+'" style="text-transform: uppercase;" name="instruments[]" id="instruments'+i+'"><option value="">--SELECT INSTRUMENT--</option><?php $query = $this->db->select('id, instruments_name, status')->from(' presto_instruments')->where('status','1')->get();foreach($query->result() as $instruments){?><option value="<?php echo $instruments->id;?>"><?php echo trim(strtoupper($instruments->instruments_name));?></option><?php }?></select></div></div><div class="col-md-2"><div class="form-group"><label for="field-1" class="control-label">QUANTITY</label><input type="text" class="form-control" id="qty" name="qty[]" placeholder="" required></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:30px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div><br/>');
 initializeSelect2('select3'+i);
 });
 
 
 
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id+'').remove();
 });
 
});
	  </script>
	  <script>
	  function initializeSelect2(selectElementObj) {
         $('.'+selectElementObj).select2({});
         
      }
	  </script>
		<script>
		// Time Picker
            jQuery('#timepicker').timepicker({
                defaultTIme : false
            });
			jQuery('#timepicker4').timepicker({
                defaultTIme : false
            });
            jQuery('#timepicker2').timepicker({
                showMeridian : false
            });
            jQuery('#timepicker3').timepicker({
                minuteStep : 15
            });
		</script>
 <script>
$( document ).ready(function() {
$('.select2').select2({ });
$('.select3').select2({ });
$('.select4').select2({ });
 function initializeSelect2() {
    $('.select3').select2({ });
  }
  
$('#example').dataTable({
 "bProcessing": false,
 "pagination":true,
 fixedHeader: {
            header: true
        },
   scrollCollapse: true,
   fixedColumns:   {
            leftColumns: 3
        },
 "sAjaxSource": "<?php echo page_url;?>FMS/order_list/",
 "aoColumns": [
						{ mData: 'sr_no' } ,
	 { mData: 'generatebutton' },
						{ mData: 'planorder' },
					
                        { mData: 'added_on' },
						{ mData: 'itemname' },
                        { mData: 'order_type' },
                        { mData: 'marketing_person' },
						{ mData: 'po_number' },
						{ mData: 'company_name' },
	 { mData: 'contactperson' },
	 { mData: 'designation' },
						{ mData: 'address' },
						{ mData: 'email' },
						{ mData: 'mobile_number' },
	 { mData: 'phone' },
						{ mData: 'internal_order_no' },
						{ mData: 'discount' },
						{ mData: 'order_value_after_discount' },
						{ mData: 'advance_amount' },
						{ mData: 'payment_terms' },
						{ mData: 'installation_charges' },
						{ mData: 'packingcharges' },
						{ mData: 'freigntcharges' },
						{ mData: 'remarks' }
						
						
                ]
        });   
});

</script>

<script language="javascript" type="text/javascript">   
$(document).ready(function() {
$("#save").click(function() {
var order_type = $("#order_type").val();
if(order_type=='')
{
	$("#error_order_type").html('Required!');
}
var person_name = $("#person_name").val();
if(person_name=='')
{
	
	$("#error_person_name").html('Required!');
}

var po_number = $("#po_number").val();
if(po_number=='')
{
	
	$("#error_po_number").html('Required!');
}

var company_name = $("#company_name").val();
if(company_name=='')
{
	
	$("#error_company_name").html('Required!');
}
var address = $("#address").val();
if(address=='')
{
	
	$("#error_address").html('Required!');
}
var email_id = $("#email_id").val();
if(email_id=='')
{
	
	$("#error_email_id").html('Required!');
}
var mobile_number = $("#mobile_number").val();
if(mobile_number=='')
{
	
	$("#error_mobile_number").html('Required!');
}
var auto_generated_io = $("#auto_generated_io").val();
if(auto_generated_io=='')
{
	
	$("#error_internal_order_no").html('Required!');
}
var instruments = $("#instruments").val();
if(instruments=='')
{
	
	$("#error_instruments").html('Required!');
}

var payment_term= $("#payment_term").val();
if(payment_term=='')
{
	
	$("#error_payment_term").html('Required!');
}

var packing_type= $("#packing_type").val();
if(packing_type=='')
{
	
	$("#error_packing_type").html('Required!');
}
var packing_charges= $("#packing_charges").val();
if(packing_charges=='')
{
	
	$("#error_packing_charges").html('Required!');
}
var installation_charges= $("#installation_charges").val();
if(installation_charges=='')
{
	
	$("#error_installation_charges").html('Required!');
}
var freight_type= $("#freight_type").val();
if(freight_type=='')
{
	
	$("#error_freight_type").html('Required!');
}

var status= $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}


var contactperson= $("#contactperson").val();
if(contactperson=='')
{
	
	$("#error_contact_person").html('Required!');
}



if(order_type=='' || person_name=='' || po_number=='' || company_name==''|| address==''|| email_id=='' || mobile_number=='' || internal_order_no=='' || instruments=='' || payment_term=='' || installation_charges==''|| packing_type=='' || packing_charges=='' || freight_type=='' || status=='' || contactperson=='')
{
	
	return false;
}

});
});
</script>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready
</script>
</body>
</html>