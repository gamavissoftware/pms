<!DOCTYPE html>
<html>
<head>
<title><?php echo sitetitle; ?> DF Meeting MOM</title>
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
<style>
body {
    background: #f4f7fb;
}
.toolbar-shell {
    margin-top: 20px;
    margin-bottom: 20px;
}
.toolbar-panel {
    background: #ffffff;
    border: 1px solid #dbe4ef;
    border-radius: 14px;
    padding: 14px 18px;
    box-shadow: 0 12px 28px rgba(16, 42, 67, 0.06);
}
.toolbar-title {
    font-size: 20px;
    font-weight: 700;
    color: #17324d;
    margin: 0 0 4px;
}
.toolbar-copy {
    font-size: 13px;
    color: #6b7c8f;
    margin: 0;
}
.toolbar-actions {
    text-align: right;
}
.toolbar-button {
    display: inline-block;
    padding: 9px 16px;
    border-radius: 999px;
    border: 1px solid #1f7a8c;
    background: #1f7a8c;
    color: #ffffff;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
}
.toolbar-button:hover,
.toolbar-button:focus {
    color: #ffffff;
    text-decoration: none;
}
.admin-utility-note {
    display: block;
    margin-top: 10px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
    color: #7a8795;
}
.admin-utility-link {
    display: inline-block;
    margin-top: 2px;
    font-size: 12px;
    color: #17324d;
    border-bottom: 1px solid #17324d;
    text-decoration: none;
}
.admin-utility-link:hover,
.admin-utility-link:focus {
    color: #17324d;
    text-decoration: none;
}
.admin-utility-date {
    display: block;
    margin-top: 2px;
    font-size: 11px;
    color: #7a8795;
}
</style>

</head>

