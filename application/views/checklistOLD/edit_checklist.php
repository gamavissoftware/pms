<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Checklist</title>

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
		<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" rel="stylesheet"/>
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
	<?php $this->load->view('common/nav-menu.php');?>


        </header>

        <!-- End Navigation Bar-->





        <div class="wrapper">

            <div class="container">



                <!-- Page-Title -->

                <div class="row">

                    <div class="col-sm-12">

                        <div class="page-title-box">

                            <div class="btn-group pull-right">

                              

                            </div>

                            <h4 class="page-title">Edit Checklist</h4>

                        </div>

                    </div>

                </div>

                <!-- end page title end breadcrumb -->





                <div class="row" style="padding-top:50px;">

                    <div class="col-xs-12">

                        <div class="card-box">



                            <div class="row">

                                <div class="col-sm-12 col-xs-12 col-md-12">



								<?php

								$id = $this->uri->segment(3);

								$this->db->select('*')->from('compliance_task_report')->where('task_id',$id);

								$query = $this->db->get();

                                    if($query->num_rows() > 0) {
								$res = $query->result();

								foreach($res as $rows);
								    $task = $rows->task;
                                    $task_id = $rows->task_id;   
                                    $company_id = $rows->company_id;
								    $video_link = $rows->video_link;
								    $first_day = $rows->first_day;
								    $tat_id = $rows->tat_id;
								    $second_day = $rows->second_day;
								    $first_date = $rows->first_date;
								    $last_date = $rows->last_date;
								    $status = $rows->status;
                                } else {
                                    $task = '';
                                    $task_id= '';
                                    $company_id = '';
                                    $video_link = '';
                                    $first_day = '';
                                    $tat_id = '';
                                    $second_day = '';
                                    $first_date = '';
                                    $last_date = '';
                                    $status = '';
                                }
								?>

								 <form method="post" action="<?php echo page_url;?>Checklist/update_checklist/<?php echo $task_id;?>" onsubmit="return validate()";>

										<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">BUSINESS LOCATION</label>
														 <span id="error_business_loc" style="color:red;">*</span>
														 <select class="form-control" id="business_loc" name="business_loc" required>
													<option value="">--SELECT BUSINESS LOCATION--</option>
													<?php 
													$this->db->select('a.company_name, a.business_loc_id,a.state_id, a.city_id,b. 	state_id,b.state_name,c.city_id,c.city_name')->from('business_location a')->join('states b','a.state_id=b.state_id','left')->join('cities c','a.city_id=c.city_id','left')->where('business_loc_status','1');
													$this->db->order_by('a.company_name','asc');
													$query = $this->db->get();
													
													if($query->num_rows() > 0) {
													$res = $query->result();
													foreach($res as $row){
													?>
													<option value="<?php echo $row->business_loc_id;?>" <?php if($rows->company_id==$row->business_loc_id){echo "selected";}?>><?php echo strtoupper($row->company_name);?>(<?php echo strtoupper($row->state_name);?>, <?php echo strtoupper($row->city_name);?>)</option>
											<?php } }?>		
												</select>
												<script type="text/javascript">
											
													$("#business_loc").change(function(){
													var business_loc=$("#business_loc").val();
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Checklist/user_list",
													data:"business_loc="+business_loc,
													success:function(data){
													$("#user_id").html(data);
													}
													});
													
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Checklist/user_list",
													data:"business_loc="+business_loc,
													success:function(data){
													$("#reporting_head").html(data);
													}
													});
													});
											
												</script>
												
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">USER NAME</label>
														 <span id="error_user_id" style="color:red;">*</span>
														 <select class="form-control select3" id="user_id" name="user_id" required>
													<?php 
														 $qry = $this->db->select('user_id, business_location, first_name, last_name')->from('system_users')->where('business_location',$company_id)->get();
											if($qry->num_rows() > 0) {			 
											foreach($qry->result() as $assignedto){
														 ?>
													<option value="<?php echo $assignedto->user_id;?>" <?php if($rows->user_id==$assignedto->user_id){echo "selected";}?>><?php echo strtoupper($assignedto->first_name." ".$assignedto->last_name);?></option>
														 <?php } }?>
													
												</select>
													</div>
												</div>
                                                <div class="col-md-6">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">CHECKLIST POINT</label>

														<span id="error_task" style="color:red;">*</span>

                                                        <input type="text" class="form-control" id="task" name="task" placeholder="" value="<?php echo $task;?>" style="text-transform:uppercase" required>

                                                    </div>

                                                </div>
												
												<div class="col-md-6">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">VIDEO LINK</label>

														<input type="text" class="form-control" id="video_link" name="video_link" placeholder="" value="<?php echo $video_link;?>" style="text-transform:uppercase">

                                                    </div>

                                                </div>

												<div class="col-md-3">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">Turnaround Time</label>

														<span id="error_turnaroundtime" style="color:red;">*</span>

														<select class="form-control" id="turnaroundtime" name="turnaroundtime" style="text-transform:uppercase" required>

														<option value="">--Select Turnaround Time--</option>
														<?php 
														$query = $this->db->select('id,turnaroundtime, status')->from('compliance_tat')->where('status','1')->get();

                                            if($query->num_rows() > 0) {
														foreach($query->result() as $turnaroundtime){
														?>
														<option value="<?php echo $turnaroundtime->id;?>" <?php if($rows->tat_id==$turnaroundtime->id){echo "selected";}?>><?php echo strtoupper($turnaroundtime->turnaroundtime);?></option>
														<?php } }?>

														</select>

                                                    </div>

                                                </div>
												
												<script>
													function shownext(){
														
										$('#firstdayrow').show();
										$('#seconddayrow').show();
										$('#firstdayrow').hide();
										$('#seconddayrow').hide();
										$('#firstdaterow').show();
										$('#seconddaterow').show();
										$('#firstdaterow').hide();
										$('#seconddaterow').hide();
										var turnaroundtime = $('#turnaroundtime').val();
										if(turnaroundtime=='4'){
										$('#firstdayrow').show();
										$('#seconddayrow').show();
										}else if(turnaroundtime=='5'){
										$('#firstdaterow').show();
										$('#seconddaterow').show();
										$('#firstdayrow').hide();
										$('#seconddayrow').hide();
										}else{
										$('#firstdaterow').hide();
										$('#seconddaterow').hide();
										$('#firstdayrow').hide();
										$('#seconddayrow').hide();
														}
													}
												</script>
												<div class="col-md-3" id="firstdayrow" <?php if($tat_id=='4'){}else{?>style="display:none"<?php }?>>
												<div class="form-group">
												<label>FIRST DAY</label>
												<select class="form-control" id="first_day" name="first_day" style="text-transform:uppercase">

													<option value="">--Select First Day--</option>
													<option value="monday" <?php if($first_day=='monday'){echo "selected";}?>>Monday</option>
													<option value="tuesday" <?php if($first_day=='tuesday'){echo "selected";}?>>Tuesday</option>
													<option value="wednesday" <?php if($first_day=='wednesday'){echo "selected";}?>>Wednesday</option>
													<option value="thurday" <?php if($first_day=='thurday'){echo "selected";}?>>Thursday</option>
													<option value="friday" <?php if($first_day=='friday'){echo "selected";}?>>Friday</option>
													<option value="saturday" <?php if($first_day=='saturday'){echo "selected";}?>>Saturday</option>
												</select>
												</div>
												</div>
												<div class="col-md-3" id="seconddayrow" <?php if($tat_id=='4'){}else{?>style="display:none"<?php }?>>
												<div class="form-group">
												<label>SECOND DAY</label>
												<select class="form-control" id="second_day" name="second_day" style="text-transform:uppercase">
												<option value="">--Select Second Day--</option>
													<option value="monday" <?php if($second_day=='monday'){echo "selected";}?>>Monday</option>
													<option value="tuesday" <?php if($second_day=='tuesday'){echo "selected";}?>>Tuesday</option>
													<option value="wednesday" <?php if($second_day=='wednesday'){echo "selected";}?>>Wednesday</option>
													<option value="thurday" <?php if($second_day=='thurday'){echo "selected";}?>>Thursday</option>
													<option value="friday" <?php if($second_day=='friday'){echo "selected";}?>>Friday</option>
													<option value="saturday" <?php if($second_day=='saturday'){echo "selected";}?>>Saturday</option>
												</select>
												</div>
												</div>
												
												<div class="col-md-3" id="firstdaterow" <?php if($tat_id=='5'){}else{?>style="display:none"<?php }?>>
												<div class="form-group">
												<label>FIRST DATE</label>
												<select class="form-control" id="first_date" name="first_date" style="text-transform:uppercase">

													<option value="">--Select First Date--</option>
													<?php 
													for($i=1; $i<=31; $i++){
													?>
													<option value="<?php echo $i;?>" <?php if($first_date==$i){echo "selected";}?>><?php echo $i;?></option>
													<?php }?>
												</select>
												</div>
												</div>
												<div class="col-md-3" id="seconddaterow" <?php if($tat_id=='5'){}else{?>style="display:none"<?php }?>>
												<div class="form-group">
												<label>SECOND DATE</label>
												<select class="form-control" id="second_date" name="second_date" style="text-transform:uppercase">
												<option value="">--Select Second Date--</option>
													<?php 
													for($i=1; $i<=31; $i++){
													?>
													<option value="<?php echo $i;?>" <?php if($last_date==$i){echo "selected";}?>><?php echo $i;?></option>
													<?php }?>
												</select>
												</div>
												</div>
												<?php $qry = $this->db->select('id, task_id,dateforemail')->from('compliance_set_date')->where('task_id',$this->uri->segment(3))->get();
												if($qry->num_rows()>0){
													foreach($qry->result() as $duedatesss);
													$duedate = $duedatesss->dateforemail;
												}else{
													$duedate = date('d-m-Y');
												}
												?>
												<div class="col-md-3">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">Due Date</label>

														<span id="error_duedate" style="color:red;">*</span>
													
                                                        <input type="text" class="form-control datepicker" id="duedate" name="duedate" value="<?php echo date('m-d-Y',strtotime($duedate));?>" placeholder="Due Date" style="text-transform:uppercase" required>

                                                    </div>

                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Status</label>
                                                        <select class="form-control" name="status" id="status">
                                                            <option value="1" <?php if($status=='1'){echo "selected";}?>>Active</option>
                                                            <option value="0" <?php if($status=='0'){echo "selected";}?>>Not Active</option>
                                                        </select>
                                                    </div>
                                                </div>
										
										<div class="col-md-6"></div>
										<div class="col-md-3">

											<div class="form-group pull-right" style="padding-top:24px;">

												<label>&nbsp;</label>

												<input type="submit" class="btn btn-success" id="saves" value="Update">

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

                <footer class="footer text-right">

                    <div class="container">

                        <div class="row">

                            <div class="col-xs-12 text-center">

                                © <?php echo date('Y');?>. All rights reserved.

                            </div>

                        </div>

                    </div>

                </footer>

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

