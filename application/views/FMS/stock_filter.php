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

  <div class="row">
        <div class="col-sm-12">
          <div class="text-center">
            <h4 class="page-title">Filter</h4>
          </div> 
        </div>
      </div>

        <form action="<?php echo page_url;?>FMS/filter_stock_data" method="post">
          <div class="row">
            <div class="col-sm-3">
            <div class="form-group">
              <label>Start Date</label>
              <input type="date" name="start" id="start" required class="form-control" min="<?php echo date('Y-m-d',strtotime('2023-07-12'));?>" value="<?php echo $this->uri->segment(3);?>">
            </div>
            </div>

            <div class="col-sm-3">
             <div class="form-group">
              <label>End Date</label>
              <input type="date" name="end" id="end" required class="form-control" max="<?php echo date('Y-m-d');?>"  value="<?php echo $this->uri->segment(4);?>">
            </div>
            </div>   


     

          <div class="col-sm-3">
            <div class="form-group">
              <label>Billing Company</label>
          <select class="form-control" name="company" id="company">
          <!-- <option value="ALL">ALL</option> -->
          <?php
          $uri = $this->uri->segment(3);
          if ($getRackLocation != '') {
          foreach ($getRackLocation as $row1) { ?>
          <option value="<?php echo $row1->id; ?>" <?php if ($row1->id == $this->uri->segment(5)) {
          echo "selected"; $compname=$row1->companyname;
          } ?>><?php echo $row1->companyname; ?></option>
          <?php }
          } ?>
          </select>
          </div>
          </div>

            <div class="col-sm-3">
           
            <div class="form-group">
            <label>Product</label>
            <select name="product" id="product" class="form-control select2">
              <option value="ALL" <?php if($this->uri->segment(6)=="ALL"){?> selected <?php } ?>>ALL</option>
              <?php 
              $row=$this->db->select('id,instruments_name,pack_size')->from('presto_instruments')->get();
              if($row->num_rows()>0)
              {
              foreach($row->result() as $rows)
              {
              ?>
              <option value="<?php echo $rows->id;?>" <?php if($this->uri->segment(6)==$rows->id){?> selected <?php } ?>><?php echo $rows->instruments_name;?>-<?php echo $rows->pack_size;?></option>
              <?php } } ?>
            </select>
          
            </div>
            </div>
   
            <div class="col-md-4"></div>
            <div class="col-md-4 text-center">
              <input type="submit" name="sub" class="btn btn-success" value="Filter" style="width:50%">
            </div>
        
  

          </div>
        </form>

      <!-- Page-Title -->
   
      <!-- end page title end breadcrumb -->
      <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

      <div class="row" style="margin-top:20px">
        <div class="col-sm-12">
          <div class="card-box table-responsive">
            <table id="example" class="table table-bordered manglesh">
              <thead>
                <tr>
                  <th></th>
                  <th></th>
                  <th colspan="3" style="text-align: center;">Opening</th>

                  <th colspan="3" style="text-align: center;">Inward</th>

                  <th colspan="3" style="text-align: center;">Outward</th>

                  <th colspan="3" style="text-align: center;">Closing</th>

                </tr>
                <tr>
                  <th>Sr No.</th>
                  <th>Product</th>
                  <th>Opening Qty</th>
                  <th>Opening Rate</th>
                  <th>Opening Value</th>

                  <th>Inward Qty</th>
                  <th>Inward Rate</th>
                  <th>Inward Value</th>

                  <th>Outward Qty</th>
                  <th>Outward Rate</th>
                  <th>Outward Value</th>


                  <th>Closing Qty</th>
                  <th>Closing Rate</th>
                  <th>Closing Value</th>
                     
                </tr>
              </thead>
              <tbody>
                <?php


                $this->db->select('a.pack_size,a.hsncode,a.id, a.instruments_name,a.unit,a.stock')
                ->from('presto_instruments a');
                if($this->uri->segment(6)<>'ALL' && $this->uri->segment(6)<>'')
                {
                $this->db->where('id',$this->uri->segment(6));
                }

                $query =$this->db->get();


              $i=1;
              if($query->num_rows() > 0) {
              foreach($query->result() as $row) {
              if($this->uri->segment(5)<>'ALL' && $this->uri->segment(5)<>'')
              {
                $show=$CI->master->checkforcompany($row->id,$this->uri->segment(5));
              }else
              {
                $show=1;
              }

              //echo $show; exit;
            
              if($show>0)
              {
              if(strtotime($this->uri->segment(3))==strtotime('2023-07-12'))
              {
              $getOpenstock = $CI->master->get_stock_for_product_static($this->uri->segment(3),$this->uri->segment(4),$this->uri->segment(5),$row->id);
              $ops=explode('~',$getOpenstock);
              $opening_Q=$ops[0];
              $opening_R=$ops[1];
              $opening_V=$opening_Q*$opening_R;

              // echo $opening_Q; exit;


              }else
              {

                 $getOpenstock = $CI->master->get_Open_stock_for_product($this->uri->segment(3),$this->uri->segment(4),$this->uri->segment(5),$row->id);


                $ops=explode('~',$getOpenstock);

                $opening_Q=$ops[0];
                $opening_R=$ops[1];
                $opening_V=$opening_Q*$opening_R;
              }


                $instock=$CI->master->get_inward_between_dates($this->uri->segment(3),$this->uri->segment(4),$this->uri->segment(5),$row->id);
                $opin=explode('~',$instock);
                $opin_Q=$opin[0];
                $opin_R=$opin[1];
                $opin_V=$opin_Q*$opin_R;


              $outstock=$CI->master->get_outward_between_dates($this->uri->segment(3),$this->uri->segment(4),$this->uri->segment(5),$row->id);

              $opout=explode('~',$outstock);
              $opout_Q=$opout[0];
              $opout_R=$opout[1];
              $opout_V=$opout_Q*$opout_R;
              $closing=$opening_Q+$opin_Q-$opout_Q;
              if($opening_Q>0 || $opin_Q>0)
              {
              $closing_R=(($opening_Q*$opening_R)+($opin_Q*$opin_R))/($opening_Q+$opin_Q);
              }else
              {
                $closing_R=0;
              }

              $closing_V=$closing*$closing_R;
             
                ?>
                <tr>
                  <td><?php echo $i;?></td>
                  <td><?php echo $row->instruments_name;?> <?php echo $row->pack_size;?></td>
                   
                  <td><?php echo $opening_Q;?></td>
                  <td><?php echo $opening_R;?></td>
                  <td><?php echo $opening_V;?></td>
                  <td><?php echo $opin_Q;?></td>
                  <td><?php echo $opin_R;?></td>
                  <td><?php echo $opin_V;?></td>
                   <td><?php echo $opout_Q;?></td>
                  <td><?php echo $opout_R;?></td>
                  <td><?php echo $opout_V;?></td>
                  <td><?php echo $closing;?></td>
                  <td><?php echo $closing_R;?></td>
                  <td><?php echo $closing_V;?></td>
                
                  
                </tr>
                <?php $i++;
              } } } ?>
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

var a="<?php echo date('d-M-Y',strtotime($this->uri->segment(3)));?>";
        var b="<?php echo date('d-M-Y',strtotime($this->uri->segment(4)));?>";
        var c="<?php echo strtolower(str_replace(' ','_',$compname));?>";

      $('#example').dataTable({

        

        dom: 'lBfrtip',
"buttons": [
{
  extend: 'excel',
                title: "ClosingStock_"+a+"_"+b+"_"+c
              
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