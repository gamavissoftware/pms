<?php
   $uri=$this->uri->segment(3);
                $stdate=$this->uri->segment(4);
                $enddate=$this->uri->segment(5);
                ?>
<script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart1);

        function drawChart1() {
        //alert('hi');
        $.ajax({
        url: "<?php echo page_url; ?>Salesreporting/pieleadsourcewiseleaddata/<?php echo $stdate; ?>/<?php echo $enddate;?>",
        method: "GET",
        success: function(data) {
        //alert(data);
        drawMonthwiseChart9(data);
        }

        });


        }

          function drawMonthwiseChart9(chart_data) {
          var data = google.visualization.arrayToDataTable(JSON.parse(chart_data));
          var options = {

              chartArea:{left: '5%', width: '100%', height: '100%'},
          title: '',

          is3D: true,

          pieSliceText: 'value',
          sliceVisibilityThreshold :0,
          fontSize: 17,
          legend: {
          position: 'labeled'
          },

          };
          var chart = new google.visualization.PieChart(document.getElementById('piechart'));
          chart.draw(data, options);
          }

      
    </script>


               <!-- <div class="col-sm-12">
                <div class="card-box">
                  <?php 
             
                $dateone=date('d-m-Y',strtotime($stdate));
                $datetwo=date('d-m-Y',strtotime($enddate));
                ?>
                
                  
                  <div class="ribbon11-wrapper">
  <h5 class="ribbon11"><b>Source Wise Leads </b></h5>
  <p><?php echo date('d-M-Y',strtotime($dateone)); ?> to <?php echo date('d-M-Y',strtotime($datetwo)); ?></p>
</div>
                  
<div class="row">
  <div class="col-sm-3"></div>
  <div class="col-sm-6" style="border: 1px dashed lightgray; margin-top: 20px; border-radius: 5px;">
  <div id="piechart" style="height: 300px; width: 100%; text-align: center; "></div>

  </div>
  <div class="col-sm-3"></div>
</div>


                 
                  
                </div>
              </div> -->
           
  <div class="col-sm-12">
    <div class="card-box"> 

    <div class="tablebutton">
  <button class="gridlist" onclick="opengrid(event, 'graph-btn')" id="defaultopen"><i class="fa fa-pie-chart" aria-hidden="true"></i></button>
  <button class="gridlist" onclick="opengrid(event, 'table-btn')"><i class="fa fa-table" aria-hidden="true"></i></button>
</div>

<div id="graph-btn" class="gridcontent">
<div class="ribbon11-wrapper">
  <h5 class="ribbon11"><b>Source Wise Leads </b></h5>
  <p><?php echo date('d-M-Y',strtotime($dateone)); ?> to <?php echo date('d-M-Y',strtotime($datetwo)); ?></p>
</div>

<div class="row">
  <div class="col-sm-3"></div>
  <div class="col-sm-6" style="border: 1px dashed lightgray; margin-top: 20px; border-radius: 5px; padding:10px;">
  <div id="piechart" style="height: 300px; width: 100%; text-align: center; "></div>

  </div>
  <div class="col-sm-3"></div>
</div>
</div>

<div id="table-btn" class="gridcontent table-graph">
  <div class="ribbon11-wrapper">
  <h5 class="ribbon11"><b>Source Wise Leads </b></h5>
  <p><?php echo date('d-M-Y',strtotime($dateone)); ?> to <?php echo date('d-M-Y',strtotime($datetwo)); ?></p>
</div>
<div class="row">
<div class="col-sm-2"></div>
  <div class="col-sm-8" style="border: 1px dashed lightgray; margin-top: 20px; border-radius: 5px; padding:10px;">
<table class="table table-striped table-bordered pretty">
  <tr>
    <th>Source of leads</th>
    <th>Leads Count</th>
  </tr>
  <?php
  $this->db->select('source_id,lead_source')->from('lead_source')->where('status',1);

        $query=$this->db->get();
        if($query->num_rows() >0)
            { 
            $i=1;    
            foreach ($query->result() as $row) {
            $this->db->select('id')
            ->from('leads')
            ->where('lead_source_id',$row->source_id);
            if($stdate<>'')
            {
            $this->db->where('added_on >=', $stdate)->where('added_on <=',$enddate); 
            }
            $q=$this->db->get();
            $leadcount=$q->num_rows();
            $instname=$row->lead_source;
          ?>
  <tr>
    <td><?php echo $instname; ?></td>
    <td><?php echo $leadcount; ?></td>
  </tr>
<?php }}?>
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
