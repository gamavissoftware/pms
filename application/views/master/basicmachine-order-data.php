<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright;?>">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title><?php echo sitetitle;?> Basic Machine List</title>
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
           <link href="<?php echo assets_url; ?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
            <?PHP 
            $q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
            foreach($q->result() as $LOGO);
            ?>

		<style>
            .select2-container {
    box-sizing: border-box;
    display: inline-block;
    margin: 0;
    position: relative;
    vertical-align: middle;
    width: 100% !important;
}

		table.manglesh thead th {

				background: <?php echo $LOGO->colorcode;?>;

				color:#fff;

				font-weight:bold;

				text-align:center;

			}
				table.manglesh tbody td {
				    text-align:center;
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
			             <h4 class="text-center" style="background-color:#fbeeee; padding:10px; 10px; 0px; 10px;">Basic Machine Order List</h4>

                        </div>

                    </div>

                </div>

                <!-- end page title end breadcrumb -->

        <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
        <div class="row">
         <div class="col-sm-12">

                        <div class="card-box table-responsive">
                        	<table id="example" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th>DF No.</th>
                                    <th>Financial Year</th>
									<th>Company Name</th>
									<th>Po No</th>
									<th>Po date</th>
                                    <th>Order Value</th>
                                    <th>Marketing Person</th>
                                    <th>Po Attachment</th>
                                    <th>Brand</th>
                                    <th>Payment Terms </th>
                                    <th>Edit PO</th>
                                    <th>Canceled Order</th>

                                </tr>
                                </thead>
                            </table>

                        </div>

                    </div>

                </div>

                 

<div id="updateprogress1" class="modal fade" role="dialog">
<form id="updateprogressform" method="post" action="<?php echo page_url;?>Task/brandmapping"  enctype="multipart/form-data">
<div id="pageloader1">
<img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
</div>
<div class="modal-dialog">
<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal">&times;</button>
<h4 class="modal-title" style="font-weight: bold; text-align:center;">Assign Brand</h4>
<h5 style="text-align:center; font-weight:bold;" id="currenctlyassigned"></h5>
</div>
<div class="modal-body">
<div class="row">

<div class="col-md-12">
    <input type="hidden" id="poid" name="poid" value="">

<select class="form-control brands" name="tagbrand" id="tagbrand" required>
    <?php $q = $this->db->select('id, name')->from('company_brand')->order_by('name','asc')->get();
    foreach($q->result() as $row){?>
        <option value="<?php echo $row->id;?>"><?php echo ucwords(strtolower($row->name));?></option>
<?php }?>
</select>
</div>
</div>

<div style="height:40px; clear:both"></div>


<div class="row">
<div class="col-md-4"></div>
<div class="col-md-4">
<input type="submit" style="width: 100%;" name="" value="Submit" class="btn btn-success">
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
    function assignbrand(id){
$("#updateprogress1").modal('show');
$("#poid").val(id);
//$("#currenctlyassigned").html(currenctlyassigned);
var department = departmentid;

}
</script>

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

<script>
$(document).ready(function(){
$("#updateprogressform").on("submit", function(){
$("#pageloader1").fadeIn();
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

<script>

$(document).ready(function(){

	   $("#depsave").attr('disabled',false);

	   $("#depsave").val('submit');

  $("#loginForm").on("submit", function(){

   // $("#pageloader").fadeIn();

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
    pageLength:1000,

"pagination":true,

"sAjaxSource": "<?php echo page_url;?>Task/basicmachinedatalistinfo/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>",

"aoColumns": [

				{ mData: 'sr_no' } ,
                { mData: 'df_no' } ,
				{ mData: 'financialyear' },
                { mData: 'company_name' },
				{ mData: 'pono' },
				{ mData: 'podate' },
                { mData: 'ordervalue' },
                { mData: 'marketingperson' },
                { mData: 'po_attachment' },
                { mData: 'brandtag' },
                { mData: 'milestone' },
                { mData: 'edit' },
                { mData: 'canceledorder' }

		]

});   

});



</script>

<script>
function confirmCancel(orderId) {
    if (confirm("Are you sure you want to cancel this order?")) {
        window.location.href = "<?php echo page_url; ?>Task/canceledorder/" + orderId;
    }
}
</script>

		<script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js" type="text/javascript"></script>




<script>
$( document ).ready(function() {
$('.brands').select2({
tags:true
});
});
</script>

    </body>

</html>

