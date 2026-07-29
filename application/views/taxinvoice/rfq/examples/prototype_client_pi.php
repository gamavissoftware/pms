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

$sql = "SELECT b.delivery_type,b.shipment_type, a.unique_id, a.company_name, a.customer_name, a.postal_address, a.country_code, a.contact_no, a.email_id, d.shipment,d.shipment_amt,d.currency_preference, c.billing_address FROM leads a LEFT JOIN bom_pi_sales_client_info c  ON a.id=c.lead_id LEFT JOIN bom_pi_sales_project_info b ON a.id=b.lead_id  LEFT JOIN prototype_quoted_price d ON d.lead_id=a.id WHERE a.id=$lead_id";
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



$sqlo = "SELECT id, pi_generated_On,customer_po,currency_rate FROM order_won WHERE lead_id=$lead_id";
 //echo $sqlo;exit;
$resulto = mysqli_query($con, $sqlo);

if(mysqli_num_rows($resulto) > 0) {
    $data1211 = mysqli_fetch_array($resulto);
    $pidateday = date('D',strtotime($data1211['pi_generated_On']));
    $pidatedate = date('d',strtotime($data1211['pi_generated_On']));
    $pidateyear= date('Y',strtotime($data1211['pi_generated_On']));
    $pidatemonth= date('M',strtotime($data1211['pi_generated_On']));
    $pidatemonths= date('m',strtotime($data1211['pi_generated_On']));
    $customer_po= $data1211['customer_po'];
    $currency_rate=$data1211['currency_rate'];
    $order_won_id=$data1211['id'];

    
}else
{
     $pidateday = '';
    $pidatedate = '';
    $pidateyear= '';
    $pidatemonth= '';
    $pidatemonths='';
    $customer_po= '';
    $currency_rate=0;
    $order_won_id=0;
   

}


$sql14 = "SELECT id, order_amt, currency_type,currency_rate,shipment,shipment_amt FROM prototype_pi_for_client WHERE order_won_id=$order_won_id";
$results14 = mysqli_query($con, $sql14);
$datas14 = mysqli_fetch_array($results14);
$prototype_pi_id = $datas14['id'];
$order_amt = $datas14['order_amt'];
$shipment=$datas14['shipment'];
$shipment_amt=$datas14['shipment_amt'];
$currency_rate=$datas14['currency_rate'];
if($datas14['currency_type'] == 1) {
    $sign = '₹';
    $currency_type = 'INR'; 
    $delivery_type=2;   
} else {
    $sign = '$';
    $currency_type = 'USD';  
    $delivery_type=1; 
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
    }       // $mouldoverallweight[] = $datas122345allp['mould_weight'];
    }


$suppliergivenprice = array_sum($partprice);

 

$businesscaseprice = $finalprice;

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

if($indiansupplier==1)
{
    $company_names = 'HONGYI <span style="color:orange;">JIG</span> RAPID TECHNOLOGIES';
    $company_addresss = '15/1, 2nd Floor, Rama Road Industrial Area Delhi 110015 (INDIA)';
    $logo = '<img src="/var/www/hongyijig.in/pdf/logo.jpeg" width="200px">';

}else
{

$company_names = $company_names;
$company_addresss = $company_addresss;
$logo =$logo;

}


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
<td style="background-color:lightgrey; text-align:center; font-weight:bold; font-size:15px;">PROTOTYPE BOM </td>
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
<th style="border:1px solid black;width:100px;">AMOUNT<br/>(in '.$sign.')</th>
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
           // echo $sql3; exit;
            $result3 = mysqli_query($con,$sql3);
            $datas3 = mysqli_fetch_array($result3);
           // echo $datas3['amount']; exit;


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
            $html .= '<td style="text-align:center;">'.$i.'</td>
                    <td style="text-align:center;">'.$datas['name'].'<br><img src="'.IMAGEPATH.$datas['image'].'" width="100px"></td>
                    <td style="text-align:center;">L-'.$part_length1.'<br/> W-'.$part_width1.'<br/> H-'.$part_height1.'</td>
                    <td style="text-align:center;">'.$datas['qps'].'</td>
                    <td style="text-align:center;">'.$datas['monthproduction'].'</td>
                    <td style="text-align:left;"><strong>Color-</strong>'.$color1.'<br/><strong>Finish-</strong> '.$fin1.'<br/><strong>Material-</strong>'.$material1.'</td>
                    <td style="text-align:center;">'.$part_amount.'</td>';
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
            <td  style="text-align:center;">'.$i.'</td>
            <td  style="text-align:center;">'.$data['part_name'].'<br><img src="'.image_url.$data['part_picture'].'" width="100px"></td>
            <td  style="text-align:center;">L-'.$data['part_size_length'].'<br/> W-'.$data['part_size_width'].'<br/> H-'.$data['part_size_height'].'</td>
            <td  style="text-align:center;">'.$data['qps'].'</td>
            <td  style="text-align:center;">'.$data['total_qty'].'</td>
            <td  style="text-align:center;"><strong>Color-</strong>'.$color.'<br/><strong>Finish-</strong> '.$fin.'<br/><strong>Material-</strong>'.$material.'</td>
            <td  style="text-align:center;">'.$part_amount.'</td>
          </tr>';

          $total_part_amount[] = round($part_amount);
    $i++;
    }
}

