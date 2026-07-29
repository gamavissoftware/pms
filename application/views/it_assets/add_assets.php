<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> IT Assets</title>
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
<style>
	.label {
    display: inline-block;
    max-width: 100%;
    margin-bottom: 5px;
    font-weight: 700;
    font-size: 10px !important;
}
table.pretty th {
				font-size:12px;
				
			}
table.pretty tbody td {
				font-size:12px;
				
			}
</style>
  <?PHP 
$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
foreach($q->result() as $LOGO);
?>

<style>

table.pretty thead th {

background: <?php echo $LOGO->colorcode;?>;

color:#fff;

font-weight:bold;

text-align:center;

}

</style>
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
            <div class="container-fluid">

                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right">
						  <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">Add New Item</button>

<button class="btn btn-warning waves-effect waves-light" data-toggle="modal" data-target="#filter-modal">Filter Data By Terms</button>
                           </div>
                           
                            <h4 class="page-title">IT Assets List</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
<div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered pretty">
                                <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th style="width:20%">Action</th>
                                    <th>User Name</th>
									<th>Assigned To</th>
									<th>IT Asset Name</th>
									<th>Computer Name</th>
									<th>Asset Type</th>
									<th>Location </th>
									<th>Department</th>
									<th>Microsoft Office</th>
									<th>Microsoft Office Licence Types</th>
									<th>Operating System</th>
							    	<th>Microsoft Windows Licence Types</th>
							    	<th>Anti Virus</th>
									<th>Brand Name</th>
									<th>Model No.</th>
									<th>Serial No.</th>
									<th>Part No.</th>
									<th>Issue</th>
									
									<th>Purchase Date</th>
									<th>Care Pack AMC Purchase Date</th>
									<th>Warranty Expire Date</th>
									<th>RAM</th>
									<th>Hard Disk Drive</th>
									<th>Processor</th>
									<th>Website</th>
                                        <th>Customer Care</th>
										<th>Vendor Name</th>
							    	<th>Monitor / TFT Serial No</th>
							    	<th>Battery Purchase Date</th>
							    	
							    	<th>Relationship / Accounts No.</th>
									<th>Telephone No. </th>
									<th>Software Key</th>
									<th>License No</th>
									<th>Login User</th>
									<th>Login Password</th>
									<th>Keyboard</th>
									<th>Mouse</th>
									<th>IP Address</th>
									<th>Screen</th>
									<th>Service Tag</th>
									<th>Renewal to be Done</th>
								
									<th>Invoice Attached</th>
                                    <th>Remark</th>
                                    <th>Available in Stock</th>
                                    
                                </tr>
                                </thead>
                                
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
 <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="<?php echo page_url;?>IT_Assets/add_items" enctype="multipart/form-data">
                                <div class="modal-dialog modal-full">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Add New Item</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                             
											<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Business Location</label>
														 <span id="error_business_loc" style="color:red;"></span>
														 <select class="form-control"  id="business_loc" name="business_loc">
													<option value="">--Select Location--</option>
													<?php 
													$this->db->select('a.company_name, a.business_loc_id,a.state_id, a.city_id,b. 	state_id,b.state_name,c.city_id,c.city_name')->from('business_location a')->join('states b','a.state_id=b.state_id','left')->join('cities c','a.city_id=c.city_id','left')->where('business_loc_status','1');
													$this->db->order_by('a.company_name','asc');
													$query = $this->db->get();
													$res = $query->result();
													foreach($res as $row){
													?>
													<option value="<?php echo $row->business_loc_id;?>"><?php echo $row->company_name;?>(<?php echo $row->state_name;?>, <?php echo $row->city_name;?>)</option>
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
												
											   <div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Department</label>
														 <span id="error_location" style="color:red;"></span>
														 <select class="form-control select3" id="location" name="location">
													<option value="">--Select Department--</option>
													
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
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Antivirus Key</label>
														 <span id="error_antivirus_key" style="color:red;"></span>
														<input type="" class="form-control" name="antivirus_key" id="antivirus_key" value="">
													</div>
												</div>
												
													<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">IT Asset Name</label>
														 <span id="error_antivirus_key" style="color:red;"></span>
														<input type="" class="form-control" name="it_asset_name" id="it_asset_name" value="">
													</div>
												</div>
												
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Asset Type</label>
														 <span id="error_asset_type" style="color:red;"></span>
														 <select class="form-control select3" id="asset_type" name="asset_type">
													<option value="">--Select Asset Type--</option>
													<?php $query = $this->db->select('type_id, asset_type,status')->from('asset_type')->where('status','1')->get();
													foreach($query->result() as $asset_type){?>
													<option value="<?php echo $asset_type->type_id;?>"><?php echo $asset_type->asset_type;?></option>
													<?php }?>
												</select>
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Battery Purchase Date</label>
														 <span id="error_battery_purchase_date" style="color:red;"></span>
														<input type="date" class="form-control" name="battery_purchase_date" id="battery_purchase_date" value="">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Battery Warranty Expire</label>
														 <span id="error_battery_warranty_expr" style="color:red;"></span>
														<input type="date" class="form-control" name="battery_warranty_expr" id="battery_warranty_expr" value="">
													</div>
												</div>
												
												
													<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Brand</label>
														 <span id="error_brand_name" style="color:red;"></span>
														 <select class="form-control select3" id="brand_name" name="brand_name">
													<option value="">--Select Brand --</option>
													<?php $query = $this->db->select('brand_id, brand_name,status')->from('asset_brands')->where('status','1')->get();
													foreach($query->result() as $brand){?>
													<option value="<?php echo $brand->brand_id;?>"><?php echo $brand->brand_name;?></option>
													<?php }?>
													
												</select>
													</div>
												</div>
												
													<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px;">AMC Purchase Date</span></label>
														 
														<input type="date" class="form-control" name="amc_purchase_date" id="amc_purchase_date" value="">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Computer Name</label>
														 <span id="error_computer_name" style="color:red;"></span>
														 <input type="text" class="form-control" name="computer_name" id="computer_name" value="">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Customer Care</label>
														 <span id="error_customer_care" style="color:red;"></span>
														 <input type="text" class="form-control" name="customer_care" id="customer_care" value="">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Harddisk Drive </label>
														 <span id="error_harddisk_drive" style="color:red;"></span>
														 <input type="text" class="form-control" name="harddisk_drive" id="harddisk_drive" value="">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">IP Address</label>
														 <span id="error_ip_address" style="color:red;"></span>
														 <input type="text" class="form-control" name="ip_address" id="ip_address" value="">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Invoice Attached</label>
														 <span id="error_asset_type" style="color:red;"></span>
														 <select class="form-control select3" id="invoice_attached" name="invoice_attached">
													<option value="">--Select--</option>
													<option value="Yes">Yes</option>
													<option value="No">No</option>
													
												</select>
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Issue</label>
														 <span id="error_issue" style="color:red;"></span>
														 <select class="form-control select3" id="issue" name="issue">
													<option value="">--Select--</option>
													<option value="Yes">Yes</option>
													<option value="No">No</option>
													
												</select>
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">KeyBoard</label>
														 <span id="error_keyboard" style="color:red;"></span>
														 <input type="text" class="form-control" name="keyboard" id="keyboard" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">License No.</label>
														 <span id="error_license_no" style="color:red;"></span>
														 <input type="text" class="form-control" name="license_no" id="license_no" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Login User</label>
														 <span id="error_login_user" style="color:red;"></span>
														 <input type="text" class="form-control" name="login_user" id="login_user" value="">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Login Password</label>
														 <span id="error_login_password" style="color:red;"></span>
														 <input type="text" class="form-control" name="login_password" id="login_password" value="">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Microsoft Office</label>
														 <span id="error_ms_office" style="color:red;"></span>
														 <select class="form-control select3" id="ms_office" name="ms_office">
													<option value="">--Select Office--</option>
													<?php $query = $this->db->select('ms_office_id, ms_office,status')->from('asset_microsift_office')->where('status','1')->get();
													foreach($query->result() as $ms_office){?>
													<option value="<?php echo $ms_office->ms_office_id;?>"><?php echo $ms_office->ms_office;?></option>
													<?php }?>
												</select>
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px;">Microsoft Office Licence Types</span></label>
														 <span id="error_asset_type" style="color:red;"></span>
														 <select class="form-control select3" id="ms_office_license_type" name="ms_office_license_type">
													<option value="">--Select Office Licence Type--</option>
													<?php $query = $this->db->select('ms_office_id, ms_office_licence_type,status')->from('asset_microsift_office_licence_type')->where('status','1')->get();
													foreach($query->result() as $ms_office_license){?>
													<option value="<?php echo $ms_office_license->ms_office_id;?>"><?php echo $ms_office_license->ms_office_licence_type;?></option>
													<?php }?>
												</select>
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px;"> Windows Licence Types</span></label>
														 <span id="error_window_license_type" style="color:red;"></span>
														 <select class="form-control select3" id="window_license_type" name="window_license_type">
													<option value="">--Select Windows Licence Type--</option>
													<?php $query = $this->db->select('ms_office_id, ms_office_licence_type,status')->from('asset_microsift_windows_licence_type')->where('status','1')->get();
													foreach($query->result() as $ms_window_license){?>
													<option value="<?php echo $ms_window_license->ms_office_id;?>"><?php echo $ms_window_license->ms_office_licence_type;?></option>
													<?php }?>
												</select>
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Model No.</label>
														 <span id="error_model_no" style="color:red;"></span>
														 <input type="text" class="form-control" name="model_no" id="model_no" value="">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Monitor / TFT Serial No.</label>
														 <span id="error_monitor_tft_sr_no" style="color:red;"></span>
														 <input type="text" class="form-control" name="monitor_tft_sr_no" id="monitor_tft_sr_no" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Mouse
</label>
														 <span id="error_monitor_tft_sr_no" style="color:red;"></span>
														 <input type="text" class="form-control" name="mouse" id="mouse" value="">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px;"> Operating System</span></label>
														 <span id="error_operating_system" style="color:red;"></span>
														 <select class="form-control select3" id="operating_system" name="operating_system">
													<option value="">--Select Operating System--</option>
													<?php $query = $this->db->select('operationg_id, operating_system,status')->from('asset_operating_system')->where('status','1')->get();
													foreach($query->result() as $operating_system){?>
													<option value="<?php echo $operating_system->operationg_id;?>"><?php echo $operating_system->operating_system;?></option>
													<?php }?>
												</select>
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Operating System Type</label>
														 <span id="error_issue" style="color:red;"></span>
														 <select class="form-control select3" id="operating_system_type" name="operating_system_type">
													<option value="">--Select--</option>
													<option value="64 Bit">64 Bit</option>
													<option value="32 Bit">32 Bit</option>
													
												</select>
													</div>
												</div>
												
											<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Part No.</label>
														 <span id="error_part_no" style="color:red;"></span>
														 <input type="text" class="form-control" name="part_no" id="part_no" value="">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Processor</label>
														 <span id="error_processor" style="color:red;"></span>
														 <input type="text" class="form-control" name="processor" id="processor" value="">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Purchase Date</label>
														 <span id="error_purchase_date" style="color:red;"></span>
														 <input type="date" class="form-control" name="purchase_date" id="purchase_date" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">RAM</label>
														 <span id="error_ram" style="color:red;"></span>
														 <input type="text" class="form-control" name="ram" id="ram" value="">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px">Relationship / Accounts No.</span></label>
														 <span id="error_ram" style="color:red;"></span>
														 <input type="text" class="form-control" name="relationship_acc_no" id="relationship_acc_no" value="">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Renewal to be Done</label>
														 <span id="error_issue" style="color:red;"></span>
														 <select class="form-control select3" id="renewal_done" name="renewal_done">
														<option value="">--Select--</option>
														<option value="Yes">Yes</option>
														<option value="No">No</option>
													
												</select>
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px">Screen</span></label>
														 <span id="error_ram" style="color:red;"></span>
														 <input type="text" class="form-control" name="screen" id="screen" value="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px">Serial No.
</span></label>
														 <span id="error_ram" style="color:red;"></span>
														 <input type="text" class="form-control" name="serial_number" id="serial_number" value="">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px">Service Tag</span></label>
														 <span id="error_ram" style="color:red;"></span>
														 <input type="text" class="form-control" name="service_tag" id="service_tag" value="">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px">Software Key
</span></label>
														 <span id="error_ram" style="color:red;"></span>
														 <input type="text" class="form-control" name="software_key" id="software_key" value="">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px">Telephone No.
</span></label>
														 <span id="error_ram" style="color:red;"></span>
														 <input type="text" class="form-control" name="telephone" id="telephone" value="">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px">User Name
</span></label>
														 <span id="error_ram" style="color:red;"></span>
														 <input type="text" class="form-control" name="user_name" id="user_name" value="">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px">Vendor Name
</span></label>
														 <span id="error_ram" style="color:red;"></span>
														 <input type="text" class="form-control" name="vendor_name" id="vendor_name" value="">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px">Warranty End date
</span></label>
														 <span id="error_ram" style="color:red;"></span>
														 <input type="date" class="form-control" name="warranty_end_date" id="warranty_end_date" value="">
													</div>
												</div>
												
												<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px">Website

</span></label>
														 <span id="error_ram" style="color:red;"></span>
														 <input type="text" class="form-control" name="website" id="website" value="">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px">Assigned to

</span></label>
														 <span id="error_ram" style="color:red;"></span>
														<select class="form-control select3" id="assigned_to" name="assigned_to">
													<option value="">--Assigned To--</option>
													<?php 
													$query= $this->db->select('user_id, first_name, last_name, user_status, hide_profile')->from('system_users')->where('hide_profile','0')->where('user_status','1')->get();
													foreach($query->result() as $assign){
													?>
													<option value="<?php echo $assign->user_id;?>"><?php echo $assign->first_name;?> <?php echo $assign->last_name;?></option>
													<?php }?>
													</select>
													</div>
												</div>
												
											<div class="col-md-2">
													<div class="form-group">
														<label>Attached File</label>
														<input type="file" class="form-control" name="attachment" id="attachment" value=""> 
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														<label>Available in stock</label>
														<select name="available_status" id="available_status" class="form-control">
														    <option value="">-- Select Option-</option>
														     <option value="1">Yes</option>
														      <option value="0">No</option>
														    
														</select>
													
													</div>
												</div>	
												
													<div class="col-md-6">
												    <div class="form-group">
												        <label>Remark</label>
												        <textarea class="form-control" name="it_remarks" id="it_remarks"></textarea>
												    </div>
												    
												</div>
												
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div><!-- /.modal -->

<div id="filter-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="<?php echo page_url;?>IT_Assets/filter_by_type">
                                <div class="modal-dialog modal-full">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Sort By</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Asset Type</label>
														
														 <select class="form-control select3" id="assettype" name="assettype">
													<option value="">--Select Asset Type--</option>
													<?php $query = $this->db->select('type_id, asset_type,status')->from('asset_type')->where('status','1')->get();
													foreach($query->result() as $asset_type){?>
													<option value="<?php echo $asset_type->type_id;?>"><?php echo $asset_type->asset_type;?></option>
													<?php }?>
												</select>
													</div>
												</div>
											<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Business Location</label>
														
														 <select class="form-control"  id="businessloc" name="businessloc">
													<option value="">--Select Location--</option>
													<?php 
													$this->db->select('a.company_name, a.business_loc_id,a.state_id, a.city_id,b. 	state_id,b.state_name,c.city_id,c.city_name')->from('business_location a')->join('states b','a.state_id=b.state_id','left')->join('cities c','a.city_id=c.city_id','left')->where('business_loc_status','1');
													$this->db->order_by('a.company_name','asc');
													$query = $this->db->get();
													$res = $query->result();
													foreach($res as $row){
													?>
													<option value="<?php echo $row->business_loc_id;?>"><?php echo $row->company_name;?>(<?php echo $row->state_name;?>, <?php echo $row->city_name;?>)</option>
											<?php }?>		
												</select>
												<script type="text/javascript">
											
													$("#businessloc").change(function(){
													var business_loc=$("#businessloc").val();
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Tech_support/select_installed_location",
													data:"business_loc="+business_loc,
													success:function(data){
													$("#department").html(data);
													}
													});
													});
											
												</script>
												
													</div>
												</div>
												
											   <div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Department</label>
														 <span id="error_location" style="color:red;"></span>
														 <select class="form-control select3" id="department" name="department">
													<option value="">--Select Department--</option>
													
												</select>
							
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px;">Microsoft Office Licence Types</span></label>
														
														 <select class="form-control select3" id="ms_office_license_type" name="ms_office_license_type">
													<option value="">--Select Office Licence Type--</option>
													<?php $query = $this->db->select('ms_office_id, ms_office_licence_type,status')->from('asset_microsift_office_licence_type')->where('status','1')->get();
													foreach($query->result() as $ms_office_license){?>
													<option value="<?php echo $ms_office_license->ms_office_id;?>"><?php echo $ms_office_license->ms_office_licence_type;?></option>
													<?php }?>
												</select>
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px;"> Windows Licence Types</span></label>
														 <span id="error_window_license_type" style="color:red;"></span>
														 <select class="form-control select3" id="window_license_type" name="window_license_type">
													<option value="">--Select Windows Licence Type--</option>
													<?php $query = $this->db->select('ms_office_id, ms_office_licence_type,status')->from('asset_microsift_windows_licence_type')->where('status','1')->get();
													foreach($query->result() as $ms_window_license){?>
													<option value="<?php echo $ms_window_license->ms_office_id;?>"><?php echo $ms_window_license->ms_office_licence_type;?></option>
													<?php }?>
												</select>
													</div>
												</div>
											
											<div class="col-md-2">
													<div class="form-group">
														<label>Available in stock</label>
														<select name="available_status" id="available_status" class="form-control">
														    <option value="">-- Select Option-</option>
														     <option value="1">Yes</option>
														      <option value="0">No</option>
														    
														</select>
													
													</div>
												</div>	
												
											<div class="col-md-2">
												<div class="form-group">
													<label>Asset Value</label>
													<input type="number" class="form-control" step="0.1" name="asset_value" id="asset_value" value="">
												</div>
											</div>
												
                                            </div>
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Search"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div><!-- /.modal -->
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

       

        <script>
