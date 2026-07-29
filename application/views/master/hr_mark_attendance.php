<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Mark Your Attendance</title>

      
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

    </head>


    <body>


        <!-- Navigation Bar-->
        <header id="topnav">
          <?php $this->load->view('common/nav-menu');?>
        </header>
        <!-- End Navigation Bar-->


        <div class="wrapper">
            <div class="container">

                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right">
						  <?php 
						  $query = $this->db->select('a.attendance_date,b.first_name, b.last_name,a.employee_name, a.evening_time, a.morning_time')->from('mark_your_attendance a')->join('system_users b','a.employee_id=b.user_id','left')->where('a.id',$this->uri->segment(4))->get();
						  foreach($query->result() as $row);
						  ?>
                               
                            </div>
                           
                            <h4 class="page-title">Update Employee Attendance</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">

								<?php $message = $this->session->flashdata('message');
								if($message){}else{
								?>
								
                                   <form validate="true" action="<?php echo page_url;?>Master/User_management/update_attendance/<?php echo $this->uri->segment(4);?>" method="post"  enctype="multipart/form-data">
                                       <div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Employee Name</label>
														 <span id="error_attendance" style="color:red;">*</span><br>
									    <input type="text" class="form-control" name="employee_name" value="<?php if($row->employee_name==''){ echo $row->first_name." ".$row->last_name;}else{?><?php echo $row->employee_name;}?>" readonly>
												</div>
												</div>
												
										<div class="col-md-1">
													<div class="form-group">
														 <label for="field-2" class="control-label">Date</label>
														 <span id="error_attendance" style="color:red;">*</span><br>
									<input type="text" class="form-control" name="attendancedate" value="<?php echo date('d-m-Y',strtotime($row->attendance_date));?>" readonly>
													</div>
												</div>
												
									    	<div class="col-md-1">
													<div class="form-group">
														 <label for="field-2" class="control-label">Mark Your Attendance</label>
														 <span id="error_attendance" style="color:red;">*</span>
														 <input type="checkbox" name="attendance" value="1" required>
													</div>
												</div>
											
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Attendance Morning Time</label>
														 <span id="error_attendance" style="color:red;">*</span><br>
														 <input type="time" class="form-control" name="morning_time" id="morning_time" value="<?php echo $row->morning_time;?>" required>
													</div>
												</div>
												
												
													<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Attendance Evening Time</label>
														
														 <input type="time" class="form-control" name="endtime" id="endtime" value="<?php echo $row->evening_time;?>">
													</div>
												</div>
												
													
											<div class="col-md-4">
											    <div class="form-group">
											        <label>Remarks </label>
											        <textarea class="form-control" name="remarks" id="remarks"></textarea>
											    </div>
											</div>
												
											<div class="col-md-11"></div>
											<div class="col-md-1">
											<div class="form-group" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" class="btn btn-success" id="save" value="Submit">
											</div>
										</div>
                                            </div>
									</form>
                                  <?php }?> 

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
"sAjaxSource": "<?php echo page_url;?>Master/User_management/your_attendance_report",
"aoColumns": [
				{ mData: 'sr_no' },
				{ mData: 'attendance_date' },
				{ mData: 'morning_time' },
				{ mData: 'morning_selfie' },
				{ mData: 'evening_time' },
				{ mData: 'evening_selfie' },
				{mData:'daystat'}
				
		]
});   
});

</script>

    </body>
</html>