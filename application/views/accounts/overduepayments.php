<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright;?>">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title><?php echo sitetitle;?> Overdue Payments</title>
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
        <link href="<?php echo assets_url;?>css/our.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
        		<?PHP 

$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();

foreach($q->result() as $LOGO);

?>

		<style>

		table.manglesh thead th {
                background: <?php echo $LOGO->colorcode;?>;
                color:#fff;
                font-weight:bold;
                text-align:center;
                text-transform: capitalize !important;

			}
				table.manglesh tbody td {
				    text-align:center;
                    font-size:10px;
				}
             .c11{
                background-color: #F9E4E4;
             }


		</style>

    </head>

    <body>
<!-- Navigation Bar-->
        <header id="topnav">
          <?php $this->load->view('common/nav-menu');?>
        </header>
        <!-- End Navigation Bar-->
        <?php $this->load->view('common/info-section.php');?>
        <div class="wrapper">
            <div class="container-fluid">
                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right">
						</div>
			             <h4 class="text-center">OVERDUE PAYMENTS</h4> <hr>

                        </div>

                    </div>

                </div>

                <!-- end page title end breadcrumb -->

        <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        	<table id="example" class="table table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th>SR NO.</th>
									<th>COMPANY NAME</th>
                                    <th>PO NO.</th>
                                    <th>PO DATE</th>
                                    <th>DF NO.</th>
                                    <th>PO DOWNLOAD</th>
                                    <th>TASK NAME</th>
                                    <th>TARGET DATE</th>
                                    <th>PAYMENT (%)</th>
                                    <th>AMOUNT</th>
                                    <th>MARKETING PERSON</th>
                                    <th>PAYMENT STATUS</th>
                                    <th>WORK STATUS</th>
                                   
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Footer -->

<?php $this->load->view('common/footer');?>

                <!-- End Footer -->



            </div> <!-- end container -->

        </div>

        <!-- end wrapper -->


<div id="updateprogress" class="modal fade" role="dialog">
<form id="updateprogressform" method="post" action="<?php echo page_url;?>Accounts/updatepaymentdetailfrompaymentdashboard/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>"  enctype="multipart/form-data">
<div id="pageloader1">
<img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
</div>
<div class="modal-dialog">
<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal">&times;</button>
<h4 class="modal-title" style="font-weight: bold; text-align:center;">Update Payment Receipt</h4>
</div>
<div class="modal-body">
<div class="row">

<div class="col-md-12">
<input type="hidden" id="taskkiid" value="" name="taskkiid">
<div class="form-group">
<label>Amount Received <span style="color:red" id="error_taskstatus">*</span></label>
<input type="number" class="form-control" name="amountreceived" id="amountreceived" step="any" readonly required value="">
</div>
</div>
</div>
<div class="row">
    <div class="col-md-12">
<div class="form-group">
<label>Payment Date <span style="color:red" id="error_paymentreceivedate">*</span></label>
<input type="date" class="form-control" name="paymentreceivedate" id="paymentreceivedate" required value="">
</div>
</div>
</div>

<div class="row">

<div class="col-md-12">
<div class="form-group">
<label>Remarks (If Any) <span style="color:red" id="error_taskremarks"></span></label>
<textarea name="taskremarks" id="taskremarks" class="form-control"></textarea>
</div>
</div>
</div>



<div class="row">
<div class="col-md-4"></div>
<div class="col-md-4">
<input type="submit" style="width: 100%;" name="" onclick="taskupdationvalidation();" value="Submit" class="btn btn-success">
</div>
</div>

</div>
<!-- <div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div> -->
</div>

</div>
</form>
</div>
<script type="text/javascript">
function updateyourprogressremarks(id,amount){
$("#updateprogress").modal('show');
$("#taskkiid").val(id);
$("#amountreceived").val(amount);
}
</script>
 <script>
$(document).ready(function(){
  $("#updateprogressform").on("submit", function(){
    $("#pageloader1").fadeIn();
  });//submit
});//document ready
</script>


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
<script src="<?php echo assets_url;?>pages/datatables.init.js"></script>
<script src="<?php echo assets_url;?>js/jquery.core.js"></script>
<script src="<?php echo assets_url;?>js/jquery.app.js"></script>



<script>

$(document).ready(function(){
$("#depsave").attr('disabled',false);
$("#depsave").val('submit');
$("#loginForm").on("submit", function(){
$("#depsave").attr('disabled',true);
$("#depsave").val('Please Wait...');
});//submit

});//document ready

</script>  
<script>
$( document ).ready(function() {
$('#example').dataTable({
"bProcessing": true,
fixedHeader: true,
"pagination":true,
"pageLength": 100,
dom: 'Bfrtip',
buttons: [
            {
                extend: 'excelHtml5',
                title: 'Overdue Payments'
            }
        ],

"sAjaxSource": "<?php echo page_url;?>Accounts/overduepaymentslist",

"aoColumns": [

				{ mData: 'sr_no' } ,
				{ mData: 'company_name' },
                { mData: 'pono' },
                { mData: 'podate' },
                { mData: 'df_no' },
                { mData: 'poattachment' },
                { mData: 'task_name' },
                { mData: 'workcompletiondate' },
                { mData: 'payment_percentage' },
                { mData: 'amount' },
                { mData: 'marketingperson' },
                { mData: 'message' },
                { mData: 'workstatus' },
],
"initComplete": function(settings, json) {

getcolors()
}

});   

});

$('#example').on('draw.dt', function() {
// do action here

getcolors();
});

$('#example').on('search.dt', function() {

getcolors();
});

function getcolors() {
$("#example tr").each(function() {
var currentRow = $(this);
var col1_value = currentRow.find("td:eq(11)").text();
if (col1_value == 'Payment Collection Pending') {
                    currentRow.addClass("c11");
                }



});
}
</script>

		

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<script language="javascript" type="text/javascript">   

jQuery.noConflict();

$(document).ready(function() {

$("#depsave").click(function() {

var business_loc = $("#business_loc").val();

if(business_loc=='')

{

	$("#error_business_loc").html('Required!');

}

var department_name = $("#department_name").val();

if(department_name=='')

{

	

	$("#error_department_name").html('Required!');

}



var status = $("#status").val();

if(status=='')

{

	

	$("#error_status").html('Required!');

}





if(business_loc=='' || department_name==''|| status=='' )

{

	

	return false;

}



});

});

</script>

    </body>

</html>

