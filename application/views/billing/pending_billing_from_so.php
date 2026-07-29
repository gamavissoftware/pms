<?php 
$DI = &get_instance();
$DI->load->model('Salescrm_model', 'salescrm');
$getAllTransporters = $DI->salescrm->getAllTransporters();
?><!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="NJ Media">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title><?php echo sitetitle; ?> ORDERS PENDING FOR BILLING</title>



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

.select2 {
  width: 100% !important;
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
                  <div class="col-md-6">
                    <h4 class="page-title">Sales Orders Pending To Be Sent for Billing</h4>
                  </div>
                   <div class="col-md-3 pull-right" style="margin-top: 30px;">
                    <a href="<?php echo page_url;?>Billing/cancelled_history" class="btn btn-warning">View Cancelled Order</a>
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
                          <th>Edit Customer Details</th>
                          <th>Edit Order Details</th>
                          <th>ORDER SOURCE.</th>
                          <th>ORDER DATE/SALES ORDER NO.</th>
                          <th>SALES AGENT.</th>
                           <th>BILLING COMPANY</th>
                          <th>CUSTOMER COMPANY NAME</th>
                          <th>CUSTOMER NAME</th>
                          <th>TAX DETAILS</th>
                          <th>PRODUCTS</th>
                          <th>SHIPPING ADDRESS</th>
                          <th>BILLING ADDRESS</th>
                          <th>PAYMENT TERMS</th>
                          <th>PO DETAILS</th>
                         <!--  <th>SEND TO TALLY</th>
                          <th>BILLED</th>
                          <th>ORDER DETAILS</th>
                          <th>TAX INVOICE</th> -->
                          <th>SEND FOR BILLING</th>
                          <th>CANCEL ORDER</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
		  
              <div id="myModal" class="modal  fade" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                      <h5 class="modal-title">Send To Tally</h5>
                    </div>
                    <div class="modal-body">
                      <form method="post" action="<?php echo page_url;?>Billing/send_to_tally">
                        <input type="hidden" name="order_id" id="order_id">
                        <input type="hidden" name="quotation_id" id="quotation_id">
                        <input type="hidden" name="billing_company" id="billing_company">
                        <div class="row">
                          <div class="col-sm-12 show_products">
                          </div>
                        </div>
                        <hr>
                        <div class="row">
                          <div class="col-sm-12">
                            <div class="col-md-9">
                              <h4 class="page-title text-center">Transport Details</h4>
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-sm-12">
                            <div class="col-sm-4">
                              <div class="form-group">
                                <label>Transporter Type</label>
                                  <select name="transporter_type" id="transporter_type" class="form-control" onchange="chk_transporter_type()">
                                    <option value="">Select</option>
                                    <option value="1">Owned</option>
                                    <option value="2">Hired</option>
                                   </select>
                              </div>
                            </div>
                            <div class="col-sm-4" id="show_owned_transporters" style="display: none;">
                              <div class="form-group">
                                <label>Vehicle No.</label>
                                  <select name="vehicle_no" id="vehicle_no" class="form-control select3">
                                    
                                  </select>
                              </div>
                            </div>




                            <div class="col-md-4 show_hired_transporters" style="display: none;">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Transporter Name<span style="color:red;">*</span></label>
                                <select class="form-control select2" name="transporter_name" id="transporter_name" onchange="getTransporterDetails()">
                                <option value="">SELECT</option>
                                <?php if ($getAllTransporters != '') {
                                foreach ($getAllTransporters as $row2) { ?>
                                <option value="<?php echo $row2->id; ?>"><?php echo $row2->name; ?></option>
                                <?php }
                                } ?>
                                </select>
                                <!-- <input type="text" class="form-control" name="name" required=""> -->
                                </div>
                                </div>

                                 <div class="col-md-4 show_hired_transporters" style="display: none;">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Mobile No.<span style="color:red;">*</span></label>
                                <input type="number" maxlength="10" class="form-control" name="tmobile_no" id="tmobile_no" value="" data-mask="(999) 999-9999">
                                </div>
                                </div>

                                 <div class="col-md-6 show_hired_transporters" style="display: none;">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Address</label>
                                <textarea class="form-control" name="taddress" id="taddress"></textarea>
                                </div>
                                </div>



                            <div class="col-sm-6" id="show_hired_transporters" style="display: none;">
                              <div class="form-group">
                                <label>Vehicle No.</label>
                                  <input type="text" name="vehicle_no1" id="vehicle_no1" class="form-control">
                              </div>
                            </div>
                            <div style="clear:both;height: 10px;"></div>

                             <div class="col-md-4 show_hired_transporters" style="display: none;">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Rate Type<span style="color:red;">*</span></label>
                                <select class="form-control" id="rate_type" name="rate_type">
                                    <option value="">Type</option>
                                    <option value="1">Per Ltr</option>
                                    <option value="2">Fix Rate</option>
                                </select>
                                </div>
                                </div>

                                <div class="col-md-4 show_hired_transporters" style="display: none;">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Rate<span style="color:red;">*</span></label>
                                <input type="text" class="form-control" id="transport_rate" name="transport_rate" onkeyup="allow_decimal('transport_rate');">
                                </div>
                                </div>
                           
                            <div class="col-sm-4">
                              <div class="form-group">
                                <label>Transporter ID</label>
                                  <input type="text" name="transporter_id" id="transporter_id" class="form-control" autocomplete="off">
                              </div>
                            </div>
                            <div class="col-sm-4">
                              <div class="form-group">
                                <label>Distance in km<span style="color: red;">*</span></label>
                                  <input type="text" name="distance_in_km" id="distance_in_km" class="form-control" autocomplete="off" required>
                              </div>
                            </div>
                            <div class="col-sm-4">
                              <div class="form-group">
                                <label>Vehicle Type<span style="color: red;">*</span></label>
                                  <input type="text" name="vehicle_type" id="vehicle_type" class="form-control" autocomplete="off" required>
                              </div>
                            </div>
                            <div class="col-sm-4">
                              <div class="form-group">
                                <label>Destination<span style="color: red;">*</span></label>
                                  <input type="text" name="destination" id="destination" class="form-control" autocomplete="off" required>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="row" style="margin-top:20px;">
                          <div class="col-md-12 text-center">
                          <button type="submit" class="btn btn-primary" id="submit_data">Submit</button><br/>
                          <span id="checkforstockdata" style="color:red;font-weight: bold;font-size:16px"></span>
                        </div>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>  




               <div id="cancell_order" class="modal fade" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog">

                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                      <h5 class="modal-title">Cancell Sales Order</h5>
                    </div>
                    <div class="modal-body">
                      <form method="post" action="<?php echo page_url;?>Billing/cancel_order">
                        <input type="hidden" name="cancell_order_id" id="cancell_order_id">
                
                        <div class="row">
                          <div class="col-sm-12">
                            <div  class="form-group">
                             <label>Cancellation Remarks</label>
                             <textarea class="form-control" name="cancell_rmk" id="cancell_rmk"></textarea>
                            </div>
                            
                          </div>
                        </div>
                       
                          <button type="submit" class="btn btn-primary">Submit</button>
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

 "sAjaxSource": "<?php echo page_url;?>Billing/all_pending_billing_so",

 "aoColumns": [

               { mData: 'sr_no' },
               { mData: 'edit_customer' },
               { mData: 'edit_order' },
               { mData: 'source' },
               { mData: 'invoice_no' },
               { mData: 'agent' },
               { mData: 'billing_company' },
               { mData: 'company_name' },
               { mData: 'customer_name' },
               { mData: 'taxdetail' },
               { mData: 'products' },
               { mData: 'shipaddress' },
               { mData: 'billingaddress' },
               { mData: 'paymentterm' },
               { mData: 'po_details' },
               { mData: 'sendtobilling' },
               // { mData: 'sendtotally' },
               // { mData: 'billed' },
               // { mData: 'order_details' },
               // { mData: 'tax_invoice' },
               { mData: 'cancell' }

                ]

        });


              
         

});


