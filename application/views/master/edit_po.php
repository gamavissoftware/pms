<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="<?php echo copyright; ?>">
<link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
<title><?php echo sitetitle; ?>PO Edit</title>
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
<h4 class="text-center" style="background-color:#fbeeee; padding:10px; 10px; 10px; 10px;">Edit PO</h4>
</div>
</div>
</div>
<!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
<?php 
$q = $this->db->select('*')->from('poreceived')->where('id',$this->uri->segment(3))->get();
foreach($q->result() as $row);

?>
<div class="row">
<div class="col-xs-12">
<div class="card-box">
<form method="post" id="loginForm" action="<?php echo page_url;?>Task/updatepoinfo/<?php echo $row->id;?>" enctype="multipart/form-data">
<div id="pageloader">
<img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
</div>
<div class="row">
<div class="col-sm-12 col-xs-12 col-md-12">

<div class="col-md-2">
<div class="form-group">
<label for="field-1" class="control-label">Upload PO</label>
<span id="error_attachpo" style="color:red;">*</span>
<input type="file" name="attachpo" id="attachpo" value="" class="form-control">
<input type="hidden" name="oldpo" id="oldpo" value="<?php echo $row->po_attachment;?>">
</div>
</div>


<div class="col-md-10">
<div class="form-group">
<label for="field-1" class="control-label">Payment Term 
    <!-- <i class="fa fa-plus" data-toggle="modal" data-target="#con-close-modal"></i> -->
</label>
<span id="error_existingpaymentterms" style="color:red;">*</span>
<select class="form-control" name="existingpaymentterms" id="existingpaymentterms" readonly>
<option value="">Select Payment Term</option>
<?php 
$businessloc = 2;
$this->db->select('id,payment_terms')->from('payment_terms')->where('status',1);
$this->db->where('id',$row->payment_term);
$q = $this->db->get();
foreach($q->result() as $row1){
?>
<option value="<?php echo $row1->id;?>" <?php if($row->payment_term==$row1->id){echo "selected";}?>><?php echo $row1->payment_terms;?></option>
<?php }?>
</select>
</div>
</div>

<div class="col-md-4">
<div class="form-group">
<label>Company Name</label>
<span style="color:red" id="error_companyname">*</span>
<input type="text" class="form-control" name="companyname" required id="companyname" value="<?php echo $row->company_name;?>">
</div>
</div>

<div class="col-md-4">
<div class="form-group">
<label>PO Number</label>
<span style="color:red" id="error_pono">*</span>
<input type="text" class="form-control" name="pono" id="pono required" value="<?php echo $row->pono;?>">
</div>
</div>

<div class="col-md-2">
    <div class="form-group">
        <label>Financial Year</label>
        <select class="form-control" name="financialyear" id="financialyear">
            <?php $q = $this->db->select('id, year')->from('financialyear')->order_by('id','desc')->get();
                foreach($q->result() as $r){?>
                <option value="<?php echo $r->id;?>"><?php echo $r->year;?></option>
                <?php }?>
        </select>
    </div>
</div>

<div class="col-md-2">
<div class="form-group">
<label>PO Date</label>
<span style="color:red" id="error_podate">*</span>
<input type="date" class="form-control" name="podate" id="podate" required value="<?php echo $row->podate;?>" readonly>
</div>
</div>

<div class="col-md-4">
<div class="form-group">
<label>Customer Currency</label>
<span style="color:red" id="error_currency">*</span>
<select class="form-control" name="currency" id="currency" required onchange="calculatecurrency();" readonly>
<option value="">Select Customer Currency</option> 
<option value="INR" <?php if($row->customer_currency=='INR'){echo "selected";}?>>INR</option>
<option value="USD" <?php if($row->customer_currency=='USD'){echo "selected";}?>>USD</option>
<option value="EUR" <?php if($row->customer_currency=='EUR'){echo "selected";}?>>EUR</option>
</select>
</div>
</div>



<div class="col-md-4">
	<div class="form-group">
		<label>Amount in Customer Currency</label>
		<input class="form-control" type="number" name="ordervalueincustomercurrency" id="ordervalueincustomercurrency" value="<?php echo $row->amount_in_customer_currency;?>" onkeyup="calculatecurrency();">
	</div>
</div>

