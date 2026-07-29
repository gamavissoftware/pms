<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title>FILTER BY VENDOR</title>
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
		
		<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" rel="stylesheet"/>
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
                            <h4 class="page-title">FILTER BY VENDOR</h4>
                        </div>
                    </div>
                </div>
		  <form method="post" action="<?php echo page_url;?>Reporting/vendorwise_purchase_report"> 
		  <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box" style="padding-bottom:100px">
                           <div class="col-md-3">
                               <div class="form-group">
                                   <label>Vendor</label>
                                   <select class="form-control" name="vendor" id="vendor" required="required">
                                       <option value="">--Select Vendor--</option>
                                       <?php 
                                       $q = $this->db->select('a.vendor, b.id, b.name')->from('purchase_order a')->join('vendors b','a.vendor=b.id','left')->where('b.status','1')->group_by('a.vendor')->get();
                                       foreach($q->result() as $row){
                                       ?>
                                       <option value="<?php echo $row->id;?>"><?php echo $row->name;?></option>
                                       <?php }?>
                                   </select>
                               </div>
                           </div>
                           <div class="col-md-3">
                               <div class="form-group">
                                   <label>Start Date</label>
                                   <input type="text" id="datepicker1" class="form-control datepicker1" name="startdate" value="" required="required">
                               </div>
                           </div>
                           
                            <div class="col-md-3">
                               <div class="form-group">
                                   <label>End Date</label>
                                   <input type="text" id="datepicker2" class="form-control datepicker" name="enddate" value="" required="required">
                               </div>
                           </div>
                           <div class="col-md-3">
                               <div class="form-group" style="padding-top:20px">
                                   <input type="submit" class="btn btn-success " value="SEARCH">
                               </div>
                           </div>
                        </div>
                    </div>
                </div>
                </form>
                
                 <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example1" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                   <th>Sr No.</th>
                                   	<th>VENDOR NAME</th>
                                   	<th>ADDRESS</th>
                                   	<th>PHONE</th>
									<th>TOTAL VALUE</th>
								
                                </tr>
                                </thead>


                                <tbody>
                                    <?php 
                                    
                                    $i=1;
                                    $q = $this->db->select('a.price,a.vendor, b.name, b.address, b.phone')->from('purchase_order a')->join('vendors b','a.vendor=b.id','left')->group_by('a.vendor')->get();
                                    foreach($q->result() as $row){
                                    ?>
                                    <tr>
                                        <td><?php echo $i;?></td>
                                        <td><?php echo $row->name;?></td>
                                        <td><?php echo $row->address;?></td>
                                        <td><?php echo $row->phone;?></td>
                                        <td><strong style="color:red;"><?php 
                                            $q= $this->db->select_sum('price')->select('price')->from('purchase_order')->where('vendor',$row->vendor)->get();
                                            foreach($q->result() as $total);
                                            echo $total->price;
                                            
                                        ?></strong></td>
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

       
<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <!-- Datatable init js -->
        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
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
   
        
});


</script>
<script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready

</script>
<script> $(document).ready(function() {
 var date = new Date();
date.setDate(date.getDate());
   $('.datepicker').datepicker({
       orientation: 'bottom',
    todayHighlight:true
    });
                $("#datepicker1").datepicker({
					placement: 'bottom',
					todayHighlight:true
				});
				$("#datepicker2").datepicker({
					placement: 'bottom',
					todayHighlight:true
					
				});
				
                

            });
</script>
</body>
</html>