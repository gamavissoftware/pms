<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="NJ Media">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title><?php echo sitetitle; ?> ORDERS PENDING FOR DISPATCH</title>



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
                    <div class="col-md-9">
                      <h4 class="page-title">ORDERS PENDING FOR DISPATCH</h4>
                    </div>
                    <div class="col-md-3" style="margin-top: 30px;">
                      <a href="<?php echo page_url;?>Billing/dispatch_history" class="btn btn-warning btn-xs">View Dispatch History</a>
                    </div>
                </div>
              </div>
              <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
              <div class="row">
                <div class="col-sm-12">
                  <div class="card-box table-responsive">
                    <table id="example2" class="table table-striped table-bordered manglesh">
                      <thead>
                        <tr>
                          <th>SR NO.</th>
                           <th>LEAD MANAGER</th>
                           <th>BILLING COMPANY</th>
                          <th>CUSTOMER COMPANY NAME</th>
                          <th>CUSTOMER NAME</th>
                          <th>TAX DETAILS</th>
                          <th>PRODUCTS</th>
                          <th>SHIPPING ADDRESS</th>
                          <th>BILLING ADDRESS</th>
                          <th>PAYMENT TERMS</th>
                          <th>SEND TO TALLY</th>
                          <th>BILLED</th>
                          <th>ORDER DETAILS</th>
                          <th>EWAY BILL</th>
                          <th>DISPATCH</th>
                          <th>TAX INVOICE</th>
                          <!-- <th>PAYMENT COLLECTION HISTORY</th> -->
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
		  


              <!-- Modal -->
              <div id="myModal" class="modal fade" role="dialog">
              <div class="modal-dialog">

              <!-- Modal content-->
              <form action="<?php echo page_url;?>Billing/add_transport_detail" method="post">
              <input type="hidden" name="order_id" id="order_id">
              <div class="modal-content">
              <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Dispatch Details For </h4>
              </div>
              <div class="modal-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Transporter Name</label>
                    <input type="text" name="tname" id="tname" class="form-control">
                  </div>
                </div>


                 <div class="col-md-6">
                  <div class="form-group">
                    <label>Transporter ID</label>
                    <input type="text" name="tid" id="tid"  class="form-control" onblur="validate_gst()">
                  </div>
                </div>



                 <div class="col-md-6">
                  <div class="form-group">
                    <label>Distance In Km <span style="color: red">*</span></label>
                    <input type="number" steps="0.01" name="distance" id="distance" required class="form-control">
                  </div>
                </div>



                 <div class="col-md-6">
                  <div class="form-group">
                    <label>Vehicle No.<span style="color: red">*</span></label>
                    <input type="text" name="vehicle_no" id="vehicle_no" required class="form-control">
                  </div>
                </div>


              </div>
              </div>
              <div class="modal-footer">
              <input type="submit" name="sub" value="Submit" class="btn btn-success">
              </div>
              </div>
             </form>

              </div>
              </div>


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

		
 <script>

$( document ).ready(function() {
$('#example2').dataTable({
 "bProcessing": false,
 "pagination":true,
 fixedHeader: true,
"bSort": false,
 fixedColumns:   {
  leftColumns: 3
  },
    pageLength:50,

 "sAjaxSource": "<?php echo page_url;?>Billing/pending_for_dispatch_list",

 "aoColumns": [

               { mData: 'sr_no' },
               { mData: 'lead_manager' },
               { mData: 'billing_company' },
                { mData: 'company_name' },
                { mData: 'customer_name' },
                 { mData: 'taxdetail' },
                { mData: 'products' },
                { mData: 'shipaddress' },
                { mData: 'billingaddress' },
                { mData: 'paymentterm' },
                { mData: 'sendtotally' },
                { mData: 'billed' },
                { mData: 'order_details' },
                { mData: 'eway_bill' },
                { mData: 'dispatch' },
                { mData: 'tax_invoice' }
                // { mData: 'payment_collection' }

                ]

        });

});

</script>

<script type="text/javascript">
    function dispatch(id) {
        var dispatch = $('#dispatch'+id).val();
        // alert(billing);

        if(dispatch != '') {
          $.ajax({
                type:"post",
                url:"<?php echo page_url;?>Billing/dispatch",
                data:{id: id},
                success:function(data){
                  $('#dispatched'+id).html(data);
                }
                });
        }
    }

    function open_modal(id)
    {

      $("#myModal").modal('show');
      $("#order_id").val(id);

       $.ajax({
                type:"post",
                url:"<?php echo page_url;?>Billing/getorder_details",
                data:{id: id},
                success:function(data){
                  $("#vehicle_no").val(data);
                
                }
                });


    }



    // function validate_gst() {
    //        var gstinVal = $('#gst_no').val();
    //       var reggst = /^([0-9]){2}([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}([0-9]){1}([a-zA-Z]){1}([0-9]){1}?$/;
    //     if(!reggst.test(gstinVal) && gstinVal!=''){
    //             alert('GST Identification Number is not valid. It should be in this "11AAAAA1111Z1A1" format');
    //     }
    //     }
</script>

<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>




</body>

</html>
