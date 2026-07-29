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
<td width="20%"></td>
<td width="60%" style="text-align:center;"><h2>Process Parameter Sheet (Molding)</h2></td>
<td width="20%">
<img src="https://www.hongyijig.com/wp-content/uploads/2021/01/HongyiJig-main-logo.png" alt="logo" width="200px">
</td>
</tr>
</table>

<h4>PROCESS CONTROL SHEET (INJECTION MOLDING)</h4>
<table>
<tr>
<td width="40%">
<table border="1" style="width:100%; padding:3px;">
<tr>
    <td style="background-color:whitesmoke;">Machine Ton</td>
    <td></td>
</tr>
<tr>
    <td style="background-color:whitesmoke;">CUSTOMER</td>
    <td></td>
</tr>
<tr>
    <td style="background-color:whitesmoke;">RAW MATERIAL</td>
    <td></td>
</tr>
<tr>
    <td style="background-color:whitesmoke;">MAT. GRADE</td>
    <td></td>
</tr>
<tr>
    <td style="background-color:whitesmoke;">PREHEATING TEMP./TIME</td>
    <td></td>
</tr>
<tr>
    <td style="background-color:whitesmoke;">MB % & COLOUR</td>
    <td></td>
</tr>
</table>
</td>
<td width="20%"></td>
<td width="40%">

<table border="1" style="width:100%; padding:3px; ">
                                <tr>
                                    <td style="background-color:whitesmoke;">SHOT WT. (GMS)</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="background-color:whitesmoke;">SHOT WT. (GMS)</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="background-color:whitesmoke;">RUNNER WT. (GMS)</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="background-color:whitesmoke;">PART WT.(GMS)</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="background-color:whitesmoke;">NO. OF CAVITY</td>
                                    <td></td>
                                </tr>
                            </table>
</td>
</tr>
</table>
<br><br>
<h4>TEMPERATURES</h4>

<table border="1" style="width:100%; padding:3px;">
<tr>
    <th width="15%" style="text-align:center; background-color:whitesmoke;">&#8451;</th>
    <th  width="10%" style="text-align:center; background-color:whitesmoke;">NH</th>
    <th  width="10%" style="text-align:center; background-color:whitesmoke;">Z1</th>
    <th  width="10%" style="text-align:center; background-color:whitesmoke;">Z2</th>
    <th  width="10%" style="text-align:center; background-color:whitesmoke;">Z3</th>
    <th  width="10%" style="text-align:center; background-color:whitesmoke;">Z4</th>
    <th  width="10%" style="text-align:center; background-color:whitesmoke;">Z5</th>
    <th  colspan="5" width="25%" style="text-align:center; background-color:whitesmoke;">HRTC</th>
</tr>
<tr>
    <td style="text-align:center;">SET <b>&#8451;</b></td>
    <td style="text-align:center;"></td>
    <td style="text-align:center;"></td>
    <td style="text-align:center;"></td>
    <td style="text-align:center;"></td>
    <td style="text-align:center;"></td>
    <td style="text-align:center;"></td>
    <td style="text-align:center;"></td>
    <td style="text-align:center;"></td>
    <td style="text-align:center;"></td>
    <td style="text-align:center;"></td>
    <td style="text-align:center;"></td>
</tr>
</table>

<br><br>


<table>
<tr>
<td width="50%">
<h4>INJECTION</h4>
<table border="1" style="width:100%; padding:3px;">
<tr>
<td width="40%" style="background-color:whitesmoke;">POSITION (MM)</td>
<td width="20%"></td>
<td width="20%"></td>
<td width="20%"></td>
</tr>
<tr>
<td style="background-color:whitesmoke;">SPEED (MM/SEC.)
</td>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td style="background-color:whitesmoke;">PRESSURE (BAR)</td>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td style="background-color:whitesmoke;">PACK / HOLD PRS.(BAR)</td>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td style="background-color:whitesmoke;">PACK / HOLD SPD.(MM/SEC)</td>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td style="background-color:whitesmoke;">PACK / HOLD TIME(Sec)</td>
<td></td>
<td></td>
<td></td>
</tr>
</table>
</td>
<td width="10%"></td>
<td width="40%">
<h4>REFILL</h4>
<table border="1" style="width:100%; padding:3px; ">
<tr>
<td width="60%" style="background-color:whitesmoke;">STEP</td>
<td  width="40%"></td>
</tr>
<tr>
<td   style="background-color:whitesmoke;">PRESSURE (BAR)</td>
<td></td>
</tr>
<tr>
<td   style="background-color:whitesmoke;">SPEED (MM/SEC.) / RPM</td>
<td></td>
</tr>
<tr>
<td   style="background-color:whitesmoke;">BACK PRESSURE (BAR)</td>
<td></td>
</tr>
<tr>
<td   style="background-color:whitesmoke;">POSITION (mm)</td>
<td></td>
</tr>
                            </table>
