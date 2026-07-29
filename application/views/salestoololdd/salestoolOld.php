<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Prestogroup Sales Tool</title>

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
		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
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
						
                           
                            <h4 class="page-title">SALES TOOL</h4>
                        </div>
                    </div>
                </div>
				
				

                <!-- end page title end breadcrumb -->
	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
		<div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
						<div>
                         <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">COMPANY NAME</label>
														<span id="error_flowname" style="color:red;">*</span>
                                                        <input type="text" class="form-control" id="companyname" name="companyname" style="text-transform: uppercase;" placeholder="" required onkeyup="populatedata();">
                                                    </div>
                                                </div>
												
												  <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">CONTACT PERSON</label>
														<span id="error_flowname" style="color:red;">*</span>
                                                        <input type="text" class="form-control" id="contactperson" name="contactperson" style="text-transform: uppercase;" placeholder="" required onkeyup="populatedata();">
                                                    </div>
                                                </div>
												
												
												  <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">OFFICE CONTACT</label>
														<span id="error_flowname" style="color:red;">*</span>
                                                        <input type="text" class="form-control" id="officecontact" name="officecontact" style="text-transform: uppercase;" placeholder="" required onkeyup="populatedata();">
                                                    </div>
                                                </div>
												
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">MOBILE</label>
														<span id="error_flowname" style="color:red;">*</span>
                                                        <input type="text" class="form-control" id="mobile" name="mobile" style="text-transform: uppercase;" placeholder="" required onkeyup="populatedata();">
                                                    </div>
                                                </div>
												
												
													<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">EMAIL</label>
														<span id="error_flowname" style="color:red;">*</span>
                                                        <input type="email" class="form-control" id="email" name="email" style="text-transform: uppercase;" placeholder="" required onkeyup="populatedata();">
                                                    </div>
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">LOCATION</label>
														<span id="error_flowname" style="color:red;">*</span>
                                                        <input type="text" class="form-control" id="location" name="location" style="text-transform: uppercase;" placeholder="" required onkeyup="populatedata();">
                                                    </div>
                                                </div>
												
													<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">INDUSTRY</label>
														<span id="error_flowname" style="color:red;">*</span>
                                                        <input type="text" class="form-control" id="industry" name="industry" style="text-transform: uppercase;" placeholder="" required onkeyup="populatedata();">
                                                    </div>
                                                </div>
												</div>
												
											<div style="clear:both;"></div>
											<div class="col-md-3">
											<div class="form-group">
											<label for="field-1" class="control-label">MACHINE</label>
											<span id="error_who" style="color:red;">*</span>
											<select class="form-control select3" style="text-transform: uppercase;" name="machine" id="machine">
											<option value="">--SELECT MACHINE--</option>

											</select>
											</div>
											</div>
											
											<div class="col-md-3">
											<div class="form-group">
											<label for="field-1" class="control-label">MODEL</label>
											<span id="error_who" style="color:red;">*</span>
											<select class="form-control select3" style="text-transform: uppercase;" name="model" id="model">
											<option value="">--SELECT MODEL--</option>

											</select>
											</div>
											</div>
											
											
											<div class="col-md-3">
											<div class="form-group">
											<label for="field-1" class="control-label">CAPACITY</label>
											<span id="error_who" style="color:red;">*</span>
											<select class="form-control select3" style="text-transform: uppercase;" name="capacity" id="capacity">
											<option value="">--SELECT CAPACITY--</option>

											</select>
											</div>
											</div>
											
											
												
											<div class="col-md-2">
											<div class="form-group">
											<label for="field-1" class="control-label">SAMPLE TO BE TESTED</label>
											<span id="error_who" style="color:red;">*</span>
											 <input type="text" class="form-control" id="sample" name="sample" style="text-transform: uppercase;" placeholder="" required>
										
											</div>
											</div>
											
											
											<div class="col-md-1">
											<div class="form-group">
											<label for="field-1" class="control-label">&nbsp;</label><br/>
											 <span class="btn btn-warning" id="addmore_btn1"><i class="fa fa-plus"></i></span>
										
											</div>
											</div>
											
											<div id="dynamictasks1"></div>
											
											
												
												
												
												
												


                        </div>
                    </div>
        
                    <div class="col-sm-12">
                        <div class="card-box table-responsive" style="background-color:#134F5C;color:white;font-weight:bold;">
                         <p>Thank you for calling-us <span class="contactperson">Mr. JWALA SINGH</span>,</p>
						 <p>Sir,</p>
						 <p>You wish to test paper with <span class="machinename">BURSTING STRENGTH TESTER</span> Capacity <span class="capacity">40 KGF</span></p>
						 <p>We have different models in this and the difference between these models are :</p>
						 <p><span class="model">PNEUMATIC</span></p>
						 <p><span class="model">DIGITAL</span></p>
						 <p><span class="model">ANALOGUE</span></p>
						 
						 <p>if you are making paper we also supply :</p>
						 <p><span class="machinename">BURSTING STRENGTH TESTER</span></p>
						 <p><span class="machinename">BURSTING STRENGTH TESTER</span> is being used in <span class="location">alwar</span> by :</p>
						 <p>Person Name:</p>
						 <p>MR. HEMANT</p>
						 <p>MR. VIJAY</p>
						 
						 <p>Reconfirming Your Details Mr. <span class="contactperson">JWALA SINGH</span></p>
						 <p>Machine : <span class="machinename">BURSTING STRENGTH TESTER</span></p>
						 <p>Model : <span class="model">COMPUTERISED</span></p>
						 <p>Sample to be tested : <span class="sampletobetested">paper</span></p>
						 <p>Customer Name : <span class="contactperson">JWALA SINGH</span></p>
						 <p>Location : <span class="location">alwar</span></p>
						 <p>Company Name : <span class="company">PRESTO DEMO</span></p>
						 <p>Office Contact No. : <span class="offcontact">123123</span></p>
						 <p>Mobile Number : <span class="mobile">9999999999</span></p>
						 <p>Email : <span class="email">9999999999</span></p>



                                <tbody>
								
                                </tbody>
                            </table>
                        </div>
                    </div>
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

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
		<script>
		// Time Picker
            jQuery('#timepicker').timepicker({
                defaultTIme : false
            });
			jQuery('#timepicker4').timepicker({
                defaultTIme : false
            });
            jQuery('#timepicker2').timepicker({
                showMeridian : false
            });
            jQuery('#timepicker3').timepicker({
                minuteStep : 15
            });
		</script>


