<?php
if($this->uri->segment(3)=='')
{
	echo "INVALID ACCESS";exit;
	
}else
{
	
	$rest=$this->db->select('a.*')->from('machine_parts_with_picture a')->where('a.id',$this->uri->segment(3))->get();
	if($rest->num_rows()==0)
	{
		echo "Invalid Access"; exit;
	}
	
}

$CI =& get_instance();
$CI->load->model('Store_model');

$itemname=$CI->Store_model->machineitemdetails($this->uri->segment(3));
if(count($itemname)>0)
{
	foreach($itemname as $itemname1);
	
	$name=$itemname1->part;
	$currentstock=$itemname1->current_stock;
	$shortname=$itemname1->shortname;
	$fincode=$itemname1->fincode;
	$specification=$itemname1->specification;
	
	
	
}else{
	$name='';
	$currentstock='';
	$shortname='';
	$fincode='';
	$specification='';
	
}

$getnoncrmuser=$CI->Store_model->getnoncrmuser();

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Issue General Items</title>

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
						
                           
                            <h4 class="text-center">ISSUE <?php echo $name;?>  AVAILABLE STOCK: <?php echo $currentstock;?> <?php echo $shortname;?></h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
		<div class="row">
	
					<div class="card-box table-responsive">
					<div class="col-sm-12">
					<?php
					
					if($currentstock>0)
					{
					?>
					<form onsubmit="return validate();" id="form" action="<?php echo page_url;?>Store/issueuserwisemachineitem/<?php echo $this->uri->segment(3);?>" method="post">
					<input type="hidden" name="currentstock" value="<?php echo $currentstock;?>">

					 <div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Item Name <span id="error_machine_part" style="color:red;">*</span><span style="color:red;"></span></label>
														 
														 <input type="text" name="iname" id="iname" class="form-control req" readonly value="<?php echo $name;?>">
														
													</div>
												</div>
												
												
													 <div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Fincode<span id="error_machine_part" style="color:red;">*</span><span style="color:red;"></span></label>
														 
														 <input type="text" name="fcode" id="fcode" class="form-control req" readonly value="<?php echo $fincode;?>">
														
													</div>
												</div>
												
												
													 <div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Specification <span id="error_machine_part" style="color:red;">*</span><span style="color:red;"></span></label>
														 
														 <input type="text" name="iname" id="iname" class="form-control" readonly value="<?php echo $specification;?>">
														
													</div>
												</div>
												
												
												
												<div class="col-md-2" style="display:none">
												<div class="form-group">
												<label for="field-2" class="control-label">Jobcard No. <span id="error_machine_part" style="color:red;">*</span><span style="color:red;"></span></label>

												<input type="text" name="jobcard" id="jobcard" class="form-control" value="">

												</div>
												</div>

					
					
												<div class="col-md-2">
												<div class="form-group">
												<label for="field-2" class="control-label">Issue Quantity <span id="error_machine_part" style="color:red;">*</span><span style="color:red;"></span></label>

												<input type="text" name="iqty" id="iqty" class="form-control req only-numeric" onblur="checkforstock(this.value);">

												</div>
												</div>
												<script type="text/javascript">

													$(".only-numeric").on("input", function(evt) {
													var self = $(this);
													self.val(self.val().replace(/[^0-9\.]/g, ''));
													if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
													{
													evt.preventDefault();
													}
													});

 
 
												function checkforstock(issue)
												{
													var stoc="<?php echo $currentstock;?>";
														
													if(parseFloat(issue)>parseFloat(stoc))
													{
														alert('Issue cannot be greater than stock');
														$("#iqty").val('');
													}

												}
												
												/**
												$(document).ready(function() {
												$(".only-numeric").bind("keypress", function (e) {
												var keyCode = e.which ? e.which : e.keyCode

												if (!(keyCode >= 48 && keyCode <= 57)) {
												//$(".error").css("display", "inline");
												return false;
												}else{
												//$(".error").css("display", "none");
												}
												});
												}); **/

												</script>
												
												
												<div class="col-md-2">
												<div class="form-group">
												<label for="field-2" class="control-label">User Type <span id="error_machine_part" style="color:red;">*</span><span style="color:red;"><?php echo form_error('machine_part');?></span></label>

												<select name="usertype" class="form-control req" onchange="checkuser(this.value);">
												<option value="">USER TYPE</option>
												<option value="1">CRM</option>
												<option value="2">NON CRM</option>
												</select>
												</div>
												</div>
												
												<div class="col-md-2 noncrmuser" style="display:none">
												<div class="form-group">
												<label for="field-2" class="control-label">NON CRM USER NAME <span id="error_machine_part" style="color:red;">*</span></label>

												<select name="noncrm" id="noncrm" class="form-control">
												<option value="">Select User</option>
												<?php
												if(count($getnoncrmuser)>0)
												{
													foreach($getnoncrmuser as $getnoncrmuser1)
													{
												?>
												<option value="<?php echo $getnoncrmuser1->id;?>"><?php echo $getnoncrmuser1->employee_name;?></option>
												<?php
													}
												}
												?>
												</select>

												</div>
												</div>
												
												
												
												<div class="col-md-2 crmuser" style="display:none">
												<div class="form-group">
												<label for="field-2" class="control-label">User Type <span id="error_machine_part" style="color:red;">*</span><span style="color:red;"><?php echo form_error('machine_part');?></span></label>

												<select name="crmusers" class="form-control select2" id="crms">
												<option value="">CRM USER</option>
												<?php
												$restyu=$CI->Store_model->getallusers();
												
												if(count($restyu)>0)
												{
													foreach($restyu as $restyu1)
													{
												?>
												<option value="<?php echo $restyu1->user_id;?>"><?php echo $restyu1->first_name;?> <?php echo $restyu1->last_name;?></option>
												<?php
													}
												}
												?>
												</select>

												</div>
												</div>
												
													<div class="col-md-4">
												<div class="form-group">
												<label for="field-2" class="control-label">Remarks if(any)</label>

												<textarea name="remarks" id="rmk" class="form-control" placeholder="Remarks (If Any)"></textarea>

												</div>
												</div>
												
												</div>
												
												<div class="row">
												<div class="col-md-12">
												<input type="submit" name="sub" class="btn btn-success pull-right" >
												</div>
												
												</div>
												
												</form>				   
<?php
					}else{
					?>
					<h3 class="text-center">Stock Not Available. Issue is not Allowed</h3>
					<?php
					}
					?>
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
		

	 


