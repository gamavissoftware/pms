<?php

/** QUERY ENDS **/
define('image_url', 'https://hongyijig.in/assets/client_bom_data/part_image/');
include('mysqlconfig.php');

$lead_id = $_GET['lead_id'];
$option_id = $_GET['option'];
$supplier = $_GET['suplier'];
$negotiation = $_GET['negotiation'];

if ($negotiation == '') {
    $negotiation = 0;   
}

$sql12 = "SELECT a.beneficiary_name, a.beneficiary_address, a.beneficiary_account_no, a.bank_name, a.bank_address, a.swift_code, a.bank_code, a.intermediary_bank_name, a.intermediary_bank_address, a.intermediary_swift_code, b.company_spokes_person_name, b.spokes_person_mobile, b.spokes_person_emailid, b.spokes_person_wechat FROM supplier_bank_detail a JOIN supplier_spokes_person_detail b ON b.supplier_id=a.supplier_id WHERE a.supplier_id=$supplier";
$result12 = mysqli_query($con, $sql12);
$data12 = mysqli_fetch_array($result12);

$sql13 = "SELECT id FROM bom_moulds_detail WHERE lead_id=$lead_id";
$result13 = mysqli_query($con, $sql13);
$total_moulds = mysqli_num_rows($result13);

$sql = "SELECT unique_id, company_name, customer_name, postal_address, country_code, contact_no, email_id FROM leads WHERE id=$lead_id";
// echo $sql;exit;
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
 
$html='
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
<td>'.$company_name.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">COMPANY ADDRESS</td>
<td>'.$postal_address.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">CONTACT PERSON</td>
<td>'.$customer_name.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">MOBILE NUMBER</td>
<td>'.$contact_no.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">EMAIL ID</td>
<td>'.$email_id.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">WE CHAT ID</td>
<td>-</td>
</tr>
</table>

</td>

<td>
<table border="1" style="padding:3px;">
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">LOT. NO-1</td>
<td>-</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">TOTAL NO OF MOULDS </td>
<td>'.$total_moulds.'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">PURCHASE ORDER NUMBER</td>
<td>HIGPO</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">OWNERS NAME</td>
<td>'.$data12['company_spokes_person_name'].'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">OWNERS MOBILE NUMBER</td>
<td>'.$data12['spokes_person_mobile'].'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">EMAIL ID</td>
<td>'.$data12['spokes_person_emailid'].'</td>
</tr>
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">WECHAT ID</td>
<td>'.$data12['spokes_person_wechat'].'</td>
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
<td>-</td>
</tr>
</table></td>
<td><table style="width:100%; padding:3px;" border="1">
<tr>
<td style="background-color:lightgrey; text-align:right; font-weight:bold;">DATE</td>
<td>
</td>
</tr>
</table></td>
</tr>
</table>

<p style=" font-size:9px;">Dear <span>'.$data12['company_spokes_person_name'].'</span><br>We are pleased to entrust you with purchase order for the product/services mention below subject to terms and conditions given below
</p>

