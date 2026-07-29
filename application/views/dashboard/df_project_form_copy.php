

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


                <div class="col-sm-12">
                    <div class="form_box">
                        <form action="" enctype="multipart/form-data" method="post">
                            <table class="table">
                                <tr>
                                    <th width="20%">From: Project Dept.</th>
                                    <th class="text-center" width="15%">REF DF. No.-</th>
                                    <th colspan="2" width="35%">To: All Departments</th>
                                    <th colspan="2" width="25%">DISPATCH DEPT. CLEARANCE</th>
                                </tr>
                                <tr>
                                    <td class="text-center" rowspan="9" style="vertical-align: middle;">
                                        <p><b>Design Form<br><input type="text" class="form-control" name="design_form_name" required><b>Date:</b><input type="date" class="form-control" name="design_form_date" required></b></p>
                                    </td>
                                    <td class="text-center" rowspan="9" style="vertical-align: middle;">
                                        <p><b>Design Form Date:<br><input type="date" class="form-control" name="ref_df_date" required></b></p>
                                    </td>
                                    <td width="10%">Design:</td>
                                    <td width="25%"><input type="text" class="form-control" name="design" required></td>
                                    <td width="12%"> IOM Nos. (Projects)</td>
                                    <td width="13%"><input type="text" class="form-control" name="iom_no" required></td>
                                </tr>
                                <tr>
                                    <td>Assembly:</td>
                                    <td><input type="text" class="form-control" name="assembly" required></td>
                                    <td>INVOICE NO.</td>
                                    <td><input type="text" class="form-control" name="invoice_no" required></td>
                                </tr>
                                <tr>
                                    <td>Trial:</td>
                                    <td><input type="text" class="form-control" name="trial" required></td>
                                    <td> INVOICE DATE</td>
                                    <td><input type="date" class="form-control" name="invoice_date" required></td>
                                </tr>
                                <tr>
                                    <td>Service:</td>
                                    <td><input type="text" class="form-control" name="service" required></td>
                                    <td rowspan="6" class="text-center" rowspan="9" style="vertical-align: middle;">
                                        <p><b>SIGN & STAMPED</b></p>
                                    </td>
                                    <td rowspan="6"></td>
                                </tr>
                                <tr>
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
                                </tr>
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
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="ce_complied_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center">>></td>
                                    <td>Date of PO</td>
                                  
                                    <td width="50%" class="text-center"><input type="date" name="date_of_po" class="form-control" value="" readonly></td>
                                    <?php ?>
                                    <td><input type="text" class="form-control" name="date_of_po_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center">>></td>
                                    <td>Penalty Clause</td>
                                    <td width="50%" class="text-center">
                                        <select name="penalty_clause" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control" name="penalty_clause_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center">>></td>
                                    <td>Dispatch date</td>
                                    <td width="50%" class="text-center"><input type="date" class="form-control" name="dispatch_date" required></td>
                                    <td><input type="text" class="form-control" name="dispatch_date_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center">>></td>
                                    <td>Trial Date</td>

                                    <td width="50%" class="text-center">
                                        <input type="date" class="form-control" name="trial_date" required>
                                    </td>

                                    <td><input type="text" class="form-control" name="trial_date_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center">>></td>
                                    <td>Machine Model No.</td>
                                    <td width="50%" class="text-center"><input type="text" name="machine_mode_no" class="form-control" value="" readonly></td>
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
                                    <td width="25%" class="text-center"><input type="text" name="tracks" class="form-control" value="" required></td>
                                    <td><input type="text" class="form-control" name="tracks_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">2</td>
                                    <td width="17%">Product to be Packed</td>
                                    <td width="25%" class="text-center">
                                        <select name="product_packed" id="product_packed" class="form-control" onchange="selectedoption()" required>
                                            <option value="">--Select--</option>
                                            <option value="1" id="liquid" >Liquid / Paste</option>
                                            <option value="2" id="powder" >Powder / Granules</option>

                                        </select>


                                        <div id="powder_option" style="display: none;">
                                            <br>
                                            <select name="powder_option" id="powder_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Free Flow">Free Flow</option>
                                                <option value="Non Free Flow">Non Free Flow</option>
                                            </select>
                                        </div>


                                        <div id="liquid_option" style="display: none;">
                                            <br>
                                            <select name="liquid_option" id="liquid_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Non-Viscous">Non-Viscous</option>
                                                <option value="Viscous">Viscous</option>
                                            </select>
                                        </div>
                                        <!---------Non-Viscous--------->
                                        <div id="non_viscous_option" style="display: none;">
                                            <br>
                                            <select name="non_viscous_option" id="non_viscous_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Soenoid Type - Electrical">Soenoid Type - Electrical</option>
                                                <option value="Pnuematic">Pnuematic</option>
                                            </select>
                                        </div>
                                        <!---------Non-Viscous--------->

                                        <!---------Viscous--------->
                                        <div id="viscous_option" style="display: none;">
                                            <br>
                                            <select name="viscous_option" id="viscous_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Piston Filler">Piston Filler</option>
                                                <option value="Flow meter">Flow meter </option>
                                            </select>
                                        </div>
                                        <!---------Viscous--------->
                                        <div id="piston_filler_option" style="display: none;">
                                            <br>
                                            <select name="piston_filler_option" id="piston_filler_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Indivisual Driven">Indivisual Driven</option>
                                                <option value="Overall Driven">Overall Driven </option>
                                            </select>
                                        </div>

                                        <div id="follow_meter_option" style="display: none;">
                                            <br>
                                            <select name="follow_meter_option" id="piston_filler_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Mass flow meter">Mass flow meter</option>
                                                <option value="Electromagnatic flow meter">Electromagnatic flow meter</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td><input type="text" class="form-control" name="product_packed_remarks"></td>
                                </tr>

                                <script>
                                    function selectedoption() {
                                        var selectedValue = $('#product_packed').val();

                                        // Reset related selects, but keep values that were set dynamically intact.
                                        $('#liquid_option_select').val('');
                                        $('#viscous_option_select').val('');
                                        $('#powder_option_select').val('');
                                        $('#free_flow_option_select').val('');
                                        $('#weigher_system_option_select').val('');
                                        $('#liner_weigher_option_select').val('');
                                        $('#mult_head_weigher_option_select').val('');
                                        $('#non_viscous_option_select').val('');
                                        $('#piston_filler_option_select').val('');

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

                                        // Show corresponding options based on the selected value
                                        if (selectedValue == '2') {
                                            $('#powder_option').show();
                                        } else if (selectedValue == '1') {
                                            $('#liquid_option').show();
                                        }
                                    }

                                    $(document).ready(function() {
                                        // Call selectedoption when the page loads to ensure initial state
                                        selectedoption();

                                        // When the product_packed selection changes
                                        $('#product_packed').change(function() {
                                            var selectedValue = $(this).val();

                                            // Reset the select boxes
                                            $('#liquid_option_select').val('');
                                            $('#viscous_option_select').val('');
                                            $('#powder_option_select').val('');
                                            $('#free_flow_option_select').val('');
                                            $('#weigher_system_option_select').val('');
                                            $('#liner_weigher_option_select').val('');
                                            $('#mult_head_weigher_option_select').val('');
                                            $('#non_viscous_option_select').val('');
                                            $('#piston_filler_option_select').val('');

                                            // Hide all options before displaying the relevant ones
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

                                            // Show corresponding options based on the selected value
                                            if (selectedValue == '2') { // Powder selected
                                                $('#powder_option').show();
                                            } else if (selectedValue == '1') { // Liquid selected
                                                $('#liquid_option').show();
                                            }
                                        });

                                        // Handle liquid_option_select change
                                        $('#liquid_option_select').change(function() {
                                            var selectedValue = $(this).val();

                                            // Reset the selects related to the liquid option
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
                                                <option value="Auger Filler System">Auger Filler System</option>
                                            </select>
                                        </div>


                                        <div id="cup_filler" style="display:none;">
                                            <select name="cup_filler_option" id="" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Multi Head Weight">Multi Head Weight </option>
                                                <option value="Belt Weigher">Belt Weigher</option>
                                            </select>
                                        </div>


                                        <!---------IF Select Free Flow--------->

                                        <div id="free_flow_option" style="display:none;">

                                            <select name="free_flow_option" id="free_flow_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Weigher System">Weigher System</option>
                                                <option value="Volumetric Cup Filler">Volumetric Cup Filler</option>
                                            </select>
                                        </div>

                                        <!---------IF Weigher System--------->

                                        <div id="weigher_system_option" style="display:none;">
                                            <br>
                                            <select name="weigher_system_option" id="weigher_system_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Liner Weigher">Liner Weigher</option>
                                                <option value="Belt Weigher">Belt Weigher</option>
                                                <option value="Multi Head Weigher">Multi Head Weigher</option>
                                            </select>
                                        </div>

                                        <!---------IF Weigher System--------->

                                        <!---------IF Liner Weigher--------->

                                        <div id="liner_weigher_option" style="display:none;">
                                            <br>
                                            <select name="liner_weigher_option" id="liner_weigher_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="2 Head">2 Head</option>
                                                <option value="4 Head">4 Head</option>

                                            </select>
                                        </div>

                                        <!---------IF Liner Weigher--------->

                                        <!---------IF Multi Head Weigher--------->

                                        <div id="mult_head_weigher_option" style="display:none;">
                                            <br>
                                            <select name="mult_head_weigher_option" id="mult_head_weigher_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="10 Head">10 Head</option>
                                                <option value="14 Head">14 Head</option>
                                                <option value="20 Head">20 Head</option>
                                            </select>
                                        </div>

                                        <!---------IF Multi Head Weigher--------->

                                        <!---------IF Volumetric Cap Filler--------->

                                        <div id="volumetric_cap_option" style="display:none;">
                                            <br>
                                            <select name="volumetric_cap_option" id="volumetric_cap_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Tilting cup filler">Tilting cup filler </option>
                                                <option value="Slide Cup Filler">Slide Cup Filler</option>
                                                <option value="Rotary Disc Cup Filler">Rotary Disc Cup Filler</option>
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
                                    <td class="text-center" width="3%" style="vertical-align: middle;">4</td>
                                    <td width="17%" style="vertical-align: middle;">Product Specification<br>(Viscosity / Density)</td>
                                    <td width="25%" class="text-center">Density - <input type="text" class="form-control" value="" name="density"></td>
                                    <td width="25%" class="text-center">Viscosity - <input type="text" class="form-control" value="" name="viscosity"></td>
                                    <td width="25%" class="text-center" style="vertical-align: middle; position: relative;"><textarea class="form-control text--box" name="product_specification_remarks"></textarea></td>
                                </tr>
                            </table>


                            <table class="table">

                                <tr>
                                    <td class="text-center" width="3%" style="vertical-align: middle;">5</td>
                                    <td width="17%" style="vertical-align: middle;">Pouch Size (W x L) in mm</td>
                                    <td width="50%" class="text-center"> 
                                                <div class="row">
                                                    <div class="col-sm-4"><input type="text" class="form-control" name="pouch_width[]" value="" readonly></div>
                                                    <div class="col-sm-4"><input type="text" class="form-control" name="pouch_length[]" value="" readonly></div>
                                                    <div class="col-sm-4"><input type="text" class="form-control" name="pouch_height[]" value="" readonly></div>
                                                </div>
                                       
                                    </td>

                                    <td width="25%" class="text-center" style="vertical-align: middle; position:relative;"><textarea name="pouch_size_remarks" class="form-control text--box" id=""></textarea></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%" style="vertical-align: middle;">6</td>
                                    <td width="17%" style="vertical-align: middle;">Quantity to be packed</td>
                                    <td width="50%" class="text-center">
                                       
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <input type="text" class="form-control" name="quantity_packed[]" value="">
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <select class="form-control qtyunitdata" name="quantity_packed_unit[]" id="editqty_unit" required onchange="selectProductPacked();">
                                                            <option value="">Select Qty</option>
                                                            <option value="ml" >ml</option>
                                                            <option value="gm" >gm</option>
                                                        </select>
                                                    </div>
                                                </div>

                                        

                                    </td>


                                    <td width="25%" class="text-center" style="vertical-align: middle; position:relative;"><textarea name="quantity_packed_remarks" class="form-control text--box" id=""></textarea></td>
                                </tr>
                            </table>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">7</td>
                                    <td width="17%">Profile of Sealing</td>
                                    <td width="50%">
                                        <select name="profile_of_sealing" id="profile_of_sealing" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Butt Sealing">Butt Sealing</option>
                                            <option value="V-Lining">V-Lining</option>
                                            <option value="knurling">Knurling</option>
                                        </select>
                                    </td>
                                    <td width="25%">
                                        <input type="text" name="profle_sealing_remarks" class="form-control">
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
                                        <select name="notching_option" id="" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                     </td>
                                    <td width="25%"><input type="text" class="form-control" name="notching_option_remarks"></td>
                                </tr>
                            </table>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">9</td>
                                    <td width="17%" rowspan="2">Hopper Details</td>
                                    <td width="50%" class="text-center">
                                        <select name="hooper_details" id="hooper_details" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Openable Type">Openable Type</option>
                                            <option value="Closed Type">Closed Type</option>
                                        </select>

                                        <!-----When i click on Openable type option this div display block---------->
                                        <div id="openable" style="display: none;">
                                            <br>
                                            <select name="openable_option" id="openable_option" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Pressurised">Pressurised</option>
                                                <option value="Non-Pressurised / Normal">Non-Pressurised / Normal</option>
                                            </select>
                                        </div>
                                        <!-----When i click on Openable type option this div display block---------->

                                        <div id="closed" style="display: none;">
                                            <br>
                                            <select name="closed_option" id="closed_option" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Pressurised">Pressurised</option>
                                                <option value="Non-Pressurised / Normal">Non-Pressurised / Normal</option>
                                            </select>
                                        </div>

                                        <!-----When i click on Pressurised option this div display block---------->
                                        <div id="pressurised" style="display: none;">
                                            <br>
                                            <select name="pressurised_option" id="pressurised_option" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Jacketed">Jacketed</option>
                                                <option value="Non-Jacketed">Non-Jacketed</option>
                                            </select>
                                        </div>
                                        <!-----When i click on Pressurised option this div display block---------->

                                        <div id="closed_pressurised" style="display: none;">
                                            <br>
                                            <select name="closed_pressurised_option" id="closed_pressurised_option" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Jacketed">Jacketed</option>
                                                <option value="Non-Jacketed">Non-Jacketed</option>
                                            </select>
                                        </div>

                                        <!-----When i click on Non-Pressurised option this div display block---------->
                                        <div id="non_pressurised" style="display: none;">
                                            <br>
                                            <select name="non_pressurised_option" id="non_pressurised_option" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Jacketed">Jacketed</option>
                                                <option value="Non-Jacketed">Non-Jacketed</option>
                                            </select>
                                        </div>
                                        <!-----When i click on Non-Pressurised option this div display block---------->

                                        <div id="non_closed_pressurised" style="display: none;">
                                            <br>
                                            <select name="non_closed_pressurised_option" id="non_closed_pressurised_option" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Jacketed">Jacketed</option>
                                                <option value="Non-Jacketed">Non-Jacketed</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="hooper_details_remarks"></td>
                                </tr>

                            </table>

                            <script>
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
                            </script>
                            <table class="table">

                                <tr>
                                    <td class="text-center" width="3%">10</td>
                                    <td width="17%">Cladding Provision</td>
                                    <td width="50%" class="text-center">
                                        <select name="cladding_provision" id="" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="cladding_provision_remarks"></td>
                                </tr>
                            </table>
                            <table class="table">

                                <tr>
                                    <td class="text-center" width="3%" rowspan="2">11</td>
                                    <td width="17%" rowspan="2">Provision of Embossing coding</td>
                                    <td width="50%" class="text-center" rowspan="2">
                                        <select name="embossing" id="embossing" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>

                                        <div id="embossing_option" style="display: none;">
                                            <br>
                                            <select name="embossing_option" id="embossing_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Linear Block Type">Linear Block Type</option>
                                                <option value="Rotary Type">Rotary Type</option>
                                            </select>
                                        </div>

                                        <div id="linear_option" style="display: none;">
                                            <br>
                                            <select name="linear_option" id="linear_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="T020">T020</option>
                                                <option value="T062">T062</option>
                                            </select>
                                        </div>

                                        <div id="rotary_option" style="display: none;">
                                            <br>
                                            <select name="rotary_option" id="rotary_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="RING TYPE">RING TYPE</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td width="25%" class="text-center" rowspan="2"><input type="text" class="form-control" name="provision_remarks"></td>
                                </tr>

                                <script>
                                    $(document).ready(function() {

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





                                <!-- <tr>
                                <td class="text-center" width="3%" rowspan="2">11</td>
                                <td width="17%" rowspan="2">Provision of Embossing coding</td>
                                <td width="8.3333%" class="text-center" rowspan="2">Yes</td>
                                <td width="8.3333%" class="text-center" rowspan="2">No</td>
                                <td width="8.3333%" class="text-center">Linear Block Type</td>
                                <td width="8.3333%" class="text-center" rowspan="2">T020</td>
                                <td width="8.3333%" class="text-center" rowspan="2">T062</td>
                                <td width="8.3333%" class="text-center" rowspan="2">RING TYPE</td>
                                <td width="25%" class="text-center" rowspan="2"><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                                <td class="text-center">Rotary Type</td>
                            </tr> -->
                            </table>

                            <!--------------------------------------------------------------->

                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">12</td>
                                    <td width="17%">Web Aligner</td>
                                    <td width="50%" class="text-center">
<select name="web_aligner" id="web_aligner" class="form-control">
    <option value="">--Select--</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
</select>

<div id="web_aligner_option" style="display: none;">
     <br>
                                        <select name="web_aligner_option" id="web_aligner_option_select" class="form-control" style="display:block;">
                                            <option value="">--Select--</option>
                                            <option value="ELECTRONIC">ELECTRONIC</option>
                                            <option value="Make - E + L">Make - E + L</option>
                                             <option value="RE">RE</option>
                                              <option value="NA">NA</option>
                                            
                                        </select>
</div>
                                    </td>
                                   
                                    <td width="25%"><input type="text" class="form-control" name="web_aligner_remarks"></td>
                                </tr>
                            </table>


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
                                    <td class="text-center" width="3%">13</td>
                                    <td width="17%">Center Slitting</td>
                                    <td width="50%" class="text-center">
                                        <select name="center_slitting" id="" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Rotary">Rotary</option>
                                            <option value="Normal Blade">Normal Blade</option>
                                            <option value="Surgical Blade">Surgical Blade</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="center_slitting_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">14</td>
                                    <td width="17%">Vertical Slitter</td>
                                    <td width="50%" class="text-center">
                                        <select name="vertical_slitting" id="" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Rotary">Rotary</option>
                                            <option value="Normal Blade">Normal Blade</option>
                                            <option value="Surgical Blade">Surgical Blade</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="vertical_slitting_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">15</td>
                                    <td width="17%">Vertical Sealer Drive</td>
                                    <td width="50%" class="text-center">
                                        <select name="vertical_sealer" id="" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Servo Motor Driven">Servo Motor Driven</option>
                                            <option value="CAM Driven">CAM Driven</option>
                                            <option value="Pneumatic">Pneumatic</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="vertical_sealer_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">16</td>
                                    <td width="17%">Laminate Pulling Drive</td>
                                    <td width="50%" class="text-center">
                                        <select name="laminate_pulling" id="" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Servo Motor Driven">Servo Motor Driven</option>
                                            <option value="AC Geared Motor">AC Geared Motor</option>
                                            <option value="Clutch Brake">Clutch Brake</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="laminate_pulling_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">17</td>
                                    <td width="17%">Embossing Coding Drive</td>
                                    <td width="50%" class="text-center">
                                        <select name="embossing_coding" id="" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Servo Motor Driven">Servo Motor Driven</option>
                                            <option value="NOT APPLICABLE">NOT APPLICABLE</option>
                                            <option value="Pneumatic Driven">Pneumatic Driven</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="embossing_coding_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">18</td>
                                    <td width="17%">Cooling Station Drive</td>
                                    <td width="50%" class="text-center">
                                        <select name="cooling_station" id="" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Servo Motor Driven">Servo Motor Driven</option>
                                            <option value="NOT APPLICABLE">NOT APPLICABLE</option>
                                            <option value="Pneumatic Driven">Pneumatic Driven</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="cooling_station_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">19</td>
                                    <td width="17%">Horizontal Sealer Drive</td>
                                    <td width="50%" class="text-center">
                                        <select name="horizontal_sealer" id="" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Servo Motor Driven">Servo Motor Driven</option>
                                            <option value="CAM Driven">CAM Driven</option>
                                            <option value="Pneumatic">Pneumatic</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="horizontal_sealer_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">20</td>
                                    <td width="17%">Perforation Blade Drive</td>
                                    <td width="50%" class="text-center">
                                        <select name="perforation_blade" id="" class="form-control">
                                            <option value="">--Select--</option>
                                             <option value="Servo Motor Driven">Servo Motor Driven</option>
                                            <option value="NOT APPLICABLE">NOT APPLICABLE</option>
                                            <option value="Pneumatic Driven">Pneumatic Driven</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="perforation_blade_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">21</td>
                                    <td width="17%">Piston Drive</td>
                                    <td width="50%" class="text-center">
                                        <select name="priston_drive" id="" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Servo Motor Driven">Servo Motor Driven</option>
                                            <option value="AC Geared Motor">AC Geared Motor</option>
                                            <option value="Clutch Brake">Clutch Brake</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="priston_drive_remarks"></td>
                                </tr>
                            </table>
                            <table class="table">

                                <tr>
                                    <td class="text-center" width="3%">22</td>
                                    <td width="17%">Valve Type</td>
                                    <td width="50%" class="text-center">
                                        <select name="valve_type" id="valve_type" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Individual Dozing">Individual Dozing</option>
                                            <option value="Rotary Dozing">Rotary Dozing</option>
                                        </select>

                                        <div id="individual_option" style="display: none;">
                                            <br>
                                            <select name="individual_option" id="individual_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="NRV Type">NRV Type</option>
                                                <option value="PCL Controlled Type">PCL Controlled Type</option>
                                            </select>
                                        </div>

                                        <div id="rotary_option1" style="display:none;" >
                                            <br>
                                            <select name="rotary_option1" id="rotary_option_select1" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Servo Driven">Servo Driven</option>
                                                <option value="Pneumatic Driven">Pneumatic Driven</option>
                                                <option value="Cam Driven">Cam Driven</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="valve_type_remarks"></td>
                                </tr>

                                <script>
                                    $(document).ready(function() {

                                        $('#valve_type').change(function() {
                                            var valveType = $(this).val();


                                            $('#individual_option').hide();
                                            $('#rotary_option1').hide();

                                            $('#rotary_option_select1').val('');

                                            $('#individual_option_select').val('');

                                            if (valveType == "Individual Dozing") {
                                                $('#individual_option').show();
                                            } else if (valveType == "Rotary Dozing") {
                                                $('#rotary_option1').show();
                                            }
                                        });

                                    });
                                </script>
                            </table>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">23</td>
                                    <td width="17%">Shut off Nozzle Drive</td>
                                    <td width="50%" class="text-center">
                                        <select name="shutt_off_nozzle" id="" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Servo Motor Driven">Servo Motor Driven</option>
                                            <option value="NOT APPLICABLE">NOT APPLICABLE</option>
                                            <option value="Pneumatic Driven">Pneumatic Driven</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="shutt_off_nozzle_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">24</td>
                                    <td width="17%">Filling Drive</td>
                                    <td width="50%" class="text-center">
                                        <select name="filling_plate_drive" id="" class="form-control">
                                            <option value="">--Select--</option>
                                           <option value="Servo Motor Driven">Servo Motor Driven</option>
                                            <option value="NOT APPLICABLE">NOT APPLICABLE</option>
                                            <option value="AC Geared Motor">AC Geared Motor</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="filling_plate_drive_remarks"></td>
                                </tr>
                            </table>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">25</td>
                                    <td width="17%">Individual Weight Adjustment</td>
                                    <td width="50%" class="text-center">
                                        <select name="individual_weight" id="" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Servo Motor Driven">Servo Motor Driven</option>
                                            <option value="Manual">Manual</option>

                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="individual_weight_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">26</td>
                                    <td width="17%">Overall Weight Adjustment</td>
                                    <td width="50%" class="text-center">
                                        <select name="overall_weight_adjust" id="" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Servo Motor Driven">Servo Motor Driven</option>
                                            <option value="Manual">Manual</option>

                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="overall_weight_adjust_remarks"></td>
                                </tr>
                            </table>
                            <table class="table">

                                <tr>
                                    <td class="text-center" width="3%">27</td>
                                    <td width="17%">Traverse Drive</td>
                                    <td width="50%" class="text-center">
                                        <select name="traverse_drive" id="traverse_drive" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>

                                        <div id="yes_traverse_drive" style="display: none;">
                                            <br>
                                            <select name="yes_traverse_drive" id="yes_traverse_drive_option" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Servo Motor Driven">Servo Motor Driven</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="traverse_drive_remarks"></td>
                                </tr>

                                <script>
                                    $(document).ready(function() {

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

                                    <td width="50%" class="text-center"><select name="printer" id="printer" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Inkjet">Inkjet</option>
                                            <option value="TTO">TTO</option>
                                            <option value="Thermal Inkjet Printer">Thermal Inkjet Printer</option>
                                        </select>

                                        <div id="inkjet_option" style="display: none;">
                                            <br>
                                            <select name="inkjet_option" id="inkjet_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Markem Image">Markem Image</option>
                                                <option value="Videojet">Videojet</option>
                                                <option value="Dominos">Dominos</option>
                                                <option value="Control Print">Control Print</option>
                                            </select>
                                        </div>

                                        <div id="tto_option" style="display: none;">
                                            <br>
                                            <select name="tto_option" id="tto_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                               <option value="Markem Image">Markem Image</option>
                                                <option value="Videojet">Videojet</option>
                                                <option value="Dominos">Dominos</option>
                                                <option value="Control Print">Control Print</option>
                                            </select>
                                        </div>

                                        <div id="thermal_inkjet_option" style="display: none;">
                                            <br>
                                            <select name="thermal_inkjet_option" id="thermal_inkjet_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Norwix">Norwix</option>
                                            </select>
                                        </div>

                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="printer_remarks"></td>
                                </tr>
                                <script>
                                    $(document).ready(function() {

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

                                    <td width="50%" class="text-center"><select name="case_packer_drive" id="" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Servo Motor Drive">Servo Motor Drive</option>
                                            <option value="Pnesumatic Driven">Pnesumatic Driven</option>
                                            <option value="AC Geared Motor">AC Geared Motor</option>
                                        </select></td>
                                    <td width="25%"><input type="text" class="form-control" name="case_packer_drive_remarks"></td>
                                </tr>
                            </table>
                            <table class="table">

                                <tr>
                                    <td class="text-center" width="3%">30</td>
                                    <td width="17%">Nozzle/Funnel</td>
                                    <td width="50%" class="text-center" colspan="2">
                                        <select name="nozzle_funnel" id="nozzle_funnel" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Powder">Powder</option>
                                            <option value="Liquid - Capillary">Liquid - Capillary</option>
                                            <option value="Liquid - Shut off Nozzle">Liquid - Shut off Nozzle</option>
                                        </select>

                                        <div id="powder_option1" style="display: none;">
                                            <br>
                                            <select name="powder_option1" id="powder_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="1">Oval</option>
                                                <option value="2">Round</option>
                                            </select>
                                        </div>

                                        <div id="liquid_option1" style="display: none;">
                                            <br>
                                            <select name="liquid_option1" id="liquid_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="1">Round</option>
                                                <option value="2">Elliptical</option>
                                            </select>
                                        </div>

                                        <div id="liquid_shut_option1" style="display: none;">
                                            <br>
                                            <select name="liquid_shut_option1" id="liquid_shut_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="1">Rhombus</option>
                                                <option value="2">Round</option>
                                            </select>
                                        </div>

                                    </td>
                                    <td width="25%" rowspan="2"><input type="text" class="form-control" name="nozzle_funnel_remarks"></td>
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
                            </table>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">31</td>
                                    <td width="17%">Hose Pipe (type)</td>
                                    <td width="50" class="text-center">
                                        <select name="hose_pipe" id="hose_pipe" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Threaded">Threaded</option>
                                            <option value="Tri-Clamp">Tri-Clamp</option>
                                        </select>
                                    </td>

                                    <td width="25%"><input type="text" class="form-control" name="hose_pipe_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">32</td>
                                    <td width="17%">Batch Cut Format</td>
                                    <td width="50%" class="text-center">
                                        <select name="batch_cut_format" id="batch_cut_format" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Straight" >Straight Cut</option>
                                            <option value="String" >String</option>
                                        </select>

                                        <div id="string_option" style="display: none;">
                                            <br>
                                            <select name="string_option" id="string_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Matt Format">Matt Format</option>
                                            </select>
                                        </div>

                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="batch_cut_format_remarks"></td>
                                </tr>

                                <script>
                                    $(document).ready(function() {

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
                                    <td width="50%" class="text-center" rowspan="6">
                                        <select name="horizontal_sealer" id="" class="form-control">
                                            <option value="">--Select--</option>
                                           
                                            <option value=""></option>
                                           
                                        </select>

                                        <b>Image:</b>
                                        
                                    
                                    </td>
                                    <td width="25%" rowspan="6" style="position: relative;">
                                        <textarea name="horizontal_sealer_remarks" id="" class="form-control text--box"></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%" style="height: 20px;"></td>
                                    <td width="17%" style="height: 20px;"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%" style="height: 20px;"></td>
                                    <td width="17%" style="height: 20px;">Total Sealing width - 5mm</td>
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
                                </tr>
                            </table>
                            <table class="table">
                                <tr>
                                    <th class="text-center" colspan="3">Order Details For MULTI TRACK MACHINE</th>
                                    <th class="text-center" colspan="1" width="25%">REMARKS</th>
                                </tr>

                                <tr>
                                    <td class="text-center" width="3%">34</td>
                                    <td width="17%">Vertical Sealer</td>
                                    <td width="50%" class="text-center" rowspan="6">
                                         <select name="vertical_sealer" id="" class="form-control">
                                            <option value="">--Select--</option>
                                          
                                            <option value=""></option>
                                          
                                        </select>
                                         <b>Image:</b>
                                    </td>
                                    <td width="25%" rowspan="6" style="position: relative;">
                                        <textarea name="vertical_sealer_remarks" id="" class="form-control text--box"></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%" style="height: 20px;"></td>
                                    <td width="17%" style="height: 20px;"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%" style="height: 20px;"></td>
                                    <td width="17%" style="height: 20px;">Total Sealing width - 5mm</td>
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
                                </tr>
                            </table>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">35</td>
                                    <td width="17%">Rotary Valve Coating</td>
                                    <td width="50%">
                                        <select name="rotary_valve_coating" id="rotary_valve_coating" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="HALAR">HALAR</option>
                                            <option value="TEFLON">TEFLON</option>
                                            <option value="No Coating">No Coating</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="rotary_valve_coating_remarks"></td>
                                </tr>
                            </table>

                            <table class="table">

                                <tr>
                                    <td class="text-center" width="3%">36</td>
                                    <td width="17%">Working Speed</td>
                                    <td width="50%" class="text-center">
                                        <input type="text" name="working_speed" value="50 - 60 Strokes/minute" class="form-control">
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="working_speed_remarks"></td>
                                </tr>
                            </table>
                            <table class="table">

                                <tr>
                                    <td class="text-center" width="3%">37</td>
                                    <td width="17%">Reel Shaft Type</td>
                                    <td width="50%" class="text-center">
                                        <select name="reel_shaft_type" id="reel_shaft_type" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Hollow">Hollow</option>
                                            <option value="Solid">Solid</option>
                                            <option value="Pneumatic">Pneumatic</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="reel_shaft_type_remarks"></td>
                                </tr>
                            </table>

                            <table class="table">

                                <tr>
                                    <td class="text-center" width="3%">38</td>
                                    <td width="17%">Reel Core Diameter</td>
                                    <td width="50%">
                                        <select name="reel_core_diameter" id="reel_core_diameter" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="76mm">76mm</option>
                                            <option value="152mm">152mm</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="reel_core_diameter_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">39</td>
                                    <td width="17%">Trial Material / Product</td>
                                    <td width="50%">
                                        <select name="trial_material" id="trial_material" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="trial_material_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">40</td>
                                    <td width="17%">Laminate Details</td>
                                    <td width="50%">
                                        <select name="laminate_detail" id="laminate_detail" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Heat Sealable laminate">Heat Sealable laminate</option>
                                            <option value="Impulse Sealer">Impulse Sealer</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="laminate_detail_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">41</td>
                                    <td width="17%">Heater Control System</td>
                                    <td width="50%">
                                        <select name="heater_control_system" id="heater_control_system" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Grouping">Grouping</option>
                                            <option value="Individual">Individual</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="heater_control_system_remarks"></td>
                                </tr>
                            </table>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">42</td>
                                    <td width="17%">Beacon Lights</td>
                                    <td width="50%" class="text-center" colspan="1">
                                        <select name="beacon_light" id="" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="3 Stage">3 Stage</option>
                                            <option value="4 Stage">4 Stage</option>
                                            <option value="5 Stage">5 Stage</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="beacon_light_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">43</td>
                                    <td width="17%">Hopper Level Sensor</td>
                                    <td width="50%" class="text-center">
                                        <select id="hooper_level" name="hooper_level" class="form-control">
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
                                    <td class="text-center" width="3%">44</td>
                                    <td width="17%">Safety Relay + Safety Switch</td>
                                    <td width="50%" class="text-center"><select name="safety_relay" id="" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Yes">Yes</option>

                                            <option value="No">No</option>
                                        </select></td>
                                    <td width="25%"><input type="text" class="form-control" name="safety_relay_remarks"></td>
                                </tr>
                            </table>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">45</td>
                                    <td width="17%">PLC Make</td>


                                    <td width="50%" class="text-center"><select name="plc_maker" id="" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Allen Bradely">Allen Bradely</option>
                                            <option value="Omron">Omron</option>
                                            <option value="Mitsubishi">Mitsubishi</option>
                                        </select></td>

                                    <td width="25%"><input type="text" class="form-control" name="plc_maker_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">46</td>
                                    <td width="17%">CIP System</td>

                                    <td class="text-center" width="50%">
                                        <select name="cip_system" id="cip_system" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>

                                        <div id="cip_system_option" style="display: none;">
                                            <br>
                                            <select name="cip_system_option" id="cip_system_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Alfa Laval">Alfa Laval</option>
                                                <option value="Shubham Standard">Shubham Standard</option>
                                            </select>
                                        </div>
                                    </td>

                                    <td width="25%" rowspan="2"><input type="text" class="form-control" name="cip_system_option_remarks"></td>
                                </tr>

                                <script>
                                    $(document).ready(function() {

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
                                    <td class="text-center" width="3%">47</td>
                                    <td width="17%">Tool Kit</td>
                                    <td width="50%" class="text-center">
                                        <select name="tool_kit" id="" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Yes">Yes</option>

                                            <option value="No">No</option>
                                        </select>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="tool_kit_remarks"></td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="3%">48</td>
                                    <td width="17%">Changeover Parts</td>
                                    <td width="50%" class="text-center">
                                        <select name="changeover_part" id="" class="form-control">
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
                                    <th class="text-center" colspan="3">Order Details For MULTI TRACK MACHINE</th>
                                    <th class="text-center" colspan="1" width="25%">REMARKS</th>
                                </tr>

                                <tr>
                                    <td class="text-center" width="3%">49</td>
                                    <td width="17%">Secondary Packaging Solution</td>
                                    <td width="50%" class="text-center">
                                        <select name="secondary_pack" id="secondary_pack" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>

                                        <div id="yes_secondary_pack" style="display: none;">
                                            <br>
                                            <select name="yes_secondary_pack[]" id="yes_secondary_pack_option" class="select2" multiple style="width: 100%;">
                                                <option value="">--Select--</option>
                                                <option value="Collating Conveyor">Collating Conveyor</option>
                                                <option value="Transfer Conveyor">Transfer Conveyor</option>
                                                <option value="Case Erector">Case Erector</option>
                                                <option value="Case Packer">Case Packer</option>
                                                <option value="Flow Wrap Machine">Flow Wrap Machine</option>
                                                <option value="Case Taping Machine">Case Taping Machine</option>
                                                <option value="Check Weigher with Rejection System">Check Weigher with Rejection System</option>
                                            </select>
                                        </div>

                                        <div id="case_packer" style="display:none;">
                                            <br>
                                            <select name="case_packer" id="case_packer_option" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Slide Type">Slide Type</option>
                                                <option value="Butterfly Type">Butterfly Type</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td width="25%" style="position: relative;"><textarea name="secondary_pack_remarks" id="" class="form-control text--box"></textarea></td>
                                </tr>

                                <script>
                                    $(document).ready(function() {

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

                                        $('#yes_secondary_pack_option').select2();

                                        $('#yes_secondary_pack_option').change(function() {
                                            var secondaryPackValue1 = $(this).val();


                                            if (secondaryPackValue1 && secondaryPackValue1.includes("Case Packer")) {
                                                $('#case_packer').show();
                                            } else {
                                                $('#case_packer').hide();
                                            }
                                        });




                                    });
                                </script>


                            </table>
                            <table class="table">
                                <tr>
                                    <td class="text-center" width="3%">50</td>
                                    <td width="17%">Ladder & Platform</td>
                                    <td width="50%" class="text-center">
                                        <select name="ladder_platform" id="" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </td>

                                    <td width="25%"><input type="text" class="form-control" name="ladder_platform_remarks"></td>
                                </tr>

                                <tr>
                                    <td class="text-center" width="3%">51</td>
                                    <td width="17%">Machine Guarding</td>
                                    <td width="50%" class="text-center">

                                        <select name="machine_guarding" id="machine_guarding" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="Aluminium">Aluminium</option>
                                            <option value="SS-304">SS-304</option>
                                            <option value="Shubham Standard">Shubham Standard</option>
                                        </select>

                                        <div id="aluminium_option" style="display: none;">
                                            <br>
                                            <select name="aluminium_option" id="aluminium_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Standard">Standard</option>
                                                <option value="360">360</option>
                                            </select>
                                        </div>
                                        <div id="ss_304_option" style="display: none;">
                                            <br>
                                            <select name="ss_304_option" id="ss_304_option_select" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="Standard">Standard</option>
                                                <option value="360">360</option>
                                            </select>
                                        </div>

                                    </td>
                                    <td width="25%" style="position: relative;"><textarea name="machine_guarding_remarks" id="" class="form-control text--box"></textarea></td>
                                </tr>

                                <script>
                                    $(document).ready(function() {

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
                                    <td class="text-center" width="3%">52</td>
                                    <td width="17%">SPECIAL NOTES</td>
                                    <td width="75%">

                                        <div class="row">
                                            <div class="col-sm-8">
                                                <select name="special_notes_list" id="special_notes_list1" class="form-control">
                                                    <option value="">--Select Parts Description--</option>
                                                    <option value="1">Coding Pad</option>
                                                    <option value="2">Oil Seal</option>
                                                    <option value="3">O-Ring</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-3">
                                                <input type="number" name="special_notes_qty" class="form-control">
                                            </div>
                                            <div class="col-sm-1">
                                                <button type="button" class="btn btn-warning" name="add" id="addmore_btn1" style="padding: 0px !important; height: 21px !important; font-size: 13px !important; width: 21px;"><b>+</b></button>
                                            </div>
                                        </div>
                                        <div id="dynamictasks1"></div>
                                        <br>
                                        <textarea class="form-control ckeditor" name="special_notes" id="special_notes"></textarea>
                                    </td>
                                </tr>
                            </table>
                            <table class="table">

                                <tr>
                                    <td class="text-center" width="3%">53</td>
                                    <td width="17%">Design/Trial Comments</td>
                                    <td width="75%">
                                        <input type="text" class="form-control">


                                    </td>
                                </tr>
                            </table>
                            <table class="table">

                                <tr>
                                    <td class="text-center">Reviewed By:</td>
                                    </td>
                                </tr>
                            </table>

                            <table class="table">

                                <tr>
                                    <td class="text-center">
                                        <div class="row" style="margin: 50px 0px;">
                                            <div class="col-sm-2">Project</div>
                                            <div class="col-sm-2">Marketing Dept. Head Design Dept.</div>
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
    </script>
    </script>

    <script src="<?php echo assets_url; ?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            var i = 2;
            $('#addmore_btn1').click(function() {
                i++;

                $('#dynamictasks1').append('<div id="row' + i + '" class="row"><div class="col-sm-8"><select name="special_notes_list' + i + '" id="special_notes_list" class="form-control"><option value="">--Select Parts Description--</option><option value="1">Coding Pad</option><option value="2">Oil Seal</option><option value="3">O-Ring</option></select></div><div class="col-sm-3"><input type="number" name="special_notes_qty" class="form-control"></div><div class="col-sm-1"><button type="button" name="add" class="btn_remove  btn btn-danger" id="' + i + '"><i class="fa fa-close"></i></button></div></div>');

                showhidedfbox();

            });


            $(document).on('click', '.btn_remove', function() {
                var button_id = $(this).attr("id");
                $('#row' + button_id + '').remove();
            });

        });
    </script>
</body>

</html>