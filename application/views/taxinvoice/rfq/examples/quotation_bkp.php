<?php

/** QUERY ENDS **/
define('image_url', '/var/www/hongyijig.in/assets/client_bom_data/part_image/');
include('mysqlconfig.php');

$lead_id = $_GET['lead_id'];
$option_id = $_GET['option'];
$supplier = $_GET['suplier'];
// echo $supplier;exit;
$negotiation = $_GET['negotiation'];

if ($negotiation == '') {
	$negotiation = 0;	
}

$query1 = "SELECT * FROM quotation_tooling_time WHERE lead_id=$lead_id";
$results1 = mysqli_query($con, $query1);
$datas1 = mysqli_fetch_array($results1);

$tooling_days = ($datas1['tooling_days'] * 16)/100;
$dispatch_days = ($datas1['dispatch_days'] * 16)/100;
$b_2 = ($datas1['b_2'] * 16)/100;
$b_3 = ($datas1['b_3'] * 16)/100;
$b_4 = ($datas1['b_4'] * 16)/100;

$total_tooling_days = $datas1['tooling_days'] + $tooling_days;
$total_dispatch_days = $datas1['dispatch_days'] + $dispatch_days;
$total_b_2 = $datas1['b_2'] + $b_2;
$total_b_3 = $datas1['b_3'] + $b_3;
$total_b_4 = $datas1['b_4'] + $b_4;

$query = "SELECT d.currency_rate,b.company_name,a.delivery_type, b.customer_name, b.unique_id, b.currency_preference, c.project_name,c.billing_address, d.version, d.added_on FROM bom_pi_sales_project_info a LEFT JOIN leads b ON b.id=a.lead_id LEFT JOIN bom_pi_sales_client_info c ON c.lead_id=a.lead_id LEFT JOIN client_quoted_price d ON d.lead_id=a.lead_id WHERE a.lead_id = $lead_id AND d.option_id = $option_id";
// echo $query;exit;
$result = mysqli_query($con, $query);
$data = mysqli_fetch_array($result);

$currency_preference = $data['currency_preference'];


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

/** FINAL PRICE FOR THIS QUOTE **/

$sqlfinal = "SELECT final_price FROM client_quoted_price WHERE lead_id = $lead_id AND option_id=$option_id AND supplier_id=$supplier";
$resultsfinal = mysqli_query($con, $sqlfinal);
$datasfinalp = mysqli_fetch_array($resultsfinal);
$finalprice = $datasfinalp['final_price'];


// echo $finalprice;exit;
/** GET ALL PARTS LOOP AND CALCULATE THE PRICE GIVEN **/

$allmoulds = "SELECT id FROM bom_moulds_detail WHERE lead_id=$lead_id";
$resultsallmould = mysqli_query($con, $allmoulds);
$partprice = array();
$partprice[] = 0;
$mouldoverallweight = array();
$mouldoverallweight[] = 0;
while ($dataallmould = mysqli_fetch_array($resultsallmould)) {
	$allmouldid = $dataallmould['id'];
	$sql12allp = "SELECT mould_weight,price_in_dollar FROM sourcing_quotation WHERE option_id=$option_id AND mould_id = $allmouldid AND lead_id=$lead_id AND supplier_id=$supplier ORDER BY id DESC";

	// echo $sql12allp;exit;
	$results12allp = mysqli_query($con, $sql12allp);
	$mouldoptionnumallp = mysqli_num_rows($results12allp);
	if ($mouldoptionnumallp > 0) {
		$datas122345allp = mysqli_fetch_array($results12allp);
		$partprice[] = $datas122345allp['price_in_dollar'];
		$mouldoverallweight[] = $datas122345allp['mould_weight'];
	} else {

		$sql12zeroallp = "SELECT mould_weight,price_in_dollar FROM sourcing_quotation WHERE option_id=0 AND mould_id = $allmouldid AND lead_id=$lead_id AND supplier_id=$supplier ORDER BY id DESC";

		$results12zeroallp = mysqli_query($con, $sql12zeroallp);
		$datas122345zalpp = mysqli_fetch_array($results12zeroallp);

		$partprice[] = $datas122345zalpp['price_in_dollar'];
		$mouldoverallweight[] = $datas122345zalpp['mould_weight'];
	}
}

$suppliergivenprice = array_sum($partprice);
 // echo array_sum($mouldoverallweight);exit;


$businesscaseprice = $finalprice;
$suppliergivenweight = array_sum($mouldoverallweight);
 //echo $businesscaseprice.'<br/>'.$suppliergivenprice; exit;

if ($negotiation == 1) {
		$sqlneg = "SELECT negotiated_price FROM order_won WHERE lead_id = $lead_id";
		$resultneg = mysqli_query($con, $sqlneg);
		if(mysqli_num_rows($resultneg) > 0) {
			$dataneg = mysqli_fetch_array($resultneg);
			$businesscaseprice = $dataneg['negotiated_price'];
		}
	}


if ($businesscaseprice > $suppliergivenprice) {

	$diff = $businesscaseprice - $suppliergivenprice;
	$getpercentage = $diff * 100;
	$getextraper = $getpercentage / $suppliergivenprice;

	$percentfactor = $getextraper;
	//echo $percentfactor; exit;

} else {
	echo "BUSINESS CASE PRICE IS LESS THAN SUPPLIER PRICE! HENCE QUOTATION CANNOT BE CREATED";
	//exit;
}

require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);



// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH);
$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));

// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

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
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
	require_once(dirname(__FILE__) . '/lang/eng.php');
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
$pdf->setTextShadow(array('enabled' => false, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

// Set some content to print

/** quert paet **/

$delivery_type = $data['delivery_type'];

if ($delivery_type == 1) {
	$company_name = 'HONGYI <span style="color:orange;">JIG </span>RAPID TECHNOLOGIES CO., LTD.';
	$company_address = 'Unit H 1/F Mau Lam Comm Bldg16-18 Mau Lam St Jordan Kln, HK';
	$logo = '<img src="/var/www/hongyijig.in/pdf/internallogo.jpeg" width="200px">';

} else if ($delivery_type == 2) {
	$company_name = 'HONGYI <span style="color:orange;">JIG</span> RAPID TECHNOLOGIES';
	$company_address = '15/1, 2nd Floor, Rama Road Industrial Area Delhi 110015 (INDIA)';
	$logo = '<img src="/var/www/hongyijig.in/pdf/logo.jpeg" width="200px">';

/** GET CURRENCY VALUE **/
// $querycurr = "SELECT rate FROM currency_rates WHERE id=1";
// $resultscurr = mysqli_query($con, $querycurr);
// $datascurr = mysqli_fetch_array($resultscurr);
// $currrate=$datascurr['rate'];
/** END **/


} else {
	$company_name = '';
	$company_address = '';
	$logo = '';
}




$customer_name = $data['customer_name'];
$project_name = $data['project_name'];
$unique_id = $data['unique_id'];
$version = $data['version'];
$added_on = $data['added_on'];
$currency_rate=round($data['currency_rate'],2);
// echo $added_on;exit; 
$addedon = date('l, F d , Y', strtotime($added_on));
if ($delivery_type == 1) {
	$fob = "FOB";
} else if ($delivery_type == 2) {
	$fob = "";
}

if ($negotiation == 1) {
	$version = $version + 1;
}

$html = '

<table style="padding:3px; font-size:9px;">
<tr><td>' . $logo . '</td></tr>
<tr><td>' . $company_name . '</td></tr>
<tr><td>' . $company_address . '</td></tr>
<tr><td style="border-bottom:1px solid black;"></td></tr>
</table>
<br><br><br><br>
<br><br><br><br><br><br><br>
<table style="padding:3px; font-size:13px; text-align:center;">
<tr><td style="color:grey; font-weight:bold;">Kind Attention to
</td></tr>
<tr><td style="color:black; font-weight:bold;">Mr. ' . $customer_name . '</td></tr>

</table>

<br><br><br><br>
<br><br><br><br><br><br>
<table style="padding:3px; font-size:13px; text-align:center;">
<tr><td style="color:grey; font-weight:bold;">Techno-Commercial Quotation for</td></tr>
<tr><td style="color:black; font-weight:bold;">' . $project_name . '
</td></tr>

</table>

<br><br><br><br>
<br><br><br><br><br><br>

<table style="padding:3px; font-size:13px; text-align:center;">
<tr><td style="color:grey; font-weight:bold;">Version</td></tr>

<tr><td style="color:grey; ">REF-' . $unique_id . '/' . $version . '.00</td></tr>
</table>



<br><br><br><br>
<br><br><br><br>
<table style="padding:3px; font-size:13px; text-align:center;">
<tr><td style="color:grey; font-weight:bold;">Date</td></tr>
<tr><td style="color:black; font-weight:bold;">' . $addedon . '</td></tr>

</table>


<br pagebreak="true" />





<table style="padding:3px; font-size:11px;">
<tr ><td style="color:grey; font-size:11px; font-weight:bold;">INTRODUCTION</td></tr>
<tr><td style="color:grey; ">Project & Scope of Work</td></tr>
</table>
<table style="padding:3px; font-size:11px;">
<tr>
<th style="text-align:center; background-color:lightgrey; border:1px solid black; font-weight:bold;">PROJECT DETAILS</th>
<th style="text-align:center; background-color:lightgrey; border:1px solid black; font-weight:bold;">SCOPE OF WORK</th>
</tr>
<tr>
<td style="border:1px solid black; text-align:center; "><div style="line-height:90px;">' . $project_name . '</div></td>
<td style="border:1px solid black;">HJIG will check the Part design feasibility, create a DFM
to have an understanding with the client about the
Parting line and Gate etc. Doing Moldflow Analysis if
required. Tool designs and Tool design review with the
customer as per available machine compatibility and the
development of Moulds with overseas Toolmakers and
give satisfactory delivery to the customer</td>
</tr>
</table>
<br><br>

<table style="padding:3px; font-size:11px;">
<tr ><td style="color:grey; font-size:11px; font-weight:bold;">Delivery Milestones</td></tr>
<tr><td style="color:grey; ">SERVICES</td></tr>
</table>
<table style="padding:3px; font-size:11px;">
<tr>
<th style="text-align:center; background-color:lightgrey; border:1px solid black; font-weight:bold;">Milestones</th>
<th style="text-align:center; background-color:lightgrey; border:1px solid black; font-weight:bold;">Days</th>
</tr>
<tr>
<td style="border:1px solid black;">B-0 : Product Design/Prototyping</td>
<td style="border:1px solid black; text-align:center; ">'.round($total_tooling_days).'</td>
</tr>
<tr>
<td style="border:1px solid black;">B-1 : Pre Tooling</td>
<td style="border:1px solid black; text-align:center; ">'.round($total_dispatch_days).'</td>
</tr>
<tr>
<td style="border:1px solid black;">B-2 : Tooling</td>
<td style="border:1px solid black; text-align:center; ">'.round($total_b_2).'</td>
</tr>
<tr>
<td style="border:1px solid black;">B-3 : From T0 to Tool Dispatch</td>
<td style="border:1px solid black; text-align:center; ">'.round($total_b_3).'</td>
</tr>
<tr>
<td style="border:1px solid black;">B-4 : Shipment</td>
<td style="border:1px solid black; text-align:center; ">'.round($total_b_4).'</td>
</tr>
</table>
<br><br>

<table style="padding:3px; font-size:11px;">
<tr ><td style="color:grey; font-size:11px; font-weight:bold;">Techno-Commercial Milestones</td></tr>
<tr><td style="color:grey; ">SERVICES</td></tr>
</table>
<table style="padding:3px; font-size:11px;">
<tr>
<th style="text-align:center; background-color:lightgrey; border:1px solid black; font-weight:bold;">S. NO</th>
<th style="text-align:center; background-color:lightgrey; border:1px solid black; font-weight:bold;">SERVICE TITLE</th>
<th style="text-align:center; background-color:lightgrey; border:1px solid black; font-weight:bold;">PRICE US$</th>
<th style="text-align:center; background-color:lightgrey; border:1px solid black; font-weight:bold;">LEAD TIME</th>
</tr>';

$sql_ser = "SELECT a.service_amt, a.lead_time, a.po_upload, b.service_name, c.company_name FROM bom_pi_services_info a LEFT JOIN services b ON a.hjig_services=b.id LEFT JOIN external_suppliers c ON c.id=a.external_suppliers WHERE lead_id = $lead_id";
// echo $sql_ser;exit;
$results_ser = mysqli_query($con, $sql_ser);

$sno = 1;
$total_ser_amt = array();
$total_ser_amt[] = 0;
while($data_ser = mysqli_fetch_array($results_ser)) {
$lead_time = round($data_ser['lead_time']);
$total_lead_time = round(($lead_time * 16)/100) + $lead_time;
$service_name = $data_ser['service_name'];
$service_amt = round($data_ser['service_amt']);

$sql14 = "SELECT low_price, low_percent, high_price, high_percent FROM business_case_client WHERE $service_amt BETWEEN low_price AND high_price";
$result14 = mysqli_query($con, $sql14);
$client_quotation = '';
    if(mysqli_num_rows($result14) > 0) {                                
	    $data14 = mysqli_fetch_array($result14);

	        if($data14['low_price'] == $service_amt) {
	            $client_quotation = $service_amt + (($service_amt * $data14['low_percent'])/100);
	        } else if($data14['high_price'] == $service_amt) {
	            $client_quotation = $service_amt + (($service_amt * $data14['high_percent'])/100);
	        } else if(($data14['low_price'] < $service_amt) && ($data14['high_price'] > $service_amt)) {
	            $total_percentage = $data14['low_percent'] + $data14['high_percent'];
				$total_c_percentage=$total_percentage/2;
				
	            $client_quotation = $service_amt + (($service_amt * $total_c_percentage)/100);
	        } else if($data14['low_price'] > $service_amt)
			{
				echo $client_quotation = 'NO RANGE FOUND FOR BUSINESS CASE';exit;
				
			}else
				{
	            $client_quotation = '';
	        }
	    } else {
	    	echo $client_quotation = 'NO RANGE FOUND FOR BUSINESS CASE';exit;
	    }
         // echo $client_quotation;
   

$html .= '<tr>
<td style="border:1px solid black; text-align:center; ">'.$sno.'</td>
<td style="border:1px solid black;text-align:center;">'.$service_name.'</td>
<td style="border:1px solid black; text-align:center; ">$'.round($client_quotation).'</td>
<td style="border:1px solid black; text-align:center; ">'.$total_lead_time.'</td>
</tr>';
$total_ser_amt[] = round($client_quotation);
$sno++;
}

$html .= '<tr style="background-color:lightgrey; font-weight:bold;" nobr="true">
<td colspan="2" style="text-align:right;">TOTAL</td>
<td>$'.array_sum($total_ser_amt).'</td>
<td></td>
<td></td>
</tr>
<tr style="background-color:#eeeeee; font-weight:bold; text-align: center;" nobr="true">
<td colspan="11">AMOUNT IN WORDS: USD ' . ucwords(getCurrencyCode(array_sum($total_ser_amt))) . ' Only</td>
</tr>
</table>
<br><br>

<table style="padding:3px; font-size:11px;">
<tr><td style="color:grey; font-weight:bold; font-size:11px;">LIST OF TOOLS WITH TECHNICAL SPECS</td></tr>
<tr><td style="color:grey; ">Project & Scope of Work</td></tr>
</table>
<table style="padding:3px; font-size:11px; text-align:center;" rules="all" border="1">
<tr style="background-color:lightgrey; font-weight:bold;" nobr="true">
<th>S.No</th>
<th>Part Name<br>Part Picture<br>Part Size<br/>Cavity</th>
<th>Part CFM</th>
<th>Runner Type/Make</th>
<th>Mould Sizes(mm)</th>
<th>Mould Material</th>
<th>Weight in Kgs.</th>
<th>Cost ' . $fob . '(USD)</th>
</tr>';

$sql = "SELECT id, type FROM bom_moulds_detail WHERE lead_id = $lead_id";
$results = mysqli_query($con, $sql);

if (mysqli_num_rows($results) > 0) {
	$mld = 1;
	$mouldcheck = array();
	$mould_weight = array();
	$priceindollar = array();
	while ($datas = mysqli_fetch_array($results)) {
		$mould_id = $datas['id'];

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
			$sql2 = "SELECT a.color,a.colortype,a.pantonecode,a.actual_shrinkage,b.colourname, c.partname FROM  bom_mould_wise_part_detail a LEFT JOIN part_colour b ON a.color=b.id LEFT JOIN part_master c ON c.id=a.part_material WHERE a.mouldid = $mould_id";
			$result2 = mysqli_query($con, $sql2);
			$data2 = mysqli_fetch_array($result2);
			$color = '';
			if ($data2['colortype'] == 1) {
				$color = $data2['colourname'];
			} else {
				$color = "Special Color-" . $data2['pantonecode'];
			}

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

			$material = $data2['partname'] . $data2['actual_shrinkage'];

			$sql3 = "SELECT b.name as core_name, a.runner_type,e.name as gatingname,a.gating_type,a.tips,c.name as cavity_name, a.mould_base_steel,a.core_steel,a.cavity_steel, a.insertmoulding, a.cadweight, d.name as runner_brand, f.name as runner_name FROM bom_part_additional_details a LEFT JOIN core_steel b ON a.core_steel=b.id LEFT JOIN cavity_steel c ON c.id=a.cavity_steel LEFT JOIN runner_type d ON d.id=a.runner_type  LEFT JOIN gating_type e ON a.gating_type=e.id LEFT JOIN hot_runner_brand f ON f.id=a.runnerbrand WHERE a.partdetailid = $part_id";
			//echo $sql3;exit;
			$results3 = mysqli_query($con, $sql3);
			$datas3 = mysqli_fetch_array($results3);


			if ($datas3['runner_type'] == 1) {
				$runname = "Hot Runner";
				$runner_brand = "<strong>Runner Brand</strong><br/>".$datas3['runner_name'];
				$gating = "<strong>Gating</strong><br/>" . $datas3['gatingname'];
				$tips = "<strong>Tips</strong><br/>" . $datas3['tips'];
			} else if ($datas3['runner_type'] == 2) {
				$runname = "Cold Runner";
				$runner_brand = "";
				$gating = "<strong>Gating</strong><br/>" . $datas3['gatingname'];
				$tips = '';
			} else {
				$runname = "";
				$runner_brand = "";
				$gating = '';
				$tips = '';
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
							$runner_brand = "<strong>Runner Brand</strong><br/>".$otherchanged['runner_brand'];
							$tips = $otherchanged['tips'];
						} else {
							$runname = "COLD RUNNER";
							$runner_brand = "";
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
					}
				}
				//$datasother = mysqli_fetch_array($resultsother);

			}


			if ($option_id > 0) {


				$sql12 = "SELECT mould_size,x, y, z, mould_weight,price_in_dollar FROM sourcing_quotation WHERE option_id=$option_id AND mould_id = $mould_id AND lead_id=$lead_id AND supplier_id=$supplier ORDER BY id DESC";
				//echo $sql12;exit;
				$results12 = mysqli_query($con, $sql12);
				$mouldoptionnum = mysqli_num_rows($results12);
				if ($mouldoptionnum > 0) {
					$datas122345 = mysqli_fetch_array($results12);
					$mouldsize = "X - " . $datas122345z['x'] . " mm<br>" . "Y - " . $datas122345z['y'] . " mm<br>" . "Z - " . $datas122345z['z'] . " mm";
					$mouldweight = $datas122345['mould_weight'];
					$priceinusd = $datas122345['price_in_dollar'];
					// echo $priceinusd.'<br>';
					$revamp = $percentfactor / 100;
					// echo $revamp;exit;
					$revamp1 = $priceinusd * $revamp;
					$priceinusd = $priceinusd + $revamp1;
				} else {

					$sql12zero = "SELECT mould_size,x, y, z, mould_weight,price_in_dollar FROM sourcing_quotation WHERE option_id=0 AND mould_id = $mould_id AND lead_id=$lead_id AND supplier_id=$supplier ORDER BY id DESC";


					// echo $sql12zero;exit;
					$results12zero = mysqli_query($con, $sql12zero);
					$datas122345z = mysqli_fetch_array($results12zero);
					$mouldsize = "X - " . $datas122345z['x'] . " mm<br>" . "Y - " . $datas122345z['y'] . " mm<br>" . "Z - " . $datas122345z['z'] . " mm";
					$mouldweight = $datas122345z['mould_weight'];
					$priceinusd = $datas122345z['price_in_dollar'];

					$revamp = $percentfactor / 100;
					$revamp1 = $priceinusd * $revamp;
					$priceinusd = $priceinusd + $revamp1;
				}
			} else {

				

				$sql12zero = "SELECT mould_size,x, y, z, mould_weight,price_in_dollar FROM sourcing_quotation WHERE option_id=0 AND mould_id = $mould_id AND lead_id=$lead_id AND supplier_id=$supplier ORDER BY id DESC";
				// echo $sql12zero;exit;
				$results12zero = mysqli_query($con, $sql12zero);
				$datas122345z = mysqli_fetch_array($results12zero);
				$mouldsize = "X - " . $datas122345z['x'] . " mm<br>" . "Y - " . $datas122345z['y'] . " mm<br>" . "Z - " . $datas122345z['z'] . " mm";
				$mouldweight = $datas122345z['mould_weight'];
				$priceinusd = $datas122345z['price_in_dollar'];

				$revamp = $percentfactor / 100;
				$revamp1 = $priceinusd * $revamp;

				$priceinusd = $priceinusd + $revamp1;
			}

			

			$html .= '<tr nobr="true">';

			if (!in_array($mould_id, $mouldcheck)) {
				$html .= '<td rowspan="' . $partscount . '"><strong>MOULD ' . $mld . '<br>' . $mould_type . $mould_id.'</strong></td>';
			}

			$html .= '<td>' . $data1['name'] . '<br><img src="' . image_url . $data1['image'] . '" width="100px"><br>Size: ' . $part_length . 'x' . $part_width . 'x' . $part_height . '<br/>Cavity:' . $data1['cavity'] . '</td>
			<td><strong>Part Colour</strong><br/>' . $color . '<br/><br/>
			<strong>Surface Finish</strong><br/>' . $fin . '<br/><br/>
			<strong>Material</strong><br/>' . $material . '</td>';
			if (!in_array($mould_id, $mouldcheck)) {
				$html .= '<td rowspan="' . $partscount . '"><strong>Runner</strong><br/>' . $runname. '<br/><br/>'.$runner_brand . '<br/><br/>' . $gating . '<br/><br/>' . $tips .'</td>
			<td rowspan="' . $partscount . '">' . $mouldsize . '</td>
			<td rowspan="' . $partscount . '"><strong>Tool Steel Core</strong><br>'.$data9['tool_cavity'].'<br><strong>Tool Steel Cavity</strong><br>'.$data9['core_cavity'].'<br><strong>Tool Steel Mould Base</strong><br>'.$steelname.'</td>
			<td rowspan="' . $partscount . '">' . $mouldweight . 'kg</td>
			<td rowspan="' . $partscount . '" valign="center">$' . round($priceinusd) . '</td>';
				$mould_weight[] = $mouldweight;
				$priceindollar[] = round($priceinusd);
			}


			$html .= '</tr>';

			if ($partscount > 1) {
				$mouldcheck[] = $mould_id;
			}
			$i++;
		}
		$mld++;
	}
}

