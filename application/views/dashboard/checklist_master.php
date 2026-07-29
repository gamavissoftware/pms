<?php $user_id =$this->session->userdata['logged_in']['user_id'];?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php //echo copyright; ?>">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title><?php //echo sitetitle; ?> Checklist Management</title>
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
    <div class="container-fluid" >
        <div class="dashboard-header">
            <h1>Checklist Module Management</h1><hr>
        </div>
        <div class="row">
		<?php 
		$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','14')->where('submodule_access','1')->get();
		if($qry->num_rows()>0){
		?>	
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Checklist/Turnaroundtime">
                    <div class="report-box badge1" >
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>visit_history.png">
                        </div>
                            <p>Checklist TAT</p>
                    </div>
                </a>
            </div>
            <?php }?>
			<?php 
			$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','15')->where('submodule_access','1')->get();
			if($qry->num_rows()>0){
			?>	
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Checklist/create_checklist">
                    <div class="report-box badge1" >
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>sales_testing.png">
                        </div>
                            <p>Checklists</p>
                    </div>
                </a>
            </div>
            <?php }?>
					     
									
			<?php
			$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','16')->where('submodule_access','1')->get();
			if($qry->num_rows()>0){
			?>
            <!--<div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Checklist/mispercentage">
                    <div class="report-box badge1" >
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>user_role.png">
                        </div>
                            <p>Checklist MIS Factor</p>
                    </div>
                </a>
            </div>-->
            <?php }?>
			
			<?php
			$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','39')->where('submodule_access','1')->get();
			if($qry->num_rows()>0){
			?>
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Checklist/view_your_checklist">
                    <div class="report-box badge1" >
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>your_checklist.png">
                        </div>
                            <p>View Your Checklist</p>
                    </div>
                </a>
            </div>
            <?php }?>
			
			<?php
			$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','40')->where('submodule_access','1')->get();
			if($qry->num_rows()>0){
			?>
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Checklist/checklist_yesterday_report">
                    <div class="report-box badge1" >
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>user_role.png">
                        </div>
                            <p>View Checklist Report</p>
                    </div>
                </a>
            </div>
            <?php }?>
			
			<?php
			$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','41')->where('submodule_access','1')->get();
			if($qry->num_rows()>0){
			?>
            <!--<div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Checklist/checklist_dashboard">
                    <div class="report-box badge1" >
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>dashboard.png">
                        </div>
                            <p>Auditor Checklist Dashboard</p>
                    </div>
                </a>
            </div>-->
            <?php }?>
			
			<?php
			$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','42')->where('submodule_access','1')->get();
			if($qry->num_rows()>0){
			?>
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Checklist/auditor_check_dashboard">
                    <div class="report-box badge1" >
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>purchase_request.png">
                        </div>
                            <p>Auditor Checklist Report</p>
                    </div>
                </a>
            </div>
            <?php }?>
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


		
    <script>
        
        $( document ).ready(function(){
            $.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/getsalesreportingcount/1',
                type: 'get',
                success: function(data){
                    $('#sales_visit').attr('data-badge', data);
                }
            });
            
             $.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/getsalesreportingcount/2',
                type: 'get',
                success: function(data){
                    $('#daily_update').attr('data-badge', data);
                }
            });
            
             $.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/getsalesreportingcount/3',
                type: 'get',
                success: function(data){
                    $('#payment_collection').attr('data-badge', data);
                }
            });
        });
 
    </script>

</body>


</html>