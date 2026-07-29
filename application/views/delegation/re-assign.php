<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Redelegate Form</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
		<link href="<?php echo assets_url;?>plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

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
                <div class="row" style="padding-top:100px">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="btn-group pull-right">
                              
                            </div>
                            <h4 class="page-title">RE ASSIGN THIS TASK</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->


                <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">
								<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
								<?php
								$id = $this->uri->segment(3);
								$this->db->select('*')->from('delegation_task')->where('id',$id);
								$query = $this->db->get();
								if($query->num_rows() > 0) {
								$res = $query->result();
								foreach($res as $rows);
								$yourname = $rows->yourname;
								$task = $rows->task;
								$delegated_date = date('m-d-Y',strtotime($rows->delegated_date));
								
    								if($rows->image <> '') {
    								$picture = '<img src="'.sfdocument.'delegation/'.$rows->image.'" style="width:100px;">';
    								} else {
    								    $picture = '';
    								}
								} else {
								$yourname = '';
								$task = '';
								$delegated_date = '';
								}
								?>
								
                                   <form method="post" id="loginForm" action="<?php echo page_url;?>Delegation/re_assign_task/" enctype="multipart/form-data">
								    <div id="pageloader">
                                    <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
                                    </div>
										<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">YOUR NAME</label>
														 <span style="color:red;">*</span>
														 <span id="error_your_name" style="color:red;"></span>
														 <select class="form-control select3" id="your_name" name="your_name">
													
													<?php
													$user_id =$this->session->userdata['logged_in']['user_id'];
													$query = $this->db->select('a.assigned_to, a.status, b.user_id, b.title, b.first_name, b.last_name')->from('delegation_master a')->join('system_users b','a.assigned_to=b.user_id','left')->where('b.user_id',$user_id)->get();
													
												if($query->num_rows() > 0) {
													foreach($query->result() as $users);?>
													<option value="<?php echo $users->assigned_to;?>" <?php if($users->assigned_to==$rows->yourname){echo "selected";}?>><?php echo strtoupper($users->title." ".$users->first_name." ".$users->last_name);?></option>
													
													<?php } ?>
												</select>
													</div>
												</div>
												<div class="col-md-4" style="display:none;">
													<div class="form-group">
														 <label for="field-2" class="control-label">DEPARTMENT DELEGATED TO </label>
														 <span style="color:red;">*</span>
														 <span id="error_department" style="color:red;"></span>
														 <select class="form-control" id="department" name="department">
													<option value="0">--SELECT DEPARTMENT--</option>
													 <?php //$query = $this->db->select('department_id,status,department')->from('departments')->where('assign_delegation','1')->where('status','1')->get();
													// if($query->num_rows() > 0) {
													//foreach($query->result() as $department){?>
													<!-- <option value="<?php //echo $department->department_id;?>" <?php //if($department->department_id==$rows->department_id){echo "selected";}?>><?php //echo strtoupper($department->department);?></option> -->
													<?php //} }?>
												</select>
													 <script type="text/javascript">
											
													$("#department").change(function(){
													var department=$("#department").val();
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Delegation/user_list_new",
													data:"department="+department,
													success:function(data){
													$("#delegate_to").html(data);
													}
													});
													});
											
												</script>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">DELEGATED TO</label>
														 <span style="color:red;">*</span>
														 
														 <span id="error_delegate_to" style="color:red;"></span>
														 <select class="form-control select2" id="delegate_to" name="delegate_to">
													<?php $query = $this->db->select('a.user_id, a.title, a.first_name, a.last_name, a.department_id, a.user_status,b.department')->from('system_users a')->join('departments b','a.department_id=b.department_id')->where('a.user_status','1')->get();
													if($query->num_rows() > 0) {
													foreach($query->result() as $users){?>
													<option value="<?php echo $users->user_id;?>" <?php if($rows->delegate_to==$users->user_id){?> selected <?php } ?>><?php echo strtoupper($users->first_name." ".$users->last_name)."-".$users->department;?></option>
													<?php } }?>
													
												</select>
													</div>
												</div>
												<div class="col-md-6">
												<div class="form-group">
												<label for="field-2" class="control-label">WORK DELEGATED</label>
												<span style="color:red;">*</span>
												<span id="error_task" style="color:red;"></span>
												<input type="text" class="form-control" name="task" id="task" value="<?php echo $task;?>">
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
												<label for="field-2" class="control-label">UPLOAD IMAGE(IF ANY)</label>
												
												<input type="file" class="form-control" name="image" id="image" value="">
												<input type="hidden" name="oldfile" value="<?php echo $rows->image;?>">
												
											<?php echo $picture;?>
												</div>
											</div>
											
											
											 <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">WORK PRIORITY</label>
														<span style="color:red;">*</span>
														<span id="error_datepicker1" style="color:red;"></span>
                                                        <input type="text" id="datepicker1" name="work_completion_date" class="form-control" value="<?php echo $delegated_date;?>">
                                                    </div>
                                                </div>
										<div class="col-md-9"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" id="reassign_save" class="btn btn-success" value="RE ASSIGN">
											</div>
										</div>
									
									</form>
                                   

                                </div>

                            </div>
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
		<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
		<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script language="javascript" type="text/javascript">   
$(document).ready(function() {
	$('.select2').select2({ });
$('.select3').select2({ });
$('.select4').select2({ });

});


$("#reassign_save").click(function() {
	var your_name= $("#your_name").val();
if(your_name=='')
{
	$("#error_your_name").html('Required!');
} else {
    $("#error_your_name").html('');
}
var department = $("#department").val();
if(department=='')
{
	$("#error_department").html('Required!');
} else {
    $("#error_department").html('');
}
var delegate_to = $("#delegate_to").val();
if(delegate_to=='')
{
	$("#error_delegate_to").html('Required!');
} else {
    $("#error_delegate_to").html('');
}

var task = $("#task").val();
if(task=='')
{
	
	$("#error_task").html('Required!');
} else {
    $("#error_task").html('');
}
var datepicker1 = $("#datepicker1").val();
if(datepicker1=='')
{
	
	$("#error_datepicker1").html('Required!');
} else {
    $("#error_datepicker1").html('');
}


if(your_name==''|| department=='' || delegate_to=='' ||  task=='' || datepicker1=='')
{
	
	return false;
}

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

                 $("#datepicker1").datepicker({
					orientation: 'bottom',
					todayHighlight:true,
		        	startDate: '-0m'
				});
      
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
                })

            });</script>
            <script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
			</body>
</html>