$grand_total_amt = array_sum($priceindollar) + array_sum($total_ser_amt);
 // echo "<pre>";print_r($priceindollar);exit;
$html .= '<tr style="background-color:lightgrey; font-weight:bold;" nobr="true">
<td colspan="6" style="text-align:right;">Total</td>
<td> ' . array_sum($mould_weight) . ' Kgs</td>
<td>$' . array_sum($priceindollar) . '</td>
</tr>
<tr style="background-color:lightgrey; font-weight:bold;" nobr="true">
<td colspan="6" style="text-align:right;">Services</td>
<td></td>
<td>$'.array_sum($total_ser_amt).'</td>
</tr>
<tr style="background-color:lightgrey; font-weight:bold;" nobr="true">
<td colspan="6" style="text-align:right;">Grand Total In USD</td>
<td></td>
<td>$'.$grand_total_amt.'</td>
</tr>
<tr style="background-color:#eeeeee; font-weight:bold;" nobr="true">
<td colspan="11">AMOUNT IN WORDS: USD ' . ucwords(getCurrencyCode($grand_total_amt)) . ' Only</td>
</tr>';

if($delivery_type==2)
{
	$inrtotal=$grand_total_amt*$currency_rate;
	$html.='<tr style="background-color:lightgrey; font-weight:bold;" nobr="true">
	<td colspan="6" style="text-align:right;">Grand Total In INR</td>
	<td></td>
	<td>₹ '.$inrtotal.'</td>
	</tr>
	<tr style="background-color:#eeeeee; font-weight:bold;" nobr="true">
	<td colspan="11">AMOUNT IN WORDS: Rs. ' . ucwords(getCurrencyCode($inrtotal)) . ' Only</td>
	</tr>';
}

