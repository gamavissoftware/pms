<?php
$CI = &get_instance();
$CI->load->model('Fms_model');
$UI = &get_instance();
$UI->load->model('Store_model');
$DI = &get_instance();
$DI->load->model('Dashboard_model');
$EI = &get_instance();
$EI->load->model('Salescrm_model');
$getAllLeadStages = $DI->Dashboard_model->getAllLeadStages();
$user_id = $_SESSION['logged_in']['user_id'];
$user_role = $this->session->userdata['logged_in']['role'];
$first_name = $this->session->userdata['logged_in']['user_name'];
$last_name = $this->session->userdata['logged_in']['last_name'];
$profile_image = $this->session->userdata['logged_in']['profile_image'];
$q = $this->db->select('profile_image')->from('system_users')->where('user_id', $user_id)->get();
if ($q->num_rows() > 0) {
  foreach ($q->result() as $row);
  $profilepic=$row->profile_image;
} else {
  $profilepic="";
}

$app=array();
$row112=$this->db->select('approval_id')->from('approvals_permission')->where('user_id',$_SESSION['logged_in']['user_id'])->get();
if($row112->num_rows()>0)
{
foreach($row112->result() as $rowss)
{
$app[]=$rowss->approval_id;
}

}else
{}

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="<?php echo copyright; ?>">
  <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
  <title><?php echo sitetitle; ?> Dashboard Copy</title>
  <!--Morris Chart CSS -->
  <link rel="stylesheet" href="<?php echo assets_url; ?>plugins/morris/morris.css">
  <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo assets_url; ?>css/dashboardcss.css" rel="stylesheet" type="text/css" />
  <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->

  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

  <!--[if lt IE 9]>

        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>

        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>

        <![endif]-->



  <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
  <script type="text/javascript">
    window.onload = function() {

      location.href = document.getElementById("selectbox").value;

    }
  </script>

  <style>
   .list-group-item {
    /* border: 1px solid #ebeff2; */
    /* border-left: none; */
    /* border-right: none; */
    background: whitesmoke !important;
    margin-top: 5px !important;
    border: none !important;
    padding: 6px !important;
}

    .user-desc span {
      color: black;
    }

    .again__circle1 {
    background: #fff;
    /* border-left: 2px solid; */
    color: #666;
    text-align: center;
    font-size: 20px;
    margin: auto;
    /* border-image: linear-gradient(#00DBDE, #FC00FF) 1; */
    padding: 2px;
    font-weight: 700;
    border: 2px solid #ebebeb;
    border-radius: 5px;
    width: 40px;
    height: 40px;
    box-shadow: 1px 1px 10px #dbd7d7;
    background: whitesmoke;
    animation: pulse 2s infinite;
}

.boxstyling {
    padding: 10px 10px 10px 22px;
    background: #fff;
    border-radius: 7px;
    box-shadow: 0 1px 7px 1px rgb(90 90 90 / 5%);
    border: 1px solid #dce1ef;
}

@-webkit-keyframes pulse {
  0% {
    -webkit-box-shadow: 0 0 0 0 rgba(204,169,44, 0.4);
  }
  70% {
      -webkit-box-shadow: 0 0 0 10px rgba(204,169,44, 0);
  }
  100% {
      -webkit-box-shadow: 0 0 0 0 rgba(204,169,44, 0);
  }
}
@keyframes pulse {
  0% {
    -moz-box-shadow: 0 0 0 0 rgba(204,169,44, 0.4);
    box-shadow: 0 0 0 0 rgba(204,169,44, 0.4);
  }
  70% {
      -moz-box-shadow: 0 0 0 10px rgba(204,169,44, 0);
      box-shadow: 0 0 0 10px rgba(204,169,44, 0);
  }
  100% {
      -moz-box-shadow: 0 0 0 0 rgba(204,169,44, 0);
      box-shadow: 0 0 0 0 rgba(204,169,44, 0);
  }
}

/* .again__circle1::before {
    content: '';
    position: relative;
    display: block;
    width: 150%;
    height: 150%;
    box-sizing: border-box;
    margin-left: -25%;
    margin-top: -25%;
    background-color: #1e4823;
    animation: pulse-ring 1.25s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
}
.again__circle1::after {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    display: block;
    background-color: white;
    box-shadow: 0 0 8px rgba(0,0,0,.3);
}

@keyframes pulse-ring {
  0% {
    transform: scale(.33);
  }
  80%, 100% {
    opacity: 0;
  }
}

@keyframes pulse-dot {
  0% {
    transform: scale(.8);
  }
  50% {
    transform: scale(1);
  }
  100% {
    transform: scale(.8);
  }
}  */
  </style>

</head>

