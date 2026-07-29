<?php date_default_timezone_set('Asia/Kolkata');

$uri=$this->uri->segment(3);

$name='';

$email='';

$mobile='';

$company='';

$leadcamefrom='';

$remarks='';

$apiid='';

$location='';
$title='';

if($uri<>'')

{

$leadfrom=$this->uri->segment(4);
if($leadfrom==2)
{
$rest=$this->db->select('title,name,email,mobile,company,source,remarks,location')->from('import_leads')->where('id',$uri)->get();
}else
{
$rest=$this->db->select('title,name,email,mobile,company,source,remarks,location')->from('web_to_api_leads')->where('id',$uri)->get();	
}



if($rest->num_rows()>0)

{

	foreach($rest->result() as $restt);



	$name=$restt->name;

	$email=$restt->email;

	$mobile=$restt->mobile;

	$company=$restt->company;

	$leadcamefrom=$restt->source;
	$title=$restt->title;

	$remarks=$restt->remarks;

	$apiid=$uri;

    $location=$restt->location;

	

}



}



$CI =& get_instance();

$CI->load->model('Salescrm_model', 'salescrm');

$DI =& get_instance();

$DI->load->model('Dashboard_model');

$getTeams = $CI->salescrm->getTeams();

// $mode=$DI->Dashboard_model->getsettings();
// if(count($mode)>0)
// {
// 	$mode=$mode['mode'];
// }else
// {
// 	$mode=1;
// }

$checkRolePermission = $CI->salescrm->checkRolePermission($_SESSION['logged_in']['user_id'], 47);

if ($checkRolePermission != '') {
    foreach ($checkRolePermission as $row);
    $madd = $row->madd;
    $mremove = $row->mremove;
} else {
    $madd = '';
    $mremove = '';
}

$checkRolePermissionForAddLead = $CI->salescrm->checkRolePermission($_SESSION['logged_in']['user_id'], 46);

if ($checkRolePermissionForAddLead != '') {
    foreach ($checkRolePermissionForAddLead as $row1);
    $madd1 = $row1->madd;
    $mremove1 = $row1->mremove;
} else {
    $madd1 = '';
    $mremove1 = '';
}

?>

<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="<?php echo copyright;?>">



        <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">



        <title><?php echo sitetitle;?></title>



        <!-- Table Responsive css -->

		<script src="<?php echo assets_url;?>js/angular.min.js"></script>

		 <!-- DataTables -->

        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>plugins/datatables/fixuedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />

		<link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" >

        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />

		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 

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

<?PHP 

$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
if($q->num_rows()>0)
{
foreach($q->result() as $LOGO);
$color=$LOGO->colorcode;
}else
{
	$color='';
}

?>

<style>

            table.pretty thead th {

                text-align: center;

                background: <?php echo $color;?>;

                color:#fff;

				font-size:12px;

            }

			table.pretty td {

                text-align: center;

                font-size:12px;

            }

			.feedback {

  background-color : <?php echo $LOGO->colorcode;?>;

  color: white;

  padding: 10px 20px;

  border-radius: 4px;

  border-color: #46b8da;

}



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

			</style>

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

                            <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success" name="action_button"><i class="fa fa-arrow-left"></i>Back</button></a>
                            
                             <?php if($madd1 == 1 && $mremove1 == 0) {?>
						 <!-- 	<div class="btn-group pull-right">

						  		<button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">Add New Lead</button>

						  	
                            </div> -->
                        	<?php } ?>

                        </div>

                    </div>

                </div>

               



                <div class="row" style="margin-top:20px;">

                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">

                        <div class="page-title-box">                           

                            <h4 class="page-title text-center">&nbsp Lead List</h4>

                        </div>

                    </div>

                </div>

