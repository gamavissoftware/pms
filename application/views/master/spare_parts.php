<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright;?>">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title><?php echo sitetitle;?> Spare Parts List</title>
        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
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
        text-align:center;
        }
        table.manglesh tbody td {
        text-align:center;
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

            <div class="container">



                <!-- Page-Title -->

                <div class="row" style="margin-top:20px;">

                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">

                        <div class="page-title-box">

						 <div class="btn-group pull-right">

						  <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">ADD SPARE PARTS</button>
                            </div>
                        <h4 class="page-title">SPARE PARTS LIST</h4>
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
                                    <th>Code</th>  
                                    <th>Revision</th>    
                                    <th>Description</th>                     
                                    <th>Status</th>
                                    <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->

 <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

 <form id="loginForm" method="post" action="<?php echo page_url;?>Master/User_management/add_spare_parts">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">Add Spare Parts</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">

                                               

                                                

											

												

												<div class="col-md-6">

													<div class="form-group">

														 <label for="field-2" class="control-label">Code</label>

														 <span id="error_code" style="color:red;">*</span>

														 <input type="text" class="form-control" name="code" id="code"  value="">

													</div>

												</div>

                                                	<div class="col-md-6">

													<div class="form-group">

														 <label for="field-2" class="control-label">Revision</label>

														 <span id="error_revision" style="color:red;">*</span>

														 <input type="text" class="form-control" name="revision" id="revision"  value="">

													</div>

												</div>

                                                	<div class="col-md-6">

													<div class="form-group">

														 <label for="field-2" class="control-label">Desciption</label>

														 <span id="error_description" style="color:red;">*</span>

														 <input type="text" class="form-control" name="description" id="description"  value="">

													</div>

												</div>

                                                

												

												

												<div class="col-md-6">

												<div class="form-group">

												<label for="field-2" class="control-label">Status</label>

												<span id="error_status" style="color:red;">*</span>

												<select class="form-control" id="status" name="status">

													<option value="">--Select Status--</option>

													<option value="1">Active</option>

													<option value="0">Inactive</option>

												</select>

												</div>

											</div>

                                           

											

                                        </div>

                                        <div class="modal-footer">

                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

                                            <input type="submit" id="rolesave" class="btn btn-info" value="Submit"> 

                                        </div>

                                    </div>

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

"sAjaxSource": "<?php echo page_url;?>Master/User_management/spare_parts_list",

"aoColumns": [

				{ mData: 'sr_no' } ,

				{ mData: 'code' },

                { mData: 'revision' },

                { mData: 'description' },

				{ mData: 'status' },

				{ mData: 'edit' }

				

				

		]

});   

});



</script>

		

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<script>

$(document).ready(function(){

	   $("#rolesave").attr('disabled',false);

	   $("#rolesave").val('submit');

  $("#loginForm").on("submit", function(){

   // $("#pageloader").fadeIn();

   $("#rolesave").attr('disabled',true);

     $("#rolesave").val('Please Wait...');

  });//submit

});//document ready

</script>

<script language="javascript" type="text/javascript">   

jQuery.noConflict();

$(document).ready(function() {

$("#rolesave").click(function() {



var code = $("#code").val();

if(code=='')

{

	

	$("#error_code").html('Required!');

}


var revision = $("#revision").val();

if(revision=='')

{

	

	$("#error_revision").html('Required!');

}

var description = $("#description").val();

if(description=='')

{

	

	$("#error_description").html('Required!');

}



var status = $("#status").val();

if(status=='')

{

	

	$("#error_status").html('Required!');

}





if(code=='' || status=='' || revision=='' || description=='' )

{

	

	return false;

}



});

});

</script>

    </body>

</html>