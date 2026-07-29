<?php
// $CI = &get_instance();
$po_id = $this->uri->segment(3);

// $lead_id = $this->uri->segment(4);

// $CI->load->model('Salescrm_model', 'salescrm');

// $quote_record_id = $this->salescrm->getRecordID($lead_id);

$po_rec = $this->db->select('df_number')->from('poreceived')->where('id', $po_id)->get();

if ($po_rec->num_rows() > 0) {
    foreach ($po_rec->result() as $row_rec);

    $nextdf = $row_rec->df_number;
}



// echo $record_id; exit;

// $annexture_1 = $this->salescrm->getannexture_1($quote_record_id);
// // echo "<pre>"; print_r($annexture_1); exit;
// if (count($annexture_1) > 0) {
//     $product_to_be_packed = $annexture_1[0];
//     $batchcut = $annexture_1[16];
//     $liquidviscositydata = $annexture_1[10];
//     $powderdensitydata = $annexture_1[12];
//     $qty_data = $this->salescrm->getQtyPackedData($quote_record_id);
//      $horizontal_sealing_width=$annexture_1[4];
//      $vertical_sealing_width=$annexture_1[5];
//      $typeofsealing=$annexture_1[7];
//      $liquid_option = $annexture_1[17];
//      $powder_option = $annexture_1[18];
//      $non_viscous_option=$annexture_1[19];
// 	$viscous_option=$annexture_1[20];
// 	$piston_filler_option=$annexture_1[21];
// 	$follow_meter_option=$annexture_1[22];
// 	$free_flow_option=$annexture_1[23];
// 	$weigher_system_option=$annexture_1[24];
// 	$liner_weigher_option=$annexture_1[25];
// 	$mult_head_weigher_option=$annexture_1[26];
// 	$volumetric_cap_option=$annexture_1[27];
// 	$non_free_flow_option=$annexture_1[28];
//      $power_supply=$annexture_1[9];
//      $machine_type=$annexture_1[29];
//       $cup_filler_option=$annexture_1[31];
// } else {
//     $product_to_be_packed = '';
//     $batchcut = '';
//     $liquidviscositydata = '';
//     $powderdensitydata = '';
//     $qty_data = '';
//     $horizontal_sealing_width='';
//      $vertical_sealing_width='';
//      $typeofsealing='';
//       $liquid_option = '';
//      $powder_option = '';
//      	$non_viscous_option='';
// 	$viscous_option='';
// 	$piston_filler_option='';
// 	$follow_meter_option='';
// 	$free_flow_option='';
// 	$weigher_system_option='';
// 	$liner_weigher_option='';
// 	$mult_head_weigher_option='';
// 	$volumetric_cap_option='';
// 	$non_free_flow_option='';
//      $power_supply='';
//        $machine_type='';
//          $cup_filler_option='';
// }



// $getannexture_2 = $this->salescrm->getannexture_2($quote_record_id);
// // echo "<pre>"; print_r($getannexture_2); exit;
// if (count($getannexture_2) > 0) {
//     $no_of_track = $getannexture_2[3];
//     $machinemodel = $getannexture_2[0];
// } else {
//     $no_of_track = '';
//     $machinemodel = '';
// }

$qry = $this->db->select('*')->from('basic_machine_df_design_form_table')->where('po_id', $po_id)->get();

if ($qry->num_rows() > 0) {
    foreach ($qry->result() as $rows);
    //echo "<pre>"; print_r($rows); exit;

    $df_form_name = $rows->design_form_name;
    $design_form_date = $rows->design_form_date;
    $reference_no = $rows->reference_no;
    $ref_df_date = $rows->ref_df_date;

    $iom_no = $rows->iom_no;
    $invoice_no = $rows->invoice_no;
    $invoice_date = $rows->invoice_date;
    $id = $rows->id;

    // $record_id = $rows->id;
}




$multi = $this->db->select('*')->from('basic_machine_df_form_multi_track_machine')->where('record_id', $id)->get();

if ($multi->num_rows() > 0) {
    foreach ($multi->result() as $multi_rows);

    $ce_complied = $multi_rows->ce_complied;
    $date_of_po = $multi_rows->date_of_po;
    $penalty_clause = $multi_rows->penalty_clause;
    $ce_complied_remarks = $multi_rows->ce_complied_remarks;
    $date_of_po_remarks = $multi_rows->date_of_po_remarks;
    $penalty_clause_remarks = $multi_rows->penalty_clause_remarks;
    $dispatch_date = $multi_rows->dispatch_date;
    $dispatch_date_remarks = $multi_rows->dispatch_date_remarks;
    $trial_date = $multi_rows->trial_date;
    $trial_date_remarks = $multi_rows->trial_date_remarks;
    $machine_mode_no = $multi_rows->machine_mode_no;
    $machine_mode_no_remarks = $multi_rows->machine_mode_no_remarks;
    $machine_type = $multi_rows->machine_type;
    $machine_type_remarks = $multi_rows->machine_type_remarks;
    $machine_orientation = $multi_rows->machine_orientation;
    $machine_orientation_remarks = $multi_rows->machine_orientation_remarks;
}

$multi_spec = $this->db->select('*')->from('basic_machine_df_form_machine_specification')->where('record_id', $id)->get();

if ($multi_spec->num_rows() > 0) {
    foreach ($multi_spec->result() as $spec_rows);

    $tracks = $spec_rows->tracks;
    $product_packed = $spec_rows->product_packed;
    $powder_option = $spec_rows->powder_option;
    $liquid_option = $spec_rows->liquid_option;
    $non_viscous_option = $spec_rows->non_viscous_option;
    $viscous_option = $spec_rows->viscous_option;
    $piston_filler_option = $spec_rows->piston_filler_option;
    $follow_meter_option = $spec_rows->follow_meter_option;
    $non_free_flow_option = $spec_rows->non_free_flow_option;
    $cup_filler_option = $spec_rows->cup_filler_option;
    $free_flow_option = $spec_rows->free_flow_option;
    $weigher_system_option = $spec_rows->weigher_system_option;
    $liner_weigher_option = $spec_rows->liner_weigher_option;
    $mult_head_weigher_option = $spec_rows->mult_head_weigher_option;
    $volumetric_cap_option = $spec_rows->volumetric_cap_option;
    $profile_of_sealing = $spec_rows->profile_of_sealing;
    $tracks_remarks = $spec_rows->tracks_remarks;
    $product_packed_remarks = $spec_rows->product_packed_remarks;
    $filling_unit_remarks = $spec_rows->filling_unit_remarks;
    $density = $spec_rows->density;
    $viscosity = $spec_rows->viscosity;
    $product_specification_remarks = $spec_rows->product_specification_remarks;
    $profle_sealing_remarks = $spec_rows->profle_sealing_remarks;
}

$multitrack = $this->db->select('*')->from('basic_machine_df_form_multi_track_machine1')->where('record_id', $id)->get();


if ($multitrack->num_rows() > 0) {
    foreach ($multitrack->result() as $spec_rows);

    $notching_option = $spec_rows->notching_option;
    $notching_option_remarks = $spec_rows->notching_option_remarks;
    $hooper_details = $spec_rows->hooper_details;
    $openable_option = $spec_rows->openable_option;
    $closed_option = $spec_rows->closed_option;
    $pressurised_option = $spec_rows->pressurised_option;
    $closed_pressurised_option = $spec_rows->closed_pressurised_option;
    $non_pressurised_option = $spec_rows->non_pressurised_option;
    $non_closed_pressurised_option = $spec_rows->non_closed_pressurised_option;
    $hooper_details_remarks = $spec_rows->hooper_details_remarks;
    $cladding_provision = $spec_rows->cladding_provision;
    $cladding_provision_remarks = $spec_rows->cladding_provision_remarks;
    $embossing = $spec_rows->embossing;
    $embossing_option = $spec_rows->embossing_option;
    $linear_option = $spec_rows->linear_option;
    $rotary_option = $spec_rows->rotary_option;
    $provision_remarks = $spec_rows->provision_remarks;
    $web_aligner = $spec_rows->web_aligner;

    $web_aligner_option = $spec_rows->web_aligner_option;
    $web_aligner_remarks = $spec_rows->web_aligner_remarks;
}

$multitrack1 = $this->db->select('*')->from('basic_machine_df_form_multi_track_machine2')->where('record_id', $id)->get();


if ($multitrack1->num_rows() > 0) {
    foreach ($multitrack1->result() as $spec_rows);

    $center_slitting = $spec_rows->center_slitting;
    $center_slitting_remarks = $spec_rows->center_slitting_remarks;
    $vertical_slitting = $spec_rows->vertical_slitting;
    $vertical_slitting_remarks = $spec_rows->vertical_slitting_remarks;
    $vertical_sealer = $spec_rows->vertical_sealer;
    $vertical_sealer_remarks = $spec_rows->vertical_sealer_remarks;
    $laminate_pulling = $spec_rows->laminate_pulling;
    $laminate_pulling_remarks = $spec_rows->laminate_pulling_remarks;
    $up_down = $spec_rows->up_down;
    $up_down_remarks = $spec_rows->up_down_remarks;
    $embossing_coding = $spec_rows->embossing_coding;
    $embossing_coding_remarks = $spec_rows->embossing_coding_remarks;
    $cooling_station = $spec_rows->cooling_station;
    $cooling_station_remarks = $spec_rows->cooling_station_remarks;
    $horizontal_sealer = $spec_rows->horizontal_sealer;
    $horizontal_sealer_remarks = $spec_rows->horizontal_sealer_remarks;
    $perforation_blade = $spec_rows->perforation_blade;
    $perforation_blade_remarks = $spec_rows->perforation_blade_remarks;
    $priston_drive = $spec_rows->priston_drive;
    $priston_drive_remarks = $spec_rows->priston_drive_remarks;
    $shutt_off_nozzle = $spec_rows->shutt_off_nozzle;
    $shutt_off_nozzle_remarks = $spec_rows->shutt_off_nozzle_remarks;
    $filling_plate_drive = $spec_rows->filling_plate_drive;
    $filling_plate_drive_remarks = $spec_rows->filling_plate_drive_remarks;
    $individual_weight = $spec_rows->individual_weight;
    $individual_weight_remarks = $spec_rows->individual_weight_remarks;
    $overall_weight_adjust = $spec_rows->overall_weight_adjust;
    $overall_weight_adjust_remarks = $spec_rows->overall_weight_adjust_remarks;
    $vertical_sealer_width = $spec_rows->vertical_sealer_width;
    $horizontal_sealer_width = $spec_rows->horizontal_sealer_width;
}

$multitrack2 = $this->db->select('*')->from('basic_machine_df_form_multi_track_machine3')->where('record_id', $id)->get();

if ($multitrack2->num_rows() > 0) {
    foreach ($multitrack2->result() as $spec_rows);

    $traverse_drive = $spec_rows->traverse_drive;
    $yes_traverse_drive = $spec_rows->yes_traverse_drive;
    $traverse_drive_remarks = $spec_rows->traverse_drive_remarks;
    $printer_yes_no = $spec_rows->printer_yes_no;
    $printer = $spec_rows->printer;
    $inkjet_option = $spec_rows->inkjet_option;
    $tto_option = $spec_rows->tto_option;
    $thermal_inkjet_option = $spec_rows->thermal_inkjet_option;
    $printer_remarks = $spec_rows->printer_remarks;
    $case_packer_drive = $spec_rows->case_packer_drive;
    $case_packer_drive_remarks = $spec_rows->case_packer_drive_remarks;
    $nozzle_funnel = $spec_rows->nozzle_funnel;
    $powder_option1 = $spec_rows->powder_option1;
    $liquid_option1 = $spec_rows->liquid_option1;
    $liquid_shut_option1 = $spec_rows->liquid_shut_option1;
    $nozzle_funnel_remarks = $spec_rows->nozzle_funnel_remarks;
    $hose_pipe = $spec_rows->hose_pipe;
    $hose_pipe_remarks = $spec_rows->hose_pipe_remarks;
    $batch_cut_format = $spec_rows->batch_cut_format;
    $string_option = $spec_rows->string_option;
    $batch_cut_format_remarks = $spec_rows->batch_cut_format_remarks;
}

$multitrack3 = $this->db->select('*')->from('basic_machine_df_form_multi_track_machine4')->where('record_id', $id)->get();

if ($multitrack3->num_rows() > 0) {
    foreach ($multitrack3->result() as $spec_rows);
    $horizontal_sealer1 = $spec_rows->horizontal_sealer1;
    $horizontal_sealer1_remarks = $spec_rows->horizontal_sealer1_remarks;
    $vertical_sealer1 = $spec_rows->vertical_sealer1;
    $vertical_sealer1_remarks = $spec_rows->vertical_sealer1_remarks;
    $rotary_valve_coating = $spec_rows->rotary_valve_coating;
    $rotary_valve_coating_remarks = $spec_rows->rotary_valve_coating_remarks;
    $working_speed = $spec_rows->working_speed;
    $working_speed_remarks = $spec_rows->working_speed_remarks;
    $reel_shaft_type = $spec_rows->reel_shaft_type;
    $reel_shaft_type_remarks = $spec_rows->reel_shaft_type_remarks;
    $reel_core_diameter = $spec_rows->reel_core_diameter;
    $reel_core_diameter_remarks = $spec_rows->reel_core_diameter_remarks;
    $trial_material = $spec_rows->trial_material;
    $trial_material_remarks = $spec_rows->trial_material_remarks;
    $laminate_detail = $spec_rows->laminate_detail;
    $laminate_detail_remarks = $spec_rows->laminate_detail_remarks;
    $heater_control_system = $spec_rows->heater_control_system;
    $heater_control_system_remarks = $spec_rows->heater_control_system_remarks;
    $beacon_light = $spec_rows->beacon_light;
    $beacon_light_remarks = $spec_rows->beacon_light_remarks;
    $hooper_level = $spec_rows->hooper_level;
    $hooper_level_option = $spec_rows->hooper_level_option;
    $hooper_level_remarks = $spec_rows->hooper_level_remarks;
    $safety_relay = $spec_rows->safety_relay;
    $safety_relay_remarks = $spec_rows->safety_relay_remarks;
    $plc_maker = $spec_rows->plc_maker;
    $plc_maker_remarks = $spec_rows->plc_maker_remarks;
    $supply_voltage = $spec_rows->supply_voltage;
    $supply_voltage_remarks = $spec_rows->supply_voltage_remarks;
    $hmi_size = $spec_rows->hmi_size;
    $hmi_size_remarks = $spec_rows->hmi_size_remarks;
    $cip_system = $spec_rows->cip_system;
    $cip_system_option = $spec_rows->cip_system_option;
    $cip_system_option_remarks = $spec_rows->cip_system_option_remarks;
    $tool_kit = $spec_rows->tool_kit;
    $tool_kit_remarks = $spec_rows->tool_kit_remarks;
    $changeover_part = $spec_rows->changeover_part;
    $changeover_part_remarks = $spec_rows->changeover_part_remarks;
}


$multitrack4 = $this->db->select('*')->from('basic_machine_df_form_multi_track_machine5')->where('record_id', $id)->get();

if ($multitrack4->num_rows() > 0) {
    foreach ($multitrack4->result() as $spec_rows);

    $secondary_pack = $spec_rows->secondary_pack;
    $yes_secondary_pack = $spec_rows->yes_secondary_pack;
    $case_packer = $spec_rows->case_packer;
    $secondary_pack_remarks = $spec_rows->secondary_pack_remarks;
    $ladder_platform_remarks = $spec_rows->ladder_platform_remarks;
    $machine_guarding = $spec_rows->machine_guarding;
    $aluminium_option = $spec_rows->aluminium_option;
    $ss_304_option = $spec_rows->ss_304_option;
    $machine_guarding_remarks = $spec_rows->machine_guarding_remarks;
    $special_notes = $spec_rows->special_notes;
    $ladder_platform = $spec_rows->ladder_platform;
}

$sizepouch = $this->db->select('*')->from('basic_machine_df_form_machine_specification_size_qnty')->where('record_id', $id)->get();


if ($sizepouch->num_rows() > 0) {

    foreach ($sizepouch->result() as $rowws);

    $pouch_size_remarks = $rowws->pouch_size_remarks;
    $quantity_packed_remarks = $rowws->quantity_packed_remarks;
}





?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title>Edit DF Project Form | <?php echo sitetitle; ?></title>
    <!-- Table Responsive css -->
    <script src="<?php echo assets_url; ?>js/angular.min.js"></script>
    <!-- DataTables -->
    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script> -->
    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">

    <script type="text/javascript" src="<?php echo assets_url; ?>ckeditor/ckeditor.js"></script>
    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>

    <?PHP

    $q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();

    foreach ($q->result() as $LOGO);

    ?>

    <style>
        table.manglesh thead th {
            background: <?php echo $LOGO->colorcode; ?>;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }

        table.manglesh tbody td {
            text-align: center;
        }

        .text--box {
            position: absolute;
            top: 2px;
            width: 98.5%;
            min-height: 90% !important;
        }
    </style>
</head>

