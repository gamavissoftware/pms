<?php
include('mysqliconfig.php');
$id=$_GET['location_id'];
$start_date=$_GET['start_date'];
$end_date=$_GET['end_date'];

$query = "SELECT id, name FROM hpcl_location WHERE id=$id";
$results = mysqli_query($con,$query);

$location_name = '';

if(mysqli_num_rows($results) > 0) {
    $rows=mysqli_fetch_array($results);
    $location_name = $rows['name'];
 }

$start_end_month = array();
$month = strtotime($start_date);
$end = strtotime($end_date);


while($month <= $end) {
	$monthstart=date('Y-m-01',$month);
	$monthend=date('Y-m-t',$month);

	$start_end_month[] = date('F Y', strtotime($monthstart));

	// echo $monthstart;
	$month = strtotime("+1 month", $month);

	// echo strtotime($month;exit;
}

$billing_month = '';

if(count($start_end_month) > 0) {
	$billing_month = implode(', ', $start_end_month);
}

// echo $billing_month;
// exit;


$sql1 = "SELECT a.combination,a.purchase_entry,a.id, a.auto_gen_code, a.current_date, a.payment_terms, a.credit_period FROM approval_form a WHERE a.current_date >= '$start_date' AND a.current_date <= '$end_date'";
$result1 = mysqli_query($con,$sql1);

$grandtotal_claim = array();
$grandtotal_claim[] = 0;

if(mysqli_num_rows($result1) > 0) {
    while($row1=mysqli_fetch_array($result1)) {
    	$approval_id = $row1['id'];


    	 if($row1['combination'] == 0) {
    	 	$totalclaim = array();
    	 	$totalclaim[] = 0;

    	 	$sql2 = "SELECT a.approval_id, a.credit_vli, a.moq, a.validity_from, a.validity_to, a.product_id, a.pack_size, a.location, c.density, c.pack_size as pack_type FROM approval_product_details a JOIN approval_form b ON b.id=a.approval_id JOIN presto_instruments c ON c.id=a.product_id JOIN hpcl_location d ON d.id=a.location WHERE a.approval_id=$approval_id AND a.location=$id";
    	 	$result2 = mysqli_query($con,$sql2);

    	 	if(mysqli_num_rows($result2) > 0) {
            	while($row2=mysqli_fetch_array($result2)) {
            		$product = $row2['product_id'];
	                $vli = $row2['credit_vli'];
	                $moq = $row2['moq'];
	                $product_density = $row2['density'];
	                $product_pack_type = $row2['pack_type'];
	                $approval_pack_size = $row2['pack_size'];
	                $valid_from = $row2['validity_from'];
	                $valid_to = $row2['validity_to'];
	                $location = $row2['location'];

	                $claim = 0;
	                $qty = array();
	                $sql3 = "SELECT a.inventory_id,a.qty,a.pack_size FROM inventory_details a JOIN inventory b ON a.inventory_id=b.id JOIN vendors c ON c.id=b.party WHERE b.currentdate >= '$valid_from' AND b.currentdate <= '$valid_to' AND a.product=$product AND c.hpcl_location=$location";
	              
	                $result3 = mysqli_query($con,$sql3);

	                if(mysqli_num_rows($result3) > 0) {
	                    while($row3=mysqli_fetch_array($result3)) {
	                        $qty[] = $row3['qty'];
	                    }
	                }

	              //  echo "<pre>"; print_r($qty); exit;

	                if($moq > 0) {
	                    if($product_pack_type=="BULK" && $approval_pack_size==5) {
	                        $converted_moq = $moq/$product_density;
	                    } else {
	                        $converted_moq = $moq;
	                    }
	                } else {
	                    $converted_moq = 0;
	                }

	                if(count($qty) > 0) {
	                    $purchased_qty = array_sum($qty);
	                } else {
	                    $purchased_qty = 0;
	                }

	                if($purchased_qty >= $converted_moq) {
	                    $claim=$purchased_qty*$vli;
	                } else {
	                    $diff=$converted_moq-$purchased_qty;
	                }

	                $totalclaim[] = $claim;
            	}
            }

            $grandtotal_claim[] = array_sum($totalclaim);
    	 } else {
    	 	$sql4 = "SELECT a.approval_id, a.credit_vli, a.moq, a.validity_from, a.validity_to, a.product_id, a.pack_size, a.location, c.density, c.pack_size as pack_type FROM approval_product_details a JOIN approval_form b ON b.id=a.approval_id JOIN presto_instruments c ON c.id=a.product_id JOIN hpcl_location d ON d.id=a.location WHERE a.approval_id=$approval_id";
    	 	$result4 = mysqli_query($con,$sql4);

            if(mysqli_num_rows($result4) > 0) {
                   $sql5 = "SELECT moq,vli,type FROM approval_combination_type_1 WHERE approval_id=$approval_id";
                    $result5 = mysqli_query($con,$sql5);

                    $moq=0;
					$vli=0;
					
                    if(mysqli_num_rows($result5) > 0) {
                         $row5 = mysqli_fetch_array($result5);
                         $moq = $row5['moq'];
                         $vli = $row5['vli'];
                         $type = $row5['type'];
                    }

                while($row4 = mysqli_fetch_array($result4)) {
                	$product_density = $row4['density'];
                    $product_pack_type = $row4['pack_type'];
                    $approval_pack_size = $row4['pack_size'];
                    $valid_from = $row4['validity_from'];
	                $valid_to = $row4['validity_to'];
	                $location = $row4['location'];

	                $sql6 = "SELECT product,combination_id FROM approval_combination_detail_type_1 WHERE approval_id=$approval_id";
                    $result6 = mysqli_query($con,$sql6);

                    if(mysqli_num_rows($result6) > 0) {
                         while($row6 = mysqli_fetch_array($result6)) {
                         	$combination_id = $row6['combination_id'];
                            $product = $row6['product'];
			                 
		                    $qty = array();
		                    $sql7 = "SELECT a.inventory_id,a.qty,a.pack_size FROM inventory_details a JOIN inventory b ON a.inventory_id=b.id JOIN vendors c ON c.id=b.party WHERE b.currentdate >= '$valid_from' AND b.currentdate <= '$valid_to' AND a.product=$product AND c.hpcl_location=$location";
		                    $result7 = mysqli_query($con,$sql7);

		                    if(mysqli_num_rows($result7) > 0) {
		                        while($row7=mysqli_fetch_array($result7)) {
		                            $qty[] = $row7['qty'];
		                        }
		                    }
                         }
                    }

                    $sql8 = "SELECT moq,vli FROM approval_combination_type_1 WHERE id=$combination_id";
                    $result8 = mysqli_query($con,$sql8);

                    $moq=0;
					$vli=0;
					
                    if(mysqli_num_rows($result8) > 0) {
                         $row8 = mysqli_fetch_array($result8);
                         $moq = $row8['moq'];
                         $vli = $row8['vli'];
                    }

                    if($moq > 0) {
                        if($product_pack_type=="BULK" && $approval_pack_size==5) {
                            $converted_moq=$moq/$product_density;
                        } else {
                            $converted_moq=$moq;
                        }
                    } else {
                        $converted_moq=0;
                    }

                    if(count($qty) > 0) {
                        $purchased_qty=array_sum($qty);
                    } else {
                        $purchased_qty=0;
                    }

                    if($purchased_qty>=$converted_moq) {
                        $claim=$purchased_qty*$vli;
                    } else {
                        $diff=$converted_moq-$purchased_qty;
                    }

                    $totalclaim[] = $claim;

                }
            }

             $grandtotal_claim[] = array_sum($totalclaim);
    	 }
    }
}

