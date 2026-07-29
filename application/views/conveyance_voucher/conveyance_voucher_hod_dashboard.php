<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Prestogroup</title>

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
		<style>
table.manglesh thead th {
				background: #003366;
				color:#fff;
				font-weight:bold;
			}
</style>
    </head>
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
                        <img src="<?php echo dashboard_icon; ?>Conveyance Hod dashboard.jpg" style="    width: 100%;">
                    </div>
                    <div class="col-sm-8">
                        <h6>Conveyance Hod dashboard
</h6>
                        <p>The HODs will verify the details and give approval to the engineers who fill the form. </p>
                    </div>
                    <div class="col-sm-2">
                        <!-- <div class="text-center"><a href="#">
                               
                                <img src="<?php echo dashboard_icon; ?>header_icon.png" style="width: 40%; margin-top: 50px;">
                            </a>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

        <div class="wrapper">
            <div class="container-fluid">

                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
						
                           
                            <h4 class="page-title text-center">Conveyance HOD Dashboard</h4>
                        </div>
                    </div>
                    <div class="col-md-3"></div>
						<div class="col-lg-3 col-md-3 col-xs-12 col-sm-12 ">
                        <a href="<?php echo page_url;?>Sales/local_conveyance_data/<?php echo $this->uri->segment(3);?>"><div class="card-box">
                            <div class="text-center">
                                
							<div class="text-center">
                                 <div class="row">
								<div class="col-md-12"><h4>Local Coveyance Data<br>Total-<span style="color:red; font-weight:bold;">(<?php
								if($this->uri->segment(3)){
								    $user_id =$this->uri->segment(3);	
								}else{
								    $user_id =$this->session->userdata['logged_in']['user_id'];
								}
									
		$q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
		if($q->num_rows()>0)
		{
		foreach($q->result() as $teamdetail);
		 $teammembers = array();
		$query = $this->db->select('team_id, employee_id')->from('presto_team_members')->where('team_id',$teamdetail->team_id)->get();
        $res = $query->result();
         foreach($res as $teaminfo){
            $teammembers[] = $teaminfo->employee_id;
            
        }
	
		//$team = implode(',',$teammembers);
		$team = "'" . implode ( "', '", $teammembers ) . "'";
		}else
		{
		    $team='NA';
		}
		if($team<>'NA'){
								$query = $this->db->select('id')->from('conveyance_voucher')->where_in('added_by',$team,false)->where('status','0')->where('hod_status',0)->order_by('hod_status','asc')->get();
								$res = $query->result();
								echo count($res);
		}else{
		    echo "0";
		}
								?>)</span></h4>
								</div>
							</div>
                            </div>
                            </div>
                        </div></a>
							</div>
							
							<div class="col-lg-3 col-md-3 col-xs-12 col-sm-12 ">
                        <a href="<?php echo page_url;?>Sales/engineer_visit_hod_dashboard/<?php echo $this->uri->segment(3);?>"><div class="card-box">
                            <div class="text-center">
                                
							<div class="text-center">
                                 <div class="row">
								<div class="col-md-12"><h4>Visit form Data<br>Total-<span style="color:red; font-weight:bold;">(<?php 
								
								if($this->uri->segment(3)){
								    $user_id =$this->uri->segment(3);	
								}else{
								    $user_id =$this->session->userdata['logged_in']['user_id'];
								}
									
		$q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
		if($q->num_rows()>0)
		{
		foreach($q->result() as $teamdetail);
		 $teammembers = array();
		$query = $this->db->select('team_id, employee_id')->from('presto_team_members')->where('team_id',$teamdetail->team_id)->get();
        $res = $query->result();
         foreach($res as $teaminfo){
            $teammembers[] = $teaminfo->employee_id;
            
        }
	
		//$team = implode(',',$teammembers);
		$team = "'" . implode ( "', '", $teammembers ) . "'";
		}else
		{
		    $team='NA';
		}
		if($team<>'NA'){
		    $query = $this->db->select('id')->from('engineer_visit')->where_in('engineer',$team,false)->where('hod_payment_status','0')->get();
								$res = $query->result();
								echo count($res);
		}else{
		    echo "0";
		}
								?>)</span></h4>
								</div>
							</div>
                            </div>
                            </div>
                        </div></a>
							</div>
							<div class="col-md-3"></div>
                </div>
				
				
                <!-- end page title end breadcrumb -->
	



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
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
		<script>
		// Time Picker
            jQuery('#timepicker').timepicker({
                defaultTIme : false
            });
			jQuery('#timepicker4').timepicker({
                defaultTIme : false
            });
            jQuery('#timepicker2').timepicker({
                showMeridian : false
            });
            jQuery('#timepicker3').timepicker({
                minuteStep : 15
            });
		</script>
 

<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
</body>
</html>