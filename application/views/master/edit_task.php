<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">
		<link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
		<title><?php echo sitetitle; ?>Edit Task</title>
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
         <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
    </head>
    <body>
    	<!-- Navigation Bar-->
        <header id="topnav">
          <?php $this->load->view('common/nav-menu');?>
        </header>
        <!-- End Navigation Bar-->
        <div class="wrapper">
            <div class="container">
            	<!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right"></div>
                           <h4 class="text-center" style="background-color:#fbeeee; padding:10px; 10px; 10px; 10px;">Edit Task Master</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

<?php 
	$q = $this->db->select('*')->from('task_management')->where('task_id',$this->uri->segment(3))->get();
	foreach($q->result() as $edit);
?>

               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">
<form method="post" id="loginForm" action="<?php echo page_url;?>Task/update_task/<?php echo $this->uri->segment(3);?>" onsubmit="return validation();">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Department</label>
														<span id="error_department" style="color:red;">*</span>
														<select class="form-control multipleselect" name="department" id="department">
															<option value="">Select Department</option>
															<?php 
																	$businessloc = 2;
																	$q = $this->db->select('department_id, department')->from('departments')->where('business_loc_id',2)->where('status',1)->get();
																	foreach($q->result() as $row){
															?>
															<option value="<?php echo $row->department_id;?>" <?php if($edit->department_id==$row->department_id){echo "selected";}?>><?php echo $row->department;?></option>
															<?php }?>
														</select>
												    </div>
                                                </div>
                                                <script type="text/javascript">

													$("#department").change(function(){
													var department=$("#department").val();
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Delegation/user_list_new",
													data:"department="+department,
													success:function(data){
														//alert(data);
													$("#responsibleperson").html(data);
													}
													});
													});

													</script>

                                                  <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Responsible Person</label>
														<span id="error_responsibleperson" style="color:red;">*</span>
														<select class="form-control" name="responsibleperson" id="responsibleperson">
														<?php 
															$qs = $this->db->select('b.user_id, b.title, b.first_name, b.last_name')->from('system_users b')->where('b.department_id',$edit->department_id)->get();
															if($qs->num_rows()>0){
																foreach($qs->result() as $roww){?>
																<option value="<?php echo $roww->user_id;?>" <?php if($roww->user_id=$edit->responsible_person_id){echo "selected";}?>><?php echo strtoupper($roww->title." ".$roww->first_name." ".$roww->last_name);?></option>
														<?php }	}?>	
														</select>
												    </div>
                                                </div>
												
												<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Task Name</label>
														<span id="error_taskname" style="color:red;">*</span>
														<input type="text" class="form-control" name="taskname" id="taskname" value="<?php echo $edit->task_name;?>">
                                                    </div>
                                                </div>
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Task Type</label>
														 <span id="error_task_type" style="color:red;">*</span>
														<select class="form-control" name="task_type" id="task_type">
															<option value="1" <?php if($edit->task_type==1){echo "selected";}?> >Main Task</option>
															<option value="2" <?php if($edit->task_type==2){echo "selected";}?> >Sub Task</option>
														</select>
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">TAT (in Days)</label>
														 <span id="error_tat" style="color:red;">*</span>
														<input type="number" class="form-control" name="tat" id="tat" value="<?php echo $edit->tat;?>">
													</div>
												</div>

												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">TAT (in Days) for Repeat</label>
														 <span id="error_repeattat" style="color:red;">*</span>
														<input type="number" class="form-control" name="repeattat" id="repeattat" value="<?php echo $edit->repeattat;?>">
													</div>
												</div>

												<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">TAT Start From</label>
														 <span id="error_startfrom" style="color:red;">*</span>
														<select class="form-control" name="startfrom" id="startfrom"> 
															<?php 
																$q = $this->db->select('a.task_id, a.task_name, b.department')->from('task_management a')->join('departments b','a.department_id=b.department_id','left')->where('a.status',1)->get();
																foreach($q->result() as $existingtask){
															?>
															<option value="<?php echo $existingtask->task_id;?>" <?php if($edit->tat_start_from==$existingtask->task_id){echo "selected";}?> ><?php echo $existingtask->task_name;?> (<?php echo $existingtask->department;?>)</option>
															<?php }?>
														</select>
													</div>
												</div>

												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Task Frequency</label>
														 <span id="error_taskfrequency" style="color:red;">*</span>
														<select class="form-control" name="taskfrequency" id="taskfrequency"> 
															<option value="1" <?php if($edit->task_frequency==1){echo "selected";}?> >Onetime</option>
															<option value="2" <?php if($edit->task_frequency==2){echo "selected";}?>>Recurring Task</option>
														</select>
													</div>
												</div>

												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Sort Order</label>
														 <span id="error_sortorder" style="color:red;">*</span>
														<input type="text" class="form-control" name="sortorder" id="sortorder" value="<?php echo $edit->sortorder;?>">
													</div>
												</div>

												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Status</label>
														 <span id="error_status" style="color:red;">*</span>
														<select class="form-control" name="status" id="status"> 
															<option value="1" <?php if($edit->status==1){echo "selected";}?> >Active</option>
															<option value="2" <?php if($edit->status==2){echo "selected";}?>>Inactive</option>
														</select>
													</div>
												</div>
												<div class="col-md-12">
													<h4 class="text-center" style="background-color:#f1f1f1; padding:10px; 10px; 10px; 10px;">Define Messages for Departments</h4><hr>
												</div>
										
												<?php 
													$q3 = $this->db->select('id, department_id, task_message')->from('task_related_messages')->where('taskid',$this->uri->segment(3))->get();
													if($q->num_rows()>0){
													foreach($q3->result() as $rowss){												
													?>
											
												<div class="col-md-3">
													<div class="form-group">
														<label>Department</label> 
														<span style="color: red;" id="error_messagefordepartment">*</span>
												<select class="form-control multipleselect" id="messagefordepartment0" name="messagefordepartment[]">
													<option value="">--Applicable for All Department--</option>
													<?php $businessloc = 2;
													 $query = $this->db->select('department_id,status,department')->from('departments')->where('status','1')->where('business_loc_id',$businessloc)->get();
													foreach($query->result() as $department){?>
													<option value="<?php echo $department->department_id;?>" <?php if($rowss->department_id==$department->department_id){echo "selected";}?> ><?php echo strtoupper($department->department);?></option>
													<?php }?>
												</select>
												<input type="hidden" name="recordid[]" id="recordid" value="<?php echo $rowss->id;?>">
													</div>
												</div>
												
												<div class="col-md-8">
													<label>Message</label>
													<span class="btn btn-xs btn-info" onclick="appenddfnumber('{df_number}', 'definemessage',<?php echo $rowss->id;?>);">DF Number</span> <span class="btn btn-xs btn-info" onclick="appenddepartmentname('{department_name}','definemessage',<?php echo $rowss->id;?>);">Department Name</span> <span class="btn btn-xs btn-info" onclick="appenddepartmenthead('{department_hod}','definemessage',<?php echo $rowss->id;?>);">Department HOD</span>
													<span id="error_message">*</span>
													<div class="form-group">
														<textarea class="form-control" name="definemessage[]" id="definemessagess<?php echo $rowss->id;?>"><?php echo $rowss->task_message;?></textarea>
														<script>
															var recordid = '<?php echo $rowss->id;?>';
															CKEDITOR.replace( 'definemessagess'+recordid);
															</script>
													</div>
												</div>
											
												<div class="col-md-1">
												<div class="form-group" style="padding-top:50px">
													<a href="<?php echo page_url;?>Task/deletetaskmessage/<?php echo $rowss->id;?>/<?php echo $this->uri->segment(3);?>" onclick="return confirm('Are you sure you want to delete this item?');"><button type="button" class="btn btn-danger" name="add"><i class="fa fa-trash"></i></button></a>
												</div>
												
												</div>
												<?php }}?>
												<div class="col-md-3">
													<div class="form-group">
														<label>Department</label> 
														<span style="color: red;" id="error_messagefordepartment">*</span>
												<select class="form-control multipleselect" id="messagefordepartment0" name="messagefordepartment[]">
													<option value="">--Applicable for All Department--</option>
													<?php $businessloc = 2;
													 $query = $this->db->select('department_id,status,department')->from('departments')->where('status','1')->where('business_loc_id',$businessloc)->get();
													foreach($query->result() as $department){?>
													<option value="<?php echo $department->department_id;?>"><?php echo strtoupper($department->department);?></option>
													<?php }?>
												</select>
												<input type="hidden" name="recordid[]" id="recordid" value="">
													</div>
												
												</div>
												
												<div class="col-md-8">
													<label>Message</label>
													<span class="btn btn-xs btn-info" onclick="append_dfnumber('{df_number}','definemessage',0);">DF Number</span> <span class="btn btn-xs btn-info" onclick="appenddepartment_name('{department_name}','definemessage',0);">Department Name</span> <span class="btn btn-xs btn-info" onclick="appenddepartment_head('{department_hod}','definemessage',0);">Department HOD</span>
													<span id="error_message">*</span>
													<div class="form-group">
														<textarea class="form-control" name="definemessage[]" id="definemessage0"></textarea>
															<script>
															CKEDITOR.replace( 'definemessage0' );
															</script>
													</div>
												</div>
											
												<div class="col-md-1">
												<div class="form-group" style="padding-top:50px">
													<button type="button" class="btn btn-warning" name="add" id="addmore_btn1"><i class="fa fa-plus"></i></button>
												</div>
												
												</div>

												
												
												<div id="dynamictasks1"></div>
												<div class="col-md-2">
													<div class="form-group">
														<label>Is it Final Step?</label>
														<input type="checkbox" name="isitfinalstep" value="1" <?php if($edit->isitfinalstep==1){echo "checked";}?>>
													</div>
												</div>

												<div class="col-md-2">
													<div class="form-group">
														<label>Show in MD Sir Gantt Chart</label>
														<select class="form-control" name="visibleformd" id="visibleformd">
															<option value="1" <?php if($edit->visibleformd==1){echo "selected";}?>>Yes</option>
															<option value="0" <?php if($edit->visibleformd==0){echo "selected";}?>>No</option>
														</select>
													</div>
												</div>

												<div class="col-md-3">
													<div class="form-group">
														<label>Machine Ready on Floor Step?</label>
														<select class="form-control" name="machinereadyonfloor" id="machinereadyonfloor">
															<option value="1" <?php if($edit->machinereadyonfloor==1){echo "selected";}?>>Yes</option>
															<option value="0" <?php if($edit->machinereadyonfloor==0){echo "selected";}?>>No</option>
														</select>
													</div>
												</div>


											
												<div class="col-md-12" style="padding-top:30px;"></div><hr>
												<div class="col-md-12">
													<div class="form-group pull-right">
													<input type="submit" class="btn btn-success" name="Save" id="savedata">
												</div>
												</div>

                            </div>
                            	

                            </form>
                            <!-- end row -->
                        </div> <!-- end ard-box -->
                    </div><!-- end col-->

                </div>
                <!-- end row -->
 

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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script>
$(document).ready(function(){
	   $("#teamupdate").attr('disabled',false);
	   $("#teamupdate").val('Update');
  $("#loginForm").on("submit", function(){
   // $("#pageloader").fadeIn();
   $("#teamupdate").attr('disabled',true);
     $("#teamupdate").val('Please Wait...');
  });//submit
});//document ready
</script>

