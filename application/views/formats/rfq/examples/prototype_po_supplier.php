<?php
define('redirecturl', 'https://hongyijig.in/index.php/Proposal/preview_service_po/');
include('mysqlconfig.php');
$order_won_id = $_GET['order_won_id'];
$lead_id = $_GET['lead_id'];
$supplier_id = $_GET['suplier'];

$sql = "SELECT a.id, a.added_on,a.delivery_date, a.delivery_type, a.service_amt,a.supplier_amt, b.company_name, b.company_registered_address, b.company_website, b.license_no, b.supplier_location, c.company_spokes_person_name, c.spokes_person_mobile, c.spokes_person_emailid, d.beneficiary_name, d.beneficiary_address, d.beneficiary_account_no, d.beneficiary_bank_name, d.beneficiary_bank_address, d.beneficiary_swift_code, d.beneficiary_bank_code, d.intermediatery_bank_name, d.intermediatery_bank_address, d.intermediatery_bank_swift_code FROM prototype_services a JOIN suppliers b ON b.supplier_id=a.supplier_id JOIN supplier_spokes_person_detail c ON c.supplier_id=b.supplier_id JOIN prototype_bank_details d ON d.prototype_id=a.id WHERE a.order_won_id=$order_won_id";
// echo $sql;exit;
$result = mysqli_query($con, $sql);
 

 $prototype_id = '';
 $company_name = '';
 $address = '';
 $mobile_no = '';
 $email_id = '';
 $website = '';
 $delivery_date = '';
 $delivery_type = '';
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
 $license_no = '';
 $supplier_location = '';
 $supplier_original_price=0;
if (mysqli_num_rows($result) > 0) {
    $data = mysqli_fetch_array($result);
    $prototype_id = $data['id'];
    $company_name = $data['company_name'];
    $address = $data['company_registered_address'];
    $mobile_no = $data['spokes_person_mobile'];
    $email_id = $data['spokes_person_emailid'];
    $website = $data['company_website'];
    $delivery_date = $data['delivery_date'];
    $delivery_type = $data['delivery_type'];
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
    $service_name = 'PROTOTYPING';
    $service_amt = $data['service_amt'];
    $spokes_person_name = $data['company_spokes_person_name'];
    $scopeofwork = $data['scopeofwork'];
    $terms = $data['terms'];
    $outside_india = $data['outside_india'];
    $added_on = $data['added_on'];
    $license_no = $data['license_no'];
    $supplier_location = $data['supplier_location'];
    $supplier_original_price=$data['supplier_amt'];
}

if($supplier_location == 101) {
    $in_words = '(in INR)';
    $symbol="₹";
} else {
    $in_words = '(in CNY)';
    $symbol="¥";
}

$po_no = str_pad($ext_service_id+1, 3, '0', STR_PAD_LEFT);


/** check diff between final and supplier quote **/

$businesscaseprice=$service_amt;
$supplier_price=$supplier_original_price;