<body>



  <div class="contentarea">

    <!-- Navigation Bar-->

    <?php $this->load->view('common/nav-menu'); ?>

    <!-- End Navigation Bar-->

    <div class="wrapper">
      <div class="container-fluid" style="background-color:white; min-height:500px !important;">

        <div class="row">
          <div class="col-sm-9">
            <div class="dashagain">
              <div class="row row-flex" id="shortcut">
              	<?php
        /*Daily Task for Admin*/
                // $submodules = "'1', '14', '15', '19', '20', '21', '22', '23', '24', '25', '26'";
        if($_SESSION['logged_in']['department_id']==1)
        {
                $submodules = "'1', '14', '15','75','76','87','114','127','130','62','134','147','23','24','2','3','39','4','121','119','120','152'";
              $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
              if ($as > 0) {
              ?>
                <div class="col-sm-4">
                  <div class="dash__box">
                    <h3>Daily Task</h3>
                    <hr>
                    <div class="row">
 <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(152);
                          if ($a > 0) { ?>

                        <a href="<?php echo page_url; ?>Delegation/delegation_task">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle" id="delegateddata"><i class="fa fa-file"></i></div>
                          <p>Delegation Form</p>
                        </div>
                      </a>
                    <?php }?>

                      <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(19);
                          if ($a > 0) { ?>
                      <a href="<?php echo page_url; ?>Delegation/delegation_dashboard">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle1" id="delegateddata">
                            <?php
                            $q = $this->db->select('id')->from('delegation_task')->where('yourname', $user_id)->where('task_status', '0')->get();
                            $report = count($q->result());
                            echo $report;
                            ?>
                          </div>
                         
                          <p>Delegated Task</p>
                        </div>
                      </a>
                    <?php } ?>


                     <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(26);
                          if ($a > 0) { ?>
                      <a href="<?php echo page_url; ?>Delegation/delegated_task">
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="again__circle" id="delegateddata">
                            <?php
                            $q = $this->db->select('id')->from('delegation_task')->where('task_status', '0')->where('delegate_to', $user_id)->get();
                            $report = count($q->result());
                            echo $report;
                            ?>
                          </div>
                          <p>Delegated Task to You</p>
                        </div>
                      </a>
                      <?php } ?> 


                      
                     <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(20);
                          if ($a > 0) { ?>
                      <a href="<?php echo page_url; ?>Daily_reporting/reporting_dashboard">
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="again__circle" id="dailyworkreport">0</div>
                          <p>View Daily Report</p>
                        </div>
                      </a>
                      <?php } ?>
                    
            
                      <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(22);
                          if ($a > 0) { ?>

                      <a href="<?php echo page_url; ?>Checklist/checklist_yesterday_report">
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="again__circle" id="dailyworkreport">0</div>
                          <p>YESTERDAY CHECKLIST REPORT</p>
                        </div>
                      </a>
                       <?php } ?>

                        
                       <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(23);
                          if ($a > 0) { ?>
                      <a href="<?php echo page_url; ?>Mom/dashboard/1/<?php echo $user_id; ?>" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle" id="todaysmom">
                            <?php
                            $q = $this->db->select('id')->from('mom')->where('mom_date', date('Y-m-d'))->get();
                            echo count($q->result());
                            ?>
                          </div>
                          <p>Todays MOM</p>
                        </div>
                      </a>
                      <?php } ?>

                      <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(24);
                          if ($a > 0) { ?>
                      <a href="<?php echo page_url; ?>Mom/mom_assigned_to_you" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle" id="momassignedtoyou">
                            <?php
                            $q = $this->db->select('id')->from('mom_ponits_view')->where('responsible_person', $user_id)->where('workstatus', NULL)->get();
                            echo count($q->result());
                            ?>
                          </div>
                          <p>MOM Assigned to You</p>
                        </div>
                      </a>
                       <?php } ?>

                         <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(1);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->todays_followup_count(); ?>
                      <a href="<?php echo page_url; ?>Leads/todaysfollowup" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle" id="momassignedtoyou">
                            <?php echo $b;?>
                          </div>
                          <p>Todays Followup</p>
                        </div>
                      </a>
                      <?php } ?>

                       <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(14);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->missed_followup_count(); ?>
                      <a href="<?php echo page_url; ?>Leads/missed_followup_up" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle" id="momassignedtoyou">
                            <?php echo $b;?>
                          </div>
                          <p>Missed Followup Leads</p>
                        </div>
                      </a>
                      <?php } ?>

                       <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(15);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->upcoming_followup_count(); ?>
                      <a href="<?php echo page_url; ?>Leads/upcomeingfollow" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle" id="momassignedtoyou">
                            <?php echo $b;?>
                          </div>
                          <p>Upcoming Followups</p>
                        </div>
                      </a>
                      <?php } ?>


                        <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(114);
                          if ($a > 0) {
                      ?>
                      <a href="<?php echo page_url; ?>Customer/agent_customers" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle"><i class="fa fa-user"></i>
                          </div>
                          <p>AGENT WISE CUSTOMER</p>
                        </div>
                      </a>
                      <?php } ?>

                          <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(75);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->visit_counts_today(); ?>
                      <a href="<?php echo page_url; ?>Leads/todays_visit/<?php echo date('Y-m-d');?>/<?php echo date('Y-m-d');?>/ALL/" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle">
                            <?php echo $b;?>
                          </div>
                          <p>Todays Visit<br/> (New Customer)</p>
                        </div>
                      </a>
                      <?php } ?>

                          <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(109);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->visit_counts_old_today(); ?>
                      <a href="<?php echo page_url; ?>Leads/todays_visit_old_customer/<?php echo date('Y-m-d');?>/<?php echo date('Y-m-d');?>/ALL/" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle">
                            <?php echo $b;?>
                          </div>
                          <p>Todays Visit<br/> (Old Customer)</p>
                        </div>
                      </a>
                      <?php } ?>


                       <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(77);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->visit_schedule_counts_today(); ?>
                      <a href="<?php echo page_url; ?>Leads/todays_visit_scheduled/<?php echo date('Y-m-d');?>/<?php echo date('Y-m-d');?>/ALL/" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle">
                            <?php echo $b;?>
                          </div>
                          <p>Todays Scheduled Visits</p>
                        </div>
                      </a>
                      <?php } ?>


                        <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(78);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->visit_schedule_counts_today_user_wise($user_id); ?>
                      <a href="<?php echo page_url; ?>Leads/todays_visit_userwise_scheduled/<?php echo date('Y-m-d');?>/<?php echo date('Y-m-d');?>/<?php echo $user_id;?>/" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle">
                            <?php echo $b;?>
                          </div>
                          <p>Todays Scheduled Visits</p>
                        </div>
                      </a>
                      <?php } ?>

                       <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(170);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->yourtotaldistributor($user_id);?>
                      <a href="<?php echo page_url; ?>Distributor/distributor_list" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle">
                            <?php echo $b;?>
                          </div>
                          <p>Your Distributor</p>
                        </div>
                      </a>
                      <?php } ?>

                    </div>



                    <div class="row">
                       <?php
                      $submodules = "'87'";
                    $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
                    if ($as > 0) {

                          $query = $this->db->select('id')
                          ->from('inventory_batch_no')
                          ->where('test_report', 0)
                          ->get();
                                                   
                    ?>
                <a href="<?php echo page_url; ?>Inventory/pending_test_report" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle">
                            <?php echo $query->num_rows();?>
                          </div>
                          <p>Test Reports to be Uploaded</p>
                        </div>
                      </a>

              <?php } ?>


               <?php
                      $submodules = "'127'";
                    $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
                    if ($as > 0) {

                        
                                                   
                    ?>
                <a href="<?php echo page_url; ?>Customer/customer_not_taken_product" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle">
                            <i class="fa fa-users"></i>
                          </div>
                          <p>Customer Followup List</p>
                        </div>
                      </a>

              <?php } ?>



                <?php
                      $submodules = "'130'";
                    $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
                    if ($as > 0) {
                   ?>
                <a href="<?php echo page_url; ?>Sales_stats_reporting/customer_qty_comparision" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle">
                            <i class="fa fa-users"></i>
                          </div>
                          <p>Customer Wise Sale Comparision</p>
                        </div>
                      </a>

              <?php } ?>



                <?php
                      $submodules = "'134'";
                    $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
                    if ($as > 0) {

                      ?>
                <a href="<?php echo page_url; ?>FMS/stock_filter/<?php echo date('Y-m-d');?>/<?php echo date('Y-m-d');?>/1/ALL" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle">
                            <i class="fa fa-list"></i>
                          </div>
                          <p>Inventory Opening/Closing Report</p>
                        </div>
                      </a>

              <?php } ?>



               <?php
                      $submodules = "'135'";
                    $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
                    if ($as > 0) {
                    ?>
                <a href="<?php echo page_url; ?>FMS/sales_inventory_report/1">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle">
                            <i class="fa fa-list"></i>
                          </div>
                          <p>Sales Inventory Report</p>
                        </div>
                      </a>

              <?php } ?>

               <?php
                      $submodules = "'136'";
                    $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
                    if ($as > 0) {

                        $month = date('m');
                                                   
                    ?>
                <a href="<?php echo page_url; ?>FMS/stockmatching/<?php echo $month;?>">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle">
                            <i class="fa fa-list"></i>
                          </div>
                          <p>Manual Stock Entry for Reconciliation</p>
                        </div>
                      </a>

              <?php } ?>


                <?php
                      $submodules = "'137'";
                    $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
                    if ($as > 0) {

                      
                                                   
                    ?>
                <a href="<?php echo page_url; ?>FMS/stock_reco">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle">
                            <i class="fa fa-file"></i>
                          </div>
                          <p>Reconciliation Report</p>
                        </div>
                      </a>

              <?php } ?>


                <?php
                      $submodules = "'140'";
                    $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
                    if ($as > 0) {

                       $d=$this->Dashboard_model->min_stock_alert();
                      
                                                   
                    ?>
                <a href="<?php echo page_url; ?>FMS/min_stock_alert">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle">
                           <?php echo $d;?>
                          </div>
                          <p>Min Stock Alert</p>
                        </div>
                      </a>

              <?php } ?>



               <?php
                      $submodules = "'144'";
                    $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
                    if ($as > 0) {

                              
                                                   
                    ?>
                <a href="<?php echo page_url; ?>Customer/ledger_report">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle">
                           <i class="fa fa-file"></i>
                          </div>
                          <p>Customer Ledger</p>
                        </div>
                      </a>

              <?php } ?>

               <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(147);
                          if ($a > 0) { ?>

                      <a href="<?php echo page_url; ?>Checklist/view_your_checklist">
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="again__circle" id="dailyworkreport">
                            
                            <?php 
                              $date = date('Y-m-d');
                              $this->db->select('a.task_id')->from('compliance_task_report a')->join('compliance_set_date p','a.task_id=p.task_id')->group_by('p.task_id');
                               $this->db->where('a.user_id',$user_id);
                               $this->db->where('a.status','1');
                               $query = $this->db->get();  
                               echo count($query->result());
                            ?>



                          </div>
                          <p>Your <br>Todays<br> Checklist </p>
                        </div>
                      </a>
                       <?php } ?>


                     




                    </div>
                  </div>
                  <?php } } ?>

                    <?php
                $submodules = "'2', '3', '4', '39','79','80','119','120','121','55','141','142','143'";
              $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
              if ($as > 0) {
              ?>

              <div class="dash__box" style="display:none">
                    <h3>Quotations</h3>
                    <hr>
                    <div class="row">
                      <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(2);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->discount_approval_count(); ?>
                      <a href="<?php echo page_url; ?>Customer/discount_approval">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>Pending for Approval</p>
                        </div>
                      </a>
                       <?php } ?>

                      <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(3);
                              if ($a > 0) {
                      $b = $DI->Dashboard_model->quotations_expiring_today_count(); ?>
                      <a href="<?php echo page_url; ?>Customer/quotations_expiring_today">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>Quotations Expiring Today(Running)</p>
                        </div>
                      </a>
                      <?php } 
                      ?>

                      <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(39);
                              if ($a > 0) {
                      $b = $DI->Dashboard_model->lead_quotations_expiring_today_count(); ?>
                      <a href="<?php echo page_url; ?>Leads/lead_quotations_expiring_today">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>Quotations Expiring Today (New)</p>
                        </div>
                      </a>
                      <?php } 
                      ?>

                      <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(4);
                              if ($a > 0) {
                      $b = $DI->Dashboard_model->rejected_quotations_count(); ?>
                      <a href="<?php echo page_url; ?>Customer/rejected_quotations">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>Rejected Quotations</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                      <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(121);
                              if ($a > 0) {
                     ?>
                      <a href="<?php echo page_url; ?>Customer/quotation">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-file"></i></div>
                          <p>Create Old Customer Quotations</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                        <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(119);
                              if ($a > 0) {
                      $b = $DI->Dashboard_model->old_quote_for_orders_admin(); ?>
                      <a href="<?php echo page_url; ?>Customer/quotation_dashboard">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>Old Customer Quotations<br/>Pending for Order</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                     <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(120);
                              if ($a > 0) {
                      $b = $DI->Dashboard_model->old_quote_for_orders_user(); ?>
                      <a href="<?php echo page_url; ?>Customer/quotation_dashboard">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>Old Customer Quotations <br/>Pending for Order</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                    </div>



                      <?php
                $submodules = "'79','117','55','139','141','142','143'";
              $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
              if ($as > 0) {
              ?>
           <h3>Purchases</h3><hr>
          <div class="row">
                  <?php
                  $a = $DI->Dashboard_model->checkForDashboardSubmodules(79);
                  if ($a > 0) { ?>
                  <a href="<?php echo page_url; ?>Inventory">
                  <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                  <div class="again__circle" style="padding: 0px !important;"><img src="<?php echo assets_url;?>images/barrel.png" width="32px"></div>
                  <p>Add Purchases</p>
                  </div>
                  </a>
                  <?php  } ?>


                  <?php
                  $a = $DI->Dashboard_model->checkForDashboardSubmodules(55);
                  if ($a > 0) { ?>
                  <a href="<?php echo page_url; ?>Inventory/this_month_purchases/<?php echo date('Y-m-01')?>/<?php echo date('Y-m-t');?>/ALL/ALL">
                  <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                  <div class="again__circle" style="padding: 0px !important;"><i class="fa fa-file"></i></div>
                  <p>This Month Purchases</p>
                  </div>
                  </a>
                  <?php } ?>

                    <?php
                  $a = $DI->Dashboard_model->checkForDashboardSubmodules(141);
                  if ($a > 0) { ?>
                  <a href="<?php echo page_url; ?>Inventory/inventory_summary/<?php echo date('Y-m-01')?>/<?php echo date('Y-m-t');?>/ALL/ALL/ALL/">
                  <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                  <div class="again__circle" style="padding: 0px !important;"><i class="fa fa-file"></i></div>
                  <p>This Month Purchases Summary</p>
                  </div>
                  </a>
                  <?php } ?>

                  <?php
                  $a = $DI->Dashboard_model->checkForDashboardSubmodules(142);
                  if ($a > 0) { ?>
                  <a href="<?php echo page_url; ?>Inventory/inventory_summary_with_dencity_report/<?php echo date('Y-m-01')?>/<?php echo date('Y-m-t');?>/ALL/ALL/ALL/">
                  <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                  <div class="again__circle" style="padding: 0px !important;"><i class="fa fa-file"></i></div>
                  <p>This Month Purchases Summary with Density</p>
                  </div>
                  </a>
                  <?php } ?>

                   <?php
                  $a = $DI->Dashboard_model->checkForDashboardSubmodules(143);
                  if ($a > 0) { ?>
                  <a href="<?php echo page_url; ?>Inventory/empty_barrel_report/ALL/ALL/ALL/ALL/ALL/1">
                  <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                  <div class="again__circle" style="padding: 0px !important;"><i class="fa fa-file"></i></div>
                  <p>Empty Barrel<br>Stock Report</p>
                  </div>
                  </a>
                  <?php } ?>



                   <?php
                  $a = $DI->Dashboard_model->checkForDashboardSubmodules(139);
                  if ($a > 0) {
                   $b = $DI->Dashboard_model->transporter_pending_payment_incoming();
                   ?>
                  <a href="<?php echo page_url; ?>Billing/transporter_pending_payment_incoming/<?php echo date('Y-m-01')?>/<?php echo date('Y-m-t');?>/ALL/ALL">
                  <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                  <div class="again__circle" style="padding: 0px !important;"><?php echo $b;?></div>
                  <p>Transporter Pending Payment (Incoming)</p>
                  </div>
                  </a>
                  <?php } ?>


                    <?php
                  $a = $DI->Dashboard_model->checkForDashboardSubmodules(117);
                  if ($a > 0) {
                   $b = $DI->Dashboard_model->transporter_pending_payment_outgoing();
                   ?>
                  <a href="<?php echo page_url; ?>Billing/transporter_pending_payment_outgoing/<?php echo date('Y-m-01')?>/<?php echo date('Y-m-t');?>/ALL">
                  <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                  <div class="again__circle" style="padding: 0px !important;"><?php echo $b;?></div>
                  <p>Transporter Pending Payment (Outgoing)</p>
                  </div>
                  </a>
                  <?php } ?>

          </div>
        <?php } ?>



                <h3 style="display: none;">Approvals</h3><hr>
                <div class="row">


                   <?php if(in_array(2,$app)){ 
                     $b = $DI->Dashboard_model->quote_discount_count();
                     ?>
                    <a href="<?php echo page_url; ?>Leads/quotation_discount_approval" style="display: none;">
                  <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                  <div class="again__circle" style="padding: 0px !important;"><?php echo $b;?></div>
                  <p>Quotation Discount</p>
                  </div>
                  </a>

                <?php 
                 $b = $DI->Dashboard_model->order_discount_count();
                 ?>

                   <a href="<?php echo page_url; ?>Leads/order_discount_approval" style="display: none;">
                  <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                  <div class="again__circle" style="padding: 0px !important;"><?php echo $b;?></div>
                  <p>Order Discount</p>
                  </div>
                  </a>

                      <?php } ?>

                        <?php if(in_array(6,$app)){ 
                          $b = $DI->Dashboard_model->orders_on_hold_count_user();
                            ?>
                        <a href="<?php echo page_url; ?>Leads/order_on_hold" style="display: none;">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                        <div class="again__circle"><?php echo $b;?></div>
                        <p>Orders On Hold</p>
                        </div>
                        </a>
 
                    <?php } ?>
                


                 <!--   <a href="<?php echo page_url; ?>Leads/common_approval">
                  <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                  <span class="btn btn-danger" style="animation: pulse 2s infinite;">Approvals Required&nbsp;<span class="badge" id="admin_approval" style="font-size:21px; font-weight:bold;"></span></span>
                  </div>
                  </a>  -->

                   <!--  <a href="<?php echo page_url; ?>Leads/triggers">
                  <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                  <span class="btn btn-danger">Trigger Reminders</span>
                  </div>
                  </a> -->


                  <?php 
                  if($_SESSION['logged_in']['department_id']==6)
                  {
                  ?>
              <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                <table class="table table-bordered">
                <thead>
               
                </thead>
                <tbody>

                  <?php 
                    $previous_start_date=date('Y-m-01',strtotime('-1 Month'));
                    $previous_end_date=date('Y-m-t',strtotime($previous_start_date));
                    $start_date=date('Y-m-01');
                    $end_date=date('Y-m-t',strtotime($start_date));
                    // echo $previous_start_date."<br/>".$previous_end_date."<br/>".$start_date."<br/>".$end_date; exit;

                     $previous_month_profit=$EI->Salescrm_model->get_profit_numbers($previous_start_date,$previous_end_date,$_SESSION['logged_in']['user_id']);
                      $previous_month_target=$EI->Salescrm_model->get_target($previous_start_date,$previous_end_date,$_SESSION['logged_in']['user_id']);

                       if($previous_month_profit>=$previous_month_target)
                        {
                            $balance_left=0;
                        }else{

                        $balance_left=$previous_month_profit-$previous_month_target;
                        }

                         $this_month_target=$EI->Salescrm_model->get_target($start_date,$end_date,$_SESSION['logged_in']['user_id']);
                         $this_month_profit=$EI->Salescrm_model->get_profit_numbers($start_date,$end_date,$_SESSION['logged_in']['user_id']);

                         if($this_month_target>0)
                         {
                         $target_left=$this_month_target-$this_month_profit;
                         $achieved_per=$this_month_profit*100/$this_month_target;

                         $target_left_per=$target_left*100/$this_month_target;
                        }else
                        {
                          $target_left=0;
                          $achieved_per=0;
                          $target_left_per=0;
                        }



                  ?>

                   <tr>
                <th style="width:100%;text-align:center;color:red;" colspan="2">Your Target Summary for <?php echo date('F');?></th>
                
                </tr>

                <tr>
                <th style="width:50%">Previous Balance</th>
                <th style="width:50%;font-weight:bold;color:black;font-size:15px;"><?php echo $balance_left;?></th>
               
                </tr>
                 <tr>
                <th style="width:50%"><?php echo date('F');?> Target</th>
                <th style="width:50%;font-weight:bold;color:black;font-size:15px;"><?php echo $this_month_target;?></th>
               
                </tr>
                 <tr>
                <th style="width:50%">Target Achieved</th>
                <th style="width:50%;font-weight:bold;color:black;font-size:15px;"><div class="col-md-6"><?php echo $this_month_profit;?></div><div class="col-md-4"></div><div class="col-md-2" style="color:red;"><?php echo $achieved_per;?>%</div></th>
               
                </tr>

                 <tr>
                <th style="width:50%">Target Pending</th>
                <th style="width:50%;font-weight:bold;color:black;font-size:15px;"><div class="col-md-6"><?php echo $target_left;?></div><div class="col-md-4"></div><div class="col-md-2" style="color:red;"><?php echo $target_left_per;?>%</div></th>
               
                </tr>
                

               
                </tbody>
                </table>
                </div>

               <?php } ?>



                </div>

                 
                  </div>  
    </div>
            <?php } ?>
              
<!--Daily Task for Admin end here---->

<!-- USER DAILY TASK START HERE-->
<?php
                if($_SESSION['logged_in']['department_id']!=1)
        {
        /*Daily Task for Users*/
               $submodules = "'16','17', '18', '27', '28', '29', '30', '31', '32', '33', '34','87','113','132','147','152'";
              //$submodules = "'16','17', '18'";
              $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
              if ($as > 0) {
              ?>
                <div class="col-sm-4">
                  <div class="dash__box">
                    <h3>Daily Task<span class="btn btn-xs" style="background-color:#2986CE; color:#fff">Take Action</span></h3>
                    <hr>
                    <div class="row">
                       <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(152);
                          if ($a > 0) { ?>
                        <a href="<?php echo page_url; ?>Delegation/delegation_task">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle" id="delegateddata"><i class="fa fa-file"></i></div>
                          <p>DELEGATION FORM</p>
                        </div>
                      </a>
                    <?php }?>

                      <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(27);
                          if ($a > 0) { ?>
                      <a href="<?php echo page_url; ?>Delegation/delegation_dashboard">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle" id="delegateddata">
                            <?php
                            $q = $this->db->select('id')->from('delegation_task')->where('yourname', $user_id)->where('task_status', '0')->get();
                            $report = count($q->result());
                            echo $report;
                            ?>
                          </div>
                          <p>DELEGATED TASK</p>
                        </div>
                      </a>
                    <?php } ?>
                    <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(147);
                          if ($a > 0) { ?>

                      <a href="<?php echo page_url; ?>Checklist/view_your_checklist">
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="again__circle" id="dailyworkreport">
                            
                            <?php 
                              $date = date('Y-m-d');
                              $this->db->select('a.task_id')->from('compliance_task_report a')->join('compliance_set_date p','a.task_id=p.task_id')->group_by('p.task_id');
                               $this->db->where('a.user_id',$user_id);
                               $this->db->where('a.status','1');
                               $query = $this->db->get();  
                               echo count($query->result());
                            ?>
                          </div>
                          <p>YOUR <br>TODAYS<br> CHECKLIST </p>
                        </div>
                      </a>
                       <?php } ?>

                    <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(32);
                          if ($a > 0) { ?>
                      <a href="<?php echo page_url; ?>Mom/mom_assigned_to_you" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle" id="momassignedtoyou">
                            <?php
                            $q = $this->db->select('id')->from('mom_ponits_view')->where('responsible_person', $user_id)->where('workstatus', NULL)->get();
                            echo count($q->result());
                            ?>
                          </div>
                          <p>MOM ASSIGNED TO YOU </p>
                        </div>
                      </a>
                       <?php } ?>
                      <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(29);
                          if ($a > 0) { ?>
                      <!-- <a href="<?php echo page_url; ?>Daily_reporting/reporting_dashboard">
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="again__circle" id="dailyworkreport">0</div>
                          <p>TASK DUE</p>
                        </div>
                      </a> -->
                      <?php } ?>

                       <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(76);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->visit_counts_userwise_today($user_id); ?>
                      <a href="<?php echo page_url; ?>Leads/todays_visit_userwise/<?php echo date('Y-m-d');?>/<?php echo date('Y-m-d');?>/<?php echo $user_id;?>/" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle">
                            <?php echo $b;?>
                          </div>
                          <p>TODAY'S VISIT <br/>(NEW CUSTOMER)</p>
                        </div>
                      </a>
                      <?php } ?>

                        <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(110);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->visit_counts_userwise_old_today($user_id); ?>
                      <a href="<?php echo page_url; ?>Leads/todays_visit_userwise_for_old_customer/<?php echo date('Y-m-d');?>/<?php echo date('Y-m-d');?>/<?php echo $user_id;?>/" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle">
                            <?php echo $b;?>
                          </div>
                          <p>TODAY'S VISIT <br/>(OLD CUSTOMER)</p>
                        </div>
                      </a>
                      <?php } ?>

                      
                    </div>
                   
                    <div class="row">
                      <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(33);
                          if ($a > 0) { ?>
                      <a href="<?php echo page_url; ?>Mom/mom_assigned_to_you" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle" id="momassignedtoyou">
                            <i class="fa fa-wpforms"></i>
                          </div>
                          <p>ADD YOUR REPORT</p>
                        </div>
                      </a>
                      <?php } ?>
                      <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(34);
                          if ($a > 0) { ?>
                      <a href="<?php echo page_url; ?>Delegation/delegated_task">
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="again__circle" id="delegateddata">
                            <?php
                            $q = $this->db->select('id')->from('delegation_task')->where('task_status', '0')->where('delegate_to', $user_id)->get();
                            $report = count($q->result());
                            echo $report;
                            ?>
                          </div>
                          <p>DELEGATED TASK TO YOU</p>
                        </div>
                      </a>
                      <?php } ?>
                      
                       <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(16);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->todays_followup_user_count(); ?>
                      <a href="<?php echo page_url; ?>Leads/todaysfollowup_user" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle" id="momassignedtoyou">
                            <?php echo $b;?>
                          </div>
                          <p>TODAY'S FOLLOWUP</p>
                        </div>
                      </a>
                      <?php } ?>

                       <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(17);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->missed_followup_user_count(); ?>
                      <a href="<?php echo page_url; ?>Leads/missed_followup_up_user" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle" id="momassignedtoyou">
                            <?php echo $b;?>
                          </div>
                          <p>MISSED FOLLOWUP LEADS</p>
                        </div>
                      </a>
                      <?php } ?>

                       <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(18);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->upcoming_followup_user_count(); ?>
                      <a href="<?php echo page_url; ?>Leads/upcomeingfollow_user" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle" id="momassignedtoyou">
                            <?php echo $b;?>
                          </div>
                          <p>UPCOMING FOLLOWUPS</p>
                        </div>
                      </a>
                      <?php } ?>

                        <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(113);
                          if ($a > 0) {
                      ?>
                      <a href="<?php echo page_url; ?>Customer/agent_customers/<?php echo $_SESSION['logged_in']['user_id'];?>" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle"><i class="fa fa-user"></i>
                          </div>
                          <p>YOUR CUSTOMERS<BR/>&<BR/>DISTRIBUTORS</p>
                        </div>
                      </a>
                      <?php } ?>

                       <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(123);
                          if ($a > 0) {
                      ?>
                      <a href="<?php echo page_url; ?>Sales_stats_reporting/margin_sheet/<?php echo date('Y-m-01');?>/<?php echo date('Y-m-t');?>/<?php echo $_SESSION['logged_in']['user_id'];?>/1">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle"><i class="fa fa-money"></i>
                          </div>
                          <p>YOUR MARGIN SHEET</p>
                        </div>
                      </a>
                      <?php } ?>


                       <?php
                      $submodules = "'87'";
                    $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
                    if ($as > 0) {

                          $query = $this->db->select('id')
                          ->from('inventory_batch_no')
                          ->where('test_report', 0)
                          ->get();
                                                   
                    ?>
                <a href="<?php echo page_url; ?>Inventory/pending_test_report" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle">
                            <?php echo $query->num_rows();?>
                          </div>
                          <p>TEST REPORTS TO BE UPLOADED</p>
                        </div>
                      </a>

              <?php } ?>


                <?php
                      $submodules = "'132'";
                    $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
                    if ($as > 0) {

                        
                                                   
                    ?>
                <a href="<?php echo page_url; ?>Customer/customer_not_taken_product/<?php echo $_SESSION['logged_in']['user_id'];?>" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle">
                            <i class="fa fa-users"></i>
                          </div>
                          <p>CUSTOMER FOLLOWUP LIST</p>
                        </div>
                      </a>

              <?php } ?>

                 <?php
                      $submodules = "'134'";
                    $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
                    if ($as > 0) {

                        
                                                   
                    ?>
                <a href="<?php echo page_url; ?>FMS/stock_filter/<?php echo date('Y-m-d');?>/<?php echo date('Y-m-d');?>/1/ALL" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle">
                            <i class="fa fa-list"></i>
                          </div>
                          <p>INVENTORY OPENING/CLOSING REPORT</p>
                        </div>
                      </a>

              <?php } ?>

               <?php
                      $submodules = "'130'";
                    $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
                    if ($as > 0) {

                        
                                                   
                    ?>
                <a href="<?php echo page_url; ?>Sales_stats_reporting/customer_qty_comparision" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle">
                            <i class="fa fa-users"></i>
                          </div>
                          <p>CUSTOMER WISE SALE COMPARISION</p>
                        </div>
                      </a>

              <?php } ?>

                <?php
                      $submodules = "'135'";
                    $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
                    if ($as > 0) {

                        
                                                   
                    ?>
                <a href="<?php echo page_url; ?>FMS/sales_inventory_report/1">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle">
                            <i class="fa fa-list"></i>
                          </div>
                          <p>SALES INVENTORY REPORT</p>
                        </div>
                      </a>

              <?php } ?>

              <?php
                      $submodules = "'136'";
                    $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
                    if ($as > 0) {

                        $month = date('m');
                                                   
                    ?>
                <a href="<?php echo page_url; ?>FMS/stockmatching/<?php echo $month;?>">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle">
                            <i class="fa fa-list"></i>
                          </div>
                          <p>MANUAL STOCK ENTRY FOR RECONCILIATION</p>
                        </div>
                      </a>

              <?php } ?>


                <?php
                      $submodules = "'137'";
                    $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
                    if ($as > 0) {

                      
                                                   
                    ?>
                <a href="<?php echo page_url; ?>FMS/stock_reco">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle">
                            <i class="fa fa-file"></i>
                          </div>
                          <p>RECONCILIATION REPORT</p>
                        </div>
                      </a>

              <?php } ?>

               <?php
                      $submodules = "'140'";
                    $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
                    if ($as > 0) {

                      $d=$this->Dashboard_model->min_stock_alert();
                                                   
                    ?>
                <a href="<?php echo page_url; ?>FMS/min_stock_alert">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle">
                            <?php echo $d;?>
                          </div>
                          <p>MIN STOCK ALERT REPORT</p>
                        </div>
                      </a>

              <?php } ?>


               <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(170);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->yourtotaldistributor($user_id);?>
                      <a href="<?php echo page_url; ?>Distributor/distributor_list" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="again__circle">
                            <?php echo $b;?>
                          </div>
                          <p>Your Distributor</p>
                        </div>
                      </a>
                      <?php } ?>

              <div style="clear:both;height: 5px;"></div>



                <a href="<?php echo page_url; ?>Leads/common_approval_user">
                  <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                  <span class="btn btn-danger" style="animation: pulse 2s infinite;">Your Pending Approvals&nbsp;<span class="badge" id="user_approval" style="font-size:21px; font-weight:bold;"></span></span>
                  </div>
                  </a>
                    </div>
                  </div>
                </div>

                <?php } }?>
                
