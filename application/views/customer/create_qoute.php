<?php 
$CI = &get_instance();
$CI->load->model('Master_model', 'master');
$drums_tnc = $CI->master->getAllTnC(1);
$bulk_tnc = $CI->master->getAllTnC(2);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="NJ Media">

    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

    <title><?php echo sitetitle; ?> Customer Quotation</title>

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

                        <h4 class="page-title text-center">Customer Quotation</h4>
                    </div>
                </div>
            </div>
            <!-- end page title end breadcrumb -->
            <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
            <form id="quotation" method="post" action="<?php echo page_url; ?>Customer/addquotation" autocomplete="off" onsubmit="return validate_form();">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Billing Company <span style="color:red">*</span></label>
                                        <span id="error_state" style="color:red;"></span>
                                        <select class="form-control mand" name="company" id="company" required onchange="getcustomer(); getproduct();get_tnc(this.value);">
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
                                        <label for="field-2" class="control-label">Customer Name</label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <span class="pull-right"><a href="javascript;:" data-toggle="modal" data-target="#con-close-modal"><i class="fa fa-plus" title="Add New Customer"></i></a></span>
                                        <select class="form-control mand select3" name="customer" id="customer" required onchange="displayproduct(this.value);getCustomerDetails(this.value)">
                                            <option value="">Select</option>
                                        </select>

                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Validity Date</label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <input type="text" class="form-control datepicker" name="validity_date" required="" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div id="showselectedcustomerquotationdata"></div>
                                </div>
                            </div>

                            <div class="col-md-12" style="display:none;" id="show">
                                <div class="row" style="margin-top:20px;">
                                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                                        <div class="page-title-box">
                                            <h4 class="page-title text-center">Customer Details</h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-3">
                                          <label>Mobile No<span style="color: red;">*</span></label>
                                          <input type="number" name="cust_mobile_no" id="cust_mobile_no" autocomplete="nope" minlength="10" maxlength="10" class="form-control mand" onblur="check_shipping_mobile()" required>
                                        </div>
                                        <div class="col-md-3">
                                          <label>Email</label>
                                          <input type="text" name="cust_email" id="cust_email" autocomplete="nope" class="form-control" onblur="check_shipping_email()">
                                        </div>
                                    </div>
                                </div>

                                <div class="row" style="margin-top:20px;">
                                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                                        <div class="page-title-box">
                                            <h4 class="page-title text-center">Product Details</h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-3" class="control-label">Competitor Product</label>
                                        <select class="form-control" name="comp_product[]" id="comp_product0" onchange="getourproductname(0);"></select>
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-3" class="control-label">Product</label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <select class="form-control mand" name="product[]" id="product0" onchange="getprice(0,this.value);getdiscount(0,this.value);getunit(0,this.value);" required>
                                            <option value="">Select</option></select>
                                         <span id="recommendation0"></span>
                                    </div>
                                </div>

                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Qty</label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <input type="number" class="form-control mand" name="qty[]" id="qty0" autocomplete="off" required>

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
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Offered Price Per <span class="list_price_unit0"></span></label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <input type="text" class="form-control mand" name="listprice[]" id="listprice0" value="" required onblur="getnetamt(0)" autocomplete="off" oninput="allow_decimal('listprice0');">
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
                               
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <label for="field-2" class="control-label">Terms & Conditions Type</label>
                                        <span id="error_rack_location" style="color:red;">*</span><br>
                                        Bulk T&C &nbsp;<input type="radio" name="chk_tnc" value="1" onchange="check_tnc()">
                                        Drums T&C &nbsp;<input type="radio" name="chk_tnc" value="2" checked onchange="check_tnc()">
                                    </div>
                                </div>
                            </div>


                            <div class="row drums_tnc">
                               
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
                            <div class="row">
                                 <div class="form-group pull-right">
                                    <input type="submit" id="save" class="btn btn-info" value="Submit" disabled="">
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
                                 <label for="field-2" class="control-label">GST</label>
                                 <span id="error_rack_location" style="color:red;">*</span>
                                 <input type="text" class="form-control" id="gst" onblur="validate_gst()" required>
                            </div>
                        </div>

                         <div class="col-md-4">
                            <div class="form-group">
                                 <label for="field-2" class="control-label">MSME Number</label>
                                
                                <input type="text" class="form-control allow_decimal"  id="msme_number">
                            </div>
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
                    <input type="submit" class="btn btn-info" value="Submit" onclick="submit_data()"> 
                </div>
            </div>
        </div>
      
    </div><!-- /.modal -->

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
            $('#example').dataTable({
                "bProcessing": true,
                "pagination": true,
                "sAjaxSource": "<?php echo page_url; ?>Customer/customer_list/<?php echo $this->uri->segment(3); ?>",
                  pageLength:50,
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
        });
    </script>

    <script>
        function displayproduct(custid) {
          
            if (custid != '') {
                $('#show').css('display', '');
                 $('.remarkbox').css('display', '');
        
            } else {
                $('#show').css('display', 'none');
                 $('.remarkbox').css('display', 'none');
            }
        }
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            var i = 1;
            $('#addmore_btn').click(function() {
                $('#dynamictasks').append('<div id="row' + i + '" class="row"><div class="col-md-12"><div class="col-md-3"><div class="form-group"><label for="field-3" class="control-label">Competitor Product</label><select class="form-control" name="comp_product[]" id="comp_product'+i+'" onchange="getourproductname('+i+');"></select></div></div><div class="col-md-3"><div class="form-group"><label for="field-3" class="control-label">Product</label><span id="error_rack_location" style="color:red;">*</span><select class="form-control mand" name="product[]" id="product' + i + '" required onchange="getprice(' + i + ',this.value);getdiscount(' + i + ',this.value);getunit('+i+',this.value)"><option value="">Select</option></select><span id="recommendation'+i+'"></span></div></div><div class="col-md-1"><div class="form-group"><label for="field-2" class="control-label">Qty</label><span id="error_rack_location" style="color:red;">*</span><input type="number" class="form-control mand" name="qty[]" id="qty' + i + '" required></div></div><div class="col-md-1"><div class="form-group"><label for="field-2" class="control-label">Unit</label><span id="error_rack_location" style="color:red;">*</span><input type="text" name="unit_name[]" id="unit_name'+i+'" class="form-control" readonly><input type="hidden" name="pack_size[]" id="pack_size'+i+'"></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Offered Price Per <span class="list_price_unit'+i+'"></span></label><span id="error_rack_location" style="color:red;">*</span><input type="number" class="form-control mand" name="listprice[]" id="listprice' + i + '" step="any" oninput="allow_decimal("listprice' + i + '");"  onblur="getnetamt(' + i + ')" required><p id="discountpriceshow'+i+'" style="display: none;"></p><input type="hidden" name="discountpricehide[]" id="discountpricehide' + i + '" ></div></div><div class="col-md-2" style="display:none;"><div class="form-group"><label for="field-2" class="control-label">Discount Price Per <span class="list_price_unit'+i+'"></span></label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control" value="0" name="discountprice[]" id="discountprice' + i + '" value="0" onblur="getnetamt(' + i + ')" oninput="allow_decimal("discountprice' + i + '");"></div></div><div class="col-md-2" style="display:none;"><div class="form-group"><label for="field-2" class="control-label">Net Price</label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control" name="netprice[]" id="netprice' + i + '" value="0" readonly> </div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:23px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="' + i + '"><i class="fa fa-close"></i></button></div></div></div><br/>');


                getproductname(i);
                initializeSelect2("comp_product"+i);
                initializeSelect2_product("product"+i);
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

            $('#product0').select2();

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
        function getcustomer() {
            var custid = $('#company').val();
            //alert(custid);
            if (custid != '') {
                $.ajax({
                    type: "post",
                    url: "<?php echo page_url; ?>Customer/getcustomerdata",
                    data: "custid=" + custid,
                    success: function(data) {
                        //alert(data);
                        $("#customer").html(data);
                        $(':input[type="submit"]').prop('disabled', false);
                    }
                });
            }
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
                        $("#product0").html(data);
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
             $('#save').prop('disabled', true);
            
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

                         $('#save').prop('disabled', false);
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
                        $("#cust_email").val(arr[4]);
                        $("#cust_mobile_no").val(arr[14]);
                        check_billing_email();
                        check_billing_mobile();

                    }
                });

                getcustomerprevious_quote_details(custid);
            }

            
        }


        function getcustomerprevious_quote_details(custid)
        {
                $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Customer/getcustomerprevious_quote_details",
                data: "custid=" + custid,
                success: function(data) {
                $("#showselectedcustomerquotationdata").html(data);
                }
                });

        }



        function check_billing_email() {
          var billing_email = $('#cust_email').val();
          // alert(billing_email);
          if(billing_email != '') {
              var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                
                if (!regex.test(billing_email)) {
                  alert('Invalid Email ID!');
                  $('#cust_email').val('');
                }
          }
        }

        function check_billing_mobile() {
          var billing_mobile_no = $('#cust_mobile_no').val();
          var regex = /^[7-9][0-9]{9}$/;
            
            if(billing_mobile_no!='')
            {
            if (!regex.test(billing_mobile_no)) {
              alert('Invalid Mobile No!');
              $('#cust_mobile_no').val('');
            }
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

        function getunit(i, product) {
            $('#save').prop('disabled', true);
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
                    $('#save').prop('disabled', false);


                }
            });
        }
    }  

        function submit_data()
        {
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
        

          if(company!='' && companyname!='' && gst!='' && title!='' && contact_person!='' && mobile!='' && email!='' && address !='' && state !='' && cityname!='' && pincode!='')
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
                        msme_number: msme_number
                      },
                success: function(data) {

                if(data>0)
                {
                    getcustomerdata(data,companyname);

                    $("#con-close-modal").modal('hide');

                }else
                {
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
                       displayproduct(custid);
                        // getCustomerDetails();
                    }
                });

        }

        function get_tnc(company) {
            $('#save').prop('disabled', true);
                $.ajax({
                    type: "post",
                    url: "<?php echo page_url; ?>Customer/get_tnc",
                    data: {company : company},
                    success: function(data) {
                        var arr = data.split('|');
                        console.log(arr);
                        CKEDITOR.instances['drums'].setData(arr[0]);
                        CKEDITOR.instances['bulk'].setData(arr[1]);
                        $('#save').prop('disabled', false);
                        // $(".drums").text();
                        // $(".bulk").text(arr[1]);
                    }
                });
        }
    </script>
</body>

</html>