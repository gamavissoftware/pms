<?php 
//echo "<pre>"; print_r($_SESSION['logged_in']); exit;
$user_id = base64_decode($this->uri->segment(3));
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright;?>">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title><?php echo sitetitle;?>Download Pictures</title>
        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>plugins/audio/manage-audio.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
        <style>
            table.manglesh thead th {


                background: #332b24;

                text-align: center;
                color:#fff;


                font-weight:bold;


            }

             table.manglesh tbody td {


                text-align: center;
                font-weight:bold;


            }


				#mybutton {
				  position: fixed;
				  bottom: -4px;
				  right: 10px;
				}
				.select2-container {
				    width: 100% !important;
				}

			   .add_more {
		        margin-top: 33px;
		        }

		        .remove {
		        margin-top: 33px;
		        }

		        .delete_product {
		        margin-top: 33px;
		        }
			</style>
			  <style>
        .select2-container--default .select2-selection--single
        {
            height: 34px !important;
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
            <div class="row" style="margin-top:20px;">
                <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
	                <div class="page-title-box">
	                    <!-- <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success" name="action_button"><i class="fa fa-arrow-left"></i>Back</button></a> -->
					 <div class="btn-group pull-right"></div>
	                   
	                    <h4 class="page-title text-center">Customer Feedback On Demo</h4>
	                </div>
                </div>
            </div>
                <!-- end page title end breadcrumb -->
        <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

        <div class="row">
            <div class="col-xs-12">
                <div class="card-box">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12 col-md-12">


                            <?php 
                                 $qq = $this->db->select('company_name')->from('leads')->where('id',$this->uri->segment(3))->get();
                                 foreach ($qq->result() as $row);

                            ?>

                            <h4 class="text-center"><?php echo $row->company_name;?> Demo Pictures Product wise </h4><hr>



                            </div>
                            <?php 
                            $i=1;
                                $q = $this->db->select('a.id, b.instruments_name, a.product_id')->from('lead_demo_feedback a')->join('presto_instruments b','a.product_id=b.id')->where('a.lead_id',$this->uri->segment(3))->get();
                                if($q->num_rows()>0){
                                    foreach($q->result() as $row){
                            ?>
                            <div class="col-md-12">
                                <h5 class="text-center"><?php echo $row->instruments_name;?></h5>
                                <table class="table table-bordered manglesh">
    <thead>
      <tr>
        <th>Pre Image</th>
        <th>Post Image</th>
        
      </tr>
    </thead>
    <tbody>
        <?php 
        $a=1;
            $q = $this->db->select('picture, post_picture')->from('demo_feedback_photos')->where('demo_id',$row->id)->where('product_id',$row->product_id)->get();
            if($q->num_rows()>0){
                foreach($q->result() as $row1){
        ?>
      <tr>
        <td><img src="<?php echo sfdocument;?>demofiles/<?php echo $row1->picture;?>" width="50%"></td>
        <td><img src="<?php echo sfdocument;?>demofiles/<?php echo $row1->post_picture;?>" width="50%"></td>
    </tr>
        <?php $a++;}}?>
    
    </tbody>
  </table> 
                            </div>

                        <?php $i++; } }?>

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
         <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
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

      

        <!-- App js -->

        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

 

		<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

        <script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo assets_url;?>plugins/audio/manage-audio.js"></script>

<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
    </body>
</html>