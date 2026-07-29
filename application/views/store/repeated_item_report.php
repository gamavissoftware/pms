<?php
$CI =& get_instance();
$CI->load->model('Store_model');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title>REPEATED ITEMS REPORT</title>
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
 <body>

<!-- Navigation Bar-->
                <header id="topnav">
          <?php $this->load->view('common/nav-menu');?>
        </header>
        <!-- End Navigation Bar-->

<div class="wrapper">
            <div class="container-fluid">

                <!-- Page-Title -->
              
                <!-- end page title end breadcrumb -->
	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
		<!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <h4 class="page-title">REPEATED ITEMS ORDER REPORT</h4>
                        </div>
                    </div>
                </div>
		  
		  <div class="row">
                    <div class="col-sm-6">
                        <div class="card-box table-responsive">
                            <h4 class="text-center">MACHINE ITEMS</h4>
                            <table id="example1" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                   <th>Sr No.</th>
                                   <th>ITEM NAME</th>
									<th>TOTAL PURCHASE FREQUENCY</th>
								
                                </tr>
                                </thead>


                                <tbody>
                                    <?php 
                                    $i=1;
                                    $today = date('Y-m-d');
                                    $time = strtotime($today);
                                    $final = date("Y-m-d", strtotime("-1 month", $time));
                                   $todaysdate = $today." 23:59:59";
                                   $onemonthpast = $final." 00:00:00";
                                    $query = $this->db->select('a.itemid, b.part')->from('purchase_order a')->join('machine_parts_with_picture b','a.itemid=b.id','left')->where('a.potype','0')->where('a.addedOn BETWEEN "'.$onemonthpast. '" and "'.$todaysdate.'"')->group_by('a.itemid')->get();
                                        foreach($query->result() as $row){
                                            
                                        
                                    ?>
								<tr>
								    <td><?php echo $i;?></td>
								    <td><?php echo $row->part;?></td>
								    <td><?php 
								    $q = $this->db->select('id')->from('purchase_order')->where('itemid',$row->itemid)->where('potype','0')->get();
								    echo count($q->result());
								    ?></td>
								</tr>
								<?php $i++;}?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="col-sm-6">
                        <div class="card-box table-responsive">
                            <h4 class="text-center">GENERAL ITEMS</h4>
                            <table id="example2" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                   <th>Sr No.</th>
                                   <th>ITEM NAME</th>
									<th>TOTAL PURCHASE FREQUENCY</th>
								
                                </tr>
                                </thead>


                                 <tbody>
                                    <?php 
                                    $i=1;
                                    $query = $this->db->select('a.itemid, b.item_name')->from('purchase_order a')->join('house_keeping_items b','a.itemid=b.id','left')->where('a.potype','1')->where('a.addedOn BETWEEN "'.$onemonthpast. '" and "'.$todaysdate.'"')->group_by('a.itemid')->get();
                                        foreach($query->result() as $row){
                                            
                                        
                                    ?>
								<tr>
								    <td><?php echo $i;?></td>
								    <td><?php echo $row->item_name;?></td>
								    <td><?php 
								    $q = $this->db->select('id')->from('purchase_order')->where('itemid',$row->itemid)->where('potype','1')->get();
								    echo count($q->result());
								    ?></td>
								</tr>
								<?php $i++;}?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        
        
        
        <!-- Modal -->

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
 
$('#example123445').dataTable({"bSort" : false});
 
        
$('#example1').dataTable({
 "bProcessing": false,
 "pagination":true,
fixedHeader: true,
 scrollCollapse: true,
 fixedColumns:   {
        leftColumns: 3
        }

});     
   
   $('#example2').dataTable({
 "bProcessing": false,
 "pagination":true,
fixedHeader: true,
 scrollCollapse: true,
 fixedColumns:   {
        leftColumns: 3
        }

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

function approvepo(pono)
{
	
		document.location="<?php echo page_url;?>Store/approvepo/"+pono;
		return true;

}

function showpopup(pono)
{
    
    $("#myModal").modal('show');
    $("#ponum").text(pono);
     $("#pono").val(pono);
    
    
}



</script>

</body>
</html>