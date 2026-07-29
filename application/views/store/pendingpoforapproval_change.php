<?php
	$CI = &get_instance();
	$CI->load->model('Store_model');

	/** THIS IS FOR TOTAL **/
	$jbvalue = $CI->Store_model->jobcardunapprovedpo();
	$indentvalue = $CI->Store_model->indentunapprovedpo();
	$imsvalue = $CI->Store_model->imsunapprovedpo();
	/** END **/


	/** THIS IS ONLY FOR UNAPPROVED **/
	$jbvalueforunapprove = $CI->Store_model->jobcardunapprovedpovalue();
	$indentvalueforunapprove = $CI->Store_model->indentunapprovedpovalue();
	$imsvalueforunapprove = $CI->Store_model->imsunapprovedpovalue();
	/** END **/
	$totalmonthlypo = $CI->Store_model->totalapprovedpothismonth();
	?>

	<!DOCTYPE html>

	<html>

	<head>

		<meta charset="utf-8">

		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<meta name="description" content="">

		<meta name="author" content="NJ Media">

		<link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

		<title>PENDING PO FOR APPROVAL</title>

		<!-- Table Responsive css -->

		<script src="<?php echo assets_url; ?>js/angular.min.js"></script>

		<!-- DataTables -->

		<link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />

		<link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />

		<link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />

		<link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />

		<link href="https:cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.dataTables.min.css">

		<link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />

		<script src="https:cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

		<link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">

		<script type="text/javascript" src="<?php echo assets_url; ?>ckeditor/ckeditor.js"></script>

		<link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />

		<link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />

		<link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />

		<link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />

		<link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />

		<link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />

		<link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />

		<link href="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">

		<script type="text/javascript" src="<?php echo assets_url; ?>ckeditor/ckeditor.js"></script>

		<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">

		<!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->

		<!-- WARNING: Respond.js doesn't work if you view the page via file: -->

		<!--[if lt IE 9]>

        <script src="https:oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>

        <script src="https:oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>

        <![endif]-->



		<script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>

		<style>
			table.manglesh thead th {

				background: #003366;

				color: #fff;

				font-size: 11px;

				font-weight: bold;

				text-align: center;

				padding: 10px !important;

			}

			table tbody tr td {

				font-size: 14px;

				color: #000;

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

				left: 50%;

				margin-left: -32px;

				margin-top: -32px;

				position: absolute;

				top: 50%;

			}

			#fixedbutton {
				position: fixed;
				top: 50%;
				right: 0%;
				z-index: 999;
			}

			.row-flex {
				display: flex;
				flex-wrap: wrap;
			}

			@media (max-width:576px) {
				.row-flex {
					display: block;
				}
			}

			.flutter {
				height: 100%;
				padding: 10px;
				background-color: white;
				box-shadow: 1px 1px 10px lightgrey;
				/* margin-top: 20px; */
				border-radius: 5px;

			}

			.page-title {
				font-size: 20px;
				font-weight: 600;
				margin-bottom: 0px;
				margin-top: 0px;
				line-height: 70px;
			}

			@media (max-width:576px) {
				.page-title {
					font-size: 17px;
					font-weight: 600;
					margin-bottom: 20px;
					margin-top: 30px;
					line-height: 30px;
				}
			}

			.btn-own{
				border: 1px solid black;
				background-color: lightgray;
				color: black;
				font-weight: 600;
			}
		</style>

	</head>

	</head>





	<body>





		<!-- Navigation Bar-->

		<header id="topnav">

			<?php $this->load->view('common/nav-menu'); ?>

		</header>

		<!-- End Navigation Bar-->



		<div class="wrapper">

			<div class="container-fluid">



				<!-- Page-Title -->


				<div class="row">


					<div class="col-sm-12">

						<div class="col-md-4">
							<div class="page-title-box">
								<!-- <h4 class="page-title">PENDING PO FOR APPROVAL</h4> -->
							</div>
						</div>
						<div class="col-md-6"></div>
						<div class="col-md-2">
							<a href="<?php echo page_url; ?>Reporting/pendingpoforapproval_history"><span class="btn btn-primary btn-xs" style="    float: right;">Approved PO History</span></a>
						</div>

					</div>

				</div>

				<!-- end page title end breadcrumb -->

				<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

				<div class="row ">
					<div class="col-sm-12">
						<div class="row row-flex">
							<div class="col-md-3"></div>
							<div class="col-md-4 ">
								<div class="flutter">

									<table class="table table-bordered">
										<thead>
											<tr>
												<th colspan="6" style="text-align:center;">UNAPPROVED PO VALUE</th>


											</tr>

										</thead>
										<tbody>
											<?php
											$total = round($jbvalueforunapprove, 0) + round($indentvalueforunapprove, 0) + round($imsvalueforunapprove, 0);
											?>
											<tr>
												<td colspan="2" class="text-center">JOBCARD VALUE - <?php echo $CI->Store_model->moneyFormatIndia(round($jbvalueforunapprove, 0)); ?></td>
												<td colspan="2" class="text-center">INDENT VALUE - <?php echo $CI->Store_model->moneyFormatIndia(round($indentvalueforunapprove, 0)); ?></td>
												<td colspan="2" class="text-center">IMS VALUE - <?php echo $CI->Store_model->moneyFormatIndia(round($imsvalueforunapprove, 0)); ?></td>

											</tr>
										</tbody>
									</table>
								</div>
							</div>


							<div class="col-md-3">
								<div class="flutter">
									<table class="table table-bordered">
										<thead>
											<tr>
												<th colspan="" style="text-align:center;">Total PO value (TODAY)</th>
												<th colspan="" style="text-align:center;">Total PO value (<?php echo strtoupper(date('M')); ?>)</th>


											</tr>

										</thead>
										<tbody>
											<?php

											$total = round($jbvalue, 0) + round($indentvalue, 0) + round($imsvalue, 0);


											?>
											<tr>
												<td class="text-center"><?php echo $CI->Store_model->moneyFormatIndia($total); ?></td>
												<td class="text-center"><?php echo $CI->Store_model->moneyFormatIndia($totalmonthlypo); ?></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>


					<div class="col-sm-12">
						<div class="row">
							<div class="col-sm-12">
								<div class="page-title-box" style="display: flex;">
									<h4 class="page-title">PENDING PO FOR APPROVAL(JOB CARD) OF VALUE - <?php echo $CI->Store_model->moneyFormatIndia(round($jbvalueforunapprove, 0)); ?></h4>
								<!-- 	<div style="margin-top: 23px;    position: absolute;
    right: 12px;"><a href="<?php //echo page_url; ?>Store/approvedalljobcardpo" class="btn btn-own btn-xs">APPROVED ALL JOB CARD PO ITEM</a></div> -->
								</div>

							</div>
						</div>

