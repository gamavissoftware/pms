<?php
$pageurl = page_url."Formats/previewdfdesignform/";
$po_id = $this->uri->segment(3);
$lead_id = $this->uri->segment(4);
$CI = &get_instance();
$CI->load->model('Salescrm_model', 'salescrm');

/*** QUOTATION DATA **/

$quote_record_id = $CI->salescrm->getRecordID($lead_id);

// echo $record_id; exit;

$annexture_1 = $CI->salescrm->getannexture_1($quote_record_id);
// echo "<pre>"; print_r($annexture_1); exit;
if (count($annexture_1) > 0) {
    $product_to_be_packed = $annexture_1[0];
    $batchcut = $annexture_1[16];
    $liquidviscositydata = $annexture_1[10];
    $powderdensitydata = $annexture_1[12];
    $qty_data = $CI->salescrm->getQtyPackedData($quote_record_id);
     $horizontal_sealing_width1 = $annexture_1[4]; // e.g. "300mm"

// Extract numeric value
preg_match('/\d+/', $horizontal_sealing_width1, $num_match);
$numeric_value = isset($num_match[0]) ? (int)$num_match[0] : 0;

// Extract unit (like mm, cm, etc.)
preg_match('/[a-zA-Z]+/', $horizontal_sealing_width1, $unit_match);
$unit = isset($unit_match[0]) ? $unit_match[0] : '';

// Multiply numeric value
$multiplied_value = $numeric_value * 2;

// Final result
$horizontal_sealing_width = $multiplied_value . $unit;




     $vertical_sealing_width1 = $annexture_1[5]; // e.g. "300mm"

// Extract numeric value
preg_match('/\d+/', $vertical_sealing_width1, $num_match);
$numeric_value = isset($num_match[0]) ? (int)$num_match[0] : 0;

// Extract unit
preg_match('/[a-zA-Z]+/', $vertical_sealing_width1, $unit_match);
$unit = isset($unit_match[0]) ? $unit_match[0] : '';

// Multiply numeric value
$multiplied_value = $numeric_value * 2;

// Create final result
$vertical_sealing_width = $multiplied_value . $unit;




     $typeofsealing=$annexture_1[7];
     $liquid_option = $annexture_1[17];
     $powder_option = $annexture_1[18];
     $non_viscous_option=$annexture_1[19];
    $viscous_option=$annexture_1[20];
    $piston_filler_option=$annexture_1[21];
    $follow_meter_option=$annexture_1[22];
    $free_flow_option=$annexture_1[23];
    $weigher_system_option=$annexture_1[24];
    $liner_weigher_option=$annexture_1[25];
    $mult_head_weigher_option=$annexture_1[26];
    $volumetric_cap_option=$annexture_1[27];
    $non_free_flow_option=$annexture_1[28];
     $power_supply=$annexture_1[9];
     $machine_type=$annexture_1[29];
      $cup_filler_option=$annexture_1[31];
} else {
    $product_to_be_packed = '';
    $batchcut = '';
    $liquidviscositydata = '';
    $powderdensitydata = '';
    $qty_data = '';
    $horizontal_sealing_width='';
     $vertical_sealing_width='';
     $typeofsealing='';
      $liquid_option = '';
     $powder_option = '';
        $non_viscous_option='';
    $viscous_option='';
    $piston_filler_option='';
    $follow_meter_option='';
    $free_flow_option='';
    $weigher_system_option='';
    $liner_weigher_option='';
    $mult_head_weigher_option='';
    $volumetric_cap_option='';
    $non_free_flow_option='';
     $power_supply='';
       $machine_type='';
         $cup_filler_option='';
}



                                        if($product_to_be_packed==1){
                                            $producttt = 'Liquid / Paste';
                                        }else if($product_to_be_packed==2){
                                             $producttt = 'Powder / Granules';
                                        }
                                        

