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
						 <div class="btn-group pull-right">
						  <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">Add New Task</button>
                               
                            </div>
                           
                            <h4 class="page-title">&nbsp <?php 
							$this->db->select('audit_type_id, audit_type')->from('audit_type');
							$this->db->where('audit_type_id',$this->uri->segment(4));
							$query = $this->db->get();
							$res = $query->result();
							foreach($res as $row){
							echo $row->audit_type;
							}
							?> Audit Report</h4><hr>
							
                        </div>
                    </div>
					
	
	</div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
			<div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
						
                            <table id="example" class="table manglesh table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>Sr No.</th>
									<th>Process Name</th>
									<th>TAT</th>
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
 <form id="loginForm" method="post" action="<?php echo page_url;?>Audit_report/add_task/<?php echo $this->uri->segment(3);?>">
                                <div class="modal-dialog modal-full">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Add New Task</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Audit Type</label>
														<span id="error_audit_type" style="color:red;">*</span>
												<select class="form-control" id="audit_type" name="audit_type" readonly>
													
													<?php $this->db->select('*')->from('audit_type');
													$this->db->where('audit_type_id',$this->uri->segment(3));
													$this->db->where('status','1');
													$this->db->order_by('audit_type','asc');
													$query = $this->db->get();
													$res = $query->result();
													foreach($res as $row){?>
													<option value="<?php echo $row->audit_type_id;?>"><?php echo $row->audit_type;?></option>
													<?php }?>
													
												</select>
												
                                                    </div>
													
													<div class="form-group">
														 <label for="field-2" class="control-label">Process Objective</label>
														 <span id="error_process_objectives" style="color:red;">*</span>
														 <textarea class="form-control" name="process_objectives" id="process_objectives"></textarea>
													</div>
													
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-2" class="control-label">Section</label>
														<span id="error_audit_section" style="color:red;">*</span>
                                                        <select class="form-control" name="audit_section" id="audit_section">
														<option value="">--Select Section--</option>
														<?php 
														$query = $this->db->select('audit_section_id, audit_type, audit_section,status')->from('audit_section')->where('audit_type',$this->uri->segment(3))->where('status','1')->get();
														foreach($query->result() as $section){
														?>
														<option value="<?php echo $section->audit_section_id;?>"><?php echo $section->audit_section;?></option>
														<?php }?>
														</select>
														</div>
														<div class="form-group">
														 <label for="field-2" class="control-label">Process Steps</label>
														 <textarea class="form-control" name="process_step" id="process_step"></textarea>
													</div>
                                                </div>
												 <div class="col-md-3">
													<div class="form-group">
                                                        <label for="field-2" class="control-label">Responsible Person</label>
														<span id="error_responsible_person" style="color:red;">*</span>
                                                        <select class="form-control" name="responsible_person" id="responsible_person">
														<option value="">--Select User--</option>
														<?php 
														$query = $this->db->select('user_id, first_name, last_name,user_status')->from('system_users')->where('user_status','1')->get();
														foreach($query->result() as $users){
														?>
														<option value="<?php echo $users->user_id;?>"><?php echo $users->first_name;?> <?php echo $users->last_name;?></option>
														<?php }?>
														</select>
														</div>
												 </div>
												<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Process Name</label>
														 <span id="error_process_name" style="color:red;">*</span>
														<textarea class="form-control" name="process_name" id="process_name"></textarea>
													</div>
												</div>
												
												
												
												
												
												<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Tentative Time</label>
														 <input type="text" class="form-control" name="tentative_time" id="tentative_time" placeholder="Tentative Time" value="">
													</div>
												</div>
												
												<div class="col-md-3">
												<div class="form-group">
												<label for="field-2" class="control-label">Status</label>
												<span id="error_status" style="color:red;">*</span>
												<select class="form-control" id="status" name="status">
													<option value="">--Select Status--</option>
													<option value="1">Active</option>
													<option value="0">Inactive</option>
												</select>
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
				{ mData: 'process_name' },
				{ mData: 'tentative_time' },
				{ mData: 'added_on' },
				{ mData: 'remarks' },
				{mData:'addedby'}
				
				
				
		]
});  
 
});

</script>

    </body>
</html>