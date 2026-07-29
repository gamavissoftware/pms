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
			
			}
			
			.select2-container--default .select2-selection--single
{
height:40px !important;	
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
				<form autocomplete="off" action="<?php echo page_url;?>Salestool/savedata" method="post">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
						
                           
                            <h4 class="page-title">SALES TOOL</h4>
                        </div>
                    </div>
                </div>
				
				

                <!-- end page title end breadcrumb -->
	<span style="color:red;text-align:center;"><?php echo $this->session->flashdata('message'); ?></span>
		<div class="row">
                    <div class="col-sm-12">
					
                        <div class="card-box table-responsive">
						<div>
                       
												
												 <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">TITLE </label>
														<span id="error_flowname" style="color:red;">*</span>
                                                        <select class="form-control" id="title" name="title" style="text-transform: uppercase;" placeholder="" required onchange="populatedata();">
														<option value="">Salutation</option>
														<option value="Mr.">Mr.</option>
														<option value="Mrs.">Mrs.</option>
														<option value="Ms.">Ms.</option>
														</select>
                                                    </div>
                                                </div>
												
												
												  <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">CONTACT PERSON</label>
														<span id="error_flowname" style="color:red;">*</span>
                                                        <input type="text" class="form-control" id="contactperson" name="contactperson" style="text-transform: capitalize;" placeholder="" required onkeyup="populatedata();">
                                                    </div>
                                                </div>
												
												
												  <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">COMPANY NAME</label>
														<span id="error_flowname" style="color:red;">*</span>
                                                        <input type="text" class="form-control" id="usercompanyname" name="usercompanyname" style="text-transform: uppercase;" placeholder="" required onkeyup="populatedata();">
                                                    </div>
                                                </div>
												
												  <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">OFFICE CONTACT</label>
														<span id="error_flowname" style="color:red;">*</span>
                                                        <input type="text" class="form-control" id="officecontact" name="officecontact" style="text-transform: uppercase;" placeholder="" required onkeyup="populatedata(); phoneno();">
                                                    </div>
                                                </div>
												
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">MOBILE</label>
														<span id="error_flowname" style="color:red;">*</span>
                                                        <input type="text" class="form-control mob" id="mobile" name="mobile" style="text-transform: uppercase;" placeholder="" required onkeyup="populatedata(); mobileno();" onblur="validatedigit();">
                                                    </div>
                                                </div>
												
												
													<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">EMAIL</label>
														<span id="error_flowname" style="color:red;">*</span>
                                                        <input type="email" class="form-control" id="email" name="email" style="text-transform: uppercase;" placeholder="" required onblur="populatedata();" onblur="checkemail();">
                                                    </div>
                                                </div>
												
												<script>


												function phoneno(){          
												$('#officecontact').keypress(function(e) {
												var a = [];
												var k = e.which;

												for (i = 48; i < 58; i++)
												a.push(i);

												if (!(a.indexOf(k)>=0))
												e.preventDefault();
												});
												}
												
												
												function mobileno(){          
												$('#mobile').keypress(function(e) {
												var a = [];
												var k = e.which;

												for (i = 48; i < 58; i++)
												a.push(i);

												if (!(a.indexOf(k)>=0))
												e.preventDefault();
												});
												}


												function validatedigit()
												{
												var mob=$("#mobile").val();

												var len=mob.length;
												if(len=='10')
												{
												return true;
												}else
												{
												alert('Mobile number should be 10 digit only');
												var mob=$("#mobile").val('');
												populatedata();
												}
												}
												
												function checkemail()
												{
													var email=$("#email").val();
													
												var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
												if(!regex.test(email)) {
												alert('Invalid Email');
												$("#email").val('');
												populatedata();
												return false;
												}else{
												return true;
												}
												}
													
										
												</script>
												
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">LOCATION</label>
														<span id="error_flowname" style="color:red;">*</span>
                                                        <select class="form-control select4" id="location" name="location" required onchange="populatedata(); checkforcompanyhistory();"><option></option></select>
                                                    </div>
                                                </div>
												
												
													 <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">INDUSTRY</label>
														<span id="error_flowname" style="color:red;">*</span>
                                                        <select class="form-control" id="industry" name="industry" style="text-transform: uppercase;" placeholder="" required onChange="populatedata(); checkforcompanyhistory(); getsubindustrytype();">
														<option value="">SELECT INDUSTRY</option>
														<?php
														$restyu=$this->db->select('id,industry')->from('salestoolindustry')->where('status','1')->get();
														if($restyu->num_rows()>0)
														{
															foreach($restyu->result() as $restyu12)
															{
														?>
														<option value="<?php echo $restyu12->id;?>"><?php echo $restyu12->industry;?></option>
														<?php
															}
														}
														?>
														
														</select>
                                                    </div>
                                                </div>
												
												
												<div style="clear:both;height:10px"></div>
												
												 <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">SUB INDUSTRY</label>
													
                                                        <select class="form-control" id="subindustry" name="subindustry" style="text-transform: uppercase;" placeholder="" onchange="populatedata(); checkforcompanyhistory();">
														<option value="">SUB INDUSTRY</option>
														
														
														</select>
                                                    </div>
                                                </div>
												
												</div>
												
											<div style="clear:both;"></div>
											
											<div class="row dynamicrow">
											<div class="col-md-3">
											<div class="form-group">
											<label for="field-1" class="control-label">MACHINE</label>
											<span id="error_who" style="color:red;">*</span>
											<select class="form-control select3 
											machinerow0" style="text-transform: uppercase;" name="machine[]" id="machine0" onchange="getmodelcapacity(0); populatedata(); generateintromachinescript(0);" required rowid="0">
											<option value=''>SELECT MACHINE</option>
											
											<?php
											$mach=$this->db->select('id,machinename')->from('salestoolmachines')->get();
											if($mach->num_rows()>0)
											{
												foreach($mach->result() as $mach1)
												{
											?>

<option value="<?php echo $mach1->id;?>"><?php echo $mach1->machinename;?></option>
											<?php
												}											
											}
											?>

											</select>
											</div>
											</div>
											
											<div class="col-md-3">
											<div class="form-group">
											<label for="field-1" class="control-label">MODEL</label>
											<span id="error_who" style="color:red;">*</span>
											<select class="form-control selectedmodel" style="text-transform: uppercase;" name="model[]" id="model0" onchange="generateintromachinescript(0); getmodelattachment(0);">
											<option value="">--SELECT MODEL--</option>

											</select>
											<p class="attachment0" style="size:11px;text-align:center;"></p>
											</div>
											
											</div>
											
											
											<div class="col-md-3">
											<div class="form-group">
											<label for="field-1" class="control-label">CAPACITY</label>
											<span id="error_who" style="color:red;">*</span>
											<select class="form-control selectedcapacity" style="text-transform: uppercase;" name="capacity[]" id="capacity0" onchange="generateintromachinescript(0);">
											<option value="">--SELECT CAPACITY--</option>

											</select>
											</div>
											</div>
											
											
												
											<div class="col-md-2">
											<div class="form-group">
											<label for="field-1" class="control-label">SAMPLE TO BE TESTED</label>
											<span id="error_who" style="color:red;">*</span>
											 <select class="form-control select5 allsample" id="sample0" name="sample[]" required onchange="populatedata(); checkforcompanyhistory();" onkeyup="generateintromachinescript(0);"><option></option></select>
										
											</div>
											</div>
											
											
											<div class="col-md-1">
											<div class="form-group">
											<label for="field-1" class="control-label">&nbsp;</label><br/>
											 <span class="btn btn-warning" id="addmore_btn1"><i class="fa fa-plus"></i></span>
										
											</div>
											</div>
											
											</div>
											<div id="dynamictasks1"></div>
											
											<div style="clear:both;height:15px"></div>
											
												
												
												
												


                        </div>
                    </div>
					
        
                   <!-- <div class="col-sm-12">
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
                    </div> -->
					<?php
					$restyui=$this->db->select('*')->from('salestooltemplete')->get();
		if($restyui->num_rows()>0)
		{
			foreach($restyui->result() as $row);
					?>
					<div class="col-sm-12">
                        <div class="card-box table-responsive scriptdata" style="color:black;">
                       <div class="col-md-8" style="font-size:18px">
					    <p><?php echo $row->intro;?></p>
						<p><?php echo $row->first_field;?> - <span class="<?php echo $row->person_title;?>"></span> <span class="<?php echo $row->person_name;?>"></span>,</p>
						<p><span class="<?php echo $row->salutation;?>"></span></p>
						<p><span class="<?php echo $row->second_field;?>"><?php echo $row->second_field;?></p>
						
						<div id="lineone" style="display:none">
						<p>
						<table border='1' style='width:100%; padding:5px 0 0 5px;text-align:center;'><tr style='background-color:white;text-align:center;'></tr>
						<tr class="intro0">
						<td style="text-align:center;padding: 7px 8px 0px 8px;"></td>
						</tr>
						</tbody>
						</table></p>
						</div>

						<p><?php echo $row->third_field;?></p>
						<div id="linefour" style="display:none">
						<p class="modeldetail0">
						</p>
						</div>
						<p><span class="<?php echo $row->machine_model;?>"></span></p>
						<p><?php echo $row->fourth_field;?> <span class="<?php echo $row->sample_name;?>"></span> <?php echo $row->fifth_field;?></p>
						<div id="linefive">
						<p class="related0"></p>
						</div>

						<div id="linesix">
						<p class="companyhistory0"></p>
						</div>
						<p><span class="<?php echo $row->company_detail;?>"></span></p>
						<p><?php echo $row->seventh_line;?> <span class="<?php echo $row->person_title;?>"></span> <span class="<?php echo $row->again_person_name;?>"></span></p>
						
						
						<div id="linetwo" style="display:none">
						<p>You require</p>
						<p class="reconf">
						<span class="detail0"></span>
						</p>
						<p class="othermodel">
						<span class="othermodeldetail0">
						
						</span>
						</p>
						</div>
						
						<p class="linethree"></p>
						<p><?php echo $row->lastline;?></p>



                              
                        </div>
						
						
						<div class="col-md-4"></div>
						
						<div class="row">
						<div class="col-md-12">
											<div class="col-md-4"></div>
											<div class="col-md-4 text-center"><input type="submit" class="btn btn-success" value="Submit" style="width:70%"></div>
											<div class="col-md-4"></div>
										</div>	
											</div>
												
						
						</div>
						
						
						
						
                    </div>
					
					
					<?php
					
		}
		?>
                </div>
		
 
                <!-- Footer -->
               <?php $this->load->view('common/footer');?>
                <!-- End Footer -->

            </div> <!-- end container -->
        </div>
			</form>	
                <!-- en
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
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
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


<script type="text/javascript">
$(document).ready(function(){
	var purl="<?php echo page_url;?>Salestool/getlocations";
$('.select2').select2({ });
$('.select3').select2({});
$('.select4').select2({
	
placeholder: 'TYPE TO SELECT',
minmumInputLength:4,
		allowClear: true,
		tags:true,

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

			
	
	
	
	
}).on("change", function(e) {
checkforcompanyhistory();
});




var industsu=$("#industry").val();
if(industsu=='')
{
	var insd="NA";
}else{
	var insd=industsu;
}

var purl1="<?php echo page_url;?>Salestool/getsamples/"+insd;

$('.select5').select2({
	
placeholder: 'TYPE TO SELECT',
minmumInputLength:4,
		allowClear: true,
		tags:true,

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

			
	
	
	
	
}).on("change", function(e) {
generateintromachinescript(0);
});


var i=1;
$('#addmore_btn1').click(function(){

 
 
 
 
 $('#dynamictasks1').append('<div id="row'+i+'" class="dynamicrow"><div class="col-md-3"><div class="form-group"><label for="field-1" class="control-label">MACHINE</label><span id="error_who" style="color:red;">*</span><select class="form-control select3 selectedmachines machinerow'+i+'" style="text-transform: uppercase;" name="machine[]" id="machine'+i+'" onchange="getmodelcapacity('+i+'); generateintromachinescript('+i+');" required rowid="'+i+'"><option value="">SELECT MACHINE</option><?php $mach=$this->db->select("id,machinename")->from("salestoolmachines")->get();if($mach->num_rows()>0){ foreach($mach->result() as $mach1){ ?><option value="<?php echo $mach1->id;?>"><?php echo $mach1->machinename;?></option><?php }}?></select></div></div><div class="col-md-3"><div class="form-group"><label for="field-1" class="control-label">MODEL</label><span id="error_who" style="color:red;">*</span><select class="form-control select3 selectedmodel" style="text-transform: uppercase;" name="model[]" id="model'+i+'" onchange="generateintromachinescript('+i+'); getmodelattachment('+i+');"><option value="">--SELECT MODEL--</option></select><p class="attachment'+i+'" style="size:11px;text-align:center;"></p></div></div><div class="col-md-3"><div class="form-group"><label for="field-1" class="control-label">CAPACITY</label><span id="error_who" style="color:red;">*</span><select class="form-control select3 selectedcapacity " style="text-transform: uppercase;" name="capacity[]" id="capacity'+i+'" onchange="generateintromachinescript('+i+');"><option value="">--SELECT CAPACITY--</option></select></div></div><div class="col-md-2"><div class="form-group"><label for="field-1" class="control-label">SAMPLE TO BE TESTED</label><span id="error_who" style="color:red;">*</span><select class="form-control samplerow'+i+' select5 allsample" id="sample'+i+'" name="sample[]" required onchange="populatedata(); checkforcompanyhistory();" onkeyup="generateintromachinescript('+i+');"><option></option></select></div></div><div class="col-md-1"><div class="form-group"><label for="field-1" class="control-label">&nbsp;</label><br/><span class="btn btn-warning btn_remove" id="'+i+'"><i class="fa fa-minus"></i></span></div></div></div><div style="clear:both;height:20px"></div>');
 
 initializeSelect2('machinerow'+i,i);
  initializeSelect5('samplerow'+i,i);
 var count = $('.dynamicrow').length;
var ccount=count-1;
 $('<tr class="intro'+ccount+'"><td style="text-align:center;"></td></tr>').insertAfter('#lineone tr:last');
 $('<span class="detail'+ccount+'"></span>').insertAfter('#linetwo .reconf span:last');
 $('<span class="othermodeldetails'+ccount+'"></span>').insertAfter('#linetwo .othermodel span:last');
 $('<p class="modeldetail'+ccount+'"></p>').insertAfter('#linefour p:last');
 $('<p class="related'+ccount+'"></p>').insertAfter('#linefive p:last');
 $('<p class="companyhistory'+ccount+'"></p>').insertAfter('#linesix p:last');
 
  i++;
 
 });
 
 
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 var count = $('.dynamicrow').length;
 var but=button_id;
 $('#row'+button_id).remove();
	$(".intro"+but).remove(); 
	$(".detail"+but).remove(); 
	$(".modeldetail"+but).remove(); 
$(".related"+but).remove(); 	
$(".companyhistory"+but).remove(); 	
  i--;
 });
 
});




