<?php
// Include the main TCPDF library (search for installation path).
include('mysqlconfig.php');

$lead_id=$_GET['lead_id'];

$query = "SELECT id, customer_name, unique_id, contact_no, email FROM leads WHERE id = $lead_id";
$results = mysqli_query($con,$query);
$row_query = mysqli_num_rows($results);

$query_info = "SELECT a.company_name, a.project_name, a.company_address, a.gst_no, b.summary FROM customer_extra_details a JOIN rfq_lead_summary b ON a.lead_id=b.lead_id WHERE a.lead_id = $lead_id";
//echo $query_info;exit;
$result_info = mysqli_query($con,$query_info);
$row_query_info = mysqli_num_rows($result_info);

$sql = "SELECT b.title, b.description, a.remarks, a.ques_id as id, a.ans_id FROM rfq_customer_info a JOIN rfq_input_requirement_master b ON a.ques_id=b.id WHERE a.lead_id = $lead_id";
//echo $sql;exit;
$result=mysqli_query($con,$sql);
$row_count = mysqli_num_rows($result);

require_once('tcpdf_include.php');

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
if($row_query > 0) {
$data = mysqli_fetch_array($results);
$customer_name = $data['customer_name'];
$contact_no = $data['contact_no'];
$email = $data['email'];
$unique_id = $data['unique_id'];
}

if($row_query_info > 0) {
$data_info = mysqli_fetch_array($result_info);
$company_name = $data_info['company_name'];
$project_name = $data_info['project_name'];
$company_address = $data_info['company_address'];
$gst_no = $data_info['gst_no'];
$summary = $data_info['summary'];
}

$html='  	

<table rules="all" bordercolor="black" border="0">


<tr style="border-collapse:collapse;">

	<td width="500">
		<table rules="all" cellpadding="5">

			<tbody>
				
				<tr style="border-collapse:collapse;">
			<td style=" border: none; background-color:lightgrey;">
			<h5 style=" text-align: center;
			font-weight: bold;
			font-size: 17px;
			margin: 0;">
				PROJECT INFORMATION SHEET FOR '.$customer_name.'
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

	<td>
		<table rules="all" cellpadding="5">

			<tbody>
				<tr style="border-collapse:collapse;">
					<td style="border: 1px solid black; text-align:right; font-weight: bold;  font-size: 9px;
					padding: 3px;">
					SELECT QUERY NO.
					</td>
					<td style="border: 1px solid black; font-size: 9px;
					padding: 3px; ">
					'.$unique_id.'
					</td>
				</tr>
				<tr>
				<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 9px;
				padding: 3px;">SELECT STAGE NO.</td>
				<td style="border: 1px solid black;  font-size: 9px;
				padding: 3px;">Quotation</td>
			</tr>

			<tr>
				<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 9px;
				padding: 3px;">COMPANY NAME</td>
				<td style="border: 1px solid black;  font-size: 9px;
				padding: 3px;">'.$company_name.'</td>
			</tr>

			<tr>
				<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 9px;
				padding: 3px;">CONTACT PERSON NAME</td>
				<td style="border: 1px solid black;  font-size: 9px;
				padding: 3px;">'.$customer_name.'</td>
			</tr>

			<tr>
				<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 9px;
				padding: 3px;">CONTACT PERSON MOBILE NO.</td>
				<td style="border: 1px solid black;  font-size: 9px;
				padding: 3px;">'.$contact_no.'</td>
			</tr>

			<tr>
			<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 9px;
            padding: 3px;">CONTACT PERSON EMAIL ID</td>
			<td style="border: 1px solid black;  font-size: 9px;
            padding: 3px;">'.$email.'</td>
		</tr>
			
			</tbody>
		</table>


	</td>

	<td  style="padding:3px;">
		<table rules="all" cellpadding="5">

			<tbody>
			<tr style="border-collapse:collapse;">
			<td style="border: 1px solid black; text-align:right; font-weight: bold;  font-size: 9px;
			padding: 3px;">
			DATE
			</td>
			<td style="border: 1px solid black; font-size: 9px;
			padding: 3px; ">
			Monday, 12 April, 2021
			</td>
		</tr>
		<tr>
		<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 9px;
		padding: 3px;">PROJECT NAME</td>
		<td style="border: 1px solid black;  font-size: 9px;
		padding: 3px;">'.$project_name.'</td>
	</tr>

	<tr>
		<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 9px;
		padding: 3px; height: 75px;"><br><br> COMPANY ADDRESS</td>
		<td style="border: 1px solid black;  font-size: 9px;
		padding: 3px;">'.$company_address.'</td>
	</tr>

	

	<tr>
		<td style="text-align: right; font-weight: bold; border: 1px solid black;  font-size: 9px;
		padding: 3px;">Company GST No.</td>
		<td style="border: 1px solid black;  font-size: 9px;
		padding: 3px;">'.$gst_no.'</td>
	</tr>
			</tbody>
		</table>
	</td>
