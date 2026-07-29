<?php
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies
ob_start();
define('filepath',$_SERVER['DOCUMENT_ROOT'].'/application/views/formats/rfq/examples/');
define('imagepaths',$_SERVER['DOCUMENT_ROOT'].'/application/views/formats/rfq/examples/images/');

$poid=$this->uri->segment(3);
$lead_id=$this->uri->segment(4);
$CI=&get_instance();
$CI->load->model('Salescrm_model','salescrm');
$podetails=$CI->salescrm->getporeceivedData($poid,$lead_id);
if(count($podetails)>0)
{
    $payment_term=$podetails[4];

}else
{
    echo "Invalid Details"; exit;
}

$quote_record_id=$CI->salescrm->getRecordID($lead_id);
$basicData=$CI->salescrm->getquoteBasicData($quote_record_id);
if(count($basicData)>0)
{
    $currency=$basicData[3];
}else
{
    echo "Invalid Details"; exit;
}

$re=$this->db->select('payment_percentage')->from('payment_terms_milestone')->where('payment_term_id',$payment_term)->order_by('id','ASC')->limit(1)->get();
if($re->num_rows()>0)
{
    foreach($re->result() as $rrow);
    $payment_percent=$rrow->payment_percentage;
}else
{
    echo "Payment Terms not found please edit payment terms and check!"; exit;
}

$re=$this->db->select('payment_terms')->from('payment_terms')->where('id',$payment_term)->get();
if($re->num_rows()>0)
{
    foreach($re->result() as $rrrrror);
    $payment_terms_written=$rrrrror->payment_terms;
}else
{
    $payment_terms_written='';
}

$rest=$this->db->select('*')->from('performa_invoice')->where('lead_id',$lead_id)->where('po_id',$poid)->get();
if($rest->num_rows()>0)
{
    foreach($rest->result() as $row);
    $invoice_no=$row->invoice_no;
    $invoice_date=$row->invoice_date;
    $state=$row->state;
    $place_of_supply=$row->place_of_supply;
    $customer_po=$row->customer_po;
    $customer_po_date=$row->customer_po_date;
    $freight=$row->freight;
    $remarks=$row->remarks;
    $record_id=$row->id;
    $buyer_order_no=$row->buyer_order_no;
    $buyer_order_date=$row->buyer_order_date;
    $iec_code=$row->iec_code;
    $rbi_code=$row->rbi_code;
    $eepc_no=$row->eepc_no;
    $hsn=$row->hsn;


    $rest=$this->db->select('*')->from('performa_invoice_address')->where('record_id',$record_id)->get();
    if($rest->num_rows()>0)
    {
    foreach($rest->result() as $row);
    $bill_to_name=$row->bill_to_name;
    $bill_address=$row->bill_address;
    $bill_state=$row->bill_state;
    $bil_state_code=$row->bil_state_code;
    $bill_gst=$row->bill_gst;
    $bill_pan=$row->bill_pan;
    $ship_to_name=$row->ship_to_name;
    $ship_address=$row->ship_address;
    $ship_state=$row->ship_state;
    $ship_state_code=$row->ship_state_code;
    $ship_gst=$row->ship_gst;
    $ship_pan=$row->ship_pan;
    $bill_iec_no=$row->bill_iec;

    }else
    {
    echo "Invalid Access"; exit;
    }



 $rest=$this->db->select('*')->from('performa_invoice_export_shipping_details')->where('record_id',$record_id)->get();
    if($rest->num_rows()>0)
    {
    foreach($rest->result() as $row);
    $pre_carriage=$row->pre_carriage;
    $carriage_receipt=$row->carriage_receipt;
    $country_of_origin=$row->country_of_origin;
    $country_of_final_destination=$row->country_of_final_destination;
    $vessel=$row->vessel;
    $port_of_loading=$row->port_of_loading;
    $terms_of_payment=$row->terms_of_payment;
    $port_of_discharge=$row->port_of_discharge;
    $final_destination=$row->final_destination;
    $country_final_destination=$row->country_final_destination;
    }else
    {
    echo "Invalid Access"; exit;
    }





}else
{
    echo "Invalid Access"; exit;
}





