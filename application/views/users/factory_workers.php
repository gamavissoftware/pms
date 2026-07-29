<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="<?php echo copyright;?>">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title><?php echo sitetitle;?> Factory Workers List</title>



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
				table.manglesh tbody td {
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

            <div class="container">



                <!-- Page-Title -->

                <div class="row" style="margin-top:20px;">

                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">

                        <div class="page-title-box">

						 <div class="btn-group pull-right">

						  <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">ADD NEW WORKER</button>

                               

                            </div>

                           

                            <h4 class="page-title">FACTORY WORKER LIST</h4>

                        </div>

                    </div>

                </div>

                <!-- end page title end breadcrumb -->

<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>




				<!-- Modal -->
				<div id="statusremarks" class="modal fade" role="dialog">
				<div class="modal-dialog">

				<form action="<?php echo page_url;?>User/change_worker_status" method="post">
					<input type="hidden" name="worker_id" id="worker_id">
					<input type="hidden" name="worker_status" id="worker_status">
				<!-- Modal content-->
				<div class="modal-content">
				<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"><span id="statustype"></span></h4>
				</div>
				<div class="modal-body">
				<div class="row">
					
					<div class="col-md-12">
						<div class="form-group">
							<label>User</label>
						<input type="text" name="readonly" id="user" value="" class="form-control" readonly>
					</div>
				</div>


					<div class="col-md-12">
						<div class="form-group">
							<label>Remarks</label>
					<textarea class="form-control" name="remarks" placeholder="Enter Remarks"></textarea>
				</div>
					</div>

				</div>
				</div>
				<div class="modal-footer">
				<input type="submit" class="btn btn-success">
				</div>
				</div>
				</form>
				</div>
				</div>







				<!-- LOCATION POPUP -->
				<!-- Modal -->
				<div id="locationchange" class="modal fade" role="dialog">
				<div class="modal-dialog">

				<form action="<?php echo page_url;?>User/change_worker_location" method="post">
					<input type="hidden" name="worker_id_for_location" id="worker_id_for_location">
					<input type="hidden" name="worker_location" id="worker_location">
				<!-- Modal content-->
				<div class="modal-content">
				<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Location Transfer Request for <span id="personname"></span></h4>
				</div>
				<div class="modal-body">
				<div class="row">
					
					<div class="col-md-12">
						<div class="form-group">
							<label>Current Location <span style="color:red;">*</span><</label>
						<input type="text" name="readonly" id="current_location" value=""  required class="form-control" readonly>
					</div>
				</div>


					<div class="col-md-12">
						<div class="form-group">
						<label>Choose Transfer Location <span style="color:red;">*</span></label>
					<select name="transfer_location" class="form-control" required id="transfer_location"></select>
				</div>
					</div>

						<div class="col-md-12">
						<div class="form-group">
							<label>Reason for Transfer (If any)</label>
					<textarea class="form-control" name="transfer_remarks" placeholder="Enter Remarks"></textarea>
				</div>
					</div>

				</div>
				</div>
				<div class="modal-footer">
				<input type="submit" class="btn btn-success">
				</div>
				</div>
				</form>
				</div>
				</div>
				<!--- END -->


                 <div class="row">

                    <div class="col-sm-12">

                        <div class="card-box table-responsive">

                            <table id="example" class="table table-striped table-bordered manglesh">

                                <thead>

                                <tr>

									<th>Sr No.</th>
									<th>Image</th>
									<th>Worker Name</th>
									<th>Worker Name (As per ID)</th>
									<th>Working Location</th>
									<th>Mobile</th>
									<th>DOJ</th>
									<th>DOB</th>
									<th>DOCUMENT UPLOADED</th>
									<th>Status</th>
									<th>Added On</th>
									<th>Added By</th>
									<th>Edit</th>
									<th>Location Change Request</th>
                                  

                                </tr>

                                </thead>

                                

                            </table>

                        </div>

                    </div>

                </div>

                <!-- end row -->



							

							

							

							<div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

 <form id="loginForm" method="post" action="<?php echo page_url;?>User/add_workers"  enctype="multipart/form-data">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">Add New Worker</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">

                                            		<div class="col-md-6">

													<div class="form-group">

														 <label for="field-2" class="control-label">Worker Name <span style="color: red">*</span></label>

														 <span id="error_department_name" style="color:red;"></span>

														 <input type="text" class="form-control" name="name" id="name"  value="" required>

													</div>

												</div>

												<div class="col-md-6">

													<div class="form-group">

														 <label for="field-2" class="control-label">Worker Name as per ID <span style="color: red">*</span></label>

														 <span id="error_department_name" style="color:red;"></span>

														 <input type="text" class="form-control" name="name_id" id="name_id"  value="" required>

													</div>

												</div>


                                                <div class="col-md-6">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">Location <span style="color: red">*</span></label>

														<span id="error_business_loc" style="color:red;"></span>

												<select class="form-control" id="location" name="location" required>

													<option value="">--Select Location--</option>
												<?php 
													$a=$this->db->select('id,rack_location')->from('store_rack_location')->get();
													if($a->num_rows()>0)
													{
														foreach($a->result() as $as)
														{
														?>
													<option value="<?php echo $as->id;?>"><?php echo $as->rack_location;?></option>
													<?php
														}

													}
	 													?>

												</select>

												    </div>

                                                </div>


                                                	<div class="col-md-6">

													<div class="form-group">

														 <label for="field-2" class="control-label">Mobile <span style="color: red">*</span></label>

														 <span id="error_department_name" style="color:red;"></span>

														 <input type="number" maxlength="10" class="form-control" name="mobile" id="mobile"  value="" required  data-mask="(999) 999-9999">

													</div>

												</div>


													<div class="col-md-6">

													<div class="form-group">

														 <label for="field-2" class="control-label">Date Of Joining<span style="color: red">*</span></label>

														 <span id="error_department_name" style="color:red;"></span>

														 <input type="date" class="form-control" name="doj" id="doj"  value="" required>

													</div>

												</div>


													<div class="col-md-6">

													<div class="form-group">

														 <label for="field-2" class="control-label">Salary <span style="color: red">*</span></label>

														 <span id="error_department_name" style="color:red;"></span>

														 <input type="text" class="form-control" name="salary" id="salary"  value="" required>

													</div>

												</div>

													<div class="col-md-6">

													<div class="form-group">

														 <label for="field-2" class="control-label">DOB <span style="color: red">*</span></label>

														 <span id="error_department_name" style="color:red;"></span>

														 <input type="date" class="form-control" name="dob" id="dob"  value="" required>

													</div>

												</div>
                                                		

                                                	<div class="col-md-6">

													<div class="form-group">

														 <label for="field-2" class="control-label">Document Type <span style="color: red">*</span></label>

														 <span id="error_department_name" style="color:red;"></span>

														 <input type="text" class="form-control" name="type" id="type"  value="" required>

													</div>

												</div>	


													<div class="col-md-6">

													<div class="form-group">

														 <label for="field-2" class="control-label">Upload Document <span style="color: red">*</span></label>

														 <span id="error_department_name" style="color:red;"></span>

														 <input type="file" class="form-control" name="document" id="document"  value="" required>

													</div>

												</div>	


													<div class="col-md-6">

													<div class="form-group">

														 <label for="field-2" class="control-label">Passport Size Image </label>

														 <span id="error_department_name" style="color:red;"></span>

														 <input type="file" class="form-control" name="pass_image" id="pass_image"  value="" required>

													</div>

												</div>	
												

												<div class="col-md-6">

												<div class="form-group">

												<label for="field-2" class="control-label">Status <span style="color: red">*</span></label>

												<span id="error_status" style="color:red;"></span>

												<select class="form-control" id="status" name="status" required>

													<option value="">--Select Status--</option>

													<option value="1">Active</option>

													<option value="0">Inactive</option>

												</select>

												</div>

											</div>

                                            </div>

											

											

                                        </div>

                                        <div class="modal-footer">

                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

                                            <input type="submit" id="depsave" class="btn btn-info" value="Submit"> 

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

$(document).ready(function(){

	   $("#depsave").attr('disabled',false);

	   $("#depsave").val('submit');

  $("#loginForm").on("submit", function(){

   // $("#pageloader").fadeIn();

   $("#depsave").attr('disabled',true);

     $("#depsave").val('Please Wait...');

  });//submit

});//document ready

</script>  



        <script>

$( document ).ready(function() {

$('#example').dataTable({

"bProcessing": true,

	fixedHeader: true,

"pagination":true,

"sAjaxSource": "<?php echo page_url;?>User/workers_data",

"aoColumns": [

				{ mData: 'sr_no' } ,
				{ mData: 'passport_image' } ,
				{ mData: 'name' },
				{ mData: 'name_per_id' },
				{ mData: 'location' },
				{ mData: 'mobile' },
				{ mData: 'doj' },
				{ mData: 'dob' },
				{ mData: 'document_uploaded' },
				{ mData: 'status' },
				{ mData: 'addedon' },
				{ mData: 'addedby' },
				{ mData: 'edit' },
				{ mData: 'locationchange' }

                                  
				

				

		]

});   

});



</script>

		

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<script language="javascript" type="text/javascript">   

jQuery.noConflict();

$(document).ready(function() {

$("#depsave").click(function() {

var business_loc = $("#business_loc").val();

if(business_loc=='')

{

	$("#error_business_loc").html('Required!');

}

var department_name = $("#department_name").val();

if(department_name=='')

{

	

	$("#error_department_name").html('Required!');

}



var status = $("#status").val();

if(status=='')

{

	

	$("#error_status").html('Required!');

}





if(business_loc=='' || department_name==''|| status=='' )

{

	

	return false;

}



});

});

function add_remarks(id,status)
{

	


	if(status==0)
	{
		var a="Account Activation Request";
		var sta="1";
	}else
	{
		var a="Account Deactivation Request";
		var sta=0;
	}
	$("#statusremarks").modal('show');

	$("#statustype").html(a);
	$("#worker_id").val(id);
	$("#worker_status").val(sta);

		$.ajax({

		type:"post",

		url:"<?php echo page_url;?>User/getworkername/"+id,

		data:"",

		success:function(data){

		$("#user").val(data);

		}

		});
}


function openlocationpopup(id)
{
	$.ajax({

		type:"post",

		url:"<?php echo page_url;?>User/getworker_details_location/"+id,

		data:"",

		success:function(data){

		if(data!="NA")
		{
			var d= data.split('|');	

			//$("#user").val(data);
			$("#worker_id_for_location").val(id);
			$("#worker_location").val(d[1]);
			getotherlocations(d[1]);
			$("#locationchange").modal('show');
			$("#personname").html(d[0]);
			$("#current_location").val(d[2]);
		}


		}

		});

}

function getotherlocations(location)
{

	$.ajax({

		type:"post",

		url:"<?php echo page_url;?>User/get_store_location/"+location,

		data:"",

		success:function(data){

		$("#transfer_location").html(data);

		}

		});

}
</script>

    </body>

</html>

