<?php
$CI =& get_instance();
$CI->load->model('Salescrm_model', 'salescrm');
$DI =& get_instance();
$DI->load->model('Dashboard_model');

$getTeams = $CI->salescrm->getTeams();
$getProducts = $CI->salescrm->getProducts($this->uri->segment(3));
// $mode=$DI->Dashboard_model->getsettings();
// if(count($mode)>0)
// {
// 	$mode=$mode['mode'];
// }else
// {
// 	$mode=1;
// }
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
	                    <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success" name="action_button"><i class="fa fa-arrow-left"></i>Back</button></a>
					 <div class="btn-group pull-right"></div>
	                   
	                    <h4 class="page-title">Edit leads</h4>
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
								$this->db->select('*')
										 ->from('leads')
										 ->where('id',$id);
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $row)
									$status = $row->status;
									if($status=='1'){
										$sta = "Active";
									}else{
										$sta = "InActive";
									}
								?>
								

                            <form method="post" action="<?php echo page_url;?>Leads/update_lead_information/<?php echo $row->id;?>">
								   <h5 class="modal-title"><strong>LEAD INFORMATION</strong></h5>
								   <hr>
                                    <input type="hidden" name="lasturl" value="<?php echo $this->uri->segment(4);?>">

							<div class="row">
								<div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">Create Date</label>
										<span style="color:red;">*</span>
										<span id="error_create_date" style="color:red;"></span>
										<input type="text" id="create_date" name="create_date" class="form-control" value="<?php echo date('d-m-Y', strtotime($row->create_date));?>">
                                    </div>
                                </div>

								<div class="col-md-3">
	                                <div class="form-group">
	                                    <label for="field-1" class="control-label">Lead Source</label>
										<span style="color:red;">*</span>
										<span id="error_lead_source" style="color:red;"></span>
										<select class="form-control" id="lead_source" name="lead_source" onchange="getTeamViaSource()">
											<option value="">--Select Lead Source--</option>
											<?php $query = $this->db->select('source_id, lead_source, status')->from('lead_source')->where('company_id',$_SESSION['logged_in']['business_location'])->where('status','1')->get();
											foreach($query->result() as $lead_source){?>													
											<option value="<?php echo $lead_source->source_id;?>" <?php if($row->lead_source_id==$lead_source->source_id){echo "selected";}?>> <?php echo $lead_source->lead_source;?> </option>	
										<?php }?>	
										</select>
	                            	</div>
	                            </div>

								<!-- <div class="col-md-3">
									<div class="form-group">
										<label for="field-2" class="control-label">Status</label>
										<span style="color:red;">*</span>
										<span id="error_status" style="color:red;"></span>
										<select class="form-control" name="status" id="status"  value="" >
											<option value="<?php echo $row->status; ?>"></option>
											<option value="1" <?php if( $row->status=='1'){echo "selected";}?>> Active </option>
										 	<option value="0" <?php if( $row->status=='0'){echo "selected";}?>> Inactive </option>
										</select>
									</div>
								</div> -->

								<div class="col-sm-3">
									<div class="form-group">
	                                    <label for="field-2" class="control-label">Customer Unique ID </label>
	                                    <input type="text" class="form-control" placeholder="Customer Unique ID" value="<?php echo $row->unique_id; ?>" readonly/>
									</div>
								</div>
							</div>

							<!-- <div class="row">
								<div class="col-md-3">
									<div class="form-group">
										 <label for="field-2" class="control-label">Assign to Team</label>
										  <span style="color:red;">*</span>
										  <span id="error_assign_team" style="color:red;"></span>
										  <select class="form-control" name="assign_team" id="assign_team">
										 	<option value="">Select Team</option> 
										 	<?php 
										 	 if(count($getTeams) > 0) {
										 	foreach($getTeams as $teams) {?>
										 	<option value="<?php echo $teams->team_id;?>"><?php echo $teams->team_name;?></option>
										 <?php 
										}
										 } 
										 ?>
										  </select>
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
									 <label for="field-2" class="control-label">Assign to Team Member</label>
									  <span style="color:red;">*</span>
									  <span id="error_assign_team_member" style="color:red;"></span>
									  <select class="form-control" name="assign_team_member" id="assign_team_member">
									  </select>
									</div>
								</div>
							</div> -->

							<h5 class="modal-title"><strong>COMPANY INFORMATION</strong></h5>
							<hr>

							<div class="col-sm-4" style="border-right: 1px solid lightgrey;">
							   <div class="form-group">
                                   <label for="field-1" class="control-label">Company Name</label>
								   <input type="text" id="company_name" name="company_name" placeholder="Company Name" class="form-control" value="<?php echo $row->company_name;?>" required="">
                                </div>

                                <div class="form-group">
                                    <label for="field-1" class="control-label">Customer Type</label>
									<select class="form-control" id="patient_type" name="patient_type" onchange="check_business()">
										<option value="">--Select Customer Type--</option>	
										<?php 
										$this->db->select('*')->from('patient_type')->where('status','1')->where('company_id',$_SESSION['logged_in']['business_location']);
										$query = $this->db->get();
										$res = $query->result();
										foreach($res as $patient_type){?>	
											<option value="<?php echo $patient_type->patient_id;?>" <?php if($row->patient_type_id==$patient_type->patient_id){echo "selected";}?>> <?php echo $patient_type->patient_type;?> </option>	
										<?php }?>
									</select>
                                </div>

                                <?php 
                                	if($row->patient_type_id == 7) {
                                		$a = '';
                                	} else {
                                		$a = 'display:none;';
                                	}
                                ?>
			                        <div class="form-group" id="show_other_business" style="<?php echo $a;?>">
			                            <label for="field-1" class="control-label">Other Business Name</label>
			                            <span style="color:red;">*</span>
			                            <input type="text" class="form-control" id="other_business" name="other_business" placeholder="Other Business" autocomplete="nope" value="<?php echo $row->other_business;?>">
			                        </div>
                                <div class="form-group">
                                <label for="field-1" class="control-label">Company Location</label>
                                    <select class="form-control" id="company_location" name="company_location" onchange="getProductsOfCompany(0, 0)">
                                        <option value="">--Select Company Location--</option>
                                 <?php          $sql3 = $this->db->select('id, companyname')
                                                                 ->from('store_rack_location')
                                                                 ->get();

                                        if($sql3->num_rows() >  0) {
                                        foreach($sql3->result() as $row3) {?>   

                                        <option value="<?php echo $row3->id;?>" <?php if($row3->id==$row->hpcl_company) { echo 'selected';} ?>> <?php echo $row3->companyname;?> </option>    

                                     <?php } }?>

                                    </select>
                            </div>
                            <?php if($getProducts != '') {
                            		foreach($getProducts as $row1) {?>
                            	<input type="hidden" name="product_id[]" value="<?php echo $row1->leadproduct;?>">
                            <div class="row products<?php echo $row1->leadproduct;?>" style="display: none;">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">Competitor Product</label>
                                        <span style="color:red;">*</span>
                                        <input type="text" name="competitor_product_edit[]" value="<?php echo $row1->competitor_product;?>" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">Products</label>
                                        <span style="color:red;">*</span>
                                        <span id="error_product" style="color:red;"></span>
			                             <select class="form-control product_name0 product_refresh" name="products_edit[]">
			                             	<option value="">SELECT</option>

                                        <?php 

	$query = $this->db->select('b.id, b.instruments_name, b.pack_size')
		 				
		 				  ->from('presto_instruments b')
						  ->where('b.status',1)
						  ->get();

			if($query->num_rows()>0) {
	
				foreach($query->result() as $rowdata) {
                                       ?>
                                        	 		
                                        	 		<option value="<?php echo $rowdata->id;?>" <?php if($row1->product_id==$rowdata->id) { echo 'selected';}?>><?php echo $rowdata->instruments_name;?>-<?php echo $rowdata->pack_size;?></option>
                                        <?php	} } ?>
			                              </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <a href="javascript:void(0)" class="btn btn-danger btn-xs delete_product" onclick="delete_product(<?php echo $row1->leadproduct;?>)"><span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                                </div>
                            </div>
                        	<?php } } ?>

                        	<div class="row productss" style="display:none">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">Competitor Product</label>
                                        <span style="color:red;">*</span>
                                        <input type="text" name="competitor_product[]" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">Products</label>
                                        <span style="color:red;">*</span>
                                        <span id="error_product" style="color:red;"></span>
                                        <select class="form-control product_name0 product_refresh" name="products[]">
                                        	<option value="">SELECT</option>
                                        <?php $sql =  $this->db->select('b.id, b.instruments_name')
		 				  ->from('company_products a')
		 				  ->join('presto_instruments b', 'b.id=a.product_id')
						  ->where('a.company_id', $row->hpcl_company)
						  ->get();

                                        	 if($sql->num_rows() > 0) {
                                        	 	foreach ($sql->result() as $row2) { ?>
                                        	 		<option value="<?php echo $row2->id;?>"><?php echo $row2->instruments_name;?></option>
                                        <?php	} } ?>
			                              </select>

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <a href="javascript:void(0)" class="btn btn-warning btn-xs add_more"><span class="glyphicon glyphicon glyphicon-plus" aria-hidden="true"></span></a>
                                </div>
                            </div>

								<div class="form-group" style="display: none;">
                                    <label for="field-1" class="control-label">Country</label>
									<span style="color:red;">*</span>
									<span id="error_country" style="color:red;"></span>
							        <input type="hidden" name="country_name" id="country_name" value="101"> 
                                </div>
				

								<div class="form-group">
                                    <label for="field-2" class="control-label">City </label>
                                    <input type="text" id="city" name="city" class="form-control" placeholder="City" value="<?php echo $row->city; ?>"/>
								</div>														
					
							</div>
						   <div class="col-sm-4" style="border-right: 1px solid lightgrey;">
						   		<div class="form-group">
                                    <label for="field-2" class="control-label">Customer Name(Decision Maker)</label>
									<span style="color:red;">*</span>
									<span id="error_cust_name" style="color:red;"></span>
                                    <input type="text" id="cust_name" name="cust_name" class="form-control" placeholder="Customer Name" value="<?php echo $row->customer_name; ?>"/>
								</div>

							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
									<label for="field-2" class="control-label" style="font-size:10px;">Country Code</label>
									
									<input type="text" class="form-control" name="country_code" id="country_code"  value="<?php echo $row->country_code; ?>" readonly>
									</div>
								</div>
								<div class="col-md-8">
									<div class="form-group">
									<label for="field-2" class="control-label">Contact Number</label>
									<span style="color:red;">*</span>
									<span id="error_mobile_no" style="color:red;"></span>
									<input type="text" class="form-control" name="mobile_no" id="mobile_no"  value="<?php echo $row->contact_no;?>" placeholder="Contact Number" >
									</div>
								</div>
							</div>
								<div class="form-group">
	                                <label for="field-2" class="control-label">Email ID</label>
									<span style="color:red;">*</span>
									<span id="error_email_id" style="color:red;"></span>
	                                <input type="text" id="email_id" name="email_id" class="form-control" placeholder="Email ID" value="<?php echo $row->email_id; ?>" required/>
								</div>
								<div class="form-group">
									<label for="field-2" class="control-label">Postal Address</label>
									<textarea name="postal_address" class="form-control" placeholder="Postal Address" style="height:186px;"><?php echo $row->postal_address;?></textarea>
								</div>
						   </div>
							<div class="col-sm-4">
								<div class="form-group">
									<label for="field-2" class="control-label">Alternate Contact Details</label>
									<input type="text" class="form-control" name="alt_contact" id="alt_contact" placeholder="Alternate Contact" value="<?php echo $row->alt_contact; ?>">
								</div>
								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
										<label for="field-2" class="control-label" style="font-size:10px;">Country Code</label>
										
										<input type="text" class="form-control" name="country_code" id="country_code"  value="<?php echo $row->country_code; ?>" readonly>
										</div>
									</div>
									<div class="col-md-8">
										<div class="form-group">
										<label for="field-2" class="control-label">Contact Number</label>
										<input type="text" class="form-control" name="alt_contact_no" value="<?php echo $row->alt_contact_no;?>" placeholder="Contact Number" >
										</div>
									</div>
								</div>

								<div class="form-group">
									<label for="field-2" class="control-label">E-mail</label>
									<input type="email" class="form-control" name="email" id="email" placeholder="E-mail" value="<?php echo $row->email; ?>">
								</div>

								<div class="form-group">
									<label for="field-2" class="control-label">Remarks (If Any..) </label>
									<textarea class="form-control" name="spacification" id="spacification"  style="overflow:hidden" placeholder="Remarks"><?php echo $row->remarks; ?></textarea>
								</div>
							</div>
									 
										
											

                                              

												 <div class="col-md-3">
                                                   
                                                </div>
												
									
                                                <div class="col-md-3">
                                                  
                                                </div>

                                                <div class="col-md-3">
												
												</div>
												
												 
												<div class="col-md-3">
													
												</div>
												<div class="col-md-3">
													
                                                </div>
												
											
												<div class="col-md-3">
													
												</div>

												<div class="col-md-3">
													
												</div>

												<div class="col-md-3">
                                                   
                                                </div>
									   
									   			<div class="col-md-3">
                                                    
                                                </div>

                                                <div class="col-md-3">
                                                  
                                                </div>

                                                <div class="col-md-3">
                                                   
                                                </div>

												<div class="col-md-3">
												
												</div>
												<div class="col-md-3">
														

														</div>
											
												
												
												<div class="col-md-4">
												<!-- <div class="form-group">
														 <label for="field-2" class="control-label">Message </label>
														 <Span id="error_spacification" style="color:red;"></span>
														 <textarea class="form-control" name="message1" id="message1"  style="overflow:hidden" placeholder="Message"><?php echo $row->message; ?></textarea>
														 
													</div> -->
													
												</div>
												
												<div class="col-md-5">
												
												</div>
									
										<div class="col-md-12">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" id="saves_form" class="btn btn-success" value="Update">
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

        <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
		
