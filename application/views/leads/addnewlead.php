<?php 
$user_id = base64_decode($this->uri->segment(3));
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright;?>">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title><?php echo sitetitle;?></title>
        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>plugins/audio/manage-audio.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
        <style>


				#mybutton {
				  position: fixed;
				  bottom: -4px;
				  right: 10px;
				}
				.select2-container {
				    width: 100% !important;
				}

			   .add_more {
		        margin-top: 33px;
		        }

		        .remove {
		        margin-top: 33px;
		        }

		        .delete_product {
		        margin-top: 33px;
		        }
			</style>
			  <style>
        .select2-container--default .select2-selection--single
        {
            height: 34px !important;
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
        <div class="container">

                <!-- Page-Title -->
            <div class="row" style="margin-top:20px;">
                <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
	                <div class="page-title-box">
	                    <!-- <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success" name="action_button"><i class="fa fa-arrow-left"></i>Back</button></a> -->
					 <div class="btn-group pull-right"></div>
	                   
	                    <h6 class="page-title">Add New Lead</h6>
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

				<form id="loginForm" method="post" action="<?php echo page_url;?>Leads/add_open_leads/<?php echo $user_id;?>" enctype="multipart/form-data" onsubmit="return validate_lead();">
                <div class="row">


                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Create Date</label>
                            <span style="color:red;">*</span>
                            <span id="error_create_date" style="color:red;"></span>
                            <input type="text" id="create_date" class="mand form-control" name="create_date" value="<?php echo date('d-m-Y');?>" autocomplete="nope" required>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Lead Source</label>
                            <span style="color:red;">*</span>
                            <span id="error_lead_source" style="color:red;"></span>
                            <select class="form-control mand" id="lead_source" name="lead_source" onchange="getReferralName()" required>
                                    <?php $query = $this->db->select('source_id, lead_source, status')
                                                            ->from('lead_source')
                                                            ->where('status',1)
                                                            ->get();

                                    foreach($query->result() as $lead_source) { ?>
                                    <option value="<?php echo $lead_source->source_id;?>"><?php echo $lead_source->lead_source;?> </option>   
                                    <?php }?>   
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-3 referral_name" style="display: none;">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Referral Name</label>
                            <span style="color:red;">*</span>
                            <span id="error_create_date" style="color:red;"></span>
                            <input type="text" class="form-control" id="referral_name" name="referral_name" autocomplete="nope">
                        </div>
                    </div>

                      <div class="col-sm-3">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Customer Type</label>
                            <span style="color:red;">*</span>
                            <select id="patient_type" name="patient_type" class="mand form-control" required onchange="check_business()">
                                <option value="">--Select Nature of Business--</option> 
                                <?php 
                                    $this->db->select('*')
                                             ->from('patient_type')
                                             ->where('status','1');
                                    $query = $this->db->get();
                                    $res = $query->result();

                                    foreach($res as $patient_type){?>   
                                    <option value="<?php echo $patient_type->patient_id;?>"> <?php echo $patient_type->patient_type;?> </option>    

                                    <?php }?>
                            </select>
                        </div>
                    </div>


                    <div class="col-sm-3" style="display:none" id="distributorData">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Distributor</label>
                            <span style="color:red;">*</span>
                            <select id="distributor" name="distributor" class="form-control">
                                <option value="">--Select Distributor--</option> 
                                <?php 
                                 $role=$_SESSION['logged_in']['role']; 
                                    $this->db->select('*')
                                             ->from('distributor');
                                             if( $role==1){

                                             }else{
                                                $this->db->where('added_by',$user_id);
                                             }

                                    $query = $this->db->get();
                                    if($query->num_rows()>0)
                                    {
                                    $res = $query->result();
                                    foreach($res as $patient_type){
                                    ?>   
                                    <option value="<?php echo $patient_type->id;?>"><?php echo $patient_type->firm_name;?> </option>    
                                    <?php } }?>
                            </select>
                        </div>
                    </div>

                    <?php 
                        $role=$_SESSION['logged_in']['role']; 
                        if($role==1){?>
                              <div class="col-sm-2">
                        <div class="form-group">
                            <label>Zone</label>
                             <span style="color:red;">*</span>
                            <select class="form-control" name="zone" id="zone" onchange="zonewiseuser();">
                                <option value="">Select Zone</option>
                                <?php 
                                    $q = $this->db->select('id,zone')->from('saleszone')->where('status',1)->get();
                                    foreach($q->result() as $rows){?>
                                        <option value="<?php echo $rows->id;?>"><?php echo $rows->zone;?></option>

                                    <?php }
                                ?>
                                
                            </select>
                        </div>
                    </div>

                      <?php  }else{
                        $q = $this->db->select('a.zoneid')->from('saleszoneusers a')->where('a.userid',$user_id)->get();
                        foreach($q->result() as $rowss);
                    ?>
                      <div class="col-sm-2">
                        <div class="form-group">
                            <label>Zone</label>
                             <span style="color:red;">*</span>
                            <select class="form-control" name="zone" id="zone" onchange="zonewiseuser();">
                                <?php 
                                    $q = $this->db->select('id,zone')->from('saleszone')->where('id',$rowss->zoneid)->get();
                                    foreach($q->result() as $rows){?>
                                        <option value="<?php echo $rows->id;?>"><?php echo $rows->zone;?></option>

                                    <?php }
                                ?>
                                
                            </select>
                        </div>
                    </div>

                <?php }?>

                  

                    <script type="text/javascript">
                     function zonewiseuser(){
                     	var zone = $("#zone").val();
						$.ajax({
						type:"post",
						url:"<?php echo page_url;?>Leads/saleszonewiseuser",
						data:"zone="+zone,
						success:function(data){
							$("#assigntozoneuser").html(data);
						}
						});
                     }
                    </script>

                     <?php 
                    $userrole = $_SESSION['logged_in']['role'];	
                    if( $userrole==1){?>
                    	  <div class="col-sm-3">
                        <div class="form-group">
                            <label for="field-2" class="control-label">Assign Lead</label>
                            <span style="color:red;">*</span>
                            <select class="form-control" name="assigntouser" id="assigntozoneuser">
                            	
                            </select>
                        </div>
                    </div>

                   <?php  }else{?>
                   	 <div class="col-sm-3">
                        <div class="form-group">
                            <label for="field-2" class="control-label">Assign Lead</label>
                            <span style="color:red;">*</span>
                            <select class="form-control" name="assigntouser" id="assigntouser">
                            	<?php 
                            		$q = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('user_status',1)->where('user_id',$user_id)->get();
                            		foreach($q->result() as $user){?>
                            			<option value="<?php echo $user->user_id;?>"><?php echo $user->first_name." ".$user->last_name;?> </option>

                            	<?php 	}
                            	?>
                            </select>
                        </div>
                    </div>

                 <?php   }

                    ?>
                    

                     <div class="col-sm-3" style="display: none">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Your Company Location</label>
                            <span style="color:red;">*</span>
                            <select id="company_location" name="company_location" onchange="getProductsOfCompany(0)" class="mand form-control" required>
								<?php          $sql3 = $this->db->select('id, companyname')
								->from('store_rack_location')
								->get();

								if($sql3->num_rows() >  0) {
								foreach($sql3->result() as $row3) {?>   

								<option value="<?php echo $row3->id;?>" selected="selected"> <?php echo $row3->companyname;?> </option>    

								<?php } }?>
                            </select>
                        </div>
                    </div>
                </div>


                
                <hr>
                <div class="row">      
                    <div class="col-md-12">
                        <h5 class="page-title text-center">Customer Information</h5>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Company Name</label>
                            <span style="color:red;">*</span>
                            <input type="text" id="company_name" name="company_name" placeholder="Company Name"
                                autocomplete="nope" class="form-control mand" required>
                                  <img style="float:right;display:none;" id='loading' class="loading" width="100px" src="http://rpg.drivethrustuff.com/shared_images/ajax-loader.gif" /> 
                        </div>
                    </div>

                     <div class="col-sm-3" style="display:none;">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Company GSTN</label>
                            <span style="color:red;"></span>
                            <input type="text" id="company_gstn" name="company_gstn" placeholder="Company GSTN"
                                autocomplete="nope" class="form-control">
                        </div>
                    </div>  

                   <div class="col-sm-3">
                        <div class="form-group">
                            <label for="field-2" class="control-label">State</label>
                            <span style="color:red;"></span>
                            <select class="form-control" name="state" id="state" required>
                                <option value="">Select State</option>
                                <?php 
                                    $q = $this->db->select('state_id, state_name')->from('states')->where('country_id',101)->get();
                                    foreach($q->result() as $rowssss){
                                ?>
                                <option value="<?php echo $rowssss->state_name;?>"><?php echo $rowssss->state_name;?></option>
                            <?php }?>
                            </select>
                            
                        </div>
                    </div>

                      <div class="col-sm-3">
                        <div class="form-group">
                            <label for="field-2" class="control-label">City/Area</label>
                            <span style="color:red;"></span>
                            <input type="text" id="city" name="city" placeholder="City" autocomplete="nope" class="form-control">
                        </div>
                    </div>
                   
                     

                   
                   
                    <div class="col-sm-12 productss">
                        <div class="row">
                            <div class="col-md-3" style="display:none">
                                <div class="form-group">
                                    <label for="field-1" class="control-label">Competitor Product</label>
                                    <span style="color:red;">*</span>
                                    <select name="competitor_product[]" id="competitor_product0" class="form-control mand" onchange="getourproductname(0);" required>
                                        <option value="NA">NA</option>
                                    </select>
                                     <span></span>
                                    <div class="form-group comp_files0" style="margin-top:10px;display:none;">
                                        <label class="c_specfile_input0">Specs File</label>
                                        <input type="file" name="c_spec_file[]" id="c_spec_file0" class="form-control c_specfile_input0">
                                        <span id="c_spec0"></span>
                                    </div>
                                

                                 
                                    <div class="form-group comp_files0" style="display:none;">
                                        <label class="c_msdsfile_input0">MSDS File</label>
                                        <input type="file" name="c_msds_file[]" id="c_msds_file0" class="form-control c_msdsfile_input0">
                                         <span id="c_msds0"></span>
                                    </div>
                                
                                 
                                </div>


                            </div>

                            <div class="col-md-4" style="display:none">
                                <div class="form-group">
                                    <label for="field-1" class="control-label">Our Product</label>
                                    <span style="color:red;">*</span>
                                    <span id="error_product" style="color:red;"></span>
                                    <select class="product_name0" name="products[]" class="form-control" id="product_name0" onchange="getunit(0,this.value)">
                                    </select>
                                    <span id="recommendation0"></span>
                                </div>
 

                                <div class="form-group our_files0" style="margin-top:10px; display:none;">
                                        <label class="o_specfile_input0">Specs File</label>
                                        <input type="file" name="o_spec_file[]" id="o_spec_file0" class="form-control o_specfile_input0">
                                          <span id="o_spec0"></span>
                                </div>
                                

                                 
                                    <div class="form-group our_files0" style="display:none;">
                                        <label class="o_msdsfile_input0">MSDS File</label>
                                        <input type="file" name="o_msds_file[]" id="o_msds_file0" class="form-control o_msdsfile_input0">
                                         <span id="o_msds0"></span>
                                    </div>
                                

                            </div>

                            <div class="col-md-2" style="display:none">
                                <div class="form-group">
                                    <label for="field-1" class="control-label">Qty</label>
                                    <span style="color:red;">*</span>
                                    <input type="number" name="qty[]" id="qty0" class="form-control">
                                 </div>
                            </div>

                             <div class="col-md-2" style="display:none;">
                                <div class="form-group">
                                    <label for="field-1" class="control-label">Unit</label>
                                    <span style="color:red;">*</span>
                                    <select name="pack_size[]" id="pack_size0" class="form-control">
                                    </select>
                                 </div>
                            </div>

                            
                            <div class="col-md-1 " style="display:none;">
                                <a href="javascript:void(0)" class="btn btn-warning btn-xs add_more"
                                    style="margin-top: 32px;"><i class="fa fa-plus" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    </div>
                  
                    <div class="col-sm-12">
                        <hr>
                        <div class="row">
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="field-2" class="control-label">Title</label>
                                    <span style="color:red;">*</span>
                                    <span id="error_title" style="color:red;"></span>
                                    <select name="title" id="title" class="mand form-control">
                                        <option value="Mr.">Mr.</option>
                                        <option value="Mrs.">Mrs.</option>
                                        <option value="Miss.">Miss.</option>
                                    </select>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">

                                    <label for="field-2" class="control-label">Customer Name</label>

                                    <span style="color:red;">*</span>

                                    <span id="error_cust_name" style="color:red;"></span>

                                    <input type="text" id="cust_name" name="cust_name" placeholder="Customer Name"
                                        class="mand form-control" autocomplete="nope">

                                </div>
                            </div>

                             <div class="col-md-2">
                                <div class="form-group">
                                    <label for="field-2" class="control-label"> Designation</label>
                                    <span style="color:red;">*</span>
                                    <span id="error_title" style="color:red;"></span>
                                   <input type="text" class="form-control" name="designation" id="designation" value="">

                                </div>
                            </div>

                             <div class="col-md-1 col-1">
                                <div class="form-group">
                                    <label for="field-2" class="control-label" style="font-size:10px">Country Code</label>
                                    <input type="text" class="form-control" name="country_code" value="91" readonly>
                                </div>
                            </div>
                            <div class="col-md-2 col-2">
                                <div class="form-group">
                                    <label for="field-2" class="control-label">Contact Number</label>
                                    <span style="color:red;" id="customerexist">* </span>
                                    <span id="error_mobile_no" style="color:red;"></span>
                                    <input type="number" id="mobile_no" name="mobile_no" class="form-control mob mand" value=""
                                        placeholder="Contact No" autocomplete="nope" onblur="validatecustomer();">
                                </div>
                            </div>

                            <script type="text/javascript">
                                function validatecustomer(){
                                    var mobile_no = $("#mobile_no").val();
                                        $.ajax({
                                        type:"post",
                                        url:"<?php echo page_url;?>Leads/checkcustomerexistance",
                                        data:"mobile_no="+mobile_no,
                                        success:function(data){
                                        $("#customerexist").html(data);
                                        if(data){
                                            $("#mobile_no").val('');
                                        }
                                        }
                                        });

                                }
                            </script>
                            <input class="form-control" name="message1" id="message1" style="overflow:hidden"
                                type="hidden">

                         <div class="col-sm-3">
                        <div class="form-group">

                            <label for="field-2" class="control-label">Email ID</label>

                            <span id="error_email_id" style="color:red;"></span>

                            <input type="text" class="form-control" id="email_id" name="email_id" placeholder="Email ID" 
                                autocomplete="nope">

                        </div>
                    </div>
                        </div>
                    </div>
                   
                  
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="field-2" class="control-label">Postal Address</label>
                            <span style="color:red;">*</span>
                            <textarea name="postal_address" id="postal_address" class="form-control mand" placeholder="Postal Address"
                                autocomplete="nope" style="height:186px;"></textarea>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">

                            <label for="field-2" class="control-label">Alternate Contact Person</label>

                            <input type="text" class="form-control" name="alt_contact" id="alt_contact" placeholder="Alternate Contact"
                                autocomplete="nope">

                        </div>
                    </div>
                      <div class="col-md-3 col-3">

                                <div class="form-group">

                                    <label for="field-2" class="control-label">Country
                                        Code</label>

                                    <input type="text" class="form-control country_code" name="country_code" id="country_code"
                                        value="91" readonly>

                                </div>

                            </div>

                            <div class="col-md-3 col-3">
                                <div class="form-group">
                                    <label for="field-2" class="control-label">Contact Number</label>
                                    <input type="text" class="form-control" name="alt_contact_no" placeholder="Contact Number"
                                        autocomplete="nope">
                                </div>
                            </div>
                            <input name="message1" id="message1" style="overflow:hidden" type="hidden">

                    <div class="col-sm-3">
                        <div class="form-group">

                            <label for="field-2" class="control-label">E-mail</label>

                            <input type="email" class="form-control" name="email" id="email" placeholder="E-mail" autocomplete="nope">

                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="row">

                          
                            
                        </div>
                    </div>
                  
                    <div class="col-sm-12">
                        <div class="form-group">

                            <label for="field-2" class="control-label">Remarks (If Any..) </label>


                            <textarea name="spacification" class="form-control" id="spacification" placeholder="Enter Remarks"
                                autocomplete="nope" style="height:187px;"></textarea>



                        </div>
                    </div>


                    <div class="col-md-3" style="display:none;">
                          <div class="form-group">
                            <label for="field-1" class="control-label">Attachment</label>
                                <input type="file" name="attach[]" id="attach" class="form-control" multiple>
                        </div>
                    </div>

                    <div class="col-sm-12" style="display: none;">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Record Audio</label>
                    
                            <div class="col-sm-3 audio-record">
                                <button id="recordButton" style="background-color:green  !important;color:white !important;">Start Recording</button>
                                <button id="stopButton" class="inactive" style="background-color:red  !important; color:white !important;">Stop</button>
                            </div>
                            <div class="col-sm-3 playback">
                                <audio src="" controls id="audio-playback" class="hidden"></audio>
                            </div>
                            <input type="hidden" name="audiofile" id="audiofile">
                            <div class="col-sm-3 download" style="display:none;">
                                <button class="hidden" id="downloadContainer">
                                    <a href="" download="" id="downloadButton">Download Audio</a>
                                </button>
                            </div>

                        </div>
                    </div>


                        <div class="col-sm-12"  style="display: none;" id="recorded_audio">
                        <div class="col-sm-12">
                        <div class="form-group">
                        <label for="field-1" class="control-label">Send Audio To</label>
                        <span id="error_lead_source" style="color:red;"></span>
                        <select class="form-control" id="send_audio" name="send_audio">
                        <option value="">--Select User--</option>
                        <?php $query = $this->db->select('user_id, first_name, last_name')
                        ->from('system_users')
                        ->where('user_status','1')
                        ->get();

                        foreach($query->result() as $lead_source) { ?>
                        <option value="<?php echo $lead_source->user_id;?>"><?php echo $lead_source->first_name;?><?php echo $lead_source->last_name;?> </option>   
                        <?php }?>   
                        </select>
                        </div>
                        </div>
                        </div>


                      <div class="col-sm-12 ">
                      	<div class="form-group text-center"><input type="submit" id="saves_form" class="btn btn-success" value="Submit"></div>
                        
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
         <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
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

      

        <!-- App js -->

        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

 

		<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

        <script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo assets_url;?>plugins/audio/manage-audio.js"></script>
<script type="text/javascript">

 $(document).ready(function() { 
 var dateToday = new Date();   
     jQuery('#create_date').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: 'dd-mm-yyyy',
        startDate: '-7d',
     });

 });


    function enable_stop() {
        $("#record").attr('disabled', true);
        $("#stopRecord").attr('disabled', false);

        navigator.getUserMedia({audio: true}, function(stream) { /* do stuff */ });
    }

    function getProductsOfCompany(j) {
        var company_location = $("#company_location").val();

                 $.ajax({
                    type:"post",
                    url:"<?php echo page_url;?>Open_leads/getCompanyProducts",
                    data:{company_location:company_location},

                    success:function(data) {
                        $(".product_name"+j).html(data);
                    }
                });
    }

    function check_business() {
        var patient_type = $("#patient_type").val();
        $("#distributorData").css('display', 'none');
        $("#distributor").removeClass('mand');

        if(patient_type ==1) {
                $("#distributorData").css('display', '');
                $("#distributor").addClass('mand');
        }
    }

    function getReferralName() {
        $(".referral_name").css('display', 'none');
        $("#referral_name").removeClass('mand');
       if($("#lead_source").val() == 3) {
            $(".referral_name").css('display', '');
            $("#referral_name").addClass('mand');
       }
    }

        function validate_lead() {

         $("#saves_form").attr('disabled',false);

         $("#saves_form").val('Submit');

         $("#loginForm :input").attr('required',false);



            var isValid=0;



                $("#loginForm .mand").each(function() {

                var element = $(this).val();

                if (element=="") {

                    isValid=1;

                }

            });



        if(isValid==0) {

            $("#saves_form").attr('disabled',true);

            $("#saves_form").val('Please Wait..');

                 return true;

        } else {



             $("#saves_form").attr('disabled',false);

             $("#saves_form").val('Submit');

             alert('All Fields marked with * are mandatory');

            return false;

        }



    }

    function validatedigit()
      {
          //alert('hi');
         var mob=$(".mob").val();
        var len=mob.length;
        if(len<11 && len>9)
        {
        return true;
        }else
        {
        alert('Mobile No should be 10 Digits.');
        var mob=$(".mob").val('');
        }
      }
