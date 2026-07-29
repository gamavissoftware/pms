<?php


$CI =& get_instance();


$CI->load->model('Master_model');


?>


<!DOCTYPE html>


<html>


    <head>


        <meta charset="utf-8">


        <meta name="viewport" content="width=device-width, initial-scale=1.0">


        <meta name="description" content="">


        <meta name="author" content="<?php echo copyright; ?>">





        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">





        <title><?php echo sitetitle; ?> User Permission Report</title>





        <!-- Table Responsive css -->


		<script src="<?php echo assets_url;?>js/angular.min.js"></script>


		 <!-- DataTables -->


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


        <link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">


		<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">


        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->


        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->


        <!--[if lt IE 9]>


        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>


        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>


        <![endif]-->





        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>


 <style>


            .requiredclass{


                border: 2px solid #E12830;


            }


        </style>
 <?PHP 
$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
foreach($q->result() as $LOGO);
?>

		<style>


table.manglesh thead th {


				background: <?php echo $LOGO->colorcode;?>;


				color:#fff;


				font-weight:bold;


				text-align:center;


			}





table.report thead th {


				background: red;


				color:#fff;


				font-weight:bold;


			}


table.report tbody td {


				font-weight:bold;


				color:#000;


			}








.tracksheet th,td


{


	text-align:center;


	font-size:12px;


	width:0.05%;


	padding-right: 10px;


    padding-left: 10px;


	


}





 table{


	 display: block;


	 


  


	


 }





			</style>


    </head>








   <body>


<header id="topnav">


<?php $this->load->view('common/nav-menu.php');?>


</header>


<?php $this->load->view('common/info-section.php');?>

<div class="wrapper" style="padding-top: 110px !important;">





            <div class="container" style="background-color:#fff">











                <!-- Page-Title -->





                <div class="row">


				


				<div class="col-sm-12">





                        <div class="page-title-box">


                            











<div class="col-md-12">


    <h4 class="text-center">USER PERMISSION REPORT</h4><hr>


    


</div>


						


	





	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>


                        </div>





                    </div>								


															





                </div>





                <!-- end page title end breadcrumb -->





		





		<div class="row">


<div class="col-md-12"><div class="col-md-9"></div><div class="col-md-3"><input type="text" name="myInput" id="myInput" class="form-control" placeholder="SEARCH USER" style="text-transform:uppercase"></div></div>


                    <div class="col-sm-12">





                        <div class="card-box table-responsive" style="margin-top:20px;">





<table border="1" width="100%" class="tracksheet" id="user135">


 <thead>


      <tr>


	           <th style="background-color:white; font-weight:bold;width:100px;">USER</th>


			  <th style="background-color:white; font-weight:bold; text-align:center;width:600px;">PERMISSION</th>


			 


			


               


       	  


		


		


      </tr>


	  	    


    </thead>


    <tbody>


	<?php


	$userid=$CI->Master_model->getallsystemusers();


	if(count($userid)>0)


	{


		foreach($userid as $userids)


		{


			$username=$CI->Master_model->getsusername($userids);


			$module=$CI->Master_model->getmodule($userids);


			


	?>


	


		<tr class="private">


		


		<td style="text-align:left;"><?php echo strtoupper($username);?></td>


		<td>


		<div class="text-center">


		<?php


		if(count($module)>0)


		{


		?>


		


		<table border="1" style="width:500px;margin-top:20px;margin-bottom:20px;">


		<tbody>


		<tr style="background-color:white">


		<th style="">MODULE</th>


		<th>SUBMODULE</th>


		<th>PERMISSION</th>


		</tr>


		<?php


		foreach($module as $module1)


		{


			$modulename=$CI->Master_model->getmodulename($module1);


			$submodule=$CI->Master_model->getsubmodule($module1,$userids);


	


		?>


		


		<?php


		if(count($submodule)>0)


		{


			foreach($submodule as $submodules)


			{


				$submodulenames=$CI->Master_model->submodulename($submodules);


				$permi=$CI->Master_model->getpermission($userids,$submodules);


		?>


		


		<tr>


		<td><?php echo $modulename;?></td>





		<td><?php echo $submodulenames;?></td>





		<td><?php echo $permi;?></td>





		</tr>


		<?php


			}


			


		}


	


		}


		?>


		</tbody>


		</table>


		<?php


		}else{ 


		echo "<strong>NO MODULE ASSIGNED</strong>";  


			}


		?>


		</div>


		</td>


		


		</tr>


		


<?php


		}


	  }else


	  {


	


	?>


	<tr>


		<td colspan="2"><strong>NO USER AVAILABLE</strong></td>


		


		</tr>


	


	<?php


	  }


	  ?>


	


	  	


		 


          </tbody>


</table>





                        </div>





                    </div>





                </div>





                <!-- end row -->


                <!-- Footer -->





                <?php $this->load->view('common/footer');?>





                <!-- End Footer -->


            </div> <!-- end container -->





        </div>





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


        <script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>


        <!-- Datatable init js -->


        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>





        <!-- App js -->


        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>


        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>








<script>


$(document).ready(function(){


  $("#myInput").on("keyup", function() {


    var value = $(this).val().toLowerCase();


    $(".tracksheet tr.private").filter(function() {


      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)


    });


  });


});


</script>


<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>


</body></html>