<table style="width:100%; padding:3px; font-size:9px;">
<tr>
<td style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black;">LIST OF MOULDS </td>
</tr>
</table>
<table style="width:100%; padding:3px; font-size:9px;">';
$html .= '<tr>
<th style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black; width:30px;">S.No</th>
<th style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black; width:85px;">PART NAME</th>
<th style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black;">PART CFM </th>
<th style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black;">PART PICTURE</th>
<th style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black;"> PART WEIGHT</th>
<th style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black;">CAVITY </th>
<th style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black;">TYPE OF RUNNER
</th>
<th style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black;">HOT RUNNER BRAND. </th>
<th style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black; ">CYCLE TIME</th>
<th style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black;">TOOL STEEL </th>
<th style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black;">PRICE IN USD
</th>
</tr>';

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

            $sql2 = "SELECT a.color,a.colortype,a.pantonecode,a.actual_shrinkage, a.cycletime, b.colourname, c.partname FROM  bom_mould_wise_part_detail a LEFT JOIN part_colour b ON a.color=b.id LEFT JOIN part_master c ON c.id=a.part_material WHERE a.mouldid = $mould_id";
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

            $sql3 = "SELECT b.name as core_name, a.runner_type,e.name as gatingname,a.gating_type,a.tips,c.name as cavity_name, a.mould_base_steel,a.core_steel,a.cavity_steel, a.insertmoulding, a.benchmarkweight, a.cadweight, d.name as runner_name FROM bom_part_additional_details a LEFT JOIN core_steel b ON a.core_steel=b.id LEFT JOIN cavity_steel c ON c.id=a.cavity_steel LEFT JOIN runner_type d ON d.id=a.runner_type  LEFT JOIN gating_type e ON a.gating_type=e.id WHERE a.partdetailid = $part_id";
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

            if($datas3['benchmarkweight'] == '') {
                $benchmarkweight = NA;
            } else {
                $benchmarkweight = $datas3['benchmarkweight']." gm";
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
                    $price_in_dollar = $datas122345['price_in_dollar'];
                    // echo $priceinusd.'<br>';
                    // $revamp = $percentfactor / 100;
                    // echo $revamp;exit;
                    // $revamp1 = $priceinusd * $revamp;
                    // $priceinusd = $priceinusd + $revamp1;
                } else {

                    $sql12zero = "SELECT mould_size,x, y, z, mould_weight,price_in_dollar FROM sourcing_quotation WHERE option_id=0 AND mould_id = $mould_id AND lead_id=$lead_id AND supplier_id=$supplier";
                    // echo $sql12zero;exit;
                    $results12zero = mysqli_query($con, $sql12zero);
                    $datas122345z = mysqli_fetch_array($results12zero);
                    $mouldsize = "X - " . $datas122345z['x'] . " mm<br>" . "Y - " . $datas122345z['y'] . " mm<br>" . "Z - " . $datas122345z['z'] . " mm";
                    $mouldweight = $datas122345z['mould_weight'];
                    $price_in_dollar = $datas122345z['price_in_dollar'];

                    // $revamp = $percentfactor / 100;
                    // $revamp1 = $priceinusd * $revamp;
                    // $priceinusd = $priceinusd + $revamp1;
                }
            } else {

                $sql12zero = "SELECT mould_size,x, y, z, mould_weight,price_in_dollar FROM sourcing_quotation WHERE option_id=0 AND mould_id = $mould_id AND lead_id=$lead_id AND supplier_id=$supplier";
                // echo $sql12zero;exit;
                $results12zero = mysqli_query($con, $sql12zero);
                $datas122345z = mysqli_fetch_array($results12zero);
                $mouldsize = "X - " . $datas122345z['x'] . " mm<br>" . "Y - " . $datas122345z['y'] . " mm<br>" . "Z - " . $datas122345z['z'] . " mm";
                $mouldweight = $datas122345z['mould_weight'];
                $price_in_dollar = $datas122345z['price_in_dollar'];

                // $revamp = $percentfactor / 100;
                // $revamp1 = $priceinusd * $revamp;

                // $priceinusd = $priceinusd + $revamp1;
            }

            $sql9 = "SELECT a.remarks, b.material_grade as tool_cavity, c.material_grade as mould_base, d.material_grade as core_cavity FROM bom_moulds_detail a LEFT JOIN steel_type b ON a.mouldcavitysteel=b.id LEFT JOIN steel_type c ON a.mouldbasesteel=c.id LEFT JOIN steel_type d ON a.mouldcoresteel=d.id WHERE a.id = $mould_id AND lead_id = $lead_id";
            $result9 = mysqli_query($con, $sql9);
            $data9 = mysqli_fetch_array($result9);


$html .= '<tr>';
	if (!in_array($mould_id, $mouldcheck)) {
	$html .= '<td style="text-align:center;  border:1px solid black;" rowspan="'.$partscount.'"><strong>MOULD '.$mld .'<br>'.$mould_type.'</strong></td>';
	}
