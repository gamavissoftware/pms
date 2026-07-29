<?php
ob_start();
set_time_limit(0);
ini_set("memory_limit", "256M");
define('stamp_url', '/var/www/hongyijig.in/assets/tooling_po_stamp/');
define('image_url', '/var/www/hongyijig.in/assets/client_bom_data/part_image/');
define('redirecturl', 'https://hongyijig.in/index.php/Proposal/preview_tooling_po/');
include('mysqlconfig.php');
$lead_id = $_GET['lead_id'];
$supplier = $_GET['supplier_id'];
$option_id = $_GET['option_id'];

$sql22 = "SELECT supplier_id FROM suppliers WHERE supplier_id=$supplier AND supplier_location=101";
$result22 = mysqli_query($con, $sql22);

if(mysqli_num_rows($result22) > 0) {
	$in_word = 'INR';
	$symbol = '₹';
	$country_flag = 2;
} else {
	$in_word = 'CNY';
	$symbol = '¥';
	$country_flag = 1;
}

$sql13 = "SELECT id FROM bom_moulds_detail WHERE lead_id = $lead_id";
$result13 = mysqli_query($con, $sql13);
$total_moulds = mysqli_num_rows($result13);


/** QUERY ENDS **/
$query = "SELECT a.company_name,a.company_registration_id, a.company_registered_address, b.company_spokes_person_name, b.spokes_person_mobile, b.spokes_person_emailid, b.spokes_person_wechat, c.beneficiary_name, c.beneficiary_address, c.beneficiary_account_no, c.bank_name, c.bank_address, c.swift_code, c.bank_code, c.intermediary_bank_name, c.intermediary_bank_address, c.intermediary_swift_code, d.company_legal_representative_name, d.mobile_number, d.email_id, d.wechat_id FROM suppliers a JOIN supplier_spokes_person_detail b ON b.supplier_id=a.supplier_id JOIN supplier_bank_detail c ON c.supplier_id=a.supplier_id JOIN supplier_legal_representative d ON d.supplier_id=a.supplier_id WHERE a.supplier_id=$supplier";
// echo $sql;exit;
$resultq = mysqli_query($con,$query);

$company_name = '';
$company_registered_address = '';
$company_spokes_person_name = '';
$spokes_person_mobile = '';
$spokes_person_emailid = '';
$spokes_person_wechat = '';
$company_registration_id = '';
$beneficiary_name = '';
$beneficiary_address = '';
$beneficiary_account_no = '';
$bank_name = '';
$bank_address = '';
$swift_code = '';
$bank_code = '';
$intermediary_bank_name = '';
$intermediary_bank_address = '';
$intermediary_swift_code = '';
$company_legal_representative_name = '';
$mobile_number = '';
$email_id = '';
$wechat_id = '';
if(mysqli_num_rows($resultq) > 0) {
 	$rowq = mysqli_fetch_array($resultq);
 	$company_name = $rowq['company_name'];
 	$supplier_company_name = $rowq['company_name'];
 	$company_registered_address = $rowq['company_registered_address'];
 	$company_spokes_person_name = $rowq['company_spokes_person_name'];
 	$spokes_person_mobile = $rowq['spokes_person_mobile'];
 	$spokes_person_emailid = $rowq['spokes_person_emailid'];
 	$spokes_person_wechat = $rowq['spokes_person_wechat'];
 	$company_registration_id = $rowq['company_registration_id'];
 	$beneficiary_name = $rowq['beneficiary_name'];
 	$beneficiary_address = $rowq['beneficiary_address'];
 	$beneficiary_account_no = $rowq['beneficiary_account_no'];
 	$bank_name = $rowq['bank_name'];
 	$bank_address = $rowq['bank_address'];
 	$swift_code = $rowq['swift_code'];
 	$bank_code = $rowq['bank_code'];
 	$intermediary_bank_name = $rowq['intermediary_bank_name'];
 	$intermediary_bank_address = $rowq['intermediary_bank_address'];
 	$intermediary_swift_code = $rowq['intermediary_swift_code'];
 	$company_legal_representative_name = $rowq['company_legal_representative_name'];
 	$mobile_number = $rowq['mobile_number'];
 	$email_id = $rowq['email_id'];
 	$wechat_id = $rowq['wechat_id'];
 }

 $additionalmould=array();
	$sql101 = "SELECT mould_id FROM bom_mouldwise_options WHERE id = $option_id";
		$results101 = mysqli_query($con, $sql101);
		if(mysqli_num_rows($results101)>0)
		{
			$datas101 = mysqli_fetch_array($results101);

			$additionalmould[]=$datas101['mould_id'];

		}