function getmodelcapacity(row)
{
	
	var machine=$("#machine"+row).val();
	if(machine!='')
	{
		
			$.ajax({
			type:"post",
			url:"<?php echo page_url;?>Salestool/getmodel/"+machine,
			data:"1",
			success:function(data){
			$("#model"+row).html(data);
			generateintromachinescript(row);
			setstyle();
			getmodelattachment(row);
			checkforcompanyhistory();


	}
			});
		
		
		
		
		
		
	}
	
	 getcapacity(row);
	 
	
	
}


function getcapacity(row)
{
	var machine=$("#machine"+row).val();
	if(machine!='')
	{
		
			$.ajax({
			type:"post",
			url:"<?php echo page_url;?>Salestool/getcapacity/"+machine,
			data:"1",
			success:function(data){
			$("#capacity"+row).html(data);
			generateintromachinescript(row);
			setstyle();
			checkforcompanyhistory();

			}
			});
		
	}
	
}
	  </script>
	  
	   <script>
	  function initializeSelect2(selectElementObj) {
         $('.'+selectElementObj).select2({});
         
      }
	  
	  
	   function initializeSelect5(selectElementObj,row) {
		   
		  
		   var industsu=$("#industry").val();
if(industsu=='')
{
	var insd="NA";
}else{
	var insd=industsu;
}

var purl1="<?php echo page_url;?>Salestool/getsamples/"+insd;

         $('.'+selectElementObj).select2({
	
	
placeholder: 'TYPE TO SELECT',
minmumInputLength:4,
		allowClear: true,
		tags:true,

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

	
	
}).on("change", function(e) {
generateintromachinescript(row);
});

         
      }
	
  function populatedata()
{
	var title=$("#title").val();
	if(title!='')
	{
	$(".persontitle").html(title);
	if(title=='Mr.')
	{
		var salutation="Sir";
	}else
	{
		var salutation="Ma'am";
	}
	$(".salutation").html(salutation);
	}else{
		$(".persontitle").html('');
		$(".salutation").html('');
	}
	
	var contactperson=getfirstlettercap($("#contactperson").val());
	//alert(contactperson);

	$(".personname").html(contactperson);
	
	var locations=getfirstlettercap($("#location").val());
	$(".location").html(locations);
	
	
	getcustomeroveralldata(title,contactperson,locations);
	setstyle();
	getallsample();
	checkforcompanyhistory();
	checkforothermodels();
	
	
}
	