$html .= '<td style="text-align:center;  border:1px solid black;">'.$data1['name'].'</td>
<td style="text-align:center;  border:1px solid black;"><strong>Part Colour</strong><br/>' . $color . '<br/><br/><strong>Surface Finish</strong><br/>' . $fin . '<br/><br/><strong>Material</strong><br/>' . $material . '</td>
<td style="text-align:center;  border:1px solid black;"><img src="'.image_url.$data1['image'].'" width="100px"></td>
<td style="text-align:center;  border:1px solid black;">'.$benchmarkweight.'</td>
<td style="text-align:center;  border:1px solid black;">'.$data1['cavity'].'</td>';
 if (!in_array($mould_id, $mouldcheck)) {
$html .= '<td style="text-align:center;  border:1px solid black;" rowspan="' . $partscount . '"><strong>Runner</strong><br/>' . $runname . '<br/><br/>' . $gating . '<br/><br/>' . $tips . '</td>';
}
$html .= '<td style="text-align:center;  border:1px solid black;"></td>
<td style="text-align:center;  border:1px solid black;">'.$data2['cycletime'].'</td>
<td style="text-align:center;  border:1px solid black;"><strong>Tool Steel Core</strong><br>'.$data9['tool_cavity'].'<br><strong>Tool Steel Cavity</strong><br>'.$data9['core_cavity'].'<br><strong>Tool Steel Mould Base</strong><br>'.$data9['mould_base'].'</td>
<td style="text-align:center;  border:1px solid black;">'.round($price_in_dollar).'</td>
</tr>';

    $priceindollar[] = $price_in_dollar;
 if ($partscount > 1) {
                $mouldcheck[] = $mould_id;
            }
        }
    $mld++;
    }
}

$html .= '<tr>
<td style="text-align:center;  border:1px solid black; font-weight:bold; background-color:lightgrey;" colspan="10">TOTAL </td>
<td style="text-align:center;  border:1px solid black; background-color:lightgrey;">$'.array_sum($priceindollar).'</td>

</tr>
<tr>
<td style="text-align:center;  border:1px solid black; font-weight:bold; background-color:lightgrey;" colspan="2" >AMOUNT IN WORDS </td>
<td style=" border:1px solid black; " colspan="9">USD '.ucwords(getCurrencyCode(array_sum($priceindollar))).' Only
</td>

</tr>
</table>

<br><br>
<table style="width:100%; padding:3px; font-size:9px;">
<tr>
<td style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black;">PAYMENT TERMS </td>
<td style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black;">SUPPLIERS BANK DETAILS</td>
</tr>
</table>

<br><br>
<table style="width:100%;  font-size:9px; ">
<tr style=" border-collapse: collapse;">
<td style="  border-collapse: collapse; ">
<table style="  border-collapse: collapse; padding:3px;">
<tr style="  border-collapse: collapse;">
<th style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black; width:40px;">S. NO.</th>
<th style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black; width:73px;">PAYMENT DEVISION </th>
<th style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black; width:100px;">STAGE </th>
<th style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black; width:40px;">% AGE</th>
<th style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black;">AMOUNT</th>
</tr>';

$sql14 = "SELECT final_price FROM client_quoted_price WHERE lead_id = $lead_id AND option_id=$option_id AND supplier_id=$supplier";
$results14 = mysqli_query($con, $sql14);
$datas14 = mysqli_fetch_array($results14);

$total_amount = array();

// $final_price = $datas14['final_price'];
$final_price = array_sum($priceindollar);

$sql13 = "SELECT payment_stage, stage, percentage FROM payment_terms WHERE lead_id = $lead_id";
$results13 = mysqli_query($con, $sql13);

while ($datas13 = mysqli_fetch_array($results13)) {
	$percentage = $datas13['percentage'];
	$amount = ($final_price * $percentage) / 100;

$html .= '<tr>
<td style="border:1px solid black; text-align:center;">1.</td>
<td style="border:1px solid black; text-align:left;">' . $datas13['payment_stage'] . '</td>
<td style="border:1px solid black; text-align:left;">' . $datas13['stage'] . '</td>
<td style="border:1px solid black; text-align:center;">' . $percentage . '%</td>
<td style="border:1px solid black; text-align:center;">' . $amount . '</td>
</tr>';
	$total_amount[] = $amount;

}

$html .= '</table>

Payment terms will be 30% advance with PO, Mid payment @ 30% after T-0 samples approvals (subjected to 90% okay in dimensions/ surface finish & Features only) & 40% after final samples approval with 100% okay dimensiona as per drawings and proof of Moulds packed for shipment.<br>
Mid payment is subjected to 90% okay in dimensions/ surface finish & Features only. The last balance payment will be made after proper evidences of mould quality check requested by HJIG Technical team & receiving Commercial 
Invoice and Packaging List from Supplier.


</td>
<td >
<table style="width:100%; padding:3px; font-size:9px;" border="1">
<tr>
<td style="text-align:right; font-weight:bold; background-color:lightgrey; ">Beneficiary Name</td>
<td>'.$data12['beneficiary_name'].'
</td>
</tr>

