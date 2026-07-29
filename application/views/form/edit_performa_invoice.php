<?php
$user_id = $this->session->userdata['logged_in']['user_id'];
$CI=&get_instance();
$CI->load->model('Salescrm_model','salescrm');
$po_id=$this->uri->segment(3);
$lead_id=$this->uri->segment(4);
$podata=$CI->salescrm->getporeceivedData($po_id,$lead_id);
if(count($podata)>0)
{
    $pono=$podata[0];
    $podate=$podata[1];
    $currency=$podata[2];
    $pi_invoice_no=$podata[3];

}else
{
    echo "Invalid Access"; exit;
}


$record_id=$CI->salescrm->getRecordID($lead_id);
//echo $record_id; exit;
$othercharge=$CI->salescrm->quotation_freight_packing_forwarding($record_id);
//echo "<pre>"; print_r($othercharge); exit;
if(count($othercharge)>0)
{
    if($othercharge['freight']==1)
    {
        $ftype="Extra at Actuals";
    }else if($othercharge['freight']==2)
    {
        $ftype="In Customer Scope";
    }else
    {
        $ftype="Additional";
    }

}else
{
    $ftype='';
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


$rest=$this->db->select('*')->from('performa_invoice')->where('lead_id',$lead_id)->where('po_id',$po_id)->get();
if($rest->num_rows()>0)
{
    foreach($rest->result() as $row);
    $invoice_no=$row->invoice_no;
    $invoice_date=$row->invoice_date;
    $state=$row->state;
    $place_of_supply=$row->place_of_supply;
    $customer_po=$row->customer_po;
    $customer_po_date=$row->customer_po_date;
    $freight=$row->freight;
    $hsn=$row->hsn;
    $remarks=$row->remarks;
    $record_id=$row->id;


    $rest=$this->db->select('*')->from('performa_invoice_address')->where('record_id',$record_id)->get();
    if($rest->num_rows()>0)
    {
    foreach($rest->result() as $row);
    $bill_to_name=$row->bill_to_name;
    $bill_address=$row->bill_address;
    $bill_state=$row->bill_state;
    $bill_state_code=$row->bil_state_code;
    $bill_gst=$row->bill_gst;
    $bill_pan=$row->bill_pan;
    $ship_to_name=$row->ship_to_name;
    $ship_address=$row->ship_address;
    $ship_state=$row->ship_state;
    $ship_state_code=$row->ship_state_code;
    $ship_gst=$row->ship_gst;
    $ship_pan=$row->ship_pan;

    }else
    {
    echo "Invalid Access"; exit;
    }





}else
{
    echo "Invalid Access"; exit;
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

    <title>Domestic Performa Invoice</title>

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
                        <h4>EDIT PERFORMA INVOICE (DOMESTIC)</h4>
                    </div>
                </div>
            </div>
            <!-- end page title end breadcrumb -->

            <form action="<?php echo page_url;?>Form/update_domestic_pi/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $record_id;?>" method="post" id="frm">
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
                    <!-- <div class="form_box">
                        <table class="table ">
                            <tr>
                                <td width="20%">
                                    <b>Company Name</b>
                                </td>
                                <td width="30%">
                                    <input type="text" name="company_name" class="form-control">
                                </td>
                                <td width="20%">
                                    <b>Phone No.</b>
                                </td>
                                <td width="30%">
                                    <input type="tel" name="company_phone" class="form-control">
                                </td>
                            </tr>
                        </table>
                        <table class="table">
                            <tr>
                                <td width="20%">
                                    <b>Address</b>
                                </td>
                                <td width="80%">
                                    <input type="text" name="company_address" class="form-control">
                                </td>
                            </tr>
                        </table>
                        <table class="table ">
                            <tr>
                                <td width="20%">
                                    <b>Email Id</b>
                                </td>
                                <td width="30%">
                                    <input type="email" name="company_email" class="form-control">
                                </td>
                                <td width="20%">
                                    <b>PAN</b>
                                </td>
                                <td width="30%">
                                    <input type="text" name="company_pan" class="form-control">
                                </td>
                            </tr>
                            <tr>

                                <td width="20%">
                                    <b>GSTIN</b>
                                </td>
                                <td width="30%">
                                    <input type="text" name="company_gst" class="form-control">
                                </td>
                                <td width="20%">
                                    <b>CIN</b>
                                </td>
                                <td width="30%">
                                    <input type="text" name="company_cin" class="form-control">
                                </td>
                            </tr>
                        </table>
                        <table class="table">
                            <tr>

                                <td width="20%">
                                    <b>State</b>
                                </td>
                                <td width="30%">
                                    <input type="text" name="company_state" class="form-control">
                                </td>
                                <td width="20%">
                                    <b>State Code</b>
                                </td>
                                <td width="30%">
                                    <input type="text" name="company_state_code" class="form-control">
                                </td>
                            </tr>
                        </table>

                    </div> -->
                    <div class="form_box">
                        <table class="table">
                            <tr>
                                <td width="20%">
                                    <b>Prof.Inv.No. <span style="color:red">*</span></b>
                                </td>
                                <td width="30%">
                                    <input type="text" class="form-control" name="prof_inv_no" value="<?php echo $invoice_no;?>" required>
                                </td>

                                <td width="20%" rowspan="4" style="vertical-align: top;">
                                    <b>Freight Terms <span style="color:red">*</span></b>
                                </td>
                                <td width="30%" rowspan="4" style="vertical-align: top;">
                                    <textarea class="form-control" name="freight_terms" required readonly><?php echo $freight;?></textarea>
                                </td>

                                 
                               
                            </tr>
                            <tr>
                                <td width="20%">
                                    <b>Prof.Inv Date <span style="color:red">*</span></b>
                                </td>
                                <td width="30%">
                                    <input type="date" class="form-control"  required 
                                    name="prof_inv_date" value="<?php echo $invoice_date;?>">
                                </td>
                               
                               
                            </tr>
                            <tr>
                                <td width="20%">
                                    <b>State <span style="color:red">*</span></b>
                                </td>
                                <td width="30%">
                                    <input type="text" class="form-control" name="state" required value="<?php echo $state;?>">
                                </td>
                               
                            </tr>
                          
                            <tr>
                                <td width="20%">
                                    <b>Place of Supply <span style="color:red">*</span></b>
                                </td>
                                <td width="30%">
                                    <input type="text" class="form-control" name="place_supply" required value="<?php echo $place_of_supply;?>">
                                </td>
                            </tr>
                            <tr>
                                <td width="20%">
                                    <b>Cust PO No. <span style="color:red">*</span></b>
                                </td>
                                <td width="30%">
                                    <input type="text" class="form-control" name="cust_po_no" value="<?php echo $customer_po;?>" required readonly>
                                </td>
                                  <td width="20%" rowspan="4" style="vertical-align: top;">
                                    <b>HSN Code <span style="color:red">*</span></b>
                                </td>
                                <td width="30%" rowspan="4" style="vertical-align: top;">
                                    <input type="text" class="form-control" value="<?php echo $hsn;?>" name="hsn_code">
                                </td>
                            </tr>
                            <tr>
                                <td width="20%">
                                    <b>Cust PO Date <span style="color:red">*</span></b>
                                </td>
                                <td width="30%">
                                    <input type="text" class="form-control" name="cust_po_date" required value="<?php echo date('d-m-Y',strtotime($customer_po_date));?>" readonly>
                                </td>
                            </tr>
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
                                <div class="form_box">
                                    <table class="table">
                                        <tr>
                                            <td width="40%">
                                                <b>Name<span style="color:red">*</span></b>
                                            </td>
                                            <td width="60%">
                                                <input type="text" class="form-control" name="bill_to_name" id="bill_to_name" value="<?php echo $bill_to_name;?>" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="40%">
                                                <b>Address <span style="color:red">*</span></b>
                                            </td>
                                            <td width="60%">
                                                <input type="text" class="form-control" name="bill_to_address" id="bill_to_address" value="<?php echo $bill_address;?>" required>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="table">
                                        <tr>
                                            <td width="20%">
                                                <b>State<span style="color:red">*</span></b>
                                            </td>
                                            <td width="30%">
                                                <input type="text" class="form-control" required name="bill_to_state" id="bill_to_state" value="<?php echo $bill_state;?>">
                                            </td>
                                            <td width="20%">
                                                <b>State Code<span style="color:red">*</span></b>
                                            </td>
                                            <td width="30%">
                                                <input type="text" class="form-control" required name="bill_to_state_code" id="bill_to_state_code" value="<?php echo $bill_state_code;?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="20%">
                                                <b>GST No.<span style="color:red">*</span></b>
                                            </td>
                                            <td width="30%">
                                                <input type="text" class="form-control" required name="bill_to_gst_no" id="bill_to_gst_no" value="<?php echo $bill_gst;?>">
                                            </td>
                                            <td width="20%">
                                                <b>PAN No.<span style="color:red">*</span></b>
                                            </td>
                                            <td width="30%">
                                                <input type="text" class="form-control" required name="bill_to_pan_no" id="bill_to_pan_no" value="<?php echo $bill_pan;?>">
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
                                <div class="form_box">
                                    <table class="table">
                                        <tr>
                                            <td width="40%">
                                                <b>Name <span style="color:red">*</span></b>
                                            </td>
                                            <td width="60%">
                                                <input type="text" class="form-control" name="ship_to_name" id="ship_to_name" required value="<?php echo $ship_to_name;?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="40%">
                                                <b>Address <span style="color:red">*</span></b>
                                            </td>
                                            <td width="60%">
                                                <input type="text" class="form-control" name="ship_to_address" id="ship_to_address" required value="<?php echo $ship_address;?>">
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="table">
                                        <tr>
                                            <td width="20%">
                                                <b>State<span style="color:red">*</span></b>
                                            </td>
                                            <td width="30%">
                                                <input type="text" class="form-control" name="ship_to_state" id="ship_to_state" required value="<?php echo $ship_state;?>">
                                            </td>
                                            <td width="20%">
                                                <b>State<span style="color:red">*</span></b>
                                                <b>State Code</b>
                                            </td>
                                            <td width="30%">
                                                <input type="text" required class="form-control" name="ship_to_state_code" id="ship_to_state_code" value="<?php echo $ship_state_code;?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="20%">
                                                <b>GST No.<span style="color:red">*</span></b>
                                            </td>
                                            <td width="30%">
                                                <input type="text" required class="form-control" name="ship_to_gst_no" id="ship_to_gst_no" value="<?php echo $ship_gst;?>">
                                            </td>
                                            <td width="20%">
                                                <b>PAN No.<span style="color:red">*</span></b>
                                            </td>
                                            <td width="30%">
                                                <input type="text" required class="form-control" name="ship_to_pan_no" id="ship_to_pan_no" value="<?php echo $ship_pan;?>">
                                            </td>
                                        </tr>
                                    </table>
                                     <p style="height:20px;"></p>
                                </div>
                            </div>
                        </div>
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
                        $rest=$this->db->select('*')->from('performa_invoice_items')->where('record_id',$record_id)->get();
        if($rest->num_rows()>0)
        {
            $i=1;
        foreach($rest->result() as $row)
        {

            if($row->discount_per>0)
            {
                $revised_rate=floatval($row->rate-($row->rate*($row->discount_per/100)));
            }else
            {
                $revised_rate=floatval($row->rate);
            }

            $total=floatval(round($revised_rate*$row->qty,2));
                      
                    $item_code=$row->item_code;
                    $desc=$row->description;
                    $hsn=$row->hsn;
                    $qty=$row->qty;
                    $price=$row->rate;
                    $discount_per=$row->discount_per;
                    $uom=$row->uom;
                    $total_price=$qty*$price;
                      $basic_cost[]=$total_price;
?>
                        <table class="table existingTable<?php echo $row->id;?>">
                            <input type="hidden" name="itemsID[]" value="<?php echo $row->id;?>">
                 
                            <tr>
                                <!-- <td width="20%">
                                    <label><b>Item Code</b><span style="color:red">*</span></label>
                                     <input type="text" name="item_code<?php //echo $row->id;?>" required class="form-control" id="item_code<?php //echo $i;?>" value="<?php //echo $item_code;?>"> 
                                </td> -->
                                
                                <td width="20%">
                                    <label for=""><b>Desc. of Goods/Services</b><span style="color:red">*</span></label>
                                     <textarea name="desc_goods<?php echo $row->id;?>" required  id="desc_goods<?php echo $i;?>" class="form-control" id=""><?php echo $desc;?></textarea>
                                </td>
                               
                                <!-- <td width="20%">
                                    <label for=""><b>HSN/SAC Code</b><span style="color:red">*</span></label>
                                       <input type="text" name="hsn_code<?php //echo $row->id;?>" id="hsn_code<?php //echo $i;?>" required class="form-control" value="<?php //echo $hsn;?>">
                                </td> -->
                              
                                <td width="10%">
                                    <label for=""><b>Quantity</b><span style="color:red">*</span></label>
                                     <input type="number" name="qty<?php echo $row->id;?>" id="qty<?php echo $i;?>" class="form-control" required value="<?php echo floatval($qty);?>" onblur="checkDiscountValue(<?php echo $i;?>);">
                                </td>
                                <td width="20%">
                                    <b>UOM<span style="color:red">*</span></b>
                                     <input type="text" name="uom<?php echo $row->id;?>" id="uom<?php echo $i;?>" required class="form-control" value="<?php echo $uom;?>" readonly>
                                </td>
                            </tr>
                        </table>
                        <table class="table existingTable<?php echo $row->id;?>">
                            <tr>
                                <td width="20%">
                                    <b>Rate INR<span style="color:red">*</span></b>
                                     <input type="number" name="rate_inr<?php echo $row->id;?>" id="rate_inr<?php echo $i;?>" class="form-control" value="<?php echo floatval($price);?>" onblur="checkDiscountValue(<?php echo $i;?>);" required>
                                </td>
                               
                                <!-- <td width="20%">
                                    <b>Disc%<span style="color:red">*</span></b>
                                      <input type="number" name="discount<?php //echo $row->id;?>" id="discount<?php //echo $i;?>" class="form-control" value="<?php //echo $discount_per;?>" onblur="checkDiscountValue(<?php //echo $i;?>);" required>
                                </td> -->
                               
                                <!-- <td width="20%">
                                    <b>After Disc Value<span style="color:red">*</span></b>
                                     <input type="number" name="discount_value<?php //echo $row->id;?>" required  id="discount_value<?php //echo $i;?>" class="form-control" value="<?php //echo floatval($revised_rate);?>" readonly>
                                </td> -->
                               
                                <td width="20%">
                                    <b>Total Amount<span style="color:red">*</span></b>
                                       <input type="number" name="total_amount<?php echo $row->id;?>" required id="total_amount<?php echo $i;?>" class="form-control" value="<?php echo $total_price;?>" readonly>
                                </td>
                              
                                <td width="20%" style="text-align: center;">
                                    <button type="button" class="btn btn-dark" style="padding: 0px !important; height: 21px !important; font-size: 13px !important; width: 21px; margin-top: 20px;" onclick="delete_records(<?php echo $row->id;?>);"><b>X</b></button>
                                </td>
                            </tr>
                        </table><hr style="border-top:1px solid #7f7f7f;" class="NewaddTable<?php echo $row->id;?>">
                        <?php $i++;
                        } } ?>



                       

                    <div class="col-md-12">
                        <input type="checkbox" name="check" id="check" value="1" onchange="showMoreData();">&nbsp;<strong style="color:black;font-weight: bold;">Add More Line Items</strong>
                    </div>

                        <?php 
                        $i=$i+1;
                        ?>
                        <table class="table NewaddTable" style="display: none;">
                                             <tr>
                                <!-- <td width="20%">
                                    <label><b>Item Code</b><span style="color:red">*</span></label>
                                     <input type="text" name="item_code[]"  id="item_code<?php //echo $i;?>" class="form-control" id="item_code"> 
                                </td> -->
                               
                                <td width="20%">
                                    <label for=""><b>Desc. of Goods/Services</b><span style="color:red">*</span></label>
                                     <textarea name="desc_goods[]"  id="desc_goods<?php echo $i;?>" class="form-control" id=""></textarea>
                                </td>
                               
                                <!-- <td width="20%">
                                    <label for=""><b>HSN/SAC Code</b><span style="color:red">*</span></label>
                                       <input type="text" name="hsn_code[]"  id="hsn_code<?php //echo $i;?>" class="form-control">
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
                                    <b>Rate INR<span style="color:red">*</span></b>
                                     <input type="text" name="rate_inr[]" id="rate_inr<?php echo $i;?>" class="form-control" onblur="checkDiscountValue(<?php echo $i;?>);" >
                                </td>
                               
                                <!-- <td width="20%">
                                    <b>Disc%<span style="color:red">*</span></b>
                                      <input type="text" name="discount[]" id="discount<?php //echo $i;?>" class="form-control" onblur="checkDiscountValue(<?php //echo $i;?>);" >
                                </td>
                               
                                <td width="20%">
                                    <b>After Disc Value<span style="color:red">*</span></b>
                                     <input type="text" name="discount_value[]"  readonly id="discount_value<?php //echo $i;?>" class="form-control">
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

                         <div style="display: flex; align-items: center; width: 100%;">
                    <div style="flex: 1; height: 1px; background-color: #000;"></div>
                    <div style="padding: 0 10px;font-weight: bold;color:red;font-size:20px;">Total Basic Cost - <span id="tbc"><?php echo array_sum($basic_cost);?></span><input type="hidden" name="basicCost" class="basicCost" value="<?php echo array_sum($basic_cost);?>"><input type="hidden" name="OriginalbasicCost" class="OriginalbasicCost" value="<?php echo array_sum($basic_cost);?>"></div>
                    <div style="flex: 1; height: 1px; background-color: #000;"></div>
                    </div>

                    

                        <table class="table" style="margin-top:60px;">
<tr>
    <td>
        <label for=""><b>Remarks</b></label>
        <textarea name="remarks" class="form-control" id=""><?php echo $remarks;?></textarea>
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


    <!------------------All Item Fields Before Change--------------------------->

     <!-- $('#item_add').append('<div id="row' + i + '" class="row" style="margin-top:10px; border-top: 1px solid #7f7f7f;"><table class="table NewaddTable"><tr><td width="20%"><label><b>Item Code</b><span style="color:red">*</span></label><input type="text" name="item_code[]" class="form-control" required id="item_code'+i+'"> </td><td width="20%"><label for=""><b>Desc. of Goods/Services<span style="color:red">*</span></b></label><textarea name="desc_goods[]" class="form-control" id="desc_goods'+i+'" required></textarea></td><td width="20%"><label for=""><b>HSN/SAC Code<span style="color:red">*</span></b></label><input type="text" required name="hsn_code[]" class="form-control"></td><td width="20%"><label for=""><b>Quantity<span style="color:red">*</span></b></label><input type="text" required name="qty[]" id="qty'+i+'" class="form-control" onblur="checkDiscountValue('+i+');"></td><td width="20%"><b>UOM<span style="color:red">*</span></b><input required type="text" name="uom[]" id="uom'+i+'" value="Nos" class="form-control"></td></tr></table><table class="table NewaddTable"><tr><td width="20%"><b>Rate INR<span style="color:red">*</span></b><input type="text" required name="rate_inr[]" id="rate_inr'+i+'" onblur="checkDiscountValue('+i+');" class="form-control"></td><td width="20%"><b>Disc%<span style="color:red">*</span></b><input type="text" required name="discount[]" id="discount'+i+'" class="form-control" onblur="checkDiscountValue('+i+');"></td><td width="20%"><b>After Disc Value<span style="color:red">*</span></b><input type="text"  required name="discount_value[]" id="discount_value'+i+'" readonly class="form-control"></td><td width="20%"><b>Total Amount<span style="color:red">*</span></b><input type="text" name="total_amount[]" readonly id="total_amount'+i+'" required class="form-control"></td><td width="20%" style="text-align:center"><button type="button" name="add" class="btn_remove  btn btn-danger" id="' + i + '" style="padding: 0px !important; height: 21px !important; font-size: 13px !important; width: 21px; margin-top:20px;"><i class="fa fa-close"></i></button></td></tr></table></div>'); -->


    <script>
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
       var discount=$("#discount"+flag).val();

    /** MINUS DISCOUNT PERCENTAGE **/
       if(discount!='' && discount>0)
       {
    
            var rate_inr=rate_inr-(rate_inr*(discount/100));
            var rate_inr=rate_inr.toFixed(2);
            $("#discount_value"+flag).val(rate_inr);
             var a=qty*rate_inr;
            var a=a.toFixed(2);
            $("#total_amount"+flag).val(a);
       }else
       {
            $("#discount_value"+flag).val(rate_inr);
            var a=qty*rate_inr;
            var a=a.toFixed(2);
            $("#total_amount"+flag).val(a);
       }

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


   function delete_records(id)
   {

    if(confirm('Do you really want to delete this Row?'))
    {

       $.ajax({
                    type: "post",
                    url: "<?php echo page_url;?>Form/DeleteItems",
                    data: "id=" + id,
                    success: function (data) {
                        if(data>0)
                        {
                        $(".existingTable"+id).remove();
                        }   

                    }
                });


    }
    

   }
    </script>

</body>

</html>