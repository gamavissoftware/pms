<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>SERVICE REQUEST RAISED</title>

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
                <div class="row">
                     <div class="col-sm-12">
                        <div class="page-title-box">
                            <h4 class="page-title">SERVICE/REPAIR REQUEST</h4>
                        </div>
                        <div>
                          <a href="<?php echo page_url;?>Store/servicerequestdashboardforservicehistory"><span class='btn btn-success pull-right'>HISTORY</span></a>
                        </div>
                    </div>
					<div class="col-sm-12 text-center"><span style="color:red"><?php echo $this->session->flashdata('response');?></span></div>
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
                                    <!--<th>STATUS</th>-->
									<th>COMPANY NAME</th>
									<th>CHALLAN</th>
									<th>QC REMAKRS</th>
									
									<th>QC DONE ON</th>
									<th>QC DONE BY</th>
									<th>REQ. RAISED ON</th>
									<th>REQ. RAISED BY</th>
								
							
									
									
                                </tr>
                                </thead>


                                <tbody>
								
                                </tbody>
                            </table>
                        
						</div>
                    </div>
              
		
		</div>


<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
	
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Upload RGP</h4>
      </div>
	  <form name="frm" enctype="multipart/form-data" action="<?php echo page_url;?>Store/uploadchallan" method="post">
	  <input type="hidden" name="recordid" id="recordid">
      <div class="modal-body">
       <div class="row">
	   <div class="col-md-6">
	   <div class="form-group">
	   <label>Upload Challan</label>
	   <input type="file" name="image"  class="form-control" required>
	   
	   </div>
	   
	   </div>
	   </div>
	
      </div>
	  
	  
      <div class="modal-footer">
        <input type="submit" name="upch" class="btn btn-success">
      </div>
    </div>
</form>
  </div>
</div>


<div id="showReturnModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

 <form id="loginForm" method="post" action="<?php echo page_url;?>Store/saveActionType">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                <h4 class="modal-title">NEXT STEP</h4>

            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="field-1" class="control-label">ACTION TYPE</label>
                            <span id="error_order_type" style="color:red;">*</span>
                            <select class="form-control" id="generate_order" name="action_type" onchange="generateOrder()">
                              <option value="">---SELECT---</option>
                              <option value="1">RETURN WITHOUT REPAIR</option>
                              <option value="2">GENERATE INTERNAL ORDER</option>
                              <option value="3">OTHER</option>
                            </select>
                        </div>
                    </div>  
                    <div class="col-md-6" id="show_remarks" style="display: none;">
                        <div class="form-group">
                            <label for="field-1" class="control-label">REMARKS</label>
                            <span id="mark_required" style="color:red;"></span>
                            <textarea rows="4" cols="30" name="remarks" id="remarks" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="col-md-6" id="show_io_no" style="display: none;">
                        <div class="form-group">
                            <label for="field-1" class="control-label">IO No.</label>
                            <span id="mark_required1" style="color:red;"></span>
                            <input type="text" name="io_no" id="io_no" class="form-control">
                        </div>
                    </div>
                        
                </div>
            </div>

            <div class="modal-footer">
                <input type="hidden" name="hidden_id" id="hidden_id">

                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                <input type="submit" value="Submit" class="btn btn-success">

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
 "sAjaxSource": "<?php echo page_url;?>Store/getrepairservicerequestforservice",
 "aoColumns": [
				{ mData: 'sr_no' } ,
			


				{ mData: 'company' },
				{ mData: 'challan' },
				{ mData: 'instrument' },
				
				
				{ mData: 'qcon' },
				{ mData: 'qcby' },
				{ mData: 'addedOn' },
				{ mData: 'raisedby' }
						
						
                      
						
						
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
<script>
function showmodal(id)
{

	$("#recordid").val(id);
	$("#myModal").modal('show');
}


function showReturnModal(id)
{
  $("#showReturnModal").modal('show');
  $("#hidden_id").val(id);
}

  function generateOrder() {
    var generate_order = $("#generate_order").val();
    if (generate_order == 1) {
      $("#show_remarks").css("display", "block");
      $("#show_io_no").css("display", "none");
      $("#remarks").attr("required", true);
      $("#io_no").attr("required", false);
      $("#mark_required").text("*");
    } else if(generate_order == 2) {
      $("#show_io_no").css("display", "block");
      $("#show_remarks").css("display", "none");
      $("#io_no").attr("required", true);
      $("#remarks").attr("required", false);
      $("#mark_required1").text("*");
    } else if(generate_order == 3) {
      $("#show_io_no").css("display", "none");
      $("#show_remarks").css("display", "block");
      $("#remarks").attr("required", true);
      $("#io_no").attr("required", false);
      $("#mark_required").text("*");
    }  else {
      $("#show_io_no").css("display", "none");
      $("#show_remarks").css("display", "none");
        if (generate_order == '') {
          $("#generate_order").attr("required", true);
        }

    }
  }
</script>
</body>
</html>
