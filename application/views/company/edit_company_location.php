<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle;?> Edit Location</title>

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
                           
                            <h4 class="page-title">Edit Location</h4>
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
								$id = $this->uri->segment(4);
								$this->db->select('bank_details_hpcl,pincode,hpcl_code,id,companyname,rack_location,gst,address,contact_person,mobile,email_id,status,state_id,city_id,alt_mobile,landline_number,googlemap,locate_us, bank_details')->from('store_rack_location')->where('id',$id);
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $rack)
								?>
								
                                   <form method="post" action="<?php echo page_url;?>Master/Company_location/update_company_location/<?php echo $rack->id;?>"  enctype="multipart/form-data">
								   

                                     <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Company Name</label>
                                                         <span id="error_rack_location" style="color:red;"></span>
                                                         <input type="text" class="form-control" name="company" id="company"  value="<?php echo $rack->companyname;?>" required>
                                                    </div>
                                                </div>

										  <div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">Location</label>
														 <span id="error_rack_location" style="color:red;"></span>
														 <input type="text" class="form-control" name="rack_location" id="rack_location"  value="<?php echo $rack->rack_location;?>" required>
													</div>
												</div>
												
                                                    <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">GST</label>
                                                         <span id="error_rack_location" style="color:red;">*</span>
                                                         <input type="text" class="form-control" name="gst" id="gst"   value="<?php echo $rack->gst;?>" required>
                                                    </div>
                                                </div>


                                                 <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Address</label>
                                                         <span id="error_rack_location" style="color:red;">*</span>
                                                        <textarea name="address" required class="form-control"><?php echo $rack->address;?></textarea>
                                                    </div>
                                                </div>
                                        
                                             <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Pincode</label>
                                                         <span id="error_rack_location" style="color:red;">*</span>
                                                        <input type="text" class="form-control" name="pincode" id="pincode"  value="<?php echo $rack->pincode;?>"  required>
                                                    </div>
                                                </div>

                                             <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Contact Person</label>
                                                         <span id="error_rack_location" style="color:red;">*</span>
                                                        <input type="text" class="form-control" name="contact_person" id="contact_person"  value="<?php echo $rack->contact_person;?>"  required>
                                                    </div>
                                                </div>

                                                  <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Mobile</label>
                                                         <span id="error_rack_location" style="color:red;">*</span>
                                                        <input type="number" class="form-control" name="mobile" id="mobile"   value="<?php echo $rack->mobile;?>"  required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Alt Mobile</label>
                                                         <span id="error_rack_location" style="color:red;"></span>
                                                        <input type="number" class="form-control" name="altmobile" id="altmobile"  value="<?php echo $rack->alt_mobile;?>" >
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Offical Landline Number</label>
                                                         <span id="error_rack_location" style="color:red;">*</span>
                                                        <input type="text" class="form-control" name="landline" id="landline"  value="<?php echo $rack->landline_number;?>" required>
                                                    </div>
                                                </div>

                                                   <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Email</label>
                                                         <span id="error_rack_location" style="color:red;">*</span>
                                                        <input type="email" class="form-control" name="email" id="email"  value="<?php echo $rack->email_id;?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                <div class="form-group">
                                                <label for="field-2" class="control-label">State <span style="color:red">*</span></label>
                                                <span id="error_state" style="color:red;"></span>
                                                <select class="form-control" name="state" id="state" required onchange="getcity();">
                                                <option value="">Select State</option>
                                                <?php 
                                                $q = $this->db->select('state_id, state_name')->from('states')->where('country_id','101')->get();
                                                foreach($q->result() as $row){
                                                ?>
                                                <option value="<?php echo $row->state_id;?>" <?php if($row->state_id==$rack->state_id){ echo "selected"; }?>><?php echo $row->state_name;?></option>
                                                <?php }?>
                                                </select>
                                                </div>
                                                </div>
                                                <div class="col-md-4">
                                                <div class="form-group">
                                                <label for="field-2" class="control-label">City <span style="color:red">*</span></label>
                                                <span id="error_state" style="color:red;"></span>
                                                <select class="form-control select2" name="cityname" id="cityname" required >
                                                    <?php 
                                                $c = $this->db->select('city_id,city_name')->from('cities')->where('state_id',$rack->state_id)->get();
                                                if($c->num_rows()>0)
                                                {
                                                foreach($c->result() as $rows)
                                                {
                                                ?>
                                                <option value="<?php echo $rows->city_id; ?>" <?php if($rows->city_id==$rack->city_id){?> selected <?php } ?>><?php echo $rows->city_name; ?></option>
                                                <?php } }?>
                                                </select>
                                                </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Google Map Link</label>
                                                         <span id="error_rack_location" style="color:red;">*</span>
                                                        <input type="text" class="form-control" name="googlemap" id="googlemap"  value="<?php echo $rack->googlemap; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Locate us on Website </label>
                                                         <span id="error_rack_location" style="color:red;">*</span>
                                                        <input type="text" class="form-control" name="locateus" id="locateus"  value="<?php echo $rack->locate_us; ?>" required>
                                                    </div>
                                                </div>


                                                 <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">HPCL Business Code </label>
                                                         <span id="error_rack_location" style="color:red;"></span>
                                                        <input type="number" class="form-control" name="hpclcode" id="hpclcode"  value="<?php echo $rack->hpcl_code;?>">
                                                    </div>
                                                </div>

                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="field-2" class="control-label">Bank Details</label>
                                                        <textarea class="form-control" name="bank_details"><?php echo $rack->bank_details;?></textarea>
                                                    </div>
                                                </div>


                                                  <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="field-2" class="control-label">Bank Details for HPCL</label>
                                                        <textarea class="form-control" name="bank_details_hpcl"><?php echo $rack->bank_details_hpcl;?></textarea>
                                                    </div>
                                                </div>


                                                  <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Company Profile

                                                         </label>
                                                        
                                                        <input type="file" name="profile" id="profile" class="form-control">
                                                    </div>
                                                </div>

                                                <div style="clear: both;height: 10px;"></div>

                                                <div class="col-md-12">
                                                    <h4 class="page-title">Bank Accounts</h4>
                                                    <hr>
                                                </div>

                                                <?php
                                                $rt=$this->db->select('*')->from('store_rack_location_account')->where('location_id',$id)->get();
                                                if($rt->num_rows()>0)
                                                {
                                                    foreach($rt->result() as $rtt)
                                                    {
                                                ?>
                                                <div class="col-md-12">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Bank Name <span style="color:red;">*</span></label>
                                                                <input type="hidden" name="edit_id[]" value="<?php echo $rtt->id;?>">
                                                                <input type="text" name="bankname_edit<?php echo $rtt->id;?>" id="bankname_edit<?php echo $rtt->id;?>" value="<?php echo $rtt->bank_name;?>" class="form-control">
                                                        </div>
                                                    </div>
                                                      <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Account No. <span style="color:red;">*</span></label>
                                                                <input type="text" name="account_edit<?php echo $rtt->id;?>" id="account_edit<?php echo $rtt->id;?>" value="<?php echo $rtt->account;?>" class="form-control" required>
                                                        </div>
                                                    </div>

                                                      <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>IFSC <span style="color:red;">*</span></label>
                                                                <input type="text" name="ifsc_edit<?php echo $rtt->id;?>" id="ifsc_edit<?php echo $rtt->id;?>" value="<?php echo $rtt->ifsc;?>" class="form-control" required>
                                                        </div>
                                                    </div>


                                                      <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Branch <span style="color:red;">*</span></label>
                                                                <input type="text" name="branch_edit<?php echo $rtt->id;?>" id="branch_edit<?php echo $rtt->id;?>" value="<?php echo $rtt->branch;?>" class="form-control" required>
                                                        </div>
                                                    </div>


                                                    <div class="col-md-1">
                                                    <div class="form-group">
                                                    <label>Primary Account</label><br/>
                                                    <input type="checkbox" class="check1only" name="primary_edit<?php echo $rtt->id;?>" id="primary_edit<?php echo $rtt->id;?>" value="1" <?php if($rtt->primary_account==1){?> checked <?php } ?>  onchange="checkmeonly();">
                                                    </div>
                                                    </div>

                                                </div>
                                                <?php
                                                } 
                                                } 
                                                ?>



                                                <div class="col-md-12">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Add More Accounts?</label><br/>
                                                               
                                                                <input type="checkbox" name="addnaccount" id="addnaccount" value="1" onchange="new_accounts()">
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="col-md-12 newaccounts" style="display:none;">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Bank Name <span style="color:red;">*</span></label>
                                                                
                                                                <input type="text" name="bankname[]" id="bankname[]" value="" class="form-control">
                                                        </div>
                                                    </div>
                                                      <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Account No. <span style="color:red;">*</span></label>
                                                                <input type="text" name="account[]" id="account[]" value="" class="form-control"> 
                                                        </div>
                                                    </div>

                                                      <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>IFSC <span style="color:red;">*</span></label>
                                                                <input type="text" name="ifsc[]" id="ifsc[]" value="" class="form-control">
                                                        </div>
                                                    </div>


                                                      <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Branch <span style="color:red;">*</span></label>
                                                                <input type="text" name="branch[]" id="branch[]" value="" class="form-control">
                                                        </div>
                                                    </div>


                                                    <div class="col-md-1">
                                                    <div class="form-group">
                                                    <label>Primary Account</label><br/>
                                                    <input type="checkbox" name="primary[]" id="primary[]" class="check1only" value="1" >
                                                    </div>
                                                    </div>
                                                    <div class="col-md-1 text-center">
                                                    <div class="form-group">
                                                    <label></label><br/>
                                                    <a href='javascript:;' class="btn btn-warning btn-xs add_more"><i class="fa fa-plus"></i></a>
                                                    </div>
                                                    </div>

                                                </div>
                                                 <div class="col-md-12 newaccounts" id="addnewaccount" style="display:none;">
                                                     
                                                 </div>



                                                <div class="col-md-12">
                                                    <div class="col-md-4"></div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Status</label>
                                                         <span id="error_rack_location" style="color:red;">*</span>
                                                        <select class="form-control" name="status" id="status">
                                                            <option value="1" <?php if($rack->status==1){ echo "selected";} ?>>ACTIVE</option>
                                                            <option value="0" <?php if($rack->status==0){ echo "selected";} ?>>INACTIVE</option>
                                                        </select>
                                                    </div>
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

        var j = 1;
        $('.add_more').click(function() {
        $('#addnewaccount').append('<div id="row'+j+'"><div class="col-md-3"><div class="form-group"><label>Bank Name <span style="color:red;">*</span></label><input type="text" name="bankname[]" id="bankname'+j+'" value="" class="form-control"></div></div><div class="col-md-3"><div class="form-group"><label>Account No. <span style="color:red;">*</span></label><input type="text" name="account[]" id="account'+j+'" value="" class="form-control"></div></div><div class="col-md-2"><div class="form-group"><label>IFSC <span style="color:red;">*</span></label><input type="text" name="ifsc[]" id="ifsc'+j+'" value="" class="form-control"></div> </div><div class="col-md-2"><div class="form-group"><label>Branch <span style="color:red;">*</span></label><input type="text" name="branch[]" id="branch'+j+'" value="" class="form-control"></div></div><div class="col-md-1"><div class="form-group"><label>Primary Account</label><br/><input type="checkbox" name="primary[]" class="check1only" onchange="check_primary(this);" id="primary'+j+'" value="1"></div></div><div class="col-md-1 text-center"><div class="form-group"><label></label><br/><a href="javascript:;" class="btn btn-danger btn-xs remove" data-id="'+j+'"><i class="fa fa-minus"></i></a></div></div></div>');
        j++;
        });

        $(document).on('click', '.remove', function() {
        var i= $(this).attr('data-id');
        $(this).parents("#row"+i).remove();

       

        });



$("#save").click(function() {
var rack_location = $("#rack_location").val();
if(rack_location=='')
{
	$("#error_rack_location").html('Required!');
}

if(rack_location=='')
{
	
	return false;
}

});
});
</script>
<script>
    function getcity()
    {
        var stateid=$('#state').val();
          if (stateid !== '') {
            $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Master/Company_location/getcityname",
                data: "stateid=" + stateid,
                success: function(data) {
                    //alert(data);
                    $("#cityname").html(data);
                }
            });
        }

    }

    function new_accounts()
    {

        if($('#addnaccount').is(":checked"))
        {
        
            $(".newaccounts").css('display','');
            $(".newaccounts :input").attr('required',true);

        }else
        {   
           
             $(".newaccounts").css('display','none');
            $(".newaccounts :input").attr('required',false);
        }

        $(".check1only").attr('required',false);
        
    }

        $('.check1only').on('change', function() {
        
            $(".check1only").prop("checked", false); //uncheck all checkboxes
            $(this).prop("checked", true);

        });

        function check_primary(ele)
        {
            
        
            $(".check1only").prop("checked", false); //uncheck all checkboxes
            $(ele).prop("checked", true);

        }

</script>
    </body>
</html>