<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle;?> Order Planning</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
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
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/tagmanager/3.0.2/tagmanager.min.css">	
		<link href="<?php echo assets_url;?>plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<script language="JavaScript" type="text/javascript">
$(document).ready(function(){
    $("a.delete").click(function(e){
        if(!confirm('Do you really want to delete this record?')){
            e.preventDefault();
            return false;
        }
        return true;
    });
      $("a.copydata").click(function(e){
        if(!confirm('Do you really want to copy this record?')){
            e.preventDefault();
            return false;
        }
        return true;
    });
    
});
</script>
<style>
table.manglesh thead th {
				background: #003366;
				color:#fff;
				font-weight:bold;
			}
</style>
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
                            <div class="btn-group pull-right">
                              
                            </div>
                            <h4 class="page-title">ORDER PLANNING</h4>
							<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
							<?php $orderid = $this->uri->segment(3);
							if($orderid){
							?>
							<div class="row panel">
							<table class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th>COMPANY NAME</th>
									<th>INTERNAL ORDER NO</th>
									<th>ORDER RECEIVED DATE</th>
									<th>PO NUMBER</th>
                                    <th>ORDER TYPE</th>
									<th>TOTAL ORDER QTY</th>
                                    
                                </tr>
                                </thead>


                                <tbody>
								<?php 
								$query = $this->db->select('order_id, order_type, company_name, internal_order_no, po_number,added_on')->from('prestogroup_orders')->where('order_id',$this->uri->segment(3))->get();
								foreach($query->result() as $row){
								?>
									<tr>
										<td><strong><?php echo strtoupper($row->company_name);?></strong></td>
										<td><strong><?php echo strtoupper($row->internal_order_no);?></strong></td>
										<td><strong><?php 
										date_default_timezone_set("Asia/Kolkata");
										echo $addeddate = date('d-M-Y', strtotime($row->added_on));
										$time = date('H:i:s', strtotime($row->added_on));
										echo $addedtime = "<br>". date('g:i A', strtotime($time));
										?></strong></td>
										<td><strong><?php echo strtoupper($row->po_number);?></strong></td>
										<td><strong><?php echo strtoupper($row->order_type);?></strong></td>
										<td>
											<?php 
											$qty = array();
											$query = $this->db->select('id, order_id,qty')->from('order_instruments')->where('order_id',$this->uri->segment(3))->get();
											$res = $query->result();
											foreach($res as $qtyinfo){
												$qty[] = $qtyinfo->qty;
											}
											echo array_sum($qty);
											?>
										</td>
									</tr>
								<?php }?>
                                </tbody>
                            </table></div>
							<?php }?>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<?php
$id = $this->uri->segment(3);
				/** Get order planned **/
			$restt=$this->db->select('count(id) as totaljobcard')->from('order_instruments')->where('order_id',$id)->get();
			foreach($restt->result() as $instcount);
			$totjobcard= $instcount->totaljobcard;
			
			$restt1=$this->db->select('count(id) as totaljobcardplanned')->from('order_planning')->where('order_id',$id)->get();
			foreach($restt1->result() as $instcount1);
			$totjobcardplanned= $instcount1->totaljobcardplanned;
				
				