<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>



                 <div class="row">

                    <div class="col-sm-12">

                        <div class="card-box table-responsive">

                            <form method="post" action="<?php echo page_url;?>Leads/lead_assign_to_team" id="frm" onsubmit="return validate();">



							<table id="example" class="table table-striped table-bordered pretty">

                                <thead>

                                 <tr>
                                     <th>Sr No.</th>
                                     <th>Query No/Date</th>
                                     <th>Customer Type</th>
                                     <th>Lead Source</th>
                                     <th>Company</th>
                                     <th>Customer Name</th>                                  
                                     <th>Products</th>
                                     <th>Primary Contact</th>
                                     <th>Alternate Contact</th>
                                     <th>Address</th>
                                 

                                     <th>Lead Remarks</th>
                                    
                                     <th>Lead Manager</th>
                                     <th>Current Status</th>
                                     <th>Last Updated On</th>
                                     <th>Update Progress</th>
                                     <th>Edit</th>
                                          
                                </tr>

                                </thead>

                                

                            </table>

							</form>

                        </div>

                    </div>

                </div>

                <!-- end row -->

 <div id="con-close-modal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <form id="loginForm" method="post" action="<?php echo page_url;?>Leads/add_leads" enctype="multipart/form-data" onsubmit="return validate_lead()">
 	<input type="hidden" name="apiid" value="<?php echo $apiid;?>">
        <div class="modal-dialog modal-full">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title">Add New Lead</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="field-1" class="control-label">Create Date</label>
                                <span style="color:red;">*</span>
                                <span id="error_create_date" style="color:red;"></span>
                                <input type="text" id="create_date" name="create_date" class="form-control" value="<?php echo date('d-m-Y');?>" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="field-1" class="control-label">Lead Source</label>
    							<span style="color:red;">*</span>
    							<span id="error_lead_source" style="color:red;"></span>
                                <select class="form-control" id="lead_source" name="lead_source">
                                    <option value="">--Select Lead Source--</option>
                                    <?php $query = $this->db->select('source_id, lead_source, status')->from('lead_source')->where('status','1')->get();
                                    foreach($query->result() as $lead_source){

                                    $a='';

                                    if($leadcamefrom==$lead_source->source_id)

                                    {

                                    $a="selected";



                                    }

                                    ?>													

                                    <option value="<?php echo $lead_source->source_id;?>" <?php echo $a;?>> <?php echo $lead_source->lead_source;?> </option>	

                                    <?php }?>	

                                </select>
                            </div>
                        </div>

						<!-- <div class="col-md-2">
							<div class="form-group">
								 <label for="field-2" class="control-label">Assign to Team</label>
								  <span style="color:red;">*</span>
								  <span id="error_assign_team" style="color:red;"></span>
    								 <select class="form-control" name="assign_team" id="assign_team" onchange="getTeamMembers()">
    								 	<option value="">Select Team</option>

    								 	<?php if(count($getTeams) > 0) {
    								 		foreach($getTeams as $teams) {?>
    								 	 <option value="<?php echo $teams->team_id;?>"><?php echo $teams->team_name;?></option>

    								 <?php } } ?>
    								 </select>
							</div>
						</div>
						<div class="col-md-2">
						  <div class="form-group">
							 <label for="field-2" class="control-label">Assign to Team Member</label>
							 <span style="color:red;">*</span>
							 <span id="error_assign_team_member" style="color:red;"></span>
							 <select class="form-control" name="assign_team_member" id="assign_team_member"></select>
						  </div>
                        </div> -->
                        <div class="col-md-2" style="display:none;">
                            <label for="field-2" class="control-label">Send Whatsapp?</label><br>
                            <input type="checkbox" name="send_whatsapp" value="0" checked>
                        </div>
                    </div>

										
                    <div class="row">
                        <div class="col-md-4" style="border-right:1px solid lightgrey;">
                            <div class="form-group">
                                <label for="field-1" class="control-label">Company Name</label>
						        <input type="text" id="company_name" name="company_name" placeholder="Company Name" class="form-control" autocomplete="off" value="<?php echo $company;?>">
                            </div>

                            <div class="form-group">
                                <label for="field-1" class="control-label">Nature of Business</label>
                                <select class="form-control" id="patient_type" name="patient_type">
                                    <option value="">--Select Nature of Business--</option>	
                                    <?php 
                                    $this->db->select('*')->from('patient_type')->where('status','1');
                                    $query = $this->db->get();
                                    $res = $query->result();

                                    foreach($res as $patient_type){?>	
                                    <option value="<?php echo $patient_type->patient_id;?>"> <?php echo $patient_type->patient_type;?> </option>	

                                    <?php }?>

                                </select>
                            </div>

                            <div class="form-group">
                                <label for="field-1" class="control-label">Company Location</label>
                                    <select class="form-control" id="company_location" name="company_location" onchange="getProductsOfCompany(0)">
                                        <option value="">--Select Company Location--</option>
                                 <?php          $sql3 = $this->db->select('id, companyname')
                                                                 ->from('store_rack_location')
                                                                 ->get();

                                        if($sql3->num_rows() >  0) {
                                        foreach($sql3->result() as $row3) {?>   

                                        <option value="<?php echo $row3->id;?>"> <?php echo $row3->companyname;?> </option>    

                                     <?php } }?>

                                    </select>
                            </div>
                            <div class="row productss">
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
                                        <select class="form-control product_name0" name="products[]">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <a href="javascript:void(0)" class="btn btn-warning btn-xs add_more"><span class="glyphicon glyphicon glyphicon-plus" aria-hidden="true"></span></a>
                                </div>
                            </div>



												<div class="form-group">

                                                        <label for="field-1" class="control-label"> Company Website</label>

															<input type="web" id="" name="website" placeholder="www.example.com" class="form-control" autocomplete="off">

                                                </div>



												<div class="form-group" style="display: none;">

                                                        <label for="field-1" class="control-label">Country</label>

														<span style="color:red;">*</span>

														<span id="error_country" style="color:red;"></span>

												        <input type="hidden" name="country_name" id="country_name" value="101">											 

                                                    </div>






												<div class="form-group">

												<label for="field-2" class="control-label">City/Area</label>

												<input type="text" id="city" name="city" class="form-control" placeholder="City" autocomplete="off" value="<?php echo $location;?>"/>

												</div>
											   

											    </div>

										

												

												 <div class="col-md-4" style="border-right:1px solid lightgrey;">
												 	<div class="row">
												 		<div class="col-md-4">
												 	<div class="form-group">

                                                        <label for="field-2" class="control-label">Title</label>

														<span style="color:red;">*</span>

														<span id="error_title" style="color:red;"></span>

														<select class="form-control" name="title" id="title" required="">
														<option value="Mr.">Mr.</option>
														<option value="Mrs.">Mrs.</option>
														<option value="Miss.">Miss.</option>
														</select> 

														</div>
													</div>
													<div class="col-md-8">
												 <div class="form-group">

                                                        <label for="field-2" class="control-label">Customer Name(Decision Maker)</label>

														<span style="color:red;">*</span>

														<span id="error_cust_name" style="color:red;"></span>

                                                        <input type="text" id="cust_name" name="cust_name" class="form-control" placeholder="Customer Name" value="<?php echo $name;?>" autocomplete="off"/>

														</div>
													</div>
													</div>

														<div class="row">

														<div class="col-md-4">

															<div class="form-group">

															<label for="field-2" class="control-label" style="font-size:10px;">Country Code</label>

															<input type="text" class="form-control" name="country_code" value="91" readonly>

															</div>

														</div>

														<div class="col-md-8">

															<div class="form-group">

															<label for="field-2" class="control-label">Contact Number</label>

															<span style="color:red;">*</span>

															<span id="error_mobile_no" style="color:red;"></span>

															<input type="text" id="mobile_no" name="mobile_no" class="form-control" value="<?php echo $mobile;?>" placeholder="Contact No" autocomplete="off"/>

															</div>



														</div>

                                                        <input class="form-control" name="message1" id="message1"  style="overflow:hidden" type="hidden">

                                                        

														

														

													</div>



													<div class="form-group">

                                                        <label for="field-2" class="control-label">Email ID</label>

                                                        <span style="color:red;">*</span>

														<span id="error_email_id" style="color:red;"></span>

                                                        <input type="text" id="email_id" name="email_id" class="form-control" placeholder="Email ID" value="<?php echo $email;?>" autocomplete="off"/>

														</div>



														<!-- <div class="form-group">

                                                        <label for="field-2" class="control-label">Single Window Contact Person(Client)</label>

														<span style="color:red;">*</span>

														<span id="error_contact_person" style="color:red;"></span>

                                                        <input type="text" id="contact_person" name="contact_person" class="form-control" placeholder="Contact Person" autocomplete="off"/>

														</div> -->



														<div class="form-group">

															<label for="field-2" class="control-label">Postal Address</label>

															

															<textarea name="postal_address" id="postal_address" class="form-control" placeholder="Postal Address" autocomplete="off" style="height:186px;"></textarea>

														</div>



													

                                                </div>										

												 

												

                                               

                                                <div class="col-md-4">

												<div class="form-group">

														 <label for="field-2" class="control-label">Alternate Contact Details</label>

														 <input type="text" class="form-control" name="alt_contact" id="alt_contact" placeholder="Alternate Contact" autocomplete="off">

													</div>



													<div class="row">

														<div class="col-md-4">

															<div class="form-group">

															<label for="field-2" class="control-label" style="font-size:10px;">Country Code</label>

															<input type="text" class="form-control country_code" name="country_code" id="country_code" readonly>

															</div>

														</div>

														<div class="col-md-8">

															<div class="form-group">

															<label for="field-2" class="control-label">Contact Number</label>

															

															<input type="text" class="form-control" name="alt_contact_no" placeholder="Contact Number" autocomplete="off">

															</div>



														</div>

                                                        <input class="form-control" name="message1" id="message1"  style="overflow:hidden" type="hidden">

                                                        

														

														

													</div>



													<div class="form-group">

														 <label for="field-2" class="control-label">E-mail</label>

														 <input type="email" class="form-control" name="email" id="email" placeholder="E-mail" autocomplete="off">

													</div>



													<div class="form-group">

														 <label for="field-2" class="control-label">Remarks (If Any..) </label>

														 <?php if($remarks == '') {

                                                            $r = '';

                                                        } else {

                                                            $r = 'readonly';

                                                        }?>

														 <textarea class="form-control" name="spacification" id="spacification" placeholder="Enter Remarks" autocomplete="off" style="height:187px;" <?php echo $r;?>><?php echo $remarks;?></textarea>

														 

														</div>



													

                                                </div>

												

												

												

												



                                                        <!-- <div style="clear:both; height:20px"></div> -->

												

											

                                            </div>

											

                                        </div>

                                        <div class="modal-footer">

                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

                                            <input type="submit" id="saves_form" class="btn btn-info" value="Submit"> 

                                        </div>

                                    </div>

                                </div>

								</form>

                            </div><!-- /.modal -->

							

							

							<div id="filter-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

 <form id="loginForm" method="post" action="<?php echo page_url;?>Leads/filter_by_date_category">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">Filter by Date</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">

											<div class="col-md-4">

												   <div class="form-group">

                                                        <label for="field-1" class="control-label">Customer Type</label>

														<span id="error_patient_type" style="color:red;">*</span>

												<select class="form-control" id="patient_type" name="patient_type">

													<option value="">All</option>	

													<?php 

													$this->db->select('*')->from('patient_type')->where(' 	status','1');

													$query = $this->db->get();

													$res = $query->result();

													foreach($res as $patient_type){?>	

													<option value="<?php echo $patient_type->patient_id;?>"> <?php echo $patient_type->patient_type;?> </option>	

								<?php }?>

												</select>

                                                </div>

											</div>

											 <div class="col-md-4">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">From</label>

														<span style="color:red;">*</span>

														<span id="error_date_from" style="color:red;"></span>

												<input type="text" id="date_from" name="from_date" class="form-control" autocomplete="off">

                                                </div>

                                                </div>

												

												<div class="col-md-4">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">To</label>

														<span style="color:red;">*</span>

														<span id="error_date_to" style="color:red;"></span>

												<input type="text" id="date_to" name="to_date" class="form-control" autocomplete="off">

                                                </div>

                                                </div>

												

                                            </div>

											

                                        </div>

                                        <div class="modal-footer">

                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

                                            <input type="submit" id="save_filter" class="btn btn-info" value="Submit"> 

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

