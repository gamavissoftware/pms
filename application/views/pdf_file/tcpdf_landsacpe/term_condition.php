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
// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
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

<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td>HONGYI JIG RAPID TECHOLOGIES CO. LTD. and TAIZHOU OKEMOLD CO. LTD. wish to explore a business opportunity of mutual interest and in connection with this opportunity may disclose certain confidential technical and business information
</td>
</tr>
<tr><td>This Tool Development Agreement, known as the “Agreement”, made this Monday, 31, May, 2021 is by and between
HONGYI JIG RAPID TECHOLOGIES CO.LTD. the “Releasor”, and TAIZHOU OKEMOLD CO. LTD. the“Recipient”, and
collectively “the Parties”.</td>
</tr>
<tr><td>WHEREAS, “Recipient” agrees to furnish terms & Conditions applicable on every PO raised by Releaser HONGYI JIG
RAPID TECHOLOGIES CO. LTD. in future. </td></tr>

</table>
<br>
<br>
<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="border-bottom:1px dotted black;"></td></tr>
</table>
<br>
<br>
<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:center; font-weight:bold;  ">TERMS & CONDITIONS</td>
</tr>

</table>
<br>
<br>

<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">BASIC TERMS</td>

</tr>
</table>

<ol style="font-size:10px;">
<li>Company certification (OPL) Fee to deal with Indian Bank of $150 will be innitialy charge before the placement of any order. </li>
<li> Subletting of our orders may be permitted only on our prior-permission in writing. </li>
<li>All Mould Material Certificates to be given on purchase of mould material along with delivery of moulds. </li>
<li>In case of Hot Runner moulds, the Hot runner gurantee certificate to be given along with moulds. </li>
<li>The Material Test Certificates and Hot Runner gurantee cards will be sent along with the Mould supplies would be your liability.
</li>
<li>In case of assembly parts, tool maker will check the drawings and designs feasiability of the meting parts and in case of any doubt on meting parts assembly design failure, tool maker will raise 
the doubt before taking the order. After receiving the order it will be tool makers responsibility to provide the desired results of quality assembly of product. </li>
<li>Supplier will follow the mould standard given by Hongyi JIG for each order. </li>
<li>Tool supplier will provide the temperature Controller with each hot runner system. </li>
<li>All parts weight must be as per the CAD or bench marked part weights as per BOM. Only 2% daviation is acceptable. </li>
</ol>

<br><br>
<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">ORDER & ORDER CONFIRMATION 
</td>

</tr>
</table>

<ol style="font-size:10px;">
<li>All Orders are subjected to the date of release of PO signed copy from HJIG incase if advance payment release date lies with in 10 days of PO. The Lead time will be considered from the date of PO released. </li>
<li>No Terms of conditions will be acceptable seperately if not mentions in the PO released by HJIG and signed by the tool maker. </li>
<li> All POs will be considered "ACCEPTED" untill unless a written objection has been raised with in 24 hours over the same email id reply from which PO is received. </li>

</ol>


<br><br>
<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">TRIALS & TESTING
</td>

</tr>
</table>

<ol style="font-size:10px;">
<li>There will be 50 shots batch production of the molds which will be done exclusively to check the tool/mold quality. The Raw material will be bourne by the Tool maker with in the project cost. This will be on the basis of EXW delivery. </li>
<li> Supplier will ensure to achieve 90% accuracy on dimensions, surface finish and features of the part in the first trial of mould. </li>
<li>Tool maker company will check the trial samples of T-0 by himself and do the corrections without waiting for the feedback from client, to achive 100% ok dimesions based on the 3D CAD and 2D drawing Dimensions (Follow by the tolerances if given). </li>
<li>The Tool maker will send the inspection reports of the trialed samples along with the "moulding machine parameters screen shots in English Translated" to HJIG immidiately after 24 hours of mould trial in the following manner 
<ol style="font-size:10px;">
<li>Dimenstional inspection report of each part based on given dimensions and tolerances</li>
<li>Dimenstional inspection report of each part based on basic over all assembly dimensions in case if 2D drawing is missing </li>
<li>Visual inspection report of all the features of the components showing moulding defects / feature complition and surface finish. </li>


