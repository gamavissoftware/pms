<?php $user_id =$this->session->userdata['logged_in']['user_id'];?>
<!DOCTYPE html>
<html>
    <head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="<?php //echo copyright; ?>">
<link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
<title><?php echo sitetitle; ?>Quotation Report</title>
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
<h1>Quotation Dashboard</h1><hr>
</div>

    <div >
    <div class="row">

    <?php 

	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid',8)->where('submoduleid',70)->where('submodule_access','1')->get();

	if($qry->num_rows()>0){

	?>	
            <div class="col-sm-4 col-md-4 col-lg-2">
               
                <a href="<?php echo page_url;?>Customer/quotation"><div class="report-box">
                    <div class="text-center">
                <img src="<?php echo dashboard_icon;?>top-bar.png">
</div>
           <p> CREATE QUOTATION</p>
                </div></a>
            </div>
<?php 
}
?>

           <?php 

    $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid',8)->where('submoduleid',71)->where('submodule_access','1')->get();

    if($qry->num_rows()>0) {   ?>  
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Customer/quotation_dashboard">
                    <div class="report-box">
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>panel_cutting.png">
                        </div>
                        <p> ALL QUOTATIONS</p>
                    </div>
                </a>
            </div>
<?php 
}
?>




           <?php 

    $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid',8)->where('submoduleid',73)->where('submodule_access','1')->get();

    if($qry->num_rows()>0) {   ?>  
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Customer/discount_approval">
                    <div class="report-box">
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>payment_rec.png">
                        </div>
                        <p> QUOTATIONS FOR APPROVAL</p>
                    </div>
                </a>
            </div>
<?php 
}
?>


   <?php 

    $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid',8)->where('submoduleid',95)->where('submodule_access','1')->get();

    if($qry->num_rows()>0) {   ?>  
            <div class="col-sm-4 col-md-4 col-lg-2" style="display: none;">
                <a href="<?php echo page_url;?>Billing/vehicle_sale_register/<?php echo base64_encode('ALL');?>/<?php echo date('Y-m-01');?>/<?php echo date('Y-m-t');?>">
                    <div class="report-box">
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>payment_rec.png">
                        </div>
                        <p> Vehicle Sale Register</p>
                    </div>
                </a>
            </div>
<?php 
}
?>

          
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