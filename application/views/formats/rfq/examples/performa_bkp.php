<?php

/** QUERY ENDS **/
define('image_url', '/var/www/hongyijig.in/assets/client_bom_data/part_image/');

define('redirecturl', 'https://hongyijig.in/index.php/Proposal/preview_performa/');
include('mysqlconfig.php');

$lead_id = $_GET['lead_id'];
$option_id = $_GET['option'];
$supplier = $_GET['suplier'];
$negotiation = $_GET['negotiation'];

if ($negotiation == '') {
    $negotiation = 0;   
}

$sql = "SELECT b.delivery_type,a.unique_id, a.company_name, a.customer_name, a.postal_address, a.country_code, a.contact_no, a.email_id FROM leads a LEFT JOIN bom_pi_sales_project_info b ON a.id=b.lead_id WHERE a.id=$lead_id";
//echo $sql;exit;
$result = mysqli_query($con, $sql);

if(mysqli_num_rows($result) > 0) {
    $data = mysqli_fetch_array($result);
    $query_no = $data['unique_id'];
    $company_name = $data['company_name'];
    $customer_name = $data['customer_name'];
    $postal_address = $data['postal_address'];
    $country_code = $data['country_code'];
    $contact_no = $data['contact_no'];
    $email_id = $data['email_id'];
    $delivery_type=$data['delivery_type'];
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

}





