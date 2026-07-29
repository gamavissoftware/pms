<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">
		<link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title>Prestogroup</title>
        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
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
table.manglesh thead th {
				background: #003366;
				color:#fff;
				font-weight:bold;
			}
			.ui-datepicker { 
  margin-left: 100px;
  z-index: 1000;
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
            <div class="container-fluid">
                <div style="padding-top:100px"></div>
                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="btn-group pull-right">
                              
                            </div>
                            <h4 class="page-title">Tour Conveyance Form </h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
                 <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">
                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">
								<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
								    <form id="loginForm" method="post" action="<?php echo page_url;?>Sales/sale_service_conveyance_voucher" enctype="multipart/form-data">
								        <input type="hidden" name="visitid" value="<?php echo $this->uri->segment(3);?>">
								    <div id="pageloader">
                                    <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
                                    </div>
                                    
                                            	<div class="col-md-3">
												
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">PURPOSE OF TRIP</label>
														<span id="error_purpose_of_trip" style="color:red;">*</span>
                                                         <select class="form-control" name="purpose_of_trip" id="purpose_of_trip" required>
															<option value="2">SERVICE</option>
															<option value="1">SALE</option>
															
														 </select>
                                                    </div>
                                                </div>
                                                
                                                	 <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">TOUR START DATE</label>
														<span id="error_start_date" style="color:red;">*</span>
														 <input type="text" id="start_date" name="start_date" class="form-control datepicker" value="" required>
                                                    </div>
                                                </div>
                                            
                                             <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">TOUR END DATE</label>
														<span id="error_end_date" style="color:red;">*</span>
														 <input type="text" id="end_date" onChange="gettotaldays();" name="end_date" class="form-control datepicker" value="" required>
                                                    </div>
                                                </div>
                                        
										
												   
											
                                                 <script> 
                                                 
                                                 function gettotaldays(){
													 checkdate();
											 var date1 = $("#start_date").val();
                                        		var date2 = $("#end_date").val();
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Sales/gettotaldays",
													data:"date1="+date1+"&date2="+date2,
													success:function(data){
													    
											$("#total_days_of_trip").val(data);
													}
													});
													

													}

</script> 