//echo "<pre>"; print_r($grandtotal_claim);exit;

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

    $sel="SELECT c.name, c.address, c.gst, c.state FROM approval_product_details a JOIN hpcl_location b ON b.id=a.location JOIN vendors c ON c.hpcl_location=b.id WHERE a.location=$id";
   // echo $sel;exit;
        $coures=mysqli_query($con,$sel);
        $row_cnt = mysqli_num_rows($coures);
        if($row_cnt==0)
        {
        echo "INVALID ACCESS"; exit;
        }else
        {

        $row=mysqli_fetch_array($coures);
        $invoice_no=$row['invoice_no'];
        $order_date=date('d M, Y',strtotime($row['added_on']));
        $supplier_ref=$row['supplier_ref'];
        $other_ref=$row['other_ref'];
        $terms_of_del=$row['terms_of_del'];
        $dispatched_through=$row['dispatched_through'];
        $destination=$row['destination'];
        $particular=$row['particular'];
        $transporter_rate_type=$row['transporter_rate_type'];
        $transporter_rate_fixed=$row['transporter_rate_fixed'];
        $buyer_name=$row['name'];
        $buyer_address=$row['address'];
        $buyer_gst=$row['gst'];
        $buyer_state=$row['state'];

        $sel1="SELECT state_name,state_code FROM states WHERE state_id=$buyer_state";
         $coures1=mysqli_query($con,$sel1);
        $row_cnt1 = mysqli_num_rows($coures1);
        if($row_cnt1>0)
        {
             $row1=mysqli_fetch_array($coures1);
             $state_name=$row1['state_name'];
             $state_code=$row1['state_code'];


        }else{ $state_name=''; $state_code=''; }



        $sql = "SELECT companyname as sunder_company_name, address as sunder_company_address,pincode as sunder_pincode,state_id as sunder_state,city_id as sundercity,gst as sunder_gst,email_id as sunder_email, bank_details FROM store_rack_location WHERE id=3";
        $result = mysqli_query($con, $sql);

        if(mysqli_num_rows($result) > 0) {
        	$row = mysqli_fetch_array($result);

        	$billing_company_name = $row['sunder_company_name'];
        	$billing_company_address = $row['sunder_company_address'];
	        $billing_company_pincode = $row['sunder_pincode'];
	        $billing_company_state = $row['sunder_state'];
	        $billing_company_city = $row['sunder_city'];
	        $seller_gst = $row['sunder_gst'];
	        $billing_company_email = $row['sunder_email'];
	        $bank_details = $row['bank_details'];
        } else {
        	$billing_company_name = '';
        	$billing_company_address = '';
	        $billing_company_pincode = '';
	        $billing_company_state = '';
	        $billing_company_city = '';
	        $seller_gst = '';
	        $billing_company_email = '';
	        $bank_details = '';
        }


        $sel7="SELECT state_name,state_code FROM states WHERE state_id='$billing_company_state'";
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
<tr><td><span>Tax Amount (in words)  :</span><b>INR '.ucwords(getCurrencyCode(round($total_gst))).'</b><br><br><br><br>
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
