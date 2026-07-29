<?php




/** QUERY ENDS **/


require_once('tcpdf/tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);



// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH);
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
$pdf->AddPage('L', 'A4');
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

// Call before the addPage() method
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
// set text shadow effect
$pdf->setTextShadow(array('enabled' => false, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

// Set some content to print

/** quert paet **/

$html = '

<table width="100%" style="padding:3px; ">
<tr>
<td style="font-size:25px; text-align:left; border:1px solid black; background-color:lightgrey;">
<b>Macro Planning Sheet</b>
</td>

<td style="text-align:right;">
<img src="https://www.hongyijig.com/wp-content/uploads/2021/01/HongyiJig-main-logo.png" width="150px" >
</td>
</tr>
</table>
<br><br>

<table width="100%" style="padding:3px;" border="1" ruled="all">
<tr>
<td style="background-color:lightgrey;">Query No</td>
<td></td>
<td style="background-color:lightgrey;">Order Punching Date</td>
<td></td>
</tr>
<tr>
<td style="background-color:lightgrey;">Project Name</td>
<td></td>
<td style="background-color:lightgrey;">No of Mould</td>
<td></td>
</tr>
<tr>
<td style="background-color:lightgrey;">Client Name</td>
<td></td>
<td style="background-color:lightgrey;">Quotation Tooling Time</td>
<td></td>
</tr>
</table>
<br>
<br>
<table width="100%" style="padding:3px; text-align:center;" border="1" ruled="all">
<tr>
<th style="background-color:lightgrey;" width="10%">S.NO.</th>
<th style="background-color:lightgrey;" width="30%">STAGE NAME</th>
<th style="background-color:lightgrey;" width="20%">START DATE</th>
<th style="background-color:lightgrey;" width="20%">END DATE</th>
<th style="background-color:lightgrey;" width="20%">TOTAL WORKING DAYS</th>
</tr>
<tr>
<td>1</td>
<td>B0</td>
<td>02-03-2023</td>
<td>30-03-2023</td>
<td>24</td>
</tr>
</table>

';


//echo $footer_logo_html; exit;
// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.



$filelocation = $_SERVER['DOCUMENT_ROOT'] . '/image_bank/popdf';
$fileNL = $filelocation . "/akash.pdf"; //Linux

$pdf->Output($fileNL, 'I');



//============================================================+
// END OF FILE
//============================================================+