<script language="javascript" type="text/javascript">   
$(document).ready(function() {
$("#savedata").click(function() {
var department = $("#department").val();
if(department=='')
{
	$("#error_department").html('Required!');
}
var taskname = $("#taskname").val();
if(taskname=='')
{
	
	$("#error_taskname").html('Required!');
}
var task_type = $("#task_type").val();
if(task_type=='')
{
	
	$("#error_task_type").html('Required!');
}
var tat = $("#tat").val();
if(tat=='')
{
	
	$("#error_tat").html('Required!');
}

var startfrom = $("#startfrom").val();
if(startfrom=='')
{
	
	$("#error_startfrom").html('Required!');
}

var taskfrequency = $("#taskfrequency").val();
if(taskfrequency=='')
{
	
	$("#error_taskfrequency").html('Required!');
}

var sortorder = $("#sortorder").val();
if(sortorder=='')
{
	
	$("#error_sortorder").html('Required!');
}

var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}


if(department=='' || taskname=='' || task_type=='' || tat=='' || startfrom=='' || taskfrequency=='' ||  sortorder=='' || status=='')
{
	
	return false;
}

});
});
</script>    
<script type="text/javascript">
$(document).ready(function(){
 var i=1;
 var dfno =  "'{df_number} '";
 var mes = "'definemessage'";
 var depet = "'{department_name}'";
 var deprthead = "'{department_hod}'";
 $('#addmore_btn1').click(function(){

 $('#dynamictasks1').append('<div id="row'+i+'"><div class="col-md-3"><div class="form-group"><label>Department</label> <span style="color: red;" id="error_messagefordepartment">*</span><select class="form-control multipleselect" id="messagefordepartment'+i+'" name="messagefordepartment[]"><option value="">--Applicable for All Department--</option><?php $businessloc = 2; $query = $this->db->select('department_id,status,department')->from('departments')->where('status','1')->where('business_loc_id',$businessloc)->get(); foreach($query->result() as $department){?><option value="<?php echo $department->department_id;?>"><?php echo strtoupper($department->department);?></option><?php }?></select><input type="hidden" name="recordid[]" id="recordid" value=""><div class="form-group"></div></div></div><div class="col-md-8"><label>Message</label><span class="btn btn-xs btn-info" onclick="append_dfnumber('+dfno+','+mes+','+i+');">DF Number</span> <span class="btn btn-xs btn-info" onclick="appenddepartment_name('+depet+','+mes+','+i+');">Department Name</span> <span class="btn btn-xs btn-info" onclick="appenddepartment_head('+deprthead+','+mes+','+i+');">Department HOD</span><span id="error_message">*</span><div class="form-group"><textarea class="form-control" name="definemessage[]" id="definemessage'+i+'"></textarea></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:50px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div><br/>');
   	 intialzeselect2(i);
 	 CKEditorChange("definemessage"+i);
  i++;
 });
 
  function intialzeselect2(i){
$('#messagefordepartment'+i).select2();
 }
 
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id+'').remove();
 });
 
});

