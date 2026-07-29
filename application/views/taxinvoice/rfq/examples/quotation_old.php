<?php
/** QUERY ENDS **/
define('image_url','https://hongyijig.in/assets/client_bom_data/part_image/');
include('mysqlconfig.php');

$lead_id=$_GET['lead_id'];
$option_id=$_GET['option'];
$supplier=$_GET['suplier'];


	$sql12 = "SELECT * FROM client_quoted_price WHERE option_id=$option_id AND part_id = $id AND lead_id=$lead_id";
	//echo $sql12;exit;
	$results12 = mysqli_query($con,$sql12);
	$datas12 = mysqli_fetch_array($results12);


/** FINAL PRICE FOR THIS QUOTE **/

$sqlfinal = "SELECT final_price FROM client_quoted_price WHERE lead_id = $lead_id AND option_id=$option_id AND supplier_id=$supplier";
	$resultsfinal = mysqli_query($con,$sqlfinal);
	$datasfinalp = mysqli_fetch_array($resultsfinal);
	$finalprice=$datasfinalp['final_price'];


	/** GET ALL PARTS LOOP AND CALCULATE THE PRICE GIVEN **/

$allparts="SELECT id FROM bom_part_details WHERE lead_id=$lead_id";
$resultsallpart = mysqli_query($con,$allparts);
$partprice=array();
$partprice[]=0;
$mouldoverallweight=array();
$mouldoverallweight[]=0;
	while($dataallpart = mysqli_fetch_array($resultsallpart))
	{
					$allpartid=$dataallpart['id'];
					$sql12allp = "SELECT mould_weight,price_in_dollar FROM sourcing_quotation WHERE option_id=$option_id AND part_id = $allpartid AND lead_id=$lead_id AND supplier_id='$supplier'";

					$results12allp = mysqli_query($con,$sql12allp);
					$mouldoptionnumallp=mysqli_num_rows($results12allp);
					if($mouldoptionnumallp>0)
					{
					$datas122345allp = mysqli_fetch_array($results12allp);
					$partprice[]=$datas122345allp['price_in_dollar'];
					$mouldoverallweight[]=$datas122345allp['mould_weight'];
					}else
					{

					$sql12zeroallp = "SELECT mould_weight,price_in_dollar FROM sourcing_quotation WHERE option_id=0 AND part_id = $allpartid AND lead_id=$lead_id AND supplier_id='$supplier'";

					$results12zeroallp = mysqli_query($con,$sql12zeroallp);
					$datas122345zalpp = mysqli_fetch_array($results12zeroallp);

					$partprice[]=$datas122345zalpp['price_in_dollar'];
					$mouldoverallweight[]=$datas122345zalpp['mould_weight'];



					}


	}


$suppliergivenprice=array_sum($partprice);
$businesscaseprice=$finalprice;
$suppliergivenweight=array_sum($mouldoverallweight);
//echo $suppliergivenprice."<br/>".$businesscaseprice; exit;
if($suppliergivenprice<$businesscaseprice)
{

$diff=$businesscaseprice-$suppliergivenprice;
$getpercentage=$diff*100;
$getextraper=$getpercentage/$suppliergivenprice; 

$percentfactor=ceil($getextraper);

}else
{
	echo "BUSINESS CASE PRICE IS LESS THAN SUPPLIER PRICE! HENCE QUOTATION CANNOT BE CREATED"; exit;
}

	/** END **/



$query = "SELECT b.company_name,a.delivery_type, b.customer_name, b.unique_id, c.project_name, d.version, d.added_on,c.billing_address FROM bom_pi_sales_project_info a LEFT JOIN leads b ON b.id=a.lead_id LEFT JOIN bom_pi_sales_client_info c ON c.lead_id=a.lead_id LEFT JOIN client_quoted_price d ON d.lead_id=a.lead_id WHERE a.lead_id = $lead_id";
$result = mysqli_query($con,$query);
$data = mysqli_fetch_array($result);

function getCurrencyCode($grandtot) {
	/* CURRENCY CODE **/
	$number=floatval($grandtot);
	$decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'one', 2 => 'two',
        3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
        7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve',
        13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
        16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
        19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
        40 => 'forty', 50 => 'fifty', 60 => 'sixty',
        70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
    $digits = array('', 'hundred','thousand','lakh', 'crore');

    while( $i < $digits_length ) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
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
   $words=$rupees.'rupees '. $paise ;
	
	/* END **/
}

require_once('tcpdf_include.php');

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

/** quert paet **/
$delivery_type = $data['delivery_type']; 

	if($delivery_type == 1) {
		$company_name = 'HONGYI JIG RAPID TECHNOLOGIES CO., LTD.';
		$company_address = 'Unit H 1/F Mau Lam Comm Bldg16-18 Mau Lam St Jordan Kln, HK';
		$logo = '<img src="https://hongyijig.in/pdf/internallogo.jpeg" width="200px">';
	} else {
		$company_name = 'HONGYI JIG RAPID TECHNOLOGIES';
		$company_address = '15/1, 2nd Floor, Rama Road Industrial Area Delhi 110015 (INDIA)';
		$logo = '<img src="https://hongyijig.in/pdf/logo.jpeg" width="200px">';
	}

