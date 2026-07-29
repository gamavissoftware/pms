<?php
$CI = &get_instance();
$CI->load->model('Salescrm_model', 'salescrm');
$MI = &get_instance();
$MI->load->model('Master_model', 'master');
$DI = &get_instance();
$DI->load->model('Dashboard_model');

$getOrderDetails=$CI->salescrm->getOrderDetails($this->uri->segment(3));
// echo "<pre>";print_r($getOrderDetails);exit;

$getspecialremarks=$CI->salescrm->getlastremarks($this->uri->segment(4));

if($getOrderDetails != '') {
  foreach ($getOrderDetails as $row1);
    $payment_type = $row1->payment_type;
    $utr_no = $row1->utr_no;
    $cheque_no = $row1->cheque_no;
    if($row1->pdc_date == '0000-00-00' || $row1->pdc_date == '1970-01-01') {
      $pdc_date = '';
    } else {
      $pdc_date = date('d-m-Y',strtotime($row1->pdc_date));
    }
    $po_no = $row1->po_no;

    if($row1->po_date != '0000:00:00' && $row1->po_date != '1970-01-01') {
      $po_date = date('d-m-Y',strtotime($row1->po_date));
    } else {
      $po_date = '';
    }

    $source = $row1->source;
    $agent = $row1->agent;
    $invoice_no = $row1->invoice_no;
    $sales_no = $row1->sales_order_no;
    $upload_po = $row1->upload_po;
    $ship_to = $row1->ship_to;
    $shipping_name = $row1->shipping_name;
    $shipping_address = $row1->shipping_address;
    $shipping_state = $row1->shipping_state;
    $shipping_city = $row1->shipping_city;
    $shipping_pincode = $row1->shipping_pincode;
    $shipping_phone_no = $row1->shipping_phone_no;
    $shipping_mobile_no = $row1->shipping_mobile_no;
    $shipping_email = $row1->shipping_email;
    $same_shipping_billing = $row1->same_shipping_billing;
    $bill_to = $row1->bill_to;
    $billing_name = $row1->billing_name;
    $billing_address = $row1->billing_address;
    $billing_state = $row1->billing_state;
    $billing_city = $row1->billing_city;
    $billing_pincode = $row1->billing_pincode;
    $billing_phone_no = $row1->billing_phone_no;
    $billing_mobile_no = $row1->billing_mobile_no;
    $billing_email = $row1->billing_email;
    $credit_terms = $row1->credit_terms;
    $extra_days = $row1->extra_days;
    $max_credit_limit = $row1->max_credit_limit;
    $reference = $row1->reference;
    $note = $row1->note;
    $pan_no = $row1->pan_no;
    $msme_no = $row1->msme_no;
    $registration_type = $row1->registration_type;
    $gst_no = $row1->gst_no;
    $hpcl_billing_company = $row1->hpcl_billing_company;
} else {
    $source = '';
    $agent = '';
    $invoice_no = '';
    $sales_no='';
    $payment_type = '';
    $utr_no = '';
    $cheque_no = '';
    $pdc_date = '';
    $upload_po = '';
    $ship_to = '';
    $shipping_name = '';
    $shipping_address = '';
    $shipping_state = '';
    $shipping_city = '';
    $shipping_pincode = '';
    $shipping_phone_no = '';
    $shipping_mobile_no = '';
    $shipping_email = '';
    $same_shipping_billing = '';
    $bill_to = '';
    $billing_name = '';
    $billing_address = '';
    $billing_state = '';
    $billing_city = '';
    $billing_pincode = '';
    $billing_phone_no = '';
    $billing_mobile_no ='';
    $billing_email = '';
    $credit_terms = '';
    $extra_days = '';
    $max_credit_limit = '';
    $reference = '';
    $note = '';
    $pan_no = '';
    $msme_no = '';
    $registration_type = '';
    $gst_no = '';
    $hpcl_billing_company = '';
    $po_no = '';
    $po_date = '';
}

// echo $pdc_date;exit;

$getQuotationInfo=$CI->salescrm->getQuotationInfo($this->uri->segment(3));
$productdetail=$CI->salescrm->getQuotationProducts($this->uri->segment(4));

if($getQuotationInfo != '') {
  foreach ($getQuotationInfo as $row);
  $validity_date = date('d-m-Y', strtotime($row->validity_date));
  $company_name = $row->company_name;
  $customer_name = $row->customer_name;
  $credit_days = $row->credit_days;
  $company_id = $row->company_id;
  $customer_id = $row->customer_id;
} else {
  $validity_date = '';
  $company_name = '';
  $customer_name = '';
  $credit_days = '';
  $company_id = '';
  $customer_id = '';
}

