<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">
		<link rel="shortcut icon" href="assets/images/favicon.ico">
		<title><?php echo sitetitle; ?></title>
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


    </head>


    <body>

        <!-- Navigation Bar-->
       <header id="topnav">
            <div class="topbar-main">
                <div class="container">

                    <!-- LOGO -->
                    <div class="topbar-left">
                        <a href="javascript:void(0);" class="logo" style="background-color:#fff;  border-radius: 10px; margin-bottom:10px"><img src="assets/images/logo-1.png"></a>
                    </div>
                    <!-- End Logo container-->


                    <div class="menu-extras">

                        <ul class="nav navbar-nav navbar-right pull-right">
                            <li>
                                <form role="search" class="navbar-left app-search pull-left hidden-xs">
                                     <input type="text" placeholder="Search..." class="form-control">
                                     <a href=""><i class="fa fa-search"></i></a>
                                </form>
                            </li>
                            <li>
                                <!-- Notification -->
                                <div class="notification-box">
                                    <ul class="list-inline m-b-0">
                                        <li>
                                            <a href="javascript:void(0);" class="right-bar-toggle">
                                                <i class="zmdi zmdi-notifications-none"></i>
                                            </a>
                                            <div class="noti-dot">
                                                <span class="dot"></span>
                                                <span class="pulse"></span>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <!-- End Notification bar -->
                            </li>

                            <li class="dropdown user-box">
                                <a href="" class="dropdown-toggle waves-effect waves-light profile " data-toggle="dropdown" aria-expanded="true">
                                    <img src="assets/images/users/gaurav.png" alt="user-img" class="img-circle user-img">
                                    <div class="user-status away"><i class="zmdi zmdi-dot-circle"></i></div>
                                </a>

                                <ul class="dropdown-menu">
                                    <li><a href="javascript:void(0)"><i class="ti-user m-r-5"></i> Profile</a></li>
                                    <li><a href="javascript:void(0)"><i class="ti-settings m-r-5"></i> Settings</a></li>
                                    <li><a href="javascript:void(0)"><i class="ti-lock m-r-5"></i> Lock screen</a></li>
                                    <li><a href="javascript:void(0)"><i class="ti-power-off m-r-5"></i> Logout</a></li>
                                </ul>
                            </li>
                        </ul>
                        <div class="menu-item">
                            <!-- Mobile menu toggle-->
                            <a class="navbar-toggle">
                                <div class="lines">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </a>
                            <!-- End mobile menu toggle-->
                        </div>
                    </div>

                </div>
            </div>

            <div class="navbar-custom">
                <div class="container">
                    <div id="navigation">
                        <!-- Navigation Menu-->
                        <ul class="navigation-menu">
                            <li>
                                <a href="javascript:void(0);"><i class="zmdi zmdi-view-dashboard"></i> <span> Dashboard </span> </a>
                            </li>
							
							<li class="has-submenu">
                                <a href="#"><i class="ti-settings"></i><span> Masters </span> </a>
                                <ul class="submenu">
                                    <li><a href="javascript:void(0);">Master -1</a></li>
                                    <li><a href="javascript:void(0);">Master -2</a></li>
                                    <li><a href="javascript:void(0);">Master -3</a></li>
                                    <li><a href="javascript:void(0);">Master -4</a></li>
                                    <li><a href="javascript:void(0);">Master -5</a></li>
                                    <li><a href="javascript:void(0);">Master -6</a></li>
                                    
                                    
                                </ul>
                            </li>
							
							 <li class="has-submenu">
                                <a href="#"><i class="fa fa-users"></i><span> User Management</span> </a>
                                <ul class="submenu">
                                     <li><a href="javascript:void(0);">Users Role</a></li>
                                    <li><a href="javascript:void(0);">Users List</a></li>
                                    
                                </ul>
                            </li>
							
							 <li class="has-submenu">
                                <a href="#"><i class="ti-folder"></i><span> Quotation Module</span> </a>
                                <ul class="submenu">
                                     <li><a href="javascript:void(0);">Create New</a></li>
                                    <li><a href="javascript:void(0);">List All Quotation</a></li>
                                    
                                </ul>
                            </li>
							
							 <li>
                                <a href="#"><i class="ti-id-badge"></i><span> Office Maintenance Module</span> </a>
                                
                            </li>
							 
							 <li class="has-submenu">
                                <a href="#"><i class="ti-support "></i><span> Tech Support Module</span> </a>
                                <ul class="submenu">
                                     <li><a href="javascript:void(0);">Raise New Ticket</a></li>
                                    <li><a href="javascript:void(0);">View Status</a></li>
                                    
                                </ul>
                            </li>
                            
                        </ul>
                        <!-- End navigation menu  -->
                    </div>
                </div>
            </div>
        </header>
        <!-- End Navigation Bar-->


        <div class="wrapper">
            <div class="container">

                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                      
                        <h4 class="page-title">Create New User Account</h4>
                    </div>
                </div>


                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box">

                          
                            <div class="row">
                               
                                    <div class="col-md-3">
                                       <div class="form-group">
                                            <label for="exampleInputEmail1">First Name</label>
                                            <input class="form-control" id="exampleInputEmail1" placeholder="" type="text">
                                        </div>
									</div>
									<div class="col-md-3">
                                         <div class="form-group">
                                            <label for="exampleInputEmail1">Last Name</label>
                                            <input class="form-control" id="exampleInputEmail1" placeholder="" type="text">
                                        </div>
										</div>
										<div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Email address</label>
                                            <input class="form-control" id="exampleInputEmail1" placeholder="" type="email">
                                        </div>
										</div>
										<div class="col-md-3">
                                         <div class="form-group">
                                            <label for="exampleInputEmail1">Contact Number</label>
                                            <input class="form-control" id="exampleInputEmail1" placeholder="" type="text">
                                        </div>
										</div>
                                       <div class="col-md-3">
									   <div class="form-group">
                                            <label for="exampleInputEmail1">Alternate Contact</label>
                                            <input class="form-control" id="exampleInputEmail1" placeholder="" type="text">
                                        </div>
										</div>
										
										 <div class="col-md-3">
									   <div class="form-group">
                                            <label for="exampleInputEmail1">Father Name</label>
                                            <input class="form-control" id="exampleInputEmail1" placeholder="" type="text">
                                        </div>
										</div>
										
										 <div class="col-md-3">
									   <div class="form-group">
                                            <label for="exampleInputEmail1">Mother Name</label>
                                            <input class="form-control" id="exampleInputEmail1" placeholder="" type="text">
                                        </div>
										</div>
										
										 <div class="col-md-3">
									   <div class="form-group">
                                            <label for="exampleInputEmail1">Date of Birth</label>
                                            <input class="form-control" id="exampleInputEmail1" placeholder="" type="date">
                                        </div>
										</div>
										 <div class="col-md-3">
									   <div class="form-group">
                                            <label for="exampleInputEmail1">Date of Joining</label>
                                            <input class="form-control" id="exampleInputEmail1" placeholder="" type="date">
                                        </div>
										</div>
										
										 <div class="col-md-3">
									   <div class="form-group">
                                            <label for="exampleInputEmail1">Qualification</label>
                                           <select class="form-control">
												<option value="">--Select--</option>
												<option value="">Graduate</option>
												<option value="">Post Graduate</option>
											
										   </select>
                                        </div>
										</div>
										
										
										 <div class="col-md-3">
									   <div class="form-group">
                                            <label for="exampleInputEmail1">Adhar Card</label>
                                            <input class="form-control" id="exampleInputEmail1" placeholder="" type="file">
                                        </div>
										</div>
										
										
										 <div class="col-md-3">
									   <div class="form-group">
                                            <label for="exampleInputEmail1">PAN Number</label>
                                            <input class="form-control" id="exampleInputEmail1" placeholder="" type="text">
                                        </div>
										</div>
										
										 <div class="col-md-3">
									   <div class="form-group">
                                            <label for="exampleInputEmail1">Upload Profile Photo</label>
                                            <input class="form-control" id="exampleInputEmail1" placeholder="" type="file">
                                        </div>
										</div>
										
										
										 <div class="col-md-3">
									   <div class="form-group">
                                            <label for="exampleInputEmail1">User Role</label>
                                             <select class="form-control">
												<option value="">--Select--</option>
												<option value="">Administrator</option>
												<option value="">HR</option>
												<option value="">Accountant</option>
												<option value="">Manager</option>
											
										   </select>
                                        </div>
										</div>
										
										 <div class="col-md-3">
									   <div class="form-group">
                                            <label for="exampleInputEmail1">Address</label>
											<textarea class="form-control"></textarea>
                                        </div>
										</div>
										
										
										 <div class="col-md-3">
									   <div class="form-group">
                                            <label for="exampleInputEmail1">Status</label>
											<select class="form-control">
												<option value="">--Select--</option>
												<option value="">Active</option>
												<option value="">Not Active</option>
												
										   </select>
                                        </div>
										</div>
										
										 <div class="col-md-3">
									   <div class="form-group pull-right" style="margin-top:25px">
                                           <span class="btn btn-success">Save</span>
                                        </div>
										</div>
                                   
                                


                            </div><!-- end row -->
                        </div>
                    </div><!-- end col -->
                </div>
                <!-- end row -->

                


                <!-- Footer -->
                <footer class="footer text-right">
                    <div class="container">
                        <div class="row">
                            <div class="col-xs-6">
                                 2018 © Prestogroup Designed & Developed by <a href="http://www.netjunglemedia.com" target="_blank">Netjungle Media</a>.
                            </div>
                            <div class="col-xs-6">
                              
                            </div>
                        </div>
                    </div>
                </footer>
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


        <!-- App js -->
        <script src="assets/js/jquery.core.js"></script>
        <script src="assets/js/jquery.app.js"></script>

    </body>
</html>