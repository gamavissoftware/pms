<?php 
$CI = &get_instance();
$CI->load->model('Salescrm_model', 'salescrm');
$getAllVendors = $CI->salescrm->getAllVendors();
$getAllUnits = $CI->salescrm->getAllUnits();
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
        $hpcl_location = $row->name;
        if($row->payment_terms) {
          $payment_terms = 'ADVANCE';
        } else {
          $payment_terms = 'CREDIT PERIOD';
        }
        $credit_period = $row->credit_period;
        if($row->transportation == 1) {
          $transportation = 'Included';  
        } else if($row->transportation == 2) {
          $transportation = 'Not Included'; 
        } else {
          $transportation = '';
        }
        $transportation_rate = $row->transportation_rate;
  }

$getEditInventoryEntry = $CI->salescrm->getEditInventoryEntry($this->uri->segment(3));


  $currentdate = '';
  $bill_no = '';
  $party = '';
  $entry_id = 0;

  if($getEditInventoryEntry != '') {
    foreach ($getEditInventoryEntry as $row1);
        $currentdate = $row1->currentdate;
        $bill_no = $row1->bill_no;
        $party = $row1->party;
        $entry_id = $row1->id;
  }

$getEditInventoryEntryDetails = $CI->salescrm->getEditInventoryEntryDetails($this->uri->segment(4));


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

  <link href="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">

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

  <link href="<?php echo assets_url; ?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">



  <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->

  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

  <!--[if lt IE 9]>

        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>

        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>

        <![endif]-->



  <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>

  <style>

     .marginbottom {

      margin-bottom: 20px;

    }



    .iii[disabled] {

      pointer-events: none;

      opacity: 0.49;

    }



    .iii i {

      position: absolute;

      top: 50%;

      left: 50%;

      transform: translate(-50%, -50%);

      z-index: 1;

      font-size: 100px;

    }

    .sidenav {
            height: 100%;
            width: 0;
            position: fixed;
            z-index: 1;
            top: 0;
            right: 0;
            background-color: #fff;
            border: 1px solid lightgray;
            overflow-x: hidden;
            transition: 0.5s;
            padding-top: 50px;
            padding-bottom: 50px;
            z-index: 100;
        }

        .sidenav a {
            padding: 8px 8px 8px 32px;
            text-decoration: none;
            font-size: 25px;
            color: #818181;
            display: block;
            transition: 0.3s;
        }

        .sidenav a:hover {
            color: #818181;
        }

        .sidenav .closebtn {
            position: absolute;
            top: -15px;
            right: 5px;
            font-size: 36px;
            margin-left: 50px;
        }

        @media screen and (max-height: 450px) {
            .sidenav {
                padding-top: 15px;
            }

            .sidenav a {
                font-size: 18px;
            }
        }

        .todo-box {
            border: 1px solid lightgray;
            border-radius: 5px;
            height: none;
        }

        .all-notes {
            height: 50px;
            padding: 15px;
            border-bottom: 1px solid #cdcdcd;
        }

        .all-notes p {
            font-weight: 600;
        }

        .all-notes i {
            color: #3d48c4;
        }

        .to-list {
            padding: 20px;

        }

        .todo-list {
            margin: 10px 0;
            overflow-y: auto;
            height: 535px;
        }

        .todo-list .todo-item {
            padding: 15px;
            margin: 5px 0;
            border-radius: 0;
            background: #f7f7f7;
        }

          .search-page h3{
font-weight: 600;
        }

        .search-page h3 span{
            background: #fff1ea;
            color: #f9ab00;
            border-radius: 5px;
            padding: 5px;
            font-size: 20px;
        }

        .search-page p{
            color: black;
            /* font-size: 14px; */
            margin: 0;
        }

        .search-page p i{
            margin-right: 10px;
        }

        .search-page h5 {
            font-size: 17px;
            margin: 0px;
            padding: 5px 0px;
            font-weight: 700;
            border-bottom: 1px solid #25272e52;
            margin-bottom: 10px;
        }
        

        .search-page table{
            width: 100%;
          
           
        }

        .search-page table th{
            padding: 5px;
            text-align: center;
            border: 1px solid lightgray;
            color: black;
            background-color: whitesmoke;
        }

        .search-page table td{
            border: 1px solid lightgray;
            padding: 5px;
            text-align: center;
            color: black;
        }
  </style>


</head>





