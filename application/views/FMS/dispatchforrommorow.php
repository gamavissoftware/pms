<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>DISPATCH FOR TOMMOROW ORDERS</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.dataTables.min.css">
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
		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script>
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
				font-size:11px;
				font-weight:bold;
			}
table tbody tr td {
  font-size: 11px;
  color:#000;
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
  left: 50%;
  margin-left: -32px;
  margin-top: -32px;
  position: absolute;
  top: 50%;
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
						<?php 
						$user_id =$this->session->userdata['logged_in']['user_id'];
						?>
                           <!--<a href="<?php echo page_url;?>Form/data_report/6/<?php echo $user_id;?>" target="_blank"><span class="btn btn-success btn-xs pull-right">Service Dispatch for Tomorrow</span></a>-->
                            <h4 class="page-title text-center">DISPATCH FOR TOMMOROW - SALES</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
		<div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th>PRINT PACKING LABEL</th>
                                    <th style="width:50%">INSTRUMENTS</th>
                                    <th>COMPANY NAME</th>
                                    <th>ADDRESS WITH PINCODE</th>
                                      <th>EMAIL ID</th>
									<th>MOBILE NUMBER</th>
                                    <th>INTERNAL ORDER NUMBER</th>
                                    <th>FREIGHT TYPE</th>
                                     <th>ORDER TYPE</th>
                                     <th>REGION MARKETING PERSON</th>
                                     <th>PO NUMBNER</th>
                                  
									<!--<th>PAYMENT TERMS</th>
									<th>INSTALLATION CHARGES TYPE</th>-->
                                    <th>PACKING CHARGES</th>
									<th>TIMESTAMP</th>
									<th>PLANNED TIME</th>
									<th>ACTUAL TIME</th>
									<th>REMARKS</th>
									<th>SEND TO ACCOUNTS</th>
									<th>MARK DONE</th>
									<th>DOCKET IMAGE</th>
									
                                </tr>
                                </thead>


                                <tbody>
								
                                </tbody>
                            </table>
                        </div>
                        
                      <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right" style="margin-top:30px">
						 <!--<a href="<?php echo page_url;?>Form/view_history/<?php echo $this->uri->segment(3);?>" target="_blank"><span class="btn btn-warning btn-xs">VIEW HISTORY</span></a>-->
						 
						 <?php 
                      $query = $this->db->select('dashboard_video_link')->from('dynamic_forms')->where('id','6')->get();
                      foreach($query->result() as $row);
                      ?>
                      <!--<a href="<?php echo $row->dashboard_video_link;?>" target="_blank"><span class="btn btn-success btn-xs">Click here to view Video <i class="fa fa-video-camera" style="font-size:15px"></i></span></a>-->
						 </div>
                           
                            <h4 class="page-title text-center">DISPATCH FOR TOMORROW- SERVICE</h4>
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
						$query = $this->db->select('user_id, ref_no')->from('dynamic_forms')->where('user_id',$user_id)->where('id','6')->get();
						if($query->num_rows()>0){
						?>
						
                            <table id="example1" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
								<?php 
								$Q2 = $this->db->select('tatappl,form_type, ref_no')->from('dynamic_forms')->where('id','6')->get();
								foreach($Q2->result() as $forminfo);
									$tatapp=$forminfo->tatappl;
								
								
									$que=$this->db->select('form_id, form_label, field_type')->from('master_dynamic_fields')->where('form_id','6')->get();
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
										
								    <th>Upload Docket Image</th>
								    
											<?php 
											if($forminfo->form_type=='2'){
												?>
												<th>REMARKS</th>
												<?php 
											}
											?>
											<th>RESPONSEID</th>
											<th>CURRENT STATUS</th>
											<?php 
									}
								?>	
                                </tr>
                                </thead>
                                <tbody>
								<?php 
								$k=1;
									$q=$this->db->select('a.id, a.responseid, a.form_id, a.formdata,b.mark_as_done, b.record_id, a.planned_date, a.ref_remarks, a.added_on,a.work_status')->from('dynamic_form_data a')->join('dynamic_form_data_mark_done b','a.id=b.record_id','left')->where('a.form_id','6')->where('a.work_status','0')->order_by('a.id','desc')->get();
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
										    <?php if($forminfo->ref_no=='1'){
										        if($row->ref_remarks!==''){
										            echo $row->mark_as_done;
										        }else{
										            
										        }
										    }else{
										        echo $row->mark_as_done;
										    }?>
										   </td>
										   
										    
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
											if($row->work_status=='0')
											{
											    $staa="<span style='color:red'>Pending</span>";
											}else
											{
											  $staa="<span style='color:red'>Completed</span>"; 
											}
											?>
												<td><?php echo $staa;?></td>
											
										<?php 
										}?>
										
										
								</tr>
									<?php $k++;}?>
                                </tbody>
                            </table><?php }else{
                            	
									$qrysss = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','13')->where('submodule_access','1')->get();
									if($qrysss->num_rows()>0){
									
                            
                            ?>
                            <table id="example1" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
								<?php 
								$Q2 = $this->db->select('tatappl,form_type, ref_no')->from('dynamic_forms')->where('id','6')->get();
								foreach($Q2->result() as $forminfo);
								$tatapp=$forminfo->tatappl;
								$que=$this->db->select('form_id, form_label, field_type')->from('master_dynamic_fields')->where('form_id','6')->get();
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
											<th>CURRENT STATUS</th> 
											<?php 
									}
								?>
                                   
									
                                </tr>
                                </thead>
                                <tbody>
								<?php 
								$k=1;
									$q=$this->db->select('a.id,a.responseid,a.form_id, a.formdata,b.mark_as_done, b.record_id, a.ref_remarks, a.planned_date, a.added_on,a.work_status')->from('dynamic_form_data a')->join('dynamic_form_data_mark_done b','a.id=b.record_id','left')->where('a.form_id','6')->where('a.work_status','0')->order_by('a.id','desc')->get();
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
										
										$date1 = date('Y-m-d',strtotime($row->planned_date));
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
												<?php
											if($row->work_status=='0')
											{
											    $staa="<span style='color:red'>Pending</span>";
											}else
											{
											  $staa="<span style='color:red'>Completed</span>"; 
											}
											?>
												<td><?php echo $staa;?></td>
										<?php 
										}?>
										
								</tr>
									<?php $k++;}?>
                                </tbody>
                            </table>
                            
                            
                            <?php }}?>
                            
                            <?php 
						$query = $this->db->select('a.user_id, b.ref_no')->from('dynamic_form_dashboard_access a')->join('dynamic_forms b','a.form_id=b.id','left')->where('a.user_id',$user_id)->where('a.form_id','6')->get();
						if($query->num_rows()>0){
						    
						    
						   
						?>
                           <table id="example1" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
								<?php 
								$Q2 = $this->db->select('tatappl,form_type, ref_no')->from('dynamic_forms')->where('id','6')->get();
								    foreach($Q2->result() as $forminfo);
									$tatapp=$forminfo->tatappl;
								
								
									$que=$this->db->select('form_id, form_label, field_type')->from('master_dynamic_fields')->where('form_id','6')->get();
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
											
											
											<?php 
											if($forminfo->form_type=='2'){
												?>
												<th>REMARKS</th>
												<?php 
											}
											?>
											<th>RESPONSEID</th>
											<th>CURRENT STATUS</th>
											<?php 
									}
								?>	
                                </tr>
                                </thead>
                                <tbody>
								<?php 
								$k=1;
									$q=$this->db->select('a.id, a.responseid, a.form_id, a.formdata,b.mark_as_done, b.record_id, a.planned_date, a.ref_remarks, a.added_on,a.work_status')->from('dynamic_form_data a')->join('dynamic_form_data_mark_done b','a.id=b.record_id','left')->where('a.form_id','6')->where('a.work_status','0')->get();
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
											<?php
											if($row->work_status=='0')
											{
											    $staa="<span style='color:red'>Pending</span>";
											}else
											{
											  $staa="<span style='color:red'>Completed</span>"; 
											}
											?>
												<td><?php echo $staa;?></td>
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
 
 
 	<div class="row">
		 
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <h4 class="text-center page-title">DISPATCH FOR TOMORROW -SERVICE HISTORY</h4><hr>
                            <table id="example3" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
								<?php 
									$que=$this->db->select('form_id, form_label, field_type')->from('master_dynamic_fields')->where('form_id','6')->get();
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
									$q=$this->db->select('a.id, a.form_id,a.ref_remarks, a.formdata,b.mark_as_done, b.record_id, a.planned_date, a.added_on, a.task_complition_time')->from('dynamic_form_data a')->join('dynamic_form_data_mark_done b','a.id=b.record_id','left')->where('a.form_id','6')->where('a.work_status','1')->order_by('a.id','desc')->get();
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
										echo $days;
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
                    
                    </div>
                </div>
                
                
                <div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
	<form id="popfrm" action="" method="post" enctype="multipart/form-data">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Dispatching Order for <span id="setiono"></span></h4>
      </div>
      <div class="modal-body">
	  <div class="row">

	  		<div class="col-md-6">
		<div class="form-group">
		<label>Shipment Mode</label>
		<select name="shipment" class="form-control mand" id="shipment" required onchange="checkfortempo(this.value);">


	</select>
		</div>
		</div>
