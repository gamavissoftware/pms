<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="NJ Media">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title><?php echo sitetitle; ?> ALL ORDERS</title>



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
              <?php
              $paymentOverdueFeatureAvailable =
                $this->db->table_exists('order_punch') &&
                $this->db->table_exists('order_punch_mailing_details') &&
                $this->db->table_exists('order_punch_tax_details') &&
                $this->db->table_exists('customer_quotation') &&
                $this->db->table_exists('customer_detail') &&
                $this->db->table_exists('store_rack_location') &&
                $this->db->table_exists('system_users');
              ?>

              <div class="row">
                <div class="col-sm-12">
                  <div class="col-md-4"></div>
                  <div class="col-md-4 page-title-box">
                    <h4 class="page-title text-center">CUSTOMER WHOSE PAYMENT IS OVERDUE</h4>
                  </div>
                 <div class="col-md-4" style="margin-top: 20px;">
                <?php
                if($paymentOverdueFeatureAvailable && $_SESSION['logged_in']['role']==1)
                {
                ?>
                <a href='<?php echo page_url;?>Dbbackup/customer_payment_reminders_to_admin_new/NA/1' class="btn btn-success pull-right">Download Consolidated Payemnt Due/Overdue List</a>
                <?php }else if($paymentOverdueFeatureAvailable)
                { ?>
                  <a href='<?php echo page_url;?>Dbbackup/customer_payment_reminders_to_salesAgent/NA/1/<?php echo $_SESSION['logged_in']['user_id'];?>' class="btn btn-success pull-right">Download Consolidated Payemnt Due/Overdue List</a>
                <?php  } ?>
                 </div>
                </div>
              </div>


              <?php if (!$paymentOverdueFeatureAvailable) { ?>
              <div class="row">
                <div class="col-sm-12">
                  <div class="alert alert-warning" style="margin-top: 15px;">
                    Payment overdue reporting is not available on this database because the billing order tables are missing. The dashboard card has been kept fail-safe so the main dashboard will continue working normally.
                  </div>
                </div>
              </div>
              <?php } ?>

              <?php if ($paymentOverdueFeatureAvailable) { ?>
                <div class="row">
                  <?php
                  $paymentOverdueFilterAction = page_url . 'Customer/filter_overdue_payments/' . $this->uri->segment(3);
                  if (strtolower($this->router->fetch_class()) === 'reporting') {
                    $paymentOverdueFilterAction = page_url . 'Reporting/filterpaymentoverdue/' . $this->uri->segment(3);
                  }
                  ?>
                  <form action="<?php echo $paymentOverdueFilterAction; ?>" method="post">
                <div class="col-sm-12">
                  <div class="col-md-4">
                  </div>
                   <div class="col-md-4 card-box">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Company</label>
                        <select name="company" id="company" class="form-control"> <!--onchange="changecompany(this.value);"-->
                          <option value="ALL">ALL</option>
                          <?php 
                          $restey=$this->db->select('id,companyname')->from('store_rack_location')->get();
                          if($restey->num_rows()>0)
                          {
                            foreach($restey->result() as $row)
                            {
                          ?>
                          <option value="<?php echo $row->id;?>" <?php if($this->uri->segment(4)==$row->id){?> selected <?php } ?>><?php echo $row->companyname;?></option>
                          <?php 
                          }
                          }
                          ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Overdue By</label>
                        <select name="overdue" id="overdue" class="form-control">
                          <option value="ALL" <?php if($this->uri->segment(5)=='ALL'){?> selected <?php } ?>>ALL</option>
                          <option value="1" <?php if($this->uri->segment(5)==1){?> selected <?php } ?>>0-29 Days</option>
                          <option value="2" <?php if($this->uri->segment(5)==2){?> selected <?php } ?>>30-59 Days</option>
                          <option value="3" <?php if($this->uri->segment(5)==3){?> selected <?php } ?>>60-89 Days</option>
                          <option value="4" <?php if($this->uri->segment(5)==4){?> selected <?php } ?>>90 +</option>
                         
                          ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="col-md-4"></div>
                      <div class="col-md-4 text-center">
                        <input type="submit" name="sub" class="btn btn-success" value="Submit">
                      </div>
                    </div>

                  </div>
                </div>
              </form>
              </div>
              <?php } ?>


              <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
              <?php if ($paymentOverdueFeatureAvailable) { ?>
              <div class="row">
                <div class="col-sm-12">
                  <div class="card-box table-responsive">
                    <table id="example2" class="table table-striped table-bordered manglesh">
                      <thead>
                        <tr>
                          <th>SR NO.</th>
                          <th>SALES AGENT</th>
                          <th>BILLING COMPANY</th>
                          <th>INVOICE NO.</th>
                          <th>CUSTOMER COMPANY NAME</th>
                          <th>CUSTOMER NAME</th>
                          <th>PRODUCTS</th>
                          <th>ORDER VALUE</th>
                          <th>PARTIAL PAYMENT</th>
                          <th>PAYMENT DUE</th>
                            <?php 
              if($_SESSION['logged_in']['role']==1)
              {?>
                          <th>PAYMENT CLOSE</th>
                        <?php } ?>
                          <th>PAYMENT TERMS</th>
                          <th>BILLING DATE</th>
                          <th>EXPECTED PAYMENT DATE</th>
                          <th>PAYMENT DATE EXCEEDED BY</th>
                          <th>DO NOT FOLLOWUP</th>
                          
                         
                        
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <?php } ?>
		  

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
<?php if ($paymentOverdueFeatureAvailable) { ?>
$('#example2').dataTable({
 "bProcessing": false,
 "pagination":true,
 fixedHeader: true,
"bSort": true,
 fixedColumns:   {
  leftColumns: 3
  },
  dom: 'lBfrtip',
 "sAjaxSource": "<?php echo page_url;?>Customer/all_payment_overdue_list/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>",
 buttons: [
  {
    extend: 'excel',
    exportOptions: {
      columns: [ 0, 1, 2, 3, 4, 5, 7, 8, 9, 10, 11,12,13]
    },
  },
  ],

  pageLength:50,
 "aoColumns": [

               { mData: 'sr_no' },
             
               { mData: 'agent' },
                { mData: 'company_name' },
                 { mData: 'invoice' },
                { mData: 'cust_company_name' },
                { mData: 'customer_name' },
               
                { mData: 'products' },
                { mData: 'order_value' },
                { mData: 'partial_payment' },
                { mData: 'payment_due' },
              <?php 
              if($_SESSION['logged_in']['role']==1)
              {?>
              { mData: 'payment_close' },
              <?php } ?>
             
                { mData: 'payment_terms' },
                { mData: 'billing_date' },
                { mData: 'payment_date' },
                { mData: 'exceeded_by' },
                { mData: 'do_not_followup' }
          



                ]

        });
<?php } ?>

});





