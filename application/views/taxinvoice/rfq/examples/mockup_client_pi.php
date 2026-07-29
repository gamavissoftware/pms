<?php

/** QUERY ENDS **/
define('image_url', '/var/www/hongyijig.in/assets/prototype_part_picture/');
define('IMAGEPATH', '/var/www/hongyijig.in/assets/client_bom_data/part_image/');

define('redirecturl', 'https://hongyijig.in/index.php/Proposal/preview_performa/');
include('mysqlconfig.php');

$lead_id = $_GET['lead_id'];
$order_won_id = $_GET['order_won_id'];
$supplier_id=$_GET['suplier'];

if ($negotiation == '') {
    $negotiation = 0;   
}

$sql = "SELECT b.delivery_type,b.shipment_type, a.unique_id, a.company_name, a.customer_name, a.postal_address, a.country_code, a.contact_no, a.email_id, c.billing_address FROM leads a LEFT JOIN bom_pi_sales_client_info c  ON a.id=c.lead_id LEFT JOIN bom_pi_sales_project_info b ON a.id=b.lead_id  WHERE a.id=$lead_id";
//echo $sql;exit;
$result = mysqli_query($con, $sql);

if(mysqli_num_rows($result) > 0) {
    $data = mysqli_fetch_array($result);
    $query_no = $data['unique_id'];
    $company_name = $data['company_name'];
    $customer_name = $data['customer_name'];
    $postal_address = $data['billing_address'];
    $country_code = $data['country_code'];
    $contact_no = $data['contact_no'];
    $email_id = $data['email_id'];
    $delivery_type=$data['delivery_type'];
    if($data['shipment_type'] == 1) {
        $shipment_type = 'Air';
    } else {
        $shipment_type = 'Sea';
    }
$currency_preference=$data['currency_preference'];
$shipment_appl=$data['shipment'];
$shipment_amount=$data['shipment_amt'];



}else
{
    $query_no = '';
    $company_name ='';
    $customer_name = '';
    $postal_address = '';
    $country_code = '';
    $contact_no = '';
    $email_id = '';
    $delivery_type='';
    $shipment_type='';
    $currency_preference='';

}


$sql12233 = "SELECT * FROM mockup_pi_for_client WHERE order_won_id=$order_won_id";
//echo $sql;exit;
$result12233 = mysqli_query($con, $sql12233);

if(mysqli_num_rows($result12233) > 0) {
    $data12233 = mysqli_fetch_array($result12233);

    $shipment_appl=$data12233['shipment'];
    $shipment_amount=$data12233['shipment_amt'];
    $currency_type=$data12233['currency_type'];
    $pidatedate=$data12233['added_on'];
    $amount=$data12233['order_amt'];
    $scope=$data12233['scopeofwork'];
    $currency_rate=$data12233['currency_rate'];
    $prototype_pi_id=$data12233['id'];
    if($currency_type==0)
    {
        $symbol="USD";
        $delivery_type=1;
        $sign="$";
    }else
    {
        $symbol="INR";
         $delivery_type=2;
         $sign="₹";
    }

}



$sql17 = "SELECT member_id FROM  lead_assigned_to_team_member WHERE lead_id = $lead_id";
$results17 = mysqli_query($con, $sql17);
$data17 = mysqli_fetch_array($results17);
$member_id = $data17['member_id'];


$sql18 = "SELECT first_name, last_name,official_no,email FROM  system_users WHERE user_id = $member_id";;
//echo $sql2; exit;
$result18 = mysqli_query($con, $sql18);
$data18 = mysqli_fetch_array($result18);
//echo "<pre>"; print_r($datass); exit;
$name = $data18["first_name"] . ' ' . $data18["last_name"];
$nomob=$data18["official_no"];
$email=$data18["email"];




  



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

/** QUERY ENDS **/