$html.='</table>';

if($delivery_type==2)
{
$html.='<br><br><table style="padding:3px; font-size:11px;">
<tr nobr="true"><td style="">Please consider the currency exchange rate of the day at the time of generating purchase order and Bank Transfer of each stage as below. The Current USD Rate is '.number_format($currency_rate,2).' The Total Value is '.$inrtotal.' in INR. So, the total price will be considered in '.$grand_total_amt.' USD. All the upcoming payments will be made basis on the currency exchange price of US Dollar on the day of Payment Made.</td></tr>
<tr nobr="true"><td style="color:grey; "></td></tr>
</table>';
}

$html.='<br><br>
<table style="padding:3px; font-size:11px;">
<tr nobr="true"><td style="color:grey; font-weight:bold; font-size:11px;">COMMERCIAL TRANSACTIONS</td></tr>
<tr nobr="true"><td style="color:grey; ">Payment Milestones and Bank Details</td></tr>
</table>




<table style=" font-size:11px; padding:0px;" border="1">

	<tr>
		<td style="width:288px; " >
			<table border="1" style="padding:3px; ">
				<tr style="background-color:lightgrey; font-weight:bold; text-align:center;">
					<th colspan="2">Account Details </th>
				</tr>';

if ($delivery_type == 1) {
	$html .= '<tr style="font-weight:bold;" nobr="true">
<td style="text-align:right; width:100px;">Account Name</td>
<td style="text-align:left; width:187px;">HONGYI JIG RAPID TECHNOLOGIES CO., LTD</td>
</tr>
<tr style="" nobr="true">
<td style="text-align:right;font-weight:bold;">Account No.</td>
<td style="text-align:left;">048-857452-838</td>
</tr>
<tr style="" nobr="true">
<td style="text-align:right;font-weight:bold;">Beneficiary Bank</td>
<td style="text-align:left;">HSBC HONG KONG BANK </td>
</tr>
<tr style="" nobr="true">
<td style="text-align:right;font-weight:bold;">Bank Address</td>
<td style="text-align:left;">1 QUEEN,S ROAD, CENTRAL, HONG KONG</td>
</tr>
<tr style="" nobr="true">
<td style="text-align:right;font-weight:bold;">SWIFT CODE</td>
<td style="text-align:left;">HSBCHKHHHKH</td>
</tr>';
} else {
	$html .= '<tr style="font-weight:bold;" nobr="true">
				<td style="text-align:right; width:100px;">Account Name</td>
				<td style="text-align:left; width:187px;"> HONGYI JIG RAPID TEC</td>
				</tr>
				<tr style="" nobr="true">
				<td style="text-align:right;font-weight:bold;">Account No.</td>
				<td style="text-align:left;"> 2016256010761</td>
				</tr>
				<tr style="" nobr="true">
				<td style="text-align:right;font-weight:bold;">Beneficiary Bank</td>
				<td style="text-align:left;">CANARA BANK</td>
				</tr>
				<tr style="" nobr="true">
				<td style="text-align:right;font-weight:bold;">Branch</td>
				<td style="text-align:left;">DELHI KESHAV PURAM</td>
				</tr>
				<tr style="" nobr="true">
				<td style="text-align:right;font-weight:bold;">IFSC Code</td>
				<td style="text-align:left;">CNRB0002016</td>
				</tr>
				<tr style="" nobr="true">
				<td style="text-align:right;font-weight:bold;">MICR Code</td>
				<td style="text-align:left;">110015072</td>
				</tr>';
}
$html .= '</table>
		</td>

