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

<table rules="all" bordercolor="black" border="0">


<tr style="border-collapse:collapse;">

	

	<td width="150" style="padding:3px;">
		<table rules="all" cellpadding="5">

			<tbody>
			<tr style="border-collapse:collapse;">
			<td style=" border:none;">
			<img src="tcpdf/images/logo_hong.jpg" style="width:300; border-bottom: 1px dotted black;">
			
		</td>
	</tr>
			</tbody>
		</table>
	</td>

    <td width="100"></td>
   

    <td width="">
		<table rules="all" cellpadding="">

			<tbody>
				
				<tr style="border-collapse:collapse;">
			<td style=" border: none; background-color:lightgrey;">
			<h5 style=" text-align: center;
			font-weight: bold;
			font-size: 30px;
			margin: 0;">
            Tax Invoice
			
		
							</h5>
			
		</td>
	</tr>
		
			
			</tbody>
		</table>


	</td>
</tr>
</table>

<table rules="all" bordercolor="black" border="0">
<tr style="border-collapse:collapse;">

<td width="250">
<p style="    text-align: left; font-weight: bold; font-size: 9px;   margin:0px;">Hongyi <span style="color: orange;">JIG</span> Rapid Technologies </p>
<p style="font-size:9px;">15/1, 2nd Floor, Rama Road, New Delhi-110015<br>
GSTIN No: <span style="font-weight: bold;">07AQKPK4320K1ZS</span><br>
GSTIN/UIN: <span style="font-weight: bold;">07AQKPK4320K1ZS</span><br>
State Name: Delhi, Code: 07<br>
CIN:<span style="font-weight: bold;">07AQKPK4320K1ZS</span></p><br><br>
<p style="text-align: center;
background-color: lightgray;
font-weight: bold; border-top: 1px dotted black; font-size:9px;">Consignee Details</p>

<table cellpadding="3" style="font-size:9px;">
<tr>
<td style="border-right: 1px solid black; font-weight:bold; width: 125px; text-align:right; padding:3px;">
                                    Company Name
                                </td>
<td></td>
</tr>
<tr>
<td style="border-right: 1px solid black; font-weight:bold; width: 125px; text-align:right; padding:3px; height:50px;">
Address
                                </td>
<td></td>
</tr>

<tr>
<td style="border-right: 1px solid black; font-weight:bold; width: 125px; text-align:right; padding:3px;">
GSTIN NO
                                </td>
<td></td>
</tr>
</table>



<p style="text-align: center;
background-color: lightgray;
font-weight: bold; border-top: 1px dotted black; font-size:9px;">Buyer (If other than consignee)</p>
<table cellpadding="3" style="font-size:9px;">
<tr>
<td style="border-right: 1px solid black; font-weight:bold; width: 125px; text-align:right; padding:3px;">
                                    Company Name
                                </td>
<td></td>
</tr>
<tr>
<td style="border-right: 1px solid black; font-weight:bold; width: 125px; text-align:right; padding:3px; height:50px;">
Address
                                </td>
<td></td>
</tr>

<tr>
<td style="border-right: 1px solid black; font-weight:bold; width: 125px; text-align:right; padding:3px;">
GSTIN NO
                                </td>
<td></td>
</tr>
</table><hr>
</td>




<td width="380" style="font-size:9px;">
<p style="text-align:right; font-size:9px;">Original for Recipient </p>
<table cellpadding="3" >
<tr>

<td >
<table cellpadding="3" style="width:100%;">
<tr>

<td style=" width:110px; text-align:right; font-weight:bold; font-size:9px;">Invoice No</td>
<td style="border:1px solid black; font-size:9px; width:100px;">HJIG/2021-22/0001</td>
</tr>
<tr>

<td style="  text-align:right; font-weight:bold;">E-Way Bill No</td>
<td style="border:1px solid black;"></td>
</tr>
<tr>

<td style="  text-align:right; font-weight:bold;">Delivery Note</td>
<td style="border:1px solid black;"></td>
</tr>
<tr>

<td style="  text-align:right; font-weight:bold;">Suppliers Ref</td>
<td style="border:1px solid black;"></td>
</tr>


</table>
</td>
<td >
<table cellpadding="3" style="width:100%;">
<tr>

<td style="  text-align:right; font-weight:bold; width:130px;">Dated</td>
<td style="border:1px solid black; width:55px;">4/14/2021</td>
</tr>
<tr>

<td style="  text-align:right; font-weight:bold;">Mode of Payment</td>
<td style="border:1px solid black;"></td>
</tr>
<tr>

<td style="  text-align:right; font-weight:bold;">Terms of Payment</td>
<td style="border:1px solid black;"></td>
</tr>
<tr>

<td style="  text-align:right; font-weight:bold;">Other Reference(s)</td>
<td style="border:1px solid black;"></td>
</tr>



