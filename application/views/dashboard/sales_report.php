<?php $user_id =$this->session->userdata['logged_in']['user_id'];?>
<!DOCTYPE html>


<html>


    <head>


        <meta charset="utf-8">


        <meta name="viewport" content="width=device-width, initial-scale=1.0">


        <meta name="description" content="">


        <meta name="author" content="<?php //echo copyright; ?>">





        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">





        <title><?php //echo sitetitle; ?> Sales Report</title>





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
<img src="<?php echo dashboard_icon;?>sales_report.jpg" style="    width: 100%;">
</div>
<div class="col-sm-8">
<h6>Sales Report</h6>
<p>Sales report, gives an overview of the state of the sales activities within a company. It shows the different trends happening in the sales volume over a certain time, but also analyzes the different steps of the sales funnel and the performance of sales executives.</p>
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


<div class="wrapper">
    <div class="container-fluid" >
<div class="dashboard-header">
<h1>Sales Dashboard</h1><hr>
</div>
<!-- <div class="text-center">
 <button class="tablink  " onclick="openPage('form', this, 'orange')">Form</button> 
    <button class="tablink " onclick="openPage('report', this, 'orange')" id="defaultOpen">Reporting</button>

    <button class="tablink " onclick="openPage('person', this, 'orange' )">Responsible Person</button>
</div> -->

    <!-- <div id="form" class="tabcontent">
    <div class="row">
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Sales/visit_schedule_dashboard
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>sales_visit.png">
</div>
           <p>Sales Tool</p>
                </div></a>
            </div>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Sales/sales_daily_update_dashboard
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>daily_update.png">
</div>
           <p>Staff Movement/OD</p>
                </div></a>
            </div>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Sales/salespaymentcollection
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>payment_collection.png">
</div>
           <p>Daily Reporting Form</p>
                </div></a>
            </div>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Sales/salespaymentcollection
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>payment_collection.png">
</div>
           <p>Leave Application</p>
                </div></a>
            </div>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Sales/salespaymentcollection
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>payment_collection.png">
</div>
           <p>Payment Collection</p>
                </div></a>
            </div>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Sales/salespaymentcollection
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>payment_collection.png">
</div>
           <p>Payment Collection</p>
                </div></a>
            </div>
            
        </div>
        
    </div> -->

  <div >
        <div class="row">
        <?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','165')->where('submodule_access','1')->get();
								//	if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Sales/visit_schedule_dashboard
"><div class="report-box badge1" data-badge="" id="sales_visit">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>sales_visit.png">
</div>
           <p>  Sales Visit Dashboard</p>
                </div></a>
            </div>
            <?php 
        //}
        ?>
            <?php 
								//	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','166')->where('submodule_access','1')->get();
								//	if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Sales/sales_daily_update_dashboard
"><div class="report-box badge1" data-badge="" id="daily_update">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>daily_update.png">
</div>
           <p> Daily Update Dashboard</p>
                </div></a>
            </div>
            <?php 
        //}
        ?>
            <?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','259')->where('submodule_access','1')->get();
									//if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Sales/salespaymentcollection
"><div class="report-box badge1" data-badge="" id="payment_collection">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>payment_collection.png">
</div>
           <p>Payment Collection</p>
                </div></a>
            </div>
            <?php
         //}
         ?>
            <?php
								    //	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','147')->where('submodule_access','1')->get();
									//if($qry->num_rows()>0){
									    ?>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Sampletesting/sampletestrequests
"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>tat.png">
</div>
           <p>Sample Testing Dashboard</p>
                </div></a>
            </div>
            <?php 
        //}
        ?>
            <?php
								    	//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','264')->where('submodule_access','1')->get();
									//if($qry->num_rows()>0){
									    ?>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Sampletest/sampletestrequests
"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>dashboard.png">
</div>
           <p>Walmart Sample Testing Dashboard</p>
                </div></a>
            </div>
            <?php 
        //}
        ?>
            <?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','242')->where('submodule_access','1')->get();
									//if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Sampletesting/sampletestrequests/<?php echo $_SESSION['logged_in']['user_id'];?>
"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>purchase_request.png">
</div>
           <p>Your Sample Testing Request</p>
                </div></a>
            </div>
            <?php
         //}
         ?>
        </div>
        </div>

    <!-- <div id="person" class="tabcontent">
        <h3>Responsible Person</h3>
       
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
    </script> -->

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