<body>
    <!-- Navigation Bar-->
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>

    <!-- End Navigation Bar-->


    <?php $this->load->view('common/info-section.php'); ?>


    <div class="wrapper">

        <div class="container">



            <!-- Page-Title -->

            <div class="row" style="margin-top:20px;">

                <?php
                // $q = $this->db->select('df_sr_no')->from('df_release')->limit(1)->order_by('id','desc')->get();
                // foreach($q->result() as $existingdfno);
                //     if($existingdfno->df_sr_no==1730 || $existingdfno->df_sr_no>1730){
                //         $nextdf = $existingdfno->df_sr_no+1;
                //     }else{
                //         $nextdf = '1730';
                //     }

                ?>
                <div class="col-md-12">
                    <h2 class="text-center">DF Review Meeting</h2>
                    <hr>
                </div>

                <div class="col-sm-12">

                    <div class="form_box">
                        <form action="<?php echo page_url; ?>Fetch_dynamic_data/basic_machine_df_project_form_update/<?php echo $this->uri->segment(3); ?>/<?php echo $id; ?>" enctype="multipart/form-data" method="post">
                            <table class="table">
                                <tr>
                                    <th width="50%">From: Project Dept.</th>
                                    <th class="text-center" width="50%">Reference DF. No.-</th>
                                    <!-- <th colspan="2" width="35%">To: All Departments</th>-->

                                </tr>
                                <tr>
                                    <td class="text-center" rowspan="9" style="vertical-align: middle;">
                                        <p><b>DF No.<br>
                                                <!-- <input type="text" class="form-control" value="<?php //echo $df_form_name; 
                                                                                                    ?>" readonly style="text-align: center;" name="design_form_name" required> -->

                                                <b><?php echo $nextdf; ?></b><br>

                                                <b>Date:</b><input type="date" class="form-control" value="<?php echo $design_form_date; ?>" style="text-align: center;" name="design_form_date" required></b></p>
                                    </td>
                                    <td class="text-center" rowspan="9" style="vertical-align: middle;">
                                        <input type="text" name="reference_no" class="form-control" value="<?php echo $reference_no; ?>" style="text-align: center;" required>

                                        <p><b>Reference DF Date:<br><input type="date" class="form-control" value="<?php echo $ref_df_date; ?>" style="text-align: center;" readonly name="ref_df_date" required></b></p>
                                    </td>
                                    <!--<td width="10%">Design:</td>
                                    <td width="25%"><input type="text" class="form-control" name="design" required></td>-->

                                </tr>

                                <!--  <tr>
                                   <td>Service:</td>
                                    <td><input type="text" class="form-control" name="service" required></td>
                                    <td rowspan="6" class="text-center" rowspan="9" style="vertical-align: middle;">
                                        <p><b>SIGN & STAMPED</b></p>
                                    </td>
                                    <td rowspan="6"></td>
                                </tr>-->
                                <!-- <tr>
                                    <td>Stores:</td>
                                    <td><input type="text" class="form-control" name="stores" required></td>
                                </tr>
                                <tr>
                                    <td>Electrical:</td>
                                    <td><input type="text" class="form-control" name="electrical" required></td>
                                </tr>
                                <tr>
                                    <td>Purchase</td>
                                    <td><input type="text" class="form-control" name="purchase" required></td>
                                </tr>
                                <tr>
                                    <td>Planning:</td>
                                    <td><input type="text" class="form-control" name="planning" required></td>
                                </tr>
                                <tr>
                                    <td>QC:</td>
                                    <td><input type="text" class="form-control" name="qc" required></td>
                                </tr>-->
                            </table>
                            <table class="table">
                                <tr>
                                    <th class="text-center" colspan="3">Order Details For MULTI TRACK MACHINE</th>
                                    <th class="text-center" colspan="1" width="25%">REMARKS</th>
                                </tr>

                                <tr>
                                    <td class="text-center" width="3%">>></td>
                                    <td width="17%">CE complied</td>
                                    <td width="50%" class="text-center">
                                        <select name="ce_complied" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Yes" <?php if ($ce_complied == 'Yes') { ?> selected <?php } ?>>Yes</option>
                                            <option value="No" <?php if ($ce_complied == 'No') { ?> selected <?php } ?>>No</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" value="<?php echo $ce_complied_remarks; ?>" name="ce_complied_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center">>></td>
                                    <td>Date of PO</td>

                                    <?php
                                    $q = $this->db->select('podate')->from('poreceived')->where('id', $po_id)->get();


                                    foreach ($q->result() as $dfform);
                                    ?>
                                    <td width="50%" class="text-center">
                                        <input type="date" name="date_of_po" class="form-control" value="<?php echo $dfform->podate; ?>" readonly>


                                    </td>
                                    <?php ?>

                                    <td><input type="text" class="form-control" name="date_of_po_remarks" value="<?php echo $date_of_po_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center">>></td>
                                    <td>Penalty Clause</td>
                                    <td width="50%" class="text-center">
                                        <select name="penalty_clause" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Yes" <?php if ($penalty_clause == 'Yes') { ?> selected <?php } ?>>Yes</option>
                                            <option value="No" <?php if ($penalty_clause == 'No') { ?> selected <?php } ?>>No</option>
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control" name="penalty_clause_remarks" value="<?php echo $penalty_clause_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center">>></td>
                                    <td>Dispatch date</td>
                                    <td width="50%" class="text-center">

                                        <input type="date" class="form-control" name="dispatch_date" value="<?php echo $dispatch_date; ?>" required>




                                        <!-- <?php echo date('d-m-Y', strtotime($dispatch_date)); ?> -->

                                    </td>
                                    <td><input type="text" class="form-control" name="dispatch_date_remarks" value="<?php echo $dispatch_date_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center">>></td>
                                    <td>Trial Date</td>

                                    <td width="50%" class="text-center">
                                        <input type="date" class="form-control" name="trial_date" value="<?php echo $trial_date; ?>" required>

                                    </td>

                                    <td><input type="text" class="form-control" name="trial_date_remarks" value="<?php echo $trial_date_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center">>></td>
                                    <td>Machine Model No.</td>
                                    <td width="50%" class="text-center">

                                        <input type="text" name="machine_mode_no" class="form-control" value="<?php echo $machine_mode_no; ?>">



                                    </td>
                                    <td><input type="text" class="form-control" name="machine_mode_no_remarks" value="<?php echo $machine_mode_no_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center">>></td>
                                    <td>Machine Type</td>
                                    <td width="50%" class="text-center">
                                        <input type="text" name="machine_type" class="form-control" value="<?php echo $machine_type; ?>">

                                      


                                    </td>
                                    <td><input type="text" class="form-control" name="machine_type_remarks" value="<?php echo $machine_type_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center">>></td>
                                    <td>Machine Orientation</td>
                                    <td width="50%" class="text-center"><input type="text" name="machine_orientation" class="form-control" value="<?php echo $machine_orientation; ?>"></td>
                                    <td><input type="text" class="form-control" name="machine_orientation_remarks" value="<?php echo $machine_orientation_remarks; ?>"></td>
                                </tr>
                            </table>
                            <table class="table">
                                <tr>
                                    <th class="text-center" colspan="3">Machine Specification</th>
                                    <th class="text-center" colspan="1" width="25%">REMARKS</th>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">1</td>
                                    <td width="17%">No. of Tracks</td>
                                    <td width="25%" class="text-center">

                                        <input type="text" name="tracks" class="form-control" value="<?php echo $tracks; ?>" required>



                                    </td>
                                    <td><input type="text" class="form-control" name="tracks_remarks" value="<?php echo $tracks_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">2</td>
                                    <td width="17%">Product to be Packed</td>
                                    <td width="25%" class="text-center">
                                        <select name="product_packed" id="product_packed" class="form-control" onchange="selectedoption()" required>
                                            <option value="">--Select--</option>
                                            <option value="1" id="liquid" <?php if ($product_packed == 1) { ?> selected <?php } ?>>Liquid / Paste</option>
                                            <option value="2" id="powder" <?php if ($product_packed == 2) { ?> selected <?php } ?>>Powder / Granules</option>

                                        </select>





                                        <div id="powder_option" style="display: none;">
                                            <br>
                                            <select name="powder_option" id="powder_option_select" class="form-control" onchange="selectpow()">
                                                <option value="">--Select--</option>
                                                <option value="Free Flow" <?php if ($powder_option == 'Free Flow') { ?> selected <?php } ?>>Free Flow</option>
                                                <option value="Non Free Flow" <?php if ($powder_option == 'Non Free Flow') { ?> selected <?php } ?>>Non Free Flow</option>
                                            </select>
                                        </div>




                                        <div id="liquid_option" style="display: none;">
                                            <br>
                                            <select name="liquid_option" id="liquid_option_select" class="form-control" onchange="selectliq()">
                                                <option value="">--Select--</option>
                                                <option value="Non-Viscous" <?php if ($liquid_option == 'Non-Viscous') { ?> selected <?php } ?>>Non-Viscous</option>
                                                <option value="Viscous" <?php if ($liquid_option == 'Viscous') { ?> selected <?php } ?>>Viscous</option>
                                            </select>
                                        </div>






                                    </td>
                                    <td><input type="text" class="form-control" name="product_packed_remarks" value="<?php echo $product_packed_remarks; ?>"></td>
                                </tr>

                                <script>
                                    function selectedoption() {
                                        var selectedValue = $('#product_packed').val();

                                        $('#powder_option').hide();
                                        $('#liquid_option').hide();
                                        $('#free_flow_option').hide();
                                        $('#weigher_system_option').hide();
                                        $('#liner_weigher_option').hide();
                                        $('#mult_head_weigher_option').hide();
                                        $('#volumetric_cap_option').hide();
                                        $('#non_viscous_option').hide();
                                        $('#viscous_option').hide();
                                        $('#piston_filler_option').hide();
                                        $('#follow_meter_option').hide();

                                        $('#density').hide();
                                        $('#viscosity').hide();

                                        if (selectedValue == '2') {
                                            $('#powder_option').show();
                                            $('#density').show();
                                        } else if (selectedValue == '1') {
                                            $('#liquid_option').show();
                                            $('#viscosity').show();

                                        }
                                    }

                                    function selectliq() {

                                        var selectedValue = $('#liquid_option_select').val();

                                        $('#non_viscous_option').hide();
                                        $('#viscous_option').hide();

                                        if (selectedValue == 'Non-Viscous') {
                                            $('#non_viscous_option').show();
                                        } else if (selectedValue == 'Viscous') {
                                            $('#viscous_option').show();

                                        }

                                    }

                                    function selectpow() {

                                        var selectedValue = $('#powder_option_select').val();

                                        $('#free_flow_option').hide();
                                        $('#non_free_flow').hide();

                                        if (selectedValue == 'Free Flow') {
                                            $('#free_flow_option').show();
                                        } else if (selectedValue == 'Non Free Flow') {
                                            $('#non_free_flow').show();

                                        }

                                    }


                                    function weigher_fun() {

                                        var selectedValue = $('#free_flow_option_select').val();


                                        $('#weigher_system_option').hide();
                                        $('#volumetric_cap_option').hide();


                                        if (selectedValue == 'Weigher System') {
                                            $('#weigher_system_option').show();
                                        } else if (selectedValue == 'Volumetric Cup Filler') {
                                            $('#volumetric_cap_option').show();
                                            $('#liner_weigher_option').hide();
                                        }
                                    }


                                    function weigher_system() {

                                        var selectedValue = $('#weigher_system_option_select').val();

                                        // Hide all related options by default
                                        $('#liner_weigher_option').hide();
                                        $('#mult_head_weigher_option').hide();

                                        // Show the relevant option based on the weigher system selection
                                        if (selectedValue == 'Liner Weigher') {
                                            $('#liner_weigher_option').show();
                                        } else if (selectedValue == 'Multi Head Weigher') {
                                            $('#mult_head_weigher_option').show();
                                        }
                                    }

                                    function viscous() {
                                        var selectedValue = $('#viscous_option_select').val();

                                        // Hide piston_filler_option and follow_meter_option by default
                                        $('#piston_filler_option').hide();
                                        $('#follow_meter_option').hide();

                                        // Show the relevant option based on the viscous selection
                                        if (selectedValue == 'Piston Filler') {
                                            $('#piston_filler_option').show();
                                        } else if (selectedValue == 'Flow meter') {
                                            $('#follow_meter_option').show();
                                        }
                                    }

                                    $(document).ready(function() {

                                        selectedoption();

                                        selectliq();

                                        selectpow();

                                        weigher_fun();

                                        weigher_system();

                                        viscous();


                                        $('#product_packed').change(function() {
                                            var selectedValue = $(this).val();


                                            $('#viscous_option_select').val('');

                                            $('#weigher_system_option_select').val('');
                                            $('#liner_weigher_option_select').val('');
                                            $('#mult_head_weigher_option_select').val('');
                                            $('#non_viscous_option_select').val('');
                                            $('#piston_filler_option_select').val('');


                                            $('#powder_option').hide();
                                            $('#liquid_option').hide();
                                            $('#free_flow_option').hide();
                                            $('#weigher_system_option').hide();
                                            $('#liner_weigher_option').hide();
                                            $('#mult_head_weigher_option').hide();
                                            $('#volumetric_cap_option').hide();
                                            $('#non_viscous_option').hide();
                                            $('#viscous_option').hide();
                                            $('#piston_filler_option').hide();
                                            $('#follow_meter_option').hide();


                                            if (selectedValue == '2') {
                                                $('#powder_option').show();
                                            } else if (selectedValue == '1') {
                                                $('#liquid_option').show();
                                            }
                                        });


                                        $('#liquid_option_select').change(function() {
                                            var selectedValue = $(this).val();


                                            $('#non_viscous_option').hide();
                                            $('#viscous_option').hide();
                                            $('#piston_filler_option').hide();

                                            // Show options based on the selected liquid type
                                            if (selectedValue == 'Non-Viscous') {
                                                $('#non_viscous_option').show();
                                            } else if (selectedValue == 'Viscous') {
                                                $('#viscous_option').show();
                                            }
                                        });

                                        // Handle viscous_option_select change
                                        $('#viscous_option_select').change(function() {
                                            var selectedValue = $(this).val();

                                            // Hide piston_filler_option and follow_meter_option by default
                                            $('#piston_filler_option').hide();
                                            $('#follow_meter_option').hide();

                                            // Show the relevant option based on the viscous selection
                                            if (selectedValue == 'Piston Filler') {
                                                $('#piston_filler_option').show();
                                            } else if (selectedValue == 'Flow meter') {
                                                $('#follow_meter_option').show();
                                            }
                                        });

                                        // Handle powder_option_select change
                                        $('#powder_option_select').change(function() {
                                            var selectedValue = $(this).val();

                                            // Hide all related options by default
                                            $('#free_flow_option').hide();
                                            $('#non_free_flow').hide();

                                            // Show the relevant option based on the powder type
                                            if (selectedValue == 'Free Flow') {
                                                $('#free_flow_option').show();
                                            } else if (selectedValue == 'Non Free Flow') {
                                                $('#non_free_flow').show();
                                                $('#weigher_system_option').hide();
                                                $('#liner_weigher_option').hide();
                                                $('#mult_head_weigher_option').hide();
                                                $('#volumetric_cap_option').hide();
                                            }
                                        });

                                        // Handle free_flow_option_select change
                                        $('#free_flow_option_select').change(function() {
                                            var selectedValue = $(this).val();

                                            // Hide all related options by default
                                            $('#weigher_system_option').hide();
                                            $('#volumetric_cap_option').hide();

                                            // Show the relevant option based on the free flow selection
                                            if (selectedValue == 'Weigher System') {
                                                $('#weigher_system_option').show();
                                            } else if (selectedValue == 'Volumetric Cup Filler') {
                                                $('#volumetric_cap_option').show();
                                                $('#liner_weigher_option').hide();
                                            }
                                        });

                                        // Handle weigher_system_option_select change
                                        $('#weigher_system_option_select').change(function() {
                                            var selectedValue = $(this).val();

                                            // Hide all related options by default
                                            $('#liner_weigher_option').hide();
                                            $('#mult_head_weigher_option').hide();

                                            // Show the relevant option based on the weigher system selection
                                            if (selectedValue == 'Liner Weigher') {
                                                $('#liner_weigher_option').show();
                                            } else if (selectedValue == 'Multi Head Weigher') {
                                                $('#mult_head_weigher_option').show();
                                            }
                                        });
                                    });
                                </script>



                                <tr>
                                    <td class="text-center" width="3%">3</td>
                                    <td width="17%">Type Of Filling Unit</td>
                                    <td width="50%" class="text-center">

                                        <div id="non_free_flow" style="display: none;">
                                            <select name="non_free_flow_option" id="" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Auger Filler System" <?php if ($non_free_flow_option == 'Auger Filler System') { ?> selected <?php } ?>>Auger Filler System</option>
                                            </select>
                                        </div>





                                        <div id="cup_filler" style="display:none;">
                                            <select name="cup_filler_option" id="" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Multi Head Weight" <?php if ($cup_filler_option == 'Multi Head Weight') { ?> selected <?php } ?>>Multi Head Weight </option>
                                                <option value="Belt Weigher" <?php if ($cup_filler_option == 'Belt Weigher') { ?> selected <?php } ?>>Belt Weigher</option>
                                            </select>
                                        </div>




                                        <!---------IF Select Free Flow--------->

                                        <div id="free_flow_option" style="display:none;">

                                            <select name="free_flow_option" id="free_flow_option_select" class="form-control" onchange="weigher_fun()">
                                                <option value="">--Select--</option>
                                                <option value="Weigher System" <?php if ($free_flow_option == 'Weigher System') { ?> selected <?php } ?>>Weigher System</option>
                                                <option value="Volumetric Cup Filler" <?php if ($free_flow_option == 'Volumetric Cup Filler') { ?> selected <?php } ?>>Volumetric Cup Filler</option>
                                            </select>
                                        </div>



                                        <!---------IF Weigher System--------->

                                        <div id="weigher_system_option" style="display:none;">
                                            <br>
                                            <select name="weigher_system_option" id="weigher_system_option_select" class="form-control" onchange="weigher_system()">
                                                <option value="">--Select--</option>
                                                <option value="Liner Weigher" <?php if ($weigher_system_option == 'Liner Weigher') { ?> selected <?php } ?>>Liner Weigher</option>
                                                <option value="Belt Weigher" <?php if ($weigher_system_option == 'Belt Weigher') { ?> selected <?php } ?>>Belt Weigher</option>
                                                <option value="Multi Head Weigher" <?php if ($weigher_system_option == 'Multi Head Weigher') { ?> selected <?php } ?>>Multi Head Weigher</option>
                                            </select>
                                        </div>



                                        <!---------IF Weigher System--------->

                                        <!---------IF Liner Weigher--------->

                                        <div id="liner_weigher_option" style="display:none;">
                                            <br>
                                            <select name="liner_weigher_option" id="liner_weigher_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="2 Head" <?php if ($liner_weigher_option == '2 Head') { ?> selected <?php } ?>>2 Head</option>
                                                <option value="4 Head" <?php if ($liner_weigher_option == '4 Head') { ?> selected <?php } ?>>4 Head</option>

                                            </select>
                                        </div>


                                        <!---------IF Liner Weigher--------->

                                        <!---------IF Multi Head Weigher--------->

                                        <div id="mult_head_weigher_option" style="display:none;">
                                            <br>
                                            <select name="mult_head_weigher_option" id="mult_head_weigher_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="10 Head" <?php if ($mult_head_weigher_option == '10 Head') { ?> selected <?php } ?>>10 Head</option>
                                                <option value="14 Head" <?php if ($mult_head_weigher_option == '14 Head') { ?> selected <?php } ?>>14 Head</option>
                                                <option value="20 Head" <?php if ($mult_head_weigher_option == '20 Head') { ?> selected <?php } ?>>20 Head</option>
                                            </select>
                                        </div>



                                        <!---------IF Multi Head Weigher--------->

                                        <!---------IF Volumetric Cap Filler--------->

                                        <div id="volumetric_cap_option" style="display:none;">
                                            <br>
                                            <select name="volumetric_cap_option" id="volumetric_cap_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Tilting cup filler" <?php if ($volumetric_cap_option == 'Tilting cup filler') { ?> selected <?php } ?>>Tilting cup filler </option>
                                                <option value="Slide Cup Filler" <?php if ($volumetric_cap_option == 'Slide Cup Filler') { ?> selected <?php } ?>>Slide Cup Filler</option>
                                                <option value="Rotary Disc Cup Filler" <?php if ($volumetric_cap_option == 'Rotary Disc Cup Filler') { ?> selected <?php } ?>>Rotary Disc Cup Filler</option>
                                            </select>

                                        </div>


                                        <!---------Non-Viscous--------->
                                        <div id="non_viscous_option" style="display: none;">
                                            <select name="non_viscous_option" id="non_viscous_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Soenoid Type - Electrical" <?php if ($non_viscous_option == 'Soenoid Type - Electrical') { ?> selected <?php } ?>>Soenoid Type - Electrical</option>
                                                <option value="Pnuematic" <?php if ($non_viscous_option == 'Pnuematic') { ?> selected <?php } ?>>Pnuematic</option>
                                            </select>
                                        </div>


                                        <!---------Non-Viscous--------->

                                        <!---------Viscous--------->
                                        <div id="viscous_option" style="display: none;">
                                            <select name="viscous_option" id="viscous_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Piston Filler" <?php if ($viscous_option == 'Piston Filler') { ?> selected <?php } ?>>Piston Filler</option>
                                                <option value="Flow meter" <?php if ($viscous_option == 'Flow meter') { ?> selected <?php } ?>>Flow meter </option>
                                            </select>
                                        </div>


                                        <!---------Viscous--------->

                                        <div id="piston_filler_option" style="display: none;">
                                            <br>
                                            <select name="piston_filler_option" id="piston_filler_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Indivisual Driven" <?php if ($piston_filler_option == 'Indivisual Driven') { ?> selected <?php } ?>>Indivisual Driven</option>
                                                <option value="Overall Driven" <?php if ($piston_filler_option == 'Overall Driven') { ?> selected <?php } ?>>Overall Driven </option>
                                            </select>
                                        </div>



                                        <div id="follow_meter_option" style="display: none;">
                                            <br>
                                            <select name="follow_meter_option" id="piston_filler_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Mass flow meter" <?php if ($follow_meter_option == 'Mass flow meter') { ?> selected <?php } ?>>Mass flow meter</option>
                                                <option value="Electromagnatic flow meter" <?php if ($follow_meter_option == 'Electromagnatic flow meter') { ?> selected <?php } ?>>Electromagnatic flow meter</option>
                                            </select>
                                        </div>



                                        <!---------IF Volumetric Cap Filler--------->

                                        <!-- <select name="type_filling_unit" id="type_filling_unit" class="form-control">
                                        <option value="">--Select--</option>
                                        <option value="1">Volumetric Cup Filler</option>
                                        <option value="2">Auger Filler</option>
                                        <option value="3">Solenoid Valve</option>
                                        <option value="4">Piston Filler</option>
                                        <option value="5">Flow Meter</option>
                                    </select> -->

                                    </td>
                                    <td><input type="text" class="form-control" name="filling_unit_remarks" value="<?php echo $filling_unit_remarks; ?>"></td>
                                </tr>

                                <script>
                                    $(document).ready(function() {

                                        $('#type_filling_unit').change(function() {
                                            var selectedValue = $(this).val();

                                            $('#cup_filler').hide();

                                            if (selectedValue == '1') { // Powder selected
                                                $('#cup_filler').show();
                                            }
                                        });
                                    });
                                </script>
                            </table>

                            <table class="table">

                                <tr>
                                    <td class="text-center" width="3%" style="vertical-align: middle;">4</td>
                                    <td width="17%" style="vertical-align: middle;">Product Specification<br>(Viscosity / Density)</td>


                                    <td width="50%" class="text-center">
                                        <div id="density" style="display: none;">
                                            Density - <input type="text" class="form-control" value="<?php echo $density; ?>" name="density">
                                        </div>
                                        <div id="viscosity" style="display: none;">
                                            Viscosity - <input type="text" class="form-control" value="<?php echo $viscosity; ?>" name="viscosity">
                                        </div>




                                    </td>

                                    <td width="25%" class="text-center" style="vertical-align: middle; position: relative;"><textarea class="form-control text--box" name="product_specification_remarks"><?php echo $product_specification_remarks; ?></textarea></td>
                                </tr>
                            </table>


                            <table class="table">

                                <tr>
                                    <td class="text-center" width="3%" style="vertical-align: middle;">5</td>
                                    <td width="17%" style="vertical-align: middle;">Pouch Size (W x L) in mm</td>
                                    <td width="50%" class="text-center">
                                        <?php
                                        $sizepouch = $this->db->select('*')->from('basic_machine_df_form_machine_specification_size_qnty')->where('record_id', $id)->get();


                                        if ($sizepouch->num_rows() > 0) {

                                            foreach ($sizepouch->result() as $rowws) {

                                                $pouch_width = $rowws->pouch_width;
                                                $pouch_length = $rowws->pouch_length;
                                                $pouch_height = $rowws->pouch_height;


                                        ?>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                    <input type="hidden" name="multi_pouch_id[]" value="<?php echo $rowws->id; ?>">
                                                        <input type="text" class="form-control" name="pouch_width<?php echo $rowws->id; ?>" value="<?php echo $pouch_width; ?>">
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <input type="text" class="form-control" name="pouch_length<?php echo $rowws->id; ?>" value="<?php echo $pouch_length; ?>">
                                                    </div>
                                                    <div class="col-sm-3"><input type="text" class="form-control" name="pouch_height<?php echo $rowws->id; ?>" value="<?php echo $pouch_height; ?>">
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <button type="button" name="remove" class="delete_pouch_entry  btn btn-danger" data-id="<?php echo $rowws->id; ?>" id="delete_pouch_entry" style="padding: 0px !important; height: 21px !important; font-size: 13px !important; width: 21px;"><i class="fa fa-trash-o"></i></button>
                                                    </div>
                                                </div>
                                        <?php
                                            }
                                        }
                                        ?>

                                        <div class="form-group">
                                            <input type="checkbox" id="addmorepack" name="addmorepack" value="1">
                                            <label for="addmore">Add More</label>
                                        </div>

                                        <div class="rowshow" style="display: none;">
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <input type="text" class="form-control" name="pouch_width[]" value="W-">
                                                </div>
                                                <div class="col-sm-3">

                                                    <input type="text" class="form-control" name="pouch_length[]" value="L-">

                                                </div>
                                                <div class="col-sm-3"><input type="text" class="form-control" name="pouch_height[]" value="H-">
                                                </div>
                                                <div class="col-sm-3">
                                                    <button type="button" class="btn btn-warning" name="add" id="addmore_btn5" style="padding: 0px !important; height: 21px !important; font-size: 13px !important; width: 21px;"><b>+</b></button>
                                                </div>
                                            </div>
                                            <div id="multi_pouch">

                                            </div>
                                        </div>

                                    </td>

                                    <td width="25%" class="text-center" style="vertical-align: middle; position:relative;">



                                        <textarea name="pouch_size_remarks" class="form-control text--box" id=""><?php echo $pouch_size_remarks ?></textarea>


                                    </td>
                                </tr>
                                <script>
                                    $(document).ready(function() {

                                        $('#addmorepack').on('change', function() {


                                            $('.rowshow').hide();

                                            $('.rowshow :input').attr('required', false);

                                            if ($(this).is(':checked')) {
                                                $('.rowshow').show();
                                                $('.rowshow :input').attr('required', true);
                                            } else {
                                                $('.rowshow').hide();
                                                $('.rowshow :input').attr('required', false);
                                            }
                                        });
                                    });
                                </script>
                                <tr>
                                    <td class="text-center" width="3%" style="vertical-align: middle;">6</td>
                                    <td width="17%" style="vertical-align: middle;">Quantity to be packed</td>
                                    <td width="50%" class="text-center">
                                        <?php
                                        $sizepouch = $this->db->select('*')->from('basic_machine_df_form_machine_spec_qty')->where('record_id', $id)->get();


                                        if ($sizepouch->num_rows() > 0) {

                                            foreach ($sizepouch->result() as $rowws) {

                                                $quantity_packed = $rowws->quantity_packed;
                                                $quantity_packed_unit = $rowws->quantity_packed_unit;
                                        ?>
                                                <div class="row">

                                                    <div class="col-sm-4">
                                                    <input type="hidden" name="multi_qty_id[]" value="<?php echo $rowws->id; ?>">
                                                        <input type="text" class="form-control" name="quantity_packed<?php echo $rowws->id; ?>" value="<?php echo $quantity_packed; ?>">


                                                    </div>
                                                    <div class="col-sm-4">



                                                        <select class="form-control qtyunitdata" name="quantity_packed_unit<?php echo $rowws->id; ?>" id="editqty_unit" required onchange="selectProductPacked();">
                                                            <option value="">Select Qty</option>
                                                            <option value="ml" <?php if ($quantity_packed_unit == "ml") { ?> selected <?php } ?>>ml</option>
                                                            <option value="gm" <?php if ($quantity_packed_unit == "gm") { ?> selected <?php } ?>>gm</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <button type="button" name="remove" class="delete_qty_entry  btn btn-danger" data-id="<?php echo $rowws->id; ?>" id="delete_qty_entry" style="padding: 0px !important; height: 21px !important; font-size: 13px !important; width: 21px;"><i class="fa fa-trash-o"></i></button>
                                                    </div>
                                                </div>

                                        <?php
                                            }
                                        }
                                        ?>

