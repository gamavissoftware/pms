<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="NJ Media">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title><?php echo sitetitle; ?></title>



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

<link href="<?php echo assets_url; ?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">

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
                  <div class="col-md-9">
                    <h4 class="page-title">Orders for Payment Adjustment</h4>
                  </div>
                  <div class="col-md-3" style="margin-top: 30px;">
                    <a href="<?php echo page_url;?>Billing/order_pending_for_complete_payment_history" class="btn btn-warning">View Adjustment History</a>
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
                          <th>ORDER DATE/INVOICE NO.</th>
                          <th>SALES AGENT.</th>
                          <th>BILLING COMPANY</th>
                          <th>CUSTOMER COMPANY NAME</th>
                          <th>CUSTOMER NAME</th>
                          <th>PRODUCTS</th>
                          <th>BASIC ORDER AMOUNT</th>
                          <th>GST AMOUNT</th>
                          <th>TOTAL ORDER AMOUNT</th>
                          <th>TOTAL ORDER AMOUNT POST TDS DEDUCTION</th>
                          <th>PAYMENT RECIEVED</th>
                          <th>PAYMENT DUE</th>
                          <th>ADJUST & CLOSE</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>




              <div class="row">
                <div class="col-sm-12">
                  <div class="col-md-9">
                    <h4 class="page-title">Orders Pending for Payment Adjustment Approval</h4>
                  </div>
               
                </div>
              </div>
              <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
              <div class="row">
                <div class="col-sm-12">
                  <div class="card-box table-responsive">
                    <table id="example21" class="table table-striped table-bordered manglesh">
                      <thead>
                        <tr>
                          <th>SR NO.</th>
                          <th>ORDER DATE/INVOICE NO.</th>
                          <th>SALES AGENT.</th>
                          <th>BILLING COMPANY</th>
                          <th>CUSTOMER COMPANY NAME</th>
                          <th>CUSTOMER NAME</th>
                          <th>PRODUCTS</th>
                          <th>BASIC ORDER AMOUNT</th>
                          <th>GST AMOUNT</th>
                          <th>TOTAL ORDER AMOUNT</th>
                          <th>TOTAL ORDER AMOUNT POST TDS DEDUCTION</th>
                          <th>PAYMENT RECIEVED</th>
                          <th>PAYMENT DUE</th>
                          <th>ADJUSTMENT DETAILS</th>
                          <th>SEND FOR APPROVAL ON</th>

                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>


		  
              <div id="myModal" class="modal fade" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                      <h5 class="modal-title">Adjust Payment</h5>
                    </div>
                    <div class="modal-body">
                      <form method="post" action="<?php echo page_url;?>Billing/adjust_order_payment" enctype="multipart/form-data">
                        <input type="hidden" name="order_id" id="order_id">
                    
                        <div class="row">


                          <div class="col-md-6">
                            <div class="form-group">
                              <label>Adjustment Type</label>
                              <select name="adjust" id="adjust" class="form-control" onchange="checknote();">
                                <option value="">Select</option>
                                <option value="1">Round Off</option>
                                <option value="2">Debit Note</option>
                                <option value="3">Bad Debt</option>
                              </select>
                              <span style="color:red;font-weight:bold;display:none;" id="round_data"></span>
                            </div>
                          </div>
                          <script type="text/javascript">
                            function checknote()
                            {
                                $("#round_data").css('display','none');
                                $("#round_data").text('');
                              $(".dbnote").css('display','none');
                              $("#debit_note").attr('required',false);
                              var adjust=$("#adjust").val();
                              if(adjust==2)
                              {
                                $(".dbnote").css('display','');
                                $("#debit_note").attr('required',true);
                              }
                            }
                          </script>


                           <div class="col-md-6 dbnote" style="display:none;">
                            <div class="form-group">
                              <label>Upload Debit Note <span style="color:red">*</span></label>
                              <input type="file" name="debit_note" id="debit_note" class="form-control" >
                            </div>
                          </div>


                            <div class="col-md-12">
                            <div class="form-group">
                              <label>Remarks <span style="color:red">*</span></label>
                              <textarea class="form-control" name="remarks" id="remarks" required style="resize:none;" required></textarea>
                            </div>
                          </div>



                         </div>
                      

                        <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
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
<script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js" type="text/javascript"></script>

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

 "sAjaxSource": "<?php echo page_url;?>Billing/pending_order_payment_adjustment",

 "aoColumns": [

               { mData: 'sr_no' },
               { mData: 'invoice_no' },
               { mData: 'agent' },
               { mData: 'billing_company' },
               { mData: 'company_name' },
               { mData: 'customer_name' },
               { mData: 'products' },
               { mData: 'basic_order_amount' },
               { mData: 'gst_order_amount' },
               { mData: 'total_order_amount' },
               { mData: 'order_amount_after_tds' },
               { mData: 'payment_recvd' },
               { mData: 'payment_due' },
               { mData: 'adjustment' }
             

               

                ]

        });




$('#example21').dataTable({
 "bProcessing": false,
 "pagination":true,
 fixedHeader: true,
"bSort": false,
 fixedColumns:   {
  leftColumns: 3
  },

 "sAjaxSource": "<?php echo page_url;?>Billing/pending_order_payment_adjustment_for_approval",

 "aoColumns": [

               { mData: 'sr_no' },
               { mData: 'invoice_no' },
               { mData: 'agent' },
               { mData: 'billing_company' },
               { mData: 'company_name' },
               { mData: 'customer_name' },
               { mData: 'products' },
               { mData: 'basic_order_amount' },
               { mData: 'gst_order_amount' },
               { mData: 'total_order_amount' },
               { mData: 'order_amount_after_tds' },
               { mData: 'payment_recvd' },
               { mData: 'payment_due' },
               { mData: 'adjustment' },
               { mData: 'approval_send' }
             

               

                ]

        });


         

});





</script>

<script type="text/javascript">
  function adjust_payment(id,due) {  

   $("#round_data").css('display','none');
    $("#round_data").text('');

        $("#myModal").modal('show');
        $("#order_id").val(id);
        if(parseFloat(due)>500)
        {
          $("#adjust option[value='1']"). attr("disabled",true);
          $("#round_data").css('display','');
          $("#round_data").text('Round Off Applicable only for dues equal or less than 500');

        }else
        {
           $("#adjust option[value='1']"). attr("disabled",false);
        }

   }

    function billing(id) {
    var billing = $('#billing'+id).val();
    // alert(billing);

    if(billing != '') {
      $.ajax({
            type:"post",
            url:"<?php echo page_url;?>Billing/billing",
            data:{id: id},
            success:function(data){
              $('#billed'+id).html(data);
            }
            });
    }

  }

  function validate_batch_code(i) {
           var batch_code = $('#batch_code'+i).val();
           var reggst = /^([a-zA-Z]){1}([0-9]){1}-\s*([0-9]){2}-\s*([a-zA-Z]){1}-\s*([0-9]){3}?$/;

            if(!reggst.test(batch_code) && batch_code!=''){
                    alert('Batch Code Number is not valid.');
                    $('#batch_code'+i).val('');
            }
    }

    function initializeSelect2() {
       var url = "<?php echo page_url;?>Billing/getBatchCodes";

                $('.select2').select2({ 
                    placeholder: 'TYPE TO SELECT',                    
                    minmumInputLength:4,
                    allowClear: true,
                    tags: true,
                    multiple: true,
        
                        ajax: {
                          url: url,
                          dataType: 'json',
                          delay: 250,

                          processResults: function (data) {
                            return {
                              results: data
                            };

                          },

                    cache: true
                }

            });
    }
</script>


</body>

</html>
