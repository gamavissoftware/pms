<?php 
    $user_id = base64_decode($this->uri->segment(3));
    $CI =& get_instance();
    $CI->load->model('Open_lead_model', 'open_lead');
    if($this->uri->segment(4)<>'NA')
    {
    $getCustomerName = $CI->open_lead->getCustomerDetails($this->uri->segment(4));  
    if(count($getCustomerName)>0)
    {
        $customername=$getCustomerName[0];
        $customer_tds_appl=$getCustomerName[1];
        $customer_tds=$getCustomerName[2];
    }else
    {
        $customername='';
        $customer_tds_appl='';
        $customer_tds='';
    }

    }else{

        $customername='';
        $customer_tds_appl='';
        $customer_tds='';
    }
    

  // $this->db->select('')


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Form</title>

        <!-- Table Responsive css -->
        <script src="<?php echo assets_url;?>js/angular.min.js"></script>
         <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" >
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
         <link href="<?php echo assets_url; ?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

    <style>
        .mobile-form {
            background-color: whitesmoke;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid lightgray;
            margin: 20px 0px;
        }

        .mobile-form h1 {
            color: #17a2b8;
            font-size: 20px;
            text-align: center;
            margin: 8px 0px;
            font-weight: 700;
        }

        .mobile-form label {
            font-size: 14px;
        }

        .mobile-form select {
            width: 100%;
            border: 1px solid lightgray;
            border-radius: 2px;
            padding: 5px 10px;
            font-size: 14px;
            outline: #17a2b8;
            -webkit-appearance: none;
        }

        .mobile-form textarea {
            width: 100%;
            border: 1px solid lightgray;
            border-radius: 2px;
            padding: 5px 10px;
            font-size: 14px;
            outline: #17a2b8;
            -webkit-appearance: none;
        }

        /*.mobile-form input {
            width: 100%;
            border: 1px solid lightgray;
            border-radius: 2px;
            padding: 5px 10px;
            font-size: 14px;
            outline: #17a2b8;
            -webkit-appearance: none;
        }
*/
        @media (max-width:576px) {
            .mobile-form input {
                font-size: 12px;
            }

            .mobile-form label {
                font-size: 12px;
            }

            .mobile-form select {
                font-size: 12px;
            }

            .mobile-form textarea {
                font-size: 12px;
            }

            .btn {
                margin-top: 29px !important;
                padding: 5px 10px !important;
                font-size: 12px !important;
            }

            .mobile-form img{
                width: 100%;
            }
        }

            table.pretty thead th {
                text-align: center;
                background: maroon;
                color:#fff;
                font-size:12px;
            }
            table.pretty td {
                text-align: center;
                font-size:12px;
            }

        .add_more {
            margin-top: 33px;
            }

        .remove_row {
            margin-top: 33px;
            }

            .select2-container .select2-selection--single
            {
                height: 35px !important;
            }
    </style>
</head>

