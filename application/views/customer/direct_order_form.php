<?php 
$CI = &get_instance();
$DI = &get_instance();
$CI->load->model('Master_model', 'master');
$DI->load->model('Salescrm_model', 'salescrm');
$drums_tnc = $CI->master->getAllTnC(1);
$bulk_tnc = $CI->master->getAllTnC(2);
$getAllStates = $DI->salescrm->getAllStates();
$financial=$DI->salescrm->get_finacial_year_range();
$max_customer_code=$DI->salescrm->getHighestCustomerCode();
$getAllTransporters = $DI->salescrm->getAllTransporters();
$pending_tally_orders = $DI->salescrm->check_for_not_send_to_tally();
if($pending_tally_orders>0)
{
    echo "<strong style='color:red;font-weight:bold;'>SOME OLD ORDERS BILLING (SEND TO TALLY IS NOT DONE. PLEASE COMPLETE THE PREVIOUS BILLING BEFORE ANY NEW ORDERS</strong>"; exit;
}
if(count($financial)>0)
{
    $start_date=$financial['start_date']." 00:00:00";
    $end_date=$financial['end_date']." 23:59:59";

}else
{
    $start_date=date('Y-04-01')." 00:00:00";
    $end_date=date('Y-m-d')." 23:59:59";
}

        // $sql = $this->db->select('generated_order_id')
        //                 ->from('order_punch')
        //                 ->where('added_on>=',$start_date)
        //                 ->where('added_on<=',$end_date)
        //                 ->order_by('id', 'DESC')
        //                 ->limit(1)
        //                 ->get();

        //     if ($sql->num_rows() > 0) {
        //         foreach ($sql->result() as $row);   
        //             $generated_order_id = str_pad($row->generated_order_id+1, 3, '0', STR_PAD_LEFT);
        //     } else {
        //             $generated_order_id = '001';
        //     }

        //     $invoiceno="INV".date('Y').$generated_order_id;

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="NJ Media">

    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

    <title><?php echo sitetitle; ?> Direct Order Form</title>

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
    <style>
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


    <div class="wrapper">
        <div class="container">

            <!-- Page-Title -->
            <div class="row" style="margin-top:20px;">
                <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                    <div class="page-title-box">

                        <h4 class="page-title">Direct Order Form</h4>
                    </div>
                </div>
            </div>
            <!-- end page title end breadcrumb -->
            <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
            <form id="quotation" method="post" action="<?php echo page_url;?>Customer/add_direct_order" enctype="multipart/form-data" onsubmit="return validate_form();">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <div class="col-md-12">

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
                                                        <option value="<?php echo $row->source_id;?>"><?php echo $row->lead_source;?></option>
                                                    <?php }
                                                    } ?>
                                        </select>
                                    </div>
                                </div>


                                 

                                <div style="clear:both;height:10px"></div>


                                 <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Billing Company <span style="color:red">*</span></label>
                                        <span id="error_state" style="color:red;"></span>
                                        <select class="form-control mand" name="company" id="company" required onchange="resetform(this.value);">
                                            <option value="">Select Company</option>
                                            <?php
                                            $q = $this->db->select('id, companyname')->from('store_rack_location')->where('status', 1)->get();
                                            foreach ($q->result() as $row) {
                                            ?>
                                                <option value="<?php echo $row->id; ?>"><?php echo $row->companyname; ?></option>
                                            <?php } ?>
                                        </select>

                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Customer Name&nbsp;</label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <!-- <span class="pull-right"><a href="javascript;:" data-toggle="modal" data-target="#con-close-modal"><i class="fa fa-plus" title="Add New Customer"></i></a></span> -->

                                        <select class="form-control mand select3" name="customer" id="customer" required onchange="getCustomerDetails();">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                    <div class="form-group" style="display:none;">
                                        <label for="field-2" class="control-label">Select Multiple Companies for Customer</label>
                                        <span id="error_state" style="color:red;"></span>
                                        <select class="form-control" name="mul_company[]" id="mul_company">
                                        </select>

                                    </div>
                                </div>

                                 <div class="col-md-2">
                                    <div class="form-group">
                                          <label for="field-2" class="control-label">Customer Alias&nbsp;</label>
                                    
                                        <input type="text" class="form-control" name="alias" id="alias" readonly>
                                     </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                          <label for="field-2" class="control-label">Customer Code&nbsp;</label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <input type="number" min="1" class="form-control mand" name="customer_codes" id="customer_codes">
                                     </div>
                                </div>

                                 <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Sales Agent</label>
                                        <span id="error_rack_location" style="color:red;">*</span>

                                        <select class="form-control mand" name="agent" id="agent" required>
                                           <!--  <option value="">Select</option> -->
                                           
                                        </select>
                                    </div>
                                </div>
                               
                            </div>
                            <div class="col-md-12" style="display:none;" id="show">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-3" class="control-label">Competitor Product</label>
                                       <!--  <span id="error_rack_location" style="color:red;">*</span> -->
                                        <select class="form-control" name="comp_product[]" id="comp_product0" onchange="getourproductname(0);"></select>
                                    </div>
                                    <input type="hidden" name="rebrand_used[]" id="rebrand_used0" value="0">
                                    <div class="form-group" id="rebrand_appl0" style="display:none;">
                                        <label for="field-3" class="control-label">Want to Rebrand?</label><br/>
                                        <input type="checkbox" name="rebrand[]" id="rebrand0" value="1" onchange="check_for_rebrand_appl(0);">
                                    </div>

                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-3" class="control-label">Product</label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <select class="form-control mand products" name="product[]" id="product0" onchange="getprice(0,this.value);getdiscount(0,this.value); getunit(0,this.value); getbatchcode(0,this.value); getstock1(0); match_stock_availability1(0); check_for_rebrand(0)" required>
                                            <option value="">Select</option></select>
                                         <span id="recommendation0"></span>
                                    </div>

                                      <div class="form-group" id="rebrand_prd_div0" style="display:none;">
                                        <label for="field-3" class="control-label">Rebrand Product <span  style="color:red;">*</span></label>
                                        <select name="rebrand_prd[]" id="rebrand_prd0" class="form-control">
                                            
                                        </select>
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
                                <label for="field-2" class="control-label">Stock Avail.</label>
                                <span id="error_rack_location" style="color:red;">*</span>
                                <input type="text" readonly class="form-control"  id="stock0" value="">

                                </div>
                                </div>


                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Qty</label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <input type="text" class="form-control mand" name="qty[]" id="qty0" autocomplete="off" oninput="allow_decimal('qty0')" required onkeyup="match_stock_availability1(0);">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Unit</label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <input type="text" name="unit_name[]" id="unit_name0" class="form-control" readonly>
                                        <input type="hidden" name="pack_size[]" id="pack_size0">
                                        <input type="hidden" name="pack_size_text[]" id="pack_size_text0">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Order Price/<span class="list_price_unit0"></span></label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <input type="text" class="form-control mand" name="listprice[]" id="listprice0" value="" onblur="getnetamt(0)" autocomplete="off" oninput="allow_decimal('listprice0');">
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
                                        <textarea class="form-control drums" name="drums_tnc" id="drums" value=""></textarea>
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
                                            <textarea class="form-control bulk" name="bulk_tnc" id="bulk" value=""></textarea>
                                        </div>
                                
                                </div>
                                <script>
                                CKEDITOR.replace('bulk');
                                </script>
                            </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12" style="margin-top:20px;">
                                    <div class="col-md-4" style="display:none;">
                                          <label>Invoice No.</label>
                                          <input type="text" name="invoice_no" id="invoice_no" class="form-control mand" required autocomplete="nope" readonly value="0">
                                    </div>


                                    <div class="col-md-4">
                                    <label>Sales No</label>
                                    <input type="text" name="sales_no" id="sales_no" class="form-control mand" required autocomplete="nope" readonly value="">
                                    </div>
                                                                        <div class="col-md-4">
                                          <label>PO No.</label>
                                          <input type="text" name="po_no" class="form-control" autocomplete="nope">
                                    </div>
                                    <div class="col-md-4">
                                          <label>PO Date</label>
                                          <input type="text" name="po_date" id="datepicker" class="form-control" autocomplete="nope">
                                    </div>
                                     <div class="col-md-4">
                                            <label>Upload PO/Evidence </label>
                                            <span class="input-icon icon-right">
                                              <input type="file" name="upload_file">
                                            </span>
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
                              <div class="col-md-4">
                                <label>Payment Type<span style="color: red;">*</span></label>
                                <span class="input-icon icon-right" style="margin-bottom:10px">
                                <input type="hidden" name="customer_id" value="">
                                <select class="form-control mand" name="payment_type" id="payment_type" required onchange="checkpdf();cheque_detail_for_adv();">
                                  <option value="">SELECT PAYMENT TYPE</option>
                                  <option value="2">Cash</option>
                                  <option value="3">Online</option>
                                  <option value="4">PDC</option>
                                  <option value="5">Credit</option>
                                  <option value="6">Advance</option>
                                </select>
                                </span><br/>
                                <span style="margin-top:10px;text-align: center;display:none;" id="paychange"><a href="javascript:;" onclick="payment_change_req();">Change in Payment terms? Raise request to admin</a></span>
                              </div>


                              <script type="text/javascript">

                                    $(document).ready(function() {

                                     $("#pcd_details").css('display','none');
                                     $(".cheque_details").css('display','none');
                                     $("#pdcrecv").removeClass('mand');
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

                                        if(payment_type==4)
                                        {
                                            $("#pcd_details").css('display','');
                                            $(".cheque_details").css('display','');
                                            $("#pdcrecv").addClass('mand');

                                            // cheque_detail();
                                        }
                                    });


                                  function checkpdf()
                                  {
                                     $("#pcd_details").css('display','none');
                                     $(".cheque_details").css('display','none');
                                     $("#pdcrecv").removeClass('mand');
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

                                    if(payment_type==4)
                                    {
                                        $("#pcd_details").css('display','');
                                        $(".cheque_details").css('display','');
                                        $("#pdcrecv").addClass('mand');

                                        // cheque_detail();
                                    }
                                  }

                              </script>

                                <div class="col-md-4" id="credit_days" style="display: none;">
                                <label>Credit Days<span style="color: red;">*</span></label>
                                <span class="input-icon icon-right" style="margin-bottom:10px">
                                <input type="number" name="paymentterms" id="paymentterms" class="form-control" value="">

                                </span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4" id="pcd_details" style="display:none;">
                                    <div class="form-group">
                                        <label>PDC Recieved?</label>
                                        <select name="pdcrecv" id="pdcrecv" class="form-control" onchange="cheque_detail();">
                                            <option value="">Select</option>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>
                                <script>
                                    function cheque_detail()
                                    {
                                         $(".cheque_details").css('display','none');
                                            $("#cheque_no").removeClass('mand');
                                            $("#cheque_date").removeClass('mand');
                                            $("#cheque_no").attr('required',false);
                                            $("#cheque_date").attr('required',false);

                                        var pdcrecv=$("#pdcrecv").val();
                                        if(pdcrecv==1)
                                        {
                                            // alert('hi');
                                            $(".cheque_details").css('display','');
                                            $("#cheque_no").addClass('mand');
                                            $("#cheque_date").addClass('mand');
                                            $("#cheque_no").attr('required',true);
                                            $("#cheque_date").attr('required',true);

                                        }

                                    }

                                    function cheque_detail_for_adv() {
                                        var payment_type=$("#payment_type").val();
                                            $(".cheque_details").css('display','none');
                                            $("#cheque_no").removeClass('mand');
                                            $("#cheque_date").removeClass('mand');
                                            $("#cheque_no").attr('required',false);
                                            $("#cheque_date").attr('required',false);

                                        if(payment_type==6)
                                        {
                                            $(".cheque_details").css('display','');
                                            $("#cheque_no").addClass('mand');
                                            $("#cheque_date").addClass('mand');
                                            $("#cheque_no").attr('required',true);
                                            $("#cheque_date").attr('required',true);

                                        }
                                    }
                                </script>

                                <div class="col-md-4 cheque_details" style="display:none;">
                                    <div class="form-group">
                                        <label>Cheque No. <span style="color: red">*</span></label>
                                        <input type="text" name="cheque_no" id="cheque_no" class="form-control">
                                    </div>
                                </div>


                                  <div class="col-md-4 cheque_details" style="display:none;">
                                    <div class="form-group">
                                        <label>Cheque Date <span style="color: red">*</span></label>
                                        <input type="date"  min="<?php echo date('Y-m-d');?>" name="cheque_date" id="cheque_date" class="form-control">
                                    </div>
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
                                  <input type="text" name="bill_to" id="bill_to" autocomplete="nope" class="form-control mand" required>
                                </div>
                                <div class="col-md-3" style="display:none">
                                  <label>Customer Name<span style="color: red;">*</span></label>
                                  <input type="text" name="billing_name" id="billing_name" autocomplete="nope" class="form-control" value="">
                                </div>
                                <div class="col-md-3">
                                  <label>Address<span style="color: red;">*</span></label>
                                  <textarea class="form-control mand" name="billing_address" autocomplete="nope" id="billing_address" required></textarea>
                                </div>
                                <div class="col-md-3">
                                  <label>State<span style="color: red;">*</span></label>
                                  <select class="form-control mand" name="billing_state" id="billing_state" required>
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
                                  <input type="text" name="billing_city" id="billing_city" autocomplete="nope" class="form-control mand" value="" required>
                                </div>
                                <div style="clear:both;height:5px"></div>
                                <div class="col-md-3">
                                  <label>Pincode<span style="color: red;">*</span></label>
                                  <input type="text" name="billing_pincode" id="billing_pincode" autocomplete="nope" class="form-control mand" value="" onblur="check_billing_pincode()" required>
                                </div>
                                <div class="col-md-3">
                                  <label>Phone No</label>
                                  <input type="text" name="billing_phone_no" id="billing_phone_no" autocomplete="nope" class="form-control">
                                </div>
                                <div class="col-md-3">
                                  <label>Mobile No<span style="color: red;">*</span></label>
                                  <input type="number" name="billing_mobile_no" id="billing_mobile_no" autocomplete="nope" class="form-control mand" minlength="10" onblur="check_billing_mobile()" required>
                                </div>
                                <div class="col-md-3">
                                  <label>Email</label>
                                  <input type="text" name="billing_email" id="billing_email" autocomplete="nope" class="form-control" onblur="check_billing_email()">
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
                                      <h4 class="page-title text-center">&nbsp Shipping Address</h4>
                                  </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-12">
                                <div class="col-md-3">
                                  <label>Ship To<span style="color: red;">*</span></label>
                                  <input type="text" name="ship_to" id="ship_to" autocomplete="nope" class="form-control mand" value="" required>
                                </div>
                                <div class="col-md-3" style="display:none">
                                  <label>Customer Name<span style="color: red;">*</span></label>
                                  <input type="text" name="shipping_name" id="shipping_name" class="form-control" autocomplete="nope">
                                </div>
                                <div class="col-md-3">
                                  <label>Address<span style="color: red;">*</span></label>
                                  <textarea class="form-control mand" name="shipping_address" autocomplete="nope" id="shipping_address" required></textarea>
                                </div>
                                <div class="col-md-3">
                                  <label>State<span style="color: red;">*</span></label>
                                  <select class="form-control mand" name="shipping_state" id="shipping_state" required>
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
                                  <input type="text" name="shipping_city" id="shipping_city" autocomplete="nope" class="form-control mand" required>
                                </div>
                                <div style="clear:both;height:5px"></div>
                                <div class="col-md-3">
                                  <label>Pincode<span style="color: red;">*</span></label>
                                  <input type="text" name="shipping_pincode" id="shipping_pincode" autocomplete="nope" class="form-control mand" onblur="check_shipping_pincode()" required>
                                </div>
                                <div class="col-md-3">
                                  <label>Phone No</label>
                                  <input type="text" name="shipping_phone_no" id="shipping_phone_no" autocomplete="nope" class="form-control">
                                </div>
                                <div class="col-md-3">
                                  <label>Mobile No<span style="color: red;">*</span></label>
                                  <input type="number" name="shipping_mobile_no" id="shipping_mobile_no" autocomplete="nope" minlength="10" maxlength="10" class="form-control mand" onblur="check_shipping_mobile()" required>
                                </div>
                                <div class="col-md-3">
                                  <label>Email</label>
                                  <input type="text" name="shipping_email" id="shipping_email" autocomplete="nope" class="form-control" onblur="check_shipping_email()">
                                </div>
                                
                              </div>
                            </div>

                            <div class="row text-center" style="margin-top:50px;">
                              <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                                  <div class="col-md-4">
                                      <label>Freight Applicable?</label>
                                      <input type="checkbox" id="check_freight" name="check_freight" value="1" onchange="checkfreight()">
                                  </div>
                                  <div class="col-md-4" id="freight" style="display:none;">
                                    <label>Freight Amount<span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" name="freight_amt" id="freight_amt" oninput="allow_decimal('freight_amt')">
                                  </div>
                              </div>
                            </div>

                           <!--  <div class="row" style="margin-top:20px;">
                                <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                                    <div class="page-title-box">                           
                                        <h4 class="page-title text-center">&nbsp Transport Detail</h4>
                                    </div>
                                </div>
                            </div> -->
                            <!-- <div class="row">
                              <div class="col-md-12">

                                <div class="col-md-2">
                                <label>Select Type</label>
                                <select name="ttype" id="ttype" class="form-control mand" onchange="chk_owned()">
                                <option value="">Select Type</option>
                                <option value="1">Self Owned</option>
                                <option value="2">Hired</option>
                                </select>
                                </div>
                                <script type="text/javascript">
                                    function chk_owned() {
                                    $(".external_transporter").css('display', 'none');

                                    var type = $("#ttype").val();
                                    $(".selfowned").css('display','none')
                                    $("#self_vehicle_no").removeClass('mand');
                                    $(".external_transporter").css('display','none')
                                    $("#transporter_name").removeClass('mand');
                                    $("#tmobile_no").removeClass('mand');
                                    $("#taddress").removeClass('mand');
                                    $("#vehicle_no").removeClass('mand');
                                    $("#transport_rate").removeClass('mand');
                                    $("#rate_type").removeClass('mand');

                                    if (type != '') {
                                    if (type == 1) {
                                     $(".selfowned").css('display','');
                                     $("#self_vehicle_no").attr('required',true);
                                     $("#self_vehicle_no").addClass('mand');
                                    } else {
                                    $(".external_transporter").css('display','');
                                    $("#transporter_name").addClass('mand');
                                    $("#tmobile_no").addClass('mand');
                                    $("#taddress").addClass('mand');
                                    $("#vehicle_no").addClass('mand');
                                    $("#transport_rate").addClass('mand');
                                    $("#rate_type").addClass('mand');

                                    }

                                    }
                                    }

                                </script>


                                 <div class="col-md-2 selfowned" style="display:none">
                                      <label>Vehicle No.<span style="color: red;">*</span></label>
                                      <select name="self_vehicle_no" id="self_vehicle_no" class="form-control select34" >
                                        <option value=""></option>
                                        <?php 
                                        $restey=$this->db->select('name')->from('our_vehicles')->get();
                                        if($restey->num_rows()>0)
                                            { 
                                                foreach($restey->result() as $row)
                                                {
                                                ?>
                                        <option value="<?php echo $row->name;?>"><?php echo $row->name;?></option>
                                        <?php } } ?>
                                      </select>
                                     
                                  </div>-->

                                <!-- HIRED TRANSPORTER -->

                                <!-- <div class="col-md-3 external_transporter" style="display: none;">
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
                               
                                </div>
                                </div> -->
                               <!--  <div class="col-md-3 external_transporter" style="display: none;">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Mobile No.<span style="color:red;">*</span></label>
                                <input type="number" maxlength="10" class="form-control" name="tmobile_no" id="tmobile_no" value="" data-mask="(999) 999-9999">
                                </div>
                                </div> -->
                               <!--  <div class="col-md-3 external_transporter" style="display: none;">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Address</label>
                                <textarea class="form-control" name="taddress" id="taddress"></textarea>
                                </div>
                                </div> -->
                              <!--   <div class="col-md-2 external_transporter" style="display: none;">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Vehicle No.<span style="color:red;">*</span></label>
                                <input type="text" class="form-control" name="vehicle_no" id="vehicle_no">
                                </div>
                                </div> -->

                                <!--   <div class="col-md-2 external_transporter" style="display: none;">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Transporter Rate Type<span style="color:red;">*</span></label>
                                <select class="form-control" id="rate_type" name="rate_type">
                                    <option value="">Type</option>
                                    <option value="1">Per Ltr</option>
                                    <option value="2">Fix Rate</option>
                                </select>
                                </div>
                                </div>

                                <div class="col-md-2 external_transporter" style="display: none;">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Transporter Rate<span style="color:red;">*</span></label>
                                <input type="text" class="form-control" id="transport_rate" name="transport_rate" onkeyup="allow_decimal('trate');">
                                </div>
                                </div>
 -->