<tr>
<td style="text-align:right; font-weight:bold; background-color:lightgrey; ">Beneficiary Address</td>
<td>"'.$data12['beneficiary_address'].'" 
</td>
</tr>

<tr>
<td style="text-align:right; font-weight:bold; background-color:lightgrey; ">Beneficiary Account no.</td>
<td>'.$data12['beneficiary_account_no'].'
</td>
</tr>

<tr>
<td style="text-align:right; font-weight:bold; background-color:lightgrey; ">Beneficiary Bank Name</td>
<td>"'.$data12['bank_name'].'" 
</td>
</tr>

<tr>
<td style="text-align:right; font-weight:bold; background-color:lightgrey; ">Beneficiary Bank Address</td>
<td>"'.$data12['bank_address'].'"
</td>
</tr>

<tr>
<td style="text-align:right; font-weight:bold; background-color:lightgrey; "> Beneficiary Swift Code</td>
<td>'.$data12['swift_code'].'
</td>
</tr>

<tr>
<td style="text-align:right; font-weight:bold; background-color:lightgrey; ">Beneficiary Bank Code</td>
<td>'.$data12['bank_code'].'

</td>
</tr>

<tr>
<td style="text-align:right; font-weight:bold; background-color:lightgrey; ">Intermediatery Bank Name</td>
<td>"'.$data12['intermediary_bank_name'].'"
</td>
</tr>

<tr>
<td style="text-align:right; font-weight:bold; background-color:lightgrey; ">Intermediatery Bank Address</td>
<td>"'.$data12['intermediary_bank_address'].'"

</td>
</tr>

<tr>
<td style="text-align:right; font-weight:bold; background-color:lightgrey; ">Intermediatery Bank Swift Code</td>
<td>'.$data12['intermediary_swift_code'].'
</td>
</tr>
</table>
</td>
</tr>
</table>

<br><br>
<table style="width:100%; padding:3px; font-size:9px;">
<tr>
<td style="text-align:center; font-weight:bold; background-color:lightgrey; ">LEAD TIME MILSTONES </td>

</tr>
</table>



<br><br>


<table  style="width:100%;  font-size:9px;">


<tr style="border-collapse:collapse;">

	<td >
		<table rules="all" cellpadding="3">

			<tbody>
			<tr>
			<td style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black; width:40px;">S. NO. </td>
			<td style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black; width:20px;"></td>
			<td style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black; width:192px;">DELIVERY MILE STONES</td>
			<td style="text-align:center; font-weight:bold; background-color:lightgrey; border:1px solid black; width:60px;">DATE </td>
			</tr>

			<tr>
			<td style="text-align:center;   border:1px solid black; ">1.</td>
			<td style="text-align:center;  border:1px solid black; "></td>
			<td style="text-align:left;  border:1px solid black; ">PART DESIGN FEASIABILITY FEEDBACK</td>
			<td style="text-align:left;   border:1px solid black; ">2021-04-17</td>
			</tr>
			<tr>
			<td style="text-align:center;   border:1px solid black; ">2.</td>
			<td style="text-align:center;  border:1px solid black; "></td>
			<td style="text-align:left;  border:1px solid black; ">DFM / MOULD FLOW & TOOL DESIGNS </td>
			<td style="text-align:left;   border:1px solid black; ">2021-04-17</td>
			</tr>
			<tr>
			<td style="text-align:center;   border:1px solid black; ">3.</td>
			<td style="text-align:center;  border:1px solid black; "></td>
			<td style="text-align:left;  border:1px solid black; ">TOOLING TILL T-0 </td>
			<td style="text-align:left;   border:1px solid black; ">2021-04-17</td>
			</tr>
			<tr>
			<td style="text-align:center;   border:1px solid black; ">4.</td>
			<td style="text-align:center;  border:1px solid black; "></td>
			<td style="text-align:left;  border:1px solid black; ">FINAL MOULD DELIVERY WITH BATCH PRODUCTION</td>
			<td style="text-align:left;   border:1px solid black; ">2021-04-17</td>
			</tr>
		
			</tbody>
		</table>


	</td>

	<td  style="padding:3px; border:1px solid black; font-size:8px;">
	The Lead time will be considered & bounded from the date of Purchase Order (subjected to advance payment with in 10 days from the date of PO) to the delivery of moulds to Port (Subjected to evidence). Otherwise supplier will be liable to pay the penality fee to client. All milestone such as Tool Design, Tooling, trials and testing, batch production for tools quality and mould packaging etc. comes with in the lead time.<br>
	Any delay in the lead time of the moulds delivery more than 10%, will be subjected to penalty of 1% of the project cost (Total Moulds cost) per day.
	</td>
