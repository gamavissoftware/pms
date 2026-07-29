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


$getannexture_2 = $CI->salescrm->getannexture_2($quote_record_id);
// echo "<pre>"; print_r($getannexture_2); exit;
if (count($getannexture_2) > 0) {
    $no_of_track = $getannexture_2[3];
    $machinemodel = $getannexture_2[0];
} else {
    $no_of_track = '';
    $machinemodel = '';
}




/** END **/


    

        $qry = $this->db->select('*')->from('df_design_form_table')->where('po_id',$po_id)->where('lead_id',$lead_id)->get();

        if($qry->num_rows()>0){
            foreach($qry->result() as $rows);

            $df_form_name= $rows->design_form_name;
            $design_form_date = $rows->design_form_date;
            $ref_df_date = $rows->ref_df_date;
          $reference_no = $rows->reference_no;
            $iom_no= $rows->iom_no;
            $invoice_no= $rows->invoice_no;
            $id =$rows->id;
        }


         $q = $this->db->select('podate,df_number')->from('poreceived')->where('lead_id', $lead_id)->where('id',$po_id)->get();
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




        $multi = $this->db->select('*')->from('df_form_multi_track_machine')->where('record_id',$id)->get();

        if($multi->num_rows()>0){
            foreach($multi->result() as $multi_rows);

            $ce_complied= $multi_rows->ce_complied;
             $bom_no= $multi_rows->bom_no;
              $bom_no_remarks= $multi_rows->bom_no_remarks;
            //$date_of_po= $multi_rows->date_of_po;
            $penalty_clause= $multi_rows->penalty_clause;
            $ce_complied_remarks= $multi_rows->ce_complied_remarks;
            $date_of_po_remarks= $multi_rows->date_of_po_remarks;
            $penalty_clause_remarks= $multi_rows->penalty_clause_remarks;
            $dispatch_date= $multi_rows->dispatch_date;
            $dispatch_date_remarks= $multi_rows->dispatch_date_remarks;
            $trial_date= $multi_rows->trial_date;
            $trial_date_remarks= $multi_rows->trial_date_remarks;
            $machine_mode_no= $multi_rows->machine_mode_no;
            $machine_mode_no_remarks= $multi_rows->machine_mode_no_remarks;
            //  $machine_type = $multi_rows->machine_type;
    $machine_type_remarks = $multi_rows->machine_type_remarks;
     $machine_orientation = $multi_rows->machine_orientation;
    $machine_orientation_remarks = $multi_rows->machine_orientation_remarks;
          
        }

        $multi_spec =$this->db->select('*')->from('df_form_machine_specification')->where('record_id',$id)->get();

        if($multi_spec->num_rows()>0){
            foreach($multi_spec->result() as $spec_rows);

             $tracks= $spec_rows->tracks;
             $product_packed= $spec_rows->product_packed;
            // $powder_option= $spec_rows->powder_option;
          //   $liquid_option= $spec_rows->liquid_option;
             //$non_viscous_option= $spec_rows->non_viscous_option;
             //$viscous_option= $spec_rows->viscous_option;
           //  $piston_filler_option= $spec_rows->piston_filler_option;
             $follow_meter_option= $spec_rows->follow_meter_option;
            //  $non_free_flow_option= $spec_rows->non_free_flow_option;
            // $cup_filler_option= $spec_rows->cup_filler_option;
            //  $free_flow_option= $spec_rows->free_flow_option;
            //  $weigher_system_option= $spec_rows->weigher_system_option;
            //  $liner_weigher_option= $spec_rows->liner_weigher_option;
             $mult_head_weigher_option= $spec_rows->mult_head_weigher_option;
            //  $volumetric_cap_option= $spec_rows->volumetric_cap_option;
             $profile_of_sealing= $spec_rows->profile_of_sealing;
             $tracks_remarks= $spec_rows->tracks_remarks;
             $product_packed_remarks= $spec_rows->product_packed_remarks;
             $filling_unit_remarks= $spec_rows->filling_unit_remarks;
            //  $density= $spec_rows->density;
            //  $viscosity= $spec_rows->viscosity;
             $product_specification_remarks= $spec_rows->product_specification_remarks;
             $profle_sealing_remarks= $spec_rows->profle_sealing_remarks;

        }

        if($product_to_be_packed == 1){
            $product_echo = 'Liquid / Paste';

            $pro = 'Viscosity:<br>'.$liquidviscositydata;
        }else if($product_to_be_packed == 2){
            $product_echo = 'Powder / Granules';

             $pro = 'Density:<br>'.$powderdensitydata;
        }


        $multitrack = $this->db->select('*')->from('df_form_multi_track_machine1')->where('record_id',$id)->get();


         if($multitrack->num_rows()>0){
            foreach($multitrack->result() as $spec_rows);

             $notching_option= $spec_rows->notching_option;
             $notching_option_remarks= $spec_rows->notching_option_remarks;
             $hooper_details= $spec_rows->hooper_details;
             $openable_option= $spec_rows->openable_option;
             $closed_option= $spec_rows->closed_option;
             $pressurised_option= $spec_rows->pressurised_option;
             $closed_pressurised_option= $spec_rows->closed_pressurised_option;
             $non_pressurised_option= $spec_rows->non_pressurised_option;
             $non_closed_pressurised_option= $spec_rows->non_closed_pressurised_option;
             $hooper_details_remarks= $spec_rows->hooper_details_remarks;
             $cladding_provision= $spec_rows->cladding_provision;
             $cladding_provision_remarks= $spec_rows->cladding_provision_remarks;
             $embossing= $spec_rows->embossing;
             $embossing_option= $spec_rows->embossing_option;
             $linear_option= $spec_rows->linear_option;
             $rotary_option= $spec_rows->rotary_option;
             $provision_remarks= $spec_rows->provision_remarks;
             $web_aligner= $spec_rows->web_aligner;
             $web_aligner_option= $spec_rows->web_aligner_option;

             $unwind_main_motor= $spec_rows->unwind_main_motor;
             $unwind_main_motor_remarks= $spec_rows->unwind_main_motor_remarks;
             $unwind_dancing_roll= $spec_rows->unwind_dancing_roll;
             $unwind_dancing_roll_remarks= $spec_rows->unwind_dancing_roll_remarks;
             $rotary_plain_cut_assembly= $spec_rows->rotary_plain_cut_assembly;
             $rotary_plain_cut_assembly_remarks= $spec_rows->rotary_plain_cut_assembly_remarks;
             $auger_drive= $spec_rows->auger_drive;
             $auger_drive_remarks= $spec_rows->auger_drive_remarks;
             $static_charge_eliminator= $spec_rows->static_charge_eliminator;
             $static_charge_eliminator_remarks= $spec_rows->static_charge_eliminator_remarks;
             $kld_clearance= $spec_rows->kld_clearance;
             $kld_clearance_remarks= $spec_rows->kld_clearance_remarks;
             $heater_ssr_fault= $spec_rows->heater_ssr_fault;
             $heater_ssr_fault_remarks= $spec_rows->heater_ssr_fault_remarks;
             $pin_hole_assembly= $spec_rows->pin_hole_assembly;
             $pin_hole_assembly_remarks= $spec_rows->pin_hole_assembly_remarks;
             $nitrogen_purging= $spec_rows->nitrogen_purging;
             $nitrogen_purging_remarks= $spec_rows->nitrogen_purging_remarks;
             $sip_maker= $spec_rows->sip_maker;
             $sip_maker_remarks= $spec_rows->sip_maker_remarks;
             $bagging_unit= $spec_rows->bagging_unit;
             $bagging_unit_remarks= $spec_rows->bagging_unit_remarks;

             //echo $web_aligner_option; exit;
             $rt=$this->db->select('name')->from('quote_parts_heading_master_options')->where('id',$web_aligner_option)->get();
             if($rt->num_rows()>0)
             {
                foreach($rt->result() as $rrtt);
                $web_aligner_option=$rrtt->name;
             }
             $web_aligner_remarks= $spec_rows->web_aligner_remarks;

         }


         $multitrack1 = $this->db->select('*')->from('df_form_multi_track_machine2')->where('record_id',$id)->get();

         
         if($multitrack1->num_rows()>0){
            foreach($multitrack1->result() as $spec_rows);

             $center_slitting= $spec_rows->center_slitting;
             $center_slitting_remarks= $spec_rows->center_slitting_remarks;
             $vertical_slitting= $spec_rows->vertical_slitting;
             $vertical_slitting_remarks= $spec_rows->vertical_slitting_remarks;
             $vertical_sealer= $spec_rows->vertical_sealer;
             $vertical_sealer_remarks= $spec_rows->vertical_sealer_remarks;
             $laminate_pulling= $spec_rows->laminate_pulling;
             $laminate_pulling_remarks= $spec_rows->laminate_pulling_remarks;
             $embossing_coding= $spec_rows->embossing_coding;
             $embossing_coding_remarks= $spec_rows->embossing_coding_remarks;
             $cooling_station= $spec_rows->cooling_station;
             $cooling_station_remarks= $spec_rows->cooling_station_remarks;
             $horizontal_sealer= $spec_rows->horizontal_sealer;
             $horizontal_sealer_remarks= $spec_rows->horizontal_sealer_remarks;
             $perforation_blade= $spec_rows->perforation_blade;
             $perforation_blade_remarks= $spec_rows->perforation_blade_remarks;
             $priston_drive= $spec_rows->priston_drive;
             $priston_drive_remarks= $spec_rows->priston_drive_remarks;
             $shutt_off_nozzle= $spec_rows->shutt_off_nozzle;
             $shutt_off_nozzle_remarks= $spec_rows->shutt_off_nozzle_remarks;
             $filling_plate_drive= $spec_rows->filling_plate_drive;
             $filling_plate_drive_remarks= $spec_rows->filling_plate_drive_remarks;
             $individual_weight= $spec_rows->individual_weight;
             $individual_weight_remarks= $spec_rows->individual_weight_remarks;
             $overall_weight_adjust= $spec_rows->overall_weight_adjust;
             $overall_weight_adjust_remarks= $spec_rows->overall_weight_adjust_remarks;
               $vertical_sealer_width= $spec_rows->vertical_sealer_width;
                 $horizontal_sealer_width= $spec_rows->horizontal_sealer_width;


         }


           $multitrack2 = $this->db->select('*')->from('df_form_multi_track_machine3')->where('record_id',$id)->get();

             if($multitrack2->num_rows()>0){
            foreach($multitrack2->result() as $spec_rows);

             $traverse_drive= $spec_rows->traverse_drive;
              $yes_traverse_drive= $spec_rows->yes_traverse_drive;
               $traverse_drive_remarks= $spec_rows->traverse_drive_remarks;
               $printer= $spec_rows->printer_yes_no;
                $inkjet_option= $spec_rows->inkjet_option;
                 $tto_option= $spec_rows->tto_option;
                  $thermal_inkjet_option= $spec_rows->thermal_inkjet_option;
                   $printer_remarks= $spec_rows->printer_remarks;
                    $case_packer_drive= $spec_rows->case_packer_drive;
                     $case_packer_drive_remarks= $spec_rows->case_packer_drive_remarks;
                      $nozzle_funnel= $spec_rows->nozzle_funnel;
                       $powder_option1= $spec_rows->powder_option1;
                        $liquid_option1= $spec_rows->liquid_option1;
                         $liquid_shut_option1= $spec_rows->liquid_shut_option1;
                          $nozzle_funnel_remarks= $spec_rows->nozzle_funnel_remarks;
                           $hose_pipe= $spec_rows->hose_pipe;
                            $hose_pipe_remarks= $spec_rows->hose_pipe_remarks;
                             $batch_cut_format= $spec_rows->batch_cut_format;
                              $string_option= $spec_rows->string_option;
                               $batch_cut_format_remarks= $spec_rows->batch_cut_format_remarks;
            
             }


              $multitrack3 = $this->db->select('*')->from('df_form_multi_track_machine4')->where('record_id',$id)->get();

                        if($multitrack3->num_rows()>0){
                        foreach($multitrack3->result() as $spec_rows);
                        $horizontal_sealer1= $spec_rows->horizontal_sealer1;
                        $horizontal_sealer1_remarks= $spec_rows->horizontal_sealer1_remarks;
                        $vertical_sealer1= $spec_rows->vertical_sealer1;
                        $vertical_sealer1_remarks= $spec_rows->vertical_sealer1_remarks;
                        $rotary_valve_coating= $spec_rows->rotary_valve_coating;
                        $rotary_valve_coating_remarks= $spec_rows->rotary_valve_coating_remarks;
                        $working_speed= $spec_rows->working_speed;
                        $working_speed_remarks= $spec_rows->working_speed_remarks;
                        $reel_shaft_type= $spec_rows->reel_shaft_type;
                        $reel_shaft_type_remarks= $spec_rows->reel_shaft_type_remarks;
                        $reel_core_diameter= $spec_rows->reel_core_diameter;
                        $reel_core_diameter_remarks= $spec_rows->reel_core_diameter_remarks;
                        $trial_material= $spec_rows->trial_material;
                        $trial_material_remarks= $spec_rows->trial_material_remarks;
                        $laminate_detail= $spec_rows->laminate_detail;
                        $laminate_detail_remarks= $spec_rows->laminate_detail_remarks;
                        $heater_control_system= $spec_rows->heater_control_system;
                        $heater_control_system_remarks= $spec_rows->heater_control_system_remarks;
                        $beacon_light= $spec_rows->beacon_light;
                        $beacon_light_remarks= $spec_rows->beacon_light_remarks;
                        $hooper_level= $spec_rows->hooper_level;
                        $hooper_level_option= $spec_rows->hooper_level_option;
                        $hooper_level_remarks= $spec_rows->hooper_level_remarks;
                        $safety_relay= $spec_rows->safety_relay;
                        $safety_relay_remarks= $spec_rows->safety_relay_remarks;
                        $plc_maker= $spec_rows->plc_maker;
                        $plc_maker_remarks= $spec_rows->plc_maker_remarks;
                        $supply_voltage = $spec_rows->supply_voltage;
                        $supply_voltage_remarks = $spec_rows->supply_voltage_remarks;
                        $hmi_size = $spec_rows->hmi_size;
                        //echo $hmi_size; exit;
                        $hmi_size_remarks = $spec_rows->hmi_size_remarks;
                        $cip_system= $spec_rows->cip_system;
                        $cip_system_option= $spec_rows->cip_system_option;
                        $cip_system_option_remarks= $spec_rows->cip_system_option_remarks;
                        $tool_kit= $spec_rows->tool_kit;
                        $tool_kit_remarks= $spec_rows->tool_kit_remarks;
                        $changeover_part= $spec_rows->changeover_part;
                        $changeover_part_remarks= $spec_rows->changeover_part_remarks;
        
        
        
        }

            $multitrack4 = $this->db->select('*')->from('df_form_multi_track_machine5')->where('record_id',$id)->get();

             if($multitrack4->num_rows()>0){
            foreach($multitrack4->result() as $spec_rows);
        
             $secondary_pack= $spec_rows->secondary_pack;
           
             $case_packer= $spec_rows->case_packer;
             $secondary_pack_remarks= $spec_rows->secondary_pack_remarks;
             $ladder_platform_remarks= $spec_rows->ladder_platform_remarks;
             $machine_guarding= $spec_rows->machine_guarding;
             $aluminium_option= $spec_rows->aluminium_option;
             $ss_304_option= $spec_rows->ss_304_option;
             $machine_guarding_remarks= $spec_rows->machine_guarding_remarks;
             $special_notes= $spec_rows->special_notes;
             $ladder_platform= $spec_rows->ladder_platform;

             $tilting_flaps = $spec_rows->tilting_flaps;
             $tilting_flaps_remarks = $spec_rows->tilting_flaps_remarks;
             $tilting_movement_drive = $spec_rows->tilting_movement_drive;
             $tilting_movement_drive_remarks = $spec_rows->tilting_movement_drive_remarks;
             $vert_hori_movement = $spec_rows->vert_hori_movement;
             $vert_hori_movement_remarks = $spec_rows->vert_hori_movement_remarks;
            //  $perforation_drive = $spec_rows->perforation_drive;
            //  $perforation_drive_remarks = $spec_rows->perforation_drive_remarks;
             $collating_conveyor = $spec_rows->collating_conveyor;
             $collating_conveyor_remarks = $spec_rows->collating_conveyor_remarks;
             $rope_conveyor = $spec_rows->rope_conveyor;
             $rope_conveyor_remarks = $spec_rows->rope_conveyor_remarks;
             $take_off_conveyor = $spec_rows->take_off_conveyor;
             $take_off_conveyor_remarks = $spec_rows->take_off_conveyor_remarks;
             $weighing_conveyor_drive = $spec_rows->weighing_conveyor_drive;
             $weighing_conveyor_drive_remarks = $spec_rows->weighing_conveyor_drive_remarks;

             }


             $sizepouch = $this->db->select('*')->from('df_form_machine_specification_size_qnty')->where('record_id', $id)->get();

            if($sizepouch->num_rows()>0){


                 foreach($sizepouch->result() as $rowws);

    $pouch_size_remarks =$rowws->pouch_size_remarks;
    $quantity_packed_remarks=$rowws->quantity_packed_remarks;
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
$pdf->SetAutoPageBreak(TRUE, 1);


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
        <td >DF No.- '.$df_form_name.' <br> Date:- '.date('d-m-Y', strtotime($design_form_date)).'</td>
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
        <td width="17%" style="text-align:left;">BOM N0.</td>
        <td width="55%">'.$bom_no.'</td>
        <td width="25%">'.$bom_no_remarks.'</td>
    </tr>
     <tr>
        <td width="3%" style="text-align:center;">>></td>
        <td width="17%" style="text-align:left;">CE complied</td>
        <td width="55%">'.$ce_complied.'</td>
        <td width="25%">'.$ce_complied_remarks.'</td>
    </tr>';

   

    $html.='
   
    <tr>
        <td  style="text-align:center;">>></td>
        <td  style="text-align:left;">Date of PO</td>
        <td >'.$date_of_po.'</td>
        <td >'.$date_of_po_remarks.'</td>
    </tr>
    <tr>
        <td  style="text-align:center;">>></td>
        <td  style="text-align:left;">Penalty Clause</td>
        <td >'.$penalty_clause.'</td>
        <td >'.$penalty_clause_remarks.'</td>
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
        <td  style="text-align:left;">Machine Model No.</td>
        <td >'.$machinemodel.'</td>
        <td >'.$machine_mode_no_remarks.'</td>
    </tr>
     <tr>
        <td  style="text-align:center;">>></td>
        <td  style="text-align:left;">Machine Type</td>
        <td >'.$machine_type.'</td>
        <td >'.$machine_type_remarks.'</td>
    </tr>
     <tr>
        <td  style="text-align:center;">>></td>
        <td  style="text-align:left;">Machine Orientation</td>
        <td >'.$machine_orientation.'</td>
        <td >'.$machine_orientation_remarks.'</td>
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
       
        $output = $product_echo;
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


        //non-viscous-option
     

      


     

        

        

        $html .=''.$output.' </td>
        <td width="25%">'.$filling_unit_remarks.'</td>
    </tr>
</table>
<table class="table1">
    <tr>
        <td width="3%" style="text-align:center;">4</td>
        <td width="17%" style="text-align:left;">Product Specification (Viscosity / Density)</td>
         <td width="55%">'.$pro.'</td>
        <td width="25%">'.$product_specification_remarks.'</td>
    </tr>';
      

    $html .='<tr>
        <td width="3%" style="text-align:center;">5</td>
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
   <tr>
        <td width="3%" style="text-align:center;">7</td>
        <td width="17%" style="text-align:left;">Working Speed</td>
        <td width="55%">'.$working_speed.'</td>
        <td width="25%">'.$working_speed_remarks.'</td>
    </tr>
      

  
   <tr>
        <td width="3%" style="text-align:center;">8</td>
        <td width="17%" style="text-align:left;">Profile of Sealing</td>
        <td width="55%">'.$typeofsealing.'</td>
        <td width="25%">'.$profle_sealing_remarks.'</td>
    </tr>
    <tr>
        <td width="3%" style="text-align:center;">9</td>
        <td width="17%" style="text-align:left;">Provision of Notching</td>
        <td width="55%">'.$notching_option.'</td>
        <td width="25%">'.$notching_option_remarks.'</td>
    </tr>
</table>
<table class="table1">
    <tr>
        <td width="3%" style="text-align:center;">10</td>
        <td width="17%" style="text-align:left;">Hopper Details</td>
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
    </tr>
    </table>
 <br pagebreak="true">
<table class="table1">
    <tr>
        <td width="3%" style="text-align:center;">11</td>
        <td width="17%" style="text-align:left;">Cladding Provision</td>
        <td width="55%">'.$cladding_provision.'</td>
        <td width="25%">'.$cladding_provision_remarks.'</td>
    </tr>';

    if($embossing == 'Yes'){
     $html .='<tr>
        <td width="3%" style="text-align:center;">12</td>
        <td width="17%" style="text-align:left;">Provision of Embossing coding</td>
        <td width="18.333%">'.$embossing.'</td>
        <td width="18.333%">'.$embossing_option.'</td>
        <td width="18.333%">'.$linear_option.' '.$rotary_option.'</td>
        <td width="25%">'.$provision_remarks.'</td>
    </tr>';}else if($embossing == 'No'){
     $html .=' <tr>
        <td width="3%" style="text-align:center;">12</td>
        <td width="17%" style="text-align:left;">Provision of Embossing coding</td>
        <td width="55%">'.$embossing.'</td>
        <td width="25%">'.$provision_remarks.'</td>
    </tr>';
    }
 $html .='</table>
<table class="table1">';
 if($web_aligner == 'Yes'){
     $html .='<tr>
        <td width="3%" style="text-align:center;">13</td>
        <td width="17%" style="text-align:left;">Web Aligner</td>
        <td width="27.5%">'.$web_aligner.'</td>
              <td width="27.5%">'.$web_aligner_option.'</td>
        <td width="25%">'.$web_aligner_remarks.'</td>
    </tr>';
 }else if($web_aligner == 'No'){
    $html .='<tr>
        <td width="3%" style="text-align:center;">13</td>
        <td width="17%" style="text-align:left;">Web Aligner</td>
        <td width="55%">'.$web_aligner.'</td>
        <td width="25%">'.$web_aligner_remarks.'</td>
    </tr>';
}
$html .='
    <tr>
        <td width="3%" style="text-align:center;">14</td>
        <td width="17%" style="text-align:left;">Center Slitting</td>
        <td width="55%">'.$center_slitting.'</td>
        <td width="25%">'.$center_slitting_remarks.'</td>
    </tr>

    </table>
   
<table class="table1">
    <tr>
        <td width="3%" style="text-align:center;">15</td>
        <td width="17%" style="text-align:left;">Vertical Slitter</td>
        <td width="55%">'.$vertical_slitting.'</td>
        <td width="25%">'.$vertical_slitting_remarks.'</td>
    </tr>
     <tr>
        <td width="3%" style="text-align:center;">16</td>
        <td width="17%" style="text-align:left;">Unwind Main Motor Drive</td>
        <td width="55%">'.$unwind_main_motor.'</td>
        <td width="25%">'.$unwind_main_motor_remarks.'</td>
    </tr>
     <tr>
        <td width="3%" style="text-align:center;">17</td>
        <td width="17%" style="text-align:left;">Unwind Dancing Roll Drive</td>
        <td width="55%">'.$unwind_dancing_roll.'</td>
        <td width="25%">'.$unwind_dancing_roll_remarks.'</td>
    </tr>
      <tr>
        <td width="3%" style="text-align:center;">18</td>
        <td width="17%" style="text-align:left;">Laminate Pulling Drive</td>
        <td width="55%">'.$laminate_pulling.'</td>
        <td width="25%">'.$laminate_pulling_remarks.'</td>
    </tr>
    <tr>
        <td width="3%" style="text-align:center;">19</td>
        <td width="17%" style="text-align:left;">Vertical Sealer Drive</td>
        <td width="55%">'.$vertical_sealer.'</td>
        <td width="25%">'.$vertical_sealer_remarks.'</td>
    </tr>
   <tr>
        <td width="3%" style="text-align:center;">20</td>
        <td width="17%" style="text-align:left;">Horizontal Sealer Drive</td>
        <td width="55%">'.$horizontal_sealer.'</td>
        <td width="25%">'.$horizontal_sealer_remarks.'</td>
    </tr>
    <tr>
        <td width="3%" style="text-align:center;">21</td>
        <td width="17%" style="text-align:left;">Rotary Plain Cut Assembly</td>
        <td width="55%">'.$rotary_plain_cut_assembly.'</td>
        <td width="25%">'.$rotary_plain_cut_assembly_remarks.'</td>
    </tr>
    <tr>
        <td width="3%" style="text-align:center;">22</td>
        <td width="17%" style="text-align:left;">Embossing Coding Drive</td>
        <td width="55%">'.$embossing_coding.'</td>
        <td width="25%">'.$embossing_coding_remarks.'</td>
    </tr>
    <tr>
        <td width="3%" style="text-align:center;">23</td>
        <td width="17%" style="text-align:left;">Cooling Station Drive</td>
        <td width="55%">'.$cooling_station.'</td>
        <td width="25%">'.$cooling_station_remarks.'</td>
    </tr>
   
    <tr>
        <td width="3%" style="text-align:center;">24</td>
        <td width="17%" style="text-align:left;">Perforation Blade Drive</td>
        <td width="55%">'.$perforation_blade.'</td>
        <td width="25%">'.$perforation_blade_remarks.'</td>
    </tr>
<tr>
        <td width="3%" style="text-align:center;">25</td>
        <td width="17%" style="text-align:left;">Auger Drive</td>
        <td width="55%">'.$auger_drive.'</td>
        <td width="25%">'.$auger_drive_remarks.'</td>
    </tr>
    <tr>
        <td width="3%" style="text-align:center;">26</td>
        <td width="17%" style="text-align:left;">Individual Weight Adjustment</td>
        <td width="55%">'.$individual_weight.'</td>
        <td width="25%">'.$individual_weight_remarks.'</td>
    </tr>
  <tr>
        <td width="3%" style="text-align:center;">27</td>
        <td width="17%" style="text-align:left;">Overall Weight Adjustment</td>
        <td width="55%">'.$overall_weight_adjust.'</td>
        <td width="25%">'.$overall_weight_adjust_remarks.'</td>
    </tr>
</table>

<table class="table1">';
if($traverse_drive == 'Yes'){
    $html .=' <tr>
        <td width="3%" style="text-align:center;">28</td>
        <td width="17%" style="text-align:left;">Traverse Drive</td>
         <td width="27.5%">'.$traverse_drive.'</td>
        <td width="27.5%">'.$yes_traverse_drive.'</td>
        <td width="25%">'.$traverse_drive_remarks.'</td>
    </tr>';
}else if($traverse_drive == 'No'){
    $html .='<tr>
        <td width="3%" style="text-align:center;">28</td>
        <td width="17%" style="text-align:left;">Traverse Drive</td>
        <td width="55%">'.$traverse_drive.'</td>
        <td width="25%">'.$traverse_drive_remarks.'</td>
    </tr>';
}





   $html .=' <tr>
        <td width="3%" style="text-align:center;">29</td>
        <td width="17%" style="text-align:left;">Case Packer Drive</td>
        <td width="55%">'.$case_packer_drive.'</td>
        <td width="25%">'.$case_packer_drive_remarks.'</td>
    </tr>
    
    
   <tr>
        <td width="3%" style="text-align:center;">30</td>
        <td width="17%" style="text-align:left;">Nozzle/Funnel</td>
        <td width="27.5%">'.$nozzle_funnel.'</td>
        <td width="27.5%">'.$powder_option1.' '.$liquid_option1.' '.$liquid_shut_option1.'</td>
        <td width="25%">'.$nozzle_funnel_remarks.'</td>
    </tr>
    <tr>
        <td width="3%" style="text-align:center;">31</td>
        <td width="17%" style="text-align:left;">Hose Pipe (type)</td>
        <td width="55%">'.$hose_pipe.'</td>
        <td width="25%">'.$hose_pipe_remarks.'</td>
    </tr>
</table>
<table class="table1">';
if($batchcut == 'Straight'){
     $html .='<tr>
        <td width="3%" style="text-align:center;">32</td>
        <td width="17%" style="text-align:left;">Batch Cut Format</td>
        <td width="55%">'.$batchcut.'</td>
        <td width="25%">'.$batch_cut_format_remarks.'</td>
    </tr>';
}else if($batchcut == 'String'){
     $html .='<tr>
        <td width="3%" style="text-align:center;">32</td>
        <td width="17%" style="text-align:left;">Batch Cut Format</td>
        <td width="27.5%">'.$batchcut.'</td>
        <td width="27.5%">'.$string_option.'</td>
        <td width="25%">'.$batch_cut_format_remarks.'</td>
    </tr>';
}
 $html .='</table>
<table class="table1">
   
    <tr>
        <td width="3%" style="text-align:center;">33</td>
        <td width="17%" style="text-align:left;">Horizontal Sealer Width</td>
        <td width="55%" >'.$horizontal_sealing_width.'mm</td>
        <td width="25%" >'.$horizontal_sealer1_remarks.'</td>
    </tr>

    
    <tr>
        <td width="3%" style="text-align:center;">34</td>
        <td width="17%" style="text-align:left;">Vertical Sealer Width</td>
        <td width="55%" >'.$vertical_sealing_width.'mm</td>
        <td width="25%">'.$vertical_sealer1_remarks.'</td>
    </tr>
      <tr>
       <td width="3%" style="text-align:center;">35</td>
        <td width="17%" style="text-align:left;">HMI Size</td>
        <td width="55%">'.$hmi_size.'</td>
        <td width="25%">'.$hmi_size_remarks.'</td>
    </tr>
    </table><br pagebreak="true">
<table class="table1">
    
      <tr>
        <td width="3%" style="text-align:center;">36</td>
        <td width="17%" style="text-align:left;">Reel Shaft Type</td>
        <td width="55%">'.$reel_shaft_type.'</td>
        <td width="25%">'.$reel_shaft_type_remarks.'</td>
    </tr>
     <tr>
        <td width="3%" style="text-align:center;">37</td>
        <td width="17%" style="text-align:left;">Reel Core Diameter</td>
        <td width="55%">'.$reel_core_diameter.'</td>
        <td width="25%">'.$reel_core_diameter_remarks.'</td>
    </tr>
    <tr>
        <td width="3%" style="text-align:center;">38</td>
        <td width="17%" style="text-align:left;">Static Charge Eliminator</td>
        <td width="55%">'.$static_charge_eliminator.'</td>
        <td width="25%">'.$static_charge_eliminator_remarks.'</td>
    </tr>
    <tr>
        <td width="3%" style="text-align:center;">39</td>
        <td width="17%" style="text-align:left;">Laminate Details</td>
        <td width="55%">'.$laminate_detail.'</td>
        <td width="25%">'.$laminate_detail_remarks.'</td>
    </tr>
    <tr>
        <td width="3%" style="text-align:center;">40</td>
        <td width="17%" style="text-align:left;">KLD Clearance</td>
        <td width="55%">'.$kld_clearance.'</td>
        <td width="25%">'.$kld_clearance_remarks.'</td>
    </tr>
    <tr>
        <td width="3%" style="text-align:center;">41</td>
        <td width="17%" style="text-align:left;">Heater & SSR Fault Detection Box</td>
        <td width="55%">'.$heater_ssr_fault.'</td>
        <td width="25%">'.$heater_ssr_fault_remarks.'</td>
    </tr>
</table>
<table class="table1">
    
   <tr>
        <td width="3%" style="text-align:center;">42</td>
        <td width="17%" style="text-align:left;">Trial Material / Product</td>
        <td width="55%">'.$trial_material.'</td>
        <td width="25%">'.$trial_material_remarks.'</td>
    </tr>
    <tr>
        <td width="3%" style="text-align:center;">43</td>
        <td width="17%" style="text-align:left;">Pin Hole Assembly</td>
        <td width="55%">'.$pin_hole_assembly.'</td>
        <td width="25%">'.$pin_hole_assembly_remarks.'</td>
    </tr>
     <tr>
        <td width="3%" style="text-align:center;">44</td>
        <td width="17%" style="text-align:left;">Nitrogen Purging / Dust Suction</td>
        <td width="55%">'.$nitrogen_purging.'</td>
        <td width="25%">'.$nitrogen_purging_remarks.'</td>
    </tr>
     <tr>
        <td width="3%" style="text-align:center;">45</td>
        <td width="17%" style="text-align:left;">Heater Control System</td>
        <td width="55%">'.$heater_control_system.'</td>
        <td width="25%">'.$heater_control_system_remarks.'</td>
    </tr>
    <tr>
        <td width="3%" style="text-align:center;">46</td>
        <td width="17%" style="text-align:left;">Beacon Lights</td>
        <td width="55%">'.$beacon_light.'</td>
        <td width="25%">'.$beacon_light_remarks.'</td>
    </tr>
</table>
<table class="table1">';
if($hooper_level == 'Yes'){
    $html .='  <tr>
        <td width="3%" style="text-align:center;">47</td>
        <td width="17%" style="text-align:left;">Hopper Level Sensor</td>
        <td width="27.5%">'.$hooper_level.'</td>
       
        <td width="27.5%">'.$hooper_level_option.'</td>
        <td width="25%">'.$hooper_level_remarks.'</td>
    </tr>';
} else if($hooper_level == 'No'){
    $html .=' <tr>
       <td width="3%" style="text-align:center;">47</td>
        <td width="17%" style="text-align:left;">Hopper Level Sensor</td>
        <td width="55%">'.$hooper_level.'</td>
        <td width="25%">'.$hooper_level_remarks.'</td>
    </tr>';
}
   $html .='  <tr>
       <td width="3%" style="text-align:center;">48</td>
        <td width="17%" style="text-align:left;">Safety Relay + Safety Switch</td>
        <td width="55%">'.$safety_relay.'</td>
        <td width="25%">'.$safety_relay_remarks.'</td>
    </tr>

  

    <tr>
       <td width="3%" style="text-align:center;">49</td>
        <td width="17%" style="text-align:left;">PLC Make</td>
        <td width="55%">'.$plc_maker.'</td>
        <td width="25%">'.$plc_maker_remarks.'</td>
    </tr>
  
    ';
   
   
    $html .=' 
    <tr>
       <td width="3%" style="text-align:center;">50</td>
        <td width="17%" style="text-align:left;">Tool Kit</td>
        <td width="55%">'.$tool_kit.'</td>
        <td width="25%">'.$tool_kit_remarks.'</td>
    </tr>
    <tr>
       <td width="3%" style="text-align:center;">51</td>
        <td width="17%" style="text-align:left;">Changeover Parts</td>
        <td width="55%">'.$changeover_part.'</td>
        <td width="25%">'.$changeover_part_remarks.'</td>
    </tr>
</table>
<table class="table1">';

    $check_packer =array();

  $seond_pack = $this->db->select('*')->from('df_form_multi_track_machine_pack')->where('record_id',$id)->get();

   $i=1;
          if($seond_pack->num_rows()>0){
            foreach($seond_pack->result() as $sec_rows){
                $check_packer[]=$sec_rows->yes_secondary_pack;
            }}

    if($secondary_pack == 'Yes' && in_array('Case Packer',$check_packer)){
    $html .='
     <tr>
        <td width="3%" style="text-align:center;">52</td>
        <td width="17%" style="text-align:left;">Secondary Packaging Solution</td>
        <td width="18.333%" >'.$secondary_pack.'</td>';

  

              

        $html .='<td width="18.333%" style="text-align:left;">
        
        ';
        
      $i=1;
          if($seond_pack->num_rows()>0){
            foreach($seond_pack->result() as $sec_rows){

  $yes_secondary_pack= $sec_rows->yes_secondary_pack;
  $yes_secondary_pack_qty= $sec_rows->yes_secondary_pack_qty;
            

  if($yes_secondary_pack_qty>0){
 $html .=''.$yes_secondary_pack.'-   '.$yes_secondary_pack_qty.'<br>
     
    ';
  }
$i++;
     
        }
          }
        
        $html .='
      
        </td>';


       $html .=' <td width="18.333%">'.$case_packer.'</td>
        <td width="25%">'.$secondary_pack_remarks.'</td>
    </tr>';
    }else if($secondary_pack == 'Yes'){
    $html .='
    <tr>
        <td width="3%" style="text-align:center;">52</td>
        <td width="17%" style="text-align:left;">Secondary Packaging Solution</td>
        <td width="27.5%">'.$secondary_pack.'</td>';


      

        $html .='<td width="27.5%" style="text-align:left;">
        ';
        
       $i=1;
          if($seond_pack->num_rows()>0){
            foreach($seond_pack->result() as $sec_rows){

  $yes_secondary_pack= $sec_rows->yes_secondary_pack;
  $yes_secondary_pack_qty= $sec_rows->yes_secondary_pack_qty;
            

     $html .=''.$yes_secondary_pack.'-    '.$yes_secondary_pack_qty.'<br>
     
    ';

$i++;
        
        }
          }
        
        $html .='</td>';

        $html .='



        <td width="25%">'.$secondary_pack_remarks.'</td>
    </tr>';
    }else{

    $html .=' <tr>
        <td width="3%" style="text-align:center;">52</td>
        <td width="17%" style="text-align:left;">Secondary Packaging Solution</td>
        <td width="55%">'.$secondary_pack.'</td>
        <td width="25%">'.$secondary_pack_remarks.'</td>
    </tr>';
    }
$html .='</table>

<table class="table1">
<tr>
        <td width="3%" style="text-align:center;">53</td>
        <td width="17%" style="text-align:left;">Bagging Unit</td>
        <td width="55%">'.$bagging_unit.'</td>
        <td width="25%">'.$bagging_unit_remarks.'</td>
    </tr>
 <tr>
        <td width="3%" style="text-align:center;">54</td>
        <td width="17%" style="text-align:left;">Ladder & Platform</td>
        <td width="55%">'.$ladder_platform.'</td>
        <td width="25%">'.$ladder_platform_remarks.'</td>
    </tr>
';

if($machine_guarding == 'Aluminium'){
 $html .='<tr>
        <td width="3%" style="text-align:center;">55</td>
        <td width="17%" style="text-align:left;">Machine Guarding</td>
        <td width="27.5%">'.$machine_guarding.'</td>
        <td width="27.5%">'.$aluminium_option.'</td>
        <td width="25%">'.$machine_guarding_remarks.'</td>
    </tr>';
}else if($machine_guarding == 'SS-304'){
     $html .='<tr>
        <td width="3%" style="text-align:center;">55</td>
        <td width="17%" style="text-align:left;">Machine Guarding</td>
          <td width="27.5%">'.$machine_guarding.'</td>
        <td width="27.5%">'.$ss_304_option.'</td>
        <td width="25%">'.$machine_guarding_remarks.'</td>
    </tr>';

}else{
   $html .=' <tr>
        <td width="3%" style="text-align:center;">55</td>
        <td width="17%" style="text-align:left;">Machine Guarding</td>
        <td width="55%">'.$machine_guarding.'</td>
        <td width="25%">'.$machine_guarding_remarks.'</td>
    </tr>
    
';
}
$html .=' </table>
    <br pagebreak="true">
<table class="table1">
 <tr>
        <td width="3%" style="text-align:center;">56</td>
        <td width="17%" style="text-align:left;">Tilting Flaps (Open/Close) Drive</td>
        <td width="55%">'.$tilting_flaps.'</td>
        <td width="25%">'.$tilting_flaps_remarks.'</td>
    </tr>
     <tr>
        <td width="3%" style="text-align:center;">57</td>
        <td width="17%" style="text-align:left;">Tilting Movement Drive</td>
        <td width="55%">'.$tilting_movement_drive.'</td>
        <td width="25%">'.$tilting_movement_drive_remarks.'</td>
    </tr>

     <tr>
        <td width="3%" style="text-align:center;">58</td>
        <td width="17%" style="text-align:left;">Vertical and Horizontal Up-down Movement</td>
        <td width="55%">'.$vert_hori_movement.'</td>
        <td width="25%">'.$vert_hori_movement_remarks.'</td>
    </tr>

  
     <tr>
        <td width="3%" style="text-align:center;">59</td>
        <td width="17%" style="text-align:left;">Collating Conveyor Drive</td>
        <td width="55%">'.$collating_conveyor.'</td>
        <td width="25%">'.$collating_conveyor_remarks.'</td>
    </tr>
     <tr>
        <td width="3%" style="text-align:center;">60</td>
        <td width="17%" style="text-align:left;">Rope Conveyor Drive</td>
        <td width="55%">'.$rope_conveyor.'</td>
        <td width="25%">'.$rope_conveyor_remarks.'</td>
    </tr>
     <tr>
        <td width="3%" style="text-align:center;">61</td>
        <td width="17%" style="text-align:left;">Take-Off Conveyor Drive</td>
        <td width="55%">'.$take_off_conveyor.'</td>
        <td width="25%">'.$take_off_conveyor_remarks.'</td>
    </tr>
     <tr>
        <td width="3%" style="text-align:center;">62</td>
        <td width="17%" style="text-align:left;">Weighing Conveyor Drive</td>
        <td width="55%">'.$weighing_conveyor_drive.'</td>
        <td width="25%">'.$weighing_conveyor_drive_remarks.'</td>
    </tr>

</table>
<table class="table1">
 <tr>
        <td width="3%" style="text-align:center;">63</td>
        <td width="17%" style="text-align:left;">SPECIAL NOTES</td>';


   
            
       $html .=' <td width="80%" style="text-align:left;">'.$special_notes.'</td>';
      

   $html .=' </tr>
</table>
<table class="table1">
    <tr>
        <td width="100%" style="text-align:center;">Reviewed By:</td>
    </tr>
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
//echo $fileNL; exit;
$pdf->Output($filelocation.$fileNL, 'F');

  $pdf->Output($filelocation.$fileNL, 'I');

$url6 = base64_encode($fileNL);

// echo $url6; exit;
//header('location:'.$pageurl.$this->uri->segment(3)."/".$this->uri->segment(4)."/".$url6); 
//============================================================+
// END OF FILE
//============================================================+