$( document ).ready(function() {
$('#example').dataTable({
"bProcessing": true,
"pageLength": 500,
"pagination":true,
dom: 'Bfrtip',
        buttons: [
            'excel'
        ],
"sAjaxSource": "<?php echo page_url;?>IT_Assets/IT_item_list",
"aoColumns": [
				{ mData: 'sr_no' } ,
				{ mData: 'edit' },
					{ mData: 'user_name' },
				{ mData: 'assigned_to' },
				{ mData: 'it_asset_name' },
					{ mData: 'computer_name' },
				{ mData: 'asset_type' },
				{ mData: 'business_location' } ,
				{ mData: 'location' },
					{ mData: 'ms_office' },
				{ mData: 'ms_office_licence_type' },
				{ mData: 'operating_system' },
					{ mData: 'ms_office_window_licence_type' },
					{ mData: 'antivirus_key' },
				{ mData: 'brand_name' },
				{ mData: 'model_number' },
				{ mData: 'serial_number' },
				{ mData: 'part_number' },
				{ mData: 'issue' },
			
				{ mData: 'purchase_date' },
				{ mData: 'amc_purchase_date' },
				{ mData: 'warranty_end_date' },
				{ mData: 'ram' },
				{ mData: 'harddisk_drive' },
				{ mData: 'processor' },
				{ mData: 'website' },
				
				{ mData: 'customer_care' },
				
			
					{ mData: 'vendor_name' },
				{ mData: 'monitor_tft_sr_number' },
				{ mData: 'battery_purchase_date' },
				
				{ mData: 'relationship_acc_no' },
				{ mData: 'telephone_number' },
				{ mData: 'software_key' },
				{ mData: 'license_number' },
				{ mData: 'login_user' },
				{ mData: 'login_password' },
					{ mData: 'keyboard' },
				{ mData: 'mouse' },
				{ mData: 'ip_aadress' },
				{ mData: 'screen' },
				{ mData: 'service_tag' },
				{ mData: 'renewal_to_be_done' },
				
						{ mData: 'invoice_attached' },
						{ mData: 'it_remarks' },
						{ mData: 'available_in_stock' }
				
				
				
		]
});   
});

</script>
<script language="javascript" type="text/javascript">   

$(document).ready(function() {
	$('.select2').select2({ });
	$('.select3').select2({ });
	$('.select4').select2({ });
$("#save").click(function() {
	var business_loc= $("#business_loc").val();
if(business_loc=='')
{
	$("#error_business_loc").html('Required!');
}
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


if(business_loc==''|| item_name=='' || location=='' ||  status=='' )
{
	
	return false;
}

});
});
</script>
 <script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
    </body>
</html>