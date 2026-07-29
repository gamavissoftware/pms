   <?php

      $user=$this->uri->segment(4);
      // $users=base64_decode($user);
      //           echo $users; exit;
      $stdate=$this->uri->segment(5)." 00:00:00";
      $enddate=$this->uri->segment(6)." 23:59:59";
      $CI =& get_instance();
      $CI->load->model('Salescrm_model');
      $DI =& get_instance();
      $DI->load->model('Dashboard_model', 'dashboardmodel');
      $mode=$DI->dashboardmodel->getsettings();
      if(count($mode)>0)
      {
        $mode=$mode['mode'];
      }else
      {
        $mode=1;
      }
      ?>
   <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart1);

        function drawChart1() {
        //alert('hi sele');
        $.ajax({
        url: "<?php echo page_url; ?>Piechart/conversionbymember/<?php echo $user ?>/<?php echo $stdate; ?>/<?php echo $enddate;?>",
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
        title:'Leads Converted by Agents',
        colors: ['gray'],
        hAxis: {
            title: "Agents"
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
      
    </script>


  <?php 
                $uri=$this->uri->segment(3);
                $stdate=$this->uri->segment(5)." 00:00:00";
                $enddate=$this->uri->segment(6)." 23:59;59";
                $dateone=date('d-m-Y',strtotime($stdate));
                $datetwo=date('d-m-Y',strtotime($enddate));
                ?>
  

<div class="col-sm-12">
    <div class="card-box" > 

    <div class="tablebutton">
  <button class="gridlist" onclick="opengrid(event, 'graph-btn')" id="defaultopen"><i class="fa fa-pie-chart" aria-hidden="true"></i></button>
  <button class="gridlist" onclick="opengrid(event, 'table-btn')"><i class="fa fa-table" aria-hidden="true"></i></button>
</div>

<div id="graph-btn" class="gridcontent">
<div class="ribbon11-wrapper">
  <h5 class="ribbon11"><b>Leads Converted by Agents</b></h5>
  <p><?php echo date('d-M-Y',strtotime($dateone)); ?> to <?php echo date('d-M-Y',strtotime($datetwo)); ?></p>
</div>

<div class="row">
  <div id="chart_area2" style="width: 100%; height: 420px;"></div>

  </div>
</div>
<div id="table-btn" class="gridcontent search-page">
  <div class="ribbon11-wrapper">
  <h5 class="ribbon11"><b>Leads Converted by Agents</b></h5>
  <p><?php echo date('d-M-Y',strtotime($dateone)); ?> to <?php echo date('d-M-Y',strtotime($datetwo)); ?></p>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="col-md-3"></div>
      <div class="col-md-6">
          <table>
            <tr>
              <th>Agents Name</th>
              <th>Leads Count</th>
            </tr>
            <?php
            $getConvertedLeadsByMember = $CI->Salescrm_model->getConvertedLeadsByMember($user);
            $conversion_lead_stage = $DI->dashboardmodel->getConversionLeadStage();
                if($getConvertedLeadsByMember != '') {
                  foreach($getConvertedLeadsByMember as $row) {
                    if($mode==1)
                    { 
                    $getConvertedLeadsByMemberData = $CI->Salescrm_model->getConvertedLeadsByMemberData($row->user_id, $stdate, $enddate, $conversion_lead_stage);
                    }else
                    {
                      $getConvertedLeadsByMemberData = $CI->Salescrm_model->getConvertedLeadsByMemberDatamulti($row->user_id, $stdate, $enddate, $conversion_lead_stage);
                    }
                      $leadcount=$getConvertedLeadsByMemberData;
                      $name=$row->first_name.' '.$row->last_name;
                    ?>
                  
                  <tr>
                    <td><?php echo $name; ?></td>
                    <td><?php echo $leadcount; ?></td>
                  </tr>
                <?php } }?>
          </table>
          </div>
      </div>
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
          