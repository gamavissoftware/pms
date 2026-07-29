<?php defined('BASEPATH') OR exit('No direct script access allowed');

$department_name = !empty($department) ? $department : 'Department';
$tasks = isset($tasks) && is_array($tasks) ? $tasks : array();
$summary = isset($summary) && is_array($summary) ? $summary : array();
$owner_summary = isset($owner_summary) && is_array($owner_summary) ? $owner_summary : array();
$df_summary = isset($df_summary) && is_array($df_summary) ? $df_summary : array();

$total_tasks = !empty($summary['total_tasks']) ? (int) $summary['total_tasks'] : 0;
$unique_df_count = !empty($summary['unique_df_count']) ? (int) $summary['unique_df_count'] : 0;
$max_delay_days = !empty($summary['max_delay_days']) ? (int) $summary['max_delay_days'] : 0;
$avg_delay_days = !empty($summary['avg_delay_days']) ? (int) $summary['avg_delay_days'] : 0;
$unassigned_tasks = !empty($summary['unassigned_tasks']) ? (int) $summary['unassigned_tasks'] : 0;
$tasks_without_updates = !empty($summary['tasks_without_updates']) ? (int) $summary['tasks_without_updates'] : 0;
$tasks_with_open_tickets = !empty($summary['tasks_with_open_tickets']) ? (int) $summary['tasks_with_open_tickets'] : 0;
$critical_tasks = !empty($summary['critical_tasks']) ? (int) $summary['critical_tasks'] : 0;
$stale_tasks = !empty($summary['stale_tasks']) ? (int) $summary['stale_tasks'] : 0;

$top_df_pressure = array_slice($df_summary, 0, 6);
$top_owner_pressure = array_slice($owner_summary, 0, 6);
$snapshot_time = date('d M Y, h:i A');

$critical_ratio = $total_tasks > 0 ? round(($critical_tasks / $total_tasks) * 100) : 0;
$stale_ratio = $total_tasks > 0 ? round(($stale_tasks / $total_tasks) * 100) : 0;

$management_signal = 'No overdue open tasks are visible in this department right now.';
$management_note = 'The report is ready to use for monitoring, escalation, and follow-up.';

if ($total_tasks > 0) {
    if ($critical_tasks > 0) {
        $management_signal = $critical_tasks . ' task(s) have entered the critical delay zone and need direct escalation support.';
        $management_note = 'Critical delay is defined here as 15 or more overdue days on an open task.';
    } elseif ($stale_tasks > 0) {
        $management_signal = $stale_tasks . ' task(s) are overdue and also stale from an update perspective.';
        $management_note = 'These tasks need follow-up because the visible update trail is weak or outdated.';
    } elseif ($tasks_with_open_tickets > 0) {
        $management_signal = 'Execution is still active, but support tickets are influencing task closure in this department.';
        $management_note = 'Open ticket visibility helps management separate workload delay from support dependency.';
    } else {
        $management_signal = 'Overdue task pressure exists, but the current update trail is still visible and actionable.';
        $management_note = 'This is a good state for weekly management review and targeted follow-up.';
    }
}

$attention_points = array();
if (!empty($top_df_pressure[0])) {
    $attention_points[] = $top_df_pressure[0]['df_no'] . ' has the highest visible DF pressure with ' . (int) $top_df_pressure[0]['task_count'] . ' overdue task(s).';
}
if (!empty($top_owner_pressure[0])) {
    $attention_points[] = $top_owner_pressure[0]['owner'] . ' is currently holding the highest overdue workload in this department.';
}
if ($tasks_without_updates > 0) {
    $attention_points[] = $tasks_without_updates . ' task(s) do not have a visible latest update remark.';
}
if ($unassigned_tasks > 0) {
    $attention_points[] = $unassigned_tasks . ' task(s) are still unassigned and need ownership clarity.';
}
if (empty($attention_points)) {
    $attention_points[] = 'No special pressure signal found. Use this view for regular tracking and verification.';
}

$max_owner_task_count = 1;
foreach ($top_owner_pressure as $owner_row) {
    $max_owner_task_count = max($max_owner_task_count, (int) $owner_row['task_count']);
}

$max_df_pressure_delay = 1;
foreach ($top_df_pressure as $df_row) {
    $max_df_pressure_delay = max($max_df_pressure_delay, (int) $df_row['max_delay_days']);
}

if (!function_exists('pending_task_report_escape')) {
    function pending_task_report_escape($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('pending_task_report_date')) {
    function pending_task_report_date($value)
    {
        $value = trim((string) $value);
        if ($value === '' || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
            return 'N/A';
        }

        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return pending_task_report_escape($value);
        }

        return date('d-m-Y', $timestamp);
    }
}

