<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Prestogroup</title>

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
<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
		<link href="<?php echo assets_url;?>plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
<style>
table.manglesh thead th {
				background: #003366;
				color:#fff;
				font-weight:bold;
			}
			
#pageloader
{
  background: rgba( 255, 255, 255, 0.8 );
  display: none;
  height: 100%;
  position: fixed;
  width: 100%;
  z-index: 9999;
}
#pageloader img
{
  left: 30%;
  margin-left: -10px;
  margin-top: -10px;
  position: absolute;
  top: 30%;
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
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="btn-group pull-right">
                              
                            </div>
                            <h4 class="page-title">Edit Conveyance Voucher Detail</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->


                <div class="row" style="padding-top:130px">
                    <div class="col-xs-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">
								<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
								<?php
								$id = $this->uri->segment(3);
								$this->db->select('*')->from('conveyance_voucher')->where('id',$id);
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $row)
								
								?>
								
                                   <form method="post" id="loginForm" action="<?php echo page_url;?>Sales/edit_conveyance_voucher/<?php echo $row->id;?>">
								    <div id="pageloader">
                                    <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
                                    </div>
										<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">DATE</label>
														<span id="error_date" style="color:red;">*</span>
														 <input type="text" id="datepicker1" name="date" class="form-control" value="<?php echo date('d-m-Y',strtotime($row->travel_date));?>">																											
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">FROM</label>
														<span id="error_from" style="color:red;">*</span>
                                                        <input type="text" class="form-control" id="from" name="from" style="text-transform: uppercase;" placeholder="" value="<?php echo $row->from_location;?>" required>
                                                    </div>
                                                </div>
												
												  <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">PROCEED TO</label>
														<span id="error_proceed_to" style="color:red;">*</span>
                                                        <input type="text" class="form-control" id="proceed_to" name="proceed_to" style="text-transform: uppercase;" placeholder="" value="<?php echo $row->proceed_to;?>" required>
                                                    </div>
                                                </div>
												
											<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">MODE</label>
														<span id="error_mode" style="color:red;">*</span>
                                                         <select class="form-control" name="mode" id="mode">
															<option value="">SELECT MODE</option>
															<option value="1" <?php if($row->mode=='1'){echo "selected";}?>>PUBLIC CONVEYANCE</option>
															<option value="2" <?php if($row->mode=='2'){echo "selected";}?>>OWN</option>
														 </select>
                                                    </div>
                                                </div>
												
												<script>
												$(function() {
													<?php if($row->mode=='2'){}else{?>
												$('#row_dim').hide(); 
													<?php }?>
												$('#mode').change(function(){
												if($('#mode').val() == '2') {
												$('#row_dim').show();
												$('#amountbox').hide();
												
												} else {
												$('#row_dim').hide(); 
												$('#amountbox').show();
												} 
												});
												});
											</script>
											<div class="col-md-6" id="row_dim">
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
												<label for="field-2" class="control-label">START READING</label>
												<input type="number" class="form-control" name="start_reading" id="end_reading" value="<?php echo $row->start_reading;?>">
												</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
												<label for="field-2" class="control-label">END READING</label>
												<input type="number" class="form-control" name="end_reading" id="end_reading" value="<?php echo $row->end_reading;?>">
												</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
												<label for="field-2" class="control-label">RATE Kms.</label>
												<input type="number" class="form-control" name="rate_per_km" step="0.2" id="rate_per_km" value="<?php echo $row->rate_per_km;?>">
												</div>
												</div>
											</div>
												
											</div>
											
											<div class="col-md-3" id="amountbox">
											
													<div class="form-group">
												<label for="field-2" class="control-label">AMOUNT</label>
												<input type="text" class="form-control" name="amount" id="amount" value="<?php echo $row->amount;?>" readonly>
												</div>
												
												
											</div>
												 
										<div class="col-md-9"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" id="save" class="btn btn-success" value="Update">
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
		<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
		<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script language="javascript" type="text/javascript">   
$(document).ready(function() {
$("#save").click(function() {
	
var date = $("#date").val();
if(date=='')
{
	$("#error_date").html('Required!');
}
var from = $("#from").val();
if(from=='')
{
	$("#error_from").html('Required!');
}
var proceed_to = $("#proceed_to").val();
if(proceed_to=='')
{
	
	$("#error_proceed_to").html('Required!');
}

var mode = $("#mode").val();
if(mode=='')
{
	
	$("#error_mode").html('Required!');
}


if(date=='' || from=='' || proceed_to=='' || mode=='')
{
	
	return false;
}

});
});


	

</script>

<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready
</script>
<script> $(document).ready(function() {
				$("#datepicker1").datepicker({
					dateFormat: 'dd-mm-yyyy'
				});
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
					
                })

            });</script>
			</body>
</html>