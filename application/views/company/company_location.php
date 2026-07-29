<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle;?> Locations</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
        <link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
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
						  <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">Add New Location</button>
                               
                            </div>
                           
                            <h4 class="page-title">Company Location List</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

                 <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>SR NO</th>
                                    <th>Company Name</th>
									 <th>Location</th>
                                      <th>GST</th>
                                       <th>Address</th>
                                         <th>Pincode</th>
                                        <th>Contact Person</th>
                                         <th>Mobile</th>
                                          <th>Email</th>
                                          <th>state</th>
                                          <th>Landline Number</th>
                                          <th>Google Map</th>
                                          <th>Locate us</th>
                                           <th>HPCL Code</th>

                                            <th>Bank Details</th>
                                            <th>Bank Details for HPCL</th>  
                                                <th>Status</th>
                                                <th>Action</th>
                                    
                                </tr>
                                </thead>
								<tbody></tbody>
                                
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
 <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="<?php echo page_url;?>Master/Company_location/add_company_location"  enctype="multipart/form-data">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Add New Location</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Company Name</label>
                                                         <span id="error_rack_location" style="color:red;">*</span>
                                                         <input type="text" class="form-control" name="company" id="company"  value="" required>
                                                    </div>
                                                </div>

                                             <div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">Location</label>
														 <span id="error_rack_location" style="color:red;">*</span>
														 <input type="text" class="form-control" name="rack_location" id="rack_location"  value="" required>
													</div>
												</div>


                                                  <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">GST</label>
                                                         <span id="error_rack_location" style="color:red;">*</span>
                                                         <input type="text" class="form-control" name="gst" id="gst"  value="" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">

                                                 <div class="col-md-12">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Address</label>
                                                         <span id="error_rack_location" style="color:red;">*</span>
                                                        <textarea name="address" required class="form-control"></textarea>
                                                    </div>
                                                </div>
                                        
											

                                                  <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Contact Person</label>
                                                         <span id="error_rack_location" style="color:red;">*</span>
                                                        <input type="number" class="form-control" name="pincode" id="pincode"  value="" required>
                                                    </div>
                                                </div>

                                             <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Contact Person</label>
                                                         <span id="error_rack_location" style="color:red;">*</span>
                                                        <input type="text" class="form-control" name="contact_person" id="contact_person"  value="" required>
                                                    </div>
                                                </div>

                                                  <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Mobile</label>
                                                         <span id="error_rack_location" style="color:red;">*</span>
                                                        <input type="number" class="form-control" name="mobile" id="mobile"  value="" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Alt Mobile</label>
                                                         <span id="error_rack_location" style="color:red;"></span>
                                                        <input type="number" class="form-control" name="altmobile" id="altmobile"  value="" >
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Offical Landline Number</label>
                                                         <span id="error_rack_location" style="color:red;">*</span>
                                                        <input type="text" class="form-control" name="landline" id="landline"  value="" required>
                                                    </div>
                                                </div>
                                                   <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Email</label>
                                                         <span id="error_rack_location" style="color:red;">*</span>
                                                        <input type="email" class="form-control" name="email" id="email"  value="" required>
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
                                                <option value="<?php echo $row->state_id;?>"><?php echo $row->state_name;?></option>
                                                <?php }?>
                                                </select>
                                                </div>
                                                </div>
                                                <div class="col-md-4">
                                                <div class="form-group">
                                                <label for="field-2" class="control-label">City <span style="color:red">*</span></label>
                                                <span id="error_state" style="color:red;"></span>
                                                <select class="form-control select2" name="cityname" id="cityname" required onchange="getcity();">
                                                <option value="">Select City</option>
                                                
                                                </select>
                                                </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Google Map Link</label>
                                                         <span id="error_rack_location" style="color:red;">*</span>
                                                        <input type="text" class="form-control" name="googlemap" id="googlemap"  value="" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Locate us on Website </label>
                                                         <span id="error_rack_location" style="color:red;">*</span>
                                                        <input type="text" class="form-control" name="locateus" id="locateus"  value="" required>
                                                    </div>
                                                </div>


                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">HPCL Business Code </label>
                                                         <span id="error_rack_location" style="color:red;"></span>
                                                        <input type="number" class="form-control" name="hpclcode" id="hpclcode"  value="">
                                                    </div>
                                                </div>

                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="field-2" class="control-label">Bank Details</label>
                                                        <textarea class="form-control" name="bank_details"></textarea>
                                                    </div>
                                                </div>


                                                 <div class="col-md-4">
                                                    <div class="form-group">
                                                         <label for="field-2" class="control-label">Status</label>
                                                         <span id="error_rack_location" style="color:red;">*</span>
                                                        <select class="form-control" name="status" id="status">
                                                            <option value="1">ACTIVE</option>
                                                            <option value="0">INACTIVE</option>
                                                        </select>
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
"pagination":true,
"sAjaxSource": "<?php echo page_url;?>Master/Company_location/campany_location_list",
"aoColumns": [
				{ mData: 'sr_no' } ,
                { mData: 'companyname' },
				{ mData: 'rack_location' },
                { mData: 'gst' },
                { mData: 'address' },
                { mData: 'pincode' },
                { mData: 'contactperson' },
                { mData: 'mobile' },
                { mData: 'email' },
                { mData: 'state' },
                { mData: 'landline_number' },
                { mData: 'googlemap' },
                { mData: 'locate_us' },
                { mData: 'hpclcode' },
                { mData: 'bank_details' },
                { mData: 'bank_details_hpcl' },
                { mData: 'status' },
				{ mData: 'edit' }
				
				
		]
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
</script>		
		
<script language="javascript" type="text/javascript">   

$(document).ready(function() {
$("#save").click(function() {
var vendor_name = $("#vendor_name").val();
if(vendor_name=='')
{
	$("#error_vendor_name").html('Required!');
}
var item_name = $("#item_name").val();
if(item_name=='')
{
	
	$("#error_item_name").html('Required!');
}

var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}


if(vendor_name=='' || item_name==''|| status=='' )
{
	
	return false;
}

});
});
</script>
    </body>
</html>