<!DOCTYPE html>
<html>
<head>
<title><?php echo sitetitle; ?> MOM</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700" rel="stylesheet">
<link href="<?php echo assets_url;?>fonts/calibri.ttf" rel="stylesheet">
<link rel="stylesheet" href="<?php echo assets_url;?>css/invoice/sampletest.css">
<style type="text/css" media="print">
  @page {  size: A4;
   margin: 0mm 0mm 0mm 0mm; margin-bottom:0mm;}
   

.printhide
{
	display:none;
}


.col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12 {
        float: left;
   }
   .col-sm-12 {
        width: 100%;
   }
   .col-sm-11 {
        width: 91.66666667%;
   }
   .col-sm-10 {
        width: 83.33333333%;
   }
   .col-sm-9 {
        width: 75%;
   }
   .col-sm-8 {
        width: 66.66666667%;
   }
   .col-sm-7 {
        width: 58.33333333%;
   }
   .col-sm-6 {
        width: 50%;
   }
   .col-sm-5 {
        width: 41.66666667%;
   }
   .col-sm-4 {
        width: 33.33333333%;
   }
   .col-sm-3 {
        width: 25%;
   }
   .col-sm-2 {
        width: 16.66666667%;
   }
   .col-sm-1 {
        width: 8.33333333%;
   }
    html, body {
        height: auto;    
    }
}


</style>

</head>

<body>
<div class="container printhide" style="margin-top:20px;margin-bottom:20px">
    <div class="row text-center">
    <span class="btn btn-success" style="text-align:center" onclick="printDiv();">PRINT</span>
