<?php
$summary = isset($dashboard['summary']) ? $dashboard['summary'] : array();
$filter_summary = isset($dashboard['filter_summary']) ? $dashboard['filter_summary'] : array('period' => 'All available records', 'user' => 'All Conductors', 'department' => 'All Departments');
$department_breakdown = isset($dashboard['department_breakdown']) ? $dashboard['department_breakdown'] : array();
$conductor_breakdown = isset($dashboard['conductor_breakdown']) ? $dashboard['conductor_breakdown'] : array();
$participant_breakdown = isset($dashboard['participant_breakdown']) ? $dashboard['participant_breakdown'] : array();
$time_slot_breakdown = isset($dashboard['time_slot_breakdown']) ? $dashboard['time_slot_breakdown'] : array();
$trend = isset($dashboard['trend']) ? $dashboard['trend'] : array('labels' => array(), 'meeting_counts' => array(), 'duration_hours' => array());
$insights = isset($dashboard['insights']) ? $dashboard['insights'] : array();
$top_department = isset($dashboard['top_department']) ? $dashboard['top_department'] : null;
$top_conductor = isset($dashboard['top_conductor']) ? $dashboard['top_conductor'] : null;
$top_participant = isset($dashboard['top_participant']) ? $dashboard['top_participant'] : null;
$longest_meeting = isset($dashboard['longest_meeting']) ? $dashboard['longest_meeting'] : null;
$is_management_view = ($_SESSION['logged_in']['role'] == 12);
$page_heading = $is_management_view ? 'Meeting Intelligence Dashboard' : 'My Meeting Intelligence Dashboard';
$page_subheading = $is_management_view ? 'A smarter management view of collaboration load, time investment, and team engagement.' : 'A cleaner view of your collaboration trail, discussion effort, and meeting patterns.';
$department_chart_labels = array();
$department_chart_counts = array();
$department_chart_palette = array('#0f766e', '#f59e0b', '#ef4444', '#1d4ed8', '#7c3aed', '#0ea5e9');
$conductor_chart_labels = array();
$conductor_chart_counts = array();
$time_slot_labels = array();
$time_slot_counts = array();
$mode_chart_labels = array('Manual Range', 'Auto Timer');
$mode_chart_counts = array(
    isset($summary['manual_range_count']) ? intval($summary['manual_range_count']) : 0,
    isset($summary['auto_timer_count']) ? intval($summary['auto_timer_count']) : 0
);

foreach ($department_breakdown as $department_item) {
    $department_chart_labels[] = $department_item['label'];
    $department_chart_counts[] = intval($department_item['meeting_count']);
}

foreach ($conductor_breakdown as $conductor_item) {
    $conductor_chart_labels[] = $conductor_item['label'];
    $conductor_chart_counts[] = intval($conductor_item['meeting_count']);
}

