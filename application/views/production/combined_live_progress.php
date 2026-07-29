<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
// Filter ONLY running jobs if mixed data is sent
$running_jobs = [];
if (!empty($jobs)) {
    foreach ($jobs as $j) {
        if (!isset($j->is_active) || (int)$j->is_active === 1) {
            $running_jobs[] = $j;
        }
    }
}

$total_running         = count($running_jobs);
$sum_progress          = 0;
$line_stats            = []; // line_name => ['jobs'=>x, 'total_progress'=>y]
$today                 = date('Y-m-d');
$total_days_weighted   = 0;
$at_risk_count         = 0;
$AI_DAYS_THRESHOLD     = 5;   // days after which low progress is considered "at risk"
$AI_PROGRESS_THRESHOLD = 40;  // progress below this after threshold days => risk

foreach ($running_jobs as $job_row) {

    // Progress field: prefer last_progress, else progress_percent, else 0
    $p = 0;
    if (isset($job_row->last_progress)) {
        $p = (int) $job_row->last_progress;
    } elseif (isset($job_row->progress_percent)) {
        $p = (int) $job_row->progress_percent;
    }

    $sum_progress += $p;

    $line = !empty($job_row->line_name) ? $job_row->line_name : 'Unassigned Line';
    if (!isset($line_stats[$line])) {
        $line_stats[$line] = [
            'jobs'           => 0,
            'total_progress' => 0,
            'avg_progress'   => 0,
        ];
    }
    $line_stats[$line]['jobs']++;
    $line_stats[$line]['total_progress'] += $p;

    // Days in production based on release_date
    if (!empty($job_row->release_date)) {
        $days_spent = (int) floor((strtotime($today) - strtotime($job_row->release_date)) / (60*60*24)) + 1;
        if ($days_spent < 1) $days_spent = 1;
        $total_days_weighted += $days_spent;

        // At-risk rule: old job + low progress
        if ($days_spent >= $AI_DAYS_THRESHOLD && $p < $AI_PROGRESS_THRESHOLD) {
            $at_risk_count++;
        }
    }
}

// Compute line-wise averages
foreach ($line_stats as $lineName => $data) {
    if ($data['jobs'] > 0) {
        $line_stats[$lineName]['avg_progress'] = round($data['total_progress'] / $data['jobs'], 1);
    }
}

// Overall averages
$avg_progress = $total_running > 0 ? round($sum_progress / $total_running, 1) : 0;
$avg_days_per_job = ($total_running > 0 && $total_days_weighted > 0) 
    ? round($total_days_weighted / $total_running, 1)
    : 0;

// AI overview message
$ai_overview = "Overall progress is moving at a healthy pace across running jobs.";
if ($total_running === 0) {
    $ai_overview = "No running jobs found. All lines appear to be idle or completed.";
} elseif ($avg_progress < 30) {
    $ai_overview = "Most jobs are in the early phase. Management should focus on start-up bottlenecks.";
} elseif ($avg_progress >= 30 && $avg_progress < 70) {
    $ai_overview = "Jobs are in mid-phase. Monitor lines with lower-than-average completion to avoid delays.";
} elseif ($avg_progress >= 70) {
    $ai_overview = "Jobs are nearing completion. Plan inspections, QA, and dispatch in advance.";
}