<script>
												function checkdate(){
													var selectedate = $("#end_date").val();
													$.ajax({
                                                        type: "post",
                                                        url: "<?php echo page_url; ?>Sales/checkdays/",
                                                        data: "selectedate=" + selectedate,
                                                        success: function(data) {
															if(data>7){
																$("#showremarkdiv").show();
																$("#engineerlateremarks").prop('required',true);
															}else{
																$("#showremarkdiv").hide();
																$("#engineerlateremarks").prop('required',false);
															}
                                                           
                                                        }
                                                    });
												}
												</script>
   <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">TOTAL DAYS OF TRIP</label>
														<span id="error_departure_time" style="color:red;">*</span>
                                                        <input type="number" class="form-control" id="total_days_of_trip" name="total_days_of_trip" style="text-transform: uppercase;" value="" readonly required>
                                                    </div>
                                                </div>
                                                
        <div class="row">
		<input type="hidden" name="rowid" class="rowid" value="0">
												 <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">DATE</label>
														<span id="error_traveldate" style="color:red;">*</span>
														 <input type="text" id="traveldate" name="traveldate[]" class="form-control datepicker" value="">
                                                    </div>
                                                </div>
												 <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">CITY NAME</label>
														<span id="error_start_date" style="color:red;">*</span>
														 <input type="text" id="city_name" name="city_name[]" class="form-control" value="" required>
                                                    </div>
                                                </div>
												 <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">COMPANY NAME</label>
														<span id="error_start_date" style="color:red;">*</span>
														 <input type="text" id="company_name" name="company_name[]" class="form-control" value="" required>
                                                    </div>
                                                </div>
											
											
												<div class="col-md-2">
													<div class="form-group">
														<label>LIVING EXPENSE</label>
														<select class="form-control" name="livingexpense[]" id="livingexpense" onChange="fetch_expense_options(0);">
														<option value="">SELECT EXPENSE TYPE</option>
														<?php 
														$query =$this->db->select('id, options')->from('conveyance_type_options')->where('conveyance_type_id','1')->where('status','1')->get();
														foreach($query->result() as $row){
														?>
														<option value="<?php echo $row->id;?>"><?php echo $row->options;?></option>
														<?php }?>
														</select>
														 
													</div>
												</div>
												
												<div class="col-md-2">
												<div class="form-group">
												<label>AMOUNT</label>
													<input type="number" class="form-control" name="livingexpense_amount[]" id="livingexpense_amount" value="" step="0.2">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														<label>TRAVEL EXPENSE</label>
														<select class="form-control" name="travel_expense[]" id="travel_expense" onChange="fetch_expense_options(0);">
														<option value="">SELECT EXPENSE TYPE</option>
														<?php 
														$query =$this->db->select('id, options')->from('conveyance_type_options')->where('conveyance_type_id','2')->where('status','1')->get();
														foreach($query->result() as $row){
														?>
														<option value="<?php echo $row->id;?>"><?php echo $row->options;?></option>
														<?php }?>
														</select>
														 
													</div>
												</div>
												
												<div class="col-md-2">
												<div class="form-group">
												<label>AMOUNT</label>
													<input type="number" class="form-control" name="travel_expense_amount[]" id="travel_expense_amount" value="" step="0.2">
													</div>
												</div>
												
												<div class="col-md-2">
												<div class="form-group">
												<label>FOOD EXPENSE</label>
													<input type="number" class="form-control" name="foodexpense[]" id="foodexpense" value="" step="0.2">
													</div>
												</div>
												<div class="col-md-2">
												<div class="form-group">
												<label>BILL BREAKUP</label>
													<input type="file" class="form-control" name="bills[]" id="bills" value="">
													</div>
												</div>
												
												<div class="col-md-2">
												<div class="form-group">
												<label>LIVING BILL</label>
													<input type="file" class="form-control" name="livingexpensebill[]" id="livingexpensebill" value="">
													</div>
												</div>
												<div class="col-md-2"><div class="form-group"><label>TRAVELLING BILL</label>
												<input type="file" class="form-control" name="travellingbill[]" id="travellingbill" value=""></div></div>
												<div class="col-md-1" id="hidebutton0">
												<div class="form-group" style="margin-top:25px">
												
												<span id="addnewrow0" class="addnewrow">
												
														</div>
													</div>
												</div>
												
											<div id="dynamictasks"></div>
												
<div class="col-md-12" style="display:none;" id="showremarkdiv">
	<div class="form-group">
		<label>Why are you late? Please specify *</label>
		<input type="text" class="form-control" name="engineerlateremarks" id="engineerlateremarks" value="">
	</div>
</div>												
										<div class="col-md-9"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" id="save" class="btn btn-success" value="Save">
											</div>
										</div>
									
									</form>
                                   
</div>
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
		<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
		<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script language="javascript" type="text/javascript">   
$(document).ready(function() {
$("#save").click(function() {
	
var purpose_of_trip = $("#purpose_of_trip").val();
if(purpose_of_trip=='')
{
	$("#error_purpose_of_trip").html('Required!');
}
var start_date = $("#datepicker").val();
if(start_date=='')
{
	$("#error_start_date").html('Required!');
}
var end_date = $("#datepicker1").val();
if(end_date=='')
{
	
	$("#error_end_date").html('Required!');
}

if(purpose_of_trip=='' || start_date=='' || end_date=='')
{
	
	return false;
}

});
});


	

</script>

