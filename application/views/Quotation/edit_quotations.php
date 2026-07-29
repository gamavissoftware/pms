<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Edit Power Factor</title>

        <!-- Table Responsive css -->
        <script src="<?php echo assets_url;?>js/angular.min.js"></script>
         <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

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
                           
                             <h4>Create New Quotation</h4>
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

                                
                                   <form id="quotation" method="post" action="<?php echo page_url;?>/Quotation/updatequotation/<?php echo $quotation_details['id'];?>">
<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12" style="margin-top: 20px;">
                                        
            <div class="row">
            <div class="col-md-2">
            <div class="form-group">
            <label>Digital Letter Head</label>
            <div class="row">
                <?php 

                if(!empty($quotation_details['letter_head']) && ($quotation_details['letter_head'] == 1)){
                $checked = 'checked';
                }else{
                 $checked = '';
                }
                ?>
                <div class="col-md-6">
                    <input type="radio" name="letter_head" id="letter_head" value="1" <?php echo $checked;?>/> With
                </div>
               <?php  if($quotation_details['letter_head'] == 0){
                $checked = 'checked';
                }else{
                $checked = '';
                } ?>
                <div class="col-md-6">
                    <input type="radio" name="letter_head" id="letter_head" value="0" <?php echo $checked;?>/>
            Without
                </div>
            </div>
           
            
            </div>
            </div>
            
             <div class="col-md-2">
                    <div class="form-group">
                        <label for="field-1" class="control-label">Customer Name</label>
                        <span id="error_customer_name" style="color:red;"></span>
                        <select class="form-control" id="customer_name" name="customer_name">
                                                    <option value="">--Select Customer--</option>
                                                    <?php 
                                                    $this->db->select('*')->from('presto_customers')->where('status','1');
                                                    $this->db->order_by('customer_id','asc');
                                                    $query = $this->db->get();
                                                    $res = $query->result();
                                                    foreach($res as $row){
                                                    if($row->customer_id == $quotation_details['customer_name']){
                                                    $selected = 'selected';
                                                    }else{
                                                    $selected = '';
                                                    }
                                                    ?>
                                                    <option value="<?php echo $row->customer_id;?>" <?php echo $selected;?>><?php echo $row->customer_name;?></option>
                                            <?php }?>       
                                                </select>
                                                
                                              
                    </div>
                    </div>
           
           
                <div class="col-md-2">
                <div class="form-group">
                <label for="field-1" class="control-label">Quotation Date</label>
                <span id="error_quotation_date" style="color:red;"></span>
                <div>
                <input type='text' name="quotation_date" id="datepicker1" class="form-control" value="<?php echo $quotation_details['quotation_date'];?>" />
                  
                </div>
                </div>
                </div>
                <div class="col-md-2">
                <div class="form-group">
                <label for="field-2" class="control-label">Refrence Number</label>
                <span id="error_reference_number" style="color:red;"></span>
                <input name="reference_number" id="reference_number" class="form-control" value="<?php echo $quotation_details['reference_number'];?>"/>
                    </div>
                    </div>
                     <div class="col-md-2">
                    <div class="form-group">
                         <label for="field-2" class="control-label">Select Subject </label>
                          <span id="error_subject" style="color:red;"></span>
                          <select class="form-control" id="subject" name="subject">
                                                    <option value="">--Select Subject--</option>
                                                    <?php 
                                                    $this->db->select('*')->from('standard_subject')->where('status','1');
                                                    $this->db->order_by('id','asc');
                                                    $query = $this->db->get();
                                                    $res = $query->result();
                                                    foreach($res as $row){
                                                    if($row->id == $quotation_details['subject']){
                                                    $selected = 'selected';
                                                    }else{
                                                    $selected = '';
                                                    }
                                                    ?>
                                                    <option value="<?php echo $row->id;?>" <?php echo $selected;?>><?php echo $row->title;?></option>
                                            <?php }?>       
                                                </select>
                                                <script type="text/javascript">
                                            
                                                    $("#subject").change(function(){
                                                    var subject=$("#subject").val();
                                                    $.ajax({
                                                    type:"post",
                                                    url:"<?php echo page_url;?>Quotation/standardtext",
                                                    data:"subject="+subject,
                                                    success:function(data){
                                                    $("span#standardtext").html(data);
                                                    }
                                                    });
                                                    });
                                                $( "#subject" ).trigger( "change" );
                                                </script>
                    </div>
                   </div>
                    <div class="col-md-2">
                    <div class="form-group">
                         <label for="field-2" class="control-label">Select Greeting Title </label>
                          <span id="error_greeting_title" style="color:red;"></span>
                    <select class="form-control" id="greetingtitle" name="greetingtitle">

                    <option value="">--Select Greeting Title--</option>
                    <option value="1" <?php if($quotation_details['greetingtitle'] == 1){echo "selected";}?>>Dear Sir</option> 
                    <option value="2" <?php if($quotation_details['greetingtitle'] == 2){echo "selected";}?>>Dear Mam</option> 
                    </select>
                    </div>
                    </div>
                   
                    <div class="col-md-6">
                    <div class="form-group">
                    <label for="field-2" class="control-label">Standard Text</label>
                    <span id="standardtext"><input class="form-control"  name="standardtext" id="standardtext" placeholder="Standard text"></span>
                   
                 
                    </div>
                    </div>  
                    <div class="col-md-6">
                    <div class="form-group">
                    <label for="field-2" class="control-label">Customer Address</label>
                    <span id="error_address" style="color:red;"></span>
                    <textarea class="form-control" name="address" id="address"><?php echo $quotation_details['address'];?></textarea>
                    </div>
                    </div> 