</table>
</td>
</tr>



</table>

<br>
<br>
<br>




<table cellpadding="3" style="width:100%;">
<tr>

<td >
<table cellpadding="3" style="width:100%;">
<tr>

<td style=" width:110px; text-align:right; font-weight:bold;">Customers Code</td>
<td style="border:1px solid black; width:100px;">QN-1650</td>
</tr>
<tr>

<td style="  text-align:right; font-weight:bold;">Customers Order No</td>
<td style="border:1px solid black;">QN-1650-1</td>
</tr>
<tr>

<td style="  text-align:right; font-weight:bold;">Dispatch Document No</td>
<td style="border:1px solid black;"></td>
</tr>



</table>
</td>
<td >
<table cellpadding="3"  >
<tr>

<td style="  text-align:right; font-weight:bold; width:130px;">Date of Dispatch</td>
<td style="border:1px solid black; width:55px;">4/14/2021</td>
</tr>
<tr>

<td style="  text-align:right; font-weight:bold;">Mode of Dispatch</td>
<td style="border:1px solid black;"></td>
</tr>
<tr>

<td style="  text-align:right; font-weight:bold;">Destination</td>
<td style="border:1px solid black;">delhi</td>
</tr>




</table>
</td>
</tr>
</table>

<table style="width:100%; padding:3px;">
<tr>
<td style=" width:112px; text-align:right; font-weight:bold;">Dispatch Through</td>
<td style="border:1px solid black; width:270px;"></td>
</tr>
</table>

<table style="width:100%; padding:3px;">
<tr>
<td style=" width:112px; text-align:right; font-weight:bold;">Terms of Delivery</td>
<td style="border:1px solid black; width:464px; height:120px; width:270px;">Against Payment </td>
</tr>
</table>


</td>
</tr>
</table>


<table style="width:100%; padding:3px; font-size:9px;">
<tr>
<th style="text-align:center; background-color:lightgrey; font-weight:bold; border:1px solid black; width:30px;">S.No</th>
<th style="text-align:center; background-color:lightgrey; font-weight:bold; border:1px solid black; width:251px;">DESRIPTION OF GOODS</th>
<th style="text-align:center; background-color:lightgrey; font-weight:bold; border:1px solid black; width:70px;">HSN CODE</th>
<th style="text-align:center; background-color:lightgrey; font-weight:bold; border:1px solid black; width:70px;">GST RATE </th>
<th style="text-align:center; background-color:lightgrey; font-weight:bold; border:1px solid black; width:70px;">QTY</th>
<th style="text-align:center; background-color:lightgrey; font-weight:bold; border:1px solid black; width:70px;">RATE UNIT COST</th>
<th style="text-align:center; background-color:lightgrey; font-weight:bold; border:1px solid black; width:70px;">COST IN RS. </th>
</tr>
<tr>
<td style="text-align: center; border-right: 1px solid black; border-left: 1px solid black;">1</td>
<td>20 CC BARREL</td>
<td style="text-align: center; border-right: 1px solid black; border-left: 1px solid black;">9985</td>
<td style="text-align: center; border-right: 1px solid black; border-left: 1px solid black;">18%</td>
<td style="text-align: center; border-right: 1px solid black; border-left: 1px solid black;">10000</td>
<td style="text-align: center; border-right: 1px solid black; border-left: 1px solid black;">₹10.0</td>
<td style="text-align: center; border-right: 1px solid black; border-left: 1px solid black;">₹100,000.0</td>
</tr>

<tr>
<td style="text-align: center; border-right: 1px solid black; border-left: 1px solid black;">2</td>
<td>20 CC BARREL</td>
<td style="text-align: center; border-right: 1px solid black; border-left: 1px solid black;">9985</td>
<td style="text-align: center; border-right: 1px solid black; border-left: 1px solid black;">18%</td>
<td style="text-align: center; border-right: 1px solid black; border-left: 1px solid black;">10000</td>
<td style="text-align: center; border-right: 1px solid black; border-left: 1px solid black;">₹12.0</td>
<td style="text-align: center; border-right: 1px solid black; border-left: 1px solid black;">₹120,000.0</td>
</tr>

<tr>
<td style="text-align: center; border-right: 1px solid black; border-left: 1px solid black;">3</td>
<td>20 CC BARREL</td>
<td style="text-align: center; border-right: 1px solid black; border-left: 1px solid black;">9985</td>
<td style="text-align: center; border-right: 1px solid black; border-left: 1px solid black;">18%</td>
<td style="text-align: center; border-right: 1px solid black; border-left: 1px solid black;">10000</td>
<td style="text-align: center; border-right: 1px solid black; border-left: 1px solid black;">₹15.0</td>
<td style="text-align: center; border-right: 1px solid black; border-left: 1px solid black;">₹150,000.0</td>
</tr>