<script type="text/javascript">
	function calculatecurrency() {
		//getcurrencyvalue();
    var currency = $("#currency").val();
    var basecurrency = "INR";
    var order_value = $("#ordervalueincustomercurrency").val();

    // Show the loading message
    $("#loadingMessage").show();

    $.ajax({
        type: "post",
        url: "<?php echo page_url;?>Task/googlecurrencyconvertertool",
        data: "currency=" + currency + "&basecurrency=" + basecurrency + "&order_value=" + order_value,
        success: function(data) {
            $("#order_value").val(data);
        },
        complete: function() {
            // Hide the loading message
            $("#loadingMessage").hide();
        }
    });
}


</script>
<div id="loadingMessage" style="display: none; color:red;">Please wait while calculating...</div>
<!-- <input type="text" name="currencyvalue" id="currencyvalue" value=""> -->
<div class="col-md-4">
<div class="form-group">
<label>Order Value in INR</label>
<span style="color:red" id="error_podate">*</span>
<input type="number" class="form-control" name="order_value" id="order_value" steps="any" value="<?php echo $row->order_value;?>" readonly required>
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


<div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

<form id="loginForm" method="post" action="<?php echo page_url;?>Task/directpaymenttermmaster"  enctype="multipart/form-data">
<div id="pageloader">
<img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
</div>  
<div class="modal-dialog">

<div class="modal-content">

<div class="modal-header">

<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

<h4 class="modal-title">Add New Payment Term</h4>

</div>

<div class="modal-body">

<div class="row">

<div class="col-md-12">

<div class="form-group">
<label for="field-1" class="control-label">Payment Term</label>
<span id="error_paymentterms" style="color:red;">*</span>
<input type="text" class="form-control" name="paymentterms" id="paymentterms" value="" required>
</div>

</div>

<div class="col-md-3">
<div class="form-group">
<label>Payment (%)</label>
<span style="color:red">*</span>
<input type="number" class="form-control" name="paymentpercentage[]" id="paymentpercentage" value="">

</div>
</div>

<div class="col-md-8">
<div class="form-group">
<label>Milestone</label> 
<span style="color: red;" id="error_milestone">*</span>
<select class="form-control" id="milestone" name="milestone[]">
<option value="">--Select Milestone--</option>
<?php
$businessloc = 2;
$query = $this->db->select('task_id, task_name')->from('task_management')->where('status','1')->get();
foreach($query->result() as $task){?>
<option value="<?php echo $task->task_id;?>"><?php echo $task->task_name;?></option>
<?php }?>
</select>
</div>
</div>



<div class="col-md-1">
<div class="form-group" style="padding-top:23px">
<button type="button" class="btn btn-warning" name="add" id="addmore_btn1"><i class="fa fa-plus"></i></button>
</div>

</div>

<div id="dynamictasks1"></div>
</div>

</div>

<div class="modal-footer">

<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

<input type="submit" id="posave" class="btn btn-info" value="Submit"> 

</div>

</div>



</form>

</div><!-- /.modal -->



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
var attachpo = $("#attachpo").val();
if(attachpo=='')
{	 $("#attachpo").css("border", "1px solid red");
$("#error_attachpo").html('Required!');
}

var existingpaymentterms = $("#existingpaymentterms").val();
if(existingpaymentterms=='')
{	 $("#existingpaymentterms").css("border", "1px solid red");
$("#error_existingpaymentterms").html('Required!');
}
var companyname = $("#companyname").val();
if(companyname=='')
{
$("#companyname").css("border", "1px solid red");
$("#error_companyname").html('Required!');
}

var pono = $("#pono").val();
if(pono=='')
{
$("#pono").css("border", "1px solid red");
$("#error_pono").html('Required!');
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






if(attachpo=='' || paymentterms=='' || companyname=='' || pono=='' || podate=='' || currency=='')
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
$('#dynamictasks1').append('<div id="row'+i+'"><div class="col-md-3"><div class="form-group"><label>Payment (%)</label><span style="color:red">*</span><input type="text" class="form-control" name="paymentpercentage[]" id="paymentpercentage" value=""><input type="hidden" name="recordid[]" value=""></div></div><div class="col-md-8"><div class="form-group"><label>Milestone</label><span style="color: red;" id="error_milestone">*</span><select class="form-control" id="milestone" name="milestone[]"><option value="">--Select Milestone--</option><?php $businessloc = 2; $query = $this->db->select('task_id, task_name')->from('task_management')->where('status','1')->get(); foreach($query->result() as $task){?><option value="<?php echo $task->task_id;?>"><?php echo $task->task_name;?></option><?php }?></select></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:0px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div><br/>');

});


$(document).on('click', '.btn_remove', function(){
var button_id = $(this).attr("id");
$('#row'+button_id+'').remove();
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