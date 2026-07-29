<?php
$CI = &get_instance();
$lead_id = $this->uri->segment(5);
$CI->load->model('Salescrm_model', 'salescrm');

$record_id = $this->salescrm->getRecordID($lead_id);

// echo $record_id; exit;

$annexture_1 = $this->salescrm->getannexture_1($record_id);
// echo "<pre>"; print_r($annexture_1); exit;
if (count($annexture_1) > 0) {
    $product_to_be_packed = $annexture_1[0];
    $batchcut = $annexture_1[16];
    $liquidviscositydata = $annexture_1[10];
    $powderdensitydata = $annexture_1[12];
    $qty_data = $this->salescrm->getQtyPackedData($record_id);
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


$getannexture_2 = $this->salescrm->getannexture_2($record_id);
// echo "<pre>"; print_r($getannexture_2); exit;
if (count($getannexture_2) > 0) {
    $no_of_track = $getannexture_2[3];
    $machinemodel = $getannexture_2[0];
} else {
    $no_of_track = '';
    $machinemodel = '';
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

        .required_block{
                border-left: 3px solid #c00;
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
                    
        $q = $this->db->select('podate,df_number')->from('poreceived')->where('lead_id', $lead_id)->where('id',$this->uri->segment(4))->get();

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

                    <p style="border-left: 3px solid #c00; color:#000;"><span style="color:red;">*</span> <b>= Required Information</b></p>
                    <div class="form_box">
                        <form action="<?php echo page_url; ?>Dashboard/add_design_form_600/<?php echo $this->uri->segment(3); ?>/<?php echo $this->uri->segment(4); ?>/<?php echo $this->uri->segment(5); ?>" enctype="multipart/form-data" method="post">
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
                                        <b>
                                            <input text="type" name="design_form_name" class="form-control" value="DF-<?php echo $df_number;?>" readonly></b><br>
                                        <b>Date:</b><input type="date" class="form-control required_block" name="design_form_date" style="text-align: center;" min="<?php echo date('Y-m-d');?>" required></b></p>
                                    </td>
                                    <td class="text-center" rowspan="9" style="vertical-align: middle;">
                                        <input type="text" name="reference_no" class="form-control required_block" required>
                                        <p><b>Reference DF Date:<br><input type="date" class="form-control required_block" name="ref_df_date" max="<?php echo date('Y-m-d'); ?>" required></b></p>
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
                                       <input type="text" class="form-control" name="bom_no">
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="bom_no_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center">>></td>
                                    <td>Date of PO</td>
                                   
                                    <td width="50%" class="text-center">
                                       <?php echo $podate; ?>
                                    </td>
                                    <?php ?>
                                    <td><input type="text" class="form-control" name="date_of_po_remarks"></td>
                                </tr>
                             
                                <tr>
                                    <td class="text-center">>></td>
                                    <td>Dispatch date/Remarks</td>
                                    <td width="50%" class="text-center">
                                        
                                    <input type="date" class="form-control" value="<?php echo $task_103_date;?>" name="dispatch_date" readonly>
                                   
                                
                                </td>
                                    <td><input type="text" class="form-control" name="dispatch_date_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center">>></td>
                                    <td>Trial Date</td>

                                    <td width="50%" class="text-center">

                                    <input type="date" class="form-control" value="<?php echo $task_52_date;?>" name="trial_date" readonly>
                                      
                                    </td>

                                    <td><input type="text" class="form-control" name="trial_date_remarks"></td>
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
                                        
                                      <input type="text" name="machine_qty" class="form-control required_block" value="" required>

                                </td>
                                    <td><input type="text" class="form-control" name="machine_qty_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center">>></td>
                                    <td>Model No.</td>
                                    <td width="50%" class="text-center">
                                        
                                    <?php echo $machinemodel; ?>
                                    </td>
                                    <td><input type="text" class="form-control" name="machine_mode_no_remarks"></td>
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
                                    <td><input type="text" class="form-control" name="tracks_remarks"></td>
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
                                    <td><input type="text" class="form-control" name="product_packed_remarks"></td>
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
                                    <td class="text-center" width="3%" style="vertical-align: middle;">3</td>
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
                                    <td width="25%" class="text-center" ><input name="quantity_packed_remarks" class="form-control" id=""></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%" style="vertical-align: middle;">4</td>
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

                                    <td width="25%" class="text-center" ><input name="pouch_size_remarks" class="form-control" id=""></td>
                                </tr>
                                  <tr>
                                    <td class="text-center" width="3%">5</td>
                                    <td width="17%">Horizontal Profile of Sealing</td>
                                    <td width="50%">
                                         <p><?php echo $typeofsealing; ?></p>
                                    </td>
                                    <td width="25%">
                                        <input type="text" name="profle_sealing_remarks" class="form-control">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">6</td>
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
                                    <td><input type="text" class="form-control" name="filling_unit_remarks"></td>
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
                                    <td class="text-center" width="3%">7</td>
                                    <td width="17%">Provision of Notching</td>
                                     <td width="50%" class="text-center">
                                        <select name="notching_option" id="" class="form-control required_block" required>
                                            <option value="">--Select--</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                     </td>
                                    <td width="25%"><input type="text" class="form-control" name="notching_option_remarks"></td>
                                </tr>
                                  <tr>
                                    <td class="text-center" width="3%">8</td>
                                    <td width="17%">Filling Tank</td>
                                     <td width="50%" class="text-center">
                                        <select name="filling_tank" id="filling_tank" class="form-control required_block" required>
                                            <option value="">--Select--</option>
                                            <option value="Header">Header</option>
                                            <option value="Hopper">Hopper</option>
                                            <option value="NA">NA</option>
                                        </select>

                                        <select name="header_option" id="header_option" class="form-control" style="display: none;">
                                            <option value="">--Select--</option>
                                            <option value="Jacketed">Jacketed</option>
                                            <option value="Non-Jacketed">Non-Jacketed</option>
                                            <option value="NA">NA</option>
                                        </select>
                                     </td>
                                    <td width="25%"><input type="text" class="form-control" name="filling_tank_remarks"></td>
                                </tr>
                            </table>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">9</td>
                                    <td width="17%">Hopper Type</td>
                                    <td width="50%" class="text-center">
                                        <select name="hooper_details" id="hooper_details" class="form-control required_block" required style="display: block;">
                                            <option value="NA">NA</option>
                                        </select>

                                       
                                        <div id="openable" style="display: none;">
                                            <select name="openable_option" id="openable_option" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Pressurised">Pressurised</option>
                                                <option value="Non-Pressurised / Normal">Non-Pressurised / Normal</option>
                                            </select>
                                        </div>
                                       

                                        <!-- <div id="closed" style="display: none;">
                                            <br>
                                            <select name="closed_option" id="closed_option" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Pressurised">Pressurised</option>
                                                <option value="Non-Pressurised / Normal">Non-Pressurised / Normal</option>
                                            </select>
                                        </div> -->

                                      
                                        <div id="pressurised" style="display: none;">
                                            <br>
                                            <select name="pressurised_option" id="pressurised_option" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Jacketed">Jacketed</option>
                                                <option value="Non-Jacketed">Non-Jacketed</option>
                                            </select>
                                        </div>
                                      

                                        <!-- <div id="closed_pressurised" style="display: none;">
                                            <br>
                                            <select name="closed_pressurised_option" id="closed_pressurised_option" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Jacketed">Jacketed</option>
                                                <option value="Non-Jacketed">Non-Jacketed</option>
                                            </select>
                                        </div> -->

                                       
                                        <div id="non_pressurised" style="display: none;">
                                            <br>
                                            <select name="non_pressurised_option" id="non_pressurised_option" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Jacketed">Jacketed</option>
                                                <option value="Non-Jacketed">Non-Jacketed</option>
                                            </select>
                                        </div>
                                      

                                        <!-- <div id="non_closed_pressurised" style="display: none;">
                                            <br>
                                            <select name="non_closed_pressurised_option" id="non_closed_pressurised_option" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Jacketed">Jacketed</option>
                                                <option value="Non-Jacketed">Non-Jacketed</option>
                                            </select>
                                        </div> -->
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="hooper_details_remarks"></td>
                                </tr>


                               <tr>
                                    <td class="text-center" width="3%">10</td>
                                    <td width="17%">Provision of Coding</td>
                                     <td width="50%" class="text-center">
                                        <select name="provision_coding" id="provision_coding" class="form-control required_block" required>
                                            <option value="">--Select--</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>


                                        <div id="provision_coding_option" style="display: none;">
                                            <br>
                                            <p>Hinge Type</p>

                                            <select name="provision_coding_option" id="provision_coding_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="BRADMA">BRADMA</option>
                                                <option value="SHUBHAM STD.">SHUBHAM STD.</option>
                                            </select>

                                        </div>

                                     </td>
                                    <td width="25%"><input type="text" class="form-control" name="provision_coding_remarks" placeholder="Digit Size"></td>
                                </tr>

                                <script>
                                     $(document).ready(function() {

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
                                

                            </table>

                            <!-- <script>
                                $(document).ready(function() {

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
                            </script> -->
                           
                           

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
                                    <td class="text-center" width="3%">11</td>
                                    <td width="17%">


<?php echo $row->name;?>

  


                                    </td>
                                    <td width="50%" class="text-center">
<select name="web_aligner" id="web_aligner" class="form-control required_block" required>
    <option value="">--Select--</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
</select>

<div id="web_aligner_option" style="display: none;">
     <br>
                                        <select name="web_aligner_option" id="web_aligner_option_select" class="form-control" style="display:block;">
                                            <option value="">--Select--</option>
                                            <!-- <option value="ELECTRONIC">ELECTRONIC</option>
                                            <option value="Make - E + L">Make - E + L</option>
                                             <option value="RE">RE</option>
                                              <option value="NA">NA</option>


                                               <option value="">Select</option> -->
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
    <option value="<?php echo $row1->id;?>" <?php if($row1->id==$valueid){ ?> selected <?php } ?>><?php echo $row1->name;?></option>
    <?php 
        } 
        } 
        ?>
                                            
                                        </select>
</div>
                                    </td>
                                   
                                    <td width="25%"><input type="text" class="form-control" name="web_aligner_remarks"></td>
                                </tr>
                            </table>
<?php } } ?>

                            <script>
                                $(document).ready(function() {

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
                                    <td class="text-center" width="3%">12</td>
                                    <td width="17%">Center Slitting</td>
                                    <td width="50%" class="text-center">
                                        <select name="center_slitting" id="" class="form-control required_block" required>
                                            <option value="">--Select--</option>
                                            <option value="Rotary">Rotary</option>
                                            <option value="Normal Blade">Normal Blade</option>
                                            <option value="Surgical Blade">Surgical Blade</option>
                                            <option value="NA">NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="center_slitting_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">13</td>
                                    <td width="17%">Vertical Slitter</td>
                                    <td width="50%" class="text-center">
                                        <select name="vertical_slitting" id="" class="form-control required_block" required>
                                            <option value="">--Select--</option>
                                           <option value="Rotary">Rotary</option>
                                            <option value="Blade">Blade</option>
                                            <option value="NA">NA</option>
                                             <!-- <option value="Normal Blade">Normal Blade</option>
                                            <option value="Surgical Blade">Surgical Blade</option> -->
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="vertical_slitting_remarks"></td>
                                </tr>
                                 <tr>
                                    <td class="text-center" width="3%">14</td>
                                    <td width="17%">Pulling Machine</td>
                                    <td width="50%" class="text-center">
                                        <select name="pulling_machine" id="" class="form-control required_block" required>
                                            <option value="">--Select--</option>
                                            <option value="Servo">Servo</option>
                                            <option value="Clutch Brake">Clutch Brake</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="pulling_machine_remarks"></td>
                                </tr>
                                 
                               
                            </table>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">15</td>
                                    <td width="17%">Nozzle/Funnel</td>
                                    <td width="50%" class="text-center" >
                                        <select name="nozzle_funnel" id="nozzle_funnel" class="form-control required_block" required>
                                            <option value="">--Select--</option>
                                            <option value="Powder">Powder</option>
                                            <option value="Liquid - Capillary">Liquid - Capillary</option>
                                            <option value="Liquid - Shut off Nozzle">Liquid - Shut off Nozzle</option>
                                            <option value="NA">NA</option>
                                        </select>

                                        <div id="powder_option1" style="display: none;">
                                            <br>
                                            <select name="powder_option1" id="powder_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Oval">Oval</option>
                                                <option value="Round">Round</option>
                                                <option value="NA">NA</option>
                                            </select>
                                        </div>

                                        <div id="liquid_option1" style="display: none;">
                                            <br>
                                            <select name="liquid_option1" id="liquid_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Round">Round</option>
                                                <option value="Elliptical">Elliptical</option>
                                                <option value="NA">NA</option>
                                            </select>
                                        </div>

                                        <div id="liquid_shut_option1" style="display: none;">
                                            <br>
                                            <select name="liquid_shut_option1" id="liquid_shut_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Rhombus">Rhombus</option>
                                                <option value="Round">Round</option>
                                                <option value="NA">NA</option>
                                            </select>
                                        </div>

                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="nozzle_funnel_remarks"></td>
                                </tr>

                                <script>
                                    $(document).ready(function() {

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

                                  <tr>
                                    <td class="text-center" width="3%">16</td>
                                    <td width="17%">Hose Pipe</td>
                                    <td width="50" class="text-center">
                                        <select name="hose_pipe" id="hose_pipe" class="form-control required_block" required>
                                            <option value="">--Select--</option>
                                            <option value="Threaded">Threaded</option>
                                            <option value="Tri-Clamp">Tri-Clamp</option>
                                            <option value="NA">NA</option>
                                        </select>
                                    </td>

                                    <td width="25%"><input type="text" class="form-control" name="hose_pipe_remarks"></td>
                                </tr>
                                  <tr>
                                    <td class="text-center" width="3%">17</td>
                                    <td width="17%">Horizontal Sealing</td>
                                    <td width="50%" class="text-center">
                                        <select name="horizontal_sealer" id="" class="form-control required_block" required>
                                            <option value="">--Select--</option>
                                            <option value="Servo Motor Driven">Servo Motor Driven</option>
                                            <option value="CAM Driven">CAM Driven</option>
                                            <option value="Pneumatic">Pneumatic</option>
                                            <option value="NA">NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="horizontal_sealer_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">18</td>
                                    <td width="17%">Vertical Sealing</td>
                                    <td width="50%" class="text-center">
                                        <select name="vertical_sealer" id="" class="form-control required_block" required>
                                            <option value="">--Select--</option>
                                            <option value="Servo Motor Driven">Servo Motor Driven</option>
                                            <option value="CAM Driven">CAM Driven</option>
                                            <option value="Pneumatic">Pneumatic</option>
                                            <option value="NA">NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="vertical_sealer_remarks"></td>
                                </tr>
                                 <tr>
                                    <td class="text-center" width="3%">19</td>
                                    <td width="17%">Piston Movement</td>
                                    <td width="50%" class="text-center">
                                        <select name="priston_drive" id="" class="form-control required_block" required>
                                            <option value="">--Select--</option>
                                            <option value="Servo Motor Driven">Servo Motor Driven</option>
                                            <option value="CAM Driven">CAM Driven</option>
                                            <option value="Pneumatic">Pneumatic</option>
                                            <option value="NA">NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="priston_drive_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">20</td>
                                    <td width="17%">Valve Movement</td>
                                    <td width="50%" class="text-center">
                                        <select name="valve_movement" id="" class="form-control required_block" required>
                                            <option value="">--Select--</option>
                                            <option value="Servo Motor Driven">Servo Motor Driven</option>
                                            <option value="CAM Driven">CAM Driven</option>
                                            <option value="Pneumatic">Pneumatic</option>
                                            <option value="NA">NA</option>

                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="valve_movement_remarks"></td>
                                </tr>
                            </table>
                          

                            <table class="table">
                              
                                <tr>
                                    <td class="text-center" width="3%">21</td>
                                    <td width="17%">Total Width of Horizontal Sealer</td>
                                    <td width="50%" class="text-center">
                                       

                                        <p><?php echo $horizontal_sealing_width;?>mm</p>
                                      
                                        
                                    
                                    </td>
                                    <td width="25%"style="position: relative;">
                                        <textarea name="horizontal_sealer1_remarks" id="" class="form-control text--box"></textarea>
                                    </td>
                                </tr>
                               
                            </table>
                            <table class="table"> 
                               
                                <tr>
                                    <td class="text-center" width="3%">22</td>
                                    <td width="17%">Total Width of Vertical</td>
                                    <td width="50%" class="text-center" >
                                      
                                        
                                         <p><?php echo $vertical_sealing_width;?>mm</p>
                                        
                                      
                                    </td>
                                    <td width="25%" style="position: relative;">
                                        <textarea name="vertical_sealer1_remarks" id="" class="form-control text--box"></textarea>
                                    </td>
                                </tr>
                              
                            </table>
                            

                            <table class="table">

                                <tr>
                                    <td class="text-center" width="3%">23</td>
                                    <td width="17%">Working Speed</td>
                                    <td width="50%" class="text-center">
                                        <input type="text" name="working_speed" value="50 - 60 Strokes/minute" class="form-control required_block" required>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="working_speed_remarks"></td>
                                </tr>
                            </table>
                          

                            <table class="table">

                                <tr>
                                    <td class="text-center" width="3%">24</td>
                                    <td width="17%">Core Diameter</td>
                                    <td width="50%">
                                        <select name="reel_core_diameter" id="reel_core_diameter" class="form-control required_block" required>
                                            <option value="">--Select--</option>
                                            <option value="76mm">76mm</option>
                                            <option value="152mm">152mm</option>
                                            <option value="NA">NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="reel_core_diameter_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">25</td>
                                    <td width="17%">Trial Material / Product</td>
                                    <td width="50%">
                                        <select name="trial_material" id="trial_material" class="form-control required_block" required>
                                            <option value="">--Select--</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="trial_material_remarks"></td>
                                </tr>
                                 <tr>
                                    <td class="text-center" width="3%">26</td>
                                    <td width="17%">Laminate for Trial</td>
                                    <td width="50%">
                                        <select name="laminate_trial"  class="form-control required_block" required>
                                            <option value="">--Select--</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="laminate_trail_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">27</td>
                                    <td width="17%">Laminate Details</td>
                                    <td width="50%">
                                        <select name="laminate_detail" id="laminate_detail" class="form-control required_block" required>
                                            <option value="">--Select--</option>
                                            <option value="Heat Sealable laminate">Heat Sealable laminate</option>
                                           <!-- <option value="Impulse Sealer">Impulse Sealer</option>-->
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="laminate_detail_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">28</td>
                                    <td width="17%">Heater & SSR Fault Detection Box</td>
                                    <td width="50%">
                                        <select name="heater_ssr_box" id="heater_ssr_box" class="form-control required_block" required>
                                            <option value="">--Select--</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                           
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="heater_ssr_box_remarks"></td>
                                </tr>
                            </table>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">29</td>
                                    <td width="17%">Beacon Lights</td>
                                    <td width="50%" class="text-center" colspan="1">
                                        <select name="beacon_light" id="" class="form-control required_block" required>
                                            <option value="">--Select--</option>
                                             <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                            <option value="NA">NA</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="beacon_light_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">30</td>
                                    <td width="17%">Hopper Level Sensor</td>
                                    <td width="50%" class="text-center">
                                        <select id="hooper_level" name="hooper_level" class="form-control required_block" required>
                                            <option value="">--Select--</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>

                                        <div id="hooper_level_option" style="display: none;">
                                            <br>
                                            <select name="hooper_level_option" id="hooper_level_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Shubham Standard">Shubham Standard</option>
                                                <option value="Sapcon">Sapcon</option>
                                                <option value="Vega">Vega</option>
                                                <option value="Sick">Sick</option>
                                                <option value="ETH">ETH</option>
                                                <option value="NA">NA</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="hooper_level_remarks"></td>
                                </tr>

                                <script>
                                    $(document).ready(function() {

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
                                    <td width="50%" class="text-center"><select name="safety_relay" id="" class="form-control required_block" required>
                                            <option value="">--Select--</option>
                                            <option value="Yes">Yes</option>

                                            <option value="No">No</option>
                                        </select></td>
                                    <td width="25%"><input type="text" class="form-control" name="safety_relay_remarks"></td>
                                </tr>
                            </table>
                            <table class="table">
                                  
                                <tr>
                                    <td class="text-center" width="3%">32</td>
                                    <td width="17%">PLC Make</td>


                                    <td width="50%" class="text-center"><select name="plc_maker" id="" class="form-control required_block" required>
                                            <option value="">--Select--</option>
                                            <option value="Allen Bradely">Allen Bradely</option>
                                            <option value="Omron">Omron</option>
                                            <option value="Mitsubhishi">Mitsubhishi</option>
                                            <option value="NA">NA</option>
                                        </select></td>

                                    <td width="25%"><input type="text" class="form-control" name="plc_maker_remarks"></td>
                                </tr>
                                
                               <tr>
                                    <td class="text-center" width="3%">33</td>
                                    <td width="17%">Conveyor</td>


                                    <td width="50%" class="text-center">
                                        <select name="conveyor" id="conveyor" class="form-control required_block" required>
                                            <option value="">--Select--</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    
                                        <div id="conveyor_option" style="display: none;">
<br>
                                        <select name="conveyor_option_yes" id="conveyor_option_yes" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Std Conveyor">Std Conveyor</option>
                                            <option value="Collating Conveyor">Collating Conveyor</option>
                                        </select>

                                        </div>
                                    
                                    
                                    </td>

                                    <td width="25%"><input type="text" class="form-control" name="conveyor_remarks"></td>
                                </tr>

                                <script>
                                    $(document).ready(function() {

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
                                        <select name="standard" class="form-control required_block" required>
                                            <option value="">--Select--</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    
                                      
                                    </td>

                                    <td width="25%"><input type="text" class="form-control" name="standard_remarks"></td>
                                </tr>
                            </table>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">35</td>
                                    <td width="17%">Changeover Parts</td>
                                    <td width="50%" class="text-center">
                                        <select name="changeover_part" id="" class="form-control required_block" required>
                                            <option value="">--Select--</option>
                                            <option value="Yes">Yes</option>

                                            <option value="No">No</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="changeover_part_remarks"></td>
                                </tr>
                            </table>
                           
                             
                           
                            <table class="table">

                                <tr>
                                    <td class="text-center" width="3%">36</td>
                                    <td width="17%">SPECIAL NOTES</td>
                                    <td width="75%">
                                        <textarea class="form-control ckeditor" name="special_notes" id="special_notes"></textarea>
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

   
    <script>
$(document).ready(function() {

    // Step 1: Show/hide openable when filling tank changes
    $('#filling_tank').change(function() {
        var selected = $(this).val();

        // Handle Hopper selection
        if (selected === 'Hopper') {
            // Hide hooper_details select
            $('#hooper_details').fadeOut(function() {
                $(this).val('NA').prop('required', false).removeClass('required_block');
            });

            // Show openable select
            $('#openable').fadeIn();
            $('#openable_option').prop('required', true).addClass('required_block');

        } else {
            // Show hooper_details select again for other options
            $('#hooper_details').fadeIn().prop('required', true).addClass('required_block');

            // Hide openable and nested selects
            $('#openable').fadeOut(function() {
                $('#openable_option').val('').prop('required', false).removeClass('required_block');
                $('#pressurised, #non_pressurised').fadeOut(function() {
                    $('#pressurised_option, #non_pressurised_option').val('').prop('required', false).removeClass('required_block');
                });
            });
        }

        // Handle Header selection separately if needed
        if (selected === 'Header') {
            $('#header_option').fadeIn().prop('required', true).addClass('required_block');
        } else {
            $('#header_option').fadeOut(function() {
                $('#header_option').val('').prop('required', false).removeClass('required_block');
            });
        }
    });

    // Step 2: Show pressurised/non-pressurised based on openable_option
    $('#openable_option').change(function() {
        var selected = $(this).val();

        if (selected === 'Pressurised') {
            $('#pressurised').fadeIn();
            $('#pressurised_option').prop('required', true).addClass('required_block');
            $('#non_pressurised').fadeOut(function() {
                $('#non_pressurised_option').val('').prop('required', false).removeClass('required_block');
            });
        } else if (selected === 'Non-Pressurised / Normal') {
            $('#non_pressurised').fadeIn();
            $('#non_pressurised_option').prop('required', true).addClass('required_block');
            $('#pressurised').fadeOut(function() {
                $('#pressurised_option').val('').prop('required', false).removeClass('required_block');
            });
        } else {
            $('#pressurised, #non_pressurised').fadeOut(function() {
                $('#pressurised_option, #non_pressurised_option').val('').prop('required', false).removeClass('required_block');
            });
        }
    });

});
</script>



 
</body>

</html>