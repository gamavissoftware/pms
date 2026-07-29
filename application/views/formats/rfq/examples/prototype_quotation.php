<?php
define('image_url', '/var/www/hongyijig.in/assets/prototype_part_picture/');
define('IMAGEPATH', '/var/www/hongyijig.in/assets/client_bom_data/part_image/');

include('mysqlconfig.php');

$order_won_id = $_GET['order_won_id'];
$lead_id = $_GET['lead_id'];




$query = "SELECT b.company_name,a.delivery_type, a.shipment_type, a.tool_quality_standard, b.customer_name, b.unique_id, c.project_name,c.billing_address, d.supplier_id, d.currency_rate, d.shipment, d.shipment_amt, d.added_on, d.currency_preference FROM bom_pi_sales_project_info a LEFT JOIN leads b ON b.id=a.lead_id LEFT JOIN bom_pi_sales_client_info c ON c.lead_id=a.lead_id LEFT JOIN prototype_quoted_price d ON d.lead_id=a.lead_id WHERE a.lead_id = $lead_id AND d.order_won_id=$order_won_id";
// echo $query;exit;
$result = mysqli_query($con, $query);
$data = mysqli_fetch_array($result);
$supplier_id = $data['supplier_id'];
$currency_preference = $data['currency_preference'];
$addedon = date('l, F d , Y', strtotime($data['added_on']));

if($data['currency_rate'] > 0) {
	$conversion_currency_rate = $data['currency_rate'];
} else {
	$conversion_currency_rate = 75;
}
$shipment_appl=$data['shipment'];
$shipment_amount=$data['shipment_amt'];
if($shipment_appl==1)
{
	$delivery_type=2;
}else
{
	$delivery_type=1;
}

 //echo $conversion_currency_rate;exit;
/*---------------------------------CASES-------------------------------------------------------------------*/

$sql20 = "SELECT supplier_id FROM suppliers WHERE supplier_id=$supplier_id AND supplier_location=101";
$result20 = mysqli_query($con, $sql20);

if(mysqli_num_rows($result20) > 0) {
	/** in this case supplier is of india **/

	if($currency_preference == 1) {
	/** IF SUPPLIER QUOTE IS INR AND QUOTE PREFERENCE IS ALSO INR SO DO NOTHING**/
		$symbol = '₹';
		$in_words = 'INR';
		$multiplication_factor=1;
		$division_factor=1;

		$b_multiplication_factor=1;
		$b_division_factor=1;
	} else {

	/** IF SUPPLIER QUOTE IS INR AND QUOTE PREFERENCE IS USD SO CONVERT INR TO USD**/
		$symbol = '$';
		$in_words = 'USD';
		$multiplication_factor=1;
		$division_factor=$conversion_currency_rate;

		$b_multiplication_factor=1;
		$b_division_factor=$conversion_currency_rate;
	}

	$indiansupplier=1;
	$country_flag = 2;
} else {

/** in this case supplier is outside india **/

	if($currency_preference == 1) {
	/** IF SUPPLIER QUOTE IS USD AND QUOTE PREFERENCE IS INR SO CONVERT USD TO INR**/

	$symbol = '₹';
	$in_words = 'INR';
	$multiplication_factor=$conversion_currency_rate;
	$division_factor=1;

		$b_multiplication_factor=$conversion_currency_rate;
		$b_division_factor=1;
		
	} else {

		/** IF SUPPLIER QUOTE IS USD AND QUOTE PREFERENCE IS USD SO DO NOTHING**/
		$symbol = '$';
		$in_words = 'USD';
		$multiplication_factor=1;
		$division_factor=1;

		$b_multiplication_factor=1;
		$b_division_factor=1;
	}

	$indiansupplier=0;
	 $country_flag = 1;
}





/** FINAL PRICE FOR THIS QUOTE **/

$sqlfinal = "SELECT final_price,cny_to_usd,inr_to_usd,inr_to_cny FROM prototype_quoted_price WHERE order_won_id = $order_won_id AND supplier_id=$supplier_id";
$resultsfinal = mysqli_query($con, $sqlfinal);
$datasfinalp = mysqli_fetch_array($resultsfinal);
$finalprice = $datasfinalp['final_price'];
$cny_to_usd=$datasfinalp['cny_to_usd'];
$inr_to_usd=$datasfinalp['inr_to_usd'];
$inr_to_cny=$datasfinalp['inr_to_cny'];
// echo $finalprice;exit;

