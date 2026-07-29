<?php 
$CI =& get_instance();
$CI->load->model('Salescrm_model','salescrm');

if($this->uri->segment(3) != 'ALL') {
  $getHpclCompanyName = $this->salescrm->getHpclCompanyName($this->uri->segment(3));
} else {
  $getHpclCompanyName = 'ALL COMPANIES';
}


$today=$this->uri->segment(3);
$CI =& get_instance();
$CI->load->model('Daily_report_model');

$start_date = $this->uri->segment(4);
$end_date = $this->uri->segment(5);

$total_monthly_sales = $CI->Daily_report_model->total_monthly_sales($start_date, $end_date);
$total_billed_today = explode("|",$total_monthly_sales);
$total_billed = $total_billed_today[0];
$total_billed_amount = $total_billed_today[1];
$total_billed_qty = $total_billed_today[2];

?>

<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="NJ Media">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title><?php echo sitetitle; ?> BILLING FROM <?php echo $getHpclCompanyName;?></title>



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
    <link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" >
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
                  <div class="col-md-12">
                    <h4 class="page-title text-center">BILLING FROM <?php echo $getHpclCompanyName;?></h4>
                  </div>
                </div>
              </div>

              <div class="row" style="margin-top:20px;margin-bottom:20px;">
                <div class="col-xs-3"></div>
                <div class="col-xs-6">
                  <div class="card-box">
                    <div class="row">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                      <form  method="post" action="<?php echo page_url;?>Billing/filter_hpcl_billing/<?php echo $this->uri->segment(3);?>">
                        <div class="col-md-4">
                          <div class="form-group">
                            <label for="field-2" class="control-label">From Date<span style="color: red;">*</span></label>
                            <input class="form-control" type="text" name="from_date" id="to_date" value="<?php echo date('d-m-Y', strtotime($this->uri->segment(4)));?>" autocomplete="off" required>
                          </div>
                        </div>    
                        <div class="col-md-4">
                          <label>To Date<span style="color: red;">*</span></label>
                          <input class="form-control" type="text" name="to_date" id="to_date" value="<?php echo date('d-m-Y', strtotime($this->uri->segment(5)));?>" autocomplete="off" required>
                        </div>
                        <div class="col-md-4">
                          <label></label>
                          <input class="btn btn-success" style="margin-top:24px;" type="submit" name="submit" value="Filter">
                        </div>
                        
                      </form>
                    </div>
                    </div>
                  </div>
                </div>
              </div>
              <?php if($this->uri->segment(3) == 'ALL') { ?>
              <div class="row">
                <div class="col-md-12">
                  <div class="col-md-8"></div>
                  <div class="col-md-2 card-box" style="margin-top:20px;, text-align: center;">
                        <span style="font-weight: bold;color:red;font-size: 15px;">TOTAL VALUE</span><br/>
                        <span style="font-weight: bold;font-size: 26px;" id="mouldtotal"><?php echo '₹'.$total_billed_amount;?></span>
                    </div>
                     <div class="col-md-2 card-box" style="margin-top:20px;, text-align: center;">
                         <span style="font-weight: bold;color:red;font-size: 15px;">TOTAL QTY</span><br/>
                        <span style="font-weight: bold;font-size: 26px;" id="bsize"><?php echo $total_billed_qty.' ltr';?></span>
                     </div>
                </div>
              </div>
              <?php } ?>
              <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
              <div class="row">
                <div class="col-sm-12">
                  <div class="card-box table-responsive">
                    <table id="example2" class="table table-striped table-bordered manglesh">
                      <thead>
                        <tr>
                          <th>SR NO.</th>
                          <th>BILLING DATE</th>
                          <th>PARTICULARS</th>
                          <th>VOUCHER TYPE</th>
                          <th>VOUCHER NO.</th>
                          <th>GSTIN/UIN</th>
                          <th>PAN NO.</th>
                          <th>ORDER NO.</th>
                          <th>TERMS OF PAYMENT</th>
                        <!--   <th>OTHER REFERENCES</th>
                          <th>TERMS OF DELIVERY</th>
                          <th>DELIVERY NOTE NO. & DATE</th>
                          <th>DISPATCH DOC. NO</th> -->
                          <th>DISPATCH THROUGH</th>
                          <th>DESTINATION</th>
                          <th>QUANTITY</th>
                         <!--  <th>ALT. UNITS</th> -->
                          <!-- <th>RATE</th> -->
                          <th>VALUE</th>
                          <th>GROSS TOTAL</th>
                          <th>SALE TYPE</th>
                          <th>SALES VALUE</th>
                          <th>OUTPUT CGST 9%</th>
                          <th>OUTPUT SGST 9%</th>
                          <th>OUTPUT IGST 18%</th>
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
        <script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
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
 dom: 'Bfrtip',

        buttons: [

            'excel'

        ],

"lengthMenu": [[50, 100, 200, -1], [50, 100, 200, "All"]],
"bSort": false,
 fixedColumns:   {
  leftColumns: 3
  },

  pageLength:50,
 "sAjaxSource": "<?php echo page_url;?>Billing/billing_from_hpclcompany_list/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>",

 "aoColumns": [

               { mData: 'sr_no' },
               { mData: 'billed_date' },
               { mData: 'company_name' },
               { mData: 'voucher_type' },
               { mData: 'invoice_no' },
               { mData: 'gst_no' },
               { mData: 'pan_no' },
               { mData: 'order_no' },
               { mData: 'paymentterm' },
               // { mData: 'other_references' },
               // { mData: 'terms_of_delivery' },
               // { mData: 'delivery_note' },
               // { mData: 'dispatch_doc_no' },
               { mData: 'dispatch_through' },
               { mData: 'destination' },
               { mData: 'total_qty' },
               // { mData: 'alt_units' },
              // { mData: 'order_rate' },
               { mData: 'order_value' },
               { mData: 'gross_total' },
               { mData: 'sale_type' },
               { mData: 'local_sale' },
               { mData: 'cgst' },
               { mData: 'sgst' },
               { mData: 'igst' }

                ]

        });

 jQuery('#from_date').datepicker({
  autoclose: true,
  todayHighlight: true,
  format: 'dd-mm-yyyy'
 });

 jQuery('#to_date').datepicker({
  autoclose: true,
  todayHighlight: true,
  format: 'dd-mm-yyyy'
 });
         

});





</script>


</body>

</html>