</script>

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-36251023-1']);
  _gaq.push(['_setDomainName', 'jqueryscript.net']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>


<script type="text/javascript">
                var j=1;

            $('.add_more').click(function(){
                getProductsOfCompany(j);
            $('.productss').append('<div class="row fieldGroups"> <div class="col-md-3" style="display:none"> <div class="form-group"> <label for="field-1" class="control-label">Competitor Product</label> <span style="color:red;">*</span><select name="competitor_product[]" id="competitor_product'+j+'" class="form-control mand" onchange="getourproductname('+j+');" required><option value="NA">NA</option></select></div><div class="form-group comp_files'+j+'" style="margin-top:10px;display:none;"><label class="c_specfile_input'+j+'">Specs File</label><input type="file" name="c_spec_file[]" id="c_spec_file'+j+'" class="form-control c_specfile_input'+j+'"><span id="c_spec'+j+'"></span></div><div class="form-group comp_files'+j+'" style="display:none;"><label class="c_msdsfile_input'+j+'">MSDS File</label><input type="file" name="c_msds_file[]" id="c_msds_file'+j+'" class="form-control c_msdsfile_input'+j+'"><span id="c_msds'+j+'"></span></div></div><div class="col-md-4"><div class="form-group"> <label for="field-1" class="control-label">Our Product</label><span style="color:red;">*</span> <span id="error_product" style="color:red;"></span><select class="product_name'+j+' mand" id="product_name'+j+'"  name="products[]" required onchange="getunit('+j+',this.value)"> </select><span id="recommendation'+j+'"></span></div><div class="form-group our_files'+j+'" style="margin-top:10px;display:none;"><label class="o_specfile_input'+j+'">Specs File</label><input type="file" name="o_spec_file[]" id="o_spec_file'+j+'" class="form-control o_specfile_input'+j+'"><span id="o_spec'+j+'"></span></div><div class="form-group our_files'+j+'" style="display:none;"><label class="o_msdsfile_input'+j+'">MSDS File</label><input type="file" name="o_msds_file[]" id="o_msds_file'+j+'" class="form-control o_msdsfile_input'+j+'"><span id="o_msds'+j+'"></span></div></div><div class="col-md-2"><div class="form-group"><label for="field-1" class="control-label">Qty</label><span style="color:red;">*</span><input type="number" name="qty[]" id="qty'+j+'" class="form-control mand" min="1" required></div></div><div class="col-md-2"><div class="form-group"><label for="field-1" class="control-label">Unit</label><span style="color:red;">*</span><select name="pack_size[]" id="pack_size'+j+'" class="form-control mand" required></select></div></div><div class="col-md-1"> <a href="javascript:void(0)" class="btn btn-danger btn-xs remove" style="margin-top: 32px;"><i class="fa fa-remove" aria-hidden="true"></i></a> </div></div>');
            initializeSelect2("competitor_product"+j);
            initializeSelect2_product("product_name"+j);
            j++;
            });

            $(document).on('click', '.remove', function(){
                $(this).parents(".fieldGroups").remove();
            });

</script>
<script type="text/javascript">
    
$(document).ready(function(){
     getProductsOfCompany(0);
var purl="<?php echo page_url;?>Open_leads/getrecommendations";
$('#competitor_product0').select2({ 
        placeholder: 'TYPE TO SELECT',
        minmumInputLength:4,
        allowClear: true,
        tags:true,
        ajax: {
        url: purl,
        dataType: 'json',
        delay: 250,
        data: function (params) {

        return {
        searchTerm: params.term
        };

        },
        processResults: function (data) {
        return {
        results: data
        };
},
cache: true

        }
});


$('#product_name0').select2({ });
});



 function initializeSelect2(selectElementObj) {
   
    var purl="<?php echo page_url;?>Open_leads/getrecommendations";

            $('#'+selectElementObj).select2({ 
            placeholder: 'TYPE TO SELECT',
            minmumInputLength:4,
            allowClear: true,
            tags:true,
            ajax: {
            url: purl,
            dataType: 'json',
            delay: 250,
            data: function (params) {
            return {
            searchTerm: params.term
            };
            },
            processResults: function (data) {
            return {
            results: data
            };
            },
            cache: true

            }
            });
         
      }


function initializeSelect2_product(selectElementObj) {

    $('#'+selectElementObj).select2({ });

}
      function getourproductname(id)
      {
         $(".comp_files"+id).css('display','none');
         $(".our_files"+id).css('display','none');
        $(".c_specfile_input"+id).css('display','');
        $("#c_spec"+id).html('');
        $(".c_msdsfile_input"+id).css('display','');
          $(".comp_files"+id).css('display','');
        $("#c_msds"+id).html('');
        var comproduct=$("#competitor_product"+id).val();

        if(comproduct!='')
        {

            $(".comp_files"+id).css('display','');

            $.ajax({
            type:"post",
            url:"<?php echo page_url;?>Open_leads/getrecommendations_our_product",
            data:"comproduct="+comproduct,
            success:function(data){
                 $("#recommendation"+id).css('font-size','12px');
            $("#recommendation"+id).css('color','red');
            $("#recommendation"+id).css('font-weight','bold');
            $("#recommendation"+id).html(data);
            }
            });


            $.ajax({
            type:"post",
            url:"<?php echo page_url;?>Open_leads/get_competitor_files",
            data:"comproduct="+comproduct,
            success:function(data){

                var a=data.split('|');

                if(a[0]!='')
                {
                    $(".c_specfile_input"+id).css('display','none');
                    $("#c_spec"+id).html(a[0]);
                }

                if(a[1]!='')
                {
                $(".c_msdsfile_input"+id).css('display','none');
                $("#c_msds"+id).html(a[1]);
                }
               
            }
            });


        }

      }

      function add_data_from_visit()
      {
        var v=$("#visit").val();
        if(v!='')
        {
            $.ajax({
            type:"post",
            url:"<?php echo page_url;?>Open_leads/get_visit_details",
            data:"id="+v,
            success:function(data){
            var d=data.split('|');

            $("#title").val(d[0]);
            $("#cust_name").val(d[1]);
            $("#email_id").val(d[2]);
            $("#mobile_no").val(d[4]);
            $("#city").val(d[5]);
            $("#company_name").val(d[6]);
            $("#postal_address").val(d[7]);
            $("#alt_contact").val(d[8]);
            $("#alt_contact_no").val(d[9]);
            $("#email_id").val(d[10]);
            $("#patient_type").val(d[11]);
            // if(d[12]!='')
            // {
            // $("#company_gstn").val(d[12]);
            // }else
            // {
            // $("#company_gstn").val(d[12]);
            // $("#company_gstn").attr('readonly',false);
            // }
           
            }
            });

        }
      }

        function getunit(i, product) {
            $(".our_files"+i).css('display','none');

            $(".o_specfile_input"+i).css('display','');
            $("#o_spec"+i).html('');
            $(".o_msdsfile_input"+i).css('display','');

            $(".o_specfile_input"+i).css('display','');
            $("#o_spec"+i).html('');
            $(".o_msdsfile_input"+i).css('display','');
            $("#o_msds"+i).html('');


         
        $("#c_msds"+i).html('');
            
                $(".our_files"+i).css('display','');
                $.ajax({
                    type: "post",
                    url: "<?php echo page_url; ?>Open_leads/getunit",
                    data: "proid=" + product,
                    success: function(data) {
                        // alert("#pack_size"+i);
                        // var arr = data.split('|');
                        $("#pack_size"+i).html(data);
                        // $("#pack_size"+i).val(arr[1]);


                    }
                });

                 $.ajax({
                    type: "post",
                    url: "<?php echo page_url; ?>Open_leads/get_our_product_files",
                    data: "proid=" + product,
                    success: function(data) {
                   
                    var a=data.split("~");
            
                    if(a[0]!='')
                    {
                    $(".o_specfile_input"+i).css('display','none');
                    $("#o_spec"+i).html(a[0]);
                    }

                    if(a[1]!='')
                    {
                    $(".o_msdsfile_input"+i).css('display','none');
                    $("#o_msds"+i).html(a[1]);
                    }


                    }
                });
            
        }


//          function check_gst(gst){

//             if(gst!='')
//             {
//     if(gst.length != 15){
//         alert("Invalid Length of GSTIN");
//         $("#company_gstn").val('');
//     }else{
//         var state = parseInt(gst.substring(0, 2)); 
//         // FIRST 2 CHARACTERS STATE CODE
//         if(state < 1 || state > 37){
//             alert("Invalid First Two Characters of GSTIN");
//             $("#company_gstn").val('');
//         }
//         // NEXT 10 CHARACTERS PAN NO. VALIDATION
//         var pan = gst.substring(2, 12).toUpperCase();
//         var regex = /[a-zA-Z]{3}[PCHFATBLJG]{1}[a-zA-Z]{1}[0-9]{4}[a-zA-Z]{1}$/;
//         if( !regex.test(pan) ){
//             alert("Invalid GSTIN");
//             $("#company_gstn").val('');
//         }
//         // DEFAULT 14TH CHARACTER 'Z'
//         var char14 = gst[13].toUpperCase();
//         if(char14 != "Z"){
//             alert("14th character of GSTIN should be 'Z'");
//             $("#company_gstn").val('');
//         }
//         // CHECKSUM DIGIT 
//         if(check_gst_checksum(gst.substring(0, 14)) != gst[14]){
//             alert("Invalid GSTIN");
//             $("#company_gstn").val('');
//         }

//         }
//         //return true;

//     }
// }


function check_gstnew(gst)
{
    var a=0;
     var statecode = gst.substring(0, 2);
            var pancarno = gst.substring(2, 12);
            var entityNumber = gst.substring(12, 13);
            var defaultZvalue =gst.substring(13, 14);
            var checksumdigit = gst.substring(14, 15);
            if (gst.length != 15) {
                alert('GST Number is invalid');
            
                var a=1;
                           }
            if (pancarno.length != 10) {
                alert('GST number is invalid ');
              
                var a=1;
                
            }
            if (defaultZvalue !== 'Z') {
                alert('GST Number is invalid Z not in Entered Gst Number');
                
                var a=1;
            }

            if ($.isNumeric(statecode)) {
                
            } else {
                alert('Please Enter Valid State Code');
               
                var a=1;
            }

            // if ($.isNumeric(checksumdigit)) {
               
            // } else {
            //     alert('GST number is invalid last character must be digit');
              
            //     var a=1;

            // }


           
            // if(a==0)
            // {
            //     check_gst(gst);
            // }else
            // {
            //     $("#company_gstn").val('');
            // }

}
 function check_gst(gst){

    $("#loading").css('display','none');
    if(gst!='')
    {

    $.ajax({
                    type:"post",
                    url:"<?php echo page_url;?>Open_leads/getGSTData",
                    data:{gst:gst},
                    beforeSend: function() {
                    $("#loading").css('display','');
                
                    },
                    success:function(data) {

                        if(data!='NA')
                        {
                            $("#company_name").val(data);
                        }else
                        {
                            alert('Invalid GST No. Provided');
                            $("#company_name").val('');
                        }
                          $("#loading").css('display','none');
                      
                    }
                });
    }

}
</script>
<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
    </body>
</html>