<?php
define('redirecturl', 'https://hongyijig.in/index.php/Proposal/preview_service_po/');
include('mysqlconfig.php');
$order_won_id = $_GET['order_won_id'];
$lead_id = $_GET['lead_id'];

$sql = "SELECT a.id, a.supplier_id, b.company_name, b.address, b.email_id, c.spokes_person_name, c.spokes_person_mobile FROM b4_quoted_price a JOIN cha b ON b.id=a.supplier_id JOIN cha_spokesperson_detail c ON c.external_supp_id=b.id WHERE a.order_won_id=$order_won_id";
// echo $sql;exit;
$result = mysqli_query($con, $sql);
 

 $supplier_id = '';
 $company_name = '';
 $spokes_person_name = '';
 $address = '';
 $mobile_no = '';
 $email_id = '';

if (mysqli_num_rows($result) > 0) {
    $data = mysqli_fetch_array($result);
    $supplier_id = $data['supplier_id'];
    $company_name = $data['company_name'];
    $spokes_person_name = $data['spokes_person_name'];
    $address = $data['address'];
    $mobile_no = $data['spokes_person_mobile'];
    $email_id = $data['email_id'];
}

$sql1 = "SELECT shipment_type, delivery_type FROM bom_pi_sales_project_info WHERE lead_id=$lead_id";
$result1 = mysqli_query($con, $sql1);

$shipment_type = '';

if (mysqli_num_rows($result1) > 0) {
	$data1 = mysqli_fetch_array($result1);
	$shipment_type = $data1['shipment_type'];
}

if($shipment_type == 1) {
	$type = 'AIR';
} else {
	$type = 'SEA';
}

$sql3 = "SELECT added_on FROM b4_po_payment_terms WHERE order_won_id=$order_won_id";
$result3 = mysqli_query($con, $sql3);

$order_date = '';

if(mysqli_num_rows($result3) > 0) {
	$data3 = mysqli_fetch_array($result3);
	$order_date = date('d-m-Y', strtotime($data3['added_on']));
}
 
/** end **/

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

