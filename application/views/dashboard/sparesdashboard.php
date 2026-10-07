<?php
defined('BASEPATH') or exit('No direct script access allowed');

// --- Data Fetching (Organized at the top) ---
$user_id = $this->session->userdata['logged_in']['user_id'];
$DI = &get_instance();
$DI->load->model('Dashboard_model');

$getAllLeadStages = $DI->Dashboard_model->getAllspareLeadStagesofleads();
$getAllLeadStagesforquotation = $DI->Dashboard_model->getAllspareLeadStagesofquotation();

$running_orders_count = $DI->Dashboard_model->get_running_orders_count();
$recent_running_orders = $DI->Dashboard_model->get_recent_running_orders(5);

// Pre-fetch user followup data for admins
$followup_users_data = [];
if ($_SESSION['logged_in']['user_id'] == 139 || $_SESSION['logged_in']['user_id'] == 61 || $_SESSION['logged_in']['user_id'] == 161 || $_SESSION['logged_in']['user_id'] == 114 || $_SESSION['logged_in']['user_id'] == 189 || $_SESSION['logged_in']['user_id'] == 111) {
    $this->db->select('user_id, title, first_name, last_name')->from('system_users')->where('department_id', 31)->where('business_location', 2)->where('user_status', 1)->order_by('first_name');
    $followup_users_data = $this->db->get()->result();
}

// Fetch Chart Data for Admins/Managers
$sales_agent_data = [];
$top_products_data = [];
if (pms_is_super_admin() || $this->session->userdata['logged_in']['user_id'] == 139 || $_SESSION['logged_in']['user_id'] == 114 || $_SESSION['logged_in']['user_id'] == 189 || $_SESSION['logged_in']['user_id'] == 111) {
    $sales_agent_data = $DI->Dashboard_model->get_spares_sales_by_agent();
    $top_products_data = $DI->Dashboard_model->get_spares_top_products_by_value();
}

// Helper function to assign meaningful colors to opportunity stages
function get_status_color_class($lead_name) {
    $name = strtolower($lead_name);
    if (strpos($name, 'won') !== false || strpos($name, 'closed') !== false || strpos($name, 'po received') !== false) {
        return 'kpi-status-won';
    }
    if (strpos($name, 'lost') !== false || strpos($name, 'dropped') !== false || strpos($name, 'cancelled') !== false) {
        return 'kpi-status-lost';
    }
    if (strpos($name, 'negotiation') !== false || strpos($name, 'proposal') !== false || strpos($name, 'discussion') !== false) {
        return 'kpi-status-progress';
    }
    if (strpos($name, 'new') !== false || strpos($name, 'qualified') !== false || strpos($name, 'open') !== false) {
        return 'kpi-status-active';
    }
    return 'kpi-status-default';
}

