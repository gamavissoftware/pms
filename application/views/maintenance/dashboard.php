<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="assets/images/favicon.ico">

        <title><?php echo sitetitle; ?> Dashboard</title>

        <!--Morris Chart CSS -->
		<link rel="stylesheet" href="assets/plugins/morris/morris.css">

        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/core.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/components.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/pages.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/menu.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/responsive.css" rel="stylesheet" type="text/css" />
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="assets/js/modernizr.min.js"></script>

<style>
.share-it{
	position:fixed;
	min-height:200px;
	width:40px;
	left:0;
	z-index:9;
	top:20%;
}	
.share-it i{
	font-size:16px;
}
	
a.multipage{background:#ee3046; border:2px #ee3046 solid; color:#fff;} 	
a.multipage:hover{background:#fff; border:2px #fff solid; color:#333;} 	
	
	
	.facebook{margin:10px auto; float:left; margin-right:4px;}
.facebook  a{
	color:#fff;
	padding:1px 1px;
	background-color:#77cdf1;
	display:inline-block;
	transition:0.5s ease;
}

.twitter{margin:0 auto; float:left; margin-right:4px;}
.twitter  a{
	color:#fff;
	padding:1px 1px;
	background-color:#006dac;
	display:inline-block;
	text-align:center;
	transition:0.5s ease;
}


.google{margin:0px auto; float:left; margin-right:4px;}
.google  a{
	color:#fff;
	padding:1px 1px;
	background-color:#ef7f1a;
	display:inline-block;
	transition:0.5s ease;
}
.finsys{margin:10px auto; float:left; margin-right:4px;}
.finsys  a{
	color:#fff;
	padding:1px 1px;
	background-color:#ef7f1a;
	display:inline-block;
	transition:0.5s ease;
}



.rss{margin:10 auto; margin-top:10px; float:left; margin-right:4px;}
.rss a{
	color:#fff;
	padding:1px 1px;
	background-color:#bdcc1f;
	display:inline-block;
	transition:0.5s ease;
}
.panel-group {
    margin-bottom: 0px;
}

</style>
    </head>


    <body>

        <!-- Navigation Bar-->
       <?php $this->load->view('common/nav-menu');?>
        <!-- End Navigation Bar-->


        <div class="wrapper">
            <div class="container-fluid" style="background-color:#fff; min-height:463px;">

                <!-- Page-Title -->
               
			<div class="row" style="margin-top:20px">
                    <div class="col-lg-2 col-md-2 col-xs-12 col-sm-12">
                        <div class="card-box widget-user">
                            <div class="text-center">
                                <h2 class="text-custom" data-plugin="counterup">10</h2>
                                <h5>Business Messages</h5>
                            </div>
                        </div>
                    </div>

                   <a href="<?php echo page_url;?>Office_maintenance/Reminder_meeting"> <div class="col-lg-2 col-md-2 col-xs-12 col-sm-12">
                        <div class="card-box widget-user">
                            <div class="text-center">
                                <h2 class="text-pink" data-plugin="counterup">1</h2>
                                <h5>Reminders</h5>
                            </div>
                        </div>
                    </div></a>

                    <a href="<?php echo page_url;?>Office_maintenance/office_phone_testing"><div class="col-lg-2 col-md-2 col-xs-12 col-sm-12">
                        <div class="card-box widget-user">
                            <div class="text-center">
                                <h2 class="text-warning" data-plugin="counterup">1</h2>
                                <h5>Office Phone Testing</h5>
                            </div>
                        </div>
                    </div></a>

                    <a href="<?php echo page_url;?>Office_maintenance/camera_testing"><div class="col-lg-2 col-md-2 col-xs-12 col-sm-12">
                        <div class="card-box widget-user">
                            <div class="text-center">
                                <h2 class="text-info" data-plugin="counterup">1</h2>
                                <h5>Office Camera Testing</h5>
                            </div>
                        </div>
                    </div></a>
					
					 <a href="<?php echo page_url;?>Office_maintenance/Electricity_load"><div class="col-lg-2 col-md-2 col-xs-12 col-sm-12">
                        <div class="card-box widget-user">
                            <div class="text-center">
                                <h2 class="text-info" data-plugin="counterup">989</h2>
                                <h5>Electricity Load</h5>
                            </div>
                        </div>
                    </div></a>
					
					 <a href="<?php echo page_url;?>Office_maintenance/unpaid_bills"><div class="col-lg-2 col-md-2 col-xs-12 col-sm-12">
                        <div class="card-box widget-user">
                            <div class="text-center">
                                <h2 class="text-info" data-plugin="counterup">10</h2>
                                <h5>Unpaid Bills</h5>
                            </div>
                        </div>
                    </div></a>
                </div>
				
              

                <!-- Footer -->
               <?php $this->load->view('common/footer');?>
                <!-- End Footer -->

            </div>
            <!-- end container -->



            <!-- Right Sidebar -->
            <div class="side-bar right-bar">
                <a href="javascript:void(0);" class="right-bar-toggle">
                    <i class="zmdi zmdi-close-circle-o"></i>
                </a>
                <h4 class="">Notifications</h4>
                <div class="notification-list nicescroll">
                    <ul class="list-group list-no-border user-list">
                        <li class="list-group-item">
                            <a href="#" class="user-list-item">
                                <div class="avatar">
                                    <img src="assets/images/users/avatar-2.jpg" alt="">
                                </div>
                                <div class="user-desc">
                                    <span class="name">Michael Zenaty</span>
                                    <span class="desc">There are new settings available</span>
                                    <span class="time">2 hours ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="#" class="user-list-item">
                                <div class="icon bg-info">
                                    <i class="zmdi zmdi-account"></i>
                                </div>
                                <div class="user-desc">
                                    <span class="name">New Signup</span>
                                    <span class="desc">There are new settings available</span>
                                    <span class="time">5 hours ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="#" class="user-list-item">
                                <div class="icon bg-pink">
                                    <i class="zmdi zmdi-comment"></i>
                                </div>
                                <div class="user-desc">
                                    <span class="name">New Message received</span>
                                    <span class="desc">There are new settings available</span>
                                    <span class="time">1 day ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="list-group-item active">
                            <a href="#" class="user-list-item">
                                <div class="avatar">
                                    <img src="assets/images/users/avatar-3.jpg" alt="">
                                </div>
                                <div class="user-desc">
                                    <span class="name">James Anderson</span>
                                    <span class="desc">There are new settings available</span>
                                    <span class="time">2 days ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="list-group-item active">
                            <a href="#" class="user-list-item">
                                <div class="icon bg-warning">
                                    <i class="zmdi zmdi-settings"></i>
                                </div>
                                <div class="user-desc">
                                    <span class="name">Settings</span>
                                    <span class="desc">There are new settings available</span>
                                    <span class="time">1 day ago</span>
                                </div>
                            </a>
                        </li>

                    </ul>
                </div>
            </div>
            <!-- /Right-bar -->

        </div>



        <!-- jQuery  -->
        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/detect.js"></script>
        <script src="assets/js/fastclick.js"></script>

        <script src="assets/js/jquery.slimscroll.js"></script>
        <script src="assets/js/jquery.blockUI.js"></script>
        <script src="assets/js/waves.js"></script>
        <script src="assets/js/wow.min.js"></script>
        <script src="assets/js/jquery.nicescroll.js"></script>
        <script src="assets/js/jquery.scrollTo.min.js"></script>

        <!-- KNOB JS -->
        <!--[if IE]>
        <script type="text/javascript" src="assets/plugins/jquery-knob/excanvas.js"></script>
        <![endif]-->
        <script src="assets/plugins/jquery-knob/jquery.knob.js"></script>

        <!--Morris Chart-->
		<script src="assets/plugins/morris/morris.min.js"></script>
		<script src="assets/plugins/raphael/raphael-min.js"></script>

        <!-- Dashboard init -->
        <script src="assets/pages/jquery.dashboard.js"></script>

        <!-- App js -->
        <script src="assets/js/jquery.core.js"></script>
        <script src="assets/js/jquery.app.js"></script>

    </body>
</html>