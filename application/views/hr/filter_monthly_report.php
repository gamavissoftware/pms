<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title> Attendace Report</title>

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

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
     <style>
            .requiredclass{
                border: 2px solid #E12830;
            }
        </style>
		<style>
table.manglesh thead th {
				background: #003366;
				color:#fff;
				font-weight:bold;
				text-align:center;
			}
table.report thead th {
				background: red;
				color:#fff;
				font-weight:bold;
			}
table.report tbody td {
				font-weight:bold;
				color:#000;
			}
.feedback {
  background-color : #31B0D5;
  color: white;
  padding: 10px 20px;
  border-radius: 4px;
  border-color: #46b8da;
}
			</style>
    </head>


   <body>
<header id="topnav">
<?php $this->load->view('common/nav-menu.php');?>
</header>
<div class="wrapper">

            <div class="container-fluid" style="background-color:#fff">
<!-- Page-Title -->

                <div class="row">
				
				<div class="col-sm-12">
<div class="page-title-box">

<div class="col-md-12">
    <h4 class="text-center">USER ATTENDANCE DASHBOARD</h4>
    <div class="pull-right"></div>
    
    
    
</div>
						
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
                        </div>

                    </div>
                </div>

                <!-- end page title end breadcrumb -->
		<div class="row">
<div class="col-md-3"></div>
<div class="col-md-6" style="background-color:grey">
     <form id="loginForm" method="post" action="<?php echo page_url;?>Hr/attendance_monthly_report">
    <div class="row">
                                                
                                                
												 <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-2" class="control-label">User List </label>
														<span id="error_city" style="color:red;"></span>
														<select class="form-control" name="user_id" id="user_id">
														<option value="">Select User</option>
										<?php 
								if(!empty($user_id)){
								    $userids  = $user_id;
								}else{
								    $userids="";
								}
								$query = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('user_status','1')->where('hide_profile','0')->get();
								foreach($query->result() as $row){
								?>
								<option value="<?php echo $row->user_id;?>" <?php if($userids==$row->user_id){echo "selected";}?>><?php echo $row->first_name." ".$row->last_name;?></option>
								<?php }?>
														</select>
                                                    </div>
                                                </div>
												
												<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">Start Date </label>
														 <span id="error_company" style="color:red;"></span>
														 <input type="date" class="form-control" name="startdate" id="startdate"  value="<?php 
														 if(!empty($startdate)){
									  echo $startdate;
									}
														 ?>">
													</div>
												</div>
												<div class="col-md-4">
												<div class="form-group">
														 <label for="field-2" class="control-label">End Date </label>
														 <Span id="address_error" style="color:red;"></span>
														<input type="date" class="form-control" name="enddate" id="enddate"  value="<?php 
														 if(!empty($enddate)){
									  echo $enddate;
									}
														 ?>">
														 
														 </textarea>
														 
													</div>
												</div>
												<div class="col-md-9"></div>
												<div class="col-md-2 pull-right">
												    <input type="submit" id="save" class="btn btn-info" value="Submit"> 
												</div>
											
											
                                            </div>
                                            </form>
    
</div>
<div class="col-md-3"></div>
                    <div class="col-sm-12">
<form action="<?php echo page_url;?>Checklist/update_audit_remarks/" method="post" enctype="multipart/form-data">
                        <div class="card-box table-responsive">
<table id="example" class="table table-striped table-bordered manglesh">
<thead>
                                    <tr><th>SR NO.</th>
									<th>EMPLOYEE NAME</th>
									<th>TOTAL WORKING DAYS</th>
									<th>ATTENDANCE UPDATED </th>
									<th>ATTENDANCE NOT UPDATED </th>
									<th>TOTAL SHORT LEAVE</th>
                                </tr>
                                </thead>
								<tbody>
								<?php 
								$k=1;
								
								$this->db->select('user_id, first_name, last_name')->from('system_users');
								if(!empty($user_id)){
								    $this->db->where('user_id',$user_id);
								}
								$query = $this->db->where('user_status','1')->where('hide_profile','0')->get();
								foreach($query->result() as $row){
								?>
								<tr>
									<td class="text-center"><?php echo $k;?></td>
									<td class="text-center"><?php echo $row->first_name;?> <?php echo $row->last_name;?></td>
									<td class="text-center"><?php 
									
									if(!empty($startdate)){
									  $start_date =   $startdate;
									}
									else{
									  $start_date = date('Y-m-')."01";  
									}
									 
									if(!empty($enddate)){
									    $enddate=$enddate;
									}else{
									    $end_date = date("Y-m-t", strtotime($start_date));
									    $enddate= date('Y-m-d');
									}
         
         if(!empty($startdate) && !empty($enddate))
         {
            $now = strtotime($enddate); 
            $your_date = strtotime($startdate);
            $totalday = $now - $your_date;
            $totaldays = round($totalday / (60 * 60 * 24));
         }else{
           $totaldays =  date('t');  
         }
         
         
        $num_sundays="";
        for ($i = 0; $i < ((strtotime($enddate) - strtotime($start_date)) / 86400); $i++){ 
        if(date('l',strtotime($start_date) + ($i * 86400)) == 'Sunday'){ 
        $num_sundays++; 
        } 
        } 
        
       $totalworkingdays = $totaldays-$num_sundays;
       echo $totalworkingdays; 
									?></td>
									<td class="text-center">
									    <?php $q = $this->db->select('id')->from('mark_your_attendance')->where('employee_id',$row->user_id)->where('attendance_date BETWEEN "'. date('Y-m-d', strtotime($start_date)). '" and "'. $enddate.'"')->where('absent_status','0')->get();
									    echo count($q->result());
									    ?>
									    
									</td>
									   <td class="text-center"> <?php $q = $this->db->select('id')->from('mark_your_attendance')->where('employee_id',$row->user_id)->where('attendance_date BETWEEN "'. date('Y-m-d', strtotime($start_date)). '" and "'. $enddate.'"')->where('absent_status','1')->get();
									    echo count($q->result());
									    ?>
									    
									</td>
									
									<td class="text-center"><?php 
									$query = $this->db->select('id')->from('leave_application')->where('employee_id',$row->user_id)->where('from_loc BETWEEN "'. date('Y-m-d', strtotime($start_date)). '" and "'. $enddate.'"')->get();
									$res = count($query->result());
									echo $res;
									?></td>
                                    
								</tr>
								<?php $k++;}?>
								</tbody>
                            </table>
                        </div>
</form>
                    </div>

                </div>

                <!-- end row -->


                <!-- Footer -->

                <?php $this->load->view('common/footer');?>

                <!-- End Footer -->
            </div> <!-- end container -->

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
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>


<script>
$( document ).ready(function() {
	$('.select2').select2({ });
$('.select3').select2({ });
$('.select4').select2({ });
$('#example').dataTable({
"bProcessing": true,
 dom: 'Bfrtip',
        buttons: [
            'excel'
        ],
"lengthMenu": [[100, 200, -1], [100, 200, "All"]],
fixedHeader:{
    header:true,
    headerOffset:$('#topnav').outerHeight()
},
"searchHighlight": true,
"pagination":true
});   
});

</script>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
</body></html>