<!-- 
                                   <div class="col-md-3">
                                      <label>Vehicle Type<span style="color: red;">*</span></label>
                                      <input type="text" name="vehicle_type" id="vehicle_type" class="form-control" autocomplete="nope" required>
                                   </div> -->
                                  <!--   <div class="col-md-3">
                                      <label>Destination<span style="color: red;">*</span></label>
                                      <input type="text" name="destination" id="destination" class="form-control" autocomplete="nope" required>
                                   </div> -->
                              <!--</div>
                            </div> -->
                           
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
                                  <input type="text" name="msme_no" id="msme_no" class="form-control" value="" autocomplete="nope">
                                </div>

                                <div class="col-md-3">
                                  <label>PAN/IT No.<span style="color: red;">*</span></label>
                                  <input type="text" name="pan_no" id="pan_no" class="form-control mand" autocomplete="nope" onblur="check_pan()" required="">
                                </div>
                                <div class="col-md-3">
                                  <label>Registration Type<span style="color: red;">*</span></label>
                                  <input type="text" name="registration_type" id="registration_type" class="form-control mand" autocomplete="nope" value="Regular" readonly>
                                </div>
                                <div class="col-md-3">
                                  <label>GSTIN/UIN<span style="color: red;">*</span></label>
                                  <input type="text" name="gst_no" id="gst_no" class="form-control mand gst_no" autocomplete="nope" onblur="validate_gst()" readonly>
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
                                <div class="col-md-9">
                                  <label>Instructions for Billing & Dispatch Department<span style="color: red;"></span></label>
                                  <textarea class="form-control" name="note"></textarea>
                                </div>
                              </div>
                            </div>
                            <div class="row" style="margin-top: 20px">
                                <div class="col-md-12">
                                 <div class="form-group pull-right">
                                    <input type="submit" id="save" class="btn btn-info" value="Submit">
                                </div>
                                </div>
                            </div>
                        </div>
            </form>

        </div>
    </div>


     <div id="con-close-modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Add New customer</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="field-2" class="control-label">Company <span style="color:red">*</span></label>
                                <span id="error_state" style="color:red;"></span>
                                <select class="form-control" id="clientcompany" required onchange="checkIfDuplicateNoExists()">
                                <option value="">Select Company</option>
                                <?php 
                                $q = $this->db->select('id, companyname')->from('store_rack_location')->where('status',1)->get();
                                foreach($q->result() as $row){
                                ?>
                                <option value="<?php echo $row->id;?>"><?php echo $row->companyname;?></option>
                                <?php }?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                 <label for="field-2" class="control-label">Company Name</label>
                                 <span id="error_rack_location" style="color:red;">*</span>
                                 <input type="text" class="form-control"  id="cust_companyname"  value="" required>
                            </div>
                        </div>


                        <div class="col-md-4">
                        <div class="form-group">
                        <label for="field-2" class="control-label">Customer Code</label>
                        <span id="error_rack_location" style="color:red;">*</span>
                        <input type="text" class="form-control" name="customer_code" id="customer_code" value="<?php echo $max_customer_code;?>" required readonly>
                        </div>
                        </div>

                     <div class="col-md-4">
                            <div class="form-group">
                                 <label for="field-2" class="control-label">GST</label>
                                 <span id="error_rack_location" style="color:red;">*</span>
                                 <input type="text" class="form-control gst_no" id="gst" onblur="validate_gst()" required>
                            </div>
                        </div>

                         <div class="col-md-4">
                            <div class="form-group">
                                 <label for="field-2" class="control-label">MSME Number</label>
                                
                                <input type="text" class="form-control allow_decimal"  id="msme_number">
                            </div>
                        </div>

                    </div>


                     <div class="col-md-4">
                            <div class="form-group">
                                 <label for="field-2" class="control-label">Order Max Limit<span style="color: red;">*</span></label>
                                <input type="number" class="form-control allow_decimal" name="order_max_limit" id="order_max_limit" required value="0">
                            </div>
                        </div>



                    <div class="row">                                           

                        <div class="col-md-2">
                            <div class="form-group">
                        <label for="field-2" class="control-label">Title <span style="color:red">*</span></label>
                        <span id="error_state" style="color:red;"></span>
                        <select class="form-control" name="title" id="title" required>
                        <option value="">Select</option>
                        <option value="Mr.">Mr.</option>
                        <option value="Mrs.">Mrs.</option>
                        <option value="Miss.">Miss.</option>
                        </select>
                        </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                 <label for="field-2" class="control-label">Contact Person</label>
                                 <span id="error_rack_location" style="color:red;">*</span>
                                <input type="text" class="form-control"  id="contact_person"  value="" required>
                            </div>
                        </div>
                          <div class="col-md-4">
                            <div class="form-group">
                                 <label for="field-2" class="control-label">Mobile</label>
                                 <span id="error_rack_location" style="color:red;">*</span>
                                <input type="number" minlength="10" maxlength="10" class="form-control"  id="mobile" required onblur="checkIfDuplicateNoExists()">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                 <label for="field-2" class="control-label">Alternate Mobile</label>
                                 <span id="error_rack_location" style="color:red;"></span>
                                <input type="number" class="form-control"  id="alt_contact"  value="" >
                            </div>
                        </div>


                           <div class="col-md-4">
                            <div class="form-group">
                                 <label for="field-2" class="control-label">Email</label>
                                 <span id="error_rack_location" style="color:red;">*</span>
                                <input type="email" class="form-control" id="email"  value="" required>
                            </div>
                        </div>
                          <div class="col-md-12">
                            <div class="form-group">
                                 <label for="field-2" class="control-label">Billing Address</label>
                                 <span id="error_rack_location" style="color:red;">*</span>
                                <textarea  id="address" required class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="col-md-4">
                        <div class="form-group">
                        <label for="field-2" class="control-label">Billing State <span style="color:red">*</span></label>
                        <span id="error_state" style="color:red;"></span>
                        <select class="form-control"  id="state" required onchange="getcity();">
                        <option value="">Select State</option>
                        <?php 
                        $q = $this->db->select('state_id, state_name')->from('states')->where('country_id','101')->get();
                        foreach($q->result() as $row){
                        ?>
                        <option value="<?php echo $row->state_id;?>"><?php echo $row->state_name;?></option>
                        <?php }?>
                        </select>
                        </div>
                        </div>
                        <div class="col-md-4">
                        <div class="form-group">
                        <label for="field-2" class="control-label">Billing City <span style="color:red">*</span></label>
                        <span id="error_state" style="color:red;"></span>
                        <!-- <select class="form-control select2" name="cityname" id="cityname" required onchange="getcity();"> -->
                        <!-- <option value="">Select City</option> -->
                        
                        <!-- </select> -->
                        <input type="text"  class="form-control " id="cityname" required  >
                        </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                 <label for="field-2" class="control-label">Billing Pincode</label>
                                 <span id="error_rack_location" style="color:red;">*</span>
                                 <input name="pincode" type="text" class="form-control allow_decimal"  id="pincode" maxlength="6" required onblur="validate_pincode()">
                            </div>
                        </div>

                      <!--   <div class="col-md-4">
                        <div class="form-group">
                        <label for="field-2" class="control-label">Credit Period<span style="color:red">*</span></label>
                        <span id="error_state" style="color:red;"></span>
                            <select class="form-control" name="credit_period" id="credit_period" required>
                                <option value="">Select Credit Period</option>
                                <?php //if($getCreditPeriod != '') {
                                       // foreach($getCreditPeriod as $row1) {?>
                                <option value="<?php //echo $row1->id;?>"><?php //echo $row1->credit_period;?></option>
                                <?php //} } ?>
                           </select>
                        </div>
                        </div> -->
