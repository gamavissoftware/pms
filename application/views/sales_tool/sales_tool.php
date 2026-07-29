<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Salestool</title>

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
        <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
		<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
    <style>
table.manglesh thead th {
				background: #003366;
				color:#fff;
				font-weight:bold;
			}
			
#pageloader
{
  background: rgba( 255, 255, 255, 0.8 );
  display: none;
  height: 100%;
  position: fixed;
  width: 100%;
  z-index: 9999;
}
#pageloader img
{
  left: 50%;
  margin-left: -32px;
  margin-top: -32px;
  position: absolute;
  top: 50%;
}

.addPromotion {
font-size: 20px;
margin-top: 20px;
color: #808080;
}

.addPromotion1 {
font-size: 20px;
margin-top: 20px;
color: #808080;
}

.addDetails {
font-size: 20px;
margin-top: 20px;
color: #808080;
}

.addDetails1 {
font-size: 20px;
margin-top: 20px;
color: #808080;
}

.addCharges {
font-size: 20px;
margin-top: 20px;
color: #808080;
}

.addCharges1 {
font-size: 20px;
margin-top: 20px;
color: #808080;
}

.removePromotion {
font-size: 20px;
margin-top: 20px;
color: #808080;
}

.removeDetails {
font-size: 20px;
margin-top: 20px;
color: #808080;
}

.removeCharges {
font-size: 20px;
margin-top: 20px;
color: #808080;
}