<td style="width:341px; background-color:lightgrey;">
<table border="1" style="padding:3px;" >
<tr style="background-color:lightgrey; font-weight:bold; text-align:center;">
<th style="width:136px;">PAYMENT STAGE</th>
<th style="width:87px;">STAGE</th>
<th style="width:40px;">%</th>
<th style="width:77px;">AMOUNT</th>
</tr>';
$sql14 = "SELECT final_price FROM client_quoted_price WHERE lead_id = $lead_id AND option_id=$option_id AND supplier_id=$supplier";
$results14 = mysqli_query($con, $sql14);
$datas14 = mysqli_fetch_array($results14);

$sql13 = "SELECT payment_stage, stage, percentage FROM payment_terms WHERE lead_id = $lead_id";
//echo $sql13; exit;
$results13 = mysqli_query($con, $sql13);
$total_amount = array();

// echo array_sum($total_ser_amt);exit;
// $final_price = $datas14['final_price'];
// echo $final_price;exit;
// $final_price = array_sum($priceindollar) + array_sum($total_ser_amt);
$final_price = $grand_total_amt;
while ($datas13 = mysqli_fetch_array($results13)) {

	$percentage = $datas13['percentage'];
	$amount = ($final_price * $percentage) / 100;
	$html .= '<tr style="text-align:center; ">
	<td style="font-weight:bold; text-align:right; background-color:white;">' . $datas13['payment_stage'] . '</td>
	<td style="background-color:white;">' . $datas13['stage'] . '</td>
	<td style="background-color:white;">' . $percentage . '%</td>
	<td style="background-color:white;">$' . $amount . '</td>
	</tr>';
	$total_amount[] = $amount;
}


