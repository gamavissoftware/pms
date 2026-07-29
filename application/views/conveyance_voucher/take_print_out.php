<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="NJ Media">
	<link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
	<title><?php echo sitetitle; ?>  Take Printout</title>

	<!-- Table Responsive css -->
	<script src="<?php echo assets_url; ?>js/angular.min.js"></script>
	<!-- DataTables -->
	<link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
	<link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
	<link href="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
	<link href="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
	<link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
	<link href="<?php echo assets_url; ?>plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

	<!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

	<script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
	<style>
		body {
			font-family: 'Montserrat', sans-serif;

		}

		table.manglesh thead th {
			background: #003366;
			color: #fff;
			font-weight: bold;
		}

		#pageloader {
			background: rgba(255, 255, 255, 0.8);
			display: none;
			height: 100%;
			position: fixed;
			width: 100%;
			z-index: 9999;
		}

		#pageloader img {
			left: 30%;
			margin-left: -10px;
			margin-top: -10px;
			position: absolute;
			top: 30%;
		}

		.card-box {
			margin-top: 50px;
			padding: 10px;
		}

		.card-box img {
			width: 200px;
		}

		.card-box h3 {
			font-size: 30px;
			/* text-align: center; */
			font-weight: 900;

		}

		.card-box h4 {
			font-size: 11px;
			text-align: center;
			border: 2px solid #505458;
			width: 160px;
			padding: 0px;
			border-radius: 15px;
			margin: auto;
			font-weight: 900;
		}

		.table-vach {
			width: 100%;

			margin-top: 20px;
		}

		.table-vach th {
			text-align: center;
			padding: 5px;
		}

		.table-vach td {
			text-align: center;
			padding: 5px;
		}

		.tabled td {
			text-align: left !important;
			padding: 2px !important;
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
		<div class="container">
			<?php
			$q = $this->db->select('a.id, a.travel_date, a.from_location, a.proceed_to, a.mode, a.start_reading, a.end_reading, a.rate_per_km, a.amount,parking_charges, b.first_name, b.last_name, c.department, d.user_role')->from('conveyance_voucher a')->join('system_users_view b', 'a.added_by=b.user_id', 'left')->join('departments c', 'b.department_id=c.department_id', 'left')->join('user_role d', 'b.user_role_id=d.user_role_id', 'left')->where('a.id', $this->uri->segment(3))->get();
			foreach ($q->result() as $row);
			$mode = $row->mode;
			if ($mode == '1') {
				$md = "PUBLIC CONVEYANCE";
			} else {
				$md = "OWN";
			}
			$start_reading = $row->start_reading;
			$end_reading = $row->end_reading;
			$totalkm = $end_reading - $start_reading;

			?>
			<div class="col-sm-1"></div>
			<div class="col-sm-10">
				<div class="card-box">
					<div class="row">
						<div class="col-sm-3"><img src="<?php echo assets_url; ?>images/logo_mitr.png"></div>
						<div class="col-sm-9">
							<h3>HPCL SUNDAR INDUSTRIAL OIL</h3>
						</div>
					</div>
					<h4>CONVEYANCE VOUCHER</h4>
					<table class="table-vach tabled">
						<tr>
							<td width="700"></td>
							<td></td>
							<td width="100"></td>
							<td></td>
							<td width="100" style="font-weight:bold;">DATE</td>
							<td style="border-bottom:1px dotted black;" width="100"><?php echo $row->travel_date; ?></td>
						</tr>
					</table>
					<table class="table-vach tabled">
						<tr>
							<td width="130" style="font-weight:bold;">Name of Employee</td>
							<td style="border-bottom:1px dotted black; width:200px"><?php echo ucfirst($row->first_name . " " . $row->last_name); ?></td>
							<td style="width:100px"></td>
							<td width="100" style="font-weight:bold;">Department</td>
							<td style="border-bottom:1px dotted black;"><?php echo ucfirst($row->department); ?></td>
							<td style="width:100px"></td>
							<td width="100" style="font-weight:bold;">Designation</td>
							<td style="border-bottom:1px dotted black;" width="150"><?php echo ucfirst($row->user_role); ?></td>
						</tr>
					</table>

					<!-- <table class="table-vach" border="1">
						<tr>
							<th width="80">Date</th>
							<th>From</th>
							<th>Proceed To</th>
							<th>Mode</th>
							<?php if ($row->mode == '2') { ?>
								<th colspan="2">If Own Vehicle
									<table class="table-vach" style="margin:0">
										<tr>
											<td>Kms.</td>
											<td>Rate</td>
										</tr>
									</table>
								</th>
							<?php } ?>
							<?php if ($row->mode == '1') { ?>
								<th colspan="3">If Public Conveyance

								</th>
							<?php } ?>
							<th>Amount</th>
						</tr>


						<tr>
							<td><?php echo $row->travel_date; ?></td>
							<td><?php echo $row->from_location; ?></td>
							<td><?php echo $row->proceed_to; ?></td>
							<td><?php echo $md; ?></td>
							<td></td>
							<td></td>


						</tr>
						<tr>
							<td colspan="4"><strong>Total</strong></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>

					</table> -->

					<!-------------------------------------------------------------------->

					<table class="table-vach" border="1">
						<tr>
							<th width="80">Date</th>
							<th>From</th>
							<th>Proceed To</th>
							<th>Mode</th>
							<?php if ($row->mode == '2') { ?>
							<th colspan="2">If Own Vehicle
								<table class="table-vach" style="margin:0">
									<tr>
										<td>Kms.</td>
										<td>Rate</td>
									</tr>
								</table>
							</th>

							<?php } ?>
							<?php if ($row->mode == '1') { ?>
							<th>If Public Conveyance</th>
							<?php } ?>
							<th>Amount</th>
						</tr>


						<tr>
							<td><?php echo $row->travel_date; ?></td>
							<td><?php echo $row->from_location; ?></td>
							<td><?php echo $row->proceed_to; ?></td>
							<td><?php echo $md; ?></td>
							<?php if ($row->mode == '2') { ?>
							<td><?php echo $totalkm;?>
							<?php $finaltotal = $row->amount;?>
							</td>
							<td><?php echo $row->rate_per_km;?></td>
							<?php } ?>
							<?php if ($row->mode == '1') { ?>
							<td>
								<table class="table-vach" border="1">
								<?php 
								$q = $this->db->select('a.expense_type, a.amount, b.options, a.to_location, a.from_location')->from('local_conveyance_expense a')->join('conveyance_type_options b','a.expense_type=b.id','left')->where('a.conveyance_id',$this->uri->segment(3))->get();
								$k=1;
								foreach($q->result() as $rows){
									$totalamount[] = $rows->amount;
								?>
									<tr>
										<td><?php echo $k;?></td>
										<td><?php echo $rows->options;?></td>
										<td><?php echo $rows->to_location;?></td>
										<td><?php echo $rows->from_location;?></td>
										<td><?php echo $rows->amount;?></td>
									</tr>
								<?php $k++;}?>
								</table>
							</td>
							<?php 
							$finaltotal = array_sum($totalamount);
							} ?>
<td><?php echo $finaltotal;?></td>
						</tr>


					</table>

					<!-------------------------------------------------------------------->
					<table class="table-vach">
						<tr>
							<th style="text-align:left;">Signature Of Claiment</th>
							<th style="text-align:left;">Checked By</th>
							<th style="text-align:right;">Approved By</th>
						</tr>
					</table>
				</div>
				<center><span class="btn btn-warning" onclick="printpage();" id="printpagebutton">Take Printout</span></center>

				<script>
					function printpage() {
        var printButton = document.getElementById("printpagebutton");
        printButton.style.visibility = 'hidden';
        window.print()
        printButton.style.visibility = 'visible';
    }
				</script>
			</div>
			<div class="col-sm-1"></div>


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
	<script src="<?php echo assets_url; ?>js/fastclick.js"></script>
	<script src="<?php echo assets_url; ?>js/jquery.slimscroll.js"></script>
	<script src="<?php echo assets_url; ?>js/jquery.blockUI.js"></script>
	<script src="<?php echo assets_url; ?>js/waves.js"></script>
	<script src="<?php echo assets_url; ?>js/wow.min.js"></script>
	<script src="<?php echo assets_url; ?>js/jquery.nicescroll.js"></script>
	<script src="<?php echo assets_url; ?>js/jquery.scrollTo.min.js"></script>
	<script src="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
	<script src="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

	<script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
	<script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			var i = 1;
			$('#addmore_btn').click(function() {

				i++;
				$('#dynamictasks').append('<div id="row' + i + '" class="row"><div class="col-md-5"><div class="form-group"><label>TRAVEL EXPENSE</label><select class="form-control" name="travel_expense[]" id="travel_expense" onChange="fetch_expense_options(0);" ><option value="">SELECT EXPENSE TYPE</option><?php $query = $this->db->select('id, options')->from('conveyance_type_options')->where('conveyance_type_id', '2')->where('status', '1')->get();
																																																																														foreach ($query->result() as $row) { ?><option value="<?php echo $row->id; ?>"><?php echo $row->options; ?></option><?php } ?></select></div></div><div class="col-md-3"><div class="form-group"><label>AMOUNT</label><input type="number" class="form-control" name="travel_expense_amount[]" id="travel_expense_amount" value="" step="0.2" ></div></div><div class="col-md-3"><div class="form-group"><label>BILL ATTACHMENT</label><input type="file" class="form-control" name="bill_attachment[]" id="bill_attachment" value="" ></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:30px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="' + i + '"><i class="fa fa-close"></i></button></div></div></div><br/>');

			});


			$(document).on('click', '.btn_remove', function() {
				var button_id = $(this).attr("id");
				$('#row' + button_id + '').remove();
			});

		});
	</script>
	<script language="javascript" type="text/javascript">
		$(document).ready(function() {
			$("#save").click(function() {

				var date = $("#date").val();
				if (date == '') {
					$("#error_date").html('Required!');
				}
				var from = $("#from").val();
				if (from == '') {
					$("#error_from").html('Required!');
				}
				var proceed_to = $("#proceed_to").val();
				if (proceed_to == '') {

					$("#error_proceed_to").html('Required!');
				}

				var mode = $("#mode").val();
				if (mode == '') {

					$("#error_mode").html('Required!');
				}


				if (date == '' || from == '' || proceed_to == '' || mode == '') {

					return false;
				}

			});
		});



		function getrate(type) {
			$.ajax({
				type: "post",
				url: "<?php echo page_url; ?>Sales/getperkmrate/" + type,
				data: "1",
				success: function(data) {

					$("#rate_per_km").val(data);
					calculatetotamount();
				}
			});


		}

		function calculatetotamount() {
			$("#finalamount").val('');
			var start_reading = $("#start_reading").val().trim();
			var end_reading = $("#end_reading").val().trim();
			var rate_per_km = $("#rate_per_km").val().trim();
			var parkingamount = $("#parkingamount").val().trim();

			if (start_reading != '' && end_reading != '' && rate_per_km != '') {
				if (end_reading > start_reading) {
					var diff = end_reading - start_reading;
					var amount = rate_per_km * diff;
					var finalvalue = parseInt(amount) + parseInt(parkingamount);
					$("#finalamount").val(finalvalue);
				} else {
					alert('End reading cannot be less than Start reading');
					return false;

				}


			}

		}
	</script>

	<script src="<?php echo assets_url; ?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
	<script>
		$(document).ready(function() {
			$("#loginForm").on("submit", function() {
				$("#pageloader").fadeIn();
			}); //submit
		}); //document ready
	</script>
	<script>
		$(document).ready(function() {
			$("#datepicker1").datepicker({
				dateFormat: 'dd-mm-yyyy'
			});
			$("#datepicker1btn").click(function(event) {
				event.preventDefault();
				$("#datepicker1").focus();

			})

		});
	</script>
</body>

</html>