<body>
<div class="container printhide toolbar-shell">
    <div class="toolbar-panel">
        <div class="row">
            <div class="col-sm-8">
                <h1 class="toolbar-title">DF Meeting MOM</h1>
                <p class="toolbar-copy">Print-ready MOM view with responsible person, due date and live work status for every weekly meeting point.</p>
            </div>
            <div class="col-sm-4 toolbar-actions">
                <a href="<?php echo page_url; ?>Task/dfweeklymeetingpoints" class="toolbar-button" style="margin-right:8px;">DF Weekly Meeting Points</a>
                <span class="toolbar-button" onclick="printDiv();">Print MOM</span>
                <?php if (!empty($can_manage_friday_reset)) { ?>
                    <span class="admin-utility-note">Admin backup tool only</span>
                    <a href="<?php echo page_url; ?>Task/dfmeeting_friday_reset" class="admin-utility-link">Open Friday reset utility</a>
                    <span class="admin-utility-date">Legacy running DFs only | Target: <?php echo date('d-M-Y', strtotime($friday_reset_target_date)); ?></span>
                <?php } ?>
            </div>
        </div>
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
$q = $this->db->select('a.particular, a.date, a.time, a.cc_record, b.first_name, b.last_name')->from('dfwise_mom a')->join('system_users b','a.added_by=b.user_id','left')->where('a.dfid',$this->uri->segment(3))->get();
if($q->num_rows()==0)
{
	echo "<h3 class='text-center'>No DF Meeting MOM Recorded</h3>"; exit;
}
foreach($q->result() as $row);

	
	$Q3 = $this->db->select('added_on')->from('dfmom_points')->where('df_id',$this->uri->segment(3))->order_by('id','desc')->limit(1)->get();
	$ro = $Q3->row();
	$createddate  = !empty($ro->added_on) ? date('d-m-Y',strtotime($ro->added_on)) : date('d-m-Y', strtotime($row->date));
	$createdtime = !empty($ro->added_on) ? date('h:i A',strtotime($ro->added_on)) : date('h:i A', strtotime($row->time));




					?>
					
					<table class="table tablenoborder" style="border:1px solid #333;margin-bottom:0">
						<thead>
							<tr class="border">
								<th style="width:50%;text-align:left;text-transform:uppercase;padding:4px 6px"><?php echo $row->particular;?></th>
								<td style="width:30%">DATE:  <?php echo $createddate;?> </td>
								<td style="width:20%">TIME: <?php echo $createdtime;?></td>
							</tr>
							
							
						</thead>
					</table>
					<table class="table tablenoborder" style="border:1px solid #333;margin-bottom:0">
						<thead>
							<tr class="border">
								<td style="border:1px solid #333;padding:4px 6px;"><strong>CREATED BY:</strong><br/> 
								<?PHP ECHO $row->first_name." ".$row->last_name;?>
								</td>
								<td style="border:1px solid #333;padding:4px 6px;width:80%"><strong>PARTICIPANT:</strong><br/>
								<?php 
								$q = $this->db->select('b.first_name, b.last_name')->from('dfwise_mom_participant a')->join('system_users b','a.user_id=b.user_id','left')->where('dfid',$this->uri->segment(3))->group_by('a.user_id')->get();
								foreach($q->result() as $row1){
									echo $row1->first_name." ".$row1->last_name.", ";
								}

								?>								
								</td>
								
							</tr>
						</thead>
					</table>
					<table style="margin-bottom:0;width:100%;">
						<tbody>
							<tr>
								<td style="width:4cm;border:1px solid #333;padding:4px 6px; text-align:center;"><strong>SR NO</strong></td>
								<td style="width:4cm;border:1px solid #333;padding:4px 6px; text-align:center;"><strong>Meeting Date</strong></td>
								<td style="width:6cm;border:1px solid #333;padding:4px 6px; text-align:center; "><strong>Particular</strong></td>
								<td style="width:5cm;border:1px solid #333;padding:4px 6px; text-align:center; "><strong>Responsible Person</strong></td>
								<td style="width:3cm;border:1px solid #333;padding:4px 6px; text-align:center; "><strong>Due Date</strong></td>
								<td style="width:3cm;border:1px solid #333;padding:4px 6px; text-align:center; "><strong>Status</strong></td>
								<td style="width:5cm;border:1px solid #333;padding:4px 6px; text-align:center; "><strong>Latest Remarks</strong></td>
								
							</tr>
							<?php 
							$i=1;
							$q = $this->db->query("
    SELECT
        p1.mom_point,
        p1.added_on,
        p1.due_date,
        p1.workstatus,
        p1.work_remarks,
        responsible.title AS responsible_title,
        responsible.first_name AS responsible_first_name,
        responsible.last_name AS responsible_last_name
    FROM dfmom_points p1
    LEFT JOIN system_users responsible
        ON responsible.user_id = p1.responsible_person
    WHERE p1.df_id = '".$this->uri->segment(3)."'
    ORDER BY p1.added_on ASC, p1.id ASC
");
							foreach($q->result() as $roww){
								$statusLabel = 'Open';
								if ((int) $roww->workstatus === 1) {
									$statusLabel = 'Done';
								} else if ((int) $roww->workstatus === 2) {
									$statusLabel = 'In Progress';
								}
							?>
							<tr>
								<td style="width:8%; border:1px solid #333;padding:4px 6px; text-align:center;"><?php echo $i;?></td>
								<td style="width:12%; border:1px solid #333;padding:4px 6px; text-align:center;"><?php echo date('d-m-Y',strtotime($roww->added_on));?></td>
								<td style="width:34%;border:1px solid #333;padding:4px 6px; text-align:center;"><?php echo ucwords(strtolower($roww->mom_point));?></td>
								<td style="width:18%;border:1px solid #333;padding:4px 6px; text-align:center;"><?php echo ucwords(strtolower(trim($roww->responsible_title." ".$roww->responsible_first_name." ".$roww->responsible_last_name)));?></td>
								<td style="width:10%;border:1px solid #333;padding:4px 6px; text-align:center;"><?php echo !empty($roww->due_date) ? date('d-m-Y',strtotime($roww->due_date)) : '-';?></td>
								<td style="width:8%;border:1px solid #333;padding:4px 6px; text-align:center;"><?php echo $statusLabel; ?></td>
								<td style="width:18%;border:1px solid #333;padding:4px 6px; text-align:center;"><?php echo !empty($roww->work_remarks) ? ucwords(strtolower($roww->work_remarks)) : '-';?></td>
								
							</tr>
							<?php $i++;}?>
								
						</tbody>
					</table>
					<table style="margin-bottom:0;width:100%;">
						<tbody>
							<tr>
								<td style="padding:4px 6px;width:200px"><b>CHECKED BY:</b><br/></td>								
								<!-- <td style="padding:4px 6px;text-align:right"><b>www.shubhampack.com</b></td> -->
							</tr>
							<tr>
								<td style="border-bottom:2px solid #333;padding:4px 6px"><img src="" width="170px"></td>
								<td style="padding:4px 6px;text-align:right"><img src="" width="70"></td>
								
							</tr>
							
						
							<!-- <tr>
								<td style="padding:4px 6px"><b>info@shubhampack.com</b></td>
								<td style="padding:4px 6px;text-align:right">&nbsp;</td>
								
							</tr> -->
							
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
