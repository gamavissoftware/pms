<?php
$user_id = $this->session->userdata['logged_in']['user_id'];
$DI = &get_instance();
$DI->load->model('Dashboard_model');
$getAllLeadStages = $DI->Dashboard_model->getAllLeadStagesofleads();
$getAllLeadStagesforquotation = $DI->Dashboard_model->getAllLeadStagesofquotation();

// Data for charts - assuming these are loaded by your controller and passed to the view
// Example: $sales_data = $DI->Dashboard_model->get_sales_performance_data();
// Example: $order_data_by_brand = $DI->Dashboard_model->get_top_brand_data();

// Helper function to assign meaningful colors to opportunity stages
function get_status_color_class($lead_name) {
    $name = strtolower($lead_name);
    if (strpos($name, 'won') !== false || strpos($name, 'closed') !== false) {
        return 'kpi-status-won';
    }
    if (strpos($name, 'lost') !== false || strpos($name, 'dropped') !== false) {
        return 'kpi-status-lost';
    }
    if (strpos($name, 'negotiation') !== false || strpos($name, 'proposal') !== false || strpos($name, 'discussion') !== false) {
        return 'kpi-status-progress';
    }
    if (strpos($name, 'new') !== false || strpos($name, 'qualified') !== false || strpos($name, 'open') !== false) {
        return 'kpi-status-active';
    }
    return 'kpi-status-default'; // A neutral default color
}

