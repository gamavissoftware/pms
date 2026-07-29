<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Forms</title>

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
						
						 
						 <?php 
                      $query = $this->db->select('dashboard_video_link')->from('dynamic_forms')->where('id','18')->get();
                      foreach($query->result() as $row);
                      ?>
                      <a href="<?php echo $row->dashboard_video_link;?>" target="_blank"><span class="btn btn-success btn-xs">Click here to view Video <i class="fa fa-video-camera" style="font-size:15px"></i></span></a>
						 </div>
                           
                            <h4 class="page-title text-center">ALL HELP TICKETS CONSOLIDATE REPORT</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
	<?php echo $this->session->flashdata('message'); 
	$user_id =$this->session->userdata['logged_in']['user_id'];	
	?>
		<div class="row">
		    <div class="col-md-11">
		         <h3 class="text-center"><?php $query = $this->db->select('a.id, a.ref_no, a.form_title, a.description, a.department_id, b.department_id, b.department')->from('dynamic_forms a')->join('departments b','a.department_id=b.department_id','left')->where('a.form_running_status','0')->where('a.id','18')->get();
							foreach($query->result() as $row){
								echo "<span style='font-size:18px'>".strtoupper($row->form_title)."</span>";
								echo "<span style='font-size:14px'>  (".strtoupper($row->description).")</span>";
							}?></h3>
		    </div>
		 <div class="col-md-1">
		      <a href="<?php echo page_url;?>Form/view_history/18" target="_blank"><span class="btn btn-warning btn-xs">VIEW HISTORY</span></a>
		 </div>
		
							
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <?php 
						$query = $this->db->select('user_id, ref_no')->from('dynamic_forms')->where('id','18')->get();
						if($query->num_rows()>0){
						
						?>
						
                            <table id="example" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
								<?php 
								$Q2 = $this->db->select('tatappl,form_type, ref_no')->from('dynamic_forms')->where('id','18')->get();
								foreach($Q2->result() as $forminfo);
									$tatapp=$forminfo->tatappl;
								
								
									$que=$this->db->select('form_id, form_label, field_type')->from('master_dynamic_fields')->where('form_id','18')->get();
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
											<?php 
									}
								?>	
                                </tr>
                                </thead>
                                <tbody>
								<?php 
								$k=1;
									$q=$this->db->select('a.id, a.responseid, a.form_id, a.formdata,b.mark_as_done, b.record_id, a.planned_date, a.ref_remarks, a.added_on')->from('dynamic_form_data a')->join('dynamic_form_data_mark_done b','a.id=b.record_id','left')->where('a.form_id','18')->where('a.work_status','0')->get();
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
										     <form method="post" action="<?php echo page_url;?>Form/update_ref_remarks/<?php echo $row->id;?>/18">
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
									<?php $k++;}?>
                                </tbody>
                            </table><?php }?>
                            
                        </div>
                    </div>
                </div>
                <!-- end row -->
                
                
                	<div class="row">
		 <div class="col-md-11">
		         <h3 class="text-center"><?php $query = $this->db->select('a.id, a.ref_no, a.form_title, a.description, a.department_id, b.department_id, b.department')->from('dynamic_forms a')->join('departments b','a.department_id=b.department_id','left')->where('a.form_running_status','0')->where('a.id','1')->get();
							foreach($query->result() as $row){
								echo "<span style='font-size:18px'>".strtoupper($row->form_title)."</span>";
								echo "<span style='font-size:14px'>  (".strtoupper($row->description).")</span>";
							}?></h3>
		    </div>
		 <div class="col-md-1">
		      <a href="<?php echo page_url;?>Form/view_history/1" target="_blank"><span class="btn btn-warning btn-xs">VIEW HISTORY</span></a>
		 </div>
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <?php 
						$query = $this->db->select('user_id, ref_no')->from('dynamic_forms')->where('id','1')->get();
						if($query->num_rows()>0){
						
						?>
						
                            <table id="example1" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
								<?php 
								$Q2 = $this->db->select('tatappl,form_type, ref_no')->from('dynamic_forms')->where('id','1')->get();
								foreach($Q2->result() as $forminfo);
									$tatapp=$forminfo->tatappl;
								
								
									$que=$this->db->select('form_id, form_label, field_type')->from('master_dynamic_fields')->where('form_id','1')->get();
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
											<?php 
									}
								?>	
                                </tr>
                                </thead>
                                <tbody>
								<?php 
								$k=1;
									$q=$this->db->select('a.id, a.responseid, a.form_id, a.formdata,b.mark_as_done, b.record_id, a.planned_date, a.ref_remarks, a.added_on')->from('dynamic_form_data a')->join('dynamic_form_data_mark_done b','a.id=b.record_id','left')->where('a.form_id','1')->where('a.work_status','0')->get();
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
										     <form method="post" action="<?php echo page_url;?>Form/update_ref_remarks/<?php echo $row->id;?>/1">
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
									<?php $k++;}?>
                                </tbody>
                            </table><?php }?>
                            
                        </div>
                    </div>
                </div>
                <!-- end row -->
                
                <div class="row">
		 <div class="col-md-11">
		         <h3 class="text-center"><?php $query = $this->db->select('a.id, a.ref_no, a.form_title, a.description, a.department_id, b.department_id, b.department')->from('dynamic_forms a')->join('departments b','a.department_id=b.department_id','left')->where('a.form_running_status','0')->where('a.id','14')->get();
							foreach($query->result() as $row){
								echo "<span style='font-size:18px'>".strtoupper($row->form_title)."</span>";
								echo "<span style='font-size:14px'>  (".strtoupper($row->description).")</span>";
							}?></h3>
		    </div>
		 <div class="col-md-1">
		      <a href="<?php echo page_url;?>Form/view_history/14" target="_blank"><span class="btn btn-warning btn-xs">VIEW HISTORY</span></a>
		 </div>
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <?php 
						$query = $this->db->select('user_id, ref_no')->from('dynamic_forms')->where('id','14')->get();
						if($query->num_rows()>0){
						
						?>
						
                            <table id="example2" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
								<?php 
								$Q2 = $this->db->select('tatappl,form_type, ref_no')->from('dynamic_forms')->where('id','14')->get();
								foreach($Q2->result() as $forminfo);
									$tatapp=$forminfo->tatappl;
								
								
									$que=$this->db->select('form_id, form_label, field_type')->from('master_dynamic_fields')->where('form_id','1')->get();
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
											<?php 
									}
								?>	
                                </tr>
                                </thead>
                                <tbody>
								<?php 
								$k=1;
									$q=$this->db->select('a.id, a.responseid, a.form_id, a.formdata,b.mark_as_done, b.record_id, a.planned_date, a.ref_remarks, a.added_on')->from('dynamic_form_data a')->join('dynamic_form_data_mark_done b','a.id=b.record_id','left')->where('a.form_id','14')->where('a.work_status','0')->get();
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
										     <form method="post" action="<?php echo page_url;?>Form/update_ref_remarks/<?php echo $row->id;?>/14">
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
									<?php $k++;}?>
                                </tbody>
                            </table><?php }?>
                            
                        </div>
                    </div>
                </div>
                <!-- end row -->
                
                
                <div class="row">
		 <div class="col-md-11">
		         <h3 class="text-center"><?php $query = $this->db->select('a.id, a.ref_no, a.form_title, a.description, a.department_id, b.department_id, b.department')->from('dynamic_forms a')->join('departments b','a.department_id=b.department_id','left')->where('a.form_running_status','0')->where('a.id','12')->get();
							foreach($query->result() as $row){
								echo "<span style='font-size:18px'>".strtoupper($row->form_title)."</span>";
								echo "<span style='font-size:14px'>  (".strtoupper($row->description).")</span>";
							}?></h3>
		    </div>
		 <div class="col-md-1">
		      <a href="<?php echo page_url;?>Form/view_history/12" target="_blank"><span class="btn btn-warning btn-xs">VIEW HISTORY</span></a>
		 </div>
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <?php 
						$query = $this->db->select('user_id, ref_no')->from('dynamic_forms')->where('id','12')->get();
						if($query->num_rows()>0){
						
						?>
						
                            <table id="example4" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
								<?php 
								$Q2 = $this->db->select('tatappl,form_type, ref_no')->from('dynamic_forms')->where('id','12')->get();
								foreach($Q2->result() as $forminfo);
									$tatapp=$forminfo->tatappl;
								
								
									$que=$this->db->select('form_id, form_label, field_type')->from('master_dynamic_fields')->where('form_id','12')->get();
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
											<?php 
									}
								?>	
                                </tr>
                                </thead>
                                <tbody>
								<?php 
								$k=1;
									$q=$this->db->select('a.id, a.responseid, a.form_id, a.formdata,b.mark_as_done, b.record_id, a.planned_date, a.ref_remarks, a.added_on')->from('dynamic_form_data a')->join('dynamic_form_data_mark_done b','a.id=b.record_id','left')->where('a.form_id','12')->where('a.work_status','0')->get();
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
										     <form method="post" action="<?php echo page_url;?>Form/update_ref_remarks/<?php echo $row->id;?>/14">
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
									<?php $k++;}?>
                                </tbody>
                            </table><?php }?>
                            
                        </div>
                    </div>
                </div>
                <!-- end row -->
                
                <div class="row">
		 <div class="col-md-11">
		         <h3 class="text-center"><?php $query = $this->db->select('a.id, a.ref_no, a.form_title, a.description, a.department_id, b.department_id, b.department')->from('dynamic_forms a')->join('departments b','a.department_id=b.department_id','left')->where('a.form_running_status','0')->where('a.id','2')->get();
							foreach($query->result() as $row){
								echo "<span style='font-size:18px'>".strtoupper($row->form_title)."</span>";
								echo "<span style='font-size:14px'>  (".strtoupper($row->description).")</span>";
							}?></h3>
		    </div>
		 <div class="col-md-1">
		      <a href="<?php echo page_url;?>Form/view_history/2" target="_blank"><span class="btn btn-warning btn-xs">VIEW HISTORY</span></a>
		 </div>
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <?php 
						$query = $this->db->select('user_id, ref_no')->from('dynamic_forms')->where('id','2')->get();
						if($query->num_rows()>0){
						
						?>
						
                            <table id="example3" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
								<?php 
								$Q2 = $this->db->select('tatappl,form_type, ref_no')->from('dynamic_forms')->where('id','2')->get();
								foreach($Q2->result() as $forminfo);
									$tatapp=$forminfo->tatappl;
								
								
									$que=$this->db->select('form_id, form_label, field_type')->from('master_dynamic_fields')->where('form_id','2')->get();
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
											<?php 
									}
								?>	
                                </tr>
                                </thead>
                                <tbody>
								<?php 
								$k=1;
									$q=$this->db->select('a.id, a.responseid, a.form_id, a.formdata,b.mark_as_done, b.record_id, a.planned_date, a.ref_remarks, a.added_on')->from('dynamic_form_data a')->join('dynamic_form_data_mark_done b','a.id=b.record_id','left')->where('a.form_id','2')->where('a.work_status','0')->get();
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
										     <form method="post" action="<?php echo page_url;?>Form/update_ref_remarks/<?php echo $row->id;?>/14">
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
									<?php $k++;}?>
                                </tbody>
                            </table><?php }?>
                            
                        </div>
                    </div>
                </div>
                <!-- end row -->
                
                
                <div class="row">
		 <div class="col-md-11">
		         <h3 class="text-center"><?php $query = $this->db->select('a.id, a.ref_no, a.form_title, a.description, a.department_id, b.department_id, b.department')->from('dynamic_forms a')->join('departments b','a.department_id=b.department_id','left')->where('a.form_running_status','0')->where('a.id','17')->get();
							foreach($query->result() as $row){
								echo "<span style='font-size:18px'>".strtoupper($row->form_title)."</span>";
								echo "<span style='font-size:14px'>  (".strtoupper($row->description).")</span>";
							}?></h3>
		    </div>
		 <div class="col-md-1">
		      <a href="<?php echo page_url;?>Form/view_history/17" target="_blank"><span class="btn btn-warning btn-xs">VIEW HISTORY</span></a>
		 </div>
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <?php 
						$query = $this->db->select('user_id, ref_no')->from('dynamic_forms')->where('id','17')->get();
						if($query->num_rows()>0){
						
						?>
						
                            <table id="example5" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
								<?php 
								$Q2 = $this->db->select('tatappl,form_type, ref_no')->from('dynamic_forms')->where('id','17')->get();
								foreach($Q2->result() as $forminfo);
									$tatapp=$forminfo->tatappl;
								
								
									$que=$this->db->select('form_id, form_label, field_type')->from('master_dynamic_fields')->where('form_id','17')->get();
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
											<?php 
									}
								?>	
                                </tr>
                                </thead>
                                <tbody>
								<?php 
								$k=1;
									$q=$this->db->select('a.id, a.responseid, a.form_id, a.formdata,b.mark_as_done, b.record_id, a.planned_date, a.ref_remarks, a.added_on')->from('dynamic_form_data a')->join('dynamic_form_data_mark_done b','a.id=b.record_id','left')->where('a.form_id','17')->where('a.work_status','0')->get();
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
										     <form method="post" action="<?php echo page_url;?>Form/update_ref_remarks/<?php echo $row->id;?>/17">
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
									<?php $k++;}?>
                                </tbody>
                            </table><?php }?>
                            
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
 "pagination":true
 });
 $('#example1').dataTable({
 "bProcessing": false,
 "pagination":true
 });
 
 $('#example2').dataTable({
 "bProcessing": false,
 "pagination":true
 });
 
 $('#example3').dataTable({
 "bProcessing": false,
 "pagination":true
 });
 $('#example4').dataTable({
 "bProcessing": false,
 "pagination":true
 });
 
 $('#example5').dataTable({
 "bProcessing": false,
 "pagination":true
 });
});

</script>

<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
</body>
</html>