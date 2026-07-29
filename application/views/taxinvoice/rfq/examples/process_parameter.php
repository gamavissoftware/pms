<?php
$order_won_id = $this->uri->segment(3);
$CI = &get_instance();
$CI->load->model('TCPDF_model');
$lead_id = $CI->TCPDF_model->getLeadIDFromOrderWon_without_flag($order_won_id);
$project_name = $CI->TCPDF_model->getProjectName($lead_id);
$query_no = $CI->TCPDF_model->getLeadQueryno($lead_id);
$process_parameter = $CI->TCPDF_model->get_process_parameter($order_won_id);
$buyoff_process_hrtc = $CI->TCPDF_model->buyoff_process_hrtc($process_parameter->id);
$process_injection_position = $CI->TCPDF_model->buyoff_process_injection_position($process_parameter->id);
$process_injection_speed = $CI->TCPDF_model->buyoff_process_injection_speed($process_parameter->id);
$process_injection_pressure = $CI->TCPDF_model->buyoff_process_injection_pressure($process_parameter->id);
$process_injection_prs = $CI->TCPDF_model->buyoff_process_injection_prs($process_parameter->id);
$process_injection_spd = $CI->TCPDF_model->buyoff_process_injection_spd($process_parameter->id);
$process_injection_time = $CI->TCPDF_model->buyoff_process_injection_time($process_parameter->id);
$process_forward_speed = $CI->TCPDF_model->buyoff_process_forward_speed($process_parameter->id);
$process_retract_speed = $CI->TCPDF_model->buyoff_process_retract_speed($process_parameter->id);

$process_forward_pressure = $CI->TCPDF_model->buyoff_process_forward_pressure($process_parameter->id);
$process_retract_pressure = $CI->TCPDF_model->buyoff_process_retract_pressure($process_parameter->id);
$process_forward_position = $CI->TCPDF_model->buyoff_process_forward_position($process_parameter->id);
$process_retract_position = $CI->TCPDF_model->buyoff_process_retract_position($process_parameter->id);



// echo "<pre>";
// print_r($process_parameter);






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


