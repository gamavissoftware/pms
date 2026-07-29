<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Prestomitr Engineer Visit History</title>

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
				font-size:11px;
			}
			table.manglesh tbody td {
				
				font-size:11px;
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
                        <img src="<?php echo dashboard_icon; ?>engineers visit histroy.jpg" style="    width: 100%;">
                    </div>
                    <div class="col-sm-8">
                        <h6>engineers visit histroy
</h6> 
                        <p>This section of the dashboard will allow you to view engineers visit history with clients. The service team can easily sort the data or search particular data on the basis of date.</p>
                    </div>
                    <div class="col-sm-2">
                        <div class="text-center"><a href="https://vimeo.com/prestogroup/review/593703840/0718db2c0e?sort=lastUserActionEventDate&direction=desc
">
                                <!-- <i class="fa fa-video-camera" aria-hidden="true"></i>  -->
                                <img src="<?php echo dashboard_icon; ?>header_icon.png" style="width: 40%; margin-top: 50px;">
                            </a>
                        </div>
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
						
                           
                            <h4 class="page-title text-center">Engineers Visit History</h4>
                        </div>
                    </div>
                </div>
               
				<div class="row" style="margin-top:20px;margin-bottom:20px;">
					<div class="col-xs-12">
                        <div class="card-box">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                       
						<form  method="POST" action="<?php echo page_url;?>Sales/engineer_visit_dashboard_history_filter" enctype="multipart/form-data">
						<div class="col-md-4">
							<div class="form-group">
							<label for="field-2" class="control-label">From Date</label>
						    <input class="form-control"  type="date" name="frmdate" value="">
							</div>
						</div>		
						<div class="col-md-4">
							<label>To Date</label>
						    <input class="form-control" type="date" name="todate" value="">
						</div>
						<div class="col-md-4">
							<label></label>
						    <button class="btn btn-success" style="margin-top:24px;" type="submit" name="submit" value="Get Data" >Get Data</button>
						</div>
						</form>
                    </div>
					</div>
					</div>
                </div>
                <!-- end page title end breadcrumb -->
			<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
		<div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th>SR NO.</th>
									<th>TIMESTAMP</th>
									<th>YOUR NAME</th>
									<th>ENGINEER NAME</th>
                                    <th>SALES FORCE NO.</th>
									<th>WARRANTY STATUS</th>
									<th>NATURE OF COMPLAINT</th>
									<th>COMPANY NAME</th>
									<th>CONTACT PERSON</th>
									<TH>CONTACT NUMBER</TH>
									<th>DATE</th>
									<th>CHARGABLE</th>
									<th>PAYMENT COLLECTION</th>
									<th>CHARGES</th>
									<th>BILL NUMBER</th>
									<th>ACTUAL OVSERVATION</th>
									<th>SPARE PARTS</th>
									<Th>PART NAME</th>
									<th>PICTURE</th>
									<th>SERVICE REPORT</th>
									<th>NEXT ACTION</th>
									<th>ENGINEER PROGRESS</th>
									<th>MARK AS DONE</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->



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
 <script>
$( document ).ready(function() {
$('.select2').select2({ });
$('.select3').select2({ });
$('.select4').select2({ });
$('#example').dataTable({
 "bProcessing": false,
 "pagination":true,
 "sAjaxSource": "<?php echo page_url;?>Sales/visit_review_for_hods_history/",
 "aoColumns": [
						{ mData: 'sr_no' } ,
                        { mData: 'addedon' },
						{ mData: 'name' },
						{ mData:'engineer'},
						{ mData: 'sale_force_no' },
						{ mData: 'warrenty_status' },
						{ mData: 'nature_of_complaints' },
						{ mData: 'company_name' },
						{ mData: 'contact_person' },
						{ mData: 'contact_number' },
						{mData:'visit_date'},
						{mData:'chargable'},
						{mData:'payment_to_collect'},
						{mData:'charges'},
						{mData:'bill_number'},
						{mData:'observation_of_engineer'},
						{mData:'spare_parts'},
						{mData:'part_name'},
						{mData:'picture'},
						{mData:'service_report'},
						{mData:'next_action'},
						{ mData: 'edit' },
						{mData:'hod_sta'}
						
                ]
        });   
});

</script>

<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
</body>
</html>