<div class="col-md-2">
            <div class="form-group">
            <input type="checkbox" name="gst" id="gst" <?php if($quotation_details['gst_applicable'] == 1){echo "checked";}?> value="1"  onchange="isgst();"/> GST Applicable
            </div>
			 </div>	
<div class="col-md-1 withgst" style="display:none">
<div class="form-group">
            <input type="text" name="gst_percent" class="form-control" value="<?php echo $quotation_details['gst_rate'];?>" placeholder="in %" id="gst_percent"  style="width:100%"/>
            </div>
			</div>
			
			<div class="col-md-3 withgst" style="display:none">
			 <div class="form-group">
            <select type="text" name="gst_state" class="form-control" id="gst_state" />
			<option value="">Supply?</option>
			<option value="1" <?php if($quotation_details['gst_supply'] == 1){echo "selected";}?>>Within Same State</option>
			<option value="2" <?php if($quotation_details['gst_supply'] == 2){echo "selected";}?>>Other State</option>
			</select>
            </div>
</div>	
          
<script>
function isgst()
{
	
	if($('#gst').is(":checked"))
	{
$(".withgst").css('display','');
$("#gst_percent").attr('required',true);
$("#gst_state").attr('required',true);

	}else{
		
		$(".withgst").css('display','none');
$("#gst_percent").attr('required',false);
$("#gst_state").attr('required',false);
	}

}