if (!function_exists('pending_task_report_datetime')) {
    function pending_task_report_datetime($value)
    {
        $value = trim((string) $value);
        if ($value === '' || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
            return 'N/A';
        }

        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return pending_task_report_escape($value);
        }

        return date('d-m-Y h:i A', $timestamp);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Department-wise overdue task intelligence report">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> | Delayed Task Intelligence</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>

    <style>
        :root{
            --ink:#10243e;
            --muted:#5b6f88;
            --line:#d7e1ee;
            --surface:#ffffff;
            --surface-soft:#f4f8fc;
            --brand:#0455bf;
            --brand-2:#0ea5c6;
            --critical:#b42318;
            --high:#c2410c;
            --watch:#0369a1;
            --success:#15803d;
            --shadow:0 24px 60px rgba(16, 36, 62, .10);
        }

        body{
            font-family:'Plus Jakarta Sans', sans-serif;
            color:var(--ink);
            background:
                radial-gradient(circle at top left, rgba(4, 85, 191, .12), transparent 26%),
                radial-gradient(circle at right top, rgba(14, 165, 198, .12), transparent 24%),
                linear-gradient(180deg, #eef5fb 0%, #f7fbff 42%, #edf3f9 100%);
        }

        .page-shell{
            max-width:1480px;
            margin:0 auto;
            padding:18px 0 28px;
        }

        .hero-panel{
            position:relative;
            overflow:hidden;
            border-radius:28px;
            padding:28px;
            background:
                radial-gradient(circle at right bottom, rgba(255,255,255,.16), transparent 26%),
                linear-gradient(135deg, #0f1f33 0%, #0a4db1 50%, #0ea5c6 100%);
            color:#fff;
            box-shadow:var(--shadow);
        }

        .hero-panel:before{
            content:"";
            position:absolute;
            width:260px;
            height:260px;
            border-radius:999px;
            background:rgba(255,255,255,.08);
            top:-80px;
            right:-70px;
            filter:blur(4px);
        }

        .hero-panel:after{
            content:"";
            position:absolute;
            width:180px;
            height:180px;
            border-radius:999px;
            background:rgba(255,255,255,.10);
            left:-40px;
            bottom:-70px;
            filter:blur(4px);
        }

        .hero-kicker{
            position:relative;
            font-size:11px;
            font-weight:800;
            letter-spacing:.16em;
            text-transform:uppercase;
            color:rgba(255,255,255,.74);
        }

        .hero-title{
            position:relative;
            margin-top:10px;
            font-size:34px;
            line-height:1.05;
            font-weight:800;
            letter-spacing:-.03em;
            color:#fff;
        }

        .hero-subtitle{
            position:relative;
            margin-top:12px;
            max-width:930px;
            font-size:14px;
            line-height:1.7;
            color:rgba(255,255,255,.84);
        }

        .hero-chip-row{
            position:relative;
            display:flex;
            flex-wrap:wrap;
            gap:10px;
            margin-top:20px;
        }

        .hero-chip{
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding:8px 14px;
            border-radius:999px;
            border:1px solid rgba(255,255,255,.18);
            background:rgba(255,255,255,.10);
            color:#fff;
            font-size:12px;
            font-weight:800;
        }

        .content-card{
            border-radius:24px;
            border:1px solid rgba(215, 225, 238, .9);
            background:rgba(255,255,255,.92);
            box-shadow:0 16px 45px rgba(16, 36, 62, .06);
        }

        .stat-grid{
            display:grid;
            grid-template-columns:repeat(4, minmax(0, 1fr));
            gap:14px;
            margin-top:18px;
        }

        @media (max-width:1100px){
            .stat-grid{ grid-template-columns:repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width:640px){
            .stat-grid{ grid-template-columns:1fr; }
        }

        .stat-card{
            border-radius:22px;
            padding:18px;
            background:linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
            border:1px solid #e3ebf5;
            min-height:128px;
        }

        .stat-label{
            font-size:11px;
            font-weight:800;
            letter-spacing:.12em;
            text-transform:uppercase;
            color:var(--muted);
        }

        .stat-value{
            margin-top:9px;
            font-size:30px;
            line-height:1;
            font-weight:800;
            color:var(--ink);
        }

        .stat-note{
            margin-top:9px;
            font-size:12px;
            line-height:1.6;
            color:var(--muted);
        }

        .insight-grid{
            display:grid;
            grid-template-columns:1.25fr 1fr 1fr;
            gap:18px;
            margin-top:18px;
        }

        @media (max-width:1200px){
            .insight-grid{ grid-template-columns:1fr; }
        }

        .panel-title{
            font-size:18px;
            line-height:1.2;
            font-weight:800;
            color:var(--ink);
        }

        .panel-subtitle{
            margin-top:5px;
            font-size:12px;
            line-height:1.6;
            color:var(--muted);
        }

        .signal-box{
            border-radius:22px;
            padding:22px;
            background:
                radial-gradient(circle at top right, rgba(14,165,198,.12), transparent 26%),
                linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
            border:1px solid #dce7f3;
            height:100%;
        }

        .signal-head{
            font-size:12px;
            font-weight:800;
            letter-spacing:.14em;
            text-transform:uppercase;
            color:#3b82f6;
        }

        .signal-text{
            margin-top:12px;
            font-size:22px;
            line-height:1.35;
            font-weight:800;
            color:var(--ink);
        }

        .signal-note{
            margin-top:10px;
            font-size:13px;
            line-height:1.75;
            color:var(--muted);
        }

        .signal-list{
            margin:16px 0 0;
            padding:0;
            list-style:none;
        }

        .signal-list li{
            position:relative;
            padding-left:18px;
            margin-top:10px;
            font-size:13px;
            line-height:1.7;
            color:var(--ink);
        }

        .signal-list li:before{
            content:"";
            position:absolute;
            left:0;
            top:9px;
            width:8px;
            height:8px;
            border-radius:999px;
            background:linear-gradient(135deg, var(--brand) 0%, var(--brand-2) 100%);
        }

        .pressure-card{
            border-radius:22px;
            padding:20px;
            border:1px solid #dce7f3;
            background:linear-gradient(180deg, #ffffff 0%, #f9fbff 100%);
            height:100%;
        }

        .pressure-stack{
            display:flex;
            flex-direction:column;
            gap:12px;
            margin-top:16px;
        }

        .pressure-item{
            border-radius:18px;
            padding:14px;
            border:1px solid #e6edf7;
            background:#fff;
        }

        .pressure-item-title{
            font-size:14px;
            line-height:1.4;
            font-weight:800;
            color:var(--ink);
        }

        .pressure-item-sub{
            margin-top:4px;
            font-size:12px;
            color:var(--muted);
        }

        .pressure-bar{
            width:100%;
            height:10px;
            margin-top:12px;
            border-radius:999px;
            background:#e8eef6;
            overflow:hidden;
        }

        .pressure-bar > span{
            display:block;
            height:100%;
            border-radius:999px;
            background:linear-gradient(135deg, #0a4db1 0%, #0ea5c6 100%);
        }

        .pressure-meta{
            display:flex;
            flex-wrap:wrap;
            gap:8px;
            margin-top:10px;
        }

        .mini-chip{
            display:inline-flex;
            align-items:center;
            padding:6px 10px;
            border-radius:999px;
            font-size:11px;
            font-weight:800;
            background:#edf4fb;
            color:#23415f;
        }

        .mini-chip.critical{ background:#fde7e5; color:var(--critical); }
        .mini-chip.high{ background:#ffedd5; color:var(--high); }
        .mini-chip.watch{ background:#e0f2fe; color:var(--watch); }
        .mini-chip.good{ background:#dcfce7; color:var(--success); }

        .table-card{
            margin-top:18px;
            padding:22px;
        }

        .table-toolbar{
            display:flex;
            flex-wrap:wrap;
            gap:12px;
            justify-content:space-between;
            align-items:flex-end;
            margin-bottom:18px;
        }

        .toolbar-group{
            display:flex;
            flex-wrap:wrap;
            gap:12px;
            align-items:flex-end;
        }

        .filter-block label{
            display:block;
            margin-bottom:7px;
            font-size:11px;
            font-weight:800;
            letter-spacing:.12em;
            text-transform:uppercase;
            color:var(--muted);
        }

        .report-filter{
            min-width:180px;
            height:42px;
            border-radius:14px;
            border:1px solid #d7e1ee;
            background:#fff;
            padding:0 12px;
            font-size:12px;
            font-weight:700;
            color:var(--ink);
        }

        .toolbar-info{
            font-size:12px;
            line-height:1.7;
            color:var(--muted);
        }

        .btn-clear-filter{
            height:42px;
            border:1px solid #d7e1ee;
            border-radius:14px;
            background:#fff;
            color:var(--ink);
            font-size:12px;
            font-weight:800;
            padding:0 16px;
        }

        .report-table-wrap{
            overflow-x:auto;
            border-radius:20px;
            border:1px solid #e3ebf5;
        }

        table.report-table{
            width:100% !important;
            min-width:1480px;
            margin:0 !important;
            border-collapse:separate;
            border-spacing:0;
        }

        table.report-table thead th{
            background:#f5f9fd;
            color:var(--muted);
            border-bottom:1px solid #e3ebf5 !important;
            padding:16px 14px !important;
            font-size:11px;
            font-weight:800;
            letter-spacing:.12em;
            text-transform:uppercase;
            vertical-align:middle;
        }

        table.report-table tbody td{
            background:#fff;
            padding:16px 14px !important;
            border-top:1px solid #eef3f9 !important;
            vertical-align:top;
        }

        table.report-table tbody tr:hover td{
            background:#fbfdff;
        }

        .row-critical td{
            background:#fff5f4 !important;
        }

        .row-high td{
            background:#fffaf2 !important;
        }

        .sno-badge{
            width:34px;
            height:34px;
            border-radius:12px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            background:#eef4fb;
            font-size:12px;
            font-weight:800;
            color:#1e3a5f;
        }

        .cell-title{
            font-size:14px;
            line-height:1.45;
            font-weight:800;
            color:var(--ink);
        }

        .cell-sub{
            margin-top:5px;
            font-size:12px;
            line-height:1.65;
            color:var(--muted);
        }

        .cell-link{
            color:var(--brand);
            font-weight:800;
            text-decoration:none;
        }

        .cell-link:hover,
        .cell-link:focus{
            color:#023d8a;
            text-decoration:none;
        }

        .status-badge{
            display:inline-flex;
            align-items:center;
            gap:6px;
            padding:6px 10px;
            border-radius:999px;
            font-size:11px;
            font-weight:800;
            letter-spacing:.04em;
            text-transform:uppercase;
        }

        .badge-critical{ background:#fde7e5; color:var(--critical); }
        .badge-high{ background:#ffedd5; color:var(--high); }
        .badge-watch{ background:#e0f2fe; color:var(--watch); }
        .badge-ticket{ background:#dbeafe; color:#1d4ed8; }
        .badge-fresh{ background:#dcfce7; color:var(--success); }
        .badge-stale{ background:#fff1c2; color:#9a6700; }
        .badge-empty{ background:#f1f5f9; color:#475569; }

        .remark-box{
            padding:12px 13px;
            border-radius:16px;
            border:1px solid #e6edf7;
            background:#f9fbff;
            font-size:12px;
            line-height:1.75;
            color:#21364e;
        }

        .remark-box.empty{
            background:#fff7ed;
            border-color:#fed7aa;
            color:#9a3412;
        }

        .actions-stack{
            display:flex;
            flex-direction:column;
            gap:10px;
        }

        .action-link{
            display:inline-flex;
            justify-content:center;
            align-items:center;
            min-height:38px;
            padding:0 12px;
            border-radius:12px;
            font-size:12px;
            font-weight:800;
            text-decoration:none;
            transition:all .16s ease;
        }

        .action-link.primary{
            background:linear-gradient(135deg, #0a4db1 0%, #0ea5c6 100%);
            color:#fff;
        }

        .action-link.secondary{
            background:#edf4fb;
            color:#16385f;
        }

        .action-link:hover,
        .action-link:focus{
            text-decoration:none;
            transform:translateY(-1px);
            box-shadow:0 10px 20px rgba(10, 77, 177, .14);
        }

        .section-divider{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:14px;
            margin-top:18px;
            margin-bottom:12px;
        }

        .section-caption{
            font-size:12px;
            color:var(--muted);
            line-height:1.7;
        }

        .empty-state{
            padding:48px 22px;
            text-align:center;
            color:var(--muted);
            font-size:14px;
            line-height:1.8;
        }

        .dataTables_wrapper .dataTables_filter input{
            border:1px solid #d7e1ee;
            border-radius:12px;
            min-height:38px;
            padding:0 12px;
            margin-left:8px;
            color:var(--ink);
        }

        .dataTables_wrapper .dataTables_length select{
            border:1px solid #d7e1ee;
            border-radius:12px;
            min-height:38px;
            padding:0 10px;
            color:var(--ink);
            background:#fff;
        }

        .dt-buttons .btn{
            border-radius:12px !important;
            border:1px solid #d7e1ee !important;
            background:#fff !important;
            color:var(--ink) !important;
            font-size:12px !important;
            font-weight:800 !important;
            padding:9px 14px !important;
            box-shadow:none !important;
        }

        .dt-buttons .btn:hover,
        .dt-buttons .btn:focus{
            background:#edf4fb !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button{
            border-radius:10px !important;
        }

        @media (max-width:767px){
            .hero-panel{ padding:22px; }
            .hero-title{ font-size:28px; }
            .signal-text{ font-size:18px; }
            .table-card{ padding:18px; }
            .table-toolbar{ align-items:stretch; }
            .toolbar-group{ width:100%; }
            .filter-block, .report-filter, .btn-clear-filter{ width:100%; }
        }
    </style>
</head>
<body>
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>

    <div class="wrapper">
        <div class="container-fluid">
            <div class="page-shell">
                <div class="hero-panel">
                    <div class="hero-kicker">Department Delay Intelligence</div>
                    <div class="hero-title"><?php echo pending_task_report_escape($department_name); ?> Overdue Task Command Center</div>
                    <div class="hero-subtitle">
                        This view shows only overdue open tasks for this department, excludes on-hold DF records, preserves unassigned work, and brings together latest remarks, ticket exposure, owner accountability, and direct drill-down links for management.
                    </div>
                    <div class="hero-chip-row">
                        <span class="hero-chip"><?php echo $total_tasks; ?> overdue task(s)</span>
                        <span class="hero-chip"><?php echo $unique_df_count; ?> active DF(s)</span>
                        <span class="hero-chip">Max delay <?php echo $max_delay_days; ?> day(s)</span>
                        <span class="hero-chip">Snapshot <?php echo pending_task_report_escape($snapshot_time); ?></span>
                    </div>
                </div>

                <div class="stat-grid">
                    <div class="stat-card content-card">
                        <div class="stat-label">Overdue Tasks</div>
                        <div class="stat-value"><?php echo $total_tasks; ?></div>
                        <div class="stat-note">Exact open-task pressure visible on this report right now.</div>
                    </div>
                    <div class="stat-card content-card">
                        <div class="stat-label">Affected DFs</div>
                        <div class="stat-value"><?php echo $unique_df_count; ?></div>
                        <div class="stat-note">How many DF jobs currently contain overdue work in this department.</div>
                    </div>
                    <div class="stat-card content-card">
                        <div class="stat-label">Critical Delay</div>
                        <div class="stat-value"><?php echo $critical_tasks; ?></div>
                        <div class="stat-note"><?php echo $critical_ratio; ?>% of open overdue work is already 15 or more days late.</div>
                    </div>
                    <div class="stat-card content-card">
                        <div class="stat-label">Average Delay</div>
                        <div class="stat-value"><?php echo $avg_delay_days; ?></div>
                        <div class="stat-note">Average overdue age across all visible tasks.</div>
                    </div>
                    <div class="stat-card content-card">
                        <div class="stat-label">No Visible Update</div>
                        <div class="stat-value"><?php echo $tasks_without_updates; ?></div>
                        <div class="stat-note">Tasks where the latest update remark trail is blank.</div>
                    </div>
                    <div class="stat-card content-card">
                        <div class="stat-label">Stale Updates</div>
                        <div class="stat-value"><?php echo $stale_tasks; ?></div>
                        <div class="stat-note"><?php echo $stale_ratio; ?>% of overdue tasks have no update or an update older than 7 days.</div>
                    </div>
                    <div class="stat-card content-card">
                        <div class="stat-label">Open Tickets</div>
                        <div class="stat-value"><?php echo $tasks_with_open_tickets; ?></div>
                        <div class="stat-note">Tasks where help-ticket dependency is still active.</div>
                    </div>
                    <div class="stat-card content-card">
                        <div class="stat-label">Unassigned Work</div>
                        <div class="stat-value"><?php echo $unassigned_tasks; ?></div>
                        <div class="stat-note">Tasks that need immediate owner allocation before follow-up.</div>
                    </div>
                </div>

                <div class="insight-grid">
                    <div class="signal-box content-card">
                        <div class="signal-head">Management Readout</div>
                        <div class="signal-text"><?php echo pending_task_report_escape($management_signal); ?></div>
                        <div class="signal-note"><?php echo pending_task_report_escape($management_note); ?></div>
                        <ul class="signal-list">
                            <?php foreach ($attention_points as $attention_point): ?>
                                <li><?php echo pending_task_report_escape($attention_point); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="pressure-card content-card">
                        <div class="panel-title">Top DF Pressure</div>
                        <div class="panel-subtitle">Management can immediately see which DF jobs are carrying the heaviest overdue load.</div>
                        <div class="pressure-stack">
                            <?php if (!empty($top_df_pressure)): ?>
                                <?php foreach ($top_df_pressure as $pressure_row): ?>
                                    <?php
                                    $delay_pct = $max_df_pressure_delay > 0 ? round(((int) $pressure_row['max_delay_days'] / $max_df_pressure_delay) * 100) : 0;
                                    ?>
                                    <div class="pressure-item">
                                        <div class="pressure-item-title"><?php echo pending_task_report_escape($pressure_row['df_no']); ?></div>
                                        <div class="pressure-item-sub">
                                            <?php echo pending_task_report_escape($pressure_row['company_name'] !== '' ? $pressure_row['company_name'] : 'Company not mapped'); ?>
                                        </div>
                                        <div class="pressure-bar"><span style="width:<?php echo max(6, $delay_pct); ?>%;"></span></div>
                                        <div class="pressure-meta">
                                            <span class="mini-chip"><?php echo (int) $pressure_row['task_count']; ?> overdue task(s)</span>
                                            <span class="mini-chip critical">Max delay <?php echo (int) $pressure_row['max_delay_days']; ?> day(s)</span>
                                            <span class="mini-chip"><?php echo (int) $pressure_row['open_ticket_count']; ?> open ticket link(s)</span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="empty-state">No DF pressure visible for the selected department.</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="pressure-card content-card">
                        <div class="panel-title">Owner Accountability</div>
                        <div class="panel-subtitle">This section highlights who is currently carrying the highest overdue workload inside the department.</div>
                        <div class="pressure-stack">
                            <?php if (!empty($top_owner_pressure)): ?>
                                <?php foreach ($top_owner_pressure as $owner_row): ?>
                                    <?php
                                    $owner_pct = $max_owner_task_count > 0 ? round(((int) $owner_row['task_count'] / $max_owner_task_count) * 100) : 0;
                                    ?>
                                    <div class="pressure-item">
                                        <div class="pressure-item-title"><?php echo pending_task_report_escape($owner_row['owner']); ?></div>
                                        <div class="pressure-bar"><span style="width:<?php echo max(6, $owner_pct); ?>%;"></span></div>
                                        <div class="pressure-meta">
                                            <span class="mini-chip"><?php echo (int) $owner_row['task_count']; ?> overdue task(s)</span>
                                            <span class="mini-chip high">Max delay <?php echo (int) $owner_row['max_delay_days']; ?> day(s)</span>
                                            <span class="mini-chip"><?php echo (int) $owner_row['open_ticket_count']; ?> ticket link(s)</span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="empty-state">No owner pressure signal is available right now.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="table-card content-card">
                    <div class="table-toolbar">
                        <div>
                            <div class="panel-title">Overdue Task Register</div>
                            <div class="panel-subtitle">Use search, export, and smart filters to review overdue work by urgency, freshness, and accountability.</div>
                        </div>
                        <div id="pendingTaskExportButtons"></div>
                    </div>

                    <div class="section-divider">
                        <div class="section-caption">
                            Scope: overdue open tasks only. Direct links are available for Gantt and full DF intelligence review.
                        </div>
                        <div id="pendingTaskCounter" class="section-caption"></div>
                    </div>

                    <div class="table-toolbar">
                        <div class="toolbar-group">
                            <div class="filter-block">
                                <label for="urgencyFilter">Urgency</label>
                                <select id="urgencyFilter" class="report-filter">
                                    <option value="">All urgency</option>
                                    <option value="critical">Critical</option>
                                    <option value="high">High</option>
                                    <option value="watch">Watch</option>
                                </select>
                            </div>
                            <div class="filter-block">
                                <label for="updateFilter">Update Health</label>
                                <select id="updateFilter" class="report-filter">
                                    <option value="">All update states</option>
                                    <option value="missing">No visible update</option>
                                    <option value="stale">Stale update</option>
                                    <option value="fresh">Updated recently</option>
                                </select>
                            </div>
                            <div class="filter-block">
                                <label for="ownerFilter">Owner</label>
                                <select id="ownerFilter" class="report-filter">
                                    <option value="">All owners</option>
                                    <?php foreach ($owner_summary as $owner_row): ?>
                                        <option value="<?php echo pending_task_report_escape(strtolower($owner_row['owner'])); ?>">
                                            <?php echo pending_task_report_escape($owner_row['owner']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="button" id="clearFilters" class="btn-clear-filter">Reset Filters</button>
                        </div>
                        <div class="toolbar-info">
                            The table keeps unassigned work, latest ticket references, and last visible action remarks in one place for quick review.
                        </div>
                    </div>

                    <div class="report-table-wrap">
                        <table id="pendingTaskTable" class="table report-table">
                            <thead>
                                <tr>
                                    <th style="width:70px;">S. No.</th>
                                    <th style="width:260px;">DF Snapshot</th>
                                    <th style="width:260px;">Task Window</th>
                                    <th style="width:180px;">Owner</th>
                                    <th style="width:180px;">Delay Health</th>
                                    <th style="width:360px;">Latest Update</th>
                                    <th style="width:160px;">Ticket Visibility</th>
                                    <th style="width:150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($tasks)): ?>
                                    <?php foreach ($tasks as $index => $task): ?>
                                        <?php
                                        $urgency_key = !empty($task['urgency_key']) ? strtolower($task['urgency_key']) : 'watch';
                                        $assigned_to = !empty($task['assigned_to']) ? $task['assigned_to'] : 'Unassigned';
                                        $update_age_days = isset($task['update_age_days']) && $task['update_age_days'] !== null ? (int) $task['update_age_days'] : null;
                                        $latest_update_text = trim((string) $task['latest_update_remark']);
                                        $update_state = 'fresh';
                                        if ($latest_update_text === '') {
                                            $update_state = 'missing';
                                        } elseif ($update_age_days !== null && $update_age_days > 7) {
                                            $update_state = 'stale';
                                        }

                                        $row_class = '';
                                        if ($urgency_key === 'critical') {
                                            $row_class = 'row-critical';
                                        } elseif ($urgency_key === 'high') {
                                            $row_class = 'row-high';
                                        }
                                        ?>
                                        <tr
                                            class="<?php echo $row_class; ?>"
                                            data-urgency="<?php echo pending_task_report_escape($urgency_key); ?>"
                                            data-update="<?php echo pending_task_report_escape($update_state); ?>"
                                            data-owner="<?php echo pending_task_report_escape(strtolower($assigned_to)); ?>"
                                        >
                                            <td>
                                                <span class="sno-badge"><?php echo $index + 1; ?></span>
                                            </td>
                                            <td>
                                                <div class="cell-title"><?php echo pending_task_report_escape($task['df_no']); ?></div>
                                                <div class="cell-sub">
                                                    <?php echo pending_task_report_escape($task['company_name'] !== '' ? $task['company_name'] : 'Company not mapped'); ?>
                                                </div>
                                                <div class="cell-sub">
                                                    <?php echo pending_task_report_escape($task['df_description'] !== '' ? $task['df_description'] : 'DF description not available'); ?>
                                                </div>
                                                <div class="pressure-meta" style="margin-top:12px;">
                                                    <span class="mini-chip">Released <?php echo pending_task_report_escape(pending_task_report_date($task['df_release_date'])); ?></span>
                                                    <?php if (!empty($task['marketing_person'])): ?>
                                                        <span class="mini-chip">Marketing <?php echo pending_task_report_escape($task['marketing_person']); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="cell-title"><?php echo pending_task_report_escape($task['task_name']); ?></div>
                                                <div class="cell-sub">Start: <?php echo pending_task_report_escape(pending_task_report_date($task['start_date'])); ?></div>
                                                <div class="cell-sub">Planned end: <?php echo pending_task_report_escape(pending_task_report_date($task['end_date'])); ?></div>
                                                <div class="pressure-meta" style="margin-top:12px;">
                                                    <span class="mini-chip"><?php echo pending_task_report_escape($department_name); ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="cell-title"><?php echo pending_task_report_escape($assigned_to); ?></div>
                                                <div class="cell-sub">
                                                    <?php if (strtolower($assigned_to) === 'unassigned'): ?>
                                                        This overdue work still needs clear ownership.
                                                    <?php else: ?>
                                                        Accountable person for task closure and latest execution update.
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td data-order="<?php echo (int) $task['delay_days']; ?>">
                                                <div>
                                                    <span class="status-badge badge-<?php echo pending_task_report_escape($urgency_key); ?>">
                                                        <?php echo pending_task_report_escape(!empty($task['urgency_label']) ? $task['urgency_label'] : 'Watch'); ?>
                                                    </span>
                                                </div>
                                                <div class="cell-title" style="margin-top:12px;"><?php echo (int) $task['delay_days']; ?> day(s) delayed</div>
                                                <div class="pressure-meta" style="margin-top:12px;">
                                                    <?php if ($update_state === 'fresh'): ?>
                                                        <span class="mini-chip good">Updated recently</span>
                                                    <?php elseif ($update_state === 'stale'): ?>
                                                        <span class="mini-chip high">Update age <?php echo $update_age_days; ?> day(s)</span>
                                                    <?php else: ?>
                                                        <span class="mini-chip critical">No visible update</span>
                                                    <?php endif; ?>
                                                    <?php if ((int) $task['open_ticket_count'] > 0): ?>
                                                        <span class="mini-chip watch"><?php echo (int) $task['open_ticket_count']; ?> open ticket(s)</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td data-order="<?php echo !empty($task['latest_update_on']) ? strtotime($task['latest_update_on']) : 0; ?>">
                                                <?php if ($latest_update_text !== ''): ?>
                                                    <div class="remark-box">
                                                        <?php echo nl2br(pending_task_report_escape($latest_update_text)); ?>
                                                    </div>
                                                    <div class="pressure-meta" style="margin-top:12px;">
                                                        <span class="status-badge badge-fresh">Updated <?php echo pending_task_report_escape(pending_task_report_datetime($task['latest_update_on'])); ?></span>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="remark-box empty">
                                                        No latest execution remark is visible from pending status, task remarks, or ticket updates.
                                                    </div>
                                                    <div class="pressure-meta" style="margin-top:12px;">
                                                        <span class="status-badge badge-empty">Needs follow-up</span>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="cell-title"><?php echo (int) $task['open_ticket_count']; ?> open</div>
                                                <div class="cell-sub">
                                                    Latest ticket:
                                                    <?php echo pending_task_report_escape(!empty($task['latest_ticket_no']) ? $task['latest_ticket_no'] : 'N/A'); ?>
                                                </div>
                                                <div class="pressure-meta" style="margin-top:12px;">
                                                    <?php if ((int) $task['open_ticket_count'] > 0): ?>
                                                        <span class="status-badge badge-ticket">Support dependency active</span>
                                                    <?php else: ?>
                                                        <span class="status-badge badge-empty">No open ticket</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="actions-stack">
                                                    <a href="<?php echo pending_task_report_escape($task['df_detail_url']); ?>" target="_blank" class="action-link primary">DF Intelligence</a>
                                                    <a href="<?php echo pending_task_report_escape($task['gantt_url']); ?>" target="_blank" class="action-link secondary">Open Gantt</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8">
                                            <div class="empty-state">
                                                No overdue open tasks were found for this department at the current snapshot.
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
    <script>
        $(document).ready(function () {
            var urgencyFilter = '';
            var updateFilter = '';
            var ownerFilter = '';
            var hasRecords = <?php echo $total_tasks > 0 ? 'true' : 'false'; ?>;

            if (!hasRecords) {
                $('#pendingTaskCounter').text('Visible records: 0 of 0');
                $('#urgencyFilter, #updateFilter, #ownerFilter, #clearFilters').prop('disabled', true);
                return;
            }

            var table = $('#pendingTaskTable').DataTable({
                order: [[4, 'desc']],
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
                scrollX: true,
                responsive: false,
                dom: 'Blfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Delayed Task Intelligence - <?php echo addslashes($department_name); ?>'
                    },
                    {
                        extend: 'csvHtml5',
                        title: 'Delayed Task Intelligence - <?php echo addslashes($department_name); ?>'
                    },
                    {
                        extend: 'print',
                        title: 'Delayed Task Intelligence - <?php echo addslashes($department_name); ?>'
                    }
                ],
                columnDefs: [
                    { orderable: false, targets: [0, 7] }
                ],
                language: {
                    search: 'Search register',
                    lengthMenu: 'Show _MENU_ items',
                    info: 'Showing _START_ to _END_ of _TOTAL_ overdue task(s)',
                    infoEmpty: 'No overdue tasks found',
                    zeroRecords: 'No matching overdue tasks found'
                }
            });

            table.buttons().container().appendTo('#pendingTaskExportButtons');

            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                if (settings.nTable.id !== 'pendingTaskTable') {
                    return true;
                }

                var rowNode = table.row(dataIndex).node();
                var rowUrgency = String($(rowNode).attr('data-urgency') || '');
                var rowUpdate = String($(rowNode).attr('data-update') || '');
                var rowOwner = String($(rowNode).attr('data-owner') || '');

                if (urgencyFilter && rowUrgency !== urgencyFilter) {
                    return false;
                }

                if (updateFilter && rowUpdate !== updateFilter) {
                    return false;
                }

                if (ownerFilter && rowOwner !== ownerFilter) {
                    return false;
                }

                return true;
            });

            function refreshCounter() {
                var info = table.page.info();
                $('#pendingTaskCounter').text('Visible records: ' + info.recordsDisplay + ' of ' + info.recordsTotal);
            }

            $('#urgencyFilter').on('change', function () {
                urgencyFilter = String($(this).val() || '');
                table.draw();
            });

            $('#updateFilter').on('change', function () {
                updateFilter = String($(this).val() || '');
                table.draw();
            });

            $('#ownerFilter').on('change', function () {
                ownerFilter = String($(this).val() || '');
                table.draw();
            });

            $('#clearFilters').on('click', function () {
                urgencyFilter = '';
                updateFilter = '';
                ownerFilter = '';
                $('#urgencyFilter').val('');
                $('#updateFilter').val('');
                $('#ownerFilter').val('');
                table.search('').draw();
            });

            table.on('draw', function () {
                refreshCounter();
            });

            refreshCounter();
        });
    </script>

    <?php $this->load->view('common/footer'); ?>
</body>
</html>