$show_order_analytics = (pms_is_super_admin() || in_array((int) $this->session->userdata['logged_in']['user_id'], array(139, 161), true));
$order_summary = isset($order_analytics_summary) && is_array($order_analytics_summary) ? $order_analytics_summary : array();
$order_summary_defaults = array(
    'total_orders' => 0,
    'total_order_value' => 0,
    'avg_order_value' => 0,
    'active_agents' => 0,
    'active_brands' => 0,
    'latest_order_date' => '',
    'top_agent_name' => 'No sales data',
    'top_agent_value' => 0,
    'top_brand_name' => 'No brand data',
    'top_brand_value' => 0,
);
$order_summary = array_merge($order_summary_defaults, $order_summary);
$marketing_df_summary = isset($marketing_df_income) && is_array($marketing_df_income) ? $marketing_df_income : array();
$spares_summary = isset($spares_order_summary) && is_array($spares_order_summary) ? array_merge(array(
    'total_orders' => 0, 'total_order_value' => 0, 'avg_order_value' => 0, 'active_agents' => 0, 'latest_order_date' => ''
), $spares_order_summary) : array('total_orders' => 0, 'total_order_value' => 0, 'avg_order_value' => 0, 'active_agents' => 0, 'latest_order_date' => '');
$spares_breakdown = isset($spares_income_breakdown) && is_array($spares_income_breakdown) ? $spares_income_breakdown : array();
$service_breakdown = isset($service_income_breakdown) && is_array($service_income_breakdown) ? $service_income_breakdown : array();
$service_summaries = array();
foreach ((isset($service_order_summary) && is_array($service_order_summary)) ? $service_order_summary : array() as $service_summary_row) {
    $service_currency = strtoupper(trim((string) $service_summary_row['currency']));
    $service_summaries[$service_currency !== '' ? $service_currency : 'INR'] = $service_summary_row;
}
$service_inr_summary = isset($service_summaries['INR']) ? $service_summaries['INR'] : array('total_orders' => 0, 'total_order_value' => 0, 'avg_order_value' => 0, 'active_agents' => 0, 'latest_order_date' => '');
$service_usd_summary = isset($service_summaries['USD']) ? $service_summaries['USD'] : array('total_orders' => 0, 'total_order_value' => 0, 'avg_order_value' => 0, 'active_agents' => 0, 'latest_order_date' => '');
$spares_top_agent = !empty($spares_sales_data) ? $spares_sales_data[0] : null;
$service_top_agents = array();
foreach ((isset($service_sales_data) && is_array($service_sales_data)) ? $service_sales_data : array() as $service_agent_row) {
    $agent_currency = strtoupper(trim((string) $service_agent_row->currency));
    if (!isset($service_top_agents[$agent_currency])) {
        $service_top_agents[$agent_currency] = $service_agent_row;
    }
}
$spares_top_brand_data = isset($spares_top_brand) && is_array($spares_top_brand) ? $spares_top_brand : array();
$service_top_brand_data = isset($service_top_brands) && is_array($service_top_brands) ? $service_top_brands : array();
$overall_income_inr = (float) ($marketing_df_summary['running_total_value'] ?? 0)
    + (float) ($spares_breakdown['domestic_value'] ?? 0)
    + (float) $service_inr_summary['total_order_value'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="google" content="notranslate">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A modern and user-friendly dashboard for Opportunities and Quotations.">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> | Dashboard</title>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

        body {
            background-color: var(--secondary-color);
        }

        /* USER PREFERENCE: Tighter layout */
        .wrapper .container-fluid {
            padding: 5px 15px;
        }

        .page-header {
            padding-bottom: 0px !important;
            margin: 0px 0 15px !important; /* Added a bit of bottom margin for spacing */
            border-bottom: 1px solid #eee !important;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-color);
        }

        .section-heading {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modern-card {
            background-color: #fff;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 20px;
            margin-bottom: 25px;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .kpi-card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
            gap: 15px;
        }
        
        .kpi-card {
            padding: 15px;
            border-radius: var(--border-radius);
            border-width: 1px;
            border-style: solid;
            transition: all 0.3s ease;
            text-decoration: none !important;
        }

        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        /* USER PREFERENCE: Bold, black titles */
        .kpi-card p {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 5px;
            line-height: 1.4;
            color: #000 !important;
        }

        /* USER PREFERENCE: Gradient text for numbers */
        .stats-value {
            font-size: 20px !important;
            margin: 0px !important;
            color: transparent;
            background-image: linear-gradient(90deg, #049dd4, #0f62fe, #8a3ffc);
            display: inline-block;
            caret-color: #0f62fe;
            -webkit-background-clip: text;
            background-clip: text;
        }
        
        /* Background colors for cards are retained for visual cues */
        .kpi-status-won { background-color: #e4f9f1; border-color: #a3e9d2; }
        .kpi-status-lost { background-color: #fdeeee; border-color: #f7c5c5; }
        .kpi-status-active { background-color: #e9f2ff; border-color: #a6caff; }
        .kpi-status-progress { background-color: #fff4e6; border-color: #ffdcb3; }
        .kpi-status-default { background-color: #f8f9fa; border-color: #dee2e6; }

        .sidebar-kpi-stack .kpi-card { display: flex; justify-content: space-between; align-items: center; padding: 12px 15px; margin-bottom: 15px; background-color: #fff; border: 1px solid var(--border-color); }
        .sidebar-kpi-stack .kpi-card:last-child { margin-bottom: 0; }
        .sidebar-kpi-stack .kpi-card p { margin-bottom: 0; color: var(--text-color-light) !important; font-weight: 600; }
        .sidebar-kpi-stack .kpi-card .stats-value { font-size: 20px !important; } /* Keep gradient */
        .kpi-card-missed { border-left: 4px solid #f05050; }
        .kpi-card-today { border-left: 4px solid #ffbd4a; }
        .kpi-card-upcoming { border-left: 4px solid #4a81d4; }
        
        .quick-links-list { list-style: none; padding: 0; margin: 0; }
        .quick-links-list li a { display: flex; align-items: center; padding: 12px 0; text-decoration: none; color: var(--text-color); font-weight: 500; border-bottom: 1px solid var(--border-color); }
        .quick-links-list li:last-child a { border-bottom: none; }
        .quick-links-list li a:hover { color: var(--primary-color); }
        .quick-links-list li a .fa { margin-right: 15px; width: 20px; text-align: center; color: var(--text-color-light); }

        .chart-container { flex-grow: 1; min-height: 300px; }

        /* Attractive Team Followup Details */
        .team-followup-list { padding: 0; margin: 0; list-style: none; max-height: 350px; overflow-y: auto; }
        .user-followup-item { display: flex; align-items: center; justify-content: space-between; padding: 15px 5px; border-bottom: 1px solid var(--border-color); }
        .user-followup-item:last-child { border-bottom: none; }
        
        .user-info { font-weight: 600; color: var(--text-color); }
        .user-stats { display: flex; gap: 15px; }
        .stat-box { text-align: center; }
        .stat-box a { text-decoration: none; }
        .stat-box .stat-label { font-size: 11px; text-transform: uppercase; color: var(--text-color-light); }
        .stat-box .stat-count { font-size: 18px; font-weight: 700; display: block; }
        .stat-box .stat-count-missed { color: #dc3545; }
        .stat-box .stat-count-today { color: #ffc107; }

        .analytics-hero-card {
            background: linear-gradient(135deg, #16324f 0%, #0f62fe 58%, #2f9e44 100%);
            border-radius: 18px;
            box-shadow: 0 18px 45px rgba(15, 98, 254, 0.18);
            color: #fff;
            margin-bottom: 24px;
            overflow: hidden;
            padding: 24px;
            position: relative;
        }

        .analytics-hero-card:before {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 999px;
            content: "";
            height: 180px;
            position: absolute;
            right: -40px;
            top: -50px;
            width: 180px;
        }

        .analytics-hero-top {
            align-items: flex-start;
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            justify-content: space-between;
            position: relative;
            z-index: 1;
        }

        .analytics-eyebrow {
            color: rgba(255,255,255,0.76);
            display: inline-block;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .analytics-hero-title {
            color: #fff;
            font-size: 30px;
            font-weight: 700;
            margin: 0 0 8px;
        }

        .analytics-hero-text {
            color: rgba(255,255,255,0.88);
            font-size: 14px;
            margin: 0;
            max-width: 760px;
        }

        .analytics-filter-form {
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.16);
            border-radius: 16px;
            min-width: 250px;
            padding: 14px;
        }

        .analytics-filter-form label {
            color: rgba(255,255,255,0.8);
            display: block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .analytics-filter-controls {
            align-items: center;
            display: flex;
            gap: 10px;
        }

        .analytics-select {
            border: 0;
            border-radius: 12px;
            box-shadow: none;
            min-height: 42px;
        }

        .analytics-context-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
            position: relative;
            z-index: 1;
        }

        .analytics-context-pill {
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 999px;
            color: #fff;
            display: inline-flex;
            font-size: 12px;
            gap: 8px;
            padding: 8px 12px;
        }

        .analytics-summary-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            margin-top: 20px;
            position: relative;
            z-index: 1;
        }

        .analytics-summary-card {
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.16);
            border-radius: 16px;
            padding: 16px;
        }

        .analytics-summary-label {
            color: rgba(255,255,255,0.74);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.05em;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .analytics-summary-label-row { display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:10px; }
        .analytics-summary-label-row .analytics-summary-label { margin-bottom:0; }
        .analytics-income-info { position:relative; display:inline-flex; }
        .analytics-income-info-trigger { display:inline-flex; align-items:center; justify-content:center; width:23px; height:23px; padding:0; border:1px solid rgba(255,255,255,.45); border-radius:50%; background:rgba(255,255,255,.14); color:#fff; cursor:help; }
        .analytics-income-tooltip { position:absolute; z-index:40; top:31px; right:0; width:255px; padding:12px 14px; border-radius:10px; background:#fff; color:#17324f; box-shadow:0 14px 30px rgba(6,24,44,.3); opacity:0; visibility:hidden; transform:translateY(-4px); transition:opacity .15s ease, transform .15s ease; pointer-events:none; font-size:12px; font-weight:500; text-transform:none; letter-spacing:0; }
        .analytics-income-tooltip:before { content:""; position:absolute; top:-6px; right:6px; border-width:0 6px 6px; border-style:solid; border-color:transparent transparent #fff; }
        .analytics-income-tooltip-row { display:flex; justify-content:space-between; gap:12px; padding:5px 0; }
        .analytics-income-tooltip-row + .analytics-income-tooltip-row { border-top:1px solid #e4ebf3; }
        .analytics-income-tooltip-row strong { white-space:nowrap; }
        .analytics-income-info:hover .analytics-income-tooltip, .analytics-income-info:focus-within .analytics-income-tooltip { opacity:1; visibility:visible; transform:translateY(0); }

        .analytics-summary-value {
            color: #fff;
            font-size: 26px;
            font-weight: 800;
            line-height: 1.05;
            margin-bottom: 6px;
        }

        .analytics-summary-note {
            color: rgba(255,255,255,0.78);
            font-size: 12px;
        }

        .analytics-highlight-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            margin-top: 16px;
            position: relative;
            z-index: 1;
        }

        .analytics-highlight-card {
            background: rgba(9, 20, 34, 0.18);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 16px;
            padding: 16px;
        }

        .analytics-highlight-card strong {
            color: #fff;
            display: block;
            font-size: 18px;
            margin-top: 4px;
        }

        .analytics-highlight-card span {
            color: rgba(255,255,255,0.74);
            display: block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .analytics-chart-card {
            padding-bottom: 16px;
        }

        .analytics-chart-head {
            align-items: flex-start;
            display: flex;
            gap: 12px;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .analytics-chart-title {
            color: var(--text-color);
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 4px;
        }

        .analytics-chart-subtitle {
            color: var(--text-color-light);
            font-size: 13px;
            margin: 0;
        }

        .analytics-chart-container {
            min-height: 320px;
        }

        .chart-empty-state {
            align-items: center;
            background: linear-gradient(180deg, #f9fbff 0%, #f2f6fb 100%);
            border: 1px dashed #cfd9e6;
            border-radius: 14px;
            color: #6b778c;
            display: flex;
            font-size: 14px;
            justify-content: center;
            min-height: 320px;
            padding: 24px;
            text-align: center;
        }

        .analytics-mini-list {
            display: grid;
            gap: 10px;
            margin-top: 16px;
        }

        .analytics-mini-item {
            align-items: center;
            background: #f7faff;
            border: 1px solid #dce8f8;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            padding: 10px 12px;
        }

        .analytics-mini-rank {
            background: #0f62fe;
            border-radius: 999px;
            color: #fff;
            display: inline-flex;
            font-size: 11px;
            font-weight: 700;
            justify-content: center;
            margin-right: 10px;
            min-width: 24px;
            padding: 4px 6px;
        }

        .analytics-mini-name {
            align-items: center;
            color: #1f2d3d;
            display: inline-flex;
            font-size: 13px;
            font-weight: 600;
        }

        .analytics-mini-value {
            color: #16324f;
            font-size: 13px;
            font-weight: 700;
        }

        .analytics-legend-list {
            display: grid;
            gap: 10px;
            margin-top: 16px;
        }

        .analytics-legend-item {
            align-items: center;
            background: #fbfcfe;
            border: 1px solid #e5edf7;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            padding: 10px 12px;
        }

        .analytics-legend-meta {
            color: #6b778c;
            font-size: 12px;
        }

        .analytics-dot {
            border-radius: 999px;
            display: inline-block;
            height: 10px;
            margin-right: 8px;
            width: 10px;
        }

        @media (max-width: 767px) {
            .analytics-hero-card {
                padding: 20px;
            }

            .analytics-hero-title {
                font-size: 24px;
            }

            .analytics-filter-form {
                min-width: 100%;
            }

            .analytics-filter-controls {
                flex-direction: column;
                align-items: stretch;
            }
        }
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
                        <h1 class="page-title text-center">Opportunities & Quotation Dashboard</h1>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-9">
                    
                    <div class="modern-card">
                        <h5 class="section-heading">
                            <span>Opportunities Statistics</span>
                            <a href='<?php echo page_url; ?>Leads/opportunity' class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> New Opportunity</a>
                        </h5>
                        <div class="kpi-card-grid">
                            <?php 
                            foreach ($getAllLeadStages as $row1) :
                                $datacount = $DI->Dashboard_model->lead_stage_counts($row1->lead_id); 
                                $status_class = get_status_color_class($row1->lead_name);
                                ?>
                                <a href="<?php echo page_url; ?>Leads/lead_stages/<?php echo $row1->lead_id; ?>" class="kpi-card <?php echo $status_class; ?>">
                                    <p><?php echo $row1->lead_name; ?></p>
                                    <div class="stats-value"><?php echo $datacount; ?></div>
                                </a>
                            <?php endforeach; ?>

                            <?php 
                            foreach ($getAllLeadStagesforquotation as $row1) :
                                $datacount = $DI->Dashboard_model->lead_stage_counts($row1->lead_id);
                                $status_class = get_status_color_class($row1->lead_name);
                                ?>
                                <a href="<?php echo page_url; ?>Leads/lead_stages/<?php echo $row1->lead_id; ?>" class="kpi-card <?php echo $status_class; ?>">
                                    <p><?php echo $row1->lead_name; ?></p>
                                    <div class="stats-value"><?php echo $datacount; ?></div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php if ($_SESSION['logged_in']['user_id'] == 139 || $_SESSION['logged_in']['user_id'] == 61 || $_SESSION['logged_in']['user_id'] == 161) : ?>
                    <div class="modern-card">
                        <h5 class="section-heading">Team Followup Details</h5>
                        <ul class="team-followup-list">
                            <?php
                            $this->db->select('user_id,first_name,last_name')->from('system_users')->where('marketing_person', 1)->where('business_location', 2)->where('user_status', 1);
                            $rest = $this->db->order_by('first_name')->get();
                            if ($rest->num_rows() > 0) {
                                foreach ($rest->result() as $row) {
                                    $c = $this->Dashboard_model->FollowupCountsUsers(2, $row->user_id);
                                    $d = $this->Dashboard_model->FollowupCountsUsers(1, $row->user_id);
                            ?>
                                <li class="user-followup-item">
                                    <div class="user-info"><?php echo ucwords(strtolower($row->first_name . ' ' . $row->last_name)); ?></div>
                                    <div class="user-stats">
                                        <div class="stat-box">
                                            <a href='<?php echo page_url; ?>Leads/followups/2/<?php echo $row->user_id; ?>'>
                                                <span class="stat-count stat-count-missed"><?php echo $c; ?></span>
                                                <span class="stat-label">Missed</span>
                                            </a>
                                        </div>
                                        <div class="stat-box">
                                            <a href='<?php echo page_url; ?>Leads/followups/1/<?php echo $row->user_id; ?>'>
                                                <span class="stat-count stat-count-today"><?php echo $d; ?></span>
                                                <span class="stat-label">Today's</span>
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            <?php } } else { echo "<li>No active users found.</li>"; } ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-3">
                    <div class="modern-card">
                        <h5 class="section-heading">
                           <span>Followups</span>
                        </h5>
                        <div class="sidebar-kpi-stack">
                             <?php $b1 = $DI->Dashboard_model->FollowupCounts(1); ?>
                            <a href="<?php echo page_url; ?>Leads/followups/1" class="kpi-card kpi-card-today">
                                <p>Today's Followup(s)</p>
                                <div class="stats-value"><?php echo $b1; ?></div>
                            </a>
                            
                            <?php $b2 = $DI->Dashboard_model->FollowupCounts(2); ?>
                            <a href="<?php echo page_url; ?>Leads/followups/2" class="kpi-card kpi-card-missed">
                                <p>Missed Followup(s)</p>
                                <div class="stats-value"><?php echo $b2; ?></div>
                            </a>

                            <?php $b3 = $DI->Dashboard_model->FollowupCounts(3); ?>
                            <a href="<?php echo page_url; ?>Leads/followups/3" class="kpi-card kpi-card-upcoming">
                                <p>Upcoming Followup(s)</p>
                                <div class="stats-value"><?php echo $b3; ?></div>
                            </a>
                        </div>
                    </div>
                    
                    <div class="modern-card">
                        <h5 class="section-heading">
                           <span>Quick Links</span>
                        </h5>
                        <ul class="quick-links-list">
                            <li><a href="<?php echo page_url; ?>Customer/addnewcustomer"><i class="fa fa-user-plus"></i> Add New Customer</a></li>
                            <li><a href='<?php echo page_url; ?>Customer/viewyourcustomers'><i class="fa fa-users"></i> View Your Customers</a></li>
                            <li><a href="<?php echo page_url; ?>Leads/all_opportunities"><i class="fa fa-list"></i> All Opportunities</a></li>
                             <li><a href="<?php echo page_url; ?>Business_card_leads"><i class="fa fa-id-card-o"></i> Exhibition Business Card Leads</a></li>
                            <li><a href="<?php echo page_url; ?>Model_quote_report"><i class="fa fa-file-text-o"></i> Model-wise Quote Report</a></li>
                            <?php if ($user_id == 139 || $user_id == 161) : ?>
                            <li><a href="<?php echo page_url; ?>Dashboard/filteropportunitybysource/ALL"><i class="fa fa-filter"></i> Source Wise Report</a></li>
                            <li><a href="<?php echo page_url; ?>Customer/customer_view"><i class="fa fa-globe"></i> View Indian Customers</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <?php if ($user_id == 139 || $user_id == 161) : ?>
                    <div class="modern-card">
                        <h5 class="section-heading">
                           <span>Email Actions</span>
                        </h5>
                        <ul class="quick-links-list">
                        <?php
                            $qry_exhibition = $this->db->get_where('module_capablity', ['role_id' => $user_id, 'moduleid' => '15', 'submoduleid' => '38', 'submodule_access' => '1']);
                            if ($qry_exhibition->num_rows() > 0) :
                        ?>
                            <li><a href="<?php echo page_url; ?>Master/User_management/triggeremail/1"><i class="fa fa-envelope"></i> Intro (Exhibition)</a></li>
                        <?php endif; ?>

                        <?php
                            $qry_common = $this->db->get_where('module_capablity', ['role_id' => $user_id, 'moduleid' => '15', 'submoduleid' => '39', 'submodule_access' => '1']);
                            if ($qry_common->num_rows() > 0) :
                        ?>
                            <li><a href="<?php echo page_url; ?>Master/User_management/triggeremail/2"><i class="fa fa-envelope-o"></i> Intro (Common)</a></li>
                        <?php endif; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
                </div>
            
            <?php if ($show_order_analytics) : ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="analytics-hero-card">
                        <div class="analytics-hero-top">
                            <div>
                                <span class="analytics-eyebrow">Order Intelligence</span>
                                <h3 class="analytics-hero-title">Sales Performance & Brand Momentum</h3>
                                <p class="analytics-hero-text">This section now reads the latest financial year by default and lets you jump into any past financial year as needed. Calculations are based on the PO date available in the order records.</p>
                                <!-- Model-wise quote report (2026-09-30). Model_quote_report re-checks
                                     the same admin rule this section is shown under. -->
                                <a href="<?php echo page_url; ?>Model_quote_report" class="btn btn-primary btn-sm" style="margin-top:6px;">
                                    <i class="fa fa-file-text-o"></i> Model-wise Quote Report
                                </a>
                            </div>
                            <form method="get" action="<?php echo page_url; ?>Dashboard/opportunity_dashboard" class="analytics-filter-form">
                                <label for="financial_year">Financial Year</label>
                                <div class="analytics-filter-controls">
                                    <select name="financial_year" id="financial_year" class="form-control analytics-select" onchange="this.form.submit();">
                                        <?php foreach ($financial_year_options as $financial_year_option) { ?>
                                            <option value="<?php echo htmlspecialchars($financial_year_option['value'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($selected_financial_year === $financial_year_option['value']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($financial_year_option['label'], ENT_QUOTES, 'UTF-8'); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <button type="submit" class="btn btn-default">View</button>
                                </div>
                            </form>
                        </div>

                        <div class="analytics-context-row">
                            <span class="analytics-context-pill"><strong><?php echo htmlspecialchars($selected_financial_year_label, ENT_QUOTES, 'UTF-8'); ?></strong></span>
                            <span class="analytics-context-pill"><?php echo date('d M Y', strtotime($selected_financial_year_start_date)); ?> to <?php echo date('d M Y', strtotime($selected_financial_year_end_date)); ?></span>
                            <?php if (!empty($order_summary['latest_order_date'])) { ?>
                                <span class="analytics-context-pill">Latest booked order: <?php echo date('d M Y', strtotime($order_summary['latest_order_date'])); ?></span>
                            <?php } ?>
                        </div>

                        <div class="analytics-summary-grid">
                            <div class="analytics-summary-card">
                                <div class="analytics-summary-label">Total Order Value</div>
                                <div class="analytics-summary-value">Rs <?php echo number_format((float) $order_summary['total_order_value'], 0); ?></div>
                                <div class="analytics-summary-note">Revenue booked in this financial year</div>
                            </div>
                            <div class="analytics-summary-card">
                                <div class="analytics-summary-label">Total Orders</div>
                                <div class="analytics-summary-value"><?php echo (int) $order_summary['total_orders']; ?></div>
                                <div class="analytics-summary-note">Confirmed orders mapped to the selected FY</div>
                            </div>
                            <div class="analytics-summary-card">
                                <div class="analytics-summary-label">Active Sales Agents</div>
                                <div class="analytics-summary-value"><?php echo (int) $order_summary['active_agents']; ?></div>
                                <div class="analytics-summary-note">Agents who booked at least one order</div>
                            </div>
                            <div class="analytics-summary-card">
                                <div class="analytics-summary-label">Average Order Value</div>
                                <div class="analytics-summary-value">Rs <?php echo number_format((float) $order_summary['avg_order_value'], 0); ?></div>
                                <div class="analytics-summary-note"><?php echo (int) $order_summary['active_brands']; ?> active brands contributed</div>
                            </div>
                        </div>

                        <div class="analytics-highlight-grid">
                            <div class="analytics-highlight-card">
                                <span>Top Sales Agent</span>
                                <strong><?php echo htmlspecialchars($order_summary['top_agent_name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                <div class="analytics-summary-note">Booked Rs <?php echo number_format((float) $order_summary['top_agent_value'], 0); ?> in this FY</div>
                            </div>
                            <div class="analytics-highlight-card">
                                <span>Top Brand</span>
                                <strong><?php echo htmlspecialchars($order_summary['top_brand_name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                <div class="analytics-summary-note">Generated Rs <?php echo number_format((float) $order_summary['top_brand_value'], 0); ?> in this FY</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-7">
                    <div class="modern-card analytics-chart-card">
                        <div class="analytics-chart-head">
                            <div>
                                <h5 class="analytics-chart-title">Sales Agent Performance</h5>
                                <p class="analytics-chart-subtitle">Ranking by total booked order value for <?php echo htmlspecialchars($selected_financial_year_label, ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                            <div>
                                <a href="<?php echo page_url; ?>OrderController/userwisemonthlyreport" class="btn btn-default btn-sm">Detailed Report</a>
                                <a href="<?php echo page_url; ?>OrderController/dfwisecostorderreport" class="btn btn-primary btn-sm">DF Wise Cost Report</a>
                            </div>
                        </div>
                        <?php if (!empty($sales_data)) { ?>
                            <div class="chart-container analytics-chart-container">
                                <canvas id="salesBarChart"></canvas>
                            </div>
                            <div class="analytics-mini-list">
                                <?php foreach (array_slice($sales_data, 0, 3) as $index => $sales_item) { ?>
                                    <div class="analytics-mini-item">
                                        <span class="analytics-mini-name"><span class="analytics-mini-rank">#<?php echo $index + 1; ?></span><?php echo htmlspecialchars($sales_item->agent_name, ENT_QUOTES, 'UTF-8'); ?></span>
                                        <span class="analytics-mini-value">Rs <?php echo number_format((float) $sales_item->total_sales, 0); ?></span>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="chart-empty-state">No sales agent order data found for <?php echo htmlspecialchars($selected_financial_year_label, ENT_QUOTES, 'UTF-8'); ?>.</div>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="modern-card analytics-chart-card">
                        <div class="analytics-chart-head">
                            <div>
                                <h5 class="analytics-chart-title">Top 5 Brands by Order</h5>
                                <p class="analytics-chart-subtitle">Brand contribution mix for <?php echo htmlspecialchars($selected_financial_year_label, ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                            <a href="<?php echo page_url; ?>OrderController" class="btn btn-default btn-sm">Brand Report</a>
                        </div>
                        <?php if (!empty($order_data_by_brand)) { ?>
                            <div class="chart-container analytics-chart-container">
                                <canvas id="orderChart"></canvas>
                            </div>
                            <div class="analytics-legend-list">
                                <?php
                                $brand_palette = array('#0f62fe', '#2f9e44', '#f59f00', '#d6336c', '#5f3dc4');
                                foreach ($order_data_by_brand as $brand_index => $brand_item) {
                                    $brand_color = $brand_palette[$brand_index % count($brand_palette)];
                                ?>
                                    <div class="analytics-legend-item">
                                        <div>
                                            <div class="analytics-mini-name"><span class="analytics-dot" style="background: <?php echo $brand_color; ?>;"></span><?php echo htmlspecialchars($brand_item->name, ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="analytics-legend-meta"><?php echo (int) $brand_item->order_count; ?> orders</div>
                                        </div>
                                        <div class="analytics-mini-value">Rs <?php echo number_format((float) $brand_item->total_order_value, 0); ?></div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="chart-empty-state">No brand-level order data found for <?php echo htmlspecialchars($selected_financial_year_label, ENT_QUOTES, 'UTF-8'); ?>.</div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="analytics-hero-card">
                        <div class="analytics-hero-top">
                            <div>
                                <span class="analytics-eyebrow">Department Revenue</span>
                                <h3 class="analytics-hero-title">Spares Income &amp; Service Sales</h3>
                                <p class="analytics-hero-text">Finalized Spares orders and won Service purchase orders for <?php echo htmlspecialchars($selected_financial_year_label, ENT_QUOTES, 'UTF-8'); ?>. Service currencies are reported separately.</p>
                            </div>
                        </div>
                        <div class="analytics-summary-grid">
                            <div class="analytics-summary-card">
                                <div class="analytics-summary-label">Total Income — All Departments</div>
                                <div class="analytics-summary-value">Rs <?php echo number_format($overall_income_inr, 0); ?></div>
                                <div class="analytics-summary-note">Running Marketing DFs + Domestic Spares + INR Service; hold DFs excluded</div>
                            </div>
                            <div class="analytics-summary-card">
                                <div class="analytics-summary-label-row">
                                    <div class="analytics-summary-label">Marketing - Running DF</div>
                                    <span class="analytics-income-info">
                                        <button type="button" class="analytics-income-info-trigger" aria-label="Show Running and Hold DF values by Domestic and International type"><i class="fa fa-info"></i></button>
                                        <span class="analytics-income-tooltip" role="tooltip">
                                            <span class="analytics-income-tooltip-row"><span>Running - Domestic (<?php echo (int) ($marketing_df_summary['running_domestic_orders'] ?? 0); ?>)</span><strong>Rs <?php echo number_format((float) ($marketing_df_summary['running_domestic_value'] ?? 0), 0); ?></strong></span>
                                            <span class="analytics-income-tooltip-row"><span>Running - International (<?php echo (int) ($marketing_df_summary['running_international_orders'] ?? 0); ?>)</span><strong>Rs <?php echo number_format((float) ($marketing_df_summary['running_international_value'] ?? 0), 0); ?></strong></span>
                                            <span class="analytics-income-tooltip-row"><span>Hold - Domestic (<?php echo (int) ($marketing_df_summary['hold_domestic_orders'] ?? 0); ?>)</span><strong>Rs <?php echo number_format((float) ($marketing_df_summary['hold_domestic_value'] ?? 0), 0); ?></strong></span>
                                            <span class="analytics-income-tooltip-row"><span>Hold - International (<?php echo (int) ($marketing_df_summary['hold_international_orders'] ?? 0); ?>)</span><strong>Rs <?php echo number_format((float) ($marketing_df_summary['hold_international_value'] ?? 0), 0); ?></strong></span>
                                        </span>
                                    </span>
                                </div>
                                <div class="analytics-summary-value">Rs <?php echo number_format((float) ($marketing_df_summary['running_total_value'] ?? 0), 0); ?></div>
                                <div class="analytics-summary-note"><?php echo (int) ($marketing_df_summary['running_total_orders'] ?? 0); ?> active DF orders; hold value available in tooltip</div>
                            </div>
                            <div class="analytics-summary-card">
                                <div class="analytics-summary-label-row">
                                    <div class="analytics-summary-label">Spares Income (INR)</div>
                                    <span class="analytics-income-info">
                                        <button type="button" class="analytics-income-info-trigger" aria-label="Show Domestic and International Spares values"><i class="fa fa-info"></i></button>
                                        <span class="analytics-income-tooltip" role="tooltip">
                                            <span class="analytics-income-tooltip-row"><span>Domestic (<?php echo (int) ($spares_breakdown['domestic_orders'] ?? 0); ?>)</span><strong>Rs <?php echo number_format((float) ($spares_breakdown['domestic_value'] ?? 0), 0); ?></strong></span>
                                            <span class="analytics-income-tooltip-row"><span>International (<?php echo (int) ($spares_breakdown['international_orders'] ?? 0); ?>)</span><strong>$<?php echo number_format((float) ($spares_breakdown['international_value'] ?? 0), 0); ?></strong></span>
                                        </span>
                                    </span>
                                </div>
                                <div class="analytics-summary-value">Rs <?php echo number_format((float) ($spares_breakdown['domestic_value'] ?? 0), 0); ?></div>
                                <div class="analytics-summary-note"><?php echo (int) ($spares_breakdown['domestic_orders'] ?? 0); ?> domestic orders · <?php echo (int) $spares_summary['active_agents']; ?> active agents</div>
                            </div>
                            <div class="analytics-summary-card">
                                <div class="analytics-summary-label-row">
                                    <div class="analytics-summary-label">Service Sales (INR)</div>
                                    <span class="analytics-income-info">
                                        <button type="button" class="analytics-income-info-trigger" aria-label="Show Domestic and International Service values"><i class="fa fa-info"></i></button>
                                        <span class="analytics-income-tooltip" role="tooltip">
                                            <span class="analytics-income-tooltip-row"><span>Domestic (<?php echo (int) ($service_breakdown['domestic_orders'] ?? 0); ?>)</span><strong>Rs <?php echo number_format((float) ($service_breakdown['domestic_value'] ?? 0), 0); ?></strong></span>
                                            <span class="analytics-income-tooltip-row"><span>International (<?php echo (int) ($service_breakdown['international_orders'] ?? 0); ?>)</span><strong>$<?php echo number_format((float) ($service_breakdown['international_value'] ?? 0), 0); ?></strong></span>
                                        </span>
                                    </span>
                                </div>
                                <div class="analytics-summary-value">Rs <?php echo number_format((float) $service_inr_summary['total_order_value'], 0); ?></div>
                                <div class="analytics-summary-note"><?php echo (int) $service_inr_summary['total_orders']; ?> won orders · Avg Rs <?php echo number_format((float) $service_inr_summary['avg_order_value'], 0); ?></div>
                            </div>
                            <div class="analytics-summary-card">
                                <div class="analytics-summary-label">Service Sales (USD)</div>
                                <div class="analytics-summary-value">$<?php echo number_format((float) $service_usd_summary['total_order_value'], 0); ?></div>
                                <div class="analytics-summary-note"><?php echo (int) $service_usd_summary['total_orders']; ?> won orders · Avg $<?php echo number_format((float) $service_usd_summary['avg_order_value'], 0); ?></div>
                            </div>
                            <div class="analytics-summary-card">
                                <div class="analytics-summary-label">Spares Average Order</div>
                                <div class="analytics-summary-value">Rs <?php echo number_format((float) $spares_summary['avg_order_value'], 0); ?></div>
                                <div class="analytics-summary-note">Average finalized Spares order value</div>
                            </div>
                        </div>
                        <div class="analytics-highlight-grid">
                            <div class="analytics-highlight-card">
                                <span>Spares Top Sales Agent</span>
                                <strong><?php echo $spares_top_agent ? htmlspecialchars($spares_top_agent->agent_name, ENT_QUOTES, 'UTF-8') : 'No sales data'; ?></strong>
                                <div class="analytics-summary-note">Rs <?php echo number_format($spares_top_agent ? (float) $spares_top_agent->total_sales : 0, 0); ?></div>
                            </div>
                            <div class="analytics-highlight-card">
                                <span>Spares Top Brand</span>
                                <strong><?php echo !empty($spares_top_brand_data['brand_name']) ? htmlspecialchars($spares_top_brand_data['brand_name'], ENT_QUOTES, 'UTF-8') : 'No brand data'; ?></strong>
                                <div class="analytics-summary-note">Rs <?php echo number_format(!empty($spares_top_brand_data['total_sales']) ? (float) $spares_top_brand_data['total_sales'] : 0, 0); ?></div>
                            </div>
                            <?php foreach (array('INR', 'USD') as $highlight_currency) {
                                $service_agent = isset($service_top_agents[$highlight_currency]) ? $service_top_agents[$highlight_currency] : null;
                                $service_brand = isset($service_top_brand_data[$highlight_currency]) ? $service_top_brand_data[$highlight_currency] : array();
                                $currency_prefix = $highlight_currency === 'USD' ? '$' : 'Rs ';
                            ?>
                                <div class="analytics-highlight-card">
                                    <span>Service Top Sales Agent (<?php echo $highlight_currency; ?>)</span>
                                    <strong><?php echo $service_agent ? htmlspecialchars($service_agent->agent_name, ENT_QUOTES, 'UTF-8') : 'No sales data'; ?></strong>
                                    <div class="analytics-summary-note"><?php echo $currency_prefix; ?><?php echo number_format($service_agent ? (float) $service_agent->total_sales : 0, 0); ?></div>
                                </div>
                                <div class="analytics-highlight-card">
                                    <span>Service Top Brand (<?php echo $highlight_currency; ?>)</span>
                                    <strong><?php echo !empty($service_brand['brand_name']) ? htmlspecialchars($service_brand['brand_name'], ENT_QUOTES, 'UTF-8') : 'No brand data'; ?></strong>
                                    <div class="analytics-summary-note"><?php echo $currency_prefix; ?><?php echo number_format(!empty($service_brand['total_sales']) ? (float) $service_brand['total_sales'] : 0, 0); ?></div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="modern-card analytics-chart-card">
                        <div class="analytics-chart-head"><div><h5 class="analytics-chart-title">Spares Sales Agent Performance</h5><p class="analytics-chart-subtitle">Finalized order income by marketing owner</p></div></div>
                        <?php if (!empty($spares_sales_data)) { ?>
                            <div class="chart-container analytics-chart-container"><canvas id="sparesSalesChart"></canvas></div>
                        <?php } else { ?>
                            <div class="chart-empty-state">No Spares order income found for <?php echo htmlspecialchars($selected_financial_year_label, ENT_QUOTES, 'UTF-8'); ?>.</div>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="modern-card analytics-chart-card">
                        <div class="analytics-chart-head"><div><h5 class="analytics-chart-title">Service Sales Agent Performance</h5><p class="analytics-chart-subtitle">Won service orders by owner; currencies shown independently</p></div></div>
                        <?php if (!empty($service_sales_data)) { ?>
                            <div class="analytics-mini-list">
                                <?php foreach ($service_sales_data as $service_item) { $row_currency = strtoupper(trim((string) $service_item->currency)); ?>
                                    <div class="analytics-mini-item">
                                        <span class="analytics-mini-name"><?php echo htmlspecialchars($service_item->agent_name, ENT_QUOTES, 'UTF-8'); ?> <span class="analytics-context-pill"><?php echo htmlspecialchars($row_currency, ENT_QUOTES, 'UTF-8'); ?></span></span>
                                        <span class="analytics-mini-value"><?php echo $row_currency === 'USD' ? '$' : 'Rs '; ?><?php echo number_format((float) $service_item->total_sales, 0); ?> <small>(<?php echo (int) $service_item->order_count; ?>)</small></span>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="chart-empty-state">No won Service sales found for <?php echo htmlspecialchars($selected_financial_year_label, ENT_QUOTES, 'UTF-8'); ?>.</div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php $this->load->view('common/footer'); ?>
            </div> </div> <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
    
    <?php if ($show_order_analytics) : ?>
    <script>
        const salesData = <?php echo json_encode($sales_data); ?>;
        const orderData = <?php echo json_encode($order_data_by_brand); ?>;
        const sparesSalesData = <?php echo json_encode($spares_sales_data); ?>;
        const brandPalette = ['#0f62fe', '#2f9e44', '#f59f00', '#d6336c', '#5f3dc4'];

        function formatCurrency(value) {
            return 'Rs ' + new Intl.NumberFormat('en-IN', {
                maximumFractionDigits: 0
            }).format(value || 0);
        }

        function formatCompactCurrency(value) {
            if (!value) {
                return 'Rs 0';
            }

            const absValue = Math.abs(value);
            if (absValue >= 10000000) {
                return 'Rs ' + (value / 10000000).toFixed(1).replace(/\.0$/, '') + ' Cr';
            }
            if (absValue >= 100000) {
                return 'Rs ' + (value / 100000).toFixed(1).replace(/\.0$/, '') + ' L';
            }
            if (absValue >= 1000) {
                return 'Rs ' + (value / 1000).toFixed(1).replace(/\.0$/, '') + ' K';
            }
            return formatCurrency(value);
        }

        if (document.getElementById('salesBarChart') && salesData.length > 0) {
            const salesCtx = document.getElementById('salesBarChart').getContext('2d');
            const salesGradient = salesCtx.createLinearGradient(0, 0, 0, 320);
            salesGradient.addColorStop(0, 'rgba(15, 98, 254, 0.92)');
            salesGradient.addColorStop(1, 'rgba(74, 129, 212, 0.28)');

            new Chart(salesCtx, {
                type: 'bar',
                data: {
                    labels: salesData.map(item => item.agent_name),
                    datasets: [{
                        label: 'Total Sales',
                        data: salesData.map(item => Number(item.total_sales || 0)),
                        backgroundColor: salesGradient,
                        borderColor: 'rgba(15, 98, 254, 1)',
                        borderWidth: 1.2,
                        borderRadius: 12,
                        maxBarThickness: 44
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#e5e9f2' },
                            ticks: {
                                callback: function(value) {
                                    return formatCompactCurrency(value);
                                }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                autoSkip: false,
                                maxRotation: 35,
                                minRotation: 35,
                                padding: 6,
                                font: {
                                    size: 10
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const dataPoint = salesData[context.dataIndex] || {};
                                    return [
                                        'Sales: ' + formatCurrency(context.parsed.y),
                                        'Orders: ' + Number(dataPoint.order_count || 0),
                                        'Avg Order: ' + formatCurrency(dataPoint.avg_order_value || 0)
                                    ];
                                }
                            }
                        }
                    }
                }
            });
        }

        if (document.getElementById('sparesSalesChart') && sparesSalesData.length > 0) {
            const sparesCtx = document.getElementById('sparesSalesChart').getContext('2d');
            new Chart(sparesCtx, {
                type: 'bar',
                data: {
                    labels: sparesSalesData.map(item => item.agent_name),
                    datasets: [{
                        label: 'Spares Income',
                        data: sparesSalesData.map(item => Number(item.total_sales || 0)),
                        backgroundColor: 'rgba(47, 158, 68, 0.78)',
                        borderColor: '#2f9e44', borderWidth: 1.2, borderRadius: 10, maxBarThickness: 44
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#e5e9f2' }, ticks: { callback: value => formatCompactCurrency(value) } },
                        x: { grid: { display: false }, ticks: { maxRotation: 0, minRotation: 0 } }
                    },
                    plugins: { legend: { display: false }, tooltip: { callbacks: { label: context => {
                        const row = sparesSalesData[context.dataIndex] || {};
                        return ['Income: ' + formatCurrency(context.parsed.y), 'Orders: ' + Number(row.order_count || 0), 'Avg Order: ' + formatCurrency(row.avg_order_value || 0)];
                    } } } }
                }
            });
        }

        if (document.getElementById('orderChart') && orderData.length > 0) {
            const orderCtx = document.getElementById('orderChart').getContext('2d');

            new Chart(orderCtx, {
                type: 'doughnut',
                data: {
                    labels: orderData.map(item => item.name),
                    datasets: [{
                        data: orderData.map(item => Number(item.total_order_value || 0)),
                        backgroundColor: brandPalette,
                        borderColor: '#ffffff',
                        borderWidth: 4,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '62%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const dataPoint = orderData[context.dataIndex] || {};
                                    return [
                                        context.label,
                                        'Order Value: ' + formatCurrency(context.parsed),
                                        'Orders: ' + Number(dataPoint.order_count || 0),
                                        'Avg Order: ' + formatCurrency(dataPoint.avg_order_value || 0)
                                    ];
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
    <?php endif; ?>

</body>
</html>
