<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Edit FMS Flow</title>

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
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="btn-group pull-right">
                              
                            </div>
                            <h4 class="page-title">Edit FMS Flow</h4>
							<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->


                <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">

								<?php
								$id = $this->uri->segment(3);
								$this->db->select('*')->from('fms_flow')->where('flow_id',$id);
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $row)
								
								?>
								
                                   <form method="post" action="<?php echo page_url;?>FMS/update_fms_flow/<?php echo $row->flow_id;?>">
								   <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">PRODUCTION FLOW</label>
														<span id="error_production_flow" style="color:red;">*</span>
																																									<select class="form-control" style="text-transform: uppercase;" name="production_flow" id="production_flow" onchange="getusers();" readonly>
															<option value="">--SELECT PRODCTION FLOW--</option>
															<?php 
															$query = $this->db->select('id,production_flow, sortorder, status')->from('production_flow')->where('status','1')->order_by('sortorder','asc')->get();
															foreach($query->result() as $row2){
															?>
															<option value="<?php echo $row2->id;?>" <?php if($row->production_flow_id==$row2->id){echo "selected";}?>><?php echo $row2->production_flow;?></option>
															<?php }?>
														</select>
                                                    </div>
                                                </div>
										<div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">FLOW NAME</label>
														<span id="error_flowname" style="color:red;">*</span>
                                                        <input type="text" class="form-control" id="flowname" name="flowname" style="text-transform: uppercase;" placeholder="" required value="<?php echo $row->fms_flow;?>">
                                                    </div>
                                                </div>
												
												<div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">WHO</label>
														<span id="error_who" style="color:red;">*</span>
                                                        <select class="form-control select3" style="text-transform: uppercase;" name="who" id="who">
															<option value="">--SELECT WHO--</option>
															
														</select>
                                                    </div>
                                                </div>
												
												<div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">WHAT</label>
														<span id="error_what" style="color:red;">*</span>
                                                        <input type="text" class="form-control" id="what" name="what" placeholder="" style="text-transform: uppercase;" value="<?php echo $row->what_wedo;?>" required>
                                                    </div>
                                                </div>
												<div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">HOW</label>
														<span id="error_how" style="color:red;">*</span>
                                                        <input type="text" class="form-control" id="how" name="how" placeholder="" style="text-transform: uppercase;" value="<?php echo $row->how_wedo;?>" required>
                                                    </div>
                                                </div>
												
												<div class="col-md-1">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">DAYS</label>
														<span id="error_day" style="color:red;">*</span>
                                                        <input type="number" class="form-control" id="days" name="days" style="text-transform: uppercase;" value="<?php echo $row->total_days;?>" min="0" placeholder="" required>
                                                    </div>
                                                </div>
									   
									   <div class="col-md-1">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">HOUR</label>
														<span id="error_time" style="color:red;"></span>
                                                        <input type="number" class="form-control" id="time" name="time" style="text-transform: uppercase;" value="<?php echo $row->set_time;?>" min="0" placeholder="">
                                                    </div>
                                                </div>
												
									   <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">TAT FROM</label>
														<span id="error_name" style="color:red;">*</span>
														<select name="tat" id="tat" class="form-control" required>
														<option value="<?php echo $row->tat;?>">TAT FROM</option>
															<option value="0" <?php if($row->tat=='0'){?> selected <?php } ?>>ORDER PLANNING</option>
														<?php
															
														$restyu=$this->db->select('flow_id,fms_flow')->from('fms_flow')->where('production_flow_id',$row->production_flow_id)->where('flow_id !=',$id)->get();
														
														if($restyu->num_rows()>0)
														{
														
															foreach($restyu->result() as $flowp)
															{
														?>
														<option value="<?php echo $flowp->flow_id;?>" <?php if($row->tat==$flowp->flow_id){?> selected <?php } ?>><?php echo $flowp->fms_flow;?></option>
														
														<?php
															}
														}
														
														?>
														</select>
                                                       
                                                    </div>
                                                </div>
									   
												<div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">WHEN</label>
														<span id="error_when" style="color:red;">*</span>
                                                        <input type="text" class="form-control" id="when" name="when" style="text-transform: uppercase;" value="<?php echo $row->when_wedo;?>" placeholder="" required>
                                                    </div>
                                                </div>
									   
									   <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">MOVE TO  <?php 
														$movetodata = explode(',',$row->moveto);
														$query = $this->db->select('flow_id,fms_flow ')->from('fms_flow')->where_in('flow_id',$movetodata)->get();
														foreach($query->result() as $moveto){
															echo "<span class='btn btn-success btn-xs'>".$moveto->fms_flow."</span>&nbsp; &nbsp;";
														}
														?>
														<?php if($row->moveto<>'0'){?>
														<a href="<?php echo page_url;?>FMS/delete_moveto/<?php echo $this->uri->segment(3);?>" class="delete"><i class="fa fa-trash" style="font-size:20px;"></i></a><?php }?></label>
														<input type="hidden" name="existingvalue" value="<?php echo $row->moveto;?>">
														<span id="error_moveto" style="color:red;">*</span>
														<select name="moveto[]" id="moveto" class="select3" multiple>
														</select>
                                                       
                                                    </div>
                                                </div>
												
												<div class="col-md-1">
													<div class="form-group">
														<label>Dependent</label><br>
														<input type="checkbox" name="dependent" value="1" id="dependent" <?php if($row->dependency=='1'){echo "checked";}?>><br>
													</div>
												</div>
												<script>
													$('#dependent').change(function(){
													if (this.checked) {
													$('#showdependent').fadeIn('slow');
													}
													else {
													$('#showdependent').fadeOut('slow');
													}                   
													});
												</script>
												
												<div class="col-md-3" id="showdependent" <?php if($row->dependency=='1'){}else{?> style="display:none"<?php }?>>
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">DEPENDENT TO <?php 
													
														$query = $this->db->select('a.id,a.flowid, a.dependentflowid, b.flow_id,b.fms_flow ')->from('flowdependency a')->join('fms_flow b','a.dependentflowid=b.flow_id','left')->where('a.flowid',$this->uri->segment(3))->get();
														foreach($query->result() as $dependentto){
															echo "<span class='btn btn-success btn-xs'>".$dependentto->fms_flow."<a href='".page_url."FMS/deletedependency/".$dependentto->id."/".$this->uri->segment(3)."' class='deletethis'><i class='fa fa-trash' style='font-size:16px; color:#fff;'></i></a></span>&nbsp; &nbsp;";
														}
														?></label>
														<span id="error_dependentto" style="color:red;">*</span>
														<select name="dependentto[]" id="dependentto" class="select3" multiple>
														
														</select>
                                                       
                                                    </div>
                                                </div>
									   <div style="clear:both;height:10px"></div>
												<div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">VIDEO LINK</label>
														<span id="error_name" style="color:red;"></span>
                                                        <input type="text" class="form-control" id="video_link" value="<?php echo $row->video_link;?>" name="video_link" placeholder="">
                                                    </div>
													
												
                                                </div>
												
												<div class="col-md-1">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">SET ORDER</label>
														
														<select name="set_order" id="set_order" class="form-control">
														<option value="">Set Order</option>
														<option value="<?php echo $row->setorder;?>" selected><?php echo $row->setorder;?></option>
														<?php
														$resttui=$this->db->select('setorder')->from('fms_flow')->where('production_flow_id',$row->production_flow_id)->get();
														$previousorder=array();
														if($resttui->num_rows()>0)
														{
															foreach($resttui->result() as $resttui1)
															{
																$previousorder[]=$resttui1->setorder;
															}
														}else
														{
															$previousorder=array();
														}
														
														for($i=1;$i<16;$i++)
														{
															if (!in_array($i, $previousorder)) 
														{ 
 														?>
														<option value="<?php echo $i;?>"><?php echo $i;?></option>
														<?php
														}
														}
														?>
														</select>
                                                       
                                                    </div>
                                                </div>


	<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">RESPONSE TYPE</label>
														<span id="error_name" style="color:red;">*</span>
														<select name="response_type" id="response_type" class="form-control" required>
														<option value="<?php echo $row->uitype;?>">RESPONSE TYPE</option>
														<?php
															
														$restyu=$this->db->select('id,pagetype')->from('uitype')->get();
														
														if($restyu->num_rows()>0)
														{
															foreach($restyu->result() as $uittype)
															{
														?>
														<option value="<?php echo $uittype->id;?>" <?php if($row->uitype==$uittype->id){?> selected <?php } ?>><?php echo $uittype->pagetype;?></option>
														
														<?php
															}
														}
														
														?>
														</select>
                                                       
                                                    </div>
                                                </div>


