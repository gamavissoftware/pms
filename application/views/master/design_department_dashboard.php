<?php
$company = $this->db->select('colorcode')->from('company_information')->limit(1)->get()->row();
$themeColor = (!empty($company->colorcode)) ? $company->colorcode : '#159bd3';
$today = date('Y-m-d');
$weekEnd = date('Y-m-d', strtotime('+7 days'));
$monthStart = date('Y-m-d', strtotime('-29 days'));
$currentWeekStart = date('Y-m-d', strtotime('monday this week'));
$currentWeekEnd = date('Y-m-d', strtotime('sunday this week'));
$upcomingWeekStart = date('Y-m-d', strtotime('monday next week'));
$upcomingWeekEnd = date('Y-m-d', strtotime('sunday next week'));

function designDashboardPersonName($name)
{
    $name = trim(preg_replace('/\s+/', ' ', (string) $name));
    return $name === '' ? 'Unassigned' : ucwords(strtolower($name));
}

function designDashboardValidDate($date)
{
    $date = trim((string) $date);
    return $date !== '' && $date !== '0000-00-00' && $date !== '0000-00-00 00:00:00';
}

$departments = $this->db->select('department_id, department')->from('departments')
    ->like('department', 'design')->order_by('department', 'ASC')->get()->result_array();
$departmentIds = array_map(function ($row) { return (int) $row['department_id']; }, $departments);

$tasks = array();
if (!empty($departmentIds)) {
    $tasks = $this->db->select('td.id, td.df_id, td.assigned_user, td.start_date, td.end_date, td.task_status, td.task_completed_on, td.remarks, tm.task_name, dr.df_no, dr.df_description, dr.df_status, dr.on_hold AS df_on_hold, d.department, u.user_status AS designer_user_status, TRIM(CONCAT(IFNULL(u.title, ""), " ", IFNULL(u.first_name, ""), " ", IFNULL(u.last_name, ""))) AS designer', false)
        ->from('task_department_wise_scheduling td')
        ->join('task_management tm', 'tm.task_id = td.taskid', 'left')
        ->join('df_release dr', 'dr.id = td.df_id', 'left')
        ->join('departments d', 'd.department_id = td.department_id', 'left')
        ->join('system_users u', 'u.user_id = td.assigned_user', 'left')
        ->where_in('td.department_id', $departmentIds)
        ->where('IFNULL(td.on_hold, 0) =', 0, false)
        ->order_by('td.end_date', 'ASC')->get()->result_array();
}

$stats = array('running_df' => 0, 'open' => 0, 'due_today' => 0, 'overdue' => 0, 'planned_today' => 0, 'completed_month' => 0, 'completed_on_time' => 0);
$stats['running_df'] = $this->db->from('df_release')
    ->where('df_status', 0)
    ->where('on_hold', 0)
    ->count_all_results();
$designerMap = array();
$daily = array();
for ($i = 6; $i >= 0; $i--) {
    $key = date('Y-m-d', strtotime('-' . $i . ' days'));
    $daily[$key] = array('label' => date('d M', strtotime($key)), 'planned' => 0, 'completed' => 0);
}