$getannexture_2 = $CI->salescrm->getannexture_2($quote_record_id);
// echo "<pre>"; print_r($getannexture_2); exit;
if (count($getannexture_2) > 0) {
    $no_of_track = $getannexture_2[3];
    $machinemodel = $getannexture_2[0];
} else {
    $no_of_track = '';
    $machinemodel = '';
}


   

    

    


         $q = $this->db->select('podate,df_number')->from('poreceived')->where('lead_id', $lead_id)->get();
    if($q->num_rows()>0)
    {
    foreach ($q->result() as $dfform);
    $date_of_po=date('d-m-Y', strtotime($dfform->podate));
    $df_form_name=$dfform->df_number;
    }else
    {
        $date_of_po='';
        $df_form_name='';
    }



    $q = $this->db->select('*')->from('df_design_form_600_table')->where('po_id',$po_id)->where('lead_id', $lead_id)->get();

if($q->num_rows()>0){

    foreach($q->result() as $rows);

    $design_form_date = $rows->design_form_date;
    $reference_no = $rows->reference_no;
    $ref_df_date = $rows->ref_df_date;
     $bom_no = $rows->bom_no;
      $bom_no_remarks = $rows->bom_no_remarks;
       $date_of_po_remarks = $rows->date_of_po_remarks;
        $dispatch_date = $rows->dispatch_date;
         $dispatch_date_remarks = $rows->dispatch_date_remarks;
          $trial_date = $rows->trial_date;
           $trial_date_remarks = $rows->trial_date_remarks;
            $machine_qty = $rows->machine_qty;
             $machine_qty_remarks = $rows->machine_qty_remarks;
              $machine_mode_no_remarks = $rows->machine_mode_no_remarks;
                $id=$rows->id;
}


$r = $this->db->select('*')->from('df_form_600_machine_specification')->where('record_id',$id)->get();

if($r->num_rows()>0){
    foreach($r->result() as $row);

    $tracks_remarks =$row->tracks_remarks;
     $product_packed_remarks =$row->product_packed_remarks;
      $filling_unit_remarks =$row->filling_unit_remarks;
       $quantity_packed_remarks =$row->quantity_packed_remarks;
        $pouch_size_remarks =$row->pouch_size_remarks;
         $profle_sealing_remarks =$row->profle_sealing_remarks;
          $notching_option =$row->notching_option;
           $notching_option_remarks =$row->notching_option_remarks;
             $filling_tank =$row->filling_tank;
               $filling_tank_remarks =$row->filling_tank_remarks;
                 $provision_coding =$row->provision_coding;
                   $provision_coding_option =$row->provision_coding_option;
                   $provision_coding_remarks =$row->provision_coding_remarks;
            $hooper_details =$row->hooper_details;
             $openable_option =$row->openable_option;
              $closed_option =$row->closed_option;
               $pressurised_option =$row->pressurised_option;
                $closed_pressurised_option =$row->closed_pressurised_option;
                 $non_pressurised_option =$row->non_pressurised_option;
                  $non_closed_pressurised_option =$row->non_closed_pressurised_option;
                   $hooper_details_remarks =$row->hooper_details_remarks;
                    $web_aligner =$row->web_aligner;
                     $web_aligner_option =$row->web_aligner_option;
                      $web_aligner_remarks =$row->web_aligner_remarks;
                       $center_slitting =$row->center_slitting;
                        $center_slitting_remarks =$row->center_slitting_remarks;
}


  $rest1=$this->db->select('id,name')->from('quote_parts_heading_master_options')->where('id',$web_aligner_option)->get();

  if($rest1->num_rows()>0){
    foreach($rest1->result() as $opt);

    $web_name =$opt->name;
  }

$s =$this->db->select('*')->from('df_form_600_machine_specification_1')->where('record_id',$id)->get();