// $( document ).ready(function() {


// });

</script>

<script type="text/javascript">

  function cancel_order(id) {
  $('#cancell_order_id').val(id);
  $("#cancell_order").modal('show');
  
  }



  function send_to_tally(id, quote_id,billing_company) {

     $("#submit_data").attr('disabled',true);
     $("#submit_data").css('display','none');
     $("#checkforstockdata").html('');
    var send_to_tally = $('#send_to_tally'+id).val();
    $("#myModal").modal('show');

    $.ajax({
            type:"post",
            url:"<?php echo page_url;?>Billing/getProductName",
            data:{quote_id: quote_id,company:billing_company},
            success:function(data){
              var d=data.split('~');
              $('.show_products').html(d[0]);
              $('#order_id').val(id);
              $('#quotation_id').val(quote_id);
              $('#billing_company').val(billing_company);
              initializeSelect2();
              initializeSelect3();

              if(d[1]!=0)
              {

                $("#submit_data").attr('disabled',true);
                $("#submit_data").css('display','none');
                $("#checkforstockdata").html('One or more product(s) stock is not available');

              }else{

                 $("#submit_data").attr('disabled',false);
                 $("#submit_data").css('display','');
              }
            }
          });

        $.ajax({
            type:"post",
            url:"<?php echo page_url;?>Billing/getTransportDetails",
            data:{order_id: id},
            success:function(data){
              var arr = data.split('|');
              $('#transporter_name').val(arr[0]);
              $('#transporter_id').val(arr[1]);
              $('#distance_in_km').val(arr[2]);
              // $('#vehicle_no').val(arr[3]);
              $('#vehicle_type').val(arr[4]);
              $('#destination').val(arr[5]);
            }
          });

    // if(send_to_tally != '') {
    //   $.ajax({
    //         type:"post",
    //         url:"<?php echo page_url;?>Billing/send_to_tally",
    //         data:{id: id},
    //         success:function(data){
    //           $('#sent_to_tally'+id).html(data);
    //         }
    //         });
    // }

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

                $('.select2').select2({ tags:true });
    }

  function initializeSelect3() {
     var url1 = "<?php echo page_url;?>Billing/getVehicles";

                $(".select3").select2({ 
                    placeholder: 'TYPE TO SELECT',                    
                    minmumInputLength:4,
                    allowClear: true,
                     tags: true,
        
                        ajax: {
                          url: url1,
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

    function chk_transporter_type() {
      var transporter_type = $('#transporter_type').val();
      $('#show_hired_transporters').css('display','none');
      $('.show_hired_transporters').css('display','none');
      $('#show_owned_transporters').css('display','none');
      $('#vehicle_no').attr('required',false);
      $('#vehicle_no1').attr('required',false);
      $('#transporter_name').attr('required',false);
      $('#tmobile_no').attr('required',false);
      $('#taddress').attr('required',false);
      $('#rate_type').attr('required',false);
      $('#transport_rate').attr('required',false);

      if(transporter_type == 1) {

        $('#show_owned_transporters').css('display','');
        $('#show_hired_transporters').css('display','none');
        $('.show_hired_transporters').css('display','none')
        $('#vehicle_no').attr('required',true);
        $('#vehicle_no1').attr('required',false); 

      } else if(transporter_type == 2) {

        $('#show_hired_transporters').css('display','');
        $('.show_hired_transporters').css('display','');
        $('#vehicle_no1').attr('required',true); 
        $('#transporter_name').attr('required',true);
        $('#tmobile_no').attr('required',true);
        $('#taddress').attr('required',true);
        $('#rate_type').attr('required',true);
        $('#transport_rate').attr('required',true); 

      }
    }

    function getTransporterDetails() {
          var transporter_id = $("#transporter_name").val();
          // alert(transporter_id);
      
          $.ajax({
              type: "post",
              url: "<?php echo page_url; ?>Approval/getTransporterDetails",
              data: {
                  transporter_id: transporter_id
              },
              success: function(data) {
                  var arr = data.split('|');
                  var mobile_no = arr[0];
                  var address = arr[1];
      
                  $("#tmobile_no").val(mobile_no);
                  $("#taddress").val(address);
      
      
              }
          });
      }

      function allow_decimal(data) {
      
          var self = $("#" + data);
          self.val(self.val().replace(/[^0-9\.]/g, ''));
          if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
              evt.preventDefault();
          }
      
      
      }


$( document ).ready(function() {
      $('#submit_data').dblclick(function(e){ 
    e.preventDefault();
})
    });


function send_order_to_billing(orderid,billing_company)
{
  if(confirm('Have you checked SO Details and want to send Sales Order to Billing '))
  {
    document.location="<?php echo page_url;?>Billing/send_to_billing/"+orderid+"/"+billing_company;
  }

}
</script>


</body>

</html>