</td>
</tr>
</table>
<br><br>
<br><br>
<h4>EJECTOR</h4>

<table border="1" style="width:100%; padding:3px; ">
<tr>
                                    <td width="23%" style="background-color:whitesmoke;"></td>
                                    <td colspan="5" width="35%" style="background-color:whitesmoke; text-align:center;">FORWARD</td>
                                    <td width="7%" style="background-color:whitesmoke;"></td>
                                    <td colspan="5"  width="35%" style="background-color:whitesmoke; text-align:center;">RETRACT</td>
                                </tr>
                                <tr>
                                    <td width="23%">SPEED (MM/SEC.)</td>
                                    <td width="7%"></td>
                                    <td width="7%"></td>
                                    <td width="7%"></td>
                                    <td width="7%"></td>
                                    <td width="7%"></td>
                                    <td width="7%"></td>
                                    <td width="7%"></td>
                                    <td width="7%"></td>
                                    <td width="7%"></td>
                                    <td width="7%"></td>
                                    <td width="7%"></td>
                                </tr>
                                <tr>
                                    <td>PRESSURE (BAR)</td>
                                    <td ></td>
                                    <td ></td>
                                    <td ></td>
                                    <td ></td>
                                    <td ></td>
                                    <td ></td>
                                    <td ></td>
                                    <td ></td>
                                    <td ></td>
                                    <td ></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>POSITION (MM)</td>
                                    <td ></td>
                                    <td ></td>
                                    <td ></td>
                                    <td ></td>
                                    <td ></td>
                                    <td ></td>
                                    <td ></td>
                                    <td ></td>
                                    <td ></td>
                                    <td ></td>
                                    <td></td>
                                </tr>
</table>

<br><br>

<table width="100%" >
<tr>
<td width="40%">
<h4>GENERAL</h4>
<table border="1" style="width:100%; padding:3px; ">
                                <tr>
                                    <td width="60%" style="background-color:whitesmoke;">INJECTION TIME (SEC.)</td>
                                    <td width="40%"></td>
                                </tr>
                                <tr>
                                    <td style="background-color:whitesmoke;">ROT. TIME DELAY (SEC.) </td>
                                    <td><input type="text"></td>
                                </tr>
                                <tr>
                                    <td style="background-color:whitesmoke;">COOLING TIME (SEC.)</td>
                                    <td><input type="text"></td>
                                </tr>
                                <tr>
                                    <td style="background-color:whitesmoke;">CYCLE TIME (SEC.)</td>
                                    <td><input type="text"></td>
                                </tr>
                                <tr>
                                    <td style="background-color:whitesmoke;">TARGET/HOUR</td>
                                    <td><input type="text"></td>
                                </tr>
                                <tr>
                                    <td style="background-color:whitesmoke;">TEMP.CTRL FIX HALF</td>
                                    <td><input type="text"></td>
                                </tr>
                                <tr>
                                    <td style="background-color:whitesmoke;">TEMP. CTRL MOV. HALF</td>
                                    <td><input type="text"></td>
                                </tr>
                                <tr>
                                    <td style="background-color:whitesmoke;">MTC / MOULD TEMP.</td>
                                    <td><input type="text"></td>
                                </tr>
