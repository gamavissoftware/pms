<?php 

$sql1 = $this->db->select('a.id as order_id, c.id, c.company_name, c.customer_name')
                 ->from('order_punch a')
                 ->join('customer_quotation b', 'b.id=a.quotation_id')
                 ->join('customer_detail c', 'c.id=b.customer_id')
                 ->get();


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cheque Collection Form</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="<?php echo assets_url;?>js/angular.min.js"></script>
    <link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" >
    <link href="<?php echo assets_url;?>plugins/audio/manage-audio.css" rel="stylesheet" type="text/css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

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

        .mobile-form input {
            width: 100%;
            border: 1px solid lightgray;
            border-radius: 2px;
            padding: 5px 10px;
            font-size: 14px;
            outline: #17a2b8;
            -webkit-appearance: none;
        }

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
                    <h1 class="text-center">CHEQUE COLLECTION FORM</h1>
                </div>
            </div>
            <hr>
            <form id="loginForm" method="post" action="<?php echo page_url;?>Open_leads/save_cheque_collection" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Date</label>
                            <span style="color:red;">*</span>
                            <span id="error_create_date" style="color:red;"></span>
                            <input type="text" id="current_date" class="mand" name="current_date" value="<?php echo date('d-m-Y');?>" autocomplete="nope">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Company</label>
                            <span style="color:red;">*</span>
                            <span id="error_lead_source" style="color:red;"></span>
                            <select class="form-control mand" id="company" name="company">
                                <option value="">Select Company</option>
                                    <?php
                                    $q = $this->db->select('id, companyname')->from('store_rack_location')->where('status', 1)->get();
                                    foreach ($q->result() as $row) {
                                    ?>
                                        <option value="<?php echo $row->id; ?>"><?php echo $row->companyname; ?></option>
                                    <?php } ?>   
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Customer</label>
                            <span style="color:red;">*</span>
                            <span id="error_lead_source" style="color:red;"></span>
                            <select class="form-control mand" id="customer" name="customer" onchange="getTotalCustPriceTillDate()">
                                <option value="">Select Customer</option>
                                    <?php

                                    if($sql1->num_rows() > 0) {              
                                        foreach ($sql1->result() as $row1) {?>
                                            <option value="<?php echo $row1->id;?>-<?php echo $row1->order_id;?>"><?php echo $row1->company_name; ?></option>
                                    <?php } } ?>   
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 productss">
                        <div class="row">
                            <div class="col-md-3 col-3">
                                <div class="form-group">
                                    <label for="field-1" class="control-label">Cheque Amt</label>
                                    <span style="color:red;">*</span>
                                    <input type="text" name="cheque_amt[]" autocomplete="nope" class="mand total_amount" onkeyup="calculate_total_amt()">
                                </div>
                            </div>
                            <div class="col-md-2 col-2">
                                <div class="form-group">
                                    <label for="field-1" class="control-label">Cheque Date</label>
                                    <span style="color:red;">*</span>
                                    <span id="error_product" style="color:red;"></span>
                                    <input type="text" name="cheque_date[]" autocomplete="nope" class="mand cheque_date0">
                                </div>
                            </div>
                            <div class="col-md-3 col-3">
                                <div class="form-group">
                                    <label for="field-1" class="control-label">Cheque No</label>
                                    <span style="color:red;">*</span>
                                    <span id="error_product" style="color:red;"></span>
                                    <input type="text" name="cheque_no[]" autocomplete="nope" class="mand">
                                </div>
                            </div>
                            <div class="col-md-3 col-3">
                                <div class="form-group">
                                    <label for="field-1" class="control-label">Bank Name</label>
                                    <span style="color:red;">*</span>
                                    <span id="error_product" style="color:red;"></span>
                                    <input type="text" name="cheque_bank_name[]" autocomplete="nope" class="mand">
                                </div>
                            </div>
                            <div class="col-md-1 col-1">
                                <a href="javascript:void(0)" class="btn btn-warning btn-xs add_more"
                                    style="margin-top: 32px;"><i class="fa fa-plus" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6"></div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Total Amount</label>
                            <span style="color:red;">*</span>
                            <span id="error_lead_source" style="color:red;"></span>
                            <input type="text" name="total_amount" id="total_amount" autocomplete="nope">
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Deposited to</label>
                            <input type="text" name="bank_name" autocomplete="nope">
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Total Collection As On <?php echo date('d-m-Y');?></label>
                            <input type="text" name="total_collection" id="total_collection" readonly="">
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <input type="submit" id="saves_form" class="btn btn-info" value="Submit">
                    </div>
                </div>
            </form>
        </div>
    </div>

