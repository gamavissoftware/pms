<?php

$CI =& get_instance();
$CI->load->model('Store_model');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>ISSUE BLOCKED ITEMS</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.dataTables.min.css">
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
        <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script>
		<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
	<style>
	.select2-container .select2-selection--single {

    height: 38px !important;
	
	}
	
	.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 35px !important;
	}
	</style>
    </head>
    </head>


    <body>


        <!-- Navigation Bar-->
                <header id="topnav">
         <div class="text-center"><img src="<?php echo assets_url;?>images/logo-1.png" width="20%"><div>
        </header>
        <!-- End Navigation Bar-->


        <div class="wrapper" style="margin-left: 10px;margin-right: 10px;">
            <div class="container-fluid">

                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
						  
                            <h4 class="page-title text-center">ISSUE ITEMS AGAINST JOBCARD</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
	<form action="<?php echo page_url;?>User/searchjobcard" name="frm" onsubmit="return validate();" method="post" autocomplete="nope">
		<div class="card-box table-responsive">
				<div  class="row">
				<div class="col-md-2"></div>
				
				<div class="col-md-8">
				
				<div class="col-md-3">
				<input type="text" name="code" class="form-control" id="code" placeholder="Enter Your Code" required autocomplete="off" autocorrect="off" autocapitalize="off">
				</div>
				
					
				<div class="col-md-3">
				<select name="type" class="form-control"  id="type" onchange="issuetype(this.value);">
				<option value="">Type</option>
				<option value="1">JOBCARD ISSUE</option>
				<option value="2">GENERAL ITEMS ISSUE</option>
				<!--<option value="3">GENERAL ITEM ISSUE</option>-->
				</select>
				</div>
				
				<div class="col-md-3 jobcardissue" style="display:none">
				<select name="jobcard" class="form-control select3"  id="jobcardss"  style="width:100%">
				<option value=""></option>
				</select>
				</div>
				<script>
				function issuetype(vaal)
				{
					if(vaal=='1')
					{

					$(".jobcardissue").css('display','');
					$(".jobcardissue").attr('required',true);

					}else{

					$(".jobcardissue").css('display','none');
					$(".jobcardissue").attr('required',false);
					}

				}
				</script>
				
				<div class="col-md-3">
				<input type="submit" name="sub" class="btn btn-success" style="width:50%">
				</div>
				
				
				</div>
				
				<div class="col-md-2"></div>


				</div>
                </div>
	</form>
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
        <script src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>
<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready



$(document).ready(function() {
 var purl="<?php echo page_url;?>User/getopenjobcardlist";
 $('.select3').select2({
 placeholder: 'TYPE TO SELECT JOBCARD',
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
	
});











function validate()
{
	var isvalid=true;


var code=$("#code").val();
	if(code=='')
	{
	alert('Your Unique Profile Code Required');
	isvalid=false;
	}
	
	
	var type=$("#type").val();
	if(type=='')
	{
	alert('Type to proceed');
	isvalid=false;
	}
	
	if(type=='1')
	{
	var isto=$("#jobcardss").val();
	if(isto=='' || isto=='null')
	{
	alert('Select Jobcard to Proceed');
	isvalid=false;
	}
	}
	
	
	

	if(isvalid==false)
	{
	return false;
	}else{

	return true;
	}
	
}


</script>
</body>
</html>
