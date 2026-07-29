<?php $user_id =$this->session->userdata['logged_in']['user_id'];?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php //echo copyright; ?>">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title><?php echo sitetitle; ?> Service Report</title>
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
	</head>
    <body>
	<header id="topnav">
          <?php $this->load->view('common/nav-menu');?>
        </header>
<div class="wrapper">
    <div class="container-fluid" >
<div class="dashboard-header">
<h1>Conveyance Dashboard</h1><hr>
</div>
<div>
        <div class="row">        
			<div class="col-sm-4 col-md-4 col-lg-2">
			<a href="<?php echo page_url;?>Sales/local_conveyance_dashboard
			"><div class="report-box badge1" data-badge="" id="localconveyance">
			<div class="text-center">
			<img src="<?php echo dashboard_icon;?>dashboard.png">
			</div>
			<p>Your Local Conveyance Dashboard</p>
			</div></a>
			</div>
          
			<div class="col-sm-4 col-md-4 col-lg-2">
			<a href="<?php echo page_url;?>Sales/sale_service_dashboard
			"><div class="report-box badge1" data-badge="" id="tourconveyance" >
			<div class="text-center">
			<img src="<?php echo dashboard_icon;?>dashboard.png">
			</div>
			<p> Your Tour Conveyance Dashboard</p>
			</div></a>
			</div>
           
			<div class="col-sm-4 col-md-4 col-lg-2">
			<a href="<?php echo page_url;?>Sales/local_conveyance_hod_dashboard
			"><div class="report-box badge1" data-badge="" id="localconveyancehod" style="
			background: #eee;" >
			<div class="text-center">
			<img src="<?php echo dashboard_icon;?>business_location.png">
			</div>
			<p>Local Conveyance HOD Dashboard</p>
			</div></a>
			</div>
           
			<div class="col-sm-4 col-md-4 col-lg-2">
			<a href="<?php echo page_url;?>Sales/local_conveyance_account_dashboard
			"><div class="report-box badge1" data-badge="" id="localconveyanceaccount" >
			<div class="text-center">
			<img src="<?php echo dashboard_icon;?>accounts.png">
			</div>
			<p>Local Conveyance Account/HR Dashboard</p>
			</div></a>
			</div>
          
			<div class="col-sm-4 col-md-4 col-lg-2">
			<a href="<?php echo page_url;?>Sales/sale_service_dashboard_overall
			"><div class="report-box badge1" data-badge="" id="tourconveyance" >
			<div class="text-center">
			<img src="<?php echo dashboard_icon;?>dashboard.png">
			</div>
			<p>Over All Tour Conveyance Dashboard</p>
			</div></a>
			</div>
           
			<div class="col-sm-4 col-md-4 col-lg-2">
			<a href="<?php echo page_url;?>Sales/sale_service_conveyance_hod_dashboard
			"><div class="report-box badge1" data-badge="" id="tourconveyancehod" style="background: #eee;" >
			<div class="text-center">
			<img src="<?php echo dashboard_icon;?>tour.png">
			</div>
			<p>Tour Conveyance HOD Dashboard</p>
			</div></a>
			</div>

			<div class="col-sm-4 col-md-4 col-lg-2">
			<a href="<?php echo page_url;?>Sales/sale_service_conveyance_account_dashboard
			"><div class="report-box badge1" data-badge="" id="tourconveyanceaccount" >
			<div class="text-center">
			<img src="<?php echo dashboard_icon;?>accounts.png">
			</div>
			<p>Tour Conveyance Account/HR Dashboard</p>
			</div></a>
			</div>
           
			<div class="col-sm-4 col-md-4 col-lg-2">
			<a href="<?php echo page_url;?>Sales/local_conveyance_data_history
			"><div class="report-box " >
			<div class="text-center">
			<img src="<?php echo dashboard_icon;?>visit_history.png">
			</div>
			<p>Approved Local Conveyance History</p>
			</div></a>
			</div>
           
			<div class="col-sm-4 col-md-4 col-lg-2">
			<a href="<?php echo page_url;?>Sales/sale_service_conveyance_hod_dashboard_history"><div class="report-box " >
			<div class="text-center">
			<img src="<?php echo dashboard_icon;?>tour.png">
			</div>
			<p>Approved Tour Conveyance History</p>
			</div></a>
			</div>
           
			<div class="col-sm-4 col-md-4 col-lg-2">
			<a href="<?php echo page_url;?>Sales/rejeted_local_conveyance_dashboard"><div class="report-box " >
			<div class="text-center">
			<img src="<?php echo dashboard_icon;?>dashboard.png">
			</div>
			<p>Rejected Local Conveyance </p>
			</div></a>
			</div>
          
			<div class="col-sm-4 col-md-4 col-lg-2">
			<a href="<?php echo page_url;?>Sales/rejeted_tour_conveyance_dashboard"><div class="report-box " >
			<div class="text-center">
			<img src="<?php echo dashboard_icon;?>tour.png">
			</div>
			<p>Rejected Tour Conveyance </p>
			</div></a>
			</div>           
        </div>
        </div>  
    </div>
</div>
<?php $this->load->view('common/footer');?>
<!-- End Footer -->
 </div> <!-- end container -->
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
<script src="<?php echo assets_url;?>pages/datatables.init.js"></script>
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo assets_url;?>js/jquery.core.js"></script>
<script src="<?php echo assets_url;?>js/jquery.app.js"></script>
<script>
        
        $( document ).ready(function(){
            $.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/visithoddashboard/1',
                type: 'get',
                success: function(data){
                    $('#hod_dashboard').attr('data-badge', data);
                }
            });
            
             $.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/yourvisitdata/<?php echo $user_id;?>',
                type: 'get',
                success: function(data){
                    $('#viewyourdashboard').attr('data-badge', data);
                }
            });
            
            
            $.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/qcservicerequest/7',
                type: 'get',
                success: function(data){
                    $('#qc_service').attr('data-badge', data);
                }
            });
            
            $.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/servicerepairrequest',
                type: 'get',
                success: function(data){
                    $('#servicerepairrequest').attr('data-badge', data);
                }
            });
			$.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/localconveyance',
                type: 'get',
                success: function(data){
                    $('#localconveyance').attr('data-badge', data);
                }
            });
			$.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/localconveyancehod',
                type: 'get',
                success: function(data){
                    $('#localconveyancehod').attr('data-badge', data);
                }
            });
			$.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/localconveyanceaccount',
                type: 'get',
                success: function(data){
                    $('#localconveyanceaccount').attr('data-badge', data);
                }
            });
			$.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/tourconveyance',
                type: 'get',
                success: function(data){
                    $('#tourconveyance').attr('data-badge', data);
                }
            });
			$.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/tourconveyancehod',
                type: 'get',
                success: function(data){
                    $('#tourconveyancehod').attr('data-badge', data);
                }
            });
			$.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/tourconveyanceaccount',
                type: 'get',
                success: function(data){
                    $('#tourconveyanceaccount').attr('data-badge', data);
                }
            });
			
			
        });
 
    </script>
</body>
</html>