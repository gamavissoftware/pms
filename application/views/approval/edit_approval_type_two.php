<?php
  $CI = &get_instance();
  $CI->load->model('Salescrm_model', 'salescrm');
  $getAllUnits = $CI->salescrm->getAllUnits();
  $getAllProducts = $CI->salescrm->getAllProducts();
  $getHpclLocations = $CI->salescrm->getHpclLocations();
  $getLastInsertedCode = $CI->salescrm->getLastInsertedCode_TYPE_2();
  $getEditApproval = $CI->salescrm->getEditApprovalTypeTwo($this->uri->segment(3));
  date_default_timezone_set("Asia/Kolkata");
  $type = "";
  $current_date = "";
  $customer_name = "";
  $customer_tds = "";
  $customer_code = "";
  $invoice = "";
  $invoice_date='';
  $annexture_name = "";
  $annexture_upload = "";
  if ($getEditApproval != '') {
    foreach ($getEditApproval as $row);
    $type = $row->type;
    $current_date = $row->current_date;
    $customer_name = $row->customer_name;
    $customer_tds = $row->customer_tds;
    $customer_tcs = $row->customer_tcs;
    $customer_code = $row->customer_code;
    $invoice = $row->invoice;
    $invoice_date=$row->invoice_date;
    $annexture_name = $row->annexture_name;
    $annexture_upload = $row->annexture;
    // $credit_period = $row->credit_period;
  }
  $getEditProductApproval = $CI->salescrm->getEditProductApprovalTypeTwo($this->uri->segment(3));
  // print_r($getEditApproval);
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
    <link href="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
    <link href="<?php echo assets_url; ?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
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
      .select2-container .select2-selection--single {
      width: 100% !important;
      height: 35px !important;
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
              <h4 class="page-title">Type II Approval Form</h4>
            </div>
          </div>
        </div>
        <!-- end page title end breadcrumb -->
        <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
        <form id="quotation" method="post" action="<?php echo page_url; ?>Approval/update_approval_type_two/<?php echo $this->uri->segment(3); ?>/<?php echo $this->uri->segment(4); ?>/<?php echo $this->uri->segment(5); ?>/<?php echo $this->uri->segment(6); ?>/<?php echo $this->uri->segment(7); ?>/<?php echo $this->uri->segment(8); ?>" autocomplete="off" onsubmit="return validate();" enctype="multipart/form-data">
          <div class="row">
            <div class="col-sm-12">
              <div class="card-box table-responsive">
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-1">
                      <div class="form-group">
                        <label for="field-2" class="control-label">TYPE<span style="color:red;">*</span></label>
                        <select name="type" id="type" required class="form-control">
                          <option value="">Select</option>
                          <option value="TYPE II" <?php if ($type  == 'TYPE II') {
                            echo 'selected';
                            } ?>>TYPE II</option>
                          <option value="TYPE III" <?php if ($type  == 'TYPE III') {
                            echo 'selected';
                            } ?>>TYPE III</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Approval Date <span style="color:red;">*</span></label>
                        <input type="date" class="form-control" name="current_date" value="<?php echo date('Y-m-d', strtotime($current_date)); ?>">
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Customer Name <span style="color:red;">*</span></label><br>
                        <select name="customer_name" id="customer_name" class="form-control select1" required onchange="get_customer_code();">
                          <option value=""></option>
                          <?php
                            $r = $this->db->select('id,customer_name')->from('hpcl_direct_customer')->get();
                            if ($r->num_rows() > 0) {
                              foreach ($r->result() as $rr) {
                            ?>
                          <option value="<?php echo $rr->id; ?>" <?php if ($rr->id == $customer_name) {
                            echo 'selected';
                            } ?>><?php echo $rr->customer_name; ?></option>
                          <?php }
                            }
                            ?>
                        </select>
                      </div>
                    </div>
                    <script type="text/javascript">
                      function get_customer_code() {
                        var customer = $("#customer_name").val();
                        $.ajax({
                          type: "post",
                          url: "<?php echo page_url; ?>Approval/get_customer_code",
                          data: "customer=" + customer,
                          success: function(data) {
                            var d = data.split("|");
                            if (d[0] != '') {
                              $("#customer_code").val(d[0]);
                              $("#customer_code").attr('readonly', true);
                            } else {
                              $("#customer_code").val('');
                              $("#customer_code").attr('readonly', false);
                            }
                            if (d[1] != '' && d[1] > 0) {
                              $("#customer_tds").val(d[1]);
                              $("#customer_tds").attr('readonly', true);
                            } else {
                              $("#customer_tds").val(0);
                              $("#customer_tds").attr('readonly', false);
                            }
                          }
                        });
                      }
                    </script>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Customer Code <span style="color:red;">*</span></label><br>
                        <input type="text" name="customer_code" id="customer_code" class="form-control" value="<?php echo $customer_code; ?>" readonly>
                      </div>
                    </div>
                   
                  


                              
<!-- 
                                 <div class="col-md-2">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Annexture<span style="color:red;">*</span></label><br>
                                <input type="text" name="annexture" id="annexture" class="form-control"  value="<?php echo $annexture_name;?>" required>

                                </div>
                                </div>
 -->
                             <!--     <div class="col-md-2">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Annexture File</label><br>
                                <input type="file" name="overall_annexture" id="overall_annexture" class="form-control">
                                <input type="hidden" name="old_annexture" id="old_annexture" value="<?php echo $annexture_upload;?>">
                                <a href="<?php echo page_url1;?>type_two_annexure/<?php echo $annexture_upload;?>" target="_blank">Click Here</a>

                                </div>
                                </div> -->



                    <!--  <div class="col-md-2">
                      <div class="form-group">
                          <label for="field-2" class="control-label">Combination Approval <span style="color:red;">*</span></label><br>
                          <input type="checkbox" name="combination_approval" id="combination_approval" value="1" onchange="chkIfCombApproved()" style="width: 30px;height: 25px;">
                      </div>
                      </div> -->
                  </div>
                </div>
                <hr>
                <?php if ($getEditProductApproval != '') {
                  $i = 0;
                  foreach ($getEditProductApproval as $row1) {
                  ?>
                <div class="row" id="remove_product<?php echo $row1->id; ?>">
                  <div class="col-md-12">
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Location<span style="color:red;">*</span></label>
                        <select class="form-control mand" name="edit_hpcl_location[]" id="edit_hpcl_location<?php echo $i; ?>">
                          <option value="">SELECT</option>
                          <?php if ($getHpclLocations != '') {
                            foreach ($getHpclLocations as $row) { ?>
                          <option value="<?php echo $row->id; ?>" <?php if ($row->id == $row1->location) {
                            echo 'selected';
                            } ?>><?php echo $row->name; ?></option>
                          <?php }
                            } ?>
                        </select>
                      </div>
                    </div>
                    <input type="hidden" name="approval_detail_id_type_two[]" value="<?php echo $row1->id; ?>">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="field-3" class="control-label">Product</label>
                        <span style="color:red;">*</span>
                        <select class="form-control mand select30" name="edit_product[]" id="edit_product<?php echo $i; ?>" required onchange="add_instruments(); checkbulkandunitedit(<?php echo $i; ?>);">
                          <option value="">Select</option>
                          <?php if ($getAllProducts != '') {
                            foreach ($getAllProducts as $row) { ?>
                          <option value="<?php echo $row->id; ?>" <?php if ($row->id == $row1->product_id) {
                            echo 'selected';
                            } ?>><?php echo $row->instruments_name; ?>-<?php echo $row->pack_size; ?></option>
                          <?php }
                            } ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="field-3" class="control-label">Unit</label>
                        <span style="color:red;">*</span>
                        <select class="form-control mand" name="edit_unit[]" id="editunit<?php echo $i; ?>" required onchange="">
                          <!-- <option value="">Select</option> -->
                          <!-- <option value="">SELECT</option> -->
                          <?php if ($getAllUnits != '') {
                            foreach ($getAllUnits as $row2) { ?>
                          <?php if ($row2->id == $row1->pack_size) {
                            ?>
                          <option value="<?php echo $row2->id; ?>"><?php echo $row2->shortname; ?></option>
                          <?php
                            } ?>
                          <!-- <option value="<?php echo $row2->id; ?>" <?php if ($row2->id == $row1->pack_size) {
                            echo 'selected';
                            } ?>><?php echo $row2->shortname; ?></option> -->
                          <?php  }
                            } ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Approved Price</label>
                        <span style="color:red;">*</span>
                        <input type="text" class="form-control mand" name="edit_approved_price[]" id="approved_price<?php echo $i; ?>" autocomplete="off" required onkeyup="allow_decimal('approved_price<?php echo $i; ?>');" value="<?php echo $row1->approved_price; ?>">
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Valid From<span class="list_price_unit<?php echo $i; ?>"></span></label>
                        <span style="color:red;">*</span>
                        <input type="date" class="form-control mand" name="edit_valid_from[]" id="valid_from<?php echo $i; ?>" required autocomplete="off" value="<?php echo date('Y-m-d', strtotime($row1->validity_from)); ?>">
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Valid Till<span class="list_price_unit<?php echo $i; ?>"></span></label>
                        <span style="color:red;">*</span>
                        <input type="date" class="form-control mand" name="edit_valid_till[]" id="valid_till<?php echo $i; ?>" value="<?php echo date('Y-m-d', strtotime($row1->validity_to)); ?>" required autocomplete="off">
                      </div>
                    </div>
                    <div class="col-md-1">
                      <div class="form-group">
                        <label for="field-2" class="control-label">CREDIT DAYS </label>
                        <span style="color:red;">*</span>
                        <input type="text" class="form-control mand common_readonly" name="edit_credit_days[]" id="credit_days<?php echo $i; ?>" autocomplete="off" onkeyup="allow_decimal('credit_days<?php echo $i; ?>');" value="<?php echo $row1->credit_days; ?>">
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="field-2" class="control-label">CFA Commision (per LTR)</label>
                        <span style="color:red;">*</span>
                        <input type="text" class="form-control mand common_readonly" name="edit_commision[]" autocomplete="off" id="commision<?php echo $i; ?>" onkeyup="allow_decimal('commision<?php echo $i; ?>');" value="<?php echo $row1->commision; ?>">
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Transportation<span style="color:red;">*</span></label>
                        <select class="form-control mand" name="edit_transportation[]" id="edit_transportation<?php echo $i; ?>" onchange="check_transport_rate_edit(<?php echo $i; ?>);">
                          <option value="">Select</option>
                          <option value="1" <?php if ($row1->transport_type  == '1') {
                            echo 'selected';
                            } ?>>EXMI</option>
                          <option value="2" <?php if ($row1->transport_type  == '2') {
                            echo 'selected';
                            } ?>>DELIVERED</option>
                        </select>
                      </div>
                    </div>
                    <?php
                      if ($row1->transport_type  == '1') {
                        $a = '';
                      } else {
                        $a = 'display:none;';
                      }
                      ?>
                    <div class="col-md-2" style="<?php echo $a; ?>" id="edit_trnsrate<?php echo $i; ?>">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Transportation Rate<span style="color:red;">*</span></label>
                        <input type="number" step="0.01" name="edit_trate[]" class="form-control" onkeyup="allow_decimal('trate<?php echo $i; ?>')" ; value="<?php echo $row1->transport_rate; ?>">
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Annexture<span style="color:red;">*</span></label>
                        <input type="text" name="edit_remarks[]" class="form-control" id="remarks<?php echo $i; ?>"  value="<?php echo $row1->annexure; ?>">
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Annexture Upload<span style="color:red;">*</span></label>
                        <input type="file" name="edit_annex_upload[]" class="form-control" id="annex_upload<?php echo $i; ?>">
                        <?php if ($row1->annexure_upload) {
                          ?>
                        <a href="<?php echo site_http_root . 'type_two_annexure/' . $row1->annexure_upload; ?>" download class="btn-sm btn-success">Download</a>
                        <?php
                          }  ?>
                      </div>
                    </div>
                    <div class="col-md-1">
                      <div class="form-group" style="margin-top:25px">
                        <button type="button" class="btn btn-danger" name="add" id="delete_btn" onclick="delete_approval_product_type_two(<?php echo $row1->id; ?>)"><i class="fa fa-times"></i></button>
                      </div>
                    </div>
                  </div>
                </div>
                <hr>
                <?php $i++;
                  }
                  } ?>
                <!-- New record add -->
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Location<span style="color:red;">*</span></label>
                        <select class="form-control " name="hpcl_location[]" id="hpcl_location0">
                          <option value="">SELECT</option>
                          <?php if ($getHpclLocations != '') {
                            foreach ($getHpclLocations as $row) { ?>
                          <option value="<?php echo $row->id; ?>"><?php echo $row->name; ?></option>
                          <?php }
                            } ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="field-3" class="control-label">Product</label>
                        <span style="color:red;">*</span>
                        <select class="form-control  select30" name="product[]" id="product0" onchange="add_instruments(); checkbulkandunit(0);">
                          <option value="">Select</option>
                          <?php if ($getAllProducts != '') {
                            foreach ($getAllProducts as $row) { ?>
                          <option value="<?php echo $row->id; ?>"><?php echo $row->instruments_name; ?>-<?php echo $row->pack_size; ?></option>
                          <?php }
                            } ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="field-3" class="control-label">Unit</label>
                        <span style="color:red;">*</span>
                        <select class="form-control " name="unit[]" id="unit0" onchange="">
                          <option value="">Select</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Approved Price</label>
                        <span style="color:red;">*</span>
                        <input type="text" class="form-control " name="approved_price[]" id="approved_price0" autocomplete="off" onkeyup="allow_decimal('approved_price0');">
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Valid From<span class="list_price_unit0"></span></label>
                        <span style="color:red;">*</span>
                        <input type="date" class="form-control " name="valid_from[]" id="valid_from0" autocomplete="off">
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Valid Till<span class="list_price_unit0"></span></label>
                        <span style="color:red;">*</span>
                        <input type="date" class="form-control " name="valid_till[]" id="valid_till0" autocomplete="off">
                      </div>
                    </div>
                    <div class="col-md-1">
                      <div class="form-group">
                        <label for="field-2" class="control-label">CREDIT DAYS </label>
                        <span style="color:red;">*</span>
                        <input type="text" class="form-control  common_readonly" name="credit_days[]" id="credit_days0" autocomplete="off" onkeyup="allow_decimal('credit_days0');">
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="field-2" class="control-label">CFA Commision (per LTR)</label>
                        <span style="color:red;">*</span>
                        <input type="text" class="form-control  common_readonly" name="commision[]" autocomplete="off" id="commision0" onkeyup="allow_decimal('commision0');" value="0">
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Transportation<span style="color:red;">*</span></label>
                        <select class="form-control " name="transportation[]" id="transportation0" onchange="check_transport_rate(0);">
                          <option value="">Select</option>
                          <option value="1">EXMI</option>
                          <option value="2">DELIVERED</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-2" style="display:none" id="trnsrate0">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Transportation Rate<span style="color:red;">*</span></label>
                        <input type="number" step="0.01" name="trate[]" class="form-control" onkeyup="allow_decimal('trate0')" ;>
                      </div>
                    </div>
                    <div class="col-md-2" >
                      <div class="form-group">
                        <label for="field-2" class="control-label">Annexture<span style="color:red;">*</span></label>
                        <input type="text" name="remarks[]" class="form-control" id="remarks0">
                      </div>
                    </div>
                    <div class="col-md-2" >
                      <div class="form-group">
                        <label for="field-2" class="control-label">Annexture Upload<span style="color:red;">*</span></label>
                        <input type="file" name="annex_upload[]" class="form-control" id="annex_upload[]">
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

                <div class="row" style="margin-top:40px;">
                  <div class="col-md-4"></div>
                  <div class="col-md-4">
                  <div class="form-group text-center">
                    <input type="submit" id="saves" style="width:50%" class="btn btn-success" value="Submit">
                  </div>
                </div>
                </div>
              </div>
        </form>
        </div>
        </div>
        <!-- Footer -->
        <?php $this->load->view('common/footer'); ?>
        <!-- End Footer -->
      </div>
      <!-- end container -->
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
    <script src="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
    <!-- Datatable init js -->
    <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>
    <!-- App js -->
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
    <script type="text/javascript">
      $(document).ready(function() {
        $('.select30').select2();
        $('.select1').select2({
          tags: true
        });
        var i = 1;
        $('#addmore_btn').click(function() {
          $('#dynamictasks').append('<hr><div id="row' + i + '" class="row" style="margin-top:5px"><div class="col-md-12">  <div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Location<span style="color:red;">*</span></label><select class="form-control mand" name="hpcl_location[]" id="hpcl_location' + i + '"><option value="">SELECT</option><?php if ($getHpclLocations != '') {
        foreach ($getHpclLocations as $row) { ?> <option value="<?php echo $row->id; ?>"><?php echo $row->name; ?></option> <?php }
        } ?></select></div></div><div class="col-md-3"> <div class="form-group"> <label for="field-3" class="control-label">Product</label> <span style="color:red;">*</span><select class="form-control mand select3' + i + '" name="product[]" id="product' + i + '" required onchange="add_instruments();checkbulkandunit(' + i + ');"><option value="">Select</option> <?php if ($getAllProducts != '') {
        foreach ($getAllProducts as $row) { ?> <option value="<?php echo $row->id; ?>"><?php echo $row->instruments_name; ?>-<?php echo $row->pack_size; ?></option> <?php }
        } ?> </select> </div></div><div class="col-md-2"><div class="form-group"><label for="field-3" class="control-label">Unit</label><span style="color:red;">*</span><select class="form-control mand" name="unit[]" id="unit' + i + '" required onchange=""><option value="">Select</option></select></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Approved Price</label><span style="color:red;">*</span><input type="text" class="form-control mand notext' + i + '1" name="approved_price[]" id="approved_price' + i + '" autocomplete="off" required onkeyup="checkappprice(' + i + ',1);"></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Valid From<span class="list_price_unit0"></span></label><span style="color:red;">*</span><input type="date" class="form-control mand" name="valid_from[]" id="valid_from' + i + '" required autocomplete="off"></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Valid Till<span class="list_price_unit0"></span></label><span style="color:red;">*</span><input type="date" class="form-control mand" name="valid_till[]" id="valid_till' + i + '" required autocomplete="off"></div></div><div class="col-md-1"><div class="form-group"><label for="field-2" class="control-label">CREDIT DAYS </label><span style="color:red;">*</span><input type="text" class="form-control mand common_readonly" name="credit_days[]" id="credit_days0" autocomplete="off" onkeyup="allow_decimal(credit_days' + i + ');"></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">CFA Commision (per LTR)</label><span style="color:red;">*</span><input type="text" class="form-control mand common_readonly" name="commision[]" autocomplete="off" id="commision0" onkeyup="allow_decimal(commision' + i + ');" value="0"></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Transportation<span style="color:red;">*</span></label><select class="form-control mand" name="transportation[]" id="transportation' + i + '" onchange="check_transport_rate(' + i + ');"><option value="">Select</option><option value="1">EXMI</option><option value="2">DELIVERED</option></select></div></div><div class="col-md-2" style="display:none" id="trnsrate' + i + '"><div class="form-group"><label for="field-2" class="control-label">Transportation Rate<span style="color:red;">*</span></label><input type="number" step="0.01" name="trate[]" class="form-control" id="trate' + i + '" onkeyup="allow_decimal(trate' + i + ')";></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Annex<span style="color:red;">*</span></label><input type="text" class="form-control" name="remarks[]" id="remarks'+i+'"></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Annexture Upload<span style="color:red;">*</span></label><input type="file" name="annex_upload[]" class="form-control"></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:23px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="' + i + '"><i class="fa fa-close"></i></button></div></div></div><br/>');
          initializeSelect2(i);
          //   getproductname(i);
          // chkIfCombApproved();
          i++;
        });
        $(document).on('click', '.btn_remove', function() {
          var button_id = $(this).attr("id");
          $('#row' + button_id + '').remove();
        });
        var k = 1;
        $('.add_more_product_comb').click(function() {
          $('.prod_comb0').append('<div id="row' + k + '" class="row"><div class="col-md-12"><div class="col-md-3"><div class="form-group"> <label for="field-3" class="control-label">Product</label> <span style="color:red;">*</span><select class="form-control mand  combi_product" name="comb_product[]" id="comb_product' + k + '"></select> </div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:23px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove_prod_comb btn btn-danger btn-xs" id="' + k + '"><i class="fa fa-close"></i></button></div></div></div><br/>');
          add_instruments_by_id(k);
          k++;
        });
        // var j = 1;
        //  $('#add_more_combination').click(function() {
        //      var flagnew=parseInt(j);
        //      $('.combination_repeat').append('<div id="row' + j + '" class="row"><div class="col-md-12"><hr><div class="row"><div class="col-md-12"><div class="col-md-3"> <div class="form-group"> <label for="field-2" class="control-label">Combination Type<span style="color:red;">*</span></label> <select class="form-control" name="transportation" id="transportation" onchange="chk_transportation()"> <option value="">Select</option> <option value="1">By MOQ</option> <option value="2">By VLI</option> <option value="2">By MOQ & VLI</option> </select> </div></div><div class="col-md-5"></div><div class="col-md-3"><div class="form-group pull-left" style="padding-top:23px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove_prod_comb_row btn btn-danger" id="' + j + '"><i class="fa fa-close"></i></button></div></div></div></div><div class="row prod_comb'+flagnew+'"><div class="col-md-12"><div class="col-md-3"> <div class="form-group"> <label for="field-3" class="control-label">Product</label> <span style="color:red;">*</span><select class="form-control mand select3'+j+'" name="product[]" id="product0" required><option value="">Select</option> <?php if ($getAllProducts != '') {
        foreach ($getAllProducts as $row) { ?> <option value="<?php echo $row->id; ?>"><?php echo $row->instruments_name; ?></option> <?php }
        } ?> </select> </div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:23px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" class="btn btn-warning btn-xs" onclick="add_new_products_for_combination('+j+')" data-id="'+j+'" name="add"><i class="fa fa-plus"></i></button></div></div></div><div class="row"> <div class="col-md-12"> <div class="col-md-3"> <div class="form-group"> <label for="field-3" class="control-label">MOQ</label> <span style="color:red;">*</span> <input type="text" class="form-control mand" name="moq[]" autocomplete="off" id="moq0" value="0"> </div></div><div class="col-md-3"> <div class="form-group"> <label for="field-3" class="control-label">VLI</label> <span style="color:red;">*</span> <input type="text" class="form-control mand" name="credit_vli[]" id="credit_vli0" autocomplete="off"> </div></div></div></div></div></div><br/>');
        //      initializeSelect2(j);
        //      j++;
        //  });
        $(document).on('click', '.btn_remove_prod_comb', function() {
          var button_id = $(this).attr("id");
          $('#row' + button_id + '').remove();
        });
        function add_new_products_for_combination(id) {
          var flag = id;
          var flagew = parseInt(flag) + 1;
          $('.prod_comb' + flag).append('<div id="row' + flagew + '" class="row"><div class="col-md-12"><div class="col-md-3"><div class="form-group"> <label for="field-3" class="control-label">Product</label> <span style="color:red;">*</span><select class="form-control mand select3' + flagew + '" name="product[]" id="product0" required><option value="">Select</option> <?php if ($getAllProducts != '') {
        foreach ($getAllProducts as $row) { ?> <option value="<?php echo $row->id; ?>"><?php echo $row->instruments_name; ?></option> <?php }
        } ?> </select> </div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:23px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove_prod_comb btn btn-danger btn-xs" id="' + flagew + '"><i class="fa fa-close"></i></button></div></div></div><br/>');
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
        if ($("#transportation").val() == 2) {
          $("#chk_transportation").css('display', '');
          $("#rate").attr('required', true);
        }
      }
      function initializeSelect2(i) {
        $('.select3' + i).select2();
      }
      function check_unit(i) {
        var pack_size = $('.pack_size' + i + ' option:selected').text();
        if ($('.pack_size' + i).val() != '') {
          $('.chk_unit' + i).text('Per ' + pack_size);
        } else {
          $('.chk_unit' + i).text('');
        }
      }
      function check_credit_period() {
        $("#chk_credit").css('display', 'none');
        $("#credit_period").attr('required', false);
        if ($("#payment_terms").val() == 2) {
          $("#chk_credit").css('display', '');
          $("#credit_period").attr('required', true);
        }
      }
      function validate() {
        $("#saves").attr('disabled', false);
        $("#saves").val('Submit');
        var isValid = 0;
        $(".mand").each(function() {
          var element = $(this).val();
          if (element == "") {
            isValid = 1;
          }
        });
        if (isValid == 0) {
          $("#saves").attr('disabled', true);
          $("#saves").val('Please Wait..');
          return true;
        } else {
          $("#saves").attr('disabled', false);
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
      function allow_decimal(data) {
        var self = $("#" + data);
        self.val(self.val().replace(/[^0-9\.]/g, ''));
        if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
          evt.preventDefault();
        }
      }
      function checkappprice(i, j) {
        var self = $(".notext" + i + j);
        self.val(self.val().replace(/[^0-9\.]/g, ''));
        if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
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
        if (combination_approval == 1) {
          $('.common_readonly').attr('readonly', true);
          $('.common_readonly').val(0);
          $('.combination_repeat').css('display', '');
          $('.combination_head').css('display', '');
          $(".combi_product").addClass('mand');
          $("#combination_type").addClass('mand');
          $("#com_moq0").addClass('mand');
          $("#com_credit_vli0").addClass('mand');
          chk_combination_type();
        }
      }
      function chk_combination_type() {
        var ctype = $("#combination_type").val();
        if (ctype != '') {
          if (ctype == 1) {
            $("#combmoqfield").css('display', '');
            $("#com_moq0").addClass('mand');
            $("#combcreditfield").css('display', 'none');
            $("#com_credit_vli0").removeClass('mand');
      
          } else if (ctype == 2) {
            $("#combmoqfield").css('display', 'none');
            $("#com_moq0").removeClass('mand');
            $("#combcreditfield").css('display', '');
            $("#com_credit_vli0").addClass('mand');
          } else {
            $("#combmoqfield").css('display', '');
            $("#com_moq0").addClass('mand');
            $("#combcreditfield").css('display', '');
            $("#com_credit_vli0").addClass('mand');
          }
        }
      }
      function add_instruments() {
        var val = [];
        $('select[name="product[]"]').each(function() {
          var a = $(this).val();
          val.push(a);
        });
        var dval = val.join(',');
        $.ajax({
          type: "post",
          url: "<?php echo page_url; ?>/Approval/getproducts_byid",
          data: "prd=" + dval,
          success: function(data) {
            $(".combi_product").html(data);
          }
        });
      }
      function add_instruments_by_id(flag) {
        var val = [];
        $('select[name="product[]"]').each(function() {
          var a = $(this).val();
          val.push(a);
        });
        var dval = val.join(',');
        $.ajax({
          type: "post",
          url: "<?php echo page_url; ?>/Approval/getproducts_byid",
          data: "prd=" + dval,
          success: function(data) {
            $("#comb_product" + flag).html(data);
          }
        });
      }
      function checkbulkandunit(flag) {
        var prd = $("#product" + flag).val();
        $.ajax({
          type: "post",
          url: "<?php echo page_url; ?>Approval/getProductUnit",
          data: "prd=" + prd,
          success: function(data) {
            $("#unit" + flag).html(data);
          }
        });
      }
      function checkbulkandunitedit(flag) {
        var prd = $("#edit_product" + flag).val();
        $.ajax({
          type: "post",
          url: "<?php echo page_url; ?>Approval/getProductUnit",
          data: "prd=" + prd,
          success: function(data) {
            $("#editunit" + flag).html(data);
          }
        });
      }
      function check_transport_rate(i) {
        $("#trnsrate" + i).css('display', 'none');
        $("#trate" + i).removeClass('mand');
        $("#trate" + i).attr('required', false);
        var trns = $("#transportation" + i).val();
        if (trns == 1) {
          $("#trnsrate" + i).css('display', '');
          $("#trate" + i).addClass('mand');
          $("# " + i).attr('required', true);
        }
      }
      function check_transport_rate_edit(i) {
        $("#edit_trnsrate" + i).css('display', 'none');
        $("#trate" + i).removeClass('mand');
        $("#trate" + i).attr('required', false);
        var trns = $("#edit_transportation" + i).val();
        // alert(trns)
        if (trns == 1) {
          $("#edit_trnsrate" + i).css('display', '');
          $("#trate" + i).addClass('mand');
          $("#trate" + i).attr('required', true);
        }
      }
      function delete_approval_product_type_two(id) {
            if(confirm('Are you sure you want to delete this product?')) {
                $.ajax({
                    type:"post",
                    url:"<?php echo page_url;?>Approval/delete_approval_product_type_two",
                    data:{id: id},
                    success:function(data){
                        if(data == 1) {
                          $('#remove_product'+id).remove();
                        }
                    }
                    });
            }
        }
    </script>
  </body>
</html>