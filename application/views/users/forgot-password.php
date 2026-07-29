<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright;?>">

        <!-- App Favicon -->
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <!-- App title -->
        <title><?php echo sitetitle;?> Reset Your Password</title>

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
        <?php 
        $q=$this->db->select('image')->from('loginpage_image')->get();		if($q->num_rows()>0){
        foreach($q->result() as $bgimg);
        $img = $bgimg->image;		}else{			$img = "";		}
        ?>
        <style>
        .account-pages{
            background: url("<?php echo homepage_image;?><?php echo $img;?>") center;
            position: absolute;
            height: 100%;
            width: 100%;
            background-size: cover;
        }
           
        </style>
    </head>
    <body>

        <div class="account-pages"></div>
        <div class="clearfix"></div>
        <div class="wrapper-page" style="margin: 1% auto;">
            <div class="text-center">
               <a href="javascript:void(0);" class="logo"><img src="<?php echo assets_url;?>images/companylogo.png" style="width:60%"></a>

            </div>
        	<div class="m-t-40 card-box">
                <div class="text-center">
                    <h4 class="text-uppercase font-bold m-b-0">Recover your Password</h4>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal m-t-20" action="<?php echo page_url;?>User/forgot_password" method="post">

                        <div class="form-group ">
                            <div class="col-xs-12">
							<span style="color:red; font-size:12px" id="error_email"></span>
                                <input class="form-control" type="text" required="" placeholder="Email ID" name="email" id="email" value="">
                            </div>
                        </div>

                      
                        <div class="form-group text-center m-t-30">
                            <div class="col-xs-12">
                                <button class="btn btn-custom btn-bordred btn-block waves-effect waves-light" type="submit" id="save">Send</button>
                            </div>
                        </div>

                        <div class="form-group m-t-30 m-b-0">
                            <div class="col-sm-12">
                                <a href="<?php echo page_url;?>" class="text-muted"><i class="fa fa-user m-r-5"></i> Login your Account?</a><br><br>
								
								<p style="font-size:12px;"><?php echo date('Y');?> © <?php echo sitetitle;?> Designed & Developed By <?php echo copyright;?></p>
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
var email = $("#email").val();
if(email=='')
{
	$("#error_email").html('Email ID is Required!');
}

if(email=='')
{
	
	return false;
}

});
});
</script>
</html>