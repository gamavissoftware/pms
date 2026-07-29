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
        <title><?php echo sitetitle; ?> Delegation Response</title>

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

        <div class="account-pages"></div>
        <div class="clearfix"></div>
        <div class="wrapper-page" style="margin: 1% auto;">
            <div class="text-center">
                <a href="javascript:void(0);" class="logo"><img src="<?php echo assets_url;?>images/logo-1.png"></a>

            </div>
        	<div class="m-t-40 card-box">
                <div class="text-center">
                    <h4 class="text-uppercase font-bold m-b-0">WORK DELEGATION RESPONSE FORM</h4>
                </div>
                <div class="panel-body">
				<?php 
				$id = 
				$query = $this->db->select('id, task, case_no,task_status')->from('delegation_task')->where('id',$this->uri->segment(3))->where('task_status','0')->get();
				if($query->num_rows()>0){
					foreach($query->result() as $rows);
				?>
				
				
                    <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
                    <form method="post" class="form-horizontal m-t-20" action="<?php echo page_url;?>User/delegation_response/<?php echo $rows->id;?>">

                        <div class="form-group ">
                            <div class="col-xs-12">
							<span style="color:red; font-size:12px" id="error_email"></span>
							<label>CASE NO</label>
                                <input class="form-control" type="text" placeholder="Email ID" name="email" id="email" value="<?php echo $rows->case_no;?>" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-xs-12">
							<label>USER RESPONSE</label>
							<span style="color:red;  font-size:12px;" id="error_user_response"></span>
                                <textarea class="form-control" rows="10" name="user_response" id="user_response"></textarea>
                            </div>
                        </div>

                        <div class="form-group text-center m-t-30">
                            <div class="col-xs-12">
                                <button class="btn btn-custom btn-bordred btn-block waves-effect waves-light" type="submit" id="save">SUBMIT</button>
                            </div>
                        </div>

                        <div class="form-group m-t-30 m-b-0">
                            <div class="col-sm-12">
                               <p style="font-size:12px;"><?php echo date('Y');?> © Shubham Pack</p>
                            </div>
                        </div>
                    </form>
				<?php }else{?>
				<h3>THIS TASK HAS BEEN COMPLETED OR RE-ASSIGNED TO OTHER PERSON.</h3>
				<?php }?>

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
var user_response = $("#user_response").val();
if(user_response=='')
{
	$("#error_user_response").html('Required!');
}

if(user_response=='')
{
	
	return false;
}

});
});
</script>
</html>