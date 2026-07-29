<?php
  $CI = &get_instance();
  $CI->load->model('Salescrm_model');
  $getProductsByLocation = $CI->salescrm->getProductsByLocation(4);
  $getHpclLocations = $CI->salescrm->getHpclLocations();
  // print_r($getHpclLocations);
  if ($start_date <> '' && $end_date <> '') {
    $starting_date = $start_date;
    $ending_date = $end_date;
    $hpcl_locations = $hpcl_locations;
    $products = $products;
  } else {
    $starting_date = date('Y-m-01');
    $ending_date = date('Y-m-t');
    $hpcl_locations = 'ALL';
    $products = 'ALL';
  }
  
  if ($products <> 'ALL' && $products <> '') {
    $product = $CI->Salescrm_model->get_product_name($products);
  } else {
    $product = "ALL";
  }

  if ($hpcl_locations <> 'ALL') {
    $loc = $CI->Salescrm_model->get_location_name($hpcl_locations);
  } else {
    $loc = "ALL";
  }
  
  
  if ($products <> 'ALL') {
    $product = $CI->Salescrm_model->get_product_name($products);
  } else {
    $product = "ALL";
  }

  $filter_criteria = '<table style="border: 1px solid black;" class="table table-bordered">
                  <tbody><tr>
                  <th style="border: 1px solid black; font-weight:bold;text-align:center;" colspan="3">Report Filter Criteria</th>        
                  </tr>
                  <tr>
                  <th style="border: 1px solid black;text-align:center; width:300px;">Date</th>
                  <th style="border: 1px solid black;text-align:center; width:200px;">Location</th>
                  <th style="border: 1px solid black;text-align:center; width:200px;">Product</th>
                  </tr>
                  <tr>
                  <td style="border: 1px solid black;text-align:center;color:black;">' . date('d-M-Y', strtotime($starting_date)) . ' to ' . date('d-M-Y', strtotime($ending_date)) . '</td>
                  <td style="border: 1px solid black;text-align:center;color:black;">' . $loc . '</td>
                  <td style="border: 1px solid black;text-align:center;color:black;">' . $product . '</td>
                  </tr>
                  </tbody></table>';
  ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?></title>
    <!-- Table Responsive css -->
    <script src="<?php echo assets_url; ?>js/angular.min.js"></script>
    <!-- DataTables -->
    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <?PHP
      $q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
      foreach ($q->result() as $LOGO);
      ?>
    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
    <style>
      table.pretty thead th {
      text-align: center;
      background: <?php echo $LOGO->colorcode; ?>;
      color: #fff;
      font-size: 12px;
      }
      table.pretty td {
      text-align: center;
      font-size: 12px;
      }
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
        <div class="row" style="margin-top:20px">
          <div class="col-md-12">
            <?php echo $this->session->flashdata('message'); ?>
          </div>
        </div>
        <div class="row" style="margin-top:20px;">
          <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 text-center">
            <!-- <div class="btn-group pull-right">
                <a href="<?php echo page_url; ?>Store/direct_customer_stock_in_ward"> <button class="btn btn-success waves-effect waves-light">Add Direct Customer Stock Inward</button></a>
              </div> -->
            <h4 class="page-title text-center text-uppercase">HPCL Direct Customer Stock Inward List</h4>
          </div>
        </div>
        <div class="row" style="margin-top:20px;">
          <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
           
            <form method="post" action="<?php echo page_url; ?>Store/filter_customer_stock_inward_list">
              <div class="card-box col-md-12">
                <div class="">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>From</label>
                      <span id="error_create_date" style="color:red;">*</span>
                      <input type="date" name="from_date" class="form-control" value="<?php  echo $starting_date; ?>" required="">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>To</label>
                      <span id="error_create_date" style="color:red;">*</span>
                      <input type="date" name="to_date" class="form-control" value="<?php echo $ending_date; ?>" required="">
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label>Location</label>
                      <select class="form-control" name="hpcl_locations">
                        <option value="ALL">ALL</option>
                        <?php if ($getHpclLocations != '') {
                          foreach ($getHpclLocations as $row1) { ?>
                        <option value="<?php echo $row1->id; ?>" <?php if ($row1->id == $hpcl_locations) {
                          echo 'selected';
                          }; ?>><?php echo $row1->name; ?></option>
                        <?php }
                          } ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Product</label>
                      <select class="form-control" name="products" id="products">
                        <option value="ALL">ALL</option>
                        <?php if ($getProductsByLocation != '') {
                          foreach ($getProductsByLocation as $row2) { ?>
                        <option value="<?php echo $row2->id; ?>" <?php if ($row2->id == $products) {
                          echo 'selected';
                          }; ?>><?php echo $row2->instruments_name; ?>-<?php echo $row2->pack_size; ?></option>
                        <?php }
                          } ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-1" style="margin-top: 23px;">
                    <input type="submit" class="btn btn-success" value="Filter">
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>

          <div class="row">
          <div class="col-md-3"></div>
          <div class="col-md-6 card-box"><?php echo $filter_criteria; ?></div>
        </div>


        <!-- end page title end breadcrumb -->
        <div class="row">
          <div class="col-sm-12">
            <div class="card-box table-responsive">
              <table id="example" class="table table-striped table-bordered pretty">
                <thead>
                  <tr>
                    <th>Sr No.</th>
                    <th>Stock Date</th>
                    <th>Location</th>
                    <th>Invoice Date</th>
                    <th>Invoice No</th>
                    <th>Invoice Attachment</th>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Batch No.</th>
                    <th>Remarks</th>
                    <th>Action</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>
                <!--Edit Modal -->
        <div id="myModalEdit" class="modal fade" role="dialog">
          <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Update Customer Stock Inward</h4>
              </div>
              <form action="<?php echo page_url; ?>Store/update_customer_stock_inward" method="post" enctype="multipart/form-data">
                <input type="hidden" name="customer_stock_in_word_detail" id="customer_stock_in_word_detail" value="">
                <input type="hidden" name="direct_customer_stock_id" id="direct_customer_stock_id" value="">
                <div class="modal-body">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Current Date <span style="color:red;">*</span></label>
                        <input type="date" class="form-control" name="stock_date" required id="stock_date">
                      </div>
                    </div>

                      <div class="col-md-4">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Invoice Date <span style="color:red;">*</span></label>
                        <input type="date" class="form-control" name="invoicedate" required id="invoicedate">
                      </div>
                    </div>
                     <div class="col-md-4">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Invoice No <span style="color:red;">*</span></label>
                        <input type="text" class="form-control" name="invoiceno" required id="invoiceno">
                      </div>
                    </div>

                      <div class="col-md-4">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Invoice Attachment <span style="color:red;">*</span></label>
                        <input type="file" class="form-control" name="invoiceattachment" required id="invoiceattachment">
                        <input type="hidden"  name="oldimage" id="oldimage" value="">
                      </div>
                    </div>

                    <div class="col-md-4">
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
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="field-3" class="control-label">Item</label>
                        <span style="color:red;">*</span>
                        <select class="form-control mand select30" name="product" id="product0" required onchange="checkunit(0);;">
                          <option value="">Select</option>
                         <!--  <?php if ($getProductsByLocation != '') {
                            foreach ($getProductsByLocation as $row) { ?>
                          <option value="<?php echo $row->id; ?>"><?php echo $row->instruments_name; ?>-<?php echo $row->pack_size; ?></option>
                          <?php }
                            } ?> -->
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="field-2" class="control-label">QTY (In <span class="un0"></span>)</label>
                        <span style="color:red;">*</span>
                        <input type="text" class="form-control mand" name="qty" id="qty0" autocomplete="off" required onkeyup="allow_decimal('qty0');" readonly>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Remarks </label>
                        <span style="color:red;">*</span>
                        <input type="text" class="form-control mand " name="remarks" id="remarks0" autocomplete="off">
                      </div>
                    </div>

                      <div class="col-md-4">
                      <div class="form-group">
                        <label for="field-2" class="control-label">Batch No </label>
                        <span style="color:red;">*</span>
                        <input type="text" class="form-control mand " name="batchno" id="batchno" autocomplete="off">
                      </div>
                    </div>

                  </div>
                </div>
                <div class="modal-footer">
                  <input type="submit" class="btn btn-success" value="Submit">
                </div>
              </form>
            </div>
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
    <script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
    <!-- Datatable init js -->
    <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>
    <!-- App js -->
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
    <script>
      $(document).ready(function() {
        // $('#product0').select2();
        $('#example').dataTable({
          "bProcessing": true,
          "pagination": true,
          "sAjaxSource": "<?php echo page_url; ?>Store/direct_customer_stock_in_word_data/<?php echo $starting_date; ?>/<?php echo $ending_date; ?>/<?php echo $hpcl_locations; ?>/<?php echo $products; ?>",
          "aoColumns": [
            {
              mData: 'sr_no'
            },
            {
              mData: 'stock_date'
            },
            {
              mData: 'location'
            },
            {
              mData: 'invoice_date'
            },
            {
              mData: 'invoice_no'
            },
            {
              mData: 'attachment'
            },
            {
              mData: 'item'
            },
            {
              mData: 'qty'
            },
             {
              mData: 'batchno'
            },
            
            {
              mData: 'remarks'
            },
            { mData: 'edit' },
          ]
        });
      });
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script language="javascript" type="text/javascript">
      jQuery.noConflict();
      $(document).ready(function() {

      });
    </script>
    <script type="text/javascript">
      function edit_adjustment(id) {
        $('#myModalEdit').modal('show');
        $.ajax({
          type: "post",
          url: "<?php echo page_url; ?>Store/get_customer_stock_inward",
          data: "id=" + id,
          success: function(data) {
            // alert(data)
            var d = data.split('|')
             $('#stock_date').val(d[0]);
             $('#hpcl_location0').val(d[1]).change();
            $('#product0').html('<option>'+d[2]+'</option>');
            $('#qty0').val(d[3]);
            $('#remarks0').val(d[4]);
            $('#customer_stock_in_word_detail').val(d[5]); 
            $('#direct_customer_stock_id').val(d[6]); 
            $('#invoicedate').val(d[7]); 
            $('#invoiceno').val(d[8]); 
            $('#oldimage').val(d[9]); 
            $('#batchno').val(d[10]); 
           
          }
        });
      }
    </script>
  </body>
</html>