#country-list li {
    padding: 10px;
    background: #f0f0f0;
    border-bottom: #bbb9b9 1px solid;
    width: 476px;
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
 <div class="card-box">
    <?php echo $this->session->flashdata('message'); ?>
    <h4 style="text-align: left;">Machine Details</h4>
 <form method="post" autocomplete="off" action="<?php echo page_url;?>Sales_tool/add_details" id="frm" enctype="multipart/form-data">
    <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="field-1" class="control-label">Machine Name</label>
                    <span id="error_order_type" style="color:red;">*</span>
                    <select class="form-control sele" onchange="getNamePrice()"></select>
                     <input type="hidden" id="machine_name" name="machine_name" class="form-control">
                </div>
            </div> 
        <div id="suggestion-box"></div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="field-1" class="control-label">Price</label>
                <span id="error_datepicker1" style="color:red;"></span>
                <input type="text" id="machine_price" name="price" class="form-control" onkeypress="return isNumberKey(event,this)">
            </div>
        </div> 
         
        
        
        <div class="col-md-2">
            <div class="form-group">
                <label for="field-1" class="control-label">Max Discount Capping in %</label>
                <span id="error_datepicker1" style="color:red;"></span>
                <input type="text" id="discount" name="discount" class="form-control" onkeypress="return isNumberKey(event,this)" onkeyup="calculate_discount()">
            </div>
        </div> 

         <div class="col-md-2">
            <div class="form-group">
                <label for="field-1" class="control-label">Discounted Price</label>
                <span id="error_datepicker1" style="color:red;"></span>
                <input type="text" id="discounted_price" name="discounted_price" class="form-control" readonly>
            </div>
        </div> 

        <div class="col-md-2">
            <div class="form-group">
                <label for="field-1" class="control-label">PDF</label>
                <span id="error_datepicker1" style="color:red;"></span>
                <input type="file" id="pdf_file" name="pdf_file" class="form-control">
            </div>
        </div> 

        <div class="col-md-2">
            <div class="form-group">
                <label for="field-1" class="control-label">Video</label>
                <span id="error_datepicker1" style="color:red;"></span>
                <input type="text" id="video" name="video" class="form-control">
            </div>
        </div> 
    </div>

    <div class="row">
         <div class="col-md-12">
            <div class="form-group">
                <label for="field-1" class="control-label">USP</label>
                <span id="error_datepicker1" style="color:red;"></span>
                <textarea class="form-control" name="usp" id="usp" placeholder="" required></textarea>
                <script>CKEDITOR.replace( 'usp' );</script>
            </div>
        </div>
    </div>
        <hr>
        <h4 style="text-align: left;">Promotion</h4>
        <div class="row" id="promotion">
         <div class="col-md-2">
            <div class="form-group">
                <label for="field-1" class="control-label">Name</label>
                <span id="error_order_type" style="color:red;">*</span>
                 <input type="text" id="promotion_name" name="promotion_name[]" class="form-control">
            </div>
            </div> 
            <div class="col-md-2">
                <div class="form-group fieldGroup">
                    <label for="field-1" class="control-label">Price</label>
                    <span id="error_username" style="color:red;">*</span>
                    <input type="text" id="promotion_price0" name="promotion_price[]" class="form-control mand" autocomplete="off" value="" onkeypress="return isNumberKey(event,this)" required="">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group fieldGroup">
                    <label for="field-1" class="control-label">Description</label>
                    <span id="error_username" style="color:red;">*</span>
                    <textarea class="form-control description" name="description[]" id="description" placeholder="" required></textarea>
                    <!-- <script>CKEDITOR.replace( 'description' );</script> -->
                </div>
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0)" class="" onclick="appendpromotions(0);"><i class="fa fa-plus addPromotion" aria-hidden="true"></i></a>
            </div>
        </div>
        <hr>
        <h4 style="text-align: left;">Client Details</h4>
        <div class="row" id="details">
            <div class="col-md-2">
            <div class="form-group">
                <label for="field-1" class="control-label">Client Name</label>
                <span id="error_order_type" style="color:red;">*</span>
                 <input type="text" id="client_name" name="client_name[]" class="form-control">
            </div>
            </div>
         <div class="col-md-2">
            <div class="form-group">
                <label for="field-1" class="control-label">City</label>
                <span id="error_order_type" style="color:red;">*</span>
                 <input type="text" id="city0" name="city[]" class="form-control">
            </div>
            </div> 
            <div class="col-md-3">
                <div class="form-group fieldGroup">
                    <label for="field-1" class="control-label">Industry</label>
                    <span id="error_username" style="color:red;">*</span>
                    <input type="text" id="industry0" name="industry[]" class="form-control mand" autocomplete="off" value="" required="">
                </div>
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0)" class=""><i class="fa fa-plus addDetails" aria-hidden="true"></i></a>
            </div>
        </div>
        <hr>
           <h4 style="text-align: left;">Miscelleneous Charges</h4>
        <div class="row">
         <div class="col-md-4">
            <div class="form-group">
                <label for="field-1" class="control-label">Name</label>
                <span id="error_order_type" style="color:red;">*</span>
                 <input type="text" id="charges_name0" name="charges_name[]" class="form-control" value="Installation" readonly>
            </div>
            </div> 
            <div class="col-md-3">
                <div class="form-group fieldGroup">
                    <label for="field-1" class="control-label">Charges</label>
                    <span id="error_username" style="color:red;">*</span>
                    <input type="text" id="charges0" name="charges[]" class="form-control mand" autocomplete="off" value="1000" readonly>
                <span style="color:red;"></span>
                </div>
            </div>
        </div>

           <div class="row">
         <div class="col-md-4">
            <div class="form-group">
                <label for="field-1" class="control-label">Name</label>
                <span id="error_order_type" style="color:red;">*</span>
                 <input type="text" id="charges_name0" name="charges_name[]" class="form-control" value="Freight" readonly>
            </div>
            </div> 
            <div class="col-md-3">
                <div class="form-group fieldGroup">
                    <label for="field-1" class="control-label">Charges</label>
                    <span id="error_username" style="color:red;">*</span>
                    <input type="text" id="charges0" name="charges[]" class="form-control mand" autocomplete="off" value="100" readonly>
                <span style="color:red;"></span>
                </div>
            </div>
        </div>

        <div class="row" id="charges">
         <div class="col-md-4">
            <div class="form-group">
                <label for="field-1" class="control-label">Name</label>
                <span id="error_order_type" style="color:red;">*</span>
                 <input type="text" id="charges_name0" name="charges_name[]" class="form-control">
            </div>
            </div> 
            <div class="col-md-3">
                <div class="form-group fieldGroup">
                    <label for="field-1" class="control-label">Charges</label>
                    <span id="error_username" style="color:red;">*</span>
                    <input type="text" id="charges0" name="charges[]" class="form-control mand" autocomplete="off" value="" required="">
                <span style="color:red;"></span>
                </div>
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0)" class=""><i class="fa fa-plus addCharges" aria-hidden="true"></i></a>
            </div>
        </div>
    <div class="row">
       <div class="col-md-4"> 
            
            <input type="submit" value="Submit" id="sub" class="btn btn-success">
        </div>
    </div>
    </form>