<script>



$( document ).ready(function() {

$('#example').dataTable({

"bProcessing": true,

"pagination":true,

"deferRender": true,

"pageLength": 100,

"stateSave": true,

"sAjaxSource": "<?php echo page_url;?>Leads/Lead_list/<?php echo base64_encode(current_url());?>",

"aoColumns": [

			 { mData: 'sr_no' } ,
                { mData: 'unique' },
                { mData: 'customer_type' },
                { mData: 'leadsource' },
                { mData: 'company' },
                { mData: 'customer_name' },                
                { mData: 'products' }, 
                { mData: 'mobile' },               
                { mData: 'alternatedetail' },
                { mData: 'address' },

              
                { mData: 'lastremarks' },
                { mData: 'leadmanager'},
                { mData: 'current_status'},
                { mData: 'lastupdatedon'},
                 { mData: 'update'} ,
                  { mData: 'edit'} 
               
				

		]

});   



			// var lead_source = $("#lead_source").val();



			// if(lead_source == 1 || lead_source == 2 || lead_source == 4 || lead_source == 7 || lead_source == 8 || lead_source == 11) {



			// 	$('#assign_team').empty().append('<option value="2">Sales NBD</option>');

			// 	getTeamMembers(2);



			// } else if(lead_source == '') {

			// 	$('#assign_team option[value=""]').attr('selected','selected');

			// } else {

			// 	$('#assign_team').empty().append('<option value="2">Sales NBD</option>');

			// 	getTeamMembers(3);



			// }

});



