<?php $user_id =$this->session->userdata['logged_in']['user_id'];?>
<!DOCTYPE html>
<html>
    <head>


        <meta charset="utf-8">


        <meta name="viewport" content="width=device-width, initial-scale=1.0">


        <meta name="description" content="">


        <meta name="author" content="<?php //echo copyright; ?>">





        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">





        <title><?php //echo sitetitle; ?> Common Forms Dashboard</title>





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
<div class="container-fluid">
<div class="desc-box">
<div class="row">
<div class="col-sm-2">
<img src="<?php echo dashboard_icon;?>common_form.jpg" style="    width: 100%;">
</div>
<div class="col-sm-8">
<h6>Common Forms Dashboard</h6>
<p>If you want to access all the forms in this single dashboard, then this panel is created for you. From staff movement form to material movement in-out dashboard form, you will get all the list of forms here. This makes it easy for you to locate all forms in one place.</p>
</div>
<div class="col-sm-2">
<div class="text-center"><a href="#">
<!-- <i class="fa fa-video-camera" aria-hidden="true"></i>  -->
<img src="<?php echo dashboard_icon;?>header_icon.png" style="width: 40%; margin-top: 50px;">
</a>
</div>
</div>
</div>
</div>
</div>
</div>

<div class="wrapper ">
    <div class="container-fluid" >
<div class="dashboard-header">
<h1>Common Forms Dashboard</h1><hr>
</div>


  
        <div class="row">
		<?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','151')->where('submodule_access','1')->get();
									//if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Movement_tracking/staff_movement
"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>staff_move.png">
</div>
           <p>Staff Movement Form</p>
                </div></a>
            </div>
									<?php 
                                //}
                                ?>
									
									<?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','205')->where('submodule_access','1')->get();
								//	if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Form/view/20
"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>user_role.png">
</div>
           <p>Engineering Change Note</p>
                </div></a>
            </div>
									<?php 
                                //}
                                ?>

<?php 
								//	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','150')->where('submodule_access','1')->get();
								//	if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Movement_tracking/movement_out
"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>weight.png">
</div>
           <p>Movement in Out Form</p>
                </div></a>
            </div>
									<?php 
                                //}
                                ?>
							<?php 
								//	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','113')->where('submodule_access','1')->get();
								//	if($qry->num_rows()>0){
									?>		
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Form/view/23
"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>tat.png">
</div>
           <p>Night Report Form</p>
                </div></a>
            </div>
									<?php 
                                //}
                                ?>
<?php 
								//	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','124')->where('submodule_access','1')->get();
								//	if($qry->num_rows()>0){
									?>	

            <!-- <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Form/view/32
"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>order_dispatch.png">
</div>
           <p>Courier In Form</p>
                </div></a>
            </div> -->
									<?php 
                                //}
                                ?>
									<?php 
								//	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','123')->where('submodule_access','1')->get();
								//	if($qry->num_rows()>0){
									?>	
            <!-- <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Form/view/33
"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>ready_dispatch.png">
</div>
           <p>Courier Out Form</p>
                </div></a>
            </div> -->
									<?php 
                                //}
                                ?>
									
									<?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','125')->where('submodule_access','1')->get();
									//if($qry->num_rows()>0){
									?>	

            <!-- <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Form/view/35
"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>cons.png">
</div>
           <p>Diesel Consumption Form</p>
                </div></a>
            </div> -->
									<?php 
                                //}
                                ?>
									
					<?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','128')->where('submodule_access','1')->get();
									//if($qry->num_rows()>0){
									?>					

            <!-- <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Form/view/38
"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>pannel.png">
</div>
           <p>Panel Plate Cutting Form</p>
                </div></a>
            </div> -->
									<?php 
                                //}
                                ?>
									
				<?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','131')->where('submodule_access','1')->get();
									//if($qry->num_rows()>0){
									?>					
            <!-- <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Form/view/41
"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>open.png">
</div>
           <p>Office Open Closed Form</p>
                </div></a>
            </div> -->
									<?php 
                                //}
                                ?>
									<?php 
								//	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','203')->where('submodule_access','1')->get();
								//	if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Daily_reporting
"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>sales_pending.png">
</div>
           <p>Staff Daily Reporting Form</p>
                </div></a>
            </div>
									<?php 
                                //}
                                ?>
			
			<?php 
                   // $submoduleid = array('303','304','302');
                   // $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where_in('submoduleid',$submoduleid)->where('submodule_access','1')->get();
                  //  if($qry->num_rows()>0){
                    ?>
			  <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Daily_reporting/reporting_dashboard
"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>sales_pending.png">
</div>
           <p>Staff Daily Reporting Dashboard</p>
                </div></a>
            </div>
									<?php 
                                //}
                                ?>

            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Hr/leave_application
"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>leave-app.png">
</div>
           <p>Leave Application</p>
                </div></a>
            </div>
<?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','171')->where('submodule_access','1')->get();
									//if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Hr/hod_leave_dashboard
"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>dashboard.png">
</div>
           <p>Leave Application HOD Dashboard</p>
                </div></a>
            </div>
									<?php 
                                //}
                                ?>
			 <?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','156')->where('submodule_access','1')->get();
									//if($qry->num_rows()>0){
									?>
			 <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Movement_tracking/movement_out_dashboard"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>leave.png">
</div>
           <p>Material Movement In Out Dashboard</p>
                </div></a>
            </div>
									<?php 
                                //}
                                ?>
									
									<?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','157')->where('submodule_access','1')->get();
								//	if($qry->num_rows()>0){
									?>
			 <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Movement_tracking/staff_movement_dashboard
"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>staff-move.png">
</div>
           <p>Staff Movement Dashboard</p>
                </div></a>
            </div>
									<?php 
                                //}
                                ?>
									
			<?php 
									
									
									$q = $this->db->select('id,form_title,images, status')->from('dynamic_forms')->where('status','1')->where('form_running_status','0')->get();
									foreach($q->result() as $row){
									?>
									
			 <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Form/view/<?php echo $row->id;?>"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>purchase_request.png">
</div>
           <p><?php echo ucfirst($row->form_title);?></p>
                </div></a>
            </div>
			
			<?php 
									
									}
									
									?>
           
            
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