$sql32 = "SELECT id FROM prototype_sourcing_quotation WHERE cny=1 AND order_won_id=$order_won_id AND supplier_id=$supplier_id";
$result32 = mysqli_query($con, $sql32);

if(mysqli_num_rows($result32) > 0) {

		if($indiansupplier==1 && $currency_preference==0)
		{
			// INR TO USD
		$multiplication_factor=1;
		$division_factor=$inr_to_usd;

		}else if($indiansupplier==1 && $currency_preference==1)
		{
		$multiplication_factor=1;
		$division_factor=1;

		}else if($indiansupplier==0 && $currency_preference==0)
		{

			// CNY TO USD
		$multiplication_factor=$cny_to_usd;
		$division_factor=1;
		}else
		{

		$multiplication_factor=1;
		$division_factor=$inr_to_cny;


		}

	} else {
		$b_multiplication_factor=1;
		$b_division_factor=1;
	}


	
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





//echo $multiplication_factor.'<br>'.$division_factor;exit;
/** GET ALL PARTS LOOP AND CALCULATE THE PRICE GIVEN **/

$partprice = array();
$partprice[] = 0;

	$allmouldid = $dataallmould['part_id'];
	$sql12allp = "SELECT amount FROM prototype_sourcing_quotation WHERE order_won_id=$order_won_id AND supplier_id=$supplier_id  ORDER BY id DESC";

	// echo $sql12allp;exit;
	$results12allp = mysqli_query($con, $sql12allp);
	$mouldoptionnumallp = mysqli_num_rows($results12allp);
	if($mouldoptionnumallp>0)
	{
	while( $datas122345allp =mysqli_fetch_array($results12allp))
	{
		if($indiansupplier==1)
		{
		$partprice[] = $datas122345allp['amount']*$inr_to_usd;
		}else
		{
		$partprice[] = $datas122345allp['amount']*$cny_to_usd;
		}
	}		// $mouldoverallweight[] = $datas122345allp['mould_weight'];
	}


$suppliergivenprice = array_sum($partprice);

 

$businesscaseprice = $finalprice;
// $suppliergivenweight = array_sum($mouldoverallweight);



	// if($indiansupplier==1 && $currency_preference==0)
	// {
	// 	$businesscaseprice=$finalprice;
	// 	$suppliergivenprice=$suppliergivenprice;

	// }else if($indiansupplier==1 && $currency_preference==1)
	// {
	// 	// echo $conversion_currency_rate;exit;
	// 	$businesscaseprice=$finalprice*$conversion_currency_rate;
	// 	$suppliergivenprice=$suppliergivenprice;
	// }else if($indiansupplier==0 && $currency_preference==0)
	// {

	// 	$businesscaseprice=$finalprice;
	// 	$suppliergivenprice=$suppliergivenprice;


	// }else
	// {

	// 	$businesscaseprice=$finalprice*$conversion_currency_rate;
	// 	$suppliergivenprice=$suppliergivenprice;

	// }




	//convert to USD AGAIN
//$suppliergivenprice=$suppliergivenprice*$rmb_usd_rate;
// echo $businesscaseprice."<br/>".$suppliergivenprice;exit;

