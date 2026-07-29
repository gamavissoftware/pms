<?php 
  $CI =& get_instance();
  $CI->load->model('Salescrm_model', 'salescrm');
  $getAllUnits = $CI->salescrm->getAllUnits();
  $getAllProducts = $CI->salescrm->getAllProducts();
  $getHpclLocations = $CI->salescrm->getHpclLocations();
  $getLastInsertedCode = $CI->salescrm->getLastInsertedCode_TYPE_2();
$interest=$CI->salescrm->get_interest_slab();
$gst_slab=$CI->salescrm->get_gst_slab();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="NJ Media">

    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

    <title><?php echo sitetitle; ?> Edit Approval Form</title>

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
  
  .select2 {
  width: 100% !important;
}


    .select2-container .select2-selection--single
    {
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
                    <h4 class="page-title">Edit Type II/III Invoice Upload Form</h4>
                </div>
            </div>
        </div>

        <?php 
            $q = $this->db->select('*')->from('type_2_3_invoice')->where('id',$this->uri->segment(3))->get();
            foreach($q->result() as $rows);
        ?>
            <!-- end page title end breadcrumb -->
        <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
        <form id="quotation" method="post" action="<?php echo page_url; ?>Approval/update_save_invoice_type_2_3/<?php echo $this->uri->segment(3);?>" autocomplete="off" onsubmit="return validate();" enctype="multipart/form-data">
            <input type="hidden" name="interest" value="<?php echo $interest;?>">
            <input type="hidden" name="gst" value="<?php echo $gst_slab;?>">
            <input type="hidden"  name="oldinvoice" value="<?php echo $rows->invoice;?>">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="col-md-12" style="border-bottom:1px dashed #000;margin-bottom: 30px;">
                                    <h5 class="page-title" style="line-height:32px;font-size:15px;">Invoice Details</h3>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">TYPE<span style="color:red;">*</span></label>
                                       <select name="type" id="type" required class="form-control mand">
                                        <option value="">Select</option>
                                        <option value="TYPE II" <?php if($rows->type=='TYPE II'){echo "selected";}?>>TYPE II</option>
                                        <option value="TYPE III" <?php if($rows->type=='TYPE III'){echo "selected";}?>>TYPE III</option>
                                       </select>
                                    </div>
                                </div>


                             
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Invoice Date <span style="color:red;">*</span></label>
                                        <input type="date" class="form-control mand" name="invoice_date" value="<?php echo $rows->invoice_date;?>" required>
                                    </div>
                                </div>

                                  <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Invoice Number <span style="color:red;">*</span></label>
                                        <input type="text" class="form-control mand" name="invoice_number" value="<?php echo $rows->invoice_no;?>" required>
                                    </div>
                                </div>

                                  <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Invoice Attachment </label>
                                        <input type="file" class="form-control" name="attach" value="">
                                    </div>
                                </div>


                                 <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Shipping Location <span style="color:red;">*</span></label>
                                          <select class="form-control mand" name="hpcl_location" id="hpcl_location0" required>
                                            <option value="">SELECT</option>
                                            <?php if($getHpclLocations != '') {
                                                    foreach($getHpclLocations as $row) {?>
                                            <option value="<?php echo $row->id;?>" <?php if($rows->shipping_from==$row->id){echo "selected";}?> ><?php echo $row->name;?></option>
                                            <?php } } ?>
                                        </select>
                                    </div>
                                </div>
                              
                                <div style="clear:both;height: 30px;"></div>

                                <div class="col-md-12" style="border-bottom:1px dashed #000;margin-bottom: 30px;">
                                    <h5 class="page-title" style="line-height:32px;font-size:15px;">Customer Details</h3>
                                </div>

                              <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Customer Name <span style="color:red;">*</span></label><br>
                                        <select name="customer_name" id="customer_name" class="form-control mand" required onchange="get_customer_code();">
                                            <?php 
                                            $r=$this->db->select('id,customer_name, customer_code')->from('hpcl_direct_customer')->where('id',$rows->customer)->get();
                                           
                                            foreach($r->result() as $rr);
                                          
                                            ?>
                                            <option value="<?php echo $rr->id;?>"><?php echo $rr->customer_name;?></option>
                                            
                                        </select>
                                    </div>
                                </div>
                                <script type="text/javascript">
                                    function get_customer_code()
                                    {
                                        var customer=$("#customer_name").val();
                                            $.ajax({
                                            type: "post",
                                            url: "<?php echo page_url;?>Approval/get_customer_code",
                                            data: "customer="+customer,
                                            success: function(data) {
                                                var d=data.split("|");
                                                if(d[0]!='')
                                                {
                                                $("#customer_code").val(d[0]);
                                                $("#customer_code").attr('readonly',true);
                                                }else
                                                {
                                                $("#customer_code").val(0);
                                                $("#customer_code").attr('readonly',true);
                                                }

                                                if(d[1]!='' && d[1]>0)
                                                {
                                                $("#customer_tds").val(d[1]);
                                                $("#customer_tds").attr('readonly',true);
                                                }else
                                                {
                                                $("#customer_tds").val(0);
                                                $("#customer_tds").attr('readonly',true);
                                                }
                                                if(d[5]!='' && d[5]>0)
                                                {
                                                $("#customer_tcs").val(d[5]);
                                                $("#customer_tcs").attr('readonly',true);
                                                }else
                                                {
                                                $("#customer_tcs").val(0);
                                                $("#customer_tcs").attr('readonly',true);
                                                }
                                            }
                                            });
                                    }
                                </script>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Customer Code <span style="color:red;">*</span></label><br>
                                        <input type="text" name="customer_code" id="customer_code" value="<?php echo $rr->customer_code;?>" class="form-control mand" readonly required>
                                           
                                    </div>
                                </div>

                                <div class="col-md-2">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Customer TDS <span style="color:red;">*</span></label><br>
                                <input type="text" name="customer_tds" id="customer_tds" class="form-control mand" value="<?php echo $rows->tds;?>" required readonly>
                                </div>
                                </div>

                                <div class="col-md-2">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Customer TCS <span style="color:red;">*</span></label><br>
                                <input type="text" name="customer_tcs" id="customer_tcs" class="form-control mand" value="<?php echo $rows->tcs;?>" required readonly>

                                </div>
                                </div>

                              
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">

                              

     <div class="col-md-12" style="border-bottom:1px dashed #000;margin-bottom: 30px;">
                                    <h5 class="page-title" style="line-height:32px;font-size:15px;">Product Details</h3>
                                </div>

                                <?php 
                                    $qq = $this->db->select('id, invoice_id, product_id, unit, price, qty, payment')->from('type_2_3_invoice_particular')->where('invoice_id',$this->uri->segment(3))->get();
                                    foreach($qq->result() as $rowss){
                                ?>
                                <div class="row">
                                <div class="col-md-4">
                                    <input type="hidden"  name="productrecordid[]" value="<?php echo $rowss->id;?>">
                                    <div class="form-group">
                                        <label for="field-3" class="control-label">Product</label>
                                        <span style="color:red;">*</span>
                                        <select class="form-control mand" name="product[]" id="product" required>
                                            <?php 
                                            $q1 = $this->db->select('id, instruments_name, pack_size')->from('presto_instruments')->where('id',$rowss->product_id)->get();
                                            foreach($q1->result() as $product) { ?>
                                                        <option value="<?php echo $product->id;?>"><?php echo $product->instruments_name;?>-<?php echo $product->pack_size;?></option>
                                                   <?php }?>
                                               
                                        </select>
                                    </div>
                                </div>

                                   <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-3" class="control-label">Unit</label>
                                        <span style="color:red;">*</span>
                                        <select class="form-control mand" name="unit[]" id="unit" required>
                                            <?php 
                                                    $q2 = $this->db->select('id, name')->from('units')->where('id',$rowss->unit)->get();
                                                    foreach($q2->result() as $unit);
                                            ?>
                                        <option value="<?php echo $unit->id;?>"><?php echo $unit->name;?></option>
                                           
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Billing Price</label>
                                        <span style="color:red;">*</span>
                                        <input type="text" class="form-control mand" name="approved_price[]" id="approved_price" autocomplete="off" value="<?php echo $rowss->price;?>" required>
                                    </div>
                                </div>

                                  <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Shipped Qty</label>
                                        <span style="color:red;">*</span>
                                        <input type="text" class="form-control mand" value="<?php echo $rowss->qty;?>" name="qty[]" id="qty" autocomplete="off" required >
                                    </div>
                                </div>
                                       
                   
                                <div class="col-md-1">
                                    <div class="form-group" style="margin-top:25px">
                                        <a href="<?php echo page_url;?>Approval/deleteproductinfo/<?php echo $rowss->id;?>/<?php echo $this->uri->segment(3);?>"><button type="button" class="btn btn-warning"><i class="fa fa-trash"></i></button></a>
                                    </div>
                                </div>
                            </div>
                       

                    <?php }?>

                        <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="field-3" class="control-label">Product</label>
                                        <span style="color:red;"></span>
                                         
                                        <select class="form-control select30" name="product[]" id="product0"  onchange="add_instruments(); checkbulkandunit(0);">
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
                                        <span style="color:red;"></span>
                                        <select class="form-control" name="unit[]" id="unit0" onchange="">
                                            <option value="">Select</option>
                                           
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Billing Price</label>
                                        <span style="color:red;"></span>
                                        <input type="text" class="form-control" name="approved_price[]" id="approved_price0" autocomplete="off" onkeyup="allow_decimal('approved_price0');">
                                    </div>
                                </div>

                                  <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Shipped Qty</label>
                                        <span style="color:red;"></span>
                                        <input type="text" class="form-control" name="qty[]" id="qty0" autocomplete="off" onkeyup="allow_decimal('qty0');">
                                    </div>
                                </div>
                              
<!-- 
                                 <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Transportation<span style="color:red;">*</span></label>
                                        <select class="form-control mand" name="transportation[]" id="transportation0" onchange="check_transport_rate(0);">
                                            <option value="">Select</option>
                                            <option value="1">EXMI</option>
                                            <option value="2">DELIVERED</option>
                                        </select>
                                    </div>
                                </div>
 -->
                                
                          


                                <div class="col-md-1">
                                    <div class="form-group" style="margin-top:25px">
                                        <button type="button" class="btn btn-warning" name="add" id="addmore_btn"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="dynamictasks"></div>


                         <div class="col-md-12" style="border-bottom:1px dashed #000;margin-bottom: 30px;">
                                    <h5 class="page-title" style="line-height:32px;font-size:15px;">Transportation Details</h3>
                                </div>

                                <div class="col-md-2">
                                <div class="form-group">
                                <label for="field-3" class="control-label">Transportation</label>
                                <span style="color:red;">*</span>
                                <select class="form-control mand" name="transportation" id="transportation" required onchange="check_transport();" >
                                <option value="">Select</option>
                                <option value="1" <?php if($rows->transportation==1){echo "selected";}?>>Our</option>
                                <option value="2" <?php if($rows->transportation==2){echo "selected";}?>>Customer</option>

                                </select>
                                </div>
                                </div>
                                <script>
                                    function check_transport()
                                    {

                                         $("#ttype").css('display','none');
                                        $("#trns_type").removeClass('mand');
                                        $("#trns_type").attr('required',false);

                                       var trns=$("#transportation").val();
                                       if(trns==1)
                                       {
                                        $("#ttype").css('display','');
                                        $("#trns_type").addClass('mand');
                                        $("#trns_type").attr('required',true);
                                       }
                                    }
                                </script>


                                <div id="ttype" style="display:none;">

                                <div class="col-md-2">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Transport Type</label>
                                <span style="color:red;">*</span>
                                    <select class="form-control" name="trns_type" id="trns_type" required onchange="check_transport_type();" >
                                    <option value="">Select</option>
                                    <option value="1" <?php if($rows->transport_type==1){echo "selected";}?>>Our Vehicle</option>
                                    <option value="2" <?php if($rows->transport_type==2){echo "selected";}?>>Hired Vehicle</option>
                                    </select>
                                </div>
                                </div>
                                <script type="text/javascript">
                                    function check_transport_type()
                                    {   
                                        var trns_type=$("#trns_type").val();
                                        if(trns_type==1)
                                        {

                                        $(".our_vehicle").css('display','');
                                        $(".our_vehicle_no").attr('required',true);
                                        $(".our_vehicle_no").addClass('mand');
                                        $(".hired_vehicle").css('display','none');
                                        $("#transporter_id").attr('required',false);
                                        $("#trns_rate_type").attr('required',false);
                                        $("#trrate").attr('required',false);

                                        $("#transporter_id").removeClass('mand');
                                        $("#trns_rate_type").removeClass('mand');
                                        $("#trrate").removeClass('mand');
                                       
                                        }else if(trns_type==2)
                                        {

                                        $(".our_vehicle").css('display','none');
                                        $(".our_vehicle_no").attr('required',false);
                                        $(".our_vehicle_no").removeClass('mand');
                                         $(".hired_vehicle").css('display','');
                                        $("#transporter_id").attr('required',true);
                                        $("#trns_rate_type").attr('required',true);
                                        $("#trrate").attr('required',true);

                                        $("#transporter_id").addClass('mand');
                                        $("#trns_rate_type").addClass('mand');
                                        $("#trrate").addClass('mand');;

                                        }else
                                        {

                                        $(".our_vehicle").css('display','none');
                                        $(".our_vehicle_no").attr('required',false);
                                        $(".hired_vehicle").css('display','none');
                                        $("#transporter_id").attr('required',false);
                                        $("#trns_rate_type").attr('required',false);
                                        $("#trrate").attr('required',false);

                                        $(".our_vehicle_no").removeClass('mand');
                                        $("#transporter_id").removeClass('mand');
                                        $("#trns_rate_type").removeClass('mand');
                                        $("#trrate").removeClass('mand');

                                       

                                        }

                                    }
                                </script>



                    

                                  <div class="col-md-3 our_vehicle" style="display:none;">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Vehicle No.</label>
                                <span style="color:red;">*</span>
                                    <select class="form-control" name="our_vehicle_no" id="our_vehicle_no">
                                    <option value="">Select</option>
                                    <?php 
                                    $res=$this->db->select('id,name')->from('our_vehicles')->where('status',1)->get();
                                    if($res->num_rows()>0)
                                    {
                                    foreach($res->result() as $rowsss)
                                    {
                                    ?>
                                    <option value="<?php echo $rowsss->id;?>" <?php if($rows->vehicle_no==$rowsss->id){echo "selected";}?>><?php echo $rowsss->name;?></option>
                                    <?php
                                    }
                                    }
                                    ?>
                                    
                                    </select>
                                </div>
                                </div>

                                <!--- FOR HIRED -->

                                <div class="col-md-2 hired_vehicle" style="display:none;">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Select Transporter</label>
                                <span style="color:red;">*</span>
                                    <select class="form-control" name="transporter_id" id="transporter_id" onchange="getTransporterDetails()">
                                    <option value="">Select</option>
                                     <?php 
                                    $res=$this->db->select('id,name')->from('transporter_details')->get();
                                    if($res->num_rows()>0)
                                    {
                                    foreach($res->result() as $rowssss)
                                    {
                                    ?>
                                    <option value="<?php echo $rowssss->id;?>" <?php if($rows->transporter_id==$rowssss->id){echo "selected";}?>><?php echo $rowssss->name;?></option>
                                    <?php
                                    }
                                    }
                                    ?>
                                    
                                    </select>
                                </div>
                                </div>

                                <div class="col-md-3 hired_vehicle" style="display:none;">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Mobile No.<span style="color:red;">*</span></label>
                                <input type="number" maxlength="10" class="form-control" name="mobile_no" id="mobile_no" value="<?php echo $rows->mobile_no;?>" data-mask="(999) 999-9999">
                                </div>
                                </div>
                                <div class="col-md-3 hired_vehicle" style="display:none;">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Address</label>
                                <textarea class="form-control" name="address" id="address"><?php echo $rows->address;?></textarea>
                                </div>
                                </div>
                                <div class="col-md-4 hired_vehicle" style="display:none;">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Vehicle No.<span style="color:red;">*</span></label>
                                <input type="text" class="form-control" name="vehicle_no" value="<?php echo $rows->vehicle_no;?>" id="vehicle_no">
                                </div>
                                </div>


                                  <div class="col-md-2 hired_vehicle" style="display:none;">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Transporter Rate Type</label>
                                <span style="color:red;">*</span>
                                    <select class="form-control" name="trns_rate_type" id="trns_rate_type" >
                                    <option value="">Select</option>
                                    <option value="1" <?php if($rows->rate_type==1){echo "selected";}?>>Per Ltr</option>
                                    <option value="2" <?php if($rows->rate_type==2){echo "selected";}?>>Fixed</option>
                                    </select>
                                </div>
                                </div>

                                <div class="col-md-2 hired_vehicle"  style="display:none;">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Transporter Rate</label>
                                <span style="color:red;">*</span>
                                   <input type="number" step="0.01" name="trrate" class="form-control" value="<?php echo $rows->transport_rate;?>" id="trrate">
                                </div>
                                </div>



                            </div>

                               

                         
                          

                          



                    


                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-4"></div>
                                     <div class="col-md-4">
                                 <div class="form-group text-center">
                                    <input type="submit" id="saves" class="btn btn-success" style="width:100%" value="Submit">
                                </div>
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
            check_transport();
            check_transport_type();
            $('.select30').select2({}); 

            $('.select1').select2({ tags:true }); 

            var i = 1;
            $('#addmore_btn').click(function() {
                $('#dynamictasks').append('<hr><div id="row' + i + '" class="row" style="margin-top:5px"><div class="col-md-12"><div class="col-md-4"> <div class="form-group"> <label for="field-3" class="control-label">Product</label> <span style="color:red;">*</span><select class="form-control mand select3'+i+'" name="product[]" id="product'+i+'" required onchange="add_instruments();checkbulkandunit('+i+');"><option value="">Select</option> <?php if($getAllProducts !='') { foreach($getAllProducts as $row) { ?> <option value="<?php echo $row->id;?>"><?php echo $row->instruments_name;?>-<?php echo $row->pack_size;?></option> <?php }} ?> </select> </div></div><div class="col-md-2"><div class="form-group"><label for="field-3" class="control-label">Unit</label><span style="color:red;">*</span><select class="form-control mand" name="unit[]" id="unit'+i+'" required onchange=""><option value="">Select</option></select></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Billing Price</label><span style="color:red;">*</span><input type="text" class="form-control mand notext'+i+'1" name="approved_price[]" id="approved_price'+i+'" autocomplete="off" required onkeyup="checkappprice('+i+',1);"></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Shipped Qty</label><span style="color:red;">*</span><input type="text" class="form-control mand" name="qty[]" id="qty'+i+'" autocomplete="off" required onkeyup="initializedecimal("qty'+i+'");"></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:23px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="' + i + '"><i class="fa fa-close"></i></button></div></div></div><br/>');


                initializeSelect2(i);
            
            
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


             var url1 = "<?php echo page_url;?>Billing/getVehicles";

                $(".select3").select2({ 
                    placeholder: 'TYPE TO SELECT',                    
                    minmumInputLength:4,
                    allowClear: true,
        
        
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


function initializedecimal(data)
{
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

    function check_transport_rate(i)
    {
         $("#trnsrate"+i).css('display','none');
        $("#trate"+i).removeClass('mand');
        $("#trate"+i).attr('required',false);
       var trns= $("#transportation"+i).val();
       if(trns==2)
       {
        $("#trnsrate"+i).css('display','');
        $("#trate"+i).addClass('mand');
         $("#trate"+i).attr('required',true);
       }
    }


      $( document ).ready(function() {
    $("#our_vehicle_no").select2({});
    $("#transporter_id").select2({tags:true});
});


      function getTransporterDetails() {
          var transporter_id = $("#transporter_id").val();
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
    </script>
</body>

</html>