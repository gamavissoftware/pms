<!DOCTYPE html>


<html>


    <head>


        <meta charset="utf-8">


        <meta name="viewport" content="width=device-width, initial-scale=1.0">


        <meta name="description" content="">


        <meta name="author" content="<?php //echo copyright; ?>">





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


		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>


		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 


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





        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
	<?PHP 
//$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
//foreach($q->result() as $LOGO);
?>

    <style>


table.manglesh thead th {


				background: red;


				color:#fff;


				font-weight:bold;


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
    <div class="container-fluid" >
<div class="dashboard-header">
<h1>Trigger Notifications Manually</h1><hr>
</div>

<div class="dashboard-header">
<?php echo $this->session->flashdata('message');?>
</div>

  <div >
        <div class="row">
            <div class="col-md-12">
                <h3>Notifications to Customers</h3>
            </div>
        
            <div class="col-sm-4 col-md-4 col-lg-2">
        <form action="<?php echo page_url;?>Dbbackup/customer_payment_reminders_to_customer/1
            " method="post">
            <div class="report-box " style="background-color:#ff6961;color:white" >
            <div class="text-center" style="margin-bottom: 10px;">
            <i class="fa fa-volume-up " style="font-size: 39px;"></i>
            </div>
            <p style="color:white;font-weight:bold;">CUSTOMER PAYMENT REMINDER</p>
            <p><input type="text" class="form-control" name="mobile" placeholder="Enter Mobile No. seperated by comma"></p><br/>
             <p><input type="submit" class="btn btn-default" style="background-color: white;" value="Send"></p>
            </div>
        </form>
            </div>




   
    </div>


     <div class="row" style="margin-top:20px;">
            <div class="col-md-12">
                <h3>Notifications to Admin</h3>
            </div>
        
             <div class="col-sm-4 col-md-4 col-lg-2">
        <form action="<?php echo page_url;?>Dbbackup/customer_payment_reminders_to_admin_new/1
            " method="post">
            <div class="report-box " style="background-color:#ff6961;color:white" >
            <div class="text-center" style="margin-bottom: 10px;">
            <i class="fa fa-volume-up " style="font-size: 39px;"></i>
            </div>
            <p style="color:white;font-weight:bold;">CUSTOMER PAYMENT</p>
            <p><input type="text" class="form-control" name="mobile" placeholder="Enter Mobile No. seperated by comma"></p><br/>
             <p><input type="submit" class="btn btn-default" style="background-color: white;" value="Send"></p>
            </div>
        </form>
            </div>



   <div class="col-sm-4 col-md-4 col-lg-2">
        <form action="<?php echo page_url;?>Dbbackup/order_on_hold_admin/1
            " method="post">
            <div class="report-box " style="background-color:#ff6961;color:white" >
            <div class="text-center" style="margin-bottom: 10px;">
            <i class="fa fa-volume-up " style="font-size: 39px;"></i>
            </div>
            <p style="color:white;font-weight:bold;">ORDERS ON HOLD</p>
            <p><input type="text" class="form-control" name="mobile" placeholder="Enter Mobile No. seperated by comma"></p><br/>
             <p><input type="submit" class="btn btn-default" style="background-color: white;" value="Send"></p>
            </div>
        </form>
            </div>

             <div class="col-sm-4 col-md-4 col-lg-2">
        <form action="<?php echo page_url;?>Dbbackup/cheque_to_be_recieved_user/1
            " method="post">
            <div class="report-box " style="background-color:#ff6961;color:white" >
            <div class="text-center" style="margin-bottom: 10px;">
            <i class="fa fa-volume-up " style="font-size: 39px;"></i>
            </div>
            <p style="color:white;font-weight:bold;">PDC TO BE RECIEVED</p>
            <p><input type="text" class="form-control" name="mobile" placeholder="Enter Mobile No. seperated by comma"></p><br/>
             <p><input type="submit" class="btn btn-default" style="background-color: white;" value="Send"></p>
            </div>
        </form>
            </div>

             <div class="col-sm-4 col-md-4 col-lg-2">
        <form action="<?php echo page_url;?>Dbbackup/cheque_to_be_deposited/1
            " method="post">
            <div class="report-box " style="background-color:#ff6961;color:white" >
            <div class="text-center" style="margin-bottom: 10px;">
            <i class="fa fa-volume-up " style="font-size: 39px;"></i>
            </div>
            <p style="color:white;font-weight:bold;">PDC TO BE DEPOSITED</p>
            <p><input type="text" class="form-control" name="mobile" placeholder="Enter Mobile No. seperated by comma"></p><br/>
             <p><input type="submit" class="btn btn-default" style="background-color: white;" value="Send"></p>
            </div>
        </form>
            </div>
            
   
    </div>

     <div class="row" style="margin-top:20px;">
            <div class="col-md-12">
                <h3>Notifications to Users</h3>
            </div>
        
           <div class="col-sm-4 col-md-4 col-lg-2">
        <form action="<?php echo page_url;?>Dbbackup/order_on_hold_agent/1
            " method="post">
            <div class="report-box " style="background-color:#ff6961;color:white" >
            <div class="text-center" style="margin-bottom: 10px;">
            <i class="fa fa-volume-up " style="font-size: 39px;"></i>
            </div>
            <p style="color:white;font-weight:bold;">ORDERS ON HOLD</p>
            <p><input type="text" class="form-control" name="no." placeholder="Enter Mobile No. seperated by comma"></p><br/>
             <p><input type="submit" class="btn btn-default" style="background-color: white;" value="Send"></p>
            </div>
        </form>
            </div>



   <div class="col-sm-4 col-md-4 col-lg-2">
        <form action="<?php echo page_url;?>Dbbackup/cheque_to_be_recieved_user/1
            " method="post">
            <div class="report-box " style="background-color:#ff6961;color:white" >
            <div class="text-center" style="margin-bottom: 10px;">
            <i class="fa fa-volume-up " style="font-size: 39px;"></i>
            </div>
            <p style="color:white;font-weight:bold;">PDC TO BE RECIEVED</p>
            <p><input type="text" class="form-control" name="no." placeholder="Enter Mobile No. seperated by comma"></p><br/>
             <p><input type="submit" class="btn btn-default" style="background-color: white;" value="Send"></p>
            </div>
        </form>
            </div>

             <div class="col-sm-4 col-md-4 col-lg-2">
        <form action="<?php echo page_url;?>Dbbackup/cheque_to_be_deposited/1
            " method="post">
            <div class="report-box " style="background-color:#ff6961;color:white" >
            <div class="text-center" style="margin-bottom: 10px;">
            <i class="fa fa-volume-up " style="font-size: 39px;"></i>
            </div>
            <p style="color:white;font-weight:bold;">PDC TO BE DEPOSITED</p>
            <p><input type="text" class="form-control" name="no." placeholder="Enter Mobile No. seperated by comma"></p><br/>
             <p><input type="submit" class="btn btn-default" style="background-color: white;" value="Send"></p>
            </div>
        </form>
            </div>
            
            
   
    </div>
</div>




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


        <script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>


        <!-- Datatable init js -->


        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>


<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>


        <!-- App js -->


        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>


        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>


		
    <script>
        
        $( document ).ready(function(){
            $.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/getsalesreportingcount/1',
                type: 'get',
                success: function(data){
                    $('#sales_visit').attr('data-badge', data);
                }
            });
            
             $.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/getsalesreportingcount/2',
                type: 'get',
                success: function(data){
                    $('#daily_update').attr('data-badge', data);
                }
            });
            
             $.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/getsalesreportingcount/3',
                type: 'get',
                success: function(data){
                    $('#payment_collection').attr('data-badge', data);
                }
            });
        });
 
    </script>

</body>


</html>