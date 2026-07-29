<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> History</title>

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
			table.taskdetail thead th {
				background: yellow;
				color:#000;
				font-weight:bold;
			}
</style>
    </head>
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
						<?php if($this->uri->segment(3)=='43'){?>
						     <button class="btn btn-warning waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">FILTER</button>
						<?php }
						?>
						 </div>
						 
						 
						 
						 <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="<?php echo page_url;?>Form/view_filtered_data/<?php echo $this->uri->segment(3);?>"  enctype="multipart/form-data">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">FILTER BY DATE </h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                             
													<div class="col-md-6">
													<div class="form-group">
														 <label for="field-2" class="control-label">START DATE</label>
														 <span id="error_target" style="color:red;"></span>
														 <input type="date" class="form-control" name="start_date" id="start_date"  value="" required>
													</div>
												</div>
													<div class="col-md-6">
													<div class="form-group">
														 <label for="field-2" class="control-label">END DATE</label>
														 <span id="error_target" style="color:red;"></span>
														 <input type="date" class="form-control" name="end_date" id="end_date"  value="" required>
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
                           
                            <h4 class="page-title text-center"><?php $query = $this->db->select('a.id, a.dashboard_title, a.description, a.department_id, b.department_id, b.department')->from('dynamic_forms a')->join('departments b','a.department_id=b.department_id','left')->where('a.id',$this->uri->segment(3))->get();
							foreach($query->result() as $row){
								echo "<span style='font-size:18px'>".strtoupper($row->dashboard_title)."</span>";
								echo "<span style='font-size:14px'>  (".strtoupper($row->description).")</span>";
							}?> <span style="color:red">HISTORY</span></h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
	<?php echo $this->session->flashdata('message'); ?>
		<div class="row">
		 
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
								<?php 
								
									$que=$this->db->select('form_id, form_label, field_type')->from('master_dynamic_fields')->where('form_id',$this->uri->segment(3))->get();
									if($que->num_rows()>0){
										foreach($que->result() as $row){
											?>
											<th><?php echo strtoupper($row->form_label);?></th>
											<?php
											}
											?>
											
											<th>TIMESTAMP</th>
											<th>PLANNED DATE</th>
											<th>TOTAL DAYS</th>
											<th>COMPLITION DATE/TIME</th>
											<th>REMARKS</th>
											<th>Ref No Remarks</th>
											 <th>Upload Docket Image</th>
											<?php 
									}
								?>
                                    
									
                                </tr>
                                </thead>
                                <tbody>
								<?php 
								$k=1;
							$startdate = $start_date." 00:00:00";
							$enddate = $end_date." 23:59:59";
									$this->db->select('a.id, a.form_id,a.ref_remarks, a.formdata,b.mark_as_done, b.record_id, a.planned_date, a.added_on, a.task_complition_time')->from('dynamic_form_data a')->join('dynamic_form_data_mark_done b','a.id=b.record_id','left')->where('a.form_id',$this->uri->segment(3))->where('a.work_status','1');
									$this->db->where('added_on BETWEEN "'.$startdate. '" and "'.$enddate.'"');
									$q= $this->db->order_by('a.id','desc')->get();
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
											$finalvalue=	"<a href='".dynamicformdata.$json_data[$name]."' download>CLICK HERE TO DOWNLOAD</a>";
												}else{
													$finalvalue="";
												}
											}else{
											$finalvalue = strtoupper($json_data[$name]);	
											}
											
											
									?>
									
									<td><?php echo $finalvalue;?></td>
									
										<?php }?>
										<td><?php  if($row->added_on=='0000-00-00 00:00:00'){}else{
											echo date('d-m-Y H:i:s',strtotime($row->added_on));
										}?></td>
										<td><?php  if($row->planned_date=='0000-00-00 00:00:00'){}else{
											echo date('d-m-Y H:i:s',strtotime($row->planned_date));
										}?></td>
										
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
										<td style="<?php echo $textcolor;?>"><?php 
										
										$date1 = date('Y-m-d',strtotime($row->planned_date));
										$date2 = $row->task_complition_time;

										$diff = abs(strtotime($date2) - strtotime($date1));

										$years = floor($diff / (365*60*60*24));
										$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
										$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
										$query = $this->db->select('holiday_id')->from('prestogroup_holidays')->where('holiday_date BETWEEN "'. date('Y-m-d', strtotime($date1)). '" and "'. date('Y-m-d', strtotime($date2)).'"')->get();
										
								    $holidays= count($query->result());
										$totaldays = $days-$holidays;
										echo $totaldays;
										?></td>
										<?php 
										$planneddate = $row->planned_date;
										$task_complition_time = $row->task_complition_time;
										if($task_complition_time>$planneddate){
											
											$background = "background-color:red; color:#fff;";
										}else{
											$background = "background-color:green; color:#fff;";
										}
										?>
										<td style="<?php echo $background;?>"><?php if($row->task_complition_time=='0000-00-00 00:00:00'){}else{
											echo $row->task_complition_time;
										}?></td>
										<td><?php 
										    $qqq = $this->db->select('remarks')->from('dynamic_form_data_remarks')->where('data_id',$row->id)->get();
										    if($qqq->num_rows()>0){
										        foreach($qqq->result() as $remarksinfo){
										            echo $remarksinfo->remarks.
										            "<br/>";
										        }
										    }
										    ?></td>
										<td><?php echo $row->ref_remarks;?></td>
										<td> <?php 
										    $query = $this->db->select('filename')->from('dynamic_data_docketfile')->where('record_id',$row->id)->where('form_id','6')->limit('1')->order_by('id','desc')->get();
										    if($query->num_rows()>0){
										       foreach($query->result() as $docdata);
										       ?>
										       <a href="<?php echo docketpath;?><?php echo $docdata->filename;?>" download><span>Click here to download</span></a>
										       <?php 
										    }
										    ?></td>
										<?php 
										}?>
										
								</tr>
									<?php $k++;}?>
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
$('#example').dataTable({
 "bProcessing": false,
	fixedHeader: true,
 "pagination":true,
  dom: 'Bfrtip',
        buttons: [
            'excel'
        ]
 });   
});

</script>

<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
</body>
</html>