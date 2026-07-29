<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Audit Reporting </title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 
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
			}
		</style>
    </head>


    <body>


        <!-- Navigation Bar-->
        <header id="topnav">
          <?php $this->load->view('common/nav-menu');?>
        </header>
        <!-- End Navigation Bar-->


        <div class="wrapper" style="background-color:#fff;">
            <div class="container-fluid">

                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
                        <div class="col-md-8"></div>
                          <div class="col-md-4">
                    <div class="row">
                    <div class="col-md-4">  <div class="btn-group pull-right">
						 <a href="<?php echo page_url;?>Audit_report/View_complete_report_by_status/<?php echo $this->uri->segment(3);?>/1"> <button class="btn btn-success waves-effect waves-light">Task Done</button></a>
                         </div></div>
                    <div class="col-md-4">   <div class="btn-group pull-right">
                           <a href="<?php echo page_url;?>Audit_report/View_complete_report_by_status/<?php echo $this->uri->segment(3);?>/0"><button class="btn btn-danger waves-effect waves-light">Task Not Done</button></a>
                            </div></div>
                    <div class="col-md-4">	 <div class="btn-group pull-right">
						  <button class="btn btn-warning waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">Fiter by Date</button>
                               
                            </div></div>
                          
                          
                          </div>
                          
                          </div>
                      
                     
                           
                            <h4 class="page-title">&nbsp <span style="color:red; font-weight:bold;"><?php 
							$this->db->select('audit_type_id, audit_type')->from('audit_type');
							$this->db->where('audit_type_id',$this->uri->segment(3));
							$query = $this->db->get();
							$res = $query->result();
							foreach($res as $row){
							echo $row->audit_type;
							}
							?></span> Audit Report</h4><hr>
							
                        </div>
                    </div>
					
	
	</div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
			<div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
						
                            <table id="example" class="table manglesh table-striped table-bordered pretty">
                                <thead>
                                <tr>
                                    <th>Sr No.</th>
									<th>Audit Section</th>
									<th>Process Name</th>
									<th>TAT</th>
									<th>Scheduled TAT</th>
                                    <th>Added On</th>
									<th>Remarks</th>
									<th>Added By</th>
                                    
                                </tr>
                                </thead>
									
                            </table>
						
                        </div>
                    </div>
                </div>
                <!-- end row -->
				<div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="<?php echo page_url;?>Audit_report/view_data/<?php echo $this->uri->segment(3);?>">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Filter Data by Date</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                               <div class="col-md-6">
													<div class="form-group">
														 <label for="field-2" class="control-label">Start Date</label>
														 <span id="error_process_name" style="color:red;"></span>
														<input class="form-control" type="date" name="from_date" value="<?php echo date('Y-m-d');?>">
													</div>
												</div>
												
												
												<div class="col-md-6">
													<div class="form-group">
														 <label for="field-2" class="control-label">End Date</label>
														 <span id="error_process_name" style="color:red;"></span>
														<input class="form-control" type="date" name="to_date" value="<?php echo date('Y-m-d');?>">
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
"sAjaxSource": "<?php echo page_url;?>Audit_report/complete_audit_report_view/<?php echo $this->uri->segment(3);?>",
"aoColumns": [
				{ mData: 'sr_no' } ,
				{ mData: 'audit_section' },
				{ mData: 'process_name' },
				{ mData: 'tentative_time' },
				{ mData: 'scheduledtime' },
				{ mData: 'added_on' },
				{ mData: 'remarks' },
				{mData:'addedby'}
				
				
				
		]
});  
 
});

</script>

    </body>
</html>