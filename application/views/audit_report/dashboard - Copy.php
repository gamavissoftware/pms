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
                <div class="row">
                    <div class="col-sm-12">
                        
                        <h4 class="page-title">Department List</h4>
                    </div>
                </div>
 <div class="col-md-12">
					<div class="row">
					<div class="col-lg-2 col-md-2 col-xs-12 col-sm-12">
                        <div class="card-box widget-user">
                            <div class="text-center">
							<div class="row">
								
								<div class="col-md-12"><h3>Service</h3></div>
								 <div class="panel-group" id="accordion">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 data-toggle="collapse" data-parent="#accordion" href="#collapse1" class="panel-title expand">
           <div class="right-arrow pull-right">+</div>
          <a href="#">Audit Report</a>
        </h4>
      </div>
      <div id="collapse1" class="panel-collapse collapse">
        <div class="panel-body">
			<a href="<?php echo page_url;?>Audit_report/add_task"><span class="btn btn-xs btn-success">Add Task</span></a>
			<span class="btn btn-xs btn-warning">View Audit Report</span>
		
		</div>
      </div>
    </div>
   </div>			
	</div>
    </div>
      </div>
          </div>
				<div class="col-lg-2 col-md-2 col-xs-12 col-sm-12">
                        <div class="card-box widget-user">
                            <div class="text-center">
							<div class="row">
								
								<div class="col-md-12"><h3>HR</h3></div>
								 <div class="panel-group" id="accordion1">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 data-toggle="collapse" data-parent="#accordion2" href="#collapse2" class="panel-title expand">
           <div class="right-arrow pull-right">+</div>
          <a href="#">Audit Report</a>
        </h4>
      </div>
      <div id="collapse2" class="panel-collapse collapse">
        <div class="panel-body">
			<span class="btn btn-xs btn-success	">Add Task</span>
			<span class="btn btn-xs btn-warning">View Audit Report</span>
		
		</div>
      </div>
    </div>
   </div>			
	</div>
    </div>
      </div>
          </div>	
				<div class="col-lg-2 col-md-2 col-xs-12 col-sm-12">
                        <div class="card-box widget-user">
                            <div class="text-center">
							<div class="row">
								
								<div class="col-md-12"><h3>QC</h3></div>
								 <div class="panel-group" id="accordion1">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 data-toggle="collapse" data-parent="#collapse3" href="#collapse3" class="panel-title expand">
           <div class="right-arrow pull-right">+</div>
          <a href="#">Audit Report</a>
        </h4>
      </div>
      <div id="collapse3" class="panel-collapse collapse">
        <div class="panel-body">
			<span class="btn btn-xs btn-success	">Add Task</span>
			<span class="btn btn-xs btn-warning">View Audit Report</span>
		
		</div>
      </div>
    </div>
   </div>			
	</div>
    </div>
      </div>
          </div>	
				<div class="col-lg-2 col-md-2 col-xs-12 col-sm-12">
                        <div class="card-box widget-user">
                            <div class="text-center">
							<div class="row">
								
								<div class="col-md-12"><h3>Sales</h3></div>
								 <div class="panel-group" id="accordion4">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 data-toggle="collapse" data-parent="#collapse4" href="#collapse4" class="panel-title expand">
           <div class="right-arrow pull-right">+</div>
          <a href="#">Audit Report</a>
        </h4>
      </div>
      <div id="collapse4" class="panel-collapse collapse">
        <div class="panel-body">
			<span class="btn btn-xs btn-success	">Add Task</span>
			<span class="btn btn-xs btn-warning">View Audit Report</span>
		
		</div>
      </div>
    </div>
   </div>			
	</div>
    </div>
      </div>
          </div>	
				<div class="col-lg-2 col-md-2 col-xs-12 col-sm-12">
                        <div class="card-box widget-user">
                            <div class="text-center">
							<div class="row">
								
								<div class="col-md-12"><h3>Reception</h3></div>
								 <div class="panel-group" id="accordion1">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 data-toggle="collapse" data-parent="#accordion5" href="#accordion5" class="panel-title expand">
           <div class="right-arrow pull-right">+</div>
          <a href="#">Audit Report</a>
        </h4>
      </div>
      <div id="accordion5" class="panel-collapse collapse">
        <div class="panel-body">
			<span class="btn btn-xs btn-success	">Add Task</span>
			<span class="btn btn-xs btn-warning">View Audit Report</span>
		
		</div>
      </div>
    </div>
   </div>			
	</div>
    </div>
      </div>
          </div>	
				<div class="col-lg-2 col-md-2 col-xs-12 col-sm-12">
                        <div class="card-box widget-user">
                            <div class="text-center">
							<div class="row">
								
								<div class="col-md-12"><h3>Purchase</h3></div>
								 <div class="panel-group" id="accordion6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 data-toggle="collapse" data-parent="#collapse6" href="#collapse6" class="panel-title expand">
           <div class="right-arrow pull-right">+</div>
          <a href="#">Audit Report</a>
        </h4>
      </div>
      <div id="collapse6" class="panel-collapse collapse">
        <div class="panel-body">
			<span class="btn btn-xs btn-success	">Add Task</span>
			<span class="btn btn-xs btn-warning">View Audit Report</span>
		
		</div>
      </div>
    </div>
   </div>			
	</div>
    </div>
      </div>
          </div>	
				<div class="col-lg-2 col-md-2 col-xs-12 col-sm-12">
                        <div class="card-box widget-user">
                            <div class="text-center">
							<div class="row">
								
								<div class="col-md-12"><h3>Maintenance</h3></div>
								 <div class="panel-group" id="accordion1">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 data-toggle="collapse" data-parent="#accordion7" href="#accordion7" class="panel-title expand">
           <div class="right-arrow pull-right">+</div>
          <a href="#">Audit Report</a>
        </h4>
      </div>
      <div id="accordion7" class="panel-collapse collapse">
        <div class="panel-body">
			<span class="btn btn-xs btn-success	">Add Task</span>
			<span class="btn btn-xs btn-warning">View Audit Report</span>
		
		</div>
      </div>
    </div>
   </div>			
	</div>
    </div>
      </div>
          </div>	
				<div class="col-lg-2 col-md-2 col-xs-12 col-sm-12">
                        <div class="card-box widget-user">
                            <div class="text-center">
							<div class="row">
								
								<div class="col-md-12"><h3>Store</h3></div>
								 <div class="panel-group" id="accordion1">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 data-toggle="collapse" data-parent="#accordion8" href="#accordion8" class="panel-title expand">
           <div class="right-arrow pull-right">+</div>
          <a href="#">Audit Report</a>
        </h4>
      </div>
      <div id="accordion8" class="panel-collapse collapse">
        <div class="panel-body">
			<span class="btn btn-xs btn-success	">Add Task</span>
			<span class="btn btn-xs btn-warning">View Audit Report</span>
		
		</div>
      </div>
    </div>
   </div>			
	</div>
    </div>
      </div>
          </div>	
				<div class="col-lg-2 col-md-2 col-xs-12 col-sm-12">
                        <div class="card-box widget-user">
                            <div class="text-center">
							<div class="row">
								
								<div class="col-md-12"><h3>Purchase</h3></div>
								 <div class="panel-group" id="accordion1">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 data-toggle="collapse" data-parent="#accordion9" href="#accordion9" class="panel-title expand">
           <div class="right-arrow pull-right">+</div>
          <a href="#">Audit Report</a>
        </h4>
      </div>
      <div id="accordion9" class="panel-collapse collapse">
        <div class="panel-body">
			<span class="btn btn-xs btn-success	">Add Task</span>
			<span class="btn btn-xs btn-warning">View Audit Report</span>
		
		</div>
      </div>
    </div>
   </div>			
	</div>
    </div>
      </div>
          </div>	
					
				
                </div>
                <!-- end row -->
			</div>

                


              

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