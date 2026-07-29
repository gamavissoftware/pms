<!DOCTYPE html>


<html>


    <head>


        <meta charset="utf-8">


        <meta name="viewport" content="width=device-width, initial-scale=1.0">


        <meta name="description" content="">


        <meta name="author" content="<?php echo copyright; ?>">





        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">





        <title><?php echo sitetitle; ?> Checklist Dashboard</title>





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
$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
foreach($q->result() as $LOGO);
?>

    <style>


table.manglesh thead th {


				background: <?php echo $LOGO->colorcode;?>;


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
<h1>Checklist Dashboard</h1><hr>
</div>


  
        <div class="row">
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Checklist/view_department_wise_checklist/2"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>sales.png">
</div>
           <p>  Sales</p>
                </div></a>
            </div>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Checklist/view_department_wise_checklist/5"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>service.png">
</div>
           <p>Service</p>
                </div></a>
            </div>
          

            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Checklist/view_department_wise_checklist/14"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>store.png">
</div>
           <p>Store</p>
                </div></a>
            </div>

            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Checklist/view_department_wise_checklist/12"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>purchase.png">
</div>
           <p>Purchase</p>
                </div></a>
            </div>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Checklist/view_department_wise_checklist/6"><div class="report-box " ><div class="text-center">
                <img src="<?php echo dashboard_icon;?>accounts.png">
</div>
           <p>Accounts</p>
                </div></a>
            </div>

            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Checklist/view_department_wise_checklist/4"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>hr.png">
</div>
           <p>HR</p>
                </div></a>
            </div>

            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Checklist/view_department_wise_checklist/7"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>it.png">
</div>
           <p>IT</p>
                </div></a>
            </div>

            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Checklist/view_department_wise_checklist/13"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>user_role.png">
</div>
           <p>Admin</p>
                </div></a>
            </div>
            
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


		


 


</body>


</html>