$customer_name = $data['customer_name']; 
$project_name = $data['project_name']; 
$unique_id = $data['unique_id']; 
$version = $data['version']; 
$added_on = $data['added_on'];
// echo $added_on;exit; 
$addedon = date('l, F d , Y', strtotime($added_on));
if($delivery_type=='1'){
	$fob = "FOB";
}else{
	$fob="";
}

$html='

<table style="padding:3px; font-size:11px;">
<tr><td>'.$logo.'</td></tr>
<tr><td>'.$company_name.'</td></tr>
<tr><td>'.$company_address.'</td></tr>
<tr><td style="border-bottom:1px solid black;">www.hongyijig.com</td></tr>
</table>
<br><br><br><br>
<br><br><br><br><br><br><br>
<table style="padding:3px; font-size:11px; text-align:center;">
<tr><td style="color:grey; font-weight:bold;">Kind Attention to
</td></tr>
<tr><td style="color:black; font-weight:bold;">Mr. '.$customer_name.'</td></tr>

</table>

<br><br><br><br>
<br><br><br><br><br><br>
<table style="padding:3px; font-size:11px; text-align:center;">
<tr><td style="color:grey; font-weight:bold;">Techno-Commercial Quotation for</td></tr>
<tr><td style="color:black; font-weight:bold;">'.$project_name.'
</td></tr>

</table>

<br><br><br><br>
<br><br><br><br><br><br>

<table style="padding:3px; font-size:11px; text-align:center;">
<tr><td style="color:grey; font-weight:bold;">Version</td></tr>

<tr><td style="color:grey; ">REF-'.$unique_id.'/'.$version.'.00</td></tr>
</table>



<br><br><br><br>
<br><br><br><br><br><br>
<table style="padding:3px; font-size:11px; text-align:center;">
<tr><td style="color:grey; font-weight:bold;">Date</td></tr>
<tr><td style="color:black; font-weight:bold;">'.$addedon.'</td></tr>

</table>
<br><br><br><br>
<table style="padding:3px; font-size:11px;">
<tr><td style="color:grey; font-weight:bold;">Introduction</td></tr>
<tr><td style="color:grey; ">Project & Scope of Work</td></tr>
</table>
<table style="padding:3px; font-size:11px;">
<tr>
<th style="text-align:center; background-color:lightgrey; border:1px solid black; font-weight:bold;">PROJECT DETAILS</th>
<th style="text-align:center; background-color:lightgrey; border:1px solid black; font-weight:bold;">SCOPE OF WORK</th>
</tr>
<tr>
<td style="border:1px solid black;">'.$project_name.'</td>
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
<tr><td style="color:grey; font-weight:bold;">List of Tools with Technical Specs</td></tr>
<tr><td style="color:grey; ">Project & Scope of Work</td></tr>
</table>
<table style="padding:3px; font-size:11px; text-align:center;" rules="all" border="1">
<tr style="background-color:lightgrey; font-weight:bold;">
<th style="width:40px;">S.No</th>
<th style="width:100px;">Part Name<br>Part Picture<br>Part Size<br/>Cavity</th>
<th style="width:130px;">Part CFM</th>
<th>Runner Type/Make</th>
<th>Mould Sizes(mm)</th>
<th>Mould Material</th>
<th>Weight in Kgs.</th>
<th>Cost '.$fob.'(USD)</th>
</tr>';

$sql = "SELECT id, mould_id, name, image, partfinish, partlength, partheight, partwidth, cavity FROM bom_part_details WHERE lead_id = $lead_id";
$results = mysqli_query($con,$sql);
$i = 1;
$mould_weight = array();
$priceindollar = array();

