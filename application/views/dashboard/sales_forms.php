<?php 
$user_id =$this->session->userdata['logged_in']['user_id'];
?><!DOCTYPE html>


<html>


    <head>


        <meta charset="utf-8">


        <meta name="viewport" content="width=device-width, initial-scale=1.0">


        <meta name="description" content="">


        <meta name="author" content="<?php //echo copyright; ?>">





        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">





        <title><?php //echo sitetitle; ?> Sales Form Dashboard</title>





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
<img src="<?php echo dashboard_icon;?>sales_form.jpg" style="    width: 100%;">
</div>
<div class="col-sm-8">
<h6>Sales Forms Dashboard</h6>
<p>In this panel, you will be able to find all forms that are related to all sales departments. You can edit, view, and modify the forms with the help of this panel. The sales form includes weight and dimension form, pre-sales form, post-sales freight form, visit form, daily update form, sample testing form, sales tool, and Walmart sample testing form.</p>
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
<h1>Sales Forms Dashboard</h1><hr>
</div>


  
        <div class="row">
        <?php 
								//	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','81')->where('submodule_access','1')->get();
								//	if($qry->num_rows()>0){
									?>	
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Form/view/5
"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>weight.png">
</div>
           <p>Weight & Dimension Form</p>
                </div></a>
            </div>
            <?php 
        //}
        ?>

            <?php 
								//	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','87')->where('submodule_access','1')->get();
								//	if($qry->num_rows()>0){
									?>	
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Form/view/11
"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>pre-sale.png">
</div>
           <p>Pre-Sales Freight Form</p>
                </div></a>
            </div>
          
            <?php 
        //}
        ?>
            <?php 
								//	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','234')->where('submodule_access','1')->get();
								//	if($qry->num_rows()>0){
									?>	
            <!-- <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Form/view/54
"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>sales.png">
</div>
           <p>Post Sales Freight Form</p>
                </div></a>
            </div> -->
            <?php 
        //}
        ?>
            <?php 
								//	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','115')->where('submodule_access','1')->get();
								//	if($qry->num_rows()>0){
									?>	
            <!-- <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Form/view/25
"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>purchase_request.png">
</div>
           <p>Visit Form</p>
                </div></a>
            </div> -->
            <?php 
        //}
        ?>
            <?php 
								//	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','155')->where('submodule_access','1')->get();
								//	if($qry->num_rows()>0){
									?>	
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Sales/sales_daily_update
"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>daily_update.png">
</div>
           <p>Daily Update Form</p>
                </div></a>
            </div>
            <?php 
        //}
        ?>
            <?php 
								//	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','146')->where('submodule_access','1')->get();
								//	if($qry->num_rows()>0){
									?>	
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Sampletesting
"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>order_planning.png">
</div>
           <p>Sample Testing Form</p>
                </div></a>
            </div>
            <?php 
        //}
        ?>
            <?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','263')->where('submodule_access','1')->get();
									//if($qry->num_rows()>0){
									?>	
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Sampletest
"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>sales_testing.png">
</div>
           <p>Walmart Sample Testing Form</p>
                </div></a>
            </div>
            <?php 
        //}
        ?>
            <?php 
								//	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','72')->where('submodule_access','1')->get();
								//	if($qry->num_rows()>0){
									?>	
			 <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Salestool"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>order_planning.png">
</div>
           <p>Sales Tool</p>
                </div></a>
            </div>
            <?php 
        //}
        ?>				
            <?php 
									//$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','101')->where('submodule_access','1')->get();
									//if($qry->num_rows()>0){
									?>	
		    <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Salestool/callhistory"><div class="report-box " >
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>order_history.png">
</div>
           <p>Sales Tool History</p>
                </div></a>
            </div>
            <?php 
        //}
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