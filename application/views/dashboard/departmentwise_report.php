<?php $user_id =$this->session->userdata['logged_in']['user_id'];?>
<!DOCTYPE html>


<html>


    <head>


        <meta charset="utf-8">


        <meta name="viewport" content="width=device-width, initial-scale=1.0">


        <meta name="description" content="">


        <meta name="author" content="<?php //echo copyright; ?>">





        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">





        <title><?php //echo sitetitle; ?> Departmentwise Report</title>





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
<img src="<?php echo dashboard_icon;?>production_icon.jpg" style="    width: 100%;">
</div>
<div class="col-sm-8">
<h6>Production Report</h6>
<p>A production report contains detailed information about a company's shop production data. By manufacturing production reports, you can make business decisions based on actual facts. With manufacturing production reports you can also catch potential problems before they have impacted the customer.</p>
</div>
<div class="col-sm-2">
<div class="text-center"><a href="#">
<!-- <i class="fa fa-video-camera" aria-hidden="true"></i>  -->
<img src="<?php echo dashboard_icon;?>header_icon.png" style="width: 40%; margin-top: 35px;">
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
<h1>Production Dashboard</h1><hr>
</div>

<!-- <div class="text-center">
 <button class="tablink  " onclick="openPage('form', this, 'orange')">Form</button>
    <button class="tablink " onclick="openPage('report', this, 'orange')" id="defaultOpen">Reporting</button>

    <button class="tablink " onclick="openPage('person', this, 'orange' )">Responsible Person</button>
</div> -->
<!-- 
    <div id="form" class="tabcontent">
        <h3>Form</h3>
        
    </div> -->

    <div >
    <div class="row">

<?php 

//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','65')->where('submodule_access','1')->get();

//if($qry->num_rows()>0){

?>
            <div class="col-sm-4 col-md-4 col-lg-2">
               
                <a href="<?php echo page_url;?>FMS/fms_reporting/1"><div class="report-box">
                    <div class="text-center">
                <img src="<?php echo dashboard_icon;?>fms.png">
</div>
           <p>FMS Reporting</p>
                </div></a>
            </div>
            <?php 
        //}
        ?>

<?php
										$reslongre=$this->db->select('id,production_flow')->from('production_flow')->where('longreport','1')->order_by('sortorder','ASC')->get();
										if($reslongre->num_rows()>0)
										{
										foreach($reslongre->result() as $reslongre1)
										{
											$term="FMS ".strtoupper($reslongre1->production_flow)." REPORTING";
										$qry = $this->db->select('a.id')->from('module_capablity a')->join('submodule b','a.submoduleid=b.id')->where('a.role_id',$user_id)->where('a.moduleid','3')->where('b.submodule',$term)->where('a.submodule_access','1')->get();
										//$res = $qry->result();
										if($qry->num_rows()>0){
											
										?>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>FMS/fms_reporting/<?php echo $reslongre1->id;?>"><div class="report-box">
            <div class="text-center">
                <img src="<?php echo dashboard_icon;?>planned_order.png">
</div>
           <p>FMS <?php echo strtolower($reslongre1->production_flow);?> Reporting</p>
                </div></a>
            </div>
<?php
										}
										
										}
										}
										?>

            <?php 
									// $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','45')->where('submodule_access','1')->get();
									// if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>FMS/view_planned_orders"><div class="report-box">
            <div class="text-center">
                <img src="<?php echo dashboard_icon;?>planned_order.png">
</div>
           <p>Planned Orders</p>
                </div></a>
            </div>
<?php 
//}
?>

<?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','46')->where('submodule_access','1')->get();
									//if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>FMS/planned_actual
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>planned_actual.png">
</div>
           <p> Planned to Actual</p>
                </div></a>
            </div>
<?php 
//}
?>


<?php 
							//		$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','47')->where('submodule_access','1')->get();
							//		if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/pendingorders
"><div class="report-box badge1" data-badge="0">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>pending_order.png">
</div>
           <p>Pending Order Report</p>
                </div></a>
            </div>
<?php 
//}
?>

<?php 
//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','246')->where('submodule_access','1')->get();
//if($qry->num_rows()>0){
									?> 
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/salespendingorders
"><div class="report-box badge1" data-badge="0">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>sales_pending.png">
</div>
           <p>Sales Pending Order Report</p>
                </div></a>
            </div>
<?php 
//}
?>

<?php 
								//	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','48')->where('submodule_access','1')->get();
								//	if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/readyforpacking"><div class="report-box badge1" data-badge="0">
            <div class="text-center">
                <img src="<?php echo dashboard_icon;?>machine_packing.png">
</div>
           <p>Machines for Packing</p>
                </div></a>
            </div>
<?php 
//}
?>        


