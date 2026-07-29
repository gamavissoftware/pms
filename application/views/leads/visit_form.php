<?php 
$user_id = base64_decode($this->uri->segment(3));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visit Form</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="<?php echo assets_url;?>js/angular.min.js"></script>
    <link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" >
    <link href="<?php echo assets_url;?>plugins/audio/manage-audio.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script>

    <style>

        .loading
        {
        position: absolute;
        left:0px;
        top: -1px;
        }
        .select2-container--default .select2-selection--single
        {
            height: 34px !important;
        }
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
        .select2-container{
            width: 100% !important;
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
                    <h1 class="text-center">ADD NEW VISIT</h1>
                </div>
            </div>
             <?php
                $id = $this->uri->segment(3);
                $assigneduser = base64_decode($id);
            
                
                ?>
            <hr>
             <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="field-1" class="control-label">Customer Type</label>
                                <select class="form-control" id="customertype" name="customertype" onchange="getcustomerinformation();">
                                    <option value="1">New Customer (First Time Visit)</option>
                                    <option value="3">Incomplete Visit</option>
                                    <option value="2">Old Customer</option>
                                </select>
                            </div>
                        </div>
                </div>
                 <script>
                 $('#oldcustomerinfo').fadeOut('slow');
                 $("#box").show();
                    function getcustomerinformation(){
                        var customertype = $("#customertype").val();
                         $("#customer_type_value").val(0);
                      if(customertype==2 || customertype==3){
                          $("#box").hide();
                          $('#oldcustomerinfo').fadeIn('slow');
                          if(customertype==2)
                          {
                            $(".customer_data").css('display','');
                            $(".customer_name").attr('required',true);

                            $(".incomplete_visit_data").css('display','none');
                            $("#visit_customer").attr('required',false);
                            $("#customer_type_value").val(2);

                          }else
                          {
                            $(".customer_data").css('display','none');
                            $(".customer_name").attr('required',false);

                            $(".incomplete_visit_data").css('display','');
                            $("#visit_customer").attr('required',true);
                            $("#customer_type_value").val(3);
                          }
                      }else{
                          $("#box").show();
                           $('#oldcustomerinfo').fadeOut('slow');

                            $(".customer_data").css('display','none');
                            $(".customer_name").attr('required',false);

                            $(".incomplete_visit_data").css('display','none');
                            $("#visit_customer").attr('required',false);

                          
                      }
                    }
                </script>

            <form  method="post" action="<?php echo page_url;?>Open_leads/add_oldcustomer_visit/<?php echo $user_id;?>" enctype="multipart/form-data">
               
               
                
                <div class="row" id="oldcustomerinfo" style="display:none;">
                       <div class="col-sm-6 col-md-4 col-lg-4 col-xs-12 customer_data" style="display:none">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Customer Name</label>
                            <span style="color:red;">*</span>
                            <span id="error_create_date" style="color:red;"></span>
                           <select class="form-control customername" name="customer_name" id="customer_name">
                               <option value="">Select Customer</option>
                               <?php 
                                $q = $this->db->select('id, company_name')->from('customer_detail')->where('status',1)->where('assigned_to',$assigneduser)->order_by('company_name','ASC')->get();
                                foreach($q->result() as $row){
                                
                               ?>
                               <option value="<?php echo $row->id;?>"><?php echo $row->company_name;?></option>
                               <?php }?>
                           </select>
                        </div>
                    </div>

                      <div class="col-sm-6 col-md-4 col-lg-4 col-xs-12 incomplete_visit_data" style="display:none;">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Incomplete Visit</label>
                            <span style="color:red;">*</span>
                            <span id="error_create_date" style="color:red;"></span>
                           <select class="form-control visitcustomer" name="visit_customer" id="visit_customer" required>
                               <option value="">Select Customer</option>
                               <?php 
                                $q = $this->db->select('id, company_name')->from('daily_visits')->where('converted',0)->where('added_by',$assigneduser)->order_by('company_name','ASC')->get();
                                foreach($q->result() as $row){
                                
                               ?>
                               <option value="<?php echo $row->id;?>"><?php echo $row->company_name;?></option>
                               <?php }?>
                           </select>
                        </div>
                    </div>
                    
                    <div class="col-sm-8 col-md-8 col-lg-8 col-xs-12">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Remarks</label>
                            <span style="color:red;">*</span>
                            <span id="error_create_date" style="color:red;"></span>
                           <textarea class="form-control" name="remarks" id="remarks" required></textarea>
                           <input type="hidden" name="customer_type_value" id="customer_type_value" value="0">
                            <script>
                                                    CKEDITOR.replace('remarks');
                                                    </script>
                        </div>
                    </div>
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="submit" class="btn btn-info" value="Submit" name="">
                        </div>
                    </div>
                    
                </div>

            </form>    
            <form id="loginForm" method="post" action="<?php echo page_url;?>Open_leads/add_visits/<?php echo $user_id;?>" enctype="multipart/form-data" onsubmit="return validate_lead();">
               
                
                <div class="row" id="box">
                    <div class="col-sm-6 col-md-4 col-lg-4 col-xs-12">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Visit Date</label>
                            <span style="color:red;">*</span>
                            <span id="error_create_date" style="color:red;"></span>
                            <input type="text" id="create_date" class="mand" name="create_date" value="<?php echo date('d-m-Y');?>"  autocomplete="nope" required>
                        </div>
                    </div>
                               
                      <div class="col-sm-12">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Company GSTN</label>
                            <span style="color:red;">*</span>
                            <input type="text" id="company_gstn" name="company_gstn" placeholder="Company GSTN"
                                autocomplete="nope" class="mand" required>
                        </div>

                    </div> 

                     
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Company Name (Trade Name)</label>
                            <span style="color:red;">*</span>
                            <input type="text" id="company_name" name="company_name" placeholder="Company Name"
                                autocomplete="nope" class="mand" required>
                                 
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Nature of Business</label>
                            <span style="color:red;">*</span>
                            <select id="patient_type" name="patient_type" class="mand" required>
                                <option value="">--Select Nature of Business--</option> 
                                <?php 
                                    $this->db->select('*')
                                             ->from('patient_type')
                                             ->where('status','1');
                                    $query = $this->db->get();
                                    $res = $query->result();

                                    foreach($res as $patient_type){?>   
                                    <option value="<?php echo $patient_type->patient_id;?>"> <?php echo $patient_type->patient_type;?> </option>    

                                    <?php }?>



                            </select>
                        </div>
                    </div>
                   
                                   
                    
                        <div class="col-sm-12">
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="field-2" class="control-label">Title</label>
                                    <span style="color:red;">*</span>
                                    <span id="error_title" style="color:red;"></span>
                                    <select name="title" id="title" class="mand">
                                        <option value="Mr.">Mr.</option>
                                        <option value="Mrs.">Mrs.</option>
                                        <option value="Miss.">Miss.</option>
                                    </select>

                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">

                                    <label for="field-2" class="control-label">Customer Name(Decision Maker)</label>

                                    <span style="color:red;">*</span>

                                    <span id="error_cust_name" style="color:red;"></span>

                                    <input type="text" id="cust_name" name="cust_name" placeholder="Customer Name"
                                        class="mand" autocomplete="nope">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-md-4 col-4">
                                <div class="form-group">
                                    <label for="field-2" class="control-label">Country Code</label>
                                    <span style="color:red;">*</span>
                                    <input type="text" name="country_code" value="91" >
                                </div>
                            </div>
                            <div class="col-md-8 col-8">
                                <div class="form-group">
                                    <label for="field-2" class="control-label">Contact Number</label>
                                    <span style="color:red;">*</span>
                                    <span id="error_mobile_no" style="color:red;"></span>
                                    <input type="number" id="mobile_no" name="mobile_no" class="form-control mob mand" value=""
                                        placeholder="Contact No" autocomplete="nope" onblur="validatedigit()">
                                </div>
                            </div>
                           
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">

                            <label for="field-2" class="control-label">Email ID</label>

                            <span id="error_email_id" style="color:red;"></span>

                            <input type="text" id="email_id" name="email_id" placeholder="Email ID" 
                                autocomplete="nope">

                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="field-2" class="control-label">Postal Address</label>
                            <span style="color:red;">*</span>
                            <textarea name="postal_address" id="postal_address" class="mand" placeholder="Postal Address"
                                autocomplete="nope" style="height:186px;"></textarea>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="field-2" class="control-label">City/Area</label>
                            <span style="color:red;">*</span>
                            <input type="text" id="city" name="city" placeholder="City" autocomplete="nope" class="mand">
                        </div>
                    </div>


                    <div class="col-sm-12">
                        <div class="form-group">

                            <label for="field-2" class="control-label">Alternate Contact Person</label>

                            <input type="text" name="alt_contact" id="alt_contact" placeholder="Alternate Contact"
                                autocomplete="nope">

                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="row">

                            <div class="col-md-4 col-4">

                                <div class="form-group">

                                    <label for="field-2" class="control-label">Country
                                        Code</label>

                                    <input type="text" class=" country_code" name="country_code" id="country_code"
                                        value="91" readonly>

                                </div>

                            </div>

                            <div class="col-md-8 col-8">
                                <div class="form-group">
                                    <label for="field-2" class="control-label">Contact Number</label>
                                    <input type="text" name="alt_contact_no" placeholder="Contact Number"
                                        autocomplete="nope">
                                </div>
                            </div>
                            <input name="message1" id="message1" style="overflow:hidden" type="hidden">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">

                            <label for="field-2" class="control-label">E-mail</label>

                            <input type="email" name="email" id="email" placeholder="E-mail" autocomplete="nope">

                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">

                            <label for="field-2" class="control-label">Remarks (If Any..) </label>


                            <textarea name="spacification" id="spacification" placeholder="Enter Remarks"
                                autocomplete="nope" style="height:187px;"></textarea>



                        </div>
                    </div>


                    <div class="col-md-12">
                          <div class="form-group">
                            <label for="field-1" class="control-label">Attachment</label>
                            <div class="col-sm-3">
                                <input type="file" name="attach[]" id="attach" class="form-control" multiple>
                            
                            </div>

                        </div>
                    </div>


                     <div class="col-md-12">
                          <div class="form-group">
                            <label for="field-1" class="control-label">Next Visit Date <span style='color:red'>*</span> </label>
                            <div class="col-sm-3">
                                <input type="date" name="followup_date" id="followup_date" class="form-control" min="<?php echo date('Y-m-d');?>" required>
                            </div>

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
 $(".customername").select2();  
     jQuery('#create_date').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: 'dd-mm-yyyy',
        startDate: "-2d",
        endDate: '+0d'

     });

 });


    function enable_stop() {
        $("#record").attr('disabled', true);
        $("#stopRecord").attr('disabled', false);

        navigator.getUserMedia({audio: true}, function(stream) { /* do stuff */ });
    }

    function getProductsOfCompany(j) {
        var company_location = $("#company_location").val();

                 $.ajax({
                    type:"post",
                    url:"<?php echo page_url;?>Open_leads/getCompanyProducts",
                    data:{company_location:company_location},

                    success:function(data) {
                        $(".product_name"+j).html(data);
                    }
                });
    }

    function getReferralName() {
        $(".referral_name").css('display', 'none');
        $("#referral_name").removeClass('mand');
       if($("#lead_source").val() == 3) {
            $(".referral_name").css('display', '');
            $("#referral_name").addClass('mand');
       }
    }

        function validate_lead() {
            
            var customertype = $("#customertype").val();
            //alert(customertype);

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

    function validatedigit()
      {
          //alert('hi');
         var mob=$(".mob").val();
        var len=mob.length;
        if(len<11 && len>9)
        {
        return true;
        }else
        {
        alert('Mobile No should be 10 Digits.');
        var mob=$(".mob").val('');
        }
      }
</script>

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-36251023-1']);
  _gaq.push(['_setDomainName', 'jqueryscript.net']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<script>
try {
  fetch(new Request("https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js", { method: 'HEAD', mode: 'no-cors' })).then(function(response) {
    return true;
  }).catch(function(e) {
    var carbonScript = document.createElement("script");
    carbonScript.src = "//cdn.carbonads.com/carbon.js?serve=CK7DKKQU&placement=wwwjqueryscriptnet";
    carbonScript.id = "_carbonads_js";
    document.getElementById("carbon-block").appendChild(carbonScript);
  });
} catch (error) {
  console.log(error);
}
</script>

<script type="text/javascript">
                var j=1;

            $('.add_more').click(function(){
                getProductsOfCompany(j);
            $('.productss').append('<div class="row fieldGroups"> <div class="col-md-4"> <div class="form-group"> <label for="field-1" class="control-label">Competitor Product</label> <span style="color:red;">*</span><select name="competitor_product[]" id="competitor_product'+j+'" class="form-control mand" onchange="getourproductname('+j+');" required></select></div></div><div class="col-md-2"><div class="form-group"><label for="field-1" class="control-label">Qty</label><span style="color:red;">*</span><input type="number" name="qty[]" id="qty'+j+'" class="form-control mand" min="1" required></div></div><div class="col-md-2"><div class="form-group"><label for="field-1" class="control-label">Pack Size</label><span style="color:red;">*</span><select name="pack_size[]" id="pack_size'+j+'" class="form-control mand" required><option value="">Select</option><option value="1">Drum</option><option value="2">Bucket</option><option value="3">Bulk</option><option value="4">Cans</option><option value="5">Kgs</option></select></div></div><div class="col-md-3"><div class="form-group"> <label for="field-1" class="control-label">Our Product</label><span style="color:red;">*</span> <span id="error_product" style="color:red;"></span><select class="product_name'+j+' mand" id="product_name'+j+'"  name="products[]" required> </select><span id="recommendation'+j+'"></span></div></div><div class="col-md-1"> <a href="javascript:void(0)" class="btn btn-danger btn-xs remove" style="margin-top: 32px;"><i class="fa fa-remove" aria-hidden="true"></i></a> </div></div>');
            initializeSelect2("competitor_product"+j);
            initializeSelect2_product("product_name"+j);
            j++;
            });

            $(document).on('click', '.remove', function(){
                $(this).parents(".fieldGroups").remove();
            });

</script>
<script type="text/javascript">
    
$(document).ready(function(){
var purl="<?php echo page_url;?>Open_leads/getrecommendations";
$('#competitor_product0').select2({ 
        placeholder: 'TYPE TO SELECT',
        minmumInputLength:4,
        allowClear: true,
        tags:true,
        ajax: {
        url: purl,
        dataType: 'json',
        delay: 250,
        data: function (params) {

        return {
        searchTerm: params.term
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


$('#product_name0').select2({ });
});



 function initializeSelect2(selectElementObj) {
   
    var purl="<?php echo page_url;?>Open_leads/getrecommendations";

            $('#'+selectElementObj).select2({ 
            placeholder: 'TYPE TO SELECT',
            minmumInputLength:4,
            allowClear: true,
            tags:true,
            ajax: {
            url: purl,
            dataType: 'json',
            delay: 250,
            data: function (params) {
            return {
            searchTerm: params.term
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
         
      }


function initializeSelect2_product(selectElementObj) {

    $('#'+selectElementObj).select2({ });

}
      function getourproductname(id)
      {
        var comproduct=$("#competitor_product"+id).val();

        if(comproduct!='')
        {

            $.ajax({
            type:"post",
            url:"<?php echo page_url;?>Open_leads/getrecommendations_our_product",
            data:"comproduct="+comproduct,
            success:function(data){
                 $("#recommendation"+id).css('font-size','12px');
            $("#recommendation"+id).css('color','red');
            $("#recommendation"+id).css('font-weight','bold');
            $("#recommendation"+id).html(data);
            }
            });

        }

      }


//       function check_gstOldd(gst){
//     if(gst.length != 15){
//         alert("Invalid Length of GSTIN");
//         $("#company_gstn").val('');
//     }else{
//         var state = parseInt(gst.substring(0, 2)); 
//         // FIRST 2 CHARACTERS STATE CODE
//         if(state < 1 || state > 37){
//             alert("Invalid First Two Characters of GSTIN");
//             $("#company_gstn").val('');
//         }
//         // NEXT 10 CHARACTERS PAN NO. VALIDATION
//         var pan = gst.substring(2, 12).toUpperCase();
//         var regex = /[a-zA-Z]{3}[PCHFATBLJG]{1}[a-zA-Z]{1}[0-9]{4}[a-zA-Z]{1}$/;
//         if( !regex.test(pan) ){
//             alert("Invalid GSTIN");
//             $("#company_gstn").val('');
//         }
//         // DEFAULT 14TH CHARACTER 'Z'
//         var char14 = gst[13].toUpperCase();
//         if(char14 != "Z"){
//             alert("14th character of GSTIN should be 'Z'");
//             $("#company_gstn").val('');
//         }
//         // CHECKSUM DIGIT 
//         if(check_gst_checksum(gst.substring(0, 14)) != gst[14]){
//             alert("Invalid GSTIN");
//             $("#company_gstn").val('');
//         }

//         //return true;

//     }
// }


function check_gstnew(gst)
{
    var a=0;
     var statecode = gst.substring(0, 2);
            var pancarno = gst.substring(2, 12);
            var entityNumber = gst.substring(12, 13);
            var defaultZvalue =gst.substring(13, 14);
            var checksumdigit = gst.substring(14, 15);
            if (gst.length != 15) {
                alert('GST Number is invalid');
            
                var a=1;
                           }
            if (pancarno.length != 10) {
                alert('GST number is invalid ');
              
                var a=1;
                
            }
            if (defaultZvalue !== 'Z') {
                alert('GST Number is invalid Z not in Entered Gst Number');
                
                var a=1;
            }

            if ($.isNumeric(statecode)) {
                
            } else {
                alert('Please Enter Valid State Code');
               
                var a=1;
            }

            // if ($.isNumeric(checksumdigit)) {
               
            // } else {
            //     alert('GST number is invalid last character must be digit');
              
            //     var a=1;

            // }


           
            if(a==0)
            {
                check_gst(gst);
            }else
            {
                $("#company_gstn").val('');
            }

}
 function check_gst(gst){

    $("#loading").css('display','none');
    if(gst!='')
    {

    $.ajax({
                    type:"post",
                    url:"<?php echo page_url;?>Open_leads/getGSTData",
                    data:{gst:gst},
                    beforeSend: function() {
                    $("#loading").css('display','');
                
                    },
                    success:function(data) {

                        if(data!='NA')
                        {
                            $("#company_name").val(data);
                        }else
                        {
                            alert('Invalid GST No. Provided');
                            $("#company_name").val('');
                        }
                          $("#loading").css('display','none');
                      
                    }
                });
    }

}
</script>
<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>

</body>

</html>