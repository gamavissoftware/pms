<?php 
  $CI =& get_instance();
  $CI->load->model('Salescrm_model', 'salescrm');
  $getAllProducts = $CI->salescrm->getAllProducts();
  $getAllUnits = $CI->salescrm->getAllUnits();
  $getHpclLocations = $CI->salescrm->getHpclLocations();
  $getEditApproval = $CI->salescrm->getEditApproval($this->uri->segment(3));

  $current_date = '';
  $hpcl_location = '';
  $payment_terms = '';
  $credit_period = '';
  $transportation = '';
  $transportation_rate = '';
  $combination = '';
  $moq = '';
  $vli = '';

  if($getEditApproval != '') {
    foreach ($getEditApproval as $row);
        $current_date = $row->current_date;
        $combination = $row->combination;
        $hpcl_location = $row->hpcl_location;
        $payment_terms = $row->payment_terms;
        $credit_period = $row->credit_period;
        $transportation = $row->transportation;
        $transportation_rate = $row->transportation_rate;
  }

  $getEditProductApproval = $CI->salescrm->getEditProductApproval($this->uri->segment(3));
  $getEditCombinationProduct = $CI->salescrm->getEditCombinationProduct($this->uri->segment(3));
// print_r($getEditApproval);
// print_r($getEditProductApproval);
// print_r($getEditCombinationProduct);

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="NJ Media">

    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

    <title><?php echo sitetitle; ?> EDIT TYPE 1 APPROVAL</title>

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
    <link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
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
                    <h4 class="page-title">EDIT TYPE 1 APPROVAL</h4>
                </div>
            </div>
        </div>
            <!-- end page title end breadcrumb -->
        <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
        <form id="quotation" method="post" action="<?php echo page_url; ?>Approval/update_approval/<?php echo $this->uri->segment(3);?>" autocomplete="off" onsubmit="return validate_form();"  enctype="multipart/form-data">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Current Date</label>
                                        <input type="text" class="form-control datepicker1" name="current_date" value="<?php echo date('d-m-Y', strtotime($current_date));?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Combination Approval <span style="color:red;">*</span></label><br>
                                        <input type="text" name="" class="form-control" id="" value="<?php if($combination == '1') { echo 'Combination Approval';}else{ echo "Per Product Approval"; }?>" style="color:red;font-weight:bold;" readonly >
                                        <input type="hidden" name="combination_approval" <?php if($combination == 1) {?> value="1" <?php }else{?> value="0" <?php } ?> >
                                    </div>
                                </div>
                               
                            </div>
                        </div>
                        <?php if($getEditProductApproval != '') {

                          $i = 0;
                                foreach($getEditProductApproval as $row1) {
                                    $unit_name = $CI->salescrm->getUnitName($row1->pack_size);?>
                        <div class="row" id="remove_product<?php echo $row1->id;?>">
                            <div class="col-md-12">

                               <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Locations</label>

                                        <select class="form-control " name="edit_hpcl_location[]">
                                            <option value="">SELECT</option>
                                            <?php if($getHpclLocations != '') {
                                                    foreach($getHpclLocations as $row) {?>
                                            <option value="<?php echo $row->id;?>" <?php if($row->id == $row1->location) { echo 'selected';}?>><?php echo $row->name;?></option>
                                            <?php } } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-3" class="control-label">Product</label>
                                        <span style="color:red;">*</span>
                                        <input type="hidden" name="approval_detail_id[]" value="<?php echo $row1->id;?>">
                                        <select class="form-control mand select30" name="edit_product[]" id="edit_product<?php echo $i; ?>" required onchange="add_instruments(); check_editbulkandunit(<?php echo $i; ?>);">
                                            <option value="">Select</option>
                                            <?php if($getAllProducts != '') {
                                                    foreach($getAllProducts as $row) { ?>
                                                        <option value="<?php echo $row->id;?>" <?php if($row->id == $row1->product_id) { echo 'selected';} ?>><?php echo $row->instruments_name;?></option>
                                                   <?php }
                                                } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Approved Price</label>
                                        <span style="color:red;">*</span>
                                        <input type="text" class="form-control mand" name="edit_approved_price[]" id="approved_price0" autocomplete="off" value="<?php echo $row1->approved_price;?>" required>
                                    </div>
                                </div>
                                <div class="col-md-2 col-2">
                                  <div class="form-group">
                                    <label for="field-1" class="control-label">Pack Size</label>
                                    <span style="color:red;">*</span>
                                    <select class="form-control mand pack_size<?php echo $row1->id;?>" name="edit_pack_size[]" id="edit_pack_size<?php echo $i; ?>" onchange="check_unit(<?php echo $row1->id;?>)">
                                      <!-- <option value="">SELECT</option> -->
                                      <?php if($getAllUnits != '') {
                                              foreach($getAllUnits as $row2) {?>
                                      <?php if($row2->id == $row1->pack_size) {

                                        ?>
                                        <option value="<?php echo $row2->id;?>"><?php echo $row2->shortname;?></option>
                                        <?php 
                                      } ?>

                                      <!-- <option value="<?php echo $row2->id;?>" <?php if($row2->id == $row1->pack_size) { echo 'selected';} ?>><?php echo $row2->shortname;?></option> -->


                                      <?php  } } ?>
                                    </select>
                                  </div>
                                </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="field-2" class="control-label">Valid From<span class="list_price_unit0"></span></label>
                                    <span style="color:red;">*</span>
                                    <input type="date" class="form-control mand" name="edit_valid_from[]" id="valid_from0" value="<?php echo date('Y-m-d', strtotime($row1->validity_from));?>" required autocomplete="off">
                                </div>
                            </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Valid Till<span class="list_price_unit0"></span></label>
                                        <span style="color:red;">*</span>
                                        <input type="date" class="form-control mand" name="edit_valid_till[]" id="valid_till0" value="<?php echo date('Y-m-d', strtotime($row1->validity_to));?>" required autocomplete="off">
                                    </div>
                                </div>
                               <!--  <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Price Validity<span class="list_price_unit0"></span></label>
                                        <span style="color:red;">*</span>
                                        <input type="text" class="form-control mand" name="edit_price_validity[]" id="price_validity0" required autocomplete="off" value="<?php echo $row1->price_validity;?>" oninput="allow_decimal('price_validity0');">
                                    </div>
                                </div> -->
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">CREDIT/VLI  <span class="chk_unit<?php echo $row1->id;?>">Per <?php echo $unit_name;?></span></label>
                                        <span style="color:red;">*</span>
                                        <input type="text" class="form-control common_readonly" name="edit_credit_vli[]" id="credit_vli0" autocomplete="off" value="<?php echo $row1->credit_vli;?>" oninput="allow_decimal('credit_vli0');">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Minimum Qty if Any</label>
                                        <span style="color:red;">*</span>
                                        <input type="text" class="form-control common_readonly" name="edit_moq[]" autocomplete="off" id="edit_moq<?php echo $i; ?>" value="<?php echo $row1->moq;?>">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Historical Data Based MOQ?</label>
                                            <input type="checkbox" name="edit_historical_moq[]" id="edit_historical_moq<?php echo $i; ?>" onchange="check_for_historical_edit(<?php echo $i; ?>);" value="1" class="common_readonly">
                                        </div>
                                    </div>
                               
                                     <div class="col-md-12 historical_div" style="display: none;" id="edit_moqyear<?php echo $i; ?>">
                                        <div class="form-group">
                                            <label>Year <span style="color:red">*</span></label>
                                            <select class="form-control yearinput" name="edit_year[]" id="edit_year<?php echo $i; ?>" onchange="get_historical_moq_edit(<?php echo $i; ?>);">
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

                                     <div class="col-md-12 increment_div" style="display: none;" id="edit_increment_div<?php echo $i; ?>">
                                        <div class="form-group">
                                            <label>Increase By % <span style="color:red">*</span></label>
                                            <input type="text" class="form-control increment_input" name="edit_increment[]" id="edit_increment<?php echo $i; ?>" onblur="get_historical_moq_edit(<?php echo $i; ?>);">
                                            
                                        </div>
                                        <span style="color:red;font-weight: bold;"></span>
                                    </div>
                              </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Annexture<span style="color:red;">*</span></label>
                                       <input type="text" name="edit_annexture[]" class="form-control" id="annexture0" value="<?php echo $row1->annexture;?>" >
                                    </div>
                                </div>

                                   <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Annexture Upload<span style="color:red;">*</span></label>
                                       <input type="file" name="edit_annex_upload[]" class="form-control" id="annex_upload0" >
                                       <?php if($row1->annexure_upload){
                                        ?>

                                       <a href="<?php echo site_http_root.'type_one_annexure/'.$row1->annexure_upload;?>" download class="btn-sm btn-success" >Download</a>
                                        <?php
                                       }  ?>

                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group" style="margin-top:25px">
                                        <button type="button" class="btn btn-danger" name="add" id="delete_btn" onclick="delete_approval_product(<?php echo $row1->id;?>)"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <hr style="border:1px dotted #000;">
                        <?php $i++; } } ?>

                        <!-- New record add -->
                         <div class="row" style="display: none;">
                            <div class="col-md-12">
                                <input type="checkbox" name="add_new" id="add_new" value="1" onchange="add_new_row();">Add New Item
                            </div>
                        </div>
                        <script type="text/javascript">
                            
                        </script>
                        <div class="row" id="newrow" style="display: none;">
                            <div class="col-md-12">

                               <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Location<span style="color:red;">*</span></label>
                                        <select class="form-control" name="hpcl_location[]" id="hpcl_location0">
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
                                        <select class="form-control select30" name="product[]" id="product0" onchange="add_instruments(); checkbulkandunit(0); ">
                                            <option value="">Select</option>
                                            <?php if($getAllProducts != '') {
                                                    foreach($getAllProducts as $row) { ?>
                                                        <option value="<?php echo $row->id;?>"><?php echo $row->instruments_name;?></option>
                                                   <?php }
                                                } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Approved Price</label>
                                        <span style="color:red;">*</span>
                                        <input type="text" class="form-control" name="approved_price[]" id="approved_price0" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-2 col-2">
                                  <div class="form-group">
                                    <label for="field-1" class="control-label">Pack Size</label>
                                    <span style="color:red;">*</span>
                                    <select class="form-control pack_sizeadd0" name="pack_size[]" onchange="check_unit_add(0)" id="pack_sizeadd0">
                                      <option value="">SELECT</option>
                                      <?php if($getAllUnits != '') {
                                              foreach($getAllUnits as $row2) {?>
                                      <option value="<?php echo $row2->id;?>"><?php echo $row2->shortname;?></option>
                                      <?php } } ?>
                                    </select>
                                  </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Valid From<span class="list_price_unit0"></span></label>
                                        <span style="color:red;">*</span>
                                        <input type="date" class="form-control" name="valid_from[]" id="valid_from0" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Valid Till<span class="list_price_unit0"></span></label>
                                        <span style="color:red;">*</span>
                                        <input type="date" class="form-control" name="valid_till[]" id="valid_till0" autocomplete="off">
                                    </div>
                                </div>
                                <!-- <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Price Validity<span class="list_price_unit0"></span></label>
                                        <span style="color:red;">*</span>
                                        <input type="text" class="form-control mand" name="price_validity[]" id="price_validity0" autocomplete="off" oninput="allow_decimal('price_validity0');">
                                    </div>
                                </div> -->
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">CREDIT/VLI <span class="chk_unitadd0"></span></label>
                                        <span style="color:red;">*</span>
                                        <input type="text" class="form-control common_readonly" name="credit_vli[]" id="credit_vli0" autocomplete="off" oninput="allow_decimal('credit_vli0');">
                                    </div>
                                </div>

                               
                                <div class="col-md-3">
                                   <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Minimum Qty if Any</label>
                                        <span style="color:red;">*</span>
                                        <input type="text" class="form-control common_readonly" name="moq[]" autocomplete="off" id="moq0">
                                    </div>
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



                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Annexture<span style="color:red;">*</span></label>
                                       <input type="text" name="annexture[]" class="form-control" id="annexture0" >
                                    </div>
                                </div>

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
                        <div class="row" style="display:none">
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Payment Terms<span style="color:red;">*</span></label>
                                        <select class="form-control mand" name="payment_terms" id="payment_terms" onchange="check_credit_period()">
                                            <option value="1" <?php if($payment_terms == 1) { echo 'selected';}?>>ADVANCE</option>
                                            <option value="2" <?php if($payment_terms == 2) { echo 'selected';}?>>CREDIT PERIOD</option>
                                        </select>
                                    </div>
                                </div>
                                <?php if($payment_terms == 1) {
                                    $a = 'display:none;';
                                } else {
                                    $a = '';
                                }?>
                                <div class="col-md-3" id="chk_credit" style="<?php echo $a;?>">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Credit Period<span style="color:red;">*</span></label>
                                        <input type="text" class="form-control" name="credit_period" id="credit_period" value="<?php echo $credit_period;?>">
                                    </div>
                                </div>
                                 <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Transportation<span style="color:red;">*</span></label>
                                        <select class="form-control" name="transportation" id="transportation" onchange="chk_transportation()">
                                          <option value="1" <?php if($transportation == 1) { echo 'selected';};?>>Included</option>
                                            <option value="2" <?php if($transportation == 2) { echo 'selected';};?>>Not Included</option>
                                        </select>
                                    </div>
                                </div>

                                <!--
                                <?php 
                                    if($transportation == 1) {
                                        $a = 'display:none;';
                                    } else {
                                        $a = '';
                                    }
                                ?>
                                <div class="col-md-3" id="chk_transportation" style=<?php echo $a;?>>
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Transportation Rate<span style="color:red;">*</span></label>
                                        <input type="text" class="form-control" name="rate" id="rate" value="<?php echo $transportation_rate;?>">
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
                            <div id="edit_prd_comb">
       