while($datas = mysqli_fetch_array($results)) {
	//echo "<pre>"; print_r($datas);
$id = $datas['id'];
$mouldid = $datas['mould_id'];

$sql1 = "SELECT a.color,a.colortype,a.pantonecode,a.actual_shrinkage,b.colourname, c.partname FROM  bom_mould_wise_part_detail a LEFT JOIN part_colour b ON a.color=b.id LEFT JOIN part_master c ON c.id=a.part_material WHERE a.mouldid = $mouldid";

$results1 = mysqli_query($con,$sql1);
$datas1 = mysqli_fetch_array($results1);
//echo "<pre>"; print_r($datas1); exit;
$color='';
if($datas1['colortype']==1)
{
$color=$datas1['colourname'];
}else
{
$color="Special Color-".$datas1['pantonecode'];
}

$material = $datas1['partname'].$datas1['actual_shrinkage'];

if($datas['partfinish']==1)
	{
		$fin="High Gloss Mirror";
	}else if($datas['partfinish']==2)
	{
		$fin="High Gloss";
	}else if($datas['partfinish']==3)
	{
		$fin="Normal Polish";
	}else if($datas['partfinish']==4)
	{
		$fin="Texture Matt Finish (".$data['matcode'].")";
	}


	if($datas['partlength'] == '') {
		$part_length = 0;
	} else {
		$part_length = $datas['partlength'];
	}
	if($datas['partwidth'] == '') {
		$part_width = 0;
	} else {
		$part_width = $datas['partwidth'];
	}

	if($datas['partheight'] == '') {
		$part_height = 0;
	} else {
		$part_height = $datas['partheight'];
	}

	$sql11 = "SELECT b.name as core_name, a.runner_type,e.name as gatingname,a.gating_type,a.tips,c.name as cavity_name, a.mould_base_steel,a.core_steel,a.cavity_steel, a.insertmoulding, a.cadweight, d.name as runner_name FROM bom_part_additional_details a LEFT JOIN core_steel b ON a.core_steel=b.id LEFT JOIN cavity_steel c ON c.id=a.cavity_steel LEFT JOIN runner_type d ON d.id=a.runner_type  LEFT JOIN gating_type e ON a.gating_type=e.id WHERE a.partdetailid = $id";
	//echo $sql11;exit;
	$results11 = mysqli_query($con,$sql11);
	$datas11 = mysqli_fetch_array($results11);


	if($datas11['runner_type']==1)
	{
		$runname="Hot Runner";
		$gating="<strong>Gating</strong><br/>".$datas11['gatingname'];
		$tips="<strong>Tips</strong><br/>".$datas11['tips'];


	}else if($datas11['runner_type']==2)
	{	
		$runname="Cold Runner";
		$gating="<strong>Gating</strong><br/>".$datas11['gatingname'];
		$tips='';

	}else
	{
		$runname="";
		$gating='';
		$tips='';

	}


//echo "<pre>"; print_r($datas11); exit;
	$sql12 = "SELECT mould_size, mould_weight,price_in_dollar FROM sourcing_quotation WHERE option_id=$option_id AND part_id = $id AND lead_id=$lead_id AND supplier_id='$supplier'";

	$results12 = mysqli_query($con,$sql12);
	$mouldoptionnum=mysqli_num_rows($results12);
	if($mouldoptionnum>0)
	{
	$datas122345 = mysqli_fetch_array($results12);
	$mouldsize=$datas122345['mould_size'];
	$mouldweight=$datas122345['mould_weight'];
	$priceinusd=$datas122345['price_in_dollar'];
	$revamp=$percentfactor/100;
	$revamp1=$priceinusd*$revamp;
	$priceinusd=$priceinusd+$revamp1;
	}else
	{

		$sql12zero = "SELECT mould_size, mould_weight,price_in_dollar FROM sourcing_quotation WHERE option_id=0 AND part_id = $id AND lead_id=$lead_id AND supplier_id='$supplier'";

		$results12zero = mysqli_query($con,$sql12zero);
		$datas122345z = mysqli_fetch_array($results12zero);
		$mouldsize=$datas122345z['mould_size'];
		$mouldweight=$datas122345z['mould_weight'];
		$priceinusd=$datas122345z['price_in_dollar'];
		$revamp=$percentfactor/100;
		$revamp1=$priceinusd*$revamp;
		$priceinusd=$priceinusd+$revamp1;



	}


	


	/** MOULD BASE STEEL **/
		$sql1211 = "SELECT a.mouldbasesteel,b.material_grade,c.manufacturer_name FROM bom_moulds_detail a JOIN steel_type b ON a.mouldbasesteel=b.id JOIN manufacturer c ON b.manufacturer=c.id WHERE a.id = $mouldid";

		
		$results1211 = mysqli_query($con,$sql1211);
		$datas1211 = mysqli_fetch_array($results1211);

		$steelname=$datas1211['material_grade'];
		$manufacturername=$data1211['manufacturer_name'];
		
		
	/** END **/


	/** CHECK IF RUNNER OR STEEL HAS BEEN DIFFERENT **/

	if($option_id<>0)
	{
	$checkforother="SELECT type,runner_type,runner_brand,gating,tips,steelbrand,steel FROM bom_mouldwise_options WHERE lead_id='$lead_id' AND mould_id='$mouldid'";
	$resultsother = mysqli_query($con,$checkforother);
	$numrowother=mysqli_num_rows($resultsother);
	if($numrowother>0)
	{
		$otherchanged= mysqli_fetch_array($resultsother);
		if($otherchanged['type']==1)
		{
			$gateid=$otherchanged['gating'];
			if($otherchanged['runner_type']==1)
				{
					$runname="HOT RUNNER";
					$tips=$otherchanged['tips'];
				}else
				{
					$runname="COLD RUNNER";
					$tips='';

				}

				$gate="SELECT name FROM gating_type WHERE id='$gateid'";
				$forgatether = mysqli_query($con,$gate);
				$gatename= mysqli_fetch_array($forgatether);

				$gating=$gatename['name'];

				

		}else if($otherchanged['type']==2)
		{	
			/** FOR STEEL AND BRAND **/

			$steelbrand=$otherchanged['steelbrand'];
			$steel=$otherchanged['steel'];

			$steelbr="SELECT manufacturer_name FROM manufacturer WHERE id='$steelbrand'";
			$forsteelbther = mysqli_query($con,$steelbr);
			$steelbname= mysqli_fetch_array($forsteelbther);

			$manufacturername=$steelbname['manufacturer_name'];


			$steelbr="SELECT material_grade FROM steel_type WHERE id='$steel'";
			$forsteelbther = mysqli_query($con,$steelbr);
			$steelbname= mysqli_fetch_array($forsteelbther);
			$steelname=$steelbname['material_grade'];

		}else
		{

			$steelbrand=$otherchanged['steelbrand'];
			$steel=$otherchanged['steel'];

			$steelbr="SELECT manufacturer_name FROM manufacturer WHERE id='$steelbrand'";
			$forsteelbther = mysqli_query($con,$steelbr);
			$steelbname= mysqli_fetch_array($forsteelbther);

			$manufacturername=$steelbname['manufacturer_name'];


			$steelbr="SELECT material_grade FROM steel_type WHERE id='$steel'";
			$forsteelbther = mysqli_query($con,$steelbr);
			$steelbname= mysqli_fetch_array($forsteelbther);
			$steelname=$steelbname['material_grade'];


			$gateid=$otherchanged['gating'];
			if($otherchanged['runner_type']==1)
				{
					$runname="HOT RUNNER";
					$tips=$otherchanged['tips'];
				}else
				{
					$runname="COLD RUNNER";
					$tips='';

				}

				$gate="SELECT name FROM gating_type WHERE id='$gateid'";
				$forgatether = mysqli_query($con,$gate);
				$gatename= mysqli_fetch_array($forgatether);

				$gating=$gatename['name'];

		}
	}
	//$datasother = mysqli_fetch_array($resultsother);

	}

	/** END **/

$html .= '<tr>
<td>'.$i.$mouldid.'</td>
<td>'.$datas['name'].'<br><img src="'.image_url.$datas['image'].'" width="100px"><br>Size: '.$part_length.'x'.$part_width.'x'.$part_height.'<br/>Cavity:'.$datas['cavity'].'</td>
<td><strong>Part Colour</strong><br/>'.$color.'<br/><br/>
<strong>Surface Finish</strong><br/>'.$fin.'<br/><br/>
<strong>Material</strong><br/>'.$material.'</td>
<td><strong>Runner</strong><br/>'.$runname.'<br/><br/>'.$gating.'<br/><br/>'.$tips.'</td>
<td>'.$mouldsize.'mm</td>
<td>'.$manufacturername."<br>".$steelname.'</td>
<td>'.$mouldweight.'</td>
<td>$'.$priceinusd.'</td>
</tr>';
$i++;
$mould_weight[] = $mouldweight;
$priceindollar[] = $priceinusd;
}

