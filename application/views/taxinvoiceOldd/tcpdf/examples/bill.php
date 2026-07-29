<?php
include('mysqliconfig.php');
$id=$_GET['order_id'];
$flag=$_GET['flag'];
if($flag==1)
{
$d="ORIGINAL FOR RECIPIENT";
}else if($flag==2)
{
$d="DUPLICATE FOR TRANSPORTER";
}else if($flag==3)
{
$d="TRIPLICATE FOR SUPPLIER";
}else
{
    $d='';
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



if($id<>'')
{

    $sel="SELECT e.pan_no as customer_pan_order_punch,e.gst_no as customer_gst_order_punch,a.po_no,k.contact_no as buyer_mobile,k.customer_name as buyer_self_name,k.company_name as buyer_name,k.pan as pan_no, k.email as buyeremail,d.shipping_state as buyer_state,d.shipping_city as buyer_city,k.gst as buyer_gst,d.billing_address as buyer_address,k.pan as buyer_pan,c.address as sunder_company_address,c.pincode as sunder_pincode,c.state_id as sunder_state,c.city_id as sundercity,c.gst as sunder_gst,c.email_id as sunder_email,`a`.`added_on`, `a`.`invoice_no`, `d`.`bill_to`,`a`.`hpcl_billing_company`, `d`.`ship_to`, `d`.`shipping_name`, `d`.`shipping_address`, `d`.`shipping_state`, `d`.`shipping_city`, `d`.`shipping_pincode`, `d`.`shipping_phone_no`, `d`.`shipping_mobile_no`, `d`.`shipping_email`, `d`.`billing_name`, `d`.`billing_address`, `d`.`billing_state`, `d`.`billing_city`, `d`.`billing_pincode`, `d`.`billing_phone_no`, `d`.`billing_mobile_no`, `d`.`billing_email`, d.vehicle_no, d.destination, `b`.`lead_id`, `b`.`company_id`, `a`.`id`, `a`.`payment_type`, `a`.`credit_days`, `a`.`cheque_no`, `a`.`pdc_date`, `a`.`utr_no`, `a`.`upload_po`, `a`.`send_to_tally`, `a`.`billing`, `a`.`po_no`, `a`.`po_date`, `b`.`id` as `quotation_id`, `c`.`companyname`, c.bank_details, `a`.`quotation_id` as `orderpunchquote`, `e`.`pan_no`, `e`.`gst_no`, `e`.`msme_no`, `b`.`customer_id`, f.note FROM `order_punch` `a` JOIN `order_punch_mailing_details` `d` ON `a`.`id`=`d`.`order_id` JOIN `order_punch_tax_details` `e` ON `a`.`id`=`e`.`order_id` JOIN `order_punch_payment_details` `f` ON `a`.`id`=`f`.`order_id` JOIN `customer_quotation` `b` ON `b`.`id`=`a`.`quotation_id` JOIN `store_rack_location` `c` ON `c`.`id`=`b`.`company_id` JOIN customer_detail k ON b.customer_id=k.id WHERE a.id='$id'";
   
        $coures=mysqli_query($con,$sel);
        $row_cnt = mysqli_num_rows($coures);
        if($row_cnt==0)
        {
        echo "INVALID ACCESS"; exit;
        }else
        {

        $row=mysqli_fetch_array($coures);
        $billing_company=$row['companyname'];
        $bank_details=$row['bank_details'];
        $billing_company_address=$row['sunder_company_address'];
        $billing_company_pincode=$row['sunder_pincode'];
        $billing_company_state=$row['sunder_state'];
        $billing_company_city=$row['sunder_city'];
        $billing_company_gst=$row['sunder_gst'];
        $billing_company_email=$row['sunder_email'];
        $invoice_no=$row['invoice_no'];
        $order_date=date('d M, Y',strtotime($row['added_on']));
        $buyer_name=$row['buyer_name'];
        $buyer_email=$row['buyer_email'];
        $buyer_state=$row['buyer_state'];
        $buyer_city=$row['buyer_city'];
        $buyer_gst=$row['customer_gst_order_punch'];
        $buyer_address=$row['buyer_address'];
        $buyer_pan=$row['customer_pan_order_punch'];
        $buyer_email=$row['buyer_email'];
        $buyer_self_name=$row['buyer_self_name'];
        $buyer_mobile=$row['buyer_mobile'];
        
        $delivery_note=$row['note'];
        $quotation_id=$row['quotation_id'];
        $seller_gst=$row['sunder_gst'];
        // $seller_gst=07;
        $pan_no=$row['pan_no'];
        $vehicle_no=$row['vehicle_no'];
        $destination=$row['destination'];
        $po_no=$row['po_no'];

        if($row['payment_type'] == 2) {
            $payment_type = 'Cash';
        } else if($row['payment_type'] == 3) {
            $payment_type = 'Online';
        } else if($row['payment_type'] == 4) {
            $payment_type = 'PDC';
        } else if($row['payment_type'] == 5) {
            $payment_type = 'Credit';
        } else {
            $payment_type = '';
        }

        $sel1="SELECT state_name,state_code FROM states WHERE state_id='$billing_company_state'";
         $coures1=mysqli_query($con,$sel1);
        $row_cnt1 = mysqli_num_rows($coures1);
        if($row_cnt1>0)
        {
             $row1=mysqli_fetch_array($coures1);
             $billing_company_state=$row1['state_name'];
             $billing_company_state_code=$row1['state_code'];


        }else{ $billing_company_state=''; $billing_company_state_code=''; }


        $sel7="SELECT state_name,state_code FROM states WHERE state_id='$buyer_state'";
         $coures7=mysqli_query($con,$sel7);
        $row_cnt7 = mysqli_num_rows($coures7);
        if($row_cnt7>0)
        {
             $row7=mysqli_fetch_array($coures7);
             $seller_state=$row7['state_name'];
             $seller_state_code=$row7['state_code'];


        }else{ $seller_state=''; $seller_state_code=''; }

// echo $billing_company_state;exit;

        $sel2="SELECT city_name FROM cities WHERE city_id='$billing_company_city'";
        $coures2=mysqli_query($con,$sel2);
        $row_cnt2 = mysqli_num_rows($coures2);
        if($row_cnt2>0)
        {
        $row2=mysqli_fetch_array($coures2);
        $billing_company_city=$row2['city_name'];
        }else{ $billing_company_city=''; }






        }


}else
{
    echo "Invalid Request"; exit;
}
// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Saurabh Dubey');
$pdf->SetTitle('Tax Invoice');
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
<table width="100% " style="padding:0px;" >
<tr>
<td width="33.3%">
</td>
<td width="33.3%"><p style="text-align:center; font-size:14px;">Tax-Invoice</p>
</td>
<td width="33.3%"><p style="text-align:right; font-size:10px;font-weight:bold;">'.$d.'</p>
</td>
</tr>
</table>


<table width="100% " style="padding:0px;" >
<tr>
<td width="50%" style="border:1px solid black;">
<table width="100%" style="padding:3px;" >
<tr>
<td style="font-size:13px; padding:3px; border-bottom:1px solid black; height:140px;"><b>'.$billing_company.'</b><br><br>
<span style="font-size:11px; margin:0px;">'.$billing_company_address.'<br>
GSTIN/UIN: '.$billing_company_gst.'<br>
State Name :  '.$billing_company_state.', Code : '.$billing_company_state_code.'<br>
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
State Name 	    : '.$seller_state.',<br>CODE :- '.$seller_state_code.',<br/>'.$buyer_self_name.'<br/>'.$buyer_mobile.'<br/>'.$buyer_email.'

</span>
</td>
</tr>
</table>
</td>
<td width="50%" style="padding:3px;">
<table width="100%" style="padding:3px;" border="1" rule="all">
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Invoice No.</span> <br><b>'.$invoice_no.'</b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Dated</span> <br><b>'.$order_date.'</b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Delivery Note</span> <br><b>'.$delivery_note.'</b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Mode/Terms of Payment</span> <br><b>'.$payment_type.'</b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Supplier’s Ref.</span> <br><b></b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Other Reference(s)</span> <br><b></b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Buyer’s Order No.</span> <br><b>'.$po_no.'</b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Dated</span> <br><b>'.$order_date.'</b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Dispatch Document No.</span> <br><b></b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Delivery Note Date</span> <br><b></b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Dispatched through</span> <br><b>'.$vehicle_no.'</b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Destination</span> <br><b>'.$destination.'</b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px; height:70px;" colspan="2"><span style="font-size:11px; margin:0px;">Terms of Delivery</span> <br><b></b></td>
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
<td width="10%" style="text-align:center; border:1px solid black;">Batch No.</td>
<td width="9%" style="text-align:center; border:1px solid black;">Quantity</td>
<td width="9%" style="text-align:center; border:1px solid black;">Rate</td>
<td width="6%" style="text-align:center; border:1px solid black;">per</td>
<td width="20%" style="text-align:center; border:1px solid black;">Amount</td>
</tr>';

 $sql3 = "SELECT a.qty,a.agreed_price,b.instruments_name,b.hsncode,a.batch_code,c.shortname FROM customer_quotation_detail a JOIN presto_instruments b ON b.id=a.product_id LEFT JOIN units c ON c.id=a.pack_size WHERE quotation_id=$quotation_id";
 $result3 = mysqli_query($con,$sql3);
 $row_cnt3 = mysqli_num_rows($result3);
$i = 1;
$total_price = array();

 if($row_cnt3 > 0) {
    while($row3=mysqli_fetch_array($result3)) {
        $total = $row3['agreed_price'] * $row3['qty'];
        $html .= '<tr>
        <td style="border-left:1px solid black; text-align:center;">'.$i.'</td>
        <td style="border-left:1px solid black;"><i><b>'.$row3['instruments_name'].'</b></i></td>
        <td style="border-left:1px solid black;">'.$row3['hsncode'].'</td>
        <td style="border-left:1px solid black;">'.$row3['batch_code'].'</td>
        <td style="border-left:1px solid black;">'.$row3['qty'].'</td>
        <td style="border-left:1px solid black;"> '.$row3['agreed_price'].'</td>
        <td style="border-left:1px solid black;">'.$row3['shortname'].'</td>
        <td style="border-left:1px solid black; border-right:1px solid black; text-align:right;"><b>'.$total.'</b></td>
        </tr>';
    $total_price[] = $total;
   $i++;
    }
}


$html .= '<tr>
<td style="border-left:1px solid black; text-align:center;"></td>
<td style="border-left:1px solid black; text-align:right;"><i><b>Taxable Amount </b></i></td> 
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black; border-right:1px solid black; text-align:right;"><b>'.array_sum($total_price).'</b></td>
</tr>';
$total_gst = 0;
    if($buyer_gst<>'' && $seller_gst <>'')
    {
        $bscode=substr($buyer_gst,0,2);
        $sscode=substr($seller_gst,0,2);

        if($bscode==$sscode)
        {
            $gst=((array_sum($total_price))*9)/100;
            // $sgst=round(($gst/2),2);
            // $igst=0;

            $html .= '<tr>
                        <td style="border-left:1px solid black; text-align:center;"></td>
                        <td style="border-left:1px solid black; text-align:right;"><i><b>OUTPUT CGST @9%</b></i></td> 
                        <td style="border-left:1px solid black;"></td>
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
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black; border-right:1px solid black; text-align:right;"><b>'.$gst.'</b></td>
                        </tr>';
            $total_gst = $gst+$gst;

        }else
        {
            $gst=((array_sum($total_price))*18)/100;
            $igst=round($gst,2);
            $cgst=0;

            $html .= '<tr>
                        <td style="border-left:1px solid black; text-align:center;"></td>
                        <td style="border-left:1px solid black; text-align:right;"><i><b>IGST @18%</b></i></td> 
                        <td style="border-left:1px solid black;"></td>
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

$final_amt = array_sum($total_price) + $total_gst;
$html .= '<tr>
<td style="border:1px solid black; text-align:center;"></td>
<td style="border:1px solid black; text-align:right;">Total</td> 
<td style="border:1px solid black;"></td>
<td style="border:1px solid black;"></td>
<td style="border:1px solid black;"></td>
<td style="border:1px solid black;"></td>
<td style="border:1px solid black;"></td>
<td style="border:1px solid black; border-right:1px solid black; text-align:right;"><b>Rs. '.round($final_amt).'</b></td>
</tr>
</table>
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
            $gst=((array_sum($total_price))*9)/100;
$html .= '<td width="15%" style="text-align:center;">CGST 9%</td>
<td width="15%" style="text-align:center;">SGST 9%</td>';
 $total_gst = $gst+$gst;
        }else
        {
            $gst=((array_sum($total_price))*18)/100;
            $igst=round($gst,2);
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

$sql33 = "SELECT a.qty,a.agreed_price,b.instruments_name,b.hsncode, c.shortname FROM customer_quotation_detail a JOIN presto_instruments b ON b.id=a.product_id JOIN units c ON c.id=a.pack_size WHERE quotation_id=$quotation_id";
 $result33 = mysqli_query($con,$sql33);
 $row_cnt33 = mysqli_num_rows($result33);

if($row_cnt33 > 0) {
    $data33 = mysqli_fetch_array($result33);
    // echo "<pre>";print_r($data33);exit;
    $hsncode = $data33['hsncode'];

$html .= '<tr>
<td>'.$hsncode.'</td>
<td>'.array_sum($total_price).'</td>';
$total_gst = 0;
    if($buyer_gst<>'' && $seller_gst <>'')
    {
        $bscode=substr($buyer_gst,0,2);
        $sscode=substr($seller_gst,0,2);
        if($bscode==$sscode)
        {
            $gst=((array_sum($total_price))*9)/100;
$html .= '<td>'.$gst.'</td>
          <td>'.$gst.'</td>';
 $total_gst = $gst+$gst;
        }else
        {
            $gst=((array_sum($total_price))*18)/100;
            $igst=round($gst,2);
            $cgst=0;
         $total_gst = $gst;

         $html .= '<td>'.$total_gst.'</td>';
        }

    }else
    {
        $igst=0;
        $cgst=0;
    }
$html .= '<td>'.round($total_gst).'</td>
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
            $gst=((array_sum($total_price))*9)/100;
$html .= '<td></td>
          <td></td>';
 $total_gst = $gst+$gst;
        }else
        {
            $gst=((array_sum($total_price))*18)/100;
            $igst=round($gst,2);
            $cgst=0;
         $total_gst = $gst;

         $html .= '<td></td>';
        }

    }else
    {
        $igst=0;
        $cgst=0;
    }
$html .= '<td>'.round($total_gst).'</td>
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
<td>: <b>'.substr($billing_company_gst,2,11).'</b></td>
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

<p style="text-align:right; margin-bottom:50px;"><b>for '.$billing_company.'</b></p>
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
