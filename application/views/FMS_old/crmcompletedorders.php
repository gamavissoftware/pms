<?php

$CI =& get_instance();

$CI->load->model('Fms_model');

$rest=$CI->Fms_model->getzones();

$zone=$this->uri->segment(3);

?>

<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="<?php echo copyright; ?>">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title> <?php echo sitetitle; ?> ORDERS READY FOR DISPATCH</title>



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

						

                           <div class="col-md-8">

                            <h4 class="page-title"><?php $user_id =$this->session->userdata['logged_in']['user_id'];

									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','178')->where('submodule_access','1')->get();

									if($qry->num_rows()>0){

									?>VIEW YOUR <?php }else{?><?php }?>  ORDERS READY FOR DISPATCH</h4>

									</div>

									

									<?php

						$user_id =$this->session->userdata['logged_in']['user_id'];

						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','178')->where('submodule_access','1')->get();

						if($qry->num_rows()==0){

						?>

						<div class="col-md-4 pull-right" style="margin-bottom:10px;">

						<h5>Filter by Zone</h5>

							<select class="form-control" onchange="getzonewisedata(this.value);">

							<option value="" <?php if($zone==''){ ?> selected <?php } ?>>ALL ZONE</option>

							<?php

							if(count($rest)>0)

							{

								foreach($rest as $rest1)

								{

								

							?>

							<option value="<?php echo $rest1->id;?>" <?php if($zone==$rest1->id){ ?> selected <?php } ?>><?php echo $rest1->zone;?></option>

							<?php

								}

							}

							?>

							

							

							</select>

							</div>

						<?php

						}

						?>

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

									<th>Mark Done</th>

									<th>ADDED ON</th>

									<th style="width:50%">INSTRUMENTS</th>

                                    <th>ORDER TYPE</th>

                                    <th>REGION MARKETING PERSON</th>

                                    <th>PO NUMBNER</th>

                                    <th>COMPANY NAME</th>

									<th>ADDRESS WITH PINCODE</th>

									<th>EMAIL ID</th>

									<th>MOBILE NUMBER</th>

									<th>INTERNAL ORDER NUMBER</th>

									<th>DISCOUNT</th>

									<th>ORDER VALUE AFTER DISCOUNT</th>

									<th>ADVANCE AMOUNT RECEIVED</th>

									<th>PAYMENT TERMS</th>

									<th>INSTALLATION CHARGES TYPE</th>

									<th>PACKING CHARGES</th>

									<th>FREIGHT TYPE</th>

									<th>REMARKS</th> 

									

									

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

		

		<script type="text/javascript">

$(document).ready(function(){

	





 var i=2;

 $('#addmore_btn1').click(function(){

 i++;

 

 $('#dynamictasks1').append('<div id="row'+i+'" class="row"><div class="col-md-9"><div class="form-group"><label for="field-1" class="control-label">INSTRUMENT NAME</label><span id="error_instruments" style="color:red;">*</span><select class="form-control select3'+i+'" style="text-transform: uppercase;" name="instruments[]" id="instruments'+i+'"><option value="">--SELECT INSTRUMENT--</option><?php $query = $this->db->select('id, instruments_name, status')->from(' presto_instruments')->where('status','1')->get();foreach($query->result() as $instruments){?><option value="<?php echo $instruments->id;?>"><?php echo trim(strtoupper($instruments->instruments_name));?></option><?php }?></select></div></div><div class="col-md-2"><div class="form-group"><label for="field-1" class="control-label">QUANTITY</label><input type="text" class="form-control" id="qty" name="qty[]" placeholder="" required></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:30px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div><br/>');

 initializeSelect2('select3'+i);

 });

 

 

 

 $(document).on('click', '.btn_remove', function(){

 var button_id = $(this).attr("id");

 $('#row'+button_id+'').remove();

 });

 

});

	  </script>

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

		

	<?php 

	$user_id =$this->session->userdata['logged_in']['user_id'];

									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','178')->where('submodule_access','1')->get();

									if($qry->num_rows()>0){

									?>		

		

 <!--<script>

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

 "sAjaxSource": "<?php echo page_url;?>Reporting/packed_order_list/<?php echo $user_id;?>",

 "aoColumns": [

						{ mData: 'sr_no' } ,

						

						

                        { mData: 'closeorder' },

                        { mData: 'added_on' },

						{ mData: 'itemname' },

                        { mData: 'order_type' },

                        { mData: 'marketing_person' },

						{ mData: 'po_number' },

						{ mData: 'company_name' },

						{ mData: 'address' },

						{ mData: 'email' },

						{ mData: 'mobile_number' },

						{ mData: 'internal_order_no' },

						{ mData: 'discount' },

						{ mData: 'order_value_after_discount' },

						{ mData: 'advance_amount' },

						{ mData: 'payment_terms' },

						{ mData: 'installation_charges' },

						{ mData: 'packingcharges' },

						{ mData: 'freigntcharges' },

						{ mData: 'remarks' }

						

						

                ]

        });   

});