//============================================================+
// File name   : example_001.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 001 for TCPDF class
//               Default Header and Footer
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Default Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 */

// Include the main TCPDF library (search for installation path).

require_once(filepath.'tcpdf_include.php');

class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Logo

       // if($this->page==2)
       // {
        //$this->SetMargins(10, 30, 10, 10);
        $image_file = imagepaths.'Header.jpg';
        //echo $image_file; exit;
        $this->Image($image_file, 0, 2, 200);
        // Set font
        $this->SetFont('helvetica', 'B', 20);
        // Title
        $this->Cell(0, 15, '', 0, false, 'C', 0, '', 0, false, 'M', 'M');
       // }


         $pageWidth = $this->getPageWidth();
        $pageHeight = $this->getPageHeight();

        // Set transparency
        $this->SetAlpha(0.5);  // Increase transparency to make the watermark lighter

        // Set the font for the watermark
        $this->SetFont('helvetica', 'B', 80);

        // Set a light gray color for the watermark text
        $this->SetTextColor(200, 200, 200);

        // Calculate x and y position for the watermark
        $watermarkText = "Shubham Pack";
        $textWidth = $this->GetStringWidth($watermarkText, 'helvetica', 'B', 80);
        $x = ($pageWidth / 2) - ($textWidth / 2);
        $y = ($pageHeight / 2)-(80/4); // Adjusted for font size

        // Rotate the text
        $this->StartTransform();
        $this->Rotate(45, $pageWidth / 2, $pageHeight / 2);

        // Add the watermark text
        $this->Text($x, $y, $watermarkText);

        // Stop the transformation
        $this->StopTransform();

        // Reset transparency
        $this->SetAlpha(1);

        // Reset text color to default
        $this->SetTextColor(0, 0, 0);
    }

    // Page footer
    public function Footer() {
    $this->SetY(-50);
    $logoX = -2; // 
    $logoFileName = imagepaths.'Footer.jpg';
    $logoWidth = 210; // 15mm
    $logoY = 270;
    $logo = $this->Image($logoFileName, $logoX, $logoY, $logoWidth);
    // $this->SetX($this->w - 18 - $logoWidth); // documentRightMargin = 18
    // $this->Cell(10,10, $logo, 0, 0, '');
    }
}


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 065');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);



// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(15, PDF_MARGIN_TOP, 15, PDF_MARGIN_FOOTER);
$pdf->SetMargins(10, 25, 10, 20, true);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// define ('PDF_PAGE_ORIENTATION', 'L');

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetAutoPageBreak(TRUE, 2);
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
$pdf->setPrintFooter(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('dejavusans', '', 12, '', true);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// set text shadow effect
$pdf->setTextShadow(array('enabled'=>false, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));


// Set some content to print
$html = '
<style>
    table{
        width:100%;
        }

        .table {
            width: 100%;
            font-size:12px;
            padding:5px;
        }


        .table th {
            border: 1px solid #000;
            background-color: #e5e5e5;
            text-align: center;
        }

        .table td {
            border: 1px solid #000;
            background-color: #fff;
            text-align: center;
        }

        .table1 {
            font-size:9px;
            width: 100%;
              padding: 5px;
        }

        .table1 th {
            border: 1px solid #000;
            background-color: #e5e5e5;
            text-align: center;
          
        }

        .table1 td {
            border: 1px solid #000;
            background-color: #fff;
            text-align: center;
        }

        .table2 {
            font-size:9px;
            width: 100%;
              padding: 5px;
              
        }

        .table2 th {
           
            background-color: #e5e5e5;
            text-align: center;
          
        }

        .table2 td {
            background-color: #fff;
            text-align: left;
        }

        


</style>

<table class="table1">
    <tr>
        <td style="font-size:13px;">PROFORMA INVOICE</td>
    </tr>
</table>
<table class="table1">
    <tr>
        <td><b style="font-size:13px;">Shubham Flexible Packaging Machines Pvt. Ltd.</b><br>B-8A, Sector-59, Part II, Ballabgarh, Faridabad - 121 004, India<br>Email ID: info@shubhampack.com Phone:+91 129 427 2350<br>PAN: ASD4578562, GSTIN: ASD45785625632 CIN: 8795623145878<br>State Name: HARYANA State Code: 06</td>
    </tr>
</table>
<table class="table1">
    <tr>
        <td>
            <table class="table2">
                <tr>
                    <td width="30%">Prof.Inv.No.</td>
                    <td width="70%">: '.$invoice_no.'</td>
                </tr>
                  <tr>
                    <td width="30%">Prof.Inv Date</td>
                    <td width="70%">: '.date('d-M-Y',strtotime($invoice_date)).'</td>
                </tr>
                  
            </table>
        </td>
        <td>
            <table class="table2">
                <tr>
                    <td width="30%">Buyer Order No.</td>
                    <td width="70%">: '.$buyer_order_no.'</td>
                </tr>
                  <tr>
                    <td width="30%">Buyer Order Date</td>
                    <td width="70%">: '.date('d-M-Y',strtotime($buyer_order_date)).'</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table class="table1">
    <tr>
        <td>
            Details of Customer (Bill To)
        </td>
        <td>
           Details of Customer (Ship To)
        </td>
    </tr>
</table>
<table class="table1">
    <tr>
        <td>
             <table class="table2">
                <tr>
                    <td width="40%">Name</td>
                    <td width="60%">:'.$bill_to_name.'</td>
                </tr>
                  <tr>
                    <td width="40%">Address</td>
                    <td width="60%">: '.$bill_address.'</td>
                </tr>
                  <tr>
                    <td width="40%">State</td>
                    <td width="60%">: '.$bill_state.'</td>
                </tr>
                  <tr>
                    <td width="40%">State Code</td>
                    <td width="60%">: '.$bil_state_code.'</td>
                </tr>
                 <tr>
                    <td width="40%">IEC Code</td>
                    <td width="60%">: '.$iec_code.'</td>
                </tr>
                  <tr>
                    <td width="40%">GST No.</td>
                    <td width="60%">: '.$bill_gst.'</td>
                </tr>
                 
            </table>
        </td>
        <td>
         <table class="table2">
               <tr>
                    <td width="40%">Name</td>
                    <td width="60%">: '.$ship_to_name.'</td>
                </tr>
                  <tr>
                    <td width="40%">Address</td>
                    <td width="60%">: '.$ship_address.'</td>
                </tr>
                  <tr>
                    <td width="40%">State</td>
                    <td width="60%">: '.$ship_state.'</td>
                </tr>
                  <tr>
                    <td width="40%">State Code</td>
                    <td width="60%">: '.$ship_state_code.'</td>
                </tr>
                 
            </table>
        </td>
    </tr>
</table>
<table class="table1">
    <tr>
       
        <td width="25%" style="text-align:left;">Country of Origin of Goods<br>'.$country_of_origin.'</td>
        <td width="25%" style="text-align:left;">Country of Final Destination<br>'.$country_of_final_destination.'</td>
         <td width="25%" style="text-align:left;">Port of Loading<br>'.$port_of_loading.'</td>
         <td width="25%" style="text-align:left;">Port of Discharge<br>'.$port_of_discharge.'</td>
       
    </tr>
    
</table>';

if($currency==1){
    $curr = "USD";
    $sign="$";
}else if($currency==2){
    $curr = "INR";
    $sign="₹";
}else{
    $curr = "EURO";
    $sign="€";
}


$html.='<table class="table1">
    <tr>
        <th width="7%">Sr.No.</th>
       
        <th width="28%">Description of Goods/Services</th>
        <th width="15%">HSN/SAC<br> Code</th>
        <th width="5%">Qty</th>
        <th width="10%">UOM</th>
        <th width="18%">Rate '.$curr.'</th>
        <th width="17%">Total Amount</th>
    </tr>';

        $qty_array=array();
        $total_array[]=0;
        $qty_array[]=0;
        $basic_cost=array();
        $basic_cost[]=0;
        $rest=$this->db->select('*')->from('performa_invoice_items')->where('record_id',$record_id)->get();
        if($rest->num_rows()>0)
        {
            $t=1;
        foreach($rest->result() as $row)
        {

            if($row->discount_per>0)
            {
                $revised_rate=floatval($row->rate-($row->rate*($row->discount_per/100)));
            }else
            {
                $revised_rate=floatval($row->rate);
            }

            $total=floatval(round($revised_rate*$row->qty,2));
            $qty_array[]=$row->qty;
            $total_array[]=$total;
            $basic_cost[]=$total;
        $html.='<tr>
        <td>'.$t.'.</td>
        
        <td>'.ucwords(strtolower($row->description)).'</td>
        <td>'.strtoupper($hsn).'</td>
        <td>'.$row->qty.'</td>
        <td>'.$row->uom.'</td>
        <td>'.$CI->salescrm->formatIndianNumber(floatval($row->rate)).'</td>
    
        <td>'.$CI->salescrm->formatIndianNumber($total).'</td>
        </tr>';
        $t++;
        }
        }


        $othercharge=$CI->salescrm->quotation_freight_packing_forwarding($quote_record_id);
                    //echo "<pre>"; print_r($othercharge); exit;
                    $i=$t;
                    if(count($othercharge)>0)
                    {



                    if($othercharge['packing_charges']>0)
                    {
                        $charge=$othercharge['packing_charges']/100;
                        $forwarding_cost=array_sum($basic_cost)*$charge;
                        $total_array[]=$forwarding_cost;
                    $html.='<tr>
                    <td>'.$i.'.</td>

                    <td>'.ucwords(strtolower('Packaging Charges')).' @ '.floatval($othercharge['packing_charges']).'%</td>
                    <td>'.strtoupper($hsn).'</td>
                    <td>-</td>
                    <td>-</td>
                    <td>'.$CI->salescrm->formatIndianNumber(floatval($forwarding_cost)).'</td>
                    <td>'.$CI->salescrm->formatIndianNumber(floatval($forwarding_cost)).'</td>
                    </tr>';

                    $i++;
                    }



                    if($othercharge['forwarding_charges']>0)
                    {
                        $charge=$othercharge['forwarding_charges']/100;
                        $forwarding_cost=array_sum($basic_cost)*$charge;
                        $total_array[]=$forwarding_cost;
                    $html.='<tr>
                    <td>'.$i.'.</td>

                    <td>'.ucwords(strtolower('Forwarding Charges')).' @ '.floatval($othercharge['forwarding_charges']).'%</td>
                    <td>'.strtoupper($hsn).'</td>
                    <td>-</td>
                    <td>-</td>
                    <td>'.$CI->salescrm->formatIndianNumber(floatval($forwarding_cost)).'</td>
                    <td>'.$CI->salescrm->formatIndianNumber(floatval($forwarding_cost)).'</td>
                    </tr>';

                    $i++;
                    }


                    if($othercharge['insurance']>0)
                    {

                        $charge=$othercharge['insurance']/100;
                        $forwarding_cost=array_sum($basic_cost)*$charge;
                        $total_array[]=$forwarding_cost;
                    $html.='<tr>
                    <td>'.$i.'.</td>    

                    <td>'.ucwords(strtolower('Insurance Charges')).' @ '.floatval($othercharge['insurance']).'%</td>
                    <td>'.strtoupper($hsn).'</td>
                    <td>-</td>
                    <td>-</td>
                    <td>'.$CI->salescrm->formatIndianNumber(floatval($forwarding_cost)).'</td>
                    <td>'.$CI->salescrm->formatIndianNumber(floatval($forwarding_cost)).'</td>
                    </tr>';

                    $i++;

                    }


                    $ftypedata=$othercharge['freight_type'];
                    if($othercharge['freight']==1 || $othercharge['freight']==2)
                    {
                            if($othercharge['freight']==1)
                            {
                            $ftype="Extra at Actuals";
                            }else
                            {
                            $ftype="In Customer Scope";
                            }

                    }else
                    {
                   
                        $total_array[]=$othercharge['freight_charges'];
                    $html.='<tr>
                    <td>'.$i.'.</td>    

                    <td>'.ucwords(strtolower('Freight')).'</td>
                    <td>'.strtoupper($hsn).'</td>
                    <td>-</td>
                    <td>-</td>
                    <td>'.$CI->salescrm->formatIndianNumber(floatval($othercharge['freight_charges'])).'</td>
                    <td>'.$CI->salescrm->formatIndianNumber(floatval($othercharge['freight_charges'])).'</td>
                    </tr>';

                    $i++;
                    }
                }

    // $html.='<tr>
    //     <td></td>
    //     <td>Total</td>
    //     <td></td>
    //     <td></td>
    //     <td>'.$CI->salescrm->formatIndianNumber(array_sum($qty_array)).'</td>
    //     <td></td>
    //     <td></td>
    //     <td></td>
    //     <td></td>
    //     <td>'.$CI->salescrm->formatIndianNumber(array_sum($total_array)).'</td>
    // </tr>
$html.='</table>


<table class="table1">
    <tr>
        <td style="text-align:left;"><b>Remarks:</b>'.$remarks.'</td>
        <td style="text-align:left;"><b>Total Amount - '.$sign.$CI->salescrm->formatIndianNumber(round(array_sum($total_array))).'</b></td>
    </tr>
</table>
<table class="table1">
    <tr>
        <td style="text-align:left;" rowspan="2"><b>Please Remit this payment to our Bankers</b><br/><br>Axis Bank Ltd.<br>
SCO-40, Sec-7 Market<br>
Ballabgarh, Faridabad 121004<br>
Haryana, India<br>
A/c holder name- Shubham Flexible Packaging Machines Pvt. Ltd.<br>
A/c no.- 920030068344715<br>
Swift code- AXISINBB039<br></td>
        <td style="text-align:left;">
            <table width="100%">
                <tr>
                    <td width="50%"><b>Advance '.floatval($payment_percent).'%</b></td>';
                      $tot=round(array_sum($total_array));
                      
                    $tobepaid=floatval($tot*($payment_percent/100));

                    $html.='<td width="50%" style="text-align:left;"><b>'.$sign.$CI->salescrm->formatIndianNumber($tobepaid).'</b></td>
                </tr>
            </table>
            <br>
            <br>
            <br>
            <br>
           
        </td>
    </tr>
   
         <tr>
            <td>
                <table width="100%">
                 <tr>
                    <td width="50%" style="text-align:left;"><b>Total Amount</b></td>
                  <td width="50%" style="text-align:left;"><b>'.$sign.$CI->salescrm->formatIndianNumber(round($tobepaid)).'</b></td>
                </tr>
            </table>
            </td>
        </tr>
</table>

<table class="table1">
    <tr>
        <td>
             <table class="table2">
                <tr>
                    <td width="20%"><b>Amount In Words</b>.</td>
                    <td width="80%">: '.ucwords(strtolower(str_replace('-','',$CI->salescrm->convertNumberToWordsUSD(round($tobepaid))))).'</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table class="table1">
    <tr>
        <td>
             <table class="table2">
                <tr>
                    <td width="15%"><b>Payment Terms</b>.</td>
                     <td width="85%">: '.ucwords(strtolower($payment_terms_written)).'</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table class="table1">
    <tr>
        <td>
             <table width="100%">
               
                    <tr>
                        <td style="text-align:left;"><b>Incoterms: '.$ftypedata.'<br>Declaration<br>We declare that this invoice shows the actual price<br>of the goods describe and that all<br>particular are true and correct</b></td>
                        <td style="text-align:right;">
                            For, <b>Shubham Flexible Packaging Machines Pvt. Ltd.</b>
             <br>
             <br>
             <br>
             <br>
             <br>
             Authorised Signatory
                        </td>
                    </tr>
              
             </table>
        </td>
    </tr>
</table>

';

// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
ob_end_clean();

$pdf->Output();
// filelocation = SITE_ROOT.'designform/';

//$fileNL='DF_'.$df_form_name.'_'.$design_form_date.'_.pdf'; //Linux
//echo $fileNL; exit;
$pdf->Output($filelocation.$fileNL, 'I');

 // $pdf->Output($filelocation.$fileNL, 'I');

$url6 = base64_encode($fileNL);

echo $url6; exit;
//header('location:'.$pageurl.$this->uri->segment(3)."/".$this->uri->segment(4)."/".$url6); 
//============================================================+
// END OF FILE
//============================================================+
