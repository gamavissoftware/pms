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
						 <div class="btn-group pull-right">
						  
                               
                            </div>
                           
                            <h4 class="page-title">Visit Form</h4>
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
								$query = $this->db->select('a.*, b.first_name, b.last_name')->from('engineer_visit a')->join('system_users b','a.user_id=b.user_id','left')->where('a.id',$this->uri->segment(3))->get();
								foreach($query->result() as $row);
								?>
								
                                   <form method="post" action="<?php echo page_url;?>Sales/edit_engineer_visit_data/<?php echo $this->uri->segment(3);?>"  enctype="multipart/form-data">
								   <input type="hidden" name="old_files" value="<?php echo $row->service_report;?>">
								   <input type="hidden" name="old_picture" value="<?php echo $row->picture;?>">
								  
										  <div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">YOUR NAME</label>
														 <span id="error_machine" style="color:red;">*</span>
														 <input type="text" class="form-control" name="yourname" id="yourname" value="<?php echo $row->first_name;?> <?php echo $row->last_name;?>" readonly>
													</div>
												</div>
												
												 <div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Company Name</label>
														 <span id="error_company_name" style="color:red;">*</span>
														<input type="text" class="form-control" name="company_name" id="company_name" value="<?php echo $row->company_name;?>">
													</div>
												</div>
												 <div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Sales Force No</label>
														 <span id="error_sales_force" style="color:red;">*</span>
														 <input type="text" class="form-control" name="sales_force" id="sales_force" value="<?php echo $row->sale_force_no;?>">
													</div>
												</div>
												
												<div class="col-md-5">
													<div class="form-group">
														<label>Warranty Status</label>
														<span id="error_warrenty_status" style="color:red;">*</span>
														<input type="text" class="form-control" name="warrenty_status" id="warrenty_status" value="<?php echo $row->warrenty_status;?>">
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														<label>Nature of Complaints</label>
														<span id="error_nature_of_complaints" style="color:red;">*</span>
														<input type="text" class="form-control" name="nature_of_complaints" id="nature_of_complaints" value="<?php echo $row->nature_of_complaints;?>">
													</div>
												</div>
												
													<div class="col-md-2">
													<div class="form-group">
														<label>ENGINEER</label>
														<span id="error_nature_of_complaints" style="color:red;">*</span>
													<select class="form-control select2" name="engineer" id="engineer">
													    <option>Select Engineer</option>
													    <?php 
													    $query = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('user_status','1')->where('hide_profile','0')->get();
													    foreach($query->result() as $row1){
													    ?>
													    <option value="<?php echo $row1->user_id;?>" <?php if($row->engineer==$row1->user_id){echo "selected";}?>><?php echo $row1->first_name." ".$row1->last_name;?></option>
													    <?php }?>
													</select>
													</div>
												</div>
											
												
												<div class="col-md-3">
													<div class="form-group">
														<label>Service Visit Report <?php if($row->service_report){?>
														<a href="<?php echo service_visit_report.$row->service_report;?> download">Click Here to Download</a>
														<?php }?></label>
														<span id="error_service_visit_report" style="color:red;">*</span>
														<input type="file" class="form-control" name="service_visit_report" id="service_visit_report" value="">
														
														
													</div>
												</div>
												
													<div class="col-md-4">
													<div class="form-group">
														<label>Actual Observation of Engineer</label>
														<span id="error_service_visit_report" style="color:red;">*</span>
														<input type="text" class="form-control" name="actual_ovservation" id="actual_ovservation" value="<?php echo $row->observation_of_engineer;?>">
														
													</div>
												</div>
													<div class="col-md-3">
													<div class="form-group">
														<label>Spare Parts</label>
														<span id="error_service_visit_report" style="color:red;">*</span>
													<select class="form-control" name="spare_parts" id="spare_parts">
													    <option>Select Option</option>
													    <option value="1" <?php if($row->spares_parts=='1'){echo "selected";}?>>Yes</option>
													    <option value="0" <?php if($row->spares_parts=='0'){echo "selected";}?>>No</option>
													</select>
													
													 <script>
												$(function() {
												 <?php if($row->spares_parts=='1'){}else{?>   
												$('#hidedetail').hide(); 
												$('#hidepicture').hide(); 
												<?php }?>
												$('#spare_parts').change(function(){
												if($('#spare_parts').val() == '1') {
												$('#hidedetail').show(); 
												$('#hidepicture').show();
												
												} else {
												$('#hidedetail').hide(); 
												$('#hidepicture').hide(); 
												
												} 
												});
												});
											</script>
														
													</div>
												</div>
												
												<div class="col-md-3" id="hidedetail" <?php if($row->spares_parts=='1'){}else{?>style="display:none" <?php }?>>
											    <div class="form-group">
											        <label>Parts Name</label>
											        <input type="" class="form-control" name="parts_name" id="parts_name" value="<?php echo $row->part_name;?>">
											    </div>
											</div>
											
												<div class="col-md-3" id="hidepicture" style="display:none">
													<div class="form-group">
														<label>Picture <?php if($row->service_report){?>
														<a href="<?php echo service_visit_report.$row->picture;?> download">Click Here to Download</a>
														<?php }?></label>
														<span id="error_picture" style="color:red;">*</span>
														<input type="file" class="form-control" name="picture" id="picture" value="">
														
													</div>
												</div>
											<div class="col-md-3">
												<div class="form-group">
												<label for="field-2" class="control-label">Case</label>
												<span id="error_case" style="color:red;">*</span>
												<select class="form-control" id="cases" name="case">
													<option value="">--Select Open--</option>
													<option value="1" <?php if($row->case_status=='1'){echo "selected";}?>>Open </option>
													<option value="0" <?php if($row->case_status=='0'){echo "selected";}?>>Closed</option>
												</select>
												</div>
											</div>
												
                                            </div>
										<div class="col-md-9"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" class="btn btn-success" value="Save">
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
     $('.select2').select2({ });
$("#save").click(function() {
var company_name = $("#company_name").val();
if(company_name=='')
{
	$("#error_company_name").html('Required!');
}
var sales_force = $("#sales_force").val();
if(sales_force=='')
{
	$("#error_sales_force").html('Required!');
}
var warrenty_status = $("#warrenty_status").val();
if(warrenty_status=='')
{
	$("#error_warrenty_status").html('Required!');
}
var nature_of_complaints = $("#nature_of_complaints").val();
if(nature_of_complaints=='')
{
	$("#error_nature_of_complaints").html('Required!');
}
var service_visit_report = $("#service_visit_report").val();
if(service_visit_report=='')
{
	$("#error_service_visit_report").html('Required!');
}
var cases = $("#cases").val();
if(cases=='')
{
	$("#error_case").html('Required!');
}


if(company_name=='' || sales_force=='' || warrenty_status=='' || nature_of_complaints=='' ||service_visit_report=='' || cases=='')
{
	
	return false;
}

});
});
</script>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
    </body>
</html>