<?php 
								//	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','326')->where('submodule_access','1')->get();
								//	if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Reporting/readyforpackingforservice"><div class="report-box badge1" data-badge="0">
                <div class="text-center">
                <img src="<?php echo dashboard_icon;?>machine_packing.png">
</div>
           <p>Machines for Packing for Service</p>
                </div></a>
            </div>
<?php 
//}
?>


<?php 
								//	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','49')->where('submodule_access','1')->get();
								//	if($qry->num_rows()>0){
									?>	
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/completedorders
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>ready_dispatch.png">
</div>
           <p> Ready for Dispatch Complete Report</p>
                </div></a>
            </div>
<?php 
//}
?>


<?php 
								//	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','247')->where('submodule_access','1')->get();
								//	if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/reorderlist
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>order.jpg">
</div>
           <p>Reorder Report</p>
                </div></a>
            </div>
<?php
 
 //}
 ?>

<?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','178')->where('submodule_access','1')->get();
									//if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/completedorders
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>order.jpg">
</div>
           <p> View Your Ready for Dispatch Orders</p>
                </div></a>
            </div>
<?php 
//}
?>



<?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','50')->where('submodule_access','1')->get();
									//if($qry->num_rows()>0){
									?>

            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/dispatchfortommorow/0
"><div class="report-box badge1" data-badge="0">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>order_dispatch.png">
</div>
           <p> Dispatch for Tomorrow</p>
                </div></a>
            </div>
<?php 
//}
?>

<?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','328')->where('submodule_access','1')->get();
									//if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/servicedispatchhistory
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>to_order.png">
</div>
           <p> Dispatch for Tomorrow Service History</p>
                </div></a>
            </div>
<?php 
//}
?>

<?php 
								//	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','325')->where('submodule_access','1')->get();
								//	if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/dispatchforaccounts
"><div class="report-box badge1" data-badge="0">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>ready_billing.png">
</div>
           <p>Ready For Billing</p>
                </div></a>
            </div>
     <?php 
    //}
    ?>

        
     <?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','222')->where('submodule_access','1')->get();
									//if($qry->num_rows()>0){
									?>	
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Reporting/salesdispatchfortommorow
"><div class="report-box badge1" data-badge="0">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>sales_dispatch.png">
</div>
           <p>Sales Dispatch for Tomorrow</p>
                </div></a>
            </div>
<?php 
//}
?>


<?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','299')->where('submodule_access','1')->get();
									//if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/salesstockvaluelist
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>stock_list.png">
</div>
           <p>Sales Stock List</p>
                </div></a>
            </div>
<?php 
//}
?>

<?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','51')->where('submodule_access','1')->get();
									//if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/servicerequest
"><div class="report-box badge1" data-badge="0">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>services_request.png">
</div>
           <p> Service Request</p>
                </div></a>
            </div>
<?php 
//}
?>


<?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','52')->where('submodule_access','1')->get();
									//if($qry->num_rows()>0){
									?>	
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/previousorders
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>order_history.png">
</div>
           <p> Previous Order History</p>
                </div></a>
            </div>
<?php 
//}
?>


<?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','53')->where('submodule_access','1')->get();
									//if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/lotorderlist
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>lot_order.png">
</div>
           <p> Lot Order List</p>
                </div></a>
            </div>
<?php 
//}
?>


<?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','301')->where('submodule_access','1')->get();
									//if($qry->num_rows()>0){
									?>	
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/orderstarttoendreport
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>start_end.png">
</div>
           <p>Orders Start to End Report </p>
                </div></a>
            </div>
    <?php 
//}
?>

      
    <?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','233')->where('submodule_access','1')->get();
									//if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Reporting/divertedorderlist
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>divert_order.png">
</div>
           <p> Diverted Order List</p>
                </div></a>
            </div>
<?php 
//}
?>



<?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','105')->where('submodule_access','1')->get();
									//if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/machinecostprice
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>production_cost.png">
</div>
           <p>WIP Production Cost</p>
                </div></a>
            </div>
<?php 
//}
?>

<?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','250')->where('submodule_access','1')->get();
									//if($qry->num_rows()>0){
									?>	
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/finishedgoods
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>good_value.png">
</div>
           <p>Finished Goods Value</p>
                </div></a>
            </div>
            <?php 
        //}
        ?>
			<?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','360')->where('submodule_access','1')->get();
									//if($qry->num_rows()>0){
									?>
		 <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/salespendingordersforso
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>good_value.png">
</div>
           <p>ORDER SO</p>
                </div></a>
            </div>
									<?php 
                        //}
                                ?>
									<?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','377')->where('submodule_access','1')->get();
									//if($qry->num_rows()>0){
									?>
									 <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/dispatchreadyorder
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>good_value.png">
</div>
           <p>Ready Orders (Dispatch)</p>
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


		


 


</body>


</html>