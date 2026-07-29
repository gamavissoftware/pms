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
table.pretty thead th {
				background: <?php echo $LOGO->colorcode;?>;
				color:#fff;
				font-weight:bold;
			}

body{margin-top:40px;}
.widget {
    margin: 0 0 25px 0;
    display: block;
    -webkit-border-radius: 2px;
    -moz-border-radius: 2px;
    border-radius: 2px;
}
.widget .widget-heading {
    padding: 7px 15px;
    -webkit-border-radius: 2px 2px 0 0;
    -moz-border-radius: 2px 2px 0 0;
    border-radius: 2px 2px 0 0;
    text-align: center;
    background: #084c94;
    color: white;
	min-height: 57px;
}
.widget .widget-body {
    padding: 10px 15px;
    font-size: 36px;
    font-weight: 300;
    background: #706f6d;
}

.widget1 .widget1-body {
    padding: 10px 15px;
    font-size: 36px;
    font-weight: 300;
    background: #cccf2f;
}
.widget1 .widget1-heading {
    padding: 7px 15px;
    -webkit-border-radius: 2px 2px 0 0;
    -moz-border-radius: 2px 2px 0 0;
    border-radius: 2px 2px 0 0;
    text-transform: uppercase;
    text-align: center;
    background: green;
    color: white;
}
.feedback {
  background-color : #31B0D5;
  color: white;
  padding: 10px 20px;
  border-radius: 4px;
  border-color: #46b8da;
}

#mybutton {
  position: fixed;
  bottom: -4px;
  right: 10px;
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
						      <a href="<?php echo page_url;?>Audit_report/View_complete_report_by_category/<?php echo $this->uri->segment(3);?>"><span class="btn btn-warning">View Report</span></a>
						  <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">Add New Task</button>
                               
                            </div>
                           
                            <h4 class="page-title">&nbsp <?php 
							$this->db->select('audit_type_id, audit_type')->from('audit_type');
							$this->db->where('audit_type_id',$this->uri->segment(3));
							$query = $this->db->get();
							$res = $query->result();
							foreach($res as $row){
								echo $row->audit_type;
							}
							?> Department Audit List</h4>
                        </div>
                    </div>
				<div class="panel-group" id="accordion">
    <div class="panel panel-default">
      <!--<div class="panel-heading">
        <h4 data-toggle="collapse" data-parent="#accordion" href="#collapse1" class="panel-title expand">
           <div class="right-arrow">+</div>
          
        </h4>
      </div>-->
      <div id="collapse1" class="panel-collapse collapse">
        <div class="panel-body">
			<?php 
				$query = $this->db->select('audit_section_id, audit_type, audit_section,status')->from('audit_section')->where('audit_type',$this->uri->segment(3))->where('status','1')->get();
				foreach($query->result() as $timing){?>
				<a href="<?php echo page_url;?>Audit_report/view_audit_tasks/<?php echo $this->uri->segment(3);?>/<?php echo $timing->audit_section_id;?>"><div class="col-lg-2 col-md-2 col-sm-4">
              <div class="widget">
                <div class="widget-heading clearfix">
                  <div class="text-center"><strong style="font-size:11px;"><?php echo $timing->audit_section;?></strong></div>
                 
                </div>
                <div class="widget-body clearfix">
                   <div class="text-center number" style="color:#fff;">
					<?php 
					$this->db->select('audit_type_id,task_id, status')->from('audit_tasks');
					$this->db->where('audit_type_id',$this->uri->segment(3))->where('audit_section_id',$timing->audit_section_id);
					$query = $this->db->get();
					$res = $query->result();
					echo count($res);
					?>
				   </div>
                </div>

               
              </div>
															</div></a><?php }?>
		</div>
      </div>
    </div>
   </div>			
			
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

                 <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
						<form action="<?php echo page_url;?>Audit_report/updateremarks/<?php echo $this->uri->segment('3');?>" method="post" enctype="multipart/form-data">
                            <table id="example" class="table table-striped table-bordered pretty">
                                <thead>
                                <tr>
                                    <th>Sr No.</th>
									<?php if($this->uri->segment(3)=='1'){?><th>Section</th><?php }?>
                                    <th>Process Name</th>
                                    <th>Process Objective</th>
                                    <th>Process Steps</th>
                                    <th>Tentative Time</th>
                                    <th>Scheduled TAT</th>
                                    <th>Status</th>
                                    <th>Category</th>
                                    <th>Weightage</th>
									<th style="width:20%">Audit Checklist</th>
                                    <th>Action</th>
                                    
                                </tr>
                                </thead>
									
                            </table>
							<center><button class="feedback" id="checklist">Update checklist</button></center>

							</form>
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
														<span id="error_audit_section" style="color:red;"></span>
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
												 
												<div class="col-md-6">
													<div class="form-group">
														 <label for="field-2" class="control-label">Process Name</label>
														 <span id="error_process_name" style="color:red;">*</span>
														<textarea class="form-control" name="process_name" id="process_name"></textarea>
													</div>
												</div>
												
												
												
												
												
												<div class="col-md-3">
													<div class="form-group">
                                                        <label for="field-2" class="control-label">Tentative Time</label>
														<span id="error_audit_section" style="color:red;">*</span>
                                                        <select class="form-control" name="tentative_time" id="tentative_time">
														<option value="">--Select TAT--</option>
														<?php 
														$query = $this->db->select('timing_id, timing_period, timing_status')->from('timing_slot')->where('timing_status','1')->get();
														foreach($query->result() as $tat){
														?>
														<option value="<?php echo $tat->timing_id;?>"><?php echo $tat->timing_period;?></option>
														<?php }?>
														</select>
														</div>
												<script type="text/javascript">
											
													$("#tentative_time").change(function(){
													var tentative_time=$("#tentative_time").val();
													//alert(tentative_time);
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Audit_report/select_tat_durations",
													data:"tentative_time="+tentative_time,
													success:function(data){
													$("#duration").html(data);
													}
													});
													});
											
												</script>
												
												
												</div>
												
												<div class="col-md-3">
												     <div class="form-group">
												         <label>Duration</label>
													<select class="form-control" id="duration" name="duration">
														
													</select>
												 </div>
											
												</div>
													<div class="col-md-3">
											    
											    	<div class="form-group">
												<label for="field-2" class="control-label">Category</label>
												<span id="error_category" style="color:red;">*</span>
												<select class="form-control" id="category" name="category">
													<option value="">--Select Category--</option>
													<option value="1">Normal</option>
													<option value="2">Critical</option>
												</select>
												</div>
											</div>	
											
												<div class="col-md-3">
											    
											    	<div class="form-group">
												<label for="field-2" class="control-label">weightage (%)</label>
											
											<input type="number" class="form-control" name="weightage" id="weightage" value="">
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
"sAjaxSource": "<?php echo page_url;?>Audit_report/Audit_task_list/<?php echo $this->uri->segment(3);?>",
"aoColumns": [
				{ mData: 'sr_no' } ,
					<?php if($this->uri->segment(3)=='1'){?>{ mData: 'audit_section' },<?php }?>
				{ mData: 'process_name' },
				{ mData: 'process_objective' },
				{ mData: 'process_steps' },
				{ mData: 'tentative_time' },
				{ mData: 'scheduledtime' },
				{ mData: 'status' },
				{mData:'category'},
				{mData:'weightage'},
				{ mData: 'update_status' },
				{ mData: 'edit' }
				
				
		]
});   
});

