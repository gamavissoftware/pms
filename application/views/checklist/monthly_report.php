<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Checklist</title>

        <!-- Table Responsive css -->
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
		<style>
            .requiredclass{
                border: 2px solid #E12830;
            }
        </style>
		<?PHP 
$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
foreach($q->result() as $LOGO);
?>
		<style>
table.manglesh thead th {
				background: <?php echo $LOGO->colorcode;?>;
				color:#fff;
				font-weight:bold;
			}

table.report thead th {
				background: #2E7DA6;
				color:#fff;
				font-weight:bold;
			}
table.report tbody td {
				font-weight:bold;
				color:#000;
			}
.feedback {
  background-color : #31B0D5;
  color: white;
  padding: 10px 20px;
  border-radius: 4px;
  border-color: #46b8da;
}

.tddata{
	padding:5px 5px 5px 5px; text-align:center; color:#fff; font-size:16px; font-weight:bold;
}
.donebackgroundcolor{
				background-color:green !important; 
				color:#fff !important;
				font-weight:bold !important;
			}
.notdonebackgroundcolor{
				background-color:red !important; 
				color:#fff !important;
				font-weight:bold !important;
			}
			
			</style>
			 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
			
    </head>


   <body>
<header id="topnav">
<?php $this->load->view('common/nav-menu.php');?>
</header>
<div class="wrapper">

            <div class="container">
	<!-- Page-Title -->

                
					<form id="loginForm" method="post" action="<?php echo page_url;?>Checklist/view_userwise_monthly_report" enctype="multipart/form-data">
					<div class="row">
					    <div class="col-md-2"></div>
					    <div class="col-md-8">
					        <div class="row  card-box">
					            <div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">BUSINESS LOCATION</label>
														 <span id="error_business_loc" style="color:red;">*</span>
												<select class="form-control" id="business_loc" name="business_loc">
													<option value="">--SELECT BUSINESS LOCATION--</option>
													<?php 
													$this->db->select('a.company_name, a.business_loc_id,a.state_id, a.city_id,b. 	state_id,b.state_name,c.city_id,c.city_name')->from('business_location a')->join('states b','a.state_id=b.state_id','left')->join('cities c','a.city_id=c.city_id','left')->where('business_loc_status','1');
													$this->db->order_by('a.company_name','asc');
													$query = $this->db->get();
													$res = $query->result();
													foreach($res as $row){
													?>
													<option value="<?php echo $row->business_loc_id;?>"><?php echo strtoupper($row->company_name);?> <?php echo strtoupper($row->state_name);?>, <?php echo strtoupper($row->city_name);?></option>
											<?php }?>		
												</select>
												<script type="text/javascript">
											
													$("#business_loc").change(function(){
													var business_loc=$("#business_loc").val();
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Checklist/user_list",
													data:"business_loc="+business_loc,
													success:function(data){
													$("#user_id").html(data);
													}
													});
													
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Checklist/user_list",
													data:"business_loc="+business_loc,
													success:function(data){
													$("#reporting_head").html(data);
													}
													});
													});
											
												</script>
												
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">USER NAME</label>
														 <span id="error_user_id" style="color:red;">*</span>
														 <select class="form-control select3" id="user_id" name="user_id" required>
													<option value="">--SELECT USER--</option>
													
												</select>
													</div>
												</div>
                                               
												
											
												<div class="col-md-2" style="display:none">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">TAT</label>

														

														<select class="form-control" id="turnaroundtime" name="turnaroundtime" style="text-transform:uppercase">

														<option value="">--Select Turnaround Time--</option>
														<?php 
														$query = $this->db->select('id,turnaroundtime, status')->from('compliance_tat')->where('status','1')->get();

														foreach($query->result() as $turnaroundtime){
														?>
														<option value="<?php echo $turnaroundtime->id;?>"><?php echo strtoupper($turnaroundtime->turnaroundtime);?></option>
														<?php }?>

														</select>

                                                    </div>

                                                </div>
                                                
												<div class="col-md-4">

                                                    <div class="form-group">

                                                        <label for="field-1" class="control-label">MONTH</label>

														<span id="error_month" style="color:red;">*</span>

														<select class="form-control" id="month" name="month" style="text-transform:uppercase" required>

														<option value="">SELECT MONTH</option>
														<option value="01">JAN</option>
														<option value="02">FEB</option>
														<option value="03">MAR</option>
														<option value="04">APR</option>
														<option value="05">MAY</option>
														<option value="06">JUN</option>
														<option value="07">JUL</option>
														<option value="08">AUG</option>
														<option value="09">SEP</option>
														<option value="10">OCT</option>
														<option value="11">NOV</option>
														<option value="12">DEC</option>

														</select>

                                                    </div>

                                                </div>
                                                <div class="col-md-9"></div>
                                              <div class="col-md-3 pull-right">
                                                  <div class="form-group pull-right">
                                                      <input type="submit" id="save" class="btn btn-info" value="filter"> 
                                                  </div>
                                                   
                                              </div>
					        </div>
					    </div>
					    <div class="col-md-2"></div>
			
										  
										

                                            </div>									
					
					</form>										
															
															

                </div>

                <!-- end page title end breadcrumb -->

		

	
 

                <!-- Footer -->

                <?php $this->load->view('common/footer');?>

                <!-- End Footer -->
            </div> <!-- end container -->

        </div>

       <!-- jQuery  -->
       
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




</body></html>