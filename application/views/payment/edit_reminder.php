<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?></title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
		<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" rel="stylesheet"/>
        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
	
		<style>
			.divheight{
			padding-top:110px;
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
  left: 30%;
  margin-left: -10px;
  margin-top: -10px;
  position: absolute;
  top: 30%;
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
            <div class="container">

                <!-- Page-Title -->
				
                <div class="row">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						
                        <h4 class="page-title">Edit Task Detail</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
                <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
				
				<?php 
				$q = $this->db->select('*')->from('reminder_tasks')->where('id',$this->uri->segment(3))->get();
				foreach($q->result() as $row);
				?>
               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">
<form id="loginForm" method="post" action="<?php echo page_url;?>Reminder/update_reminder/<?php echo $row->id;?>" enctype="multipart/form-data">
    <div id="pageloader">
        <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." /></div>	
                             <div class="row">
                                           	<div class="col-md-12">
													<div class="form-group">
														 <label for="field-2" class="control-label">Task Name</label>
														 <span id="error_task_name" style="color:red;">*<?php echo form_error('task_name'); ?></span>
														 <input type="text" class="form-control" name="task_name" id="task_name" value="<?php echo $row->task_name;?>">
													</div>
												</div> 
													<div class="col-md-12">
													<div class="form-group">
														 <label for="field-2" class="control-label">Detail</label>
														 <span id="error_detail" style="color:red;">*<?php echo form_error('detail'); ?></span>
														 <textarea class="form-control" name="detail" id="detail"><?php echo $row->detail;?></textarea>
													</div>
												</div>  
												</div>
												 <div class="row">
											<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Due Date</label>
														 <span id="error_billing_date" style="color:red;">*<?php echo form_error('billing_date'); ?></span>
														 <input type="text" class="form-control datepicker" name="billing_date" id="billing_date" value="<?php echo date('m/d/Y',strtotime($row->billing_date));?>">
													</div>
												</div>
											
												<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Task Type </label>
														 <span id="error_tasktype" style="color:red;">*<?php echo form_error('tasktype'); ?></span>
														 <select class="form-control" name="tasktype" id="tasktype">
														     <option value="">Select type</option>
														     <option value="One time" <?php if($row->tasktype=='One time'){echo "selected";}?>>One time</option>
														     <option value="Monthly" <?php if($row->tasktype=='Monthly'){echo "selected";}?>>Monthly</option>
														     <option value="Half Yearly" <?php if($row->tasktype=='Half Yearly'){echo "selected";}?>>Half Yearly</option>
														     <option value="Yearly" <?php if($row->tasktype=='Yearly'){echo "selected";}?>>Yearly</option>
														 </select>
													</div>
												</div>
												
												<script>
												    
                                                $(function() {
                                                $('#row_dim').hide(); 
                                                $('#tasktype').change(function(){
                                            if($('#tasktype').val() == 'One time') {
                                                $('#row_dim').hide(); 
                                                } else {
                                                $('#row_dim').show(); 
                                                } 
                                                });
                                                });
												</script>
												
												<div class="col-md-3" <?php if($row->tasktype=='One time'){?> style="display:none"<?php }?> id="row_dim">
												    <div class="form-group">
												        <label>Remind before</label><br>
												        <?php 
												        $str_arr = explode (",", $row->reminder_schedule); 
												        
												        ?>
												        
												        2 Days <input type="checkbox" name="reminderin[]" <?php if($str_arr[0]=='2'){echo "checked";}?> <?php if($str_arr[1]=='2'){echo "checked";}?> <?php if($str_arr[2]=='2'){echo "checked";}?> value="2">
												         5 Days <input type="checkbox" name="reminderin[]" <?php if($str_arr[0]=='5'){echo "checked";}?> <?php if($str_arr[1]=='5'){echo "checked";}?> <?php if($str_arr[2]=='5'){echo "checked";}?> value="5">
												         
												         7 Days <input type="checkbox" name="reminderin[]" <?php if($str_arr[0]=='7'){echo "checked";}?> <?php if($str_arr[1]=='7'){echo "checked";}?> <?php if($str_arr[2]=='7'){echo "checked";}?> value="7">
												    </div>
												</div>
												<div class="col-md-3">
												    <div class="form-group">
												        <label>Reminder Date</label>
												        <input type="text" class="form-control datepicker" name="reminder_date" id="reminder_date" value="<?php echo date('m/d/Y',strtotime($row->reminder_date));?>">
												    </div>
												</div>
											<div class="col-md-3">
												<div class="form-group">
												<label for="field-2" class="control-label">Assign To</label>
												<span id="error_assign_to" style="color:red;">*<?php echo form_error('assign_to'); ?></span>
                                        <select class="form-control select2" name="assign_to" id="assign_to">
                                            <option value="">--Select User--</option>
                                            <?php 
                                            $q = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('user_status','1')->where('hide_profile','0')->order_by('first_name','asc')->get();
                                            foreach($q->result() as $row1){
                                            ?>
                                            <option value="<?php echo $row1->user_id;?>" <?php if($row->assign_to==$row1->user_id){echo "selected";}?>><?php echo $row1->first_name." ".$row1->last_name;?></option>
                                            <?php }?>
                                        </select>
												</div>
											</div>
											
											<div class="col-md-12">
											<div class="form-group pull-right">
												<input type="submit" id="save" class="btn btn-info" value="Submit">
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
        <script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
<script language="javascript" type="text/javascript">   
$(document).ready(function() {
$('.select2').select2({ });
$("#save").click(function() {
	var task_name= $("#task_name").val();
if(task_name=='')
{
	$("#error_task_name").html('Required!');
}
var billing_date = $("#billing_date").val();
if(billing_date=='')
{
	$("#error_billing_date").html('Required!');
}
var next_billing_date = $("#next_billing_date").val();
if(next_billing_date=='')
{
	$("#error_next_billing_date").html('Required!');
}


var reminder_date = $("#reminder_date").val();
if(reminder_date=='')
{
	$("#error_reminder_date").html('Required!');
}



if(task_name==''|| billing_date=='' || next_billing_date=='' || reminder_date=='')
{
	
	return false;
}

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
 <script> $(document).ready(function() {
 var date = new Date();
date.setDate(date.getDate());
   
$('.datepicker').datepicker({
    todayHighlight:true
    
    
});

                $("#datepicker1").datepicker({
					orientation: 'bottom',
					todayHighlight:true
				});
			//	$('.datepicker').datepicker({todayHighlight:true});
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
					
                })

            });</script>
    </body>
</html>