<form action="<?php echo page_url;?>Store/approvebulkpos" method="post">
						<div class="card-box table-responsive">

							<table id="example123445" class="table table-bordered manglesh">

								<thead align="center">

									<tr>

										<th width="50">Sr No.</th>

										<th width="200">VENDOR NAME</th>

										<th width="500"><span class="text-center">ITEM</span> <span class="pull-right">Approve All<br/><input type="checkbox" class="approveallbulkjb1" onchange="checkallbulkjb('approveallbulkjb1','approvealljb');" style='width:20px;height:20px;'></span></th>

									</tr>

								</thead>

								<tbody>

									<?php
									$res = $this->db->select('v.name,v.id, v.address,v.payment_terms,v.payment_mode,v.deliverytime,v.otherdeliverytime')->from('vendors v')->join('purchase_order a', 'a.vendor=v.id')->where('a.source', '1')->where('a.approved', 0)->where('a.negotiation',1)->group_by('a.vendor')->get();
									if ($res->num_rows() > 0) {
										$i = 1;

										foreach ($res->result() as $vendor) {
											$venderid = $vendor->id;
											$totalamt=array();
											$rest = $this->db->select('a.price,a.qty,a.vendor,a.pricechange,a.originalprice,a.id,a.prno,a.jobcard,a.source,h.sourceid,a.potype,a.addedOn,a.pono,e.first_name,e.last_name,a.itemid,e.department_id, v.name, v.address,v.payment_terms,v.payment_mode,v.deliverytime,v.otherdeliverytime, a.fright, a.fright_charges, a.packing_charges')

												->from('purchase_order_view a')

												->join('system_users_view e', 'e.user_id=a.addedBy', 'left')

												->join('purchase_request_view h', 'h.prno=a.prno', 'left')

												->join('vendors_view v', 'a.vendor=v.id', 'left')

												->where('a.approved', '0')
												->where('a.source', '1')
												->where('a.negotiation', '1')
												->where('a.vendor', $venderid)
												->order_by('a.addedOn', 'DESC')

												->group_by('a.id')->get();

											if ($rest->num_rows() > 0) {

												$j = 1;
												$html1 = '';
												$prevpo = '';
												//$html1 = '<form action="' . page_url . 'Store/approvebulkpos" method="post" onsubmit="return checkforsingleboxapproval();">';
												$html1 .= "<table style='width:100%;' class='table table-bordered manglesh' >
									 			<thead align='center'>
												<tr>
												<th style='width:50px'>Sno</th>
												<th style='width:200px'>Item</th>
												<th style='width:20px'>Qty</th>
												<th style='width:100px'>PR/PO No.</th>
												<th style='width:100px'>Source</th>
												<th style='width:50px'>Per Unit Price</th>
												<th style='width:50px'>Previous Rate</th>
												<th style='width:50px'>Diff</th>
												<th style='width:50px'>Approve</th>
												<th style='width:50px'>Sent To Accounts</th>
												<th style='width:50px'>Reject</th>
												</tr>
												</thead>
												<tbody>
													";

													//All <br> <input type='checkbox' class='appdata" . $venderid . "1' onclick='checkalldata(" . $venderid . ",1);' style='width:20px;height:20px;'>

												foreach ($rest->result() as $restyui1) {

													$jobcard = '';


													if ($restyui1->source == 1) {

														$job = $this->db->select('job_card_no')->from('order_instruments_view')->where('id', $restyui1->jobcard)->get();

														if ($job->num_rows() > 0) {

															foreach ($job->result() as $job1);

															$jobcard = 'Jobcard-' . $job1->job_card_no;
														} else {

															$jobcard = '';
														}

														$polink = "po";

														$mrnlink = "mrn";
													} else if ($restyui1->source == 2) {

														/** Intend **/

														$jobcard = 'INDENT- IND' . $restyui1->sourceid;

														$polink = "generalpo";

														$mrnlink = "generalmrn";
													} else if ($restyui1->source == 3) {

														/** Intend **/

														$jobcard = 'AUTO PR';

														$polink = "po";

														$mrnlink = "mrn";
													} else if ($restyui1->source == 4) {

														/** Intend **/
														$jobcard = 'AUTO PR AGAINST BLOCKAGE';

														$polink = "po";

														$mrnlink = "mrn";
													}


													if ($restyui1->potype == '0') {

														$polink = "po";

														$mrnlink = "mrn";
													} else {

														$polink = "generalpo";

														$mrnlink = "generalmrn";
													}



													$html = '<a href="javascript:;" onclick="approvepo(' . "'" . $restyui1->pono . "'" . ');" class="btn btn-warning btn-xs">APPROVE PO</a>';

													$approved = '<span id="app' . $restyui1->id . '" style="color:green;font-weight:bold;"><input type="checkbox"  class="approvealljb approval' . $venderid . '1" name="approvedrecord[]" id="approvedrecord' . $restyui1->id . $restyui1->itemid . '" style="width:20px;height:20px;" value="' . $restyui1->id . '" onchange="markasapproved(' . "'" . $restyui1->id . "'" . ',' . "'" . $restyui1->pono . "'" . ',' . "'" . $restyui1->itemid . "'" . ');"></span><input type="hidden" name="mypo' . $restyui1->id . '" value="' . $restyui1->pono . '"><input type="hidden" name="myitemid' . $restyui1->id . '" value="' . $restyui1->itemid . '">';

													$html2 = '<a href="javascript:;" class="btn btn-warning btn-xs" onclick="showpopup(' . "'" . $restyui1->pono . "'" . ');">REJECT</a>';



													$htm = "";





													if ($restyui1->potype == '0') {



														$indenttype = "Machine Related Items";

														$rest123 = $this->db->select('b.conversion_weight,b.conversion_unit,b.part as item_name,a.qty,a.price,a.unit,a.itemid')->from('purchase_order_view a')->join('machine_parts_with_picture_view b', 'a.itemid=b.id')->where('b.id', $restyui1->itemid)->get();
														$currentrice = $this->Store_model->getitemprice($restyui1->potype, $restyui1->vendor, $restyui1->itemid);
													} else {

														$indenttype = "General Items";

														$rest123 = $this->db->select('b.item_name,a.qty,a.unit,a.price, a.itemid')->from('purchase_order_view a')->join('house_keeping_items b', 'a.itemid=b.id')->where('b.id', $restyui1->itemid)->get();

														$currentrice = $this->Store_model->getitemprice($restyui1->potype, $restyui1->vendor, $restyui1->itemid);
													}





													if ($rest123->num_rows() > 0) {



														foreach ($rest123->result() as $rest1231)

															$price = $rest1231->price;



														$oldprice = "";

														$query = $this->db->select('price')->from('purchase_order_view')->where('itemid', $rest1231->itemid)->where('approved', '1')->limit('1')->order_by('id', 'desc')->get();

														if ($query->num_rows() > 0) {

															foreach ($query->result() as $oldata);

															$oldprice = $oldata->price;

															$diff = $price - $oldprice;
														} else {


															$oldprice = $currentrice;

															$diff = $price - $oldprice;
														}

														$itemname = strtoupper($rest1231->item_name);

														$qty = $rest1231->qty;
													}



													$pon = "<a href='" . page_url . "Store/" . $polink . "/" . $restyui1->pono . "' target='_blank'>" . $restyui1->pono . "</a>";



													if ($restyui1->deliverytime == 'Other') {

														$delt = $restyui1->otherdeliverytime;
													} else {

														$delt = $restyui1->deliverytime;
													}



													if ($restyui1->pricechange == 1) {

														$pri = "<span style='color:red;font-weight:bold;'>" . $price . "</span>";

														$oldpri = $restyui1->originalprice;
														$prem = "(Price Changed)";
													} else {

														$pri = $price;

														$oldpri = "";
														$prem = "";
													}



													$createdby = ucfirst($restyui1->first_name . " " . $restyui1->last_name);


													if ($prevpo <> $restyui1->pono) {
														$createdby;
													}


													if (trim($diff) > 0) {
														$pricc = "<span style='color:red'>" . $pri . "</span>";
													} else {


														$pricc = "<span style='color:green'>" . $pri . "</span>";
													}

													if ($diff > 0) {
														$b = "background-color:#ff6961;color:white;font-weight:bold;";
													} else if($diff==0) {
														$b = "background-color:#77DD77;color:white;font-weight:bold;";
													}else
													{
														$b = "background-color:#77DD77;color:white;font-weight:bold;";
													}

														$originalunit = $CI->Store_model->getunit($rest1231->unit);
													$convertedunit = $CI->Store_model->getunit($rest1231->conversion_unit);
													if ($rest1231->conversion_unit > 0) {
														$convertedweight = $rest1231->conversion_weight * $qty;
													$convertedweight1 = $rest1231->conversion_weight * $qty . " " . $convertedunit . "(Converted Weight)";
													$qty = $convertedweight;
													} else {
													$convertedweight1 = '';
													$qty=$restyui1->qty;
													}


													if($restyui1->price<>'')
													{
													$totalamt[]=$restyui1->price*$qty;
													}
													
													


													$sta='<input type="checkbox" name="sendtoaccounts111111[]" id="sendtoaccounts1111232311" style="width:20px;height:20px;">';
											


													$html1 .= "
												<tr>
												<td>" . $j . "</td>
												<td>" . $itemname . "</td>
												<td style='width:20px'>" .floatval($qty)."<br/>".$originalunit."<br />".$convertedweight1. "</td>
												<td>" . $restyui1->prno . "/" . $pon . "<br>" . $createdby . "</td>
												<td>" . $jobcard . "</td>
												<td>" . $pricc . "/" . $prem . "</td>
												<td>" . $oldprice . "</td>
												<td style=" . $b . ">" . $diff . "</td>
												<td><div class='text-center'>" . $approved . "</div></td>
												<td><div class='text-center'>" . $sta . "</div></td>
												<td>" . $html2 . "</td>
												</tr>";


												
													$j++;
												}
												$tamt=array_sum($totalamt);
												$html1 .= "
												<tr>
												<td colspan=4></td>
												<td style='background-color:#0404046b; color:#fff;font-weight:600;'>TOTAL</td>
												<td style='background-color:#0404046b; color:#fff;font-weight:600;'>" . round($tamt,2) ."</td>
												<td></td>
												</tr>";
												$html1 .= "</tbody>
												</table>";
												// $html1 .= '<input type="submit" class="btn btn-success pull-right" value="Job Card Approval">
												// </form>';
											}



											//echo "<pre>"; print($html1); exit;

									?>

											<tr id="" style="font-size:18px;">

												<td><?php echo $i; ?></td>
												<td>
													<?php echo $vendor->name; ?><br><strong>Total Amt:-<?php echo $tamt;?></strong></td>

												<td><?php echo $html1; ?></td>

											</tr>

									<?php


											$i++;
										}
									}
									?>

								</tbody>

							</table>
					
						

						</div>
						
						<div class="col-md-12" style="margin-top:10px;">
							<div class="col-md-5"></div>
							<div class="col-md-2">
							<input type="submit" class="btn btn-success btn-sm" value="APPROVE" style="width:100%;color:white;font-weight: bold;">
						</div>
						</div>
						
					</form>
					</div>
				</div>




				<div style="clear: both;"></div>

			

				<div class="col-sm-12">
					<div class="row">
						<div class="col-sm-12">
							<div class="page-title-box" style="display: flex;">
								<h4 class="page-title">PENDING PO FOR APPROVAL(INDENT) OF VALUE - <?php echo $CI->Store_model->moneyFormatIndia(round($indentvalueforunapprove, 0)); ?></h4>
							<!-- 	<div style="    margin-top: 23px;
    position: absolute;
    right: 0;"><a href="<?php echo page_url; ?>Store/approvedallindentpo" class="btn btn-own btn-xs">APPROVED ALL INDENT PO ITEM</a></div> -->
							</div>
						</div>
					</div>


					<div class="col-md-12" style="margin-top:10px;">