<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
var i=1;
$(document).on('click', '#addmore_btn', function(){
 $('#dynamictasks').append('<div id="row'+i+'" class="row"><input type="hidden" name="rowid" class="rowid" value="0"><div class="col-md-2"><div class="form-group"><label for="field-1" class="control-label">DATE</label><span id="error_start_date" style="color:red;">*</span><input type="text" id="traveldate" name="traveldate[]" class="form-control datepicker" value=""></div></div><div class="col-md-2"><div class="form-group"><label for="field-1" class="control-label">CITY NAME</label><span id="error_start_date" style="color:red;">*</span><input type="text" id="city_name" name="city_name[]" class="form-control" value=""></div></div><div class="col-md-2"><div class="form-group"><label for="field-1" class="control-label">COMPANY NAME</label><span id="error_start_date" style="color:red;">*</span><input type="text" id="company_name" name="company_name[]" class="form-control" value=""></div></div><div class="col-md-2"><div class="form-group"><label>LIVING EXPENSE</label><select class="form-control" name="livingexpense[]" id="livingexpense" onChange="fetch_expense_options(0);"><option value="">SELECT EXPENSE TYPE</option><?php $query =$this->db->select('id, options')->from('conveyance_type_options')->where('conveyance_type_id','1')->where('status','1')->get();foreach($query->result() as $row){?><option value="<?php echo $row->id;?>"><?php echo $row->options;?></option><?php }?></select></div></div><div class="col-md-2"><div class="form-group"><label>AMOUNT</label><input type="number" class="form-control" name="livingexpense_amount[]" id="livingexpense_amount" value="" step="0.2"></div></div><div class="col-md-2"><div class="form-group"><label>TRAVEL EXPENSE</label><select class="form-control" name="travel_expense[]" id="travel_expense" onChange="fetch_expense_options(0);"><option value="">SELECT EXPENSE TYPE</option><?php $query =$this->db->select('id, options')->from('conveyance_type_options')->where('conveyance_type_id','2')->where('status','1')->get();foreach($query->result() as $row){?><option value="<?php echo $row->id;?>"><?php echo $row->options;?></option><?php }?></select></div></div><div class="col-md-2"><div class="form-group"><label>AMOUNT</label><input type="number" class="form-control" name="travel_expense_amount[]" id="travel_expense_amount" value="" step="0.2"></div></div><div class="col-md-2"><div class="form-group"><label>FOOD EXPENSE</label><input type="number" class="form-control" name="foodexpense[]" id="foodexpense" value="" step="0.2"></div></div><div class="col-md-2"><div class="form-group"><label>BILL BREAKUP</label><input type="file" class="form-control" name="bills[]" id="bills" value=""></div></div><div class="col-md-2"><div class="form-group"><label>LIVING BILL</label><input type="file" class="form-control" name="livingexpensebill[]" id="livingexpensebill" value=""></div></div><div class="col-md-2"><div class="form-group"><label>TRAVELLING BILL</label><input type="file" class="form-control" name="travellingbill[]" id="travellingbill" value=""></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:3px"><label for="field-1" class="control-label">&nbsp;</label><div class="addnewrow" id="addnewrow'+i+'"></div><div></div></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:24px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div><br/>');
  hideaddnewrowbutton(i);
  i++;
   $('.datepicker').datepicker({
            autoclose: true,
            format: 'dd-mm-yyyy'
        });
 });

 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id+'').remove();
 hideaddnewrowbutton(button_id);
 });
 
 $(document).on('click', '.addrow', function(){	
 var t = $("#rowid").val();
 $('#hidebutton'+t+'').css("display","none");
 });
 
});
	  </script>
<script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready
</script>

 <script> $(document).ready(function() {

                $("#datepicker1").datepicker({
					orientation: 'bottom',
					 dateFormat: "dd-mm-yy"
				});
				$("#datepicker").datepicker({
					orientation: 'bottom',
					 dateFormat: "dd-mm-yy"
				});
				$("#datepicke2").datepicker({
					orientation: 'bottom'
				});
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
					
                })

var rowdata = $(".rowid").length;
var a = parseInt(rowdata)-parseInt(1);
$('.addnewrow:last').html('<button type="button" class="btn btn-warning" name="add" id="addmore_btn"><i class="fa fa-plus"></i></button>');

            });
			function hideaddnewrowbutton(i){
				
				var rowdata = $(".rowid").length;
var a = parseInt(rowdata)-parseInt(1);
$('.addnewrow:last').html('<button type="button" class="btn btn-warning" name="add" id="addmore_btn"><i class="fa fa-plus"></i></button>');
//$("#addnewrow"+a).html('<button type="button" class="btn btn-warning" name="add" id="addmore_btn"><i class="fa fa-plus"></i></button>');
var b = parseInt(i)-parseInt(1);
$("#addnewrow"+b).css('display','none');
$('.addnewrow:last').css('display','');
			}
			</script>
			
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
    <script>
        $('.datepicker').datepicker({
            autoclose: true,
            format: 'dd-mm-yyyy',
            //startDate: new Date()
        });
    </script>
			</body>
</html>