// $total_amt = array_sum($priceindollar) + array_sum($total_ser_amt);

if((mysqli_num_rows($result20) == 0) && $delivery_type==2) {
    if(array_sum($mould_weight) <= 1000) {
        $total_mould_weight = 1000;
    } else {
        $total_mould_weight = array_sum($mould_weight);
    }
// $total_shipment = ($total_mould_weight/1000)*$shipment;
$total_shipment = $shipment;
$duties = ((array_sum($priceindollar)+$total_shipment)*15)/100;
} else{
 $total_shipment = 0;
 $duties = 0;
}

$total_amt = array_sum($priceindollar) + array_sum($total_ser_amt) + $total_shipment + $duties;

$html .= '<tr>
<td style="border:1px solid black; background-color:lightgrey; text-align:right; font-weight:bold;" colspan="6" >TOTAL</td>
<td style="border:1px solid black; background-color:lightgrey; text-align:center; font-weight:bold;">'.$sign.$order_amt.'</td>
</tr>';


if($shipment_appl==1) {
		$html .= '<tr>
			<td style="border:1px solid black; background-color:lightgrey; text-align:right;  font-weight:bold;" colspan="6">(Approx) Shipment</td>
			<td style="border:1px solid black;  text-align:center;" >'.$sign.round($shipment_amount).'</td>
			</tr>';
}else
{
    $shipment_amount=0;
}

$grandtot=$order_amt+$shipment_amount;

$html.='<tr style="background-color:#eeeeee; font-weight:bold;" nobr="true">
<td colspan="6" style="text-align:right">GRAND TOTAL IN ('.$currency_type.')</td>
<td  style="text-align:center"> '.$sign." ".$grandtot . ' </td>
</tr>';


$html.='<tr style="background-color:#eeeeee; font-weight:bold;" nobr="true">
<td colspan="7" style="text-align:right">AMOUNT IN WORDS: '.$currency_type.' '.ucwords(getCurrencyCode($grandtot)) . ' Only</td>

</tr>';

if($delivery_type==1)
{
    $inrtotal=$grandtot*$currency_rate;
    $html.='<tr style="background-color:lightgrey; font-weight:bold;" nobr="true">
    <td colspan="6" style="text-align:right;">Grand Total In INR</td>
    <td>₹ '.round($inrtotal).'</td>
    </tr>
    <tr style="background-color:#eeeeee; font-weight:bold;" nobr="true">
    <td colspan="8" style="text-align:right"   >AMOUNT IN WORDS: Rs. ' . ucwords(getCurrencyCode($inrtotal)) . ' Only</td>
    </tr>';
}

if($currency_type==1)
{
$html.='<tr style="background-color:#eeeeee; font-weight:bold;" nobr="true">
    <td colspan="11" style="text-align:right">GST as per actual at the time of invoice</td>
    </tr>';
}


$html.='</table>

<br>
<br>';



if((mysqli_num_rows($result20) == 0) && $delivery_type==2 && $currency_preference==0)
{
$html.='<br><br><table style="padding:3px; font-size:11px;">
<tr nobr="true"><td style="">Please consider the currency exchange rate of the day at the time of making Bank Transfer through RBI of each stage. The currency rate at the time of Quotation Generated is '.$currency_rate.' / US$, so the Total Value of US $'.$order_amt.'  is Rs. '.$inrtotal.'/-<br/><br/>All the upcoming payments will be made basis on the currency exchange price of US Dollar on the day of Payment Made.</td></tr>
<tr nobr="true"><td style="color:grey; "></td></tr>
</table>';
}



$html.='<table style="width:100%; min-height:200px; font-size:9px; padding:3px;" >
<tr>
<td style="width:450px; ">
<table border="1" style="text-align:center;  padding:3px;">
<tr>
<td colspan="4">Payment Stage</td>
</tr>

<tr>
<td style="background-color:lightgrey;">STAGE</td>
<td style="width:130px; background-color:lightgrey;">STATUS</td>
<td style="width:70px; background-color:lightgrey;">PERCENTAGE</td>
<td style="width:96px; background-color:lightgrey;">AMOUNT</td>
</tr>';

$total_amount = array();
$final_price = $total_amt;

$sql13 = "SELECT payment_division, stage, percentage FROM prototype_pi_payment_terms WHERE prototype_id=$prototype_pi_id";
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
