<?php
$uriflag=$this->uri->segment(4);
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies
ob_start();
$pageurl = page_url."Opportunity/previewquoteandsendforapproval/";
//define('pageurl',$_SERVER['DOCUMENT_ROOT'].'/application/views/formats/rfq/examples/');
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
    // public function Footer() {
    //     // reserve 35mm footer area (same as auto-page-break margin)
    //     $footerHeight = 35;

    //     // move to start of footer area
    //     $this->SetY(-$footerHeight);

    //     $logoX        = 0; // left edge
    //     $logoWidth    = 210; // full A4 width in mm
    //     $logoFileName = imagepaths.'Footer.jpg';

    //     // draw footer image within reserved footer band
    //     $this->Image($logoFileName, $logoX, $this->GetY(), $logoWidth);
    // }


    public function Footer() {

    $footerHeight = 35;

    // Move to footer start
    $this->SetY(-$footerHeight);

    $logoX        = 0;
    $logoWidth    = 210;
    $logoFileName = imagepaths.'Footer.jpg';

    // Footer Image
    $this->Image($logoFileName, $logoX, $this->GetY(), $logoWidth);

    // -------------------------
    // PAGE NUMBER
    // -------------------------

    $this->SetY(-10); // Position above bottom edge
    $this->SetFont('helvetica', '', 9);
    $this->SetTextColor(0, 0, 0);

    // Option 1 → Page 1 of 4
    $pageText = 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages();

    // Option 2 → 1 / 4
    // $pageText = $this->getAliasNumPage().' / '.$this->getAliasNbPages();

    $this->Cell(0, 5, $pageText, 0, false, 'R');
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

// ---- UPDATED MARGINS & AUTOPAGEBREAK (for footer space) ----
$footerHeight = 35; // space reserved at bottom for footer image

// set margins (left, top, right)
$pdf->SetMargins(10, 25, 10);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin($footerHeight);

// set auto page breaks with same footer space
$pdf->SetAutoPageBreak(TRUE, $footerHeight);
// ------------------------------------------------------------

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

// (no extra SetAutoPageBreak here – already set above)

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// set text shadow effect
$pdf->setTextShadow(array('enabled'=>false, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

$CI=&get_instance();
$CI->load->model('Salescrm_model','salescrm');
$data=$CI->salescrm->getQuoteData($this->uri->segment(3),$pdf);
//echo $data; exit;
$html=$data;
//echo $html; exit;
$current_page = $pdf->getPage();

$getopprtunityuniquecode = $CI->salescrm->getopportunitygeneraterefno($this->uri->segment(3));
$version = $CI->salescrm->getopportunityversionfno($this->uri->segment(3));
$leadid = $CI->salescrm->getleadidfromsalescrm($this->uri->segment(3));
// echo $leadid; exit;
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
// $pdf->Output($fileNL, 'I');
if($uriflag=="Open")
{
    $pdf->Output($filelocation.$fileNL, 'F');
    $pdf->Output($fileNL, 'I');
}else
{
header('location:'.$pageurl.$this->uri->segment(3)."/".$leadid);
}

//============================================================+
// END OF FILE
//============================================================+
