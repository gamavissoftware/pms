<?php


// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 001');
$pdf->SetSubject('TCPDF Tutorial');
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
$pdf->SetFont('dejavusans', '', 14, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// set text shadow effect
// $pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Set some content to print
$html = '
<table style="padding:5px; width:100%;">
<tr>
	<td style="font-size:14px; color:blue;">GST NO. 07AALPM1443Q1Z8</td>
	<td style="font-size:28px; color:black;"><br><br><i>MEHTA COSMETICS</i></td>
</tr>
</table>
<br>
<hr>
<table style="padding:5px; width:100%;">
<tr>
	<td></td>
	<td style="font-size:14px; color:black;">L-112, Sector 2, DSIIDC, Bawana<br>Industrial Area, North West Delhi,<br>Delhi-110039<br>Contact: +91 9811330506<br>Email: G.S.Mehta@hotmail.com</td>
</tr>
</table>
<br>
<br>
<table style="padding:5px; width:100%;">
<tr>
<td style="font-size:14px; text-align:center; color:black;"><b>PURCHASE ORDER</b></td>
</tr>
</table>
<br>
<br>
<table style="padding:5px; width:100%;">
<tr>
<td style="font-size:12px;"><p>To,<br><br><b>MARKEM IMAJE INDIA PVT LTD, Plot No. SP6-44, RIICO Industrial  Area, Karoli, ALWAR, 301707, Rajasthan, India, GSTIN: 08AAACI2979F1ZR</b></p>
<p>Kind attention: Md Danish khan<br>
Contact No.      : 9773896018<br>
<b>Sub: Purchase Order no.    MC/2021-22/26</b><br>
<b>Quotation Ref. No.         NA</b>
</p>
<p>Dear Sir,</p>
<p>We hereby order to supply the following material.</p>
</td>
<td style="font-size:12px; text-align:right;"><p><b>Date:- 28-10-2021</b></p></td>
</tr>
</table>
<br><br>

<table style="width:100%; padding:5px; font-size:12px;" border="1" rule="all">
<tr>
<td style="font-size:12px; text-align:center; width:8%;">Sr.no</td>
<td style="font-size:12px; text-align:center; width:47%;">Description</td>
<td style="font-size:12px; text-align:center; width:8%;">Qty</td>
<td style="font-size:12px; text-align:center; width:12%;">Basic Price Rs.</td>
<td style="font-size:12px; text-align:center; width:10%;">GST</td>
<td style="font-size:12px; text-align:center; width:15%;">Total Amount Rs</td>
</tr>
<tr>
<td style="font-size:12px; text-align:center; "></td>
<td style="font-size:12px; text-align:center; "></td>
<td style="font-size:12px; text-align:center; "></td>
<td style="font-size:12px; text-align:center; "></td>
<td style="font-size:12px; text-align:center; "></td>
<td style="font-size:12px; text-align:center; "></td>
</tr>
</table>
<br>
<br>
<table style="width:100%; padding:5px; font-size:12px;" border="1" rule="all">
<tr>
<td style="font-size:12px;  "></td>
<td style="font-size:12px; "><b>BILL TO ADDRESS</b></td>
<td style="font-size:12px;  "><b>SHIP TO ADDRESS</b></td>
</tr>
<tr>
<td style="font-size:12px;  "></td>
<td style="font-size:12px; ">L-112, Sector 2, DSIIDC, Bawana Industrial Area, North West Delhi, Delhi, 110039</td>
<td style="font-size:12px;  ">L-112, Sector 2, DSIIDC, Bawana Industrial Area, North West Delhi, Delhi, 110039</td>
</tr>

<tr>
<td style="font-size:12px;  "><b>GST No.</b></td>
<td style="font-size:12px; ">07AALPM1443Q1Z8</td>
<td style="font-size:12px;  ">07AALPM1443Q1Z8</td>
</tr>
<tr>
<td style="font-size:12px;  "><b>Contact Person Name</b></td>
<td style="font-size:12px; ">Gurpreet Singh Mehta</td>
<td style="font-size:12px;  ">Gurpreet Singh Mehta</td>
</tr>
<tr>
<td style="font-size:12px;  "><b>Contact No.</b></td>
<td style="font-size:12px; ">9811330506</td>
<td style="font-size:12px;  ">9811330506</td>
</tr>
<tr>
<td style="font-size:12px;  "><b>Email ID.</b></td>
<td style="font-size:12px; ">gurpreet@mehtacosmetics.com</td>
<td style="font-size:12px;  ">gurpreet@mehtacosmetics.com</td>
</tr>
</table>
<br>
<br>
<table style="width:100%; padding:5px; font-size:12px;" border="1" rule="all">
<tr>
<td colspan="2"><b>TERMS & CONDITIONS</b></td>
</tr>
<tr>
<td style="font-size:12px; width:30%;">INCO TERMS</td>
<td style="font-size:12px; width:70%;">EX WORKS Alwar</td>
</tr>
<tr>
<td style="font-size:12px; width:30%;"><b>Payment Terms</b></td>
<td style="font-size:12px; width:70%;"></td>
</tr>
<tr>
<td style="font-size:12px; width:30%;"><b>TAX</b></td>
<td style="font-size:12px; width:70%;"></td>
</tr>
<tr>
<td style="font-size:12px; width:30%;"><b>Quotation Validity Date</b></td>
<td style="font-size:12px; width:70%;"></td>
</tr>
</table>
<p style="font-size:12px;">
Authorised Signatory- Name & Signature with stamp<br><br><br><br>
<br><br>

AMRIT SINGH MEHTA
</p>
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