<tr>
                            <td style="text-align: center; border-right: 1px solid black; border-left: 1px solid black;">4</td>
                            <td>20 CC BARREL</td>
                            <td style="text-align: center; border-right: 1px solid black; border-left: 1px solid black;">9985</td>
                            <td style="text-align: center; border-right: 1px solid black; border-left: 1px solid black;">18%</td>
                            <td style="text-align: center; border-right: 1px solid black; border-left: 1px solid black;">10000</td>
                            <td style="text-align: center; border-right: 1px solid black; border-left: 1px solid black;">₹20.0</td>
                            <td style="text-align: center; border-right: 1px solid black; border-left: 1px solid black;">₹200,000.0</td>
                        </tr>
</table>



<table style="width:100%; padding:3px; font-size:9px;">
<tr>
<td style="text-align:center;  width:30px; border-right: 1px solid black; border-left: 1px solid black;"></td>
<td style="text-align:right;  width:251px; border-right: 1px solid black; border-left: 1px solid black; font-style: italic; font-weight:bold">CGST OUTPUT 9%</td>
<td style="text-align:center;  width:70px; border-right: 1px solid black; border-left: 1px solid black;"></td>
<td style="text-align:center;  width:70px; border-right: 1px solid black; border-left: 1px solid black;">9% </td>
<td style="text-align:center;  width:70px; border-right: 1px solid black; border-left: 1px solid black;"></td>
<td style="text-align:center;  width:70px; border-right: 1px solid black; border-left: 1px solid black;"></td>
<td style="text-align:center;  width:70px; border-right: 1px solid black; border-left: 1px solid black;">₹51,300.0</td>
</tr>

<tr>
<td style="text-align:center;  width:30px; border-right: 1px solid black; border-left: 1px solid black;"></td>
<td style="text-align:right;  width:251px; border-right: 1px solid black; border-left: 1px solid black; font-style: italic; font-weight:bold">SGST OUTPUT 9%</td>
<td style="text-align:center;  width:70px; border-right: 1px solid black; border-left: 1px solid black;"></td>
<td style="text-align:center;  width:70px; border-right: 1px solid black; border-left: 1px solid black;">9% </td>
<td style="text-align:center;  width:70px; border-right: 1px solid black; border-left: 1px solid black;"></td>
<td style="text-align:center;  width:70px; border-right: 1px solid black; border-left: 1px solid black;"></td>
<td style="text-align:center;  width:70px; border-right: 1px solid black; border-left: 1px solid black;">₹51,300.0</td>
</tr>

<tr>
<th style="text-align:center;  width:30px; border-right: 1px solid black; border-left: 1px solid black;"></th>
<th style="text-align:right;  width:251px; border-right: 1px solid black; border-left: 1px solid black; font-style: italic; font-weight:bold">Round off</th>
<th style="text-align:center;  width:70px; border-right: 1px solid black; border-left: 1px solid black;"></th>
<th style="text-align:center;  width:70px; border-right: 1px solid black; border-left: 1px solid black;"></th>
<th style="text-align:center;  width:70px; border-right: 1px solid black; border-left: 1px solid black;"></th>
<th style="text-align:center;  width:70px; border-right: 1px solid black; border-left: 1px solid black;"></th>
<th style="text-align:center;  width:70px; border-right: 1px solid black; border-left: 1px solid black;"></th>
</tr>
</table>
<table style="width:100%; padding:3px; font-size:9px;">
<tr>
<td style="text-align:right;   border:1px solid black; background-color:lightgrey; width:561px"  colspan="6">TOTAL</td>
<td style="text-align:center;  border:1px solid black;  background-color:lightgrey; width:70px;"> ₹672,600.0</td>

</tr>
</table>

<table style="width:100%; padding:3px; font-size:9px;">
<tr>
<td style="text-align:left;   border:1px solid black; background-color:lightgrey; " >Total Amount (in words): #NAME?</td>


</tr>
</table>
<br>
<br>

<table style="width:100%; padding:3px; font-size:9px;">
<tr>
<th style="text-align:center; background-color:lightgrey; font-weight:bold; border:1px solid black; width:200px;">HSN/SAC</th>
<th style="text-align:center; background-color:lightgrey; font-weight:bold; border:1px solid black; width:150px;">Taxable Value</th>
<th style="text-align:center; background-color:lightgrey; font-weight:bold; border:1px solid black; width:70px;">TAX</th>
<th style="text-align:center; background-color:lightgrey; font-weight:bold; border:1px solid black; width:70px;">TAX</th>
<th style="text-align:center; background-color:lightgrey; font-weight:bold; border:1px solid black; width:70px;">Amount</th>
<th style="text-align:center; background-color:lightgrey; font-weight:bold; border:1px solid black; width:70px;">Total Tax Amount</th>
</tr>

