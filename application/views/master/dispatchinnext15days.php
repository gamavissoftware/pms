<?php
$CIA =& get_instance();
$CIA->load->model('Task_model');

$company_q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
$LOGO = ($company_q->num_rows() > 0) ? $company_q->row() : null;
$themeColor = (!empty($LOGO->colorcode)) ? $LOGO->colorcode : '#2473a0';

function safeDateShowDispatchReport($date, $format = 'd-m-Y')
{
    if (empty($date) || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
        return '';
    }

    return date($format, strtotime($date));
}

$reportid = $this->uri->segment(3);
$today = date('Y-m-d');
$future = date('Y-m-d', strtotime('+15 days'));

$rowsData = array();

$totalDf = 0;
$totalDelayedDf = 0;
$totalCriticalDf = 0;
$totalDispatchCompleted = 0;
$totalProgress = 0;

$dispatchSubQuery = "
    (
        SELECT 
            df_id,
            MIN(end_date) AS dispatch_due_date,
            MAX(task_completed_on) AS dispatch_completed_on,
            MAX(task_status) AS dispatch_status
        FROM task_department_wise_scheduling
        WHERE taskid = 103
        AND df_id > 0
        AND end_date BETWEEN ".$this->db->escape($today)." AND ".$this->db->escape($future)."
        GROUP BY df_id
    ) dispatch
";

$poSubQuery = "
    (
        SELECT 
            df_id,
            MAX(id) AS po_id,
            MAX(company_name) AS company_name,
            MIN(podate) AS podate,
            MAX(added_by) AS added_by
        FROM poreceived
        WHERE df_id IS NOT NULL
        AND df_id > 0
        GROUP BY df_id
    ) po
";

