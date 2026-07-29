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
 
$html='

<table style="width:100%; padding:3px;">
<tr>
<td style="background-color:lightgrey; text-align:center; font-weight:bold; font-size:25px; width:350px; border:1px solid black;">PERFORMA INVOICE </td>
<td style="width:50px;"></td>
<td><img src="tcpdf/images/logo_hong.jpg"></td>
</tr>
</table>
<br><br>
<table style="width:100%; padding:3px; font-size:9px;" rull="all" border="1">
<tr>
<td style="background-color:lightgrey; font-weight:bold; text-align:center;">QUERY NO.</td>
<td>QN-0020</td>
<td style="background-color:lightgrey; font-weight:bold; text-align:center;">PI NUMBER</td>
<td>PI/QN-0020/001</td>
<td style="background-color:lightgrey; font-weight:bold; text-align:center;">CUSTOMER PO NO</td>
<td>-</td>
</tr>
</table>
<table style="width:100%; padding:3px; font-size:9px;" rull="all" border="1">
<tr>
<td style="background-color:lightgrey; font-weight:bold; text-align:center;">STAGE</td>
<td style="width:150px;">Sales Agreement</td>
<td style="background-color:lightgrey; font-weight:bold; text-align:center; width:50px;">DATE </td>
<td style="width:130px;">Wednesday, April 14, 2021</td>
<td style="background-color:lightgrey; font-weight:bold; text-align:center; width:90px;">QUOTATION NO. </td>
<td></td>
</tr>
</table>

<table style="width:100% padding:3px">
<tr>
<td style="border-bottom:1px solid black;"></td>
</tr>
</table>

<br><br>
<table style="width:100%; padding:3px; font-size:9px;">
<tr>
<td >BUYERS DETAILS</td>
<td >SELLERS DETAILS</td>
</tr>
<tr>
<td >

<table style="width:100%; padding:3px; font-size:9px;">

<tr >
<td style="text-align:right; font-weight:bold; width:110px; background-color:lightgrey;">COMPANY NAME</td>
<td style="width:200px;">Star Enterprises</td>
</tr>
<tr >
<td style="text-align:right; font-weight:bold; background-color:lightgrey;">ADDRESS</td>
<td>Backside Oswal Wollen Mills(Agro Mills),G.T.Road, Ludhiana-141010,Punjab </td>
</tr>
<tr >
<td style="text-align:right; font-weight:bold; background-color:lightgrey;">CONTACT PERSON</td>
<td>Mr. Sanchit Setia</td>
</tr>
<tr >
<td style="text-align:right; font-weight:bold; background-color:lightgrey;">MOBILE NO.</td>
<td>8427555554</td>
</tr>

<tr >
<td style="text-align:right; font-weight:bold; background-color:lightgrey;">GSTIN</td>
<td>03DTEPS5628Q1Z0</td>
</tr>

<tr >
<td style="text-align:right; font-weight:bold; background-color:lightgrey;">E-MAIL</td>
<td>sanchitst9@gmail.com</td>
</tr>
</table>
</td>

<td>
<table style="width:100%; padding:3px; font-size:9px;">
<tr >
<td style="text-align:right; font-weight:bold; width:110px; background-color:lightgrey;">COMPANY NAME</td>
<td style="width:200px; font-weight:bold;">HONGYI <span style="color:orange;">JIG</span> RAPID TECHNOLOGIES</td>
</tr>
<tr >
<td style="text-align:right; font-weight:bold; background-color:lightgrey;">ADDRESS</td>
<td>15/1, 2nd Floor, Rama Road Industrial Area Delhi 110015 (INDIA) </td>
</tr>
<tr >
<td style="text-align:right; font-weight:bold; background-color:lightgrey;">CONTACT PERSON</td>
<td>Pooja Wadhwa</td>
</tr>
<tr >
<td style="text-align:right; font-weight:bold; background-color:lightgrey;">MOBILE NO.</td>
<td>9999644767</td>
</tr>

