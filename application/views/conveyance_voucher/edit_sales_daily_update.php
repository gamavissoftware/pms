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
                            <h4 class="page-title">EDIT FIELD SALES DAILY UPDATE</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->


                <div class="row" style="padding-top:20px">
                    <div class="col-xs-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">
								<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
								<?php
								$id = $this->uri->segment(3);
								$this->db->select('*')->from('field_sales_daily_update')->where('id',$id);
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $row)
								
								?>
								
                                   <form method="post" id="loginForm" action="<?php echo page_url;?>Sales/update_sales_daily_update/<?php echo $row->id;?>">
								    <div id="pageloader">
                                    <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
									<input type="hidden" name="old_file" value="<?php echo $row->visiting_card;?>">
                                    </div>
									 <?php 
								$first_name =$this->session->userdata['logged_in']['user_name'];	
								$last_name =$this->session->userdata['logged_in']['last_name'];
								?> 
											 <div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Your Name</label>
														 <span id="error_machine" style="color:red;">*</span>
														 <input type="text" class="form-control" name="yourname" id="yourname" value="<?php echo $first_name;?> <?php echo $last_name;?>" readonly>
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Customer Name</label>
														 <span id="error_machine" style="color:red;">*</span>
														 <input type="text" class="form-control" name="customer_name" id="customer_name" value="<?php echo $row->customer_name;?>" required>
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Customer Contact Number</label>
														 <span id="error_machine" style="color:red;">*</span>
														 <input type="text" class="form-control" name="contact_number" id="contact_number" value="<?php echo $row->contact_number;?>" required>
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Company Name</label>
														 <span id="error_company_name" style="color:red;">*</span>
														 <input type="text" class="form-control" name="company_name" id="company_name" value="<?php echo $row->company_name;?>" required>
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Visit No</label>
														 <span id="error_visit_number" style="color:red;">*</span>
														 <input type="text" class="form-control" name="visit_number" id="visit_number" value="<?php echo $row->visit_number;?>" required>
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Action Taken</label>
														 <span id="error_action_taken" style="color:red;">*</span>
														 <select class="form-control" name="action_taken" id="action_taken">
														 <option value="VISIT" <?php if($row->action_taken=='VISIT'){echo "selected";}?>>VISIT</option>
														 <option value="PHONE CALL" <?php if($row->action_taken=='PHONE CALL'){echo "selected";}?>>PHONE CALL</option>
														 <option value="MAIL" <?php if($row->action_taken=='MAIL'){echo "selected";}?>>MAIL</option>
														 </select>
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Stage</label>
														 <span id="error_stage" style="color:red;">*</span>
														 <select class="form-control" name="stage" id="stage">
														 <option value="PRELIMINARY" <?php if($row->stage=='PRELIMINARY'){echo "selected";}?>>PRELIMINARY</option>
														 <option value="PHONE CALL" <?php if($row->stage=='PHONE CALL'){echo "selected";}?>>TECHNICAL</option>
														 <option value="COMERCIAL" <?php if($row->stage=='COMERCIAL'){echo "selected";}?>>COMERCIAL</option>
														 <option value="NEGOTIATION" <?php if($row->stage=='NEGOTIATION'){echo "selected";}?>>NEGOTIATION</option>
														 <option value="FINALIZATION" <?php if($row->stage=='FINALIZATION'){echo "selected";}?>>FINALIZATION</option>
														 </select>
													</div>
												</div>
												
												<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Value</label>
														 <span id="error_stage" style="color:red;">*</span>
														 <select class="form-control" name="value" id="value">
														 <option value="0-1 LACS." <?php if($row->stage=='0-1 LACS.'){echo "selected";}?>>0-1 LACS.</option>
														 <option value="1-3 LACS." <?php if($row->stage=='1-3 LACS.'){echo "selected";}?>>1-3 LACS.</option>
														 <option value="3-5 LACS." <?php if($row->stage=='3-5 LACS.'){echo "selected";}?>>3-5 LACS.</option>
														 <option value="5 LACS. & ABOVE" <?php if($row->stage=='5 LACS. & ABOVE'){echo "selected";}?>>5 LACS. & ABOVE</option>
														
														 </select>
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Machine Name</label>
														 <span id="error_machine_name" style="color:red;">*</span>
														 <input type="text" class="form-control" name="machine_name" id="machine_name" value="<?php echo $row->machine_name;?>" required>
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Next Action Plan</label>
														 <span id="error_next_action_plan" style="color:red;">*</span>
														 <input type="text" class="form-control" name="next_action_plan" id="next_action_plan" value="<?php echo $row->next_action_plan;?>" required>
													</div>
												</div>
											 <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Date of Next Plan</label>
														<span id="error_visit_date" style="color:red;"></span>
                                                        <input type="text" id="datepicker1" name="date_of_next_plan" class="form-control datepicker" autocomplete="off" value="<?php echo date('m/d/Y',strtotime($row->date_of_next_plan));?>" required>
                                                    </div>
                                                </div>
											 <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Visiting Card Image (Upload if Any	)</label>
														
                                                        <input type="file" id="visiting_card" name="visiting_card" class="form-control" autocomplete="off" value="">
														<?php 
														if($row->visiting_card){
															?>
															<img src="<?php echo sale_visit.$row->visiting_card;?>" width="100px">
															<?php
														}
														?>
                                                    </div>
                                                </div>
												
										<div class="col-md-12"></div>
										<div class="col-md-12">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" id="save" class="btn btn-success" value="Submit">
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