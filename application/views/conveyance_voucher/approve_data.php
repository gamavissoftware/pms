<?php
$sdate=$this->uri->segment(3);
$edate=$this->uri->segment(4);
$amount=$this->uri->segment(5);
$user_id=$this->uri->segment(6);
$type=$this->uri->segment(7);
$start_read=$this->uri->segment(8);
$end_read=$this->uri->segment(9);
$diff=$this->uri->segment(10);
$misc_charge=$this->uri->segment(11);
$puse=$this->uri->segment(12);
$average=$this->uri->segment(13);
$flag=$this->uri->segment(14);

$row=$this->db->select('first_name,last_name')->from('system_users')->where('user_id',$user_id)->get();
if($row->num_rows()>0)
{
foreach($row->result() as $rr);

$name=$rr->first_name." ".$rr->last_name;
}else
{
	$name='';
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>APPROVE DATA</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
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
                           
                            <h4 class="page-title text-center">APPROVE CONVEYANCE AMOUNT</h4><hr>
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
								
                                   <form method="post" action="<?php echo page_url;?>Sales/approve_convence_data/">
                                   	<input type="hidden" name="user_id" value="<?php echo $user_id;?>">
                                   	<input type="hidden" name="flag" value="<?php echo $flag;?>">
								
								  
								  <div class="row">
										  <div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">YOUR NAME</label>
														 <span id="error_machine" style="color:red;">*</span>
														 <input type="text" class="form-control" name="yourname" id="yourname" value="<?php echo $name;?>" readonly>
													</div>
												</div>
												
												 <div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">From</label>
														 <span id="error_company_name" style="color:red;">*</span>
														<input type="text" class="form-control" name="sdate" id="sdate" value="<?php echo date('d-M-Y',strtotime($sdate));?>" readonly>
													</div>
												</div>


												 <div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">TO</label>
														 <span id="error_company_name" style="color:red;">*</span>
														<input type="text" class="form-control" name="edate" id="edate" value="<?php echo date('d-M-Y',strtotime($edate));?>" readonly>
													</div>
												</div>


												 <div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Start Reading</label>
														 <span id="error_company_name" style="color:red;">*</span>
														<input type="text" class="form-control" value="<?php echo $start_read;?>" readonly>
													</div>
												</div>


												 <div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">End Read</label>
														 <span id="error_company_name" style="color:red;">*</span>
													<input type="text" class="form-control" value="<?php echo $end_read;?>" readonly>
													</div>
												</div>


												 <div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Net KM</label>
														 <span id="error_company_name" style="color:red;">*</span>
														<input type="text" class="form-control" name="" id="" value="<?php echo $diff;?>" readonly>
													</div>
												</div>

												<?php 
													if($type==1)
													{?>

												 <div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Amount to be approved</label>
														 <span id="error_company_name" style="color:red;">*</span>
														<input type="text" class="form-control" name="amount" id="amount" value="<?php echo $amount;?>" readonly style="color:red;font-weight:bold;font-size:18px;">
													</div>
												</div>

												<?php }else
												{ ?>
													<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Petrol Used</label>
														 <span id="error_company_name" style="color:red;">*</span>
														<input type="text" class="form-control" name="puse" id="puse" value="<?php echo $puse;?>" readonly style="color:red;font-weight:bold;font-size:18px;">
													</div>
												</div>

												<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Vehicle Average</label>
														 <span id="error_company_name" style="color:red;">*</span>
														<input type="text" class="form-control" name="average" id="average" value="<?php echo $average;?>" readonly style="color:red;font-weight:bold;font-size:18px;">
													</div>
												</div>



												<?php } ?>
											</div>


											<div  class="row">


										
										<?php 
													if($type==1)
													{?>


												<div class="col-md-3">
												<div class="form-group">
												<label for="field-2" class="control-label">Approved Amount</label>
												<span id="error_company_name" style="color:red;">*</span>
												<input type="text" class="form-control" name="app_amount" id="app_amount" value="<?php echo $amount;?>">
												</div>
												</div>

											<?php }else{ ?>

												<div class="col-md-3">
												<div class="form-group">
												<label for="field-2" class="control-label">Approved Petrol Usage</label>
												<span id="error_company_name" style="color:red;">*</span>
												<input type="text" class="form-control" name="app_amount" id="app_amount" value="">
												</div>
												</div>


											<?php } ?>

												 
                                            </div>

										<div class="col-md-9"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" class="btn btn-success" value="Save">
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
     $('.select2').select2({ });
$("#save").click(function() {
var company_name = $("#company_name").val();
if(company_name=='')
{
	$("#error_company_name").html('Required!');
}
var sales_force = $("#sales_force").val();
if(sales_force=='')
{
	$("#error_sales_force").html('Required!');
}
var warrenty_status = $("#warrenty_status").val();
if(warrenty_status=='')
{
	$("#error_warrenty_status").html('Required!');
}
var nature_of_complaints = $("#nature_of_complaints").val();
if(nature_of_complaints=='')
{
	$("#error_nature_of_complaints").html('Required!');
}
var service_visit_report = $("#service_visit_report").val();
if(service_visit_report=='')
{
	$("#error_service_visit_report").html('Required!');
}
var cases = $("#cases").val();
if(cases=='')
{
	$("#error_case").html('Required!');
}


if(company_name=='' || sales_force=='' || warrenty_status=='' || nature_of_complaints=='' ||service_visit_report=='' || cases=='')
{
	
	return false;
}

});
});
</script>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
    </body>
</html>