foreach ($tasks as &$task) {
    $status = (int) $task['task_status'];
    $isDone = ($status === 1);
    $isOpen = in_array($status, array(0, 2), true);
    $hasCompletedDate = designDashboardValidDate($task['task_completed_on']);
    $completedDate = $hasCompletedDate ? date('Y-m-d', strtotime($task['task_completed_on'])) : '';
    $task['completed_date'] = $completedDate;
    $designer = designDashboardPersonName($task['designer']);
    $hasDueDate = !empty($task['end_date']) && $task['end_date'] !== '0000-00-00';
    $completionDelayDays = 0;
    if ($isDone && $hasDueDate && $completedDate !== '' && $completedDate > $task['end_date']) {
        $completionDelayDays = (int) floor((strtotime($completedDate) - strtotime($task['end_date'])) / 86400);
    }
    $task['completion_delay_days'] = $completionDelayDays;
    $task['completion_outcome'] = $isDone ? ($completionDelayDays > 0 ? 'Completed Late' : 'Completed On Time') : '';
    $task['designer'] = $designer;
    $task['status_key'] = $isDone ? 'completed' : ($status === 2 ? 'approval' : 'open');
    $task['status_label'] = $isDone ? 'Completed' : ($status === 2 ? 'Waiting Approval' : 'Open');
    if ($isOpen && $hasDueDate && $task['end_date'] < $today) { $task['status_key'] = 'overdue'; $task['status_label'] = 'Overdue'; }
    elseif ($isOpen && $hasDueDate && $task['end_date'] === $today) { $task['status_key'] = 'today'; $task['status_label'] = 'Due Today'; }

    if ($isOpen) $stats['open']++;
    if ($isOpen && $task['end_date'] === $today) $stats['due_today']++;
    if ($isOpen && $hasDueDate && $task['end_date'] < $today) $stats['overdue']++;
    if ($isOpen && $task['start_date'] === $today) $stats['planned_today']++;
    if ($isDone && $completedDate >= $monthStart && $completedDate <= $today) {
        $stats['completed_month']++;
        if ($hasDueDate && $completedDate <= $task['end_date']) $stats['completed_on_time']++;
    }
    if (!isset($designerMap[$designer])) $designerMap[$designer] = array('name' => $designer, 'assigned' => 0, 'open' => 0, 'overdue' => 0, 'completed' => 0, 'on_time' => 0);
    $designerMap[$designer]['assigned']++;
    if ($isOpen) $designerMap[$designer]['open']++;
    if ($isOpen && $hasDueDate && $task['end_date'] < $today) $designerMap[$designer]['overdue']++;
    if ($isDone && $completedDate >= $monthStart) {
        $designerMap[$designer]['completed']++;
        if ($hasDueDate && $completedDate <= $task['end_date']) $designerMap[$designer]['on_time']++;
    }
    if (isset($daily[$task['start_date']])) $daily[$task['start_date']]['planned']++;
    if (isset($daily[$completedDate])) $daily[$completedDate]['completed']++;
}
unset($task);
foreach ($designerMap as &$designerRow) {
    $designerRow['on_time_pct'] = $designerRow['completed'] > 0 ? round(($designerRow['on_time'] / $designerRow['completed']) * 100) : 0;
}
unset($designerRow);
usort($designerMap, function ($a, $b) { return $b['assigned'] - $a['assigned']; });
$onTimeRate = $stats['completed_month'] > 0 ? round(($stats['completed_on_time'] / $stats['completed_month']) * 100) : 0;
$designerPerformanceTasks = array();
foreach ($tasks as $task) {
    if ((int) $task['assigned_user'] <= 0 || $task['designer'] === 'Unassigned' || (int) $task['designer_user_status'] !== 1) {
        continue;
    }
    $designerPerformanceTasks[] = array(
        'user_id' => (int) $task['assigned_user'],
        'name' => $task['designer'],
        'df_no' => !empty($task['df_no']) ? $task['df_no'] : '-',
        'task_name' => !empty($task['task_name']) ? $task['task_name'] : 'Unnamed task',
        'start_date' => designDashboardValidDate($task['start_date']) ? date('Y-m-d', strtotime($task['start_date'])) : '',
        'due_date' => designDashboardValidDate($task['end_date']) ? date('Y-m-d', strtotime($task['end_date'])) : '',
        'completed_date' => $task['completed_date'],
        'status' => (int) $task['task_status'],
        'remarks' => !empty($task['remarks']) ? $task['remarks'] : '-'
    );
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo sitetitle; ?> Design Department Dashboard</title>
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet"><link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet"><link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet"><link href="<?php echo assets_url; ?>css/our.css" rel="stylesheet"><link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet">
    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
    <style>
        body{background:#f3f6fb}.design-hero{background:linear-gradient(135deg,<?php echo $themeColor; ?> 0%,#12263d 100%);border-radius:18px;padding:27px;color:#fff;margin:20px 0;box-shadow:0 12px 35px rgba(0,0,0,.13);position:relative;overflow:hidden}.design-hero:after{content:"";position:absolute;width:270px;height:270px;border-radius:50%;right:-85px;top:-110px;background:rgba(255,255,255,.08)}.design-hero h2{color:#fff;margin:0;font-size:28px;font-weight:900}.hero-sub{margin-top:8px;opacity:.9}.hero-pill{display:inline-block;margin-top:13px;padding:7px 13px;border-radius:25px;background:rgba(255,255,255,.16);font-weight:700}.hero-date{text-align:right;position:relative;z-index:1}.hero-date strong{display:block;font-size:25px;margin-top:4px}.kpi{background:#fff;border:1px solid #edf0f5;border-radius:16px;padding:18px;min-height:122px;margin-bottom:18px;position:relative;overflow:hidden;box-shadow:0 6px 18px rgba(31,45,61,.05)}.kpi:after{content:"";position:absolute;width:86px;height:86px;border-radius:50%;right:-22px;bottom:-32px;background:#f0f6fc}.kpi i{position:absolute;right:15px;top:15px;width:38px;height:38px;border-radius:12px;background:#19a1dc;color:#fff;text-align:center;line-height:38px;font-size:17px;z-index:1}.kpi label{display:block;color:#758195;font-size:12px;text-transform:uppercase;letter-spacing:.4px}.kpi b{display:block;font-size:28px;color:#142034;line-height:35px}.kpi span{font-size:12px;color:#8b96a8}.panel-box{background:#fff;border:1px solid #edf0f5;border-radius:16px;padding:18px;margin-bottom:18px;box-shadow:0 5px 16px rgba(31,45,61,.04)}.panel-title{font-size:15px;font-weight:800;color:#182438;margin-bottom:16px}.panel-title i{color:#18a0d8;margin-right:7px}.filter-panel label{font-size:11px;text-transform:uppercase;color:#707c90}.form-control{border-radius:20px;border-color:#e4e9f0;box-shadow:none}.summary-filter{display:flex;align-items:flex-end;gap:10px;flex-wrap:wrap;margin-bottom:16px}.summary-filter .field{min-width:175px}.summary-filter label{display:block;font-size:11px;text-transform:uppercase;color:#707c90}.summary-filter .btn{border-radius:20px;font-weight:700;padding-left:18px;padding-right:18px}.performance-summary-table th{text-align:center}.performance-summary-table td{text-align:center;font-size:14px}.performance-summary-table td.user-cell{text-align:left;font-weight:800;color:#1b293d}.metric-link{background:none;border:0;padding:3px 8px;border-radius:12px;cursor:pointer}.metric-link:hover{background:#eaf6fc;text-decoration:underline}.metric-count{font-size:17px;font-weight:900}.metric-completed{color:#159b70}.metric-ongoing{color:#2477b3}.metric-late{color:#d7463f}.performance-pill{display:inline-block;min-width:68px;padding:6px 10px;border-radius:15px;background:#e6f7f1;color:#16865f;font-weight:900}.detail-modal .modal-content{border:0;border-radius:16px;overflow:hidden}.detail-modal .modal-header{background:linear-gradient(135deg,#159fd5,#174367);color:#fff}.detail-modal .modal-title{color:#fff;font-weight:800}.detail-modal .close{color:#fff;opacity:1}.detail-modal .modal-body{max-height:65vh;overflow:auto}.detail-modal table th{background:#159fd5;color:#fff;white-space:nowrap}.chart-wrap{height:260px}.performance-list{max-height:260px;overflow:auto}.person-row{display:flex;align-items:center;border-bottom:1px solid #eff2f6;padding:10px 0}.person-avatar{width:38px;height:38px;border-radius:50%;background:#e9f6fc;color:#118cbe;display:flex;align-items:center;justify-content:center;font-weight:900;margin-right:10px}.person-info{flex:1}.person-info b{display:block;color:#172236}.person-info small{color:#8994a5}.score{text-align:right}.score b{color:#119b71}.progress{height:6px;margin:5px 0 0;background:#edf1f5}.progress-bar{background:#18a0d8}.table-card{padding:18px;overflow:hidden}.table>thead>tr>th{background:#159fd5;color:#fff;border:0;font-size:12px;white-space:nowrap}.table>tbody>tr>td{vertical-align:middle}.table>tbody>tr.completed-late-row>td{background:#fff1f0!important;border-top-color:#f7cbc7}.table>tbody>tr.completed-late-row{box-shadow:inset 4px 0 #e5544b}.badge-status{display:inline-block;padding:6px 9px;border-radius:14px;font-size:11px;font-weight:800;white-space:nowrap}.status-overdue{background:#fde8e8;color:#c93636}.status-today{background:#fff2d9;color:#b26a00}.status-completed{background:#e4f7ef;color:#16885f}.status-completed-late{background:#fbd9d6;color:#b92e28}.status-open,.status-approval{background:#e7f1fb;color:#2572ae}.task-name{font-weight:700;color:#1a273a}.df-meta{font-size:11px;color:#8a95a6}.empty-design{text-align:center;padding:45px;color:#7f8a9a}.quick-views{margin-top:14px}.quick-view{border:1px solid #dfe8f1;background:#f7fafc;color:#536176;border-radius:18px;padding:6px 12px;margin:0 5px 5px 0;font-size:12px;font-weight:700}.quick-view.active,.quick-view:hover{background:#159fd5;border-color:#159fd5;color:#fff}.dataTables_filter{display:none}.dataTables_wrapper .dataTables_length{float:left;margin-bottom:12px}.dataTables_wrapper .dataTables_info{float:left;clear:both;padding-top:14px;color:#748094}.dataTables_wrapper .dataTables_paginate{float:right;padding-top:9px;text-align:right}.dataTables_wrapper .dataTables_paginate .paginate_button{display:inline-flex!important;align-items:center;justify-content:center;min-width:34px;height:34px;margin-left:5px!important;padding:0 10px!important;border:1px solid #dce5ee!important;border-radius:8px!important;background:#fff!important;color:#536176!important;box-shadow:none!important;cursor:pointer}.dataTables_wrapper .dataTables_paginate .paginate_button:hover{background:#eaf7fc!important;border-color:#159fd5!important;color:#159fd5!important}.dataTables_wrapper .dataTables_paginate .paginate_button.current,.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover{background:#159fd5!important;border-color:#159fd5!important;color:#fff!important}.dataTables_wrapper .dataTables_paginate .paginate_button.disabled,.dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover{background:#f5f7fa!important;border-color:#e8edf2!important;color:#aeb7c3!important;cursor:default}.dataTables_wrapper table.dataTable{width:100%!important;margin:0!important}.dataTables_scroll{clear:both}.dataTables_wrapper:after{content:"";display:block;clear:both}.serial-cell{font-weight:700;color:#607087}@media(max-width:767px){.hero-date{text-align:left;margin-top:18px}.design-hero h2{font-size:23px}.chart-wrap{height:220px}.filter-panel .form-control{margin-bottom:10px}.dataTables_wrapper .dataTables_info,.dataTables_wrapper .dataTables_paginate{float:none;width:100%;text-align:center}.dataTables_wrapper .dataTables_paginate{margin-top:7px}.dataTables_wrapper .dataTables_paginate .paginate_button{margin:3px!important}}
        .kpi-link{display:block;color:inherit;text-decoration:none}.kpi-link:hover,.kpi-link:focus{color:inherit;text-decoration:none}.kpi-link .kpi{cursor:pointer;transition:transform .18s ease,box-shadow .18s ease,border-color .18s ease}.kpi-link:hover .kpi,.kpi-link:focus .kpi{transform:translateY(-2px);border-color:#b9def0;box-shadow:0 10px 24px rgba(31,45,61,.12)}
    </style>
</head>
<body>
<header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
<div class="wrapper"><div class="container-fluid">
    <div class="design-hero"><div class="row"><div class="col-md-8"><h2>Design Department Dashboard</h2><div class="hero-sub">Today’s plan, due tasks, workload and designer performance in one view.</div><div class="hero-pill"><i class="fa fa-paint-brush"></i> Live design task tracking</div></div><div class="col-md-4 hero-date"><span>Dashboard Date</span><strong><?php echo date('d M Y'); ?></strong><small><?php echo date('l'); ?></small></div></div></div>

    <div class="row">
        <div class="col-md-3 col-sm-4"><a class="kpi-link" href="<?php echo page_url; ?>Task/dfreleasedashboard/" title="Open Running DF report"><div class="kpi"><i class="fa fa-folder-open"></i><label>Running DF</label><b><?php echo $stats['running_df']; ?></b><span>All active DFs company-wide</span></div></a></div>
        <div class="col-md-3 col-sm-4"><a class="kpi-link design-kpi-report" href="#designTasks" data-report="planned_today" title="View tasks planned today"><div class="kpi"><i class="fa fa-calendar-check-o"></i><label>Planned Today</label><b><?php echo $stats['planned_today']; ?></b><span>Tasks starting today</span></div></a></div>
        <div class="col-md-3 col-sm-4"><a class="kpi-link design-kpi-report" href="#designTasks" data-report="due_today" title="View tasks due today"><div class="kpi"><i class="fa fa-clock-o"></i><label>Due Today</label><b><?php echo $stats['due_today']; ?></b><span>Need action today</span></div></a></div>
        <div class="col-md-3 col-sm-4"><a class="kpi-link design-kpi-report" href="#designTasks" data-report="overdue" title="View overdue tasks"><div class="kpi"><i class="fa fa-exclamation-triangle"></i><label>Overdue</label><b><?php echo $stats['overdue']; ?></b><span>Past committed date</span></div></a></div>
        <div class="col-md-3 col-sm-4"><a class="kpi-link design-kpi-report" href="#designTasks" data-report="open" title="View all open tasks"><div class="kpi"><i class="fa fa-list-ul"></i><label>Open Tasks</label><b><?php echo $stats['open']; ?></b><span>Pending / approval</span></div></a></div>
        <div class="col-md-3 col-sm-4"><a class="kpi-link design-kpi-report" href="#designTasks" data-report="completed_month" title="View tasks completed in the last 30 days"><div class="kpi"><i class="fa fa-check"></i><label>30-Day Output</label><b><?php echo $stats['completed_month']; ?></b><span>Tasks completed</span></div></a></div>
        <div class="col-md-3 col-sm-4"><a class="kpi-link design-kpi-report" href="#designTasks" data-report="on_time_month" title="View on-time completions from the last 30 days"><div class="kpi"><i class="fa fa-line-chart"></i><label>On-Time Rate</label><b><?php echo $onTimeRate; ?>%</b><span>Last 30 days</span></div></a></div>
    </div>

    <div class="panel-box">
        <div class="panel-title"><i class="fa fa-table"></i> Designer Performance Summary</div>
        <div class="summary-filter">
            <div class="field"><label>From Date</label><input type="date" id="summaryFromDate" class="form-control" value=""></div>
            <div class="field"><label>To Date</label><input type="date" id="summaryToDate" class="form-control" value=""></div>
            <button type="button" id="applySummaryDates" class="btn btn-primary"><i class="fa fa-filter"></i> Apply</button>
            <button type="button" id="allTimeSummary" class="btn btn-default"><i class="fa fa-history"></i> All Time</button>
            <small id="summaryDateNote" style="color:#7d8999;padding-bottom:8px;">Counts use the task planned-start date.</small>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered performance-summary-table">
                <thead><tr><th style="text-align:left;">User Name</th><th>Tasks Assigned</th><th>Completed</th><th>Ongoing</th><th>Completed With Delay</th><th>Overall Performance</th></tr></thead>
                <tbody id="designerSummaryBody"></tbody>
            </table>
        </div>
    </div>

    <div class="row"><div class="col-md-7"><div class="panel-box"><div class="panel-title"><i class="fa fa-bar-chart"></i> Planned vs Completed — Last 7 Days</div><div class="chart-wrap"><canvas id="trendChart"></canvas></div></div></div>
    <div class="col-md-5"><div class="panel-box"><div class="panel-title"><i class="fa fa-users"></i> <span id="performanceTitle">Designer Performance — Last 30 Days</span></div><div class="performance-list" id="designerPerformanceList">
        <?php if (empty($designerMap)) { ?><div class="empty-design">No designer task data found.</div><?php } ?>
        <?php foreach ($designerMap as $person) { $initials = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $person['name']), 0, 2)); ?>
        <div class="person-row"><div class="person-avatar"><?php echo htmlspecialchars($initials ?: 'NA'); ?></div><div class="person-info"><b><?php echo htmlspecialchars($person['name']); ?></b><small><?php echo $person['open']; ?> open · <?php echo $person['overdue']; ?> overdue · <?php echo $person['completed']; ?> completed</small><div class="progress"><div class="progress-bar" style="width:<?php echo $person['on_time_pct']; ?>%"></div></div></div><div class="score"><b><?php echo $person['on_time_pct']; ?>%</b><small> on time</small></div></div>
        <?php } ?>
    </div></div></div></div>

    <div class="panel-box filter-panel"><div class="panel-title"><i class="fa fa-filter"></i> Smart Filters</div><div class="row">
        <div class="col-md-3"><label>Search</label><input id="taskSearch" class="form-control" placeholder="Search DF, task or designer..."></div>
        <div class="col-md-3"><label>Designer</label><select id="designerFilter" class="form-control"><option value="">All Designers</option><?php foreach ($designerMap as $person) { ?><option><?php echo htmlspecialchars($person['name']); ?></option><?php } ?></select></div>
        <div class="col-md-2"><label>Status</label><select id="statusFilter" class="form-control"><option value="">All Statuses</option><option value="today">Due Today</option><option value="overdue">Overdue</option><option value="open">Open</option><option value="approval">Waiting Approval</option><option value="completed">All Completed</option><option value="completed_on_time">Completed On Time</option><option value="completed_late">Completed Late</option><option value="unassigned">Unassigned</option></select></div>
        <div class="col-md-2"><label>Task Period</label><select id="windowFilter" class="form-control"><option value="active">All Active Tasks</option><option value="planned_today">Planned Today</option><option value="due_today">Due Today</option><option value="current_week">Current Week</option><option value="upcoming_week">Upcoming Week</option><option value="next_7_days">Next 7 Days</option><option value="overdue">All Overdue Tasks</option><option value="completed_week">Completed This Week</option><option value="all">All Historical Tasks</option></select></div>
        <div class="col-md-2"><label>&nbsp;</label><button id="resetFilters" class="btn btn-default btn-block" style="border-radius:20px;font-weight:700"><i class="fa fa-refresh"></i> Reset</button></div>
    </div><div class="quick-views"><button class="quick-view active" data-view="active">Active</button><button class="quick-view" data-view="planned_today">Planned Today</button><button class="quick-view" data-view="due_today">Due Today</button><button class="quick-view" data-view="current_week">Current Week</button><button class="quick-view" data-view="upcoming_week">Upcoming Week</button><button class="quick-view" data-view="overdue">Overdue</button><button class="quick-view" data-view="completed_week">Completed This Week</button></div></div>

    <div class="panel-box table-card" id="designTaskReport"><div class="panel-title"><i class="fa fa-tasks"></i> <span id="taskReportTitle">Design Task Planner</span></div><div class="table-responsive"><table class="table table-striped" id="designTasks"><thead><tr><th>#</th><th>DF No.</th><th>Task</th><th>Designer</th><th>Planned Start</th><th>Due Date</th><th>Completed Date</th><th>Status</th><th>Remarks</th></tr></thead><tbody>
    <?php foreach ($tasks as $index => $task) { ?>
        <tr class="<?php echo $task['completion_delay_days'] > 0 ? 'completed-late-row' : ''; ?>" data-designer="<?php echo htmlspecialchars($task['designer']); ?>" data-status="<?php echo $task['status_key']; ?>" data-task-status="<?php echo (int)$task['task_status']; ?>" data-start="<?php echo $task['start_date']; ?>" data-due="<?php echo $task['end_date']; ?>" data-completed="<?php echo $task['completed_date']; ?>" data-delay-days="<?php echo (int)$task['completion_delay_days']; ?>" data-open="<?php echo in_array((int)$task['task_status'], array(0,2), true) ? '1' : '0'; ?>">
            <td class="serial-cell"></td><td><b><?php echo htmlspecialchars($task['df_no'] ?: '-'); ?></b><div class="df-meta"><?php echo htmlspecialchars($task['department']); ?></div></td>
            <td><div class="task-name"><?php echo htmlspecialchars($task['task_name'] ?: 'Unnamed task'); ?></div><div class="df-meta"><?php echo htmlspecialchars($task['df_description']); ?></div></td><td><?php echo htmlspecialchars($task['designer']); ?></td>
            <td data-order="<?php echo $task['start_date']; ?>"><?php echo !empty($task['start_date']) ? date('d M Y', strtotime($task['start_date'])) : '-'; ?></td><td data-order="<?php echo $task['end_date']; ?>"><?php echo !empty($task['end_date']) ? date('d M Y', strtotime($task['end_date'])) : '-'; ?></td><td data-order="<?php echo $task['completed_date']; ?>"><?php echo $task['completed_date'] !== '' ? date('d M Y', strtotime($task['completed_date'])) : ''; ?></td>
            <td><?php if ((int)$task['task_status'] === 1) { ?><span class="badge-status <?php echo $task['completion_delay_days'] > 0 ? 'status-completed-late' : 'status-completed'; ?>"><?php echo $task['completion_delay_days'] > 0 ? 'Late by ' . (int)$task['completion_delay_days'] . ' day(s)' : 'Completed On Time'; ?></span><?php } else { ?><span class="badge-status status-<?php echo $task['status_key']; ?>"><?php echo $task['status_label']; ?></span><?php } ?></td><td><?php echo htmlspecialchars($task['remarks'] ?: '-'); ?></td>
        </tr>
    <?php } ?>
    </tbody></table></div></div>
