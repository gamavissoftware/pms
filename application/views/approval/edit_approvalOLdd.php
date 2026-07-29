<?php 
  $CI =& get_instance();
  $CI->load->model('Salescrm_model', 'salescrm');
  $getAllProducts = $CI->salescrm->getAllProducts();
  $getAllUnits = $CI->salescrm->getAllUnits();
  $getHpclLocations = $CI->salescrm->getHpclLocations();
  $getEditApproval = $CI->salescrm->getEditApproval($this->uri->segment(3));

  $current_date = '';
  $hpcl_location = '';
  $payment_terms = '';
  $credit_period = '';
  $transportation = '';
  $transportation_rate = '';

  if($getEditApproval != '') {
    foreach ($getEditApproval as $row);
        $current_date = $row->current_date;
        $hpcl_location = $row->hpcl_location;
        $payment_terms = $row->payment_terms;
        $credit_period = $row->credit_period;
        $transportation = $row->transportation;
        $transportation_rate = $row->transportation_rate;
  }

  $getEditProductApproval = $CI->salescrm->getEditProductApproval($this->uri->segment(3));
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="NJ Media">

    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

    <title><?php echo sitetitle; ?> EDIT TYPE 1 APPROVAL</title>

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
                    <h4 class="page-title">EDIT TYPE 1 APPROVAL</h4>
                </div>
            </div>
        </div>
            <!-- end page title end breadcrumb -->
        <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
        <form id="quotation" method="post" action="<?php echo page_url; ?>Approval/update_approval/<?php echo $this->uri->segment(3);?>" autocomplete="off" onsubmit="return validate_form();">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Current Date</label>
                                        <input type="text" class="form-control" name="current_date" value="<?php echo date('d-m-Y', strtotime($current_date));?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Locations</label>
                                        <select class="form-control" name="hpcl_location">
                                            <option value="">SELECT</option>
                                            <?php if($getHpclLocations != '') {
                                                    foreach($getHpclLocations as $row) {?>
                                            <option value="<?php echo $row->id;?>" <?php if($row->id == $hpcl_location) { echo 'selected';}?>><?php echo $row->name;?></option>
                                            <?php } } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if($getEditProductApproval != '') {
                                foreach($getEditProductApproval as $row1) {
                                    $unit_name = $CI->salescrm->getUnitName($row1->pack_size);?>
                        <div class="row" id="remove_product<?php echo $row1->id;?>">
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-3" class="control-label">Product</label>
                                        <span style="color:red;">*</span>
                                        <input type="hidden" name="approval_detail_id[]" value="<?php echo $row1->id;?>">
                                        <select class="form-control mand" name="edit_product[]" id="product0" required>
                                            <option value="">Select</option>
                                            <?php if($getAllProducts != '') {
                                                    foreach($getAllProducts as $row) { ?>
                                                        <option value="<?php echo $row->id;?>" <?php if($row->id == $row1->product_id) { echo 'selected';} ?>><?php echo $row->instruments_name;?></option>
                                                   <?php }
                                                } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Approved Price</label>
                                        <span style="color:red;">*</span>
                                        <input type="text" class="form-control mand" name="edit_approved_price[]" id="approved_price0" autocomplete="off" value="<?php echo $row1->approved_price;?>" required>
                                    </div>
                                </div>
                                <div class="col-md-2 col-2">
                                  <div class="form-group">
                                    <label for="field-1" class="control-label">Pack Size</label>
                                    <span style="color:red;">*</span>
                                    <select class="form-control mand pack_size<?php echo $row1->id;?>" name="edit_pack_size[]" onchange="check_unit(<?php echo $row1->id;?>)">
                                      <option value="">SELECT</option>
                                      <?php if($getAllUnits != '') {
                                              foreach($getAllUnits as $row2) {?>
                                      <option value="<?php echo $row2->id;?>" <?php if($row2->id == $row1->pack_size) { echo 'selected';} ?>><?php echo $row2->shortname;?></option>
                                      <?php } } ?>
                                    </select>
                                  </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Price Validity<span class="list_price_unit0"></span></label>
                                        <span style="color:red;">*</span>
                                        <input type="text" class="form-control mand" name="edit_price_validity[]" id="price_validity0" required autocomplete="off" value="<?php echo $row1->price_validity;?>" oninput="allow_decimal('price_validity0');">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">CREDIT/VLI  <span class="chk_unit<?php echo $row1->id;?>">Per <?php echo $unit_name;?></span></label>
                                        <span style="color:red;">*</span>
                                        <input type="text" class="form-control" name="edit_credit_vli[]" id="credit_vli0" autocomplete="off" value="<?php echo $row1->credit_vli;?>" oninput="allow_decimal('credit_vli0');">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Minimum Qty if Any</label>
                                        <span style="color:red;">*</span>
                                        <input type="text" class="form-control" name="edit_moq[]" autocomplete="off" id="moq0" value="<?php echo $row1->moq;?>">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group" style="margin-top:25px">
                                        <button type="button" class="btn btn-danger" name="add" id="delete_btn" onclick="delete_approval_product(<?php echo $row1->id;?>)"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } } ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-3" class="control-label">Product</label>
                                        <span style="color:red;">*</span>
                                        <select class="form-control mand" name="product[]" id="product0">
                                            <option value="">Select</option>
                                            <?php if($getAllProducts != '') {
                                                    foreach($getAllProducts as $row) { ?>
                                                        <option value="<?php echo $row->id;?>"><?php echo $row->instruments_name;?></option>
                                                   <?php }
                                                } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Approved Price</label>
                                        <span style="color:red;">*</span>
                                        <input type="text" class="form-control mand" name="approved_price[]" id="approved_price0" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-2 col-2">
                                  <div class="form-group">
                                    <label for="field-1" class="control-label">Pack Size</label>
                                    <span style="color:red;">*</span>
                                    <select class="form-control mand pack_sizeadd0" name="pack_size[]" onchange="check_unit_add(0)">
                                      <option value="">SELECT</option>
                                      <?php if($getAllUnits != '') {
                                              foreach($getAllUnits as $row2) {?>
                                      <option value="<?php echo $row2->id;?>"><?php echo $row2->shortname;?></option>
                                      <?php } } ?>
                                    </select>
                                  </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Price Validity<span class="list_price_unit0"></span></label>
                                        <span style="color:red;">*</span>
                                        <input type="text" class="form-control mand" name="price_validity[]" id="price_validity0" autocomplete="off" oninput="allow_decimal('price_validity0');">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">CREDIT/VLI <span class="chk_unitadd0"></span></label>
                                        <span style="color:red;">*</span>
                                        <input type="text" class="form-control" name="credit_vli[]" id="credit_vli0" autocomplete="off" oninput="allow_decimal('credit_vli0');">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Minimum Qty if Any</label>
                                        <span style="color:red;">*</span>
                                        <input type="text" class="form-control" name="moq[]" autocomplete="off" id="moq0">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group" style="margin-top:25px">
                                        <button type="button" class="btn btn-warning" name="add" id="addmore_btn"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="dynamictasks"></div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Payment Terms<span style="color:red;">*</span></label>
                                        <select class="form-control mand" name="payment_terms" id="payment_terms" onchange="check_credit_period()">
                                            <option value="1" <?php if($payment_terms == 1) { echo 'selected';}?>>ADVANCE</option>
                                            <option value="2" <?php if($payment_terms == 2) { echo 'selected';}?>>CREDIT PERIOD</option>
                                        </select>
                                    </div>
                                </div>
                                <?php if($payment_terms == 1) {
                                    $a = 'display:none;';
                                } else {
                                    $a = '';
                                }?>
                                <div class="col-md-3" id="chk_credit" style="<?php echo $a;?>">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Credit Period<span style="color:red;">*</span></label>
                                        <input type="text" class="form-control" name="credit_period" id="credit_period" value="<?php echo $credit_period;?>">
                                    </div>
                                </div>
                                <!-- <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Transportation<span style="color:red;">*</span></label>
                                        <select class="form-control" name="transportation" id="transportation" onchange="chk_transportation()">
                                            <option value="">Select</option>
                                            <option value="1" <?php if($transportation == 1) { echo 'selected';};?>>Included</option>
                                            <option value="2" <?php if($transportation == 2) { echo 'selected';};?>>Not Included</option>
                                        </select>
                                    </div>
                                </div>
                                <?php 
                                    if($transportation == 1) {
                                        $a = 'display:none;';
                                    } else {
                                        $a = '';
                                    }
                                ?>
                                <div class="col-md-3" id="chk_transportation" style=<?php echo $a;?>>
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Transportation Rate<span style="color:red;">*</span></label>
                                        <input type="text" class="form-control" name="rate" id="rate" value="<?php echo $transportation_rate;?>">
                                    </div>
                                </div> -->
                            </div>
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

    <script type="text/javascript">
        $(document).ready(function() {
            var i = 1;
            $('#addmore_btn').click(function() {
                $('#dynamictasks').append('<div id="row' + i + '" class="row">   <div class="col-md-12"><div class="col-md-3"> <div class="form-group"> <label for="field-3" class="control-label">Product</label> <span style="color:red;">*</span> <select class="form-control mand" name="product[]" id="product0" required> <option value="">Select</option> <?php if($getAllProducts !='') { foreach($getAllProducts as $row) { ?> <option value="<?php echo $row->id;?>"><?php echo $row->instruments_name;?></option> <?php }} ?> </select> </div></div><div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">Approved Price</label> <span style="color:red;">*</span> <input type="text" class="form-control mand" name="approved_price[]" id="approved_price0" autocomplete="off" required> </div></div><div class="col-md-2 col-2"> <div class="form-group"> <label for="field-1" class="control-label">Pack Size</label> <span style="color:red;">*</span> <select class="form-control mand pack_sizeadd'+i+'" name="pack_size[]" onchange="check_unit_add('+i+')"> <option value="">SELECT</option> <?php if($getAllUnits !='') { foreach($getAllUnits as $row2) { ?> <option value="<?php echo $row2->id;?>"><?php echo $row2->shortname;?></option> <?php }} ?> </select> </div></div><div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">Price Validity<span class="list_price_unit0"></span></label> <span style="color:red;">*</span> <input type="text" class="form-control mand" name="price_validity[]" id="price_validity0" required autocomplete="off"> </div></div><div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">CREDIT/VLI <span class="chk_unitadd'+i+'"></span></label> <span style="color:red;">*</span> <input type="text" class="form-control" name="credit_vli[]" id="credit_vli0" autocomplete="off" onblur="getnetamt(0)"> </div></div><div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">Minimum Qty if Any</label> <span style="color:red;">*</span> <input type="text" class="form-control" name="moq[]" autocomplete="off" id="moq0"> </div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:23px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="' + i + '"><i class="fa fa-close"></i></button></div></div></div><br/>');


                getproductname(i);
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

        function check_unit(i) {
            var pack_size = $('.pack_size'+i+' option:selected').text();
            
            if($('.pack_size'+i).val() != '') {
                $('.chk_unit'+i).text('Per '+pack_size);
            } else {
                $('.chk_unit'+i).text('');
            } 
        }

        function check_unit_add(i) {
            var pack_size = $('.pack_sizeadd'+i+' option:selected').text();
            
            if($('.pack_sizeadd'+i).val() != '') {
                $('.chk_unitadd'+i).text('Per '+pack_size);
            } else {
                $('.chk_unitadd'+i).text('');
            } 
        }

        function delete_approval_product(id) {
            if(confirm('Are you sure you want to delete this product?')) {
                $.ajax({
                    type:"post",
                    url:"<?php echo page_url;?>Approval/delete_approval_product",
                    data:{id: id},
                    success:function(data){
                        if(data == 1) {
                          $('#remove_product'+id).remove();
                        }
                    }
                    });
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
    </script>
</body>

</html>