<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="<?php echo copyright;?>">

<link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

<title><?php echo sitetitle;?> SET PERMISSION</title>
     
    <link href="https://fonts.googleapis.com/css?family=Ropa+Sans" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo assets_url;?>css/custom.css">


        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 
        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

<style>
    .addMore {
            margin-top: 22px;
              }
</style>

    </head>



<body>

<!-- Navigation Bar-->

        <header id="topnav">

          <?php $this->load->view('common/nav-menu');?>

        </header>

        <div class="wrapper">
            <div class="container">

                <!-- Page-Title -->
                <!-- end page title end breadcrumb -->

				
               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">
                    <form id="loginForm" method="post" action="<?php echo page_url;?>Lead_funnel/add_lead_funnel" enctype="multipart/form-data">
						<span style="color:red;"><?php echo $this->session->flashdata('message');?></span>
                             <div class="row">
                                <div class="row">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
                        
                            <h4 class="page-title text-center">ADD WEBSITE FORM</h4> 
                        </div>
                    </div>
                </div>
                
            <div class="row">
                <div class="col-md-4">
                	
	                    <div class="form-group">
	                        <label for="field-1" class="control-label">FORM NAME</label>
	                        <input type="text" class="form-control" name="form_name">
							
	                    </div>
	                    </div>
	                    </div>
	                    
	                    
               	<!--<div class="row">-->
                <!--    <div class="col-md-4">-->
                <!--        <div class="page-title-box">-->
                <!--            <h6 class="page-title" style="font-size:10px;">ADD FIELDS</h6> -->
                <!--        </div>-->
                <!--    </div>-->
                <!--     <div class="col-md-2">-->
                <!--        <div class="page-title-box">-->
                <!--            <h6 class="page-title" style="font-size:10px;">REQUIRED</h6> -->
                <!--        </div>-->
                <!--    </div>-->
                <!--</div>   -->
                
                
            <div class="row" id="repeat">
                <div class="col-md-12">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="field-1" class="control-label">ADD FIELD</label>
                        <input type="text" id="add_field" name="add_field[]" class="form-control" autocomplete="off" value="" required>
                        <span style="color:red;"><?php echo form_error('add_field');?></span>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="field-1" class="control-label">REQUIRED</label>
                        <input type="checkbox" id="required_field" name="required_field[]" autocomplete="off" value="1" required>
                        <span style="color:red;"><?php echo form_error('required_field');?></span>
                    </div>
                </div>
                <div class="col-md-1">
                    <a href="javascript:void(0)" class="btn btn-warning addMore"><span class="glyphicon glyphicon glyphicon-plus" aria-hidden="true"></span></a>
                </div>

            </div>
           </div>
                
         </div>
                            
            <div class="row">
               <div class="col-md-4"> 
                    
                    <input type="submit" value="Submit" id="sub" class="btn btn-success">
                </div>
            </div>
                        </form>
                            <!-- </div> -->
                    
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
        
        <script>
$(document).ready(function(){
  
});//document ready

var i=1;
$('.addMore').click(function(){
    i++;
                $('#repeat').append('<div class="row fieldGroup" id="row'+i+'"><div class="col-md-4"><div class="form-group"><input type="text" id="add_field" name="add_field[]" class="form-control" autocomplete="off" value="" required><span style="color:red;"><?php echo form_error('add_field');?></span></div></div><div class="col-md-1"><div class="form-group"><input type="checkbox" id="required_field" name="required_field[]" autocomplete="off" value="1" required><span style="color:red;"><?php echo form_error('required_field');?></span></div></div><div class="col-md-2"> <a href="javascript:void(0)" class="btn btn-danger remove"><span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span></a> </div></div>');
                });

                $(document).on('click', '.remove', function(){
                    $(this).parents(".fieldGroup").remove();
                    });
</script>
    </body>
</html>