<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Delegation</title>

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

                <!-- Page-Title 
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="btn-group pull-right">
                              
                            </div>
                            <h4 class="page-title">Edit Delegation Master</h4>
                        </div>
                    </div>
                </div>-->
                <!-- end page title end breadcrumb -->
        <?php 
        $query=$this->db->select('*')->from('company_information')->where('id','1')->get();
        if($query->num_rows()>0)
        {
            foreach($query->result() as $comprofile);
        }
        
        ?>

        <div class="account-pages"></div>

        <div class="clearfix"></div>

        <div class="wrapper-pages" style="margin: 1% auto;">

           

        	<div class="m-t-40 card-box">

                <div class="text-center">

                    <h4 class="text-uppercase font-bold m-b-0">Edit Company Account</h4>

                     <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

                </div>

                <div class="panel-body">

                    <form method="post" class="form-horizontal m-t-20" action="<?php echo page_url;?>User/updateregistration" enctype="multipart/form-data">
                        <input type="hidden" name="oldimage" value="<?php echo $comprofile->logo; ?>">
                    <div class="row">
                        <div class="col-md-3">
					  <div class="form-group ">

                            <div class="col-xs-12">

							<span style="color:red; font-size:12px" id="error_person_name"></span>

                                <input class="form-control" type="text" placeholder="Person Name" name="person_name" id="person_name" value="<?php echo $comprofile->person_name; ?>">

                            </div>

                        </div>
                        </div>
						
                        <div class="col-md-3">
						  <div class="form-group ">

                            <div class="col-xs-12">

							<span style="color:red; font-size:12px" id="error_company_name"></span>

                                <input class="form-control" type="text" placeholder="Company Name" name="company_name" id="company_name" value="<?php echo $comprofile->company_name; ?>">

                            </div>

                        </div>
                        </div>
                       
                    <div class="col-md-3">
						<div class="form-group ">

                            <div class="col-xs-12">

							<span style="color:red; font-size:12px" id="error_contact_number"></span>

                                <input class="form-control" type="text" placeholder="Contact Number" name="contact_number" id="contact_number" value="<?php echo $comprofile->company_name; ?>">

                            </div>

                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group ">

                            <div class="col-xs-12">

							<span style="color:red; font-size:12px" id="error_email"></span>

                                <input class="form-control" type="text" placeholder="Email ID" name="email" id="email" value="<?php echo $comprofile->company_name; ?>">

                            </div>

                        </div>
                    
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">

                            <div class="col-xs-12">

							<span style="color:red;  font-size:12px;" id="error_password"></span>

                                <input class="form-control" type="password" required="" placeholder="Password" name="password" id="password" value="<?php echo $comprofile->company_name; ?>">

                            </div>

                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group ">

                            <div class="col-xs-12">

							<span style="color:red; font-size:12px" id="error_logo"></span>

							<label>Company Logo Size (200px X 158px)</label>

                                <input class="form-control" type="file" placeholder="Logo" name="logo" id="logo" value="<?php echo $comprofile->logo; ?>">
                            <img src="<?php echo sfdocument; ?>registration/<?php echo $comprofile->logo; ?>" width="20%" />
                            </div>

                        </div>
                        </div>
						
                        <div class="col-md-3">
						   <div class="form-group ">

                            <div class="col-xs-12">

							<span style="color:red; font-size:12px" id="error_address"></span>

                                <textarea class="form-control" type="text" placeholder="Address" name="address" id="address" value=""><?php echo $comprofile->address; ?></textarea>

                            </div>

                        </div>
                        </div>
                      <div class="col-md-3">
                        <div class="form-group ">

                            <div class="col-xs-12">

							<span style="color:red; font-size:12px" id="error_colorcode"></span>

							<label>Color picker for Reporting Dashboard</label>

                                <input class="form-control" type="color" placeholder="Color Picker" name="colorcode" id="colorcode" value="<?php echo $comprofile->colorcode; ?>">

                            </div>

                        </div>
                        </div>
                        </div>
                        <h6 class="text-uppercase font-bold m-b-0 text-center">mail setup</h6>
                        <hr>
                        
                        <div class="row">
                        <div class="col-md-3">
                        <div class="form-group ">

                            <div class="col-xs-12">

							<span style="color:red; font-size:12px" id="error_colorcode"></span>
                                <input class="form-control" type="email" placeholder="Email ID" name="configemail" id="configemail" value="<?php echo $comprofile->config_mail; ?>">

                            </div>

                        </div>
                        </div>
                        <div class="col-md-3">
                        <div class="form-group ">

                            <div class="col-xs-12">

							<span style="color:red; font-size:12px" id="error_colorcode"></span>
                                <input class="form-control" type="text" placeholder="Password" name="configpassword" id="configpassword" value="<?php echo $comprofile->config_password; ?>">

                            </div>

                        </div>
                        </div>
                        <div class="col-md-3">
                        <div class="form-group ">

                            <div class="col-xs-12">

							<span style="color:red; font-size:12px" id="error_colorcode"></span>
                                <input class="form-control" type="text" placeholder="SMTP Host" name="smtp" id="smtp" value="<?php echo $comprofile->smtp_host; ?>">

                            </div>

                        </div>
                        </div>
                        <div class="col-md-3">
                        <div class="form-group ">

                            <div class="col-xs-12">

							<span style="color:red; font-size:12px" id="error_colorcode"></span>
                                <input class="form-control" type="text" placeholder="Port Number" name="port" id="port" value="<?php echo $comprofile->port_number; ?>">

                            </div>

                        </div>
                        </div>
                        </div>
                        <div class="row">
                            <h6 class="text-uppercase font-bold m-b-0 text-center"> SMS API setup</h6>
                        <hr>
                         <div class="col-md-3">
                        <div class="form-group ">

                            <div class="col-xs-12">

							<span style="color:red; font-size:12px" id="error_colorcode"></span>
                                <input class="form-control" type="text" placeholder="Auth Key" name="auth" id="auth" value="<?php echo $comprofile->auth_key; ?>">

                            </div>

                        </div>
                        </div>
                        <div class="col-md-3">
                        <div class="form-group ">

                            <div class="col-xs-12">

							<span style="color:red; font-size:12px" id="error_colorcode"></span>
                                <input class="form-control" type="text" placeholder="sender Id" name="sender" id="sender" value="<?php echo $comprofile->senderid; ?>">

                            </div>

                        </div>
                        </div>
                        <div class="col-md-3">
                        <div class="form-group ">

                            <div class="col-xs-12">

							<span style="color:red; font-size:12px" id="error_colorcode"></span>
                                <input class="form-control" type="text" placeholder="SMS provider name" name="smsname" id="smsname" value="<?php echo $comprofile->sms_provider_name; ?>">

                            </div>

                        </div>
                        </div>

                       

                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                        <div class="form-group text-center m-t-30">

                            <div class="col-xs-12">

                                <button class="btn btn-custom btn-bordred btn-block waves-effect waves-light" type="submit" id="save">Submit</button>

                            </div>

                        </div>
                        </div>
                        
                        <div class="col-md-4"></div>
                    </div>


                        <div class="form-group m-t-30 m-b-0">

                            <div class="col-sm-12">

                              

								

							<!--	<p style="font-size:12px;"><?php echo date('Y');?> © LEAD MANAGEMENT SYSTEM DEVELOPED BY <?php echo copyright;?></p>-->

                            </div>

                        </div>

                    </form>




            </div>

            <!-- end card-box-->



          

            

        </div>

        <!-- end wrapper page -->

        


        </div>


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
<script>
$(document).ready(function(){
	   $("#updsave").attr('disabled',false);
	   $("#updsave").val('submit');
  $("#loginForm").on("submit", function(){
   // $("#pageloader").fadeIn();
   $("#updsave").attr('disabled',true);
     $("#updsave").val('Please Wait...');
  });//submit
});//document ready
</script>
<script language="javascript" type="text/javascript">   
$(document).ready(function() {
	$('.select2').select2({ });
$('.select3').select2({ });
$('.select4').select2({ });
$("#save").click(function() {
	
	var business_loc = $("#business_loc").val();
if(business_loc=='')
{
	$("#error_business_loc").html('Required!');
}
var user_id = $("#user_id").val();
if(user_id=='')
{
	$("#error_user_id").html('Required!');
}
var reporting_head = $("#reporting_head").val();
if(reporting_head=='')
{
	
	$("#error_reporting_head").html('Required!');
}

var what = $("#what").val();
if(what=='')
{
	
	$("#error_what").html('Required!');
}

var how = $("#how").val();
if(how=='')
{
	
	$("#error_how").html('Required!');
}
var when = $("#when").val();
if(when=='')
{
	
	$("#error_when").html('Required!');
}


var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}


if(business_loc=='' || user_id=='' || reporting_head=='' || what=='' || how==''|| when=='' || status=='' || set_order=='' || response_type=='' || actiontobetaken=='')
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
				$("#datepicker1").datepicker();
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
                })

            });</script>
			</body>
</html>