if($s->num_rows()>0){
    foreach($s->result() as $row);

      $vertical_slitting =$row->vertical_slitting;
       $vertical_slitting_remarks =$row->vertical_slitting_remarks;
        $pulling_machine =$row->pulling_machine;
         $pulling_machine_remarks =$row->pulling_machine_remarks;
          $hose_pipe =$row->hose_pipe;
           $hose_pipe_remarks =$row->hose_pipe_remarks;
            $horizontal_sealer =$row->horizontal_sealer;
             $horizontal_sealer_remarks =$row->horizontal_sealer_remarks;
              $vertical_sealer =$row->vertical_sealer;
               $vertical_sealer_remarks =$row->vertical_sealer_remarks;
                $priston_drive =$row->priston_drive;
                 $priston_drive_remarks =$row->priston_drive_remarks;
                  $valve_movement =$row->valve_movement;
                   $valve_movement_remarks =$row->valve_movement_remarks;
                    $nozzle_funnel =$row->nozzle_funnel;
                     $powder_option1 =$row->powder_option1;
                      $liquid_option1 =$row->liquid_option1;
                       $liquid_shut_option1 =$row->liquid_shut_option1;
                        $nozzle_funnel_remarks =$row->nozzle_funnel_remarks;
                         $horizontal_sealer1_remarks =$row->horizontal_sealer1_remarks;
                          $vertical_sealer1_remarks =$row->vertical_sealer1_remarks;
                           $working_speed =$row->working_speed;
                            $working_speed_remarks =$row->working_speed_remarks;

}

$t =$this->db->select('*')->from('df_form_600_machine_specification_2')->where('record_id',$id)->get();

if($t->num_rows()>0){
    foreach($t->result() as $row);

    $reel_core_diameter =$row->reel_core_diameter;
    $reel_core_diameter_remarks =$row->reel_core_diameter_remarks;
    $trial_material =$row->trial_material;
    $trial_material_remarks =$row->trial_material_remarks;
    $laminate_trial =$row->laminate_trial;
    $laminate_trail_remarks =$row->laminate_trail_remarks;
    $laminate_detail =$row->laminate_detail;
    $laminate_detail_remarks =$row->laminate_detail_remarks;
    $heater_ssr_box =$row->heater_ssr_box;
    $heater_ssr_box_remarks =$row->heater_ssr_box_remarks;
    $beacon_light =$row->beacon_light;
    $beacon_light_remarks =$row->beacon_light_remarks;
    $hooper_level =$row->hooper_level;
    $hooper_level_option =$row->hooper_level_option;
    $hooper_level_remarks =$row->hooper_level_remarks;
    $safety_relay =$row->safety_relay;
    $safety_relay_remarks =$row->safety_relay_remarks;
    $plc_maker =$row->plc_maker;
    $plc_maker_remarks =$row->plc_maker_remarks;
    $conveyor =$row->conveyor;
    $conveyor_option_yes =$row->conveyor_option_yes;
    $conveyor_remarks =$row->conveyor_remarks;
    $standard =$row->standard;
    $standard_remarks =$row->standard_remarks;
    $changeover_part =$row->changeover_part;
    $changeover_part_remarks =$row->changeover_part_remarks;
    $special_notes =$row->special_notes;

}

      

       
     

       

        


        



           


          
           

//============================================================+
// File name   : example_001.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 001 for TCPDF class
//               Default Header and Footer
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Default Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 */

// Include the main TCPDF library (search for installation path).
require_once('application/views/formats/rfq/examples/tcpdf_include.php');
class MYPDF extends TCPDF {
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
}

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);





// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

$pdf->SetMargins(5, 5, 5, true);