<script language="javascript" type="text/javascript">   
$(document).ready(function() {
$("#save").click(function() {
	
	var production_flow = $("#production_flow").val();
if(production_flow=='')
{
	$("#error_production_flow").html('Required!');
}
var flowname = $("#flowname").val();
if(flowname=='')
{
	$("#error_flowname").html('Required!');
}
var who = $("#who").val();
if(who=='')
{
	
	$("#error_who").html('Required!');
}

var what = $("#what").val();
if(what=='')
{
	
	$("#error_what").html('Required!');
}

var how = $("#how").val();
if(how=='')
{
	
	$("#error_how").html('Required!');
}
var days = $("#days").val();
if(days=='')
{
	
	$("#error_days").html('Required!');
}

var when = $("#when").val();
if(when=='')
{
	
	$("#error_when").html('Required!');
}
var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}
var set_order = $("#set_order").val();
if(set_order=='')
{
	
	$("#setorder").html('Required!');
}

var response_type = $("#response_type").val();
if(response_type=='')
{
	
	$("#responsetype").html('Required!');
}

var actiontobetaken = $("#actiontobetaken").val();
if(actiontobetaken=='')
{
	
	$("#actiontobe_taken").html('Required!');
}

if(production_flow=='' || flowname=='' || who=='' || what=='' || how==''|| days==''|| when=='' || status=='' || set_order=='' || response_type=='' || actiontobetaken=='')
{
	
	return false;
}

});
});


</script>

<script type="text/javascript">
$(document).ready(function(){
$('.select2').select2({ });
$('.select3').select2({ });


 var i=2;
 $('#addmore_btn1').click(function(){
 i++;
 alert('hi');
 $('#dynamictasks1').append('<div id="row'+i+'"><div class="col-md-3"><div class="form-group"><label for="field-1" class="control-label">MACHINE</label><span id="error_who" style="color:red;">*</span><select class="form-control select3" style="text-transform: uppercase;" name="who" id="who"><option value="">--SELECT WHO--</option></select></div></div><div class="col-md-2"><div class="form-group"><label for="field-1" class="control-label">MODEL</label><span id="error_who" style="color:red;">*</span><select class="form-control select3" style="text-transform: uppercase;" name="who" id="who"><option value="">--SELECT WHO--</option></select></div></div><div class="col-md-2"><div class="form-group"><label for="field-1" class="control-label">CAPACITY</label><span id="error_who" style="color:red;">*</span><select class="form-control select3" style="text-transform: uppercase;" name="who" id="who"><option value="">--SELECT WHO--</option></select></div></div><div class="col-md-3"><div class="form-group"><label for="field-1" class="control-label">SAMPLE TO BE TESTED</label><span id="error_who" style="color:red;">*</span><input type="text" class="form-control" id="flowname" name="flowname" style="text-transform: uppercase;" placeholder="" required></div></div>	<div class="col-md-1"><div class="form-group"><label for="field-1" class="control-label">&nbsp;</label><br/><span class="btn btn-warning btn_remove" id="'+i+'"><i class="fa fa-minus"></i></span></div></div></div>');
 initializeSelect2('select3'+i);
 });
 
 
 
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id+'').remove();
 });
 
});

function populatedata()
{
	var companyname=$("#companyname").val();

	$(".company").html(companyname);
	var contactperson=$("#contactperson").val();
	$(".contactperson").html(contactperson);

	var officecontact=$("#officecontact").val();
	$(".offcontact").html(officecontact);
	
	var mobile=$("#mobile").val();
	$(".mobile").html(mobile);
	
	var email=$("#email").val();
	$(".email").html(email);
	
	
	var location=$("#location").val();
	$(".location").html(location);
	
	var industry=$("#industry").val();
	$(".industry").html(industry);
	
	
}
	  </script>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
</body>
</html>