<div class="form-group">
                                            <input type="checkbox" id="addmoreqty" name="addmoreqty" value="1">
                                            <label for="addqty">Add More</label>
                                        </div>

                                        <div class="rowshowqty" style="display: none;">
                                        <div class="row">
                                            <div class="col-sm-7">
                                            <!-- <input type="hidden" name="multi_qty_id[]" value=""> -->
                                                <input type="text" class="form-control" name="quantity_packed[]" value="">
                                            </div>
                                            <div class="col-sm-3">
                                                <select class="form-control qtyunitdata" name="quantity_packed_unit[]" id="editqty_unit"  onchange="selectProductPacked();">
                                                    <option value="">Select Qty</option>
                                                    <option value="ml">ml</option>
                                                    <option value="gm">gm</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-2">
                                                <button type="button" class="btn btn-warning" name="add" id="addmore_btn6" style="padding: 0px !important; height: 21px !important; font-size: 13px !important; width: 21px;"><b>+</b></button>
                                            </div>
                                        </div>
                                        <div id="multiqty">

                                        </div>
                                        </div>

                                    </td>


                                    <td width="25%" class="text-center" style="vertical-align: middle; position:relative;"><textarea name="quantity_packed_remarks" class="form-control text--box" id=""><?php echo $quantity_packed_remarks ?></textarea></td>
                                </tr>
                            </table>

                            <script>
                                $(document).ready(function() {

                                    $('#addmoreqty').on('change', function() {


                                          $('.rowshowqty').hide();

                                           $('.rowshowqty :input').attr('required',false);

                                        if ($(this).is(':checked')) {
                                            $('.rowshowqty').show();
                                             $('.rowshowqty :input').attr('required',true);
                                        } else {
                                            $('.rowshowqty').hide();
                                             $('.rowshowqty :input').attr('required',false);
                                        }
                                    });
                                });
                            </script>


                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">7</td>
                                    <td width="17%">Profile of Sealing</td>
                                    <td width="50%">
                                        <select name="profile_of_sealing" id="profile_of_sealing" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="V-Lining" <?php if ($profile_of_sealing == "V-Lining") { ?> selected <?php } ?>>V-Lining</option>
                                            <option value="Butt" <?php if ($profile_of_sealing == "Butt") { ?> selected <?php } ?>>Butt</option>
                                            <option value="Knurling" <?php if ($profile_of_sealing == "Knurling") { ?> selected <?php } ?>>Knurling</option>
                                            <option value="Plain" <?php if ($profile_of_sealing == "Plain") { ?> selected <?php } ?>>Plain</option>
                                        </select>



                                    </td>
                                    <td width="25%">
                                        <input type="text" name="profle_sealing_remarks" class="form-control" value="<?php echo $profle_sealing_remarks; ?>">
                                    </td>
                                </tr>
                            </table>
                            <table class="table">
                                <tr>
                                    <th class="text-center" colspan="3">Order Details For MULTI TRACK MACHINE</th>
                                    <th class="text-center" colspan="1" width="25%">REMARKS</th>
                                </tr>

                                <tr>
                                    <td class="text-center" width="3%">8</td>
                                    <td width="17%">Provision of Notching</td>
                                    <td width="50%" class="text-center">
                                        <select name="notching_option" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Yes" <?php if ($notching_option == 'Yes') { ?> selected <?php } ?>>Yes</option>
                                            <option value="No" <?php if ($notching_option == 'No') { ?> selected <?php } ?>>No</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="notching_option_remarks" value="<?php echo $notching_option_remarks; ?>"></td>
                                </tr>
                            </table>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">9</td>
                                    <td width="17%" rowspan="2">Hopper Details</td>
                                    <td width="50%" class="text-center">
                                        <select name="hooper_details" id="hooper_details" class="form-control" required onchange="hopper_detail()">
                                            <option value="">--Select--</option>
                                            <option value="Openable Type" <?php if ($hooper_details == 'Openable Type') { ?> selected <?php } ?>>Openable Type</option>
                                            <option value="Closed Type" <?php if ($hooper_details == 'Closed Type') { ?> selected <?php } ?>>Closed Type</option>
                                            <option value="NA" <?php if ($hooper_details == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>

                                        <!-----When i click on Openable type option this div display block---------->
                                        <div id="openable" style="display: none;">
                                            <br>
                                            <select name="openable_option" id="openable_option" class="form-control" onchange="open_()">
                                                <option value="">--Select--</option>
                                                <option value="Pressurised" <?php if ($openable_option == 'Pressurised') { ?> selected <?php } ?>>Pressurised</option>
                                                <option value="Non-Pressurised / Normal" <?php if ($openable_option == 'Non-Pressurised / Normal') { ?> selected <?php } ?>>Non-Pressurised / Normal</option>
                                                <option value="NA" <?php if ($openable_option == 'NA') { ?> selected <?php } ?>>NA</option>
                                            </select>
                                        </div>
                                        <!-----When i click on Openable type option this div display block---------->

                                        <div id="closed" style="display: none;">
                                            <br>
                                            <select name="closed_option" id="closed_option" class="form-control" onchange="close_()">
                                                <option value="">--Select--</option>
                                                <option value="Pressurised" <?php if ($closed_option == 'Pressurised') { ?> selected <?php } ?>>Pressurised</option>
                                                <option value="Non-Pressurised / Normal" <?php if ($closed_option == 'Non-Pressurised / Normal') { ?> selected <?php } ?>>Non-Pressurised / Normal</option>
                                                <option value="NA" <?php if ($closed_option == 'NA') { ?> selected <?php } ?>>NA</option>
                                            </select>
                                        </div>

                                        <!-----When i click on Pressurised option this div display block---------->
                                        <div id="pressurised" style="display: none;">
                                            <br>
                                            <select name="pressurised_option" id="pressurised_option" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Jacketed" <?php if ($pressurised_option == 'Jacketed') { ?> selected <?php } ?>>Jacketed</option>
                                                <option value="Non-Jacketed" <?php if ($pressurised_option == 'Non-Jacketed') { ?> selected <?php } ?>>Non-Jacketed</option>
                                                <option value="NA" <?php if ($pressurised_option == 'NA') { ?> selected <?php } ?>>NA</option>
                                            </select>
                                        </div>
                                        <!-----When i click on Pressurised option this div display block---------->

                                        <div id="closed_pressurised" style="display: none;">
                                            <br>
                                            <select name="closed_pressurised_option" id="closed_pressurised_option" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Jacketed" <?php if ($closed_pressurised_option == 'Jacketed') { ?> selected <?php } ?>>Jacketed</option>
                                                <option value="Non-Jacketed" <?php if ($closed_pressurised_option == 'Non-Jacketed') { ?> selected <?php } ?>>Non-Jacketed</option>
                                                <option value="NA" <?php if ($closed_pressurised_option == 'NA') { ?> selected <?php } ?>>NA</option>
                                            </select>
                                        </div>

                                        <!-----When i click on Non-Pressurised option this div display block---------->
                                        <div id="non_pressurised" style="display: none;">
                                            <br>
                                            <select name="non_pressurised_option" id="non_pressurised_option" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Jacketed" <?php if ($non_pressurised_option == 'Jacketed') { ?> selected <?php } ?>>Jacketed</option>
                                                <option value="Non-Jacketed" <?php if ($non_pressurised_option == 'Non-Jacketed') { ?> selected <?php } ?>>Non-Jacketed</option>
                                                <option value="NA" <?php if ($non_pressurised_option == 'NA') { ?> selected <?php } ?>>NA</option>
                                            </select>
                                        </div>
                                        <!-----When i click on Non-Pressurised option this div display block---------->

                                        <div id="non_closed_pressurised" style="display: none;">
                                            <br>
                                            <select name="non_closed_pressurised_option" id="non_closed_pressurised_option" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Jacketed" <?php if ($non_closed_pressurised_option == 'Jacketed') { ?> selected <?php } ?>>Jacketed</option>
                                                <option value="Non-Jacketed" <?php if ($non_closed_pressurised_option == 'Non-Jacketed') { ?> selected <?php } ?>>Non-Jacketed</option>
                                                <option value="NA" <?php if ($non_closed_pressurised_option == 'NA') { ?> selected <?php } ?>>NA</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="hooper_details_remarks" value="<?php echo $hooper_details_remarks; ?>"></td>
                                </tr>

                            </table>

                            <script>
                                function hopper_detail() {
                                    var selectedValue = $('#hooper_details').val();




                                    $('#openable').hide();
                                    $('#pressurised').hide();
                                    $('#non_pressurised').hide();
                                    $('#closed').hide();
                                    $('#closed_pressurised').hide();
                                    $('#non_closed_pressurised').hide();


                                    if (selectedValue == 'Openable Type') {
                                        $('#openable').show();
                                    }

                                    if (selectedValue == 'Closed Type') {
                                        $('#closed').show();
                                    }

                                }

                                function open_() {
                                    var openableOption = $('#openable_option').val();


                                    $('#non_pressurised').hide();


                                    if (openableOption == 'Pressurised') {
                                        $('#pressurised').show();
                                    } else if (openableOption == 'Non-Pressurised / Normal') {
                                        $('#non_pressurised').show();
                                        $('#pressurised').hide();
                                    }
                                }

                                function close_() {
                                    var closedOption = $('#closed_option').val();

                                    // $('#closed_pressurised_option').val('');
                                    // $('#non_closed_pressurised_option').val('');

                                    $('non_closed_pressuised').hide();


                                    if (closedOption == 'Pressurised') {
                                        $('#closed_pressurised').show();
                                    } else if (closedOption == 'Non-Pressurised / Normal') {
                                        $('#non_closed_pressurised').show();
                                        $('#closed_pressurised').hide();
                                    }
                                }



                                $(document).ready(function() {

                                    hopper_detail();


                                    open_();

                                    close_();

                                    $('#hooper_details').change(function() {
                                        var selectedValue = $(this).val();

                                        $('#openable_option').val('');

                                        $('#closed_option').val('');

                                        $('#openable').hide();
                                        $('#pressurised').hide();
                                        $('#non_pressurised').hide();
                                        $('#closed').hide();
                                        $('#closed_pressurised').hide();
                                        $('#non_closed_pressurised').hide();


                                        if (selectedValue == 'Openable Type') {
                                            $('#openable').show();
                                        }

                                        if (selectedValue == 'Closed Type') {
                                            $('#closed').show();
                                        }
                                    });


                                    $('#openable_option').change(function() {
                                        var openableOption = $(this).val();

                                        $('#pressurised_option').val('');

                                        $('#non_pressurised_option').val('');
                                        $('#non_pressurised').hide();


                                        if (openableOption == 'Pressurised') {
                                            $('#pressurised').show();
                                        } else if (openableOption == 'Non-Pressurised / Normal') {
                                            $('#non_pressurised').show();
                                            $('#pressurised').hide();
                                        }
                                    });

                                    $('#closed_option').change(function() {

                                        var closedOption = $(this).val();

                                        $('#closed_pressurised_option').val('');
                                        $('#non_closed_pressurised_option').val('');

                                        $('non_closed_pressuised').hide();


                                        if (closedOption == 'Pressurised') {
                                            $('#closed_pressurised').show();
                                        } else if (closedOption == 'Non-Pressurised / Normal') {
                                            $('#non_closed_pressurised').show();
                                            $('#closed_pressurised').hide();
                                        }

                                    });
                                });
                            </script>
                            <table class="table">

                                <tr>
                                    <td class="text-center" width="3%">10</td>
                                    <td width="17%">Cladding Provision</td>
                                    <td width="50%" class="text-center">
                                        <select name="cladding_provision" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Yes" <?php if ($cladding_provision == 'Yes') { ?> selected <?php } ?>>Yes</option>
                                            <option value="No" <?php if ($cladding_provision == 'No') { ?> selected <?php } ?>>No</option>
                                            <option value="NA" <?php if ($cladding_provision == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="cladding_provision_remarks" value="<?php echo $cladding_provision_remarks; ?>"></td>
                                </tr>
                            </table>
                            <table class="table">

                                <tr>
                                    <td class="text-center" width="3%" rowspan="2">11</td>
                                    <td width="17%" rowspan="2">Provision of Embossing coding</td>
                                    <td width="50%" class="text-center" rowspan="2">
                                        <select name="embossing" id="embossing" class="form-control" required onchange="cladding()">
                                            <option value="">--Select--</option>
                                            <option value="Yes" <?php if ($embossing == 'Yes') { ?> selected <?php } ?>>Yes</option>
                                            <option value="No" <?php if ($embossing == 'No') { ?> selected <?php } ?>>No</option>
                                            <option value="NA" <?php if ($embossing == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>

                                        <div id="embossing_option" style="display: none;">
                                            <br>
                                            <select name="embossing_option" id="embossing_option_select" class="form-control" onchange="embo_opt()">
                                                <option value="">--Select--</option>
                                                <option value="Linear Block Type" <?php if ($embossing_option == 'Linear Block Type') { ?> selected <?php } ?>>Linear Block Type</option>
                                                <option value="Rotary Type" <?php if ($embossing_option == 'Rotary Type') { ?> selected <?php } ?>>Rotary Type</option>
                                                <option value="NA" <?php if ($embossing_option == 'NA') { ?> selected <?php } ?>>NA</option>
                                            </select>
                                        </div>

                                        <div id="linear_option" style="display: none;">
                                            <br>
                                            <select name="linear_option" id="linear_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="T020" <?php if ($linear_option == 'T020') { ?> selected <?php } ?>>T020</option>
                                                <option value="T062" <?php if ($linear_option == 'T062') { ?> selected <?php } ?>>T062</option>
                                                <option value="NA" <?php if ($linear_option == 'NA') { ?> selected <?php } ?>>NA</option>
                                            </select>
                                        </div>

                                        <div id="rotary_option" style="display: none;">
                                            <br>
                                            <select name="rotary_option" id="rotary_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="RING TYPE" <?php if ($rotary_option == 'RING TYPE') { ?> selected <?php } ?>>RING TYPE</option>
                                                <option value="NA" <?php if ($rotary_option == 'NA') { ?> selected <?php } ?>>NA</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td width="25%" class="text-center" rowspan="2"><input type="text" class="form-control" name="provision_remarks" value="<?php echo $provision_remarks; ?>"></td>
                                </tr>

                                <script>
                                    function cladding() {

                                        var embossingValue = $('#embossing').val();




                                        $('#embossing_option').hide();
                                        $('#linear_option').hide();
                                        $('#rotary_option').hide();

                                        if (embossingValue == 'Yes') {

                                            $('#embossing_option').show();
                                        }

                                    }

                                    function embo_opt() {
                                        var embossingOptionValue = $('#embossing_option_select').val();

                                        $('#rotary_option_select').val('');

                                        $('#linear_option').hide();
                                        $('#rotary_option').hide();

                                        if (embossingOptionValue == 'Linear Block Type') {
                                            $('#linear_option').show();
                                        } else if (embossingOptionValue == 'Rotary Type') {
                                            $('#rotary_option').show();
                                        }
                                    }

                                    $(document).ready(function() {

                                        cladding();
                                        embo_opt();

                                        $('#embossing').change(function() {
                                            var embossingValue = $(this).val();


                                            $('#embossing_option_select').val('');

                                            $('#embossing_option').hide();
                                            $('#linear_option').hide();
                                            $('#rotary_option').hide();

                                            if (embossingValue == 'Yes') {

                                                $('#embossing_option').show();
                                            }
                                        });


                                        $('#embossing_option_select').change(function() {
                                            var embossingOptionValue = $(this).val();

                                            $('#rotary_option_select').val('');

                                            $('#linear_option').hide();
                                            $('#rotary_option').hide();

                                            if (embossingOptionValue == 'Linear Block Type') {
                                                $('#linear_option').show();
                                            } else if (embossingOptionValue == 'Rotary Type') {
                                                $('#rotary_option').show();
                                            }
                                        });
                                    });
                                </script>
                            </table>

                            <!--------------------------------------------------------------->
                            <?php
                            $rest = $this->db->select('id,name')->from('quote_parts_heading_master')->where('id', 13)->get();
                            if ($rest->num_rows() > 0) {
                                foreach ($rest->result() as $row) {
                            ?>
                                    <table class="table">
                                        <tr>
                                            <td class="text-center" width="3%">12</td>
                                            <td width="17%">


                                                <?php echo $row->name; ?>




                                            </td>
                                            <td width="50%" class="text-center">
                                                <select name="web_aligner" id="web_aligner" class="form-control" required onchange="webaligner()">
                                                    <option value="">--Select--</option>
                                                    <option value="Yes" <?php if ($web_aligner == 'Yes') { ?> selected <?php } ?>>Yes</option>
                                                    <option value="No" <?php if ($web_aligner == 'No') { ?> selected <?php } ?>>No</option>
                                                    <option value="NA" <?php if ($web_aligner == 'NA') { ?> selected <?php } ?>>NA</option>
                                                </select>

                                                <div id="web_aligner_option" style="display: none;">
                                                    <br>
                                                    <select name="web_aligner_option" id="web_aligner_option_select" class="form-control" style="display:block;">
                                                        <option value="">--Select--</option>
                                                        <?php
                                                        $rest1 = $this->db->select('id,name')->from('quote_parts_heading_master_options')->where('head_id', $row->id)->get();
                                                        if ($rest->num_rows() > 0) {
                                                            foreach ($rest1->result() as $row1) {
                                                                $resty = $this->db->select('value_id')->from('quotation_brand_data')->where('record_id', $record_id)->where('head_id', $row->id)->get();
                                                                if ($resty->num_rows() > 0) {
                                                                    foreach ($resty->result() as $rrrow);
                                                                    $valueid = $rrrow->value_id;
                                                                } else {
                                                                    $valueid = 0;
                                                                }
                                                        ?>
                                                                <option value="<?php echo $row1->id; ?>" <?php if ($row1->id == $web_aligner_option) { ?> selected <?php } ?>><?php echo $row1->name; ?></option>
                                                        <?php
                                                            }
                                                        }
                                                        ?>

                                                        <option value="NA" <?php if ($web_aligner_option == 'NA') { ?> selected <?php } ?>>NA</option>




                                                    </select>
                                                </div>
                                            </td>

                                            <td width="25%"><input type="text" class="form-control" name="web_aligner_remarks" value="<?php echo $web_aligner_remarks; ?>"></td>
                                        </tr>
                                    </table>
                            <?php }
                            } ?>

                            <script>
                                function webaligner() {

                                    var webValue = $('#web_aligner').val();




                                    $('#web_aligner_option').hide();



                                    if (webValue == 'Yes') {

                                        $('#web_aligner_option').show();
                                    }

                                }


                                $(document).ready(function() {

                                    webaligner();

                                    $('#web_aligner').change(function() {
                                        var webValue = $(this).val();




                                        $('#web_aligner_option').hide();

                                        $('#web_aligner_option_select').val('');

                                        if (webValue == 'Yes') {

                                            $('#web_aligner_option').show();
                                        }
                                    });

                                });
                            </script>


                            <!-------------------------------------------------------------->

                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">13</td>
                                    <td width="17%">Center Slitting</td>
                                    <td width="50%" class="text-center">
                                        <select name="center_slitting" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Rotary" <?php if ($center_slitting == 'Rotary') { ?> selected <?php } ?>>Rotary</option>
                                            <option value="Normal Blade" <?php if ($center_slitting == 'Normal Blade') { ?> selected <?php } ?>>Normal Blade</option>
                                            <option value="Surgical Blade" <?php if ($center_slitting == 'Surgical Blade') { ?> selected <?php } ?>>Surgical Blade</option>
                                            <option value="NA" <?php if ($center_slitting == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="center_slitting_remarks" value="<?php echo $center_slitting_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">14</td>
                                    <td width="17%">Vertical Slitter</td>
                                    <td width="50%" class="text-center">
                                        <select name="vertical_slitting" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Rotary" <?php if ($vertical_slitting == 'Rotary') { ?> selected <?php } ?>>Rotary</option>
                                            <option value="NA" <?php if ($vertical_slitting == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="vertical_slitting_remarks" value="<?php echo $vertical_slitting_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">15</td>
                                    <td width="17%">Vertical Sealer Drive</td>
                                    <td width="50%" class="text-center">
                                        <select name="vertical_sealer" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Servo Motor Driven" <?php if ($vertical_sealer == 'Servo Motor Driven') { ?> selected <?php } ?>>Servo Motor Driven</option>
                                            <option value="CAM Driven" <?php if ($vertical_sealer == 'CAM Driven') { ?> selected <?php } ?>>CAM Driven</option>
                                            <option value="Pneumatic" <?php if ($vertical_sealer == 'Pneumatic') { ?> selected <?php } ?>>Pneumatic</option>
                                            <option value="NA" <?php if ($vertical_sealer == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="vertical_sealer_remarks" value="<?php echo $vertical_sealer_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">16</td>
                                    <td width="17%">Laminate Pulling Drive</td>
                                    <td width="50%" class="text-center">
                                        <select name="laminate_pulling" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Servo Motor Driven" <?php if ($laminate_pulling == 'Servo Motor Driven') { ?> selected <?php } ?>>Servo Motor Driven</option>
                                            <option value="AC Geared Motor" <?php if ($laminate_pulling == 'AC Geared Motor') { ?> selected <?php } ?>>AC Geared Motor</option>
                                            <option value="Clutch Brake" <?php if ($laminate_pulling == 'Clutch Brake') { ?> selected <?php } ?>>Clutch Brake</option>
                                            <option value="NA" <?php if ($laminate_pulling == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="laminate_pulling_remarks" value="<?php echo $laminate_pulling_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">17</td>
                                    <td width="17%">Up & Down</td>
                                    <td width="50%" class="text-center">
                                        <select name="up_down" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Servo" <?php if ($up_down == 'Servo') { ?> selected <?php } ?>>Servo</option>
                                            <option value="NA" <?php if ($up_down == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="up_down_remarks" value="<?php echo $up_down_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">18</td>
                                    <td width="17%">Embossing Coding Drive</td>
                                    <td width="50%" class="text-center">
                                        <select name="embossing_coding" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Servo Motor Driven" <?php if ($embossing_coding == 'Servo Motor Driven') { ?> selected <?php } ?>>Servo Motor Driven</option>
                                            <option value="Not Applicable" <?php if ($embossing_coding == 'Not Applicable') { ?> selected <?php } ?>>Not Applicable</option>
                                            <option value="Pneumatic Driven" <?php if ($embossing_coding == 'Pneumatic Driven') { ?> selected <?php } ?>>Pneumatic Driven</option>
                                            <option value="NA" <?php if ($embossing_coding == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="embossing_coding_remarks" value="<?php echo $embossing_coding_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">19</td>
                                    <td width="17%">Cooling Station Drive</td>
                                    <td width="50%" class="text-center">
                                        <select name="cooling_station" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Servo Motor Driven" <?php if ($cooling_station == 'Servo Motor Driven') { ?> selected <?php } ?>>Servo Motor Driven</option>
                                            <option value="Cold Air" <?php if ($cooling_station == 'Cold Air') { ?> selected <?php } ?>>Cold Air</option>
                                            <option value="Chilled Water" <?php if ($cooling_station == 'Chilled Water') { ?> selected <?php } ?>>Chilled Water</option>
                                            <option value="Not Applicable" <?php if ($cooling_station == 'Not Applicable') { ?> selected <?php } ?>>Not Applicable</option>
                                            <option value="Pneumatic Driven" <?php if ($cooling_station == 'Pneumatic Driven') { ?> selected <?php } ?>>Pneumatic Driven</option>
                                            <option value="NA" <?php if ($cooling_station == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="cooling_station_remarks" value="<?php echo $cooling_station_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">20</td>
                                    <td width="17%">Horizontal Sealer Drive</td>
                                    <td width="50%" class="text-center">
                                        <select name="horizontal_sealer" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Servo Motor Driven" <?php if ($horizontal_sealer == 'Servo Motor Driven') { ?> selected <?php } ?>>Servo Motor Driven</option>
                                            <option value="CAM Driven" <?php if ($horizontal_sealer == 'CAM Driven') { ?> selected <?php } ?>>CAM Driven</option>
                                            <option value="Pneumatic" <?php if ($horizontal_sealer == 'Pneumatic') { ?> selected <?php } ?>>Pneumatic</option>
                                            <option value="NA" <?php if ($horizontal_sealer == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="horizontal_sealer_remarks" value="<?php echo $horizontal_sealer_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">21</td>
                                    <td width="17%">Perforation Blade Drive</td>
                                    <td width="50%" class="text-center">
                                        <select name="perforation_blade" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Servo Motor Driven" <?php if ($perforation_blade == 'Servo Motor Driven') { ?> selected <?php } ?>>Servo Motor Driven</option>
                                            <option value="Not Applicable" <?php if ($perforation_blade == 'Not Applicable') { ?> selected <?php } ?>>Not Applicable</option>
                                            <option value="Pneumatic Driven" <?php if ($perforation_blade == 'Pneumatic Driven') { ?> selected <?php } ?>>Pneumatic Driven</option>
                                            <option value="Numeric" <?php if ($perforation_blade == 'Numeric') { ?> selected <?php } ?>>Numeric</option>
                                            <option value="NA" <?php if ($perforation_blade == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="perforation_blade_remarks" value="<?php echo $perforation_blade_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">22</td>
                                    <td width="17%">Piston Drive</td>
                                    <td width="50%" class="text-center">
                                        <select name="priston_drive" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Servo Motor Driven" <?php if ($priston_drive == 'Servo Motor Driven') { ?> selected <?php } ?>>Servo Motor Driven</option>
                                            <option value="AC Geared Motor" <?php if ($priston_drive == 'AC Geared Motor') { ?> selected <?php } ?>>AC Geared Motor</option>
                                            <option value="Clutch Brake" <?php if ($priston_drive == 'Clutch Brake') { ?> selected <?php } ?>>Clutch Brake</option>
                                            <option value="NA" <?php if ($priston_drive == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="priston_drive_remarks" value="<?php echo $priston_drive_remarks; ?>"></td>
                                </tr>
                            </table>

                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">23</td>
                                    <td width="17%">Shut off Nozzle Drive</td>
                                    <td width="50%" class="text-center">
                                        <select name="shutt_off_nozzle" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Servo Motor Driven" <?php if ($shutt_off_nozzle == 'Servo Motor Driven') { ?> selected <?php } ?>>Servo Motor Driven</option>
                                            <option value="Not Applicable" <?php if ($shutt_off_nozzle == 'Not Applicable') { ?> selected <?php } ?>>Not Applicable</option>
                                            <option value="Pneumatic Driven" <?php if ($shutt_off_nozzle == 'Pneumatic Driven') { ?> selected <?php } ?>>Pneumatic Driven</option>
                                            <option value="Linear motor" <?php if ($shutt_off_nozzle == 'Linear motor') { ?> selected <?php } ?>>Linear motor</option>
                                            <option value="NA" <?php if ($shutt_off_nozzle == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="shutt_off_nozzle_remarks" value="<?php echo $shutt_off_nozzle_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">24</td>
                                    <td width="17%">Filling Drive</td>
                                    <td width="50%" class="text-center">
                                        <select name="filling_plate_drive" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Servo Motor Driven" <?php if ($filling_plate_drive == 'Servo Motor Driven') { ?> selected <?php } ?>>Servo Motor Driven</option>
                                            <option value="Not Applicable" <?php if ($filling_plate_drive == 'Not Applicable') { ?> selected <?php } ?>>Not Applicable</option>
                                            <option value="AC Geared Motor" <?php if ($filling_plate_drive == 'AC Geared Motor') { ?> selected <?php } ?>>AC Geared Motor</option>
                                            <option value="NA" <?php if ($filling_plate_drive == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="filling_plate_drive_remarks" value="<?php echo $filling_plate_drive_remarks; ?>"></td>
                                </tr>
                            </table>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">25</td>
                                    <td width="17%">Individual Weight Adjustment</td>
                                    <td width="50%" class="text-center">
                                        <select name="individual_weight" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Servo Motor Driven" <?php if ($individual_weight == 'Servo Motor Driven') { ?> selected <?php } ?>>Servo Motor Driven</option>
                                            <option value="Manual" <?php if ($individual_weight == 'Manual') { ?> selected <?php } ?>>Manual</option>
                                            <option value="HMI" <?php if ($individual_weight == 'HMI') { ?> selected <?php } ?>>HMI</option>
                                            <option value="NA" <?php if ($individual_weight == 'NA') { ?> selected <?php } ?>>NA</option>

                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="individual_weight_remarks" value="<?php echo $individual_weight_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">26</td>
                                    <td width="17%">Overall Weight Adjustment</td>
                                    <td width="50%" class="text-center">
                                        <select name="overall_weight_adjust" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Servo Motor Driven" <?php if ($overall_weight_adjust == 'Servo Motor Driven') { ?> selected <?php } ?>>Servo Motor Driven</option>
                                            <option value="HMI" <?php if ($overall_weight_adjust == 'HMI') { ?> selected <?php } ?>>HMI</option>
                                            <option value="Manual" <?php if ($overall_weight_adjust == 'Manual') { ?> selected <?php } ?>>Manual</option>
                                            <option value="NA" <?php if ($overall_weight_adjust == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="overall_weight_adjust_remarks" value="<?php echo $overall_weight_adjust_remarks; ?>"></td>
                                </tr>
                            </table>
                            <table class="table">

                                <tr>
                                    <td class="text-center" width="3%">27</td>
                                    <td width="17%">Traverse Drive</td>
                                    <td width="50%" class="text-center">
                                        <select name="traverse_drive" id="traverse_drive" class="form-control" required onchange="drive()">
                                            <option value="">--Select--</option>
                                            <option value="Yes" <?php if ($traverse_drive == 'Yes') { ?> selected <?php } ?>>Yes</option>
                                            <option value="No" <?php if ($traverse_drive == 'No') { ?> selected <?php } ?>>No</option>
                                        </select>

                                        <div id="yes_traverse_drive" style="display: none;">
                                            <br>
                                            <select name="yes_traverse_drive" id="yes_traverse_drive_option" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Servo Motor Driven" <?php if ($yes_traverse_drive == 'Servo Motor Driven') { ?> selected <?php } ?>>Servo Motor Driven</option>
                                                <option value="NA" <?php if ($yes_traverse_drive == 'NA') { ?> selected <?php } ?>>NA</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="traverse_drive_remarks" value="<?php echo $traverse_drive_remarks; ?>"></td>
                                </tr>

                                <script>
                                    function drive() {
                                        var selectedValue2 = $('#traverse_drive').val();


                                        $('#yes_traverse_drive').hide();



                                        if (selectedValue2 == 'Yes') {
                                            $('#yes_traverse_drive').show();
                                        }

                                    }
                                    $(document).ready(function() {

                                        drive();

                                        $('#traverse_drive').change(function() {
                                            var selectedValue2 = $(this).val();


                                            $('#yes_traverse_drive').hide();

                                            $('#yes_traverse_drive_option').val('');



                                            if (selectedValue2 == 'Yes') {
                                                $('#yes_traverse_drive').show();
                                            }


                                        });
                                    });
                                </script>
                            </table>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">28</td>
                                    <td width="17%">Printer</td>
                                    <td width="50%" class="text-center">

                                        <select name="printer_yes_no" id="printer_yes_no" class="form-control" required onchange="yes_printer()">
                                            <option value="">--Select--</option>
                                            <option value="Yes" <?php if ($printer_yes_no == 'Yes') { ?> selected <?php } ?>>Yes</option>
                                            <option value="No" <?php if ($printer_yes_no == 'No') { ?> selected <?php } ?>>No</option>
                                        </select>
                                        <div id="yes_printer" style="display: none;">
                                            <br>
                                            <select name="printer" id="printer" class="form-control" onchange="printer()">
                                                <option value="">--Select--</option>
                                                <option value="Inkjet" <?php if ($printer == 'Inkjet') { ?> selected <?php } ?>>Inkjet</option>
                                                <option value="TTO" <?php if ($printer == 'TTO') { ?> selected <?php } ?>>TTO</option>
                                                <option value="Thermal Inkjet Printer" <?php if ($printer == 'Thermal Inkjet Printer') { ?> selected <?php } ?>>Thermal Inkjet Printer</option>
                                                <option value="NA" <?php if ($printer == 'NA') { ?> selected <?php } ?>>NA</option>
                                            </select>
                                        </div>


                                        <div id="inkjet_option" style="display: none;">
                                            <br>
                                            <select name="inkjet_option" id="inkjet_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Markem Image" <?php if ($inkjet_option == 'Markem Image') { ?> selected <?php } ?>>Markem Image</option>
                                                <option value="Videojet" <?php if ($inkjet_option == 'Videojet') { ?> selected <?php } ?>>Videojet</option>
                                                <option value="Dominos" <?php if ($inkjet_option == 'Dominos') { ?> selected <?php } ?>>Dominos</option>
                                                <option value="Control Print" <?php if ($inkjet_option == 'Control Print') { ?> selected <?php } ?>>Control Print</option>
                                                <option value="NA" <?php if ($inkjet_option == 'NA') { ?> selected <?php } ?>>NA</option>
                                            </select>
                                        </div>

                                        <div id="tto_option" style="display: none;">
                                            <br>
                                            <select name="tto_option" id="tto_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Markem Image" <?php if ($tto_option == 'Markem Image') { ?> selected <?php } ?>>Markem Image</option>
                                                <option value="Videojet" <?php if ($tto_option == 'Videojet') { ?> selected <?php } ?>>Videojet</option>
                                                <option value="Dominos" <?php if ($tto_option == 'Dominos') { ?> selected <?php } ?>>Dominos</option>
                                                <option value="Control Print" <?php if ($tto_option == 'Control Print') { ?> selected <?php } ?>>Control Print</option>
                                                <option value="NA" <?php if ($tto_option == 'NA') { ?> selected <?php } ?>>NA</option>
                                            </select>
                                        </div>

                                        <div id="thermal_inkjet_option" style="display: none;">
                                            <br>
                                            <select name="thermal_inkjet_option" id="thermal_inkjet_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Norwix" <?php if ($thermal_inkjet_option == 'Norwix') { ?> selected <?php } ?>>Norwix</option>
                                                <option value="NA" <?php if ($thermal_inkjet_option == 'NA') { ?> selected <?php } ?>>NA</option>
                                            </select>
                                        </div>

                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="printer_remarks" value="<?php echo $printer_remarks; ?>"></td>
                                </tr>
                                <script>
                                    function yes_printer() {
                                        var printerYesNo = $('#printer_yes_no').val();

                                        if (printerYesNo === 'Yes') {
                                            $('#yes_printer').show(); // Show printer selection
                                        } else {
                                            $('#yes_printer').hide(); // Hide printer selection
                                            $('#inkjet_option, #tto_option, #thermal_inkjet_option').hide(); // Hide further options
                                        }
                                    }

                                    function printer() {

                                        var selectedValue1 = $('#printer').val();


                                        $('#inkjet_option').hide();

                                        $('#tto_option').hide();

                                        $('#thermal_inkjet_option').hide();



                                        if (selectedValue1 == 'Inkjet') {
                                            $('#inkjet_option').show();
                                        }

                                        if (selectedValue1 == 'TTO') {
                                            $('#tto_option').show();
                                        }

                                        if (selectedValue1 == 'Thermal Inkjet Printer') {
                                            $('#thermal_inkjet_option').show();
                                        }

                                    }

                                    $(document).ready(function() {

                                        printer();
                                        yes_printer()

                                        $('#printer_yes_no').on('change', function() {
                                            var printerYesNo = $(this).val();

                                            if (printerYesNo === 'Yes') {
                                                $('#yes_printer').show(); // Show printer selection
                                            } else {
                                                $('#yes_printer').hide(); // Hide printer selection
                                                $('#inkjet_option, #tto_option, #thermal_inkjet_option').hide(); // Hide further options
                                            }
                                        });

                                        $('#printer').change(function() {
                                            var selectedValue1 = $(this).val();


                                            $('#inkjet_option').hide();

                                            $('#tto_option').hide();

                                            $('#thermal_inkjet_option').hide();

                                            $('#thermal_inkjet_option_select').val('');
                                            $('#inkjet_option_select').val('');
                                            $('#tto_option_select').val('');


                                            if (selectedValue1 == 'Inkjet') {
                                                $('#inkjet_option').show();
                                            }

                                            if (selectedValue1 == 'TTO') {
                                                $('#tto_option').show();
                                            }

                                            if (selectedValue1 == 'Thermal Inkjet Printer') {
                                                $('#thermal_inkjet_option').show();
                                            }
                                        });
                                    });
                                </script>
                            </table>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">29</td>
                                    <td width="17%">Case Packer Drive</td>

                                    <td width="50%" class="text-center"><select name="case_packer_drive" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Servo Motor Drive" <?php if ($case_packer_drive == 'Servo Motor Drive') { ?> selected <?php } ?>>Servo Motor Drive</option>
                                            <option value="Pneumatic Driven" <?php if ($case_packer_drive == 'Pneumatic Driven') { ?> selected <?php } ?>>Pneumatic Driven</option>
                                            <option value="NA" <?php if ($case_packer_drive == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select></td>
                                    <td width="25%"><input type="text" class="form-control" name="case_packer_drive_remarks" value="<?php echo $case_packer_drive_remarks; ?>"></td>
                                </tr>
                            </table>
                            <table class="table">

                                <tr>
                                    <td class="text-center" width="3%">30</td>
                                    <td width="17%">Nozzle/Funnel</td>
                                    <td width="50%" class="text-center" colspan="2">
                                        <select name="nozzle_funnel" id="nozzle_funnel" class="form-control" required onchange="funnel()">
                                            <option value="">--Select--</option>
                                            <option value="Powder" <?php if ($nozzle_funnel == 'Powder') { ?> selected <?php } ?>>Powder</option>
                                            <option value="Liquid - Capillary" <?php if ($nozzle_funnel == 'Liquid - Capillary') { ?> selected <?php } ?>>Liquid - Capillary</option>
                                            <option value="Shut off nozzle" <?php if ($nozzle_funnel == 'Shut off nozzle') { ?> selected <?php } ?>>Shut off nozzle</option>
                                            <option value="Shut off + Blow off nozzle" <?php if ($nozzle_funnel == 'Shut off + Blow off nozzle') { ?> selected <?php } ?>>Shut off + Blow off nozzle</option>
                                            <option value="NA" <?php if ($nozzle_funnel == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>

                                        <div id="powder_option1" style="display: none;">
                                            <br>
                                            <select name="powder_option1" id="powder_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Oval" <?php if ($powder_option1 == 'Oval') { ?> selected <?php } ?>>Oval</option>
                                                <option value="Round" <?php if ($powder_option1 == 'Round') { ?> selected <?php } ?>>Round</option>
                                                <option value="NA" <?php if ($powder_option1 == 'NA') { ?> selected <?php } ?>>NA</option>
                                            </select>
                                        </div>

                                        <div id="liquid_option1" style="display: none;">
                                            <br>
                                            <select name="liquid_option1" id="liquid_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Round" <?php if ($liquid_option1 == 'Round') { ?> selected <?php } ?>>Round</option>
                                                <option value="Elliptical" <?php if ($liquid_option1 == 'Elliptical') { ?> selected <?php } ?>>Elliptical</option>
                                                <option value="NA" <?php if ($liquid_option1 == 'NA') { ?> selected <?php } ?>>NA</option>
                                            </select>
                                        </div>

                                        <div id="liquid_shut_option1" style="display: none;">
                                            <br>
                                            <select name="liquid_shut_option1" id="liquid_shut_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Rhombus" <?php if ($liquid_shut_option1 == 'Rhombus') { ?> selected <?php } ?>>Rhombus</option>
                                                <option value="Round" <?php if ($liquid_shut_option1 == 'Round') { ?> selected <?php } ?>>Round</option>
                                                <option value="NA" <?php if ($liquid_shut_option1 == 'NA') { ?> selected <?php } ?>>NA</option>
                                            </select>
                                        </div>

                                    </td>
                                    <td width="25%" rowspan="2"><input type="text" class="form-control" name="nozzle_funnel_remarks" value="<?php echo $nozzle_funnel_remarks; ?>"></td>
                                </tr>

                                <script>
                                    function funnel() {
                                        var selectedValue1 = $('#nozzle_funnel').val();


                                        $('#powder_option1').hide();

                                        $('#liquid_option1').hide();

                                        $('#liquid_shut_option1').hide();




                                        if (selectedValue1 == 'Powder') {
                                            $('#powder_option1').show();
                                        }

                                        if (selectedValue1 == 'Liquid - Capillary') {
                                            $('#liquid_option1').show();
                                        }

                                        if (selectedValue1 == 'Liquid - Shut off Nozzle') {
                                            $('#liquid_shut_option1').show();
                                        }
                                    }

                                    $(document).ready(function() {

                                        funnel();

                                        $('#nozzle_funnel').change(function() {
                                            var selectedValue1 = $(this).val();


                                            $('#powder_option1').hide();

                                            $('#liquid_option1').hide();

                                            $('#liquid_shut_option1').hide();

                                            $('#powder_option_select').val('');

                                            $('#liquid_option_select').val('');
                                            $('#liquid_shut_option_select').val('');


                                            if (selectedValue1 == 'Powder') {
                                                $('#powder_option1').show();
                                            }

                                            if (selectedValue1 == 'Liquid - Capillary') {
                                                $('#liquid_option1').show();
                                            }

                                            if (selectedValue1 == 'Liquid - Shut off Nozzle') {
                                                $('#liquid_shut_option1').show();
                                            }
                                        });
                                    });
                                </script>
                            </table>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">31</td>
                                    <td width="17%">Hose Pipe (type)</td>
                                    <td width="50" class="text-center">
                                        <select name="hose_pipe" id="hose_pipe" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Threaded" <?php if ($hose_pipe == 'Threaded') { ?> selected <?php } ?>>Threaded</option>
                                            <option value="Tri-Clamp" <?php if ($hose_pipe == 'Tri-Clamp') { ?> selected <?php } ?>>Tri-Clamp</option>
                                            <option value="NA" <?php if ($hose_pipe == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>
                                    </td>

                                    <td width="25%"><input type="text" class="form-control" name="hose_pipe_remarks" value="<?php echo $hose_pipe_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">32</td>
                                    <td width="17%">Batch Cut Format</td>
                                    <td width="50%" class="text-center">
                                        <select name="batch_cut_format" id="batch_cut_format" class="form-control" onchange="cut_format()">
                                            <option value="">--Select--</option>
                                            <option value="Straight" <?php if ($batch_cut_format == 'Straight') { ?> selected <?php } ?>>Straight Cut</option>
                                            <option value="String" <?php if ($batch_cut_format == 'String') { ?> selected <?php } ?>>String</option>
                                        </select>

                                        <div id="string_option" style="display: none;">
                                            <br>
                                            <select name="string_option" id="string_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Matt Format" <?php if ($string_option == 'Matt Format') { ?> selected <?php } ?>>Matt Format</option>
                                            </select>
                                        </div>






                                        <?php
                                        if ($batch_cut_format == 'String') {
                                        ?>

                                            <select name="string_option" id="string_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Matt Format" <?php if ($string_option == 'Matt Format') { ?> selected <?php } ?>>Matt Format</option>
                                                <option value="NA" <?php if ($string_option == 'NA') { ?> selected <?php } ?>>NA</option>
                                            </select>

                                        <?php } ?>

                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="batch_cut_format_remarks" value="<?php echo $batch_cut_format_remarks; ?>"></td>
                                </tr>

                                <script>
                                    function cut_format() {

                                        var selectedValue1 = $('#batch_cut_format').val();


                                        $('#string_option').hide();

                                        $('#string_option_select').val('');
                                        if (selectedValue1 == 'String') {
                                            $('#string_option').show();
                                        }


                                    }

                                    $(document).ready(function() {

                                        cut_format();

                                        $('#batch_cut_format').change(function() {
                                            var selectedValue1 = $(this).val();


                                            $('#string_option').hide();

                                            $('#string_option_select').val('');
                                            if (selectedValue1 == 'String') {
                                                $('#string_option').show();
                                            }
                                        });
                                    });
                                </script>
                            </table>

                            <table class="table">
                                <tr>
                                    <th class="text-center" colspan="3">Order Details For MULTI TRACK MACHINE</th>
                                    <th class="text-center" colspan="1" width="25%">REMARKS</th>
                                </tr>

                                <tr>
                                    <td class="text-center" width="3%">33</td>
                                    <td width="17%">Horizontal Sealer</td>
                                    <td width="50%" class="text-center">
                                        <input type="text" name="horizontal_sealer_width" value="<?php echo $horizontal_sealer_width; ?>" class="form-control">


                                        <!-- <select name="horizontal_sealer1" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <?php
                                            $qry = $this->db->select('id,machine_code,machine_image')->from('multi_track_machine_code_image')->where('status', 1)->where('sealer', 1)->get();

                                            if ($qry->num_rows() > 0) {

                                                foreach ($qry->result() as $mac) {

                                            ?>
                                                    <option value="<?php echo $mac->machine_code; ?>" <?php if ($mac->machine_code == $horizontal_sealer1) {
                                                                                                            echo 'selected';
                                                                                                        } ?>><?php echo $mac->machine_code; ?></option>
                                            <?php }
                                            } ?>
                                        </select>

                                        <b>Image:</b> -->


                                    </td>
                                    <td width="25%" style="position: relative;">

                                        <textarea name="horizontal_sealer1_remarks" id="" class="form-control text--box"><?php echo $horizontal_sealer1_remarks; ?></textarea>

                                    </td>
                                </tr>
                                <!-- <tr>
                                    <td class="text-center" width="3%" style="height: 20px;"></td>
                                    <td width="17%" style="height: 20px;"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%" style="height: 20px;"></td>
                                    <td width="17%" style="height: 20px;">Total Sealing width - <input type="text" name="horizontal_sealer_width" value="<?php echo $horizontal_sealer_width; ?>" class="form-control" readonly></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%" style="height: 20px;"></td>
                                    <td width="17%" style="height: 20px;"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%" style="height: 20px;"></td>
                                    <td width="17%" style="height: 20px;"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%" style="height: 20px;"></td>
                                    <td width="17%" style="height: 20px;"></td>
                                </tr> -->
                            </table>
                            <table class="table">
                                <tr>
                                    <th class="text-center" colspan="3">Order Details For MULTI TRACK MACHINE</th>
                                    <th class="text-center" colspan="1" width="25%">REMARKS</th>
                                </tr>

                                <tr>
                                    <td class="text-center" width="3%">34</td>
                                    <td width="17%">Vertical Sealer</td>
                                    <td width="50%" class="text-center">
                                        <input type="text" name="vertical_sealer_width" value="<?php echo $vertical_sealer_width; ?>" class="form-control">


                                        <!-- <select name="vertical_sealer1" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <?php
                                            $qry = $this->db->select('id,machine_code')->from('multi_track_machine_code_image')->where('status', 1)->where('sealer', 0)->get();

                                            if ($qry->num_rows() > 0) {

                                                foreach ($qry->result() as $mac) {

                                            ?>
                                                    <option value="<?php echo $mac->machine_code; ?>" <?php if ($mac->machine_code == $vertical_sealer1) {
                                                                                                            echo 'selected';
                                                                                                        } ?>><?php echo $mac->machine_code; ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                        <b>Image:</b> -->
                                    </td>
                                    <td width="25%" style="position: relative;">
                                        <textarea name="vertical_sealer1_remarks" id="" class="form-control text--box"><?php echo $vertical_sealer1_remarks; ?></textarea>
                                    </td>
                                </tr>
                                <!-- <tr>
                                    <td class="text-center" width="3%" style="height: 20px;"></td>
                                    <td width="17%" style="height: 20px;"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%" style="height: 20px;"></td>
                                    <td width="17%" style="height: 20px;">Total Sealing width - <input type="text" name="vertical_sealer_width	" value="<?php echo $vertical_sealer_width; ?>" class="form-control" readonly></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%" style="height: 20px;"></td>
                                    <td width="17%" style="height: 20px;"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%" style="height: 20px;"></td>
                                    <td width="17%" style="height: 20px;"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%" style="height: 20px;"></td>
                                    <td width="17%" style="height: 20px;"></td>
                                </tr> -->
                            </table>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">35</td>
                                    <td width="17%">Rotary Valve Coating</td>
                                    <td width="50%">
                                        <select name="rotary_valve_coating" id="rotary_valve_coating" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="HALAR" <?php if ($rotary_valve_coating == 'HALAR') { ?> selected <?php } ?>>HALAR</option>
                                            <option value="TEFLON" <?php if ($rotary_valve_coating == 'TEFLON') { ?> selected <?php } ?>>TEFLON</option>
                                            <option value="No Coating" <?php if ($rotary_valve_coating == 'No Coating') { ?> selected <?php } ?>>No Coating</option>
                                            <option value="NA" <?php if ($rotary_valve_coating == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="rotary_valve_coating_remarks" value="<?php echo $rotary_valve_coating_remarks; ?>"></td>
                                </tr>
                            </table>

                            <table class="table">

                                <tr>
                                    <td class="text-center" width="3%">36</td>
                                    <td width="17%">Working Speed</td>
                                    <td width="50%" class="text-center">
                                        <input type="text" name="working_speed" value="<?php echo $working_speed; ?>" class="form-control" required>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="working_speed_remarks" value="<?php echo $working_speed_remarks; ?>"></td>
                                </tr>
                            </table>
                            <table class="table">

                                <tr>
                                    <td class="text-center" width="3%">37</td>
                                    <td width="17%">Reel Shaft Type</td>
                                    <td width="50%" class="text-center">
                                        <select name="reel_shaft_type" id="reel_shaft_type" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Hollow" <?php if ($reel_shaft_type == 'Hollow') { ?> selected <?php } ?>>Hollow</option>
                                            <option value="Solid" <?php if ($reel_shaft_type == 'Solid') { ?> selected <?php } ?>>Solid</option>
                                            <option value="Pneumatic" <?php if ($reel_shaft_type == 'Pneumatic') { ?> selected <?php } ?>>Pneumatic</option>
                                            <option value="NA" <?php if ($reel_shaft_type == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="reel_shaft_type_remarks" value="<?php echo $reel_shaft_type_remarks; ?>"></td>
                                </tr>
                            </table>

                            <table class="table">

                                <tr>
                                    <td class="text-center" width="3%">38</td>
                                    <td width="17%">Reel Core Diameter</td>
                                    <td width="50%">
                                        <select name="reel_core_diameter" id="reel_core_diameter" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="76mm" <?php if ($reel_core_diameter == '76mm') { ?> selected <?php } ?>>76mm</option>
                                            <option value="152mm" <?php if ($reel_core_diameter == '152mm') { ?> selected <?php } ?>>152mm</option>
                                            <option value="NA" <?php if ($reel_core_diameter == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="reel_core_diameter_remarks" value="<?php echo $reel_core_diameter_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">39</td>
                                    <td width="17%">Trial Material / Product</td>
                                    <td width="50%">
                                        <select name="trial_material" id="trial_material" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Yes" <?php if ($trial_material == 'Yes') { ?> selected <?php } ?>>Yes</option>
                                            <option value="No" <?php if ($trial_material == 'No') { ?> selected <?php } ?>>No</option>
                                            <option value="NA" <?php if ($trial_material == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="trial_material_remarks" value="<?php echo $trial_material_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">40</td>
                                    <td width="17%">Laminate Details</td>
                                    <td width="50%">
                                        <select name="laminate_detail" id="laminate_detail" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Heat Sealable laminate" <?php if ($laminate_detail == 'Heat Sealable laminate') { ?> selected <?php } ?>>Heat Sealable laminate</option>
                                            <option value="NA" <?php if ($laminate_detail == 'NA') { ?> selected <?php } ?>>NA</option>
                                            <!-- <option value="Impulse Sealer">Impulse Sealer</option>-->
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="laminate_detail_remarks" value="<?php echo $laminate_detail_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">41</td>
                                    <td width="17%">Heater Control System</td>
                                    <td width="50%">
                                        <select name="heater_control_system" id="heater_control_system" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Grouping" <?php if ($heater_control_system == 'Grouping') { ?> selected <?php } ?>>Grouping</option>
                                            <option value="Individual" <?php if ($heater_control_system == 'Individual') { ?> selected <?php } ?>>Individual</option>
                                            <option value="NA" <?php if ($heater_control_system == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="heater_control_system_remarks" value="<?php echo $heater_control_system_remarks; ?>"></td>
                                </tr>
                            </table>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">42</td>
                                    <td width="17%">Beacon Lights</td>
                                    <td width="50%" class="text-center" colspan="1">
                                        <select name="beacon_light" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="3 Stage" <?php if ($beacon_light == '3 Stage') { ?> selected <?php } ?>>3 Stage</option>
                                            <option value="4 Stage" <?php if ($beacon_light == '4 Stage') { ?> selected <?php } ?>>4 Stage</option>
                                            <option value="5 Stage" <?php if ($beacon_light == '5 Stage') { ?> selected <?php } ?>>5 Stage</option>
                                            <option value="NA" <?php if ($beacon_light == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="beacon_light_remarks" value="<?php echo $beacon_light_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">43</td>
                                    <td width="17%">Hopper Level Sensor</td>
                                    <td width="50%" class="text-center">
                                        <select id="hooper_level" name="hooper_level" class="form-control" required onchange="hopper()">
                                            <option value="">--Select--</option>
                                            <option value="Yes" <?php if ($hooper_level == 'Yes') { ?> selected <?php } ?>>Yes</option>
                                            <option value="No" <?php if ($hooper_level == 'No') { ?> selected <?php } ?>>No</option>
                                            <option value="NA" <?php if ($hooper_level == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>

                                        <div id="hooper_level_option" style="display: none;">
                                            <br>
                                            <select name="hooper_level_option" id="hooper_level_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Shubham Standard" <?php if ($hooper_level_option == 'Shubham Standard') { ?> selected <?php } ?>>Shubham Standard</option>
                                                <option value="Sapcon" <?php if ($hooper_level_option == 'Sapcon') { ?> selected <?php } ?>>Sapcon</option>
                                                <option value="Vega" <?php if ($hooper_level_option == 'Vega') { ?> selected <?php } ?>>Vega</option>
                                                <option value="Sick" <?php if ($hooper_level_option == 'Sick') { ?> selected <?php } ?>>Sick</option>
                                                <option value="E+H" <?php if ($hooper_level_option == 'E+H') { ?> selected <?php } ?>>E+H</option>
                                                <option value="NA" <?php if ($hooper_level_option == 'NA') { ?> selected <?php } ?>>NA</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="hooper_level_remarks" value="<?php echo $beacon_light_remarks; ?>"></td>
                                </tr>

                                <script>
                                    function hooper() {
                                        var selectedValue = $('#hooper_level').val();


                                        $('#hooper_level_option').hide();

                                        if (selectedValue == 'Yes') {
                                            $('#hooper_level_option').show();
                                        }
                                    }
                                    $(document).ready(function() {

                                        hooper();

                                        $('#hooper_level').change(function() {
                                            var selectedValue = $(this).val();


                                            $('#hooper_level_option').hide();

                                            $('#hooper_level_option_select').val('');

                                            if (selectedValue == 'Yes') {
                                                $('#hooper_level_option').show();
                                            }
                                        });
                                    });
                                </script>
                            </table>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">44</td>
                                    <td width="17%">Safety Relay + Safety Switch</td>
                                    <td width="50%" class="text-center"><select name="safety_relay" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Yes" <?php if ($safety_relay == 'Yes') { ?> selected <?php } ?>>Yes</option>

                                            <option value="No" <?php if ($safety_relay == 'No') { ?> selected <?php } ?>>No</option>
                                        </select></td>
                                    <td width="25%"><input type="text" class="form-control" name="safety_relay_remarks" value="<?php echo $safety_relay_remarks; ?>"></td>
                                </tr>
                            </table>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">45</td>
                                    <td width="17%">Supply Voltage</td>


                                    <td width="50%" class="text-center">

                                        <input type="text" name="supply_voltage" value="<?php echo $supply_voltage; ?>" class="form-control">



                                    </td>

                                    <td width="25%"><input type="text" class="form-control" name="supply_voltage_remarks" value="<?php echo $supply_voltage_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">46</td>
                                    <td width="17%">PLC Make</td>


                                    <td width="50%" class="text-center"><select name="plc_maker" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Allen Bradely" <?php if ($plc_maker == 'Allen Bradely') { ?> selected <?php } ?>>Allen Bradely</option>
                                            <option value="Omron" <?php if ($plc_maker == 'Omron') { ?> selected <?php } ?>>Omron</option>
                                            <option value="Mitsubishi" <?php if ($plc_maker == 'Mitsubishi') { ?> selected <?php } ?>>Mitsubhishi</option>
                                            <option value="NA" <?php if ($plc_maker == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select></td>

                                    <td width="25%"><input type="text" class="form-control" name="plc_maker_remarks" value="<?php echo $plc_maker_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">47</td>
                                    <td width="17%">HMI Size</td>


                                    <td width="50%" class="text-center"><select name="hmi_size" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="7 inch" <?php if ($hmi_size == '7 inch') { ?> selected <?php } ?>>7 inch</option>
                                            <option value="10 inch" <?php if ($hmi_size == '10 inch') { ?> selected <?php } ?>>10 inch</option>
                                            <option value="12 inch" <?php if ($hmi_size == '12 inch') { ?> selected <?php } ?>>12 inch</option>
                                            <option value="15 inch" <?php if ($hmi_size == '15 inch') { ?> selected <?php } ?>>15 inch</option>
                                            <option value="NA" <?php if ($hmi_size == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select></td>

                                    <td width="25%"><input type="text" class="form-control" name="hmi_size_remarks" value="<?php echo $hmi_size_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">48</td>
                                    <td width="17%">CIP System</td>

                                    <td class="text-center" width="50%">
                                        <select name="cip_system" id="cip_system" class="form-control" required onchange="cip()">
                                            <option value="">--Select--</option>
                                            <option value="Yes" <?php if ($cip_system == 'Yes') { ?> selected <?php } ?>>Yes</option>
                                            <option value="No" <?php if ($cip_system == 'No') { ?> selected <?php } ?>>No</option>
                                            <option value="NA" <?php if ($cip_system == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>

                                        <div id="cip_system_option" style="display: none;">
                                            <br>
                                            <select name="cip_system_option" id="cip_system_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Alfa Laval" <?php if ($cip_system_option == 'Alfa Laval') { ?> selected <?php } ?>>Alfa Laval</option>
                                                <option value="Shubham Standard" <?php if ($cip_system_option == 'Shubham Standard') { ?> selected <?php } ?>>Shubham Standard</option>
                                                <option value="NA" <?php if ($cip_system_option == 'NA') { ?> selected <?php } ?>>NA</option>
                                            </select>
                                        </div>
                                    </td>

                                    <td width="25%" rowspan="2"><input type="text" class="form-control" name="cip_system_option_remarks" value="<?php echo $cip_system_option_remarks; ?>"></td>
                                </tr>

                                <script>
                                    function cip() {
                                        var selectedValue = $('#cip_system').val();


                                        $('#cip_system_option').hide();

                                        if (selectedValue == 'Yes') {
                                            $('#cip_system_option').show();
                                        }
                                    }

                                    $(document).ready(function() {

                                        cip();

                                        $('#cip_system').change(function() {
                                            var selectedValue = $(this).val();


                                            $('#cip_system_option').hide();

                                            $('#cip_system_option_select').val('');

                                            if (selectedValue == 'Yes') {
                                                $('#cip_system_option').show();
                                            }
                                        });
                                    });
                                </script>
                            </table>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">49</td>
                                    <td width="17%">Tool Kit</td>
                                    <td width="50%" class="text-center">
                                        <select name="tool_kit" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Yes" <?php if ($tool_kit == 'Yes') { ?> selected <?php } ?>>Yes</option>

                                            <option value="No" <?php if ($tool_kit == 'No') { ?> selected <?php } ?>>No</option>
                                            <option value="NA" <?php if ($tool_kit == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="tool_kit_remarks" value="<?php echo $tool_kit_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">50</td>
                                    <td width="17%">Changeover Parts</td>
                                    <td width="50%" class="text-center">
                                        <select name="changeover_part" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Yes" <?php if ($changeover_part == 'Yes') { ?> selected <?php } ?>>Yes</option>

                                            <option value="No" <?php if ($changeover_part == 'No') { ?> selected <?php } ?>>No</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="changeover_part_remarks" value="<?php echo $changeover_part_remarks; ?>"></td>
                                </tr>
                            </table>
                            <table class="table">
                                <tr>
                                    <th class="text-center" colspan="3">Order Details For MULTI TRACK MACHINE</th>
                                    <th class="text-center" colspan="1" width="25%">REMARKS</th>
                                </tr>

                                <tr>
                                    <td class="text-center" width="3%">51</td>
                                    <td width="17%">Secondary Packaging Solution</td>
                                    <td width="50%">
                                        <select name="secondary_pack" id="secondary_pack" class="form-control" required onchange="second()">
                                            <option value="">--Select--</option>
                                            <option value="Yes" <?php if ($secondary_pack == 'Yes') { ?> selected <?php } ?>>Yes</option>
                                            <option value="No" <?php if ($secondary_pack == 'No') { ?> selected <?php } ?>>No</option>
                                        </select>



                                        <div id="yes_secondary_pack" style="display: none;">
                                            <?php


                                            $packed = $this->db->select('*')->from('basci_machine_df_form_multi_track_machine_pack')->where('record_id', $id)->get();

                                            $i = 1;
                                            if ($packed->num_rows() > 0) {
                                                foreach ($packed->result() as $row) {
                                            ?>
                                                    <div class="row" style="margin-top:10px">
                                                        <div class="col-sm-8">
                                                            <input type="hidden" name="multi_track_machine_id[]" value="<?php echo $row->id; ?>">
                                                            <select name="yes_secondary_pack<?php echo $row->id; ?>" id="yes_secondary_pack_option" class="form-control yes_secondary_pack_option" style="width: 100%;" onchange="checkcasepacker()">
                                                                <option value="">--Select--</option>
                                                                <option value="Collating Conveyor" <?php if ($row->yes_secondary_pack == 'Collating Conveyor') { ?> selected <?php } ?>>Collating Conveyor</option>
                                                                <option value="Auto Collating Conveyor" <?php if ($row->yes_secondary_pack == 'Auto Collating Conveyor') { ?> selected <?php } ?>>Auto Collating Conveyor</option>
                                                                <option value="Transfer Conveyor" <?php if ($row->yes_secondary_pack == 'Transfer Conveyor') { ?> selected <?php } ?>>Tranfer Conveyor</option>
                                                                <option value="Taper Conveyor" <?php if ($row->yes_secondary_pack == 'Taper Conveyor') { ?> selected <?php } ?>>Taper Conveyor</option>
                                                                <option value="Case Erector" <?php if ($row->yes_secondary_pack == 'Case Erector') { ?> selected <?php } ?>>Case Erector</option>
                                                                <option value="Auto Case Erector" <?php if ($row->yes_secondary_pack == 'Auto Case Erector') { ?> selected <?php } ?>>Auto Case Erector</option>
                                                                <option value="Case Packer" <?php if ($row->yes_secondary_pack == 'Case Packer') { ?> selected <?php } ?>>Case Packer</option>
                                                                <option value="Auto Case Packer" <?php if ($row->yes_secondary_pack == 'Auto Case Packer') { ?> selected <?php } ?>>Auto Case Packer</option>
                                                                <option value="Flow Wrap Machine" <?php if ($row->yes_secondary_pack == 'Flow Wrap Machine') { ?> selected <?php } ?>>Flow Wrap Machine</option>
                                                                <option value="Case Taping Machine" <?php if ($row->yes_secondary_pack == 'Case Taping Machine') { ?> selected <?php } ?>>Case Taping Machine</option>
                                                                <option value="Check Weigher with Rejection System" <?php if ($row->yes_secondary_pack == 'Check Weigher with Rejection System') { ?> selected <?php } ?>>Check Weigher with Rejection System</option>
                                                                <option value="Auto Sack Packer" <?php if ($row->yes_secondary_pack == 'Auto Sack Packer') { ?> selected <?php } ?>>Auto Sack Packer</option>
                                                                <option value="Auto L-Sealer Machine" <?php if ($row->yes_secondary_pack == 'Auto L-Sealer Machine') { ?> selected <?php } ?>>Auto L-Sealer Machine</option>
                                                            </select>

                                                        </div>
                                                        <div class="col-sm-3">
                                                            <input type="text" name="yes_secondary_pack_qty<?php echo $row->id; ?>" value="<?php echo $row->yes_secondary_pack_qty; ?>" placeholder="add quantity" class="form-control">
                                                        </div>
                                                        <div class="col-sm-1">
                                                            <button type="button" name="remove" class="btn_remove  btn btn-danger" data-id="<?php echo $row->id; ?>" id="delete_pack" style="padding: 0px !important; height: 21px !important; font-size: 13px !important; width: 21px;"><i class="fa fa-trash-o"></i></button>
                                                        </div>
                                                    </div>

                                            <?php $i++;
                                                }
                                            } ?>


                                            <div class="form-group">
                                                <input type="checkbox" id="add_more_pack" name="add_more_pack" value="1">
                                                <label for="add_more1">Add More</label>
                                            </div>

                                            <div class="row_show" style="display: none;">
                                                <div class="row">
                                                    <div class="col-sm-8">
                                                        <select name="yes_secondary_pack_add[]" id="yes_secondary_pack_option_add" class="form-control yes_secondary_pack_option" style="width: 100%;" onchange="checkcasepacker()">
                                                            <option value="">--Select--</option>
                                                            <option value="Collating Conveyor">Collating Conveyor</option>
                                                            <option value="Auto Collating Conveyor">Auto Collating Conveyor</option>
                                                            <option value="Tranfer Conveyor">Tranfer Conveyor</option>
                                                            <option value="Taper Conveyor">Taper Conveyor</option>
                                                            <option value="Case Erector">Case Erector</option>
                                                            <option value="Auto Case Erector">Auto Case Erector</option>
                                                            <option value="Case Packer">Case Packer</option>
                                                            <option value="Auto Case Packer">Auto Case Packer</option>
                                                            <option value="Flow Wrap Machine">Flow Wrap Machine</option>
                                                            <option value="Case Taping Machine">Case Taping Machine</option>
                                                            <option value="Check Weigher with Rejection System">Check Weigher with Rejection System</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <input type="text" name="yes_secondary_pack_qty_add[]" placeholder="add quantity" class="form-control">
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button type="button" class="btn btn-warning" name="add" id="addmore_btn3" style="padding: 0px !important; height: 21px !important; font-size: 13px !important; width: 21px;"><b>+</b></button>
                                                    </div>
                                                </div>

                                                <div id="secondary_packData"></div>
                                            </div>
                                        </div>

                    </div>




                    <div id="yes_secondary_pack" style="display: none;">
                        <br>



                        <br>
                        <!-- <select name="yes_secondary_pack[]" id="yes_secondary_pack_option" class="select2" multiple style="width: 100%;" onchange="yes_second()">
                                                <option value="">--Select--</option>
                                                

                                                 <option value="Collating Conveyor" <?php if ($yes_secondary_pack == 'Collating Conveyor') { ?> selected <?php } ?>>Collating Conveyor</option>
                                                <option value="Auto Collating Conveyor" <?php if ($yes_secondary_pack == 'Auto Collating Conveyor') { ?> selected <?php } ?>>Auto Collating Conveyor</option>
                                                <option value="Tranfer Conveyor" <?php if ($yes_secondary_pack == 'Tranfer Conveyor') { ?> selected <?php } ?>>Tranfer Conveyor</option>
                                                <option value="Taper Conveyor" <?php if ($yes_secondary_pack == 'Taper Conveyor') { ?> selected <?php } ?>>Taper Conveyor</option>
                                               <option value="Case Erector" <?php if ($yes_secondary_pack == 'Case Erector') { ?> selected <?php } ?>>Case Erector</option>
                                                <option value="Auto Case Erector" <?php if ($yes_secondary_pack == 'Auto Case Erector') { ?> selected <?php } ?>>Auto Case Erector</option>
                                               <option value="Case Packer" <?php if ($yes_secondary_pack == 'Case Packer') { ?> selected <?php } ?>>Case Packer</option>
                                                <option value="Auto Case Packer" <?php if ($yes_secondary_pack == 'Auto Case Packer') { ?> selected <?php } ?>>Auto Case Packer</option>
                                                <option value="Flow Wrap Machine" <?php if ($yes_secondary_pack == 'Flow Wrap Machine') { ?> selected <?php } ?>>Flow Wrap Machine</option>
                                                 <option value="Case Taping Machine" <?php if ($yes_secondary_pack == 'Case Taping Machine') { ?> selected <?php } ?>>Case Taping Machine</option>
                                              <option value="Check Weigher with Rejection System" <?php if ($yes_secondary_pack == 'Check Weigher with Rejection System') { ?> selected <?php } ?>>Check Weigher with Rejection System</option>
                                            </select> -->
                    </div>

                    <div id="case_packer" style="display:none;">
                        <br>
                        <select name="case_packer" id="case_packer_option" class="form-control">
                            <option value="">--Select--</option>
                            <option value="Slide Type" <?php if ($case_packer == 'Slide Type') { ?> selected <?php } ?>>Slide Type</option>
                            <option value="Butterfly Type" <?php if ($case_packer == 'Butterfly Type') { ?> selected <?php } ?>>Butterfly Type</option>
                        </select>
                    </div>
                    </td>
                    <td width="25%" style="position: relative;"><textarea name="secondary_pack_remarks" id="" class="form-control text--box"><?php echo $secondary_pack_remarks; ?></textarea></td>
                    </tr>

                    <script>
                        $(document).ready(function() {

                            $('#add_more_pack').on('change', function() {


                                $('.row_show').hide();

                                $('.row_show :input').attr('required', false);

                                if ($(this).is(':checked')) {
                                    $('.row_show').show();
                                    $('.row_show :input').attr('required', true);
                                } else {
                                    $('.row_show').hide();
                                    $('.row_show :input').attr('required', false);
                                }
                            });
                        });
                    </script>

                    <script>
                        function second() {

                            var secondaryPackValue = $('#secondary_pack').val();

                            if (secondaryPackValue == "Yes") {
                                $('#yes_secondary_pack').show();

                            } else {
                                $('#yes_secondary_pack').hide();
                                $('#case_packer').hide();
                            }

                        }


                        function yes_second() {
                            var secondaryPackValue1 = $('#yes_secondary_pack_option').val();


                            if (secondaryPackValue1 && secondaryPackValue1.includes("Case Packer")) {
                                $('#case_packer').show();
                            } else {
                                $('#case_packer').hide();
                            }


                            var casePackerExists = false;
                            $('#pack-table tr').each(function() {
                                var rowText = $(this).find('td:nth-child(2)').text(); // Get text of the second column (Name)
                                if (rowText.includes("Case Packer")) {
                                    casePackerExists = true;
                                }
                            });

                            // Show or hide the case_packer div based on the presence of "Case Packer"
                            if (casePackerExists) {
                                $('#case_packer').show();
                            } else {
                                $('#case_packer').hide();
                            }


                        }

                        $(document).ready(function() {

                            second();

                            yes_second();

                            checkcasepacker();

                            $('#secondary_pack').change(function() {
                                var secondaryPackValue = $(this).val();


                                $('#yes_secondary_pack_option').val('');

                                $('#case_packer_option').val('');

                                if (secondaryPackValue == "Yes") {
                                    $('#yes_secondary_pack').show();

                                } else {
                                    $('#yes_secondary_pack').hide();
                                    $('#case_packer').hide();
                                }
                            });

                            // $('#yes_secondary_pack_option').select2();

                            // $('#yes_secondary_pack_option').change(function() {
                            //     var secondaryPackValue1 = $(this).val();


                            //     if (secondaryPackValue1 && secondaryPackValue1.includes("Case Packer") ) {
                            //         $('#case_packer').show();
                            //     } else {
                            //         $('#case_packer').hide();
                            //     }
                            // });




                        });

                        function checkcasepacker() {

                            $('#case_packer').attr('required', false);
                            var values = $('.yes_secondary_pack_option').map(function() {
                                return $(this).val();
                            }).get();

                            if (values.includes('Case Packer')) {
                                $('#case_packer').show();
                                $('#case_packer').attr('required', true);
                            } else {
                                $('#case_packer').hide();
                                $('#case_packer').attr('required', false);
                            }

                        }
                    </script>


                    </table>
                    <table class="table">
                        <tr>
                            <td class="text-center" width="3%">52</td>
                            <td width="17%">Ladder & Platform</td>
                            <td width="50%" class="text-center">
                                <select name="ladder_platform" id="" class="form-control" required>
                                    <option value="">--Select--</option>
                                    <option value="Yes" <?php if ($ladder_platform == 'Yes') { ?> selected <?php } ?>>Yes</option>
                                    <option value="No" <?php if ($ladder_platform == 'No') { ?> selected <?php } ?>>No</option>
                                </select>
                            </td>

                            <td width="25%"><input type="text" class="form-control" name="ladder_platform_remarks" value="<?php echo $ladder_platform_remarks; ?>"></td>
                        </tr>

                        <tr>
                            <td class="text-center" width="3%">52</td>
                            <td width="17%">Machine Guarding</td>
                            <td width="50%" class="text-center">

                                <select name="machine_guarding" id="machine_guarding" class="form-control" required onchange="machine()">
                                    <option value="">--Select--</option>
                                    <option value="Aluminium" <?php if ($machine_guarding == 'Aluminium') { ?> selected <?php } ?>>Aluminium</option>
                                    <option value="SS-304" <?php if ($machine_guarding == 'SS-304') { ?> selected <?php } ?>>SS-304</option>
                                    <option value="Shubham Standard" <?php if ($machine_guarding == 'Shubham Standard') { ?> selected <?php } ?>>Shubham Standard</option>
                                    <option value="NA" <?php if ($machine_guarding == 'NA') { ?> selected <?php } ?>>NA</option>
                                </select>

                                <div id="aluminium_option" style="display: none;">
                                    <br>
                                    <select name="aluminium_option" id="aluminium_option_select" class="form-control">
                                        <option value="">--Select--</option>
                                        <option value="Standard" <?php if ($aluminium_option == 'Standard') { ?> selected <?php } ?>>Standard</option>
                                        <option value="360" <?php if ($aluminium_option == '360') { ?> selected <?php } ?>>360</option>
                                        <option value="NA" <?php if ($aluminium_option == 'NA') { ?> selected <?php } ?>>NA</option>
                                    </select>
                                </div>
                                <div id="ss_304_option" style="display: none;">
                                    <br>
                                    <select name="ss_304_option" id="ss_304_option_select" class="form-control">
                                        <option value="">--Select--</option>
                                        <option value="Standard" <?php if ($ss_304_option == 'Standard') { ?> selected <?php } ?>>Standard</option>
                                        <option value="360" <?php if ($ss_304_option == '360') { ?> selected <?php } ?>>360</option>
                                        <option value="NA" <?php if ($ss_304_option == 'NA') { ?> selected <?php } ?>>NA</option>
                                    </select>
                                </div>

                            </td>
                            <td width="25%" style="position: relative;"><textarea name="machine_guarding_remarks" id="" class="form-control text--box"><?php echo $machine_guarding_remarks; ?></textarea></td>
                        </tr>

                        <script>
                            function machine() {
                                var secondaryPackValue = $('#machine_guarding').val();

                                $('#aluminium_option').hide();
                                $('#ss_304_option').hide();
                                if (secondaryPackValue == "Aluminium") {
                                    $('#aluminium_option').show();

                                } else if (secondaryPackValue == "SS-304") {
                                    $('#ss_304_option').show();

                                }
                            }

                            $(document).ready(function() {

                                machine()

                                $('#machine_guarding').change(function() {
                                    var secondaryPackValue = $(this).val();

                                    $('#aluminium_option').hide();
                                    $('#ss_304_option').hide();
                                    $('#aluminium_option_select').val('');
                                    $('#ss_304_option_select').val('');
                                    if (secondaryPackValue == "Aluminium") {
                                        $('#aluminium_option').show();

                                    } else if (secondaryPackValue == "SS-304") {
                                        $('#ss_304_option').show();

                                    }
                                });

                            });
                        </script>
                    </table>
                    <table class="table">

                        <tr>
                            <td class="text-center" width="3%">54</td>
                            <td width="17%">SPECIAL NOTES</td>
                            <td width="75%">
                                <!-- <?php

                                        $qry = $this->db->select('*')->from('df_form_multi_track_machine6')->where('record_id', $id)->get();

                                        if ($qry->num_rows() > 0) {

                                            foreach ($qry->result() as $pouch) {

                                        ?>
                                                <div class="row row_delete" style="margin-bottom:10px;" id="row_<?php echo $pouch->id; ?>">
                                                    <div class="col-sm-8">
                                                        <label for=""><b>Parts Description</b></label>
                                                        <select name="special_notes_list[]" id="special_notes_list" class="form-control" required>
                                                            <option value="">--Select--</option>
                                                            <option value="1" <?php if ($pouch->special_notes_list == '1') { ?> selected <?php } ?>>Coding Pad</option>
                                                            <option value="2" <?php if ($pouch->special_notes_list == '2') { ?> selected <?php } ?>>Oil Seal</option>
                                                            <option value="3" <?php if ($pouch->special_notes_list == '3') { ?> selected <?php } ?>>O-Ring</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <label for=""><b>Qty</b></label>
                                                        <input type="number" name="special_notes_qty[]" class="form-control" value="<?php echo $pouch->special_notes_qty; ?>" required>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button type="button" name="remove" class="btn_remove  btn btn-danger" id="delete_entry" style="padding: 0px !important; height: 21px !important; font-size: 13px !important; width: 21px; margin-top:19px;"><i class="fa fa-trash-o"></i></button>
                                                    </div>
                                                </div>

                                        <?php }
                                        }
                                        ?>

                                        <div class="form-group">
                                            <input type="checkbox" id="add_more" name="add_more">
                                            <label for="add_more">Add More</label>
                                        </div>

                                        <div class="row_show" style="display: none;">
                                            <div class="row " style="margin-bottom:10px; ">
                                                <div class="col-sm-8">
                                                    <label for=""><b>Parts Description</b></label>
                                                    <select name="special_notes_list[]" id="special_notes_list" class="form-control" required>
                                                        <option value="">--Select--</option>
                                                        <option value="1">Coding Pad</option>
                                                        <option value="2">Oil Seal</option>
                                                        <option value="3">O-Ring</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-3">
                                                    <label for=""><b>Qty</b></label>
                                                    <input type="number" name="special_notes_qty[]" class="form-control" value="" required>
                                                </div>
                                                <div class="col-sm-1">
                                                    <button type="button" class="btn btn-warning" name="add" id="addmore_btn1" style="padding: 0px !important; height: 21px !important; font-size: 13px !important; width: 21px; margin-top:19px;"><b>+</b></button>
                                                </div>
                                            </div>

                                            <div id="dynamictasks1"></div>
                                        </div> -->



                                <textarea class="form-control ckeditor" name="special_notes" id="special_notes">
                                            <?php echo $special_notes; ?>
                                        </textarea>
                            </td>
                        </tr>
                    </table>

                    <script>
                        $(document).ready(function() {

                            $('#add_more').on('change', function() {
                                if ($(this).is(':checked')) {
                                    $('.row_show').show();
                                } else {
                                    $('.row_show').hide();
                                }
                            });


                            $(document).on('click', '#delete_entry', function() {

                                if (confirm("Are you sure you want to delete this data?")) {
                                    $(this).closest('.row_delete').remove();
                                } else {
                                    return false;
                                }

                            });
                        });
                    </script>

                    <script>
                        $(document).ready(function() {

                            $('#addmore').on('change', function() {
                                if ($(this).is(':checked')) {
                                    $('.row_show').show();
                                } else {
                                    $('.row_show').hide();
                                }
                            });


                            $(document).on('click', '#delete_pouch_entry', function() {

                                if (confirm("Are you sure you want to delete this data?")) {
                                    $(this).closest('.row_delete').remove();
                                } else {
                                    return false;
                                }

                            });
                        });
                    </script>

                    <script>
                        $(document).ready(function() {

                            $('#add_more').on('change', function() {
                                if ($(this).is(':checked')) {
                                    $('.row_show').show();
                                } else {
                                    $('.row_show').hide();
                                }
                            });


                            $(document).on('click', '#delete_qty_entry', function() {

                                if (confirm("Are you sure you want to delete this data?")) {
                                    $(this).closest('.row_delete').remove();
                                } else {
                                    return false;
                                }

                            });
                        });
                    </script>
                    <!--<table class="table">

                                <tr>
                                    <td class="text-center" width="3%">53</td>
                                    <td width="17%">Design/Trial Comments</td>
                                    <td width="75%">
                                        <input type="text" class="form-control" name="trial_comments">
                                    </td>
                                </tr>
                            </table>-->
                    <table class="table">

                        <tr>
                            <td class="text-center">Reviewed By:</td>
                            </td>
                        </tr>
                    </table>

                    <table class="table">

                        <tr>
                            <td class="text-center">
                                <div class="row" style="margin: 60px 0px;">
                                    <div class="col-sm-2">Project/Marketing Dept.</div>
                                    <div class="col-sm-2">Head Design Dept.</div>
                                    <div class="col-sm-2">Trial Dept.</div>
                                    <div class="col-sm-2">Production/PPC</div>
                                    <div class="col-sm-2">Purchase Dept.</div>
                                    <div class="col-sm-2">Plant Head</div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4">
                                        <p><b>Mr.Rishabh Sharma</b><br>Executive Director - Design & Development</p>
                                    </div>
                                    <div class="col-sm-4">
                                        <p><b>Mr.Shubham Sharma</b><br>Executive Director - Business Development</p>
                                    </div>
                                    <div class="col-sm-4">
                                        <p><b>Mr.Virender Sharma</b><br>Chairman & Managing Director</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <table class="table">
                        <tr>
                            <td>
                                <div class="text-center" style="margin-top: 20px; margin-bottom:20px;">
                                    <button type="submit" class="btn btn-success" style="width: 18%;">Update</button>
                                </div>
                            </td>
                        </tr>
                    </table>

                    </form>
                </div>
            </div>

        </div>



        <!-- Footer -->

        <?php $this->load->view('common/footer'); ?>

        <!-- End Footer -->



    </div> <!-- end container -->

    </div>

    <!-- end wrapper -->





    <!-- jQuery  -->

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>

    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>

    <script src="<?php echo assets_url; ?>js/detect.js"></script>

    <script src="<?php echo assets_url; ?>js/fastclick.js"></script>

    <script src="<?php echo assets_url; ?>js/jquery.slimscroll.js"></script>

    <script src="<?php echo assets_url; ?>js/jquery.blockUI.js"></script>

    <script src="<?php echo assets_url; ?>js/waves.js"></script>

    <script src="<?php echo assets_url; ?>js/wow.min.js"></script>

    <script src="<?php echo assets_url; ?>js/jquery.nicescroll.js"></script>

    <script src="<?php echo assets_url; ?>js/jquery.scrollTo.min.js"></script>



    <!-- Datatables-->

    <script src="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.bootstrap.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.buttons.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/jszip.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/pdfmake.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/vfs_fonts.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.html5.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.print.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.fixedHeader.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.keyTable.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.responsive.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.scroller.min.js"></script>



    <!-- Datatable init js -->

    <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>



    <!-- App js -->

    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>

    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>


    <script language="javascript" type="text/javascript">
        $(document).ready(function() {
            $('.select2').select2({});
        });

        function CKEditorChange(name) {
            CKEDITOR.replace(name);
        }

        var i = 2;


        $('#addmore_btn1').click(function() {
            i++;

            $('#dynamictasks1').append('<div id="row' + i + '" class="row" style="margin-bottom:10px;"><div class="col-sm-8"><label for=""><b>Parts Description</b></label><select name="special_notes_list[]" id="special_notes_list" class="form-control"><option value="">--Select--</option><option value="1">Coding Pad</option><option value="2">Oil Seal</option><option value="3">O-Ring</option></select></div><div class="col-sm-3"><label for=""><b>Qty</b></label><input type="number" name="special_notes_qty[]" class="form-control" ></div><div class="col-sm-1"><button type="button" name="add" class="btn_remove  btn btn-danger" id="' + i + '" style="padding: 0px !important; height: 21px !important; font-size: 13px !important; width: 21px; margin-top:19px;"><i class="fa fa-close"></i></button></div></div>');

            showhidedfbox();

        });


        $(document).on('click', '.btn_remove', function() {
            var button_id = $(this).attr("id");
            $('#row' + button_id + '').remove();
        });
    </script>
    </script>

    <script src="<?php echo assets_url; ?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>



    <script>
        $(document).on('click', '.btn_remove', function() {
            var rowId = $(this).data('id'); // Get the ID of the row
            var rowElement = $('#pack-' + rowId); // The entire row to remove

            if (confirm("Are you sure you want to delete this item?")) {
                $.ajax({
                    url: '<?php echo page_url ?>Dashboard/secondary_pack_delete', // URL to the controller method
                    type: 'POST',
                    data: {
                        id: rowId
                    }, // Send the ID to the controller
                    success: function(response) {
                        if (response == 'success') {
                            rowElement.remove(); // Remove the row from the table if deletion was successful
                            alert('Item deleted successfully!');
                        } else {
                            alert('Error deleting item!');
                        }
                    },
                    error: function() {
                        alert('An error occurred!');
                    }
                });
            }
        });
    </script>


<script>
    $(document).on('click', '.delete_pouch_entry', function () {
    let id = $(this).data('id');
    let button = $(this);

    if (confirm("Are you sure you want to delete this entry?")) {
        $.ajax({
            url: "<?php echo page_url ?>Dashboard/delete_pouch_entry",
            type: "POST",
            data: {id: id},
            success: function (response) {
                if (response == 'success') {
                    button.closest('.row').remove();
                } else {
                    alert('Error deleting data.');
                }
            }
        });
    }
});
</script>

<script>
    $(document).on('click', '.delete_qty_entry', function () {
    let id = $(this).data('id');
    let button = $(this);

    if (confirm("Are you sure you want to delete this entry?")) {
        $.ajax({
            url: "<?php echo page_url ?>Dashboard/delete_qty_entry",
            type: "POST",
            data: {id: id},
            success: function (response) {
                if (response == 'success') {
                    button.closest('.row').remove();
                } else {
                    alert('Error deleting data.');
                }
            }
        });
    }
});
</script>

    <script>
        var i = 2;


        $('#addmore_btn3').click(function() {

            $('#secondary_packData').append('<div id="row' + i + '" class="row" style="margin-top:10px;"><div class="col-sm-8"><select required name="yes_secondary_pack_add[]" id="yes_secondary_pack_option" class="form-control yes_secondary_pack_option" style="width: 100%;" onchange="checkcasepacker()"><option value="">--Select--</option><option value="Collating Conveyor">Collating Conveyor</option><option value="Auto Collating Conveyor">Auto Collating Conveyor</option><option value="Tranfer Conveyor">Tranfer Conveyor</option><option value="Taper Conveyor">Taper Conveyor</option><option value="Case Erector">Case Erector</option><option value="Auto Case Erector">Auto Case Erector</option><option value="Case Packer">Case Packer</option><option value="Auto Case Packer">Auto Case Packer</option><option value="Flow Wrap Machine">Flow Wrap Machine</option><option value="Case Taping Machine">Case Taping Machine</option><option value="Check Weigher with Rejection System">Check Weigher with Rejection System</option><option value="Auto Sack Packer">Auto Sack Packer</option><option value="Auto L-Sealer Machine">Auto L-Sealer Machine</option></select></div><div class="col-sm-3"><input type="text" name="yes_secondary_pack_qty_add[]" required placeholder="add quantity" class="form-control"></div><div class="col-sm-1"><button type="button" name="add" class="btn_remove1  btn btn-danger" id="' + i + '" style="padding: 0px !important; height: 21px !important; font-size: 13px !important; width: 21px;"><i class="fa fa-close"></i></button></div></div>');

            // // showhidedfbox();


            i++;
        });


        $(document).on('click', '.btn_remove1', function() {
            var button_id = $(this).attr("id");
            $('#row' + button_id + '').remove();
        });
    </script>

    <script>
        var i = 2;


        $('#addmore_btn5').click(function() {

            $('#multi_pouch').append('<div id="row1' + i + '" class="row" style="margin-top:10px;"><div class="col-sm-3"><input type="text" class="form-control" name="pouch_width[]" value="W-"></div><div class="col-sm-3"><input type="text" class="form-control" name="pouch_length[]" value="L-" ></div><div class="col-sm-3"><input type="text" class="form-control" name="pouch_height[]" value="H-" ></div><div class="col-sm-3"><button type="button" name="add" class="btn_remove1  btn btn-danger" id="' + i + '" style="padding: 0px !important; height: 21px !important; font-size: 13px !important; width: 21px;"><i class="fa fa-close"></i></button></div></div>');

            i++;
        });


        $(document).on('click', '.btn_remove1', function() {
            var button_id = $(this).attr("id");
            $('#row1' + button_id + '').remove();
        });
    </script>


<script>
        var i = 2;


        $('#addmore_btn6').click(function() {

            $('#multiqty').append('<div id="row2' + i + '" class="row" style="margin-top:10px;"> <div class="col-sm-7"><input type="text" class="form-control" name="quantity_packed[]" value=""></div><div class="col-sm-3"><select class="form-control qtyunitdata" name="quantity_packed_unit[]" id="editqty_unit" required onchange="selectProductPacked();"><option value="">Select Qty</option><option value="ml">ml</option><option value="gm">gm</option></select></div><div class="col-sm-2"><button type="button" name="add" class="btn_remove2  btn btn-danger" id="' + i + '" style="padding: 0px !important; height: 21px !important; font-size: 13px !important; width: 21px;"><i class="fa fa-close"></i></button></div></div>');

            i++;
        });


        $(document).on('click', '.btn_remove2', function() {
            var button_id = $(this).attr("id");
            $('#row2' + button_id + '').remove();
        });
    </script>


</body>

</html>