// set text shadow effect
$pdf->setTextShadow(array('enabled' => false, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

// Set some content to print

/** quert paet **/

$html = '

<table width="100%">
<tr>
<td width="20%"></td>
<td width="60%" style="text-align:center;"><h2>Process Parameter Sheet (Molding)</h2></td>
<td width="20%">
<img src="https://www.hongyijig.com/wp-content/uploads/2021/01/HongyiJig-main-logo.png" alt="logo" width="200px">
</td>
</tr>
</table>


<table>
<tr>
<td width="40%">
<table border="1" style="width:100%; padding:3px;">
<tr>
    <td style="background-color:lightgrey;">Project Name</td>
    <td style="font-weight:bold;">'.$project_name.'</td>
</tr>
<tr>
    <td style="background-color:lightgrey;">Query No.</td>
    <td style="font-weight:bold;">'.$query_no.'</td>
</tr>
</table>
</td>
<td width="20%"></td>
<td width="40%">
</td>
</tr>
</table>


<h4>PROCESS CONTROL SHEET (INJECTION MOLDING)</h4>
<table>
<tr>
<td width="40%">
<table border="1" style="width:100%; padding:3px;">
<tr>
    <td style="background-color:lightgrey;">Machine Ton</td>
    <td>'.$process_parameter->machine_ton.'</td>
</tr>
<tr>
    <td style="background-color:lightgrey;">CUSTOMER</td>
    <td>'.$process_parameter->customer.'</td>
</tr>
<tr>
    <td style="background-color:lightgrey;">RAW MATERIAL</td>
    <td>'.$process_parameter->raw_material.'</td>
</tr>
<tr>
    <td style="background-color:lightgrey;">MAT. GRADE</td>
    <td>'.$process_parameter->mat_grade.'</td>
</tr>
<tr>
    <td style="background-color:lightgrey;">PREHEATING TEMP./TIME</td>
    <td>'.$process_parameter->preheating_temp.'</td>
</tr>
<tr>
    <td style="background-color:lightgrey;">MB % & COLOUR</td>
    <td>'.$process_parameter->mb_colour.'</td>
</tr>
</table>
</td>
<td width="20%"></td>
<td width="40%">

<table border="1" style="width:100%; padding:3px; ">
                                <tr>
                                    <td style="background-color:lightgrey;">SHOT WT. (GMS)</td>
                                    <td>'.$process_parameter->shot_wt.'</td>
                                </tr>
                                <tr>
                                    <td style="background-color:lightgrey;">SHOT WT. (GMS)</td>
                                    <td>'.$process_parameter->shot_wt_gms.'</td>
                                </tr>
                                <tr>
                                    <td style="background-color:lightgrey;">RUNNER WT. (GMS)</td>
                                    <td>'.$process_parameter->runner_wt.'</td>
                                </tr>
                                <tr>
                                    <td style="background-color:lightgrey;">PART WT.(GMS)</td>
                                    <td>'.$process_parameter->part_wt.'</td>
                                </tr>
                                <tr>
                                    <td style="background-color:lightgrey;">NO. OF CAVITY</td>
                                    <td>'.$process_parameter->cavity.'</td>
                                </tr>
                            </table>
</td>
</tr>
</table>
<br><br>
<h4>TEMPERATURES</h4>

<table border="1" style="width:100%; padding:3px;">
<tr>
    <th width="15%" style="text-align:center; background-color:lightgrey;">&#8451;</th>
    <th  width="10%" style="text-align:center; background-color:lightgrey;">NH</th>
    <th  width="10%" style="text-align:center; background-color:lightgrey;">Z1</th>
    <th  width="10%" style="text-align:center; background-color:lightgrey;">Z2</th>
    <th  width="10%" style="text-align:center; background-color:lightgrey;">Z3</th>
    <th  width="10%" style="text-align:center; background-color:lightgrey;">Z4</th>
    <th  width="10%" style="text-align:center; background-color:lightgrey;">Z5</th>
    <th  colspan="5" width="25%" style="text-align:center; background-color:lightgrey;">HRTC</th>
</tr>
<tr>
    <td style="text-align:center;">SET <b>&#8451;</b></td>
    <td style="text-align:center;">'.$process_parameter->nh.'</td>
    <td style="text-align:center;">'.$process_parameter->z1.'</td>
    <td style="text-align:center;">'.$process_parameter->z2.'</td>
    <td style="text-align:center;">'.$process_parameter->z3.'</td>
    <td style="text-align:center;">'.$process_parameter->z4.'</td>
    <td style="text-align:center;">'.$process_parameter->z5.'</td>

    <td style="text-align:center;">'.$buyoff_process_hrtc->hrtc1.'</td>
    <td style="text-align:center;">'.$buyoff_process_hrtc->hrtc2.'</td>
    <td style="text-align:center;">'.$buyoff_process_hrtc->hrtc3.'</td>
    <td style="text-align:center;">'.$buyoff_process_hrtc->hrtc4.'</td>
    <td style="text-align:center;">'.$buyoff_process_hrtc->hrtc5.'</td>
</tr>
</table>

<br><br>


<table>
<tr>
<td width="50%">
<h4>INJECTION</h4>
<table border="1" style="width:100%; padding:3px;">
<tr>
<td width="40%" style="background-color:lightgrey;">POSITION (MM)</td>
<td width="20%">'.$process_injection_position->position1.'</td>
<td width="20%">'.$process_injection_position->position2.'</td>
<td width="20%">'.$process_injection_position->position3.'</td>
</tr>
<tr>
<td style="background-color:lightgrey;">SPEED (MM/SEC.)
</td>
<td>'.$process_injection_speed->speed1.'</td>
<td>'.$process_injection_speed->speed2.'</td>
<td>'.$process_injection_speed->speed3.'</td>
</tr>
<tr>
<td style="background-color:lightgrey;">PRESSURE (BAR)</td>
<td>'.$process_injection_pressure->pressure1.'</td>
<td>'.$process_injection_pressure->pressure2.'</td>
<td>'.$process_injection_pressure->pressure3.'</td>
</tr>
<tr>
<td style="background-color:lightgrey;">PACK / HOLD PRS.(BAR)</td>
<td>'.$process_injection_prs->psr1.'</td>
<td>'.$process_injection_prs->psr2.'</td>
<td>'.$process_injection_prs->psr3.'</td>
</tr>
<tr>
<td style="background-color:lightgrey;">PACK / HOLD SPD.(MM/SEC)</td>
<td>'.$process_injection_spd->spd1.'</td>
<td>'.$process_injection_spd->spd2.'</td>
<td>'.$process_injection_spd->spd3.'</td>
</tr>
<tr>
<td style="background-color:lightgrey;">PACK / HOLD TIME(Sec)</td>
<td>'.$process_injection_time->time1.'</td>
<td>'.$process_injection_time->time2.'</td>
<td>'.$process_injection_time->time3.'</td>
</tr>
</table>
</td>
<td width="10%"></td>
<td width="40%">
<h4>REFILL</h4>
<table border="1" style="width:100%; padding:3px; ">
<tr>
<td width="60%" style="background-color:lightgrey;">STEP</td>
<td  width="40%">'.$process_parameter->refil_step.'</td>
</tr>
<tr>
<td   style="background-color:lightgrey;">PRESSURE (BAR)</td>
<td>'.$process_parameter->refil_pressure_bar.'</td>
</tr>
<tr>
<td   style="background-color:lightgrey;">SPEED (MM/SEC.) / RPM</td>
<td>'.$process_parameter->refil_speed.'</td>
</tr>
<tr>
<td   style="background-color:lightgrey;">BACK PRESSURE (BAR)</td>
<td>'.$process_parameter->refil_back_pressure.'</td>
</tr>
<tr>
<td   style="background-color:lightgrey;">POSITION (mm)</td>
<td>'.$process_parameter->refil_position.'</td>
</tr>
   </table>
</td>
</tr>
</table>
<br><br>
<br><br>
<h4>EJECTOR</h4>

<table border="1" style="width:100%; padding:3px; ">
<tr>
                                    <td width="23%" style="background-color:lightgrey;"></td>
                                    <td colspan="5" width="35%" style="background-color:lightgrey; text-align:center;">FORWARD</td>
                                    <td width="7%" style="background-color:lightgrey;"></td>
                                    <td colspan="5"  width="35%" style="background-color:lightgrey; text-align:center;">RETRACT</td>
                                </tr>
                                <tr>
                                    <td width="23%">SPEED (MM/SEC.)</td>
                                    <td width="7%">'.$process_forward_speed->speed1.'</td>
                                    <td width="7%">'.$process_forward_speed->speed2.'</td>
                                    <td width="7%">'.$process_forward_speed->speed3.'</td>
                                    <td width="7%">'.$process_forward_speed->speed4.'</td>
                                    <td width="7%">'.$process_forward_speed->speed5.'</td>
                                    <td ></td>
                                    <td width="7%">'.$process_retract_speed->speed1.'</td>
                                    <td width="7%">'.$process_retract_speed->speed2.'</td>
                                    <td width="7%">'.$process_retract_speed->speed3.'</td>
                                    <td width="7%">'.$process_retract_speed->speed4.'</td>
                                    <td width="7%">'.$process_retract_speed->speed5.'</td>
                                </tr>
                                <tr>
                                    <td>PRESSURE (BAR)</td>
                                    <td>'.$process_forward_pressure->pressure1.'</td>
                                    <td>'.$process_forward_pressure->pressure2.'</td>
                                    <td>'.$process_forward_pressure->pressure3.'</td>
                                    <td>'.$process_forward_pressure->pressure4.'</td>
                                    <td>'.$process_forward_pressure->pressure5.'</td>
                                    <td ></td>
                                    <td>'.$process_retract_pressure->pressure1.'</td>
                                    <td>'.$process_retract_pressure->pressure2.'</td>
                                    <td>'.$process_retract_pressure->pressure3.'</td>
                                    <td>'.$process_retract_pressure->pressure4.'</td>
                                    <td>'.$process_retract_pressure->pressure5.'</td>
                                </tr>
                                <tr>
                                    <td>POSITION (MM)</td>
                                    <td>'.$process_forward_position->position1.'</td>
                                    <td>'.$process_forward_position->position2.'</td>
                                    <td>'.$process_forward_position->position3.'</td>
                                    <td>'.$process_forward_position->position4.'</td>
                                    <td>'.$process_forward_position->position5.'</td>
                                    <td ></td>
                                    <td>'.$process_retract_position->position1.'</td>
                                    <td>'.$process_retract_position->position2.'</td>
                                    <td>'.$process_retract_position->position3.'</td>
                                    <td>'.$process_retract_position->position4.'</td>
                                    <td>'.$process_retract_position->position5.'</td>
                                </tr>
</table>

<br><br>

<table width="100%" >    
<tr>
<td width="40%">
<h4>GENERAL</h4>
<table border="1" style="width:100%; padding:3px; ">
                                <tr>
                                    <td width="60%" style="background-color:lightgrey;">INJECTION TIME (SEC.)</td>
                                    <td width="40%">'.$process_parameter->injection_time.'</td>
                                </tr>
                                <tr>
                                    <td style="background-color:lightgrey;">ROT. TIME DELAY (SEC.) </td>
                                    <td>'.$process_parameter->rot_time_delay.'</td>
                                </tr>
                                <tr>
                                    <td style="background-color:lightgrey;">COOLING TIME (SEC.)</td>
                                    <td>'.$process_parameter->cooling_time.'</td>
                                </tr>
                                <tr>
                                    <td style="background-color:lightgrey;">CYCLE TIME (SEC.)</td>
                                    <td>'.$process_parameter->cycle_time.'</td>
                                </tr>
                                <tr>
                                    <td style="background-color:lightgrey;">TARGET/HOUR</td>
                                    <td>'.$process_parameter->target.'</td>
                                </tr>
                                <tr>
                                    <td style="background-color:lightgrey;">TEMP.CTRL FIX HALF</td>
                                    <td>'.$process_parameter->temp_ctrl_fix_half.'</td>
                                </tr>
                                <tr>
                                    <td style="background-color:lightgrey;">TEMP. CTRL MOV. HALF</td>
                                    <td>'.$process_parameter->temp_ctrl_mov_half.'</td>
                                </tr>
                                <tr>
                                    <td style="background-color:lightgrey;">MTC / MOULD TEMP.</td>
                                    <td>'.$process_parameter->mtc_mould_temp.'</td>
                                </tr>
</table>
</td>
<td width="5%"></td>
<td width="55%">
<h4 style="text-align:center;">TIMER</h4>
<table border="1" style="width:100%; padding:3px; ">
<tr>
                                    <td width="40%" style="background-color:lightgrey;">TRANSFER POSITION (MM)</td>
                                    <td width="10%" style="text-align:center;">'.$process_parameter->transfer_position.'</td>
                                    <td width="10%" >TIMER</td>
                                    <td width="10%" >TIME</td>
                                    <td width="10%" >DELAY</td>
                                    <td width="10%" >TIMER</td>
                                    <td width="10%" >TIME</td>
                                </tr>
                                <tr>
                                    <td style="background-color:lightgrey;">SHOT SIZE (MM)</td>
                                    <td style="text-align:center;">'.$process_parameter->shot_size.'</td>
                                    <td style="text-align:center;">'.$process_parameter->shot_size_timer.'</td>
                                    <td style="text-align:center;">'.$process_parameter->shot_size_time.'</td>
                                    <td style="text-align:center;">'.$process_parameter->shot_size_delay.'</td>
                                    <td style="text-align:center;">'.$process_parameter->shot_size_delay_timer.'</td>
                                    <td style="text-align:center;">'.$process_parameter->shot_size_delay_time.'</td>
                                </tr>
                                <tr>
                                    <td style="background-color:lightgrey;">SUCK BACK (MM)</td>
                                    <td style="text-align:center;">'.$process_parameter->suck_back.'</td>
                                    <td style="text-align:center;">'.$process_parameter->suck_back_timer.'</td>
                                    <td style="text-align:center;">'.$process_parameter->suck_back_time.'</td>
                                    <td style="text-align:center;">'.$process_parameter->suck_back_delay.'</td>
                                    <td style="text-align:center;">'.$process_parameter->suck_back_delay_timer.'</td>
                                    <td style="text-align:center;">'.$process_parameter->suck_back_delay_time.'</td>
                                </tr>
                                <tr>
                                    <td style="background-color:lightgrey;">MOULD OPEN END (MM)</td>
                                    <td style="text-align:center;">'.$process_parameter->mould_open_end.'</td>
                                </tr>
                                <tr>
                                    <td style="background-color:lightgrey;">EJECT COUNT (NOS)</td>
                                    <td style="text-align:center;">'.$process_parameter->eject_count.'</td>
                                </tr>
                                <tr>
                                    <td style="background-color:lightgrey;">EJECTOR FOR TIME</td>
                                    <td style="text-align:center;">'.$process_parameter->ejector_for_time.'</td>
                                </tr>
                                <tr>
                                    <td style="background-color:lightgrey;">CLAMPING FORCE (TONS)</td>
                                    <td style="text-align:center;">'.$process_parameter->clamping_force.'</td>
                                </tr>
                                <tr>
                                    <td style="background-color:lightgrey;">CUSHION (MM)</td>
                                    <td style="text-align:center;">'.$process_parameter->cushion.'</td>
                                </tr>
</table>
</td>
</tr>
</table>

<br><br>';

if($process_parameter->ejector_plate_back == 1){
  $ejector_plate_back = 'Yes';
}else if($process_parameter->ejector_plate_back == 0){
  $ejector_plate_back = 'No';
}else {
  $ejector_plate_back = '';
}

if($process_parameter->indicated_with_stopper == 1){
  $indicated_with_stopper = 'Yes';
}else if($process_parameter->indicated_with_stopper == 0){
  $indicated_with_stopper = 'No';
}else {
  $indicated_with_stopper = '';
}

if($process_parameter->marking_position_io == 1){
  $marking_position_io = 'Yes';
}else if($process_parameter->marking_position_io == 0){
  $marking_position_io = 'No';
}else {
  $marking_position_io = '';
}


if($process_parameter->hydraulic_connections == 1){
  $hydraulic_connections = 'Yes';
}else if($process_parameter->hydraulic_connections == 0){
  $hydraulic_connections = 'No';
}else {
  $hydraulic_connections = '';
}


if($process_parameter->no_oil_leakage == 1){
  $no_oil_leakage = 'Yes';
}else if($process_parameter->no_oil_leakage == 0){
  $no_oil_leakage = 'No';
}else {
  $no_oil_leakage = '';
}


if($process_parameter->hydraulic_rotation_movement == 1){
  $hydraulic_rotation_movement = 'Yes';
}else if($process_parameter->hydraulic_rotation_movement == 0){
  $hydraulic_rotation_movement = 'No';
}else {
  $hydraulic_rotation_movement = '';
}


if($process_parameter->cooling_connection == 1){
  $cooling_connection = 'Yes';
}else if($process_parameter->cooling_connection == 0){
  $cooling_connection = 'No';
}else {
  $cooling_connection = '';
}


if($process_parameter->cooling_nipples_size == 1){
  $cooling_nipples_size = 'Yes';
}else if($process_parameter->cooling_nipples_size == 0){
  $cooling_nipples_size = 'No';
}else {
  $cooling_nipples_size = '';
}



$html .= '<table width="100%" border="1" >
<tr>
<td width="30%">
<h4>Ejection Side</h4>
<table border="1" style="width:100%; padding:3px; ">
                                <tr>
                                    <td width="75%"></td>
                                    <td width="25%" style="text-align:center;">YES/NO</td>
                                </tr>
                                <tr>
                                    <td width="75%" style="background-color:lightgrey;">There are springs for ensuring the ejector plate back</td>
                                    <td width="25%" style="text-align:center;">'.$ejector_plate_back.' </td>
                                </tr>
                                <tr>
                                    <td style="background-color:lightgrey;">Ejection stroke is indicated with stopper (Limit /Switch)</td>
                                    <td style="text-align:center;">'.$indicated_with_stopper.'</td>
                                </tr>
</table>
</td>
<td width="5%"></td>
<td width="30%">
<h4>Hydraulic Sliders</h4>
<table border="1" style="width:100%; padding:3px; ">
                                <tr>
                                    <td width="75%"></td>
                                    <td width="25%" style="text-align:center;">YES/NO</td>
                                </tr>
                                <tr>
                                    <td width="75%" style="background-color:lightgrey;">Marking position input and output</td>
                                    <td width="25%" style="text-align:center;">'.$marking_position_io.'</td>
                                </tr>
                                <tr>
                                    <td style="background-color:lightgrey;">Hydraulic connections at opposite side of the operator</td>
                                    <td style="text-align:center;">'.$hydraulic_connections.'</td>
                                </tr>
                                <tr>
                                    <td style="background-color:lightgrey;">Make sure there is no oil leakage at 160 bar pressure</td>
                                    <td style="text-align:center;">'.$no_oil_leakage.'</td>
                                </tr>
                                <tr>
                                    <td style="background-color:lightgrey;">Hydraulic cyclinder with no rotation movement with sliders element</td>
                                    <td style="text-align:center;">'.$hydraulic_rotation_movement.' </td>
                                </tr>
</table>
</td>
<td width="5%"></td>
<td width="30%">
<h4>Cooling</h4>
<table border="1" style="width:100%; padding:3px; ">
                                <tr>
                                    <td width="75%"></td>
                                    <td width="25%" style="text-align:center;">YES/NO</td>
                                </tr>
                                <tr>
                                    <td width="75%" style="background-color:lightgrey;">Cooling connection should be opposite to the operator side</td>
                                    <td width="25%" style="text-align:center;">'.$cooling_connection.' </td>
                                </tr>
                                <tr>
                                    <td style="background-color:lightgrey;">Cooling Nipples Size must be 3/4 Push Pin Plug.</td>
                                    <td style="text-align:center;">'.$cooling_nipples_size.'</td>
                                </tr>
</table>
</td>
</tr>
<table>
';


//echo $footer_logo_html; exit;
// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.



$filelocation = $_SERVER['DOCUMENT_ROOT'] . '/image_bank/popdf';
$fileNL = $filelocation . "/akash.pdf"; //Linux

$pdf->Output($fileNL, 'I');



//============================================================+
// END OF FILE
//============================================================+
