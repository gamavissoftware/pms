<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title>Delegation Form</title>
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
			padding-top:30px;
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
      

        <div class="wrapper">
            <div class="container">

                <!-- Page-Title -->
				<div class="divheight hidden-xs"></div>
                <div class="row">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						
                            <h4 class="page-title">WORK DELEGATION FORM</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
				
               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">
<form id="loginForm" method="post" action="<?php echo page_url;?>Delegation/update_delegation_task" enctype="multipart/form-data">
        <div id="pageloader">
        <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
        </div>	
                             <div class="row">
                                             <input type="hidden" name="case_no" value="<?php echo $task->case_no; ?>">
											<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">YOUR NAME</label>
														 <span id="error_your_name" style="color:red;">*</span>
														 <select class="form-control" id="your_name" name="your_name">
													
													<?php 
													$user_id =$this->session->userdata['logged_in']['user_id'];	
															
													$query = $this->db->select('a.assigned_to, b.user_id, b.title, b.first_name, b.last_name')->from('delegation_master a')->join('system_users b','a.assigned_to=b.user_id','left')->where('b.user_id',$user_id)->get();
													foreach($query->result() as $users){?>
													<option value="<?php echo $users->assigned_to;?>"><?php echo strtoupper($users->title." ".$users->first_name." ".$users->last_name);?></option>
													<?php }?>
													<?php if($user_id==114){?>
														<option value="61">Virendra Sharma</option>
													<?php }?>													
												</select>
													</div>
												</div>

												<div class="col-md-3">
													<div class="form-group">
														<label>Select Company</label>
														<span style="color: red;" id="error_business_loc">*</span>
														<select class="form-control" id="business_loc" name="business_loc">

													<?php 

													$this->db->select('a.company_name, a.business_loc_id,a.state_id, a.city_id,b. 	state_id,b.state_name,c.city_id,c.city_name')->from('business_location a')->join('states b','a.state_id=b.state_id','left')->join('cities c','a.city_id=c.city_id','left')->where('business_loc_status','1')->where('business_loc_id',2);

													$this->db->order_by('a.company_name','asc');

													$query = $this->db->get();

													$res = $query->result();

													foreach($res as $row){

													?>

													<option value="<?php echo $row->business_loc_id;?>"><?php echo strtoupper($row->company_name);?>(<?php echo strtoupper($row->state_name);?>, <?php echo strtoupper($row->city_name);?>)</option>

													<?php }?>		

												</select>
													</div>
												</div>

												
<!-- 
													<div class="col-md-3">
													<div class="form-group">
													<label for="field-2" class="control-label">DEPARTMENT DELEGATED TO </label>
													<span id="error_department" style="color:red;">*</span>
													<select class="form-control" id="department" name="department" required>
														<option value="">SELECT DEPARTMENT</option>
														<?php 
														$q = $this->db->select('department_id, department')->from('departments')->where('business_loc_id',2)->where('status',1)->get();
															
															foreach($q->result() as $row){

														?>
														<option value="<?php echo $row->department_id;?>"><?php echo ucwords(strtoupper($row->department));?></option>
													<?php }?>
													</select>
													<script type="text/javascript">

													$("#department").change(function(){
													var department=$("#department").val();
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Delegation/user_list_new",
													data:"department="+department,
													success:function(data){
														//alert(data);
													$("#delegate_to").html(data);
													}
													});
													});

													</script>
													</div>
												</div> -->
												<div class="col-md-6">
													<div class="form-group">
														 <label for="field-2" class="control-label">DELEGATED TO</label>
														 <span id="error_delegate_to" style="color:red;">*</span>
														 <!-- <select class="form-control select3" id="delegate_to" name="delegate_to" required>
													<option value="">--CHOOSE--</option>
													
												</select> -->

                                             <select class="select3" id="delegate_to" name="delegate_to[]" multiple>
<?php 
$this->db->select('u.user_id, u.first_name, u.last_name, d.department')
         ->from('system_users u')
         ->join('departments d', 'u.department_id = d.department_id', 'left')
         ->where('u.user_status', 1);

$users = $this->db->get()->result();