<!-- 
                        <div class="col-md-4">
                            <div class="form-group">
                                 <label for="field-2" class="control-label">Credit Limit</label>
                                 <span id="error_rack_location" style="color:red;">*</span>
                                <input type="text" class="form-control allow_decimal" name="credit_limit" id="credit_limit" required>
                            </div>
                        </div> -->

                      

                         <div class="col-md-4" style="display:none;">
                            <div class="form-group">
                                 <label for="field-2" class="control-label">Status</label>
                                 <span id="error_rack_location" style="color:red;">*</span>
                                <select class="form-control" name="status" id="status">
                                    <option value="1">ACTIVE</option>
                                
                                </select>
                            </div>
                        </div>

                    </div>
                    
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    <input type="submit" id="save" class="btn btn-info" value="Submit" onclick="submit_data()"> 
                </div>
            </div>
        </div>
      
    </div><!-- /.modal -->


    <!-- Modal -->
<div id="payment_change" class="modal fade" role="dialog"> <div
class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Payment Term Change Request</h4>
      </div>
     

      <div class="modal-body">
     <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Customer Name</label>
                <input type="text" name="rcust"  class="form-control"  id="rcust" value="" readonly>
                <input type="hidden" name="cust_req" id="cust_req" value="">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Current Payment Type</label>
                <input type="text" name="cpayment" id="cpayment"  class="form-control" value="" readonly required>
            </div>
        </div>

            <div class="col-md-6">
            <div class="form-group">
            <label>Requested Payment Type</label>
            <select class="form-control" name="payment_type_r" id="payment_type_r" required onchange="checkpdf_req();" required>
            <option value="">SELECT PAYMENT TYPE</option>
            <option value="2">Cash</option>
            <option value="3">Online</option>
            <option value="4">PDC</option>
            <option value="5">Credit</option>
            <option value="6">Advance</option>
            </select>

            </div>
            </div>

            <div class="col-md-6" id="credit_days_req" style="display: none;">
            <label>Credit Days<span style="color: red;">*</span></label>
            <input type="number" name="paymentterms_req" id="paymentterms_req" class="form-control" value="">
            </div>

      </div>
    </div>
      <div class="modal-footer">
        <input type="submit" class="btn btn-success" value="Submit" onclick="submit_payment_request();">
      </div>
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
    <script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
    <script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

    <!-- Datatable init js -->
    <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>

    <!-- App js -->
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>



    <script>
        $(document).ready(function() {
            $('.select34').select2({tags:true});
            $('#batch_code0').select2({});
            $("#transporter_name").select2({
              tags: true
          });
            $('#example').dataTable({
                "bProcessing": true,
                "pagination": true,
                  pageLength:50,
                "sAjaxSource": "<?php echo page_url; ?>Customer/customer_list/<?php echo $this->uri->segment(3); ?>",
                "aoColumns": [{
                        mData: 'sr_no'
                    },
                    {
                        mData: 'company'
                    },
                    {
                        mData: 'company_name'
                    },
                    {
                        mData: 'gst'
                    },
                    {
                        mData: 'address'
                    },
                    {
                        mData: 'contactperson'
                    },
                    {
                        mData: 'mobile'
                    },
                    {
                        mData: 'email'
                    },
                    {
                        mData: 'country'
                    },
                    {
                        mData: 'state'
                    },
                    {
                        mData: 'city'
                    },
                    {
                        mData: 'edit'
                    }


                ]
            });

                      jQuery('#datepicker').datepicker({

                        autoclose: true,

                        todayHighlight: true,

                        format: 'dd-mm-yyyy'

                        // startDate: new Date()

                      });
        });
    </script>

    <script>
        function displayproduct(custid) {
          
            if (custid != '') {
                $('#show').css('display', '');
                 $('.remarkbox').css('display', '');
                   getinvoice_no(custid);
                   getsalesorderno();
        
            } else {
                $('#show').css('display', 'none');
                 $('.remarkbox').css('display', 'none');
            }


          


        }


        function getinvoice_no(compid)
        {


                $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Billing/getinvoice_no",
                data: "compid=" + compid,
                success: function(data) {
                //$("#invoice_no").val(data);
                }
                });

        }

        function getsalesorderno()
        {

              $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Billing/getsalesorder_no",
                data: "compid=",
                success: function(data) {
                $("#sales_no").val(data);
                }
                });

        }

        function get_tnc(company) {
                $.ajax({
                    type: "post",
                    url: "<?php echo page_url; ?>Customer/get_tnc",
                    data: {company : company},
                    success: function(data) {
                        var arr = data.split('|');
                        console.log(arr);
                        CKEDITOR.instances['drums'].setData(arr[0]);
                        CKEDITOR.instances['bulk'].setData(arr[1]);
                        // $(".drums").text();
                        // $(".bulk").text(arr[1]);
                    }
                });
        }
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            var i = 1;
            $('#addmore_btn').click(function() {
                $('#dynamictasks').append('<div id="row' + i + '" class="row"><div class="col-md-12"><div class="col-md-2"><div class="form-group"><label for="field-3" class="control-label">Competitor Product</label><select class="form-control" name="comp_product[]" id="comp_product'+i+'" onchange="getourproductname('+i+');"></select></div><input type="hidden" name="rebrand_used[]" id="rebrand_used'+i+'" value="0"><div class="form-group" id="rebrand_appl'+i+'" style="display:none;"><label for="field-3" class="control-label">Want to Rebrand?</label><br/><input type="checkbox" name="rebrand[]" id="rebrand'+i+'" value="1" onchange="check_for_rebrand_appl('+i+');"></div></div><div class="col-md-2"><div class="form-group"><label for="field-3" class="control-label">Product</label><span id="error_rack_location" style="color:red;">*</span><select class="form-control mand products" name="product[]" id="product' + i + '" required onchange="getprice(' + i + ',this.value);getdiscount(' + i + ',this.value); getunit('+i+',this.value); getbatchcode('+i+',this.value); getstock1('+i+'); match_stock_availability1('+i+');check_for_rebrand('+i+')"><option value="">Select</option></select><span id="recommendation'+i+'"></span></div><div class="form-group" id="rebrand_prd_div'+i+'" style="display:none;"><label for="field-3" class="control-label">Rebrand Product <span  style="color:red;">*</span></label><select name="rebrand_prd[]" id="rebrand_prd'+i+'" class="form-control"></select></div></div><div class="col-md-2" id="batch_code_div'+i+'" style="display:none;"><div class="form-group"><label for="field-3" class="control-label">Batch Code</label><select class="form-control  mand select2" name="batch_code[]" id="batch_code'+i+'"><option value="">Select Batch Code</option></select></div></div><div class="col-md-1"><div class="form-group"><label for="field-2" class="control-label">Stock Avail.</label><span id="error_rack_location" style="color:red;">*</span><input type="text" readonly class="form-control"  id="stock'+i+'" value=""></div></div><div class="col-md-1"><div class="form-group"><label for="field-2" class="control-label">Qty</label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control mand" name="qty[]" id="qty' + i + '" oninput="allow_decimal("qty'+i+'");" onkeyup="match_stock_availability1('+i+');"></div></div><div class="col-md-1"><div class="form-group"><label for="field-2" class="control-label">Unit</label><span id="error_rack_location" style="color:red;">*</span><input type="text" name="unit_name[]" id="unit_name'+i+'" class="form-control" readonly><input type="hidden" name="pack_size[]" id="pack_size'+i+'"><input type="hidden" name="pack_size_text[]" id="pack_size_text'+i+'"></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Order Price/<span class="list_price_unit'+i+'"></span></label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control mand" name="listprice[]" id="listprice' + i + '" oninput="allow_decimal("listprice' + i + '");"  onblur="getnetamt(' + i + ')" required><p id="discountpriceshow'+i+'" style="display: none;"></p><input type="hidden" name="discountpricehide[]" id="discountpricehide' + i + '" ></div></div><div class="col-md-2" style="display:none;"><div class="form-group"><label for="field-2" class="control-label">Discount Price Per <span class="list_price_unit'+i+'"></span></label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control" value="0" name="discountprice[]" id="discountprice' + i + '" value="0" onblur="getnetamt(' + i + ')" oninput="allow_decimal("discountprice' + i + '");"></div></div><div class="col-md-2" style="display:none;"><div class="form-group"><label for="field-2" class="control-label">Net Price</label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control" name="netprice[]" id="netprice' + i + '" value="0" readonly> </div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:23px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="' + i + '"><i class="fa fa-close"></i></button></div></div></div><br/>');


                getproductname(i);
                initializeSelect2("comp_product"+i);
                initializeSelect2_product("product"+i);
                initializeSelect2_batchcode("batch_code"+i);
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

        });

        function myfunction(id) {
            var product = $(".uname" + id).val();
            $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Quotation/productspecifications",
                data: "product=" + product,

                success: function(data) {
                    $(".getname" + id).html(data);
                }
            });
        }
    </script>


    <script>
        function getCustomerDetails() {
            var custid = $('#customer').val();
             $('#paymentterms').attr('readonly',false);

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
                        if(arr[15]==0 || arr[15]=='')
                        {
                            $("#customer_codes").val('');
                            $("#customer_codes").attr('readonly',false);
                        }else
                        {
                            $("#customer_codes").val(arr[15]);  
                            $("#customer_codes").attr('readonly',true); 
                        }
                        
                        $("#paychange").css('display','none');
                        var payment=arr[16];
                        var credit_days=arr[17];
                        var alias=arr[18];
                        $("#alias").val(alias);

                        if(payment>0)
                        {
                        $('#paymentterms').val(credit_days);
                        $('#paymentterms').attr('readonly',true);
                        $('#payment_type option').prop('disabled',true);
                        $('#payment_type option[value='+payment+']').attr('selected','selected');
                        $('#payment_type option[value='+payment+']').prop('disabled',false);
                        $("#paychange").css('display','');
                        }

                        getcustomer_assigned_to(custid);
                        checkpdf();

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


        function getcustomer() {
            var custid = $('#company').val();
            //alert(custid);
                  $.ajax({
                    type: "post",
                    url: "<?php echo page_url; ?>Customer/getcustomerdata",
                    data: "custid=" + custid,
                    success: function(data) {
                        //alert(data);
                        $("#customer").html(data);
                    }
                });
        
        }

        function checkfreight() {
            $('#freight').css('display', 'none');
            $('#freight_amt').removeClass('mand');
            
            if($('input[name=check_freight]').is(':checked')) {
              $('#freight').css('display', '');
              $('#freight_amt').addClass('mand');
            }
        } 

        function getproduct() {
            var proid = $('#company').val();
            //alert(custid);
            if (proid != '') {
                $.ajax({
                    type: "post",
                    url: "<?php echo page_url; ?>Customer/getproductdataNew",
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
                    url: "<?php echo page_url; ?>Customer/getproductdataNew",
                    data: "proid=" + proid,
                    success: function(data) {
                        //alert(data);
                        $("#product" + i).html(data);
                    }
                });
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
                        $("#pack_size_text"+i).val(arr[2]);
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
                        $("#discountpriceshow" + i).text('Allowed Price: '+data);
                        $("#discountpriceshow" + i).css('color', 'red');
                        $("#discountpriceshow" + i).css('font-weight', 'bold');
                    }
                });
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

            if (parseFloat(listprice) < parseFloat(discountpricehide)) {
                if (confirm('List Price is less than ' + discountpricehide + '. Are you sure you want to continue with this price?')) {

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

        function allow_decimal(data) {
            var self = $("#" + data);
            self.val(self.val().replace(/[^0-9\.]/g, ''));
            if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
                evt.preventDefault();
            }


        }


        function validate_form() {
            var isValid = 0;
            $(".mand").each(function() {
                var element = $(this).val();
                if (element == "") {

                    isValid = 1;
                }
            });


            if (isValid == 1) 
            {
                // alert($(this));
                alert('All Fields marked (*) are mandatory');
                return false;
            }
        }

        function check_tnc() {
            $(".bulk_tnc").css('display', 'none');
            $(".drums_tnc").css('display', 'none'); 
            // alert($('input[name=chk_tnc]:checked').val());
            if ($('input[name=chk_tnc]:checked').val() == 1) {
                $(".bulk_tnc").css('display', '');
            } else if ($('input[name=chk_tnc]:checked').val() == 2) {
                $(".drums_tnc").css('display', ''); 
            }
        }
    </script>

    <script language="javascript" type="text/javascript">
        $(document).ready(function() {
            $('.select3').select2();
            $('.select4').select2({
                tags:true
            });
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


        function getourproductname(id)
      {
        var comproduct=$("#comp_product"+id).val();

        if(comproduct!='')
        {

            $.ajax({
            type:"post",
            url:"<?php echo page_url;?>Open_leads/getrecommendations_our_product",
            data:"comproduct="+comproduct,
            success:function(data){
                 $("#recommendation"+id).css('font-size','12px');
            $("#recommendation"+id).css('color','red');
            $("#recommendation"+id).css('font-weight','bold');
            $("#recommendation"+id).html(data);
            }
            });

        }

      }

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

    var url1 = "<?php echo page_url;?>Customer/getHpclCompanies";

            $('#mul_company').select2({ 
                    placeholder: 'TYPE TO SELECT',                    
                    minmumInputLength:4,
                    allowClear: true,
                    multiple: true,
        
                        ajax: {
                          url: url1,
                          dataType: 'json',
                          delay: 250,

                          processResults: function (data) {
                            return {
                              results: data
                            };

                          },

                    cache: true
                }

            });

// var url1 = "<?php echo page_url;?>Customer/getHpclCompanies";
//     $('#comp_product0').select2({ 
//         placeholder: 'TYPE TO SELECT',
//         minmumInputLength:4,
//         allowClear: true,
//         tags:true,
//         ajax: {
//         url: purl,
//         dataType: 'json',
//         delay: 250,
//         data: function (params) {

//         return {
//         searchTerm: params.term
//         };

//         },
//         processResults: function (data) {
//         return {
//         results: data
//         };
// },
// cache: true

//         }
// });



$('#product0').select2({ });


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

       function initializeSelect2_batchcode(selectElementObj) {
   

            $('#'+selectElementObj).select2({});
         
      }




    function validate_gst() {
        var gstinVal = $('.gst_no').val();
        var reggst = /^([0-9]){2}([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}([0-9]){1}([a-zA-Z]){1}([a-zA-Z0-9]){1}?$/;
            if(!reggst.test(gstinVal) && gstinVal!=''){
                    alert('GST Identification Number is not valid. It should be in this "11AAAAA1111Z1A1" format');
                    $('#gst').val('');
            }
    }

        // function validate_batch_code(i) {
        //    var batch_code = $('#batch_code'+i).val();
        //    var reggst = /^([a-zA-Z]){1}([0-9]){1}-\s*([0-9]){2}-\s*([a-zA-Z]){1}-\s*([0-9]){3}?$/;

        //     if(!reggst.test(batch_code) && batch_code!=''){
        //             alert('Batch Code Number is not valid.');
        //             $('#batch_code'+i).val('');
        //     }
        // }

        function check_shipping_pincode() {
              var shipping_pincode = $('#shipping_pincode').val();
              var zipRegex = /^\d{6}$/;

              if(shipping_pincode!='')
              {
              if (!zipRegex.test(shipping_pincode))
              {
                  alert('Invalid Pincode!');
                  $('#shipping_pincode').val('');
              }
            }
        }

        function check_shipping_email() {
          var shipping_email = $('#shipping_email').val();
          var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if(shipping_email!='')
            {
            if (!regex.test(shipping_email)) {
              alert('Invalid Email ID!');
              $('#shipping_email').val('');
            }
            }
        }

        function check_shipping_mobile() {
          var shipping_mobile_no = $('#shipping_mobile_no').val();
          var regex = /^[7-9][0-9]{9}$/;
            if(shipping_mobile_no!='')
            {
            if (!regex.test(shipping_mobile_no)) {
              alert('Invalid Mobile No!');
              $('#shipping_mobile_no').val('');
            }
            }
        }

        function check_billing_pincode() {
              var billing_pincode = $('#billing_pincode').val();
              var zipRegex = /^\d{6}$/;
              if(billing_pincode!='')
              {
              if (!zipRegex.test(billing_pincode))
              {
                  alert('Invalid Pincode!');
                  $('#billing_pincode').val('');
              }
          }
        }

        function check_billing_email() {
          var billing_email = $('#billing_email').val();
          // alert(billing_email);
          if(billing_email != '') {
              var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                
                if (!regex.test(billing_email)) {
                  alert('Invalid Email ID!');
                  $('#billing_email').val('');
                }
          }
        }

        function check_billing_mobile() {
          var billing_mobile_no = $('#billing_mobile_no').val();
          var regex = /^[7-9][0-9]{9}$/;
            
            if(billing_mobile_no!='')
            {
            if (!regex.test(billing_mobile_no)) {
              alert('Invalid Mobile No!');
              $('#billing_mobile_no').val('');
            }
            }
        }

         function check_pan() {
          var pan_no = $('#pan_no').val();
          var regex = /[a-zA-z]{5}\d{4}[a-zA-Z]{1}/;
            if(pan_no!='')
            {
            if (!regex.test(pan_no)) {
              alert('Invalid PAN No!');
              $('#pan_no').val('');
            }
            }
        }


        function submit_data()
        {
            $("#save").attr('disabled',true);
            $("#save").val('Please wait');
           var company=$("#clientcompany").val();
           var companyname=$("#cust_companyname").val();
           var gst=$("#gst").val();
           var msme_number=$("#msme_number").val();
           var title=$("#title").val();
           var contact_person=$("#contact_person").val();
           var mobile=$("#mobile").val();
           var email=$("#email").val();
           var address=$("#address").val();
          var state=$("#state").val();
          var cityname=$("#cityname").val();
          var pincode=$("#pincode").val();
        var customer_code=$("#customer_code").val();
        var order_max_limit=$("#order_max_limit").val();
        

          if(company!='' && companyname!='' && gst!='' && title!='' && contact_person!='' && mobile!='' && email!='' && address !='' && state !='' && cityname!='' && pincode!='' && customer_code!='' && order_max_limit!='')
          {

        
             $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Customer/add_customer_vis_ajax",

                data: { hpclcompany: companyname,
                        client_company: company,
                        gst: gst,
                        title: title,
                        contact_person: contact_person,
                        mobile: mobile,
                        email: email,
                        address: address,
                        state: state,
                        cityname: cityname,
                        pincode: pincode,
                        msme_number: msme_number,
                        order_max_limit:order_max_limit,
                        customer_code:customer_code
                      },
                success: function(data) {

                if(data>0)
                {
                     $("#save").attr('disabled',false);
                    $("#save").val('Submit');
                    getcustomerdata(data,companyname);

                    $("#con-close-modal").modal('hide');

                }else
                {
                    $("#save").attr('disabled',false);
                    $("#save").val('Submit');
                    alert('Customer Data Insertion Failed');
                    return false
                }
                
                }
                });



          }else
          {
            alert('Please fill all mandatory Fields');
            return false;
          }

        }


        function getcustomerdata(custid,companyname)
        {

            $.ajax({
                    type: "post",
                    url: "<?php echo page_url; ?>Customer/getcustomerdata",
                    data: "custid=",
                    success: function(data) {
                      
                        $("#customer").html(data);

                        /** MAKE SELECTED BY VALUE **/

                       $('#customer option[value='+custid+']').attr('selected','selected');

                        getCustomerDetails();
                    }
                });

        }

    function checkIfDuplicateNoExists() {
        var company=$('#clientcompany').val();
        var company_name=$('#clientcompany option:selected').text();
        var mobile=$('#mobile').val();

        $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Customer/checkIfDuplicateNoExists",
                data: { mobile: mobile,
                        company: company
                      },
                    success: function(data) {
                        if(data == 1) {
                            alert('Duplicate Contact No. of '+company_name+' company found.');
                            $("#mobile").val('');
                        }
                    }
            });
    }


    //      function validate_gst() {
    //     var gstinVal = $('#gst').val();
    //     if(gstinVal!='')
    //     {
    //     var reggst = /^([0-9]){2}([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}([0-9]){1}([a-zA-Z]){1}([0-9]){1}?$/;
    //         if(!reggst.test(gstinVal) && gstinVal!=''){
    //                 alert('GST Identification Number is not valid. It should be in this "11AAAAA1111Z1A1" format');
    //                 $('#gst').val('');
    //         }
    //     }
    // }

    function payment_change_req(){
        var customer=$("#customer").val();
        $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Billing/get_customer_detail_with_payment",
                data: "customer=" + customer,
                success: function(data) {
                var d=data.split("|");
                $("#cust_req").val(customer); 
                $("#rcust").val(d[1]);
                $("#cpayment").val(d[0]);
                $("#payment_change").modal('show');
                }
                });

       



    }


    function checkpdf_req()
    {
   
    var payment_type=$("#payment_type_r").val();
    if(payment_type==4 || payment_type==5)
    {
   // $("#paymentterms_req").addClass('mand');
    $("#credit_days_req").css('display','');
    }else
    {
    //$("#paymentterms_req").removeClass('mand');
    $("#credit_days_req").css('display','none');
    }

    
    }


    function submit_payment_request()
    {
        var customer= $("#cust_req").val();
        var ptype=  $("#payment_type_r").val();
        if(ptype==4 || ptype==5)
        {
        var credit=$("#paymentterms_req").val();
        }else
        {
        var credit=0;
        }

        if(customer!='' && ptype!='')
        {
         $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Billing/raise_payment_change_request",
                data: "customer=" + customer+"&type="+ptype+"&credit_days="+credit,
                success: function(data) {
                    alert('Request has been raised. Please wait for approval');
                    $("#payment_change").modal('hide');

               
                }
                });
        }else
        {
            alert('Please Fill all the data');
        }




    }

          function get_customer(compid) {
            
               var purl = "<?php echo page_url;?>Customer/get_customer_by_company";
             $(".select3").select2({
              placeholder: 'TYPE TO SELECT',
              minmumInputLength: 4,
              allowClear: true,
              ajax: {
                url: purl,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                  return {
                    searchTerm: params.term,
                    compid: compid,
                  };

                },
                processResults: function(data) {
                  return {
                    results: data
                  };
                },
                cache: true

              }
            });    


        }

         function getcustomer_assigned_to(custid)
        {

             $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Customer/getassigned_user",
                data: "custid=" + custid,
                success: function(data) {
                    $("#agent").html(data);
                
                }
                });

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
      
                  $("#tmobile_no").val(mobile_no);
                  $("#taddress").val(address);
      
      
              }
          });
      }

      function allow_decimal(data) {
      
          var self = $("#" + data);
          self.val(self.val().replace(/[^0-9\.]/g, ''));
          if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
              evt.preventDefault();
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

                    $("#stock"+flag).val(data);
                    $("#save").attr('disabled',false);
                    
                }
                });

        }

        function match_stock_availability1(flag)
        {
           var product=$("#product"+flag).val();
           var stock_avail=$("#stock"+flag).val();
           if(stock_avail!=''){ var stock_avail=stock_avail}else{ var stock_avail=0;}
            var qty_edit=$("#qty"+flag).val();
            if(qty_edit!=''){ var qty_edit=qty_edit}else{ var qty_edit=0;}

             if(parseFloat(qty_edit)>parseFloat(stock_avail))
            {
                alert('Available Stock is less than inputted Qty. Please change the Qty');
                $("#qty"+flag).val('');
            }

        }

        function resetform(selectedvalue)
        {
            $("#quotation")[0].reset();

            $("#company").val(selectedvalue);
            getproduct();
            displayproduct(selectedvalue);
            get_tnc(selectedvalue);
            get_customer(selectedvalue);


        }

        function check_for_rebrand(flag)
        {
            $("#rebrand_appl"+flag).css('display','none'); 
            $("#rebrand_prd_div"+flag).css('display','none'); 
            $("#rebrand_prd"+flag).removeClass('mand'); 
            $("#rebrand_prd"+flag).attr('required',false); 
             $("#rebrand_prd"+flag).html('');
             $('#rebrand'+flag).prop('checked',false);
             $("#rebrand_used"+flag).val(0);

            var prd=$("#product"+flag).val();

            if(prd!='')
            {
                $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Customer/check_for_rebrand",
                data: "prd=" + prd,
                success: function(data) {
                    if(data!='')
                    {
                   $("#rebrand_appl"+flag).css('display','');   
                   $("#rebrand_prd"+flag).html(data); 
                   $("#rebrand_used"+flag).val(1);
                   }  
                   
                
                }
                }); 

            }

        }


        function check_for_rebrand_appl(flag)
        {

           
            if($('#rebrand' + flag).is(":checked"))
            {
                $("#rebrand_prd_div"+flag).css('display',''); 
                $("#rebrand_prd"+flag).addClass('mand'); 
                $("#rebrand_prd"+flag).attr('required',true); 
            }else
            {
                $("#rebrand_prd_div"+flag).css('display','none'); 
                $("#rebrand_prd"+flag).removeClass('mand'); 
                $("#rebrand_prd"+flag).attr('required',false); 
            }


        }
       
    </script>
</body>

</html>