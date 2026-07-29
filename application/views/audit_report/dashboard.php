<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Dashboard</title>

        <!--Morris Chart CSS -->
		<link rel="stylesheet" href="<?php echo assets_url;?>plugins/morris/morris.css">

        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

<style>
.panel-group {
    margin-bottom: 0px;
}
.badge1 {
		position:relative;
	}
	.badge1[data-badge]:after {
		content:attr(data-badge);
		position:absolute;
		top:-10px;
		right:-10px;
		font-size:12px;
		font-weight:bold;
		background:green;
		color:white;
		width:18px;height:18px;
		text-align:center;
		line-height:18px;
		border-radius:50%;
		box-shadow:0 0 1px #333;
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
	<?php 
	$this->db->select('audit_type_id,audit_type, status')->from('audit_type')->where('status','1');
	$query = $this->db->get();
	$res = $query->result();
	foreach($res as $audit_type){
	?>
	<div class="col-lg-2 col-md-2 col-xs-12 col-sm-12">
     <div class="card-box widget-user  badge1" data-badge="
<?php 
$query = $this->db->select('audit_type_id, remark_flag')->from('audit_tasks')->where('audit_type_id',$audit_type->audit_type_id)->where('remark_flag','1')->get();
$res = $query->result();
echo count($res);
?>" style="min-height: 240px;">
     <div class="text-center">
	<div class="row">
	<div class="col-md-12"><h3><?php echo $audit_type->audit_type;?></h3></div>
	<div class="panel-group" id="accordion">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 data-toggle="collapse" data-parent="#accordion" href="#collapse1" class="panel-title expand">
          <a href="#">Audit Report</a>
        </h4>
      </div>
      <div>
        <?php if($audit_type->audit_type_id=='2'){?>
	   <div class="panel-body">
			<a href="<?php echo page_url;?>Audit_report/add_hr_work/<?php echo $audit_type->audit_type_id;?>"><span class="btn btn-xs btn-success">Add Task</span></a>
			<a href="<?php echo page_url;?>Audit_report/add_hr_work/<?php echo $audit_type->audit_type_id;?>"><span class="btn btn-xs btn-warning">View Audit Report</span></a>
		</div>
	  <?php }else{?>
        <div class="panel-body">
			<a href="<?php echo page_url;?>Audit_report/add_task/<?php echo $audit_type->audit_type_id;?>"><span class="btn btn-xs btn-success">Add Task</span></a>
			<a href="<?php echo page_url;?>Audit_report/add_task/<?php echo $audit_type->audit_type_id;?>"><span class="btn btn-xs btn-warning">View Audit Report</span></a>
		</div>
	  <?php }?>
      </div>
    </div>
   </div>			
	</div>
    </div>
      </div>
          </div>
				
								<?php }?>		
				
				
				
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
                                    <img src="<?php echo assets_url;?>images/users/avatar-2.jpg" alt="">
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
                                    <img src="<?php echo assets_url;?>images/users/avatar-3.jpg" alt="">
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

        <!-- KNOB JS -->
        <!--[if IE]>
        <script type="text/javascript" src="<?php echo assets_url;?>plugins/jquery-knob/excanvas.js"></script>
        <![endif]-->
        <script src="<?php echo assets_url;?>plugins/jquery-knob/jquery.knob.js"></script>

        <!--Morris Chart-->
		<script src="<?php echo assets_url;?>plugins/morris/morris.min.js"></script>
		<script src="<?php echo assets_url;?>plugins/raphael/raphael-min.js"></script>

        <!-- Dashboard init -->
        <script src="<?php echo assets_url;?>pages/jquery.dashboard.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

    </body>
</html>