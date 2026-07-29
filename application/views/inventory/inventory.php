<?php
  $CI = &get_instance();
  $CI->load->model('Salescrm_model', 'salescrm');
  $getAllProducts = $CI->salescrm->getAllProducts();
  $getAllTransporters = $CI->salescrm->getAllTransporters();
  $getAllVendors = $CI->salescrm->getAllVendors();
  $getRackLocation = $CI->salescrm->getRackLocation();
  $role=$_SESSION['logged_in']['role'];

  /**  CHECK IF ALL PREVIOUS PURCHASE IS SEND TO TALLY **/
  $bills='';
  $last7days=date('Y-m-d',strtotime('-7 Days'));
  $r=$this->db->select('id,bill_no')->from('inventory')->where('currentdate<',$last7days)->where('send_to_tally',0)->where('currentdate!=','0000-00-00')->get(); 
  if($r->num_rows()>0)
  {
    
    foreach($r->result() as $rowsss)
    {
    $bills.=$rowsss->bill_no."<br/>";
    }

    echo "<strong style='color:red;font-weight:bold;'>Following Bills which are more tha 7 days old are not send to Tally. Please import it to tally to continue booking purchases<br/>".$bills."</strong>"; exit;
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
    <title><?php echo sitetitle; ?></title>
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
    <script type="text/javascript" src="<?php echo assets_url; ?>ckeditor/ckeditor.js"></script>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
    <style>
      .select2-container {
      width: 100% !important;
      }
      .marginbottom {
      margin-bottom: 20px;
      }
      .iii[disabled] {
      pointer-events: none;
      opacity: 0.49;
      }
      .iii i {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 1;
      font-size: 100px;
      }
      .sidenav {
      height: 100%;
      width: 0;
      position: fixed;
      z-index: 1;
      top: 0;
      right: 0;
      background-color: #fff;
      border: 1px solid lightgray;
      overflow-x: hidden;
      transition: 0.5s;
      padding-top: 50px;
      padding-bottom: 50px;
      z-index: 100;
      }
      .sidenav a {
      padding: 8px 8px 8px 32px;
      text-decoration: none;
      font-size: 25px;
      color: #818181;
      display: block;
      transition: 0.3s;
      }
      .sidenav a:hover {
      color: #818181;
      }
      .sidenav .closebtn {
      position: absolute;
      top: -15px;
      right: 5px;
      font-size: 36px;
      margin-left: 50px;
      }
      @media screen and (max-height: 450px) {
      .sidenav {
      padding-top: 15px;
      }
      .sidenav a {
      font-size: 18px;
      }
      }
      .todo-box {
      border: 1px solid lightgray;
      border-radius: 5px;
      height: none;
      }
      .all-notes {
      height: 50px;
      padding: 15px;
      border-bottom: 1px solid #cdcdcd;
      }
      .all-notes p {
      font-weight: 600;
      }
      .all-notes i {
      color: #3d48c4;
      }
      .to-list {
      padding: 20px;
      }
      .todo-list {
      margin: 10px 0;
      overflow-y: auto;
      height: 535px;
      }
      .todo-list .todo-item {
      padding: 15px;
      margin: 5px 0;
      border-radius: 0;
      background: #f7f7f7;
      }
      .search-page h3 {
      font-weight: 600;
      }
      .search-page h3 span {
      background: #fff1ea;
      color: #f9ab00;
      border-radius: 5px;
      padding: 5px;
      font-size: 20px;
      }
      .search-page p {
      color: black;
      /* font-size: 14px; */
      margin: 0;
      }
      .search-page p i {
      margin-right: 10px;
      }
      .search-page h5 {
      font-size: 17px;
      margin: 0px;
      padding: 5px 0px;
      font-weight: 700;
      border-bottom: 1px solid #25272e52;
      margin-bottom: 10px;
      }
      .search-page table {
      width: 100%;
      }
      .search-page table th {
      padding: 5px;
      text-align: center;
      border: 1px solid lightgray;
      color: black;
      background-color: whitesmoke;
      }
      .search-page table td {
      border: 1px solid lightgray;
      padding: 5px;
      text-align: center;
      color: black;
      }
    </style>
  </head>
  <body>
    <!-- Navigation Bar-->
    <header id="topnav">
      <?php $this->load->view('common/nav-menu'); ?>
    </header>
    <div class="wrapper">
      <div class="container">
        <div class="row" style="margin-top:20px;">
          <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
            <div class="page-title-box">
              <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success" name="action_button"><i class="fa fa-arrow-left"></i>Back</button></a>
              <h4 class="page-title text-center">PURCHASE FORM</h4>
            </div>
          </div>
          <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12"><?php echo $this->session->flashdata('message'); ?></div>
        </div>
        <form method="post" action="<?php echo page_url; ?>Inventory/add_inventory" onsubmit="return validate();">
          <input type="hidden" id="hpcl_vendor" value="0">
          <div class="row">
            <div class="col-sm-12">
              <div class="card-box">
                <div class="row">
                  <div class="col-sm-12 col-xs-12 col-md-12">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="field-1" class="control-label">Purchase Date</label>
                        <span style="color:red;">*</span>
                        <input type="text" name="current_date" id="datepicker" required class="form-control mand" autocomplete="off" >
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="field-1" class="control-label">Bill No</label>
                        <span style="color:red;">*</span>
                        <input type="text" name="bill_no" class="form-control mand"  required autocomplete="off">
                      </div>
                    </div>
                    <div class="col-md-3">
                    <div class="form-group">
                      <label for="field-2" class="control-label">Company</label>
                      <span id="error_vendor_name" style="color:red;">*</span>
                      <select class="form-control" name="pur_company" id="pur_company" required onchange="getParty()" required>
                        <option value="">Select Company</option>
                        <?php if ($getRackLocation != '') {
                          foreach ($getRackLocation as $row1) { ?>
                        <option value="<?php echo $row1->id; ?>"><?php echo $row1->companyname; ?></option>
                        <?php }
                          } ?>
                      </select>
                      
                    </div>
                  </div>
                  <div class="col-md-3">
                      <div class="form-group">
                        <label for="field-1" class="control-label">Party</label>
                        <span style="color:red;">*</span>
                        <!-- <span class="pull-right"><a href="javascript;:" data-toggle="modal" data-target="#con-close-modal"><i class="fa fa-plus" title="Add New Party"></i></a></span> -->
                        <select name="party" class="form-control mand" id="pur_party" required onchange="check_for_hpcl();">
                        
                        </select>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <label>Payment Type<span style="color: red;">*</span></label>
                      <span class="input-icon icon-right" style="margin-bottom:10px">
                        <select class="form-control mand"  required name="payment_type" id="payment_type" required="" onchange="checkpdf()">
                          <option value="">SELECT PAYMENT TYPE</option>
                          <option value="2">Cash</option>
                          <option value="3">Online</option>
                          <option value="4">PDC</option>
                          <option value="5">Credit</option>
                          <option value="6" selected>Advance</option>
                        </select>
                      </span>
                    </div>
                    <script type="text/javascript">
                      $(document).ready(function() {

                     

                      
                          var payment_type = $("#payment_type").val();
                          if (payment_type == 4 || payment_type == 5) {
                              $("#credit_days").addClass('mand');
                              $("#paymentterms").css('display', '');
                          } else {
                              $("#credit_days").removeClass('mand');
                              $("#paymentterms").css('display', 'none');
                          }
                      });
                      
                      
                      function checkpdf() {
                          var payment_type = $("#payment_type").val();
                          if (payment_type == 4 || payment_type == 5) {
                              $("#credit_days").addClass('mand');
                              $("#paymentterms").css('display', '');
                          } else {
                              $("#credit_days").removeClass('mand');
                              $("#paymentterms").css('display', 'none');
                          }
                      
                      }
                    </script>
                    <div id="paymentterms" style="display: none;">
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="field-1" class="control-label">Credit Days</label>
                          <span style="color:red;">*</span>
                          <input type="number" name="credit_days" id="credit_days" class="form-control" autocomplete="off">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <hr>
                <div class="row productss">
                  <div class="col-sm-12 col-xs-12 col-md-12">
                    <div class="col-md-3 col-3">
                      <div class="form-group">
                        <label for="field-1" class="control-label">Product</label>
                        <span style="color:red;">*</span>
                        <select name="product[]" id="product0" class="form-control mand prd" required  onchange="getunit(0);" >
                          <option value="">SELECT</option>
                          <?php if ($getAllProducts != '') {
                            foreach ($getAllProducts as $row) { ?>
                          <option value="<?php echo $row->id; ?>"><?php echo $row->instruments_name; ?>-<?php echo $row->pack_size; ?></option>
                          <?php  }
                            } ?>
                        </select>
                        <input type="hidden" name="bulk[]" id="bulk0" value="0">
                      </div>
                    </div>
                    <div class="col-md-3 col-3">
                      <div class="form-group">
                        <label for="field-1" class="control-label">Qty</label>
                        <span style="color:red;">*</span>
                        <input type="number" name="qty[]" id="quantity0" class="form-control mand" required autocomplete="nope" step="any">
                      </div>
                    </div>
                    <div class="col-md-3 col-3">
                      <div class="form-group">
                        <label for="field-1" class="control-label">Unit</label>
                        <span style="color:red;">*</span>
                        <select name="pack_size[]" id="unit0" class="form-control mand" required autocomplete="nope" onchange="checkforkgs(0);">
                        </select>
                      </div>
                    </div>
                    <div class="col-md-3 col-3" id="dens0" style="display:none;">
                      <div class="form-group">
                        <label for="field-1" class="control-label">Density</label>
                        <span style="color:red;">*</span>
                        <input type="text" name="density[]" id="density0" class="form-control" autocomplete="nope" step="any" onkeyup="allow_decimal('density0');">
                      </div>
                    </div>
                     <div class="col-md-3 col-3" id="bulktobulkorbulktodrum0" style="display:none;">
                      <div class="form-group">
                        <label for="field-1" class="control-label">Select Bulk Type</label>
                        <span style="color:red;">*</span>
                        <select class="form-control" name="bulktype[]" id="bulktype0" onchange="checkbulktype(0);" value="">
                          <option value="1"> Bulk to Bulk </option>
                          <option value="2"> Bulk to Drum </option>

                        </select>
                      </div>
                    </div>

                    <div class="col-md-3" id="selectsecondproduct0" style="display: none;">
                      <div class="form-group">
                        <label for="field-1" class="control-label">Select Product</label>
                        <select name="secondproduct[]" id="product00" class="form-control" >
                          <option value="">SELECT</option>
                          <?php if ($getAllProducts != '') {
                            foreach ($getAllProducts as $row) { ?>
                          <option value="<?php echo $row->id; ?>"><?php echo $row->instruments_name; ?>-<?php echo $row->pack_size; ?></option>
                          <?php  }
                            } ?>
                        </select>
                        </div>
                    </div>



                    <div class="col-md-3 col-3">
                      <div class="form-group">
                        <label for="field-1" class="control-label">Rate/<span id="rate0"></span></label>
                        <span style="color:red;">*</span>
                        <input type="number" name="rate[]" id="ratess0" onkeyup="calculatetaxablevalue(0);" class="form-control mand" autocomplete="nope" step="any" required>
                      </div>
                    </div>

                    <script type="text/javascript">
                        function calculatetaxablevalue(i){
                            var rate = $("#ratess"+i).val();
                            var qty = $("#quantity"+i).val();
                            var taxablevalue = rate*qty;
                            $("#taxablevalue"+i).val(taxablevalue);


                        }

                        function calculateratebytaxableqty(i){
                          var taxablevalue = $("#taxablevalue"+i).val();
                            var qty = $("#quantity"+i).val();
                            var ratevalue = taxablevalue/qty;
                            $("#ratess"+i).val(ratevalue);
                        }
                    </script>

                    <div class="col-md-3 col-3">
                      <div class="form-group">
                        <label for="field-1" class="control-label">Taxable Value</label>
                        <span style="color:red;">*</span>
                        <input type="number" name="taxablevalue[]" onkeyup="calculateratebytaxableqty(0);" id="taxablevalue0" class="form-control mand" autocomplete="nope" step="any" required>
                      </div>
                    </div>
                    <div class="col-md-3 col-3">
                      <div class="form-group">
                        <label for="field-1" class="control-label">Batch No</label>
                        <span style="color:red;"><span id="bmand0">*</span>(Batch No. to be separated by comma (,))</span>
                        <!-- <input type="text" name="batch_no[]" class="form-control mand" autocomplete="nope"> -->
                        <textarea name="batch_no[]" class="form-control mand" id="batch_no0" onblur="check_batch_no(0);"></textarea>
                        <span id="batch_error0" style="color:red;font-weight: bold;font-weight: bold;"></span>
                      </div>
                    </div>
                    <div class="col-md-1 col-1">
                      <a href="javascript:void(0)" class="btn btn-warning btn-xs add_more" style="margin-top: 32px;"><i class="fa fa-plus" aria-hidden="true"></i></a>
                    </div>
                  </div>
                </div>
                <hr>
                <div class="row">
                  <div class="col-md-12">
                    <h4 class="page-title text-center">TRANSPORTATION DETAILS</h4>
                  </div>
                  <div class="col-md-12">
                    <div class="col-md-2">
                      <label>Select Type</label>
                      <select name="ttype" id="ttype" class="form-control mand" onchange="chk_owned()">
                        <option value="">Select Type</option>
                        <option value="1">Self Owned</option>
                        <option value="2">Hired</option>
                      </select>
                    </div>
                    <div class="col-md-3"></div>
                    <div class="col-md-2">
                      <label>Test Report Required</label><br>
                      <input type="checkbox" name="chk_report" value="1">
                    </div>
                  </div>
                </div>
                <div class="row" id="external_transporter" style="display: none;margin-top:20px;">
                  <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Transporter Name<span style="color:red;">*</span></label>
                        <select class="form-control select2" name="transporter_name" id="transporter_name" onchange="getTransporterDetails()">
                          <option value="">SELECT</option>
                          <?php if ($getAllTransporters != '') {
                            foreach ($getAllTransporters as $row2) { ?>
                          <option value="<?php echo $row2->id; ?>"><?php echo $row2->name; ?></option>
                          <?php }
                            } ?>
                        </select>
                        <!-- <input type="text" class="form-control" name="name" required=""> -->
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Mobile No.<span style="color:red;">*</span></label>
                        <input type="number" maxlength="10" class="form-control" name="mobile_no" id="mobile_no" value="" data-mask="(999) 999-9999">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Address</label>
                        <textarea class="form-control" name="address" id="address"></textarea>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Vehicle No.<span style="color:red;">*</span></label>
                        <input type="text" class="form-control" name="vehicle_no" id="vehicle_no">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Vehicle Type<span style="color:red;">*</span></label>
                        <input type="text" class="form-control" name="vehicle_type" id="vehicle_type">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Transporter Rate/LTR<span style="color:red;">*</span></label>
                        <input type="text" class="form-control" id="transport_rate" name="transport_rate" onkeyup="allow_decimal('trate');">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row" style="margin-top:20px;">
                  <div class="col-md-12 text-center">
                    <input type="submit" id="saves" class="btn btn-success" value="Submit">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
        <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
          <!-- <form id="loginForm" method="post" action="<?php echo page_url; ?>Master/User_management/add_new_vendors/<?php echo $this->uri->segment('4'); ?>"  enctype="multipart/form-data" onsubmit="return validate();"> -->
          <!-- <input type="hidden" name="itemid" value="<?php echo $itemid; ?>">
            <input type="hidden" name="originalprice" value="<?php echo $original_price; ?>">
            
            <input type="hidden" name="discounttype" value="<?php echo $discount_type; ?>">
            
            <input type="hidden" name="discount_percent" value="<?php echo $discount_percent; ?>">
            
            <input type="hidden" name="discount_price" value="<?php echo $discounted_price; ?>">
            
            <input type="hidden" name="finalvalue" value="<?php echo $total_value; ?>">
            
            <input type="hidden" name="prno" value="<?php echo $prno; ?>"> -->
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Add New Vendor</h4>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="field-2" class="control-label">Company</label>
                      <span id="error_vendor_name" style="color:red;">*</span>
                      <select class="form-control" name="company" id="company" required>
                        <option value="">Select Company</option>
                        <?php if ($getRackLocation != '') {
                          foreach ($getRackLocation as $row1) { ?>
                        <option value="<?php echo $row1->id; ?>"><?php echo $row1->companyname; ?></option>
                        <?php }
                          } ?>
                      </select>
                      
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="field-2" class="control-label">Vendor Name</label>
                      <span id="error_vendor_name" style="color:red;">*</span>
                      <input type="text" class="form-control" name="vendor_name" id="vendor_name" value="<?php //echo $name;
                        ?>" autocomplete="off" required>
                    </div>
                  </div>

                    <div class="col-md-4">

                          <div class="form-group">

                             <label for="field-2" class="control-label">Vendor Code</label>

                             <span id="error_vendor_name" style="color:red;">*</span>

                             <input type="text" class="form-control" name="vendor_code" id="vendor_code"  value="" autocomplete="off" required>

                          </div>

                        </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="field-2" class="control-label">Contact Person</label>
                      <span id="error_vendor_name" style="color:red;">*</span>
                      <input type="text" class="form-control" name="contactperson" id="contactperson" autocomplete="off" required>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="field-2" class="control-label">Contact</label>
                      <span id="error_vendor_name" style="color:red;">*</span>
                      <input type="text" class="form-control" name="contact" id="contact" value="<?php //echo $phone_number;
                        ?>" autocomplete="off" required>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="field-2" class="control-label">Email ID</label>
                      <span id="error_vendor_name" style="color:red;">*</span>
                      <input type="text" class="form-control" name="email" id="email" autocomplete="off" required>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="field-2" class="control-label">GST No.</label>
                      <span id="error_vendor_name" style="color:red;">*</span>
                      <input type="text" class="form-control" name="gst" id="gst" autocomplete="off" required>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="field-2" class="control-label">PAN No.</label>
                      <span id="error_vendor_name" style="color:red;">*</span>
                      <input type="text" class="form-control" name="pan" id="pan" autocomplete="off" required>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <label for="field-2" class="control-label">Address</label>
                      <span id="error_vendor_name" style="color:red;">*</span>
                      <textarea class="form-control" name="ven_address" id="ven_address" autocomplete="off" required style="resize:none;"></textarea>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                <input type="submit" id="vensave" class="btn btn-info" value="Submit" onclick="submit_data()">
              </div>
            </div>
          </div>
          <!-- </form> -->
        </div>

        <!-- /.modal -->
        <?php $this->load->view('common/footer'); ?>
      </div>
    </div>
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
    <!-- App js -->
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
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
    <script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
    <script src="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
    <script>
      $(document).ready(function() {
          $("#transporter_name").select2({
              //tags: true
          });
      
          $('#product0').select2();
          $('#product01').select2();
      
      var role="<?php echo $role;?>";

      if(role!=1)
      {

          $('#datepicker').datepicker({
              autoclose: true,
              todayHighlight: true,
              format: 'dd-mm-yyyy',
              startDate: '-7d',
              endDate: '+0d'
          });
      }else
      {
          $('#datepicker').datepicker({
              autoclose: true,
              todayHighlight: true,
              format: 'dd-mm-yyyy',
              endDate: '+0d'
          });
      }
      
          $('#man_date0').datepicker({
              autoclose: true,
              todayHighlight: true,
              format: 'dd-mm-yyyy'
          });
      });
    </script>
    <script type="text/javascript">
      
      var j = 1;
      $('.add_more').click(function() {

          $('.productss').append('<div class="row fieldGroups"><div class="col-md-12"><div class="col-md-3 col-3"><div class="form-group"> <label for="field-1" class="control-label">Product</label> <span style="color:red;">*</span><select name="product[]" id="product' + j + '" class="form-control mand prd" onchange="getunit(' + j + ');"><option value="">SELECT</option><?php if ($getAllProducts != '') {
        foreach ($getAllProducts as $row) { ?><option value="<?php echo $row->id; ?>"><?php echo $row->instruments_name; ?>-<?php echo $row->pack_size; ?></option><?php }
        } ?> </select><input type="hidden" name="bulk[]" id="bulk' + j + '" value="0"></div></div><div class="col-md-3 col-3"> <div class="form-group"> <label for="field-1" class="control-label">Qty</label> <span style="color:red;">*</span> <input type="number" step="any" name="qty[]" class="form-control mand" id="quantity'+j+'" autocomplete="nope"> </div></div><div class="col-md-3 col-3"> <div class="form-group"> <label for="field-1" class="control-label">Unit</label> <span style="color:red;">*</span><select name="pack_size[]" id="unit' + j + '" class="form-control mand" autocomplete="nope" onchange="checkforkgs(' + j + ');"></select></div></div><div class="col-md-3 col-3" id="dens' + j + '" style="display:none;"><div class="form-group"><label for="field-1" class="control-label">Density</label><span style="color:red;">*</span><input type="text" name="density[]" id="density'+j+'" onkeyup="allow_decimal_for_dens('+j+')" class="form-control" autocomplete="nope" "></div></div><div class="col-md-3 col-3" id="bulktobulkorbulktodrum'+j+'" style="display:none;"><div class="form-group"><label for="field-1" class="control-label">Select Bulk Type</label><span style="color:red;">*</span><select class="form-control" name="bulktype[]" id="bulktype'+j+'" onchange="checkbulktype('+j+');" value=""><option value="1"> Bulk to Bulk </option><option value="2"> Bulk to Drum </option></select></div></div><div class="col-md-3" id="selectsecondproduct'+j+'" style="display: none;"><div class="form-group"><label for="field-1" class="control-label">Select Product</label><select name="secondproduct[]" id="product0'+j+'" class="form-control" ><option value="">SELECT</option><?php if ($getAllProducts != '') {foreach ($getAllProducts as $row) { ?><option value="<?php echo $row->id; ?>"><?php echo $row->instruments_name; ?>-<?php echo $row->pack_size; ?></option><?php  }} ?></select></div></div><div class="col-md-3 col-3"> <div class="form-group"> <label for="field-1" class="control-label">Rate/<span id="rate' + j + '"></span></label> <span style="color:red;">*</span> <input type="number" step="any" name="rate[]" id="ratess'+j+'" onkeyup="calculatetaxablevalue('+j+');" class="form-control mand" autocomplete="nope"> </div></div><div class="col-md-3 col-3"><div class="form-group"><label for="field-1" class="control-label">Taxable Value</label><span style="color:red;">*</span><input type="number" name="taxablevalue[]" onkeyup="calculateratebytaxableqty('+j+');" id="taxablevalue'+j+'" class="form-control mand" autocomplete="nope" step="any"></div></div><div class="col-md-3 col-3"> <div class="form-group"> <label for="field-1" class="control-label">Batch No</label> <span style="color:red;"><span id="bmand'+j+'">*</span>(Batch No. to be separated by comma (,))</span><textarea name="batch_no[]"  id="batch_no'+j+'" class="form-control mand" onblur="check_batch_no('+j+');"></textarea><span id="batch_error'+j+'" style="color:red;font-weight: bold;font-weight: bold;"></span></div></div><div class="col-md-1 col-1"> <a href="javascript:void(0)" class="btn btn-danger btn-xs remove" style="margin-top: 32px;"><i class="fa fa-remove" aria-hidden="true"></i></a></div></div></div>');
        
          var k=j;
          j++;

           getDatePickers(k);
          initializeSelect2_product("product" + k);
          initializeSelect2_secondproduct("product0" + k);
          allow_decimal("density"+k);


      
      });
      
      $(document).on('click', '.remove', function() {
          $(this).parents(".fieldGroups").remove();
      });
      
      function getDatePickers(j) {
          $('#man_date' + j).datepicker({
              autoclose: true,
              todayHighlight: true,
              format: 'dd-mm-yyyy'
          });
      }
    </script>
    <script type="text/javascript">
      function allow_decimal(data) {
      
          var self = $("#" + data);
          self.val(self.val().replace(/[^0-9\.]/g, ''));
          if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
              evt.preventDefault();
          }
      
      
      }
      
      function getunit(flag) {

      var purdate=$("#datepicker").val();
      var pur_company=$("#pur_company").val();
     

      $("#bulktobulkorbulktodrum"+flag).css('display','none');
      $("#bulktype"+flag).attr('required',false);
      $("#bulktype"+flag).removeClass('mand');
          $("#dens" + flag).css('display', 'none');
          $("#density" + flag).removeClass('mand');
          $("#bulk" + flag).val(0);

          /** batch no **/
          $("#batch_no"+flag).attr('readonly',false);
          $("#batch_no"+flag).attr('required',true);
          $("#batch_no"+flag).text('');
          $("#batch_no"+flag).addClass('mand');
          $("#bmand"+flag).text('*');
          /** END**/

          var product = $("#product" + flag).val();
          if(product!='')
          {
          if(purdate!='' && pur_company!='')
          {


            //   $.ajax({
            // type: "post",
            // url: "<?php echo page_url; ?>Inventory/get_product_unit",
            // data: "prd=" + product,
            // success: function(data) {
            // var d = data.split('|');
            // $("#unit" + flag).html(d[0]);
            // $("#rate" + flag).text(d[1]);
            // if (d[2] == 4) {
            // $("#dens" + flag).css('display', '');
            // $("#density" + flag).addClass('mand');
            // }
            // }
            // });
           


            var hpcl=$("#hpcl_vendor").val();

            if(pur_company==3 && hpcl==1)
            {
            $.ajax({
            type: "post",
            url: "<?php echo page_url; ?>Approval/check_for_apporval_against_purchase",
            data: "prd="+product+"&pur_date="+purdate,
            success: function(data) {
            if(data>0)
            {

               $.ajax({
            type: "post",
            url: "<?php echo page_url; ?>Inventory/get_product_unit",
            data: "prd=" + product,
            success: function(data) {

            var d = data.split('|');
          
            $("#unit" + flag).html(d[0]);
            $("#rate" + flag).text(d[1]);
            
            if(d[3]=='BULK' || $d[3]=='BUCKET'){
              $("#batch_no"+flag).attr('readonly',true);
              $("#batch_no"+flag).attr('required',false);
              $("#batch_no"+flag).text('Not Required');
              $("#batch_no"+flag).removeClass('mand');
              $("#bmand"+flag).text('');

            }else
            {
               $("#batch_no"+flag).attr('readonly',false);
              $("#batch_no"+flag).attr('required',true);
               $("#batch_no"+flag).text('');
               $("#batch_no"+flag).addClass('mand');
               $("#bmand"+flag).text('*');
            }

            // if (d[2] == 4) {
            // $("#dens" + flag).css('display', '');
            // $("#density" + flag).addClass('mand');
            // }
            }
            });
            }else
            {
              alert('Product Cannot be Selected. Since No Approval has been found for this product among this purchase date');
              $("#product"+flag).val(null).trigger('change'); 
            }

      
            }
            });
          }else
          {

              $.ajax({
            type: "post",
            url: "<?php echo page_url; ?>Inventory/get_product_unit",
            data: "prd=" + product,
            success: function(data) {
            var d = data.split('|');
             
            $("#unit" + flag).html(d[0]);
            $("#rate" + flag).text(d[1]);

            if(d[3]=='BULK' || $d[3]=='BUCKET'){
              $("#batch_no"+flag).attr('readonly',true);
              $("#batch_no"+flag).attr('required',false);
              $("#batch_no"+flag).text('Not Required');
              $("#batch_no"+flag).removeClass('mand');
              $("#bmand"+flag).text('');

            }else
            {
               $("#batch_no"+flag).attr('readonly',false);
              $("#batch_no"+flag).attr('required',true);
               $("#batch_no"+flag).text('');
               $("#batch_no"+flag).addClass('mand');
               $("#bmand"+flag).text('*');
            }
            // if (d[2] == 5) {
            // $("#dens" + flag).css('display', '');
            // $("#density" + flag).addClass('mand');
            // }
            }
            });

          }




          }else
          {

          alert('Choose Purchase Date/Company First');
           $("#product"+flag).val(null).trigger('change'); 
          }

        }


        
      checkforbulkitem(flag)
      
      
      }

      function checkforbulkitem(flag)
      {

      $("#bulktobulkorbulktodrum"+flag).css('display','none');
      $("#bulktype"+flag).attr('required',false);
      $("#bulktype"+flag).removeClass('mand');

         $("#bulk" + flag).val(0);
        var product=$("#product"+flag).val();
          $.ajax({
              type: "post",
              url: "<?php echo page_url; ?>Inventory/check_for_bulk",
              data: "prd=" + product,
              success: function(data) {
                //alert(data);

                  if (data > 0) {
                   // alert(flag); 
                      $("#bulk" + flag).val(1);
                      // $("#unit" + flag).append("<option value='4'>Kilogram</option>");
                      $("#bulktobulkorbulktodrum"+flag).css('display','');
                      $("#bulktype"+flag).attr('required',true);
                      $("#bulktype"+flag).addClass('mand');

                  }
            
      
              }
          });

      }

      function checkbulktype(i){
          var bulktype = $("#bulktype"+i).val();
         
          if(bulktype==2){
            $("#selectsecondproduct"+i).css('display','');
                      $("#product0"+i).attr('required',true);
                      $("#product0"+i).addClass('mand');

                      $("#batch_no"+i).attr('readonly',false);
                      $("#batch_no"+i).addClass('mand');
                      $("#batch_no"+i).attr('required',true);
                      $("#batch_no"+i).html('');

                    }else{
                      $("#selectsecondproduct"+i).css('display','none');
                      $("#product0"+i).attr('required',false);
                      $("#product0"+i).removeClass('mand');

                      $("#batch_no"+i).attr('readonly',true);
                      $("#batch_no"+i).removeClass('mand');
                      $("#batch_no"+i).attr('required',false);
                      $("#batch_no"+i).html('');
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
      
      function initializeSelect2_product(selectElementObj) {
          $('#' + selectElementObj).select2();
      }
      function initializeSelect2_secondproduct(selectElementObj) {
          $('#' + selectElementObj).select2();
      }
    </script>
    <script>
      function chk_owned() {
          $("#external_transporter").css('display', 'none');
          $("#transporter_name").removeClass('mand');
          $("#mobile_no").removeClass('mand');
          $("#vehicle_no").removeClass('mand');
          $("#vehicle_type").removeClass('mand');
          $("#transport_rate").removeClass('mand');
      
          var type = $("#ttype").val();
      
          if (type != '') {
              if (type == 1) {
                  $("#external_transporter").css('display', 'none');
                  $("#transporter_name").removeClass('mand');
                  $("#mobile_no").removeClass('mand');
                  $("#vehicle_no").removeClass('mand');
                  $("#vehicle_type").removeClass('mand');
                  $("#transport_rate").removeClass('mand');
              } else {
                  $("#external_transporter").css('display', '');
      
                  $("#transporter_name").addClass('mand');
                  $("#mobile_no").addClass('mand');
                  $("#vehicle_no").addClass('mand');
                  $("#vehicle_type").addClass('mand');
                  $("#transport_rate").addClass('mand');
      
              }
      
          }
      }
      
      function getTransporterDetails() {
          var transporter_id = $("#transporter_name").val();
          // alert(transporter_id);
      
          $.ajax({
              type: "post",
              url: "<?php echo page_url; ?>Approval/getTransporterDetails",
              data: {
                  transporter_id: transporter_id
              },
              success: function(data) {
                  var arr = data.split('|');
                  var mobile_no = arr[0];
                  var address = arr[1];
      
                  $("#mobile_no").val(mobile_no);
                  $("#address").val(address);
      
      
              }
          });
      }
      
      
      function checkforkgs(flag) {

        $("#bulktobulkorbulktodrum"+flag).css('display','none');
        $("#bulktype"+flag).attr('required',false);
        $("#bulktype"+flag).removeClass('mand');

        $("#rate" + flag).text('');
          var u = $("#unit" + flag).val();
          var ifbulk = $("#bulk" + flag).val();
          var u11 = $("#unit" + flag).text();
          if (u == 5 && ifbulk==1) {
              $("#dens" + flag).css('display', '');
              $("#density" + flag).addClass('mand');
              $("#rate"+flag).text('Kg');

            $("#bulktobulkorbulktodrum"+flag).css('display','');
            $("#bulktype"+flag).attr('required',true);
            $("#bulktype"+flag).addClass('mand');

          } else {
              $("#dens" + flag).css('display', 'none');
              $("#density" + flag).removeClass('mand');
              $("#rate"+flag).text(u11);


          }
      
      }
      
        function submit_data() {
          $("#vensave").attr('disabled', true);
          $("#vensave").val('Please wait');
          var company=$("#company").val();
           var vendor_name=$("#vendor_name").val();
           var contactperson=$("#contactperson").val();
           var contact=$("#contact").val();
           var email=$("#email").val();
           var gst=$("#gst").val();
           var pan=$("#pan").val();
           var address=$("#ven_address").val();
           var vendor_code=$("#vendor_code").val();
           // alert(company)
           // alert(vendor_name)
           // alert(contactperson)
           // alert(contact)
           // alert(email)
           // alert(gst)
           // alert(pan)
           // alert(address)
          if(company!='' && vendor_name!='' && contactperson!='' && contact!='' && email!='' && gst!='' && pan!='' && vendor_code!='')
          {
             $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>/Master/User_management/add_vendor_via_ajax",

                data: { company: company,
                        vendor_name: vendor_name,
                        contactperson: contactperson,
                        contact: contact,
                        email: email,
                        gst: gst,
                        pan: pan,
                        address: address,
                        vendor_code:vendor_code
                      },
                success: function(data) {

                if(data>0)
                {
                     $("#vensave").attr('disabled',false);
                    $("#vensave").val('Submit');
                    // getcustomerdata(data,companyname);

                    $("#con-close-modal").modal('hide');

                }else
                {
                    $("#vensave").attr('disabled',false);
                    $("#vensave").val('Submit');
                    alert('All Fields are mandatory');
                    return false
                }
                
                }
            });

          }else{
            alert('Please fill all mandatory Fields');
            $("#vensave").attr('disabled', false);
            $("#vensave").val('Submit');
            return false;
          }
      
      }

      function getParty(){
        var pur_company = $('#pur_company').val();
        // alert(pur_company);
        $.ajax({
              type: "post",
              url: "<?php echo page_url; ?>Inventory/get_company_partyNew",
              data: "company=" + pur_company,
              success: function(data) {                  
                  $("#pur_party").html(data);                  
              }     
      
          });


        $('.prd').val(null).trigger('change');

      }


      function initialize_allow_decimal(data)
      {


          var self = $(".allow_decimal");
          self.val(self.val().replace(/[^0-9\.]/g, ''));
          if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
              evt.preventDefault();
          }


      }

      function allow_decimal_for_dens(j){

        var self = $("#density"+j);
          self.val(self.val().replace(/[^0-9\.]/g, ''));
          if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
              evt.preventDefault();
          }
      

      }


      function check_for_hpcl(){
        var d=$("#pur_party").val();
        if(d!='')
        {
            $.ajax({
            type: "post",
            url: "<?php echo page_url; ?>Inventory/get_part_detail",
            data: "party=" +d,
            success: function(data) {
              $("#hpcl_vendor").val(data);
            }

            });
        }

      }

      function check_batch_no(flag)
      {
        $("#batch_error"+flag).html('');
        var batch_no=$("#batch_no"+flag).val();
        var product=$("#product"+flag).val();
        if(batch_no!='' && product!='')
        {
            $.ajax({
            type: "post",
            url: "<?php echo page_url; ?>Inventory/check_batch_no",
            data: "batch_no="+batch_no+"&product="+product,
            success: function(data) {
              if(data!='')
              {
                $("#batch_no"+flag).val('');
                $("#batch_error"+flag).html(data);
              }
          
            }
            });

        }

      }
    </script>
  </body>
</html>