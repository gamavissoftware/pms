<?php
  $CI = &get_instance();
  $CI->load->model('Salescrm_model', 'salescrm');

  $getProductsByLocation = $CI->salescrm->getProductsByLocation(4);
  $getHpclLocations = $CI->salescrm->getHpclLocations();
  // print_r($getProductsByLocation);
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
    <link href="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">
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
    <style type="text/css">
      .select2-container {
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
              <!--  <div class="btn-group pull-right">
                <a href="<?php echo page_url; ?>Store/customer_stock_inward_list"> <button class="btn btn-success waves-effect waves-light">Direct Customer Stock Inward List</button></a>
              </div> -->
              <h4 class="page-title text-uppercase">HPCL Direct Customer Stock Inward</h4>
            </div>
          </div>
        </div>
        <!-- end page title end breadcrumb -->
        <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
        <form id="quotation" method="post" action="<?php echo page_url; ?>Store/save_customer_stock_inward" autocomplete="off" onsubmit="return validate();" enctype="multipart/form-data">
          <div class="row">
            <div class="col-sm-12">
              <div class="card-box table-responsive">
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Current Date <span style="color:red;">*</span></label>
                        <input type="date" class="form-control" name="stock_date" required>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Location<span style="color:red;">*</span></label>
                        <select class="form-control mand" name="hpcl_location" id="hpcl_location0" required>
                          <option value="">SELECT</option>
                          <?php if ($getHpclLocations != '') {
                            foreach ($getHpclLocations as $row) { ?>
                          <option value="<?php echo $row->id; ?>"><?php echo $row->name; ?></option>
                          <?php }
                            } ?>
                        </select>
                      </div>
                    </div>
                     <div class="col-md-3">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Invoice Date <span style="color:red;">*</span></label>
                        <input type="date" class="form-control" name="invoicedate" id="invoicedate" required>
                      </div>
                    </div>

                     <div class="col-md-2">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Invoice No. <span style="color:red;">*</span></label>
                        <input type="text" class="form-control" name="invoiceno" id="invoiceno" required>
                      </div>
                    </div>

                     <div class="col-md-2">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Invoice Attachment<span style="color:red;">*</span></label>
                        <input type="file" class="form-control" name="invoiceattachment" id="invoiceattachment" required>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="field-3" class="control-label">Item</label>
                        <span style="color:red;">*</span>
                        <select class="form-control mand select30" name="product[]" id="product0" required onchange="checkunit(0);;">
                          <option value="">Select</option>
                          <?php if ($getProductsByLocation != '') {
                            foreach ($getProductsByLocation as $row) { ?>
                          <option value="<?php echo $row->id; ?>"><?php echo $row->instruments_name; ?>-<?php echo $row->pack_size; ?></option>
                          <?php }
                            } ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="field-2" class="control-label">QTY (In <span class="un0"></span>)</label>
                        <span style="color:red;">*</span>
                        <input type="text" class="form-control mand" name="qty[]" id="qty0" autocomplete="off" required onkeyup="allow_decimal('qty0');">
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Remarks </label>
                        <span style="color:red;">*</span>
                        <input type="text" class="form-control mand " name="remarks[]" id="remarks0" autocomplete="off">
                      </div>
                    </div>

                     <div class="col-md-2">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Batch No. </label>
                        <span style="color:red;">*</span>
                        <input type="text" class="form-control mand " name="batchno[]" id="batchno0" autocomplete="off">
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
                <hr>
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
      </div>
      <!-- end container -->
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
    <script src="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
    <!-- Datatable init js -->
    <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>
    <!-- App js -->
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
    <script type="text/javascript">
      $(document).ready(function() {
        $('.select30').select2();
        var i = 1;
        $('#addmore_btn').click(function() {
          $('#dynamictasks').append('<div id="row' + i + '" class="row"><hr><div class="col-md-12">  <div class="col-md-3"> <div class="form-group"> <label for="field-3" class="control-label">Product</label> <span style="color:red;">*</span><select class="form-control mand select3' + i + '" name="product[]" id="product' + i + '" required onchange="checkunit(' + i + ');"><option value="">Select</option> <?php if ($getProductsByLocation != '') {
        foreach ($getProductsByLocation as $row) { ?> <option value="<?php echo $row->id; ?>"><?php echo $row->instruments_name; ?>-<?php echo $row->pack_size; ?></option> <?php }
        } ?> </select> </div></div>     <div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">QTY (In <span class="un' + i + '"></span>)</label> <span style="color:red;">*</span> <input type="text" class="form-control mand allow_decimal" name="qty[]" id="qty' + i + '" autocomplete="off" required > </div></div><div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">Remarks </label> <span style="color:red;">*</span> <input type="text" class="form-control mand " name="remarks[]" id="remarks' + i + '" autocomplete="off" > </div></div> <div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Batch No. </label><span style="color:red;">*</span><input type="text" class="form-control mand " name="batchno[]" id="batchno'+i+'" autocomplete="off"></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:23px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="' + i + '"><i class="fa fa-close"></i></button></div></div></div><br/>');
          $(".allow_decimal").on("input", function(evt) {
            var self = $(this);
            self.val(self.val().replace(/[^0-9\.]/g, ''));
            if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
              evt.preventDefault();
            }
          });
          $('.select3' + i).select2();
          // initializeSelect2(i);
          //   getproductname(i);
          // chkIfCombApproved();
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
    <script></script>
    <script language="javascript" type="text/javascript">
      $(document).ready(function() {
      });
    </script>
    <script type="text/javascript">
      function allow_decimal(data) {
        var self = $("#" + data);
        self.val(self.val().replace(/[^0-9\.]/g, ''));
        if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
          evt.preventDefault();
        }
      }
      function checkunit(flag) {
        var inst = $('#product' + flag).val()
        $.ajax({
          type: "post",
          url: "<?php echo page_url; ?>Store/get_item_units",
          data: "item=" + inst,
          success: function(data) {
            $(".un" + flag).text(data);
          }
        });
      }
    </script>
  </body>
</html>