<script language="javascript" type="text/javascript">   

$(document).ready(function() {


			var lead_source = $("#lead_source").val();
			var leadID = "<?php echo $id;?>";

			if(lead_source == 1 || lead_source == 2 || lead_source == 4 || lead_source == 7 || lead_source == 8 || lead_source == 11) {

				$('#assign_team').empty().append('<option value="2">Sales NBD</option>');
				getTeamMembers(2, leadID);

			} else if(lead_source == '') {
				$('#assign_team option[value=""]').attr('selected','selected');
			} else {
				$('#assign_team').empty().append('<option value="3">Sales CRR</option>');
				getTeamMembers(3, leadID);

			}
});
	
	$("#company_name").keypress(function(event) {
    var character = String.fromCharCode(event.keyCode);
    return isValidCompany(character);     
});

function isValidCompany(str) {
    return !/[~`!@#$%\^&*()+=\-\[\]\\';,/{}|\\":<>\?]/g.test(str);
}

$("#contact_person").keypress(function(event) {
    var character = String.fromCharCode(event.keyCode);
    return isValidPerson(character);     
});

function isValidPerson(str) {
    return !/[~`!@#$%\^&*()+=\-\[\]\\';,/{}|\\":<>\?]/g.test(str);
}

$("#cust_name").keypress(function(event) {
    var character = String.fromCharCode(event.keyCode);
    return isValidCustomer(character);     
});

function isValidCustomer(str) {
    return !/[~`!@#$%\^&*()+=\-\[\]\\';,/{}|\\":<>\?]/g.test(str);
}

$(function() {
  var regExp = /[0-9\.\,]/;
  $('#mobile').on('keydown keyup', function(e) {
    var value = String.fromCharCode(e.which) || e.key;
    console.log(e);
    // Only numbers, dots and commas
    if (!regExp.test(value)
      && e.which != 188 // ,
      && e.which != 190 // .
      && e.which != 8   // backspace
      && e.which != 46  // delete
      && (e.which < 37  // arrow keys
        || e.which > 40)) {
          e.preventDefault();
          return false;
    }
  });
});

$(function() {
  var regExp = /[0-9\.\,]/;
  $('#mobile_no').on('keydown keyup', function(e) {
    var value = String.fromCharCode(e.which) || e.key;
    console.log(e);
    // Only numbers, dots and commas
    if (!regExp.test(value)
      && e.which != 188 // ,
      && e.which != 190 // .
      && e.which != 8   // backspace
      && e.which != 46  // delete
      && (e.which < 37  // arrow keys
        || e.which > 40)) {
          e.preventDefault();
          return false;
    }
  });
});
</script>

<script type="text/javascript">
	
$("#saves_form").click(function() {
var create_date = $("#create_date").val();
if(create_date=='')
{
	$("#error_create_date").html('Required!');
} else {
	$("#error_create_date").html('');
}

var lead_source = $("#lead_source").val();
if(lead_source=='')
{
	$("#error_lead_source").html('Required!');
} else {
	$("#error_lead_source").html('');
}

var cust_name = $("#cust_name").val();
if(cust_name=='')
{
	$("#error_cust_name").html('Required!');
} else {
	$("#error_cust_name").html('');
}


var country = $("#country_name").val();
if(country=='')
{
	$("#error_country").html('Required!');
} else {
	$("#error_country").html('');
}


var email_id = $("#email_id").val();
if(email_id=='')
{
	$("#error_email_id").html('Required!');
} else {
	$("#error_email_id").html('');
}

var mobile_no = $("#mobile_no").val();
if(mobile_no=='')
{
	$("#error_mobile_no").html('Required!');
} else {
	$("#error_mobile_no").html('');
}

var assign_team = $("#assign_team").val();
if(assign_team=='')
{
	$("#error_assign_team").html('Required!');
} else {
	$("#error_assign_team").html('');
}

// var assign_team_member = $("#assign_team_member").val();
// if(assign_team_member=='')
// {
// 	$("#error_assign_team_member").html('Required!');
// } else {
// 	$("#error_assign_team_member").html('');
// }


var status = $("#status").val();
if(status=='')
{	
	$("#error_status").html('Required!');
} else {
	$("#error_status").html('');
}

if(create_date==''  || lead_source==''|| country=='' || cust_name=='' || email_id == '' || mobile_no=='' || status=='' || assign_team == '' || assign_team_member == '')
{
	
	return false;
}

});
</script>

<script type="text/javascript">
		function getTeamMembers(assign_team, leadID) {
		// var assign_team = $("#assign_team").val();
			$.ajax({
				type:"post",
				url:"<?php echo page_url;?>Leads/getTeamMembers",
				data: {assign_team: assign_team, leadID: leadID},
				success:function(data){
					$("#assign_team_member").html(data);
				}
				});
		}

	function getTeamViaSource() {
		var lead_source = $("#lead_source").val();

			if(lead_source == 1 || lead_source == 2 || lead_source == 4 || lead_source == 7 || lead_source == 8 || lead_source == 11) {

				$('#assign_team').empty().append('<option value="2">Sales NBD</option>');
				getTeamMembers(2);

			} else {
				$('#assign_team').empty().append('<option value="3">Sales CRR</option>');
				getTeamMembers(3);

			}
	}

	function delete_product(product_id) {
		if(confirm('Are you sure you want to delete this product?')) {
			$.ajax({
					type:"post",
					url:"<?php echo page_url;?>Leads/delete_product",
					data: {product_id: product_id},
						success:function(data){
							if (data == 1) {
								$(".products"+product_id).remove();
							// $("#assign_team_member").html(data);
							}
						}
				});
		}
	}

    function check_business() {
        var patient_type = $("#patient_type").val();
        $("#show_other_business").css('display', 'none');
        $("#other_business").removeClass('mand');

        if(patient_type == 7) {
            $("#show_other_business").css('display', '');
            $("#other_business").addClass('mand');
        }
    }

	    var j=1;

        $('.add_more').click(function(){
            getProductsOfCompany(j, 1);
            $('.productss').append('<div class="row fieldGroups"><div class="col-md-12"><div class="col-md-5"> <div class="form-group"> <label for="field-1" class="control-label">Competitor Product</label> <span style="color:red;">*</span> <input type="text" name="competitor_product[]" class="form-control"> </div></div><div class="col-md-5"> <div class="form-group"> <label for="field-1" class="control-label">Products</label> <span style="color:red;">*</span> <span id="error_product" style="color:red;"></span> <select class="form-control product_name'+j+' product_refresh" name="products[]"> </select> </div></div><div class="col-md-2"><a href="javascript:void(0)" class="btn btn-danger btn-xs remove"><span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span></a> </div></div></div>');
            j++;
        });

        $(document).on('click', '.remove', function(){
            $(this).parents(".fieldGroups").remove();
        });

    function getProductsOfCompany(j, i) {
        var company_location = $("#company_location").val();

                 $.ajax({
                    type:"post",
                    url:"<?php echo page_url;?>Leads/get_products",
                    data:{company_location:company_location},

                    success:function(data) {
                    	if(i == 1) {
                        	$(".product_name"+j).html(data);
                    	} else {
                    		$(".product_refresh").html(data);
                    	}
                    }
                });
    }

	// function getProductsOfCompany() {
	// 	     var url = "<?php echo page_url;?>Leads/get_products";

 //             $('.select3').select2({ 
 //                placeholder: 'TYPE TO SELECT',
 //                minmumInputLength:4,
 //                allowClear: true,
 //                multiple: true,
 //                    ajax: {
 //                        url: url,
 //                        dataType: 'json',
 //                        delay: 250,

 //                            data: function (params) {

 //                                return {
 //                                    searchTerm: params.term,
 //                                    company_location: $("#company_location").val()
 //                                    };

 //                                },processResults: function (data) {

 //                                    return {
 //                                        results: data
 //                                        };

 //                                    },

 //                                 cache: true

 //                                }

 //                            });
	// }
</script>

<script type="text/javascript">
	$( document ).ready(function() {
  		$('#create_date').datepicker({
		 	autoclose: true,
		 	todayHighlight: true,
		 	format: 'dd-mm-yyyy',
		 	minDate: '-7d'
		 });
	});
</script>

<script type="text/javascript">
         $( document ).ready(function() {

            $('.select2').select2({ });
                 var url = "<?php echo page_url;?>Leads/get_products";

             $('.select3').select2({ 
                placeholder: 'TYPE TO SELECT',
                minmumInputLength:4,
                allowClear: true,
                multiple: true,
                    ajax: {
                        url: url,
                        dataType: 'json',
                        delay: 250,

                            data: function (params) {

                                return {
                                    searchTerm: params.term,
                                    company_location: $("#company_location").val()
                                    };

                                },processResults: function (data) {

                                    return {
                                        results: data
                                        };

                                    },

                                 cache: true

                                }

                            });

                // $('.select3').select2({ 
                //     placeholder: 'TYPE TO SELECT',
                //     minmumInputLength:3,
                //     allowClear: true,
                //     multiple: true,

                //     ajax: {
                //       url: url,
                //       dataType: 'json',
                //       delay: 250,

                //       processResults: function (data) {
                //         return {
                //           results: data
                //         };
                //       },
                //       cache: true
                //     }

                // });
        });
        </script>
    </body>
</html>