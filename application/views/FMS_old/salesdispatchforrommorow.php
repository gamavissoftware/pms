<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="<?php echo copyright; ?>">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title><?php echo sitetitle; ?> DISPATCH FOR TOMMOROW ORDERS</title>



        <!-- Table Responsive css -->

		<script src="<?php echo assets_url;?>js/angular.min.js"></script>

		 <!-- DataTables -->

        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />

		<link href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.dataTables.min.css">

        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />

		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">

		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 

        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">

		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script>

		<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->

        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

        <!--[if lt IE 9]>

        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>

        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>

        <![endif]-->



        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

		 <?PHP 

$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();

foreach($q->result() as $LOGO);

?>

		<style>

table.manglesh thead th {

				background: <?php echo $LOGO->colorcode;?>;

				color:#fff;

				font-size:11px;

				font-weight:bold;

			}

table tbody tr td {

  font-size: 11px;

  color:#000;

}

#pageloader

{

  background: rgba( 255, 255, 255, 0.8 );

  display: none;

  height: 100%;

  position: fixed;

  width: 100%;

  z-index: 9999;

}

#pageloader img

{

  left: 50%;

  margin-left: -32px;

  margin-top: -32px;

  position: absolute;

  top: 50%;

}

</style>

    </head>

    </head>





    <body>





        <!-- Navigation Bar-->

                <header id="topnav">

          <?php $this->load->view('common/nav-menu');?>

        </header>

        <!-- End Navigation Bar-->


        <?php $this->load->view('common/info-section.php');?> 


        <div class="wrapper">

            <div class="container-fluid">



                <!-- Page-Title -->

                <div class="row">

                    <div class="col-sm-12">

                        <div class="page-title-box">

						<?php 

						$user_id =$this->session->userdata['logged_in']['user_id'];

						?>

                          

                            <h4 class="page-title">YOUR DISPATCH FOR TOMMOROW ORDER LIST</h4>

                        </div>

                    </div>

                </div>

                <!-- end page title end breadcrumb -->

	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

		<div class="row">

                    <div class="col-sm-12">

                        <div class="card-box table-responsive">

                            <table id="example" class="table table-striped table-bordered manglesh">

                                <thead>

                                <tr>

                                    <th>Sr No.</th>

                                    <th>PRINT PACKING LABEL</th>

                                    <th style="width:50%">INSTRUMENTS</th>

                                    <th>COMPANY NAME</th>

                                    <th>ADDRESS WITH PINCODE</th>

                                    <th>INTERNAL ORDER NUMBER</th>

                                    <th>FREIGHT TYPE</th>

                                     <th>ORDER TYPE</th>

                                     <th>REGION MARKETING PERSON</th>

                                     <th>PO NUMBNER</th>

                                    <th>EMAIL ID</th>

									<th>MOBILE NUMBER</th>

									<th>PAYMENT TERMS</th>

									<th>INSTALLATION CHARGES TYPE</th>

                                    <th>PACKING CHARGES</th>

									<th>TIMESTAMP</th>

									<th>PLANNED TIME</th>

									<th>ACTUAL TIME</th>

									<th>REMARKS</th>

									<th>MARK DONE</th>

									<th>DOCKET IMAGE</th>

									

									

                                </tr>

                                </thead>





                                <tbody>

								

                                </tbody>

                            </table>

                        </div>

                        

                         <div class="row card-box table-responsive">

    <h4 class="text-center">

       DISPATCH FOR TOMORROW HISTORY

    </h4>

                             <table id="example1" class="table table-striped table-bordered manglesh">

                                <thead>

                                <tr>

                                    <th>Sr No.</th>

                                    <th>PRINT PACKING LABEL</th>

                                    <th style="width:50%">INSTRUMENTS</th>

                                    <th>COMPANY NAME</th>

                                    <th>ADDRESS WITH PINCODE</th>

                                    <th>INTERNAL ORDER NUMBER</th>

                                    <th>FREIGHT TYPE</th>

                                     <th>ORDER TYPE</th>

                                     <th>REGION MARKETING PERSON</th>

                                     <th>PO NUMBNER</th>

                                    <th>EMAIL ID</th>

									<th>MOBILE NUMBER</th>

									<th>PAYMENT TERMS</th>

									<th>INSTALLATION CHARGES TYPE</th>

                                    <th>PACKING CHARGES</th>

									<th>TIMESTAMP</th>

									<th>PLANNED TIME</th>

									<th>ACTUAL TIME</th>

									<th>REMARKS</th>

									<th>MARK DONE</th>

									<th>DOCKET IMAGE</th>

									

									

                                </tr>

                                </thead>





                                <tbody>

								

                                </tbody>

                            </table>

                        </div>

                    

                    </div>

                </div>

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

        <script src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>

