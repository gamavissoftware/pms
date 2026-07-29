<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title><?php echo sitetitle; ?> View Overall Help Tickets</title>
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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
#pageloader1
{
  background: rgba( 255, 255, 255, 0.8 );
  display: none;
  height: 100%;
  position: fixed;
  width: 100%;
  z-index: 9999;
}
#pageloader1 img
{
  left: 30%;
  margin-left: -10px;
  margin-top: -10px;
  position: absolute;
  top: 30%;
}

.timeline {
    border-left: 2px solid #ddd;
    padding-left: 20px;
    margin-left: 20px;
}

.timeline-item {
    margin-bottom: 20px;
    position: relative;
}

.timeline-date {
    font-weight: bold;
    margin-bottom: 5px;
}

.timeline-content {
    background-color: #f9f9f9;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -10px;
    top: 5px;
    width: 10px;
    height: 10px;
    background-color: #ddd;
    border: 2px solid #fff;
    border-radius: 50%;
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

                <div class="row card-box">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Filter by DF</label>
                            <select class="form-control task_dfno_filter" name="dfidforfilter" id="dfidforfilter" onchange="filterrecordbydf();">
                                <option value="ALL">ALL</option>
                                <?php $q = $this->db->select('id, df_no')->from('df_release')->where('df_status',0)->get();
                                    foreach($q->result() as $row){
                                    
                                ?>
                                <option value="<?php echo $row->id;?>" <?php if($this->uri->segment(4)==$row->id){echo "selected";}?>><?php echo $row->df_no;?></option>
                                <?php }?>
                            </select>
                        </div>
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
                                        <th>Delay</th>
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



<div id="showcommentbox" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<form id="updateprogressform" method="post" action="<?php echo page_url;?>Task/addyourcommentonticket"  enctype="multipart/form-data">
    <div id="pageloader1">
<img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
</div>
    <input type="hidden" name="recordid" id="recordid" value="">
 	<div class="modal-dialog">
                                	<div class="modal-content">
                                    	<div class="modal-header">
                                        	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Add Your Comment</h4>
                                        </div>
                                     <div class="modal-body">
                                        <div class="row">
                                        <div class="col-md-12">
									 	<div class="form-group">
									 	 <label for="field-2" class="control-label">Write Your Comment</label>
									 	 <span id="error_department_name" style="color:red;"></span>
									 	<textarea class="form-control" name="yourcomment" id="yourcomment" required></textarea>
									 	</div>
									 </div>
										</div>
                                        </div>
                                        <div class="modal-footer">
                                        	<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="depsave" class="btn btn-info" value="Submit">
                                        </div>
                                    </div>
                                </div>

                                </form>


                            </div><!-- /.modal -->

                        <div class="modal fade" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header">
                        <h5 class="modal-title" id="ticketModalLabel">Help Ticket Timeline</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                        </div>
                        <div class="modal-body">
                        <div id="timeline" class="timeline">
                        <!-- Timeline items will be injected here -->
                        </div>
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                        </div>
                        </div>
                        </div>



                <!-- Footer -->
               <?php $this->load->view('common/footer');?>
                <!-- End Footer -->

            </div>
            <!-- end container -->



        </div>



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
"sAjaxSource": "<?php echo page_url;?>Task/viewhelpticketdfwiselist/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>",
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
                { mData: 'delay' },
				{ mData: 'closestatus' },
				{ mData: 'comment' },
				{ mData: 'addcomment' }
				
		]
});   
});

</script>

<script type="text/javascript">
    function showcommentbox(i){
       $("#showcommentbox").modal('show');
       $("#recordid").val(i);
    }
</script>

<script type="text/javascript">
    function showcommunicationhistory(i){
       $("#ticketModal").modal('show');
       
    }
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js" integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
$( document ).ready(function() {
$('.task_dfno_filter').select2();
});
</script>
<script>
$(document).ready(function(){
    $("")
$("#updateprogressform").on("submit", function(){
$("#pageloader1").fadeIn();
});//submit
});//document ready
</script>

<script type="text/javascript">
    function filterrecordbydf(){
        var userid = '<?php echo $this->uri->segment(3);?>';
        var dfid = $("#dfidforfilter").val();
        window.location.href = 'https://pms.shubhampack.in/index.php/Task/viewhelpticketdfwise/'+userid+'/'+dfid;
    }
</script>

<script type="text/javascript">
 function loadTicketHistory(ticketId) {
    $.ajax({
        url: '<?php echo page_url;?>Task/history/' + ticketId,
        method: 'GET',
        success: function(ticketHistory) {
            var timeline = $('#timeline');
            // Clear previous timeline items
            timeline.empty();

            // Populate timeline with ticket history
            ticketHistory.forEach(function(item) {
                var timelineItem = `
                    <div class="timeline-item">
                        <div class="timeline-date">${item.added_on}</div>
                        <div class="timeline-content">${item.comment}</div>
                    </div>
                `;                timeline.append(timelineItem);
            });
        },
        error: function() {
            $('#timeline').html('<div class="alert alert-danger">Failed to load ticket history.</div>');
        }
    });
}
</script>

    </body>
</html>