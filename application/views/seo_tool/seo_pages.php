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
         <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
         <link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css">

<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>  
        <link rel="stylesheet" type="text/css" href="<?php echo assets_url;?>css/seo_steps.css">
   <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>  
   <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script> 
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
        <!-- <script src="<?php echo assets_url;?>js/seo_steps.js"></script> -->
		<style>
			.divheight{
			padding-top:100px;
			}

.vertical {
  border-left: 4px solid #A9A9A9;
  height: 150px;
}

.addMore {
margin-top: 22px;
}
.remove {
margin-top: 22px;
}

.addMore2 {
margin-top: 22px;
}
.remove2 {
margin-top: 22px;
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

				<div class="divheight hidden-xs"></div>	
               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">
                            <div class="stepwizard">
                                    <div class="stepwizard-row setup-panel">
                                        <div class="stepwizard-step">
                                            <a href="<?php echo page_url;?>seo_tool/seo_createProject" type="button" class="btn btn-default btn-circle">1</a>
                                            <p>Step 1</p>
                                        </div>
                                        <div class="stepwizard-step">
                                            <a href="<?php echo page_url;?>seo_tool/seo_createProject/addCompanyDetails" type="button" class="btn btn-default btn-circle">2</a>
                                            <p>Step 2</p>
                                        </div>
                                        <div class="stepwizard-step">
                                            <a href="<?php echo page_url;?>seo_tool/seo_createProject/seo_pages" type="button" class="btn btn-primary btn-circle">3</a>
                                            <p>Step 3</p>
                                        </div>
                                </div>
                            </div>
                                
					
                   
                            <!-----Step 3----->
                    <!-- <div class="setup-content" id="step-3"> -->
                        <form id="loginForm" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="row">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
                        
                            <h4 class="page-title text-center">CREATE PAGES</h4>
                        </div>
                    </div>
                </div>
                                <div class="col-md-6">
                                    <div class="row" id="repeat2">
                                        <div class="col-md-10">
                                            <div class="form-group fieldGroup">
                                                <label for="field-1" class="control-label">Company Details</label>
                                                <span id="error_username" style="color:red;">*</span>
                                                <input type="text" id="company_name" name="company_name" class="form-control" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <a href="javascript:void(0)" class="btn btn-success addMore2"><span class="glyphicon glyphicon glyphicon-plus" aria-hidden="true"></span></a>
                                        </div>
                                    </div>
                                </div> 
                            <div class="col-md-6">
                                <div class="col-md-4"></div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">Import Excel File</label>
                                        <input type="file" id="import_excel" name="import_excel" class="form-control" autocomplete="off" value="">
                                    </div>
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">Download a Sample File</label>
                                    </div>
                                </div>
                            </div>  

                                <div class="col-md-12">
                                    <div class="form-group pull-right">
                                        <input type="submit" name="" value="Submit" class="btn btn-primary nextBtn btn-lg pull-right">
                                    </div>                                              
                                </div>
                            </div>
                        </form>  
                        <!-- </div>          -->
										
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

        <!-- Datatables-->
        <script src="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.bootstrap.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.buttons.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/jszip.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/pdfmake.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/vfs_fonts.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/buttons.html5.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/buttons.print.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.fixedHeader.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.keyTable.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.responsive.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.scroller.min.js"></script>

        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

        <script type="text/javascript">


                $('.addMore2').click(function(){
                $('#repeat2').append('<div class="row fieldGroups"> <div class="col-md-10"> <div class="form-group fieldGroup"> <label for="field-1" class="control-label">Company Details</label> <span id="error_username" style="color:red;">*</span> <input type="text" id="company_name" name="company_name" class="form-control" autocomplete="off"> </div></div><div class="col-md-2"> <a href="javascript:void(0)" class="btn btn-danger remove2"><span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span></a> </div></div>');
                });

                $(document).on('click', '.remove2', function(){
                    $(this).parents(".fieldGroups").remove();
                    });
            
        </script>
    </body>
</html>