	<!DOCTYPE html>
	<html>
	<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="<?php echo copyright; ?>">
	<link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
	<title><?php echo sitetitle; ?>Fixed Expenses</title>
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
	<h4 class="text-center" style="background-color:#fbeeee; padding:10px; 10px; 10px; 10px;">Fixed Expenses Master</h4>
	</div>
	</div>
	</div>
	<!-- end page title end breadcrumb -->
	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

	<div class="row">
	<div class="col-xs-12">
	<div class="card-box">
	<form method="post" id="loginForm" action="<?php echo page_url;?>Task/addexpense" >
	<div class="row">
	<div class="col-sm-12 col-xs-12 col-md-12">
	<?php 
		$rt=$this->db->select('id,head,cost')->from('expense_master')->get();
		if($rt->num_rows()>0){
			foreach($rt->result() as $row)
			{
	?>
	<input type="hidden" name="edit_id[]" value="<?php echo $row->id;?>">
	<div class="col-md-12">
	<div class="col-md-3">
	<div class="form-group">
	<label>Head</label> 
	<span style="color: red;" id="error_milestone">*</span>
	<input type="text" class="form-control" id="edit_head<?php echo $row->id;?>" name="edit_head<?php echo $row->id;?>" required value="<?php echo $row->head;?>">
	</div>
	</div>

	<div class="col-md-3">
	<div class="form-group">
	<label>Cost (in ₹)</label>
	<span style="color:red">*</span>
	<input type="number" class="form-control" name="edit_cost<?php echo $row->id;?>" min="1" id="edit_cost<?php echo $row->id;?>" value="<?php echo $row->cost;?>">
	</div>
	</div>

	



	<div class="col-md-1">
	<div class="form-group" style="padding-top:23px">
	<a href="<?php echo page_url;?>Task/deleteexpense/<?php echo $row->id;?>" onclick="return confirm('Are you sure you want to delete this item?');"><button type="button" class="btn btn-danger" name="add" id="addmore_btn1"><i class="fa fa-trash"></i></button></a>
	</div>

	</div>
	</div>
	<?php } }?>

<div class="col-md-12">
<div class="col-md-3">
	<div class="form-group">
	<label>Head</label> 
	<span style="color: red;" id="error_milestone">*</span>
	<input type="text" class="form-control" id="head" name="head[]">
	</div>
	</div>
	<div class="col-md-3">
	<div class="form-group">
	<label>Cost (in ₹)</label>
	<span style="color:red">*</span>
	<input type="number" class="form-control" name="cost[]" id="cost" value="">
	</div>
	</div>

	



	<div class="col-md-1">
	<div class="form-group" style="padding-top:23px">
	<button type="button" class="btn btn-warning" name="add" id="addmore_btn1"><i class="fa fa-plus"></i></button>
	</div>

	</div>
	</div>

	<div id="dynamictasks1"></div>


	<div class="col-md-12" style="padding-top:30px;"></div><hr>

	<div class="col-md-12">
	<div class="form-group pull-right">
	<input type="submit" class="btn btn-success" name="Save" id="savedata">
	</div>
	</div>
	</div>
	</form>
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

	<script language="javascript" type="text/javascript">   
	jQuery.noConflict();
	$(document).ready(function() {
	$("#savedata").click(function() {
	var existingpaymentterms = $("#existingpaymentterms").val();
	if(existingpaymentterms=='')
	{
	$("#error_existingpaymentterms").html('Required!');
	}



	if(existingpaymentterms=='')
	{

	return false;
	}

	});
	});
	</script>    
	<script type="text/javascript">
	$(document).ready(function(){
	var i=1;
	$('#addmore_btn1').click(function(){
	i++;

	$('#dynamictasks1').append('<div id="row'+i+'"><div class="col-md-12"><div class="col-md-3"><div class="form-group"><label>Head</label><span style="color: red;" id="error_milestone">*</span><input type="text" class="form-control" id="head" name="head[]"></div></div><div class="col-md-3"><div class="form-group"><label>Cost (in ₹)</label><span style="color:red">*</span><input type="text" class="form-control" name="cost[]" id="cost" value=""></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:20px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div><br/>');

	});


	$(document).on('click', '.btn_remove', function(){
	var button_id = $(this).attr("id");
	$('#row'+button_id+'').remove();
	});

	});
	</script>
	</body>
	</html>