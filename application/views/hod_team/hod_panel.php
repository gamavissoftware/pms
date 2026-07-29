<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Prestogroup</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css">
      
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
		<style>table.manglesh thead th {
				background: #003366;
				color:#fff;
				font-weight:bold;
				text-align:center;
			}
			table.manglesh tbody td {
				text-align:center;
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
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right">
						    </div>
                           
                            <h4 class="page-title text-center">NEXT DATE SCHEDULE OF THE EMPLOYEE</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
<?php 
$user_id =$this->session->userdata['logged_in']['user_id'];
								$query = $this->db->select('team_name, employee_id, id')->from('presto_hod')->where('employee_id',$user_id)->get();
								foreach($query->result() as $row);
?>
				<form action="<?php echo page_url;?>HOD_Team/update_schedule/<?php echo $row->id;?>" method="post">
                 <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
						<div class="col-md-2"><input type="text" class="form-control datepicker" name="schedule_date" value="<?php echo date('d-m-Y', strtotime(' +1 day'));?>" style="background-color:red; color:#fff; font-weight:bold;"></div>
                            <table class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th>Sr No.</th>
									 <th>EMPLOYEE NAME</th>
                                    <th>EMPLOYEE CODE</th>
                                    <th>YES</th>
									<th>NO</th>
                                    
                                </tr>
                                </thead>
								<tbody>
								<?php 
								$i=0;
								$q=$this->db->select('employee_name,employee_code, id')->from('hod_team_members')->where('team_id',$row->id)->get();
								foreach($q->result() as $rows){
								?>
									<tr>
										<td><?php echo $i;?></td>
										<td>
										<input type="hidden" name="recordid[]" value="<?php echo $rows->id;?>">
										<input type="hidden" name="employee_name[]" value="<?php echo $rows->employee_name;?>">
										<input type="hidden" name="employee_code[]" value="<?php echo $rows->employee_code;?>">
										<?php echo $rows->employee_name;?></td>
										<td><?php echo $rows->employee_name;?></td>
										<?php
										$yes ="checked";
										$no ="";
										$q = $this->db->select('status')->from('hod_team_employee_schedule')->where('record_id',$rows->id)->where('team_id',$row->id)->where('scheduled_date',date('Y-m-d', strtotime(' +1 day')))->get();
										if($q->num_rows()>0){
											foreach($q->result() as $checkinfo);
											if($checkinfo->status=='1'){
												$yes="checked";
											}else{
												$no = "checked";
											}
										}
										?>
										
										<td><input type="radio" name="attendance<?php echo $i;?>" value="1" <?php echo $yes;?>></td>
										<td><input type="radio" name="attendance<?php echo $i;?>" value="0" <?php echo $no;?>></td>
									</tr>
								<?php $i++;}?>
								<tr>
									<td colspan="3"></td>
									<td colspan="2">
									<?php 
									$query = $this->db->select()->from('hod_team_employee_schedule')->where('team_id',$row->id)->where('scheduled_date',date('Y-m-d', strtotime(' +1 day')))->get();
									if($query->num_rows()>0){
										echo "<h4 style='color:red;'>You have scheduled your team plan.</h4>";
									?>
									<?php }else{?>
									<input type="submit" class="btn btn-success">
									<?php }?></td>
								</tr>
								</tbody>
                                
                            </table>
                        </div>
                    </div>
                </div>
				
                <!-- end row -->
			</form>


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
       
<script language="javascript" type="text/javascript">   
$(document).ready(function() {
$("#save").click(function() {
var team_leader = $("#team_leader").val();
if(team_leader=='')
{
	$("#error_team_leader").html('Required!');
}
var team_name = $("#team_name").val();
if(team_name=='')
{
	$("#error_team_name").html('Required!');
}
if(team_leader=='' || team_name=='')
{
	return false;
}
});
});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script><script>
    $('.datepicker').datepicker({
     autoclose: true,
     orientation: "bottom",
 format:'dd-mm-yyyy'
   });

</script>
    </body>
</html>