<script>
function checkfortempo(vaal)
{

	if(vaal=='By Tempo')
	{

$("#doc").html(' (optional)');
$("#docketimage").attr('required',false);

	
	}else{

$("#doc").html('');
$("#docketimage").attr('required',true);

	}

}
</script>



        <div class="col-md-6">
	   <div class="form-group">
	   <label>Docket Image<span id="doc"></span></label>
	   <input type="file" name="docketno" id="docketimage" class="form-control validateDocketFile" placeholder="ENTER DOCKET NO." onchange="validateDocketFile()" accept="application/pdf" >
	   </div>
	   </div>
	   
	   
	      <div class="col-md-6">
	   <div class="form-group">
	   <label>Bill No.</label>
	   <input type="text" name="billno" class="form-control mand" placeholder="ENTER Bill NO." required >
	   </div>
	   </div>

 <div class="col-md-6">
	   <div class="form-group">
	   <label>Bill Copy Image</label>
	   <input type="file" name="bcopy" id="bcopy" class="form-control validateBillFile" placeholder="" required onchange="validateBillFile()" accept="application/pdf">
	   </div>
	   </div>

	      <div class="col-md-6">
	   <div class="form-group">
	   <label>Bill Date.</label>
	   <input type="date" name="billdate" class="form-control mand" placeholder="ENTER Bill Date" required>
	   </div>
	   </div>


	      <div class="col-md-6">
	   <div class="form-group">
	   <label>Total Packet.</label>
	   <input type="number" name="packet" id="totpack" class="form-control mand allow_numeric" placeholder="ENTER Total Packet" required>
	   </div>
	   </div>

	

	    <div class="col-md-6">
	   <div class="form-group">
	   <label>Docket No./ Temp No. <span style="color:red" id="docno"></span></label>
	   <input type="text" name="docketnumber" id="docketnumber" class="form-control mand " placeholder="ENTER DOCKET NO." required>
	   </div>
	   </div>

	      <div class="col-md-6">
	   <div class="form-group">
	   <label>DOD Amount</label>
	   <input type="text" name="dodamount" class="form-control mand allow_decimal" placeholder="ENTER DOD Amount" required>
	   </div>
	   </div>



	      <div class="col-md-6">
	   <div class="form-group">
	   <label>Freight Amount.</label>
	   <input type="text" name="freightamount" class="form-control mand allow_decimal" placeholder="ENTER Freight Amount" required>
	   </div>
	   </div>

	   
		</div>
      </div>
      <div class="modal-footer">
        <input type="submit" class="btn btn-success" value="submit">
      </div>
    </div>
	</form>

  </div>
