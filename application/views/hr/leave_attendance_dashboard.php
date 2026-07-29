<?php $user_id =$this->session->userdata['logged_in']['user_id'];?>
<!DOCTYPE html>
<html>
    <head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="<?php //echo copyright; ?>">
<link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
<title><?php //echo sitetitle; ?>Attendance & Leave Report</title>
<script src="<?php echo assets_url;?>js/angular.min.js"></script>
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
                <header id="topnav">
          <?php $this->load->view('common/nav-menu');?>


        </header>


        <!-- End Navigation Bar-->

<div class="wrapper ">
<div class="container-fluid ">

</div>
</div>


<div class="wrapper ">
    <div class="container-fluid" >
<div class="dashboard-header">
<h1>Attendance and Leave Application Dashboard</h1><hr>
</div>

    <div >
    <div class="row">

                        <div class="col-sm-4 col-md-4 col-lg-2">
                        <a href="<?php echo page_url;?>Master/User_management/mark_your_attendance"><div class="report-box">
                        <div class="text-center">
                        <img src="<?php echo dashboard_icon;?>top-bar.png">
                        </div>
                        <p>Mark your Attendance</p>
                        </div></a>
                        </div>


                        <div class="col-sm-4 col-md-4 col-lg-2">
                        <a href="<?php echo page_url;?>Hr/leave_application"><div class="report-box">
                        <div class="text-center">
                        <img src="<?php echo dashboard_icon;?>top-bar.png">
                        </div>
                        <p>Leave Application Form</p>
                        </div></a>
                        </div>


                        
                        <?php 

                        $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','10')->where('submoduleid',85)->where('submodule_access','1')->get();

                        if($qry->num_rows()>0){

                        ?>
                        <div class="col-sm-4 col-md-4 col-lg-2">
                        <a href="<?php echo page_url;?>Hr/view_your_leave"><div class="report-box">
                        <div class="text-center">
                        <img src="<?php echo dashboard_icon;?>top-bar.png">
                        </div>
                        <p>View Your Leave History</p>
                        </div></a>
                        </div>
                        <?php 
                        }
                        ?>


                        <?php 

                        $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid',89)->where('submodule_access','1')->get();

                        if($qry->num_rows()>0){

                        ?>
                        <div class="col-sm-4 col-md-4 col-lg-2">
                        <a href="<?php echo page_url;?>Hr/overallleave"><div class="report-box">
                        <div class="text-center">
                        <img src="<?php echo dashboard_icon;?>top-bar.png">
                        </div>
                        <p>Overall Leave History</p>
                        </div></a>
                        </div>
                        <?php 
                        }
                        ?>

                        <?php 

                        $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','10')->where('submoduleid', 87)->where('submodule_access','1')->get();

                        if($qry->num_rows()>0){

                        ?>
                        <div class="col-sm-4 col-md-4 col-lg-2">
                        <a href="<?php echo page_url;?>Master/User_management/user_attendance_history"><div class="report-box">
                        <div class="text-center">
                        <img src="<?php echo dashboard_icon;?>top-bar.png">
                        </div>
                        <p>Your Attendance History</p>
                        </div></a>
                        </div>
                        <?php 
                        }
                        ?>

                        <?php 

                        $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','10')->where('submoduleid', 88)->where('submodule_access','1')->get();

                        if($qry->num_rows()>0){

                        ?>
                        <div class="col-sm-4 col-md-4 col-lg-2">
                        <a href="<?php echo page_url;?>Master/User_management/hod_attendance"><div class="report-box">
                        <div class="text-center">
                        <img src="<?php echo dashboard_icon;?>top-bar.png">
                        </div>
                        <p>Overall Attendance History</p>
                        </div></a>
                        </div>
                        <?php 
                        }
                        ?>

                    <?php 

                        $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','10')->where('submoduleid',83)->where('submodule_access','1')->get();

                        if($qry->num_rows()>0){

                        ?>
                        <div class="col-sm-4 col-md-4 col-lg-2">
                        <a href="<?php echo page_url;?>Master/User_management/attendance_dashboard"><div class="report-box">
                        <div class="text-center">
                        <img src="<?php echo dashboard_icon;?>top-bar.png">
                        </div>
                        <p>Attendance Report of the Day</p>
                        </div></a>
                        </div>
                        <?php 
                        }
                        ?>


						
						<?php 

                        $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','10')->where('submoduleid',84)->where('submodule_access','1')->get();

                        if($qry->num_rows()>0){

                        ?>
                        <div class="col-sm-4 col-md-4 col-lg-2">
                        <a href="<?php echo page_url;?>Hr/employee_monthly_attendance"><div class="report-box">
                        <div class="text-center">
                        <img src="<?php echo dashboard_icon;?>top-bar.png">
                        </div>
                        <p>Attendance Report of the Month</p>
                        </div></a>
                        </div>
                        <?php 
                        }
                        ?>

                        <!--//Hr/attendance_report-->
						


						
<!-- 						<?php 

                        $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid',159)->where('submodule_access','1')->get();

                        if($qry->num_rows()>0){

                        ?>
                        <div class="col-sm-4 col-md-4 col-lg-2">
                        <a href="<?php echo page_url;?>Hr/hod_leave_dashboard"><div class="report-box">
                        <div class="text-center">
                        <img src="<?php echo dashboard_icon;?>top-bar.png">
                        </div>
                        <p>HOD Leave Application Dashboard</p>
                        </div></a>
                        </div>
                        <?php 
                        }
                        ?> -->
						
<!-- 						<?php 

                        $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid',82)->where('submodule_access','1')->get();

                        if($qry->num_rows()>0){

                        ?>
                        <div class="col-sm-4 col-md-4 col-lg-2">
                        <a href="<?php echo page_url;?>Hr/onleavetoday"><div class="report-box">
                        <div class="text-center">
                        <img src="<?php echo dashboard_icon;?>top-bar.png">
                        </div>
                        <p>On Leave Today Dashboard</p>
                        </div></a>
                        </div>
                        <?php 
                        }
                        ?> -->
          
</div>

       
        
    </div>

    


    <script>
        function openPage(pageName, elmnt, color) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablink");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].style.backgroundColor = "";
            }
            document.getElementById(pageName).style.display = "block";
            elmnt.style.backgroundColor = color;

            
        }

        document.getElementById("defaultOpen").click();
    </script>



      
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




        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>


<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>


        <!-- App js -->


        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>


        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
</body>


</html>