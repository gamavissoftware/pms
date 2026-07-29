<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="NJ Media">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title><?php echo sitetitle; ?> Quotations Expiring Today</title>



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
              <form method="post" id="frm" action="<?php echo page_url;?>Customer/send_notifications" onsubmit="return validate()">
              <div class="row">
                <div class="col-sm-12">
                    <div class="col-md-6 page-title-box">
                    <h4 class="page-title">LEADS QUOTATIONS EXPIRING TODAY</h4>
                  </div>
                  <div class="col-md-6 pull-right" style="margin-top: 20px;">
                    <input type="submit" class="btn btn-warning pull-right" id="saves" value="Send Notifications to Customers">
                  </div>
                </div>
                </div>
              <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
              <div class="row">
                <div class="col-sm-12">
                  <div class="card-box table-responsive">
                    <table id="example" class="table table-striped table-bordered manglesh">
                      <thead>
                        <tr>
                          <th>SR NO.</th>
                          <th>CREATE DATE</th>
                          <th>COMPANY NAME</th>
                          <th>CUSTOMER NAME</th>
                          <th>EMAIL</th>
                          <th>CONTACT NO</th>
                          <th>CITY</th>
                          <th>ADDRESS</th>
                          <th>PRODUCTS</th>
                          <th>QUOTATION</th>
                          <th>EXTEND BY</th>
                          <th>NOTIFY CUSTOMERS</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div> 
            </form>
                              <!-- Page-Title -->
              <div class="row">
                <div class="col-sm-12">
                    <div class="col-md-6 page-title-box">
                    <h4 class="page-title">QUOTATION EXPIRATION EXTENSIONS</h4>
                  </div>
                </div>
                </div>
              <div class="row">
                <div class="col-sm-12">
                  <div class="card-box table-responsive">
                    <table id="example1" class="table table-striped table-bordered manglesh">
                      <thead>
                        <tr>
                          <th>SR NO.</th>
                          <th>CREATE DATE</th>
                          <th>COMPANY NAME</th>
                          <th>CUSTOMER NAME</th>
                          <th>EMAIL</th>
                          <th>CONTACT NO</th>
                          <th>CITY</th>
                          <th>ADDRESS</th>
                          <th>QUOTATIONS EXPIRY</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div> 
          

              <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
              <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                          <h4 class="modal-title">Notification History</h4>
                      </div>
                      <div class="modal-body">
                          <div class="row">
                              <div class="col-md-12">
                                  <table style="width: 100%; font-size:14px;" border="1">
                                    <thead>
                                      <tr>
                                          <th style="padding: 5px; background-color: lightgray;" width="20%">Notification Sent On</th>
                                          <th style="padding: 5px; background-color: lightgray;" width="20%">Notification Sent By</th>
                                      </tr>
                                    </thead>
                                    <tbody id="notification_history">
                                      
                                    </tbody>
                                  </table>
                              </div>
                          </div>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                      </div>
                  </div>
              </div>
              </form>
          </div><!-- /.modal -->

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
$('#example').dataTable({
 "bProcessing": false,
 "pagination":true,
 fixedHeader: true,
"bSort": false,
 fixedColumns:   {
  leftColumns: 3
  },

  pageLength:50,
 "sAjaxSource": "<?php echo page_url;?>Customer/quotations_expiring_today_list",

 "aoColumns": [

                { mData: 'sr_no' },
                { mData: 'create_date' },
                { mData: 'company_name' },
                { mData: 'customer_name' },
                { mData: 'email' },
                { mData: 'mobile' },
                { mData: 'city' },
                { mData: 'address' },
                { mData: 'products' },
                { mData: 'quotation' },
                { mData: 'extend_by' },
                { mData: 'notify_customers' }

                ]

        }); 

$('#example1').dataTable({
 "bProcessing": false,
 "pagination":true,
 fixedHeader: true,
"bSort": false,
 fixedColumns:   {
  leftColumns: 3
  },

 "sAjaxSource": "<?php echo page_url;?>Customer/quotations_expiration_ext",

 "aoColumns": [

                { mData: 'sr_no' },
                { mData: 'create_date' },
                { mData: 'company_name' },
                { mData: 'customer_name' },
                { mData: 'email' },
                { mData: 'mobile' },
                { mData: 'city' },
                { mData: 'address' },
                { mData: 'products' }

                ]

        });  

});





</script>

<script>

function extend_quotation_validity(id, j) {
  if(j == 1) {
    var validity = 'a week';
  } else if(j == 2) {
    var validity = '15 days';
  } else if (j == 3) {
    var validity = 'a month';
  } else if (j == 4) {
    var validity = 'a year';
  }

  if(confirm('Are you sure you want to extend this quotation validity by '+validity+'?')) {
      document.location = "<?php echo page_url;?>Customer/increase_validity/"+id+"/"+j;
  }
}

	 function validate() {
	 	 // alert('hi');
	     $("#saves").attr('disabled',false);        
	      $("#saves").val('Submit');

		   var checkeditem = $('#frm input:checked').length;
		   // alert(checkeditem);
		       if(checkeditem > 0) {
		            $("#saves").attr('disabled',true);        
		             $("#saves").val('Submit');
		           return true;
		       } else {
		        $("#saves").attr('disabled',false);
		        $("#saves").val('Send Notifications to Customers');
		       alert('Atleast one customer should be checked to send notification');
		       return false;
		   } 

	}


</script>




<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>

<script>

function accept_reject_remarks(detail_id) {
  $('#detail_id').val(detail_id);
  $('#myModal').modal('show');
}

function getNotificationHistory(quotation_id) {
  $('#myModal').modal('show');

  $.ajax({
        type:"post",
        url:"<?php echo page_url;?>Customer/getNotificationHistory",
        data:{ quotation_id: quotation_id},
          success:function(data) {
            $("#notification_history").html(data);
        }
      });
  // $('#quotation_id').val(quotation_id);
}
</script>



</body>

</html>
