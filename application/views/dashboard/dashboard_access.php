<?php 
$CI =& get_instance();
$CI->load->model('Dashboard_model');
$getUsername = $CI->Dashboard_model->getUsername($this->uri->segment(3));
$business_location = $this->session->userdata['logged_in']['business_location'];
?>

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


    </head>



<body>

<!-- Navigation Bar-->

        <header id="topnav">

          <?php $this->load->view('common/nav-menu');?>

        </header>
                       <!-- Page-Title -->


                <!-- end page title end breadcrumb -->

<div class="wrapper">

            <div class="container">
        <!-- End Navigation Bar-->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">\
                        <div class="page-title-box">
                          <h4 class="page-title text-center">Create Dashboard for <?php echo $getUsername;?></h4>
                        </div>
                    </div>
                </div>
<div class="row">
            <div class="col-md-12">
           
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
    <div class="draggable-main">        
                <div class="col-md-6" style="border-right: 2px dotted black;">
                    <h1 style="font-size: 20px;">Dashboard Modules</h1>
                  <div class="box" style="overflow-y: auto; ">
                    <ul class="connected-sortable draggable-left">
                        <?php
                        //echo "<pre>"; print_r($dashboardModules); exit;
                            if($dashboardModules!=''){
                         foreach($dashboardModules as $modules) {?>
                      <li><?php echo $modules->sub_module_name;?><input type="hidden" name="module[]" value="<?php echo $modules->moduleid;?>"><input type="hidden" name="submodule[]" value="<?php echo $modules->submoduleid;?>"></li>
                      <?php } } ?>
                    </ul>
                  </div>
              </div>
              
             <form method="post" action="<?php echo page_url;?>Dashboard/add_dashboard_access/<?php echo $this->uri->segment(3);?>">
            
              <div class="col-md-6">
             
                  <h1 style="font-size: 20px; ">Access Permission</h1>
                
                  <div class="box" style="height:350px; overflow-y: auto; ">
                    <ul class="connected-sortable draggable-right">
                     <?php
                     if(!empty($dashboardAccessModules)) {
                     foreach($dashboardAccessModules as $access) {?>
                        <li><?php echo $access->sub_module_name;?><input type="hidden" name="module[]" value="<?php echo $access->moduleid;?>"><input type="hidden" name="submodule[]" value="<?php echo $access->submoduleid;?>"></li>
                     <?php } } ?>
                    </ul>
                  </div>
                  <div class="text-center">
                  <input type="submit" class="btn btn-success" value="Submit" style="margin-top:30px;">

                     </div>
                     </div>
                  </form> 
              
              <div class="col-md-4">
               
              </div>
              </div> 
              </div>
              
        </div>
      </div>
      
    </div>

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

<!-- App js -->

        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


<script language="javascript" type="text/javascript">


    $(".draggable-left, .draggable-right").sortable({
  connectWith: ".connected-sortable",
  stack: ".connected-sortable ul"
}).disableSelection();

</script>
    </body>

</html>