<body>

    <div class="container ">
        <div class="mobile-form">
            <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
            <div class="row">
                <div class="col-sm-12 col-12">
                    <div class="text-center">
                        <img src="<?php echo sfdocument;?>hpcl_logo.png" style="width: 200px;">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-12">
                    <h1 class="text-center">Payment Form</h1>
                </div>
            </div>
            <hr>
            
            <?php 

            if($this->uri->segment(4)<>'' && $this->uri->segment(4)<>'NA' )
            {
                $d="mand";
                $a="*";
            }else
            {
                $d='';
                $a="";
            }
            ?>
            <form id="loginForm" method="post" action="<?php echo page_url;?>Open_leads/save_customer_payments/<?php echo base64_decode($this->uri->segment(3));?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>" enctype="multipart/form-data" onsubmit="return validate_lead();">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Customers</label>
                            <span style="color:red;"><?php echo $a;?></span>
                            <span id="error_lead_source" style="color:red;"></span>
                            <select class="form-control <?php echo $d;?> select2" id="customers" name="customers" onchange="showCustomerOrders(<?php echo $this->uri->segment(5);?>)">
                            </select>
                        </div>
                    </div>

                     <div class="col-sm-2">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Payment Adjustment Type</label>
                            <span style="color:red;">*</span>
                            <span id="error_lead_source" style="color:red;"></span>
                            <select class="form-control mand" id="adjust" name="adjust" onchange="check_adjustment();">
                                <option value="1">By FIFO</option>
                                <option value="2">Against Bill</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label>Bank A/C<span style="color: red;">*</span></label>
                        <span class="input-icon icon-right" style="margin-bottom:10px">
                        <select name="bank" id="bank" class="form-control"> 
                                <?php 
                                $rty=$this->db->select('id,bank_name,account')->from('store_rack_location_account')->where('location_id',$this->uri->segment(5))->get();
                                if($rty->num_rows()>0)
                                {
                                if($rty->num_rows()<1)
                                {
                                ?>
                            <option value="">Select</option>
                            <?php } ?>

                            <?php
                             foreach($rty->result() as $row)
                            {
                                if($rty->num_rows()==1)
                                {
                                    $a="selected";
                                }else
                                {
                                    $a="";
                                }
                        ?>
                        <option value="<?php echo $row->id;?>" <?php echo $a;?>><?php echo $row->account;?>-<?php echo $row->bank_name;?></option>
                        <?php
                        }
                        }
                        ?>
                        </select>
                        </span>
                        </div>


                </div>

        <?php if($this->uri->segment(4) != 0 && $this->uri->segment(4) != '' && $this->uri->segment(4) != 'NA') {?>
            <div class="row">
                <div class="col-sm-12 col-12">
                    <h1 class="text-center">Payment Details of <?php echo $customername;?></h1>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                  <div class="card-box table-responsive">
                    <table id="example2" class="table table-striped table-bordered manglesh">
                      <thead>
                        <tr>
                          <th>SR NO.</th>
                          <th>BILLING COMPANY</th>
                          <th>INVOICE NO.</th>
                           <th>INVOICE DATE</th>
                          <th>ORDER AMOUNT</th>
                          <th>AMOUNT RECD</th>
                          <th>REMAINING AMOUNT</th>
                          <th>STATUS</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            

            
                <input type="hidden" name="tds_appl" value="<?php echo $customer_tds_appl;?>">
                <input type="hidden" name="tds_per" value="<?php echo $customer_tds;?>">
                <div class="row add_payment">
                    <?php  if($customer_tds_appl==1)
                    {?>
                    <div class="col-md-12 text-center"><p style="color:red;font-size:16px;font-weight: bold;"><?php echo $customer_tds;?> % TDS applicable on Payments</p></div>
                    <?php } ?>

                     <div class="col-md-2">
                        <label>Payment Date<span style="color: red;">*</span></label>
                        <span class="input-icon icon-right" style="margin-bottom:10px">
                        <input type="date" class="form-control" name="payment_date[]" id="payment_date0" required>
                        </span>
                      </div>


                    <div class="col-md-2">
                        <label>Payment Type<span style="color: red;">*</span></label>
                        <span class="input-icon icon-right" style="margin-bottom:10px">
                        <select class="form-control" name="payment_type[]" id="payment_type0" onchange="getPaymentInfo(0)" required>
                          <option value="">SELECT PAYMENT TYPE</option>
                          <option value="1">Cheque</option>
                          <option value="2">Cash</option>
                          <option value="3">NEFT</option>
                        </select>
                        </span>
                      </div>
                      <div id="show_cheque_no0" style="display: none;">
                        <div class="col-md-3">
                            <label>Cheque No<span style="color: red;">*</span></label>
                            <span class="input-icon icon-right" style="margin-bottom:10px">
                            <input type="text" class="form-control" name="cheque_no[]" id="cheque_no0" autocomplete="nope">
                            </span>
                        </div>
                        <div class="col-md-2">
                            <label>Cheque Date<span style="color: red;">*</span></label>
                            <span class="input-icon icon-right" style="margin-bottom:10px">
                            <input type="date" class="form-control" name="cheque_date[]" id="cheque_date0" autocomplete="nope">
                            </span>
                        </div>
                      </div>
                      <div class="col-md-3" id="show_trans_no0" style="display: none;">
                        <label>Transaction No.<span style="color: red;">*</span></label>
                        <span class="input-icon icon-right" style="margin-bottom:10px">
                        <input type="text" class="form-control" name="transaction_no[]" id="transaction_no0" autocomplete="nope">
                        </span>
                      </div>
                      <div class="col-md-4">
                        <label>Amount<span style="color: red;">*</span></label>
                        <span class="input-icon icon-right" style="margin-bottom:10px">
                        <input type="text" class="form-control" name="total_amount[]" id="total_amount0" autocomplete="off" required>
                        </span>
                      </div>

                        

                      <div class="col-sm-1">
                        <a href="javascript:void(0)" class="btn btn-warning btn-xs add_more"><span class="glyphicon glyphicon glyphicon-plus" aria-hidden="true"></span></a>
                    </div>
                </div>

                <div class="row" style="margin-top: 60px;">
                    <div class="col-md-12 text-center" id="recommend" style="color:red;font-size:15px;font-weight: bold;margin-bottom: 20px;"></div>
                    <div class="col-md-4"></div>
                    <div class="col-md-4 text-center">
                        <input type="submit" name="sub" id="saves_form" class="btn btn-success" style="width:50%">
                    </div>
                </div>
            </form>
        <?php } ?>
        </div>
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
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js" type="text/javascript"></script>

