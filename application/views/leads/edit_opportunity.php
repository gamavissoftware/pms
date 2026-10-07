<?php 
$CI = &get_instance();
$CI->load->model('Lead_model');
$lead_details=$CI->Lead_model->getLeadDetailsNew($this->uri->segment(3));
// echo "<pre>"; print_r($lead_details); exit;
if(count($lead_details)>0)
{
    foreach($lead_details as $row);
    $create_date=$row->create_date;
    $unique_id=$row->unique_id;
    $customer_id=$row->customer_id;
    $postal_address=$row->postal_address;
    $company_name=$row->company_name;
    $machine_type=$row->machine_type;
    $country_id=$row->country;
    $lead_source_id=$row->lead_source_id;
    $patient_type_id=$row->patient_type_id;
    $added_by=$row->added_by;
    $remarks=$row->remarks;
    $customise_remarks=$row->customise_remarks;
    $email_id=$row->email;
    $customer_contact=$row->customer_contact;
    $merchant_export=$row->merchantexport;
    $customer_type=$row->customer_type;
    $probability=$row->probability;
    $customer_name=$row->customer_name;
    $product_details=$CI->Lead_model->getProductDetails($this->uri->segment(3));
    if(count($product_details)>0)
    {
        $product_name=$product_details[0];
        $qty=$product_details[1];
        $lead_product_id=$product_details[2];
        $product_id=$product_details[3];
    }
}
//echo "<pre>"; print_r($lead_details); exit;
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright;?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle;?></title>

        <!-- Table Responsive css -->
        <script src="<?php echo assets_url;?>js/angular.min.js"></script>
         <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
    <link href="<?php echo assets_url; ?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
<?PHP 
$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
foreach($q->result() as $LOGO);
?>
        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
<style>
            table.pretty thead th {
                text-align: center;
                background: <?php echo $LOGO->colorcode;?>;
                color:#fff;
                font-size:12px;
            }
            table.pretty td {
                text-align: center;
                font-size:12px;
            }
            </style>
    </head>


    <body>


        <!-- Navigation Bar-->
        <header id="topnav">
          <?php $this->load->view('common/nav-menu');?>
        </header>
        <!-- End Navigation Bar-->