?>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">
<h3 class="text-center"><strong>PLAN YOUR ORDER - <span style="color:red"><?php echo "(".$totjobcardplanned."/".$totjobcard.")"?></span></strong></h3><hr>
                            <div class="row">
							<?php
								
								$this->db->select('*')->from('prestogroup_orders')->where('order_id',$id);
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $row)
								
								?>
								
                                   <form method="post" action="<?php echo page_url;?>FMS/orderplanstepone/<?php echo $row->order_id;?>" id="planform">
								   <input type="hidden" name="fabricationreq" id="fabricationreq" value="0">
                                <div class="col-sm-12 col-xs-12 col-md-12">

								
								  
										 <div class="col-md-9">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">JOB CARD NO.</label>
														<span id="jobcard" style="color:red;">*</span>&nbsp;&nbsp;&nbsp;&nbsp;<span id="notify" style="color:red;text-align:center;"></span>
														<?php echo form_error('jobcard');?>
                                                        <select class="form-control select3" style="text-transform: uppercase;" name="jobcardno[]" id="jobcardno" required onchange="checkforfabrication();">
															<option value="">--SELECT JOB CARD NO--</option>
														</select>
                                                    </div>
										
                                                </div>
												
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">ORDER STATUS</label><span id="jobcard" style="color:red;">*</span>
														
                                                       <select class="form-control" style="text-transform: uppercase;" name="orderstatus" id="orderstatus" required onchange="orderconftype(this.value);">
															<option value="">--SELECT ORDER STATUS--</option>
															<option value="1">RECIEVED</option>
															<option value="2">NOT RECIEVED</option>
															<option value="3">NOT CLEAR</option>
															
														</select>
														</div>
                                                </div>
											
											
												<div class="col-md-3 remarks" style="display:none">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARKS</label><span id="jobcard121" style="color:red;">*</span><br/>
														
                                                       <textarea name="remarks" id="remarks" class="form-control"></textarea></div>
                                                </div>
                                                </div>
												
											<div class="col-sm-12 col-xs-12 col-md-12 orderrecvd" style="display:none">

											<div class="col-md-3">
											<div class="form-group">
											<label for="field-1" class="control-label">ORDER TYPE</label>
											<span id="jobcard" style="color:red;">*</span>
											
											 <select class="form-control" style="text-transform: uppercase;" name="ordertype" id="ordertype" onchange="getfileno();">
															<option value="">--SELECT ORDER TYPE--</option>
															<option value="1">STANDARD</option>
															<option value="2">CUSTOMIZED</option>
															<option value="3">SERVICE</option>
											</select>
											<!---<script type="text/javascript">
											
													$("#ordertype").change(function(){
													var ordertype=$("#ordertype").val();
													if(ordertype=='1'){
													$("#fileno").attr('required',true);
													var jobcardno = $("#jobcardno").val();
													if(jobcardno==''){
														alert('Please select Jobcard/Ordered Instrument first.');
														return false;
													}else{
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>FMS/select_filenumber",
													data:"jobcardno="+jobcardno,
													success:function(data){
													$("#fileno").val(data);
													}
													});
													}
													}else{
													$("#fileno").val('');	
													$("#fileno").attr('required',false);
													}
													
													});
											
												</script>--->
											</div>
											</div>
											
											<div class="col-md-3">
											<div class="form-group">
											<label for="field-1" class="control-label">FACTORY</label>
											<span id="jobcard" style="color:red;">*</span>
											
											 <select class="form-control" style="text-transform: uppercase;" name="factory" id="factory" onchange="getfmsflow(); checkforstock(this.value); checkforurl(this.value);">
															<option value="">--SELECT FACTORY--</option>
															<?php
															$rep=$this->db->select('id,production_flow')->from('production_flow')->where('status','1')->order_by('sortorder','ASC')->get();
															if($rep->num_rows()>0)
															{
																foreach($rep->result() as $respp)
																{
															?>
															<option value="<?php echo $respp->id;?>"><?php echo $respp->production_flow;?></option>
															<?php
																}
															}
															?>
																											
														</select>
											</div>
											</div>
											
											<div class="col-md-3">
											<div class="form-group">
											<label for="field-1" class="control-label">FMS Flow</label>
											<span id="jobcard" style="color:red;">*</span>
											
											 <select class="form-control" style="text-transform: uppercase;" name="pfms" id="pfms" required>
															<option value="">--SELECT PROCESS--</option>
															
																											
														</select>
											</div>
											</div>


<div class="col-md-2">
											<div class="form-group">
											<label for="field-1" class="control-label">FILE NO.</label>
											
											<input type="text" name="fileno" id="fileno" value="" class="form-control" placeholder="FILE NO." >
											
											</div>
											</div>
											
											
<div class="col-md-1 reorder" style="display:none;">
											<div class="form-group">
											<label for="field-1" class="control-label">REORDER.</label>
											<br/>
											<input type="checkbox" name="reorder" id="reorder" value="1">
											
											</div>
											</div>
											</div>											
												 
												 
											
										<div class="col-md-3"></div>
										<div class="col-md-3"><span id="stockinfo" style="color:red;font-weight:bold;"></span><br/><span id="minstockinfo" style="color:red;font-weight:bold;"></span><br/></div>
										<div class="col-md-3"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" id="save" class="btn btn-success" value="Update">
												
											</div>
										</div>
										<div class="col-md-12 text-center"><span id="notavailable" style="color:red;font-weight:bold;"></span></div>
									
									</form>
                                   

                                </div>

                            </div>
                            <!-- end row -->
                        </div> <!-- end ard-box -->
                    </div><!-- end col-->

