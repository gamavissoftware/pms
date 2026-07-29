<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Add Reading</title>

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
						  <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">Add Reading</button>
                               
                            </div>
                           
                            <h4 class="page-title"><?php 
											$query = $this->db->select('office_id, company_name')->from('offices')->where('office_id',$this->uri->segment(3))->get();
											foreach($query->result() as $row){
												echo $row->company_name;
											}
											?> Power Factor</h4>
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
									<th>Date</th>
									<th>Morning Reading (KWH)</th>
                                    <th>Morning Reading (KVAH)</th>
                                    <th>Evening Reading (KWH)</th>
                                    <th>Evening Reading (KVAH)</th>
                                    <th>Total Unit Consumed In Morning (KWH)</th>
                                    <th>Total Unit Consumed In Evening (KWH)</th>
                                    <th>Total Unit Consumed In Morning(KVAH)</th>
                                    <th>Total Unit Consumed In Evening(KVAH)</th>
                                    <th>Remarks</th>
                                    <th>Added On</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
 <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="<?php echo page_url;?>Power_factor/add_reading/<?php echo $this->uri->segment(3);?>">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Add Reading of <?php 
											$query = $this->db->select('office_id, company_name')->from('offices')->where('office_id',$this->uri->segment(3))->get();
											foreach($query->result() as $row){
												echo $row->company_name;
											}
											?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                               <div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Date </label>
														 <span id="error_reading_date" style="color:red;">*</span>
														 <input type="date" class="form-control" name="reading_date" id="reading_date" placeholder="Date" value="<?php echo date('Y-m-d');?>">
													</div>
												</div>
												<div class="col-md-9">
													<div class="row">
													<div class="col-md-6">
													<div class="form-group">
														 <center><label class="control-label">Morning Reading</label></center>
														 <div class="row">
															<div class="col-md-6">
																 <span id="error_company" style="color:red;"></span>
														 <input type="text" class="form-control" name="morning_kwh" id="morning_kwh" placeholder="KWH" value="">
															</div>
															<div class="col-md-6">
																<span id="error_company" style="color:red;"></span>
														 <input type="text" class="form-control" name="morning_kvah" id="morning_kvah" placeholder="KVAH" value="">
															</div>
														 </div>
														 
														
													</div>
												</div>
												
												<div class="col-md-6">
													<div class="form-group">
														 <center><label class="control-label">Evening Reading</label></center>
														 <div class="row">
															<div class="col-md-6">
																 <span id="error_company" style="color:red;"></span>
														 <input type="text" class="form-control" name="evening_kwh" id="evening_kwh" placeholder="KWH" value="">
															</div>
															<div class="col-md-6">
																<span id="error_company" style="color:red;"></span>
														 <input type="text" class="form-control" name="evening_kvah" id="evening_kvah" placeholder="KVAH" value="">
															</div>
														 </div>
														 
														
													</div>
												</div>
													</div>
												</div>
												
												
												<div class="col-md-12">
												<div class="form-group">
														 <label for="field-2" class="control-label">Remark </label>
														 <Span id="address_error" style="color:red;"></span>
														 <textarea class="form-control" name="remarks" id="remarks" placeholder="Remarks" >
														 
														 </textarea>
														 
													</div>
												</div>
												
											
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
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
"bProcessing": true,
"pagination":true,
"sAjaxSource": "<?php echo page_url;?>Power_factor/reading_report/<?php echo $this->uri->segment(3);?>",
"aoColumns": [
				{ mData: 'sr_no' } ,
				{ mData: 'reading_date' },
				{ mData: 'morning_kwh' },
				{ mData: 'morning_kvah' },
				{ mData: 'evening_kwh' },
				{ mData: 'evening_kvah' },
				{ mData: 'unit_consumed_morning_kwh' },
				{ mData: 'unit_consumed_morning_kvah' },
				{ mData: 'unit_consumed_evening_kwh' },
				{ mData: 'unit_consumed_evening_kvah' },
				{ mData: 'remarks' },
				{ mData: 'added_time' },
				{ mData: 'edit' }
				
		]
});   
});

</script>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script language="javascript" type="text/javascript">   
jQuery.noConflict();
$(document).ready(function() {
$("#save").click(function() {
var reading_date = $("#reading_date").val();
if(reading_date=='')
{
	$("#error_reading_date").html('Required!');
}
if(reading_date=='')
{
	
	return false;
}

});
});
</script>
    </body>
</html>