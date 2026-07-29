<?php
$CI = &get_instance();
$CI->load->model('Master_model', 'master');
$getFinGoodsType = $CI->master->getFinGoodsType();
$getRackLocation = $CI->master->getRackLocation();
$getunit = $CI->master->getunit();
?>
<?php 
//echo "<pre>"; print_r($_SESSION['logged_in']); exit;
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
        <title><?php echo sitetitle;?> Pending for Approval</title>

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
	                   
	                    <h4 class="page-title text-center">Pending for Approval</h4>
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
                    $uri = $this->uri->segment(3);
                  $check = is_numeric($uri);
                  if($check){

                   $qq = $this->db->select('customer_name, id, company_name, contact_person,email, contact_no, postal_address')->from('leads')->where('id',$this->uri->segment(3))->get();
                        foreach($qq->result() as $rowss);

                    
                ?>
				<form id="loginForm" method="post" action="<?php echo page_url;?>Leads/customize_requirement_approval_rejection/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>" enctype="multipart/form-data" onsubmit="return validate_leads();">
                <div class="row">

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Company Name</label>
                            <span style="color:red;">*</span>
                            <input type="text" id="company_name" name="company_name" value="<?php echo $rowss->company_name;?>" placeholder="Company Name"
                                autocomplete="nope" class="form-control mand" readonly required>
                                  
                        </div>
                    </div>

                      <div class="col-sm-3">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Customer Name</label>
                            <span style="color:red;">*</span>
                            <input type="text" id="customer_name" name="customer_name" value="<?php echo $rowss->customer_name;?>" placeholder="Customer Name"
                                autocomplete="nope" class="form-control mand" readonly required>
                                 
                        </div>
                    </div>

                <div class="col-sm-3">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Contact No</label>
                            <span style="color:red;">*</span>
                            <input type="text" id="company_name" name="company_name" value="<?php echo $rowss->company_name;?>" placeholder="Company Name"
                                autocomplete="nope" class="form-control mand" readonly required>
                                
                        </div>
                    </div>

                <div class="col-sm-3">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Email ID</label>
                            <input type="text" id="email_id" name="email_id" value="<?php echo $rowss->email;?>" placeholder="Email Id"
                                autocomplete="nope" class="form-control mand" readonly required>
                                  
                        </div>
                    </div>

                <div class="col-sm-12">
                        <div class="form-group">
                            <label for="field-2" class="control-label">Address</label>
                            <span style="color:red;">*</span>
                            <textarea name="postal_address" id="postal_address" class="form-control" readonly  placeholder="Address"
                                autocomplete="nope"><?php echo $rowss->postal_address;?></textarea>
                        </div>
                    </div>

                    <?php 
                    $q = $this->db->select('remarks')->from('progress_remarks')->where('lead_id',$this->uri->segment(3))->where('lead_status',$this->uri->segment(4))->get();
                    if($q->num_rows()>0){
                        foreach($q->result() as $row){
                           $customrequirement = $row->remarks; 
                        }
                    }else{
                         $customrequirement = "";
                    }
                    ?>
                    <div class="col-md-12" id="showcustomizedsection">
                        <div class="form-group">
                            <label>Customization Brief <span style="color:red;">*</span></label>
                            <textarea class="form-control" name="customizationbrief" id="customizationbrief" style="height:186px;"><?php echo  $customrequirement;?></textarea>
                        </div>  
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Approval Status</label>
                            <select class="form-control" name="approval" id="approval" onchange="defineproductinfo();" required>
                                <option value="">Select Option</option>
                                <option value="1">Approve</option>
                                <option value="2">Reject</option>
                            </select>
                        </div>
                    </div>
                    <script type="text/javascript">
                       function defineproductinfo(){
                        var approval = $("#approval").val();
                        if(approval==1){
                            $("#showproductname").show();
                            $("#rejectionremarkdiv").hide();
                            $("#product_name").attr('Required',true);
                            $("#product_code").attr('Required',true);
                            $("#hsn").attr('Required',true);
                            $("#unit").attr('Required',true);
                            $("#pack_size").attr('Required',true);
                            $("#price").attr('Required',true);
                            $("#disprice").attr('Required',true);
                             $("#rejectionremark").attr('Required',false);
                        }else{
                            $("#showproductname").hide();
                            $("#rejectionremarkdiv").show();
                            $("#product_name").attr('Required',false);
                            $("#product_code").attr('Required',false);
                            $("#hsn").attr('Required',false);
                            $("#unit").attr('Required',false);
                            $("#pack_size").attr('Required',false);
                            $("#price").attr('Required',false);
                            $("#disprice").attr('Required',false);
                            $("#rejectionremark").attr('Required',true);
                        }
                       }

                    </script>

                    <div id="showproductname" style="display:none">

                        <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">PRODUCT NAME</label>
                                            <span id="error_instrument_name" style="color:red;">*</span>
                                            <input type="text" class="form-control" id="product_name" style="text-transform: uppercase;" name="product_name">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">CODE</label>
                                            <span id="error_instrument_model" style="color:red;">*</span>
                                            <input type="text" class="form-control" id="product_code" style="text-transform: uppercase;" name="product_code" required>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>HSN</label>
                                            <span id="error_mvalue" style="color:red;">*</span>
                                            <input class="form-control" type="number" name="hsn" id="hsn" required step="any">
                                        </div>
                                    </div>


                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="field-2" class="control-label">UNIT</label>
                                            <span id="error_mvalue" style="color:red;">*</span>
                                            <select class="form-control" name="unit">
                                                <option value="">SELECT</option>
                                                <?php if ($getunit != '') {
                                                    foreach ($getunit as $row1) { ?>
                                                        <option value="<?php echo $row1->shortname; ?>"><?php echo $row1->shortname; ?></option>
                                                <?php }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-2" style="display:none;">
                                    <div class="form-group">
                                    <label for="field-1" class="control-label">PRODUCT VOLUME</label>
                                    <span id="" style="color:red;">*</span>
                                    <input type="text" class="form-control" id="volume" style="text-transform: uppercase;" min="1"  name="volume" onkeyup="allow_decimal('prd_density');">
                                    </div>
                                    </div>



                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="field-2" class="control-label">PACK SIZE</label>
                                            <span id="error_mvalue" style="color:red;">*</span>
                                            <select class="form-control select4" name="pack_size" id="pack_size" required="" onchange="add_density();">
                                                <option value="">SELECT</option>
                                                <option value="BUCKET">BUCKET</option>
                                                <option value="DRUM">DRUM</option>
                                                <option value="BULK">BULK</option>
                                                
                                            </select>
                                        </div>
                                    </div>
                                    <script type="text/javascript">
                                        function add_density()
                                        {
                                            $("#density").css('display','none');
                                            $("#prd_density").attr('required',false);
                                            var pack_size=$("#pack_size").val();
                                            if(pack_size=="BULK")
                                            {
                                                $("#density").css('display','');
                                                $("#prd_density").attr('required',true);
                                            }
                                        }
                                    </script>


                                    <div class="col-md-2" id="density" style="display: none;">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">PRODUCT DENSITY</label>
                                            <span id="" style="color:red;">*</span>
                                            <input type="text" class="form-control" id="prd_density" style="text-transform: uppercase;" name="prd_density" onkeyup="allow_decimal('prd_density');">
                                        </div>
                                    </div>


                                   
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Price</label>
                                            <span id="error_mvalue" style="color:red;">*</span>
                                            <input class="form-control" type="number" name="price" id="price" required step="any">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Allowed Price</label>
                                            <span id="error_mvalue" style="color:red;">*</span>
                                            <input class="form-control" type="number" name="disprice" id="disprice" required onblur="checkamt(this.value);" step="any">
                                        </div>
                                    </div>


                                    <div class="col-md-2" style="display:none">
                                        <div class="form-group">
                                            <label>COMPANY LOCATION</label>
                                            <span id="error_mvalue" style="color:red;">*</span>
                                            <!-- <select class="select3" name="rack_location" required="" > -->
                                            <select class="select3" name="rack_location[]" multiple="" required="">
                                                
                                            <?php if ($getRackLocation != '') {
                                                    foreach ($getRackLocation as $row1) { ?>
                                                        <option value="<?php echo $row1->id; ?>" selected><?php echo $row1->companyname; ?></option>
                                                <?php }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-2" style="display:none">
                                        <div class="form-group">
                                            <label>TRIAL READING REQUIRED?</label>
                                            <span id="error_mvalue" style="color:red;"></span><br/>
                                            <input type="checkbox" name="trial" id="trial" value="1">
                                            
                                        </div>
                                    </div>

                                    <div class="col-md-2" style="display:none">
                                    <div class="form-group">
                                    <label>PRODUCT SPEC FILE</label>
                                    <span id="error_mvalue" style="color:red;">*</span> 
                                    <input type="file" name="specfile" id="specfile" class="form-control">
                                    </div>
                                    </div>

                                    <div class="col-md-2" style="display:none">
                                    <div class="form-group">
                                    <label>PRODUCT MSDS</label>
                                    <span id="error_mvalue" style="color:red;">*</span> 
                                    <input type="file" name="msds" id="msds" class="form-control">
                                    </div>
                                    </div>
                        
                    </div>

                    <div  class="col-sm-9" id="rejectionremarkdiv" style="display:none">
                       
                            <div class="form-group">
                            <label>Rejection Remarks</label>
                            <textarea class="form-control" name="rejectionremark" id="rejectionremark" style="height:186px;"></textarea>
                        </div>
                       
                    </div>

                    
                  

                      <div class="col-sm-12 ">
                      	<div class="form-group pull-right"><input type="submit" id="saves_form" class="btn btn-info" value="Submit"></div>
                        
                    </div>
                </div>
            </form>

            <?php } ?>                       

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
<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
    </body>
</html>