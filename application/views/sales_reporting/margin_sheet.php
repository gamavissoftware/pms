<?php 
$DI = &get_instance();
$DI->load->model('Salescrm_model', 'salescrm');
$getAllTransporters = $DI->salescrm->getAllTransporters();
if($this->uri->segment(5)<>'ALL')
{
$user=$DI->salescrm->getusername($this->uri->segment(5));
}else
{
  $user="Overall Company";
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
                  
                    <h4 class="page-title"><?php echo $user;?> MARGIN SHEET FOR PERIOD <?php echo strtoupper(date('d-M-Y',strtotime($this->uri->segment(3))));?> to <?php echo strtoupper(date('d-M-Y',strtotime($this->uri->segment(4))));?>  </h4>
                  
                               
                </div>

                 <form method="post" action="<?php echo page_url;?>Sales_stats_reporting/filter_margin_sheet">
                <div class="card-box col-md-12">
                                <div class="">
                                   <div class="col-md-3"></div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>From</label>
                                            <span id="error_create_date" style="color:red;">*</span>
                                            <input type="month" name="from_date" class="form-control" value="<?php echo date('Y-m',strtotime($this->uri->segment(3)));?>" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>To</label>
                                            <span id="error_create_date" style="color:red;">*</span>
                                            <input type="month" name="to_date" class="form-control" value="<?php echo date('Y-m',strtotime($this->uri->segment(4)));?>" required="">
                                        </div>
                                    </div>


                                  <?php if($this->uri->segment(6)=='')
                                  {
                                  ?>
                                  <div class="col-md-2">
                                  <div class="form-group">
                                  <label>Agent</label>
                                  <span id="error_create_date" style="color:red;">*</span>
                                  <select name="user" id="user" class="form-control" required="">
                                  <option value="ALL"  <?php if($this->uri->segment(5)=='ALL'){?> selected <?php } ?>>ALL</option>
                                  <?php 
                                  $q1 = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('department_id',6)->where('user_status',1)->or_where('user_id',25)->where('user_status',1)->get();

                                  foreach($q1->result() as $rowss){?>
                                  <option value='<?php echo $rowss->user_id;?>' <?php if($this->uri->segment(5)==$rowss->user_id){?> selected <?php } ?>><?php echo $rowss->first_name." ".$rowss->last_name;?></option>
                                  <?php }
                                  ?>

                                  </select>
                                  </div>
                                  }
                                  </div>
                                  <?php }else{ ?>
                                    <div class="col-md-2">
                                  <div class="form-group">
                                  <label>Agent</label>
                                  <span id="error_create_date" style="color:red;">*</span>
                                  <select name="user" id="user" class="form-control" required="">
                                                                  <?php 
                                  $q1 = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('user_id',$this->uri->segment(5))->get();

                                  foreach($q1->result() as $rowss){?>
                                  <option value='<?php echo $rowss->user_id;?>'><?php echo $rowss->first_name." ".$rowss->last_name;?></option>
                                  <?php }
                                  ?>

                                  </select>
                                  </div>
                                  }
                                  </div>

                                  <?php } ?>

                            
                                  
                               
                                    <div class="col-md-1" style="margin-top: 23px;">
                                        <input type="submit" class="btn btn-success" value="Filter">
                                    </div>


                                </div>
                            </div>
                          </form>
              </div>


              <?php
                            
                              $start_date1=date('Y-m-01',strtotime($this->uri->segment(3)));
                              $end_date1=date('Y-m-t',strtotime($this->uri->segment(4)));
                        
                              $this_month_sale=$DI->salescrm->get_sales_numbers($start_date1,$end_date1,$this->uri->segment(5));

                              $this_month_profit=$DI->salescrm->get_profit_numbers($start_date1,$end_date1,$this->uri->segment(5));

?>
                                    
              <div class="row">
                <div class="col-dm-12">
                  <div class="col-md-6"></div>
                  <div class="col-md-3 card-box">
                    <p class="page-title text-center" style="color:red;line-height: 0px;">Total Sales(Non GST) (in ₹)</p>
                                     <p class="page-title text-center" style="color:red"><?php echo $this_month_sale;?></p>
                  </div>
                  <div class="col-md-3 card-box">
                     <p class="page-title text-center" style="color:red;line-height: 0px;">Total Profit (in ₹)</p>
                                     <p class="page-title text-center" style="color:red"><?php echo $this_month_profit;?></p>

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
                          <th>ORDER AMOUNT (WITHOUT TAX)</th>
                          <th>COST PRICE AMOUNT</th>
                          <th>PROFIT</th>
                          <th>ORDER SOURCE.</th>
                          <th>ORDER DATE/INVOICE NO.</th>
                          <th>SALES AGENT.</th>
                           <th>BILLING COMPANY</th>
                          <th>CUSTOMER COMPANY NAME</th>
                          <th>CUSTOMER NAME</th>
                          <th>PRODUCTS</th>
                         
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
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

 "sAjaxSource": "<?php echo page_url;?>Billing/sales_margin_list/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>",

 "aoColumns": [

               { mData: 'sr_no' },
               { mData: 'order_amount' },
               { mData: 'cp' },
               { mData: 'profit' },
               { mData: 'source' },
               { mData: 'invoice_no' },
               { mData: 'agent' },
               { mData: 'billing_company' },
               { mData: 'company_name' },
               { mData: 'customer_name' },
               { mData: 'products' }

               


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



  function send_to_tally(id, quote_id) {
    var send_to_tally = $('#send_to_tally'+id).val();
    $("#myModal").modal('show');

    $.ajax({
            type:"post",
            url:"<?php echo page_url;?>Billing/getProductName",
            data:{quote_id: quote_id},
            success:function(data){
              $('.show_products').html(data);
              $('#order_id').val(id);
              $('#quotation_id').val(quote_id);
              initializeSelect2();
              initializeSelect3();
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
</script>


</body>

</html>
