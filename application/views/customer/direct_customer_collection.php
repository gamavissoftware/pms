<?php
$CI = &get_instance();
$CI->load->model('Salescrm_model');
// $getCreditPeriod = $CI->Salescrm_model->getCreditPeriod();
$getAllStates = $CI->salescrm->getAllStates();
$direct_customer=$CI->salescrm->getDirectCustomer($this->uri->segment(3));
if($direct_customer<>'')
{
    foreach($direct_customer as $customer);
    $name=$customer->customer_name;
}else
{
    ECHO "Invalid Data"; exit;
}
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

                          <!-- <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#add_direct_customer">ADD</button> -->

                               

                            </div>

                           

                         

                        </div>

                    </div>


                <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                    <div class="page-title-box">
                        <div class="btn-group pull-right">


                            <div class="col-md-6 pull-right">


                            </div>
                        </div>

                        <h4 class="page-title text-center"><?php echo ucwords(strtolower($name));?> Payment Wallet Details</h4>
                    </div>
                </div>
            </div>
            <!-- end page title end breadcrumb -->
            <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

       
        <form action="<?php echo page_url;?>Customer/filter_wallet" method="post">
                <div class="col-sm-12 card-box">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Customer</label>
                                <select class="form-control" name="customer" id="customer" required>
                                    <?php 
                                    $rest=$this->db->select('id,customer_name')->from('hpcl_direct_customer')->get();
                                    if($rest->num_rows()>0)
                                    {
                                        foreach($rest->result() as $rows)
                                        {
                                    ?>
                                    <option value="<?php echo $rows->id;?>" <?php if($rows->id==$this->uri->segment(3)){?> selected <?php } ?> ><?php echo $rows->customer_name;?></option>
                                  
                                    <?php } }?>
                               
                                </select> 
                               </div>
                        </div>
                          <div class="col-sm-4">
                              <div class="form-group">
                                  <label>Start Date</label>
                                  <input type="date" name="sdate" required class="form-control" value="<?php echo $this->uri->segment(4);?>">
                              </div>
                          </div>
                            <div class="col-sm-4">
                              <div class="form-group">
                                  <label>End Date</label>
                                  <input type="date" name="edate" required class="form-control" value="<?php echo $this->uri->segment(5);?>">
                              </div>
                          </div>
                        
                    </div>
                    <div class="row">
                        <div class="col-sm-4"></div>
                        <div class="col-sm-4 text-center">
                            <input type="submit" name="" class="btn btn-success" value="Filter" style="width:50%">
                        </div>
                    </div>
                    
                    
                </div>
            </form>

            
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <table id="example" class="table table-striped table-bordered manglesh">
                            <thead>
                                <tr>
                                    <th>SR NO</th>
                                    <th>Customer Name</th>
                                    <th>Date</th>
                                    <th>Collection ID</th>
                                    <th>Collection Amount</th>
                                    <th>Balance</th>
                                    <th>Remarks</th>
                                    <th>Credit/Debit Note</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>

                        </table>
                    </div>
                </div>
            </div>
            <!-- end row -->

           
 



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
                "sAjaxSource": "<?php echo page_url; ?>Customer/direct_customer_collection_list/<?php echo $this->uri->segment(3); ?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>",
                  pageLength:50,
                "aoColumns": [{
                        mData: 'sr_no'
                    },
                    {
                        mData: 'customer_name'
                    },
                    {
                        mData: 'date'
                    },
                    {
                        mData: 'collection_id'
                    },
                    {
                        mData: 'amount'
                    },
                    {
                        mData: 'balance'
                    },
                    {
                        mData: 'remarks'
                    },
                    {
                        mData: 'credit_debit_note'
                    },
                    {
                        mData: 'action'
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


function deletecollection_id(collection_id,flag)
{
    if(flag==0)
    {
    if(confirm('Do you really want revoke all payments done via this collection ID. All payments related to it will be deleted?'))
    {
         document.location="<?php echo page_url;?>Customer/delete_collection_id/"+collection_id+"/"+flag+"/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>";
    }
    }else
    {
        if(confirm('Do you really want revoke all payments done via this collection ID and delete this collection id?'))
    {
        document.location="<?php echo page_url;?>Customer/delete_collection_id/"+collection_id+"/"+flag+"/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>";
    }

    }   

}
    </script>
</body>

</html>