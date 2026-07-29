<?php
$CI = &get_instance();
$CI->load->model('Master_model', 'master');
$getFinGoodsType = $CI->master->getFinGoodsType();
$getRackLocation = $CI->master->getRackLocation();
$getunit = $CI->master->getunit();

$getRackLocation1 = $CI->master->getRackLocationByID($this->uri->segment(4));

$cname='';
if ($getRackLocation1 != '') {
foreach ($getRackLocation1 as $row1) {
$cname=$row1->companyname;
} } 
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
					<h4 class="page-title">RECONCILIATION REPORT FOR <?php echo date('d-M-Y',strtotime($this->uri->segment(3)));?> FOR <?php echo strtoupper($cname);?></h4>

					<?php 
					$dateformat = date('d_M_Y',strtotime($this->uri->segment(3)));
					$companyname = preg_replace('/[^A-Za-z0-9. -]/', '', $cname);
						$filename = "RECONCILIATION_REPORT_FOR_".$dateformat."_".$companyname;
					?>
				</div>
				

			</div>
			<!-- end page title end breadcrumb -->
			<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
			
		

		<?php 

		if($this->uri->segment(3)<>'' && $this->uri->segment(4)<>''){?>

			<div class="row">
			 

				<div class="col-sm-12">
					<div class="card-box table-responsive">
						



						


						<table id="example" class="table table-bordered manglesh">
							<thead>
								<tr>
									<th>Sr No.</th>
									<th>Product Name</th>
									<th>Product Code</th>
									
									
									<th>Manual Entered Stock</th>
									<th>Closing Stock On <?php echo date('d-M-Y',strtotime($this->uri->segment(3)));?></th>
									<th>Diff</th>
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
								$query = $this->db->select('a.pack_size,a.hsncode,a.density,a.spec_file,msds_file,a.id, a.instruments_name, a.model_number, a.mvalue, a.image, a.status, a.discount_price,a.unit, trial_reading,a.stock')
						 ->from('presto_instruments a')->where_in('a.id',$pids,false)
				 	     ->get();
				 	     foreach($query->result() as $row){
				 	     ?>
								
								<tr>
									<td><?php echo $i;?></td>
									<td><?php echo $row->instruments_name;?></td>
									<td><?php echo $row->model_number;?></td>
								
								
									<?php
									foreach ($getRackLocation1 as $row1) {
										$rowsid = $row1->id.$row->id;
									 ?>
										
									<td>
										<?php
										$stock = 0;
									
										 $qq = $this->db->select('stock,company_id')->from('stock_value_month_wise_report')->where('product_id',$row->id)->where('stockdate',$this->uri->segment(3))->where('company_id',$this->uri->segment(4))->get();
										if($qq->num_rows()>0){
											foreach($qq->result() as $rq);

												$stock=floatval($rq->stock);


										}



										?>
																				
										<?php echo $stock;?> <?php echo $row->unit;?>
										

									</td>


									<?php 

									$sdate=$this->uri->segment(3);
									$edate=$this->uri->segment(3);

									if(strtotime($this->uri->segment(3))==strtotime('2023-07-12'))
									{
									$getOpenstock = $CI->master->get_stock_for_product_static($sdate,$edate,$this->uri->segment(4),$row->id);
									$ops=explode('~',$getOpenstock);
									$opening_Q=$ops[0];
									$opening_R=$ops[1];
									$opening_V=$opening_Q*$opening_R;
									}else
									{

									$getOpenstock = $CI->master->get_Open_stock_for_product($sdate,$edate,$this->uri->segment(4),$row->id);


									$ops=explode('~',$getOpenstock);

									$opening_Q=$ops[0];
									$opening_R=$ops[1];
									$opening_V=$opening_Q*$opening_R;
									}


									$instock=$CI->master->get_inward_between_dates($sdate,$edate,$this->uri->segment(4),$row->id);
									$opin=explode('~',$instock);
									$opin_Q=$opin[0];
									$opin_R=$opin[1];
									$opin_V=$opin_Q*$opin_R;


									$outstock=$CI->master->get_outward_between_dates($sdate,$edate,$this->uri->segment(4),$row->id);

									$opout=explode('~',$outstock);
									$opout_Q=$opout[0];
									$opout_R=$opout[1];
									$opout_V=$opout_Q*$opout_R;
									$closing=$opening_Q+$opin_Q-$opout_Q;



									?>



									<td><?php echo $closing;?></td>

									<?php 
									
									$data=$stock-$closing;

									if($stock<$closing)
									{
										$diff=$data;
										$b="background-color:red;font-weight:bold;color:white;";

									}else if($stock==$closing)
									{
										
										$diff=$data;
											$b="";
									}else
									{
										$diff=$data;
										
										$b="background-color:green;font-weight:bold;color:white;";
									}?>
									<td style="<?php echo $b;?>"><?php echo $diff;?></td>
									<?php } ?>
								</tr>

							<?php 	$i++;} }?>
							</tbody>
						</table>
					</div>
				</div>
		}

				

			</div>
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
			var filename = "<?php echo $filename;?>";
			$('#example').dataTable({
				 pageLength: 500,
				"pagination": true,
				fixedHeader: true,
				stateSave: true,
				   dom: 'lBfrtip',
"buttons": [
  {
  extend: 'excel',
                title: filename
              
 }
],

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