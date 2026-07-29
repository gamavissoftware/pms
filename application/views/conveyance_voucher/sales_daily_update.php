<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="NJ Media">

	<link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

	<title>Prestogroup Schedule</title>

	<!-- Table Responsive css -->
	<script src="<?php echo assets_url; ?>js/angular.min.js"></script>
	<!-- DataTables -->
	<link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
	<link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
	<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
	<!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" rel="stylesheet" />
	<script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
	<style>
		.divheight {
			padding-top: 20px;
		}
	</style>
</head>


<body>


	<!-- Navigation Bar-->
	<header id="topnav">
		<?php $this->load->view('common/nav-menu'); ?>
	</header>
	<!-- End Navigation Bar-->

	<div class="wrapper">
        <div class="container-fluid">
            <div class="desc-box">
                <div class="row">
                    <div class="col-sm-2">
                        <img src="<?php echo dashboard_icon; ?>field-sales-daily-update.png" style="    width: 100%;">
                    </div>
                    <div class="col-sm-8">
                        <h6>FIELD SALES DAILY UPDATE FORM</h6>
                        <p class="pagedescriptionfont">Those sales engineers that go for the visit can make the use of this form to update the details.

</p>
                    </div>
                    <div class="col-sm-2">
                        <div class="text-center"><a href="https://vimeo.com/prestogroup/review/593691024/daa38debbe?sort=alphabetical&direction=asc
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
		<div class="container">

			<!-- Page-Title -->
			<div class="divheight hidden-xs"></div>
			<div class="row">
				<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
					<div class="page-title-box">

						<h4 class="page-title text-center">FIELD SALES DAILY UPDATE FORM</h4>
					</div>
				</div>
			</div>
			<!-- end page title end breadcrumb -->


			<div class="row">
				<div class="col-xs-12">
					<div class="card-box">
						<form id="loginForm" method="post" action="<?php echo page_url; ?>Sales/sales_daily_update" enctype="multipart/form-data">
							<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
							<div class="row">
								<?php
								$first_name = $this->session->userdata['logged_in']['user_name'];
								$last_name = $this->session->userdata['logged_in']['last_name'];
								?>
								<div class="col-md-3">
									<div class="form-group">
										<label for="field-2" class="control-label">Your Name</label>
										<span id="error_machine" style="color:red;">*</span>
										<input type="text" class="form-control" name="yourname" id="yourname" value="<?php echo $first_name; ?> <?php echo $last_name; ?>" readonly>
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<label for="field-2" class="control-label">Customer Name</label>
										<span id="error_machine" style="color:red;">*</span>
										<input type="text" class="form-control" name="customer_name" id="customer_name" value="" required>
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<label for="field-2" class="control-label">Customer Contact Number</label>
										<span id="error_machine" style="color:red;">*</span>
										<input type="text" class="form-control" name="contact_number" id="contact_number" value="" required>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label for="field-2" class="control-label">Company Name</label>
										<span id="error_company_name" style="color:red;">*</span>
										<input type="text" class="form-control" name="company_name" id="company_name" value="" required>
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<label for="field-2" class="control-label">Visit No</label>
										<span id="error_visit_number" style="color:red;">*</span>
										<input type="text" class="form-control" name="visit_number" id="visit_number" value="" required>
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<label for="field-2" class="control-label">Action Taken</label>
										<span id="error_action_taken" style="color:red;">*</span>
										<select class="form-control" name="action_taken" id="action_taken">
											<option value="VISIT">VISIT</option>
											<option value="PHONE CALL">PHONE CALL</option>
											<option value="MAIL">MAIL</option>
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label for="field-2" class="control-label">Stage</label>
										<span id="error_stage" style="color:red;">*</span>
										<select class="form-control" name="stage" id="stage">
											<option value="PRELIMINARY">PRELIMINARY</option>
											<option value="PHONE CALL">TECHNICAL</option>
											<option value="COMERCIAL">COMERCIAL</option>
											<option value="NEGOTIATION">NEGOTIATION</option>
											<option value="FINALIZATION">FINALIZATION</option>
										</select>
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<label for="field-2" class="control-label">Value</label>
										<span id="error_stage" style="color:red;">*</span>
										<select class="form-control" name="value" id="value">
											<option value="0-1 LACS.">0-1 LACS.</option>
											<option value="1-3 LACS.">1-3 LACS.</option>
											<option value="3-5 LACS.">3-5 LACS.</option>
											<option value="5 LACS. & ABOVE">5 LACS. & ABOVE</option>

										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label for="field-2" class="control-label">Machine Name</label>
										<span id="error_machine_name" style="color:red;">*</span>
										<input type="text" class="form-control" name="machine_name" id="machine_name" value="" required>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label for="field-2" class="control-label">Next Action Plan</label>
										<span id="error_next_action_plan" style="color:red;">*</span>
										<input type="text" class="form-control" name="next_action_plan" id="next_action_plan" value="" required>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label for="field-1" class="control-label">Date of Next Plan</label>
										<span id="error_visit_date" style="color:red;"></span>
										<input type="text" id="datepicker1" name="date_of_next_plan" class="form-control datepicker" autocomplete="off" value="" required>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label for="field-1" class="control-label">Visiting Card Image (Upload if Any )</label>

										<input type="file" id="visiting_card" name="visiting_card" class="form-control" autocomplete="off" value="">
									</div>
								</div>

								<div class="col-md-12">
									<div class="form-group">
										<label>Remarks</label>
										<textarea class="form-control" name="remarks" id="remarks"></textarea>
									</div>
								</div>


								<div class="col-md-12">
									<div class="form-group pull-right">
										<input type="submit" id="save" class="btn btn-info" value="Submit">
									</div>
								</div>

							</div>
						</form>

						<!-- end row -->
					</div> <!-- end ard-box -->
				</div><!-- end col-->

			</div>
			<!-- end row -->
			<!-- Footer -->
			<?php $this->load->view('common/footer'); ?>
			<!-- End Footer -->

		</div> <!-- end container -->
	</div>
	<!-- end wrapper -->


	<!-- jQuery  -->
	<script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
	<script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
	<script src="<?php echo assets_url; ?>js/detect.js"></script>
	<!-- <script src="<?php echo assets_url; ?>js/fastclick.js"></script>
	<script src="<?php echo assets_url; ?>js/jquery.slimscroll.js"></script>
	<script src="<?php echo assets_url; ?>js/jquery.blockUI.js"></script>
	<script src="<?php echo assets_url; ?>js/waves.js"></script>
	<script src="<?php echo assets_url; ?>js/wow.min.js"></script>
	<script src="<?php echo assets_url; ?>js/jquery.nicescroll.js"></script>
	<script src="<?php echo assets_url; ?>js/jquery.scrollTo.min.js"></script> -->

	<!-- Datatables-->
	<script src="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.js"></script>
	<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.bootstrap.js"></script>
	<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.buttons.min.js"></script>
	<script src="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.js"></script>
	<!-- <script src="<?php echo assets_url; ?>plugins/datatables/jszip.min.js"></script>
	<script src="<?php echo assets_url; ?>plugins/datatables/pdfmake.min.js"></script>
	<script src="<?php echo assets_url; ?>plugins/datatables/vfs_fonts.js"></script>
	<script src="<?php echo assets_url; ?>plugins/datatables/buttons.html5.min.js"></script>
	<script src="<?php echo assets_url; ?>plugins/datatables/buttons.print.min.js"></script> -->
	<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.fixedHeader.min.js"></script>
	<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.keyTable.min.js"></script>
	<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.responsive.min.js"></script>
	<script src="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.js"></script>
	<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.scroller.min.js"></script>
	<script src="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
	<!-- Datatable init js -->
	<script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>

	<!-- App js -->
	<script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
	<script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

	<script language="javascript" type="text/javascript">
		$(document).ready(function() {
			$('.select2').select2({});
			$('.select3').select2({});
			$('.select4').select2({});
			$("#save").click(function() {
				var customer_name = $("#customer_name").val();
				if (customer_name == '') {
					$("#error_customer_name").html('Required!');
				}
				var contact_number = $("#contact_number").val();
				if (contact_number == '') {
					$("#error_contact_number").html('Required!');
				}
				var company_name = $("#company_name").val();
				if (company_name == '') {
					$("#error_company_name").html('Required!');
				}

				var address = $("#address").val();
				if (address == '') {

					$("#error_address").html('Required!');
				}
				var visit_date = $("#visit_date").val();
				if (visit_date == '') {

					$("#error_visit_date").html('Required!');
				}
				var schedule_time = $("#schedule_time").val();
				if (schedule_time == '') {

					$("#error_schedule_time").html('Required!');
				}
				var who_will_visit = $("#who_will_visit").val();
				if (who_will_visit == '') {

					$("#error_who_will_visit").html('Required!');
				}


				if (customer_name == '' || contact_number == '' || company_name == '' || address == '' || visit_date == '' || schedule_time == '' || who_will_visit == '') {

					return false;
				}

			});
		});
	</script>
	<script src="<?php echo assets_url; ?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
	<script>
		$(document).ready(function() {
			$('.datepicker').datepicker({
				todayHighlight: true
			});

			$("#datepicker1").datepicker({
				orientation: 'bottom',
				todayHighlight: true
			});
			//	$('.datepicker').datepicker({todayHighlight:true});
			$("#datepicker1btn").click(function(event) {
				event.preventDefault();
				$("#datepicker1").focus();

			})

		});
	</script>
</body>

</html>