   <?php

      $user=$this->uri->segment(4);
      // $users=base64_decode($user);
      //           echo $users; exit;
      $startdate=$this->uri->segment(5);
      $enddate=$this->uri->segment(6);

      $CI =& get_instance();
      $CI->load->model('Salescrm_model');
      $DI =& get_instance();
      $DI->load->model('Dashboard_model', 'dashboardmodel');
      ?>
   <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart1);

        function drawChart1() {
        //alert('hi sele');
        $.ajax({
        url: "<?php echo page_url; ?>Piechart/compareteamhandlelead/<?php echo $user ?>/<?php echo $startdate; ?>/<?php echo $enddate;?>",
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
    data.addColumn('string', 'Product');
    data.addColumn('number', 'leads');
    data.addColumn({type:'string', role:'annotation'});

    $.each(JSON.parse(jsonData), function(i, jsonData){
        var industry = jsonData.indus;
        var leads = parseFloat($.trim(jsonData.count));
        var labels = parseFloat($.trim(jsonData.labels));
        data.addRows([[industry, leads,labels+" Leads"]]);
    });

    var options = {
        colors: ['gray'],
        hAxis: {
            title: "Teams"
        },
        vAxis: {
            title: 'Leads (count)',
            format: '0'
        },
        chartArea:{width:'80%',height:'60%'}
    }

     


      var chart = new google.visualization.ColumnChart(document.getElementById('chart_area2'));

      chart.draw(data, options);
      }

       if (data.getNumberOfRows() === 0) {
      data.addRows([
        ['', 0,'No Data Copy']
      ]);
    }
      
    </script>


   <!-- <div class="col-sm-12">
        <div class="card-box">
            <?php 
                $uri=$this->uri->segment(3);
                $startdate=$this->uri->segment(5);
                $enddate=$this->uri->segment(6);
                $dateone=date('d-m-Y',strtotime($startdate));
                $datetwo=date('d-m-Y',strtotime($enddate));
                ?>
                  <div class="ribbon11-wrapper">
                    <h5 class="ribbon11"><b>Leads Handled by Team</b></h5>
                    <p><?php echo date('d-M-Y',strtotime($dateone)); ?> to <?php echo date('d-M-Y',strtotime($datetwo)); ?></p>
                   </div>
          
          <div id="chart_area2" style="width: 100%; height: 420px;"></div>
        </div>
      </div> -->

      <div class="col-sm-12">
    <div class="card-box" style="height: 515px;"> 

    <div class="tablebutton">
  <button class="gridlist" onclick="opengrid(event, 'graph-btn')" id="defaultopen"><i class="fa fa-pie-chart" aria-hidden="true"></i></button>
  <button class="gridlist" onclick="opengrid(event, 'table-btn')"><i class="fa fa-table" aria-hidden="true"></i></button>
</div>

<div id="graph-btn" class="gridcontent">
<div class="ribbon11-wrapper">
  <h5 class="ribbon11"><b>Leads Handled by Team</b></h5>
  <p><?php echo date('d-M-Y',strtotime($dateone)); ?> to <?php echo date('d-M-Y',strtotime($datetwo)); ?></p>
</div>

<div class="row">
  <div id="chart_area2" style="width: 100%; height: 420px;"></div>

  </div>
</div>
<div id="table-btn" class="gridcontent table-graph">
  <div class="ribbon11-wrapper">
  <h5 class="ribbon11"><b>Leads Handled by Team</b></h5>
  <p><?php echo date('d-M-Y',strtotime($dateone)); ?> to <?php echo date('d-M-Y',strtotime($datetwo)); ?></p>
</div>
<div class="row">
<div class="col-sm-2"></div>
  <div class="col-sm-8" style="border: 1px dashed lightgray; margin-top: 20px; border-radius: 5px; padding:10px;">
<table>
  <tr>
    <th>Team Name</th>
    <th>Leads Count</th>
  </tr>
  <?php
  $getCompareLeadByTeam = $CI->Salescrm_model->getCompareLeadByTeam($user, $startdate, $enddate);

  if($getCompareLeadByTeam != '') {
    
    foreach($getCompareLeadByTeam as $row) {
       
       $getCompareLeadByTeamData = $CI->Salescrm_model->getCompareLeadByTeamData($row->team_id);
          
          foreach($getCompareLeadByTeamData as $row1);
  ?>
    <tr>
      <td><?php echo $row->team_name; ?></td>
      <td><?php echo $row1->asd; ?></td>
    </tr>
  <?php } }else{?>

  <tr>
      <td colspan="2">No Data Available</td>
  
    </tr>

  <?php } ?>
</table>
</div>
  <div class="col-sm-2"></div>
</div>
</div>



<script>
function opengrid(evt, cityName) {
  var i, gridcontent, gridlist;
  gridcontent = document.getElementsByClassName("gridcontent");
  for (i = 0; i < gridcontent.length; i++) {
    gridcontent[i].style.display = "none";
  }
  gridlist = document.getElementsByClassName("gridlist");
  for (i = 0; i < gridlist.length; i++) {
    gridlist[i].className = gridlist[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}
document.getElementById("defaultopen").click();
</script>
    </div>
  </div>
          