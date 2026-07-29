<?php
$orderflow=$this->uri->segment(3);
if($orderflow=='')
{
	echo "Invalid Access to this page";exit;
}
$restyu=$this->db->select('fms_flow')->from('fms_flow')->where('flow_id',$orderflow)->get();
foreach($restyu->result() as $restyiou);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>ORDER STAGE-<?php echo $restyiou->fms_flow;?></title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
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
						 
                           
                            <h4 class="page-title"><span style="color:red;"><?php echo $restyiou->fms_flow;?></span> STAGE ORDERS ASSIGNED TO YOU</h4>
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
                                    <th>SR NO.</th>
									<th>TIMESTAMP</th>
									<th>MACHINE NAME</th>
                                    <th>I/O & JOB CARD</th>
                                    <th>FACTORY</th>
                                    <th>PLANNED TIME</th>
									<th>ACTUAL TIME</th>
									<th>TOTAL DAYS</th>
									<th>IN STOCK</th>
									<th>PO NUMBER</th>
									<th>INSTRUMENT NAME</th>
									<th>ACTION</th>
									
                                </tr>
                                </thead>


                                <tbody>
								
                                </tbody>
                            </table>
                        </div>
                    </div>
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
<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
		
	
	
 <script>
$( document ).ready(function() {

$('#example').dataTable({
 "bProcessing": false,
 "pagination":true,
 "sAjaxSource": "<?php echo page_url;?>Orderstage/machining_process3/<?php echo $orderflow;?>",
 "aoColumns": [
						{ mData: 'sr_no' } ,
						{ mData: 'orderdate'},
                        { mData: 'mname' },
                        { mData: 'jobcard' },
                        { mData: 'factory' },
			 { mData: 'tat' },
						{ mData: 'actual' },
						{ mData: 'totdays' },
	 { mData: 'stockinfo' },
	 { mData: 'prno' },
	 { mData: 'material' },
						{ mData: 'action' }
						
                ]
        });   
});

</script>


<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready

function markstagedone(idd,flowstage,orderid,jobcardid)
{
	if(confirm('Confirm you want to mark this stage as done?'))
	{
		document.location="<?php echo page_url;?>Orderstage/markdone/"+idd+"/"+flowstage+"/"+orderid+"/"+jobcardid;
		return true;
		
	}else
	{
		return false;
	}
}
	function getstockdetail(stageid)
{
var stock=$("#stock"+stageid).val();
if(stock==0)
{
$("#prreq"+stageid).css('display','');
$("#prno"+stageid).attr('required',true);
$("#mtname"+stageid).attr('required',true);

}else{

$("#prreq"+stageid).css('display','none');
$("#prno"+stageid).attr('required',false);
$("#mtname"+stageid).attr('required',false);

}


}
</script>
</body>
</html>