<?php
$CI = &get_instance();
$CI->load->model('Fms_model');
$UI = &get_instance();
$UI->load->model('Store_model');
$DI = &get_instance();
$DI->load->model('Dashboard_model');
$getAllLeadStages = $DI->Dashboard_model->getAllLeadStages();
$user_id = $_SESSION['logged_in']['user_id'];

$user_role = $this->session->userdata['logged_in']['role'];

$first_name = $this->session->userdata['logged_in']['user_name'];

$last_name = $this->session->userdata['logged_in']['last_name'];

$profile_image = $this->session->userdata['logged_in']['profile_image'];

$q = $this->db->select('profile_image')->from('system_users')->where('user_id', $user_id)->get();

if ($q->num_rows() > 0) {

  foreach ($q->result() as $row);

  $profilepic = $row->profile_image;
} else {

  $profilepic = "";
}

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
                <div class="col-sm-4">
                  <div class="dash__box">
                    <h3>Daily Task <span class="btn btn-xs" style="background-color:#2986CE; color:#fff">Take Action</span></h3>
                    <hr>
                    <div class="row">
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
                      <a href="<?php echo page_url; ?>Daily_reporting/reporting_dashboard">
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="again__circle" id="dailyworkreport"></div>
                          <p>VIEW DAILY REPORT</p>
                        </div>
                      </a>

                      <a href="<?php echo page_url; ?>Daily_reporting/reporting_dashboard">
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="again__circle" id="dailyworkreport">0</div>
                          <p>TASK DUE</p>
                        </div>
                      </a>
                    </div>
                    <div class="row">



                      <a href="<?php echo page_url; ?>Checklist/checklist_yesterday_report">
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="dailytasksection" id="dailyworkreport">0</div>
                          <p>YESTERDAY CHECKLIST REPORT</p>
                        </div>
                      </a>

                      <a href="<?php echo page_url; ?>Mom/dashboard/1/<?php echo $user_id; ?>" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="dailytasksection" id="todaysmom">
                            <?php
                            $q = $this->db->select('id')->from('mom')->where('mom_date', date('Y-m-d'))->get();
                            echo count($q->result());
                            ?>
                          </div>
                          <p>TODAYS MOM</p>
                        </div>
                      </a>
                      <a href="<?php echo page_url; ?>Mom/mom_assigned_to_you" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="dailytasksection" id="momassignedtoyou">
                            <?php
                            $q = $this->db->select('id')->from('mom_ponits_view')->where('responsible_person', $user_id)->where('workstatus', NULL)->get();
                            echo count($q->result());
                            ?>
                          </div>
                          <p>MOM ASSIGNED TO YOU </p>
                        </div>
                      </a>


                    </div>
                    <div class="row">
                      <a href="<?php echo page_url; ?>Mom/mom_assigned_to_you" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="dailytasksection" id="momassignedtoyou">
                            <i class="fa fa-wpforms"></i>
                          </div>
                          <p>ADD YOUR REPORT</p>
                        </div>
                      </a>
                      <a href="<?php echo page_url; ?>Delegation/delegated_task">
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="dailytasksection" id="delegateddata">
                            <?php
                            $q = $this->db->select('id')->from('delegation_task')->where('task_status', '0')->where('delegate_to', $user_id)->get();
                            $report = count($q->result());
                            echo $report;
                            ?>
                          </div>
                          <p>DELEGATED TASK TO YOU</p>
                        </div>
                      </a>
                      <a href="<?php echo page_url; ?>Leads/todaysfollowup" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="dailytasksection" id="momassignedtoyou">
                            <i class="fa fa-wpforms"></i>
                          </div>
                          <p>TODAY'S FOLLOWUP</p>
                        </div>
                      </a>
                      <a href="<?php echo page_url; ?>Leads/missed_followup_up" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="dailytasksection" id="momassignedtoyou">
                            <i class="fa fa-wpforms"></i>
                          </div>
                          <p>MISSED FOLLOWUP LEADS</p>
                        </div>
                      </a>
                      <a href="<?php echo page_url; ?>Leads/upcomeingfollow" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="dailytasksection" id="momassignedtoyou">
                            <i class="fa fa-wpforms"></i>
                          </div>
                          <p>UPCOMING FOLLOWUPS</p>
                        </div>
                      </a>
                    </div>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="dash__box">
                    <h3>Quotation</h3>
                    <hr>
                    <div class="row">
                      <?php
                      // $a = $DI->Dashboard_model->checkForDashboardSubmodules(50);
                      //         if ($a > 0) {
                      $b = $DI->Dashboard_model->discount_approval_count(); ?>
                      <a href="<?php echo page_url; ?>Customer/discount_approval">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>PENDING FOR APPROVAL</p>
                        </div>
                      </a>
                      <?php //} 
                      ?>

                      <?php
                      // $a = $DI->Dashboard_model->checkForDashboardSubmodules(50);
                      //         if ($a > 0) {
                      $b = $DI->Dashboard_model->quotations_expiring_today_count(); ?>
                      <a href="<?php echo page_url; ?>Customer/quotations_expiring_today">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="dailytasksection"><?php echo $b; ?></div>
                          <p>QUOTATIONS EXPIRING TODAY</p>
                        </div>
                      </a>
                      <?php //} 
                      ?>

                      <?php
                      // $a = $DI->Dashboard_model->checkForDashboardSubmodules(50);
                      //         if ($a > 0) {
                      $b = $DI->Dashboard_model->rejected_quotations_count(); ?>
                      <a href="<?php echo page_url; ?>Customer/rejected_quotations">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="dailytasksection"><?php echo $b; ?></div>
                          <p>REJECTED QUOTATIONS</p>
                        </div>
                      </a>
                      <?php //} 
                      ?>
                    </div>
                  </div>
                </div>
                <?php if (count($getAllLeadStages) > 0) {
                  $i = 2; ?>
                  <div class="col-sm-4">
                    <div class="dash__box">
                      <h3>Lead Stage Statistics</h3>
                      <hr>
                      <div class="row">
                        <?php $kl = 1;
                        foreach ($getAllLeadStages as $row1) {

                          $datacount = $DI->Dashboard_model->lead_stage_counts($row1->lead_id);


                        ?>

                          <a href="<?php echo page_url; ?>Leads/lead_stages/<?php echo $row1->lead_id; ?>">
                            <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                              <div class="again__circle"><?php echo $datacount; ?></div>
                              <p><?php echo $row1->lead_name; ?></p>
                            </div>
                          </a>
                          <?php if ($kl % 3 == 0) { ?>
                            <div style="clear: both;height:5px;"></div>
                          <?php } ?>
                        <?php $kl++;
                        } ?>
                      </div>
                    </div>
                  </div>
                <?php } ?>
                <div class="col-sm-4">
                  <div class="dash__box">
                    <h3>Order</h3>
                    <hr>
                    <div class="row">
                      <?php
                      // $a = $DI->Dashboard_model->checkForDashboardSubmodules(50);
                      //         if ($a > 0) {
                      $b = $DI->Dashboard_model->pending_for_order_punch_count(); ?>
                      <a href="<?php echo page_url; ?>Leads/pending_for_order_punching">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle"><?php echo $b; ?></div>
                          <p>PENDING FOR ORDER PUNCHING</p>
                        </div>
                      </a>
                      <?php //} 
                      ?>

                      <?php
                      // $a = $DI->Dashboard_model->checkForDashboardSubmodules(50);
                      //         if ($a > 0) {
                      $b = $DI->Dashboard_model->quotations_expiring_today_count(); ?>
                      <a href="<?php echo page_url; ?>Customer/all_orders">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="dailytasksection"><?php echo $b; ?></div>
                          <p>ALL ORDERS</p>
                        </div>
                      </a>
                      <?php //} 
                      ?>
                    </div>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="dash__box">
                    <h3>Local/Tour Conveyance</h3>
                    <hr>
                    <div class="row">
                      <a href="<?php echo page_url; ?>Sales/conveyance_voucher">
                        <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                          <div class="again__circle">
                            <i class="fa fa-wpforms" aria-hidden="true"></i>
                          </div>
                          <p>LOCAL CONVEYANCE FORM</p>
                        </div>
                      </a>


                      <a href="<?php echo page_url; ?>Sales/local_conveyance_dashboard">
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="dailytasksection" id="dailyworkreport"><i class="fa fa-dashboard" aria-hidden="true"></i></div>
                          <p>YOUR LOCAL CONVEYANCE DASHBOARD</p>
                        </div>
                      </a>

                      <a href="<?php echo page_url; ?>Sales/local_conveyance_data/">
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="dailytasksection" id="dailyworkreport"> <?php
                                                                              $q = $this->db->select('id')->from('conveyance_voucher')->where('hod_status', 0)->where('account_status', 0)->get();
                                                                              echo count($q->result());

                                                                              ?></div>
                          <p>HOD LOCAL CONVEYANCE DASHBOARD</p>
                        </div>
                      </a>
                    </div>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="dash__box">
                    <h3>Attendance/Leaves</h3>
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

                      <a href="<?php echo page_url; ?>Hr/hod_leave_dashboard">
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="dailytasksection" id="dailyworkreport"><?php
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
                                                                              } else {
                                                                              ?>0
                          <?php } ?></div>
                          <p>LEAVE HOD DASHBOARD</p>
                        </div>
                      </a>

                      <a href="<?php echo page_url; ?>Hr/onleavetoday">
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="dailytasksection" id="dailyworkreport">0</div>
                          <p>ON LEAVE TODAY DASHBOARD</p>
                        </div>
                      </a>
                    </div>
                    <div class="row">

                      <a href="<?php echo page_url; ?>Master/User_management/mark_your_attendance" target="_blank">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                          <div class="dailytasksection" id="todaysmom">
                            0
                          </div>
                          <p>MARK YOUR ATTENDANCE</p>
                        </div>
                      </a>

                      <a href="<?php echo page_url; ?>Master/User_management/attendance_dashboard">
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="dailytasksection" id="dailyworkreport"></div>
                          <p>ATTENDANCE REPORT</p>
                        </div>
                      </a>
                      <a href="<?php echo page_url; ?>Hr/attendance_report/">
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="dailytasksection" id="dailyworkreport"></div>
                          <p>EMPLOYEE ATTENDANCE MONTHLY REPORT</p>
                        </div>
                      </a>

                    </div>
                  </div>
                </div>
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
              <ul class="list-group m-b-0 user-list" style="height: 170px; overflow-y:auto" id="notificationdata">



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
  </script>


</body>

</html>