function CKEditorChange(name) {
      CKEDITOR.replace(name);
    }
	  </script>
	  <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
	   <script type="text/javascript">
            $( document ).ready(function() {
                    $('.multipleselect').select2();
                     $('#startfrom').select2();
            });

   function appenddfnumber(tag,definemessage,rowid)
    {
      var edi='definemessagess'+rowid;

      CKEDITOR.instances[edi].insertText(tag); 
   

    }

     function appenddepartmentname(tag,definemessage,rowid)
    {
      var edi='definemessagess'+rowid;

      CKEDITOR.instances[edi].insertText(tag); 
   

    }
     function appenddepartmenthead(tag,definemessage,rowid)
    {
      var edi='definemessagess'+rowid;

      CKEDITOR.instances[edi].insertText(tag); 
   

    }

    function append_dfnumber(tag,definemessage,rowid)
    {
      var edi='definemessage'+rowid;

      CKEDITOR.instances[edi].insertText(tag); 
   

    }
    function appenddepartment_name(tag,definemessage,rowid)
    {
      var edi='definemessage'+rowid;

      CKEDITOR.instances[edi].insertText(tag); 
   

    }

    function appenddepartment_head(tag,definemessage,rowid)
    {
      var edi='definemessage'+rowid;

      CKEDITOR.instances[edi].insertText(tag); 
   

    }

    

    
        </script>
</body>
</html>