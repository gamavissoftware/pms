<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Prestogroup</title>

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
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right">
						  
                               
                            </div>
                           
                            <h4 class="page-title">Edit Machine Detail with Picture</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">
								<?php
								$id = $this->uri->segment(3);
								$this->db->select('*')->from('machine_parts_with_picture')->where('id',$id);
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $machine)
								?>
								
                                   <form method="post" action="<?php echo page_url;?>Store/update_machine_detail_picture/<?php echo $machine->id;?>"  enctype="multipart/form-data">
								   <input type="hidden" name="old_img" value="<?php echo $machine->picture;?>">
										  <div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Machine</label>
														 <span id="error_machine" style="color:red;"></span>
														 <select class="form-control" name="machine" id="machine">
														 <?php 
														 $query = $this->db->select('machine_id, machine')->from('machine_master')->where('status','1')->get();
														 foreach($query->result() as $row){
														 ?>
															<option value="<?php echo $row->machine_id;?>" <?php if($machine->item_id==$row->machine_id){echo "selected";}?>><?php echo $row->machine;?></option>
														 <?php }?>
														 </select>
													</div>
												</div>
												
												 <div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Machine Part</label>
														 <span id="error_machine_part" style="color:red;"></span>
														 <select class="form-control" name="machine_part" id="machine_part">
														 <?php 
														 $query = $this->db->select('part_id, machine_part')->from('machine_parts_master')->where('status','1')->get();
														 foreach($query->result() as $row){
														 ?>
															<option value="<?php echo $row->part_id;?>" <?php if($machine->part_id==$row->part_id){echo "selected";}?>><?php echo $row->machine_part;?></option>
														 <?php }?>
														 </select>
													</div>
												</div>
												 <div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Rack Location</label>
														 <span id="error_rack_location" style="color:red;"></span>
														 <select class="form-control" name="rack_location" id="rack_location">
														 <?php 
														 $query = $this->db->select('id, rack_location')->from('store_rack_location')->get();
														 foreach($query->result() as $row){
														 ?>
															<option value="<?php echo $row->id;?>"><?php echo $row->rack_location;?></option>
														 <?php }?>
														 </select>
													</div>
												</div>
												
												<div class="col-md-4">
													<div class="form-group">
														<label>Specification</label>
														<input type="text" class="form-control" name="specification" id="specification" value="<?php echo $machine->specification;?>">
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														<label>Make</label>
														<input type="text" class="form-control" name="make" id="make" value="<?php echo $machine->makes;?>">
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														<label>Fincode</label>
														<input type="text" class="form-control" name="fincode" id="fincode" value="<?php echo $machine->fincode;?>">
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														<label>Qty</label>
														<input type="text" class="form-control" name="qty" id="qty" value="<?php echo $machine->qty;?>">
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														<label>Picture</label>
														<input type="file" class="form-control" name="picture" id="picture" value="">
														<img src="<?php echo product_items;?><?php echo $machine->picture;?>" width="100">
													</div>
												</div>
												
											<div class="col-md-3">
												<div class="form-group">
												<label for="field-2" class="control-label">Status</label>
												<span id="error_status" style="color:red;"></span>
												<select class="form-control" id="status" name="status">
													<option value="<?php echo $machine->status;?>">--Select Status--</option>
													<option value="1" <?php if($machine->status=='1'){echo "selected";}?>>Active</option>
													<option value="0" <?php if($machine->status=='0'){echo "selected";}?>>Inactive</option>
												</select>
												</div>
											</div>
												
                                            </div>
										<div class="col-md-9"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" class="btn btn-success" value="Update">
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

     <script language="javascript" type="text/javascript">   

$(document).ready(function() {
$("#save").click(function() {
var machine = $("#machine").val();
if(machine=='')
{
	$("#error_machine").html('Required!');
}
var machine_part = $("#machine_part").val();
if(machine_part=='')
{
	$("#error_machine_part").html('Required!');
}
var rack_location = $("#rack_location").val();
if(rack_location=='')
{
	$("#error_rack_location").html('Required!');
}

if(machine=='' || machine_part=='' || rack_location=='')
{
	
	return false;
}

});
});
</script>
    </body>
</html>