</script>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script language="javascript" type="text/javascript">   
jQuery.noConflict();
$(document).ready(function() {
	
	if($('.dny').is(":checked")){
			$("#checklist").attr('disabled',true);
	}
	
	$("#checklist").attr('disabled',true);
$("#save").click(function() {
	
var audit_type = $("#audit_type").val();
if(audit_type=='')
{
	$("#error_audit_type").html('Required!');
}

var process_objectives = $("#process_objectives").val();
if(process_objectives=='')
{
	
	$("#error_process_objectives").html('Required!');
}


var process_name = $("#process_name").val();
if(process_name=='')
{
	
	$("#error_process_name").html('Required!');
}

var category = $("#category").val();
if(category=='')
{
	
	$("#error_category").html('Required!');
}

var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}

if(audit_type=='' || process_objectives==''|| process_name=='' || status==''  || category=='')
{
	
	return false;
}

});
});
</script>


<script>
function display_qtybox(id,taskid){
$('#makenewhidden'+id).html("");
if($('.dny').is(":checked")){
	$("#checklist").attr('disabled',false);
	$('#qty'+id).css('display','block');
	$('#report'+id).css('display','block');
	$('#qty'+id).attr('required',true);

	$("#makenewhidden"+id).append('<input type="hidden" value="'+taskid+'" name="countall[]">');
	
} else{
	$('#qty'+id).css('display','none');
	$('#report'+id).css('display','none');
	$('#qty'+id).attr('required',false);
		//$('#makenewhidden'+id).remove();

	
}
                
}
</script>
    </body>
</html>