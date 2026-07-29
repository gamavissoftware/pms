<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Prestogroup Delegated Calls Dashboard</title>

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
				font-weight:bold;
				font-size:10px;
			}
			table.taskdetail thead th {
				background: yellow;
				color:#000;
				font-weight:bold;
				font-size:12px;
			}
			
			.backgroundcolor{
				background-color:yellow !important; 
				color:#000;
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
            <div class="desc-box">
                <div class="row">
                    <div class="col-sm-2">
                        <img src="<?php echo dashboard_icon; ?>call delegation.png" style="    width: 100%;">
                    </div>
                    <div class="col-sm-8">
                        <h6>YOUR DELEGATION CALLS DASHBOARD
</h6>
                        <p class="pagedescriptionfont">The users can view the list and status of delegated calls that have been assigned to them.


</p>
                    </div>
                    <div class="col-sm-2">
                        <!-- <div class="text-center"><a href="#">
                             
                                <img src="<?php echo dashboard_icon; ?>header_icon.png" style="width: 40%; margin-top: 50px;">
                            </a>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
        <div class="wrapper">
            <div class="container-fluid">

                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right" style="margin-top:30px">
						 </div>
                           
                            <!-- <h4 class="page-title text-center">YOUR DELEGATION CALLS DASHBOARD</h4> -->
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
	<?php echo $this->session->flashdata('message'); ?>
		<div class="row">
		  
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th>SR NO.</th>
									<th>TIMESTAMP</th>
									<th>DELEGATED BY</th>
                                    <th>DELEGATED TO</th>
                                    <th>CUSTOMER NAME</th>
									<th>CONTACT NUMBER</th>
                                    <th>EMAIL</th>
									<th>MESSAGE</th>
									<th style="width:30%">UPDATE STATUS</th>
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
		// Time Picker
            jQuery('#timepicker').timepicker({
                defaultTIme : false
            });
			jQuery('#timepicker4').timepicker({
                defaultTIme : false
            });
            jQuery('#timepicker2').timepicker({
                showMeridian : false
            });
            jQuery('#timepicker3').timepicker({
                minuteStep : 15
            });
		</script>
 <script>
$( document ).ready(function() {
$('.select2').select2({ });
$('.select3').select2({ });
$('.select4').select2({ });
$('#example').dataTable({
 "bProcessing": false,
	fixedHeader: true,
 "pagination":true,
"pageLength": 100,
 "sAjaxSource": "<?php echo page_url;?>Delegation/delegated_calls_list/",
 "aoColumns": [
						{ mData: 'sr_no' } ,
                        { mData: 'timestamp' },
                        { mData: 'delegated_by' },
						{ mData: 'delegated_to' },
					    { mData: 'customer_name' },
	                    {mData:'contact_number'},
						{ mData: 'email' },
						{ mData: 'message' },
						{ mData: 'status' }
						
                ],"initComplete": function(settings, json) {
    
    getcolor();
  }
        });   
		
		

});


function getcolor()
{
$("#example tr").each(function(){
	var todaydate = "<?php echo date('d-M-Y');?>";
	
	var currentRow=$(this);
	     var col1_value=currentRow.find("td:eq(6)").text();
        var col2_value=currentRow.find("td:eq(7)").text();
        var col3_value=currentRow.find("td:eq(8)").text();
        var col4_value=currentRow.find("td:eq(9)").text();
		if(col1_value==todaydate || col2_value==todaydate || col3_value==todaydate || col4_value==todaydate)
		{
			
			currentRow.find("td:eq(7)").addClass("backgroundcolor");
			currentRow.find("td:eq(8)").addClass("backgroundcolor");
			currentRow.find("td:eq(9)").addClass("backgroundcolor");
			//currentRow.addClass("backgroundcolor");
		}
	
});
}
</script>


<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
</body>
</html>