function markstagedone(orderid,compyname,internalorderno)

{

	if(confirm('Confirm you want to close following order? \nCustomer '+compyname+'\n Internal Order No. '+internalorderno))

	{

		document.location="<?php echo page_url;?>Reporting/movetodispatch/"+orderid;

		return true;

		

	}else

	{

		return false;

	}

}

</script>

<?php }else{ ?>

 <script>

$( document ).ready(function() {



$('#example').dataTable({

 "bProcessing": false,

 "pagination":true,

fixedHeader: true,

   scrollCollapse: true,

   fixedColumns:   {

            leftColumns: 3

        },

 "sAjaxSource": "<?php echo page_url;?>Reporting/packed_order_list/NA/<?php echo $this->uri->segment(3);?>",

 "aoColumns": [

						{ mData: 'sr_no' } ,

						

						

                        { mData: 'closeorder' },

                        { mData: 'added_on' },

						{ mData: 'itemname' },

                        { mData: 'order_type' },

                        { mData: 'marketing_person' },

						{ mData: 'po_number' },

						{ mData: 'company_name' },

						{ mData: 'address' },

						{ mData: 'email' },

						{ mData: 'mobile_number' },

						{ mData: 'internal_order_no' },

						{ mData: 'discount' },

						{ mData: 'order_value_after_discount' },

						{ mData: 'advance_amount' },

						{ mData: 'payment_terms' },

						{ mData: 'installation_charges' },

						{ mData: 'packingcharges' },

						{ mData: 'freigntcharges' },

						{ mData: 'remarks' }

						

						

                ]

        });   

});





function markstagedone(orderid)

{

	if(confirm('Confirm you want to close following order?')

	{

		document.location="<?php echo page_url;?>Reporting/movetodispatch/"+orderid;

		return true;

		

	}else

	{

		return false;

	}

}

</script>



<?php }?>



<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>



<script>

function getzonewisedata(vaal)

{

	

	document.location="<?php echo page_url;?>Reporting/completedorders/"+vaal;

	

}

</script>-->





<?php 

	$user_id =$this->session->userdata['logged_in']['user_id'];

									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','178')->where('submodule_access','1')->get();

									if($qry->num_rows()>0){

									?>		

		

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

 "sAjaxSource": "<?php echo page_url;?>Reporting/packed_order_list/<?php echo $user_id;?>",

 "aoColumns": [

						{ mData: 'sr_no' } ,

						

						

                        { mData: 'closeorder' },

                        { mData: 'added_on' },

						{ mData: 'itemname' },

                        { mData: 'order_type' },

                        { mData: 'marketing_person' },

						{ mData: 'po_number' },

						{ mData: 'company_name' },

						{ mData: 'address' },

						{ mData: 'email' },

						{ mData: 'mobile_number' },

						{ mData: 'internal_order_no' },

						{ mData: 'discount' },

						{ mData: 'order_value_after_discount' },

						{ mData: 'advance_amount' },

						{ mData: 'payment_terms' },

						{ mData: 'installation_charges' },

						{ mData: 'packingcharges' },

						{ mData: 'freigntcharges' },

						{ mData: 'remarks' }

						

						

                ]

        });   

});





function markstagedone(orderid)

{

	if(confirm('Confirm you want to close following order?'))

	{

		document.location="<?php echo page_url;?>Reporting/movetodispatch/"+orderid;

		return true;

		

	}else

	{

		return false;

	}

}

</script>

<?php }else{?>

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

 "sAjaxSource": "<?php echo page_url;?>Reporting/packed_order_list/NA/<?php echo $this->uri->segment(3);?>",

 "aoColumns": [

						{ mData: 'sr_no' } ,

						

						

                        { mData: 'closeorder' },

                        { mData: 'added_on' },

						{ mData: 'itemname' },

                        { mData: 'order_type' },

                        { mData: 'marketing_person' },

						{ mData: 'po_number' },

						{ mData: 'company_name' },

						{ mData: 'address' },

						{ mData: 'email' },

						{ mData: 'mobile_number' },

						{ mData: 'internal_order_no' },

						{ mData: 'discount' },

						{ mData: 'order_value_after_discount' },

						{ mData: 'advance_amount' },

						{ mData: 'payment_terms' },

						{ mData: 'installation_charges' },

						{ mData: 'packingcharges' },

						{ mData: 'freigntcharges' },

						{ mData: 'remarks' }

						

						

                ]

        });   







});





function markstagedone(orderid)

{

	if(confirm('Confirm you want to close following order?'))

	{

		document.location="<?php echo page_url;?>Reporting/movetodispatch/"+orderid;

		return true;

		

	}else

	{

		return false;

	}

}

</script>

<?php }?>



<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>

<script>

$(document).ready(function(){

  $("#loginForm").on("submit", function(){

    $("#pageloader").fadeIn();

  });//submit

});//document ready



function getzonewisedata(vaal)

{

	

	document.location="<?php echo page_url;?>Reporting/completedorders/"+vaal;

	

}

</script>





</body>

</html>