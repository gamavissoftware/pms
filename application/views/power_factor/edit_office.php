<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Edit Power Factor</title>

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
                           
                            <h4 class="page-title">Edit Office Address Detail</h4>
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
								$this->db->select('*')->from('offices')->where('office_id',$id);
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $row)
								?>
								
                                   <form method="post" action="<?php echo page_url;?>Power_factor/update_office_detail/<?php echo $row->office_id;?>">
										<div class="col-md-3">
											<div class="form-group">
												<label>Country Name</label>
												<select class="form-control" id="country_name" name="country_name">
												<?php 
												$this->db->select('*')->from('countries')->where('country_id',$row->country_id);
													$query = $this->db->get();
													$result = $query->result();
													foreach($result as $country)
													{
													?>
													<option value="<?php echo $row->country_id;?>"><?php echo $country->country_name;?></option>
													<?php }?>
													<?php 
													$this->db->select('*')->from('countries')->where('country_status','1');
													$this->db->order_by('country_name');
													$query = $this->db->get();
													$res = $query->result();
													foreach($res as $country_detail){
													?>
													<option value="<?php echo $country_detail->country_id;?>"><?php echo $country_detail->country_name;?></option>
											<?php }?>		
												</select>
												
												<script type="text/javascript">
											
													$("#country_name").change(function(){
													var country_name=$("#country_name").val();
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Master/Business_location/select_state",
													data:"country_name="+country_name,
													success:function(data){
													$("#state").html(data);
													}
													});
													});
											
												</script>
												
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label>State</label>
												 <select class="form-control" name="state" id="state">
														<option value="<?php echo $row->state_id;?>">
														<?php $this->db->select('*');
														$this->db->distinct();
														$this->db->from('states');
														$this->db->where('state_id',$row->state_id);
														$this->db->order_by('state_name','asc');
														$res = $this->db->get();
														$state_info= $res->result();
														foreach($state_info as $state_Detail)
														{echo $state_Detail->state_name;}?></option>
												 </select>
												 
												 <script type="text/javascript">
											
													$("#state").change(function(){
													var state=$("#state").val();
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Master/Business_location/select_city",
													data:"state="+state,
													success:function(data){
													$("#city_name").html(data);
													}
													});
													});
											
												</script>
											</div>
										</div>
										
										
										<div class="col-md-3">
											<div class="form-group">
												<label>City</label>
													<select class="form-control" name="city_name" id="city_name">
														<option value="<?php echo $row->city_id;?>"><?php
$this->db->select('city_id,city_name')->from('cities')->where('city_id',$row->city_id);
$query = $this->db->get();
$city_info = $query->result();
foreach($city_info as $city_detail)
{
	
	echo $city_detail->city_name;
}

														?></option>
														</select>
											</div>
										</div>
										
										<div class="col-md-3">
											<div class="form-group">
												<label>Company Name</label>
												 <input type="text" class="form-control" name="company_name" id="company_name" placeholder="Company Name" value="<?php echo $row->company_name;?>">
											</div>
										</div>
										
										
										
										<div class="col-md-6">
											<div class="form-group">
												<label>Address</label>
												 <textarea class="form-control" name="address" id="address" placeholder="Address"><?php echo $row->address;?></textarea>
											</div>
										</div>
										
										<div class="col-md-3">
											<div class="form-group">
												<label>Status</label>
												<select class="form-control" id="status" name="status">
													<option value="<?php echo $row->office_status;?>">--Select Status--</option>
													<option value="1" <?php if($row->office_status=='1'){echo "selected";}?>>Active</option>
													<option value="0" <?php if($row->office_status=='0'){echo "selected";}?>>Inactive</option>
												</select>
											</div>
										</div>
										<div class="col-md-3"></div>
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