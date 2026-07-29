<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright;?>">
		<link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
		<title><?php echo sitetitle;?> Edit Department</title>
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
		<link href="https://pms.shubhampack.in/assets/plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">

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
                           
                            <h4 class="page-title">EDIT DEPARTMENT</h4>
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
								$this->db->select('department_id, show_in_master_index,reference, business_loc_id, department,status,assign_delegation, departmenthead')->from('departments')->where('department_id',$id); 
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $department)
								?>
								
                                   <form method="post" id="loginForm" action="<?php echo page_url;?>Master/User_management/update_department/<?php echo $department->department_id;?>"  enctype="multipart/form-data">
								   <input type="hidden" name="oldfile" value="<?php echo $department->reference;?>">
										 <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Business Location</label>
														<span id="error_business_loc" style="color:red;"></span>
												<select class="form-control" id="business_loc" name="business_loc" readonly>
													<?php 
													$this->db->select('a.company_name, a.business_loc_id,a.state_id, a.city_id,b. 	state_id,b.state_name,c.city_id,c.city_name')->from('business_location a')->join('states b','a.state_id=b.state_id','left')->join('cities c','a.city_id=c.city_id','left')->where('business_loc_id',$department->business_loc_id);
													$this->db->order_by('a.company_name','asc');
													$query = $this->db->get();
													$res = $query->result();
													foreach($res as $row){
													?>
													<option value="<?php echo $row->business_loc_id;?>" <?php if($department->business_loc_id==$row->business_loc_id){echo "selected";}?>><?php echo $row->company_name;?>(<?php echo $row->state_name;?>, <?php echo $row->city_name;?>)</option>
											<?php }?>		
												</select>
												    </div>
                                                </div>
                                                
												
												
												<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">Department Name</label>
														 <span id="error_department_name" style="color:red;"></span>
														 <input type="text" class="form-control" name="department_name" id="department_name"  value="<?php echo $department->department;?>">
													</div>
												</div>
												
												
											
												<div class="col-md-4">
												<div class="form-group">
												<label for="field-2" class="control-label">Status</label>
												<span id="error_status" style="color:red;"></span>
												<select class="form-control" id="status" name="status">
													<option value="<?php echo $department->status;?>">--Select Status--</option>
													<option value="1" <?php if($department->status=='1'){echo "selected";}?>>Active</option>
													<option value="0" <?php if($department->status=='0'){echo "selected";}?>>Inactive</option>
												</select>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label>Assign Department Head</label>
													<select class="form-control" name="departmenthead" id="departmenthead" required>
														<option value="">Select Department Head</option>
														<?php 
														$q1 = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('user_status',1)->where('department_id',$this->uri->segment(4))->get();
														foreach($q1->result() as $res){

															$sel = '';
															// echo $department->departmenthead;

															if($department->departmenthead == $res->user_id){

																$sel = 'Selected';
															}
														?>
														<option value="<?php echo $res->user_id;?>" <?php echo $sel; ?>><?php echo $res->first_name." ".$res->last_name;?></option>
													<?php }?>
													</select>
												</div>
											</div>

											<div class="col-md-4">
												<div class="form-group">
													<label>Departments which the helpticket can be raise</label>
													<select name="related_department_name[]" multiple id="" class="select2" data-plugin="multiselect">
														<option value="">--Select--</option>
														<?php 
														
														$qy = $this->db->select('department, department_id')->from('departments')->where('department_id!=', $id)->get();
														
													$all =array();
														$select = '';
													foreach($qy->result() as $dept){

														$qc = $this->db->select('related_department_name')->from('multiple_department_add')->where('dept_id', $id)->get();

														foreach($qc->result() as $all_data){

														$all[] = $all_data->related_department_name;
														}
if(count($all)>0){

	if(in_array($dept->department_id, $all)){

		$select = 'Selected';

	}
}

														?>

														<option value="<?php echo $dept->department_id; ?>" <?php echo $select; ?>><?php echo $dept->department; ?></option>

														<?php }?>
													</select>


												</div>
											</div>
											
											<div class="col-md-4" style="display:none">
												<div class="form-group">
												<label for="field-2" class="control-label">Show in Master Index</label>
												<span id="error_status" style="color:red;"></span>
												<select class="form-control" id="show_in_master_index" name="show_in_master_index">
													<option value="<?php echo $department->status;?>">--Select an Option--</option>
													<option value="1" <?php if($department->show_in_master_index=='1'){echo "selected";}?>>Yes</option>
													<option value="0" <?php if($department->show_in_master_index=='0'){echo "selected";}?>>No</option>
												</select>
												</div>
											</div>
											
												<div class="col-md-3" style="display:none">
											    <div class="form-group">
											 <label>Show in Delegation </label>
											        <select class="form-control" name="assign_delegation" id="assign_delegation">
											            <option value="1" <?php if($department->assign_delegation=='1'){echo "selected";} ?>>Yes</option>
											            <option value="0" <?php if($department->assign_delegation=='0'){echo "selected";} ?>>No</option>
											        </select>
											    </div>
											</div>
                                            </div>
										<div class="col-md-9"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" id="depupdate" class="btn btn-success" value="Update">
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

		<script>
		$(document).ready(function() {
			
			$('.select2').select2({ });
		});
		</script>
		 <script src="https://pms.shubhampack.in/assets/plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
	   $("#depupdate").attr('disabled',false);
	   $("#depupdate").val('Update');
  $("#loginForm").on("submit", function(){
   // $("#pageloader").fadeIn();
   $("#depupdate").attr('disabled',true);
     $("#depupdate").val('Please Wait...');
  });//submit
});//document ready
</script>
       

        <script>
$( document ).ready(function() {
$('#example').dataTable({
"bProcessing": true,
"pagination":true,
"sAjaxSource": "<?php echo page_url;?>Master/Business_location/business_loc_listing",
"aoColumns": [
				{ mData: 'sr_no' } ,
				{ mData: 'country_name' },
				{ mData: 'state_name' },
				{ mData: 'city_name' },
				{ mData: 'company_name' },
				{ mData: 'address' },
				{ mData: 'contact_number' },
				{ mData: 'status' },
				{ mData: 'edit' }
				
		]
});   
});

</script>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script language="javascript" type="text/javascript">   
jQuery.noConflict();
$(document).ready(function() {
$("#save").click(function() {
var country_name = $("#country_name").val();
if(country_name=='')
{
	$("#error_name").html('Required!');
}
var state = $("#state").val();
if(state=='')
{
	
	$("#error_state").html('Required!');
}

var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}

var city_name = $("#city_name").val();
if(city_name=='')
{
	
	$("#error_city").html('Required!');
}

var company_name = $("#company_name").val();
if(company_name=='')
{
	
	$("#error_company").html('Required!');
}

var address = $("#address").val();
if(address=='')
{
	
	$("#address_error").html('Required!');
}

var contact_number = $("#contact_number").val();
if(contact_number=='')
{
	
	$("#contact_error").html('Required!');
}

if(country_name=='' || state=='' || city_name=='' || company_name=='' || address=='' || contact_number=='' )
{
	
	return false;
}

});
});
</script>


    </body>
</html>