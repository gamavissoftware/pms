<?php
// Include the main TCPDF library (search for installation path).





require_once('tcpdf/tcpdf_include.php');


// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

	


}



// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);



// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH);
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setCellPaddings(0,0,0,0);

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
// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// set text shadow effect
$pdf->setTextShadow(array('enabled'=>false, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Set some content to print

/** quert paet **/
 
  
/** end **/

	
$html='  	

<table style="font-size:10px; padding:3px;">
<tr>
<td style=" text-align:center; font-weight:bold;">SUPPLIER
</td>
</tr>
</table>
<br>
<br>
<table style="font-size:9px; padding:3px;">

<tr>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey; width:40px;">S. No.</th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey;">Part Code </th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey;">Part Name</th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey;">Part CFM</th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey; width:100px;">Part Date / PPT Name</th>
<th  style="text-align:center; border:1px solid black;   background-color:lightgrey;">Part Size (mm)</th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey;">Part Weight (Grams)</th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey;">Mould Type </th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey;">Cavitation</th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey;">Tool Steel Cavity </th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey;">Tool Steel Core</th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey;">Tool Steel Mould Base</th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey; width:50px;">Runner Type </th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey;">Insert Moulding</th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey;">Remarks </th>
</tr>

<tr>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
</tr>

<tr>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
<td style="border:1px dotted black;"></td>
</tr>

</table>

';

		



//echo $footer_logo_html; exit;
// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
//$pdf->writeHTML($footertext, false, true, false, true);

//$footer_logo_html='Hello';

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.



$filelocation = $_SERVER['DOCUMENT_ROOT'].'image_bank/testronixpdf';
$fileNL =$filelocation."/".str_replace('-','','hello').".pdf"; //Linux
$pdf->Output($fileNL,'I');







//============================================================+
// END OF FILE
//============================================================+
