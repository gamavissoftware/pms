<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>SALES TOOL COMMUNICATION</title>

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
               <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right" style="margin-top:30px">
						  <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">ADD COMMUNICATION</button>
                               
                            </div>
                           
                            <h4 class="page-title">SALES TOOL COMMUNICATION</h4>
							<div class="col-md-12"><span style="color:red;"><?php echo $this->session->flashdata('message');?></span></div>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
	<span style="color:red;"></span>
		<div class="row">
		
		  <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered manglesh" width="100%">
                                <thead>
                                <tr>
                                    <th>Sr No.</th>
									<th>TYPE</th>
									<th>PDF</th>
									<th>VIDEO</th>
									<th>MESSAGE</th>
									<th>END LINE</th>
									<th>EDIT</th>
							
									
									
                                </tr>
                                </thead>


                                <tbody>
								
                                </tbody>
                            </table>
                        
						</div>
                    </div>
              
		
		</div>


         <!-- end row -->
 <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="<?php echo page_url;?>Salestool/addcommunication">
  <div id="pageloader">
   <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
</div>
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Add Communication</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">TYPE</label>
														<span id="error_holidayname" style="color:red;"></span>
                                                       <select name="type" id="type" class="form-control" required>
													   
													   <option value="">Select Type</option>
														<?php
														$restye=$this->db->select('id,type')->from('salestoolcommunication')->where('status','1')->get();
														if($restye->num_rows()>0)
														{
														foreach($restye->result() as $restye1)
														{
														?>
														<option value="<?php echo $restye1->id;?>"><?php echo $restye1->type;?></option>
														<?php
														}
														}
														?>
									
										
													   </select>
                                                    </div>
                                                </div>
												
												 <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Include PDF</label><br/>
														<span id="error_datepicker1" style="color:red;"></span>
                                                        <input type="checkbox" id="pdf" name="pdf" value="1">
                                                    </div>
                                                </div>
												
												 <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Include Video</label><br/>
														<span id="error_datepicker1" style="color:red;"></span>
                                                        <input type="checkbox" name="video" id="video" value="1">
                                                    </div>
                                                </div>
												<div style="clear:both;height:10px;"></div>
												
												 <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Message</label>
														<span id="error_holiday_days" style="color:red;"></span>
                                                        <textarea class="form-control" name="msg" id="msg"required ></textarea>
                                                    </div>
                                                </div>
												
												
												 <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Finishing Line</label>
														<span id="error_holiday_days" style="color:red;"></span>
                                                        <textarea class="form-control" name="fmsg" id="fmsg"></textarea>
                                                    </div>
                                                </div>
												
												
												
												 <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Status</label>
														<span id="error_holiday_days" style="color:red;"></span>
                                                        <select class="form-control" name="status" id="status">
															<option value="1" selected>Active</option>
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
 fixedHeader: {
            header: true
        },
   scrollCollapse: true,
 "sAjaxSource": "<?php echo page_url;?>Salestool/commdata/",
 "aoColumns": [
						{ mData: 'sr_no' } ,
						{ mData: 'type' } ,
						{ mData: 'pdf' } ,
						{ mData: 'video' } ,
						{ mData: 'message' },
                        { mData: 'endline' },
                        { mData: 'edit' }
						
                      
						
						
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