<tr >
<td style="text-align:right; font-weight:bold; background-color:lightgrey;">E-MAIL</td>
<td>scrm@hongyijig.com</td>
</tr>

<tr >
<td style="text-align:right; font-weight:bold; background-color:lightgrey;">WEB</td>
<td>www.hongyijig.com</td>
</tr>
</table>
</td>
</tr>

<tr>
<td style="border-top:1px solid black;"></td>
<td style="border-top:1px solid black;"></td>
</tr>
</table>



<table style="width:100%; padding:3px;" >
<tr>
<td style="background-color:lightgrey; text-align:center; font-weight:bold; font-size:15px;">LIST OF MOULDS WITH SPECS </td>
</tr>
</table>
<br>
<br>

<table style="width:100%; padding:3px; font-size:8px;">
<tr style="background-color:lightgrey; text-align:center; font-weight:bold; ">
<th style="border:1px solid black; width:30px;">S.No</th>
<th style="border:1px solid black; width:80px;">Part Name</th>
<th style="border:1px solid black;">Part CFM</th>
<th style="border:1px solid black;">Part Picture</th>
<th style="border:1px solid black;">Part Size</th>
<th style="border:1px solid black; width:40px;">Cavity</th>
<th style="border:1px solid black;">Runner Type/Make</th>
<th style="border:1px solid black; width:70px;">Mould Sizes (mm) </th>
<th style="border:1px solid black; width:65px;">Mould Material</th>
<th style="border:1px solid black;" >Weight in Kgs.</th>
<th style="border:1px solid black;">Cost FOB
(USD) </th>
</tr>

