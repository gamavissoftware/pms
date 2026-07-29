<?php //echo "<pre>";print_r($editQuoteHistory);exit;
foreach ($editQuoteHistory as $quoteHistory);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> CALIBRATION,AMC,VISIT CHARGES</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
<link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
		<link href="<?php echo assets_url;?>plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
  text-align:center;
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
  left: 30%;
  margin-left: -10px;
  margin-top: -10px;
  position: absolute;
  top: 30%;
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
            <div class="container">

                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="btn-group pull-right">
                              
                            </div>
                            <h4 class="page-title">CALIBRATION,AMC,VISIT CHARGES QUOTES</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->

				<form action="<?php echo page_url;?>Calibration/updateQuote" method="post">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-7 col-xs-7 col-md-7">
								<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
								
								
								<div class="col-md-12">
								
								<!-- <div class="col-md-6">
								<div class="form-group">
								<label for="field-1" class="control-label">Quote Type</label>
								<span id="error_order_type" style="color:red;">*</span>
								<select name="qtype" id="qtype" class="form-control">
								<option value="">SELECT TYPE</option>
								<option value="1">AMC</option>
								<option value="2">CALIBRATION</option>
								<option value="3">VISIT CHARGE</option>
							
								</select>
								</div>
								</div> -->
								
								
								<div class="col-md-6">
								<div class="form-group">
								<label for="field-1" class="control-label">Company Name</label>
								<span id="error_order_type" style="color:red;">*</span>
								<input type="text" name="company" id="company" class="form-control" value="<?php echo $quoteHistory->company;?>">
								</div>
								</div>
								
								<!-- <div class="col-md-6">
								<div class="form-group">
								<label for="field-1" class="control-label">Company Address</label>
								<span id="error_order_type" style="color:red;">*</span>
								<textarea type="text" name="companyaddress" id="companyaddress" class="form-control"></textarea>
								</div>
								</div> -->
								
								<div class="col-md-6">
								<div class="form-group">
								<label for="field-1" class="control-label">Person Name</label>
								<span id="error_person_name" style="color:red;">*</span>
								<input type="text" name="person_name" id="person_name" class="form-control" value="<?php echo $quoteHistory->person_name;?>">
								</div>
								</div>
								
								<div class="col-md-6">
								<div class="form-group">
								<label for="field-1" class="control-label">Contact Number</label>
								<span id="error_contact_number" style="color:red;">*</span>
								<input type="text" name="contact_number" id="contact_number" class="form-control" value="<?php echo $quoteHistory->contact_number;?>">
								</div>
								</div>
								<div class="col-md-6">
								<div class="form-group">
								<label for="field-1" class="control-label">Email</label>
								<span id="error_contact_number" style="color:red;">*</span>
								<input type="text" name="email" id="email" class="form-control" value="<?php echo $quoteHistory->email;?>">
								</div>
								</div>
								
									
								<!-- <div class="col-md-6">
								<div class="form-group">
								<label for="field-1" class="control-label">City</label>
								<span id="error_order_type" style="color:red;">*</span>
								
								<select class="form-control mand select23" name="city" id="city0"  onChange="getstate(0);">
								
								</select>
								
								</div>
								</div>
								
								<div style="clear:both;height:10px;"></div>

								<div class="col-md-6">
								<div class="form-group">
								<label for="field-1" class="control-label">State</label>
								<span id="error_order_type" style="color:red;">*</span>
								<select class="form-control mand select24" name="state" id="state0">
								</select>
								</div>
								</div> -->
								
								
							
								</div>
								
								
								
								<!-- <div class="col-md-12">
								<div class="col-md-6">
									<div class="form-group">
									<label for="field-1" class="control-label">Machine </label>
									<span id="error_order_type" style="color:red;">*</span>
									<select class="form-control mach sele" name="machine" id="machine0" >
									<option value="">Select Machine</option>
								
									</select>
									</div>
									</div>
									<div clas="col-md-2" style="margin-top:25px">
									<span class="btn btn-warning btn-xs" id="addmore_btn2"><i class="fa fa-plus"></i></span>
									
									</div>
									</div> -->
									
									<div id="dynamicdata">
									
									
									</div>
									
								
								
							
                                 <div style="clear:both;height:15px"></div>
								<div class="col-md-12 text-center">
								<!-- <span class="btn btn-success btn-xs">Update Quote</span> -->
								<input type="hidden" name="quote_id" value="<?php echo $quoteHistory->id;?>">
								<button type="submit" class="btn btn-success btn-xs">Update Quote</button>
								</div>
                                </div>

							<div class="col-sm-5 col-xs-5 col-md-5" id="table">
							
								<div id="fquote"><h2 class="text-center" style="display:none"></h2></div>
							
							</div>
                            </div>
                            <!-- end row -->
                        </div> <!-- end ard-box -->
                    </div><!-- end col-->

                </div>
				</form>
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
		<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
		<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<script language="javascript" type="text/javascript">   