$html .= '<tr style="background-color:lightgrey; font-weight:bold;">
<td colspan="6" style="text-align:right;">Total</td>
<td> '.array_sum($mould_weight).' Kgs</td>
<td>$'.array_sum($priceindollar).'</td>
</tr>';
$html .= '<tr style="background-color:#eeeeee; font-weight:bold;">
<td colspan="11">AMOUNT IN WORDS: USD '.ucwords(getCurrencyCode(array_sum($priceindollar))).' Only</td>
</tr>
</table>
<br><br>
<div style="page-break-before:always">&nbsp;</div> 
<table style="padding:3px; font-size:11px;">
<tr><td style="color:grey; font-weight:bold;">Commercial Transactions</td></tr>
<tr><td style="color:grey; ">Payment Milestones and Bank Details</td></tr>
</table>




<table style=" font-size:11px; ">

<tr>
<td style="width:294px;">
<table border="1" style="padding:3px;">
<tr style="background-color:lightgrey; font-weight:bold; text-align:center;">
<th colspan="2">Account Details </th>
</tr>';
if($delivery_type == 1) {
$html .= '<tr style="font-weight:bold;">
<td style="text-align:right; width:100px;">Account Name</td>
<td style="text-align:left; width:187px;">HONGYI JIG RAPID TECHNOLOGIES CO., LTD</td>
</tr>
<tr style="">
<td style="text-align:right;font-weight:bold;">Account No.</td>
<td style="text-align:left;">048-857452-838</td>
</tr>
<tr style="">
<td style="text-align:right;font-weight:bold;">Beneficiary Bank</td>
<td style="text-align:left;">HSBC HONG KONG BANK </td>
</tr>
<tr style="">
<td style="text-align:right;font-weight:bold;">Bank Address</td>
<td style="text-align:left;">1 QUEEN,S ROAD, CENTRAL, HONG KONG</td>
</tr>
<tr style="">
<td style="text-align:right;font-weight:bold;">SWIFT CODE</td>
<td style="text-align:left;">HSBCHKHHHKH</td>
</tr>';
} else {
	$html .= '<tr style="font-weight:bold;">
				<td style="text-align:right; width:100px;">Account Name</td>
				<td style="text-align:left; width:187px;"> HONGYI JIG RAPID TECHNOLOGIES</td>
				</tr>
				<tr style="">
				<td style="text-align:right;font-weight:bold;">Account No.</td>
				<td style="text-align:left;"> 2016201002983</td>
				</tr>
				<tr style="">
				<td style="text-align:right;font-weight:bold;">Beneficiary Bank</td>
				<td style="text-align:left;">CANARA BANK</td>
				</tr>
				<tr style="">
				<td style="text-align:right;font-weight:bold;">Bank Address</td>
				<td style="text-align:left;">KESHAV PURAM DELHI</td>
				</tr>
				<tr style="">
				<td style="text-align:right;font-weight:bold;">IFSC CODE</td>
				<td style="text-align:left;">CNRB0002016</td>
				</tr>
				<tr style="">
				<td style="text-align:right;font-weight:bold;">MICR</td>
				<td style="text-align:left;">110015072</td>
				</tr>';
}

