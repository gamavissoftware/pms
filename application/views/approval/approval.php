<?php 
  $CI =& get_instance();
  $CI->load->model('Salescrm_model', 'salescrm');
  $getAllUnits = $CI->salescrm->getAllUnits();
  $getAllProducts = $CI->salescrm->getAllProducts();
  $getHpclLocations = $CI->salescrm->getHpclLocations();
  $getLastInsertedCode = $CI->salescrm->getLastInsertedCode();

                                            
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="NJ Media">

    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

    <title><?php echo sitetitle; ?> Approval Form</title>

    <!-- Table Responsive css -->
    <script src="<?php echo assets_url; ?>js/angular.min.js"></script>
    <!-- DataTables -->
    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" >
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
    <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.18.0/ckeditor.js" integrity="sha512-woYV6V3QV/oH8txWu19WqPPEtGu+dXM87N9YXP6ocsbCAH1Au9WDZ15cnk62n6/tVOmOo0rIYwx05raKdA4qyQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

 <style type="text/css">
        .select2-container
    {
        width: 100% !important;

    }
    </style>
</head>


<body>


    <!-- Navigation Bar-->
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>
    <!-- End Navigation Bar-->

<div class="wrapper">
   <div class="container">
            <!-- Page-Title -->
        <div class="row" style="margin-top:20px;">
            <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                <div class="page-title-box">
                    <h4 class="page-title">Approval Form</h4>
                </div>
            </div>
        </div>
            <!-- end page title end breadcrumb -->
        <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
        <form id="quotation" method="post" action="<?php echo page_url; ?>Approval/save_approval" autocomplete="off" onsubmit="return validate();" enctype="multipart/form-data">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Auto-generated Code <span style="color:red;">*</span></label>
                                        <input type="text" class="form-control" value="<?php echo 'TYPE-1'.$getLastInsertedCode;?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Current Date <span style="color:red;">*</span></label>
                                        <input type="date" class="form-control mand" name="current_date" value="<?php echo date('d-m-Y');?>"  required>
                                    </div>
                                </div>
                              
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Combination Approval <span style="color:red;">*</span></label><br>
                                        <input type="checkbox" name="combination_approval" id="combination_approval" value="1" onchange="chkIfCombApproved()" style="width: 30px;height: 25px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">

                                  <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Location<span style="color:red;">*</span></label>
                                        <select class="form-control mand" name="hpcl_location[]" id="hpcl_location0">
                                            <option value="">SELECT</option>
                                            <?php if($getHpclLocations != '') {
                                                    foreach($getHpclLocations as $row) {?>
                                            <option value="<?php echo $row->id;?>"><?php echo $row->name;?></option>
                                            <?php } } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-3" class="control-label">Product</label>
                                        <span style="color:red;">*</span>
                                        <select class="form-control mand select30" name="product[]" id="product0" required onchange="add_instruments(); checkbulkandunit(0); get_historical_moq(0);">
                                            <option value="">Select</option>
                                            <?php if($getAllProducts != '') {
                                                    foreach($getAllProducts as $row) { ?>
                                                        <option value="<?php echo $row->id;?>"><?php echo $row->instruments_name;?>-<?php echo $row->pack_size;?></option>
                                                   <?php }
                                                } ?>
                                        </select>
                                    </div>
                                </div>

                                   <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-3" class="control-label">Unit</label>
                                        <span style="color:red;">*</span>
                                        <select class="form-control mand" name="unit[]" id="unit0" required onchange="">
                                            <option value="">Select</option>
                                           
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Approved Price</label>
                                        <span style="color:red;">*</span>
                                        <input type="text" class="form-control mand" name="approved_price[]" id="approved_price0" autocomplete="off" required onkeyup="allow_decimal('approved_price0');">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Valid From<span class="list_price_unit0"></span></label>
                                        <span style="color:red;">*</span>
                                        <input type="date" class="form-control mand" name="valid_from[]" id="valid_from0" required autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Valid Till<span class="list_price_unit0"></span></label>
                                        <span style="color:red;">*</span>
                                        <input type="date" class="form-control mand" name="valid_till[]" id="valid_till0" required autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">CREDIT NOTE (IN RS) <span class="chk_unit0"></span></label>
                                        <span style="color:red;">*</span>
                                        <input type="text" class="form-control mand common_readonly" name="credit_vli[]" id="credit_vli0" autocomplete="off" onkeyup="allow_decimal('credit_vli0');">
                                    </div>
                                </div>



                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Minimum Qty (if Any in LTR)</label>
                                        <span style="color:red;">*</span>
                                        <input type="text" class="form-control mand common_readonly" name="moq[]" autocomplete="off" id="moq0" onkeyup="allow_decimal('moq0');" value="0">
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Historical Data Based MOQ?</label>
                                            <input type="checkbox" name="historical_moq[]" id="historical_moq0" onchange="check_for_historical(0);" value="1">
                                        </div>
                                    </div>
                               
                                     <div class="col-md-12 historical_div" style="display: none;" id="moqyear0">
                                        <div class="form-group">
                                            <label>Year <span style="color:red">*</span></label>
                                            <select class="form-control yearinput" name="year[]" id="year0" onchange="get_historical_moq(0);">
                                                <option value="">Select</option>
                                                <?php for($kl=1;$kl<5;$kl++)
                                                { 
                                                    $number = str_pad($kl, 2, '0', STR_PAD_LEFT);
                                                    $year=date('Y',strtotime("-".$kl." Years"));
                                                    ?>
                                                    <option value="<?php echo $year;?>"><?php echo $year;?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <span style="color:red;font-weight: bold;"></span>
                                    </div>

                                     <div class="col-md-12 increment_div" style="display: none;" id="increment_div0">
                                        <div class="form-group">
                                            <label>Increase By % <span style="color:red">*</span></label>
                                            <input type="text" class="form-control increment_input" name="increment[]" id="increment0" onblur="get_historical_moq(0);">
                                            
                                        </div>
                                        <span style="color:red;font-weight: bold;"></span>
                                    </div>

                                </div>


                                 <!--  <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Invoice No.<span style="color:red;">*</span></label>
                                       <input type="text" name="invoice_no[]" class="form-control" id="invoice_no0" style="text-transform:capitalize;" >
                                    </div>
                                </div> -->


                               <!--    <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Billing Date<span style="color:red;">*</span></label>
                                       <input type="date" name="billing_date[]" class="form-control mand" id="billing_date0"required >
                                    </div>
                                </div> -->

                            
                                  <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Annexture<span style="color:red;">*</span></label>
                                       <input type="text" name="annexture[]" class="form-control" id="annexture0" >
                                    </div>
                                </div>

                                <div style="clear:both;height: 5px"></div>
                                  <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Annexture Upload<span style="color:red;">*</span></label>
                                       <input type="file" name="annex_upload[]" class="form-control" id="annex_upload0" >
                                    </div>
                                </div>


                                <div class="col-md-1">
                                    <div class="form-group" style="margin-top:25px">
                                        <button type="button" class="btn btn-warning" name="add" id="addmore_btn"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="dynamictasks"></div>


                        <hr  style="display:none;">
                         <div class="row" style="display:none;">
                             <div class="col-md-12">
                                <div class="col-md-4">
                                 <h3>Payment Terms</h3>
                                </div>
                             </div>
                         </div>
                        <div class="row" style="margin-top:20px;display: none;">
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Payment Terms<span style="color:red;">*</span></label>
                                        <!-- <input type="text" class="form-control" name="payment_terms"> -->
                                        <select class="form-control mand" name="payment_terms" id="payment_terms" onchange="check_credit_period()">
                                        	<!-- <option value="">SELECT</option> -->
                                        	<option value="1">ADVANCE</option>
                                        	<!-- <option value="2">CREDIT PERIOD</option> -->
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3" id="chk_credit" style="display: none;">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Credit Days<span style="color:red;">*</span></label>
                                        <input type="number" class="form-control" name="credit_period" id="credit_period">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Transportation<span style="color:red;">*</span></label>
                                        <select class="form-control mand" name="transportation" id="transportation" onchange="chk_transportation();">
                                            <!-- <option value="">Select</option> -->
                                           <!--  <option value="1">Included</option> -->
                                            <option value="2">Not Included</option>
                                        </select>
                                    </div>
                                </div>

                               <!--  <div class="col-md-3" id="chk_transportation" style="display: none;">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Transportation Rate<span style="color:red;">*</span></label>
                                        <input type="text" class="form-control" name="rate" id="rate">
                                    </div>
                                </div> -->
                            </div>
                        </div>
                        <hr>
                        <div class="row combination_head" style="display: none;">
                            <div class="col-md-12 text-center">
                                <h3>Product Combinations</h3>
                                
                            </div>
                        </div>

                        <div class="combination_repeat" style="display: none;" style="margin-top:20px;">
                            <div class="row">
                                <div class="col-md-12" style="margin-top:25px;">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="field-2" class="control-label">Combination Type<span style="color:red;">*</span></label>
                                            <select class="form-control" name="combination_type" id="combination_type" onchange="chk_combination_type();">
                                             <option value="3">By MOQ & VLI</option>
                                            </select>

                                        </div>
                                    </div>
                                    <div class="col-md-3"></div>
                                  <!--   <div class="col-md-5">
                                        <div class="form-group" style="margin-top:25px">
                                            <button type="button" class="btn btn-warning" name="add" id="add_more_combination">Add More Combinations</button>
                                        </div>
                                    </div> -->
                                </div>
                            </div>

                            <div class="row prod_comb0">
                                <div class="col-md-12">
                                     <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="field-3" class="control-label">Products</label>
                                            <span style="color:red;">*</span>
                                            <select class="form-control combi_product" name="comb_product[]" id="comb_product0">
                                                
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group" style="margin-top:25px">
                                            <button type="button" class="btn btn-warning btn-xs add_more_product_comb" name="add" id="" data-id="0"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-3" id="combmoqfield"  style="display:none;">
                                        <div class="form-group">
                                            <label for="field-3" class="control-label">MOQ (IN LTR)</label>
                                            <span style="color:red;">*</span>
                                            <input type="text" class="form-control" name="comb_moq" autocomplete="off" id="com_moq0" onkeyup="allow_decimal('com_moq0');" value="0">
                                        </div>
<!-- 
                                        <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Historical Data Based MOQ?</label>
                                            <input type="checkbox" name="historical_moq_comb[]" id="historical_moq_comb" onchange="check_for_historical_comb();">
                                        </div>
                                    </div> -->
                                  
                               
                                   <!--   <div class="col-md-12 historical_div_comb" style="display: none;" id="moqyear_comb">
                                        <div class="form-group">
                                            <label>Year <span style="color:red">*</span></label>
                                            <select class="form-control" name="year_comb" id="year_comb">
                                                <option value="">Select</option>
                                                <?php 
                                                // for($kl=1;$kl<5;$kl++)
                                                // { 
                                                //     $number = str_pad($kl, 2, '0', STR_PAD_LEFT);
                                                //     $year=date('Y',strtotime("-".$kl." Years"));
                                                    ?>
                                                    <option value="<?php //echo $year;?>"><?php //echo $year;?></option>
                                                <?php //} ?>
                                            </select>
                                        </div>
                                    </div> -->

                                  <!--   <div class="col-md-12" style="display: none;" id="increment_div_comb">
                                        <div class="form-group">
                                            <label>Increase By % <span style="color:red">*</span></label>
                                            <input type="text" class="form-control" name="increment_comb" id="increment_comb">
                                            
                                        </div>
                                        <span style="color:red;font-weight: bold;"></span>
                                    </div>
 -->


                                    </div>
                                    <div class="col-md-3" id="combcreditfield"  style="display:none;">
                                        <div class="form-group">
                                            <label for="field-3" class="control-label">CREDIT NOTE (IN RS)</label>
                                            <span style="color:red;">*</span>
                                            <input type="text" class="form-control" name="comb_credit_vli" id="com_credit_vli0" autocomplete="off" onkeyup="allow_decimal('com_credit_vli0');">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <div class="row">
                                 <div class="form-group pull-right">
                                    <input type="submit" id="saves" class="btn btn-info" value="Submit">
                                </div>
                            </div>
                        </div>
            </form>

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
    <script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
    <!-- Datatable init js -->
    <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>

    <!-- App js -->
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.select30').select2(); 
            var i = 1;
            $('#addmore_btn').click(function() {
                $('#dynamictasks').append('<hr style="border:1px dotted #000;"><div id="row' + i + '" class="row"><div class="col-md-12">  <div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Location<span style="color:red;">*</span></label><select class="form-control mand" name="hpcl_location[]" id="hpcl_location'+i+'"><option value="">SELECT</option><?php if($getHpclLocations != '') { foreach($getHpclLocations as $row) {?> <option value="<?php echo $row->id;?>"><?php echo $row->name;?></option> <?php } } ?></select></div></div><div class="col-md-3"> <div class="form-group"> <label for="field-3" class="control-label">Product</label> <span style="color:red;">*</span><select class="form-control mand select3'+i+'" name="product[]" id="product'+i+'" required onchange="add_instruments();checkbulkandunit('+i+');get_historical_moq('+i+');"><option value="">Select</option> <?php if($getAllProducts !='') { foreach($getAllProducts as $row) { ?> <option value="<?php echo $row->id;?>"><?php echo $row->instruments_name;?>-<?php echo $row->pack_size;?></option> <?php }} ?> </select> </div></div><div class="col-md-2"><div class="form-group"><label for="field-3" class="control-label">Unit</label><span style="color:red;">*</span><select class="form-control mand" name="unit[]" id="unit'+i+'" required onchange=""><option value="">Select</option></select></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Approved Price</label><span style="color:red;">*</span><input type="text" class="form-control mand notext'+i+'1" name="approved_price[]" id="approved_price'+i+'" autocomplete="off" required onkeyup="checkappprice('+i+',1);"></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Valid From<span class="list_price_unit0"></span></label><span style="color:red;">*</span><input type="date" class="form-control mand" name="valid_from[]" id="valid_from'+i+'" required autocomplete="off"></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Valid Till<span class="list_price_unit0"></span></label><span style="color:red;">*</span><input type="date" class="form-control mand" name="valid_till[]" id="valid_till'+i+'" required autocomplete="off"></div></div><div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">CREDIT NOTE (in Rs) <span class="chk_unit'+i+'"></span></label> <span style="color:red;">*</span> <input type="text" class="form-control mand common_readonly notext'+i+'2" name="credit_vli[]" id="credit_vli'+i+'" autocomplete="off" onblur="getnetamt(0)" onkeyup="checkappprice('+i+',2);"> </div></div><div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">Minimum Qty if Any</label> <span style="color:red;">*</span> <input type="text" class="form-control mand common_readonly notext'+i+'3" name="moq[]" autocomplete="off" id="moq'+i+'" onkeyup="checkappprice('+i+',3);" value="0"> </div> <div class="col-md-12"><div class="form-group"><label>Historical Data Based MOQ?</label><input type="checkbox" name="historical_moq[]" id="historical_moq'+i+'" onchange="check_for_historical('+i+');"></div></div><div class="col-md-12 historical_div" style="display: none;" id="moqyear'+i+'"><div class="form-group"><label>Year <span style="color:red">*</span></label><select class="form-control yearinput" name="year[]" id="year'+i+'" onchange="get_historical_moq('+i+');"><option value="">Select</option><?php for($kl=1;$kl<5;$kl++){ $number = str_pad($kl, 2, '0', STR_PAD_LEFT); $year=date('Y',strtotime("-".$kl." Years"));?><option value="<?php echo $year;?>"><?php echo $year;?></option><?php } ?></select></div></div><div class="col-md-12 increment_div" style="display: none;" id="increment_div'+i+'"><div class="form-group"><label>Increase By % <span style="color:red">*</span></label><input type="number" class="form-control increment_input" name="increment[]" id="increment'+i+'" onblur="get_historical_moq('+i+');"></div><span style="color:red;font-weight: bold;"></span></div></div><div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">Annexture<span style="color:red;">*</span></label> <input type="text" name="annexture[]" class="form-control mand" id="annexture'+i+'" > </div></div><div style="clear:both;height:5px;"></div><div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">Annexture Upload<span style="color:red;">*</span></label> <input type="file" name="annex_upload[]" class="form-control mand" id="annex_upload'+i+'" > </div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:23px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="' + i + '"><i class="fa fa-close"></i></button></div></div></div><br/>');

                initializeSelect2(i);
             //   getproductname(i);
                chkIfCombApproved();
                i++;
            });

            $(document).on('click', '.btn_remove', function() {
                var button_id = $(this).attr("id");
                $('#row' + button_id + '').remove();
            });

            var k = 1;
            $('.add_more_product_comb').click(function() {

                $('.prod_comb0').append('<div id="row' + k + '" class="row"><div class="col-md-12"><div class="col-md-3"><div class="form-group"> <label for="field-3" class="control-label">Product</label> <span style="color:red;">*</span><select class="form-control mand  combi_product" name="comb_product[]" id="comb_product'+k+'"></select> </div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:23px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove_prod_comb btn btn-danger btn-xs" id="' + k + '"><i class="fa fa-close"></i></button></div></div></div><br/>');
                    
                    add_instruments_by_id(k);
               
                k++;
            });

           // var j = 1;
           //  $('#add_more_combination').click(function() {
            
           //      var flagnew=parseInt(j);
           //      $('.combination_repeat').append('<div id="row' + j + '" class="row"><div class="col-md-12"><hr><div class="row"><div class="col-md-12"><div class="col-md-3"> <div class="form-group"> <label for="field-2" class="control-label">Combination Type<span style="color:red;">*</span></label> <select class="form-control" name="transportation" id="transportation" onchange="chk_transportation()"> <option value="">Select</option> <option value="1">By MOQ</option> <option value="2">By VLI</option> <option value="2">By MOQ & VLI</option> </select> </div></div><div class="col-md-5"></div><div class="col-md-3"><div class="form-group pull-left" style="padding-top:23px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove_prod_comb_row btn btn-danger" id="' + j + '"><i class="fa fa-close"></i></button></div></div></div></div><div class="row prod_comb'+flagnew+'"><div class="col-md-12"><div class="col-md-3"> <div class="form-group"> <label for="field-3" class="control-label">Product</label> <span style="color:red;">*</span><select class="form-control mand select3'+j+'" name="product[]" id="product0" required><option value="">Select</option> <?php if($getAllProducts !='') { foreach($getAllProducts as $row) { ?> <option value="<?php echo $row->id;?>"><?php echo $row->instruments_name;?></option> <?php }} ?> </select> </div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:23px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" class="btn btn-warning btn-xs" onclick="add_new_products_for_combination('+j+')" data-id="'+j+'" name="add"><i class="fa fa-plus"></i></button></div></div></div><div class="row"> <div class="col-md-12"> <div class="col-md-3"> <div class="form-group"> <label for="field-3" class="control-label">MOQ</label> <span style="color:red;">*</span> <input type="text" class="form-control mand" name="moq[]" autocomplete="off" id="moq0" value="0"> </div></div><div class="col-md-3"> <div class="form-group"> <label for="field-3" class="control-label">VLI</label> <span style="color:red;">*</span> <input type="text" class="form-control mand" name="credit_vli[]" id="credit_vli0" autocomplete="off"> </div></div></div></div></div></div><br/>');

           //      initializeSelect2(j);
           //      j++;
           //  });


           
            $(document).on('click', '.btn_remove_prod_comb', function() {
                var button_id = $(this).attr("id");
                $('#row' + button_id + '').remove();
            });


            function add_new_products_for_combination(id)
            {        
                
                var flag=id;
                var flagew=parseInt(flag)+1;

                $('.prod_comb'+flag).append('<div id="row'+flagew+ '" class="row"><div class="col-md-12"><div class="col-md-3"><div class="form-group"> <label for="field-3" class="control-label">Product</label> <span style="color:red;">*</span><select class="form-control mand select3'+flagew+'" name="product[]" id="product0" required><option value="">Select</option> <?php if($getAllProducts !='') { foreach($getAllProducts as $row) { ?> <option value="<?php echo $row->id;?>"><?php echo $row->instruments_name;?></option> <?php }} ?> </select> </div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:23px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove_prod_comb btn btn-danger btn-xs" id="'+flagew+ '"><i class="fa fa-close"></i></button></div></div></div><br/>');

                initializeSelect2(flagew);

            }
               

            var date = new Date();
              var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
            jQuery('.datepicker').datepicker({
                startDate: date,
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
            });

        });
    </script>


    <script>

        function chk_transportation() {
            $("#chk_transportation").css('display', 'none');
            $("#rate").attr('required', false);

           if($("#transportation").val() == 2) {
                $("#chk_transportation").css('display', '');
                $("#rate").attr('required', true);
           }
           
        }

        function initializeSelect2(i) {
            $('.select3'+i).select2(); 
        }

        function check_unit(i) {
            var pack_size = $('.pack_size'+i+' option:selected').text();
            
            if($('.pack_size'+i).val() != '') {
                $('.chk_unit'+i).text('Per '+pack_size);
            } else {
                $('.chk_unit'+i).text('');
            } 
        }

        function check_credit_period() {
        	 $("#chk_credit").css('display', 'none');
             $("#credit_period").attr('required', false);

           if($("#payment_terms").val() == 2) {
                $("#chk_credit").css('display', '');
                $("#credit_period").attr('required', true);
           }
        }

        function validate() {
 			$("#saves").attr('disabled',false);
			$("#saves").val('Submit');

			var isValid=0;

			$(".mand").each(function() {
				var element = $(this).val();

					if (element=="") {
						isValid=1;
					}
			});

			if(isValid==0) {
				$("#saves").attr('disabled',true);
				$("#saves").val('Please Wait..');
			    return true;			 
			 } else {
				$("#saves").attr('disabled',false);
				$("#saves").val('Submit');
			    alert('All Fields Marked as (*) are mandatory');
			    return false;
			 }   

		}
    </script>

    <script language="javascript" type="text/javascript">
        $(document).ready(function() {


            $("#save").click(function() {
                var vendor_name = $("#vendor_name").val();
                if (vendor_name == '') {
                    $("#error_vendor_name").html('Required!');
                }
                var item_name = $("#item_name").val();
                if (item_name == '') {

                    $("#error_item_name").html('Required!');
                }

                var status = $("#status").val();
                if (status == '') {

                    $("#error_status").html('Required!');
                }


                if (vendor_name == '' || item_name == '' || status == '') {

                    return false;
                }

            });
        });
    </script>
    <script type="text/javascript">


   function allow_decimal(data)
{
    var self = $("#"+data);
      self.val(self.val().replace(/[^0-9\.]/g, ''));
   if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
   {
     evt.preventDefault();
   }


}