<script type="text/javascript">

 $(document).ready(function() {   
     jQuery('#current_date').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: 'dd-mm-yyyy'
     });

     jQuery('.cheque_date0').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: 'dd-mm-yyyy'
     });

     var url = "<?php echo page_url;?>Open_leads/getCustomers";

        $('.select2').select2({ 
                    placeholder: 'TYPE TO SELECT',                    
                    minmumInputLength:4,
                    allowClear: true,
        
                        ajax: {
                          url: url,
                          dataType: 'json',
                          delay: 250,
                          data: function (params) {

                                return {
                                    q: params.term,
                                    company:"<?php echo $this->uri->segment(5);?>"
                                    };

                                },

                          processResults: function (data) {
                            return {
                              results: data
                            };

                          },

                    cache: true
                }

            });

        $('#example2').dataTable({
             "bProcessing": false,
             "pagination":true,
             fixedHeader: true,
            "bSort": false,
             fixedColumns:   {
              leftColumns: 3
        },

         "sAjaxSource": "<?php echo page_url;?>Open_leads/customer_order_list/<?php echo $user_id;?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>",

         "aoColumns": [

                       { mData: 'sr_no' },
                       { mData: 'billing_company' },
                       { mData: 'invoice_no' },
                       { mData: 'invoice_date' },
                       { mData: 'order_amount' },
                       { mData: 'amount_recd' },
                       { mData: 'rem_amt' },
                       { mData: 'status' }
                // { mData: 'payment_collection' }

                ],"initComplete": function( settings, json ) {
            
            if($("#adjust").val()==1)
            {
                $(".selectbill").attr('disabled',true);
            }
  }

        });

            var j = 1;

            $('.add_more').click(function(){
                $('.add_payment').append('<div class="row fieldGroups" id="row'+j+'"><div class="col-md-12"><div class="col-md-2"><label>Payment Date<span style="color: red;">*</span></label><span class="input-icon icon-right" style="margin-bottom:10px"><input type="date" class="form-control" name="payment_date[]" id="payment_date'+j+'" required></span></div><div class="col-md-2"> <label>Payment Type<span style="color: red;">*</span></label> <span class="input-icon icon-right" style="margin-bottom:10px"> <select class="form-control" name="payment_type[]" id="payment_type'+j+'" onchange="getPaymentInfo('+j+')"> <option value="">SELECT PAYMENT TYPE</option> <option value="1">Cheque</option> <option value="2">Cash</option> <option value="3">NEFT</option> </select> </span> </div><div id="show_cheque_no'+j+'" style="display:none;"><div class="col-md-3"> <label>Cheque No<span style="color: red;">*</span></label> <span class="input-icon icon-right" style="margin-bottom:10px"> <input type="text" class="form-control" name="cheque_no[]" id="cheque_no'+j+'" autocomplete="nope"> </span> </div><div class="col-md-2"> <label>Cheque Date<span style="color: red;">*</span></label> <span class="input-icon icon-right" style="margin-bottom:10px"> <input type="date" class="form-control" name="cheque_date[]" id="cheque_date'+j+'" autocomplete="nope"> </span> </div></div><div class="col-md-3" id="show_trans_no'+j+'" style="display: none;"> <label>Transaction No<span style="color: red;">*</span></label> <span class="input-icon icon-right" style="margin-bottom:10px"> <input type="text" class="form-control" name="transaction_no[]" id="transaction_no'+j+'" autocomplete="nope"> </span> </div><div class="col-md-4"> <label>Amount<span style="color: red;">*</span></label> <span class="input-icon icon-right" style="margin-bottom:10px"> <input type="text" class="form-control" name="total_amount[]" id="total_amount'+j+'" autocomplete="off" required> </span> </div><div class="col-md-1"> <a href="javascript:void(0)" class="btn btn-danger btn-xs remove_row"><span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span></a> </div></div></div>');

                j++;
            });

            $(document).on('click', '.remove_row', function(){
                $(this).parents(".fieldGroups").remove();
            });

 });


        function validate_lead() {

            $("#saves_form").attr('disabled',true);

            $("#saves_form").val('Please Wait..');

            var isValid=0;

            $("#loginForm .mand").each(function() {

            var element = $(this).val();

            if (element=="") {

            isValid=1;

            }
            });



           var adjust=$("#adjust").val();
           
           if(adjust==1)
           {

           }else if(adjust==2)
           {

            var d = $('input:checkbox:checked').length;
            if(d==0){
                isValid=1;
                alert('selection of at least one bill is required');
                 $("#saves_form").attr('disabled',false);
                 $("#saves_form").val('Submit');
                return false;

                }
                

           }



            if(isValid==0) {

            $("#saves_form").attr('disabled',true);

            $("#saves_form").val('Please Wait..');

            return true;

            } else {

            $("#saves_form").attr('disabled',false);

            $("#saves_form").val('Submit');

            alert('All Fields marked with * are mandatory');

            return false;

            }
 }




    function areAnyChecked(formID) {
  return !!$('#'+formID+' input[type=checkbox]:checked').length;
}
 

    function showCustomerOrders(comp) {
        var cust_id = $('#customers').val();
        $('#show_orders').css('display', '');
    
        location.href = '<?php echo page_url;?>Open_leads/getCustomerOrders/<?php echo $user_id;?>/'+cust_id+"/"+comp;
    }

    function getPaymentInfo(i) {
        $("#recommend").text('');
          var payment_type = $("#payment_type"+i).val();
          $("#show_cheque_no"+i).css('display', 'none');
          $("#cheque_no"+i).removeClass('mand');
          $("#show_trans_no"+i).css('display', 'none');
          $("#transaction_no"+i).removeClass('mand');

          if (payment_type == 1) {
            $("#show_cheque_no"+i).css('display', '');
            $("#cheque_no"+i).addClass('mand');

            $("#recommend").text('Suggestion: Convince this party to change payment terms from Cheque To Online Payment Mode');
          } else if (payment_type == 3) {
            $("#show_trans_no"+i).css('display', '');
            $("#transaction_no"+i).addClass('mand');
          }
        }

        function check_adjustment()
        {
            var ad=$("#adjust").val();
            if(ad==1)
            {
                $(".selectbill").attr('disabled',true);

            }else
            {
                $(".selectbill").attr('disabled',false);
            }

        }

       
</script>



</body>

</html>