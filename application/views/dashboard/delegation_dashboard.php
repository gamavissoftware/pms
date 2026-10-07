<?php $user_id =$this->session->userdata['logged_in']['user_id'];?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php //echo copyright; ?>">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title><?php //echo sitetitle; ?> Delegation Management</title>
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

.delegation-card-order {
  display: flex;
  flex-wrap: wrap;
}

.delegation-card-form { order: 1; }
.delegation-card-dashboard { order: 2; }
.delegation-card-assigned { order: 3; }
.delegation-card-task-history { order: 4; }
.delegation-card-history { order: 5; }
.delegation-card-master { order: 6; }


</style></head>
 <body>
 <!-- Navigation Bar-->
 <header id="topnav">
 <?php $this->load->view('common/nav-menu');?>
 </header><!-- End Navigation Bar-->
<div class="wrapper">
    <div class="container-fluid" >
        <div class="dashboard-header">
            <h1>Delegation Module Management</h1><hr>
        </div>
        <div class="row delegation-card-order">
		<?php 
		$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','13')->where('submodule_access','1')->get();
		if($qry->num_rows()>0){
		?>	
            <div class="col-sm-4 col-md-4 col-lg-2 delegation-card delegation-card-master">
                <a href="<?php echo page_url;?>Delegation/delegation_master">
                    <div class="report-box badge1" >
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>delegation_master.png">
                        </div>
                            <p>Delegation Master</p>
                    </div>
                </a>
            </div>
            <?php }?>
			<?php 
		$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','14')->where('submodule_access','1')->get();
		 if($qry->num_rows()>0){
			?>	
            <div class="col-sm-4 col-md-4 col-lg-2 delegation-card delegation-card-form">
                <a href="<?php echo page_url;?>Delegation/new_delegation_task">
                    <div class="report-box badge1" >
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>delegation_form.png">
                        </div>
                            <p>Delegation Form</p>
                    </div>
                </a>
            </div>
            <?php }?>
			
			<?php 
			$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','15')->where('submodule_access','1')->get();
			if($qry->num_rows()>0){
			?>	
            <div class="col-sm-4 col-md-4 col-lg-2 delegation-card delegation-card-dashboard">
                <a href="<?php echo page_url;?>Delegation/delegation_dashboard">
                    <div class="report-box badge1" >
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>dashboard.png">
                        </div>
                            <p>Delegation Dashboard</p>
                    </div>
                </a>
            </div>
            <?php }?>
					     
									
			<?php
			$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','16')->where('submodule_access','1')->get();
			 if($qry->num_rows()>0){
			?>
            <div class="col-sm-4 col-md-4 col-lg-2 delegation-card delegation-card-task-history">
                <a href="<?php echo page_url;?>Delegation/delegated_task_history">
                    <div class="report-box badge1" >
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>visit_history.png">
                        </div>
                            <p>Delegated Task History</p>
                    </div>
                </a>
            </div>
            <?php }?>
			
			<?php
			$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','17')->where('submodule_access','1')->get();
			if($qry->num_rows()>0){
			?>
            <div class="col-sm-4 col-md-4 col-lg-2 delegation-card delegation-card-assigned">
                <a href="<?php echo page_url;?>Delegation/delegated_task">
                    <div class="report-box badge1" >
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>user_role.png">
                        </div>
                            <p>Task Delegated to You</p>

                            <?php 
    $q = $this->db->select('id')->from('delegation_task')->where('delegate_to',$user_id)->where('task_status',0)->get();
    if($q->num_rows()>0){
?>
                            	<div style="background: black; color: white;width: 16px; height: 16px; font-size: 9px; text-align: center; line-height: 16px; border-radius: 50%;
    position: absolute; right: -8px; top: -8px;"><?php echo count($q->result());?></div>
<?php }?>

                    </div>
                </a>
            </div>
            <?php }?>
			<?php
             $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','25')->where('submodule_access','1')->get();
            if($qry->num_rows()>0){
            ?>
            <div class="col-sm-4 col-md-4 col-lg-2 delegation-card delegation-card-history">
                <a href="<?php echo page_url;?>Delegation/delegation_history">
                    <div class="report-box badge1" >
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>user_role.png">
                        </div>
                            <p>Delegation History</p>
                    </div>
                </a>
            </div>
        <?php }?>
			<?php
			// $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','40')->where('submodule_access','1')->get();
			// if($qry->num_rows()>0){
			?>
        <!--     <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php //echo page_url;?>Delegation/audit_dashbaord">
                    <div class="report-box badge1" >
                        <div class="text-center">
                            <img src="<?php //echo dashboard_icon;?>dashboard1.png">
                        </div>
                            <p>Auditor Delegation Dashboard</p>
                    </div>
                </a>
            </div> -->
            <?php //}?>
			
			
			
        </div>
    </div>
</div> <!-- Footer -->


               <?php $this->load->view('common/footer');?>


                <!-- End Footer --></div> <!-- end container -->
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
        <script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>


        <!-- Datatable init js -->


        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>


<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>


        <!-- App js -->


        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>


        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

</body>


</html>
