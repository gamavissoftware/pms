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
    <!DOCTYPE html>
    <html>

    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?>Payment Terms</title>
    <!-- Table Responsive css -->
    <script src="<?php echo assets_url; ?>js/angular.min.js"></script>
    <!-- DataTables -->
    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/our.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/bootoast@1.1.4/dist/bootoast.min.css
    " rel="stylesheet">
    <link href="<?php echo dashboard_asset_url; ?>dashboard.css" rel="stylesheet" type="text/css">
    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
     <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

      
 <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('orderChart').getContext('2d');

            var orderData = <?php echo json_encode($order_data); ?>;
            var labels = [];
            var data = [];

            orderData.forEach(function(order) {
                labels.push(order.order_type === '1' ? 'Dom' : 'Exp');
                data.push(order.percentage);
            });

            var myPieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: ['#FF6384', '#36A2EB'],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        datalabels: {
                            formatter: (value, ctx) => {
                                return ctx.chart.data.labels[ctx.dataIndex] + ': ' + value.toFixed(2) + '%';
                            },
                            color: '#fff',
                            font: {
                                weight: 'bold',
                                size: 16
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        });
    </script>
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
    </head>


    <body>


    <!-- Navigation Bar-->
    <header id="topnav">
    <?php $this->load->view('common/nav-menu'); ?>
    </header>
    <!-- End Navigation Bar-->
    <div class="wrapper">
    <div class="container-fluid">
    <div class="row">
    <div class="col-sm-5">
      <div class="row card-box" style="min-height:600px;">
        <div class="col-md-12">
            <h3 class="page-title text-center">DF & OPPORTUNITY INFORMATION</h3><hr>
        </div>
        <div class="row">
            <a href="<?php echo page_url;?>Task/dfreleasedashboard/"><div class="col-sm-3 col-md-3" style="min-height:130px">
     <div class="stats-box1"  style="background-color:#b2d8b0 !important;">
       
        <div class="stats-value1"><?php 
        $q = $this->db->select('id')->from('df_release')->where('df_status',0)->get();
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
$q = $this->db->select('id, df_no, added_on, df_upload, added_on')->from('df_release')->where('df_status',0)->get();
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
$percetage =  round($totaldone*100/ $count);

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


       <div class="col-sm-3 col-md-3" style="min-height:130px">
     <div class="stats-box1" style="background-color: #f4a6a6 !important;">
        
        <div class="stats-value1">0</div><div class="hrclas"></div>
        <div class="stats-label1">Dept.<br> Delay</div>
      </div>
    </div>

      <div class="col-sm-3 col-md-3" style="min-height:130px">
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
     <a href="<?php echo page_url;?>Leads/lead_stages/31"><div class="col-sm-4 col-md-4">
     <div class="stats-box"  style="background-color: #4B0082 !important;">
       
        <div class="stats-value"><?php 
        $stageid = 37;
        $last15daysquotationcount = $DI->Dashboard_model->lead_stage_counts_in_15days($createdate,$stageid);
        echo $last15daysquotationcount;
      ?></div>
      <hr>
        <div class="stats-label">QUOTATION <br>SENT IN LAST 15 DAYS</div>
      </div>
    </div></a>

    <div class="col-sm-4 col-md-4">
     <div class="stats-box"  style="background-color: #D2691E !important;">
       
        <div class="stats-value">₹ 10,500,000</div><hr>
        <div class="stats-label">OPPORTUNITY CONVERTED TO SALES</div>
      </div>
    </div>

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
            <h3 class="page-title text-center">COMPANY REVENUE INFORMATION</h3><hr>
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




  

    <!-- Footer -->
    <?php $this->load->view('common/footer'); ?>
    <!-- End Footer -->

    </div> <!-- end container -->
    </div>
    <!-- end wrapper -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

    <!-- jQuery  -->
    <!-- <script src="<?php echo assets_url; ?>js/jquery.min.js"></script> -->
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>js/detect.js"></script>
    <script src="<?php echo assets_url; ?>js/fastclick.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.slimscroll.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.blockUI.js"></script>
    <script src="<?php echo assets_url; ?>js/waves.js"></script>
    <script src="<?php echo assets_url; ?>js/wow.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.nicescroll.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.scrollTo.min.js"></script>

    <!-- Datatables-->

    <script src="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.bootstrap.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.buttons.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/jszip.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/pdfmake.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/vfs_fonts.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.html5.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.print.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.fixedHeader.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.keyTable.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.responsive.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.scroller.min.js"></script>




    <!-- Datatable init js -->
    <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>

    <!-- App js -->
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
    <script src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>




    <script>
    $(document).ready(function() {
    var a=$("#task_date_filter").val();

    $('#example').dataTable({

    "bProcessing": true,
    "pagination":true,
    "sAjaxSource": "<?php echo page_url;?>Task/ongoingtasklist/",

    "aoColumns": [

    { mData: 'sr_no' } ,
    { mData: 'df_no' } ,
    { mData: 'dfupload' } ,
    { mData: 'df_release_date' } ,
    { mData: 'department' } ,
    { mData: 'membername' } ,
    { mData: 'task_name' } ,
    { mData: 'end_date' }, 
    { mData: 'pendingdays' }, 
    { mData: 'updateprogress' },
    { mData: 'remarks' }, 
    ]

    });

    $('#example2').dataTable({

    "bProcessing": true,
    "pagination":true,
    "sAjaxSource": "<?php echo page_url;?>Task/outdatedtask",

    "aoColumns": [

    { mData: 'sr_no' } ,
    { mData: 'df_no' } ,
    { mData: 'dfupload' } ,
    { mData: 'df_release_date' } ,
    { mData: 'department' } ,
    { mData: 'membername' } ,
    { mData: 'task_name' } ,
    { mData: 'end_date' }, 
    { mData: 'pendingdays' }, 
    { mData: 'updateprogress' },
    { mData: 'remarks' }


    ]

    });

    $('#example3').dataTable({
    "bProcessing": true,
    "pagination":true,
    scrollX: true,
    "sAjaxSource": "<?php echo page_url;?>Task/completeddf",
    "aoColumns": [

    { mData: 'sr_no' } ,
    { mData: 'df_no' } ,
    { mData: 'dfupload' } ,
    { mData: 'df_release_date' } ,
    { mData: 'department' } ,
    { mData: 'membername' } ,
    { mData: 'taskname' } ,
    { mData: 'tasktat' } ,
    { mData: 'addedon' },
    { mData: 'taskdelay' } ,
    { mData: 'status' } ,
    { mData: 'remarks' } ,
    { mData: 'addedby' }
    ],
    "initComplete": function(settings, json) {

    getcolors()
    }

    });


    $('#example').on('draw.dt', function() {
    // do action here

    getcolors();
    });

    $('#example').on('search.dt', function() {

    getcolors();
    });

    <?php 
    if($_SESSION['logged_in']['adminuser']==1 || $_SESSION['logged_in']['adminuser']==2){ ?>
    $('#example4').dataTable({
    "bProcessing": true,
    "pagination":true,
    "sAjaxSource": "<?php echo page_url;?>Task/pendingtoassigndf",
    "aoColumns": [

    { mData: 'sr_no' } ,
    { mData: 'df_no' } ,
    { mData: 'df_release_date' } ,
    { mData: 'downloaddf' },
    { mData: 'assigment' }


    ]

    });
    <?php } ?>
    });


    // function getcolors() {
    // $("#example3 tr").each(function() {
    // var currentRow = $(this);
    // var col1_value = currentRow.find("td:eq(10)").text();
    //  if (col1_value == 'On Time') {
    //                     currentRow.addClass("c11");
    //                 } else if (col1_value == 'Delayed') {
    //                     currentRow.addClass("c22")
    //                 } else if (col1_value == 'Early') {
    //                     currentRow.addClass("c33")
    //                 } 



    // });
    // }
    </script>

    <script>
    function openCity(evt, cityName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += " active";
    }
    document.getElementById("defaultOpen").click();
    </script>

    <script>

    //setInterval('updateTimer()', 1000);


    function updateTimer1() {
    future = Date.parse("April 22, 2024 11:30:00");
    now = new Date();
    diff =  now-future;

    days = Math.floor(diff / (1000 * 60 * 60 * 24));
    hours = Math.floor(diff / (1000 * 60 * 60));
    mins = Math.floor(diff / (1000 * 60));
    secs = Math.floor(diff / 1000);

    d = days;
    h = hours - days * 24;
    m = mins - hours * 60;
    s = secs - mins * 60;

    document.getElementById("timer1")
    .innerHTML =
    '<div>' + d + '<span> Days </span>' + m + '<span> min</span></div>' +
    // '<div>' + h + '</div>' +
    // '<div>' + m + '<span> min</span></div>';
    '<div>' + s + ' sec</div>';


    }
    //setInterval('updateTimer1()', 1000);



    function show_update_popup()
    {
    if($('input[class=update_task]:checked'))
    {
    $("#myModal").modal('show');
    }else
    {

    }

    }
    </script>


    <script language="javascript" type="text/javascript">
    $(document).ready(function() {
    unassignednotificationcount();
    ongoingtaskcountnotification();
    overduetaskcountnotification();
    completeddfnotificationcount();
    // setInterval(tiggernotification, 100000);
    //setInterval(getnotificationdata, 10000);
    setInterval(unassignednotificationcount, 30000);
    setInterval(ongoingtaskcountnotification, 30000);
    setInterval(overduetaskcountnotification, 30000);
    setInterval(completeddfnotificationcount, 30000);
    // toastr.options.onCloseClick= function() { alert("You clicked close button!"); };
    // tiggernotification();
    $("#savedata").click(function() {
    var uploaddf = $("#uploaddf").val();
    if(uploaddf=='')
    {
    $("#error_uploaddf").html('Required!');
    $("#uploaddf").css("border", "1px solid red");
    }

    var dfno = $("#dfno").val();
    if(dfno=='')
    {
    $("#error_dfno").html('Required!');
    $("#dfno").css("border", "1px solid red");
    }

    if(uploaddf=='' || dfno=='')
    {

    return false;
    }

    });

    });

    function taskupdationvalidation(){
    var taskstatus = $("#taskstatus").val();
    if(taskstatus=='')
    {
    $("#error_taskstatus").html('Required!');
    $("#taskstatus").css("border", "1px solid red");
    }else{

    if(taskstatus==0){
    $("#error_taskremarks").html('Required!');
    $("#taskremarks").css("border", "1px solid red");
    return false;
    }
    }


    if(taskstatus=='')
    {

    return false;
    }
    }
    </script> 

    <script type="text/javascript">
    function updatedfrelease(id,poid){

    $("#dfmodal").modal('show');
    $("#dfrecordid").val(id);
    $("#poid").val(poid);
    }
    </script>

    <script type="text/javascript">
    function updateyourprogressremarks(id,sortorder,dfno,taskid){

    $("#updateprogress").modal('show');
    $("#taskkiid").val(id);
    $("#mastertaskid").val(taskid);
    $("#shortorder").val(sortorder);
    $("#progressdfno").val(dfno);


    }
    </script>
    <script type="text/javascript">
    function reassigntasktoanotheruser(id, departmentid,assigneduser){
    $("#updateprogress1").modal('show');
    $("#selectedtasktoreassign").val(id);
    //$("#currenctlyassigned").html(currenctlyassigned);
    var department = departmentid;
    $.ajax({
    type:"post",
    url:"<?php echo page_url;?>Task/fetchreassignuserlist",
    data:"department="+department+"&assigneduser="+assigneduser,
    success:function(data){
    $("#showuserstoreassign").html(data);
    }
    });

    $.ajax({
    type:"post",
    url:"<?php echo page_url;?>Task/currentlyassignedto",
    data:"id="+id,
    success:function(taskinfo){
    $("#currenctlyassigned").html(taskinfo);
    }
    });


    }
    </script>
    <script type="text/javascript">
    function assigntasktousers(id, departmentid){
    $("#assigntaskwindow").modal('show');
    var dfid = id;
    var department = departmentid;
    //alert(department);
    $.ajax({
    type:"post",
    url:"<?php echo page_url;?>Task/featch_dynamic_tasks",
    data:"dfid="+dfid+"&department="+department,
    success:function(data){
    $("#showdynamictask").html(data);
    }
    });

    }
    </script>
    <?php 
    $department = $this->session->userdata['logged_in']['department_id'];
    $user_id =$this->session->userdata['logged_in']['user_id'];
    ?>
    <script src="
    https://cdn.jsdelivr.net/npm/bootoast@1.1.4/dist/bootoast.min.js
    "></script>
    <script type="text/javascript">
    function tiggernotification(){
    var departmentid = '<?php echo $department;?>';
    var userid = '<?php echo $user_id;?>';
    $.ajax({
    type:"post",
    url:"<?php echo page_url;?>Task/checknotification",
    data:"departmentid="+departmentid+"&userid="+userid,
    dataType: 'json',
    success:function(data){
    //alert(data);
    jQuery.each(data, function(i, item) {
    //alert(data[i].notification);
    const bootoast = window.Bootoast;
    bootoast.toast({
    "message": data[i].notification,
    "type": "danger",
    "position": "top-right",
    "icon": "",
    "animationDuration": "300",
    "dismissable": true
    });
    });
    }
    });



    }

    // function getnotificationdata(){
    //      var departmentid = '<?php echo $department;?>';
    //     var userid = '<?php echo $user_id;?>';
    //      $.ajax({
    //         type:"post",
    //         url:"<?php echo page_url;?>Task/notificationuserwise",
    //         data:"departmentid="+departmentid+"&userid="+userid,
    //         success:function(data){
    //             //alert(data);
    //            $("#showlivenotifications").html(data);

    //         }
    //     });
    // }

    function unassignednotificationcount(){
    $.ajax({
    type:"post",
    url:"<?php echo page_url;?>Task/unassignednotificationcount",
    success:function(data){

    $("#unassignednotificationcount").html(data);

    }
    }); 
    }

    function ongoingtaskcountnotification(){
    $.ajax({
    type:"post",
    url:"<?php echo page_url;?>Task/ongoingtaskcountnotification",
    success:function(data){

    $("#ongoingtaskcountnotification").html(data);

    }
    }); 
    }

    function overduetaskcountnotification(){
    $.ajax({
    type:"post",
    url:"<?php echo page_url;?>Task/overduetaskcountnotification",
    success:function(data){

    $("#overduetaskcountnotification").html(data);

    }
    });  
    }
    function completeddfnotificationcount(){
    $.ajax({
    type:"post",
    url:"<?php echo page_url;?>Task/completeddfnotificationcount",
    success:function(data){
    // alert(data);
    $("#completeddfnotificationcount").html(data);

    }
    });  
    }
    <?php 
    $activeusertype =  $_SESSION['logged_in']['adminuser'];
    ?>
    function filter_ongoing_task()
    {
    var filter=$("#task_date_filter").val();
    var departmentfilter=$("#task_department_filter").val();
    var usefilter=$("#task_user_filter").val();
    var dfno = $("#task_dfno_filter").val();
    var activeusertype = '<?php echo $activeusertype;?>';
    $('#example').DataTable().ajax.url("<?php echo page_url;?>Task/ongoingtasklist/"+filter+"/"+departmentfilter+"/"+usefilter+"/"+dfno+"/"+activeusertype).load();
    //$('#example').DataTable().ajax.reload();
    }

    function filter_overdue_task()
    {
    var filter=$("#task_number_filter_overdue").val();
    var fiterindays = $("#noofdaysdue").val();
    var departmentfilter=$("#task_department_filter_overdue").val();
    var usefilter=$("#task_user_filter_overdue").val();
    var dfno = $("#task_dfno_filter_overdue").val();
    $('#example2').DataTable().ajax.url("<?php echo page_url;?>Task/outdatedtask/"+filter+"/"+departmentfilter+"/"+usefilter+"/"+dfno+"/"+fiterindays).load();
    //$('#example').DataTable().ajax.reload();
    }

    function filter_completed_task()
    {
    var departmentfilter=$("#task_department_filter_completed").val();
    var usefilter=$("#task_user_filter_completed").val();
    var dfno = $("#task_dfno_filter_completed").val();
    $('#example3').DataTable().ajax.url("<?php echo page_url;?>Task/completeddf/"+departmentfilter+"/"+usefilter+"/"+dfno).load(getcolors);
    //$('#example').DataTable().ajax.reload();
    }
    function getcolors() {
    $("#example3 tr").each(function() {
    var currentRow = $(this);
    var col1_value = currentRow.find("td:eq(10)").text();
    // alert(col1_value);

    if (col1_value == 'On Time') {
    currentRow.addClass("c11");
    } else if (col1_value == 'Delayed') {
    currentRow.addClass("c22")
    } else if (col1_value == 'Early') {
    currentRow.addClass("c33")
    } 



    });
    }

    </script>


    <div id="assigntaskwindow" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

    <form id="assigntasktoteammember" method="post" action="<?php echo page_url;?>Task/assigntasktoteammember"  enctype="multipart/form-data">
    <div id="pageloader2">
    <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
    </div>
    <div class="modal-dialog modal-lg">
    <div class="modal-content">
    <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">Assign Task to Your Team Members</h4>
    </div>

    <div class="modal-body">
    
    <div class="row">
    <div id="showdynamictask"></div>
    </div>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
    <input type="submit" id="assigntaskdata" class="btn btn-info" value="Submit"> 

    </div>

    </div>

    </form>

    </div>


    <script>
    $(document).ready(function(){
    $("#updateprogressform").on("submit", function(){
    $("#pageloader1").fadeIn();
    });//submit
    });//document ready
    </script>
    <script>
    $(document).ready(function(){
    $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
    });//submit
    });//document ready
    </script>
    <script>
    $(document).ready(function(){
    $("#assigntasktoteammember").on("submit", function(){
    $("#pageloader2").fadeIn();
    });//submit
    });//document ready

    function applysameUser()
    {
        if($('#appall').is(':checked'))
        {
            var d=$(".firstClass").val();
            if(d!='')
            {  $(".allassignUser").val(d);
            }else
            {
               $("#appall").prop('checked', false);
            }
           
        }else
        {
            //alert('bye');
        }

    }
    </script>

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
</body>
</html>