<?php 
  $CI =& get_instance();
  $CI->load->model('Salescrm_model', 'salescrm');
  $getAllUnits = $CI->salescrm->getAllUnits();
  $getAllProducts = $CI->salescrm->getAllProducts();
  $getHpclLocations = $CI->salescrm->getHpclLocations();
  $getAllTransporters = $CI->salescrm->getAllTransporters();
  $getLastInsertedTypeThreeCode = $CI->salescrm->getLastInsertedTypeThreeCode();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="NJ Media">

    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

    <title><?php echo sitetitle; ?> Approval Form</title>

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
        .select2-container
    {
        width: 100% !important;

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
                    <h4 class="page-title">TYPE-III APPROVAL FORM</h4>
                </div>
            </div>
        </div>
            <!-- end page title end breadcrumb -->
        <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
        <form id="quotation" method="post" action="<?php echo page_url; ?>Approval/save_type_three_approval" autocomplete="off" onsubmit="return validate();">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Auto-generated Code</label>
                                        <input type="text" class="form-control" value="<?php echo 'TYPE-3'.$getLastInsertedTypeThreeCode;?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Current Date</label>
                                        <input type="text" class="form-control" name="current_date" value="<?php echo date('d-m-Y');?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Locations<span style="color:red;">*</span></label>
                                        <select class="form-control mand" name="hpcl_location">
                                            <option value="">SELECT</option>
                                            <?php if($getHpclLocations != '') {
                                                    foreach($getHpclLocations as $row) {?>
                                            <option value="<?php echo $row->id;?>"><?php echo $row->name;?></option>
                                            <?php } } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-3" class="control-label">Product</label>
                                        <span style="color:red;">*</span>
                                        <select class="form-control mand select30" name="product[]" id="product0" required>
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
                                        <input type="text" class="form-control mand" name="approved_price[]" id="approved_price0" autocomplete="off" required oninput="allow_decimal('approved_price0');">
                                    </div>
                                </div>
                                <div class="col-md-2 col-2">
                                  <div class="form-group">
                                    <label for="field-1" class="control-label">Pack Size</label>
                                    <span style="color:red;">*</span>
                                    <select class="form-control mand pack_size0" name="pack_size[]" onchange="check_unit(0)">
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
                                        <input type="date" class="form-control mand" name="price_validity[]" id="price_validity0" required autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">CREDIT/VLI <span class="chk_unit0"></span></label>
                                        <span style="color:red;">*</span>
                                        <input type="text" class="form-control mand" name="credit_vli[]" id="credit_vli0" autocomplete="off" oninput="allow_decimal('credit_vli0');">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Minimum Qty if Any</label>
                                        <span style="color:red;">*</span>
                                        <input type="text" class="form-control mand" name="moq[]" autocomplete="off" id="moq0" oninput="allow_decimal('moq0');" value="0">
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
                                <!-- <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Payment Terms<span style="color:red;">*</span></label>
                                        <select class="form-control mand" name="payment_terms" id="payment_terms" onchange="check_credit_period()">
                                        	<option value="">SELECT</option>
                                        	<option value="1">ADVANCE</option>
                                        	<option value="2">CREDIT PERIOD</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3" id="chk_credit" style="display: none;">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Credit Period<span style="color:red;">*</span></label>
                                        <input type="text" class="form-control" name="credit_period" id="credit_period">
                                    </div>
                                </div> -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Transportation<span style="color:red;">*</span></label>
                                        <select class="form-control mand" name="transportation" id="transportation" onchange="chk_transportation()">
                                            <option value="">Select</option>
                                            <option value="1">Included</option>
                                            <option value="2">Not Included</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- <div class="col-md-3" id="chk_owned" style="display: none;">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Transport Owned/Hired<span style="color:red;">*</span></label>
                                        <select class="form-control" name="transport_owned_hired" id="chk_owned_hired" onchange="chk_owned()">
                                            <option value="">Select</option>
                                            <option value="1">Owned</option>
                                            <option value="2">Hired</option>
                                        </select>
                                    </div>
                                </div> -->
                                <div class="col-md-3" id="chk_transportation" style="display: none;">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Transportation Rate<span style="color:red;">*</span></label>
                                        <input type="text" min="1" class="form-control" name="rate" id="rate" oninput="allow_decimal('rate');">
                                    </div>
                                </div>
                            </div>
                        </div>
                            <div class="row">
                                 <div class="form-group pull-right">
                                    <input type="submit" id="saves" class="btn btn-info" value="Submit">
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
            $('.select4').select2({ tags: true});
            $('.select30').select2(); 
            var i = 1;
            $('#addmore_btn').click(function() {
                $('#dynamictasks').append('<div id="row' + i + '" class="row">   <div class="col-md-12"><div class="col-md-3"> <div class="form-group"> <label for="field-3" class="control-label">Product</label> <span style="color:red;">*</span> <select class="form-control mand select3'+i+'" name="product[]" id="product0" required> <option value="">Select</option> <?php if($getAllProducts !='') { foreach($getAllProducts as $row) { ?> <option value="<?php echo $row->id;?>"><?php echo $row->instruments_name;?></option> <?php }} ?> </select> </div></div><div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">Approved Price</label> <span style="color:red;">*</span> <input type="text" class="form-control mand notext'+i+'1" name="approved_price[]" id="approved_price0" autocomplete="off" required oninput="checkappprice('+i+',1);"> </div></div><div class="col-md-2 col-2"> <div class="form-group"> <label for="field-1" class="control-label">Pack Size</label> <span style="color:red;">*</span> <select class="form-control mand pack_size'+i+'" name="pack_size[]" onchange="check_unit('+i+')"> <option value="">SELECT</option> <?php if($getAllUnits !='') { foreach($getAllUnits as $row2) { ?> <option value="<?php echo $row2->id;?>"><?php echo $row2->shortname;?></option> <?php }} ?> </select> </div></div><div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">Price Validity<span class="list_price_unit0"></span></label> <span style="color:red;">*</span> <input type="date" class="form-control mand" name="price_validity[]" id="price_validity0" required autocomplete="off"> </div></div><div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">CREDIT/VLI <span class="chk_unit'+i+'"></span></label> <span style="color:red;">*</span> <input type="text" class="form-control mand notext'+i+'2" name="credit_vli[]" id="credit_vli0" autocomplete="off" onblur="getnetamt(0)" oninput="checkappprice('+i+',2);"> </div></div><div class="col-md-3"> <div class="form-group"> <label for="field-2" class="control-label">Minimum Qty if Any</label> <span style="color:red;">*</span> <input type="text" class="form-control mand notext'+i+'3" name="moq[]" autocomplete="off" id="moq0" oninput="checkappprice('+i+',3);" value="0"> </div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:23px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="' + i + '"><i class="fa fa-close"></i></button></div></div></div><br/>');

                initializeSelect2(i);
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

        // function chk_transportation() {
        //     $("#chk_owned").css('display', 'none');
        //     $("#chk_owned_hired").removeClass('mand');
        //     $("#rate").removeClass('mand');
        //     $(".chk_transporter_details").css('display', 'none');
        //     $("#chk_transportation").css('display', 'none');

        //    if($("#transportation").val() == 2) {
        //         $("#chk_owned").css('display', '');
        //         $("#chk_owned_hired").addClass('mand');
        //    }
           
        // }

        function chk_transportation() {
            $("#chk_transportation").css('display', 'none');
            $("#rate").removeClass('mand');

           if($("#transportation").val() == 2) {
                $("#chk_transportation").css('display', '');
                $("#rate").addClass('mand');
           }
           
        }

        function chk_owned() {
            $("#chk_transportation").css('display', '');
            // $(".chk_transporter_details").css('display', 'none');
            $("#rate").addClass('mand');
            // $("#transporter_name").removeClass('mand');
            // $("#mobile_no").removeClass('mand');
            // $("#address").removeClass('mand');
            // $("#vehicle_no").removeClass('mand');
            // $("#vehicle_type").removeClass('mand');
            // $("#transport_rate").removeClass('mand');

             // if($("#chk_owned_hired").val() == 2) {
             //    $(".chk_transporter_details").css('display', '');
             //    $("#transporter_name").addClass('mand');
             //    $("#mobile_no").addClass('mand');
             //    $("#address").addClass('mand');
             //    $("#vehicle_no").addClass('mand');
             //    $("#vehicle_type").addClass('mand');
             //    $("#transport_rate").addClass('mand');
             // }
        }

        function getTransporterDetails() {
            var transporter_id = $("#transporter_name").val();

            $.ajax({
                    type:"post",
                    url:"<?php echo page_url;?>Approval/getTransporterDetails",
                    data:{transporter_id: transporter_id},
                    success:function(data) {
                         var arr = data.split('|');
                        var mobile_no = arr[0];
                        var address = arr[1];

                        $("#mobile_no").val(mobile_no);
                        $("#address").val(address);
                    }
                });
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

       function allow_decimal(data) {
            var self = $("#"+data);
              self.val(self.val().replace(/[^0-9\.]/g, ''));
           if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
           {
             evt.preventDefault();
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

        function checkappprice(i,j)
        {
             var self = $(".notext"+i+j);
              self.val(self.val().replace(/[^0-9\.]/g, ''));
           if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
           {
             evt.preventDefault();
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