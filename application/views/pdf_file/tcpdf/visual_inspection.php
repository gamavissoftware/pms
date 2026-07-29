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


// set text shadow effect
$pdf->setTextShadow(array('enabled' => false, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

// Set some content to print

/** quert paet **/

$html = '

<table width="100%" border="1" ruled="all" style="padding:3px;">
    <tr>
        <td style="background-color:whitesmoke;">Order number </td>
        <td></td>
        <td style="background-color:whitesmoke;">Part name</td>
        <td></td>
        <td style="background-color:whitesmoke;">Date of trial</td>
        <td></td>
    </tr>
    <tr>
        <td style="background-color:whitesmoke;">Mould No </td>
        <td></td>
        <td style="background-color:whitesmoke;">PPT file link </td>
        <td></td>
        <td style="background-color:whitesmoke;">Trial Number</td>
        <td></td>
    </tr>
    <tr>
        <td style="background-color:whitesmoke;">Part No.</td>
        <td></td>
        <td style="background-color:whitesmoke;">Feedback Date</td>
        <td></td>
        <td style="background-color:whitesmoke;">Checked by </td>
        <td></td>
    </tr>
    <tr>
        <td style="background-color:whitesmoke;">No. of Obeservations</td>
        <td></td>
        <td style="background-color:whitesmoke;">Trial Stage </td>
        <td></td>
        <td style="background-color:whitesmoke;">Approved by </td>
        <td></td>
    </tr>
</table>

<br>
<br>


<table width="100%" border="1" ruled="all" style="padding:3px;">
<tr>
<td style="text-align:center; background-color:whitesmoke;" colspan="2" width="44%">TRIAL</td>
<td style="text-align:center; background-color:whitesmoke;" colspan="2" width="14%">T0</td>
<td style="text-align:center; background-color:whitesmoke;" colspan="2" width="14%">T1</td>
<td style="text-align:center; background-color:whitesmoke;" colspan="2" width="14%">T2</td>
<td style="text-align:center; background-color:whitesmoke;" colspan="2" width="14%">T3</td>
</tr>
    <tr>
        <td width="6%" style="text-align:center; background-color:whitesmoke;">Sr. No.</td>
        <td width="38%" style="text-align:center; background-color:whitesmoke;">Check points </td>
        <td width="6%" style="text-align:center; background-color:whitesmoke;">OK/NG</td>
        <td width="8%" style="text-align:center; background-color:whitesmoke;">Slide No.</td>
        <td width="6%" style="text-align:center; background-color:whitesmoke;">OK/NG</td>
        <td width="8%" style="text-align:center; background-color:whitesmoke;">Slide No.</td>
        <td width="6%" style="text-align:center; background-color:whitesmoke;">OK/NG</td>
        <td width="8%" style="text-align:center; background-color:whitesmoke;">Slide No.</td>
        <td width="6%" style="text-align:center; background-color:whitesmoke;">OK/NG</td>
        <td width="8%" style="text-align:center; background-color:whitesmoke;">Slide No.</td>
    </tr>
    <tr>
        <td>1</td>
        <td>Parting line flashes 			
        </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td>2</td>
        <td>Warpage 			
        </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
    <td>3</td>
    <td>Gate mark			
    </td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
</tr>
<tr>
<td>4</td>
<td>Sink marks 			
</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td>5</td>
<td>Flash at locking area and two lock are block 			
</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td>6</td>
<td>Flow marks 			
</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td>7</td>
<td>Runner weight 			
</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td>8</td>
<td>Part weight compared to design weight 			
</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td>9</td>
<td>Part material used authenticity 			
</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td>10</td>
<td>Stretch marks 			
</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td>11</td>
<td>Ejector marks i.e Ejector pin/blade or stress on the side walls due to stripper 			
</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td>12</td>
<td>Short fill vs flashes 			
</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td>13</td>
<td>Surface finish 			
</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td>14</td>
<td>Fitment of mating parts/Assembly 			
</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td>15</td>
<td>Fitment of BOP 			
</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
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



$filelocation = $_SERVER['DOCUMENT_ROOT'] . '/image_bank/popdf';
$fileNL = $filelocation . "/akash.pdf"; //Linux

$pdf->Output($fileNL, 'I');



//============================================================+
// END OF FILE
//============================================================+
