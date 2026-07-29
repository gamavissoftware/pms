<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Edit IT Item</title>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 
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
						  
                               
                            </div>
                           
                            <h4 class="page-title">Edit Item Detail</h4>
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
								$this->db->select('*')->from('presto_it_assets')->where('asset_id',$id);
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $asset_item)
							//	echo "<pre>"; print_r($asset_item);exit;
								?>
								
                                   <form method="post" action="<?php echo page_url;?>IT_Assets/update_item_detail/<?php echo $asset_item->asset_id;?>" enctype="multipart/form-data">
										<input type="hidden" name="old_image" value="<?php echo $asset_item->attachment_file;?>"> 		
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
													<option value="<?php echo $row->business_loc_id;?>" <?php if($asset_item->business_location_id==$row->business_loc_id){echo "selected";}?>><?php echo $row->company_name;?>(<?php echo $row->state_name;?>, <?php echo $row->city_name;?>)</option>
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
													<option value="<?php echo $asset_item->department_id;?>">
													<?php 
														$query = $this->db->select('location_id, business_loc_id, installed_location')->from('installed_location')->where('location_id',$asset_item->department_id)->get();
														foreach($query->result() as $department)
														{
															echo $department->installed_location;
														}
													?>
													</option>
													
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
														<input type="" class="form-control" name="antivirus_key" id="antivirus_key" value="<?php echo $asset_item->antivirus_key;?>">
													</div>
												</div>
												
													<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">IT Asset Name</label>
														 <span id="error_antivirus_key" style="color:red;"></span>
														<input type="" class="form-control" name="it_asset_name" id="it_asset_name" value="<?php echo $asset_item->it_asset_name;?>">
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
													<option value="<?php echo $asset_type->type_id;?>" <?php if($asset_item->asset_type==$asset_type->type_id){echo "selected";}?>><?php echo $asset_type->asset_type;?></option>
													<?php }?>
												</select>
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Battery Purchase Date</label>
														 <span id="error_battery_purchase_date" style="color:red;"></span>
														<input type="date" class="form-control" name="battery_purchase_date" id="battery_purchase_date" value="<?php echo $asset_item->battery_purchase_date;?>">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Battery Warranty Expire</label>
														 <span id="error_battery_warranty_expr" style="color:red;"></span>
														<input type="date" class="form-control" name="battery_warranty_expr" id="battery_warranty_expr" value="<?php echo $asset_item->battery_warrenty_expire;?>">
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
													<option value="<?php echo $brand->brand_id;?>" <?php if($asset_item->brand_id==$brand->brand_id){echo "selected";}?>><?php echo $brand->brand_name;?></option>
													<?php }?>
													
												</select>
													</div>
												</div>
												
													<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px;">Care Pack / AMC Purchase Date</span></label>
														 
														<input type="date" class="form-control" name="amc_purchase_date" id="amc_purchase_date" value="<?php echo $asset_item->amc_purchase_date;?>">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Computer Name</label>
														 <span id="error_computer_name" style="color:red;"></span>
														 <input type="text" class="form-control" name="computer_name" id="computer_name" value="<?php echo $asset_item->computer_name;?>">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Customer Care</label>
														 <span id="error_customer_care" style="color:red;"></span>
														 <input type="text" class="form-control" name="customer_care" id="customer_care" value="<?php echo $asset_item->customer_care;?>">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Harddisk Drive </label>
														 <span id="error_harddisk_drive" style="color:red;"></span>
														 <input type="text" class="form-control" name="harddisk_drive" id="harddisk_drive" value="<?php echo $asset_item->harddisk_drive;?>">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">IP Address</label>
														 <span id="error_ip_address" style="color:red;"></span>
														 <input type="text" class="form-control" name="ip_address" id="ip_address" value="<?php echo $asset_item->ip_aadress;?>">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Invoice Attached</label>
														 <span id="error_asset_type" style="color:red;"></span>
														 <select class="form-control select3" id="invoice_attached" name="invoice_attached">
													<option value="">--Select--</option>
													<option value="Yes" <?php if($asset_item->invoice_attached=='Yes'){echo "selected";}?>>Yes</option>
													<option value="No" <?php if($asset_item->invoice_attached=='No'){echo "selected";}?>>No</option>
													
												</select>
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Issue</label>
														 <span id="error_issue" style="color:red;"></span>
														 <select class="form-control select3" id="issue" name="issue">
													<option value="">--Select--</option>
													<option value="Yes" <?php if($asset_item->issue=='Yes'){echo "selected";}?>>Yes</option>
													<option value="No" <?php if($asset_item->issue=='No'){echo "selected";}?>>No</option>
													
												</select>
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">KeyBoard</label>
														 <span id="error_keyboard" style="color:red;"></span>
														 <input type="text" class="form-control" name="keyboard" id="keyboard" value="<?php echo $asset_item->keyboard;?>">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">License No.</label>
														 <span id="error_license_no" style="color:red;"></span>
														 <input type="text" class="form-control" name="license_no" id="license_no" value="<?php echo $asset_item->license_number;?>">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Login User</label>
														 <span id="error_login_user" style="color:red;"></span>
														 <input type="text" class="form-control" name="login_user" id="login_user" value="<?php echo $asset_item->login_user;?>">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Login Password</label>
														 <span id="error_login_password" style="color:red;"></span>
														 <input type="text" class="form-control" name="login_password" id="login_password" value="<?php echo $asset_item->login_password;?>">
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
													<option value="<?php echo $ms_office->ms_office_id;?>" <?php if($asset_item->ms_office==$ms_office->ms_office_id){echo "selected";}?>><?php echo $ms_office->ms_office;?></option>
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
													<option value="<?php echo $ms_office_license->ms_office_id;?>" <?php if($asset_item->ms_office_license_type==$ms_office_license->ms_office_id){echo "selected";}?>><?php echo $ms_office_license->ms_office_licence_type;?></option>
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
													<option value="<?php echo $ms_window_license->ms_office_id;?>" <?php if($asset_item->window_license_type==$ms_window_license->ms_office_id){echo "selected";}?>><?php echo $ms_window_license->ms_office_licence_type;?></option>
													<?php }?>
												</select>
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Model No.</label>
														 <span id="error_model_no" style="color:red;"></span>
														 <input type="text" class="form-control" name="model_no" id="model_no" value="<?php echo $asset_item->model_number;?>">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Monitor / TFT Serial No.</label>
														 <span id="error_monitor_tft_sr_no" style="color:red;"></span>
														 <input type="text" class="form-control" name="monitor_tft_sr_no" id="monitor_tft_sr_no" value="<?php echo $asset_item->monitor_tft_sr_number;?>">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Mouse
