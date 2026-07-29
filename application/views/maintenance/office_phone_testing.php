<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Raise Ticket</title>
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
<?PHP 
$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
foreach($q->result() as $LOGO);
?>

<style>

table.display thead th {

background: <?php echo $LOGO->colorcode;?>;

color:#fff;

font-weight:bold;

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


        <div class="wrapper">
            <div class="container-fluid">

                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right">
						  <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">Add New Report</button>
                           </div>
                           
                            <h4 class="page-title">Office Phone Testing Report</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
<div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                             <table id="example" class="table table-striped table-bordered display">
                                <thead>
                                <tr>
                                     <th>Sr No.</th>
                                    <th>Date</th>
                                    <th>Phone / Area</th>
                                    <th>Report</th>
                                    <th>Message</th>
                                    <th>Action</th>
                                    
                                </tr>
                                </thead>


                               <tfoot>
								<tr>
									 <th>Sr No.</th>
                                    <th>Date</th>
                                    <th>Phone / Area</th>
                                    <th>Report</th>
                                    <th>Message</th>
                                    <th>Action</th>
                                    

								</tr>
						</tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
 <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="<?php echo page_url;?>Office_maintenance/office_phone_testing" enctype="multipart/form-data">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Office Phone Testing</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                             
											<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Landling Phone (Area)<span style="color:red;">*</span></label>
														<span id="error_phone_area" style="color:red;"></span>
														<input type="text" class="form-control" name="phone_area" id="phone_area" placeholder="Phone Area">
												</div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="field-2" class="control-label"> Test Report <span style="color:red;">*</span></label>
														<span id="error_testrpt" style="color:red;"></span>
														<select class="form-control" name="testrpt" id="testrpt">
														<option value="">--Select Option--</option>
															<option value="Working">Working</option>
															<option value="Not Working">Not Working</option>
														</select>
                                                    </div>
                                                </div>
												
												 <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-2" class="control-label">Report<span style="color:red;">*</span></label>
														<span id="error_report" style="color:red;"></span>
														<textarea class="form-control" name="report" id="report" placeholder="Report"></textarea>
														
                                                    </div>
                                                </div>
												
                                            </div>
											
											
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="phoneareabtn" class="btn btn-info" value="Submit"> 
                                        </div>
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

        <script>
$( document ).ready(function() {
$('#example').dataTable({
 "bProcessing": false,
 "pagination":true,
 "sAjaxSource": "<?php echo page_url;?>Office_maintenance/test_Report_list",
 "aoColumns": [
						{ mData: 'sr_no' } ,
                        { mData: 'added_on' },
						{ mData: 'phone_area' },
						{ mData: 'testing_report' },
						{ mData: 'message' },
						{ mData: 'edit' }
						
                ]
        });   
});

</script>
<script language="javascript" type="text/javascript">   
$(document).ready(function() {
// phone area validation 
$("#phoneareabtn").click(function() {
var phone_area = $("#phone_area").val();
if(phone_area=='')
{
	$("#error_phone_area").html('Required!');
}
var testrpt = $("#testrpt").val();
if(testrpt=='')
{
	
	$("#error_testrpt").html('Required!');
}

var report= $("#report").val();
if(report=='')
{
	
	$("#error_report").html('Required!');
}

if(phone_area=='' || testrpt=='' || report=='')
{
	
	return false;
}

});
});
</script>
  <script>
  $( function() {
    $( "#datecalender" ).datepicker();
	$( "#todaydate" ).datepicker();
	$( "#bill_payment_Date" ).datepicker();
	$( "#purchase_date" ).datepicker();
  } );
  </script>
 <script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
    </body>
</html>