$html .= '</table>

</td>

<td >
<table border="1" style="padding:3px;">
<tr style="background-color:lightgrey; font-weight:bold; text-align:center;">
<th style="width:100px;">PAYMENT STAGE</th>
<th style="width:100px;">STAGE</th>
<th>%</th>
<th style="width:70px;">AMOUNT</th>
</tr>';

	$sql14 = "SELECT final_price FROM client_quoted_price WHERE lead_id = $lead_id AND option_id=$option_id AND supplier_id=$supplier";
	$results14 = mysqli_query($con,$sql14);
	$datas14 = mysqli_fetch_array($results14);

	$sql13 = "SELECT payment_stage, stage, percentage FROM payment_terms WHERE lead_id = $lead_id";
	//echo $sql13; exit;
	$results13 = mysqli_query($con,$sql13);
	$total_amount = array();

	$final_price = $datas14['final_price'];
		
	while($datas13 = mysqli_fetch_array($results13)) {

		$percentage = $datas13['percentage'];
		$amount = ($final_price * $percentage)/100;
	$html .= '<tr style="text-align:center;">
	<td style="font-weight:bold; text-align:right;">'.$datas13['payment_stage'].'</td>
	<td>'.$datas13['stage'].'</td>
	<td>'.$percentage.'%</td>
	<td>'.$amount.'</td>
	</tr>';
	$total_amount[] = $amount;
}

if($delivery_type==2){
	$sql131 = "SELECT final_price, supplier_id FROM client_quoted_price WHERE lead_id = $lead_id";
	$results131 = mysqli_query($con,$sql131);
	while($datas131 = mysqli_fetch_array($results131)){
	
	$supplierid = $datas131['supplier_id'];
	}
	// $sql1311 = "SELECT mould_weight FROM sourcing_quotation WHERE supplier_id = $supplierid";
	// $results1311 = mysqli_query($con,$sql1311);
	// while($datas1311 = mysqli_fetch_array($results1311)){
	// 	$mouldweightdata[] = $datas1311['mould_weight'];
	// }
	$valuetotal = array_sum($total_amount);


	$mould_weight_data = $suppliergivenweight; 
	$pertonvalue = 150;
	  $seafreight = ($mould_weight_data/$valuetotal)*$pertonvalue;	
$customvalue = ($valuetotal+$seafreight)*0.28;	
$clearance = 400;
$grandtotal = ceil($valuetotal+$seafreight+$customvalue+$clearance);  
$finalgrategrandtotal = $valuetotal+$grandtotal;
}else{
	$finalgrategrandtotal=$valuetotal;
	$grandtotal="0";
}			
$html .= '<tr style="background-color:lightgrey; text-align:center;">
<td colspan="3" style="font-weight:bold; text-align:right;">SHIPMENT CHARGES</td>
<td>$'.round($grandtotal).'</td>
</tr>
<tr style="background-color:lightgrey; text-align:center;">
<td colspan="2" style="font-weight:bold; text-align:right;">TOTAL</td>
<td>100%</td>
<td>$'.round($finalgrategrandtotal).'</td>
</tr>
</table>


</td>
</tr>
</table>
<br><br>
<table style="padding:3px; font-size:11px;">
<tr><td style="color:grey; font-weight:bold;">Roles & Responsibilities
</td></tr>
<tr><td style="color:grey; ">Understanding the Key responsibilities with customer
</td></tr>
</table>
<table style="padding:3px; font-size:11px;" border="1">
<tr>
<th style="text-align:center; background-color:lightgrey; border:1px solid black; font-weight:bold;">ROLES & RESPONSIBILITIES HONGYI JIG </th>
<th style="text-align:center; background-color:lightgrey; border:1px solid black; font-weight:bold;">ROLES & RESPONSIBILITIES CUSTOMER</th>
</tr>
<tr>
<td >
<ol>
<li>HJIG will make the Prototype and make the final
checking on it with Client.
</li>
<li> HJIG will take responsibility for providing the tool
design.
</li>
<li>Hongyi JIG will take active participation in criticizing
the tool design to increase the quality and good mold
life.
</li>
<li>HJIG will be responsible for time bound delivery and
quality of the services of each stage.</li>
<li>HJIG will provide a weekly report with the pictures to
communicate the manufacturing progress details
</li>
<li> HJIG will arrange trails of tools in China. </li>
<li>HJIG Project Leader will have video/Interctive/ Face
to Face meetings with client for all technical
discussions
</li>
<li>HJIG Project Leader will have video/Interctive/ Face
to Face meetings with client for all technical
discussions
</li>
</ol>
</td>
<td>
<ol>
<li>Client will check the Prototype, Will not make any design
changes after final design submission.
</li>
<li>Client will provide us the 3D drawings, 2D drawings CFM
Details, GD&T along with critical dimensions and Tolerance
Standard in one go. 
</li>
<li>Client will provide the reference samples and details of child
parts for the standards fitment.
</li>
<li> The part data is being provided by client so if there is any
change in Part design after finalization of the designs and
during tool development stage, the modification will be
chargeable</li>
<li>All inputs related to project i.e. technical information,
dimensional tolerances, Deviations, Physical samples related to
sample approval criteria will be defined in one brain storming
meeting before kicking off the project. </li>
<li>All feedbacks on the Trial samples will be given by client
</li>
<li>Client will finally approve the trial samples and moulds before
delivery 
</li>
<li>Client will provide the 5 sets of BOPs on its own cost to
China. Incase if HJIG takes the responsibility the shipment cost
will be additional
</li>
</ol>
</td>
</tr>
</table>
<br><br>
<table style="padding:3px; font-size:11px;">
<tr><td style="color:grey; font-weight:bold;">Terms & conditions 

