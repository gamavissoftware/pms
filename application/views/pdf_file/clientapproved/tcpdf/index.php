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

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// set text shadow effect
$pdf->setTextShadow(array('enabled'=>false, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Set some content to print

/** quert paet **/
 
  
/** end **/

	
$html='  	

<table rules="all" bordercolor="black" border="0">


<tr style="border-collapse:collapse;">

	<td width="800">
		<table rules="all" cellpadding="5">

			<tbody>
				
				<tr style="border-collapse:collapse;">
			<td style=" border: none; background-color:lightgrey;">
			<h5 style=" text-align: center;
			font-weight: bold;
			font-size: 17px;
			margin: 0;">
				PROJECT INFROMATION SHEET
			<br>
			<span style="margin: 0;
            text-align: center;
            font-weight: bold;
            font-size: 12px;">
                                Scope of Business and Service
                            </span>
							</h5>
			
		</td>
	</tr>
		
			
			</tbody>
		</table>


	</td>

	<td width="" style="padding:3px;">
		<table rules="all" cellpadding="5">

			<tbody>
			<tr style="border-collapse:collapse;">
			<td style=" border:none;">
			<img src="tcpdf/images/logo_hong.jpg">
			
		</td>
	</tr>
			</tbody>
		</table>
	</td>
</tr>
</table>
<br>
<br>















<table rules="all" bordercolor="black" border="0">


<tr style="border-collapse:collapse;">

	<td width="490">
		<table rules="all" cellpadding="5">

			<tbody>
				<tr style="border-collapse:collapse;">
					<td style="border: 1px solid black; text-align:right; font-weight: bold;  font-size: 12px;
					padding: 3px;">
					SELECT QUERY NO.
					</td>
					<td style="border: 1px solid black; font-size: 12px;
					padding: 3px; ">
					
					</td>
				</tr>
				<tr>
				<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
				padding: 3px;">SELECT STAGE NO.</td>
				<td style="border: 1px solid black;  font-size: 12px;
				padding: 3px;">Quotation</td>
			</tr>

			<tr>
				<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
				padding: 3px;">COMPANY NAME</td>
				<td style="border: 1px solid black;  font-size: 12px;
				padding: 3px;">Motif Electric Limited</td>
			</tr>

			<tr>
				<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
				padding: 3px;">CONTACT PERSON NAME</td>
				<td style="border: 1px solid black;  font-size: 12px;
				padding: 3px;">Mr. Mohit Rai Goyal</td>
			</tr>

			<tr>
				<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
				padding: 3px;">CONTACT PERSON MOBILE NO.</td>
				<td style="border: 1px solid black;  font-size: 12px;
				padding: 3px;">9999414134</td>
			</tr>

			<tr>
			<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
            padding: 3px;">CONTACT PERSON EMAIL ID</td>
			<td style="border: 1px solid black;  font-size: 12px;
            padding: 3px;">mohitrai@motifcap.com</td>
		</tr>
			
			</tbody>
		</table>


	</td>

	<td width="456" style="padding:3px;">
		<table rules="all" cellpadding="5">

			<tbody>
			<tr style="border-collapse:collapse;">
			<td style="border: 1px solid black; text-align:right; font-weight: bold;  font-size: 12px;
			padding: 3px;">
			DATE
			</td>
			<td style="border: 1px solid black; font-size: 12px;
			padding: 3px; ">
			Monday, 12 April, 2021
			</td>
		</tr>
		<tr>
		<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
		padding: 3px;">PROJECT NAME</td>
		<td style="border: 1px solid black;  font-size: 12px;
		padding: 3px;">Quotation</td>
	</tr>

	<tr>
		<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
		padding: 3px; height: 75px;"><br><br> COMPANY ADDRESS</td>
		<td style="border: 1px solid black;  font-size: 12px;
		padding: 3px;"></td>
	</tr>

	

	<tr>
		<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
		padding: 3px;">Company GST No.</td>
		<td style="border: 1px solid black;  font-size: 12px;
		padding: 3px;"></td>
	</tr>
			</tbody>
		</table>
	</td>
</tr>
</table>

<table>
<tr>
<td >
                                <h4 style="    text-align: center;
								font-weight: bold
								font-size: 17px;">QUERIES AND INPUTS REQUIRED FROM CUSTOMER</h4>
                               
                            </td>
</tr>

</table>

<table style="padding:3px;">
<tr>
<th style="border:1px solid black; font-weight:bold; text-align:center; background-color:lightgrey; width:398px; ">DETAILS</th>
<th style="border:1px solid black; font-weight:bold; text-align:center; background-color:lightgrey; width:248px;">REMARK</th>
<th style="border:1px solid black; font-weight:bold; text-align:center; background-color:lightgrey; width:111px;">STATUS</th>
<th style="border:1px solid black; font-weight:bold; text-align:center; background-color:lightgrey; width:189px;">REMARK</th>
</tr>
</table>

<table style="padding:3px;">
<tr>
<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
		padding: 3px; width:150px;">PRODUCT DESIGN</td>
<td style="border:1px solid black; font-size:12px; width:248px;">O YOU WANT US TO DO A PRODUCT DESIGN FOR YOU OR YOU ALREADY HAVE A PRODUCT DESIGN ?</td>
<td style="border:1px solid black; font-size:12px; width:248px;"></td>
<td style="border:1px solid black; font-size:12px; font-weight: bold; width: 111px;">NO</td>
<td style="border:1px solid black; font-size:12px;"></td>
</tr>

<tr>
<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
		padding: 3px;">INPUT TYPE</td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px; font-weight: bold">PPT PRESENTATION</td>
<td style="border:1px solid black; font-size:12px;"></td>
</tr>

<tr>
<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
		padding: 3px;">REVERSE ENGG OR NPD</td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px; font-weight: bold">REVERSE ENGINEERING + NPD BOTH</td>
<td style="border:1px solid black; font-size:12px;"></td>
</tr>

<tr>
<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
		padding: 3px;">BENCHMARK SAMPES STATUS</td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px; font-weight: bold">REQUIRED A CONCEPT DESIGN TOO</td>
<td style="border:1px solid black; font-size:12px;"></td>
</tr>

<tr>
<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
		padding: 3px;">MOCKUP</td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px; font-weight: bold">NO</td>
<td style="border:1px solid black; font-size:12px;"></td>
</tr>

<tr>
<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
		padding: 3px;">PROTOTYPE</td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px; font-weight: bold">NO</td>
<td style="border:1px solid black; font-size:12px;"></td>
</tr>

<tr>
<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
		padding: 3px;">3D FORMAT</td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px; font-weight: bold">VISI 3D (wkf)</td>
<td style="border:1px solid black; font-size:12px;"></td>
</tr>

<tr>
<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
		padding: 3px;">2D WITH GD&T</td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px; font-weight: bold">NO</td>
<td style="border:1px solid black; font-size:12px;"></td>
</tr>

<tr>
<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
		padding: 3px;">INDIVIDUAL PART/ASSEMBLY</td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px; font-weight: bold">ASSEMBLY</td>
<td style="border:1px solid black; font-size:12px;"></td>
</tr>

<tr>
<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
		padding: 3px;">NO OF ASSEMBLIES / VARRIENTS</td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px; font-weight: bold">NO</td>
<td style="border:1px solid black; font-size:12px;"></td>
</tr>

<tr>
<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
		padding: 3px;">MOULDING MACHINE DETAIL</td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px; font-weight: bold">NO</td>
<td style="border:1px solid black; font-size:12px;"></td>
</tr>

<tr>
<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
		padding: 3px;">
		RELATIVE FITMENT PARTS</td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px; font-weight: bold">NO</td>
<td style="border:1px solid black; font-size:12px;"></td>
</tr>

<tr>
<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
		padding: 3px;">BOPs</td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px; font-weight: bold">NO</td>
<td style="border:1px solid black; font-size:12px;"></td>
</tr>

<tr>
<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
		padding: 3px;">OTHER METING PARTS</td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px; font-weight: bold">NO</td>
<td style="border:1px solid black; font-size:12px;"></td>
</tr>

<tr>
<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
		padding: 3px;">INSERT MOULDING PARTS	</td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px; font-weight: bold"></td>
<td style="border:1px solid black; font-size:12px;"></td>
</tr>

<tr>
<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
		padding: 3px;">RAW MATERIAL</td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px; font-weight: bold">RM FROM CLIENT</td>
<td style="border:1px solid black; font-size:12px;"></td>
</tr>

<tr>
<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
		padding: 3px;">TOOL LIFE</td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px; font-weight: bold">1000-5000</td>
<td style="border:1px solid black; font-size:12px;"></td>
</tr>

<tr>
<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
		padding: 3px;">BATCH PRODUCTION</td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px; font-weight: bold"></td>
<td style="border:1px solid black; font-size:12px;"></td>
</tr>

<tr>
<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
		padding: 3px;">TRIAL SAMPLES QTY</td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px; font-weight: bold"></td>
<td style="border:1px solid black; font-size:12px;"></td>
</tr>

<tr>
<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
		padding: 3px;">TRIAL INSPECTION PROCES</td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px; font-weight: bold">NO</td>
<td style="border:1px solid black; font-size:12px;"></td>
</tr>

<tr>
<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
		padding: 3px;">MOULD MAINTANANCE</td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px; font-weight: bold">NO</td>
<td style="border:1px solid black; font-size:12px;"></td>
</tr>

<tr>
<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
		padding: 3px;">FOB / CIF</td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px; font-weight: bold">IMPORT BY CLIENT</td>
<td style="border:1px solid black; font-size:12px;"></td>
</tr>

<tr>
<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
		padding: 3px;">SEA / AIR</td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px; font-weight: bold">SEA</td>
<td style="border:1px solid black; font-size:12px;"></td>
</tr>

<tr>
<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 12px;
		padding: 3px;">CUSTOMER REQUESTS</td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px;"></td>
<td style="border:1px solid black; font-size:12px; font-weight: bold">NO</td>
<td style="border:1px solid black; font-size:12px;"></td>
</tr>
</table>
<br>
<br>



<table style="width: 100%; padding:3px; ">
<tr>
	<td style="border:1px solid black; ">
		<h4 style="font-size:20px; ">ABOUT THE PROJECT</h4>
		<p style="text-align: left;">Write a summary about the project and scope of business</p>
	</td>
</tr>

<tr>
	<td style="height: 50px; border:1px solid black">
		<textarea class="text-area"></textarea>
	</td>
</tr>
</table>



<br><br>
			<br>

<table rules="all" bordercolor="black" border="0">


<tr style="border-collapse:collapse;">

	<td width="490">
		<table rules="all" cellpadding="5">

			<tbody>
				
				<tr style="border-collapse:collapse;">
			<td style=" border-top:1px solid black; border-bottom:1px solid black;">
			<p style="text-align: left;">On / Behalf of<br> Signature<br><br><br><br><span style="text-align: left; font-weight: 700;">Hongyi JIG Rapid Technologies Co. Ltd.</span>  </p>
			
		</td>
	</tr>
		
			
			</tbody>
		</table>


	</td>

	<td width="456" style="padding:3px;">
		<table rules="all" cellpadding="5">

			<tbody>
			<tr style="border-collapse:collapse;">
			<td style=" border-top:1px solid black; border-bottom:1px solid black; border-left:1px solid black;">
			<p style="text-align: right;">On / Behalf of<br> Signature <br><br><br><br><span style="text-align: right; font-weight: 700;">Clients Company Name</span>  </p>
			
		</td>
	</tr>
			</tbody>
		</table>
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
