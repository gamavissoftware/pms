<?php
$id=$this->uri->segment(4);
$CI = &get_instance();
$CI->load->model('Master_model', 'master');
$getRackLocation = $CI->master->getRackLocation();
$itemid='';

	$name='';

	$phone_number='';

	$prno='';

	$email='';

	$address='';

	$delivery_days='';

	$delivery_by='';

	$payment_terms='';

	$payment_mode='';

	$original_price='';

	$discounted_price='';

	$discount_type='';

	$discount_percent='';

	$total_value='';
	$code='';

if($id<>'')

{

	

	$this->db->select('*')->from('vendor_for_review')->Where('status','1');

	$query = $this->db->get();

	

	if($query->num_rows()>0)

	{

	foreach($query->result() as $row);

	

	$itemid=$row->item_id;

	$name=$row->vendor_name;

	$phone_number=$row->phone_number;

	$email=$row->email;

	$address=$row->address;

	$delivery_days=$row->delivery_days;

	$delivery_by=$row->delivery_by;

	$payment_terms=$row->payment_terms;

	$payment_mode=$row->payment_mode;

	$original_price=$row->original_price;

	$discounted_price=$row->discounted_price;

	$discount_type=$row->discount_type;

	$discount_percent=$row->discount_percent;

	$total_value=$row->total_value;

	$prno=$row->prno;

	

	

	}

			

	

	

	

}

?>

<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="<?php echo copyright; ?>">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title><?php echo sitetitle; ?> Vendor List</title>



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

		<?php

		if($this->uri->segment(4)<>'')

		{

		?>

		<script>

		$( document ).ready(function() {

		$("#con-close-modal").modal('show');

		});

		</script>

		<?php

		}

		?>
<?PHP 

$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();

foreach($q->result() as $LOGO);

?>

		<style>

		table.manglesh thead th {

				background: <?php echo $LOGO->colorcode;?>;

				color:#fff;

				font-weight:bold;

				text-align:center;

			}

		</style>


    </head>





    <body>





        <!-- Navigation Bar-->

        <header id="topnav">

          <?php $this->load->view('common/nav-menu');?>

        </header>

        <!-- End Navigation Bar-->


		<?php $this->load->view('common/info-section.php');?>


        <div class="wrapper">

            <div class="container-fluid">



                <!-- Page-Title -->

                <div class="row" style="margin-top:20px;">

                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">

                        <div class="page-title-box">

						 <div class="btn-group pull-right">

						  <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">Add New Vendor</button>

                               

                            </div>

                           

                            <h4 class="page-title">Vendor List</h4>

                        </div>

                    </div>

                </div>

                <!-- end page title end breadcrumb -->

<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>



                 <div class="row">

                    <div class="col-sm-12">

                        <div class="card-box table-responsive">

                            <table id="example" class="table table-bordered manglesh">

                                <thead>

                                <tr>

                                    <th>SR NO</th>

                   <th>COMPANY</th>
                   <th>VENDOR NAME</th>
                   <th>VENDOR CODE</th>
                  

									 <th>CONTACT PERSON</th>

                                    <th>PHONE</th>

                                    <th>EMAIL</th>

                                    <th>GST</th>

                                    <th>PAN</th>

                                    <th>ADDRESS</th>

                                    <!-- <th>PAYMENT TERM</th>

                                    <th>CREDIT PERIOD</th>

                                    <th>DELIVERY BY</th>

                                    <th>DELIVERY TIME</th>
									<th>FREIGHT (%)</th>
									<th>PACKING (%)</th> -->

                                    <th>EDIT</th>

                                    

                                </tr>

                                </thead>

								<tbody></tbody>

                                

                            </table>

                        </div>

                    </div>

                </div>

                <!-- end row -->

 <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

 <form id="loginForm" method="post" action="<?php echo page_url;?>Master/User_management/add_new_vendors/<?php echo $this->uri->segment('4');?>"  enctype="multipart/form-data" onsubmit="return validate();">

<input type="hidden" name="itemid" value="<?php echo $itemid;?>">

<input type="hidden" name="originalprice" value="<?php echo $original_price;?>">

<input type="hidden" name="discounttype" value="<?php echo $discount_type;?>">

<input type="hidden" name="discount_percent" value="<?php echo $discount_percent;?>">

<input type="hidden" name="discount_price" value="<?php echo $discounted_price;?>">

<input type="hidden" name="finalvalue" value="<?php echo $total_value;?>">