<!--<span class="btn btn-success" style="text-align:center" data-toggle="modal" data-target="#con-close-modal">SEND MAIL</span>-->   
        
    </div>
    
	<div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="<?php echo page_url;?>Master/User_management/email_template"  enctype="multipart/form-data">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Add New Email Template</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                              <div class="col-md-12">
													<div class="form-group">
														 <label for="field-2" class="control-label">Add Email-Ids with comma</label>
														 <span id="error_email" style="color:red;"></span>
														 <input type="text" class="form-control" name="emails" id="emails"  value="" required>
													</div>
												</div>
												
												<div class="col-md-12">
													<div class="form-group">
														 <label for="field-2" class="control-label">Message</label>
														 <span id="error_message" style="color:red;"></span>
														 <textarea class="form-control" name="message" id="message" required></textarea>
														 
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
</div>
    <page size="A4" id="printthis">
    	<div class="pageinside-invoice">
			<div class="row">
				<div class="col-sm-12">
				
					<table style="width:100%">
						<thead>
							<tr>
								<td style="text-align:center"><img src="<?php echo assets_url;?>images/shubhampack.png" width="200"></td>
							</tr>
						</thead>
					</table>
					<?php 
					$query = $this->db->select('a.id,a.particular, a.mom_date, a.mom_time, a.participants, a.added_on, b.title, b.first_name, b.last_name, c.df_no, c.df_description')->from('dfwise_iom a')->join('system_users b','a.added_by=b.user_id','left')->join('df_release c','a.df_id=c.id','left')->where('a.id',$this->uri->segment(3))->get();
					foreach($query->result() as $row);
					?>
					<table class="table tablenoborder" style="border:1px solid #333;margin-bottom:0">
						<thead>
							<tr class="border">
								<td colspan="3" style="width:100%;text-align:center;text-transform:uppercase;padding:4px 6px; font-weight:bold;"><?php echo $row->df_no." ".$row->df_description;?> (<?php echo $row->particular;?>)</td>
								</tr>
							<tr>
								<td style="width:60%;text-align:left;text-transform:uppercase;padding:4px 6px"></td>
								<td>DATE: <?php echo date('d-m-Y',strtotime($row->mom_date));?> </td>
								<td>TIME: <?php echo date('h:i A',strtotime($row->mom_time));?></td>
							</tr>
							
							
						</thead>
					</table>
					<table class="table tablenoborder" style="border:1px solid #333;margin-bottom:0">
						<thead>
							<tr class="border">
								<td style="border:1px solid #333;padding:4px 6px; width:40%">CREATED BY:<br/> 
								<b><?PHP echo $row->title." ".$row->first_name." ".$row->last_name;?></b>
								</td>
								<td style="border:1px solid #333;padding:4px 6px;width:60%">PARTICIPANT:<br/>
								<b><?php 
								$member = explode(',', $row->participants);
								$participantperson="";
								$q = $this->db->select('title, first_name, last_name')->from('system_users')->where_in('user_id',$member)->get();
								$participant_names = array();
								foreach ($q->result() as $participantinfo) {
								$participant_names[] = ucwords(strtolower($participantinfo->title." ".$participantinfo->first_name . " " . $participantinfo->last_name));
								}
								$participantperson = implode(", ", $participant_names);
								echo $participantperson; 

								?>	</b>							
								</td>
								
							</tr>
						</thead>
					</table>
					<table style="margin-bottom:0;width:100%;">
						<tbody>
							<tr>
								<td style="width:5%; border:1px solid #333;padding:4px 6px; text-align:center; background-color:#cfcfcf; font-weight: bold;">SR NO</td>
								<td colspan="2" style="width:55%cm;border:1px solid #333;padding:4px 6px; text-align:center;background-color:#cfcfcf;  font-weight: bold;">PARTICULAR</td>
								<td colspan="2" style="width:20%;border:1px solid #333;padding:4px 6px; text-align:center; background-color:#cfcfcf; font-weight: bold;">DUE DATE</td>
								<td style="width:10%;border:1px solid #333;padding:4px 6px; text-align:center; background-color:#cfcfcf; font-weight: bold;">DEPARTMENT</td>
								<td style="width:10%;border:1px solid #333;padding:4px 6px; text-align:center; background-color:#cfcfcf; font-weight: bold;">ASSIGNED TO</td>
								<td style="width:4cm;border:1px solid #333;padding:4px 6px; text-align:center; background-color:#cfcfcf; font-weight: bold;">WORK STATUS / REMARKS</td>
								<td style="width:4cm;border:1px solid #333;padding:4px 6px; text-align:center; background-color:#cfcfcf; font-weight: bold;">COMPLETION TIME</td>
							</tr>
							<?php 
							$i=1;
							$q = $this->db->select('a.particular, a.due_date, b.title, b.first_name, b.last_name, c.department, a.workstatus, a.update_on, a.remarks')->from('dfwise_iom_points a')->join('system_users b','a.responsible_person=b.user_id','left')->join('departments c','b.department_id=c.department_id','left')->where('a.mom_id',$this->uri->segment(3))->get();
							foreach($q->result() as $row){
								if($row->workstatus=='1'){
									$workstatus = "Done";
									$completiontime = date('Y-m-d H:i A',strtotime($row->update_on));
								}else{
									$workstatus="";
									$completiontime="";
								}
							?>
							<tr>
								<td style="width:5%;border:1px solid #333;padding:4px 6px; text-align:center;"><?php echo $i;?></td>
								<td colspan="2" style="width:55%;border:1px solid #333;padding:4px 6px; text-align:center;"><?php echo ucwords(strtolower($row->particular));?></td>
								<td colspan="2" style="width:20%;border:1px solid #333;padding:4px 6px; text-align:center;"><?php echo date('d-m-Y',strtotime($row->due_date));?></td>
								<td style="width:10%;border:1px solid #333;padding:4px 6px; text-align:center;"><?php echo ucwords(strtolower($row->department));?></td>
								<td style="width:10%;border:1px solid #333;padding:4px 6px; text-align:center;"><?php echo $row->title;?> <?php echo $row->first_name." ".$row->last_name;?></td>
								<td style="width:4cm;border:1px solid #333;padding:4px 6px; text-align:center;"><?php echo $workstatus;?><br><br> <?php echo $row->remarks;?></td>
								<td style="width:4cm;border:1px solid #333;padding:4px 6px; text-align:center;"><?php echo $completiontime;?></td>
							</tr>
							<?php $i++;}?>
								
						</tbody>
					</table>
					<table style="margin-bottom:0;width:100%;">
						<tbody>
							<tr>
								<td style="padding:4px 6px;width:200px"><b>CHECKED BY:</b><br/></td>								
								<td style="padding:4px 6px;text-align:right"><b>www.shubhampack.com</b></td>
							</tr>
							<tr>
								<td style="border-bottom:2px solid #333;padding:4px 6px"><img src="" width="170px"></td>
								<td style="padding:4px 6px;text-align:right"><img src="" width="70"></td>
								
							</tr>
							
						
							<tr>
								<td style="padding:4px 6px"><b>shubham@shubhampack.com</b></td>
								<td style="padding:4px 6px;text-align:right">&nbsp;</td>
								
							</tr>
							
								</tr>
							
						</tbody>
					</table>
				</div>
			</div>
        </div>
    </page>
	
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url;?>js/html2canvas.js"></script>
<script>
function printDiv() 
{
window.print();
}
</script>
</body>
</html>