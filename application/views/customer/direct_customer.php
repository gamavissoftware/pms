<?php
$CI = &get_instance();
$CI->load->model('Salescrm_model');
// $getCreditPeriod = $CI->Salescrm_model->getCreditPeriod();
$getAllStates = $CI->salescrm->getAllStates();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="NJ Media">

    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

    <title><?php echo sitetitle; ?> Customers</title>

    <!-- Table Responsive css -->
    <script src="<?php echo assets_url; ?>js/angular.min.js"></script>
    <!-- DataTables -->
    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
    <link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
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

    <?PHP

    $q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();

    foreach ($q->result() as $LOGO);

    ?>
    <style>
        table.manglesh thead th {

            background: <?php echo $LOGO->colorcode; ?>;

            color: #fff;

            font-weight: bold;

            text-align: center;

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

                         <div class="btn-group pull-right">

                          <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#add_direct_customer">ADD</button>

                               

                            </div>

                           

                         

                        </div>

                    </div>


                <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                    <div class="page-title-box">
                        <div class="btn-group pull-right">


                            <div class="col-md-6 pull-right">


                            </div>
                        </div>

                        <h4 class="page-title">Direct Customer List</h4>
                    </div>
                </div>
            </div>
            <!-- end page title end breadcrumb -->
            <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <table id="example" class="table table-striped table-bordered manglesh">
                            <thead>
                                <tr>
                                    <th>SR NO</th>
                                    <th>Customer Name</th>
                                    <th>Customer Code</th>
                                    <th>TDS</th>
                                    <th>GST</th>
                                    <th>TCS</th>
                                    <th>Address</th>
                                    <th>State</th>
                                    <th>Payment Collection</th>
                                    <th>TQ</th>
                                    <th>Action</th>
                                    <th>Add Payment</th>
                                    <th>Add Debit Credit Note</th>
                                </tr>
                            </thead>
                            <tbody></tbody>

                        </table>
                    </div>
                </div>
            </div>
            <!-- end row -->

            <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <form id="loginForm" method="post" action="<?php echo page_url; ?>Wallet/add_customer_payment">
                    <div class="modal-dialog">

                        <div class="modal-content">

                            <div class="modal-header">

                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                <h4 class="modal-title">Add Payment</h4>

                            </div>

                            <div class="modal-body">

                                <div class="row">

                                    <div class="col-md-6">

                                        <div class="form-group">

                                            <label for="field-1" class="control-label">Customer Name</label>



                                            <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="" value="" readonly autocomplete="off">

                                        </div>

                                    </div>
                                    <input type="hidden" class="form-control" id="customer_id" name="customer_id" placeholder="" value="" readonly autocomplete="off">
                                    <div class="col-md-6">

                                        <div class="form-group">

                                            <label for="field-1" class="control-label">Payment Date</label>

                                            <span id="error_interest" style="color:red;"></span>

                                            <input type="date" class="form-control" id="payment_date" name="payment_date" placeholder="" value="" required autocomplete="off">

                                        </div>

                                    </div>


                                    <div class="col-md-6">

                                        <div class="form-group">

                                            <label for="field-1" class="control-label">Collection ID</label>

                                            <span id="error_interest" style="color:red;"></span>

                                            <input type="text" class="form-control" id="collection_id" name="collection_id" placeholder="" value="" required autocomplete="off" style="text-transform: capitalize;">

                                        </div>

                                    </div>


                                    <div class="col-md-6">

                                        <div class="form-group">

                                            <label for="field-1" class="control-label">Collection Amount</label>

                                            <span id="error_interest" style="color:red;"></span>

                                            <input type="text" class="form-control allow_decimal" id="amount" name="amount" placeholder="" value="" required autocomplete="off">

                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <div class="form-group">

                                            <label for="field-1" class="control-label">Remarks</label>

                                            <span id="error_interest" style="color:red;"></span>

                                            <textarea class="form-control" id="remarks" name="remarks" placeholder="" value="" required></textarea>

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
  <!-- Modal for Debit Credit -->

  <div id="myModalDeditCredit" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none; padding-right: 8px;">
                   <form id="loginForm" method="post" action="<?php echo page_url;?>Wallet/save_customer_credit_debit_note">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    <h4 class="modal-title">Add Credit/Debit Note</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="field-1" class="control-label">CREDIT/DEBIT NOTE</label>
                                                            <span id="error_interest" style="color:red;">*</span>
                                                            <select class="form-control" name="credit_debit" id="credit_debit" onchange="show_note_details()" required="">
                                                                <option value="">SELECT</option>
                                                                <option value="1">CREDIT NOTE</option>
                                                                <option value="2">DEBIT NOTE</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="show_details" style="display: none;">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="field-1" id="credit_debit_for">*</label>
                                                                <span id="error_interest" style="color:red;">*</span>
                                                                <textarea class="form-control mand" name="credit_debit_for"></textarea>
                                                            </div>
                                                        </div>
                                                         <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="field-1" id="credit_debit_amount"></label>
                                                                <span id="error_interest" style="color:red;">*</span>
                                                                <input type="text" class="form-control allow_decimal mand" name="credit_debit_amount">
                                                                <input type="hidden" name="collection_balance" value="" id="collection_balance">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3"></div>
                                                        <div class="col-md-7">
                                                            <div class="form-group">
                                                                <label for="field-1" id="credit_debit_collection"></label>
                                                                <span id="error_interest" style="color:red;"></span>
                                                                <input type="text" class="form-control" name="credit_debit_collection" id="credit_debit_collection_amt" value="" readonly>
                                                                <input type="hidden" name="collection_primary_id" value="" id="collection_primary_id">
                                                                <input type="hidden" name="balance_id" id="balance_id" value="">
                                                                <input type="hidden" name="customer_id" id="customer_id_for_col" value="">
                                                            </div>
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
                            </div>






                            <div id="myModalTQ" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none; padding-right: 8px;">
                   <form id="loginForm" method="post" action="<?php echo page_url;?>Wallet/save_customer_tq">
                    <input type="hidden" name="customer_id" id="customer_idSSSSS" value="">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    <h4 class="modal-title">Add TQ</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                   
                                                   
                                                       


                                                         <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="field-1" id="credit_debit_amount">TQ DATE</label>
                                                                <span id="error_interest" style="color:red;">*</span>
                                                                <input type="date" class="form-control mand" name="tq_date">
                                                              
                                                            </div>
                                                        </div>

                                                            <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="field-1">TQ REFERENCE NO.</label>
                                                                <span id="error_interest" style="color:red;"></span>
                                                                <input type="text" class="form-control mand" name="tq_ref" id="tq_ref" value="" required>
                                                              
                                                            </div>
                                                        </div>


                                                         <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="field-1" id="credit_debit_amount">TQ AMOUNT</label>
                                                                <span id="error_interest" style="color:red;">*</span>
                                                                <input type="text" class="form-control allow_decimal mand" name="tq_amount">
                                                              
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
                            </div>



                            <!-- ADD NEW -->


                              <div id="add_direct_customer" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <form id="loginForm" method="post" action="<?php echo page_url;?>Customer/add_direct_customer">
                    <div class="modal-dialog">

                        <div class="modal-content">

                            <div class="modal-header">

                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                <h4 class="modal-title">Add Customer</h4>

                            </div>

                            <div class="modal-body">

                            
              <div class="row">
                <div class="col-md-12">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="field-2" class="control-label">Customer Name</label>
                      <span style="color:red;">*</span>
                      <input type="text" class="form-control mand" name="customer_name" id="customer_name" autocomplete="off" value="" required>
                    </div>
                  </div>
                 
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="field-2" class="control-label">Customer Code</label>
                      <span style="color:red;">*</span>
                      <input type="text" class="form-control mand" name="customer_code" id="customer_code" autocomplete="off" value="" required>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="field-2" class="control-label">TDS</label>
                      <span style="color:red;">*</span>
                      <input type="text" class="form-control mand" name="tds" id="tds" autocomplete="off" value="" required oninput="allow_decimal('tds')" value="0">
                    </div>
                  </div>
                   <div class="col-md-3">
                    <div class="form-group">
                      <label for="field-2" class="control-label">TCS</label>
                      <span style="color:red;">*</span>
                      <input type="text" class="form-control mand" name="tcs" id="tcs" autocomplete="off" value="" required oninput="allow_decimal('tcs')" value="0">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="field-2" class="control-label">GST</label>
                      <span style="color:red;">*</span>
                      <input type="text" class="form-control mand" name="gst" id="gst" autocomplete="off" value="" required>
                    </div>
                  </div>
                 
                  <div class="col-md-8">
                    <div class="form-group">
                      <label for="field-2" class="control-label">Address</label>
                      <span style="color:red;">*</span>
                      <textarea name="address" class="form-control" required></textarea>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="field-3" class="control-label">State</label>
                      <span style="color:red;">*</span>
                      <select class="form-control mand select30" name="state" id="state" required>
                        <option value="">Select</option>
                        <?php if ($getAllStates != '') {
                          foreach ($getAllStates as $row) { ?>
                            <option value="<?php echo $row->state_id; ?>"><?php echo $row->state_name; ?></option>
                        <?php }
                        } ?>
                      </select>
                    </div>
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

            <!-- end -->
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

    <!-- Datatable init js -->
    <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>

    <!-- App js -->
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>



    <script>
        $(document).ready(function() {
            $('#example').dataTable({
                "bProcessing": true,
                "pagination": true,
                "sAjaxSource": "<?php echo page_url; ?>Customer/direct_customer_list/<?php echo $this->uri->segment(3); ?>",
                  pageLength:50,
                "aoColumns": [{
                        mData: 'sr_no'
                    },
                    {
                        mData: 'customer_name'
                    },
                    {
                        mData: 'customer_code'
                    },
                    {
                        mData: 'tds'
                    },
                    {
                        mData: 'gst'
                    },
                    {
                        mData: 'tcs'
                    },
                    {
                        mData: 'address'
                    },
                    {
                        mData: 'state'
                    },
                    {
                        mData: 'collection'
                    },
                    {
                        mData: 'tq'
                    },
                    // {
                    //     mData: 'customer_debit_credit'
                    // },

                    {
                        mData: 'edit'
                    },
                    {
                        mData: 'payment'
                    },
                    {
                        mData: 'debit_credit'
                    }


                ]
            });
        });
    </script>
    <script>
        $(".allow_decimal").on("input", function(evt) {
            var self = $(this);
            self.val(self.val().replace(/[^0-9\.]/g, ''));

            if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
                evt.preventDefault();
            }
        });


        function validate_gst() {
            var gstinVal = $('#gst').val();
            var reggst = /^([0-9]){2}([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}([0-9]){1}([a-zA-Z]){1}([a-zA-Z0-9]){1}?$/;
            if (!reggst.test(gstinVal) && gstinVal != '') {
                alert('GST Identification Number is not valid. It should be in this "11AAAAA1111Z1A1" format');
                $('#gst').val('');
            }
        }

        function payment(id) {
            // alert(id)
            $('#con-close-modal').modal('show');
            $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Customer/get_direct_customer",
                data: {
                    id: id
                },
                success: function(data) {
                    var d = data.split('|');
                    $('#customer_id').val(d[0]);
                    $('#customer_name').val(d[1]);
                }
            });
        }

        function add_debit_credit(id) {
            $('#myModalDeditCredit').modal('show');
            $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Wallet/getCustomerCollection",
                data: {
                    customer_id: id
                },
                success: function(data) {
                    var d = data.split('|');
                    $('#collection_balance').val(d[0]);
                    $('#credit_debit_collection_amt').val(d[1]);
                    $('#collection_primary_id').val(d[2]);
                    $('#balance_id').val(d[3]);
                    $('#customer_id_for_col').val(d[4]);
                }
            });
        }

        function show_note_details() {
            $('.mand').attr('required', false);
            $('.show_details').css('display', 'none');
            $('#credit_debit_for').text('');
            $('#credit_debit_amount').text('');

            if($('#credit_debit').val() == 1) {
                $('.show_details').css('display', '');
                $('#credit_debit_for').text('Credit Reason');
                $('#credit_debit_amount').text('Credit Amount');
                $('#credit_debit_collection').text('The amount will be credited to this Collection ID.');
                $('.mand').attr('required', true);
            } else if($('#credit_debit').val() == 2) {
                $('.show_details').css('display', '');
                $('#credit_debit_for').text('Debit Reason');
                $('#credit_debit_amount').text('Debit Amount');
                $('#credit_debit_collection').text('The amount will be debited from this Collection ID.');
                $('.mand').attr('required', true);
            }
        }
    </script>

    <script language="javascript" type="text/javascript">
        $(document).ready(function() {

        });

         function tq_payment(id) {
            // alert(id)
            $('#myModalTQ').modal('show');
            $('#customer_idSSSSS').val(id);
           
        }
    </script>
</body>

</html>