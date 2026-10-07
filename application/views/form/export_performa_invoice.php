<?php
$user_id = $this->session->userdata['logged_in']['user_id'];
$CI=&get_instance();
$CI->load->model('Salescrm_model','salescrm');
$po_id=$this->uri->segment(3);
$lead_id=$this->uri->segment(4);
$Resty=$this->db->select('unique_no')->from('leads')->where('id',$lead_id)->get();
if($Resty->num_rows()>0)
{
    foreach($Resty->result() as $Roow);
    $unique_no=$Roow->unique_no;    
}else
{
    echo "Invalid Link!"; exit;
}

$podata=$CI->salescrm->getporeceivedData($po_id,$lead_id);
if(count($podata)>0)
{
    $pono=$podata[0];
    $podate=$podata[1];
    $currency=$podata[2];
    $pi_invoice_no="PFI/".$unique_no;
    $payment_term=$podata[4];

if($currency==1){
    $curr = "USD";
    $sign="$";
}else if($currency==2){
    $curr = "INR";
    $sign="₹";
}else{
    $curr = "EURO";
    $sign="€";
}



    $this->load->helper('export_pi');
    $pi_payment = export_pi_payment_terms($CI, $payment_term, $CI->salescrm->getRecordID($lead_id));
    $payment_terms_written = $pi_payment['text'];




}else
{
    echo "Invalid Access"; exit;
}


$record_id=$CI->salescrm->getRecordID($lead_id);
//echo $record_id; exit;

$basicData=$CI->salescrm->getquoteBasicData($record_id);
if(count($basicData)>0)
{
        $countryname=$basicData[9];
}else
{
    $countryname='';
}

$othercharge=$CI->salescrm->quotation_freight_packing_forwarding($record_id);
//echo "<pre>"; print_r($othercharge); exit;
if(count($othercharge)>0)
{
    $ftypedata=$othercharge['freight_type'];

    if($othercharge['freight']==1)
    {
        $ftype="Extra at Actuals";
    }else if($othercharge['freight']==2)
    {
        $ftype="In Customer Scope";
    }else
    {
        if($othercharge['freight']==3)
        {
            $ftype="Additional";
        }else
        {
             $ftype="Inland Transportation";
        }


    }

        if($ftypedata=="FOB" || $ftypedata=='CFR' || $ftypedata=='CIF')
        {
        $port=$othercharge['port_name'];
        }else
        {
        $port='';
        }
}else
{
    $ftype='';
    $port='';
}



