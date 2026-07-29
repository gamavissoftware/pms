<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; Management Dashboard</title>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f4f7f6; }
        .card-box { 
            background-color: #ffffff; 
            border-radius: 12px; 
            border: 1px solid #e9ecef; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
            padding: 25px; 
            margin-bottom: 25px;
            height: 100%;
        }
        
        /* Trend Card Styles */
        .trend-box { text-align: center; }
        .trend-box .value { font-size: 2.2em; font-weight: 700; margin-bottom: 5px; }
        .trend-box .trend { font-size: 1.1em; font-weight: 600; }
        .trend-box .trend .fa { margin-right: 5px; }
        .trend-box .trend-up { color: #dc3545; }
        .trend-box .trend-down { color: #28a745; }
        .trend-box .trend-neutral { color: #777; }
        .trend-box .label { font-size: 1em; color: #777; margin-top: 5px; }
        .value-loss { color: #a94442; }
        .value-tasks { color: #dc3545; }
        .value-delay { color: #ffc107; }

        /* NEW: Clickable card styles */
        .widget-card-link {
            display: block;
            color: inherit;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
        }
        .widget-card-link:hover, .widget-card-link:focus {
            color: inherit;
            text-decoration: none;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        /* At-Risk List Styles */
        .at-risk-list { list-style: none; padding-left: 0; margin-bottom: 0; }
        .at-risk-list li {
            padding-bottom: 12px; margin-bottom: 12px;
            border-bottom: 1px solid #f4f4f4;
            display: flex; align-items: center;
        }
        .at-risk-list li:last-child { border-bottom: 0; margin-bottom: 0; padding-bottom: 0; }
        .at-risk-list .due-date {
            font-size: 0.9em; font-weight: 600; color: #fff;
            background: #ffc107; border-radius: 4px;
            padding: 8px; text-align: center; min-width: 60px;
        }
        .at-risk-list .due-date .day { display: block; font-size: 1.4em; line-height: 1.2; }
        .at-risk-list .task-info { margin-left: 12px; }
        .at-risk-list .task-info .task-name { font-weight: 600; color: #333; }
        .at-risk-list .task-info .task-owner { font-size: 0.9em; color: #555; }
        
        /* Bottleneck Table */
        .bottleneck-table img {
            width: 35px; height: 35px;
            border-radius: 50%; object-fit: cover;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
    
    <div class="wrapper">
        <div class="container-fluid">
            
            <div class="row" style="margin-top: 20px;">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <div class="row">
                            <div class="col-sm-8">
                                <h4 class="page-title">Management Dashboard: Bird's Eye View</h4>
                            </div>
                            <div class="col-sm-4 text-right">
                                <a href="<?php echo page_url; ?>Df_reports/df_issue_review" class="btn btn-primary waves-effect waves-light">
                                    <i class="fa fa-file-text-o"></i> DF Issue Review
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <h4 class="m-t-0 header-title"><b>30-Day Delay Trends</b> (Click to see details)</h4>
                </div>
                <?php
                    // Helper function to calculate trend
                    function getTrend($current, $previous, $lower_is_better = true) {
                        if ($previous == 0) {
                            return ($current > 0) ? '<span class="trend trend-up"><i class="fa fa-arrow-up"></i> (New)</span>' : '<span class="trend trend-neutral">-</span>';
                        }
                        $perc = (($current - $previous) / $previous) * 100;
                        
                        $up_class = $lower_is_better ? 'trend-up' : 'trend-down';
                        $down_class = $lower_is_better ? 'trend-down' : 'trend-up';

                        if ($perc > 0) {
                            return '<span class="trend ' . $up_class . '"><i class="fa fa-arrow-up"></i> ' . round($perc) . '%</span>';
                        } elseif ($perc < 0) {
                            return '<span class="trend ' . $down_class . '"><i class="fa fa-arrow-down"></i> ' . round(abs($perc)) . '%</span>';
                        }
                        return '<span class="trend trend-neutral"><i class="fa fa-arrows-h"></i> 0%</span>';
                    }
                ?>
                <div class="col-md-4">
                    <a href="<?php echo page_url; ?>Df_reports/trend_detail_report/30" target="_blank" class="widget-card-link">
                        <div class="card-box trend-box">
                            <p class="value value-loss"><i class="fa fa-inr"></i> <?php echo number_format($trends['current']['total_loss'], 0); ?></p>
                            <div class="label">Est. Loss (30 Days)</div>
                            <?php echo getTrend($trends['current']['total_loss'], $trends['previous']['total_loss'], true); ?>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="<?php echo page_url; ?>Df_reports/trend_detail_report/30" target="_blank" class="widget-card-link">
                        <div class="card-box trend-box">
                            <p class="value value-tasks"><?php echo number_format($trends['current']['total_tasks_closed_late'], 0); ?></p>
                            <div class="label">Tasks Closed Late (30 Days)</div>
                            <?php echo getTrend($trends['current']['total_tasks_closed_late'], $trends['previous']['total_tasks_closed_late'], true); ?>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="<?php echo page_url; ?>Df_reports/trend_detail_report/30" target="_blank" class="widget-card-link">
                        <div class="card-box trend-box">
                            <p class="value value-delay"><?php echo round($trends['current']['avg_delay'], 1); ?> <span style="font-size: 0.5em;">Days</span></p>
                            <div class="label">Avg. Delay (30 Days)</div>
                            <?php echo getTrend($trends['current']['avg_delay'], $trends['previous']['avg_delay'], true); ?>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-5">
                    <div class="card-box">
                        <h4 class="widget-title"><i class="fa fa-bell text-warning"></i> Proactive: At-Risk Tasks (Due in 7 Days)</h4>
                        <?php if (empty($at_risk_tasks)): ?>
                            <p class="text-muted">No pending tasks are due in the next 7 days.</p>
                        <?php else: ?>
                            <ul class="at-risk-list">
                            <?php foreach($at_risk_tasks as $task): ?>
                                <li>
                                    <div class="due-date">
                                        <?php echo date('d', strtotime($task['end_date'])); ?>
                                        <span class="day"><?php echo date('M', strtotime($task['end_date'])); ?></span>
                                    </div>
                                    <div class="task-info">
                                        <div class="task-name"><?php echo htmlspecialchars($task['task_name']); ?> (<?php echo htmlspecialchars($task['df_no']); ?>)</div>
                                        <div class="task-owner"><?php echo htmlspecialchars($task['assigned_user_name']); ?> - <?php echo htmlspecialchars($task['department']); ?></div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="card-box">
                        <h4 class="widget-title"><i class="fa fa-pie-chart text-success"></i> Root Cause Analysis (From Tickets)</h4>
                        <canvas id="rootCauseChart" style="max-height: 250px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <h4 class="widget-title"><i class="fa fa-hand-paper-o text-info"></i> Approver Bottlenecks (Top 5)</h4>
                        <p class="text-muted" style="margin-top: -10px; margin-bottom: 15px;">Showing delayed tasks that are "Pending Approval" and waiting on a team leader.</p>
                        <div class="table-responsive">
                            <table class="table table-hover bottleneck-table">
                                <thead>
                                    <tr>
                                        <th>Approver</th>
                                        <th>Department / Team</th>
                                        <th class="text-center">Tasks Waiting</th>
                                        <th class="text-center">Total Days Late (Sum)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($approver_bottlenecks)): ?>
                                        <tr><td colspan="4" class="text-center text-muted">No approver bottlenecks found.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($approver_bottlenecks as $approver): ?>
                                            <tr>
                                                <td>
                                                    <img src="<?php echo !empty($approver['profile_pic']) ? user_profile. $approver['profile_pic'] : user_profile . 'userplaceholder.jpeg'; ?>" alt="pic">
                                                    <strong><?php echo htmlspecialchars($approver['approver_name']); ?></strong>
                                                </td>
                                                <td><?php echo htmlspecialchars($approver['team_name']); ?></td>
                                                <td class="text-center"><strong style="font-size: 1.3em;"><?php echo $approver['pending_tasks']; ?></strong></td>
                                                <td class="text-center"><strong style="font-size: 1.3em; color: #dc3545;"><?php echo $approver['total_delay_days']; ?></strong></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    
    <?php $this->load->view('common/footer'); ?>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
    
   <script>
        $(document).ready(function() {
            
            // --- Root Cause Chart ---
            <?php if (!empty($root_cause_stats)): ?>
            try {
                var rootCauseData = <?php echo json_encode($root_cause_stats); ?>;
                var rcLabels = rootCauseData.map(function(item) { return item.reason; });
                var rcValues = rootCauseData.map(function(item) { return item.reason_count; });

                var ctxRootCause = document.getElementById('rootCauseChart').getContext('2d');
                if (ctxRootCause) {
                    new Chart(ctxRootCause, {
                        type: 'doughnut',
                        data: {
                            labels: rcLabels,
                            datasets: [{
                                label: 'Root Causes',
                                data: rcValues,
                                backgroundColor: [
                                    'rgba(217, 83, 79, 0.7)',  // Red
                                    'rgba(240, 173, 78, 0.7)', // Orange
                                    'rgba(91, 192, 222, 0.7)', // Blue
                                    'rgba(92, 184, 92, 0.7)',  // Green
                                    'rgba(155, 89, 182, 0.7)', // Purple
                                    'rgba(52, 73, 94, 0.7)'    // Grey
                                ],
                                borderColor: '#fff',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                }
                            }
                        }
                    });
                }
            } catch(e) { console.error("Error initializing Root Cause chart:", e); }
            <?php endif; ?>
            // --- END: New Chart ---

        });
    </script>
</body>
</html>
