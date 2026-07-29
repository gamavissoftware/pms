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
        <title><?php echo sitetitle; ?> Reject Quotation</title>

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
           <div class="row">
               <div class="col-sm-12 hidden-xs">
                   <a href="javascript:void(0);" class="logo img-responsive" width="50%"><img src="https://pms.shubhampack.in/assets/images/shubhampack.png"></a>
               </div>
           </div>
        	<div class="m-t-40 card-box">
                <div class="text-center">
                    <h5 class="text-uppercase font-bold m-b-0">MARK QUOTATION AS APPROVE OR REJECT</h5>
                </div>
                <div class="panel-body">
				
				
				
                    <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
                    <form method="post" class="form-horizontal m-t-20" action="<?php echo page_url;?>User/putremarktoreject/<?php echo $this->uri->segment(3);?>">

                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="changestatus" id="changestatus" onchange="validate();" required>
                            <option value="">Select Option </option>
                            <option value="1">Approve </option>
                            <option value="2">Reject </option>
                        </select>
                    </div>
                    

                        <div class="form-group" style="display:none;" id="remarksbox">
                            <div class="col-xs-12">
							<label>REMARK</label>
							<span style="color:red;  font-size:12px;" id="error_user_response"></span>
                                <textarea class="form-control" rows="10" name="remarks" id="remarks"></textarea>
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
				

                </div>
            </div>
            <!-- end card-box-->

          
            
        </div>
        <!-- end wrapper page -->
        

        
    	<script>
            var resizefunc = [];
        </script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

<script type="text/javascript">
                        function validate() {

                             $("#remarks").attr('Required',false);  
                           var changestatus = $("#changestatus").val();
                           if(changestatus==2){
                            $("#remarksbox").show();
                            $("#remarks").attr('Required',true);
                           }else{
                            $("#remarksbox").hide();
                            $("#remarks").attr('Required',false); 
                           }
                        }
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


</html>