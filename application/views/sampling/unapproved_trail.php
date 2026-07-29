<?php 
$user_id = $_SESSION['logged_in']['user_id'];
?>
<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="NJ Media">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title><?php echo sitetitle; ?> Unapproved Trail</title>



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
                  <div class="col-md-9">
                    <h4 class="page-title">Unapproved Trail of New Customer</h4>
                  </div>
                  <!--<div class="col-md-3" style="margin-top: 30px;">
                    <a href="<?php echo page_url;?>Sampling/Trial_request_history" class="btn btn-warning">View Trial History</a>
                  </div>-->
                  <!-- <div class="col-md-2" style="margin-top: 30px;">
                    <a href="<?php echo page_url;?>Sampling/trial_readings/t4r4i4a4l" class="btn btn-warning">View Readings </a>
                  </div> -->
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
                          <th>CUSTOMER COMPANY NAME</th>
                          <th>CUSTOMER NAME</th>
                          <th>TRIALS TO BE DONE</th>
                          <th>REMARKS</th>
                          <th>ADDRESS</th>
                          <th>REQUEST RAISED BY/ON</th>
                          <th>UPDATE STATUS</th>
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
        <form action="<?php echo page_url;?>Sampling/addremarks_trial" method="post">
          <input type="hidden" name="request_id" id="request_id" value="">
          <input type="hidden" name="lead_id" id="lead_id" value="">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">ADD REMARKS FOR TRIAL SENT</h4>
        </div>
        <div class="modal-body">
       <div class="row">
         <div class="col-md-12">
          <textarea name="remarks" class="form-control" placeholder="Add Remarks" required style="resize:none;"></textarea>
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
		  

                <!-- Modal -->
        <div id="myModalClose" class="modal fade" role="dialog">
        <div class="modal-dialog">

        <!-- Modal content-->
        <form action="<?php echo page_url;?>Sampling/close_trial" method="post">
          <input type="hidden" name="request_id" id="requests_id" value="">
          <input type="hidden" name="lead_id" id="leads_id" value="">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">ADD REASON FOR CLOSING TRIAL</h4>
        </div>
        <div class="modal-body">
       <div class="row">
         <div class="col-md-12">
          <textarea name="remarks" class="form-control" placeholder="Add Remarks" required style="resize:none;"></textarea>
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
<?php if($user_id==1 || $user_id==25){?>
 "sAjaxSource": "<?php echo page_url;?>Sampling/unapproved_trial_request",
<?php }else{?>
   "sAjaxSource": "<?php echo page_url;?>Sampling/unapproved_trial_request/<?php echo $user_id;?>",
<?php }?>


 "aoColumns": [

                { mData: 'sr_no' },
                { mData: 'company_name' },
                { mData: 'customer_name' },
                { mData: 'products' },
                { mData: 'remarks' },
                { mData: 'shipaddress' },
                { mData: 'raisedby' },
                { mData: 'status' }
               
               

                ]

                



        });

});





</script>

<script>

$( document ).ready(function() {
$('#example3').dataTable({
 "bProcessing": false,
 "pagination":true,
 fixedHeader: true,
"bSort": false,
 fixedColumns:   {
  leftColumns: 3
  },
<?php if($user_id==1 || $user_id==25){?>
 "sAjaxSource": "<?php echo page_url;?>Sampling/trial_request_of_old_customers",
<?php }else{?>
  "sAjaxSource": "<?php echo page_url;?>Sampling/trial_request_of_old_customers/<?php echo $user_id;?>",
<?php }?>

 "aoColumns": [

                { mData: 'sr_no' },
                { mData: 'company_name' },
                { mData: 'customer_name' },
                { mData: 'products' },
                { mData: 'remarks' },
                { mData: 'shipaddress' },
                { mData: 'raisedby' },
                { mData: 'status' }
               
               

                ]

                



        });

});





</script>

<script type="text/javascript">
  function send_to_tally(id) {
    var send_to_tally = $('#send_to_tally'+id).val();

    if(send_to_tally != '') {
      $.ajax({
            type:"post",
            url:"<?php echo page_url;?>Billing/send_to_tally",
            data:{id: id},
            success:function(data){
              $('#sent_to_tally'+id).html(data);
            }
            });
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

  function add_remarks(id, lead_id)
  {
    $("#myModal").modal('show');
    $("#request_id").val(id);
    $("#lead_id").val(lead_id);


  }

    function close_trial(id, lead_id)
  {
    $("#myModalClose").modal('show');
    $("#requests_id").val(id);
    $("#leads_id").val(lead_id);


  }
</script>

<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>




</body>

</html>
