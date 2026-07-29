<?php
$CI =& get_instance();
$CI->load->model('Lead_model'); 
$stdate=$this->uri->segment(4);
$enddate=$this->uri->segment(5);   
$DI =& get_instance();
$DI->load->model('Dashboard_model', 'dashboardmodel');
$conversion_lead_stage = $DI->dashboardmodel->getConversionLeadStage();
?>
<script type="text/javascript">
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart1);

function drawChart1() {

$.ajax({
url: "<?php echo page_url; ?>Salesreporting/pieleadquotationtoconversiondata/<?php echo $stdate; ?>/<?php echo $enddate;?>",
method: "GET",
success: function(data) {
drawMonthwiseChart8(data);
}

});


}

  function drawMonthwiseChart8(chart_data) {
//  alert(chart_data);
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


  <?php 
                $uri=$this->uri->segment(3);
                $stdate=$this->uri->segment(4)." 00:00:00";
                $enddate=$this->uri->segment(5)." 00:00:00";
                $dateone=date('d-m-Y',strtotime($stdate));
                $datetwo=date('d-m-Y',strtotime($enddate));
                ?>
              

 <div class="col-sm-12">
    <div class="card-box"> 

    <div class="tablebutton">
  <button class="gridlist" onclick="opengrid(event, 'graph-btn')" id="defaultopen"><i class="fa fa-pie-chart" aria-hidden="true"></i></button>
  <button class="gridlist" onclick="opengrid(event, 'table-btn')"><i class="fa fa-table" aria-hidden="true"></i></button>
</div>

<div id="graph-btn" class="gridcontent">
<div class="ribbon11-wrapper">
  <h5 class="ribbon11"><b>Quotation to Conversion</b></h5>
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
  <h5 class="ribbon11"><b>Quotation to Conversion</b></h5>
  <p><?php echo date('d-M-Y',strtotime($dateone)); ?> to <?php echo date('d-M-Y',strtotime($datetwo)); ?></p>
</div>
<div class="row">
<div class="col-sm-2"></div>
  <div class="col-sm-8" style="border: 1px dashed lightgray; margin-top: 20px; border-radius: 5px; padding:10px;">
<table class="table table-striped table-bordered pretty">
  <tr>
    <th>Months</th>
    <th>Leads Count</th>
  </tr>
  <?php
  if($conversion_lead_stage<>'')
  {
                $monthstdate=date('Y-m-d',strtotime($this->uri->segment(4)))." 00:00:00";
                $monthenddate=date('Y-m-d',strtotime($this->uri->segment(5)))." 23:59:59";

                $this->db->select('id')->from('progress_remarks')->where('lead_status', $conversion_lead_stage);
                if($stdate<>'')
                {
                $this->db->where('added_on >=', $monthstdate)->where('added_on <=',$monthenddate); 
                }

                $query=$this->db->get();

                if($query->num_rows()>0)
                {
              
                
          ?>
  <tr>
    <td><?php echo date('d-M-Y',strtotime($monthstdate))." to ".date('d-M-Y',strtotime($monthenddate)); ?></td>
    <td><?php echo $query->num_rows(); ?></td>
  </tr>
<?php  }else{ ?> 

  <tr>
    <td colspan="2">No Data Available</td>
  </tr>

<?php } }else {?>

  <tr>
    <td colspan="2">No Data Available</td>
  </tr>

<?php  }?>
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