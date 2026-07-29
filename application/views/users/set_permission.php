<?php 
$business_location = $this->session->userdata['logged_in']['business_location'];
?><!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="<?php echo copyright;?>">

<link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

<title><?php echo sitetitle;?> SET PERMISSION</title>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>



        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 

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





        <div class="wrapper" style="background-color:white">

            <div class="container">



              <div class="row">



                    <div class="col-sm-12">



                        <div class="page-title-box">



                            <div class="btn-group pull-right">

</div>

<?php

$user_id=$this->uri->segment(4);

$userd=$this->db->select('user_id, first_name, last_name, title')->from('system_users')->where('user_id',$user_id)->get();

foreach($userd->result() as $users);



?>



	                            <h4 class="page-title text-center">Set Permission for <?php echo $users->title;?> <?php echo $users->first_name;?> <?php echo $users->last_name;?>  </h4><hr>



                        </div>



                    </div>



                </div>



                <!-- end page title end breadcrumb -->











<form name="frm"  action="<?php echo page_url;?>Master/User_management/assigncapabilities/<?php echo $user_id;?>" method="post">

<div clss="container">

 <?php

	$que=$this->db->select('*')->from('system_modules')->where('status',1);
    if($business_location==1){
        $this->db->where('dynachem',1);
    }
    if($business_location==2){
        $this->db->where('shubhampack',2);
    }
    $que = $this->db->order_by('id','ASC')->get();

	$i=0;

	foreach($que->result() as $sysmodule)

	{

	?>

             <div class="row">

             

	<div class="col-md-3" style="text-align:right;color:red">

     <input type="hidden" name="module[]" value="<?php echo $sysmodule->id;?>">

    <?php echo $sysmodule->modulename;?> <input type="radio" name="moduleaccess<?php echo $sysmodule->id;?>" id="module<?php echo $sysmodule->id;?>" onChange="makesubmoduleactive(<?php echo $sysmodule->id;?>);" value="1"> Y &nbsp;&nbsp;&nbsp;

    <input type="radio" name="moduleaccess<?php echo $sysmodule->id;?>" onChange="makesubmoduleactive(<?php echo $sysmodule->id;?>);" checked value="0"> N

   

    

    

      <script>

	  function makesubmoduleactive(id)

	  {

		  if(document.getElementById('module'+id).checked) {

			  

			   $('#show_detail'+id).fadeIn('slow');

		  }else

		  {

			  $('#show_detail'+id).fadeOut('slow');

		  }

		  

	  }

                                                </script>

                                                

    </div>

    <div class="col-md-9" style="border-left:1px solid grey;display:none"  id="show_detail<?php echo $sysmodule->id;?>">

    

    <?php

	$this->db->select('*')->from('submodule')->where('moduleid',$sysmodule->id);
   $this->db->where('shubhampack',2);
    $submod=$this->db->where('status','1')->get();

	foreach($submod->result() as $subbmod)

	{

	

	?>

   	<div class="row">

    	<div class="col-md-3">

        	<?php echo $subbmod->submodule;?>

            <input type="hidden" name="submodule<?php echo $sysmodule->id?>[]" value="<?php echo $subbmod->id;?>">

        </div>

        <div class="col-md-9">

        	<div class="row">

            	<div class="col-md-4">Allow <input type="checkbox" name="add<?php echo $sysmodule->id;?><?php echo $subbmod->id;?>" value="1"></div>

                <div class="col-md-4" style="display: none">Edit <input type="checkbox" name="edit<?php echo $sysmodule->id?><?php echo $subbmod->id;?>" value="1"></div>

                <div class="col-md-4" style="display:none">Remove <input type="checkbox" name="remove<?php echo $sysmodule->id;?><?php echo $subbmod->id;?>" value="1"></div>

            </div>

        </div>

    </div>

    <?php

	}

	?>

    

    

    </div>

</div>

<hr style="color:grey;">

<?php

	}

	?>

<div style="clear:both;height:50px"></div>

  <div class="row">

   <div class="col-md-12 text-center">

            <input type="submit" value="Set Permission" class="btn btn-sm btn-success">

              </div> 

  </div>

  <div style="clear:both;height:20px"></div>



</div>

                <!-- end row -->

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

<!-- App js -->

        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<script language="javascript" type="text/javascript">   



$(document).ready(function() {

$("#save").click(function() {

	var question= $("#question").val();

if(question=='')

{

	$("#error_question").html('Required!');

}





if(question=='')

{

	

	return false;

}



});

});

</script>

    </body>

</html>