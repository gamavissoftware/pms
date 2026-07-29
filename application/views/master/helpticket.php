<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> DF Wise Help Tickets</title>

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
		<link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" >
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
<style type="text/css">
	    table.pretty5 thead th {
text-align: center;
background:#049dd4;
color:#fff;
font-size:12px;
font-weight: bold;
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
                <div class="row">
                    <div class="col-sm-12" style="margin-top:20px">
                      <a href="<?php echo page_url;?>Task/viewdfwisetickethistory"><span class="btn btn-xs btn-danger pull-right">View History</span></a>
                        <h4 class="page-title text-center" >HELP TICKET DASHBOARD</h4>
					<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>	
                    </div>
                </div>	

                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
						<table id="example" class="table pretty5 table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sr No</th>
                                        <th>DF No</th>
                                        <th>Ticket No</th>
                                        <th>Task</th>
                                        <th>Remark</th>
                                        <th>Department</th>
                                        <th>Whom Assigned</th>
                                        <th>Added On</th>
                                        <th>Added By</th>
                                        <th>Status</th>
                                        <th>Comment</th>
                                        <th>Action</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>

									
                                </tbody>
                            </table>
                        </div>
                    </div><!-- end col -->
                </div>
                <!-- end row -->

<div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
<form id="loginForm" method="post" action="<?php echo page_url;?>Master/User_management/add_department"  enctype="multipart/form-data">
 	<div class="modal-dialog">
                                	<div class="modal-content">
                                    	<div class="modal-header">
                                        	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Add Your Comment</h4>
                                        </div>
                                     <div class="modal-body">
                                        <div class="row">
                                        <div class="col-md-6">
									 	<div class="form-group">
									 	 <label for="field-2" class="control-label">Remark</label>
									 	 <span id="error_department_name" style="color:red;"></span>
									 	<textarea class="form-control" name="remarks" id="remarks" required></textarea>
									 	</div>
									 </div>
										</div>
                                        </div>
                                        <div class="modal-footer">
                                        	<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="depsave" class="btn btn-info" value="Submit">
                                        </div>
                                    </div>
                                </form>
 </div><!-- /.modal -->

                <!-- Footer -->
               <?php $this->load->view('common/footer');?>
                <!-- End Footer -->

            </div>
            <!-- end container -->


        </div>

        <div id="updateprogress" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

    <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Update Your Comments</h4>
                                        </div>
                                     <div class="modal-body">
                                        <div class="row">
                                        <div class="col-md-12">
                                            <div id="loadingMessage" style="display: none; color:red;">Please wait...</div>

                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label>Write Your Comment Here! <span style="color:red;">*</span></label>
                                                    <textarea class="form-control" name="yourremarks" id="yourremarks" required></textarea>
                                                </div>
                                            </div>
                                         
                                     </div>



                                        </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            
                                        </div>
                                    </div>
                               

                            </div><!-- /.modal -->

<script type="text/javascript">
    function showcommentbox(i){
       $("#updateprogress").modal('show');
var taskid = id;
$.ajax({
        type: "post",
        url: "<?php echo page_url;?>Task/getallcommunicationoftaskss",
        data: "taskid=" + taskid,
        success: function(data) {
            $("#fetchdynamiccommunication").html(data);
        },
        complete: function() {
            // Hide the loading message
            $("#loadingMessage").hide();
        }
    });
    }
</script>


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
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
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
"sAjaxSource": "<?php echo page_url;?>Task/viewraisedtickets/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>",
"aoColumns": [
				{ mData: 'sr_no' } ,
				{ mData: 'dfno' },
				{ mData: 'ticket_no' },
				{ mData: 'taskname' },
				{ mData: 'remarks' },
				{ mData: 'department' },
				{ mData: 'assignedto' },
				{ mData: 'addedon' },
				{ mData: 'addedby' },
				{ mData: 'closestatus' },
				{ mData: 'comment' },
				{ mData: 'addcomment' }
				
		]
});   
});

</script>
		<script>
		 // Date Picker
            jQuery('#date_of_birth').datepicker();
			jQuery('#date_of_joining').datepicker();
            jQuery('#datepicker-autoclose').datepicker({
                autoclose: true,
                todayHighlight: true
            });
		</script>

		<script type="text/javascript">
			function $("#updateprogress").modal('show');
		</script>

    </body>
</html>