// Helper function to assign icons to stages
function get_stage_icon($stage_name) {
    $name = strtolower($stage_name);
    if (strpos($name, 'new') !== false) return 'fa-star';
    if (strpos($name, 'cancelled') !== false) return 'fa-ban';
    if (strpos($name, 'quotation') !== false) return 'fa-file-text-o';
    if (strpos($name, 'negotiation') !== false) return 'fa-comments-o';
    if (strpos($name, 'won') !== false || strpos($name, 'po') !== false) return 'fa-trophy';
    if (strpos($name, 'lost') !== false) return 'fa-thumbs-o-down';
    if (strpos($name, 'follow') !== false) return 'fa-calendar-check-o';
    return 'fa-tasks';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; Spares Dashboard</title>
    
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>

    <style>
        :root {
            --primary-color: #4a81d4;
            --secondary-color: #f4f5f7;
            --text-color: #333;
            --text-color-light: #6c757d;
            --border-color: #e5e9f2;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --border-radius: 8px;
        }

        body { background-color: var(--secondary-color); }
        .wrapper .container-fluid { padding: 15px 25px; }

        .page-header { margin-bottom: 25px; padding-bottom: 10px; border-bottom: 1px solid var(--border-color); }
        .page-title { font-size: 28px; font-weight: 700; color: var(--text-color); }
        .section-heading { font-size: 18px; font-weight: 600; color: var(--text-color); margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; }
        
        .modern-card { background-color: #fff; border: 1px solid var(--border-color); border-radius: var(--border-radius); box-shadow: var(--shadow); padding: 20px; margin-bottom: 25px; height: 100%; display: flex; flex-direction: column; }
        
        .kpi-card-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 15px; }
        
        .kpi-card { padding: 15px; border-radius: var(--border-radius); border-width: 1px; border-style: solid; transition: all 0.3s ease; text-decoration: none !important; display: flex; flex-direction: column; justify-content: space-between; min-height: 110px; }
        .kpi-card:hover { transform: translateY(-5px); box-shadow: 0 8px 16px rgba(0,0,0,0.1); }
        .kpi-card .card-top { display: flex; justify-content: space-between; align-items: flex-start; }
        .kpi-card p { font-size: 13px; font-weight: 600; line-height: 1.4; margin: 0; }
        .kpi-card .stats-value { font-size: 28px; font-weight: 700; align-self: flex-end; }
        .kpi-card .card-icon { font-size: 18px; opacity: 0.5; }

        .kpi-status-won { background-color: #e4f9f1; border-color: #a3e9d2; }
        .kpi-status-won .stats-value, .kpi-status-won p, .kpi-status-won .card-icon { color: #146c43; }
        .kpi-status-lost { background-color: #fdeeee; border-color: #f7c5c5; }
        .kpi-status-lost .stats-value, .kpi-status-lost p, .kpi-status-lost .card-icon { color: #a30000; }
        .kpi-status-active { background-color: #e9f2ff; border-color: #a6caff; }
        .kpi-status-active .stats-value, .kpi-status-active p, .kpi-status-active .card-icon { color: #0056b3; }
        .kpi-status-progress { background-color: #fff4e6; border-color: #ffdcb3; }
        .kpi-status-progress .stats-value, .kpi-status-progress p, .kpi-status-progress .card-icon { color: #b86e00; }
        .kpi-status-default { background-color: #f8f9fa; border-color: #dee2e6; }
        .kpi-status-default .stats-value, .kpi-status-default p, .kpi-status-default .card-icon { color: #495057; }

        .sidebar-kpi-stack .kpi-card { display: flex; flex-direction: row; min-height: auto; justify-content: space-between; align-items: center; padding: 12px 15px; margin-bottom: 15px; background-color: #fff; border: 1px solid var(--border-color); }
        .sidebar-kpi-stack .kpi-card:last-child { margin-bottom: 0; }
        .sidebar-kpi-stack .kpi-card p { color: var(--text-color-light); font-weight: 600; }
        .sidebar-kpi-stack .kpi-card .stats-value { font-size: 20px; color: var(--text-color); }
        .kpi-card-missed { border-left: 4px solid #f05050; }
        .kpi-card-today { border-left: 4px solid #ffbd4a; }
        .kpi-card-upcoming { border-left: 4px solid #4a81d4; }
        .kpi-card-running { border-left: 4px solid #1abc9c; }

        .quick-links-list { list-style: none; padding: 0; margin: 0; }
        .quick-links-list li a { display: flex; align-items: center; padding: 12px 0; text-decoration: none; color: var(--text-color); font-weight: 500; border-bottom: 1px solid var(--border-color); }
        .quick-links-list li:last-child a { border-bottom: none; }
        .quick-links-list li a:hover { color: var(--primary-color); }
        .quick-links-list li a .fa { margin-right: 15px; width: 20px; text-align: center; color: var(--text-color-light); }

        .chart-container { flex-grow: 1; min-height: 320px; }
        
        .team-followup-list { padding: 0; margin: 0; list-style: none; max-height: 350px; overflow-y: auto; }
        .user-followup-item { display: flex; align-items: center; justify-content: space-between; padding: 15px 5px; border-bottom: 1px solid var(--border-color); }
        .user-followup-item:last-child { border-bottom: none; }
        .user-info { font-weight: 600; color: var(--text-color); }
        .user-stats { display: flex; gap: 15px; }
        .stat-box a { text-decoration: none; text-align: center; }
        .stat-box .stat-label { font-size: 11px; text-transform: uppercase; color: var(--text-color-light); }
        .stat-box .stat-count { font-size: 18px; font-weight: 700; display: block; }
        .stat-box .stat-count-missed { color: #dc3545; }
        .stat-box .stat-count-today { color: #ffc107; }

        .recent-orders-list { list-style: none; padding: 0; margin: 0; max-height: 350px; overflow-y: auto; }
        .order-item { display: flex; align-items: center; justify-content: space-between; padding: 15px 5px; border-bottom: 1px solid var(--border-color); }
        .order-item:last-child { border-bottom: none; }
        .order-info .order-so { font-weight: 600; color: var(--text-color); display: block; }
        .order-info .order-customer { font-size: 13px; color: var(--text-color-light); }
    </style>
</head>

<body>
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>
    
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header">
                        <h1 class="page-title text-center">Spares (Trading) Dashboard</h1>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-9">
                    <div class="modern-card">
                        <h5 class="section-heading">
                            <span>Opportunities Statistics</span>
                            <a href='<?php echo page_url; ?>Spares/leadform' class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> New Opportunity</a>
                        </h5>
                        <div class="kpi-card-grid">
                            <?php foreach ($getAllLeadStages as $row1) :
                                $datacount = $DI->Dashboard_model->spares_lead_stage_counts($row1->lead_id);
                                $status_class = get_status_color_class($row1->lead_name);
                                $icon_class = get_stage_icon($row1->lead_name);
                                ?>
                                <a href="<?php echo page_url; ?>Spares/opportunity_stage_wise/<?php echo $row1->lead_id; ?>" class="kpi-card <?php echo $status_class; ?>">
                                    <div class="card-top">
                                        <p><?php echo $row1->lead_name; ?></p>
                                        <i class="fa <?php echo $icon_class; ?> card-icon"></i>
                                    </div>
                                    <div class="stats-value"><?php echo $datacount; ?></div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="modern-card">
                        <h5 class="section-heading">Recent Running Orders</h5>
                         <ul class="recent-orders-list">
                            <?php if (!empty($recent_running_orders)): ?>
                                <?php foreach ($recent_running_orders as $order): ?>
                                    <li class="order-item">
                                        <div class="order-info">
                                            <span class="order-so">SO-<?php echo $order->order_id; ?></span>
                                            <span class="order-customer"><?php echo htmlspecialchars($order->company_name); ?></span>
                                        </div>
                                        <div>
                                            <a href="<?php echo page_url; ?>Spares_execution/order/<?php echo $order->order_id; ?>" class="btn btn-sm btn-primary">Execution Tasks</a>
                                            <a href="<?php echo page_url; ?>Spares/order_detail/<?php echo $order->order_id; ?>" class="btn btn-sm btn-success">Order Detail</a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>No recent running orders found.</li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <?php if (!empty($followup_users_data)) : ?>
                    <div class="modern-card">
                        <h5 class="section-heading">Team Followup Details</h5>
                        <ul class="team-followup-list">
                            <?php foreach ($followup_users_data as $row):
                                $missed_count = $this->Dashboard_model->SparesFollowupCountsUsers(2, $row->user_id);
                                $today_count = $this->Dashboard_model->SparesFollowupCountsUsers(1, $row->user_id);
                            ?>
                                <li class="user-followup-item">
                                    <div class="user-info"><?php echo ucwords(strtolower($row->title . " " . $row->first_name)); ?></div>
                                    <div class="user-stats">
                                        <div class="stat-box">
                                            <a href='<?php echo page_url; ?>Spares/followup_list/2/<?php echo $row->user_id; ?>'>
                                                <span class="stat-count stat-count-missed"><?php echo $missed_count; ?></span>
                                                <span class="stat-label">Missed</span>
                                            </a>
                                        </div>
                                        <div class="stat-box">
                                            <a href='<?php echo page_url; ?>Spares/followup_list/1/<?php echo $row->user_id; ?>'>
                                                <span class="stat-count stat-count-today"><?php echo $today_count; ?></span>
                                                <span class="stat-label">Today's</span>
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-3">
                    <div class="modern-card">
                        <h5 class="section-heading">Action Center</h5>
                        <div class="sidebar-kpi-stack">
                            <a href="<?php echo page_url; ?>Spares/followup_list/1" class="kpi-card kpi-card-today">
                                <p>Today's Followups</p>
                                <div class="stats-value"><?php echo $DI->Dashboard_model->SparesFollowupCounts(1); ?></div>
                            </a>
                            <a href="<?php echo page_url; ?>Spares/followup_list/2" class="kpi-card kpi-card-missed">
                                <p>Missed Followups</p>
                                <div class="stats-value"><?php echo $DI->Dashboard_model->SparesFollowupCounts(2); ?></div>
                            </a>
                            <a href="<?php echo page_url; ?>Spares/followup_list/3" class="kpi-card kpi-card-upcoming">
                                <p>Upcoming Followups</p>
                                <div class="stats-value"><?php echo $DI->Dashboard_model->SparesFollowupCounts(3); ?></div>
                            </a>
                            <a href="<?php echo page_url; ?>Spares/running_orders_list" class="kpi-card kpi-card-running">
                                <p>Running Orders</p>
                                <div class="stats-value"><?php echo $running_orders_count; ?></div>
                            </a>
                        </div>
                    </div>
                    
                    <div class="modern-card">
                        <h5 class="section-heading">Quick Links</h5>
                        <ul class="quick-links-list">
                            <li><a href="<?php echo page_url; ?>Spares/leadform"><i class="fa fa-plus-circle"></i> Add New Opportunity</a></li>
                            <li><a href="<?php echo page_url; ?>Spares/opportunity_list"><i class="fa fa-list-alt"></i> All Opportunities</a></li>
                            <li><a href="<?php echo page_url; ?>Spares/running_orders_list"><i class="fa fa-cogs"></i> All Running Orders</a></li>
                            <li><a href="<?php echo page_url; ?>Spares_execution/dashboard"><i class="fa fa-tasks"></i> Execution Dashboard</a></li>
                        </ul>
                    </div>
                </div>
                </div>
            
            <?php if (pms_is_super_admin() || $this->session->userdata['logged_in']['user_id'] == 139 || $this->session->userdata['logged_in']['user_id'] == 114 || $this->session->userdata['logged_in']['user_id'] == 189) : ?>
            <div class="row">
                <div class="col-lg-7">
                    <div class="modern-card">
                        <h5 class="section-heading">Sales Agent Performance (Spares)</h5>
                        <div class="chart-container">
                            <canvas id="salesBarChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="modern-card">
                        <h5 class="section-heading">Top 5 Spares by Value</h5>
                        <div class="chart-container">
                            <canvas id="topProductsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php $this->load->view('common/footer'); ?>
        </div> </div> <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <?php if (pms_is_super_admin() || $this->session->userdata['logged_in']['user_id'] == 139 || $this->session->userdata['logged_in']['user_id'] == 114 || $this->session->userdata['logged_in']['user_id'] == 189) : ?>
        <script>
            $(document).ready(function() {
                // --- Sales Agent Performance Chart ---
                const salesAgentData = <?php echo json_encode($sales_agent_data); ?>;
                if (salesAgentData && salesAgentData.length > 0) {
                    const salesCtx = document.getElementById('salesBarChart').getContext('2d');
                    new Chart(salesCtx, {
                        type: 'bar',
                        data: {
                            labels: salesAgentData.map(d => d.agent_name),
                            datasets: [{
                                label: 'Total Sales Value',
                                data: salesAgentData.map(d => d.total_sales),
                                backgroundColor: 'rgba(74, 129, 212, 0.7)',
                                borderColor: 'rgba(74, 129, 212, 1)',
                                borderWidth: 1,
                                borderRadius: 5
                            }]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: { 
                                y: { beginAtZero: true, ticks: { callback: value => '₹' + new Intl.NumberFormat('en-IN', { notation: 'compact', compactDisplay: 'short' }).format(value) } },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                }

                // --- Top Products Chart ---
                const topProductsData = <?php echo json_encode($top_products_data); ?>;
                 if (topProductsData && topProductsData.length > 0) {
                    const productsCtx = document.getElementById('topProductsChart').getContext('2d');
                    new Chart(productsCtx, {
                        type: 'bar',
                        data: {
                            labels: topProductsData.map(p => p.product_name),
                            datasets: [{
                                label: 'Total Sales Value',
                                data: topProductsData.map(p => p.total_product_value),
                                backgroundColor: ['rgba(26, 188, 156, 0.7)', 'rgba(52, 152, 219, 0.7)', 'rgba(243, 156, 18, 0.7)', 'rgba(155, 89, 182, 0.7)', 'rgba(52, 73, 94, 0.7)'],
                                borderRadius: 5
                            }]
                        },
                        options: {
                            indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                x: { beginAtZero: true, ticks: { callback: value => '₹' + new Intl.NumberFormat('en-IN', { notation: 'compact', compactDisplay: 'short' }).format(value) } },
                                y: { grid: { display: false } }
                            }
                        }
                    });
                }
            });
        </script>
    <?php endif; ?>
</body>
</html>