</tr>
</table>

<br><br>
<table style="width:100%; padding:3px; font-size:9px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">BASIC TERMS</td>

</tr>
</table>

<ol style="font-size:9px;">
<li>Company certification (OPL) Fee to deal with Indian Bank of $150 will be innitialy charge before the placement of any order. </li>
<li> Subletting of our orders may be permitted only on our prior-permission in writing. </li>
<li>All Mould Material Certificates to be given on purchase of mould material along with delivery of moulds. </li>
<li>In case of Hot Runner moulds, the Hot runner gurantee certificate to be given along with moulds. </li>
<li>The Material Test Certificates and Hot Runner gurantee cards will be sent along with the Mould supplies would be your liability.
</li>
<li>In case of assembly parts, tool maker will check the drawings and designs feasiability of the meting parts and in case of any doubt on meting parts assembly design failure, tool maker will raise 
the doubt before taking the order. After receiving the order it will be tool makers responsibility to provide the desired results of quality assembly of product. </li>
<li>Supplier will follow the mould standard given by Hongyi JIG for each order. </li>
<li>Tool supplier will provide the temperature Controller with each hot runner system. </li>
<li>All parts weight must be as per the CAD or bench marked part weights as per BOM. Only 2% daviation is acceptable. </li>
</ol>

<br><br>
<table style="width:100%; padding:3px; font-size:9px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">ORDER & ORDER CONFIRMATION 
</td>

</tr>
</table>

<ol style="font-size:9px;">
<li>All Orders are subjected to the date of release of PO signed copy from HJIG incase if advance payment release date lies with in 10 days of PO. The Lead time will be considered from the date of PO released. </li>
<li>No Terms of conditions will be acceptable seperately if not mentions in the PO released by HJIG and signed by the tool maker. </li>
<li> All POs will be considered "ACCEPTED" untill unless a written objection has been raised with in 24 hours over the same email id reply from which PO is received. </li>

</ol>


<br><br>
<table style="width:100%; padding:3px; font-size:9px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">TRIALS & TESTING
</td>

</tr>
</table>

<ol style="font-size:9px;">
<li>There will be 50 shots batch production of the molds which will be done exclusively to check the tool/mold quality. The Raw material will be bourne by the Tool maker with in the project cost. This will be on the basis of EXW delivery. </li>
<li> Supplier will ensure to achieve 90% accuracy on dimensions, surface finish and features of the part in the first trial of mould. </li>
<li>Tool maker company will check the trial samples of T-0 by himself and do the corrections without waiting for the feedback from client, to achive 100% ok dimesions based on the 3D CAD and 2D drawing Dimensions (Follow by the tolerances if given). </li>
<li>The Tool maker will send the inspection reports of the trialed samples along with the "moulding machine parameters screen shots in English Translated" to HJIG immidiately after 24 hours of mould trial in the following manner 
<ol style="font-size:9px;">
<li>Dimenstional inspection report of each part based on given dimensions and tolerances</li>
<li>Dimenstional inspection report of each part based on basic over all assembly dimensions in case if 2D drawing is missing </li>
<li>Visual inspection report of all the features of the components showing moulding defects / feature complition and surface finish. </li>


</ol>
</li>
<li>Minimum 5 sets of trial shots will be expressed by the tool maker by DHL fast express on each trial. </li>
<li>All mould trials are mendatory to made on the same size moulding machines as recomended by HJIG technical team / mutually decided at the time of agreement. </li>
<li>All the mould trials will be captured through a clear video with minimum 3 shots by keeping the mobile camera in a horizontal way. </li>
<li>All trial samples which has a size of more than 500 mm, must be packed in a wooden box to avoid any damages. </li>
<li>The supplier will do the Dry Run test at the time of final testing with the duration of 30 min for all moulds and share us their video in horizontal view. </li>
<li>In case of assembly parts, tool maker will check the drawings and designs feasibility of the meting parts and in case of any doubt on meting parts assembly design failure, tool maker will raise 
the doubt before taking the order. After receiving the order it will be tool makers responsibility to provide the desire results of quality assembly of product. </li>
<li>Tool maker will take the responsibility of checking the assembly flows and required ECNs to serve the quality product and make the correction at his own cost to deliver a good quality product without waiting for the customer feedback. </li>
<li>Tool maker will take a written approval for any kind of ECN modification on part or tool modification through a detailed PPT from HJIG</li>
<li>Tool maker will start the modification of each tool on FIFO basis (First In First Out) to save the tool correction time for the final mould trial. In special conditions, like in case results awaited for 
the meting part mould, the tool maker will take special permission from HJIG to hold the correction activities for the perticular part / mould. </li>
<li>In case there is assembly of the product and the moulds for meting parts are being made from another supplier in china, then it is resposibility of the supplier who is dealing with major parts of assembly that after tool trails, check for the assembly in proper with the meting parts and do the corrections as per feedback observed. </li>
<li>The tool maker will share the moulding parameters of the mould trial run through an Excel sheet specifically. 
</li>

