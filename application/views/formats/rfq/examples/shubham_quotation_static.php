<?php
ob_start();
define('filepath',$_SERVER['DOCUMENT_ROOT'].'/application/views/formats/rfq/examples/');
define('imagepaths',$_SERVER['DOCUMENT_ROOT'].'/application/views/formats/rfq/examples/images/');
//echo filepath; exit;
require_once(filepath.'tcpdf_include.php');
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Logo

       // if($this->page==2)
       // {
        //$this->SetMargins(10, 30, 10, 10);
        $image_file = imagepaths.'Header.jpg';
        //echo $image_file; exit;
        $this->Image($image_file, 0, 2, 200);
        // Set font
        $this->SetFont('helvetica', 'B', 20);
        // Title
        $this->Cell(0, 15, '', 0, false, 'C', 0, '', 0, false, 'M', 'M');
       // }


         $pageWidth = $this->getPageWidth();
        $pageHeight = $this->getPageHeight();

        // Set transparency
        $this->SetAlpha(0.5);  // Increase transparency to make the watermark lighter

        // Set the font for the watermark
        $this->SetFont('helvetica', 'B', 80);

        // Set a light gray color for the watermark text
        $this->SetTextColor(200, 200, 200);

        // Calculate x and y position for the watermark
        $watermarkText = "Shubham Pack";
        $textWidth = $this->GetStringWidth($watermarkText, 'helvetica', 'B', 80);
        $x = ($pageWidth / 2) - ($textWidth / 2);
        $y = ($pageHeight / 2)-(80/4); // Adjusted for font size

        // Rotate the text
        $this->StartTransform();
        $this->Rotate(45, $pageWidth / 2, $pageHeight / 2);

        // Add the watermark text
        $this->Text($x, $y, $watermarkText);

        // Stop the transformation
        $this->StopTransform();

        // Reset transparency
        $this->SetAlpha(1);

        // Reset text color to default
        $this->SetTextColor(0, 0, 0);
    }

    // Page footer
    public function Footer() {
    $this->SetY(-50);
    $logoX = -8; // 
    $logoFileName = imagepaths.'Footer.jpg';
    $logoWidth = 232; // 15mm
    $logoY = 270;
    $logo = $this->Image($logoFileName, $logoX, $logoY, $logoWidth);
    $this->SetX($this->w - 18 - $logoWidth); // documentRightMargin = 18
    $this->Cell(10,10, $logo, 0, 0, '');
    }
}



// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 065');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);



// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(15, PDF_MARGIN_TOP, 15, PDF_MARGIN_FOOTER);
$pdf->SetMargins(10, 25, 10, 20, true);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// define ('PDF_PAGE_ORIENTATION', 'L');

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetAutoPageBreak(TRUE, 2);
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
$pdf->setPrintFooter(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('times', '', 12, '', true);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// set text shadow effect
$pdf->setTextShadow(array('enabled'=>false, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

$CI=&get_instance();
$CI->load->model('Salescrm_model','salescrm');
$data=$CI->salescrm->getQuoteData($this->uri->segment(3),$pdf);
//echo $data; exit;
$html='


<table width="100%" ruled="all" style=" padding:3px;">
<tr>
<td style="text-align:left;"><strong>Ref. no. SPM/DOM/P/SR/0041/24-25</strong></td>
<td style="text-align:right;"><strong>Date: 30 May 2024</strong></td>
</tr>
</table>
<table style="padding-top:200px">
<tr>
<td>
<h2 style="text-align:center;">TECHNO COMMERCIAL <br>QUOTE FOR<br>
HFFS FLOW WRAP MACHINE MODEL SAURABH</h2>
</td></tr>
</table>
<table style="padding-top:20px">
<tr>
<td><i style="text-align:center; font-size:18px;">“Global Standards Unmatched Performance”</i></td>
</tr>
</table>

<table style="padding-top:150px">
<tr style="padding-left:300px">
<td style="text-align:center;">Specially prepared for<br>
<h2 style="font-size:22px;"><b>YOUR MEDIA MATE - GMVS COMPANY</b></h2>
<h4 style="font-size:18px;"><b>MANGLESH</b></h4><br>
<h5 style="font-size:18px;">GRENADA</h5>
</td>

</tr>
</table>

<table style="padding-top:100px">
<tr>
<td>
<b>Submitted by:</b><br>
Shubham Sharma<br>
Marketing<br>
Email: Shubham@shubhampack.com<br>
Cell: +91-8130192001<br>
</td>
</tr>
</table>

<br pagebreak="true">

<table>
<tr>
<td><h2 style="text-align:center;">Index</h2></td></tr>
</table>

<table style="padding-top:30px; padding-bottom:30px;">
<tr>
<td style="text-align:center; width:50%"><u>Annexure-I</u></td>
<td style="text-align:center; width:50%">Project Data</td>
</tr>
</table>

<table style="padding-top:30px; padding-bottom:30px;">
<tr>
<td style="text-align:center; width:50%"><u>Annexure-II</u></td>
<td style="text-align:center; width:50%">Technical Specifications</td>
</tr>
</table>

<table style="padding-top:30px; padding-bottom:30px;">
<tr>
<td style="text-align:center; width:50%"><u>Annexure-III</u></td>
<td style="text-align:center; width:50%">Exclusions</td>
</tr>
</table>

<table style="padding-top:30px; padding-bottom:30px;">
<tr>
<td style="text-align:center; width:50%"><u>Annexure-IV</u></td>
<td style="text-align:center; width:50%">Price Schedule & Optional Items</td>
</tr>
</table>

<table style="padding-top:30px; padding-bottom:30px;">
<tr>
<td style="text-align:center; width:50%"><u>Annexure-V</u></td>
<td style="text-align:center; width:50%">Commercial terms and conditions</td>
</tr>
</table><table style="padding-top:30px; padding-bottom:30px;">
<tr>
<td style="text-align:center; width:50%"><u>Annexure-VI</u></td>
<td style="text-align:center; width:50%">Machine Layout Image<br></td>
</tr>
</table><table style="padding-top:30px; padding-bottom:30px;">
<tr>
<td style="text-align:center; width:50%"><u>Annexure-VII</u></td>
<td style="text-align:center; width:50%">1 Year Consumable Spares<br></td>
</tr>
</table><br pagebreak="true">

<table >
<h2 style="text-align:center">Annexure-I<br>Project Data</h2>
</table>

<table border="1" style="padding:5px 5px 5px 5px;">
<tr style="padding:120px;">
    <td width="4%">1</td>
    <td width="48%">Product to be Packed</td>
    <td width="48%">Liquid</td>
</tr>

<tr style="padding-top:20px;">
    <td  width="4%">2</td>
    <td  width="48%">Product name</td>
    <td  width="48%">Multi-Track High Speed Continuous Machine Model SPM 1250P-10A-6T-TCF for 20g Pack & Changeover Parts of 80g Pack</td>
</tr>

<tr>
    <td width="4%">3</td>
    <td width="48%">Pouch Size & Type</td>
    <td width="48%">1001 X 1201 (mm)</td>
</tr>

<tr>
    <td width="4%">4</td>
    <td width="48%">Quantity to be packed</td>
    <td width="48%">1231 ml</td>
</tr><tr>
    <td width="4%">5</td>
    <td width="48%">Horizontal Sealing Width</td>
    <td width="48%">101mm</td>
</tr>
<tr>
    <td width="4%" >6</td>
    <td width="48%">Vertical Sealing Width</td>
    <td width="48%">51mm</td>
</tr>

<tr>
    <td width="4%">7<br/></td>
    <td width="48%" >Perforation Pitch</td>
    <td width="48%">31mm</td>
</tr>

<tr>
    <td width="4%">8<br/></td>
    <td width="48%" >Perforation Style</td>
    <td width="48%">Straight</td>
</tr>

<tr>
    <td width="4%">9<br/></td>
    <td width="48%" >Batch Cut</td>
    <td width="48%">Cutting</td>
</tr>

<tr>
    <td width="4%" ><br/>10</td>
    <td width="48%">Type of Sealing</td>
    <td width="48%">Knurling</td>
</tr>

<tr>
    <td width="4%" height="10%">11</td>
    <td width="48%" height="10%">PLC Make</td>
    <td width="48%" height="10%">Omron</td>
</tr>

<tr>
    <td width="4%" >12</td>
    <td width="48%">Power Supply</td>
    <td width="48%">415 VAC, 3Ph, 50 Hz1</td>
</tr><tr>
    <td width="4%">13</td>
    <td width="48%">Liquid Viscosity</td>
    <td width="48%">12</td>
</tr><tr>
    <td width="4%">14</td>
    <td width="48%">Liquid Conductivity</td>
    <td width="48%">32</td>
</tr></table>
<br pagebreak="true">

<table>
<tr style="text-align:center;">
<td><h2 style="text-align:center"><u><br/>Quotation Cum Technical Specification Of The Machine</u></h2></td>
</tr>
</table><br/><br/>

<table border="1" style="padding:5px 5px 5px 5px">
<tr>
<th style="width:10%; text-align:center; font-weight:bold; padding:10%;">Sr No.</th>
<th style="width:90%; text-align:center; font-weight:bold; padding:10%;">Description</th>
</tr>

<tr>
<td style="width:10%; text-align:center; padding:10%;">01</td>
<td style="width:90%; padding-left:10%;"><b>HFFS FLOW WRAP MACHINE MODEL 10A-6T-TCF FOR 20G PACK & CHANGEOVER PARTS OF 80G PACK</b>
<ul><li>Basic machine MS frame in welded structure.</li><li>test</li><li>test</li><li>test1</li></ul>
</td>
</tr>

<tr>
<td></td>
<td><b>No. of Axis in Machine: <strong>6</strong></b><ul style="padding:0px;"><li>weewe</li><li>dffd</li><li>fdf</li><li>testrtrtr</li><li>testrtrtr</li><li>saurabh</li></ul></td></tr>
</table><br pagebreak="true">

<table>
<tr>
    <td><h2 style="text-align:center;">Annexure-II<br>
    Technical Specifications</h2></td>
</tr>
<tr style="padding-top:30px">
<td></td>
</tr>
</table>

<table border="1" style="padding:5px 5px 5px 5px;">
<tbody style="padding-left:30px">
<tr>
<td>Machine Model </td>
<td><b>SAURABH</b></td>
</tr>

<tr>
<td>Sealing Style </td>
<td>4 Side Seal1</td>
</tr>

<tr>
<td>Speed</td>
<td>Design Speed- <p>For 20g Pack = **70-80 Strokes / min X no. of tracks = **70 X 6 Tracks = **420-480 Pouches/min = **420 X 60 Pouches/hr = **25200 X 20g = **504000 g/hr = **504kg/hr OR 0.50 ton/hr1</p>
<br><br>
Actual Speed-<p>For 80g Pack = **60-70 Strokes / min X no. of tracks = **60 X 5 Tracks = **300-350 Pouches/min = **300 X 60 Pouches/hr = **18000 X 80g = **1440000 g/hr = **144kg/hr OR 1.44 ton/hr1</p>
</td>
</tr>

<tr>
<td>No. of Tracks</td>
<td>For 20g Pack – 6 Tracks For 80g Pack – 5 Tracks1</td>
</tr>

<tr>
<td>Laminate specification (in mm) </td>
<td>Width-1250 mm Max1<br><br>
Max. Reel Dia-450 mm to 500 mm1<br><br>
Reel Core Dia-76/152 mm1<br></td>
</tr>
<tr>
<td>Product to be packed </td>
<td>Liquid</td>
</tr>
<tr>
<td>Filling capacity (in gram) </td>
<td>1231</td>
</tr><tr>
<td>Pouch Size (in mm)</td>
<td>1001 X 1201 X 1001 </td>
</tr>
<tr>
<td>Electrical Spec. </td>
<td><p>3, 415 V</p>

<p>Connected load &ndash; 28 KW</p>

<p>Consumption &ndash; 21 KW</p>

<p>PLC controlled Operations</p>

<p>RTD module with PLC for Temperature controllers1</p></td></tr>

<tr>
<td>Layout Dimensions (in mm)</td>
<td>3501<br>2601<br>3201<br></td>
</tr>

<tr>
<td>Machine Weight (in kg)</td>
<td>Net Weight -4201 kgs.<br>
Gross Weight -4301 kgs.</td>
</tr>

<tr>
<td style="padding: 8px;">Compressed Air </td>
<td style="padding: 8px;">Operating Pressure - 31 CFM CFM<br>
Consumption - 61 BAR BAR</td>
</tr>
</tbody>
</table>

<br pagebreak="true">
<table style="padding:10px 10px 10px 40px">
    <tr>
        <td style="text-align:center"><h2><u>Annexure-III<br>
Exclusion<br>
(To be provided by customer</u></h2></td>
    </tr>
</table>


<table style="padding:10px 10px 10px 10px">
<tr>
<td width="10%">1.0</td>
<td width="90%">Foundation and any civil building work</td>
</tr>
<tr>
<td width="10%">2.0</td>
<td width="90%">Dismantling of existing equipment, if any.</td>
</tr>
<tr style="padding:40px">
<td width="10%">3.0 </td>
<td width="90%">Compressed air piping including compressor</td>
</tr>
<tr style="padding:40px">
<td width="10%">4.0 </td>
<td width="90%">Incomer cable and power supply up to Shubham Pack control panel.</td>
</tr>
<tr style="padding:40px">
<td width="10%">5.0 </td>
<td width="90%">Voltage stabilizer of suitable capacity in case voltage and frequency variation is more
than 10% & 3% respectively at site</td>
</tr>
<tr style="padding:40px">
<td width="10%">6.0</td>
<td width="90%">Bulk material/Laminate during Factory acceptance test (FAT)</td>
</tr>
<tr style="padding:40px">
<td width="10%">7.0 </td>
<td width="90%">Special tools and tackles like cranes, lifts etc., at the time of installation.</td>
</tr>
<tr style="padding:40px">
<td width="10%">8.0</td>
<td width="90%">Skilled and unskilled man power at site along with qualified supervisor
to assist installation.</td>
</tr>
</table><br pagebreak="true">
<table>
<tr>
<td>
<h2 style="text-align:center;">Annexure-IV<br>
Price Schedule</h2>
</td>
</tr>
<tr>
<td><h3 style="text-align:center;">Line items below will be added as per the requirement</h3></td>
</tr>
</table><br/><br/>

<table style="width:100%; border-collapse: collapse; padding:5px 5px 5px 5px">
<thead>
<tr>
<th style="border: 1px solid black; padding: 14px; width:10%;font-weight:bold; text-align:center;"><br/>S.no.<br/></th>
<th style="border: 1px solid black; padding: 14px; width:40%;font-weight:bold;"><br/>Item description<br/></th>
<th style="border: 1px solid black; padding: 14px; width:20%;font-weight:bold;"><br/>Unit Price (EURO)<br/></th>
<th style="border: 1px solid black; padding: 14px; width:10%;font-weight:bold; text-align:center;"><br/>Qty<br/></th>
<th style="border: 1px solid black; padding: 14px; width:20%;font-weight:bold;"><br/>Total Price (EURO)<br/></th>
</tr>
</thead>
<tbody><tr>
<td style="border: 1px solid black; padding: 40px; width:10%; text-align:center;"></td>
        <td colspan="4" style="border: 1px solid black; padding: 40px;width:90%"><br/><u><strong>HFFS FLOW WRAP MACHINE MODEL SAURABH</strong></u><br/></td>
    </tr><tr>
        <td style="border: 1px solid black; padding: 40px; width:10%; text-align:center;"><br/>1<br/></td>
        <td style="border: 1px solid black; padding: 40px; width:40%"><br/>Price of design, manufacturing, supply of machine as per technical specifications at Annexure-II <strong>SAURABH</strong><br/></td>
            <td style="border: 1px solid black; padding: 40px; width:20%;text-align:right;"><br/>300<br/></td>
        <td style="border: 1px solid black; padding: 40px; width:10%;text-align:center;"><br/>200<br/></td>
    
        <td style="border: 1px solid black; padding: 40px; width:20%;text-align:right;"><br/><strong>60,000</strong><br/></td>
    </tr><tr>
        <td style="border: 1px solid black; padding: 14px; width:10%; text-align:center;"><br/>2<br/></td>
        <td style="border: 1px solid black; padding: 14px; width:40%"><br/>Case Errector<br/></td>
            <td style="border: 1px solid black; padding: 14px; width:20%;text-align:right;"><br/>34<br/></td>
        <td style="border: 1px solid black; padding: 14px; width:10%;text-align:center;"><br/>300<br/></td>
    
        <td style="border: 1px solid black; padding: 14px; width:20%;text-align:right;"><br/><strong>10,200</strong><br/></td>
    </tr><tr>
        <td style="border: 1px solid black; padding: 14px; width:10%; text-align:center;"><br/>3<br/></td>
        <td style="border: 1px solid black; padding: 14px; width:40%"><br/>Case Dropper<br/></td>
            <td style="border: 1px solid black; padding: 14px; width:20%;text-align:right;"><br/>56<br/></td>
        <td style="border: 1px solid black; padding: 14px; width:10%;text-align:center;"><br/>45<br/></td>
    
        <td style="border: 1px solid black; padding: 14px; width:20%;text-align:right;"><br/><strong>2,520</strong><br/></td>
    </tr><tr>
        <td style="border: 1px solid black; padding: 14px; width:10%; text-align:center;"><br/>4<br/></td>
        <td style="border: 1px solid black; padding: 14px; width:40%"><br/>Cobot 10kg<br/></td>
            <td style="border: 1px solid black; padding: 14px; width:20%;text-align:right;"><br/>100<br/></td>
        <td style="border: 1px solid black; padding: 14px; width:10%;text-align:center;"><br/>120<br/></td>
    
        <td style="border: 1px solid black; padding: 14px; width:20%;text-align:right;"><br/><strong>12,000</strong><br/></td>
    </tr><tr>
<td style="border: 1px solid black; padding: 14px; width:10%; text-align:center;"><br/>5<br/></td>
<td style="border: 1px solid black; padding: 14px; width:40%"><br/>1 Year Consumable Spares<br/>
</td>
<td style="border: 1px solid black; padding: 14px; width:20%;text-align:right;"><br/>9.00<br/></td>
<td style="border: 1px solid black; padding: 14px; width:10%;text-align:center;"><br/>367 Set<br/></td>

<td style="border: 1px solid black; padding: 14px; width:20%;text-align:right;"><strong><br/>16,059<br/></strong></td>
</tr><tr>
<td style="border: 1px solid black; padding: 8px; width:10%; text-align:center;"></td>
<td colspan="3" style="border: 1px solid black; padding: 8px; width:70%"><br/>Basic Cost (Ex-Works)<br/></td>
<td style="border: 1px solid black; padding: 8px; width:20%;text-align:right;"><br/><strong>100,779</strong><br/></td>

</tr><tr style="">
<td style="border: 1px solid black; padding: 8px; width:10%; text-align:center;"><br/>6<br/></td>
<td colspan="3" style="border: 1px solid black; padding: 8px; width:70%"><br/>Freight-Exclusive<br/></td>
<td style="border: 1px solid black; padding: 8px; width:20%;text-align:right;text-align:right;"><strong><br/>240.00<br/></strong>
</td>
</tr><tr style="">
<td style="border: 1px solid black; padding: 8px; width:10%; text-align:center;"><br/>7<br/></td>
<td colspan="3" style="border: 1px solid black; padding: 8px; width:70%"><br/>Packing Charges @2.50%<br/></td>
<td style="border: 1px solid black; padding: 8px; width:20%;text-align:right;"><strong><br/>2,519.475<br/></strong>
</td>
</tr><tr style="">
<td style="border: 1px solid black; padding: 8px; width:10%; text-align:center;"><br/>8<br/></td>
<td colspan="3" style="border: 1px solid black; padding: 8px; width:70%"><br/>Forwarding Charges @1.00%<br/></td>
<td style="border: 1px solid black; padding: 8px; width:20%;text-align:right;"><strong><br/>1,007.79<br/></strong></td>
</tr><tr style="">
<td style="border: 1px solid black; padding: 8px; width:10%; text-align:center;"><br/>9<br/></td>
<td colspan="3" style="border: 1px solid black; padding: 8px; width:70%"><br/>Insurance Charges @0.50%<br/></td>
<td style="border: 1px solid black; padding: 8px; width:20%;text-align:right;"><strong><br/>503.895<br/></strong>
</td>
</tr><tr>
<td style="border: 1px solid black; padding: 8px; width:10%; text-align:center;"></td>
<td colspan="3" style="border: 1px solid black; padding: 8px; width:70%"><br/><strong>FOB, Cost of machine<br/>EURO One Lakh Five Thousand Fifty   </strong><br/></td>
<td style="border: 1px solid black; padding: 8px; width:20%;text-align:right;"><br/><strong>105,050</strong><br/></td>
</tr></tbody> 
</table><br pagebreak="true"><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><table style="width:100%; border-collapse: collapse; padding:5px 5px 5px 5px">
<thead>
<tr style="background-color:#78cae3">
<th colspan="5" style="text-align:center;"><strong>Optional Accessories</strong></th>
</tr>
<tr>
<th style="border: 1px solid black; padding: 14px; width:10%;font-weight:bold;">S.no.</th>
<th style="border: 1px solid black; padding: 14px; width:40%;font-weight:bold;">Item description</th>
<th style="border: 1px solid black; padding: 14px; width:20%;font-weight:bold;">Unit Price (EURO)</th>
<th style="border: 1px solid black; padding: 14px; width:10%;font-weight:bold;">Qty</th>
<th style="border: 1px solid black; padding: 14px; width:20%;font-weight:bold;">Total Price (EURO)</th>
</tr>
</thead>
<tbody>

<tr>
<td style="border: 1px solid black; padding: 14px; width:10%">1</td>
<td style="border: 1px solid black; padding: 14px; width:40%">Bagging Unit
</td>
<td style="border: 1px solid black; padding: 14px; width:20%;text-align:right;">213</td>
<td style="border: 1px solid black; padding: 14px; width:10%;text-align:right;">120</td>

<td style="border: 1px solid black; padding: 14px; width:20%;text-align:right;"><strong>25,560</strong></td>
</tr>

<tr>
<td style="border: 1px solid black; padding: 14px; width:10%">2</td>
<td style="border: 1px solid black; padding: 14px; width:40%">Bagging Unit
</td>
<td style="border: 1px solid black; padding: 14px; width:20%;text-align:right;">213</td>
<td style="border: 1px solid black; padding: 14px; width:10%;text-align:right;">120</td>

<td style="border: 1px solid black; padding: 14px; width:20%;text-align:right;"><strong>25,560</strong></td>
</tr>

<tr>
<td style="border: 1px solid black; padding: 14px; width:10%">3</td>
<td style="border: 1px solid black; padding: 14px; width:40%">Bagging Unit
</td>
<td style="border: 1px solid black; padding: 14px; width:20%;text-align:right;">213</td>
<td style="border: 1px solid black; padding: 14px; width:10%;text-align:right;">120</td>

<td style="border: 1px solid black; padding: 14px; width:20%;text-align:right;"><strong>25,560</strong></td>
</tr>

<tr>
<td style="border: 1px solid black; padding: 14px; width:10%">4</td>
<td style="border: 1px solid black; padding: 14px; width:40%">Dust Collector SS-Make 5HP
</td>
<td style="border: 1px solid black; padding: 14px; width:20%;text-align:right;">3,221</td>
<td style="border: 1px solid black; padding: 14px; width:10%;text-align:right;">12231</td>

<td style="border: 1px solid black; padding: 14px; width:20%;text-align:right;"><strong>39,396,051</strong></td>
</tr>

</table><br pagebreak="true"><table style="padding:0px 5px 5px 0px">
<tr>
<td><h2 style="text-align:center"><u>Annexure-V<br>
Commercial Terms and Conditions</u></h2></td>
</tr>
<tr>
<td>
<h4><u>PRICE BASIS</u></h4>
</td></tr>
<tr>
<td>All prices are on FOB Port, Basis unless otherwise specified.</td>
</tr>
<tr><td><br/><h4><u>TERMS OF PAYMENT</u></h4></td></tr>
<tr>
<td>30% of Basic value as Advance ---- 60% of Basic value with Taxes & others after FAT ---- 10% of Basic value after commissioning
</td>
</tr>

<tr>
<td>Supplier’s Bankers details:<br></td>
</tr>
<tr>
<td>Axis Bank Ltd.<br>
SCO-40, Sec-7 Market<br>
Ballabgarh, Faridabad 121004<br>
Haryana, India<br>
A/c holder name- Shubham Flexible Packaging Machines Pvt. Ltd.<br>
A/c no.- 920030068344715<br>
Swift code- AXISINBB039<br></td>
</tr>
<tr>
<td>The Letter of Credit must permit <b>partial shipment</b> and transshipment and should be valid for
negotiation for a period of 21 days beyond the last permissible date of shipment.</td></tr>
<tr>
<td><h4><strong>The Letter of Credit must accept Combined Transport Bill of Lading issued by the Shipping
Company in New Delhi as a negotiable document.</strong></h4><br></td>
</tr>
<tr>
<td><h4><strong><u>PACKING CHARGES: -</u></strong></h4>
In strong seaworthy wooden boxes. 1% of forwarding charges will be applicable in case of terms other
than Ex – Works.</td>
</tr>

<tr>
<td>
<h4><br/><strong><u>INSURANCE: -</u></strong></h4>
Buyer’s responsibility to take suitable insurance for goods from seller’s warehouse in Ballabhgarh to
the port of discharge covering all risks including erection, installation and commissioning for 110 %
of CIF value. Documentary evidence of this insurance to be given to us at least 30 days before
shipment. Insurance will be applicable in case of terms other than Ex-works.
</td>
</tr>

<tr>
<td><br/><h4><strong><u>FREIGHT: -</u></strong></h4>
Exclusive</td>
</tr>

<tr>
<td><h4><strong><u>DELIVERY: -</u></strong></h4>
<p>Within 4 months from the date of receipt of advance or within 3 months from the date of receipt of LC, whichever is later.1</p></td>
</tr>

<tr>
<td><h4><strong><u>INSTALLATION / START- UP AND TRAINING</u></strong></h4>
<p>To be done by factory trained engineers of Shubham pack or its authorized sub suppliers. The
installation cost is indicated separately in the price schedule at Annexure-IV.</p>
<p>Standard tool kit will be provided along with machine <a href="https://pms.shubhampack.in/index.php/User/freesparelist" target="_blank">Click here to view standard toolkit</a></p>
</td>
</tr>
<tr>
<td><strong>Buyer has to provide Hotel, Food, local conveyance and medical expenses for all service
engineers at customer site.</strong></td>
</tr>

<tr>
<td>Service /installation charges are extra as per number of days required. Buyer has to bear expenses
for extra to and fro air fares, if any, stay in hotel, food, local transport and medical expenses for
deputing engineers and also reimburse out of pocket expenses <strong>@ EURO 3501</strong> per day per including the travel time and intervening holidays.</td>
</tr>

<tr>
<td>Local Labor, Power and other connected items including lifting, tackle, foundation and masonry work
during installation shall have to be provided by the Buyer.<br>
Buyer is advised to unload the machine from the truck/container and place it at designated place. It
is also advisable that inlet of bulk feed, air connection, power supply etc. should be made ready before
arrival of Installation engineer. However, all such connections as well as electrical power ON should
be done in presence of installation engineer only.</td>
</tr>

<tr>
<td><h4><strong><u>WARRANTY</u></strong></h4>
<p>We warranty for a period of 12 months from the date of erection of products at Buyer’s site,
all products and parts thereof, when properly installed, adjusted, operated and maintained
as per our proposal and / or the applicable technical manuals. This will however not
include components made of rubber, plastic and electrical equipment and other parts /
components subject to normal wear and tear.</p>
<p>Warranty does not cover consumables. These parts are considered as consumables and are required
to be paid for upon replacement.</p>
<p>Customer must buy critical and consumable spares for 24 months in order to avail comprehensive
warranty of 24 months.</p>
<p>Warranty does not cover damage of part due to poor preventive maintenance OR parts that are subject
to damage due to voltage fluctuation or improper voltage at buyer site.<br></p>
<p>Warranty does not apply to any equipment which has been improperly installed, adjusted, operated,
maintained, repaired or altered by unauthorized persons. We reserve the right to inspect any claimed
defect prior to replacement.</p>
<p>We will replace/repair, any defective material or workmanship, provided that we are given
written notice of the claimed defects above. Unless caused by us, equipment damaged by
overloading, exposure to corrosive or abrasive substance of abnormal dampness or other
misuse, neglect or accident, shall not be subject to the warranty set forth above.</p>
<p>The Warranty as referred to above clause will cease to operate if:<br>
a. The buyer, within the Warranty period, sells or otherwise parts with possession of the products.
 <p style="text-align:center">AND / OR</p>
b. Any local mechanic or electrician tampers with the Products without Shubham’ written
permission</p>

</td>
</tr>
<tr>
<td>
<h4><strong><u>LIABILITY</u></strong></h4>
<p>Liability of Shubham Flexible Packaging Machines Pvt. Ltd is limited to Warranty performance of
equipment and does not cover any aspect related to conversion of material. Shubham Flexible
Packaging Machines Pvt. Ltd undertakes no responsibility on performance of laminate/film converted
on the equipment. Customer must conduct trials at his own risk and cost, to evaluate and achieve
satisfactory results before commencing large-scale production. Machine speed and performance are
indicative and may vary with different substrates and raw material and may not be accurate. Liability of Shubham Flexible Packaging Machines Pvt. Ltd is only limited to replacement of defective parts and
does not cover any incidental or consequential loss to customer. </p>
</td>
</tr>
<tr>
<td><h4><strong><u>CANCELLATION</u></strong></h4><br>
<p>In the event of a request to stop work or to cancel any part of the order should be mutually discussed
between Supplier and Buyer.</p>
</td>
</tr>

<tr>
<td><h4><strong><u>FORCE MAJEURE:</u></strong></h4><br>
<p>We will not be responsible for any delay in delivery or for non-delivery of our products by reasons of
Force Majeure, such as acts of God, war, riots, civil disturbances, acts of authorities, strikes, lockouts
or other labour difficulties or any other circumstances beyond our control which might affect us or
our suppliers and hinder, impede or prevent deliveries. We will send the notice of force majeure to
customer in writing.</p>


</td>
</tr>


</table><table>
<tr>
<td>
<h4><strong><u>APPROVAL</u></strong></h4>
<p>Shubham Flexible Packaging Machines Pvt. Ltd will fully assemble the equipment prior to shipping
and a representative of the customer must approve the Equipment in writing prior to shipment. Any
modification or change suggested at this point will invalidate the delivery date clause and reasonable
time will be allowed to incorporate the modification.</p>

</td>
</tr>

<tr>
<td><h4><strong><u>ARBITRATION</u></strong></h4><br>
<p>Any dispute or differences whatsoever arising between the parties out of or relating to the construction,
meaning and operation or effect of this contract or the breach thereof shall be settled by arbitration in
accordance with the Rules of Arbitration of the Indian Council of Arbitration and the Award made in
pursuance thereof shall be binding on the parties.</p>
</td>
</tr>

<tr>
<td><h4><strong><u>VALIDITY:</u></strong></h4><br>
<p>This Contract and prices are valid for 30 days and supersedes all previous contracts. The terms and
conditions mentioned above shall supersede any terms and conditions agreed to earlier Performa
invoice if any.</p>
</td>
</tr>

<tr>
<td><h4><strong><u>GENERAL:</u></strong></h4><br>
<p>Continuous improvement is standard policy at Shubham. Accordingly, all specification and features
are subject to change without any prior notice.<br></p>
</td>
</tr>
</table><br pagebreak="true"><table>
            <tr>
            <td><h2 style="text-align:center"><u>Annexure-VI</h2></u></td>
            </tr>

            <tr>
            <td><img src="https://pms.shubhampack.in/image_bank/opportunitydocs/layoutimg/1718206572.png"></td>
            </tr></table><br pagebreak="true"><br/><br/><table style="width:100%; border-collapse: collapse; padding:5px 5px 5px 5px;" border="1">
<tr>
<th>
<h2 style="text-align:center;padding:">1 Year Consumable Spares</h2>
</th>
</tr><tr>
<td style="border: 1px solid black; padding: 8px;text-align:center;"><a href="">Click here to view the Consumable Spare</a></td>
</tr>
</table>';
//echo $html; exit;
$current_page = $pdf->getPage();

$getopprtunityuniquecode = $CI->salescrm->getopportunitygeneraterefno($this->uri->segment(3));
$version = $CI->salescrm->getopportunityversionfno($this->uri->segment(3));
//echo $version; exit;
$refrencenumber = str_replace('/', '_', $getopprtunityuniquecode);
// Print text using writeHTMLCell()

// echo $pdf->getNumPages(); exit;

$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);




// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.

ob_end_clean();
//$pdf->Output();
$filelocation = SITE_ROOT.'shubhamquotation/';
$fileNL='Quotation_'.$refrencenumber.'_V'.$version.'.pdf'; //Linux
//echo $fileNL; exit;
 $pdf->Output($filelocation.$fileNL, 'F');
 $pdf->Output($fileNL, 'I');

//============================================================+
// END OF FILE
//============================================================+