<div class="row">

                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
						<h3 class="text-center"><strong>ORDER HAS BEEN PLANNED</strong></h3><hr>
                            <table id="example" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th>SR NO.</th>
									<th>TIME STAMP</th>
									<th>MACHINE NAME</th>
									<th>JOB CARD NO.</th>
                                    <th>PLANNED TIME</th>
                                    <th>ACTUAL TIME</th>
                                    <th>FACTORY</th>
                                    <th>ORDER TYPE</th>
									<th>ORDER STATUS</th>
									<th>FILE NO</th>
									<th>REMARKS</th>
									<th>START STAGE</th>
									<th>CURRENT STAGE</th>
									<th>TOTAL DAYS</th>
									<th>ACTION</th>
                                </tr>
                                </thead>


                                <tbody>
								<?php 
								$i=1;
									$this->db->select('a.jobcard_id,a.id, a.order_id,a.planstartsfrom, a.orderstatus, a.remarks, a.ordertype, a.factory, a.fileno, a.plannedOn, a.plannedby, b.user_id, b.title, b.first_name, b.last_name, c.id, c.production_flow, d.id, d.item_id, d.qty, d.job_card_no,d.instrument_addedon, e.id, e.instruments_name, f.order_id, f.order_status,')->from('order_planning a')->join('system_users b','a.plannedby=b.user_id','left')->join('production_flow c','a.factory=c.id','left')->join('order_instruments d','a.jobcard_id=d.id','left')->join('presto_instruments e','d.item_id=e.id','left')->join('prestogroup_orders f','a.order_id=f.order_id','left');
			                        $this->db->where('a.order_id',$this->uri->segment(3));
									$this->db->where('f.order_status','1');
									$query = $this->db->order_by('a.plannedOn','desc')->get();
									$res = $query->result();
									foreach($res as $row){
									$timestamp_show = date('d-M-Y', strtotime($row->plannedOn));
									$timestamp_time = date('H:i:s', strtotime($row->plannedOn));
									$timestamptime_show = "<br>". date('g:i A', strtotime($timestamp_time));
									$addeddate = date('d-M-Y', strtotime($row->plannedOn));
									$instrumentsplanned= 	date('d-M-Y',strtotime($addeddate . "+1 days"));
			                        $time = date('H:i:s', strtotime($row->plannedOn));
			                        $addedtime = "<br>". date('g:i A', strtotime($time)); 
									$ordertype = $row->ordertype;
									if($ordertype=='1'){
										$ordertypee = "STANDARD";
									}else if($ordertype=='2'){
										$ordertypee = "CUSTOMIZED";
									}else{
										$ordertypee = "SERVICE";
									}
									$orderstatus = $row->orderstatus;
									if($orderstatus=='1'){
									$orderstatuss = "RECIEVED";
									}else if($orderstatus=='2'){
									$orderstatuss = "NOT RECIEVED";
									}else{
									$orderstatuss = "NOT CLEAR";
									}
									
									$strt = date('Y-m-d', strtotime($row->instrument_addedon));
									$today = date('Y-m-d');
									$date1 = new DateTime($today); 
									$date2 = new DateTime($strt); 
									$interval = $date1->diff($date2); 
									$days = $interval->d; 
										
									$stt=$this->db->select('a.fms_flow')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->where('b.jobcardid',$row->jobcard_id)->where('b.orderid',$row->order_id)->order_by('b.id','ASC')->limit(1)->get();
										
										if($stt->num_rows()>0)
										{
											foreach($stt->result() as $stty);
											$startfrom=$stty->fms_flow;
											
										}else{ $startfrom=''; }
										
										$stt=$this->db->select('a.fms_flow,a.setorder')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->where('b.jobcardid',$row->jobcard_id)->where('b.orderid',$row->order_id)->order_by('b.id','DESC')->limit(1)->get();
										
										if($stt->num_rows()>0)
										{
											foreach($stt->result() as $stty);
											$end=$stty->fms_flow;
											$setcurrentorder=$stty->setorder;
											
										}else{ $end=''; $setcurrentorder=0;}
										?>
									<tr>
										<td><?php echo $i;?></td>
										<td><?php echo $timestamp_show.$timestamptime_show;?></td>
										<td><?php echo strtoupper($row->instruments_name);?></td>
										<td><?php echo strtoupper($row->job_card_no);?></td>
										<td><?php echo $instrumentsplanned.$addedtime;?></td>
										<td><?php echo $timestamp_show.$timestamptime_show;?></td>
										<td><?php echo strtoupper($row->production_flow);?></td>
										<td><?php echo $ordertypee;?></td>
										<td><?php echo $orderstatuss;?></td>
										<td><?php echo strtoupper($row->fileno);?></td>
										<td><?php echo strtoupper($row->remarks);?></td>
										<td><?php echo $startfrom;?>
										<td><?php echo $end;?></td>
										<td><?php echo $days;?></td>
										<td><span class="btn btn-warning btn-xs" data-toggle="modal" data-target="#myModal<?php echo $row->jobcard_id;?>">Jump FMS</span></td>
									</tr>
									
									<!-- Modal -->
<div id="myModal<?php echo $row->jobcard_id;?>" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
	<form action="<?php echo page_url;?>FMS/jumpjobcard/<?php echo $row->jobcard_id;?>" method="post">
<input type="hidden" name="orderid" value="<?php echo $row->order_id;?>">
<input type="hidden" name="production_flow_id" value="<?php echo $row->factory;?>">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">JUMP PLANNED JOBCARD <?php echo strtoupper($row->job_card_no);?></h4>
      </div>
      <div class="modal-body">
	  <div class="col-md-12">
        <div class="col-md-6">
		<input type="text" name="machinename" class="form-control" readonly value="<?php echo  strtoupper($row->instruments_name) ;?>">
		</div>
		
		<div class="col-md-6">
		<select name="fms" class="form-control" required>
		<option value="">Jump To</option>
		<?php
		$fmsss=$this->db->select('flow_id,fms_flow')->from('fms_flow')->where('production_flow_id',$row->factory)->where('setorder>',$setcurrentorder)->get();
		if($fmsss->num_rows()>0)
		{
			foreach($fmsss->result() as $fmsss1)
			{
		?>
		<option value="<?php echo $fmsss1->flow_id;?>"><?php echo $fmsss1->fms_flow;?></option>
		<?php
			}
		}
		?>
		</select>
		</div>
			
		</div>
	
	
      </div><br/><br/>
      <div class="modal-footer">
       <input type="submit" class="btn btn-success" value="Jump">
      </div>
	  </form>
    </div>

  </div>
</div>

									<?php $i++;}?>
                                </tbody>
                            </table>
                        </div>
                    </div>
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
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>


