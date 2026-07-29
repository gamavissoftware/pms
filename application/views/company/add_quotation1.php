<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Testronix Quotation</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/dataTablesles/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
        <link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" >

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
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right">
						  
                               
                            </div>
                           
                             <h4>Create New Lead</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">

								
   <form id="quotation" method="post" action="<?php echo page_url;?>/Quotation1/addquotation"> 
<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12" style="margin-top: 20px;">
    <input type="hidden" name="custom_qoute" value="0">
                                        
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="field-1" class="control-label">Lead Source <span style="color:red">*</span></label>
                        <span id="error_customer_name" style="color:red;"></span>
                        <select class="form-control" name="lead_source" id="lead_source" required>
                            <option value="">---SELECT---</option>
                            <?php foreach($getLeadSource as $source) {?>
                            <option value="<?php echo $source->id;?>"><?php echo $source->source;?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="field-1" class="control-label">Designation <span style="color:red">*</span></label>
                        <span id="error_customer_name" style="color:red;"></span>
                        <select class="form-control" name="designation" id="designation" required>
                            <option value="">---SELECT---</option>
                            <?php foreach($getDesignations as $designation) {?>
                            <option value="<?php echo $designation->id;?>"><?php echo $designation->designation;?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                        <label for="field-2" class="control-label">Status</label>
                        <span id="error_status" style="color:red;">*</span>
                        <select class="form-control" id="lead_stage" name="lead_stage" required>
                            <?php foreach($getLeadCreated as $lead_stage) {?>
                            <option value="<?php echo $lead_stage->id;?>" <?php if($lead_stage->id == 7) {echo 'selected';}?>><?php echo $lead_stage->lead_status;?></option>
                            <?php } ?>
                        </select>
                  </div>
                </div>

               <div class="col-md-1">
                    <div class="form-group">
                        <label for="field-1" class="control-label">Title <span style="color:red">*</span></label>
                        <span id="error_customer_name" style="color:red;"></span>
                        <select class="form-control" name="title" id="title" required>
							<option value="Mr.">Mr.</option>
							<option value="Mrs.">Mrs.</option>
						</select>
                    </div>
                    </div>
            
				
				 <div class="col-md-2">
                    <div class="form-group">
                        <label for="field-1" class="control-label">Customer Name <span style="color:red">*</span></label>
                        <span id="error_customer_name" style="color:red;"></span>
                        <input type="text" class="form-control" name="customer_name" id="customer_name" value="" required>
                    </div>
                  </div>
					 <div class="col-md-2">
                    <div class="form-group">
                        <label for="field-1" class="control-label">Company Name <span style="color:red">*</span> </label>
                        <span id="error_customer_name" style="color:red;"></span>
                        <input type="text" class="form-control" name="company_name" id="company_name" value="" required>
                    </div>
                    </div>
					 <div class="col-md-2">
                    <div class="form-group">
                        <label for="field-1" class="control-label">Contact Number</label>
                        <span id="error_contact_number" style="color:red;"></span>
                        <input type="text" class="form-control" name="contact_number" id="contact_number" value="">
                    </div>
                    </div>
					
					<div class="col-md-3">
                    <div class="form-group">
                        <label for="field-1" class="control-label">Email</label>
                        <span id="error_email" style="color:red;"></span>
                        <input type="email" class="form-control" name="email" id="email" value="">
                    </div>
                    </div>

                     <div class="col-md-2">
                        <div class="form-group">
                        <label for="field-2" class="control-label">Export</label>
                        <span id="error_export" style="color:red;"></span>
                        <select class="form-control" name="export" id="export" onchange="hide_state()">
                            <option value="0">NO</option>
                            <option value="1">YES</option>
                        </select>
                        </div>
                    </div>

                     <div class="col-md-2" id="hide_state">
                    <div class="form-group">
                    <label for="field-2" class="control-label">State <span style="color:red">*</span></label>
                    <span id="error_state" style="color:red;"></span>
                    <select class="form-control" name="state" id="state" required>
                        <option value="">Select State</option>
                        <?php 
                        $q = $this->db->select('state_id, state_name')->from('states')->where('country_id','101')->get();
                        foreach($q->result() as $row){
                        ?>
                        <option value="<?php echo $row->state_id;?>"><?php echo $row->state_name;?></option>
                        <?php }?>
                    </select>
                    </div>
                    </div>

                       <!--- merge start---->
                     <div class="col-md-6">
                    <div class="form-group">
                    <label for="field-2" class="control-label">Add Email in CC with (,) between to emails</label>
                    <span id="error_address" style="color:red;"></span>
                    <input type="text" class="form-control" name="ccemails" id="ccemails" value="">
                    </div>
                    </div>
                    <!--- merge end---->
                    

                   <!--  <div class="col-md-3">
                        <div class="form-group">
                             <label for="field-2" class="control-label">Next Follow-up Date</label>
                             <input type="text" class="form-control" value="<?php echo date('d-m-Y', strtotime(' +1 day')); ?>" readonly>
                        </div>
                    </div> -->

                    <script type="text/javascript">
                        function hide_state() {
                            var export_var = $('#export').val();

                                if(export_var == 1) {
                                  $('#hide_state').css('display', 'none');  
                                  $('#state').val('');  
                                  $("#state").attr('required',false);
                                } else {
                                  $('#hide_state').css('display', 'block');  
                                  $("#state").attr('required',true);

                                }
                        }
                    </script>
					
                   
					
					 <div class="col-md-4">
                    <div class="form-group">
                    <label for="field-2" class="control-label">Customer Address</label>
                    <span id="error_address" style="color:red;"></span>
                    <input type="text" class="form-control" name="address" id="address" value="">
                    </div>
                    </div>
					
					
					
					<!--  <div class="col-md-6">
                    <div class="form-group">
                    <label for="field-2" class="control-label">Add Email in CC with (,) between to emails</label>
                    <span id="error_address" style="color:red;"></span>
                    <input type="text" class="form-control" name="ccemails" id="ccemails" value="">
                    </div>
                    </div> -->
                                              
      <div style="clear:both;height:10px"></div>
        <div class="col-md-6">
        <div class="form-group">
        <label for="field-1" class="control-label">Product Name <span style="color:red">*</span></label>
        <span id="error_product" style="color:red;"></span>
        <select class="form-control select3" id="product" name="product[]" required>
                                                    <option value="">--Select Product--</option>
                                                    <?php 
                                                    $this->db->select('id, instruments_name, model_number')->from('presto_instruments')->where('status','1')->where('instruments_of','2');
                                                    $this->db->order_by('instruments_name','asc');
                                                    $query = $this->db->get();
                                                    $res = $query->result();
                                                    foreach($res as $row){
														
                                                    ?>
                                                    <option value="<?php echo $row->id;?>"><?php echo strtoupper($row->instruments_name);?> (<?php echo strtoupper($row->model_number);?>)</option>
                                            <?php }?>       
                                                </select>
                                               
        </div>
        </div>
                                
               
             <div class="col-md-1">
                <div class="form-group">
                    <label for="field-1" class="control-label">Quantity <span style="color:red">*</span></label>
                    <span id="error_product_quantity" style="color:red;"></span>
                    <input type="number" class="form-control" id="productquantity" name="productquantity[]" value="1" required>
            
                </div>
            </div>                     
                                                
             <div class="col-md-2">
            <div class="form-group" style="margin-top:25px">
            <button type="button" class="btn btn-warning" name="add" id="addmore_btn">Add More Product<i class="fa fa-plus"></i></button>
            </div>
            </div>


     <div class="col-md-12">                                          
    <div id="dynamictasks"></div>
	</div>
           
<div class="col-md-12" style="margin-top:30px">
<div class="form-group pull-right">
<input type="submit" id="save" class="btn btn-info" value="Submit"> 
</div>
</div>
                           
                                            
                                       
                                    </div>
                                </div>
                                </form>
                                   

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

        <script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
		
        <script> $(document).ready(function() {


                $("#datepicker1").datepicker();
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
                })



               


            });</script>
		<script type="text/javascript">
         $(document).ready(function(){
 var i=1;



 $('#addmore_btn').click(function(){


 
 $('#dynamictasks').append('<div id="row'+i+'" class="row">   <div class="col-md-6"><div class="form-group"><label for="field-1" class="control-label">Product Name</label><span id="error_support_option" style="color:red;"></span><select class="form-control select3'+i+'" id="product" name="product[]"><option value="">--Select Product--</option><?php $this->db->select('id, instruments_name, model_number')->from('presto_instruments')->where('status','1')->where('instruments_of','2');$this->db->order_by('instruments_name','asc');$query = $this->db->get();$res = $query->result();foreach($res as $row){?><option value="<?php echo $row->id;?>"><?php echo strtoupper($row->instruments_name);?> (<?php echo strtoupper($row->model_number);?>)</option><?php }?></select></div></div><div class="col-md-1"><div class="form-group"><label for="field-1" class="control-label">Quantity</label><input type="number" class="form-control" id="productquantity" name="productquantity[]" value="1"></div></div><div class="col-md-2"><div class="form-group pull-left" style="padding-top:23px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div><br/>');
 
initializeSelect2('select3'+i);
 i++;
 });

 

                                                  
 
 
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id+'').remove();
 });
 
});

          function myfunction(id){
            
                                                    var product=$(".uname"+id).val();
                                                    $.ajax({
                                                    type:"post",
                                                    url:"<?php echo page_url;?>Quotation/productspecifications",
                                                    data:"product="+product,
                   
                                                    success:function(data){
                                                    $(".getname"+id).html(data);
                                                    }
                                                    });
                                                    }


      </script> 



