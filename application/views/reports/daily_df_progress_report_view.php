<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$company_q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
$brand_row = $company_q->num_rows() > 0 ? $company_q->row() : null;
$theme_color = !empty($brand_row->colorcode) ? $brand_row->colorcode : '#0f766e';

$format_date = function ($value, $format = 'd M Y') {
    if (empty($value) || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
        return '--';
    }
    $timestamp = strtotime($value);
    return $timestamp ? date($format, $timestamp) : '--';
};

$status_class_map = [
    'closed_today' => 'status-closed',
    'on_hold' => 'status-hold',
    'critical' => 'status-critical',
    'delayed' => 'status-delayed',
    'updated' => 'status-updated',
    'stalled' => 'status-stalled'
];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo html_escape($page_title); ?></title>

    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />

    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            background:
                radial-gradient(circle at top right, rgba(15, 118, 110, 0.12), transparent 34%),
                linear-gradient(180deg, #f5f8fb 0%, #eef3f8 100%);
        }

        .report-shell {
            margin-top: 22px;
            margin-bottom: 28px;
        }

        .report-hero {
            background: linear-gradient(135deg, <?php echo $theme_color; ?> 0%, #102a43 100%);
            border-radius: 22px;
            padding: 28px;
            color: #fff;
            box-shadow: 0 20px 45px rgba(16, 42, 67, 0.22);
            position: relative;
            overflow: hidden;
        }

        .report-hero:before,
        .report-hero:after {
            content: "";
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
        }

        .report-hero:before {
            width: 220px;
            height: 220px;
            top: -80px;
            right: -50px;
        }

        .report-hero:after {
            width: 140px;
            height: 140px;
            bottom: -55px;
            left: -25px;
        }

        .hero-title {
            margin: 0;
            font-size: 30px;
            font-weight: 900;
            letter-spacing: .2px;
            color: #fff !important;
        }

        .hero-subtitle {
            margin-top: 8px;
            font-size: 14px;
            opacity: .92;
            max-width: 760px;
            color: rgba(255, 255, 255, 0.92);
        }

        .hero-pill-row {
            margin-top: 14px;
        }

        .hero-pill {
            display: inline-block;
            margin-right: 10px;
            margin-bottom: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .filter-card,
        .kpi-card,
        .insight-card,
        .chart-card,
        .attention-card,
        .table-card,
        .user-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #e9eef5;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
        }

        .filter-card {
            padding: 20px;
            height: 100%;
        }

        .filter-title {
            font-size: 15px;
            font-weight: 900;
            color: #102a43;
            margin-bottom: 14px;
        }

        .btn-hero-primary {
            background: #102a43;
            border-color: #102a43;
            color: #fff;
            font-weight: 800;
        }

        .btn-hero-primary:hover,
        .btn-hero-primary:focus {
            background: #081b33;
            border-color: #081b33;
            color: #fff;
        }

        .btn-hero-secondary {
            background: transparent;
            border-color: rgba(16, 42, 67, 0.18);
            color: #102a43;
            font-weight: 800;
        }

        .kpi-grid {
            margin-top: 22px;
        }

        .kpi-card {
            padding: 18px;
            min-height: 138px;
            position: relative;
            overflow: hidden;
            margin-bottom: 18px;
        }

        .kpi-card:after {
            content: "";
            position: absolute;
            right: -18px;
            bottom: -18px;
            width: 84px;
            height: 84px;
            border-radius: 50%;
            background: rgba(15, 118, 110, 0.08);
        }

        .kpi-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: <?php echo $theme_color; ?>;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-bottom: 14px;
        }

        .kpi-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #6b7280;
            font-weight: 800;
        }

        .kpi-value {
            font-size: 30px;
            line-height: 1.05;
            font-weight: 900;
            color: #102a43;
            margin-top: 10px;
        }

        .kpi-foot {
            font-size: 12px;
            color: #7b8794;
            margin-top: 8px;
        }

        .insight-card {
            padding: 18px 20px;
            margin-top: 6px;
            margin-bottom: 18px;
        }

        .insight-title {
            font-weight: 900;
            color: #102a43;
            margin-bottom: 10px;
        }

        .insight-line {
            font-size: 13px;
            color: #334e68;
            margin-bottom: 8px;
            padding-left: 18px;
            position: relative;
        }

        .insight-line:before {
            content: "";
            position: absolute;
            left: 0;
            top: 7px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: <?php echo $theme_color; ?>;
        }

        .chart-card,
        .attention-card {
            padding: 20px;
            height: 100%;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 900;
            color: #102a43;
            margin: 0 0 6px;
        }

        .section-subtitle {
            font-size: 12px;
            color: #7b8794;
            margin-bottom: 16px;
        }

        .user-card {
            padding: 20px;
            height: 100%;
            margin-bottom: 20px;
        }

        .user-list {
            list-style: none;
            padding-left: 0;
            margin-bottom: 0;
        }

        .user-item {
            border: 1px solid #edf2f7;
            border-radius: 14px;
            background: #fbfdff;
            padding: 12px 14px;
            margin-bottom: 12px;
        }

        .user-item:last-child {
            margin-bottom: 0;
        }

        .user-name {
            font-weight: 900;
            color: #102a43;
            margin-bottom: 5px;
        }

        .user-meta {
            font-size: 12px;
            color: #64748b;
            line-height: 1.5;
        }

        .user-status-active {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .user-status-idle {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .user-table thead th {
            background: #102a43 !important;
            color: #fff !important;
            text-align: center;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .user-table tbody td {
            vertical-align: top !important;
            font-size: 12px;
            color: #334155;
        }

        .user-row-active td { background: #f0fdf4 !important; }
        .user-row-idle td { background: #fff7f7 !important; }

        .attention-list {
            list-style: none;
            padding-left: 0;
            margin-bottom: 0;
        }

        .attention-item {
            border: 1px solid #edf2f7;
            border-radius: 14px;
            padding: 12px 14px;
            margin-bottom: 12px;
            background: #f9fbfd;
        }

        .attention-item:last-child {
            margin-bottom: 0;
        }

        .attention-df {
            font-weight: 900;
            color: #102a43;
        }

        .attention-meta {
            color: #52606d;
            font-size: 12px;
            margin-top: 4px;
        }

        .attention-badges {
            margin-top: 8px;
        }

        .mini-pill,
        .status-pill,
        .action-pill {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            margin-right: 6px;
            margin-bottom: 6px;
        }

        .mini-blue { background: #e7f0ff; color: #1d4ed8; }
        .mini-green { background: #eafaf0; color: #15803d; }
        .mini-amber { background: #fff7e8; color: #b45309; }
        .mini-red { background: #feefef; color: #b91c1c; }
        .mini-slate { background: #eef2f6; color: #475569; }

        .table-card {
            padding: 18px;
        }

        .table-head {
            margin-bottom: 14px;
        }

        .table-head h4 {
            margin: 0;
            font-weight: 900;
            color: #102a43;
        }

        .table-head p {
            margin-top: 6px;
            color: #6b7280;
            font-size: 13px;
        }

        table.df-progress-table thead th {
            background: <?php echo $theme_color; ?> !important;
            color: #fff !important;
            border-color: <?php echo $theme_color; ?> !important;
            text-align: center;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        table.df-progress-table tbody td {
            vertical-align: top !important;
            font-size: 12px;
            color: #334155;
        }

        .row-critical td { background: #fff1f2 !important; }
        .row-delayed td { background: #fff8eb !important; }
        .row-updated td { background: #f1fbf9 !important; }
        .row-closed td { background: #eefaf1 !important; }
        .row-hold td { background: #f4f5f7 !important; }
        .row-stalled td { background: #fbfcfd !important; }

        .df-code {
            font-size: 18px;
            font-weight: 900;
            color: #102a43;
            margin-bottom: 5px;
        }

        .df-desc {
            color: #334e68;
            font-weight: 700;
            line-height: 1.45;
            margin-bottom: 8px;
        }

        .meta-line {
            color: #6b7280;
            margin-bottom: 5px;
            line-height: 1.4;
        }

        .meta-line strong {
            color: #102a43;
        }

        .status-pill {
            border: 1px solid transparent;
        }

        .status-critical { background: #fee2e2; color: #b91c1c; border-color: #fecaca; }
        .status-delayed { background: #ffedd5; color: #c2410c; border-color: #fed7aa; }
        .status-updated { background: #dcfce7; color: #15803d; border-color: #bbf7d0; }
        .status-closed { background: #dbeafe; color: #1d4ed8; border-color: #bfdbfe; }
        .status-hold { background: #e5e7eb; color: #4b5563; border-color: #d1d5db; }
        .status-stalled { background: #f1f5f9; color: #475569; border-color: #e2e8f0; }

        .progress-shell {
            margin-bottom: 10px;
        }

        .progress-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }

        .progress-label {
            font-weight: 900;
            color: #102a43;
        }

        .progress-value {
            font-weight: 900;
            color: <?php echo $theme_color; ?>;
        }

        .health-bar {
            width: 100%;
            height: 10px;
            background: #e5edf5;
            border-radius: 999px;
            overflow: hidden;
        }

        .health-bar span {
            display: block;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, <?php echo $theme_color; ?> 0%, #0ea5e9 100%);
        }

        .event-card {
            border: 1px solid #edf2f7;
            border-radius: 12px;
            padding: 10px 11px;
            margin-bottom: 8px;
            background: #fff;
        }

        .event-card:last-child {
            margin-bottom: 0;
        }

        .event-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 5px;
        }

        .event-type {
            font-weight: 900;
            color: #102a43;
        }

        .event-time {
            color: #64748b;
            font-size: 11px;
            white-space: nowrap;
        }

        .event-task {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .event-meta {
            color: #64748b;
            font-size: 11px;
            margin-bottom: 4px;
        }

        .event-message {
            color: #475569;
            line-height: 1.45;
        }

        .empty-note {
            color: #7b8794;
            font-style: italic;
        }

        .latest-time {
            font-weight: 900;
            color: #102a43;
            margin-bottom: 6px;
        }

        .latest-summary {
            color: #334e68;
            line-height: 1.5;
            margin-bottom: 5px;
        }

        .latest-actor {
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .focus-card {
            border: 1px solid #edf2f7;
            border-radius: 12px;
            padding: 12px;
            background: #fff;
            margin-bottom: 10px;
        }

        .focus-task {
            font-weight: 900;
            color: #102a43;
            margin-bottom: 5px;
        }

        .focus-meta {
            color: #64748b;
            line-height: 1.45;
        }

        .delay-card {
            border: 1px dashed #f59e0b;
            background: #fffdf8;
            border-radius: 11px;
            padding: 9px 10px;
            margin-bottom: 8px;
        }

        .delay-card:last-child {
            margin-bottom: 0;
        }

        .delay-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            font-weight: 800;
            color: #92400e;
        }

        .delay-meta {
            color: #7c2d12;
            font-size: 11px;
            margin-top: 4px;
        }

        .action-pill {
            text-decoration: none !important;
            border: 1px solid transparent;
        }

        .action-blue { background: #e0f2fe; color: #075985; border-color: #bae6fd; }
        .action-green { background: #dcfce7; color: #166534; border-color: #bbf7d0; }
        .action-amber { background: #fef3c7; color: #92400e; border-color: #fde68a; }

        .action-pill:hover,
        .action-pill:focus {
            opacity: .88;
            text-decoration: none;
        }

        .datatable-tools .dt-buttons {
            margin-bottom: 12px;
        }

        .datatable-tools .dataTables_filter input {
            border-radius: 999px;
            border: 1px solid #dbe4ee;
            padding: 6px 12px;
        }

        .datatable-tools .dataTables_length select {
            border-radius: 10px;
            border: 1px solid #dbe4ee;
        }

        @media (max-width: 767px) {
            .hero-title {
                font-size: 24px;
            }

            .report-hero,
            .filter-card,
            .kpi-card,
            .insight-card,
            .chart-card,
            .attention-card,
            .table-card {
                border-radius: 16px;
            }
        }
    </style>
</head>
<body>
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>

    <div class="wrapper">
        <div class="container-fluid report-shell">
            <div class="row">
                <div class="col-lg-8">
                    <div class="report-hero">
                        <h1 class="hero-title"><?php echo html_escape($page_title); ?></h1>
                        <div class="hero-subtitle">
                            A management-first daily DF pulse showing project movement on the selected date, current task health, open delays, help-ticket pressure, and the next attention point for every visible DF.
                        </div>
                        <div class="hero-pill-row">
                            <span class="hero-pill"><i class="fa fa-calendar"></i> Report Date: <?php echo html_escape($report_date_display); ?></span>
                            <span class="hero-pill"><i class="fa fa-filter"></i> Scope: <?php echo html_escape($scope_label); ?></span>
                            <span class="hero-pill"><i class="fa fa-line-chart"></i> Trackability: Daily movement + current health</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="filter-card">
                        <div class="filter-title">Report Controls</div>
                        <form method="get" action="<?php echo page_url; ?>Dashboard/daily_df_progress_report">
                            <div class="form-group">
                                <label>Report Date</label>
                                <input type="date" name="report_date" class="form-control" value="<?php echo html_escape($report_date); ?>">
                            </div>
                            <div class="form-group">
                                <label>Visibility Scope</label>
                                <select name="scope" class="form-control">
                                    <?php foreach ($scope_options as $scope_key => $scope_text): ?>
                                        <option value="<?php echo html_escape($scope_key); ?>" <?php echo ($scope === $scope_key) ? 'selected' : ''; ?>>
                                            <?php echo html_escape($scope_text); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <button type="submit" class="btn btn-hero-primary btn-block">Refresh Report</button>
                                </div>
                                <div class="col-xs-6">
                                    <a href="<?php echo page_url; ?>Dashboard/daily_df_progress_report" class="btn btn-hero-secondary btn-block">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row kpi-grid">
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="kpi-card">
                        <div class="kpi-icon"><i class="fa fa-folder-open-o"></i></div>
                        <div class="kpi-label">Active DF</div>
                        <div class="kpi-value"><?php echo (int) $summary['active_df_count']; ?></div>
                        <div class="kpi-foot">Running DFs still under execution</div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="kpi-card">
                        <div class="kpi-icon"><i class="fa fa-random"></i></div>
                        <div class="kpi-label">DF Moved</div>
                        <div class="kpi-value"><?php echo (int) $summary['movement_df_count']; ?></div>
                        <div class="kpi-foot">DFs with visible movement on selected date</div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="kpi-card">
                        <div class="kpi-icon"><i class="fa fa-check-circle"></i></div>
                        <div class="kpi-label">Tasks Completed</div>
                        <div class="kpi-value"><?php echo (int) $summary['completed_task_count']; ?></div>
                        <div class="kpi-foot">Task closures captured on selected date</div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="kpi-card">
                        <div class="kpi-icon"><i class="fa fa-pause-circle"></i></div>
                        <div class="kpi-label">No Update</div>
                        <div class="kpi-value"><?php echo (int) $summary['no_update_df_count']; ?></div>
                        <div class="kpi-foot">Running DFs without visible movement</div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="kpi-card">
                        <div class="kpi-icon"><i class="fa fa-exclamation-triangle"></i></div>
                        <div class="kpi-label">Open Delays</div>
                        <div class="kpi-value"><?php echo (int) $summary['open_delayed_task_count']; ?></div>
                        <div class="kpi-foot">Current delayed tasks across visible DFs</div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="kpi-card">
                        <div class="kpi-icon"><i class="fa fa-life-ring"></i></div>
                        <div class="kpi-label">Open Tickets</div>
                        <div class="kpi-value"><?php echo (int) $summary['open_ticket_count']; ?></div>
                        <div class="kpi-foot">Active help-ticket pressure on visible DFs</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="kpi-card">
                        <div class="kpi-icon"><i class="fa fa-users"></i></div>
                        <div class="kpi-label">Users With Active DF Tasks</div>
                        <div class="kpi-value"><?php echo (int) $user_summary['users_with_active_df_tasks']; ?></div>
                        <div class="kpi-foot">Only `user_status = 1` users with assigned running DF tasks</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="kpi-card">
                        <div class="kpi-icon"><i class="fa fa-play-circle"></i></div>
                        <div class="kpi-label">Working Today</div>
                        <div class="kpi-value"><?php echo (int) $user_summary['users_working_today']; ?></div>
                        <div class="kpi-foot">Users with visible progress / completion activity</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="kpi-card">
                        <div class="kpi-icon"><i class="fa fa-user-times"></i></div>
                        <div class="kpi-label">No Visible Update</div>
                        <div class="kpi-value"><?php echo (int) $user_summary['users_no_update_today']; ?></div>
                        <div class="kpi-foot">Users holding running DF tasks but no visible daily movement</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="kpi-card">
                        <div class="kpi-icon"><i class="fa fa-tasks"></i></div>
                        <div class="kpi-label">Open Assigned Tasks</div>
                        <div class="kpi-value"><?php echo (int) $user_summary['total_open_assigned_tasks']; ?></div>
                        <div class="kpi-foot">Open tasks currently sitting with active visible assignees</div>
                    </div>
                </div>
            </div>

            <div class="insight-card">
                <div class="insight-title">Management Insight Layer</div>
                <?php foreach ($insights as $insight): ?>
                    <div class="insight-line"><?php echo html_escape($insight); ?></div>
                <?php endforeach; ?>
            </div>

            <div class="row">
                <div class="col-lg-7">
                    <div class="chart-card">
                        <div class="section-title">Department Activity Pulse</div>
                        <div class="section-subtitle">Every visible movement event on the selected date, grouped by department.</div>
                        <div style="height: 320px; position: relative;">
                            <canvas id="departmentActivityChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="attention-card">
                        <div class="section-title">Priority Attention Queue</div>
                        <div class="section-subtitle">High-risk DFs ranked by open delay pressure, tickets, and no-update risk.</div>
                        <ul class="attention-list">
                            <?php if (empty($top_attention)): ?>
                                <li class="attention-item">
                                    <div class="attention-df">No high-risk DF found</div>
                                    <div class="attention-meta">The current filter returned no DF records for attention.</div>
                                </li>
                            <?php else: ?>
                                <?php foreach ($top_attention as $attention): ?>
                                    <li class="attention-item">
                                        <div class="attention-df"><?php echo html_escape($attention['df_no']); ?></div>
                                        <div class="attention-meta">
                                            <?php echo html_escape($attention['df_description']); ?><br>
                                            <?php echo html_escape($attention['company_name'] !== '' ? $attention['company_name'] : 'Company not mapped'); ?>
                                        </div>
                                        <div class="attention-badges">
                                            <span class="mini-pill mini-red">Delayed <?php echo (int) $attention['open_delayed_count']; ?></span>
                                            <span class="mini-pill mini-amber">Tickets <?php echo (int) $attention['open_ticket_count']; ?></span>
                                            <span class="mini-pill mini-blue">Touched <?php echo (int) $attention['today_touched_count']; ?></span>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <div class="chart-card">
                        <div class="section-title">User Activity Watch</div>
                        <div class="section-subtitle">Daily working vs no-update visibility for active users holding running DF tasks.</div>
                        <div style="height: 320px; position: relative;">
                            <canvas id="userActivityChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="user-card">
                        <div class="section-title">People Working Today</div>
                        <div class="section-subtitle">Visible daily progress based on task updates, remark trail, or completion on running DF tasks.</div>
                        <ul class="user-list">
                            <?php if (empty($top_active_users)): ?>
                                <li class="user-item">
                                    <div class="user-name">No working activity captured</div>
                                    <div class="user-meta">No active assignee showed visible task progress on the selected date.</div>
                                </li>
                            <?php else: ?>
                                <?php foreach ($top_active_users as $user_row): ?>
                                    <li class="user-item">
                                        <div class="user-name"><?php echo html_escape($user_row['user_name']); ?></div>
                                        <div class="user-meta">
                                            <?php echo html_escape($user_row['department_label']); ?><br>
                                            Active DFs: <?php echo (int) $user_row['assigned_df_count']; ?> | Open Tasks: <?php echo (int) $user_row['open_assigned_tasks']; ?><br>
                                            Worked Tasks Today: <?php echo (int) $user_row['today_working_task_count']; ?> | Completed: <?php echo (int) $user_row['today_completion_count']; ?> | Remarks: <?php echo (int) $user_row['today_remark_count']; ?>
                                        </div>
                                        <div style="margin-top: 8px;">
                                            <span class="mini-pill user-status-active"><?php echo html_escape($user_row['status_label']); ?></span>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="user-card">
                        <div class="section-title">People Needing Follow-up</div>
                        <div class="section-subtitle">Active users with assigned running DF tasks but no visible progress signal on the selected date.</div>
                        <ul class="user-list">
                            <?php if (empty($idle_users)): ?>
                                <li class="user-item">
                                    <div class="user-name">No idle user found</div>
                                    <div class="user-meta">Every active assignee with running DF tasks showed some visible activity.</div>
                                </li>
                            <?php else: ?>
                                <?php foreach ($idle_users as $user_row): ?>
                                    <li class="user-item">
                                        <div class="user-name"><?php echo html_escape($user_row['user_name']); ?></div>
                                        <div class="user-meta">
                                            <?php echo html_escape($user_row['department_label']); ?><br>
                                            Active DFs: <?php echo (int) $user_row['assigned_df_count']; ?> | Open Tasks: <?php echo (int) $user_row['open_assigned_tasks']; ?><br>
                                            Delayed Open Tasks: <?php echo (int) $user_row['delayed_open_tasks']; ?> | Max Delay: <?php echo (int) $user_row['max_delay_days']; ?>d
                                        </div>
                                        <div style="margin-top: 8px;">
                                            <span class="mini-pill user-status-idle"><?php echo html_escape($user_row['status_label']); ?></span>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="table-card" style="margin-bottom: 18px;">
                <div class="table-head">
                    <h4>User Accountability Sheet</h4>
                    <p>Only active users with assigned tasks on running DFs are shown here. This makes it easy for management to see who worked and who needs follow-up on the selected date.</p>
                </div>

                <div class="table-responsive datatable-tools">
                    <table id="dailyDfUserTable" class="table table-bordered table-striped user-table">
                        <thead>
                            <tr>
                                <th>Sr. No.</th>
                                <th>User</th>
                                <th>Current Load</th>
                                <th>Daily Working Signal</th>
                                <th>Latest User Activity</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($user_activity)): ?>
                                <?php foreach ($user_activity as $user_index => $user_row): ?>
                                    <tr class="<?php echo !empty($user_row['has_worked_today']) ? 'user-row-active' : 'user-row-idle'; ?>">
                                        <td class="text-center"><?php echo $user_index + 1; ?></td>
                                        <td>
                                            <div class="user-name"><?php echo html_escape($user_row['user_name']); ?></div>
                                            <div class="user-meta"><?php echo html_escape($user_row['department_label']); ?></div>
                                        </td>
                                        <td>
                                            <div class="meta-line"><strong>Running DFs:</strong> <?php echo (int) $user_row['assigned_df_count']; ?></div>
                                            <div class="meta-line"><strong>Total Assigned Tasks:</strong> <?php echo (int) $user_row['total_assigned_tasks']; ?></div>
                                            <div class="meta-line"><strong>Open Tasks:</strong> <?php echo (int) $user_row['open_assigned_tasks']; ?></div>
                                            <div class="meta-line"><strong>Delayed Open Tasks:</strong> <?php echo (int) $user_row['delayed_open_tasks']; ?> | <strong>Max Delay:</strong> <?php echo (int) $user_row['max_delay_days']; ?>d</div>
                                        </td>
                                        <td>
                                            <span class="mini-pill mini-blue">Worked Tasks <?php echo (int) $user_row['today_working_task_count']; ?></span>
                                            <span class="mini-pill mini-green">Completed <?php echo (int) $user_row['today_completion_count']; ?></span>
                                            <span class="mini-pill mini-amber">Remarks <?php echo (int) $user_row['today_remark_count']; ?></span>
                                            <span class="mini-pill mini-slate">Assigned <?php echo (int) $user_row['today_assignment_count']; ?></span>
                                        </td>
                                        <td>
                                            <div class="latest-time"><?php echo $format_date($user_row['latest_activity_on'], 'd M Y h:i A'); ?></div>
                                            <div class="latest-summary"><?php echo html_escape($user_row['latest_activity_summary']); ?></div>
                                        </td>
                                        <td class="text-center">
                                            <span class="mini-pill <?php echo !empty($user_row['has_worked_today']) ? 'user-status-active' : 'user-status-idle'; ?>">
                                                <?php echo html_escape($user_row['status_label']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No active user with running DF task assignment found for the selected report.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="table-card">
                <div class="table-head">
                    <h4>DF-by-DF Daily Control Sheet</h4>
                    <p>Use this to review each DF’s selected-date movement, current execution health, current attention point, and direct action links. Export buttons are available for management circulation.</p>
                </div>

                <div class="table-responsive datatable-tools">
                    <table id="dailyDfProgressTable" class="table table-bordered table-striped df-progress-table">
                        <thead>
                            <tr>
                                <th>Sr. No.</th>
                                <th>DF Snapshot</th>
                                <th>Daily Movement</th>
                                <th>Current Health</th>
                                <th>Latest Recorded Activity</th>
                                <th>Next Attention</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rows)): ?>
                                <?php foreach ($rows as $index => $row): ?>
                                    <?php $row_class = isset($status_class_map[$row['status_key']]) ? 'row-' . str_replace('status-', '', $status_class_map[$row['status_key']]) : 'row-stalled'; ?>
                                    <tr class="<?php echo $row_class; ?>">
                                        <td class="text-center"><?php echo $index + 1; ?></td>
                                        <td>
                                            <div class="df-code"><?php echo html_escape($row['df_no']); ?></div>
                                            <div class="df-desc"><?php echo html_escape($row['df_description']); ?></div>
                                            <div class="meta-line"><strong>Company:</strong> <?php echo html_escape($row['company_name'] !== '' ? $row['company_name'] : 'Not mapped'); ?></div>
                                            <div class="meta-line"><strong>Marketing:</strong> <?php echo html_escape($row['marketing_owner']); ?></div>
                                            <div class="meta-line"><strong>Machine:</strong> <?php echo html_escape($row['machine_name'] !== '' ? $row['machine_name'] : 'Not mapped'); ?></div>
                                            <div class="meta-line"><strong>Released:</strong> <?php echo $format_date($row['release_date']); ?></div>
                                            <div class="meta-line">
                                                <span class="status-pill <?php echo html_escape($status_class_map[$row['status_key']]); ?>">
                                                    <?php echo html_escape($row['status_label']); ?>
                                                </span>
                                                <?php if ((int) $row['on_hold'] === 1): ?>
                                                    <span class="status-pill status-hold">On Hold</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="margin-bottom: 8px;">
                                                <span class="mini-pill mini-blue">Touched <?php echo (int) $row['today_touched_count']; ?></span>
                                                <span class="mini-pill mini-green">Completed <?php echo (int) $row['today_completed_count']; ?></span>
                                                <span class="mini-pill mini-amber">Remarks <?php echo (int) $row['today_remark_count']; ?></span>
                                                <span class="mini-pill mini-slate">Assigned <?php echo (int) $row['today_assignment_count']; ?></span>
                                                <?php if ((int) $row['today_new_ticket_count'] > 0): ?>
                                                    <span class="mini-pill mini-red">Tickets <?php echo (int) $row['today_new_ticket_count']; ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <?php if (empty($row['today_events'])): ?>
                                                <div class="empty-note">No visible movement captured on the selected date.</div>
                                            <?php else: ?>
                                                <?php foreach (array_slice($row['today_events'], 0, 4) as $event): ?>
                                                    <div class="event-card">
                                                        <div class="event-top">
                                                            <div class="event-type"><?php echo html_escape($event['type']); ?></div>
                                                            <div class="event-time"><?php echo $format_date($event['time'], 'd M h:i A'); ?></div>
                                                        </div>
                                                        <div class="event-task"><?php echo html_escape($event['task_name']); ?></div>
                                                        <div class="event-meta"><?php echo html_escape($event['owner']); ?> | <?php echo html_escape($event['department']); ?></div>
                                                        <div class="event-message"><?php echo html_escape($event['message']); ?></div>
                                                    </div>
                                                <?php endforeach; ?>
                                                <?php if (count($row['today_events']) > 4): ?>
                                                    <div class="empty-note"><?php echo count($row['today_events']) - 4; ?> more events are available in the exported data/search results.</div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="progress-shell">
                                                <div class="progress-top">
                                                    <div class="progress-label">Completion</div>
                                                    <div class="progress-value"><?php echo (int) $row['completion_pct']; ?>%</div>
                                                </div>
                                                <div class="health-bar">
                                                    <span style="width: <?php echo max(0, min(100, (int) $row['completion_pct'])); ?>%;"></span>
                                                </div>
                                            </div>

                                            <div class="meta-line"><strong>Total Tasks:</strong> <?php echo (int) $row['total_tasks']; ?></div>
                                            <div class="meta-line"><strong>Completed:</strong> <?php echo (int) $row['completed_tasks']; ?> | <strong>Open:</strong> <?php echo (int) $row['open_tasks']; ?></div>
                                            <div class="meta-line"><strong>Planned Close:</strong> <?php echo $format_date($row['planned_close_date']); ?></div>
                                            <div class="meta-line"><strong>Dispatch Due:</strong> <?php echo $format_date($row['dispatch_due_date']); ?></div>

                                            <div style="margin-top: 8px;">
                                                <span class="mini-pill mini-red">Delayed <?php echo (int) $row['open_delayed_count']; ?></span>
                                                <span class="mini-pill mini-amber">Max Delay <?php echo (int) $row['max_delay_days']; ?>d</span>
                                                <span class="mini-pill mini-blue">Open Tickets <?php echo (int) $row['open_ticket_count']; ?></span>
                                            </div>

                                            <?php if ((int) $row['closed_late_today_count'] > 0): ?>
                                                <div class="meta-line" style="margin-top: 8px;"><strong>Late Closures On Date:</strong> <?php echo (int) $row['closed_late_today_count']; ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="latest-time"><?php echo $format_date($row['latest_activity_on'], 'd M Y h:i A'); ?></div>
                                            <div class="latest-summary"><?php echo html_escape($row['latest_activity_summary']); ?></div>
                                            <div class="latest-actor"><?php echo html_escape($row['latest_activity_actor']); ?></div>

                                            <?php if (!empty($row['today_department_counts'])): ?>
                                                <div style="margin-top: 10px;">
                                                    <?php foreach ($row['today_department_counts'] as $dept_name => $dept_count): ?>
                                                        <span class="mini-pill mini-slate"><?php echo html_escape($dept_name); ?> <?php echo (int) $dept_count; ?></span>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row['next_due_task'] !== ''): ?>
                                                <div class="focus-card">
                                                    <div class="focus-task"><?php echo html_escape($row['next_due_task']); ?></div>
                                                    <div class="focus-meta">
                                                        Due <?php echo $format_date($row['next_due_date']); ?><br>
                                                        Owner: <?php echo html_escape($row['next_due_owner']); ?>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="empty-note">No open task pending for follow-up.</div>
                                            <?php endif; ?>

                                            <?php if (!empty($row['delayed_tasks'])): ?>
                                                <?php foreach (array_slice($row['delayed_tasks'], 0, 3) as $delay): ?>
                                                    <div class="delay-card">
                                                        <div class="delay-head">
                                                            <span><?php echo html_escape($delay['task_name']); ?></span>
                                                            <span><?php echo (int) $delay['delay_days']; ?>d</span>
                                                        </div>
                                                        <div class="delay-meta">
                                                            <?php echo html_escape($delay['department']); ?> | <?php echo html_escape($delay['owner']); ?><br>
                                                            Due <?php echo $format_date($delay['due_date']); ?>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?php echo page_url; ?>Task/finalgantchartWithDetails/<?php echo (int) $row['id']; ?>" target="_blank" class="action-pill action-blue">Gantt</a><br>
                                            <a href="<?php echo page_url; ?>Task/viewdfmeetingmom/<?php echo (int) $row['id']; ?>" target="_blank" class="action-pill action-green">DF MOM</a><br>
                                            <?php if ((int) $row['open_ticket_count'] > 0): ?>
                                                <a href="<?php echo page_url; ?>Task/helpticketsforyou/ALL/<?php echo (int) $row['id']; ?>" target="_blank" class="action-pill action-amber">Tickets</a>
                                            <?php else: ?>
                                                <span class="action-pill action-amber">No Ticket</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No DF data found for the selected filter.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php $this->load->view('common/footer'); ?>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.bootstrap.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.buttons.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/jszip.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/pdfmake.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/vfs_fonts.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.html5.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.print.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#dailyDfProgressTable').DataTable({
                responsive: true,
                pageLength: 25,
                order: [[0, 'asc']],
                dom: 'Bflrtip',
                buttons: [
                    { extend: 'copyHtml5', className: 'btn btn-default btn-sm' },
                    { extend: 'excelHtml5', className: 'btn btn-success btn-sm', title: 'Daily_DF_Progress_<?php echo html_escape($report_date); ?>' },
                    { extend: 'csvHtml5', className: 'btn btn-info btn-sm', title: 'Daily_DF_Progress_<?php echo html_escape($report_date); ?>' },
                    { extend: 'print', className: 'btn btn-primary btn-sm', title: '<?php echo html_escape($page_title); ?>' }
                ]
            });

            var departmentLabels = <?php echo json_encode(array_map(function ($row) { return $row['department']; }, $department_activity)); ?>;
            var departmentCounts = <?php echo json_encode(array_map(function ($row) { return (int) $row['count']; }, $department_activity)); ?>;

            var chartCanvas = document.getElementById('departmentActivityChart');
            if (chartCanvas && departmentLabels.length > 0) {
                new Chart(chartCanvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: departmentLabels,
                        datasets: [{
                            label: 'Movement Events',
                            data: departmentCounts,
                            backgroundColor: '<?php echo $theme_color; ?>',
                            borderRadius: 10,
                            maxBarThickness: 36
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            } else if (chartCanvas) {
                chartCanvas.parentNode.innerHTML = '<div class="empty-note">No department movement was captured for the selected date.</div>';
            }

            $('#dailyDfUserTable').DataTable({
                responsive: true,
                pageLength: 15,
                order: [[0, 'asc']],
                dom: 'Bflrtip',
                buttons: [
                    { extend: 'copyHtml5', className: 'btn btn-default btn-sm' },
                    { extend: 'excelHtml5', className: 'btn btn-success btn-sm', title: 'Daily_DF_User_Activity_<?php echo html_escape($report_date); ?>' },
                    { extend: 'csvHtml5', className: 'btn btn-info btn-sm', title: 'Daily_DF_User_Activity_<?php echo html_escape($report_date); ?>' }
                ]
            });

            var userActivityCanvas = document.getElementById('userActivityChart');
            if (userActivityCanvas) {
                new Chart(userActivityCanvas.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Working Today', 'No Visible Update'],
                        datasets: [{
                            data: [<?php echo (int) $user_summary['users_working_today']; ?>, <?php echo (int) $user_summary['users_no_update_today']; ?>],
                            backgroundColor: ['#16a34a', '#dc2626'],
                            borderColor: '#ffffff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
