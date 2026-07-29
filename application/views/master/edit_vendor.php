<?php
$CI = &get_instance();
$CI->load->model('Master_model', 'master');
$getRackLocation = $CI->master->getRackLocation();

?>

<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="description" content="">
      <meta name="author" content="<?php echo copyright; ?>">
      <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
      <title><?php echo sitetitle; ?> Edit Vendor</title>
      <!-- Table Responsive css -->		<script src="<?php echo assets_url;?>js/angular.min.js"></script>		 <!-- DataTables -->        
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
      <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->        <!--[if lt IE 9]>        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>        <![endif]-->        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>    
   </head>
   <body>
      <!-- Navigation Bar-->        
      <header id="topnav">          <?php $this->load->view('common/nav-menu');?>        </header>
      <!-- End Navigation Bar-->        
      <div class="wrapper">
         <div class="container">
            <!-- Page-Title -->                
            <div class="row" style="margin-top:20px;">
               <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                  <div class="page-title-box">
                     <div class="btn-group pull-right">						                                                             </div>
                     <h4 class="page-title">Edit Vendor Detail</h4>
                  </div>
               </div>
            </div>
            <!-- end page title end breadcrumb --><span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>               
            <div class="row">
               <div class="col-xs-12">
                  <div class="card-box">
                     <div class="row">
                        <div class="col-sm-12 col-xs-12 col-md-12">
                           <?php								$id = $this->uri->segment(4);								$this->db->select('*')->from('vendors')->where('id',$id);								$query = $this->db->get();								$res = $query->result();								foreach($res as $vendor)								?>								                                   
                           <form id="loginForm" method="post" action="<?php echo page_url;?>Master/User_management/update_vendor/<?php echo $vendor->id;?>"  enctype="multipart/form-data">

                        <div class="col-md-3">

                          <div class="form-group">

                             <label for="field-2" class="control-label">Company</label>

                             <span id="error_vendor_name" style="color:red;">*</span>
                             <select class="form-control" name="company" id="company"  required>
                               <option value="">Select Company</option>
                                <?php if($getRackLocation != '') {
                                        foreach($getRackLocation as $row1) {?>
                                    <option value="<?php echo $row1->id;?>" <?php if($row1->id == $vendor->company){ echo 'selected';} ?> ><?php echo $row1->companyname;?></option>
                                    <?php } } ?>
                             </select>
                             

                          </div>

                        </div>
                              <div class="col-md-3">
                                 <div class="form-group">														 
                                    <label for="field-2" class="control-label">Vendor Name</label>														 <span id="error_vendor_name" style="color:red;"></span>														 <input type="text" class="form-control" name="vendor_name" id="vendor_name"  value="<?php echo $vendor->name;?>" required>													</div>
                              </div>

                              <?php
                              if($vendor->code==0){
                               $q = $this->db->select('code')->from('vendors')->Where('status',1)->order_by('code','desc')->limit(1)->get();
                                       if($q->num_rows()>0){
                                          foreach($q->result() as $ros);
                                          $currentcode = $ros->code;
                                          $add = 1;
                                          $nextcode = $currentcode+$add;
                                       }else{
                                          $nextcode = "";
                                       }
                              }else{
                                 $nextcode = $vendor->code;
                              }?>

                                 <div class="col-md-3">
                                       <div class="form-group">
                                           <label for="field-2" class="control-label">Vendor Code</label>
                                           <span id="error_vendor_name" style="color:red;">*</span>
                                           <input type="text" class="form-control" name="vendor_code" id="vendor_code"  readonly autocomplete="off" required value="<?php echo $nextcode;?>">
                                       </div>

                                 </div>

                              <div class="col-md-2" style="display:none">
                              <div class="form-group">
                              <label for="field-2" class="control-label">HPCL VENDOR?</label>
                              <span id="error_vendor_name" style="color:red;">*</span><br/>
                              <input type="checkbox" name="hpcl_vendor" id="hpcl_vendor"  value="1" <?php if($vendor->hpcl==1){?> checked <?php } ?> onchange="hpcl_vendor_data();">
                              </div>
                              </div>
                              <script>
                                 function hpcl_vendor_data()
                                 {
                                     $("#hpcl_vendor_map").css('display','none')
                                     $("#location").attr('required',false);

                                    if($('#hpcl_vendor').is(":checked"))
                                    {
                                       $("#hpcl_vendor_map").css('display','')
                                       $("#location").attr('required',true);
                                    }
                                 }
                              </script>
                           

                              <div class="col-md-2" id="hpcl_vendor_map" <?php if($vendor->hpcl==1){?> <?php }else{ ?>style="display: none;" <?php } ?>>
                               <div class="form-group">
                                  <label for="field-2" class="control-label">Map Location</label>
                                  <select class="form-control" name="location" id="location">
                                    <option value="">Select</option>
                                     <?php
                                     $rtyu=$this->db->select('id,name')->from('hpcl_location')->get();
                                     if($rtyu->num_rows()>0)
                                     {
                                       foreach($rtyu->result() as $row)
                                       {
                                     ?>
                                     <option value="<?php echo $row->id;?>" <?php if($row->id==$vendor->hpcl_location){?> selected <?php } ?>><?php echo $row->name;?></option>
                                     <?php 
                                       }
                                       }
                                     ?>
                                  </select>
                               </div>
                              </div>


                             

                              <div class="col-md-3">
                                 <div class="form-group">
                                 <label for="field-2" class="control-label">Contact Person</label>
                                 <span id="error_vendor_name" style="color:red;"></span>									 
                                 <input type="text" class="form-control" name="contactperson" id="contactperson" value="<?php echo $vendor->contactperson;?>" required>													
                              </div>
                              </div>

                              <div class="col-md-3">
                                 <div class="form-group">														 
                                    <label for="field-2" class="control-label">Contact</label>														 <span id="error_vendor_name" style="color:red;"></span>														 <input type="text" class="form-control" name="contact" id="contact"  value="<?php echo $vendor->phone;?>" required>													
                                 </div>
                              </div>

                              <div class="col-md-3">
                                 <div class="form-group">														 
                                    <label for="field-2" class="control-label">Email ID</label>														 <span id="error_vendor_name" style="color:red;"></span>														 <input type="text" class="form-control" name="email" id="email"  value="<?php echo $vendor->email;?>" required>													
                                 </div>
                              </div>

                              <div class="col-md-3">
                                 <div class="form-group">														 
                                    <label for="field-2" class="control-label">GST No.</label>														 <span id="error_vendor_name" style="color:red;"></span>														 <input type="text" class="form-control" name="gst" id="gst"  value="<?php echo $vendor->gst;?>" required>													
                                 </div>
                              </div>

                              <div class="col-md-3">
                                 <div class="form-group">														 
                                    <label for="field-2" class="control-label">PAN No.</label>														 <span id="error_vendor_name" style="color:red;"></span>														 <input type="text" class="form-control" name="pan" id="pan" value="<?php echo $vendor->pan;?>"  required>													
                                 </div>
                              </div>

                              <div class="col-md-12">
                                 <div class="form-group">														 
                                    <label for="field-2" class="control-label">Address</label>														 <span id="error_vendor_name" style="color:red;"></span>														 <textarea class="form-control" name="address" id="address" required style="resize:none;"><?php echo $vendor->address;?></textarea>													
                                 </div>
                              </div>

                           

                           
                            <!--  <div class="col-md-4">
                                 <div class="form-group">														 <label for="field-2" class="control-label">Payment Terms</label>														 <span id="error_vendor_name" style="color:red;"></span>													 <input type="text" class="form-control" name="payment" id="payment"  value="<?php echo $vendor->payment_terms;?>" required>													</div>
                              </div>
                               <div class="col-md-4">
                                 <div class="form-group">
                                    <label for="field-2" class="control-label">Payment Mode</label>														 <span id="error_vendor_name" style="color:red;"></span>    
                                    <select class="form-control" name="pmode" id="pmode"  value="" required>
                                       <option value="">Select Mode</option>
                                       <option value="Cheque" <?php if($vendor->payment_mode=='Cheque'){?> selected <?php } ?>>Cheque</option>
                                       <option value="NEFT" <?php if($vendor->payment_mode=='NEFT'){?> selected <?php } ?>>NEFT</option>
                                    </select>
                                 </div>
                              </div>
                              <div class="col-md-4">
                                 <div class="form-group">														 <label for="field-2" class="control-label">Credit Days</label>														 <span id="error_vendor_name" style="color:red;"></span>														 <input type="text" class="form-control" name="credit" id="credit"  value="<?php echo $vendor->credit_days;?>" required>													</div>
                              </div>
                              <div class="col-md-4">
                                 <div class="form-group">
                                    <label for="field-2" class="control-label">Delivery By</label>														 <span id="error_vendor_name" style="color:red;"></span>														
                                    <select  class="form-control" name="dby" id="dby" required>
                                       <option value="">Delivery By</option>
                                       <option value="By Presto" <?php if($vendor->deliveryby=='By Presto'){?> selected <?php } ?>>By Presto</option>
                                       <option value="By Vendor" <?php  if($vendor->deliveryby=='By Vendor'){?> selected <?php } ?>>By Vendor</option>
                                    </select>
                                 </div>
                              </div>
                              <div class="col-md-4">
                                 <div class="form-group">
                                    <label for="field-2" class="control-label">Delivery Time</label>														 <span id="error_vendor_name" style="color:red;"></span>														
                                    <select  class="form-control" name="del_type" id="del_type" required onchange="deltypecheck(this.value);">
                                       <option value="">Delivery Time</option>
                                       <option value="2" <?php if($vendor->deliverytime=='2'){?> selected <?php } ?>>Ready</option>
                                       <option value="7" <?php if($vendor->deliverytime=='7'){?> selected <?php } ?>>7 days</option>
                                       <option value="15" <?php if($vendor->deliverytime=='15'){?> selected <?php } ?>>15 days</option>
                                       <option value="30" <?php if($vendor->deliverytime=='30 days'){?> selected <?php } ?>>30 days</option>
                                       <option value="Other" <?php //if($vendor->deliverytime=='Other'){?> selected <?php //} ?>>Other</option>
                                    </select>
                                 </div>
                              </div>
                              <script>																																				$( document ).ready(function() {												deltypecheck($("#del_type").val());												});												function deltypecheck(vaal)												{																										if(vaal=='Other')													{														$("#othertype").css('display','');														$("#otherdtype").attr('required',true);																											}else													{														$("#othertype").css('display','none');														$("#otherdtype").attr('required',false);																											}																									}												</script>																																																 
                              <div class="col-md-4" id="othertype" style="display:none;">
                                 <div class="form-group">														 <label for="field-2" class="control-label">Define Delivery Time</label>														 <span id="error_vendor_name" style="color:red;"></span>														<input type="number" name="otherdtype" id="otherdtype" class="form-control" value="<?php echo $vendor->deliverytime;?>" >																											</div>
                              </div>
                              <div class="col-md-4">
                                 <div class="form-group">														 <label for="field-2" class="control-label">Packing (%)</label>														 <span id="error_packingpercentage" style="color:red;"></span>														<input type="number" name="packingpercentage" id="packingpercentage" class="form-control" value="<?php echo $vendor->packingpercentage;?>">													</div>
                              </div>
                              <div class="col-md-4">
                                 <div class="form-group">														 <label for="field-2" class="control-label">Freight (%)</label>														 <span id="error_freightpercentage" style="color:red;"></span>														<input type="number" name="freightpercentage" id="freightpercentage" class="form-control" value="<?php echo $vendor->freightpercentage;?>">													</div>
                              </div> -->
                        </div>
                        <div class="col-md-9"></div>										<div class="col-md-3">											<div class="form-group pull-right" style="padding-top:24px;">												<label>&nbsp;</label>												<input type="submit" class="btn btn-success" id="venupdate" value="Update">											</div>										</div>																		</form>                                                                   
                     </div>
                  </div>
                  <!-- end row -->                        
               </div>
               <!-- end ard-box -->                    
            </div>
            <!-- end col-->                
         </div>
         <!-- end row -->                 <!-- Footer --><?php $this->load->view('common/footer');?>                <!-- End Footer -->            
      </div>
      <!-- end container -->        </div>        <!-- end wrapper -->         <!-- jQuery  -->        <script src="<?php echo assets_url;?>js/jquery.min.js"></script>        <script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>        <script src="<?php echo assets_url;?>js/detect.js"></script>        <script src="<?php echo assets_url;?>js/fastclick.js"></script>        <script src="<?php echo assets_url;?>js/jquery.slimscroll.js"></script>        <script src="<?php echo assets_url;?>js/jquery.blockUI.js"></script>        <script src="<?php echo assets_url;?>js/waves.js"></script>        <script src="<?php echo assets_url;?>js/wow.min.js"></script>        <script src="<?php echo assets_url;?>js/jquery.nicescroll.js"></script>        <script src="<?php echo assets_url;?>js/jquery.scrollTo.min.js"></script>        <!-- Datatables-->        <script src="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.js"></script>        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.bootstrap.js"></script>        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.buttons.min.js"></script>        <script src="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.js"></script>        <script src="<?php echo assets_url;?>plugins/datatables/jszip.min.js"></script>        <script src="<?php echo assets_url;?>plugins/datatables/pdfmake.min.js"></script>        <script src="<?php echo assets_url;?>plugins/datatables/vfs_fonts.js"></script>        <script src="<?php echo assets_url;?>plugins/datatables/buttons.html5.min.js"></script>        <script src="<?php echo assets_url;?>plugins/datatables/buttons.print.min.js"></script>        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.fixedHeader.min.js"></script>        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.keyTable.min.js"></script>        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.responsive.min.js"></script>        <script src="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.js"></script>        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.scroller.min.js"></script>        <!-- Datatable init js -->        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>        <!-- App js -->        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>        <script src="<?php echo assets_url;?>js/jquery.app.js"></script><script>$(document).ready(function(){	   $("#venupdate").attr('disabled',false);	   $("#venupdate").val('Update');  $("#loginForm").on("submit", function(){   // $("#pageloader").fadeIn();   $("#venupdate").attr('disabled',true);     $("#venupdate").val('Please Wait...');  });//submit});//document ready</script>     <script language="javascript" type="text/javascript">   $(document).ready(function() {$("#venupdate").click(function() {var vendor_name = $("#vendor_name").val();if(vendor_name==''){	$("#error_vendor_name").html('Required!');}var item_name = $("#item_name").val();if(item_name==''){		$("#error_item_name").html('Required!');}var status = $("#status").val();if(status==''){		$("#error_status").html('Required!');}if(vendor_name=='' || item_name==''|| status=='' ){		return false;}});});</script>    
   </body>
</html>