if ($at_risk_count > 0 && $total_running > 0) {
    $ai_overview .= " AI Note: {$at_risk_count} job(s) appear at risk (low progress vs age). Review these urgently.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Live Production Progress - All Lines</title>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <!-- DataTables CDN (same as dashboard style) -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { 
            background: #f3f4f6; 
            font-family: 'Poppins', sans-serif; 
        }
        .wrapper { padding-top: 25px; }

        .page-title { font-size: 20px; font-weight: 700; color: #111827; }
        .text-subtitle { font-size: 13px; color: #6b7280; }

        .stat-card {
            border-radius: 15px;
            padding: 18px 18px;
            color: #fff;
            box-shadow: 0 8px 25px rgba(15,23,42,0.2);
            position: relative;
            overflow: hidden;
        }
        .stat-card h3 {
            font-size: 26px;
            margin: 0;
            font-weight: 700;
        }
        .stat-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.85;
        }
        .bg-gradient-indigo {
            background: linear-gradient(135deg, #4872b8, #312e81);
        }
        .bg-gradient-green {
            background: linear-gradient(135deg, #16a34a, #15803d);
        }
        .bg-gradient-amber {
            background: linear-gradient(135deg, #f97316, #c2410c);
        }
        .stat-icon {
            position: absolute;
            right: 18px;
            bottom: 10px;
            font-size: 42px;
            opacity: 0.2;
        }

        .ai-overview-box {
            background: #ffffff;
            border-radius: 15px;
            padding: 14px 16px;
            box-shadow: 0 10px 30px rgba(15,23,42,0.08);
            font-size: 13px;
            color: #374151;
        }
        .ai-overview-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6b7280;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .line-table-card {
            background: #ffffff;
            border-radius: 15px;
            padding: 14px 16px 10px;
            box-shadow: 0 10px 30px rgba(15,23,42,0.08);
        }

        .section-title {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6b7280;
            font-weight: 600;
        }

        /* Main Jobs Table */
        .card-table {
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(15,23,42,0.08);
            border: none;
        }
        table.dataTable thead th {
            background-color: #f8fafc; 
            color: #475569; 
            font-weight: 700;
            text-transform: uppercase; 
            font-size: 11px; 
            border-bottom: 1px solid #e2e8f0 !important; 
            padding: 12px 10px;
        }
        table.dataTable tbody td {
            padding: 11px 10px; 
            vertical-align: middle; 
            color: #111827; 
            font-size: 13px; 
            border-top: 1px solid #f1f5f9;
        }

        .progress-badge {
            display: inline-flex;
            align-items: center;
            background: #eff6ff;
            color: #1d4ed8;
            border-radius: 999px;
            padding: 3px 9px;
            font-size: 11px;
            font-weight: 600;
        }
        .progress-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            margin-right: 6px;
            background: #22c55e;
        }
        .progress-dot.slow {
            background: #f97316;
        }
        .progress-dot.critical {
            background: #ef4444;
        }

        .job-line-pill {
            display: inline-flex;
            align-items: center;
            padding: 3px 9px;
            border-radius: 999px;
            background: #f3f4ff;
            color: #4338ca;
            font-size: 11px;
            font-weight: 500;
        }

        .btn-view-timeline {
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
        }

        /* DataTables Search Input Tweak */
        div.dataTables_filter input {
            border-radius: 999px !important;
            padding: 4px 10px !important;
            font-size: 12px;
        }
        div.dataTables_length select {
            border-radius: 999px !important;
            padding: 2px 8px !important;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <!-- Top Navigation -->
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>

    <div class="wrapper">
        <div class="container-fluid">

            <!-- Header & CTA -->
            <div class="row align-items-center mb-4 mt-2">
                <div class="col-md-8">
                    <h4 class="page-title mb-1">Live Production Progress</h4>
                    <span class="text-subtitle">Combined view of all currently running jobs across lines</span>
                </div>
                <div class="col-md-4 text-md-right mt-3 mt-md-0">
                    <a href="<?php echo page_url.'Masters/manage_jobs'; ?>" class="btn btn-light btn-sm">
                        <i class="fa fa-list"></i> Back to Job Dashboard
                    </a>
                </div>
            </div>

            <!-- Top Stats + AI Overview -->
            <div class="row mb-3">
                <div class="col-md-3 mb-3">
                    <div class="stat-card bg-gradient-indigo">
                        <div class="stat-label">Running Jobs</div>
                        <h3><?php echo $total_running; ?></h3>
                        <div class="stat-icon"><i class="fa fa-cubes"></i></div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-card bg-gradient-green">
                        <div class="stat-label">Average Completion</div>
                        <h3><?php echo $avg_progress; ?><span style="font-size:16px;">%</span></h3>
                        <div class="stat-icon"><i class="fa fa-line-chart"></i></div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-card bg-gradient-amber">
                        <div class="stat-label">Jobs at Risk (AI)</div>
                        <h3><?php echo $at_risk_count; ?></h3>
                        <div class="stat-icon"><i class="fa fa-exclamation-triangle"></i></div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="ai-overview-box">
                        <div class="ai-overview-title">AI Management Insight</div>
                        <div><?php echo $ai_overview; ?></div>
                    </div>
                </div>
            </div>

            <!-- Line-wise Snapshot -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="line-table-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="section-title">Line-wise Snapshot</span>
                            <small class="text-muted">Quick view of load and completion across production lines.</small>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Line</th>
                                        <th>Running Jobs</th>
                                        <th>Avg Completion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($line_stats)): ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">No running jobs currently.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach($line_stats as $lineName => $data): ?>
                                            <tr>
                                                <td><?php echo $lineName; ?></td>
                                                <td><?php echo $data['jobs']; ?></td>
                                                <td><?php echo $data['avg_progress']; ?>%</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Combined Jobs Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-table">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="section-title">Running Jobs - Detailed View</span>
                                <small class="text-muted">Search by DF, line, machine, or supervisor.</small>
                            </div>
                            <div class="table-responsive">
                                <table id="combinedJobsTable" class="table table-hover dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>DF Number</th>
                                            <th>Line / Machine</th>
                                            <th>Supervisor</th>
                                            <th>Release Date</th>
                                            <th>Last Progress</th>
                                            <th>Last Updated</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($running_jobs)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">No running jobs to show.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach($running_jobs as $row): ?>
                                                <?php
                                                    $p = 0;
                                                    if (isset($row->last_progress)) {
                                                        $p = (int) $row->last_progress;
                                                    } elseif (isset($row->progress_percent)) {
                                                        $p = (int) $row->progress_percent;
                                                    }

                                                    $dotClass = '';
                                                    if ($p < 30) $dotClass = 'critical';
                                                    elseif ($p < 60) $dotClass = 'slow';

                                                    $release_date_str = !empty($row->release_date)
                                                        ? date('d M Y', strtotime($row->release_date))
                                                        : '-';

                                                    $last_date_str = !empty($row->last_log_date)
                                                        ? date('d M Y', strtotime($row->last_log_date))
                                                        : '-';

                                                    $last_time_str = !empty($row->last_log_time)
                                                        ? date('h:i A', strtotime($row->last_log_time))
                                                        : '';
                                                ?>
                                                <tr>
                                                    <td>
                                                        <div class="font-weight-bold text-primary">
                                                            <?php echo $row->df_number; ?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="job-line-pill mb-1">
                                                            <i class="fa fa-map-marker mr-1"></i>
                                                            <?php echo !empty($row->line_name) ? $row->line_name : 'N/A'; ?>
                                                        </div>
                                                        <div style="font-size:12px; color:#6b7280;">
                                                            <i class="fa fa-cogs"></i> 
                                                            <?php echo !empty($row->machine_name) ? $row->machine_name : 'N/A'; ?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div style="font-size:13px; font-weight:600;">
                                                            <?php echo !empty($row->supervisor_name) ? $row->supervisor_name : '-'; ?>
                                                        </div>
                                                    </td>
                                                    <td><?php echo $release_date_str; ?></td>
                                                    <td>
                                                        <span class="progress-badge">
                                                            <span class="progress-dot <?php echo $dotClass; ?>"></span>
                                                            <?php echo $p; ?>%
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if($last_date_str === '-' && $last_time_str === ''): ?>
                                                            <span class="text-muted" style="font-size:12px;">No update yet</span>
                                                        <?php else: ?>
                                                            <div style="font-size:12px;">
                                                                <?php echo $last_date_str; ?>
                                                                <?php if($last_time_str): ?>
                                                                    <span class="text-muted">(<?php echo $last_time_str; ?>)</span>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if(!empty($row->assignment_id)): ?>
                                                            <a href="<?php echo page_url.'Masters/view_job_timeline/'.$row->assignment_id; ?>" 
                                                               class="btn btn-primary btn-xs btn-view-timeline">
                                                               <i class="fa fa-bar-chart"></i> View Timeline
                                                            </a>
                                                        <?php else: ?>
                                                            <span class="text-muted" style="font-size:11px;">No timeline link</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div> <!-- /table-responsive -->
                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- /container-fluid -->
    </div> <!-- /wrapper -->

    <?php $this->load->view('common/footer'); ?>

    <!-- Scripts -->
    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#combinedJobsTable').DataTable({
            "order": [],
            "pageLength": 10,
            "language": { 
                "search": "",
                "searchPlaceholder": "Search DF, line, machine, supervisor...",
                "lengthMenu": "_MENU_ per page"
            },
            "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
                   "<'row'<'col-sm-12'tr>>" +
                   "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "drawCallback": function () { 
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded'); 
            }
        });
    });
    </script>
</body>
</html>
