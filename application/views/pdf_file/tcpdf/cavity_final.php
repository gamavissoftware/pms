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

<table width="100%" style="padding:5px;">
<tr>

<td style="text-align:center;" ><img src="https://www.hongyijig.com/wp-content/uploads/2021/01/HongyiJig-main-logo.png" style="width:150px;"></td>
</tr>
</table>

<table width="100%" style="padding:3px; font-size:10px; text-align:center;" border="1" ruled="all">
<tr>
<td rowspan="2" style="vertical-align:middle;">Method</td>
<td>CMM</td>
<td>Digital Callper</td>
<td>Veriner Callper</td>
<td>Micrometer</td>
<td>Block Gage</td>
<td>Pin Gage</td>
<td>Radius Guage</td>
<td>Projector</td>
<td>Height Guage</td>
<td>Eye View</td>
<td>Depth Gage</td>
<td>Dial Indicator</td>
</tr>
<tr>
                    <td>CMM</td>
                    <td>DC</td>
                    <td>VC</td>
                    <td>M</td>
                    <td>BG</td>
                    <td>PG</td>
                    <td>RG</td>
                    <td>PJ</td>
                    <td>HG</td>
                    <td>V</td>
                    <td>DG</td>
                    <td>DI</td>
                  </tr>
</table>

<br><br>

<table width="100%" >
<tr>
<td width="50%">
<table style="padding:3px; font-size:10px;" width="100%" border="1" ruled="all">
            <tr>
                                    <td style="text-align:right; background-color:whitesmoke;">Order Number</td>
                                    <td></td>
                                    </tr>
                                    <tr>
                                    <td style="text-align:right; background-color:whitesmoke;">Mould Number</td>
                                    <td></td>
                                    </tr>
                                    <tr>
                                    <td style="text-align:right; background-color:whitesmoke;">Part Number/Name</td>
                                    <td> </td>
                                    </tr>
                                    <tr>
                                    <td style="text-align:right; background-color:whitesmoke;">Total Number of Dims</td>
                                    <td>
                                        
                                    </td>
                                    </tr>
                                    <tr>
                                    <td style="text-align:right; background-color:whitesmoke;">Total Number of Cavity</td>
                                    <td>
                                        
                                    </td>
                                    </tr>
</table>
</td>
<td width="50%">
<table style="padding:3px; font-size:10px;" width="100%" border="1" ruled="all">
            <tr>
                                    <td style="text-align:right; background-color:whitesmoke;">Design Part Weight</td>
                                    <td></td>
                                    </tr>
                                    <tr>
                                    <td style="text-align:right; background-color:whitesmoke;">Actual Part Weight</td>
                                    <td></td>
                                    </tr>
                                    <tr>
                                    <td style="text-align:right; background-color:whitesmoke;">Part Material	</td>
                                    <td> </td>
                                    </tr>
                                    <tr>
                                    <td style="text-align:right; background-color:whitesmoke;">TBalloon 2D Drawing Link	</td>
                                    <td>
                                        
                                    </td>
                                    </tr>
</table>
</td>
</tr>
</table>


<table width="100%" style="padding:3px; font-size:10px; text-align:center;" border="1" ruled="all">
                   <tr>
                                      <th colspan="19" style="background-color:whitesmoke;">MIC</th>
                  </tr>
                  <tr>
                    <th rowspan="2" style="background-color:whitesmoke;">Dim No.</th>
                    <th rowspan="2" style="background-color:whitesmoke;">Drg. Dim.</th>
                    <th colspan="4" style="background-color:whitesmoke;"></th>
                    <th colspan="2" style="background-color:whitesmoke;">Part Observation</th>
                    <th rowspan="2" style="background-color:whitesmoke;">TOOL</th>
                   <th colspan="2" style="background-color:whitesmoke;">Trial Samples Dimensions -T-0 (By Supplier)</th>
                    <th colspan="2" style="background-color:whitesmoke;">Trial Samples Dimensions -T-0 (By Hongyijig)</th>
                    <th colspan="2" style="background-color:whitesmoke;">Final Samples Dimensions -T-1 (By Supplier)</th>
                     <th colspan="2" style="background-color:whitesmoke;">Final Samples Dimensions -T-1 (By HJIG)</th>

                    <th rowspan="2" style="background-color:whitesmoke;">JUDGEMENT</th>
                    <th rowspan="2" style="background-color:whitesmoke;">Inspection<br>Process</th>
                  </tr>
                  <tr >
                    <th style="background-color:whitesmoke;" >Tol (+)</th>
                    <th style="background-color:whitesmoke;" >Tol (-)</th>
                    <th style="background-color:whitesmoke;" >Max</th>
                    <th style="background-color:whitesmoke;" >Min</th>
                    <th style="background-color:whitesmoke;" >MAX</th>
                    <th style="background-color:whitesmoke;" >MIN</th>
                   <th style="background-color:whitesmoke;" >CAV 1</th>
                   <th style="background-color:whitesmoke;" >CAV 2</th>
                   <th style="background-color:whitesmoke;" >CAV 1</th>
                   <th style="background-color:whitesmoke;" >CAV 2</th>
                   <th style="background-color:whitesmoke;" >CAV 1</th>
                   <th style="background-color:whitesmoke;" >CAV 2</th>
                   <th style="background-color:whitesmoke;" >CAV 1</th>
                   <th style="background-color:whitesmoke;" >CAV 2</th>                  
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
                    <td></td>
                                        <td>12</td>

                                        <td>21</td>

                    

                                         <td>12</td>

                                        <td>34</td>

                    

                                        <td>12</td>

                                        <td>13</td>

                    

                                        <td></td>

                                        <td></td>

                    
                    <td></td>
                    <td></td>
                   
                  </tr>

                

                </table>

                <br><br><br>

