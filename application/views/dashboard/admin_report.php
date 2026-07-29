<?php $user_id =$this->session->userdata['logged_in']['user_id'];?>
<?php 



$VI=& get_instance();
$VI->load->model('Fms_mismodel');

$currentweekdates=$VI->Fms_mismodel->currentweekdates();
//$currentweekdates=$VI->Fms_mismodel->getcurrentweekalldates();

$ststst= base64_encode($currentweekdates[0]);
$endddd=base64_encode($currentweekdates[1]);
?>
<!DOCTYPE html>


<html>


    <head>


        <meta charset="utf-8">


        <meta name="viewport" content="width=device-width, initial-scale=1.0">


        <meta name="description" content="">


        <meta name="author" content="<?php //echo copyright; ?>">





        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">





        <title><?php //echo sitetitle; ?>Admin Report</title>





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
<img src="<?php echo dashboard_icon;?>admin_icon.jpg" style="    width: 100%;">
</div>
<div class="col-sm-8">
<h6>Admin Dashboard</h6>
<p>In this panel, the admin has access to all the functionalities and permissions provided to other users. Thus, the admin can easily view items dashboard, overall MIS, FMS MIS, delegation reports, and booking & billing information. </p>
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
<h1>Admin Dashboard</h1><hr>
</div>
<!-- 
<div class="text-center">
  <button class="tablink  " onclick="openPage('form', this, 'orange')">Form</button>
    <button class="tablink " onclick="openPage('report', this, 'orange')" id="defaultOpen">Reporting</button>

    <button class="tablink " onclick="openPage('person', this, 'orange' )">Responsible Person</button>
</div> -->

    <!-- <div id="form" class="tabcontent">
        <h3>Form</h3>
        
    </div> -->

    <div >
    <div class="row">

    <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','148')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
               
                <a href="<?php echo page_url;?>Store/imported_items"><div class="report-box">
                    <div class="text-center">
                <img src="<?php echo dashboard_icon;?>import.png">
</div>
           <p>Imported Items Dashboard</p>
                </div></a>
            </div>

            <?php }?>	
									
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','175')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
               
               <a href="<?php echo page_url;?>Store/issued_item_dashboard"><div class="report-box">
                   <div class="text-center">
               <img src="<?php echo dashboard_icon;?>item.png">
</div>
          <p>Issued Items Dashboard</p>
               </div></a>
           </div>
           <?php }?>	

           <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','176')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
           <div class="col-sm-4 col-md-4 col-lg-2">
               
               <a href="<?php echo page_url;?>Store/received_item_dashboard"><div class="report-box">
                   <div class="text-center">
               <img src="<?php echo dashboard_icon;?>po_approval.png">
</div>
          <p>Recevied Items Dashboard</p>
               </div></a>
           </div>
           <?php }?>	

           <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','158')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
           <div class="col-sm-4 col-md-4 col-lg-2">
               
               <a href="<?php echo page_url;?>Store/imported_stock_request"><div class="report-box">
                   <div class="text-center">
               <img src="<?php echo dashboard_icon;?>import.png">
</div>
          <p>Imported Item Request</p>
               </div></a>
           </div>
           <?php }?>

           <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','54')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/misscore/<?php echo $ststst;?>/<?php echo $endddd;?>"><div class="report-box">
            <div class="text-center">
                <img src="<?php echo dashboard_icon;?>payment_rec.png">
</div>
           <p>Overall MIS</p>
                </div></a>
            </div>
            <?php }?>

            <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','55')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>FMS/fmsmis/<?php echo $ststst;?>/<?php echo $endddd;?>
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>management.png">
</div>
           <p>Fms MIS</p>
                </div></a>
            </div>
            <?php }?>

            <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','56')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>FMS/nonfmsmis/<?php echo $ststst;?>/<?php echo $endddd;?>
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>todays_issue.png">
</div>
           <p>Non Fms MIS</p>
                </div></a>
            </div>
            <?php }?>

            <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','58')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Delegation/audit_dashbaord
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>user_role.png">
</div>
           <p>Auditor Delegation Report</p>
                </div></a>
            </div>
            <?php }?>
            <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','243')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Delegation/your_delegated_calls_dashboard"><div class="report-box">
            <div class="text-center">
                <img src="<?php echo dashboard_icon;?>call.png">
</div>
           <p>Your Delegated Calls Dashboard</p>
                </div></a>
            </div>
            <?php }?>

            <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','159')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Movement_tracking/billing_to_booking"><div class="report-box">
                <div class="text-center">
                <img src="<?php echo dashboard_icon;?>holiday.png">
</div>
           <p>Booking to Billing</p>
                </div></a>
            </div>
            <?php }?>
			
			<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','309')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
			   <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Reporting/reporting_index"><div class="report-box">
                <div class="text-center">
                <img src="<?php echo dashboard_icon;?>report.png">
</div>
           <p>Reporting Index</p>
                </div></a>
            </div>
            
			<?php }?>
        </div>

       
        
    </div>

    <!-- <div id="person" class="tabcontent">
        <h3>Responsible Person</h3>
       
    </div> -->



    <!-- <script>
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


		


 


</body>


</html>