$( document ).ready(function() {
    	
if($('#gst').is(":checked"))
	{
$(".withgst").css('display','');
$("#gst_percent").attr('required',true);
$("#gst_state").attr('required',true);

	}else{
		
		$(".withgst").css('display','none');
$("#gst_percent").attr('required',false);
$("#gst_state").attr('required',false);
	}
});
</script>						
            
          <div style="clear:both;height:10px"></div>                                     
       <?php 
       if(!empty($productquotations)){
             foreach($productquotations as $element){
       ?>

        <span id="product<?php echo $element['id'];?>"><div class="col-md-4">
        <div class="form-group">
        <label for="field-1" class="control-label">Product Name</label>  
        <input type="text" class="form-control" value="<?php echo $element['product_name'];?>" readonly/>                       
        </div>
        </div>
                                
        <div class="col-md-5">
        <div class="form-group">
        <label for="field-1" class="control-label">Product Specifications</label>
        <input type="text" class="form-control" value="<?php echo $element['product_specifications'];?>" readonly/> 
        </div>
        </div>
        <div class="col-md-1">
        <div class="form-group">
        <label for="field-1" class="control-label">Quantity</label>
        <span id="error_product_quantity" style="color:red;"></span>
        <input type="number" class="form-control" value="<?php echo $element['product_quantity'];?>" readonly>
        </div>
        </div>
                                                        
        <div class="col-md-2">
        <div class="form-group" style="margin-top:25px">
        <a class="deleteproduct btn btn-danger" data-deleteproductid="<?php echo $element['id'];?>" name="deleteproduct" id="deleteproduct"><i class="fa fa-trash"></i></a>
        </div>
        </div></span>

       <?php } }?>
        <div class="col-md-4">
        <div class="form-group">
        <label for="field-1" class="control-label">Product Name</label>
        <span id="error_product" style="color:red;"></span>
        <select class="form-control" id="product" name="product[]">
                                                    <option value="">--Select Product--</option>
                                                    <?php 
                                                    $this->db->select('*')->from('presto_product_specifications')->where('status','1');
                                                    $this->db->order_by('product_id','asc');
                                                    $query = $this->db->get();
                                                    $res = $query->result();
                                                    foreach($res as $row){
                                                    ?>
                                                    <option value="<?php echo $row->product_id;?>"><?php echo $row->product_name;?></option>
                                            <?php }?>       
                                                </select>
                                                <script type="text/javascript">
                                            
                                                    $("#product").change(function(){
                                                    var product=$("#product").val();
                                                    $.ajax({
                                                    type:"post",
                                                    url:"<?php echo page_url;?>Quotation/productspecifications",
                                                    data:"product="+product,
                                                    success:function(data){
                                                    $("#productspecification").html(data);
                                                    }
                                                    });
                                                    });
                                            
                                                </script>
        </div>
        </div>
                                
                <div class="col-md-5">
                <div class="form-group">
                    <label for="field-1" class="control-label">Product Specifications</label>
                     <span id="error_product_specification" style="color:red;"></span>
                     <select class="form-control" id="productspecification" name="productspecification[]">
                     <option value="">--Select Product Specification--</option>  
                    </select>
            
                </div>
            </div>
             <div class="col-md-1">
                <div class="form-group">
                    <label for="field-1" class="control-label">Quantity</label>
                    <span id="error_product_quantity" style="color:red;"></span>
                    <input type="number" class="form-control" id="productquantity" name="productquantity[]" value="1" >
            
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
                                            
                    <?php foreach($termsquotations as $element){?>  
                    <span id="terms<?php echo $element['id'];?>"><div class="col-md-7">
                    <div class="form-group">
                    <label for="field-1" class="control-label">Terms & Condition</label>
                    <input type="text" value="<?php echo $element['title'];?>" class="form-control"readonly/>
            
                </div>
                </div> 
              <div class="col-md-1">
            <div class="form-group" style="margin-top:25px">
            <a class="deleteterms btn btn-danger" data-deletetermsid="<?php echo $element['id'];?>" name="deleteterm" id="deleteterm"><i class="fa fa-trash"></i></a>
            </div>
            </div></span>     
        <?php }?>



                    <div class="col-md-7">
                    <div class="form-group">
                    <label for="field-1" class="control-label">Terms & Condition</label>
                     <span id="error_terms" style="color:red;"></span>
                     <select class="form-control" id="terms" name="terms[]">
                                                    <option value="">--Select Terms and Condition--</option>
                                                    <?php 
                                                    $this->db->select('*')->from('terms_and_conditions_master')->where('status','1');
                                                    $this->db->order_by('id','asc');
                                                    $query = $this->db->get();
                                                    $res = $query->result();
                                                    foreach($res as $row){
                                                    ?>
                                                    <option value="<?php echo $row->id;?>"><?php echo $row->title;?></option>
                                            <?php }?>       
                                                </select>
            
                </div>
                </div> 
              <div class="col-md-1">
            <div class="form-group" style="margin-top:25px">
            <button type="button" class="btn btn-warning" name="add" id="addmoreterm_btn"><i class="fa fa-plus"></i></button>
            </div>
            </div>            
            <div class="col-md-3" style="margin:20px;">
            <div class="form-group">
            <?php 
            if($quotation_details['convert_to_performa'] == 1){
            $checked = 'checked';
            }else{
            $checked = '';
            }
            ?>
            &nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="convert_to_performa" id="convert_to_performa" value="1" <?php echo $checked;?>/> Convert Quotation To Performa
            </div>
            </div>
             <div class="col-md-12">                                          
             <div id="dynamicterms"></div>
             </div>
             
            <hr>
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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script> $(document).ready(function() {


                $("#datepicker1").datepicker();
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
                })



               


            });</script>

