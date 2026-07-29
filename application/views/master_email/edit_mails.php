<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Edit Support Email ID</title>

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
                           
                             <h4>Edit <?php 
				if($this->uri->segment(4)=='1'){
				echo $support = "IT Support";
			}elseif($this->uri->segment(4)=='2'){
				echo $support = "Dispatch Support";
			}elseif($this->uri->segment(4)=='3'){
				echo $support = "Service Support";
			}elseif($this->uri->segment(4)=='4'){
				echo $support = "Maintenance Support";
			}?> Mails </h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
	<?php
								$id = $this->uri->segment(3);
								$this->db->select('*')->from('support_email_option')->where('id',$id);
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $row)
								?>
								
                                   <form method="post" action="<?php echo page_url;?>Master_emails/update_email_detail/<?php echo $row->id;?>/<?php echo $this->uri->segment(4);?>">
									
               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">

							<div class="row">
                                               <div class="col-md-3">
													<div class="form-group">
                                                        <label for="field-1" class="control-label">Support Option<span style="color:red;0">*</span></label>
														<span id="error_support_option" style="color:red;"></span>
														<select class="form-control" name="support_option" id="support_option">
															<option value="">Select Option</option>
															<option value="1" <?php if($row->support_option=='1'){echo "selected";}?>>IT Support</option>
															<option value="2" <?php if($row->support_option=='2'){echo "selected";}?>>Dispatch Support</option>
															<option value="3" <?php if($row->support_option=='3'){echo "selected";}?>>Service Support</option>
															<option value="4" <?php if($row->support_option=='4'){echo "selected";}?>>Maintenance Support</option>
															
														</select>
												
                                                    </div>
											</div>
														
                                              <div class="col-md-4">
													<div class="form-group">
                                                        <label for="field-1" class="control-label">Master Email ID (To)<span style="color:red;0">*</span></label>
														<span id="error_email" style="color:red;"></span>
														<input type="email" class="form-control" id="email" name="email" placeholder="Email ID" value="<?php echo $row->email;?>" required>
												
                                                    </div>
												</div>
														
											<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Member Name <span style="color:red;">*</span></label>
														 <span id="error_member" style="color:red;"></span>
														 <input type="text" class="form-control" name="member" id="member" value="<?php echo $row->name;?>" placeholder="Member Name" >
														
													</div>
												</div>
												
													<div class="col-md-2">
													<div class="form-group" style="margin-top:25px">
														<button type="button" class="btn btn-warning" name="add" id="addmore_btn"><i class="fa fa-plus"></i></button>
													</div>
												</div>
											
                                            </div>
											<?php 
											$query = $this->db->select('*')->from('support_email_options')->where('support_option',$row->support_option)->get();
											foreach($query->result() as $emails){
											?>
											<div class="row">
												<div class="col-md-5">
												<input type="hidden" name="record_id[]" value="<?php echo $emails->id;?>">
													<div class="form-group">
														<label>Email ID</label>
														<input type="text" class="form-control" name="emails[]" value="<?php echo $emails->email;?>">
													</div>
												</div>
												<div class="col-md-5">
													<div class="form-group">
														<label>Member Name</label>
														<input type="text" class="form-control" name="members[]" value="<?php echo $emails->name;?>">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group" style="margin-top:25px">
														
														<a href="<?php echo page_url;?>Master_emails/delete_emails/<?php echo $emails->id;?>"><i class="fa fa-trash" style="font-size:30px"></i></a>
													</div>
												</div>
												
												</div>
											<?php }?>
											</div>
										<div class="row">
												<h5 style="margin-left:10px; font-weight:bold;">Add Member in CC</h5><hr>
												<div id="dynamictasks" style="margin-left:20px">
											</div>
											</div>
										<div class="col-md-9"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" class="btn btn-success" value="Update">
											</div>
										</div>
									
									
                                   

                                </div>

                            </div>
                            <!-- end row -->
                        </div> <!-- end ard-box -->
                    </div><!-- end col-->
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
<script type="text/javascript">
		 $(document).ready(function(){
 var i=1;
 $('#addmore_btn').click(function(){
 i++;
 
 $('#dynamictasks').append('<div id="row'+i+'" class="row"><div class="col-md-5"><div class="form-group"><label for="field-1" class="control-label">Email ID<span style="color:red;0">*</span></label><span id="error_email" style="color:red;"></span><input type="hidden" name="record_id[]" value=""><input type="email" class="form-control" id="email" name="emails[]" placeholder="Email ID" required></div></div><div class="col-md-5"><div class="form-group"><label for="field-1" class="control-label">Member Name<span style="color:red;">*</span></label><span id="error_schedule" style="color:red;"></span><input type="text" class="form-control" name="members[]" id="member" value="" placeholder="Member Name"></div></div> <div class="col-md-2"><div class="form-group pull-left" style="padding-top:30px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div><br/>');
 
 });
 
 
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id+'').remove();
 });
 
});
	  </script>
    </body>
</html>