<?php
$CI = &get_instance();
$po_id = $this->uri->segment(3);
$lead_id = $this->uri->segment(4);
$CI->load->model('Salescrm_model', 'salescrm');

$quote_record_id = $this->salescrm->getRecordID($lead_id);

// echo $record_id; exit;

$annexture_1 = $this->salescrm->getannexture_1($quote_record_id);
// echo "<pre>"; print_r($annexture_1); exit;
if (count($annexture_1) > 0) {
    $product_to_be_packed = $annexture_1[0];
    $batchcut = $annexture_1[16];
    $liquidviscositydata = $annexture_1[10];
    $powderdensitydata = $annexture_1[12];
    $qty_data = $this->salescrm->getQtyPackedData($quote_record_id);
     $horizontal_sealing_width=$annexture_1[4];
     $vertical_sealing_width=$annexture_1[5];
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


$getannexture_2 = $this->salescrm->getannexture_2($quote_record_id);
// echo "<pre>"; print_r($getannexture_2); exit;
if (count($getannexture_2) > 0) {
    $no_of_track = $getannexture_2[3];
    $machinemodel = $getannexture_2[0];
} else {
    $no_of_track = '';
    $machinemodel = '';
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



?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title>DF Project Form | <?php echo sitetitle; ?></title>
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
                //     if($existingdfno->df_sr_no==1730){
                //         $nextdf = $existingdfno->df_sr_no+1;
                //     }else{
                //         $nextdf = '1730';
                //     }
                    
        $q = $this->db->select('podate,df_number')->from('poreceived')->where('lead_id', $lead_id)->get();

        if($q->num_rows()>0)
        {
                                    foreach ($q->result() as $dfform);
                                    $df_number=$dfform->df_number;
                                    $podate=$dfform->podate;
            }else
            {
                $df_number='';
                $podate='';

                ECHO "DF NUMBER NOT FOUND"; exit;
            }
                                    
                ?>
                <div class="col-sm-12">
                    <?php echo  $this->session->flashdata('success'); ?>
                    <div class="form_box">
                        <form action="<?php echo page_url; ?>Dashboard/edit_design_form_600/<?php echo $this->uri->segment(3); ?>/<?php echo $this->uri->segment(4); ?>/<?php echo $id; ?>/<?php echo $this->uri->segment(5); ?>" enctype="multipart/form-data" method="post">
                            <table class="table">
                                <tr>
                                    <th width="50%">From: Project Dept.</th>
                                    <th class="text-center" width="50%">Reference DF. No.-</th>
                                   <!-- <th colspan="2" width="35%">To: All Departments</th>-->
                                    
                                </tr>
                                <tr>
                                    <td class="text-center" rowspan="9" style="vertical-align: middle;">
                                        <p><b>DESIGN FORM<br>
                                          <!-- <input type="text" class="form-control" name="design_form_name" value="<?php echo $nextdf;?>" style="text-align:center;" readonly required>-->
                                        <b>DF-<?php echo $df_number;?></b><br>
                                        <b>Date:</b><input type="date" class="form-control" name="design_form_date" style="text-align: center;" min="<?php echo date('Y-m-d');?>" required value="<?php echo $design_form_date; ?>"></b></p>
                                    </td>
                                    <td class="text-center" rowspan="9" style="vertical-align: middle;">
                                        <input type="text" name="reference_no" class="form-control" value="<?php echo $reference_no; ?>" required>
                                        <p><b>Reference DF Date:<br><input type="date" class="form-control" value="<?php echo $ref_df_date; ?>" name="ref_df_date" max="<?php echo date('Y-m-d'); ?>" required></b></p>
                                    </td>
                                </tr>
                            </table>
                            <table class="table">
                                <tr>
                                    <th class="text-center" colspan="3">Order Details For MULTI TRACK MACHINE</th>
                                    <th class="text-center" colspan="1" width="25%">REMARKS</th>
                                </tr>

                                <tr>
                                    <td class="text-center" width="3%">>></td>
                                    <td width="17%">BOM No.</td>
                                    <td width="50%" class="text-center">
                                       <input type="text" class="form-control" name="bom_no" value="<?php echo $bom_no; ?>">
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="bom_no_remarks" value="<?php echo $bom_no_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center">>></td>
                                    <td>Date of PO</td>
                                   
                                    <td width="50%" class="text-center">
                                       <?php echo $podate; ?>
                                    </td>
                                    <?php ?>
                                    <td><input type="text" class="form-control" name="date_of_po_remarks" value="<?php echo $date_of_po_remarks; ?>"></td>
                                </tr>
                             
                                <tr>
                                    <td class="text-center">>></td>
                                    <td>Dispatch date</td>
                                    <td width="50%" class="text-center">
                                        
                                    <input type="date" class="form-control"  name="dispatch_date" value="<?php echo $dispatch_date; ?>" readonly>
                                   
                                
                                </td>
                                    <td><input type="text" class="form-control" name="dispatch_date_remarks" value="<?php echo $dispatch_date_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center">>></td>
                                    <td>Trial Date</td>

                                    <td width="50%" class="text-center">

                                    <input type="date" class="form-control" name="trial_date" value="<?php echo $trial_date; ?>" readonly>
                                      
                                    </td>

                                    <td><input type="text" class="form-control" name="trial_date_remarks" value="<?php echo $trial_date_remarks; ?>"></td>
                                </tr>
                                    <script type="text/javascript">
                                    $(document).ready(function() {
                                    $('#trial_date, #dispatch_date').on('change', function() {
                                    let trialDate = new Date($('#trial_date').val());
                                    let dispatchDate = new Date($('#dispatch_date').val());

                                    if (trialDate > dispatchDate) {
                                    alert('Trial Date cannot be greater than Dispatch Date.');
                                    $('#trial_date').val('');
                                    }
                                    });
                                    });

                                    </script>

                                      <tr>
                                    <td class="text-center">>></td>
                                    <td>Qty. of Machine</td>
                                    <td width="50%" class="text-center">
                                        
                                      <input type="text" name="machine_qty" class="form-control" value="<?php echo $machine_qty; ?>">

                                </td>
                                    <td><input type="text" class="form-control" name="machine_qty_remarks" value="<?php echo $machine_qty_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center">>></td>
                                    <td>Machine Model No.</td>
                                    <td width="50%" class="text-center">
                                        
                                    <?php echo $machinemodel; ?>
                                    </td>
                                    <td><input type="text" class="form-control" name="machine_mode_no_remarks" value="<?php echo $machine_mode_no_remarks; ?>"></td>
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
                                    
                                        <?php echo $no_of_track; ?>
                                    
                                    </td>
                                    <td><input type="text" class="form-control" name="tracks_remarks" value="<?php echo $tracks_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">2</td>
                                    <td width="17%">Product to be Packed</td>
                                    <td width="25%" class="text-center">
                                        <?php
                                        if($product_to_be_packed==1){
                                            $producttt = 'Liquid / Paste';
                                        }else if($product_to_be_packed==2){
                                             $producttt = 'Powder / Granules';
                                        }
                                        ?>

                                       <p> <?php echo $producttt; ?></p>


                                       
                                        <p><?php echo $powder_option; ?></p>


                                        <p><?php echo $liquid_option; ?></p>
                                       
                                      
                                    </td>
                                    <td><input type="text" class="form-control" name="product_packed_remarks" value="<?php echo $product_packed_remarks; ?>"></td>
                                </tr>

                                <script>

                                    function selectedoption() {
                                        var selectedValue = $('#product_packed').val();

                                        // Reset related selects, but keep values that were set dynamically intact.
                                        //   $('#liquid_option_select').val('');
                                     

                                        // Hide all related options
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
                                        // Show corresponding options based on the selected value
                                        if (selectedValue == '2') {
                                            $('#powder_option').show();
                                              $('#density').show();
                                        } else if (selectedValue == '1') {
                                            $('#liquid_option').show();
                                              $('#viscosity').show();

                                        }
                                    }

                                    function selectliq(){

                                          var selectedValue = $('#liquid_option_select').val();

                                           $('#non_viscous_option').hide();
                                        $('#viscous_option').hide();

                                            if (selectedValue == 'Non-Viscous') {
                                            $('#non_viscous_option').show();
                                        } else if (selectedValue == 'Viscous') {
                                            $('#viscous_option').show();

                                        }

                                        }

                                         function selectpow(){

                                          var selectedValue = $('#powder_option_select').val();

                                           $('#free_flow_option').hide();
                                        $('#non_free_flow').hide();

                                            if (selectedValue == 'Free Flow') {
                                            $('#free_flow_option').show();
                                        } else if (selectedValue == 'Non Free Flow') {
                                            $('#non_free_flow').show();

                                        }

                                        }

                                          function weigher_fun(){
                                         
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


                                    function weigher_system(){
                                       
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

                                    $(document).ready(function() {
                                        // Call selectedoption when the page loads to ensure initial state
                                        selectedoption();

                                        selectliq();

                                        selectpow();

                                          weigher_system();

                                        // When the product_packed selection changes
                                        $('#product_packed').change(function() {
                                           var selectedValue = $(this).val();

                                           
                                            $('#viscous_option_select').val('');
                                         
                                            $('#weigher_system_option_select').val('');
                                            $('#liner_weigher_option_select').val('');
                                            $('#mult_head_weigher_option_select').val('');
                                            $('#non_viscous_option_select').val('');
                                            $('#piston_filler_option_select').val('');
                                            $('#non_free_flow_option_select').val('');
                                            $('#powder_option_select').val('');
                                            $('#liquid_option_select').val('');
                                          
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
                                            $('#non_free_flow').hide();
                                            
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

                                            $('#viscous_option_select').val('');

                                            $('#non_viscous_option_select').val('');

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

                                            $('#piston_filler_option_select').val('');

                                            $('#piston_filler_option_select')

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

                                            $('#free_flow_option_select').val('');

                                            $('#non_free_flow_option_select').val('');

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

                                            $('#mult_head_weigher_option').hide();

                                            $('#weigher_system_option_select').val('');

                                            $('#volumetric_cap_option_select').val('');

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

                                            $('#liner_weigher_option_select').val('');

                                             $('#mult_head_weigher_option_select').val('');


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

                                      
                                        <p><?php echo $non_free_flow_option; ?></p>


                                        

                                          <p><?php echo $cup_filler_option; ?></p>


                                        <!---------IF Select Free Flow--------->

                                       


                                         <p><?php echo $free_flow_option; ?></p>

                                        <!---------IF Weigher System--------->

                                      

                                         <p><?php echo $weigher_system_option; ?></p>

                                        <!---------IF Weigher System--------->

                                        <!---------IF Liner Weigher--------->

                                      

                                         <p><?php echo $liner_weigher_option; ?></p>

                                        <!---------IF Liner Weigher--------->

                                        <!---------IF Multi Head Weigher--------->

                                     

                                         <p><?php echo $mult_head_weigher_option; ?></p>

                                        <!---------IF Multi Head Weigher--------->

                                        <!---------IF Volumetric Cap Filler--------->

                                      

                                         <p><?php echo $volumetric_cap_option; ?></p>

                                        <!---------Non-Viscous--------->
                                       

                                        <p><?php echo $non_viscous_option; ?></p>
                                        <!---------Non-Viscous--------->

                                         <!---------Viscous--------->
                                      

                                        <p><?php echo $viscous_option; ?></p>
                                        <!---------Viscous--------->

                                        

                                         <p><?php echo $piston_filler_option; ?></p>

                                      

                                        <p><?php echo $follow_meter_option; ?></p>

                                        <!---------IF Volumetric Cap Filler--------->

                                     
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
                                    <td width="17%" style="vertical-align: middle;">Quantity to be packed</td>
                                    <td width="50%" class="text-center">
                                        <?php
                                        if (count($qty_data) > 0) {
                                            for ($r = 0; $r < count($qty_data['qty']); $r++) {
                                        ?>
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <?php echo $qty_data['qty'][$r]; ?>  <?php echo $qty_data['unit'][$r]; ?>
                                                    </div>
                                                </div>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td width="25%" class="text-center" ><input name="quantity_packed_remarks" class="form-control" id="" value="<?php echo $quantity_packed_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%" style="vertical-align: middle;">5</td>
                                    <td width="17%" style="vertical-align: middle;">Pouch Size (W x L) in mm</td>
                                    <td width="50%" class="text-center"> <?php
                                                                            if (count($qty_data) > 0) {
                                                                                for ($r = 0; $r < count($qty_data['qty']); $r++) {
                                                                            ?>
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                       
                                                       <p>W- <?php echo $qty_data['width'][$r]; ?><p>
                                                    
                                                    </div>
                                                    <div class="col-sm-4">
                                                        
                                                   
                                                    <p>L- <?php echo $qty_data['length'][$r]; ?></p>

                                                </div>
                                                   
                                                <p>H- <?php echo $qty_data['height'][$r]; ?><p>
                                                </div>
                                                </div>
                                        <?php
                                                                                }
                                                                            }
                                        ?>
                                    </td>

                                    <td width="25%" class="text-center" ><input name="pouch_size_remarks" class="form-control" id="" value="<?php echo $pouch_size_remarks; ?>"></td>
                                </tr>
                              
                            </table>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">6</td>
                                    <td width="17%">Horizontal Profile of Sealing</td>
                                    <td width="50%">
                                         <p><?php echo $typeofsealing; ?></p>
                                    </td>
                                    <td width="25%">
                                        <input type="text" name="profle_sealing_remarks" value="<?php echo $profle_sealing_remarks; ?>" class="form-control">
                                    </td>
                                </tr>
                            </table>
                            <table class="table">
                              
                                <tr>
                                    <td class="text-center" width="3%">7</td>
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
                                    <td class="text-center" width="3%">8</td>
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

                                function open_(){
                                      var openableOption = $('#openable_option').val();

                                      
                                        $('#non_pressurised').hide();


                                        if (openableOption == 'Pressurised') {
                                            $('#pressurised').show();
                                        } else if (openableOption == 'Non-Pressurised / Normal') {
                                            $('#non_pressurised').show();
                                            $('#pressurised').hide();
                                        }
                                }

                                function close_(){
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

                                          $('#non_pressurised_option').val('');

                                           $('#pressurised_option').val('');

                                           $('#closed_pressurised_option').val('');

                                            $('#non_closed_pressurised_option').val('');

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
                           
                           

                            <!--------------------------------------------------------------->
<?php 
    $rest=$this->db->select('id,name')->from('quote_parts_heading_master')->where('id',13)->get();
    if($rest->num_rows()>0)
    {
        foreach($rest->result() as $row)
        {
    ?>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">9</td>
                                    <td width="17%">


<?php echo $row->name;?>

  


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
             $rest1=$this->db->select('id,name')->from('quote_parts_heading_master_options')->where('head_id',$row->id)->get();
    if($rest->num_rows()>0)
    {
        foreach($rest1->result() as $row1)
        {
            $resty=$this->db->select('value_id')->from('quotation_brand_data')->where('record_id',$record_id)->where('head_id',$row->id)->get();
            if($resty->num_rows()>0)
            {
                foreach($resty->result() as $rrrow);
                $valueid=$rrrow->value_id;
            }else{
                $valueid=0;
            }
    ?>
    <option value="<?php echo $row1->id;?>" <?php if($row1->id==$web_aligner_option){ ?> selected <?php } ?>><?php echo $row1->name;?></option>
    <?php 
        } 
        } 
        ?>

      


                                              
                                                       
                                                    </select>
</div>
                                    </td>
                                   
                                     <td width="25%"><input type="text" class="form-control" name="web_aligner_remarks" value="<?php echo $web_aligner_remarks; ?>"></td>
                                </tr>
                            </table>
<?php } } ?>

                            <script>
                                function webaligner(){

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
                                    <td class="text-center" width="3%">10</td>
                                    <td width="17%">Provision of Coding</td>
                                     <td width="50%" class="text-center">
                                        <select name="provision_coding" id="provision_coding" class="form-control required_block" required onchange="coding()">
                                            <option value="">--Select--</option>
                                            <option value="Yes" <?php if ($provision_coding == 'Yes') { ?> selected <?php } ?>>Yes</option>
                                            <option value="No" <?php if ($provision_coding == 'No') { ?> selected <?php } ?>>No</option>
                                        </select>


                                        <div id="provision_coding_option" style="display: none;">
                                            <br>
                                            <p>Hinge Type</p>

                                            <select name="provision_coding_option" id="provision_coding_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="BRADMA" <?php if ($provision_coding_option == 'BRADMA') { ?> selected <?php } ?>>BRADMA</option>
                                                <option value="SHUBHAM STD." <?php if ($provision_coding_option == 'SHUBHAM STD.') { ?> selected <?php } ?>>SHUBHAM STD.</option>
                                            </select>

                                        </div>

                                     </td>
                                    <td width="25%"><input type="text" class="form-control" name="provision_coding_remarks" value="<?php echo $provision_coding_remarks; ?>" placeholder="Digit Size"></td>
                                </tr>

                                 <script>

function coding(){

     var webValue = $('#provision_coding').val();




                                        $('#provision_coding_option').hide();

                                     

                                        if (webValue == 'Yes') {
                                            $('#provision_coding_option').show();
                                        }


}



                                     $(document).ready(function() {

                                        coding();

                                    $('#provision_coding').change(function() {
                                        var selectedValue = $(this).val();

                                        $('#provision_coding_option_select').val('');

                                      
                                        $('#provision_coding_option').hide();


                                        if (selectedValue == 'Yes') {
                                            $('#provision_coding_option').show();
                                        }

                                     
                                    });
});
                                </script>

                                <tr>
                                    <td class="text-center" width="3%">11</td>
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
                                    <td class="text-center" width="3%">12</td>
                                    <td width="17%">Filling Tank</td>
                                    <td width="50%" class="text-center">
                                       <select name="filling_tank" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                        <option value="Header" <?php if ($filling_tank == 'Header') { ?> selected <?php } ?>>Header</option>
                                            <option value="NA" <?php if ($filling_tank == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>
                                    </td>
                                     <td width="25%"><input type="text" class="form-control" name="filling_tank_remarks" value="<?php echo $filling_tank_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">13</td>
                                    <td width="17%">Vertical Slitter</td>
                                    <td width="50%" class="text-center">
                                       <select name="vertical_slitting" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Rotary" <?php if ($vertical_slitting == 'Rotary') { ?> selected <?php } ?>>Rotary</option>
                                             <option value="Blade" <?php if ($vertical_slitting == 'Blade') { ?> selected <?php } ?>>Blade</option>
                                            <option value="NA" <?php if ($vertical_slitting == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>
                                    </td>
                                     <td width="25%"><input type="text" class="form-control" name="vertical_slitting_remarks" value="<?php echo $vertical_slitting_remarks; ?>"></td>                               </tr>
                                 <tr>
                                    <td class="text-center" width="3%">14</td>
                                    <td width="17%">Pulling Machine</td>
                                    <td width="50%" class="text-center">
                                        <select name="pulling_machine" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Servo" <?php if ($pulling_machine == 'Servo') { ?> selected <?php } ?>>Servo</option>
                                            <option value="Clutch Brake" <?php if ($pulling_machine == 'Clutch Brake') { ?> selected <?php } ?>>Clutch Brake</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="pulling_machine_remarks" value="<?php echo $pulling_machine_remarks; ?>"></td>
                                </tr>
                                   <tr>
                                    <td class="text-center" width="3%">15</td>
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
                                    <td class="text-center" width="3%">16</td>
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
                                    <td class="text-center" width="3%">17</td>
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
                                </tr>
                                 <tr>
                                    <td class="text-center" width="3%">18</td>
                                    <td width="17%">Piston Movement</td>
                                    <td width="50%" class="text-center">
                                          <select name="priston_drive" id="" class="form-control" required>
                                           
                                               <option value="">--Select--</option>
                                             <option value="Servo Motor Driven" <?php if ($priston_drive == 'Servo Motor Driven') { ?> selected <?php } ?>>Servo Motor Driven</option>
                                            <option value="CAM Driven" <?php if ($priston_drive == 'CAM Driven') { ?> selected <?php } ?>>CAM Driven</option>
                                            <option value="Pneumatic" <?php if ($priston_drive == 'Pneumatic') { ?> selected <?php } ?>>Pneumatic</option>
                                             <option value="NA" <?php if ($priston_drive == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="priston_drive_remarks" value="<?php echo $priston_drive_remarks; ?>"></td>
                                </tr>
                              
                             
                               
                              
                                <tr>
                                    <td class="text-center" width="3%">19</td>
                                    <td width="17%">Valve Movement</td>
                                    <td width="50%" class="text-center">

                                     <select name="valve_movement" id="" class="form-control " required>
                                           
                                               <option value="">--Select--</option>
                                             <option value="Servo Motor Driven" <?php if ($valve_movement == 'Servo Motor Driven') { ?> selected <?php } ?>>Servo Motor Driven</option>
                                            <option value="CAM Driven" <?php if ($valve_movement == 'CAM Driven') { ?> selected <?php } ?>>CAM Driven</option>
                                            <option value="Pneumatic" <?php if ($valve_movement == 'Pneumatic') { ?> selected <?php } ?>>Pneumatic</option>
                                             <option value="NA" <?php if ($valve_movement == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>
                                      
                                            
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="valve_movement_remarks" value="<?php echo $valve_movement_remarks; ?>"></td>
                                </tr>
                               
                            </table>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">20</td>
                                    <td width="17%">Nozzle/Funnel</td>
                                    <td width="50%" class="text-center" colspan="2">
                                        <select name="nozzle_funnel" id="nozzle_funnel" class="form-control" required onchange="funnel()">
                                            <option value="">--Select--</option>
                                            <option value="Powder" <?php if ($nozzle_funnel == 'Powder') { ?> selected <?php } ?>>Powder</option>
                                            <option value="Liquid - Capillary" <?php if ($nozzle_funnel == 'Liquid - Capillary') { ?> selected <?php } ?>>Liquid - Capillary</option>
                                            <option value="Liquid - Shut off Nozzle" <?php if ($nozzle_funnel == 'Liquid - Shut off Nozzle') { ?> selected <?php } ?>>Liquid - Shut off Nozzle</option>
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
                                    <td class="text-center" width="3%">21</td>
                                    <td width="17%">Horizontal Sealer Width</td>
                                    <td width="50%" class="text-center">
                                       

                                        <p><?php echo $horizontal_sealing_width;?>mm</p>
                                      
                                        
                                    
                                    </td>
                                    <td width="25%"style="position: relative;">
                                        <textarea name="horizontal_sealer1_remarks" value="" id="" class="form-control text--box"><?php echo $horizontal_sealer1_remarks;?></textarea>
                                    </td>
                                </tr>
                               
                            </table>
                            <table class="table"> 
                               
                                <tr>
                                    <td class="text-center" width="3%">22</td>
                                    <td width="17%">Vertical Sealer Width</td>
                                    <td width="50%" class="text-center" >
                                      
                                        
                                         <p><?php echo $vertical_sealing_width;?>mm</p>
                                        
                                      
                                    </td>
                                    <td width="25%" style="position: relative;">
                                        <textarea name="vertical_sealer1_remarks" id="" class="form-control text--box"><?php echo $vertical_sealer1_remarks;?></textarea>
                                    </td>
                                </tr>
                              
                            </table>
                            

                            <table class="table">

                                <tr>
                                    <td class="text-center" width="3%">23</td>
                                    <td width="17%">Working Speed</td>
                                    <td width="50%" class="text-center">
                                        <input type="text" name="working_speed" value="<?php echo $working_speed; ?>" class="form-control" required>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="working_speed_remarks" value="<?php echo $working_speed_remarks; ?>"></td>
                                </tr>
                            </table>
                          

                            <table class="table">

                                <tr>
                                    <td class="text-center" width="3%">24</td>
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
                                    <td class="text-center" width="3%">25</td>
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
                                    <td class="text-center" width="3%">26</td>
                                    <td width="17%">Laminate for Trial</td>
                                    <td width="50%">
                                        <select name="laminate_trial"  class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Yes" <?php if ($laminate_trial == 'Yes') { ?> selected <?php } ?>>Yes</option>
                                            <option value="No" <?php if ($laminate_trial == 'No') { ?> selected <?php } ?>>No</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="laminate_trail_remarks" value="<?php echo $laminate_trail_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">27</td>
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
                                    <td class="text-center" width="3%">28</td>
                                    <td width="17%">Heater & SSR Fault Detection Box</td>
                                    <td width="50%">
                                        <select name="heater_ssr_box" id="heater_ssr_box" class="form-control" required>
                                            <option value="">--Select--</option>
                                           <option value="Yes" <?php if ($heater_ssr_box == 'Yes') { ?> selected <?php } ?>>Yes</option>
                                            <option value="No" <?php if ($heater_ssr_box == 'No') { ?> selected <?php } ?>>No</option>
                                           
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="heater_ssr_box_remarks" value="<?php echo $heater_ssr_box_remarks; ?>"></td>
                                </tr>
                            </table>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">29</td>
                                    <td width="17%">Beacon Lights</td>
                                    <td width="50%" class="text-center" colspan="1">
                                          <select name="beacon_light" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Yes" <?php if ($beacon_light == 'Yes') { ?> selected <?php } ?>>Yes</option>
                                            <option value="No" <?php if ($beacon_light == 'No') { ?> selected <?php } ?>>No</option>
                                             <option value="NA" <?php if ($beacon_light == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="beacon_light_remarks" value="<?php echo $beacon_light_remarks; ?>"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">30</td>
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
                                                <option value="ETH" <?php if ($hooper_level_option == 'ETH') { ?> selected <?php } ?>>ETH</option>
                                                <option value="NA" <?php if ($hooper_level_option == 'NA') { ?> selected <?php } ?>>NA</option>
                                            </select>
                                        </div>
                                    </td>
                                   <td width="25%"><input type="text" class="form-control" name="hooper_level_remarks" value="<?php echo $hooper_level_remarks; ?>"></td>
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
                                    <td class="text-center" width="3%">31</td>
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
                                    <td class="text-center" width="3%">32</td>
                                    <td width="17%">PLC Make</td>


                                    <td width="50%" class="text-center"><select name="plc_maker" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Allen Bradely" <?php if ($plc_maker == 'Allen Bradely') { ?> selected <?php } ?>>Allen Bradely</option>
                                            <option value="Omron" <?php if ($plc_maker == 'Omron') { ?> selected <?php } ?>>Omron</option>
                                            <option value="Mitsubhishi" <?php if ($plc_maker == 'Mitsubhishi') { ?> selected <?php } ?>>Mitsubhishi</option>
                                             <option value="NA" <?php if ($plc_maker == 'NA') { ?> selected <?php } ?>>NA</option>
                                        </select></td>

                                    <td width="25%"><input type="text" class="form-control" name="plc_maker_remarks" value="<?php echo $plc_maker_remarks; ?>"></td>

                                </tr>
                                
                               <tr>
                                    <td class="text-center" width="3%">33</td>
                                    <td width="17%">Conveyor</td>


                                    <td width="50%" class="text-center">
                                        <select name="conveyor" id="conveyor" class="form-control" required onchange="con();">
                                            <option value="">--Select--</option>
                                            <option value="Yes" <?php if ($conveyor == 'Yes') { ?> selected <?php } ?>>Yes</option>

                                            <option value="No" <?php if ($conveyor == 'No') { ?> selected <?php } ?>>No</option>
                                        </select>
                                    
                                        <div id="conveyor_option" style="display: none;">
<br>
                                        <select name="conveyor_option_yes" id="conveyor_option_yes" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Std Conveyor" <?php if ($conveyor_option_yes == 'Std Conveyor') { ?> selected <?php } ?>>Std Conveyor</option>
                                            <option value="Collating Conveyor" <?php if ($conveyor_option_yes == 'Collating Conveyor') { ?> selected <?php } ?>>Collating Conveyor</option>
                                        </select>

                                        </div>
                                    
                                    
                                    </td>

                                    <td width="25%"><input type="text" class="form-control" name="conveyor_remarks" value="<?php echo $conveyor_remarks; ?>"></td>
                                </tr>

                                <script>
                                      function con() {
                                        var selectedValue = $('#conveyor').val();


                                       $('#conveyor_option').hide();

                                         

                                            if (selectedValue == 'Yes') {
                                                $('#conveyor_option').show();
                                            }
                                    }
                                    $(document).ready(function() {

                                        con();

                                        $('#conveyor').change(function() {
                                            var selectedValue = $(this).val();


                                            $('#conveyor_option').hide();

                                            $('#conveyor_option_yes').val('');

                                            if (selectedValue == 'Yes') {
                                                $('#conveyor_option').show();
                                            }
                                        });
                                    });
                                </script>

                                  <tr>
                                    <td class="text-center" width="3%">34</td>
                                    <td width="17%">Standard Spares</td>


                                    <td width="50%" class="text-center">
                                        <select name="standard" class="form-control" required>
                                            <option value="">--Select--</option>
                                          <option value="Yes" <?php if ($standard == 'Yes') { ?> selected <?php } ?>>Yes</option>

                                            <option value="No" <?php if ($standard == 'No') { ?> selected <?php } ?>>No</option>
                                        </select>
                                    
                                      
                                    </td>

                                    <td width="25%"><input type="text" class="form-control" name="standard_remarks" value="<?php echo $standard_remarks; ?>"></td>
                                </tr>
                            </table>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">35</td>
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
                                    <td class="text-center" width="3%">36</td>
                                    <td width="17%">SPECIAL NOTES</td>
                                    <td width="75%">
                                        <textarea class="form-control ckeditor" name="special_notes" id="special_notes"><?php echo $special_notes; ?></textarea>
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
                                            <div class="col-sm-2">Group CEO</div>
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
                                            <button type="submit" class="btn btn-success" style="width: 18%;">Submit</button>
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

//         var i=2;

 
//    $('#addmore_btn1').click(function() {
//                 i++;

//                 $('#dynamictasks1').append('<div id="row' + i + '" class="row" style="margin-bottom:10px;"><div class="col-sm-8"><label for=""><b>Parts Description</b></label><select name="special_notes_list[]" id="special_notes_list" class="form-control"><option value="">--Select--</option><option value="1">Coding Pad</option><option value="2">Oil Seal</option><option value="3">O-Ring</option></select></div><div class="col-sm-3"><label for=""><b>Qty</b></label><input type="number" name="special_notes_qty[]" class="form-control" ></div><div class="col-sm-1"><button type="button" name="add" class="btn_remove  btn btn-danger" id="' + i + '" style="padding: 0px !important; height: 21px !important; font-size: 13px !important; width: 21px; margin-top:19px;"><i class="fa fa-close"></i></button></div></div>'); 

//                 showhidedfbox();

//             });
 
 
//  $(document).on('click', '.btn_remove', function(){
//  var button_id = $(this).attr("id");
//  $('#row'+button_id+'').remove();
//  });
    </script>


<script>
    var i=2;

 
   $('#addmore_btn3').click(function() {
                
                $('#secondary_packData').append('<div id="row' + i + '" class="row" style="margin-top:10px;"><div class="col-sm-8"><select name="yes_secondary_pack[]" id="yes_secondary_pack_option" class="form-control yes_secondary_pack_option" style="width: 100%;" onchange="checkcasepacker()"><option value="">--Select--</option><option value="Collating Conveyor">Collating Conveyor</option><option value="Auto Collating Conveyor">Auto Collating Conveyor</option><option value="Tranfer Conveyor">Tranfer Conveyor</option><option value="Taper Conveyor">Taper Conveyor</option><option value="Case Erector">Case Erector</option><option value="Auto Case Erector">Auto Case Erector</option><option value="Case Packer">Case Packer</option><option value="Auto Case Packer">Auto Case Packer</option><option value="Flow Wrap Machine">Flow Wrap Machine</option><option value="Case Taping Machine">Case Taping Machine</option><option value="Check Weigher with Rejection System">Check Weigher with Rejection System</option><option value="Rope Conveyor">Rope Conveyor</option><option value="Take Off Conveyor">Take Off Conveyor</option><option value="Elevated Conveyor">Elevated Conveyor</option></select></div><div class="col-sm-3"><input type="text" name="yes_secondary_pack_qty[]" class="form-control"></div><div class="col-sm-1"><button type="button" name="add" class="btn_remove  btn btn-danger" id="' + i + '" style="padding: 0px !important; height: 21px !important; font-size: 13px !important; width: 21px;"><i class="fa fa-close"></i></button></div></div>');

                // // showhidedfbox();
 

                i++;
            });
 
 
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id+'').remove();
 });
</script>
    </script>

    <script src="<?php echo assets_url; ?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>

   

 
</body>

</html>