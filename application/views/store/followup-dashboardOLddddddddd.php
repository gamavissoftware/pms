<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>PO FOLLOW-UP</title>

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
		<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" rel="stylesheet"/>
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
						
                           
                            <h4 class="page-title">FOLLOW-UP DASHBOARD</h4>
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
									<th>PO NO.</th>
									<th>VENDOR NAME</th>
									<th>ITEM</th>
									<th>QTY</th>
									<th>CONTACT NUMBER</th>
									<th>DELIVERY DAYS</th>
									<th>PREVIOUS STATUS</th>
									<th>HOD REMARKS</th>
									<th>FOLLOW-UP</th>
									<th>MARK AS DONE</th>
									
                                </tr>
                                </thead>
								

                                <tbody>
								<?php 
								
									$rest=$this->db->select('a.poid,a.hoddelayremarks,a.id,a.po_no, a.followupdate,a.followup_status, b.vendor,b.potype,a.itemid, c.name, c.phone, c.deliverytime')->from('vendor_followup a')->join('purchase_order b','a.poid=b.id')->join('vendors c','b.vendor=c.id')->where('a.followup_status!=','1')->where('a.followup_close','0')->where('b.approved','1')->group_by('a.id')->order_by('followupdate','DESC')->get();

									//->where('a.followupdate>=',date('Y-m-d'))
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
			    
			    $redyte=$this->db->select('followup_status')->from('vendor_followup')->where('itemid',$restyui1->itemid)->where('po_no',$restyui1->po_no)->where('followup_status !=','0')->order_by('followupdate','DESC')->limit(1)->get();
			    if($redyte->num_rows()>0)
			    {
			    foreach($redyte->result() as $redyte1)
			    $currentstatus = $redyte1->followup_status;
			  
			    if($currentstatus=='1'){
			        $sta = "Ready";
			    }else if($currentstatus=='0'){
			        $sta ="Pending";
			    }else if($currentstatus=='2'){
			        $sta = "On Time";
			    }else if($currentstatus=='3'){
			        $sta="Delayed";
			    }else if($currentstatus=='4'){
			       $sta="Po Received";
			    }
				}else
				{
				    $sta='';
				}
				
				
				/** GET FOLLOWUP NEXT DATE **/
				
				 $redyt22e=$this->db->select('followupdate')->from('vendor_followup')->where('itemid',$restyui1->itemid)->where('po_no',$restyui1->po_no)->where('followup_status ','0')->order_by('followupdate','ASC')->limit(1)->get();
			    if($redyt22e->num_rows()>0)
			    {
			    foreach($redyt22e->result() as $redyt22e1)
				if($redyt22e1->followupdate){
			    $nextfollowupdate = $redyt22e1->followupdate;
				}else{
				$nextfollowupdate="";	
				}
			   
				}else
				{
				    $nextfollowupdate=$restyui1->followupdate;
				}
				
				/** GET NEW ID FOR EACH FOLLOWUP**/
				$rowid='';
				$yetdeuejud=$this->db->select('id')->from('vendor_followup')->where('itemid',$restyui1->itemid)->where('po_no',$restyui1->po_no)->where('followupdate',$nextfollowupdate)->get();
				if($yetdeuejud->num_rows()>0)
				{
				foreach($yetdeuejud->result() as $yetdeuejud1);
				$rowid=$yetdeuejud1->id;
				}else
				{
					
				}
				/** END **/
				$html='<button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$restyui1->id.'">Update follow-up</button>';