</div>





  <div id="myModalforaccounts" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
	<form id="popfrm" action="<?php echo page_url;?>Reporting/adddetailsforaccounts" method="post" enctype="multipart/form-data">
		<input type="hidden" name="orderid" id="orderid" value="">
		<input type="hidden" name="iono" id="iono" value="">
		<input type="hidden" name="segment" value="<?php echo $this->uri->segment(3);?>">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Send Order for Billing to Accounts for Internal Order <span id="setiono1"></span></h4>
      </div>
      <div class="modal-body">
	  <div class="row">

 <div class="col-md-6">
	   <div class="form-group">
	   <label>No. of Packets <span style="color:red">*</span></label>
	   <input type="number" name="packets" id="packets" class="form-control" min="1" placeholder="No. Of Packets" required>
	   </div>
	   </div>

	  		<div class="col-md-6">
		<div class="form-group">
		<label>Transport Type  <span style="color:red">*</span></label>
		<select name="shipment" class="form-control mand" id="shipmentsss" required onchange="checkfortempo(this.value);">
		<option value=''>Select</option>
		<option value='By Road'>By Road</option>
		<option value='By Air'>By Air</option>
		<option value='By Hand'>By Hand</option>
		<option value='By Tempo'>By Tempo</option>


	</select>
		</div>
		</div>