// $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
// $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
	require_once(dirname(__FILE__) . '/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('dejavusans', '', 13, '', true);


//$pdf->SetFont('msungstdlight', '', 12);


// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage('L');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set some content to print
$html = '
<style>
    table{
        width:100%;
        }

        .table {
            width: 100%;
            font-size:12px;
            padding:5px;
        }


        .table th {
            border: 1px solid #000;
            background-color: #e5e5e5;
            text-align: center;
        }

        .table td {
            border: 1px solid #000;
            background-color: #fff;
            text-align: center;
        }

        .table1 {
            font-size:12px;
            width: 100%;
              padding: 5px;
        }

        .table1 th {
            border: 1px solid #000;
            background-color: #e5e5e5;
            text-align: center;
          
        }

        .table1 td {
            border: 1px solid #000;
            background-color: #fff;
            text-align: center;
        }

        .table2 {
            font-size:12px;
            width: 100%;
              padding: 5px;
        }

        .table2 th {
           
            background-color: #e5e5e5;
            text-align: center;
          
        }

        .table2 td {
            background-color: #fff;
            text-align: center;
        }

        


</style>

<table class="table1">
    <tr>
        <th width="50%" style="text-align:left;">From: Project Dept.</th>
        <th width="50%">Reference DF. No.-</th>
    </tr>
    <tr>
        <td >DESIGN FORM<br>DF-'.$df_form_name.'  <br>Date:- '.date('d-m-Y', strtotime($design_form_date)).'</td>
        <td >'.$reference_no.'<br>Reference DF Date:<br>'.date('d-m-Y', strtotime($ref_df_date)).'</td>
    </tr>

</table>
<table class="table1">
    <tr>
        <th width="75%" colspan="3">Order Details For MULTI TRACK MACHINE</th>
        <th width="25%">REMARKS</th>
    </tr>
    <tr>
        <td width="3%" style="text-align:center;">>></td>
        <td width="17%" style="text-align:left;">BOM No.</td>
        <td width="55%">'.$bom_no.'</td>
        <td width="25%">'.$bom_no_remarks.'</td>
    </tr>';

   

    $html.='<tr>
        <td  style="text-align:center;">>></td>
        <td  style="text-align:left;">Date of PO</td>
        <td >'.$date_of_po.'</td>
        <td ></td>
    </tr>
    <tr>
        <td  style="text-align:center;">>></td>
        <td  style="text-align:left;">Dispatch date</td>
        <td >'.date('d-m-Y', strtotime($dispatch_date)).'</td>
        <td >'.$dispatch_date_remarks.'</td>
    </tr>
    <tr>
        <td  style="text-align:center;">>></td>
        <td  style="text-align:left;">Trial Date</td>
        <td >'.date('d-m-Y', strtotime($trial_date)).'</td>
        <td >'.$trial_date_remarks.'</td>
    </tr>
     <tr>
        <td  style="text-align:center;">>></td>
        <td  style="text-align:left;">Qty. of Machine</td>
        <td >'.$machine_qty.'</td>
        <td >'.$machine_qty_remarks.'</td>
    </tr>
    <tr>
        <td  style="text-align:center;">>></td>
        <td  style="text-align:left;">Machine Model No.</td>
        <td >'.$machinemodel.'</td>
        <td >'.$machine_mode_no_remarks.'</td>
    </tr>
</table>
<table class="table1">
    <tr>
        <th width="75%" colspan="3">Machine Specification</th>
        <th width="25%">REMARKS</th>
    </tr>
     <tr>
        <td width="3%" style="text-align:center;">1</td>
        <td width="17%" style="text-align:left;">No. of Tracks</td>
        <td width="55%">'.$no_of_track.'</td>
        <td width="25%">'.$tracks_remarks.'</td>
    </tr>
</table>

<table class="table1">
    <tr>
        <td width="3%" style="text-align:center;">2</td>
        <td width="17%" style="text-align:left;">Product to be Packed</td>
        <td width="55%">'; 
       
        $output = $producttt;
        if (!empty($powder_option)) {
            $output .= ' --> ' . $powder_option;
        }
        
        if (!empty($liquid_option)) {
            $output .= ' --> ' . $liquid_option;
        }

    $html .=''.$output.'</td>
        <td width="25%">'.$product_packed_remarks.'</td>
    </tr>
</table>

<table class="table1">
    <tr>
        <td width="3%" style="text-align:center;">3</td>
        <td width="17%" style="text-align:left;">Type Of Filling Unit</td>
        <td width="55%">';
        
        $output = '';


         //viscous-option

           if (!empty($non_free_flow_option)) {
            
            $output .= $non_free_flow_option."<br/>";
        }



         if (!empty($cup_filler_option)) {
           
            $output .= $cup_filler_option."<br/>";
        }


          if (!empty($free_flow_option)) {
            $output .= $free_flow_option."<br/>";
        }


        if (!empty($weigher_system_option)) {
        
            $output .= $weigher_system_option."<br/>";
        }



           if (!empty($liner_weigher_option)) {
           
            $output .= $liner_weigher_option."<br/>";
        }



          if (!empty($mult_head_weigher_option)) {
        
            $output .= $mult_head_weigher_option."<br/>";
        }




        if (!empty($volumetric_cap_option)) {
          
            $output .= $volumetric_cap_option."<br/>";
        }
        



       if (!empty($non_viscous_option)) {
            $output .= $non_viscous_option."<br/>";
        }


          if (!empty($viscous_option)) {
            $output .=  $viscous_option."<br/>";
        }

           if (!empty($piston_filler_option)) {
            $output .= $piston_filler_option;
        }

         if (!empty($follow_meter_option)) {
            $output .= $follow_meter_option;
        }


        //non-viscous-option
     

      


     

        

        

        $html .=''.$output.' </td>
        <td width="25%">'.$filling_unit_remarks.'</td>
    </tr>
</table>
<table class="table1">
   ';
      

    $html .='<tr>
        <td width="3%" style="text-align:center;">4</td>
        <td width="17%" style="text-align:left;">Pouch Size (W x L x H) in mm</td>
        <td width="55%">
            ';
          
                if (count($qty_data) > 0) {
                                                                                for ($r = 0; $r < count($qty_data['qty']); $r++) {
                                                                        

                                                                                    if($qty_data['height'][$r]!=0){
                $html .=$qty_data['width'][$r].' X '.$qty_data['length'][$r].' X '.$qty_data['height'][$r].'<BR/>';

                                                                                    } else{

                                                                                         $html .=$qty_data['width'][$r].' X '.$qty_data['length'][$r].'<BR/>';
                  
        
                                                                                    }
                   } 

                    }


            
           $html .=' 
        </td>
        <td width="25%">'.$pouch_size_remarks.'</td>
    </tr>
      <tr>
        <td width="3%" style="text-align:center;">5</td>
        <td width="17%" style="text-align:left;">Horizontal Profile of Sealing</td>
        <td width="55%">'.$typeofsealing.'</td>
        <td width="25%">'.$profle_sealing_remarks.'</td>
    </tr>

    <tr>
        <td width="3%" style="text-align:center;">6</td>
        <td width="17%" style="text-align:left;">Quantity to be packed</td>
        <td width="55%">
           ';
          
                 if (count($qty_data) > 0) {
                                            for ($r = 0; $r < count($qty_data['qty']); $r++) {

                $html .='
                    '.$qty_data['qty'][$r].' '.$qty_data['unit'][$r].'<br/>
                  
               ';

           }
                                        }
           $html .=' 
        </td>
        <td width="25%">'.$quantity_packed_remarks.'</td>
    </tr>';
      
   $html .=' 
</table>
<table class="table1">
 
    <tr>
        <td width="3%" style="text-align:center;">7</td>
        <td width="17%" style="text-align:left;">Provision of Notching</td>
        <td width="55%">'.$notching_option.'</td>
        <td width="25%">'.$notching_option_remarks.'</td>
    </tr>
</table>

<table class="table1">
    <tr>
        <td width="3%" style="text-align:center;">8</td>
        <td width="17%" style="text-align:left;">Hopper Type</td>
        <td width="55%">'; 
         $output = '';

        // Check if $hooper_details has a value
        if (!empty($hooper_details)) {
            $output .= $hooper_details;
        }

        // Check if $openable_option has a value
        if (!empty($openable_option)) {
            if (!empty($output)) {
                $output .= ' --> ';
            }
            $output .= $openable_option;
        }

        // Check if $pressurised_option has a value
        if (!empty($pressurised_option)) {
            if (!empty($output)) {
                $output .= ' --> ';
            }
            $output .= $pressurised_option;
        }

        // Check if $non_pressurised_option has a value
        if (!empty($non_pressurised_option)) {
            if (!empty($output)) {
                $output .= ' --> ';
            }
            $output .= $non_pressurised_option;
        }

        // Check if $closed_option has a value
        if (!empty($closed_option)) {
            if (!empty($output)) {
                $output .= ' --> ';
            }
            $output .= $closed_option;
        }

        // Check if $closed_pressurised_option has a value
        if (!empty($closed_pressurised_option)) {
            if (!empty($output)) {
                $output .= ' --> ';
            }
            $output .= $closed_pressurised_option;
        }

        // Check if $non_closed_pressurised_option has a value
        if (!empty($non_closed_pressurised_option)) {
            if (!empty($output)) {
                $output .= ' --> ';
            }
            $output .= $non_closed_pressurised_option;
        }
        
        $html .=''.$output.' </td>
        <td width="25%">'.$hooper_details_remarks.'</td>
    </tr>';

    if($provision_coding_option!=' '){

        $coding_otion = '<br>Hinge Type:- '.$provision_coding_option;

    }
   
 $html .='</table>
 <table class="table1">
  <tr>
        <td width="3%" style="text-align:center;">9</td>
        <td width="17%" style="text-align:left;">Filling Tank</td>
        <td width="55%">'.$filling_tank.'</td>
        <td width="25%">'.$filling_tank_remarks.'</td>
    </tr>


    <tr>
        <td width="3%" style="text-align:center;">10</td>
        <td width="17%" style="text-align:left;">Provision of Coding</td>
        <td width="55%">'.$provision_coding.' '.$coding_otion.'</td>
        <td width="25%">'.$provision_coding_remarks.'</td>
    </tr>
</table>
<table class="table1">';
 if($web_aligner == 'Yes'){
     $html .='<tr>
        <td width="3%" style="text-align:center;">11</td>
        <td width="17%" style="text-align:left;">Web Aligner</td>
        <td width="27.5%">'.$web_aligner.'</td>
              <td width="27.5%">'.$web_name.'</td>
        <td width="25%">'.$web_aligner_remarks.'</td>
    </tr>';
 }else if($web_aligner == 'No'){
    $html .='<tr>
        <td width="3%" style="text-align:center;">11</td>
        <td width="17%" style="text-align:left;">Web Aligner</td>
        <td width="55%">'.$web_aligner.'</td>
        <td width="25%">'.$web_aligner_remarks.'</td>
    </tr>';
}
$html .='</table>
<table class="table1">
    <tr>
        <td width="3%" style="text-align:center;">12</td>
        <td width="17%" style="text-align:left;">Center Slitting</td>
        <td width="55%">'.$center_slitting.'</td>
        <td width="25%">'.$center_slitting_remarks.'</td>
    </tr>
    <tr>
        <td width="3%" style="text-align:center;">13</td>
        <td width="17%" style="text-align:left;">Vertical Slitter</td>
        <td width="55%">'.$vertical_slitting.'</td>
        <td width="25%">'.$vertical_slitting_remarks.'</td>
    </tr>
    <tr>
        <td width="3%" style="text-align:center;">14</td>
        <td width="17%" style="text-align:left;">Vertical Sealer Drive</td>
        <td width="55%">'.$vertical_sealer.'</td>
        <td width="25%">'.$vertical_sealer_remarks.'</td>
    </tr>
     <tr>
        <td width="3%" style="text-align:center;">15</td>
        <td width="17%" style="text-align:left;">Pulling Machine</td>
        <td width="55%">'.$pulling_machine.'</td>
        <td width="25%">'.$pulling_machine_remarks.'</td>
    </tr>
    <tr>
        <td width="3%" style="text-align:center;">16</td>
        <td width="17%" style="text-align:left;">Horizontal Sealer Drive</td>
        <td width="55%">'.$horizontal_sealer.'</td>
        <td width="25%">'.$horizontal_sealer_remarks.'</td>
    </tr>
    <tr>
        <td width="3%" style="text-align:center;">17</td>
        <td width="17%" style="text-align:left;">Piston Movement</td>
        <td width="55%">'.$priston_drive.'</td>
        <td width="25%">'.$priston_drive_remarks.'</td>
    </tr>
    <tr>
        <td width="3%" style="text-align:center;">18</td>
        <td width="17%" style="text-align:left;">Valve Movement</td>
        <td width="55%">'.$valve_movement.'</td>
        <td width="25%">'.$valve_movement_remarks.'</td>
    </tr>
</table>
<table class="table1">';


   $html .=' 

   <tr>
        <td width="3%" style="text-align:center;">19</td>
        <td width="17%" style="text-align:left;">Nozzle/Funnel</td>
        <td width="27.5%">'.$nozzle_funnel.'</td>
        <td width="27.5%">'.$powder_option1.' '.$liquid_option1.' '.$liquid_shut_option1.'</td>
        <td width="25%">'.$nozzle_funnel_remarks.'</td>
    </tr>
    <tr>
        <td width="3%" style="text-align:center;">20</td>
        <td width="17%" style="text-align:left;">Hose Pipe (type)</td>
        <td width="55%">'.$hose_pipe.'</td>
        <td width="25%">'.$hose_pipe_remarks.'</td>
    </tr>
</table>
';

 $html .='
<table class="table1">
   
    <tr>
        <td width="3%" style="text-align:center;">21</td>
        <td width="17%" style="text-align:left;">Total Width of Horizontal Sealer</td>
        <td width="55%" >'.$horizontal_sealing_width.'mm</td>
        <td width="25%" >'.$horizontal_sealer1_remarks.'</td>
    </tr>
</table>
<table class="table1">
    
    <tr>
        <td width="3%" style="text-align:center;">22</td>
        <td width="17%" style="text-align:left;">Total Width of Vertical Sealer</td>
        <td width="55%" >'.$vertical_sealing_width.'mm</td>
        <td width="25%">'.$vertical_sealer_remarks.'</td>
    </tr>
</table>
<table class="table1">
    <tr>
        <td width="3%" style="text-align:center;">23</td>
        <td width="17%" style="text-align:left;">Working Speed</td>
        <th width="55%">'.$working_speed.'</th>
        <td width="25%">'.$working_speed_remarks.'</td>
    </tr>
     <tr>
        <td width="3%" style="text-align:center;">24</td>
        <td width="17%" style="text-align:left;">Core Diameter</td>
        <td width="55%">'.$reel_core_diameter.'</td>
        <td width="25%">'.$reel_core_diameter_remarks.'</td>
    </tr>
     <tr>
        <td width="3%" style="text-align:center;">25</td>
        <td width="17%" style="text-align:left;">Trial Material / Product</td>
        <td width="55%">'.$trial_material.'</td>
        <td width="25%">'.$trial_material_remarks.'</td>
    </tr>
     <tr>
        <td width="3%" style="text-align:center;">26</td>
        <td width="17%" style="text-align:left;">Laminate for Trial</td>
        <td width="55%">'.$laminate_trial.'</td>
        <td width="25%">'.$laminate_trail_remarks.'</td>
    </tr>
     <tr>
        <td width="3%" style="text-align:center;">27</td>
        <td width="17%" style="text-align:left;">Laminate Details</td>
        <td width="55%">'.$laminate_detail.'</td>
        <td width="25%">'.$laminate_detail_remarks.'</td>
    </tr>
     <tr>
        <td width="3%" style="text-align:center;">28</td>
        <td width="17%" style="text-align:left;">Heater & SSR Fault Detection Box</td>
        <td width="55%">'.$heater_ssr_box.'</td>
        <td width="25%">'.$heater_ssr_box_remarks.'</td>
    </tr>
    <tr>
        <td width="3%" style="text-align:center;">29</td>
        <td width="17%" style="text-align:left;">Beacon Lights</td>
        <td width="55%">'.$beacon_light.'</td>
        <td width="25%">'.$beacon_light_remarks.'</td>
    </tr>
</table>
<table class="table1">';
if($hooper_level == 'Yes'){
    $html .='  <tr>
        <td width="3%" style="text-align:center;">30</td>
        <td width="17%" style="text-align:left;">Hopper Level Sensor</td>
        <td width="27.5%">'.$hooper_level.'</td>
       
        <td width="27.5%">'.$hooper_level_option.'</td>
        <td width="25%">'.$hooper_level_remarks.'</td>
    </tr>';
} else if($hooper_level == 'No'){
    $html .=' <tr>
       <td width="3%" style="text-align:center;">30</td>
        <td width="17%" style="text-align:left;">Hopper Level Sensor</td>
        <td width="55%">'.$hooper_level.'</td>
        <td width="25%">'.$hooper_level_remarks.'</td>
    </tr>';
}
   $html .='  <tr>
       <td width="3%" style="text-align:center;">31</td>
        <td width="17%" style="text-align:left;">Safety Relay + Safety Switch</td>
        <td width="55%">'.$safety_relay.'</td>
        <td width="25%">'.$safety_relay_remarks.'</td>
    </tr>

   

    <tr>
       <td width="3%" style="text-align:center;">32</td>
        <td width="17%" style="text-align:left;">PLC Make</td>
        <td width="55%">'.$plc_maker.'</td>
        <td width="25%">'.$plc_maker_remarks.'</td>
    </tr>
  
    ';
   
    if($conveyor == 'Yes'){
   $html .='  <tr>
        <td width="3%" style="text-align:center;">33</td>
        <td width="17%" style="text-align:left;">Conveyor</td>
        <td width="27.5%">'.$conveyor.'</td>
        <td width="27.5%">'.$conveyor_option_yes.'</td>
        <td width="25%">'.$conveyor_remarks.'</td>
    </tr>';
    }else if($conveyor == 'No'){
  $html .='   <tr>
       <td width="3%" style="text-align:center;">33</td>
        <td width="17%" style="text-align:left;">Conveyor</td>
        <td width="55%">'.$conveyor.'</td>
        <td width="25%">'.$conveyor_remarks.'</td>
    </tr>';
    }
    $html .=' 

     <tr>
       <td width="3%" style="text-align:center;">34</td>
        <td width="17%" style="text-align:left;">Standard Spares</td>
        <td width="55%">'.$standard.'</td>
        <td width="25%">'.$standard_remarks.'</td>
    </tr>
    <tr>
       <td width="3%" style="text-align:center;">35</td>
        <td width="17%" style="text-align:left;">Changeover Parts</td>
        <td width="55%">'.$changeover_part.'</td>
        <td width="25%">'.$changeover_part_remarks.'</td>
    </tr>
</table>


<table class="table1">
 <tr>
        <td width="3%" style="text-align:center;">36</td>
        <td width="17%" style="text-align:left;">SPECIAL NOTES</td>';


   
            
       $html .=' <td width="80%" style="text-align:left;">'.$special_notes.'</td>';
      

   $html .=' </tr>
</table>

<table class="table1">
    <tr>
        <td width="100%" style="text-align:center;">
         <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <table class="table2">
                <tr>
                    <td>Project/Marketing Dept.</td>
                    <td>Head Design Dept.</td>
                    <td>Trial Dept.</td>
                    <td>Production/PPC</td>
                    <td>Purchase Dept.</td>
                    <td>Plant Head</td>
                </tr>
            </table>
            <br>
            <br>
            <br><br><br><br>
            <table class="table2">
                <tr>
                    <td>Mr.Rishabh Sharma<br>
Executive Director - Design & Development</td>
                    <td>Mr.Shubham Sharma<br>
Executive Director - Business Development</td>
                    <td>Mr.Virendra Sharma<br>
Chairman & Managing Director</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
';

// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
ob_end_clean();

//$pdf->Output();
$filelocation = SITE_ROOT.'designform/';

$fileNL='DF_'.$df_form_name.'_'.$design_form_date.'_.pdf'; //Linux
//echo $filelocation; exit;
$pdf->Output($filelocation.$fileNL, 'F');

  //$pdf->Output($filelocation.$fileNL, 'I');

$url6 = base64_encode($fileNL);

//echo $url6; exit;
header('location:'.$pageurl.$this->uri->segment(3)."/".$this->uri->segment(4)."/".$url6); 
//============================================================+
// END OF FILE
//============================================================+