function getcustomeroveralldata(title,contactperson,locations)
{
	
	var title=title;
	if(title!='')
	{
		var title=title;
	}else{
		var title="NA";
	}
	
	var contactperson=contactperson;
	if(contactperson!='')
	{
		var contactperson=contactperson;
	}else{
		
		var contactperson="NA";
	}
	
	var locations=locations;
	if(locations!='')
	{
		var locations=locations;
	}else{
		var locations="NA";
		
	}
	var usercompname=$("#usercompanyname").val();
	if(usercompname!='')
	{
		var usercompname=usercompname;
	}else
	{
		var usercompname="NA";
	}
	var officecontact=$("#officecontact").val();
	if(officecontact!='')
	{
		var officecontact=officecontact;
	}else
	{
		var officecontact="NA";
	}
	
	
	var mobile=$("#mobile").val();
	if(mobile!='')
	{
		var mobile=mobile;
	}else
	{
		var mobile="NA";
	}
	
	var email=$("#email").val();
	if(email!='')
	{
		var email=email;
	}else
	{
		var email="NA";
	}
	
	
	
	$.ajax({
			type:"post",
			url:"<?php echo page_url;?>Salestool/getcustomerinfo/"+usercompname+"/"+officecontact+"/"+mobile+"/"+email+"/"+title+"/"+contactperson+"/"+locations,
			data:"1",
			success:function(data){

			
			$(".linethree").html(data);
			
			setstyle();
			
			}
			});
	
	
	
}

