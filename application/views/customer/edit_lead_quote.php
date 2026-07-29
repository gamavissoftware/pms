<?php 
$id=$this->uri->segment(3);
$CI = &get_instance();
$CI->load->model('Master_model', 'master');
$DI = &get_instance();
$DI->load->model('Salescrm_model', 'salescrm');
$drums_tnc = $CI->master->getAllTnC(1);
$bulk_tnc = $CI->master->getAllTnC(2);

$ro=$this->db->select('hpcl_company as company_id, company_name,customer_name,validity_date, check_terms,general_terms,bulk_terms')
             ->from('leads')
             ->where('id',$id)
             ->get();
if($ro->num_rows()==0)
{
    echo "Invalid Data"; exit;
}else
{
    foreach($ro->result() as $row);
    $company_id=$row->company_id;
    $company_name=$row->company_name;
    $customer_name=$row->customer_name;
    
    if($row->validity_date == '0000-00-00') {
        $validity_date = '';
    } else {
        $validity_date = date('d-m-Y',strtotime($row->validity_date));
    }

    $check_terms=$row->check_terms;
    $drums_tnc=$row->general_terms;
    $bulk_tnc=$row->bulk_terms;
}

$getLeadProducts=$DI->salescrm->getLeadProducts($id);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="NJ Media">

    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

    <title><?php echo sitetitle; ?> Change Product Status</title>

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
    <link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
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

                        <h4 class="page-title"> Change Product Status</h4>
                    </div>
                </div>
            </div>
            <!-- end page title end breadcrumb -->
            <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
            <form id="quotation" method="post" action="<?php echo page_url; ?>Customer/updateleadquotation/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>" onsubmit="return validate_form();">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Company <span style="color:red">*</span></label>
                                        <span id="error_state" style="color:red;"></span>
                                        <select class="form-control mand" name="company" id="company" required onchange="getcustomer(); getproduct();">
                                           
                                            <?php
                                            $q = $this->db->select('id, companyname')->from('store_rack_location')->where('id', $company_id)->get();
                                            foreach ($q->result() as $row) {
                                            ?>
                                                <option value="<?php echo $row->id; ?>" <?php if($row->id==$company_id){?> selected <?php } ?>><?php echo $row->companyname; ?></option>
                                            <?php } ?>
                                        </select>

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Customer Name</label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <input type="text" class="form-control" name="customer" id="customer" value="<?php echo $customer_name;?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Validity Date</label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <input type="text" class="form-control datepicker" name="validity_date" required="" value="<?php echo $validity_date;?>" autocomplete="off">
                                    </div>
                                </div>
                            </div>

                            <?php if($getLeadProducts != '') { 
                                    foreach($getLeadProducts as $editdata) {?>

                            <input type="hidden" name="editid[]" value="<?php echo $editdata->id;?>">
                            <input type="hidden" name="flag[]" value="<?php echo $editdata->flag;?>">
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-3" class="control-label">Product</label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <select class="form-control mand" name="productedit<?php echo $editdata->id;?>" id="productedit<?php echo $editdata->id;?>" required>

                                            <option value="<?php echo $editdata->product_id;?>"><?php echo $editdata->instruments_name;?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Pack Size</label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <input type="text" class="form-control mand" name="qtyedit<?php echo $editdata->id;?>" id="qtyedit<?php echo $editdata->id;?>" required value="<?php echo $editdata->qty;?>">

                                    </div>
                                </div>
                                <?php $discountpricehideedit = $CI->master->getdiscountpriceeditdata($editdata->product_id);
                                ?>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Offered Price Per <?php echo $editdata->unit;?><span class="list_price_unit0"></span></label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <input type="text" class="form-control mand" name="listpriceedit<?php echo $editdata->id;?>" id="listpriceedit<?php echo $editdata->id;?>" value="<?php echo $editdata->price;?>" required onblur="getnetamtedit(<?php echo $editdata->id;?>)" oninput="allow_decimal('listpriceedit<?php echo $editdata->id;?>');">
                                        <p id="discountpriceshowedit<?php echo $editdata->id;?>" style="color:red;font-weight:bold">Allowed Price: <?php echo $discountpricehideedit;?></p>
                                        <input type="hidden" name="discountpricehideedit<?php echo $editdata->id;?>" value="<?php echo $discountpricehideedit;?>" id="discountpricehideedit<?php echo $editdata->id;?>">

                                    </div>
                                </div>
                                
                                <div class="col-md-2" style="display: none;">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Discount Price Per <?php echo $editdata->unit;?><span class="list_price_unit0"></span></label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <input type="text" class="form-control" name="discountpriceedit<?php echo $editdata->id;?>" id="discountpriceedit<?php echo $editdata->id;?>" value="0" onblur="getnetamtedit(<?php echo $editdata->id;?>);" oninput="allow_decimal('discountpriceedit<?php echo $editdata->id;?>');" value="<?php echo $editdata->discount_price;?>">

                                    </div>
                                </div>
                                <div class="col-md-2" style="display: none;">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Net Price</label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <input type="text" class="form-control" name="netpriceedit<?php echo $editdata->id;?>" value="0" id="netpriceedit<?php echo $editdata->id;?>" readonly value="<?php echo $editdata->net_price;?>">

                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group" style="margin-top:25px">
                                        <a href="<?php echo page_url; ?>Leads/delete_lead_product/<?php echo $editdata->id;?>/<?php echo $this->uri->segment(3); ?>"><button type="button" class="btn btn-danger" name="delete" id="deletebutoon" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-close"></i></button></a>
                                    </div>
                                </div>
                               
                            </div>

                            <?php 
                            }
                            }
                            ?>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <label for="field-2" class="control-label">Terms & Conditions Type</label>
                                        <span id="error_rack_location" style="color:red;">*</span><br>
                                        <input type="radio" name="chk_tnc" value="1" <?php if($check_terms == 1) { echo 'checked';} ?> onchange="check_tnc()">&nbsp;&nbsp;Bulk T&C 
                                        <input type="radio" name="chk_tnc" value="2" <?php if($check_terms == 2) { echo 'checked';} ?> onchange="check_tnc()">&nbsp;&nbsp;Drums T&C
                                    </div>
                                </div>
                            </div>

                            <?php 
                                $ab = "display: none;";
                                $ba = "display: none;";

                                if($check_terms == 1) {
                                    $ab = "";
                                } else if($check_terms == 2) {
                                    $ba = "";
                                }

                            ?>
                            <div class="row drums_tnc" style="<?php echo $ba;?>">
                                <div class="col-md-12 remarkbox" style="margin-top:20px;">
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

                            <div class="row bulk_tnc" style="<?php echo $ab;?>">
                                <div class="col-md-12 remarkbox" style="margin-top:20px;">
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

                            <div class="col-md-12" style="margin-top: 20px;">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Add More Products</label>
                                        <input type="checkbox" name="add_product" id="add_product" onchange="add_product_data();">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" style="display:none;" id="shownewproduct">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-3" class="control-label">Product</label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <select class="form-control" name="product[]" id="product0" onchange="getprice(0,this.value);getdiscount(0,this.value);">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Pack Size</label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <input type="text" class="form-control" name="qty[]" id="qty0" >

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Offered Price Per <span class="list_price_unit0"></span></label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <input type="text" class="form-control" name="listprice[]" id="listprice0" value=""  onblur="getnetamt(0)" oninput="allow_decimal('listprice0');">
                                        <p id="discountpriceshow0" style="display: none;"></p>
                                        <input type="hidden" name="discountpricehide[]" value="" id="discountpricehide0">
                                    </div>
                                </div>
                                <div class="col-md-2" style="display:none;">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Discount Price Per <span class="list_price_unit0"></span></label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <input type="text" class="form-control" name="discountprice[]" id="discountprice0" value="0" onblur="getnetamt(0)" oninput="allow_decimal('discountprice0');">
                                        

                                    </div>
                                </div>
                                <div class="col-md-2" style="display:none;">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Net Price</label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <input type="text" class="form-control" name="netprice[]" value="0" id="netprice0"  readonly>

                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group" style="margin-top:25px">
                                        <button type="button" class="btn btn-warning" name="add" id="addmore_btn"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                                <div id="dynamictasks"></div>
                               
                            </div>
                            <div class="row">
                                 <div class="form-group pull-right">
                                    <input type="submit" id="save" class="btn btn-info" value="Submit">
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
    <!-- Datatable init js -->
    <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>

    <!-- App js -->
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>



    <script>
        $(document).ready(function() {
           // getcustomer();
             var date = new Date();
             jQuery('.datepicker').datepicker({
                startDate: date,
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
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

         function displayproductedit() {
          
          var custid=$("#customer").val();

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
                $('#dynamictasks').append('<div id="row' + i + '" class="row">   <div class="col-md-12"><div class="col-md-3"><div class="form-group"><label for="field-3" class="control-label">Product</label><span id="error_rack_location" style="color:red;">*</span><select class="form-control mand" name="product[]" id="product' + i + '" required onchange="getprice(' + i + ',this.value);getdiscount(' + i + ',this.value);"><option value="">Select</option></select></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Pack Size</label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control mand" name="qty[]" id="qty' + i + '" required></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Offered Price Per <span class="list_price_unit'+i+'"></span></label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control mand" name="listprice[]" id="listprice' + i + '" oninput="allow_decimal("listprice' + i + '");"  onblur="getnetamt(' + i + ')" required><p id="discountpriceshow'+i+'" style="display: none;"></p><input type="hidden" name="discountpricehide[]" id="discountpricehide' + i + '" ></div></div><div class="col-md-2" style="display:none;"><div class="form-group"><label for="field-2" class="control-label">Discount Price Per <span class="list_price_unit'+i+'"></span></label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control" name="discountprice[]" id="discountprice' + i + '" value="0" onblur="getnetamt(' + i + ')" oninput="allow_decimal("discountprice' + i + '");"></div></div><div class="col-md-2" style="display:none;"><div class="form-group"><label for="field-2" class="control-label">Net Price</label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control" name="netprice[]" id="netprice' + i + '" value="0" readonly> </div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:23px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="' + i + '"><i class="fa fa-close"></i></button></div></div></div><br/>');


                getproductname(i);
                i++;
            });


            $(document).on('click', '.btn_remove', function() {
                var button_id = $(this).attr("id");
                $('#row' + button_id + '').remove();
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
        function getcustomer() {
            var custid = $('#company').val();
            var custidselected="<?php echo $customer_id;?>";
            //alert(custid);
            if (custid != '') {
                $.ajax({
                    type: "post",
                    url: "<?php echo page_url; ?>Customer/getcustomerdataedit",
                    data: "custid="+custid+"&selectedcompany="+custidselected,
                    success: function(data) {
                        //alert(data);
                        $("#customer").html(data);
                        displayproductedit();
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
                    url: "<?php echo page_url; ?>Customer/getproductdata",
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

            if (parseFloat(listprice) < parseFloat(discountpricehide)) {
                if (confirm('List Price is less than ' + discountpricehide + '. Are you sure you want to continue with this price?')) {

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

        // function check_edit_discount(i) {
        //     var listpriceedit = $("#listpriceedit"+i).val();
        //     var discountpriceedit = $("#discountpriceedit"+i).val();
        //     var discountpricehideedit = $("#discountpricehideedit"+i).val();

        //     if (parseFloat(discountpriceedit) > parseFloat(discountpricehideedit)) {
        //         if (confirm('Discount Price is more than ' + discountpricehideedit + '. Are you sure you want to continue with this discount?')) {

        //             if (parseFloat(listpriceedit) > 0 && parseFloat(discountpriceedit) != '') {
        //                 var netamt = parseFloat(listpriceedit) - parseFloat(discountpriceedit);
        //                 $('#netpriceedit' + i).val(netamt);
        //             } else {
        //                 $('#netpriceedit' + i).val(0);
        //             }
        //         } else {
                    
        //             $("#discountpriceedit" + i).val('');
        //         }
        //     } else {
        //         var netamt = parseFloat(listpriceedit) - parseFloat(discountpriceedit);
        //         $('#netpriceedit'+i).val(netamt);
        //     }
        // }

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
        });

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
                getproductname(0);
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
</body>

</html>