<script language="javascript" type="text/javascript">   

$(document).ready(function() {
 var purl="<?php echo page_url;?>FMS/jobcardautocomplete/<?php echo $this->uri->segment(3);?>";
 $('.select3').select2({

        placeholder: 'TYPE TO SELECT',
minmumInputLength:4,
		allowClear: true,

        ajax: {

          url: purl,

          dataType: 'json',

          delay: 250,

          processResults: function (data) {

			 

            return {

              results: data

            };

          },

          cache: true

        }

      });
	  
	  
	  
	 

});
</script>
<script>
$( document ).ready(function() {
$('#example').dataTable({
 "bProcessing": false,
 "pagination":true,
 "sAjaxSource": "<?php echo page_url;?>FMS/planned_order_list/<?php echo $this->uri->segment(3);?>",
 "aoColumns": [
						{ mData: 'sr_no' } ,
						{ mData: 'timestamp' },
						{ mData: 'instruments_name' },
                        { mData: 'job_card_no' },
                        { mData: 'added_on' },
						{ mData: 'actualtime' },
                        { mData: 'production_flow' },
						{ mData: 'ordertypee' },
						{ mData: 'orderstatuss' },
						{ mData: 'fileno' },
						{ mData: 'remarks' },
						{mData:'fms_flow'},
						{ mData: 'totaldays' }
						
						
                ]
        });   
});

</script>
<script type="text/javascript">