$sqlo = "SELECT pi_generated_On,customer_po,currency_rate FROM order_won WHERE lead_id=$lead_id";
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

    
}else
{
     $pidateday = '';
    $pidatedate = '';
    $pidateyear= '';
    $pidatemonth= '';
    $pidatemonths='';
    $customer_po= '';
    $currency_rate=0;
   

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
} else if ($delivery_type == 2) {
    $company_names = 'HONGYI <span style="color:orange;">JIG</span> RAPID TECHNOLOGIES';
    $company_addresss = '15/1, 2nd Floor, Rama Road Industrial Area Delhi 110015 (INDIA)';
    $logo = '<img src="/var/www/hongyijig.in/pdf/logo.jpeg" width="200px">';
} else {
    $company_names = '';
    $company_addresss = '';
    $logo = '';
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
<td style="background-color:lightgrey; text-align:center; font-weight:bold; font-size:15px;">LIST OF MOULDS WITH SPECS </td>
</tr>
</table>
<br>
<br>

<table style="width:100%; padding:3px; font-size:8px;" border="1">
<tr style="background-color:lightgrey; text-align:center; font-weight:bold; ">
<th style="border:1px solid black; width:65px;">S.No</th>
<th style="border:1px solid black; width:75px;">Part Name</th>
<th style="border:1px solid black;">Part CFM</th>
<th style="border:1px solid black;">Part Picture</th>
<th style="border:1px solid black;">Part Size</th>
<th style="border:1px solid black; width:40px;">Cavity</th>
<th style="border:1px solid black;">Runner Type/Make</th>
<th style="border:1px solid black; width:70px;">Mould Sizes (mm) </th>
<th style="border:1px solid black; width:65px;">Mould Material</th>
<th style="border:1px solid black; width:40px;" >Weight in Kgs.</th>
<th style="border:1px solid black; width:45px;">Cost FOB (USD) </th>
</tr>';

/** SERVICE AMOUNT **/
$sql_ser = "SELECT a.service_amt FROM bom_pi_services_info a JOIN services b ON a.hjig_services=b.id WHERE lead_id = $lead_id";
$results_ser = mysqli_query($con, $sql_ser);

$total_ser_amt = array();
$total_ser_amt[] = 0;
while($data_ser = mysqli_fetch_array($results_ser)) {
$service_amt = $data_ser['service_amt'];
$total_ser_amt[] = round($service_amt);
}
/** FINAL PRICE FOR THIS QUOTE **/

$sqlfinal = "SELECT final_price FROM client_quoted_price WHERE lead_id = $lead_id AND option_id=$option_id AND supplier_id=$supplier";
$resultsfinal = mysqli_query($con, $sqlfinal);
$datasfinalp = mysqli_fetch_array($resultsfinal);
$finalprice = $datasfinalp['final_price'];

/** GET ALL PARTS LOOP AND CALCULATE THE PRICE GIVEN **/

$allmoulds = "SELECT id FROM bom_moulds_detail WHERE lead_id=$lead_id";
$resultsallmould = mysqli_query($con, $allmoulds);
$partprice = array();
$partprice[] = 0;
$mouldoverallweight = array();
$mouldoverallweight[] = 0;
while ($dataallmould = mysqli_fetch_array($resultsallmould)) {
    $allmouldid = $dataallmould['id'];
    $sql12allp = "SELECT mould_weight,price_in_dollar FROM sourcing_quotation WHERE option_id=$option_id AND mould_id = $allmouldid AND lead_id=$lead_id AND supplier_id=$supplier";

    // echo $sql12allp;exit;
    $results12allp = mysqli_query($con, $sql12allp);
    $mouldoptionnumallp = mysqli_num_rows($results12allp);
    if ($mouldoptionnumallp > 0) {
        $datas122345allp = mysqli_fetch_array($results12allp);
        $partprice[] = $datas122345allp['price_in_dollar'];
        $mouldoverallweight[] = $datas122345allp['mould_weight'];
    } else {

        $sql12zeroallp = "SELECT mould_weight,price_in_dollar FROM sourcing_quotation WHERE option_id=0 AND mould_id = $allmouldid AND lead_id=$lead_id AND supplier_id=$supplier";

        $results12zeroallp = mysqli_query($con, $sql12zeroallp);
        $datas122345zalpp = mysqli_fetch_array($results12zeroallp);

        $partprice[] = $datas122345zalpp['price_in_dollar'];
        $mouldoverallweight[] = $datas122345zalpp['mould_weight'];
    }
}

$suppliergivenprice = array_sum($partprice);
// echo $suppliergivenprice;exit;


$businesscaseprice = $finalprice;
$suppliergivenweight = array_sum($mouldoverallweight);
// echo $businesscaseprice.'<br/>'.$suppliergivenprice; exit;

if ($negotiation == 1) {
        $sqlneg = "SELECT negotiated_price FROM order_won WHERE lead_id = $lead_id";
        $resultneg = mysqli_query($con, $sqlneg);
        if(mysqli_num_rows($resultneg) > 0) {
            $dataneg = mysqli_fetch_array($resultneg);
            $businesscaseprice = $dataneg['negotiated_price'];
        }
    }


if ($suppliergivenprice < $businesscaseprice) {

    $diff = $businesscaseprice - $suppliergivenprice;
    $getpercentage = $diff * 100;
    $getextraper = $getpercentage / $suppliergivenprice;

    $percentfactor = $getextraper;
    //echo $percentfactor; exit;

} else {
    echo "BUSINESS CASE PRICE IS LESS THAN SUPPLIER PRICE! HENCE QUOTATION CANNOT BE CREATED";
    exit;
}

$query = "SELECT id, type FROM bom_moulds_detail WHERE lead_id = $lead_id";
$results = mysqli_query($con, $query);

if (mysqli_num_rows($results) > 0) {
    $mld = 1;
    $mouldcheck = array();
    $mould_weight = array();
    $priceindollar = array();
    while ($datas = mysqli_fetch_array($results)) {
        $mould_id = $datas['id'];
        $type = $datas['type'];

            if ($type == 1) {
                $mould_type = 'Single';
            } else if ($type == 2) {
                $mould_type = 'Multi';
            } else if ($type == 3) {
                $mould_type = 'Family';
            }
        // $sql9 = "SELECT a.remarks, b.material_grade as tool_cavity, c.material_grade as mould_base, d.material_grade as core_cavity FROM bom_moulds_detail a LEFT JOIN steel_type b ON a.mouldcavitysteel=b.id LEFT JOIN steel_type c ON a.mouldbasesteel=c.id LEFT JOIN steel_type d ON a.mouldcoresteel=d.id WHERE a.id = $mould_id AND lead_id = $lead_id";
        // $result9 = mysqli_query($con, $sql9);
        // $data9 = mysqli_fetch_array($result9);

        $sql1 = "SELECT id, mould_id, name, image, partfinish, partlength, partheight, partwidth, cavity FROM bom_part_details WHERE lead_id = $lead_id AND mould_id = $mould_id";
        $result1 = mysqli_query($con, $sql1);
        $partscount = mysqli_num_rows($result1);
        $i = 1;

        while ($data1 = mysqli_fetch_array($result1)) {
            $part_id = $data1['id'];

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

            $sql2 = "SELECT a.color,a.colortype,a.pantonecode,a.actual_shrinkage,b.colourname, c.partname FROM  bom_mould_wise_part_detail a LEFT JOIN part_colour b ON a.color=b.id LEFT JOIN part_master c ON c.id=a.part_material WHERE a.mouldid = $mould_id";
            $result2 = mysqli_query($con, $sql2);
            $data2 = mysqli_fetch_array($result2);
            $color = '';
            if ($data2['colortype'] == 1) {
                $color = $data2['colourname'];
            } else {
                $color = "Special Color-" . $data2['pantonecode'];
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

            $sql3 = "SELECT b.name as core_name, a.runner_type,e.name as gatingname,a.gating_type,a.tips,c.name as cavity_name, a.mould_base_steel,a.core_steel,a.cavity_steel, a.insertmoulding, a.cadweight, d.name as runner_name FROM bom_part_additional_details a LEFT JOIN core_steel b ON a.core_steel=b.id LEFT JOIN cavity_steel c ON c.id=a.cavity_steel LEFT JOIN runner_type d ON d.id=a.runner_type  LEFT JOIN gating_type e ON a.gating_type=e.id WHERE a.partdetailid = $part_id";
            //echo $sql3;exit;
            $results3 = mysqli_query($con, $sql3);
            $datas3 = mysqli_fetch_array($results3);


            if ($datas3['runner_type'] == 1) {
                $runname = "Hot Runner";
                $gating = "<strong>Gating</strong><br/>" . $datas3['gatingname'];
                $tips = "<strong>Tips</strong><br/>" . $datas3['tips'];
            } else if ($datas3['runner_type'] == 2) {
                $runname = "Cold Runner";
                $gating = "<strong>Gating</strong><br/>" . $datas3['gatingname'];
                $tips = '';
            } else {
                $runname = "";
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
                    }
                }
                //$datasother = mysqli_fetch_array($resultsother);

            }

                        if ($option_id > 0) {

                $sql12 = "SELECT mould_size,x, y, z, mould_weight,price_in_dollar FROM sourcing_quotation WHERE option_id=$option_id AND mould_id = $mould_id AND lead_id=$lead_id AND supplier_id=$supplier";
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

                    $sql12zero = "SELECT mould_size,x, y, z, mould_weight,price_in_dollar FROM sourcing_quotation WHERE option_id=0 AND mould_id = $mould_id AND lead_id=$lead_id AND supplier_id=$supplier";
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

                $sql12zero = "SELECT mould_size,x, y, z, mould_weight,price_in_dollar FROM sourcing_quotation WHERE option_id=0 AND mould_id = $mould_id AND lead_id=$lead_id AND supplier_id=$supplier";
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

            $sql9 = "SELECT a.remarks, b.material_grade as tool_cavity, c.material_grade as mould_base, d.material_grade as core_cavity FROM bom_moulds_detail a LEFT JOIN steel_type b ON a.mouldcavitysteel=b.id LEFT JOIN steel_type c ON a.mouldbasesteel=c.id LEFT JOIN steel_type d ON a.mouldcoresteel=d.id WHERE a.id = $mould_id AND lead_id = $lead_id";
            $result9 = mysqli_query($con, $sql9);
            $data9 = mysqli_fetch_array($result9);

$html .= '<tr style="text-align:center;">';

    if (!in_array($mould_id, $mouldcheck)) {
        $html .= '<td style="border:1px solid black;" rowspan="'.$partscount.'"><strong>MOULD '.$mld .'<br>'.$mould_type.' Mould</strong></td>';
    }

$html .= '<td style="border:1px solid black; ">'.$data1['name'].'</td>
<td style="border:1px solid black; "><strong>Part Colour</strong><br/>' . $color . '<br/><br/><strong>Surface Finish</strong><br/>' . $fin . '<br/><br/><strong>Material</strong><br/>' . $material . '</td>
<td style="border:1px solid black; "><img src="'.image_url.$data1['image'].'" width="100px"></td>
<td style="border:1px solid black; ">'.$part_length.'x'.$part_width.'x'.$part_height.'</td>
<td style="border:1px solid black; ">'.$data1['cavity'].'</td>';

    if (!in_array($mould_id, $mouldcheck)) {
    $html .= '<td style="border:1px solid black;" rowspan="' . $partscount . '"><strong>Runner</strong><br/>' . $runname . '<br/><br/>' . $gating . '<br/><br/>' . $tips . '</td>
    <td style="border-left:1px solid black; border-right:1px solid black; border-top:1px solid black; ">'.$mouldsize.'</td>
    <td style="border-left:1px solid black; border-right:1px solid black; border-top:1px solid black; " rowspan="'.$partscount.'"><strong>Tool Steel Core</strong><br>'.$data9['tool_cavity'].'<br><strong>Tool Steel Cavity</strong><br>'.$data9['core_cavity'].'<br><strong>Tool Steel Mould Base</strong><br>'.$data9['mould_base'].'</td>
    <td style="border-left:1px solid black; border-right:1px solid black; border-top:1px solid black; " rowspan="'.$partscount.'">'.$mouldweight.'</td>
    <td style="border-left:1px solid black; border-right:1px solid black; border-top:1px solid black; ">'.round($priceinusd).'</td>';
                $mould_weight[] = $mouldweight;
                $priceindollar[] = round($priceinusd);
    }

$html .= '</tr>';
        
        if ($partscount > 1) {
                $mouldcheck[] = $mould_id;
            }
        }
    $mld++;
    }
}

$total_amt = array_sum($priceindollar) + array_sum($total_ser_amt);

$html .= '<tr>
<td style="border:1px solid black; background-color:lightgrey; text-align:right; font-weight:bold;" colspan="9" >TOTAL</td>
<td style="border:1px solid black; background-color:lightgrey; text-align:center; font-weight:bold;">'.array_sum($mould_weight).' Kgs</td>
<td style="border:1px solid black; background-color:lightgrey; text-align:center; font-weight:bold;">$'.array_sum($priceindollar).'</td>
</tr>

<tr>

<td style="border:1px solid black; background-color:lightgrey; text-align:right;  font-weight:bold;" colspan="10">OTHER SERVICES</td>
<td style="border:1px solid black;  text-align:center;" >$'.array_sum($total_ser_amt).'</td>

</tr>';


if ($delivery_type == 2) {

    $mould_weight_data = $suppliergivenweight;
$pertonvalue = 150;
$seafreight = ($mould_weight_data / 1000) * $pertonvalue;
$customvalue = (array_sum($priceindollar) + $seafreight) * 0.28;
$clearance = 400;

if ($delivery_type == 2) {
$grandtotal = ceil($seafreight + $customvalue + $clearance);
} else {
$grandtotal = 0;
}

$html .= '<tr>

<td style="border:1px solid black; background-color:lightgrey; text-align:right;  font-weight:bold;" colspan="10">SHIPMENT </td>
<td style="border:1px solid black;  text-align:center;">$' . round($grandtotal) . '</td>

</tr>';
}

$total_amt=array_sum($priceindollar)+$grandtotal+array_sum($total_ser_amt);

$html.='<tr style="background-color:#eeeeee; font-weight:bold;" nobr="true">
<td colspan="10" style="text-align:right">GRAND TOTAL IN USD</td>
<td  style="text-align:right"> $'.$total_amt . ' </td>
</tr>';


$html.='<tr style="background-color:#eeeeee; font-weight:bold;" nobr="true">
<td colspan="11" style="text-align:right">AMOUNT IN WORDS: USD '.ucwords(getCurrencyCode($total_amt)) . ' Only</td>

</tr>';

if($delivery_type==2)
{
    $inrtotal=$total_amt*$currency_rate;
    $html.='<tr style="background-color:lightgrey; font-weight:bold;" nobr="true">
    <td colspan="10" style="text-align:right;">Grand Total In INR</td>
    <td>₹ '.$inrtotal.'</td>
    </tr>
    <tr style="background-color:#eeeeee; font-weight:bold;" nobr="true">
    <td colspan="11" style="text-align:right"   >AMOUNT IN WORDS: Rs. ' . ucwords(getCurrencyCode($inrtotal)) . ' Only</td>
    </tr>';
}


$html.='<tr style="background-color:#eeeeee; font-weight:bold;" nobr="true">
    <td colspan="11" style="text-align:right">GST as per actual at the time of invoice</td>
    </tr>';


// $html .= '<tr>
// <td style="border-left:1px solid black; border-top:1px solid black; background-color:lightgrey; text-align:right; border-right:1px solid black; font-weight:bold;" colspan="3">AMOUNT IN WORDS</td>
// <td style="border-left:1px solid black; border-top:1px solid black;" colspan="6">USD '.ucwords(getCurrencyCode($total_amt)).' Only </td>
// <td style="border:1px solid black; background-color:lightgrey; text-align:right; font-weight:bold;" >GRAND TOTAL </td>
// <td style="border:1px solid black;  text-align:center; ">$'.$total_amt.'</td>
// </tr>

// <tr>
// <td style="border-left:1px solid black; border-bottom:1px solid black; background-color:lightgrey; text-align:right; border-right:1px solid black;" colspan="3"></td>
// <td style="border-left:1px solid black; border-bottom:1px solid black;" colspan="6"></td>
// <td style="border:1px solid black; background-color:lightgrey; text-align:left; font-size:7px; font-weight:bold;" >GST as per actual at the time of invoice </td>
// <td style="border:1px solid black;  text-align:center; "></td>
// </tr>';


$html.='</table>

<br>
<br>';


if($delivery_type==2)
{
$html.='<table style="padding:3px; font-size:11px;">
<tr nobr="true"><td style="">Please consider the currency exchange rate of the day at the time of generating purchase order and Bank Transfer of each stage as below. The Current USD Rate is '.number_format($currency_rate,2).' The Total Value is '.$inrtotal.' in INR. So, the total price will be considered in '.$total_amt.' USD. All the upcoming payments will be made basis on the currency exchange price of US Dollar on the day of Payment Made.</td></tr>
<tr nobr="true"><td style="color:grey; "></td></tr>
</table>';
}



$html.='<table style="width:100%; font-size:9px; padding:3px;" >
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

$sql14 = "SELECT final_price FROM client_quoted_price WHERE lead_id = $lead_id AND option_id=$option_id AND supplier_id=$supplier";
$results14 = mysqli_query($con, $sql14);
$datas14 = mysqli_fetch_array($results14);

$total_amount = array();

// $final_price = $datas14['final_price'];
$final_price = $total_amt;

$sql13 = "SELECT payment_stage, stage, percentage FROM payment_terms WHERE lead_id = $lead_id";
$results13 = mysqli_query($con, $sql13);
$paymentstep=mysqli_num_rows($results13);
$pst=0;
$paynow='';
while ($datas13 = mysqli_fetch_array($results13)) {
    $percentage = $datas13['percentage'];
    $amount = ($final_price * $percentage) / 100;
        $html .= '<tr>
        <td style="text-align:right:">' . $datas13['payment_stage'] . '</td>
        <td style="text-align:left:">'.$datas13['stage'].'</td>
        <td style="text-align:center:">' . $percentage . '%</td>
        <td style="text-align:center:">$' . $amount . '</td>
        </tr>';
        $total_amount[] = $amount;
        if($pst==0)
        {
            $paynow=$amount;    
        }
            $pst++;
    }

$html .= '<tr>
<td style="text-align:right; font-weight:bold; background-color:lightgrey;">TOTAL</td>
<td style="text-align:left; background-color:lightgrey;"></td>
<td style="text-align:left; background-color:lightgrey;"></td>
<td style="text-align:center; background-color:lightgrey;" colspan="2">$ '.array_sum($total_amount).'</td> 


</tr>
</table >
</td>
<td style="width:180px;" rowspan="'.$paymentstep.'">
<table border="1" style="text-align:center;  padding:3px;">
<tr>
<td style="background-color:lightgrey;">AMOUNT NEED TO PAY NOW</td>

</tr>
<tr>
<td style="font-weight:bold; font-size:20px; height:85px;">$'.$paynow.'
</td>
</tr>
</table>
</td>
</tr>
</table>
<br><br>


<table style="width:100%; padding:3px; font-size:8px;">
<tr>
<td style="font-weight:bold;">TERMS OF TRANSECTIONS</td>
</tr>

<tr>
1.Order execution terms will be based on the quotation agreement (as per the Quotation No. mentioned. <br>
2.All payments will be consider in terms of US$ amount and subjected to exchange rate vaiations. the difference will be charged from the client.<br> 
3.The bank transfer fee will be paid by the customer, in case of any difference appeared, will be charged in the last payment before delivery.
</tr>
</table>


';  


//echo $html; exit;
//echo $footer_logo_html; exit;
// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.



$filelocation = $_SERVER['DOCUMENT_ROOT'].'/image_bank/client_performa';
$fileNL = $filelocation."/".$query_no.".pdf"; //Linux

$pdf->Output($fileNL, 'F');

header('location:'.redirecturl.$query_no);


//============================================================+
// END OF FILE
//============================================================+
