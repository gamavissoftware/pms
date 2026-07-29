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

<table width="100%">
    <tr>
        <td width="80%" style="text-align:left;"><h2>PERFORMA INVOICE</h2></td>
        <td width="20%">
            <img src="https://www.hongyijig.com/wp-content/uploads/2021/01/HongyiJig-main-logo.png" alt="logo" width="200px">
        </td>
    </tr>
</table>
<div></div>
<table width="100%" border="1" ruled="all"  style=" padding: 3px; text-align: center;;font-size: 10px;">
    <tr>
        <th width="5%" style="background-color:lightgrey;"><b>S.No.</b></th>
        <th width="20%" style="background-color:lightgrey;"><b>Description</b></th>
        <th width="8%" style="background-color:lightgrey;"><b>Total Amount</b></th>
        <th width="30%" style="padding: 0; background-color:lightgrey;"><div style="margin-bottom:10px;"><b>Performa Invoice</b></div>
            <table width="100%" style=" padding: 3px; text-align: center;font-size: 10px;" border="1" ruled="all">
                <tr>
                    <th width="60%"><b>Date | PI Number</b></th>
                    <th width="40%"><b>Amount USD</b></th>
                </tr>
            </table>
        </th>
        <th width="30%" style="padding: 0; background-color:lightgrey;"><div style="margin-bottom:0px;"><b>Amount Received</b></div>
            <table width="100%" style=" padding: 3px; text-align: center;;font-size: 10px;" border="1" ruled="all">
                <tr>
                    <th width="40%"><b>Date</b></th>
                    <th width="30%"><b>INR</b></th>
                    <th width="30%"><b>Balance</b></th>
                </tr>
            </table>
        </th>
        <th width="7%" style="background-color:lightgrey;"><b>Balance</b></th>
    </tr>
    <tr>
                <td>1</td>
                <td>Link to Quoatation | Sales Agreement</td>
                <td>8000</td>
                <td style="padding: 0;"><b></b>
                    <table border="1" ruled="all" width="100%" style=" padding: 3px; text-align: center;;font-size: 10px;">
                        <tr>
                            <td width="60%">26 March, 2023 | HJIG-01-0345</td>
                            <td width="40%">1800 + Old Balance</td>
                        </tr>
                        <tr>
                            <td >26 March, 2023 | HJIG-01-0345</td>
                            <td >1800 + Old Balance</td>
                        </tr>
                    </table>
                </td>
                <td style="padding: 0;"><b></b>
                    <table border="1" ruled="all" width="100%" style=" padding: 3px; text-align: center;;font-size: 10px;">
                        <tr>
                            <td width="40%">29 March, 2023</td>
                            <td width="30%">140,000.00</td>
                            <td width="30%">$1,707</td>
                        </tr>
                        <tr>
                            <td>29 March, 2023</td>
                            <td>140,000.00</td>
                            <td>$1,707</td>
                        </tr>
                    </table>
                </td>
                <td>$1800</td>
            </tr>
            <tr>
            <td>2</td>
            <td>Link to Quoatation | Sales Agreement</td>
            <td>8000</td>
            <td style="padding: 0;"><b></b>
                <table border="1" ruled="all" width="100%" style=" padding: 3px; text-align: center;;font-size: 10px;">
                    <tr>
                        <td width="60%">26 March, 2023 | HJIG-01-0345</td>
                        <td width="40%">1800 + Old Balance</td>
                    </tr>
                 
                </table>
            </td>
            <td style="padding: 0;"><b></b>
                <table border="1" ruled="all" width="100%" style=" padding: 3px; text-align: center;;font-size: 10px;">
                    <tr>
                        <td width="40%">29 March, 2023</td>
                        <td width="30%">140,000.00</td>
                        <td width="30%">$1,707</td>
                    </tr>
                </table>
            </td>
            <td>$1800</td>
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
