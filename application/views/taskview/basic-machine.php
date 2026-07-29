<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="<?php echo copyright; ?>">
<link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
<title><?php echo sitetitle; ?>Basic Machine </title>
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
<style>
.divheight{
padding-top:30px;
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
<style type="text/css">
.showdiv{
display: none;
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
<div class="container">

<!-- Page-Title -->
<div class="row" style="margin-top:20px;">
<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
<div class="page-title-box">
<div class="btn-group pull-right"></div>
<h4 class="text-center" style="background-color:#fbeeee; padding:10px; 10px; 10px; 10px;">Add Basic Machine Info</h4>
</div>
</div>
</div>
<!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

<div class="row">
<div class="col-xs-12">
<div class="card-box">
<form method="post" id="loginForm" action="<?php echo page_url;?>Task/addbasicmachine" enctype="multipart/form-data">
<div id="pageloader">
<img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
</div>
<div class="row">
<div class="col-sm-12 col-xs-12 col-md-12">

<!-- <div class="col-md-2">
<div class="form-group">
<label for="field-1" class="control-label">Upload PO</label>
<span id="error_attachpo" style="color:red;">*</span>
<input type="file" name="attachpo" id="attachpo" value="" class="form-control" required>
</div>
</div> -->


<!-- <div class="col-md-10">
<div class="form-group">
<label for="field-1" class="control-label">Payment Term <i class="fa fa-plus" data-toggle="modal" data-target="#con-close-modal"></i></label>
<span id="error_existingpaymentterms" style="color:red;">*</span>
<select class="form-control" name="existingpaymentterms" id="existingpaymentterms" required>
<option value="">Select Payment Term</option>
<?php 
$businessloc = 2;
$this->db->select('id,payment_terms')->from('payment_terms')->where('status',1);
if($this->uri->segment(3)){
$this->db->where('id',$this->uri->segment(3));
}
$q = $this->db->get();
foreach($q->result() as $row){
?>
<option value="<?php echo $row->id;?>"><?php echo $row->payment_terms;?></option>
<?php }?>
</select>
</div>
</div> -->

<div class="col-md-4">
<div class="form-group">
<label>Machine Type</label>
<span style="color:red" id="error_machine_type">*</span>
<input type="text" class="form-control" name="machine_type" required id="machine_type" value="BASIC MACHINE" readonly>
</div>
</div>



<div class="col-md-4" style="display:none">
<div class="form-group">
<label>Machine Model</label>
<span style="color:red" id="error_modelno">*</span>

<select class="form-control" name="modelno" id="modelno">
	<option value="">Select Model</option>
	<?php 
	$q = $this->db->select('id,model_no')->from('basic_machine_model_for_pms')->get();
	foreach($q->result() as $row){
	?>
	<option value="<?php echo $row->id;?>"><?php echo $row->model_no;?></option>
<?php }?>
</select>
</div>
</div>



<div class="col-md-4">
<div class="form-group">
<label>Date</label>
<span style="color:red" id="error_order_punch_date">*</span>
<input type="date" class="form-control" name="order_punch_date" id="order_punch_date" required value="<?php echo date('Y-m-d');?>">
</div>
</div>

<div class="col-md-4">
	<div class="form-group">
		<label>Speed</label>
		<span style="color:red" id="error_speed">*</span>
		<input type="text" class="form-control" name="speed" id="speed" value="" required>
	</div>
</div>


<div class="col-md-4">
	<div class="form-group">
		<label>Product</label>
		<span style="color:red" id="error_productname">*</span>
		<select class="form-control" name="productname" id="productname" required>
			<option value="">Select Product</option>
			<option value="1">Liquid / Paste</option>
			<option value="2">Powder / Granules</option>
			
		</select>
	</div>
</div>

<div class="col-md-4">
	<div class="form-group">
		<label>Refrence DF (if any)</label>
		<input type="text" class="form-control" name="referencedf" id="referencedf" value="">
	</div>
</div>


<div class="col-md-12" style="padding-top:30px;"></div><hr>

<div class="col-md-12">
<div class="form-group pull-right">
<input type="submit" class="btn btn-success" name="Save" id="savedata">
</div>
</div>


</div>


</form>
<!-- end row -->
</div> <!-- end card-box -->
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script>
$(document).ready(function(){
$("#teamupdate").attr('disabled',false);
$("#teamupdate").val('Update');
$("#loginForm").on("submit", function(){
// $("#pageloader").fadeIn();
$("#teamupdate").attr('disabled',true);
$("#teamupdate").val('Please Wait...');
});//submit
});//document ready
</script>

<script>
$(document).ready(function(){
$("#loginForm").on("submit", function(){
$("#pageloader").fadeIn();
});//submit
});//document ready
</script>

<script language="javascript" type="text/javascript">   
$(document).ready(function() {
$("#savedata").click(function() {

var order_punch_date = $("#order_punch_date").val();
if(order_punch_date=='')
{	 $("#order_punch_date").css("border", "1px solid red");
$("#error_order_punch_date").html('Required!');
}
var speed = $("#speed").val();
if(speed=='')
{
$("#speed").css("border", "1px solid red");
$("#error_speed").html('Required!');
}

var productname = $("#productname").val();
if(productname=='')
{
$("#productname").css("border", "1px solid red");
$("#error_productname").html('Required!');
}
var podate = $("#podate").val();
if(podate=='')
{
$("#podate").css("border", "1px solid red");
$("#error_podate").html('Required!');
}
var currency = $("#currency").val();
if(currency=='')
{
$("#currency").css("border", "1px solid red");
$("#error_currency").html('Required!');
}






if(order_punch_date=='' || speed=='' || productname=='')
{

return false;
}

});
});
</script>    


<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<script>

$(document).ready(function(){
$("#posave").attr('disabled',false);
$("#posave").val('submit');
$("#loginForm").on("submit", function(){
$("#posave").attr('disabled',true);
$("#posave").val('Please Wait...');

});//submit

});//document ready

</script>
</body>
</html>