<script>
function checkfortempo(vaal)
{

$("#shipmentpartner").prop("selectedIndex", 0);
	/** DEFAULT **/
$("#shiprow").css('display','none');
$("#shipmentpartner").attr('required',false);
$(".customerrow").css('display','none');
$("#customername").attr('required',false);
$("#customercontact").attr('required',false);
$("#othershiprow").css('display','none');
$("#othershipmentpartner").attr('required',false);
$(".temporow").css('display','');
$(".tempono").attr('required',true);
$(".settemp").text('');
	/** END **/



	if(vaal=='By Road' || vaal=='By Air' )
	{

$("#shiprow").css('display','');
$("#shipmentpartner").attr('required',true);

	
	}else if(vaal=='By Hand')
	{

$(".customerrow").css('display','');
$("#customername").attr('required',true);
$("#customercontact").attr('required',true);

	}else if(vaal=='By Tempo')
	{

$(".temporow").css('display','');
$(".tempono").attr('required',true);
$(".settemp").text('*');


	}else{

$("#shiprow").css('display','none');
$("#shipmentpartner").attr('required',false);


	}

}
</script>

	

	  		<div class="col-md-6" id="shiprow" style="display:none">
		<div class="form-group">
		<label>Transporters <span style="color:red">*</span></label>
		<select name="shipmentpartner" class="form-control mand" id="shipmentpartner" required onchange="checkforother(this.value);">
		<option value=''>Select</option>
		<option value='GATI'>GATI</option>
		<option value='DTDC'>DTDC</option>
		<option value='TCI'>TCI</option>
		<option value='TRACK ON'>TRACK ON</option>
		<option value='BLUE DART'>BLUE DART</option>
		<option value='OTHER'>OTHER</option>


	</select>
		</div>
		</div>

		<script>
function checkforother(vaal)
{

if(vaal=='OTHER')
{

$("#othershiprow").css('display','');
$("#othershipmentpartner").attr('required',true);


}else{

$("#othershiprow").css('display','none');
$("#othershipmentpartner").attr('required',false);


}

}
</script>




        <div class="col-md-6" id="othershiprow" style="display: none;">
	   <div class="form-group">
	   <label>Other Transporters <span style="color:red">*</span></label>
	   <input type="text" name="othershipmentpartner" id="othershipmentpartner" class="form-control" placeholder="Other Transporters" >
	   </div>
	   </div>


	       <div class="col-md-6 customerrow" style="display: none;">
	   <div class="form-group">
	   <label>Customer Name <span style="color:red">*</span></label>
	   <input type="text" name="customername" id="customername" class="form-control" placeholder="Customer Name" >
	   </div>
	   </div>


	       <div class="col-md-6 customerrow" style="display: none;">
	   <div class="form-group">
	   <label>Customer Contact <span style="color:red">*</span></label>
	   <input type="text" name="customercontact" id="customercontact" class="form-control" placeholder="Customer Contact" >
	   </div>
	   </div>



	     <div class="col-md-6 temporow">
	   <div class="form-group">
	   <label>Tempo Number <span class="settemp" style="color:red"></span></label>
	   <input type="text" name="tempono" id="tempono" class="form-control" placeholder="Tempo Number" >
	   </div>
	   </div>
	   
	   
	  
	   
		</div>
      </div>
      <div class="modal-footer">
        <input type="submit" class="btn btn-success" value="submit">
      </div>
    </div>
	</form>

  </div>
</div>

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
        <script src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>
<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
		
	
	  <script>
	  function initializeSelect2(selectElementObj) {
         $('.'+selectElementObj).select2({});
         
      }
	  </script>
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
 function initializeSelect2() {
    $('.select3').select2({ });
  }
  
