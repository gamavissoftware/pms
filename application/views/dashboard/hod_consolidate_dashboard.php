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
				font-weight:bold;
				
			}
			table.manglesh tbody td {
				font-size:14px;
				
			}
			table.taskdetail thead th {
				background: yellow;
				color:#000;
				font-weight:bold;
			}
			h4{
				font-size:15px;
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
                    <div class="col-sm-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right" style="margin-top:30px">
						 </div>
                           
                            <h4 class="page-title text-center">HOD Consolidate Dashboard</h4>
                        </div>
                    </div>
					<?php 
					$user_id =$this->session->userdata['logged_in']['user_id'];
					$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','27')->where('submodule_access','1')->get();
					if($qry->num_rows()>0){
					?>
					<div class="col-lg-2 col-md-2 col-xs-12 col-sm-12" id="delegationtasklink">
                        <div class="card-box">
                            <div class="text-center">
                            <div class="text-center">
                                 <div class="row">
								<div class="col-md-12"><h4>Delegated Task</h4>
								</div>
							</div>
                            </div>
                            </div>
                        </div>
					</div>
				<?php }?>
						<script>
						$(document).ready(function(){
						 $("#delegationtasklink").click(function(){
							$("#delegationtask").show();
							$('html,body').animate({scrollTop: $("#delegationtask").offset().top},'slow');
						  });
						});
						</script>
						<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','33')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
					<div class="col-lg-2 col-md-2 col-xs-12 col-sm-12" id="localconveyancelink">
                       <div class="card-box">
                            <div class="text-center">
                            <div class="text-center">
							<div class="row">
							<div class="col-md-12"><h4>Local Conveyance</h4>
							</div>
							</div>
                            </div>
                            </div>
							</div>
					</div>
					<?php }?>
							
<script>
$(document).ready(function(){
 $("#localconveyancelink").click(function(){
    $("#localconveyance").show();
	$('html,body').animate({scrollTop: $("#localconveyance").offset().top},'slow');
  });
});
</script>
					<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','37')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
					<div class="col-lg-2 col-md-2 col-xs-12 col-sm-12" id="tourconveyancelink">
                        <div class="card-box">
                            <div class="text-center">
                                
							<div class="text-center">
                                 <div class="row">
								<div class="col-md-12"><h4>Tour Conveyance</h4>
								</div>
							</div>
                            </div>
                            </div>
                        </div>
							</div>
									<?php }?>	
<script>
$(document).ready(function(){
 $("#tourconveyancelink").click(function(){
    $("#tourconveyance").show();
	$('html,body').animate({scrollTop: $("#tourconveyance").offset().top},'slow');
  });
});
</script>
								<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','165')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
							<div class="col-lg-2 col-md-2 col-xs-12 col-sm-12" id="visitschedulelink">
                        <div class="card-box">
                            <div class="text-center">
                                
							<div class="text-center">
                                 <div class="row">
								<div class="col-md-12"><h4>Visit Scheduled</h4>
								</div>
							</div>
                            </div>
                            </div>
                        </div>
									</div><?php }?>
<script>
$(document).ready(function(){
 $("#visitschedulelink").click(function(){
    $("#visitschedule").show();
	$('html,body').animate({scrollTop: $("#visitschedule").offset().top},'slow');
  });
});
</script>
							
							<div class="col-lg-2 col-md-2 col-xs-12 col-sm-12" id="salesdailyupdateslink">
                        <div class="card-box">
                            <div class="text-center">
                            <div class="text-center">
                                 <div class="row">
								<div class="col-md-12"><h4 style="font-size: 14px;">Fields Sales Daily Updates</h4>
								</div>
							</div>
                            </div>
                            </div>
                        </div>
							</div>
<script>
$(document).ready(function(){
 $("#salesdailyupdateslink").click(function(){
    $("#salesdailyupdates").show();
	$('html,body').animate({scrollTop: $("#salesdailyupdates").offset().top},'slow');
  });
});
</script>
							
							<div class="col-lg-2 col-md-2 col-xs-12 col-sm-12" id="leaveapplicationlink">
                        <div class="card-box">
                            <div class="text-center">
                                
							<div class="text-center">
                                 <div class="row">
								<div class="col-md-12"><h4>Leave Application</h4>
								</div>
							</div>
                            </div>
                            </div>
                        </div>
							</div>
<script>
$(document).ready(function(){
$("#leaveapplicationlink").click(function(){
$("#leaveapplication").show();
$('html,body').animate({scrollTop: $("#leaveapplication").offset().top},'slow');
});
});
</script>	
							
							<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','6')->where('submoduleid','93')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?> 	
						<div class="col-lg-2 col-md-2 col-xs-12 col-sm-12" id="accounthelpticketlink">
                        <div class="card-box">
                            <div class="text-center">
                                
							<div class="text-center">
                                 <div class="row">
								<div class="col-md-12"><h4>Account Help Ticket</h4>
								</div>
							</div>
                            </div>
                            </div>
                        </div></a>
									</div><?php }?>
							<script>
$(document).ready(function(){
$("#accounthelpticketlink").click(function(){
$("#accounthelpticket").show();
$('html,body').animate({scrollTop: $("#accounthelpticket").offset().top},'slow');
});
});
</script>	
	<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','6')->where('submoduleid','94')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>  
							
							<div class="col-lg-2 col-md-2 col-xs-12 col-sm-12" id="servicehelpticketlink">
							<div class="card-box">
                            <div class="text-center">
                             <div class="text-center">
                                 <div class="row">
								<div class="col-md-12"><h4>Service Help Ticket</h4>
								</div>
							</div>
                            </div>
                            </div>
							</div>
									</div><?php }?>
							<script>
$(document).ready(function(){
$("#servicehelpticketlink").click(function(){
$("#servicehelpticket").show();
$('html,body').animate({scrollTop: $("#servicehelpticket").offset().top},'slow');
});
});
</script>		<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','6')->where('submoduleid','95')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>  
							<div class="col-lg-2 col-md-2 col-xs-12 col-sm-12" id="eahelpticketlink">
							<div class="card-box">
                            <div class="text-center">
                             <div class="text-center">
                                 <div class="row">
								<div class="col-md-12"><h4>EA Help Ticket</h4>
								</div>
							</div>
                            </div>
                            </div>
							</div>
									</div><?php }?>
							
							<script>
$(document).ready(function(){
$("#eahelpticketlink").click(function(){
$("#eahelpticket").show();
$('html,body').animate({scrollTop: $("#eahelpticket").offset().top},'slow');
});
});
</script>	
								<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','6')->where('submoduleid','96')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>  
							<div class="col-lg-2 col-md-2 col-xs-12 col-sm-12" id="ithelpticketlink" style="display:none">
							<div class="card-box">
                            <div class="text-center">
                             <div class="text-center">
                                 <div class="row">
								<div class="col-md-12"><h4>IT Help Ticket</h4>
								</div>
							</div>
                            </div>
                            </div>
							</div>
									</div><?php }?>
							
