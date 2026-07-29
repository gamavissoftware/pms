<?php
defined('BASEPATH') or exit('No direct script access allowed');

$DI = &get_instance();
$DI->load->model('Dashboard_model');

// Fetch Service specific data 
$service_stages = $DI->Dashboard_model->getAllServiceLeadStages();
$today_followup = $DI->Dashboard_model->getServiceFollowupCounts(1);
$missed_followup = $DI->Dashboard_model->getServiceFollowupCounts(2);
$upcoming_followup = $DI->Dashboard_model->getServiceFollowupCounts(3);
$payment_permissions = isset($payment_permissions) && is_array($payment_permissions) ? $payment_permissions : array();
$can_create_service_payment = !empty($payment_permissions['can_create']);
$can_approve_service_payment = !empty($payment_permissions['can_approve']);

if (!function_exists('get_service_status_color')) {
    function get_service_status_color($name) {
        $name = strtolower($name);
        if (strpos($name, 'won') !== false) return 'kpi-status-won';
        if (strpos($name, 'rejected') !== false) return 'kpi-status-lost';
        if (strpos($name, 'approval') !== false) return 'kpi-status-progress';
        return 'kpi-status-active';
    }
}

if (!function_exists('get_service_icon')) {
    function get_service_icon($name) {
        $name = strtolower($name);
        if (strpos($name, 'new') !== false) return 'fa-star';
        if (strpos($name, 'quotation') !== false) return 'fa-file-text-o';
        if (strpos($name, 'approval') !== false) return 'fa-check-square-o';
        if (strpos($name, 'won') !== false) return 'fa-trophy';
        return 'fa-tasks';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; Service Analytics Dashboard</title>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />

    <style>
        :root { --primary: #4a81d4; --success: #28a745; --warning: #f1b53d; --danger: #f05050; }
        body { background-color: #f4f7fa; }
        .wrapper { padding-top: 20px; }
        .modern-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 20px; margin-bottom: 25px; border: 1px solid #eef2f7; }
        .dashboard-title { font-weight: 700; color: #333; margin-bottom: 20px; border-left: 5px solid var(--primary); padding-left: 15px; }
        
        .kpi-card-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; }
        .kpi-card { padding: 12px; border-radius: 10px; transition: 0.3s; position: relative; overflow: hidden; text-decoration: none !important; border: 1px solid rgba(0,0,0,0.03); }
        .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .kpi-card i { position: absolute; right: -5px; bottom: -5px; font-size: 35px; opacity: 0.1; }
        .kpi-card .stats-value { font-size: 24px; font-weight: 700; display: block; margin-top: 5px; }
        
        .kpi-status-won { background: #d4f8e8; color: #146c43; }
        .kpi-status-active { background: #e9f2ff; color: #0056b3; }
        .kpi-status-progress { background: #fff4e6; color: #b86e00; }
        .kpi-status-lost { background: #ffe9e9; color: #a94442; }

        .sidebar-kpi { display: flex; justify-content: space-between; align-items: center; padding: 12px; margin-bottom: 10px; background: #fff; border-left: 4px solid var(--primary); border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); text-decoration: none !important; }
        .chart-container { position: relative; height: 280px; width: 100%; }
        
        /* Dashboard Calendar Small Version */
        #dashCalendar { font-size: 11px; max-height: 500px; }
        .fc-toolbar-title { font-size: 14px !important; font-weight: 600; }
        .fc-button { padding: 2px 5px !important; font-size: 11px !important; }
        
        .op-link { display: block; padding: 10px 0; border-bottom: 1px solid #f1f4f8; color: #444; font-weight: 500; transition: 0.2s; text-decoration: none !important; }
        .op-link:hover { color: var(--primary); padding-left: 5px; }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
    
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <h3 class="dashboard-title">Service Pipeline & Field Operations</h3>
                <div class="col-lg-9">
                    <div class="modern-card">
                        <div style="display:flex; justify-content: space-between; margin-bottom: 15px;">
                            <h5 style="font-weight: 700; margin: 0;"><i class="fa fa-pie-chart text-primary"></i> Stage Distribution</h5>
                            <div>
                                <a href="<?php echo page_url; ?>ServiceLeads/opportunity_list" class="btn btn-default btn-xs"><i class="fa fa-list"></i> All Opportunities</a>
                                <a href="<?php echo page_url; ?>ServiceLeads/create" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> New Lead</a>
                            </div>
                        </div>
                        <div class="kpi-card-grid">
                            <?php foreach ($service_stages as $stage) : 
                                $count = $DI->Dashboard_model->getServiceStageCounts($stage->stage_id);
                                $color_class = get_service_status_color($stage->stage_name);
                                $icon = get_service_icon($stage->stage_name);
                            ?>
                                <a href="<?php echo page_url; ?>ServiceLeads/opportunity_list/<?php echo $stage->stage_id; ?>" class="kpi-card <?php echo $color_class; ?>">
                                    <small style="font-weight: 600; text-transform: uppercase;"><?php echo $stage->stage_name; ?></small>
                                    <span class="stats-value"><?php echo $count; ?></span>
                                    <i class="fa <?php echo $icon; ?>"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="modern-card">
                                <h5 style="font-weight: 700;"><i class="fa fa-bar-chart text-primary"></i> Engineer Earning (WON)</h5>
                                <div class="chart-container">
                                    <canvas id="engineerBarChart"></canvas>
                                </div>
                            </div>
                           
                        </div>
                        <div class="col-md-6">
                             <div class="modern-card">
                                <h5 style="font-weight: 700;"><i class="fa fa-globe text-warning"></i> Business Split</h5>
                                <div class="chart-container" style="height: 280px;">
                                    <canvas id="businessPieChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="modern-card">
                        <h5 style="font-weight: 700; margin-bottom: 20px;"><i class="fa fa-bell-o text-danger"></i> Action Center</h5>
                        
                        <a href="<?php echo page_url; ?>ServiceLeads/opportunity_list?filter=today" class="sidebar-kpi" style="border-left-color: var(--warning);">
                            <span class="text-muted">Today's Followups</span>
                            <span class="badge badge-warning"><?php echo $today_followup; ?></span>
                        </a>
                        <a href="<?php echo page_url; ?>ServiceLeads/opportunity_list?filter=missed" class="sidebar-kpi" style="border-left-color: var(--danger);">
                            <span class="text-muted">Missed Followups</span>
                            <span class="badge badge-danger"><?php echo $missed_followup; ?></span>
                        </a>
                        <a href="<?php echo page_url; ?>ServiceLeads/opportunity_list?filter=upcoming" class="sidebar-kpi" style="border-left-color: var(--primary);">
                            <span class="text-muted">Upcoming Tasks</span>
                            <span class="badge badge-primary"><?php echo $upcoming_followup; ?></span>
                        </a>
                    </div>

                    <div class="modern-card">
                        <h5 style="font-weight: 700; margin-bottom: 15px;"><i class="fa fa-bolt text-primary"></i> Operations</h5>
                        <div class="list-group list-group-flush">
                            <?php $user_id = $this->session->userdata['logged_in']['user_id']; ?>
                            <a href="<?php echo page_url; ?>ServiceLeads/engineer_scheduler" class="op-link">
                                <i class="fa fa-calendar-plus-o text-success m-r-10"></i> Schedule site Visit
                            </a>
                            <?php if ($can_create_service_payment): ?>
                                <a href="<?php echo page_url; ?>ServiceLeads/service_payment_request_form" class="op-link">
                                    <i class="fa fa-credit-card text-warning m-r-10"></i> Raise Payment Request
                                </a>
                                <a href="<?php echo page_url; ?>ServiceLeads/service_payment_requests" class="op-link">
                                    <i class="fa fa-list-alt text-primary m-r-10"></i> Payment Request Register
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo page_url; ?>ServiceLeads/order_report" class="op-link">
                                <i class="fa fa-file-text-o text-primary m-r-10"></i> Revenue Report
                            </a>
                            <?php if ($can_approve_service_payment): ?>
                                <a href="<?php echo page_url; ?>ServiceLeads/service_payment_approvals" class="op-link">
                                    <i class="fa fa-check-square-o text-success m-r-10"></i> Payment Approval Queue
                                </a>
                            <?php endif; ?>
                            <?php
                                $service_visit_overview_submodule = $this->db->select('id')
                                    ->from('submodule')
                                    ->where('moduleid', '17')
                                    ->where('submodule', 'SERVICE ENGINEER VISIT ASSIGNMENT OVERVIEW')
                                    ->limit(1)
                                    ->get()
                                    ->row();
                                $service_visit_overview_visible = false;
                                if (!empty($service_visit_overview_submodule)) {
                                    $service_overview_access = $this->db->get_where('module_capablity', ['role_id' => $user_id, 'moduleid' => '17', 'submoduleid' => (int) $service_visit_overview_submodule->id, 'submodule_access' => '1']);
                                    if ($service_overview_access->num_rows() > 0) {
                                        $service_visit_overview_visible = true;
                                    }
                                }
                                if (!$service_visit_overview_visible && !empty($_SESSION['logged_in']['adminuser']) && (int) $_SESSION['logged_in']['adminuser'] === 1) {
                                    $service_visit_overview_visible = true;
                                }
                                if ($service_visit_overview_visible) { ?>
                                    <a href="<?php echo page_url; ?>ServiceLeads/engineer_assignment_overview" class="op-link">
                                        <i class="fa fa-list-ul text-info m-r-10"></i> Engineer Visit Assignments
                                    </a>
                            <?php } ?>
                            <?php 
                                $qry = $this->db->get_where('module_capablity', ['role_id' => $user_id, 'moduleid' => '17', 'submoduleid' => '63', 'submodule_access' => '1']);
                                if ($qry->num_rows() > 0) { ?>
                                    <a href="<?php echo page_url; ?>ServiceMaster" class="op-link">
                                        <i class="fa fa-gears text-muted m-r-10"></i> Service Masters
                                    </a>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="modern-card" style="background: #2b3d51; color: #fff; text-align: center;">
                        <small style="opacity: 0.7;">TOTAL WINS THIS PERIOD</small>
                        <h1 style="font-weight: 700; margin: 10px 0;"><?php echo $DI->Dashboard_model->getServiceStageCounts(7); ?></h1>
                        <a href="<?php echo page_url; ?>ServiceLeads/order_report" class="btn btn-warning btn-sm btn-block">View Revenue</a>
                    </div>
                </div>

                <div class="col-md-12">
                            <div class="modern-card">
                                <h5 style="font-weight: 700; margin-bottom: 15px;"><i class="fa fa-calendar-check-o text-success"></i> Field Deployment Calendar</h5>
                                <div id="dashCalendar"></div>
                            </div>
                        </div>
            </div>
        </div>
    </div>
    
    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

    <script>
    $(document).ready(function() {
        // 1. Initialize Dashboard Calendar
        var calendarEl = document.getElementById('dashCalendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: 480,
            headerToolbar: { left: 'prev,next', center: 'title', right: 'today' },
            events: "<?php echo page_url; ?>ServiceLeads/get_scheduled_events",
            eventClick: function(info) {
                alert('Visit: ' + info.event.title);
            }
        });
        calendar.render();

        // 2. Engineer Earning Bar Chart
        const barCtx = document.getElementById('engineerBarChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($engineer_earnings ?? array(), 'label')); ?>,
                datasets: [{
                    label: 'Revenue Won',
                    data: <?php echo json_encode(array_column($engineer_earnings ?? array(), 'value')); ?>,
                    backgroundColor: '#4a81d4',
                    borderRadius: 4
                }]
            },
            options: { maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });

        // 3. Business Split Pie Chart
        const pieCtx = document.getElementById('businessPieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Domestic', 'International'],
                datasets: [{
                    data: [
                        <?php echo $business_split->domestic_count ?? 0; ?>, 
                        <?php echo $business_split->international_count ?? 0; ?>
                    ],
                    backgroundColor: ['#28a745', '#f1b53d'],
                    borderWidth: 0
                }]
            },
            options: { maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'bottom' } } }
        });
    });
    </script>
</body>
</html>