$sql131 = "SELECT final_price, supplier_id FROM client_quoted_price WHERE lead_id = $lead_id";
$results131 = mysqli_query($con, $sql131);
while ($datas131 = mysqli_fetch_array($results131)) {

	$supplierid = $datas131['supplier_id'];
}
// $sql1311 = "SELECT mould_weight FROM sourcing_quotation WHERE supplier_id = $supplierid";
// $results1311 = mysqli_query($con,$sql1311);
// while($datas1311 = mysqli_fetch_array($results1311)){
// 	$mouldweightdata[] = $datas1311['mould_weight'];
// }
$valuetotal =  array_sum($priceindollar);


$mould_weight_data = $suppliergivenweight;
$pertonvalue = 150;
$seafreight = ($mould_weight_data / 1000) * $pertonvalue;
$customvalue = ($valuetotal + $seafreight) * 0.28;
$clearance = 400;

if ($delivery_type == 2) {
//$grandtotal = ceil($seafreight + $customvalue + $clearance);
$grandtotal=0;
} else {
$grandtotal = 0;
}

$finalgrategrandtotal = array_sum($total_amount) + $grandtotal;

if ($delivery_type == 2) {
$html .= '<tr style="background-color:lightgrey; text-align:center;">
<td colspan="3" style="font-weight:bold; text-align:right;">SHIPMENT & CLEARANCE CHARGES</td>
<td>As per actual</td>
</tr>';
}

$html .= '<tr style="background-color:lightgrey; text-align:center;">
<td colspan="3" style="font-weight:bold; text-align:right;">TOTAL</td>
<td>$' . round($finalgrategrandtotal) . '</td>
</tr>
</table>


</td>
</tr>
</table>
<br><br>
<table style="padding:3px; font-size:11px;">
<tr><td style="color:grey; font-weight:bold; font-size:11px;">ROLES & RESPONSIBILITIES
</td></tr>
<tr><td style="color:grey; ">Understanding the Key responsibilities with customer
</td>

</tr>
<tr><td style="color:grey; ">We define the total Project in 6 Phases, Please note the Roles and responsibility on each
phase. Stage B-0 is applicable only with special requests.</td></tr>
</table>
<table style="padding:3px; font-size:11px;" border="1">
<tr nobr="true">
<th style="text-align:center; background-color:lightgrey; border:1px solid black; font-weight:bold;">STAGE</th>
<th style="text-align:center; background-color:lightgrey; border:1px solid black; font-weight:bold;">HONGYI HJIG </th>
<th style="text-align:center; background-color:lightgrey; border:1px solid black; font-weight:bold;">CUSTOMER</th>
</tr>
<tr nobr="true">
<td><strong>Stage B-0
</strong><br>(Product
Design/Prototy
ping)</td>
<td>HJIG will offer Product Styling. Based
on selected styles. Our engineering
team will prepare the detailed
Engineered 3D models. Any detailed
2D drawing with GD&T will be
chargeable.
</td>
<td>Customers will provide the complete
details for the product design such
as Bench marked samples, BOPs,
Fitment parts, necessary references,
Standards for certifications etc.
Customer will also take active
participation in design selection and
engineering validation.</td>
</tr>

<tr nobr="true">
<td><strong>Stage B-1
</strong><br>(Pre-Tooling
Task)
)</td>
<td>HJIG will provide the DFM (Design
Feasibility), Moldflow Analysis in
case of Hot runner and a Detailed
tool Design in STEP Format.
In case of larger businesses, Our
team will also help you to finalise the
suitable Moulding machine selection
and production planning based on
your monthly sales targets to
minimise the investment and to get
the maximum output.</td>
<td>Customers will approve the DFM in
terms of approving the Gating mark
and parting lines acceptability.
Customer will share their production/
Sales targets, Injection moulding
machines details (in case if you
already owned the machines)
Will provide the Raw material
technical specifications to be finally
used in the production.
</td>
</tr>


<tr nobr="true">
<td><strong>Stage B-2
</strong><br>(Tooling)

)</td>
<td>HJIG will provide you the updates of
Tooling Process on a weekly basis
through Gantt Charts and Process
Pictures.
</td>
<td><strong style="text-align:center;">NA</strong></td>
</tr>