<div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">ACTION TO BE TAKEN</label>
														<span id="error_action" style="color:red;">*</span>
                                                        <textarea class="form-control" id="actiontobetaken" name="actiontobetaken" placeholder="" required><?php echo $row->actiontobetaken;?></textarea>
                                                    </div>
                                                </div>
									   
									   
									   <div class="col-md-4">
                                                    <div class="form-group">
STOCK VALUE				<span id="actiontobe_taken" style="color:red;">*</span>
                                                       <input type="number" min="0" class="form-control" name="stockvalue" id="stockvalue" value="<?php echo floatval($row->stockvalue);?>" required>
                                                    </div>
                                                </div>
                                                
                                                	<div class="col-md-2">
													<div class="form-group">
														<label>Days to perpare <span style="color:red">*</span></label><br>
													<input type="number" name="pdays" class="form-control" required  value="<?php echo floatval($row->pdays);?>">
													</div>
												</div>
												
                                                
									   <div class="col-md-2">
													<div class="form-group">
														<label>Final step?</label><br>
														<input type="checkbox" name="final" value="1" id="final" <?php if($row->finalstep=='1'){?> checked <?php } ?>>
													</div>
												</div>
									   
									    <div class="col-md-2">
													<div class="form-group">
														<label>Fabrication Jump Appl?</label><br>
														<input type="checkbox" name="jumpfabrication" value="1" id="final" <?php if($row->jumpfabricationappl=='1'){?> checked <?php } ?>>
													</div>
												</div>
									   
									   <div style="clear:both;height:15px"></div>
													<div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">OBSERVER</label>
														<span id="error_who" style="color:red;">*</span>
                                                        <select class="select4" multiple style="text-transform: uppercase;" name="observer[]" id="observer">
															<option value="">--SELECT--</option>
															<?php
															$alreadyselected=array();
														$Restyyuuuu=$this->db->select('a.userid')->from('flowobserver a')->where('a.flowid',$row->flow_id)->get();
														if($Restyyuuuu->num_rows()>0)
														{
														foreach($Restyyuuuu->result() as $Restyyuuuu123)
														{
															$alreadyselected[]=$Restyyuuuu123->userid;
														}
														
														}
		
	
															$restsysyu=$this->db->select('b.user_id,b.first_name,b.last_name')->from('flowtousers a')->join('system_users b','a.userid=b.user_id')->where('b.user_id!=',$row->who_wedo)->group_by('userid')->get();
															if($restsysyu->num_rows()>0)
															{
															   
																foreach($restsysyu->result() as $restsysyu1)
																{
																	if(in_array($restsysyu1->user_id,$alreadyselected))
																	{
																	   
																$a="selected";
																	}else{
																		
																		$a='';
																	}
															?>
															<option value="<?php echo $restsysyu1->user_id;?>" <?php echo $a;?>><?php echo $restsysyu1->first_name;?> <?php echo $restsysyu1->last_name;?></option>
															<?php
																}
															}
															?>
														</select>
                                                    </div>
                                                </div>
												
												
												
									   <div class="col-md-2">
									   		<div class="form-group">
												<label>STATUS</label>
												<select class="form-control" id="status" name="status">
													<option value="<?php echo $row->status;?>"><?php if($row->status=='1'){echo "Active";}else{echo "Inactive";}?></option>
													<option value="1" <?php if($row->status=='1'){echo "selected";}?>>ACTIVE</option>
													<option value="0" <?php if($row->status=='0'){echo "selected";}?>>INACTIVE</option>
												</select>
											</div>
									   </div>
												
										
										<div class="col-md-9"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" id="save" class="btn btn-success" value="UPDATE">
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
			 jQuery('#datepicker').datepicker();
            jQuery('#datepicker-autoclose').datepicker({
                autoclose: true,
                todayHighlight: true
            });
			jQuery('#datepicker-autoclose1').datepicker({
                autoclose: true,
                todayHighlight: true
            });
		
		</script>
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<script language="javascript" type="text/javascript">   
$(document).ready(function() {
	getusers();
	getfmsinfo();
	getdependentfms();
$('.select2').select2({ });
$('.select3').select2({ });
$('.select4').select2({ });
$("#save").click(function() {
var flowname = $("#flowname").val();
if(flowname=='')
{
	$("#error_flowname").html('Required!');
}
var who = $("#who").val();
if(who=='')
{
	
	$("#error_who").html('Required!');
}

var what = $("#what").val();
if(what=='')
{
	
	$("#error_what").html('Required!');
}

var how = $("#how").val();
if(how=='')
{
	
	$("#error_how").html('Required!');
}
var days = $("#days").val();
if(days=='')
{
	
	$("#error_days").html('Required!');
}
var when = $("#when").val();
if(when=='')
{
	
	$("#error_when").html('Required!');
}
var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}

if(flowname=='' || who=='' || what=='' || how==''|| days==''|| when=='' || status=='')
{
	
	return false;
}

});
});

