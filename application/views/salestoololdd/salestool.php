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
					<form autocomplete="off" >
                        <div class="card-box table-responsive">
						<div>
                         <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">COMPANY NAME</label>
														<span id="error_flowname" style="color:red;">*</span>
                                                        <input type="text" class="form-control" id="usercompanyname" name="usercompanyname" style="text-transform: uppercase;" placeholder="" required onkeyup="populatedata();">
                                                    </div>
                                                </div>
												
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
												
													<!--<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">INDUSTRY</label>
														<span id="error_flowname" style="color:red;">*</span>
                                                        <input type="text" class="form-control" id="industry" name="industry" style="text-transform: uppercase;" placeholder="" required onkeyup="populatedata();">
                                                    </div>
                                                </div>
												</div>-->
												
											<div style="clear:both;"></div>
											
											<div class="row dynamicrow">
											<div class="col-md-3">
											<div class="form-group">
											<label for="field-1" class="control-label">MACHINE</label>
											<span id="error_who" style="color:red;">*</span>
											<select class="form-control select3 
											machinerow0" style="text-transform: uppercase;" name="machine[]" id="machine0" onchange="getmodelcapacity(0); populatedata(); generateintromachinescript(0);">
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
											<select class="form-control selectedmodel" style="text-transform: uppercase;" name="model[]" id="model0" onchange="generateintromachinescript(0);">
											<option value="">--SELECT MODEL--</option>

											</select>
											</div>
											</div>
											
											
											<div class="col-md-3">
											<div class="form-group">
											<label for="field-1" class="control-label">CAPACITY</label>
											<span id="error_who" style="color:red;">*</span>
											<select class="form-control selectedcapacity" style="text-transform: uppercase;" name="capacity" id="capacity0" onchange="generateintromachinescript(0);">
											<option value="">--SELECT CAPACITY--</option>

											</select>
											</div>
											</div>
											
											
												
											<div class="col-md-2">
											<div class="form-group">
											<label for="field-1" class="control-label">SAMPLE TO BE TESTED</label>
											<span id="error_who" style="color:red;">*</span>
											 <input type="text" class="form-control allsample" id="sample0" name="sample" style="text-transform: capitalize;" placeholder="" required onkeyup="generateintromachinescript(0);">
										
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
											
											
												
												
												
												
												


                        </div>
                    </div>
					</form>
        
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
                        <div class="card-box table-responsive scriptdata" style="background-color:#134F5C;color:white;font-weight:bold;">
                       
					    <p><?php echo $row->intro;?></p>
						<p><?php echo $row->first_field;?> - <span class="<?php echo $row->person_title;?>"></span> <span class="<?php echo $row->person_name;?>"></span>,</p>
						<p><span class="<?php echo $row->salutation;?>"></span></p>
						<p><span class="<?php echo $row->second_field;?>"><?php echo $row->second_field;?></p>
						
						<div id="lineone">
						<p><span class="intro0"></span></p>
						</div>

						<p><?php echo $row->third_field;?></p>
						<div id="linefour">
						<p><span class="modeldetail0"></span></p>
						</div>
						<p><span class="<?php echo $row->machine_model;?>"></span></p>
						<p><?php echo $row->fourth_field;?> <span class="<?php echo $row->sample_name;?>"></span> <?php echo $row->fifth_field;?></p>
						<p><span class="<?php echo $row->related_machines;?>">Related Machine</span> <span><?php echo $row->sixth_field;?></span> <span class="<?php echo $row->location;?>"></span> <?php echo $row->afterlocation;?></p>
						<p><span class="<?php echo $row->company_detail;?>"></span></p>
						<p><?php echo $row->seventh_line;?> <span class="<?php echo $row->person_title;?>"></span> <span class="<?php echo $row->again_person_name;?>"></span></p>
						
						<div id="linetwo">
						<p><span class="detail0"></span></p>
						</div>
						
						<p class="linethree"></p>
						<p><?php echo $row->lastline;?></p>



                              
                        </div>
                    </div>
					
					<?php
					
		}
		?>
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


