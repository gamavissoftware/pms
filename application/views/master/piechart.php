<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="<?php echo copyright;?>">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title><?php echo sitetitle;?> Department List</title>



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
        	 <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>


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

						  <a href="<?php echo page_url; ?>Piechart/piechartfilterdata"><button class="btn btn-success waves-effect waves-light"  style="background-color: ;">Filter Data Graph</button></a>

                               

                            </div>

                           

                            <h4 class="page-title">PIE LIST</h4>

                        </div>

                    </div>

                </div>

                <!-- end page title end breadcrumb -->

<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>



                 <div class="row">

                    <div class="col-sm-12">
<!-----------Source Conversion---------->
                <div class="col-sm-6">
                <div class="card-box">
                  <div id="piechart" style="height: 300px; width: 100%;"></div>
                  <!-- <div id="chartContainer" style="height: 300px; width: 100%;"></div> -->
                </div>
              </div>
              <script type="text/javascript">
                 $(document).ready(function(){
        $.ajax({
        url: "<?php echo page_url; ?>Piechart/pieleadsourcewiseleadconversiondata",
        method: "GET",
        success: function(data) {
        //alert(data);
        drawMonthwiseChart8(data);
        }

        });
        });
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawMonthwiseChart8);

      function drawMonthwiseChart8(chart_data) {
        //alert(chart_data);
        var data = google.visualization.arrayToDataTable(JSON.parse(chart_data));

        var options = {
          title: 'Source Of Conversion',
          pieHole: 0.4
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
      }
    </script>
<!-----------Source Conversion---------->
         <!-----------Source Wise Leads---------->  
       <div class="col-sm-6">
                <div class="card-box">
                  <div id="piechart1" style="height: 300px; width: 100%;"></div>
                </div>
              </div>

      <script type="text/javascript">
        $(document).ready(function(){
        $.ajax({
        url: "<?php echo page_url; ?>Piechart/pieleadsourcewiseleaddata",
        method: "GET",
        success: function(data) {
        //alert(data);
        drawMonthwiseChart7(data);
        }

        });
        });
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawMonthwiseChart7);

      function drawMonthwiseChart7(chart_data) {

        var data = google.visualization.arrayToDataTable(JSON.parse(chart_data));

        var options = {
          title: 'Source Wise Leads',
          pieHole: 0.4
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart1'));

        chart.draw(data, options);
      }
    </script>
    <!-----------Source Wise Leads---------->
<!-----------Quotation to Conversion---------->
    <div class="col-sm-6">
                <div class="card-box">
                  <div id="piechart2" style="height: 300px; width: 100%;"></div>
                </div>
              </div>

      <script type="text/javascript">

        $(document).ready(function(){
        $.ajax({
        url: "<?php echo page_url; ?>Piechart/pieleadquotationtoconversiondata",
        method: "GET",
        success: function(data) {
        //alert(data);
        drawMonthwiseChart6(data);
        }

        });
        });

      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawMonthwiseChart6);

      function drawMonthwiseChart6(chart_data) {

        var data = google.visualization.arrayToDataTable(JSON.parse(chart_data));

        var options = {
          title: 'Quotation to Conversion',
          pieHole: 0.4
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart2'));

        chart.draw(data, options);
      }
    </script>
     <!-----------Quotation to Conversion---------->
    <!---------High Selling Product------------>
    <div class="col-sm-6">
	    <div class="card-box">
	      <div id="piechart3" style="height: 300px; width: 100%;"></div>
	    </div>
	  </div>
    
      <script type="text/javascript">
        $(document).ready(function(){
        $.ajax({
        url: "<?php echo page_url; ?>Piechart/pieleadshighsellingdata",
        method: "GET",
        success: function(data) {
        //alert(data);
        drawMonthwiseChart5(data);
        }

        });
        });

      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawMonthwiseChart5);

      function drawMonthwiseChart5(chart_data) {

        var data = google.visualization.arrayToDataTable(JSON.parse(chart_data));

        var options = {
          title: 'High Selling Product',
          pieHole: 0.4
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart3'));

        chart.draw(data, options);
      }
    </script>
<!---------High Selling Product------------>

<!---------Unqualified Leads by Source------------>
    <div class="col-sm-6">
                <div class="card-box">
                  <div id="piechart5" style="height: 300px; width: 100%;"></div>
                  <!-- <div id="chartContainer" style="height: 300px; width: 100%;"></div> -->
                </div>
              </div>
     <script type="text/javascript">
        $(document).ready(function(){
        $.ajax({
        url: "<?php echo page_url; ?>Piechart/pieleadsunqulifiedbysourcedata",
        method: "GET",
        success: function(data) {
        //alert(data);
        drawMonthwiseChart4(data);
        }

        });
        });
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawMonthwiseChart4);

      function drawMonthwiseChart4(chart_data) {

        var data = google.visualization.arrayToDataTable(JSON.parse(chart_data));

        var options = {
          title: 'Unqualified Leads by Source',
          pieHole: 0.4
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart5'));

        chart.draw(data, options);
      }
    </script>
<!---------Unqualified Leads by Source------------>

<!---------Unqualified Leads by Region------------>
    <div class="col-sm-6">
                <div class="card-box">
                  <div id="piechart6" style="height: 300px; width: 100%;"></div>
                  <!-- <div id="chartContainer" style="height: 300px; width: 100%;"></div> -->
                </div>
              </div>
      <script type="text/javascript">

        $(document).ready(function(){
        $.ajax({
        url: "<?php echo page_url; ?>Piechart/pieleadsunqulifiedbyregiondata",
        method: "GET",
        success: function(data) {
        //alert(data);
        drawMonthwiseChart3(data);
        }

        });
        });
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawMonthwiseChart3);

      function drawMonthwiseChart3(chart_data) {

        var data = google.visualization.arrayToDataTable(JSON.parse(chart_data));

        var options = {
          title: 'Unqualified Leads by Region',
          pieHole: 0.4
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart6'));

        chart.draw(data, options);
      }
    </script>
<!---------Unqualified Leads by Region------------>

<!---------Unqualified Leads by Person Wise------------>
    <div class="col-sm-6">
                <div class="card-box">
                  <div id="piechart7" style="height: 300px; width: 100%;"></div>
                  <!-- <div id="chartContainer" style="height: 300px; width: 100%;"></div> -->
                </div>
              </div>

    <script type="text/javascript">

        $(document).ready(function(){
        $.ajax({
        url: "<?php echo page_url; ?>Piechart/pieleadspersonwisedata",
        method: "GET",
        success: function(data) {
        //alert(data);
        drawMonthwiseChart2(data);
        }

        });
        });
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawMonthwiseChart2);

      function drawMonthwiseChart2(chart_data) {
        //alert(chart_data);

        var data = google.visualization.arrayToDataTable(JSON.parse(chart_data));

        var options = {
          title: 'Unqualified Leads by Person Wise',
          pieHole: 0.4
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart7'));

        chart.draw(data, options);
      }
    </script>
<!---------Unqualified Leads by Person Wise------------>
<!--------- Leads by Region------------>
    <div class="col-sm-6">
                <div class="card-box">
                  <div id="piechart8" style="height: 300px; width: 100%;"></div>
                  <!-- <div id="chartContainer" style="height: 300px; width: 100%;"></div> -->
                </div>
              </div>

      <script type="text/javascript">
        $(document).ready(function(){
  $.ajax({
    url: "<?php echo page_url; ?>Piechart/pieleadsbyregionwiseenquerydata",
    method: "GET",
    success: function(data) {
        //alert(data);
       drawMonthwiseChart1(data);
    }
    
  });
  });
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawMonthwiseChart1);

      function drawMonthwiseChart1(chart_data) {
        //alert(chart_data);
        var data = google.visualization.arrayToDataTable(JSON.parse(chart_data));

        var options = {
          title: 'Region Wise Enquiry',
          pieHole: 0.4
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart8'));

        chart.draw(data, options);
      }
    </script>
<!--------- Leads by Region enquy------------>
<!--------- Leads by Region------------>
        <div class="col-sm-6">
        <div class="card-box">
        <div id="piechart10" style="height: 300px; width: 100%;"></div>
        <!-- <div id="chartContainer" style="height: 300px; width: 100%;"></div> -->
        </div>
        </div>

        <script type="text/javascript">
        $(document).ready(function(){
        $.ajax({
        url: "<?php echo page_url; ?>Piechart/pieleadsbyregiondata",
        method: "GET",
        success: function(data) {
        //alert(data);
        drawMonthwiseChart(data);
        }
        });
        });

      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawMonthwiseChart);

      function drawMonthwiseChart(chart_data) {
      
     //alert(chart_data);
        var data = google.visualization.arrayToDataTable(JSON.parse(chart_data));
        var options = {
          title: 'Region Wise Business',
          pieHole: 0.4
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart10'));

        chart.draw(data, options);
      }
 
    </script>
<!--------- Leads by Region------------>
<!--------Todays Leads ------------->
    <div class="col-sm-6">
        <div class="card-box">
          <div id="chart_div" style="height: 300px; width: 100%;"></div>
          <!-- <canvas id="cha" style="height: 300px; width: 100%;"></canvas> -->
        </div>
      </div>

       <script>

google.charts.load('current', {packages: ['corechart', 'bar']});
google.charts.setOnLoadCallback(drawMultSeries);

function drawMultSeries() {
      var data = new google.visualization.DataTable();
      data.addColumn('timeofday', 'Time of Day');
      data.addColumn('number', 'Leads of the day');
<?php
$st_date=date('Y-m-d')." "."00:00:00";
$end_date=date('Y-m-d')." "."23:59:59";
//echo $st_date; exit;
    $res = $this->db->select('*')->from('leads')->where('added_on >=',$st_date)->where('added_on <=',$end_date)->get();
    
    //echo $leadcount; exit;
     if($res->num_rows() >0)
      {   $leadcount=$res->num_rows();  
         foreach ($res->result() as $row) {
            $time=date('h a',strtotime($row->added_on));
            $time1=date('H',strtotime($row->added_on));

       
    ?>
      data.addRows([
       <?php
         echo "[{v:[".$time1.",0],f: '".$time."'},".$leadcount."],";
         
        ?>
        
      ]);
  <?php }}?>
      var options = {
        title: 'Lead the Day',
        hAxis: {
          title: 'Time of Day',
          format: 'h:mm a',
          viewWindow: {
            min: [7, 30, 0],
            max: [18, 30, 0]
          }
        },
        vAxis: {
          title: 'Rating (scale of 1-10)'
        }
      };

      var chart = new google.visualization.ColumnChart(
        document.getElementById('chart_div'));

      chart.draw(data, options);
    }
      </script>
      <!--------Months Wise Bar chart Leads ------------->
    <div class="col-sm-6">
        <div class="card-box">
          <!-- <canvas id="cha" style="height: 300px; width: 100%;"></canvas> -->
          <div id="chart_area" style="width: 100%; height: 420px;"></div>
        </div>
      </div>

       <script>
        $(document).ready(function(){
  $.ajax({
    url: "<?php echo page_url; ?>Piechart/monthwiseleads",
    method: "GET",
    success: function(data) {
        //alert(data);
       drawMonthwiseChart(data);
    }
    
  });

  function drawMonthwiseChart(chart_data)
{
    var jsonData = chart_data;
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Month');
    data.addColumn('number', 'Leads');

    $.each(JSON.parse(jsonData), function(i, jsonData){
        var month = jsonData.mon;
        var leads = parseFloat($.trim(jsonData.count));
        data.addRows([[month, leads]]);
    });

    var options = {
        title:'Months Wise Leads',
        hAxis: {
            title: "Months"
        },
        vAxis: {
            title: 'Leads'
        },
        chartArea:{width:'80%',height:'60%'}
    }

    var chart = new google.visualization.ColumnChart(document.getElementById('chart_area'));

    chart.draw(data, options);
}

});

      </script>
      <!--------Months Wise Bar chart Leads ------------->
      <!--------Year Wise Bar chart Leads ------------->
    <div class="col-sm-6">
        <div class="card-box">
          <!-- <canvas id="cha" style="height: 300px; width: 100%;"></canvas> -->
          <div id="chart_area1" style="width: 100%; height: 420px;"></div>
        </div>
      </div>

       <script>
        $(document).ready(function(){
  $.ajax({
    url: "<?php echo page_url; ?>Piechart/yearwiseleads",
    method: "GET",
    success: function(data) {
        //alert(data);
       drawMonthwiseChart(data);
    }
    
  });

  function drawMonthwiseChart(chart_data)
{
    var jsonData = chart_data;
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Year');
    data.addColumn('number', 'Leads');

    $.each(JSON.parse(jsonData), function(i, jsonData){
        var year = jsonData.year;
        var leads = parseFloat($.trim(jsonData.count));
        data.addRows([[year, leads]]);
    });

    var options = {
        title:'Year Wise Leads',
        hAxis: {
            title: "Year"
        },
        vAxis: {
            title: 'Leads'
        },
        chartArea:{width:'80%',height:'60%'}
    }

    var chart = new google.visualization.ColumnChart(document.getElementById('chart_area1'));

    chart.draw(data, options);
}

});

      </script>
      <!--------Year Wise Bar chart Leads ------------->
       <!--------INdustry Wise Bar chart Leads ------------->
    <div class="col-sm-6">
        <div class="card-box">
          <!-- <canvas id="cha" style="height: 300px; width: 100%;"></canvas> -->
          <div id="chart_area2" style="width: 100%; height: 420px;"></div>
        </div>
      </div>

       <script>
        $(document).ready(function(){
  $.ajax({
    url: "<?php echo page_url; ?>Piechart/industrywiseleads",
    method: "GET",
    success: function(data) {
        //alert(data);
       drawMonthwiseChart(data);
    }
    
  });

  function drawMonthwiseChart(chart_data)
{
    var jsonData = chart_data;
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'industry');
    data.addColumn('number', 'Leads');

    $.each(JSON.parse(jsonData), function(i, jsonData){
        var industry = jsonData.indus;
        var leads = parseFloat($.trim(jsonData.count));
        data.addRows([[industry, leads]]);
    });

    var options = {
        title:'Industry Wise Leads',
        hAxis: {
            title: "Industry"
        },
        vAxis: {
            title: 'Leads'
        },
        chartArea:{width:'80%',height:'60%'}
    }

    var chart = new google.visualization.ColumnChart(document.getElementById('chart_area2'));

    chart.draw(data, options);
}

});

      </script>
      <!--------------insutry wise -------------------->

      <!--------TOP 10  Bar chart Leads ------------->
    <div class="col-sm-6">
        <div class="card-box">
          <!-- <canvas id="cha" style="height: 300px; width: 100%;"></canvas> -->
          <div id="chart_area3" style="width: 100%; height: 420px;"></div>
        </div>
      </div>

       <script>
        $(document).ready(function(){
  $.ajax({
    url: "<?php echo page_url; ?>Piechart/toptenleads",
    method: "GET",
    success: function(data) {
        //alert(data);
       drawMonthwiseChart(data);
    }
    
  });

  function drawMonthwiseChart(chart_data)
{
    var jsonData = chart_data;
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'indus');
    data.addColumn('number', 'Amount');

    $.each(JSON.parse(jsonData), function(i, jsonData){
        var industry = jsonData.indus;
        var leads = parseFloat($.trim(jsonData.count));
        data.addRows([[industry, leads]]);
    });

    var options = {
        title:'Top 10 Customer',
        hAxis: {
            title: "Customer Name"
        },
        vAxis: {
            title: 'Amount'
        },
        chartArea:{width:'80%',height:'60%'}
    }

    var chart = new google.visualization.ColumnChart(document.getElementById('chart_area3'));

    chart.draw(data, options);
}

});

      </script>
      <!--------------TOP 10  wise -------------------->
      <!--------TOP Enquiries Bar chart Leads ------------->
    <div class="col-sm-6">
        <div class="card-box">
          <!-- <canvas id="cha" style="height: 300px; width: 100%;"></canvas> -->
          <div id="chart_area4" style="width: 100%; height: 420px;"></div>
        </div>
      </div>

       <script>
        $(document).ready(function(){
  $.ajax({
    url: "<?php echo page_url; ?>Piechart/topenquririesdata",
    method: "GET",
    success: function(data) {
       // alert(data);
       drawMonthwiseChart(data);
    }
    
  });

  function drawMonthwiseChart(chart_data)
{
    var jsonData = chart_data;
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Product');
    data.addColumn('number', 'sales');

    $.each(JSON.parse(jsonData), function(i, jsonData){
        var industry = jsonData.indus;
        var leads = parseFloat($.trim(jsonData.count));
        data.addRows([[industry, leads]]);
    });

    var options = {
        title:'Top Enquiry Product',
        hAxis: {
            title: "Product Name"
        },
        vAxis: {
            title: 'Sales Unit'
        },
        chartArea:{width:'80%',height:'60%'}
    }

    var chart = new google.visualization.ColumnChart(document.getElementById('chart_area4'));

    chart.draw(data, options);
}

});

      </script>
      <!--------------TOP Enquiries  wise -------------------->
    <!--------Leads quotation and conversion  ------------->
    <div class="col-sm-6">
        <div class="card-box">
          <!-- <canvas id="cha" style="height: 300px; width: 100%;"></canvas> -->
          <div id="chart_area5" style="width: 100%; height: 420px;"></div>
        </div>
      </div>

       <script>
        $(document).ready(function(){
  $.ajax({
    url: "<?php echo page_url; ?>Piechart/barquotationtoconversion",
    method: "GET",
    success: function(data) {
       // alert(data);
       drawMonthwiseChart(data);
    }
    
  });

  function drawMonthwiseChart(chart_data)
{
    var jsonData = chart_data;
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Month');
    data.addColumn('number', 'Quotation');
    data.addColumn('number', 'Conversion');

    $.each(JSON.parse(jsonData), function(i, jsonData){
        var months = jsonData.months;
        var quto = parseFloat($.trim(jsonData.indus));
        var conver = parseFloat($.trim(jsonData.count));
        data.addRows([[months, quto,conver]]);
    });
    

    
    var options = {
        title:'Leads Quotation / Conversion',
        hAxis: {
            title: "Months"
        },
        vAxis: {
            title: 'Leads'
        },
        chartArea:{width:'80%',height:'60%'}
    }   

    var chart = new google.visualization.ColumnChart(document.getElementById('chart_area5'));
    chart.draw(data, options);
    
}

});

      </script>
      <!--------------  wise -------------------->
      <!--------Leads quotation and conversion AMT  ------------->
    <div class="col-sm-6">
        <div class="card-box">
          <!-- <canvas id="cha" style="height: 300px; width: 100%;"></canvas> -->
          <div id="chart_area6" style="width: 100%; height: 420px;"></div>
        </div>
      </div>

       <script>
        $(document).ready(function(){
  $.ajax({
    url: "<?php echo page_url; ?>Piechart/barquotationtoconversionamt",
    method: "GET",
    success: function(data) {
       // alert(data);
       drawMonthwiseChart(data);
    }
    
  });

  function drawMonthwiseChart(chart_data)
{
    var jsonData = chart_data;
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Month');
    data.addColumn('number', 'Quotation Amt');
    data.addColumn('number', 'Conversion Amt');

    $.each(JSON.parse(jsonData), function(i, jsonData){
        var months = jsonData.months;
        var quto = parseFloat($.trim(jsonData.indus));
        var conver = parseFloat($.trim(jsonData.count));
        data.addRows([[months, quto,conver]]);
    });
    

    
    var options = {
        title:'Leads Quotation / Conversion Amount',
        hAxis: {
            title: "Months"
        },
        vAxis: {
            title: 'Leads'
        },
        chartArea:{width:'80%',height:'60%'}
    }   

    var chart = new google.visualization.ColumnChart(document.getElementById('chart_area6'));
    chart.draw(data, options);
    
}

});

      </script>
      <!--------------TOP Enquiries  wise -------------------->
      

                    </div>

                </div>

                <!-- end row -->



							

							

							

			
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

        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>


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



  



		

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<script language="javascript" type="text/javascript">   

jQuery.noConflict();

$(document).ready(function() {

$("#depsave").click(function() {

var business_loc = $("#business_loc").val();

if(business_loc=='')

{

	$("#error_business_loc").html('Required!');

}

var department_name = $("#department_name").val();

if(department_name=='')

{

	

	$("#error_department_name").html('Required!');

}



var status = $("#status").val();

if(status=='')

{

	

	$("#error_status").html('Required!');

}





if(business_loc=='' || department_name==''|| status=='' )

{

	

	return false;

}



});

});

</script>

    </body>

</html>

