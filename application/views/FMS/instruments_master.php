<?php
$CI = &get_instance();
$CI->load->model('Master_model', 'master');
// $getFinGoodsType = $CI->master->getFinGoodsType();
// $getRackLocation = $CI->master->getRackLocation();
$getunit = $CI->master->getunit();
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="NJ Media">
	<link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
	<title><?php echo sitetitle; ?> Product Management</title>
	<!-- Table Responsive css -->
	<script src="<?php echo assets_url; ?>js/angular.min.js"></script>
	<!-- DataTables -->
	<link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo assets_url; ?>ckeditor/ckeditor.js"></script>
	<link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
	<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
	 <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
   
	<!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

	<script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
	<style>
		.select2-container
		{
			width: 100% !important;
			height: 43px !important;
		}
		table.manglesh thead th {
			background: #003366;
			color: #fff;
			font-weight: bold;
		}


		.input-color input {
			padding-left: 20px;
		}

		.input-color .color-box {
			width: 10px;
			height: 10px;
			display: inline-block;
			background-color: #ccc;
			position: absolute;
			left: 5px;
			top: 5px;
		}

		.backgroundcolor {
			background-color: #EBEFF2 !important;
		}

		#pageloader {
			background: rgba(255, 255, 255, 0.8);
			display: none;
			height: 100%;
			position: fixed;
			width: 100%;
			z-index: 9999;
		}

		#pageloader img {
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
		<?php $this->load->view('common/nav-menu'); ?>
	</header>
	<!-- End Navigation Bar-->


	<div class="wrapper">
		<div class="container-fluid">

			<div class="row">
				<div class="col-sm-12">
					<div class="text-center">
						<h4 class="page-title">SHUBHAM PACK MACHINES MASTER</h4>
					</div> 
				</div>
				<div class="col-sm-3"></div>
				<div class="col-sm-6"></div>
				<div class="col-sm-3 " style="padding:15px;">
				<button class="btn btn-success waves-effect waves-light pull-right" data-toggle="modal" data-target="#con-close-modal">Add New Product</button>
				</div>
			</div>
			<!-- Page-Title -->
			<div class="row">
				<div class="col-md-4">
					<h4 class="page-title"></h4>
				</div>
				<div class="col-md-4 pull-right"></div>
				<div class="col-md-4 pull-right">
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
									<th>Sr No.</th>
									<th>Product Name</th>
									<th>Product Code</th>
									<th>HSN Code</th>
									<th>Manual</th>
							
									<th>Status</th>
									<th>Action</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
			</div>
			<!-- end row -->
			<div id="con-close-modal" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
				<form id="loginForm" method="post" action="<?php echo page_url; ?>FMS/instruments" enctype="multipart/form-data">
					<div id="pageloader">
						<img src="<?php echo assets_url; ?>images/loading.gif" alt="processing..." />
					</div>
					<div class="modal-dialog modal-full">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
								<h4 class="modal-title">Add Product</h4>
							</div>
							<div class="modal-body">
								<div class="row">
								
									<div class="col-md-4">
										<div class="form-group">
											<label for="field-1" class="control-label">PRODUCT NAME</label>
											<span id="error_instrument_name" style="color:red;">*</span>
											<input type="text" class="form-control" id="product_name" style="text-transform: uppercase;" name="product_name" required>
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group">
											<label for="field-1" class="control-label">CODE/MODEL</label>
											<span id="error_instrument_model" style="color:red;">*</span>
											<input type="text" class="form-control" id="product_code" style="text-transform: uppercase;" name="product_code" required>
										</div>
									</div>

									<div class="col-md-2">
										<div class="form-group">
											<label>HSN</label>
											<span id="error_mvalue" style="color:red;">*</span>
											<input class="form-control" type="number" name="hsn" id="hsn" required step="any">
										</div>
									</div>


									

									<div class="col-md-2">
									<div class="form-group">
									<label>PRODUCT MANUAL</label>
									<span id="error_mvalue" style="color:red;">*</span> 
									<input type="file" name="msds" id="msds" class="form-control">
									</div>
									</div>

									
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
								<input type="submit" id="save" class="btn btn-info" value="Submit">
							</div>
						</div>
					</div>
				</form>
			</div><!-- /.modal -->


			<!-- Footer -->
			<?php $this->load->view('common/footer'); ?>
			<!-- End Footer -->

		</div> <!-- end container -->
	</div>
	<!-- end wrapper -->


	<!-- jQuery  -->
	<script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
	<script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
	<script src="<?php echo assets_url; ?>js/detect.js"></script>
	<script src="<?php echo assets_url; ?>js/fastclick.js"></script>
	<script src="<?php echo assets_url; ?>js/jquery.slimscroll.js"></script>
	<script src="<?php echo assets_url; ?>js/jquery.blockUI.js"></script>
	<script src="<?php echo assets_url; ?>js/waves.js"></script>
	<script src="<?php echo assets_url; ?>js/wow.min.js"></script>
	<script src="<?php echo assets_url; ?>js/jquery.nicescroll.js"></script>
	<script src="<?php echo assets_url; ?>js/jquery.scrollTo.min.js"></script>

	<!-- Datatables-->
	<script src="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.js"></script>
	<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.bootstrap.js"></script>
	<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.buttons.min.js"></script>
	<script src="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.js"></script>
	<script src="<?php echo assets_url; ?>plugins/datatables/jszip.min.js"></script>
	<script src="<?php echo assets_url; ?>plugins/datatables/pdfmake.min.js"></script>
	<script src="<?php echo assets_url; ?>plugins/datatables/vfs_fonts.js"></script>
	<script src="<?php echo assets_url; ?>plugins/datatables/buttons.html5.min.js"></script>
	<script src="<?php echo assets_url; ?>plugins/datatables/buttons.print.min.js"></script>
	<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.fixedHeader.min.js"></script>
	<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.keyTable.min.js"></script>
	<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.responsive.min.js"></script>
	<script src="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.js"></script>
	<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.scroller.min.js"></script>
	<script src="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
	<!-- Datatable init js -->
	<script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>

	<!-- App js -->
	<script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
	<script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
	<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
    <!-- Datatables-->
	<script>
		$(document).ready(function() {

			$('#example').dataTable({
				"bProcessing": false,
				"pagination": true,
				fixedHeader: true,
				stateSave: true,
				dom: 'lBfrtip',
    buttons: [ 
        'excelHtml5', 
    ],
				"sAjaxSource": "<?php echo page_url; ?>FMS/instruments_list/<?php echo $this->uri->segment(3); ?>",
				"aoColumns": [{
						mData: 'sr_no'
					},
					{
						mData: 'instruments_name'
					},

					{
						mData: 'model_number'
					},
					
					{
						mData: 'hsn'
					},

					{
						mData: 'msds'
					},

					
					{
						mData: 'status'
					},
					{
						mData: 'edit'
					}

				]
			});

		});
	</script>
	<script>
		function checkamt(discountprice) {
			//alert(discountprice);
			var price = $("#price").val();
			if (parseFloat(discountprice) > parseFloat(price)) {
				alert('Please enter correct value.');
				$("#disprice").val('');
			} else {
				true;
			}
		}

		function checkamtdist(disprice_distributor) {
			//alert(discountprice);
			var distributorprice = $("#distributorprice").val();
			if (parseFloat(disprice_distributor) > parseFloat(distributorprice)) {
				alert('Please enter correct value.');
				$("#disprice_distributor").val('');
			} else {
				true;
			}
		}
	</script>
	<script language="javascript" type="text/javascript">
		$(document).ready(function() {
			$(".select2").select2({});
			$(".select4").select2({ tags:true });
			$("#save").click(function() {
				var instrument_name = $("#instrument_name").val();
				if (instrument_name == '') {
					$("#error_instrument_name").html('Required!');
				}



				var instrument_stock = $("#instrument_stock").val();
				if (instrument_stock == '') {
					$("#error_instrument_stock").html('Required!');
				}

				var instrument_minstock = $("#instrument_minstock").val();
				if (instrument_minstock == '') {
					$("#error_instrument_minstock").html('Required!');
				}
				var instruments_of = $("#instruments_of").val();
				if (instruments_of == '') {
					$("#error_instruments_of").html('Required!');
				}


				var status = $("#status").val();
				if (status == '') {

					$("#error_status").html('Required!');
				}

				if (instrument_name == '' || status == '' || instrument_stock == '' || instrument_minstock == '' || instruments_of == '') {

					return false;
				}

			});
		});

		function getcolors() {


			$("#example tr").each(function() {

				var currentRow = $(this);

				var col1_value = currentRow.find("td:eq(4)").text();
				col1_value = col1_value.trim();

				if (col1_value == 'NO') {
					currentRow.addClass("backgroundcolor");
				}



			});
		}

		function filter_by_company() {
			var curl = $("#curl").val();
			document.location.href = "<?php echo page_url;?>FMS/instruments/"+curl;
		}
	</script>
	<script>
		$(document).ready(function() {
			$("#loginForm").on("submit", function() {
				$("#pageloader").fadeIn();
			}); //submit
			$('.select3').select2();
		}); //document ready


		function add_avg_rate(prd,company)
		{
			$("#avg_result"+prd+company).text('');
			var avg_rate=$("#avg_rate"+prd+company).val();
			if(prd!='' && company!='')
			{
				$.ajax({
				type:"post",
				url:"<?php echo page_url;?>FMS/add_avg_rate",
				data:"prd="+prd+"&company="+company+"&avg_rate="+avg_rate,
				success: function(data){
					// if(parseInt(data)>0)
					// {

						$("#avg_result"+prd+company).text('Updated');
					// }else
					// {
					// 	$("#avg_result"+prd+company).text('Failed');
					// }
				}
				});
			}

		}

		 function allow_decimal(data) {
      
          var self = $("#" + data);
          self.val(self.val().replace(/[^0-9\.]/g, ''));
          if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
              evt.preventDefault();
          }
      
      
      }


      function add_min_stock(prd,company)
		{
			$("#min_result"+prd+company).text('');
			var avg_rate=$("#min_stock"+prd+company).val();
			if(prd!='' && company!='')
			{
				$.ajax({
				type:"post",
				url:"<?php echo page_url;?>FMS/add_min_stock",
				data:"prd="+prd+"&company="+company+"&avg_rate="+avg_rate,
				success: function(data){
					// if(parseInt(data)>0)
					// {

						$("#min_result"+prd+company).text('Updated');
					// }else
					// {
					// 	$("#avg_result"+prd+company).text('Failed');
					// }
				}
				});
			}

		}
	</script>
</body>

</html>