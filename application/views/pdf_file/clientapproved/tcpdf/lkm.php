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

<table style=" font-size:7px; width:100%; padding:2px; border:1px solid #ff8800;">
<tr >
<th style=" text-align:center; width:60px; background-color:wheat"></th>
<th style=" text-align:center; width:60px; background-color:wheat"></th>
<th style=" text-align:center; width:120px; background-color:wheat">Comparable Standard </th>

<th style=" text-align:center; width:120px; background-color:wheat"></th>
<th style=" text-align:center; width:200px; background-color:wheat"></th>
<th style=" text-align:center; width:144x; background-color:wheat">Typical Analysis at Major Chemical Content </th>

<th style=" text-align:center; width:250px; background-color:wheat"></th>
</tr>


<tr >
<th style="border:1px solid #ff8800; text-align:center; width:60px; background-color:wheat; ">Manufacturer</th>
<th style="border:1px solid #ff8800; text-align:center; width:60px; background-color:wheat; ">Material Grade</th>
<th style="border:1px solid #ff8800; text-align:center; width:40px; background-color:wheat; ">AISI</th>
<th style="border:1px solid #ff8800; text-align:center; width:40px; background-color:wheat; ">JIS</th>
<th style="border:1px solid #ff8800; text-align:center; width:40px; background-color:wheat; ">W.Nr</th>
<th style="border:1px solid #ff8800; text-align:center; width:120px; background-color:wheat; ">Delivered Hardness from steel mill (Surface)</th>
<th style="border:1px solid #ff8800; text-align:center; width:200px; background-color:wheat; ">Characteristics</th>
<th style="border:1px solid #ff8800; text-align:center; width:18x; background-color:wheat; ">C</th>
<th style="border:1px solid #ff8800; text-align:center; width:18x; background-color:wheat; ">Si</th>
<th style="border:1px solid #ff8800; text-align:center; width:18x; background-color:wheat; ">Cr</th>
<th style="border:1px solid #ff8800; text-align:center; width:18x; background-color:wheat; ">Ni</th>
<th style="border:1px solid #ff8800; text-align:center; width:18x; background-color:wheat; ">Mn</th>
<th style="border:1px solid #ff8800; text-align:center; width:18x; background-color:wheat; ">Mo</th>
<th style="border:1px solid #ff8800; text-align:center; width:18x; background-color:wheat; ">V</th>
<th style="border:1px solid #ff8800; text-align:center; width:18x; background-color:wheat; ">W</th>
<th style="border:1px solid #ff8800; text-align:center; width:250px; background-color:wheat; ">Applications</th>
</tr>

<tr>
<td style="border:1px solid #ff0066; text-align:center; "></td>
<td colspan="15px" style="border:1px solid #ff0066; text-align:left; background-color:#ffcce0;">Aubert & Duval
</td>
</tr>

<tr>
<td style="border:1px solid #ff0066; text-align:center; "></td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">MEK4 
</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">-
</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">-
</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">1.8523
</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">Prehardened to HB350 - 400
</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">High hardness and toughness, good for nitriding to increase surface hardness
</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">0.4
</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">-
</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">0.3
</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">-
</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">-
</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">0.1
</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">0.2
</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">-
</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">Plastic injection mould and slider with complicated structure requiring high wear resistance. Nitriding can be applied after pilot run to 
improve the mould life; not suitable for moulding corrosive resins

</td>
</tr>




<tr>
<td style="border:1px solid #ff0066; text-align:center; "></td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">DC3

</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">H11 Modified High Purity Process
</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">SKD6 Modified High Purity Process
</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">1.2340 High Purity Process
</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">Annealed to HB235
</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">High purity, better toughness and thermal conductivity than common hot work steels, excellent 
resistance to heat checking, outstanding hardenability,
good dimensional stability during hardening process

</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">0.4
</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">-
</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">0.3
</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">-
</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">-
</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">0.1
</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">0.2
</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">-
</td>
<td  style="border:1px solid #ff0066; text-align:center; background-color:#ffe7cb;">Large size light-alloy die casting dies with long production run

</td>
</tr>



<tr>
<td style="border:1px solid #ff8800; text-align:center; "></td>
<td colspan="15px" style="border:1px solid #ff8800; text-align:left; background-color:#ff8800;">ASSAB Steel
</td>
</tr>


<tr>
<td style="border:1px solid #ff8800; text-align:center; "></td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">718H (IMPAX HI HARD)


</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">P20 Modified
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">-
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">1.2738 
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">Prehardened to HB330 - 380
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">Homogenous microstructure, stable quality, good texturing and EDM property 

</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">0.4
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">-
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">0.3
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">-
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">-
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">0.1
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">0.2
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">-
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">Plastic injection mould for medium production run and PS, PE, PP, ABS and other non-corrosive resins

</td>
</tr>







<tr>
<td style="border:1px solid #ff8800; text-align:center; "></td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">NIMAX 


</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;" colspan="3">Special Steel
</td>

<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">Prehardened to HB360 - 400
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">Good polishing and texturing properties. Superior EDM-ability and machinability. High toughness and 
good weldability  

</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">0.4
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">-
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">0.3
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">-
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">-
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">0.1
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">0.2
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">-
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">Plastic injection mould for various

</td>
</tr>

<tr>
<td style="border:1px solid #ff8800; text-align:center; "></td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">S136 (STAVAX ESR) 


</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;" rowspan="2">420 Modified ESR

</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;" rowspan="2">SUS420J2 Modified ESR
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;" rowspan="2">1.2083 Modified ESR

</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">Annealed to HB200

</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">High purity with fine microstructure, can be polished to mirror finish. Excellent corrosion resistance 
and low deformation during heat treatment  

</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;" rowspan="2">0.4
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;" rowspan="2">-
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;" rowspan="2">0.3
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;" rowspan="2">-
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;" rowspan="2">-
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;" rowspan="2">0.1
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;" rowspan="2">0.2
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;" rowspan="2">-
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">Plastic injection mould with small to medium size, long production run and high polishing requirement,
e.g. PMMA, PC, resins such as PVC, PA, POM or additives with corrosive property, machine part for food processing machinery


</td>
</tr>


<tr>
<td style="border:1px solid #ff8800; text-align:center; "></td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">718H (IMPAX HI HARD)


</td>

<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">Prehardened to HB330 - 380
</td>
<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">Homogenous microstructure, stable quality, good texturing and EDM property 

</td>

<td  style="border:1px solid #ff8800; text-align:center; background-color:#ffe7cb;">Plastic injection mould for medium production run and PS, PE, PP, ABS and other non-corrosive resins

</td>
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
