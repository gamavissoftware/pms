<!DOCTYPE html>
<?php $team_task_settings_enabled = $this->db->field_exists('show_all_team_tasks', 'prestogroup_teams') && $this->db->field_exists('allow_task_assignment', 'prestogroup_teams'); ?>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title><?php echo sitetitle; ?> Team Management</title>
        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/orgchart/2.1.3/css/jquery.orgchart.min.css">
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/orgchart/2.1.3/js/jquery.orgchart.min.js"></script>


        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
<style>
    .orgchart .node .content
    {
      white-space: break-word !important;
    }
        #chart-container {
  font-family: Arial;
  height: 5000px;
  border: 2px dashed #aaa;
  border-radius: 5px;
  overflow: auto;
  text-align: center;
}

.orgchart {
  background: #fff; 
}
.orgchart td.left, .orgchart td.right, .orgchart td.top {
  border-color: #aaa;
}
.orgchart td>.down {
  background-color: #aaa;
}
.orgchart .middle-level .title {
  background-color: #006699;
}
.orgchart .middle-level .content {
  border-color: #006699;
}
.orgchart .product-dept .title {
  background-color: #009933;
}
.orgchart .product-dept .content {
  border-color: #009933;
}
.orgchart .rd-dept .title {
  background-color: #993366;
}
.orgchart .rd-dept .content {
  border-color: #993366;
}
.orgchart .pipeline1 .title {
  background-color: #996633;
}
.orgchart .pipeline1 .content {
  border-color: #996633;
}
.orgchart .frontend1 .title {
  background-color: #cc0066;
}
.orgchart .frontend1 .content {
  border-color: #cc0066;
}


    </style>
<style>

.team{

    padding:75px 0;

}

h6.description{

	font-weight: bold;

	letter-spacing: 2px;

	color: #999;

	border-bottom: 1px solid rgba(0, 0, 0,0.1);

	padding-bottom: 5px;

}

.profile-box {
    margin-top: 25px;
    margin: 2px 2px 2px 2px;
    min-height: 300px;
    background-color: white;
    box-shadow: 1px 1px 10px lightgrey;
    border: 1px solid lightgray;
}

.pro-box{
  height:195px;
  overflow:hidden;
  background-color:white;
}

.profile-box img{
  width:100%;
}

.profile-box h1{

	font-weight: normal;

	font-size: 20px;
text-align:center;
	margin:10px 0 0 0;

}

.profile-box p{
  text-align:center;
}

.profile-box i{
  font-size:17px;
  color:red;
  margin-right:10px;
}


.profile-box h2{

	font-size: 14px;

	font-weight: lighter;

	margin-top: 5px;

}

.profile-box .img-box{

	opacity: 1;

	display: block;

	position: relative;

}

.profile-box .img-box:after{

	content:"";

	opacity: 0;

	background-color: rgba(0, 0, 0, 0.75);

	position: absolute;

	right: 0;

	left: 0;

	top: 0;

	bottom: 0;

}

.img-box ul{

	position: absolute;

	z-index: 2;

	bottom: 50px;

	text-align: center;

	width: 100%;

	padding-left: 0px;

	height: 0px;

	margin:0px;

	opacity: 0;

}

.profile-box .img-box:after, .img-box ul, .img-box ul li{

	-webkit-transition: all 0.5s ease-in-out 0s;

    -moz-transition: all 0.5s ease-in-out 0s;

    transition: all 0.5s ease-in-out 0s;

	border:2px solid #000;

}

.img-box ul i{

	font-size: 20px;

	letter-spacing: 10px;

}

.img-box ul li{

	width: 30px;

    height: 30px;

    text-align: center;

    border: 1px solid #88C425;

    margin: 2px;

    padding: 5px;

	display: inline-block;

}

.img-box a{

	color:#fff;

}

.img-box:hover:after{

	opacity: 1;

}

.img-box:hover ul{

	opacity: 1;

}

.img-box ul a{

	-webkit-transition: all 0.3s ease-in-out 0s;

	-moz-transition: all 0.3s ease-in-out 0s;

	transition: all 0.3s ease-in-out 0s;

}

.img-box a:hover li{

	border-color: #fff;

	color: #88C425;

}

a{

    color:#88C425;

}

a:hover{

    text-decoration:none;

    color:#519548;

}

i.red{

    color:#BC0213;

}

</style>

    </head>
