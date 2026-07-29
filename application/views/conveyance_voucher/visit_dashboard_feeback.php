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
                        <img src="<?php echo dashboard_icon; ?>HOD Dashboard of all engineers.jpg" style="    width: 100%;">
                    </div>
                    <div class="col-sm-8">
                        <h6>HOD Dashboard of all engineers
</h6>
                        <p>With the help of this dashboard, the service team can easily determine the list of engineers meeting with the customers.</p>
                    </div>
                    <div class="col-sm-2">
                        <div class="text-center"><a href="https://vimeo.com/prestogroup/review/593693819/ed2a819c82?sort=alphabetical&direction=asc
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
						
                            <!-- <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">Filter by Date</button> -->
                            <!-- <h4 class="page-title text-center">HOD Dashboard of All Engineers</h4> -->
							<!-- <a href="<?php echo page_url;?>Sales/ftr_report"><div class="btn btn-success pull-right">Total FTR <?PHP 
							$q = $this->db->select('id')->from('engineer_visit')->where('visit_date=engineer_updatetime')->where('case_status',0)->get();
							echo count($q->result());
							
							?></div></a> -->
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						
                           
                            <h4 class="page-title"> VISITED FEEDBACK LIST
							
							</h4>
                        </div>
						
                    </div>
                </div>
				
				
				
				<div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="<?php echo page_url;?>Sales/engineer_visit_dashboard_history_filter">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Filter by Date</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
												<div class="col-md-6">
												<div class="form-group">
												<label for="field-2" class="control-label">From Date</label>
												<input class="form-control"  type="date" name="frmdate" value="">
												</div>
												</div>		
												<div class="col-md-6">
												<div class="form-group">
												<label>To Date</label>
												<input class="form-control" type="date" name="todate" value="">
												</div>
												</div>
                                                
												
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div><!-- /.modal -->
				
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
                                    <th>FEEDBACK FORM</th>
									<!-- <th>YOUR NAME</th> -->
									<th>ENGINEER NAME</th>
                                    <th>SALES FORCE NO.</th>
									<th>WARRANTY STATUS</th>
									<th>NATURE OF COMPLAINT</th>
									<th>COMPANY NAME</th>
									<th>CONTACT PERSON</th>
									<TH>CONTACT NUMBER</TH>
									<TH>LEAD</TH>
									<th>DATE</th>
									<th>INSTRUMENT DETAIL</th>
									<th>CHARGABLE</th>
									<th>PAYMENT COLLECTION</th>
									<th>CHARGES</th>
									<th>BILL NUMBER</th>
									
									<th>SERVICE REPORT</th>
									<!-- <th>NEXT ACTION</th> -->
									<th>ENGINEER PROGRESS</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">                       
                           
                            <h4 class="page-title"> VISITED FEEDBACK HISTORY
                            
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example2" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th>SR NO.</th>
                                    <th>TIMESTAMP</th>
                                    <th>FEEDBACK VIEW</th>
                                    <!-- <th>YOUR NAME</th> -->
                                    <th>ENGINEER NAME</th>
                                    <th>SALES FORCE NO.</th>
                                    <th>WARRANTY STATUS</th>
                                    <th>NATURE OF COMPLAINT</th>
                                    <th>COMPANY NAME</th>
                                    <th>CONTACT PERSON</th>
                                    <TH>CONTACT NUMBER</TH>
                                    <TH>LEAD</TH>
                                    <th>DATE</th>
                                    <th>INSTRUMENT DETAIL</th>
                                    <th>CHARGABLE</th>
                                    <th>PAYMENT COLLECTION</th>
                                    <th>CHARGES</th>
                                    <th>BILL NUMBER</th>
                                    
                                    <th>SERVICE REPORT</th>
                                    <!-- <th>NEXT ACTION</th> -->
                                    <th>ENGINEER PROGRESS</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->

<div id="add_lead_information" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="<?php echo page_url;?>Sales/hodmarkasdone">
 <input type="hidden" name="leadid" id="leadid" value="">
 <input type="hidden" name="servicecaseid" id="servicecaseid" value="">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Update Progress</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
												<div class="col-md-6">
												<div class="form-group">
												<label for="field-2" class="control-label">Action Taken</label>
												<textarea class="form-control" name="actiontaken" id="actiontaken" required></textarea>
												</div>
												</div>		
												<div class="col-md-6">
												<div class="form-group">
												<label>Customer Remark</label>
												<textarea class="form-control" name="customer_remarks" id="customer_remarks" required></textarea>
												</div>
												</div>
                                                
												
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div><!-- /.modal -->



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
 "sAjaxSource": "<?php echo page_url;?>Sales/visit_review_feedback_list/",
 "aoColumns": [
						{ mData: 'sr_no' } ,
                        { mData: 'addedon' },
                        {mData:'hod_sta'},
						/*{ mData: 'name' },*/
						{ mData:'engineer'},
						{ mData: 'sale_force_no' },
						{ mData: 'warrenty_status' },
						{ mData: 'nature_of_complaints' },
						{ mData: 'company_name' },
						{ mData: 'contact_person' },
						{ mData: 'contact_number' },
						{ mData: 'lead' },
						{mData:'visit_date'},
						{mData:'instrumentdetail'},
						{mData:'chargable'},
						{mData:'payment_to_collect'},
						{mData:'charges'},
						{mData:'bill_number'},
						
						{mData:'service_report'},
						//{mData:'next_action'},
						{ mData: 'edit' }
						
						
                ]
        });   
});

</script>

<script>
$( document ).ready(function() {
$('.select2').select2({ });
$('.select3').select2({ });
$('.select4').select2({ });
$('#example2').dataTable({
 "bProcessing": false,
 "pagination":true,
 "sAjaxSource": "<?php echo page_url;?>Sales/visit_review_feedback_list_history/",
 "aoColumns": [
                        { mData: 'sr_no' } ,
                        { mData: 'addedon' },
                        {mData:'hod_sta'},
                        /*{ mData: 'name' },*/
                        { mData:'engineer'},
                        { mData: 'sale_force_no' },
                        { mData: 'warrenty_status' },
                        { mData: 'nature_of_complaints' },
                        { mData: 'company_name' },
                        { mData: 'contact_person' },
                        { mData: 'contact_number' },
                        { mData: 'lead' },
                        {mData:'visit_date'},
                        {mData:'instrumentdetail'},
                        {mData:'chargable'},
                        {mData:'payment_to_collect'},
                        {mData:'charges'},
                        {mData:'bill_number'},
                        
                        {mData:'service_report'},
                        //{mData:'next_action'},
                        { mData: 'edit' }
                        
                        
                ]
        });   
});

</script>

<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
<script>
function showmodal(i,j){
	//alert(j);
	$("#add_lead_information").modal('show');
	$("#leadid").val(i);
	$("#servicecaseid").val(j);
}
function showmodalchangeeng(i,j){
    //alert(j);
    $("#change_engineerid").modal('show');
    $("#leadid1").val(i);
    $("#servicecaseid1").val(j);
}
</script>
</body>
</html>