<!--USER DAILY TASK END HERE-->

           	 <?php
             $submodules = "'8', '9'";
              $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
              // echo $as;exit;
              if ($as > 0) {
              ?>
                <div class="col-sm-4">
                  <div class="dash__box">
                    <h3>Quotations</h3>
                    <hr>
                    <div class="row">

                      <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(8);
                              if ($a > 0) {
                      $b = $DI->Dashboard_model->quotations_expiring_today_user_count(); ?>
                      <a href="<?php echo page_url; ?>Customer/quotations_expiring_today_user">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>QUOTATIONS EXPIRING TODAY</p>
                        </div>
                      </a>
                      <?php } 
                      ?>

                      <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(9);
                              if ($a > 0) {
                      $b = $DI->Dashboard_model->rejected_quotations_user_count(); ?>
                      <a href="<?php echo page_url; ?>Customer/rejected_quotations_user">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>REJECTED QUOTATIONS</p>
                        </div>
                      </a>
                      <?php } 
                      ?>
                    </div>
                  </div>
                </div>
            	<?php } ?>

            	 <?php $a = $DI->Dashboard_model->checkForDashboardSubmodules(12);
                          if ($a > 0) {

				 if (count($getAllLeadStages) > 0) {

          
                  $i = 2; ?>
                  <div class="col-sm-8">
                    <div class="dash__box" style="min-height: 527px;">
                      <h3>Lead Stage Statistics <a href="<?php echo page_url;?>Search/globalfilter"><span class="pull-right btn btn-warning btn-sm">Global Filter</span></h3>
                      <hr>
                      <div class="row">
                        <?php $kl = 1; 
                        $datacount = $DI->Dashboard_model->visit_counts(); ?>
                         <!--  <a href="<?php echo page_url; ?>Leads/visits">
                            <div class="col-md-3 col-sm-3 col-xs-3 text-center">
                              <div class="again__circle"><?php echo $datacount;?></div>
                              <p>Visits Pending For Lead</p>
                            </div>
                          </a>
                         -->

                     
                        <?php $kl = 1;
                        //echo "<pre>"; print_r($getAllLeadStages); exit;
                        foreach ($getAllLeadStages as $row1) {
                          if($row1->customization_related==0){
                       
                          $datacount = $DI->Dashboard_model->lead_stage_counts($row1->lead_id); 

                           if($row1->step_type==0)
                          {
                            $color="border: 1px solid #fa5c50;";
                          }else
                          {
                            $color="border: 1px solid #5ea0d2;";
                          }
                           ?>

                          <a href="<?php echo page_url; ?>Leads/lead_stages/<?php echo $row1->lead_id; ?>">
                            <div class="col-md-3 col-sm-3 col-xs-3 text-center">
                              <div class="again__circle" style="<?php echo $color;?>"><?php echo $datacount; ?></div>
                              <p><?php echo $row1->lead_name; ?></p>
                            </div>
                          </a>
                          <?php if ($kl % 4 == 0) { ?>
                            <div style="clear: both;height:5px;"></div>
                          <?php } ?>
                        <?php $kl++;
                        } }?>
                        <div style="clear:both; height:10px;"></div>
<hr style="color:#000 !important">
                        <?php $kl = 1;
                        //echo "<pre>"; print_r($getAllLeadStages); exit;
                        foreach ($getAllLeadStages as $row1) {
                          if($row1->customization_related==1){
                       
                          $datacount = $DI->Dashboard_model->lead_stage_counts($row1->lead_id);
                           if($row1->step_type==0)
                          {
                            $color="border: 1px solid #fa5c50;";
                          }else
                          {
                            $color="border: 1px solid #5ea0d2;";
                          }
                            ?>

                          <a href="<?php echo page_url; ?>Leads/lead_stages/<?php echo $row1->lead_id; ?>">
                            <div class="col-md-3 col-sm-3 col-xs-3 text-center">
                              <div class="again__circle" style="<?php echo $color;?>"><?php echo $datacount; ?></div>
                              <p><?php echo $row1->lead_name; ?></p>
                            </div>
                          </a>
                          <?php if ($kl % 4 == 0) { ?>
                            <div style="clear: both;height:5px;"></div>
                          <?php } ?>
                        <?php $kl++;
                        } }?>




                      </div>
                    </div>
                  </div>
                <?php } } ?>

                 <?php $a = $DI->Dashboard_model->checkForDashboardSubmodules(13);
                  if ($a > 0) {

				 if (count($getAllLeadStages) > 0) {
                  $i = 2; ?>
                  <div class="col-sm-8">
                    <div class="dash__box" style="min-height: 527px;">
                      
                        <div class="row">
                          <div class="col-md-6"><h3>Lead Stage Statistics</h3></div>
                          <div class="col-md-3"><a href="<?php echo page_url; ?>Leads/addnewlead/<?php echo base64_encode($_SESSION['logged_in']['user_id']);?>"><span class="pull-right btn btn-primary btn-xs">Add New Lead</span><span style="padding:30px;;"></span></div>
                          <div class="col-md-3"> <a href="<?php echo page_url; ?>Distributor"><span class="pull-right btn btn-warning btn-xs">Add New Distributor</span></div>
                          
                        </div>  &nbsp; &nbsp; &nbsp;

                     
                      
                      <hr>
                      <div class="row">
                        <?php $kl = 1; ?>
                        <!--  <a href="<?php echo page_url; ?>Leads/addnewlead/<?php echo base64_encode($_SESSION['logged_in']['user_id']);?>" target="_blank">
                            <div class="col-md-3 col-sm-3 col-xs-3 text-center">
                              <div class="again__circle"><i class="fa fa-file"></i></div>
                              <p>Lead Form</p>
                            </div>
                          </a>
 -->

                         <?php //$kl = 2; 
                        $datacount = $DI->Dashboard_model->visit_counts_userwise($user_id); ?>
                         <!--  <a href="<?php echo page_url; ?>Leads/user_wise_visit">
                            <div class="col-md-3 col-sm-3 col-xs-3 text-center">
                              <div class="again__circle"><?php echo $datacount;?></div>
                              <p>Visits Pending For Lead</p>
                            </div>
                          </a> -->


                        <?php $kl = 1;
                        foreach ($getAllLeadStages as $row1) {
                          if($row1->customization_related==0){

                          $datacount = $DI->Dashboard_model->lead_stage_user_counts($row1->lead_id);  
                          if($row1->step_type==0)
                          {
                            $color="border: 1px solid #fa5c50;";
                          }else
                          {
                            $color="border: 1px solid #5ea0d2;";
                          }
                          ?>

                          <a href="<?php echo page_url; ?>Leads/lead_stages_user/<?php echo $row1->lead_id; ?>">
                            <div class="col-md-3 col-sm-3 col-xs-3 text-center">
                              <div class="again__circle" style="<?php echo $color;?>"><?php echo $datacount; ?></div>
                              <p><?php echo $row1->lead_name; ?></p>
                            </div>
                          </a>
                          <?php if ($kl % 4 == 0) { ?>
                            <div style="clear: both;height:5px;"></div>
                          <?php } ?>
                        <?php $kl++;
                        } }?>

                      
                        <div style="clear:both; height:10px;"></div>
                          <hr>
                        <?php $kl = 1;
                        foreach ($getAllLeadStages as $row1) {
                          if($row1->customization_related==1){

                          $datacount = $DI->Dashboard_model->lead_stage_user_counts($row1->lead_id);  
                          if($row1->step_type==0)
                          {
                            $color="border: 1px solid #fa5c50;";
                          }else
                          {
                            $color="border: 1px solid #5ea0d2;";
                          }
                          ?>

                          <a href="<?php echo page_url; ?>Leads/lead_stages_user/<?php echo $row1->lead_id; ?>">
                            <div class="col-md-3 col-sm-3 col-xs-3 text-center">
                              <div class="again__circle" style="<?php echo $color;?>"><?php echo $datacount; ?></div>
                              <p><?php echo $row1->lead_name; ?></p>
                            </div>
                          </a>
                          <?php if ($kl % 4 == 0) { ?>
                            <div style="clear: both;height:5px;"></div>
                          <?php } ?>
                        <?php $kl++;
                        } }?>
                      </div>
                    </div>
                  </div>
                <?php } } ?>

                 <?php
              $submodules = "'5','6','82','122'";
              $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
              if ($as > 0) {
              ?>
                <div class="col-sm-4">
                  <div class="dash__box">
                    <h3>Order</h3>
                    <hr>
                    <div class="row">
                        <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(82);
                          if ($a > 0) { ?>
                      <a href="<?php echo page_url; ?>Customer/direct_order_form">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle">
                            <i class="fa fa-wpforms" aria-hidden="true"></i>
                          </div>
                          <p>DIRECT ORDER FORM</p>
                        </div>
                      </a>
                      <?php } ?>
                      <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(5);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->pending_for_order_punch_count(); ?>
                    <!--   <a href="<?php echo page_url; ?>Leads/pending_for_order_punching">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>PENDING FOR ORDER PUNCHING</p>
                        </div>
                      </a> -->
                      <?php } 
                      ?>

                      <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(6);
                              if ($a > 0) {
                      $b = $DI->Dashboard_model->all_orders_count(); ?>
                      <a href="<?php echo page_url; ?>Customer/all_orders/<?php echo date('Y-m-d',strtotime('-1 Months'));?>/<?php echo date('Y-m-d');?>/ALL/ALL/ALL">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>ALL ORDERS</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                       <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(122);
                              if ($a > 0) {
                      $b = $DI->Dashboard_model->all_orders_count(); ?>
                      <a href="<?php echo page_url; ?>Sales_stats_reporting/margin_sheet/<?php echo date('Y-m-01');?>/<?php echo date('Y-m-t');?>/ALL">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-money"></i></div>
                          <p>MARGIN SHEET</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                    </div>
                  </div>
                </div>
            	<?php } ?>

                 <?php  $submodules = "'2','4','151'";
              $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
              if ($as > 0) {?>
                <div class="col-sm-4">
                  <div class="dash__box">
                    <h3>APPROVALS</h3>
                    <hr>
                    <div class="row">
                        <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(2);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->discount_approval_count(); ?>
                      <a href="<?php echo page_url; ?>Customer/discount_approval">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>Quotation Discount</p>
                        </div>
                      </a>
                       <?php } ?>

                      <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(3);
                              if ($a > 0) {
                      $b = $DI->Dashboard_model->quotations_expiring_today_count(); ?>
                      <a href="<?php echo page_url; ?>Customer/quotations_expiring_today">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>Quotations Expiring Today(Running)</p>
                        </div>
                      </a>
                      <?php } 
                      ?>

                      <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(151);
                              if ($a > 0) {
                      $b = $DI->Dashboard_model->paymenttermsapproval(); ?>
                      <a href="<?php echo page_url; ?>Customer/customerpendingforpaymenttermsapproval">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>Customer Payment Terms</p>
                        </div>
                      </a>
                      <?php } 
                      ?>

                      <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(39);
                              if ($a > 0) {
                      $b = $DI->Dashboard_model->lead_quotations_expiring_today_count(); ?>
                      <a href="<?php echo page_url; ?>Leads/lead_quotations_expiring_today">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>Quotations Expiring Today (New)</p>
                        </div>
                      </a>
                      <?php } 
                      ?>

                      <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(4);
                              if ($a > 0) {
                      $b = $DI->Dashboard_model->rejected_quotations_count(); ?>
                      <a href="<?php echo page_url; ?>Customer/rejected_quotations">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>Rejected Quotations</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                      <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(121);
                              if ($a > 0) {
                     ?>
                      <a href="<?php echo page_url; ?>Customer/quotation">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-file"></i></div>
                          <p>Create Old Customer Quotations</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                        <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(119);
                              if ($a > 0) {
                      $b = $DI->Dashboard_model->old_quote_for_orders_admin(); ?>
                      <a href="<?php echo page_url; ?>Customer/quotation_dashboard">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>Old Customer Quotations<br/>Pending for Order</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                         <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(120);
                              if ($a > 0) {
                      $b = $DI->Dashboard_model->old_quote_for_orders_user(); ?>
                      <a href="<?php echo page_url; ?>Customer/quotation_dashboard">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>Old Customer Quotations <br/>Pending for Order</p>
                        </div>
                      </a>
                      <?php } 
                      ?>
                       <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(2);
                              if($a > 0) {
                      $b = $DI->Dashboard_model->po_approval(); ?>
                      <a href="<?php echo page_url; ?>Reporting/pendingpoforapproval_change">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b;?></div>
                          <p>Pending PO for Approval</p>
                        </div>
                      </a>
                      <?php } 
                      ?>



                    </div>
                  </div>
                </div>
              <?php } ?>


  <?php  $submodules = "'153','154','155','174'";
              $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
              if ($as > 0) {?>
                <div class="col-sm-4">
                  <div class="dash__box">
                    <h3>PURCHASE</h3>
                    <hr>
                    <div class="row">
                        <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(153);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->discount_approval_count(); ?>
                      <a href="<?php echo page_url; ?>Store/indent_form">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-wpforms"></i></div>
                          <p>Indent Form</p>
                        </div>
                      </a>
                       <?php } ?>

                      

                        <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(174);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->pending_indent_for_review(); ?>
                      <a href="<?php echo page_url; ?>Reporting/pending_indent_dashboard">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b;?></div>
                          <p>Indent For Approval</p>
                        </div>
                      </a>
                       <?php } ?>



                      <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(154);
                              if ($a > 0) {
                      $b = $DI->Dashboard_model->quotations_expiring_today_count(); ?>
                      <a href="<?php echo page_url; ?>Store/createbulkpo">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-list"></i></div>
                          <p>Pending PR for PO Generation</p>
                        </div>
                      </a>
                      <?php } 
                      ?>
                      <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(155);
                              if ($a > 0) {
                      $b = $DI->Dashboard_model->quotations_expiring_today_count(); ?>
                      <a href="<?php echo page_url; ?>Reporting/itemmrn">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-list"></i></div>
                          <p>Pending MRN Requests</p>
                        </div>
                      </a>
                      <?php } 
                      ?>

                    </div>
                  </div>
                </div>
              <?php } ?>

                <?php  $submodules = "'156','157','168','169'";
              $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
              if ($as > 0) {?>
                <div class="col-sm-4">
                  <div class="dash__box">
                    <h3>STORE</h3>
                    <hr>
                    <div class="row">
                        <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(156);
                          if ($a > 0) {?>
                      
                      <a href="<?php echo page_url; ?>Reporting/gateentry">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-wpforms"></i></div>
                          <p>QC</p>
                        </div>
                      </a>
                       <?php } ?>

                      <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(157);
                              if ($a > 0) {?>
                      
                      <a href="<?php echo page_url; ?>Store/storereciept">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-list"></i></div>
                          <p>Store Receipt</p>
                        </div>
                      </a>
                      <?php } 
                      ?>
                      <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(158);
                              if ($a > 0) {?>
                     
                      <a href="<?php echo page_url; ?>Store/unacknowledgeditems">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-list"></i></div>
                          <p>Material Issued not Updated for Store</p>
                        </div>
                      </a>
                      <?php } 
                      ?>

                       <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(159);
                              if ($a > 0) {?>
                     
                      <a href="<?php echo page_url; ?>User/issuejobcard">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-list"></i></div>
                          <p>Material Issue Form</p>
                        </div>
                      </a>
                      <?php } 
                      ?>

                    </div>
                  </div>
                </div>
              <?php } ?>


            	               <?php
              $submodules = "'10','11'";
              $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
              if ($as > 0) {
              ?>
                <div class="col-sm-4">
                  <div class="dash__box">
                    <h3>Order</h3>
                    <hr>
                    <div class="row">
                       
                      <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(10);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->pending_for_order_punch_user_count(); ?>
                     <!--  <a href="<?php echo page_url; ?>Leads/pending_for_order_punching_user">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>PENDING FOR ORDER PUNCHING</p>
                        </div>
                      </a> -->
                      <?php } 
                      ?>

                      <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(11);
                              if ($a > 0) {
                      $b = $DI->Dashboard_model->all_orders_user_count(); ?>
                      <a href="<?php echo page_url; ?>Customer/all_orders_user/<?php echo date('Y-m-d',strtotime('-1 Months'));?>/<?php echo date('Y-m-d');?>">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>ALL ORDERS</p>
                        </div>
                      </a>
                      <?php } 
                      ?>
                    </div>
                  </div>
                </div>
            	<?php } ?>


                             <?php
                             $submodules = "'35','37','40','88','89','90','91','92','93','94','95','96','97','98','129','138','145','146','148'";
              $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
              if ($as > 0) {
              ?>
                <div class="col-sm-4">
                  <div class="dash__box">
                    <h3>Billing & Payments</h3>
                    <hr>
                    <div class="row">
                      <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(96);
                          if ($a > 0) {
                     ?>
                      <a href="<?php echo page_url; ?>Open_leads/choose_company/<?php echo base64_encode($_SESSION['logged_in']['user_id']);?>" target="_blank">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-file"></i></div>
                          <p>Payment Form</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                        <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(98);
                          if ($a > 0) {
                     ?>
                      <a href="<?php echo page_url; ?>Billing/payment_history_data/<?php echo base64_encode($_SESSION['logged_in']['user_id']);?>" target="_blank">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-file"></i></div>
                          <p>Payment Form History</p>
                        </div>
                      </a>
                      <?php } 
                      ?>

                      <!-- <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(37);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->pending_for_ppc_clearance_today_count(); ?>
                      <a href="<?php echo page_url; ?>Customer/pending_for_ppc_clearance_today">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>PENDING PDC FOR CLEARANCE TODAY</p>
                        </div>
                      </a>
                      <?php } 
                      ?> -->

                      <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(88);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->pending_for_pdc_recd_count(); ?>
                      <a href="<?php echo page_url; ?>Customer/pending_for_pdc_recd">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?> </div>
                          <p>PDC TO BE PICKED UP</p>
                        </div>
                      </a>
                      <?php } 
                      ?>

                    <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(89);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->pending_for_pdc_dep_count(); ?>
                      <a href="<?php echo page_url; ?>Customer/pending_for_pdc_deposited">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?> </div>
                          <p>PDC TO BE DEPOSITED</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                      <!-- FOR ADMIN -->
                        <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(90);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->order_payment_overdue(); ?>
                      <a href="<?php echo page_url; ?>Customer/payment_overdue">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?> </div>
                          <p>Payments Overdue</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                        <!-- FOR USER -->
                        <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(91);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->order_payment_overdue_agent_wise($_SESSION['logged_in']['user_id']); ?>
                      <a href="<?php echo page_url; ?>Customer/payment_overdue/<?php echo $_SESSION['logged_in']['user_id'];?>">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?> </div>
                          <p>Payments Overdue</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                      <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(92);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->order_payment_upcoming(); ?>
                      <a href="<?php echo page_url; ?>Customer/upcoming_customer_payments">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?> </div>
                          <p>Upcoming Payments (in 10 Days)</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                       <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(93);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->order_payment_upcoming_agent_wise($_SESSION['logged_in']['user_id']); ?>
                      <a href="<?php echo page_url; ?>Customer/upcoming_customer_payments/<?php echo $_SESSION['logged_in']['user_id'];?>">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?> </div>
                          <p>Upcoming Payments (in 10 Days)</p>
                        </div>
                      </a>
                      <?php } 
                      ?>
                      
                    <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(94);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->orders_on_hold_count(); ?>
                      <!-- <a href="<?php echo page_url; ?>Billing/order_on_hold">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?> </div>
                          <p>Order On Hold</p>
                        </div>
                      </a> -->
                      <?php } 
                      ?>

                    <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(129);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->orders_on_hold_om(); ?>
                      <a href="<?php echo page_url; ?>Billing/orders_on_hold_om">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?> </div>
                          <p>Order On Hold</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                       <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(40);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->pending_sales_order_for_billing(); ?>
                      <a href="<?php echo page_url; ?>Billing/order_pending_for_billing_so">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>PENDING SALES ORDER FOR BILLING</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                      <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(40);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->pending_order_for_billing(); ?>
                      <a href="<?php echo page_url; ?>Billing/order_pending_for_billing">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>PENDING BILLING</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                        <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(138);
                          if ($a > 0) {
                      ?>
                      <a href="<?php echo page_url; ?>Billing/billing_history/<?php echo date('Y-m-d',strtotime('-1 Months'));?>/<?php echo date('Y-m-d');?>/ALL/ALL/ALL">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-list"></i></div>
                          <p>BILLING HISTORY</p>
                        </div>
                      </a>
                      <?php } 
                      ?>

                         <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(148);
                          if ($a > 0) {
                      ?>
                      <a href="<?php echo page_url; ?>Billing/rebrand_product_history/<?php echo date('Y-m-d',strtotime('-1 Months'));?>/<?php echo date('Y-m-d');?>/ALL/ALL/ALL">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-list"></i></div>
                          <p>PRODUCT REBRAND HISTORY</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                      <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(97);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->orders_pending_for_payment(); ?>
                      <a href="<?php echo page_url; ?>Billing/order_pending_for_complete_payment">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>Orders with payment due</p>
                        </div>
                      </a>
                      <?php } 
                      ?>
                       <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(107);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->unfollow_customer_for_payment(); ?>
                      <a href="<?php echo page_url; ?>Customer/unfollow_customer_for_payment">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>Unfollow Customer</p>
                        </div>
                      </a>
                      <?php 
                      } 
                      ?>

                       <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(108);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->unfollow_customer_for_user(); ?>
                      <a href="<?php echo page_url; ?>Customer/unfollow_customer_for_user">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>Unfollow Customer</p>
                        </div>
                      </a>
                      <?php 
                      } 
                      ?>

                       <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(145);
                          if ($a > 0) {
                      ?>
                      <a href="<?php echo page_url; ?>Payment/cheque_bounce">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-money"></i></div>
                          <p>Cheque Bounce</p>
                        </div>
                      </a>
                      <?php 
                      } 
                      ?>


                         <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(146);
                          if ($a > 0) {
                      ?>
                       <a href="<?php echo page_url; ?>Payment/cheque_bounce_history">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-money"></i></div>
                          <p>Cheque Bounce History</p>
                        </div>
                      </a>
                      <?php 
                      } 
                      ?>


                    </div>
                  </div>
                </div>
              <?php } ?>

                             <?php
                             $submodules = "'36', '38'";
              $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
              if ($as > 0) {
              ?>
                <div class="col-sm-4">
                  <div class="dash__box">
                    <h3>Billing</h3>
                    <hr>
                    <div class="row">
                      <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(36);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->pending_for_ppc_clearance_user_count(); ?>
                      <a href="<?php echo page_url; ?>Customer/pending_for_ppc_clearance_user">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>PENDING PDC FOR CLEARANCE</p>
                        </div>
                      </a>
                      <?php } 
                      ?>

                      <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(38);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->pending_for_ppc_clearance_today_user_count(); ?>
                      <a href="<?php echo page_url; ?>Customer/pending_for_ppc_clearance_today_user">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>PENDING PDC FOR CLEARANCE TODAY</p>
                        </div>
                      </a>
                      <?php } 
                      ?>

                       <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(41);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->pending_for_dispatch_count(); ?>
                      <a href="<?php echo page_url; ?>Billing/pending_for_dispatch">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>DISPATCH</p>
                        </div>
                      </a>
                      <?php } 
                      ?>
                    </div>
                  </div>
                </div>
              <?php } ?>

                <?php
                $submodules = "'41'";
                $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
                if ($as > 0) {
                ?>
                <div class="col-sm-4">
                  <div class="dash__box">
                    <h3>PRODUCTION</h3>
                    <hr>
                    <div class="row">
                         <a href="<?php echo page_url;?>FMS/neworderform">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-file"></i></div>
                          <p>CREATE PRODUCTION ORDER</p>
                        </div>
                      </a>
                       <a href="<?php echo page_url;?>FMS/order">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle">
                            
                          </div>
                          <p>ALL ORDERS</p>
                        </div>
                      </a>
                       <a href="<?php echo page_url;?>/FMS/unplannedorder">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-list"></i></div>
                          <p>UNPLANNED ORDERS</p>
                        </div>
                      </a>
                      <?php
                        $q = $this->db->select('flow_id, fms_flow, dependency, production_flow_id')->from('fms_flow')->where('status',1)->order_by('setorder','ASC')->get();
                        foreach($q->result() as $proc){
                           $mmer = $CI->Fms_model->checkifmsmerge($proc->flow_id);


                                    $skkipl = $CI->Fms_model->checkifstepisskippable($proc->flow_id);

                                    /** CHECK FOR DEPENDENCY **/
                                    $checkdepend = $this->db->select('dependency')->from('fms_flow_view')->where('flow_id', $proc->flow_id)->where('dependency', '1')->get();
                                    $depend = $checkdepend->num_rows();

                                    if ($mmer == 0 && $skkipl == 0 && $depend == 0) {

                                        $flowtottask = $CI->Fms_model->menunotifications($proc->flow_id, $proc->dependency);
                                    } else {


                                       
                                        //$flowtottask = $CI->Fms_model->menunotificationsformergeflow($proc->flow_id, $proc->production_flow_id);
                                        $flowtottask = $CI->Fms_model->new_merge_flow_count($proc->flow_id, $proc->production_flow_id);
                                       

                                    }
                      ?> 

                      <a href="<?php echo page_url; ?>Orderstage/process/<?php echo $proc->flow_id; ?>/<?php echo $proc->production_flow_id; ?>">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $flowtottask ;?></div>
                          <p><?php echo $proc->fms_flow;?></p>
                        </div>
                      </a>
                      <?php }?>
                    </div>
                  </div>
                </div>
              <?php } ?>


               <?php
              $submodules = "'50','51'";
              $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
              if ($as > 0) {
              ?>
                <div class="col-sm-4">
                  <div class="dash__box">
                    <h3>Sampling/Trail</h3>
                    <hr>
                    <div class="row">
                       <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(111);
                          if ($a > 0) {
                      //$b = $DI->Dashboard_model->pending_for_sample_request(); ?>
                      <a href="<?php echo page_url; ?>Trail">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-file"></i></div>
                          <p>Trail Request form<br>
                          Old Customer</p>
                        </div>
                      </a>
                      <?php } 
                      ?>
                      <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(50);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->pending_for_sample_request(); ?>
                      <a href="<?php echo page_url; ?>Sampling/pending_samples">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>Samples to be send</p>
                        </div>
                      </a>
                      <?php } 
                      ?>

                      <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(57);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->pending_for_trial_request(); ?>
                      <a href="<?php echo page_url; ?>Sampling/pending_trials">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>Trial Start</p>
                        </div>
                      </a>
                      <?php } 
                      ?>
                       <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(112);
                          if ($a > 0) {
                      $b2 = $DI->Dashboard_model->pending_for_trial_request_for_user($user_id); ?>
                      <a href="<?php echo page_url; ?>Sampling/pending_trials">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b2; ?></div>
                          <p>Trial Start</p>
                        </div>
                      </a>
                      <?php } 
                      ?>

                       <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(115);
                          if ($a > 0) {
                      //$b2 = $DI->Dashboard_model->pending_for_trial_request_for_user($user_id); ?>
                      <a href="<?php echo page_url; ?>Sampling/samplependingforview">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-list"></i></div>
                          <p>Your Sample Request </p>
                        </div>
                      </a>
                      <?php } 
                      ?>

                        <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(116);
                          if ($a > 0) {
                      //$b2 = $DI->Dashboard_model->pending_for_trial_request_for_user($user_id); ?>
                      <a href="<?php echo page_url; ?>Sampling/yourtrailrequestreport">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-list"></i></div>
                          <p>Your Trail Request </p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                      <!-- <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(86);
                          if ($a > 0) { ?>
                      <a href="<?php echo page_url; ?>Sampling/trial_readings">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle">
                            <i class="fa fa-wpforms" aria-hidden="true"></i>
                          </div>
                          <p>ADD TRIAL READINGS</p>
                        </div>
                      </a>
                      <?php } ?> -->
                    </div>
                  </div>
                </div>
              <?php } ?>

                <div class="col-sm-4" style="display:none">
                  <div class="dash__box">
                    <h3>Conveyance</h3>
                    <hr>
                    <div class="row">
                      <a href="<?php echo page_url; ?>Sales/conveyance_voucher_new">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle">
                            <i class="fa fa-wpforms" aria-hidden="true"></i>
                          </div>
                          <p>CONVEYANCE FORM & DATA</p>
                        </div>
                      </a>


                       <a href="<?php echo page_url; ?>Sales/user_wise_approval_pending">
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="again__circle" id="dailyworkreport"><i class="fa fa-dashboard" aria-hidden="true"></i></div>
                          <p>YOUR PENDING CONVEYANCE FOR APPROVAL</p>
                        </div>
                      </a>

                      <a href="<?php echo page_url; ?>Sales/user_wise_approval">
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="again__circle" id="dailyworkreport"><i class="fa fa-dashboard" aria-hidden="true"></i></div>
                          <p>YOUR APPROVED CONVEYANCE</p>
                        </div>
                      </a>
                      <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(83);
                          if ($a > 0) { ?>

                      <a href="<?php echo page_url; ?>Sales/local_conveyance_dashboard_new/">
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="again__circle" id="dailyworkreport"> 
							<?php
						  $b=$DI->Dashboard_model->convence_for_approval();
              echo $b; 
							?></div>
                          <p>HOD CONVEYANCE DASHBOARD</p>
                        </div>
                      </a>
                    <?php } ?>
                     <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(84);
                          if ($a > 0) { //conveyance_data_for_accounts ?>
                      <a href="<?php echo page_url; ?>Sales/accounts_convence_dashboard">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle">
                            <i class="fa fa-wpforms" aria-hidden="true"></i>
                          </div>
                          <p>CONVEYANCE REPORT FOR ACCOUNTS</p>
                        </div>
                      </a>
                    <?php } ?>
                    </div>
                  </div>
                </div>
                <div class="col-sm-4" style="display:none">
                  <div class="dash__box">
                    <h3>Leave Management</h3>
                    <hr>
                    <div class="row">
                      <a href="<?php echo page_url; ?>Hr/leave_application">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle">
                            <i class="fa fa-wpforms" aria-hidden="true"></i>
                          </div>
                          <p>LEAVE APPLICATION</p>
                        </div>
                      </a>

                      <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(74);
                          if ($a > 0) {
                      ?>
                        <a href="<?php echo page_url; ?>Hr/view_your_leave">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle">
                            <i class="fa fa-wpforms" aria-hidden="true"></i>
                          </div>
                          <p>VIEW YOUR APPLICATION</p>
                        </div>
                      </a>
                      <?php 
                    }
                    ?>

                      <a href="<?php echo page_url; ?>Hr/hod_leave_dashboard">
                        <div class="col-md-4 col-sm-4 col-xs-4">
						<div class="again__circle" id="dailyworkreport"><?php
						$leave_data = array();
						$user_id = $this->session->userdata['logged_in']['user_id'];

						$q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader', $user_id)->get();

						if ($q->num_rows() > 0) {
						foreach ($q->result() as $teamdetail);
						$teammembers = array();
						$query = $this->db->select('team_id, employee_id')->from('presto_team_members')->where('team_id', $teamdetail->team_id)->get();
						$res = $query->result();

						foreach ($res as $teaminfo) {
						$teammembers[] = $teaminfo->employee_id;
						}

						//$team = implode(',',$teammembers);
						$team = "'" . implode("', '", $teammembers) . "'";
						} else {
						$team = 'NA';
						}

						if ($team <> 'NA') {

						$user_id = $this->session->userdata['logged_in']['user_id'];
						$this->db->select('a.id')->from('leave_application a')->join('system_users b', 'a.employee_id=b.user_id', 'left')->join('departments c', 'b.department_id=c.department_id', 'left')->join('user_role d', 'b.user_role_id=d.user_role_id', 'left');
						$this->db->where_in('a.employee_id', $team, false)->where('a.approval_status', '0');
						$query = $this->db->order_by('leave_date', 'desc')->get();
						$res = $query->result();
						echo count($res);
						}else if($user_id==1){
						    $this->db->select('a.id')->from('leave_application a')->join('system_users b', 'a.employee_id=b.user_id', 'left')->join('departments c', 'b.department_id=c.department_id', 'left')->join('user_role d', 'b.user_role_id=d.user_role_id', 'left')->where('a.approval_status', '0');
						$query = $this->db->order_by('leave_date', 'desc')->get();
						$res = $query->result();
						echo count($res);
						    
						} else {
						?>0
						<?php } ?></div>
                          <p>LEAVE HOD DASHBOARD</p>
                        </div>
                      </a>

                      <a href="<?php echo page_url; ?>Hr/onleavetoday">
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="again__circle" id="dailyworkreport">0</div>
                          <p>ON LEAVE TODAY DASHBOARD</p>
                        </div>
                      </a>
                    </div>
                    <?php if($user_id==1){
                    ?>
                    <div class="row">

                      <a href="<?php echo page_url; ?>Master/User_management/attendance_dashboard">
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="again__circle" id="dailyworkreport"><?php 
                          $q= $this->db->select('id')->from('mark_your_attendance')->where('attendance_date',date('Y-m-d'))->order_by('id','desc')->get();
                          echo count($q->result());
                          ?></div>
                          <p>ATTENDANCE REPORT OF THE DAY</p>
                        </div>
                      </a>
                      <a href="<?php echo page_url; ?>Hr/employee_monthly_attendance">
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="again__circle" id="dailyworkreport"><?php 
                          $q = $this->db->select('user_id')->from('system_users')->where('user_status',1)->get();
                          echo count($q->result());
                          ?></div>
                          <p>EMPLOYEE ATTENDANCE MONTHLY REPORT</p>
                        </div>
                      </a>

                    </div>
                    <?php }?>
                  </div>
                </div> 
                <?php
               $submodules = "'42','43','44','45','46','54','55','63','128','131','133'";
              $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
              if ($as > 0) {
              ?>
                <div class="col-sm-4" style="display: none;">
                  <div class="dash__box">
                    <h3>TYPE - I</h3>
                    <span style="color:red;">Type I refers where Sundar Oil purchases Item from HPCL</span>

                    <hr>
                    <div class="row">
                      <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(46);
                          if ($a > 0) { ?>
                      <a href="<?php echo page_url; ?>Approval/approval">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-wpforms" aria-hidden="true"></i></div>
                          <p>ADD APPROVAL</p>
                        </div>
                      </a>
                      <?php } ?>

                    

                      <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(42);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->pending_for_approval_count(); ?>
                      <a href="<?php echo page_url; ?>Approval">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>THIS MONTH'S APPROVALS</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                           <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(131);
                          if ($a > 0) { ?>
                      <a href="<?php echo page_url; ?>Approval/approval_summary/<?php echo date('Y-m-01');?>/<?php echo date('Y-m-t');?>">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-wpforms" aria-hidden="true"></i></div>
                          <p>APPROVAL SUMMARY</p>
                        </div>
                      </a>
                      <?php } ?>
                      
                      <?php
                     // $a = $DI->Dashboard_model->checkForDashboardSubmodules(55);
                     //      if ($a > 0) {
                     //  $b = $DI->Dashboard_model->this_month_purchases_count(); ?>
                      <!-- <a href="<?php //echo page_url; ?>Inventory/this_month_purchases/<?php //echo date('Y-m-01')?>/<?php //echo date('Y-m-t');?>/ALL/ALL">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>THIS MONTH'S PURCHASES</p>
                        </div>
                      </a> -->
                      <?php //} 
                      ?>

                      <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(43);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->payment_due_today_count(); ?>
                      <a href="<?php echo page_url; ?>Approval/payment_due_today">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>PAYMENT DUE TODAY</p>
                        </div>
                      </a>
                      <?php } 
                      ?>

                       <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(44);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->upcoming_payments_count(); ?>
                      <a href="<?php echo page_url; ?>Approval/upcoming_payments/ALL/<?php echo date('Y-m-01');?>/<?php echo date('Y-m-t');?>">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>UPCOMING/PENDING PAYMENTS</p>
                        </div>
                      </a>
                      <?php } 
                      ?>

                       <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(45);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->late_payments_count(date('Y-m-01'),date('Y-m-t')); ?>
                     <!--  <a href="<?php echo page_url; ?>Approval/late_payments/ALL/<?php echo date('Y-m-01');?>/<?php echo date('Y-m-t');?>">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>LATE PAYMENTS FOR THE MONTH</p>
                        </div>
                      </a> -->
                      <?php } 
                      ?>


                        <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(128);
                          if ($a > 0) { ?>
                      <a href="<?php echo page_url; ?>Approval/type_1_trail/<?php echo date('Y-m-01');?>/<?php echo date('Y-m-t');?>/ALL">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-wpforms" aria-hidden="true"></i></div>
                          <p>TRIAL RUN</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


					  
					  <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(54);
                          if ($a > 0) { ?>
                      <a href="<?php echo page_url; ?>Approval/this_month_claim/<?php echo date('Y-m-01');?>/<?php echo date('Y-m-t');?>/ALL">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-wpforms" aria-hidden="true"></i></div>
                          <p>THIS MONTH'S CLAIM</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                        <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(133);
                          if ($a > 0) { ?>
                      <a href="<?php echo page_url; ?>ExcelImport/type_one_claim_without_interest/2022-10-01/<?php echo date('Y-m-d');?>/ALL/">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-list" aria-hidden="true"></i></div>
                          <p>SUNDER TYPE 1 LEDGER</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


					  
					   <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(63);
                          if ($a > 0) { ?>
                     <!--  <a href="<?php echo page_url; ?>Type_reporting/type1statistics/">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-wpforms" aria-hidden="true"></i></div>
                          <p>TYPE 1 STATISTICS REPORT</p>
                        </div>
                      </a> -->
                      <?php } 
                      ?>

                    <!--   <a href="<?php echo page_url; ?>Type_reporting/all_type_files/<?php echo date('Y-m-01');?>/<?php echo date('Y-m-t');?>">
                      <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                      <span class="btn btn-danger" style="animation: pulse 2s infinite;">This Month's Claim, Commission, Transportation File&nbsp;</span>
                      </div>
                      </a> -->
                    </div>
                  </div>
                </div>
            <?php } ?>

                <?php
              $submodules = "'52','53','60','61','62','65','70','100','105','118','124','126'";
              $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
              if ($as > 0) {
              ?>
                <div class="col-sm-4">
                  <div class="dash__box">
                    <h3>TYPE - II/III</h3>
                    <span style="color:red;">Type II/III refers where HPCL directly bills customer, Sundar Oil Act as Commission Agent</span>
                    <hr>
                    <div class="row">
                      <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(52);
                          if ($a > 0) { ?>
                      <a href="<?php echo page_url; ?>Approval/type_two_approval_new">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-wpforms" aria-hidden="true"></i></div>
                          <p>ADD APPROVAL</p>
                        </div>
                      </a>
                      <?php } ?>

                      <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(53);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->type_two_approval_count(); ?>
                      <a href="<?php echo page_url; ?>Approval/type_two_approval_list/<?php echo date('Y-m-01')?>/<?php echo date('Y-m-t');?>/ALL/ALL/ALL">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                            <p>THIS MONTH'S APPROVALS</p>
                        </div>
                      </a>
                      <?php } 
                      ?>



                         <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(70);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->type_two_approval_count_delivery_pending_type_2(); ?>
                     <!--  <a href="<?php //echo page_url; ?>Approval/type_two_approval_list_pending_delivery/<?php //echo date('Y-m-01')?>/<?php //echo date('Y-m-t');?>/ALL/ALL/ALL">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                            <p>PENDING APPROVALS FOR COMMISION</p>
                        </div>
                      </a> -->
                      <?php } 
                      ?>

                       <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(118);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->type_2_3_invoice_this_month(); ?>
                      <a href="<?php echo page_url; ?>Approval/type_two_approval_list_upload_invoice/<?php echo date('Y-m-01')?>/<?php echo date('Y-m-t');?>/ALL/ALL/ALL">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b;?></div>
                            <p>TYPE II/III INVOICES</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                      <?php
                      // $a = $DI->Dashboard_model->checkForDashboardSubmodules(60);
                      // if ($a > 0) {
                      // $b = $DI->Dashboard_model->pending_customer_payment(); 
                      ?>
                     <!--  <a href="<?php //echo page_url; ?>Approval/type_2_customer_payment_pending/<?php //echo date('Y-m-01');?>/<?php //echo date('Y-m-t');?>/ALL">
                      <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                      <div class="again__circle"><?php //echo $b; ?></div>
                      <p>PENDING PAYMENT FROM CUSTOMER</p>
                      </div>
                      </a> -->
                      <?php 
                      // } 
                      ?>


                      <?php
                      $a = $DI->Dashboard_model->checkForDashboardSubmodules(60);
                      if ($a > 0) {
                      $b = $DI->Dashboard_model->pending_customer_payment_new(); ?>
                      <a href="<?php echo page_url; ?>Approval/type_2_customer_payment_pending_new/ALL/ALL/ALL">
                      <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                      <div class="again__circle"><?php echo $b; ?></div>
                      <p>PENDING PAYMENT FROM CUSTOMER</p>
                      </div>
                      </a>
                      <?php } 
                      ?>



                        <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(124);
                          if ($a > 0) {
                      ?>
                      <a href="<?php echo page_url; ?>Approval/type_two_approval_list_trial/<?php echo date('Y-m-01')?>/<?php echo date('Y-m-t');?>/ALL/ALL/">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-wpforms" aria-hidden="true"></i></div>
                            <p>APPROVALS TRIAL RUN</p>
                        </div>
                      </a>
                      <?php } 
                      ?>



                       <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(61);
                          if ($a > 0) {
                      ?>
                      <a href="<?php echo page_url; ?>Approval/this_month_claim_type2/<?php echo date('Y-m-01')?>/<?php echo date('Y-m-t');?>/ALL/ALL/">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-wpforms" aria-hidden="true"></i></div>
                            <p>THIS MONTH'S CFA COMMISSION & TRANSPORTATION</p>
                        </div>
                      </a>
                      <?php } 
                      ?>



                      



                        <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(62);
                          if ($a > 0) {
                            $b=$DI->Dashboard_model->transporter_pending_payment_type_2();
                      ?>
                      <a href="<?php echo page_url; ?>Approval/pending_transporters_payment/ALL/ALL">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b;?></div>
                            <p>TRANSPORTERS PENDING PAYMENT</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                       <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(100);
                          if ($a > 0) {
                            $b=$DI->Dashboard_model->type_two_approval_count_late_payment_type_2();
                      ?>
                     <!--  <a href="<?php //echo page_url; ?>Approval/type_2_customer_payment_charges/<?php //echo date('Y-m-01');?>/<?php //echo date('Y-m-t');?>">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b;?></div>
                            <p>LATE PAYMENT CHARGES</p>
                        </div>
                      </a> -->
                      <?php } 
                      ?>

                    


                       <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(65);
                          if ($a > 0) { ?>
                     <!--  <a href="<?php echo page_url; ?>Type_reporting/typetwostatistics/">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-wpforms" aria-hidden="true"></i></div>
                          <p>TYPE II STATISTICS REPORT</p>
                        </div>
                      </a> -->
                      <?php } 
                      ?>

                      <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(105);
                          if ($a > 0) { ?>
                      <a href="<?php echo page_url; ?>Store/direct_customer_stock_in_ward">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-wpforms" aria-hidden="true"></i></div>
                          <p>STOCK INWARD</p>
                        </div>
                      </a>
                      <?php } 
                      ?>                      

                      <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(106);
                          if ($a > 0) { ?>
                      <a href="<?php echo page_url; ?>Store/customer_stock_inward_list">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-wpforms" aria-hidden="true"></i></div>
                          <p>STOCK INWARD HISTORY</p>
                        </div>
                      </a>
                      <?php } 
                      ?>

                       <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(105);
                          if ($a > 0) { ?>
                      <a href="<?php echo page_url; ?>Store/direct_customer_stock_outward">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-wpforms" aria-hidden="true"></i></div>
                          <p>STOCK OUTWARD</p>
                        </div>
                      </a>
                      <?php } 
                      ?>                      

                      <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(106);
                          if ($a > 0) { ?>
                      <a href="<?php echo page_url; ?>Store/customer_stock_outward_list">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-wpforms" aria-hidden="true"></i></div>
                          <p>STOCK OUTWARD HISTORY</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                         <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(126);
                          if ($a > 0) { ?>
                      <a href="<?php echo page_url; ?>ExcelImport/customer_ledger">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-users" aria-hidden="true"></i></div>
                          <p>CUSTOMER LEDGER</p>
                        </div>
                      </a>
                      <?php } 
                      ?>

                    </div>
                  </div>
                </div>
            <?php } ?>

            <?php
              $submodules = "'64','66','67','68','69','71','72','73'";
              $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
              if ($as > 0) {
              ?>
                <div class="col-sm-4">
                  <div class="dash__box">
                    <h3>TYPE - III</h3>
                     <span style="color:red;">Type III refers where Item comes to Sundar godown and is dispatched by Sundar Oil</span>
                    <hr>
                    <div class="row">
                      <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(64);
                          if ($a > 0) { ?>
                      <a href="<?php echo page_url; ?>Approval/type_three_approval">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-wpforms" aria-hidden="true"></i></div>
                          <p>ADD TYPE-III APPROVAL</p>
                        </div>
                      </a>
                      <?php } ?>


                        <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(66);
                          if ($a > 0) { 
                            $b = $DI->Dashboard_model->type_three_approval_count(); ?>
                      <a href="<?php echo page_url; ?>Approval/type_three_approval_list/<?php echo date('Y-m-01')?>/<?php echo date('Y-m-t');?>/ALL/ALL/ALL">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b;?></div>
                          <p>THIS MONTH'S APPROVAL</p>
                        </div>
                      </a>
                      <?php } ?>

                         <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(71);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->type_two_approval_count_delivery_pending_type_3(); ?>
                      <a href="<?php echo page_url; ?>Approval/type_three_approval_list_pending_delivery/<?php echo date('Y-m-01')?>/<?php echo date('Y-m-t');?>/ALL/ALL/ALL">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                            <p>PENDING APPROVALS FOR DELIVERY</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                        <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(67);
                          if ($a > 0) {
                      $b = $DI->Dashboard_model->type_three_transportation_count(); ?>
                      <a href="<?php echo page_url; ?>Approval/type_3_transportation_claim/<?php echo date('Y-m-01')?>/<?php echo date('Y-m-t');?>/ALL/ALL">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                            <p>THIS MONTH'S TRANSPORTATION CLAIM</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                          <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(68);
                          if ($a > 0) {
                      ?>
                      <a href="<?php echo page_url; ?>Approval/this_month_claim_type3/<?php echo date('Y-m-01')?>/<?php echo date('Y-m-t');?>/ALL/ALL/ALL">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-wpforms" aria-hidden="true"></i></div>
                            <p>THIS MONTH'S CR. NOTE CLAIM</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                         <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(69);
                          if ($a > 0) {
                            $b=$DI->Dashboard_model->transporter_pending_payment_type_3();
                      ?>
                      <a href="<?php echo page_url; ?>Approval/pending_transporters_payment_type_3/<?php echo date('Y-m-01')?>/<?php echo date('Y-m-t');?>/ALL/ALL/ALL">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b;?></div>
                            <p>TRANSPORTERS PENDING PAYMENT</p>
                        </div>
                      </a>
                      <?php } 
                      ?>


                       <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(72);
                          if ($a > 0) {
                            
                      ?>
                      <a href="<?php echo page_url; ?>Approval/own_transport_report/<?php echo date('Y-m-01')?>/<?php echo date('Y-m-t');?>/ALL/ALL/ALL">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-file"></i></div>
                            <p>SUNDAR OIL OWN TRANSPORT REPORT FOR MONTH</p>
                        </div>
                      </a>
                      <?php } 
                      ?>

                      <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(73);
                          if ($a > 0) { ?>
                    <!--   <a href="<?php echo page_url; ?>Type_reporting/typethreestatistics/">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-wpforms" aria-hidden="true"></i></div>
                          <p>TYPE III STATISTICS REPORT</p>
                        </div>
                      </a> -->
                      <?php } 
                      ?>



                    </div>
                  </div>
                </div>
            <?php } ?>



             <?php
              $submodules = "'101','102','103'";
              $as = $DI->Dashboard_model->checkfforanymoduleassigned($submodules);
              if ($as > 0) {
              ?>
                <div class="col-sm-4">
                  <div class="dash__box">
                    <h3>APPROVAL BASED TRANSPORTATION</h3>
                     <span style="color:red;">This refers where We act as transport agent</span>
                    <hr>
                    <div class="row">
                      <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(101);
                          if ($a > 0) { ?>
                      <a href="<?php echo page_url; ?>Approval/transportation_approval">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><i class="fa fa-wpforms" aria-hidden="true"></i></div>
                          <p>ADD APPROVAL</p>
                        </div>
                      </a>
                      <?php } ?>


                        <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(102);
                          if ($a > 0) { 
                            $b = $DI->Dashboard_model->transportation_approval_count(); ?>
                      <a href="<?php echo page_url; ?>Approval/months_transport_based_claim/<?php echo date('Y-m-01')?>/<?php echo date('Y-m-t');?>/ALL/ALL/ALL">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b;?></div>
                          <p>THIS MONTH'S APPROVAL & CLAIM</p>
                        </div>
                      </a>
                      <?php } ?>

                        <?php
                     $a = $DI->Dashboard_model->checkForDashboardSubmodules(103);
                          if ($a > 0) { 
                            $b = $DI->Dashboard_model->transportation_pending_payment_count(); ?>
                      <a href="<?php echo page_url; ?>Approval/pending_transporter_payment/ALL">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b;?></div>
                          <p>PENDING TRANSPORTER(s) PAYMENT</p>
                        </div>
                      </a>
                      <?php } ?>

                     


                      





                    </div>
                  </div>
                </div>
            <?php } ?>


              </div>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="fixed-pos">
              <h3>
                <div class="row">
                  <div class="col-sm-6 col-6">
                    <i class="zmdi zmdi-notifications-none m-r-5" style="color:red;"></i>Notification
                  </div>
                  <div class="col-sm-6 col-6">
                    <div class="pull-right">
                    <?php

$module = $this->db->select('access')->from('module_access')->where('role_id', $user_id)->where('moduleid', '1')->get();

if ($module->num_rows() > 0) {

  foreach ($module->result() as $moddata);

  if ($moddata->access == '1') {



?><span class="btn btn-warning btn-xs text-center" data-toggle="modal" data-target="#con-close-modal1"><i class="fa fa-plus-circle" aria-hidden="true"></i></span><?php }
                                                                                                                                          } ?> <a href="<?php echo page_url; ?>Dashboard/view_all_events"><i class="fa fa-eye"></i></a>
                    </div>
                    
                  </div>
                </div>
              </h3>
              <hr>
              <ul class="list-group m-b-0 user-list" style="overflow-y:auto" id="notificationdata">



                <div id='loadingmessage2' style='display:none'>

                  <img src='https://media.giphy.com/media/3oEjI6SIIHBdRxXI40/giphy.gif' />

                </div>



              </ul>
            </div>
          </div>
        </div>
      </div>





      <div id="con-close-modal1" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <form id="loginForm" method="post" action="<?php echo page_url; ?>Dashboard/add_news_events" enctype="multipart/form-data">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">ADD NEWS/EVENTS</h4>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group"><label>Date</label><input type="date" name="event_date" id="event_date" value="<?php echo date('Y-m-d'); ?>" class="form-control"></div>
                    <div class="form-group"><label>Upload Image</label><input type="file" name="event_file" id="event_file" value="" class="form-control"></div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group"><label>News/Events</label><textarea class="form-control" name="news_events" required></textarea></div>
                  </div>
                </div>
              </div>
              <div class="modal-footer"><button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button><input type="submit" id="save" class="btn btn-info" value="Submit"></div>
            </div>
          </div>
        </form>
      </div><!-- /.modal -->

      <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <form id="loginForm" method="post" action="<?php echo page_url; ?>Dashboard/add_Quote" enctype="multipart/form-data">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">ADD QUOTE OF THE DAY</h4>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <textarea class="form-control" name="quote_of_the_day" required></textarea>
                    </div>
                  </div>
                </div>

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                <input type="submit" id="save" class="btn btn-info" value="Submit">
              </div>
            </div>
          </div>
        </form>
      </div><!-- /.modal -->

      <!-- Footer -->

      <?php $this->load->view('common/footer'); ?>

      <!-- End Footer -->



    </div>

    <!-- end container -->
  </div>

  </div> <!-- contentarea end -->

  <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>

  <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>

  <script src="<?php echo assets_url; ?>js/detect.js"></script>

  <script src="<?php echo assets_url; ?>js/fastclick.js"></script>



  <script src="<?php echo assets_url; ?>js/jquery.slimscroll.js"></script>

  <script src="<?php echo assets_url; ?>js/jquery.blockUI.js"></script>

  <script src="<?php echo assets_url; ?>js/waves.js"></script>

  <script src="<?php echo assets_url; ?>js/wow.min.js"></script>

  <script src="<?php echo assets_url; ?>js/jquery.nicescroll.js"></script>

  <script src="<?php echo assets_url; ?>js/jquery.scrollTo.min.js"></script>



  <!-- KNOB JS -->

  <!--[if IE]>

        <script type="text/javascript" src="<?php echo assets_url; ?>plugins/jquery-knob/excanvas.js"></script>

        <![endif]-->

  <script src="<?php echo assets_url; ?>plugins/jquery-knob/jquery.knob.js"></script>



  <!--Morris Chart-->

  <script src="<?php echo assets_url; ?>plugins/morris/morris.min.js"></script>

  <script src="<?php echo assets_url; ?>plugins/raphael/raphael-min.js"></script>



  <!-- Dashboard init -->

  <script src="<?php echo assets_url; ?>pages/jquery.dashboard.js"></script>



  <!-- App js -->

  <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>

  <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />





  <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
  <script>
    $('#loadingmessage2').show();
    $('#loadingmessage21').show();
    $.ajax({
      url: '<?php echo page_url; ?>Fetch_dynamic_data/notification_data/',
      type: 'get',
      success: function(data) {
        $('#notificationdata').html(data);
        $('#notificationdata1').html(data);
        $('#loadingmessage2').hide();
        $('#loadingmessage21').hide();
      }
    });



    $.ajax({
      url: '<?php echo page_url; ?>Fetch_dynamic_data/user_wise_approval',
      type: 'get',
      success: function(data) {
        $("#admin_approval").text(data);
      
      }
    });


     $.ajax({
      url: '<?php echo page_url; ?>Fetch_dynamic_data/approval_pending_from_admin',
      type: 'get',
      success: function(data) {
        $("#user_approval").text(data);
      
      }
    });

    var shownTicketNotifications = {};

    function fetchTicketNotifications() {
      $.ajax({
        url: "<?php echo page_url . 'Task/fetchNotifications'; ?>",
        method: "POST",
        cache: false,
        dataType: "json",
        success: function(data) {
          var notifications = Array.isArray(data) ? data : [];

          notifications.forEach(function(notification) {
            if (shownTicketNotifications[notification.id]) {
              return;
            }

            shownTicketNotifications[notification.id] = true;

            toastr.info(notification.message, "New Notification", {
              timeOut: 0,
              extendedTimeOut: 0,
              closeButton: true,
              tapToDismiss: false,
              positionClass: "toast-bottom-right",
              onHidden: function() {
                markTicketNotificationAsRead(notification.id);
              }
            });
          });
        }
      });
    }

    function markTicketNotificationAsRead(notification_id) {
      $.ajax({
        url: "<?php echo page_url . 'Task/markNotificationAsRead'; ?>",
        method: "POST",
        cache: false,
        data: {
          notification_id: notification_id
        }
      });
    }

    fetchTicketNotifications();
    setInterval(fetchTicketNotifications, 15000);

  </script>



</body>

</html>