foreach ($time_slot_breakdown as $slot_item) {
    $time_slot_labels[] = $slot_item['label'];
    $time_slot_counts[] = intval($slot_item['count']);
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
    <title><?php echo sitetitle; ?> Meeting Dashboard</title>
    <meta name="google" content="notranslate">

    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />

    <style>
        :root {
            --meeting-ink: #132238;
            --meeting-muted: #6f8198;
            --meeting-surface: #ffffff;
            --meeting-bg: #f4f7fb;
            --meeting-line: rgba(19, 34, 56, 0.1);
            --meeting-teal: #0f766e;
            --meeting-orange: #f59e0b;
            --meeting-red: #dc2626;
            --meeting-blue: #1d4ed8;
            --meeting-sky: #0ea5e9;
        }

        body {
            background:
                radial-gradient(circle at top right, rgba(245, 158, 11, 0.12), transparent 22%),
                radial-gradient(circle at top left, rgba(15, 118, 110, 0.14), transparent 28%),
                var(--meeting-bg);
            color: var(--meeting-ink);
        }

        .meeting-dashboard-page {
            padding-top: 20px;
            padding-bottom: 30px;
        }

        .hero-card,
        .panel-shell,
        .metric-card,
        .insight-card,
        .spotlight-card {
            border: 1px solid var(--meeting-line);
            border-radius: 22px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.07);
            background: var(--meeting-surface);
        }

        .hero-card {
            overflow: hidden;
            padding: 34px;
            margin-bottom: 24px;
            background:
                linear-gradient(135deg, rgba(15, 118, 110, 0.96), rgba(19, 34, 56, 0.96)),
                var(--meeting-surface);
            color: #fff;
            position: relative;
        }

        .hero-card:before,
        .hero-card:after {
            content: "";
            position: absolute;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
        }

        .hero-card:before {
            width: 220px;
            height: 220px;
            top: -70px;
            right: -40px;
        }

        .hero-card:after {
            width: 140px;
            height: 140px;
            bottom: -30px;
            right: 120px;
        }

        .hero-content,
        .hero-actions {
            position: relative;
            z-index: 1;
        }

        .hero-eyebrow {
            display: inline-block;
            font-size: 12px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.78);
            margin-bottom: 10px;
        }

        .hero-card h2 {
            margin: 0 0 10px;
            font-size: 33px;
            line-height: 1.2;
            font-weight: 700;
            color: #fff;
        }

        .hero-card p {
            max-width: 760px;
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 16px;
        }

        .hero-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            font-size: 13px;
            font-weight: 600;
        }

        .hero-actions {
            display: flex;
            justify-content: flex-end;
            align-items: flex-start;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 12px;
        }

        .hero-actions .btn {
            border-radius: 999px;
            padding: 11px 20px;
            font-weight: 700;
            border: none;
        }

        .hero-actions .btn-default {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .filter-shell,
        .panel-shell {
            padding: 24px;
            margin-bottom: 22px;
        }

        .section-title {
            margin: 0;
            font-size: 21px;
            font-weight: 700;
            color: var(--meeting-ink);
        }

        .section-subtitle {
            margin-top: 6px;
            color: var(--meeting-muted);
            font-size: 14px;
            line-height: 1.6;
        }

        .panel-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 18px;
        }

        .quick-range-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .quick-range-btn {
            border: 1px solid rgba(15, 118, 110, 0.18);
            background: rgba(15, 118, 110, 0.06);
            color: var(--meeting-teal);
            border-radius: 999px;
            padding: 8px 14px;
            font-size: 12px;
            font-weight: 700;
        }

        .filter-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--meeting-muted);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .filter-shell .form-control,
        .filter-shell .select2-container .select2-selection--single {
            border-radius: 14px !important;
            min-height: 46px;
            border: 1px solid rgba(19, 34, 56, 0.12);
            box-shadow: none !important;
        }

        .filter-shell .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 44px;
            color: var(--meeting-ink);
            padding-left: 14px;
        }

        .filter-shell .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 44px;
            right: 10px;
        }

        .filter-shell .btn {
            border-radius: 14px;
            min-height: 46px;
            font-weight: 700;
        }

        .metric-card {
            padding: 22px;
            position: relative;
            overflow: hidden;
            margin-bottom: 22px;
            min-height: 170px;
        }

        .metric-card:before {
            content: "";
            position: absolute;
            width: 120px;
            height: 120px;
            border-radius: 999px;
            right: -30px;
            top: -30px;
            background: rgba(15, 118, 110, 0.08);
        }

        .metric-icon {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
            margin-bottom: 18px;
        }

        .metric-icon.teal { background: linear-gradient(135deg, #0f766e, #14b8a6); }
        .metric-icon.orange { background: linear-gradient(135deg, #f59e0b, #fb923c); }
        .metric-icon.blue { background: linear-gradient(135deg, #1d4ed8, #38bdf8); }
        .metric-icon.red { background: linear-gradient(135deg, #dc2626, #fb7185); }
        .metric-icon.navy { background: linear-gradient(135deg, #132238, #334155); }
        .metric-icon.sky { background: linear-gradient(135deg, #0ea5e9, #06b6d4); }

        .metric-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--meeting-muted);
            font-weight: 700;
            margin-bottom: 7px;
        }

        .metric-value {
            font-size: 31px;
            line-height: 1.15;
            font-weight: 700;
            color: var(--meeting-ink);
            margin-bottom: 8px;
        }

        .metric-meta {
            color: var(--meeting-muted);
            font-size: 13px;
            line-height: 1.6;
        }

        .insight-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .insight-card {
            padding: 18px 18px 18px 20px;
            border-left: 5px solid var(--meeting-teal);
            min-height: 150px;
        }

        .insight-index {
            width: 32px;
            height: 32px;
            border-radius: 999px;
            background: rgba(15, 118, 110, 0.11);
            color: var(--meeting-teal);
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
        }

        .insight-card p {
            margin: 0;
            font-size: 14px;
            line-height: 1.7;
            color: var(--meeting-ink);
        }

        .spotlight-card {
            padding: 24px;
            background:
                linear-gradient(180deg, rgba(15, 118, 110, 0.06), transparent 55%),
                #fff;
            min-height: 100%;
        }

        .spotlight-item {
            padding: 14px 0;
            border-bottom: 1px dashed rgba(19, 34, 56, 0.12);
        }

        .spotlight-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .spotlight-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--meeting-muted);
            font-weight: 700;
            margin-bottom: 6px;
        }

        .spotlight-value {
            font-size: 17px;
            font-weight: 700;
            color: var(--meeting-ink);
            margin-bottom: 3px;
        }

        .spotlight-note {
            color: var(--meeting-muted);
            font-size: 13px;
            line-height: 1.6;
        }

        .chart-shell {
            min-height: 390px;
        }

        .chart-shell canvas {
            width: 100% !important;
            height: 280px !important;
        }

        .mini-table {
            margin-bottom: 0;
        }

        .mini-table thead th {
            border-top: none !important;
            color: var(--meeting-muted);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .mini-table tbody td {
            vertical-align: middle !important;
            border-color: rgba(19, 34, 56, 0.07) !important;
            color: var(--meeting-ink);
        }

        .rank-pill,
        .soft-badge,
        .mode-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 700;
        }

        .rank-pill {
            background: rgba(29, 78, 216, 0.1);
            color: var(--meeting-blue);
        }

        .soft-badge {
            background: rgba(15, 118, 110, 0.08);
            color: var(--meeting-teal);
        }

        .soft-badge.warn {
            background: rgba(245, 158, 11, 0.12);
            color: #b45309;
        }

        .soft-badge.gray {
            background: rgba(100, 116, 139, 0.12);
            color: #475569;
        }

        .mode-badge.manual {
            background: rgba(245, 158, 11, 0.12);
            color: #b45309;
        }

        .mode-badge.auto {
            background: rgba(14, 165, 233, 0.12);
            color: #0369a1;
        }

        .records-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
        }

        .records-caption {
            color: var(--meeting-muted);
            font-size: 14px;
            line-height: 1.6;
            margin-top: 6px;
        }

        .meeting-table thead th {
            background: #132238 !important;
            color: #fff;
            border-color: #132238 !important;
            font-size: 12px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            vertical-align: middle !important;
        }

        .meeting-table tbody td {
            vertical-align: top !important;
            border-color: rgba(19, 34, 56, 0.08) !important;
        }

        .meeting-table tbody tr:nth-child(even) {
            background: rgba(15, 118, 110, 0.018);
        }

        .meeting-table tbody tr:hover {
            background: rgba(15, 118, 110, 0.05);
        }

        .table-person {
            font-weight: 700;
            color: var(--meeting-ink);
            margin-bottom: 4px;
        }

        .table-subtext {
            color: var(--meeting-muted);
            font-size: 12px;
            line-height: 1.5;
        }

        .df-card {
            background: rgba(15, 118, 110, 0.05);
            border-radius: 14px;
            padding: 10px 12px;
            min-width: 150px;
        }

        .df-code {
            font-weight: 700;
            color: var(--meeting-teal);
            margin-bottom: 3px;
        }

        .remarks-preview {
            color: var(--meeting-ink);
            line-height: 1.6;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .btn-remarks {
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            padding: 6px 12px;
        }

        .dt-buttons {
            margin-bottom: 12px;
        }

        .dt-buttons .btn {
            border-radius: 999px !important;
            border: 1px solid rgba(19, 34, 56, 0.12) !important;
            background: #fff !important;
            color: var(--meeting-ink) !important;
            font-weight: 700 !important;
            margin-right: 6px;
            margin-bottom: 8px;
        }

        .dataTables_filter input {
            border-radius: 999px !important;
            border: 1px solid rgba(19, 34, 56, 0.12) !important;
            box-shadow: none !important;
            padding: 6px 14px !important;
        }

        .empty-chart {
            min-height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: var(--meeting-muted);
            border: 1px dashed rgba(19, 34, 56, 0.14);
            border-radius: 16px;
            background: rgba(15, 118, 110, 0.03);
            font-size: 14px;
            padding: 20px;
        }

        .modal-content {
            border-radius: 22px;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--meeting-teal), #132238);
            color: #fff;
            border-bottom: none;
        }

        .modal-header .close {
            color: #fff;
            opacity: 0.9;
        }

        @media (max-width: 991px) {
            .hero-actions {
                justify-content: flex-start;
            }

            .insight-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .hero-card {
                padding: 24px 20px;
            }

            .hero-card h2 {
                font-size: 28px;
            }

            .panel-top,
            .records-head {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>
    <?php $this->load->view('common/info-section.php'); ?>

    <div class="wrapper">
        <div class="container-fluid meeting-dashboard-page">
            <div class="hero-card">
                <div class="row">
                    <div class="col-md-8 hero-content">
                        <span class="hero-eyebrow">Meeting Intelligence</span>
                        <h2><?php echo $page_heading; ?></h2>
                        <p><?php echo $page_subheading; ?></p>
                        <div class="hero-pills">
                            <span class="hero-pill"><i class="fa fa-calendar"></i> <?php echo $filter_summary['period']; ?></span>
                            <span class="hero-pill"><i class="fa fa-user-circle-o"></i> <?php echo $filter_summary['user']; ?></span>
                            <span class="hero-pill"><i class="fa fa-sitemap"></i> <?php echo $filter_summary['department']; ?></span>
                            <span class="hero-pill"><i class="fa fa-clock-o"></i> Last logged: <?php echo isset($summary['last_logged_on_display']) ? $summary['last_logged_on_display'] : 'N/A'; ?></span>
                        </div>
                    </div>
                    <div class="col-md-4 hero-actions">
                        <a href="<?php echo page_url; ?>Maintenance_support/meetinglog" class="btn btn-warning"><i class="fa fa-plus-circle"></i> Log New Meeting</a>
                        <a href="<?php echo page_url; ?>Maintenance_support/listmeetings" class="btn btn-default"><i class="fa fa-refresh"></i> Reset Dashboard</a>
                    </div>
                </div>
            </div>

            <?php if ($this->session->flashdata('message')) { ?>
                <div class="alert alert-success" style="border-radius:16px; border:none; box-shadow:0 10px 30px rgba(15,118,110,0.12);">
                    <?php echo $this->session->flashdata('message'); ?>
                </div>
            <?php } ?>

            <div class="panel-shell filter-shell">
                <div class="panel-top">
                    <div>
                        <h3 class="section-title">Smart Filters</h3>
                        <p class="section-subtitle">Cut the dashboard by time, conductor, and department so management can read the story behind the rows.</p>
                    </div>
                    <div class="quick-range-wrap">
                        <button type="button" class="quick-range-btn" data-range="today">Today</button>
                        <button type="button" class="quick-range-btn" data-range="last7">Last 7 Days</button>
                        <button type="button" class="quick-range-btn" data-range="month">This Month</button>
                        <button type="button" class="quick-range-btn" data-range="quarter">Last 90 Days</button>
                    </div>
                </div>
                <form method="post" action="<?php echo page_url; ?>Maintenance_support/filterlistmeetings">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="filter-label">Start Date</label>
                                <input type="date" class="form-control" name="startdate" id="startdate" value="<?php echo !empty($filterdata['startdate']) ? $filterdata['startdate'] : ''; ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="filter-label">End Date</label>
                                <input type="date" class="form-control" name="enddate" id="enddate" value="<?php echo !empty($filterdata['enddate']) ? $filterdata['enddate'] : ''; ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="filter-label">Conducted By</label>
                                <select class="form-control select2" name="user_id" id="user_id">
                                    <?php if ($is_management_view) { ?>
                                        <option value="">All Conductors</option>
                                    <?php } ?>
                                    <?php foreach ($users as $user) { ?>
                                        <option value="<?php echo $user['user_id']; ?>" <?php echo (!empty($filterdata['userid']) && (string) $filterdata['userid'] === (string) $user['user_id']) ? 'selected' : ''; ?>>
                                            <?php echo ucwords(strtolower(trim($user['title'] . ' ' . $user['first_name'] . ' ' . $user['last_name']))); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="filter-label">Department</label>
                                <select class="form-control select2" name="department_id" id="department_id">
                                    <option value="">All Departments</option>
                                    <?php foreach ($departments as $department) { ?>
                                        <option value="<?php echo $department['department_id']; ?>" <?php echo (isset($filterdata['department_id']) && (string) $filterdata['department_id'] !== '' && (string) $filterdata['department_id'] === (string) $department['department_id']) ? 'selected' : ''; ?>>
                                            <?php echo strtoupper($department['department']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success"><i class="fa fa-filter"></i> Apply Filters</button>
                            <a href="<?php echo page_url; ?>Maintenance_support/listmeetings" class="btn btn-default"><i class="fa fa-undo"></i> Clear Filters</a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="row">
                <div class="col-md-4 col-lg-2">
                    <div class="metric-card">
                        <div class="metric-icon teal"><i class="fa fa-handshake-o"></i></div>
                        <div class="metric-label">Meetings Logged</div>
                        <div class="metric-value"><?php echo isset($summary['total_meetings']) ? $summary['total_meetings'] : 0; ?></div>
                        <div class="metric-meta">Rows available in the current dashboard scope.</div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="metric-card">
                        <div class="metric-icon orange"><i class="fa fa-clock-o"></i></div>
                        <div class="metric-label">Collaboration Time</div>
                        <div class="metric-value"><?php echo isset($summary['total_duration_clock']) ? $summary['total_duration_clock'] : '00:00:00'; ?></div>
                        <div class="metric-meta"><?php echo isset($summary['total_duration_human']) ? $summary['total_duration_human'] : '0s'; ?> invested in discussions.</div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="metric-card">
                        <div class="metric-icon blue"><i class="fa fa-hourglass-half"></i></div>
                        <div class="metric-label">Average Duration</div>
                        <div class="metric-value"><?php echo isset($summary['average_duration_clock']) ? $summary['average_duration_clock'] : '00:00:00'; ?></div>
                        <div class="metric-meta">Average meeting size across the selected records.</div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="metric-card">
                        <div class="metric-icon navy"><i class="fa fa-sitemap"></i></div>
                        <div class="metric-label">Department Reach</div>
                        <div class="metric-value"><?php echo isset($summary['unique_departments']) ? $summary['unique_departments'] : 0; ?></div>
                        <div class="metric-meta"><?php echo isset($summary['average_meetings_per_day']) ? $summary['average_meetings_per_day'] : 0; ?> meetings per active day on average.</div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="metric-card">
                        <div class="metric-icon red"><i class="fa fa-paperclip"></i></div>
                        <div class="metric-label">Attachment Coverage</div>
                        <div class="metric-value"><?php echo isset($summary['attachment_rate']) ? $summary['attachment_rate'] : 0; ?>%</div>
                        <div class="metric-meta"><?php echo isset($summary['attachment_count']) ? $summary['attachment_count'] : 0; ?> meetings include supporting files.</div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="metric-card">
                        <div class="metric-icon sky"><i class="fa fa-sliders"></i></div>
                        <div class="metric-label">Timing Discipline</div>
                        <div class="metric-value"><?php echo isset($summary['manual_range_rate']) ? $summary['manual_range_rate'] : 0; ?>%</div>
                        <div class="metric-meta"><?php echo isset($summary['manual_range_count']) ? $summary['manual_range_count'] : 0; ?> manual-range records and <?php echo isset($summary['auto_timer_count']) ? $summary['auto_timer_count'] : 0; ?> auto-timed logs.</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="panel-shell">
                        <div class="panel-top">
                            <div>
                                <h3 class="section-title">Smart Insights</h3>
                                <p class="section-subtitle">This layer distills what management usually has to read manually from the table.</p>
                            </div>
                        </div>
                        <div class="insight-grid">
                            <?php foreach ($insights as $index => $insight) { ?>
                                <div class="insight-card">
                                    <div class="insight-index"><?php echo $index + 1; ?></div>
                                    <p><?php echo $insight; ?></p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="spotlight-card">
                        <div class="panel-top" style="margin-bottom:10px;">
                            <div>
                                <h3 class="section-title">Executive Spotlight</h3>
                                <p class="section-subtitle">Fast answers to the biggest management questions.</p>
                            </div>
                        </div>

                        <div class="spotlight-item">
                            <div class="spotlight-label">Top Department</div>
                            <div class="spotlight-value"><?php echo $top_department ? $top_department['label'] : 'No data'; ?></div>
                            <div class="spotlight-note"><?php echo $top_department ? $top_department['meeting_count'] . ' meetings for ' . $top_department['duration_human'] : 'No department trend available.'; ?></div>
                        </div>

                        <div class="spotlight-item">
                            <div class="spotlight-label">Top Conductor</div>
                            <div class="spotlight-value"><?php echo $top_conductor ? $top_conductor['label'] : 'No data'; ?></div>
                            <div class="spotlight-note"><?php echo $top_conductor ? $top_conductor['meeting_count'] . ' meetings led with ' . $top_conductor['avg_duration_clock'] . ' average duration.' : 'No conductor trend available.'; ?></div>
                        </div>

                        <div class="spotlight-item">
                            <div class="spotlight-label">Top Participant</div>
                            <div class="spotlight-value"><?php echo $top_participant ? $top_participant['label'] : 'No data'; ?></div>
                            <div class="spotlight-note"><?php echo $top_participant ? $top_participant['meeting_count'] . ' meetings received for ' . $top_participant['duration_human'] : 'No participant trend available.'; ?></div>
                        </div>

                        <div class="spotlight-item">
                            <div class="spotlight-label">Longest Meeting</div>
                            <div class="spotlight-value"><?php echo $longest_meeting ? $longest_meeting['duration_clock'] : '00:00:00'; ?></div>
                            <div class="spotlight-note">
                                <?php echo $longest_meeting ? $longest_meeting['meeting_date_display'] . ' in ' . $longest_meeting['department_name'] . '<br>' . $longest_meeting['conductor_name'] . ' with ' . $longest_meeting['participant_name'] : 'No long-meeting highlight available.'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="panel-shell chart-shell">
                        <div class="panel-top">
                            <div>
                                <h3 class="section-title">Meeting Trend</h3>
                                <p class="section-subtitle">A date-wise view of meeting count and total hours spent.</p>
                            </div>
                        </div>
                        <canvas id="meetingTrendChart"></canvas>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="panel-shell chart-shell">
                        <div class="panel-top">
                            <div>
                                <h3 class="section-title">Timing Mode Mix</h3>
                                <p class="section-subtitle">Manual ranges versus auto-timed meetings.</p>
                            </div>
                        </div>
                        <canvas id="meetingModeChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="panel-shell chart-shell">
                        <div class="panel-top">
                            <div>
                                <h3 class="section-title">Department Split</h3>
                                <p class="section-subtitle">Which departments are driving the most meeting load.</p>
                            </div>
                        </div>
                        <canvas id="departmentMixChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel-shell chart-shell">
                        <div class="panel-top">
                            <div>
                                <h3 class="section-title">Time Slot Focus</h3>
                                <p class="section-subtitle">Morning, afternoon, and evening distribution.</p>
                            </div>
                        </div>
                        <canvas id="timeSlotChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="panel-shell">
                        <div class="panel-top">
                            <div>
                                <h3 class="section-title">Top Conductors</h3>
                                <p class="section-subtitle">Who is driving collaboration and how much time it consumes.</p>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table mini-table">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Conductor</th>
                                        <th>Meetings</th>
                                        <th>Total Time</th>
                                        <th>Avg</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($conductor_breakdown)) { ?>
                                        <?php foreach ($conductor_breakdown as $index => $item) { ?>
                                            <tr>
                                                <td><span class="rank-pill">#<?php echo $index + 1; ?></span></td>
                                                <td><?php echo $item['label']; ?></td>
                                                <td><?php echo $item['meeting_count']; ?></td>
                                                <td><?php echo $item['duration_clock']; ?></td>
                                                <td><?php echo $item['avg_duration_clock']; ?></td>
                                            </tr>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <tr><td colspan="5" class="text-center text-muted">No meeting conductor data available.</td></tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel-shell">
                        <div class="panel-top">
                            <div>
                                <h3 class="section-title">Top Participants</h3>
                                <p class="section-subtitle">Who teams are engaging with most frequently.</p>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table mini-table">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Participant</th>
                                        <th>Meetings</th>
                                        <th>Total Time</th>
                                        <th>Avg</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($participant_breakdown)) { ?>
                                        <?php foreach ($participant_breakdown as $index => $item) { ?>
                                            <tr>
                                                <td><span class="rank-pill">#<?php echo $index + 1; ?></span></td>
                                                <td><?php echo $item['label']; ?></td>
                                                <td><?php echo $item['meeting_count']; ?></td>
                                                <td><?php echo $item['duration_clock']; ?></td>
                                                <td><?php echo $item['avg_duration_clock']; ?></td>
                                            </tr>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <tr><td colspan="5" class="text-center text-muted">No participant data available.</td></tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-shell">
                <div class="panel-top">
                    <div>
                        <h3 class="section-title">Department Overview</h3>
                        <p class="section-subtitle">A balanced view of load, time investment, and average meeting size by department.</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table mini-table">
                        <thead>
                            <tr>
                                <th>Department</th>
                                <th>Meeting Count</th>
                                <th>Total Time</th>
                                <th>Average Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($department_breakdown)) { ?>
                                <?php foreach ($department_breakdown as $item) { ?>
                                    <tr>
                                        <td><?php echo $item['label']; ?></td>
                                        <td><?php echo $item['meeting_count']; ?></td>
                                        <td><?php echo $item['duration_clock']; ?></td>
                                        <td><?php echo $item['avg_duration_clock']; ?></td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr><td colspan="4" class="text-center text-muted">No department breakdown available.</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="panel-shell">
                <div class="records-head">
                    <div>
                        <h3 class="section-title">Meeting Records</h3>
                        <p class="records-caption">Detailed rows stay available for audit and drill-down, but the dashboard above should make trend reading much faster.</p>
                    </div>
                    <span class="soft-badge gray"><?php echo isset($summary['total_meetings']) ? $summary['total_meetings'] : 0; ?> records</span>
                </div>

                <div class="table-responsive">
                    <table id="meetingDashboardTable" class="table table-bordered table-hover meeting-table">
                        <thead>
                            <tr>
                                <th>Sr</th>
                                <th>Date</th>
                                <th>Time Window</th>
                                <th>Duration</th>
                                <th>DF</th>
                                <th>Department</th>
                                <th>Meeting With</th>
                                <th>Conducted By</th>
                                <th>Mode</th>
                                <th>Attachment</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($tickets)) { ?>
                                <?php foreach ($tickets as $index => $ticket) { ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td data-order="<?php echo $ticket['meeting_date']; ?>">
                                            <div class="table-person"><?php echo $ticket['meeting_date_display']; ?></div>
                                            <div class="table-subtext">Logged <?php echo !empty($ticket['added_on']) ? date('h:i A', strtotime($ticket['added_on'])) : 'N/A'; ?></div>
                                        </td>
                                        <td>
                                            <div class="table-person"><?php echo $ticket['meeting_time_display'] != '' ? $ticket['meeting_time_display'] : 'N/A'; ?></div>
                                            <div class="table-subtext"><?php echo $ticket['meeting_mode']; ?></div>
                                        </td>
                                        <td>
                                            <span class="soft-badge"><?php echo $ticket['duration_clock']; ?></span>
                                            <div class="table-subtext" style="margin-top:6px;"><?php echo $ticket['duration_human']; ?></div>
                                        </td>
                                        <td>
                                            <?php if ($ticket['has_df']) { ?>
                                                <div class="df-card">
                                                    <div class="df-code"><?php echo $ticket['df_no']; ?></div>
                                                    <div class="table-subtext"><?php echo $ticket['df_description']; ?></div>
                                                </div>
                                            <?php } else { ?>
                                                <span class="soft-badge gray">No DF</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <div class="table-person"><?php echo $ticket['department_name']; ?></div>
                                        </td>
                                        <td>
                                            <div class="table-person"><?php echo $ticket['participant_name']; ?></div>
                                        </td>
                                        <td>
                                            <div class="table-person"><?php echo $ticket['conductor_name']; ?></div>
                                        </td>
                                        <td>
                                            <span class="mode-badge <?php echo $ticket['meeting_mode'] == 'Manual Range' ? 'manual' : 'auto'; ?>">
                                                <?php echo $ticket['meeting_mode']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($ticket['has_attachment']) { ?>
                                                <a href="<?php echo $ticket['attachment_url']; ?>" class="btn btn-xs btn-success btn-remarks" download><i class="fa fa-download"></i> Download</a>
                                            <?php } else { ?>
                                                <span class="soft-badge gray">No File</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <div class="remarks-preview"><?php echo $ticket['remarks_preview'] != '' ? $ticket['remarks_preview'] : 'No remarks'; ?></div>
                                            <?php if (!empty($ticket['remarks'])) { ?>
                                                <button type="button" class="btn btn-default btn-xs btn-remarks open-remarks-modal" data-remarks="<?php echo htmlspecialchars($ticket['remarks'], ENT_QUOTES, 'UTF-8'); ?>">
                                                    <i class="fa fa-file-text-o"></i> View Full
                                                </button>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php $this->load->view('common/footer'); ?>
        </div>
    </div>

    <div id="remarksModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Meeting Discussion Details</h4>
                </div>
                <div class="modal-body" id="remarksModalBody"></div>
            </div>
        </div>
    </div>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
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
    <script src="<?php echo assets_url; ?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
    <script src="<?php echo assets_url; ?>plugins/chart.js/chart.min.js"></script>

    <script>
        var meetingTrendLabels = <?php echo json_encode($trend['labels'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        var meetingTrendCounts = <?php echo json_encode($trend['meeting_counts'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        var meetingTrendHours = <?php echo json_encode($trend['duration_hours'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        var departmentChartLabels = <?php echo json_encode($department_chart_labels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        var departmentChartCounts = <?php echo json_encode($department_chart_counts, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        var departmentChartPalette = <?php echo json_encode($department_chart_palette, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        var conductorChartLabels = <?php echo json_encode($conductor_chart_labels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        var conductorChartCounts = <?php echo json_encode($conductor_chart_counts, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        var timeSlotLabels = <?php echo json_encode($time_slot_labels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        var timeSlotCounts = <?php echo json_encode($time_slot_counts, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        var modeChartLabels = <?php echo json_encode($mode_chart_labels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        var modeChartCounts = <?php echo json_encode($mode_chart_counts, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;

        function renderEmptyChart(canvasId, message) {
            var canvas = document.getElementById(canvasId);
            if (!canvas) {
                return;
            }
            canvas.outerHTML = '<div class="empty-chart">' + message + '</div>';
        }

        function formatDateForInput(dateObject) {
            var year = dateObject.getFullYear();
            var month = String(dateObject.getMonth() + 1).padStart(2, '0');
            var day = String(dateObject.getDate()).padStart(2, '0');
            return year + '-' + month + '-' + day;
        }

        $(document).ready(function() {
            $('.select2').select2({ width: '100%' });

            $('#meetingDashboardTable').DataTable({
                responsive: true,
                fixedHeader: true,
                pageLength: 25,
                order: [[1, 'desc']],
                dom: "<'row'<'col-sm-6'B><'col-sm-6'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                buttons: [
                    { extend: 'copy', className: 'btn btn-default btn-sm' },
                    { extend: 'csv', className: 'btn btn-default btn-sm' },
                    { extend: 'excel', className: 'btn btn-default btn-sm' },
                    { extend: 'pdf', className: 'btn btn-default btn-sm' },
                    { extend: 'print', className: 'btn btn-default btn-sm' }
                ]
            });

            $('.open-remarks-modal').on('click', function() {
                $('#remarksModalBody').html($(this).attr('data-remarks'));
                $('#remarksModal').modal('show');
            });

            $('.quick-range-btn').on('click', function() {
                var rangeType = $(this).data('range');
                var today = new Date();
                var startDate = new Date(today.getTime());
                var endDate = new Date(today.getTime());

                if (rangeType === 'today') {
                    startDate = today;
                    endDate = today;
                } else if (rangeType === 'last7') {
                    startDate.setDate(today.getDate() - 6);
                } else if (rangeType === 'month') {
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                } else if (rangeType === 'quarter') {
                    startDate.setDate(today.getDate() - 89);
                }

                $('#startdate').val(formatDateForInput(startDate));
                $('#enddate').val(formatDateForInput(endDate));
            });

            if (meetingTrendLabels.length > 0) {
                new Chart(document.getElementById('meetingTrendChart').getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: meetingTrendLabels,
                        datasets: [
                            {
                                label: 'Meetings',
                                data: meetingTrendCounts,
                                borderColor: '#0f766e',
                                backgroundColor: 'rgba(15, 118, 110, 0.10)',
                                borderWidth: 3,
                                pointBackgroundColor: '#0f766e',
                                pointRadius: 4,
                                fill: true,
                                yAxisID: 'y-axis-meetings'
                            },
                            {
                                label: 'Hours Spent',
                                data: meetingTrendHours,
                                borderColor: '#f59e0b',
                                backgroundColor: 'rgba(245, 158, 11, 0.08)',
                                borderWidth: 2,
                                borderDash: [6, 4],
                                pointBackgroundColor: '#f59e0b',
                                pointRadius: 4,
                                fill: false,
                                yAxisID: 'y-axis-hours'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            position: 'bottom'
                        },
                        tooltips: {
                            mode: 'index',
                            intersect: false
                        },
                        scales: {
                            xAxes: [{
                                gridLines: {
                                    display: false
                                }
                            }],
                            yAxes: [{
                                id: 'y-axis-meetings',
                                position: 'left',
                                ticks: {
                                    beginAtZero: true,
                                    precision: 0
                                },
                                gridLines: {
                                    color: 'rgba(19, 34, 56, 0.08)'
                                }
                            }, {
                                id: 'y-axis-hours',
                                position: 'right',
                                ticks: {
                                    beginAtZero: true
                                },
                                gridLines: {
                                    drawOnChartArea: false
                                }
                            }]
                        }
                    }
                });
            } else {
                renderEmptyChart('meetingTrendChart', 'No meeting trend data is available for the current filter selection.');
            }

            if ((modeChartCounts[0] + modeChartCounts[1]) > 0) {
                new Chart(document.getElementById('meetingModeChart').getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: modeChartLabels,
                        datasets: [{
                            data: modeChartCounts,
                            backgroundColor: ['#f59e0b', '#0ea5e9'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            position: 'bottom'
                        }
                    }
                });
            } else {
                renderEmptyChart('meetingModeChart', 'No timing mode data is available yet.');
            }

            if (departmentChartLabels.length > 0) {
                new Chart(document.getElementById('departmentMixChart').getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: departmentChartLabels,
                        datasets: [{
                            data: departmentChartCounts,
                            backgroundColor: departmentChartPalette,
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            position: 'bottom'
                        }
                    }
                });
            } else {
                renderEmptyChart('departmentMixChart', 'No department distribution data is available for the current selection.');
            }

            if (timeSlotLabels.length > 0 && timeSlotCounts.some(function(item) { return item > 0; })) {
                new Chart(document.getElementById('timeSlotChart').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: timeSlotLabels,
                        datasets: [{
                            label: 'Meeting Count',
                            data: timeSlotCounts,
                            backgroundColor: ['#0f766e', '#f59e0b', '#1d4ed8']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            display: false
                        },
                        scales: {
                            xAxes: [{
                                gridLines: {
                                    display: false
                                }
                            }],
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    precision: 0
                                },
                                gridLines: {
                                    color: 'rgba(19, 34, 56, 0.08)'
                                }
                            }]
                        }
                    }
                });
            } else {
                renderEmptyChart('timeSlotChart', 'No time-slot distribution data is available for the current selection.');
            }
        });
    </script>
</body>
</html>
