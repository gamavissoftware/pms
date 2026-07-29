<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Testronix Quotation</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script>
<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
        <link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" >

        <style type="text/css">
            .addMore {
            margin-top: 30px;
            }

            .remove {
            margin-top: 30px;
            }
        </style>

    </head>


    <body>


        <!-- Navigation Bar-->
        <header id="topnav">
          <?php $this->load->view('common/nav-menu');?>
        </header>
        <!-- End Navigation Bar-->


        <div class="wrapper">
            <div class="container-fluid">

                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right">
						  
                               
                            </div>
                           
                             <h4>COMMUNICATION MASTER</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">

								
   <form id="quotation" method="post" action="<?php echo page_url;?>/Quotation/add_communication">
<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12" style="margin-top: 20px;">
                                        
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="field-1" class="control-label">Lead Stages</label>
                        <span id="error_customer_name" style="color:red;"></span>
                        <select class="form-control" name="lead_stage" id="lead_stage">
                            <option value="">---SELECT---</option>
                            <option value="0">QUOTATION SEND</option>
                            <option value="1">ON HOLD</option>
                            <option value="2">DEAD</option>
                            <option value="3">PI SENT</option>
                            <option value="4">HOT LEAD</option>
                            <option value="5">ORDER LOST</option>
                            <option value="6">ORDER CLOSED WON</option>
                        </select>
                    </div>
                </div>
               <div class="col-md-2">
                    <div class="form-group">
                        <label for="field-1" class="control-label">Days</label>
                        <span id="error_customer_name" style="color:red;"></span>
                        <input type="number" class="form-control" name="days" id="customer_name" value="">
                    </div>
                    </div>
					
					<div class="col-md-2">
                    <div class="form-group">
                    <label for="field-2" class="control-label">Include Product PDF</label><br>
                    <span id="error_export" style="color:red;"></span>
                    <input type="checkbox" name="include_pdf" id="customer_name" value="1">
                    </div>
                    </div>

                    <div class="col-md-2">
                    <div class="form-group">
                    <label for="field-2" class="control-label">Include Quotation</label><br>
                    <span id="error_export" style="color:red;"></span>
                    <input type="checkbox" name="include_quotation" id="customer_name" value="2">
                    </div>
                    </div>

                    <div class="col-md-2">
                    <div class="form-group">
                    <label for="field-2" class="control-label">Include PI</label><br>
                    <span id="error_export" style="color:red;"></span>
                    <input type="checkbox" name="include_pi" id="customer_name" value="3">
                    </div>
                    </div>    
                    </div>

                    <div class="row" id="repeat">
                        <div class="col-md-12">
                        <div class="col-md-2">
                            <div class="form-group fieldGroup">
                                <label for="field-1" class="control-label">Designation</label>
                                <select class="form-control" name="designation[]">
                                    <option value="">---SELECT---</option>
                                <?php $sql=$this->db->select("*")
                                                    ->from("designation_master")
                                                    ->where("status", 1)
                                                    ->get(); 
                                    if($sql->num_rows() > 0) { 
                                        foreach($sql->result() as $rows) {?> 
                                            <option value="<?php echo $rows->id;?>"><?php echo $rows->designation;?></option>
                                        <?php } }?>
                                </select>
                               <span style="color:red;"></span>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="field-2" class="control-label">Email Body</label>
                                <span id="error_address" style="color:red;">For title, use "customer_title". For name, use "customer_name". For company name, use "company_name". For Contact Number, use "contact_number". For Email, use "email_id". For Product Name, use "product_name". </span>
                                <textarea class="form-control ckeditor" name="email_body[]"></textarea>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <a href="javascript:void(0)" class="btn btn-warning addMore"><span class="glyphicon glyphicon glyphicon-plus" aria-hidden="true"></span></a>
                        </div>
                    </div>
                    </div>

                    <div class="row">
                        <div class="col-md-10" style="margin-top:30px">
                            <div class="form-group pull-left">
                            <select class="form-control" name="status">
                                <option value="1">ACTIVE</option>
                                <option value="0">INACTIVE</option>
                            </select> 
                            </div>
                        </div>
                        <div class="col-md-2" style="margin-top:30px">
                            <div class="form-group pull-right">
                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                            </div>
                        </div>
                    </div>
                                </div>
                                </form>
                                   

                                </div>

                            </div>
                            <!-- end row -->
                        </div> <!-- end ard-box -->
                    </div><!-- end col-->

                </div>
                <!-- end row -->
 

                <!-- Footer -->
