<?php
$CI =& get_instance();
$CI->load->model('Store_model');
$jobcard=$CI->Store_model->jobcardprvspo('1');
$indent=$CI->Store_model->jobcardprvspo('2');
$ims=$CI->Store_model->jobcardprvspo('3');
$total=$CI->Store_model->jobcardprvspo(0);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>PR VS PO REPORT</title>

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

                      <div class="col-sm-4">
                        <div class="page-title-box">
                        
                           
                            <h4 class="page-title">PR VS PO REPORT (JOBCARD)- <?php echo round($jobcard,0);?></h4>
                        </div>
                    </div>

                <div class="col-sm-4 card-box">

                <table class="table table-bordered">
                <thead>
                <tr>
                <th colspan="6" style="text-align:center;">TOTAL PENDING PR VS PO</th>


                </tr>

                </thead>
                <tbody>
                <?php       
                $total=round($jobcard,0)+round($indent,0)+round($ims,0);
                ?>
                <tr>
                <td colspan="2" class="text-center" style="font-weight:bold;">JOBCARD - <?php echo round($jobcard,0);?></td>
                <td colspan="2" class="text-center" style="font-weight:bold;">INDENT - <?php echo round($indent,0);?></td>
                <td colspan="2" class="text-center" style="font-weight:bold;">IMS - <?php echo round($ims,0);?></td>

                </tr>
                </tbody>
                </table>

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
									<th>PR No.</th>
									<th>Source</th>
									<th>Indenter Ref</th>
									<th>Item</th>
									<th>Finsys Code</th>
                                    <th>Specification</th>
                                    <th>Quantity</th>
                                     <th>Pending Since</th>
                                    
                                </tr>
                                </thead>


                                <tbody>
								
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>



                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                        
                           
                            <h4 class="page-title">PR VS PO REPORT (INDENT) - <?php echo round($indent,0);?></h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
    <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
        <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example2" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th>PR No.</th>
                                    <th>Source</th>
                                    <th>Indenter Ref</th>
                                    <th>Item</th>
                                    <th>Finsys Code</th>
                                    <th>Specification</th>
                                    <th>Quantity</th>
                                     <th>Pending Since</th>
                                </tr>
                                </thead>


                                <tbody>
                                
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>





                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                        
                           
                            <h4 class="page-title">PR VS PO REPORT (IMS) - <?php echo round($ims,0);?></h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
    <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
        <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example3" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th>PR No.</th>
                                    <th>Source</th>
                                    <th>Indenter Ref</th>
                                    <th>Item</th>
                                    <th>Finsys Code</th>
                                    <th>Specification</th>
                                    <th>Quantity</th>
                                     <th>Pending Since</th>
                                </tr>
                                </thead>


                                <tbody>
                                
                                </tbody>
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
		

	 
 <script>
$( document ).ready(function() {
 
$('#example').dataTable({
 "bProcessing": false,
 "pagination":true,
fixedHeader: true,
   scrollCollapse: true,
   pageLength:100,
   fixedColumns:   {
            leftColumns: 3
        },
 "sAjaxSource": "<?php echo page_url;?>Reporting/pr_vs_po_list/1",
 "aoColumns": [
					{ mData: 'sr_no' },
					{ mData: 'pr_no'},
					{ mData: 'source'},
					{ mData: 'indenter_ref'},
					{ mData: 'item'},
					{ mData: 'fincode'},
                    { mData: 'specification'},
                    { mData: 'qty'},
                    { mData: 'pendsince'}
						
						
                ]
        });   
});


</script>



<script>
$( document ).ready(function() {
 
$('#example2').dataTable({
 "bProcessing": false,
 "pagination":true,
fixedHeader: true,
   scrollCollapse: true,
   pageLength:100,
   fixedColumns:   {
            leftColumns: 3
        },
 "sAjaxSource": "<?php echo page_url;?>Reporting/pr_vs_po_list/2",
 "aoColumns": [
                    { mData: 'sr_no' },
                    { mData: 'pr_no'},
                    { mData: 'source'},
                    { mData: 'indenter_ref'},
                    { mData: 'item'},
                    { mData: 'fincode'},
                    { mData: 'specification'},
                    { mData: 'qty'},
                    { mData: 'pendsince'}
                        
                        
                ]
        });   
});


</script>




<script>
$( document ).ready(function() {
 
$('#example3').dataTable({
 "bProcessing": false,
 "pagination":true,
fixedHeader: true,
   scrollCollapse: true,
   pageLength:100,
   fixedColumns:   {
            leftColumns: 3
        },
 "sAjaxSource": "<?php echo page_url;?>Reporting/pr_vs_po_list/3",
 "aoColumns": [
                    { mData: 'sr_no' },
                    { mData: 'pr_no'},
                    { mData: 'source'},
                    { mData: 'indenter_ref'},
                    { mData: 'item'},
                    { mData: 'fincode'},
                    { mData: 'specification'},
                    { mData: 'qty'},
                     { mData: 'pendsince'}
                        
                        
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
</script>
</body>
</html>
