<?php
$CI = &get_instance();
$CI->load->model('Salescrm_model', 'salescrm');
$MI = &get_instance();
$MI->load->model('Master_model', 'master');
$DI = &get_instance();
$DI->load->model('Dashboard_model');

$getQuotationInfo=$CI->salescrm->getQuotationInfo($this->uri->segment(4));
$productdetail=$CI->salescrm->getQuotationProducts($this->uri->segment(4));

if($getQuotationInfo != '') {
  foreach ($getQuotationInfo as $row);
  $validity_date = date('d-m-Y', strtotime($row->validity_date));
  $company_name = $row->companyname;
  $customer_name = $row->customer_name;
} else {
  $validity_date = '';
  $company_name = '';
  $customer_name = '';
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
    <span style="font-size: 24px;
    cursor: pointer;
    position: fixed;
    right: 2px;
    background: #4241bf;
    width: 65px;
    font-weight: 900;
    color: white;
    text-align: center;
    padding: 5px 10px;
    border-radius: 30px 0px 0px 30px;" onclick="openNav()"><i class="fa fa-sticky-note" aria-hidden="true"></i></span>
<!--------------------------------NOTES-------------------------------------->
  <div class="wrapper">
    <div class="container">
      <!-- Page-Title -->
    <form method="post" class="card-box" method="post" action="<?php echo page_url; ?>Customer/save_order_details/<?php echo $this->uri->segment(3); ?>/<?php echo $this->uri->segment(4); ?>" enctype="multipart/form-data" onsubmit="return validate()">
      <div class="row">

        <div class="col-sm-12" >
        <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success btn-xs" name="action_button"><i class="fa fa-arrow-left"></i>Back</button></a>

          <div class=" " style="margin-top: 30px;">
            <div class="profile-info-name">
              <div class="profile-info-detail" style="margin-top:1px">
                <div class="text-center card-box search-page">
                  <h3 class="m-t-0 m-b-0">
                    </h3>
                  <br />
                  <div class="text-center">
                   <!--  <?php

                    if ($quotesend > 0) {
                    ?>
                      <a href="<?php echo page_url1; ?>pdf/rfq/examples/<?php echo $quotefile; ?>?lead_id=<?php echo $this->uri->segment(3); ?>" target="_blank"><span class="btn btn-success btn-xs">Generated Quotation </span></a>&nbsp;&nbsp;
                    <?php
                    }
                    ?>

                    <?php
                    if ($pisend > 0) {
                    ?>
                      <a href="<?php echo page_url1; ?>pdf/rfq/examples/<?php echo $pifile; ?>?lead_id=<?php echo $this->uri->segment(3); ?>" target="_blank"><span class="btn btn-success btn-xs">Generated PI</span></a>
                    <?php } ?> -->

                  </div>
                </div>

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
                          <p><strong><i class="fa fa-calendar" aria-hidden="true" style="color:#f9ab00;"></i>Validity Date:</strong></p>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                          <p><?php echo $validity_date; ?></p>
                        </div>
                      </div>

                      <hr>
                      <!------------------------------------------>

                      <div class="clearfix"></div>
                    </div>

                  </div>
                  <div class="row">
                  <div class="col-sm-12">
                    <div class="card-box search-page" style="height: 305px; overflow-y: auto;">
                      <h5>Commercial</h5>
                       <table>
                                        <tr>
                                            <th>S.no</th>
                                            <th>Product</th>
                                            <th>Qty</th>
                                            <th>List Price</th>
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
                                            <td><input type="hidden" name="quote_detail_id[]" value="<?php echo $row2->id;?>"><input type="text" name="agreed_price[]" class="form-control"></td>
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
                    </div>
                  </div>
                </div>
                </div>

              </div>
            </div>

            <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>




            <!------------------To disable Folloup Use disabled attribute--------------------->

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
                    <select class="form-control" name="payment_type" id="payment_type" onchange="getPaymentInfo()">
                      <option value="">SELECT PAYMENT TYPE</option>
                      <option value="1">Cheque</option>
                      <option value="2">Cash</option>
                      <option value="3">NEFT</option>
                      <option value="4">PDC</option>
                    </select>
                    </span>
                  </div>
                  <div class="col-md-4">
                    <label>Amount<span style="color: red;">*</span></label>
                    <span class="input-icon icon-right" style="margin-bottom:10px">
                    <input type="text" class="form-control" name="total_amount" id="total_amount" autocomplete="off" required>
                    </span>
                  </div>
                  <div class="col-md-4" id="show_cheque_no" style="display: none;">
                    <label>Cheque No<span style="color: red;">*</span></label>
                    <span class="input-icon icon-right" style="margin-bottom:10px">
                    <input type="text" class="form-control" name="cheque_no" id="cheque_no" autocomplete="nope">
                    </span>
                  </div>
                  <div class="col-md-4" id="show_pdc_date" style="display: none;">
                    <label>PDC Date<span style="color: red;">*</span></label>
                    <span class="input-icon icon-right" style="margin-bottom:10px">
                    <input type="text" class="form-control" name="pdc_date" id="pdc_date" autocomplete="nope">
                    </span>
                  </div>
                  <div class="col-md-4" id="show_utr_no" style="display: none;">
                    <label>UTR No<span style="color: red;">*</span></label>
                    <span class="input-icon icon-right" style="margin-bottom:10px">
                    <input type="text" class="form-control" name="utr_no" id="utr_no" autocomplete="nope">
                    </span>
                  </div>
                <div class="col-md-4">
                    <label>Upload PO<span style="color: red;">*</span></label>
                    <span class="input-icon icon-right" style="margin-bottom:10px">
                      <input type="file" name="upload_file" required="">
                    </span>
                </div>
                </div>

                <br>
                <hr>
                <br>

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
                      <label>Name<span style="color: red;">*</span></label>
                      <input type="text" name="shipping_name" id="shipping_name" autocomplete="nope" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                      <label>Address<span style="color: red;">*</span></label>
                      <textarea class="form-control" name="shipping_address" autocomplete="nope" id="shipping_address" required></textarea>
                    </div>
                    <div class="col-md-3">
                      <label>State<span style="color: red;">*</span></label>
                      <select class="form-control" name="shipping_state" id="shipping_state" required>
                        <option value="">SELECT STATE</option>
                        <?php if($getAllStates != '') {
                                foreach($getAllStates as $rows) {?>
                          <option value="<?php echo $rows->state_id;?>"><?php echo $rows->state_name;?></option>
                        <?php } } ?>
                      </select>
                      <!-- <input type="text" name="state" class="form-control" value="<?php echo $state_name;?>"> -->
                    </div>
                    <div class="col-md-3">
                      <label>City<span style="color: red;">*</span></label>
                      <input type="text" name="shipping_city" id="shipping_city" autocomplete="nope" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                      <label>Pincode<span style="color: red;">*</span></label>
                      <input type="text" name="shipping_pincode" id="shipping_pincode" autocomplete="nope" class="form-control" onblur="check_shipping_pincode()" required>
                    </div>
                    <div class="col-md-3">
                      <label>Phone No</label>
                      <input type="text" name="shipping_phone_no" id="shipping_phone_no" autocomplete="nope" class="form-control">
                    </div>
                    <div class="col-md-3">
                      <label>Mobile No<span style="color: red;">*</span></label>
                      <input type="text" name="shipping_mobile_no" id="shipping_mobile_no" autocomplete="nope" class="form-control" onblur="check_shipping_mobile()" required>
                    </div>
                    <div class="col-md-3">
                      <label>Email<span style="color: red;">*</span></label>
                      <input type="text" name="shipping_email" id="shipping_email" autocomplete="nope" class="form-control" onblur="check_shipping_email()" required>
                    </div>
                  </div>
                </div>

                <div class="row text-center" style="margin-top:50px;">
                  <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                      <div class="page-title-box">
                          <label>Billing Address same as Delivery Address?</label>
                          <input type="checkbox" id="check_billing" name="check_billing" value="1" onchange="check_address()">
                      </div>
                  </div>
                </div>

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
                      <label>Name<span style="color: red;">*</span></label>
                      <input type="text" name="billing_name" id="billing_name" autocomplete="nope" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                      <label>Address<span style="color: red;">*</span></label>
                      <textarea class="form-control" name="billing_address" autocomplete="nope" id="billing_address" required></textarea>
                    </div>
                    <div class="col-md-3">
                      <label>State<span style="color: red;">*</span></label>
                      <select class="form-control" name="billing_state" id="billing_state" required>
                        <option value="">SELECT STATE</option>
                        <?php if($getAllStates != '') {
                                foreach($getAllStates as $rows) {?>
                          <option value="<?php echo $rows->state_id;?>"><?php echo $rows->state_name;?></option>
                        <?php } } ?>
                      </select>
                      <!-- <input type="text" name="state" class="form-control" value="<?php echo $state_name;?>"> -->
                    </div>
                    <div class="col-md-3">
                      <label>City<span style="color: red;">*</span></label>
                      <input type="text" name="billing_city" id="billing_city" autocomplete="nope" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                      <label>Pincode<span style="color: red;">*</span></label>
                      <input type="text" name="billing_pincode" id="billing_pincode" autocomplete="nope" class="form-control" onblur="check_billing_pincode()" required>
                    </div>
                    <div class="col-md-3">
                      <label>Phone No</label>
                      <input type="text" name="billing_phone_no" id="billing_phone_no" autocomplete="nope" class="form-control">
                    </div>
                    <div class="col-md-3">
                      <label>Mobile No<span style="color: red;">*</span></label>
                      <input type="text" name="billing_mobile_no" id="billing_mobile_no" autocomplete="nope" class="form-control" onblur="check_billing_mobile()" required>
                    </div>
                    <div class="col-md-3">
                      <label>Email<span style="color: red;">*</span></label>
                      <input type="text" name="billing_email" id="billing_email" autocomplete="nope" class="form-control" onblur="check_billing_email()" required>
                    </div>
                  </div>
                </div>

                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">                           
                            <h4 class="page-title text-center">&nbsp Transport Detail</h4>
                        </div>
                    </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                     <div class="col-md-4">
                          <label>Vehicle No.<span style="color: red;">*</span></label>
                          <input type="text" name="vehicle_no" id="vehicle_no" class="form-control" autocomplete="nope" required>
                      </div>
                       <div class="col-md-4">
                          <label>Vehicle Type<span style="color: red;">*</span></label>
                          <input type="text" name="vehicle_type" id="vehicle_type" class="form-control" autocomplete="nope" required>
                       </div>
                        <div class="col-md-4">
                          <label>Destination<span style="color: red;">*</span></label>
                          <input type="text" name="destination" id="destination" class="form-control" autocomplete="nope" required>
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
                      <label>PAN/IT No.<span style="color: red;">*</span></label>
                      <input type="text" name="pan_no" id="pan_no" class="form-control" autocomplete="nope" onblur="check_pan()" required>
                    </div>
                    <div class="col-md-3">
                      <label>Registration Type<span style="color: red;">*</span></label>
                      <input type="text" name="registration_type" class="form-control" autocomplete="nope" required>
                    </div>
                    <div class="col-md-3">
                      <label>GSTIN/UIN<span style="color: red;">*</span></label>
                      <input type="text" name="gst_no" class="form-control" id="gst_no" autocomplete="nope" onblur="validate_gst()" required>
                    </div>
                  </div>
                </div>
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">                           
                            <h4 class="page-title text-center">&nbsp Other Details</h4>
                        </div>
                    </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-3">
                      <label>Reference<span style="color: red;"></span></label>
                      <input type="text" name="reference" class="form-control" autocomplete="nope">
                    </div>
                    <div class="col-md-3">
                      <label>Note<span style="color: red;"></span></label>
                      <textarea class="form-control" name="note" autocomplete="nope" required></textarea>
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

            "sAjaxSource": "<?php echo page_url; ?>Leads/customer_remarks_list/<?php echo $this->uri->segment(3); ?>",
              pageLength:50,

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

            var i = 1;
            $('#addmore_btn').click(function() {
                $('#dynamictasks').append('<div id="row' + i + '" class="row">   <div class="col-md-12"><div class="col-md-3"><div class="form-group"><label for="field-3" class="control-label">Product</label><span id="error_rack_location" style="color:red;">*</span><select class="form-control" name="product[]" id="product0" onchange="getprice(' + i + ',this.value);getdiscount(' + i + ',this.value);"> <option value="">Select</option> <?php $sql1=$this->db->select('id, instruments_name')->from('presto_instruments')->where('company_id', $customer_detail->hpcl_company)->get(); if($sql1->num_rows()>0){ foreach($sql1->result() as $row4){ ?> <option value="<?php echo $row4->id;?>"><?php echo $row4->instruments_name;?></option> <?php }} ?> </select></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Pack Size</label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control mand" name="qty[]" id="qty' + i + '" required></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">List Price Per <span class="list_price_unit'+i+'"></span></label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control mand" name="listprice[]" id="listprice' + i + '" oninput="allow_decimal("listprice' + i + '");"  onblur="getnetamt(' + i + ')" required></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Discount Price Per <span class="list_price_unit'+i+'"></span></label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control mand" name="discountprice[]" id="discountprice' + i + '" required onblur="getnetamt(' + i + ')" oninput="allow_decimal("discountprice' + i + '");"><p id="discountpriceshow'+i+'" style="display: none;"></p><input type="hidden" name="discountpricehide[]" id="discountpricehide' + i + '" ></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Net Price</label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control" name="netprice[]" id="netprice' + i + '" required readonly> </div></div><div class="col-md-1"><div class="form-group pull-left"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="' + i + '">REMOVE</button></div></div></div><br/>');
                i++;
            });


            $(document).on('click', '.btn_remove', function() {
                var button_id = $(this).attr("id");
                $('#row' + button_id + '').remove();
            });


        });
      </script>



      <script type="text/javascript">
        $(document).ready(function() {



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

          jQuery('#pdc_date').datepicker();

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


        function validate()



        {



          $("#saves").attr('disabled', false);



          $("#saves").val('Update Followup Remarks');







          // $("#form :input").attr('required',false);







          var isValid = 0;



          $(".mand").each(function() {



            var element = $(this).val();



            if (element == "") {







              isValid = 1;



            }











          });











          if (isValid == 0)



          {



            $("#saves").attr('disabled', true);



            $("#saves").val('Please Wait..');



            return true;







          } else



          {



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

              var shipping_name = $('#shipping_name').val();
              var shipping_address = $('#shipping_address').val();
              var shipping_state = $('#shipping_state').val();
              var shipping_city = $('#shipping_city').val();
              var shipping_pincode = $('#shipping_pincode').val();
              var shipping_contact_person = $('#shipping_contact_person').val();
              var shipping_phone_no = $('#shipping_phone_no').val();
              var shipping_mobile_no = $('#shipping_mobile_no').val();
              var shipping_email = $('#shipping_email').val();

              if($('input[name=check_billing]').is(':checked')) {
                  $('#billing_name').val(shipping_name);
                  $('#billing_address').val(shipping_address);
                  $('#billing_state').val(shipping_state);
                  $('#billing_city').val(shipping_city);
                  $('#billing_pincode').val(shipping_pincode);
                  $('#billing_contact_person').val(shipping_contact_person);
                  $('#billing_phone_no').val(shipping_phone_no);
                  $('#billing_mobile_no').val(shipping_mobile_no);
                  $('#billing_email').val(shipping_email);
              } else {
                  $('#billing_name').val('');
                  $('#billing_address').val('');
                  $('#billing_state').val('');
                  $('#billing_city').val('');
                  $('#billing_pincode').val('');
                  $('#billing_contact_person').val('');
                  $('#billing_phone_no').val('');
                  $('#billing_mobile_no').val('');
                  $('#billing_email').val('');
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

</body>

</html>