require_once('tcpdf_include.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

}
class MyCustomPDFWithWatermark extends TCPDF {
    public function Header() {
        // Get the current page break margin
        $bMargin = $this->getBreakMargin();

        // Get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;

        // Disable auto-page-break
        $this->SetAutoPageBreak(false, 0);

        // Define the path to the image that you want to use as watermark.
        $img_file = 'tcpdf/images/water.jpg';

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
 if ($delivery_type == 1) {
    $company_names = 'HONGYI <span style="color:orange;">JIG </span>RAPID TECHNOLOGIES CO., LTD.';
    $company_addresss = 'Unit H 1/F Mau Lam Comm Bldg16-18 Mau Lam St Jordan Kln, HK';
    $logo = '<img src="/var/www/hongyijig.in/pdf/internallogo.jpeg" width="200px">';
    $fob = 'FOB';
} else if ($delivery_type == 2) {
    $company_names = 'HONGYI <span style="color:orange;">JIG</span> RAPID TECHNOLOGIES';
    $company_addresss = '15/1, 2nd Floor, Rama Road Industrial Area Delhi 110015 (INDIA)';
    $logo = '<img src="/var/www/hongyijig.in/pdf/logo.jpeg" width="200px">';
    $fob = '';
} else {
    $company_names = '';
    $company_addresss = '';
    $logo = '';
    $fob = '';
}

// if($indiansupplier==1)
// {
//     $company_names = 'HONGYI <span style="color:orange;">JIG</span> RAPID TECHNOLOGIES';
//     $company_addresss = '15/1, 2nd Floor, Rama Road Industrial Area Delhi 110015 (INDIA)';
//     $logo = '<img src="/var/www/hongyijig.in/pdf/logo.jpeg" width="200px">';

// }else
// {

// $company_names = $company_names;
// $company_addresss = $company_addresss;
// $logo =$logo;

// }


$html='

<table style="width:100%; padding:3px;">
<tr>
<td style="background-color:lightgrey; text-align:center; font-weight:bold; font-size:25px; width:350px; border:1px solid black;">PERFORMA INVOICE </td>
<td style="width:50px;"></td>
<td>'.$logo.'</td>
</tr>
</table>
<br><br>
<table style="width:100%; padding:3px; font-size:9px;" rull="all" border="1">
<tr>
<td style="background-color:lightgrey; font-weight:bold; text-align:center;width:100px">QUERY NO.</td>
<td style="width:100px;">'.$query_no.'</td>
<td style="background-color:lightgrey; font-weight:bold; text-align:center;width:120px;">PI NUMBER</td>
<td style="width:120px;">PI/'.$pidatedate.$pidatemonths.$pidateyear.'/'.$query_no.'</td>
<td style="background-color:lightgrey; font-weight:bold; text-align:center;width:100px">CUSTOMER PO NO</td>
<td style="width:100px;">'.$customer_po.'</td>
</tr>
</table>

<table style="width:100%; padding:3px; font-size:9px;" rull="all" border="1">
<tr>
<td style="background-color:lightgrey; font-weight:bold; text-align:center;width:100px;">STAGE</td>
<td style="width:100px;">Sales Agreement</td>
<td style="background-color:lightgrey; font-weight:bold; text-align:center; width:120px;">DATE </td>
<td style="width:120px;">'.$pidateday.', '.$pidatemonth.' '.$pidatedate.' , '.$pidateyear.'</td>
<td style="background-color:lightgrey; font-weight:bold; text-align:center; width:100px;">QUOTATION NO. </td>
<td style="width:100px;"></td>
</tr>
</table>

<table style="width:100% padding:3px">
<tr>
<td style="border-bottom:1px solid black;"></td>
</tr>
</table>

<br><br>
<table style="width:100%; padding:3px; font-size:9px;">
<tr>
<td >BUYERS DETAILS</td>
<td >SELLERS DETAILS</td>
</tr>
<tr>
<td >

<table style="width:100%; padding:3px; font-size:9px;">

<tr >
<td style="text-align:right; font-weight:bold; width:110px; background-color:lightgrey;">COMPANY NAME</td>
<td style="width:200px;">'.$company_name.'</td>
</tr>
<tr >
<td style="text-align:right; font-weight:bold; background-color:lightgrey;">ADDRESS</td>
<td >'.$postal_address.'</td>
</tr>
<tr >
<td style="text-align:right; font-weight:bold; background-color:lightgrey;">CONTACT PERSON</td>
<td>'.$customer_name.'</td>
</tr>
<tr >
<td style="text-align:right; font-weight:bold; background-color:lightgrey;">MOBILE NO.</td>
<td>'.$contact_no.'</td>
</tr>';

$html123='<tr >
<td style="text-align:right; font-weight:bold; background-color:lightgrey;">GSTIN</td>
<td>03DTEPS5628Q1Z0</td>
</tr>';

$html.='<tr >
<td style="text-align:right; font-weight:bold; background-color:lightgrey;">E-MAIL</td>
<td>'.$email_id.'</td>
</tr>
</table>
</td>';




$html.='<td>
<table style="width:100%; padding:3px; font-size:9px;">
<tr >
<td style="text-align:right; font-weight:bold; width:110px; background-color:lightgrey;">COMPANY NAME</td>
<td style="width:200px; font-weight:bold;">'.$company_names.'</td>
</tr>
<tr >
<td style="text-align:right; font-weight:bold; background-color:lightgrey;">ADDRESS</td>
<td >'.$company_addresss.'</td>
</tr>
<tr >
<td style="text-align:right; font-weight:bold; background-color:lightgrey;">CONTACT PERSON</td>
<td>'.$name.'</td>
</tr>
<tr >
<td style="text-align:right; font-weight:bold; background-color:lightgrey;">MOBILE NO.</td>
<td>'.$nomob.'</td>
</tr>

<tr >
<td style="text-align:right; font-weight:bold; background-color:lightgrey;">E-MAIL</td>
<td>'.$email.'</td>
</tr>

<tr >
<td style="text-align:right; font-weight:bold; background-color:lightgrey;">WEB</td>
<td>www.hongyijig.com</td>
</tr>
</table>
</td>
</tr>

<tr>
<td style="border-top:1px solid black;"></td>
<td style="border-top:1px solid black;"></td>
</tr>
</table>



<table style="width:100%; padding:3px;" >
<tr>
<td style="background-color:lightgrey; text-align:center; font-weight:bold; font-size:15px;">MOCKUP</td>
</tr>
</table>
<br>
<br>

<table style="width:100%; padding:3px; font-size:8px;" border="1">
<tr style="background-color:lightgrey; text-align:center; font-weight:bold; ">
<th style="border:1px solid black; width:20%;">S.No</th>
<th style="border:1px solid black; width:60%;">Particular</th>
<th style="border:1px solid black;width:20%">AMOUNT<br/>(in '.$symbol.')</th>
</tr>';




           

           

            $html .= '<tr nobr="true">';
            $html .= '<td style="text-align:center;">1</td>';
            $html .= '<td style="text-align:center;">'.$scope.'</td>';
            $html .= '<td style="text-align:center;">'.$amount.'</td>';
                  
            $html .= '</tr>';

           
   



// $total_amt = array_sum($priceindollar) + array_sum($total_ser_amt);

$total_shipment=0;
$total_amt = array_sum($priceindollar);

$html .= '<tr>
<td style="border:1px solid black; background-color:lightgrey; text-align:right; font-weight:bold;" colspan="2" >TOTAL</td>
<td style="border:1px solid black; background-color:lightgrey; text-align:center; font-weight:bold;">'.$sign.$amount.'</td>
</tr>';


if($shipment_appl==1) {
		$html .= '<tr>
			<td style="border:1px solid black; background-color:lightgrey; text-align:right;  font-weight:bold;" colspan="2">(Approx) Shipment</td>
			<td style="border:1px solid black;  text-align:center;" >'.$sign.round($shipment_amount).'</td>
			</tr>';
}else
{
    $shipment_amount=0;
}

$grandtot=$amount+$shipment_amount;

$html.='<tr style="background-color:#eeeeee; font-weight:bold;" nobr="true">
<td colspan="2" style="text-align:right">GRAND TOTAL IN ('.$symbol.')</td>
<td  style="text-align:center"> '.$sign." ".$grandtot . ' </td>
</tr>';


$html.='<tr style="background-color:#eeeeee; font-weight:bold;" nobr="true">
<td colspan="3" style="text-align:right">AMOUNT IN WORDS: '.$symbol.' '.ucwords(getCurrencyCode($grandtot)) . ' Only</td>

</tr>';

if($currency_type==0)
{
    $inrtotal=$grandtot*$currency_rate;
    $html.='<tr style="background-color:lightgrey; font-weight:bold;" nobr="true">
    <td colspan="3" style="text-align:right;">Grand Total In INR</td>
    <td>₹ '.round($inrtotal).'</td>
    </tr>
    <tr style="background-color:#eeeeee; font-weight:bold;" nobr="true">
    <td colspan="8" style="text-align:right"   >AMOUNT IN WORDS: Rs. ' . ucwords(getCurrencyCode($inrtotal)) . ' Only</td>
    </tr>';
}


if($currency_type==1)
{
$html.='<tr style="background-color:#eeeeee; font-weight:bold;" nobr="true">
    <td colspan="3" style="text-align:right">GST as per actual at the time of invoice</td>
    </tr>';
}


$html.='</table>

<br>
<br>';



if($currency_preference==0)
{
    $inrtotal=$grandtot*$currency_rate;

$html.='<br><br><table style="padding:3px; font-size:11px;">
<tr nobr="true"><td style="">Please consider the currency exchange rate of the day at the time of making Bank Transfer through RBI of each stage. The currency rate at the time of Quotation Generated is '.$currency_rate.' / US$, so the Total Value of US $'.$grandtot.'  is Rs. '.$inrtotal.'/-<br/><br/>All the upcoming payments will be made basis on the currency exchange price of US Dollar on the day of Payment Made.</td></tr>
<tr nobr="true"><td style="color:grey; "></td></tr>
</table>';
}



$html.='<table style="width:100%; min-height:200px; font-size:9px; padding:3px;" >
<tr>
<td style="width:450px; ">
<table border="1" style="text-align:center;  padding:3px;">
<tr>
<td colspan="3">Payment Stage</td>
</tr>

<tr>
<td style="background-color:lightgrey;">STAGE</td>
<td style="width:130px; background-color:lightgrey;">STATUS</td>
<td style="width:70px; background-color:lightgrey;">PERCENTAGE</td>
<td style="width:96px; background-color:lightgrey;">AMOUNT</td>
</tr>';

$total_amount = array();
$final_price = $total_amt;

$sql13 = "SELECT payment_division, stage, percentage FROM mockup_pi_payment_terms WHERE prototype_id=$prototype_pi_id";
$results13 = mysqli_query($con, $sql13);
$paymentstep=mysqli_num_rows($results13);
$pst=0;
$paynow='';
$i = 0;
while ($datas13 = mysqli_fetch_array($results13)) {
    $percentage = $datas13['percentage'];
    // $amount = ($final_price * $percentage) / 100;
    if($i==0) {
	  $amount = (($percentage*$grandtot)/100);
	} else {
	  $amount = (($percentage*$grandtot)/100);
	}
        $html .= '<tr>
        <td style="text-align:right:">' . $datas13['payment_division'] . '</td>
        <td style="text-align:left:">'.$datas13['stage'].'</td>
        <td style="text-align:center:">' . $percentage . '%</td>
        <td style="text-align:center:">$' . round($amount) . '</td>
        </tr>';
        $total_amount[] = $amount;
        if($pst==0)
        {
            $paynow=$amount;    
        }
            $pst++;
            $i++;
    }


$html .= '<tr>
<td style="text-align:right; font-weight:bold; background-color:lightgrey;">TOTAL</td>
<td style="text-align:left; background-color:lightgrey;"></td>
<td style="text-align:left; background-color:lightgrey;"></td>
<td style="text-align:center; background-color:lightgrey;" colspan="2"> '.$sign.round(array_sum($total_amount)).'</td> 


</tr>
</table >
</td>
<td style="width:180px;" rowspan="'.$paymentstep.'">
<table border="1" style="text-align:center;  padding:3px;">
<tr>
<td style="background-color:lightgrey;">AMOUNT NEED TO PAY NOW</td>

</tr>
<tr>
<td style="font-weight:bold; font-size:20px; height:85px;">'.$sign.round($paynow).'
</td>
</tr>
</table>
</td>
</tr>
</table>
<br><br>';



$html.='<table border="1" style="padding:3px; ">
                <tr style="background-color:lightgrey; font-weight:bold; text-align:center;">
                    <th colspan="2">Account Details </th>
                </tr>';

if ($delivery_type == 1) {
    $html .= '<tr style="font-weight:bold;" nobr="true">
<td style="text-align:left; ">Account Name</td>
<td style="text-align:left; ">HONGYI JIG RAPID TECHNOLOGIES CO., LTD</td>
</tr>
<tr style="" nobr="true">
<td style="text-align:left;font-weight:bold;">Account No.</td>
<td style="text-align:left;">048-857452-838</td>
</tr>
<tr style="" nobr="true">
<td style="text-align:left;font-weight:bold;">Beneficiary Bank</td>
<td style="text-align:left;">HSBC HONG KONG BANK </td>
</tr>
<tr style="" nobr="true">
<td style="text-align:left;font-weight:bold;">Bank Address</td>
<td style="text-align:left;">1 QUEEN,S ROAD, CENTRAL, HONG KONG</td>
</tr>
<tr style="" nobr="true">
<td style="text-align:left;font-weight:bold;">SWIFT CODE</td>
<td style="text-align:left;">HSBCHKHHHKH</td>
</tr>';
} else {
    $html .= '<tr style="font-weight:bold;" nobr="true">
                <td style="text-align:border-leftleft; ">Account Name</td>
                <td style="text-align:left; "> HONGYI JIG RAPID TEC</td>
                </tr>
                <tr style="" nobr="true">
                <td style="text-align:left;font-weight:bold;">Account No.</td>
                <td style="text-align:left;"> 2016256010761</td>
                </tr>
                <tr style="" nobr="true">
                <td style="text-align:left;font-weight:bold;">Beneficiary Bank</td>
                <td style="text-align:left;">CANARA BANK</td>
                </tr>
                <tr style="" nobr="true">
                <td style="text-align:left;font-weight:bold;">Branch</td>
                <td style="text-align:left;">DELHI KESHAV PURAM</td>
                </tr>
                <tr style="" nobr="true">
                <td style="text-align:left;font-weight:bold;">IFSC Code</td>
                <td style="text-align:left;">CNRB0002016</td>
                </tr>
                <tr style="" nobr="true">
                <td style="text-align:left;font-weight:bold;">MICR Code</td>
                <td style="text-align:left;">110015072</td>
                </tr>';
}
$html .= '</table>';


$html .='<table style="width:100%; padding:3px; font-size:8px;">
<tr>
<td style="font-weight:bold;">TERMS OF TRANSACTIONS</td>
</tr>

<tr>
1.Order execution terms will be based on the quotation agreement (as per the Quotation No. mentioned. <br>
2.All payments will be consider in terms of US$ amount and subjected to exchange rate vaiations. the difference will be charged from the client.<br> 
3.The bank transfer fee will be paid by the customer, in case of any difference appeared, will be charged in the last payment before delivery.
</tr>
</table>';  


//echo $html; exit;
//echo $footer_logo_html; exit;
// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.



$filelocation = $_SERVER['DOCUMENT_ROOT'].'/image_bank/client_performa';
$fileNL = $filelocation."/".$query_no.".pdf"; //Linux

$pdf->Output($fileNL, 'I');

// header('location:'.redirecturl.$query_no);


//============================================================+
// END OF FILE
//============================================================+
