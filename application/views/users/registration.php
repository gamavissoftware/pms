<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="<?php echo copyright; ?>">



        <!-- App Favicon -->

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <!-- App title -->

        <title><?php echo sitetitle; ?> New Registration</title>



        <!-- App CSS -->

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


    <div class="container">

        <div class="account-pages"></div>

        <div class="clearfix"></div>

        <div class="wrapper-pages" style="margin: 1% auto;">

           

        	<div class="m-t-40 card-box">

                <div class="text-center">

                    <h4 class="text-uppercase font-bold m-b-0">Create New Account</h4>

                     <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

                </div>

                <div class="panel-body">

                    <form method="post" class="form-horizontal m-t-20" action="<?php echo page_url;?>User/addregistration" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-3">
					  <div class="form-group ">

                            <div class="col-xs-12">

							<span style="color:red; font-size:12px" id="error_person_name"></span>

                                <input class="form-control" type="text" placeholder="Person Name" name="person_name" id="person_name" value="">

                            </div>

                        </div>
                        </div>
						
                        <div class="col-md-3">
						  <div class="form-group ">

                            <div class="col-xs-12">

							<span style="color:red; font-size:12px" id="error_company_name"></span>

                                <input class="form-control" type="text" placeholder="Company Name" name="company_name" id="company_name" value="">

                            </div>

                        </div>
                        </div>
                       
                    <div class="col-md-3">
						<div class="form-group ">

                            <div class="col-xs-12">

							<span style="color:red; font-size:12px" id="error_contact_number"></span>

                                <input class="form-control" type="text" placeholder="Contact Number" name="contact_number" id="contact_number" value="">

                            </div>

                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group ">

                            <div class="col-xs-12">

							<span style="color:red; font-size:12px" id="error_email"></span>

                                <input class="form-control" type="text" placeholder="Email ID" name="email" id="email" value="">

                            </div>

                        </div>
                    
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">

                            <div class="col-xs-12">

							<span style="color:red;  font-size:12px;" id="error_password"></span>

                                <input class="form-control" type="password" required="" placeholder="Password" name="password" id="password" value="">

                            </div>

                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group ">

                            <div class="col-xs-12">

							<span style="color:red; font-size:12px" id="error_logo"></span>

							<label>Company Logo Size (200px X 158px)</label>

                                <input class="form-control" type="file" placeholder="Logo" name="logo" id="logo" value="">

                            </div>

                        </div>
                        </div>
						
                        <div class="col-md-3">
						   <div class="form-group ">

                            <div class="col-xs-12">

							<span style="color:red; font-size:12px" id="error_address"></span>

                                <textarea class="form-control" type="text" placeholder="Address" name="address" id="address" value="">Address</textarea>

                            </div>

                        </div>
                        </div>
                      <div class="col-md-3">
                        <div class="form-group ">

                            <div class="col-xs-12">

							<span style="color:red; font-size:12px" id="error_colorcode"></span>

							<label>Color picker for Reporting Dashboard</label>

                                <input class="form-control" type="color" placeholder="Color Picker" name="colorcode" id="colorcode" value="">

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
                                <input class="form-control" type="email" placeholder="Email ID" name="configemail" id="configemail" value="">

                            </div>

                        </div>
                        </div>
                        <div class="col-md-3">
                        <div class="form-group ">

                            <div class="col-xs-12">

							<span style="color:red; font-size:12px" id="error_colorcode"></span>
                                <input class="form-control" type="text" placeholder="Password" name="configpassword" id="configpassword" value="">

                            </div>

                        </div>
                        </div>
                        <div class="col-md-3">
                        <div class="form-group ">

                            <div class="col-xs-12">

							<span style="color:red; font-size:12px" id="error_colorcode"></span>
                                <input class="form-control" type="text" placeholder="SMTP Host" name="smtp" id="smtp" value="">

                            </div>

                        </div>
                        </div>
                        <div class="col-md-3">
                        <div class="form-group ">

                            <div class="col-xs-12">

							<span style="color:red; font-size:12px" id="error_colorcode"></span>
                                <input class="form-control" type="text" placeholder="Port Number" name="port" id="port" value="">

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
                                <input class="form-control" type="text" placeholder="Auth Key" name="auth" id="auth" value="">

                            </div>

                        </div>
                        </div>
                        <div class="col-md-3">
                        <div class="form-group ">

                            <div class="col-xs-12">

							<span style="color:red; font-size:12px" id="error_colorcode"></span>
                                <input class="form-control" type="text" placeholder="sender Id" name="sender" id="sender" value="">

                            </div>

                        </div>
                        </div>
                        <div class="col-md-3">
                        <div class="form-group ">

                            <div class="col-xs-12">

							<span style="color:red; font-size:12px" id="error_colorcode"></span>
                                <input class="form-control" type="text" placeholder="SMS provider name" name="smsname" id="smsname" value="">

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

                              

								

								<p style="font-size:12px;"><?php echo date('Y');?> © LEAD MANAGEMENT SYSTEM DEVELOPED BY <?php echo copyright;?></p>
									<script type="text/javascript"> //<![CDATA[
  var tlJsHost = ((window.location.protocol == "https:") ? "https://secure.trust-provider.com/" : "http://www.trustlogo.com/");
  document.write(unescape("%3Cscript src='" + tlJsHost + "trustlogo/javascript/trustlogo.js' type='text/javascript'%3E%3C/script%3E"));
//]]></script>
<script language="JavaScript" type="text/javascript">
  TrustLogo("https://www.positivessl.com/images/seals/positivessl_trust_seal_sm_124x32.png", "POSDV", "none");
</script>

                            </div>

                        </div>

                    </form>



                </div>

            </div>

            <!-- end card-box-->



          

            

        </div>

        <!-- end wrapper page -->

        


        </div>
        

    	<script>

            var resizefunc = [];

        </script>



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



        <!-- App js -->

        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

	

	</body>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<script language="javascript" type="text/javascript">   

jQuery.noConflict();

$(document).ready(function() {

$("#save").click(function() {

var company_name = $("#company_name").val();

if(company_name=='')

{

	$("#error_company_name").html('Company Name is Required!');

}

var person_name = $("#person_name").val();

if(person_name=='')

{

	$("#error_person_name").html('Person Name is Required!');

}

var contact_number = $("#contact_number").val();

if(contact_number=='')

{

	$("#error_contact_number").html('Contact number is Required!');

}

var email = $("#email").val();

if(email=='')

{

	$("#error_email").html('Email ID is Required!');

}

var password = $("#password").val();

if(password=='')

{

	

	$("#error_password").html('Password is Required!');

}

var logo = $("#logo").val();

if(logo=='')

{

	

	$("#error_logo").html('LOGO is Required!');

}

var colorcode = $("#colorcode").val();

if(colorcode=='')

{

	

	$("#error_colorcode").html('Color Codee is Required!');

}





if(company_name=='' || person_name=='' || contact_number=='' || email=='' || password=='' || logo=='' || colorcode=='')

{

	

	return false;

}



});

});

</script>

</html>