</script>



<script language="javascript" type="text/javascript">   

        

$(document).ready(function() {

	var ur="<?php echo $uri;?>";

if(ur!='')

{

	autofilldata();	

}

 jQuery('#create_date').datepicker({

 	autoclose: true,

 	todayHighlight: true,

 	format: 'dd-mm-yyyy'

 });

 jQuery('#date_from').datepicker({

 	autoclose: true,

 	todayHighlight: true,

 	format: 'dd-mm-yyyy'

 });

 jQuery('#date_to').datepicker({

 	autoclose: true,

 	todayHighlight: true,

 	format: 'dd-mm-yyyy'

 });

});

</script>

<script>

	

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

var product = $("#product").val();

if(product=='')

{

	$("#error_product").html('Required!');

} else {

	$("#error_product").html('');

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



var assign_team_member = $("#assign_team_member").val();

if(assign_team_member=='')

{

	$("#error_assign_team_member").html('Required!');

} else {

	$("#error_assign_team_member").html('');

}





var status = $("#status").val();

if(status=='')

{	

	$("#error_status").html('Required!');

} else {

	$("#error_status").html('');

}



if(create_date==''  || lead_source==''|| country=='' || cust_name=='' || email_id == '' || mobile_no=='' || status=='' || assign_team == '' || assign_team_member == '' || prroduct=='')

{

	

	return false;

}



});