<script>
$(document).ready(function(){
$("#ithelpticketlink").click(function(){
$("#ithelpticket").show();
$('html,body').animate({scrollTop: $("#ithelpticket").offset().top},'slow');
});
});
</script>
<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','6')->where('submoduleid','100')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>  
							<div class="col-lg-2 col-md-2 col-xs-12 col-sm-12" id="maintenancehelpticketlink">
							<div class="card-box">
                            <div class="text-center">
                             <div class="text-center">
                                 <div class="row">
								<div class="col-md-12"><h4>Maintenance Help Ticket</h4>
								</div>
							</div>
                            </div>
                            </div>
							</div>
									</div><?php }?>
							<script>
							$(document).ready(function(){
							$("#maintenancehelpticketlink").click(function(){
							$("#maintenancehelpticket").show();
							$('html,body').animate({scrollTop: $("#maintenancehelpticket").offset().top},'slow');
							});
							});
							</script>
							<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','6')->where('submoduleid','90')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
							<div class="col-lg-2 col-md-2 col-xs-12 col-sm-12" id="helpticketforgmlink">
							<div class="card-box">
                            <div class="text-center">
                             <div class="text-center">
                                 <div class="row">
								<div class="col-md-12"><h4>Help Ticket for GM</h4>
								</div>
							</div>
                            </div>
                            </div>
							</div>
							</div>
									<?php }?>
							<script>
							$(document).ready(function(){
							$("#helpticketforgmlink").click(function(){
							$("#helpticketforgm").show();
							$('html,body').animate({scrollTop: $("#helpticketforgm").offset().top},'slow');
							});
							});
							</script>
							<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','147')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
							<div class="col-lg-2 col-md-2 col-xs-12 col-sm-12" id="sampletestingreportlink">
							<div class="card-box">
                            <div class="text-center">
                             <div class="text-center">
                                 <div class="row">
								<div class="col-md-12"><h4>Sample Testing Report</h4>
								</div>
							</div>
                            </div>
                            </div>
							</div>
									</div><?php }?>
							<script>
							$(document).ready(function(){
							$("#sampletestingreportlink").click(function(){
							$("#sampletestingreport").show();
							$('html,body').animate({scrollTop: $("#sampletestingreport").offset().top},'slow');
							});
							});
							</script>
							<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','50')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
							<div class="col-lg-3 col-md-3 col-xs-12 col-sm-12" id="dispatchfortommorowlink">
							<div class="card-box">
                            <div class="text-center">
                             <div class="text-center">
                                 <div class="row">
								<div class="col-md-12"><h4>Dispatch for Tomorrow Order List
</h4>
								</div>
							</div>
                            </div>
                            </div>
							</div>
									</div><?php }?>
							<script>
$(document).ready(function(){
$("#dispatchfortommorowlink").click(function(){
$("#dispatchfortommorow").show();
$('html,body').animate({scrollTop: $("#dispatchfortommorow").offset().top},'slow');
});
});
</script>
							
							 <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','161')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
							<div class="col-lg-3 col-md-3 col-xs-12 col-sm-12" id="serviceengineervisitlink">
							<div class="card-box">
                            <div class="text-center">
                             <div class="text-center">
                                 <div class="row">
								<div class="col-md-12"><h4>Service Engineer Visit Report
</h4>
								</div>
							</div>
                            </div>
                            </div>
							</div>
									</div><?php }?>
							<script>
							$(document).ready(function(){
							$("#serviceengineervisitlink").click(function(){
							$("#serviceengineervisit").show();
							$('html,body').animate({scrollTop: $("#serviceengineervisit").offset().top},'slow');
							});
							});
							</script>
							<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','49')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
							<div class="col-lg-3 col-md-3 col-xs-12 col-sm-12" id="readyordercompleteorderlink">
							<div class="card-box">
                            <div class="text-center">
                             <div class="text-center">
                                 <div class="row">
								<div class="col-md-12"><h4>Ready Orders Complete Orders