$('form input').keydown(function (e) {
    if (e.keyCode == 13) {
		e.preventDefault();
        return false;
    }
});

  function orderconftype(val)
  {
	  if(val=='3')
	  {
		  $(".remarks").css('display','');
		  $("#remarks").attr('required',true);
		  $(".orderrecvd").css('display','none');
		    $("#ordertype").attr('required',false);
		  $("#factory").attr('required',false);
		   $("#fileno").attr('required',false);
		   $("#pfms").attr('required',false);
	  }else if(val=='1')
	  {
		  $(".remarks").css('display','none');
		  $("#remarks").attr('required',false);
		  $(".orderrecvd").css('display','');
		  $("#ordertype").attr('required',true);
		  $("#factory").attr('required',true);
		   $("#fileno").attr('required',true);
		    $("#pfms").attr('required',true);
		  
	  }else{
		   $(".remarks").css('display','none');
		  $("#remarks").attr('required',false);
		   $(".orderrecvd").css('display','none');
		   $("#ordertype").attr('required',false);
		  $("#factory").attr('required',false);
		   $("#fileno").attr('required',false);
		   $("#pfms").attr('required',false);
	  }
		  
  }
  
  function getfmsflow()
  {
	  $("#stockinfo").html();
		$("#minstockinfo").html();	
	  var factory=$("#factory").val();
	  
	  $.ajax({
		type:"post",
		url:"<?php echo page_url;?>FMS/getfmsslowprocesswise/"+factory,
		data:"1",
		success:function(data){
		$("#pfms").html(data);
		}
		});
	  
  }
	
	

	
	
	function checkforfabrication()
	{

		$("#fabricationreq").val('0');
		$("#notify").html('');
		
		var instrument=$('#jobcardno').val();
		 $.ajax({
		type:"post",
		url:"<?php echo page_url;?>FMS/getfabricationdetails/"+instrument,
		data:"1",
		success:function(data){
		
		if(data==1)
		{
		$("#fabricationreq").val('1');	
		$("#notify").html('This instrument will also go under fabrication process if send into Machining FMS');
		}else
		{
		$("#fabricationreq").val('0');
		$("#notify").html('');
		}
		}
		});
		
		
	
		
	} 
	
	function checkforstock(stockk)
	{
		
		$("#stockinfo").html('');
		$("#minstockinfo").html('');
		$("#notavailable").html('');
		$("#save").attr('disabled',false);
		

		if(stockk=='2')
		{
		var instrument=$('#jobcardno').val();
		
		if(instrument!='')
		{
		 $.ajax({
		type:"post",
		url:"<?php echo page_url;?>FMS/getstockdetails/"+instrument,
		data:"1",
		success:function(data){
		
		$("#stockinfo").html("Stock available "+data);
		if(data==0)
		{
			$("#save").attr('disabled',true);
			$("#notavailable").html('CANNOT PROCEED FOR INSTOCK SINCE STOCK NOT AVAILABLE');
		}else{
			$("#save").attr('disabled',false);
			$("#notavailable").html('');
			
		}
		
		getminstock(data);
		}
		});
		
		}else{
			
			alert('Please select Instrument First');
		}
		
		}
		
	}
	
	function getminstock(stock)
	{
		var instrument=$('#jobcardno').val();
		 $.ajax({
		type:"post",
		url:"<?php echo page_url;?>FMS/getminstockdetails/"+instrument,
		data:"1",
		success:function(data){
		
		$("#minstockinfo").html("Min. Stock "+data);
		if(stock<data)
		{
			var diff=parseInt(data)-parseInt(stock);
			if(diff!=0)
			{

				$("#reorder").attr('checked',true);
			}

		}
		
		}
		});
		
		
	}
	
	function checkforurl(val)
	{
		var orderid="<?php echo $row->order_id;?>";
		var pgurl="<?php echo page_url;?>";
		if(val=='2'){
			$(".reorder").css('display','');
		$('#planform').attr('action', pgurl+"FMS/orderplansteptwo/"+orderid );
		}else if(val=='3'){
			$(".reorder").css('display','none');
		$('#planform').attr('action', pgurl+"FMS/orderplanstepthree/"+orderid );
		}else{
			
			$(".reorder").css('display','none');
			$('#planform').attr('action', pgurl+"FMS/orderplanstepone/"+orderid );
		}
		
		
	}
	
	
	function getfileno()
	{
	    	var instrument=$('#jobcardno').val();
	    	if(instrument!='')
	    	{
        $.ajax({
        type:"post",
        url:"<?php echo page_url;?>FMS/select_filenumber",
        data:"jobcardno="+instrument,
        success:function(data){
        
        $("#fileno").val(data);
        }
        });
	    	}else
	    	{
	    	    
	    	    alert('Select jobcard first');
	    	}

	    
	}
</script>

</body>
<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>


</html>