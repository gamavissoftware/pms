<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Edit Generator Reading</title>

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

                             <h4>Edit generator reading <strong style="color:red;"><?php
											$query = $this->db->select('office_id, company_name')->from('offices')->where('office_id',$this->uri->segment(4))->get();
											foreach($query->result() as $row){
												echo $row->company_name;
											}
											?></strong></h4>
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
								$this->db->select('*')->from('generator_reading')->where('id',$id);
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $row)
								?>

                                   <form method="post" action="<?php echo page_url;?>Power_factor/update_generator_reading/<?php echo $row->id;?>/<?php echo $this->uri->segment(4);?>">
									<div class="row">
                                               
                                              
												<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">Date </label>
														 <span id="error_reading_date" style="color:red;">*</span>
														 <input type="date" class="form-control" name="create_date" id="create_date" placeholder="Date" value="<?php echo $row->date;?>">
													</div>
												</div>
												
												<div class="col-md-4">
															<label class="control-label">Morning Reading</label>
																 <span id="error_company" style="color:red;"></span>
														 <input type="text" class="form-control" name="morning_reading" id="morning_reading" placeholder="KWH" value="<?php echo $row->morning_reading;?>">
															</div>
													<div class="col-md-4">
																<label class="control-label">Evening Reading</label>
																 <span id="error_company" style="color:red;"></span>
														 <input type="text" class="form-control" name="evening_reading" id="evening_reading" placeholder="KWH" value="<?php echo $row->evening_reading;?>">
															</div>

												<div class="col-md-12">
												<div class="form-group">
														 <label for="field-2" class="control-label">Remark </label>
														 <Span id="address_error" style="color:red;"></span>
														 <textarea class="form-control" name="remarks" id="remarks" placeholder="Remarks" >
														 <?php echo $row->remarks;?>
														 </textarea>

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


if(country_name=='' || state=='' || city_name=='' || company_name=='')
{

	return false;
}

});
});
</script>
    </body>
</html>
