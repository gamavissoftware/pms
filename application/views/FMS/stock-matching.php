<?php
$CI = &get_instance();
$CI->load->model('Master_model', 'master');
$getFinGoodsType = $CI->master->getFinGoodsType();
$getRackLocation = $CI->master->getRackLocation();
$getunit = $CI->master->getunit();

$getRackLocation1 = $CI->master->getRackLocationByID($this->uri->segment(4));
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

		.fixed {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    margin: 0 1rem;
    padding: 1rem;
    background-color: #fff;
    border: 1px solid #ccc;
    
    .fixed-box {
        display: flex;
        justify-content: center;
        
        
    }
}
	</style>
	<script type="text/javascript">
		$(function() {
    var $box = $('.box');

    var $submitButton = $box.find('#button').first().clone(true);

    var fixed_content = '';

    $submitButton.each(function() {
        var box_content = $(this).html();
        fixed_content = fixed_content + box_content;
    });
    
    fixed_content = '<div class="fixed"><div class="fixed-box">' + fixed_content + '</div></div>';
    
    $submitButton.html(fixed_content);
    
    $('body').append($submitButton);
    
   $('#button').on('inview', function(event, isInView) {
      if (isInView) {
          $('.fixed').fadeOut();
      } else {
          $('.fixed').fadeIn();
      }
   });
});
	</script>
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
				<div class="col-md-12">
					<h4 class="page-title">MANUAL STOCK ENTRY FOR RECONCILIATION</h4>
				</div>
				

			</div>
			<!-- end page title end breadcrumb -->
			<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
			<form method="post" action="<?php echo page_url;?>FMS/stock_reconcile">

				<div class="row card-box">
					<div class="col-md-12">						
					<div class="col-md-2"></div>
					<div class="col-md-4">
					<input type="date" name="sdate" id="sdate" value="<?php if($this->uri->segment(3)<>''){ echo $this->uri->segment(3); }else{ echo date('Y-m-d'); };?>" class="form-control">
					</div>
					<div class="col-md-4">
					<select class="form-control" name="company" id="company" required>
					<option value="">Select Company</option>
					<?php if ($getRackLocation != '') {
					foreach ($getRackLocation as $row1) { ?>
					<option value="<?php echo $row1->id; ?>" <?php if($this->uri->segment(4)==$row1->id){?> selected <?php } ?>><?php echo $row1->companyname; ?></option>
					<?php }
					} ?>
					</select>
					</div>		
					</div>
					<div class="col-md-12" style="margin-top:20px;">	
						<div class="col-md-4"></div>
						<div class="col-md-4 text-center"><input style="width:50%" type="submit" name="sub" class="btn btn-warning" value="Filter"></div>
					</div>					
					</div>
				</form>
				</div>

		

		<?php 

		if($this->uri->segment(3)<>'' && $this->uri->segment(4)<>''){?>

<form action="<?php echo page_url;?>FMS/stock_reconcile_save_at_once/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>" name="frm" method="post">
			<div class="row">
			 

				<div class="col-sm-12">
					<div class="card-box table-responsive">
						



						


						<table id="example" class="table table-bordered manglesh">
							<thead>
								<tr>
									<th>Sr No.</th>
									<th>Product Name</th>
									<th>Product Code</th>
									<th>Unit</th>
									<th>Price</th>
									
									<?php
									$uri = $this->uri->segment(3);
									$company = $this->uri->segment(4);
									if ($getRackLocation1 != '') {
									foreach ($getRackLocation1 as $row1) { ?>
									<th><?php echo $row1->companyname; ?></th>
									<?php }
									} ?>
								
								</tr>
							</thead>
							<tbody>
								<?php 
								$year = date('Y');

								$pid=array();
								$i=1;
								$rt=$this->db->select('product_id')->from('company_products')->where('company_id',$this->uri->segment(4))->get();
								if($rt->num_rows()>0)
								{
									foreach($rt->result() as $rttt)
									{
										$pid[]=$rttt->product_id;
									}
								}

								
								if(count($pid)>0)
								{
									$pids = "'" . implode ( "', '", $pid ) . "'";
								$i=1;
								$query = $this->db->select('a.pack_size,a.hsncode,a.density,a.spec_file,msds_file,a.id, a.instruments_name, a.model_number, a.mvalue, a.image, a.status, a.discount_price,a.unit, trial_reading,a.stock')
						 ->from('presto_instruments a')->where_in('a.id',$pids,false)
				 	     ->get();
				 	     foreach($query->result() as $row){

				 	     ?>
								
								<tr>
									<td><?php echo $i;?> 
									<input type="hidden" name="prdid[]" value="<?php echo $row->id;?>">
									</td>
									<td><?php echo $row->instruments_name;?></td>
									<td><?php echo $row->model_number;?></td>
									<td><?php echo $row->unit;?></td>
									<td><?php echo $row->mvalue;?></td>
									<?php
									foreach ($getRackLocation1 as $row1) {
										$rowsid = $row1->id.$row->id;
									 ?>
										
									<td>
										<?php
										$stock = "";
										$disablebox = "";
										 $qq = $this->db->select('stock,company_id')->from('stock_value_month_wise_report')->where('product_id',$row->id)->where('stockdate',$this->uri->segment(3))->where('company_id',$this->uri->segment(4))->get();
										if($qq->num_rows()>0){
											foreach($qq->result() as $rq);

												$stock=floatval($rq->stock);

										$submoduleid = array('111');
										$qry = $this->db->select('id')->from('module_capablity')->where('role_id', $_SESSION['logged_in']['user_id'])->where('moduleid', 1)->where_in('submoduleid', $submoduleid)->where('submodule_access', '1')->get();
										if ($qry->num_rows() == 0) {

												$disablebox="readonly";
											}


										}



										?>
												
										<!--- FOR AJAX --->
										<!-- onkeyup="updatestockvalue(<?php //echo $row->id;?>,'<?php //echo $this->uri->segment(3);?>',<?php //echo $this->uri->segment(4);?>);"	

																	 -->
										<input type="text" step="any" class="form-control allow_decimal" name="stockvalue[]" id="stockvalue<?php echo $row->id;?>"  value="<?php echo $stock;?>" <?php echo $disablebox;?>>
										

									</td>
									<?php } ?>
								</tr>

							<?php 	$i++;} }?>
							</tbody>
						</table>
					</div>
				</div>
		}

				

			</div>

			<div class="row">
				<div class="col-md-4"></div>
				<div class="col-md-4">
				<input type="submit" style="width:100%" name="sub" class="btn btn-success" id="sub" value="Submit">
				</div>
			</div>
		</form>
			<!-- end row -->

		<?php } ?>
			


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
				 pageLength: 500,
				"pagination": true,
				fixedHeader: true,
				stateSave: true
			});


			
			$(".allow_decimal").on("input", function(evt) {

   var self = $(this);

   self.val(self.val().replace(/[^0-9\.]/g, ''));

   if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 

   {

     evt.preventDefault();

   }

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
	</script>

	<script type="text/javascript">
		function updatestockvalue(id,selectdate,company) {
			var productid = id;
			var seldate=selectdate;

			var companyid = company;
			var stock = $("#stockvalue"+productid).val();

			$.ajax({
			type:"post",
			url:"<?php echo page_url;?>FMS/updatestockvalueviaajax/",
			data:"productid="+productid+"&seldate="+seldate+"&companyid="+companyid+"&stock="+stock,
			success:function(data){
			$("#success"+e).html(data);
			}

			});

			
		}
	</script>	
</body>

</html>