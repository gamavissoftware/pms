<?php

$CI =& get_instance();

$CI->load->model('Fms_model');

$showdetail=$CI->Fms_model->showpendingitemdetail($this->uri->segment('3'));

?>

<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="NJ Media">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title>Order Details</title>



        <!-- Table Responsive css -->

		<script src="<?php echo assets_url;?>js/angular.min.js"></script>

		 <!-- DataTables -->

        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />

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

		<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->

        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

        <!--[if lt IE 9]>

        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>

        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>

        <![endif]-->



        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

		<style>

table.manglesh thead th {

				background: #003366;

				color:#fff;

				font-weight:bold;

			}

				

			

		

			

			th, td { text-align: center; }

			

			.backgroundcolor{ background-color:#FCDCB1 !important; }

			.backgroundcolorserrvice{ background-color:#FEFDCD !important; }

			.backgroundcolorpurple{ background-color:#efe0ff !important; }



</style>

    </head>

 



    <body>





        <!-- Navigation Bar-->

                <header id="topnav">

          <?php $this->load->view('common/nav-menu');?>

        </header>

        <!-- End Navigation Bar-->





        <div class="wrapper">

            <div class="container-fluid">



                <!-- Page-Title -->

                <div class="row">

                    <div class="col-sm-12">

                        <div class="page-title-box col-md-9">

						 <!--<div class="btn-group pull-right" style="margin-top:30px">

						 <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">Add New FMS Flow</button>

                               

                            </div>-->

							<?php

									$ffl=$this->db->select('fms_flow')->from('fms_flow')->where('flow_id',$this->uri->segment(3))->get();

									if($ffl->num_rows()>0)

									{

									foreach($ffl->result() as $ffl1);



									$fms=$ffl1->fms_flow;



									}else{

									$fms="";

									}

									

								?>

                           

                            <h4 class="page-title"><span ><?php echo $fms;?></span> PENDING ORDER DETAILS</h4>

                        </div>

                        <div class="col-md-3 card-box">

                            

        <table class="table table-bordered">

		<thead>

		<tr>

		<th colspan="10" style="text-align:center;">Legend- Color Indicator</th>



		</tr>



		</thead>

		<tbody>

    	<tr>

		<td style="background-color:#FCDCB1;"></td>

		<td style="width: 117px;">Sales Order<br/><span id="sorder" style="font-weight: bold;"></span></td>

        <td style="background-color:#FEFDCD;"></td>

		<td style="width: 117px;">Service Order</td>

        <td style="background-color:#fff;"></td>

		<td style="width: 117px;">Presto Internal Order</td>

		 <td style="background-color:#efe0ff;"></td>

		<td style="width: 117px;">Testronix Order</td>

		</tr>



		</tbody>

		</table>

                            

                            

                        </div>

                    </div>

                </div>

                <!-- end page title end breadcrumb -->

	<span ><?php echo $this->session->flashdata('message'); ?></span>

		<div class="row">

                    <div class="col-sm-12">

                        <div class="card-box table-responsive">

                            <table id="example" class="table table-bordered manglesh">

                                <thead>

                                <tr>

                                    <th>SR NO.</th>

									<th>TIMESTAMP</th>

									<th>ORDER TYPE</th>

                                    <th>JOB CARD NO.</th>

                                    <th>INSTRUMENT NAME</th>

                                    <th>PLANNED DATE</th>

									<th>TOTAL DAYS</th>

                                    <?php

                                    if($this->uri->segment(3)=='8' || $this->uri->segment(3)=='10' ){ ?>

                                    <th>ITEM DETAILS</th>

                                    <th>INWARD DETAILS</th>

                                    <?php } ?>

                                </tr>

                                </thead>





                                <tbody>

								

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

                <!-- end row -->

 

		<?php

		if($showdetail=='1')

		{

		?>



		<div class="row">

		<div class="col-sm-12">

		<h3>Items PR Details</h3>

		</div>

		</div>

		<div class="row">

		<div class="col-sm-12">

		<div class="card-box table-responsive">

		<table id="example12" class="table table-bordered manglesh">

		<thead>

		<tr>

		<th>SR NO.</th>

		<th>ORDER TYPE</th>

		<th>JOB CARD NO.</th>

		<th>INSTRUMENT NAME</th>

		<th>PR GENERATED</th>

		<th>ITEM DETAILS</th>

		</tr>

		</thead>

		<tbody>

		</tbody>

		</table>

		</div>

		</div>

		</div>

		<!-- end row -->



		<?php

		}

		?>





<?php

if($this->uri->segment(5)=='')

{

?>

	<div class="row">

	    <div class="col-md-12 page-title-box">

						 <div class="btn-group pull-right" style="margin-top:30px">

						  <!--<button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">Add New FMS Flow</button>-->

                               

                            </div>

							<?php

									$ffl=$this->db->select('fms_flow')->from('fms_flow')->where('flow_id',$this->uri->segment(3))->get();

									if($ffl->num_rows()>0)

									{

									foreach($ffl->result() as $ffl1);



									$fms=$ffl1->fms_flow;



									}else{

									$fms="";

									}

									

								?>

                           

                            <h4 class="page-title"><span ><?php echo str_replace("NOT",'',$fms);?></span> COMPLETED ORDER DETAILS (PAST 20 DAYS)</h4>

                        </div>

                    <div class="col-sm-12">

                        <div class="card-box table-responsive">

                            <table id="example1" class="table table-striped table-bordered manglesh">

                                <thead>

                                <tr>

                                    <th>SR NO.</th>

									<th>TIMESTAMP</th>

                                    <th>JOB CARD NO.</th>

                                    <th>INSTRUMENT NAME</th>

                                    <th>PLANNED DATE</th>

                                    <th>ACTUAL DATE</th>

                                    <th>DIFFERENCE</th>

									<?php

									if($this->uri->segment(3)=='7' || $this->uri->segment(3)=='9')

									{

									?>

									<th style="width:100px">OUT ITEM DETAILS</th>

									<?php

									}

									?>

									

										<?php

									if($this->uri->segment(3)=='8' || $this->uri->segment(3)=='10')

									{

									?>

									<th style="width:100px">OUT ITEM DETAILS</th>

									<th style="width:100px">IN ITEM DETAILS</th>

									<th style="width:100px">DIFF</th>

									<th style="width:100px">DAYS TAKEN BY VENDOR</th>

									

									<?php

									}

									?>

									

									

									

                                </tr>

                                </thead>





                                <tbody>

								

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>



<?php

}

?>

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

<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>

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

 "bProcessing": false,

 "pagination":true,

	fixedHeader: true,

	"pageLength": 100,

 "sAjaxSource": "<?php echo page_url;?>FMS/planned_actual_pendinglist/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>",

 "aoColumns": [

						{ mData: 'sr_no' } ,

                        { mData: 'timestamp' },

                        { mData: 'ordertype' },

						{ mData: 'orderno' },

                        { mData: 'instrumentname' },

						{ mData: 'plandate' },

						{ mData: 'totaldays' }

                        <?php

                        if($this->uri->segment(3)=='8' || $this->uri->segment(3)=='10' )

                        {

                        ?>

                        ,{ mData: 'itemdetail' }

                         ,{ mData: 'inwarddetails' }

                        <?php

                        }

                        ?>

						

                ],"initComplete": function(settings, json) {

    //alert('hi');

			getcolors();

   

  }

        });

        

           $('#example').on('draw.dt', function() {

    // do action here



    getcolors();

});  



  $('#example').on('search.dt', function() {

   

    getcolors();

});  



        

			$('#example1').dataTable({

			"bProcessing": false,

			"pagination":true,

			fixedHeader: true,

			"pageLength": 100,

			"sAjaxSource": "<?php echo page_url;?>FMS/planned_actual_completelist/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>",

			"aoColumns": [

			{ mData: 'sr_no' } ,

			{ mData: 'timestamp' },

			{ mData: 'orderno' },

			{ mData: 'instrumentname' },

			{ mData: 'plandate' },

			{ mData: 'completed' },

			{ mData: 'donediff' }

			<?php

			if($this->uri->segment(3)=='7'  || $this->uri->segment(3)=='9')

			{

			?>

			,{ mData: 'itemdetails' }



			<?php

			}

			?>



			<?php

			if($this->uri->segment(3)=='8'  || $this->uri->segment(3)=='10')

			{

			?>

			,{ mData: 'itemdetails' }

			,{ mData: 'outitemdetails' }



			,{ mData: 'diff' }

			,{ mData: 'vendortime' }



			<?php

			}

			?>



			],"initComplete": function(settings, json) {

			//  alert('hi');





			}

			});  

			

			



			$('#example12').dataTable({

			"bProcessing": false,

			"pagination":true,

			fixedHeader: true,

			"pageLength": 100,

			"sAjaxSource": "<?php echo page_url;?>FMS/planned_actual_pendinglistwithitemdetails/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>",

			"aoColumns": [

			{ mData: 'sr_no' } ,

	

			{ mData: 'ordertype' },

			{ mData: 'orderno' },

			{ mData: 'instrumentname' },

			{ mData: 'itemdetail' },

			{ mData: 'inwarddetails' }

			],"initComplete": function(settings, json) {

			//  alert('hi');





			}

			});  







	

 

	

});



	 

function getcolors()

{



$("#example tr").each(function(){

		

	var currentRow=$(this);

	

	    var col1_value=currentRow.find("td:eq(2)").text();

	   col1_value=col1_value.trim();

		if(col1_value=='SALE' || col1_value=='sale' )

		{

			currentRow.addClass("backgroundcolor");

		} 

		

			if(col1_value=='SERVICE' || col1_value=='service' )

		{

			currentRow.addClass("backgroundcolorservice");

		} 



		if(col1_value=='SALE Testronix' || col1_value=='SERVICE Testronix' || col1_value=='sale Testronix' || col1_value=='service Testronix')

		{

			



			currentRow.addClass("backgroundcolorpurple");

		}



	



	 

});



getsalescount();

}



function getsalescount()

{



    var numItems = $('.backgroundcolor').length;

    $("#sorder").html(numItems);







}



function showalldata(jbcard)

{

$(".showmorejbcard"+jbcard).css('display','');

$(".closetoggle"+jbcard).css('display','none');

}

</script>





<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>

</body>

</html>

