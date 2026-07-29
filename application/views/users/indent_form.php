<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> INDENT</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
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
<style>
body{
	font-size:11px;
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
           <span style="text-align:center"> <img src="https://prestomitr.com/assets/images/logo-1.png"></span>
          <?php //$this->load->view('common/nav-menu');?>
        </header>
        <!-- End Navigation Bar-->
        <div class="wrapper">
            <div class="container">

                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right">
                               
                            </div>
                           
                            <h4 class="page-title">INDENT FORM</h4>
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
								<h5 class="text-center" style="color:red;">If total price is greater than 500 then indent will go for approval.</h5>
								<!--<div class="col-md-2"><input type="radio" name="indenttype" value="2" onchange="openform(0); removeotherrows(0);">&nbsp;<strong>General Purpose Item</strong></div>-->
								</div>
							<div style="clear:both;height:20px;"></div>
							
								<form id="loginForm" method="post" action="<?php echo page_url;?>User/saveindent/" action="post">
								<div id="pageloader">
                                    <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
                                    </div>
							<div id="indentform">
							
							<div class="col-sm-12 col-xs-12 col-md-12">

                            <div class="col-md-2">
							<div class="form-group">
							<label for="field-2" class="control-label">Your Employee Code</label>
							<span id="error_item_name" style="color:red;"></span>
							
						
							<input type="text" class="form-control" name="employeecode" id="employeecode" value="">
							</div>
							</div>
							<div class="col-md-2">
							<div class="form-group">
							<label for="field-2" class="control-label">Indent For</label>
							<span id="error_item_name" style="color:red;"></span>
							<select class="form-control" name="type" id="type" onChange="fetchindenttype(this.value);" style="font-size:11px;" required>
							<option value=''>Indent Type</option>
							<option value='1'>Machine Related Items</option>
							<option value='2'>General Items</option>

							</select>
							</div>
							</div>
							
							
							</div>
							
							
                                <div class="col-sm-12 col-xs-12 col-md-12" style="display:none" id="testtttt">
								<div class="col-md-4 housekeepingfields">
												<div class="form-group">
												<label for="field-2" class="control-label">ITEM NAME</label>
												<span id="error_item_name" style="color:red;"></span>
												  <select class="form-control select" style="text-transform: uppercase;" name="instruments[]" id="instruments0" Onchange="getunit(0);"></select>
												   <span><a href="javascript:;" onclick="requestmaster();">Item Not in list? Request Master</a></span>
												</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														<label>QTY</label>
														<input type="number" class="form-control" min="1" name="qty[]" id="qty0" value="" onkeyup="checkprice(0);" required>
													</div>
												</div>
												<!--<div class="col-md-2">
													<div class="form-group">
														<label>PRICE</label>
														<input type="text" class="form-control" name="price[]" id="price0" value="" readonly>
													</div>
												</div>-->
												<div class="col-md-2">
													<div class="form-group">
														<label>UNIT</label>
														<input type="text" class="form-control" name="unit[]" id="unit0" value="" readonly>
													</div>
												</div>
												<div class="col-md-1">
													<div class="form-group" style="margin-top:25px">
														<button type="button" class="btn btn-warning" name="add" id="addmore_btn1"><i class="fa fa-plus"></i></button>
													</div>
												</div>
												
                                            </div>
											
											
											
											<div class="col-md-12">
												<div id="dynamictasks1"></div>
											</div>
											
											 <div class="col-md-8" style="display:none" id="remarkbox">
							<div class="form-group">
							<label for="field-2" class="control-label">Remarks</label>
							<span id="error_item_name" style="color:red;"></span>
							<input type="text" class="form-control" name="remarks" id="remarks" value="">
							</div>
							</div>
											
											
											
										<div class="row">	
										<div class="col-md-9"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" class="btn btn-success" value="Update">
											</div>
										</div>
									
									
                                   

                                </div>
								
								</div>
								</form>
								

                            </div>
                            <!-- end row -->
                        </div> <!-- end ard-box -->
                    </div><!-- end col-->

                </div>
                <!-- end row -->
 
 
  <!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Request New Master</h4>
      </div>
	
<form method="post">
	<input type="hidden" name="itemtype" id="itemtype" value="">
	<div class="modal-body">
	
	<div class="row">
	<div class="col-md-4">
	<input type="text" class="form-control" placeholder="Item Name"  name="itemnew[]" required/>
	</div>
	
	<div class="col-md-2">
	<input  type="number" step="0.01" class="form-control" placeholder="Qty" name="qtynew[]" required/>
	</div>
	
	<div class="col-md-3">
	<select name="unitnew[]" id="unitnew" class="form-control">
	<option value="">Select Unit</option>
	<?php
	$Restey=$this->db->select('id,shortname')->from('units')->order_by('name','ASC')->get();
	if($Restey->num_rows()>0)
	{
		foreach($Restey->result() as $Restey1)
		{
	?>
	<option value="<?php echo $Restey1->id;?>"><?php echo strtoupper($Restey1->shortname);?></option>
	
	<?php
		}
	}
	?>
	</select>
	</div>
	
	<div class="col-md-3" style="margin-top:10px;">
	<a href="javascript:;"><span class="btn btn-xs btn-success" id="addnew_btn1"><i class="fa fa-plus"></i></button></a>
	</div>
	</div>
	<div style="clear:both;height:5px"></div>
	
	<div id="dynamicdata12">
	
	</div>
	
	
	</div>
	<div class="modal-footer">
	<span class="btn btn-xs btn-success" onclick="checkvalidate();">Submit</span>
	</div>
</form>
	  
    </div>

  </div>
</div>

<!-- END MODAL __>


                <!-- Footer -->
<?php //$this->load->view('common/footer');?>
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

        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

 <script>

	$('#type').change(function(){
	var ot=$("#type").val();
	var purl="<?php echo page_url;?>Store/getmachines/"+ot;
$('.select2').select2({ });
$('.select3').select2({ });
$('#instruments0').select2({ 

		placeholder: 'TYPE TO SELECT',
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
$('.select4').select2({ });
});


</script>

<script>
	  function initializeSelect2(selectElementObj,ot) {
		  var purl="<?php echo page_url;?>Store/getmachines/"+ot;
         $('.'+selectElementObj).select2({
			 placeholder: 'TYPE TO SELECT',
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
         
      }
	  </script>
	  
<script type="text/javascript">
$(document).ready(function(){
var k=1;
$('#addmore_btn1').click(function(){
$('#dynamictasks1').append('<div id="row'+k+'" class="row appendrows"><div class="col-md-12"><div class="col-md-4"><div class="form-group"><label for="field-2" class="control-label">ITEM NAME</label><span id="error_item_name" style="color:red;"></span><select class="form-control selectotheritems'+k+'" style="text-transform: uppercase;" name="instruments[]" id="instruments'+k+'" Onchange="getunit('+k+');"></select></div></div><div class="col-md-2"><div class="form-group"><label>QTY</label><input type="number" class="form-control" min="1" name="qty[]" id="qty'+k+'" value="" onkeyup="checkprice('+k+');" required></div></div><div class="col-md-2"><div class="form-group"><label>UNIT</label><input type="text" class="form-control" name="unit[]" id="unit'+k+'" value="" readonly></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:21px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+k+'"><i class="fa fa-close"></i></button></div></div></div></div><br/>');
 
 openform(k);
 var ot=$("#type").val();
 initializeSelect2('selectotheritems'+k,ot);

  k++;
  
 });
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id+'').remove();
 });
 
});
</script>
<script language="javascript" type="text/javascript">   

$(document).ready(function() {
	$('.select2').select2({ });
$("#save").click(function() {
	
var machine = $("#machine").val();
if(machine=='')
{
	$("#error_machine").html('Required!');
}

if(machine=='')
{
	
	return false;
}

});
});

function openform(id)
{

	$("#indentform").css('display','');
	var type=$("input[name='indenttype']:checked").val();
	if(type==1)
	{
		$(".machinefields").css('display','');
		$(".housekeepingfields").css('display','none');
	}else
	{
		$(".machinefields").css('display','none');
		$(".housekeepingfields").css('display','');
	}
	
	$.ajax({
	type:"post",
	url:"<?php echo page_url;?>Store/getcategory",
	data:"category="+type,
	success:function(data){
		
	$("#category"+id).html(data);
	}
	});
	
	
	
			
	
}

function removeotherrows(id)
{
	
$(".appendrows").remove();
	
}
</script>

<script>
	 
	  function validateall()
	  {
		  
		 $('#indentform input').each(function() {
        if(!$(this).val()){
            alert('All fields are mandatory');
           return false;
        }
		
    });
		  
	  }
	  </script>
	  
<script>
function fetchindenttype(vaaal)
{
	if(vaaal!='')
	{
	$("#testtttt").css('display','');
	//fetch_material_type_data();
		
	}else{
		
		$("#testtttt").css('display','none');
		//fetch_material_type_data();
	}
	
}

function getunit(i){
	checkprice(i);
	var item = $("#instruments"+i).val();
	var ot=$("#type").val();
	$.ajax({
	type:"post",
	url:"<?php echo page_url;?>Store/getitemunit",
	data:"item="+item+"&ot="+ot,
	success:function(data){
	$("#unit"+i).val(data);
	}
	});
}

function checkprice(i){
	var item = $("#instruments"+i).val();
	var ot=$("#type").val();
	var qty = $("#qty"+i).val();
	if(qty!='' || qty!=0)
	{
	$.ajax({
	type:"post",
	url:"<?php echo page_url;?>Store/getselected_itemprice",
	data:"item="+item+"&ot="+ot+"&qty="+qty,
	success:function(data){
	      if(data>500){
	          $("#remarkbox").show();
	          $("remarkbox").attr('required',true);
	      }else{
	          $("#remarkbox").hide();
	           $("remarkbox").attr('required',false);
	      }
	$("#price"+i).val(data);
	}
	});
	}else
	{
	    $("#price"+i).val('0');
	}
}
</script>
<script>
$(document).ready(function(){
	$("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready



function requestmaster()
{
	var type=$("#type").val();
	if(type!='')
	{
	$("#myModal").modal('show');
	$("#itemtype").val(type);
	}else{
		
		alert('Select Indent Type First');
		return false;
	}
	
}


function checkvalidate()
{
	var type=$("#type").val();
	
	var empty = true;
$('#masterform :input').each(function(){
   if($(this).val()==""){
      alert('All fields are manadatory');
	  return false;
    }
 });
	
 if(empty==true)
 {
	var values = [];
$("input[name='itemnew[]']").each(function() {
    values.push($(this).val());
});

var item=values.join(',');
var values1 = [];
$("input[name='qtynew[]']").each(function() {
    values1.push($(this).val());
});
	
	var qty=values1.join(',');
	
var values2 = [];
$("select[name='unitnew[]']").each(function() {
    values2.push($(this).val());
}); 
	
	var unit=values2.join(',');

	 $.ajax({
	type:"post",
	url:"<?php echo page_url;?>Store/requestmaster",
	data:"item="+item+"&qty="+qty+"&unit="+unit+"&itemtype="+type,
	success:function(data){
		if(data=='')
		{
			alert('Master Request Added');
			$("#myModal").modal('hide');	
		}else{
			
			alert('Entry Failed');
			return false;
		}
		
	}
	});
	
	
 }else{
	 
	 alert('All Fields are mandatory');
	 return false;
 }
	
}



$(document).ready(function(){
var m=1;
$('#addnew_btn1').click(function(){
$('#dynamicdata12').append('<div class="row" id="row123'+m+'"><div class="col-md-4"><input type="text" class="form-control" placeholder="Item Name"  name="itemnew[]" required/></div><div class="col-md-2"><input type="number" step="0.01" class="form-control" placeholder="Qty" name="qtynew[]" required /></div><div class="col-md-3"><select name="unitnew[]" id="unitnew" class="form-control" required><option value="">Select Unit</option><?php $Restey=$this->db->select('id,shortname')->from('units')->order_by('name','ASC')->get(); if($Restey->num_rows()>0){ foreach($Restey->result() as $Restey1){?><option value="<?php echo $Restey1->id;?>"><?php echo strtoupper($Restey1->shortname);?></option><?php }}?></select></div><div class="col-md-3" style="margin-top:10px;"><a href="javascript:;"><span class="btn btn-xs btn-success btn_remove121" id="'+m+'"><i class="fa fa-close"></i></button></a></div></div><div style="clear:both;height:5px"></div>');
   m++;
  
 });
 
  $(document).on('click', '.btn_remove121', function(){
 var button_id = $(this).attr("id");
 $('#row123'+button_id).remove();
 }); 
 
 
});
</script>

<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
    </body>
</html>