$( document ).ready(function() {
var j=1;
$('#addmore_btn2').click(function(){


$('#dynamicdata').append('<div class="col-md-12 row'+j+'"><div class="col-md-6"><div class="form-group"><label for="field-1" class="control-label">Machine </label><span id="error_order_type" style="color:red;">*</span><select class="form-control mach mand select3'+j+'" name="machine" id="machine'+j+'"></select></div></div><div clas="col-md-2" style="margin-top:25px"><span class="btn btn-warning btn-xs addclose" id="'+j+'"><i class="fa fa-close"></i></span></div></div>');
initializeSelect2('select3'+j);
j++;

});

 $(document).on('click', '.addclose', function(){
 var button_id = $(this).attr("id");
 $('.row'+button_id).remove();
	 
  j--;
 });

});
 
 
</script>

<script>
$( document ).ready(function() {
	
	var purl="<?php echo page_url;?>FMS/getmachines/";

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


var purl1="<?php echo page_url;?>Calibration/getcity/";

$('.select24').select2({});
$('.select23').select2({ 
placeholder: 'TYPE TO SELECT',
minmumInputLength:1,
		allowClear: true,

        ajax: {

          url: purl1,

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

});


</script>
<script>
	  function initializeSelect2(selectElementObj) {
		  var purl="<?php echo page_url;?>FMS/getmachines/";
         $('.'+selectElementObj).select2({
			 
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
         
      }
	  
		function getstate(id)
		{
			var st=$("#city"+id).val();
			$.ajax({
			type:"post",
			url:"<?php echo page_url;?>Calibration/getstate/"+st,
			data:"1",
			success:function(data){

			$("#state0").html(data);

			}
			});
		
		  
		  
	  }
	  
	  function generatequote()
	  {
		  var qtype=$("#qtype").val();
		  var company=$("#company").val();
		  var state=$("#state0").val();
		  var city=$("#city0").val();

			var values = [];
		
					$(".mach").each(function() {
					 values.push($(this).val());
					});
			
			if(values.length>0)
			{
			var ins=values.join(",");
			}else{
				
				var ins='';
			}
			
			
			if(company!='' && state!='' && city !='' && ins!='' && qtype!='')
			{
				
				$.ajax({
				type:"post",
				url:"<?php echo page_url;?>Calibration/generatequote/",
				data:"company="+company+"&state="+state+"&city="+city+"&ins="+ins+"&qtype="+qtype,
				beforeSend: function(){
				// Statement
				$("#fquote").html('<h2 class="text-center" style="">Fetching Quote...</h2>');
				$("#fquote").css('display','');
				},
				success:function(data){

				$("#fquote").remove()
				$("#table").html(data);
				}
				});
				

			}else
			{
			alert('All Fields are mandatory');
			}

		  
	  }
	  
	  
	 
	  </script>
</body>
<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>


</html>
