   <?php 
      $stdate=$this->uri->segment(4);
      $enddate=$this->uri->segment(5);
      $CI =& get_instance();
      $CI->load->model('Dashboard_model', 'dashboardmodel');
      $unqualified_lead_stage = $CI->dashboardmodel->getUnqualifiedLeadStage();
      $mode=$CI->dashboardmodel->getsettings();
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
        url: "<?php echo page_url; ?>Salesreporting/pieleadsunqulifiedbyregiondata/<?php echo $stdate; ?>/<?php echo $enddate;?>",
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
        
          is3D: true,

 chartArea:{left: '5%', width: '100%', height: '100%'},
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
  <h5 class="ribbon11"><b>Unqualified Leads by Region</b></h5>
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
  <h5 class="ribbon11"><b>Unqualified Leads by Region</b></h5>
  <p><?php echo date('d-M-Y',strtotime($dateone)); ?> to <?php echo date('d-M-Y',strtotime($datetwo)); ?></p>
</div>

<div class="row">
<div class="col-sm-2"></div>
  <div class="col-sm-8" style="border: 1px dashed lightgray; margin-top: 20px; border-radius: 5px; padding:10px;">
<table>

  <tr>
    <th>Region</th>
    <th>Leads Count</th>
  </tr>
  <?php

if($unqualified_lead_stage<>'')
{
  if($mode==1)
  {
 $query=$this->db->select('state_id,state_name')->from('states')->where('state_status',1)->get();
        if($query->num_rows() >0)
            { 
            $i=1;    
            foreach ($query->result() as $row) {
            $this->db->select('a.id')->from('leads a')->join('progress_remarks b','a.id=b.lead_id')->where('a.state',$row->state_id)->where('b.lead_status',$unqualified_lead_stage)->where('a.company_id',$_SESSION['logged_in']['business_location']);
            if($stdate<>'')
            {
            $this->db->where('a.added_on >=', $stdate)->where('a.added_on <=',$enddate); 
            }
            $q=$this->db->get();

            $leadcount=$q->num_rows();
            if($leadcount>0)
            {
             $stname=$row->state_name;                
          ?>
  <tr>
    <td><?php echo $stname; ?></td>
    <td><?php echo $leadcount; ?></td>
  </tr>
<?php } } }else{?> 
 <tr>
    <td colspan="2">No Data Available</td>
  </tr> 

   <?php } }else{?>

<?php
 $query=$this->db->select('id as state_id,zone as state_name')->from('saleszone')->where('company_id',$_SESSION['logged_in']['business_location'])->get();
        if($query->num_rows() >0)
            { 
            $i=1;    
            foreach ($query->result() as $row) {
            $this->db->select('a.id')->from('leads a')->join('progress_remarks b','a.id=b.lead_id')->where('a.client_location',$row->state_id)->where('a.company_id',$_SESSION['logged_in']['business_location'])->where('b.lead_status',$unqualified_lead_stage);
            if($stdate<>'')
            {
            $this->db->where('a.added_on >=', $stdate)->where('a.added_on <=',$enddate); 
            }
            $q=$this->db->get();

            $leadcount=$q->num_rows();
            if($leadcount>0)
            {
             $stname=$row->state_name;                
          ?>
  <tr>
    <td><?php echo $stname; ?></td>
    <td><?php echo $leadcount; ?></td>
  </tr>
<?php } } }else{?>   
  <tr>
    <td colspan="2">No Data Available</td>
  </tr>   
<?php } }  }else{?> 

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