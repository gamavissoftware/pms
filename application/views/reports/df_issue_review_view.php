<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
if (!function_exists('df_issue_review_severity_class')) {
    function df_issue_review_severity_class($label)
    {
        $label = strtolower((string) $label);
        if ($label === 'critical') {
            return 'severity-critical';
        }
        if ($label === 'high') {
            return 'severity-high';
        }
        if ($label === 'watch') {
            return 'severity-watch';
        }
        return 'severity-low';
    }
}

if (!function_exists('df_issue_review_status_class')) {
    function df_issue_review_status_class($label)
    {
        $label = strtolower((string) $label);
        if ($label === 'pending approval') {
            return 'status-approval';
        }
        if ($label === 'open overdue') {
            return 'status-overdue';
        }
        if ($label === 'delayed closed') {
            return 'status-closed';
        }
        return 'status-watch';
    }
}

if (!function_exists('df_issue_review_date_label')) {
    function df_issue_review_date_label($value)
    {
        if (empty($value)) {
            return '--';
        }
        $time = strtotime($value);
        if ($time === false) {
            return '--';
        }
        return date('d M', $time);
    }
}

if (!function_exists('df_issue_review_offset_pct')) {
    function df_issue_review_offset_pct($date_value, $timeline_start, $timeline_days)
    {
        if (empty($date_value) || empty($timeline_start) || $timeline_days <= 0) {
            return 0;
        }
        $offset_days = (int) floor((strtotime($date_value) - strtotime($timeline_start)) / 86400);
        return max(0, min(100, ($offset_days / $timeline_days) * 100));
    }
}

if (!function_exists('df_issue_review_span_pct')) {
    function df_issue_review_span_pct($start_date, $end_date, $timeline_days)
    {
        if (empty($start_date) || empty($end_date) || $timeline_days <= 0) {
            return 0;
        }
        $span = (int) floor((strtotime($end_date) - strtotime($start_date)) / 86400) + 1;
        return max(1.5, min(100, ($span / $timeline_days) * 100));
    }
}

$top_df = $management_highlights['top_df'] ?? null;
$top_department = $management_highlights['top_department'] ?? null;
$critical_high_count = (int) ($management_highlights['critical_high_count'] ?? 0);
$takeaways = array();

if ($top_df) {
    $takeaways[] = $top_df['df_no'] . ' is the top review DF with ' . $top_df['total_issues'] . ' issue record(s) and ' . $top_df['max_delay_days'] . ' day(s) max delay.';
}
if ($top_department) {
    $takeaways[] = $top_department['name'] . ' is carrying the heaviest delay load in the selected view.';
}
foreach (($overall_improvement_points ?? array()) as $point) {
    $takeaways[] = $point;
    if (count($takeaways) >= 3) {
        break;
    }
}