function checkappprice(i,j)
{
     var self = $(".notext"+i+j);
      self.val(self.val().replace(/[^0-9\.]/g, ''));
   if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
   {
     evt.preventDefault();
   }

}

function chkIfCombApproved() {
    var combination_approval = $('input[name="combination_approval"]:checked').val(); 
        $('.common_readonly').attr('readonly', false);
        $('.combination_repeat').css('display', 'none');
        $('.combination_head').css('display', 'none');
         $(".combi_product").removeClass('mand');
        $("#combination_type").removeClass('mand');
        $("#com_moq0").removeClass('mand');
        $("#com_credit_vli0").removeClass('mand');
        $('input[name="historical_moq[]"]').attr('disabled',false); 
        $("#moqyear_comb").css('display','none');
        $("#year_comb").attr('required',false);
        $(".historical_div_comb").css('display','none');
        $("#com_moq0").attr('readonly',false);
        $(".increment_div").css('display','none');
        $(".increment_input").attr('required',false);
        $('.yearinput').attr('required',false); 


    if(combination_approval == 1) {
        $('.common_readonly').attr('readonly', true);
        $('.common_readonly').val(0);
        $('.combination_repeat').css('display', '');
        $('.combination_head').css('display', '');
        $(".combi_product").addClass('mand');
        $("#combination_type").addClass('mand');
        $("#com_moq0").addClass('mand');
        $("#com_credit_vli0").addClass('mand');
        chk_combination_type();
        $('input[name="historical_moq[]"]').attr('disabled',true); 
        $(".historical_div").css('display','none');
        //alert('hi');
        $('.yearinput').attr('required',false);
        $(".increment_div").css('display','none');
        $(".increment_input").attr('required',false);


    }
}


    function chk_combination_type()
    {
        var ctype=$("#combination_type").val();
        if(ctype!='')
        {
            if(ctype==1)
            {
                $("#combmoqfield").css('display','');
                $("#com_moq0").addClass('mand'); 


                $("#combcreditfield").css('display','none');
                $("#com_credit_vli0").removeClass('mand');


                

            }else if(ctype==2)
            {
                $("#combmoqfield").css('display','none');
                $("#com_moq0").removeClass('mand'); 

                $("#combcreditfield").css('display','');
                $("#com_credit_vli0").addClass('mand');

            }else 
            {

                $("#combmoqfield").css('display','');
                $("#com_moq0").addClass('mand'); 

                $("#combcreditfield").css('display','');
                $("#com_credit_vli0").addClass('mand');

            }
        }
    }

    function add_instruments()
    {

         var val = [];
        $('select[name="product[]"]').each(function () {
        var a=$(this).val();
        val.push(a);
        });

        var dval=val.join(',');

            $.ajax({
            type: "post",
            url: "<?php echo page_url;?>/Approval/getproducts_byid",
            data: "prd=" + dval,
            success: function(data) {
           $(".combi_product").html(data);
            }
            });
    }


     function add_instruments_by_id(flag)
    {
        
         var val = [];
        $('select[name="product[]"]').each(function () {
        var a=$(this).val();
        val.push(a);
        });

        var dval=val.join(',');

            $.ajax({
            type: "post",
            url: "<?php echo page_url;?>/Approval/getproducts_byid",
            data: "prd=" + dval,
            success: function(data) {
           $("#comb_product"+flag).html(data);
            }
            });
    }


    function checkbulkandunit(flag)
    {
        var prd=$("#product"+flag).val();

         $.ajax({
            type: "post",
            url: "<?php echo page_url;?>Approval/getProductUnit",
            data: "prd=" + prd,
            success: function(data) {
              $("#unit"+flag).html(data);
            }
            });

    }

    function check_for_historical(id)
    {
         $("#moqyear"+id).css('display','none');
         $("#year"+id).attr('required',false);
         $("#increment_div"+id).css('display','none');
         $("#increment"+id).attr('required',false);
         $("#moq"+id).attr('readonly',false);
        if($('#historical_moq' + id).is(":checked"))
        {
            $("#moqyear"+id).css('display','');
            $("#year"+id).attr('required',true);

            $("#increment_div"+id).css('display','');
            $("#increment"+id).attr('required',true);
            $("#moq"+id).attr('readonly',true);
        }

    }


    function check_for_historical_comb()
    {
            $("#moqyear_comb").css('display','none');
            $("#year_comb").attr('required',false);
            $(".historical_div_comb").css('display','none');
            $("#com_moq0").attr('readonly',false);
            $(".increment_comb").attr('required',false);
             $(".increment_div_comb").css('display','none');
        if($('#historical_moq_comb').is(":checked"))
        {
            $(".historical_div_comb").css('display','');
            $("#moqyear_comb").css('display','');
            $("#year_comb").attr('required',true);
            $("#com_moq0").attr('readonly',true);
            $(".increment_div_comb").css('display','');
            $(".increment_comb").attr('required',true);

        }

    }

    function get_historical_moq(id)
    {
        var prd=$("#product"+id).val();
        var year=$("#year"+id).val();
        var increment=$("#increment"+id).val();
        if(prd!='' && year!='' && increment!='')
        {

             $.ajax({
            type: "post",
            url: "<?php echo page_url;?>Approval/getPreviousPurchase_for_ajax",
            data: "year="+year+"&product="+prd+"&increment="+increment,
            success: function(data) {
            $("#moq"+id).val(data);
            }
            });

        }

    }
    </script>
</body>

</html>