</ol>
</li>
<li>Minimum 5 sets of trial shots will be expressed by the tool maker by DHL fast express on each trial. </li>
<li>All mould trials are mendatory to made on the same size moulding machines as recomended by HJIG technical team / mutually decided at the time of agreement. </li>
<li>All the mould trials will be captured through a clear video with minimum 3 shots by keeping the mobile camera in a horizontal way. </li>
<li>All trial samples which has a size of more than 500 mm, must be packed in a wooden box to avoid any damages. </li>
<li>The supplier will do the Dry Run test at the time of final testing with the duration of 30 min for all moulds and share us their video in horizontal view. </li>
<li>In case of assembly parts, tool maker will check the drawings and designs feasibility of the meting parts and in case of any doubt on meting parts assembly design failure, tool maker will raise 
the doubt before taking the order. After receiving the order it will be tool makers responsibility to provide the desire results of quality assembly of product. </li>
<li>Tool maker will take the responsibility of checking the assembly flows and required ECNs to serve the quality product and make the correction at his own cost to deliver a good quality product without waiting for the customer feedback. </li>
<li>Tool maker will take a written approval for any kind of ECN modification on part or tool modification through a detailed PPT from HJIG</li>
<li>Tool maker will start the modification of each tool on FIFO basis (First In First Out) to save the tool correction time for the final mould trial. In special conditions, like in case results awaited for 
the meting part mould, the tool maker will take special permission from HJIG to hold the correction activities for the perticular part / mould. </li>
<li>In case there is assembly of the product and the moulds for meting parts are being made from another supplier in china, then it is resposibility of the supplier who is dealing with major parts of assembly that after tool trails, check for the assembly in proper with the meting parts and do the corrections as per feedback observed. </li>
<li>The tool maker will share the moulding parameters of the mould trial run through an Excel sheet specifically. 
</li>

</ol>

<br><br>
<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">COMMUNICATION
</td>

</tr>
</table>

<ol style="font-size:10px;">
<li>Tool maker will share the first master plan of manufacturing after receiving PO & before first advance payment & will share the micro plan after tooling kick off every week through an Excel Gantt Chart. 
</li>
<li>In case of any doubt or critical situations, tool maker or HJIG team will resolve amicably over a video conferencing call on Zoom, Skype, Webex. So supplier must be ready with these application on his laptop or desk top with speakers and microphone to take calls. </li>
<li>All communication must be in English Language. The supplier must have a good translator for project coordination in case if the concerned person can not speak English. </li>
<li> For each project there will be a whatsapp group for micro discussion. It is mandatory that the company Boss must be present and active in that group
.</li>
</ol>

<br><br>
<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">DELIVERY
</td>

</tr>
</table>

<ol style="font-size:10px;">
<li>All deliveries will be FOB including Batch productions. It will not matter at all whether Consignee book by Air/ Sea or LCL/FCL. Supplier has to pay the all FOB charges by himself to the 
"specified shipping agent by the client". </li>
<li>Before delivery Tool maker will supply the amended final mould design based on the corrections over the time period of multiple trials. </li>
<li>Before delivery, Tool maker will fill and supply the Final Moulding Parameters of each mould as per the Hongyi JIG specs sheet.</li>
<li>Tool maker will help Hongyi JIG to inspect the mould quality defined by HJIG Technical Team before delivery and share the pictures/videos evidence to prove. 
</li>
<li>All trialed samples which has a size of more than 500 mm, must be packed in a wooden box to avoid any damages and send to HJIG Office in India. And the freight cost will be bourne by Tool 
</li>
<li>All moulds must be packaged in a wooden case. For the weight above 1 Ton all moulds must have a Steel angle frame. </li>
<li>All Mould Material Certificates to be given on purchase of mould material along with delivery of moulds. </li>
<li> In case of Hot Runner moulds, the Hot runner certificate to be given along with moulds. </li>
<li>The Material Test Certificates and Hot Runner gurantee cards will be sent along with the Mould supplies would be your liability though not mentioned in our specifically.
</li>
<li>The supplier will do the Dry Run test at the time of final testing with the duration of 30 min for all moulds and share us their video by shooting the video by keeping mobile camera in horizontal position. </li>
</ol>


<br><br>
<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">SHIPMENT & TRANSFERS

</td>

</tr>
</table>

