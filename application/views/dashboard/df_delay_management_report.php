<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> | DF Delay Management Report</title>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css" />

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/chart.js/chart.min.js"></script>

    <style>
        :root{
            --navy:#10233f;
            --navy-soft:#1f3f73;
            --blue:#2f6fed;
            --sky:#73b6ff;
            --ink:#122033;
            --muted:#667892;
            --line:#dbe4f0;
            --panel:#ffffff;
            --panel-soft:#f6f9fd;
            --success:#1f9d63;
            --warning:#dd8f12;
            --danger:#d84f57;
            --info:#3384d8;
            --shadow:0 22px 45px rgba(16, 35, 63, .09);
        }

        body{
            background:
                radial-gradient(circle at top left, rgba(47, 111, 237, .10), transparent 26%),
                linear-gradient(180deg, #edf4fb 0%, #f7fbff 100%);
            color: var(--ink);
        }

        .report-wrap{
            max-width: 1380px;
            margin: 24px auto;
            padding: 0 18px 32px;
        }

        .report-shell{
            border-radius: 28px;
            border: 1px solid rgba(219, 228, 240, .95);
            background: rgba(255,255,255,.96);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .hero-panel{
            position: relative;
            overflow: hidden;
            padding: 28px;
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.14), transparent 24%),
                linear-gradient(135deg, #0d1d36 0%, #1e4f9a 52%, #4e92f0 100%);
            color: #fff;
        }

        .hero-panel:before{
            content:"";
            position:absolute;
            right:-60px;
            bottom:-80px;
            width:240px;
            height:240px;
            border-radius:999px;
            background:rgba(255,255,255,.10);
        }

        .topbar{
            position: relative;
            z-index: 2;
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            gap:18px;
            flex-wrap:wrap;
        }

        .eyebrow{
            display:inline-block;
            padding:6px 12px;
            border-radius:999px;
            background:rgba(255,255,255,.12);
            border:1px solid rgba(255,255,255,.18);
            font-size:11px;
            font-weight:900;
            letter-spacing:.09em;
            text-transform:uppercase;
        }

        .hero-title{
            margin-top:14px;
            font-size:34px;
            line-height:1.02;
            font-weight:900;
            letter-spacing:-.03em;
        }

        .hero-copy{
            margin-top:10px;
            max-width:780px;
            color:rgba(255,255,255,.83);
            font-size:14px;
            line-height:1.55;
        }

        .hero-actions{
            display:flex;
            flex-wrap:wrap;
            gap:10px;
        }

        .action-btn,
        .action-btn:hover,
        .action-btn:focus{
            text-decoration:none;
        }

        .action-btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-height:44px;
            padding:0 16px;
            border-radius:14px;
            border:1px solid rgba(255,255,255,.18);
            background:rgba(255,255,255,.10);
            color:#fff;
            font-size:12px;
            font-weight:900;
            letter-spacing:.05em;
            text-transform:uppercase;
        }

        .action-btn.light{
            background:#fff;
            color:var(--navy);
            border-color:#fff;
        }

        .action-btn.disabled{
            opacity:.45;
            pointer-events:none;
        }

        .selector-panel{
            position: relative;
            z-index: 2;
            margin-top:22px;
            padding:18px;
            border-radius:22px;
            border:1px solid rgba(255,255,255,.18);
            background:rgba(255,255,255,.08);
            backdrop-filter: blur(12px);
        }

        .selector-grid{
            display:grid;
            grid-template-columns:minmax(0, 1fr) auto;
            gap:14px;
            align-items:end;
        }

        .selector-label{
            display:block;
            margin-bottom:8px;
            font-size:12px;
            font-weight:800;
            color:rgba(255,255,255,.84);
            letter-spacing:.04em;
            text-transform:uppercase;
        }

        .select2-container{ width:100% !important; }
        .select2-container .select2-selection--single{
            height:52px;
            border-radius:16px;
            border:0;
            background:#fff;
            display:flex;
            align-items:center;
            padding:0 14px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered{
            color:var(--ink);
            line-height:52px;
            padding-left:2px;
            font-size:13px;
            font-weight:700;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow{
            height:52px;
            right:12px;
        }
        .select2-dropdown{
            border:1px solid var(--line);
            border-radius:16px;
            overflow:hidden;
        }

        .primary-btn{
            min-width:170px;
            height:52px;
            border:0;
            border-radius:16px;
            background:linear-gradient(135deg, #fff 0%, #dfefff 100%);
            color:var(--navy);
            font-size:13px;
            font-weight:900;
            letter-spacing:.06em;
            text-transform:uppercase;
            box-shadow:0 14px 30px rgba(11, 24, 44, .16);
        }

        .primary-btn[disabled]{
            opacity:.68;
            cursor:not-allowed;
        }

        .inline-alert{
            display:none;
            margin-top:16px;
            padding:12px 14px;
            border-radius:14px;
            border:1px solid #ffd7d9;
            background:#fff2f3;
            color:#9e2b34;
            font-size:12px;
            font-weight:800;
        }

        .body-panel{
            padding:24px;
            background:linear-gradient(180deg, #fbfdff 0%, #f4f8fc 100%);
        }

        .empty-state{
            padding:56px 18px;
            text-align:center;
            border-radius:24px;
            border:1px dashed var(--line);
            background:#fff;
        }

        .empty-state h3{
            margin:0;
            font-size:24px;
            font-weight:900;
            color:var(--navy);
        }

        .empty-state p{
            margin:10px auto 0;
            max-width:580px;
            color:var(--muted);
            font-size:14px;
            line-height:1.6;
        }

        .workspace{ display:none; }

        .headline-card,
        .section-card{
            border-radius:24px;
            border:1px solid var(--line);
            background:#fff;
            box-shadow:0 14px 28px rgba(16, 35, 63, .04);
        }

        .headline-card{
            padding:24px;
        }

        .headline-grid{
            display:grid;
            grid-template-columns:minmax(0, 1.4fr) minmax(280px, .8fr);
            gap:18px;
        }

        .headline-title{
            font-size:30px;
            line-height:1.04;
            font-weight:900;
            color:var(--navy);
            letter-spacing:-.03em;
        }

        .headline-subtitle{
            margin-top:8px;
            color:var(--muted);
            font-size:14px;
            line-height:1.55;
        }

        .badge-row{
            display:flex;
            flex-wrap:wrap;
            gap:10px;
            margin-top:16px;
        }

        .mini-badge{
            display:inline-flex;
            align-items:center;
            padding:8px 12px;
            border-radius:999px;
            background:#eff5fb;
            color:var(--navy-soft);
            font-size:12px;
            font-weight:800;
        }

        .health-pill{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-width:120px;
            padding:9px 14px;
            border-radius:999px;
            font-size:12px;
            font-weight:900;
            letter-spacing:.06em;
            text-transform:uppercase;
        }

        .health-pill.critical{ background:#ffe7e9; color:#b22734; }
        .health-pill.attention{ background:#fff2dc; color:#9a6506; }
        .health-pill.stable{ background:#e6f7ef; color:#187848; }
        .health-pill.closed{ background:#e7effb; color:#315d9d; }
        .health-pill.hold{ background:#edf1f6; color:#5f6c7d; }

        .executive-note{
            height:100%;
            padding:18px;
            border-radius:20px;
            background:linear-gradient(180deg, #0f2341 0%, #17355d 100%);
            color:#fff;
        }

        .executive-note h4{
            margin:0;
            font-size:13px;
            font-weight:900;
            letter-spacing:.08em;
            text-transform:uppercase;
            color:rgba(255,255,255,.72);
        }

        .executive-note p{
            margin:10px 0 0;
            font-size:15px;
            line-height:1.65;
            color:rgba(255,255,255,.88);
        }

        .kpi-grid{
            display:grid;
            grid-template-columns:repeat(6, minmax(0, 1fr));
            gap:14px;
            margin-top:18px;
        }

        .kpi-card{
            padding:16px;
            border-radius:20px;
            border:1px solid var(--line);
            background:linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        }

        .kpi-label{
            font-size:11px;
            font-weight:900;
            letter-spacing:.09em;
            text-transform:uppercase;
            color:var(--muted);
        }

        .kpi-value{
            margin-top:10px;
            font-size:28px;
            line-height:1;
            font-weight:900;
            color:var(--navy);
        }

        .kpi-sub{
            margin-top:8px;
            font-size:12px;
            color:var(--muted);
            line-height:1.45;
        }

        .section-card{
            margin-top:18px;
            padding:22px;
        }

        .section-head{
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            gap:16px;
            margin-bottom:16px;
        }

        .section-title{
            margin:0;
            font-size:22px;
            line-height:1.08;
            font-weight:900;
            color:var(--navy);
            letter-spacing:-.02em;
        }

        .section-copy{
            margin-top:6px;
            color:var(--muted);
            font-size:13px;
        }

        .department-grid{
            display:grid;
            grid-template-columns:repeat(3, minmax(0, 1fr));
            gap:14px;
        }

        .department-card{
            border-radius:22px;
            padding:18px;
            border:1px solid var(--line);
            background:linear-gradient(180deg, #ffffff 0%, #f8fbfe 100%);
        }

        .department-rank{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-width:92px;
            padding:6px 10px;
            border-radius:999px;
            background:#eef4ff;
            color:var(--blue);
            font-size:11px;
            font-weight:900;
            letter-spacing:.08em;
            text-transform:uppercase;
        }

        .department-name{
            margin-top:14px;
            font-size:22px;
            line-height:1.12;
            font-weight:900;
            color:var(--navy);
        }

        .department-stats{
            display:grid;
            grid-template-columns:repeat(3, minmax(0, 1fr));
            gap:10px;
            margin-top:16px;
        }

        .department-stat{
            padding:12px;
            border-radius:16px;
            background:#f4f8fd;
        }

        .department-stat-label{
            font-size:10px;
            font-weight:900;
            letter-spacing:.08em;
            text-transform:uppercase;
            color:var(--muted);
        }

        .department-stat-value{
            margin-top:6px;
            font-size:20px;
            line-height:1;
            font-weight:900;
            color:var(--navy);
        }

        .department-note{
            margin-top:14px;
            padding-top:14px;
            border-top:1px solid #ebf1f8;
            color:var(--muted);
            font-size:13px;
            line-height:1.55;
        }

        .chart-grid{
            display:grid;
            grid-template-columns:minmax(0, 1.35fr) minmax(300px, .85fr);
            gap:18px;
        }

        .chart-panel{
            border-radius:20px;
            border:1px solid var(--line);
            background:linear-gradient(180deg, #ffffff 0%, #f9fbff 100%);
            padding:18px;
            min-height:340px;
        }

        .chart-canvas-wrap{
            position:relative;
            height:270px;
            margin-top:14px;
        }

        .timeline-axis{
            position:relative;
            height:34px;
            margin-left:220px;
            margin-bottom:8px;
            border-bottom:1px solid #e8eef7;
        }

        .timeline-tick{
            position:absolute;
            bottom:0;
            transform:translateX(-50%);
            font-size:11px;
            color:var(--muted);
            white-space:nowrap;
        }

        .timeline-tick:before{
            content:"";
            position:absolute;
            left:50%;
            bottom:20px;
            width:1px;
            height:10px;
            background:#d8e2ee;
        }

        .timeline-row{
            display:grid;
            grid-template-columns:200px minmax(0, 1fr);
            gap:20px;
            align-items:center;
            padding:12px 0;
        }

        .timeline-row + .timeline-row{
            border-top:1px solid #eef3f9;
        }

        .timeline-label{
            font-size:14px;
            font-weight:800;
            color:var(--navy);
        }

        .timeline-meta{
            margin-top:5px;
            color:var(--muted);
            font-size:12px;
        }

        .timeline-track{
            position:relative;
            height:34px;
            border-radius:999px;
            background:#eef4fb;
            overflow:hidden;
        }

        .timeline-plan,
        .timeline-current{
            position:absolute;
            top:7px;
            height:20px;
            border-radius:999px;
        }

        .timeline-plan{
            background:#cfe0fb;
        }

        .timeline-current{
            background:linear-gradient(135deg, #f17b80 0%, #d84f57 100%);
            box-shadow:0 8px 18px rgba(216, 79, 87, .22);
        }

        .timeline-current.ontrack{
            background:linear-gradient(135deg, #4bb887 0%, #1f9d63 100%);
            box-shadow:0 8px 18px rgba(31, 157, 99, .18);
        }

        .timeline-marker{
            position:absolute;
            top:3px;
            width:2px;
            height:28px;
            background:#0f2341;
            opacity:.48;
        }

        .timeline-empty{
            padding:18px;
            border-radius:18px;
            background:#f7fafe;
            color:var(--muted);
            text-align:center;
        }

        .table-wrap{
            overflow-x:auto;
        }

        .table-clean{
            width:100%;
            margin:0;
        }

        .table-clean thead th{
            border-bottom:1px solid #dfe8f2;
            color:var(--muted);
            font-size:11px;
            font-weight:900;
            letter-spacing:.08em;
            text-transform:uppercase;
            padding:12px 10px;
            background:#f7fafe;
        }

        .table-clean tbody td{
            padding:14px 10px;
            border-top:1px solid #edf2f8;
            vertical-align:top;
            color:var(--ink);
            font-size:13px;
        }

        .delay-pill{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-width:74px;
            padding:6px 10px;
            border-radius:999px;
            background:#ffe6e8;
            color:#b72e38;
            font-size:11px;
            font-weight:900;
            letter-spacing:.05em;
            text-transform:uppercase;
        }

        .soft-note{
            color:var(--muted);
            font-size:12px;
            line-height:1.5;
        }

        .muted-link,
        .muted-link:hover,
        .muted-link:focus{
            color:var(--info);
            text-decoration:none;
            font-weight:800;
        }

        @media (max-width: 1199px){
            .kpi-grid{ grid-template-columns:repeat(3, minmax(0, 1fr)); }
            .department-grid{ grid-template-columns:1fr; }
            .chart-grid{ grid-template-columns:1fr; }
            .headline-grid{ grid-template-columns:1fr; }
        }

        @media (max-width: 767px){
            .report-wrap{ padding:0 12px 20px; }
            .hero-panel, .body-panel{ padding:18px; }
            .selector-grid{ grid-template-columns:1fr; }
            .primary-btn{ width:100%; }
            .kpi-grid{ grid-template-columns:repeat(2, minmax(0, 1fr)); }
            .timeline-axis{ margin-left:0; }
            .timeline-row{ grid-template-columns:1fr; gap:10px; }
            .chart-canvas-wrap{ height:240px; }
        }

        @media (max-width: 520px){
            .kpi-grid{ grid-template-columns:1fr; }
            .department-stats{ grid-template-columns:1fr; }
            .headline-title{ font-size:24px; }
            .hero-title{ font-size:28px; }
        }
    </style>
</head>
<body>
<div class="report-wrap">
    <div class="report-shell">
        <div class="hero-panel">
            <div class="topbar">
                <div>
                    <span class="eyebrow">Management Report</span>
                    <div class="hero-title">DF Delay Management Report</div>
                    <div class="hero-copy">
                        A clean executive view for one DF: planned completion, current delay exposure, top 3 departments driving slippage, and the shortest action list needed for management review.
                    </div>
                </div>

                <div class="hero-actions">
                    <a href="<?php echo page_url; ?>Task/dfreleasedashboard/1" class="action-btn">Delay Dashboard</a>
                    <a href="#" id="openDetailLink" class="action-btn disabled" target="_blank">Full DF Detail</a>
                    <a href="#" id="openGanttLink" class="action-btn light disabled" target="_blank">Task Gantt</a>
                </div>
            </div>

            <div class="selector-panel">
                <div class="selector-grid">
                    <div>
                        <label for="dfSelector" class="selector-label">Select DF</label>
                        <select id="dfSelector" class="form-control">
                            <option value="">Choose DF for management reporting</option>
                            <?php if (!empty($df_options)) { ?>
                                <?php foreach ($df_options as $df_row) { ?>
                                    <?php
                                        $df_label = strtoupper((string) $df_row['df_no']);
                                        if (!empty($df_row['df_description'])) {
                                            $df_label .= ' - ' . $df_row['df_description'];
                                        }
                                    ?>
                                    <option value="<?php echo (int) $df_row['id']; ?>">
                                        <?php echo htmlspecialchars($df_label, ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                    <button type="button" id="loadReportButton" class="primary-btn">Load Report</button>
                </div>
                <div id="inlineAlert" class="inline-alert"></div>
            </div>
        </div>

        <div class="body-panel">
            <div id="emptyState" class="empty-state">
                <h3>Pick a DF to generate the report</h3>
                <p>
                    The page will keep the view short and management-ready: what was planned, where the delay is building, which departments need attention first, and the most critical delayed tasks.
                </p>
            </div>

            <div id="workspace" class="workspace">
                <div class="headline-card">
                    <div class="headline-grid">
                        <div>
                            <div id="headlineTitle" class="headline-title">DF Report</div>
                            <div id="headlineSubtitle" class="headline-subtitle"></div>
                            <div class="badge-row">
                                <span id="healthPill" class="health-pill stable">Stable</span>
                                <span id="planWindowBadge" class="mini-badge">Plan window: -</span>
                                <span id="delayConcentrationBadge" class="mini-badge">Delay concentration: -</span>
                            </div>
                        </div>
                        <div class="executive-note">
                            <h4>Management Focus</h4>
                            <p id="executiveNoteText">Select a DF to load the executive summary.</p>
                        </div>
                    </div>
                </div>

                <div class="kpi-grid">
                    <div class="kpi-card">
                        <div class="kpi-label">Planned Closure</div>
                        <div id="plannedClosureValue" class="kpi-value">-</div>
                        <div id="plannedClosureSub" class="kpi-sub">Planned end date for the DF</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-label">Forecast Closure</div>
                        <div id="forecastClosureValue" class="kpi-value">-</div>
                        <div id="forecastClosureSub" class="kpi-sub">Current expected close based on execution status</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-label">Overall Delay</div>
                        <div id="overallDelayValue" class="kpi-value">0d</div>
                        <div id="overallDelaySub" class="kpi-sub">Difference between planned and forecast close</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-label">Avg Task Delay</div>
                        <div id="averageDelayValue" class="kpi-value">0d</div>
                        <div id="averageDelaySub" class="kpi-sub">Average delay across delayed tasks</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-label">Completion</div>
                        <div id="completionValue" class="kpi-value">0%</div>
                        <div id="completionSub" class="kpi-sub">Completed tasks against active execution plan</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-label">Open Tickets</div>
                        <div id="ticketsValue" class="kpi-value">0</div>
                        <div id="ticketsSub" class="kpi-sub">Open blockers impacting execution</div>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-head">
                        <div>
                            <h3 class="section-title">Top 3 Delay Departments</h3>
                            <div class="section-copy">Keep attention on the departments contributing the highest total delay days.</div>
                        </div>
                    </div>
                    <div id="departmentHighlights" class="department-grid"></div>
                </div>

                <div class="section-card">
                    <div class="section-head">
                        <div>
                            <h3 class="section-title">Delay Pattern</h3>
                            <div class="section-copy">One graph for department pressure and one graph for task execution health.</div>
                        </div>
                    </div>
                    <div class="chart-grid">
                        <div class="chart-panel">
                            <div class="section-title" style="font-size:18px;">Department Delay Exposure</div>
                            <div class="section-copy">Bars show total delay days. The line shows average delay per delayed task.</div>
                            <div class="chart-canvas-wrap">
                                <canvas id="departmentDelayChart"></canvas>
                            </div>
                        </div>
                        <div class="chart-panel">
                            <div class="section-title" style="font-size:18px;">Execution Mix</div>
                            <div class="section-copy">Compact view of how the DF workload is distributed right now.</div>
                            <div class="chart-canvas-wrap">
                                <canvas id="executionMixChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-head">
                        <div>
                            <h3 class="section-title">Department Timeline</h3>
                            <div class="section-copy">Compact Gantt-style view of planned versus current span for the departments under the most delay pressure.</div>
                        </div>
                    </div>
                    <div id="timelineAxis" class="timeline-axis"></div>
                    <div id="timelineRows"></div>
                </div>

                <div class="section-card">
                    <div class="section-head">
                        <div>
                            <h3 class="section-title">Critical Delayed Tasks</h3>
                            <div class="section-copy">Short task list for management discussion. Only the most relevant delayed tasks are shown.</div>
                        </div>
                    </div>
                    <div class="table-wrap">
                        <table class="table table-clean">
                            <thead>
                                <tr>
                                    <th>Task</th>
                                    <th>Department</th>
                                    <th>Owner</th>
                                    <th>Planned End</th>
                                    <th>Delay</th>
                                    <th>Last Update</th>
                                </tr>
                            </thead>
                            <tbody id="delayTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        var pageBaseUrl = <?php echo json_encode(page_url); ?>;
        var presetDfId = <?php echo json_encode((string) (int) $preset_df_id); ?>;
        var departmentDelayChart = null;
        var executionMixChart = null;

        function safeNumber(value) {
            var number = parseFloat(value);
            return isNaN(number) ? 0 : number;
        }

        function escapeHtml(value) {
            return String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function parseDateOnly(value) {
            if (!value || value === '0000-00-00' || value === '0000-00-00 00:00:00') {
                return null;
            }

            var datePart = String(value).split(' ')[0].split('-');
            if (datePart.length !== 3) {
                return null;
            }

            var year = parseInt(datePart[0], 10);
            var month = parseInt(datePart[1], 10) - 1;
            var day = parseInt(datePart[2], 10);

            if (isNaN(year) || isNaN(month) || isNaN(day)) {
                return null;
            }

            return new Date(year, month, day);
        }

        function formatDate(value) {
            var date = parseDateOnly(value);
            if (!date) {
                return '-';
            }

            var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            var day = date.getDate();
            if (day < 10) {
                day = '0' + day;
            }

            return day + ' ' + months[date.getMonth()] + ' ' + date.getFullYear();
        }

        function dateToYmd(date) {
            if (!date) {
                return '';
            }

            var month = date.getMonth() + 1;
            var day = date.getDate();

            if (month < 10) {
                month = '0' + month;
            }

            if (day < 10) {
                day = '0' + day;
            }

            return date.getFullYear() + '-' + month + '-' + day;
        }

        function daysBetween(startDate, endDate) {
            if (!startDate || !endDate) {
                return 0;
            }

            var diff = endDate.getTime() - startDate.getTime();
            return Math.round(diff / 86400000);
        }

        function clipText(value, limit) {
            value = String(value || '').trim();
            if (value.length <= limit) {
                return value;
            }
            return value.substring(0, limit - 3) + '...';
        }

        function showAlert(message) {
            $('#inlineAlert').text(message).stop(true, true).fadeIn(120);
        }

        function hideAlert() {
            $('#inlineAlert').hide().text('');
        }

        function setLoading(isLoading) {
            $('#loadReportButton').prop('disabled', isLoading).text(isLoading ? 'Loading...' : 'Load Report');
        }

        function getHealthClass(key) {
            if (key === 'critical') {
                return 'critical';
            }
            if (key === 'risk' || key === 'attention' || key === 'warning') {
                return 'attention';
            }
            if (key === 'closed') {
                return 'closed';
            }
            if (key === 'hold') {
                return 'hold';
            }
            return 'stable';
        }

        function buildExecutiveNote(summary, topDepartments) {
            var note = summary.health_note || 'Execution status is available for management review.';
            var topNames = [];

            $.each(topDepartments || [], function (_, department) {
                if (department && department.department) {
                    topNames.push(department.department);
                }
            });

            if (topNames.length) {
                note += ' Delay exposure is concentrated in ' + topNames.join(', ') + '.';
            }

            if (safeNumber(summary.overall_delay_days) > 0) {
                note += ' Current overall delay is ' + safeNumber(summary.overall_delay_days) + ' day(s).';
            }

            return note;
        }

        function buildDepartmentAction(department) {
            var avgDelay = safeNumber(department.avg_delay_days);
            var totalDelay = safeNumber(department.total_delay_days);
            var openTasks = safeNumber(department.open);
            var delayedTasks = safeNumber(department.delayed);

            if (avgDelay >= 10) {
                return 'Escalate long-pending dependencies and lock a recovery date for the oldest delayed tasks.';
            }

            if (openTasks >= delayedTasks && delayedTasks > 0) {
                return 'Tighten daily follow-up on open tasks and confirm ownership for every pending handoff.';
            }

            if (totalDelay >= 15) {
                return 'Run a focused review on bottleneck tasks and remove capacity or approval blockers this week.';
            }

            return 'Review task handoff discipline and commit the next closure dates department-wise.';
        }

        function renderSummary(data, selectedText) {
            var dfInfo = data.df_info || {};
            var poInfo = data.po_info || {};
            var summary = data.summary || {};
            var metrics = data.metrics || {};
            var topDepartments = summary.top_delay_departments || [];
            var delayDepartmentLabel = topDepartments.length ? topDepartments[0].department : 'No major delay';
            var marketingPerson = poInfo.marketing_person || 'Marketing not linked';
            var subtitleParts = [];

            if (selectedText) {
                subtitleParts.push(selectedText);
            }
            if (poInfo.pono) {
                subtitleParts.push('PO: ' + poInfo.pono);
            }
            subtitleParts.push('Marketing: ' + marketingPerson);
            if (dfInfo.added_on) {
                subtitleParts.push('Released: ' + formatDate(dfInfo.added_on));
            }

            $('#headlineTitle').text((dfInfo.df_no ? String(dfInfo.df_no).toUpperCase() : 'DF Report') + ' Delay Review');
            $('#headlineSubtitle').text(subtitleParts.join(' | '));
            $('#planWindowBadge').text('Plan window: ' + formatDate(summary.planned_start) + ' to ' + formatDate(summary.planned_end));
            $('#delayConcentrationBadge').text('Delay concentration: ' + delayDepartmentLabel);

            var healthClass = getHealthClass(summary.health_key || '');
            $('#healthPill')
                .removeClass('critical attention stable closed hold')
                .addClass(healthClass)
                .text(summary.health || 'Stable');

            $('#executiveNoteText').text(buildExecutiveNote(summary, topDepartments));

            $('#plannedClosureValue').text(formatDate(summary.planned_end));
            $('#plannedClosureSub').text('Plan starts on ' + formatDate(summary.planned_start));

            $('#forecastClosureValue').text(formatDate(summary.forecast_end || summary.actual_end || summary.planned_end));
            $('#forecastClosureSub').text((summary.actual_end ? 'Actual close captured' : 'Derived from current department slippage'));

            $('#overallDelayValue').text(safeNumber(summary.overall_delay_days) + 'd');
            $('#overallDelaySub').text((safeNumber(summary.overall_delay_days) > 0) ? 'DF is trending beyond plan' : 'No overall slippage against planned close');

            $('#averageDelayValue').text(safeNumber(summary.avg_delay_days) + 'd');
            $('#averageDelaySub').text(safeNumber(summary.delay_task_count) + ' delayed task(s) currently impacting this DF');

            $('#completionValue').text(safeNumber(metrics.completion_pct).toFixed(0) + '%');
            $('#completionSub').text(safeNumber(metrics.completed) + ' of ' + safeNumber(metrics.total) + ' tasks completed');

            $('#ticketsValue').text(safeNumber(summary.open_ticket_count));
            $('#ticketsSub').text((safeNumber(summary.closed_ticket_count) > 0 ? safeNumber(summary.closed_ticket_count) + ' resolved, ' : '') + safeNumber(summary.open_ticket_count) + ' still open');

            if (dfInfo.id) {
                $('#openDetailLink')
                    .attr('href', pageBaseUrl + 'Dashboard/df_full_detail?df_id=' + encodeURIComponent(dfInfo.id))
                    .removeClass('disabled');

                $('#openGanttLink')
                    .attr('href', pageBaseUrl + 'gantt/' + encodeURIComponent(dfInfo.id))
                    .removeClass('disabled');
            }
        }

        function renderDepartmentHighlights(topDepartments) {
            var html = '';

            if (!topDepartments || !topDepartments.length) {
                html = '<div class="timeline-empty">No major department delay is visible for the selected DF right now.</div>';
                $('#departmentHighlights').html(html);
                return;
            }

            $.each(topDepartments.slice(0, 3), function (index, department) {
                html += '<div class="department-card">';
                html += '  <span class="department-rank">Priority ' + (index + 1) + '</span>';
                html += '  <div class="department-name">' + escapeHtml(department.department || 'Department') + '</div>';
                html += '  <div class="department-stats">';
                html += '      <div class="department-stat"><div class="department-stat-label">Delayed Tasks</div><div class="department-stat-value">' + safeNumber(department.delayed) + '</div></div>';
                html += '      <div class="department-stat"><div class="department-stat-label">Total Delay</div><div class="department-stat-value">' + safeNumber(department.total_delay_days) + 'd</div></div>';
                html += '      <div class="department-stat"><div class="department-stat-label">Avg Delay</div><div class="department-stat-value">' + safeNumber(department.avg_delay_days) + 'd</div></div>';
                html += '  </div>';
                html += '  <div class="department-note">' + escapeHtml(buildDepartmentAction(department)) + '</div>';
                html += '</div>';
            });

            $('#departmentHighlights').html(html);
        }

        function destroyCharts() {
            if (departmentDelayChart) {
                departmentDelayChart.destroy();
                departmentDelayChart = null;
            }
            if (executionMixChart) {
                executionMixChart.destroy();
                executionMixChart = null;
            }
        }

        function renderDepartmentDelayChart(departments) {
            var rows = (departments || []).slice(0, 8);
            var labels = [];
            var totalDelayData = [];
            var avgDelayData = [];

            $.each(rows, function (_, row) {
                labels.push(row.department || 'Department');
                totalDelayData.push(safeNumber(row.total_delay_days));
                avgDelayData.push(safeNumber(row.avg_delay_days));
            });

            if (!labels.length) {
                labels = ['No delay'];
                totalDelayData = [0];
                avgDelayData = [0];
            }

            var ctx = document.getElementById('departmentDelayChart');
            if (!ctx) {
                return;
            }

            departmentDelayChart = new Chart(ctx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            type: 'bar',
                            label: 'Total delay days',
                            data: totalDelayData,
                            backgroundColor: 'rgba(216, 79, 87, 0.82)',
                            borderColor: 'rgba(216, 79, 87, 1)',
                            borderWidth: 1
                        },
                        {
                            type: 'line',
                            label: 'Avg delay per delayed task',
                            data: avgDelayData,
                            borderColor: '#dd8f12',
                            backgroundColor: 'rgba(221, 143, 18, 0.12)',
                            borderWidth: 3,
                            fill: false,
                            pointRadius: 4,
                            yAxisID: 'average-delay-axis'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: true,
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
                            },
                            ticks: {
                                fontColor: '#667892'
                            }
                        }],
                        yAxes: [
                            {
                                ticks: {
                                    beginAtZero: true,
                                    fontColor: '#667892'
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Total delay days'
                                },
                                gridLines: {
                                    color: 'rgba(219, 228, 240, 0.6)'
                                }
                            },
                            {
                                id: 'average-delay-axis',
                                position: 'right',
                                ticks: {
                                    beginAtZero: true,
                                    fontColor: '#667892'
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Average delay'
                                },
                                gridLines: {
                                    drawOnChartArea: false
                                }
                            }
                        ]
                    }
                }
            });
        }

        function renderExecutionMixChart(metrics) {
            metrics = metrics || {};

            var completed = safeNumber(metrics.completed) - safeNumber(metrics.closed_delayed);
            if (completed < 0) {
                completed = 0;
            }

            var openOnTrack = safeNumber(metrics.open) - safeNumber(metrics.open_delayed) - safeNumber(metrics.approval_pending);
            if (openOnTrack < 0) {
                openOnTrack = 0;
            }

            var data = [
                completed,
                safeNumber(metrics.closed_delayed),
                openOnTrack,
                safeNumber(metrics.open_delayed),
                safeNumber(metrics.approval_pending),
                safeNumber(metrics.on_hold)
            ];

            var ctx = document.getElementById('executionMixChart');
            if (!ctx) {
                return;
            }

            executionMixChart = new Chart(ctx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: [
                        'Completed On Time',
                        'Completed Late',
                        'Ongoing On Track',
                        'Open Delayed',
                        'Approval Pending',
                        'On Hold'
                    ],
                    datasets: [{
                        data: data,
                        backgroundColor: [
                            '#1f9d63',
                            '#dd8f12',
                            '#3384d8',
                            '#d84f57',
                            '#8a6fd1',
                            '#93a4b8'
                        ],
                        borderColor: '#ffffff',
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutoutPercentage: 68,
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            });
        }

        function buildTimelineDomain(summary, departments) {
            var dates = [];

            function pushDate(value) {
                var date = parseDateOnly(value);
                if (date) {
                    dates.push(date);
                }
            }

            pushDate(summary.planned_start);
            pushDate(summary.planned_end);
            pushDate(summary.forecast_end);

            $.each(departments || [], function (_, department) {
                pushDate(department.planned_start);
                pushDate(department.planned_end);
                pushDate(department.current_end);
            });

            if (!dates.length) {
                return null;
            }

            var minDate = dates[0];
            var maxDate = dates[0];

            $.each(dates, function (_, date) {
                if (date.getTime() < minDate.getTime()) {
                    minDate = date;
                }
                if (date.getTime() > maxDate.getTime()) {
                    maxDate = date;
                }
            });

            if (minDate.getTime() === maxDate.getTime()) {
                maxDate = new Date(maxDate.getTime() + (7 * 86400000));
            }

            return {
                start: minDate,
                end: maxDate,
                range: Math.max(1, daysBetween(minDate, maxDate))
            };
        }

        function getPositionPercent(date, domain) {
            var parsed = parseDateOnly(date);
            if (!parsed || !domain) {
                return 0;
            }

            return Math.max(0, Math.min(100, (daysBetween(domain.start, parsed) / domain.range) * 100));
        }

        function renderTimeline(summary, departments) {
            var rows = (departments || []).slice(0, 3);
            var domain = buildTimelineDomain(summary || {}, rows);
            var axisHtml = '';
            var rowsHtml = '';

            if (!rows.length || !domain) {
                $('#timelineAxis').html('');
                $('#timelineRows').html('<div class="timeline-empty">Timeline view is not available for this DF.</div>');
                return;
            }

            for (var tick = 0; tick < 5; tick++) {
                var tickDate = new Date(domain.start.getTime() + ((domain.range * tick) / 4) * 86400000);
                var tickPercent = (tick / 4) * 100;
                axisHtml += '<div class="timeline-tick" style="left:' + tickPercent + '%;">' + escapeHtml(formatDate(dateToYmd(tickDate))) + '</div>';
            }

            $.each(rows, function (_, row) {
                var planStart = getPositionPercent(row.planned_start, domain);
                var planEnd = getPositionPercent(row.planned_end, domain);
                var currentEnd = getPositionPercent(row.current_end || row.planned_end, domain);
                var planWidth = Math.max(2, planEnd - planStart);
                var currentWidth = Math.max(2, currentEnd - planStart);
                var isOnTrack = safeNumber(row.total_delay_days) <= 0;

                rowsHtml += '<div class="timeline-row">';
                rowsHtml += '  <div>';
                rowsHtml += '      <div class="timeline-label">' + escapeHtml(row.department || 'Department') + '</div>';
                rowsHtml += '      <div class="timeline-meta">Plan end ' + escapeHtml(formatDate(row.planned_end)) + ' | Current span ' + escapeHtml(formatDate(row.current_end || row.planned_end)) + '</div>';
                rowsHtml += '  </div>';
                rowsHtml += '  <div class="timeline-track">';
                rowsHtml += '      <div class="timeline-plan" style="left:' + planStart + '%; width:' + planWidth + '%;"></div>';
                rowsHtml += '      <div class="timeline-current ' + (isOnTrack ? 'ontrack' : '') + '" style="left:' + planStart + '%; width:' + currentWidth + '%;"></div>';
                rowsHtml += '      <div class="timeline-marker" style="left:' + planEnd + '%;"></div>';
                rowsHtml += '  </div>';
                rowsHtml += '</div>';
            });

            $('#timelineAxis').html(axisHtml);
            $('#timelineRows').html(rowsHtml);
        }

        function renderDelayTable(rows) {
            var html = '';
            var list = rows || [];

            if (!list.length) {
                html = '<tr><td colspan="6"><div class="timeline-empty">No delayed task is active for this DF right now.</div></td></tr>';
                $('#delayTableBody').html(html);
                return;
            }

            $.each(list.slice(0, 6), function (_, row) {
                var updateText = clipText(row.latest_update_text || 'No recent update captured.', 90);
                var updateMeta = row.latest_update_on ? ('<div class="soft-note">Updated ' + escapeHtml(formatDate(row.latest_update_on)) + '</div>') : '';

                html += '<tr>';
                html += '  <td><strong>' + escapeHtml(row.task_name || '-') + '</strong><div class="soft-note">' + escapeHtml(row.status_label || '') + '</div></td>';
                html += '  <td>' + escapeHtml(row.department || '-') + '</td>';
                html += '  <td>' + escapeHtml(row.responsible_person || '-') + '</td>';
                html += '  <td>' + escapeHtml(formatDate(row.end_date)) + '</td>';
                html += '  <td><span class="delay-pill">' + safeNumber(row.delay_days) + ' day(s)</span></td>';
                html += '  <td>' + escapeHtml(updateText) + updateMeta + '</td>';
                html += '</tr>';
            });

            $('#delayTableBody').html(html);
        }

        function renderReport(data, selectedText) {
            var summary = data.summary || {};
            var departmentSummary = data.department_summary || [];
            var topDepartments = summary.top_delay_departments || [];
            var delayRows = data.delay_report || [];

            $('#emptyState').hide();
            $('#workspace').show();

            renderSummary(data, selectedText);
            renderDepartmentHighlights(topDepartments);
            destroyCharts();
            renderDepartmentDelayChart(departmentSummary);
            renderExecutionMixChart(data.metrics || {});
            renderTimeline(summary, topDepartments.length ? topDepartments : departmentSummary);
            renderDelayTable(delayRows);
        }

        function loadReport(dfId, selectedText) {
            setLoading(true);
            hideAlert();

            $.ajax({
                url: pageBaseUrl + 'Dashboard/get_df_details/' + encodeURIComponent(dfId),
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    if (!data || !data.df_info || !data.df_info.id) {
                        showAlert('No DF details were found for the selected record.');
                        return;
                    }

                    if (window.history && window.history.replaceState) {
                        window.history.replaceState({}, '', pageBaseUrl + 'Dashboard/df_delay_management_report?df_id=' + encodeURIComponent(dfId));
                    }

                    renderReport(data, selectedText);
                },
                error: function () {
                    showAlert('Unable to load the DF report right now. Please try again.');
                },
                complete: function () {
                    setLoading(false);
                }
            });
        }

        $(function () {
            $('#dfSelector').select2({
                width: '100%'
            });

            $('#loadReportButton').on('click', function () {
                var dfId = $('#dfSelector').val();
                var selectedText = $('#dfSelector option:selected').text();

                if (!dfId) {
                    showAlert('Please select a DF before generating the management report.');
                    return;
                }

                loadReport(dfId, selectedText);
            });

            if (presetDfId && $('#dfSelector option[value="' + presetDfId + '"]').length) {
                $('#dfSelector').val(String(presetDfId)).trigger('change');
                $('#loadReportButton').trigger('click');
            }
        });
    })();
</script>

<?php $this->load->view('common/footer'); ?>
</body>
</html>