</div>
</div>
                            <!-- /.modal -->


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
        <script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
        <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
		
 <script>
$( document ).ready(function() {
  $(".mand").attr('required', false);

	
});

</script>

<script language="javascript" type="text/javascript">   
function validateme()
{

var shortname1 = $("#shortnames").val();
if(shortname1=='')
{
	$("#error_datepicker1").html('Required!');
	
}

var unitname = $("#unitname").val();
if(unitname=='')
{
	$("#error_holidayname").html('Required!');

}


if(unitname=='' || shortname1=='')
{
	
	return false;
}

}
</script>
<script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
    CKEDITOR.replace(".description")
  });//submit

  var purl="<?php echo page_url;?>Sales_tool/searchMachine";
         $('.sele').select2({
             
            placeholder: 'TYPE TO SELECT',
            minmumInputLength:4,
            allowClear: true,

        ajax: {

          url: purl,

          dataType: 'json',

          delay: 250,
            
          processResults: function (data) {
            return {
              results: data
            };
          },
          cache: true
        }
             
    });


});//document ready

   

var i=1;
$('.addPromotion').click(function(){
   
                $('#promotion').append('<div style="clear:both;height:5px"></div><div class="row fieldGroup" id="row'+i+'"><div class="col-md-2"><div class="form-group"><label for="field-1" class="control-label">Name</label><span id="error_order_type" style="color:red;">*</span><input type="text" id="promotion_name" name="promotion_name[]" class="form-control"></div></div> <div class="col-md-2"><div class="form-group fieldGroup"><label for="field-1" class="control-label">Price</label><span id="error_username" style="color:red;">*</span><input type="text" id="promotion_price" name="promotion_price[]" class="form-control mand" autocomplete="off" value="" onkeypress="return isNumberKey(event,this)" required=""></div></div><div class="col-md-3"><div class="form-group fieldGroup"><label for="field-1" class="control-label">Description</label><span id="error_username" style="color:red;">*</span><textarea class="form-control description" name="description[]" id="description" placeholder="" required></textarea></div></div><div class="col-md-2"><a href="javascript:void(0)" class=""><i class="fa fa-minus removePromotion" aria-hidden="true"></i></a><div class="lastfield"></div></div></div>');

                addplustolastfield();
              i++;   
                });

var j=1;
$('.addDetails').click(function(){
    
                $('#details').append('<div style="clear:both;height:5px"></div><div class="row fieldGroup" id="row'+i+'"><div class="col-md-2"><div class="form-group"><label for="field-1" class="control-label">Client Name</label><span id="error_order_type" style="color:red;">*</span><input type="text" id="client_name" name="client_name[]" class="form-control"></div></div><div class="col-md-2"><div class="form-group"><label for="field-1" class="control-label">City</label><span id="error_order_type" style="color:red;">*</span><input type="text" id="city" name="city[]" class="form-control"></div></div> <div class="col-md-3"><div class="form-group fieldGroup"><label for="field-1" class="control-label">Industry</label><span id="error_username" style="color:red;">*</span><input type="text" id="industry" name="industry[]" class="form-control mand" autocomplete="off" value="" required=""></div></div><div class="col-md-2"> <a href="javascript:void(0)" class=" "><i class="fa fa-minus removeDetails" aria-hidden="true"></i></a><div class="lastfielddetails"></div> </div></div>');
                addplustolastfielddetails();
                j++;
                });