function display_qtybox(id){

if($('#pick_items'+id).is(":checked")){



	$("#teamleader"+id).attr('required',true);

	$('#teamleader'+id).css('display','block');

	$('#teamleader'+id).attr('disabled',false);

} else{

	

	$('#teamleader'+id).css('display','none');

		$("#teamleader"+id).attr('required',false);

		$('#teamleader'+id).attr('disabled',true);

}

                

}

	

	 function validate() {

     $("#saves").attr('disabled',false);        

      $("#saves").val('Submit');



   var checkeditem = $('#frm input:checked').length;

       if(checkeditem > 0) {

            $("#saves").attr('disabled',true);        

             $("#saves").val('Submit');

           return true;

       } else {

        $("#saves").attr('disabled',false);

        $("#saves").val('ISSUE ITEMS');

       alert('Assign lead to at least one team');

       return false;

   } 



}

	

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

	

	$("#mobile").keypress(function(event) {

    var character = String.fromCharCode(event.keyCode);

    return isValidMobile(character);     

});



function isValidMobileNo(str) {

    return !/[~`!@#$%\^&*()+=\-\[\]\\';,/{}|\\":<>\?]/g.test(str);

}

	

	$("#mobile_no").keypress(function(event) {

    var character = String.fromCharCode(event.keyCode);

    return isValidMobileNo(character);     

});



function isValidMobile(str) {

    return !/[~`!@#$%\^&*()+=\-\[\]\\';,/{}|\\":<>\?]/g.test(str);

}



$("#save_filter").click(function() {

	var date_from = $("#date_from").val();

		if(date_from=='')

		{

			$("#error_date_from").html('Required!');

		} else {

			$("#error_date_from").html('');

		}



	var date_to = $("#date_to").val();

		if(date_to=='')

		{

			$("#error_date_to").html('Required!');

		} else {

			$("#error_date_to").html('');

		}



		if(date_from=='' || date_to=='')

			{

				

				return false;

			}

	});





	function validate_lead() {

		 $("#saves_form").attr('disabled',false);

		 $("#saves_form").val('Submit');

		 $("#form :input").attr('required',false);



			var isValid=0;



				$("#form .mand").each(function() {

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

		     alert('All Fields are mandatory');

     		return false;

 		}



	}



	function getTeamMembers() {
		var assign_team = $("#assign_team").val();
			$.ajax({
				type:"post",
				url:"<?php echo page_url;?>Leads/getTeamMembers",
				data: {assign_team: assign_team},
				success:function(data){
					$("#assign_team_member").html(data);
				}
			});

		}

        // function getProductsOfCompany() {
        //     var assign_team = $("#assign_team").val();
        //     $.ajax({
        //         type:"post",
        //         url:"<?php echo page_url;?>Leads/getTeamMembers",
        //         data: {assign_team: assign_team},
        //         success:function(data){
        //             $("#assign_team_member").html(data);
        //         }
        //     });
        // }



	// function getTeamViaSource() {

	// 	var lead_source = $("#lead_source").val();



	// 		if(lead_source == 1 || lead_source == 2 || lead_source == 4 || lead_source == 7 || lead_source == 8 || lead_source == 11) {



	// 			$('#assign_team').empty().append('<option value="2">Sales NBD</option>');

	// 			getTeamMembers(2);



	// 		} else {

	// 			$('#assign_team').empty().append('<option value="3">Sales CRR</option>');

	// 			getTeamMembers(3);



	// 		}

	// }







function autofilldata()

{



$("#con-close-modal").modal('show');



}



function deleteLead(id) {

        if(confirm('Are you sure you want to delete this lead?')) {

            $.ajax({

                    type:"post",

                    url:"<?php echo page_url;?>Leads/deleteLead",

                    data:{id:id},

                    success:function(data){

                        if (data == 1) {

                            location.reload();

                        }

                    }

                });

        }

}
            var j=1;

            $('.add_more').click(function(){
                getProductsOfCompany(j);
            $('.productss').append('<div class="row fieldGroups"><div class="col-md-12"><div class="col-md-5"> <div class="form-group"> <label for="field-1" class="control-label">Competitor Product</label> <span style="color:red;">*</span> <input type="text" name="competitor_product[]" class="form-control"> </div></div><div class="col-md-5"> <div class="form-group"> <label for="field-1" class="control-label">Products</label> <span style="color:red;">*</span> <span id="error_product" style="color:red;"></span> <select class="form-control product_name'+j+'" name="products[]"> </select> </div></div><div class="col-md-2"><a href="javascript:void(0)" class="btn btn-danger btn-xs remove"><span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span></a> </div></div></div>');
            j++;
            });

            $(document).on('click', '.remove', function(){
                $(this).parents(".fieldGroups").remove();
            });

    function getProductsOfCompany(j) {
        var company_location = $("#company_location").val();

                 $.ajax({
                    type:"post",
                    url:"<?php echo page_url;?>Leads/get_products",
                    data:{company_location:company_location},

                    success:function(data) {
                        $(".product_name"+j).html(data);
                    }
                });
    }




</script>



<script>

        

        $( document ).ready(function(){



        $.ajax({

            url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/43',

            type: 'get',

            success: function(data){

              $('#warm_lead').html(data);

            }

          });



        $.ajax({

            url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/44',

            type: 'get',

            success: function(data){

              $('#pisleads').html(data);

            }

          });



        $.ajax({

            url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/49',

            type: 'get',

            success: function(data){

              $('#nonqualified').html(data);

            }

          });



          $.ajax({

            url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/50',

            type: 'get',

            success: function(data){

              $('#total_leads').html(data);

            }

          });





            $.ajax({

            url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/51',

            type: 'get',

            success: function(data){

              $('#bomleads').html(data);

            }

          });     

      

    

    

    });

</script>





        <script type="text/javascript">

         $( document ).ready(function() {



            $('.select2').select2({ });

            // var url = "<?php echo page_url;?>Leads/get_products";
            // // var city_url="<?php echo page_url;?>BOM/getCities";

            //  $('.select3').select2({ 
            //     placeholder: 'TYPE TO SELECT',
            //     minmumInputLength:4,
            //     allowClear: true,
            //     multiple: true,
            //         ajax: {
            //             url: url,
            //             dataType: 'json',
            //             delay: 250,

            //                 data: function (params) {

            //                     return {
            //                         searchTerm: params.term,
            //                         company_location: $("#company_location").val()
            //                         };

            //                     },processResults: function (data) {

            //                         return {
            //                             results: data
            //                             };

            //                         },

            //                      cache: true

            //                     }

            //                 });




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