<form action="<?php echo page_url;?>Store/approvebulkpos" method="post">
						<div class="card-box table-responsive">

							<table id="example123445" class="table table-bordered manglesh">

								<thead align="center">

									<tr>

										<th width="50">Sr No.</th>

										<th width="200">VENDOR NAME</th>

											<th width="500"><span class="text-center">ITEM</span> <span class="pull-right">Approve All<br/><input type="checkbox" class="approveallbulkjb2" onchange="checkallbulkjb('approveallbulkjb2','approveallind');" style='width:20px;height:20px;'></span></th>

									</tr>

								</thead>

								<tbody>

									<?php
									$res = $this->db->select('a.id as poid,v.name,v.id, v.address,v.payment_terms,v.payment_mode,v.deliverytime,v.otherdeliverytime')->from('vendors v')->join('purchase_order a', 'a.vendor=v.id')->where('a.source', '2')->where('a.approved', 0)->where('a.negotiation',1)->group_by('a.vendor')->get();
									if ($res->num_rows() > 0) {
										$i = 1;


										//echo "<pre>"; print_r($res->result()); exit;
										foreach ($res->result() as $vendor) {
											$venderid = $vendor->id;
											$totalamt=array();
											$rest = $this->db->select('a.urgent,a.price,a.qty,a.vendor,a.pricechange,a.originalprice,a.id,a.prno,a.jobcard,a.source,h.sourceid,a.potype,a.addedOn,a.pono,e.first_name,e.last_name,a.itemid,e.department_id, v.name, v.address,v.payment_terms,v.payment_mode,v.deliverytime,v.otherdeliverytime, a.fright, a.fright_charges, a.packing_charges')

												->from('purchase_order_view a')

												->join('system_users_view e', 'e.user_id=a.addedBy')

												->join('purchase_request_view h', 'h.prno=a.prno')

												->join('vendors_view v', 'a.vendor=v.id')

												->where('a.approved', '0')
												->where('a.source', '2')
												->where('a.negotiation', '1')
												->where('a.vendor', $venderid)
												->order_by('a.addedOn', 'DESC')

												->group_by('a.id')->get();

											if ($rest->num_rows() > 0) {

												$j = 1;
												$html1 = '';
												$prevpo = '';
												
												$html1 .= "<table style='width:100%;' class='table table-bordered manglesh' >
									 			<thead align='center'>
												<tr>
												<th width='50'>Sno</th>
												<th width='200'>Item</th>
												<th width='20'>Qty</th>
												<th width='50'>PR/PO No.</th>
												<th width='100'>Source</th>
												<th width='50'>Per Unit Price</th>
												<th width='50'>Previous Rate</th>
												<th width='50'>Diff</th>
												<th width='50'>Approve</th>
												<th width='50'>Reject</th>
												</tr>
												</thead>
												<tbody>
													";

												foreach ($rest->result() as $restyui1) {

													$jobcard = '';


													if ($restyui1->source == 1) {

														$job = $this->db->select('job_card_no')->from('order_instruments_view')->where('id', $restyui1->jobcard)->get();

														if ($job->num_rows() > 0) {

															foreach ($job->result() as $job1);

															$jobcard = 'Jobcard-' . $job1->job_card_no;
														} else {

															$jobcard = '';
														}

														$polink = "po";

														$mrnlink = "mrn";
													} else if ($restyui1->source == 2) {

														/** Intend **/

														$jobcard = 'INDENT- IND' . $restyui1->sourceid;

														$polink = "generalpo";

														$mrnlink = "generalmrn";
													} else if ($restyui1->source == 3) {

														/** Intend **/

														$jobcard = 'AUTO PR';

														$polink = "po";

														$mrnlink = "mrn";
													} else if ($restyui1->source == 4) {

														/** Intend **/
														$jobcard = 'AUTO PR AGAINST BLOCKAGE';

														$polink = "po";

														$mrnlink = "mrn";
													}


													if ($restyui1->potype == '0') {

														$polink = "po";

														$mrnlink = "mrn";
													} else {

														$polink = "generalpo";

														$mrnlink = "generalmrn";
													}



													$html = '<a href="javascript:;" onclick="approvepo(' . "'" . $restyui1->pono . "'" . ');" class="btn btn-warning btn-xs">APPROVE PO</a>';

													$approved = '<span id="app' . $restyui1->id . '" style="color:green;font-weight:bold;"><input type="checkbox"  class=" approveallind  approval' . $venderid . '2" name="approvedrecord[]" id="approvedrecord' . $restyui1->id . $restyui1->itemid . '" style="width:20px;height:20px;" value="' . $restyui1->id . '" onchange="markasapproved(' . "'" . $restyui1->id . "'" . ',' . "'" . $restyui1->pono . "'" . ',' . "'" . $restyui1->itemid . "'" . ');"></span><input type="hidden" name="mypo' . $restyui1->id . '" value="' . $restyui1->pono . '"><input type="hidden" name="myitemid' . $restyui1->id . '" value="' . $restyui1->itemid . '">';
													$html22 = "";

													//$html22 = '<span id="appaccount' . $restyui1->id . '" style="color:green;font-weight:bold;"><input type="checkbox" name="sendtoaccounts[]" id="sendtoaccounts' . $restyui1->id . '" style="width:20px;height:20px;" onchange="sendtoaccounts(' . $restyui1->id . ');"></span>';


													$html2 = '<a href="javascript:;" class="btn btn-warning btn-xs" onclick="showpopup(' . "'" . $restyui1->pono . "'" . ');">REJECT</a>';



													$htm = "";





													if ($restyui1->potype == '0') {



														$indenttype = "Machine Related Items";

														$rest123 = $this->db->select('	b.conversion_weight,b.conversion_unit,b.part as item_name,a.qty,a.price,a.unit,a.itemid')->from('purchase_order_view a')->join('machine_parts_with_picture_view b', 'a.itemid=b.id')->where('b.id', $restyui1->itemid)->get();
														$currentrice = $this->Store_model->getitemprice($restyui1->potype, $restyui1->vendor, $restyui1->itemid);
													} else {

														$indenttype = "General Items";

														$rest123 = $this->db->select('b.item_name,a.qty,a.unit,a.price, a.itemid')->from('purchase_order_view a')->join('house_keeping_items b', 'a.itemid=b.id')->where('b.id', $restyui1->itemid)->get();

														$currentrice = $this->Store_model->getitemprice($restyui1->potype, $restyui1->vendor, $restyui1->itemid);
													}





													if ($rest123->num_rows() > 0) {



														foreach ($rest123->result() as $rest1231)

															$price = $rest1231->price;



														$oldprice = "";

														$query = $this->db->select('price')->from('purchase_order_view')->where('itemid', $rest1231->itemid)->where('approved', '1')->limit('1')->order_by('id', 'desc')->get();

														if ($query->num_rows() > 0) {

															foreach ($query->result() as $oldata);

															$oldprice = $oldata->price;

															$diff = $price - $oldprice;
														} else {


															$oldprice = $currentrice;

															$diff = $price - $oldprice;
														}

														$itemname = strtoupper($rest1231->item_name);

														$qty = $rest1231->qty;
													}



													$pon = "<a href='" . page_url . "Store/" . $polink . "/" . $restyui1->pono . "' target='_blank'>" . $restyui1->pono . "</a>";



													if ($restyui1->deliverytime == 'Other') {

														$delt = $restyui1->otherdeliverytime;
													} else {

														$delt = $restyui1->deliverytime;
													}



													if ($restyui1->pricechange == 1) {

														$pri = "<span style='color:red;font-weight:bold;'>" . $price . "</span>";

														$oldpri = $restyui1->originalprice;
														$prem = "(Price Changed)";
													} else {

														$pri = $price;

														$oldpri = "";
														$prem = "";
													}



													$createdby = ucfirst($restyui1->first_name . " " . $restyui1->last_name);


													if ($prevpo <> $restyui1->pono) {
														$createdby;
													}


													if (trim($diff) > 0) {
														$pricc = "<span style='color:red'>" . $pri . "</span>";
													} else {


														$pricc = "<span style='color:green'>" . $pri . "</span>";
													}

													if ($diff > 0) {
														$b = "background-color:#ff6961;color:white;font-weight:bold;";
													} else if($diff==0) {
														$b = "background-color:#77DD77;color:white;font-weight:bold;";
													}else
													{
														$b = "background-color:#77DD77;color:white;font-weight:bold;";
													}

													$originalunit = $CI->Store_model->getunit($rest1231->unit);
													$convertedunit = $CI->Store_model->getunit($rest1231->conversion_unit);
													if ($rest1231->conversion_unit > 0) {
														$convertedweight = $rest1231->conversion_weight * $qty;
													$convertedweight1 = $rest1231->conversion_weight * $qty . " " . $convertedunit . "(Converted Weight)";
													$qty = $convertedweight;
													} else {
													$convertedweight1 = '';
													$qty=$restyui1->qty;
													}


													if($restyui1->price<>'')
													{
													$totalamt[]=$restyui1->price*$qty;
													}
													if($restyui1->urgent==1)
													{
														$urgent='<span style="color:red;"><i class="fa fa-flag" aria-hidden="true"></i></span>';
													}else{
														$urgent='';
													}

													$html1 .= "
												<tr>
												<td>" . $j . "<br>".$urgent."</td>
												<td>" . $itemname . "</td>
												<td>" .floatval($qty)."<br/>".$originalunit."<br />".$convertedweight1. "</td>
												<td>" . $restyui1->prno . "/" . $pon . "<br>" . $createdby . "</td>
												<td>" . $jobcard . "</td>
												<td>" . $pricc . "/" . $prem . "</td>
												<td>" . $oldprice . "</td>
												<td style=" . $b . ">" . $diff . "</td>
												<td><div class='text-center'>" . $approved . "</div></td>
												
												<td>" . $html2 . "</td>
												</tr>";

													$j++;
												}

												$tamt=array_sum($totalamt);
												$html1 .= "
												<tr>
												<td colspan=3></td>
												<td style='background-color:#0404046b; color:#fff;font-weight:600;'>TOTAL</td>
												<td style='background-color:#0404046b; color:#fff;font-weight:600;'>" . round($tamt,2) ."</td>
												<td></td>
												</tr>";


												$html1 .= "</tbody>
												</table>";
												
											}



											//echo "<pre>"; print($html1); exit;

									?>

											<tr id="" style="font-size:18px;">

												<td><?php echo $i; ?></td>
												<td>
													<?php echo $vendor->name; ?><br><strong>Total Amt:-<?php echo $tamt; ?></strong></td>

												<td><?php echo $html1; ?></td>

											</tr>

									<?php


											$i++;
										}
									}
									?>

								</tbody>

							</table>

							

						</div>
						<div class="col-md-12" style="margin-top:10px;">
							<div class="col-md-5"></div>
							<div class="col-md-2">
							<input type="submit" class="btn btn-success btn-sm" value="APPROVE" style="width:100%;color:white;font-weight: bold;">
						</div>
						</div>
					</form>

					</div>
				
					<div class="col-sm-12">
						<div class="row">
							<div class="col-sm-12">
								<div class="page-title-box" style="display: flex;">
									<h4 class="page-title">PENDING PO FOR APPROVAL(AUTO PR IMS) OF VALUE - <?php echo $CI->Store_model->moneyFormatIndia(round($imsvalueforunapprove, 0)); ?></h4>
									<!-- <div style="    margin-top: 23px;
    position: absolute;
    right: 0;"><a href="<?php echo page_url; ?>Store/approvedallautoprpo" class="btn btn-own btn-xs">APPROVED ALL AUTO PR IMS PO ITEM</a></div> -->
								</div>
							</div>
						</div>

						<form action="<?php echo page_url;?>Store/approvebulkpos" method="post">

						<div class="card-box table-responsive">

							<table id="example1234456" class="table table-bordered manglesh">

								<thead align="center">

									<tr>

										<th width="50;">Sr No.</th>

										<th width="200">VENDOR NAME</th>

										<th width="500"><span class="text-center">ITEM</span> <span class="pull-right">Approve All<br/><input type="checkbox" class="approveallbulkjb3" onchange="checkallbulkjb('approveallbulkjb3','approveallims');" style='width:20px;height:20px;'></span></th>

									</tr>

								</thead>

								<tbody>

									<?php
									$res = $this->db->select('v.name,v.id, v.address,v.payment_terms,v.payment_mode,v.deliverytime,v.otherdeliverytime')->from('vendors v')->join('purchase_order a', 'a.vendor=v.id')->where_in('a.source', '3,4', false)->where('a.approved', 0)->where('a.negotiation',1)->group_by('a.vendor')->get();
									if ($res->num_rows() > 0) {
										$i = 1;

										foreach ($res->result() as $vendor) {
											$venderid = $vendor->id;
											$totalamt=array();
											$rest = $this->db->select('a.price,a.qty,a.vendor,a.pricechange,a.originalprice,a.id,a.prno,a.jobcard,a.source,h.sourceid,a.potype,a.addedOn,a.pono,e.first_name,e.last_name,a.itemid,e.department_id, v.name, v.address,v.payment_terms,v.payment_mode,v.deliverytime,v.otherdeliverytime, a.fright, a.fright_charges, a.packing_charges')

												->from('purchase_order_view a')

												->join('system_users_view e', 'e.user_id=a.addedBy', 'left')

												->join('purchase_request_view h', 'h.prno=a.prno', 'left')

												->join('vendors_view v', 'a.vendor=v.id', 'left')

												->where('a.approved', '0')
												->where('a.negotiation', '1')
												->where_in('a.source', '3,4', false)
												->where('a.vendor', $venderid)
												->order_by('a.addedOn', 'DESC')

												->group_by('a.id')->get();

											if ($rest->num_rows() > 0) {

												$j = 1;
												$html1 = '';
												$prevpo = '';
												
												$html1 .= "<table style='width:100%;' class='table table-bordered manglesh' >
									 			<thead align='center'>
												<tr>
												<th width='50'>Sno</th>
												<th width='200'>Item</th>
												<th>Qty</th>
												<th>PR/PO No.</th>
												<th>Source</th>
												<th>Per Unit Price</th>
												<th>Previous Rate</th>
												<th>Diff</th>
												<th>Approve</th>
												<th>Reject</th>
												</tr>
												</thead>
												<tbody>
													";

												foreach ($rest->result() as $restyui1) {

													$jobcard = '';


													if ($restyui1->source == 1) {

														$job = $this->db->select('job_card_no')->from('order_instruments_view')->where('id', $restyui1->jobcard)->get();

														if ($job->num_rows() > 0) {

															foreach ($job->result() as $job1);

															$jobcard = 'Jobcard-' . $job1->job_card_no;
														} else {

															$jobcard = '';
														}

														$polink = "po";

														$mrnlink = "mrn";
													} else if ($restyui1->source == 2) {

														/** Intend **/

														$jobcard = 'INDENT- IND' . $restyui1->sourceid;

														$polink = "generalpo";

														$mrnlink = "generalmrn";
													} else if ($restyui1->source == 3) {

														/** Intend **/

														$jobcard = 'AUTO PR';

														$polink = "po";

														$mrnlink = "mrn";
													} else if ($restyui1->source == 4) {

														/** Intend **/
														$jobcard = 'AUTO PR AGAINST BLOCKAGE';

														$polink = "po";

														$mrnlink = "mrn";
													}


													if ($restyui1->potype == '0') {

														$polink = "po";

														$mrnlink = "mrn";
													} else {

														$polink = "generalpo";

														$mrnlink = "generalmrn";
													}



													$html = '<a href="javascript:;" onclick="approvepo(' . "'" . $restyui1->pono . "'" . ');" class="btn btn-warning btn-xs">APPROVE PO</a>';

													$approved = '<span id="app' . $restyui1->id . '" style="color:green;font-weight:bold;"><input type="checkbox"  class="approveallims approval' . $venderid . '3" name="approvedrecord[]" id="approvedrecord' . $restyui1->id . $restyui1->itemid . '" style="width:20px;height:20px;" value="' . $restyui1->id . '" onchange="markasapproved(' . "'" . $restyui1->id . "'" . ',' . "'" . $restyui1->pono . "'" . ',' . "'" . $restyui1->itemid . "'" . ');"></span><input type="hidden" name="mypo' . $restyui1->id . '" value="' . $restyui1->pono . '"><input type="hidden" name="myitemid' . $restyui1->id . '" value="' . $restyui1->itemid . '">';

													$html2 = '<a href="javascript:;" class="btn btn-warning btn-xs" onclick="showpopup(' . "'" . $restyui1->pono . "'" . ');">REJECT</a>';



													$htm = "";





													if ($restyui1->potype == '0') {



														$indenttype = "Machine Related Items";

														$rest123 = $this->db->select('b.conversion_weight,b.conversion_unit,b.part as item_name,a.qty,a.price,a.unit,a.itemid')->from('purchase_order_view a')->join('machine_parts_with_picture_view b', 'a.itemid=b.id')->where('b.id', $restyui1->itemid)->get();
														$currentrice = $this->Store_model->getitemprice($restyui1->potype, $restyui1->vendor, $restyui1->itemid);
													} else {

														$indenttype = "General Items";

														$rest123 = $this->db->select('b.item_name,a.qty,a.unit,a.price, a.itemid')->from('purchase_order_view a')->join('house_keeping_items b', 'a.itemid=b.id')->where('b.id', $restyui1->itemid)->get();

														$currentrice = $this->Store_model->getitemprice($restyui1->potype, $restyui1->vendor, $restyui1->itemid);
													}





													if ($rest123->num_rows() > 0) {



														foreach ($rest123->result() as $rest1231)

															$price = $rest1231->price;



														$oldprice = "";

														$query = $this->db->select('price')->from('purchase_order_view')->where('itemid', $rest1231->itemid)->where('approved', '1')->limit('1')->order_by('id', 'desc')->get();

														if ($query->num_rows() > 0) {

															foreach ($query->result() as $oldata);

															$oldprice = $oldata->price;

															$diff = $price - $oldprice;
														} else {


															$oldprice = $currentrice;

															$diff = $price - $oldprice;
														}

														$itemname = strtoupper($rest1231->item_name);

														$qty = $rest1231->qty;
													}



													$pon = "<a href='" . page_url . "Store/" . $polink . "/" . $restyui1->pono . "' target='_blank'>" . $restyui1->pono . "</a>";



													if ($restyui1->deliverytime == 'Other') {

														$delt = $restyui1->otherdeliverytime;
													} else {

														$delt = $restyui1->deliverytime;
													}



													if ($restyui1->pricechange == 1) {

														$pri = "<span style='color:red;font-weight:bold;'>" . $price . "</span>";

														$oldpri = $restyui1->originalprice;
														$prem = "(Price Changed)";
													} else {

														$pri = $price;

														$oldpri = "";
														$prem = "";
													}



													$createdby = ucfirst($restyui1->first_name . " " . $restyui1->last_name);


													if ($prevpo <> $restyui1->pono) {
														$createdby;
													}


													if (trim($diff) > 0) {
														$pricc = "<span style='color:red'>" . $pri . "</span>";
													} else {


														$pricc = "<span style='color:green'>" . $pri . "</span>";
													}

													if ($diff > 0) {
														$b = "background-color:#ff6961;color:white;font-weight:bold;";
													} else if($diff==0) {
														$b = "background-color:#77DD77;color:white;font-weight:bold;";
													}else
													{
														$b = "background-color:#77DD77;color:white;font-weight:bold;";
													}
													
														$originalunit = $CI->Store_model->getunit($rest1231->unit);
													$convertedunit = $CI->Store_model->getunit($rest1231->conversion_unit);
													if ($rest1231->conversion_unit > 0) {
														$convertedweight = $rest1231->conversion_weight * $qty;
													$convertedweight1 = $rest1231->conversion_weight * $qty . " " . $convertedunit . "(Converted Weight)";
													$qty = $convertedweight;
													} else {
													$convertedweight1 = '';
													$qty=$restyui1->qty;
													}


													if($restyui1->price<>'')
													{
													$totalamt[]=$restyui1->price*$qty;
													}

													$html1 .= "
												<tr>
												<td>" . $j . "</td>
												<td>" . $itemname . "</td>
												<td>" .floatval($qty)."<br/>".$originalunit."<br />".$convertedweight1. "</td>
												<td>" . $restyui1->prno . "/" . $pon . "<br>" . $createdby . "</td>
												<td>" . $jobcard . "</td>
												<td>" . $pricc . "/" . $prem . "</td>
												<td>" . $oldprice . "</td>
												<td style=" . $b . ">" . $diff . "</td>
												<td>" . $approved . "</td>
												<td>" . $html2 . "</td>
												</tr>";

													$j++;
												}
												$tamt=array_sum($totalamt);
												$html1 .= "
												<tr>
												<td colspan=4></td>
												<td style='background-color:#0404046b; color:#fff;font-weight:600;'>TOTAL</td>
												<td style='background-color:#0404046b; color:#fff;font-weight:600;'>" . round($tamt,2) ."</td>
												<td></td>
												</tr>";

												$html1 .= "</tbody>
												</table>";
												
											}



											//echo "<pre>"; print($html1); exit;

									?>

											<tr id="" style="font-size:18px;">

												<td><?php echo $i; ?></td>
												<td>
													<?php echo $vendor->name; ?><br><strong>Total Amt:-<?php echo $tamt; ?></strong></td>

												<td><?php echo $html1; ?></td>

											</tr>

									<?php


											$i++;
										}
									}
									?>

								</tbody>

							</table>

							

						</div>
						<div class="col-md-12" style="margin-top:10px;">
							<div class="col-md-5"></div>
							<div class="col-md-2">
							<input type="submit" class="btn btn-success btn-sm" value="APPROVE" style="width:100%;color:white;font-weight: bold;">
						</div>
						</div>
					</form>
						<div class="col-md-12" style="margin-top:10px;">



						</div>
					</div>
				</div>
			</div>
			<!-- </form> -->

		</div>

		<!-- Page-Title -->




		<!-- Modal -->

		<div id="myModal" class="modal fade" role="dialog">

			<div class="modal-dialog">



				<!-- Modal content-->

				<form action="<?php echo page_url; ?>Reporting/addporemarks" method="post">

					<input type="hidden" name="pono" id="pono" value="">

					<div class="modal-content">

						<div class="modal-header">

							<button type="button" class="close" data-dismiss="modal">&times;</button>

							<h4 class="modal-title">Adding Remarks for PO- <span id="ponum"></span></h4>

						</div>

						<div class="modal-body">

							<div class="row">

								<textarea class="form-control" name="remarks" id="remarks" placeholder="Remarks"></textarea>

							</div>

						</div>

						<div class="modal-footer">

							<input type="submit" class="btn btn-sm btn-success" value="Update">

						</div>

				</form>



			</div>

		</div>

		<!-- Footer -->

		<?php $this->load->view('common/footer'); ?>

		<!-- End Footer -->



		</div> <!-- end container -->

		</div>
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



		<!-- Datatables-->

		<script src="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.js"></script>

		<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.bootstrap.js"></script>

		<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.buttons.min.js"></script>

		<script src="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.js"></script>

		<script src="<?php echo assets_url; ?>plugins/datatables/jszip.min.js"></script>

		<script src="<?php echo assets_url; ?>plugins/datatables/pdfmake.min.js"></script>

		<script src="<?php echo assets_url; ?>plugins/datatables/vfs_fonts.js"></script>

		<script src="<?php echo assets_url; ?>plugins/datatables/buttons.html5.min.js"></script>

		<script src="<?php echo assets_url; ?>plugins/datatables/buttons.print.min.js"></script>

		<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.fixedHeader.min.js"></script>

		<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.keyTable.min.js"></script>

		<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.responsive.min.js"></script>

		<script src="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.js"></script>

		<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.scroller.min.js"></script>

		<script src="https:cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>

		<script src="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.js"></script>

		<!-- Datatable init js -->

		<script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>



		<!-- App js -->

		<script src="<?php echo assets_url; ?>js/jquery.core.js"></script>

		<script src="<?php echo assets_url; ?>js/jquery.app.js"></script>







		<script>
			$(document).ready(function() {



				$('#example123445').dataTable({
					"bSort": false,
					"iDisplayLength": 100
				});
				$('#example1234455').dataTable({
					"bSort": false,
					"iDisplayLength": 100
				});
				$('#example1234456').dataTable({
					"bSort": false,
					"iDisplayLength": 100
				});

				$('#example').dataTable({

					"bProcessing": false,

					"pagination": true,

					fixedHeader: true,


					scrollCollapse: true,

					fixedColumns: {

						leftColumns: 3

					},

					"sAjaxSource": "<?php echo page_url; ?>Reporting/pendingpolistforapproval_changes/<?php echo $this->uri->segment(3); ?>",

					"aoColumns": [

						{
							mData: 'sr_no'
						},

						{
							mData: 'prnumber'
						},

						{
							mData: 'prno'
						},

						{
							mData: 'jobcardno'
						},

						{
							mData: 'vendor'
						},

						{
							mData: 'itemname'
						},

						{
							mData: 'qty'
						},

						{
							mData: 'price'
						},

						{
							mData: 'oldprice'
						},

						{
							mData: 'diff'
						},

						{
							mData: 'createdby'
						},

						{
							mData: 'createdon'
						},

						{
							mData: 'markrecvd'
						},

						{
							mData: 'markrrej'
						}







					]

				});



				$('#example1').dataTable({

					"bProcessing": false,

					"pagination": true,

					fixedHeader: true,

					scrollCollapse: true,

					fixedColumns: {

						leftColumns: 3

					},


					"sAjaxSource": "<?php echo page_url; ?>Reporting/polisthistory1",

					"aoColumns": [

						{
							mData: 'sr_no'
						},

						{
							mData: 'prnumber'
						},

						{
							mData: 'prno'
						},

						{
							mData: 'jobcardno'
						},

						{
							mData: 'vendor'
						},

						{
							mData: 'itemname'
						},

						{
							mData: 'qty'
						},

						{
							mData: 'price'
						},

						{
							mData: 'oldprice'
						},

						{
							mData: 'diff'
						},

						{
							mData: 'createdby'
						},

						{
							mData: 'createdon'
						},
						{
							mData: 'approvredon'
						},

						{
							mData: 'status'
						},

						{
							mData: 'remarks'
						}





					]

				});





			});
		</script>





		<script src="<?php echo assets_url; ?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>

		<script>
			$(document).ready(function() {

				$("#loginForm").on("submit", function() {

					$("#pageloader").fadeIn();

				});
				submit

			});
			document ready



			function approvepo(pono)

			{

				document.location = "<?php echo page_url; ?>Store/approvepo/" + pono;

				return true;



			}



		</script>

		<script>
			function markasapproved(i, j, itemid) {



				if ($("#approvedrecord" + i + itemid).is(":checked"))

				{

					var a = "1";

				} else

				{

					var a = "0";

				}



				$.ajax({

					type: "post",

					url: "<?php echo page_url; ?>Store/checktoapprovepo",

					data: "id=" + i + "&pono=" + j + "&itemid=" + itemid + "&type=" + a,

					success: function(data) {

						if(a==1)
						{
						$("#app" + i).html('Approved');

						$('#' + i).css({
							'background-color': '#CCFDC1'
						});

					}

					}

				});

			}





			function sendmail(pono)

			{

				if ($('#sendmails').is(":checked"))

				{

					$.ajax({

						type: "post",

						url: "<?php echo page_url; ?>Store/sendemailtosupplier",

						data: "pono=" + pono,

						success: function(data) {

							alert(data);
							$("#mail" + pono).css('color', 'green');
							$("#mail" + pono).css('font-weight', 'bold');
							$("#mail" + pono).html('Mail Send');

						}

					});

				}



			}



			function checkalldata(id, j) {

				if ($(".appdata" + id + j).is(":checked")) {
					$('.approval' + id + j).prop('checked', true);

				} else {
					$('.approval' + id + j).prop('checked', false);

				}

			}


			function checkforsingleboxapproval() {

				var checklen = $('[name="approvedrecord[]"]:checked').length;
				if (checklen > 0) {
					return true;
				} else {
					alert('At least one item selection is mandatory');
					return false;

				}

			}


			function sendtoaccounts(prid) {

				$.ajax({

					type: "post",

					url: "<?php echo page_url; ?>Store/senddirectlytoaccounts",

					data: "id=" + prid,

					success: function(data) {

						$("#appaccount" + prid).text('Approved and send to accounts');

						$('#' + prid).css({
							'background-color': '#CCFDC1'
						});

					}

				});


			}
			
			function showpopup(pono)

			{



				$("#myModal").modal('show');

				$("#ponum").text(pono);

				$("#pono").val(pono);





			}

			function checkallbulkjb(parentcheck,allcheckbox)
			{

				if ($('.'+parentcheck).is(":checked")) {
					
					$('.'+allcheckbox).prop('checked', true);

				} else {
					$('.'+allcheckbox).prop('checked', false);

				}

			}
		</script>

	</body>

	</html>