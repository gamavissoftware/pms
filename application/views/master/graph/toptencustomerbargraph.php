   <?php 
      $startdate=$this->uri->segment(4);
      $enddate=$this->uri->segment(5);
      ?>
   <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart1);

        function drawChart1() {
        $.ajax({
        url: "<?php echo page_url; ?>Piechart/toptenleads/<?php echo $startdate; ?>/<?php echo $enddate;?>",
        method: "GET",
        success: function(data) {
        //alert(data);
        drawMonthwiseChart8(data);
        }

        });

        }

          
      function drawMonthwiseChart8(chart_data)
      {
      var jsonData = chart_data;
      var data = new google.visualization.DataTable();
      data.addColumn('string', 'industry');
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

      var chart = new google.visualization.ColumnChart(document.getElementById('chart_area2'));

      chart.draw(data, options);
      }
      
    </script>


   <div class="col-sm-12">
        <div class="card-box">
            <?php 
                $uri=$this->uri->segment(3);
                $startdate=$this->uri->segment(4);
                $enddate=$this->uri->segment(5);
                $dateone=date('d-m-Y',strtotime($startdate));
                $datetwo=date('d-m-Y',strtotime($enddate));
                ?>
                  <div class="text-center"><b>Top 5 Customer</b> Start Date: <?php echo $dateone; ?> to End Date: <?php echo $datetwo; ?></div>
          <!-- <canvas id="cha" style="height: 300px; width: 100%;"></canvas> -->
          <div id="chart_area2" style="width: 100%; height: 420px;"></div>
        </div>
      </div>
          