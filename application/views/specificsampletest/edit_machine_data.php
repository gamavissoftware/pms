<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Sample Testing</title>

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
<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
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
table.manglesh thead th {
				background: #003366;
				color:#fff;
				font-size:11px;
				font-weight:bold;
			}
table tbody tr td {
  font-size: 11px;
  color:#000;
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
</style>
    </head>
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
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
						
                           
                            <h4 class="page-title">SAMPLE TESTING</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
		<div class="row">
			 <form id="loginForm" method="post" action="<?php echo page_url;?>Salestool/addcommunication">
  <div id="pageloader">
   <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
</div>
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">SAMPLE TEST</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
											
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Company Name</label>
														<span id="error_holidayname" style="color:red;"></span>
                                                       <input type="text" name="company" id="company" class="form-control" required>
													   
                                                    </div>
                                                </div>
												
												 <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Contact Person Name</label>
														<span id="error_holidayname" style="color:red;"></span>
                                                       <input type="text" name="company" id="company" class="form-control" required>
													   
                                                    </div>
                                                </div>
												
												
												 <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Email</label>
														<span id="error_holidayname" style="color:red;"></span>
                                                       <input type="text" name="company" id="company" class="form-control" required>
													   
                                                    </div>
                                                </div>
												
												
												 <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Mobile</label>
														<span id="error_holidayname" style="color:red;"></span>
                                                       <input type="text" name="company" id="company" class="form-control" required>
													   
                                                    </div>
                                                </div>
												
												
												 <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Address</label>
													 <textarea name="address" id="address" class="form-control" required></textarea>
													   
                                                    </div>
                                                </div>
												
												
													 <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Machine</label>
														<span id="error_holiday_days" style="color:red;"></span>
                                                        <select class="form-control select2" name="status" id="status">
															<option value="1" selected>Active</option>
															<option value="0">Inactive</option>

														</select>
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Testing Image</label>
														<span id="error_holiday_days" style="color:red;"></span>
                                                        <input type="file" name="videourl" id="videourl" class="form-control" required>
                                                    </div>
                                                </div>
												
													<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Testing Video</label>
														
														 <input type="text" name="videourl" id="videourl" class="form-control" required>
                                                    </div>
                                                </div>
												
												<div style="clear:both;height:10px;"></div>
												
												 <h4 style="text-align:center">SAMPLE DETAILS</h4>
												 <hr>
												
											<div class="row">
											<div class="col-md-12">
												<div class="col-md-4">
												<div class="form-group">
												<label for="field-1" class="control-label">SAMPLE NAME</label>
												<input type="text" name="sample" id="sample" class="form-control" required>
												</div>

												</div>
												</div>
												</div>
												
												<div class="row">
												<div class="col-md-12">
												<div class="col-md-4">
												<div class="form-group">
												<label for="field-1" class="control-label">NO.1</label>
												<input type="text" name="company" id="company" class="form-control" required>
												</div>

												</div>
												
												<div class="col-md-2">
												<div class="form-group">
												<label for="field-1" class="control-label">NO.1</label>
												<input type="text" name="company" id="company" class="form-control" required>
												</div>

												</div>
												
												<div class="col-md-6">
												<div class="form-group">
												<label for="field-1" class="control-label">NO.2</label>
												<input type="text" name="company" id="company" class="form-control" required>
												</div>

												</div>
												<div class="col-md-2">
												<div class="form-group">
												<label for="field-1" class="control-label">NO.3</label>
												<input type="text" name="company" id="company" class="form-control" required>
												</div>

												</div>
												<div class="col-md-6">
												<div class="form-group">
												<label for="field-1" class="control-label">NO.4</label>
												<input type="text" name="company" id="company" class="form-control" required>
												</div>

												</div>
												<div class="col-md-6">
												<label for="field-1" class="control-label">NO.5</label>
												<input type="text" name="company" id="company" class="form-control" required>

												</div>
												<div class="col-md-6">
												<input type="text" name="company" id="company" class="form-control" required>

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
		


		
 <script>

$(document).ready(function(){
$('.select2').select2({ });
$('.select3').select2({ });


 var i=1;
 $('#addmore_btn1').click(function(){

 
 $('#dynamicmodel').append('<div id="row'+i+'" class="dynamicrowformodel"><div class="col-md-6"><div class="form-group"><label for="field-1" class="control-label">MODEL</label><span id="error_holidayname" style="color:red;"></span><input type="text" class="form-control" id="model'+i+'" style="text-transform: uppercase;" name="model[]" placeholder="" value="" required></div></div><div class="col-md-4"><div class="form-group"><label for="field-1" class="control-label">PDF</label><input type="file" class="form-control" id="attachment'+i+'" style="text-transform: uppercase;" name="attachment[]" placeholder="" value="" required></div></div><div class="col-md-2"><label for="field-1" class="control-label" style="margin-top:45px"></label><span class="btn btn-warning btn_remove" id="'+i+'"><i class="fa fa-minus"></i></span></div></div>');
 
  i++;
 
 });
 
 
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id).remove();
	 
  i--;
 });
 
 
 
 var j=1;
 $('#addmore_btn2').click(function(){

 
 $('#dynamiccapacity').append('<div id="row'+j+'" class="dynamicrowforcapacity"><div class="col-md-10"><div class="form-group"><label for="field-1" class="control-label">CAPACITY</label><span id="error_holidayname" style="color:red;"></span><input type="text" class="form-control" id="capacity'+j+'" style="text-transform: uppercase;" name="capacity[]" placeholder="" value="" required></div></div><div class="col-md-2"><label for="field-1" class="control-label" style="margin-top:45px"></label><span class="btn btn-warning btn_remove1" id="'+j+'"><i class="fa fa-minus"></i></span></div></div>');
 
  j++;
 
 });
 
 
 $(document).on('click', '.btn_remove1', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id).remove();
	 
  j--;
 });
 
 
 
  var k=1;
 $('#addmore_btn3').click(function(){

 
 $('#dynamiccompany').append('<div id="row'+k+'" class="dynamicrowforcapacity"><div class="col-md-4"><div class="form-group"><label for="field-1" class="control-label">COMPANY</label><span id="error_holidayname" style="color:red;"></span><input type="text" class="form-control" id="company'+k+'" style="text-transform: uppercase;" name="company[]" placeholder="" value="" required></div></div><div class="col-md-3"><div class="form-group"><label for="field-1" class="control-label">LOCATION</label><span id="error_holidayname" style="color:red;"></span><input type="text" class="form-control" id="location'+k+'" style="text-transform: uppercase;" name="location[]" placeholder="" value="" required></div></div><div class="col-md-3"><div class="form-group"><label for="field-1" class="control-label">INDUSTRY</label><span id="error_holidayname" style="color:red;"></span><input type="text" class="form-control" id="industry'+k+'" style="text-transform: uppercase;" name="industry[]" placeholder="" value="" required></div></div><div class="col-md-2"><label for="field-1" class="control-label" style="margin-top:45px"></label><span class="btn btn-warning btn_remove1" id="'+k+'"><i class="fa fa-minus"></i></span></div></div>');
 
  k++;
 
 });
 
 
 $(document).on('click', '.btn_remove2', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id).remove();
	 
  k--;
 });
 
});


function checkfornewmodel()
{
	if($('#newmodelcheck').is(":checked"))
	{
		
		$(".newmodel").css('display','');
		$(".newmodel :input").attr('required',true);
		
		
	}else{
		
		$(".newmodel").css('display','none');
		$(".newmodel :input").attr('required',false);
		
	}
	
	
}





function checkfornewcapacity()
{
	
	if($('#newcapacitycheck').is(":checked"))
	{
		
		$(".newcapacity").css('display','');
		$(".newcapacity :input").attr('required',true);
		
		
	}else{
		
		$(".newcapacity").css('display','none');
		$(".newcapacity :input").attr('required',false);
		
	}
	
	
}


function checkfornewcompany()
{

	if($('#newcompaycheck').is(":checked"))
	{
		
		$(".newcompany").css('display','');
		$(".newcompany :input").attr('required',true);
		
		
	}else{
		
		$(".newcompany").css('display','none');
		$(".newcompany :input").attr('required',false);
		
	}
	
	
}


</script>


<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>

</body>
</html>