<script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url;?>js/detect.js"></script>
<script src="<?php echo assets_url;?>js/fastclick.js"></script>
<script src="<?php echo assets_url;?>js/jquery.slimscroll.js"></script>
<script src="<?php echo assets_url;?>js/jquery.blockUI.js"></script>
<script src="<?php echo assets_url;?>js/waves.js"></script>
<script src="<?php echo assets_url;?>js/wow.min.js"></script>
<script src="<?php echo assets_url;?>js/jquery.nicescroll.js"></script>
<script src="<?php echo assets_url;?>js/jquery.scrollTo.min.js"></script>
<script src="<?php echo assets_url;?>js/jquery.core.js"></script>
<script src="<?php echo assets_url;?>js/jquery.app.js"></script>
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo assets_url;?>plugins/audio/manage-audio.js"></script>
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
 });


        function validate_lead() {

         $("#saves_form").attr('disabled',false);

         $("#saves_form").val('Submit');

         $("#loginForm :input").attr('required',false);



            var isValid=0;



                $("#loginForm .mand").each(function() {

                var element = $(this).val();

                if (element=="") {

                    isValid=1;

                }

            });



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
</script>


<script type="text/javascript">
                var j=1;

            $('.add_more').click(function(){
            $('.productss').append('<div class="row fieldGroups"> <div class="col-md-3 col-3"> <div class="form-group"> <label for="field-1" class="control-label">Cheque Amt</label> <span style="color:red;">*</span> <input type="text" name="cheque_amt[]" autocomplete="nope" class="mand total_amount" onkeyup="calculate_total_amt()"> </div></div><div class="col-md-2 col-2"> <div class="form-group"> <label for="field-1" class="control-label">Cheque Date</label> <span style="color:red;">*</span> <span id="error_product" style="color:red;"></span> <input type="text" name="cheque_date[]" autocomplete="nope" class="mand cheque_date'+j+'"> </div></div><div class="col-md-3 col-3"> <div class="form-group"> <label for="field-1" class="control-label">Cheque No</label> <span style="color:red;">*</span> <span id="error_product" style="color:red;"></span> <input type="text" name="cheque_no[]" autocomplete="nope" class="mand"> </div></div><div class="col-md-3 col-3"> <div class="form-group"> <label for="field-1" class="control-label">Bank Name</label> <span style="color:red;">*</span> <span id="error_product" style="color:red;"></span> <input type="text" name="cheque_bank_name[]" autocomplete="nope" class="mand"> </div></div><div class="col-md-1 col-1"> <a href="javascript:void(0)" class="btn btn-danger btn-xs remove" style="margin-top: 32px;"><i class="fa fa-remove" aria-hidden="true"></i></a> </div></div>');

                getDateForChequeDate(j);
            j++;
            });

            $(document).on('click', '.remove', function(){
                $(this).parents(".fieldGroups").remove();
            });

</script>

<script type="text/javascript">
    function calculate_total_amt() {
        var sum = 0;
        $(".total_amount").each(function(){
          sum += Number($(this).val());

          if(sum>0) {
            // alert(sum);
            $('#total_amount').val(sum);
          }
      
         });
    }

    function getTotalCustPriceTillDate() {
        var customer = $('#customer').val();
        // alert(customer);
        var arr = customer.split('-');
        var customer_id = arr[0];
        var order_id = arr[1];
        // alert(order_id);

        $.ajax({
            type: "post",
            url: "<?php echo page_url; ?>Open_leads/getTotalCustPriceTillDate",
            data: { customer_id: customer_id, order_id: order_id},
            success: function(data) {
                // alert(data);
                $("#total_collection").val(data);
            }
        });
    }

    function getDateForChequeDate(j) {
        jQuery('.cheque_date'+j).datepicker({
        autoclose: true,
        todayHighlight: true,
        format: 'dd-mm-yyyy'
     });
    }
</script>

</body>

</html>