<?php
$CI = &get_instance();
$CI->load->model('Salescrm_model', 'salescrm');
$getDirectCustomer = $CI->salescrm->getCustomerdetail($this->uri->segment(3));
if(count($getDirectCustomer)>0)
{
  $custname=$getDirectCustomer[1];
}else
{
  $custname='';
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
  <title><?php echo sitetitle; ?></title>
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
   <link href="<?php echo assets_url; ?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
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
            <h4 class="page-title">ADD PRODUCTS FOR <?php echo $custname;?></h4>
          </div>
        </div>
      </div>
      <!-- end page title end breadcrumb -->
      <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
      <form id="quotation" method="post" action="<?php echo page_url; ?>Customer/update_product_add_customer/<?php echo $this->uri->segment(3); ?>" autocomplete="off">
        <div class="row">
          <div class="col-sm-12">
            <div class="card-box table-responsive">
              <!-- New record add -->
              <div class="row">
                <div class="col-md-12">
                 
                    <div class="form-group">
                      <label for="field-2" class="control-label">Products Name</label>
                      <span style="color:red;">*</span>
                      <select class="select2" name="product[]" class="form-control" id="product" multiple  required></select>
                    </div>
                  </div>

                  <div class="col-md-12">
                <?php 
                $d=$this->db->select('a.id,a.product_id,b.instruments_name')->from('customer_product_used a')->join('presto_instruments b','a.product_id=b.id')->where('a.customer_id',$this->uri->segment(3))->get();
                if($d->num_rows()>0)
                {
                foreach($d->result() as $dd)
                {
                ?>
                <div class="col-md-2 btn btn-danger"><?php echo $dd->instruments_name;?>&nbsp;<a href="javascript:;" onclick="delete_n(<?php echo $dd->id?>);" style="color:white;">X</a></div>
                <?php
                }
                } 
                ?>
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

      var url1 = "<?php echo page_url;?>Customer/getproduct";

            $('#product').select2({ 
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

          function delete_n(rowid)
          {
            if(confirm('Do you really want to delete this product?'))
            {
              document.location="<?php echo page_url;?>Customer/delete_mapped_product/"+rowid;
              return true;
            }
          }
  </script>
</body>
</html>