</table>
</td>
<td width="5%"></td>
<td width="55%">
<h4 style="text-align:center;">TIMER</h4>
<table border="1" style="width:100%; padding:3px; ">
<tr>
                                    <td width="40%" style="background-color:whitesmoke;">TRANSFER POSITION (MM)</td>
                                    <td width="10%" style="text-align:center;"></td>
                                    <td width="10%" >TIMER</td>
                                    <td width="10%" >TIME</td>
                                    <td width="10%" >DELAY</td>
                                    <td width="10%" >TIMER</td>
                                    <td width="10%" >TIME</td>
                                </tr>
                                <tr>
                                    <td style="background-color:whitesmoke;">SHOT SIZE (MM)</td>
                                    <td style="text-align:center;"></td>
                                    <td style="text-align:center;"></td>
                                    <td style="text-align:center;"></td>
                                    <td style="text-align:center;"></td>
                                    <td style="text-align:center;"></td>
                                    <td style="text-align:center;"></td>
                                </tr>
                                <tr>
                                    <td style="background-color:whitesmoke;">SUCK BACK (MM)</td>
                                    <td style="text-align:center;"></td>
                                    <td style="text-align:center;"></td>
                                    <td style="text-align:center;"></td>
                                    <td style="text-align:center;"></td>
                                    <td style="text-align:center;"></td>
                                    <td style="text-align:center;"></td>
                                </tr>
                                <tr>
                                    <td style="background-color:whitesmoke;">MOULD OPEN END (MM)</td>
                                    <td style="text-align:center;"></td>
                                </tr>
                                <tr>
                                    <td style="background-color:whitesmoke;">EJECT COUNT (NOS)</td>
                                    <td style="text-align:center;"></td>
                                </tr>
                                <tr>
                                    <td style="background-color:whitesmoke;">EJECTOR FOR TIME</td>
                                    <td style="text-align:center;"></td>
                                </tr>
                                <tr>
                                    <td style="background-color:whitesmoke;">CLAMPING FORCE (TONS)</td>
                                    <td style="text-align:center;"></td>
                                </tr>
                                <tr>
                                    <td style="background-color:whitesmoke;">CUSHION (MM)</td>
                                    <td style="text-align:center;"></td>
                                </tr>
</table>
</td>
</tr>
</table>

<br><br>
<table width="100%" border="1" >
<tr>
<td width="30%">
<h4>Ejection Side</h4>
<table border="1" style="width:100%; padding:3px; ">
                                <tr>
                                    <td width="75%"></td>
                                    <td width="25%" style="text-align:center;">YES/NO</td>
                                </tr>
                                <tr>
                                    <td width="75%" style="background-color:whitesmoke;">There are springs for ensuring the ejector plate back</td>
                                    <td width="25%" style="text-align:center;"> </td>
                                </tr>
                                <tr>
                                    <td style="background-color:whitesmoke;">Ejection stroke is indicated with stopper (Limit /Switch)</td>
                                    <td style="text-align:center;"></td>
                                </tr>
</table>
</td>
<td width="5%"></td>
<td width="30%">
<h4>Hydraulic Sliders</h4>
<table border="1" style="width:100%; padding:3px; ">
                                <tr>
                                    <td width="75%"></td>
                                    <td width="25%" style="text-align:center;">YES/NO</td>
                                </tr>
                                <tr>
                                    <td width="75%" style="background-color:whitesmoke;">Marking position input and output</td>
                                    <td width="25%" style="text-align:center;"> </td>
                                </tr>
                                <tr>
                                    <td style="background-color:whitesmoke;">Hydraulic connections at opposite side of the operator</td>
                                    <td style="text-align:center;"></td>
                                </tr>
                                <tr>
                                    <td style="background-color:whitesmoke;">Make sure there is no oil leakage at 160 bar pressure</td>
                                    <td style="text-align:center;"></td>
                                </tr>
                                <tr>
                                    <td style="background-color:whitesmoke;">Hydraulic cyclinder with no rotation movement with sliders element</td>
                                    <td style="text-align:center;"> </td>
                                </tr>
</table>
</td>
<td width="5%"></td>
<td width="30%">
<h4>Cooling</h4>
<table border="1" style="width:100%; padding:3px; ">
                                <tr>
                                    <td width="75%"></td>
                                    <td width="25%" style="text-align:center;">YES/NO</td>
                                </tr>
                                <tr>
                                    <td width="75%" style="background-color:whitesmoke;">Cooling connection should be opposite to the operator side</td>
                                    <td width="25%" style="text-align:center;"> </td>
                                </tr>
                                <tr>
                                    <td style="background-color:whitesmoke;">Cooling Nipples Size must be 3/4 Push Pin Plug.</td>
                                    <td style="text-align:center;"></td>
                                </tr>
</table>
</td>
</tr>
<table>
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