foreach($users as $row){

    $selected = in_array($row->user_id, $selected_users) ? 'selected' : '';
?>
    <option value="<?php echo $row->user_id; ?>" <?php echo $selected; ?>>
        <?php echo strtoupper($row->first_name.' '.$row->last_name); ?>
        (<?php echo strtoupper($row->department); ?>)
    </option>
<?php } ?>
</select>
													</div>
												</div>
												<div class="col-md-12">
												<div class="form-group">
												<label for="field-2" class="control-label">WORK DELEGATED</label>
												<span id="error_task" style="color:red;">*</span>
												<textarea class="form-control" name="task" id="task" required><?php echo $task->task; ?></textarea>
												</div>
											</div>
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="">EMAIL URL</label>
                                                  <textarea name="email_url" id="" class="form-control"><?php echo $task->email_url; ?></textarea>
                                                </div>
                                            </div>
											<div class="col-md-4">
												<div class="form-group">
												<label for="field-2" class="control-label">UPLOAD IMAGE(IF ANY)</label>
												
												<input type="file" class="form-control" name="image" id="image" value="">
                                                <?php if(!empty($task->image)){ ?>
    <img src="<?php echo base_url('image_bank/delegation/'.$task->image); ?>" width="100">
<?php } ?>
												</div>
											</div>
											
											
											 <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">DUE DATE</label>
														<span id="error_datepicker1" style="color:red;"></span>
                                                        <input type="date" id="work_completion_date" name="work_completion_date" class="form-control" autocomplete="off" value="<?php echo $task->targetdate; ?>" required>
                                                    </div>
                                                </div>
											
											
											<div class="col-md-12">
											<div class="form-group pull-right">
												<input type="submit"  class="btn btn-info" value="Update">
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

<!-- <script language="javascript" type="text/javascript">   
$(document).ready(function() {
	$('.select2').select2({ });
	$('.select3').select2({ });
	$('.select4').select2({ });
// $("#save").click(function() {
// 	var your_name= $("#your_name").val();
// if(your_name=='')
// {
// 	$("#error_your_name").html('Required!');
// }
// var business_loc = $("#business_loc").val();
// if(business_loc=='')
// {
// 	$("#error_business_loc").html('Required!');
// }
// // var department = $("#department").val();
// // if(department=='')
// // {
// // 	$("#error_department").html('Required!');
// // }
//    if(!delegate_to || delegate_to.length == 0){
//         $("#error_delegate_to").html('Required!');
//         isValid = false;
//     }

// var task = $("#task").val();
// if(task=='')
// {
	
// 	$("#error_task").html('Required!');
// }
// // var datepicker1 = $("#datepicker1").val();
// // if(datepicker1=='')
// // {
	
// // 	$("#error_datepicker1").html('Required!');
// // }

// var work_completion_date = $("#work_completion_date").val();
// if(work_completion_date=='')
// {
//     $("#error_datepicker1").html('Required!');
// }


// if(your_name=='' || business_loc=='' ||  delegate_to=='' ||  task=='' || work_completion_date=='')
// {
	
// 	return false;
// }

// });
});
</script> -->
 <script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
 <!-- <script>
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
    todayHighlight:true,
    format: 'dd/mm/yyyy'
    
    
});

                $("#datepicker1").datepicker({
					orientation: 'bottom',
					todayHighlight:true,
					dateFormat: 'dd/mm/yyyy'
				});
			//	$('.datepicker').datepicker({todayHighlight:true});
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
					
                })

            });</script> -->


            <script>
                $(document).ready(function() {

    $('.select2').select2();
    $('.select3').select2();

    $("#loginForm").on("submit", function(e){

        var isValid = true;

        var your_name = $("#your_name").val();
        var business_loc = $("#business_loc").val();
        var delegate_to = $("#delegate_to").val();
        var task = $("#task").val();
        var work_completion_date = $("#work_completion_date").val();

        $(".error_msg").html("");

        if(your_name == ''){
            $("#error_your_name").html("Required!");
            isValid = false;
        }

        if(business_loc == ''){
            $("#error_business_loc").html("Required!");
            isValid = false;
        }

        if(!delegate_to || delegate_to.length == 0){
            $("#error_delegate_to").html("Required!");
            isValid = false;
        }

        if(task == ''){
            $("#error_task").html("Required!");
            isValid = false;
        }

        if(work_completion_date == ''){
            $("#error_datepicker1").html("Required!");
            isValid = false;
        }

        if(!isValid){
            e.preventDefault(); // STOP SUBMIT
            return false;
        }

        $("#pageloader").fadeIn();
    });

});
            </script>
    </body>
</html>