</h4>
								</div>
							</div>
                            </div>
                            </div>
							</div>
									</div><?php }?>
							<script>
							$(document).ready(function(){
							$("#readyordercompleteorderlink").click(function(){
							$("#readyordercompleteorder").show();
							$('html,body').animate({scrollTop: $("#readyordercompleteorder").offset().top},'slow');
							});
							});
							</script>
								
                </div>
                <!-- end page title end breadcrumb -->
	<?php echo $this->session->flashdata('message'); ?>
	<div class="row" id="delegationtask" style="display:none">
		  <div class="col-sm-12">
                        <div class="card-box table-responsive">
						<h4 class="text-center" style="font-weight:bold;">Delegated Task</h4><hr>
                             <table id="example16" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th>SR NO.</th>
									<th>TIMESTAMP</th>
                                    <th>DELEGATED BY</th>
                                    <th>CASE NO</th>
                                    <th>WORK DELEGATED</th>
									<th>ATTACHMENT</th>
                                    <th>WORK PRIORITY DATE</th>
									<th>2ND DATE</th>
									<th>3RD DATE</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
		<div class="row" id="localconveyance" style="display:none">
		  <div class="col-sm-12">
                        <div class="card-box table-responsive">
						<h4 class="text-center" style="font-weight:bold;">Local Conveyance</h4><hr>
                             <table id="example" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th>SR NO.</th>
									<th>PERSON NAME</th>
									<th>DATE</th>
                                    <th>FROM</th>
                                    <th>PROCEED TO</th>
                                    <th>MODE</th>
                                    <th>VEHICLE TYPE</th>
                                    <th>START READING</th>
									<th>END READING</th>
									<th>RATE (INR)</th>
									<th>AMOUNT (INR)</th>
									<th>HOD STATUS</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
				
				<div class="row" id="tourconveyance" style="display:none">
		  <div class="col-sm-12">
                        <div class="card-box table-responsive">
						<h4 class="text-center" style="font-weight:bold;">Tour Conveyance</h4><hr>
                             <table id="example1" class="table table-striped table-bordered manglesh">
                                <thead>
                               <tr>
                                    <th>SR NO.</th>
									<th>EMPLOYEE NAME</th>
                                    <th>PURPOSE OF TRIP</th>
                                    <th>TOUR START DATE</th>
                                    <th>TOUR END DATE</th>
                                    <th>DEPARTURE TIME</th>
									<th>ARRIVAL TIME</th>
									<th>TOTAL DAYS</th>
									<th>ATTACHED BILLS</th>
									<th>TRAVEL CONVEYANCE BREIF</th>
									<th>HOD STATUS</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
 
 <div class="row" id="visitschedule" style="display:none">
		  <div class="col-sm-12">
                        <div class="card-box table-responsive">
						<h4 class="text-center" style="font-weight:bold;">Visit Scheduled</h4><hr>
                              <table id="example2" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th>SR NO.</th>
									<th>CUSTOMER NAME</th>
                                    <th>CONTACT NUMBER</th>
                                    <th>COMPANY NAME</th>
                                    <th>ADDRESS</th>
                                    <th>VISIT SCHEDULE DATE</th>
                                    <th>MEETING TIME</th>
									<th>WHO WILL VISIT</th>
									<th>ADDED BY</th>
									<th>ADDED ON</th>
									<!--<th>UPDATE STATUS</th>
									<th>EDIT</th>-->
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
				
				<div class="row" id="salesdailyupdates" style="display:none">
		  <div class="col-sm-12">
                        <div class="card-box table-responsive">
						<h4 class="text-center" style="font-weight:bold;">Fields Sales Daily Updates</h4><hr>
                               <table id="example3" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th>SR NO.</th>
									<th>NAME</th>
									<th>CUSTOMER NAME</th>
                                    <th>CONTACT NUMBER</th>
                                    <th>COMPANY NAME</th>
                                    <th>VISIT NO</th>
                                    <th>ACTION TAKEN</th>
                                    <th>STAGE</th>
									<th>VALUE</th>
									<th>MACHINE NAME</th>
									<th>NEXT ACTION PLAN</th>
									<th>DATE OF NEXT PLAN</th>
									<th>VISITING CARD</th>
									<th>REMARKS</th>
									<th>ADDED ON</th>
									<!--<th>EDIT</th>-->
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
				
				<div class="row" id="leaveapplication" style="display:none">
		  <div class="col-sm-12">
                        <div class="card-box table-responsive">
						<h4 class="text-center" style="font-weight:bold;">Leave Application</h4><hr>
                               <table id="example4" class="table table-striped table-bordered manglesh">
                                <thead>
                                 <tr>
                                    <th>SR NO.</th>
									<th>TIMESTAMP</th>
									<th>EMPLOYEE NAME</th>
									<th>DEPARTMENT</th>
									<th>DESIGNATION</th>
									<th>DATE</th>
                                    <th>REASON</th>
                                    <th>TOTAL DAYS</th>
                                    <th>PHONE NO</th>
									<th>LEAVE TYPE</th>
									<th>LEAVE FOR</th>
									<th>HOD STATUS</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
				
				<div class="row" id="accounthelpticket" style="display:none">
		  <div class="col-sm-12">
                        <div class="card-box table-responsive">
						<h4 class="text-center" style="font-weight:bold;">Account Help Ticket</h4><hr>
                                <table id="example5" class="table table-striped table-bordered manglesh">
                                <thead>
                                <?php 
								$formid = "18";
								$Q2 = $this->db->select('tatappl,form_type, ref_no')->from('dynamic_forms')->where('id',$formid)->where('user_id',$user_id)->get();
								if($Q2->num_rows()>0){
								foreach($Q2->result() as $forminfo);
									$tatapp=$forminfo->tatappl;
								$que=$this->db->select('form_id, form_label, field_type')->from('master_dynamic_fields')->where('form_id',$formid)->get();
									if($que->num_rows()>0){
										foreach($que->result() as $row){
											?>
											<th><?php echo strtoupper($row->form_label);?></th>
											<?php
											}
											?>
												
											<th>TIMESTAMP</th>
											<?php
											if($tatapp==1)
											{
											?>
											<th>PLANNED DATE</th>
											<th>TOTAL DAYS</th>
											<?php
											}
											?>
											<?php if($forminfo->ref_no=='1'){
											    ?>
											  <th>REF NO. (REMARKS)</th>  
											  <?php 
											}?>
											
											<th>MARK AS DONE</th>
											<?php 
											if($forminfo->form_type=='2'){
												?>
												<th>REMARKS</th>
												<?php 
											}
											?>
											<th>RESPONSEID</th>
											<?php 
									}
								?>	
                                </thead>
                                <tbody>
								<?php 
								$k=1;
									$q=$this->db->select('a.id, a.responseid, a.form_id, a.formdata,b.mark_as_done, b.record_id, a.planned_date, a.ref_remarks, a.added_on')->from('dynamic_form_data a')->join('dynamic_form_data_mark_done b','a.id=b.record_id','left')->where('a.form_id',$formid)->where('a.work_status','0')->get();
									if($q->num_rows()>0){
										foreach($q->result() as $row){
								?>
								<tr>
									<?php 
									
										$json_data = json_decode($row->formdata,true);
										foreach($que->result() as $rowdata)
										{
											$name1= preg_replace('/\s+/', '', $rowdata->form_label);
											$name = strtolower($name1);
											
											$fieldtype = $rowdata->field_type;
											if($fieldtype=='5'){
												if($json_data[$name]!==''){
											$finalvalue=	"<a href='".dynamicformdata.strtolower($json_data[$name])."' download>CLICK HERE TO DOWNLOAD</a>";
												}else{
													$finalvalue=""; }	
											}else{
											$finalvalue = strtoupper($json_data[$name]);	
											}
									?>
									
									<td><?php echo $finalvalue;?></td>
									
										<?php }?>
										<td><?php  if($row->added_on=='0000-00-00 00:00:00'){}else{
											echo date('d-m-y g:i A',strtotime($row->added_on));
										};?></td>
										<?php 
										$planneddate = date('Y-m-d',strtotime($row->planned_date));
											$todaydate = date('Y-m-d');
											
											
											if($todaydate>$planneddate){
												$textcolor="color:red; font-weight:bold;";
											}else if($todaydate=$planneddate){
												$textcolor="color:blue; font-weight:bold;";
											}else{
												$textcolor="color:black; font-weight:bold;";
											}
										?>
										<?php
											if($tatapp==1)
											{
											?>
										<td><?php  if($row->planned_date=='0000-00-00 00:00:00'){}else{
											echo date('d-m-y g:i A',strtotime($row->planned_date));
										};?></td>
										
										<td style="<?php echo $textcolor;?>"><?php 
										
										$date1 = date('Y-m-d',strtotime($row->added_on));
										$date2 = date('Y-m-d');

										$diff = abs(strtotime($date2) - strtotime($date1));

										$years = floor($diff / (365*60*60*24));
										$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
										$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
										echo $days;
										?></td>
										<?php
											}
											?>
											<?php if($forminfo->ref_no=='1'){
											    ?>
										<td>
										    <?php 
										
										    if($row->ref_remarks==''){
										        ?>
										     <form method="post" action="<?php echo page_url;?>Form/update_ref_remarks/<?php echo $row->id;?>/<?php echo $formid;?>">
										            <div class="row">
										                <div class="col-md-12">
										                    <div class="form-group">
										      <label>Ref No (Remarks)</label><br>
										      <textarea class="form-control" name="ref_no" id="ref_no"></textarea>
										                        
										                    </div>
										                    
										                </div>
										                <div class="col-md-12">
										                    <input type="submit" class="btn btn-success btn-xs" value="Submit">
										                </div>
										            </div>
										        </form>
										        <?php }else{
										       echo $row->ref_remarks;
										        }?>
										</td>	    
										<?php }?>
										<td>
										    <?php if($forminfo->ref_no=='1'){
										        if($row->ref_remarks!==''){
										            echo $row->mark_as_done;
										        }else{
										            
										        }
										    }else{
										        echo $row->mark_as_done;
										    }?>
										   </td>
										<?php 
											if($forminfo->form_type=='2'){
												?>
												<td><?php 
										$query = $this->db->select('remarks')->from('dynamic_form_solution_resolution_remarks')->where('record_id',$row->id)->get();
										if($query->num_rows()>0){
											foreach($query->result() as $remarks){
												echo $remarks->remarks."<br><br>";
											}
										}
										?></td>
												<?php 
											}
											?>
											<td><?php echo $row->responseid;?></td>	
										<?php 
										}?>
										
										
								</tr>
								<?php $k++;}}?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
				
				<div class="row" id="servicehelpticket" style="display:none">
		  <div class="col-sm-12">
                        <div class="card-box table-responsive">
						<h4 class="text-center" style="font-weight:bold;">Service Help Ticket</h4><hr>
                                 <table id="example6" class="table table-striped table-bordered manglesh">
                                <thead>
                                <?php 
								$formid = "17";
								$Q2 = $this->db->select('tatappl,form_type, ref_no')->from('dynamic_forms')->where('id',$formid)->where('user_id',$user_id)->get();
								if($Q2->num_rows()>0){
								foreach($Q2->result() as $forminfo);
									$tatapp=$forminfo->tatappl;
								$que=$this->db->select('form_id, form_label, field_type')->from('master_dynamic_fields')->where('form_id',$formid)->get();
									if($que->num_rows()>0){
										foreach($que->result() as $row){
											?>
											<th><?php echo strtoupper($row->form_label);?></th>
											<?php
											}
											?>
												
											<th>TIMESTAMP</th>
											<?php
											if($tatapp==1)
											{
											?>
											<th>PLANNED DATE</th>
											<th>TOTAL DAYS</th>
											<?php
											}
											?>
											<?php if($forminfo->ref_no=='1'){
											    ?>
											  <th>REF NO. (REMARKS)</th>  
											  <?php 
											}?>
											
											<th>MARK AS DONE</th>
											<?php 
											if($forminfo->form_type=='2'){
												?>
												<th>REMARKS</th>
												<?php 
											}
											?>
											<th>RESPONSEID</th>
											<?php 
									}
								?>	
                                </thead>
                                <tbody>
								<?php 
								$k=1;
									$q=$this->db->select('a.id, a.responseid, a.form_id, a.formdata,b.mark_as_done, b.record_id, a.planned_date, a.ref_remarks, a.added_on')->from('dynamic_form_data a')->join('dynamic_form_data_mark_done b','a.id=b.record_id','left')->where('a.form_id',$formid)->where('a.work_status','0')->get();
									if($q->num_rows()>0){
										foreach($q->result() as $row){
								?>
								<tr>
									<?php 
									
										$json_data = json_decode($row->formdata,true);
										foreach($que->result() as $rowdata)
										{
											$name1= preg_replace('/\s+/', '', $rowdata->form_label);
											$name = strtolower($name1);
											
											$fieldtype = $rowdata->field_type;
											if($fieldtype=='5'){
												if($json_data[$name]!==''){
											$finalvalue=	"<a href='".dynamicformdata.strtolower($json_data[$name])."' download>CLICK HERE TO DOWNLOAD</a>";
												}else{
													$finalvalue=""; }	
											}else{
											$finalvalue = strtoupper($json_data[$name]);	
											}
									?>
									
									<td><?php echo $finalvalue;?></td>
									
										<?php }?>
										<td><?php  if($row->added_on=='0000-00-00 00:00:00'){}else{
											echo date('d-m-y g:i A',strtotime($row->added_on));
										};?></td>
										<?php 
										$planneddate = date('Y-m-d',strtotime($row->planned_date));
											$todaydate = date('Y-m-d');
											
											
											if($todaydate>$planneddate){
												$textcolor="color:red; font-weight:bold;";
											}else if($todaydate=$planneddate){
												$textcolor="color:blue; font-weight:bold;";
											}else{
												$textcolor="color:black; font-weight:bold;";
											}
										?>
										<?php
											if($tatapp==1)
											{
											?>
										<td><?php  if($row->planned_date=='0000-00-00 00:00:00'){}else{
											echo date('d-m-y g:i A',strtotime($row->planned_date));
										};?></td>
										
										<td style="<?php echo $textcolor;?>"><?php 
										
										$date1 = date('Y-m-d',strtotime($row->added_on));
										$date2 = date('Y-m-d');

										$diff = abs(strtotime($date2) - strtotime($date1));

										$years = floor($diff / (365*60*60*24));
										$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
										$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
										echo $days;
										?></td>
										<?php
											}
											?>
											<?php if($forminfo->ref_no=='1'){
											    ?>
										<td>
										    <?php 
										
										    if($row->ref_remarks==''){
										        ?>
										     <form method="post" action="<?php echo page_url;?>Form/update_ref_remarks/<?php echo $row->id;?>/<?php echo $formid;?>">
										            <div class="row">
										                <div class="col-md-12">
										                    <div class="form-group">
										      <label>Ref No (Remarks)</label><br>
										      <textarea class="form-control" name="ref_no" id="ref_no"></textarea>
										                        
										                    </div>
										                    
										                </div>
										                <div class="col-md-12">
										                    <input type="submit" class="btn btn-success btn-xs" value="Submit">
										                </div>
										            </div>
										        </form>
										        <?php }else{
										       echo $row->ref_remarks;
										        }?>
										</td>	    
										<?php }?>
										<td>
										    <?php if($forminfo->ref_no=='1'){
										        if($row->ref_remarks!==''){
										            echo $row->mark_as_done;
										        }else{
										            
										        }
										    }else{
										        echo $row->mark_as_done;
										    }?>
										   </td>
										<?php 
											if($forminfo->form_type=='2'){
												?>
												<td><?php 
										$query = $this->db->select('remarks')->from('dynamic_form_solution_resolution_remarks')->where('record_id',$row->id)->get();
										if($query->num_rows()>0){
											foreach($query->result() as $remarks){
												echo $remarks->remarks."<br><br>";
											}
										}
										?></td>
												<?php 
											}
											?>
											<td><?php echo $row->responseid;?></td>	
										<?php 
										}?>
										
										
								</tr>
								<?php $k++;}}?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
				
				<div class="row" id="eahelpticket" style="display:none">
				<div class="col-sm-12">
                        <div class="card-box table-responsive">
						<h4 class="text-center" style="font-weight:bold;">EA Help Ticket</h4><hr>
                               <table id="example7" class="table table-striped table-bordered manglesh">
                                <thead>
                                <?php 
								$formid = "1";
								$Q2 = $this->db->select('tatappl,form_type, ref_no')->from('dynamic_forms')->where('id',$formid)->where('user_id',$user_id)->get();
								if($Q2->num_rows()>0){
								foreach($Q2->result() as $forminfo);
									$tatapp=$forminfo->tatappl;
								$que=$this->db->select('form_id, form_label, field_type')->from('master_dynamic_fields')->where('form_id',$formid)->get();
									if($que->num_rows()>0){
										foreach($que->result() as $row){
											?>
											<th><?php echo strtoupper($row->form_label);?></th>
											<?php
											}
											?>
												
											<th>TIMESTAMP</th>
											<?php
											if($tatapp==1)
											{
											?>
											<th>PLANNED DATE</th>
											<th>TOTAL DAYS</th>
											<?php
											}
											?>
											<?php if($forminfo->ref_no=='1'){
											    ?>
											  <th>REF NO. (REMARKS)</th>  
											  <?php 
											}?>
											
											<th>MARK AS DONE</th>
											<?php 
											if($forminfo->form_type=='2'){
												?>
												<th>REMARKS</th>
												<?php 
											}
											?>
											<th>RESPONSEID</th>
											<?php 
									}
								?>	
                                </thead>
                                <tbody>
								<?php 
								$k=1;
									$q=$this->db->select('a.id, a.responseid, a.form_id, a.formdata,b.mark_as_done, b.record_id, a.planned_date, a.ref_remarks, a.added_on')->from('dynamic_form_data a')->join('dynamic_form_data_mark_done b','a.id=b.record_id','left')->where('a.form_id',$formid)->where('a.work_status','0')->get();
									if($q->num_rows()>0){
										foreach($q->result() as $row){
								?>
								<tr>
									<?php 
									
										$json_data = json_decode($row->formdata,true);
										foreach($que->result() as $rowdata)
										{
											$name1= preg_replace('/\s+/', '', $rowdata->form_label);
											$name = strtolower($name1);
											
											$fieldtype = $rowdata->field_type;
											if($fieldtype=='5'){
												if($json_data[$name]!==''){
											$finalvalue=	"<a href='".dynamicformdata.strtolower($json_data[$name])."' download>CLICK HERE TO DOWNLOAD</a>";
												}else{
													$finalvalue=""; }	
											}else{
											$finalvalue = strtoupper($json_data[$name]);	
											}
									?>
									
									<td><?php echo $finalvalue;?></td>
									
										<?php }?>
										<td><?php  if($row->added_on=='0000-00-00 00:00:00'){}else{
											echo date('d-m-y g:i A',strtotime($row->added_on));
										};?></td>
										<?php 
										$planneddate = date('Y-m-d',strtotime($row->planned_date));
											$todaydate = date('Y-m-d');
											
											
											if($todaydate>$planneddate){
												$textcolor="color:red; font-weight:bold;";
											}else if($todaydate=$planneddate){
												$textcolor="color:blue; font-weight:bold;";
											}else{
												$textcolor="color:black; font-weight:bold;";
											}
										?>
										<?php
											if($tatapp==1)
											{
											?>
										<td><?php  if($row->planned_date=='0000-00-00 00:00:00'){}else{
											echo date('d-m-y g:i A',strtotime($row->planned_date));
										};?></td>
										
										<td style="<?php echo $textcolor;?>"><?php 
										
										$date1 = date('Y-m-d',strtotime($row->added_on));
										$date2 = date('Y-m-d');

										$diff = abs(strtotime($date2) - strtotime($date1));

										$years = floor($diff / (365*60*60*24));
										$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
										$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
										echo $days;
										?></td>
										<?php
											}
											?>
											<?php if($forminfo->ref_no=='1'){
											    ?>
										<td>
										    <?php 
										
										    if($row->ref_remarks==''){
										        ?>
										     <form method="post" action="<?php echo page_url;?>Form/update_ref_remarks/<?php echo $row->id;?>/<?php echo $formid;?>">
										            <div class="row">
										                <div class="col-md-12">
										                    <div class="form-group">
										      <label>Ref No (Remarks)</label><br>
										      <textarea class="form-control" name="ref_no" id="ref_no"></textarea>
										                        
										                    </div>
										                    
										                </div>
										                <div class="col-md-12">
										                    <input type="submit" class="btn btn-success btn-xs" value="Submit">
										                </div>
										            </div>
										        </form>
										        <?php }else{
										       echo $row->ref_remarks;
										        }?>
										</td>	    
										<?php }?>
										<td>
										    <?php if($forminfo->ref_no=='1'){
										        if($row->ref_remarks!==''){
										            echo $row->mark_as_done;
										        }else{
										            
										        }
										    }else{
										        echo $row->mark_as_done;
										    }?>
										   </td>
										<?php 
											if($forminfo->form_type=='2'){
												?>
												<td><?php 
										$query = $this->db->select('remarks')->from('dynamic_form_solution_resolution_remarks')->where('record_id',$row->id)->get();
										if($query->num_rows()>0){
											foreach($query->result() as $remarks){
												echo $remarks->remarks."<br><br>";
											}
										}
										?></td>
												<?php 
											}
											?>
											<td><?php echo $row->responseid;?></td>	
										<?php 
										}?>
										
										
								</tr>
								<?php $k++;}}?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
				
				<div class="row" id="ithelpticket" style="display:none">
				<div class="col-sm-12">
                        <div class="card-box table-responsive">
						<h4 class="text-center" style="font-weight:bold;">IT Help Ticket</h4><hr>
                                 <table id="example8" class="table table-striped table-bordered manglesh">
                                <thead>
                                <?php 
								$formid = "2";
								$Q2 = $this->db->select('tatappl,form_type, ref_no')->from('dynamic_forms')->where('id',$formid)->where('user_id',$user_id)->get();
								if($Q2->num_rows()>0){
								foreach($Q2->result() as $forminfo);
									$tatapp=$forminfo->tatappl;
								$que=$this->db->select('form_id, form_label, field_type')->from('master_dynamic_fields')->where('form_id',$formid)->get();
									if($que->num_rows()>0){
										foreach($que->result() as $row){
											?>
											<th><?php echo strtoupper($row->form_label);?></th>
											<?php
											}
											?>
												
											<th>TIMESTAMP</th>
											<?php
											if($tatapp==1)
											{
											?>
											<th>PLANNED DATE</th>
											<th>TOTAL DAYS</th>
											<?php
											}
											?>
											<?php if($forminfo->ref_no=='1'){
											    ?>
											  <th>REF NO. (REMARKS)</th>  
											  <?php 
											}?>
											
											<th>MARK AS DONE</th>
											<?php 
											if($forminfo->form_type=='2'){
												?>
												<th>REMARKS</th>
												<?php 
											}
											?>
											<th>RESPONSEID</th>
											<?php 
									}
								?>	
                                </thead>
                                <tbody>
								<?php 
								$k=1;
									$q=$this->db->select('a.id, a.responseid, a.form_id, a.formdata,b.mark_as_done, b.record_id, a.planned_date, a.ref_remarks, a.added_on')->from('dynamic_form_data a')->join('dynamic_form_data_mark_done b','a.id=b.record_id','left')->where('a.form_id',$formid)->where('a.work_status','0')->get();
									if($q->num_rows()>0){
										foreach($q->result() as $row){
								?>
								<tr>
									<?php 
									
										$json_data = json_decode($row->formdata,true);
										foreach($que->result() as $rowdata)
										{
											$name1= preg_replace('/\s+/', '', $rowdata->form_label);
											$name = strtolower($name1);
											
											$fieldtype = $rowdata->field_type;
											if($fieldtype=='5'){
												if($json_data[$name]!==''){
											$finalvalue=	"<a href='".dynamicformdata.strtolower($json_data[$name])."' download>CLICK HERE TO DOWNLOAD</a>";
												}else{
													$finalvalue=""; }	
											}else{
											$finalvalue = strtoupper($json_data[$name]);	
											}
									?>
									
									<td><?php echo $finalvalue;?></td>
									
										<?php }?>
										<td><?php  if($row->added_on=='0000-00-00 00:00:00'){}else{
											echo date('d-m-y g:i A',strtotime($row->added_on));
										};?></td>
										<?php 
										$planneddate = date('Y-m-d',strtotime($row->planned_date));
											$todaydate = date('Y-m-d');
											
											
											if($todaydate>$planneddate){
												$textcolor="color:red; font-weight:bold;";
											}else if($todaydate=$planneddate){
												$textcolor="color:blue; font-weight:bold;";
											}else{
												$textcolor="color:black; font-weight:bold;";
											}
										?>
										<?php
											if($tatapp==1)
											{
											?>
										<td><?php  if($row->planned_date=='0000-00-00 00:00:00'){}else{
											echo date('d-m-y g:i A',strtotime($row->planned_date));
										};?></td>
										
										<td style="<?php echo $textcolor;?>"><?php 
										
										$date1 = date('Y-m-d',strtotime($row->added_on));
										$date2 = date('Y-m-d');

										$diff = abs(strtotime($date2) - strtotime($date1));

										$years = floor($diff / (365*60*60*24));
										$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
										$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
										echo $days;
										?></td>
										<?php
											}
											?>
											<?php if($forminfo->ref_no=='1'){
											    ?>
										<td>
										    <?php 
										
										    if($row->ref_remarks==''){
										        ?>
										     <form method="post" action="<?php echo page_url;?>Form/update_ref_remarks/<?php echo $row->id;?>/<?php echo $formid;?>">
										            <div class="row">
										                <div class="col-md-12">
										                    <div class="form-group">
										      <label>Ref No (Remarks)</label><br>
										      <textarea class="form-control" name="ref_no" id="ref_no"></textarea>
										                        
										                    </div>
										                    
										                </div>
										                <div class="col-md-12">
										                    <input type="submit" class="btn btn-success btn-xs" value="Submit">
										                </div>
										            </div>
										        </form>
										        <?php }else{
										       echo $row->ref_remarks;
										        }?>
										</td>	    
										<?php }?>
										<td>
										    <?php if($forminfo->ref_no=='1'){
										        if($row->ref_remarks!==''){
										            echo $row->mark_as_done;
										        }else{
										            
										        }
										    }else{
										        echo $row->mark_as_done;
										    }?>
										   </td>
										<?php 
											if($forminfo->form_type=='2'){
												?>
												<td><?php 
										$query = $this->db->select('remarks')->from('dynamic_form_solution_resolution_remarks')->where('record_id',$row->id)->get();
										if($query->num_rows()>0){
											foreach($query->result() as $remarks){
												echo $remarks->remarks."<br><br>";
											}
										}
										?></td>
												<?php 
											}
											?>
											<td><?php echo $row->responseid;?></td>	
										<?php 
										}?>
										
										
								</tr>
								<?php $k++;}}?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
				
				<div class="row" id="maintenancehelpticket" style="display:none">
				<div class="col-sm-12">
                        <div class="card-box table-responsive">
						<h4 class="text-center" style="font-weight:bold;">Maintenance Help Ticket</h4><hr>
                                 <table id="example9" class="table table-striped table-bordered manglesh">
                                <thead>
                                <?php 
								$formid = "12";
								$Q2 = $this->db->select('tatappl,form_type, ref_no')->from('dynamic_forms')->where('id',$formid)->where('user_id',$user_id)->get();
								if($Q2->num_rows()>0){
								foreach($Q2->result() as $forminfo);
									$tatapp=$forminfo->tatappl;
								$que=$this->db->select('form_id, form_label, field_type')->from('master_dynamic_fields')->where('form_id',$formid)->get();
									if($que->num_rows()>0){
										foreach($que->result() as $row){
											?>
											<th><?php echo strtoupper($row->form_label);?></th>
											<?php
											}
											?>
												
											<th>TIMESTAMP</th>
											<?php
											if($tatapp==1)
											{
											?>
											<th>PLANNED DATE</th>
											<th>TOTAL DAYS</th>
											<?php
											}
											?>
											<?php if($forminfo->ref_no=='1'){
											    ?>
											  <th>REF NO. (REMARKS)</th>  
											  <?php 
											}?>
											
											<th>MARK AS DONE</th>
											<?php 
											if($forminfo->form_type=='2'){
												?>
												<th>REMARKS</th>
												<?php 
											}
											?>
											<th>RESPONSEID</th>
											<?php 
									}
								?>	
                                </thead>
                                <tbody>
								<?php 
								$k=1;
									$q=$this->db->select('a.id, a.responseid, a.form_id, a.formdata,b.mark_as_done, b.record_id, a.planned_date, a.ref_remarks, a.added_on')->from('dynamic_form_data a')->join('dynamic_form_data_mark_done b','a.id=b.record_id','left')->where('a.form_id',$formid)->where('a.work_status','0')->get();
									if($q->num_rows()>0){
										foreach($q->result() as $row){
								?>
								<tr>
									<?php 
									
										$json_data = json_decode($row->formdata,true);
										foreach($que->result() as $rowdata)
										{
											$name1= preg_replace('/\s+/', '', $rowdata->form_label);
											$name = strtolower($name1);
											
											$fieldtype = $rowdata->field_type;
											if($fieldtype=='5'){
												if($json_data[$name]!==''){
											$finalvalue=	"<a href='".dynamicformdata.strtolower($json_data[$name])."' download>CLICK HERE TO DOWNLOAD</a>";
												}else{
													$finalvalue=""; }	
											}else{
											$finalvalue = strtoupper($json_data[$name]);	
											}
									?>
									
									<td><?php echo $finalvalue;?></td>
									
										<?php }?>
										<td><?php  if($row->added_on=='0000-00-00 00:00:00'){}else{
											echo date('d-m-y g:i A',strtotime($row->added_on));
										};?></td>
										<?php 
										$planneddate = date('Y-m-d',strtotime($row->planned_date));
											$todaydate = date('Y-m-d');
											
											
											if($todaydate>$planneddate){
												$textcolor="color:red; font-weight:bold;";
											}else if($todaydate=$planneddate){
												$textcolor="color:blue; font-weight:bold;";
											}else{
												$textcolor="color:black; font-weight:bold;";
											}
										?>
										<?php
											if($tatapp==1)
											{
											?>
										<td><?php  if($row->planned_date=='0000-00-00 00:00:00'){}else{
											echo date('d-m-y g:i A',strtotime($row->planned_date));
										};?></td>
										
										<td style="<?php echo $textcolor;?>"><?php 
										
										$date1 = date('Y-m-d',strtotime($row->added_on));
										$date2 = date('Y-m-d');

										$diff = abs(strtotime($date2) - strtotime($date1));

										$years = floor($diff / (365*60*60*24));
										$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
										$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
										echo $days;
										?></td>
										<?php
											}
											?>
											<?php if($forminfo->ref_no=='1'){
											    ?>
										<td>
										    <?php 
										
										    if($row->ref_remarks==''){
										        ?>
										     <form method="post" action="<?php echo page_url;?>Form/update_ref_remarks/<?php echo $row->id;?>/<?php echo $formid;?>">
										            <div class="row">
										                <div class="col-md-12">
										                    <div class="form-group">
										      <label>Ref No (Remarks)</label><br>
										      <textarea class="form-control" name="ref_no" id="ref_no"></textarea>
										                        
										                    </div>
										                    
										                </div>
										                <div class="col-md-12">
										                    <input type="submit" class="btn btn-success btn-xs" value="Submit">
										                </div>
										            </div>
										        </form>
										        <?php }else{
										       echo $row->ref_remarks;
										        }?>
										</td>	    
										<?php }?>
										<td>
										    <?php if($forminfo->ref_no=='1'){
										        if($row->ref_remarks!==''){
										            echo $row->mark_as_done;
										        }else{
										            
										        }
										    }else{
										        echo $row->mark_as_done;
										    }?>
										   </td>
										<?php 
											if($forminfo->form_type=='2'){
												?>
												<td><?php 
										$query = $this->db->select('remarks')->from('dynamic_form_solution_resolution_remarks')->where('record_id',$row->id)->get();
										if($query->num_rows()>0){
											foreach($query->result() as $remarks){
												echo $remarks->remarks."<br><br>";
											}
										}
										?></td>
												<?php 
											}
											?>
											<td><?php echo $row->responseid;?></td>	
										<?php 
										}?>
										
										
								</tr>
								<?php $k++;}}?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
				
				<div class="row" id="helpticketforgm" style="display:none">
				<div class="col-sm-12">
                        <div class="card-box table-responsive">
						<h4 class="text-center" style="font-weight:bold;">Help Ticket for GM</h4><hr>
                                 <table id="example10" class="table table-striped table-bordered manglesh">
                                <thead>
                                <?php 
								$formid = "14";
								$Q2 = $this->db->select('tatappl,form_type, ref_no')->from('dynamic_forms')->where('id',$formid)->where('user_id',$user_id)->get();
								if($Q2->num_rows()>0){
								foreach($Q2->result() as $forminfo);
									$tatapp=$forminfo->tatappl;
								$que=$this->db->select('form_id, form_label, field_type')->from('master_dynamic_fields')->where('form_id',$formid)->get();
									if($que->num_rows()>0){
										foreach($que->result() as $row){
											?>
											<th><?php echo strtoupper($row->form_label);?></th>
											<?php
											}
											?>
												
											<th>TIMESTAMP</th>
											<?php
											if($tatapp==1)
											{
											?>
											<th>PLANNED DATE</th>
											<th>TOTAL DAYS</th>
											<?php
											}
											?>
											<?php if($forminfo->ref_no=='1'){
											    ?>
											  <th>REF NO. (REMARKS)</th>  
											  <?php 
											}?>
											
											<th>MARK AS DONE</th>
											<?php 
											if($forminfo->form_type=='2'){
												?>
												<th>REMARKS</th>
												<?php 
											}
											?>
											<th>RESPONSEID</th>
											<?php 
									}
								?>	
                                </thead>
                                <tbody>
								<?php 
								$k=1;
									$q=$this->db->select('a.id, a.responseid, a.form_id, a.formdata,b.mark_as_done, b.record_id, a.planned_date, a.ref_remarks, a.added_on')->from('dynamic_form_data a')->join('dynamic_form_data_mark_done b','a.id=b.record_id','left')->where('a.form_id',$formid)->where('a.work_status','0')->get();
									if($q->num_rows()>0){
										foreach($q->result() as $row){
								?>
								<tr>
									<?php 
									
										$json_data = json_decode($row->formdata,true);
										foreach($que->result() as $rowdata)
										{
											$name1= preg_replace('/\s+/', '', $rowdata->form_label);
											$name = strtolower($name1);
											
											$fieldtype = $rowdata->field_type;
											if($fieldtype=='5'){
												if($json_data[$name]!==''){
											$finalvalue=	"<a href='".dynamicformdata.strtolower($json_data[$name])."' download>CLICK HERE TO DOWNLOAD</a>";
												}else{
													$finalvalue=""; }	
											}else{
											$finalvalue = strtoupper($json_data[$name]);	
											}
									?>
									
									<td><?php echo $finalvalue;?></td>
									
										<?php }?>
										<td><?php  if($row->added_on=='0000-00-00 00:00:00'){}else{
											echo date('d-m-y g:i A',strtotime($row->added_on));
										};?></td>
										<?php 
										$planneddate = date('Y-m-d',strtotime($row->planned_date));
											$todaydate = date('Y-m-d');
											
											
											if($todaydate>$planneddate){
												$textcolor="color:red; font-weight:bold;";
											}else if($todaydate=$planneddate){
												$textcolor="color:blue; font-weight:bold;";
											}else{
												$textcolor="color:black; font-weight:bold;";
											}
										?>
										<?php
											if($tatapp==1)
											{
											?>
										<td><?php  if($row->planned_date=='0000-00-00 00:00:00'){}else{
											echo date('d-m-y g:i A',strtotime($row->planned_date));
										};?></td>
										
										<td style="<?php echo $textcolor;?>"><?php 
										
										$date1 = date('Y-m-d',strtotime($row->added_on));
										$date2 = date('Y-m-d');

										$diff = abs(strtotime($date2) - strtotime($date1));

										$years = floor($diff / (365*60*60*24));
										$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
										$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
										echo $days;
										?></td>
										<?php
											}
											?>
											<?php if($forminfo->ref_no=='1'){
											    ?>
										<td>
										    <?php 
										
										    if($row->ref_remarks==''){
										        ?>
										     <form method="post" action="<?php echo page_url;?>Form/update_ref_remarks/<?php echo $row->id;?>/<?php echo $formid;?>">
										            <div class="row">
										                <div class="col-md-12">
										                    <div class="form-group">
										      <label>Ref No (Remarks)</label><br>
										      <textarea class="form-control" name="ref_no" id="ref_no"></textarea>
										                        
										                    </div>
										                    
										                </div>
										                <div class="col-md-12">
										                    <input type="submit" class="btn btn-success btn-xs" value="Submit">
										                </div>
										            </div>
										        </form>
										        <?php }else{
										       echo $row->ref_remarks;
										        }?>
										</td>	    
										<?php }?>
										<td>
										    <?php if($forminfo->ref_no=='1'){
										        if($row->ref_remarks!==''){
										            echo $row->mark_as_done;
										        }else{
										            
										        }
										    }else{
										        echo $row->mark_as_done;
										    }?>
										   </td>
										<?php 
											if($forminfo->form_type=='2'){
												?>
												<td><?php 
										$query = $this->db->select('remarks')->from('dynamic_form_solution_resolution_remarks')->where('record_id',$row->id)->get();
										if($query->num_rows()>0){
											foreach($query->result() as $remarks){
												echo $remarks->remarks."<br><br>";
											}
										}
										?></td>
												<?php 
											}
											?>
											<td><?php echo $row->responseid;?></td>	
										<?php 
										}?>
										
										
								</tr>
								<?php $k++;}}?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
				
				<div class="row" id="dispatchfortommorow" style="display:none">
		  <div class="col-sm-12">
                        <div class="card-box table-responsive">
						<h4 class="text-center" style="font-weight:bold;">Dispatch for Tomorrow Order List</h4><hr>
                             <table id="example11" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th>Sr No.</th>
									<th>PRINT PACKING LABEL</th>
									<th>TIMESTAMP</th>
									<th>PLANNED TIME</th>
									<th>ACTUAL TIME</th>
									<th style="width:50%">INSTRUMENTS</th>
                                    <th>ORDER TYPE</th>
                                    <th>REGION MARKETING PERSON</th>
                                    <th>PO NUMBNER</th>
                                    <th>COMPANY NAME</th>
									<th>ADDRESS WITH PINCODE</th>
									<th>EMAIL ID</th>
									<th>MOBILE NUMBER</th>
									<th>INTERNAL ORDER NUMBER</th>
									<th>PAYMENT TERMS</th>
									<th>INSTALLATION CHARGES TYPE</th>
									<th>PACKING CHARGES</th>
									<th>FREIGHT TYPE</th>
									<th>REMARKS</th>
									
									
                                </tr>
                                </thead>


                                <tbody>
								
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
				
				<div class="row" id="serviceengineervisit" style="display:none">
		  <div class="col-sm-12">
                        <div class="card-box table-responsive">
						<h4 class="text-center" style="font-weight:bold;">Service Engineer Visit Report</h4><hr>
                             <table id="example12" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                     <th>SR NO.</th>
									<th>TIMESTAMP</th>
									<th>YOUR NAME</th>
									<th>ENGINEER NAME</th>
                                    <th>SALES FORCE NO.</th>
									<th>WARRANTY STATUS</th>
									<th>NATURE OF COMPLAINT</th>
									<th>COMPANY NAME</th>
									<th>CONTACT PERSON</th>
									<TH>CONTACT NUMBER</TH>
									<th>DATE</th>
									<th>CHARGABLE</th>
									<th>PAYMENT COLLECTION</th>
									<th>CHARGES</th>
									<th>BILL NUMBER</th>
									<th>ACTUAL OVSERVATION</th>
									<th>SPARE PARTS</th>
									<Th>PART NAME</th>
									<th>PICTURE</th>
									<th>SERVICE REPORT</th>
									<th>NEXT ACTION</th>
									
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
				
				<div class="row" id="readyordercompleteorder" style="display:none">
		  <div class="col-sm-12">
                        <div class="card-box table-responsive">
						<h4 class="text-center" style="font-weight:bold;">Ready Orders Complete Orders</h4><hr>
                              <table id="example13" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th>Sr No.</th>
									<th>CLOSE ORDER</th>
									<th>ADDED ON</th>
									<th style="width:50%">INSTRUMENTS</th>
                                    <th>ORDER TYPE</th>
                                    <th>REGION MARKETING PERSON</th>
                                    <th>PO NUMBNER</th>
                                    <th>COMPANY NAME</th>
									<th>ADDRESS WITH PINCODE</th>
									<th>EMAIL ID</th>
									<th>MOBILE NUMBER</th>
									<th>INTERNAL ORDER NUMBER</th>
									<th>DISCOUNT</th>
									<th>ORDER VALUE AFTER DISCOUNT</th>
									<th>ADVANCE AMOUNT RECEIVED</th>
									<th>PAYMENT TERMS</th>
									<th>INSTALLATION CHARGES TYPE</th>
									<th>PACKING CHARGES</th>
									<th>FREIGHT TYPE</th>
									<th>REMARKS</th> 
                                </tr>
                                </thead>


                                <tbody>
								
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
				
				<div class="row" id="sampletestingreport" style="display:none">
		  <div class="col-sm-12">
                        <div class="card-box table-responsive">
						<h4 class="text-center" style="font-weight:bold;">Sample Testing Report</h4><hr>
                              <table id="example14" class="table table-striped table-bordered manglesh" width="100%">
                                <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th>RAISED ON</th>
									<th>SAMPLE TEST ID</th>
									<th>COMPANY NAME</th>
									<th>ITEMS</th>
									<th>REPORT</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
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
<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
		<script>
		// Time Picker
            jQuery('#timepicker').timepicker({
                defaultTIme : false
            });
			jQuery('#timepicker4').timepicker({
                defaultTIme : false
            });
            jQuery('#timepicker2').timepicker({
                showMeridian : false
            });
            jQuery('#timepicker3').timepicker({
                minuteStep : 15
            });
		</script>
 <script>
