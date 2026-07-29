<?php $user_id =$this->session->userdata['logged_in']['user_id'];
$CI =& get_instance();
$CI->load->model('Lead_model','leadmodel');
$id=$this->uri->segment(3);

?>
<!DOCTYPE html>


<html>


    <head>


        <meta charset="utf-8">


        <meta name="viewport" content="width=device-width, initial-scale=1.0">


        <meta name="description" content="">


        <meta name="author" content="<?php //echo copyright; ?>">





        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">





        <title><?php echo sitetitle; ?> Customer Timeline</title>





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
          <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">


        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->


        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->


        <!--[if lt IE 9]>


        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>


        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>


        <![endif]-->





        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script> 
	<?PHP 
//$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
//foreach($q->result() as $LOGO);
?>

    <style>


table.manglesh thead th {


				background: red;


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


              

        <!-- End Navigation Bar-->

    

<div class="container-fluid" >
<div class="dashboard-header">
<h1>Customer Timeline</h1>
</div>
<div class="row">

<div class="card-box" style="background-color: #f5f5f5;">
    <div class="col-md-4"></div>
    <div class="col-md-4">
        <select name="customer" id="customer" class="form-control select2" onchange="getdata();">
            <option value="">Select</option>
          

        </select>
    </div>
    <div class="col-md-4">
        

    </div>
</div> 

</div>

<div class="row">
    <div class="col-md-12 card-box" style="background-color: #f5f5f5;" id="client_details"></div>
</div>


<div class="row">

    <div class="col-md-4 card-box" style="background-color: #f5f5f5;min-height:500px;" >
        <div style="overflow-y: scroll;height:450px;" id="quotations">
        </div>
    </div>
    <div class="col-md-5 card-box" style="background-color: #f5f5f5;min-height:500px;">
         <div style="overflow-y: scroll;height:450px;" id="orders">
        </div>
    </div>

     <div class="col-md-3 card-box" style="background-color: #f5f5f5;min-height:500px;">
         <div style="overflow-y: scroll;height:450px;" id="payment_history">
           <!--  <div style="width: 100%; height: 420px;" id="chart_area2"></div> -->
        </div>
    </div>

</div>

<div class="row">
    <div class="col-md-12" id="chart_div">

        
    </div>

</div>

    
</div>




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


		<script type="text/javascript">
            $( document ).ready(function() {
            var purl="<?php echo page_url;?>Sales_stats_reporting/getclient";
            $('.select2').select2({ 

            placeholder: 'TYPE TO SELECT',
            minmumInputLength:4,
            allowClear: true,
            tags:true,
            ajax: {
            url: purl,
            dataType: 'json',
            delay: 250,
            data: function (params) {

            return {
            searchTerm: params.term
            };

            },
            processResults: function (data) {
            return {
            results: data
            };
            },
            cache: true

            }

            });
            });
       

        </script>
         <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>

         <script>
             function getdata()
             {
                var cust=$("#customer").val();
                
                if(cust!='')
                {
                    document.location="<?php echo page_url;?>Sales_stats_reporting/fetch_customer_timeline/"+cust;
                }
             }



             function getcustomerinfo()
             {
                $.ajax({
                url: "<?php echo page_url;?>Sales_stats_reporting/getcustomerinfo/<?php echo $this->uri->segment(3);?>",
                method: "GET",
                success: function(data) {
               $("#client_details").html(data);
                }

                });
             }

             function getcustomer_quotations_info()
             {
                $.ajax({
                url: "<?php echo page_url;?>Sales_stats_reporting/getcustomer_quote_info/<?php echo $this->uri->segment(3);?>",
                method: "GET",
                success: function(data) {
               $("#quotations").html(data);
               
                }

                });

             }

             function getcustomer_order_details()
             {

                 $.ajax({
                url: "<?php echo page_url;?>Sales_stats_reporting/getcustomer_order_details/<?php echo $this->uri->segment(3);?>",
                method: "GET",
                success: function(data) {
               $("#orders").html(data);
              
                }

                });

             }

             function getcustomer_not_buyeing()
             {
                
                   $.ajax({
                url: "<?php echo page_url;?>Sales_stats_reporting/customer_not_buying/<?php echo $this->uri->segment(3);?>",
                method: "GET",
                success: function(data) {
               $("#product_not_buyed").html(data);
              
                }

                });

             }

             function get_customer_payment_details()
             {

                  $.ajax({
                url: "<?php echo page_url;?>Sales_stats_reporting/getcustomer_payment_history/<?php echo $this->uri->segment(3);?>",
                method: "GET",
                success: function(data) {
                
               $("#payment_history").html(data);
              
                }

                });
             }
         </script>
         <?php if($this->uri->segment(3)<>'')
         { ?>
         <script type="text/javascript">
            $( document ).ready(function() {
            getcustomerinfo();
            getcustomer_quotations_info();
            getcustomer_order_details();
            //getcustomer_not_buyeing();
            //get_customer_sale_pattern();
             get_customer_payment_details();
            });

         </script>
     <?php } ?>
 


    <script type="text/javascript">
     google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawChart);



                $( document ).ready(function() {

                $.ajax({
                url: "<?php echo page_url; ?>Sales_stats_reporting/getproduct_timeline/<?php echo $this->uri->segment(3);?>",
                method: "GET",
                success: function(data) {
                //alert(data);
                drawMonthwiseChart8(data);
                gettabular_product_sales_data();
                }

                });


                });

       function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Year', 'Sales', 'Expenses', 'Profit'],
          ['2014', 1000, 400, 200],
          ['2015', 1170, 460, 250],
          ['2016', 660, 1120, 300],
          ['2017', 1030, 540, 350]
        ]);

        var options = {
          chart: {
            title: 'Customer Product Timeline',
            subtitle: 'Sales, Expenses, and Profit: 2014-2017',
          },
          bars: 'vertical',
          vAxis : {format: 'decimal'},
          height: 400,
          colors: ['#1b9e77', '#d95f02', '#7570b3']
        };

        var chart = new google.charts.Bar(document.getElementById('chart_div1'));

        chart.draw(data, google.charts.Bar.convertOptions(options));

        var btns = document.getElementById('btn-group');

        btns.onclick = function (e) {

          if (e.target.tagName === 'BUTTON') {
            options.vAxis.format = e.target.id === 'none' ? '' : e.target.id;
            chart.draw(data, google.charts.Bar.convertOptions(options));
          }
        }
      }

  

    </script>



  

</body>


</html>