$basicData=$CI->salescrm->getquoteBasicData($record_id);
if(count($basicData)>0)
{
    $ref_no=$basicData[0];
    $quotation_date=$basicData[1];
    $customer_id=$basicData[2];
    $currency=$basicData[3];
    $country=$basicData[4];
    $machine_name=$basicData[5];
    $machine_model_no=$basicData[6];
    $lead_id=$basicData[7];
    $product_id=$basicData[8];
    $countryname=$basicData[9];
      $customer_detail=$CI->salescrm->getCustomerdetail($customer_id);
   // echo "<pre>"; print_r($customer_detail); exit;
    if(count($customer_detail)>0)
    {
        $customerName=$customer_detail[1];
        $customercontactperson = $customer_detail[0];
        $customeraddress = $customer_detail[3];
    }else{
        $customerName='';
        $customercontactperson = '';
        $customeraddress='';
    }
}else
{
    $ref_no='';
    $quotation_date='';
    $customer_id='';
    $currency='';
    $country='';
    $machine_name='';
    $machine_model_no='';
    $customerName='';
    $countryname = '';
    $customeraddress='';
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

    <title>Export Performa Invoice</title>

    <!-- Table Responsive css -->
    <script src="<?php echo assets_url; ?>js/angular.min.js"></script>
    <!-- DataTables -->
    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
    <link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

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
        }

        #pageloader {
            background: rgba(255, 255, 255, 0.8);
            display: none;
            height: 100%;
            position: fixed;
            width: 100%;
            z-index: 9999;
        }

        #pageloader img {
            left: 30%;
            margin-left: -10px;
            margin-top: -10px;
            position: absolute;
            top: 30%;
        }

        .form_box {
            border-style: outset;
            border-color: #f3f8fc #b3b1ac #b3b1ac #f3f8fc;
            border-width: .8px;
            padding: 1px;
            box-shadow: 0.7px 0.7px 0px black, inset -0.5px -0.5px 0px #7A7A7B, inset 0.5px 0.5px 0px #eeedea;
            background-color: #efebeb;
        }

         .form_box button {
            border-style: outset;
            border-color: #f3f8fc #b3b1ac #b3b1ac #f3f8fc;
            border-width: .8px;
            padding: 1px;
            background-color: #c0c0c0;
        }

        .form_box .form-control {
            width: 100%;
            border-bottom: 1px solid #7f7f7f;
            border-top: 1px solid #7A7A7B;
            border-left: 1px solid #7A7A7B;
            border-right: 1px solid #7f7f7f;
            background-color: #fff;
            outline: none;
            padding: 4px 8px !important;
            height: 25px !important;
            /* background-color: #eded8291; */
        }

        .form_box table tr td {
            border: none;
            color: #000;
            padding: 5px;
            font-size: 13px;
            vertical-align: middle;
            /* text-align: right; */
        }

        .form_box table tr th {
            border: none;
        }

        .no-gutters .col-sm-6 {
            padding: 0px !important;
        }

        .form_box table td label{
            text-align: left;
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
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4>PERFORMA INVOICE (EXPORTER)</h4>
                    </div>
                </div>
            </div>
            <!-- end page title end breadcrumb -->

            <form action="<?php echo page_url;?>Form/add_export_pi/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>" method="post" id="frm" onsubmit="return checkTotals();">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form_box">

                        <table class="table ">
                            <tr>
                                <th style="font-size: 16px;">
                                    <b>Pro./Transporter General Information</b>
                                </th>
                            </tr>
                        </table>
                    </div>
                   
                    <div class="form_box">
                        <table class="table">
                            <tr>
                                <td width="20%">
                                    <b>Prof.Inv.No. <span style="color:red">*</span></b>
                                </td>
                                <td width="30%">
                                    <input type="text" class="form-control" name="prof_inv_no" value="<?php echo $pi_invoice_no;?>" required>
                                </td>


                                 <td width="20%">
                                    <b>Buyer Order No <span style="color:red">*</span></b>
                                </td>
                                <td width="30%">
                                    <input type="text" class="form-control" name="buyer_order_no" required>
                                </td>


                               <!--  <td width="20%"  style="vertical-align: top;">
                                    <b>I.E.C Code No. <span style="color:red">*</span></b>
                                </td>
                                <td width="30%"  style="vertical-align: top;">
                                    <input type="text" class="form-control" name="iec_code">
                                </td> -->
                               
                            </tr>
                            <tr>
                                <td width="20%">
                                    <b>Prof.Inv Date <span style="color:red">*</span></b>
                                </td>
                                <td width="30%">
                                    <input type="date" class="form-control"  required 
                                    name="prof_inv_date">
                                </td>

                                 <td width="20%">
                                    <b>Buyer Order Date <span style="color:red">*</span></b>
                                </td>
                                <td width="30%">
                                    <input type="date" class="form-control" name="buyer_order_date" required>
                                </td>
                                 <!-- <td width="20%"  style="vertical-align: top;">
                                    <b>RBI Code <span style="color:red">*</span></b>
                                </td>
                                <td width="30%"  style="vertical-align: top;">
                                    <input type="text" class="form-control" name="rbi_code" required>
                                </td> -->
                            </tr>
                            <tr>
                               
                                <!-- <td width="20%"  style="vertical-align: top;">
                                    <b>EEPC No. <span style="color:red">*</span></b>
                                </td>
                                <td width="30%"  style="vertical-align: top;">
                                    <input type="text" class="form-control" name="eepc_no" required>
                                </td> -->
                            </tr>
                          
                            <tr>
                               
                            </tr>
                            <!-- <tr>
                                <td width="20%">
                                    <b>Cust PO No. <span style="color:red">*</span></b>
                                </td>
                                <td width="30%">
                                    <input type="text" class="form-control" name="cust_po_no" value="<?php echo $pono;?>" required readonly>
                                </td>
                            </tr> -->
                            <!-- <tr>
                                <td width="20%">
                                    <b>Cust PO Date <span style="color:red">*</span></b>
                                </td>
                                <td width="30%">
                                    <input type="text" class="form-control" name="cust_po_date" required value="<?php echo date('d-m-Y',strtotime($podate));?>" readonly>
                                </td>
                            </tr> -->
                        </table>
                    </div>

                    <div class="container-fluid" style="padding:0px">
                        <div class="row no-gutters" style="margin: 0;">
                            <div class="col-sm-6">
                                <div class="form_box">
                                    <table class="table">
                                        <tr>
                                            <th>Details of Customer (Bill To)</th>
                                        </tr>
                                    </table>
                                </div>
                                <div class="form_box" style="height: 180px;">
                                    <table class="table">
                                        <tr>
                                            <td width="40%">
                                                <b>Name<span style="color:red">*</span></b>
                                            </td>
                                            <td width="60%">
                                                <input type="text" class="form-control" name="bill_to_name" id="bill_to_name" value="<?php echo $customerName;?>" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="40%">
                                                <b>Address <span style="color:red">*</span></b>
                                            </td>
                                            <td width="60%">
                                                <input type="text" class="form-control" name="bill_to_address" id="bill_to_address" value="<?php echo $customeraddress;?>" required>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="table">
                                        <tr>
                                            <td width="20%">
                                                <b>State (optional)</b>
                                            </td>
                                            <td width="30%">
                                                <input type="text" class="form-control" name="bill_to_state" id="bill_to_state">
                                            </td>
                                            <td width="20%">
                                                <b>State Code (optional)</b>
                                            </td>
                                            <td width="30%">
                                                <input type="text" class="form-control" name="bill_to_state_code" id="bill_to_state_code">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="20%">
                                                <b>IEC CODE (optional)</b>
                                            </td>
                                            <td width="30%">
                                                <input type="text" class="form-control" name="bill_to_iec_no" id="bill_to_iec_no">
                                            </td>
                                            <td width="20%">
                                                <b>GST No.<span style="color:red"></span></b>
                                            </td>
                                            <td width="30%">
                                                <input type="text" class="form-control" name="bill_to_gst_no" id="bill_to_gst_no">
                                            </td>
                                        </tr>
                                    </table>

                                    <p style="text-align:center;"><input type="checkbox" name="same" id="same" value="1" onchange="checkBillShipSame();">&nbsp;<strong style="color:black;" >Shipping same as Billing?</strong></p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form_box">
                                    <table class="table">
                                        <tr>
                                            <th>Details of Customer (Ship To)</th>
                                        </tr>
                                    </table>
                                      </div>
                                <div class="form_box" style="height: 180px;">
                                    <table class="table">
                                        <tr>
                                            <td width="40%">
                                                <b>Name <span style="color:red">*</span></b>
                                            </td>
                                            <td width="60%">
                                                <input type="text" class="form-control" name="ship_to_name" id="ship_to_name" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="40%">
                                                <b>Address <span style="color:red">*</span></b>
                                            </td>
                                            <td width="60%">
                                                <input type="text" class="form-control" name="ship_to_address" id="ship_to_address" required>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="table">
                                        <tr>
                                            <td width="20%">
                                                <b>State (optional)</b>
                                            </td>
                                            <td width="30%">
                                                <input type="text" class="form-control" name="ship_to_state" id="ship_to_state">
                                            </td>
                                            <td width="20%">
                                                <b>State Code (optional)</b>
                                               
                                            </td>
                                            <td width="30%">
                                                <input type="text" class="form-control" name="ship_to_state_code" id="ship_to_state_code">
                                            </td>
                                        </tr>
                                        <!-- <tr>
                                            <td width="20%">
                                                <b>GST No. (optional)</b>
                                            </td>
                                            <td width="30%">
                                                <input type="text" class="form-control" name="ship_to_gst_no" id="ship_to_gst_no">
                                            </td>
                                            <td width="20%">
                                                <b>PAN No.<span style="color:red">*</span></b>
                                            </td>
                                            <td width="30%">
                                                <input type="text" required class="form-control" name="ship_to_pan_no" id="ship_to_pan_no">
                                            </td>
                                        </tr> -->
                                    </table>
                                     <p style="height:20px;"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form_box">
                        <table class="table">
                                <tr>
                                    <!-- <td width="25%">
                                        <label for=""><b>Pre-Carriage By <span style="color: red;">*</span></b></label>
                                        <input type="text" class="form-control" name="pre_carriage" required>
                                    </td>
                                    <td width="25%">
                                        <label for=""><b>Place of Receipt by Pre Carrier <span style="color: red;">*</span></b></label>
                                        <input type="text" class="form-control" name="place_receipt_pre_carriage" required>
                                    </td> -->
                                    <td width="25%">
                                        <label for=""><b>Country of Origin of Goods <span style="color: red;">*</span></b></label>
                                        <input type="text" class="form-control" name="country_origin_goods" required value="India">
                                    </td>
                                   <td width="25%">
                                        <label for=""><b>Country of Final Destination <span style="color: red;">*</span></b></label>
                                        <input type="text" class="form-control" name="country_final_destination" value="<?php echo $countryname;?>" required>
                                    </td> 
                                </tr>
                                <tr>
                                    <!-- <td width="25%">
                                        <label for=""><b>Vessel/Flight No. <span style="color: red;">*</span></b></label>
                                        <input type="text" class="form-control" name="vessel_flight_no" required>
                                    </td> -->
                                    <td width="25%">
                                        <label for=""><b>Port of Loading <span style="color: red;">*</span></b></label>
                                        <input type="text" class="form-control" name="port_loading" required value="<?php echo $port;?>">
                                    </td>

                                     <td width="25%">
                                        <label for=""><b>Port of Discharge <span style="color: red;">*</span></b></label>
                                        <input type="text" class="form-control" name="port_discharge" required>
                                    </td>

                                    <td width="25%">
                                        <label for=""><b>HSN CODE<span style="color: red;">*</span></b></label>
                                        <input type="text" class="form-control" name="hsn_code" required>
                                    </td>
                                   <!--  <td width="50%" colspan="2" rowspan="2" style="vertical-align: top;">
                                        <label for=""><b>Terms of Payment <span style="color: red;">*</span></b></label>
                                        <textarea class="form-control" name="terms_payment" required><?php echo $payment_terms_written;?></textarea>
                                    </td> -->
                                </tr>
                               <!--  <tr>
                                   
                                    <td width="25%">
                                        <label for=""><b>Final Destination <span style="color: red;">*</span></b></label>
                                        <input type="text" class="form-control" name="final_destination" required>
                                    </td>
                                </tr> -->
                        </table>
                    </div>
                    <div class="form_box">
                        <table class="table">
                            <tr>
                                <th>
                                    Items Information
                                </th>
                            </tr>
                        </table>
                    </div>
                    <div class="form_box">

                       <?php 
                       $basic_cost=array();
                       $basic_cost[]=0;
                        $dprice=$CI->salescrm->getPriceDataByCategory($record_id,0);
                        if(count($dprice)>0)
                        {
                        $i=1;
                        foreach($dprice as $dprice1)
                        {
                            $desc=$dprice1['description'];
                            $qty=$dprice1['qty'];
                            $price=$dprice1['price'];
                            $total_price=$qty*$price;
                            $basic_cost[]=$total_price;
                        ?>
                        <table class="table CAT<?php echo $i;?>">
                 
                            <tr>
                               <!--  <td width="20%">
                                    <label><b>Item Code</b><span style="color:red">*</span></label>
                                     <input type="text" name="item_code[]" required class="form-control" id="item_code<?php echo $i;?>"> 
                                </td> -->
                                
                                <td width="40%">
                                    <label for=""><b>Desc. of Goods/Services</b><span style="color:red">*</span></label>
                                     <textarea name="desc_goods[]" required  id="desc_goods<?php echo $i;?>" class="form-control" id="">Price of design, manufacturing, supply of
machine as per technical specifications at
Annexure-II  <?php echo $desc;?></textarea>
                                </td>
                               
                              <!--   <td width="20%">
                                    <label for=""><b>HSN/SAC Code</b><span style="color:red">*</span></label>
                                       <input type="text" name="hsn_code[]" id="hsn_code<?php echo $i;?>" required class="form-control">
                                </td> -->
                              
                                <td width="10%">
                                    <label for=""><b>Quantity</b><span style="color:red">*</span></label>
                                     <input type="number" name="qty[]" id="qty<?php echo $i;?>" class="form-control" required value="<?php echo floatval($qty);?>" onblur="checkDiscountValue(<?php echo $i;?>);">
                                </td>
                                <td width="20%">
                                    <b>UOM<span style="color:red">*</span></b>
                                     <input type="text" name="uom[]" id="uom<?php echo $i;?>" required class="form-control" value="Nos" readonly>
                                </td>

                                 <td width="10%">
                                    <b>Rate in <?php echo $sign;?><span style="color:red">*</span></b>
                                     <input type="number" name="rate_inr[]" id="rate_inr<?php echo $i;?>" class="form-control" value="<?php echo floatval($price);?>" onblur="checkDiscountValue(<?php echo $i;?>);" required>
                                </td>

                                <td width="20%">
                                    <b>Total Amount<span style="color:red">*</span></b>
                                       <input type="number" name="total_amount[]" required id="total_amount<?php echo $i;?>" class="form-control finalAMT" value="<?php echo $total_price;?>" readonly>
                                </td>
                              
                                <td width="20%" style="text-align: center;">
                                    <button type="button" class="btn btn-dark" name="close" id="close" style="padding: 0px !important; height: 21px !important; font-size: 13px !important; width: 21px; margin-top: 20px;" onclick="removeROW(<?php echo $i;?>);"><b>X</b></button>
                                </td>

                            </tr>
                        </table>
                       <hr class="CAT<?php echo $i;?>" style="border-top:1px solid #7f7f7f;">
                        <?php $i++;
                        } } ?>



                         <?php 
                        $dprice=$CI->salescrm->getPriceDataByCategory($record_id,1);
                        if(count($dprice)>0)
                        {
                        $i=$i;
                        foreach($dprice as $dprice1)
                        {
                            $desc=$dprice1['description'];
                            $qty=$dprice1['qty'];
                            $price=$dprice1['price'];
                            $total_price=$qty*$price;
                            $instruments=$CI->salescrm->getProductName($desc);
                            $basic_cost[]=$total_price;
?>
                        <table class="table CAT<?php echo $i;?>">
                 
                            <tr>
                                <!-- <td width="20%">
                                    <label><b>Item Code</b><span style="color:red">*</span></label>
                                     <input type="text" name="item_code[]" class="form-control" required id="item_code<?php echo $i;?>"> 
                                </td> -->
                                
                                <td width="40%">
                                    <label for=""><b>Desc. of Goods/Services</b><span style="color:red">*</span></label>
                                     <textarea name="desc_goods[]" id="desc_goods<?php echo $i;?>" required class="form-control" id=""><?php echo $instruments;?></textarea>
                                </td>
                               
                                <!-- <td width="20%">
                                    <label for=""><b>HSN/SAC Code</b><span style="color:red">*</span></label>
                                       <input type="text" name="hsn_code[]" id="hsn_code<?php echo $i;?>" required  class="form-control">
                                </td> -->
                              
                                <td width="10%">
                                    <label for=""><b>Quantity</b><span style="color:red">*</span></label>
                                     <input type="number" name="qty[]" id="qty<?php echo $i;?>" required class="form-control" value="<?php echo floatval($qty);?>" onblur="checkDiscountValue(<?php echo $i;?>);">
                                </td>
                                <td width="20%">
                                    <b>UOM<span style="color:red">*</span></b>
                                     <input type="text" name="uom[]" id="uom<?php echo $i;?>" required class="form-control" value="Nos" readonly>
                                </td>

                                <td width="10%">
                                    <b>Rate in <?php echo $sign;?><span style="color:red">*</span></b>
                                     <input type="number" name="rate_inr[]" id="rate_inr<?php echo $i;?>" class="form-control" value="<?php echo floatval($price);?>" required onblur="checkDiscountValue(<?php echo $i;?>);">
                                </td>

                                  <td width="20%">
                                    <b>Total Amount<span style="color:red">*</span></b>
                                       <input type="number" name="total_amount[]" id="total_amount<?php echo $i;?>" class="form-control finalAMT" value="<?php echo $total_price;?>" required readonly>
                                </td>
                              
                                <td width="20%" style="text-align: center;">
                                    <button type="button" class="btn btn-dark" name="close" id="close" onclick="removeROW(<?php echo $i;?>);" style="padding: 0px !important; height: 21px !important; font-size: 13px !important; width: 21px; margin-top: 20px;"><b>X</b></button>
                                </td>

                            </tr>
                        </table>
                        
                        <!-- <hr style="border-top:1px solid #7f7f7f;"> -->
                        <?php $i++;
                        } } ?>


                    <?php 
                    $conprice=array();
                    $conprice[]=0;
                    $optional=$CI->salescrm->getConsumableData($record_id);
                    //echo "<pre>"; print_r($optional); exit;
                    if(count($optional)>0)
                    {
                    $i=$i;
                    foreach($optional as $option)
                    {
                    $price=$option['price'];
                    $qty=$option['value'];
                    $conprice[]=$price*$qty;
                    } }

                    if(count($optional)>0)
                    {
                        $i=$i+1;
                        $price_data[]=array_sum($conprice);
                        $total_price=$qty*$price;
                        $basic_cost[]=$total_price;
                    ?>


                     <table class="table CAT<?php echo $i;?>">
                 
                            <tr>
                               <!--  <td width="20%">
                                    <label><b>Item Code</b><span style="color:red">*</span></label>
                                     <input type="text" name="item_code[]" required class="form-control" id="item_code<?php echo $i;?>"> 
                                </td> -->
                                
                                <td width="40%">
                                    <label for=""><b>Desc. of Goods/Services</b><span style="color:red">*</span></label>
                                     <textarea name="desc_goods[]" required id="desc_goods<?php echo $i;?>" class="form-control" id="">1 Year Consumable Spares</textarea>
                                </td>
                               
                              <!--   <td width="20%">
                                    <label for=""><b>HSN/SAC Code</b><span style="color:red">*</span></label>
                                       <input type="text" name="hsn_code[]" required id="hsn_code<?php echo $i;?>" class="form-control">
                                </td>
                               -->
                                <td width="10%">
                                    <label for=""><b>Quantity</b><span style="color:red">*</span></label>
                                     <input type="number" name="qty[]" id="qty<?php echo $i;?>" required class="form-control" value="<?php echo floatval($qty);?>" onblur="checkDiscountValue(<?php echo $i;?>);">
                                </td>
                                <td width="20%">
                                    <b>UOM<span style="color:red">*</span></b>
                                     <input type="text" name="uom[]" id="uom<?php echo $i;?>" required class="form-control" value="SET" readonly>
                                </td>

                                <td width="10%">
                                    <b>Rate in <?php echo $sign;?><span style="color:red">*</span></b>
                                     <input type="number" name="rate_inr[]" id="rate_inr<?php echo $i;?>" class="form-control" value="<?php echo floatval($price);?>" onblur="checkDiscountValue(<?php echo $i;?>);" required >
                                </td>

                                  <td width="20%">
                                    <b>Total Amount<span style="color:red">*</span></b>
                                       <input type="number" name="total_amount[]" id="total_amount<?php echo $i;?>" class="form-control finalAMT" value="<?php echo $total_price;?>" readonly required>
                                </td>
                              
                                <td width="20%" style="text-align: center;">
                                    <button type="button" class="btn btn-dark" name="close" id="close" onclick="removeROW(<?php echo $i;?>);" style="padding: 0px !important; height: 21px !important; font-size: 13px !important; width: 21px; margin-top: 20px;"><b>X</b></button>
                                </td>


                            </tr>
                        </table>
                      
                     
                    <?php 
                    } 
                    ?>

                    <?php 
$this->load->helper('export_pi');
$pi_commercial = export_pi_commercial($CI, 0, $record_id);
$dscvalafterdiscount = export_pi_discount(array_sum($basic_cost), $pi_commercial);
$basic_cost[] = -$dscvalafterdiscount;
                    ?>

                    <div style="display: flex; align-items: center; width: 100%;">
                    <div style="flex: 1; height: 1px; background-color: #000;"></div>
                    <div style="padding: 0 10px;font-weight: bold;color:red;font-size:20px;">Discount - <span id="tbc_dis"><?php echo $dscvalafterdiscount;?></span><input type="hidden" name="basicCost_discount" class="basicCost_discount" value="<?php echo $dscvalafterdiscount;?>"></div>
                    <div style="flex: 1; height: 1px; background-color: #000;"></div>
                    </div>

                    <div style="clear:both;height: 40px;"></div>


                       <div style="display: flex; align-items: center; width: 100%;">
                    <div style="flex: 1; height: 1px; background-color: #000;"></div>
                    <div style="padding: 0 10px;font-weight: bold;color:red;font-size:20px;">Total Basic Cost - <span id="tbc"><?php echo array_sum($basic_cost);?></span><input type="hidden" name="basicCost" class="basicCost" value="<?php echo array_sum($basic_cost);?>"><input type="hidden" name="OriginalbasicCost" class="OriginalbasicCost" value="<?php echo array_sum($basic_cost);?>"></div>
                    <div style="flex: 1; height: 1px; background-color: #000;"></div>
                    </div>

                    <div style="clear:both;height: 40px;"></div>


<?php
$this->load->helper('export_pi');
$pi_commercial = export_pi_commercial($CI, 0, $CI->salescrm->getRecordID($lead_id));
$this->load->view('form/_export_pi_charges', array('pi_commercial' => $pi_commercial));
?>


                 <!--  <div class="col-md-12">
                        <input type="checkbox" name="check" id="check" value="1" onchange="showMoreData();">&nbsp;<strong style="color:black;font-weight: bold;">Add More Line Items</strong>
                    </div> -->

                        <?php 
                        $i=$i+1;
                        ?>
                        <table class="table NewaddTable" style="display: none;">
                                             <tr>
                               <!--  <td width="20%">
                                    <label><b>Item Code</b><span style="color:red">*</span></label>
                                     <input type="text" name="item_code[]"  id="item_code<?php echo $i;?>" class="form-control" id="item_code"> 
                                </td> -->
                               
                                <td width="20%">
                                    <label for=""><b>Desc. of Goods/Services</b><span style="color:red">*</span></label>
                                     <textarea name="desc_goods[]"  id="desc_goods<?php echo $i;?>" class="form-control" id=""></textarea>
                                </td>
                               
                             <!--    <td width="20%">
                                    <label for=""><b>HSN/SAC Code</b><span style="color:red">*</span></label>
                                       <input type="text" name="hsn_code[]"  id="hsn_code<?php echo $i;?>" class="form-control">
                                </td> -->
                              
                                <td width="20%">
                                    <label for=""><b>Quantity</b><span style="color:red">*</span></label>
                                     <input type="text" name="qty[]" id=
                                     "qty<?php echo $i;?>" class="form-control"  onblur="checkDiscountValue(<?php echo $i;?>);">
                                </td>
                                <td width="20%">
                                    <b>UOM<span style="color:red">*</span></b>
                                     <input type="text" name="uom[]"  id="uom<?php echo $i;?>" class="form-control" value="Nos" readonly>
                                </td>
                            </tr>
                        </table>
                        <table class="table NewaddTable" style="display:none;">
                            <tr>
                                <td width="20%">
                                    <b>Rate in <?php echo $sign;?><span style="color:red">*</span></b>
                                     <input type="text" name="rate_inr[]" id="rate_inr<?php echo $i;?>" class="form-control" onblur="checkDiscountValue(<?php echo $i;?>);" >
                                </td>
                               
                               <!--  <td width="20%">
                                    <b>Disc%<span style="color:red">*</span></b>
                                      <input type="text" name="discount[]" id="discount<?php echo $i;?>" class="form-control" onblur="checkDiscountValue(<?php echo $i;?>);" >
                                </td>
                               
                                <td width="20%">
                                    <b>After Disc Value<span style="color:red">*</span></b>
                                     <input type="text" name="discount_value[]"  readonly id="discount_value<?php echo $i;?>" class="form-control">
                                </td> -->
                               
                                <td width="20%">
                                    <b>Total Amount<span style="color:red">*</span></b>
                                       <input type="text" name="total_amount[]" id=
                                       "total_amount<?php echo $i;?>" class="form-control"  readonly>
                                </td>
                              
                                <td width="20%" style="text-align: center;">
                                    <button type="button" class="btn btn-dark" name="add" id="addmore_btn3" style="padding: 0px !important; height: 21px !important; font-size: 13px !important; width: 21px; margin-top: 20px;"><b>+</b></button>
                                </td>
                            </tr>
                        </table>
                        <div id="item_add" class="container-fluid"></div>


                        <table class="table" style="margin-top:60px;">
<tr>
    <td>
        <label for=""><b>Remarks</b></label>
        <textarea name="remarks" class="form-control" id=""></textarea>
    </td>
</tr>
                        </table>

                        <table class="table" style="margin-top:60px;">
<tr>
    <td style="width:33%"></td>
    <td style="width:33%"><input type="submit" class="btn btn-md btn-success" value="Submit" style="width:100%;background-color: green !important;color:white !important;font-weight: bold;"></td>
     <td style="width:33%"></td>
</tr>
                        </table>

                    </div>




                </div>
            </div>

        </form>



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
    <script src="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

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
    <script src="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
    <!-- Datatable init js -->
    <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>

    <script src="<?php echo assets_url; ?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>

    <script>

        //<td width="20%"><b>Disc%<span style="color:red">*</span></b><input type="text" required name="discount[]" id="discount'+i+'" class="form-control" onblur="checkDiscountValue('+i+');"></td><td width="20%"><b>After Disc Value<span style="color:red">*</span></b><input type="text"  required name="discount_value[]" id="discount_value'+i+'" readonly class="form-control"></td>

        var i = "<?php echo $i+1;?>";


        $('#addmore_btn3').click(function() {

            $('#item_add').append('<div id="row' + i + '" class="row" style="margin-top:10px; border-top: 1px solid #7f7f7f;"><table class="table NewaddTable"><tr><td width="20%"><label for=""><b>Desc. of Goods/Services<span style="color:red">*</span></b></label><textarea name="desc_goods[]" class="form-control" id="desc_goods'+i+'" required></textarea></td><td width="20%"><label for=""><b>Quantity<span style="color:red">*</span></b></label><input type="text" required name="qty[]" id="qty'+i+'" class="form-control" onblur="checkDiscountValue('+i+');"></td><td width="20%"><b>UOM<span style="color:red">*</span></b><input required type="text" name="uom[]" id="uom'+i+'" value="Nos" class="form-control"></td></tr></table><table class="table NewaddTable"><tr><td width="20%"><b>Rate INR<span style="color:red">*</span></b><input type="text" required name="rate_inr[]" id="rate_inr'+i+'" onblur="checkDiscountValue('+i+');" class="form-control"></td><td width="20%"><b>Total Amount<span style="color:red">*</span></b><input type="text" name="total_amount[]" readonly id="total_amount'+i+'" required class="form-control"></td><td width="20%" style="text-align:center"><button type="button" name="add" class="btn_remove  btn btn-danger" id="' + i + '" style="padding: 0px !important; height: 21px !important; font-size: 13px !important; width: 21px; margin-top:20px;"><i class="fa fa-close"></i></button></td></tr></table></div>');

            // // showhidedfbox();


            i++;
        });


        $(document).on('click', '.btn_remove', function() {
            var button_id = $(this).attr("id");
            $('#row' + button_id + '').remove();
        });

    function checkBillShipSame()
    {
        if($('#same').is(":checked"))
        {
            var bill_to_name=$("#bill_to_name").val();
            var bill_to_address=$("#bill_to_address").val();
            var bill_to_state=$("#bill_to_state").val();
            var bill_to_state_code=$("#bill_to_state_code").val();
            var bill_to_gst_no=$("#bill_to_gst_no").val();
            var bill_to_pan_no=$("#bill_to_pan_no").val();
            /** NOW COPY **/
                $("#ship_to_name").val(bill_to_name);
                $("#ship_to_address").val(bill_to_address);
                $("#ship_to_state").val(bill_to_state);
                $("#ship_to_state_code").val(bill_to_state_code);
                $("#ship_to_gst_no").val(bill_to_gst_no);
                $("#ship_to_pan_no").val(bill_to_pan_no);
        }else
        {
                $("#ship_to_name").val('');
                $("#ship_to_address").val('');
                $("#ship_to_state").val('');
                $("#ship_to_state_code").val('');
                $("#ship_to_gst_no").val('');
                $("#ship_to_pan_no").val('');
        }

    }

    function checkDiscountValue(flag)
    {
       var qty=$("#qty"+flag).val();
       var rate_inr=$("#rate_inr"+flag).val();
       var discount=0;

    /** MINUS DISCOUNT PERCENTAGE **/
       // if(discount!='' && discount>0)
       // {
    
       //      var rate_inr=rate_inr-(rate_inr*(discount/100));
       //      var rate_inr=rate_inr.toFixed(2);
       //      $("#discount_value"+flag).val(rate_inr);
       //       var a=qty*rate_inr;
       //      var a=a.toFixed(2);
       //      $("#total_amount"+flag).val(a);
       // }else
       // {

        //$("#discount_value"+flag).val(rate_inr);
            var a=qty*rate_inr;
            var a=a.toFixed(2);
            $("#total_amount"+flag).val(a);

            getbasicCostSum();
      // }

   }


function getbasicCostSum()
{
    
    var total=0;

    $('.finalAMT').each(function() {
      //  alert($(this).val());
    total += parseFloat($(this).val());
    });

//    alert(total);
    // DISCOUNT 

    var terms = <?php echo json_encode($pi_commercial); ?>;
    var discount = Math.min(total, Math.max(0, Number(terms.discount_type) === 1 ? total * Number(terms.discountvalue) / 100 : Number(terms.discountvalue)));
    $('.basicCost_discount').val(discount);
    $('#tbc_dis').text(discount.toFixed(2));

    var total=total-discount;
    $(".basicCost").val(total);
    $("#tbc").text(total);


}

   function showMoreData()
   {
        $(".NewaddTable").css('display','none');
        $(".NewaddTable :input").attr('required',false);
        if($('#check').is(":checked"))
        {
        $(".NewaddTable").css('display','');
        $(".NewaddTable :input").attr('required',true);
        }
   }

   function checkTotals()
   {
    getbasicCostSum();

    var Obvalue=parseFloat($(".OriginalbasicCost").val());
    var bvalue=parseFloat($(".basicCost").val());

 
    if(bvalue>0)
    {
    if(Obvalue==bvalue)
    {
        return true;
    }else
    {
        alert('Basic Cost Cannot be less or more than the Quotation');
        return false;
    }
    }else
    {
        alert('No Particulars Found. Cannot be Submitted.');
        return false;
    }

   }

   function removeROW(i)
   {

        $(".CAT"+i).remove();
        getbasicCostSum();

   }
    </script>

</body>

</html>