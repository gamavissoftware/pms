<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>PO DELAY REPORT</title>

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
						
                           
                            <h4 class="page-title">DELAYED PO DASHBOARD</h4>
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
                                    <th>SR No.</th>
									<th>PO NO.</th>
									
									<th>ITEM</th>
									<th>QTY</th>
									<th>VENDOR NAME</th>
									<th>EXPECTED DAY</th>
									<th>DELAYED DAY</th>
									<th>DELAY DIFF.</th>
									
									<th>REMARKS</th>
									<th>ACTION</th>
									
                                </tr>
                                </thead>
								

                                <tbody>
								<?php 
										$rest=$this->db->select('a.id,a.itemid as selecteditem,a.po_no, a.followupdate,a.remarks, a.followup_status, b.vendor,b.potype,b.itemid, c.name, c.phone, c.deliverytime')->from('vendor_followup a')->join('purchase_order b','a.po_no=b.pono','left')->join('vendors c','b.vendor=c.id','left')->where('a.followup_status','3')->where('a.followup_close','0')->where('a.hodaction','0')->group_by('a.itemid')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{
			    //echo "<pre>"; print_r($restyui1); 
			
?>
<div id="con-close-modal<?php echo $restyui1->id;?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="<?php echo page_url;?>Store/amendment_in_po/<?php echo $restyui1->selecteditem;?>/<?php echo $restyui1->po_no;?>" onsubmit="return validateme();">
     <input type="hidden" name="followupid" value="<?php echo $restyui1->id;?>">
 <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Amendment in PO</h4>
                                        </div>
                                        <div class="modal-body">
										<?php 
											if($restyui1->potype=='0'){
												$rest123=$this->db->select('b.part as item_name,a.qty,a.unit')->from('purchase_order a')->join('machine_parts_with_picture b','a.itemid=b.id')->where('a.pono',$restyui1->po_no)->where('b.id',$restyui1->selecteditem)->get();	
											}else{
												$rest123=$this->db->select('b.item_name,a.qty,a.unit')->from('purchase_order a')->join('house_keeping_items b','a.itemid=b.id')->where('a.pono',$restyui1->po_no)->where('b.id',$restyui1->selecteditem)->get();	
											}
											foreach($rest123->result() as $itemdata){
											    //echo "<pre>"; print_r($itemdata);
										?>
                                            <div class="row">
											<input type="hidden" name="item_id[]" value="<?php echo $restyui1->itemid;?>">
											 <div class="col-md-4">
											  <div class="form-group">
                                                        <label for="field-1" class="control-label">ITEM NAME</label><br>
														<span id="error_delivery_detail" style="color:red;"></span>
                                                        <input type="text" id="item_name" name="item_name" class="form-control" value="<?php echo $itemdata->item_name;?>" readonly>
                                                    </div>
													
                                                </div>
												<div class="col-md-4">
													 <div class="form-group">
                                                        <label for="field-1" class="control-label">PREVIOUS Qty</label>
														<span id="error_datepicker1" style="color:red;">*</span>
                                                       <input type="text" id="previousqty" name="previousqty" class="form-control" value="<?php echo floatval($itemdata->qty);?>" readonly>
                                                    </div>
												</div>
												
												<div class="col-md-4">
													 <div class="form-group">
                                                        <label for="field-1" class="control-label">Qty</label>
														<span id="error_datepicker1" style="color:red;">*</span>
                                                       <input type="number" id="qty" name="qty" class="form-control" value="" max="<?php echo floatval($itemdata->qty);?>" onkeyup="checkqty();" required>
                                                    </div>
												</div>
                                            </div>
                                            
                                            <script>
                                                function checkqty(){
                                                    var qty = $("#qty").val();
                                                    var previousqty= $("#previousqty").val();
                                                    
                                                    if(parseFloat(qty) > parseFloat(previousqty)){
                                                        alert('Qty should not be greater the order qty.');
                                                        $("#qty").val('');
                                                    }
                                                }
                                            </script>
											
											<?php }?>
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
				$rest123=$this->db->select('b.id, b.part as item_name,a.qty,a.unit,c.shortname')->from('purchase_order a')->join('machine_parts_with_picture b','a.itemid=b.id')->join('units c','a.unit=c.id','left')->where('a.pono',$restyui1->po_no)->where('b.id',$restyui1->itemid)->get();	
				}else{
					$indenttype = "General Items";
				$rest123=$this->db->select('b.id, b.item_name,a.qty,a.unit, c.shortname')->from('purchase_order a')->join('house_keeping_items b','a.itemid=b.id','left')->join('units c','a.unit=c.id','left')->where('a.pono',$restyui1->po_no)->where('b.id',$restyui1->itemid)->get();	
				}
				
				
				if($rest123->num_rows()>0)
				{
				
					foreach($rest123->result() as $rest1231);
					
					$ITEMNAME  = $rest1231->item_name;
					$QTY= $rest1231->qty;
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
										<td><?php echo $ITEMNAME;?></td>
										<td><?php echo floatval($QTY);?> <?php echo $shortname;?></td>
										<td><?php echo $restyui1->name;?></td>
                                    <?php
                                    $exp='';
                                    $delayed='';
                                    $q = $this->db->select('first_date, second_date')->from('vendor_followup_delay_history')->where('item_id',$itemid)->where('po_no',$restyui1->po_no)->order_by('id','DESC')->limit(1)->get();
                                    if($q->num_rows()>0){
                                    foreach($q->result() as $row);
                                    $your_date = strtotime($row->second_date);
                                    $now = strtotime($row->first_date);
                                    $datediff = $your_date-$now;
                                    $totaldays =  round($datediff / (60 * 60 * 24));
                                    
                                    
                                      $delayed = date('d-m-Y',strtotime($row->second_date));
                                      
                                    $exp= date('d-m-Y',strtotime($row->first_date));
                                    }
                                    
                                    ?>
                                    
                                    <td>
										   <?php echo $exp;?>
										</td>
										
										<td>
										   <?php echo $delayed;?>
										</td>
										<td>
										    <span style="color:red; font-weight:bold"><?php echo $totaldays;?></span>
										</td>
										<td><?php echo $restyui1->remarks;?></td>
										<td><!--<a href="<?php echo page_url;?>Store/cancelpo/<?php echo $restyui1->po_no;?>"><span class="btn btn-danger btn-xs">Cancel</span></a> |--> <a href="javascript:;" onclick="canwaitpopup('<?php echo $restyui1->id;?>','<?php echo  $restyui1->po_no;?>')"><span class="btn btn-success btn-xs">Can Wait</span></a> | <span class="btn btn-warning btn-xs" data-toggle="modal" data-target="#con-close-modal<?php echo $restyui1->id;?>">Amendment</span></td>
									</tr>
			<?php $i++;
			}}?>
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


function canwaitpopup(id,pono)
{
    $("#remarksmodal").modal('show');
    $("#delayid").val(id);
    $("#pon").text(pono);
}
</script>


<!-- Modal -->
<div id="remarksmodal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Enter Remarks for Delayed PO for Can Wait Case for PO <span id="pon"></span></h4>
      </div>
      <form action="<?php echo page_url;?>Store/canwait" method="post">
          <input type="hidden" name="delayid" id="delayid" value="">
      <div class="modal-body">
       <div class="row">
           
           <div class="col-md-12">
           <textarea name="canwaitremarks" id="canwaitremarks" required placeholder="Remarks" class="form-control"></textarea>
           </div>
       </div>
      </div>
      
      <div class="modal-footer">
        <input type="submit" class="btn btn-success" value="Update">
      </div>
      </form>
    </div>

  </div>
</div>
</body>
</html>