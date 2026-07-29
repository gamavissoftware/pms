<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright;?>">
    <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
    <title><?php echo sitetitle;?></title>
    <script src="<?php echo assets_url;?>js/angular.min.js"></script>
     <!-- DataTables -->
    <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
    <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">

    <?php 
    $q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
    foreach($q->result() as $LOGO);
    ?>

    <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

    <style>
        table.pretty thead th {
          text-align: center;
          background:<?php echo $LOGO->colorcode;?>;
          color:#fff;
          font-size:12px;
        }

      table.pretty td {
          text-align: center;
          font-size:12px;
      }

      </style>
</head>
<body>

      <header id="topnav">
        <?php $this->load->view('common/nav-menu');?>
      </header>

  <div class="wrapper">
    <div class="container">
        <div class="row" style="margin-top:20px;">
            <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                <div class="col-md-4">
                <div class="page-title-box">
                  <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success" name="action_button" style="background-color: ;"><i class="fa fa-arrow-left"></i>Back</button></a>
               
                 
                </div>
                </div>
                <div class="col-md-6"></div>
                <div class="col-md-2">
                      <button class="btn btn-success waves-effect waves-light pull-right" data-toggle="modal" data-target="#con-close-modal">Add</button>
                       
                </div>
            </div>
        </div>
        <div class="row">
             <h4 class="page-title text-center">Equivalent Chart</h4>
        </div>

     
         <div class="row">
            <div class="col-sm-12">
                <div class="card-box table-responsive">
                    <table id="example" class="table table-striped table-bordered pretty" style="background-color: ;">
                        <thead>
                        <tr>
                          <th>S No.</th>
                          <th>Competitor Product</th>
                          <th>Our Equivalent</th>
                          <th>Competitor Product Files</th>
                          
                                                         
                        </tr>
                        </thead>                               
                    </table>
                </div>
            </div>
        </div>


        <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <form id="loginForm" method="post" action="<?php echo page_url; ?>Master/Equivalent_chart/add_data" enctype="multipart/form-data">
                
                    <div class="modal-dialog modal-full">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title">Add Product</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                               
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Competitor Product</label>
                                            <span id="error_instrument_name" style="color:red;">*</span>
                                            <input type="text" class="form-control" id="c_product_name" style="text-transform: uppercase;" name="c_product_name" required>
                                        </div>
                                    </div>
                                 
                                 
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>OUR EQUIVALENT</label>
                                            <span id="error_mvalue" style="color:red;">*</span>
                                           
                                            <select class="select3" name="rack_location[]" multiple="" required="">
                                                <option value="">SELECT</option>
                                               <?php $qu=$this->db->select('id,instruments_name')->from('presto_instruments')->where('status',1)->get();
                                                if($qu->num_rows()>0){
                                                    foreach($qu->result() as $row)
                                                    {
                                                    ?>
                                                    <option value="<?php echo $row->id;?>"><?php echo $row->instruments_name;?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>

                                
                                    <div class="col-md-2">
                                    <div class="form-group">
                                    <label>PRODUCT SPEC FILE</label>
                                    <span id="error_mvalue" style="color:red;">*</span> 
                                    <input type="file" name="specfile" id="specfile" class="form-control">
                                    </div>
                                    </div>

                                    <div class="col-md-2">
                                    <div class="form-group">
                                    <label>PRODUCT MSDS</label>
                                    <span id="error_mvalue" style="color:red;">*</span> 
                                    <input type="file" name="msds" id="msds" class="form-control">
                                    </div>
                                    </div>

                                 
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                <input type="submit" id="save" class="btn btn-info" value="Submit">
                            </div>
                        </div>
                    </div>
                </form>
            </div><!-- /.modal -->

      


          <?php $this->load->view('common/footer');?>
            </div> <!-- end container -->
        </div>

     <!-- jQuery  -->

        <script src="<?php echo assets_url;?>js/jquery.min.js"></script>
        <script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>
        <script src="<?php echo assets_url;?>js/detect.js"></script>
        <script src="<?php echo assets_url;?>js/fastclick.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.slimscroll.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.blockUI.js"></script>
        <script src="<?php echo assets_url;?>js/waves.js"></script>
        <script src="<?php echo assets_url;?>js/wow.min.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.nicescroll.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.scrollTo.min.js"></script>
        <!-- Datatables-->
        <script src="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.bootstrap.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.buttons.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/jszip.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/pdfmake.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/vfs_fonts.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/buttons.html5.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/buttons.print.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.fixedHeader.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.keyTable.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.responsive.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.scroller.min.js"></script>

        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>
        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
        <script src="<?php echo assets_url; ?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>

        <script>
        $( document ).ready(function() {
          $('#example').dataTable({
              "bProcessing": true,
              "pagination":true,
                dom: 'Bfrtip',
                buttons: [
                 'excel' 
                ],
              "sAjaxSource": "<?php echo page_url;?>Master/Equivalent_chart/chart_details",
              "aoColumns": [
                            { mData: 'sr_no' },
                            { mData: 'competitor' },
                            { mData: 'our_equivalent' },
                            { mData: 'spec_file' }
                         ]
                  });   
          });


      
      </script>
      <script>
        $(document).ready(function() {
           
            $('.select3').select2();
        }); //document ready
    </script>
</body>
</html>
