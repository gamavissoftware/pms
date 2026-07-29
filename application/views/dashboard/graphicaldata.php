<?php
    $is_admin = 0;
    $user_role= $this->session->userdata['logged_in']['role'];
    $CI =& get_instance();
    $CI->load->model('Dashboard_model');
    $adminuserrole = $CI->Dashboard_model->getsuperadminuserole();
    if(count($adminuserrole)>0){
    if(in_array($user_role,$adminuserrole)){
    $is_admin = 1;
    $_SESSION['logged_in']['adminuser'] = 1;
    }else{
    $_SESSION['logged_in']['adminuser'] = 0;
    }
    }
    $CIA =& get_instance();
    $CIA->load->model('Task_model');
    $runningdfno = $CIA->Task_model->getallrunningdf();

    if($is_admin==0){
    $department = $this->session->userdata['logged_in']['department_id'];
    $departmentid = array();
    $user_id =$this->session->userdata['logged_in']['user_id'];
    $q = $this->db->select('department_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
    if($q->num_rows()>0){
    foreach($q->result() as $row){
    $departmentid[] =$row->department_id;
    //HOD 
    $_SESSION['logged_in']['adminuser'] = 2;
    }
    }else{
    // normal user
    $_SESSION['logged_in']['adminuser'] = 3;
    }

    }
    $DI = &get_instance();
$DI->load->model('Dashboard_model');

    ?>
<style>
    .chart-container {
      width: 100%;
      max-width: 800px;
      margin: auto;
      background-color: #ffffff;
      padding: 20px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      border-radius: 10px;
    }
    .stats-box {
      padding: 10px 10px;
      color: white;
      border: none;
      border-radius: 15px;
      text-align: center;
      margin-bottom: 30px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .stats-box:hover {
      transform: translateY(-10px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    }
    .stats-box1 {
      padding: 10px 10px;
      color: black;
      border: none;
      border-radius: 15px;
      text-align: center;
      margin-bottom: 30px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .stats-box1:hover {
      transform: translateY(-10px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    }
    .stats-value1 {
      font-size: 14px;
      font-weight: bold;
      margin-bottom: 10px;
    }
    .stats-label1 {
      font-size: 14px;
      color: black;
      font-weight: bold;
    }
    .stats-icon {
      font-size: 26px;
/*      margin-bottom: 20px;*/
      color: rgba(255, 255, 255, 0.8);
    }
    .stats-value {
      font-size: 14px;
      font-weight: bold;
      margin-bottom: 10px;
    }
    .stats-label {
      font-size: 14px;
      color: rgba(255, 255, 255, 0.8);
      font-weight: bold;
    }
    hr {
    margin-top: 0px;
    margin-bottom: 10px;
    color:black !important;
    border: 0;
    border-top: 1px solid #c6c2c2;
}
  .hrclas {
    margin-top: 0px;
    margin-bottom: 10px;
    color:black !important;
    border: 0;
    border-top: 1px solid #000;
}


  </style>

    <div class="container-fluid">
    <div class="row">
    <div class="col-sm-5">
      <div class="row card-box" style="min-height:600px;">
        <div class="col-md-12">
            <h3 class="page-title text-center">DF & OPPORTUNITY INFORMATION <a href="<?php echo page_url;?>Dashboard/closeddf"><span class="btn btn-danger btn-xs">Closed DF</span></a></h3> <hr>
        </div>
        <div class="row">
            <a href="<?php echo page_url;?>Task/dfreleasedashboard/"><div class="col-sm-3 col-md-3" style="min-height:130px">
     <div class="stats-box1"  style="background-color:#b2d8b0 !important;">
       
        <div class="stats-value1"><?php 
        $q = $this->db->select('id')->from('df_release')->where('on_hold',0)->where('df_status',0)->get();
        echo $q->num_rows();
      ?></div><div class="hrclas"></div>
        <div class="stats-label1">RUNNING DF</div>
      </div>
    </div></a>

        <a href="<?php echo page_url;?>Task/dfreleasedashboard/1"><div class="col-sm-3 col-md-3" style="min-height:130px">
     <div class="stats-box1" style="background-color: #f8d568 !important;">
       
        <div class="stats-value1"><?php 
        $show = array();
$show[] = 0;
$m=1;
$reportid = $this->uri->segment(3);
$q = $this->db->select('id, df_no, added_on, df_upload, added_on')->from('df_release')->where('on_hold',0)->where('df_status',0)->get();
if($q->num_rows()>0){
foreach($q->result() as $rows){

$q1 = $this->db->select('MAX(end_date) as enddate')->from('task_department_wise_scheduling')->where('df_id',$rows->id)->get();
if($q1->num_rows()>0){
foreach($q1->result() as $r);
$planneddate = date('d-m-Y',strtotime($r->enddate));
}else{
$planneddate = '';
}


$q1 = $this->db->select('id')->from('task_department_wise_scheduling')->where('df_id',$rows->id)->get();
$count = $q1->num_rows();
$delaycount = array();
$delaycount[] = 0;
$totaldayscountarray = array();
$totaldayscountarray[] =0;
$q2 = $this->db->select('id, task_completed_on, end_date')->from('task_department_wise_scheduling')->where('df_id',$rows->id)->where('task_status',1)->get();
$totaldone = $q2->num_rows();
if($count>0){
$percetage =  round($totaldone*100/ $count);
}else{
  $percetage = 0;  
}

foreach($q2->result() as $rowss){
if(date('Y-m-d',strtotime($rowss->task_completed_on))>$rowss->end_date){
$delaycount[] = 1;

$daysss = $CIA->Task_model->getDays($rowss->end_date, date('Y-m-d',strtotime($rowss->task_completed_on)),1);

$totaldayscountarray[] = $daysss;

}



}
$delayed = array_sum($delaycount);
$totaldaysdelayed = array_sum( $totaldayscountarray);

$totaldelayedpercentage = round($delayed*100/$totaldone);

$expecteddate =  date('d-m-Y',strtotime($planneddate.' +'.$totaldaysdelayed.' Days'));
$skipped_dates = $CIA->Task_model->SKIPsingle_holidays($expecteddate);
if($totaldelayedpercentage>0){
    $show[]=1;
}else{
    $show[]=0;
}
}

}

echo array_sum($show);
?></div><div class="hrclas"></div>
        <div class="stats-label1">DF DELAYED</div>
      </div>
    </div></a>


       <div class="col-sm-3 col-md-3">
     <div class="stats-box1" style="background-color: #f4a6a6 !important;">
        
        <div class="stats-value1">0</div><div class="hrclas"></div>
        <div class="stats-label1">Dept. Delay</div>
      </div>
    </div>

      <div class="col-sm-3 col-md-3">
     <div class="stats-box1" style="background-color: #b2d8b0 !important;">
        
        <div class="stats-value1">0</div><div class="hrclas"></div>
        <div class="stats-label1">DF With IOM</div>
      </div>
    </div>

        </div>
    

<div class="row">
       <div class="col-sm-4 col-md-4" style="min-height:130px">
     <div class="stats-box1" style="background-color: #a9cfe7 !important; min-height: 114px;">
        <div class="stats-value1"><?php 
        $amount = array();
        $amount[] = 0;
          $q = $this->db->select('b.order_value')->from('df_release a')->join('poreceived b','a.id=b.df_id')->where('df_status',0)->get();
          if($q->num_rows()>0){
            foreach($q->result() as $row){
              $amount[] = $row->order_value;
            }
          }

        function formatIndianNumber($number) {
        $number_parts = explode(".", $number);
        $integer_part = $number_parts[0];
        $decimal_part = isset($number_parts[1]) ? '.' . $number_parts[1] : '';

        // Handle negative numbers
        $negative = '';
        if ($integer_part[0] == '-') {
        $negative = '-';
        $integer_part = substr($integer_part, 1);
        }

        // Split the integer part into 3 digits for the last group and 2 digits thereafter
        $lastThree = substr($integer_part, -3);
        $restUnits = substr($integer_part, 0, -3);

        if ($restUnits != '') {
        $lastThree = ',' . $lastThree;
        }

        $result = $negative . preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $restUnits) . $lastThree . $decimal_part;
        return $result;
        }

          $totalamount = array_sum($amount);
          $val = formatIndianNumber($totalamount);
          echo $val;
      ?></div><div class="hrclas"></div>
        <div class="stats-label1">RUNNING DF(S) VALUE</div>
      </div>
    </div>
  <div class="col-sm-4 col-md-4" style="min-height:130px">
     <div class="stats-box1" style="background-color: #cda2d3 !important; min-height: 114px;">
       
        <div class="stats-value1">0</div><div class="hrclas"></div>
        <div class="stats-label1">LOSS AGAINST DELAY</div>
      </div>
    </div>

     <a href="<?php echo page_url;?>Reporting/penalitydf"> 
        <div class="col-sm-4 col-md-4" style="min-height:130px">
     <div class="stats-box1" style="background-color: #ffec85 !important; min-height: 114px;">
        <div class="stats-value1"><?php 
        $q= $this->db->select('id')->from('poreceived')->where('penalityamount!=',0)->get();
        echo $q->num_rows();
      ?></div><div class="hrclas"></div>
        <div class="stats-label1">PENALITY DF</div>
      </div>
    </div>
</a>

</div>

<div class="row">
    <div class="col-md-12">
        <h4 class="text-center">NEW BUSINESS OPPORTUNITY</h4><hr>
    </div>
        <a href="<?php echo page_url;?>"><div class="col-sm-4 col-md-4">
     <div class="stats-box" style="background-color: #FF6347 !important;">
       
        <div class="stats-value">
          <?php 
          $date = new DateTime();
          $date->modify('-15 days');
          $createdate =  $date->format('Y-m-d');
          $q = $this->db->select('id')->from('leads')->where('create_date>=',$createdate)->get();
          echo $q->num_rows();
          ?>
        
      </div><hr>
        <div class="stats-label">NEW OPPORTUNITY IN LAST 15 DAYS</div>
      </div>
    </div></a>

    <?php 
    $date = new DateTime();
    $date->modify('-15 days');
    $createdate =  $date->format('Y-m-d');
    ?>
     <a href="<?php echo page_url;?>Leads/lead_stages/33"><div class="col-sm-4 col-md-4">
     <div class="stats-box"  style="background-color: #4B0082 !important;">
       
        <div class="stats-value"><?php 
        $stageid = 33;
        $last15daysquotationcount = $DI->Dashboard_model->lead_stage_counts_in_15days($createdate,$stageid);
        echo $last15daysquotationcount;
      ?></div>
      <hr>
        <div class="stats-label">QUOTATION <br>SENT IN LAST 15 DAYS</div>
      </div>
    </div></a>
    <?php 
            $date = new DateTime();
            $date->modify('-15 days');
            $enddate =  $date->format('Y-m-d');
            $startdate = date('Y-m-d');
    ?>
    <a href="<?php echo page_url;?>Task/receivedpolist/<?php echo base64_encode($startdate);?>/<?php echo base64_encode($enddate);?>"><div class="col-sm-4 col-md-4">
     <div class="stats-box"  style="background-color: #D2691E !important;">
       
        <div class="stats-value">
            <?php 
            $orderval[] = 0;
            $date = new DateTime();
            $date->modify('-15 days');
            $enddate =  $date->format('Y-m-d');
            $startdate = date('Y-m-d');

            $q = $this->db->select('a.lead_id')->from('progress_remarks a')->where('lead_status',35)->where('a.added_on BETWEEN "'.$startdate. '" and "'.$enddate.'"')->group_by('lead_id')->get();
            foreach($q->result() as $row){
                $q2 = $this->db->select('order_value')->from('poreceived')->where('lead_id',$row->lead_id)->get();
                if($q2->num_rows()>0){
                    foreach($q2->result() as $row2){
                        $orderval[] = $row2->order_value;
                    }
                }
            }
            echo array_sum($orderval);
            ?>

        </div><hr>
        <div class="stats-label">OPPORTUNITY CONVERTED TO SALES IN LAST 15 DAYS</div>
      </div>
    </div></a>

</div>

</div>
  <div class="row card-box">
     <div class="col-md-12">
      <h3 class="page-title text-center">SALES (EXPORT/DOMESTIC)</h3><hr>
      
    </div>

  

   <div style="width: 70%; margin: auto;">
        <canvas id="orderChart"></canvas>
    </div>
  </div>
    </div>
    <div class="col-md-7 card-box" style="min-height:600px;">
        <div class="col-md-12">
            <?php 
                $year = date('Y');
                $month = date('m');
                $date = new DateTime("$year-$month-01");
                $date->modify('last day of this month');
                $ldate = $date->format('Y-m-d');
                $ssdate = date('Y-m')."-01";
                
            ?>
            <h3 class="page-title text-center">COMPANY REVENUE INFORMATION <a href="<?php echo page_url;?>Dashboard/df_dispatch_report/<?php echo $ssdate;?>/<?php echo $ldate;?>"><span class="btn btn-danger btn-xs">DF Dispatch Report</span></a></h3><hr>
              <div class="col-md-12">
                <div class="row">
                   <table class="table">
            <thead class="thead-dark">
              <tr>
                <th colspan="3" style="text-align: center;"><strong><h4 style="font-weight: 800;">MONTHLY TARGET - 10 CR.</h4></strong></th>
              </tr>
              
                <tr>
                  <th>UPTO 5 CR.</th>
                  <th>UPTO 9 CR.</th>
                  <th>ABOVE 9 CR.</th>
                  </tr>
            </thead>
            <tbody>
                <tr>
                    
                    <td><div style="padding:5px 5px 5px 5px; border: 1px solid #000; background-color: #FF5733;">
                    </div></td>
                    <td><div style="padding:5px 5px 5px 5px; border: 1px solid #000; background-color: #E8EC00;">
                    </div></td>
                    <td><div style="padding:5px 5px 5px 5px; border: 1px solid #000; background-color: #2B8B3B;">
                    </div></td>
                </tr>
                
            </tbody>
        </table>
                 
                </div>
              </div>
        </div>
        <div style="width: 88%; margin: auto;">
           <canvas id="salesChart"></canvas>
        </div>
           
    </div>

    
    <div class="col-md-7 card-box" style="min-height:480px;">
        <div class="row">
 <div class="col-md-12">
            <h3 class="page-title text-center">FACTORY INFORMATION</h3><hr>
        </div>
           
                


         <a href="<?php echo page_url;?>Reporting/machinereadyonfloor"><div class="col-sm-4 col-md-4">
     <div class="stats-box"  style="background:linear-gradient(135deg, #ff7f0e, #ff7f0e) !important;">
        <div class="stats-value">
            <?php 
            $readyonfloor = array();
            $readyonfloor[]= 0;
            $q = $this->db->select('task_id')->from('task_management')->where('machinereadyonfloor',1)->get();
            if($q->num_rows()>0){
                foreach($q->result() as $row);
                $q2 = $this->db->select('a.id')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id','left')->where('a.taskid',$row->task_id)->where('a.task_status',1)->where('b.df_status',0)->get();
                if($q2->num_rows()>0){
                    foreach($q2->result() as $row2){
                        $readyonfloor[] = 1;
                    }
                }
            ?>
            <?php 
        }
        echo array_sum($readyonfloor);
            ?>


        </div><hr>
        <div class="stats-label">READY MACHINE(S) ON FLOOR</div>
      </div>
    </div></a>

     <a href="<?php echo page_url;?>Reporting/dispatchinnext15days"><div class="col-sm-4 col-md-4">
     <div class="stats-box" style="background:linear-gradient(135deg, #1f77b4, #1f77b4) !important;">
       
        <div class="stats-value"><?php 
        $dispatchcount = array();
        $dispatchcount[] = 0;
        $originalDate = date('Y-m-d');
        $date = new DateTime($originalDate);
        $date->modify('+15 days');
        $newDate = $date->format('Y-m-d');
        $q= $this->db->select('id')->from('task_department_wise_scheduling')->where('taskid',103)->where('end_date BETWEEN "'. date('Y-m-d', strtotime($originalDate)). '" and "'. date('Y-m-d', strtotime($newDate)).'"')->get();
        if($q->num_rows()>0){
            foreach($q->result() as $row){
                $dispatchcount[] = 1;
            }
        }
        echo array_sum($dispatchcount);

        ?>
        
    </div><hr>
        <div class="stats-label">DISPATCHES IN NEXT <br>15 DAYS</div>
      </div>
    </div></a>

     <a href="<?php echo page_url;?>Reporting/fatsinnext15days"> <div class="col-sm-4 col-md-4">
     <div class="stats-box" style="background:linear-gradient(135deg, #9467bd, #9467bd) !important;">
      
        <div class="stats-value"><?php 
        $dispatchcount = array();
        $dispatchcount[] = 0;
        $originalDate = date('Y-m-d');
        $date = new DateTime($originalDate);
        $date->modify('+15 days');
        $newDate = $date->format('Y-m-d');
        $q= $this->db->select('id')->from('task_department_wise_scheduling')->where('taskid',93)->where('end_date BETWEEN "'. date('Y-m-d', strtotime($originalDate)). '" and "'. date('Y-m-d', strtotime($newDate)).'"')->get();
        if($q->num_rows()>0){
            foreach($q->result() as $row){
                $dispatchcount[] = 1;
            }
        }
        echo array_sum($dispatchcount);

        ?></div><hr>
        <div class="stats-label">FAT(s) IN NEXT <br>15 DAYS</div>
      </div>
    </div></a>


            

            
                 <div class="col-md-12">
            <h3 class="page-title text-center">PAYMENT INFORMATION</h3><hr>
        </div>

         <div class="col-sm-4 col-md-4">
     <div class="stats-box"  style="background:linear-gradient(135deg, #8c564b, #61f161) !important;">
        
        <div class="stats-value"><?php 
        $totalrecaivedamount[] = 0;
        $paymentcreditedin15days = $DI->Dashboard_model->paymentcreditedin15days();
        foreach($paymentcreditedin15days as $row){
          $totalrecaivedamount[] = $row->amount_received;
        }
        $ttlrcvamt =  array_sum($totalrecaivedamount);

        $AMT = $DI->Dashboard_model->formatIndianNumber($ttlrcvamt);
        echo $AMT;
        
      ?></div><hr>
        <div class="stats-label">PAYMENT CREDITED IN LAST <br>15 DAYS</div>
      </div>
    </div>

      <a href="<?php echo page_url;?>Reporting/paymentsinnext15days"><div class="col-sm-4 col-md-4">
     <div class="stats-box" style="background:linear-gradient(135deg, #ff7f0e, #8c564b) !important;">
      
        <div class="stats-value"><?php 
        // $upcomingtotalamount[] = 0;
        // $paymentinnext15days = $DI->Dashboard_model->paymentinnext15days();
        // foreach($paymentinnext15days as $row){
        //   $upcomingtotalamount[] = $row->amount_received;
        // }
        // $upcomingttlamount =  array_sum($upcomingtotalamount);

        // $Upcomettlamt = $DI->Dashboard_model->formatIndianNumber($upcomingttlamount);
        // echo $Upcomettlamt;
        $next15dayspayment = array();
        $next15dayspayment[] = 0;

        $date = new DateTime();
    $date->modify('+15 days');
    $createdate =  $date->format('Y-m-d');
    $enddate = $createdate;
    $startdate = date('Y-m-d');
     $q = $this->db->select('b.id, b.df_no, b.added_on, b.df_upload, b.added_on')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id','left')->where('a.paymentstage',1)->where('a.end_date BETWEEN "'. date('Y-m-d', strtotime($startdate)). '" and "'. date('Y-m-d', strtotime($enddate)).'"')->where('b.df_status',0)->get();
        if($q->num_rows()>0){
            foreach($q->result() as $row){
                $next15dayspayment[] = 1;
            }
        }

        echo array_sum($next15dayspayment);
      ?></div><hr>
        <div class="stats-label">PAYMENTS IN NEXT <br>15 DAYS</div>
      </div>
    </div></a>

      <div class="col-sm-4 col-md-4">
     <div class="stats-box"  style="background:linear-gradient(135deg, #c13b3b, #ec1b1b) !important;">
       
        <div class="stats-value">0</div><hr>
        <div class="stats-label">PAYMENT OVERDUE</div>
      </div>
    </div>


           
        </div>

 
    </div>

 

    <!--  <div class="col-md-12 card-box">
         <div class="col-md-12">
            <h3 class="page-title text-center">NEW BUSINESS OPPORTUNITIES</h3><hr>
        </div>
        <a href="<?php echo page_url;?>"><div class="col-sm-4 col-md-4">
     <div class="stats-box" style="background-color: #FF6347 !important;">
        <div class="stats-icon">
          <i class="fa fa-users"></i>
        </div>
        <div class="stats-value">
          <?php 
          $date = new DateTime();
          $date->modify('-15 days');
          $createdate =  $date->format('Y-m-d');
          $q = $this->db->select('id')->from('leads')->where('create_date>=',$createdate)->get();
          echo $q->num_rows();
          ?>
        
      </div><hr>
        <div class="stats-label">NEW OPPORTUNITY IN LAST 15 DAYS</div>
      </div>
    </div></a>


    <a href="<?php echo page_url;?>Leads/lead_stages/31"><div class="col-sm-4 col-md-4">
     <div class="stats-box"  style="background-color: #4B0082 !important;">
        <div class="stats-icon">
          <i class="fa fa-files-o"></i>
        </div>
        <div class="stats-value"><?php 
        $stageid = 31;
        $last15daysquotationcount = $DI->Dashboard_model->lead_stage_counts_in_15days($createdate,$stageid);
        echo $last15daysquotationcount;
      ?></div><hr>
        <div class="stats-label">QUOTATION SENT IN LAST 15 DAYS</div>
      </div>
    </div></a>


    <div class="col-sm-4 col-md-4">
     <div class="stats-box"  style="background-color: #D2691E !important;">
        <div class="stats-icon">
          <i class="fa fa-briefcase"></i>
        </div>
        <div class="stats-value">₹ 10,500,000</div><hr>
        <div class="stats-label">OPPORTUNITY CONVERTED TO SALES</div>
      </div>
    </div>
    </div> -->


    </div>


    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('salesChart').getContext('2d');

            const salesData = <?php echo json_encode($sales_data); ?>;
            const months = salesData.map(data => data.month);
            const orderValues = salesData.map(data => parseFloat(data.order_value));

            const colors = orderValues.map(value => {
                if (value <= 50000000) {
                    return '#FF5733';
                } else if (value < 90000000) {
                    return '#E8EC00';
                } else {
                    return '#2B8B3B';
                }
            });

            const formatIndianNumber = (num) => {
                num = num.toString();
                let lastThree = num.substring(num.length - 3);
                let otherNumbers = num.substring(0, num.length - 3);
                if (otherNumbers !== '') {
                    lastThree = ',' + lastThree;
                }
                let res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
                return '₹' + res;
            };

            const chartData = {
                labels: months,
                datasets: [{
                    label: 'Monthly Sales (in ₹)',
                    data: orderValues,
                    backgroundColor: colors
                }]
            };

            const config = {
                type: 'bar',
                data: chartData,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return formatIndianNumber(value);
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += formatIndianNumber(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            };

            new Chart(ctx, config);
        });
    </script>