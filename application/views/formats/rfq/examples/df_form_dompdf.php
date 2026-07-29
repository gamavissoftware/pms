<?php
// $safeDate is callable passed from controller
$fmt = isset($safeDate) ? $safeDate : function($v,$f='d-m-Y'){ return ''; };

function esc($v){
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

// Build “Product Packed” output like your TCPDF
$productPackedLine = trim($product_echo);
if(!empty($powder_option)) $productPackedLine .= ' --> '.$powder_option;
if(!empty($liquid_option)) $productPackedLine .= ' --> '.$liquid_option;

// Build “Type of Filling Unit” output like your TCPDF (with line breaks)
$fillingUnit = '';
$addLine = function($v) use (&$fillingUnit){
    $v = trim((string)$v);
    if($v !== '') $fillingUnit .= esc($v)."<br>";
};

$addLine($non_free_flow_option);
$addLine($cup_filler_option);
$addLine($free_flow_option);
$addLine($weigher_system_option);
$addLine($liner_weigher_option);
$addLine($mult_head_weigher_option);
$addLine($volumetric_cap_option);
$addLine($non_viscous_option);
$addLine($viscous_option);
$addLine($piston_filler_option);

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    @page { margin: 10px; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color:#000; }

/* Add this inside your <style> tag */
.special-notes-area table {
    width: 100% !important;
    border: 1px solid #333 !important;
    margin: 5px 0;
}
.special-notes-area table td, .special-notes-area table th {
    border: 1px solid #333 !important;
    padding: 3px;
}

    .watermark{
        position: fixed;
        top: 35%;
        left: 10%;
        font-size: 90px;
        color: #d9d9d9;
        opacity: 0.25;
        transform: rotate(-35deg);
        z-index: 0;
        width: 100%;
        text-align: center;
    }

    .content{ position: relative; z-index: 2; }

    table{
    width:100%;
    border-collapse:collapse;
    page-break-inside:auto;
}

tr{
    page-break-inside:avoid !important;
    page-break-after:auto;
}

td{
    padding:4px 6px;
    vertical-align:top;
}

tbody{
    page-break-inside:auto;
}

thead{
    display:table-header-group;
}

tfoot{
    display:table-footer-group;
}
    .t { font-size: 11.5px; }
    .t th{ background:#e5e5e5; border:1px solid #000; padding:6px; text-align:center; }
    .t td{ border:1px solid #000; padding:6px; vertical-align: top; }
    .left { text-align:left; }
    .center { text-align:center; }
    .small { font-size: 11px; }

    .sign td{
        border: none !important;
        padding: 10px 5px;
        text-align:center;
        font-size: 11px;
    }

    .pagebreak{ page-break-before: always; }

</style>
</head>
<body>

<div class="watermark">Shubham Pack</div>

<div class="content">

    <table class="t">
        <tr>
            <th style="width:50%; text-align:left;">From: Project Dept.</th>
            <th style="width:50%;">Reference DF. No.-</th>
        </tr>
        <tr>
            <td class="left">
                DF No.- <?= esc($df->design_form_name) ?><br>
                Date:- <?= esc($fmt($df->design_form_date)) ?>
            </td>
            <td class="left">
                <?= esc($df->reference_no) ?><br>
                Reference DF Date: <?= esc($fmt($df->ref_df_date)) ?>
            </td>
        </tr>
    </table>

    <table class="t" style="margin-top:6px;">
        <tr>
            <th colspan="3" style="width:75%;">Order Details For MULTI TRACK MACHINE</th>
            <th style="width:25%;">REMARKS</th>
        </tr>

        <tr>
            <td style="width:3%;" class="center">>></td>
            <td style="width:17%;" class="left">CE complied</td>
            <td style="width:55%;"><?= esc($multi->ce_complied) ?></td>
            <td style="width:25%;"><?= esc($multi->ce_complied_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">>></td>
            <td class="left">Date of PO</td>
            <td><?= esc($date_of_po) ?></td>
            <td><?= esc($multi->date_of_po_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">>></td>
            <td class="left">Penalty Clause</td>
            <td><?= esc($multi->penalty_clause) ?></td>
            <td><?= esc($multi->penalty_clause_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">>></td>
            <td class="left">Dispatch date</td>
            <td><?= esc($fmt($multi->dispatch_date)) ?></td>
            <td><?= esc($multi->dispatch_date_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">>></td>
            <td class="left">Trial Date</td>
            <td><?= esc($fmt($multi->trial_date)) ?></td>
            <td><?= esc($multi->trial_date_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">>></td>
            <td class="left">Machine Model No.</td>
            <td><?= esc($machinemodel) ?></td>
            <td><?= esc($multi->machine_mode_no_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">>></td>
            <td class="left">Machine Type</td>
            <td><?= esc($machine_type) ?></td>
            <td><?= esc($multi->machine_type_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">>></td>
            <td class="left">Machine Orientation</td>
            <td><?= esc($multi->machine_orientation) ?></td>
            <td><?= esc($multi->machine_orientation_remarks) ?></td>
        </tr>
    </table>

    <table class="t" style="margin-top:6px;">
        <tr>
            <th colspan="3" style="width:75%;">Machine Specification</th>
            <th style="width:25%;">REMARKS</th>
        </tr>

        <tr>
            <td style="width:3%;" class="center">1</td>
            <td style="width:17%;" class="left">No. of Tracks</td>
            <td style="width:55%;"><?= esc($no_of_track) ?></td>
            <td style="width:25%;"><?= esc($spec->tracks_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">2</td>
            <td class="left">Product to be Packed</td>
            <td><?= $productPackedLine !== '' ? $productPackedLine : '-' ?></td>
            <td><?= esc($spec->product_packed_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">3</td>
            <td class="left">Type Of Filling Unit</td>
            <td><?= $fillingUnit !== '' ? $fillingUnit : '-' ?></td>
            <td><?= esc($spec->filling_unit_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">4</td>
            <td class="left">Product Specification (Viscosity / Density)</td>
            <td><?= $pro !== '' ? $pro : '-' ?></td>
            <td><?= esc($spec->product_specification_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">5</td>
            <td class="left">Pouch Size (W x L x H) in mm</td>
            <td>
                <?php
                if(!empty($qty_data) && !empty($qty_data['qty'])){
                    for($r=0;$r<count($qty_data['qty']);$r++){
                        $w = isset($qty_data['width'][$r]) ? $qty_data['width'][$r] : '';
                        $l = isset($qty_data['length'][$r])? $qty_data['length'][$r]: '';
                        $h = isset($qty_data['height'][$r])? $qty_data['height'][$r]: '';
                        if($w!=='' && $l!==''){
                            if(!empty($h) && (int)$h>0){
                                echo esc($w).' X '.esc($l).' X '.esc($h).'<br>';
                            }else{
                                echo esc($w).' X '.esc($l).'<br>';
                            }
                        }
                    }
                }else{
                    echo '-';
                }
                ?>
            </td>
            <td><?= esc($pouch_size_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">6</td>
            <td class="left">Quantity to be packed</td>
            <td>
                <?php
                if(!empty($qty_data) && !empty($qty_data['qty'])){
                    for($r=0;$r<count($qty_data['qty']);$r++){
                        $q = isset($qty_data['qty'][$r]) ? $qty_data['qty'][$r] : '';
                        $u = isset($qty_data['unit'][$r])? $qty_data['unit'][$r]: '';
                        if($q!==''){
                            echo esc($q).' '.esc($u).'<br>';
                        }
                    }
                }else{
                    echo '-';
                }
                ?>
            </td>
            <td><?= esc($quantity_packed_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">7</td>
            <td class="left">Profile of Sealing</td>
            <td><?= esc($typeofsealing) ?></td>
            <td><?= esc($spec->profle_sealing_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">8</td>
            <td class="left">Provision of Notching</td>
            <td><?= esc($m1->notching_option) ?></td>
            <td><?= esc($m1->notching_option_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">9</td>
            <td class="left">Hopper Details</td>
            <td class="left">
                <?php
                $hopper = trim($m1->hooper_details);
                $chain = [];
                if(trim($m1->openable_option)!='') $chain[] = trim($m1->openable_option);
                if(trim($m1->pressurised_option)!='') $chain[] = trim($m1->pressurised_option);
                if(trim($m1->non_pressurised_option)!='') $chain[] = trim($m1->non_pressurised_option);
                if(trim($m1->closed_option)!='') $chain[] = trim($m1->closed_option);
                if(trim($m1->closed_pressurised_option)!='') $chain[] = trim($m1->closed_pressurised_option);
                if(trim($m1->non_closed_pressurised_option)!='') $chain[] = trim($m1->non_closed_pressurised_option);

                $out = $hopper;
                foreach($chain as $c){
                    $out .= ($out!=='' ? ' --> ' : '').$c;
                }
                echo $out!=='' ? nl2br(esc($out)) : '-';
                ?>
            </td>
            <td><?= esc($m1->hooper_details_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">10</td>
            <td class="left">Cladding Provision</td>
            <td><?= esc($m1->cladding_provision) ?></td>
            <td><?= esc($m1->cladding_provision_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">11</td>
            <td class="left">Provision of Embossing coding</td>
            <td class="left">
                <?php if($m1->embossing === 'Yes'): ?>
                    <?= esc($m1->embossing) ?> | <?= esc($m1->embossing_option) ?> | <?= esc(trim($m1->linear_option.' '.$m1->rotary_option)) ?>
                <?php else: ?>
                    <?= esc($m1->embossing) ?>
                <?php endif; ?>
            </td>
            <td><?= esc($m1->provision_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">12</td>
            <td class="left">Web Aligner</td>
            <td class="left">
                <?php if($m1->web_aligner === 'Yes'): ?>
                    <?= esc($m1->web_aligner) ?> <?= $web_aligner_option_name ? ' | '.esc($web_aligner_option_name) : '' ?>
                <?php else: ?>
                    <?= esc($m1->web_aligner) ?>
                <?php endif; ?>
            </td>
            <td><?= esc($m1->web_aligner_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">13</td>
            <td class="left">Center Slitting</td>
            <td><?= esc($m2->center_slitting) ?></td>
            <td><?= esc($m2->center_slitting_remarks) ?></td>
        </tr>
    
        <tr>
            <td style="width:3%;" class="center">14</td>
            <td style="width:17%;" class="left">Vertical Slitter</td>
            <td style="width:55%;"><?= esc($m2->vertical_slitting) ?></td>
            <td style="width:25%;"><?= esc($m2->vertical_slitting_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">15</td>
            <td class="left">Vertical Sealer Drive</td>
            <td><?= esc($m2->vertical_sealer) ?></td>
            <td><?= esc($m2->vertical_sealer_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">16</td>
            <td class="left">Laminate Pulling Drive</td>
            <td><?= esc($m2->laminate_pulling) ?></td>
            <td><?= esc($m2->laminate_pulling_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">17</td>
            <td class="left">Up & Down</td>
            <td><?= esc($m2->up_down) ?></td>
            <td><?= esc($m2->up_down_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">18</td>
            <td class="left">Embossing Coding Drive</td>
            <td><?= esc($m2->embossing_coding) ?></td>
            <td><?= esc($m2->embossing_coding_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">19</td>
            <td class="left">Cooling Station Drive</td>
            <td><?= esc($m2->cooling_station) ?></td>
            <td><?= esc($m2->cooling_station_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">20</td>
            <td class="left">Horizontal Sealer Drive</td>
            <td><?= esc($m2->horizontal_sealer) ?></td>
            <td><?= esc($m2->horizontal_sealer_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">21</td>
            <td class="left">Perforation Blade Drive</td>
            <td><?= esc($m2->perforation_blade) ?></td>
            <td><?= esc($m2->perforation_blade_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">22</td>
            <td class="left">Piston Movement</td>
            <td><?= esc($m2->priston_drive) ?></td>
            <td><?= esc($m2->priston_drive_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">23</td>
            <td class="left">Shut off Nozzle Drive</td>
            <td><?= esc($m2->shutt_off_nozzle) ?></td>
            <td><?= esc($m2->shutt_off_nozzle_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">24</td>
            <td class="left">Dosing movement</td>
            <td><?= esc($m2->filling_plate_drive) ?></td>
            <td><?= esc($m2->filling_plate_drive_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">25</td>
            <td class="left">Individual Weight Adjustment</td>
            <td><?= esc($m2->individual_weight) ?></td>
            <td><?= esc($m2->individual_weight_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">26</td>
            <td class="left">Overall Weight Adjustment</td>
            <td><?= esc($m2->overall_weight_adjust) ?></td>
            <td><?= esc($m2->overall_weight_adjust_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">27</td>
            <td class="left">Traverse Drive</td>
            <td class="left">
                <?php if($m3->traverse_drive === 'Yes'): ?>
                    <?= esc($m3->traverse_drive) ?> | <?= esc($m3->yes_traverse_drive) ?>
                <?php else: ?>
                    <?= esc($m3->traverse_drive) ?>
                <?php endif; ?>
            </td>
            <td><?= esc($m3->traverse_drive_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">28</td>
            <td class="left">Printer</td>
            <td class="left">
                <?php if($m3->printer_yes_no === 'Yes'): ?>
                    <?= esc($m3->printer_yes_no) ?> | <?= esc(trim($m3->inkjet_option.' '.$m3->tto_option.' '.$m3->thermal_inkjet_option)) ?>
                <?php else: ?>
                    <?= esc($m3->printer_yes_no) ?>
                <?php endif; ?>
            </td>
            <td><?= esc($m3->printer_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">29</td>
            <td class="left">Case Packer Drive</td>
            <td><?= esc($m3->case_packer_drive) ?></td>
            <td><?= esc($m3->case_packer_drive_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">30</td>
            <td class="left">Nozzle/Funnel</td>
            <td class="left">
                <?= esc($m3->nozzle_funnel) ?>
                <?php
                    $nf = trim($m3->powder_option1.' '.$m3->liquid_option1.' '.$m3->liquid_shut_option1);
                    if($nf!='') echo ' | '.esc($nf);
                ?>
            </td>
            <td><?= esc($m3->nozzle_funnel_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">31</td>
            <td class="left">Hose Pipe (type)</td>
            <td><?= esc($m3->hose_pipe) ?></td>
            <td><?= esc($m3->hose_pipe_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">32</td>
            <td class="left">Batch Cut Format</td>
            <td class="left">
                <?php if($batchcut === 'String'): ?>
                    <?= esc($batchcut) ?> | <?= esc($m3->string_option) ?>
                <?php else: ?>
                    <?= esc($batchcut) ?>
                <?php endif; ?>
            </td>
            <td><?= esc($m3->batch_cut_format_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">33</td>
            <td class="left">Horizontal Sealer Width</td>
            <td><?= esc($horizontal_sealing_width1) ?>mm</td>
            <td><?= esc($m4->horizontal_sealer1_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">34</td>
            <td class="left">Vertical Sealer Width</td>
            <td><?= esc($vertical_sealing_width1) ?>mm</td>
            <td><?= esc($m4->vertical_sealer1_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">35</td>
            <td class="left">Rotary Valve Coating</td>
            <td><?= esc($m4->rotary_valve_coating) ?></td>
            <td><?= esc($m4->rotary_valve_coating_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">36</td>
            <td class="left">Working Speed</td>
            <td style="background:#e5e5e5;"><?= esc($m4->working_speed) ?></td>
            <td><?= esc($m4->working_speed_remarks) ?></td>
        </tr>
        <tr>
            <td class="center">37</td>
            <td class="left">Reel Shaft Type</td>
            <td ><?= esc($m4->reel_shaft_type) ?></td>
            <td><?= esc($m4->reel_shaft_type_remarks) ?></td>
        </tr>

         <tr>
            <td class="center">38</td>
            <td class="left">Reel Core Diameter</td>
            <td ><?= esc($m4->reel_core_diameter) ?></td>
            <td><?= esc($m4->reel_core_diameter_remarks) ?></td>
        </tr>

        

        <tr>
            <td class="center">39</td>
            <td class="left">Trial Material / Product</td>
            <td><?= esc($m4->trial_material) ?></td>
            <td><?= esc($m4->trial_material_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">40</td>
            <td class="left">Laminate Details</td>
            <td><?= esc($m4->laminate_detail) ?></td>
            <td><?= esc($m4->laminate_detail_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">41</td>
            <td class="left">Heater Control System</td>
            <td><?= esc($m4->heater_control_system) ?></td>
            <td><?= esc($m4->heater_control_system_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">42</td>
            <td class="left">Beacon Lights</td>
            <td><?= esc($m4->beacon_light) ?></td>
            <td><?= esc($m4->beacon_light_remarks) ?></td>
        </tr>

         <tr>
            <td class="center">43</td>
            <td class="left">Hopper Level Sensor</td>
            <td><?= esc($m4->hooper_level) ?> <?php if($m4->hooper_level=='Yes'){?> | <?= esc($m4->hooper_level_option) ?><?php }?></td>
            <td><?= esc($m4->hooper_level_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">44</td>
            <td class="left">Safety Relay + Safety Switch</td>
            <td><?= esc($m4->safety_relay) ?></td>
            <td><?= esc($m4->safety_relay_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">45</td>
            <td class="left">Supply Voltage</td>
            <td><?= esc($power_supply) ?></td>
            <td><?= esc($m4->supply_voltage_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">46</td>
            <td class="left">PLC Make</td>
            <td><?= esc($m4->plc_maker) ?></td>
            <td><?= esc($m4->plc_maker_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">47</td>
            <td class="left">HMI Size</td>
            <td><?= esc($m4->hmi_size) ?></td>
            <td><?= esc($m4->hmi_size_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">48</td>
            <td class="left">CIP System</td>
            <td class="left">
                <?php if($m4->cip_system === 'Yes'): ?>
                    <?= esc($m4->cip_system) ?> | <?= esc($m4->cip_system_option) ?>
                <?php else: ?>
                    <?= esc($m4->cip_system) ?>
                <?php endif; ?>
            </td>
            <td><?= esc($m4->cip_system_option_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">49</td>
            <td class="left">Tool Kit</td>
            <td><?= esc($m4->tool_kit) ?></td>
            <td><?= esc($m4->tool_kit_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">50</td>
            <td class="left">Changeover Parts</td>
            <td><?= esc($m4->changeover_part) ?></td>
            <td><?= esc($m4->changeover_part_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">51</td>
            <td class="left">Secondary Packaging Solution</td>
            <td class="left">
                <?= esc($m5->secondary_pack) ?>
                <?php
                if(!empty($secPackRows)){
                    echo "<br><span class='small'>";
                    foreach($secPackRows as $r){
                        $name = isset($r->yes_secondary_pack) ? $r->yes_secondary_pack : '';
                        $qty  = isset($r->yes_secondary_pack_qty) ? $r->yes_secondary_pack_qty : '';
                        if($name!=''){
                            echo esc($name);
                            if($qty!='') echo ' - '.esc($qty);
                            echo "<br>";
                        }
                    }
                    echo "</span>";
                }
                ?>
            </td>
            <td><?= esc($m5->secondary_pack_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">52</td>
            <td class="left">Ladder & Platform</td>
            <td><?= esc($m5->ladder_platform) ?></td>
            <td><?= esc($m5->ladder_platform_remarks) ?></td>
        </tr>

        <tr>
            <td class="center">53</td>
            <td class="left">Machine Guarding</td>
            <td class="left">
                <?= esc($m5->machine_guarding) ?>
                <?php
                    if($m5->machine_guarding === 'Aluminium' && !empty($m5->aluminium_option)) echo ' | '.esc($m5->aluminium_option);
                    if($m5->machine_guarding === 'SS-304' && !empty($m5->ss_304_option)) echo ' | '.esc($m5->ss_304_option);
                ?>
            </td>
            <td><?= esc($m5->machine_guarding_remarks) ?></td>
        </tr>

      <tr>
    <td class="center">54</td>
    <td class="left">SPECIAL NOTES</td>
    <td colspan="2" class="left special-notes-area">
        <?= $special_notes ?>
    </td>
</tr>

    </table>

    <div class="pagebreak"></div>

   <table width="100%" border="1" cellspacing="0" cellpadding="0"
style="table-layout:fixed; border-collapse:collapse;">

    <!-- FREEZE 6 COLUMN GRID -->
    <colgroup>
        <col style="width:16.66%">
        <col style="width:16.66%">
        <col style="width:16.66%">
        <col style="width:16.66%">
        <col style="width:16.66%">
        <col style="width:16.66%">
    </colgroup>

    <!-- HEADING -->
    <tr>
        <td colspan="6" style="text-align:center; font-weight:bold;">
            Reviewed By:
        </td>
    </tr>

    <!-- TOP ROW : 6 = 16.66% EACH -->
    <tr>

    <?php
$po_id = $this->uri->segment(3);

$this->db->select('poreceived.added_by, system_users.first_name, system_users.last_name');
$this->db->from('poreceived');
$this->db->join('system_users', 'system_users.user_id = poreceived.added_by', 'left');
$this->db->where('poreceived.id', $po_id);

$query = $this->db->get()->row();
?>

        <td style="height:130px; vertical-align:bottom; text-align:center; padding-bottom:5px;">
 <div>
        <?php 
        if(!empty($query)){
            echo ucwords(strtolower($query->first_name . ' ' . $query->last_name));
        } else {
            echo 'N/A';
        }
        ?>
    </div>
            Project/Marketing Dept.
        </td>

        <td style="height:130px; vertical-align:bottom; text-align:center; padding-bottom:5px;">
            Head Design Dept.
        </td>

        <td style="height:130px; vertical-align:bottom; text-align:center; padding-bottom:5px;">
            Trial Dept.
        </td>

        <td style="height:130px; vertical-align:bottom; text-align:center; padding-bottom:5px;">
            Production/ PPC Dept.
        </td>

        <td style="height:130px; vertical-align:bottom; text-align:center; padding-bottom:5px;">
            Purchase Dept.
        </td>

        <td style="height:130px; vertical-align:bottom; text-align:center; padding-bottom:5px;">
           Operations-Plant head
        </td>

    </tr>

    <!-- BOTTOM ROW : colspan=2 = 33.33% EACH -->
    <tr>

        <td colspan="2"
        style="height:130px; vertical-align:bottom; text-align:center; padding-bottom:5px;">
            Mr. Rishabh Sharma<br>
            Executive Director - Design & Development
        </td>

        <td colspan="2"
        style="height:130px; vertical-align:bottom; text-align:center; padding-bottom:5px;">
            Mr. Shubham Sharma<br>
            Executive Director - Business Development
        </td>

        <td colspan="2"
        style="height:130px; vertical-align:bottom; text-align:center; padding-bottom:5px;">
            Mr. Virendra Sharma<br>
            Chairman & Managing Director
        </td>

    </tr>

</table>

</div>
</body>
</html>