function generateintromachinescript(id)
{
var machines = $("#machine"+id).val();
var model = $("#model"+id).val();
var capacity = $("#capacity"+id).val();
var samples = $("#sample"+id).val();
if(model!='')
{
	model=model;
}else{
	model='NA';
}

if(capacity!='')
{
	capacity=capacity;
}else{
	capacity='NA';
}

if(samples!='')
{
	samples=samples;
}else{
	samples='NA';
}


	$.ajax({
			type:"post",
			url:"<?php echo page_url;?>Salestool/getintroline/"+machines+"/"+samples+"/"+id+"/"+capacity+"/"+model,
			data:"1",
			success:function(data){
		
			var c=id;
			$("#lineone").css('display','');
			$(".intro"+c).html(capitalize_Words(data));
			
			generateothermodelsdetails(id,machines,model);
			//generatemachinealldetails(id,machines,model,capacity,samples);
			generatemachinerelateddetails(id,machines);
			if(id==0)
			{
			generatemachineusagedetails(id,machines);
			}
			setstyle();
			
			}
			});

}	


function generatemachinealldetails(id,machines,model,capacity,samples)
{
	if(machines!='')
	{
		var machines=machines;
	}else{
		
		var machines="NA";
	}
	
	if(model!='')
	{
		var model=model;
	}else{
		
		var model="NA";
	}
	
	if(capacity!='')
	{
		var capacity=capacity;
	}else{
		
		var capacity="NA";
	}
	
	
	if(samples!='')
	{
		var samples=samples;
	}else{
		
		var samples="NA";
	}
	

	$.ajax({
			type:"post",
			url:"<?php echo page_url;?>Salestool/getmachinedetails/"+machines+"/"+model+"/"+capacity+"/"+samples+"/"+id,
			data:"1",
			success:function(data){
			
			var c=id;
			$("#linetwo").css('display','');
			if(c!=0)
			{
				var data=' ,'+data;
			}else{
				
				var data=data;
			}
			$(".detail"+c).html(data);
			
			
			}
			});
	
	
	getallsample();
	
	setstyle();
	
	
}





