   <?php

      $user=$this->uri->segment(4);
      // $users=base64_decode($user);
      //           echo $users; exit;
      $stdate=$this->uri->segment(5);
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
        url: "<?php echo page_url; ?>Piechart/discountgivenbyteam/<?php echo $user ?>/<?php echo $stdate; ?>/<?php echo $enddate;?>",
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
    data.addColumn('number', 'Discount');
    data.addColumn({type:'string', role:'annotation'});

    $.each(JSON.parse(jsonData), function(i, jsonData){
        var industry = jsonData.indus;
        var leads = parseFloat($.trim(jsonData.count));
        var labels = parseFloat($.trim(jsonData.labels));
        data.addRows([[industry, leads,"₹ "+labels]]);
    });

    var options = {
        colors: ['gray'],
        hAxis: {
            title: "Teams"
        },
        vAxis: {
            title: 'Discount',
            format: '0'
        },
        chartArea:{width:'80%',height:'60%'}
    }

      var chart = new google.visualization.ColumnChart(document.getElementById('chart_area2'));

      chart.draw(data, options);
      }
      
    </script>


  <!--  <div class="col-sm-12">
        <div class="card-box">
            <?php 
                $uri=$this->uri->segment(3);
                $stdate=$this->uri->segment(5);
                $enddate=$this->uri->segment(6);
                $dateone=date('d-m-Y',strtotime($stdate));
                $datetwo=date('d-m-Y',strtotime($enddate));
                ?>
                  <div class="ribbon11-wrapper">
                    <h5 class="ribbon11"><b>Discount Given by Team</b></h5>
                    <p><?php echo date('d-M-Y',strtotime($dateone)); ?> to <?php echo date('d-M-Y',strtotime($datetwo)); ?></p>
                   </div>
          
          <div id="chart_area2" style="width: 100%; height: 420px;"></div>
        </div>
      </div> -->

      <div class="col-sm-12">
    <div class="card-box"  style="height: 515px;"> 

    <div class="tablebutton">
  <button class="gridlist" onclick="opengrid(event, 'graph-btn')" id="defaultopen"><i class="fa fa-pie-chart" aria-hidden="true"></i></button>
  <button class="gridlist" onclick="opengrid(event, 'table-btn')"><i class="fa fa-table" aria-hidden="true"></i></button>
</div>

<div id="graph-btn" class="gridcontent">
<div class="ribbon11-wrapper">
  <h5 class="ribbon11"><b>Discount Given By Team</b></h5>
  <p><?php echo date('d-M-Y',strtotime($dateone)); ?> to <?php echo date('d-M-Y',strtotime($datetwo)); ?></p>
</div>

<div class="row">
  <div id="chart_area2" style="width: 100%; height: 420px;"></div>

  </div>
</div>
<div id="table-btn" class="gridcontent table-graph">
  <div class="ribbon11-wrapper">
  <h5 class="ribbon11"><b>Discount Given By Team</b></h5>
  <p><?php echo date('d-M-Y',strtotime($dateone)); ?> to <?php echo date('d-M-Y',strtotime($datetwo)); ?></p>
</div>
<div class="row">
<div class="col-sm-2"></div>
  <div class="col-sm-8" style="border: 1px dashed lightgray; margin-top: 20px; border-radius: 5px; padding:10px;">
<table>
  <tr>
    <th>Team Name</th>
    <th>Discount (Rs.)</th>
  </tr>
  <?php
  // $user=$this->uri->segment(4);
  //           // $users=base64_decode($user);
  //           if($user<>'NA')
  //           {
  //           $users=base64_decode($user);
  //           }
  //              // echo $users; exit;
  //           $stdate=$this->uri->segment(5);
  //           $enddate=$this->uri->segment(6);
  //       //query to get data from the table
  //           $this->db->select('a.id,b.team_id,b.team_name')->from('leads a')->join('lead_assigned_to_team c','a.id=c.lead_id')->join('prestogroup_teams b','c.lead_id=b.team_id');

  //            if($user<>'NA')
  //               {
  //                   $this->db->where_in('b.team_id',$users,false);
  //               }
  //           if($stdate<>'')
  //           {
  //           $this->db->where('a.added_on >=', $stdate)->where('a.added_on <=',$enddate); 
  //           }
  //           $query = $this->db->get();
  //           if($query->num_rows() >0){
  //           //execute query
  //           $result = $query->result();
  //           $output = array();

  //           foreach($query->result() as $row)
  //           {
  //               $income=array();
  //                $income[]=0;
  //                $sql1 = $this->db->query("SELECT b.id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team c ON a.lead_id=c.lead_id  WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status IN ('6') AND c.team_id=$row->team_id");
  //                $totall = '';
  //                 $total_discount = array();
  //                 $total_discount[] = 0;
  //                 if($sql1->num_rows() >0) {
  //                       foreach($sql1->result() as $row1) {
  //                           $sql2 = $this->db->query("SELECT qty, price, discount_type, percent_amt FROM lead_products WHERE lead_id=$row1->id");
                                
  //                               if($sql2->num_rows() >0) {
  //                                   foreach($sql2->result() as $row2) {
  //                                       $total_price = $row2->price * $row2->qty;

  //                                       if ($row2->discount_type == 0) {
  //                                           $total_discount[] = $total_price - ($total_price * $row2->percent_amt)/100;
  //                                       } else if($row2->discount_type == 1) {
  //                                           $total_discount[] = $total_price - $row2->percent_amt;
  //                                       } else {
  //                                           $total_discount[] = 0;
  //                                       }
  //                               }
  //                           }

  //                           $totall = array_sum($total_discount);
  //                       }
  //                   }

  //               $name=$row->team_name;
  $getDiscountByTeam = $CI->Salescrm_model->getDiscountByTeam($user, $stdate, $enddate);
  $conversion_lead_stage = $DI->dashboardmodel->getConversionLeadStage();

        if ($getDiscountByTeam != '') {

          foreach ($getDiscountByTeam as $row) {
          $getDiscountByTeamData = $CI->Salescrm_model->getDiscountByTeamData($row->team_id, $conversion_lead_stage);
          ?>
        <tr>
          <td><?php echo $row->team_name; ?></td>
          <td><?php echo $getDiscountByTeamData; ?></td>
        </tr>
      <?php }} else {?>
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
          