if ($supplier_price>=$businesscaseprice) {


    $diff = $supplier_price - $businesscaseprice;
    //echo $supplier_price."<br/>".$businesscaseprice; exit;
    $getpercentage = $diff * 100;
    //echo $getpercentage."<br/>".$supplier_price; exit;
    $getextraper = $getpercentage / $supplier_price;

    $percentfactor = $getextraper;
   // echo $percentfactor; exit;

} else {
    echo "NEGOTIATED PRICE CANNOT BE GREATER THAN SUPPLIER PRICE";
    //exit;
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
<td style="background-color:lightgrey; text-align:center; font-weight:bold; font-size:15px;">'.$service_name.' PURCHASE ORDER 
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
<td>'.date('d-m-Y', strtotime($added_on)).'</td>


</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">COMPANY ADDRESS</td>
<td>'.$address.'</td>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">DELIVERY DATE </td>
<td>'.date('d-m-Y', strtotime($delivery_date)).'</td>
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
<td style="background-color:lightgrey; text-align:center; font-weight:bold; font-size:10px;">BILL OF MATERIAL 
 
</td>
</tr>

    </table>

    <br>
<br>
  <table style="width:100%; padding:3px; font-size:8px;" border="1">
<tr style="background-color:lightgrey; text-align:center; font-weight:bold; ">
<th style="border:1px solid black; width:20px;">S.No</th>
<th style="border:1px solid black; width:100px;">Part Name<br>Part Picture</th>
<th style="border:1px solid black; width:100px;">Part Weight<span style="font-size:8px;">(BENCHMARKED)</span></th>
<th style="border:1px solid black;width:100px;">QPS<br><span style="font-size:8px;">(Part Quality Per Set)</span></th>
<th style="border:1px solid black;width:100px;">TOTAL QTY</th>
<th style="border:1px solid black; width:120px;">CFM<br><span style="font-size:8px;">Colour/Finish/Material</span></th>
<th style="border:1px solid black;width:100px;">AMOUNT<br/>'.$in_words.'</th>
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

            $part_amount=$datas3['amount'];
         //   echo $part_amount; exit;
            $percentfactor1=$percentfactor/100;
         //   echo $percentfactor; exit;
            $part_amount_less=$part_amount*$percentfactor1;
            $part_amount=$part_amount-$part_amount_less;

           // echo $part_amount; exit;
           

           

            $html .= '<tr nobr="true">';
            $html .= '<td>'.$i.'</td>
                    <td>'.$datas['name'].'<br><img src="'.IMAGEPATH.$datas['image'].'" width="100px"></td>
                    <td>L-'.$part_length1.'<br/> W-'.$part_width1.'<br/> H-'.$part_height1.'</td>
                    <td>'.$datas['qps'].'</td>
                    <td>'.$datas['monthproduction'].'</td>
                    <td><strong>Color-</strong>'.$color1.'<br/><strong>Finish-</strong> '.$fin1.'<br/><strong>Material-</strong>'.$material1.'</td>
                    <td style="text-align:center;">'.round($part_amount).'</td>';
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

       
            $part_amount=$datas4['amount'];
            //echo $percentfactor; exit;
            $percentfactor1=$percentfactor/100;
             //   echo $percentfactor; exit;
            $part_amount_less=$part_amount*$percentfactor1;
            $part_amount=$part_amount-$part_amount_less;

$html .= '<tr>
            <td>'.$i.'</td>
            <td>'.$data['part_name'].'<br><img src="'.image_url.$data['part_picture'].'" width="100px"></td>
            <td>L-'.$data['part_size_length'].'<br/> W-'.$data['part_size_width'].'<br/> H-'.$data['part_size_height'].'</td>
            <td>'.$data['qps'].'</td>
            <td>'.$data['total_qty'].'</td>
            <td><strong>Color-</strong>'.$color.'<br/><strong>Finish-</strong> '.$fin.'<br/><strong>Material-</strong>'.$material.'</td>
            <td style="text-align:center;">'.round($part_amount).'</td>
          </tr>';

          $total_part_amount[] = round($part_amount);
    $i++;
    }
}

if(count($total_part_amount)>0)
{
    $total=array_sum($total_part_amount);

}else
{
    $total=0;
}


$html .= '<tr>
<td style="border:1px solid black; background-color:lightgrey; text-align:right; font-weight:bold;" colspan="6" >TOTAL '.$in_words.'</td>
<td style="border:1px solid black; background-color:lightgrey; text-align:center; font-weight:bold;">'.$total.'</td>
</tr>';

$html.='<tr style="background-color:#eeeeee; font-weight:bold;" nobr="true">
<td colspan="6" style="text-align:right">GRAND TOTAL '.$in_words.'</td>
<td  style="text-align:center;">'.$total.'</td>
</tr>';


$html.='<tr style="background-color:#eeeeee; font-weight:bold;" nobr="true">
<td colspan="7" style="text-align:right">AMOUNT IN WORDS:'.ucwords(getCurrencyCode($total)).'</td>
</tr>';


if($supplier_location==101){

$html.='<tr style="background-color:#eeeeee; font-weight:bold;" nobr="true">
    <td colspan="7" style="text-align:right">GST as per actual at the time of invoice</td>
    </tr>';
}

$html.='</table>

<br>
<br>';

$html .= '<br pagebreak="true"/>

<table style=" padding:3px; font-size:10px;">
<tr style="background-color:lightgrey;">
<td style="text-align:center; font-weight:bold;">PAYMENT TERMS  & SUPPLIERS BANK DETAILS 
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

$sql1 = "SELECT payment_division, stage, percentage FROM prototype_payment_terms WHERE prototype_id=$prototype_id";
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

$html.='
<table border="1"  style="padding:3px; width:100%; text-align:center;">
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

<br>
<br>
    <table style="width:100%;  padding:3px;">
<tr>
<td style="background-color:lightgrey; text-align:center; font-weight:bold; font-size:10px;">TERMS & CONDITIONS
</td>
</tr>


    </table>
    <br>';


   $html .=$terms;

   $html .='</td>
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