?>
<div id="con-close-modal<?php echo $restyui1->id;?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="<?php echo page_url;?>Reporting/updatefollowup/<?php echo $rowid;?>/<?php echo $restyui1->po_no;?>/<?php echo $restyui1->itemid;?>/<?php echo $restyui1->poid;?>" onsubmit="return validateme();">
     <input type="hidden" name="previousfollowupdate" value="<?php echo $nextfollowupdate;?>">
 <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Update follow-up remarks</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
											 <div class="col-md-4">
											  <div class="form-group">
                                                        <label for="field-1" class="control-label">Delivery By</label><br>
														<span id="error_delivery_detail" style="color:red;"></span>
                                                        <input type="text" id="delivery_detail" name="delivery_detail" class="form-control" value="<?php 
														$query = $this->db->select('a.vendor, b.deliveryby')->from('purchase_order a')->join('vendors b','a.vendor=b.id','left')->where('a.pono',$restyui1->po_no)->where('a.approved','1')->get();
														foreach($query->result() as $row);
															if($row->deliveryby=='By Presto'){
																echo $row->deliveryby;
															}else{
																echo $row->deliveryby;
															}?>" readonly>
                                                    </div>
													
                                                </div>
                                                	<div class="col-md-4">
												    <div class="form-group">
												        <label>Delivery Days</label>
												        <input type="text" id="deliverydays" name="deliverydays" class="form-control" value="<?php echo $restyui1->deliverytime;?>" readonly>
												    </div>
												</div>
												
												
												
																		
												
												
												
												
												
												
												
												
												<div class="col-md-4">
													 <div class="form-group">
                                                        <label for="field-1" class="control-label">Status</label>
														<span id="error_datepicker1" style="color:red;">*</span>
                                                        <select class="form-control" name="isitdone" id="isitdone<?php echo $rowid;?>" onchange="checkreadyorder(<?php echo $rowid;?>);" required>
														<option value="">Select Option</option>
														<?php 
														$q = $this->db->select('id')->from('vendor_followup')->where('po_no',$restyui1->po_no)->get();
													if($q->num_rows()<=1){	
														?>
															<option value="4" selected>PO RECEIVED</option>
															<?php }else{?>
															<option value="2">ON TIME</option>
														<option value="1">READY</option>
													<option value="3">DELAYED</option>
														<?php }?>
														</select>
														
                                                    </div>
												</div>
											
												<script>
												function checkreadyorder(i){
													var pono = i;
												
													var isitdone = $("#isitdone"+pono).val();
													if(isitdone=='1'){
														<?php 
														if($row->deliveryby=='By Vendor'){
														?>
													
														$("#docketdiv"+pono).show("slow");
														("#expecteddateofdeliverydiv"+pono).hide("slow");
														<?php }?>
														
													}else if(isitdone=='3'){
													    $("#expecteddateofdeliverydiv"+pono).show("slow");
													    	$("#docketdiv"+pono).hide("slow");
													}else{
														$("#docketdiv"+pono).hide("slow");
														("#expecteddateofdeliverydiv"+pono).hide("slow");
														
													}
												}
												</script>
                                                
												 <div class="col-md-4" id="docketdiv<?php echo $rowid;?>" style="display:none">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Docket Number</label><br>
														<span id="error_datepicker1" style="color:red;"></span>
                                                        <input type="text" id="docket_no" name="docket_no" class="form-control" value="">
                                                    </div>
                                                </div>

                                            <div class="col-md-4" <?php if($q->num_rows()>=2){?>style="display:none"<?php }?>>
                                                <div class="form-group">
                                                    <label>Expected date of Delivery</label>
                                                    <input type="text" name="expected_date" class="form-control datepicker" id="datepicker1" value="" <?php if($q->num_rows()>=2){?><?php }else{?> required<?php }?>>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4" style="display:none" id="expecteddateofdeliverydiv<?php echo $rowid;?>">
                                               <div class="form-group">
                                                   <label>Expected date of Delivery</label>
                                                    <input type="text" name="expecteddateofdelivery" class="form-control datepicker" id="datepicker1" value="">
                                               </div> 
                                            </div>
												<div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Remark</label>
														<textarea class="form-control" name="remarks" id="remarks"></textarea>
                                                    </div>
                                                </div>
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit" > 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>	

<?php $htm="";

if($restyui1->potype=='0'){
					$indenttype = "Machine Related Items";
				$rest123=$this->db->select('b.id, b.part as item_name,a.qty,a.unit,c.shortname')->from('purchase_order a')->join('machine_parts_with_picture b','a.itemid=b.id')->join('units c','a.unit=c.id','left')->where('a.pono',$restyui1->po_no)->where('b.id',$restyui1->itemid)->where('a.approved','1')->get();
				}else{
					$indenttype = "General Items";
			$rest123=$this->db->select('b.id, b.item_name,a.qty,a.unit, c.shortname')->from('purchase_order a')->join('house_keeping_items b','a.itemid=b.id','left')->join('units c','a.unit=c.id','left')->where('a.pono',$restyui1->po_no)->where('b.id',$restyui1->itemid)->where('a.approved','1')->get();		
				}
				
				
				if($rest123->num_rows()>0)
				{
					
					foreach($rest123->result() as $rest1231);
					$itemname = $rest1231->item_name;
					$QTY = $rest1231->qty;
					$itemid = $rest1231->id;
					$shortname= $rest1231->shortname;
						
						
				}

								?>
									<tr>
										<td><?php echo $i;?></td>
										<td><?php echo $restyui1->po_no;?>
										<?php $q = $this->db->select('qty')->from('po_amendend_history')->where('pono',$restyui1->po_no)->where('item_id',$itemid)->order_by('id','desc')->limit('1')->get();
										if($q->num_rows()>0){
										    foreach($q->result() as $rowss);
										    echo "(AMENDED ".floatval($rowss->qty)." ".$shortname." to ".floatval($QTY)." ".$shortname.")";
										}?>
										</td>
										<td><?php echo $restyui1->name;?></td>
										<td><?php echo $itemname;?></td>
										<td><?php echo floatval($QTY);?> <?php echo $shortname;?></td>
										<td><?php echo $restyui1->phone;?></td>
										<td><?php echo $restyui1->deliverytime;?></td>
										<td><?php echo $sta;?></td>
											<td><?php echo $restyui1->hoddelayremarks;?></td>
										<td><?php echo $nextfollowupdate;?></td>
										<td><?php echo $html;?></td>
									</tr>
			<?php 
			$i++;
			}

		}
		?>
                                </tbody>
                            </table>
                        </div>
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
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
<script>
$( document ).ready(function() {
 
$('#example').dataTable({
 "bProcessing": false,
 "pagination":true,
fixedHeader: true,
   scrollCollapse: true,
   
   fixedColumns:   {
            leftColumns: 3
        }
 
        });   
});
</script>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready

function approvepo(pono)
{
	if(confirm('Confirm to approve PO '+pono+''))
	{
		document.location="<?php echo page_url;?>Store/approvepo/"+pono;
		return true;
	}
}
</script>
<script> $(document).ready(function() {
 var date = new Date();
date.setDate(date.getDate());
   $('.datepicker').datepicker({
    todayHighlight:true
    });
                $("#datepicker1").datepicker({
					orientation: 'bottom',
					todayHighlight:true
				});
				$('.datepicker').datepicker({todayHighlight:true});
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
					
                })

            });
</script>



</body>
</html>
