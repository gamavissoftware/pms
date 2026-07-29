<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Dashboard</title>

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
						 <a href="<?php echo page_url;?>Form/view_history/<?php echo $this->uri->segment(3);?>" target="_blank"><span class="btn btn-warning btn-xs">VIEW HISTORY</span></a>
						 
						 <?php 
                      $query = $this->db->select('dashboard_video_link')->from('dynamic_forms')->where('id',$this->uri->segment(3))->get();
                      foreach($query->result() as $row);
                      ?>
                      <a href="<?php echo $row->dashboard_video_link;?>" target="_blank"><span class="btn btn-success btn-xs">Click here to view Video <i class="fa fa-video-camera" style="font-size:15px"></i></span></a>
						 </div>
                           
                            <h4 class="page-title text-center"><?php $query = $this->db->select('a.id, a.ref_no, a.form_title, a.description, a.department_id, b.department_id, b.department')->from('dynamic_forms a')->join('departments b','a.department_id=b.department_id','left')->where('a.form_running_status','0')->where('a.id',$this->uri->segment(3))->get();
							foreach($query->result() as $row){
								echo "<span style='font-size:18px'>".strtoupper($row->form_title)."</span>";
								echo "<span style='font-size:14px'>  (".strtoupper($row->description).")</span>";
							}?></h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
	<?php echo $this->session->flashdata('message'); 
	$user_id =$this->session->userdata['logged_in']['user_id'];	
	?>
		<div class="row">
		 
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <?php 
						$query = $this->db->select('user_id, ref_no')->from('dynamic_forms')->where('user_id',$user_id)->where('id',$this->uri->segment(3))->get();
						if($query->num_rows()>0){
						
						?>
						
                            <table id="example" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
								<?php 
								$Q2 = $this->db->select('tatappl,form_type, ref_no')->from('dynamic_forms')->where('id',$this->uri->segment(3))->get();
								foreach($Q2->result() as $forminfo);
									$tatapp=$forminfo->tatappl;
								$que=$this->db->select('form_id, form_label, field_type')->from('master_dynamic_fields')->where('form_id',$this->uri->segment(3))->get();
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
											<th>UPDATE REMARKS</th>
											<th>MARK AS DONE</th>
												<?php if($this->uri->segment(3)=='6'){
								    ?>
								    <th>Upload Docket Image</th>
								    <?php 
								}?>
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
								<th>BUSINESS LOCATION</th>
								
							
                                </tr>
                                </thead>
                                <tbody>
								<?php 
								$k=1;
									$q=$this->db->select('a.id, a.responseid, a.form_id, a.formdata,b.mark_as_done, b.record_id,a.business_location, a.planned_date, a.ref_remarks, a.added_on')->from('dynamic_form_data a')->join('dynamic_form_data_mark_done b','a.id=b.record_id','left')->where('a.form_id',$this->uri->segment(3))->where('a.work_status','0')->order_by('a.id','desc')->get();
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
									
									<td><?php echo $finalvalue;
									
									?></td>
									
										<?php }?>
										<?php 
									
									$businessloc = $row->business_location;
									
									$query = $this->db->select('company_name')->from('business_location')->where('business_loc_id',$businessloc)->get();
									foreach($query->result() as $businesslocations);
									$businesslocation = $businesslocations->company_name;
										?>
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
										
										$query = $this->db->select('holiday_id')->from('prestogroup_holidays')->where('holiday_date BETWEEN "'. date('Y-m-d', strtotime($date1)). '" and "'. date('Y-m-d', strtotime($date2)).'"')->get();
										
								    $holidays= count($query->result());
										$totaldays = $days-$holidays;
										echo $totaldays;
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
										     <form method="post" action="<?php echo page_url;?>Form/update_ref_remarks/<?php echo $row->id;?>/<?php echo $this->uri->segment(3);?>">
										            <div class="row">
										                <div class="col-md-12">
										                    <div class="form-group">
										      <label>SOLUTION & RESOLUTION</label><br>
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
										<td> <?php 
										    $qqq = $this->db->select('remarks')->from('dynamic_form_data_remarks')->where('data_id',$row->id)->get();
										    if($qqq->num_rows()>0){
										        foreach($qqq->result() as $remarksinfo){
										            echo $remarksinfo->remarks;
										        }
										    }
										    ?>
										    <?php if($qqq->num_rows()>0){}else{?>
										    <span class="btn btn-warning btn-xs" data-toggle="modal" data-target="#taskremarkid<?php echo $row->id;?>">UPDATE REMARKS</span><?php }?></td>
										
										<div id="taskremarkid<?php echo $row->id;?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                <form id="loginForm" method="post" action="<?php echo page_url;?>Form/update_remark_on_given_task/<?php echo $row->id;?>/<?php echo $this->uri->segment(3);?>"  enctype="multipart/form-data">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Update Progress on Given Task  </h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
												<div class="col-md-12">
													<div class="form-group">
														 <label for="field-2" class="control-label">Update Remarks</label>
														 <span id="error_remarks" style="color:red;"></span><textarea class="form-control" name="remarks" id="remarks" required="required"></textarea>
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
                            </div>
                            
                            	
                            
										<td>
										   <?php /*if($forminfo->ref_no=='1'){
										        if($row->ref_remarks!==''){
										            echo $row->mark_as_done;
										        }else{
										            
										        }
										    }else{
										        echo $row->mark_as_done;
										    }*/ ?>
										    
										    <a href='<?php echo page_url;?>Form/task_marked_as_done/<?php echo $row->id;?>/<?php echo $this->uri->segment(3);?>'><span class='btn btn-success btn-xs'>MARK AS DONE</span></a>

										   </td>
										   
										   <?php 
										if($this->uri->segment(3)=='6'){?>
										<td>
										    
										    <?php 
										    $query = $this->db->select('filename')->from('dynamic_data_docketfile')->where('record_id',$row->id)->where('form_id','6')->limit('1')->order_by('id','desc')->get();
										    if($query->num_rows()>0){
										       foreach($query->result() as $docdata);
										       ?>
										       <a href="<?php echo docketpath;?><?php echo $docdata->filename;?>" download><span>Click here to download</span></a>
										       <?php 
										    }
										    ?>
										    
										    <span class="btn btn-warning btn-xs" data-toggle="modal" data-target="#docketfile<?php echo $row->id;?>">Upload Image</span></td>
										
										<div id="docketfile<?php echo $row->id;?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                <form id="loginForm" method="post" action="<?php echo page_url;?>Form/upload_docket_image/<?php echo $row->id;?>/<?php echo $this->uri->segment(3);?>"  enctype="multipart/form-data">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">UPLOAD DOCKET IMAGE</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
												<div class="col-md-12">
													<div class="form-group">
														 <label for="field-2" class="control-label">Docket File attachment</label>
														 <span id="error_remarks" style="color:red;"></span><input type="file" class="form-control" value="" name="photo" id="photo" required>
														 <input type="hidden" name="recordids" value="<?php echo $row->id;?>">
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
                            </div>
										<?php }
										?>
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
											<td><?php echo $businesslocation;?></td>
										<?php 
										}?>
										
									
								</tr>
									<?php $k++;}?>
                                </tbody>
                            </table><?php }else{
                            	
									$qrysss = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','13')->where('submodule_access','1')->get();
									if($qrysss->num_rows()>0){
									
                            
                            ?>
                            <table id="example" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
								<?php 
								$Q2 = $this->db->select('tatappl,form_type, ref_no')->from('dynamic_forms')->where('id',$this->uri->segment(3))->get();
								foreach($Q2->result() as $forminfo);
								$tatapp=$forminfo->tatappl;
								$que=$this->db->select('form_id, form_label, field_type')->from('master_dynamic_fields')->where('form_id',$this->uri->segment(3))->get();
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
										
										<?php }?>
										<!--<th>UPDATE REMARKS</th>-->
											
											<?php 
											if($forminfo->form_type=='2'){
												?>
												<th>REMARKS</th>
												<?php 
											}
											?>
											<th>RESPONSE ID</th> 
											
											<?php 
									}
								?>
								<?php if($this->uri->segment(3)=='6'){
								    ?>
                                   <th>Upload Docket Image</th>
								<?php }?>
								<th>BUSINESS LOCATION</th>								
                                </tr>
                                </thead>
                                <tbody>
								<?php 
								$k=1;
									$q=$this->db->select('a.id,a.responseid,a.form_id, a.formdata,b.mark_as_done, b.record_id, a.ref_remarks,a.business_location, a.planned_date, a.added_on')->from('dynamic_form_data a')->join('dynamic_form_data_mark_done b','a.id=b.record_id','left')->where('a.form_id',$this->uri->segment(3))->where('a.work_status','0')->order_by('a.id','desc')->get();
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
									
									<td><?php echo $finalvalue;
									
									$businessloc = $row->business_location;
									
									$query = $this->db->select('company_name')->from('business_location')->where('business_loc_id',$businessloc)->get();
									foreach($query->result() as $businesslocations);
									$businesslocation = $businesslocations->company_name;
										
									
									?></td>
									
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
										$query = $this->db->select('holiday_id')->from('prestogroup_holidays')->where('holiday_date BETWEEN "'. date('Y-m-d', strtotime($date1)). '" and "'. date('Y-m-d', strtotime($date2)).'"')->get();
										
								        $holidays= count($query->result());
										$totaldays = $days-$holidays;
										echo $totaldays;
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
										     <form method="post" action="<?php echo page_url;?>Form/update_ref_remarks/<?php echo $row->id;?>/<?php echo $this->uri->segment(3);?>">
										            <div class="row">
										                <div class="col-md-12">
										                    <div class="form-group">
										      <label>SOLUTION & RESOLUTION</label><br>
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
									<!--	<td>
										    <?php 
										    $qqq = $this->db->select('remarks')->from('dynamic_form_data_remarks')->where('data_id',$row->id)->get();
										    if($qqq->num_rows()>0){
										        foreach($qqq->result() as $remarksinfo){
										            echo $remarksinfo->remarks;
										        }
										    }
										    ?>
										    <span class="btn btn-warning btn-xs" data-toggle="modal" data-target="#taskremarkid<?php echo $row->id;?>">UPDATE REMARKS</span></td>-->
										
										<div id="taskremarkid<?php echo $row->id;?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                <form id="loginForm" method="post" action="<?php echo page_url;?>Form/update_remark_on_given_task/<?php echo $row->id;?>/<?php echo $this->uri->segment(3);?>"  enctype="multipart/form-data">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Update Progress on Given Task  </h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
												<div class="col-md-12">
													<div class="form-group">
														 <label for="field-2" class="control-label">Update Remarks</label>
														 <span id="error_remarks" style="color:red;"></span><textarea class="form-control" name="remarks" id="remarks" required="required"></textarea>
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
											<td><?php echo $businesslocation;?></td>
										<?php 
										}?>
										<?php if($this->uri->segment(3)=='6'){
								    ?>
											<td> <?php 
										    $query = $this->db->select('filename')->from('dynamic_data_docketfile')->where('record_id',$row->id)->where('form_id','6')->limit('1')->order_by('id','desc')->get();
										    if($query->num_rows()>0){
										       foreach($query->result() as $docdata);
										       ?>
										       <a href="<?php echo docketpath;?><?php echo $docdata->filename;?>" download><span>Click here to download</span></a>
										       <?php 
										    }
										    ?></td>
											
										    <?php }?>
											
								</tr>
									<?php $k++;}?>
                                </tbody>
                            </table>
                            
                            
                            <?php }}?>
                            
                            <?php 
						$query = $this->db->select('a.user_id, b.ref_no')->from('dynamic_form_dashboard_access a')->join('dynamic_forms b','a.form_id=b.id','left')->where('a.user_id',$user_id)->where('a.form_id',$this->uri->segment(3))->get();
						if($query->num_rows()>0){
						    
						    
						   
						?>
                           <table id="example" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
								<?php 
								$Q2 = $this->db->select('tatappl,form_type, ref_no')->from('dynamic_forms')->where('id',$this->uri->segment(3))->get();
								    foreach($Q2->result() as $forminfo);
									$tatapp=$forminfo->tatappl;
								
								
									$que=$this->db->select('form_id, form_label, field_type')->from('master_dynamic_fields')->where('form_id',$this->uri->segment(3))->get();
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
											<?php if($this->uri->segment(3)=='6'){
								    ?>
											<th>Upload Docket Image</th>
											<?php }?>
											<?php if($forminfo->ref_no=='1'){
											    ?>
											  <th>REF NO. (REMARKS)</th>  
											  <?php 
											}?>
											
											
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
								<th>BUSINESS LOCATION</th>
								
                                </tr>
                                </thead>
                                <tbody>
								<?php 
								$k=1;
									$q=$this->db->select('a.id, a.responseid, a.form_id, a.formdata,b.mark_as_done, b.record_id,a.business_location, a.planned_date, a.ref_remarks, a.added_on')->from('dynamic_form_data a')->join('dynamic_form_data_mark_done b','a.id=b.record_id','left')->where('a.form_id',$this->uri->segment(3))->where('a.work_status','0')->get();
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
										$businessloc = $row->business_location;
										$businessloc = $row->business_location;
									
									$query = $this->db->select('company_name')->from('business_location')->where('business_loc_id',$businessloc)->get();
									foreach($query->result() as $businesslocations);
									$businesslocation = $businesslocations->company_name;
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
										$query = $this->db->select('holiday_id')->from('prestogroup_holidays')->where('holiday_date BETWEEN "'. date('Y-m-d', strtotime($date1)). '" and "'. date('Y-m-d', strtotime($date2)).'"')->get();
										
								    $holidays= count($query->result());
										$totaldays = $days-$holidays;
										echo $totaldays;
										?></td>
											<?php
											}
											?>
											<?php if($this->uri->segment(3)=='6'){
								    ?>
												<td> <?php 
										    $query = $this->db->select('filename')->from('dynamic_data_docketfile')->where('record_id',$row->id)->where('form_id','6')->limit('1')->order_by('id','desc')->get();
										    if($query->num_rows()>0){
										       foreach($query->result() as $docdata);
										       ?>
										       <a href="<?php echo docketpath;?><?php echo $docdata->filename;?>" download><span>Click here to download</span></a>
										       <?php 
										    }
										    ?></td><?php }?>
											<?php if($forminfo->ref_no=='1'){
											    ?>
										<td>
										    <?php 
										
										    if($row->ref_remarks==''){
										        ?>
										     <form method="post" action="<?php echo page_url;?>Form/update_ref_remarks/<?php echo $row->id;?>/<?php echo $this->uri->segment(3);?>">
										            <div class="row">
										                <div class="col-md-12">
										                    <div class="form-group">
										      <label>SOLUTION & RESOLUTION</label><br>
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
											<td><?php echo $businesslocation;?></td>
										<?php 
										}?>
									
										
								</tr>
									<?php $k++;}?>
                                </tbody>
                            </table>
                            <?php }?>
                        </div>
                    </div>
                </div>
                <!-- end row -->
                
                <?php
                if($this->uri->segment(3)=='58')
                {
                
                ?>
                <div class="row">
                <div class="col-md-12">
                <div class="card-box table-responsive">
                <h4 class="text-center">QC Rejected Items</h4>
                <table id="example123" class="table table-striped table-bordered manglesh">
                <thead>
                <tr>
                <th>Sr No.</th>
                <th>PARTY NAME</th>
                <th>PO NO.</th>
                <th>ITEM</th>
                <th>FINCODE</th>
                <th>SPECIFICATION</th>
                <th>QUANTITY</th>
                <th>GENERATE</th>
                
                
                </tr>
                </thead>
                
                
                <tbody>
                
                </tbody>
                </table>
                </div> 
                </div>
                
                
                </div>
                <?php
                }
                ?>
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
 "pagination":true
 });   
});

</script>

 <script>
$( document ).ready(function() {
 
$('#example123').dataTable({
 "bProcessing": false,
 "pagination":true,
fixedHeader: true,
   scrollCollapse: true,
   fixedColumns:   {
            leftColumns: 3
        },
 "sAjaxSource": "<?php echo page_url;?>Reporting/rejecteditemrequestlist",
 "aoColumns": [
					{ mData: 'sr_no' },
	
					{ mData: 'partyname'},
					{ mData: 'ponumber'},
					{ mData: 'item'},
					{ mData: 'fincode'},
					{ mData: 'specialization'},
					{ mData: 'qty'},
					{ mData: 'challan'}
				
						
						
                ]
        });   
});


function create(id)
{
	if(confirm('Are you sure you want to generate debit note?'))
	{
		document.location="<?php echo page_url;?>Store/generatedebit_note/"+id;
		return true;
	}
	
	
}

function createchallan(id)
{
	var ch=$("#chtype"+id).val();
	if(ch==1)
	{
		
	
		document.location="<?php echo page_url;?>Store/createrejectionchallan/"+id;
	}else
	{
		document.location="<?php echo page_url;?>Store/createdebitnote/"+id;
		
	}
	
	
}
</script>


<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
</body>
</html>