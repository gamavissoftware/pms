<?php

$order_won_id = $this->uri->segment(3);
$CI = &get_instance();
$CI->load->model('TCPDF_model');
$bill_of_lading = $CI->TCPDF_model->get_buyoff_bill_of_lading($order_won_id);
$bill_of_lading_details = $CI->TCPDF_model->get_buyoff_bill_of_lading_details($bill_of_lading->id);

// echo "<pre>";
// print_r($bill_of_lading);
/** QUERY ENDS **/


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

<table width="100%">
<tr>
<td style="text-align:center;"><img src="https://www.hongyijig.com/wp-content/uploads/2021/01/HongyiJig-main-logo.png" alt="logo" width="200px">
</td>
</tr>
</table>


<h4 style="text-align:center;">BILL OF LADING</h4>

<h4>SHIPPER</h4>

<table style="width:100%; padding:3px;" border="1" ruled="all">
<tr>
    <td style="background-color:lightgrey;">Company Name</td>
    <td>'.$bill_of_lading->shipper_company_name.'</td>
</tr>
<tr>
    <td style="background-color:lightgrey;">Company Address</td>
    <td>'.$bill_of_lading->shipper_address.'</td>
</tr>
<tr>
    <td style="background-color:lightgrey;">Contact Person Name</td>
    <td>'.$bill_of_lading->shipper_name.'</td>
</tr>
<tr>
    <td style="background-color:lightgrey;">Company Person No.</td>
    <td>'.$bill_of_lading->shipper_phone.'</td>
</tr>
<tr>
    <td style="background-color:lightgrey;">Email Id</td>
    <td>'.$bill_of_lading->shipper_email.'</td>
</tr>
</table>

<h4>CONSIGNEE</h4>

<table style="width:100%; padding:3px;" border="1" ruled="all">
<tr>
    <td style="background-color:lightgrey;">Company Name</td>
    <td>'.$bill_of_lading->consignee_company_name.'</td>
</tr>
<tr>
    <td style="background-color:lightgrey;">Company Address</td>
    <td>'.$bill_of_lading->consignee_address.'</td>
</tr>
<tr>
    <td style="background-color:lightgrey;">Contact Person Name</td>
    <td>'.$bill_of_lading->consignee_name.'</td> 
</tr>
<tr>
    <td style="background-color:lightgrey;">Company Person No.</td>
    <td>'.$bill_of_lading->consignee_phone.'</td>
</tr>
<tr>
    <td style="background-color:lightgrey;">Email Id</td>
    <td>'.$bill_of_lading->consignee_email.'</td>
</tr>
</table>
<br><br>

<table style="width:100%; padding:3px; text-align:center;" border="1" ruled="all">
<tr>
    <th width="20%" style="background-color:lightgrey;">MARKS AND NUMBERS</th>
    <th width="20%" style="background-color:lightgrey;">NUMBERS AND KIND OF PACKAGES</th>
    <th width="20%" style="background-color:lightgrey;">DESCRIPTION OF GOODS</th>
    <th width="20%" style="background-color:lightgrey;">GROSS WEIGHT</th>
    <th width="20%" style="background-color:lightgrey;">MEASUREMENT</th>
</tr>';
$str = '';

if($bill_of_lading_details){
  foreach($bill_of_lading_details as $bill_lading_detail){
    $str .= '<tr>
    <td>'.$bill_lading_detail->marks_number.'</td>
    <td>'.$bill_lading_detail->kind_of_package.'</td>
    <td>'.$bill_lading_detail->description_of_goods.'</td>
    <td>'.$bill_lading_detail->gross_weight.'</td>
    <td>'.$bill_lading_detail->measurement.'</td>
</tr>';
  }
}

$html .= $str;

$html .= '</table>';  


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
