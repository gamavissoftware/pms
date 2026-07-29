<?php 
$CI =& get_instance();
$CI->load->model('Store_model');

$id=$this->uri->segment('3');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Create Debit Note</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

<link rel="stylesheet" href="<?php echo plugins_url;?>bootstrap-datepicker/css/bootstrap-datepicker3.css"/>

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
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
							<h4 class="page-title">Create Debit Note Request</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
				<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">
						<!--<h2 class="text-center">Please Wait.....</h2>-->
						<form method="post" action="<?php echo page_url;?>Store/generatedebitnote/<?php echo $this->uri->segment(3);?>"    id="frm">
						
						
						<?php 
						$id=$this->uri->segment(3);
							$rest=$this->db->select('a.*,c.source,c.potype,d.name,c.unit,a.qty,d.name,c.vendor')->from('item_rejection_request a')->join('purchase_order c','a.pono=c.pono','left')->join('vendors d','c.vendor=d.id','left')->where('a.id',$id)->get();
							foreach($rest->result() as $row);
							
							$unitname=$CI->Store_model->getunit($row->unit);
							
							if($row->potype==0)
							{
							$itemname=$CI->Store_model->getmachineitemname($row->itemid);
							$otherdetails=$CI->Store_model->getmachineotherdetails($row->itemid);

							if(count($otherdetails)>0)
							{

							$fincode=$otherdetails['fincode'];
							$specification=$otherdetails['specialization'];
							}else{
							$fincode='';
							$specification='';
							}
							}else{
							$itemname=$CI->Store_model->getgeneralitemname($row->itemid);
							$fincode='';
							$specification='';
							}
							?>
					
							<input type="hidden" name="itemid" value ="<?php echo $row->itemid;?>">
							<input type="hidden" name="pono" value="<?php echo $row->pono;?>">
							<input type="hidden" name="supplierid" value="<?php echo $row->vendor;?>">
							<input type="hidden" name="mrnhistoryid" value="<?php echo $row->mrnhistoryid;?>">
							<input type="hidden" name="unit" value="<?php echo $row->unit;?>">
							
                            <div class="row">
								   <div class="col-md-12">
								   <div class="col-md-6">
								      <div class="form-group">
														 <label for="field-2" class="control-label">Reason<span style="color:red">*</span></label>
														 <span id="error_qty" style="color:red;"></span>
										<textarea class="form-control" name="reason" id="reason" required rows="5"  style="resize:none;"></textarea>
										</div>
								   </div>
								    <div class="col-md-3" style="display:none;">
													<div class="form-group">
														 <label for="field-2" class="control-label">Vendor<span style="color:red">*</span></label>
														 <span id="error_qty" style="color:red;"></span>
														 <input type="hidden" class="form-control" name="vendor" id="vendor"  value="<?php echo $row->name;?>"  readonly>
													</div>
												</div>
												
								   <div class="col-md-4" style="display:none;">
													<div class="form-group">
														 <label for="field-2" class="control-label">ITEM NAME<span style="color:red">*</span></label>
														 <span id="error_item_name" style="color:red;"></span>
														 <input type="hidden" class="form-control" name="item_name" id="item_name"  value="<?php echo $itemname;?>" readonly>
													</div>
												</div>
												<div class="col-md-2" style="display:none;">
													<div class="form-group">
														 <label for="field-2" class="control-label">REJECT QTY IN <?php echo strtoupper($unitname);?> <span style="color:red">*</span></label>
														 <span id="error_qty" style="color:red;"></span>
														 <input type="hidden" class="form-control" name="rqty" id="rqty"  value="<?php echo floatval($row->qty);?>" readonly>
													</div>
												</div>
												
												
												
												 <div class="col-md-2" style="display:none;">
													<div class="form-group">
														 <label for="field-2" class="control-label">PO NO<span style="color:red">*</span></label>
														 <span id="error_qty" style="color:red;"></span>
														 <input type="hidden" class="form-control" name="po" id="po"  value="<?php echo $row->pono;?>" readonly>
													</div>
												</div>
												
												
												
												</div>
											</div>
											<hr/>
											
											
							
											<div class="row">
											
										<div class="col-md-9"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" class="btn btn-success" value="Create Challan">
											</div>
										</div>
									
									

                                </div>
								
								</form>
                                   
								   

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

        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>


 <script src="<?php echo plugins_url;?>bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
           $( document ).ready(function() {
   //$("#frm").submit();
});
        </script>

    </body>
</html>