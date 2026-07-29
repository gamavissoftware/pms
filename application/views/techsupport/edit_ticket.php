<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Prestogroup Edit Ticket</title>

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
						
                            <h4 class="page-title">Edit Ticket Detail</h4>
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
								$this->db->select('*')->from('tech_support_ticket')->where('ticket_id',$id);
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $ticket)
								?>
								
                                   <form method="post" action="<?php echo page_url;?>Tech_support/update_ticket_detail/<?php echo $ticket->ticket_id;?>" enctype="multipart/form-data">
								   <input type="hidden" name="old_img" value="<?php echo $ticket->screenshot;?>">
										 		<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">Location</label>
														 <span id="error_business_loc" style="color:red;"></span>
														 <select class="form-control" id="business_loc" name="business_loc">
													<option value="">--Select Location--</option>
													<?php 
													$this->db->select('a.company_name, a.business_loc_id,a.state_id, a.city_id,b. 	state_id,b.state_name,c.city_id,c.city_name')->from('business_location a')->join('states b','a.state_id=b.state_id','left')->join('cities c','a.city_id=c.city_id','left')->where('business_loc_status','1');
													$this->db->order_by('a.company_name','asc');
													$query = $this->db->get();
													$res = $query->result();
													foreach($res as $row){
													?>
													<option value="<?php echo $row->business_loc_id;?>" <?php if($ticket->business_loc_id==$row->business_loc_id){echo "selected";}?>><?php echo $row->company_name;?>(<?php echo $row->state_name;?>, <?php echo $row->city_name;?>)</option>
											<?php }?>		
												</select>
												<script type="text/javascript">
											
													$("#business_loc").change(function(){
													var business_loc=$("#business_loc").val();
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Tech_support/select_installed_location",
													data:"business_loc="+business_loc,
													success:function(data){
													$("#location").html(data);
													}
													});
													});
											
												</script>
												
													</div>
												</div>
												
											   <div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">Department</label>
														 <span id="error_location" style="color:red;">*</span>
														 <select class="form-control" id="location" name="location">
													
													<?php 
														$this->db->select('location_id, installed_location')->from('installed_location')->where('location_id',$ticket->location_id);
														$query = $this->db->get();
														$res = $query->result();
														foreach($res as $location){
													?>
													<option value="<?php echo $location->location_id;?>"><?php echo $location->installed_location;?></option>
								<?php }?>
												</select>
												<script type="text/javascript">
													$("#location").change(function(){
													var location=$("#location").val();
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Tech_support/select_department_items",
													data:"location="+location,
													success:function(data){
													$("#item_name").html(data);
													}
													});
													});
											
												</script>
													</div>
												</div>
												
												
												<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">Asset Name</label>
														 <span id="error_item_name" style="color:red;">*</span>
														 <select class="form-control select3" id="item_name" name="item_name">
														 <?php 
															$query = $this->db->select('item_id, item_name')->from('tech_items')->where('item_id',$ticket->item_name)->get();
															foreach($query->result() as $items){
														 ?>
													<option value="<?php echo $items->item_id;?>"><?php echo $items->item_name;?></option>
															<?php }?>
													
												</select>
													</div>
												</div>
												
												<div class="col-md-12">
												<div class="form-group">
												<label for="field-2" class="control-label">Remarks/ Problem/ Description</label>
												<span id="error_status" style="color:red;"></span>
												<textarea class="form-control" name="remarks" id="remarks"><?php echo $ticket->remarks;?></textarea>
												</div>
											</div>
                                            </div>
										<div class="col-md-4">
											<div class="form-group">
												<label for="field-2" class="control-label">Screenshot</label>
												<span id="error_status" style="color:red;"></span>
												<input type="file" name="screen_shot" id="screen_shot" value="">
												<img src="<?php echo techpath;?><?php echo $ticket->screenshot;?>" width="100px">
												</div>
										</div>
										<div class="col-md-4">
										<div class="form-group">
												<label for="field-2" class="control-label">Priority</label>
												<span id="error_status" style="color:red;"></span>
												<select class="form-control" name="priority" id="priority">
													<option value="">-- Select--</option>
													<option value="High" <?php if($ticket->priority=='High'){echo "selected";}?>>High</option>
													<option value="Mid" <?php if($ticket->priority=='Mid'){echo "selected";}?>>Mid</option>
													<option value="Low" <?php if($ticket->priority=='Low'){echo "selected";}?>>Low</option>
												</select>
												</div>	
										</div>
										<div class="col-md-4">
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script language="javascript" type="text/javascript">   
$(document).ready(function() {
	$('.select2').select2({ });
	$('.select3').select2({ });
	$('.select4').select2({ });
$("#save").click(function() {

var item_name = $("#item_name").val();
if(item_name=='')
{
	$("#error_item_name").html('Required!');
}
var location = $("#location").val();
if(location=='')
{
	$("#error_location").html('Required!');
}

var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}


if(item_name=='' || location=='' ||  status=='' )
{
	
	return false;
}

});
});
</script>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
    </body>
</html>