<?php
$CI = &get_instance();
$CI->load->model('Salescrm_model');
$id=$this->uri->segment(3);
$start_date=$this->uri->segment(4);
$end_date=$this->uri->segment(5);
$claim_this_month=$CI->Salescrm_model->this_month_claim_list_consolidated_amount($this->uri->segment(3),$this->uri->segment(4),$this->uri->segment(5));
$claim_this_month=explode('|',$claim_this_month);

$location_data=$CI->Salescrm_model->getHpclLocationsName($this->uri->segment(3));
if(count($location_data)>0)
{

$buyer_name=$location_data[1];
$buyer_address=$location_data[2];
$buyer_gst=$location_data[4];
$state_name=$location_data[3];
$state_code=substr($buyer_gst,0,2);

}else
{
$buyer_name='';
$buyer_address='';
$buyer_gst='';
$state_code='';
$state_name='';
}

$seller_data=$CI->Salescrm_model->getRackLocationViaID(3);
if(count($seller_data)>0)
{
$billing_company_name=$seller_data[0];
$billing_company_address=$seller_data[2];
$seller_gst=$seller_data[4];
$seller_state=$seller_data[7];
$seller_state_code=substr($seller_gst,0,2);
}else
{
$billing_company_address='';
$seller_gst='';
$seller_state='';
$seller_state_code='';

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


// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');
// Extend the TCPDF class to create custom Header and Footer

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Saurabh Dubey');
$pdf->SetTitle($location_name.'_'.$billing_month);
$pdf->SetSubject('Tax Invoice');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));

// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin('10');
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
$pdf->SetFont('dejavusans', '', 14, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// set text shadow effect
// $pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Set some content to print
$html = '
<p style="text-align:center; font-size:14px;">Tax-Invoice</p>

<table width="100% " style="padding:0px;" >
<tr>
<td width="50%" style="border:1px solid black;">
<table width="100%" style="padding:3px;" >
<tr>
<td style="font-size:13px; padding:3px; border-bottom:1px solid black; height:140px;"><b>'.$billing_company_name.'</b><br><br>
<span style="font-size:11px; margin:0px;">'.$billing_company_address.'<br>
GSTIN/UIN: '.$seller_gst.'<br>
State Name :  '.$seller_state.', Code : '.$seller_state_code.'<br>
E-Mail : '.$billing_company_email.'
</span>
</td>
</tr>
<tr>
<td style="font-size:13px; padding:3px; height:140px;">
<span style="font-size:11px; margin:0px;">Buyer</span><br>
<b>'.$buyer_name.'</b><br>
<span style="font-size:11px; margin:0px;">'.$buyer_address.'<br/>
GSTIN/UIN	:'.$buyer_gst.'<br>
State Name 	    : '.$state_name.',<br>CODE :- '.$state_code.',

</span>
</td>
</tr>
</table>
</td>
<td width="50%" style="padding:3px;">
<table width="100%" style="padding:3px;" border="1" rule="all">
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Invoice No.</span> <br><b>'.$invoice_no.'</b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Dated</span> <br><b>'.date('d F Y').'</b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Delivery Note</span> <br><b>'.$delivery_note.'</b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Mode/Terms of Payment</span> <br><b>'.$payment_type.'</b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Supplier’s Ref.</span> <br><b>'.$supplier_ref.'</b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Other Reference(s)</span> <br><b>'.$billing_month.'</b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Buyer’s Order No.</span> <br><b>'.$invoice_no.'</b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Dated</span> <br><b>'.date('d F Y').'</b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Dispatch Document No.</span> <br><b></b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Delivery Note Date</span> <br><b></b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Dispatched through</span> <br><b>'.$dispatched_through.'</b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Destination</span> <br><b>'.$destination.'</b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px; height:70px;" colspan="2"><span style="font-size:11px; margin:0px;">Terms of Delivery</span> <br><b>'.$terms_of_del.'</b></td>
</tr>
</table>
</td>
</tr>
</table>
<table style="width:100%; padding:3px; font-size:11px; " >
<tr>
<td width="6%" style="text-align:center; border:1px solid black;">S.No.</td>
<td width="30%" style="text-align:center; border:1px solid black;">Description of Goods</td>
<td width="10%" style="text-align:center; border:1px solid black;">HSN/SAC</td>
<td width="10%" style="text-align:center; border:1px solid black;">Quantity</td>
<td width="10%" style="text-align:center; border:1px solid black;">Rate</td>
<td width="10%" style="text-align:center; border:1px solid black;">per</td>
<td width="24%" style="text-align:center; border:1px solid black;">Amount</td>
</tr>';



$html .= '<tr>
<td style="border-left:1px solid black; text-align:center;"></td>
<td style="border-left:1px solid black;"><i><b>PRICE DIFFERENCE M/O '.$billing_month.' soft copy mail for approval</b></i></td>
<td style="border-left:1px solid black;">9986</td>
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black; border-right:1px solid black; text-align:right;"><b>'.array_sum($grandtotal_claim).'</b></td>
</tr>';


$total_gst = 0;
    if($buyer_gst<>'' && $seller_gst <>'')
    {
        $bscode=substr($buyer_gst,0,2);
        $sscode=substr($seller_gst,0,2);
        if($bscode==$sscode)
        {
            $gst=((array_sum($grandtotal_claim))*9)/100;
            // $sgst=round(($gst/2),2);
            // $igst=0;

            $html .= '<tr>
                        <td style="border-left:1px solid black; text-align:center;"></td>
                        <td style="border-left:1px solid black; text-align:right;"><i><b>OUTPUT CGST @9%</b></i></td> 
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black; border-right:1px solid black; text-align:right;"><b>'.$gst.'</b></td>
                        </tr>
                        <tr>
                        <td style="border-left:1px solid black; text-align:center;"></td>
                        <td style="border-left:1px solid black; text-align:right;"><i><b>OUTPUT SGST @9%</b></i></td> 
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black; border-right:1px solid black; text-align:right;"><b>'.$gst.'</b></td>
                        </tr>';
            $total_gst = $gst+$gst;

        }else
        {
            $gst=((array_sum($grandtotal_claim))*18)/100;
            $igst=$gst;
            $cgst=0;

            $html .= '<tr>
                        <td style="border-left:1px solid black; text-align:center;"></td>
                        <td style="border-left:1px solid black; text-align:right;"><i><b>GST @18%</b></i></td> 
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black; border-right:1px solid black; text-align:right;"><b>'.$gst.'</b></td>
                        </tr>';

            $total_gst = $gst;
        }

    }else
    {
        $igst=0;
        $cgst=0;
    }

$final_amt = array_sum($grandtotal_claim) + $total_gst;

$final_amt_round = round($final_amt);

$final_amt_wo_round = $final_amt;
//echo $final_amt_round.'<br>'.$final_amt_wo_round;exit;
// $amt_diff = $final_amt_round - $final_amt_wo_round;
$amt_diff = bcsub($final_amt_round, $final_amt_wo_round, 2);
// echo $amt_diff;exit;

if($amt_diff != 0) {
	$html .= '<tr>
                <td style="border-left:1px solid black; text-align:center;"></td>
                <td style="border-left:1px solid black; text-align:right;"><b>Round Off</b></td> 
                <td style="border-left:1px solid black;"></td>
                <td style="border-left:1px solid black;"></td>
                <td style="border-left:1px solid black;"></td>
                <td style="border-left:1px solid black;"></td>
                <td style="border-left:1px solid black; border-right:1px solid black; text-align:right;"><b>'.floatval($amt_diff).'</b></td>
                </tr>';
}

$html .= '<tr>
<td style="border:1px solid black; text-align:center;"></td>
<td style="border:1px solid black; text-align:right;">Total</td> 
<td style="border:1px solid black;"></td>
<td style="border:1px solid black;"></td>
<td style="border:1px solid black;"></td>
<td style="border:1px solid black;"></td>
<td style="border:1px solid black; border-right:1px solid black; text-align:right;"><b>Rs. '.round($final_amt).'</b></td>
</tr>';

$html .= '</table>
<table style="width:100%; padding:3px; font-size:12px;" border="1">
<tr>
<td style="height:80px;">
<span>Amount Chargeable (in words)</span><br>
<b>INR '.ucwords(getCurrencyCode(round($final_amt))).'</b>
</td>
</tr>
</table>
<table style="width:100%; padding:3px; font-size:12px;" border="1">
<tr>
<td width="40%" style="text-align:center;">HSN/SAC</td>
<td width="10%" style="text-align:center;">Taxable Value</td>';
$total_gst = 0;
    if($buyer_gst<>'' && $seller_gst <>'')
    {
        $bscode=substr($buyer_gst,0,2);
        $sscode=substr($seller_gst,0,2);
        if($bscode==$sscode)
        {
            $gst=((array_sum($grandtotal_claim))*9)/100;
$html .= '<td width="15%" style="text-align:center;">CGST 9%</td>
<td width="15%" style="text-align:center;">SGST 9%</td>';
 $total_gst = $gst+$gst;
        }else
        {
            $gst=((array_sum($grandtotal_claim))*18)/100;
            $igst=floatval($gst);
            $cgst=0;
         $total_gst = $gst;

         $html .= '<td width="30%" style="text-align:center;">GST 18%</td>';
        }
        
    }else
    {
        $igst=0;
        $cgst=0;
    }
$html .= '<td width="20%" style="text-align:center;">Total Tax Amount</td>
</tr>';

 $sql33 = "SELECT a.deliveredQty, b.instruments_name, b.hsncode, c.shortname FROM transportation_based_aprroval_product_details a JOIN presto_instruments b ON b.id=a.product_id LEFT JOIN units c ON c.id=a.pack_size WHERE approval_id=$id";
 $result33 = mysqli_query($con,$sql33);
 $row_cnt33 = mysqli_num_rows($result33);

if($row_cnt33 > 0) {
    $data33 = mysqli_fetch_array($result33);
    // echo "<pre>";print_r($data33);exit;
    $hsncode = $data33['hsncode'];

$html .= '<tr>
<td>9986</td>
<td>'.array_sum($grandtotal_claim).'</td>';
$total_gst = 0;
    if($buyer_gst<>'' && $seller_gst <>'')
    {
        $bscode=substr($buyer_gst,0,2);
        $sscode=substr($seller_gst,0,2);
        if($bscode==$sscode)
        {
            $gst=((array_sum($grandtotal_claim))*9)/100;
$html .= '<td>'.$gst.'</td>
          <td>'.$gst.'</td>';
 $total_gst = $gst+$gst;
        }else
        {
            $gst=((array_sum($grandtotal_claim))*18)/100;
            $igst=floatval($gst);
            $cgst=0;
         $total_gst = $gst;

         $html .= '<td>'.$total_gst.'</td>';
        }

    }else
    {
        $igst=0;
        $cgst=0;
    }
$html .= '<td>'.floatval($total_gst).'</td>
</tr>';
}
$html .= '<tr>
<td style="text-align:right;">Total</td>
<td></td>';
$total_gst = 0;
    if($buyer_gst<>'' && $seller_gst <>'')
    {
        $bscode=substr($buyer_gst,0,2);
        $sscode=substr($seller_gst,0,2);
        if($bscode==$sscode)
        {
            $gst=((array_sum($grandtotal_claim))*9)/100;
$html .= '<td></td>
          <td></td>';
 $total_gst = $gst+$gst;
        }else
        {
            $gst=((array_sum($grandtotal_claim))*18)/100;
            $igst=floatval($gst);
            $cgst=0;
         $total_gst = $gst;

         $html .= '<td></td>';
        }

    }else
    {
        $igst=0;
        $cgst=0;
    }
$html .= '<td>'.floatval($total_gst).'</td>
</tr>
</table>
<table style="width:100%; padding:3px; font-size:12px;" border="1">
<tr><td><span>Tax Amount (in words)  :</span><b>INR '.ucwords(getCurrencyCode(round($final_amt))).'</b><br><br><br><br>
<table style="width:100%;  font-size:12px;">
<tr>
<td width="70%">
<table style="width:100%;  font-size:12px;">
<tr>
<td>Company’s PAN  </td>
<td>: <b>'.substr($seller_gst,2,11).'</b></td>
</tr>
<tr>
<td></td>
<td></td>
</tr>
<tr>
<td>Bank Details</td>
<td>: <b>'.$bank_details.'</b></td>
</tr>
</table>
</td>
</tr>
<br><br><br><br>
<tr>
<td><span style="font-size:9px;">Declaration<br>
We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.

</span></td>
<td style="border:1px solid black;">

<p style="text-align:right; margin-bottom:50px;"><b>for '.$billing_company_name.'</b></p>
<p style="text-align:right; font-size:11px;">Authorised Signatory</p>
</td>
</tr>
</table>
</td></tr>

</table>
';

// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('example_001.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