<?php $this->load->view('common/footer');?>
                <!-- End Footer -->

            </div> <!-- end container -->
        </div>
        <!-- end wrapper -->


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

        <script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
		
        <script> $(document).ready(function() {


                $("#datepicker1").datepicker();
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
                })



               


            });</script>
            
		<script type="text/javascript">
         $(document).ready(function(){
            CKEDITOR.replaceClass = 'ckeditor';

             // $.ajax({
             //    type:"post",
             //    url:"<?php echo page_url;?>Master/Testronix/getDesignations",
             //    // data:{item:item},
             //    success:function(data){
             //        //alert(data);
             //    $(".designation").html(data);
             //    }
             //    });
            });

         var i=1;
            $('.addMore').click(function(){
                i++;
                $('#repeat').append('<div class="row fieldGroup" id="row'+i+'"><div class="col-md-2"><div class="form-group fieldGroup"><label for="field-1" class="control-label">Designation</label><select class="form-control designation" name="designation[]"><option value="">---SELECT---</option><?php $sql=$this->db->select("*")->from("designation_master")->where("status", 1)->get(); if($sql->num_rows() > 0) { foreach($sql->result() as $rows) { ?> <option value="<?php echo $rows->id;?>"><?php echo $rows->designation;?></option><?php } } ?></select><span style="color:red;"></span></div></div><div class="col-md-8"><div class="form-group"><label for="field-2" class="control-label">Email Body</label><span id="error_address" style="color:red;">For title, use "customer_title". For name, use "customer_name". For company name, use "company_name". For Contact Number, use "contact_number". For Email, use "email_id". For Product Name, use "product_name". </span><textarea class="form-control ckeditor'+i+'" name="email_body[]"></textarea></div></div><div class="col-md-2"> <a href="javascript:void(0)" class="btn btn-danger remove"><span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span></a> </div></div>');
                    initializeckeditor(i);
                });

                $(document).on('click', '.remove', function(){
                    $(this).parents(".fieldGroup").remove();
                    });

                function initializeckeditor(i) {
                    var ck = $(this).find('.ckeditor'+i);
                    ck.ckeditor();
                    // CKEDITOR.replace('ckeditor'+i);
                }
      </script> 


<script language="javascript" type="text/javascript">   

$(document).ready(function() {
$('.select3').select2({ });
$("#save").click(function() {
var customer_name = $("#customer_name").val();
if(customer_name=='')
{
	$("#error_customer_name").html('Required !');
}else{
   $("#error_customer_name").html(''); 
}
var quotation_date = $("#datepicker1").val();
if(quotation_date == '')
{
	
	$("#error_quotation_date").html('Required !');
}else{
   $("#error_quotation_date").html(''); 
}

var reference_number = $("#reference_number").val();
if(reference_number == '')
{
    
    $("#error_reference_number").html('Required !');
}else{
   $("#error_reference_number").html(''); 
}

var subject = $("#subject").val();
if(subject == '')
{
    
    $("#error_subject").html('Required !');
}else{
   $("#error_subject").html(''); 
}

var greetingtitle = $("#greetingtitle").val();
if(greetingtitle=='')
{
    
    $("#error_greeting_title").html('Required !');
}else{
   $("#error_greeting_title").html(''); 
}

var address = $("#address").val();
if(address=='')
{
    
    $("#error_address").html('Required !');
}else{
   $("#error_address").html(''); 
}

var product = $("#product").val();
if(product=='')
{
    
    $("#error_product").html('Required !');
}else{
   $("#error_product").html(''); 
}

var productspecification = $("#productspecification").val();
if(productspecification=='')
{
    
    $("#error_product_specification").html('Required !');
}else{
   $("#error_product_specification").html(''); 
}

var productquantity = $("#productquantity").val();
if(productquantity=='')
{
    
    $("#error_product_quantity").html('Required !');
}else{
   $("#error_product_quantity").html(''); 
}

var terms = $("#terms").val();
if(terms=='')
{
    
    $("#error_terms").html('Required !');
}else{
   $("#error_terms").html(''); 
}




if(customer_name=='' || quotation_date=='' || reference_number=='' || subject=='' || greetingtitle=='' ||  address=='' || product=='' || productspecification=='' || productquantity=='' || terms=='')
{
	
	return false;
}

});
});
</script>
<script>

	  function initializeSelect2(selectElementObj) {
		$('.'+selectElementObj).select2({
			
		 });
      }

	  </script>

 <script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
    </body>
</html>