<?php
if($getEditCombinationProduct){

  foreach($getEditCombinationProduct as $comb){
?>
   <div class="row prod_comb" id="edit_prod_comb<?php echo $comb->comb_detail_id;  ?>">
                                <div class="col-md-12">
                                     <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="field-3" class="control-label">Products</label>
                                            <span style="color:red;">*</span>
                                            <select class="form-control combi_products" name="edit_comb_product[]" id="comb_product<?php echo $comb->comb_detail_id;  ?>">
                                             <option value="<?php echo $comb->product; ?>"><?php echo $comb->instruments_name; ?></option>   
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group" style="margin-top:25px">
                                            <button type="button" name="add" class="btn_remdove_prod_comb btn btn-danger btn-xs" id="" onclick="delete_combination_product(<?php echo $comb->comb_detail_id;?>)"><i class="fa fa-close"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
<?php
  }
}
  ?>                         


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

<?php
if($getEditCombinationProduct){
  $moq = $getEditCombinationProduct[0]->moq;
  $vli = $getEditCombinationProduct[0]->vli;
  $combination_id = $getEditCombinationProduct[0]->combination_id;
}else{
  $moq = 0;
  $vli = '';
  $combination_id = '';
}

  ?>                         <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-3" id="combmoqfield"  style="display:none;">
                                        <div class="form-group">
                                            <label for="field-3" class="control-label">MOQ (IN LTR)</label>
                                            <span style="color:red;">*</span>
                                            <input type="text" class="form-control" name="comb_moq" autocomplete="off" id="com_moq0" onkeyup="allow_decimal('com_moq0');" value="<?php echo $moq; ?>">
                                        </div>
                                        <input type="hidden" name="combination_id" value="<?php echo $combination_id; ?>">
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
                                            <input type="text" class="form-control" name="comb_credit_vli" id="com_credit_vli0" autocomplete="off" onkeyup="allow_decimal('com_credit_vli0');" value="<?php echo $vli; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <div class="row">
                                 <div class="form-group pull-right">
                                    <input type="submit" id="save" class="btn btn-info" value="Submit">
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
  <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
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

    <!-- Datatable init js -->
    <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>

    <!-- App js -->
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
          $('.select30').select2(); 
            var i = 2;
            $('#addmore_btn').click(function() {
                $('#dynamictasks').append('<hr><div id="row' + i + '" class="row">   <div class="col-md-12"> <div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">Location<span style="color:red;">*</span></label> <select class="form-control mand" name="hpcl_location[]" id="hpcl_location'+i+'"> <option value="">SELECT</option> <?php if($getHpclLocations !='') { foreach($getHpclLocations as $row) {?> <option value=" <?php echo $row->id;?> "> <?php echo $row->name;?> </option> <?php }} ?> </select> </div></div><div class="col-md-3"> <div class="form-group"> <label for="field-3" class="control-label">Product</label> <span style="color:red;">*</span> <select class="form-control mand select33" name="product[]" id="product'+i+'" required  onchange="add_instruments(); checkbulkandunit('+i+');" > <option value="">Select</option> <?php if($getAllProducts !='') { foreach($getAllProducts as $row) { ?> <option value="<?php echo $row->id;?>"><?php echo $row->instruments_name;?></option> <?php }} ?> </select> </div></div><div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">Approved Price</label> <span style="color:red;">*</span> <input type="text" class="form-control mand" name="approved_price[]" id="approved_price0" autocomplete="off" required> </div></div><div class="col-md-2 col-2"> <div class="form-group"> <label for="field-1" class="control-label">Pack Size</label> <span style="color:red;">*</span> <select class="form-control mand pack_sizeadd'+i+'" name="pack_size[]" onchange="check_unit_add('+i+')" id="pack_sizeadd'+i+'"> <option value="">SELECT</option> <?php if($getAllUnits !='') { foreach($getAllUnits as $row2) { ?> <option value="<?php echo $row2->id;?>"><?php echo $row2->shortname;?></option> <?php }} ?> </select> </div></div> <div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">Valid From<span class="list_price_unit0"></span></label> <span style="color:red;">*</span> <input type="date" class="form-control mand" name="valid_from[]" id="valid_from'+i+'" autocomplete="off"> </div></div><div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">Valid Till<span class="list_price_unit0"></span></label> <span style="color:red;">*</span> <input type="date" class="form-control mand" name="valid_till[]" id="valid_till'+i+'" autocomplete="off"> </div></div><!-- <div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">Price Validity<span class="list_price_unit0"></span></label> <span style="color:red;">*</span> <input type="text" class="form-control mand" name="price_validity[]" id="price_validity0" required autocomplete="off"> </div></div>--> <div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">CREDIT/VLI <span class="chk_unitadd'+i+'"></span></label> <span style="color:red;">*</span> <input type="text" class="form-control common_readonly" name="credit_vli[]" id="credit_vli0" autocomplete="off" onblur="getnetamt(0)"> </div></div><div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">Minimum Qty if Any</label> <span style="color:red;">*</span> <input type="text" class="form-control common_readonly" name="moq[]" autocomplete="off" id="moq'+i+'"> </div> <div class="col-md-12"> <div class="form-group"> <label>Historical Data Based MOQ?</label> <input type="checkbox" name="historical_moq[]" id="historical_moq'+i+'" onchange="check_for_historical('+i+');" value="1"> </div></div><div class="col-md-12 historical_div" style="display: none;" id="moqyear'+i+'"> <div class="form-group"> <label>Year <span style="color:red">*</span></label> <select class="form-control yearinput" name="year[]" id="year'+i+'" onchange="get_historical_moq('+i+');"> <option value="">Select</option> <?php for($kl=1;$kl<5;$kl++) {$number=str_pad($kl, 2, '0', STR_PAD_LEFT); $year=date('Y',strtotime("-".$kl." Years")); ?> <option value=" <?php echo $year;?>"> <?php echo $year;?></option> <?php } ?> </select> </div><span style="color:red;font-weight: bold;"></span> </div><div class="col-md-12 increment_div" style="display: none;" id="increment_div'+i+'"> <div class="form-group"> <label>Increase By % <span style="color:red">*</span></label> <input type="text" class="form-control increment_input" name="increment[]" id="increment'+i+'" onblur="get_historical_moq('+i+');"> </div><span style="color:red;font-weight: bold;"></span> </div></div><div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">Annexture<span style="color:red;">*</span></label> <input type="text" name="annexture[]" class="form-control" id="annexture'+i+'" > </div></div><div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">Annexture Upload<span style="color:red;">*</span></label> <input type="file" name="annex_upload[]" class="form-control" id="annex_upload'+i+'" > </div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:23px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="' + i + '"><i class="fa fa-close"></i></button></div></div></div><br/>');
                $('#product'+i).select2();

                // getproductname(i);
                i++;
            });


            $(document).on('click', '.btn_remove', function() {
                var button_id = $(this).attr("id");
                $('#row' + button_id + '').remove();
            });

            var date = new Date();
              var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
            jQuery('.datepicker').datepicker({
                startDate: date,
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
            });

            jQuery('.datepicker1').datepicker({
                 todayHighlight: true,
                format: 'dd-mm-yyyy',
                 autoclose: true,
            });


                        var k = 1;
            $('.add_more_product_comb').click(function() {

                $('.prod_comb0').append('<div id="row' + k + '" class="row"><div class="col-md-12"><div class="col-md-3"><div class="form-group"> <label for="field-3" class="control-label">Product</label> <span style="color:red;">*</span><select class="form-control mand  combi_product" name="comb_product[]" id="comb_product'+k+'"></select> </div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:23px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove_prod_comb btn btn-danger btn-xs" id="' + k + '"><i class="fa fa-close"></i></button></div></div></div><br/>');
                    
                    add_instruments_by_id(k);
               
                k++;
            });

            $(document).on('click', '.btn_remove_prod_comb', function() {
                var button_id = $(this).attr("id");
                $('#row' + button_id + '').remove();
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

        function check_unit(i) {
            var pack_size = $('.pack_size'+i+' option:selected').text();
            
            if($('.pack_size'+i).val() != '') {
                $('.chk_unit'+i).text('Per '+pack_size);
            } else {
                $('.chk_unit'+i).text('');
            } 
        }

        function check_unit_add(i) {
            var pack_size = $('.pack_sizeadd'+i+' option:selected').text();
            
            if($('.pack_sizeadd'+i).val() != '') {
                $('.chk_unitadd'+i).text('Per '+pack_size);
            } else {
                $('.chk_unitadd'+i).text('');
            } 
        }

        function delete_approval_product(id) {
            if(confirm('Are you sure you want to delete this product?')) {
                $.ajax({
                    type:"post",
                    url:"<?php echo page_url;?>Approval/delete_approval_product",
                    data:{id: id},
                    success:function(data){
                        if(data == 1) {
                          $('#remove_product'+id).remove();
                        }
                    }
                    });
            }
        }
        function delete_combination_product(id) {
            if(confirm('Are you sure you want to delete this product?')) {
                $.ajax({
                    type:"post",
                    url:"<?php echo page_url;?>Approval/delete_combination_product",
                    data:{id: id},
                    success:function(data){
                        if(data == 1) {
                          $('#edit_prod_comb'+id).remove();
                        }
                    }
                    });
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


    function checkbulkandunit(flag)
    {
        var prd=$("#product"+flag).val();

              // alert(flag+' '+prd)
         $.ajax({
            type: "post",
            url: "<?php echo page_url;?>Approval/getProductUnit",
            data: "prd=" + prd,
            success: function(data) {
              $("#pack_sizeadd"+flag).html(data);
            }
            });

    }

    function check_editbulkandunit(flag)
    {
        var prd=$("#edit_product"+flag).val();

         $.ajax({
            type: "post",
            url: "<?php echo page_url;?>Approval/getProductUnit",
            data: "prd=" + prd,
            success: function(data) {
              // alert(data)
              $("#edit_pack_size"+flag).html(data);
            }
            });

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
        $('input[name="edit_historical_moq[]"]').attr('disabled',false); 
        $("#moqyear_comb").css('display','none');
        $("#year_comb").attr('required',false);
        $(".historical_div_comb").css('display','none');
        $("#com_moq0").attr('readonly',false);
        $(".increment_div").css('display','none');
        $(".increment_input").attr('required',false);
        $('.yearinput').attr('required',false); 


    if(combination_approval == 1) {
      // alert("comb")
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
        $('input[name="edit_historical_moq[]"]').attr('disabled',true); 
        $(".historical_div").css('display','none');
        //alert('hi');
        $('.yearinput').attr('required',false);
        $(".increment_div").css('display','none');
        $(".increment_input").attr('required',false);


    }
}
    </script>

    <script language="javascript" type="text/javascript">
        $(document).ready(function() {
            check_credit_period();
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
            var combination_approval = $('input[name="combination_approval"]:checked').val(); 
              if(combination_approval == 1) {
                // alert("pre comb")
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
                $('input[name="edit_historical_moq[]"]').attr('disabled',true); 
                $(".historical_div").css('display','none');
                //alert('hi');
                $('.yearinput').attr('required',false);
                $(".increment_div").css('display','none');
                $(".increment_input").attr('required',false);


            }
        });

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

            add_instruments();
        }
    }

    function add_instruments()
    {

         var val = [];

        $('select[name="edit_product[]"]').each(function () {
        var b=$(this).val();
        val.push(b);
        });
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

            combinations_detail();

    }

     function add_instruments_by_id(flag)
    {
        
         var val = [];
         $('select[name="edit_product[]"]').each(function () {
        var b=$(this).val();
        val.push(b);
        });
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

    function combinations_detail(){
      var id = <?php echo $this->uri->segment(3); ?>;
      $.ajax({
            type: "post",
            url: "<?php echo page_url;?>/Approval/combinations_detail",
            data: "id=" + id,
            success: function(data) {
            // alert(data)
            }
            });
    }

    function check_for_historical(id)
    {
      // alert(id);
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
    function check_for_historical_edit(id)
    {
      // alert(id);
         $("#edit_moqyear"+id).css('display','none');
         $("#edit_year"+id).attr('required',false);
         $("#edit_increment_div"+id).css('display','none');
         $("#edit_increment"+id).attr('required',false);
         $("#edit_moq"+id).attr('readonly',false);
        if($('#edit_historical_moq' + id).is(":checked"))
        {
            $("#edit_moqyear"+id).css('display','');
            $("#edit_year"+id).attr('required',true);

            $("#edit_increment_div"+id).css('display','');
            $("#edit_increment"+id).attr('required',true);
            $("#edit_moq"+id).attr('readonly',true);
        }

    }
    

    function get_historical_moq(id)
    {
      // alert(id)
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