<input type="hidden" name="prno" value="<?php echo $prno;?>">

                                <div class="modal-dialog modal-lg">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">Add New Vendor</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">                       
 
                         <div class="col-md-4">

                          <div class="form-group">

                             <label for="field-2" class="control-label">Company</label>

                             <span id="error_company" style="color:red;">*</span>
                             <select class="form-control" name="company" id="company"  required>
                               <option value="">Select Company</option>
                                <?php if($getRackLocation != '') {
                                        foreach($getRackLocation as $row1) {?>
                                    <option value="<?php echo $row1->id;?>"><?php echo $row1->companyname;?></option>
                                    <?php } } ?>
                             </select>
                             <!-- <input type="text" class="form-control" name="company" id="company"  autocomplete="off" required> -->

                          </div>

                        </div>



                                             <div class="col-md-4">

													<div class="form-group">

														 <label for="field-2" class="control-label">Vendor Name</label>

														 <span id="error_vendor_name" style="color:red;">*</span>

														 <input type="text" class="form-control" name="vendor_name" id="vendor_name"  value="<?php echo $name;?>" autocomplete="off" required>

													</div>

												</div>
												<?php $q = $this->db->select('code')->from('vendors')->Where('status',1)->order_by('code','desc')->limit(1)->get();
													if($q->num_rows()>0){
														foreach($q->result() as $ros);
														$currentcode = $ros->code;
														$add = 1;
														$nextcode = $currentcode+$add;
													}else{
														$nextcode = "";
													}
												?>

												   <div class="col-md-4">

													<div class="form-group">

														 <label for="field-2" class="control-label">Vendor Code</label>

														 <span id="error_vendor_name" style="color:red;">*</span>

														 <input type="text" class="form-control" name="vendor_code" id="vendor_code"  value="<?php echo $nextcode ;?>" autocomplete="off" readonly required>

													</div>

												</div>

												

												

												 <div class="col-md-4">

													<div class="form-group">

														 <label for="field-2" class="control-label">Contact Person</label>

														 <span id="error_contactperson" style="color:red;">*</span>

														 <input type="text" class="form-control" name="contactperson" id="contactperson"  autocomplete="off" required>

													</div>

												</div>

												

												 <div class="col-md-4">

													<div class="form-group">

														 <label for="field-2" class="control-label">Contact</label>

														 <span id="error_contact" style="color:red;">*</span>

														 <input type="text" class="form-control" name="contact" id="contact"  value="<?php echo $phone_number;?>" autocomplete="off" required>

													</div>

												</div>

												

												

												 <div class="col-md-4">

													<div class="form-group">

														 <label for="field-2" class="control-label">Email ID</label>

														 <span id="error_email" style="color:red;">*</span>

														 <input type="text" class="form-control" name="email" id="email" autocomplete="off" required>

													</div>

												</div>

												

												

												 <div class="col-md-4">

													<div class="form-group">

														 <label for="field-2" class="control-label">GST No.</label>

														 <span id="error_gst" style="color:red;">*</span>

														 <input type="text" class="form-control" name="gst" id="gst" autocomplete="off" required>

													</div>

												</div>

												

												 <div class="col-md-4">

													<div class="form-group">

														 <label for="field-2" class="control-label">PAN No.</label>

														 <span id="error_pan" style="color:red;">*</span>

														 <input type="text" class="form-control" name="pan" id="pan"   autocomplete="off" required>

													</div>

												</div>

												

												

												 <div class="col-md-8">

													<div class="form-group">

														 <label for="field-2" class="control-label">Address</label>

														 <span id="error_address" style="color:red;">*</span>

														 <textarea class="form-control" name="address" id="address"   autocomplete="off" required style="resize:none;"><?php echo $address;?></textarea>

													</div>

												</div>

												

												 <!-- <div class="col-md-4">

													<div class="form-group">

														 <label for="field-2" class="control-label">Payment Terms</label>

														 <span id="error_vendor_name" style="color:red;"></span>

													 <input type="text" class="form-control" name="payment" id="payment"  value="<?php echo $payment_terms;?>" autocomplete="nope" required>

													</div>

												</div>

												

												

												 <div class="col-md-4">

													<div class="form-group">

														 <label for="field-2" class="control-label">Credit Days</label>

														 <span id="error_vendor_name" style="color:red;"></span>

														 <input type="text" class="form-control" name="credit" id="credit" autocomplete="nope" required>

													</div>

												</div>

												

												

												 <div class="col-md-4">

													<div class="form-group">

														 <label for="field-2" class="control-label">Delivery By</label>

														 <span id="error_vendor_name" style="color:red;"></span>

														<select  class="form-control" name="dby" id="dby" required>

														<option value="">Delivery By</option>

														<option value="By Presto" <?php if($delivery_by=='By Presto'){?> selected <?php } ?>>By Presto</option>

														<option value="By Vendor" <?php if($delivery_by=='By Vendor'){?> selected <?php } ?>>By Vendor</option>

														</select>

													</div>

												</div>

												

												

												 <div class="col-md-4">

													<div class="form-group">

														 <label for="field-2" class="control-label">Delivery Time</label>

														 <span id="error_vendor_name" style="color:red;"></span>

														<select  class="form-control" name="del_type" id="del_type" required onchange="deltypecheck(this.value);">

													

														<option value="2" <?php if($delivery_days=='Ready'){ ?> selected <?php } ?>>Ready</option>

													

														<option value="7" <?php if($delivery_days=='7'){ ?> selected <?php } ?>>7 days</option>

														<option value="15" <?php if($delivery_days=='15'){ ?> selected <?php } ?>>15 days</option>

														<option value="30" <?php if($delivery_days=='30'){ ?> selected <?php } ?>>30 days</option>

														

														<option value="Other" <?php if($delivery_days=='Other'){ ?> selected <?php } ?>>Other</option>

														</select>

													</div>

												</div>

												<script>

												function deltypecheck(vaal)

												{

													

													if(vaal=='Other')

													{

														$("#othertype").css('display','');

														$("#otherdtype").attr('required',true);

														

													}else

													{

														$("#othertype").css('display','none');

														$("#otherdtype").attr('required',false);

														

													}

													

												}

												</script>

												

												

												 <div class="col-md-4">

													<div class="form-group">

														 <label for="field-2" class="control-label">Payment Mode</label>

														 <span id="error_vendor_name" style="color:red;">*</span>

													 <select class="form-control" name="pmode" id="pmode"  value="" required>

												<option value="">Select Mode</option>

												

												<option value="Cheque" <?php if($payment_mode=='Cheque'){?> selected <?php } ?>>Cheque</option>

												<option value="NEFT" <?php if($payment_mode=='NEFT'){?> selected <?php } ?>>NEFT</option>

											

												

													     </select>

													</div>

												</div>

												

												 <div class="col-md-4" id="othertype" style="display:none;">

													<div class="form-group">

														 <label for="field-2" class="control-label">Define Delivery Time</label>

														 <span id="error_vendor_name" style="color:red;"></span>

														<input type="number" name="otherdtype" id="otherdtype" class="form-control" >

													</div>

												</div>
												
												 <div class="col-md-4">

													<div class="form-group">

														 <label for="field-2" class="control-label">Packing (%)</label>

														 <span id="error_packingpercentage" style="color:red;"></span>

														<input type="number" name="packingpercentage" id="packingpercentage" class="form-control" >

													</div>

												</div>
												
												 <div class="col-md-4">

													<div class="form-group">

														 <label for="field-2" class="control-label">Freight (%)</label>

														 <span id="error_freightpercentage" style="color:red;"></span>

														<input type="number" name="freightpercentage" id="freightpercentage" class="form-control" >

													</div>

												</div> -->

                                            </div>

											

											

                                        </div>

                                        <div class="modal-footer">

                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

                                            <input type="submit" id="vensave" class="btn btn-info" value="Submit"> 

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