<div class="wrapper">
            <div class="container-fluid">
            <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
                
                           <h4 class="page-title text-center">Edit Existing Opportunity</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>                 
                    <div class="card-box table-responsive">
                        <form name="frm" action="<?php echo page_url;?>Leads/update_opportunity/<?php echo $this->uri->segment(3);?>" method="post">
                            <input type="hidden" name="product_detail_id" value="<?php echo $lead_product_id;?>">
                            <input type="hidden" name="customer_id" value="<?php echo $customer_id;?>">
                    <div class="row" style="border:1px dotted #000;">
                    <div class="col-sm-12">
                        <div class="col-md-12" style="margin-bottom:20px;"><h4 style="font-weight:bold;font-size:20px;"><i>Opportunity Details</i></h4></div>

                          <div class="col-md-2">
                            <div class="form-group">
                                <label>Opportunity Date<span style="color:red">*</span></label>
                                <input type="date" name="op_date" id="op_date" class="form-control" value="<?php echo set_value('op_date');?><?php echo $create_date;?>">
                                <span style="color:red;"><?php echo form_error('op_date');?></span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Source<span style="color:red">*</span></label>
                                <select name="lsource" id="lsource" class="form-control">
                                    <option value="">Select</option>
                                    <?php 
                                    $rest=$this->db->select('source_id,lead_source')->from('lead_source')->where('status',1)->get();
                                    if($rest->num_rows()>0)
                                    {
                                        foreach($rest->result() as $row)
                                        {
                                            ?>
                                            <option value="<?php echo $row->source_id;?>" <?php if(set_value('lsource')== $row->source_id){?> selected <?php } ?> <?php if($lead_source_id== $row->source_id){?> selected <?php } ?>><?php echo ucwords(strtolower($row->lead_source));?></option>
                                        <?php } } ?>
                                </select>
                                <span style="color:red;"><?php echo form_error('lsource');?></span>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Opportunity Type <span style="color:red">*</span></label>
                                <select name="op_type" id="op_type" class="form-control" onchange="">
                                    <option value="1" <?php if(set_value('op_type')==1){?> selected <?php } ?><?php if($patient_type_id==1){?> selected <?php } ?>>Domestic</option>
                                    <option value="2" <?php if(set_value('op_type')==2){?> selected <?php } ?><?php if($patient_type_id==2){?> selected <?php } ?>>Export</option>
                                </select>
                                <span style="color:red;"><?php echo form_error('op_type');?></span>
                            </div>
                        </div>


                        <div class="col-md-2">
                        <div class="form-group">
                        <label>Machine Type <span style="color:red">*</span></label>
                        <select name="mach_type" id="mach_type" class="form-control">
                        <option value="">Select</option>
                        <option value="1" <?php if(set_value('mach_type')==1){?> selected <?php } ?><?php if($machine_type==1){?> selected <?php } ?>>Liquid</option>
                        <option value="2" <?php if(set_value('mach_type')==2){?> selected <?php } ?><?php if($machine_type==2){?> selected <?php } ?>>Powder</option>
                         <option value="3" <?php if(set_value('mach_type')==3){?> selected <?php } ?><?php if($machine_type==3){?> selected <?php } ?>>Customise</option>
                        </select>
                          <span style="color:red;"><?php echo form_error('mach_type');?></span>
                        </div>
                        </div>

                         <div class="col-md-2">
                            <div class="form-group">
                                <label>Opportunity Number<span style="color:red">*</span></label>
                                <input type="text" name="op_no" id="op_no" class="form-control" readonly  value="<?php echo $unique_id;?>">
                                <input type="hidden" name="op_increment_no" id="op_increment_no">
                                <span style="color:red;">
                                    <?php echo form_error('op_no');?></span>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Marketing Person<span style="color:red">*</span></label>
                                <select type="text" name="marketing" id="marketing" class="form-control">
                                    <option value="">Select</option>
                                    <?php
                                    $this->db->select('user_id,first_name,last_name')->from('system_users')->where('marketing_person',1)->where('business_location',2)->where('user_status',1);
                                    // if($_SESSION['logged_in']['role']<>12)
                                    // {
                                    // $this->db->where('user_id',$_SESSION['logged_in']['user_id']);
                                    // }

                                    $rest=$this->db->order_by('first_name')->get();
                                    if($rest->num_rows()>0)
                                    {
                                        foreach($rest->result() as $row)
                                        {
                                    ?>
                                    <option value="<?php echo $row->user_id;?>" <?php if(set_value('marketing')==$row->user_id){?> selected <?php } ?><?php if($added_by==$row->user_id){?> selected <?php } ?>><?php echo ucwords(strtolower($row->first_name));?> <?php echo ucwords(strtolower($row->last_name));?>
                                    <?php } } ?>
                                </select>
                                <span style="color:red;"><?php echo form_error('marketing');?></span>
                            </div>
                        </div>

                        <?php 
                        if($machine_type==3)
                        {
                            $remarks=$customise_remarks;
                        }else
                        {
                            $remarks=$remarks;
                        }
                        ?>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Remarks <span id="requiredsymbol"></span></label>
                                <textarea class="form-control" name="remarks" id="remarks"><?php echo $remarks;?></textarea>
                            </div>
                        </div>
                        
                              
                        </div>
                    </div>

                    <div class="row" style="border:1px dotted #000;">
                    <div class="col-sm-12">
                        <div class="col-md-12" style="margin-bottom:20px;"><h4 style="font-weight:bold;font-size:20px;"><i>Customer Details</i>&nbsp;&nbsp;<a href='javascript:;' data-toggle="modal" data-target="#con-close-modal" class="btn btn-warning btn-xs"><i class="fa fa-plus"></i></a></h4></div>
                         <div class="col-md-3">
                            <div class="form-group">
                                <label>Customer <span style="color:red">*</span></label>
                                    <select class="form-control select3" name="customer" id="customer" onchange="getDetails();">
                                    <option value="">Select</option>
                                    </select>
                                     <span style="color:red;"><?php echo form_error('customer');?></span>
                            </div>

                               <div class="form-group">
                                <label>Customer Name <span style="color:red">*</span></label>
                                    <input type="text" class="form-control" name="cname" id="cname" class="form-control" value="<?php echo $customer_name;?>">
                            
                                     <span style="color:red;"><?php echo form_error('brand');?></span>
                            </div>

                            <div class="form-group">
                                <label>Brand <span style="color:red">*</span></label>
                                    <select class="form-control select4" name="brand" id="brand">
                                        <option value="">Select</option>
                                    </select>
                                     <span style="color:red;"><?php echo form_error('brand');?></span>
                            </div>
                        </div>

                       

                         <div class="col-md-6">
                            <div class="form-group">
                                <label>Customer Address<span style="color:red">*</span></label>
                                <textarea name="address" id="address" class="form-control"><?php echo $postal_address;?></textarea>
                                <span style="color:red;"><?php echo form_error('address');?></span>
                            </div>
                        </div>

                        <div class="col-md-3" id="gstdiv">
                             

                            <div class="form-group">
                                <label>Country<span id="gstrequiredsymbol" style="color:red;">*</span></label>
                                <select class="form-control" name="country" id="country" onchange="getcountrytax();" required>
                                    <option value="">Select</option>
                                    <?php 
                                        $q = $this->db->select('country_name, country_id')->from('countries')->where('country_status',1)->get();
                                        foreach($q->result() as $row){
                                    ?>
                                    <option value="<?php echo $row->country_id;?>" <?php if($country_id==$row->country_id){?> selected <?php } ?>><?php echo $row->country_name;?></option>
                                <?php }?>
                                </select>
                                <script type="text/javascript">
                                    function getcountrytax(){
                                        var countryid = $("#country").val();
                                        if(countryid!==''){
                                                $.ajax({
                                                type:"post",
                                                url:"<?php echo page_url;?>Leads/getcountrytaxinfo",
                                                data:"countryid="+countryid,
                                                success:function(data){
                                                    if(data){
                                                         $("#showhidetaxtype").css('display','block');
                                                        $("#taxt").html(data);
                                                    }else{
                                                        $("#showhidetaxtype").css('display','none');
                                                    }
                                                    
                                                }
                                                });
                                        }
                                    }

                                </script>
                            </div>
                            <div style="clear: both;height:5px"></div>
                            <div class="form-group" id="showhidetaxtype" style="display:none">
                                <label id="taxt"><span id="gstrequiredsymbol"></span></label>
                                <input type="text" name="gst" id="gst" class="form-control">  
                                    <!-- <span style="color:red;"><?php echo form_error('gst');?></span> -->
                            </div>
                        </div>
                        <div class="col-md-3">
                              <div class="form-group">
                                <label>Contact Number<span style="color:red">*</span></label>
                                <input type="number" name="customercontactno" id="customercontactno" value="<?php echo $customer_contact;?>" class="form-control">  
                                    <span style="color:red;"><?php echo form_error('customercontactno');?></span>
                            </div>
                        </div>
                           <div class="col-md-9">
                            <div class="form-group">
                                <label>Email ID<span style="color:red">*</span></label>
                                <input type="text" name="customeremailid" id="customeremailid" class="form-control" value="<?php echo $email_id;?>">  
                                    <span style="color:red;"><?php echo form_error('customeremailid');?></span>
                            </div>
                        </div>

                          <div style="clear:both;height:10px;"></div>
                        

                        
                              
                        </div>
                    </div>


                     <div class="row" style="border:1px dotted #000;">
                    <div class="col-sm-12">
                        <div class="col-md-12" style="margin-bottom:20px;"><h4 style="font-weight:bold;font-size:20px;"><i>Product Details</i></h4></div>
                         <div class="col-md-3">
                            <div class="form-group">
                                <label>Product <span style="color:red">*</span></label>
                            <select class="form-control mand products" name="product" id="product0">
                            <option value="">Select</option>
                            <?php 
                            $rt=$this->db->select('id,instruments_name,model_number')->from('presto_instruments')->where('status',1)->get();
                            if($rt->num_rows()>0)
                            {
                                foreach($rt->result() as $row)
                                {
                            ?>
                            <option value="<?php echo $row->id;?>" <?php if($row->id==$product_id){?> selected <?php } ?>><?php echo $row->instruments_name;?>-<?php echo $row->model_number;?></option>
                            <?php
                            }
                            }
                            ?>
                            ?>
                            </select>
                            <span style="color:red;"><?php echo form_error('product');?></span>
                            </div>
                        </div>
               

                         <div class="col-md-3">
                            <div class="form-group">
                                <label>Qty<span style="color:red">*</span></label>
                                <input type="number" name="qty" min="1" id="qty" class="form-control" value="<?php echo $qty;?>">
                                <span style="color:red;"><?php echo form_error('qty');?></span>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Merchant Export</label>
                                <select class="form-control" name="merchantexport" id="merchantexport">
                                    <option value="0" <?php if($merchant_export==0){?> selected <?php } ?>>No</option>
                                    <option value="1" <?php if($merchant_export==1){?> selected <?php } ?>>Yes</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Client Type</label>
                                <span style="color:red;"><?php echo form_error('customertype');?></span>
                                <select class="form-control" name="customertype" id="customertype" required>
                                    <option value="">Select Customer Type</option>
                                    <option value="1" <?php if($customer_type==1){?> selected <?php } ?>>New Customer</option>
                                    <option value="2" <?php if($customer_type==2){?> selected <?php } ?>>Repeat Customer</option>
                                </select>
                            </div>
                        </div>

                          <div class="col-md-3">
                            <div class="form-group">
                                <label>Probability of Conversion (%)</label>
                                <span style="color:red;">*<?php echo form_error('probability');?></span>
                                <select name="probability" id="probability" class="form-control" required>
                                <option value="">Select</option>
                                <?php
                                $d=array(10,20,30,40,50,60,70,80,90,100);
                                for($i=0;$i<count($d);$i++)
                                {
                                ?>
                                <option value="<?php echo $d[$i];?>" <?php if($probability==$d[$i]){?> selected <?php } ?>><?php echo $d[$i];?> %</option>
                                <?php 
                                }
                                ?>

                                </select>
                                </div>
                                </div>
        
                        
                              
                        </div>
                    </div>

                    <div class="row" style="margin-top:40px;">
                        <div class="col-md-4"></div>
                        <div class="col-md-4 text-center">
                            <input type="submit" name="sub" id="sub" class="btn btn-success" style="width:50%">
                        </div>
                    </div>

                </form>
                </div>
                <!-- end row -->


                <!--- MODAL ---->

                <div id="con-close-modal" class="modal fade bs-example-modal-lg"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <!-- <form id="loginForm" method="post" action="<?php echo page_url;?>Customer/add_customer"  enctype="multipart/form-data"> -->
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Add New Company</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                 <label for="field-2" class="control-label">Company Name</label>
                                 <span id="error_rack_location" style="color:red;">*</span>
                                 <input type="text" class="form-control" id="new_companyname"  value="">
                                  <!-- <img style="float:right;display:none;" id='loading' class="loading" width="100px" src="http://rpg.drivethrustuff.com/shared_images/ajax-loader.gif" />  -->
                            </div>
                        </div>

                         <div class="col-md-6">
                            <div class="form-group">
                                 <label for="field-2" class="control-label">Company Brand</label>
                                 <span id="error_rack_location" style="color:red;"></span>
                                 <select class="form-control" id="new_company_brand" style="width:100%">
                                    <option value=""></option>
                                    <?php 
                                    $rty=$this->db->select('id,name')->from('company_brand')->get();
                                    if($rty->num_rows()>0)
                                    {
                                        foreach($rty->result() as $rtyy)
                                        {
                                    ?>
                                    <option value="<?php echo $rtyy->id;?>"><?php echo $rtyy->name;?></option>
                                    <?php
                                    } 
                                    } 
                                    ?>
                                 </select>
                            </div>
                        </div>
              
                 <div class="col-md-12">
                            <div class="form-group">
                                 <label for="field-2" class="control-label">Email (add multiple email seperated by comma(,))</label>
                                 <span id="error_rack_location" style="color:red;">*</span>
                                <input type="email" class="form-control"  id="new_email"  value="">
                            </div>
                        </div>
                    

                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Contact Person</label>
                                <input type="text" class="form-control" name="contactpersonname" id="contactpersonname" value="">
                            </div>
                        </div>
                         <div class="col-md-4">
                            <div class="form-group">
                                <label>Contact No</label>
                                <input type="text" class="form-control" name="personcontactno" id="personcontactno" value="" required>
                            </div>
                        </div>
                         <div class="col-md-4">
                            <div class="form-group">
                                <label>Alternate Contact No</label>
                                <input type="text" class="form-control" name="acontactno" id="acontactno" value="">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Country</label>
                                <span style="color:red;">*</span>
                                <select class="form-control" id="new_country" style="width:100%">
                                    <option value="">Select</option>
                                    <?php
                                        $q = $this->db->select('country_name, country_id')->from('countries')->where('country_status',1)->get();
                                        foreach($q->result() as $row){
                                    ?>
                                    <option value="<?php echo $row->country_id;?>"><?php echo $row->country_name;?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">State</label>
                                <span style="color:red;">*</span>
                                <select class="form-control" id="new_state" style="width:100%">
                                    <option value="">Select</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row"> 

                          <div class="col-md-12">
                            <div class="form-group">
                                 <label for="field-2" class="control-label"> Address</label>
                                 <span id="error_rack_location" style="color:red;">*</span>
                                <textarea id="new_address" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    <input type="submit" id="save" onclick="add_customer();" class="btn btn-info" value="Submit"> 
                </div>
            </div>
        </div>
        <!-- </form> -->
    </div><!-- /.modal -->





                <!-- Footer -->
<?php $this->load->view('common/footer');?>
                <!-- End Footer -->

            </div> <!-- end container -->
        </div>
        <!-- end wrapper -->


         <!-- jQuery  -->
        <script src="<?php echo assets_url;?>js/jquery.min.js"></script>
        <script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>
        <script src="<?php echo assets_url;?>js/detect.js"></script>
        <script src="<?php echo assets_url;?>js/fastclick.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.slimscroll.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.blockUI.js"></script>
        <script src="<?php echo assets_url;?>js/waves.js"></script>
        <script src="<?php echo assets_url;?>js/wow.min.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.nicescroll.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.scrollTo.min.js"></script>

        <script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js" type="text/javascript"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

       

<script>
$( document ).ready(function() {
  //  get_opp_no();
getcustomerDetails();
$('#product0').select2({  });
$('#new_company_brand').select2({ tags:true });
var purl = "<?php echo page_url;?>Customer/get_customer_by_company";
$(".select3").select2({
placeholder: 'TYPE TO SELECT',
minmumInputLength: 2,
allowClear: true,
closeOnSelect: true,
ajax: {
url: purl,
dataType: 'json',
delay: 250,
data: function(params) {
return {
searchTerm: params.term,
selec:"<?php echo $customer_id;?>"
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



});

</script>
        
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script language="javascript" type="text/javascript">   
jQuery.noConflict();
$(document).ready(function() {
$("#save").click(function() {
var patient_type = $("#patient_type").val();
if(patient_type=='')
{
    $("#error_patient_type").html('Required!');
}

var status = $("#status").val();
if(status=='')
{
    
    $("#error_status").html('Required!');
}


if(patient_type=='' || status=='' )
{
    
    return false;
}

});
});

function get_opp_no()
{
    var op_type=$("#op_type").val();
    var mach_type=$("#mach_type").val();
    if(mach_type==3){
        $("#remarks").attr('Required',true);
        $("#requiredsymbol").html('<span style="color:red">*</span>');
    }else{
        $("#remarks").attr('Required',false);
         $("#requiredsymbol").html('');
    }

    // if(op_type==1){
    //     $("#vatdiv").css('display','none');
    //     $("#gstdiv").css('display','');
    //     $("#gst").attr('required',false);
    //      $("#gstrequiredsymbol").html('<span style="color:red">*</span>');
    // }else{
    //     $("#vatdiv").css('display','');
    //     $("#gstdiv").css('display','none');
    //      // $("#gstrequiredsymbol").attr('required',false);
    //      $("#gstrequiredsymbol").html('');
    //      $("#gst").attr('required',false);
    //      //$("#vatrequiredsymbol").html('<span style="color:red">*</span>');
    // }
    if(op_type!='' && mach_type!='')
    {
        $.ajax({
        type:"post",
        url:"<?php echo page_url;?>Leads/generateOppNo",
        data:"op_type="+op_type+"&mach_type="+mach_type,
        success:function(data){
            var d=data.split('~');
            $("#op_no").val(d[0]);
            $("#op_increment_no").val(d[1]);

        }
        });
    }

}


function getDetails() {
var custid = $('#customer').val();

if (custid != '') {
$.ajax({
type: "post",
url: "<?php echo page_url; ?>Customer/getCustomerDetails_frommaster",
data: "custid=" + custid,
success: function(data) {
var arr = data.split('|');
$("#address").val(arr[0]);
$("#gst").val(arr[6]);
$("#customercontactno").val(arr[14]);
$("#customeremailid").val(arr[20]);
$("#brand").html("<option value='"+arr[19]+"'>"+arr[19]+"</option>");
}
});
}

}

function add_customer()
{
    var new_companyname=$("#new_companyname").val();
    var new_company_brand=$("#new_company_brand").val();
    var new_email=$("#new_email").val();
    var new_address=$("#new_address").val();
    var contactpersonname11=$("#contactpersonname").val();
    var personcontactno11 =$("#personcontactno").val();
    var acontactno11 =$("#acontactno").val();
    var new_country=$("#new_country").val();
    var new_state=$("#new_state").val();
    //alert(contactpersonname11);
    if(new_companyname!='' && new_company_brand!='' && new_email!='' && new_address!='' && new_country!='' && new_state!='')
    {

        $.ajax({
        type:"post",
        url:"<?php echo page_url;?>Customer/add_new_ajax_customer",
        data:"company="+new_companyname+"&brand="+new_company_brand+"&email="+new_email+"&address="+new_address+"&contactpersonname="+contactpersonname11+"&personcontactno="+personcontactno11+"&acontactno="+acontactno11+"&country="+new_country+"&state="+new_state,
        success:function(data){
        var d=data.split('~');
        if(d[0]==0 && d[1]=='State is required'){ alert('State is required'); return; }
        if(d[0]==1)
        {

           $("#con-close-modal").modal('hide');

        }else{
           // alert(data);
            $("contactpersonname").val();
        }
    }
    });

    }else
    {
        alert('All Fields with (*) are mandatory');
    }
    }


function getcustomerDetails()
{
    var customer_id="<?php echo $customer_id;?>";
    $.ajax({
    type:"post",
    url:"<?php echo page_url;?>Leads/getCustomerDetailsByID/"+customer_id,
    data:"",
    success:function(data){
    var d=data.split('|');
        $("#customer").html(d[0]);
        $("#brand").html(d[1]);
    }
    });



}
</script>
<script type="text/javascript">
// Populate the modal State dropdown when the modal Country changes
$(document).on('change', '#new_country', function(){
    var $st = $("#new_state");
    $st.html('<option value="">Select</option>');
    if(!$(this).val()){ return; }
    $.getJSON("<?php echo page_url;?>Customer/states_by_country", {country_id: $(this).val()}, function(rows){
        $.each(rows, function(i, r){
            $st.append($('<option>').val(r.state_id).text(r.state_name));
        });
    });
});
</script>
    </body>
</html>