<tr style="text-align:center;">
<td style="border-right:1px solid black; border-left:1px solid black;">1</td>
<td style="border-right:1px solid black; border-left:1px solid black;">P_SPACER_2M</td>
<td style="border-right:1px solid black; border-left:1px solid black;">Part Colour:Black Surface Finish:MATT Material:ABS 700 FR V0
</td>
<td style="border-right:1px solid black; border-left:1px solid black;"><img src="tcpdf/images/pro.jpg"></td>
<td style="border-right:1px solid black; border-left:1px solid black;">45x45x5</td>
<td style="border-right:1px solid black; border-left:1px solid black;"> 2</td>
<td style="border-right:1px solid black; border-left:1px solid black;">Cold Runner</td>
<td style="border-right:1px solid black; border-left:1px solid black;">280*260*300</td>
<td style="border-right:1px solid black; border-left:1px solid black;">718H(ASSAB</td>
<td style="border-right:1px solid black; border-left:1px solid black;">600</td>
<td style="border-right:1px solid black; border-left:1px solid black;">$2,968</td>
</tr>

<tr style="text-align:center;">
<td style="border-right:1px solid black; border-left:1px solid black;">2</td>
<td style="border-right:1px solid black; border-left:1px solid black;">P_SPACER_2M</td>
<td style="border-right:1px solid black; border-left:1px solid black;">Part Colour:Black Surface Finish:MATT Material:ABS 700 FR V0
</td>
<td style="border-right:1px solid black; border-left:1px solid black;"><img src="tcpdf/images/pro.jpg"></td>
<td style="border-right:1px solid black; border-left:1px solid black;">45x45x5</td>
<td style="border-right:1px solid black; border-left:1px solid black;"> 2</td>
<td style="border-right:1px solid black; border-left:1px solid black;">Cold Runner</td>
<td style="border-right:1px solid black; border-left:1px solid black;">280*260*300</td>
<td style="border-right:1px solid black; border-left:1px solid black;">718H(ASSAB</td>
<td style="border-right:1px solid black; border-left:1px solid black;">600</td>
<td style="border-right:1px solid black; border-left:1px solid black;">$2,968</td>
</tr>

<tr>
<td style="border:1px solid black; background-color:lightgrey; text-align:right; font-weight:bold;" colspan="9" >TOTAL</td>
<td style="border:1px solid black; background-color:lightgrey; text-align:center; font-weight:bold;">$7,076</td>
<td style="border:1px solid black; background-color:lightgrey; text-align:center; font-weight:bold;">$38,000</td>
</tr>

<tr>
<td colspan="8"></td>
<td style="border:1px solid black; background-color:lightgrey; text-align:right; width:122px; font-weight:bold;" >OTHER SERVICES</td>
<td style="border:1px solid black;  text-align:center;"></td>

</tr>

<tr>
<td colspan="8"></td>
<td style="border:1px solid black; background-color:lightgrey; text-align:right; width:122px; font-weight:bold;" >SHIPMENT </td>
<td style="border:1px solid black;  text-align:center;">$0</td>

</tr>

<tr>
<td style="border-left:1px solid black; border-top:1px solid black; background-color:lightgrey; text-align:right; border-right:1px solid black; font-weight:bold;" colspan="2">AMOUNT IN WORDS</td>
<td style="border-left:1px solid black; border-top:1px solid black;" colspan="6"></td>
<td style="border:1px solid black; background-color:lightgrey; text-align:right; font-weight:bold;" >GRAND TOTAL </td>
<td style="border:1px solid black;  text-align:center; ">$38,000</td>
</tr>

<tr>
<td style="border-left:1px solid black; border-bottom:1px solid black; background-color:lightgrey; text-align:right; border-right:1px solid black;" colspan="2"></td>
<td style="border-left:1px solid black; border-bottom:1px solid black;" colspan="6">USD Thirty Eight Thousand Only </td>
<td style="border:1px solid black; background-color:lightgrey; text-align:left; font-size:7px; font-weight:bold;" >GST as per actual at the time of invoice </td>
<td style="border:1px solid black;  text-align:center; "></td>
</tr>
</table>

<br>
<br>
<table style="width:100%; font-size:9px; " >
<tr>
<td style=" width:170px;">PAYMENT TERMS </td>
<td style=" width:460px; text-align:right;">PLEASE CONSIDER THE CURRENCY EXCHANGE RATE OF THE DAY AT THE TIME OF BANK TRANSFER</td>
</tr>
</table>

<table style="width:100%; font-size:9px; padding:3px;" >
<tr>
<td style="width:450px; ">
<table border="1" style="text-align:center;  padding:3px;">
<tr>
<td style="background-color:lightgrey;">STAGE</td>
<td style="width:130px; background-color:lightgrey;">STATUS</td>
<td style="width:70px; background-color:lightgrey;">PERCENTAGE</td>
<td style="background-color:lightgrey;">PRV. DUES</td>
<td style="width:70px; background-color:lightgrey;">AMOUNT</td>
</tr>

<tr>
<td style="text-align:right:">ADVANCE</td>
<td style="text-align:left:">PAID</td>
<td style="text-align:center:">21%</td>
<td style="text-align:center:"></td>
<td style="text-align:center:">$8,000</td>
</tr>
<tr>
<td style="text-align:right:">ADVANCE</td>
<td style="text-align:left:">PAID</td>
<td style="text-align:center:">21%</td>
<td style="text-align:center:"></td>
<td style="text-align:center:">$8,000</td>
</tr>
<tr>
<td style="text-align:right:">ADVANCE</td>
<td style="text-align:left:">PAID</td>
<td style="text-align:center:">21%</td>
<td style="text-align:center:"></td>
<td style="text-align:center:">$8,000</td>
</tr>
<tr>
<td style="text-align:right; font-weight:bold; background-color:lightgrey;">TOTAL</td>
<td style="text-align:left; background-color:lightgrey;"></td>
<td style="text-align:left; background-color:lightgrey;"></td>
<td style="text-align:center; background-color:lightgrey;" colspan="2"></td> 


</tr>
</table >
</td>
<td style="width:180px; ">
<table border="1" style="text-align:center;  padding:3px;">
<tr>
<td style="background-color:lightgrey;">AMOUNT NEED TO PAY</td>

</tr>
<tr>
<td style="font-weight:bold; font-size:20px;">$38,000.00
</td>
</tr>
</table>
</td>
</tr>
</table>
<br><br>
<table style="padding:3px; font-size:9px; width:100%;">
<tr>
<th style="text-align:center; background-color:lightgrey; font-weight:bold;  width:260px;">Account Details</th>
<th style="text-align:center; background-color:lightgrey; font-weight:bold; ">PAYMENT STAGE</th>
<th style="text-align:center; background-color:lightgrey; font-weight:bold;  width:80px;">STAGE</th>
<th style="text-align:center; background-color:lightgrey; font-weight:bold;  width:80px;">PERCENTAGE</th>
<th style="text-align:center; background-color:lightgrey; font-weight:bold;  width:80px;">AMOUNT</th>
</tr>
<tr>
<td>
<table>
<tr>
<td style="text-align:center; border-right:1px solid black; border-left:1px solid black; width:80px;">Account Name</td>
<td style="width:200px; border-left:1px solid black;">HONGYI JIG RAPID TECHNOLOGIES</td>
</tr>
</table>
</td>
<td style="text-align:right; border-right:1px solid black; border-left:1px solid black;">ADVANCE</td>
<td style="text-align:center; border-right:1px solid black; border-left:1px solid black;">WITH PO</td>
<td style="text-align:center; border-right:1px solid black; border-left:1px solid black;">21%</td>
<td style="text-align:center; border-right:1px solid black; border-left:1px solid black;">$8,000</td>
</tr>



<tr>
<td>
<table>
<tr>
<td style="text-align:center; border-right:1px solid black; border-left:1px solid black; width:80px;">Account Name</td>
<td style="width:200px; border-left:1px solid black;">HONGYI JIG RAPID TECHNOLOGIES</td>
</tr>
</table>
</td>
<td style="text-align:right; border-right:1px solid black; border-left:1px solid black;">ADVANCE</td>
<td style="text-align:center; border-right:1px solid black; border-left:1px solid black;">WITH PO</td>
<td style="text-align:center; border-right:1px solid black; border-left:1px solid black;">21%</td>
<td style="text-align:center; border-right:1px solid black; border-left:1px solid black;">$8,000</td>
</tr>

<tr>
<td>
<table>
<tr>
<td style="text-align:center; border-right:1px solid black; border-left:1px solid black; width:80px;">Account Name</td>
<td style="width:200px; border-left:1px solid black;">HONGYI JIG RAPID TECHNOLOGIES</td>
</tr>
</table>
</td>
<td style="text-align:right; border-right:1px solid black; border-left:1px solid black;">ADVANCE</td>
<td style="text-align:center; border-right:1px solid black; border-left:1px solid black;">WITH PO</td>
<td style="text-align:center; border-right:1px solid black; border-left:1px solid black;">21%</td>
<td style="text-align:center; border-right:1px solid black; border-left:1px solid black;">$8,000</td>
</tr>

<tr>
<td></td>
<td style="text-align:right; font-weight:bold; background-color:lightgrey;" colspan="2">TOTAL</td>
<td style="text-align:center; font-weight:bold; background-color:lightgrey;">100%</td>
<td style="text-align:center; font-weight:bold; background-color:lightgrey;">$38,000</td>
<td></td>
<td></td>
</tr>

</table>

<table style="width:100%; padding:3px; font-size:8px;">
<tr>
<td style="font-weight:bold;">TERMS OF TRANSECTIONS</td>
</tr>

<tr>
1.Order execution terms will be based on the quotation agreement (as per the Quotation No. mentioned. <br>
2.All payments will be consider in terms of US$ amount and subjected to exchange rate vaiations. the difference will be charged from the client.<br> 
3.The bank transfer fee will be paid by the customer, in case of any difference appeared, will be charged in the last payment before delivery.
</tr>
</table>


';	


//echo $footer_logo_html; exit;
// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.



$filelocation = $_SERVER['DOCUMENT_ROOT'].'/image_bank/popdf';
$fileNL = $filelocation."/akash.pdf"; //Linux

$pdf->Output($fileNL, 'I');



//============================================================+
// END OF FILE
//============================================================+
