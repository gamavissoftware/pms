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
			$q = $this->db->select('a.id, a.tour_start_date,a.purpose_of_trip, b.first_name, b.last_name, c.department, d.user_role')->from('member_conveyance_information a')->join('system_users_view b', 'a.employee_id=b.user_id', 'left')->join('departments c', 'b.department_id=c.department_id', 'left')->join('user_role d', 'b.user_role_id=d.user_role_id', 'left')->where('a.id', $this->uri->segment(3))->get();
			foreach ($q->result() as $row);
			
			if($row->purpose_of_trip==1){
				$purpose_of_trip= "SALES";
			}else{
				$purpose_of_trip = "SERVICE";
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
					<h4>TOUR CONVEYANCE VOUCHER</h4>
					<table class="table-vach tabled">
						<tr>
							<td width="700"></td>
							<td></td>
							<td width="100"></td>
							<td></td>
							<td width="100">DATE</td>
							<td style="border-bottom:1px dotted black;"><strong><?php echo $row->tour_start_date; ?></strong></td>
						</tr>
					</table>
					<table class="table-vach tabled">
						<tr>
							<td width="130">Name of Employee</td>
							<td style="border-bottom:1px dotted black; width:200px"><strong><?php echo ucfirst($row->first_name . " " . $row->last_name); ?></strong></td>
							<td style="width:100px"></td>
							<td width="100">Department</td>
							<td style="border-bottom:1px dotted black; "><strong><?php echo ucfirst($row->department); ?></strong></td>
							<td style="width:100px"></td>
							<td width="100">Designation</td>
							<td style="border-bottom:1px dotted black;"><strong><?php echo ucfirst($row->user_role); ?></strong></td>
						</tr>
					</table>

					<!-------------------------------------------------------------------->
<?php 
$query = $this->db->select('purpose_of_trip, tour_start_date, tour_end_date, total_days')->from('member_conveyance_information_view')->where('id',$this->uri->segment(3))->order_by('id','desc')->get();
foreach($query->result() as $rows);

?>
					<table class="table-vach" border="1">
						<tr>
							<th width="80">PURPOSE OF THE TRIP</th>
							<th>START DATE</th>
							<th>END DATE</th>
							<th>TOTAL DAYS</th>
							<th>TRAVEL CONVEYANCE BREIF</th>
							<th>Amount</th>
						</tr>


						<tr>
							<td><?php 
							if($rows->purpose_of_trip==1){
								echo "SALES";
							}else{
								echo "SERVICE";
							}
							
							?></td>
							<td><?php echo date('d-m-Y',strtotime($rows->tour_start_date));?></td>
							<td><?php echo date('d-m-Y',strtotime($rows->tour_end_date));?></td>
							<td><?php echo $rows->total_days;?></td>
							<td>
							<table border="1">
							<tr style="background-color:yellow"><th style="padding:2px 2px 2px 2px; text-align:center">DATE</th><th style="padding:2px 2px 2px 2px; text-align:center">CITY</th> <th style="padding:2px 2px 2px 2px; text-align:center">COMPANY NAME</th> <th style="padding:2px 2px 2px 2px; text-align:center">LIVING EXPENSE</th> <th style="padding:2px 2px 2px 2px; text-align:center">AMOUNT</th> <th style="padding:2px 2px 2px 2px; text-align:center">TRAVEL EXPENSE</th> <th style="padding:2px 2px 2px 2px; text-align:center">AMOUNT</th><th style="padding:2px 2px 2px 2px; text-align:center">FOOD EXPENSE</th></tr>
							
							<?php 
							$instrumentsss = array();
			$query = $this->db->select('a.conv_id,a.bill_attachment,a.livingexpensebill,a.travellingbill, a.tour_date, a.city, a.company_name, a.living_expense_id,a.foodexpense, a.living_exp_amount, a.travel_exp_id, a.travel_exp_amount, b.options, c.options as expensetype')->from('member_conveyance_brief a')->join('conveyance_type_options b','a.living_expense_id=b.id','left')->join('conveyance_type_options c','a.travel_exp_id=c.id','left')->where('a.conv_id',$row->id)->get();
			foreach($query->result() as $record){
				$bills = "<a href='".tourbills.$record->bill_attachment."' class='btn btn-success btn-xs' download>Download</a>";
				$livingamt[] = $record->living_exp_amount;
				$travelamt[] = $record->travel_exp_amount;
				$foodamt[] = $record->foodexpense;
							?>
							
						<tr>
				<td style="padding:2px 2px 2px 2px; text-align:center"><?php echo date('d-M-Y',strtotime($record->tour_date));?></td>
				<td style="padding:2px 2px 2px 2px; text-align:center"><?php echo strtoupper($record->city);?></td>
				<td style="padding:2px 2px 2px 2px; text-align:center"><?php echo strtoupper($record->company_name);?></td>
				<td style='padding:2px 2px 2px 2px; text-align:center'><?php echo strtoupper($record->options);?></td>
				<td style="padding:2px 2px 2px 2px; text-align:center"><?php echo strtoupper($record->living_exp_amount);?></td>
				<td style="padding:2px 2px 2px 2px; text-align:center"><?php echo strtoupper($record->expensetype);?></td>
				<td style="padding:2px 2px 2px 2px; text-align:center"><?php echo strtoupper($record->travel_exp_amount);?></td>
				<td style="padding:2px 2px 2px 2px; text-align:center"><?php echo strtoupper($record->foodexpense);?></td>
				
				</tr>
				<?php 
				}
			$lv_amount = array_sum($livingamt);
			$trv_amount =  array_sum($travelamt);
			$food_amount =  array_sum($foodamt);
			$grandtotal = $lv_amount+$trv_amount+$food_amount;
				?>
				</table>
				</td>
							
				<td><?php echo $grandtotal;?></td>
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
				
				<center><span class="btn btn-warning" onclick="window.print();">Take Printout</span></center>
			</div>
			<div class="col-sm-1"><!--<a href="#" class="yourlink" class="btn btn-success btn-xs">Download</a>--></div>
<script>
$('a.yourlink').click(function(e) {
    e.preventDefault();
	<?php 
	foreach($query->result() as $files){
	?>
    window.open('<?php echo tourbills;?><?php echo $files->bill_attachment;?>');
    window.open('<?php echo tourbills;?><?php echo $files->livingexpensebill;?>');
    window.open('<?php echo tourbills;?><?php echo $files->travellingbill;?>');
	<?php }?>
  
});
</script>

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