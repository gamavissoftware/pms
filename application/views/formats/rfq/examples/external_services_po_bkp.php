<?php
define('redirecturl', 'https://hongyijig.in/index.php/Proposal/preview_service_po/');
include('mysqlconfig.php');
$service_info_id = $_GET['service_info_id'];

$sql = "SELECT a.lead_id, a.service_amt, b.company_name, b.address, b.mobile_no, b.email_id, b.website, b.outside_india, b.added_on, c.delivery_date, c.delivery_type, c.po_no, d.beneficiary_name, d.beneficiary_address, d.beneficiary_account_no, d.beneficiary_bank_name, d.beneficiary_bank_address, d.beneficiary_swift_code, d.beneficiary_bank_code, d.intermediatery_bank_name, d.intermediatery_bank_address, d.intermediatery_bank_swift_code, e.service_name, e.scopeofwork, e.terms, f.spokes_person_name FROM bom_pi_services_info a LEFT JOIN external_suppliers b ON a.external_suppliers=b.id LEFT JOIN external_po_services c ON c.service_info_id=a.id LEFT JOIN ext_ser_bank_details d ON d.service_info_id=a.id LEFT JOIN services e ON e.id=a.hjig_services LEFT JOIN external_supplier_spokesperson_detail f ON f.external_supp_id=a.external_suppliers WHERE a.id=$service_info_id";
// echo $sql;exit;
$result = mysqli_query($con, $sql);
 
 $lead_id = '';
 $company_name = '';
 $address = '';
 $mobile_no = '';
 $email_id = '';
 $website = '';
 $delivery_date = '';
 $delivery_type = '';
 $po_no = '';
 $beneficiary_name = '';
 $beneficiary_address = '';
 $beneficiary_account_no = '';
 $beneficiary_bank_name = '';
 $beneficiary_bank_address = '';
 $beneficiary_swift_code = '';
 $beneficiary_bank_code = '';
 $intermediatery_bank_name = '';
 $intermediatery_bank_address = '';
 $intermediatery_bank_swift_code = '';
 $service_name = '';
 $service_amt = '';
 $spokes_person_name = '';
 $scopeofwork = '';
 $terms = '';
 $outside_india = '';
 $added_on = '';