$timeline_start = $gantt_timeline['start'] ?? null;
$timeline_end = $gantt_timeline['end'] ?? null;
$timeline_days = max(1, (int) ($gantt_timeline['total_days'] ?? 0));
$timeline_mid = ($timeline_start && $timeline_end) ? date('Y-m-d', strtotime($timeline_start . ' +' . floor($timeline_days / 2) . ' days')) : null;
$today_marker = ($timeline_start && $timeline_end) ? df_issue_review_offset_pct($gantt_timeline['today'] ?? date('Y-m-d'), $timeline_start, $timeline_days) : null;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; DF Issue Review</title>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
        :root {
            --bg: #f5f2ea;
            --panel: #ffffff;
            --panel-soft: #faf7f0;
            --border: #e6decf;
            --ink: #1d3440;
            --muted: #70808a;
            --accent: #0f6c5b;
            --critical: #ae3d44;
            --high: #cb8a31;
            --watch: #4d6772;
            --low: #2c7f63;
            --line: #d9d0c1;
            --shadow: 0 16px 36px rgba(29, 52, 64, 0.08);
        }

        body {
            background:
                radial-gradient(circle at top left, rgba(203, 138, 49, 0.12), transparent 22%),
                radial-gradient(circle at top right, rgba(15, 108, 91, 0.10), transparent 18%),
                var(--bg);
            color: var(--ink);
        }

        .report-wrap {
            padding-top: 20px;
            padding-bottom: 34px;
        }

        .panel-card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: var(--shadow);
            margin-bottom: 18px;
        }

        .hero-card {
            padding: 26px 28px 22px;
        }

        .eyebrow {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(15, 108, 91, 0.10);
            color: var(--accent);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .hero-title {
            margin: 14px 0 8px;
            font-size: 30px;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .hero-copy {
            margin: 0;
            max-width: 760px;
            color: var(--muted);
            font-size: 15px;
            line-height: 1.65;
        }

        .hero-meta {
            text-align: right;
        }

        .hero-meta .meta-line {
            color: var(--muted);
            font-size: 13px;
            margin-top: 7px;
        }

        .btn-report {
            border-radius: 999px;
            padding: 10px 18px;
            font-weight: 600;
        }

        .filter-card {
            padding: 18px 22px 8px;
            background: var(--panel-soft);
        }

        .filter-card label {
            display: block;
            margin-bottom: 6px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .filter-card .form-group {
            margin-bottom: 12px;
        }

        .filter-card .form-control,
        .select2-container .select2-selection--single {
            height: 40px;
            border-radius: 12px;
            border: 1px solid #d9d1c2;
            box-shadow: none;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            margin-top: 22px;
        }

        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .kpi-card {
            padding: 18px 20px;
        }

        .kpi-label {
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .kpi-value {
            display: block;
            margin-top: 10px;
            font-size: 28px;
            font-weight: 700;
            line-height: 1.1;
        }

        .kpi-note {
            margin-top: 8px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.5;
        }

        .section-card {
            padding: 20px 22px;
        }

        .section-title {
            margin: 0 0 6px;
            font-size: 18px;
            font-weight: 700;
        }

        .section-note {
            color: var(--muted);
            font-size: 14px;
            margin-bottom: 16px;
        }

        .takeaway-list {
            margin: 0;
            padding-left: 18px;
        }

        .takeaway-list li {
            margin-bottom: 10px;
            line-height: 1.65;
        }

        .mini-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 12px;
            border-radius: 999px;
            background: #f1ece2;
            color: var(--ink);
            font-size: 12px;
            font-weight: 700;
        }

        .severity-pill,
        .status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .severity-critical,
        .status-overdue {
            background: rgba(174, 61, 68, 0.12);
            color: var(--critical);
        }

        .severity-high,
        .status-approval {
            background: rgba(203, 138, 49, 0.15);
            color: #a76917;
        }

        .severity-watch,
        .status-watch {
            background: rgba(77, 103, 114, 0.12);
            color: var(--watch);
        }

        .severity-low,
        .status-closed {
            background: rgba(44, 127, 99, 0.12);
            color: var(--low);
        }

        .gantt-shell {
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
        }

        .gantt-head,
        .gantt-row {
            display: flex;
            align-items: stretch;
        }

        .gantt-meta-col {
            width: 250px;
            min-width: 250px;
            border-right: 1px solid var(--border);
            background: #fff;
        }

        .gantt-head .gantt-meta-col,
        .gantt-head .gantt-scale-col {
            background: #fbf7f0;
        }

        .gantt-head-title {
            padding: 14px 16px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .gantt-scale-col {
            position: relative;
            flex: 1;
            min-height: 50px;
            border-bottom: 1px solid var(--border);
        }

        .gantt-scale-mark {
            position: absolute;
            top: 14px;
            transform: translateX(-50%);
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
        }

        .gantt-scale-mark:first-child {
            transform: none;
            left: 0 !important;
        }

        .gantt-scale-mark:last-child {
            transform: translateX(-100%);
        }

        .gantt-row {
            border-bottom: 1px solid #efe8db;
        }

        .gantt-row:last-child {
            border-bottom: 0;
        }

        .gantt-meta {
            padding: 14px 16px;
        }

        .gantt-df {
            font-size: 14px;
            font-weight: 700;
            color: var(--ink);
        }

        .gantt-sub {
            margin-top: 5px;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.5;
        }

        .gantt-track-col {
            position: relative;
            flex: 1;
            min-height: 86px;
            background:
                linear-gradient(to right, rgba(217, 208, 193, 0.35) 1px, transparent 1px);
            background-size: 12.5% 100%;
        }

        .gantt-track {
            position: absolute;
            inset: 0;
        }

        .gantt-bar {
            position: absolute;
            height: 14px;
            border-radius: 999px;
        }

        .gantt-bar-plan {
            top: 26px;
            background: rgba(29, 52, 64, 0.12);
            border: 1px solid rgba(29, 52, 64, 0.25);
        }

        .gantt-bar-current {
            top: 47px;
            background: linear-gradient(90deg, #0f6c5b, #15a187);
        }

        .gantt-today {
            position: absolute;
            top: 12px;
            bottom: 12px;
            width: 2px;
            background: #b63b43;
            opacity: 0.7;
        }

        .gantt-meta-tags {
            margin-top: 9px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .legend-row {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 14px;
        }

        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 600;
        }

        .legend-swatch {
            width: 18px;
            height: 10px;
            border-radius: 999px;
        }

        .legend-plan {
            background: rgba(29, 52, 64, 0.16);
            border: 1px solid rgba(29, 52, 64, 0.24);
        }

        .legend-current {
            background: linear-gradient(90deg, #0f6c5b, #15a187);
        }

        .legend-today {
            width: 2px;
            height: 12px;
            border-radius: 0;
            background: #b63b43;
        }

        .action-table {
            margin: 0;
        }

        .action-table > thead > tr > th {
            background: #fbf7f0;
            border-color: var(--border);
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .action-table > tbody > tr > td {
            border-color: var(--border);
            vertical-align: middle;
        }

        .action-df-title {
            font-weight: 700;
        }

        .action-df-sub {
            margin-top: 3px;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.45;
        }

        .action-note {
            color: var(--ink);
            font-size: 13px;
            line-height: 1.55;
        }

        .empty-state {
            padding: 48px 20px;
            text-align: center;
            color: var(--muted);
        }

        @media (max-width: 991px) {
            .hero-meta {
                text-align: left;
                margin-top: 16px;
            }

            .kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .gantt-head,
            .gantt-row {
                flex-direction: column;
            }

            .gantt-meta-col {
                width: 100%;
                min-width: 100%;
                border-right: 0;
                border-bottom: 1px solid var(--border);
            }

            .gantt-track-col {
                min-height: 74px;
            }
        }

        @media (max-width: 640px) {
            .kpi-grid {
                grid-template-columns: 1fr;
            }
        }

        @media print {
            #topnav,
            .filter-card,
            .btn-report {
                display: none !important;
            }

            body {
                background: #fff !important;
            }

            .panel-card {
                box-shadow: none;
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>

    <div class="wrapper report-wrap">
        <div class="container-fluid">
            <div class="panel-card hero-card">
                <div class="row">
                    <div class="col-md-8">
                        <span class="eyebrow">Executive DF Review</span>
                        <h1 class="hero-title">DF Issue Review Report</h1>
                        <p class="hero-copy">
                            A compact management page focused on where delays are concentrated, how priority DFs are moving against plan, and what immediate action is needed.
                        </p>
                    </div>
                    <div class="col-md-4 hero-meta">
                        <button type="button" class="btn btn-primary btn-report" onclick="window.print();">
                            <i class="fa fa-print"></i> Print Report
                        </button>
                        <div class="meta-line"><strong>Period:</strong> <?php echo htmlspecialchars($report_period_label); ?></div>
                        <div class="meta-line"><strong>Generated:</strong> <?php echo date('d-m-Y h:i A'); ?></div>
                    </div>
                </div>
            </div>

            <div class="panel-card filter-card">
                <form method="post" action="<?php echo page_url; ?>Df_reports/df_issue_review">
                    <div class="row">
                        <div class="col-md-2 col-sm-6">
                            <div class="form-group">
                                <label for="financial_year">Financial Year</label>
                                <select name="financial_year" id="financial_year" class="form-control">
                                    <option value="">Select FY</option>
                                    <?php
                                    $current_year = date('Y');
                                    for ($i = $current_year + 1; $i >= 2023; $i--) {
                                        $fy_text = ($i - 1) . '-' . $i;
                                        $selected = (!empty($selected_fy) && $selected_fy === $fy_text) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($fy_text) . '" ' . $selected . '>' . htmlspecialchars($fy_text) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="form-group">
                                <label for="start_date">From</label>
                                <input type="text" name="start_date" id="start_date" class="form-control datepicker" value="<?php echo htmlspecialchars($selected_start_date ?? ''); ?>" placeholder="DD-MM-YYYY">
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="form-group">
                                <label for="end_date">To</label>
                                <input type="text" name="end_date" id="end_date" class="form-control datepicker" value="<?php echo htmlspecialchars($selected_end_date ?? ''); ?>" placeholder="DD-MM-YYYY">
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="form-group">
                                <label for="department_id">Department</label>
                                <select name="department_id" id="department_id" class="form-control">
                                    <option value="">All Departments</option>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?php echo $dept['department_id']; ?>" <?php echo ((string) $selected_department_id === (string) $dept['department_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($dept['department']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="form-group">
                                <label for="df_id">DF No.</label>
                                <select name="df_id" id="df_id" class="form-control select2">
                                    <option value="">All DFs</option>
                                    <?php foreach ($all_dfs as $df): ?>
                                        <option value="<?php echo $df['df_id']; ?>" <?php echo ((string) $selected_df_id === (string) $df['df_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($df['df_no']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-12">
                            <div class="filter-actions">
                                <button type="submit" class="btn btn-primary btn-report">
                                    <i class="fa fa-search"></i> View
                                </button>
                                <a href="<?php echo page_url; ?>Df_reports/df_issue_review" class="btn btn-default btn-report">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="kpi-grid">
                <div class="panel-card kpi-card">
                    <div class="kpi-label">DFs At Risk</div>
                    <span class="kpi-value"><?php echo number_format($summary_cards['dfs_with_issues'] ?? 0); ?></span>
                    <div class="kpi-note">Unique DFs appearing in this review.</div>
                </div>
                <div class="panel-card kpi-card">
                    <div class="kpi-label">Open Issue Load</div>
                    <span class="kpi-value"><?php echo number_format($summary_cards['open_issue_count'] ?? 0); ?></span>
                    <div class="kpi-note">Pending overdue and pending approval items.</div>
                </div>
                <div class="panel-card kpi-card">
                    <div class="kpi-label">Critical / High</div>
                    <span class="kpi-value"><?php echo number_format($critical_high_count); ?></span>
                    <div class="kpi-note">DFs needing active management attention.</div>
                </div>
                <div class="panel-card kpi-card">
                    <div class="kpi-label">Ticket Signals</div>
                    <span class="kpi-value"><?php echo number_format($summary_cards['ticketed_tasks'] ?? 0); ?></span>
                    <div class="kpi-note">Delayed tasks already escalated through tickets.</div>
                </div>
            </div>

            <div class="row" style="margin-top: 18px;">
                <div class="col-lg-7">
                    <div class="panel-card section-card">
                        <h3 class="section-title">Delay Load By Department</h3>
                        <p class="section-note">Quick visual of where the issue concentration sits in the selected report window.</p>
                        <?php if (!empty($chart_data['department']['labels'])): ?>
                            <canvas id="deptIssueChart" height="210"></canvas>
                        <?php else: ?>
                            <div class="empty-state">No department issue data available for the selected filter.</div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="panel-card section-card">
                        <h3 class="section-title">Management Takeaways</h3>
                        <p class="section-note">Use these points directly in the review meeting.</p>
                        <ul class="takeaway-list">
                            <?php foreach ($takeaways as $point): ?>
                                <li><?php echo htmlspecialchars($point); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php if ($top_df): ?>
                            <div style="margin-top: 16px;">
                                <span class="mini-badge"><i class="fa fa-folder-open-o"></i> Top DF: <?php echo htmlspecialchars($top_df['df_no']); ?></span>
                                <span class="mini-badge"><i class="fa fa-building-o"></i> Focus Dept: <?php echo htmlspecialchars($top_df['top_department']); ?></span>
                                <span class="mini-badge"><i class="fa fa-user-o"></i> Primary Owner: <?php echo htmlspecialchars($top_df['primary_owner']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="panel-card section-card">
                <div class="row" style="margin-bottom: 12px;">
                    <div class="col-sm-8">
                        <h3 class="section-title">Priority DF Timeline</h3>
                        <p class="section-note">Gantt-style view of the most important DFs. Light bar shows planned window, green bar shows current movement.</p>
                    </div>
                    <div class="col-sm-4 text-right">
                        <?php if ($timeline_start && $timeline_end): ?>
                            <span class="mini-badge"><?php echo df_issue_review_date_label($timeline_start); ?> - <?php echo df_issue_review_date_label($timeline_end); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($gantt_rows) && $timeline_start && $timeline_end): ?>
                    <div class="legend-row">
                        <span class="legend-item"><span class="legend-swatch legend-plan"></span> Planned timeline</span>
                        <span class="legend-item"><span class="legend-swatch legend-current"></span> Current progress line</span>
                        <span class="legend-item"><span class="legend-swatch legend-today"></span> Today</span>
                    </div>

                    <div class="gantt-shell">
                        <div class="gantt-head">
                            <div class="gantt-meta-col">
                                <div class="gantt-head-title">Priority DFs</div>
                            </div>
                            <div class="gantt-scale-col">
                                <span class="gantt-scale-mark" style="left: 0%;"><?php echo df_issue_review_date_label($timeline_start); ?></span>
                                <span class="gantt-scale-mark" style="left: 50%;"><?php echo df_issue_review_date_label($timeline_mid); ?></span>
                                <span class="gantt-scale-mark" style="left: 100%;"><?php echo df_issue_review_date_label($timeline_end); ?></span>
                            </div>
                        </div>

                        <?php foreach ($gantt_rows as $row): ?>
                            <?php
                            $plan_left = df_issue_review_offset_pct($row['planned_start_date'], $timeline_start, $timeline_days);
                            $plan_width = df_issue_review_span_pct($row['planned_start_date'], $row['planned_end_date'], $timeline_days);
                            $current_end_date = !empty($row['actual_end_date']) ? $row['actual_end_date'] : $row['planned_end_date'];
                            $current_width = df_issue_review_span_pct($row['planned_start_date'], $current_end_date, $timeline_days);
                            ?>
                            <div class="gantt-row">
                                <div class="gantt-meta-col">
                                    <div class="gantt-meta">
                                        <div class="gantt-df"><?php echo htmlspecialchars($row['df_no']); ?></div>
                                        <div class="gantt-sub"><?php echo htmlspecialchars($row['top_department']); ?> | <?php echo htmlspecialchars($row['primary_owner']); ?></div>
                                        <div class="gantt-meta-tags">
                                            <span class="severity-pill <?php echo df_issue_review_severity_class($row['severity_label']); ?>"><?php echo htmlspecialchars($row['severity_label']); ?></span>
                                            <span class="status-pill <?php echo df_issue_review_status_class($row['status_label']); ?>"><?php echo htmlspecialchars($row['status_label']); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="gantt-track-col">
                                    <div class="gantt-track">
                                        <?php if ($today_marker !== null): ?>
                                            <span class="gantt-today" style="left: <?php echo $today_marker; ?>%;"></span>
                                        <?php endif; ?>
                                        <span class="gantt-bar gantt-bar-plan" style="left: <?php echo $plan_left; ?>%; width: <?php echo min(100 - $plan_left, $plan_width); ?>%;"></span>
                                        <span class="gantt-bar gantt-bar-current" style="left: <?php echo $plan_left; ?>%; width: <?php echo min(100 - $plan_left, $current_width); ?>%;"></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">Timeline data is not available for the selected filter.</div>
                <?php endif; ?>
            </div>

            <div class="panel-card section-card">
                <div class="row" style="margin-bottom: 10px;">
                    <div class="col-sm-8">
                        <h3 class="section-title">Management Action Table</h3>
                        <p class="section-note">Short list of the DFs to discuss, with only the action that matters next.</p>
                    </div>
                    <div class="col-sm-4 text-right">
                        <span class="mini-badge"><i class="fa fa-flag"></i> Top <?php echo number_format(count($priority_dfs ?? array())); ?> DFs</span>
                    </div>
                </div>

                <?php if (empty($priority_dfs)): ?>
                    <div class="empty-state">No DF issue found for the selected filter.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table action-table">
                            <thead>
                                <tr>
                                    <th>DF</th>
                                    <th>Department</th>
                                    <th>Owner</th>
                                    <th>Status</th>
                                    <th class="text-center">Delay</th>
                                    <th>Ticket</th>
                                    <th>Immediate Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($priority_dfs as $df): ?>
                                    <tr>
                                        <td>
                                            <div class="action-df-title"><?php echo htmlspecialchars($df['df_no']); ?></div>
                                            <div class="action-df-sub"><?php echo htmlspecialchars($df['df_description']); ?></div>
                                        </td>
                                        <td><?php echo htmlspecialchars($df['top_department']); ?></td>
                                        <td><?php echo htmlspecialchars($df['primary_owner']); ?></td>
                                        <td>
                                            <div><span class="severity-pill <?php echo df_issue_review_severity_class($df['severity_label']); ?>"><?php echo htmlspecialchars($df['severity_label']); ?></span></div>
                                            <div style="margin-top: 6px;"><span class="status-pill <?php echo df_issue_review_status_class($df['status_label']); ?>"><?php echo htmlspecialchars($df['status_label']); ?></span></div>
                                        </td>
                                        <td class="text-center">
                                            <strong><?php echo number_format($df['max_delay_days']); ?>d</strong><br>
                                            <small class="text-muted">avg <?php echo number_format($df['avg_delay_days'], 1); ?>d</small>
                                        </td>
                                        <td>
                                            <div class="action-note"><?php echo htmlspecialchars($df['ticket_refs_text']); ?></div>
                                        </td>
                                        <td>
                                            <div class="action-note"><?php echo htmlspecialchars($df['improvement_points'][0] ?? 'Weekly follow-up required.'); ?></div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php $this->load->view('common/footer'); ?>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
    <script src="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/chart.js/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
            });

            $('#df_id').select2({
                placeholder: 'Select a DF',
                allowClear: true
            });

            var $fySelect = $('#financial_year');
            var $startDate = $('#start_date');
            var $endDate = $('#end_date');

            function toggleDateInputs(disabled) {
                $startDate.prop('disabled', disabled);
                $endDate.prop('disabled', disabled);
            }

            if ($fySelect.val() !== '') {
                toggleDateInputs(true);
            }

            $fySelect.on('change', function() {
                if ($(this).val() !== '') {
                    toggleDateInputs(true);
                    $startDate.val('');
                    $endDate.val('');
                } else {
                    toggleDateInputs(false);
                }
            });

            $startDate.on('changeDate', function() {
                if ($(this).val() !== '') {
                    $fySelect.val('');
                    toggleDateInputs(false);
                }
            });

            $endDate.on('changeDate', function() {
                if ($(this).val() !== '') {
                    $fySelect.val('');
                    toggleDateInputs(false);
                }
            });

            <?php if (!empty($chart_data['department']['labels'])): ?>
            var deptLabels = <?php echo json_encode(array_values($chart_data['department']['labels'])); ?>;
            var deptIssues = <?php echo json_encode(array_values($chart_data['department']['issues'])); ?>;
            var deptCanvas = document.getElementById('deptIssueChart');
            if (deptCanvas) {
                new Chart(deptCanvas, {
                    type: 'bar',
                    data: {
                        labels: deptLabels,
                        datasets: [{
                            label: 'Issue Count',
                            data: deptIssues,
                            backgroundColor: [
                                '#0f6c5b',
                                '#1a7d68',
                                '#2f8e76',
                                '#4c9d85',
                                '#6cac95',
                                '#8bbba6'
                            ],
                            borderSkipped: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            xAxes: [{
                                gridLines: {
                                    display: false
                                },
                                ticks: {
                                    fontColor: '#61727c'
                                }
                            }],
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    precision: 0,
                                    fontColor: '#61727c'
                                },
                                gridLines: {
                                    color: 'rgba(217, 208, 193, 0.45)'
                                }
                            }]
                        }
                    }
                });
            }
            <?php endif; ?>
        });
    </script>
</body>
</html>
