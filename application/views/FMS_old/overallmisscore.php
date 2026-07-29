<?php

if($this->uri->segment('3')<>'' && $this->uri->segment('4')<>'')

{

$sdate=base64_decode($this->uri->segment('3'));

$edate=base64_decode($this->uri->segment('4'));

}else

{

	$sdate='';

	$edate='';

}

?>

<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="<?php echo copyright; ?>">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title><?php echo sitetitle; ?> MIS SCORE</title>



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

        <link rel="stylesheet" href="<?php echo plugins_url;?>bootstrap-datepicker/css/bootstrap-datepicker3.css"/>

		<?PHP 

$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();

foreach($q->result() as $LOGO);

?>

		<style>

table.manglesh thead th {

				background: <?php echo $LOGO->colorcode;?>;

				color:#fff;

				font-weight:bold;

			}

			

			.backgroundcolorblack

			{

			    

			    background-color:#E3F1FB;

			}

			

</style>

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



              <div class="row">

                    <div class="col-sm-12">

					<div class="col-sm-4">

					<div class="page-title-box">

					<h4 class="page-title">USER WISE MIS SCORE</h4>

					</div>

					</div>

					<div class="col-md-4"></div>

					

					<div class="col-md-4">

					<div class="col-md-12">

					<form action="<?php echo page_url;?>Reporting/filteroverallmis" method="post">

							<div class="col-md-12 card-box">

							<div class="col-md-4">

							<input type="text" name="start" class="form-control datepicker-autoclose" placeholder="START DATE" value="<?php echo $sdate;?>"></div>

							<div class="col-md-4">

							<input type="text" name="end" class="form-control datepicker-autoclose" placeholder="END DATE" value="<?php echo $edate;?>"></div>

							<div class="col-md-4"><input type="submit" class="btn btn-warning">

							</div>

							</div>

							</form>

					

					</div>

					</div>

                    </div>

					

					

                </div>

                <!-- end page title end breadcrumb -->

	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

		<div class="row">

                    <div class="col-sm-12">

                        <div class="card-box table-responsive">

                            <table id="example" class="table table-bordered manglesh">

                                <thead>

                                <tr>

                                    <th>SR NO.</th>

									<th>NAME</th>

                                    <th>PREVIOUS SCORE</th>

									<th>FMS SCORE</th>

                                    <th>CHECKLIST SCORE</th>

                                    <th>DELEGATION SCORE</th>

									<th>FORM SCORE</th>

									<th>TOTAL SCORE</th>

									<th>EM</th>

								<!--	<th>PREVIOUS PLANNED SCORE</th>

									<th>FUTURE PLANNED SCORE</th>

									<th>REMARKS</th>-->

									

									

                                </tr>

                                </thead>





                                <tbody>

								

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

$('#example').dataTable({

 "bProcessing": false,

 "pagination":false,

	fixedHeader: true,

	dom: 'Bfrtip',

        buttons: [

        

            'excelHtml5',

            'print'

            

        ],

 "pageLength": 200,

 "deferRender": true,

 "sAjaxSource": "<?php echo page_url;?>Reporting/misscoreoverall/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>",

 "aoColumns": [

						{ mData: 'sr_no' } ,

                        { mData: 'name' },

						{ mData: 'previousscore' },

						{ mData: 'fmsscore' },

                        { mData: 'checklistscore' },

						{ mData: 'delegationscore' },

						{ mData: 'formscore' },

						

						{ mData: 'totalscore' },

					/**	{ mData: 'previousplannedscore' },

						{ mData: 'futureplannedscore' },**/

						{ mData: 'remarks' } 

	                   

						

                ],"initComplete": function(settings, json) {

  

   getcolors()

  }

        });  

        

        

        

 $('#example').on('draw.dt', function() {

    // do action here



    getcolors();

});  



  $('#example').on('search.dt', function() {

   

    getcolors();

});  

});





 function markemdone(userid,rowid)

            {

            

                

                

                	$.ajax({

		type:"post",

		url:"<?php echo page_url;?>Reporting/emdone",

		data:"userid="+userid+"&sdate=<?php echo $this->uri->segment(3);?>&edate=<?php echo $this->uri->segment(4);?>",

		success:function(data){

	

                if(data=='1')

                {

                $("#example tr").each(function(){

		

	var currentRow=$(this);

	

	    var col1_value=currentRow.find("td:eq(0)").text();

	   col1_value=col1_value.trim();

	  

	if(col1_value==rowid)

	{

		currentRow.addClass("backgroundcolorblack");

	}

				

	



	 

});



                }





		}

		});

                

            }

            

            

</script>



<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>

 <script src="<?php echo plugins_url;?>bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>

<script type="text/javascript">

            $(function () {

                $('.datepicker-autoclose').datepicker({

                    autoclose:"true",

                    orientation: "top",

                    changeMonth: true,

    changeYear: true,

    format: 'dd-mm-yyyy',

    endDate: '+0d'

                });

            });

            

           

           

           function getcolors()

{



 

$("#example tr").each(function(){

		

	var currentRow=$(this);

	

	    var col1_value=currentRow.find("td:eq(8)").text();

	   col1_value=col1_value.trim();

	   

	if(col1_value=='EM DONE')

		{

			currentRow.addClass("backgroundcolorblack");

		}			

	



	 

});

}



        </script>

</body>

</html>