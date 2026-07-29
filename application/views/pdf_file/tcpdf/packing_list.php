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

<table width="100%" style="padding:3px;" border="1">
    <tr>
        <td style="text-align:center; font-size:25px;">PACKING LIST</td>
    </tr>
</table>

<br>
<br>
<table width="100%">
    <tr>
        <td width="45%"></td>
        <td width="10%"></td>
        <td width="45%">
            <table width="100%" style="padding:3px;" border="1" ruled="all">
                <tr>
                      <td width="50%"><b>Date</b></td>
                      <td width="50%"></td>
                </tr>
                <tr>
                      <td><b>Commerical Invoice Number</b></td>
                      <td></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<br>
<br>
<table width="100%">
    <tr>
        <td width="45%">
            <table width="100%" style="padding:3px;" border="1" ruled="all">
                <tr>
                    <td colspan="2" style="text-align:center; background-color:whitesmoke;"><b>SHIPPER</b></td>
                </tr>
                <tr>
                    <td style="background-color:whitesmoke;">Company Name</td>
                    <td></td>
                </tr>
                <tr>
                    <td style="background-color:whitesmoke;">Company Address</td>
                    <td></td>
                </tr>
                <tr>
                    <td style="background-color:whitesmoke;">Company Person Name</td>
                    <td></td>
                </tr>
                <tr>
                    <td style="background-color:whitesmoke;">Company Person No.</td>
                    <td></td>
                </tr>
                <tr>
                    <td style="background-color:whitesmoke;">Email Id</td>
                    <td></td>
                </tr>
            </table>
        </td>
        <td width="10%"></td>
        <td width="45%">
            <table width="100%" style="padding:3px;" border="1" ruled="all">
                <tr>
                    <td colspan="2" style="text-align:center; background-color:whitesmoke;"><b>CONSIGNEE</b></td>
                </tr>
                <tr>
                    <td style="background-color:whitesmoke;">Company Name</td>
                    <td></td>
                </tr>
                <tr>
                    <td style="background-color:whitesmoke;">Company Address</td>
                    <td></td>
                </tr>
                <tr>
                    <td style="background-color:whitesmoke;">Company Person Name</td>
                    <td></td>
                </tr>
                <tr>
                    <td style="background-color:whitesmoke;">Company Person No.</td>
                    <td></td>
                </tr>
                <tr>
                    <td style="background-color:whitesmoke;">Email Id</td>
                    <td></td>
                </tr>
                <tr>
                    <td style="background-color:whitesmoke;">IGST No.</td>
                    <td></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<br>
<br>
<table width="100%" style="padding:3px; text-align:center;" border="1" ruled="all">
    <tr>
        <th width="5%" style="background-color:whitesmoke;">S.NO.</th>
        <th width="26%" style="background-color:whitesmoke;">DESCRIPTION</th>
        <th width="10%" style="background-color:whitesmoke;">QTY</th>
        <th width="10%" style="background-color:whitesmoke;">NO. OF BOX</th>
        <th width="12%" style="background-color:whitesmoke;">NET WEIGTH (KG)</th>
        <th width="12%" style="background-color:whitesmoke;">GROSS WEIGHT (KG)</th>
        <th  style="background-color:whitesmoke;">BOX SIZE (MM)</th>
        <th style="background-color:whitesmoke;">CBM</th>
    </tr>
    <tr>
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
        <td colspan="2" style="text-align: right;">Total</td>
        <td><input type="text"></td>
        <td><input type="text"></td>
        <td><input type="text"></td>
        <td><input type="text"></td>
        <td><input type="text"></td>
        <td><input type="text"></td>
    </tr>
</table>
<br>
<br>
<table width="100%" >
    <tr>
        <td width="45%">
        <table width="100%" style="padding:3px;" border="1" ruled="all">
                <tr>
                      <td width="50%">SHIPMENT MODE</td>
                      <td width="50%"></td>
                </tr>
                <tr>
                      <td>SHIPMENT TERM</td>
                      <td></td>
                </tr>
            </table>
        </td>
        <td width="10%"></td>
        <td width="45%">
            <table width="100%" style="padding:3px;" border="1" ruled="all">
            <tr><td style="height: 150px; vertical-align: top;">
            SHIPPER STAMP AND SIGN
        </td></tr>
            </table>
        </td>
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