</script>
<script type="text/javascript">
  function delete_order(order_id, quotation_id) {
    if(confirm('Are you sure you want to delete this order?')) {
      document.location = "<?php echo page_url;?>Customer/delete_order/"+order_id+"/"+quotation_id;
    }
  }

  function check_followup(order_id) {
    if(confirm('Are you sure you want to unfollow this customer?')) {
      var customer_id = $(".chk_unfollow"+order_id).val();

      $.ajax({
            type:"post",
            url:"<?php echo page_url;?>Customer/unfollow_customer",
            data:"customer_id="+customer_id,
              success:function(data){
                  location.reload();
              }
            });
    }
  }

  function changecompany(val)
  {
    var user_id="<?php echo $this->uri->segment(3);?>";
      if(user_id!='')
      {
        var u=user_id;
      }else
      {
        var u="NA";
      }
    document.location="<?php echo page_url;?>Customer/payment_overdue/"+u+"/"+val;

  }

  function close_payment(orderid)
  {

    if($('#payment_close'+orderid).is(':checked'))
    {
      $.ajax({
            type:"post",
            url:"<?php echo page_url;?>Customer/markcustomerpaymentdone",
            data:"orderid="+orderid,
              success:function(data){
                if(data>0)
                {
                  $("#c"+orderid).html('Payment Closed');
                  $("#c"+orderid).css('color','red');
                }


              }
            });

    }else
    {
       
    }

  }
</script>

<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>




</body>

</html>
