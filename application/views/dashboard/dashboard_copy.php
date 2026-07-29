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
               
			<div class="col-md-1" style="background-color:#fff;">
				<div class="share-it">
		<div class="facebook">
		 <a href="#"><img src="assets/images/logo-salesforce.png" width="110px"></a>
		</div>
		<div class="twitter">
		 <a href="#"><img src="assets/images/trello-logo-blue.png" width="110px" height="68px"></a>
		</div>
		<div class="finsys hidden-xs">
		 <a href="#"><img src="assets/images/finsys.jpg" width="110px" height="68px"></a>
		</div>
		<div class="google hidden-xs">
		 <a href="#"><img src="assets/images/Chanakya_logo.jpg" width="110px" height="68px"></a>
		</div>
		<div class="rss">
		 <a href="#"><img src="assets/images/intra_logo.png" width="110px" height="68px"></a>
		</div>
		<div class="rss">
		 <a href="#"><img src="assets/images/fieldSense.png" width="110px" height="68px"></a>
		</div>
	  </div>
			</div>
             <div class="col-md-11">
					<div class="row">
				  <h4 class="page-title">Dashboard</h4>
					<div class="col-lg-3 col-md-6">
                        <div class="card-box widget-user">
                            <div class="text-center">
							<div class="row">
								<div class="col-md-4">
								<img src="assets/images/to-do.png">
								</div>
								<div class="col-md-8"><h3>Things to do</h3></div>
								              <div class="panel-group" id="accordion">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 data-toggle="collapse" data-parent="#accordion" href="#collapse1" class="panel-title expand">
           <div class="right-arrow pull-right">+</div>
          <a href="#">Scheduler</a>
        </h4>
      </div>
      <div id="collapse1" class="panel-collapse collapse">
        <div class="panel-body">
			<span class="btn btn-xs btn-warning">DAILY</span>
			<span class="btn btn-xs btn-warning">WEEKLY</span>
			<span class="btn btn-xs btn-warning">MONTHLY</span>
		</div>
      </div>
    </div>
   </div>			
							</div>
                                
                             
                            </div>
                        </div>
                    </div>
					<div class="col-lg-3 col-md-6">
                        <a href="<?php echo page_url;?>Competitor_analysis/compare_competitors_products"><div class="card-box widget-user">
                            <div class="text-center">
                                 <div class="row" style="background-color:#f5f5f5">
								<div class="col-md-4">
								<img src="assets/images/analysis.png">
								</div>
								<div class="col-md-8"><h3>Competitor Comparison</h3></div>
								
							</div>
                            </div>
                        </div></a>
                    </div>
					
					<div class="col-lg-3 col-md-6">
                        <div class="card-box widget-user">
                           <div class="row">
								<div class="col-md-4">
								<img src="assets/images/employee.png">
								</div>
								<div class="col-md-8"><h3>My PMS</h3></div>
								<div class="panel-group" id="accordion1">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 data-toggle="collapse" data-parent="#accordion1" href="#collapse2" class="panel-title expand">
           <div class="right-arrow pull-right">+</div>
          <a href="#">&nbsp;</a>
        </h4>
      </div>
      <div id="collapse2" class="panel-collapse collapse">
        <div class="panel-body">
			<table id="tech-companies-1" class="table" style="border:1px solid">
                                        <thead>
                                            <tr>
                                                <th>Work</th>
                                                <th data-priority="1">%</th>
                                              
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>XYZ</td>
                                                <td>70%</td>
                                              
                                            </tr>
                                            
                                        </tbody>
                                    </table>
		</div>
      </div>
    </div>
    
   
  </div>	
							</div>
                        </div>
                    </div>
					
					<div class="col-lg-3 col-md-6">
                        <div class="card-box widget-user">
                            <div class="text-center">
                              <div class="row">
								<div class="col-md-4">
								<img src="assets/images/report.png">
								</div>
								<div class="col-md-8"><h3>Report</h3></div>
													              <div class="panel-group" id="accordion3">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 data-toggle="collapse" data-parent="#accordion3" href="#collapse3" class="panel-title expand">
           <div class="right-arrow pull-right">+</div>
          <a href="#">Scheduler</a>
        </h4>
      </div>
      <div id="collapse3" class="panel-collapse collapse">
        <div class="panel-body">
			<span class="btn btn-xs btn-warning">HUDDLE </span>
			<span class="btn btn-xs btn-warning">EVENING REPORT
</span>
			<span class="btn btn-xs btn-warning">DAR / VISIT REPORT

</span>
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