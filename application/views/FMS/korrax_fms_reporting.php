<?php
$this->load->view('common/fmsfunction');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>FABRICATION FMS REPORTING</title>

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
<link href="https://cdn.datatables.net/fixedcolumns/3.3.0/css/fixedColumns.dataTables.min.css">
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
						 
                               
                            </div>
                           
                            <h4 class="page-title">KORRAX FMS  REPORTING DASHBOARD</h4>
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
                                    <th colspan="5" style="text-align:center; font-size:30px">ORDER MACHINE DETAIL</th>
									<?php 
									$i=1;
										$query = $this->db->select('flow_id,production_flow_id, fms_flow,setorder, status,who_wedo,what_wedo,how_wedo,total_days,when_wedo,video_link,uitype')->from('fms_flow')->where('production_flow_id','4')->where('status','1')->order_by('setorder','asc')->get();
										foreach($query->result() as $fms){
											if($fms->uitype=='3' || $fms->uitype=='8')
											{
												$colspan="4";
											}else{ $colspan="3"; }
									?>
									<th colspan="<?php echo $colspan;?>">P<?php echo $fms->setorder;?>. <?php echo $fms->fms_flow;?></th>
										<?php $i++;}?>
										
									<th style="text-align:center;">TOTAL DAYS</th>
									
                                </tr>
									<tr>
										<td colspan="5" rowspan="5" style="background-color:yellow"></td>
										<?php 
										
										foreach($query->result() as $fms){
											
											$person=$this->db->select('first_name,last_name')->from('system_users')->where('user_id',$fms->who_wedo)->get();
											if($person->num_rows()>0)
											{
												foreach($person->result() as $pers);
												
												$who=$pers->first_name." ".$pers->last_name;
											}else{ $who='';  }
											
											if($fms->uitype=='3' || $fms->uitype=='8')
											{
												$colspan="3";
											}else{ $colspan="2"; }
										?>
										<td height="5" style="font-size:10px; font-weight:bold;">WHO</td>
										<td colspan="<?php echo $colspan;?>" height="5" style="font-size:10px"><?php echo $who;?></td>
										<?php $i++;}?>
										<td>
											
										</td>
										
									</tr>
									
									<tr>
										
										<?php 
									
										foreach($query->result() as $fms){
											
										if($fms->uitype=='3' || $fms->uitype=='8')
											{
												$colspan="3";
											}else{ $colspan="2"; }	
									?>
										<td height="5" style="font-size:10px; font-weight:bold;">WHAT</td>
										<td colspan="<?php echo $colspan;?>" height="5" style="font-size:10px"><?php echo $fms->what_wedo;?></td>
										<?php $i++;}?>
										<td>
											
										</td>
										
									</tr>
									
									<tr>
										
										<?php 
									
										foreach($query->result() as $fms){
											if($fms->uitype=='3' || $fms->uitype=='8')
											{
												$colspan="3";
											}else{ $colspan="2"; }
									?>
										<td height="5" style="font-size:10px; font-weight:bold;">HOW</td>
										<td colspan="<?php echo $colspan;?>" height="5" style="font-size:10px"><?php echo $fms->how_wedo;?></td>
										<?php $i++;}?>
										<td>
											
										</td>
										
									</tr>
									
									<tr>
										
										<?php 
									
										foreach($query->result() as $fms){
											
												if($fms->uitype=='3' || $fms->uitype=='8')
											{
												$colspan="3";
											}else{ $colspan="2"; }
									?>
										<td height="5" style="font-size:10px; font-weight:bold;">WHEN</td>
										<td colspan="<?php echo $colspan;?>" height="5" style="font-size:10px"><?php echo $fms->total_days;?> <?php echo $fms->when_wedo;?>	</td>
										<?php $i++;}?>
										<td>
										</td>
										
									</tr>
									
									<tr>
										
										<?php 
									
										foreach($query->result() as $fms){
											
											if($fms->uitype=='3' || $fms->uitype=='8')
											{
												$colspan="3";
											}else{ $colspan="2"; }
									?>
										<td height="5" style="font-size:10px; font-weight:bold;">VIDEO LINK</td>
										<td colspan="<?php echo $colspan;?>" height="5" style="font-size:10px;"><?php echo $fms->video_link;?></td>
										<?php $i++;}?>
										<td></td>
										
									</tr>
									
                                <tr>
                                    <th>Sr No.</th>
									<th>TIMESTAMP</th>
									<th>MACHINE NAME</th>
									<th>I/O & JOB CARD</th>
                                    <th>FACTORY</th>
									<?php 
									$j=1;
										$query = $this->db->select('flow_id,production_flow_id, fms_flow,setorder, status,uitype')->from('fms_flow')->where('production_flow_id','4')->where('status','1')->order_by('setorder','asc')->get();
										foreach($query->result() as $fms){
									?>
                                    <th>PLANNED</th>
                                    <th>ACTUAL</th>
									<?php
									if($fms->uitype=='3' || $fms->uitype=='8')
											{
											?>
									<th>QC</th>
									<?php
										}
										?>
                                    <th>TOTAL DAYS</th>
                                    
										<?php $j++;}?>
									<th>GRAND TOTAL DAYS</th>
                                </tr>
                                </thead>


                                <tbody>
								<?php 
								$a=1;
									$query1 = $this->db->select('a.jobcard_id,a.plannedOn, a.order_id,a.factory, b.job_card_no,b.id, b.item_id, c.id, c.instruments_name, d.order_id, d.added_on, e.production_flow')->from('order_planning a')->join('order_instruments b','a.jobcard_id=b.id','left')->join('presto_instruments c','b.item_id=c.id','left')->join('prestogroup_orders d','a.order_id=d.order_id','left')->join('production_flow e','a.factory=e.id','left')->where('a.factory','4')->get();
									foreach($query1->result() as $row){
										
								?>
								<tr>
								<td><?php echo $a;?></td>
								<td><?php echo date('d-M-Y g:i A',strtotime($row->added_on));?></td>
								<td><?php echo $row->instruments_name;?></td>
								<td><?php echo $row->job_card_no;?></td>
								<td><?php echo $row->production_flow;?></td>
								<?php 
								$grandtotaldays=array();
								$grandtotaldays[]=0;
								foreach($query->result() as $fms){
								
								/*GET CURRENT PROCESS SORT ORDER*/
								$QRY = $this->db->select('total_days, tat,uitype')->from('fms_flow')->where('flow_id',$fms->flow_id)->get();
								$res = $QRY->result();
								foreach($res as $fmsinformation);
								$totaldaysslave = $fmsinformation->total_days;
								$tatstage = $fmsinformation->tat;
								$uitype=$fmsinformation->uitype;
								/** End **/	
/** Order Stage ID **/
$odst=$this->db->select('id')->from('order_stage')->where('jobcardid',$row->jobcard_id)->where('orderid',$row->order_id)->where('flowstage',$fms->flow_id)->order_by('id','DESC')->limit(1)->get();
if($odst->num_rows()>0)
{		foreach($odst->result() as $odst1);
$orderstageid=$odst1->id;

}else
{
	$orderstageid=0;
}

if($orderstageid<>0)
{
	
	$CI =& get_instance();
$CI->load->model('Fms_model');
$timestamp= $CI->Fms_model->getprevioustimestamp($tatstage,$row->plannedOn,$row->jobcard_id,$fms->flow_id,$orderstageid);
	$tattime = DateTime::createFromFormat('d-M-Y g:i A', $timestamp);
	$finaltattime=$tattime->format('g:i A');
	$tatdate=$CI->Fms_model->getupcomingtatdate($tatstage,$row->plannedOn,$row->jobcard_id,$timestamp,$totaldaysslave,$orderstageid);
	$plannedtimeortattime=$tatdate." ".$finaltattime;
	
	
	$acttua=$this->db->select('addedOn,userstatus')->from('order_stage')->where('id',$orderstageid)->get();
	if($acttua->num_rows()>0)
	{
		foreach($acttua->result() as $acttua1);
		$status=$acttua1->userstatus;
		if($acttua1->addedOn=="0000-00-00 00:00:00")
		{
			$actual="";
		}else{
			
			$actual=date('d-M-Y g:i:A',strtotime($acttua1->addedOn));
			$enddate=$acttua1->addedOn;
		}
		
	}else{
		
		$actual="";
	}
	
}else{
	$plannedtimeortattime='';
	$timestamp='';
	$actual='';
}

if($actual<>'' && $timestamp<>'')
{
	$previouscompletetime=date('Y-m-d',strtotime($timestamp));
	$CI =& get_instance();
$CI->load->model('Fms_model');
$totaldays= $CI->Fms_model->gettotaldays($status,$previouscompletetime,$enddate);
$grandtotaldays[]=$totaldays;
}else
{
	$totaldays="";
	
}


/** End **/							
									
								?>
								<!--Dynamic -->
								<td><?php echo $plannedtimeortattime;?></td>
								<td><?php echo $actual;?></td>
								<?php
									if($uitype=='3' || $uitype=='8')
{
if($uitype=='3')
{
	$table='qcremarks';
}else{
	
	$table='qcfinalremarks';
}

$qcst=$this->db->select('qcstatus')->from($table)->where('jobcardid',$row->jobcard_id)->order_by('id','DESC')->get();
if($qcst->num_rows()>0)
{
	foreach($qcst->result() as $qcst1);
	if($qcst1->qcstatus=='1')
	{
	$qcstatus="PASSED";
	}else{
	$qcstatus="REJECTED";
	}
	
}else{
	
	$qcstatus='';
}
											?>
									<td><?php echo $qcstatus;?></td>
									<?php
										}
										?>
								<td><?php echo $totaldays;?></td>
								<!--End -->
								<?php
								}
								?>
								
								<td><?php echo array_sum($grandtotaldays);?></td>
								</tr>
									<?php $a++;}?>
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
		<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
		<script src="https://cdn.datatables.net/fixedcolumns/3.3.0/js/dataTables.fixedColumns.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
<script>
$( document ).ready(function() {
$('#example').dataTable({
  scrollX: true,
   scrollCollapse: true,
   fixedColumns:   {
            leftColumns: 5
        },
 "paging":false});   
});

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