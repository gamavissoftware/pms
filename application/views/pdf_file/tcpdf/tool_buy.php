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

<table width="100%" style="padding:3px;">
<tr>
<td>
<h2 style="text-align:center;">Tool Buy Off</h2>
</td>
</tr>
</table>
<p></p>
<table width="100%" border="1" ruled="all" style="padding:3px;">
<tr>
<td width="45%"><b>TOOLBUY OFF CHECK LIST</b></td>
<td width="55%" style="text-align: right;"><b>(Before Trial)</b></td>
</tr>
</table>
<br>
<br>
<table width="100%" border="1" ruled="all" style="padding:3px;">
                                <tbody><tr>
                                    <td width="45%"><b>Standard Check List</b> <span>*</span></td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="5%">YES</td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="5%">NO</td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="5%">NA</td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="20%">Upload Evidence Pictures</td>
                                </tr>

                                <tr>
                                    <td>Check if the Final Mould Name Plate of "Hongyi JIG" Standard mounted on the mould.</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>General 2D Drawing of hot runner diagram shout be as along with name plate</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>3D mold file which includes the Hot Runner 3D</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Warrenty card in case of if Hot runner brand is Mold-Master, Yudo or HRS</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody></table>
<p></p>
                            <table width="100%" border="1" ruled="all" style="padding:3px;">
                                <tbody><tr>
                                    <td width="45%"><b>Parting Area</b> <span>*</span></td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="5%">YES</td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="5%">NO</td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="5%">NA</td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="20%">Upload Evidence Pictures</td>
                                </tr>

                                <tr>
                                    <td>Venting all around the part</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Parting area should be relived after suitable distance</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody></table>

                            <p></p>
                            <table width="100%" border="1" ruled="all" style="padding:3px;">
                                <tbody><tr>
                                    <td width="45%"><b>Part Approval</b> <span>*</span></td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="5%">YES</td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="5%">NO</td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="5%">NA</td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="20%">Upload Evidence Pictures</td>
                                </tr>

                                <tr>
                                    <td>Have you Get the final part approval from client, should be equalant to production sample &amp; moulding defects free in originol CFM</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Have you Check the client agreement copy and varify all the points are metting or not</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Have you Get the final part approval from client, should be equalant to production sample &amp; moulding defects free in originol CFM</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Have you Check the client agreement copy and varify all the points are metting or not</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody></table>
                            <p></p>
                            <p></p>
                            <table width="100%" border="1" ruled="all" style="padding:3px;">
                                <tbody><tr>
                                    <td width="45%"><b>Safety Handling</b> <span>*</span></td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="5%">YES</td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="5%">NO</td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="5%">NA</td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="20%">Upload Evidence Pictures</td>
                                </tr>

                                <tr>
                                    <td>4 support Ø 80 mm to safe the mould from rust</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Hook arrnagement for individual plate lifting (i.e. Core side &amp; CavitySide)</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Paint on mould required as per Hongyi JIG STD (Top &amp; Bottom Orange/ All other black)</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>There are insulation plates for molds with a temperature above 90 º C</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Locking plate Core &amp; cavity half locking and Dust cover on ejector assembly should be available.</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Dust cover on ejector assembly should be available.</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Shot Counter should be availabe for counting the production Parts.</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody></table>
                            <p></p>
                            <table width="100%" border="1" ruled="all" style="padding:3px;">
                                <tbody><tr>
                                    <td width="45%"><b>Sliders, Internal Slider &amp; Lifters</b> <span>*</span></td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="5%">YES</td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="5%">NO</td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="5%">NA</td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="20%">Upload Evidence Pictures</td>
                                </tr>

                                <tr>
                                    <td>Slider stoppers should be in the mould</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Row 2Bushings and "T" guide , wear plate should be Nitrided</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Nitriding of the friction faces slider</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody></table>
                            <p></p>
                            <table width="100%" border="1" ruled="all" style="padding:3px;">
                                <tbody><tr>
                                    <td width="45%"><b>Surface Quality</b> <span>*</span></td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="5%">YES</td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="5%">NO</td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="5%">NA</td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="20%">Upload Evidence Pictures</td>
                                </tr>

                                <tr>
                                    <td>Check the Surface quality incase of High Mirror Polish</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>In case of Texture, check if the texture is as per. the requirement</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Check the critical areas needed polish to release from mould are. okay</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody></table>
                            <p></p>
                            <p></p>
                            <p></p>
                            <table width="100%" border="1" ruled="all" style="padding:3px;">
                                <tbody><tr>
                                    <td width="45%"><b>Maintenance &amp; Spare Parts</b></td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="5%">YES</td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="5%">NO</td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="5%">NA</td>
                                    <td width="5%"></td>
                                    <td class="text-center" width="20%">Upload Evidence Pictures</td>
                                </tr>

                                <tr>
                                    <td>One Set of Springs a spares of the tools</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>One set of Thermocouple in. case of Hot runner</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>One set of Heaters in case of Hot Runner</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Mould Lifting I-Bolts 2pcs each mould</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody></table>
                            <p></p>
                            <table width="100%" border="1" ruled="all" style="padding:3px;">
                                <tbody><tr>
                                    <td width="45%"><b>Mould Size</b></td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="5%">YES</td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="5%">NO</td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="5%">NA</td>
                                    <td width="5%"></td>
                                    <td style="text-align:center;" width="20%">Upload Evidence Pictures</td>
                                </tr>

                                <tr>
                                    <td>Length</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Width</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Height</td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td style="text-align:center;"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody></table>
                            <p></p>
                            <table width="100%" border="1" ruled="all" style="padding:3px;">
                                <tbody><tr>
                                    <td width="45%"><b>Mold Weight</b></td>
                                    <td width="55%"></td>
                                </tr>
                            </tbody></table>
                            <p></p>
                            <table width="100%" border="1" ruled="all" style="padding:3px;">
                                <tbody><tr>
                                    <td width="45%"><b>Tool Lifting Approval</b> <span>*</span></td>
                                    <td width="55%"></td>
                                </tr>
                            </tbody></table>
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