function getusers()
{
	var previous="<?php echo $row->who_wedo;?>";
	
	var fmsp=$("#production_flow").val();
	if(fmsp!='' || fmsp!='0')
	{
		$("#who").find('option').not(':first').remove();
		$.ajax({
		type:"post",
		url:"<?php echo page_url;?>Master/User_management/getfmswiseuser/"+fmsp,
		data:"1",
		success:function(data){
		$("#who").html(data);
		$("#who").select2("val", previous);
		//$('#who option[value='+previous+']').attr('selected','selected');
		}
		});
		
	}
	
}
	
	function getfmsinfo()
{
	var existingval = "<?php echo base64_encode($row->moveto);?>";
	var fmsp=$("#production_flow").val();
	if(fmsp!='' || fmsp!='0')
	{
		$("#moveto").find('option').not(':first').remove();
		$.ajax({
		type:"post",
		url:"<?php echo page_url;?>Master/User_management/getfmsinformation/"+fmsp+"/"+existingval,
		data:"1",
		success:function(data){
		$("#moveto").html(data);
		}
		});
		
	}
	
}
	
	function getdependentfms()
{
	var existingval = "<?php echo base64_encode($row->moveto);?>";
	var fmsp=$("#production_flow").val();
	if(fmsp!='' || fmsp!='0')
	{
		$("#dependentto").find('option').not(':first').remove();
		$.ajax({
		type:"post",
		url:"<?php echo page_url;?>Master/User_management/getfmsinformation/"+fmsp+"/"+existingval,
		data:"1",
		success:function(data){
		$("#dependentto").html(data);
		}
		});
		
	}
	
}
</script>
</body>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
</html>