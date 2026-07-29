<?php
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
<p style="text-align:center; font-size:14px;">Tax-Invoice</p>

<table width="100% " style="padding:0px;" >
<tr>
<td width="50%" style="border:1px solid black;">
<table width="100%" style="padding:3px;" >
<tr>
<td style="font-size:13px; padding:3px; border-bottom:1px solid black;"><b>SUNDER INDUSTRIAL OIL</b><br><br>
<span style="font-size:11px; margin:0px;">B-21,F.I.T(FARIDABAD INDUSTRIAL TOWN)<br>
SECTOR-57, BALLABGARH<br>
DISTT. FARIDABAD<br>
GSTIN/UIN: 06ACBPN9723G2ZK<br>
State Name :  Haryana, Code : 06<br>
E-Mail : faridabadcfa@gmail.com
</span>
</td>
</tr>
<tr>
<td style="font-size:13px; padding:3px;">
<span style="font-size:11px; margin:0px;">Buyer</span><br>
<b>HINDUSTAN PETROLEUM CORPORATION LTD.</b><br>
<span style="font-size:11px; margin:0px;">B-21,F.I.T(FARIDABAD INDUSTRIAL TOWN)<br>
NH-8, DELHI JAIPUR HIGHWAY DHARUHERA,
HARYANA- <br>
GSTIN/UIN	: 06AAACH1118B1ZG<br>
State Name 	    : HARYANA,<br>CODE :- 06,

</span>
</td>
</tr>
</table>
</td>
<td width="50%" style="padding:3px;">
<table width="100%" style="padding:3px;" border="1" rule="all">
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Invoice No.</span> <br><b>SIO /20/2022-23</b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Dated</span> <br><b>29 Aug, 2022</b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Delivery Note</span> <br><b></b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Mode/Terms of Payment</span> <br><b></b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Supplier’s Ref.</span> <br><b>TYPE-1</b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Other Reference(s)</span> <br><b>September ‘21</b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Buyer’s Order No.</span> <br><b>TPYE -1 SALE HPCL </b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Dated</span> <br><b></b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Despatch Document No.</span> <br><b></b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Delivery Note Date</span> <br><b></b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Despatched through</span> <br><b></b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Destination</span> <br><b></b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px;" colspan="2"><span style="font-size:11px; margin:0px;">Terms of Delivery</span> <br><b></b></td>
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
<td width="14%" style="text-align:center; border:1px solid black;">Quantity</td>
<td width="14%" style="text-align:center; border:1px solid black;">Rate</td>
<td width="6%" style="text-align:center; border:1px solid black;">per</td>
<td width="20%" style="text-align:center; border:1px solid black;">Amount</td>
</tr>
<tr>
<td style="border-left:1px solid black; text-align:center;">1</td>
<td style="border-left:1px solid black;"><i><b>PRICE DIFFERNCE  M/O SEPT’21 TYPE1</b></i></td>
<td style="border-left:1px solid black;">9986</td>
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black; border-right:1px solid black; text-align:right;"><b>2,16,945.00</b></td>
</tr>
<tr>
<td style="border-left:1px solid black; text-align:center;"></td>
<td style="border-left:1px solid black; text-align:right;"><i><b>Taxable Amount </b></i></td> 
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black; border-right:1px solid black; text-align:right;"><b>19,525.05</b></td>
</tr>
<tr>
<td style="border-left:1px solid black; text-align:center;"></td>
<td style="border-left:1px solid black; text-align:right;"><i><b>OUTPUT CGST @9%</b></i></td> 
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black; border-right:1px solid black; text-align:right;"><b>19,525.05</b></td>
</tr>
<tr>
<td style="border-left:1px solid black; text-align:center;"></td>
<td style="border-left:1px solid black; text-align:right;"><i><b>OUTPUT SGST @9%</b></i></td> 
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black; border-right:1px solid black; text-align:right;"><b>-.10</b></td>
</tr>
<tr>
<td style="border:1px solid black; text-align:center;"></td>
<td style="border:1px solid black; text-align:right;">Total</td> 
<td style="border:1px solid black;"></td>
<td style="border:1px solid black;"></td>
<td style="border:1px solid black;"></td>
<td style="border:1px solid black;"></td>
<td style="border:1px solid black; border-right:1px solid black; text-align:right;"><b>Rs  2,55,995.00</b></td>
</tr>
</table>
<table style="width:100%; padding:3px; font-size:12px;" border="1">
<tr>
<td>
<span>Amount Chargeable (in words)</span><br>
<b>INR Two lakh fifty five thousand nine hundred ninety five only and paisa nil</b>
</td>
</tr>
</table>
<table style="width:100%; padding:3px; font-size:12px;" border="1">
<tr>
<td width="40%" style="text-align:center;">HSN/SAC</td>
<td width="10%" style="text-align:center;">Taxable Value</td>
<td width="15%" style="text-align:center;">CGST 9%</td>
<td width="15%" style="text-align:center;">SGST 9%</td>
<td width="20%" style="text-align:center;">Total Tax Amount</td>
</tr>

<tr>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td style="text-align:right;">Total</td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
</table>
<table style="width:100%; padding:3px; font-size:12px;" border="1">
<tr><td><span>Tax Amount (in words)  :</span><b>INR Thirty nine thousand fifty and paise ten only.</b><br><br><br>
<table style="width:100%;  font-size:12px;">
<tr>
<td width="50%">
<table style="width:100%;  font-size:12px;">
<tr>
<td>Company’s CST No. </td>
<td>: <b>06831331842</b></td>
</tr>
<tr>
<td>Buyer’s VAT TIN  </td>
<td>: <b>07820024950</b></td>
</tr>
<tr>
<td>Company’s PAN  </td>
<td>: <b>ACBPN9723G</b></td>
</tr>
</table>
</td>
</tr>
<br>
<tr>
<td><span style="font-size:9px;">Declaration<br>
We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.

</span></td>
<td style="border:1px solid black;">
<p style="text-align:right;"><b>for SUNDER INDUSTRIAL OIL</b></p><br><br>
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