</td></tr>
<tr><td style="color:grey; ">Common Mandetory Terms and Conditions
</td></tr>
</table>

<table style="width:100%; padding:3px; font-size:11px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
BASIC TERMS 
</td>
</tr>
</table>
<ol style="font-size:11px;">
<li>Any difference in scope of work by means of engineering effort,or number of moulds at the time of project kick off
and after Part design Sign off is subjected to a revised cost of tooling. Customer has a right to compare the revised
prices with the competitive prices from the same destination of tool makers. Do not compare the Indian tooling cost
with the imported tools. 
</li>
<li>Customer will provide all the inputs regarding project in advance before tooling kick off as below
<ul>
<li>Express minimum 5 Set of BOPs in advance before project starts to China at client’s cost.</li>
<li>Express minimum 5 sets of Child parts and fitment parts if any before project starts, to China at client’s
cost. 
</li>
<li>Incase of special Raw Material which is not available in China, client will send the desire quantity of RM
for trials/ Batch Production or Production purpose. 
</li>
<li>Any BOPs/Child parts or fasteners which need to be insert moulded in during the trial, will be supplied
by client 1 month before the trial date.</li>
</ul>
</li>
<li>All new Product Designs are mandatory to send for functional prototyping before tooling kick off. Any decision for
not going for prototyping and proceeding for tooling will be at customer’s risk. Due to this any ECN or design fault
caused tooling ECN will be chargeable and paid by the customer. </li>
<li> Prototype cost will be separate and can be quoted once Part engineering designs are ready. After Prototyping,
customer will approve & sign off the prototype and engineering design. No changes will be made after design sign
off.
</li>
<li>All the communication and conversation will be through Email / Whatsapp Group during office Time, i.e. 8:00 am to
5:00 PM. Any doubts or questions asked will be replied with in 24 hours in between the office timing working days</li>
<li>Any special Requirement need to be mentioned during Bill of Materials finalisation / Order finalisation, Any request
or change in scope of work, given during project running will be chargeable if it has incured any commercial value</li>
<li>Negotiations are welcomed before finalisation of order subjected to have competetive prices from the same
overseas Destination companies of Tool maker</li>
<li>All the requests related to required service at any stage must be addressed before order punching. During the
project running, NO additional request will be acceptable or may cost a proffessional fee.</li>
<li>The services related to product design & engineering are subjected to number of days mentioned in quote and are
subjected to per day labour cost & proffessional Fee. Any revision or extention will subjected to additional cost or
may consider to change in Scope of Business</li>
<li>The BOM offered at the time of quotation may be revised after Product Engineering Design Finalisation. Both
parties will review the BOM and Price after the Engineering Design or Design Change activities. Commercial may
revise at this stage. </li>
<li>All the prices are based on the described BOM. Any change in the specifications of Part or Mould will cause the
commercial change
</li>
</ol>

