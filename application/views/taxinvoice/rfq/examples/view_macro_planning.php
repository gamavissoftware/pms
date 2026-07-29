<?php
include('mysqlconfig.php');

$order_won_id = $_GET['order_won_id'];
$lead_id = $_GET['lead_id'];
$planning_id = $_GET['planning_id'];
$version = $_GET['version'];
$current = $_GET['current'];

$q = "SELECT id FROM bom_moulds_detail WHERE lead_id=$lead_id";
$resultq = mysqli_query($con, $q);
$total_moulds = mysqli_num_rows($resultq);

$query = "SELECT a.customer_name, a.unique_id, b.order_punched_on, c.first_name, c.last_name, d.project_name FROM leads a JOIN order_won b ON b.lead_id=a.id JOIN system_users c ON c.user_id=b.project_leader JOIN bom_pi_sales_client_info d ON d.lead_id=a.id WHERE a.id=$lead_id";
$results = mysqli_query($con, $query);
$datas = mysqli_fetch_array($results);

$order_punched_on = $datas['order_punched_on'];
$unique_id = $datas['unique_id'];
// echo $unique_id;exit;
$customer_name = $datas['customer_name'];
$project_leader = $datas['first_name'].' '.$datas['last_name'];
$project_name = $datas['project_name'];

$sql = "SELECT id FROM bom_pi_services_info WHERE lead_id=".$lead_id;
$result = mysqli_query($con, $sql);

if(mysqli_num_rows($result) == 0) {
    $chk = " WHERE common_stage_id != 1";
} else {
    $chk = '';
}



require_once('tcpdf_include.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

}
class MyCustomPDFWithWatermark extends TCPDF {
    public function Header() {
        // Get the current page break margin
        $bMargin = $this->getBreakMargin();

        // Get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;

        // Disable auto-page-break
        $this->SetAutoPageBreak(false, 0);

        // Define the path to the image that you want to use as watermark.
        $img_file = 'tcpdf/images/water.jpg';

        // Render the image
        $this->Image($img_file, 0, 0, 223, 280, '', '', '', false, 300, '', false, false, 0);

        // Restore the auto-page-break status
        $this->SetAutoPageBreak($auto_page_break, $bMargin);

        // Set the starting point for the page content
        $this->setPageMark();
    }
}


// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf = new MyCustomPDFWithWatermark(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
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
// $pdf->AddPage();

// set text shadow effect
$pdf->setTextShadow(array('enabled'=>false, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
$pdf->AddPage('L', 'A4');

$logo = '<img src="/var/www/hongyijig.in/pdf/logo.jpeg" width="200px">';
// Set some content to print

/** quert paet **/

$html .= '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<p style="text-align:center;">'.$logo.'</p>

    <p style="font-size: 18px;"><b>MACRO ORDER PLANNING SHEET</b></p>
    
<table>
    <tr>
        <td><table style="width:100%; font-size:10px; padding: 3px; text-align: left;" border="1">
            <tr>
                <td><b>Query No.</b></td>
                <td>'.$unique_id.'</td>
            </tr>
            <tr>
                <td><b>Project Name</b></td>
                <td>'.$project_name.'</td>
            </tr>
            <tr>
                <td><b>Client Name</b></td>
                <td>'.$customer_name.'</td>
            </tr>
        </table>
    </td>
        <td>
            <table style="width:100%; font-size:10px; padding: 3px; text-align: left;" border="1">
                <tr>
                    <td><b>Order Punching Date</b></td>
                    <td>'.date('d-m-Y H:i:s', strtotime($order_punched_on)).'</td>
                </tr>
                <tr>
                    <td><b>No. of Mould</b></td>
                    <td>'.$total_moulds.'</td>
                </tr>
                <tr>
                    <td><b>Project Leader</b></td>
                    <td>'.$project_leader.'</td>
                </tr>                
        </table>
    </td>
    </tr>
</table>
<br>
<br>';
$html .= '<table style="width:100%; font-size:10px; padding: 3px; text-align: center;" border="1">
    <tr>
        <td style="width:50px;"><b>S.NO.</b></td>
        <td style="width:160px;"><b>STAGE NAME AND LEVEL</b></td>
        <td><b>SOURCING</b></td>
        <td><b>SQL/PRL</b></td>
        <td style="width:150px;"><b>PRL STAGES</b></td>
        <td style="width:60px;"><b></b></td>
        <td><b>ACCOUNTABILITY</b></td>
        <td><b>TEAM MEMBER</b></td>
        <td><b>START DATE</b></td>
        <td><b>END DATE</b></td>
        <td style="width:50px;"><b>TOTAL DAYS</b></td>
    </tr>';

$sql1 = "SELECT * FROM macro_plan".$chk;
$result1 = mysqli_query($con, $sql1);

while($data1 = mysqli_fetch_array($result1)) {

    $stage_id = $data1['id'];

    if($current == 1) {
        $table_name1 = 'macro_order_planning_info';
        $table_name2 = 'macro_order_planning_team';
    } else {
        $table_name1 = 'macro_order_planning_info_history';
        $table_name2 = 'macro_order_planning_team_history';
    }

    $sql2 = "SELECT start_date, end_date, total_days,sourcing FROM $table_name1 WHERE planning_id=$planning_id AND tooling_time_id=$stage_id AND version=$version";

    $result2 = mysqli_query($con, $sql2);
    $rowcnt = mysqli_num_rows($result2);
    if($rowcnt>0)
    {
    $data2 = mysqli_fetch_array($result2);

    if($data2['sourcing']==1)
    {
        $source="IN-HOUSE";

    }else if($data2['sourcing']==2)
    {
        $source="OUT SOURCED";

    }else
    {
         $source="";
    }

    $sql3 = "SELECT a.id, a.team_member, a.sequence, a.prl_stage_id, b.first_name, b.last_name FROM $table_name2 a JOIN system_users b ON b.user_id=a.team_member WHERE order_won_id=$order_won_id AND stage_id=$stage_id AND version=$version";
    $result3 = mysqli_query($con, $sql3);
    $data3 = mysqli_fetch_array($result3);

    if($data3['sequence'] == 1) {
        $sequence = 'SEQUENTIAL';
    } else if($data3['sequence'] == 2) {
        $sequence = 'PARALLEL';
    } else {
        $sequence = '';
    }

    if($data3['prl_stage_id'] != 0) {
        $sql4 = "SELECT id, stage_name_level FROM macro_plan WHERE id=".$data3['prl_stage_id'];
        // echo $sql4;exit;
        $result4 = mysqli_query($con, $sql4);
        $data4 = mysqli_fetch_array($result4);      
        $prl_stage = $data4['stage_name_level'];  
    } else {
        $prl_stage = '-';
    }
    
    $html .= '<tr>
                <td>'.$data1['stage'].'</td>
                <td>'.$data1['stage_name_level'].'</td>
                <td>'.$source.'</td>
                <td>'.$sequence.'</td>
                <td>'.$prl_stage.'</td>
                <td>'.$data1['sub_stage'].'</td>
                <td>'.$data1['accountability'].'</td>
                <td>'.$data3['first_name'].' '.$data3['last_name'].'</td>
                <td>'.date('d-m-Y', strtotime($data2['start_date'])).'</td>
                <td>'.date('d-m-Y', strtotime($data2['end_date'])).'</td>
                <td>'.$data2['total_days'].'</td>
              </tr>';
}

}

$html .= '</table>';

$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.



$filelocation = $_SERVER['DOCUMENT_ROOT'].'/image_bank/popdf';
$fileNL = $filelocation."/akash.pdf"; //Linux

$pdf->Output($fileNL, 'I');