$sqlmm = "SELECT id FROM bom_moulds_detail WHERE lead_id = $lead_id";
$resultsmm = mysqli_query($con, $sqlmm);
$priceindollar = array();
if (mysqli_num_rows($resultsmm) > 0) {
while ($datasmm = mysqli_fetch_array($resultsmm))
{
	$mouldidforprice=$datasmm['id'];

if(in_array($mouldidforprice,$additionalmould))
		{
		$optionforprice=$option_id;
		}else
		{
		$optionforprice=0;
		}
	$sql10 = "SELECT price_in_dollar FROM sourcing_quotation WHERE lead_id=$lead_id AND mould_id=$mouldidforprice AND supplier_id=$supplier AND option_id=$optionforprice";
	$results10 = mysqli_query($con, $sql10);
	
	$datas10 = mysqli_fetch_array($results10);
	$priceindollar[] = $datas10['price_in_dollar'];
}

}

 $totalpriceindollar = array_sum($priceindollar);
// echo $totalpriceindollar;exit;

$sql11 = "SELECT final_neg_price,addedOn,po_approved FROM supplier_final_cost WHERE lead_id=$lead_id AND supplier_id=$supplier";
$results11 = mysqli_query($con, $sql11);
$datas11 = mysqli_fetch_array($results11);
$po_approved = $datas11['po_approved'];
$neg_price = $datas11['final_neg_price'];
$addedOn=$datas11['addedOn'];
$addedon = date('l, F d , Y', strtotime($addedOn));
$total_price = $totalpriceindollar - $neg_price;
$percentage = ($total_price * 100)/$totalpriceindollar;
$round_percentage = $percentage;
 function getCurrencyCode($grandtot)
{
	/* CURRENCY CODE **/
	$number = floatval($grandtot);
	$decimal = round($number - ($no = floor($number)), 2) * 100;
	$hundred = null;
	$digits_length = strlen($no);
	$i = 0;
	$str = array();
	$words = array(
		0 => '', 1 => 'one', 2 => 'two',
		3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
		7 => 'seven', 8 => 'eight', 9 => 'nine',
		10 => 'ten', 11 => 'eleven', 12 => 'twelve',
		13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
		16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
		19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
		40 => 'forty', 50 => 'fifty', 60 => 'sixty',
		70 => 'seventy', 80 => 'eighty', 90 => 'ninety'
	);
	$digits = array('', 'hundred', 'thousand', 'lakh', 'crore');

	while ($i < $digits_length) {
		$divider = ($i == 2) ? 10 : 100;
		$number = floor($no % $divider);
		$no = floor($no / $divider);
		$i += $divider == 10 ? 1 : 2;
		if ($number) {
			$plural = (($counter = count($str)) && $number > 9) ? 's' : null;
			$hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
			$str[] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
		} else $str[] = null;
	}

	$rupees = implode('', array_reverse($str));
	$paise = '';

	if ($decimal) {
		$paise = 'and ';
		$decimal_length = strlen($decimal);

		if ($decimal_length == 2) {
			if ($decimal >= 20) {
				$dc = $decimal % 10;
				$td = $decimal - $dc;
				$ps = ($dc == 0) ? '' : '-' . $words[$dc];

				$paise .= $words[$td] . $ps;
			} else {
				$paise .= $words[$decimal];
			}
		} else {
			$paise .= $words[$decimal % 10];
		}

		$paise .= ' paise';
	}

	return $rupees;
	$words = $rupees . 'rupees ' . $paise;

	/* END **/
}
 


 /** BOM SALES INFO **/

$query = "SELECT a.delivery_type FROM bom_pi_sales_project_info a WHERE a.lead_id = $lead_id";
// echo $query;exit;
$result = mysqli_query($con, $query);
$data = mysqli_fetch_array($result) ;


 /** END **/

require_once('tcpdf/tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);



// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH);
$pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}