$( document ).ready(function() {
$('.select2').select2({ });
$('.select3').select2({ });
$('.select4').select2({ });
$('#example').dataTable({
 "bProcessing": false,
 "pagination":true,
 "sAjaxSource": "<?php echo page_url;?>HOD_report/conveyance_voucher_for_hod_list/",
 "aoColumns": [
						{ mData: 'sr_no' } ,
                        { mData: 'user_name' },
                        { mData: 'travel_date' },
						{ mData: 'from_location' },
						{ mData: 'proceed_to' },
                        { mData: 'mode' },
						{ mData: 'vehicletype' },
						{ mData: 'start_reading' },
						{ mData: 'end_reading' },
						{ mData: 'rate_per_km' },
						{ mData: 'amount' },
						{ mData: 'hodremarks' }
						
                ]
        });  
$('#example1').dataTable({
 "bProcessing": false,
 "pagination":true,
 "sAjaxSource": "<?php echo page_url;?>HOD_report/travel_conveyance_voucher_hod_list/",
 "aoColumns": [
						{ mData: 'sr_no' } ,
                        { mData: 'name' },
						{ mData: 'purpose' },
						{ mData: 'start_date' },
                        { mData: 'end_date' },
						{ mData: 'departure_time' },
						{ mData: 'arrival_time' },
						{ mData: 'total_days' },
						{ mData: 'attached_bills' },
						{ mData: 'traveldata' },
						{ mData: 'hod_status' }
							
                ]
        });	

$('#example2').dataTable({
 "bProcessing": true,
 "pagination":true,
 "sAjaxSource": "<?php echo page_url;?>HOD_report/visit_schedule_list/",
 "aoColumns": [
						{ mData: 'sr_no' } ,
                        { mData: 'customer_name' },
						{ mData: 'contact_number' },
						{ mData: 'company_name' },
                        { mData: 'address' },
                        { mData: 'visit_date' },
						{ mData: 'meeting_time' },
						{ mData: 'employee' },
						{ mData: 'added_by' },
						{ mData: 'added_time' }/*,
						{mData:'updatestatus'},
						{ mData: 'edit' }*/
						
                ]
        }); 

$('#example3').dataTable({
 "bProcessing": true,
 "pagination":true,
 "sAjaxSource": "<?php echo page_url;?>HOD_report/sales_daily_update_dashboard_list/",
 "aoColumns": [
						{ mData: 'sr_no' } ,
                        { mData: 'employee' },
						{ mData: 'customer_name' },
						{ mData: 'contact_number' },
                        { mData: 'company_name' },
                        { mData: 'visit_number' },
						{ mData: 'action_taken' },
						{ mData: 'stage' },
						{ mData: 'sale_value' },
						{ mData: 'machine_name' },
						{ mData: 'next_action_plan' },
						{ mData: 'date_of_next_plan' },
						{ mData: 'visitingcard' },
						{mData:'remarks'},
						{ mData: 'added_time' }
						
                ]
        }); 	

$('#example4').dataTable({
 "bProcessing": true,
 "pagination":true,
 "sAjaxSource": "<?php echo page_url;?>HOD_report/leave_application_hod_dashboard_list/",
 "aoColumns": [
							{ mData: 'sr_no' } ,
                        { mData: 'timestamp' },
						{ mData: 'employee_name' },
						{ mData: 'department' },
						{ mData: 'user_role' },
						{ mData: 'leave_date' },
                       	{ mData: 'reason' },
						{ mData: 'total_days' },
						{ mData: 'phone_no' },
						{ mData: 'leave_type' },
						{ mData: 'leave_for' },
						{mData:'hodstatus'}
						
						
                ]
        });	

$('#example5').dataTable({
 "bProcessing": true,
 "pagination":true
        });
$('#example6').dataTable({
 "bProcessing": true,
 "pagination":true
        });
$('#example7').dataTable({
 "bProcessing": true,
 "pagination":true
        });
$('#example8').dataTable({
 "bProcessing": true,
 "pagination":true
        });
		
$('#example9').dataTable({
 "bProcessing": true,
 "pagination":true
        });
		$('#example10').dataTable({
 "bProcessing": true,
 "pagination":true
        });
$('#example16').dataTable({
 "bProcessing": false,
	fixedHeader: true,
 "pagination":true,
 "sAjaxSource": "<?php echo page_url;?>HOD_report/task_delegated_to_you/",
 "aoColumns": [
						{ mData: 'sr_no' } ,
                        { mData: 'timestamp' },
						{ mData: 'delegated_to' },
						{ mData: 'caseno' },
                        { mData: 'task' },
	                    { mData: 'attachment' },
						{ mData: 'delegated_date' },
						{ mData: 'second_date' },
						{ mData: 'third_date' }
						
                ]
        }); 	

$('#example11').dataTable({
 "bProcessing": false,
 "pagination":true,
fixedHeader: true,
   scrollCollapse: true,
   fixedColumns:   {
            leftColumns: 3
        },
 "sAjaxSource": "<?php echo page_url;?>HOD_report/dispatchfortommorow_order_list/",
 "aoColumns": [
						{ mData: 'sr_no' } ,
						{ mData: 'packinglabel' },
                        { mData: 'added_on' },
						{ mData: 'plannedtime' },
						{ mData: 'actualtime' },
						{ mData: 'itemname' },
                        { mData: 'order_type' },
                        { mData: 'marketing_person' },
						{ mData: 'po_number' }, 
						{ mData: 'company_name' },
						{ mData: 'address' },
						{ mData: 'email' },
						{ mData: 'mobile_number' },
						{ mData: 'internal_order_no' },
						{ mData: 'payment_terms' },
						{ mData: 'installation_charges' },
						{ mData: 'packingcharges' },
						{ mData: 'freigntcharges' },
						{ mData: 'remarks' }
						
						
                ]
        }); 

$('#example12').dataTable({
 "bProcessing": false,
 "pagination":true,
 "sAjaxSource": "<?php echo page_url;?>HOD_report/visit_form_list/",
 "aoColumns": [
					{ mData: 'sr_no' } ,
                        { mData: 'addedon' },
                        { mData: 'name' },
						{ mData:'engineer'},
						{ mData: 'sale_force_no' },
						{ mData: 'warrenty_status' },
						{ mData: 'nature_of_complaints' },
						{ mData: 'company_name' },
						{ mData: 'contact_person' },
						{ mData: 'contact_number' },
						{mData:'visit_date'},
						{mData:'chargable'},
						{mData:'payment_to_collect'},
						{mData:'charges'},
						{mData:'bill_number'},
						{mData:'observation_of_engineer'},
						{mData:'spare_parts'},
						{mData:'part_name'},
						{mData:'picture'},
						{mData:'service_report'},
						{mData:'next_action'}
						
						
                ]
        }); 

		$('#example13').dataTable({
 "bProcessing": false,
 "pagination":true,
fixedHeader: true,
   scrollCollapse: true,
   fixedColumns:   {
            leftColumns: 3
        },
"sAjaxSource": "<?php echo page_url;?>HOD_report/packed_order_list/",
 "aoColumns": [
						{ mData: 'sr_no' } ,
						{ mData: 'closeorder' },
                        { mData: 'added_on' },
						{ mData: 'itemname' },
                        { mData: 'order_type' },
                        { mData: 'marketing_person' },
						{ mData: 'po_number' },
						{ mData: 'company_name' },
						{ mData: 'address' },
						{ mData: 'email' },
						{ mData: 'mobile_number' },
						{ mData: 'internal_order_no' },
						{ mData: 'discount' },
						{ mData: 'order_value_after_discount' },
						{ mData: 'advance_amount' },
						{ mData: 'payment_terms' },
						{ mData: 'installation_charges' },
						{ mData: 'packingcharges' },
						{ mData: 'freigntcharges' },
						{ mData: 'remarks' }
						
						
                ]
        }); 
		
		$('#example14').dataTable({
 "bProcessing": false,
 "pagination":true,
 fixedHeader: {
            header: true
        },
   scrollCollapse: true,
 "sAjaxSource": "<?php echo page_url;?>HOD_report/sampleallrequests/",
 "aoColumns": [
						{ mData: 'sr_no' } ,
						{ mData: 'raisedon' } ,
						{ mData: 'sampleid' },
                        { mData: 'companyname' },
                        { mData: 'items' },
                        { mData: 'report' }
						
                ]
        }); 
		
});

</script>


<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
</body>
</html>