<?php
$order_won_id = $this->uri->segment(3);
$CI = &get_instance();
$CI->load->model('TCPDF_model');
$packing_list = $CI->TCPDF_model->get_packing_list($order_won_id);
$lead_id = $CI->TCPDF_model->getLeadIDFromOrderWon_without_flag($order_won_id);
$project_name = $CI->TCPDF_model->getProjectName($lead_id);
$query_no = $CI->TCPDF_model->getLeadQueryno($lead_id);
$packing_list_details = $CI->TCPDF_model->get_packing_list_details($packing_list->id);
// echo "<pre>";
// print_r($packing_list_details);
/** QUERY ENDS **/


require_once('tcpdf_include.php');


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

// Call before the addPage() method
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
// set text shadow effect
$pdf->setTextShadow(array('enabled' => false, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

// Set some content to print

/** quert paet **/

$html = '

<table width="100%" style="padding:3px;">
    <tr>
    <td width="50%" style="text-align:center; font-size:30px; background-color:lightgrey; border:1px solid black; margin-top:50px;"><b style="">PACKING LIST</b></td>
        <td width="50%" style="text-align:right; font-size:25px;"><img src="https://www.hongyijig.com/wp-content/uploads/2021/01/HongyiJig-main-logo.png" width="150px"></td>
    </tr>
</table>


<br>
<br>
<br>
<br>
<table width="100%">
    <tr>
        <td width="45%">
          <table width="100%" style="padding:3px;" border="1" ruled="all">
                <tr>
                      <td width="50%" style="background-color:lightgrey;"><b>Project Name</b></td>
                      <td width="50%" >'.$project_name .'</td>
                </tr>
                <tr>
                      <td style="background-color:lightgrey;"><b>Query No.</b></td>
                      <td>'.$query_no.'</td>
                </tr>
            </table>

        </td>
        <td width="10%"></td>
        <td width="45%">
            <table width="100%" style="padding:3px;" border="1" ruled="all">
                <tr>
                      <td width="50%" style="background-color:lightgrey;"><b>Date</b></td>
                      <td width="50%">'.date('d-m-Y', strtotime($packing_list->pl_date)) .'</td>
                </tr>
                <tr>
                      <td style="background-color:lightgrey;"><b>Commerical Invoice Number</b></td>
                      <td>'.$packing_list->pl_invoice_number.'</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<br>
<br>
<br>
<table width="100%">
    <tr>
        <td width="45%">
            <table width="100%" style="padding:3px;" border="1" ruled="all">
                <tr>
                    <td colspan="2" style="text-align:center; background-color:lightgrey;"><b>SHIPPER</b></td>
                </tr>
                <tr>
                    <td style="background-color:lightgrey;">Company Name</td>
                    <td>'.$packing_list->pl_shipper_company_name.'</td>
                </tr>
                <tr>
                    <td style="background-color:lightgrey;">Company Address</td>
                    <td>'.$packing_list->pl_shipper_company_address.'</td>
                </tr>
                <tr>
                    <td style="background-color:lightgrey;">Company Person Name</td>
                    <td>'.$packing_list->pl_shipper_name.'</td>
                </tr>
                <tr>
                    <td style="background-color:lightgrey;">Company Person No.</td>
                    <td>'.$packing_list->pl_shipper_phone.'</td>
                </tr>
                <tr>
                    <td style="background-color:lightgrey;">Email Id</td>
                    <td>'.$packing_list->pl_shipper_email.'</td>
                </tr>
            </table>
        </td>
        <td width="10%"></td>
        <td width="45%">
            <table width="100%" style="padding:3px;" border="1" ruled="all">
                <tr>
                    <td colspan="2" style="text-align:center; background-color:lightgrey;"><b>CONSIGNEE</b></td>
                </tr>
                <tr>
                    <td style="background-color:lightgrey;">Company Name</td>
                    <td>'.$packing_list->pl_consignee_company_name.'</td>
                </tr>
                <tr>
                    <td style="background-color:lightgrey;">Company Address</td>
                    <td>'.$packing_list->pl_consignee_company_address.'</td>
                </tr>
                <tr>
                    <td style="background-color:lightgrey;">Company Person Name</td>
                    <td>'.$packing_list->pl_consignee_name.'</td>
                </tr>
                <tr>
                    <td style="background-color:lightgrey;">Company Person No.</td>
                    <td>'.$packing_list->pl_consignee_phone.'</td>
                </tr>
                <tr>
                    <td style="background-color:lightgrey;">Email Id</td>
                    <td>'.$packing_list->pl_consignee_email.'</td>
                </tr>
                <tr>
                    <td style="background-color:lightgrey;">IGST No.</td>
                    <td>'.$packing_list->pl_consignee_igst.'</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<br>
<br>
<table width="100%" style="padding:3px; text-align:center;" border="1" ruled="all">
    <tr>
        <th width="5%" style="background-color:lightgrey;"><b>S.NO.</b></th>
        <th width="26%" style="background-color:lightgrey;"><b>DESCRIPTION</b></th>
        <th width="10%" style="background-color:lightgrey;"><b>QTY</b></th>
        <th width="10%" style="background-color:lightgrey;"><b>NO. OF BOX</b></th>
        <th width="12%" style="background-color:lightgrey;"><b>NET WEIGTH (KG)</b></th>
        <th width="12%" style="background-color:lightgrey;"><b>GROSS WEIGHT (KG)</b></th>
        <th  style="background-color:lightgrey;"><b>BOX SIZE (MM</b>)</th>
        <th style="background-color:lightgrey;"><b>CBM</b></th>
    </tr>';

    $str = '';
    $pl_qty = array();
    $pl_no_box =array();
    $pl_net_weight =array();
    $pl_gross_weight= array();
    $pl_box_size= array();
    $pl_cbm =array();
    if($packing_list_details){ $i=1;
      foreach($packing_list_details as $packing){
        $str .= ' <tr>
        <td>'.$i.'</td>
        <td>'.$packing->pl_description.'</td>
        <td>'.$packing->pl_qty.'</td>
        <td>'.$packing->pl_no_box.'</td>
        <td>'.$packing->pl_net_weight.'</td>
        <td>'.$packing->pl_gross_weight.'</td>
        <td>'.$packing->pl_box_size.'</td>
        <td>'.$packing->pl_cbm.'</td>
    </tr>';
    $pl_qty[] = $packing->pl_qty;
    $pl_no_box[] = $packing->pl_no_box;
    $pl_net_weight[] = $packing->pl_net_weight;
    $pl_gross_weight[] =$packing->pl_gross_weight ;
    $pl_box_size[] = $packing->pl_box_size;
    $pl_cbm[] = $packing->pl_cbm;


     $i++; }
    }

    $html .= $str;

   $html .= '<tr>
        <td colspan="2" style="text-align: right;">Total</td>
        <td><input type="text">'.array_sum($pl_qty).'</td>
        <td><input type="text">'.array_sum($pl_no_box).'</td>
        <td><input type="text">'.array_sum($pl_net_weight).'</td>
        <td><input type="text">'.array_sum($pl_gross_weight).'</td>
        <td><input type="text">'.array_sum($pl_box_size).'</td>
        <td><input type="text">'.array_sum($pl_cbm).'</td>
    </tr>
</table>
<br>
<br>
<br>
<table width="100%" >
    <tr>
        <td width="45%">
        <table width="100%" style="padding:3px;" border="1" ruled="all">
                <tr>
                      <td width="50%" style="background-color:lightgrey;">SHIPMENT MODE</td>
                      <td width="50%">'.$packing_list->pl_shipment_mode.'</td>
                </tr>
                <tr>
                      <td style="background-color:lightgrey;">SHIPMENT TERM</td>
                      <td>'.$packing_list->pl_shipment_term.'</td>
                </tr>
            </table>
        </td>
        <td width="10%"></td>
        <td width="45%">
            <table width="100%" style="padding:3px;" border="1" ruled="all">
            <tr><td style="vertical-align: top;">
            SHIPPER STAMP AND SIGN <br>
            <img src="'.ASSETSUPLOADPATH.'b2_buyoff_packing/'.$packing_list->pl_stamp.'">
        </td></tr>
            </table>
        </td>
    </tr>
</table>
';

// <img src="'.ASSETSPATH.'b2_buyoff_packing/'.$packing_list->pl_stamp.'" alt="img" style="width:100px;">
//echo ASSETSPATH.'b2_buyoff_packing/'.$packing_list->pl_stamp; exit;
// b2_buyoff_packing/'.$packing_list->pl_stamp.'

//echo $footer_logo_html; exit;
// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.

// echo $html; exit;

$filelocation = $_SERVER['DOCUMENT_ROOT'] . '/image_bank/popdf';
$fileNL = $filelocation . "/akash.pdf"; //Linux

$pdf->Output($fileNL, 'I');



//============================================================+
// END OF FILE
//============================================================+
