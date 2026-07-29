<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Field Boy dashboard</title>

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
			table.itemdetal thead th {
			background: red;
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
			table, th, td {
				text-align:center;
  border: 1px solid black;
  margin:2px 2px 2px 2px;
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
						
                           
                            <h4 class="page-title">SCHEDULER DASHBOARD</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
	
		<div class="row">
		<?php
			$q=$this->db->select('id,name,contact_number')->from('field_boy_data')->where('status','1')->get();
			foreach($q->result() as $rows){
		?>
                    <div class="col-sm-6">
                        <div class="card-box table-responsive" style="min-height:400px">
                            <table class="table table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th colspan="5" style="text-align:center;"><?php echo $rows->name;?></th>
                                </tr>
                                </thead>
								<thead style="background-color:black; color:#fff;">
									<tr>
										<td>SR NO.</td>
										<td style="width:100px">DATE</td>
										<td>ITEM DETAIL</td>
										<td>SEND SMS</td>
										
									</tr>
								</thead>
                                <tbody>
								<?php 
								$m=1;
								
								$q1 = $this->db->select('a.scheduled_date, a.pono, b.name, b.address')->from('delivery_boy_schedule a')->join('purchase_order c','a.pono=c.pono','left')->join('vendors b','c.vendor=b.id','left')->where('a.field_boy',$rows->id)->where('a.scheduled_date>=',date('Y-m-d'))->group_by('a.scheduled_date')->get();
								foreach($q1->result() as $row){
								?>
								<tr>
									<td><?php echo $m;?></td>
									<td><?php echo date('d-M-Y',strtotime($row->scheduled_date));?></td>
									<td>
										<table style="border:1px solid #000; width:400px">
											<thead>
											<tr>
												<th>PO</th>
												<th style="width:200px">ITEMS</th>
												<th>VENDOR DETAIL</th>
												<th>ACTION</th>
											</tr>
											</thead>
											<tbody>
											<?php 
											$i=1;
											$q2 = $this->db->select('a.id,a.pono,b.vendor, c.name, c.address, a.scheduled_date,b.potype')->from('delivery_boy_schedule a')->join('purchase_order b','a.pono=b.pono','left')->join('vendors c','b.vendor=c.id','left')->where('a.status','1')->where('a.scheduled_date',$row->scheduled_date)->get();
											//echo "<pre>"; print_r($q1->result()); exit;
											if($q2->num_rows()>0){
											foreach($q2->result() as $row1){
												//echo "<pre>"; print_r($row1); exit;
											$potype = $row1->potype;
											
											if($potype=='0'){
											$url ="po";
											}else{
											$url="generalpo";
											}
											?>
												<tr>
													<td><a href="<?php echo page_url;?>Store/<?php echo $url;?>/<?php echo $row1->pono;?>" target="_blank"><?php echo $row1->pono;?></a></td>
													<td>
													<div style="padding-top:5px"></div>
													<table style="width:200px; border:1px solid #000;" class="itemdetal">
														<thead style="background-color:red">
															<tr>
																<th>SR NO.</th>
																<th>ITEM</th>
																<th>QTY</th>
																
															</tr>
														</thead>
														<tbody>
														<?php 
														$k=1;
															if($row1->potype=='0'){
															$indenttype = "Machine Related Items";
															$rest123=$this->db->select('b.part as item_name,a.qty,a.unit')->from('purchase_order a')->join('machine_parts_with_picture b','a.itemid=b.id')->where('a.pono',$row1->pono)->get();	
															}else{
															$indenttype = "General Items";
															$rest123=$this->db->select('b.item_name,a.qty,a.unit')->from('purchase_order a')->join('house_keeping_items b','a.itemid=b.id')->where('a.pono',$row1->pono)->get();	
															}
															foreach($rest123->result() as $row3){
														?>
															<tr>
																<td><?php echo $k;?></td>
																<td><?php echo $row3->item_name;?></td>
																<td><?php echo $row3->qty;?></td>
															</tr>
													<?php $k++;}?>
														</tbody>
													</table>
													<div style="padding-top:5px"></div>
													</td>
													<td><?php echo $row->name;?><br><strong>Address:</strong> <?php echo $row->address;?></td>
													<td><i class="fa fa-trash" onclick="removethis('<?php echo $row1->pono;?>');"></i> | <i class="fa fa-pencil-square-o" onclick="showmodalpopup('<?php echo $row1->id;?>','<?php echo $row1->scheduled_date;?>');"></i></td>
												</tr>
												
											<?php $i++;}}?>
											</tbody>
										</table>
									</td>
									<?php 
									$scheduleddate = base64_encode($row->scheduled_date);
									$name = base64_encode($rows->name);
									?>
									
									<td><a href="<?php echo page_url;?>Store/send_schedule/<?php echo $row->pono;?>/<?php echo $rows->contact_number;?>/<?php echo $scheduleddate;?>/<?php echo $name;?>"><i class="fa fa-envelope-o" style="font-size:15px"></i></a> </td>
									
								</tr>
								<?php $m++;}?>
								
								
                                </tbody>
                            </table>
							
							
                        </div>
                    </div>
					<?php }?>
					<script>
						function showmodalpopup(id,dateinfo){
							$("#con-close-modal").modal('show');
							$("#recordid").val(id);
							$("#scheduled_date").val(dateinfo);
							
						}
					</script>
					<div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
					<form id="loginForm" method="post" action="<?php echo page_url;?>Store/update_scheduled_date"  enctype="multipart/form-data">
					<input type="hidden" name="recordid" id="recordid" value="">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Edit Scheduled date</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
											 <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Previous Date</label>
														<span id="error_business_loc" style="color:red;"></span>
														<input type="text" class="form-control" name="oldscheduled_date" id="scheduled_date" value="" readonly>
												    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Date</label>
														<span id="error_business_loc" style="color:red;"></span>
														<input type="date" class="form-control" name="nextdate" id="nextdate" value="">
												    </div>
                                                </div>
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Update"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div><!-- /.modal -->
					
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
		<!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
<script>
function removethis(pono){
	if(confirm('Do you really want to remove this?'))
	{
		document.location="<?php echo page_url;?>Store/remove_scheduler/"+pono;
		return true;
	}
}
</script>
</body>
</html>