function generateothermodelsdetails(id,machines,model)
{
	if(machines!='')
	{
	machines=machines;
	}else{
	machines="NA";
	}
	
	if(model!='')
	{
	model=model;
	}else{ model="NA"; }
	$.ajax({
			type:"post",
			url:"<?php echo page_url;?>Salestool/getothermodeldetails/"+machines+"/"+id+"/"+model,
			data:"1",
			success:function(data){
		
			var c=id;

$("#linefour").css('display','');
			$(".modeldetail"+c).html(data);

	setstyle();
			
			}
			});
			
	}
		

function getallsample()
{
	var allsample = $("select.allsample").map(function() {
        return this.value;
    }).get().join(", ");
   
	
	$(".samplename").html(getfirstlettercap(allsample));
	setstyle();
	
}



function capitalize_Words(str)
{
 //return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
 return str;
}

function ucfirst(str,force){
          str=force ? str.toLowerCase() : str;
          return str.replace(/(\b)([a-zA-Z])/,
                   function(firstLetter){
                      return   firstLetter.toUpperCase();
                   });
     }
	
	 function setstyle()
	 {
		 
		// $(".scriptdata").css('text-transform','capitalize');
	 }
	 
	 function getmodelattachment(row)
	 {
		
		 var model=$("#model"+row).val();
		 var machines = $("#machine"+row).val();
		 if(model!='')
		 {
			 
			 $.ajax({
			type:"post",
			url:"<?php echo page_url;?>Salestool/getmodalattachment/"+machines+"/"+model,
			data:"1",
			success:function(data){
		
		//alert(data);

			$(".attachment"+row).html(data);

	setstyle();
	checkforcompanyhistory();
			
			}
			});
			 
			 
		 }
		 
		 
	 }
	 
	 function generatemachinerelateddetails(id,machine)
	 {
		
		var samp=$("#sample"+id).val(); 
		  $.ajax({
			type:"post",
			url:"<?php echo page_url;?>Salestool/getrelatedmachine/"+machine+"/"+id+"/"+samp,
			data:"1",
			success:function(data){
	

		$(".related"+id).html(data);

	setstyle();
			
			}
			});
		 
		 
	 }
	 
	 
	 
	  function generatemachineusagedetails(id,machine)
	{
	

		 var indus=$("#industry").val();
		 var subindustry=$("#subindustry").val();
		 if(subindustry=='')
		 {
			 var subindustry="NA";
		 }else{
			 var subindustry=subindustry;
		 }
		 var location=$("#location").val();
		 if(machine!='' && indus!='' && location !='')
		 {
		  $.ajax({
			type:"post",
			url:"<?php echo page_url;?>Salestool/generatemachineusagedetails/"+machine+"/"+indus+"/"+location+"/"+subindustry,
			data:"1",
			success:function(data){
		$(".companyhistory"+id).html(data);

			setstyle();
			
			}
			});
		 }else{
			 
			 $(".companyhistory"+id).html('');
			 
		 }
		 
		 
	}
	 
	 function checkforcompanyhistory()
	 {
		
		 var selected = $('select[name="machine[]"]').map(function(){
			
			var rowid=$(this).attr("rowid");
			var machine=$("#machine"+rowid).val();
			if(rowid==0)
			{
			generatemachineusagedetails(rowid,machine);
			}
			
});
		 
		 
		 
	 }
	 
	 function checkforothermodels()
	 {
		 
			/** LIKED MODEL **/

			var othermodels = $.map($(':checkbox[name=othermodel\\[\\]]:checked'), function(n, i){
			return n.value;
			}).join(',');
			
			var othermachine = $.map($(':checkbox[name=othermachine\\[\\]]:checked'), function(n, i){
			return n.value;
			}).join(',');
			 $.ajax({
			type:"post",
			url:"<?php echo page_url;?>Salestool/othermodelscustomerlike/"+othermodels+"/"+othermachine,
			data:"1",
			success:function(data){
		

	$(".othermodel .othermodeldetail0").html(data);
			
			}
			});
			/** END **/
		 
		 
	 }
	 
	 