<tr nobr="true">
<td><strong>Stage B-3
</strong><br>(From T0 to
Tool Dispatch)</td>
<td>HJIG will share the Trail Videos,
Sample Pictures and will arrange the
express of samples to the customer.
HJIG will do the assembly test BOP’s
and other meting parts. If feasible
within our capability. In case of White
Goods and Other Industrial Product
HJIG will prepare Visual Inspection
Report for all the domains Assembly
fitment report.
In case of OEM Automotive and
Tier-1 components, HJIG will provide
a detailed Dimensional report
(Subjected to a list of critical
dimensions with GD&T drawing must
be given before project kick off.</td>
<td>Customer will share the testing
process before the project kick off.
Customer will provide minimum 5
sets of BOPs / Fitment parts at the
time of project kick off to validate the
product assembly.
In case of a special assembly
process, the customer will do the
assembly and provide a detailed
feedback report in ONE GO to make
the necessary ECNs in the tools.</td>
</tr>
<tr nobr="true"><td><strong>Stage B-4</strong><br>(Shipment)
</td>
<td>For <strong>CIF</strong> Shipments, HJIG will deliver
the Mould to pre-informed
Customer’s Factory.
For <strong>FOB</strong> Shipment, HJIG will
coordinate with the customer’s
forwarding Agent to handover the
shipment to the destined port of
loading.
</td>
<td>
In <strong>CIF</strong> Shipments, Customers will be
accountable to pay the pre-informed
amount of shipment, custom duties
and other taxes at least 3-4 days
before shipment reaches the port.
In case of FOB shipment, after
handing over the moulds at the port
of loading, customer will be
responsible to manage the shipment
till his factory.
</td></tr>

<tr nobr="true">
<td><strong>Stage B-5</strong><br>(Installation
and
Commissionin
g)
</td>
<td>HJIG Service Engineer will come to
the customer site to help them to set
up the mould for trial and initialise the
mould running.
</td>
<td>Customers will provide pre-planning
of Moulds Schedule to run the tools
on selected machines.
Customer will manage all the
necessary arrangements such as
machine installation / connection /
mould mounting etc.
Except Delhi/ NCR installations,
Client will manage the Travelling
and Accommodation for the
engineer.
Customer will make the prior
arrangement with the maintenance
workshop in case if some small
correction are required to run the
mould under the supervision of HJIG
Engineering team.</td>
</tr>
</table>
<br><br>
<table style="padding:3px; font-size:11px;">
<tr nobr="true"><td style="color:grey; font-weight:bold; font-size:11px;">TERMS & CONDITIONS

</td></tr>
<tr><td style="color:grey; ">Common Mandatory Terms and Conditions
</td></tr>
</table>

<table style="width:100%; padding:3px; font-size:11px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
BASIC BASIC UNDERSTANDING 
</td>
</tr>
</table>
<ol style="font-size:11px;">
<li>Any difference in scope of work by means of engineering effort,or number of moulds at the time of project kick off and after Part design Sign off is subjected to a revised cost of tooling.
</li>
<li>Customer will provide minimum 5 Set of BOPs , Child Parts or fasterners need to be insert moulded  and Fitment Parts if any in advance before project starts. This will help us to prevent any delays and rework  in the project.</li>
<li>We strongly recommend to  make Functional prototype in case of  New Product Designs  before tooling kick off, to avoid any Design ECN s and repair cost during the Project.</li>
<li>Prototype cost will  only be quoted, once Part engineering designs are ready. 
</li>
<li>We suggest  to assign one decesion maker Person from yourside during the Project running , with Hongyi JIG  Customer Relationship Manager to communicate through Email / Whatsapp Group and Personal Meetings during office Time, i.e. 8:00 am to 5:00 PM. We will try our best to reply all your doubts or questions asked with in 24 hours in between the office timing.</li>
<li>We suggest  you to provide all the Design inputs in single go to avoid any rework cost.
</li>
<li> The BOM offered at the time of quotation may be revised, incase Product Design is not finalised. Both parties will review the BOM and Price after the Engineering Design or Design Change activities. Commercial may revise at this stage.
</li>
<li>The shipment cost is based on projected estimation, In case of any difference of Prices, will be conveyed at the time of shipment. 
</li>
</ol>

<table style="width:100%; padding:3px; font-size:11px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
PROJECT KICK OFF
</td>
</tr>
</table>
<ol style="font-size:11px;">
<li>Orders are subjected to payment realisation of advance payment by Cheque / DD / Cash / RTGS / TT as per "Payment Terms".
</li>
<li>The  proposal / Agreement sent by email will be considered "Acceptable" if no objection has been raised in reply before the advance paid.
</li>
<li>Quotation validity is 15 days.
</li>
</ol>
<table style="width:100%; padding:3px; font-size:11px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
LEAD TIME
</td>
</tr>
</table>';

$html .= '<ol style="font-size:11px;">
<li>There will be 6 phases of the tooling development
<ul style="list-style-type: circle;">
<li>Stage B-0 (Product Design/Prototyping) : Lead time is based on approximation and depends on design dicussions and approvals.</li>
<li>Stage B-1 (Pre-Tooling Task) : DFM, Moldflow and Tool Design Sign Off. Lead time depends on design dicussions and approvals.</li>
<li>Stage B-2 (Tooling) : '.round($total_b_2).' Days</li>
<li>Stage B-3 (From T0 to Tool Dispatch) : '.round($total_b_3).' Days excluding Trail Samples Logistics Time.</li>
<li>Stage B-4 (Shipment) : As per actual</li>
<li>Stage B-5 (Installation and Commissioning) : As if Required.</li>
</ul>
</li>
</ol>

<table style="width:100%; padding:3px; font-size:11px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
TRIALS, TESTING & BATCH PRODUCTION
</td>
</tr>
</table>
<ol style="font-size:11px;">
<li>There will be 2-3 shots trial samples  will be provided in each trial of Mould. The samples will be expressed to Client s Address.</li>
<li>All the feedback of trailed samples will be given in one go from customer. No partial requests of tool corrections will be acceptable. </li>
<li>We suggest to share the inspection process for the mould trial samples. Any additional information or new criteria of inspection craetes complication and incraese the rework cost.</li>
<li>We suggest to define the entire cretiera of inspection in one go with clarity. Any additional information or new criteria of inspection creates re-work cost once we start the tooling process.</li>
<li>For All Production & Batch Production requirements, are based on EXW and are chargeable including raw material, production cost and packaging. In case of any production plan please inform us atleast 30 days before the T-1 date, this saves the prepration time of production. Incase of specific colours,  We request you to provide the colour code ( Panton/ RGB) in advance, otherwise HJIG will facilitate the available colours with the trial testing factory.</li>
</ol>

<table style="width:100%; padding:3px; font-size:11px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
DELIVERY
</td>
</tr>
</table>
<ol style="font-size:11px;">
<li>All the deliveries will be made after clearing full and final payment including GST/ Local Taxes (If Applicable)
</li>
<li>Tool Shipment will be made after the final approval from client so as to ensure there may not be any ECN or modification after that
</li>
</ol>
<table style="width:100%; padding:3px; font-size:11px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
SHIPMENT & TRANSFERS
</td>
</tr>
</table>
<ol style="font-size:11px;">
<li>For CIF deliveries, Hongyi JIG will quote the estimated budget in advance before project kick off. Imports and shipment documents will be kept confidential and not shared.
</li>
<li>In CIF the shipment is by Sea. If client wish to make it by Air shipment, then Air Freight, Custom Duty Difference, and Shipment handling charges extra. Estimated Costs for same will be shared prior to same.
</li>
<li>In case Insurance of Cargo is required, it must be pre informed, else Hongyi JIG will not responsible for any kind of damage or breakage during shipment.
</li>
<li>For the shipment delays like as Congestion at port, delay by customs, container shortage, flight rescheduling etc. are not in control of HJIG. HJIG will keep update to client well on time. HJIG will not take any responsibility of demurrages & penalties caused.
</li>
</ol>
<table style="width:100%; padding:3px; font-size:11px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
PAYMENTS & INVOICES
</td>
</tr>
</table>
<ol style="font-size:11px;">
<li>The currency prices will be considered at the time of order booking and paying the advance money. Any difference of forex exchange during the project running will be counted in the customer’s account at the time of payment milestone.
</li>
<li>All Invoices will be raised after the final payment & Taxes without any pendancy of balance payment including GST
</li>
<li>The cost of shipment and clearance should be paid 3 days before the container reach the Port of Discharge.
</li>
</ol>
<table style="width:100%; padding:3px; font-size:11px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
WARRANTY & SERVICE
</td>
</tr>
</table>
<ol style="font-size:11px;">
<li>Hongyi JIG Tool Development engineers will support client in Tool installation and commissioning to set up the 1st Trial production at client’s site. In case of visit, Client will align the date and time atleast 7 days before to arrange the same. In Case of any tool modification after shipment the tool repair facilities and labour will be arange by the client under the supervision of Hongyi JIG Team.</li>
<li>For tool installation except Delhi NCR, Travelling (To and Fro) and Accomodation Expenses of Service Engineer will be taken care by the customer. 
</li>
<li>Hongyi JIG Technical team will provide all final Tool Designs in Detailed 3D models (STEP / Parasolid formats)</li>
</ol>
<table style="width:100%; padding:3px; font-size:11px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
DISPUTES 
</td>
</tr>
</table>
<ol style="font-size:11px;">
<li>In case if due to any reason customer wish to stop the project in between, the commercials can be settled by paying the actual cost of services completed or initiated by Hongyi JIG. 
</li>
<li>Any Force majoure losses or damages will not be in HJIG account. 
</li>
</ol>';

$sql = "SELECT id FROM  bom_pi_sales_project_info WHERE lead_id = $lead_id";
$results = mysqli_query($con, $sql);
$data1 = mysqli_fetch_array($results);
$projectid = $data1['id'];
$sql2 = "SELECT customer_request FROM  bom_pi_sales_cust_req WHERE project_id =$projectid";
$resultssss = mysqli_query($con, $sql2);
 if(mysqli_num_rows($resultssss) > 0) {
$html .= '<table style="width:100%; padding:3px; font-size:11px;">
<tr>
<td style="text-align:center; font-weight:bold; background-color:lightgrey; ">
TERMS REQUESTED BY CLIENT

</td>
</tr>
</table>
<ol style="font-size:11px;">';

while ($datass = mysqli_fetch_array($resultssss)) {
	//echo "<pre>"; print($datass );
	//$customerrequest = $datass["customer_request"];
	$html .= '<li>' . $datass["customer_request"] . '</li>';
}
$html .= '</ol>
<br>
<br>
<br>
<br>';
}

$html .= '<table style="width:100%; padding:3px; font-size:11px;">
<tr>
<td style="text-align:center; font-weight:bold; background-color:lightgrey; ">
Signature & Stamp to Acknowledge 

</td>
</tr>
</table>
<br>
<br>
<br>
<br>
<table style="padding:3px; font-size:11px; " rules="all" border="1">
<tr style="font-weight:bold; text-align:center;">
<td>' . $company_name . '</td>
<td>' . $data['company_name'] . '</td>
</tr>
<tr style="background-color:lightgrey;">
<td>' . $company_address . '</td>
<td>' . $data['billing_address'] . '</td>
</tr>
<tr>
<td style="height:70px;"></td>
<td style="height:70px;"></td>
</tr>
</table>

<p style="font-size:11px;">Dear Sir, to confirm the order, please sign and stamp below in the client section and send a copy along with your PO.
In case of any doubt, please do not hesitate to contact us.
</p>
<p style="font-size:11px;">Regards,
</p>';

$sql17 = "SELECT member_id FROM  lead_assigned_to_team_member WHERE lead_id = $lead_id";
$results17 = mysqli_query($con, $sql17);
$data17 = mysqli_fetch_array($results17);
$member_id = $data17['member_id'];


$sql18 = "SELECT first_name, last_name FROM  system_users WHERE user_id = $member_id";;
//echo $sql2; exit;
$result18 = mysqli_query($con, $sql18);
$data18 = mysqli_fetch_array($result18);
//echo "<pre>"; print_r($datass); exit;
$name = $data18["first_name"] . ' ' . $data18["last_name"];
$html .= '<p style="font-size:11px;">' . $name . '</p>
		  <p style="font-size:11px;">Sales Manager</p>
		  <p style="font-size:11px;">' . $company_name . '</p>';


//echo $footer_logo_html; exit;
// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.



$filelocation = $_SERVER['DOCUMENT_ROOT'] . '/image_bank/popdf';
// $fileNL = $filelocation."/akash.pdf"; //Linux
$fileNL = $filelocation . "/quotation-" . $lead_id . $option_id . ".pdf"; //Linux
$pdf->Output($fileNL, 'I');



//============================================================+
// END OF FILE
//============================================================+