"sAjaxSource": "<?php echo page_url;?>Master/User_management/vendor_list",

"aoColumns": [

				{ mData: 'sr_no' } ,

        { mData: 'company' },
        { mData: 'vendor_name' },
        { mData: 'code' },
       		{ mData: 'person' },

				{ mData: 'phone' },

				{ mData: 'email' },

				{ mData: 'gst'},

				{ mData: 'pan'},

				{ mData: 'address' },

				// { mData: 'payment' },

				// { mData: 'credit' },

				// { mData: 'deliveryby' },

				// { mData: 'deliverytime' },
				// { mData: 'packingpercentage' },
				// { mData: 'freightpercentage' },
				{ mData: 'edit' }

		]

});   

});



</script>
<script>
$(document).ready(function(){
	   $("#vensave").attr('disabled',false);
	   $("#vensave").val('submit');
  $("#loginForm").on("submit", function(){
   // $("#pageloader").fadeIn();
   $("#vensave").attr('disabled',true);
     $("#vensave").val('Please Wait...');
  });//submit
});//document ready
</script>
		

		

<script language="javascript" type="text/javascript">   



$(document).ready(function() {

$("#vensave").click(function() {

	company

var company = $("#company").val();

if(company=='')

{
$("#error_company").html('Required!');

}

var vendor_name = $("#vendor_name").val();

if(vendor_name=='')

{
$("#error_vendor_name").html('Required!');

}

var contactperson = $("#contactperson").val();

if(contactperson=='')

{
$("#error_contactperson").html('Required!');

}



var status = $("#status").val();

if(status=='')

{
$("#error_status").html('Required!');

}

var contact = $("#contact").val();

if(contact=='')

{
$("#error_contact").html('Required!');

}
var email = $("#email").val();

if(email=='')

{
$("#error_email").html('Required!');

}
var gst = $("#gst").val();

if(gst=='')

{
$("#error_gst").html('Required!');

}

var pan = $("#pan").val();

if(pan=='')

{
$("#error_pan").html('Required!');

}

var address = $("#address").val();

if(address=='')

{
$("#error_address").html('Required!');

}





if(company=='' || vendor_name=='' || contactperson==''|| contact=='' || email==='' || gst=='' || pan=='' || address=='')

{

	

	return false;

}



});

});

</script>

    </body>

</html>