</label>
														 <span id="error_monitor_tft_sr_no" style="color:red;"></span>
														 <input type="text" class="form-control" name="mouse" id="mouse" value="<?php echo $asset_item->mouse;?>">
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
													<option value="<?php echo $operating_system->operationg_id;?>" <?php if($asset_item->operating_system==$operating_system->operationg_id){echo "selected";}?>><?php echo $operating_system->operating_system;?></option>
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
													<option value="64 Bit" <?php if($asset_item->operating_sys_type=='64 Bit'){echo "selected";}?>>64 Bit</option>
													<option value="32 Bit" <?php if($asset_item->operating_sys_type=='32 Bit'){echo "selected";}?>>32 Bit</option>
													
												</select>
													</div>
												</div>
												
											<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Part No.</label>
														 <span id="error_part_no" style="color:red;"></span>
														 <input type="text" class="form-control" name="part_no" id="part_no" value="<?php echo $asset_item->part_number;?>">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Processor</label>
														 <span id="error_processor" style="color:red;"></span>
														 <input type="text" class="form-control" name="processor" id="processor" value="<?php echo $asset_item->processor;?>">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Purchase Date</label>
														 <span id="error_purchase_date" style="color:red;"></span>
														 <input type="date" class="form-control" name="purchase_date" id="purchase_date" value="<?php echo $asset_item->purchase_date;?>">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">RAM</label>
														 <span id="error_ram" style="color:red;"></span>
														 <input type="text" class="form-control" name="ram" id="ram" value="<?php echo $asset_item->ram;?>">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px">Relationship / Accounts No.</span></label>
														 <span id="error_ram" style="color:red;"></span>
														 <input type="text" class="form-control" name="relationship_acc_no" id="relationship_acc_no" value="<?php echo $asset_item->relationship_acc_no;?>">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Renewal to be Done</label>
														 <span id="error_issue" style="color:red;"></span>
														 <select class="form-control select3" id="renewal_done" name="renewal_done">
														<option value="">--Select--</option>
														<option value="Yes" <?php if($asset_item->renewal_to_be_done=='Yes'){echo "selected";}?>>Yes</option>
													<option value="No" <?php if($asset_item->renewal_to_be_done=='No'){echo "selected";}?>>No</option>
													
												</select>
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px">Screen</span></label>
														 <span id="error_ram" style="color:red;"></span>
														 <input type="text" class="form-control" name="screen" id="screen" value="<?php echo $asset_item->screen;?>">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px">Serial No.
