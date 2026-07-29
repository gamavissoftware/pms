<?php
$CI = &get_instance();
$CI->load->model('Salescrm_model', 'salescrm');
$getDirectCustomer = $CI->salescrm->getDirectCustomer($this->uri->segment(3));
$getAllStates = $CI->salescrm->getAllStates();
$customer_name = '';
$customer_code = '';
$tds = '';
$address = '';
$gst = '';
$tcs = '';
$state = '';
$state_name = '';
if ($getDirectCustomer != '') {
  foreach ($getDirectCustomer as $row);
  $customer_name = $row->customer_name;
  $customer_code = $row->customer_code;
  $tds = $row->tds;
  $address = $row->address;
  $gst = $row->gst;
  $tcs = $row->tcs;
  $state = $row->state;
  $state_name = $row->state_name;
}
// print_r($getDirectCustomer);
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
      <form id="quotation" method="post" action="<?php echo page_url; ?>Customer/update_direct_customer/<?php echo $this->uri->segment(3); ?>" autocomplete="off">
        <div class="row">
          <div class="col-sm-12">
            <div class="card-box table-responsive">
              <!-- New record add -->
              <div class="row">
                <div class="col-md-12">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="field-2" class="control-label">Customer Name</label>
                      <span style="color:red;">*</span>
                      <input type="text" class="form-control mand" name="customer_name" id="customer_name" autocomplete="off" value="<?php echo $customer_name; ?>" required>
                    </div>
                  </div>
                  <input type="hidden" name="direct_customer_id" value="<?php echo $this->uri->segment(3); ?>">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="field-2" class="control-label">Customer Code</label>
                      <span style="color:red;">*</span>
                      <input type="text" class="form-control mand" name="customer_code" id="customer_code" autocomplete="off" value="<?php echo $customer_code; ?>" required>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="field-2" class="control-label">TDS</label>
                      <span style="color:red;">*</span>
                      <input type="text" class="form-control mand" name="tds" id="tds" autocomplete="off" value="<?php echo $tds; ?>" required oninput="allow_decimal('tds')" >
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="field-2" class="control-label">GST</label>
                      <span style="color:red;">*</span>
                      <input type="text" class="form-control mand" name="gst" id="gst" autocomplete="off" value="<?php echo $gst; ?>" required>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="field-2" class="control-label">TCS</label>
                      <span style="color:red;">*</span>
                      <input type="text" class="form-control mand" name="tcs" id="tcs" autocomplete="off" value="<?php echo $tcs; ?>" required oninput="allow_decimal('tcs')">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="field-2" class="control-label">Address</label>
                      <span style="color:red;">*</span>
                      <textarea name="address" class="form-control" required><?php echo $address; ?></textarea>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="field-3" class="control-label">State</label>
                      <span style="color:red;">*</span>
                      <select class="form-control mand select30" name="state" id="state" required>
                        <option value="">Select</option>
                        <?php if ($getAllStates != '') {
                          foreach ($getAllStates as $row) { ?>
                            <option value="<?php echo $row->state_id; ?>" <?php if ($row->state_id == $state) echo "selected" ?>><?php echo $row->state_name; ?></option>
                        <?php }
                        } ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label for="field-2" class="control-label">Payment Req?</label><br>
                      <input type="checkbox" name="payment_req" value="1">
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="form-group pull-right">
                  <input type="submit" id="save" class="btn btn-info" value="Submit">
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
  <script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
 
  <script src="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
  <!-- Datatable init js -->
  <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>
  <!-- App js -->
  <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
  <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
    });

      function allow_decimal(data)
          {

          var self = $("#"+data);
          self.val(self.val().replace(/[^0-9\.]/g, ''));
          if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
          {
          evt.preventDefault();
          }


          }
  </script>
</body>
</html>