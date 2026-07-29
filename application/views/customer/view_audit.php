<?php
$CI = &get_instance();
$CI->load->model('Salescrm_model', 'salescrm');
$MI = &get_instance();
$MI->load->model('Master_model', 'master');
$DI = &get_instance();
$DI->load->model('Dashboard_model');

$getOrderDetails = $CI->salescrm->getOrderDetails($this->uri->segment(3));

if ($getOrderDetails != '') {
    foreach ($getOrderDetails as $row1);
    $payment_type = $row1->payment_type;
    $utr_no = $row1->utr_no;
    $cheque_no = $row1->cheque_no;
    if ($row1->pdc_date == '0000-00-00') {
        $pdc_date = '';
    } else {
        $pdc_date = date('d-m-Y', strtotime($row1->pdc_date));
    }
    $invoice_no=$row1->invoice_no;
    $source=$row1->lead_source; 
    $agent=$row1->first_name." ".$row1->last_name;
    $order_date=$row1->added_on;
    $upload_po = $row1->upload_po;
    $ship_to = $row1->ship_to;
    $shipping_name = $row1->shipping_name;
    $shipping_address = $row1->shipping_address;

    $shipping_state = $row1->shipping_state;
    $shipping_state=$CI->salescrm->getstate($shipping_state);
    $shipping_city = $row1->shipping_city;
    $shipping_pincode = $row1->shipping_pincode;
    $shipping_phone_no = $row1->shipping_phone_no;
    $shipping_mobile_no = $row1->shipping_mobile_no;
    $shipping_email = $row1->shipping_email;
    $billing_name = $row1->bill_to;
    $billing_address = $row1->billing_address;
    $billing_state = $row1->billing_state;
    $billing_state=$CI->salescrm->getstate($billing_state);
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
    $registration_type = $row1->registration_type;
    $gst_no = $row1->gst_no;
    $msme_no = $row1->msme_no;
} else {

    echo "Invalid Link"; exit;
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
    $billing_name = '';
    $billing_address = '';
    $billing_state = '';
    $billing_city = '';
    $billing_pincode = '';
    $billing_phone_no = '';
    $billing_mobile_no = '';
    $billing_email = '';
    $credit_terms = '';
    $extra_days = '';
    $max_credit_limit = '';
    $reference = '';
    $note = '';
    $pan_no = '';
    $registration_type = '';
    $gst_no = '';
    $msme_no = '';
}

// echo $pdc_date;exit;

$getQuotationInfo = $CI->salescrm->getQuotationInfo($this->uri->segment(3));
$productdetail = $CI->salescrm->getQuotationProducts($this->uri->segment(4));

if ($getQuotationInfo != '') {
    foreach ($getQuotationInfo as $row);
    // echo $row->validity_date;exit;
    if($row->validity_date == '' || $row->validity_date == '0000-00-00') {
        $validity_date = '';
// echo 'hi'.$row->validity_date;exit;
    } else {
        $validity_date = date('d-m-Y', strtotime($row->validity_date));
    }
    $company_name = $row->company_name;
    $customer_name = $row->customer_name;
} else {
    $validity_date = '';
    $company_name = '';
    $customer_name = '';
}


$getPaymentDetails = $CI->salescrm->getPaymentDetails($this->uri->segment(3));


