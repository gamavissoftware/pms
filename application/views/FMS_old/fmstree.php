<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Edit FMS Flow</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
		<link href="<?php echo assets_url;?>plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
        
        <style>
            
            
           h1 {
            width: 580px;
            font-family: verdana, arial, helvetica, sans-serif;
            font-size: 18px;
            text-align: center;
            margin: 40px auto;
        }
        
        #container {
            width: 580px;
            font-family: verdana, arial, helvetica, sans-serif;
            font-size: 11px;
            text-align: center;
            margin: auto;
        }
        
        #no1 {
            width: 200px;
            line-height: 60px;
            border: 1px solid #00c4ffeb;
            margin: auto;
            font-size: 13px;
            background-image: linear-gradient(#f5efef87, #00c4ffeb);
        }
        
        #no2 {
            width: 260px;
            line-height: 60px;
            border: 1px solid #e4b44a;
            margin: auto;
            font-size: 13px;
            background-color: #f9e3b2;
        }
        
        #no3 {
            width: 173px;
            border: 1px solid #ecac7f;
            font-size: 13px;
            background-color: #f7d7c1;
            margin-left: -42px;
            padding: 15px;
        }
        
        #no4 {
            width: 173px;
            border: 1px solid #f56e6e;
            font-size: 13px;
            background-color: #f7c1c1;
            margin-left: 449px;
            line-height: 60px;
        }
        
        #no5 {
            width: 173px;
            border: 1px solid #e28445;
            font-size: 13px;
            background-color: #f1c3a4;
            margin-left: -42px;
            line-height: 60px;
        }
        
        #no6 {
            width: 173px;
            border: 1px solid #0f6c88;
            font-size: 13px;
            background-color: #57c8ea;
            margin-left: 449px;
            line-height: 60px;
            margin-top: 92px;
            position: absolute;
        }
        
        #no7 {
            width: 200px;
            line-height: 60px;
            border: 1px solid #00c4ffeb;
            margin: auto;
            font-size: 13px;
            background-image: linear-gradient(#f5efef87, #00c4ffeb);
        }
        
        #no8 {
            width: 200px;
            line-height: 60px;
            border: 1px solid #ec2a69;
            margin: auto;
            font-size: 13px;
            background-image: linear-gradient(#f5efef87, #ff00527d);
        }
        
        #no9 {
            width: 173px;
            border: 1px solid #7e5ffd;
            font-size: 13px;
            background-color: #b7ade0;
            margin-left: 0px;
            line-height: 60px;
        }
        
        #no10 {
            width: 173px;
            border: 1px solid #7e5ffd;
            font-size: 13px;
            background-color: #b7ade0;
            margin-left: 428px;
            line-height: 60px;
        }
        
        #no11 {
            width: 173px;
            border: 1px solid #7e5ffd;
            font-size: 13px;
            background-color: #b7ade0;
            margin-left: 0px;
            line-height: 60px;
        }
        
        #no12 {
            width: 173px;
            border: 1px solid #7e5ffd;
            font-size: 13px;
            background-color: #b7ade0;
            margin-left: 428px;
            line-height: 60px;
            margin-top: 92px;
            position: absolute;
        }
        
        #no13 {
            width: 200px;
            line-height: 60px;
            border: 1px solid #198c31;
            margin: auto;
            font-size: 13px;
            background-color: #00801c73;
        }
        
        #no14 {
            width: 200px;
            line-height: 60px;
            border: 1px solid #76dc4e;
            margin: auto;
            font-size: 13px;
            background-color: #bde0af;
        }
        
        #no15 {
            width: 200px;
            line-height: 60px;
            border: 1px solid #0f6c88;
            margin: auto;
            font-size: 13px;
            background-color: #57c8ea;
        }
        
        #line1 {
            font-size: 0;
            width: 2px;
            height: 60px;
            color: #fff;
            background-color: #000;
            margin: auto;
        }
        
        #line2 {
            font-size: 0;
            width: 120px;
            height: 2px;
            color: #fff;
            background-color: #000;
            margin-left: 39px;
            margin-top: 30px;
            position: absolute;
        }
        
        #line3 {
            font-size: 0;
            width: 120px;
            height: 2px;
            color: #fff;
            background-color: #000;
            margin-left: 420px;
            margin-top: 30px;
            position: absolute;
        }
        
        #line4 {
            font-size: 0;
            width: 2px;
            height: 90px;
            color: #fff;
            background-color: #000;
            margin-left: 39px;
            margin-top: -30px;
        }
        
        #line6 {
            font-size: 0;
            width: 2px;
            height: 90px;
            color: #fff;
            background-color: #000;
            margin-left: 39px;
            margin-top: 2px;
        }
        
        #line5 {
            font-size: 0;
            width: 2px;
            height: 90px;
            color: #fff;
            background-color: #000;
            margin-left: 538px;
            margin-top: -154px;
        }
        
        #line7 {
            font-size: 0;
            width: 2px;
            height: 92px;
            color: #fff;
            background-color: #000;
            margin-left: 538px;
            margin-top: 0px;
            position: absolute;
        }
        
        #line8 {
            font-size: 0;
            width: 317px;
            height: 2px;
            color: #fff;
            background-color: #000;
            margin-top: -31px;
            margin-left: 132px;
        }
        
        #line9 {
            font-size: 0;
            width: 2px;
            height: 70px;
            color: #fff;
            background-color: #000;
            margin: auto;
        }
        
        #line10 {
            font-size: 0;
            width: 120px;
            height: 2px;
            color: #fff;
            background-color: #000;
            margin-left: 69px;
            margin-top: 30px;
            position: absolute;
        }
        
        #line11 {
            font-size: 0;
            width: 120px;
            height: 2px;
            color: #fff;
            background-color: #000;
            margin-left: 391px;
            margin-top: 30px;
            position: absolute;
        }
        
        #line12 {
            font-size: 0;
            width: 2px;
            height: 90px;
            color: #fff;
            background-color: #000;
            margin-left: 69px;
            margin-top: -30px;
        }
        
        #line13 {
            font-size: 0;
            width: 2px;
            height: 90px;
            color: #fff;
            background-color: #000;
            margin-left: 510px;
            margin-top: -154px;
        }
        
        #line14 {
            font-size: 0;
            width: 2px;
            height: 90px;
            color: #fff;
            background-color: #000;
            margin-left: 69px;
            margin-top: 2px;
        }
        
        #line15 {
            font-size: 0;
            width: 2px;
            height: 92px;
            color: #fff;
            background-color: #000;
            margin-left: 510px;
            margin-top: 0px;
            position: absolute;
        }
        
        #line16 {
            font-size: 0;
            width: 253px;
            height: 2px;
            color: #fff;
            background-color: #000;
            margin-top: -31px;
            margin-left: 175px;
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
            <div class="container">

                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="btn-group pull-right">
                              
                            </div>
                            <h4 class="page-title">Edit FMS Flow</h4>
							<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->

            <div id="container">
            <div id="no1">MACHINING</div>
            <div id="line1"></div>
            <div id="line2"></div>
            <div id="line3"></div>
            <div id="no2">ORDER PLANNING</div>
            <div id="line4"></div>
            <div id="no3">CHECK MATERIAL & RAISE PR </div>
            <div id="line5"></div>
            <div id="no4">PENDING TO START</div>
            <div id="line7"></div>
            <div id="no6">UNDER MACHINING </div>
            <div id="line6"></div>
            <div id="no5">FULL KITTING</div>
            <div id="line8"></div>
            <div id="line9"></div>
            <div id="no7">IN PROCESS FITTING</div>
            <div id="line9"></div>
            <div id="line10"></div>
            <div id="line11"></div>
            <div id="no8">IN PROCESS QC</div>
            <div id="line12"></div>
            <div id="no9">MATERIAL PAINT OUT</div>
            <div id="line13"></div>
            <div id="no10">MATERIAL PLATING OUT</div>
            <div id="line15"></div>
            <div id="no12">MATERIAL PAINT IN</div>
            <div id="line14"></div>
            <div id="no11">MATERIAL PLATING IN</div>
            <div id="line16"></div>
            <div id="line9"></div>
            <div id="no13">FINAL FITTING</div>
            <div id="line9"></div>
            <div id="no14">ELECTRICAL</div>
            <div id="line9"></div>
            <div id="no15">FINAL QC</div>
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
		<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
		<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>


</body>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
</html>