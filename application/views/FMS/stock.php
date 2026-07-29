<?php
$CI = &get_instance();
$CI->load->model('Master_model', 'master');
$getFinGoodsType = $CI->master->getFinGoodsType();
$getRackLocation = $CI->master->getRackLocation();
$getunit = $CI->master->getunit();

$DI = &get_instance();
$DI->load->model('Salescrm_model', 'salescrm');

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="NJ Media">
  <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
  <title><?php echo sitetitle; ?> Product Management</title>
  <!-- Table Responsive css -->
  <script src="<?php echo assets_url; ?>js/angular.min.js"></script>
  <!-- DataTables -->
  <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
  <script type="text/javascript" src="<?php echo assets_url; ?>ckeditor/ckeditor.js"></script>
  <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
  <link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
  <link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
  <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

  <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
  <style>
    table.manglesh thead th {
      background: #003366;
      color: #fff;
      font-weight: bold;
    }


    .input-color input {
      padding-left: 20px;
    }

    .input-color .color-box {
      width: 10px;
      height: 10px;
      display: inline-block;
      background-color: #ccc;
      position: absolute;
      left: 5px;
      top: 5px;
    }

    .backgroundcolor {
      background-color: #EBEFF2 !important;
    }

    #pageloader {
      background: rgba(255, 255, 255, 0.8);
      display: none;
      height: 100%;
      position: fixed;
      width: 100%;
      z-index: 9999;
    }

    #pageloader img {
      left: 50%;
      margin-left: -32px;
      margin-top: -32px;
      position: absolute;
      top: 50%;
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
    <div class="container-fluid">

<!--  <div class="row">
        <div class="col-sm-12">
          <div class="text-center">
            <h4 class="page-title">Filtered by Company</h4>
          </div> 
        </div>
        <div class="col-sm-3"></div>
        <div class="col-sm-6 card-box">
          <div class="row">
            <div class="col-sm-12">
            <select class="form-control" name="curl" id="curl" onchange="filter_by_company()">
              <option value="">ALL</option>
              <?php
              $uri = $this->uri->segment(3);
              if ($getRackLocation != '') {
                foreach ($getRackLocation as $row1) { ?>
                  <option value="<?php echo $row1->id; ?>" <?php if ($row1->id == $uri) {
                                        echo "selected";
                                      } ?>><?php echo $row1->companyname; ?></option>
              <?php }
              } ?>
            </select>
            </div>
            
          </div>
          
          
        </div>
        
      </div>  -->
      <!-- Page-Title -->
      <div class="row">
        <div class="col-md-4">
          <h4 class="page-title">Stock Master</h4>
        </div>
        <div class="col-md-4 pull-right"></div>
        <div class="col-md-4 pull-right">
        </div>

      </div>
      <!-- end page title end breadcrumb -->
      <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
      <div class="row">
        <div class="col-sm-12">
          <div class="card-box table-responsive">
            <table id="example" class="table table-bordered manglesh">
              <thead>
                <tr>
                  <th>Sr No.</th>
                  <th>Product Name</th>
                <?php
                  if ($getRackLocation != '') {
                foreach ($getRackLocation as $row1) { ?> 
                  <th><?php echo strtoupper($row1->companyname);?></th>
                <?php  } }?>
                 
                 
                           
                  
                  
                </tr>
              </thead>
              <tbody>
                <?php
                $query = $this->db->select('a.pack_size,a.hsncode,a.id, a.instruments_name,a.unit,a.stock')
              ->from('presto_instruments a')
              ->get();


              $i=1;
              if($query->num_rows() > 0) {
              foreach($query->result() as $row) {
        ?>
                <tr>
                  <td><?php echo $i;?></td>
                  <td><?php echo $row->instruments_name;?> <?php echo $row->pack_size;?></td>
                    <?php
                  if ($getRackLocation != '') {
                foreach ($getRackLocation as $row1) { 

                  $stock = $DI->salescrm->get_stock_availability($row->id,$row1->id);
                  ?> 
                  <td><?php echo floatval($stock);?> <?php echo $row->unit;?></td>
                <?php  } }?>
                  
                </tr>
                <?php $i++;
              } } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!-- end row -->


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
  <script src="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
  <!-- Datatable init js -->
  <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>

  <!-- App js -->
  <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
  <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
  <script src="<?php echo assets_url; ?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
  <script>
    $(document).ready(function() {

var a="<?php echo date('d-M-Y');?>";
      $('#example').dataTable({

        dom: 'lBfrtip',

"buttons": [
{
  extend: 'excel',
                title: "ClosingStock_"+a
              
 }
]

      });
    });
  </script>
  
  <script language="javascript" type="text/javascript">
    $(document).ready(function() {
      $(".select2").select2({});

    });



    function filter_by_company() {
      var curl = $("#curl").val();
      document.location.href = "<?php echo page_url;?>FMS/stock/"+curl;
    }
  </script>
  <script>
    $(document).ready(function() {
      $("#loginForm").on("submit", function() {
        $("#pageloader").fadeIn();
      }); //submit
      $('.select3').select2();
    }); //document ready
  </script>
</body>

</html>