var k=1;
$('.addCharges').click(function(){
                $('#charges').append('<div style="clear:both;height:5px"></div><div class="row fieldGroup" id="row'+i+'"><div class="col-md-4"><div class="form-group"><label for="field-1" class="control-label">Name</label><span id="error_order_type" style="color:red;">*</span><input type="text" id="charges_name" name="charges_name[]" class="form-control"></div></div> <div class="col-md-3"><div class="form-group fieldGroup"><label for="field-1" class="control-label">Charges</label><span id="error_username" style="color:red;">*</span><input type="text" id="charges" name="charges[]" class="form-control mand" autocomplete="off" value="" required=""></div></div><div class="col-md-2"> <a href="javascript:void(0)" class=""><i class="fa fa-minus removeCharges" aria-hidden="true"></i></a> <div class="lastfieldcharges"></div></div></div>');
                addplustolastfieldcharges();
                k++;
                });


                $(document).on('click', '.removePromotion', function(){

                    $(this).parents(".fieldGroup").remove();
                     addplustolastfield1();
                    });

                $(document).on('click', '.removeDetails', function(){
                    $(this).parents(".fieldGroup").remove();
                    addplustolastfielddetails1();
                    });

                $(document).on('click', '.removeCharges', function(){
                    $(this).parents(".fieldGroup").remove();
                    addplustolastfieldcharges1();
                    });


                 $(document).on('click', '.addPromotion1', function(){
                   
                    $('#promotion').append('<div style="clear:both;height:5px"></div><div class="row fieldGroup" id="row'+i+'"><div class="col-md-2"><div class="form-group"><label for="field-1" class="control-label">Name</label><span id="error_order_type" style="color:red;">*</span><input type="text" id="promotion_name" name="promotion_name[]" class="form-control"></div></div> <div class="col-md-2"><div class="form-group fieldGroup"><label for="field-1" class="control-label">Price</label><span id="error_username" style="color:red;">*</span><input type="text" id="promotion_price" name="promotion_price[]" class="form-control mand" autocomplete="off" value="" onkeypress="return isNumberKey(event,this)" required=""></div></div><div class="col-md-3"><div class="form-group fieldGroup"><label for="field-1" class="control-label">Description</label><span id="error_username" style="color:red;">*</span><textarea class="form-control description" name="description[]" id="description" placeholder="" required></textarea></div></div><div class="col-md-2"><a href="javascript:void(0)" class=""><i class="fa fa-minus removePromotion" aria-hidden="true"></i></a><div class="lastfield"></div></div></div>');

                addplustolastfield();

                    });

                  $(document).on('click', '.addDetails1', function(){
    
                $('#details').append('<div style="clear:both;height:5px"></div><div class="row fieldGroup" id="row'+i+'"><div class="col-md-2"><div class="form-group"><label for="field-1" class="control-label">Client Name</label><span id="error_order_type" style="color:red;">*</span><input type="text" id="client_name" name="client_name[]" class="form-control"></div></div><div class="col-md-2"><div class="form-group"><label for="field-1" class="control-label">City</label><span id="error_order_type" style="color:red;">*</span><input type="text" id="city" name="city[]" class="form-control"></div></div> <div class="col-md-3"><div class="form-group fieldGroup"><label for="field-1" class="control-label">Industry</label><span id="error_username" style="color:red;">*</span><input type="text" id="industry" name="industry[]" class="form-control mand" autocomplete="off" value="" required=""></div></div><div class="col-md-2"> <a href="javascript:void(0)" class=""><i class="fa fa-minus removeDetails" aria-hidden="true"></i></a><div class="lastfielddetails"></div> </div></div>');

                addplustolastfielddetails();
                });

                  $(document).on('click', '.addCharges1', function(){
                $('#charges').append('<div style="clear:both;height:5px"></div><div class="row fieldGroup" id="row'+i+'"><div class="col-md-4"><div class="form-group"><label for="field-1" class="control-label">Name</label><span id="error_order_type" style="color:red;">*</span><input type="text" id="charges_name" name="charges_name[]" class="form-control"></div></div> <div class="col-md-3"><div class="form-group fieldGroup"><label for="field-1" class="control-label">Charges</label><span id="error_username" style="color:red;">*</span><input type="text" id="charges" name="charges[]" class="form-control mand" autocomplete="off" value="" required=""></div></div><div class="col-md-2"> <a href="javascript:void(0)" class=""><i class="fa fa-minus removeCharges" aria-hidden="true"></i></a> <div class="lastfieldcharges"></div></div></div>');
                addplustolastfieldcharges();
                k++;
                });


