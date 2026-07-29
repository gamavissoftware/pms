<?php


	$rest=$this->db->select('id, part, fincode, specification, size_in_mm,min_stock')
				   ->from('machine_parts_with_picture')
				   ->where('flag', 0)
				   ->order_by('id','DESC')
				   ->limit(50)
				   ->get();

	if($rest->num_rows()==0)

	{

		echo "NO PENDING PR ITEM AVAILABLE";

	}

	



$CI =& get_instance();

$CI->load->model('Store_model');

$itemcount=$CI->Store_model->getpartscount();


?>

<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="NJ Media">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title>UPDATE MIN MAX</title>



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

		<style>

table.manglesh thead th {

				background: #003366;

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

.defaultnone

{

	display:none;

}


.loader {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: url('<?php echo assets_url;?>images/loadingpo.gif') 50% 50% no-repeat rgb(249,249,249);

</style>

    </head>

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

                        <div class="page-title-box">

                           

                            <h4>UPDATE MIN MAX  - <span style="color:red;font-weight:bold;"><?php echo $itemcount;?></span></h4>

                        </div>

                    </div>

                </div>

                <!-- end page title end breadcrumb -->

	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

		<div class="row">
            <div class="card-box table-responsive">
				<div class="loader"></div>
                    <div class="col-sm-12">
						<div class="waitmsg" style="display:none">Please Wait...</div>
						   <form action="<?php echo page_url;?>Store/saveMachineParts" method="post" id="pocreateform">

				 <table class="table table-bordered potable">

				  <thead>

				    <tr>

				      <th scope="col">#</th>

				       <th scope="col">Sno.</th>

				      <th scope="col">Part Name</th>

				      <th scope="col">Finsys Code</th>

				      <th scope="col">Specification</th>

				      <th scope="col">Size</th>

					 <th scope="col">ALREADY UPDATED MIN</th>
					  <th scope="col">Max Qty</th>
					  <th scope="col">Min Qty</th>

				    </tr>

				  </thead>

  <tbody>

  <?php

						$i=1;

						$cost=array();

						foreach($rest->result() as $data) {					

							?>

    <tr>

        <th scope="row"><input type="checkbox" name="checkitem[]" class="checkitem<?php echo $data->id;?>" id="checkitem<?php echo $data->id;?>" value="<?php echo $data->id;?>" onchange="showMaxMin(<?php echo $data->id;?>);"><br/>

        </th>

      <th scope="row"><?php echo $i;?></th>

      <th><?php echo $data->part;?></th>

      <td style="width:5%"><?php echo $data->fincode;?></td>
      
      <td style="width:10%"><?php echo $data->specification;?></td>

      <td style="width:10%"><?php echo $data->size_in_mm;?></td>
      
        <td style="width:10%"><?php echo $data->min_stock;?></td>

	 <td><input type="text" name="max_qty<?php echo $data->id;?>"  id="max_qty<?php echo $data->id;?>" style="width:70%;display:none" class="form-control allow_numeric" value="<?php if($data->min_stock<>'0.00'){ echo floatval($data->min_stock); };?>"/></td>

	 <td><input type="text" name="min_qty<?php echo $data->id;?>"  id="min_qty<?php echo $data->id;?>" style="width:70%;display:none" class="form-control allow_numeric"/>

    </tr>

	

<?php

$i++;

}

?>						

	<tr>

	

      <td colspan="7"></td>

	  <td><input type="submit" name="save" id="gpo" value="Submit" class="btn btn-success pull-right btn-sm"></td>

    </tr>

   

  </tbody>

</table>

		</form>	   

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

<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>


<script> 

    function showMaxMin(id) {

	if($("#checkitem"+id).is(":checked")) {
	   $("#max_qty"+id).css('display','');	
	   $("#max_qty"+id).attr('required',true);	
	   $("#min_qty"+id).css('display','');	
	   $("#min_qty"+id).attr('required',true);	
	} else {
	   $("#max_qty"+id).css('display','none');	
	   $("#max_qty"+id).attr('required',false);	
	   $("#min_qty"+id).css('display','none');	
	   $("#min_qty"+id).attr('required',false);	

	}

}


$(function () {
    $(".allow_numeric").keydown(function (event) {


        if (event.shiftKey == true) {
            event.preventDefault();
        }

        if ((event.keyCode >= 48 && event.keyCode <= 57) || 
            (event.keyCode >= 96 && event.keyCode <= 105) || 
            event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 ||
            event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190) {

        } else {
            event.preventDefault();
        }

        if($(this).val().indexOf('.') !== -1 && event.keyCode == 190)
            event.preventDefault(); 
        //if a decimal has been added, disable the "."-button

    });
});
 



$(window).load(function() {
        $(".loader").fadeOut("slow");





});



</script>

</body>

</html>
