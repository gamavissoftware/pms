<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Your Attendance Dashboard</title>

      
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
  <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
		<?PHP 
$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
foreach($q->result() as $LOGO);
?>
<style>

table.manglesh thead th {

background: <?php echo $LOGO->colorcode;?>;

color:#fff;

font-weight:bold;

text-align:center;

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

                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
                           <!--  <div class="pull-left">
                                <button class="btn btn-warning waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">FILTER</button>
                            </div>
						 <div class="btn-group pull-right">
						     
						      <?php 
						      $currentime = date('H:i:s');
						       if($currentime>='10:15:00'){?><a href="<?php echo page_url;?>Master/User_management/update_attendace_of_the_employee"><button class="btn btn-success waves-effect waves-light">Check Today's Attendance</button></a><?php }?>
						      
						   <a href="<?php echo page_url;?>Master/User_management/attendance_dashboard_history/<?php echo $this->uri->segment(4);?>"><button class="btn btn-danger waves-effect waves-light">Attendance History</button></a>&nbsp; &nbsp;
                               
                               
                            </div> -->
                           
                            <h4 class="page-title text-center">Your Attendance Dashboard</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
                <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>


<div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="<?php echo page_url;?>Master/User_management/attendance_filter_report"  enctype="multipart/form-data">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">FILTER BY EMPLOYEE </h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                              <div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">EMPLOYEE NAME</label>
														 <span id="error_user_id" style="color:red;"></span>
														
														 <select class="form-control" name="user_id" id="user_id" required>
														
														 <?php 
														 $Q = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('user_status','1')->where('hide_profile','0')->order_by('first_name','asc')->get();
														 foreach($Q->result() as $row){
														 ?>
														 <option value="<?php echo $row->user_id;?>"><?php echo $row->first_name." ".$row->last_name;?></option>
														 <?php }?>
														
														 </select>
													</div>
												</div>
												
													<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">START DATE</label>
														 <span id="error_target" style="color:red;"></span>
														 <input type="date" class="form-control" name="start_date" id="start_date"  value="" required>
													</div>
												</div>
													<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">END DATE</label>
														 <span id="error_target" style="color:red;"></span>
														 <input type="date" class="form-control" name="end_date" id="end_date"  value="" required>
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

				<div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
						<table id="example" class="table table-striped table-bordered dt-responsive nowrap manglesh" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sr No</th>
                                        <!-- <th>Employee Name</th> -->
                                        <!-- <th>Department</th> -->
                                        <th>Date</th>
                                        <th>Morning Time</th>
                                        <th>Evening Time</th>
                                        <th>Total Working Time</th>
                                        <th>Attendace Status</th>
                                        
                                    <?php 
                                    if($this->uri->segment(4)){}else{?> 
                                    <!-- <th>Mark Attendance</th> -->
                                    <th>Mark Present/Absent</th>
                                    <?php }?>
                                    </tr>
                                </thead>
                                <tbody>

									
                                </tbody>
                            </table>
                        </div>
                    </div><!-- end col -->
                </div>

			

                            </div>
                            <!-- end row -->
                        </div> <!-- end ard-box -->
                    </div><!-- end col-->

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
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>
		<!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

     
<script>
$( document ).ready(function() {
$('#example').dataTable({
"bProcessing": true,
	fixedHeader: true,
"pagination":true,
"sAjaxSource": "<?php echo page_url;?>Master/User_management/user_attendance_history_report",
"aoColumns": [
				{ mData: 'sr_no' },
				// { mData: 'employee_name' },
				// { mData: 'department' },
				{ mData: 'attendance_date' },
				{ mData: 'morning_time' },
				{ mData: 'evening_time' },
				{mData:'daystat'},
				{mData:'attendace_status'}<?php 
				if($this->uri->segment(4)){}else{?>,
				// {mData:'markattendance'},
				{mData:'markpresntabsent'}<?php }?>
				
		]
});  
$('#example1').dataTable({
"bProcessing": true,
	fixedHeader: true,
"pagination":true
});
});

</script>

    </body>
</html>