<table width="100%" >
<tr>
<td width="50%">
<table style="padding:3px; font-size:10px;" width="100%" border="1" ruled="all">
            <tr>
                                    <td style="text-align:right; background-color:whitesmoke;">Order Number</td>
                                    <td></td>
                                    </tr>
                                    <tr>
                                    <td style="text-align:right; background-color:whitesmoke;">Mould Number</td>
                                    <td></td>
                                    </tr>
                                    <tr>
                                    <td style="text-align:right; background-color:whitesmoke;">Part Number/Name</td>
                                    <td> </td>
                                    </tr>
                                    <tr>
                                    <td style="text-align:right; background-color:whitesmoke;">Total Number of Dims</td>
                                    <td>
                                        
                                    </td>
                                    </tr>
                                    <tr>
                                    <td style="text-align:right; background-color:whitesmoke;">Total Number of Cavity</td>
                                    <td>
                                        
                                    </td>
                                    </tr>
</table>
</td>
<td width="50%">
<table style="padding:3px; font-size:10px;" width="100%" border="1" ruled="all">
            <tr>
                                    <td style="text-align:right; background-color:whitesmoke;">Design Part Weight</td>
                                    <td></td>
                                    </tr>
                                    <tr>
                                    <td style="text-align:right; background-color:whitesmoke;">Actual Part Weight</td>
                                    <td></td>
                                    </tr>
                                    <tr>
                                    <td style="text-align:right; background-color:whitesmoke;">Part Material	</td>
                                    <td> </td>
                                    </tr>
                                    <tr>
                                    <td style="text-align:right; background-color:whitesmoke;">TBalloon 2D Drawing Link	</td>
                                    <td>
                                        
                                    </td>
                                    </tr>
</table>
</td>
</tr>
</table>
<table width="100%" style="padding:3px; font-size:10px; text-align:center;" border="1" ruled="all">
                  
                  <tr>
                    <th rowspan="2" style="background-color:whitesmoke;">Dim No.</th>
                    <th rowspan="2" style="background-color:whitesmoke;">Drg. Dim.</th>
                    <th colspan="4" style="background-color:whitesmoke;"></th>
                    <th colspan="2" style="background-color:whitesmoke;">Part Observation</th>
                    <th rowspan="2" style="background-color:whitesmoke;">TOOL</th>
                   <th colspan="3" style="background-color:whitesmoke;">Trial Samples Dimensions -T-0 (By Supplier)	</th>
                    <th colspan="3" style="background-color:whitesmoke;">Trial Samples Dimensions -T-0 (By Hongyijig)	</th>
                    <th colspan="3" style="background-color:whitesmoke;">Final Samples Dimensions -T-1 (By Supplier)	</th>
                     <th colspan="3" style="background-color:whitesmoke;">Final Samples Dimensions -T-1 (By HJIG)	</th>

                    <th rowspan="2" style="background-color:whitesmoke;">JUDGEMENT</th>
                    <th rowspan="2" style="background-color:whitesmoke;">Inspection<br>Process</th>
                  </tr>
                  <tr >
                    <th style="background-color:whitesmoke;" >Tol (+)</th>
                    <th style="background-color:whitesmoke;" >Tol (-)</th>
                    <th style="background-color:whitesmoke;" >Max</th>
                    <th style="background-color:whitesmoke;" >Min</th>
                    <th style="background-color:whitesmoke;" >MAX</th>
                    <th style="background-color:whitesmoke;" >MIN</th>
                   <th style="background-color:whitesmoke;" >CAV 1</th>
                   <th style="background-color:whitesmoke;" >CAV 2</th>
                   <th style="background-color:whitesmoke;" >CAV 3</th>
                   <th style="background-color:whitesmoke;" >CAV 1</th>
                   <th style="background-color:whitesmoke;" >CAV 2</th>
                   <th style="background-color:whitesmoke;" >CAV 3</th>
                   <th style="background-color:whitesmoke;" >CAV 1</th>
                   <th style="background-color:whitesmoke;" >CAV 2</th>
                   <th style="background-color:whitesmoke;" >CAV 3</th>
                   <th style="background-color:whitesmoke;" >CAV 1</th>
                   <th style="background-color:whitesmoke;" >CAV 2</th>    
                   <th style="background-color:whitesmoke;" >CAV 3</th>              
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
                    <td></td>
                                        <td>12</td>

                                        <td>21</td>

                    

                                         <td>12</td>

                                        <td>34</td>

                    

                                        <td>12</td>

                                        <td>13</td>

                    

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
                <p></p>
               <p></p>
               <p></p>
               <p></p>
                <table width="100%" >
<tr>
<td width="40%">
<table style="padding:3px; font-size:10px;" width="100%" border="1" ruled="all">
            <tr>
                                    <td style=" background-color:whitesmoke;">Date of Test	</td>
                                    <td></td>
                                    </tr>
                                    <tr>
                                    <td style="background-color:whitesmoke;">Check Date	</td>
                                    <td></td>
                                    </tr>
                                    <tr>
                                    <td style="background-color:whitesmoke;">Checked Date	</td>
                                    <td> </td>
                                    </tr>
                                    <tr>
                                    <td style=" background-color:whitesmoke;">Approved By	</td>
                                    <td>
                                        
                                    </td>
                                    </tr>
                                   
</table>
</td>
<td width="10%"></td>
<td width="10%" style="padding:3px; font-size:10px;">Remarks</td>
<td width="40%" style="height:150px; border:1px solid black; padding:3px; font-size:10px;">

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