if (mysqli_num_rows($result) > 0) {
    $data = mysqli_fetch_array($result);
    $lead_id = $data['lead_id'];
    $company_name = $data['company_name'];
    $address = $data['address'];
    $mobile_no = $data['mobile_no'];
    $email_id = $data['email_id'];
    $website = $data['website'];
    $delivery_date = $data['delivery_date'];
    $delivery_type = $data['delivery_type'];
    $po_no = $data['po_no'];
    $beneficiary_name = $data['beneficiary_name'];
    $beneficiary_address = $data['beneficiary_address'];
    $beneficiary_account_no = $data['beneficiary_account_no'];
    $beneficiary_bank_name = $data['beneficiary_bank_name'];
    $beneficiary_bank_address = $data['beneficiary_bank_address'];
    $beneficiary_swift_code = $data['beneficiary_swift_code'];
    $beneficiary_bank_code = $data['beneficiary_bank_code'];
    $intermediatery_bank_name = $data['intermediatery_bank_name'];
    $intermediatery_bank_address = $data['intermediatery_bank_address'];
    $intermediatery_bank_swift_code = $data['intermediatery_bank_swift_code'];
    $service_name = $data['service_name'];
    $service_amt = $data['service_amt'];
    $spokes_person_name = $data['spokes_person_name'];
    $scopeofwork = $data['scopeofwork'];
    $terms = $data['terms'];
    $outside_india = $data['outside_india'];
    $added_on = $data['added_on'];
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
<td style="background-color:lightgrey; text-align:center; font-weight:bold; font-size:15px;">'.$service_name.' PURCHASE ORDER 
</td>
</tr>

    </table>

    <br>
<br>
    <table style="width:100%;  padding:5px; font-size:10px;">
    
<tr>
<td width="60%">
<table style="width:100%;  padding:3px;" border="1">
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">COMPANY NAME</td>
<td>'.$company_name.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">COMPANY ADDRESS</td>
<td>'.$address.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">CONTACT PERSON</td>
<td>'.$spokes_person_name.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">MOBILE NUMBER</td>
<td>'.$mobile_no.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">EMAIL ID
</td>
<td>'.$email_id.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">WEBSITE
</td>
<td>'.$website.'</td>
</tr>
</table>
</td>
<td width="40%">
<table style="width:100%;  padding:3px;" border="1">
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">ORDER DATE
</td>
<td>'.date('d-m-Y', strtotime($added_on)).'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">DELIVERY DATE 
</td>
<td>'.date('d-m-Y', strtotime($delivery_date)).'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">DELIVERY TYPE
</td>
<td>'.$delivery_type.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">PO NUMBER 
</td>
<td>'.$po_no.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">BUSINESS LICENCE NO. 
</td>
<td></td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">GST NO (IF INDIA SUPPLIER)
</td>
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

<table style="width:100%;  padding:5px; font-size:10px;">

<tr>
<td width="60%">
<table style="width:100%;  padding:5px; font-size:10px;">

<tr>
<td>SCOPE OF WORK 
</td>
</tr>
<tr>
<td style="border:1px solid black; height:100px;">'.$scopeofwork.'
</td>
</tr>


</table>
</td>
<td width="40%">
<table style="width:100%;  padding:5px; font-size:10px;">

<tr>
<td>SERVICES INCLUDED
</td>
</tr>
<tr>
<td style="border:1px solid black; height:100px;">'.$service_name.'
</td>
</tr>

</table>
</td>
</tr>


</table>

<br>
<br>
    <table style="width:100%;  padding:3px;">
    
<tr >
<td style="background-color:lightgrey; text-align:center; font-weight:bold; font-size:10px;">BILL OF MATERIAL 
 
</td>
</tr>

    </table>

    <br>
<br>
<table style="width:100%; padding:3px; font-size:10px; text-align:center;" border="1">
<tr>
<th width="50px" style="background-color:lightgrey;">S.No
</th>
<th width="200px" style="background-color:lightgrey;">PARTICULARS 
</th>
<th width="300px" style="background-color:lightgrey;">AMOUNT
</th>
</tr>
<tr>
<td>1</td>
<td>'.$service_name.'</td>
<td>'.$service_amt.'</td>
</tr>

<tr>
<td colspan="2" style="background-color:lightgrey; text-align:right;">TOTAL AMOUNT '.$currency.'</td>
<td style="text-align:center;">'.$service_amt.'</td>
</tr>

<tr>
<td colspan="2" style="background-color:lightgrey; text-align:right;">TOTAL AMOUNT IN WORDS 
</td>
<td style="text-align:center;">'.ucwords(getCurrencyCode($service_amt)).' Only</td>
</tr>
</table>
<br pagebreak="true"/>

<table style=" padding:3px; font-size:10px;">
<tr style="background-color:lightgrey;">
<td style="text-align:center; font-weight:bold;" width="60%">PAYMENT TERMS 
</td>
<td style="text-align:center; font-weight:bold;" width="40%">SUPPLIERS BANK DETAILS 
</td>
</tr>

</table>
<br>
<br>
<table style=" font-size:10px;" >
<tr >
<td width="60%" style="border:1px solid black;">
<table border="1"  style="padding:3px; width:100%; text-align:center;">
<tr style="background-color:lightgrey;">
<td style="text-align:center;  width:50px;">S.NO
</td>
<td style="text-align:center;  width:140px;">PAYMENT DIVISION
</td>
<td style="text-align:center; ">PAYMENT STAGE
</td>
<td style="text-align:center; ">% AGE 
</td></tr>';

$sql1 = "SELECT payment_division, stage, percentage FROM ext_ser_payment_terms WHERE service_info_id=$service_info_id";
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
        <td>'.$i.'</td>
        <td>'.$payment_division.'</td>
        <td>'.$stage.'</td>
        <td>'.$percentage.'%</td>
        </tr>';

        $total_percentage[] = $percentage;
    }
}
$html .= '<tr>
<td colspan="3" style="background-color:lightgrey; text-align:right;">Total</td>
<td>'.array_sum($total_percentage).'%</td>
</tr>
</table>
</td>
<td width="40%" style="border:1px solid black;">
<table style=" font-size:10px; padding:3px; width:100%;" border="1">
<tr>
<td style="background-color:lightgrey; text-align:right;">Beneficiary Name</td>
<td>'.$beneficiary_name.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right;">Beneficiary Address
</td>
<td>'.$beneficiary_address.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right;">Beneficiary Account no.
</td>
<td>'.$beneficiary_account_no.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right;">Beneficiary Bank Name
</td>
<td>'.$beneficiary_bank_name.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right;">Beneficiary Bank Address
</td>
<td>'.$beneficiary_bank_address.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right;"> Beneficiary Swift Code
</td>
<td>'.$beneficiary_swift_code.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right;">Beneficiary Bank Code
</td>
<td>'.$beneficiary_bank_code.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right;">Intermediatery Bank Name 
</td>
<td>'.$intermediatery_bank_name.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right;">Intermediatery Bank Address
</td>
<td>'.$intermediatery_bank_address.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right;">Intermediatery Bank Swift Code

</td>
<td>'.$intermediatery_bank_swift_code.'</td>
</tr>
</table>
</td>
</tr>
</table>
<br>
<br>
    <table style="width:100%;  padding:3px;">
<tr>
<td style="background-color:lightgrey; text-align:center; font-weight:bold; font-size:10px;">TERMS & CONDITIONS
</td>
</tr>


    </table>
    <br>
<br>
    <table style="font-size:10px;">
    <tr>
    <td style="border-bottom:1px solid black; height:120px;"><span style="font-weight:bold;">BASIC TERMS</span><br><i>'.$terms.'
    </td>
    </tr>
    </table>

    <br>
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
