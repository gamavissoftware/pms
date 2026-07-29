<?php

$CI =& get_instance();

$CI->load->model('Delegation_model');

?><!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="NJ Media">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title>Prestogroup Delegation Audit Report</title>



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

		<style>

table.manglesh thead th {

				background: #003366;

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

.feedback {

  background-color : #31B0D5;

  color: white;

  padding: 10px 20px;

  border-radius: 4px;

  border-color: #46b8da;

}

			</style>

    </head>





   <body>

<header id="topnav">

<?php $this->load->view('common/nav-menu.php');?>

</header>



<div class="wrapper">



        <div class="container-fluid">
            <div class="desc-box">
                <div class="row">
                    <div class="col-sm-2">
                        <img src="<?php echo dashboard_icon; ?>Auditor Delegation Report.jpg" style="    width: 100%;">
                    </div>
                    <div class="col-sm-8">
                        <h6>Auditor Delegation Report 
</h6>
                        <p>In this section of the dashboard, the auditor will be able to see the list of employees and their total delegated tasks. The auditor can also do a follow up of tasks as well.
</p>
                    </div>
                    <div class="col-sm-2">
                        <div class="text-center"><a href="https://vimeo.com/prestogroup/review/593689918/b6b4ddb8e1?sort=alphabetical&direction=asc
">
                                <!-- <i class="fa fa-video-camera" aria-hidden="true"></i>  -->
                                <img src="<?php echo dashboard_icon; ?>header_icon.png" style="width: 40%; margin-top: 50px;">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>


            <div class="container-fluid" >







                <!-- Page-Title -->



                <div class="row">

				

				<div class="col-sm-12">



                        <div class="page-title-box">

                            







<div class="col-md-12">

    <h4 class="text-center">DELEGATION AUDIT REPORTING DASHBOARD</h4><hr>

    

</div>

						

	



	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

                        </div>



                    </div>								

															



                </div>



                <!-- end page title end breadcrumb -->



		



		<div class="row">



                    <div class="col-sm-12">



                        <div class="card-box table-responsive">

<table id="example" class="table table-striped table-bordered manglesh">

<thead>

                                <tr><th>SR NO.</th>

									<th>EMPLOYEE NAME</th>

									<th>TOTAL TASK DELEGATED TO SOMEONE</th>

									<th>TOTAL TASK DELEGATED</th>

									<th>CURRENT WEEK MIS SCORE</th>

									<th>LAST WEEK MIS SCORE</th>

									

									

                                </tr>

                                </thead>

								<tbody>

								<?php 

								$k=1;

								$query = $this->db->select('user_id, first_name, last_name')->from('system_users_view')->where('user_status','1')->get();

								foreach($query->result() as $row){

									

						/** current week **/

						$taskdelegatedtomescore=$CI->Delegation_model->currentweekmisfortaskdelegatedtome($row->user_id);

						$taskdelegatedbymescore=$CI->Delegation_model->currentweekmisforfollowuptakenontaskdelegatedbyme($row->user_id);

						$delegationoverallscore=$taskdelegatedtomescore+$taskdelegatedbymescore;

						$delegationoverallscore=$delegationoverallscore;

						/** END **/

						

							/** Previous week **/

							$taskdelegatedtomescoreprevious=$CI->Delegation_model->previousweekmisfortaskdelegatedtome($row->user_id);

							$taskdelegatedbymescoreprevious=$CI->Delegation_model->previousweekmisforfollowuptakenontaskdelegatedbyme($row->user_id);

							$delegationoverallscoreprevious=$taskdelegatedtomescoreprevious+$taskdelegatedbymescoreprevious;

							$delegationoverallscoreprevious=$delegationoverallscoreprevious;

							/** END **/

								?>

								<tr>

									<td class="text-center"><?php echo $k;?></td>

									<td class="text-center"><?php echo $row->first_name;?> <?php echo $row->last_name;?></td>

									<td class="text-center"><?php $this->db->select('id')->from('delegation_task_view')->where('yourname',$row->user_id)->where('task_status','0');

									$query = $this->db->get();

									$cnt = count($query->result());

									echo $cnt;?>

									</td>

									<td><?php $this->db->select('id')->from('delegation_task_view')->where('delegate_to',$row->user_id)->where('task_status','0');

									$query = $this->db->get();

									$cnt = count($query->result());

									echo $cnt;?></td>

									<td class="text-center"><?php echo $delegationoverallscore;?></td>

						<td class="text-center"><?php echo $delegationoverallscoreprevious;?></td>

								</tr>

								<?php $k++;}?>

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

$( document ).ready(function() {

$('.select2').select2({ });

$('.select3').select2({ });

$('.select4').select2({ });

$('#example').dataTable({

"bProcessing": true,

	fixedHeader: true,

"lengthMenu": [[100, 200, -1], [100, 200, "All"]],

fixedHeader:{

    header:true,

    headerOffset:$('#topnav').outerHeight()

},

"searchHighlight": true,

"pagination":true

});   

});

</script>

<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>

</body></html>