<body>
        <header id="topnav">
          <?php $this->load->view('common/nav-menu');?>
        </header>
        <!-- End Navigation Bar-->
        <div class="wrapper">
            <div class="container-fluid" style="background-color:#fff;">
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
                            <div class="btn-group pull-right">
                                 <a href="<?php echo page_url;?>Master/User_management/add_member_in_team/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>/<?php echo $this->uri->segment(6);?>"><button class="btn btn-success waves-effect waves-light">Add Member in Team</button></a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
          <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
		  <?php
			$team_settings = $this->db->select('*')->from('prestogroup_teams')->where('team_id',$this->uri->segment(4))->get()->row();
			$team_member_count = $this->db->select('team_members_id')->from('presto_team_members')->where('team_id',$this->uri->segment(4))->count_all_results();
			$show_all_team_tasks_label = ($team_settings && !empty($team_settings->show_all_team_tasks) && (int) $team_settings->show_all_team_tasks === 1) ? 'Enabled' : 'Disabled';
			$allow_task_assignment_label = ($team_settings && !empty($team_settings->allow_task_assignment) && (int) $team_settings->allow_task_assignment === 1) ? 'Enabled' : 'Disabled';
		  ?>
                  <div class="row">
      <div class="col-md-12">
        <div class="col-lg-12">
          <h4 class="text-center">Team Structure of <?php 
            echo $team_settings ? $team_settings->team_name : '';
      ?></h4><hr>
		  <div class="row" style="margin-bottom:20px;">
			<div class="col-md-4">
				<div class="alert alert-info" style="margin-bottom:10px;">
					<strong>Total Team Users:</strong> <?php echo (int) $team_member_count + ($team_settings ? 1 : 0); ?>
				</div>
			</div>
			<?php if($team_task_settings_enabled){ ?>
			<div class="col-md-4">
				<div class="alert alert-warning" style="margin-bottom:10px;">
					<strong>Show All Team Tasks:</strong> <?php echo $show_all_team_tasks_label; ?>
				</div>
			</div>
			<div class="col-md-4">
				<div class="alert alert-success" style="margin-bottom:10px;">
					<strong>Assign Task Permission:</strong> <?php echo $allow_task_assignment_label; ?>
				</div>
			</div>
			<?php } ?>
		  </div>
          <div class="row pt-md">
		  <?php 

			$query = $this->db->select('a.team_id,a.team_leader, a.team_name, b.user_id, b.first_name, b.last_name, b.profile_image,b.user_role_id, c.user_role_id,c.user_role')->from('prestogroup_teams a')->join('system_users b','a.team_leader=b.user_id','left')->join('user_role c','b.user_role_id=c.user_role_id','left')->where('team_id',$this->uri->segment(4))->get();
            //echo "<pre>"; print_r($query->result()); exit;
            if($query->num_rows()>0){
			foreach($query->result() as $team_leader)

		  ?>

            <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12 ">
              <div class="profile-box" >
			  <div class="pro-box">
                <center>
                    <?php if($team_leader->profile_image){?>
                        <img src="<?php echo user_profile;?><?php echo $team_leader->profile_image;?>" >

                  <?php  }else{?>
                    <img src="<?php echo user_profile;?>userplaceholder.jpeg" >

                    <?php }?>
                    </center>
                </div>
              <hr>
			  <div style="text-align:center">
                <h1 style="font-size:15px;"><?php echo $team_leader->first_name." ".$team_leader->last_name;?></h1>
                <p><strong>Team Leader</strong><br><?php echo $team_leader->user_role;?></p>
            </div>
        </div>
        </div>
    <?php }?>

    
        <?php 
	        $query = $this->db->select('a.team_id,a.team_members_id, a.employee_id, b.user_id, b.first_name, b.last_name, b.profile_image,b.user_role_id, c.user_role_id,c.user_role')
	        	->from('presto_team_members a')
	        	->join('prestogroup_teams t','a.team_id=t.team_id','left')
	        	->join('system_users b','a.employee_id=b.user_id','left')
	        	->join('user_role c','b.user_role_id=c.user_role_id','left')
	        	->where('a.team_id',$this->uri->segment(4))
	        	->where('a.employee_id != t.team_leader', null, false)
	        	->get();
            foreach($query->result() as $team){
                ?>
            <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12 ">
              <div class="profile-box" >
              <div class="pro-box">
                 <center><?php if($team->profile_image){?>
                        <img src="<?php echo user_profile;?><?php echo $team->profile_image;?>" >

                  <?php  }else{?>
                    <img src="<?php echo user_profile;?>userplaceholder.jpeg" >

                    <?php }?></center>
               
            </div>
              <hr>
              <div style="margin:2px 2px 2px 2px;">
                <h1 style="font-size:15px;"><?php echo $team->first_name." ".$team->last_name;?></h1>
                <p><strong>Team User</strong><br><?php echo $team->user_role;?>  <a href="<?php echo page_url;?>Master/User_management/remove_member/<?php echo $team->team_members_id;?>/<?php echo $this->uri->segment(4);?>" onclick="return confirm('Are you sure you want to delete this member?');"><i class="fa fa-trash pull-right" title="Remove Member to the Team" style=""></i></a></p>
            </div>
            </div>

            </div>

			<?php }?>



        </div>
      </div>
    </div>
</div>

<!-- ORG CHART -->


  <div class="row" style="margin-top:10px;">
    <div class="col-md-12">
    <!-- <div id="chart-container"></div> -->
    </div>
    </div>




<?php $this->load->view('common/footer');?>
</div> <!-- end container -->
 </div>

    


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
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
         </body>

</html>