<script> $(document).ready(function() {
 var date = new Date();
date.setDate(date.getDate());
   
$('.datepicker').datepicker({
    todayHighlight:true,
    dateFormat: 'yyyy-mm-dd'
    
    
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

<script language="javascript" type="text/javascript">   


$(document).ready(function() {

$("#saves").click(function() {

var turnaroundtime = $("#turnaroundtime").val();
if(turnaroundtime=='')
{
$("#error_turnaroundtime").html('Required!');
}
var startdate = $("#startdate").val();
if(startdate=='')
{
$("#error_startdate").html('Required!');
}

var time_interval = $("#time_interval").val();
if(time_interval=='')
{
$("#error_time_interval").html('Required!');
}
var frequency = $("#frequency").val();
if(frequency=='')
{
$("#error_frequency").html('Required!');
}

var duedate = $("#duedate").val();
if(duedate=='')
{
$("#error_duedate").html('Required!');
}

var status = $("#status").val();

if(status=='')

{

	$("#error_status").html('Required!');

}



if(turnaroundtime=='' || startdate=='' || time_interval=='' || frequency=='' ||  status=='')

{

	

	return false;

}



});

});

function validate()

{

	 $("#saves").attr('disabled',false);

	$("#saves").val('Submit');



$("#form :input").attr('required',false);



var isValid=0;

$("#form .mand").each(function() {

var element = $(this).val();

if (element=="") {



isValid=1;

}





});

 

 

 if(isValid==0)

 {

	$("#saves").attr('disabled',true);

	$("#saves").val('Please Wait..');

     return true;

	 

 }else

 {

	 $("#saves").attr('disabled',false);

	$("#saves").val('Submit');

     alert('All Fields are mandatory');

     return false;

 }

    

}

</script>

    </body>
</html>