<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>

        <!-- Datatable init js -->

        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>



        <!-- App js -->

        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

		

	

	  <script>

	  function initializeSelect2(selectElementObj) {

         $('.'+selectElementObj).select2({});

         

      }

	  </script>

		<script>

		// Time Picker

            jQuery('#timepicker').timepicker({

                defaultTIme : false

            });

			jQuery('#timepicker4').timepicker({

                defaultTIme : false

            });

            jQuery('#timepicker2').timepicker({

                showMeridian : false

            });

            jQuery('#timepicker3').timepicker({

                minuteStep : 15

            });

		</script>

 <script>

$( document ).ready(function() {

$('.select2').select2({ });

$('.select3').select2({ });

$('.select4').select2({ });

 function initializeSelect2() {

    $('.select3').select2({ });

  }

  

$('#example').dataTable({

 "bProcessing": false,

 "pagination":true,

fixedHeader: true,

   scrollCollapse: true,

   fixedColumns:   {

            leftColumns: 3

        },

 "sAjaxSource": "<?php echo page_url;?>Reporting/dispatchfortommorow_order_listforsales/<?php echo $this->uri->segment(3);?>",

 "aoColumns": [

					 { mData: 'sr_no' } ,

                    { mData: 'packinglabel' },

                    { mData: 'itemname' },

                    { mData: 'company_name' },

                     { mData: 'address' },

                     { mData: 'internal_order_no' },

                     { mData: 'freigntcharges' },

                     { mData: 'order_type' },

                    { mData: 'marketing_person' },

                    { mData: 'po_number' }, 

                   { mData: 'email' },

                    { mData: 'mobile_number' },

                    { mData: 'payment_terms' },

                    { mData: 'installation_charges' },

                    { mData: 'packingcharges' },

                     { mData: 'added_on' },

                    { mData: 'plannedtime' },

                    { mData: 'actualtime' },

                     { mData: 'remarks' },

                    { mData: 'closeorder' },

                    { mData: 'docket' }

                    

						

						

                ]

        });   

        

        $('#example1').dataTable({

 "bProcessing": false,

 "pagination":true,

fixedHeader: true,

   scrollCollapse: true,

   fixedColumns:   {

            leftColumns: 3

        },

 "sAjaxSource": "<?php echo page_url;?>Reporting/dispatchfortommorow_order_listforsales_history/<?php echo $this->uri->segment(3);?>",

 "aoColumns": [

					 { mData: 'sr_no' } ,

                    { mData: 'packinglabel' },

                    { mData: 'itemname' },

                    { mData: 'company_name' },

                     { mData: 'address' },

                     { mData: 'internal_order_no' },

                     { mData: 'freigntcharges' },

                     { mData: 'order_type' },

                    { mData: 'marketing_person' },

                    { mData: 'po_number' }, 

                   { mData: 'email' },

                    { mData: 'mobile_number' },

                    { mData: 'payment_terms' },

                    { mData: 'installation_charges' },

                    { mData: 'packingcharges' },

                     { mData: 'added_on' },

                    { mData: 'plannedtime' },

                    { mData: 'actualtime' },

                     { mData: 'remarks' },

                    { mData: 'closeorder' },

                    { mData: 'docket' }

                    

						

						

                ]

        }); 

});





function markstagedone(orderid,compyname,internalorderno,ser)

{

	if(confirm('Confirm you want to close following order? \nCustomer '+compyname+'\n Internal Order No. '+internalorderno))

	{

		document.location="<?php echo page_url;?>Reporting/markfinalpacked/"+orderid+"/"+ser;

		return true;

		

	}else

	{

		return false;

	}

}

</script>





<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>

<script>

$(document).ready(function(){

  $("#loginForm").on("submit", function(){

    $("#pageloader").fadeIn();

  });//submit

});//document ready

</script>

</body>

</html>