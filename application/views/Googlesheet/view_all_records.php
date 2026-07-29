<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Prestogroup Google Sheet</title>
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
<style>
		table.manglesh thead th {
				background: #003366;
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


        <div class="wrapper">
            <div class="container-fluid">

                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						
						
							 <div class="btn-group pull-right">
						      	 <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">Add Google form /sheet link </button>&nbsp; &nbsp;
                            </div>
				
					
                           
                            <h4 class="page-title"> Google form /sheet link</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
<div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                             <table id="example" class="table manglesh table-striped table-bordered display">
                                <thead>
                                <tr>
                                     <th>Sr No.</th>
                                     <th>Business Location</th>
                                     <th>Department Name</th>
                                     <th>Title</th>
                                     <?php $user_role =$this->session->userdata['logged_in']['role'];
                                     if($user_role=='1'){
                                     ?>
                                     <th>Google Sheet Link</th>
                                     <?php }?>
                                    <th>Added By</th>
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
 <form id="loginForm" method="post" action="<?php echo page_url;?>Master/User_management/add_google_sheet_urls/<?php echo $this->uri->segment(4);?>" enctype="multipart/form-data">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Add Google Sheet Link</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                
												<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Title<span style="color:red;"></span></label>
														 <span id="error_title" style="color:red;"></span>
														 <input type="text" class="form-control" name="title[]" id="title" placeholder="" value="">
														
													</div>
												</div>
												
											
												<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">Google Form URL<span style="color:red;">*</span></label>
														 <span id="error_url" style="color:red;"></span>
														 <textarea class="form-control" name="url[]" id="url"></textarea>
														
													</div>
												</div>
													<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">Google Sheet URL<span style="color:red;">*</span></label>
														 <span id="error_url" style="color:red;"></span>
														 <textarea class="form-control" name="sheeturl[]" id="sheeturl"></textarea>
														
													</div>
												</div>
												<div class="col-md-1">
													<div class="form-group" style="margin-top:25px">
														<button type="button" class="btn btn-warning" name="add" id="addmore_btn"><i class="fa fa-plus"></i></button>
													</div>
												</div>
												
                                            </div>
											<div class="row">
												<div id="dynamictasks">
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
	fixedHeader: true,
"pagination":true,
"sAjaxSource": "<?php echo page_url;?>Master/User_management/google_sheet_lists",
"aoColumns": [
				{ mData: 'sr_no' } ,
				{ mData: 'business_loc' },
				{ mData: 'department' },
				{ mData: 'title' },
				<?php $user_role =$this->session->userdata['logged_in']['role'];
                                     if($user_role=='1'){
                                     ?>
				{ mData: 'sheetlink' },
				<?php }?>
				{ mData: 'added_by' },
				{ mData: 'added_on' },
				{ mData: 'edit' }
				
				
		]
});   
});

</script>

<script type="text/javascript">
		 $(document).ready(function(){
	var i=1;
 $('#addmore_btn').click(function(){

 i++;
 $('#dynamictasks').append('<div id="row'+i+'" class="row"><div class="col-md-3"><div class="form-group"><label for="field-2" class="control-label">Title</label><span id="error_remarks" style="color:red;"></span><input type="text" class="form-control" name="title[]" id="title" value=""></div></div><div class="col-md-4"><div class="form-group"><label for="field-1" class="control-label">Google Form URL<span style="color:red;">*</span></label><span id="error_url" style="color:red;"></span><textarea class="form-control"  name="url[]" id="url"></textarea></div></div><div class="col-md-4"><div class="form-group"><label for="field-1" class="control-label">Google Sheet Link<span style="color:red;">*</span></label><span id="error_url" style="color:red;"></span><textarea class="form-control"  name="sheeturl[]" id="sheeturl"></textarea></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:30px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div><br/>');
 
 });
 
 
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id+'').remove();
 });
 
});
	  </script>

 <script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
    </body>
</html>