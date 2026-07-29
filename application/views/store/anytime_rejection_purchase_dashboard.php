<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Anytime Rejection Purchase Dashboard</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.dataTables.min.css">
		<link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
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
						
                           
                            <h4 class="page-title">ANYTIME REJECTION PURCHASE DASHBOARD</h4>
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
									<th>YOUR NAME</th>
									<th>REJECTION TYPE</th>
									<th>REJECTED ITEM NAME</th>
									<th>REASON OF REJECTION</th>
									<th>QUANTITY</th>
									<th style="width:20%">UPLOAD IMAGE (IF ANY)</th>
									<th>STORE REMARK</th>
									<th>ACTION</th>
									
									
                                </tr>
                                </thead>
								

                                <tbody>
                                    <?php 
                                    $i=1;
                                    $q = $this->db->select('a.id,a.rejection_type, a.item_name, a.reason, a.qty, a.images, a.added_on,a.store_remarks, b.first_name, b.last_name')->from('anytime_rejection a')->join('system_users b','a.yourname=b.user_id','left')->where('a.store_status','1')->where('purchase_status','0')->get();
                                    if($q->num_rows()>0){
                                        foreach($q->result() as $row){
                                            if($row->images){
                                                $img = "<img src='".anytimerejection.$row->images."' width='20%' download>";
                                            }else{
                                               $img =""; 
                                            }
                                    
                                    $html='<button class="btn btn-success waves-effect waves-light btn-xs" data-toggle="modal" data-target="#con-close-modal'.$row->id.'">Mark as done</button>';
                                    ?>
                                    
                                    
                                    <div id="con-close-modal<?php echo $row->id;?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                        <form id="loginForm" method="post" action="<?php echo page_url;?>Store/anytimerejectionpurchaseupdate/<?php echo $row->id;?>" onsubmit="return validateme();">
                                        <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Update Remark and mark as done</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
											 <div class="col-md-4">
											     <div class="form-group">
											         <label>Vendor Name</label>
											         <select class="form-control select2" name="vendor_name" id="vendor_name">
											             <option value="">Select Vendor</option>
											             <?php 
											             $q = $this->db->select('id, name')->from('vendors')->where('status','1')->get();
											             foreach($q->result() as $rows){
											             ?>
											             <option value="<?php echo $rows->id;?>"><?php echo $rows->name;?></option>
											             <?php }?>
											         </select>
											     </div>
											 </div>
                                        
												<div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Remark</label>
														<textarea class="form-control" name="remarks" id="remarks" required></textarea>
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

                                    
                                    
								<tr>
								    <td><?php echo $i;?></td>
									<td><?php echo $row->first_name." ".$row->last_name;?></td>
								    <td><?php echo $row->rejection_type;?></td>
								    
								    <td><?php echo $row->item_name;?></td>
								    <td><?php echo $row->reason;?></td>
								    <td><?php echo $row->qty;?></td>
								    <td><?php echo $img;?></td>
								    <td><?php echo $row->store_remarks;?></td>
								    <td><?php echo $html;?></td>
								</tr>
								<?php $i++; }
							
							}
								?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
				
				<div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
						<h4 class="text-center">ANYTIME REJECTION HISTORY</h4>
                            <table id="example1" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                   <th>Sr No.</th>
									<th>YOUR NAME</th>
									<th>REJECTION TYPE</th>
									<th>REJECTED ITEM NAME</th>
									<th>REASON OF REJECTION</th>
									<th>QUANTITY</th>
									<th style="width:20%">UPLOAD IMAGE (IF ANY)</th>
									<th>STORE REMARK</th>
									<th>VENDOR NAME</th>
									<th>PURCHASE REMARK</th>
									<th>CHALLAN NUMBER</th>
									<th>RGP REMARK</th>
									<th>RECEIVED QTY</th>
									<th>GATE ENTRY REMARK</th>
									<th>QC STATUS</th>
									<th>QC REMARK</th>
									
									
                                </tr>
                                </thead>
								

                                <tbody>
                                    <?php 
                                    $i=1;
                                    $q = $this->db->select('a.id,a.rejection_type, a.item_name, a.reason, a.qty, a.images, a.added_on,a.store_remarks, b.first_name, b.last_name, a.purchase_remarks, c.name, c.address, a.challan_number, a.rgp_remarks, a.received_qty, a.gate_entry_remarks,a.qc_remarks, a.qc_status')->from('anytime_rejection a')->join('system_users b','a.yourname=b.user_id','left')->join('vendors c','a.vendor_id=c.id','left')->where('a.purchase_status','1')->where('a.store_status','1')->where('a.rgp_status','1')->where('a.gate_entry_status','1')->get();
                                    if($q->num_rows()>0){
                                        foreach($q->result() as $row){
                                            if($row->images){
                                                $img = "<img src='".anytimerejection.$row->images."' width='20%'>";
                                            }else{
                                               $img =""; 
                                            }
                                    
                                   
                                    ?>
                                    
                                    
                                    
                                    
								<tr>
								    <td><?php echo $i;?></td>
									<td><?php echo $row->first_name." ".$row->last_name;?></td>
								    <td><?php echo $row->rejection_type;?></td>
								    <td><?php echo $row->item_name;?></td>
								    <td><?php echo $row->reason;?></td>
								    <td><?php echo $row->qty;?></td>
								    <td><?php echo $img;?></td>
								    <td><?php echo $row->store_remarks;?></td>
								    <td><?php echo $row->name;?></td>
								    <td><?php echo $row->purchase_remarks;?></td>
								    <td><?php echo $row->challan_number;?></td>
								    <td><?php echo $row->rgp_remarks;?></td>
									<td><?php echo $row->received_qty;?></td>
									<td><?php echo $row->gate_entry_remarks;?></td>
								    <td><?php if($row->qc_status=='2'){echo "Rejected";}else{echo "Accepted";}?></td>
								    <td><?php echo $row->qc_remarks;?></td>
								</tr>
								<?php $i++; }
							
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
 $('.select2').select2({ });
$('#example').dataTable({
 "bProcessing": false,
 "pagination":true,
fixedHeader: true,
   scrollCollapse: true,
   fixedColumns:   {
            leftColumns: 3
        }
 
        }); 

$('#example1').dataTable({
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
<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
</body>
</html>