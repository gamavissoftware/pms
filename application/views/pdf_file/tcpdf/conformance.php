<?php




/** QUERY ENDS **/


require_once('tcpdf/tcpdf_include.php');

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


<p style="text-align:center; font-weight:bold; font-size:16px;">CONFORMANCE CERTIFICATE</p>

<table style="width:100%; padding:3px; font-size:12px;">
<tr>
<td style="width:250px;">Conformation Certificates No.</td>
<td style="text-align:right; width:20px;">:</td>
<td style="width:350px;">PSPL/CON/2019/(6973)</td>
</tr>

<tr>
<td style="width:250px;">Issued</td>
<td style="text-align:right; width:20px;">:</td>
<td style="width:350px; font-weight:bold;">Indorama Ventures & Packaging Nigeria Limited</td>
</tr>

<tr>
<td style="width:250px;">Address</td>
<td style="text-align:right; width:20px;">:</td>
<td style="width:350px;">East West Express Way, Eleme, Port Harcourt Rivers State,Nigeria,Nigeria</td>
</tr>

<tr>
<td style="width:250px;">Date</td>
<td style="text-align:right; width:20px;">:</td>
<td style="width:350px;">19/12/2019</td>
</tr>

<tr>
<td style="width:250px;">Instrument Name</td>
<td style="text-align:right; width:20px;">:</td>
<td style="width:350px; font-weight:bold;">Ball Impact Tester for Closures</td>
</tr>

<tr>
<td style="width:250px;">Serial No. of Instrument</td>
<td style="text-align:right; width:20px;">:</td>
<td style="width:350px;">10190445</td>
</tr>

<tr>
<td style="width:250px;">Validity</td>
<td style="text-align:right; width:20px;">:</td>
<td style="width:500px;">12 Months</td>
</tr>
</table>
<br>
<br>
<br>
<br>
<table style="width:100%; padding:3px; font-size:12px;">
<tr>
<td>We hereby certify that this product has been inspected and found to confirm to the applicable National Standards and Presto Q.C. Norms.</td>
</tr>


</table>
<br>
<br>
<br>
<br>
<br>
<table style=" padding:3px; font-size:12px; width:100%;" >
<tr>
<td style="text-align:left; "><span style="font-weight:bold;">Inspected by</span>
<br>
<br>
<br>
<br>
<br>
(Q.C. Engineer)
</td>

<td style="text-align:right; "><span style="font-weight:bold;">Approved by</span>
<br>
<br>
<br>
<br>
<br>
(Manager)
</td>

</tr>
</table>

<ul style="    font-style: italic; font-size:9px;">
<li>This report id for private use only. Not to be used for Publicity or Litigation.</li>
<li>This Certificate refers only to the particular item submitted for calibration</li>
<li>This Certificate Shall not be reproduced, except in full, without the written permission of chief executive Presto Stantest Pvt Ltd. Faridabad.</li>
<li>Results Reported are valid at the time of and under the stated conditions of measurement.</li>
<li>Laboratory standards are traceable to National Standards.</li>
<li>Calibration Certificate issued for Weight and Measure parameter i.e. Mass, Balance, Volumeric equipment, Measuring Scales/Tapes etc. are for scientific purpose only and should notbe used for Trade/Commercial Use.</li>
</ul>
<br>
<br>

<table>
<tr>
<td></td>
<td><img src="tcpdf/images/check.jpeg" width="70"></td>
<td></td>
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
