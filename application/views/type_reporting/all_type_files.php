<?php
$CI = &get_instance();
$CI->load->model('Salescrm_model');
$get_transportation_based_approval_details = $CI->Salescrm_model->get_transportation_based_approval_details($this->uri->segment(3), $this->uri->segment(4));
$getHpclLocations = $CI->Salescrm_model->getHpclLocations();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="NJ Media">

    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

    <title><?php echo sitetitle; ?> Download Files</title>

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

                        <h4 class="page-title text-center">This Month's Claim, Commission, Transportation File</h4>
                    </div>
                </div>
            </div>
            <!-- end page title end breadcrumb -->
            <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
            <form id="quotation" method="post" action="<?php echo page_url;?>Customer/add_direct_order" enctype="multipart/form-data" onsubmit="return validate_form();">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <div class="col-md-4">
                                <table class="table table-bordered">
                                    <tr colspan="3">
                                        <th>TYPE - I Claim</th>
                                    </tr>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Claim Sheet</th>
                                        <th>Invoice</th>
                                    </tr>
                                    <?php if($getHpclLocations != '') {
                                        $i=1;
                                        foreach($getHpclLocations as $row2) {?>
                                    <tr>
                                        <td><?php echo $i;?></td>
                                        <?php if($i == 1) {?>
                                        <td rowspan="5"><a href="<?php echo page_url;?>ExcelImport/type_one_claim_format/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>" class="btn btn-danger">Claim Sheet</a></td>
                                        <?php } ?>
                                        <td><a href="<?php echo page_url1;?>taxinvoice/tcpdf/examples/type1_invoice.php?location_id=<?php echo $row2->id;?>&start_date=<?php echo $this->uri->segment(3);?>&end_date=<?php echo $this->uri->segment(4);?>" class="btn btn-danger" target="_blank">Invoice</a></td>
                                    </tr>
                                    <?php $i++; } 
                                        } ?>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <table class="table table-bordered">
                                    <tr colspan="3">
                                        <th>TYPE - II/III Claim</th>
                                    </tr>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Claim Sheet</th>
                                        <th>Invoice</th>
                                    </tr>
                                    <?php if($getHpclLocations != '') {
                                        foreach($getHpclLocations as $row2) {?>
                                    <tr>
                                        <td>1.</td>
                                        <td><a href="<?php echo page_url;?>ExcelImport/type_two_claim_format/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>" class="btn btn-danger">Claim Sheet</a></td>
                                        <td><a href="<?php echo page_url1;?>taxinvoice/tcpdf/examples/type2_invoice.php?location_id=<?php echo $row2->id;?>&start_date=<?php echo $this->uri->segment(3);?>&end_date=<?php echo $this->uri->segment(4);?>" class="btn btn-danger" target="_blank">Invoice</a></td>
                                    </tr>
                                    <?php } 
                                        } ?>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <table class="table table-bordered">
                                    <tr colspan="3">
                                        <th>APPROVAL BASED TRANSPORTATION</th>
                                    </tr>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Claim Invoice</th>
                                    </tr>
                                    <?php 
                                    $i = 1;
                                    if($get_transportation_based_approval_details != '') {
                                            foreach($get_transportation_based_approval_details as $row) {?>
                                    <tr>
                                        <td><?php echo $i;?></td>
                                        <td><a href="<?php echo page_url1;?>taxinvoice/tcpdf/examples/invoice.php?approval_id=<?php echo $row->id;?>" class="btn btn-danger" download>Claim Invoice</a></td>
                                    </tr>
                                    <?php $i++; } } else { ?>
                                    <tr>
                                        <td colspan="2">No data found</td>
                                    </tr>
                                    <?php } ?>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label></label>
                                            <select class="form-control" name="">
                                                <option value="">SELECT</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>


    <!-- Modal -->
<div id="payment_change" class="modal fade" role="dialog"> <div
class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Payment Term Change Request</h4>
      </div>
     

      <div class="modal-body">
     <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Customer Name</label>
                <input type="text" name="rcust"  class="form-control"  id="rcust" value="" readonly>
                <input type="hidden" name="cust_req" id="cust_req" value="">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Current Payment Type</label>
                <input type="text" name="cpayment" id="cpayment"  class="form-control" value="" readonly required>
            </div>
        </div>

            <div class="col-md-6">
            <div class="form-group">
            <label>Requested Payment Type</label>
            <select class="form-control" name="payment_type_r" id="payment_type_r" required onchange="checkpdf_req();" required>
            <option value="">SELECT PAYMENT TYPE</option>
            <option value="2">Cash</option>
            <option value="3">Online</option>
            <option value="4">PDC</option>
            <option value="5">Credit</option>
            <option value="6">Advance</option>
            </select>

            </div>
            </div>

            <div class="col-md-6" id="credit_days_req" style="display: none;">
            <label>Credit Days<span style="color: red;">*</span></label>
            <input type="number" name="paymentterms_req" id="paymentterms_req" class="form-control" value="">
            </div>

      </div>
    </div>
      <div class="modal-footer">
        <input type="submit" class="btn btn-success" value="Submit" onclick="submit_payment_request();">
      </div>
    </div>


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
    <script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
    <script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

    <!-- Datatable init js -->
    <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>

    <!-- App js -->
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

</body>

</html>