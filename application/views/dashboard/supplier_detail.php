<?php $user_id =$this->session->userdata['logged_in']['user_id'];?>
<!DOCTYPE html>


<html>


    <head>


        <meta charset="utf-8">


        <meta name="viewport" content="width=device-width, initial-scale=1.0">


        <meta name="description" content="">


        <meta name="author" content="<?php echo copyright; ?>">





        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">





        <title><?php echo sitetitle; ?> Departmentwise Report</title>





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

.supplier{
    background-color:white;
    height:auto;
    padding:20px;
    box-shadow:1px 1px 10px lightgrey;
    border-radius:10px;
}

.supply-box{
    background-color: white;
    height: 60px;
    /* padding: 10px; */
    margin-top: 10px;
    margin-bottom: 10px;
}

.supply-box p{
    font-family: 'Montserrat', sans-serif;
    font-weight:700;
    font-size: 15px;
}

.supplier h3{
    font-weight:700;
}

.supplier p{
    font-weight:700;
    font-family: 'Montserrat', sans-serif;
}
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

<div class="supplier">
<h3>Jagdeep Khattar</h3>
<p>Director</p>
<hr>

<div class="row">
<div class="col-sm-6 col-md-6 col-lg-3">
<div class="supply-box">
<p>Business Location</p>
<p style="font-size:10px;">Hongyi JIG Rapid Technologies (2nd Floor, 15/1, Rama Road Industrial Area, New Delhi - 110015)</p>
</div>
</div>
<div class="col-sm-6 col-md-6 col-lg-3"><div class="supply-box">
<p>Department</p>
<p style="font-size:10px;">MDO Department</p>
</div></div>
<div class="col-sm-6 col-md-6 col-lg-3"><div class="supply-box">
<p>Email ID</p>
<p style="font-size:10px;">jagdeepkhattar@hongyijig.com</p>
</div></div>
<div class="col-sm-6 col-md-6 col-lg-3"><div class="supply-box">
<p>Contact Number</p>
<p style="font-size:10px;">9811943773</p>
</div></div>
</div><hr style="    margin-top: 5px;
    margin-bottom: 5px;
">
<div class="row">

<div class="col-sm-6 col-md-6 col-lg-3">
<div class="supply-box">
<p>Alternate Number</p>
<p style="font-size:10px;"></p>
</div>
</div>
<div class="col-sm-6 col-md-6 col-lg-3"><div class="supply-box">
<p>Father Name</p>
<p style="font-size:10px;"></p>
</div></div>
<div class="col-sm-6 col-md-6 col-lg-3"><div class="supply-box">
<p>Mother Name</p>
<p style="font-size:10px;"></p>
</div></div>
<div class="col-sm-6 col-md-6 col-lg-3"><div class="supply-box">
<p>Date of Birth</p>
<p style="font-size:10px;">1992-01-22</p>
</div></div>
</div><hr style="    margin-top: 5px;
    margin-bottom: 5px;
">
<div class="row">
<div class="col-sm-6 col-md-6 col-lg-3">
<div class="supply-box">
<p>Date of Joining</p>
<p style="font-size:10px;">2021-01-21</p>
</div>
</div>
<div class="col-sm-6 col-md-6 col-lg-3"><div class="supply-box">
<p>Qualification</p>
<p style="font-size:10px;"></p>
</div></div>
<div class="col-sm-6 col-md-6 col-lg-3"><div class="supply-box">
<p>Address</p>
<p style="font-size:10px;"></p>
</div></div>
<div class="col-sm-6 col-md-6 col-lg-3"><div class="supply-box">
<p>PAN Card</p>
<p style="font-size:10px;"></p>
</div></div>
</div><hr style="    margin-top: 5px;
    margin-bottom: 5px;
">
<div class="row">
<div class="col-sm-6 col-md-6 col-lg-3">
<div class="supply-box">
<p>Aadhar Number</p>
<p style="font-size:10px;"></p>
</div>
</div>
<div class="col-sm-6 col-md-6 col-lg-3"><div class="supply-box">
<p>Blood Group</p>
<p style="font-size:10px;">B +</p>
</div></div>

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


        <script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>


        <!-- Datatable init js -->


        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>


<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>


        <!-- App js -->


        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>


        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>


	    <script>
        
        $( document ).ready(function(){
            $.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/getproductionreportcount/1',
                type: 'get',
                success: function(data){
                    $('#pending_order').attr('data-badge', data);
                }
            });
            
             $.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/getproductionreportcount/2',
                type: 'get',
                success: function(data){
                    $('#sales_pending').attr('data-badge', data);
                }
            });
            
             $.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/getproductionreportcount/3',
                type: 'get',
                success: function(data){
                    $('#machine_packing').attr('data-badge', data);
                }
            });
            
            $.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/getproductionreportcount/4',
                type: 'get',
                success: function(data){
                    $('#machine_packing_service').attr('data-badge', data);
                }
            });
            
            $.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/getproductionreportcount/5',
                type: 'get',
                success: function(data){
                    $('#ready_dispatch').attr('data-badge', data);
                }
            });
            
            $.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/getproductionreportcount/6',
                type: 'get',
                success: function(data){
                    $('#reorder_report').attr('data-badge', data);
                }
            });
            
            $.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/getproductionreportcount/7',
                type: 'get',
                success: function(data){
                    $('#dispatch_tom').attr('data-badge', data);
                }
            });
            
            $.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/getproductionreportcount/8',
                type: 'get',
                success: function(data){
                    $('#ready_billing').attr('data-badge', data);
                }
            });
            
             $.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/getproductionreportcount/9',
                type: 'get',
                success: function(data){
                    $('#sales_dispatch').attr('data-badge', data);
                }
            });
            
            $.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/getproductionreportcount/10',
                type: 'get',
                success: function(data){
                    $('#service_request').attr('data-badge', data);
                }
            });
            
            $.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/getproductionreportcount/11',
                type: 'get',
                success: function(data){
                    $('#lot_order').attr('data-badge', data);
                }
            });
            
             $.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/getproductionreportcount/12',
                type: 'get',
                success: function(data){
                    $('#start_end').attr('data-badge', data);
                }
            });
        });
 
    </script>	


 


</body>


</html>