<body>





  <!-- Navigation Bar-->

  <header id="topnav">

    <?php $this->load->view('common/nav-menu'); ?>

  </header>


        <div class="wrapper">
          <div class="container">
              <div class="row" style="margin-top:20px;">
                  <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                      <div class="page-title-box">
                          <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success" name="action_button"><i class="fa fa-arrow-left"></i>Back</button></a>
                          <h4 class="page-title">Edit Purchase Entry</h4>
                      </div>
                  </div>
              </div>
              <div class="row">
                  <div class="col-sm-12">
                    <div class="card-box search-page">
                      <h5>Overview</h5>

                      <div class="row">
                        <div class="col-sm-6 col-xs-6">
                          <p><strong><i class="fa fa-calendar" aria-hidden="true" style="color:#f9ab00;"></i>Create Date:</strong></p>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                          <p><?php echo date('d-m-Y', strtotime($current_date));?></p>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-6 col-xs-6">
                          <p><strong><i class="fa fa-map-marker" aria-hidden="true" style="color:#f9ab00;"></i>Location: </strong></p>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                          <p><?php echo $hpcl_location;?></p>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-6 col-xs-6">
                          <p><strong><i class="fa fa-credit-card" aria-hidden="true" style="color:#f9ab00;"></i>Payment Terms: </strong></p>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                          <p><?php echo $payment_terms;?></p>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-6 col-xs-6">
                          <p><strong><i class="fa fa-credit-card" aria-hidden="true" style="color:#f9ab00;"></i>Credit Period: </strong></p>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                          <p><?php echo $credit_period;?></p>
                        </div>
                      </div>
                      <div class="clearfix"></div>
                    </div>

                  </div>

                </div>
              <form method="post" action="<?php echo page_url;?>Inventory/update_purchase_entry/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>">
              <div class="row">
                <div class="col-sm-12">
                  <div class="card-box">
                    <div class="row">
                      <div class="col-sm-12 col-xs-12 col-md-12">
                        <div class="col-md-3">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Date</label>
                              <span style="color:red;">*</span>
                              <input type="text" name="current_date" id="datepicker" class="form-control mand" autocomplete="nope" value="<?php echo date('d-m-Y', strtotime($current_date));?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Bill No</label>
                              <span style="color:red;">*</span>
                              <input type="text" name="bill_no" class="form-control mand" autocomplete="nope" value="<?php echo $bill_no;?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Party</label>
                              <span style="color:red;">*</span>
                              <select class="form-control mand" name="vendor">
                                <option value="">SELECT</option>
                                <?php if($getAllVendors != '') {
                                        foreach($getAllVendors as $rows) {?>
                                      <option value="<?php echo $rows->id;?>" <?php if($rows->id == $party) { echo 'selected';}?>><?php echo $rows->name;?></option>
                                <?php } } ?>
                              </select>
                              <!-- <input type="text" name="party" class="form-control mand" autocomplete="nope"> -->
                            </div>
                        </div>
                      </div>
                    </div>
                    <hr>
                    <?php if($getEditInventoryEntryDetails != '') {
                                foreach($getEditInventoryEntryDetails as $row1) {
                                  $getProductName = $CI->salescrm->getProductName($row1->product_id);
                                  ?>
                        <div class="row" id="remove_product<?php echo $row1->id;?>">
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-3" class="control-label">Product</label>
                                        <span style="color:red;">*</span>
                                        <input type="hidden" name="inventory_entry_id[]" class="inventory" value="<?php echo $row1->id;?>">
                                        <input type="hidden" name="approval_detail_id[]" value="<?php echo $row1->approval_detail_id;?>">
                                        <input type="text" name="product[]" class="form-control" value="<?php echo $getProductName;?>" readonly> 
                                    </div>
                                </div>
                                <div class="col-md-2 col-2">
                                  <div class="form-group">
                                    <label for="field-1" class="control-label">Qty</label>
                                    <span style="color:red;">*</span>
                                    <input type="text" name="qty[]" class="form-control" autocomplete="nope" value="<?php echo $row1->qty;?>">
                                  </div>
                                </div>
                                <div class="col-md-3 col-3">
                                  <div class="form-group">
                                    <label for="field-1" class="control-label">Pack Size</label>
                                    <span style="color:red;">*</span>
                                    <select class="form-control" name="pack_size[]">
                                      <option value="">SELECT</option>
                                      <?php if($getAllUnits != '') {
                                              foreach($getAllUnits as $row2) {?>
                                      <option value="<?php echo $row2->id;?>" <?php if($row2->id == $row1->pack_size) { echo 'selected';}?>><?php echo $row2->shortname;?></option>
                                      <?php } } ?>
                                    </select>
                                    <!-- <input type="text" name="pack_size[]" class="form-control" autocomplete="nope" value="<?php echo $row1->pack_size;?>"> -->
                                  </div>
                                </div>
                                <div class="col-md-3 col-3">
                                  <div class="form-group">
                                    <label for="field-1" class="control-label">Rate<span id="rate0"></span></label>
                                    <span style="color:red;">*</span>
                                    <input type="text" name="rate[]" class="form-control" autocomplete="nope" value="<?php echo $row1->approved_price;?>" readonly>
                                  </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group" style="margin-top:25px">
                                        <button type="button" class="btn btn-danger btn-xs" name="add" id="delete_btn" onclick="remove_row(<?php echo $row1->id;?>)"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } } ?>
                    <input type="submit" class="btn btn-success" value="Submit">
                  </div>
                </div>
              </div>
              </form>
          <?php $this->load->view('common/footer'); ?>


        </div>
      </div>



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
      <!-- App js -->
      <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
      <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
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
      <!-- Datatable init js -->
      <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>
      <!-- App js -->
      <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
      <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
      <script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
      <script src="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
      <script src="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.js"></script>

      <script>
        $(document).ready(function() {
            $('#datepicker').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
             });

            $('#man_date0').datepicker({
                  autoclose: true,
                  todayHighlight: true,
                  format: 'dd-mm-yyyy'
             });
        });
      </script>

      <script type="text/javascript">

              function remove_row(id) {
                $(document).on('click', '#delete_btn', function(){
                    $(this).parents("#remove_product"+id).remove();
                });
              }

              function getDatePickers(j) {
                $('#man_date'+j).datepicker({
                  autoclose: true,
                  todayHighlight: true,
                  format: 'dd-mm-yyyy'
               });
              }

      </script>

</body>

</html>