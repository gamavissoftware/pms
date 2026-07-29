  <?php 
                $uri=$this->uri->segment(3);
                $stdate=$this->uri->segment(4);
                $enddate=$this->uri->segment(5);
                $dateone=date('d-m-Y',strtotime($stdate));
                $datetwo=date('d-m-Y',strtotime($enddate));
                $stdate=date('Y-m-d',strtotime($dateone));
                $enddate=date('Y-m-d',strtotime($datetwo));
                $CI =& get_instance();
                $CI->load->model('Dashboard_model', 'dashboardmodel');
                $conversion_lead_stage = $CI->dashboardmodel->getConversionLeadStage();
                ?>
                <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart1);

        function drawChart1() {
        //alert('hi');
        $.ajax({
        url: "<?php echo page_url;?>Salesreporting/pieleadsourcewiseleadconversiondata/<?php echo $stdate; ?>/<?php echo $enddate;?>",
        method: "GET",
        success: function(data) {
        //alert(data);
        drawMonthwiseChart8(data);
        }

        });


        }

          function drawMonthwiseChart8(chart_data) {
          //alert(chart_data);
          var data = google.visualization.arrayToDataTable(JSON.parse(chart_data));

          var options = {
           title: '',
            chartArea:{left: '5%', width: '100%', height: '100%'},

          is3D: true,

          pieSliceText: 'value',
          sliceVisibilityThreshold :0,
    fontSize: 17,
    legend: {
      position: 'labeled'
    },
          };

          var chart = new google.visualization.PieChart(document.getElementById('piechart1'));

          chart.draw(data, options);
          }

      
    </script>


              <!--  <div class="col-sm-12">
                <div class="card-box">
                
                <div class="ribbon11-wrapper">
                <h5 class="ribbon11"><b>Source Of Conversion </b></h5>
                <p><?php echo date('d-M-Y',strtotime($dateone)); ?> to <?php echo date('d-M-Y',strtotime($datetwo)); ?></p>
                </div>

                <div class="row">
  <div class="col-sm-3"></div>
  <div class="col-sm-6" style="border: 1px dashed lightgray; margin-top: 20px; border-radius: 5px;">
  <div id="piechart1" style="height: 300px; width: 100%; text-align: center; "></div>

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
  <h5 class="ribbon11"><b>Source Of Conversion </b></h5>
  <p><?php echo date('d-M-Y',strtotime($dateone)); ?> to <?php echo date('d-M-Y',strtotime($datetwo)); ?></p>
</div>

<div class="row">
  <div class="col-sm-3"></div>
  <div class="col-sm-6" style="border: 1px dashed lightgray; margin-top: 20px; border-radius: 5px;">
  <div id="piechart1" style="height: 300px; width: 100%; text-align: center; "></div>

  </div>
  <div class="col-sm-3"></div>
</div>
</div>

<div id="table-btn" class="gridcontent table-graph">
  <div class="ribbon11-wrapper">
  <h5 class="ribbon11"><b>Source Of Conversion </b></h5>
  <p><?php echo date('d-M-Y',strtotime($dateone)); ?> to <?php echo date('d-M-Y',strtotime($datetwo)); ?></p>
</div>
<div class="row">
<div class="col-sm-2"></div>
  <div class="col-sm-8" style="border: 1px dashed lightgray; margin-top: 20px; border-radius: 5px; padding:10px;">
<table>
  <tr>
    <th>Source of leads</th>
    <th>Leads Count</th>
  </tr>
  <?php
  $company_id=$_SESSION['logged_in']['business_location'];
   if($stdate<>'')
        {
              $sql="AND a.added_on>= '".$stdate."' AND a.added_on<= '".$enddate."'";
        }else
        {
            $sql='';
        }

        if($conversion_lead_stage<>'')
        {
  $this->db->select('source_id,lead_source')->from('lead_source')->where('company_id',$_SESSION['logged_in']['business_location'])->where('status',1);
        $query=$this->db->get();
        if($query->num_rows() >0)
            { 
                $i=1;    
                foreach ($query->result() as $row) {

                $sourceid=$row->source_id;


                $query1=$this->db->query("SELECT b.id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status IN ($conversion_lead_stage) AND b.lead_source_id='$sourceid' $sql AND b.company_id='$company_id'");

                if($query1->num_rows() >0)
                {
                    $i=1;
                    $leadcount=$query1->num_rows();
                    if($leadcount>0)
                    {

          ?>
  <tr>
    <td><?php echo $row->lead_source; ?></td>
    <td><?php echo $leadcount; ?></td>
  </tr>
<?php }} }}else{?>  
<tr>
  <td colspan="2">No Data Available
  </td>
</tr>



   <?php } }else{?> 
    <tr>
  <td colspan="2">No Data Available
  </td>
</tr> <?php } ?>
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
          