</script>
<!-- <script>;</script> -->

      <script>  

    function isNumberKey(evt, element) {
  var charCode = (evt.which) ? evt.which : event.keyCode
  if (charCode > 31 && (charCode < 48 || charCode > 57) && !(charCode == 46 || charCode == 8))
    return false;
  else {
    var len = $(element).val().length;
    var index = $(element).val().indexOf('.');
    if (index > 0 && charCode == 46) {
      return false;
    }
    if (index > 0) {
      var CharAfterdot = (len + 1) - index;
      if (CharAfterdot > 3) {
        return false;
      }
    }

  }
  return true;
}

function getNamePrice() {
    var machineid = $(".sele").val();
     $.ajax({
            type:"post",
            url:"<?php echo page_url;?>Sales_tool/getNamePrice",
            data:{machineid:machineid},
            success:function(data){
                var arr = data.split('|');
                $("#machine_name").val(arr[0]);
                $("#machine_price").val(arr[1]);


            }
        });
}

function validate()
{
  $("#sub").attr('disabled',true);
  $("#sub").val('Please Wait..');
 var isValid=0;
 $(".mand").each(function() {
   var element = $(this).val();
  if (element=="") {
     
    isValid=1;
   }
});
 
 
 if(isValid==0)
 {
  $("#sub").attr('disabled',true);
  $("#sub").val('Please Wait..');
     return true;
 }else
 {
  $("#sub").attr('disabled',false);
  $("#sub").val('Submit');
     alert('All Fields marked with * are mandatory');
     return false;
 }
    
}

function addplustolastfield()
{
    $('div.lastfield:not(:last)').css('display','none');

    $('div.lastfield:last').html('<a href="javascript:;" class="addPromotion1"><i class="fa fa-plus" aria-hidden="true"></i></a>');

  
}

function addplustolastfield1()
{
    $('div.lastfield:last').html('<a href="javascript:;" class="addPromotion1"><i class="fa fa-plus" aria-hidden="true"></i></a>');
    $('div.lastfield:last').css('display','');

}

function addplustolastfielddetails()
{
    $('div.lastfielddetails:not(:last)').css('display','none');

    $('div.lastfielddetails:last').html('<a href="javascript:;" class="addDetails1"><i class="fa fa-plus" aria-hidden="true"></i></a>');

  
}

function addplustolastfielddetails1()
{
    $('div.lastfielddetails:last').html('<a href="javascript:;" class="addDetails1"><i class="fa fa-plus" aria-hidden="true"></i></a>');
    $('div.lastfielddetails:last').css('display','');

}

function addplustolastfieldcharges()
{
    $('div.lastfieldcharges:not(:last)').css('display','none');

    $('div.lastfieldcharges:last').html('<a href="javascript:;" class="addCharges1"><i class="fa fa-plus" aria-hidden="true"></i></a>');

  
}

function addplustolastfieldcharges1()
{
    $('div.lastfieldcharges:last').html('<a href="javascript:;" class="addCharges1"><i class="fa fa-plus" aria-hidden="true"></i></a>');
    $('div.lastfieldcharges:last').css('display','');

}

function calculate_discount() {
     var machine_price = $("#machine_price").val();
     var machine_name = $("#machine_name").val();
     // alert(machine_price);
     var discount = $("#discount").val();
     // alert(discount);

     var discounted_price = (discount * machine_price)/100;
     $("#discounted_price").val(discounted_price);

        // if ($("#machine_name").val() == null) {
        //     $("#machine_price").val('');
        //     $("#discount").val('');
        //     $("#discounted_price").val('');
        //  }

}

</script>
</body>
</html>
