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

$html .= '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <p style="font-size: 18px;"><b>Lead Detail</b></p>
    <hr>
<table >
    <tr>
        <td><table style="width:100%; padding: 3px; text-align: left;" border="1">
            <tr>
                <td><b>Query No.</b></td>
                <td>REF2614</td>
            </tr>
            <tr>
                <td><b>Project Name</b></td>
                <td>Air Cooler</td>
            </tr>
            <tr>
                <td><b>Client Name</b></td>
                <td>Supriya Anand</td>
            </tr>
        </table>
    </td>
        <td>
            <table style="width:100%; padding: 3px; text-align: left;" border="1">
                <tr>
                    <td><b>Order Punching Date</b></td>
                    <td>01-01-1997 05:20:00</td>
                </tr>
                <tr>
                    <td><b>No. of Mould</b></td>
                    <td>3</td>
                </tr>
        </table>
    </td>
    </tr>
</table>
<br>
<br>
<table style="width:100%; padding: 3px; text-align: center;" border="1">
    <tr>
        <td><b>S.NO.</b></td>
        <td><b>STAGE NAME AND LEVEL</b></td>
        <td><b></b></td>
        <td><b>ACCOUNTABILITY</b></td>
        <td><b>START DATE</b></td>
        <td><b>END DATE</b></td>
        <td><b>TOTAL DAYS</b></td>
        <td><b>REMARKS</b></td>
    </tr>

    <tr>
        <td>B.O</td>
        <td>CLIENT MEETING</td>
        <td>STAGE-1</td>
        <td>BDM</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
</table>';

$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.



$filelocation = $_SERVER['DOCUMENT_ROOT'].'/image_bank/popdf';
$fileNL = $filelocation."/akash.pdf"; //Linux

$pdf->Output($fileNL, 'I');