<script type="text/javascript">
$(document).ready(function(){
$('.select2').select2({ });
$('.select3').select2({ });


 var i=1;
 $('#addmore_btn1').click(function(){

 
 $('#dynamictasks1').append('<div id="row'+i+'" class="dynamicrow"><div class="col-md-3"><div class="form-group"><label for="field-1" class="control-label">MACHINE</label><span id="error_who" style="color:red;">*</span><select class="form-control select3 selectedmachines machinerow'+i+'" style="text-transform: uppercase;" name="machine[]" id="machine'+i+'" onchange="getmodelcapacity('+i+'); generateintromachinescript('+i+');"><option value="">SELECT MACHINE</option><?php $mach=$this->db->select("id,machinename")->from("salestoolmachines")->get();if($mach->num_rows()>0){ foreach($mach->result() as $mach1){ ?><option value="<?php echo $mach1->id;?>"><?php echo $mach1->machinename;?></option><?php }}?></select></div></div><div class="col-md-3"><div class="form-group"><label for="field-1" class="control-label">MODEL</label><span id="error_who" style="color:red;">*</span><select class="form-control select3 selectedmodel" style="text-transform: uppercase;" name="model[]" id="model'+i+'" onchange="generateintromachinescript('+i+');"><option value="">--SELECT MODEL--</option></select></div></div><div class="col-md-3"><div class="form-group"><label for="field-1" class="control-label">CAPACITY</label><span id="error_who" style="color:red;">*</span><select class="form-control select3 selectedcapacity " style="text-transform: uppercase;" name="capacity" id="capacity'+i+'" onchange="generateintromachinescript('+i+');"><option value="">--SELECT CAPACITY--</option></select></div></div><div class="col-md-2"><div class="form-group"><label for="field-1" class="control-label">SAMPLE TO BE TESTED</label><span id="error_who" style="color:red;">*</span><input type="text" class="form-control allsample" id="sample'+i+'" name="sample" style="text-transform: uppercase;" placeholder="" required onkeyup="generateintromachinescript('+i+');"></div></div>	<div class="col-md-1"><div class="form-group"><label for="field-1" class="control-label">&nbsp;</label><br/><span class="btn btn-warning btn_remove" id="'+i+'"><i class="fa fa-minus"></i></span></div></div></div>');
 initializeSelect2('machinerow'+i);
 var count = $('.dynamicrow').length;
var ccount=count-1;
 $('<p><span class="intro'+ccount+'"></span></p>').insertAfter('#lineone p:last');
 $('<p><span class="detail'+ccount+'"></span></p>').insertAfter('#linetwo p:last');
 $('<p><span class="modeldetail'+ccount+'"></span></p>').insertAfter('#linefour p:last');
 
  i++;
 
 });
 
 
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 var count = $('.dynamicrow').length;
 var but=button_id;
 $('#row'+button_id).remove();
	$(".intro"+but).parent().remove(); 
	$(".detail"+but).parent().remove(); 
	$(".modeldetail"+but).parent().remove();   
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

			}
			});
		
	}
	
}
	  </script>
	  
	   <script>
	  function initializeSelect2(selectElementObj) {
         $('.'+selectElementObj).select2({});
         
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
	$(".salutation").html(capitalize_Words(salutation));
	}else{
		$(".persontitle").html('');
		$(".salutation").html('');
	}
	
	var contactperson=capitalize_Words($("#contactperson").val());
	//alert(contactperson);
	$(".personname").html(contactperson);
	
	var location=capitalize_Words($("#location").val());
	$(".location").html(location);
	
	
	getcustomeroveralldata(title,contactperson,location);
	setstyle();
	
	
}
	
function getcustomeroveralldata(title,contactperson,location)
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
	
	var location=location;
	if(location!='')
	{
		var location=location;
	}else{
		var location="NA";
		
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
			url:"<?php echo page_url;?>Salestool/getcustomerinfo/"+usercompname+"/"+officecontact+"/"+mobile+"/"+email+"/"+title+"/"+contactperson+"/"+location,
			data:"1",
			success:function(data){

			
			$(".linethree").html(capitalize_Words(data));
			
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
			url:"<?php echo page_url;?>Salestool/getintroline/"+machines+"/"+samples+"/"+id+"/"+capacity,
			data:"1",
			success:function(data){
		
			var c=id;
			
			$(".intro"+c).html(capitalize_Words(data));
			
			generateothermodelsdetails(id,machines,model);
			generatemachinealldetails(id,machines,model,capacity,samples);
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
			
			$(".detail"+c).html(capitalize_Words(data));
			
			
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

			$(".modeldetail"+c).html(capitalize_Words(data));

	setstyle();
			
			}
			});
			
	}
		

function getallsample()
{
	var allsample = $("input.allsample").map(function() {
        return this.value;
    }).get().join(", ");
	
	$(".samplename").html(capitalize_Words(allsample));
	setstyle();
	
}



function capitalize_Words(str)
{
 return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
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
		 
		 $(".scriptdata").css('text-transform','capitalize');
	 }
</script>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
</body>
</html>