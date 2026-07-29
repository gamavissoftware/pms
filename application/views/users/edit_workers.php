<?php
$a=$this->db->select('*')->from('workers_list')->where('id',$this->uri->segment(3))->get();
if($a->num_rows()==0)
{
	echo "Invalid Request"; exit;
}else
{
	foreach($a->result() as $row);
}

?>
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

						
                           

                            <h4 class="page-title">EDIT FACTORY WORKER RECORD</h4>

                        </div>

                    </div>

                </div>

                <!-- end page title end breadcrumb -->

<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>



                 <div class="row">

                    <div class="col-sm-12">

                        <div class="card-box table-responsive">

                           <form id="loginForm" method="post" action="<?php echo page_url;?>User/update_workers/<?php echo $this->uri->segment(3);?>"  enctype="multipart/form-data">

                                            <div class="row">

                                            		<div class="col-md-4">

													<div class="form-group">

														 <label for="field-2" class="control-label">Worker Name <span style="color: red">*</span></label>

														 <span id="error_department_name" style="color:red;"></span>

														 <input type="text" class="form-control" name="name" id="name"  value="<?php echo $row->name;?>" required>

													</div>

												</div>

												<div class="col-md-4">

													<div class="form-group">

														 <label for="field-2" class="control-label">Worker Name as per ID <span style="color: red">*</span></label>

														 <span id="error_department_name" style="color:red;"></span>

														 <input type="text" class="form-control" name="name_id" id="name_id"  value="<?php echo $row->document_name;?>" required>

													</div>

												</div>


                                                <!-- <div class="col-md-4" style="">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">Location <span style="color: red">*</span></label>

														<span id="error_business_loc" style="color:red;"></span>

												<select class="form-control" id="location" name="location" required>

													<option value="<?php //echo $row->location;?>">--Select Location--</option>
												<?php 
													// $a=$this->db->select('id,rack_location')->from('store_rack_location')->get();
													// if($a->num_rows()>0)
													// {
													// 	foreach($a->result() as $as)
													// 	{
													// 		if($row->location==$as->id)
													// 		{
													// 			$asel="Selected";
													// 		}else
													// 		{
													// 			$asel="";

													// 		}
														?>
													<option value="<?php //echo $as->id;?>" <?php //echo $asel;?>><?php //echo $as->rack_location;?></option>
													<?php
														//}

													//}
	 													?>

												</select>

												    </div>

                                                </div> -->


                                                	<div class="col-md-4">

													<div class="form-group">

														 <label for="field-2" class="control-label">Mobile <span style="color: red">*</span></label>

														 <span id="error_department_name" style="color:red;"></span>

														 <input type="number" maxlength="10" class="form-control" name="mobile" id="mobile"  value="<?php echo $row->mobile;?>" required  data-mask="(999) 999-9999">

													</div>

												</div>


													<div class="col-md-4">

													<div class="form-group">

														 <label for="field-2" class="control-label">Date Of Joining<span style="color: red">*</span></label>

														 <span id="error_department_name" style="color:red;"></span>

														 <input type="date" class="form-control" name="doj" id="doj"  value="<?php echo $row->doj;?>" required>

													</div>

												</div>


													<div class="col-md-4">

													<div class="form-group">

														 <label for="field-2" class="control-label">Salary <span style="color: red">*</span></label>

														 <span id="error_department_name" style="color:red;"></span>

														 <input type="number" class="form-control" name="salary" id="salary"  value="<?php echo $row->salary;?>" required>
														 <input type="hidden" name="previous_salary" value="<?php echo $row->salary;?>">

													</div>

												</div>

													<div class="col-md-4">

													<div class="form-group">

														 <label for="field-2" class="control-label">DOB <span style="color: red">*</span></label>

														 <span id="error_department_name" style="color:red;"></span>

														 <input type="date" class="form-control" name="dob" id="dob"  value="<?php echo $row->dob;?>" required>

													</div>

												</div>
                                                		

                                                	<div class="col-md-4">

													<div class="form-group">

														 <label for="field-2" class="control-label">Document Type <span style="color: red">*</span></label>

														 <span id="error_department_name" style="color:red;"></span>

														 <input type="text" class="form-control" name="type" id="type"  value="<?php echo $row->document_type;?>" required>

													</div>

												</div>	


													<div class="col-md-4">

													<div class="form-group">

														 <label for="field-2" class="control-label">Upload Document </label>

														 <span id="error_department_name" style="color:red;"></span>

														 <input type="file" class="form-control" name="document" id="document"  value="">
														 <a href="<?php echo sfdocument;?>worker_docs/<?php echo $row->document;?>" download>Click to Download Previous Document</a>
														 <input type="hidden" name="olddoc" value="<?php echo $row->document;?>">

													</div>

												</div>	


													<div class="col-md-4">

													<div class="form-group">

														 <label for="field-2" class="control-label">Passport Image</label>

														 <span id="error_department_name" style="color:red;"></span>

														 <input type="file" class="form-control" name="pass_image" id="pass_image"  value="">
														 <a href="<?php echo sfdocument;?>worker_docs/passport_image/<?php echo $row->passport_image;?>" download>Click to Download Previous Passport Image</a>
														 <input type="hidden" name="oldpass" value="<?php echo $row->passport_image;?>">

													</div>

												</div>	
												


                               

							

                        </div>

                           <div class="col-md-12" style="margin-top:40px;">
               
               			<div class="col-md-4"></div>
                    	<div class="col-md-4 text-center">
                    		<input type="submit" class="btn btn-success" value="Submit" style="width:50%">
                    	</div>
                    </div>

                    </div>

                 
                    	</form>

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



    </body>

</html>