<table style="width:100%; padding:3px; font-size:11px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
ORDER & ORDER CONFIRMATION
</td>
</tr>
</table>
<ol style="font-size:11px;">
<li>Orders are subjected to payment realisation of advance payment by Cheque / DD / Cash / RTGS / TT as per
"Payment Terms".
</li>
<li>No other terms & Conditions will be acceptable, in case if those are not negotiated and mentioned in this
Agreement and acknowledged by the Client before. 
</li>
<li>The Copy of This proposal / Agreement sent by email will be considered "Acceptable" if no objection has been
raised in reply before the advance raised.</li>
<li>Quotation validity is 10 days.
</li>
<li>Orders will be executed based on all the required information/ data in one go (On Full Kitting Sheet). Any delay in
the information/data or revised information will be subjected to delay and the lead time will be accounted from the
last information delivery date. </li>
</ol>
<table style="width:100%; padding:3px; font-size:11px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
TRIALS, TESTING & BATCH PRODUCTION
</td>
</tr>
</table>
<ol style="font-size:11px;">
<li>There will be 10 shots batch production of the molds which will be done exclusively to check the tool/mold quality.
In case if customer wish to ship this batch production, the shipment is EXW and cost of shipment and custom taxes
will be borne by customer.
</li>
<li> All the feedback of trailed samples will be given in one go from customer. No partial requests of tool corrections will
be acceptable. 
</li>
<li>Any ECN or design change after the Tooling kick off will be chargeable and paid by the customer. If there will any
design change it will effect the delivery schedules as committed.
</li>
<li> Customer will pre-define the inspection process for the mould trial samples. Any additional information or new
criteria of inspection will not be entertained once tooling will be started. This means you need to fix the inspection
process & material specification as a protocol in the beginning through a black & white document. 
</li>
<li>Any additional information or new criteria of inspection will not be entertained once tooling will be started. This
means you need to fix the inspection process & material specification as a protocol in the beginning through a
black & white document. </li>
<li>Any sort of overseas visit of a client during the tool trials to witness the quality, will be borne by the client himself</li>
<li>For All Production & Batch Production requirements 
<ul>
<li>All Production orders will be made seperately and are chargeable including raw material and production
cost
</li>
<li>In case of shipping of the produced goods for batch production the packaging cost will be extra and
borne by the client. HJIG will quote for these prices after T-1</li>
<li>In case of any production plan client will inform atleast 30 days before the T-1 date
</li>
<li>HJIG will not be responsible for any kind of breakage of the samples during Air / Sea Shipment.
</li>
</ul>
</li>
<li>All moulds trial are subjected to the available testing machines with the infrastructure of Chinese selected tool
maker only.
</li>
<li>Any Batch production is only for the purpose of Tool run Inspection. These kind of production parts are not
recomended to be used for commercial sales as a part of finish product. So HJIG will not be responsible for any
commercial reimbursement in case of any damages or quality related issues. Doing batch production is only clients
own decision for satisfactory inspection of moulds. 
</li>
<li>The trials and testing will be made with the available colours of the RM. In case if customer demands the desired
CFM during tool Testing, there will be an additional charge
</li>
<li>Incase of specific colours, the customer will provide the colour code ( Panton/ RGB) in advance, otherwise HJIG
will facilitate the available colours with the trial testing factory. </li>
</ol>
<table style="width:100%; padding:3px; font-size:11px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
DELIVERY
</td>
</tr>
</table>
<ol style="font-size:11px;">
<li>Tools Deliveries are based on FOB Terms. 
</li>
<li> Incasee of CIF deliveries (Door to Door) will be on specials request by client and quoted by HJIG 
</li>
<li>Hongyi JIG will arrange the shipment and its cost of trial samples 1-2 samples of big parts and 3-4 samples for the
small part. Incase if client required a greater number of parts, the sample cost and its shipment and custom
clearance cost will be borne by client.
</li>
<li>No deliveries will be made in case of any pending dues out of total amount payable including GST amount (Local
Taxes)
</li>
<li> No deliveries will be made in case of any pending dues out of total amount payable.
</li>
<li>Any colur required on Moulds to be mentioned at time of order finalisation.
</li>
<li>Any special packaging required for moulds or production need to be mention in starting of project so as to cater
earlier into consideration. HJIG will not. be responsible any breakage during shipment. 
</li>
<li>Tool Shipment will be made after the clear approval of parts from client. HJIG will not take any responsibility of tool
modification or ECN after the Tool Shipment. 
</li>
<li>Delivery terms of Batch Production will be EXW
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
<li>Incase of CIF deliveries requested by client, Hongyi JIG will quote the eastimated shipment cost and budget in
advance before project kick off. Client will approve the shipment cost & quote. Hongyi JIG has a right for not to
share any document of imports and shipment to client. These documents will be confidential to HJIG only and
client will have no rights to demand them. 
</li>
<li>in CIF Terms, the shipment cost is by Sea. If client wish to make it by Air shipment at later stage, then he have to
pay the Air Freight Charges, Custom Duty Difference, and Shipment handling charges extra. The prices will be
given prior to make bookings and taken approval from client. No related document will be shared for same.
</li>
<li>In CIF Shipment, all the progress and processes will be transparently updated to client on time.
</li>
<li>If Insurence of Cargo is required, then client have must have to inform at time of Order Booking. There after no
request of insuarance will be acceptable 
</li>
<li>Hongyi JIG is not responsible for any kind of damage or breakage during shipment</li>
<li>For the shipment delayes like as Congestion at port, delay by customs, container shortage, flight rescheduling etc.
are not in control of HJIG. HJIG will keep update to client well on time. HJIG will not take any responsibility of
demurrages & panelties caused.
</li>
</ol>
<table style="width:100%; padding:3px; font-size:11px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
LEAD TIME
</td>
</tr>
</table>
<ol style="font-size:11px;">
<li> The Tooling lead time will be counted from the date of all tool designs approved from customer’s and given clear
Kick off for tooling by production supervisors, technical team or customer himself. Lead time of molds
manufacturing is in working days from the date of approval of tool designs, advance payment till the first trial.
</li>
<li>After T-1 to Tool Delivery the Lead time will be consider subjected to the samples express time, Final Approval and
Feedback from client & the tool corrections in 2 attempts including final surface finish.
</li>
<li>The time lines before tooling kick off are subjected to design changes and engineering suggestions and ECNc
which may differ. </li>
<li>Any request of smaple production or batch production will effect the tool delivery lead time.
</li>
<li> Any delay in dleivery of moulds due to production requests by customer will not be counted in HJIG accountability.</li>
<li>Orders will be executed based on all the required informarion in one go (on Full Kitting Sheet). Any delay in the
information or revised information will be subjected to delay and the lead time will be accounted from the last
information delivery date. 
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
<li>Payment Terms will be as per Quoted value and Stages as defined and are subjected to INR Vs US$ difference at
the time of all payment stages. </li>
<li> The currency prices will be considered at the time of order booking and paying the advance money. Any difference
of forex exchange during the project running will be counted in the customer’s account at the time of payment
milestone. 
</li>
<li>Any delay in any payment milestone more than 7 days from the stage mentioned, will be subjected to charge
interest by the supplier from client @ 0.9% of the amount due.</li>
<li>The payment transfer are subjected to 3-4 working days i.e. Monday to Friday takes time to reflect in overseas
account. Any connected action subjected to payment remmintance will be pending till payment reflects. Customer
need to consider & allow that time line to proceed for further related processes.
</li>
<li>All Invoices will be raised after the final payment & Taxes without any pendancy of balance payment including GST</li>
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
<li>Hongyi JIG technical engineers will support client to set up the tool for production at client’s site. Any tool
modification after shipment will be borne by the client. 
</li>
<li>Client will align the date and time for Tool installation and commissioning atleast 3 days before on prior notice
through Email of Whatsapp in the group. 
</li>
<li>Hongyi JIG Technical team will provide all final Tool Designs in Detailed 3D models (STEP / Parasolid formats)
</li>
<li>No cost of any spare part or paid labour after the tools delivery, will be borne by HJIG
</li>