<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
	$(".select2").select2({});
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready'

function displayqty(itemid,row)
{
	if($('.itemid' + row).is(":checked"))
	{
		$("#recvqty"+itemid+row).css('display','');
		$("#recvqty"+itemid+row).attr('required',true);
		
	}else{
		
			$("#recvqty"+itemid+row).css('display','none');
		$("#recvqty"+itemid+row).attr('required',false);
	}
	
	
}

  function isNumberKey(evt)
       {
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
       }
	   
	   function checkmaxqty(itemid,rowid,reqqty)
	   {
		  
		   var recvq=$("#recvqty"+itemid+rowid).val();
		   if(recvq.trim()>reqqty.trim())
		   {
			   alert('Recieved QTY cannot be more than Required Qty');
			   recvq=$("#recvqty"+itemid+rowid).val('');
			   recvq=$("#recvqty"+itemid+rowid).focus();
			   
			   
		   }
		   
		   
	   }
	   
	   function checkuser(vaaal)
	   {
		   if(vaaal!='')
		   {
		   if(vaaal==1)
		   {
			   $(".crmuser").css('display','');
			    $(".noncrmuser").css('display','none');
			    
			    $("#crms").addClass('req');
			    $("#noncrm").removeClass('req');
			   
			   
		   }else{
			   
			    $(".crmuser").css('display','none');
			    $(".noncrmuser").css('display','');
			       $("#crms").removeClass('req');
			    $("#noncrm").addClass('req');
			   
		   }
		   }else{
			   
			     $(".crmuser").css('display','none');
			    $(".noncrmuser").css('display','none');
			        $("#crms").removeClass('req');
			    $("#noncrm").removeClass('req');
			   
		   }
		   
		   
	   }
	   
	  function validate1()
{
 var isValid=0;
 $("#form :visible :input").each(function() {
   var element = $(this).val();
  if (element=="") {
     $(this).focus();
	  isValid=1;
   }
});
 
 
 if(isValid==0)
 {
     return true;
 }else
 {
     alert('All Fields are mandatory');
     return false;
 }
    
}


function validate()
{
    
    var value = $('.req').filter(function () {
    return this.value === '';
});
 
 if (value.length == 0) {
   
   return true;
} 
else if (value.length > 0) {
    alert('Please fill out all required fields.');
	return false;
}
    
}
</script>
</body>
</html>