require_once('tcpdf_include.php');
// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF
{
}
class MyCustomPDFWithWatermark extends TCPDF
{
    public function Header()
    {
        // Get the current page break margin
        $bMargin = $this->getBreakMargin();

        // Get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;

        // Disable auto-page-break
        $this->SetAutoPageBreak(false, 0);

        // Define the path to the image that you want to use as watermark.
        //$img_file = 'tcpdf/images/water.jpg';

        // Render the image
        $this->Image($img_file, 0, 0, 223, 280, '', '', '', false, 300, '', false, false, 0);

        // Restore the auto-page-break status
        $this->SetAutoPageBreak($auto_page_break, $bMargin);

        // Set the starting point for the page content
        $this->setPageMark();
    }
}
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf = new MyCustomPDFWithWatermark(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
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

if ($outside_india == 1) {
    $company_name1 = 'HONGYI <span style="color:orange;">JIG </span>RAPID TECHNOLOGIES CO., LTD.';
    $company_address1 = 'Unit H 1/F Mau Lam Comm Bldg16-18 Mau Lam St Jordan Kln, HK';
    $logo1 = '<img src="/var/www/hongyijig.in/pdf/internallogo.jpeg" width="200px">';
    $currency = 'US$';
} else if ($outside_india == 0) {
    $company_name1 = 'HONGYI <span style="color:orange;">JIG</span> RAPID TECHNOLOGIES';
    $company_address1 = '15/1, 2nd Floor, Rama Road Industrial Area Delhi 110015 (INDIA)';
    $currency = 'INR';
    $logo1 = '<img src="/var/www/hongyijig.in/pdf/logo.jpeg" width="200px">';
} else {
    $company_name1 = '';
    $company_address1 = '';
    $logo1 = '';
    $currency = '';
}

$html = '


    <table style="width:100%; padding-top:10px;">
        
        <tr>
            <td ></td>
            <td style=" padding:5px;">'.$logo1.'</td>
            <td ></td>
        </tr>
        <tr>
            <td style=" padding:5px; text-align:center; font-size:10px; font-weight:bold;" colspan="3">'.$company_name1.'</td>
        </tr>
        <tr >
            <td style=" padding:5px; text-align:center; font-size:10px; font-weight:bold;" colspan="3">'.$company_address1.'</td>
        </tr>
        <tr >
            <td style=" padding:5px; text-align:center; font-size:10px; font-weight:bold;" colspan="3">Tel No-86-15824020602, E-Mail: asia@hongyijig.com , Website: www.hongyijig.com
            </td>
        </tr>

    </table>
<br>
<br>
    <table style="width:100%;  padding:3px;">
    
<tr >
<td style="background-color:lightgrey; text-align:center; font-weight:bold; font-size:15px;">CHA PURCHASE ORDER 
</td>
</tr>

    </table>

    <br>
<br>
    <table style="width:100%;  padding:5px; font-size:10px;">
    
<tr>
<td width="100%">
<table style="width:100%;  padding:3px;" border="1">
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">COMPANY NAME</td>
<td>'.$company_name.'</td>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">ORDER DATE</td>
<td>'.$order_date.'</td>


</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">COMPANY ADDRESS</td>
<td>'.$address.'</td>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">DELIVERY DATE </td>
<td></td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">CONTACT PERSON</td>
<td>'.$spokes_person_name.'</td>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">DELIVERY TYPE</td>
<td>'.$delivery_type.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">MOBILE NUMBER</td>
<td>'.$mobile_no.'</td>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">PO NUMBER</td>
<td>'.$po_no.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">EMAIL ID
</td>
<td>'.$email_id.'</td>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">BUSINESS LICENCE NO.</td>
<td>'.$license_no.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">WEBSITE
</td>
<td>'.$website.'</td>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">GST NO (IF INDIA SUPPLIER)</td>
<td></td>
</tr>
</table>
</td>
</tr>

    </table>


<br>
<br>
    <table style="width:100%;  padding:5px;">
    
<tr >
<td style="font-size:10px;">Dear, '.$spokes_person_name.'<br>We are pleased to entrust you with purchase order for the '.$service_name.' mention below subject to terms and conditions given below.</td>
</tr>

    </table>

    <br>
<br>

<br>
<br>
    <table style="width:100%;  padding:3px;">
    
<tr >
<td style="background-color:lightgrey; text-align:center; font-weight:bold; font-size:10px;">'.$type.' SHIPMENT DETAILS 
 
</td>
</tr>

    </table>

    <br>
<br>
  <table style="width:100%; padding:3px; font-size:8px;" border="1">
<tr style="background-color:lightgrey; text-align:center; font-weight:bold; ">
<th style="border:1px solid black; width:220px;">Particulars</th>
<th style="border:1px solid black; width:100px;">Weight/CBM/Ton</th>
<th style="border:1px solid black; width:100px;">Sea Freight Charges in USD/CBM/Weight</th>
<th style="border:1px solid black;width:100px;">Total sea freight Charges/CBM/Ton</th>
<th style="border:1px solid black; width:100px;">Amount in INR</th>
</tr>';

$total_air_weight = array();
$total_air_seafreight = array();
$total_air_totalseafreight = array();
$total_air_rateinusd = array();
$total_air_amtininr = array();


$total_sea_weight = array();
$total_sea_seafreight = array();
$total_sea_totalseafreight = array();
$total_sea_rateinusd = array();
$total_sea_amtininr = array();

if($shipment_type == 1) {

	$query1 = "SELECT id, weight, sea_freight, total_sea_freight, rate_in_usd, new_amt_inr FROM b4_source_shipment_quotation WHERE order_won_id=$order_won_id AND lead_id=$lead_id AND supplier_id=$supplier_id AND hidden_id=1";
	$results1 = mysqli_query($con, $query1);

	$weight1 = '';
    $sourcing_id1 = '';
    $sea_freight1 = '';
    $total_sea_freight1 = '';
    $rate_in_usd1 = '';
    $amt_in_inr1 = '';

    if (mysqli_num_rows($results1) > 0) {
		$datas1 = mysqli_fetch_array($results1);
        $sourcing_id1 = $datas1['id'];
        $weight1 = floatval($datas1['weight']);
        $sea_freight1 = floatval($datas1['sea_freight']);
        $total_sea_freight1 = floatval($datas1['total_sea_freight']);
        $rate_in_usd1 = floatval($datas1['rate_in_usd']);
        $amt_in_inr1 = floatval($datas1['new_amt_inr']);
    }

    $html .= '<tr>
            <td>Ocean Freight Charges For 3 ton @ 80 USD/ton</td>
            <td>'.$weight1.'"></td>
            <td>'.$sea_freight1.'"></td>
            <td>'.$total_sea_freight1.'"></td>
            <td style="text-align:center;">'.$amt_in_inr1.'</td>
          </tr>';

    $total_air_weight[] = $weight1;
	$total_air_seafreight[] = $sea_freight1;
	$total_air_totalseafreight[] = $total_sea_freight1;
	$total_air_rateinusd[] = $rate_in_usd1;
	$total_air_amtininr[] = $amt_in_inr1;

   $query2 = "SELECT id, weight, sea_freight, total_sea_freight, rate_in_usd, new_amt_inr FROM b4_source_shipment_quotation WHERE order_won_id=$order_won_id AND lead_id=$lead_id AND supplier_id=$supplier_id AND hidden_id=2";
   $results2 = mysqli_query($con, $query2);

	$weight2 = '';
    $sourcing_id2 = '';
    $sea_freight2 = '';
    $total_sea_freight2 = '';
    $rate_in_usd2 = '';
    $amt_in_inr2 = '';

    if (mysqli_num_rows($results2) > 0) {
		$datas2 = mysqli_fetch_array($results2);
            $sourcing_id2 = $datas2['id'];
            $weight2 = floatval($datas2['weight']);
            $sea_freight2= floatval($datas2['sea_freight']);
            $total_sea_freight2 = floatval($datas2['total_sea_freight']);
            $rate_in_usd2 = floatval($datas2['rate_in_usd']);
            $amt_in_inr2 = floatval($datas2['new_amt_inr']);
    }

    $html .= '<tr>
            <td>Delivery Order Charges</td>
            <td>'.$weight2.'"></td>
            <td>'.$sea_freight2.'"></td>
            <td>'.$total_sea_freight2.'"></td>
            <td style="text-align:center;">'.$amt_in_inr2.'</td>
          </tr>';


    $total_air_weight[] = $weight2;
	$total_air_seafreight[] = $sea_freight2;
	$total_air_totalseafreight[] = $total_sea_freight2;
	$total_air_rateinusd[] = $rate_in_usd2;
	$total_air_amtininr[] = $amt_in_inr2;


   $query3 = "SELECT id, weight, sea_freight, total_sea_freight, rate_in_usd, new_amt_inr FROM b4_source_shipment_quotation WHERE order_won_id=$order_won_id AND lead_id=$lead_id AND supplier_id=$supplier_id AND hidden_id=3";
   $results3 = mysqli_query($con, $query3);

	$weight3 = '';
    $sourcing_id3 = '';
    $sea_freight3 = '';
    $total_sea_freight3 = '';
    $rate_in_usd3 = '';
    $amt_in_inr3 = '';

    if (mysqli_num_rows($results3) > 0) {
		$datas3 = mysqli_fetch_array($results3);
            $sourcing_id3 = $datas3['id'];
            $weight3 = floatval($datas3['weight']);
            $sea_freight3= floatval($datas3['sea_freight']);
            $total_sea_freight3 = floatval($datas3['total_sea_freight']);
            $rate_in_usd3 = floatval($datas3['rate_in_usd']);
            $amt_in_inr3 = floatval($datas3['new_amt_inr']);
    }

    $html .= '<tr>
            <td>Terminal Handling Charge 475 /CBM</td>
            <td>'.$weight3.'"></td>
            <td>'.$sea_freight3.'"></td>
            <td>'.$total_sea_freight3.'"></td>
            <td style="text-align:center;">'.$amt_in_inr3.'</td>
          </tr>';

    $total_air_weight[] = $weight3;
	$total_air_seafreight[] = $sea_freight3;
	$total_air_totalseafreight[] = $total_sea_freight3;
	$total_air_rateinusd[] = $rate_in_usd3;
	$total_air_amtininr[] = $amt_in_inr3;



   $query4 = "SELECT id, weight, sea_freight, total_sea_freight, rate_in_usd, new_amt_inr FROM b4_source_shipment_quotation WHERE order_won_id=$order_won_id AND lead_id=$lead_id AND supplier_id=$supplier_id AND hidden_id=4";
   $results4 = mysqli_query($con, $query4);

	$weight4 = '';
    $sourcing_id4 = '';
    $sea_freight4 = '';
    $total_sea_freight4 = '';
    $rate_in_usd4 = '';
    $amt_in_inr4 = '';

    if (mysqli_num_rows($results4) > 0) {
		$datas4 = mysqli_fetch_array($results4);
            $sourcing_id4 = $datas4['id'];
            $weight4 = floatval($datas4['weight']);
            $sea_freight4= floatval($datas4['sea_freight']);
            $total_sea_freight4 = floatval($datas4['total_sea_freight']);
            $rate_in_usd4 = floatval($datas4['rate_in_usd']);
            $amt_in_inr4 = floatval($datas4['new_amt_inr']);
    }

    $html .= '<tr>
            <td>Custom Clearance Charges</td>
            <td>'.$weight4.'"></td>
            <td>'.$sea_freight4.'"></td>
            <td>'.$total_sea_freight4.'"></td>
            <td style="text-align:center;">'.$amt_in_inr4.'</td>
          </tr>';

    $total_air_weight[] = $weight4;
	$total_air_seafreight[] = $sea_freight4;
	$total_air_totalseafreight[] = $total_sea_freight4;
	$total_air_rateinusd[] = $rate_in_usd4;
	$total_air_amtininr[] = $amt_in_inr4;

   $query5 = "SELECT id, weight, sea_freight, total_sea_freight, rate_in_usd, new_amt_inr FROM b4_source_shipment_quotation WHERE order_won_id=$order_won_id AND lead_id=$lead_id AND supplier_id=$supplier_id AND hidden_id=5";
   $results5 = mysqli_query($con, $query5);

	$weight5 = '';
    $sourcing_id5 = '';
    $sea_freight5 = '';
    $total_sea_freight5 = '';
    $rate_in_usd5 = '';
    $amt_in_inr5 = '';

    if (mysqli_num_rows($results5) > 0) {
		$datas5 = mysqli_fetch_array($results5);
            $sourcing_id5 = $datas5['id'];
            $weight5 = floatval($datas5['weight']);
            $sea_freight5= floatval($datas5['sea_freight']);
            $total_sea_freight5 = floatval($datas5['total_sea_freight']);
            $rate_in_usd5 = floatval($datas5['rate_in_usd']);
            $amt_in_inr5 = floatval($datas5['new_amt_inr']);
    }

    $html .= '<tr>
            <td>CFS Charges</td>
            <td>'.$weight5.'"></td>
            <td>'.$sea_freight5.'"></td>
            <td>'.$total_sea_freight5.'"></td>
            <td style="text-align:center;">'.$amt_in_inr5.'</td>
          </tr>';

    $total_air_weight[] = $weight5;
	$total_air_seafreight[] = $sea_freight5;
	$total_air_totalseafreight[] = $total_sea_freight5;
	$total_air_rateinusd[] = $rate_in_usd5;
	$total_air_amtininr[] = $amt_in_inr5;

   $query6 = "SELECT id, weight, sea_freight, total_sea_freight, rate_in_usd, new_amt_inr FROM b4_source_shipment_quotation WHERE order_won_id=$order_won_id AND lead_id=$lead_id AND supplier_id=$supplier_id AND hidden_id=6";
   $results6 = mysqli_query($con, $query6);

	$weight6 = '';
    $sourcing_id6 = '';
    $sea_freight6 = '';
    $total_sea_freight6 = '';
    $rate_in_usd6 = '';
    $amt_in_inr6 = '';

    if (mysqli_num_rows($results6) > 0) {
		$datas6 = mysqli_fetch_array($results6);
            $sourcing_id6 = $datas6['id'];
            $weight6 = floatval($datas6['weight']);
            $sea_freight6= floatval($datas6['sea_freight']);
            $total_sea_freight6 = floatval($datas6['total_sea_freight']);
            $rate_in_usd6 = floatval($datas6['rate_in_usd']);
            $amt_in_inr6 = floatval($datas6['new_amt_inr']);
    }

    $html .= '<tr>
            <td>Transportation Charges</td>
            <td>'.$weight6.'"></td>
            <td>'.$sea_freight6.'"></td>
            <td>'.$total_sea_freight6.'"></td>
            <td style="text-align:center;">'.$amt_in_inr6.'</td>
          </tr>';

    $total_air_weight[] = $weight6;
	$total_air_seafreight[] = $sea_freight6;
	$total_air_totalseafreight[] = $total_sea_freight6;
	$total_air_rateinusd[] = $rate_in_usd6;
	$total_air_amtininr[] = $amt_in_inr6;


	$html .= '<tr style="background-color:lightgrey;">
                <td style="text-align:center;font-weight:bold;color:black;">Total</td>
                <td style="text-align:center;font-weight:bold;color:black;">'.array_sum($total_air_weight).'</td>
                <td style="text-align:center;font-weight:bold;color:black;">'.array_sum($total_air_seafreight).'</td>
                <td style="text-align:center;font-weight:bold;color:black;">'.array_sum($total_air_totalseafreight).'</td>
                <td style="text-align:center;font-weight:bold;color:black;">-</td>
                <td style="text-align:center;font-weight:bold;color:black;">'.array_sum($total_air_amtininr).'</td>
            </tr>';

} else {

	$query1 = "SELECT id, weight, sea_freight, total_sea_freight, rate_in_usd, new_amt_inr FROM b4_source_shipment_quotation WHERE order_won_id=$order_won_id AND lead_id=$lead_id AND supplier_id=$supplier_id AND hidden_id=1";
	$results1 = mysqli_query($con, $query1);

	$weight1 = '';
    $sourcing_id1 = '';
    $sea_freight1 = '';
    $total_sea_freight1 = '';
    $rate_in_usd1 = '';
    $amt_in_inr1 = '';

    if (mysqli_num_rows($results1) > 0) {
		$datas1 = mysqli_fetch_array($results1);
        $sourcing_id1 = $datas1['id'];
        $weight1 = floatval($datas1['weight']);
        $sea_freight1 = floatval($datas1['sea_freight']);
        $total_sea_freight1 = floatval($datas1['total_sea_freight']);
        $rate_in_usd1 = floatval($datas1['rate_in_usd']);
        $amt_in_inr1 = floatval($datas1['new_amt_inr']);
    }

    $html .= '<tr>
            <td>Ocean Freight Charges</td>
            <td>'.$weight1.'</td>
            <td>'.$sea_freight1.'</td>
            <td>'.$total_sea_freight1.'</td>
            <td style="text-align:center;">'.$amt_in_inr1.'</td>
          </tr>';

    $total_sea_weight[] = $weight1;
	$total_sea_seafreight[] = $sea_freight1;
	$total_sea_totalseafreight[] = $total_sea_freight1;
	$total_sea_rateinusd[] = $rate_in_usd1;
	$total_sea_amtininr[] = $amt_in_inr1;

    $query2 = "SELECT id, weight, sea_freight, total_sea_freight, rate_in_usd, new_amt_inr FROM b4_source_shipment_quotation WHERE order_won_id=$order_won_id AND lead_id=$lead_id AND supplier_id=$supplier_id AND hidden_id=2";
   $results2 = mysqli_query($con, $query2);

	$weight2 = '';
    $sourcing_id2 = '';
    $sea_freight2 = '';
    $total_sea_freight2 = '';
    $rate_in_usd2 = '';
    $amt_in_inr2 = '';

    if (mysqli_num_rows($results2) > 0) {
		$datas2 = mysqli_fetch_array($results2);
            $sourcing_id2 = $datas2['id'];
            $weight2 = floatval($datas2['weight']);
            $sea_freight2= floatval($datas2['sea_freight']);
            $total_sea_freight2 = floatval($datas2['total_sea_freight']);
            $rate_in_usd2 = floatval($datas2['rate_in_usd']);
            $amt_in_inr2 = floatval($datas2['new_amt_inr']);
    }

    $html .= '<tr>
            <td>Inland Haulage Charges</td>
            <td>'.$weight2.'</td>
            <td>'.$sea_freight2.'</td>
            <td>'.$total_sea_freight2.'</td>
            <td style="text-align:center;">'.$amt_in_inr2.'</td>
          </tr>';


    $total_sea_weight[] = $weight2;
	$total_sea_seafreight[] = $sea_freight2;
	$total_sea_totalseafreight[] = $total_sea_freight2;
	$total_sea_rateinusd[] = $rate_in_usd2;
	$total_sea_amtininr[] = $amt_in_inr2;

    $query3 = "SELECT id, weight, sea_freight, total_sea_freight, rate_in_usd, new_amt_inr FROM b4_source_shipment_quotation WHERE order_won_id=$order_won_id AND lead_id=$lead_id AND supplier_id=$supplier_id AND hidden_id=3";
    $results3 = mysqli_query($con, $query3);

	$weight3 = '';
    $sourcing_id3 = '';
    $sea_freight3 = '';
    $total_sea_freight3 = '';
    $rate_in_usd3 = '';
    $amt_in_inr3 = '';

    if (mysqli_num_rows($results3) > 0) {
		$datas3 = mysqli_fetch_array($results3);
            $sourcing_id3 = $datas3['id'];
            $weight3 = floatval($datas3['weight']);
            $sea_freight3= floatval($datas3['sea_freight']);
            $total_sea_freight3 = floatval($datas3['total_sea_freight']);
            $rate_in_usd3 = floatval($datas3['rate_in_usd']);
            $amt_in_inr3 = floatval($datas3['new_amt_inr']);
    }

    $html .= '<tr>
            <td>Delivery Order Charges</td>
            <td>'.$weight3.'</td>
            <td>'.$sea_freight3.'</td>
            <td>'.$total_sea_freight3.'</td>
            <td style="text-align:center;">'.$amt_in_inr3.'</td>
          </tr>';

    $total_sea_weight[] = $weight3;
	$total_sea_seafreight[] = $sea_freight3;
	$total_sea_totalseafreight[] = $total_sea_freight3;
	$total_sea_rateinusd[] = $rate_in_usd3;
	$total_sea_amtininr[] = $amt_in_inr3;

   $query4 = "SELECT id, weight, sea_freight, total_sea_freight, rate_in_usd, new_amt_inr FROM b4_source_shipment_quotation WHERE order_won_id=$order_won_id AND lead_id=$lead_id AND supplier_id=$supplier_id AND hidden_id=4";
    $results4 = mysqli_query($con, $query4);

	$weight4 = '';
    $sourcing_id4 = '';
    $sea_freight4 = '';
    $total_sea_freight4 = '';
    $rate_in_usd4 = '';
    $amt_in_inr4 = '';

    if (mysqli_num_rows($results4) > 0) {
		$datas4 = mysqli_fetch_array($results4);
            $sourcing_id4 = $datas4['id'];
            $weight4 = floatval($datas4['weight']);
            $sea_freight4 = floatval($datas4['sea_freight']);
            $total_sea_freight4 = floatval($datas4['total_sea_freight']);
            $rate_in_usd4 = floatval($datas4['rate_in_usd']);
            $amt_in_inr4 = floatval($datas4['new_amt_inr']);
    }

    $html .= '<tr>
            <td>Terminal Handling Charge 475 /CBM</td>
            <td>'.$weight4.'</td>
            <td>'.$sea_freight4.'</td>
            <td>'.$total_sea_freight4.'</td>
            <td style="text-align:center;">'.$amt_in_inr4.'</td>
          </tr>';

    $total_sea_weight[] = $weight4;
	$total_sea_seafreight[] = $sea_freight4;
	$total_sea_totalseafreight[] = $total_sea_freight4;
	$total_sea_rateinusd[] = $rate_in_usd4;
	$total_sea_amtininr[] = $amt_in_inr4;

    $query5 = "SELECT id, weight, sea_freight, total_sea_freight, rate_in_usd, new_amt_inr FROM b4_source_shipment_quotation WHERE order_won_id=$order_won_id AND lead_id=$lead_id AND supplier_id=$supplier_id AND hidden_id=5";
    $results5 = mysqli_query($con, $query5);

	$weight5 = '';
    $sourcing_id5 = '';
    $sea_freight5 = '';
    $total_sea_freight5 = '';
    $rate_in_usd5 = '';
    $amt_in_inr5 = '';

    if (mysqli_num_rows($results5) > 0) {
		$datas5 = mysqli_fetch_array($results5);
            $sourcing_id5 = $datas5['id'];
            $weight5 = floatval($datas5['weight']);
            $sea_freight5 = floatval($datas5['sea_freight']);
            $total_sea_freight5 = floatval($datas5['total_sea_freight']);
            $rate_in_usd5 = floatval($datas5['rate_in_usd']);
            $amt_in_inr5 = floatval($datas5['new_amt_inr']);
    }

    $html .= '<tr>
            <td>CFS Charges</td>
            <td>'.$weight5.'</td>
            <td>'.$sea_freight5.'</td>
            <td>'.$total_sea_freight5.'</td>
            <td style="text-align:center;">'.$amt_in_inr5.'</td>
          </tr>';

    $total_sea_weight[] = $weight5;
	$total_sea_seafreight[] = $sea_freight5;
	$total_sea_totalseafreight[] = $total_sea_freight5;
	$total_sea_rateinusd[] = $rate_in_usd5;
	$total_sea_amtininr[] = $amt_in_inr5;

    $query6 = "SELECT id, weight, sea_freight, total_sea_freight, rate_in_usd, new_amt_inr FROM b4_source_shipment_quotation WHERE order_won_id=$order_won_id AND lead_id=$lead_id AND supplier_id=$supplier_id AND hidden_id=6";
    $results6 = mysqli_query($con, $query6);

	$weight6 = '';
    $sourcing_id6 = '';
    $sea_freight6 = '';
    $total_sea_freight6 = '';
    $rate_in_usd6 = '';
    $amt_in_inr6 = '';

    if (mysqli_num_rows($results6) > 0) {
		$datas6 = mysqli_fetch_array($results6);
            $sourcing_id6 = $datas6['id'];
            $weight6 = floatval($datas6['weight']);
            $sea_freight6 = floatval($datas6['sea_freight']);
            $total_sea_freight6 = floatval($datas6['total_sea_freight']);
            $rate_in_usd6 = floatval($datas6['rate_in_usd']);
            $amt_in_inr6 = floatval($datas6['new_amt_inr']);
    }

    $html .= '<tr>
            <td>Agency Charges</td>
            <td>'.$weight6.'</td>
            <td>'.$sea_freight6.'</td>
            <td>'.$total_sea_freight6.'</td>
            <td style="text-align:center;">'.$amt_in_inr6.'</td>
          </tr>';

    $total_sea_weight[] = $weight6;
	$total_sea_seafreight[] = $sea_freight6;
	$total_sea_totalseafreight[] = $total_sea_freight6;
	$total_sea_rateinusd[] = $rate_in_usd6;
	$total_sea_amtininr[] = $amt_in_inr6;

    $query7 = "SELECT id, weight, sea_freight, total_sea_freight, rate_in_usd, new_amt_inr FROM b4_source_shipment_quotation WHERE order_won_id=$order_won_id AND lead_id=$lead_id AND supplier_id=$supplier_id AND hidden_id=7";
    $results7 = mysqli_query($con, $query7);

	$weight7 = '';
    $sourcing_id7 = '';
    $sea_freight7 = '';
    $total_sea_freight7 = '';
    $rate_in_usd7 = '';
    $amt_in_inr7 = '';

    if (mysqli_num_rows($results7) > 0) {
		$datas7 = mysqli_fetch_array($results7);
            $sourcing_id7 = $datas7['id'];
            $weight7 = floatval($datas7['weight']);
            $sea_freight7 = floatval($datas7['sea_freight']);
            $total_sea_freight7 = floatval($datas7['total_sea_freight']);
            $rate_in_usd7 = floatval($datas7['rate_in_usd']);
            $amt_in_inr7 = floatval($datas7['new_amt_inr']);
    }

    $html .= '<tr>
            <td>Labor Loading & Unloading Charges</td>
            <td>'.$weight7.'</td>
            <td>'.$sea_freight7.'</td>
            <td>'.$total_sea_freight7.'</td>
            <td style="text-align:center;">'.$amt_in_inr7.'</td>
          </tr>';

    $total_sea_weight[] = $weight7;
	$total_sea_seafreight[] = $sea_freight7;
	$total_sea_totalseafreight[] = $total_sea_freight7;
	$total_sea_rateinusd[] = $rate_in_usd7;
	$total_sea_amtininr[] = $amt_in_inr7;

   $query8 = "SELECT id, weight, sea_freight, total_sea_freight, rate_in_usd, new_amt_inr FROM b4_source_shipment_quotation WHERE order_won_id=$order_won_id AND lead_id=$lead_id AND supplier_id=$supplier_id AND hidden_id=8";
    $results8 = mysqli_query($con, $query8);

	$weight8 = '';
    $sourcing_id8 = '';
    $sea_freight8 = '';
    $total_sea_freight8 = '';
    $rate_in_usd8 = '';
    $amt_in_inr8 = '';

    if (mysqli_num_rows($results8) > 0) {
		$datas8 = mysqli_fetch_array($results8);
            $sourcing_id8 = $datas8['id'];
            $weight8 = floatval($datas8['weight']);
            $sea_freight8 = floatval($datas8['sea_freight']);
            $total_sea_freight8 = floatval($datas8['total_sea_freight']);
            $rate_in_usd8 = floatval($datas8['rate_in_usd']);
            $amt_in_inr8 = floatval($datas8['new_amt_inr']);
    }

    $html .= '<tr>
            <td>Agency Documentation Charges</td>
            <td>'.$weight8.'</td>
            <td>'.$sea_freight8.'</td>
            <td>'.$total_sea_freight8.'</td>
            <td style="text-align:center;">'.$amt_in_inr8.'</td>
          </tr>';

    $total_sea_weight[] = $weight8;
	$total_sea_seafreight[] = $sea_freight8;
	$total_sea_totalseafreight[] = $total_sea_freight8;
	$total_sea_rateinusd[] = $rate_in_usd8;
	$total_sea_amtininr[] = $amt_in_inr8;

    $query9 = "SELECT id, weight, sea_freight, total_sea_freight, rate_in_usd, new_amt_inr FROM b4_source_shipment_quotation WHERE order_won_id=$order_won_id AND lead_id=$lead_id AND supplier_id=$supplier_id AND hidden_id=9";
    $results9 = mysqli_query($con, $query9);

	$weight9 = '';
    $sourcing_id9 = '';
    $sea_freight9 = '';
    $total_sea_freight9 = '';
    $rate_in_usd9 = '';
    $amt_in_inr9 = '';

    if (mysqli_num_rows($results9) > 0) {
		$datas9 = mysqli_fetch_array($results9);
            $sourcing_id9 = $datas9['id'];
            $weight9 = floatval($datas9['weight']);
            $sea_freight9 = floatval($datas9['sea_freight']);
            $total_sea_freight9 = floatval($datas9['total_sea_freight']);
            $rate_in_usd9 = floatval($datas9['rate_in_usd']);
            $amt_in_inr9 = floatval($datas9['new_amt_inr']);
    }

    $html .= '<tr>
            <td>Examination Charges</td>
            <td>'.$weight9.'</td>
            <td>'.$sea_freight9.'</td>
            <td>'.$total_sea_freight9.'</td>
            <td style="text-align:center;">'.$amt_in_inr9.'</td>
          </tr>';

    $total_sea_weight[] = $weight9;
	$total_sea_seafreight[] = $sea_freight9;
	$total_sea_totalseafreight[] = $total_sea_freight9;
	$total_sea_rateinusd[] = $rate_in_usd9;
	$total_sea_amtininr[] = $amt_in_inr9;

    $query10 = "SELECT id, weight, sea_freight, total_sea_freight, rate_in_usd, new_amt_inr FROM b4_source_shipment_quotation WHERE order_won_id=$order_won_id AND lead_id=$lead_id AND supplier_id=$supplier_id AND hidden_id=10";
    $results10 = mysqli_query($con, $query10);

	$weight10 = '';
    $sourcing_id10 = '';
    $sea_freight10 = '';
    $total_sea_freight10 = '';
    $rate_in_usd10 = '';
    $amt_in_inr10 = '';

    if (mysqli_num_rows($results10) > 0) {
		$datas10 = mysqli_fetch_array($results10);
            $sourcing_id10 = $datas10['id'];
            $weight10 = floatval($datas10['weight']);
            $sea_freight10 = floatval($datas10['sea_freight']);
            $total_sea_freight10 = floatval($datas10['total_sea_freight']);
            $rate_in_usd10 = floatval($datas10['rate_in_usd']);
            $amt_in_inr10 = floatval($datas10['new_amt_inr']);
    }

    $html .= '<tr>
            <td>Transportation</td>
            <td>'.$weight10.'</td>
            <td>'.$sea_freight10.'</td>
            <td>'.$total_sea_freight10.'</td>
            <td style="text-align:center;">'.$amt_in_inr10.'</td>
          </tr>';

    $total_sea_weight[] = $weight10;
	$total_sea_seafreight[] = $sea_freight10;
	$total_sea_totalseafreight[] = $total_sea_freight10;
	$total_sea_rateinusd[] = $rate_in_usd10;
	$total_sea_amtininr[] = $amt_in_inr10;


	$html .= '<tr style="background-color:lightgrey;">
                <td style="text-align:center;font-weight:bold;color:black;">Total</td>
                <td style="text-align:center;font-weight:bold;color:black;">'.array_sum($total_sea_weight).'</td>
                <td style="text-align:center;font-weight:bold;color:black;">'.array_sum($total_sea_seafreight).'</td>
                <td style="text-align:center;font-weight:bold;color:black;">'.array_sum($total_sea_totalseafreight).'</td>
                <td style="text-align:center;font-weight:bold;color:black;">'.array_sum($total_sea_amtininr).'</td>
            </tr>';

}


if(count($total_part_amount)>0)
{
    $total=array_sum($total_part_amount);

}else
{
    $total=0;
}


// $html .= '<tr>
// <td style="border:1px solid black; background-color:lightgrey; text-align:right; font-weight:bold;" colspan="6" >TOTAL '.$in_words.'</td>
// <td style="border:1px solid black; background-color:lightgrey; text-align:center; font-weight:bold;">'.$total.'</td>
// </tr>';

// $html.='<tr style="background-color:#eeeeee; font-weight:bold;" nobr="true">
// <td colspan="6" style="text-align:right">GRAND TOTAL '.$in_words.'</td>
// <td  style="text-align:center;">'.$total.'</td>
// </tr>';


// $html.='<tr style="background-color:#eeeeee; font-weight:bold;" nobr="true">
// <td colspan="7" style="text-align:right">AMOUNT IN WORDS:'.ucwords(getCurrencyCode($total)).'</td>
// </tr>';


$html.='</table>

<br>
<br>';

$html .= '<br pagebreak="true"/>

<table style=" padding:3px; font-size:10px;">
<tr style="background-color:lightgrey;">
<td style="text-align:center; font-weight:bold;">PAYMENT TERMS
</td>
</tr>

</table>

<br>
<br>
<table border="1"  style="padding:3px; width:100%; text-align:center;">
<tr style="background-color:lightgrey;">
<td style="text-align:center;>S.NO
</td>
<td style="text-align:center;>PAYMENT DIVISION
</td>
<td style="text-align:center;">PAYMENT STAGE
</td>
<td style="text-align:center;">% AGE 
</td></tr>';

$sql1 = "SELECT payment_division, stage, percentage FROM b4_po_payment_terms WHERE order_won_id=$order_won_id";
$result1 = mysqli_query($con, $sql1);
 
 $payment_division = '';
 $stage = '';
 $percentage = '';
 $total_percentage = array();
if (mysqli_num_rows($result1) > 0) {
    $i = 1;
    while($data1 = mysqli_fetch_array($result1)) {
    $payment_division = $data1['payment_division'];
    $stage = $data1['stage'];
    $percentage = $data1['percentage'];
        $html .= '<tr>
        <td>'.$payment_division.'</td>
        <td>'.$stage.'</td>
        <td>'.$percentage.'%</td>
        </tr>';

        $total_percentage[] = $percentage;
    }
}
$html .= '<tr>
<td colspan="2" style="background-color:lightgrey; text-align:right;">Total</td>
<td>'.array_sum($total_percentage).'%</td>
</tr>
</table>
<br/><br/>';

$sql4 = "SELECT description FROM termsandconditions WHERE supplier_type=3 AND category=4 AND country_flag=2";
// echo $sql4;exit;
$result4 = mysqli_query($con, $sql4);

if (mysqli_num_rows($result4) > 0) {
	$data4 = mysqli_fetch_array($result4);
	// echo $data4['description'];

$html.='

<br>
<br>
    <table style="width:100%;  padding:3px;">
<tr>
<td style="background-color:lightgrey; text-align:center; font-weight:bold; font-size:10px;">TERMS & CONDITIONS
</td>
</tr>


    </table><br>';

$html.=$data4['description'];

   }



  $html .=  '<br>
<br>
<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="border-right:1px solid black; height:70px;">Purchase Manager 
</td>
<td style="border-right:1px solid black; height:70px;">One Level up Approval 
</td>
<td style=" height:70px;">Directors Signature 
</td>
</tr>
</table>

<br>
<br>
<table style="width:100%; padding:3px; font-size:10px; border-top:1px solid black;">
<tr>
<td>Signature<br><i>To be Signed & Stamped by Supply Chain Manager 
</i>
</td>
<td >Signature<br><i>To be Signed & Stamped by Supply Chain Manager 

</i></td>
<td >Signature<br><i>No PO is Valid if not Sign & Stamped Originolly by Directors 
</i></td>
</tr>
</table>


';


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


$filelocation = $_SERVER['DOCUMENT_ROOT'] . '/image_bank/service_po';
$fileNL = $filelocation."/Service_PO_".$data20['unique_id'].".pdf"; //Linux

$pdf->Output($fileNL, 'I');
// header('location:'.redirecturl.$data20['unique_id']);



//============================================================+
// END OF FILE
//============================================================+