if ($businesscaseprice >= $suppliergivenprice) {

	$diff = $businesscaseprice - $suppliergivenprice;
	//echo $diff; exit;
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


if($indiansupplier==1)
{
	$company_name = 'HONGYI <span style="color:orange;">JIG</span> RAPID TECHNOLOGIES';
	$company_address = '15/1, 2nd Floor, Rama Road Industrial Area Delhi 110015 (INDIA)';
	$logo = '<img src="/var/www/hongyijig.in/pdf/logo.jpeg" width="200px">';

}else
{

$company_name = $company_name;
$company_address = $company_address;
$logo =$logo;

}




$customer_name = $data['customer_name'];
$project_name = $data['project_name'];


$unique_id = $data['unique_id'];
$version = $data['version'];
$added_on = $data['added_on'];
$currency_rate=round($data['currency_rate'],2);
// echo $added_on;exit; 
$addedon = date('l, F d , Y', strtotime($added_on));

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
<br><br>';

$html .= '<br pagebreak="true">
<table style="padding:3px; font-size:11px;" nobr="true">
<tr><td style="color:grey; font-weight:bold; font-size:11px;">LIST OF TOOLS WITH TECHNICAL SPECS</td></tr>
</table>
<table style="padding:3px; font-size:11px; text-align:center;" rules="all" border="1">
<tr style="background-color:lightgrey; font-weight:bold;" nobr="true">
<th>S.No</th>
<th>Part Name<br>Part Picture</th>
<th>Part Weight<span style="font-size:8px;">(BENCHMARKED)</span></th>
<th>QPS<br><span style="font-size:8px;">(Part Quality Per Set)</span></th>
<th>TOTAL QTY</th>
<th>CFM<br><span style="font-size:8px;">Colour/Finish/Material</span></th>
<th>AMOUNT<br/> (in '.$symbol.')</th>
</tr>';

 $sql = "SELECT a.id,a.name,a.mould_id,a.code,a.datatype,a.dataimage,a.partfinish,a.matcode,a.partlength, a.partwidth, a.partheight, a.cavity,a.image, b.remarks, c.qps, c.monthproduction FROM bom_part_details a JOIN prototype_bom b ON b.part_id=a.id LEFT JOIN bom_assembly_qps_detail c ON c.part_id=b.part_id WHERE a.lead_id=$lead_id";
$results = mysqli_query($con, $sql);

$total_part_amount = array();

if (mysqli_num_rows($results) > 0) {
	$i=1;
	while ($datas = mysqli_fetch_array($results)) {

		$part_id = $datas['id'];

		if($datas['partfinish']==1)
            {
                $fin1="High Gloss Mirror";
            }else if($datas['partfinish']==2)
            {
                $fin1="High Gloss";
            }else if($datas['partfinish']==3)
            {
                $fin1="Normal Polish";
            }else if($datas['partfinish']==4)
            {
                $fin1="Texture Matt Finish (".$datas['matcode'].")";
            }else if($datas['partfinish']==5)
            {
                $fin1="High Gloss + Texture";
            }

            if($datas['partlength'] == '') {
                $part_length1 = 0;
            } else {
                $part_length1 = $datas['partlength'];
            }
            if($datas['partwidth'] == '') {
                $part_width1 = 0;
            } else {
                $part_width1 = $datas['partwidth'];
            }

            if($datas['partheight'] == '') {
                $part_height1 = 0;
            } else {
                $part_height1 = $datas['partheight'];
            }

            $sql12 = "SELECT a.color,a.colortype,a.pantonecode,a.actual_shrinkage,b.colourname, c.partname FROM  bom_mould_wise_part_detail a LEFT JOIN part_colour b ON a.color=b.id LEFT JOIN part_master c ON c.id=a.part_material WHERE a.mouldid=".$datas['mould_id'];

            $results12 = mysqli_query($con,$sql12);
            $datas12 = mysqli_fetch_array($results12);
            $color1='';
            if($datas12['colortype']==1)
            {
            $color1=$datas12['colourname'];
            }else
            {
            $color1="Special Color-".$datas12['pantonecode'];
            }
            /** end **/
            $material1 = $datas12['partname'].$datas12['actual_shrinkage'];

            $sql3 = "SELECT amount, remarks FROM prototype_sourcing_quotation WHERE order_won_id=$order_won_id AND supplier_id=$supplier_id AND part_id=$part_id AND part_type=1";
            $result3 = mysqli_query($con,$sql3);
            $datas3 = mysqli_fetch_array($result3);

			if($indiansupplier==1)
			{
				if($currency_preference==1)
				{
				$part_amount = $datas3['amount'];
				}else
				{
				$part_amount = $datas3['amount']*$inr_to_usd;
				}

				$revamp = $percentfactor / 100;
				// echo $revamp;exit;
				$revamp1 = $part_amount * $revamp;
				$part_amount = $part_amount + $revamp1;


			}else
			{

				// O FOR USD 1 FOR INR
				//SUPPLIER NON INDIAN SO QUOTE IN CNY BUT CURRENCY IS IN INR SO CONVERT CNY TO INR 
				if($currency_preference==1)
				{
				$part_amount = $datas3['amount']/$inr_to_cny;
				}else
				{

				// O FOR USD 1 FOR INR
				//SUPPLIER NON INDIAN SO QUOTE IN CNY BUT CURRENCY IS IN USD SO CONVERT CNY TO USD 
				$part_amount = $datas3['amount']*$cny_to_usd;
				}


				$revamp = $percentfactor / 100;
				// echo $revamp;exit;
				$revamp1 = $part_amount * $revamp;
				$part_amount = $part_amount + $revamp1;



			}

           

			$html .= '<tr nobr="true">';
			$html .= '<td>'.$i.'</td>
                    <td>'.$datas['name'].'<br><img src="'.IMAGEPATH.$datas['image'].'" width="100px"></td>
                    <td>L-'.$part_length1.'<br/> W-'.$part_width1.'<br/> H-'.$part_height1.'</td>
                    <td>'.$datas['qps'].'</td>
                    <td>'.$datas['monthproduction'].'</td>
                    <td><strong>Color-</strong>'.$color1.'<br/><strong>Finish-</strong> '.$fin1.'<br/><strong>Material-</strong>'.$material1.'</td>
                    <td>'.$part_amount.'</td>';
			$html .= '</tr>';

			$total_part_amount[] = round($part_amount);

			$i++;

		$mld++;
	}
}

$sql1 = "SELECT a.id as part_id, a.part_name, a.part_picture, a.part_size_length, a.part_size_width, a.part_size_height, a.qps, a.total_qty, a.colour, a.part_color, a.pantoneshade, a.part_material, a.shrinkage, a.actual_shrinkage, a.part_finish, a.matt_code, a.matt_finish_upload, a.remarks, b.colourname, c.partname FROM prototype_bom_add_parts a LEFT JOIN part_colour b ON a.part_color=b.id LEFT JOIN part_master c ON c.id=a.part_material WHERE a.order_won_id=$order_won_id";
$result = mysqli_query($con, $sql1);

if(mysqli_num_rows($result) > 0) {
   $i = $i; 
    while ($data = mysqli_fetch_array($result)) {

        $new_part_id=$data['part_id'];

        $color='';

        if($data['colour']==1) {
            $color = $data['colourname'];
        } else {
            $color = "Special Color-".$data['pantoneshade'];
        }

        $material = $data['partname'].$data['actual_shrinkage'];

        if($data['part_finish']==1)
        {
            $fin="High Gloss Mirror";
        }else if($data['part_finish']==2)
        {
            $fin="High Gloss";
        }else if($data['part_finish']==3)
        {
            $fin="Normal Polish";
        }else if($data['part_finish']==4)
        {
            $fin="Texture Matt Finish (".$data['matt_code'].")";
        }else if($data['part_finish']==5)
        {
            $fin="High Gloss + Texture";
        }

        $sql4 = "SELECT amount, remarks FROM prototype_sourcing_quotation WHERE order_won_id=$order_won_id AND supplier_id=$supplier_id AND part_id=$new_part_id AND part_type=2";
        $result4 = mysqli_query($con,$sql4);
        $datas4 = mysqli_fetch_array($result4);


        if($indiansupplier==1)
			{
				if($currency_preference==1)
				{
				$part_amount = $datas4['amount'];
				}else
				{
				$part_amount = $datas4['amount']*$inr_to_usd;
				}

				$revamp = $percentfactor / 100;
				// echo $revamp;exit;
				$revamp1 = $part_amount * $revamp;
				$part_amount = $part_amount + $revamp1;


			}else
			{

				// O FOR USD 1 FOR INR
				//SUPPLIER NON INDIAN SO QUOTE IN CNY BUT CURRENCY IS IN INR SO CONVERT CNY TO INR 
				if($currency_preference==1)
				{
					//echo $datas4['amount'];
				
				$part_amount = $datas4['amount']/$inr_to_cny;
				}else
				{

				// O FOR USD 1 FOR INR
				//SUPPLIER NON INDIAN SO QUOTE IN CNY BUT CURRENCY IS IN USD SO CONVERT CNY TO USD 
				$part_amount = $datas4['amount']*$cny_to_usd;
				}


				$revamp = $percentfactor / 100;
				// echo $revamp;exit;
				$revamp1 = $part_amount * $revamp;
				$part_amount = $part_amount + $revamp1;



			}



$html .= '<tr>
            <td>'.$i.'</td>
            <td>'.$data['part_name'].'<br><img src="'.image_url.$data['part_picture'].'" width="100px"></td>
            <td>L-'.$data['part_size_length'].'<br/> W-'.$data['part_size_width'].'<br/> H-'.$data['part_size_height'].'</td>
            <td>'.$data['qps'].'</td>
            <td>'.$data['total_qty'].'</td>
            <td><strong>Color-</strong>'.$color.'<br/><strong>Finish-</strong> '.$fin.'<br/><strong>Material-</strong>'.$material.'</td>
            <td>'.$part_amount.'</td>
          </tr>';

          $total_part_amount[] = round($part_amount);
    $i++;
    }
}

// $grand_total_amt = array_sum($priceindollar) + array_sum($total_ser_amt);
if((mysqli_num_rows($result20) == 0) && $delivery_type==2) {
	if(array_sum($mould_weight) <= 1000) {
        $total_mould_weight = 1000;
    } else {
        $total_mould_weight = array_sum($mould_weight);
    }

// $total_shipment = ($total_mould_weight/1000)*$shipment;
$total_shipment = $shipment;

$duties = ((array_sum($total_part_amount)+$total_shipment)*15)/100;
} else{
 $total_shipment = 0;
 $duties = 0;
}

$grand_total_amt = array_sum($total_part_amount) + $shipment_amount;
$html .= '<tr style="background-color:lightgrey; font-weight:bold;" nobr="true">
<td colspan="6" style="text-align:right;">Total</td>
<td>'.$symbol . array_sum($total_part_amount) . '</td>
</tr>';
if($shipment_appl==1) {
$html .= '<tr style="background-color:lightgrey; font-weight:bold;" nobr="true">
<td colspan="5" style="text-align:right;">(Approx) Shipment
</td>
<td></td>
<td>'.$symbol."".floatval($shipment_amount).'</td>
</tr>';
}
$html .= '<tr style="background-color:lightgrey; font-weight:bold;" nobr="true">
<td colspan="5" style="text-align:right;">Grand Total In '.$in_words.'</td>
<td></td>
<td>'.$symbol.round($grand_total_amt).'</td>
</tr>
<tr style="background-color:#eeeeee; font-weight:bold;" nobr="true">
<td colspan="10">AMOUNT IN WORDS:'.$in_words.' '. ucwords(getCurrencyCode($grand_total_amt)) . ' Only</td>
</tr>';

if($currency_preference == 0)
{
	$inrtotal=$grand_total_amt*$currency_rate;
	$html.='<tr style="background-color:lightgrey; font-weight:bold;" nobr="true">
	<td colspan="5" style="text-align:right;">Grand Total In INR</td>
	<td></td>
	<td>₹ '.round($inrtotal).'</td>
	</tr>
	<tr style="background-color:#eeeeee; font-weight:bold;" nobr="true">
	<td colspan="10">AMOUNT IN WORDS: Rs. ' . ucwords(getCurrencyCode($inrtotal)) . ' Only</td>
	</tr>';
}

$html.='</table>';

if($currency_preference == 0)
{
$html.='<br><br><table style="padding:3px; font-size:11px;">
<tr nobr="true"><td style="">Please consider the currency exchange rate of the day at the time of making Bank Transfer through RBI of each stage. The currency rate at the time of Quotation Generated is '.number_format($currency_rate,2).' / US$, so the Total Value of US $'.$grand_total_amt.'  is Rs. '.$inrtotal.'/-<br/><br/>All the upcoming payments will be made basis on the currency exchange price of US Dollar on the day of Payment Made.</td></tr>
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
<th style="width:77px;">AMOUNT<br/>(in '.$symbol.')</th>
</tr>';
$sql14 = "SELECT final_price FROM client_quoted_price WHERE lead_id = $lead_id AND option_id=$option_id AND supplier_id=$supplier";
$results14 = mysqli_query($con, $sql14);
$datas14 = mysqli_fetch_array($results14);

$sql13 = "SELECT payment_stage, stage, percentage FROM prototype_cust_payment_terms WHERE order_won_id = $order_won_id";
//echo $sql13; exit;
$results13 = mysqli_query($con, $sql13);
$total_amount = array();

// echo array_sum($total_ser_amt);exit;
// $final_price = $datas14['final_price'];
// echo $final_price;exit;
// $final_price = array_sum($priceindollar) + array_sum($total_ser_amt);
$final_price = $grand_total_amt;
$i = 0;
while ($datas13 = mysqli_fetch_array($results13)) {

	// $percentage = $datas13['percentage'];
	// $amount = ($final_price * $percentage) / 100;
	$percentage = $datas13['percentage'];
	if($i==0) {
	  $amount = (($percentage*array_sum($total_part_amount))/100);
	} else {
	  $amount = (($percentage*array_sum($total_part_amount))/100);
	}
	$html .= '<tr style="text-align:center; ">
	<td style="font-weight:bold; text-align:right; background-color:white;">' . $datas13['payment_stage'] . '</td>
	<td style="background-color:white;">' . $datas13['stage'] . '</td>
	<td style="background-color:white;">' . $percentage . '%</td>
	<td style="background-color:white;">' .$symbol. round($amount) . '</td>
	</tr>';
	$total_amount[] = $amount;
	$i++;
}


if ($shipment_appl==1) {
$shipment_clearance = $shipment_amount;
$html .= '<tr style="text-align:center;">
			<td>Shipment</td>
			<td>5 Days Before Moulds Reach to destination Port</td>
			<td></td>
			<td>'.$symbol.round($shipment_clearance).'</td>
		  </tr>';
} else {
	$shipment_clearance = 0;
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

// if ($delivery_type == 2) {
// $html .= '<tr style="background-color:lightgrey; text-align:center;">
// <td colspan="3" style="font-weight:bold; text-align:right;">SHIPMENT & CLEARANCE CHARGES</td>
// <td>As per actual</td>
// </tr>';
// }

$html .= '<tr style="background-color:lightgrey; text-align:center;">
<td colspan="3" style="font-weight:bold; text-align:right;">TOTAL</td>
<td>'.$symbol. round($finalgrategrandtotal+$shipment_clearance) . '</td>
</tr>
</table>


</td>
</tr>
</table>
<br><br>';

$sql20 = "SELECT description FROM termsandconditions WHERE supplier_type=2 AND category=1 AND country_flag=$country_flag";
$result = mysqli_query($con, $sql20);

$terms_conditions = '';

if (mysqli_num_rows($result) > 0) {
    $data = mysqli_fetch_array($result);
    $terms_conditions = $data['description'];
}



$html.='<table style="padding:3px; font-size:11px;">
<tr nobr="true"><td style="color:grey; font-weight:bold; font-size:11px;">TERMS & CONDITIONS

</td></tr>
<tr>
<td style="color:grey; ">Common Mandatory Terms and Conditions
</td></tr>
</table><br/>
 <table style="font-size:10px;">
    <tr>
    <td style="border-bottom:1px solid black; height:120px;"><span style="font-weight:bold;">BASIC TERMS</span><br><i>'.$terms_conditions.'
    </td>
    </tr>
    </table>';

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


$sql19 = "SELECT assign_to FROM lead_meeting_schedule WHERE lead_id = $lead_id";
$results19 = mysqli_query($con, $sql19);
$data19 = mysqli_fetch_array($results19);
$member_id = $data19['assign_to'];
if($member_id == 0 || $member_id == '') {

$sql17 = "SELECT member_id FROM lead_assigned_to_team_member WHERE lead_id = $lead_id";
$results17 = mysqli_query($con, $sql17);
$data17 = mysqli_fetch_array($results17);
$member_id = $data17['member_id'];
$title = 'Sales Manager';
} else {
	$member_id = $member_id;
	$title = 'Business Development Manager';
}


$sql18 = "SELECT first_name, last_name FROM  system_users WHERE user_id = $member_id";;
//echo $sql2; exit;
$result18 = mysqli_query($con, $sql18);
$data18 = mysqli_fetch_array($result18);
//echo "<pre>"; print_r($datass); exit;
$name = $data18["first_name"] . ' ' . $data18["last_name"];
$html .= '<p style="font-size:11px;">' . $name . '</p>
		  <p style="font-size:11px;">'.$title.'</p>
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