$this->db->select('
    b.id,
    b.df_no,
    b.added_on,
    b.df_upload,
    b.df_status,
    dispatch.dispatch_due_date,
    dispatch.dispatch_completed_on,
    dispatch.dispatch_status,
    po.company_name,
    po.podate,
    u.title,
    u.first_name,
    u.last_name
');
$this->db->from('df_release b');
$this->db->join($dispatchSubQuery, 'dispatch.df_id = b.id', 'inner', false);
$this->db->join($poSubQuery, 'po.df_id = b.id', 'left', false);
$this->db->join('system_users u', 'u.user_id = po.added_by', 'left');
$this->db->where('b.df_status', 0);
$this->db->order_by('dispatch.dispatch_due_date', 'ASC');

$main_q = $this->db->get();

if ($main_q->num_rows() > 0) {
    foreach ($main_q->result() as $rows) {

        $dfId = (int)$rows->id;

        $plannedDateRaw = '';
        $plannedDisplay = '';

        $planned_q = $this->db
            ->select('MAX(end_date) as enddate')
            ->from('task_department_wise_scheduling')
            ->where('df_id', $dfId)
            ->get();

        if ($planned_q->num_rows() > 0 && !empty($planned_q->row()->enddate) && $planned_q->row()->enddate != '0000-00-00') {
            $plannedDateRaw = $planned_q->row()->enddate;
            $plannedDisplay = date('d-m-Y', strtotime($plannedDateRaw));
        }

        $totalTasks = $this->db
            ->from('task_department_wise_scheduling')
            ->where('df_id', $dfId)
            ->count_all_results();

        $completed_q = $this->db
            ->select('id, task_completed_on, end_date')
            ->from('task_department_wise_scheduling')
            ->where('df_id', $dfId)
            ->where('task_status', 1)
            ->get();

        $completedTasks = $completed_q->num_rows();

        $completionPercent = 0;
        if ($totalTasks > 0) {
            $completionPercent = round(($completedTasks * 100) / $totalTasks);
        }

        $delayedTasks = 0;
        $totalDaysDelayed = 0;
        $latestCompletion = '';

        if ($completed_q->num_rows() > 0) {
            foreach ($completed_q->result() as $task) {
                if (
                    !empty($task->task_completed_on) &&
                    $task->task_completed_on != '0000-00-00 00:00:00' &&
                    !empty($task->end_date) &&
                    $task->end_date != '0000-00-00'
                ) {
                    $completedOn = date('Y-m-d', strtotime($task->task_completed_on));

                    if (empty($latestCompletion) || $completedOn > $latestCompletion) {
                        $latestCompletion = $completedOn;
                    }

                    if ($completedOn > $task->end_date) {
                        $delayedTasks++;

                        if (method_exists($CIA->Task_model, 'getDays')) {
                            $daysLate = $CIA->Task_model->getDays($task->end_date, $completedOn, 1);
                        } else {
                            $daysLate = abs(round((strtotime($completedOn) - strtotime($task->end_date)) / 86400));
                        }

                        $totalDaysDelayed += (int)$daysLate;
                    }
                }
            }
        }

        $delayPercent = 0;
        if ($completedTasks > 0) {
            $delayPercent = round(($delayedTasks * 100) / $completedTasks);
        }

        $projectedDelayDays = 0;
        if (method_exists($CIA->Task_model, 'workDelayed')) {
            $projectedDelayDays = (int) $CIA->Task_model->workDelayed($dfId, 0);
        }

        $isFullyCompleted = ($totalTasks > 0 && $completedTasks >= $totalTasks);
        $completionMode = $isFullyCompleted ? 'Actual Completion' : 'Expected Completion';
        $completionDateRaw = '';
        $completionVarianceDays = 0;
        $actualDisplay = '';

        if ($isFullyCompleted && !empty($latestCompletion)) {
            $completionDateRaw = $latestCompletion;
        } elseif (!empty($plannedDateRaw)) {
            $completionDateRaw = $plannedDateRaw;

            if ($projectedDelayDays > 0) {
                $completionDateRaw = date('Y-m-d', strtotime($plannedDateRaw . ' +' . $projectedDelayDays . ' days'));
            }

            if (method_exists($CIA->Task_model, 'SKIPsingle_holidays')) {
                $holidayAdjustedDate = $CIA->Task_model->SKIPsingle_holidays($completionDateRaw);
                if (!empty($holidayAdjustedDate) && $holidayAdjustedDate != '0000-00-00') {
                    $completionDateRaw = date('Y-m-d', strtotime($holidayAdjustedDate));
                }
            }
        }

        if (!empty($completionDateRaw) && $completionDateRaw != '0000-00-00') {
            $actualDisplay = date('d-m-Y', strtotime($completionDateRaw));
        }

        if (
            !empty($plannedDateRaw) &&
            $plannedDateRaw != '0000-00-00' &&
            !empty($completionDateRaw) &&
            $completionDateRaw != '0000-00-00' &&
            strtotime($completionDateRaw) > strtotime($plannedDateRaw)
        ) {
            if (method_exists($CIA->Task_model, 'getDays')) {
                $completionVarianceDays = (int) $CIA->Task_model->getDays($plannedDateRaw, $completionDateRaw, 1);
            } else {
                $completionVarianceDays = abs(round((strtotime($completionDateRaw) - strtotime($plannedDateRaw)) / 86400));
            }
        }

        $dispatchDueDate = safeDateShowDispatchReport($rows->dispatch_due_date);
        $dispatchCompletedDate = safeDateShowDispatchReport($rows->dispatch_completed_on);

        $dispatchStatus = 'Pending';
        $dispatchStatusClass = 'warning';

        if ((int)$rows->dispatch_status == 1) {
            $dispatchStatus = 'Completed';
            $dispatchStatusClass = 'success';
            $totalDispatchCompleted++;
        }

        $daysLeft = '';
        $urgencyText = 'Normal';
        $urgencyClass = 'success';

        if (!empty($rows->dispatch_due_date) && $rows->dispatch_due_date != '0000-00-00') {
            $daysLeft = floor((strtotime($rows->dispatch_due_date) - strtotime($today)) / 86400);

            if ($daysLeft <= 3) {
                $urgencyText = 'Urgent';
                $urgencyClass = 'danger';
            } elseif ($daysLeft <= 7) {
                $urgencyText = 'Soon';
                $urgencyClass = 'warning';
            }
        }

        $show = 1;
        if ($reportid != '') {
            $show = ($delayPercent > 0) ? 1 : 0;
        }

        if ($show == 1) {

            if ($delayPercent > 0) {
                $totalDelayedDf++;
            }

            if ($delayPercent >= 50 || $urgencyText == 'Urgent') {
                $totalCriticalDf++;
            }

            $totalDf++;
            $totalProgress += $completionPercent;

            $dfOwner = trim($rows->title . ' ' . $rows->first_name . ' ' . $rows->last_name);

            $rowsData[] = array(
                'id' => $dfId,
                'df_no' => $rows->df_no,
                'df_upload' => $rows->df_upload,
                'company_name' => $rows->company_name,
                'po_date' => safeDateShowDispatchReport($rows->podate),
                'df_owner' => $dfOwner,
                'df_release_date' => safeDateShowDispatchReport($rows->added_on),
                'dispatch_due_date' => $dispatchDueDate,
                'dispatch_completed_date' => $dispatchCompletedDate,
                'dispatch_status' => $dispatchStatus,
                'dispatch_status_class' => $dispatchStatusClass,
                'planned_display' => $plannedDisplay,
                'actual_display' => $actualDisplay,
                'completion_mode' => $completionMode,
                'completion_variance_days' => $completionVarianceDays,
                'completion_percent' => $completionPercent,
                'delay_percent' => $delayPercent,
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'delayed_tasks' => $delayedTasks,
                'total_days_delayed' => $totalDaysDelayed,
                'days_left' => $daysLeft,
                'urgency_text' => $urgencyText,
                'urgency_class' => $urgencyClass
            );
        }
    }
}

$avgProgress = ($totalDf > 0) ? round($totalProgress / $totalDf) : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

    <title><?php echo sitetitle; ?> Dispatch in Next 15 Days</title>

    <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

    <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

    <style>
        body {
            background: #f3f6fb;
        }
        h1{
                color: #f6f6f6 !important;
        }
         h3{
                color: #f6f6f6 !important;
        }

        .report-hero {
            background: linear-gradient(135deg, <?php echo $themeColor;?> 0%, #111827 100%);
            border-radius: 18px;
            padding: 26px;
            margin-bottom: 22px;
            color: #fff;
            box-shadow: 0 12px 35px rgba(0,0,0,0.13);
            position: relative;
            overflow: hidden;
        }

        .report-hero:before {
            content: "";
            position: absolute;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            right: -90px;
            top: -100px;
        }

        .report-title {
            font-size: 27px;
            font-weight: 900;
            margin: 0;
            letter-spacing: .2px;
        }

        .report-subtitle {
            margin-top: 8px;
            opacity: .9;
            font-size: 14px;
        }

        .report-pill {
            display: inline-block;
            background: rgba(255,255,255,0.16);
            border: 1px solid rgba(255,255,255,0.25);
            color: #fff;
            padding: 8px 14px;
            border-radius: 30px;
            font-weight: 700;
            margin-top: 12px;
        }

        .kpi-card {
            background: #fff;
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 18px;
            border: 1px solid #edf0f5;
            box-shadow: 0 8px 24px rgba(31,41,55,0.07);
            min-height: 122px;
            position: relative;
            overflow: hidden;
        }

        .kpi-card:after {
            content: "";
            position: absolute;
            right: -25px;
            bottom: -25px;
            width: 92px;
            height: 92px;
            border-radius: 50%;
            background: rgba(72,114,184,0.08);
        }

        .kpi-icon {
            width: 42px;
            height: 42px;
            border-radius: 13px;
            background: <?php echo $themeColor;?>;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            right: 16px;
            top: 16px;
            font-size: 19px;
        }

        .kpi-label {
            color: #6b7280;
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .kpi-value {
            font-size: 30px;
            font-weight: 900;
            color: #111827;
            margin-top: 9px;
            line-height: 1.1;
        }

        .kpi-hint {
            color: #8a94a6;
            font-size: 12px;
            margin-top: 7px;
        }

        .filter-panel {
            background: #fff;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 18px;
            border: 1px solid #edf0f5;
            box-shadow: 0 8px 24px rgba(31,41,55,0.05);
        }

        .filter-title {
            font-size: 15px;
            font-weight: 900;
            color: #111827;
            margin-bottom: 12px;
        }

        .modern-table-card {
            background: #fff;
            border-radius: 18px;
            padding: 18px;
            border: 1px solid #edf0f5;
            box-shadow: 0 10px 28px rgba(31,41,55,0.07);
        }

        table.manglesh thead th {
            background: <?php echo $themeColor;?> !important;
            color: #fff !important;
            font-weight: 800;
            text-align: center;
            vertical-align: middle !important;
            white-space: nowrap;
            font-size: 12px;
        }

        table.manglesh tbody td {
            text-align: center;
            vertical-align: middle !important;
            font-size: 12px;
            color: #374151;
        }

        .df-badge {
            background: #eef4ff;
            color: <?php echo $themeColor;?>;
            border: 1px solid rgba(72,114,184,0.18);
            padding: 6px 10px;
            border-radius: 20px;
            font-weight: 900;
            display: inline-block;
            white-space: nowrap;
        }

        .company-box {
            text-align: left;
            min-width: 220px;
        }

        .company-name {
            font-weight: 900;
            color: #111827;
            text-transform: uppercase;
        }

        .owner-name {
            color: #6b7280;
            font-size: 11px;
            margin-top: 3px;
        }

        .date-stack {
            min-width: 100px;
            font-weight: 800;
        }

        .date-muted {
            color: #6b7280;
            font-size: 11px;
            font-weight: 600;
            margin-top: 2px;
        }

        .progress-wrap {
            min-width: 120px;
        }

        .progress {
            height: 8px;
            margin-bottom: 4px;
            background: #edf0f5;
            border-radius: 20px;
            box-shadow: none;
        }

        .progress-bar {
            border-radius: 20px;
        }

        .status-pill {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 900;
            white-space: nowrap;
        }

        .pill-success {
            background: #ecfdf3;
            color: #15803d;
        }

        .pill-warning {
            background: #fff7ed;
            color: #c2410c;
        }

        .pill-danger {
            background: #fef2f2;
            color: #b91c1c;
        }

        .pill-info {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .btn-action {
            border-radius: 20px;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 800;
            margin: 2px;
        }

        .empty-state {
            text-align: center;
            padding: 45px 15px;
            color: #6b7280;
        }

        .empty-state h4 {
            font-weight: 900;
            color: #111827;
        }

        .dataTables_wrapper .dt-buttons .btn {
            border-radius: 20px !important;
            margin-right: 5px;
            font-weight: 800;
        }

        .dataTables_filter input,
        .dataTables_length select,
        .filter-panel select,
        .filter-panel input {
            border-radius: 20px;
            border: 1px solid #d8dee9;
            padding: 6px 12px;
        }

        @media(max-width: 767px) {
            .report-title {
                font-size: 22px;
            }

            .kpi-value {
                font-size: 25px;
            }

            .report-hero {
                padding: 20px;
            }
        }
    </style>
</head>

<body>

<header id="topnav">
    <?php $this->load->view('common/nav-menu');?>
</header>

<div class="wrapper">
    <div class="container-fluid">

        <div class="row" style="margin-top:20px;">
            <div class="col-md-12">
                <div class="report-hero">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="report-title">Dispatches in Next 15 Days</h3>
                            <div class="report-subtitle">
                                DF-wise dispatch due tracking with company, marketing person, progress, delay and urgency indicators.
                            </div>
                            <div class="report-pill">
                                <i class="fa fa-truck"></i>
                                <?php echo date('d M Y', strtotime($today)); ?> to <?php echo date('d M Y', strtotime($future)); ?>
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <div style="font-size:13px; opacity:.85;">Total Dispatch Due</div>
                            <div style="font-size:28px; font-weight:900; margin-top:5px;">
                                <?php echo $totalDf; ?> DF
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-info" style="border-radius:12px;">
                <?php echo $this->session->flashdata('message'); ?>
            </div>
        <?php } ?>

        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-truck"></i></div>
                    <div class="kpi-label">Dispatch Due</div>
                    <div class="kpi-value"><?php echo $totalDf; ?></div>
                    <div class="kpi-hint">Active DF due for dispatch</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-check-circle"></i></div>
                    <div class="kpi-label">Dispatch Completed</div>
                    <div class="kpi-value"><?php echo $totalDispatchCompleted; ?></div>
                    <div class="kpi-hint">Dispatch task completed</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-clock-o"></i></div>
                    <div class="kpi-label">Delayed DF</div>
                    <div class="kpi-value"><?php echo $totalDelayedDf; ?></div>
                    <div class="kpi-hint">DF having delayed completed tasks</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-line-chart"></i></div>
                    <div class="kpi-label">Average Progress</div>
                    <div class="kpi-value"><?php echo $avgProgress; ?>%</div>
                    <div class="kpi-hint">Overall DF task completion</div>
                </div>
            </div>
        </div>

        <div class="filter-panel">
            <div class="filter-title">
                <i class="fa fa-filter"></i> Smart Filters
            </div>

            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <label>Search</label>
                    <input type="text" id="customSearch" class="form-control" placeholder="Search DF, company, owner...">
                </div>

                <div class="col-md-3 col-sm-6">
                    <label>Marketing Person</label>
                    <select id="marketingPersonFilter" class="form-control">
                        <option value="">All Marketing Person</option>
                        <?php
                        $owners = array();
                        if (!empty($rowsData)) {
                            foreach ($rowsData as $row) {
                                $ownerName = trim($row['df_owner']);
                                if ($ownerName != '') {
                                    $owners[$ownerName] = $ownerName;
                                }
                            }
                        }

                        if (!empty($owners)) {
                            ksort($owners);
                            foreach ($owners as $owner) {
                        ?>
                                <option value="<?php echo htmlspecialchars($owner); ?>">
                                    <?php echo $owner; ?>
                                </option>
                        <?php } } ?>
                    </select>
                </div>

                <div class="col-md-2 col-sm-6">
                    <label>Dispatch Status</label>
                    <select id="dispatchStatusFilter" class="form-control">
                        <option value="">All</option>
                        <option value="Completed">Completed</option>
                        <option value="Pending">Pending</option>
                    </select>
                </div>

                <div class="col-md-2 col-sm-6">
                    <label>Urgency</label>
                    <select id="urgencyFilter" class="form-control">
                        <option value="">All</option>
                        <option value="Urgent">Urgent</option>
                        <option value="Soon">Soon</option>
                        <option value="Normal">Normal</option>
                    </select>
                </div>

                <div class="col-md-2 col-sm-6">
                    <label>&nbsp;</label>
                    <button type="button" id="resetFilters" class="btn btn-default btn-block" style="border-radius:20px; font-weight:800;">
                        <i class="fa fa-refresh"></i> Reset
                    </button>
                </div>
            </div>
        </div>

        <div class="modern-table-card table-responsive">
            <table id="example5" class="table manglesh table-striped table-bordered">
                <thead>
                    <tr>
                        <th>S. No.</th>
                        <th>DF No.</th>
                        <th>Company / Owner</th>
                        <th>PO Date</th>
                        <th>DF Release</th>
                        <th>Dispatch Due</th>
                        <th>System Planned Closure</th>
                        <th>Actual / Expected Completion</th>
                        <th>DF Progress</th>
                        <th>Delay</th>
                        <th>Urgency</th>
                        <th>Dispatch Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($rowsData)) { ?>
                        <?php $m = 1; foreach ($rowsData as $row) {

                            $progressClass = 'progress-bar-success';
                            if ($row['completion_percent'] < 50) {
                                $progressClass = 'progress-bar-danger';
                            } elseif ($row['completion_percent'] < 80) {
                                $progressClass = 'progress-bar-warning';
                            }

                            $urgencyPillClass = 'pill-success';
                            if ($row['urgency_class'] == 'danger') {
                                $urgencyPillClass = 'pill-danger';
                            } elseif ($row['urgency_class'] == 'warning') {
                                $urgencyPillClass = 'pill-warning';
                            }

                            $dispatchStatusPillClass = ($row['dispatch_status_class'] == 'success') ? 'pill-success' : 'pill-warning';
                        ?>
                            <tr 
                                data-owner="<?php echo htmlspecialchars($row['df_owner']); ?>"
                                data-dispatch-status="<?php echo $row['dispatch_status']; ?>"
                                data-urgency="<?php echo $row['urgency_text']; ?>"
                            >
                                <td><?php echo $m; ?></td>

                                <td>
                                    <span class="df-badge">
                                        <?php echo strtoupper(trim($row['df_no'])); ?>
                                    </span>
                                </td>

                                <td>
                                    <div class="company-box">
                                        <div class="company-name">
                                            <?php echo !empty($row['company_name']) ? $row['company_name'] : 'N/A'; ?>
                                        </div>
                                        <div class="owner-name">
                                            <?php echo !empty($row['df_owner']) ? $row['df_owner'] : 'Marketing person not mapped'; ?>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="date-stack">
                                        <?php echo !empty($row['po_date']) ? $row['po_date'] : '-'; ?>
                                    </div>
                                </td>

                                <td>
                                    <div class="date-stack">
                                        <?php echo !empty($row['df_release_date']) ? $row['df_release_date'] : '-'; ?>
                                    </div>
                                </td>

                                <td>
                                    <div class="date-stack">
                                        <?php echo !empty($row['dispatch_due_date']) ? $row['dispatch_due_date'] : '-'; ?>
                                        <?php if ($row['days_left'] !== '') { ?>
                                            <div class="date-muted">
                                                <?php echo ($row['days_left'] == 0) ? 'Today' : $row['days_left'] . ' days left'; ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </td>

                                <td>
                                    <div class="date-stack">
                                        <?php echo !empty($row['planned_display']) ? $row['planned_display'] : '-'; ?>
                                    </div>
                                </td>

                                <td>
                                    <div class="date-stack">
                                        <?php echo !empty($row['actual_display']) ? $row['actual_display'] : '-'; ?>
                                        <?php if (!empty($row['completion_mode'])) { ?>
                                            <div class="date-muted">
                                                <?php echo $row['completion_mode']; ?>
                                            </div>
                                        <?php } ?>
                                        <?php if (!empty($row['completion_variance_days']) && $row['completion_variance_days'] > 0) { ?>
                                            <div class="date-muted">
                                                +<?php echo $row['completion_variance_days']; ?> delayed days
                                            </div>
                                        <?php } ?>
                                    </div>
                                </td>

                                <td>
                                    <div class="progress-wrap">
                                        <div class="progress">
                                            <div class="progress-bar <?php echo $progressClass; ?>" role="progressbar" style="width: <?php echo $row['completion_percent']; ?>%;"></div>
                                        </div>
                                        <strong><?php echo $row['completion_percent']; ?>%</strong>
                                        <div class="date-muted">
                                            <?php echo $row['completed_tasks']; ?>/<?php echo $row['total_tasks']; ?> tasks
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <?php if ($row['delay_percent'] > 0) { ?>
                                        <span class="status-pill pill-danger">
                                            <?php echo $row['delay_percent']; ?>%
                                        </span>
                                    <?php } else { ?>
                                        <span class="status-pill pill-success">On Time</span>
                                    <?php } ?>

                                    <?php if ($row['delayed_tasks'] > 0) { ?>
                                        <div class="date-muted">
                                            <?php echo $row['delayed_tasks']; ?> delayed task
                                        </div>
                                    <?php } ?>
                                </td>

                                <td>
                                    <span class="status-pill <?php echo $urgencyPillClass; ?>">
                                        <?php echo $row['urgency_text']; ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="status-pill <?php echo $dispatchStatusPillClass; ?>">
                                        <?php echo $row['dispatch_status']; ?>
                                    </span>

                                    <?php if (!empty($row['dispatch_completed_date'])) { ?>
                                        <div class="date-muted">
                                            <?php echo $row['dispatch_completed_date']; ?>
                                        </div>
                                    <?php } ?>
                                </td>

                                <td>
                                    <?php if (!empty($row['df_upload'])) { ?>
                                        <a href="<?php echo sfdocument;?>Taskdocument/dfattachment/<?php echo $row['df_upload'];?>" download class="btn btn-primary btn-xs btn-action">
                                            <i class="fa fa-download"></i> DF
                                        </a>
                                    <?php } ?>

                                    <a href="<?php echo page_url;?>Task/dfgantchartNew/<?php echo $row['id'];?>" target="_blank" class="btn btn-warning btn-xs btn-action">
                                        <i class="fa fa-bar-chart"></i> Gantt
                                    </a>
                                </td>
                            </tr>
                        <?php $m++; } ?>
                    <?php } ?>
                </tbody>
            </table>

            <?php if (empty($rowsData)) { ?>
                <div class="empty-state">
                    <h4>No Dispatch Due Found</h4>
                    <p>No active DF has dispatch due between <?php echo date('d M Y', strtotime($today)); ?> and <?php echo date('d M Y', strtotime($future)); ?>.</p>
                </div>
            <?php } ?>
        </div>

        <?php $this->load->view('common/footer');?>

    </div>
</div>

<script src="<?php echo assets_url;?>js/jquery.min.js"></script>
<script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url;?>js/detect.js"></script>
<script src="<?php echo assets_url;?>js/fastclick.js"></script>
<script src="<?php echo assets_url;?>js/jquery.slimscroll.js"></script>
<script src="<?php echo assets_url;?>js/jquery.blockUI.js"></script>
<script src="<?php echo assets_url;?>js/waves.js"></script>
<script src="<?php echo assets_url;?>js/wow.min.js"></script>
<script src="<?php echo assets_url;?>js/jquery.nicescroll.js"></script>
<script src="<?php echo assets_url;?>js/jquery.scrollTo.min.js"></script>

<script src="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/dataTables.bootstrap.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/dataTables.buttons.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/jszip.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/pdfmake.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/vfs_fonts.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/buttons.html5.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/buttons.print.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/dataTables.fixedHeader.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/dataTables.keyTable.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/dataTables.responsive.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/dataTables.scroller.min.js"></script>

<script src="<?php echo assets_url;?>js/jquery.core.js"></script>
<script src="<?php echo assets_url;?>js/jquery.app.js"></script>

<script>
$(document).ready(function () {

    var table = $('#example5').DataTable({
        processing: true,
        fixedHeader: true,
        pageLength: 25,
        responsive: false,
        scrollX: true,
        order: [[5, 'asc']],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                title: 'Dispatches in Next 15 Days'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Print',
                title: 'Dispatches in Next 15 Days'
            }
        ]
    });

    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        if (settings.nTable.id !== 'example5') {
            return true;
        }

        var rowNode = table.row(dataIndex).node();

        var ownerFilter = $('#marketingPersonFilter').val();
        var dispatchStatusFilter = $('#dispatchStatusFilter').val();
        var urgencyFilter = $('#urgencyFilter').val();

        var rowOwner = $(rowNode).data('owner');
        var rowDispatchStatus = $(rowNode).data('dispatch-status');
        var rowUrgency = $(rowNode).data('urgency');

        if (ownerFilter !== '' && rowOwner !== ownerFilter) {
            return false;
        }

        if (dispatchStatusFilter !== '' && rowDispatchStatus !== dispatchStatusFilter) {
            return false;
        }

        if (urgencyFilter !== '' && rowUrgency !== urgencyFilter) {
            return false;
        }

        return true;
    });

    $('#customSearch').on('keyup change', function () {
        table.search(this.value).draw();
    });

    $('#marketingPersonFilter, #dispatchStatusFilter, #urgencyFilter').on('change', function () {
        table.draw();
    });

    $('#resetFilters').on('click', function () {
        $('#customSearch').val('');
        $('#marketingPersonFilter').val('');
        $('#dispatchStatusFilter').val('');
        $('#urgencyFilter').val('');

        table.search('');
        table.columns().search('');
        table.draw();
    });

});
</script>

</body>
</html>