<ol style="font-size:10px;">
<li>All shipments related to tool & accessories, will be FOB including Batch productions. It will not matter at all whether Consignee book by Air/ Sea or LCL/FCL. Supplier has to pay the all FOB charges by himself to the "specified shipping agent by the client".  
</li>
<li>All shipments related to tool & accessories, will be FOB including Batch productions. It will not matter at all whether Consignee book by Air/ Sea or LCL/FCL. Supplier has to pay the all FOB charges by himself to the "specified shipping agent by the client". </li>
<li>Supplier will be responsible for "damage proof packaging" of goods to prevent any sort of breakage during shipment. (Whether by Air/Sea). In case of any damages, supplier will immediately manage to send an alternative material express by fool proof packaging. </li>
<li> Supplier will provide all the documents related to shipment such as

<ol>
<li> Bill of Lading as per given shipper and Consignee details </li>
<li>Commercial invoice and Packing List </li>
<li>Certificate of Origin </li>
</ol>
</li>
</ol>

<br><br>
<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">LEAD TIME


</td>

</tr>
</table>

<ol style="font-size:10px;">
<li>The Lead time will be considered & bounded from the date of Purchase Order (subjected to advance payment with in 10 days from the date of PO) to the delivery of moulds to Port (Subjected to evidence). Otherwise supplier will be liable to pay the penality fee to client. All milestone such as Tool Design, Tooling, trials and testing, batch production for tools quality and mould packaging etc. comes with in the lead time.  
</li>
<li> Any delay in the lead time of the moulds delivery more than 10%, will be subjected to penalty of 1% of the project cost (Total Moulds cost) per day. </li>
<li>Any project that deviates more than - 10 % from expected progress will be considered in arrears, and will require a written explanation and a plan to show capability of returning to schedule or 
justification for slippage.</li>
<li>A pilot batch production of 50 shots to check the tool quality is included in the project lead time for tools delivery. </li>
</ol>

<br><br>
<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">PAYMENTS & INVOICES



</td>

</tr>
</table>

<ol style="font-size:10px;">
<li>Payment terms will be 30% advance with PO, Mid payment @ 30% after T-0 samples approvals (subjected to 90% okay in dimensions/ surface finish & Features only) & 40% after final samples approval with 100% okay dimensiona as per drawings and proof of Moulds packed for shipme
</li>
<li>Mid payment is subjected to 90% okay in dimensions/ surface finish & Features only. </li>
<li> The last balance payment will be made after receiving Commercial Invoice and Packaging List from Supplier. </li>
<li>All payments will be made to the company account registered with HJIG
. </li>
</ol>

<br><br>
<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">WARRANTY & SERVICE 
</td>

</tr>
</table>

<ol style="font-size:10px;">
<li>In case after receiving the mould in India and during mould installation HJIG faces any issues or problems, the tool maker will support HJIG for trouble shooting the problems with in 8 working hours through video call etc. 
</li>
<li>Incase of any non performance of any mould, HJIG will get the tool repair from local resources and the proffessional fee will be bourne by Tool maker and refunded back with in 3 working days.  </li>
<li>Incase of any non performance of any mould, HJIG will get the tool repair from local resources and the proffessional fee will be bourne by Tool maker and refunded back with in 3 working days. . </li>

</ol>

<br><br>
<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">SPARES
</td>

</tr>
</table>

<ol style="font-size:10px;">
<li>Spare Mould accessaries and Standard parts will be supplied by the tool maker for each mould as below  
<ul>
<li>Spare Sprue Bush - Qty - 1 </li>
<li>Spare Ejector Pins - Qty 1 set as per the mould design</li>
<li>Mould lifting hooks - Qty - 2 
</li>
</ul>
</li>


</ol>


<br><br>
<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">DISPUTES 
</td>

</tr>
</table>

<ol style="font-size:10px;">
<li>In case of lockdowns, strikes or force majeure, we reserve the right to alter, modify postpone or cancel the order without any liability whatsoever. 
</li>
<li> Incase if any project got cancelled by the principle client before tooling kick off, (HJIG will be responsible to produce the evidences) the supplier will only charge 1% of the tool cost only for the moulds for which he has submitted the tool designs, and refund the entire amount of advance money to HJIG 
.  </li>
</ol>

<br><br>
<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">MOULD STANDARD
</td>

</tr>
</table>

<ol style="font-size:10px;">
<li>Supplier will follow Honyi JIG Mould standard as attached.  </li>
</ol>
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