<tr>
<td style="text-align: center; border:1px solid black;">9985 </td>
<td style="text-align: center; border:1px solid black;">₹570,000.0 </td>
<td style="text-align: center; border:1px solid black;">CGST</td>
<td style="text-align: center; border:1px solid black;">9%</td>
<td style="text-align: center; border:1px solid black;">₹51,300.0</td>
<td style="text-align: center; border-right:1px solid black;"></td>
</tr>
<tr>
<td style="text-align: center; border:1px solid black;"></td>
<td style="text-align: center; border:1px solid black;"></td>
<td style="text-align: center; border:1px solid black;">SCGST</td>
<td style="text-align: center; border:1px solid black;">9%</td>
<td style="text-align: center; border:1px solid black;">₹51,300.0</td>
<td style="text-align: center; border-right:1px solid black;">₹102,600.0</td>
</tr>
<tr>
<td style="text-align: center; border:1px solid black;"></td>
<td style="text-align: center; border:1px solid black;"></td>
<td style="text-align: center; border:1px solid black;"></td>
<td style="text-align: center; border:1px solid black;"></td>
<td style="text-align: center; border:1px solid black;"></td>
<td style="text-align: center; border-right:1px solid black;"></td>
</tr>

<tr>
<th style="text-align:right; background-color:lightgrey; font-weight:bold; border:1px solid black; width:200px;">Total</th>
<th style="text-align:center; background-color:lightgrey; font-weight:bold; border:1px solid black; width:150px;">₹570,000.00 </th>
<th style="text-align:center; background-color:lightgrey; font-weight:bold; border:1px solid black; width:70px;"></th>
<th style="text-align:center; background-color:lightgrey; font-weight:bold; border:1px solid black; width:70px;">18%</th>
<th style="text-align:center; background-color:lightgrey; font-weight:bold; border:1px solid black; width:70px;">₹102,600.00</th>
<th style="text-align:center; background-color:lightgrey; font-weight:bold; border:1px solid black; width:70px;">₹102,600.00</th>
</tr>
</table>



<br>
<br>
<br>
<table>
<tr>
<td style=" width:250px;">
<table style="padding:3px; font-size:9px;">
<tr>
<td style="font-weight:bold;">Companys PAN</td>
<td style="font-weight:bold;">AQKPK4320K</td>
</tr>
<tr>
<td style="font-weight:bold;">Declaration</td>

</tr>
</table>
<table style="width:100%; padding:3px; font-size:9px;">
<tr>
<td style="border:1px solid black;">We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct</td>
</tr>
</table>
<br><br>
<table style="padding:3px;">
<tr>
<th style="background-color:lightgrey; border-left:1px solid black; border-top:1px solid black; border-right:1px solid black; width:200px; font-weight:bold; font-size:9px;">Cutomers Seal and Signature</th>
</tr>
<tr>
<td style="border:1px solid black; height:50px;"></td>
</tr>
</table>

</td>
<td style=" width:380px;">
<table style="padding:3px; font-size:9px;">
<tr>
<td style="font-weight:bold; width:100px;">Account Name</td>
<td style="font-weight:bold;">Hongyi JIG Rapid Technologies</td>
</tr>
<tr>
<td style="font-weight:bold; width:100px; border-right:1px solid black;">Bank Name</td>
<td>Canara Bank C/A-2016201002984 </td>

</tr>

<tr>
<td style="font-weight:bold; width:100px; border-right:1px solid black;">A/c No</td>
<td>2016201002984</td>

</tr>

<tr>
<td style="font-weight:bold; width:100px; border-right:1px solid black;">IFS Code</td>
<td>CNRB0002016</td>

</tr>

<tr>
<td style="font-weight:bold; width:100px; ">Branch</td>
<td>Keshav Puram, New Delhi-110035</td>

</tr>
</table>

<table style="padding:3px; font-size:9px;">
<tr>
<td style="border-top:1px solid black;">
For <span style="font-weight:bold;">Hongyi JIG Rapid Technologies</span> - From 1-Apr-2020

</td>
</tr>
</table>
<br><br><br><br>
<table style="padding:3px; ">
<tr>
<td style="border-right:1px solid black; width:135px; font-size:9px;">Prepared By</td>
<td style="border-right:1px solid black; width:135px; text-align:center; font-size:9px;">Verfied By</td>
<td style="text-align:center; font-size:9px; ">Authorised Signatory</td>
</tr>
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



$filelocation = $_SERVER['DOCUMENT_ROOT'].'/image_bank/popdf';
$fileNL = $filelocation."/akash.pdf"; //Linux

$pdf->Output($fileNL, 'I');



//============================================================+
// END OF FILE
//============================================================+
