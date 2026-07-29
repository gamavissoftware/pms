<?php
$samplecode='';
$company='';
$id= $this->uri->segment(3);
if($id<>'')
{
	$sample=$this->db->select('id,sampletestid,companyname,productname,instrument')->from('specificsampletestrequest')->where('id',$id)->get();
	
	if($sample->num_rows()>0)
	{
		foreach($sample->result() as $sample1);
		$samplecode=$sample1->sampletestid;
		$company=$sample1->companyname;
		$productname=$sample1->productname;
		$instrument=$sample1->instrument;
		
		$restyui12=$this->db->select('*')->from('specificsampletobetested')->where('samplereqid',$sample1->id)->order_by('sample','ASC')->get();
		
				
		
}else{
	
	echo "INVALID ACCESS";exit;
	
}
	
}else{
	
	echo "INVALID ACCESS"; exit;
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>SAMPLE TESTING</title>

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
						
                           
                            <h4 class="page-title">SAMPLE TESTING REQUEST</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
			<div class="card-box table-responsive">
		<div class="row">
			
			 <form id="loginForm" method="post" action="<?php echo page_url;?>Sampletest/sampletestresults/<?php echo $id;?>" enctype="multipart/form-data" onsubmit="return checkfileforvalidation();">

                               
                                            <div class="row">
											
											 <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Sample ID</label>
														<span id="error_holidayname" style="color:red;"></span>
                                                       <input type="text" name="sampleid" readonly class="form-control" required value="<?php echo $samplecode;?>">
													   
                                                    </div>
                                                </div>
												
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Company Name</label>
														<span id="error_holidayname" style="color:red;"></span>
                                                       <input type="text" name="company" id="company" class="form-control" required value="<?php echo $company;?>" readonly>
													   
                                                    </div>
                                                </div>
												
												<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">INSTRUMENT NAME</label>
														<span id="error_instruments" style="color:red;">*</span>
                                                        <select class="form-control" style="text-transform: uppercase;" name="instruments" id="instruments" required>
												
															<?php 
														$query = $this->db->select('id, instruments_name, status')->from(' presto_instruments')->where('id',$instrument)->where('status','1')->get();
														foreach($query->result() as $instruments){
														?>
													  <option value="<?php echo $instruments->id;?>" selected><?php echo trim(strtoupper($instruments->instruments_name));?></option>
														<?php }?>
														</select>
                                                    </div>
                                                </div>
												
												 <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Lot No.</label>
														<span id="error_holidayname" style="color:red;"></span>
                                                       <input type="text" name="lno" id="lno" class="form-control" required>
													   
                                                    </div>
                                                </div>
												
												
												 <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Batch No.</label>
														<span id="error_holidayname" style="color:red;"></span>
                                                       <input type="text" name="bno" id="bno" class="form-control" required >
													   
                                                    </div>
                                                </div>
												
												
												 <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Product Name <span id="error_instruments" style="color:red;">*</span></label>
														<span id="error_holidayname" style="color:red;"></span>
                                                        <input type="text" class="form-control" id="prdname" name="prdname" placeholder="" required  value="<?php echo ucfirst($productname);?>" readonly>
													   
                                                    </div>
                                                </div>
												
												<script>


												function phoneno(){          
												$('.mob').keypress(function(e) {
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
												}
												}
												
											
												
												function validateemail(email)
												{
													
												
												if( !isValidEmailAddress( email ) ) {
													
													alert('Invalid Email');
													$("#emailad").val('');
												
													
												}

												}
												
												function isValidEmailAddress(emailAddress) {
												var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
												return pattern.test(emailAddress);
												};
											
												</script>
												
												 <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Sample Length</label>
														<span id="error_holidayname" style="color:red;"></span>
                                                        <input type="text" class="form-control" id="length" name="length" >
													   
                                                    </div>
                                                </div>
												
												<div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Sample Width</label>
														<span id="error_holidayname" style="color:red;"></span>
                                                        <input type="text" class="form-control" id="width" name="width">
													   
                                                    </div>
                                                </div>
												
													<div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Sample Thickness</label>
														<span id="error_holidayname" style="color:red;"></span>
                                                        <input type="text" class="form-control" id="thickness" name="thickness" >
													   
                                                    </div>
                                                </div>
												
													<div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Shape/GSM</label>
														<span id="error_holidayname" style="color:red;"></span>
                                                        <input type="text" class="form-control" id="shape" name="shape">
													   
                                                    </div>
                                                </div>
												
												<div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Test Speed</label>
														<span id="error_holidayname" style="color:red;"></span>
                                                        <input type="text" class="form-control" id="speed" name="speed">
													   
                                                    </div>
                                                </div>
												
												<div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Image <span id="error_instruments" style="color:red;">*</span></label>
														<span id="error_holidayname" style="color:red;"></span>
                                                        <input type="file" class="form-control" id="file" name="file[]" required multiple>
													   
                                                    </div>
                                                </div>
												
												
												<div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Video Link</label>
														<span id="error_holidayname" style="color:red;"></span>
                                                        <input type="text" class="form-control" id="vlink" name="vlink">
													   
                                                    </div>
                                                </div>
												
												<div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Remarks</label>
														<span id="error_holidayname" style="color:red;"></span>
                                                        <textarea class="form-control" name="remarks" placeholder="Testing Remarks"></textarea>
													   
                                                    </div>
                                                </div>
												 </div>
												 
										<?php
										if($restyui12->num_rows()>0)
										{
										?>
										<div class="row">
										
										<?php
										$t=1;
										foreach($restyui12->result() as $restyui121)
										{
											if($restyui12->num_rows()<>$t)
											{
												$sty="border-right:1px solid;#000;";
											}else{
												
												$sty='';
											}
										?>
										<input type="hidden" name="sampleid[]" value="<?php echo $restyui121->id;?>" required>
										<div class="col-md-4 rowforsample<?php echo $t;?>" style="<?php echo $sty;?>">
										<div class="col-md-12"><p class="text-center" style="color:red;font-weight:bold;">SAMPLE <?php echo $t;?>-<?php echo strtoupper($restyui121->sample);?></p></div>
										
										<div class="col-md-12">
										<div class="col-md-6 testtime<?php echo $t;?>1">
												
												<div class="form-group">
												<label for="field-1" class="control-label" id="labelfor<?php echo $t;?>1">NO. 1 <span id="error_instruments" style="color:red;">*</span></label>
												<input type="text" name="sampletest<?php echo $restyui121->id;?>[]" id="sample0" class="form-control no<?php echo $t;?>" required>
												</div>

												</div>
												
													<div class="col-md-5">
												
												<div class="form-group">
												<label for="field-1" class="control-label">UNIT <span id="error_instruments" style="color:red;">*</span></label>
												<select name="sampletestunit<?php echo $restyui121->id;?>[]" id="sampletestunit<?php echo $restyui121->id;?>" class="form-control commonunit" required>
											
												</select>
												</div>

												</div>
												
												<div class="col-md-1">
												<div class="form-group">
												<label for="field-1" class="control-label" style="margin-top:20px"></label>
												<span class="btn btn-xs btn-warning add_button1" id="<?php echo $t;?>" name="<?php echo $restyui121->id;?>"><i class="fa fa-plus"></i></span>
												</div>

												</div>
												</div>
												
												<div id="createddiv<?php echo $t;?>">
												
												
												</div>
											</div>
										<?php
										$t++;
										}
										?>
										



										</div>	
										<?php
										}else{
										?>		
										<div class="row"><p class="text-center>No Sample Found</p></div>		
										
										<?php
										}
										?>
										<div class="row">
											<div class="col-md-12">
											<div class="col-md-4"></div>
											<div class="col-md-4"></div>
											
											<div class="col-md-4"><input type="submit" class="btn btn-success pull-right" name="submit" id="sub" value="Submit"></div>
											
											</div>
											</div>
												
                                               
												
                                           
											
											
                                        
								</form>
                            
							
			  </div>
         
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
	getunits('commonunit');
	
$('.select2').select2({ });
$('.select3').select2({ }); 	


 var i=1;
 $('#addmore_btn1').click(function(){

 
 $('#dynamicmodel').append('<div id="row'+i+'" class="dynamicrowformodel"><div class="col-md-6"><div class="form-group"><label for="field-1" class="control-label">MODEL</label><span id="error_holidayname" style="color:red;"></span><i 	nput type="text" class="form-control" id="model'+i+'" style="text-transform: uppercase;" name="model[]" placeholder="" value="" required></div></div><div class="col-md-4"><div class="form-group"><label for="field-1" class="control-label">PDF</label><input type="file" class="form-control" id="attachment'+i+'" style="text-transform: uppercase;" name="attachment[]" placeholder="" value="" required></div></div><div class="col-md-2"><label for="field-1" class="control-label" style="margin-top:45px"></label><span class="btn btn-warning btn_remove" id="'+i+'"><i class="fa fa-minus"></i></span></div></div>');
 
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







var l=1;


			 $('.add_button1').click(function(){


  var rid = $(this).attr("id");
 var sampleid = $(this).attr("name");
  
  var no=$(".no"+rid).length;
  
  var no1=no+1;
  
  if(no<5)
  {
 $('#createddiv'+rid).append('<div class="col-md-12" id="row11'+l+'"><div class="col-md-6  testtime'+rid+l+'"><div class="form-group"><label id="labelfor'+rid+l+'" for="field-1" class="control-label">NO. '+no1+' <span id="error_instruments" style="color:red;">*</span></label><input type="text" name="sampletest'+sampleid+'[]" id="sample0" class="form-control no'+rid+'" required></div></div><div class="col-md-5"><div class="form-group"><label for="field-1" class="control-label">UNIT <span id="error_instruments" style="color:red;">*</span></label><select name="sampletestunit'+sampleid+'[]" id="unit'+l+'" class="form-control commonunit'+rid+l+'" required></select></div></div><div class="col-md-1"><div class="form-group"><label for="field-1" class="control-label" style="margin-top:20px"></label><span class="btn btn-xs btn-warning remove_button21" id="'+l+'" name="'+rid+'"><i class="fa fa-minus"></i></span></div></div></div>');
 
 getunits('commonunit'+rid+l);
  l++;
  }else{
	  
	  alert('maximum testing limit is 5');
  }

 
 });
 

 
 $(document).on('click', '.remove_button21', function(){
 var button_id = $(this).attr("id");
 
 var row_id = $(this).attr("name");


 $('#row11'+button_id).remove();
	 
  l--; 
  
 /**var no=$(".no"+row_id).length;
var i;
for (i = 1; i < no+1; i++) {
	var d='No.'+i;
	
	$( ".rowforsample"+row_id+" .testtime"+row_id+i+" label" ).html(d);
  
} **/
 
  
  
 });
 
 
 function getunits(classs)
 {
	
	 
	var ins=$("#instruments").val();
	
			$.ajax({
			type:"post",
			url:"<?php echo page_url;?>Sampletes/getmachineunits",
			data:"instrument="+ins,
			success:function(data){
				
				$("."+classs).html(data);
			
			}
			});
	 
	 
 }
 
 function checkfileforvalidation()
 {
	
	   var $fileUpload = $("#file");
		if (parseInt($fileUpload.get(0).files.length)>2){
		alert("You can only upload a maximum of 2 files");
		return false;
		}else{
			
			return true;
		}
     
	 
	 
 }

</script>


<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>

</body>
</html>