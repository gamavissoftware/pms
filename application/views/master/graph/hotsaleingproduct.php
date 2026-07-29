<?php
      $CI =& get_instance();
      $CI->load->model('Lead_model');  
      $stdate=$this->uri->segment(4);
      $enddate=$this->uri->segment(5);
      $dateone=date('d-M-Y',strtotime($this->uri->segment(4)));
      $datetwo=date('d-M-Y',strtotime($this->uri->segment(5)));
      $CI =& get_instance();
      $CI->load->model('Dashboard_model', 'dashboardmodel');
      $conversion_lead_stage = $CI->dashboardmodel->getConversionLeadStage();
      ?>
   <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart1);

        function drawChart1() {
        //alert('hi sele');
        $.ajax({
        url: "<?php echo page_url; ?>Salesreporting/pieleadshighsellingdata/<?php echo $stdate; ?>/<?php echo $enddate;?>",
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
          var chart = new google.visualization.PieChart(document.getElementById('piechart'));

          chart.draw(data, options);
          }

      
    </script>


             
<div class="col-sm-12">
    <div class="card-box"> 

    <div class="tablebutton">
  <button class="gridlist" onclick="opengrid(event, 'graph-btn')" id="defaultopen"><i class="fa fa-pie-chart" aria-hidden="true"></i></button>
  <button class="gridlist" onclick="opengrid(event, 'table-btn')"><i class="fa fa-table" aria-hidden="true"></i></button>
</div>

<div id="graph-btn" class="gridcontent">
<div class="ribbon11-wrapper">
  <h5 class="ribbon11"><b>Top Selling Product </b></h5>
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

<div id="table-btn" class="gridcontent table-graph">
 <div class="ribbon11-wrapper">
  <h5 class="ribbon11"><b>Top Selling Product </b></h5>
  <p><?php echo date('d-M-Y',strtotime($dateone)); ?> to <?php echo date('d-M-Y',strtotime($datetwo)); ?></p>
</div>
<div class="row">
<div class="col-sm-2"></div>
  <div class="col-sm-8" style="border: 1px dashed lightgray; margin-top: 20px; border-radius: 5px; padding:10px;">
<table>

  <tr>
    <th>Product</th>
    <th>Sale Count</th>
  </tr>
  <?php
  $prddata = array();
  if($conversion_lead_stage<>'')
  {

       $query=$this->db->query("SELECT a.lead_id FROM progress_remarks a WHERE a.added_on IN (SELECT MIN(added_on) FROM progress_remarks WHERE lead_status='$conversion_lead_stage' GROUP BY a.lead_id) AND a.added_on>='$stdate' AND a.added_on<='$enddate'");
        if($query->num_rows()>0)
        {

            foreach($query->result() as $row)
            {
                $resteye=$this->db->select('product_id')->from('lead_products')->where('lead_id',$row->lead_id)->get();
                if($resteye->num_rows()>0)
                {
                    foreach($resteye->result() as $rowsss)
                    {
                        $prddata[]=$rowsss->product_id;

                    }


                }


            }


        }

  }
         if(count(array_count_values($prddata))>0)
            {
                $prddata1=array_count_values($prddata);
                arsort($prddata1);

               
                $t=1;
                foreach ($prddata1 as $key => $value)
                {
                    $instrumentname=$CI->Lead_model->getinstrumentname($key);
                    $data[$t][0] = $instrumentname;
                    $data[$t][1] = $value;
                    $t++;
                              
          ?>
  <tr>
    <td><?php echo $instrumentname; ?></td>
    <td><?php echo $value; ?></td>
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