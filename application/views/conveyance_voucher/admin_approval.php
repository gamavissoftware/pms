<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?></title>

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
		<?PHP 
$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
foreach($q->result() as $LOGO);
?>
		<style>
table.manglesh thead th {
				background: <?php echo $LOGO->colorcode;?>;
				color:#fff;
				font-weight:bold;
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
						
                           
                            <h4 class="page-title">Pending for approval Conveyance Vouchers</h4>
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
									<th>PERIOD</th>
                                    <th>USER</th>
                                    <th>TYPE</th>
                                    <th>START READING</th>
                                    <th>END READING</th>
                                    <th>PER KM RATE</th>
                                    <th>NET KM(s)</th>
									<th>PETROL USED</th>
                                    <th>MISC CHARGES</th>
                                    <th>TOTAL AMOUNT</th>
                                    <th>VEHICLE AVERAGE</th>
									<th>HOD STATUS</th>
                                    <th>VIEW</th>
								
								
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->


                 <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                        
                           
                            <h4 class="page-title">Approved Conveyance Vouchers Dashboard</h4>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example1" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th>SR NO.</th>
                                    <th>PERIOD</th>
                                    <th>USER</th>
                                    <th>TYPE</th>
                                    <th>START READING</th>
                                    <th>END READING</th>
                                    <th>PER KM RATE</th>
                                    <th>NET KM(s)</th>
                                    <th>PETROL USED</th>
                                    <th>MISC CHARGES</th>
                                    <th>TOTAL AMOUNT</th>
                                    <th>VEHICLE AVERAGE</th>
                                    <th>APPROVED AMOUNT/PETROL</th>
                                    <th>APPROVED ON</th>
                                    <th>VIEW</th>
                                
                                
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->


                <!-- Modal -->
                <div id="myModal" class="modal fade" role="dialog">
                <div class="modal-dialog">

                <!-- Modal content-->
                <form action="<?php echo page_url;?>Sales/approve_convence_data" method="post">
                    <input type="hidden" name="user_id" id="user_id" value="">
                    <input type="hidden" name="c_start" id="c_start" value="">
                    <input type="hidden" name="c_end" id="c_end" value="">
                <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Approve Conveyance</h4>
                </div>
                <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Total Amount to approve</label>
                            <input type="text" name='amount' id="amount" class="form-control" readonly>
                        </div>
                    </div>

                     <div class="col-md-4">
                        <div class="form-group">
                            <label>Approved Amouunt</label>
                            <input type="text" name='app_amount' id="app_amount" class="form-control" required>
                        </div>
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                <input type="submit" class="btn btn-success" value="Submit">
                </div>
                </div>
            </form>

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
<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
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

$('#example').dataTable({
 "bProcessing": false,
 "pagination":true,
 "sAjaxSource": "<?php echo page_url;?>Sales/approval_for_admin/",
 "aoColumns": [
						{ mData: 'sr_no' } ,
                        { mData: 'period' },
						{ mData: 'user' },
						{ mData: 'type' },
                        { mData: 'start_reading' },
                        { mData: 'end_reading' },
						{ mData: 'per_km_rate' },
						{ mData: 'net_km' },
						{ mData: 'petrol_used' },
                        { mData: 'misc_charges' },
						{ mData: 'total_amount' },
						{ mData: 'average' },
						{ mData: 'status' },
                        { mData: 'view' }

            
						
                ]
        }); 



        $('#example1').dataTable({
 "bProcessing": false,
 "pagination":true,
 "sAjaxSource": "<?php echo page_url;?>Sales/approval_for_admin_history/",
 "aoColumns": [
                        { mData: 'sr_no' } ,
                        { mData: 'period' },
                        { mData: 'user' },
                        { mData: 'type' },
                        { mData: 'start_reading' },
                        { mData: 'end_reading' },
                        { mData: 'per_km_rate' },
                        { mData: 'net_km' },
                        { mData: 'petrol_used' },
                        { mData: 'misc_charges' },
                        { mData: 'total_amount' },
                        { mData: 'average' },
                        { mData: 'approved_amt' },
                        { mData: 'status' },
                        { mData: 'view' }

            
                        
                ]
        });   
});

</script>
	<?php 
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		?>
 

<script language="javascript" type="text/javascript">   
$(document).ready(function() {
$("#save").click(function() {
	
var date = $("#date").val();
if(date=='')
{
	$("#error_date").html('Required!');
}
var from = $("#from").val();
if(from=='')
{
	$("#error_from").html('Required!');
}
var proceed_to = $("#proceed_to").val();
if(proceed_to=='')
{
	
	$("#error_proceed_to").html('Required!');
}

var mode = $("#mode").val();
if(mode=='')
{
	
	$("#error_mode").html('Required!');
}


if(date=='' || from=='' || proceed_to=='' || mode=='')
{
	
	return false;
}

});
});
</script>
 <script> $(document).ready(function() {


                $("#datepicker1").datepicker({
					orientation: 'bottom'
				});
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
					
                })

            });



 function approve(startdate,enddate,amount,type,user_id)
 {
        $("#myModal").modal('show');

        $("#amount").val(amount);
        $("#app_amount").val(amount);
        $("#c_start").val(startdate);
        $("#c_end").val(enddate);
        $("#user_id").val(user_id);

      

    
 }

        </script>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
</body>
</html>