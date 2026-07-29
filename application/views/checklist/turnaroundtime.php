<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="<?php echo copyright; ?>">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title><?php echo sitetitle; ?> Checklist</title>



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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" rel="stylesheet"/>

		 <?PHP 

$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();

foreach($q->result() as $LOGO);

?>

<style>table.manglesh thead th {

				background: <?php echo $LOGO->colorcode;?>;

				color:#fff;

				font-weight:bold;

				text-align:center;

			}</style>

    </head>





    <body>

<header id="topnav">

<?php $this->load->view('common/nav-menu.php');?>

</header>



<?php $this->load->view('common/info-section.php');?>
<div class="wrapper">



            <div class="container">



                <!-- Page-Title -->



                <div class="row">



                    <div class="col-sm-12">



                        <div class="page-title-box">



						 <div class="btn-group pull-right" style="padding-top:20px">



						  <!--<button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">Add New Turnaround Time</button>-->

                            </div>

                            <h4 class="page-title">Turnaround Time List</h4>



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

									<th>Turnaround Time</th>

									<th>Start Date</th>

									<th>Time Interval</th>

									<th>Frequency</th>

									<th>Status</th>

									<th>Sort Order</th>

									<th>Added On</th>

									<th>Edit</th>



                                </tr>



                                </thead>

<tbody>



								 </tbody>



                            </table>



                        </div>



                    </div>



                </div>



                <!-- end row -->



 <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">



 <form id="loginForm" method="post" action="<?php echo page_url;?>Checklist/turnaroundtime">



                                <div class="modal-dialog">



                                    <div class="modal-content">



                                        <div class="modal-header">



                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>



                                            <h4 class="modal-title">Add New Turnaround Time</h4>



                                        </div>



                                        <div class="modal-body">



                                            <div class="row">



                                                <div class="col-md-6">



                                                    <div class="form-group">

														<label for="field-1" class="control-label">Turnaround Time</label>



														<span id="error_turnaroundtime" style="color:red;"></span>



                                                        <input type="text" class="form-control" id="turnaroundtime" name="turnaroundtime" placeholder="Turnaround Time" style="text-transform:uppercase" required>



                                                    </div>



                                                </div>



												<div class="col-md-6">



                                                    <div class="form-group">



                                                        <label for="field-1" class="control-label">Start Date</label>



														<span id="error_startdate" style="color:red;"></span>



                                                        <input type="text" class="form-control" id="startdate" name="startdate" placeholder="Start Date" style="text-transform:uppercase" value="<?php echo date('d-m-Y');?>" required>



                                                    </div>



                                                </div>

												 <div class="col-md-6">

												<div class="form-group">

												<label for="field-2" class="control-label">Time Interval</label>



												<span id="error_time_interval" style="color:red;"></span>



												<select class="form-control" id="time_interval" name="time_interval" style="text-transform:uppercase" required>



													<option value="">--Select Time Interval--</option>

													<option value="1">Per Day</option>

													<option value="2">Week</option>

													<option value="3">Month</option>

													<option value="4">Year</option>



												</select>



												</div>



											</div>

											

											<div class="col-md-6">



                                                    <div class="form-group">



                                                        <label for="field-1" class="control-label">Frequency</label>



														<span id="error_frequency" style="color:red;"></span>



                                                        <input type="number" class="form-control" id="frequency" name="frequency" placeholder="Frequency" value="" style="text-transform:uppercase" required>



                                                    </div>



                                                </div>

                                                <div class="col-md-6">

												<div class="form-group">

												<label for="field-2" class="control-label">Status</label>



												<span id="error_status" style="color:red;"></span>



												<select class="form-control" id="status" name="status" style="text-transform:uppercase">



													<option value="">--Select Status--</option>



													<option value="1">Active</option>



													<option value="0">Inactive</option>



												</select>



												</div>



											</div>



                                            </div>

</div>



                                        <div class="modal-footer">



                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>



                                            <input type="submit" id="saves" class="btn btn-info" value="Submit"> 



                                        </div>



                                    </div>



                                </div>



								</form>



                            </div><!-- /.modal -->











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

<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

        <!-- Datatable init js -->

        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>



        <!-- App js -->

        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

		







<script>

$( document ).ready(function() {

$('#example').dataTable({

"bProcessing": true,

	fixedHeader: true,

"pagination":true,

"sAjaxSource": "<?php echo page_url;?>Checklist/turnaroundtime_list",

"aoColumns": [

				{ mData: 'sr_no' } ,

				{ mData: 'turnaroundtime' },

				{mData:'date'},

				{mData:'time_interval'},

				{mData:'frequency'},

				{mData:'status'},

				{mData:'sortorder'},

				{mData:'added_on'},

				{ mData: 'edit' }

				

				

				

		]

});   

});



</script>

<script src="<?php echo plugins_url;?>bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>



 <script type="text/javascript">

    //         $(function () {

    //             $('#startdate').datepicker({

    //                 autoclose:"true",

    //                 orientation: "bottom",

    //                 changeMonth: true,
                    
    //                 todayHighlight:true,
		  //      	startDate: '-0m'

    // changeYear: true,

    // format: 'dd-mm-yyyy'

    //             });

    //         });


        $(document).ready(function() {
            $("#startdate").datepicker({
					todayHighlight:true,
		        	startDate: '-0m',
		        	format: 'dd-mm-yyyy'
				});
				
            });
        </script>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>



<script language="javascript" type="text/javascript">   



jQuery.noConflict();



$(document).ready(function() {



$("#saves").click(function() {



var turnaroundtime = $("#turnaroundtime").val();

if(turnaroundtime=='')

{

$("#error_turnaroundtime").html('Required!');

}

var startdate = $("#startdate").val();

if(startdate=='')

{

$("#error_startdate").html('Required!');

}



var time_interval = $("#time_interval").val();

if(time_interval=='')

{

$("#error_time_interval").html('Required!');

}

var frequency = $("#frequency").val();

if(frequency=='')

{

$("#error_frequency").html('Required!');

}



var status = $("#status").val();



if(status=='')



{



	



	$("#error_status").html('Required!');



}







if(turnaroundtime=='' || startdate=='' || time_interval=='' || frequency=='' ||  status=='')



{



	



	return false;



}







});



});

function validate()

{

	 $("#saves").attr('disabled',false);

	$("#saves").val('Submit');



$("#form :input").attr('required',false);



var isValid=0;

$("#form .mand").each(function() {

var element = $(this).val();

if (element=="") {



isValid=1;

}





});

 

 

 if(isValid==0)

 {

	$("#saves").attr('disabled',true);

	$("#saves").val('Please Wait..');

     return true;

	 

 }else

 {

	 $("#saves").attr('disabled',false);

	$("#saves").val('Submit');

     alert('All Fields are mandatory');

     return false;

 }

    

}



</script>



</body>

</html>