$getAllStates = $CI->salescrm->getAllStates();



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

          .search-page h3{
font-weight: 600;
        }

        .search-page h3 span{
            background: #fff1ea;
            color: #f9ab00;
            border-radius: 5px;
            padding: 5px;
            font-size: 20px;
        }

        .search-page p{
            color: black;
            /* font-size: 14px; */
            margin: 0;
        }

        .search-page p i{
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
        

        .search-page table{
            width: 100%;
          
           
        }

        .search-page table th{
            padding: 5px;
            text-align: center;
            border: 1px solid lightgray;
            color: black;
            background-color: whitesmoke;
        }

        .search-page table td{
            border: 1px solid lightgray;
            padding: 5px;
            text-align: center;
            color: black;
        }

         .select2-container
        {
            height: 44px !important;
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single
        {
            height: 36px !important;
        }
  </style>


</head>





<body>





  <!-- Navigation Bar-->

  <header id="topnav">

    <?php $this->load->view('common/nav-menu'); ?>

  </header>

  <!-- End Navigation Bar-->
<!--------------------------------NOTES-------------------------------------->

<div id="mySidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="todo-box">
                        <div class="all-notes">
                            <div class="row">
                                <div class="col-sm-12">
                                    <p><i class="fa fa-list" aria-hidden="true"></i> Add Notes</p>
                                </div>
                            </div>
                        </div>
                        <div class="to-list">
                            <form>
                                <input type="text" class="form-control">
                            </form>

                            <div class="todo-list">
                                <div class="todo-item">

                                    <span>Create theme</span> <a href="javascript:void(0);"
                                        class="float-right remove-todo-item"><i class="icon-close"></i></a>
                                </div>
                                <div class="todo-item">
                                    <span>Work
                                        on wordpress</span> <a href="javascript:void(0);"
                                        class="float-right remove-todo-item"><i class="icon-close"></i></a>
                                </div>
                                <div class="todo-item">

                                    <span>Organize office main department</span> <a href="javascript:void(0);"
                                        class="float-right remove-todo-item"><i class="icon-close"></i></a>
                                </div>
                              
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>

<!--------------------------------NOTES-------------------------------------->
  <div class="wrapper">
    <div class="container">
      <!-- Page-Title -->
    <form method="post" class="card-box" method="post" action="<?php echo page_url; ?>Customer/update_order_details/<?php echo $this->uri->segment(3); ?>/<?php echo $this->uri->segment(4); ?>" enctype="multipart/form-data" onsubmit="return validate()">
      <div class="row">

        <div class="col-sm-12" >
        <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success btn-xs" name="action_button"><i class="fa fa-arrow-left"></i>Back</button></a>

          <div class=" ">
            <div class="profile-info-name">
              <div class="profile-info-detail" style="margin-top:1px">
                <div class="row">
                  <div class="col-sm-12">
                    <div class="card-box search-page">
                      <h5>Overview</h5>

                      <div class="row">
                        <div class="col-sm-6 col-xs-6">
                          <p><strong><i class="fa fa-calendar" aria-hidden="true" style="color:#f9ab00;"></i>Company Name:</strong></p>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                          <p><?php echo $company_name; ?></p>
                        </div>
                      </div>

                       <div class="row">
                        <div class="col-sm-6 col-xs-6">
                          <p><strong><i class="fa fa-calendar" aria-hidden="true" style="color:#f9ab00;"></i>Customer Name:</strong></p>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                          <p><?php echo ucwords($customer_name); ?></p>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-sm-6 col-xs-6">
                          <p><strong><i class="fa fa-calendar" aria-hidden="true" style="color:#f9ab00;"></i>Special Remarks:</strong></p>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                          <p><?php echo $getspecialremarks; ?></p>
                        </div>
                      </div>

                      <hr>
                      <!------------------------------------------>

                      <div class="clearfix"></div>
                    </div>

                  </div>
                  <!-- <div class="row">
                  <div class="col-sm-12">
                    <div class="card-box search-page" style="height: 305px; overflow-y: auto;">
                      <h5>Commercial</h5>
                       <table>
                                        <tr>
                                            <th>S.no</th>
                                            <th>Product</th>
                                            <th>Qty</th>
                                            <th>Offered Price</th>
                                            <th>Agreed Price</th>
                                        </tr>

                                        <?php
                                        $i = 1;
                                        if($productdetail != '') { 
                                            foreach($productdetail as $row2) {?>
                                        <tr>
                                            <td><?php echo $i;?></td>
                                            <td><?php echo $row2->instruments_name;?></td>
                                            <td><?php echo $row2->qty;?></td>
                                            <td><?php echo $row2->list_price;?></td>
                                            <td><input type="hidden" name="quote_detail_id[]" value="<?php echo $row2->id;?>"><input type="text" name="agreed_price[]" value="<?php echo $row2->agreed_price;?>" class="form-control"></td>
                                        </tr>
                                        <?php  
                                        }                                       
                                        ?>
                                       
                                    <?php }else{ ?>

                                            <tr>
                                            <td colspan="7">No Product Available</td>
                                        
                                    
                                            </tr>

                                    <?php } ?>
                                    </table>
 -->
                                <!-- <div class="col-md-12" style="margin-top:20px;">
                                  <div class="col-md-4"></div>
                                    <div class="col-md-4"></div>
                                     <div class="col-md-4" style="margin-top:10px">
                                        <label>Upload PO/Evidence<span style="color: red;">*</span></label>
                                        <span class="input-icon icon-right" style="margin-bottom:10px">
                                          <input type="hidden" name="old_upload_file" value="<?php echo $upload_po;?>">
                                          <input type="file" name="upload_file">
                                          <?php if($upload_po <> '') { ?>
                                                  <a href="<?php echo sfdocument;?>order_punch_po/<?php echo $upload_po;?>" download>Download PO</a>
                                          <?php }?>
                                        </span>
                                    </div>
                                </div> -->

                               
                    <!-- </div>
                  </div>
                </div> -->
                </div>

              </div>
            </div>

            <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>




            <!------------------To disable Folloup Use disabled attribute--------------------->

            <div class="col-md-2">
                  <div class="form-group">
                      <label for="field-2" class="control-label">Source</label>
                      <span id="error_rack_location" style="color:red;">*</span>

                      <select class="form-control mand" name="source" id="source" required>
                          <option value="">Select</option>
                          <?php $res=$this->db->select('source_id,lead_source')
                                              ->from('lead_source')
                                              ->get();
                              if($res->num_rows() >0) {
                                  foreach($res->result() as $row) {?>
                                      <option value="<?php echo $row->source_id;?>" <?php if($row->source_id == $source) { echo 'selected';}?>><?php echo $row->lead_source;?></option>
                                  <?php }
                                  } ?>
                      </select>
                  </div>
              </div>


                <div class="col-md-2">
                  <div class="form-group">
                      <label for="field-2" class="control-label">Sales Agent</label>
                      <span id="error_rack_location" style="color:red;">*</span>

                      <select class="form-control mand select3" name="agent" id="agent" required>
                          <option value="">Select</option>
                          <?php $res=$this->db->select('user_id,first_name,last_name')
                                              ->from('system_users')
                                              ->get();
                              if($res->num_rows() >0) {
                                  foreach($res->result() as $row) {?>
                                      <option value="<?php echo $row->user_id;?>" <?php if($row->user_id == $agent) { echo 'selected';}?>><?php echo $row->first_name;?>&nbsp;<?php echo $row->last_name;?></option>
                                  <?php }
                                  } ?>
                      </select>
                  </div>
              </div>

              <div style="clear:both;height:10px"></div>

               <div class="col-md-4">
                  <div class="form-group">
                      <label for="field-2" class="control-label">Billing Company <span style="color:red">*</span></label>
                      <span id="error_state" style="color:red;"></span>
                      <select class="form-control mand" name="company" id="company" required onchange="getproduct();getcustomers(); displayproduct(this.value); ">
                          <!-- <option value="">Select Company</option> -->
                          <?php
                          $q = $this->db->select('id, companyname')->from('store_rack_location')->where('status', 1)->where('id',$company_id)->get();
                          foreach ($q->result() as $row) {
                          ?>
                              <option value="<?php echo $row->id; ?>" <?php if($row->id == $company_id) { echo 'selected';}?>><?php echo $row->companyname; ?></option>
                          <?php } ?>
                      </select>
                      <input type="hidden" name="old_company_hidden" value="<?php echo $company_id;?>">

                  </div>
              </div>


              <div class="col-md-4">
                  <div class="form-group">
                      <label for="field-2" class="control-label">Customer Name&nbsp;</label>
                      <span id="error_rack_location" style="color:red;">*</span>

                      <select class="form-control mand select3" name="customer" id="customer" required onchange="getCustomerDetails();">
                        
                          <?php $res=$this->db->select('id,company_name')
                                              ->from('customer_detail')
                                              ->where('company_id',$company_id)
                                              ->get();
                              if($res->num_rows() >0) {
                                  foreach($res->result() as $row) {

                                    // if($row->id == $customer_id) {
                                        ?>
                                      <option value="<?php echo $row->id;?>" <?php if($row->id == $customer_id) { echo 'selected';}?>><?php echo $row->company_name;?></option>
                                  <?php } }
                                  //} ?>
                      </select>
                  </div>
              </div>
             <div class="row">
                <div class="col-md-12" style="margin-top:20px;">
                    <div class="col-md-3">
                          <label>Sales Order No.</label>
                          <input type="text" name="invoice_no" id="invoice_no" class="form-control mand" required autocomplete="nope" readonly value="<?php echo $sales_no;?>">
                    </div>
                    <div class="col-md-3">
                          <label>PO No.</label>
                          <input type="text" name="po_no" class="form-control" value="<?php echo $po_no;?>" autocomplete="nope">
                    </div>
                    <div class="col-md-3">
                          <label>PO Date</label>
                          <input type="text" name="po_date" id="datepicker" class="form-control" value="<?php echo $po_date;?>" autocomplete="nope">
                    </div>
                     <div class="col-md-3">
                            <label>Upload PO/Evidence </label>
                            <span class="input-icon icon-right">
                            <input type="hidden" name="old_upload_file" value="<?php echo $upload_po;?>">
                              <input type="file" name="upload_file">
                              <?php if($upload_po <> '') { ?>
                                  <a href="<?php echo sfdocument;?>order_punch_po/<?php echo $upload_po;?>" download>Download PO</a>
                              <?php }?>
                            </span>
                    </div>
                </div>
              </div>

              <?php 
                if($productdetail != '') { 
                  foreach($productdetail as $row2) {

                 // echo "<pre>"; print_r($row2); exit;
                   ?>
              <div class="row delete_row<?php echo $row2->id;?>" style="margin-top: 30px;">
              <div class="col-md-12">
                  <div class="col-md-2">
                      <div class="form-group">
                          <label for="field-3" class="control-label">Competitor Product</label>
                         <!--  <span id="error_rack_location" style="color:red;">*</span> -->
                         <input type="text" class="form-control" value="<?php echo $row2->competitor_product;?>" readonly>
                         <input type="hidden" name="edit_product_id[]" value="<?php echo $row2->id;?>">
                          <!-- <select class="form-control" name="comp_product[]" id="comp_product0" onchange="getourproductname(0);"></select> -->
                      </div>
                  </div>
                  <div class="col-md-3">
                      <div class="form-group">
                          <label for="field-3" class="control-label">Product</label>
                          <span id="error_rack_location" style="color:red;">*</span>
                          <input type="text" class="form-control" value="<?php echo $row2->instruments_name;?>" readonly>
                          <!-- <select class="form-control mand products" name="product[]" id="product0" onchange="getprice(0,this.value);getdiscount(0,this.value); getunit(0,this.value)" required>
                              <option value="">Select</option></select>
                           <span id="recommendation0"></span> -->
                      </div>
                  </div>

                   <?php 
                   
                   $stock=$CI->salescrm->get_stock_availability($row2->product_id,$company_id); 
                   ?>

                    <div class="col-md-1">
                      <div class="form-group">
                          <label for="field-2" class="control-label">Stock Avl</label>
                          <span id="error_rack_location" style="color:red;">*</span>
                          <input type="number" class="form-control mand" name="stock_edit[]" id="stock_edit<?php echo $row2->id;?>" autocomplete="off" oninput="allow_decimal('stock_edit[<?php echo $row2->id;?>')" value="<?php echo $stock;?>"  readonly required>
                      </div>
                  </div>


                  <div class="col-md-1">
                      <div class="form-group">
                          <label for="field-2" class="control-label">Qty</label>
                          <span id="error_rack_location" style="color:red;">*</span>
                          <input type="text" class="form-control mand" name="qty_edit[]" id="qty_edit<?php echo $row2->id;?>" autocomplete="off" oninput="allow_decimal('qty_edit<?php echo $row2->id;?>')" onkeyup="match_stock_availability1(<?php echo $row2->id;?>);" value="<?php echo $row2->qty;?>" required>
                      </div>
                  </div>
                  <div class="col-md-1">
                      <div class="form-group">
                          <label for="field-2" class="control-label">Unit</label>
                          <span id="error_rack_location" style="color:red;">*</span>
                          <input type="text" class="form-control" value="<?php echo $row2->shortname;?>" readonly>
                      </div>
                  </div>
                  <div class="col-md-2">
                      <div class="form-group">
                          <label for="field-2" class="control-label">Order Price/<span><?php echo $row2->shortname;?></span></label>
                          <span id="error_rack_location" style="color:red;">*</span>
                          <input type="text" class="form-control mand" name="listprice_edit[]" id="listprice_edit<?php echo $row2->id;?>" onblur="getnetamt(0)" autocomplete="off" oninput="allow_decimal('listprice_edit<?php echo $row2->id;?>');" value="<?php echo $row2->agreed_price;?>">
                          <!-- <p id="discountpriceshow0" style="display: none;"></p>-->
                          <span style="color:red;font-weight: bold;">Min Allowed Price <?php echo $row2->max_allowed_price;?></span>
                          <input type="hidden" name="discountpricehideedit[]" value="<?php echo $row2->max_allowed_price;?>" id="discountpricehide<?php echo $row2->id;?>">
                      </div>
                  </div>




                  <div class="col-md-2">
                      <div class="form-group">
                          <label for="field-2" class="control-label">Batch Code</label>
                          <span id="error_rack_location" style="color:red;"></span>
                           <select class="form-control  mand select2" name="batch_code_edit[]" id="batch_code_edit0" >
                            <option value="">Select Batch Code</option>
                            <?php
                            $res = $this->db->select('b.id, b.batch_no')->from('inventory_details a')->join('inventory_batch_no b','a.id=b.inv_detail_id')->where('a.product',$row2->product_id)->group_by('b.batch_no')->get();
                            if($res->num_rows()>0)
                            {
                            foreach($res->result() as $row11)
                            {
                            ?>
                            <option value="<?php echo $row11->id;?>" <?php if($row2->batch_code==$row11->id){?> selected <?php  } ?>><?php echo $row11->batch_no;?></option>
                            <?php } } ?>
                            </select>
                          <!-- <input type="text" name="batch_code_edit[]" class="form-control" value="<?php echo $row2->batch_code;?>"> -->
                      </div>
                  </div>
                  <div class="col-md-2" style="display: none;">
                      <div class="form-group">
                          <label for="field-2" class="control-label">Allowed Price Per <span class="list_price_unit0"></span></label>
                          <span id="error_rack_location" style="color:red;">*</span>
                          <input type="text" class="form-control" name="discountprice[]" id="discountprice0" autocomplete="off" value="" onblur="getnetamt(0)" oninput="allow_decimal('discountprice0');">
                          

                      </div>
                  </div>
                  <div class="col-md-2" style="display: none;">
                      <div class="form-group">
                          <label for="field-2" class="control-label">Net Price</label>
                          <span id="error_rack_location" style="color:red;">*</span>
                          <input type="text" class="form-control" name="netprice[]" autocomplete="off" id="netprice0" value="0" readonly>

                      </div>
                  </div>
                  <div class="col-md-1">
                      <div class="form-group" style="margin-top:25px">
                          <!-- <button type="button" class="btn btn-warning" name="add" id="addmore_btn"><i class="fa fa-plus"></i></button> -->
                          <a href="javascript:;" onclick="delete_product(<?php echo $row2->id;?>)"><i class="fa fa-trash" aria-hidden="true" style="font-size:27px;color: red;"></i></a>
                      </div>
                  </div>
                </div>
              </div>
            <?php } } ?>

            <div class="row" style="margin-top:20px;">
              <div class="col-md-12">
                <label>Add New Product</label><br>
                <input type="checkbox" name="add_new" id="add_new" value="1" onchange="add_new_product()">
              </div>
            </div>

            <div class="col-md-12" style="display:none;" id="show">
                  <div class="col-md-2">
                      <div class="form-group">
                          <label for="field-3" class="control-label">Competitor Product</label>
                         <!--  <span id="error_rack_location" style="color:red;">*</span> -->
                          <select class="form-control" name="comp_product[]" id="comp_product0" onchange="getourproductname(0);"></select>
                      </div>
                  </div>
                  <div class="col-md-3">
                      <div class="form-group">
                          <label for="field-3" class="control-label">Product</label>
                          <span id="error_rack_location" style="color:red;">*</span>
                          <select class="form-control products" name="product[]" id="product0" onchange="getprice(0,this.value);getdiscount(0,this.value); getunit(0,this.value); getstock1(0);  getbatchcode(0,this.value);">
                              <option value="">Select</option></select>
                           <span id="recommendation0"></span>
                      </div>
                  </div>

                     <div class="col-md-1">
                      <div class="form-group">
                          <label for="field-2" class="control-label">Avl Stock</label>
                          <span id="error_rack_location" style="color:red;">*</span>
                          <input type="text" class="form-control" name="stock[]" id="stock_new0" autocomplete="off" readonly>
                      </div>
                  </div>


                  <div class="col-md-1">
                      <div class="form-group">
                          <label for="field-2" class="control-label">Qty</label>
                          <span id="error_rack_location" style="color:red;">*</span>
                          <input type="text" class="form-control" name="qty[]" id="qty_new0" autocomplete="off" oninput="allow_decimal('qty_new0')" onkeyup="match_stock_availability_new(0);">
                      </div>
                  </div>
                  <div class="col-md-1">
                      <div class="form-group">
                          <label for="field-2" class="control-label">Unit</label>
                          <span id="error_rack_location" style="color:red;">*</span>
                          <input type="text" name="unit_name[]" id="unit_name0" class="form-control" readonly>
                          <input type="hidden" name="pack_size[]" id="pack_size0">
                      </div>
                  </div>
                    <div class="col-md-2" id="batch_code_div0" style="display:none;">
                    <div class="form-group">
                    <label for="field-3" class="control-label">Batch Code</label>
                    <select class="form-control  mand select2" name="batch_code[]" id="batch_code0" >
                    <option value="">Select Batch Code</option>
                    </select>
                    </div>
                    </div>

                  <div class="col-md-1">
                      <div class="form-group">
                          <label for="field-2" class="control-label">Order Price/<span class="list_price_unit0"></span></label>
                          <span id="error_rack_location" style="color:red;">*</span>
                          <input type="text" class="form-control" name="listprice[]" id="listprice0" value="" onblur="getnetamt(0)" autocomplete="off" oninput="allow_decimal('listprice0');">
                          <p id="discountpriceshow0" style="display: none;"></p>
                          <input type="hidden" name="discountpricehide[]" value="" id="discountpricehide0">
                      </div>
                  </div>
                
                  <div class="col-md-2" style="display: none;">
                      <div class="form-group">
                          <label for="field-2" class="control-label">Allowed Price Per <span class="list_price_unit0"></span></label>
                          <span id="error_rack_location" style="color:red;">*</span>
                          <input type="text" class="form-control" name="discountprice[]" id="discountprice0" autocomplete="off" value="0" onblur="getnetamt(0)" oninput="allow_decimal('discountprice0');">
                          

                      </div>
                  </div>
                  <div class="col-md-2" style="display: none;">
                      <div class="form-group">
                          <label for="field-2" class="control-label">Net Price</label>
                          <span id="error_rack_location" style="color:red;">*</span>
                          <input type="text" class="form-control" name="netprice[]" autocomplete="off" id="netprice0" value="0" readonly>

                      </div>
                  </div>
                  <div class="col-md-1">
                      <div class="form-group" style="margin-top:25px">
                          <button type="button" class="btn btn-warning" name="add" id="addmore_btn"><i class="fa fa-plus"></i></button>
                      </div>
                  </div>
                  <div id="dynamictasks"></div>
                  <div class="row" style="display: none;">
                  <div class="col-md-12">
                      <div class="col-md-4">
                          <label for="field-2" class="control-label">Terms & Conditions Type</label>
                          <span id="error_rack_location" style="color:red;">*</span><br>
                          Bulk T&C &nbsp;<input type="radio" name="chk_tnc" value="1" onchange="check_tnc()">
                          Drums T&C &nbsp;<input type="radio" name="chk_tnc" value="2" checked onchange="check_tnc()">
                      </div>
                  </div>
              </div>
              <div class="row drums_tnc" style="display: none;">
                  <div class="col-md-12" style="margin-top:20px;">
                      <div class="form-group">
                          <label for="field-2" class="control-label">Terms & Conditions(Drums)</label>
                          <span id="error_item_name" style="color:red;"></span>
                          <textarea class="form-control drums mand" name="drums_tnc" id="drums" value=""><?php echo $drums_tnc;?></textarea>
                      </div>
                   
                  </div>
                  <script>
                  CKEDITOR.replace('drums');
                  </script>
              </div>

              <div class="row bulk_tnc" style="display: none;">
                      <div class="col-md-12" style="margin-top:20px;">
                          <div class="form-group">
                              <label for="field-2" class="control-label">Terms & Conditions(Bulk)</label>
                              <span id="error_item_name" style="color:red;"></span>
                              <textarea class="form-control bulk mand" name="bulk_tnc" id="bulk" value=""><?php echo $bulk_tnc;?></textarea>
                          </div>
                  
                  </div>
                  <script>
                  CKEDITOR.replace('bulk');
                  </script>
              </div>
            </div>
            <div class="iii">

            <div class="row" style="margin-top:20px;">
                  <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                      <div class="page-title-box">                           
                          <h4 class="page-title text-center">&nbsp Payment Terms</h4>
                      </div>
                  </div>
                </div>
              
                <div class="row">
                  <div class="col-md-4">
                    <label>Payment Type<span style="color: red;">*</span></label>
                    <span class="input-icon icon-right" style="margin-bottom:10px">
                    <select class="form-control" name="payment_type" id="payment_type" required="" onchange="checkpdf()">
                      <option value="">SELECT PAYMENT TYPE</option>
                      <option value="2" <?php if($payment_type == 2) { echo 'selected';} ?>>Cash</option>
                      <option value="3" <?php if($payment_type == 3) { echo 'selected';} ?>>Online</option>
                      <option value="4" <?php if($payment_type == 4) { echo 'selected';} ?>>PDC</option>
                      <option value="5" <?php if($payment_type == 5) { echo 'selected';} ?>>Credit</option>
                    </select>
                    </span>
                  </div>
                <!--   <?php 
                   $a = "display:none";
                   $b = "display:none";
                   $c = "display:none";
                   $d = "display:none";

                      if($payment_type == 1) {
                          $a = "";
                      } else if($payment_type == 3) {
                          $b = "";
                      } else if($payment_type == 4) {
                          $a = "";
                          $c = "";
                      }?> -->
<!--                   <div class="col-md-4" id="show_cheque_no" style="<?php echo $a;?>">
                    <label>Cheque No<span style="color: red;">*</span></label>
                    <span class="input-icon icon-right" style="margin-bottom:10px">
                    <input type="text" class="form-control" name="cheque_no" id="cheque_no" autocomplete="nope" value="<?php echo $cheque_no;?>">
                    </span>
                  </div>
                  <div class="col-md-4" id="show_pdc_date" style="<?php echo $c;?>">
                    <label>PDC Date<span style="color: red;">*</span></label>
                    <span class="input-icon icon-right" style="margin-bottom:10px">
                    <input type="text" class="form-control" name="pdc_date" id="pdc_date" autocomplete="nope" value="<?php echo $pdc_date;?>">
                    </span>
                  </div>
                  <div class="col-md-4" id="show_utr_no" style="<?php echo $b;?>">
                    <label>UTR No<span style="color: red;">*</span></label>
                    <span class="input-icon icon-right" style="margin-bottom:10px">
                    <input type="text" class="form-control" name="utr_no" id="utr_no" autocomplete="nope" value="<?php echo $utr_no;?>">
                    </span>
                  </div> -->
                           <script type="text/javascript">

                        $(document).ready(function() {

                           var payment_type= $("#payment_type").val();
                            if(payment_type==4 || payment_type==5)
                            {
                                $("#paymentterms").addClass('mand');
                                $("#credit_days").css('display','');

                            }else
                            {   
                                $("#paymentterms").removeClass('mand');
                                $("#credit_days").css('display','none');
                            }
                        });


                      function checkpdf()
                      {
                        var payment_type=$("#payment_type").val();
                        if(payment_type==4 || payment_type==5)
                        {
                            $("#paymentterms").addClass('mand');
                            $("#credit_days").css('display','');
                        }else
                        {
                             $("#paymentterms").removeClass('mand');
                             $("#credit_days").css('display','none');
                        }

                      }

                  </script>


                     <div class="col-md-4" id="credit_days" style="display: none;">
                    <label>Credit Days<span style="color: red;">*</span></label>
                    <span class="input-icon icon-right" style="margin-bottom:10px">
                   <input type="number" name="paymentterms" id="paymentterms" class="form-control" value="<?php echo $credit_days;?>">
                   
                    </span>
                  </div>
                </div>

                <br>
                <hr>
                <br>

                <div class="row" style="margin-top:20px;">
                  <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                      <div class="page-title-box">                           
                          <h4 class="page-title text-center">&nbsp Billing Address</h4>
                      </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-3">
                      <label>Bill To<span style="color: red;">*</span></label>
                      <input type="text" name="bill_to" id="bill_to" autocomplete="nope" class="form-control mand" value="<?php echo $bill_to;?>" required>
                    </div>
                    <div class="col-md-3" style="display:none">
                      <label>Customer Name<span style="color: red;">*</span></label>
                      <input type="text" name="billing_name" id="billing_name" value="<?php echo $billing_name;?>" autocomplete="nope" class="form-control">
                    </div>
                    <div class="col-md-3">
                      <label>Address<span style="color: red;">*</span></label>
                      <textarea class="form-control" name="billing_address" autocomplete="nope" id="billing_address" required><?php echo $billing_address;?></textarea>
                    </div>
                    <div class="col-md-3">
                      <label>State<span style="color: red;">*</span></label>
                      <select class="form-control" name="billing_state" id="billing_state" required>
                        <option value="">SELECT STATE</option>
                        <?php if($getAllStates != '') {
                                foreach($getAllStates as $rows) {?>
                          <option value="<?php echo $rows->state_id;?>" <?php if($billing_state == $rows->state_id) { echo 'selected'; }?>><?php echo $rows->state_name;?></option>
                        <?php } } ?>
                      </select>
                      <!-- <input type="text" name="state" class="form-control" value="<?php echo $state_name;?>"> -->
                    </div>
                    <div class="col-md-3">
                      <label>City<span style="color: red;">*</span></label>
                      <input type="text" name="billing_city" id="billing_city" value="<?php echo $billing_city;?>" autocomplete="nope" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                      <label>Pincode<span style="color: red;">*</span></label>
                      <input type="text" name="billing_pincode" id="billing_pincode" value="<?php echo $billing_pincode;?>" autocomplete="nope" class="form-control" onblur="check_billing_pincode()" required>
                    </div>
                    <div class="col-md-3">
                      <label>Phone No</label>
                      <input type="text" name="billing_phone_no" id="billing_phone_no" value="<?php echo $billing_phone_no;?>" autocomplete="nope" class="form-control">
                    </div>
                    <div class="col-md-3">
                      <label>Mobile No<span style="color: red;">*</span></label>
                      <input type="text" name="billing_mobile_no" id="billing_mobile_no" value="<?php echo $billing_mobile_no;?>" autocomplete="nope" class="form-control" onblur="check_billing_mobile()" required>
                    </div>
                    <div class="col-md-3">
                      <label>Email<span style="color: red;"></span></label>
                      <input type="text" name="billing_email" id="billing_email" value="<?php echo $billing_email;?>" autocomplete="nope" class="form-control" onblur="check_billing_email()">
                    </div>
                  </div>
                </div>

                <div class="row text-center" style="margin-top:50px;">
                  <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                      <div class="page-title-box">
                          <label>Billing Address same as Delivery Address?</label>
                          <input type="checkbox" id="check_billing" name="check_billing" value="1" <?php if($same_shipping_billing == 1) { echo 'checked';}?> onchange="check_address()">
                      </div>
                  </div>
                </div>

                <div class="row" style="margin-top:20px;">
                  <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                      <div class="page-title-box">                           
                          <h4 class="page-title text-center">&nbsp Shipping Address</h4>
                      </div>
                  </div>
                </div>

                    <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-3">
                      <label>Ship To<span style="color: red;">*</span></label>
                      <input type="text" name="ship_to" id="ship_to" autocomplete="nope" class="form-control mand" value="<?php echo $bill_to;?>" required>
                    </div>
                    <div class="col-md-3" style="display: none;">
                      <label>Customer Name<span style="color: red;">*</span></label>
                      <input type="text" name="shipping_name" id="shipping_name" value="<?php echo $shipping_name;?>" autocomplete="nope" class="form-control">
                    </div>
                    <div class="col-md-3">
                      <label>Address<span style="color: red;">*</span></label>
                      <textarea class="form-control" name="shipping_address"autocomplete="nope" id="shipping_address" required><?php echo $shipping_address;?></textarea>
                    </div>
                    <div class="col-md-3">
                      <label>State<span style="color: red;">*</span></label>
                      <select class="form-control" name="shipping_state" id="shipping_state" required>
                        <option value="">SELECT STATE</option>
                        <?php if($getAllStates != '') {
                                foreach($getAllStates as $rows) {?>
                          <option value="<?php echo $rows->state_id;?>" <?php if($shipping_state == $rows->state_id) { echo 'selected';}?>><?php echo $rows->state_name;?></option>
                        <?php } } ?>
                      </select>
                      <!-- <input type="text" name="state" class="form-control" value="<?php echo $state_name;?>"> -->
                    </div>
                    <div class="col-md-3">
                      <label>City<span style="color: red;">*</span></label>
                      <input type="text" name="shipping_city" id="shipping_city" value="<?php echo $shipping_city;?>" autocomplete="nope" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                      <label>Pincode<span style="color: red;">*</span></label>
                      <input type="text" name="shipping_pincode" id="shipping_pincode" value="<?php echo $shipping_pincode;?>" autocomplete="nope" class="form-control" onblur="check_shipping_pincode()" required>
                    </div>
                    <div class="col-md-3">
                      <label>Phone No</label>
                      <input type="text" name="shipping_phone_no" id="shipping_phone_no" value="<?php echo $shipping_phone_no;?>" autocomplete="nope" class="form-control">
                    </div>
                    <div class="col-md-3">
                      <label>Mobile No<span style="color: red;">*</span></label>
                      <input type="text" name="shipping_mobile_no" id="shipping_mobile_no" value="<?php echo $shipping_mobile_no;?>" autocomplete="nope" class="form-control" onblur="check_shipping_mobile()" required>
                    </div>
                    <div class="col-md-3">
                      <label>Email<span style="color: red;"></span></label>
                      <input type="text" name="shipping_email" id="shipping_email" value="<?php echo $shipping_email;?>" autocomplete="nope" class="form-control" onblur="check_shipping_email()">
                    </div>
                  </div>
                </div>
                
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">                           
                            <h4 class="page-title text-center">&nbsp Tax Registration Details</h4>
                        </div>
                    </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                     <div class="col-md-3">
                      <label>MSME No.</label>
                      <input type="text" name="msme_no" id="msme_no" class="form-control" value="<?php echo $msme_no;?>" autocomplete="nope">
                    </div>
                    <div class="col-md-3">
                      <label>PAN/IT No.<span style="color: red;">*</span></label>
                      <input type="text" name="pan_no" id="pan_no" class="form-control" value="<?php echo $pan_no;?>" onblur="check_pan()" required>
                    </div>
                    <div class="col-md-3">
                      <label>Registration Type<span style="color: red;">*</span></label>
                      <input type="text" name="registration_type" class="form-control" value="<?php echo $registration_type;?>" readonly>
                    </div>
                    <div class="col-md-3">
                      <label>GSTIN/UIN<span style="color: red;">*</span></label>
                      <input type="text" name="gst_no" class="form-control" id="gst_no" onblur="validate_gst()" value="<?php echo $gst_no;?>" required>
                    </div>
                  </div>
                </div>
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">                           
                            <h4 class="page-title text-center">&nbsp Payment Terms</h4>
                        </div>
                    </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-3">
                      <label>Reference<span style="color: red;"></span></label>
                      <input type="text" name="reference" class="form-control" value="<?php echo $reference;?>">
                    </div>
                    <div class="col-md-9">
                      <label>Instructions for Billing & Dispatch Department<span style="color: red;"></span></label>
                      <textarea class="form-control" name="note"><?php echo $note;?></textarea>
                    </div>
                  </div>
                </div>

                <div class="p-t-10 pull-right">

                  <input type="submit" class="btn btn-sm btn-primary" style="background-color: #383b43 !important;border: 1px solid #383b43 !important;" id="saves" value="Generate Order">

                </div>

                <div class="marginbottom"></div>

                <div class="clearfix"></div>




            </div>
          </div>
              </form>

          <!-- end row -->





          <!-- Footer -->

          <?php $this->load->view('common/footer'); ?>

          <!-- End Footer -->



        </div>

        <!-- end container -->



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


          /** REMOVE ANY VALIDATION **/


          /** END **/




          $('#example').dataTable({

            "bProcessing": true,

            "pagination": true

          });



          $('#example1').dataTable({

            "bProcessing": true,

            "pagination": true,
              pageLength:50,

            "sAjaxSource": "<?php echo page_url; ?>Leads/customer_remarks_list/<?php echo $this->uri->segment(3); ?>",

            "aoColumns": [

              {
                mData: 'sr_no'
              },

              {
                mData: 'remarks'
              },

              {
                mData: 'added_on'
              },

              {
                mData: 'name'
              }



            ]

          });

          $('#example2').dataTable({

            "bProcessing": true,

            "pagination": true,

            "sAjaxSource": "<?php echo page_url; ?>Leads/call_history_list/<?php echo $this->uri->segment(3); ?>",

            "aoColumns": [

              {
                mData: 'sr_no'
              },

              {
                mData: 'called_by'
              },

              {
                mData: 'call_Date'
              },

              {
                mData: 'start_time'
              },

              {
                mData: 'end_time'
              }



            ]

          });

          $('#example3').dataTable({

            "bProcessing": true,

            "pagination": true,

            "sAjaxSource": "<?php echo page_url; ?>Leads/customer_mail_history_list/<?php echo $this->uri->segment(3); ?>",

            "aoColumns": [

              {
                mData: 'sr_no'
              },

              {
                mData: 'Send_to'
              },

              {
                mData: 'subject'
              },

              {
                mData: 'content'
              },

              {
                mData: 'file'
              },

              {
                mData: 'remark'
              },

              {
                mData: 'date'
              }



            ]

          });

          $('#example4').dataTable({

            "bProcessing": true,

            "pagination": true,

            "sAjaxSource": "<?php echo page_url; ?>Leads/shared_lead_list/<?php echo $this->uri->segment(3); ?>",

            "aoColumns": [

              {
                mData: 'sr_no'
              },

              {
                mData: 'name'
              },

              {
                mData: 'added_on'
              }



            ]

          });


        });
      </script>



      <script type="text/javascript">
        $(document).ready(function() {
          getproduct();
          $('.select3').select2({});


          var url = "<?php echo page_url; ?>Leads/getUsers";



          $('#participants').select2({

            placeholder: 'TYPE TO SELECT',

            minmumInputLength: 4,

            allowClear: true,

            multiple: true,



            ajax: {

              url: url,

              dataType: 'json',

              delay: 250,



              processResults: function(data) {

                return {

                  results: data

                };

              },

              cache: true

            }



          });

          jQuery('#datepicker').datepicker({

                        autoclose: true,

                        todayHighlight: true,

                        format: 'dd-mm-yyyy'

                        // startDate: new Date()

                      });

          jQuery('#pdc_date').datepicker({

            autoclose: true,

            todayHighlight: true,

            format: 'dd-mm-yyyy'

            // startDate: new Date()

          });

          jQuery('#followup_date').datepicker({

            autoclose: true,

            todayHighlight: true,

            format: 'dd-mm-yyyy'

            // startDate: new Date()

          });



          jQuery('#meeting_date').datepicker({

            autoclose: true,

            todayHighlight: true,

            format: 'dd-mm-yyyy'

            // startDate: new Date()



          });



          $('#meeting_time').timepicker({

            defaultTime: false,

            showMeridian: false

          });



          jQuery('#reminder_date').datepicker({

            autoclose: true,

            todayHighlight: true,

            format: 'dd-mm-yyyy'

            // startDate: new Date()



          });



          $('#reminder_time').timepicker({



            defaultTime: false,

            showMeridian: false



          });



        });
      </script>



      <script type="text/javascript">
        function getFields() {
          var lead_stage_id = $("#leadquality").val();
          var leadquality_name = $("#leadquality option:selected").text();
          $("#remark_title").val(leadquality_name);
          $("#followup").css('display', 'none');
          $("#nonqualifiedreason").css('display', 'none');
          $(".product_details").css('display', 'none');
          $(".product_detailsedit").css('display', 'none');
          $("#terms").css('display', 'none');
          $("#termscondition").attr('required', false);
          $("#customergst").attr('required', false);
          $("#gst").css('display', 'none');
          $("#fcharges").css('display', 'none');

          $.ajax({
            type: "post",
            url: "<?php echo page_url; ?>Leads/getLeadStageDetails",
            data: {
              lead_stage_id: lead_stage_id
            },
            success: function(data) {


              var arr = data.split('|');
              var quotation_step = arr[0];
              var quotation_revised_step = arr[1];
              var pi_step = arr[2];
              var pi_revised_step = arr[3];
              var followup_date = arr[4];
              var reason = arr[5];

              if (quotation_step == 1) {

                $(".product_details").css('display', '');
              } else if (quotation_revised_step == 1) {
                $(".product_details").css('display', '');

              } else if (pi_step == 1 || pi_revised_step == 1) {
                $(".product_detailsedit").css('display', '');
                $("#terms").css('display', '');
                $("#termscondition").attr('required', true);
                $("#customergst").attr('required', true);
                $("#gst").css('display', '');
              } else if (followup_date == 1) {
                $("#followup").css('display', '');
              } else if (reason == 1) {
                $("#nonqualifiedreason").css('display', '');
              }
            }
          });
        }

          function getnetamt(i) {
            var qty = $('#qty' + i).val();
            var listprice = $('#listprice' + i).val();
            var discountprice = $('#discountprice' + i).val();
            var discountpricehide = $('#discountpricehide' + i).val();
            

            if(parseFloat(listprice) > 0) {
                $("#discountprice" + i).attr('readonly', false);
                $('#netprice' + i).val(listprice);
            } else {
                $("#discountprice" + i).attr('readonly', true);
                $('#netprice' + i).val(0);
            }

            if (parseFloat(discountprice) > parseFloat(discountpricehide)) {
                if (confirm('Discount Price is more than ' + discountpricehide + '. Are you sure you want to continue with this discount?')) {

                    if (parseFloat(listprice) > 0 && parseFloat(discountprice) != '') {
                        var netamt = parseFloat(listprice) - parseFloat(discountprice);
                        $('#netprice' + i).val(netamt);
                    } else {
                        $('#netprice' + i).val(0);
                    }
                } else {
                    
                    $("#discountprice" + i).val('');
                }
            } else {
                var netamt = parseFloat(listprice) - parseFloat(discountprice);
                $('#netprice' + i).val(netamt);
            }

        }

          function getnetamtedit(i) {
            var qty = $('#qtyedit' + i).val();

            var listprice = $('#listpriceedit' + i).val();
            var discountprice = $('#discountpriceedit' + i).val();
            var discountpricehide = $('#discountpricehideedit' + i).val();
        
            if(parseFloat(listprice) > 0) {
                $("#discountpriceedit" + i).attr('readonly', false);
                $('#netpriceedit' + i).val(listprice);
            } else {
                $("#discountpriceedit" + i).attr('readonly', true);
                $('#netpriceedit' + i).val(0);
            }

            if (parseFloat(discountprice) > parseFloat(discountpricehide)) {
                if (confirm('Discount Price is more than ' + discountpricehide + '. Are you sure you want to continue with this discount?')) {

                    if (parseFloat(listprice) > 0 && parseFloat(discountprice) != '') {
                        var netamt = parseFloat(listprice) - parseFloat(discountprice);
                        $('#netpriceedit' + i).val(netamt);
                    } else {
                        $('#netpriceedit' + i).val(0);
                    }
                } else {
                    
                    $("#discountpriceedit" + i).val('');
                }
            } else {
                var netamt = parseFloat(listprice) - parseFloat(discountprice);
                $('#netpriceedit' + i).val(netamt);
            }

        }

        function add_product_data()
        {
            if($('#add_product').is(":checked"))
            {
                $("#shownewproduct").css('display','');

                $("#product0").addClass('mand');
                $("#qty0").addClass('mand');
                $("#listprice0").addClass('mand');
                $("#discountprice0").addClass('mand');
                $("#netprice0").addClass('mand');
                $('.remarkbox').css('display','');


            }else
            {
                 $("#shownewproduct").css('display','');
                $("#product0").removeClass('mand');
                $("#qty0").removeClass('mand');
                $("#listprice0").removeClass('mand');
                $("#discountprice0").removeClass('mand');
                $("#netprice0").removeClass('mand');
            }


        }

        
        function getprice(i, pid) {
            var proid = pid;
            
            if (proid != '') {
                $.ajax({
                    type: "post",
                    url: "<?php echo page_url; ?>Customer/getproductpricedata",
                    data: "proid=" + proid,
                    success: function(data) {
                        var arr = data.split('|');
                        if(arr[0]>0)
                        {
                        $("#listprice" + i).val(arr[0]);
                        $("#netprice" + i).val(arr[0]);
                        }else
                        {
                        $("#listprice" + i).val('');
                        $("#netprice" + i).val('');
                        }
                        $(".list_price_unit" + i).text(arr[1]);
                        $("#discountpriceshow" + i).css('display', '');

                        if (arr[0] > 0) {
                            $("#discountprice" + i).attr('readonly', false);
                        } else {
                            $("#discountprice" + i).val(0);
                            $("#discountprice" + i).attr('readonly', true);
                        }
                    }
                });
            }
        }

        function getdiscount(i, pid) {
            var proid = pid;
            $("#discountpriceshow" + i).css('display', 'none');

            if (proid != '') {
                $.ajax({
                    type: "post",
                    url: "<?php echo page_url; ?>Customer/getdiscountpricedata",
                    data: "proid=" + proid,
                    success: function(data) {
                        $("#discountpricehide"+i).val(data);
                        $("#discountpriceshow" + i).text('Maximum Discount Allowed: '+data);
                        $("#discountpriceshow" + i).css('color', 'red');
                        $("#discountpriceshow" + i).css('font-weight', 'bold');
                    }
                });
            }
        }

        function allow_decimal(data) {
            var self = $("#" + data);
            self.val(self.val().replace(/[^0-9\.]/g, ''));
            if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
                evt.preventDefault();
            }
        }


        function check_shipping_pincode() {
              var shipping_pincode = $('#shipping_pincode').val();
              var zipRegex = /^\d{6}$/;

              if (!zipRegex.test(shipping_pincode))
              {
                  alert('Invalid Pincode!');
                  $('#shipping_pincode').val('');
              }
        }

        function check_shipping_email() {
          var shipping_email = $('#shipping_email').val();
          var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            
            if (!regex.test(shipping_email)) {
              alert('Invalid Email ID!');
              $('#shipping_email').val('');
            }
        }

        function check_shipping_mobile() {
          var shipping_mobile_no = $('#shipping_mobile_no').val();
          var regex = /^[7-9][0-9]{9}$/;
            
            if (!regex.test(shipping_mobile_no)) {
              alert('Invalid Mobile No!');
              $('#shipping_mobile_no').val('');
            }
        }

        function check_billing_pincode() {
              var billing_pincode = $('#billing_pincode').val();
              var zipRegex = /^\d{6}$/;

              if (!zipRegex.test(billing_pincode))
              {
                  alert('Invalid Pincode!');
                  $('#billing_pincode').val('');
              }
        }

        function check_billing_email() {
          var billing_email = $('#billing_email').val();
          var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            
            if (!regex.test(billing_email)) {
              alert('Invalid Email ID!');
              $('#billing_email').val('');
            }
        }

        function check_billing_mobile() {
          var billing_mobile_no = $('#billing_mobile_no').val();
          var regex = /^[7-9][0-9]{9}$/;
            
            if (!regex.test(billing_mobile_no)) {
              alert('Invalid Mobile No!');
              $('#billing_mobile_no').val('');
            }
        }

         function check_pan() {
          var pan_no = $('#pan_no').val();
          var regex = /[a-zA-z]{5}\d{4}[a-zA-Z]{1}/;
            
            if (!regex.test(pan_no)) {
              alert('Invalid PAN No!');
              $('#pan_no').val('');
            }
        }

        function validate_gst() {
           var gstinVal = $('#gst_no').val();
           var reggst = /^([0-9]){2}([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}([0-9]){1}([a-zA-Z]){1}([0-9]){1}?$/;

            if(!reggst.test(gstinVal) && gstinVal!=''){
                    alert('GST Identification Number is not valid. It should be in this "11AAAAA1111Z1A1" format');
                    $('#gst').val('');
            }
        }

        function CKEditorChange(name) {
          CKEDITOR.replace(name, {
            toolbar: [{
                name: 'clipboard',
                items: ['Undo', 'Redo']
              },
              {
                name: 'styles',
                items: ['Format', 'Font', 'FontSize']
              },
              {
                name: 'basicstyles',
                items: ['Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat', 'CopyFormatting']
              },
              {
                name: 'colors',
                items: ['TextColor', 'BGColor']
              },
              {
                name: 'align',
                items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']
              },
              {
                name: 'links',
                items: ['Link', 'Unlink']
              },
              {
                name: 'paragraph',
                items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote']
              },
              {
                name: 'insert',
                items: ['Image', 'Table']
              },
              {
                name: 'tools',
                items: ['Maximize']
              },
              {
                name: 'editing',
                items: ['Scayt']
              }
            ]
          });
        }


        function validate() {

          $("#saves").attr('disabled', false);
          $("#saves").val('Update Followup Remarks');
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
            $("#saves").val('Update Followup Remarks');
            alert('All fields marked with (*) are mandatory');
            return false;
          }
        }



        function removeFollowup(id) {

          if (id == 11 || id == 2 || id == 6 || id == 5) {

            $("#followup").css('display', 'none');

            $("#followup_date").removeClass('mand');

          }

        }


        function check_pincode() {
              var pincode = $('#pincode').val();
              var zipRegex = /^\d{6}$/;

              if (!zipRegex.test(pincode))
              {
                  alert('Invalid Pincode!');
                  $('#pincode').val('');
              }
        }

        function check_email() {
          var email = $('#email').val();
          var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            
            if (!regex.test(email)) {
              alert('Invalid Email ID!');
              $('#email').val('');
            }
        }

        function check_mobile() {
          var mobile_no = $('#mobile_no').val();
          var regex = /^[7-9][0-9]{9}$/;
            
            if (!regex.test(mobile_no)) {
              alert('Invalid Mobile No!');
              $('#mobile_no').val('');
            }
        }

         function check_pan() {
          var pan_no = $('#pan_no').val();
          var regex = /[a-zA-z]{5}\d{4}[a-zA-Z]{1}/;
            
            if (!regex.test(pan_no)) {
              alert('Invalid PAN No!');
              $('#pan_no').val('');
            }
        }

        function validate_gst() {
           var gstinVal = $('#gst_no').val();
           var reggst = /^([0-9]){2}([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}([0-9]){1}([a-zA-Z]){1}([0-9]){1}?$/;

            if(!reggst.test(gstinVal) && gstinVal!=''){
                    alert('GST Identification Number is not valid. It should be in this "11AAAAA1111Z1A1" format');
                    $('#gst').val('');
            }
        }

        function getunit(i, product) {
            $("#batch_code_div"+i).css('display','none');
            $("#batch_code"+i).attr('required',false);
            $("#batch_code"+i).removeClass('mand');
             if (product != '') {
                $.ajax({
                    type: "post",
                    url: "<?php echo page_url; ?>Customer/getunit",
                    data: "proid=" + product,
                    success: function(data) {
                        // alert("#pack_size"+i);
                        var arr = data.split('|');
                        $("#unit_name"+i).val(arr[0]);
                        $("#pack_size"+i).val(arr[1]);
                        if(arr[2]=='DRUM')
                        {
                            $("#batch_code_div"+i).css('display','');
                            $("#batch_code"+i).attr('required',true);
                            $("#batch_code"+i).addClass('mand');
                        }


                    }
                });
            }
        }


        function show_discount(id) {

          $(".discount_type" + id).css('display', 'none');
          var discount_type = $("#discount_type" + id).val();

          if (discount_type != 3) {
            $(".discount_type" + id).css('display', '');

          }

        }



        function show_discountedit(id) {

          $(".discount_typeedits" + id).css('display', 'none');
          var discount_type = $("#discount_typeedit" + id).val();
          if (discount_type != 3) {

            $(".discount_typeedits" + id).css('display', '');


          }

        }

        function getPaymentInfo() {
          var payment_type = $("#payment_type").val();
          $("#show_cheque_no").css('display', 'none');
          $("#show_utr_no").css('display', 'none');
          $("#show_pdc_date").css('display', 'none');
          $("#cheque_no").removeClass('mand');
          $("#utr_no").removeClass('mand');
          $("#pdc_date").removeClass('mand');

          if (payment_type == 1) {
            $("#show_cheque_no").css('display', '');
            $("#cheque_no").addClass('mand');
          } else if (payment_type == 3) {
            $("#show_utr_no").css('display', '');
            $("#utr_no").addClass('mand');
          } else if (payment_type == 4) {
            $("#show_cheque_no").css('display', '');
            $("#show_pdc_date").css('display', '');
            $("#cheque_no").addClass('mand');
            $("#pdc_date").addClass('mand');
          }
        }

        function check_address() {

               var billing_name = $('#billing_name').val();
              var billing_address = $('#billing_address').val();
              var billing_state = $('#billing_state').val();
              var billing_city = $('#billing_city').val();
              var billing_pincode = $('#billing_pincode').val();
              var billing_contact_person = $('#billing_contact_person').val();
              var billing_phone_no = $('#billing_phone_no').val();
              var billing_mobile_no = $('#billing_mobile_no').val();
              var billing_email = $('#billing_email').val();

              if($('input[name=check_billing]').is(':checked')) {
                  $('#shipping_name').val(billing_name);
                  $('#shipping_address').val(billing_address);
                  $('#shipping_state').val(billing_state);
                  $('#shipping_city').val(billing_city);
                  $('#shipping_pincode').val(billing_pincode);
                  $('#shipping_contact_person').val(billing_contact_person);
                  $('#shipping_phone_no').val(billing_phone_no);
                  $('#shipping_mobile_no').val(billing_mobile_no);
                  $('#shipping_email').val(billing_email);
              } else {
                  $('#shipping_name').val('');
                  $('#shipping_address').val('');
                  $('#shipping_state').val('');
                  $('#shipping_city').val('');
                  $('#shipping_pincode').val('');
                  $('#shipping_contact_person').val('');
                  $('#shipping_phone_no').val('');
                  $('#shipping_mobile_no').val('');
                  $('#shipping_email').val('');
              }
        }

        function add_new_product() {
          var add_new = $('input[name="add_new"]:checked').val();
          $("#show").css('display', 'none');

            if(add_new == 1) {
                $("#show").css('display', '');
            }
        }

        function delete_product(id) {
            if(confirm('Are you sure you want to delete this product?')) {
                $.ajax({
                    type: "post",
                    url: "<?php echo page_url; ?>Customer/delete_product",
                    data: {id: id},
                    success: function(data) {
                      if(data == 1) {
                        $(".delete_row"+id).remove();
                      }
                    }
                });
            }
        }

      </script>

<script>
        function openNav() {
            document.getElementById("mySidenav").style.width = "450px";
        }

        function closeNav() {
            document.getElementById("mySidenav").style.width = "0";
        }
    </script>

    <script type="text/javascript">
      $(document).ready(function(){
        var purl="<?php echo page_url;?>Open_leads/getrecommendations";
              $('#comp_product0').select2({ 
                placeholder: 'TYPE TO SELECT',
                minmumInputLength:4,
                allowClear: true,
                tags:true,
                ajax: {
                url: purl,
                dataType: 'json',
                delay: 250,
              data: function (params) {

                return {
                searchTerm: params.term
              };

              },
              processResults: function (data) {
              return {
                results: data
                };
                },
              cache: true

              }
        });


        $('#product0').select2({ })

        var i = 1;
            $('#addmore_btn').click(function() {
                $('#dynamictasks').append('<div id="row' + i + '" class="row"><div class="col-md-12"><div class="col-md-2"><div class="form-group"><label for="field-3" class="control-label">Competitor Product</label><select class="form-control" name="comp_product[]" id="comp_product'+i+'" onchange="getourproductname('+i+');"></select></div></div><div class="col-md-3"><div class="form-group"><label for="field-3" class="control-label">Product</label><span id="error_rack_location" style="color:red;">*</span><select class="form-control mand products" name="product[]" id="product' + i + '" required onchange="getprice(' + i + ',this.value);getdiscount(' + i + ',this.value); getbatchcode('+i+',this.value); getunit('+i+',this.value);getstock1('+i+');"><option value="">Select</option></select><span id="recommendation'+i+'"></span></div></div><div class="col-md-1"><div class="form-group"><label for="field-2" class="control-label">Avl Stock</label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control" name="stock[]" id="stock_new'+i+'" autocomplete="off" readonly></div></div><div class="col-md-1"><div class="form-group"><label for="field-2" class="control-label">Qty</label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control mand" name="qty[]" id="qty_new' + i + '" oninput="allow_decimal("qty_new'+i+'");" onkeyup="match_stock_availability_new('+i+');"></div></div><div class="col-md-1"><div class="form-group"><label for="field-2" class="control-label">Unit</label><span id="error_rack_location" style="color:red;">*</span><input type="text" name="unit_name[]" id="unit_name'+i+'" class="form-control" readonly><input type="hidden" name="pack_size[]" id="pack_size'+i+'"></div></div><div class="col-md-2" id="batch_code_div'+i+'" style="display:none;"><div class="form-group"><label for="field-3" class="control-label">Batch Code</label><select class="form-control  mand select2" name="batch_code[]" id="batch_code'+i+'" ><option value="">Select Batch Code</option></select></div></div><div class="col-md-1"><div class="form-group"><label for="field-2" class="control-label">Order Price/<span class="list_price_unit'+i+'"></span></label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control mand" name="listprice[]" id="listprice' + i + '" oninput="allow_decimal("listprice' + i + '");"  onblur="getnetamt(' + i + ')" required><p id="discountpriceshow'+i+'" style="display: none;"></p><input type="hidden" name="discountpricehide[]" id="discountpricehide' + i + '" ></div></div><div class="col-md-2" style="display:none;"><div class="form-group"><label for="field-2" class="control-label">Discount Price Per <span class="list_price_unit'+i+'"></span></label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control" value="0" name="discountprice[]" id="discountprice' + i + '" value="0" onblur="getnetamt(' + i + ')" oninput="allow_decimal("discountprice' + i + '");"></div></div><div class="col-md-2" style="display:none;"><div class="form-group"><label for="field-2" class="control-label">Net Price</label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control" name="netprice[]" id="netprice' + i + '" value="0" readonly> </div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:23px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="' + i + '"><i class="fa fa-close"></i></button></div></div></div><br/>');


                getproductname(i);
                initializeSelect2("comp_product"+i);
                initializeSelect2_product("product"+i);
                i++;
            });

            $(document).on('click', '.btn_remove', function() {
                var button_id = $(this).attr("id");
                $('#row' + button_id + '').remove();
            });
      });

    
       function initializeSelect2(selectElementObj) {
   
        var purl="<?php echo page_url;?>Open_leads/getrecommendations";

                $('#'+selectElementObj).select2({ 
                  placeholder: 'TYPE TO SELECT',
                  minmumInputLength:4,
                  allowClear: true,
                  tags:true,
                  ajax: {
                  url: purl,
                  dataType: 'json',
                  delay: 250,
                data: function (params) {
                  return {
                  searchTerm: params.term
                  };
                },
                processResults: function (data) {
                return {
                  results: data
                  };
                  },
                  cache: true

                }
                });
         
            }

       function initializeSelect2_product(selectElementObj) {
            $('#'+selectElementObj).select2({ });
      }

      function getproduct() {
            var proid = $('#company').val();
            //alert(custid);
            if (proid != '') {
                $.ajax({
                    type: "post",
                    url: "<?php echo page_url; ?>Customer/getCompanyProduct",
                    data: "proid=" + proid,
                    success: function(data) {
                        //alert(data);
                        $(".products").html(data);
                    }
                });
            }
        }

      function getproductname(i) {
            var proid = $('#company').val();
            //alert(custid);
            if (proid != '') {
                $.ajax({
                    type: "post",
                    url: "<?php echo page_url; ?>Customer/getproductdata",
                    data: "proid=" + proid,
                    success: function(data) {
                        //alert(data);
                        $("#product" + i).html(data);
                    }
                });
            }
        }

        function getCustomerDetails() {
            var custid = $('#customer').val();

            if (custid != '') {
                $.ajax({
                    type: "post",
                    url: "<?php echo page_url; ?>Customer/getCustomerDetails_frommaster",
                    data: "custid=" + custid,
                    success: function(data) {
                        var arr = data.split('|');
                
                        $("#billing_address").val(arr[0]);
                        $("#billing_state").val(arr[1]);
                        $("#billing_city").val(arr[2]);
                        $("#billing_pincode").val(arr[3]);
                        $("#billing_email").val(arr[4]);
                        $("#msme_no").val(arr[5]);
                        $("#gst_no").val(arr[6]);
                        $("#pan_no").val(arr[7]);
                        $("#bill_to").val(arr[13]);
                        $("#ship_to").val(arr[13]);
                        $("#billing_mobile_no").val(arr[14]);
                      
                        check_shipping_pincode();
                        check_shipping_email();
                        validate_gst();
                        check_shipping_mobile();
                        check_billing_pincode();
                        check_billing_email();
                        check_billing_mobile();
                        check_pan();


                    

                    }
                });
            }
        }


        function getcustomers()
        {


            var company=$("#company").val();
            if(company!='')
            {
                $('#customer').empty()
                    $.ajax({
                    type: "post",
                    url: "<?php echo page_url; ?>Customer/getcustomerdataCompanyWise",
                    data: "comp="+company,
                    success: function(data) {
                    $("#customer").html(data);
                    }
                    });
            }


        }

        function match_stock_availability1(flag)
        {
             var product=$("#product"+flag).val();
           var stock_avail=$("#stock_edit"+flag).val();
           if(stock_avail!=''){ var stock_avail=stock_avail}else{ var stock_avail=0;}
            var qty_edit=$("#qty_edit"+flag).val();
            if(qty_edit!=''){ var qty_edit=qty_edit}else{ var qty_edit=0;}

             if(parseFloat(qty_edit)>parseFloat(stock_avail))
            {
                alert('Available Stock is less than inputted Qty. Please change the Qty');
                $("#qty_edit"+flag).val('');
            }

        }

         function getstock1(flag)
        {
             $("#save").attr('disabled',true);
             var hpcl_company = $("#company").val();
             var product=$("#product"+flag).val();

               $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Leads/check_stock_available",
                data:"compid="+hpcl_company+"&product_id="+product,
                success: function(data) {

                    $("#stock_new"+flag).val(data);
                    $("#save").attr('disabled',false);
                    
                }
                });

        }

         function match_stock_availability_new(flag)
        {
                      
                var stock_avail=$("#stock_new"+flag).val();
                if(stock_avail!=''){ var stock_avail=stock_avail; }else{ var stock_avail=0;}
                var qty_edit=$("#qty_new"+flag).val();
                if(qty_edit!=''){ var qty_edit=qty_edit; }else{ var qty_edit=0;}
                if(parseFloat(qty_edit)>parseFloat(stock_avail))
                {
                alert('Available Stock is less than inputted Qty. Please change the Qty');
                $("#qty_new"+flag).val('');
                }

        }

         function getbatchcode(i, pid) {
            var proid = pid;
            
            if (proid != '') {
                $.ajax({
                    type: "post",
                    url: "<?php echo page_url; ?>Customer/getproductbatchcode",
                    data: "proid=" + proid,
                    success: function(data) {
                      $("#batch_code"+i).html(data);

                    }
                });
            }
        }

        function getourproductname(id)
        {

        }
    </script>

</body>

</html>