<script type="text/javascript">
 $(document).on('click','.deleteproduct', function(){
  
 var productid = $(this).data("deleteproductid");
   $.ajax({
            type:"post",
            url:"<?php echo page_url;?>Quotation/deletequotationproduct",
            data:"productid="+productid,

            success:function(data){

            $("span#product"+productid).html('<span class="alert alert-success">Product deleted</span>');
               setTimeout(function () {
                jQuery("span#product"+productid).remove()
            }, 5000);
            }
            });
 });   

</script>

<script type="text/javascript">
 $(document).on('click','.deleteterms', function(){
  
 var termsid = $(this).data("deletetermsid");
   $.ajax({
            type:"post",
            url:"<?php echo page_url;?>Quotation/deleteterms",
            data:"termsid="+termsid,

            success:function(data){

            $("span#terms"+termsid).html('<span class="alert alert-success">Terms deleted</span>');
               setTimeout(function () {
                jQuery("span#terms"+termsid).remove()
            }, 5000);
            }
            });
 });   

</script>
        <script type="text/javascript">
         $(document).ready(function(){
 var i=1;



 $('#addmore_btn').click(function(){

 i++;
 
 $('#dynamictasks').append('<div id="row'+i+'" class="row">   <div class="col-md-4"><div class="form-group"><label for="field-1" class="control-label">Product Name</label><span id="error_support_option" style="color:red;"></span><select class="uname'+i+' form-control" id="product" name="product[]" onChange="myfunction('+i+')"><option value="">--Select Product--</option> <?php  $this->db->select('*')->from('presto_product_specifications')->where('status','1'); $this->db->order_by('product_id','asc'); $query = $this->db->get(); $res = $query->result(); foreach($res as $row){ ?> <option value="<?php echo $row->product_id;?>"><?php echo $row->product_name;?></option><?php }?></select></div></div><div class="col-md-5"><div class="form-group"><label for="field-1" class="control-label">Product Specifications</label><select class="getname'+i+' form-control" id="productspecification'+i+'" name="productspecification[]"><option value="">--Select Product Specification--</option> </select></div></div><div class="col-md-1"><div class="form-group"><label for="field-1" class="control-label">Quantity</label><input type="number" class="form-control" id="productquantity" name="productquantity[]" value="1"></div></div><div class="col-md-2"><div class="form-group pull-left" style="padding-top:30px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'">Remove Product<i class="fa fa-close"></i></button></div></div></div><br/>');
 

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
 
 $('#dynamicterms').append('<div id="row'+i+'" class="row"> <div class="col-md-7"><div class="form-group"><label for="field-1" class="control-label">Terms & Condition</label><select class="form-control" id="terms" name="terms[]"><option value="">--Select Terms and Condition--</option><?php $this->db->select('*')->from('terms_and_conditions_master')->where('status','1'); $this->db->order_by('id','asc'); $query = $this->db->get(); $res = $query->result(); foreach($res as $row){?> <option value="<?php echo $row->id;?>"><?php echo $row->title;?></option> <?php }?> </select> </div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:30px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div><br/>');
 
 });
 
 
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id+'').remove();
 });
 
});
      </script> 

<script language="javascript" type="text/javascript">   
jQuery.noConflict();
$(document).ready(function() {
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







if(customer_name=='' || quotation_date=='' || reference_number=='' || subject=='' || greetingtitle=='' ||  address=='')
{
    
    return false;
}

});
});
</script>
    </body>
</html>