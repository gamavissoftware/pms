<?php


$CI =& get_instance();


$CI->load->model('Store_model');


$restyu=$CI->Store_model->getallusers();


?>


<!DOCTYPE html>


<html>


    <head>


        <meta charset="utf-8">


        <meta name="viewport" content="width=device-width, initial-scale=1.0">


        <meta name="description" content="">


        <meta name="author" content="<?php echo copyright; ?>">





        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">





        <title><?php echo sitetitle; ?> Sales Zone</title>





        <!-- Table Responsive css -->


		<script src="<?php echo assets_url;?>js/angular.min.js"></script>


		 <!-- DataTables -->


        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />


		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>


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


		<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">


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


				font-weight:bold;


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


						 <div class="btn-group pull-right" style="margin-top:30px">


						  <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">Add New Zone</button>


                               


                            </div>


                           


                            <h4 class="page-title">ADD SALES ZONE</h4>


                        </div>


                    </div>


                </div>


                <!-- end page title end breadcrumb -->


				<div class="col-md-12">


	<span style="color:red;text-align:center;"><?php echo $this->session->flashdata('message'); ?></span>


	</div>


		<div class="row">


                    <div class="col-sm-12">


                        <div class="card-box table-responsive">


                            <table id="example" class="table table-striped table-bordered manglesh">


                                <thead>


                                <tr>


                                    <th>Sr No.</th>


                                    <th>Sales Zone</th>


									<th>Users</th>


                                    <th>Action</th>


                                </tr>


                                </thead>








                                <tbody>


								


                                </tbody>


                            </table>


                        </div>


                    </div>


					</div>


                </div>


                <!-- end row -->


 <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">


 <form id="loginForm" method="post" action="<?php echo page_url;?>Master/Saleszone/addzone" onsubmit="return validateme();">


 


  <!---<div id="pageloader">


   <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />


</div>--->


                                <div class="modal-dialog modal-lg">


                                    <div class="modal-content">


                                        <div class="modal-header">


                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>


                                            <h4 class="modal-title">Add New Sales Zone</h4>


                                        </div>


                                        <div class="modal-body">


                                            <div class="row">


                                                <div class="col-md-4">


                                                    <div class="form-group">


                                                        <label for="field-1" class="control-label">Sales Zone</label>


														<span id="error_holidayname" style="color:red;"></span>


                                                        <input type="text" class="form-control" id="zone" style="text-transform: uppercase;" name="zone" placeholder="" value="">


                                                    </div>


                                                </div>


												


												 <div class="col-md-12">


                                                    <div class="form-group">


                                                        <label for="field-1" class="control-label">Assign User</label>


														<span id="error_datepicker1" style="color:red;"></span>


                                                       <select name="assuser[]" id="assuser" multiple class=" select2">


													   <option value=""></option>


													<?php


													





													if(count($restyu)>0)


													{


													foreach($restyu as $restyu1)


													{


													?>


													<option value="<?php echo $restyu1->user_id;?>"><?php echo $restyu1->first_name;?> <?php echo $restyu1->last_name;?></option>


													<?php


													}


													}


													?>


													   </select>


                                                    </div>


                                                </div>


												


                                            </div>


											


											


                                        </div>


                                        <div class="modal-footer">


                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>


                                            <input type="submit" id="save" class="btn btn-info" value="Submit" > 


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


        <script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>


        <!-- Datatable init js -->


        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>


<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>


        <!-- App js -->


        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>


        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>


		


 <script>


$( document ).ready(function() {


	


$('#example').dataTable({


 "bProcessing": false,


 "pagination":true,


 "sAjaxSource": "<?php echo page_url;?>Master/Saleszone/zone_list/",


 "aoColumns": [


						{ mData: 'sr_no' } ,


                        { mData: 'salezone' },


						{ mData: 'users' },


                        { mData: 'edit' }


						


                ]


        });   


});





</script>





<script language="javascript" type="text/javascript">   


function validateme()


{





var zone = $("#zone").val();


if(zone=='')


{


	$("#error_holidayname").html('Required!');


	


}





var assuser = $("#assuser").val();


if(assuser=='' || assuser==null)


{


	$("#error_datepicker1").html('Required!');





}








if(zone=='' || assuser=='' || assuser==null)


{


	


	return false;


}





}


</script>


<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>


<script>


$(document).ready(function(){


	


$(".select2").select2({});


});//document ready


</script>


<script> $(document).ready(function() {








                $("#datepicker1").datepicker();


                $("#datepicker1btn").click(function(event) {


                    event.preventDefault();


                    $("#datepicker1").focus();


                })





            });</script>


</body>


</html>