</tr>
</table>
<br><br>
<table style="width:100%;">
<tr>
<td >
                                <h4 style="    text-align: center;
								font-weight: bold
								font-size: 17px;">QUERIES AND INPUTS REQUIRED FROM CUSTOMER</h4>
                               
                            </td>
</tr>

</table>
<br><br>

<table style="padding:3px; font-size:9px;">
<tr>
<th style="border:1px solid black; font-weight:bold; text-align:center; background-color:lightgrey; width:290px; ">DETAILS</th>
<th style="border:1px solid black; font-weight:bold; text-align:center; background-color:lightgrey; width:150px;">REMARK</th>
<th style="border:1px solid black; font-weight:bold; text-align:center; background-color:lightgrey; width:90px;">STATUS</th>
<th style="border:1px solid black; font-weight:bold; text-align:center; background-color:lightgrey; width:109px;">REMARK</th>
</tr>
</table>

<table style="padding:3px; font-size:9px;">';
$i=1;
if($row_count > 0) {
while($data =mysqli_fetch_array($result)) {
$ques_id = $data['id'];
$title = $data['title'];
$description = $data['description'];
	
$sql1 =	"SELECT a.remarks, b.predefine_script, b.predefine_remarks FROM rfq_customer_info a JOIN rfq_script_remarks_data b ON a.ans_id=b.id WHERE a.lead_id = $lead_id AND a.ques_id = $ques_id";
	//echo $sql1;exit;
$result1=mysqli_query($con,$sql1);
$row_count1 = mysqli_num_rows($result1);
$data1 = mysqli_fetch_array($result1);
$predefine_script = $data1['predefine_script'];
$predefine_remarks = $data1['predefine_remarks'];
	
$html .= '<tr>
<td style="text-align: right; font-weight: bold; border: 1px solid black;  
		padding: 3px; width:140px;">'.$title.'</td>
<td style="border:1px solid black;  width:150px;">'.$description.'</td>
<td style="border:1px solid black;  width:150px;">'.$predefine_remarks.'</td>
<td style="border:1px solid black;  font-weight: bold; width: 90px;">'.$predefine_script.'</td>
<td style="border:1px solid black; width:109px;">'.$remarks.'</td>
</tr>';
$i++;
	}
}

$html .= '</table>
<br>
<br>



<table style="width: 100%; padding:3px; ">
<tr>
	<td style="border:1px solid black; ">
		<h4 style="font-size:18px; ">ABOUT THE PROJECT</h4>
		<p style="text-align: left; font-size:9px;">Write a summary about the project and scope of business</p>
	</td>
</tr>

<tr>
	<td style="height: 50px; border:1px solid black">
		<textarea class="text-area">'.$summary.'</textarea>
	</td>
</tr>
</table>



<br><br>
			<br>

<table rules="all" bordercolor="black" border="0">


<tr style="border-collapse:collapse;">

	<td>
		<table rules="all" cellpadding="5">

			<tbody>
				
				<tr style="border-collapse:collapse;">
			<td style=" border-top:1px solid black; border-bottom:1px solid black;">
			<p style="text-align: left; font-size:9px;">On / Behalf of<br> Signature<br><br><br><br><span style="text-align: left; font-weight: 700;">Hongyi JIG Rapid Technologies Co. Ltd.</span>  </p>
			
		</td>
	</tr>
		
			
			</tbody>
		</table>


	</td>

	<td  style="padding:3px;">
		<table rules="all" cellpadding="5">

			<tbody>
			<tr style="border-collapse:collapse;">
			<td style=" border-top:1px solid black; border-bottom:1px solid black; border-left:1px solid black;">
			<p style="text-align: right; font-size:9px;">On / Behalf of<br> Signature <br><br><br><br><span style="text-align: right; font-weight: 700;">Clients Company Name</span>  </p>
			
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