</ol>
<table style="width:100%; padding:3px; font-size:11px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
DISPUTES
</td>
</tr>
</table>
<ol style="font-size:11px;">
<li>In case if due to any reason any of the party, quits the project in between, the client has to pay the cost of services
completed or initiated by Hongyi JIG
</li>
<li>Any Force majoure losses or damages will not be in HJIG account.
</li>
<li> Any Change in the SOW (Scope Of Work) will be subjected to revised cost of the addressed Tool or Service and
will not be compared with the Local service.</li>
<li> All prices and quotes for the Additional moulds or ECN will be compared with the Tool makers in of similiar
overseas destination & eastablished protocol
</li>
<li>Transfer of final payment before delivery will be considered as client is satisfied with the project and wish to ship
the mould. There after any modification or correction will be subjected to commercial charges. 
</li>
</ol>

<table style="width:100%; padding:3px; font-size:11px;">
<tr>
<td style="text-align:center; font-weight:bold; background-color:lightgrey; ">
TERMS REQUESTED BY CLIENT

</td>
</tr>
</table>
<ol style="font-size:11px;">';
$sql = "SELECT id FROM  bom_pi_sales_project_info WHERE lead_id = $lead_id";
	$results = mysqli_query($con,$sql);
	$data1= mysqli_fetch_array($results);
	$projectid = $data1['id'];
	$sql2 = "SELECT customer_request FROM  bom_pi_sales_cust_req WHERE project_id =$projectid";

	$resultssss = mysqli_query($con,$sql2);
	
	while($datass = mysqli_fetch_array($resultssss)){
		//echo "<pre>"; print($datass );
		//$customerrequest = $datass["customer_request"];
		$html.='<li>'.$datass["customer_request"].'</li>';
	}
$html.='</ol>
<br>
<br>
<br>
<br>

<table style="width:100%; padding:3px; font-size:11px;">
<tr>
<td style="text-align:center; font-weight:bold; background-color:lightgrey; ">
Signature & Stamp to Acknowledge 

</td>
</tr>
</table>

<table style="padding:3px; font-size:11px; " rules="all" border="1">
<tr style="font-weight:bold; text-align:center;">
<td>'.$company_name.' </td>
<td>'.$data['company_name'].'</td>
</tr>
<tr style="background-color:lightgrey;text-align:center;">
<td>'.$company_address.'</td>
<td>'.$data['billing_address'].'</td>
</tr>
</table>

<p style="font-size:11px;">Dear Sir, to confirm the order, please sign and stamp below in the client section and send a copy along with your PO.
In case of any doubt, please do not hesitate to contact us.
</p>
<p style="font-size:11px;">Regards,
</p>';
$sql = "SELECT member_id FROM  lead_assigned_to_team_member WHERE lead_id = $lead_id";
	$results = mysqli_query($con,$sql);
	$data1= mysqli_fetch_array($results);
	$member_id = $data1['member_id'];

	
	$sql2 = "SELECT first_name, last_name FROM  system_users WHERE user_id = $member_id";;
	//echo $sql2; exit;
	$resultssss = mysqli_query($con,$sql2);
	
	$datass = mysqli_fetch_array($resultssss);
	//echo "<pre>"; print_r($datass); exit;
	$name = $datass["first_name"].' '.$datass["last_name"];
$html.='<p style="font-size:11px;">'.$name.'</p>
<p style="font-size:11px;">Sales Manager
</p>
<p style="font-size:11px;">'.$company_name.'</p>
';	


//echo $footer_logo_html; exit;
// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.



$filelocation = $_SERVER['DOCUMENT_ROOT'].'/image_bank/quotation';
$fileNL = $filelocation."/quotation-".$lead_id.$option_id.".pdf"; //Linux

//$pdf->Output($fileNL, 'F');

$pdf->Output($fileNL, 'I');



//============================================================+
// END OF FILE
//============================================================+
