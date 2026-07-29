<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="NJ Media">
	<link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
	<title>Prestogroup</title>

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
			$user_id = $this->uri->segment(3);
	    	$startdate=date('Y-m-d',strtotime($this->uri->segment(4)));
	   		 $enddate=date('Y-m-d',strtotime($this->uri->segment(5)));
			$q = $this->db->select('a.id, a.travel_date, a.from_location, a.proceed_to, a.mode, a.start_reading, a.end_reading, a.rate_per_km, a.amount,parking_charges, b.first_name, b.last_name, c.department, d.user_role')->from('conveyance_voucher a')->join('system_users_view b', 'a.added_by=b.user_id', 'left')->join('departments c', 'b.department_id=c.department_id', 'left')->join('user_role d', 'b.user_role_id=d.user_role_id', 'left')->where('a.added_by',$user_id)->where('a.mode','2')->get();
			foreach ($q->result() as $row);
			$mode = $row->mode;
			if ($mode == '1') {
				$md = "PUBLIC CONVEYANCE";
			} else {
				$md = "OWN";
			}
			

			?>
			<div class="col-sm-1"></div>
			<div class="col-sm-10">
				<div class="card-box">
					<div class="row">
						<div class="col-sm-3"><img src="<?php echo assets_url; ?>images/Presto_logo.png"></div>
						<div class="col-sm-9">
							<h3>PRESTO STANTEST PRIVATE LIMITED</h3>
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
							<th>Amount</th>
						</tr>
						<?php
						$tanmt=array();
						$qr = $this->db->select('a.id, a.travel_date, a.from_location, a.proceed_to, a.mode, a.start_reading, a.end_reading, a.rate_per_km, a.amount,parking_charges')->from('conveyance_voucher a')->where('a.added_by',$user_id)->where('a.status','0')->where('a.mode','2')->where('travel_date BETWEEN "'. date('Y-m-d', strtotime($startdate)). '" and "'. date('Y-m-d', strtotime($enddate)).'"')->get();
						foreach ($qr->result() as $row1){
						$start_reading = $row1->start_reading;
						$end_reading = $row1->end_reading;
						$totalkm = $end_reading - $start_reading;		
						?>	
						<tr>
							<td><?php echo $row1->travel_date; ?></td>
							<td><?php echo $row1->from_location; ?></td>
							<td><?php echo $row1->proceed_to; ?></td>
							<td><?php echo $md; ?></td>
							<?php if ($row1->mode == '2') { ?>
							<td><?php echo $totalkm;?>
							<?php $finaltotal = $row1->amount;?>
							</td>
							<td><?php echo $row->rate_per_km;?></td>
							<?php } ?>
							
<td><?php  echo $finaltotal;?></td>
						</tr>
						<?php $tanmt[]=$finaltotal;?>
						<?php
					}
					?>
					<tr>
						<td colspan="6">Total Amount</td>
						<td ><?php echo array_sum($tanmt) ;?>.00</td>
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