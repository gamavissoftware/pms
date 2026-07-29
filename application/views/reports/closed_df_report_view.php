<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
if (!function_exists('closedDfReportDate')) {
    function closedDfReportDate($value)
    {
        $value = trim((string) $value);
        if ($value === '' || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
            return '-';
        }

        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return '-';
        }

        return date('d-m-Y', $timestamp);
    }
}

if (!function_exists('closedDfReportCurrency')) {
    function closedDfReportCurrency($value)
    {
        return 'Rs ' . number_format((float) $value, 0);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> | Completed DF Analytics</title>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/chart.js/chart.min.js"></script>

    <style>
        :root{
            --navy:#10233f;
            --blue:#2f6fed;
            --sky:#77b1f5;
            --teal:#13a2a8;
            --green:#1f9d63;
            --amber:#d98c18;
            --red:#d84f57;
            --ink:#132035;
            --muted:#6a7b92;
            --line:#dbe4f0;
            --panel:#ffffff;
            --panel-soft:#f5f9fd;
            --shadow:0 18px 38px rgba(16, 35, 63, .08);
        }

        body{
            background:#f4f7fb;
            color:var(--ink);
        }

        .report-wrap{
            width:100%;
            max-width:none;
            margin:8px auto 16px;
            padding:0 8px;
        }

        .toolbar-card{
            display:flex;
            justify-content:space-between;
            align-items:end;
            gap:14px;
            flex-wrap:wrap;
            padding:12px 14px;
            border-radius:16px;
            border:1px solid var(--line);
            background:#fff;
            box-shadow:0 8px 18px rgba(16,35,63,.05);
        }

        .toolbar-title{
            margin:0;
            font-size:24px;
            line-height:1.1;
            font-weight:800;
            color:var(--navy);
        }

        .toolbar-form{
            display:flex;
            gap:10px;
            align-items:end;
            flex-wrap:wrap;
        }

        .toolbar-group label{
            display:block;
            margin-bottom:6px;
            color:var(--muted);
            font-size:11px;
            font-weight:800;
            letter-spacing:.04em;
            text-transform:uppercase;
        }

        .toolbar-group select{
            min-width:190px;
            height:40px;
            border:1px solid #d6dfeb;
            border-radius:10px;
            font-size:13px;
            font-weight:700;
            color:var(--ink);
            box-shadow:none;
        }

        .toolbar-btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            height:40px;
            padding:0 16px;
            border-radius:10px;
            border:1px solid transparent;
            font-size:12px;
            font-weight:900;
            letter-spacing:.05em;
            text-transform:uppercase;
            text-decoration:none;
        }

        .toolbar-btn.primary{
            background:var(--blue);
            color:#fff;
        }

        .toolbar-btn.secondary{
            background:#fff;
            color:var(--ink);
            border-color:#d6dfeb;
        }

        .toolbar-btn.export{
            background:var(--navy);
            color:#fff;
        }

        .panel{
            margin-top:10px;
            border-radius:18px;
            border:1px solid var(--line);
            background:#fff;
            box-shadow:0 10px 24px rgba(16,35,63,.05);
            overflow:hidden;
        }

        .df-tabs-wrap{
            overflow-x:auto;
            border-bottom:1px solid #e8eef7;
            background:#fff;
        }

        .nav-tabs.df-detail-tabs{
            border-bottom:1px solid #e8eef7;
            padding:0 16px;
            background:#fff;
            display:flex;
            flex-wrap:nowrap;
            min-width:max-content;
        }

        .nav-tabs.df-detail-tabs > li{
            margin-bottom:-1px;
            float:none;
            flex:0 0 auto;
        }

        .nav-tabs.df-detail-tabs > li > a{
            border:0;
            border-bottom:3px solid transparent;
            color:var(--muted);
            font-size:12px;
            font-weight:900;
            letter-spacing:.04em;
            text-transform:uppercase;
            padding:16px 10px 13px;
            margin-right:16px;
            background:transparent;
            white-space:nowrap;
        }

        .nav-tabs.df-detail-tabs > li.active > a,
        .nav-tabs.df-detail-tabs > li.active > a:hover,
        .nav-tabs.df-detail-tabs > li.active > a:focus{
            border:0;
            border-bottom:3px solid var(--blue);
            color:var(--blue);
            background:transparent;
        }

        .tab-pane{
            padding:22px;
        }

        .chart-grid{
            display:grid;
            grid-template-columns:minmax(320px, .9fr) minmax(0, 1.1fr);
            gap:18px;
        }

        .df-summary-grid{
            display:grid;
            grid-template-columns:repeat(4, minmax(0, 1fr));
            gap:12px;
            margin-bottom:18px;
        }

        .df-summary-card{
            padding:14px 16px;
            border-radius:16px;
            border:1px solid var(--line);
            background:#f8fbff;
        }

        .df-summary-label{
            color:var(--muted);
            font-size:11px;
            font-weight:900;
            letter-spacing:.06em;
            text-transform:uppercase;
        }

        .df-summary-value{
            margin-top:8px;
            color:var(--navy);
            font-size:22px;
            line-height:1.1;
            font-weight:800;
        }

        .df-summary-value.small{
            font-size:16px;
        }

        .df-summary-value.good{
            color:var(--green);
        }

        .df-summary-value.bad{
            color:var(--red);
        }

        .section-title{
            margin:0 0 12px;
            color:var(--navy);
            font-size:16px;
            font-weight:800;
        }

        .chart-panel{
            border-radius:20px;
            border:1px solid var(--line);
            background:linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            padding:18px;
            min-height:340px;
        }

        .chart-title{
            margin:0;
            font-size:18px;
            font-weight:900;
            color:var(--navy);
        }

        .chart-subtitle{
            margin-top:6px;
            color:var(--muted);
            font-size:13px;
        }

        .chart-wrap{
            position:relative;
            height:260px;
            margin-top:16px;
        }

        .table-wrap{
            overflow:auto;
        }

        .table-clean{
            width:100%;
            margin:0;
        }

        .table-clean thead th{
            position:sticky;
            top:0;
            z-index:1;
            background:#f6f9fd;
            color:var(--muted);
            font-size:11px;
            font-weight:900;
            letter-spacing:.08em;
            text-transform:uppercase;
            border-bottom:1px solid #dfe8f2;
            padding:12px 10px;
            white-space:nowrap;
        }

        .table-clean tbody td{
            padding:13px 10px;
            border-top:1px solid #edf2f8;
            color:var(--ink);
            font-size:13px;
            vertical-align:top;
            white-space:nowrap;
        }

        .delay-pill,
        .status-pill{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            padding:6px 10px;
            border-radius:999px;
            font-size:11px;
            font-weight:900;
            letter-spacing:.05em;
            text-transform:uppercase;
        }

        .delay-pill{
            min-width:76px;
            background:#ffe7e9;
            color:#b22734;
        }

        .delay-pill.zero{
            background:#e8f7ef;
            color:#187848;
        }

        .status-pill.delayed{
            background:#ffe7e9;
            color:#b22734;
        }

        .status-pill.ontime{
            background:#e8f7ef;
            color:#187848;
        }

        .loss-value{
            color:var(--red);
            font-weight:800;
        }

        .empty-box{
            padding:42px 16px;
            text-align:center;
            border-radius:18px;
            border:1px dashed var(--line);
            background:#fbfdff;
            color:var(--muted);
            font-size:14px;
        }

        @media (max-width: 1199px){
            .df-summary-grid{ grid-template-columns:repeat(2, minmax(0, 1fr)); }
            .chart-grid{ grid-template-columns:1fr; }
        }

        @media (max-width: 767px){
            .report-wrap{ padding:0 6px 16px; }
            .toolbar-card{ align-items:stretch; padding:12px; }
            .toolbar-title{ font-size:20px; }
            .toolbar-form{ flex-direction:column; align-items:stretch; }
            .toolbar-group select, .toolbar-btn{ width:100%; }
            .df-summary-grid{ grid-template-columns:1fr; }
            .tab-pane{ padding:16px; }
        }
    </style>
</head>
<body>
<header id="topnav">
    <?php $this->load->view('common/nav-menu'); ?>
</header>

<div class="wrapper">
    <div class="container-fluid">
        <div class="report-wrap">
            <div class="toolbar-card">
                <h1 class="toolbar-title">Completed DF Analytics</h1>
                <form method="get" action="<?php echo page_url; ?>Df_reports/closed_df_report" class="toolbar-form">
                    <div class="toolbar-group">
                        <label for="financial_year">Financial Year</label>
                        <select name="financial_year" id="financial_year" class="form-control">
                            <option value="">All Years</option>
                            <?php foreach ($financial_year_options as $option) { ?>
                                <option value="<?php echo htmlspecialchars($option, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $selected_fy === $option ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($option, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <button type="submit" class="toolbar-btn primary">Apply Filter</button>
                    <a href="<?php echo page_url; ?>Df_reports/closed_df_report" class="toolbar-btn secondary">Reset</a>
                    <a href="<?php echo page_url; ?>Df_reports/export_closed_df_report_overall_excel<?php echo $selected_fy !== '' ? '?financial_year=' . urlencode($selected_fy) : ''; ?>" class="toolbar-btn export">Export Overall Excel</a>
                    <a href="<?php echo page_url; ?>Df_reports/export_closed_df_report_excel<?php echo $selected_fy !== '' ? '?financial_year=' . urlencode($selected_fy) : ''; ?>" class="toolbar-btn secondary">Export Detailed Excel</a>
                </form>
            </div>

            <div class="panel">
                <?php if (!empty($df_detail_tabs)) { ?>
                    <div class="df-tabs-wrap">
                        <ul class="nav nav-tabs df-detail-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#overall_df_index" aria-controls="overall_df_index" role="tab" data-toggle="tab">
                                    Overall
                                </a>
                            </li>
                            <?php foreach ($df_detail_tabs as $df_tab) { ?>
                                <li role="presentation">
                                    <a href="#<?php echo htmlspecialchars($df_tab['tab_id'], ENT_QUOTES, 'UTF-8'); ?>" aria-controls="<?php echo htmlspecialchars($df_tab['tab_id'], ENT_QUOTES, 'UTF-8'); ?>" role="tab" data-toggle="tab">
                                        <?php echo htmlspecialchars($df_tab['df_no'], ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>

                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="overall_df_index">
                            <div class="chart-grid">
                                <div class="chart-panel">
                                    <h3 class="chart-title">Department Delay Share</h3>
                                    <div class="chart-subtitle">Overall delay contribution by the most delayed departments.</div>
                                    <?php if (!empty($overall_department_pie_chart['labels'])) { ?>
                                        <div class="chart-wrap">
                                            <canvas id="overall_pie_chart"></canvas>
                                        </div>
                                    <?php } else { ?>
                                        <div class="empty-box" style="margin-top:16px;">No delayed department data found for this filter.</div>
                                    <?php } ?>
                                </div>
                                <div class="chart-panel">
                                    <h3 class="chart-title">Top Delayed Departments</h3>
                                    <div class="chart-subtitle">Department-wise total delay days across all completed DFs.</div>
                                    <?php if (!empty($overall_department_bar_chart['labels'])) { ?>
                                        <div class="chart-wrap">
                                            <canvas id="overall_bar_chart"></canvas>
                                        </div>
                                    <?php } else { ?>
                                        <div class="empty-box" style="margin-top:16px;">No delayed department data found for this filter.</div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="panel" style="margin-top:18px; box-shadow:none;">
                                <div class="tab-pane" style="padding-top:18px;">
                                    <h3 class="section-title">All Completed DFs</h3>
                                    <div class="table-wrap">
                                        <table class="table table-clean">
                                            <thead>
                                                <tr>
                                                    <th>DF No</th>
                                                    <th>Marketing</th>
                                                    <th>Release Date</th>
                                                    <th>Planned Completion</th>
                                                    <th>Actual Completion</th>
                                                    <th>Planned Days</th>
                                                    <th>Actual Days</th>
                                                    <th>Difference Days</th>
                                                    <th>Delay Days</th>
                                                    <th>Delay Dept</th>
                                                    <th>Dept Delay Days</th>
                                                    <th>Loss of Delay</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($report_rows as $report_row) { ?>
                                                    <?php $is_delayed = (int) $report_row['df_delay_days'] > 0; ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($report_row['df_no'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($report_row['marketing_person'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo closedDfReportDate($report_row['df_release_date']); ?></td>
                                                        <td><?php echo closedDfReportDate($report_row['planned_closure_date']); ?></td>
                                                        <td><?php echo closedDfReportDate($report_row['actual_completion_date']); ?></td>
                                                        <td><?php echo (int) $report_row['planned_days']; ?></td>
                                                        <td><?php echo (int) $report_row['actual_days']; ?></td>
                                                        <td><?php echo (int) $report_row['difference_days']; ?></td>
                                                        <td>
                                                            <span class="delay-pill <?php echo $is_delayed ? '' : 'zero'; ?>">
                                                                <?php echo (int) $report_row['df_delay_days']; ?>d
                                                            </span>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($report_row['delay_department'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo (int) $report_row['delay_department_days']; ?></td>
                                                        <td class="loss-value"><?php echo closedDfReportCurrency($report_row['estimated_loss']); ?></td>
                                                        <td>
                                                            <span class="status-pill <?php echo $is_delayed ? 'delayed' : 'ontime'; ?>">
                                                                <?php echo htmlspecialchars($report_row['status_label'], ENT_QUOTES, 'UTF-8'); ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php foreach ($df_detail_tabs as $index => $df_tab) { ?>
                            <?php
                            $difference_class = 'small';
                                $pie_title = $df_tab['pie_chart']['mode'] === 'delay' ? 'Department Delay Share' : 'Department Actual Days Share';
                                $pie_subtitle = $df_tab['pie_chart']['mode'] === 'delay'
                                    ? 'Top departments contributing the most delay in this DF.'
                                    : 'Department-wise actual completion span for this DF.';
	                            if ((int) $df_tab['difference_days'] > 0) {
	                                $difference_class .= ' bad';
	                            } elseif ((int) $df_tab['difference_days'] < 0) {
	                                $difference_class .= ' good';
	                            }
                            ?>
                            <div role="tabpanel" class="tab-pane" id="<?php echo htmlspecialchars($df_tab['tab_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="df-summary-grid">
                                    <div class="df-summary-card">
                                        <div class="df-summary-label">DF Release Date</div>
                                        <div class="df-summary-value small"><?php echo closedDfReportDate($df_tab['df_release_date']); ?></div>
                                    </div>
                                    <div class="df-summary-card">
                                        <div class="df-summary-label">Planned Completion</div>
                                        <div class="df-summary-value small"><?php echo closedDfReportDate($df_tab['planned_closure_date']); ?></div>
                                    </div>
                                    <div class="df-summary-card">
                                        <div class="df-summary-label">Actual Completion</div>
                                        <div class="df-summary-value small"><?php echo closedDfReportDate($df_tab['actual_completion_date']); ?></div>
                                    </div>
                                    <div class="df-summary-card">
                                        <div class="df-summary-label">Difference</div>
                                        <div class="df-summary-value <?php echo $difference_class; ?>">
                                            <?php echo (int) $df_tab['difference_days']; ?>d
                                        </div>
                                    </div>
                                </div>

	                                <div class="chart-grid">
	                                    <div class="chart-panel">
	                                        <h3 class="chart-title"><?php echo htmlspecialchars($pie_title, ENT_QUOTES, 'UTF-8'); ?></h3>
	                                        <div class="chart-subtitle"><?php echo htmlspecialchars($pie_subtitle, ENT_QUOTES, 'UTF-8'); ?></div>
	                                        <div class="chart-wrap">
	                                            <canvas id="pie_<?php echo htmlspecialchars($df_tab['tab_id'], ENT_QUOTES, 'UTF-8'); ?>"></canvas>
	                                        </div>
	                                    </div>
                                    <div class="chart-panel">
                                        <h3 class="chart-title">Department Planned vs Actual</h3>
                                        <div class="chart-subtitle">Compares planned days and actual days for the top delayed departments of this DF.</div>
                                        <div class="chart-wrap">
                                            <canvas id="bar_<?php echo htmlspecialchars($df_tab['tab_id'], ENT_QUOTES, 'UTF-8'); ?>"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <div class="panel" style="margin-top:18px; box-shadow:none;">
                                    <div class="tab-pane" style="padding-top:18px;">
                                        <h3 class="section-title">Top 5 Delayed Departments</h3>
                                        <div class="table-wrap">
                                            <table class="table table-clean">
                                                <thead>
                                                    <tr>
                                                        <th>Department</th>
                                                        <th>Planned Completion</th>
                                                        <th>Actual Completion</th>
                                                        <th>Planned Days</th>
                                                        <th>Actual Days</th>
                                                        <th>Difference Days</th>
                                                        <th>Delay Days</th>
                                                        <th>Delayed Tasks</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($df_tab['department_rows'])) { ?>
                                                        <?php foreach ($df_tab['department_rows'] as $department_row) { ?>
                                                            <tr>
                                                                <td><?php echo htmlspecialchars($department_row['department'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                                <td><?php echo closedDfReportDate($department_row['planned_completion_date']); ?></td>
                                                                <td><?php echo closedDfReportDate($department_row['actual_completion_date']); ?></td>
                                                                <td><?php echo (int) $department_row['planned_days']; ?></td>
                                                                <td><?php echo (int) $department_row['actual_days']; ?></td>
                                                                <td><?php echo (int) $department_row['difference_days']; ?></td>
                                                                <td><?php echo (int) $department_row['delay_days']; ?></td>
                                                                <td><?php echo (int) $department_row['delayed_task_count']; ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <tr>
                                                            <td colspan="8">
                                                                <div class="empty-box">No department progress data found for this DF.</div>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
	                <?php } else { ?>
	                    <div class="tab-pane">
	                        <div class="empty-box">No completed DF data found for the selected filter.</div>
	                    </div>
	                <?php } ?>
	            </div>
        </div>
    </div>
</div>

<?php $this->load->view('common/footer'); ?>

<script>
    (function () {
        var overallPieChart = <?php echo json_encode($overall_department_pie_chart); ?>;
        var overallBarChart = <?php echo json_encode($overall_department_bar_chart); ?>;
        var dfTabs = <?php echo json_encode($df_detail_tabs); ?>;
        var dfTabMap = {};
        var overallRendered = false;
        var renderedTabs = {};

        dfTabs.forEach(function (tab) {
            dfTabMap[tab.tab_id] = tab;
        });

        function renderOverallCharts() {
            if (overallRendered) {
                return;
            }

            var overallPieCanvas = document.getElementById('overall_pie_chart');
            var overallBarCanvas = document.getElementById('overall_bar_chart');

            if (overallPieCanvas && overallPieChart && (overallPieChart.labels || []).length > 0) {
                new Chart(overallPieCanvas.getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: overallPieChart.labels || [],
                        datasets: [{
                            data: overallPieChart.values || [],
                            backgroundColor: ['#2f6fed', '#13a2a8', '#d84f57', '#d98c18', '#77b1f5', '#7d89f5', '#38b27f', '#ef6d3e'],
                            borderColor: '#ffffff',
                            borderWidth: 3
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
            }

            if (overallBarCanvas && overallBarChart && (overallBarChart.labels || []).length > 0) {
                new Chart(overallBarCanvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: overallBarChart.labels || [],
                        datasets: [{
                            label: 'Delay Days',
                            data: overallBarChart.values || [],
                            backgroundColor: 'rgba(216,79,87,0.82)',
                            borderColor: 'rgba(216,79,87,1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            position: 'bottom'
                        },
                        scales: {
                            xAxes: [{
                                gridLines: { display: false },
                                ticks: { fontColor: '#6a7b92' }
                            }],
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    fontColor: '#6a7b92'
                                },
                                gridLines: {
                                    color: 'rgba(219,228,240,0.7)'
                                }
                            }]
                        }
                    }
                });
            }

            overallRendered = true;
        }

        function renderTabCharts(tabId) {
            if (!tabId || renderedTabs[tabId] || !dfTabMap[tabId]) {
                return;
            }

            var tabData = dfTabMap[tabId];
            var pieCanvas = document.getElementById('pie_' + tabId);
            var barCanvas = document.getElementById('bar_' + tabId);

            if (pieCanvas && tabData.pie_chart && (tabData.pie_chart.labels || []).length > 0) {
                new Chart(pieCanvas.getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: tabData.pie_chart.labels || [],
                        datasets: [{
                            data: tabData.pie_chart.values || [],
                            backgroundColor: ['#2f6fed', '#13a2a8', '#d84f57', '#d98c18', '#77b1f5'],
                            borderColor: '#ffffff',
                            borderWidth: 3
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
            }

            if (barCanvas && tabData.bar_chart && (tabData.bar_chart.labels || []).length > 0) {
                new Chart(barCanvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: tabData.bar_chart.labels || [],
                        datasets: [
                            {
                                label: 'Planned Days',
                                data: tabData.bar_chart.planned_values || [],
                                backgroundColor: 'rgba(47,111,237,0.82)',
                                borderColor: 'rgba(47,111,237,1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Actual Days',
                                data: tabData.bar_chart.actual_values || [],
                                backgroundColor: 'rgba(216,79,87,0.82)',
                                borderColor: 'rgba(216,79,87,1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            position: 'bottom'
                        },
                        scales: {
                            xAxes: [{
                                gridLines: { display: false },
                                ticks: { fontColor: '#6a7b92' }
                            }],
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    fontColor: '#6a7b92'
                                },
                                gridLines: {
                                    color: 'rgba(219,228,240,0.7)'
                                }
                            }]
                        }
                    }
                });
            }

            renderedTabs[tabId] = true;
        }

        $(function () {
            renderOverallCharts();

            var firstTabRef = $('.df-detail-tabs li.active a').attr('href');
            if (firstTabRef) {
                renderTabCharts(firstTabRef.replace('#', ''));
            }

            $('.df-detail-tabs a[data-toggle="tab"]').on('shown.bs.tab', function (event) {
                var target = $(event.target).attr('href');
                if (target) {
                    if (target === '#overall_df_index') {
                        renderOverallCharts();
                    }
                    renderTabCharts(target.replace('#', ''));
                }
            });
        });
    })();
</script>
</body>
</html>