$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('dejavusans', '', 10, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// set text shadow effect
$pdf->setTextShadow(array('enabled'=>false, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Set some content to print


$delivery_type = $data['delivery_type'];

if ($delivery_type == 1) {
	$company_name = 'HONGYI <span style="color:orange;">JIG </span>RAPID TECHNOLOGIES CO., LTD.';
	$company_address = 'Unit H 1/F Mau Lam Comm Bldg16-18 Mau Lam St Jordan Kln, HK';
	$logo = '<img src="/var/www/hongyijig.in/pdf/internallogo.jpeg" width="200px">';
} else if ($delivery_type == 2) {
	$company_name = 'HONGYI <span style="color:orange;">JIG</span> RAPID TECHNOLOGIES';
	$company_address = '15/1, 2nd Floor, Rama Road Industrial Area Delhi 110015 (INDIA)';
	$logo = '<img src="/var/www/hongyijig.in/pdf/logo.jpeg" width="200px">';
} else {
	$company_name = '';
	$company_address = '';
	$logo = '';
}

/** quert paet **/
$html=''; 
$html.='<table style="padding:3px; font-size:9px;">
<tr><td>' . $logo . '</td></tr>
<tr><td>' . $company_name . '</td></tr>
<tr><td>' . $company_address . '</td></tr>
<tr><td style=""></td></tr>
</table>
<br><br>
<table style="padding:3px; width:100%;">

<tr>
<td style="text-align:center; background-color:lightgrey; font-weight:bold;">PURCHASE ORDER
</td>
</tr>
</table>
<p style="font-weight:bold; font-size:9px;">To,</p>
<table style="width:100%; padding:3px; font-size:9px;">

<tr>
<td>
<table border="1" style="padding:3px;">
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">COMPANY NAME</td>
<td>'.$supplier_company_name.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">COMPANY ADDRESS</td>
<td>'.$company_registered_address.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">CONTACT PERSON</td>
<td>'.$company_spokes_person_name.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">MOBILE NUMBER</td>
<td>'.$spokes_person_mobile.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">EMAIL ID</td>
<td>'.$spokes_person_emailid.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">WE CHAT ID</td>
<td>'.$spokes_person_wechat.'</td>
</tr>
</table>

</td>

<td>
<table border="1" style="padding:3px;">
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">LOT. NO-1</td>
<td></td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">TOTAL NO OF MOULDS </td>
<td>'.$total_moulds.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">PURCHASE ORDER NUMBER</td>
<td>QN-0071-0001</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">OWNERS NAME</td>
<td>'.$company_legal_representative_name.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">OWNERS MOBILE NUMBER</td>
<td>'.$mobile_number.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">EMAIL ID</td>
<td>'.$email_id.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">WECHAT ID</td>
<td>'.$wechat_id.'</td>
</tr>
</table>


</td>
</tr>
</table>

<br><br>
<table style="width:100%; padding:3px; font-size:9px;">
<tr>
<td><table style="width:100%; padding:3px;" border="1">
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">BUSINESS LICENCE NO.</td>
<td>'.$company_registration_id.'</td>
</tr>
</table></td>
<td><table style="width:100%; padding:3px;" border="1">
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">DATE</td>
<td>'.$addedon.'
</td>
</tr>
</table></td>
</tr>
</table>

<p style=" font-size:9px;">Dear <span>'.$company_spokes_person_name.'</span><br>We are pleased to entrust you with purchase order for the product/services mention below subject to terms and conditions given below
</p>

<table style="width:100%; padding:3px; font-size:9px;">
<tr>
<td style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black;">LIST OF MOULDS </td>
</tr>
</table>
<table style="width:100%; padding:3px; font-size:9px;">
<tr>
<th style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black; width:30px;">Moulds</th>
<th style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black; width:85px;">PART NAME</th>
<th style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black;">PART CFM </th>
<th style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black;">PART PICTURE</th>
<th style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black;"> PART WEIGHT</th>
<th style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black; width:50px;">CAVITY </th>
<th style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black;width:100px;">TYPE OF RUNNER
</th>
<th style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black; width:50px;" >CYCLE TIME</th>
<th style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black;">TOOL STEEL </th>
<th style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black;">PRICE IN '.$in_word.'
</th>
</tr>';

$sql = "SELECT id, type FROM bom_moulds_detail WHERE lead_id = $lead_id";
$results = mysqli_query($con, $sql);

if (mysqli_num_rows($results) > 0) {
	$mld = 1;

	// $ase=array();
	$mouldcheck = array();
	$mould_weight = array();
	$priceafterpercentage = array();


		

	while ($datas = mysqli_fetch_array($results)) {
		$mould_id = $datas['id'];

		if(in_array($mould_id,$additionalmould))
		{
		$option=$option_id;
		}else
		{
		$option=0;

		}
/** CHECK FOR ADDITIONAL OPTION **/

// echo $round_percentage; exit;
		$sql10 = "SELECT price_in_dollar FROM sourcing_quotation WHERE mould_id = $mould_id AND lead_id=$lead_id AND option_id=$option AND supplier_id=$supplier";
		// echo $sql10;exit;
		$results10 = mysqli_query($con, $sql10);
		$datas10 = mysqli_fetch_array($results10);
		$price = $datas10['price_in_dollar'];

		$price_after_cal = ($price * $round_percentage)/100;
		//echo $price."<br/>".$price_after_cal; exit;
		$price_for_mould = $price - $price_after_cal;


		$sql9 = "SELECT a.remarks, b.material_grade as tool_cavity, c.material_grade as mould_base, d.material_grade as core_cavity FROM bom_moulds_detail a LEFT JOIN steel_type b ON a.mouldcavitysteel=b.id LEFT JOIN steel_type c ON a.mouldbasesteel=c.id LEFT JOIN steel_type d ON a.mouldcoresteel=d.id WHERE a.id = $mould_id AND lead_id = $lead_id";
		$result9 = mysqli_query($con, $sql9);
		$data9 = mysqli_fetch_array($result9);

		$steelname = $data9['mould_base'];

		$sql1 = "SELECT id, mould_id, name, image, partfinish, partlength, partheight, partwidth, cavity FROM bom_part_details WHERE lead_id = $lead_id AND mould_id = $mould_id";
		$result1 = mysqli_query($con, $sql1);
		$partscount = mysqli_num_rows($result1);
		$i = 1;


		while ($data1 = mysqli_fetch_array($result1)) {
			$part_id = $data1['id'];
			$sql2 = "SELECT a.color,a.colortype,a.pantonecode,a.actual_shrinkage,a.cycletime, b.colourname, c.partname FROM  bom_mould_wise_part_detail a LEFT JOIN part_colour b ON a.color=b.id LEFT JOIN part_master c ON c.id=a.part_material WHERE a.mouldid = $mould_id";
			$result2 = mysqli_query($con, $sql2);
			$data2 = mysqli_fetch_array($result2);
			
			$color = '';
			if ($data2['colortype'] == 1) {
				$color = $data2['colourname'];
			} else {
				$color = "Special Color-" . $data2['pantonecode'];
			}

			$cycle_time = $data2['cycletime'];

			$type = $datas['type'];

			if ($type == 1) {
				$mould_type = 'Single';
			} else if ($type == 2) {
				$mould_type = 'Multi';
			} else if ($type == 3) {
				$mould_type = 'Family';
			}

			if ($data1['partlength'] == '') {
				$part_length = 0;
			} else {
				$part_length = $data1['partlength'];
			}
			if ($data1['partwidth'] == '') {
				$part_width = 0;
			} else {
				$part_width = $data1['partwidth'];
			}

			if ($data1['partheight'] == '') {
				$part_height = 0;
			} else {
				$part_height = $data1['partheight'];
			}

			if ($data1['partfinish'] == 1) {
				$fin = "High Gloss Mirror";
			} else if ($data1['partfinish'] == 2) {
				$fin = "High Gloss";
			} else if ($data1['partfinish'] == 3) {
				$fin = "Normal Polish";
			} else if ($data1['partfinish'] == 4) {
				$fin = "Texture Matt Finish (" . $data['matcode'] . ")";
			}

			$cavity = $data1['cavity'];

			$material = $data2['partname'] . $data2['actual_shrinkage'];

			$sql3 = "SELECT d.id as runner_id, b.name as core_name, a.runner_type,e.name as gatingname,a.gating_type,a.tips,c.name as cavity_name, a.mould_base_steel,a.core_steel,a.cavity_steel, a.insertmoulding, a.benchmarkweight, d.name as runner_name, f.name as runner_brand FROM bom_part_additional_details a LEFT JOIN core_steel b ON a.core_steel=b.id LEFT JOIN cavity_steel c ON c.id=a.cavity_steel LEFT JOIN runner_type d ON d.id=a.runner_type  LEFT JOIN gating_type e ON a.gating_type=e.id LEFT JOIN hot_runner_brand f ON f.id=a.runnerbrand WHERE a.partdetailid = $part_id";
			//echo $sql3;exit;
			$results3 = mysqli_query($con, $sql3);
			$datas3 = mysqli_fetch_array($results3);


			if ($datas3['runner_type'] == 1) {
				$runname = "Hot Runner";
				$runner_id = $datas3['runner_id'];
				$runner_brand = $datas3['runner_brand'];
				$gating = $datas3['gatingname'];
				$tips = $datas3['tips'];
			} else if ($datas3['runner_type'] == 2) {
				$runname = "Cold Runner";
				$runner_id = $datas3['runner_id'];
				$runner_brand = $datas3['runner_brand'];
				$gating = $datas3['gatingname'];
				$tips = '';
			} else {
				$runname = "";
				$runner_id = '';
				$runner_brand = "";
				$gating = '';
				$tips = '';
			}

			if($data3['benchmarkweight'] == '') {
				$benchmarkweight = NA;
			} else {
				$benchmarkweight = $data3['benchmarkweight']." gm";
			}

						/** CHECK IF RUNNER OR STEEL HAS BEEN DIFFERENT **/

			if ($option_id <> 0) {
				$checkforother = "SELECT type,runner_type,runner_brand,gating,tips,steelbrand,steel FROM bom_mouldwise_options WHERE lead_id='$lead_id' AND mould_id='$mould_id'";
				$resultsother = mysqli_query($con, $checkforother);
				$numrowother = mysqli_num_rows($resultsother);
				if ($numrowother > 0) {
					$otherchanged = mysqli_fetch_array($resultsother);
					if ($otherchanged['type'] == 1) {
						$gateid = $otherchanged['gating'];
						if ($otherchanged['runner_type'] == 1) {
							$runname = "HOT RUNNER";
							$tips = $otherchanged['tips'];
						} else {
							$runname = "COLD RUNNER";
							$tips = '';
						}

						$gate = "SELECT name FROM gating_type WHERE id='$gateid'";
						$forgatether = mysqli_query($con, $gate);
						$gatename = mysqli_fetch_array($forgatether);

						$gating = $gatename['name'];
					} else if ($otherchanged['type'] == 2) {
						/** FOR STEEL AND BRAND **/

						$steelbrand = $otherchanged['steelbrand'];
						$steel = $otherchanged['steel'];

						$steelbr = "SELECT manufacturer_name FROM manufacturer WHERE id='$steelbrand'";
						$forsteelbther = mysqli_query($con, $steelbr);
						$steelbname = mysqli_fetch_array($forsteelbther);

						$manufacturername = $steelbname['manufacturer_name'];


						$steelbr = "SELECT material_grade FROM steel_type WHERE id='$steel'";
						$forsteelbther = mysqli_query($con, $steelbr);
						$steelbname = mysqli_fetch_array($forsteelbther);
						$steelname = $steelbname['material_grade'];
					} else {

						$steelbrand = $otherchanged['steelbrand'];
						$steel = $otherchanged['steel'];

						$steelbr = "SELECT manufacturer_name FROM manufacturer WHERE id='$steelbrand'";
						$forsteelbther = mysqli_query($con, $steelbr);
						$steelbname = mysqli_fetch_array($forsteelbther);

						$manufacturername = $steelbname['manufacturer_name'];


						$steelbr = "SELECT material_grade FROM steel_type WHERE id='$steel'";
						$forsteelbther = mysqli_query($con, $steelbr);
						$steelbname = mysqli_fetch_array($forsteelbther);
						$steelname = $steelbname['material_grade'];


						$gateid = $otherchanged['gating'];
						if ($otherchanged['runner_type'] == 1) {
							$runname = "HOT RUNNER";
							$tips = $otherchanged['tips'];
						} else {
							$runname = "COLD RUNNER";
							$tips = '';
						}

						$gate = "SELECT name FROM gating_type WHERE id='$gateid'";
						$forgatether = mysqli_query($con, $gate);
						$gatename = mysqli_fetch_array($forgatether);
						$gating = $gatename['name'];

						if ($otherchanged['runner_type'] == 1) {
							$runname = "HOT RUNNER";
							$tips = $otherchanged['tips'];
						} else {
							$runname = "COLD RUNNER";
							$tips = '';
						}
					}
				}
				//$datasother = mysqli_fetch_array($resultsother);

			}

			if(in_array($mould_id,$additionalmould)) {
				$checkforother = "SELECT a.type, a.runner_type, a.steelbrand, a.steel, b.name as gating_name, c.name as runner_brand FROM bom_mouldwise_options a JOIN gating_type b ON b.id=a.gating JOIN hot_runner_brand c ON c.id=a.runner_brand WHERE id=$option_id";
				$resultsother = mysqli_query($con, $checkforother);
				$numrowother = mysqli_num_rows($resultsother);

				if ($numrowother > 0) {
					$otherchanged = mysqli_fetch_array($resultsother);
					if ($otherchanged['type'] == 1) {
						$gating = $otherchanged['gating_name'];
						$runner_brand = $otherchanged['runner_brand'];

							if ($otherchanged['runner_type'] == 1) {
								$runname = "HOT RUNNER";
								$tips = $otherchanged['tips'];
							} else {
								$runname = "COLD RUNNER";
								$tips = '';
							}
					} else if ($otherchanged['type'] == 2) {
						/** FOR STEEL AND BRAND **/

						$steelbrand = $otherchanged['steelbrand'];
						$steel = $otherchanged['steel'];

						$steelbr = "SELECT manufacturer_name FROM manufacturer WHERE id=$steelbrand";
						$forsteelbther = mysqli_query($con, $steelbr);
						$steelbname = mysqli_fetch_array($forsteelbther);

						$manufacturername = $steelbname['manufacturer_name'];


						$steelbr = "SELECT material_grade FROM steel_type WHERE id=$steel";
						$forsteelbther = mysqli_query($con, $steelbr);
						$steelbname = mysqli_fetch_array($forsteelbther);
						$steelname = $steelbname['material_grade'];
					} else {

						$steelbrand = $otherchanged['steelbrand'];
						$steel = $otherchanged['steel'];

						$steelbr = "SELECT manufacturer_name FROM manufacturer WHERE id='$steelbrand'";
						$forsteelbther = mysqli_query($con, $steelbr);
						$steelbname = mysqli_fetch_array($forsteelbther);

						$manufacturername = $steelbname['manufacturer_name'];


						$steelbr = "SELECT material_grade FROM steel_type WHERE id='$steel'";
						$forsteelbther = mysqli_query($con, $steelbr);
						$steelbname = mysqli_fetch_array($forsteelbther);
						$steelname = $steelbname['material_grade'];


						$gating = $otherchanged['gating_name'];
						$runner_brand = $otherchanged['runner_brand'];
						if ($otherchanged['runner_type'] == 1) {
							$runname = "HOT RUNNER";
							$tips = $otherchanged['tips'];
						} else {
							$runname = "COLD RUNNER";
							$tips = '';
						}
					}

				}
						
			}

			
			

$html .= '<tr>';
	if (!in_array($mould_id, $mouldcheck)) {
		$html .= '<td rowspan="' . $partscount . '" style="text-align:center;  border:1px solid black;"><strong>MOULD ' . $mld . '<br>' . $mould_type .'</strong></td>';
	}
$html .= '<td style="text-align:center;  border:1px solid black;">'.$data1['name'].'</td>
<td style="text-align:center;  border:1px solid black;"><strong>Part Colour</strong><br/>' . $color . '<br/><br/>
			<strong>Surface Finish</strong><br/>' . $fin . '<br/><br/>
			<strong>Material</strong><br/>' . $material . '</td>';
$html .= '<td style="text-align:center;  border:1px solid black;"><img src="' . image_url . $data1['image'] . '" width="100px"></td>
<td style="text-align:center;  border:1px solid black;">'.$benchmarkweight.'</td>
<td style="text-align:center;  border:1px solid black;">'.$cavity.'</td>';

if (!in_array($mould_id, $mouldcheck)) {

if($runner_brand != '') {
	$typess = '<br><strong>Runner Brand-</strong>'.$runner_brand.'<br><strong>Gating Type-</strong>'.$gating.'<br><strong>Tips-'.$tips.'</strong>';
} else {
	$typess = '<br><strong>Gating Type-</strong>'.$gating;
}
$html .= '<td rowspan="'.$partscount.'" style="text-align:center;  border:1px solid black;width:100px;"><strong>Runner Name-</strong>'.$runname.$typess.'</td>

<td rowspan="'.$partscount.'" style="text-align:center;  border:1px solid black;">'.$cycle_time.'</td>
<td rowspan="'.$partscount.'" style="text-align:center;  border:1px solid black;"><strong>Tool Steel Core</strong><br>'.$data9['tool_cavity'].'<br><strong>Tool Steel Cavity</strong><br>'.$data9['core_cavity'].'<br><strong>Tool Steel Mould Base</strong><br>'.$steelname.'</td>
<td rowspan="'.$partscount.'" style="text-align:center;  border:1px solid black;">'.round($price_for_mould, 2).'</td>';
}
$html .= '</tr>';

	if ($partscount > 1) {
				$mouldcheck[] = $mould_id;
			}
			$i++;
		}
		$mld++;
		 $priceafterpercentage[] = $price_for_mould;
	}
}

 $totalpriceindollaraftercal = array_sum($priceafterpercentage);

// $sql11 = "SELECT final_neg_price FROM supplier_final_cost WHERE lead_id=$lead_id AND supplier_id=$supplier_id";
// $results11 = mysqli_query($con, $sql11);
// $datas11 = mysqli_fetch_array($results11);
// $neg_price = $datas11['final_neg_price'];

// $total_price = $totalpriceindollar - $neg_price;
// $percentage = ($total_price * 100)/$totalpriceindollar;

$html .= '<tr>
<td style="text-align:center;  border:1px solid black; font-weight:bold; background-color:lightgrey;" colspan="9">TOTAL </td>
<td style="text-align:center;  border:1px solid black; background-color:lightgrey;"> '.$symbol.$totalpriceindollaraftercal.'</td>

</tr>
<tr>
<td style="text-align:center;  border:1px solid black; font-weight:bold; background-color:lightgrey;" colspan="2" >AMOUNT IN WORDS </td>
<td style=" border:1px solid black; " colspan="8"> ' .$in_word.' '. ucwords(getCurrencyCode($totalpriceindollaraftercal)) . ' Only
</td>

</tr>
</table>

<br><br>
<table style=" padding:3px; font-size:10px;">
<tr style="background-color:lightgrey;">
<td style="text-align:center; font-weight:bold;">PAYMENT TERMS  & SUPPLIERS BANK DETAILS 
</td>
</tr>
</table>

<br><br>

<table border="1"  style="width:100%; text-align:center;">
<tr style="background-color:lightgrey;">
<td style="text-align:center;">S.NO</td>
<td style="text-align:center;">PAYMENT STAGE
</td>
<td style="text-align:center;">% AGE 
</td>
<td style="text-align:center;">AMOUNT 
</td>
</tr>';

$total_percentage=array();
$total_percentage[]=0;
$sql12 = "SELECT payment_division, stage, percentage FROM supplier_payment_division WHERE lead_id=$lead_id AND supplier_id=$supplier";;
$results12 = mysqli_query($con, $sql12);
if(mysqli_num_rows($results12) > 0) {
$j = 1;
$show_details = array();
while($datas12 = mysqli_fetch_array($results12)) {
	$payment_division = $datas12['payment_division'];
	$stage = $datas12['stage'];
	$percentage = $datas12['percentage'];
	$amount = ($totalpriceindollaraftercal * $percentage)/100;
	$total_percentage[]=$amount;
	$html .= '<tr>
    <td style="border:1px solid black; text-align:center;">'.$j.'</td>
	<td style="border:1px solid black; text-align:left;">'.$stage.'</td>
	<td style="border:1px solid black; text-align:center;">'.floatval($percentage).'%</td>
	<td style="border:1px solid black; text-align:center;">'.$symbol.$amount.'</td>
	</tr>';
	$j++;

	$show_details[] = $payment_division.' @ '.floatval($percentage).'% '.$stage;
}

}

$payment_terms = implode(', ', $show_details);
$html .= '<tr>
<td colspan="3" style="background-color:lightgrey; text-align:right;">Total</td>
<td>'.$symbol."".array_sum($total_percentage).'</td>
</tr>
</table>
<br/><br/>';

$html.='
<table border="1"  style="padding:3px; width:100%; text-align:center;">
<tr>
<td style="text-align:right; font-weight:bold; background-color:lightgrey; ">Beneficiary Name</td>
<td>'.$beneficiary_name.'
</td>
</tr>

<tr>
<td style="text-align:right; font-weight:bold; background-color:lightgrey; ">Beneficiary Address</td>
<td>'.$beneficiary_address.' 
</td>
</tr>

<tr>
<td style="text-align:right; font-weight:bold; background-color:lightgrey; ">Beneficiary Account no.</td>
<td>'.$beneficiary_account_no.'
</td>
</tr>

<tr>
<td style="text-align:right; font-weight:bold; background-color:lightgrey; ">Beneficiary Bank Name</td>
<td>'.$bank_name.'
</td>
</tr>

<tr>
<td style="text-align:right; font-weight:bold; background-color:lightgrey; ">Beneficiary Bank Address</td>
<td>'.$bank_address.'
</td>
</tr>

<tr>
<td style="text-align:right; font-weight:bold; background-color:lightgrey; "> Beneficiary Swift Code</td>
<td>'.$swift_code.'
</td>
</tr>

<tr>
<td style="text-align:right; font-weight:bold; background-color:lightgrey; ">Beneficiary Bank Code</td>
<td>'.$bank_code.'

</td>
</tr>

<tr>
<td style="text-align:right; font-weight:bold; background-color:lightgrey; ">Intermediatery Bank Name</td>
<td>'.$intermediary_bank_name.'
</td>
</tr>

<tr>
<td style="text-align:right; font-weight:bold; background-color:lightgrey; ">Intermediatery Bank Address</td>
<td>'.$intermediary_bank_address.'

</td>
</tr>

<tr>
<td style="text-align:right; font-weight:bold; background-color:lightgrey; ">Intermediatery Bank Swift Code</td>
<td>'.$intermediary_swift_code.'
</td>
</tr>
</table>
<br><br>
<table style="width:100%; padding:3px; font-size:9px;">
<tr>
<td style="text-align:center; font-weight:bold; background-color:lightgrey; ">LEAD TIME MILESTONES </td>

</tr>
</table>



<br><br>



		<table rules="all" cellpadding="3" style="width:100%; text-align:center;">

			<tbody>
			<tr>
			<td style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black;">S. NO. </td>
			<td style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black;">DELIVERY MILE STONES</td>
			<td style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black;">DATE </td>
			</tr>';

			$sql13 = "SELECT delivery_milestones, delivery_date FROM supplier_lead_milestones WHERE lead_id=$lead_id AND supplier_id=$supplier";
			$results13 = mysqli_query($con, $sql13);
			$k = 1;
			while($datas13 = mysqli_fetch_array($results13)) {
				$delivery_milestones = $datas13['delivery_milestones'];

				if ($delivery_milestones == 'B0') {
					$milestones = 'PART DESIGN FEASIBILITY FEEDBACK';
				} else if ($delivery_milestones == 'B1') {
					$milestones = 'DFM/MOULD FLOW & TOOL DESIGNS';
				} else if ($delivery_milestones == 'B2') {
					$milestones = 'TOOLING TILL T-0';
				} else if ($delivery_milestones == 'B3') {
					$milestones = 'FINAL MOULD DELIVERY WITH BATCH PRODUCTION';
				} else {
					$milestones = $delivery_milestones;
				}


				$delivery_date = $datas13['delivery_date'];
			$html .= '<tr>
			<td style="text-align:center; border:1px solid black; ">'.$k.'</td>
			<td style="text-align:left; border:1px solid black; ">'.$milestones.'</td>
			<td style="text-align:left; border:1px solid black; ">'.date('d-m-Y', strtotime($delivery_date)).'</td>
			</tr>';
			$k++;
			}

		$html .= '</tbody>
		</table>
		<br/>
		The Lead time will be considered & bounded from the date of Purchase Order (subjected to advance payment with in 10 days from the date of PO) to the delivery of moulds to Port (Subjected to evidence). Otherwise supplier will be liable to pay the penality fee to client. All milestone such as Tool Design, Tooling, trials and testing, batch production for tools quality and mould packaging etc. comes with in the lead time.<br>
	Any delay in the lead time of the moulds delivery more than 10%, will be subjected to penalty of 1% of the project cost (Total Moulds cost) per day.


<br><br>';
$sql15 = "SELECT description FROM termsandconditions WHERE category=1 AND country_flag=$country_flag";	
$results15 = mysqli_query($con, $sql15);
$termsandconditions = '';

if(mysqli_num_rows($results15) > 0) {
	$data15 = mysqli_fetch_array($results15);
	$termsandconditions = $data15['description'];

}else
{
	$termsandconditions='';
}


$html .= $termsandconditions;


$stamp='';
if($po_approved == 1) {
	$sql16 = "SELECT stamp_file FROM tooling_po_stamp WHERE id=1";
	$results16 = mysqli_query($con, $sql16);
	$stamp_file = '';

	if(mysqli_num_rows($results16) > 0) {
		$data16 = mysqli_fetch_array($results16);
		$stamp_file = $data16['stamp_file'];
		$stamp= '<img src="'.stamp_url.$stamp_file.'" width="100px">';
	}

}


$html.='<table style="font-size:9px; padding:3px; width:100%;">
<tr>
<td style="text-align:center;"></td>
<td style="text-align:center;"></td>
<td style="text-align:center;">'.$stamp.'</td>
</tr>
</table>';



$html.='<hr>



<table style="font-size:9px; padding:3px; width:100%;">
<tr>
<td style="text-align:center;">Project Manager</td>
<td style="text-align:center;">Purchase Manager</td>
<td style="text-align:center;">Director</td>
</tr>
</table>


';	
// echo $html; exit;

//echo $footer_logo_html; exit;
// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$sql20 = "SELECT unique_id FROM leads WHERE id=$lead_id";
$result20 = mysqli_query($con, $sql20);


if (mysqli_num_rows($result20) > 0) {
	$data20 = mysqli_fetch_array($result20);

}

//echo $data20['unique_id'];exit;
// $sql15 = "SELECT description FROM termsandconditions WHERE category=1";	
// $results15 = mysqli_query($con, $sql15);
// $termsandconditions = '';
// if(mysqli_num_rows($results15) > 0) {
// 	$data15 = mysqli_fetch_array($results15);
// 	$termsandconditions = $datas15['description'];
// }
// echo 'hi';exit;

$filelocation = $_SERVER['DOCUMENT_ROOT'].'/image_bank/tooling_po';
$fileNL = $filelocation."/Tooling_PO_".$data20['unique_id'].".pdf"; //Linux

$pdf->Output($fileNL, 'F');
header('location:'.redirecturl.$data20['unique_id']);


//============================================================+
// END OF FILE
//============================================================+