</ol>

<br><br>
<table style="width:100%; padding:3px; font-size:9px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">COMMUNICATION
</td>

</tr>
</table>

<ol style="font-size:9px;">
<li>Tool maker will share the first master plan of manufacturing after receiving PO & before first advance payment & will share the micro plan after tooling kick off every week through an Excel Gantt Chart. 
</li>
<li>In case of any doubt or critical situations, tool maker or HJIG team will resolve amicably over a video conferencing call on Zoom, Skype, Webex. So supplier must be ready with these application on his laptop or desk top with speakers and microphone to take calls. </li>
<li>All communication must be in English Language. The supplier must have a good translator for project coordination in case if the concerned person can not speak English. </li>
<li> For each project there will be a whatsapp group for micro discussion. It is mandatory that the company Boss must be present and active in that group
.</li>
</ol>

<br><br>
<table style="width:100%; padding:3px; font-size:9px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">DELIVERY
</td>

</tr>
</table>

<ol style="font-size:9px;">
<li>All deliveries will be FOB including Batch productions. It will not matter at all whether Consignee book by Air/ Sea or LCL/FCL. Supplier has to pay the all FOB charges by himself to the 
"specified shipping agent by the client". </li>
<li>Before delivery Tool maker will supply the amended final mould design based on the corrections over the time period of multiple trials. </li>
<li>Before delivery, Tool maker will fill and supply the Final Moulding Parameters of each mould as per the Hongyi JIG specs sheet.</li>
<li>Tool maker will help Hongyi JIG to inspect the mould quality defined by HJIG Technical Team before delivery and share the pictures/videos evidence to prove. 
</li>
<li>All trialed samples which has a size of more than 500 mm, must be packed in a wooden box to avoid any damages and send to HJIG Office in India. And the freight cost will be bourne by Tool 
</li>
<li>All moulds must be packaged in a wooden case. For the weight above 1 Ton all moulds must have a Steel angle frame. </li>
<li>All Mould Material Certificates to be given on purchase of mould material along with delivery of moulds. </li>
<li> In case of Hot Runner moulds, the Hot runner certificate to be given along with moulds. </li>
<li>The Material Test Certificates and Hot Runner gurantee cards will be sent along with the Mould supplies would be your liability though not mentioned in our specifically.
</li>
<li>The supplier will do the Dry Run test at the time of final testing with the duration of 30 min for all moulds and share us their video by shooting the video by keeping mobile camera in horizontal position. </li>
</ol>


<br><br>
<table style="width:100%; padding:3px; font-size:9px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">SHIPMENT & TRANSFERS

</td>

</tr>
</table>

<ol style="font-size:9px;">
<li>All shipments related to tool & accessories, will be FOB including Batch productions. It will not matter at all whether Consignee book by Air/ Sea or LCL/FCL. Supplier has to pay the all FOB charges by himself to the "specified shipping agent by the client".  
</li>
<li>All shipments related to tool & accessories, will be FOB including Batch productions. It will not matter at all whether Consignee book by Air/ Sea or LCL/FCL. Supplier has to pay the all FOB charges by himself to the "specified shipping agent by the client". </li>
<li>Supplier will be responsible for "damage proof packaging" of goods to prevent any sort of breakage during shipment. (Whether by Air/Sea). In case of any damages, supplier will immediately manage to send an alternative material express by fool proof packaging. </li>
<li> Supplier will provide all the documents related to shipment such as

<ol>
<li> Bill of Lading as per given shipper and Consignee details </li>
<li>Commercial invoice and Packing List </li>
<li>Certificate of Origin </li>
</ol>
.</li>
</ol>