if($getPaymentDetails) {
    foreach ($getPaymentDetails as $row3);
        if($row3->payment_type == 1) {
            $payment_type = 'Cheque';
        } else if($row3->payment_type == 2) {
            $payment_type = 'Cash';
        } else if($row3->payment_type == 3) {
            $payment_type = 'NEFT';
        } else if($row3->payment_type == 4) {
            $payment_type = 'PDC';
        }  else if($row3->payment_type == 5) {
            $payment_type = 'Credit';
        } else {
            $payment_type = '';
        }

        $total_amount = $row3->total_amount;
        $cheque_no = $row3->cheque_no;
        $pdc_date = $row3->pdc_date;
        $utr_no = $row3->utr_no;
        $upload_po = $row3->upload_po;
        $payment = $row3->payment_type;
        $credit_days = $row3->credit_days;
        $po_no = $row3->po_no;
        $po_date = date('d-m-Y', strtotime($row3->po_date));
} else {
        $payment_type = '';
        $total_amount = '';
        $cheque_no = '';
        $pdc_date = '';
        $utr_no = '';
        $upload_po = '';
        $payment = '';
        $credit_days = '';
        $po_no = '';
        $po_date = '';
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

                                    <span>Create theme</span> <a href="javascript:void(0);" class="float-right remove-todo-item"><i class="icon-close"></i></a>
                                </div>
                                <div class="todo-item">
                                    <span>Work
                                        on wordpress</span> <a href="javascript:void(0);" class="float-right remove-todo-item"><i class="icon-close"></i></a>
                                </div>
                                <div class="todo-item">

                                    <span>Organize office main department</span> <a href="javascript:void(0);" class="float-right remove-todo-item"><i class="icon-close"></i></a>
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

            <!-------=========================================VIEW DETAIL====================================------------>

            <div class="row">
                <div class="col-sm-2"></div>
                <div class="col-sm-8">
                    <h3 class="text-center"><b>Order Details</b></h3>
                    <div class="card-box mt-4 audit__new">
                        <h5>Overview</h5>
                        <div class="row">
                             <div class="col-sm-6">
                                <p><b>Invoice No:</b></p>
                            </div>
                            <div class="col-sm-6">
                                <p><?php echo $invoice_no; ?></p>
                            </div>

                             <div class="col-sm-6">
                                <p><b>Order Date:</b></p>
                            </div>
                            <div class="col-sm-6">
                                <p><?php echo date('d-M-Y',strtotime($order_date)); ?></p>
                            </div>

                            <div class="col-sm-6">
                                <p><b>Order Source:</b></p>
                            </div>
                            <div class="col-sm-6">
                                <p><?php echo $source; ?></p>
                            </div>

                            <div class="col-sm-6">
                                <p><b>Order Agent:</b></p>
                            </div>
                            <div class="col-sm-6">
                                <p><?php echo $agent; ?></p>
                            </div>

                            <div class="col-sm-6">
                                <p><b>Company Name:</b></p>
                            </div>
                            <div class="col-sm-6">
                                <p><?php echo $company_name; ?></p>
                            </div>
                            <div class="col-sm-6">
                                <p><b>Customer Name:</b></p>
                            </div>
                            <div class="col-sm-6">
                                <p><?php echo ucwords($customer_name); ?></p>
                            </div>
                           
                        </div>
                        <hr>
                        <h5>Commercial</h5>
                        <table>
                            <tr>
                                <th>S.no</th>
                                <th>Product</th>
                                <th>Qty</th>
                              
                                <th>Agreed Price</th>
                            </tr>

                            <?php
                            $i = 1;
                            if ($productdetail != '') {
                                foreach ($productdetail as $row2) { ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $row2->instruments_name; ?></td>
                                        <td><?php echo $row2->qty; ?> <?php echo $row2->unit;?></td>
                                     <!--    <td><?php //echo $row2->list_price.' / '.$row2->unit; ?></td> -->
                                        <!-- <td><input type="hidden" name="quote_detail_id[]" value="<?php echo $row2->id; ?>"><input type="text" name="agreed_price[]" value="<?php echo $row2->agreed_price; ?>" class="form-control"></td> -->
                                        <td><span style="display:none ;"><?php echo $row2->id; ?></span><?php echo $row2->agreed_price; ?></td>
                                    </tr>
                                <?php
                                }
                                ?>

                            <?php } else { ?>

                                <tr>
                                    <td colspan="7">No Product Available</td>


                                </tr>

                            <?php } ?>
                        </table>
                        <hr>
                        <h5>Payment Terms</h5>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><b>Payment Type</b></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><?php echo $payment_type; ?></p>
                                    </div>
                                </div>
                            </div>

                            <?php if($payment == 4) {?>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><b>Credit Days</b></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><?php echo $credit_days; ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <?php } ?>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><b>PO No.</b></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><?php echo $po_no;?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><b>PO Date</b></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <?php
                                        if($po_date<>'' && $po_date<>'01-01-1970')
                                        {
                                            $pdate=date('d-M-Y',strtotime($po_date));
                                        }else{
                                            $pdate='';
                                        } ?>
                                        <p><?php echo $pdate;?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><b>Uploaded PO</b></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><?php if($upload_po <> '') { ?>
                                                      <a href="<?php echo sfdocument;?>order_punch_po/<?php echo $upload_po;?>" download>Download PO</a>
                                              <?php }?></p>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <hr>
                        <h5>Shipping Address</h5>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><b>Ship To</b></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><?php echo $ship_to; ?></p>
                                    </div>
                                </div>
                            </div>
                          
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><b>Address</b></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><?php echo $shipping_address; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><b>State</b></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><?php echo $shipping_state; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><b>City</b></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><?php echo $shipping_city; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><b>Pincode</b></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><?php echo $shipping_pincode; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><b>Phone No</b></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><?php echo $shipping_phone_no; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><b>Mobile No</b></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><?php echo $shipping_mobile_no; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><b>Email</b></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><?php echo $shipping_email; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h5>Billing Address</h5>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><b>Bill To</b></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><?php echo $billing_name; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><b>Address</b></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><?php echo $billing_address; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><b>State</b></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><?php echo $billing_state; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><b>City</b></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><?php echo $billing_city; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><b>Pincode</b></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><?php echo $billing_pincode; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><b>Phone No</b></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><?php echo $billing_phone_no; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><b>Mobile No</b></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><?php echo $billing_mobile_no; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><b>Email</b></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><?php echo $billing_email; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h5>Tax Registration Details</h5>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><b>PAN/IT No.</b></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><?php echo $pan_no; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><b>Registration Type</b></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><?php echo $registration_type; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><b>GSTIN/UIN</b></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><?php echo $gst_no; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><b>MSME No</b></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><?php echo $msme_no; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h5>Other Details</h5>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><b>Reference</b></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><?php echo $reference; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><b>Note</b></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><?php echo $note; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2"><span class="btn btn-primary" style="text-align:center" onclick="printDiv();">PRINT</span></div>
            </div>


            <!-------=========================================VIEW DETAIL====================================------------>

           
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
                $('#dynamictasks').append('<div id="row' + i + '" class="row">   <div class="col-md-12"><div class="col-md-3"><div class="form-group"><label for="field-3" class="control-label">Product</label><span id="error_rack_location" style="color:red;">*</span><select class="form-control" name="product[]" id="product0" onchange="getprice(' + i + ',this.value);getdiscount(' + i + ',this.value);"> <option value="">Select</option> <?php $sql1 = $this->db->select('id, instruments_name')->from('presto_instruments')->where('company_id', $customer_detail->hpcl_company)->get(); if ($sql1->num_rows() > 0) { foreach ($sql1->result() as $row4) { ?> <option value="<?php echo $row4->id; ?>"><?php echo $row4->instruments_name; ?></option> <?php } } ?> </select></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Pack Size</label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control mand" name="qty[]" id="qty' + i + '" required></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">List Price Per <span class="list_price_unit' + i + '"></span></label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control mand" name="listprice[]" id="listprice' + i + '" oninput="allow_decimal("listprice' + i + '");"  onblur="getnetamt(' + i + ')" required></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Discount Price Per <span class="list_price_unit' + i + '"></span></label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control mand" name="discountprice[]" id="discountprice' + i + '" required onblur="getnetamt(' + i + ')" oninput="allow_decimal("discountprice' + i + '");"><p id="discountpriceshow' + i + '" style="display: none;"></p><input type="hidden" name="discountpricehide[]" id="discountpricehide' + i + '" ></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Net Price</label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control" name="netprice[]" id="netprice' + i + '" required readonly> </div></div><div class="col-md-1"><div class="form-group pull-left"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="' + i + '">REMOVE</button></div></div></div><br/>');
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


            if (parseFloat(listprice) > 0) {
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

            if (parseFloat(listprice) > 0) {
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

        function add_product_data() {
            if ($('#add_product').is(":checked")) {
                $("#shownewproduct").css('display', '');

                $("#product0").addClass('mand');
                $("#qty0").addClass('mand');
                $("#listprice0").addClass('mand');
                $("#discountprice0").addClass('mand');
                $("#netprice0").addClass('mand');
                $('.remarkbox').css('display', '');


            } else {
                $("#shownewproduct").css('display', '');
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
                        if (arr[0] > 0) {
                            $("#listprice" + i).val(arr[0]);
                            $("#netprice" + i).val(arr[0]);
                        } else {
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
                        $("#discountpricehide" + i).val(data);
                        $("#discountpriceshow" + i).text('Maximum Discount Allowed: ' + data);
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


        function check_pincode() {
            var pincode = $('#pincode').val();
            var zipRegex = /^\d{6}$/;

            if (!zipRegex.test(pincode)) {
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

            if (!reggst.test(gstinVal) && gstinVal != '') {
                alert('GST Identification Number is not valid. It should be in this "11AAAAA1111Z1A1" format');
                $('#gst').val('');
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
    </script>

    <script>
        function openNav() {
            document.getElementById("mySidenav").style.width = "450px";
        }

        function closeNav() {
            document.getElementById("mySidenav").style.width = "0";
        }

        function printDiv() {
            window.print();
        }

</script>
    </script>

</body>

</html>