</div></div>

<div class="modal fade detail-modal" id="designerTaskDetailModal" tabindex="-1" role="dialog" aria-labelledby="designerTaskDetailTitle">
    <div class="modal-dialog modal-lg" role="document"><div class="modal-content">
        <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="designerTaskDetailTitle">Designer Task Details</h4></div>
        <div class="modal-body"><div class="table-responsive"><table class="table table-striped table-bordered"><thead><tr><th>#</th><th>DF No.</th><th>Task</th><th>Planned Start</th><th>Due Date</th><th>Completed Date</th><th>Status / Delay</th><th>Remarks</th></tr></thead><tbody id="designerTaskDetailBody"></tbody></table></div></div>
        <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div>
    </div></div>
</div>
<script src="<?php echo assets_url; ?>js/jquery.min.js"></script><script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script><script src="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.js"></script><script src="<?php echo assets_url; ?>plugins/chart.js/chart.min.js"></script>
<script>
$(function(){
    var today='<?php echo $today; ?>', monthStart='<?php echo $monthStart; ?>', weekEnd='<?php echo $weekEnd; ?>', currentWeekStart='<?php echo $currentWeekStart; ?>', currentWeekEnd='<?php echo $currentWeekEnd; ?>', upcomingWeekStart='<?php echo $upcomingWeekStart; ?>', upcomingWeekEnd='<?php echo $upcomingWeekEnd; ?>', kpiReport='';
    var designerPerformanceTasks=<?php echo json_encode($designerPerformanceTasks); ?>;
    var defaultPerformanceHtml=$('#designerPerformanceList').html();
    function renderDesignerSummary(){
        var from=$('#summaryFromDate').val(),to=$('#summaryToDate').val(),people={};
        if(from&&to&&from>to){alert('From Date cannot be after To Date.');return;}
        $.each(designerPerformanceTasks,function(_,task){
            if((from&&(!task.start_date||task.start_date<from))||(to&&(!task.start_date||task.start_date>to)))return;
            var key=String(task.user_id),done=Number(task.status)===1,late=done&&task.completed_date&&task.due_date&&task.completed_date>task.due_date;
            if(!people[key])people[key]={userId:task.user_id,name:task.name,assigned:0,completed:0,ongoing:0,late:0,onTime:0};
            people[key].assigned++;if(done){people[key].completed++;if(late)people[key].late++;else if(task.completed_date&&task.due_date&&task.completed_date<=task.due_date)people[key].onTime++;}else{people[key].ongoing++;}
        });
        var rows=Object.keys(people).map(function(key){var p=people[key];p.performance=p.completed?Math.round((p.onTime/p.completed)*100):0;return p;}).sort(function(a,b){return b.performance-a.performance||b.completed-a.completed||a.name.localeCompare(b.name);});
        var html='';$.each(rows,function(_,p){html+='<tr><td class="user-cell">'+escapeHtml(p.name)+'</td><td><button type="button" class="metric-link metric-count show-summary-detail" data-user="'+p.userId+'" data-name="'+escapeHtml(p.name)+'" data-type="assigned" title="View assigned tasks">'+p.assigned+'</button></td><td><button type="button" class="metric-link metric-count metric-completed show-summary-detail" data-user="'+p.userId+'" data-name="'+escapeHtml(p.name)+'" data-type="completed" title="View completed tasks">'+p.completed+'</button></td><td><button type="button" class="metric-link metric-count metric-ongoing show-summary-detail" data-user="'+p.userId+'" data-name="'+escapeHtml(p.name)+'" data-type="ongoing" title="View ongoing tasks">'+p.ongoing+'</button></td><td><button type="button" class="metric-link metric-count metric-late show-summary-detail" data-user="'+p.userId+'" data-name="'+escapeHtml(p.name)+'" data-type="late" title="View delayed completions">'+p.late+'</button></td><td><span class="performance-pill">'+p.performance+'%</span></td></tr>';});
        $('#designerSummaryBody').html(html||'<tr><td colspan="6" class="empty-design">No assigned Design tasks found for the selected date range.</td></tr>');
    }
    $('#applySummaryDates').on('click',renderDesignerSummary);
    $('#allTimeSummary').on('click',function(){$('#summaryFromDate,#summaryToDate').val('');renderDesignerSummary();});
    function formatSummaryDate(value){if(!value)return '-';var parts=value.split('-');return parts.length===3?parts[2]+'-'+parts[1]+'-'+parts[0]:value;}
    $(document).on('click','.show-summary-detail',function(){
        var userId=Number($(this).data('user')),type=String($(this).data('type')),name=String($(this).data('name')),from=$('#summaryFromDate').val(),to=$('#summaryToDate').val(),labels={assigned:'Assigned Tasks',completed:'Completed Tasks',ongoing:'Ongoing Tasks',late:'Completed With Delay'},rows=[];
        $.each(designerPerformanceTasks,function(_,task){if(Number(task.user_id)!==userId)return;if((from&&(!task.start_date||task.start_date<from))||(to&&(!task.start_date||task.start_date>to)))return;var done=Number(task.status)===1,late=done&&task.completed_date&&task.due_date&&task.completed_date>task.due_date;if(type==='completed'&&!done)return;if(type==='ongoing'&&done)return;if(type==='late'&&!late)return;rows.push(task);});
        rows.sort(function(a,b){return String(a.start_date).localeCompare(String(b.start_date))||String(a.due_date).localeCompare(String(b.due_date));});
        var html='';$.each(rows,function(index,task){var done=Number(task.status)===1,late=done&&task.completed_date&&task.due_date&&task.completed_date>task.due_date,delay=0,status='Ongoing',rowClass='';if(late){delay=Math.round((new Date(task.completed_date+'T00:00:00')-new Date(task.due_date+'T00:00:00'))/86400000);status='Completed Late by '+delay+' day(s)';rowClass=' class="completed-late-row"';}else if(done){status='Completed On Time';}else if(Number(task.status)===2){status='Waiting Approval';}else if(task.due_date&&task.due_date<today){delay=Math.round((new Date(today+'T00:00:00')-new Date(task.due_date+'T00:00:00'))/86400000);status='Overdue by '+delay+' day(s)';rowClass=' class="completed-late-row"';}html+='<tr'+rowClass+'><td>'+(index+1)+'</td><td><strong>'+escapeHtml(task.df_no)+'</strong></td><td>'+escapeHtml(task.task_name)+'</td><td>'+formatSummaryDate(task.start_date)+'</td><td>'+formatSummaryDate(task.due_date)+'</td><td>'+formatSummaryDate(task.completed_date)+'</td><td>'+escapeHtml(status)+'</td><td>'+escapeHtml(task.remarks)+'</td></tr>';});
        $('#designerTaskDetailTitle').text(name+' — '+labels[type]+' ('+rows.length+')');$('#designerTaskDetailBody').html(html||'<tr><td colspan="8" class="text-center">No records found for this selection.</td></tr>');$('#designerTaskDetailModal').modal('show');
    });
    var table=$('#designTasks').DataTable({pageLength:25,order:[[5,'asc']],autoWidth:false,scrollX:true,columnDefs:[{targets:0,orderable:false,searchable:false,width:'42px'},{targets:1,width:'75px'},{targets:4,width:'105px'},{targets:5,width:'95px'},{targets:6,width:'110px'},{targets:7,width:'125px'}],language:{emptyTable:'No Design department tasks found',zeroRecords:'No tasks match the selected filters'},infoCallback:function(settings,start,end,max,total){return total===0?'No matching tasks':'Showing '+start+' to '+end+' of '+total+' matching tasks';}});
    $.fn.dataTable.ext.search.push(function(settings,data,index){
        if(settings.nTable.id!=='designTasks')return true;
        var row=settings.aoData[index].nTr,$r=$(row),designer=$('#designerFilter').val(),status=$('#statusFilter').val(),windowKey=$('#windowFilter').val(),due=String($r.data('due')||''),start=String($r.data('start')||''),completed=String($r.data('completed')||''),isOpen=String($r.data('open'))==='1',taskStatus=String($r.data('task-status'));
        if(kpiReport==='planned_today'&&!(isOpen&&start===today))return false;
        if(kpiReport==='due_today'&&!(isOpen&&due===today))return false;
        if(kpiReport==='overdue'&&!(isOpen&&due!==''&&due!=='0000-00-00'&&due<today))return false;
        if(kpiReport==='open'&&!isOpen)return false;
        if(kpiReport==='completed_month'&&!(taskStatus==='1'&&completed>=monthStart&&completed<=today))return false;
        if(kpiReport==='on_time_month'&&!(taskStatus==='1'&&completed>=monthStart&&completed<=today&&due!==''&&due!=='0000-00-00'&&completed<=due))return false;
        if(designer&&$r.data('designer')!==designer)return false;
        if(status==='unassigned'&&$r.data('designer')!=='Unassigned')return false;
        if(status==='approval'&&taskStatus!=='2')return false;
        if(status==='completed_on_time'&&!(taskStatus==='1'&&(parseInt($r.data('delay-days'),10)||0)===0))return false;
        if(status==='completed_late'&&!(taskStatus==='1'&&(parseInt($r.data('delay-days'),10)||0)>0))return false;
        if(status&&status!=='unassigned'&&status!=='approval'&&status!=='completed_on_time'&&status!=='completed_late'&&$r.data('status')!==status)return false;
        if(windowKey==='active'&&!isOpen)return false;
        if(windowKey==='planned_today'&&!(isOpen&&start===today))return false;
        if(windowKey==='due_today'&&!(isOpen&&due===today))return false;
        if(windowKey==='current_week'&&!(isOpen&&due>=currentWeekStart&&due<=currentWeekEnd))return false;
        if(windowKey==='upcoming_week'&&!(isOpen&&due>=upcomingWeekStart&&due<=upcomingWeekEnd))return false;
        if(windowKey==='next_7_days'&&!(isOpen&&due>=today&&due<=weekEnd))return false;
        if(windowKey==='overdue'&&!(isOpen&&due!==''&&due!=='0000-00-00'&&due<today))return false;
        if(windowKey==='completed_week'&&!(taskStatus==='1'&&completed>=currentWeekStart&&completed<=currentWeekEnd))return false;
        return true;
    });
    function escapeHtml(value){return $('<div>').text(value).html();}
    function renderHistoricalPerformance(){
        var windowKey=$('#windowFilter').val();
        if(windowKey!=='all'&&windowKey!=='completed_week'){$('#performanceTitle').text('Designer Performance — Last 30 Days');$('#designerPerformanceList').html(defaultPerformanceHtml);return;}
        var people={};
        table.rows({search:'applied'}).nodes().each(function(row){var $r=$(row),name=String($r.data('designer')||'Unassigned'),done=String($r.data('task-status'))==='1',delay=parseInt($r.data('delay-days'),10)||0;if(!people[name])people[name]={name:name,total:0,completed:0,onTime:0,late:0,delayDays:0};people[name].total++;if(done){people[name].completed++;if(delay>0){people[name].late++;people[name].delayDays+=delay;}else{people[name].onTime++;}}});
        var rows=Object.keys(people).map(function(key){var p=people[key];p.rate=p.completed?Math.round((p.onTime/p.completed)*100):0;return p;}).sort(function(a,b){return b.completed-a.completed||b.total-a.total;});
        $('#performanceTitle').text(windowKey==='all'?'Designer Performance — Historical Data':'Designer Performance — Completed This Week');
        if(!rows.length){$('#designerPerformanceList').html('<div class="empty-design">No performance data for this selection.</div>');return;}
        var html='';$.each(rows,function(_,p){var initials=p.name.replace(/[^A-Za-z]/g,'').substring(0,2).toUpperCase()||'NA',avgDelay=p.late?Math.round(p.delayDays/p.late):0;html+='<div class="person-row"><div class="person-avatar">'+escapeHtml(initials)+'</div><div class="person-info"><b>'+escapeHtml(p.name)+'</b><small>'+p.completed+' completed · '+p.onTime+' on time · '+p.late+' late'+(p.late?' · avg '+avgDelay+' days late':'')+'</small><div class="progress"><div class="progress-bar" style="width:'+p.rate+'%"></div></div></div><div class="score"><b>'+p.rate+'%</b><small> on time</small></div></div>';});$('#designerPerformanceList').html(html);
    }
    function renumberRows(){var info=table.page.info();table.rows({page:'current',search:'applied'}).nodes().each(function(row,i){$('td.serial-cell',row).html(info.start+i+1);});renderHistoricalPerformance();}
    function syncQuickViews(){var selected=$('#windowFilter').val();$('.quick-view').removeClass('active').filter('[data-view="'+selected+'"]').addClass('active');}
    $('#taskSearch').on('keyup change',function(){table.search(this.value).draw()});
    $('#designerFilter,#statusFilter,#windowFilter').on('change',function(){kpiReport='';$('#taskReportTitle').text('Design Task Planner');syncQuickViews();table.draw()});
    $('.quick-view').on('click',function(){kpiReport='';$('#taskReportTitle').text('Design Task Planner');$('#windowFilter').val($(this).data('view'));syncQuickViews();table.draw()});
    $('.design-kpi-report').on('click',function(event){event.preventDefault();var labels={planned_today:'Tasks Planned Today',due_today:'Tasks Due Today',overdue:'Overdue Design Tasks',open:'All Open Design Tasks',completed_month:'Design Tasks Completed — Last 30 Days',on_time_month:'On-Time Design Completions — Last 30 Days'};kpiReport=String($(this).data('report'));$('#taskSearch,#designerFilter,#statusFilter').val('');$('#windowFilter').val('all');$('.quick-view').removeClass('active');table.search('').draw();$('#taskReportTitle').text(labels[kpiReport]||'Design Task Planner');$('html,body').animate({scrollTop:$('#designTaskReport').offset().top-85},350);});
    $('#resetFilters').on('click',function(){kpiReport='';$('#taskReportTitle').text('Design Task Planner');$('#taskSearch,#designerFilter,#statusFilter').val('');$('#windowFilter').val('active');syncQuickViews();table.search('').draw()});
    table.on('draw',renumberRows);table.draw();renumberRows();renderDesignerSummary();
    new Chart(document.getElementById('trendChart').getContext('2d'),{type:'bar',data:{labels:<?php echo json_encode(array_column($daily, 'label')); ?>,datasets:[{label:'Planned',backgroundColor:'rgba(24,160,216,.35)',borderColor:'#18a0d8',borderWidth:1,data:<?php echo json_encode(array_column($daily, 'planned')); ?>},{label:'Completed',backgroundColor:'#28b889',borderColor:'#28b889',borderWidth:1,data:<?php echo json_encode(array_column($daily, 'completed')); ?>}]},options:{maintainAspectRatio:false,responsive:true,legend:{position:'bottom'},scales:{yAxes:[{ticks:{beginAtZero:true,stepSize:1},gridLines:{color:'#edf1f5'}}],xAxes:[{gridLines:{display:false}}]}}});
});
</script>
</body></html>