</script>
<script>
												function getsubindustrytype()
												{
													
												var industry=$("#industry").val();
													if(industry!='')
													{
														
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Salestool/getsubindustry/"+industry,
													data:"1",
													success:function(data){
														
													$("#subindustry").html(data);

													setstyle();

													}
													});
														
													}
													
									
													
												}
											
											
											function getfirstlettercap(str)
											{
                                                var str= str.toLowerCase().replace(/\b[a-z]/g, function(txtVal) {
                                                return txtVal.toUpperCase();
                                                });
                                                
                                                return str;
											}
											
											
								function sendpdfwhatsapp(machine,model)
											{
											   
											    var mob=$("#mobile").val();
											     var title=$("#title").val();
											      var name=$("#contactperson").val();
											    
									if(mob!='' && title!='' && name!=''  )
											   {
                                                
                                     if(confirm('Proceed to share pdf on whatsapp?'))
                                     {
                                                    
                                    $.ajax({
                                    type:"post",
                                    url:"<?php echo page_url;?>Salestool/pdffromwhatsapp",
                                        data:{
                                        title:title,
                                        machine:machine,
                                        model:model,
                                        mob:mob,
                                        name:name
                                        },
                                    success:function(data){
                                     
                                     alert(data);
                                    
                                    
                                    }
                                    });
                                       }
											    
											   }else
											   {
											       alert('Enter Mobile No. to send');
											       return false;
											   }
											 }
											
											
											
											
											function checknoofcheckbox()
											{
                                                var countchecked = $("input[type=checkbox]:checked").length;
                                            
                                                if(countchecked >= 5) 
                                                {
                                                $('input[type=checkbox]').not(':checked').attr("disabled",true);
                                                }
                                                else
                                                {
                                                $('input[type=checkbox]').not(':checked').attr("disabled",false);
                                                }
											}
											
												</script>


<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
</body>
</html>
</html>