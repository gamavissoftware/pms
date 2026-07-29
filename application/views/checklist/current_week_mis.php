<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Current week MIS</title>

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

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script><?PHP $q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();foreach($q->result() as $LOGO);?>
 <style>
            .requiredclass{
                border: 2px solid #E12830;
            }
        </style>
		<style>
table.manglesh thead th {
				background: <?php echo $LOGO->colorcode;?>;
				color:#fff;
				font-weight:bold;
				text-align:center;
			}
			table.report tbody td {
				font-weight:bold;
				color:#000;
			}
table.report thead th {
				background: red;
				color:#fff;
				font-weight:bold;
			}
body{margin-top:40px;}

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
<div class="wrapper" style="padding-top: 80px !important;">

            <div class="container-fluid" style="background-color:#fff;">



                <!-- Page-Title -->

                <div class="row">
				
				<div class="col-sm-12">

                        <div class="page-title-box">
                            



						<div class="col-md-12 text-center">
						    	
						
						    
    <div class="col-md-12" style="padding-top:30px">
  <h4>     
<?php 
$query = $this->db->select('user_id, title, first_name, last_name')->from('system_users')->where('user_id',$this->uri->segment(3))->get();
foreach($query->result() as $row);
echo strtoupper($row->title)." ".strtoupper($row->first_name)." ".strtoupper($row->last_name);

?> MIS REPORT OF CURRENT WEEK</h4><hr>
 
						  </div>


	  
                            </div>
	

	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
                        </div>

                    </div><hr>
					
					<div class="row">
						<div class="col-md-4">
							<table class="table table-striped table-bordered report">
								<thead style="text-align:center">
									<tr>
										<th style="text-align:center">SR NO</th>
										<th style="text-align:center">TAT</th>
										<th style="text-align:center">TOTAL TASK</th>
										
									</tr>
								</thead>
								<tbody>
								<?php 
								$k=1;
								$user_id=$this->uri->segment(3);
								$query = $this->db->select('id, turnaroundtime, status,sortbynumber')->from('compliance_tat')->where('status','1')->order_by('sortbynumber','asc')->get();
								foreach($query->result() as $timing){
								?>
									<tr>
										<td style="text-align:center"><?php echo $k;?></td>
										<td style="text-align:center"><a style="text-decoration:none; color:#000"  href="<?php echo page_url;?>Checklist/checklist_filter_by_category/<?php echo $timing->id;?>/<?php echo $user_id;?>"><?php echo $timing->turnaroundtime;?></a></td>
										<td style="text-align:center"><?php 
										$query = $this->db->select('task_id, tat_id, status,company_id')->from('compliance_task_report')->where('tat_id',$timing->id)->where('user_id',$user_id)->where('status','1')->get();
										$res = $query->result();
										echo count($res);

										?></td>
										
										
									</tr>
				<?php $k++;}?>
								</tbody>
							</table>
						</div>
						
						<div class="col-md-4">
						<table class="table table-striped table-bordered report">
								<thead>
									<tr>
										<th colspan="3" style="text-align:center">MIS REPORT OF CURRENT WEEK FROM (<?php
										$current_monday =  date('Y-m-d', strtotime( "previous monday" ));
						$current_saturday =  date('Y-m-d', strtotime( "next saturday" ));
						echo $current_monday." TO ".$current_saturday;
										?>)</th>
										
									</tr>
								
									<tr>
						<th style="text-align:center">CURRENT WEEK MIS SCORE </th>
						
										
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="text-align:center"><?php 
										
						$current_monday =  date('Y-m-d', strtotime( "previous monday" ));
						$current_saturday =  date('Y-m-d', strtotime( "next saturday" ));
						
						$tatarray = array();
				           $date = date('Y-m-d');
						   $this->db->distinct();
				           $this->db->select('task_id, tat_id,company_id')->from('compliance_task_report')->where('user_id',$user_id);
						   $query = $this->db->get();
				           $res = $query->result();
						   if($query->num_rows()>0){
				           foreach($res as $row){
				               $tatarray[] = $row->task_id;
				           }
						   }
						
						if($tatarray){
						$query = $this->db->select('id')->from('checklist_done_notdone')->where_in('task_id',$tatarray)->where('task_date BETWEEN "'. $current_monday. '" and "'.$current_saturday.'"')->where('status','0')->get();
						$res = count($query->result());
						$result = $res*100;
						if($result=='0'){
							echo $result."%";
						}else{
						echo "-".$result."%";
						}}else{
							echo "0%";
						}
						?></td>
						
						</tr>
								</tbody>
							</table>
						</div>						
					
					</div>
														
															

                </div>

                <!-- end page title end breadcrumb -->

		

		<div class="row">

                    <div class="col-sm-12">

                        <div class="card-box table-responsive">

                            <table id="example" class="table table-striped table-bordered manglesh">

                                <thead>

                                <tr>

                                    <th>Sr No.</th>
									<th>Assigned To</th>
									<th>Video Link</th>
									<th>Task</th>
									<th>Turnaround Time</th>
									<th>Status</th>
									<th>Missing Date</th>
									<th>Added On</th>
                                    

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
"lengthMenu": [[50, 100, 200, -1], [50, 100, 200, "All"]],
fixedHeader:{
    header:true,
    headerOffset:$('#topnav').outerHeight()
},
"searchHighlight": true,
"pagination":true,
"sAjaxSource": "<?php echo page_url;?>Checklist/current_week_mis_list/<?php echo $user_id;?>",
"aoColumns": [
				{ mData: 'sr_no' } ,
				{mData:'assigned_to'},
				{mData:'video_link'},
				{mData:'task'},
				{mData:'turnaroundtime'},
				{mData:'status'},
				{mData:'missing_date'},
				{mData:'added_on'}
				
				
				
		]
});   
});

</script>
 <script type="text/javascript">
            $(function () {
                $('.datepicker-autoclose').datepicker({
                    autoclose:"true",
                    orientation: "bottom",
                    changeMonth: true,
    changeYear: true,
    format: 'dd-mm-yyyy'
                });
            });
        </script>



<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
</body></html>