$('#example').dataTable({
 "bProcessing": false,
 "pagination":true,
fixedHeader: true,
   scrollCollapse: true,
   fixedColumns:   {
            leftColumns: 3
        },
 "sAjaxSource": "<?php echo page_url;?>Reporting/dispatchfortommorow_order_list/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>",
 "aoColumns": [
                    { mData: 'sr_no' } ,
                    { mData: 'packinglabel' },
                    { mData: 'itemname' },
                    { mData: 'company_name' },
                     { mData: 'address' },
                      { mData: 'email' },
                    { mData: 'mobile_number' },
                     { mData: 'internal_order_no' },
                     { mData: 'freigntcharges' },
                     { mData: 'order_type' },
                    { mData: 'marketing_person' },
                    { mData: 'po_number' }, 
                  
                    /**{ mData: 'payment_terms' },
                    { mData: 'installation_charges' }, **/
                    { mData: 'packingcharges' },
                     { mData: 'added_on' },
                    { mData: 'plannedtime' },
                    { mData: 'actualtime' },

                     { mData: 'remarks' },
                       { mData: 'sendforbilling' },
                    { mData: 'closeorder' },
                    { mData: 'docket' }
                    
                    
						
						
                ]
        });  
        
         $('#example1').dataTable({
 "bProcessing": false,
 "pagination":true
 });
  $('#example3').dataTable({
 "bProcessing": false,
 "pagination":true
 });
});



function markstagedone(orderid,internalorderno,ser)
{
	$("#myModal").modal('show'); 
	$("#setiono").html(internalorderno);

	/** AJAX TO GET SELECTED DETAILS **/

		$.ajax({
		type:"post",
		url:"<?php echo page_url;?>Reporting/getfilleddetails",
		data:"internalorderno="+internalorderno,
		success:function(data){
	
		var array = data.split("|");
	
		var shipment=array[1];

		var totpack=array[4];
		var dockewtn=array[2];
		var marketing=array[5];
		$("#shipment").html('<option value="'+shipment+'">'+shipment+'</option>');
		if(shipment=='By Road' || shipment=='By Tempo' || shipment=='By Air' )
		{
		$("#docketnumber").attr('required',true);
		$("#docno").text('*');
		}else
		{
		$("#docketnumber").attr('required',false);
		$("#docno").text('');
		}
		$("#totpack").val(totpack);
		$("#totpack").attr('readonly',true);
		$("#docketnumber").val(dockewtn);

		if(marketing=='37')
		{
			$("#docketimage").attr('required',false);
			$("#bcopy").attr('required',false);
		}


		}
		});

	/** END **/


	/**$("#odid").val(internalorderno);
	$("#ser").val(ser); **/
	var url="<?php echo page_url;?>Reporting/markfinalpacked/"+orderid+"/"+ser;
	 $('#popfrm').attr('action', url);
	
	
}



function markstagedoneoLDD(orderid,compyname,internalorderno,ser)
{
	if(confirm('Confirm you want to close following order? \nCustomer '+compyname+'\n Internal Order No. '+internalorderno))
	{
		document.location="<?php echo page_url;?>Reporting/markfinalpacked/"+orderid+"/"+ser;
		return true;
		
	}else
	{
		return false;
	}
}


function validateForm() {
  var isValid = true;
  $('.mand').each(function() {
    if ($(this).val() =='' )
        isValid = false;
  });

	if(isValid==false)
	{
		alert('All Fields are required');
	return isValid;

	}else
	{
	return isValid;
	}
}


$(".allow_decimal").on("input", function(evt) {
   var self = $(this);
   self.val(self.val().replace(/[^0-9\.]/g, ''));
   if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
   {
     evt.preventDefault();
   }
 });


(".allow_numeric").on("input", function(evt) {
    var self = $(this);
    self.val(self.val().replace(/[^\d].+/, ""));
    if ((evt.which < 48 || evt.which > 57)) 
     {
	   evt.preventDefault();
     }
 });


function updatedetailsforaccount(orderid,iono,random)
{

$("#myModalforaccounts").modal('show');
$("#orderid").val(orderid);
$("#iono").val(iono);
$("#setiono1").html(iono);


}


function validateDocketFile() {
		var ext = $('.validateDocketFile').val().split('.').pop().toLowerCase();
		
			if($.inArray(ext, ['pdf','PDF']) == -1) {
			    alert('Invalid Extension!'); 
			    $('.validateDocketFile').val('');
			}

	}

	function validateBillFile() {
		var ext1 = $('.validateBillFile').val().split('.').pop().toLowerCase();
		if($.inArray(ext1, ['pdf','PDF']) == -1) {
			    alert('Invalid Extension!');
			    $('.validateBillFile').val('');
			}
	}
	
	
</script>


<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready
</script>
</body>
</html>