<br><br>
<table style="width:100%; padding:3px; font-size:9px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">LEAD TIME


</td>

</tr>
</table>

<ol style="font-size:9px;">
<li>The Lead time will be considered & bounded from the date of Purchase Order (subjected to advance payment with in 10 days from the date of PO) to the delivery of moulds to Port (Subjected to evidence). Otherwise supplier will be liable to pay the penality fee to client. All milestone such as Tool Design, Tooling, trials and testing, batch production for tools quality and mould packaging etc. comes with in the lead time.  
</li>
<li> Any delay in the lead time of the moulds delivery more than 10%, will be subjected to penalty of 1% of the project cost (Total Moulds cost) per day. </li>
<li>Any project that deviates more than - 10 % from expected progress will be considered in arrears, and will require a written explanation and a plan to show capability of returning to schedule or 
justification for slippage.</li>
<li>A pilot batch production of 50 shots to check the tool quality is included in the project lead time for tools delivery. </li>
</ol>

<br><br>
<table style="width:100%; padding:3px; font-size:9px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">PAYMENTS & INVOICES



</td>

</tr>
</table>

<ol style="font-size:9px;">
<li>Payment terms will be 30% advance with PO, Mid payment @ 30% after T-0 samples approvals (subjected to 90% okay in dimensions/ surface finish & Features only) & 40% after final samples approval with 100% okay dimensiona as per drawings and proof of Moulds packed for shipme
</li>
<li>Mid payment is subjected to 90% okay in dimensions/ surface finish & Features only. </li>
<li> The last balance payment will be made after receiving Commercial Invoice and Packaging List from Supplier. </li>
<li>All payments will be made to the company account registered with HJIG
. </li>
</ol>

<br><br>
<table style="width:100%; padding:3px; font-size:9px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">WARRANTY & SERVICE 
</td>

</tr>
</table>

<ol style="font-size:9px;">
<li>In case after receiving the mould in India and during mould installation HJIG faces any issues or problems, the tool maker will support HJIG for trouble shooting the problems with in 8 working hours through video call etc. 
</li>
<li>Incase of any non performance of any mould, HJIG will get the tool repair from local resources and the proffessional fee will be bourne by Tool maker and refunded back with in 3 working days.  </li>
<li>Incase of any non performance of any mould, HJIG will get the tool repair from local resources and the proffessional fee will be bourne by Tool maker and refunded back with in 3 working days. . </li>

</ol>

<br><br>
<table style="width:100%; padding:3px; font-size:9px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">SPARES
</td>

</tr>
</table>

<ol style="font-size:9px;">
<li>Spare Mould accessaries and Standard parts will be supplied by the tool maker for each mould as below  
<ul>
<li>Spare Sprue Bush - Qty - 1 </li>
<li>Spare Ejector Pins - Qty 1 set as per the mould design</li>
<li>Mould lifting hooks - Qty - 2 
</li>
</ul>
</li>


</ol>


<br><br>
<table style="width:100%; padding:3px; font-size:9px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">DISPUTES 
</td>

</tr>
</table>

<ol style="font-size:9px;">
<li>In case of lockdowns, strikes or force majeure, we reserve the right to alter, modify postpone or cancel the order without any liability whatsoever. 
</li>
<li> Incase if any project got cancelled by the principle client before tooling kick off, (HJIG will be responsible to produce the evidences) the supplier will only charge 1% of the tool cost only for the moulds for which he has submitted the tool designs, and refund the entire amount of advance money to HJIG 
.  </li>
</ol>

<br><br>
<table style="width:100%; padding:3px; font-size:9px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">MOULD STANDARD
</td>

</tr>
</table>

<ol style="font-size:9px;">
<li>Supplier will follow Honyi JIG Mould standard as attached.  </li>
</ol>

<hr>



<table style="font-size:9px; padding:3px; width:100%;">
<tr>
<td style="text-align:center;">Project Manager</td>
<td style="text-align:center;">Director</td>
<td style="text-align:center;">Directors</td>
</tr>
</table>


';	


//echo $footer_logo_html; exit;
// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.



$filelocation = $_SERVER['DOCUMENT_ROOT'].'/image_bank/popdf';
$fileNL = $filelocation."/akash.pdf"; //Linux

$pdf->Output($fileNL, 'I');



//============================================================+
// END OF FILE
//============================================================+