<script type="text/javascript">
         $(document).ready(function(){
 var i=1;
 $('#addmoreterm_btn').click(function(){
 i++;
 
 $('#dynamicterms').append('<div id="row'+i+'" class="row"> <div class="col-md-7"><div class="form-group"><label for="field-1" class="control-label">Terms & Condition</label><select class="form-control" id="select3'+i+'" name="terms[]"><option value="">--Select Terms and Condition--</option><?php $this->db->select('*')->from('terms_and_conditions_master')->where('status','1'); $this->db->order_by('id','asc'); $query = $this->db->get(); $res = $query->result(); foreach($res as $row){?> <option value="<?php echo $row->id;?>"><?php echo $row->title;?></option> <?php }?> </select> </div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:30px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div><br/>');
 initializeSelect2('select3'+i);
 
 });
 
 
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id+'').remove();
 });
 
});
      </script> 

<script language="javascript" type="text/javascript">   

$(document).ready(function() {
$('.select3').select2({ });
$("#save").click(function() {
var customer_name = $("#customer_name").val();
if(customer_name=='')
{
	$("#error_customer_name").html('Required !');
}else{
   $("#error_customer_name").html(''); 
}
var quotation_date = $("#datepicker1").val();
if(quotation_date == '')
{
	
	$("#error_quotation_date").html('Required !');
}else{
   $("#error_quotation_date").html(''); 
}

var reference_number = $("#reference_number").val();
if(reference_number == '')
{
    
    $("#error_reference_number").html('Required !');
}else{
   $("#error_reference_number").html(''); 
}

var subject = $("#subject").val();
if(subject == '')
{
    
    $("#error_subject").html('Required !');
}else{
   $("#error_subject").html(''); 
}

var greetingtitle = $("#greetingtitle").val();
if(greetingtitle=='')
{
    
    $("#error_greeting_title").html('Required !');
}else{
   $("#error_greeting_title").html(''); 
}

var address = $("#address").val();
if(address=='')
{
    
    $("#error_address").html('Required !');
}else{
   $("#error_address").html(''); 
}

var product = $("#product").val();
if(product=='')
{
    
    $("#error_product").html('Required !');
}else{
   $("#error_product").html(''); 
}

var productspecification = $("#productspecification").val();
if(productspecification=='')
{
    
    $("#error_product_specification").html('Required !');
}else{
   $("#error_product_specification").html(''); 
}

var productquantity = $("#productquantity").val();
if(productquantity=='')
{
    
    $("#error_product_quantity").html('Required !');
}else{
   $("#error_product_quantity").html(''); 
}

var terms = $("#terms").val();
if(terms=='')
{
    
    $("#error_terms").html('Required !');
}else{
   $("#error_terms").html(''); 
}




if(customer_name=='' || quotation_date=='' || reference_number=='' || subject=='' || greetingtitle=='' ||  address=='' || product=='' || productspecification=='' || productquantity=='' || terms=='')
{
	
	return false;
}

});
});
</script>
<script>

	  function initializeSelect2(selectElementObj) {
		$('.'+selectElementObj).select2({
			
		 });
      }

	  </script>

 <script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
    </body>
</html>