</span></label>
														 <span id="error_ram" style="color:red;"></span>
														 <input type="text" class="form-control" name="serial_number" id="serial_number" value="<?php echo $asset_item->serial_number;?>">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px">Service Tag</span></label>
														 <span id="error_ram" style="color:red;"></span>
														 <input type="text" class="form-control" name="service_tag" id="service_tag" value="<?php echo $asset_item->service_tag;?>">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px">Software Key
</span></label>
														 <span id="error_ram" style="color:red;"></span>
														 <input type="text" class="form-control" name="software_key" id="software_key" value="<?php echo $asset_item->software_key;?>">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px">Telephone No.
</span></label>
														 <span id="error_ram" style="color:red;"></span>
														 <input type="text" class="form-control" name="telephone" id="telephone" value="<?php echo $asset_item->telephone_number;?>">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px">User Name
</span></label>
														 <span id="error_ram" style="color:red;"></span>
														 <input type="text" class="form-control" name="user_name" id="user_name" value="<?php echo $asset_item->user_name;?>">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px">Vendor Name
</span></label>
														 <span id="error_ram" style="color:red;"></span>
														 <input type="text" class="form-control" name="vendor_name" id="vendor_name" value="<?php echo $asset_item->vendor_name;?>">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px">Warranty End date
</span></label>
														 <span id="error_ram" style="color:red;"></span>
														 <input type="date" class="form-control" name="warranty_end_date" id="warranty_end_date" value="<?php echo $asset_item->warranty_end_date;?>">
													</div>
												</div>
												
												<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label"><span style="font-size:12px">Website

</span></label>
														 <span id="error_ram" style="color:red;"></span>
														 <input type="text" class="form-control" name="website" id="website" value="<?php echo $asset_item->website;?>">
													</div>
													
													<div class="form-group">
													<label>Asset Value</label>
													<input type="number" class="form-control" step="0.1" name="asset_value" id="asset_value" value="<?php echo $asset_item->assets_value;?>">
												</div>
												</div>
												
												<div class="col-md-4">
													<div class="form-group">
													<label for="field-2" class="control-label"><span style="font-size:12px">Assigned to</span></label>
												<span id="error_ram" style="color:red;"></span>
													<select class="form-control" id="assigned_to" name="assigned_to">
													<option value="">--Assigned To--</option>
													<?php 
													$query= $this->db->select('user_id, first_name, last_name, user_status, hide_profile')->from('system_users')->where('hide_profile','0')->where('user_status','1')->get();
													foreach($query->result() as $assign){
													?>
													<option value="<?php echo $assign->user_id;?>" <?php if($assign->user_id==$asset_item->assigned_to){echo "selected";}?>><?php echo $assign->first_name;?> <?php echo $assign->last_name;?></option>
													<?php }?>
													</select>
													</div>
												</div>
											
											<div class="col-md-2">
													<div class="form-group">
														<label>Attached File</label>
														<input type="file" class="form-control" name="attachment" id="attachment" value=""> 
														<img src="<?php echo itassets_path;?><?php echo $asset_item->attachment_file;?>" width="100px" height="100px">
														<?php if($asset_item->attachment_file!==''){?>
														<a href="<?php echo itassets_path;?><?php echo $asset_item->attachment_file;?>" target="_blank"><span class="btn btn-success ">View Attachment</span></a>
														<?php }?>
													</div>
												</div>
												
													<div class="col-md-2">
													<div class="form-group">
														<label>Available in stock</label>
														<select name="available_status" id="available_status" class="form-control">
														    <option value="">-- Select Option-</option>
														     <option value="1" <?php if($asset_item->available_in_stock=='1'){?>selected<?php }?>>Yes</option>
														      <option value="0" <?php if($asset_item->available_in_stock=='0'){?>selected<?php }?>>No</option>
														    
														</select>
													
													</div>
												</div>
												
													<div class="col-md-6">
												    <div class="form-group">
												        <label>Remark</label>
												        <textarea class="form-control" name="it_remarks" id="it_remarks"><?php echo $asset_item->it_remarks;?></textarea>
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
<!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script language="javascript" type="text/javascript">   

$(document).ready(function() {
$("#save").click(function() {
	var question= $("#question").val();
if(question=='')
{
	$("#error_question").html('Required!');
}


if(question=='')
{
	
	return false;
}

});
});
</script>
    </body>
</html>